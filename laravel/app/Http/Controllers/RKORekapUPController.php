<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use mPDF;

class RKORekapUPController extends Controller {

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
			$aColumns = array('id','nosurat','tgsurat','uraian','nilai');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "	SELECT	a.id,
								a.nosurat,
								date_format(a.tgsurat,'%d-%m-%Y') as tgsurat,
								a.uraian,
								IFNULL(a.nilai,0) AS nilai
						FROM d_rekap_up a
						LEFT OUTER JOIN(
							SELECT	a.id_rekap_up,
									SUM(a.nilai) AS nilai
							FROM d_rko_pagu1 a
							LEFT OUTER JOIN d_rko b ON(a.id_rko=b.id)
							LEFT OUTER JOIN(
								SELECT	a.id_rko,
										b.nourut
								FROM(
									SELECT	id_rko,
											MAX(id) AS id
									FROM d_rko_status
									GROUP BY id_rko
								) a
								LEFT OUTER JOIN d_rko_status b ON(a.id_rko=b.id_rko AND a.id=b.id)
							) c ON(b.id=c.id_rko)
							WHERE b.kddept='".session('kddept')."' AND b.kdunit='".session('kdunit')."' AND b.kdsatker='".session('kdsatker')."' AND b.thang='".session('tahun')."' AND c.nourut>=1
							GROUP BY a.id_rekap_up
						) b ON(a.id=b.id_rekap_up)
						WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdalur='01'";
			
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
									<li><a href="rko/rekap-up/'.$row->id.'/download" target="_blank" title="Cetak data?">Cetak Rekap UP</a></li>
								</ul>
							</div>
							<div class="dropdown pull-right" style="height:1.5vw !important;">
								<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-pencil"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<!--<li><a href="javascript:;" id="'.$row->id.'" title="Ubah data?" class="ubah">Ubah Rekap UP</a></li>-->
									<li><a href="javascript:;" id="'.$row->id.'" title="Hapus data?" class="hapus">Hapus Rekap UP</a></li>
								</ul>
							</div>
						</center>';
							
				$output['aaData'][] = array(
					$row->id,
					$row->nosurat,
					$row->tgsurat,
					$row->uraian,
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
									FROM d_rko_pagu1
									WHERE id_rekap_up is null
									GROUP BY id_rko,CONCAT(kdprogram,'.',kdgiat,'.',kdoutput,'.',kdsoutput,'.',kdkmpnen,'.',kdskmpnen,'.',kdakun)
								) a
								GROUP BY a.id_rko
							)c ON(a.id=c.id_rko)
							WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdalur='01' AND b.nourut=1";
				
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
							<input type="checkbox" name="pilih_rko['.$row->id.']" class="pilih-rko" value="'.$row->nilai.'">
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
					
					$nilai = preg_replace("/[^0-9 \d]/i", "", $request->input('nilai'));
					
					$now = new \DateTime();
					$created_at = $now->format('Y-m-d H:i:s');
					
					$id_rekap = DB::table('d_rekap_up')->insertGetId(
						array(
							'kddept' => session('kddept'),
							'kdunit' => session('kdunit'),
							'kdsatker' => session('kdsatker'),
							'kddekon' => session('kddekon'),
							'thang' => session('tahun'),
							'kdalur' => '01',
							'kdppk' => $request->input('kdppk'),
							'nosurat' => $request->input('nomor'),
							'tgsurat' => $tanggal,
							'nilai' => $nilai,
							'uraian' => $request->input('uraian'),
							'id_user' => session('id_user'),
							'created_at' => $created_at,
							'updated_at' => $created_at
						)
					);
					
					if($id_rekap){
						
						$arr_rko = array_keys($request->input('pilih_rko'));
						
						for($i=0;$i<count($arr_rko);$i++){
							$query_status[] = "(".$arr_rko[$i].",2,'1',".session('id_user').",now(),now())"; 
						}
						
						$arr_rko = implode(",", $arr_rko);
						
						$update = DB::update("
							update d_rko_pagu1
							set id_rekap_up=?
							where id_rko in(".$arr_rko.")
						",[
							$id_rekap
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
				    return 'Anda belum memilih RKO UP/TUP nya';
				}
				
			}
			
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function hapus(Request $request)
	{
		try{
			DB::beginTransaction();
			
			if($this->cek_status($request->input('id')) && $this->cek_detil($request->input('id'))){
				
				//return 'Hapus OK!';
				
				$delete = DB::delete("
					delete from d_rekap_up where id=?
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
							a.id_rekap_up,
							GROUP_CONCAT(a.kode) AS kode,
							GROUP_CONCAT(a.nilai) AS nilai
					FROM(
						SELECT	id_rko,
								id_rekap_up,
								CONCAT(kdprogram,'.',kdgiat,'.',kdoutput,'.',kdsoutput,'.',kdkmpnen,'.',kdskmpnen,'.',kdakun) AS kode,
								SUM(nilai) AS nilai
						FROM d_rko_pagu1
						GROUP BY id_rko,id_rekap_up,CONCAT(kdprogram,'.',kdgiat,'.',kdoutput,'.',kdsoutput,'.',kdkmpnen,'.',kdskmpnen,'.',kdakun)
					) a
					GROUP BY a.id_rko,a.id_rekap_up
				)c ON(a.id=c.id_rko)
				WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND c.id_rekap_up=?
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
	
	public function simpan_detil(Request $request)
	{
		try{
			$id_rko = $request->input('id_rko');
			$kdspj = $request->input('kdspj');
			$pegawai = $request->input('pilih_pegawai');
			$arr_jabatan = $request->input('jabatan_pegawai');
			$arr_nilai = $request->input('nilai_pegawai');
			$tahun = $request->input('tahun');
			$bulan = $request->input('bulan');
			$instansi = $request->input('instansi');
			$nip = $request->input('nip');
			$nama = $request->input('nama');
			$kdjab = $request->input('kdjab');
			$uraian = $request->input('uraian');
			$nilai = preg_replace("/[^0-9 \d]/i", "", $request->input('nilai'));
			$arr_pagu = explode("-", $request->input('id_pagu'));
			$kdprogram = $arr_pagu[0];
			$kdgiat = $arr_pagu[1];
			$kdoutput = $arr_pagu[2];
			$kdsoutput = $arr_pagu[3];
			$kdkmpnen = $arr_pagu[4];
			$kdskmpnen = $arr_pagu[5];
			$kdakun = $arr_pagu[6];
			
			if($kdspj=='01' || $kdspj=='02'){ //pilih pegawai
				
				if(count($pegawai)>0){
					
					$arr_pegawai = array_keys($pegawai);
					
					$arr_data = array();
					for($i=0;$i<count($arr_pegawai);$i++){
						if($arr_jabatan[$arr_pegawai[$i]]!='' && $arr_nilai[$arr_pegawai[$i]]!=''){
							$arr_data[]=" (".$id_rko.",'".$kdspj."','".$kdprogram."','".$kdgiat."','".$kdoutput."','".$kdsoutput."','".$kdkmpnen."','".$kdskmpnen."','".$kdakun."','".$tahun."','".$bulan."','".$instansi."','".$uraian."','".$arr_pegawai[$i]."','".$arr_jabatan[$arr_pegawai[$i]]."','".preg_replace("/[^0-9 \d]/i", "", $arr_nilai[$arr_pegawai[$i]])."',".session('id_user').",now(),now()) ";
						}
					}
					
					if(count($pegawai)==count($arr_data)){
						
						$query = "insert into d_rko_pagu2(id_rko,kdspj,kdprogram,kdgiat,kdoutput,kdsoutput,kdkmpnen,kdskmpnen,kdakun,thang,periode,instansi,uraian,nip,kdjab,nilai,id_user,created_at,updated_at)
									values".implode(",", $arr_data);
									
						DB::beginTransaction();
						
						$insert=DB::insert($query);
						
						if($insert){
							DB::commit();
							return 'success';
						}
						else{
							return 'Data gagal disimpan!';
						}
						
					}
					else{
						return 'Data pegawai yang dipilih wajib diisi kolomnya!';
					}
					
				}
				else{
					return 'Anda belum memilih pegawai!';
				}
				
			}
			else{ //tidak pilih pegawai
				
				$query = "insert into d_rko_pagu2(id_rko,kdspj,kdprogram,kdgiat,kdoutput,kdsoutput,kdkmpnen,kdskmpnen,kdakun,thang,periode,instansi,uraian,nip,nama,kdjab,nilai,id_user,created_at,updated_at)
							value(".$id_rko.",'".$kdspj."','".$kdprogram."','".$kdgiat."','".$kdoutput."','".$kdsoutput."','".$kdkmpnen."','".$kdskmpnen."','".$kdakun."','".$tahun."','".$bulan."','".$instansi."','".$uraian."','".$nip."','".$nama."','".$kdjab."','".preg_replace("/[^0-9 \d]/i", "", $nilai)."',".session('id_user').",now(),now())";
							
				DB::beginTransaction();
				
				$insert=DB::insert($query);
				
				if($insert){
					DB::commit();
					return 'success';
				}
				else{
					return 'Data gagal disimpan!';
				}
				
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
			
			$rows = DB::select("select distinct id_rekap_up from d_rko_pagu1 where id_rko=?",[$request->input('id')]);
			
			$id_rekap_up= $rows[0]->id_rekap_up;
			
			if($this->cek_status($id_rekap_up)){
				
				$update = DB::update("
					update d_rko_pagu1 set id_rekap_up=null where id_rko=?
				",[
					$request->input('id')
				]);
				
				if($update){
					
					$insert = DB::insert("
						insert d_rko_status(id_rko,nourut,terima,id_user,created_at,updated_at)
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
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function cek_status($id_rekap_up)
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
							where id_rekap_up=?
						)
					) a
					LEFT OUTER JOIN d_rko_status b ON(a.id=b.id)
				) a,
				(
					SELECT	COUNT(*) AS jml
					FROM d_rko_pagu1
					WHERE id_rekap_up=?
				) b
			",[
				$id_rekap_up,
				$id_rekap_up
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
	
	/**
	 * description 
	 */
	public function download($param)
	{
		function satker($param) { 
			return DB::select("
				SELECT    a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdbpp, d.nmbpp, d.nipbpp, a.kdppk, e.nmppk, e.nipppk, c.nmsatker
				FROM      d_rko a 
				LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
				LEFT JOIN t_satker c ON a.kdsatker = c.kdsatker
				LEFT JOIN (
					SELECT	
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
				) d ON a.kdsatker=d.kdsatker AND a.thang=d.thang AND a.kdbpp = d.kdbpp
				LEFT JOIN (
					SELECT	
						a.kdsatker,
						a.thang,
						a.kdppk,
						a.nmppk,
						b.nip AS nipppk
					FROM t_ppk a
					LEFT OUTER JOIN(
						SELECT	kdsatker,
							kdppk,
							nip
						FROM t_user
						WHERE kdlevel='06'
					) b ON(a.kdsatker=b.kdsatker AND a.thang='".session('tahun')."' AND a.kdppk=b.kdppk)
				) e ON a.kdsatker=d.kdsatker AND a.thang=d.thang AND a.kdppk = e.kdppk
				WHERE     a.kdalur = '01' AND b.id_rekap_up = ".$param."
			");
		}
		
		function rprogram($param) {
			return DB::select("
				SELECT   CONCAT('023', '.', '11', '.', k.kdprogram) AS kode, k.kdprogram, l.nmprogram, SUM(nilai) AS nilai
				FROM       (SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.kdlokasi, a.kdkabkota, a.nip_pk1, a.nip_pk2,
								   a.kdbpp, a.kdppk, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, b.uraian, b.nilai, b.id_rekap_up
							FROM   d_rko a LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
							WHERE  a.kdalur = '01' AND b.id_rekap_up = ".$param.") k
						 LEFT JOIN t_program l
							  ON l.kddept = '023' AND l.kdunit = '11' AND k.kdprogram = l.kdprogram
				GROUP BY CONCAT('023', '.', '11', '.', k.kdprogram), k.kdprogram, l.nmprogram
			");
		}
		
		function rgiat($param) {
			return DB::select("
				SELECT   CONCAT('023', '.', '11', '.', k.kdprogram, '.', k.kdgiat) AS kode, k.kdprogram, k.kdgiat, l.nmgiat, sum(nilai) AS nilai
				FROM       (SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.kdlokasi, a.kdkabkota, a.nip_pk1, a.nip_pk2,
								   a.kdbpp, a.kdppk, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, b.uraian, b.nilai, b.id_rekap_up
							FROM   d_rko a LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
							WHERE  a.kdalur = '01' AND b.id_rekap_up = ".$param.") k
						 LEFT JOIN t_giat l
							  ON k.kdprogram = l.kdprogram AND k.kdgiat = l.kdgiat
				WHERE    k.kddept = '023' AND k.kdunit = '11' AND k.kdprogram = '04'
				GROUP BY CONCAT('023', '.', '11', '.', k.kdprogram, '.', k.kdgiat), k.kdprogram, k.kdgiat, l.nmgiat
			");
		}
		
		function routput($param, $kdgiat)
		{
			return DB::select("
				SELECT   CONCAT('023', '.', '11', '.', k.kdprogram, '.', k.kdgiat, '.', k.kdoutput) AS kode, k.kdprogram, k.kdgiat, k.kdoutput,
						 if(isnull(l.nmoutput), 'Tidak ada di referensi', l.nmoutput) AS nmoutput, sum(nilai) AS nilai
				FROM       (SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.kdlokasi, a.kdkabkota, a.nip_pk1, a.nip_pk2,
								   a.kdbpp, a.kdppk, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, b.uraian, b.nilai, b.id_rekap_up
							FROM   d_rko a LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
							WHERE  a.kdalur = '01' AND b.id_rekap_up = ".$param.") k
						 LEFT JOIN t_output l
							  ON k.kdoutput = l.kdoutput AND k.kdgiat = l.kdgiat
				WHERE    k.kddept = '023' AND k.kdunit = '11' AND k.kdprogram = '04' AND k.kdgiat = '".$kdgiat."'
				GROUP BY CONCAT('023', '.', '11', '.', k.kdprogram, '.', k.kdgiat, '.', k.kdoutput), k.kdprogram, k.kdgiat, k.kdoutput, if(isnull(l.nmoutput), 'Tidak ada di referensi', l.nmoutput)
			");
		}
		
		function rkomponen($param, $kdgiat, $kdoutput)
		{
			return DB::select("
				SELECT   k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen,
						 if(isnull(l.urkmpnen), 'Tidak ada di referensi', l.urkmpnen) AS urkmpnen, sum(nilai) AS nilai
				FROM       (SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.kdlokasi, a.kdkabkota, a.nip_pk1, a.nip_pk2,
								   a.kdbpp, a.kdppk, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, b.uraian, b.nilai, b.id_rekap_up
							FROM   d_rko a LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
							WHERE  a.kdalur = '01' AND b.id_rekap_up = ".$param.") k
						 LEFT JOIN t_kmpnen l
							  ON k.kdgiat = l.kdgiat AND k.kdoutput=l.kdoutput AND k.kdkmpnen = l.kdkmpnen
				WHERE    k.kddept = '023' AND k.kdunit = '11' AND k.kdprogram = '04' AND k.kdgiat = '".$kdgiat."' AND k.kdoutput = '".$kdoutput."'
				GROUP BY k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, if(isnull(l.urkmpnen), 'Tidak ada di referensi', l.urkmpnen)
			");
		}
		
		function rskomponen($param, $kdgiat, $kdoutput, $kdkmpnen)
		{
			return DB::select("
				SELECT   k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, k.kdskmpnen, if(isnull(l.urskmpnen), 'Tidak ada di referensi', l.urskmpnen) AS urskmpnen,
						 sum(nilai) AS nilai
				FROM       (SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.kdlokasi,
								   a.kdkabkota, a.nip_pk1, a.nip_pk2, a.kdbpp, a.kdppk, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen,
								   b.kdakun, b.uraian, b.nilai, b.id_rekap_up
							FROM   d_rko a LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
							WHERE  a.kdalur = '01' AND b.id_rekap_up = ".$param.") k
						 LEFT JOIN t_skmpnen l
							  ON k.kdgiat = l.kdgiat AND k.kdoutput = l.kdoutput AND k.kdkmpnen = l.kdkmpnen AND k.kdskmpnen = l.kdskmpnen
				WHERE    k.kddept = '023' AND k.kdunit = '11' AND k.kdprogram = '04' AND k.kdgiat = '".$kdgiat."' AND k.kdoutput = '".$kdoutput."' AND k.kdkmpnen = '".$kdkmpnen."'
				GROUP BY k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, k.kdskmpnen, if(isnull(l.urskmpnen), 'Tidak ada di referensi', l.urskmpnen)
			");
		}
		
		function rmak($param, $kdgiat, $kdoutput, $kdkmpnen, $kdskmpnen)
		{
			return DB::select("
				SELECT   k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, k.kdskmpnen, k.kdakun,
						 if(isnull(l.nmmak), 'Tidak ada di referensi', l.nmmak) AS nmmak, sum(nilai) AS nilai
				FROM       (SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.kdlokasi, a.kdkabkota, a.nip_pk1, a.nip_pk2,
								   a.kdbpp, a.kdppk, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, b.uraian, b.nilai, b.id_rekap_up
							FROM   d_rko a LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
							WHERE  a.kdalur = '01' AND b.id_rekap_up = ".$param.") k
						 LEFT JOIN t_mak l
							  ON k.kdakun = l.kdmak
				WHERE    k.kddept = '023' AND k.kdunit = '11' AND k.kdprogram = '04' AND k.kdgiat = '".$kdgiat."' AND k.kdoutput = '".$kdoutput."' AND k.kdkmpnen = '".$kdkmpnen."' AND k.kdskmpnen = '".$kdskmpnen."'
				GROUP BY k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, k.kdskmpnen, k.kdakun, if(isnull(l.nmmak), 'Tidak ada di referensi', l.nmmak)
			");	
		}
		
		function ruraian($param, $kdgiat, $kdoutput, $kdkmpnen, $kdskmpnen, $kdakun)
		{
			return DB::select("
				SELECT   k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, k.kdskmpnen, k.kdakun, k.uraian,
						 sum(nilai) AS nilai
				FROM       (SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.kdlokasi, a.kdkabkota, a.nip_pk1, a.nip_pk2,
								   a.kdbpp, a.kdppk, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, b.uraian, b.nilai, b.id_rekap_up
							FROM   d_rko a LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
							WHERE  a.kdalur = '01' AND b.id_rekap_up = ".$param.") k
				WHERE    k.kddept = '023' AND k.kdunit = '11' AND k.kdprogram = '04' AND k.kdgiat = '".$kdgiat."' AND k.kdoutput = '".$kdoutput."' AND k.kdkmpnen = '".$kdkmpnen."' AND k.kdskmpnen = '".$kdskmpnen."' AND k.kdakun = '".$kdakun."'
				GROUP BY k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, k.kdskmpnen, k.kdakun, k.uraian
			");
		}
		
		$html_out = HTMLController::css();
		$html_out.= '<table border="0" class="fz50">
			<thead>
				<tr>
					<th class="" colspan="3">
						RINCIAN RENCANA PENGGUNAAN UANG PERSEDIAAN (UP)<br/>
						SUMBER DANA : RUPIAH MURNI (RM) TAHUN '.Session::get('tahun').'<br/>
					</th>
				</tr>
				<tr>
					<th class="" colspan="3">&nbsp;</th>
				</tr>
				<tr>
					<th class="vt tl wd30">NAMA SATKER</th>
					<th class="vt tl wd2">:</th>
					<th class="vt tl">Badan Penelitian dan Pengembangan Pendidikan dan Kebudayaan</th>
				</tr>
				<tr>
					<th class="vt tl">KODE SATKER</th>
					<th class="vt tl">:</th>
					<th class="vt tl">'.satker($param)[0]->kdsatker.'</th>
				</tr>
				<tr>
					<th class="vt tl">NOMOR DAN TANGGAL DIPA</th>
					<th class="vt tl">:</th>
					<th class="vt tl">SP DIPA- 023.11.1.137608/2017 Tgl. 07 Desember 2016</th>
				</tr>
				<tr>
					<th class="vt tl">JUMLAH UP-RM</th>
					<th class="vt tl">:</th>
					<th class="vt tl">'.$this->rupiah(rprogram($param)[0]->nilai).'</th>
				</tr>
				<tr>
					<th class="vt tl">UNIT KERJA</th>
					<th class="vt tl">:</th>
					<th class="vt tl">BADAN STANDAR NASIONAL PENDIDIKAN (BSNP)</th>
				</tr>
			</thead>
		</table>';
		$html_out.= '<table border="0" cellpadding="2" cellspacing="-1" class="fz40">
			<tbody>
				<tr>
					<td class="wd5">&nbsp;</td>
					<td class="wd25">&nbsp;</td>
					<td class="wd3">&nbsp;</td>
					<td class="wd7">&nbsp;</td>
					<td class="wd25">&nbsp;</td>
					<td class="wd10">&nbsp;</td>
					<td class="wd8">&nbsp;</td>
					<td class="wd8">&nbsp;</td>
					<td class="wd7">&nbsp;</td>
					<td class="wd12">&nbsp;</td>
				</tr>
				<tr>
					<th class="bd">NO.</th>
					<th class="bd">KODE</th>
					<!--<th class="bd" colspan="2">KELOMPOK AKUN</th>-->
					<th class="bd" colspan="7">URAIAN</th>
					<!--<th class="bd">VOLUME</th>
					<th class="bd">SATUAN</th>-->
					<th class="bd">JUMLAH DIMINTAKAN</th>
				</tr>
				';
		foreach(rprogram($param) as $rp) {
			$html_out.= '
				<tr>
					<td class="bdlr"></td>
					<td class="bdlr vt tl fwb">'.$rp->kode.'</td>
					<td class="bdlr vt fwb" colspan="7">'.$rp->nmprogram.'</td>
					<!--<td class="bdlr">&nbsp;</td>
					<td class="bdlr">&nbsp;</td>-->
					<td class="bdlr vt tr fwb">'.$this->rupiah($rp->nilai).'</td>
				</tr>
			';
			
			foreach(rgiat($param) as $rg) {
				$html_out.= '
				<tr>
					<td class="bdlr bdt bdb"></td>
					<td class="bdlr bdt bdb vt tl fwb">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$rg->kode.'</td>
					<td class="bdlr bdt bdb vt fwb" colspan="7">'.$rg->nmgiat.'</td>
					<!--<td class="bdlr bdt bdb">&nbsp;</td>
					<td class="bdlr bdt bdb">&nbsp;</td>-->
					<td class="bdlr bdt bdb vt tr fwb">'.$this->rupiah($rg->nilai).'</td>
				</tr>
				';
				
				foreach(routput($param, $rg->kdgiat) as $ro) {
					$html_out.= '
				<tr>
					<td class="bdlr bdt bdb"></td>
					<td class="bdlr bdt bdb vt tc fwb">'.$ro->kode.'</td>
					<td class="bdlr bdt bdb vt fwb" colspan="7">'.$ro->nmoutput.'</td>
					<!--<td class="bdlr bdt bdb">&nbsp;</td>
					<td class="bdlr bdt bdb">&nbsp;</td>-->
					<td class="bdlr bdt bdb vt tr fwb">'.$this->rupiah($ro->nilai).'</td>
				</tr>	
					';
					
					foreach(rkomponen($param, $rg->kdgiat, $ro->kdoutput) as $rk) {
						$html_out.= '
				<tr>
					<td class="bdlr bdb"></td>
					<td class="bdlr bdb vt tr fwb">'.$rk->kdkmpnen.'</td>
					<td class="bdlr bdb vt fwb" colspan="7">'.$rk->urkmpnen.'</td>
					<!--<td class="bdlr bdb fwb">&nbsp;</td>
					<td class="bdlr bdb fwb">&nbsp;</td>-->
					<td class="bdlr bdb vt tr fwb">'.$this->rupiah($rk->nilai).'</td>
				</tr>
						';
						
						foreach(rskomponen($param, $rg->kdgiat, $ro->kdoutput, $rk->kdkmpnen) as $rs) {
							$html_out.= '
				<tr>
					<td class="bdlr bdb"></td>
					<td class="bdlr bdb"></td>
					<td class="bdl bdb vt tr">'.$rs->kdskmpnen.'</td>
					<td class="bdr bdb vt tl" colspan="6">'.$rs->urskmpnen.'</td>
					<!--<td class="bdlr bdb">&nbsp;</td>
					<td class="bdlr bdb">&nbsp;</td>-->
					<td class="bdlr bdb vt tr">'.$this->rupiah($rs->nilai).'</td>
				</tr>
							';
							
							foreach(rmak($param, $rg->kdgiat, $ro->kdoutput, $rk->kdkmpnen, $rs->kdskmpnen) as $rm) {
								$html_out.= '
				<tr>
					<td class="bdlr"></td>
					<td class="bdlr"></td>
					<td class="bdl vt tr">&nbsp;</td>
					<td class="vt tr">'.$rm->kdakun.'</td>
					<td class="bdr vt" colspan="5">'.$rm->nmmak.'</td>
					<!--<td class="bdlr">&nbsp;</td>
					<td class="bdlr">&nbsp;</td>-->
					<td class="bdlr vt tr">'.$this->rupiah($rm->nilai).'</td>
				</tr>
								';
								
								foreach(ruraian($param, $rg->kdgiat, $ro->kdoutput, $rk->kdkmpnen, $rs->kdskmpnen, $rm->kdakun) as $ru) {
									$html_out.= '
				<tr>
					<td class="bdlr"></td>
					<td class="bdlr"></td>
					<td class="bdl vt tr">&nbsp;</td>
					<td class="vt tr">&nbsp;</td>
					<td class="bdr vt" colspan="5">-&nbsp;'.$ru->uraian.'</td>
					<!--<td class="bdlr">&nbsp;</td>
					<td class="bdlr">&nbsp;</td>-->
					<td class="bdlr vt tr">'.$this->rupiah($ru->nilai).'</td>
				</tr>
									';
								}
							}
						}
					}
				}
			}
		}
		$html_out.= '
				<tr>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
				</tr>
				';
		$html_out.= '
			</tbody>
		</table>';
		$html_out.= '<table border="0" cellpadding="2" cellspacing="-1" class="fz50">
			<tbody>
				<tr>
					<td class="wd30">&nbsp;</td>
					<td class="wd40">&nbsp;</td>
					<td class="wd30 tl">Jakarta, </td>
				</tr>
				<tr>
					<td class="wd30">Kuasa Pengguna Anggaran,</td>
					<td class="wd40">&nbsp;</td>
					<td class="wd30">Bendahara Pengeluaran</td>
				</tr>
				<tr>
					<td class="wd30" colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td class="wd30" colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td class="wd30" colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td class="wd30">'.HTMLController::KPA()->nmkpa.'</td>
					<td class="wd40">&nbsp;</td>
					<td class="wd30">'.HTMLController::BP()->nmbp.'</td>
				</tr>
				<tr>
					<td class="wd30">NIP '.HTMLController::KPA()->nipkpa.'</td>
					<td class="wd40">&nbsp;</td>
					<td class="wd30">NIP '.HTMLController::BP()->nipbp.'</td>
				</tr>
			</tbody>
		</table>';
		//~ return $html_out;
		$mpdf = new mPDF("en", "A4", "15");
		$mpdf->SetTitle('Form RKO_UP');
		$mpdf->AddPage('P');
		$mpdf->writeHTML($html_out);
		$mpdf->Output('Rekap_Rincian_RKO_UP_'.$id.'.pdf','I');
	}
	
	public function cek_detil($id_rekap_up)
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
							where id_rekap_up=?
						)
					) a
					LEFT OUTER JOIN d_rko_status b ON(a.id=b.id)
				) a,
				(
					SELECT	COUNT(*) AS jml
					FROM d_rko_pagu1
					WHERE id_rekap_up=?
				) b
			",[
				$id_rekap_up,
				$id_rekap_up
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
	
	public function upload(Request $request)
	{
		try{
			$arr_data = explode("-", $request->input('data'));
			$id_rko = $arr_data[0];
			$kddok = $arr_data[1];
			
			$rows = DB::select("
				select	tipe
				from t_dok
				where id=?
			",[
				$kddok
			]);
			
			if(count($rows)>0){
				
				$tipe = $rows[0]->tipe;
				
				$targetFolder = 'data\dok\\'; // Relative to the root
			
				if(!empty($_FILES)) {
					$file_name = $_FILES['file']['name'];
					$tempFile = $_FILES['file']['tmp_name'];
					$targetFile = $targetFolder.$file_name;
					$fileTypes = array($tipe); // File extensions
					$fileParts = pathinfo($_FILES['file']['name']);
					$fileSize = $_FILES['file']['size'];
					//type file sesuai..??	
					if(in_array($fileParts['extension'],$fileTypes)) {
						
						//isi kosong..??
						if($fileSize>0){
							
							$now = new \DateTime();
							$tglupload = $now->format('YmdHis');
							
							$file_name_baru=$id_rko.'_'.$kddok.'_'.session('kdsatker').'_'.$tglupload.'.'.$fileParts['extension'];
							move_uploaded_file($tempFile,$targetFolder.$file_name_baru);
							
							if(file_exists($targetFolder.$file_name_baru)){
								
								$insert = DB::insert("
									insert into d_rko_dok(id_rko,id_dok,nmfile,id_user,created_at,updated_at)
									values(?,?,?,?,now(),now())
								",[
									$id_rko,
									$kddok,
									$file_name_baru,
									session('id_user')
								]);
								
								if($insert){
									return '1';
								}
								else{
									return 'File berhasil diupload, tetapi gagal disimpan!';
								}
								
							}
							else{
								return 'File gagal diupload!';
							}
							
						}
						else{
							return 'Isi file kosong, periksa data anda.';
						}
					}
					else{
						return 'Tipe file tidak sesuai.';
					}
				}
				else{
					return 'Tidak ada file yang diupload.';
				}
				
			}
			else{
				return 'Referensi jenis dokumen tidak ditemukan!';
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
	public function rupiah($angka)
	{
		return HTMLController::rupiah($angka, 0, ',', '.');
	}
}
