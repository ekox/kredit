<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransaksiDRPPController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
	
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		try{
			$aColumns = array('nodrpp','kdjendrpp','tgdrpp','urdrpp','kode','jumlah','nilai');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "nodrpp";
			/* DB table to use */
			$sTable = "	SELECT	IF(IFNULL(a.nodrpp,'')='','',a.nodrpp) AS nodrpp,
								IF(IFNULL(b.kdjendrpp,'')='','',b.kdjendrpp) AS kdjendrpp,
								IF(IFNULL(b.tgdrpp,'')='','',DATE_FORMAT(b.tgdrpp,'%d-%m-%Y')) AS tgdrpp,
								IF(IFNULL(b.urdrpp,'')='','',b.urdrpp) AS urdrpp,
								CONCAT(a.kdgiat,'-',a.kdoutput,'-',SUBSTR(a.kdmak,1,2)) AS kode,
								COUNT(a.id) AS jumlah, 
								SUM(a.totnilmak) AS nilai
						FROM d_transaksi a
						LEFT OUTER JOIN d_drpp b ON(a.nodrpp=b.nodrpp)
						LEFT OUTER JOIN(
							SELECT	a.id_trans,
									b.nourut
							FROM(
								SELECT	id_trans,
										MAX(id) AS id
								FROM d_transaksi_status
								GROUP BY id_trans
							) a
							LEFT OUTER JOIN d_transaksi_status b ON(a.id=b.id)
						) c ON(a.id=c.id_trans)
						WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."' AND c.nourut>=1
						GROUP BY a.nodrpp, 
								 b.tgdrpp,
								 b.urdrpp,
								 CONCAT(a.kdgiat,'-',a.kdoutput,'-',SUBSTR(a.kdmak,1,2))
						ORDER BY a.nodrpp DESC";
			
			/*
			 * Paging
			 */
			$sLimit = "";
			$iDisplayStart=$request->input('start');
			$iDisplayLength=$request->input('length');
			if ( isset( $iDisplayStart ) &&  $iDisplayLength != '-1' ) 
			{
				 $sLimit = "LIMIT ".intval( $request->input('start') ).", ".
					intval( $request->input('length') );
			}
			 
			 
			/*
			 * Ordering
			 */
			$sOrder = "";
			if ( $request->input('order') !==null )
			{
				$arr_order=$request->input('order');
				
				if(isset($arr_order[0]['column'])){
					$sort_col=$arr_order[0]['column'];
					
					$sort_dir='desc';
					if(isset($arr_order[0]['dir'])){
						$sort_dir=$arr_order[0]['dir'];
						$sort_col-=1;
					}
					
					$sOrder = " ORDER BY a.".$aColumns[ intval( $sort_col ) ]." ".($sort_dir==='asc' ? 'asc' : 'desc') ." ";
				}
			}
			 
			 
			/*
			 * Filtering
			 */
			$sWhere = "";
			$arr_search=$request->input('search');
			$sSearch=$arr_search['value'];
			if ( isset($sSearch) && $sSearch != "" )
			{
				for ( $i=0 ; $i<count($aColumns) ; $i++ )
				{
					$arr_where[] = $aColumns[$i]." LIKE '%".( $sSearch )."%' ";
				}
				
				$sWhere .= "WHERE ".implode(" OR ", $arr_where);
			}
			 
			/* Individual column filtering */
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if ( isset($bSearchable_i) && $bSearchable_i == "true" && $request->input('sSearch_'.$i) != '' )
				{
					if ( $sWhere == "" )
					{
						$sWhere = "WHERE ";
					}
					else
					{
						$sWhere .= " AND ";
					}
					$sWhere .= $aColumns[$i]." LIKE '%".($request->input('sSearch_'.$i))."%' ";
				}
			}

			/* Data set length after filtering */
			$iFilteredTotal = 0;
			$rows = DB::select("
				SELECT COUNT(*) as jumlah FROM (".$sTable.") a
			");
			$result = (array)$rows[0];
			if($result){
				$iFilteredTotal = $result['jumlah'];
			}
			
			/* Total data set length */
			$iTotal = 0;
			$rows = DB::select("
				SELECT COUNT(".$sIndexColumn.") as jumlah FROM (".$sTable.") a
			");
			$result = (array)$rows[0];
			if($result){
				$iTotal = $result['jumlah'];
			}
		   
			/*
			 * Format Output
			 */
			$output = array(
				"sEcho" => intval($request->input('sEcho')),
				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
			);

			/*
			 * SQL queries
			 */
			$sQuery = "
				SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
				FROM   ($sTable) a
				$sWhere
				$sOrder
				$sLimit
			";
			
			$rows = DB::select($sQuery);
			
			$rows_detil = DB::select("
				select	*
				from t_jendrpp
				order by kdjendrpp
			");
			
			foreach( $rows as $row )
			{	
				$kdjendrpp = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows_detil as $row_detil){
					$checked = '';
					if($row_detil->kdjendrpp==$row->kdjendrpp){
						$checked = 'selected="selected"';
					}
					$kdjendrpp .= '<option value="'.$row_detil->kdjendrpp.'" '.$checked.'>'.$row_detil->nmjendrpp.'</option>';
				}
				
				$aksi='<center>
							<a id="'.$row->kode.'-'.$row->nodrpp.'" title="Simpan" class="simpan btn btn-xs btn-primary"><i class="fa fa-check"></i></a>
						</center>';
						
				if($row->nodrpp!=''){
					$aksi='<center>
								<a href="transaksi/drpp/cetak/'.$row->nodrpp.'" target="_blank" title="Cetak data DRPP?" class="btn btn-xs btn-primary"><i class="fa fa-print"></i></a>
								<a id="'.$row->nodrpp.'" title="Hapus data DRPP?" class="hapus btn btn-xs btn-danger"><i class="fa fa-times"></i></a>
							</center>';
				}
				
				$output['aaData'][] = array(
					$row->kode.'-'.$row->nodrpp,
					'<input type="text" id="nodrpp-'.$row->kode.'-'.$row->nodrpp.'" name="nodrpp" value="'.$row->nodrpp.'" disabled>',
					'<select id="kdjendrpp-'.$row->kode.'-'.$row->nodrpp.'" name="kdjendrpp" disabled>'.$kdjendrpp.'</select>',
					'<input type="text" id="tgdrpp-'.$row->kode.'-'.$row->tgdrpp.'" name="tgdrpp" value="'.$row->tgdrpp.'" class="dp" disabled>',
					'<textarea id="urdrpp-'.$row->kode.'-'.$row->urdrpp.'" name="urdrpp" disabled>'.$row->urdrpp.'</textarea>',
					$row->kode,
					'<div style="text-align:right;">'.number_format($row->jumlah).'</div>',
					'<div style="text-align:right;">'.number_format($row->nilai).'</div>',
					$aksi
				);
			}
			
			return response()->json($output);
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function detil($id)
	{
		$arr_data=explode('-', $id);
		
		if($arr_data[3]==''){//belum DRPP
			
			$rows = DB::select("
				SELECT	a.id,
					date_format(a.tgkuitansi,'%d-%m-%Y') as tgkuitansi,
					CONCAT(a.kdprogram,'.',a.kdgiat,'.',a.kdoutput,'.',a.kdsoutput,'.',a.kdkmpnen,'.',a.kdskmpnen,'.',a.kdmak) AS kode,
					a.untuk,
					a.totnilmak
				FROM d_transaksi a
				LEFT OUTER JOIN(
					SELECT	a.id_trans,
							b.nourut
					FROM(
						SELECT	id_trans,
								MAX(id) AS id
						FROM d_transaksi_status
						GROUP BY id_trans
					) a
					LEFT OUTER JOIN d_transaksi_status b ON(a.id=b.id)
				) b ON(a.id=b.id_trans)
				WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."'
						AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."'
						AND a.kdgiat=? AND a.kdoutput=? AND SUBSTR(a.kdmak,1,2)=? AND b.nourut>=1 AND a.nodrpp is null
				ORDER BY a.id ASC
			",[
				$arr_data[0],
				$arr_data[1],
				$arr_data[2]
			]);
			
			$data = '<form id="form-'.$id.'">
						<input type="hidden" name="nodrpp" id="nodrpp1-'.$id.'">
						<input type="hidden" name="kdjendrpp" id="kdjendrpp1-'.$id.'">
						<input type="hidden" name="tgdrpp" id="tgdrpp1-'.$id.'">
						<input type="hidden" name="urdrpp" id="urdrpp1-'.$id.'">
						<table class="table table-bordered">
									<thead>
										<tr>
											<th>ID.Trans</th>
											<th>Tgl Kuitansi</th>
											<th>Kode</th>
											<th>Uraian</th>
											<th>Nilai</th>
											<th>Aksi</th>
										</tr>
									</thead>
									<tbody>';
						
			$i=1;
			foreach( $rows as $row )
			{
				$data .= '<tr>
								<td>'.$row->id.'</td>
								<td>'.$row->tgkuitansi.'</td>
								<td>'.$row->kode.'</td>
								<td>'.$row->untuk.'</td>
								<td style="text-align:right;">'.number_format($row->totnilmak).'</td>
								<td><center><input type="checkbox" name="kuitansi[]" value="'.$row->id.'"></center></td>
							</tr>';
				$i++;
			}
			
			$data .= '</tbody></table></form>';
			
			$output['tabel']=$data;
			
			$rows = DB::select("
				SELECT 	0 AS nodrpp,
						date_format(DATE(NOW()),'%d-%m-%Y') AS tgdrpp
			");
			
			$output['nodrpp']=$rows[0]->nodrpp;
			$output['tgdrpp']=$rows[0]->tgdrpp;
			
			return response()->json($output);
			
		}
		else{//sudah DRPP
			
			$rows = DB::select("
				SELECT	a.id,
					date_format(a.tgkuitansi,'%d-%m-%Y') as tgkuitansi,
					CONCAT(a.kdprogram,'.',a.kdgiat,'.',a.kdoutput,'.',a.kdsoutput,'.',a.kdkmpnen,'.',a.kdskmpnen,'.',a.kdmak) AS kode,
					a.untuk,
					a.totnilmak
				FROM d_transaksi a
				LEFT OUTER JOIN(
					SELECT	a.id_trans,
							b.nourut
					FROM(
						SELECT	id_trans,
								MAX(id) AS id
						FROM d_transaksi_status
						GROUP BY id_trans
					) a
					LEFT OUTER JOIN d_transaksi_status b ON(a.id=b.id)
				) b ON(a.id=b.id_trans)
				WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."'
						AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."'
						AND a.kdgiat=? AND a.kdoutput=? AND SUBSTR(a.kdmak,1,2)=? AND a.nodrpp=? AND b.nourut>=1
				ORDER BY a.id ASC
			",[
				$arr_data[0],
				$arr_data[1],
				$arr_data[2],
				$arr_data[3]
			]);
			
			$data = '<form id="form-'.$id.'">
						<input type="hidden" name="nodrpp" id="nodrpp1-'.$id.'">
						<input type="hidden" name="tgdrpp" id="tgdrpp1-'.$id.'">
						<input type="hidden" name="urdrpp" id="urdrpp1-'.$id.'">
						<table class="table table-bordered">
									<thead>
										<tr>
											<th>No Kuitansi</th>
											<th>Tgl Kuitansi</th>
											<th>Kode</th>
											<th>Uraian</th>
											<th>Nilai</th>
										</tr>
									</thead>
									<tbody>';
						
			$i=1;
			foreach( $rows as $row )
			{
				$data .= '<tr>
								<td>'.$row->id.'</td>
								<td>'.$row->tgkuitansi.'</td>
								<td>'.$row->kode.'</td>
								<td>'.$row->untuk.'</td>
								<td style="text-align:right;">'.number_format($row->totnilmak).'</td>
							</tr>';
				$i++;
			}
			
			$data .= '</tbody></table></form>';
			
			$output['tabel']=$data;
			
			return response()->json($output);
			
		}
	}
	
	public function simpan(Request $request)
	{
		try{
			if(count($request->input('kuitansi'))>0){
			
				if($request->input('kdjendrpp')!='' && $request->input('tgdrpp')!=''){
					
					$kuitansi = implode(",", $request->input('kuitansi'));
					
					DB::beginTransaction();
					
					$now = new \DateTime();
					$created_at = $now->format('Y-m-d H:i:s');
					
					$arr_tanggal1 = explode("-", $request->input('tgdrpp'));
					$tgdrpp = $arr_tanggal1[2].'-'.$arr_tanggal1[1].'-'.$arr_tanggal1[0];
					
					$input = array(
						'kddept' => session('kddept'),
						'kdunit' => session('kdunit'),
						'kdsatker' => session('kdsatker'),
						'kddekon' => session('kddekon'),
						'thang' => session('tahun'),
						'kdppk' => session('kdppk'),
						'kdbpp' => session('kdbpp'),
						'kdjendrpp' => $request->input('kdjendrpp'),
						'tgdrpp' => $tgdrpp,
						'urdrpp' => $request->input('urdrpp'),
						'id_user' => session('id_user'),
						'created_at' => $created_at,
						'updated_at' => $created_at
					);
						
					$nodrpp = DB::table('d_drpp')->insertGetId(
						$input
					);
					
					if($nodrpp){
						
						$update = DB::update("
							update d_transaksi
							set nodrpp=?
							where id in(".$kuitansi.")
						",[
							$nodrpp
						]);
						
						if($update){
							
							$arr_trans = $request->input('kuitansi');
							
							for($i=0;$i<count($arr_trans);$i++){
								$query_status[] = "(".$arr_trans[$i].",2,'1',".session('id_user').",now(),now())"; 
							}
							
							$insert = DB::insert("
								insert into d_transaksi_status(id_trans,nourut,terima,id_user,created_at,updated_at)
								values".implode(",", $query_status)."
							");
							
							if($insert){
								DB::commit();
								return 'success';
							}
							else{
								return 'Data berhasil disimpan tetapi gagal mengupdate status!';
							}
						
						}
						else{
							return 'Proses update data transaksi gagal!';
						}
						
					}
					else{
						return 'Proses insert DRPP gagal!';
					}
					
				}
				else{
					return 'Silahkan isi terlebih dahulu jenis drpp dan tanggalnya.';
				}
				
			}
			else{
				return 'Anda belum memilih kuitansi!';
			}
		
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function pilih_transaksi(Request $request, $param)
	{
		try{
			if($param!='xxx'){
				
				$aColumns = array('id','kdppk','kode','nilai');
				/* Indexed column (used for fast and accurate table cardinality) */
				$sIndexColumn = "id";
				/* DB table to use */
				$sTable = "SELECT	a.*,
									c.*
							FROM d_rko a
							LEFT OUTER JOIN(
								SELECT	a.id_rko,
										b.nourut
								FROM(
									SELECT	id_rko,
											MAX(id) AS id
									FROM d_rko_status
									GROUP BY id_rko
								) a
								LEFT OUTER JOIN d_rko_status b ON(a.id=b.id)
							) b ON(a.id=b.id_rko)
							LEFT OUTER JOIN(
								SELECT	a.id_rko,
										GROUP_CONCAT(a.kode) AS kode,
										GROUP_CONCAT(a.nilai) AS nilai
								FROM(
									SELECT	id_rko,
											CONCAT(kdprogram,'.',kdgiat,'.',kdoutput,'.',kdsoutput,'.',kdkmpnen,'.',kdskmpnen,'.',kdakun) AS kode,
											SUM(nilai) AS nilai
									FROM d_rko_pagu2
									GROUP BY id_rko,CONCAT(kdprogram,'.',kdgiat,'.',kdoutput,'.',kdsoutput,'.',kdkmpnen,'.',kdskmpnen,'.',kdakun)
								) a
								GROUP BY a.id_rko
							)c ON(a.id=c.id_rko)
							WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."' AND a.kdalur='02' AND b.nourut=5";
				
				/*
				 * Paging
				 */
				$sLimit = "";
				$iDisplayStart=$request->input('iDisplayStart');
				$iDisplayLength=$request->input('iDisplayLength');
				if ( isset( $iDisplayStart ) &&  $iDisplayLength != '-1' ) 
				{
					 $sLimit = "LIMIT ".intval( $request->input('iDisplayStart') ).", ".
						intval( $request->input('iDisplayLength') );
				}
				 
				 
				/*
				 * Ordering
				 */
				$sOrder = "";
				$iSortCol_0=$request->input('iSortCol_0');
				if ( isset($iSortCol_0  ) )
				{
					$sOrder = " ORDER BY ".$aColumns[ intval( $request->input('iSortCol_0') ) ]."
								".($request->input('sSortDir_0')==='asc' ? 'asc' : 'desc') ." ";
				}
				 
				 
				/*
				 * Filtering
				 */
				$sWhere = "";
				$sSearch=$request->input('sSearch');
				if ( isset($sSearch) && $sSearch != "" )
				{
					$sWhere = "WHERE (";
					for ( $i=0 ; $i<count($aColumns) ; $i++ )
					{
					 $bSearchable_i=$request->input('bSearchable_'.$i);
						if ( isset($bSearchable_i) && $bSearchable_i == "true" )
						{
							$sWhere .= $aColumns[$i]." LIKE '%".( $request->input('sSearch') )."%' OR ";           
						}
					}
					$sWhere = substr_replace( $sWhere, "", -3 );
					$sWhere .= ')';
				}
				 
				/* Individual column filtering */
				for ( $i=0 ; $i<count($aColumns) ; $i++ )
				{
					if ( isset($bSearchable_i) && $bSearchable_i == "true" && $request->input('sSearch_'.$i) != '' )
					{
						if ( $sWhere == "" )
						{
							$sWhere = "WHERE ";
						}
						else
						{
							$sWhere .= " AND ";
						}
						$sWhere .= $aColumns[$i]." LIKE '%".($request->input('sSearch_'.$i))."%' ";
					}
				}

				/* Data set length after filtering */
				$iFilteredTotal = 0;
				$rows = DB::select("
					SELECT COUNT(*) as jumlah FROM (".$sTable.") a
				");
				$result = (array)$rows[0];
				if($result){
					$iFilteredTotal = $result['jumlah'];
				}
				
				/* Total data set length */
				$iTotal = 0;
				$rows = DB::select("
					SELECT COUNT(".$sIndexColumn.") as jumlah FROM (".$sTable.") a
				");
				$result = (array)$rows[0];
				if($result){
					$iTotal = $result['jumlah'];
				}
			   
				/*
				 * Format Output
				 */
				$output = array(
				"sEcho" => intval($request->input('sEcho')),
					"iTotalRecords" => $iTotal,
					"iTotalDisplayRecords" => $iFilteredTotal,
					"aaData" => array()
				);

				/*
				 * SQL queries
				 */
				$sQuery = "
					SELECT SQL_CALC_FOUND_ROWS no,".str_replace(" , ", " ", implode(", ", $aColumns))."
					FROM   (
						SELECT @rownum:=@rownum+1 as no,a.*
						FROM(".
							$sTable
						.") a,
						(SELECT @rownum:=0) as r
					) a
					$sWhere
					$sOrder
					$sLimit
				";
				
				$rows = DB::select($sQuery);
				
				foreach( $rows as $row )
				{
					$isikode = '<ul>';
					$arr_kode = explode(",", $row->kode);
					for($i=0;$i<count($arr_kode);$i++){
						$isikode .= '<li>'.$arr_kode[$i].'</li>';
					}
					$isikode .= '</ul>';
					
					$isinilai = '<ul>';
					$arr_nilai = explode(",", $row->nilai);
					for($i=0;$i<count($arr_nilai);$i++){
						$isinilai .= '<li style="text-align:right;">'.number_format($arr_nilai[$i]).'</li>';
					}
					$isinilai .= '</ul>';
					
					$output['aaData'][] = array(
						$row->no,
						$row->id,
						$row->kdppk,
						$isikode,
						$isinilai,
						'<center>
							<input type="checkbox" name="pilih_rko['.$row->id.']">
						</center>'
					);
				}
				
				return response()->json($output);
			}
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function pilih($id)
	{
		try{
			$rows = DB::select("
				SELECT	id,
						kdalur,
						jenisgiat,
						urrko,
						DATE_FORMAT(tgrko,'%d-%m-%Y') AS tgrko,
						tempat,
						thang,
						DATE_FORMAT(tanggal1,'%d-%m-%Y') AS tanggal1,
						DATE_FORMAT(tanggal2,'%d-%m-%Y') AS tanggal2,
						periode1,
						periode2,
						nip_pk1,
						nip_pk2,
						kdbpp,
						kdppk
				FROM d_rko
				WHERE id=?
			",[
				$id
			]);
			
			if(count($rows)==1){
				return response()->json($rows[0]);
			}
		}
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function simpan_lama(Request $request)
	{
		try{
			if($request->input('inp-rekambaru')=='1'){ //tambah
				
				DB::beginTransaction();
				
				$arr_tanggal = explode("-", $request->input('tanggal'));
				$tanggal = $arr_tanggal[2].'-'.$arr_tanggal[1].'-'.$arr_tanggal[0];
				
				$now = new \DateTime();
				$created_at = $now->format('Y-m-d H:i:s');
				
				$id_rekap = DB::table('d_spby')->insertGetId(
					array(
						'kddept' => session('kddept'),
						'kdunit' => session('kdunit'),
						'kdsatker' => session('kdsatker'),
						'kddekon' => session('kddekon'),
						'thang' => session('tahun'),
						'kdalur' => '01',
						'kdppk' => session('kdppk'),
						'nospby' => $request->input('nomor'),
						'tgspby' => $tanggal,
						'urspby' => $request->input('uraian'),
						'id_user' => session('id_user'),
						'created_at' => $created_at,
						'updated_at' => $created_at
					)
				);
				
				if($id_rekap){
					
					$arr_rko = array_keys($request->input('pilih_rko'));
					
					for($i=0;$i<count($arr_rko);$i++){
						$query_status[] = "(".$arr_rko[$i].",6,'1',".session('id_user').",now(),now())"; 
					}
					
					$arr_rko = implode(",", $arr_rko);
					
					$update = DB::update("
						update d_rko
						set nospby=?
						where id in(".$arr_rko.")
					",[
						$request->input('nomor')
					]);
					
					if($update){
						
						$insert = DB::insert("
							insert into d_rko_status(id_rko,nourut,terima,id_user,created_at,updated_at)
							values".implode(",", $query_status)."
						");
						
						if($insert){
							DB::commit();
							return 'success';
						}
						else{
							return 'Data berhasil disimpan tetapi gagal mengupdate status!';
						}
					}
					else{
						return 'Proses update data RKO gagal!';
					}
					
				}
				else{
					return 'Proses simpan rekap gagal!';
				}
				
			}
			
		}
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function hapus(Request $request)
	{
		try{
			DB::beginTransaction();
			
			if($this->cek_status($request->input('nodrpp'))){
				
				$rows = DB::select("
					select	id
					from d_transaksi
					where nodrpp=?
				",[
					$request->input('nodrpp')
				]);
				
				$update = DB::update("
					update d_transaksi set nodrpp=null where nodrpp=?
				",[
					$request->input('nodrpp')
				]);
				
				$delete = DB::delete("
					delete from d_drpp where kddept=? and kdunit=? and kdsatker=? and thang=? and nodrpp=?
				",[
					session('kddept'),
					session('kdunit'),
					session('kdsatker'),
					session('tahun'),
					$request->input('nodrpp')
				]);
				
				foreach($rows as $row){
					$query_status[] = "(".$row->id.",1,'0',".session('id_user').",now(),now())"; 
				}
				
				$insert = DB::insert("
					insert into d_transaksi_status(id_trans,nourut,terima,id_user,created_at,updated_at)
					values".implode(",", $query_status)."
				");
				
				if($insert){
					DB::commit();
					return 'success';
				}
				else{
					return 'Data gagal dihapus!';
				}
				
			}
			else{
				return 'Data tidak dapat dihapus!';
			}
		}
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}

	public function hapus_detil(Request $request)
	{
		try{
			DB::beginTransaction();
			
			if($this->cek_status($request->input('id'))){
				
				$update = DB::update("
					update d_transaksi set nodrpp=null where id=?
				",[
					$request->input('id')
				]);
				
				$delete = DB::delete("
					delete from d_drpp where kddept=? and kdunit=? and kdsatker=? and thang=? and nodrpp=?
				",[
					session('kddept'),
					session('kdunit'),
					session('kdsatker'),
					session('tahun'),
					
				]);
				
				$insert = DB::insert("
					insert d_transaksi_status(id_trans,nourut,terima,id_user,created_at,updated_at)
					values(?,?,?,?,now(),now())
				",[
					$request->input('id'),
					1,
					'0',
					session('id_user')
				]);
				
				if($insert){
					DB::commit();
					return 'success';
				}
				else{
					return 'Data gagal dihapus!';
				}
				
			}
			else{
				return 'Data tidak dapat dihapus!';
			}
			
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function cek_status($nodrpp)
	{
		try{
			$rows = DB::select("
				SELECT	DISTINCT b.nourut
				FROM d_transaksi a
				LEFT OUTER JOIN(
					SELECT	a.id_trans,
							b.nourut
					FROM(
						SELECT	id_trans,
								MAX(id) AS id
						FROM d_transaksi_status
						GROUP BY id_trans
					) a
					LEFT OUTER JOIN d_transaksi_status b ON(a.id=b.id)
				) b on(a.id=b.id_trans)
				WHERE a.kddept=? and a.kdunit=? and a.kdsatker=? and a.thang=? and a.nodrpp=?
			",[
				session('kddept'),
				session('kdunit'),
				session('kdsatker'),
				session('tahun'),
				$nodrpp
			]);
			
			if($rows[0]->nourut==null || $rows[0]->nourut==2){
				return true;
			}
			else{
				return false;
			}
			
		}
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function cek_detil($nospby)
	{
		try{
			$rows = DB::select("
				SELECT	a.nourut,
						b.jml
				FROM(
					SELECT	nourut
					FROM(
						SELECT	MAX(id) AS id
						FROM d_rko_status
						WHERE id_rko in (
							select distinct id as id_rko
							from d_rko
							where nospby=?
						)
					) a
					LEFT OUTER JOIN d_rko_status b ON(a.id=b.id)
				) a,
				(
					SELECT	COUNT(*) AS jml
					FROM d_rko
					WHERE nospby=?
				) b
			",[
				$nospby,
				$nospby
			]);
			
			if(count($rows)>0){
				
				if($rows[0]->jml==0){
					return true;
				}
				
			}
			else{
				return true;
			}
			
		}
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}

}