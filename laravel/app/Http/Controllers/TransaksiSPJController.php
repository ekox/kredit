<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransaksiSPJController extends Controller {

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
			$aColumns = array('id','id_rko','urrko','nobuku','tgbuku','nilai','realisasi','kembali');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "	SELECT	a.id,
							a.id_rko,
							b.urrko,
							a.nobuku,
							date_format(a.tgbuku,'%d-%m-%Y') as tgbuku,
							b.nilai,
							a.realisasi,
							a.kembali,
							a.nmfile
						FROM d_spj_rampung a
						LEFT OUTER JOIN (
							select	a.id as id_rko,
									a.urrko,
									b.nilai
							from d_rko a
							left outer join(
								select	id_rko,
										sum(nilai) as nilai
								from d_rko_pagu2
								group by id_rko
							) b on(a.id=b.id_rko)
						) b ON(a.id_rko=b.id_rko)
						WHERE a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."' AND a.kdbpp='".session('kdbpp')."'
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
							<div class="dropdown pull-right" style="height:1.5vw !important;">
								<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-pencil"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<!--<li><a href="javascript:;" id="'.$row->id.'" title="Ubah data?" class="ubah">Ubah Data</a></li>-->
									<li><a href="javascript:;" id="'.$row->id.'" title="Hapus data?" class="hapus">Hapus Data</a></li>
								</ul>
							</div>
						</center>';
				
				$output['aaData'][] = array(
					$row->id,
					$row->id_rko,
					$row->urrko,
					$row->nobuku,
					$row->tgbuku,
					'<div style="text-align:right;">'.number_format($row->nilai).'</div>',
					'<div style="text-align:right;">'.number_format($row->realisasi).'</div>',
					'<div style="text-align:right;">'.number_format($row->kembali).'</div>',
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
	
	public function pilih_rko(Request $request)
	{
		try{
			$aColumns = array('id','urrko','tgrko','nilai');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "	SELECT	a.id,
							a.urrko,
							DATE_FORMAT(a.tgrko,'%d-%m-%Y') AS tgrko,
							IFNULL(c.nilai,0) AS nilai
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
							SELECT	id_rko,
								SUM(nilai) AS nilai
							FROM d_rko_pagu2
							GROUP BY id_rko
						) c ON(a.id=c.id_rko)
						WHERE a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."' AND a.kdbpp='".session('kdbpp')."' AND a.id_jubar=".session('id_user')." AND a.kdalur='03' AND b.nourut=11";
						
			
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
				$aksi='<center>
							<a href="javascript:;" id="'.$row->id.'-'.$row->nilai.'" class="btn btn-xs btn-danger pilih-rko"><i class="fa fa-check"></i></a>
						</center>';
				
				$output['aaData'][] = array(
					$row->id,
					$row->urrko,
					$row->tgrko,
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
	
	public function sisa(Request $request, $param)
	{
		try{
			$rows = DB::select("
				SELECT	a.id_rko,
						format(a.nilai,0) AS nilai_rko,
						format(IFNULL(b.nilai,0),0) AS nilai_kuitansi,
						format(IFNULL(c.nilai,0),0) AS nilai_pengembalian,
						format(a.nilai-IFNULL(b.nilai,0)-IFNULL(c.nilai,0),0) as nilai
				FROM(
					SELECT	id_rko,
							SUM(nilai) AS nilai
					FROM d_rko_pagu2
					GROUP BY id_rko
				) a
				LEFT OUTER JOIN(
					SELECT	a.id_rko,
							SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					GROUP BY a.id_rko
				) b ON(a.id_rko=b.id_rko)
				LEFT OUTER JOIN(
					SELECT 	id_rko,
							sum(nilai) as nilai
					FROM d_cek_bpp
					GROUP BY id_rko
				) c ON(a.id_rko=c.id_rko)
				where a.id_rko=?
			",[
				$param
			]);
			
			if(count($rows)>0){
				return response()->json($rows[0]);
			}
			
		}
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function pilih($param)
	{
		try{
			$rows = DB::select("
				select 	id_rko,
						nocek,
						date_format(tgcek,'%d-%m-%Y') as tgcek,
						urcek,
						format(nilai,0) as nilai
				from d_cek_bpp
				where id=?
			",[
				$param
			]);
			
			if(count($rows)>0){
				return response()->json($rows[0]);
			}
			
		}
		catch(\Exception $e){
			return $e;
		}
	}
	
	public function simpan(Request $request)
	{
		try{
			if($request->input('inp-rekambaru')=='1'){ //tambah
				
				$nilai_umk = (int)preg_replace("/[^0-9 \d]/i", "", $request->input('nilai_umk'));
				$realisasi = (int)preg_replace("/[^0-9 \d]/i", "", $request->input('realisasi'));
				$sisa = $nilai_umk-$realisasi;
				
				if($sisa>0){
					
					DB::beginTransaction();
					
					$insert = DB::insert("
						insert into d_spj_rampung(kdsatker,thang,kdppk,kdbpp,id_rko,nobuku,tgbuku,realisasi,kembali,status,id_user,created_at,updated_at)
						values(?,?,?,?,?,?,str_to_date(?,'%d-%m-%Y'),?,?,?,?,now(),now())
					",[
						session('kdsatker'),
						session('tahun'),
						session('kdppk'),
						session('kdbpp'),
						$request->input('id_rko'),
						$request->input('nobuku'),
						$request->input('tgbuku'),
						$realisasi,
						$sisa,
						0,
						session('id_user')
					]);
					
					if($insert){
						
						$insert = DB::insert("
							insert into d_rko_status(id_rko,nourut,terima,id_user,created_at,updated_at)
							values(?,?,?,?,now(),now())
						",[
							$request->input('id_rko'),
							12,
							1,
							session('id_user')
						]);
						
						if($insert){
							DB::commit();
							return 'success';
						}
						else{
							return 'Proses ubah status gagal!';
						}
						
					}
					else{
						return 'Data gagal disimpan!';
					}
					
				}
				else{
					return 'Realisasi melebihi nilai SP2Dnya!';
				}
				
			}
			else{
				DB::beginTransaction();
				
				$update = DB::update("
					update d_cek_bpp
					set nocek=?,
						tgcek=str_to_date(?,'%d-%m-%Y'),
						urcek=?,
						updated_at=now()
					where id=?
				",[
					$request->input('nomor'),
					$request->input('tanggal'),
					$request->input('uraian'),
					$request->input('inp-id')
				]);
				
				if($update){
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
	
	public function hapus(Request $request)
	{
		try{
			DB::beginTransaction();
			
			$rows = DB::select("
				SELECT	a.id,
					a.id_rko,
					b.nourut
				FROM d_spj_rampung a
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
				) b ON(a.id_rko=b.id_rko)
				WHERE id=?
			",[
				$request->input('id')
			]);
			
			if($rows[0]->nourut=='12'){
				
				$delete = DB::delete("
					delete from d_spj_rampung where id=?
				",[
					$request->input('id')
				]);
				
				if($delete){
					
					$insert = DB::insert("
						insert into d_rko_status(id_rko,nourut,terima,id_user,created_at,updated_at)
						values(?,?,?,?,now(),now())
					",[
						$rows[0]->id_rko,
						11,
						0,
						session('id_user')
					]);
					
					if($insert){
						DB::commit();
						return 'success';
					}
					else{
						return 'Proses ubah status gagal!';
					}
					
				}
				else{
					return 'Proses hapus gagal!';
				}
				
			}
			else{
				return 'Data sudah dikirim, tidak dapat dihapus!';
			}
		}
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}

}