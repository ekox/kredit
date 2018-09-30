<?php

Route::post('/cek-debitur', 'DebiturRekamController@cek_debitur'); //post cek debitur

//otentikasi/login
Route::group(['prefix' => 'auth'], function () {

	Route::get('', 'AuthenticateController@index'); //get login html
	Route::get('/logout', 'AuthenticateController@logout');
	Route::post('', 'AuthenticateController@login'); //post login

});

Route::group(['prefix' => 'home'], function () {
	
	Route::get('', 'HomeController@index'); //get home
	
});

//Route::post('debitur/rekam/debitur/rekam', 'DebiturRekamController@simpan');

//Route::get('debitur/rekam/baru', 'DebiturRekamController@baru')->middleware('role:00.01');

//debitur
Route::group(['prefix' => 'registrasi'], function () {
	
	Route::get('/tanda-terima/{param}', 'DebiturRekamController@tanda_terima');
	
	Route::group(['prefix' => 'debitur'], function () {
		
		Route::get('', 'DebiturRekamController@baru');
		Route::get('/tanda-terima/{param}', 'DebiturRekamController@tanda_terima');
		Route::post('', 'DebiturRekamController@simpan');
		
	});
	
});

//dropdown publik
Route::group(['prefix' => 'dropdown'], function () {
	
	Route::get('/hunian', 'DropdownController@hunian');
	Route::get('/jenis-kredit', 'DropdownController@jenis_kredit');
	Route::get('/tipe-kredit', 'DropdownController@tipe_kredit');
	Route::get('/form-kredit', 'DropdownController@form_kredit');
	Route::get('/jenis-kelamin', 'DropdownController@jenis_kelamin');
	Route::get('/agama', 'DropdownController@agama');
	Route::get('/pendidikan', 'DropdownController@pendidikan');
	Route::get('/pekerjaan', 'DropdownController@pekerjaan');
	Route::get('/kawin', 'DropdownController@kawin');
	Route::get('/bpjs', 'DropdownController@bpjs');
	Route::get('/prop', 'DropdownController@prop');
	Route::get('/kabkota', 'DropdownController@kabkota');
	Route::get('/kabkota/{param}', 'DropdownController@kabkota_param');
	Route::get('/kecamatan', 'DropdownController@kecamatan');
	Route::get('/kecamatan/{param1}/{param2}', 'DropdownController@kecamatan_param');
	Route::get('/kelurahan', 'DropdownController@kelurahan');
	Route::get('/kelurahan/{param1}/{param2}/{param3}', 'DropdownController@kelurahan_param');
	Route::get('/hutang', 'DropdownController@hutang');
	Route::get('/status-skoring', 'DropdownController@status_skoring');
	Route::get('/level', 'DropdownController@level');
	
});

//upload
Route::group(['prefix' => 'upload'], function () {
	
	Route::post('', 'UploadController@simpan');
	
});

//Authenticate only
Route::group(['middleware' => 'auth'], function(){
	
	//root for template
	Route::get('/', 'AppController@index');
	
	//route for CSRF Token
	Route::get('/token', 'AppController@token');
	
	//route for Cek Level
	Route::get('/cek/level', 'AuthenticateController@cek_level');
	
	//Profile
	Route::group(['prefix' => 'profile'], function(){
		
		Route::get('', 'ProfileController@index');
		Route::post('', 'ProfileController@ubah');
		Route::post('/foto', 'ProfileController@upload');
		
	});
	
	//route for Form
	Route::group(['prefix' => 'form'], function(){
		
		//rekam
		Route::group(['prefix' => 'rekam'], function(){
			
			Route::get('', 'FormRekamController@index')->middleware('role:00.01.02');
			Route::get('/download/{param}', 'FormRekamController@download')->middleware('role:00.01.02');
			Route::get('/kuota', 'FormRekamController@kuota')->middleware('role:00.01.02');
			Route::post('', 'FormRekamController@simpan')->middleware('role:00.01.02');
			Route::post('/hapus', 'FormRekamController@hapus')->middleware('role:00.01.02');
		
		});
		
	});
	
	//route for debitur
	Route::group(['prefix' => 'debitur'], function(){
		
		//rekam
		Route::group(['prefix' => 'rekam'], function(){
			
			Route::get('', 'DebiturRekamController@index')->middleware('role:00.01.02');
			Route::get('/otorisasi', 'DebiturRekamController@otorisasi');
			Route::post('', 'DebiturRekamController@simpan')->middleware('role:01.02');
			Route::get('/detil1/{param}', 'DebiturRekamController@detil1')->middleware('role:00.01.02');
			Route::get('/detil2/{param}', 'DebiturRekamController@detil2')->middleware('role:00.01.02');
			Route::get('/detil3/{param}', 'DebiturRekamController@detil3')->middleware('role:00.01.02');
			Route::get('/detil4/{param}', 'DebiturRekamController@detil4')->middleware('role:00.01.02');
			Route::get('/detil5/{param}', 'DebiturRekamController@detil5')->middleware('role:00.01.02');
			Route::get('/detil6/{param}', 'DebiturRekamController@detil6')->middleware('role:00.01.02');
			Route::post('/validasi', 'DebiturRekamController@validasi_dukcapil')->middleware('role:00.01.02');
			Route::post('/batal-validasi', 'DebiturRekamController@batal_validasi')->middleware('role:00');
			Route::post('/penolakan', 'DebiturRekamController@penolakan')->middleware('role:00.01.02');
		
		});
		
		//skoring
		Route::group(['prefix' => 'skoring'], function(){
			
			Route::get('', 'DebiturSkoringController@index')->middleware('role:00');
			Route::get('/pilih/{param1}/{param2}', 'DebiturSkoringController@pilih')->middleware('role:00');
			Route::get('/proses/{param1}/{param2}/{param3}', 'DebiturSkoringController@proses')->middleware('role:00');
			Route::post('', 'DebiturSkoringController@simpan')->middleware('role:00');
			Route::post('/hapus', 'DebiturSkoringController@hapus')->middleware('role:00');
		
		});
		
	});
	
	//route for ref
	Route::group(['prefix' => 'ref'], function(){
		
		//jenis kredit
		Route::group(['prefix' => 'jenis-kredit'], function(){
			
			Route::get('', 'DebiturRekamController@index')->middleware('role:00.01');
		
		});
		
		//user
		Route::group(['prefix' => 'user'], function(){
			
			Route::get('', 'RefUserController@index')->middleware('role:00');
			Route::get('/{param}', 'RefUserController@pilih')->middleware('role:00');
			Route::get('/pilih/dropdown', 'RefUserController@dropdown')->middleware('role:00');
			Route::post('', 'RefUserController@simpan')->middleware('role:00');
			Route::post('/reset', 'RefUserController@reset')->middleware('role:00');
			Route::delete('', 'RefUserController@hapus')->middleware('role:00');
		
		});
		
		//petugas
		Route::group(['prefix' => 'petugas'], function(){
			
			Route::get('', 'RefPetugasController@index')->middleware('role:00');
			Route::get('/{param}', 'RefPetugasController@pilih')->middleware('role:00');
			Route::post('', 'RefPetugasController@simpan')->middleware('role:00');
			Route::get('/dropdown', 'RefPetugasController@dropdown')->middleware('role:00');
		
		});
		
		//kuota
		Route::group(['prefix' => 'kuota'], function(){
			
			Route::get('', 'RefKuotaController@index')->middleware('role:00');
			Route::get('/cari/{param1}/{param2}', 'RefKuotaController@cari')->middleware('role:00');
			Route::post('', 'RefKuotaController@simpan')->middleware('role:00');
		
		});
		
	});

});