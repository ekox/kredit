<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MonitoringController extends Controller {

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
			$aColumns = array('id','nmalur','nmjenisgiat','urrko','tgrko','jml','nilai','created_at','nmstatus');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "	SELECT	a.id,
								b.nmalur,
								c.nmjenisgiat,
								a.urrko,
								DATE_FORMAT(a.tgrko,'%d-%m-%Y') AS tgrko,
								IFNULL(e.jml,0) AS jml,
								IFNULL(e.nilai,0) AS nilai,
								a.created_at,
								f.status as nmstatus
						FROM d_rko a
						LEFT OUTER JOIN t_alur b ON(a.kdalur=b.kdalur)
						LEFT OUTER JOIN t_jenisgiat c ON(a.jenisgiat=c.jenisgiat)
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
						) d ON(a.id=d.id_rko)
						LEFT OUTER JOIN(
							SELECT	id_rko,
									COUNT(*) AS jml,
									SUM(nilai) AS nilai
							FROM d_rko_pagu2
							GROUP BY id_rko
						) e ON(a.id=e.id_rko)
						LEFT OUTER JOIN t_alur_status f ON(a.kdalur=f.kdalur AND d.nourut=f.nourut)
						WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."' AND a.kdalur IN('02','03') AND d.nourut=3
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
				$aksi='<center style="width:50px;">
							<div class="dropdown pull-right" style="height:1.5vw !important;">
								<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-pencil"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="javascript:;" id="'.$row->id.'" title="Ubah data?" class="ubah">Ubah RKO</a></li>
								</ul>
							</div>
						</center>';
							
				$output['aaData'][] = array(
					$row->id,
					$row->id,
					$row->nmalur,
					$row->nmjenisgiat,
					$row->urrko,
					$row->tgrko,
					'<div style="text-align:right;">'.number_format($row->jml).'</div>',
					'<div style="text-align:right;">'.number_format($row->nilai).'</div>',
					$row->created_at,
					$row->nmstatus,
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
	
	public function saldo_up_bp()
	{
		try{
			$rows = DB::select("
				SELECT	a.*,
						a.uang_masuk_up+a.uang_masuk_gup+a.uang_masuk_ls+a.uang_masuk_bpp-a.uang_keluar AS saldo
					FROM(
						SELECT	a.nilai AS uang_masuk_up,
								b.nilai AS uang_masuk_gup,
								e.nilai AS uang_masuk_ls,
								ifnull(c.nilai,0) AS uang_keluar,
								d.nilai AS uang_masuk_bpp
						FROM(
							/* uang masuk (spm up/tup) */
							SELECT	IFNULL(SUM(b.nilai),0) AS nilai
							FROM d_spp a
							LEFT OUTER JOIN d_rekap_up b ON(a.nospp=b.nospp)
							WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.jenis='1' and a.nosp2d is not null
						) a,
						(
							/* uang masuk (spm up/tup) */
							SELECT	IFNULL(SUM(b.nilai),0) AS nilai
							FROM d_spp a
							LEFT OUTER JOIN(
								SELECT	a.kdsatker,
										a.thang,
										a.nospp,
										IFNULL(SUM(b.nilai),0) AS nilai
								FROM d_drpp a
								LEFT OUTER JOIN(
									SELECT	kdsatker,
											thang,
											nodrpp,
											SUM(totnilmak) AS nilai
									FROM d_transaksi
									GROUP BY kdsatker,thang,nodrpp
								) b ON(a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.nodrpp=b.nodrpp)
								GROUP BY a.kdsatker,a.thang,a.nospp
							) b ON(a.kdsatker=b.kdsatker and a.thang=b.thang and a.nospp=b.nospp)
							WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.jenis in('2') and a.nosp2d is not null
						) b,
						(
							/* uang ke bpp (rko gup) */
							SELECT	SUM(IFNULL(d.nilai,0)) AS nilai
							FROM(
								SELECT	id_rko,
									MAX(id) AS max_id
								FROM d_rko_status
								GROUP BY id_rko
							) a
							LEFT OUTER JOIN d_rko_status b ON(a.max_id=b.id)
							LEFT OUTER JOIN d_rko c ON(a.id_rko=c.id)
							LEFT OUTER JOIN(
								SELECT	id_rko,
									SUM(nilai) AS nilai
								FROM d_rko_pagu2
								GROUP BY id_rko
							) d ON(a.id_rko=d.id_rko)
							WHERE c.kddept='".session('kddept')."' AND c.kdunit='".session('kdunit')."' AND c.kdsatker='".session('kdsatker')."' AND c.thang='".session('tahun')."' AND c.kdalur='02' AND b.nourut>=10
						) c,
						(
							/* uang dari bpp (kelebihan uang muka) */
							SELECT IFNULL(SUM(a.nilai),0) AS nilai
							FROM d_cek_bpp a
							left outer join d_rko b on(a.id_rko=b.id)
							WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."'
						) d,
						(
							/* uang masuk (ls bp) */
							SELECT	IFNULL(SUM(b.nilai),0) AS nilai
							FROM d_spp a
							LEFT OUTER JOIN(
								SELECT	a.kdsatker,
										a.thang,
										a.nospp_ls AS nospp,
										IFNULL(SUM(b.nilai),0) AS nilai
								FROM d_rko a
								LEFT OUTER JOIN(
									SELECT	id_rko,
											SUM(nilai) AS nilai
									FROM d_rko_pagu2
									GROUP BY id_rko
								) b ON(a.id=b.id_rko)
								WHERE a.kdalur='03'
								GROUP BY a.kdsatker,a.thang,a.nospp_ls
							) b ON(a.kdsatker=b.kdsatker and a.thang=b.thang and a.nospp=b.nospp)
							WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.jenis in('3') AND a.nosp2d is not null
						) e
					) a
			");
			
			return response()->json($rows[0]);
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function saldo_up_bpp(Request $request)
	{
		try{
			$where='';
			if(session('kdlevel')=='05'){
				$where="where a.kdbpp='".session('kdbpp')."'";
			}
			
			$aColumns = array('kdbpp','nmbpp','uang_masuk1','uang_keluar1','uang_masuk2','uang_keluar2','saldo');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "kdbpp";
			/* DB table to use */
			$sTable = "SELECT	a.*,
								a.uang_masuk1-a.uang_keluar1+a.uang_masuk2-a.uang_keluar2 as saldo
						FROM(
							SELECT	
								x.kdbpp,
								x.nmbpp,
								IFNULL(a.nilai,0) AS uang_masuk1,
								IFNULL(b.nilai,0) AS uang_keluar1,
								IFNULL(c.nilai,0) AS uang_masuk2,
								IFNULL(d.nilai,0) AS uang_keluar2
							FROM (
								select	*
								from t_bpp
								where kdsatker='".session('kdsatker')."' and thang='".session('tahun')."'
							) x
							LEFT OUTER JOIN (
								/* uang masuk dari bp */
								SELECT	c.kdbpp,
									SUM(IFNULL(d.nilai,0)) AS nilai
								FROM(
									SELECT	id_rko,
										MAX(id) AS max_id
									FROM d_rko_status
									GROUP BY id_rko
								) a
								LEFT OUTER JOIN d_rko_status b ON(a.max_id=b.id)
								LEFT OUTER JOIN d_rko c ON(a.id_rko=c.id)
								LEFT OUTER JOIN(
									SELECT	id_rko,
										SUM(nilai) AS nilai
									FROM d_rko_pagu2
									GROUP BY id_rko
								) d ON(a.id_rko=d.id_rko)
								WHERE c.kddept='".session('kddept')."' AND c.kdunit='".session('kdunit')."' AND c.kdsatker='".session('kdsatker')."' AND c.thang='".session('tahun')."' AND c.kdalur='02' AND b.nourut>=10
								GROUP BY c.kdbpp
							) a ON(x.kdbpp=a.kdbpp)
							LEFT OUTER JOIN(
								/* uang keluar ke Juru Bayar */
								SELECT	c.kdbpp,
									SUM(IFNULL(d.nilai,0)) AS nilai
								FROM(
									SELECT	id_rko,
										MAX(id) AS max_id
									FROM d_rko_status
									GROUP BY id_rko
								) a
								LEFT OUTER JOIN d_rko_status b ON(a.max_id=b.id)
								LEFT OUTER JOIN d_rko c ON(a.id_rko=c.id)
								LEFT OUTER JOIN(
									SELECT	id_rko,
										SUM(nilai) AS nilai
									FROM d_rko_pagu2
									GROUP BY id_rko
								) d ON(a.id_rko=d.id_rko)
								WHERE c.kddept='".session('kddept')."' AND c.kdunit='".session('kdunit')."' AND c.kdsatker='".session('kdsatker')."' AND c.thang='".session('tahun')."' AND c.kdalur='02' AND b.nourut>=11
								GROUP BY c.kdbpp
								
								/*SELECT	b.kdbpp,
									SUM(b.nilai) AS nilai
								FROM d_spp a
								LEFT OUTER JOIN(
									SELECT	a.kdsatker,
										a.thang,
										a.kdbpp,
										a.nospp,
										SUM(b.nilai) AS nilai
									FROM d_drpp a
									LEFT OUTER JOIN(
										SELECT	kdsatker,
											thang,
											nodrpp,
											SUM(totnilmak) AS nilai
										FROM d_transaksi
										GROUP BY kdsatker,thang,nodrpp
									) b ON(a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.nodrpp=b.nodrpp)
									GROUP BY a.kdsatker,a.thang,a.kdbpp,a.nospp
								) b ON(a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.nospp=b.nospp)
								WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.jenis='2'
								GROUP BY b.kdbpp*/
							) b ON(x.kdbpp=b.kdbpp)
							LEFT OUTER JOIN(
								/* uang masuk dari juru bayar (pengembalian umk) */
								SELECT	b.kdbpp,
									IFNULL(SUM(a.nilai),0) AS nilai
								FROM d_cek_bpp a
								left outer join d_rko b on(a.id_rko=b.id)
								WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."'
								GROUP BY b.kdbpp
							) c ON(x.kdbpp=c.kdbpp)
							LEFT OUTER JOIN(
								/* uang keluar ke bp(pengembalian umk) */
								SELECT	b.kdbpp,
									IFNULL(SUM(a.nilai),0) AS nilai
								FROM d_cek_bpp a
								left outer join d_rko b on(a.id_rko=b.id)
								WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."'
								GROUP BY b.kdbpp
							) d ON(x.kdbpp=d.kdbpp)
						) a
						".$where;
			
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
				$aColumns = array('kdbpp','uang_masuk','uang_keluar_gup','uang_keluar_bp');
				
				$output['aaData'][] = array(
					$row->kdbpp,
					$row->nmbpp,
					'<div style="text-align:right;">'.number_format($row->uang_masuk1).'</div>',
					'<div style="text-align:right;">'.number_format($row->uang_keluar1).'</div>',
					'<div style="text-align:right;">'.number_format($row->uang_masuk2).'</div>',
					'<div style="text-align:right;">'.number_format($row->uang_keluar2).'</div>',
					'<div style="text-align:right;">'.number_format($row->saldo).'</div>'
				);
			}
			
			return response()->json($output);
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function saldo_up_juru_bayar(Request $request)
	{
		try{
			$where='';
			if(session('kdlevel')=='11'){
				$where="where a.id='".session('id_user')."'";
			}
			
			$aColumns = array('kdbpp','kdppk','nmjubar','uang_masuk','uang_keluar_gup','uang_keluar_bp','saldo');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "kdbpp";
			/* DB table to use */
			$sTable = "SELECT	a.*,
								a.uang_masuk-a.uang_keluar_gup-a.uang_keluar_bp as saldo
						FROM(
							SELECT	
								x.id,
								x.kdbpp,
								x.kdppk,
								x.nama as nmjubar,
								IFNULL(a.nilai,0) AS uang_masuk,
								IFNULL(b.nilai,0) AS uang_keluar_gup,
								IFNULL(c.nilai,0) AS uang_keluar_bp
							FROM t_user x
							LEFT OUTER JOIN (
								/* uang masuk dari bpp */
								SELECT	c.id_jubar,
										SUM(IFNULL(d.nilai,0)) AS nilai
								FROM(
									SELECT	id_rko,
										MAX(id) AS max_id
									FROM d_rko_status
									GROUP BY id_rko
								) a
								LEFT OUTER JOIN d_rko_status b ON(a.max_id=b.id)
								LEFT OUTER JOIN d_rko c ON(a.id_rko=c.id)
								LEFT OUTER JOIN(
									SELECT	id_rko,
										SUM(nilai) AS nilai
									FROM d_rko_pagu2
									GROUP BY id_rko
								) d ON(a.id_rko=d.id_rko)
								WHERE c.kddept='".session('kddept')."' AND c.kdunit='".session('kdunit')."' AND c.kdsatker='".session('kdsatker')."' AND c.thang='".session('tahun')."' AND c.kdalur='02' AND b.nourut>=11
								GROUP BY c.id_jubar
							) a ON(x.id=a.id_jubar)
							LEFT OUTER JOIN(
								/* uang keluar dari kuitansi gup */								
								SELECT	a.id_user as id_jubar,
										SUM(a.totnilmak) AS nilai
								FROM d_transaksi a
								LEFT OUTER JOIN(
									SELECT	b.id_trans,
											b.nourut
									FROM(
										SELECT	id_trans,
												max(id) as max_id
										FROM d_transaksi_status
										GROUP BY id_trans
									) a
									LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
								) c ON(a.id=c.id_trans)
								WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND c.nourut>=1
								GROUP BY a.id_user
							) b ON(x.id=b.id_jubar)
							LEFT OUTER JOIN(
								/* uang keluar ke bp */
								SELECT	a.id_user as id_jubar,
										IFNULL(SUM(a.nilai),0) AS nilai
								FROM d_cek_bpp a
								left outer join d_rko b on(a.id_rko=b.id)
								WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."'
								GROUP BY a.id_user
							) c ON(x.id=c.id_jubar)
							WHERE x.kdlevel='11'
						) a
						".$where;
			
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
				$aColumns = array('kdbpp','uang_masuk','uang_keluar_gup','uang_keluar_bp');
				
				$output['aaData'][] = array(
					$row->kdbpp,
					$row->kdppk,
					$row->nmjubar,
					'<div style="text-align:right;">'.number_format($row->uang_masuk).'</div>',
					'<div style="text-align:right;">'.number_format($row->uang_keluar_gup).'</div>',
					'<div style="text-align:right;">'.number_format($row->uang_keluar_bp).'</div>',
					'<div style="text-align:right;">'.number_format($row->saldo).'</div>'
				);
			}
			
			return response()->json($output);
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function rko_kuitansi(Request $request)
	{
		try{
			$aColumns = array('id','nmalur','nmjenisgiat','status','tgl_umk','tgl_drpp','nilai_rko','nilai_kuitansi','nilai_kembali','sisa');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "SELECT	a.*,
								if(a.kdalur='02',
									a.nilai_rko-a.nilai_kuitansi-a.nilai_kembali,
									0
								)AS sisa
						FROM(
							SELECT	a.id,
									a.kdalur,
									i.nmalur,
									IFNULL(j.nmjenisgiat,'N/A') AS nmjenisgiat,
									IFNULL(h.status,'N/A') AS status,
									IFNULL(DATE_FORMAT(e.created_at,'%d-%m-%Y'),'-') AS tgl_umk,
									IFNULL(DATE_FORMAT(f.created_at,'%d-%m-%Y'),'-') AS tgl_drpp,
									/*IFNULL(DATE_FORMAT(g.created_at,'%d-%m-%Y'),'-') AS tgl_kembali_umk,*/
									IF(a.kdalur='01',
										IFNULL(b.nilai,0),
										IFNULL(c.nilai,0)
									) AS nilai_rko,
									IF(a.kdalur='02',
										IFNULL(f.nilai,0),
										0
									) AS nilai_kuitansi,
									IF(a.kdalur='02',
										IFNULL(g.nilai,0),
										0
									) AS nilai_kembali
							FROM d_rko a
							LEFT OUTER JOIN(
								SELECT	id_rko,
										SUM(nilai) AS nilai
								FROM d_rko_pagu1
								GROUP BY id_rko
							) b ON(a.id=b.id_rko)
							LEFT OUTER JOIN(
								SELECT	id_rko,
										SUM(nilai) AS nilai
								FROM d_rko_pagu2
								GROUP BY id_rko
							) c ON(a.id=c.id_rko)
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
							) d ON(a.id=d.id_rko)
							LEFT OUTER JOIN(
								SELECT	b.id_rko,
										b.created_at
								FROM(
									SELECT	id_rko,
											MAX(id) AS max_id
									FROM d_rko_status
									WHERE nourut=10
									GROUP BY id_rko
								) a
								LEFT OUTER JOIN d_rko_status b ON(a.max_id=b.id)
							) e ON(a.id=e.id_rko)
							LEFT OUTER JOIN(
								SELECT	c.id_rko,
										MAX(b.created_at) AS created_at,
										SUM(c.totnilmak) AS nilai
								FROM(
									SELECT	id_trans,
											MAX(id) AS max_id
									FROM d_transaksi_status
									WHERE nourut=0
									GROUP BY id_trans
								) a
								LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
								LEFT OUTER JOIN d_transaksi c ON(a.id_trans=c.id)
								GROUP BY c.id_rko
							) f ON(a.id=f.id_rko)
							LEFT OUTER JOIN(
								SELECT	id_rko,
										created_at,
										SUM(nilai) AS nilai
								FROM d_cek_bpp
								GROUP BY id_rko,created_at
							) g ON(a.id=g.id_rko)
							LEFT OUTER JOIN t_alur_status h ON(a.kdalur=h.kdalur AND d.nourut=h.nourut)
							LEFT OUTER JOIN t_alur i ON(a.kdalur=i.kdalur)
							LEFT OUTER JOIN t_jenisgiat j ON(a.jenisgiat=j.jenisgiat)
						) a";
			
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
				$output['aaData'][] = array(
					$row->id,
					$row->nmalur,
					$row->nmjenisgiat,
					$row->status,
					$row->tgl_umk,
					$row->tgl_drpp,
					'<div style="text-align:right;">'.number_format($row->nilai_rko).'</div>',
					'<div style="text-align:right;">'.number_format($row->nilai_kuitansi).'</div>',
					'<div style="text-align:right;">'.number_format($row->nilai_kembali).'</div>',
					'<div style="text-align:right;">'.number_format($row->sisa).'</div>'
				);
			}
			
			return response()->json($output);
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function pagu_realisasi1(Request $request)
	{
		try{
			$aColumns = array('kode','pagu','rko','realisasi','sisa');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "kode";
			/* DB table to use */
			$sTable = "SELECT	CONCAT(a.kdprogram,'-',a.kdgiat,'-',a.kdoutput,'-',a.kdsoutput,'-',a.kdkmpnen,'-',a.kdskmpnen,'-',a.kdakun) AS kode,
								a.pagu,
								IFNULL(c.nilai,0) AS rko,
								IFNULL(b.nilai,0) AS realisasi,
								a.pagu-IFNULL(b.nilai,0)-IFNULL(c.nilai,0) AS sisa
							FROM(
								/* pagu akhir dipa */
								SELECT	kdsatker,
									thang,
									kdprogram,
									kdgiat,
									kdoutput,
									kdsoutput,
									kdkmpnen,
									kdskmpnen,
									kdakun,
									SUM(paguakhir) AS pagu
								FROM d_pagu
								WHERE lvl=7
								GROUP BY kdsatker,
									thang,
									kdprogram,
									kdgiat,
									kdoutput,
									kdsoutput,
									kdkmpnen,
									kdskmpnen,
									kdakun
							) a
							LEFT OUTER JOIN(
								
								/* realisasi dari kuitansi */
								SELECT	kdsatker,
									thang,
									kdprogram,
									kdgiat,
									kdoutput,
									kdsoutput,
									kdkmpnen,
									kdskmpnen,
									kdmak,
									SUM(totnilmak) AS nilai
								FROM d_transaksi
								WHERE nodrpp IS NOT NULL AND nodrpp<>''
								GROUP BY kdsatker,
									thang,
									kdprogram,
									kdgiat,
									kdoutput,
									kdsoutput,
									kdkmpnen,
									kdskmpnen,
									kdmak
									
							) b ON(a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.kdprogram=b.kdprogram AND a.kdgiat=b.kdgiat AND a.kdoutput=b.kdoutput AND
								a.kdsoutput=b.kdsoutput AND a.kdkmpnen=b.kdkmpnen AND a.kdskmpnen=b.kdskmpnen AND a.kdakun=b.kdmak)
							LEFT OUTER JOIN(
								
								/* realisasi dari rko */
								SELECT	a.kdsatker,
									a.thang,
									b.kdprogram,
									b.kdgiat,
									b.kdoutput,
									b.kdsoutput,
									b.kdkmpnen,
									b.kdskmpnen,
									b.kdakun,
									SUM(b.nilai) AS nilai
								FROM d_rko_pagu2 b
								LEFT OUTER JOIN d_rko a ON(b.id_rko=a.id)
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
								LEFT OUTER JOIN(
									SELECT DISTINCT id_rko
									FROM d_transaksi
									WHERE nodrpp IS NULL OR nodrpp=''
								) d ON(a.id=d.id_rko)
								WHERE c.nourut>=4 AND d.id_rko IS NOT NULL AND d.id_rko<>''
								GROUP BY a.kdsatker,
									a.thang,
									b.kdprogram,
									b.kdgiat,
									b.kdoutput,
									b.kdsoutput,
									b.kdkmpnen,
									b.kdskmpnen,
									b.kdakun
								
							) c ON(a.kdsatker=c.kdsatker AND a.thang=c.thang AND a.kdprogram=c.kdprogram AND a.kdgiat=c.kdgiat AND a.kdoutput=c.kdoutput AND
								a.kdsoutput=c.kdsoutput AND a.kdkmpnen=c.kdkmpnen AND a.kdskmpnen=c.kdskmpnen AND a.kdakun=c.kdakun)
							ORDER BY a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen,a.kdskmpnen,a.kdakun ASC";
			
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
				$output['aaData'][] = array(
					$row->kode,
					'<div style="text-align:right;">'.number_format($row->pagu).'</div>',
					'<div style="text-align:right;">'.number_format($row->rko).'</div>',
					'<div style="text-align:right;">'.number_format($row->realisasi).'</div>',
					'<div style="text-align:right;">'.number_format($row->sisa).'</div>'
				);
			}
			
			return response()->json($output);
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
}