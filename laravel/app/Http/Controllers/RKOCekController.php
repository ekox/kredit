<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RKOCekController extends Controller {

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
			$aColumns = array('kdbpp','nmcek','nocek','tgcek','urcek','nilai');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "nocek";
			/* DB table to use */
			$sTable = "	SELECT	a.kdbpp,
								b.nmcek,
								a.nocek,
								date_format(a.tgcek,'%d-%m-%Y') as tgcek,
								a.urcek,
								a.nilai
						FROM d_cek a
						LEFT OUTER JOIN t_cek b ON(a.kdcek=b.kdcek)
						WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."'
						ORDER BY a.id DESC";
						
			
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
									<li><a href="rko/cek/cetak/'.$row->nocek.'" target="_blank" title="Cetak data?">Cetak Uang Muka Kerja</a></li>
								</ul>
							</div>
							<div class="dropdown pull-right" style="height:1.5vw !important;">
								<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-pencil"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<!--<li><a href="javascript:;" id="'.$row->nocek.'" title="Ubah data?" class="ubah">Ubah Rekap UP</a></li>-->
									<li><a href="javascript:;" id="'.$row->nocek.'" title="Hapus data?" class="hapus">Hapus Uang Muka Kerja</a></li>
								</ul>
							</div>
						</center>';
							
				$output['aaData'][] = array(
					$row->nocek,
					$row->kdbpp,
					$row->nmcek,
					$row->nocek,
					$row->tgcek,
					$row->urcek,
					'<div style="text-align:right;">'.number_format($row->nilai).'</div>',
					$aksi
				);
			}
			
			return response()->json($output);
		}
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function pilih_rko(Request $request, $param)
	{
		try{
			if($param!='xxx'){
				
				$aColumns = array('nospby','tgspby','urspby','rko','nilai','nilai_total');
				/* Indexed column (used for fast and accurate table cardinality) */
				$sIndexColumn = "nospby";
				/* DB table to use */
				$sTable = "
							SELECT	a.nospby,
									date_format(a.tgspby,'%d-%m-%Y') as tgspby,
									a.urspby,
									ifnull(b.rko,'') as rko,
									ifnull(b.nilai,0) as nilai,
									ifnull(c.nilai,0) as nilai_total
							FROM d_spby a
							LEFT OUTER JOIN(
								SELECT	a.kddept,
										a.kdunit,
										a.kdsatker,
										a.kddekon,
										a.thang,
										a.nospby,
										GROUP_CONCAT(a.id ORDER BY a.id) AS rko,
										GROUP_CONCAT(a.urrko ORDER BY a.id) AS uraian,
										GROUP_CONCAT(b.nilai ORDER BY a.id) AS nilai
								FROM d_rko a
								LEFT OUTER JOIN(
									SELECT	id_rko,
											SUM(nilai) AS nilai
									FROM d_rko_pagu2
									GROUP BY id_rko
								) b ON(a.id=b.id_rko)
								LEFT OUTER JOIN(
									SELECT	a.id_rko,
											b.nourut
									FROM(
										SELECT	id_rko,
												MAX(id) AS max_id
										FROM d_rko_status
										GROUP BY id_rko
									) a
									LEFT OUTER JOIN d_rko_status b ON(a.max_id=b.id)
								) c ON(a.id=c.id_rko)
								WHERE c.nourut=8
								GROUP BY 	a.kddept,
											a.kdunit,
											a.kdsatker,
											a.kddekon,
											a.thang,
											a.nospby
							) b ON(a.kddept=b.kddept AND a.kdunit=b.kdunit AND a.kdsatker=b.kdsatker AND a.kddekon=b.kddekon AND a.thang=b.thang AND a.nospby=b.nospby)
							LEFT OUTER JOIN(
								SELECT	a.kddept,
										a.kdunit,
										a.kdsatker,
										a.kddekon,
										a.thang,
										a.nospby,
										GROUP_CONCAT(b.nilai ORDER BY a.id) AS nilai
								FROM d_rko a
								LEFT OUTER JOIN(
									SELECT	id_rko,
											SUM(nilai) AS nilai
									FROM d_rko_pagu2
									GROUP BY id_rko
								) b ON(a.id=b.id_rko)
								LEFT OUTER JOIN(
									SELECT	a.id_rko,
											b.nourut
									FROM(
										SELECT	id_rko,
												MAX(id) AS max_id
										FROM d_rko_status
										GROUP BY id_rko
									) a
									LEFT OUTER JOIN d_rko_status b ON(a.max_id=b.id)
								) c ON(a.id=c.id_rko)
								WHERE a.nospby is not null and a.nospby<>''
								GROUP BY 	a.kddept,
											a.kdunit,
											a.kdsatker,
											a.kddekon,
											a.thang,
											a.nospby
							) c on(a.kdsatker=c.kdsatker and a.thang=c.thang and a.nospby=c.nospby)
							WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND (a.nocek is null or a.nocek='') AND b.nilai is not null
							ORDER BY a.nospby DESC";							
				
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
					$arr_kode = explode(",", $row->rko);
					for($i=0;$i<count($arr_kode);$i++){
						$isikode .= '<li>'.$arr_kode[$i].'</li>';
					}
					$isikode .= '</ul>';
					
					$isinilai = '<ul>';
					$arr_nilai = explode(",", $row->nilai);
					$nilai1 = 0;
					for($i=0;$i<count($arr_nilai);$i++){
						$isinilai .= '<li style="text-align:right;">'.number_format($arr_nilai[$i]).'</li>';
						$nilai1 += $arr_nilai[$i];
					}
					$isinilai .= '</ul>';
					
					$isinilaitot = '<ul>';
					$arr_nilaitot = explode(",", $row->nilai_total);
					$nilai2 = 0;
					for($i=0;$i<count($arr_nilaitot);$i++){
						$isinilaitot .= '<li style="text-align:right;">'.number_format($arr_nilaitot[$i]).'</li>';
						$nilai2 += $arr_nilaitot[$i];
					}
					$isinilaitot .= '</ul>';
					
					$aksi = '';
					if($nilai1==$nilai2){
						$aksi = '<center>
									<input type="checkbox" name="pilih_rko['.$row->nospby.']" value="'.$row->rko.'">
								</center>';
					}
					
					$output['aaData'][] = array(
						$row->no,
						$row->nospby,
						$row->tgspby,
						$row->urspby,
						$isikode,
						$isinilai,
						$isinilaitot,
						$aksi
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
	
	public function pilih_rko1(Request $request, $param)
	{
		try{
			if($param!='xxx'){
				
				$aColumns = array('id','urrko','nilai','nosp2d','stssp2d');
				/* Indexed column (used for fast and accurate table cardinality) */
				$sIndexColumn = "id";
				/* DB table to use */
				$sTable = "SELECT	a.id,
								a.urrko,
								d.nilai,
								IFNULL(b.nosp2d,'-') AS nosp2d,
								IF(b.nosp2d IS NULL,0,1) AS stssp2d
							FROM d_rko a
							LEFT OUTER JOIN d_spp b ON(a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.nospp_ls=b.nospp)
							LEFT OUTER JOIN(
								SELECT	a.max_id,
									b.id_rko,
									b.nourut
								FROM(
									SELECT	id_rko,
										MAX(id) AS max_id
									FROM d_rko_status
									GROUP BY id_rko
								) a
								LEFT OUTER JOIN d_rko_status b ON(a.max_id=b.id)
							) c ON(a.id=c.id_rko)
							LEFT OUTER JOIN(
								SELECT	id_rko,
									SUM(nilai) AS nilai
								FROM d_rko_pagu2
								GROUP BY id_rko
							) d ON(a.id=d.id_rko)
							WHERE a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdalur='03' AND b.nospp IS NOT NULL AND c.nourut=7
							ORDER BY c.max_id DESC";							
				
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
					$aksi = '';
					if($row->stssp2d==1){
						$aksi = '<center>
									<input type="checkbox" name="pilih_rko1['.$row->id.']" value="'.$row->id.'">
								</center>';
					}
					
					$output['aaData'][] = array(
						$row->no,
						$row->id,
						$row->urrko,
						'<div style="text-align:right;">'.number_format($row->nilai).'</div>',
						$row->nosp2d,
						$aksi
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
	
	public function simpan(Request $request)
	{
		try{
			if($request->input('inp-rekambaru')=='1'){ //tambah
				
				DB::beginTransaction();
				
				$totnilai = preg_replace("/[^0-9 \d]/i", "", $request->input('nilai'));
				
				$kdbpp='000';
				
				if($request->input('kdcek')=='01'){
					
					if(count($request->input('pilih_rko'))>0){
						
						//$arr_rko = explode(",", array_values($request->input('pilih_rko')));
						$arr_spby = array_keys($request->input('pilih_rko'));
						
						//var_dump($arr_rko);
						
						$rows = DB::select("
							SELECT	a.id,
									a.kdbpp,
									SUM(b.nilai) AS nilai
							FROM d_rko a
							LEFT OUTER JOIN(
								SELECT	id_rko,
									SUM(nilai) AS nilai
								FROM d_rko_pagu2
								GROUP BY id_rko
							) b ON(a.id=b.id_rko)
							WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.nospby IN(".implode(",", $arr_spby).")
							GROUP BY a.id,a.kdbpp
						");
						
						$totnilai = 0;
						foreach($rows as $row){
							$query_status[] = "(".$row->id.",9,'1',".session('id_user').",now(),now())";
							$totnilai += $row->nilai;
							$kdbpp = $row->kdbpp;
						}
						
						if($request->input('kdcek')=='03'){
							$totnilai = 0;
						}
						
						$arr_spby = implode(",", $arr_spby);
						
						$update = DB::update("
							update d_spby
							set nocek=?
							where nospby in(".$arr_spby.")
						",[
							$request->input('nomor')
						]);
						
						if($update){
							
							$insert = DB::insert("
								insert into d_rko_status(id_rko,nourut,terima,id_user,created_at,updated_at)
								values".implode(",", $query_status)."
							");
							
							if(!$insert){
								return 'Data berhasil disimpan tetapi gagal mengupdate status!';
								break;
							}
						}
						else{
							return 'Proses update data RKO gagal!';
							break;
						}
						
					}
					else{
						return 'Pilih data terlebih dahulu!';
						break;
					}
					
				}
				
				if($request->input('kdcek')=='02'){
					
					if(count($request->input('pilih_rko1'))>0){
						
						$arr_rko = array_keys($request->input('pilih_rko1'));
						
						$rows = DB::select("
							SELECT	a.id,
									a.kdbpp,
									a.nospp_ls,
									SUM(b.nilai) AS nilai
							FROM d_rko a
							LEFT OUTER JOIN(
								SELECT	id_rko,
										SUM(nilai) AS nilai
								FROM d_rko_pagu2
								GROUP BY id_rko
							) b ON(a.id=b.id_rko)
							WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.id IN(".implode(",", $arr_rko).")
							GROUP BY a.id,a.kdbpp,a.nospp_ls
						");
						
						$totnilai = 0;
						foreach($rows as $row){
							$query_status[] = "(".$row->id.",8,'1',".session('id_user').",now(),now())";
							$totnilai += $row->nilai;
							$kdbpp = $row->kdbpp;
							
							$update = DB::update("
								update d_spp
								set nocek_ls=?
								where kdsatker=? and thang=? and nospp=?
							",[
								$request->input('nomor'),
								session('kdsatker'),
								session('tahun'),
								$row->nospp_ls
							]);
						
						}
						
						$insert = DB::insert("
							insert into d_rko_status(id_rko,nourut,terima,id_user,created_at,updated_at)
							values".implode(",", $query_status)."
						");
						
						if(!$insert){
							return 'Data berhasil disimpan tetapi gagal mengupdate status!';
							break;
						}
						
					}
					else{
						return 'Pilih data terlebih dahulu!';
						break;
					}
					
				}
				
				$arr_tanggal = explode("-", $request->input('tanggal'));
				$tanggal = $arr_tanggal[2].'-'.$arr_tanggal[1].'-'.$arr_tanggal[0];
				
				$now = new \DateTime();
				$created_at = $now->format('Y-m-d H:i:s');
				
				$id_rekap = DB::table('d_cek')->insertGetId(
					array(
						'kddept' => session('kddept'),
						'kdunit' => session('kdunit'),
						'kdsatker' => session('kdsatker'),
						'kddekon' => session('kddekon'),
						'thang' => session('tahun'),
						'kdbpp' => $kdbpp,
						'kdcek' => $request->input('kdcek'),
						'nocek' => $request->input('nomor'),
						'tgcek' => $tanggal,
						'urcek' => $request->input('uraian'),
						'nilai' => $totnilai,
						'id_user' => session('id_user'),
						'created_at' => $created_at,
						'updated_at' => $created_at
					)
				);
				
				if($id_rekap){
					DB::commit();
					return 'success';
				}
				else{
					return 'Proses simpan rekap gagal!';
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
			
			if($this->cek_detil($request->input('id'))){
				
				$rows = DB::select("
					select	a.kdsatker,
							a.thang,
							a.nocek,
							b.nospp,
							c.id as id_rko
					from d_cek a
					left outer join d_spp b on(a.kdsatker=b.kdsatker and a.thang=b.thang and a.nocek=b.nocek_ls)
					left outer join d_rko c on(b.kdsatker=c.kdsatker and b.thang=c.thang and b.nospp=c.nospp_ls)
					where a.kdsatker=? and a.thang=? and a.nocek=?
				",[
					session('kdsatker'),
					session('tahun'),
					$request->input('id')
				]);
				
				if(count($rows)>0){
					
					foreach($rows as $row){
						
						$update = DB::update("
							update d_spp
							set nocek_ls=null
							where kdsatker=? and thang=? and nospp=?
						",[
							session('kdsatker'),
							session('tahun'),
							$row->nospp
						]);
						
						$query_status[] = "(".$row->id_rko.",7,'1',".session('id_user').",now(),now())";
					
					}
					
					$insert = DB::insert("
						insert into d_rko_status(id_rko,nourut,terima,id_user,created_at,updated_at)
						values".implode(",", $query_status)."
					");
					
					if($insert){
						
						$delete = DB::delete("
							delete from d_cek where nocek=?
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
						return 'Data gagal diupdate!';
					}
				
				}
				else{
					return 'Data tidak ditemukan!';
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
	
	public function detil($id)
	{
		try{
			$rows = DB::select("
				SELECT	a.nospby,
						DATE_FORMAT(a.tgspby,'%d-%m-%Y') AS tgspby,
						a.urspby,
						b.rko,
						b.nilai
				FROM d_spby a
				LEFT OUTER JOIN(
					SELECT	a.kddept,
							a.kdunit,
							a.kdsatker,
							a.kddekon,
							a.thang,
							a.nospby,
							GROUP_CONCAT(a.id ORDER BY a.id) AS rko,
							GROUP_CONCAT(b.nilai ORDER BY a.id) AS nilai
					FROM d_rko a
					LEFT OUTER JOIN(
						SELECT	id_rko,
								SUM(nilai) AS nilai
						FROM d_rko_pagu2
						GROUP BY id_rko
					) b ON(a.id=b.id_rko)
					GROUP BY a.kddept,
							a.kdunit,
							a.kdsatker,
							a.kddekon,
							a.thang,
							a.nospby
				) b ON(a.kddept=b.kddept AND a.kdunit=b.kdunit AND a.kdsatker=b.kdsatker AND a.kddekon=b.kddekon AND a.thang=b.thang AND a.nospby=b.nospby)
				WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.nocek=?
			",[
				$id
			]);
			
			if(count($rows)>0){
				
				$data='<table class="table table-bordered">
						<thead>
							<tr>
								<th>No</th>
								<th>No.SPBy</th>
								<th>Tgl.SPBy</th>
								<th>Ur.SPBy</th>
								<th>ID.RKO</th>
								<th>Nilai</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>';
				
				$x=1;
				foreach($rows as $row){
					
					$isikode = '<ul>';
					$arr_kode = explode(",", $row->rko);
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
									<td>'.$row->nospby.'</td>
									<td>'.$row->tgspby.'</td>
									<td>'.$row->urspby.'</td>
									<td>'.$isikode.'</td>
									<td>'.$isinilai.'</td>
									<td>
										<center>
											<a href="javascript:;" id="'.$row->nospby.'" class="btn btn-xs btn-warning hapus-detil"><i class="fa fa-times"></i></a>
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
					update d_spby set nocek=null where nospby=?
				",[
					$request->input('id')
				]);
				
				if($update){
					
					$rows = DB::select("
						select	a.id
						from d_rko a
						WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.nospby=?
					",[
						$request->input('id')
					]);
					
					foreach($rows as $row){
						$arr_rko[] = "(".$row->id.",8,'0',".session('id_user').",now(),now())";
					}
					
					$insert = DB::insert("
						insert d_rko_status(id_rko,nourut,terima,id_user,created_at,updated_at)
						values".implode(",", $arr_rko)."
					");
					
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
				SELECT	distinct nourut
				FROM d_rko a
				LEFT OUTER JOIN(
					SELECT	a.id_rko,
						b.nourut
					FROM(
						SELECT	id_rko,
							MAX(id) AS max_id
						FROM d_rko_status
						GROUP BY id_rko
					) a
					LEFT OUTER JOIN d_rko_status b ON(a.max_id=b.id)
				) b ON(a.id=b.id_rko)
				WHERE a.nospby=?
			",[
				$nospby
			]);
			
			if(count($rows)>0){
				
				if($rows[0]->nourut==9){
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
	
	public function cek_detil($nocek)
	{
		try{
			$rows = DB::select("
				SELECT	COUNT(*) AS jml
				FROM d_spby
				WHERE nocek=?
			",[
				$nocek
			]);
			
			if($rows[0]->jml==0){
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
	
	/**
	 * description 
	 */
	public function cetakRKO($param)
	{
		try {
			$rows = DB::select("
				SELECT a.kdalur
				FROM d_rko a
				WHERE a.id=".$param."
			");
			
			if($rows[0]->kdalur == '01') {
				return redirect('./rko/up-tup/'.$param.'/download');
			} else {
				return redirect('./rko/gup-ls/'.$param.'/download');
			}
		} catch(Exception $e){
			return $e;
		}
	}
}
