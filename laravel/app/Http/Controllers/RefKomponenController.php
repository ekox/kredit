<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RefKomponenController extends Controller {

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
			$aColumns = array('kdgiat','kdoutput','nmoutput','sat');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "kdoutput";
			/* DB table to use */
			if(!isset($_GET['kdgiat'])) {
				$sTable = " 
					  SELECT t_output.*
						 FROM t_output
						WHERE kdgiat IN (
								SELECT t_giat.kdgiat
								  FROM t_giat
								 WHERE kddept = '023' AND kdunit = '11')
					ORDER BY kdgiat, kdoutput";
			} else {
				$sTable = " 
					  SELECT t_output.*
						 FROM t_output
						WHERE kdgiat IN (
								SELECT t_giat.kdgiat
								  FROM t_giat
								 WHERE kddept = '023' AND kdunit = '11' AND kdgiat= '".$_GET['kdgiat']."')
					ORDER BY kdgiat, kdoutput";
			}
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
							<a href="javascript:;" id="'.$row->kdoutput.'" title="Ubah data" class="btn btn-primary ubah"><i class="fa fa-pencil"></i></a>
							<a href="javascript:;" id="'.$row->kdoutput.'" title="Hapus" class="btn btn-danger hapus"><i class="fa fa-times"></i></a>
						</center>';
							
				$output['aaData'][] = array(
					$row->no,
					$row->kdgiat,
					$row->kdoutput,
					$row->nmoutput,
					//~ $row->nmjenisgiat,
					//~ $row->urrko,
					//~ $row->tgrko,
					//~ $row->created_at,
					//~ $aksi
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
			if(!isset($_GET['kdgiat'])) {
				$rows = DB::select("
					select	*
					from t_soutput
					WHERE kddept = '".session('kddept')."' AND kdunit = '".session('kdunit')."' 
				");
			} else {
				$rows = DB::select("
					  SELECT t_output.*
						 FROM t_output
						WHERE kdgiat IN (
								SELECT t_giat.kdgiat
								  FROM t_giat
								 WHERE kddept = '".session('kddept')."' AND kdunit = '".session('kdunit')."' AND kdgiat= '".$_GET['kdgiat']."') 
					ORDER BY kdgiat, kdoutput
				");
			} 
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->kdsoutput.'">'.$row->kdgiat.'-'.$row->kdoutput.' - '.$row->kdsoutput.' - '.$row->nmsoutput.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return $e;
		}
	}
	
	public function dipa_dropdown1($kdppk)
	{
		try{
			$where = "";
			
			if($kdppk!='xxx'){
				$where = " and kdppk='".$kdppk."' ";
			}
			
			if(isset($_GET['kdgiat'])){
				$where .= " AND kdgiat='".$_GET['kdgiat']."' ";
			}
			
			if(isset($_GET['kdoutput'])){
				$where .= " AND kdoutput='".$_GET['kdoutput']."' ";
			}
			
			if(isset($_GET['kdsoutput'])){
				$where .= " AND kdsoutput='".$_GET['kdsoutput']."' ";
			}
			
			$rows = DB::select("
				SELECT	a.*,
						IFNULL(b.urkmpnen,'Tidak ada referensi') AS urkmpnen
				FROM(
					SELECT	DISTINCT
						kdgiat,
						kdoutput,
						kdsoutput,
						kdkmpnen
					FROM d_pagu
					WHERE lvl='7' and kdsatker=? and thang=? ".$where."
				) a
				LEFT OUTER JOIN t_kmpnen b ON(a.kdgiat=b.kdgiat AND a.kdoutput=b.kdoutput AND a.kdsoutput=b.kdsoutput and a.kdkmpnen=b.kdkmpnen)
				ORDER BY a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen
			",[
				session('kdsatker'),
				session('tahun')
			]);
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->kdkmpnen.'">'.$row->kdgiat.'-'.$row->kdoutput.' - '.$row->kdsoutput.' - '.$row->kdkmpnen.' - '.$row->urkmpnen.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return $e;
		}
	}
	
}