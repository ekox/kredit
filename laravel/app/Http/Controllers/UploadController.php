<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UploadController extends Controller {

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
	
	public function index()
	{
		try{
			session(array(
				'sesi_upload' => md5(time())
			));
			
			$rows = DB::select("
				select	*
				from t_dok
				order by id asc
			");
			
			if(count($rows)>0){
				
				$data=array();

				$data['htmlout']='';
				$data['jquery']="<script>
								jQuery(document).ready(function(){
									jQuery.extend({
										getValues: function(url) {
											var result = null;
											jQuery.ajax({
												url: url,
												type: 'get',
												async: false,
												success: function(data) {
													result = data;
												}
											});
										   return result;
										}
									});
									
									var token1 = jQuery.getValues('../../token');
								";
				foreach($rows as $row) {
					
					$data['htmlout'] .= '
						<div class="form-group">
							<label class="control-label col-md-3">'.$row->nmdok.' (*'.$row->tipe.')</label>
							<div class="col-md-6" id="div2-upload-foto">
								<span class="btn btn-primary fileinput-button">
									<i class="fa fa-upload"></i>
									<span>Browse File</span>
									<input id="fileupload-'.$row->id.'" type="file" name="file-'.$row->id.'">
								</span>
								<!-- The global progress bar -->
								<div id="files-'.$row->id.'" class="files"></div>
								<div id="progress-'.$row->id.'" class="progress">
									<div class="progress-bar progress-bar-success"></div>
								</div>
							</div>
							<div class="col-lg-3" id="nmfile-'.$row->id.'"></div>
						</div>';
					
					$data['jquery'] .= "
						jQuery('#fileupload-".$row->id."').click(function(){
							jQuery('#progress-".$row->id." .progress-bar').css('width', 0);
							jQuery('#progress-".$row->id." .progress-bar').html('');
							jQuery('#nmfile-".$row->id."').html('');
						});
		
						jQuery('#fileupload-".$row->id."').fileupload({
							url:'../../upload',
							dataType: 'json',
							formData: {
								_token:token1,
								id_dok:".$row->id."
							},
							done: function (e, data) {
								jQuery('#nmfile-".$row->id."').html(data.files[0].name);
								alertify.log('Upload attachment '+data.files[0].name+' berhasil!');
							},
							error: function(error) {
								alertify.log(error.responseText);
							},
							progressall: function (e, data) {
								var progress = parseInt(data.loaded / data.total * 100, 10);
								jQuery('#progress-".$row->id." .progress-bar').css('width',progress + '%');
							}
						}).prop('disabled', !$.support.fileInput)
						  .parent().addClass($.support.fileInput ? undefined : 'disabled');
					";
					
				}
				
				$data['jquery'].="});</script>";
				
				return $data['htmlout'].$data['jquery'];
				
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function simpan(Request $request)
	{
		$id_dok = $request->input('id_dok');
        $rows=DB::select("
			SELECT * FROM t_dok WHERE id=?
        ",[$id_dok]);

        try {
            $destinationPath = 'data/debitur/upload/'.date("dmY").'/';

            //cek folder
            $listing = file_exists($destinationPath);
			if(!$listing){
				mkdir($destinationPath,0777,true);
				//mkdir($destinationPath, 0755, true);
			}
			
			//cek file
			if (!empty($_FILES)) {
				
				$fileName = $_FILES['file-'.$id_dok]['name'];
				$tempFile = $_FILES['file-'.$id_dok]['tmp_name'];
				$fileParts = pathinfo($fileName);
				$targetFile = $destinationPath.$fileName;
				$fileSize = $_FILES['file-'.$id_dok]['size'];

				$jenisFile = $rows[0]->tipe;
				$fileTypes = explode(',', $jenisFile);
				
				// cek type file	
				if (in_array($fileParts['extension'],$fileTypes)) {
					//cek ukuran file
					if($fileSize>0){
						
						$newFileName = $id_dok.'_'.md5(time()).'.'.$fileParts['extension'];
						
						$localUpload = move_uploaded_file($tempFile, $destinationPath.$newFileName);
						if($localUpload){
							session(['upload_dok_'.$id_dok=>$newFileName]);
							setcookie('upload_dok_'.$id_dok, $newFileName, time() + 3600, "/");
							
							$delete = DB::delete("
								delete from d_debitur_dok_temp
								where sesi_upload=? and id_dok=?
							",[
								session('sesi_upload'),$id_dok
							]);
							
							$insert = DB::insert("
										INSERT INTO d_debitur_dok_temp(sesi_upload,id_dok,nmfile,created_at,updated_at)
										VALUES(?,?,?,now(),now())
									", [session('sesi_upload'),$id_dok,$newFileName]);

							if($insert){
								return '1';
							}
							else{
								return 'File gagal diupload ke temporary.';
							}
						}
						else{
							return 'File gagal diupload ke local storage.';
						}
					}
					else{
						return 'Isi file kosong, periksa data Anda.';
					}
				}
				else{
					return 'Tipe file tidak sesuai.';
				}
			}
			else{
				return 'Tidak ada file yang diupload.';
			}
        }
		catch(\Exception $e) {
			//return 'Terdapat kesalahan lainnya, hubungi Administrator!';
			return $e;
		}
	}
	
}