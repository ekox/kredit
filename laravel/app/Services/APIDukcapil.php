<?php namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use DB;

class APIDukcapil {

    protected $ip_server;
	protected $user;
	protected $pass;
	protected $sertifikat;

    function __construct()
	{
		try{
			$rows = DB::select("
				select	*
				from t_api_setting
				where id=1
			");
			
			if(count($rows)>0){
				
				if($rows[0]->jenis=='1'){
					
					$this->ip_server = $rows[0]->url;
					$this->user = $rows[0]->user;
					$this->pass = $rows[0]->pass;
					
				}
				else{
					return 'Test';
				}
				
			}
			else{
				return 'Settingan API Dukcapil tidak ditemukan! code[01]';
			}
		}
		catch(\Exception $e){
			return 'Settingan API Dukcapil tidak ditemukan! code[02]';
		}
    }
	
	public function getNIK($param)
	{
		$ip = $this->ip_server;
		$user = $this->user;
		$pass = $this->pass;
		$token = md5('0'.md5(date('dmY')).'Dp');
		
		$handle = curl_init($ip);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, true);
		//curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 2);
		//curl_setopt($handle, CURLOPT_CAINFO, $this->sertifikat);
		curl_setopt($handle, CURLOPT_HTTPHEADER, array(
			'Accept: application/json',
			'Content-Type: application/json',
			'USER: '.$user,
			'PASS: '.$pass,
			'PKEY: '.$token
		));
		
		$resp = curl_exec($handle);
		
		curl_close($handle);
		
		return $resp;
	}
	
	public function regUser($access_token, $arr_data)
	{
		$arr_key=array_keys($arr_data);
				
		for($i=0;$i<count($arr_key);$i++){
			if($arr_key[$i]!='status'){
				$data[]=$arr_key[$i].'='.urlencode($arr_data[$arr_key[$i]]);
			}
		}
		
		$ip = $this->ip_server.'rest/v1/user?'.implode("&", $data);
		
		//var_dump($access_token);
		
		$handle = curl_init($ip);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($handle, CURLOPT_CAINFO, $this->sertifikat);
		curl_setopt($handle, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$access_token, 'Content-Type: application/json;charset=UTF-8'));
		$resp = curl_exec($handle);
		
		//var_dump($resp);
		
		curl_close($handle);
		
		return $resp;
	}
	
	public function updateUser($access_token, $sert_id, $arr_data)
	{
		$arr_key=array_keys($arr_data);
				
		for($i=0;$i<count($arr_key);$i++){
			if($arr_key[$i]!='status' && $arr_key[$i]!='sertifikat_id'){
				$data[]=$arr_key[$i].'='.urlencode($arr_data[$arr_key[$i]]);
			}
		}
		
		$ip = $this->ip_server.'rest/v1/user/'.$sert_id.'/update?'.implode("&", $data);
		
		$handle = curl_init($ip);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($handle, CURLOPT_CAINFO, $this->sertifikat);
		curl_setopt($handle, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$access_token, 'Content-Type: application/json;charset=UTF-8'));
		$resp = curl_exec($handle);
		
		curl_close($handle);
		
		return $resp;
	}
	
	
	public function uploadKTP($access_token, $sert_id, $nmfile_ktp)
	{
		$ip = $this->ip_server.'rest/v1/user/'.$sert_id;
		
		//return $this->filektp.$nmfile_ktp;
		
		if (function_exists('curl_file_create')) { // php 5.5+
			$cFile = curl_file_create($this->filektp.$nmfile_ktp);
		}
		else { 
			$cFile = '@' . realpath($this->filektp.$nmfile_ktp);
		}
		
		$post = array('ktp' => $cFile);
		
		//return var_dump($post);
		
		$handle = curl_init();
		curl_setopt($handle, CURLOPT_URL, $ip);
		curl_setopt($handle, CURLOPT_POST,1);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $post);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($handle, CURLOPT_CAINFO, $this->sertifikat);
		curl_setopt($handle, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$access_token));
		$resp = curl_exec ($handle);
		curl_close ($handle);
		
		return $resp;
	}
	
	public function getSertifikat($access_token, $sertifikat_id)
	{
		$ip = $this->ip_server.'rest/v1/user/crt/'.$sertifikat_id.'?produk=Tanda%20Tangan%20Digital';
		
		//var_dump($ip);
		
		$handle = curl_init($ip);
		//curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($handle, CURLOPT_CAINFO, $this->sertifikat);
		curl_setopt($handle, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=UTF-8','Authorization: Bearer '.$access_token));
		$resp = curl_exec($handle);
		
		curl_close($handle);
		
		return $resp;
	}
	
}

?>