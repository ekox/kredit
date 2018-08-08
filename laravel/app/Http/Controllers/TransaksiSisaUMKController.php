<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransaksiSisaUMKController extends Controller {

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
			$aColumns = array('id','nocek','tgcek','urcek','id_rko','urrko','nilai');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "	SELECT	a.id,
								a.nocek,
								DATE_FORMAT(a.tgcek,'%d-%m-%Y') AS tgcek,
								a.urcek,
								a.id_rko,
								b.urrko,
								a.nilai
						FROM d_cek_bpp a
						LEFT OUTER JOIN d_rko b ON(a.id_rko=b.id)
						WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND b.kdppk='01'
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
									<li><a href="javascript:;" id="'.$row->id.'" title="Ubah data?" class="ubah">Ubah Data</a></li>
									<li><a href="javascript:;" id="'.$row->id.'" title="Hapus data?" class="hapus">Hapus Data</a></li>
								</ul>
							</div>
						</center>';
				
				$output['aaData'][] = array(
					$row->id,
					$row->nocek,
					$row->tgcek,
					$row->urcek,
					$row->id_rko,
					$row->urrko,
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
				
				$sisa = (int)preg_replace("/[^0-9 \d]/i", "", $request->input('nilai'));
				
				if($sisa>0){
					
					DB::beginTransaction();
					$nilai = preg_replace("/[^0-9 \d]/i", "", $request->input('nilai'));
					
					$insert = DB::insert("
						insert into d_cek_bpp(kddept,kdunit,kdsatker,kddekon,thang,id_rko,nocek,tgcek,urcek,nilai,id_user,created_at,updated_at)
						values(?,?,?,?,?,?,?,str_to_date(?,'%d-%m-%Y'),?,?,?,now(),now())
					",[
						session('kddept'),
						session('kdunit'),
						session('kdsatker'),
						session('kddekon'),
						session('tahun'),
						$request->input('id_rko'),
						$request->input('nomor'),
						$request->input('tanggal'),
						$request->input('uraian'),
						$nilai,
						session('id_user')
					]);
					
					if($insert){
						DB::commit();
						return 'success';
					}
					else{
						return 'Data gagal disimpan!';
					}
					
				}
				else{
					return 'Pengembalian melebihi uang mukanya!';
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
			
			$delete = DB::delete("
				delete from d_cek_bpp where id=?
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
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}

}