<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TargetRenstraController extends Controller {

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
			$aColumns = array('id','nosk','noikk','urikk','satuan');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "
				SELECT 	a.id,
						b.nosk,
						a.noikk,
						a.urikk,
						a.satuan
				FROM t_renstra_ikk a
				LEFT OUTER JOIN t_renstra_sk b ON(a.id_sk=b.id)
				WHERE b.kdsatker='".session('kdsatker')."'
				ORDER BY a.id asc
			";
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
			// echo $sQuery;
			$rows = DB::select($sQuery);
			
			foreach( $rows as $row )
			{			
				$aksi='<center>
				 			<a href="javascript:;" id="'.$row->id.'" title="Ubah data" class="btn btn-xs btn-primary ubah"><i class="fa fa-pencil"></i></a>
				 			<a href="javascript:;" id="'.$row->id.'" title="Hapus" class="btn btn-xs btn-danger hapus"><i class="fa fa-times"></i></a>
				 		</center>';
							
				$output['aaData'][] = array(
					$row->id,
					$row->nosk,
					$row->noikk,
					$row->urikk,
					$row->satuan,
					//$aksi
				);
			}
			
			return response()->json($output);
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function pilih_by_tahun($tahun)
	{
		try{
			$rows = DB::select("
				SELECT	a.*
				FROM t_renstra_sk a
				WHERE a.kdsatker=?
				ORDER BY a.id ASC
			",[
				session('kdsatker')
			]);
			
			if(count($rows)>0){
				
				$data = '<table class="table table-bordered">
							<thead>
								<tr>
									<th colspan="3" style="text-align:center;">SK dan IKK</th>
									<th rowspan="2" style="text-align:center;">Target</th>
								</tr>
								<tr>
									<th style="text-align:center;">Kode</th>
									<th style="text-align:center;">Uraian</th>
									<th style="text-align:center;">Satuan</th>
								</tr>
							</thead>
							<tbody>';
				
				foreach($rows as $row){
					
					$data .= '<tr>
								<td style="font-weight: bold;">'.$row->nosk.'</td>
								<td colspan="3" style="font-weight: bold;">'.$row->ursk.'</td>
							</tr>';
							
					$rows_detil = DB::select("
						SELECT	a.*,
							IFNULL(b.target,0) AS target
						FROM(
							SELECT	a.*
							FROM t_renstra_ikk a
							WHERE a.id_sk=?
						) a
						LEFT OUTER JOIN(
							SELECT	a.id_ikk,
								a.target
							FROM d_renstra a
							WHERE a.thang=?
						) b ON(a.id=b.id_ikk)
						ORDER BY a.id ASC
					",[
						$row->id,
						$tahun
					]);
					
					foreach($rows_detil as $row_detil){
						
						$data .= '<tr>
									<td>'.$row_detil->noikk.'</td>
									<td>'.$row_detil->urikk.'</td>
									<td>'.$row_detil->satuan.'</td>
									<td>
										<input name="renstra['.$row_detil->id.']" type="text" class="val_num uang" value="'.number_format($row_detil->target).'" style="text-align:right;">
									</td>
								</tr>';
						
					}
					
				}
				
				$data .= '</tbody></table>';
				
				return $data;
				
			}
			else{
				return 'Data tidak ditemukan!';
			}
		}
		catch(\Exception $e){
			return $e;
		}
	}
	
	public function simpan(Request $request)
	{
		try{
			DB::beginTransaction();
			
			$delete = DB::delete("
				delete from d_renstra
				where thang=? and id_ikk in(
					select	a.id
					from t_renstra_ikk a
					left outer join t_renstra_sk b on(a.id_sk=b.id)
					where b.kdsatker=?
				)
			",[
				$request->input('thang'),
				session('kdsatker')
			]);
			
			$arr_renstra = $request->input('renstra');
			$arr_key = array_keys($request->input('renstra'));
			
			$arr_data = array();
			for($i=0;$i<count($arr_key);$i++){
				$arr_data[]=" (	".$arr_key[$i].",
								'".$request->input('thang')."',
								'".preg_replace("/[^0-9 \d]/i", "", $arr_renstra[$arr_key[$i]])."') ";
			}
			
			$query = "insert into d_renstra(id_ikk,thang,target)
						values".implode(",", $arr_data);
			
			$insert=DB::insert($query);
			
			if($insert){
				DB::commit();
				return 'success';
			}
			else{
				return 'Proses simpan gagal!';
			}
			
		}
		catch(\Exception $e){
			return $e;
		}
	}
}