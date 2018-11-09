<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Aplikasi Kredit Hunian</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta content="" name="keywords">
  <meta content="" name="description">

  <!-- Facebook Opengraph integration: https://developers.facebook.com/docs/sharing/opengraph -->
  <meta property="og:title" content="">
  <meta property="og:image" content="">
  <meta property="og:url" content="">
  <meta property="og:site_name" content="">
  <meta property="og:description" content="">

  <!-- Twitter Cards integration: https://dev.twitter.com/cards/  -->
  <meta name="twitter:card" content="summary">
  <meta name="twitter:site" content="">
  <meta name="twitter:title" content="">
  <meta name="twitter:description" content="">
  <meta name="twitter:image" content="">

  <!-- Favicon -->
  <link href="template/img/logo.jpeg" rel="icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Raleway:400,500,700|Roboto:400,900" rel="stylesheet">

  <!-- Bootstrap CSS File -->
  <link href="template/Bell/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Libraries CSS Files -->
  <link href="template/Bell/lib/font-awesome/css/font-awesome.min.css" rel="stylesheet">

  <!-- Main Stylesheet File -->
  <link href="template/Bell/css/style.css" rel="stylesheet">

  <!-- =======================================================
    Theme Name: Bell
    Theme URL: https://bootstrapmade.com/bell-free-bootstrap-4-template/
    Author: BootstrapMade.com
    Author URL: https://bootstrapmade.com
  ======================================================= -->
</head>

<body>
  <!-- Page Content
    ================================================== -->
  <!-- Hero -->

  <section class="hero">
    <div class="container text-center">
      <div class="row">
        <div class="col-md-12">
          <a class="hero-brand" href="auth" title="Home"><img alt="Bell Logo" src="template/Bell/img/jakarta.png" width="100" height="100"></a>
        </div>
      </div>

      <div class="col-md-12">
        <h1>
            Aplikasi Kredit Hunian
        </h1>

        <p class="tagline">
			Pemerintah Provinsi DKI Jakarta
        </p>
        <a class="btn btn-full" href="#">Khusus Petugas</a>
		<a href="data/aturan/pergub.doc" target="_blank">
			<p class="tagline">
				Download Form Permohonan
			</p>
		</a>
      </div>
    </div>

  </section>
  <!-- /Hero -->

  

  <footer class="site-footer">
    <div class="bottom">
      <div class="container">
        <div class="row">

          <div class="col-lg-6 col-xs-12 text-lg-left text-center">
            <p class="copyright-text">
              © Jakarta.go.id
            </p>
            <div class="credits">
              <!--
                All the links in the footer should remain intact.
                You can delete the links only if you purchased the pro version.
                Licensing information: https://bootstrapmade.com/license/
                Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/buy/?theme=Bell
              -->
             Powered by <a href="#">Tim Relawan IT DP 0 Rupiah</a>
            </div>
          </div>

          <!--<div class="col-lg-6 col-xs-12 text-lg-right text-center">
            <ul class="list-inline">
              <li class="list-inline-item">
                <a href="index.html">Home</a>
              </li>

              <li class="list-inline-item">
                <a href="#about">About Us</a>
              </li>

              <li class="list-inline-item">
                <a href="#features">Features</a>
              </li>

              <li class="list-inline-item">
                <a href="#portfolio">Portfolio</a>
              </li>

              <li class="list-inline-item">
                <a href="#team">Team</a>
              </li>

              <li class="list-inline-item">
                <a href="#contact">Contact</a>
              </li>
            </ul>
          </div>-->

        </div>
      </div>
    </div>
  </footer>
  <a class="scrolltop" href="#"><span class="fa fa-angle-up"></span></a>


  <!-- Required JavaScript Libraries -->
  <script src="template/Bell/lib/jquery/jquery.min.js"></script>
  <script src="template/Bell/lib/jquery/jquery-migrate.min.js"></script>
  <script src="template/Bell/lib/superfish/hoverIntent.js"></script>
  <script src="template/Bell/lib/superfish/superfish.min.js"></script>
  <script src="template/Bell/lib/tether/js/tether.min.js"></script>
  <script src="template/Bell/lib/stellar/stellar.min.js"></script>
  <script src="template/Bell/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="template/Bell/lib/counterup/counterup.min.js"></script>
  <script src="template/Bell/lib/waypoints/waypoints.min.js"></script>
  <script src="template/Bell/lib/easing/easing.js"></script>
  <script src="template/Bell/lib/stickyjs/sticky.js"></script>
  <script src="template/Bell/lib/parallax/parallax.js"></script>
  <script src="template/Bell/lib/lockfixed/lockfixed.min.js"></script>

  <!-- Template Specisifc Custom Javascript File -->
  <script src="template/Bell/js/custom.js"></script>

  <script src="template/Bell/contactform/contactform.js"></script>
  <script>
	jQuery(document).ready(function(){
		
		<?php
			if($cek_status){
				echo 'jQuery("#nik").val("'.$nik.'");
					  jQuery("#noreg").val("'.$noreg.'");
					  jQuery("#div-status").html("'.$cek_message.'");';
					  
				echo "$('html, body').animate({
						scrollTop: $('#status-registrasi').offset().top
					  }, 2000);";
			}
		?>
		
		jQuery('#cek-status').click(function(){
						
			jQuery(this).prop('disabled',true);
			jQuery(this).html('Loading.....');
			var lanjut=true;
			if(jQuery('#nik').val()==''){
				lanjut=false;
			}
			if(jQuery('#noreg').val()==''){
				lanjut=false;
			}
			if(lanjut==true){
				var data=jQuery('#form-ruh').serialize();
				jQuery.ajax({
					url:'cek-debitur',
					data:data,
					method:'POST',
					success:function(result){
						jQuery('#div-status').html(result.message);
						jQuery('#cek-status').html('Cek Status');
						jQuery('#cek-status').prop('disabled', false);
					},
					error:function(result){
						jQuery('#div-status').html(result.message);
						jQuery('#cek-status').html('Cek Status');
						jQuery('#cek-status').prop('disabled', false);
					}
				});
			}
			else{
				jQuery('#cek-status').html('Cek Status');
				jQuery('#cek-status').prop('disabled', false);
			}
		});
		
	});
  </script>

</body>
</html>