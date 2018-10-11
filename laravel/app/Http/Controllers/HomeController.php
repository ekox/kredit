<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HomeController extends Controller {

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
	public function index()
	{
		try{
			$cek_status = false;
			$cek_message = '';
			$nik = '';
			$noreg = '';
			
			if(isset($_GET['nik']) && isset($_GET['noreg'])){
				
				if($_GET['nik']!=='' && $_GET['noreg']!==''){
					
					$nik = $_GET['nik'];
					$noreg = $_GET['noreg'];
					
					$rows = DB::select("
						select	a.noreg,
								b.nmstatus
						from d_debitur a
						left outer join t_status_debitur b on(a.status=b.status)
						where a.nik=?
					",[
						$nik
					]);
					
					$cek_status = true;
					
					if(count($rows)>0){
						
						if($rows[0]->noreg==$noreg){
							
							$cek_message = '<h3>'.$rows[0]->nmstatus.'</h3>';
							
						}
						else{
							$cek_message = '<h3>NIK/Kode Pendaftaran tidak valid!</h3>';
						}
						
					}
					else{
						$cek_message = '<h3>NIK/Kode Pendaftaran tidak valid!</h3>';
					}
					
				}
				
			}
			
			$data = array(
				'cek_status' => $cek_status,
				'cek_message' => $cek_message,
				'nik' => $nik,
				'noreg' => $noreg
			);
			
			header("x-frame-options:SAMEORIGIN");
			return view('home', $data);
		}
		catch(\Exception $e){
			return $e;
		}
	}
}