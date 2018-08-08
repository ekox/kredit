<link href="{{ asset('/plugins/formwizard/css/components.css') }}" rel="stylesheet" />
<link href="{{ asset('/plugins/formwizard/css/plugins.css') }}" rel="stylesheet" />
<link href="{{ asset('/plugins/formwizard/css/layout.css') }}" rel="stylesheet" />
<link href="{{ asset('/plugins/formwizard/css/select2.css') }}" rel="stylesheet" />

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
											Step 1 of 6 </span>
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
															<i class="fa fa-check"></i> Pasangan </span>
															</a>
														</li>
														<li>
															<a href="#tab4" data-toggle="tab" class="step">
															<span class="number">
															4 </span>
															<span class="desc">
															<i class="fa fa-check"></i> Pekerjaan </span>
															</a>
														</li>
														<li>
															<a href="#tab5" data-toggle="tab" class="step">
															<span class="number">
															5 </span>
															<span class="desc">
															<i class="fa fa-check"></i> Keuangan </span>
															</a>
														</li>
														<li>
															<a href="#tab6" data-toggle="tab" class="step">
															<span class="number">
															6 </span>
															<span class="desc">
															<i class="fa fa-check"></i> Upload </span>
															</a>
														</li>
														<li>
															<a href="#tab7" data-toggle="tab" class="step">
															<span class="number">
															7 </span>
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
																<label class="control-label col-md-3">Pilih KPPN Anda</label>
																<div class="col-md-6">
																	<select id="kdkppn" name="kdkppn" class="form-control"></select>
																</div>
															</div>																	
														</div>
														<div class="tab-pane" id="tab2">
															<div class="form-group" id="div-satker">
																<label class="control-label col-md-3">Kode Satker</label>
																<div class="col-md-6">
																	<select id="kdsatker" name="kdsatker" class="form-control"></select>
																</div>
															</div>
															<div class="form-group">
																<label class="control-label col-md-3">Nomor DIPA</label>
																<div class="col-md-6">
																	<input type="text" id="nodipa" name="nodipa" class="form-control" placeholder="Masukkan Nomor DIPA" maxlength="255" autocomplete="off"/>
																</div>
															</div>
															<div class="form-group">
																<label class="control-label col-md-3">Tanggal DIPA</label>
																<div class="col-md-4">
																	<input type="text" id="tgdipa" name="tgdipa" class="form-control val_num" placeholder="Masukkan Tanggal DIPA" autocomplete="off"/>
																</div>
															</div>
															<div class="form-group">
																<label class="control-label col-md-3">Alamat Satker</label>
																<div class="col-md-2" id="div-prov">
																	<select id="kdprov3" name="kdprov3" class="form-control"></select>
																</div>
																<div class="col-md-2" id="div-kota">
																	<select id="kdkota3" name="kdkota3" class="form-control"></select>
																</div>
																<div class="col-md-2">
																	<input type="text" id="kdpos3" name="kdpos3" class="form-control val_num" placeholder="Masukkan Kode Pos" maxlength="5" autocomplete="off"/>
																</div>
															</div>
															<div class="form-group">
																<label class="control-label col-md-3">No. Telp. Kantor</label>
																<div class="col-md-6">
																	<input type="text" id="telpkantor" name="telpkantor" class="form-control" maxlength="20" placeholder="Masukkan Nomor Telepon" autocomplete="off"/>
																</div>
															</div>
															<div class="form-group">
																<label class="control-label col-md-3">No. Fax. Kantor</label>
																<div class="col-md-6">
																	<input type="text" id="faxkantor" name="faxkantor" class="form-control" maxlength="20" placeholder="Masukkan Nomor Telepon" autocomplete="off"/>
																</div>
															</div>
															
														</div>
														<div class="tab-pane" id="tab3">
															<div class="form-group">
																<label class="control-label col-md-3">Nama</label>
																<div class="col-md-6">
																	<input type="text" id="nama" name="nama" class="form-control" placeholder="Masukkan Nama Lengkap" maxlength="50" autocomplete="off"/>
																</div>
															</div>
															<div class="form-group">
																<label class="control-label col-md-3">Nomor Kartu Tanda Penduduk (KTP)</label>
																<div class="col-md-6">
																	<input type="text" id="ktp" name="ktp" class="form-control val_num" placeholder="Masukkan No. KTP" maxlength="16" autocomplete="off"/>
																</div>
																<div class="col-md-2">
																	<img src="template/img/ajax-loader.gif" id="loadingDivKtp">
																</div>
															</div>
															<div class="form-group">
																<label class="control-label col-md-3">Tempat Lahir</label>
																<div class="col-md-6">
																	<input type="text" id="tptlahir" name="tptlahir" class="form-control" placeholder="Masukkan Tempat Lahir" maxlength="50" autocomplete="off"/>
																</div>
															</div>
															<div class="form-group">
																<label class="control-label col-md-3">Tanggal Lahir</label>
																<div class="col-md-4">
																	<input type="text" id="tgllahir" name="tgllahir" class="form-control val_num" placeholder="Masukkan Tanggal Lahir" autocomplete="off"/>
																</div>
															</div>
															<div class="form-group">
																<label class="control-label col-md-3">Jenis Kelamin</label>
																<div class="col-md-4">
																	<select id="kdkelamin" name="kdkelamin" class="form-control" autocomplete="off">
																		<option style="display:none;" value="">Pilih Jenis Kelamin</option>
																		<option value="1">Laki-laki</option>
																		<option value="2">Perempuan</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="control-label col-md-3">Jenis Pegawai</label>
																<div class="col-md-4">
																	<select id="kdnip" name="kdnip" class="form-control" autocomplete="off">
																		<option style="display:none;" value="">Pilih Jenis Pegawai</option>
																		<option value="1">PNS</option>
																		<option value="2">TNI/Polri</option>
																		<option value="3">Non PNS/TNI/Polri</option>
																	</select>
																</div>
															</div>
															<div class="form-group" id="div-nip">
																<label class="control-label col-md-3">NIP/NRP</label>
																<div class="col-md-6">
																	<input type="text" id="nip" name="nip" class="form-control val_num" maxlength="18" autocomplete="off"/>
																</div>
																<div class="col-md-2">
																	<img src="template/img/ajax-loader.gif" id="loadingDivNip">
																</div>
															</div>
															<div class="form-group" id="div-nip2">
																<label class="control-label col-md-3">NIP2 (NRP+Jabatan)</label>
																<div class="col-md-6">
																	<input type="text" id="nip2" name="nip2" class="form-control" maxlength="255" autocomplete="off"/>
																</div>
															</div>
															<div class="form-group">
																<label class="control-label col-md-3">Jabatan Struktural</label>
																<div class="col-md-6">
																	<input type="text" id="jabatan" name="jabatan" class="form-control" maxlength="255" placeholder="Masukkan Jabatan Struktural" autocomplete="off"/>
																</div>
															</div>
															<div class="form-group">
																<label class="control-label col-md-3">Email</label>
																<div class="col-md-6">
																	<input type="text" id="email" name="email" class="form-control" maxlength="50" placeholder="jhon.doe@gmail.com" autocomplete="off"/>
																</div>
															</div>
															<div class="form-group">
																<label class="control-label col-md-3">No. Telp. Rumah</label>
																<div class="col-md-6">
																	<input type="text" id="telprmh" name="telprmh" class="form-control val_num" maxlength="20" placeholder="Masukkan Nomor Telepon" autocomplete="off"/>
																</div>
															</div>
															<div class="form-group">
																<label class="control-label col-md-3">No. Telp. Seluler</label>
																<div class="col-md-6">
																	<input type="text" id="telpsel" name="telpsel" class="form-control val_num" maxlength="20" placeholder="Masukkan Nomor Telepon" autocomplete="off"/>
																</div>
															</div>
															<div class="form-group">
																<label class="control-label col-md-3">Alamat tempat tinggal</label>
																<div class="col-md-2" id="div-prov">
																	<select id="kdprov1" name="kdprov1" class="form-control"></select>
																</div>
																<div class="col-md-2" id="div-kota">
																	<select id="kdkota1" name="kdkota1" class="form-control"></select>
																</div>
																<div class="col-md-2">
																	<input type="text" id="kdpos1" name="kdpos1" class="form-control val_num" placeholder="Masukkan Kode Pos" maxlength="5" autocomplete="off"/>
																</div>
															</div>
															<div class="form-group">
																<label class="control-label col-md-3">Alamat sesuai KTP</label>
																<div class="col-md-2" id="div-prov">
																	<select id="kdprov2" name="kdprov2" class="form-control"></select>
																</div>
																<div class="col-md-2" id="div-kota">
																	<select id="kdkota2" name="kdkota2" class="form-control"></select>
																</div>
																<div class="col-md-2">
																	<input type="text" id="kdpos2" name="kdpos2" class="form-control val_num" placeholder="Masukkan Kode Pos" maxlength="5" autocomplete="off"/>
																</div>
															</div>
															<!--<div class="form-group">
																<label class="control-label col-md-3">Nomor Kartu Identitas Pengantar SPM (KIPS)</label>
																<div class="col-md-6">
																	<input type="text" id="kips" name="kips" class="form-control val_num" placeholder="Masukkan No. KIPS" maxlength="16" autocomplete="off"/>
																</div>
															</div>-->
															<div class="form-group">
																<label class="control-label col-md-3">Level User</label>
																<div class="col-md-4">
																	<select id="kdlevel" name="kdlevel" class="form-control" autocomplete="off">
																		<option style="display:none;" value="">Pilih Level</option>
																		<option value="05">Pengantar SPM</option>
																		<option value="06">Bendahara</option>
																		<option value="25">Operator e-Revisi</option>
																	</select>
																</div>
															</div>
														</div>
														<div class="tab-pane" id="tab4">
															<div class="form-group">
																<label class="control-label col-md-3">Username</label>
																<div class="col-md-4">
																	<input type="text" id="username" name="username" class="form-control" maxlength="50" placeholder="Masukkan Username untuk Login" autocomplete="off"/>
																</div>
																<div class="col-md-2">
																	<img src="template/img/ajax-loader.gif" id="loadingDiv">
																</div>
															</div>
															<div class="form-group">
																<label class="control-label col-md-3">Password</label>
																<div class="col-md-4">
																	<input type="password" id="password" name="password" class="form-control" maxlength="50" placeholder="Masukkan Password untuk Login" autocomplete="off"/>
																</div>
															</div>
															<div class="form-group">
																<label class="control-label col-md-3">Password (Konfirmasi)</label>
																<div class="col-md-4">
																	<input type="password" id="password1" name="password1" class="form-control" maxlength="50" placeholder="Konfirmasi Ulang Password untuk Login" autocomplete="off"/>
																</div>
															</div>
														</div>
														<div class="tab-pane" id="tab5">
															<div class="form-group" id="div-upload-ktp">
																<label class="control-label col-md-3">Upload e-KTP (*.png atau *.jpg/ max 1MB)</label>
																<div class="col-md-6" id="div2-upload-ktp">
																	<span class="btn btn-primary fileinput-button">
																		<i class="fa fa-upload"></i>
																		<span>Browse File</span>
																		<input id="fileupload4" type="file" name="file">
																	</span>
																	<!-- The global progress bar -->
																	<div id="files4" class="files"></div>
																	<div id="progress4" class="progress">
																		<div class="progress-bar progress-bar-success"></div>
																	</div>
																</div>
																<div class="col-lg-3" id="nmfile4"></div>
															</div>
															<div class="form-group" id="div-upload-foto">
																<label class="control-label col-md-3">Upload Foto User (*.png atau *.jpg/ max 1MB)</label>
																<div class="col-md-6" id="div2-upload-foto">
																	<span class="btn btn-primary fileinput-button">
																		<i class="fa fa-upload"></i>
																		<span>Browse File</span>
																		<input id="fileupload3" type="file" name="file">
																	</span>
																	<!-- The global progress bar -->
																	<div id="files3" class="files"></div>
																	<div id="progress3" class="progress">
																		<div class="progress-bar progress-bar-success"></div>
																	</div>
																</div>
																<div class="col-lg-3" id="nmfile3"></div>
															</div>
															<div class="form-group" id="div-upload-sk">
																<label class="control-label col-md-3">Upload SK Penunjukan (*.pdf / max 1MB)</label>
																<div class="col-md-6" id="div2-upload-sk">
																	<span class="btn btn-primary fileinput-button">
																		<i class="fa fa-upload"></i>
																		<span>Browse File</span>
																		<input id="fileupload1" type="file" name="file">
																	</span>
																	<!-- The global progress bar -->
																	<div id="files1" class="files"></div>
																	<div id="progress1" class="progress">
																		<div class="progress-bar progress-bar-success"></div>
																	</div>
																</div>
																<div class="col-lg-3" id="nmfile1"></div>
															</div>
															<div class="form-group" id="div-upload-sd">
																<label class="control-label col-md-3">Upload Surat Dispensasi (*.pdf / max 1MB)</label>
																<div class="col-md-6" id="div2-upload-sd">
																	<span class="btn btn-primary fileinput-button">
																		<i class="fa fa-upload"></i>
																		<span>Browse File</span>
																		<input id="fileupload2" type="file" name="file">
																	</span>
																	<!-- The global progress bar -->
																	<div id="files2" class="files"></div>
																	<div id="progress2" class="progress">
																		<div class="progress-bar progress-bar-success"></div>
																	</div>
																</div>
																<div class="col-lg-3" id="nmfile2"></div>
															</div>
														</div>
														<div class="tab-pane" id="tab6">
															<div class="form-group">
																<label class="control-label col-md-3">Syarat dan Ketentuan</label>
																<div class="col-md-6 terms">
																	<h3 style="font-weight:700; text-align:center;">SURAT PERNYATAAN</h3>
																	<p style="margin-top:5%; font-weight:700;">Dengan ini saya menyatakan bahwa:</p>
																	<div class="col-md-1" style="text-align:right;font-weight:400;">1</div>
																	<div class="col-md-11">
																		<p style="font-weight:400;">Semua informasi yang dicantumkan pada formulir ini adalah benar dan sah dan membebaskan Kantor Pelayanan Perbendaharaan Negara dari segala tuntutan pihak ketiga baik perdata maupun pidana, sehubungan dengan kesalahan/ketidakbenaran dalam pemberian informasi.</p>
																	</div>
																	<div class="col-md-1" style="text-align:right;font-weight:400;">2</div>
																	<div class="col-md-11">
																		<p style="font-weight:400;">Saya bertanggungjawab atas risiko apabila dikemudian hari terdapat tuntutan atas transaksi pengeluaran negara atas beban APBN yang berasal dari Arsip Data Komputer/ADK atau <span style="font-style:italic;">softcopy</span> dokumen yang saya sampaikan melalui Aplikasi e-SPM.</p>
																	</div>
																	<div class="col-md-1" style="text-align:right;font-weight:400;">3</div>
																	<div class="col-md-11">
																		<p style="font-weight:400;">Saya bersedia mematuhi ketentuan penggunaan identitas pengguna <span style="font-style:italic;">(username)</span>, kata sandi <span style="font-style:italic;">(password)</span> dan token pada Aplikasi e-SPM dan bertanggungjawab penuh atas penggunaannya sebagaimana diatur dalam Peraturan Direktur Jenderal Perbendaharaan Nomor PER-..../PB/2017 tentang Tata Cara Penyampaian Surat Perintah Membayar, Data Kontrak, Rencana Penarikan Dana Harian, Data Pegawai Pemerintah Non Pegawai Negeri, Data Gaji, Dan Laporan Pertanggungjawaban Bendahara Secara Elektronik.</p>
																	</div>
																	<div class="col-md-1" style="text-align:right;font-weight:400;">4</div>
																	<div class="col-md-11">
																		<p style="font-weight:400;">Saya mengetahui semua risiko yang timbul dan mungkin timbul sehubungan dengan pengiriman data menggunakan Aplikasi e-SPM yang saya lakukan.</p>
																	</div>
																</div>
															</div>
															<div class="form-group">
																<label class="control-label col-md-3"></label>
																<div class="col-md-6">
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
<script src="{{ asset('/plugins/formwizard/js/jquery.validate.js') }}" type="text/javascript"></script>
<script src="{{ asset('/plugins/formwizard/js/jquery.bootstrap.wizard.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/plugins/formwizard/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/plugins/formwizard/js/metronic.js') }}" type="text/javascript"></script>
<script src="{{ asset('/plugins/formwizard/js/form-wizard.js') }}" type="text/javascript"></script>
<script src="{{ asset('/plugins/formwizard/js/additional-methods.js') }}" type="text/javascript"></script>