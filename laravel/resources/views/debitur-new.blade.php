<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
	<title>Aplikasi Kredit Hunian</title>
	
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	<link href="{{ asset('/template/img/logo.jpeg')}}" rel="icon" type="image/x-icon" />
	<link href="{{ asset('/template/css/bootstrap/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('/template/font-awesome-4.2.0/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('/template/css/ionicons.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('/template/css/datepicker/datepicker3.css') }}" rel="stylesheet" type="text/css" />
	
	<!-- Plugins -->
	<link href="{{ asset('/plugins/datatables/css/dataTables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('/plugins/chosen/chosen.min.css') }}" rel="stylesheet">
	<link href="{{ asset('/plugins/alertify/themes/alertify.core.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('/plugins/alertify/themes/alertify.default.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('/plugins/pekeupload/css/custom.css') }}" rel="stylesheet">
	<link href="{{ asset('/plugins/jquery-timepicker/jquery.timepicker.css') }}" rel="stylesheet" />
	<link href="{{ asset('/plugins/formwizard/css/components.css') }}" rel="stylesheet" />
	<link href="{{ asset('/plugins/formwizard/css/plugins.css') }}" rel="stylesheet" />
	<link href="{{ asset('/plugins/formwizard/css/layout.css') }}" rel="stylesheet" />
	<link href="{{ asset('/plugins/formwizard/css/select2.css') }}" rel="stylesheet" />
	<link href="{{ asset('/plugins/jquery-file-upload/css/jquery.fileupload.css') }}" rel="stylesheet" />
	
	<!-- Theme style -->
	<link href="{{ asset('/template/css/AdminLTE.css') }}" rel="stylesheet" type="text/css" />
	
</head>
<style>
	th{
		text-align:center;
	}
	a.disabled {
		opacity: 0.5
		pointer-events: none
		cursor: default
	}
</style>
<body class="skin-blue">
	
	<!-- header logo: style can be found in header.less -->
	<header class="header">
		<a href="javascript:;" class="logo">
			<!-- Add the class icon to your logo image or logo icon to add the margining -->
			<img src="{{ asset('/template/Bell/img/jakarta.png')}}" width="102" height="36">
		</a>
		<!-- Header Navbar: style can be found in header.less -->
		<nav class="navbar navbar-static-top" role="navigation">
			<div class="navbar-right">
				<ul class="nav navbar-nav">
					<li>
						<?php echo $kembali; ?>
					</li>
				</ul>
			</div>
		</nav>
	</header>
	
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<!-- Small boxes (Stat box) -->
		<div class="row">
			<div class="col-lg-12">
				
				<div class="box">
					<div class="box-header">
						
					</div><!-- /.box-header -->
					<div class="box-body">
										
						<form class="form-horizontal" id="form-ruh" name="form-ruh">
							<input type="hidden" id="inp-id" name="inp-id">
							<input type="hidden" id="inp-rekambaru" name="inp-rekambaru">
							<input type="hidden" id="inp-kdkppn" name="inp-kdkppn">
							<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />	
					
							<div class="page-content-wrapper">
								<!-- BEGIN PAGE CONTENT-->
								<div class="row">
									<div class="col-md-12">
										<div class="portlet box blue" id="form_wizard_1">
											<div class="portlet-title">
												<div class="caption">
													<i class="fa fa-user"></i> Form Registrasi Debitur - <span class="step-title">
													Step 1 of 3 </span>
												</div>
												<div class="tools hidden-xs">
													<a href="javascript:;" class="collapse">
													</a>										
												</div>
											</div>
											<div class="portlet-body form">											
												<div class="form-wizard">
														<div class="form-body">
															<ul class="nav nav-pills nav-justified steps">
																<li>
																	<a href="#tab1" data-toggle="tab" class="step active">
																	<span class="number">
																	1 </span>
																	<span class="desc">
																	<i class="fa fa-check"></i> Hunian </span>
																	</a>
																</li>
																<li>
																	<a href="#tab2" data-toggle="tab" class="step">
																	<span class="number">
																	2 </span>
																	<span class="desc">
																	<i class="fa fa-check"></i> Indentitas </span>
																	</a>
																</li>
																<li>
																	<a href="#tab3" data-toggle="tab" class="step">
																	<span class="number">
																	3 </span>
																	<span class="desc">
																	<i class="fa fa-check"></i> Konfirmasi </span>
																	</a>
																</li>
															</ul>
															<div id="bar" class="progress progress-striped" role="progressbar">
																<div class="progress-bar progress-bar-success">
																</div>
															</div>
															<div class="tab-content">
																<div class="alert alert-danger display-none">
																	<button class="close" data-dismiss="alert"></button>
																	Masih terdapat kesalahan dalam pengisian form. Silahkan cek kolom dibawah.
																</div>
																<div class="alert alert-success display-none">
																	<button class="close" data-dismiss="alert"></button>
																	Validasi form berhasil! Silahkan dilanjutkan.
																</div>
																<div class="tab-pane active" id="tab1">
																	<div class="tab-content">
																		<div class="alert alert-warning">
																			Peringatan! Waktu registrasi dibatasi 30 menit. Apabila waktu pengisian sudah melebihi 30 menit, silahkan refresh halaman registrasi.
																		</div>
																	</div>
																	<br>
																	<div class="form-group">
																		<label class="control-label col-md-3">Tipe Program </label>
																		<div class="col-md-6">
																			<select id="kdjenkredit" name="kdjenkredit" class="form-control"></select>
																		</div>
																	</div>
																	<div class="form-group">
																		<label class="control-label col-md-3">Pilih Hunian Sekarang </label>
																		<div class="col-md-6">
																			<select id="is_huni" name="is_huni" class="form-control">
																				<option value="1">Ya</option>
																				<option value="0">Tidak</option>
																			</select>
																		</div>
																	</div>
																	<div class="form-group" id="div-hunian">
																		<label class="control-label col-md-3">Hunian </label>
																		<div class="col-md-6">
																			<select id="id_hunian" name="id_hunian" class="form-control"></select>
																		</div>
																	</div>
																	<div class="form-group">
																		<label class="control-label col-md-3">Tipe Kredit </label>
																		<div class="col-md-6">
																			<select id="kdtipe" name="kdtipe" class="form-control"></select>
																		</div>
																	</div>
																	<div class="form-group">
																		<label class="control-label col-md-3">Nomor Form </label>
																		<div class="col-md-6">
																			<select id="id_form" name="id_form" class="form-control"></select>
																		</div>
																	</div>
																	<div class="form-group">
																		<label class="control-label col-md-3">ID. Rumah </label>
																		<div class="col-md-6">
																			<input type="text" id="id_hunian_dtl" name="id_hunian_dtl" class="form-control" readonly />
																		</div>
																	</div>
																	<div class="form-group">
																		<label class="control-label col-md-3">Harga Rumah </label>
																		<div class="col-md-6" id="div-harga" style="overflow-x:scroll;">
																			Silahkan pilih hunian terlebih dahulu
																		</div>
																	</div>
																	<div class="form-group">
																		<label class="control-label col-md-3">Tenor </label>
																		<div class="col-md-6">
																			<input type="text" id="tenor" name="tenor" class="form-control" readonly />
																		</div>
																	</div>
																	<div class="form-group">
																		<label class="control-label col-md-3">Tenor/Cicilan </label>
																		<div class="col-md-6" id="div-harga-dtl" style="overflow-x:scroll;">
																			Silahkan pilih harga rumah terlebih dahulu
																		</div>
																	</div>
																</div>
																<div class="tab-pane" id="tab2">
																	<div class="form-group">
																		<label class="control-label col-md-3">NIK (e-KTP)</label>
																		<div class="col-md-6">
																			<input type="text" id="nik" name="nik" class="form-control val_num" placeholder="Masukkan NIK sesuai e-KTP" maxlength="16" autocomplete="off"/>
																		</div>
																	</div>
																	<div class="form-group">
																		<label class="control-label col-md-3">Nama</label>
																		<div class="col-md-6">
																			<input type="text" id="nama" name="nama" class="form-control" placeholder="Masukkan Nama Lengkap sesuai e-KTP" maxlength="50" autocomplete="off"/>
																		</div>
																	</div>
																	<div class="form-group">
																		<label class="control-label col-md-3">NPWP</label>
																		<div class="col-md-6">
																			<input type="text" id="npwp" name="npwp" class="form-control val_num" placeholder="Masukkan NPWP" maxlength="15" autocomplete="off"/>
																		</div>
																	</div>
																</div>
																<div class="tab-pane" id="tab3">
																	<div class="form-group">
																		<label class="control-label col-md-3"></label>
																		<div class="col-md-6 terms">
																			<h3 style="font-weight:700; text-align:center;">SURAT PERNYATAAN</h3>
																			<p style="margin-top:5%; font-weight:700;">Dengan ini saya menyatakan bahwa:</p>
																			<div class="col-md-1" style="text-align:right;font-weight:400;">1</div>
																			<div class="col-md-11">
																				<p style="font-weight:400;">SAYA MENYATAKAN BAHWA INFORMASI YANG SAYA BERIKAN DIATAS ADALAH BENAR DAN DAPAT DIPERTANGGUNGJAWABKAN SECARA HUKUM.</p>
																			</div>
																			<div class="col-md-1" style="text-align:right;font-weight:400;">2</div>
																			<div class="col-md-11">
																				<p style="font-weight:400;">Saya sepenuhnya menyadari bahwa pengajuan kredit hunian ini memeiliki beberapa tahapan administratif dan teknis. Saya menyerahkan sepenuhnya proses verifikasi pengajuan kredit ini ke Pemerintah DKI Jakarta, dan saya akan menerima segala keputusan dengan penuh kesadaran hukum.</p>
																			</div>
																			<div class="col-md-1" style="text-align:right;font-weight:400;">3</div>
																			<div class="col-md-11">
																				<p style="font-weight:400;">Dengan mengisi nama lengkap saya dibawah ini, berarti saya menyatakan akan patuh dan tunduk terhadap syarat dan ketentuan yang berlaku.</p>
																			</div>
																		</div>
																	</div>
																	<div class="form-group">
																		<label class="control-label col-md-3"></label>
																		<div class="col-md-6">
																			
																			<div class="checkbox-list">
																				<b>Tgl. Pengajuan</b> <input type="text" id="tgpemohon" name="tgpemohon" value="<?php echo date('d-m-Y'); ?>" readonly>
																			</div>
																			<div class="checkbox-list" id="namakonf">
																				
																			</div>
																			<br>
																			<div class="checkbox-list">
																				<b>Bersama dengan ini, saya menyatakan bahwa semua informasi yang telah saya berikan sebelumnya adalah benar</b><br>
																				<input type="checkbox" name="agree[]" value="1" data-title="Agree" autocomplete="off"> Saya setuju dengan <u>syarat dan ketentuan</u> sebagaimana tersebut diatas.
																			</div>
																			<div id="form_agree_error">
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>
														<div class="form-actions">
															<div class="row">
																<div class="col-md-offset-3 col-md-9">
																	<a href="javascript:;" class="btn default button-previous">
																	<i class="m-icon-swapleft"></i> Kembali </a>
																	<a href="javascript:;" class="btn blue button-next">
																	Lanjutkan <i class="m-icon-swapright m-icon-white"></i>
																	</a>
																	<button type="button" class="btn green button-submit">
																	Kirim <i class="m-icon-swapright m-icon-white"></i>
																	</button>
																</div>
															</div>
														</div>
												</div>											
											</div>
										</div>
									</div>
								</div>
								<!-- END PAGE CONTENT-->
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	
<div class="modal fade" tabindex="-1" role="dialog" id="modal-registrasi">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Notifikasi</h4>
      </div>
      <div class="modal-body">
        
		<div class="alert alert-info">
			Proses registrasi selesai, silahkan lihat email Anda untuk memastikan notifikasi terkirim. Jika notifikasi belum terkirim atau file disclaimer belum terdownload secara otomatis, silahkan kirim atau download kembali melalui tombol di bawah.
		</div>
		<br>
		<div id="div-selesai">
		
		</div>
		
      </div>
      <div class="modal-footer">
        <button type="button" id="selesai" class="btn btn-default" data-dismiss="modal">Selesai</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
	
	<!-- END CONTENT -->

<!-- END CONTAINER -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="../assets/global/plugins/respond.min.js"></script>
<script src="../assets/global/plugins/excanvas.min.js"></script> 
<![endif]-->
	<script src="{{ asset('/template/js/jquery.min.js') }}"></script>
	<script src="{{ asset('/template/js/jquery-ui.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('/template/js/bootstrap.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('/plugins/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
	<script type="text/javascript" src="{{ asset('/plugins/chosen/chosen.jquery.min.js') }}"></script>
	<script src="{{ asset('/plugins/alertify/lib/alertify.min.js') }}" type="text/javascript"></script>
	<script type="text/javascript" src="{{ asset('/plugins/jquery-file-upload/js/jquery.fileupload.js') }}"></script>
	<script src="{{ asset('/plugins/formwizard/js/jquery.validate.js') }}" type="text/javascript"></script>
	<script src="{{ asset('/plugins/formwizard/js/jquery.bootstrap.wizard.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('/plugins/formwizard/js/select2.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('/plugins/formwizard/js/metronic.js') }}" type="text/javascript"></script>
	<script src="{{ asset('/plugins/formwizard/js/form-wizard-baru.js') }}" type="text/javascript"></script>
	<script src="{{ asset('/plugins/formwizard/js/additional-methods.js') }}" type="text/javascript"></script>
	<script src="{{ asset('/plugins/jQueryMaskPlugin/src/jquery.mask.js') }}" type="text/javascript"></script>
	<script src="{{ asset('/template/js/AdminLTE/app.js') }}" type="text/javascript"></script>
	
	<script>
		
		(function (window) { // Prevent Cross-Frame Scripting attacks
			if (window.location !== window.top.location)
				window.top.location = window.location;
		})(this);

		jQuery(document).ready(function() {
		   
		   // initiate layout and plugins
		   Metronic.init(); // init metronic core components
		   FormWizard.init();
		   
			jQuery('#nama').keyup(function(){
				var nilai = jQuery(this).val();
				jQuery('#namakonf').html(nilai);
			});
		   
			jQuery("body").off("keypress",'.val_char').on("keypress",'.val_char',function (e) {
				var charcode = e.which;
				if (
					(charcode === 8) || // Backspace
					(charcode === 13) || // Enter
					(charcode === 127) || // Delete
					(charcode === 32) || // Space
					(charcode === 0) || // arrow
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
			
			//untuk membatasi past date
			var nowDate = new Date();
			var today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), 0, 0, 0, 0);
			
			jQuery('.uang').mask('000,000,000,000,000', {reverse: true});
					
			jQuery('#tglhr,#tglhr_p,#tgkerja,#tgkerja_p,#tgpemohon').datepicker({
				endDate: today,
				format: 'dd-mm-yyyy',
				autoclose:true
			});
			
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
			
			var token1 = jQuery.getValues('../token');
			
			<?php echo $jquery_upload; ?>
			
			//jQuery('.val_num').val(0);
			
			jQuery('.chosen').chosen();	
			
			function numberWithCommas(x) {
				return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			}
			
			jQuery('#pengeluaran,#angsuran').keyup(function(){
				var totpengeluaran = 0;
				var totangsuran = 0;
				if(jQuery('#pengeluaran').val()!==''){
					totpengeluaran = parseInt(jQuery('#pengeluaran').val().replace(/\,/g,''));
				}
				if(jQuery('#angsuran').val()!==''){
					totangsuran = parseInt(jQuery('#angsuran').val().replace(/\,/g,''));
				}
				var total = totpengeluaran+totangsuran;
				var total = numberWithCommas(total);
				jQuery('#totpengeluaran').val(total);
			});
			
			jQuery('#penghasilan').keyup(function(){
				var nilai = jQuery(this).val();
				jQuery('#totpenghasilan').val(nilai);
			});
			
			jQuery('#penghasilan_p').keyup(function(){
				var nilai = jQuery(this).val();
				jQuery('#totpenghasilan_p').val(nilai);
			});
			
			jQuery.get('../dropdown/jenis-kredit', function(result){
				if(result){
					jQuery('#kdjenkredit').html(result).trigger('chosen:updated');
				}
			});
			
			jQuery.get('../dropdown/hunian', function(result){
				if(result){
					jQuery('#id_hunian').html(result).trigger('chosen:updated');
				}
			});
			
			jQuery('#id_hunian').change(function(){
				var id = jQuery(this).val();
				if(id==''){
					id='xxx';
				}
				jQuery('#div-harga').html('Sedang proses.....');
				jQuery.get('../dropdown/harga/'+id, function(result){
					jQuery('#div-harga').html(result);
				});
			});
			
			jQuery('#is_huni').on('change', function(){
				var is_huni = jQuery(this).val();
				if(is_huni=='0'){
					jQuery('#div-hunian').hide();
					jQuery('#div-harga').html('Silahkan pilih hunian terlebih dahulu');
				}
				else{
					jQuery('#id_hunian').select2("val", '');
					jQuery('#div-hunian').show();
				}
			});
			
			jQuery('#is_alamat_ktp').change(function(){
				var is_alamat_ktp = jQuery(this).val();
				if(is_alamat_ktp=='1'){
					jQuery('.alamat_sama').prop('disabled', true);
				}
				else{
					jQuery('.alamat_sama').prop('disabled', false);
				}
			});
			
			jQuery('#is_alamat_pemohon').change(function(){
				var is_alamat_pemohon = jQuery(this).val();
				if(is_alamat_pemohon=='1'){
					jQuery('.alamat_sama1').prop('disabled', true);
				}
				else{
					jQuery('.alamat_sama1').prop('disabled', false);
				}
			});
			
			jQuery('body').off('click', '.pilih_hunian').on('click', '.pilih_hunian', function(){
				var id = this.id;
				jQuery('#id_hunian_dtl').val(id);
				jQuery('#div-harga-dtl').html('Sedang proses.....');
				jQuery.get('../dropdown/harga-dtl/'+id, function(result){
					jQuery('#div-harga-dtl').html(result);
				});
			});
			
			jQuery('body').off('click', '.pilih_tenor').on('click', '.pilih_tenor', function(){
				var id = this.id;
				jQuery('#tenor').val(id);
			});
			
			jQuery.get('../dropdown/tipe-kredit', function(result){
				if(result){
					jQuery('#kdtipe').html(result).trigger('chosen:updated');
				}
			});
			
			jQuery.get('../dropdown/form-kredit', function(result){
				if(result){
					jQuery('#id_form').html(result).trigger('chosen:updated');
				}
			});

			jQuery.get('../dropdown/jenis-kelamin', function(result){
				if(result){
					jQuery('#kdkelamin,#kdkelamin_p').html(result).trigger('chosen:updated');
				}
			});
			
			jQuery.get('../dropdown/agama', function(result){
				if(result){
					jQuery('#kdagama,#kdagama_p').html(result).trigger('chosen:updated');
				}
			});
			
			jQuery.get('../dropdown/pendidikan', function(result){
				if(result){
					jQuery('#kdpendidikan,#kdpendidikan_p').html(result).trigger('chosen:updated');
				}
			});
			
			jQuery.get('../dropdown/kawin', function(result){
				if(result){
					jQuery('#kdkawin,#kdkawin_p').html(result).trigger('chosen:updated');
				}
			});
			
			jQuery.get('../dropdown/bpjs', function(result){
				if(result){
					jQuery('#kdbpjs').html(result).trigger('chosen:updated');
				}
			});
			
			jQuery.get('../dropdown/prop', function(result){
				if(result){
					jQuery('#kdprop,#kdprop1,#kdprop_p').html(result).trigger('chosen:updated');
				}
			});
			
			jQuery('#kdprop').change(function(){
				jQuery.get('../dropdown/kabkota/'+jQuery(this).val(), function(result){
					if(result){
						jQuery('#kdkabkota').html(result).trigger('chosen:updated');
						jQuery('#kdkabkota,#kdkec,#kdkel').select2('val', '');
					}
				});
			});
			
			jQuery('#kdprop1').change(function(){
				jQuery.get('../dropdown/kabkota/'+jQuery(this).val(), function(result){
					if(result){
						jQuery('#kdkabkota1').html(result).trigger('chosen:updated');
						jQuery('#kdkabkota1,#kdkec1,#kdkel1').select2('val', '');
					}
				});
			});
			
			jQuery('#kdprop_p').change(function(){
				jQuery.get('../dropdown/kabkota/'+jQuery(this).val(), function(result){
					if(result){
						jQuery('#kdkabkota_p').html(result).trigger('chosen:updated');
						jQuery('#kdkabkota_p,#kdkec_p,#kdkel_p').select2('val', '');
					}
				});
			});
			
			jQuery.get('../dropdown/kabkota', function(result){
				if(result){
					jQuery('#kdkabkota,#kdkabkota1,#kdkabkota_p').html(result).trigger('chosen:updated');
				}
			});
			
			jQuery('#kdkabkota').change(function(){
				jQuery.get('../dropdown/kecamatan/'+jQuery('#kdprop').val()+'/'+jQuery(this).val(), function(result){
					if(result){
						jQuery('#kdkec').html(result).trigger('chosen:updated');
						jQuery('#kdkec,#kdkel').select2('val', '');
					}
				});
			});
			
			jQuery('#kdkabkota1').change(function(){
				jQuery.get('../dropdown/kecamatan/'+jQuery('#kdprop1').val()+'/'+jQuery(this).val(), function(result){
					if(result){
						jQuery('#kdkec1').html(result).trigger('chosen:updated');
						jQuery('#kdkec1,#kdkel1').select2('val', '');
					}
				});
			});
			
			jQuery('#kdkabkota_p').change(function(){
				jQuery.get('../dropdown/kecamatan/'+jQuery('#kdprop_p').val()+'/'+jQuery(this).val(), function(result){
					if(result){
						jQuery('#kdkec_p').html(result).trigger('chosen:updated');
						jQuery('#kdkec_p,#kdkel_p').select2('val', '');
					}
				});
			});
			
			jQuery.get('../dropdown/kecamatan', function(result){
				if(result){
					jQuery('#kdkec,#kdkec1,#kdkec_p').html(result).trigger('chosen:updated');
				}
			});
			
			jQuery('#kdkec').change(function(){
				jQuery.get('../dropdown/kelurahan/'+jQuery('#kdprop').val()+'/'+jQuery('#kdkabkota').val()+'/'+jQuery(this).val(), function(result){
					if(result){
						jQuery('#kdkel').html(result).trigger('chosen:updated');
						jQuery('#kdkel').select2('val', '');
					}
				});
			});
			
			jQuery('#kdkec1').change(function(){
				jQuery.get('../dropdown/kelurahan/'+jQuery('#kdprop1').val()+'/'+jQuery('#kdkabkota1').val()+'/'+jQuery(this).val(), function(result){
					if(result){
						jQuery('#kdkel1').html(result).trigger('chosen:updated');
						jQuery('#kdkel1').select2('val', '');
					}
				});
			});
			
			jQuery('#kdkec_p').change(function(){
				jQuery.get('../dropdown/kelurahan/'+jQuery('#kdprop_p').val()+'/'+jQuery('#kdkabkota_p').val()+'/'+jQuery(this).val(), function(result){
					if(result){
						jQuery('#kdkel_p').html(result).trigger('chosen:updated');
						jQuery('#kdkel_p').select2('val', '');
					}
				});
			});
			
			jQuery.get('../dropdown/kelurahan', function(result){
				if(result){
					jQuery('#kdkel,#kdkel1,#kdkel_p').html(result).trigger('chosen:updated');
				}
			});
			
			jQuery.get('../dropdown/pekerjaan', function(result){
				if(result){
					jQuery('#kdpekerjaan,#kdpekerjaan_p').html(result).trigger('chosen:updated');
				}
			});
			
			jQuery.get('../dropdown/hutang', function(result){
				if(result){
					jQuery('#kdhutang').html(result).trigger('chosen:updated');
				}
			});
			
			jQuery.get('../dropdown/kdkreditur', function(result){
				if(result){
					jQuery('#kdkreditur').html(result).trigger('chosen:updated');
				}
			});
			
			/*jQuery.get('../upload', function(result){
				if(result){
					jQuery('#tab1').html(result);
				}
			});*/
			
			jQuery('#sama').click(function(){
				jQuery('#kodepos1').val(jQuery('#kodepos').val());
				jQuery('#telp1').val(jQuery('#telp').val());
				jQuery('#alamat1').val(jQuery('#alamat').val());
			});
			
			var jQueryloading = jQuery('#loadingDiv').hide();
			  
			var jQueryloading2 = jQuery('#loadingDivNip').hide();
			  
			var jQueryloading3 = jQuery('#loadingDivKtp').hide();
			
			jQuery("#modal-registrasi").on('hidden.bs.modal', function () {
				window.location = 'login';
			});
			
		});
	</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>