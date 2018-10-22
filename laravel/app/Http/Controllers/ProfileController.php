<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProfileController extends Controller {

	public function __construct()
	{
		//
	}
	
	public function index()
	{
		$rows = DB::select("
			select  nama,
					nik,
					telp,
					email,
					alamat,
					foto
			from t_user
			where id=?
		",
			[session('id_user')]
		);
		return response()->json($rows[0]);
	}
	
	public function ubah(Request $request)
	{
		$select = DB::select(
			"select password from t_user
				where id=?
			",
			[session('id_user')]
		);
		
		$password_lama=$select[0]->password;
		
		if(md5($request->input('password_lama'))==$password_lama){
			$password_baru=md5($request->input('password_baru'));
			$update = DB::update(
				"update t_user
					set password=?
					where id=?
				", 
				[
					$password_baru,
					session('id_user')
				]
			);
			
			if($update==true) {
				return 'success';
			} else {
				return 'Proses ubah gagal. Hubungi Administrator.';
			}
			
		}
		else{
			return 'Password tidak valid!';
		}
		
	}
	
	public function upload(Request $request)
	{		
		$targetFolder = 'data\user\\'; // Relative to the root
		
		if (!empty($_FILES)) {
			$file_name = $_FILES['file']['name'];
			$tempFile = $_FILES['file']['tmp_name'];
			$targetFile = $targetFolder.$file_name;
			$fileTypes = array('PNG', 'png', 'JPG', 'jpg'); // File extensions
			$fileParts = pathinfo($_FILES['file']['name']);
			$fileSize = $_FILES['file']['size'];
			
			//type file sesuai..??	
			if (in_array($fileParts['extension'],$fileTypes)) {
				
				//isi kosong..??
				if($fileSize>0){
				
					$file_name=session('username').'.'.$fileParts['extension'];
					
					move_uploaded_file($tempFile,$targetFolder.$file_name);
					
					if(file_exists($targetFolder.$file_name)){
						
						session([
							'upload_foto_user' => $file_name
						]);
						
						echo '1';
						
					}
					else{
						echo 'File gagal diupload.';
					}
	
				}
				else{
					echo 'Isi file kosong, periksa data anda.';
				}
			}
			else{
				echo 'Tipe file tidak sesuai.';
			}
		}
		else{
			echo 'Tidak ada file yang diupload.';
		}
	}

}
