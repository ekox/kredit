<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RefPajakController extends Controller {

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
			$aColumns = array('id','jenis_transaksi','ppn','pph_21','pph_22','pph_23','pph_24');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "SELECT 
						  id,
						  jenis_transaksi,
						  ppn,
						  pph_21,
						  pph_22,
						  pph_23,
						  pph_24 
						FROM
						  t_ref_pajak 
						ORDER BY jenis_transaksi";
			
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
				$aksi='<center>
							<a href="javascript:;" id="'.$row->id.'" title="Ubah data" class="btn btn-primary ubah"><i class="fa fa-pencil"></i></a>
							<a href="javascript:;" id="'.$row->id.'" title="Hapus" class="btn btn-danger hapus"><i class="fa fa-times"></i></a>
						</center>';
							
				$output['aaData'][] = array(
					$row->id,
					$row->jenis_transaksi,
					$row->ppn,
					$row->pph_21,
					$row->pph_22,
					$row->pph_23,
					$row->pph_24,
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
	
	public function dropdown()
	{
		try{
			$rows = DB::select("
				select	*
				from t_ref_pajak
				order by id asc
			");
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->id.'">'.$row->jenis_transaksi.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return $e;
		}
	}
	
	public function dropdown_spj($param)
	{
		try{
			$where = "";
			if($param!='xxx'){
				$where = "where kdspj='".$param."'";
			}
			
			$rows = DB::select("
				select	*
				from t_ref_pajak
				".$where."
				order by id asc
			");
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->id.'">'.$row->jenis_transaksi.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return $e;
		}
	}
	
	public function hitung($nilai,$id_ref_pajak)
	{
		try{
			$nilai = preg_replace("/[^0-9 \d]/i", "", $nilai);
			
			$rows = DB::select("
				SELECT	ROUND(?*ppn/100) AS ppn,
						ROUND(?*pph_21/100) AS pph_21,
						ROUND(?*pph_22/100) AS pph_22,
						ROUND(?*pph_23/100) AS pph_23,
						ROUND(?*pph_24/100) AS pph_24
				FROM t_ref_pajak
				WHERE id=?
			",[
				$nilai,
				$nilai,
				$nilai,
				$nilai,
				$nilai,
				$id_ref_pajak
			]);
			
			if(count($rows)>0){
				return response()->json($rows[0]);
			}
		}
		catch(\Exception $e){
			return $e;
		}
	}
}