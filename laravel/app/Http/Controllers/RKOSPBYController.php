<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use mPDF;

class RKOSPBYController extends Controller {

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
			$aColumns = array('nospby','tgspby','urspby','jml','nilai');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "nospby";
			/* DB table to use */
			$sTable = "	
						SELECT	a.*,
							IFNULL(b.jml,0) AS jml,
							IFNULL(b.nilai,0) AS nilai
						FROM d_spby a
						LEFT OUTER JOIN(
							SELECT	a.nospby,
									COUNT(*) AS jml,
									SUM(c.nilai) AS nilai
							FROM d_rko a
							LEFT OUTER JOIN(
								SELECT	b.id_rko,
										b.nourut
								FROM(
									SELECT	id_rko,
											MAX(id) AS max_id
									FROM d_rko_status
									GROUP BY id_rko
								) a
								LEFT OUTER JOIN d_rko_status b ON(a.max_id=b.id)
							) b ON(a.id=b.id_rko)
							LEFT OUTER JOIN(
								SELECT 	id_rko,
										SUM(nilai) AS nilai
								FROM d_rko_pagu2
								GROUP BY id_rko
							) c ON(a.id=c.id_rko)
							WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND b.nourut>=4
							GROUP BY a.nospby
						) b ON(a.nospby=b.nospby)
						WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."'
						ORDER BY a.nospby DESC";
						
			
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
			
			foreach( $rows as $row )
			{			
				$aksi='<center style="width:75px;">
							<div class="dropdown pull-right">
								<button class="btn btn-success btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-print"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="rko/spby/'.$row->nospby.'/download" target="_blank" title="Cetak data?">Cetak SPBy</a></li>
								</ul>
							</div>
							<div class="dropdown pull-right" style="height:1.5vw !important;">
								<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-pencil"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<!--<li><a href="javascript:;" id="'.$row->nospby.'" title="Ubah data?" class="ubah">Ubah Rekap UP</a></li>-->
									<li><a href="javascript:;" id="'.$row->nospby.'" title="Hapus data?" class="hapus">Hapus SPBy</a></li>
								</ul>
							</div>
						</center>';
							
				$output['aaData'][] = array(
					$row->nospby,
					$row->nospby,
					$row->tgspby,
					$row->urspby,
					'<div style="text-align:right;">'.number_format($row->jml).'</div>',
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
	
	public function pilih_rko(Request $request, $param)
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
	
	public function simpan(Request $request)
	{
		try{
			if($request->input('inp-rekambaru')=='1'){ //tambah
				
				if(count($request->input('pilih_rko'))>0){
				
					DB::beginTransaction();
				
					$arr_tanggal = explode("-", $request->input('tanggal'));
					$tanggal = $arr_tanggal[2].'-'.$arr_tanggal[1].'-'.$arr_tanggal[0];
					
					$now = new \DateTime();
					$created_at = $now->format('Y-m-d H:i:s');
					
					$nospby = DB::table('d_spby')->insertGetId(
						array(
							'kddept' => session('kddept'),
							'kdunit' => session('kdunit'),
							'kdsatker' => session('kdsatker'),
							'kddekon' => session('kddekon'),
							'thang' => session('tahun'),
							'kdalur' => '01',
							'kdppk' => session('kdppk'),
							//'nospby' => $request->input('nomor'),
							'tgspby' => $tanggal,
							'urspby' => $request->input('uraian'),
							'id_user' => session('id_user'),
							'created_at' => $created_at,
							'updated_at' => $created_at
						)
					);
					
					if($nospby){
						
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
							$nospby
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
				else{
				    return 'Anda belum memilih RKO';
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
			
			if($this->cek_status($request->input('id')) && $this->cek_detil($request->input('id'))){
				
				//return 'Hapus OK!';
				
				$delete = DB::delete("
					delete from d_spby where nospby=?
				",[
					$request->input('id')
				]);
				
				if($delete){
					DB::commit();
					return 'success';
				}
				else{
					return 'Proses hapus gagal!';
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
	
	public function detil($id)
	{
		try{
			$rows = DB::select("
				SELECT	a.*,
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
				WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.nospby=?
			",[
				$id
			]);
			
			if(count($rows)>0){
				
				$data='<table class="table table-bordered">
						<thead>
							<tr>
								<th>No</th>
								<th>ID.RKO</th>
								<th>PPK</th>
								<th>Akun</th>
								<th>Nilai</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>';
				
				$x=1;
				foreach($rows as $row){
					
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
					
					$data .= '<tr>
									<td>'.$x++.'</td>
									<td>'.$row->id_rko.'</td>
									<td>'.$row->kdppk.'</td>
									<td>'.$isikode.'</td>
									<td>'.$isinilai.'</td>
									<td>
										<center>
											<a href="javascript:;" id="'.$row->id_rko.'" class="btn btn-xs btn-warning hapus-detil"><i class="fa fa-times"></i></a>
										</center>
									</td>
								</tr>';
				}
				
				$data .= '</tbody></table>';
				
				return $data;
				
			}
			else{
				return 'Data detil tidak ada';
			}
			
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}

	public function hapus_detil(Request $request)
	{
		try{
			DB::beginTransaction();
			
			$rows = DB::select("select distinct nospby from d_rko where id=?",[$request->input('id')]);
			
			$nospby = $rows[0]->nospby;
			
			if($this->cek_status($nospby)){
				
				$update = DB::update("
					update d_rko set nospby=null where id=?
				",[
					$request->input('id')
				]);
				
				if($update){
					
					$insert = DB::insert("
						insert d_rko_status(id_rko,nourut,terima,id_user,created_at,updated_at)
						values(?,?,?,?,now(),now())
					",[
						$request->input('id'),
						5,
						'0',
						session('id_user')
					]);
					
					if($insert){
						DB::commit();
						return 'success';
					}
					else{
						return 'Data RKO berhasil dibatalkan, tetapi status gagal diubah!';
					}
				}
				else{
					return 'Proses hapus gagal!';
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
	
	public function cek_status($nospby)
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
							select distinct id_rko
							from d_rko_pagu1
							where nospp=?
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
				
				if($rows[0]->nourut==null || $rows[0]->nourut==2){
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
	
	/**
	 * description 
	 */
	public function download($param)
	{
		$rkos = DB::select("
			SELECT a.id, a.tgrko, a.urrko, a.kdalur
			FROM d_rko a
			WHERE a.nospby='".$param."'
		");
		
		if($rkos[0]->kdalur == '01') {
			
			$rows = DB::select("
				SELECT k.nospby, k.tgspby, k.kdbpp, k.kdppk, m.nmbpp, l.nama as nmppk, SUM(nilai) as nilai
				FROM ( SELECT a.id_rko, a.kdspj, a.thang, a.periode, a.nip, a.kdgiat, a.kdoutput, a.kdakun, a.nilai, b.kdalur, b.jenisgiat, b.kdbpp, b.kdppk, c.urspby, c.tgspby, c.nospby
						FROM d_rko_pagu2 a
						LEFT JOIN d_rko b ON a.id_rko = b.id
						LEFT JOIN d_spby c ON b.nospby=c.nospby
						WHERE b.nospby='".$param."'
				) k
				LEFT JOIN t_user l ON k.kdppk=l.kdppk and kdlevel='06'
				LEFT JOIN t_bpp m ON m.kdbpp=k.kdbpp
				GROUP BY k.nospby, k.tgspby, k.kdbpp, k.kdppk, m.nmbpp, l.nama
			");
		} else {
		
			$rows =	DB::select("
				SELECT k.thang, nospby, tgspby, urspby, k.kdppk,l.nipppk, k.kdbpp, m.nipbpp, nip_pk1, nip_pk2, nmppk, nmbpp, SUM(nilai) AS nilai
				FROM ( SELECT 	b.kdsatker,
								a.id_rko, b.thang, a.kdspj, a.id_peg_non, a.nip, a.kdakun, a.nilai, b.urrko, b.tgrko, b.kdbpp, b.kdalur, b.kdppk, b.nip_pk1, b.nip_pk2, c.nospby, c.tgspby, c.nocek, c.urspby
						FROM d_rko_pagu2 a
						LEFT JOIN d_rko b ON a.id_rko = b.id
						LEFT JOIN d_spby c ON b.nospby = c.nospby
						WHERE b.nospby = '".$param."'
				) k
				LEFT JOIN (
					SELECT
						DISTINCT
						a.kdsatker,
						a.thang,
						a.kdppk,
						b.nama as nmppk,
						b.nip AS nipppk
					FROM t_ppk a
					JOIN(
						SELECT	kdsatker,
							kdppk,
							nama,
							nip
						FROM t_user
						WHERE kdlevel='06'
						and username='".session('username')."'
					) b ON(a.kdsatker=b.kdsatker AND a.thang='".session('tahun')."' AND a.kdppk=b.kdppk and a.kdppk='".session('kdppk')."')
				) l ON k.kdsatker=l.kdsatker and k.thang=l.thang and k.kdppk=l.kdppk
				LEFT JOIN (
					SELECT
						DISTINCT
						a.kdsatker,
						a.thang,
						a.kdbpp,
						a.nmbpp,
						b.nip AS nipbpp
					FROM t_bpp a
					LEFT OUTER JOIN(
						SELECT	kdsatker,
							kdbpp,
							nip
						FROM t_user
						WHERE kdlevel='05'
					) b ON(a.kdsatker=b.kdsatker AND a.thang='".session('tahun')."' AND a.kdbpp=b.kdbpp)
				) m ON k.kdsatker=m.kdsatker and k.thang=m.thang and m.kdbpp=k.kdbpp
				GROUP BY k.thang, nospby, tgspby, k.kdppk, k.kdbpp, nip_pk1, nip_pk2, urspby, nmppk, nmbpp
			"); 
		}
		
		$html_out = HTMLController::css();
		$html_out.= '<table class="fz60" border="">
			<tbody>
				<tr>
					<td class="wd20 vm tc" rowspan="2"><img style="width:100px;height:100px;" src="template/img/tut wuri handayani1.jpeg"></td>
					<td class="tc fwb fz110">
						KEMENTERIAN PENDIDIKAN DAN KEBUDAYAAN<br/>
						BADAN PENELITIAN DAN PENGEMBANGAN<br/>
					</td>
				</tr>
				<tr>
					<td class="tc fz80">
						Alamat Kantor : Jl. Jenderal Sudirman, Senayan, Kotak Pos 4104, Jakarta 12041 <br/>
						Telepon : (021) 572-5031,573-3129, 573-7102, 579-00313; Fax: 572-1245, 572-1244, 579-00313 <br/>
						Laman : http://litbang.kemdikbud.go.id
					</td>
				</tr>
			</tbody>
		</table>
		<hr/>';
		
		$html_out.= '<p class="tc fz60 fwb">SURAT PERINTAH BAYAR';
		$html_out.= '<br/>';
		$html_out.= 'Nomor '.$rows[0]->nospby.'/KU-SPBy/PPK-BSNP/'.$this->tglIndo($rows[0]->tgspby)->romawi.'/'.$rows[0]->thang.', Tanggal '.$this->tglIndo($rows[0]->tgspby)->tanggal.' '.$this->tglIndo($rows[0]->tgspby)->bulan.' '.$rows[0]->thang.' </p>';
		$html_out.= '<br/>';
		$html_out.= '<p class="tj fz60">Saya yang bertanda tangan dibawah ini selaku Pejabat Pembuat Komitmen memerintahkan Bendahara Pengeluaran agar melakukan pembayaran sejumlah :</p>';
		$html_out.= '<p class="fz60">Rp '.number_format($rows[0]->nilai,0,",",".").'</p>';
		$html_out.= '<p class="fz60">*** '.strtoupper($this->terbilang($rows[0]->nilai)).' RUPIAH ***</p>';
		$html_out.= '<hr/>';
		$html_out.= '<table class="fz60">
			<tbody>
				<tr>
					<td colspan="" class="">Kepada</td>
					<td colspan="">: Bendahara Pengeluaran Pembantu BSNP</td>
				</tr>
				<tr>
					<td colspan="" class="">Untuk pembayaran</td>
					<td colspan="">: '.$rows[0]->urspby.'</td>
				</tr>
				<tr>
					<td colspan="2" class="">&nbsp;</td>
				</tr>
			</tbody>
		</table>
		<table class="fz60" border="0">
			<tbody>
				<tr>
					<td colspan="5" class="">Atas dasar :</td>
				</tr>
				<tr>
					<td colspan="" class="wd40">1.&nbsp;Kuitansi/bukti pembayaran</td>
					<td colspan="" class="wd2">:</td>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="" class="wd40">2.&nbsp;Nota/bukti penerimaan barang/jasa</td>
					<td colspan="" class="wd2">:</td>
					<td colspan="3">&nbsp;</td>
				</tr>
				';
				foreach($rkos as $rk) {
					$html_out.= '
						<tr>
							<td colspan="" class="wd40"></td>
							<td colspan="" class="wd2"></td>
							<td colspan="" class="wd2">-</td>
							<td colspan="2">RKO Nomor '.$rk->id.' Tanggal '.$this->tglIndo($rk->tgrko)->tanggal.' '.$this->tglIndo($rk->tgrko)->bulan.' '.Session::get('tahun').'</td>
						</tr>';
				}
				
				$html_out.='
				<tr>
					<td colspan="5" class="">&nbsp;</td>
				</tr>
			</tbody>
		</table>
		<table class="fz60">
			<tbody>
				<tr>
					<td colspan="3" class="">Dibebankan pada :</td>
				</tr>
				<tr>
					<td class="wd20">Kegiatan, Output, MAK</td>
					<td class="wd2">:</td>
					<td class=""></td>
				</tr>
				<tr>
					<td class="wd20">Kode</td>
					<td class="wd2">:</td>
					<td class=""></td>
				</tr>
				<tr>
					<td colspan="3" class="">&nbsp;</td>
				</tr>
			</tbody>
		</table>
		';
		$html_out.= '<hr/>';
		$html_out.= '<table class="fz60">
			<tbody>
				<tr>
					<td class="tl wd40"></td>
					<td class="tl wd30"></td>
					<td class="tl">Jakarta, </td>
				</tr>
				<tr>
					<td class="tl">Setuju/lunas dibayar, tanggal</td>
					<td class="tl">Diterima tanggal</td>
					<td class="tl">a.n. Kuasa Pengguna Anggaran</td>
				</tr>
				<tr>
					<td class="">Bendahara Pengeluaran</td>
					<td class="">Penerima Uang Muka Kerja</td>
					<td class="">Pejabat Pembuat Komitmen BSNP</td>
				</tr>
				<tr>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
				</tr>
				<tr>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
				</tr>
				<tr>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
				</tr>
				<tr>
					<td class="">'.HTMLController::BP()->nmbp.'</td>
					<td class="">'.$rows[0]->nmbpp.'</td>
					<td class="">'.$rows[0]->nmppk.'</td>
				</tr>
				<tr>
					<td class="">NIP '.HTMLController::BP()->nipbp.'</td>
					<td class="">NIP '.$rows[0]->nipbpp.'</td>
					<td class="">NIP '.$rows[0]->nipppk.'</td>
				</tr>
			</tbody>
		</table>';
		//~ return $html_out;
		
		$mpdf = new mPDF("en", "A4", "15");
		$mpdf->SetTitle('Form SPBy');
		$mpdf->AddPage('P');
		$mpdf->writeHTML($html_out);
		$mpdf->Output('Form_SPBy_'.$id.'.pdf','I');
	}
	
	/**
	 * description 
	 */
	public function css()
	{		
		return HTMLController::css();
	}
	
	/**
	 * format tanggal Indonesia dengan 
	 */
	public function tglIndo($date) // yyyy-mm-dd
	{
		$tanggal = substr($date,8,2);
		$arr_bulan = [
			'01'=>'Januari', '02'=>'Pebruari', '03'=>'Maret', '04'=>'April',
			'05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus',
			'09'=>'September', '10'=>'Oktober', '11'=>'Nopember', '12'=>'Desember',
		];
		$rom_bulan = [
			'01'=>'I', '02'=>'II', '03'=>'III', '04'=>'IV',
			'05'=>'V', '06'=>'VI', '07'=>'VII', '08'=>'VIII',
			'09'=>'IX', '10'=>'X', '11'=>'XI', '12'=>'II',
		];
		$bln = substr($date,5,2);
		$this->tanggal = $tanggal;
		$this->bulan = $arr_bulan[$bln];
		$this->romawi = $rom_bulan[$bln];
		$this->tahun = substr($date,0,4);
		return $this;
	}
	
	/**
	 * description 
	 */
	public function terbilang($nilai) 
	{		
		return HTMLController::terbilang($nilai);
	}
}
