<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;

class DebiturMonitoringController extends Controller {

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
	
	private function query_skoring($status,$param1,$param2)
	{
		
	}
	
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
		$arr_where = array();
		if(isset($_GET['nik'])){
			if($_GET['nik']!==''){
				$arr_where[] = " a.nik_rev like '".$_GET['nik']."%' or a.nik_rev like '%".$_GET['nik']."%' or a.nik_rev like '%".$_GET['nik']."' ";
			}
		}
		
		if(isset($_GET['nama'])){
			if($_GET['nama']!==''){
				$arr_where[] = " a.nama_lgkp like '".$_GET['nama']."%' or a.nama_lgkp like '%".$_GET['nama']."%' or a.nama_lgkp like '%".$_GET['nama']."' ";
			}
		}
		
		$str_where = "";
		if(count($arr_where)>0){
			$str_where = "where ".implode(" and ", $arr_where);
		}
		
		$rows = DB::select("
			SELECT	GROUP_CONCAT(column_name) AS kolom,
					GROUP_CONCAT(column_comment) AS komen
			FROM information_schema.columns
			WHERE table_schema = 'DP0' 
			AND table_name = 'TEMP_DEBITUR'
		");
		
		if(count($rows)>0){
			
			$str_kolom = $rows[0]->kolom;
			$arr_komen = explode(",", $rows[0]->komen);
			$arr_kolom = explode(",", $rows[0]->kolom);
			
			$header = '<table class="table table-bordered">
						<thead>
							<tr>';
							
			for($i=0;$i<count($arr_komen);$i++){
				$header .= '<th>'.$arr_komen[$i].'</th>';
			}
			
			$header .= '</tr>
						</thead>
						<tbody>';
						
			$rows = DB::select("
				SELECT	".$str_kolom."
				FROM temp_debitur a
				".$str_where."
			");
			
			foreach($rows as $row){
				
				$row = (array)$row;
				
				$j = 0;
				
				$header .= '<tr>';
				for($j=0;$j<count($arr_kolom);$j++){
					$header .= '<td>'.$row[$arr_kolom[$j]].'</td>';
				}
				$header .= '</tr>';
				
			}
			
			$header .= '</tbody></table>';
			
			return $header;
			
		}
		else{
			return 'Kolom tidak ditemukan!';
		}
		
		/*$rows = DB::select("
			SELECT	a.*
			FROM temp_debitur a
			WHERE a.nik_rev='1017192107770004'
		");
		
		$kolom = */
		
	}
	
}