<!DOCTYPE html>
<html ng-app="spa">
<head>
    <meta charset="UTF-8">
	<!--<title><?php echo $app_name.' '.$app_versi; ?></title>-->
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	<link href="{{ asset('/template/img/faviconsim.ico')}}" rel="icon" type="image/x-icon" />
	<link href="{{ asset('/template/css/bootstrap/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('/template/font-awesome-4.2.0/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('/template/css/ionicons.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('/template/css/datepicker/datepicker3.css') }}" rel="stylesheet" type="text/css" />
	
	<!-- Plugins -->
	<link href="{{ asset('/plugins/datatables/css/dataTables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('/plugins/chosen/chosen.min.css') }}" rel="stylesheet" >
	<link href="{{ asset('/plugins/alertify/themes/alertify.core.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('/plugins/alertify/themes/alertify.default.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('/plugins/pekeupload/css/custom.css') }}" rel="stylesheet" >
	<link href="{{ asset('/plugins/jquery-timepicker/jquery.timepicker.css') }}" rel="stylesheet" />
	
	<!-- Theme style -->
	<link href="{{ asset('/template/css/AdminLTE.css') }}" rel="stylesheet" type="text/css" />
	<style>html{visibility:hidden}</style>
	<script>
		if(self==top){
			document.documentElement.style.visibility='visible';
		}else{
			top.location=self.location;
		}
	</script>
	
</head>
<style>
	th{
		text-align:center;
	}
</style>
<body class="skin-blue">
	<!-- header logo: style can be found in header.less -->
	<header class="header">
		<a href="javascript:;" class="logo">
			<!-- Add the class icon to your logo image or logo icon to add the margining -->
			<img src="{{ asset('/template/img/logo.jpeg')}}" width="102" height="36">
		</a>
		<!-- Header Navbar: style can be found in header.less -->
		<nav class="navbar navbar-static-top" role="navigation">
			<!-- Sidebar toggle button-->
			<a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			<div class="navbar-header" style="padding-left:10px;">
                <a id="info-nmsatker" class="navbar-brand" href="#" style="color:#fff;"><?php echo $info_nama; ?></a>
            </div>
			
			<div class="navbar-right">
				<ul class="nav navbar-nav">
					
					<li class="dropdown notifications-menu">
						<a href="javascript:;" id="info-notif-jumlah" class="dropdown-toggle" data-toggle="dropdown">
							<i class="fa fa-envelope"></i><span class="label label-danger">0</span>
						</a>
						<ul class="dropdown-menu" id="info-notif-status"></ul>
					</li>
					
					<!-- User Account: style can be found in dropdown.less -->
					<li>
						<a href="data/manual/manual.pdf" target="_blank">
							<span>Dokumentasi</span>
						</a>
					</li>
					<li>
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
							<span>TA. <?php echo $info_tahun; ?></span>
						</a>
					</li>
					<li class="dropdown user user-menu">
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
							<i class="glyphicon glyphicon-user"></i>
							<span id="info-username"><?php echo $info_username; ?></span>
						</a>
						<ul class="dropdown-menu">
							<!-- User image -->
							<li class="user-header bg-green">
								<!--<img src="data/user/<?php echo $info_foto; ?>" class="img-circle" alt="User Image" />-->
								<p id="info-user">
									<?php echo $info_nama; ?>
									<br>
									<?php echo $info_email; ?>
								</p>
							</li>
							<!-- Menu Body -->
							<li class="user-body">
								<div class="col-xs-7 text-center">
									<a ui-sref="profile">Profile</a>
								</div>
								<!--<div class="col-xs-5">
									<a href="group" onclick="window.open(this.href, 'Group','left=20,top=20,width=500,height=500,toolbar=0,resizable=0'); return false;">Group</a>
								</div>-->
							</li>
							<!-- Menu Footer-->
							<li class="user-footer">
								<div class="pull-right">
									<a href="auth/logout" class="btn btn-danger btn-flat">Logout</a>
								</div>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</nav>
	</header>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<!-- Left side column. contains the logo and sidebar -->
		<aside class="left-side sidebar-offcanvas">
			<!-- sidebar: style can be found in sidebar.less -->
			<section class="sidebar">
				<!-- Sidebar user panel -->
				<div class="user-panel">
					<div class="pull-left image">
						<!--<img src="data/user/<?php echo $info_foto; ?>" class="img-circle" alt="User Image" />-->
					</div>
					<div class="pull-left info">
						<a href="#"><i class="fa fa-circle text-success"></i> <?php echo $info_nmlevel; ?></a>
					</div>
				</div>
				<div class="user-panel">
					<?php echo $info_nmkantor; ?>
				</div>
				
				<ul class="sidebar-menu">
					<?php echo $menu ?>
				</ul>
			</section>
				<!-- /.sidebar -->
		</aside>

			<!-- Right side column. Contains the navbar and content of the page -->
		<aside class="right-side" ui-view>
			
		</aside><!-- /.right-side -->
	</div><!-- ./wrapper -->
	<script src="{{ asset('/template/js/jquery.min.js') }}"></script>
	
	<!-- load angular -->
	<script src="{{ asset('/template/angular/angular.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('/template/angular/angular-ui-router.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('/template/angular/ngStorage.js') }}" type="text/javascript"></script>
	<script src="{{ asset('/template/angular/loading-bar.js') }}" type="text/javascript"></script>
	
	<!-- Primary -->
	<script src="{{ asset('/template/js/bootstrap.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('/template/js/jquery-ui.min.js') }}" type="text/javascript"></script>
   
	<!-- Plugins -->
	<script src="{{ asset('/plugins/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
	<script type="text/javascript" src="{{ asset('/plugins/chosen/chosen.jquery.min.js') }}"></script>
	<script src="{{ asset('/plugins/alertify/lib/alertify.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('/plugins/jQueryMaskPlugin/src/jquery.mask.js') }}" type="text/javascript"></script>
	<script type="text/javascript" src="{{ asset('/plugins/pekeupload/js/pekeUpload.js') }}"></script>
	<script src="{{ asset('/plugins/datatables/js/jquery.dataTables.js') }}" type="text/javascript"></script>
	<script src="{{ asset('/plugins/datatables/js/dataTables.bootstrap.js') }}" type="text/javascript"></script>
	<script type="text/javascript" src="{{ asset('/plugins/jquery-timepicker/jquery.timepicker.js') }}"></script>
	
	<!-- Highchart -->
	<script src="{{ asset('/plugins/highcharts/js/highcharts.js') }}"></script>
	<script src="{{ asset('/plugins/highcharts/js/highcharts-3d.js') }}"></script>
	<script src="{{ asset('/plugins/highcharts/js/modules/exporting.js') }}"></script>

	<!-- AdminLTE App -->
	<script src="{{ asset('/template/js/AdminLTE/app.js') }}" type="text/javascript"></script>
	
	<!-- App Router JS -->
	<script>
		<?php echo $angular; ?>
	</script>
	
	<script>
		jQuery("body").off("keypress",'.val_char').on("keypress",'.val_char',function (e) {
			var charcode = e.which;
			if (
				(charcode === 8) || // Backspace
				(charcode === 13) || // Enter
				(charcode === 127) || // Delete
				(charcode === 32) || // Space
				(charcode === 0) || // arrow
				//(charcode === 188) || // Koma
				//(charcode === 190) || // Titik
				//(charcode === 173) || // _
				//(charcode === 9) || // Horizontal Tab
				//(charcode === 11) || // Vertical Tab
				//(charcode >= 37 && charcode <= 40) || // arrow
				//(charcode >= 48 && charcode <= 57) ||// 0 - 9
				(charcode >= 65 && charcode <= 90) || // a - z
				(charcode >= 97 && charcode <= 122) // A - Z
				){ 
				console.log(charcode)
			}
			else {
				e.preventDefault()
				return
			}
		});	

		jQuery("body").off("keypress",'.val_name').on("keypress",'.val_name',function (e) {
			var charcode = e.which;
			if (
				(charcode === 8) || // Backspace
				(charcode === 13) || // Enter
				(charcode === 127) || // Delete
				(charcode === 32) || // Space
				(charcode === 0) || // arrow
				(charcode == 188) || // Koma
				(charcode == 190) || // Titik
				//(charcode === 173) || // _
				//(charcode === 9) || // Horizontal Tab
				//(charcode === 11) || // Vertical Tab
				//(charcode >= 37 && charcode <= 40) || // arrow
				//(charcode >= 48 && charcode <= 57) ||// 0 - 9
				(charcode >= 65 && charcode <= 90) || // a - z
				(charcode >= 97 && charcode <= 122) // A - Z
				){ 
				console.log(charcode)
			}
			else {
				e.preventDefault()
				return
			}
		});	

		//hanya alpabet
		jQuery("body").off("keypress",'.val_num').on("keypress",'.val_num',function (e) {
			var charcode = e.which;
			if (
				(charcode === 8) || // Backspace
				(charcode === 13) || // Enter
				(charcode === 127) || // Delete
				(charcode === 0) || // arrow
				(charcode >= 48 && charcode <= 57)// 0 - 9
				){ 
				console.log(charcode)
			}
			else {
				e.preventDefault()
				return
			}
		});
		
		var refresh1,refresh2,refresh3;
		
	</script>
	
</body>
</html>