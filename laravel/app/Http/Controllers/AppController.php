<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AppController extends Controller {

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
			// Create menu....
			$menus = DB::select("
				SELECT * FROM t_menu WHERE aktif='1' AND kdlevel LIKE '%+".session('kdlevel')."+%' AND parent_id=0  ORDER BY nourut
			");
			
			$html_out='';
			$angular = 'var app = angular.module("spa", ["ui.router","chieffancypants.loadingBar"]);
						app.config(function($stateProvider, $urlRouterProvider){
						$urlRouterProvider.otherwise("/");
						$stateProvider';
			
			foreach($menus as $menu) {
			
				if($menu->is_parent=='0'){
					//jika tidak, tidak perlu buat sub menu
					
					//apakah buka tab baru?
					if($menu->new_tab=='1'){
						$html_out .= '<li>	
										<a href="'.$menu->url.'" target="_blank">
											<i class="'.$menu->icon.'"></i> '.$menu->nmmenu.'
										</a>
									</li>';
					}
					else{					
						if($menu->url==''){
							$html_out .= '<li>	
											<a ui-sref="/">
												<i class="'.$menu->icon.'"></i> '.$menu->nmmenu.'
											</a>
										</li>';
							$angular .= '.state("/", {
											url: "/",
											templateUrl: "partials/'.$menu->nmfile.'"
										})';
						}
						else{
							$html_out .= '<li>	
											<a ui-sref="'.$menu->url.'">
												<i class="'.$menu->icon.'"></i> '.$menu->nmmenu.'
											</a>
										</li>';
							$angular .= '.state("'.$menu->url.'", {
											url: "/'.$menu->url.'",
											templateUrl: "partials/'.$menu->nmfile.'"
										})';
						}
					}
					
				}
				else{
					//jika ya, perlu buat sub menu dengan parameter parent_id ybs
					$html_out .= '<li class="treeview">
									<a href="javascript:;">
										<i class="'.$menu->icon.'"></i>
										<span>'.$menu->nmmenu.'</span>
										<i class="fa fa-angle-left pull-right"></i>
									</a>
									<ul class="treeview-menu">';
					
					$sub_menus = DB::select("
						SELECT * FROM t_menu WHERE aktif='1' AND kdlevel LIKE '%+".session('kdlevel')."+%' AND parent_id='".$menu->id."' ORDER BY nourut
					");
					
					//bentuk sub menu
					foreach($sub_menus as $sub_menu){
						
						//apakah tab baru?
						if($sub_menu->new_tab=='1'){
							$html_out .= '<li><a href="'.$sub_menu->url.'" target="_blank"><i class="fa fa-angle-double-right"></i> '.$sub_menu->nmmenu.'</a></li>';
						}
						else{
							$html_out .= '<li><a ui-sref="'.$sub_menu->url.'"><i class="fa fa-angle-double-right"></i> '.$sub_menu->nmmenu.'</a></li>';
							$angular .= '.state("'.$sub_menu->url.'", {
											url: "/'.$sub_menu->url.'",
											templateUrl: "partials/'.$sub_menu->nmfile.'"
										})';
						}
						
					}
					
					$html_out .= 	'</ul>
								</li>';
				}
				
			}
			
			$angular .=		'.state("profile", {
								url: "/profile",
								templateUrl: "partials/profile.html"
							});
						});';
			
			$kdsatker='';
			if(session('kdlevel')=='05' || session('kdlevel')=='06'){
				$kdsatker=' ('.session('kdsatker').')';
			}
			
			header("x-frame-options:SAMEORIGIN");
			
			return view('app',
				[
					'menu' => $html_out,
					'angular' => $angular,
					'info_nmkantor' => session('nmpetugas'),
					'info_nmlevel' => session('nmlevel'),
					'info_foto' => session('foto'),
					'info_username' => session('username'),
					'info_nama' => session('nama'),
					'info_email' => session('email'),
					'info_tahun' => session('tahun'),
					'app_name' => session('app_name'),
					'app_versi' => session('app_versi'),
					'app_ket' => session('app_ket')
				]
			);
		}
		catch(\Exception $e){
			return $e;
		}
	}
	
	public function index1()
	{
		try{
			// Create menu....
			$menus = DB::select("
				SELECT * FROM t_menu WHERE aktif='1' AND kdlevel LIKE '%+".session('kdlevel')."+%' AND parent_id=0  ORDER BY nourut
			");
			
			$html_out='';
			$angular = 'var app = angular.module("spa", ["ui.router","chieffancypants.loadingBar"]);
						app.config(function($stateProvider, $urlRouterProvider){
						$urlRouterProvider.otherwise("/");
						$stateProvider';
			
			foreach($menus as $menu) {
			
				if($menu->is_parent=='0'){
					//jika tidak, tidak perlu buat sub menu
					
					//apakah buka tab baru?
					if($menu->new_tab=='1'){
						$html_out .= '<li>	
										<a href="'.$menu->url.'" target="_blank">
											<i class="'.$menu->icon.'"></i>
											<p>'.$menu->nmmenu.'</p>
										</a>
									</li>';
					}
					else{					
						if($menu->url==''){
							$html_out .= '<li>	
											<a ui-sref="/">
												<i class="'.$menu->icon.'"></i>
												<p>'.$menu->nmmenu.'</p>
											</a>
										</li>';
							$angular .= '.state("/", {
											url: "/",
											templateUrl: "partials/'.$menu->nmfile.'"
										})';
						}
						else{
							$html_out .= '<li>
											<a ui-sref="'.$menu->url.'">
												<i class="'.$menu->icon.'"></i>
												<p>'.$menu->nmmenu.'</p>
											</a>
										</li>';
							$angular .= '.state("'.$menu->url.'", {
											url: "/'.$menu->url.'",
											templateUrl: "partials/'.$menu->nmfile.'"
										})';
						}
					}
					
				}
				else{
					//jika ya, perlu buat sub menu dengan parameter parent_id ybs
					$html_out .= '<li>
									<a data-toggle="collapse" href="#menu-'.$menu->id.'" target="_self">
										<i class="'.$menu->icon.'"></i>
										<p>'.$menu->nmmenu.'
											<b class="caret"></b>
										</p>
									</a>
									<div class="collapse" id="menu-'.$menu->id.'">
										<ul class="nav">';
					
					$sub_menus = DB::select("
						SELECT * FROM t_menu WHERE aktif='1' AND kdlevel LIKE '%+".session('kdlevel')."+%' AND parent_id='".$menu->id."' ORDER BY nourut
					");
					
					//bentuk sub menu
					foreach($sub_menus as $sub_menu){
						
						//apakah tab baru?
						if($sub_menu->new_tab=='1'){
							$html_out .= '<li>
												<a href="'.$sub_menu->url.'" target="_blank">'.$sub_menu->nmmenu.'</a>
											</li>';
						}
						else{
							$html_out .= '<li><a ui-sref="'.$sub_menu->url.'">'.$sub_menu->nmmenu.'</a></li>';
							$angular .= '.state("'.$sub_menu->url.'", {
											url: "/'.$sub_menu->url.'",
											templateUrl: "partials/'.$sub_menu->nmfile.'"
										})';
						}
						
					}
					
					$html_out .= 	'</ul>
								</li>';
				}
				
			}
			
			$angular .=		'.state("profile", {
								url: "/profile",
								templateUrl: "partials/profile.html"
							});
						});';
			
			$kdsatker='';
			if(session('kdlevel')=='05' || session('kdlevel')=='06'){
				$kdsatker=' ('.session('kdsatker').')';
			}
			
			header("x-frame-options:SAMEORIGIN");
			
			return view('app1',
				[
					'menu' => $html_out,
					'angular' => $angular,
					'info_nmkantor' => session('nmsatker'),
					'info_nmlevel' => session('nmlevel'),
					'info_foto' => session('foto'),
					'info_username' => session('username'),
					'info_nama' => session('nama'),
					'info_tahun' => session('tahun'),
					'app_nama' => session('app_nama'),
					'app_versi' => session('app_versi'),
					'app_ket' => session('app_ket')
				]
			);
		}
		catch(\Exception $e){
			return $e;
		}
	}
	
	public function token()
	{
		return csrf_token();
	}
}