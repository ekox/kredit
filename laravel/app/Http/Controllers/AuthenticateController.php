<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthenticateController extends Controller {

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
			$rows = DB::select("
				select	*
				from t_app_version
				where status='1'
			");
			
			$token = md5(uniqid(rand(), TRUE)).'.'.time();
			session(['login_token' => $token]);
			header("x-frame-options:SAMEORIGIN");
			
			return view('login_baru', array('name_app'=>$rows[0]->nama, 'versi_app'=>$rows[0]->versi, 'token'=>$token));
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function login(Request $request)
	{
		try{
			$username = $request->input('username');
			$password = $request->input('password');
			$tahun = $request->input('tahun');
			$thang = substr($tahun,2,2);
			
			if($username!='' && $password!='' && $thang!=''){
				
				$rows = DB::select("
					select	a.id,
							a.username,
							a.password,
							a.nama,
							a.nik,
							a.email,
							a.foto,
							a.aktif,
							b.kdlevel,
							b.nmlevel,
							c.kdpetugas,
							c.nmpetugas,
							d.versi as app_versi,
							d.nama as app_nama,
							d.ket as app_ket
				from t_user a
				left outer join(
					select	a.id_user,
								a.kdlevel,
								b.nmlevel
					from t_user_level a
					left outer join t_level b on(a.kdlevel=b.kdlevel)
					where a.aktif='1'
				) b on(a.id=b.id_user)
				left outer join(
					select	a.id_user,
								a.kdpetugas,
								b.nmpetugas
					from t_user_petugas a
					left outer join t_petugas b on(a.kdpetugas=b.kdpetugas)
					where a.aktif='1'
				) c on(a.id=c.id_user),
				(
					select	versi,
								nama,
								ket
					from t_app_version
					where status='1'
				) d
				where a.username=?
				",[
					$username
				]);
				
				if(isset($rows[0]) && $rows[0]->password){
				
					if($rows[0]->password==md5($password)){
					
						if($rows[0]->aktif=='1'){
						
							session([
								'authenticated' => true,
								'username' => $rows[0]->username,
								'nama' => $rows[0]->nama,
								'email' => $rows[0]->email,
								'kdlevel' => $rows[0]->kdlevel,
								'nmlevel' => $rows[0]->nmlevel,
								'kdpetugas' => $rows[0]->kdpetugas,
								'nmpetugas' => $rows[0]->nmpetugas,
								'id_user' => $rows[0]->id,
								'foto' => $rows[0]->foto,
								'tahun' => $tahun,
								'thang' => $thang,
								'app_nama' => $rows[0]->app_nama,
								'app_versi' => $rows[0]->app_versi,
								'app_ket' => $rows[0]->app_ket
							]);

							return response()->json(['error' => false,'message' => 'Login berhasil!</br>Selamat Datang']);
							
						}
						else{
							return response()->json(['error' => true,'message' => 'User tidak aktif!']);
						}
						
					}
					else{
						return response()->json(['error' => true,'message' => 'Password salah!']);
					}
				
				}
				else{
					return response()->json(['error' => true,'message' => 'Username tidak terdaftar!']);
				}
				
			}
			else{
				return response()->json(['error' => true,'message' => 'Parameter tidak valid!']);
			}
		}
		catch(\Exception $e){
			return response()->json(['error' => true,'message' => 'Terdapat kesalahan lainnya!'], 503);
		}
	}
	
	public function cek_level()
	{
		return session('kdlevel');
	}
	
	public function logout()
	{
		Session::flush();
		return redirect()->guest('/auth');
	}
}