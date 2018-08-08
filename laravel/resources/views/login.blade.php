<!DOCTYPE html>
<html style="background-color:#ffffff;">
    <head>
        <meta charset="UTF-8">
        <!--<title><?php echo $name_app.' '.$versi_app; ?> - Login</title>-->
		<link href="{{ asset('/template/img/faviconsim.ico')}}" rel="icon" type="image/x-icon" />
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="{{ asset('/template/css/bootstrap/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('/template/font-awesome-4.2.0/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="{{ asset('/plugins/alertify/themes/alertify.core.css') }}">
		<link rel="stylesheet" href="{{ asset('/plugins/alertify/themes/alertify.default.css') }}">
        <!-- Theme style -->
        <link href="{{ asset('/template/css/AdminLTE.css') }}" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
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
		@keyframes fadeIn { 
		  from { opacity: 0; } 
		}

		.loading {
		  animation: fadeIn 1s infinite alternate;
		}
	</style>
    <body style="background-color:#ffffff">

        <div class="form-box" id="login-box">
			<div style="background-color:#ffffff;">
				<!--<img src="{{ asset('/template/img/smamoranu.png') }}" width="100%" height="100%">-->
			</div>
            <form id="form-ruh" name="form-ruh" onsubmit="return false">
				<!--<input type="hidden" name="_token" value="<?php echo $token; ?>" />-->
                <div class="body bg-gray">
                    <div class="form-group">
						<div class="input-group">
							<input type="text" id="username" name="username" class="form-control" placeholder="Username"/>
							<span class="input-group-addon"><i class="fa fa-user"></i></span>
						</div>
                    </div>
                    <div class="form-group">
						<div class="input-group">
							<input type="password" id="password" name="password" class="form-control" placeholder="Password"/>
							<span class="input-group-addon"><i class="fa fa-lock"></i></span>
						</div>
                    </div>
					<div class="form-group">
						<select id="tahun" name="tahun" class="form-control">
							<option value="2017">TA. 2017</option>
							<option value="2018">TA. 2018</option>
						</select>
                    </div>
                </div>
                <div class="footer">                                                               
                    <button id="submit" type="submit" class="btn btn-block bg-blue"><i class="fa fa-arrow-circle-right"></i> Sign In</button>
					<!--<br>
					<center>
						<a href="registrasi">Klik di sini untuk registrasi!</a>
					</center>-->
                </div>
				<br>
				<center style="color:#878787;font-weight : 300;font-size : 10pt">
					Copyright © 2017
				</center>
            </form>
        </div>

        <script src="{{ asset('/template/js/jquery.min.js') }}"></script>
        <script src="{{ asset('/template/js/bootstrap.min.js') }}" type="text/javascript"></script>
		<script src="{{ asset('/plugins/alertify/lib/alertify.min.js') }}" type="text/javascript"></script>
		<script type="text/javascript">
			
			jQuery(document).ready(function(){
			
				function doBounce(element, times, distance, speed) {
					for(i = 0; i < times; i++) {
						element.animate({marginTop: '-='+distance},speed)
							.animate({marginTop: '+='+distance},speed);
					}        
				}
				
				jQuery('#tahun').val('2018').trigger('chosen:updated');
				
				setTimeout(function(){
					jQuery("#username").focus();
				},1000);
				
				//login			
				jQuery('#submit').click(function(){
				
					//bouncing for awhile...
					doBounce(jQuery('#login-box'), 3, '10px', 100);
				
					jQuery(this).prop('disabled',true);
					jQuery(this).html('<span class="loading"><i class="fa fa-refresh"></i> Loading.....</span>');
					var lanjut=true;
					if(jQuery('#username').val()==''){
						lanjut=false;
					}
					if(jQuery('#password').val()==''){
						lanjut=false;
					}
					if(jQuery('#tahun').val()==''){
						lanjut=false;
					}
					if(lanjut==true){
						var url="auth";
						var data=jQuery('#form-ruh').serialize();
						jQuery.ajax({
							url:'auth',
							data:data,
							method:'POST',
							success:function(result){
								if(result.error==false){
									alertify.log('Login berhasil.<br>Selamat datang di SisfoSPM!');
									jQuery('#submit').html('<i class="fa fa-lock"></i> Sign In');
									jQuery('#submit').prop('disabled', false);
									window.location.href='./';
								}
								else{
									alertify.log(result.message);
									jQuery('#submit').html('<i class="fa fa-lock"></i> Sign In');
									jQuery('#submit').prop('disabled', false);
								}
							},
							error:function(result){
								alertify.log('Sesi telah habis. Silahkan refresh halaman browser Anda.');
								jQuery('#submit').html('<i class="fa fa-lock"></i> Sign In');
								jQuery('#submit').prop('disabled', false);
							}
						});
					}
					else{
						alertify.log('Kolom username/password tidak dapat dikosongkan!');
						jQuery('#submit').html('<i class="fa fa-lock"></i> Sign In');
						jQuery('#submit').prop('disabled', false);
					}
				});
			});
		</script>
    </body>
</html>