<html xmlns:v="urn:schemas-microsoft-com:vml"
xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:w="urn:schemas-microsoft-com:office:word"
xmlns:m="http://schemas.microsoft.com/office/2004/12/omml"
xmlns="http://www.w3.org/TR/REC-html40">
<head>
<link href="{{ asset('/template/img/logo.jpeg')}}" rel="icon" type="image/x-icon" />
<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
<meta name=ProgId content=Word.Document>
<meta name=Generator content="Microsoft Word 14">
<meta name=Originator content="Microsoft Word 14">
<link rel=File-List href="Doc1_files/filelist.xml">
<link rel=Edit-Time-Data href="Doc1_files/editdata.mso">
<link rel=dataStoreItem href="Doc1_files/item0012.xml"
target="Doc1_files/props013.xml">
<link rel=themeData href="Doc1_files/themedata.thmx">
<link rel=colorSchemeMapping href="Doc1_files/colorschememapping.xml">
<style>
	#tanda-terima td {
		padding: 10px;
	}
.style1 {
	font-size: 24px;
	font-weight: bold;
}
.style8 {color: #0000FF; font-weight: bold; font-size: 24px; }
.style10 {font-size: 14px}
.style11 {
	font-size: 18px;
	font-weight: bold;
}
</style>
</head>

<body lang=IN style='tab-interval:36.0pt'>

<div class=WordSection1>

<p align="center" class=MsoNormal>
<![if !vml]>

<div align="center">
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <table width="679" border="1" cellpadding="0" cellspacing="0
 ">
    <tr>
      <td width="120"><div align="center"><span style="mso-ignore:vglayout;position:
relative;z-index:251662336"><img width=82 height=93
  src="../../template/img/logo.png" v:shapes="Picture_x0020_4"></span></div></td>
        <td width="515"><p align="center" class="style1">PEMERINTAH PROVINSI DKI JAKARTA</p>
        <p align="center" class="style1">PROGRAM DP 0 RUPIAH   </p></td>
      </tr>
    <tr>
      <td colspan="2" bgcolor="#999999">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2"><div align="center"><span class="style8">TANDA TERIMA PENDAFTARAN </span></div></td>
    </tr>
    <tr>
      <td colspan="2" bgcolor="#999999"><div align="center"></div></td>
      </tr>
    <tr>
      <td><span class="style10">Nama</span></td>
        <td><span class="style10"><?php echo $nama; ?></span></td>
      </tr>
    <tr>
      <td><span class="style10">NIK</span></td>
      <td><span class="style10"><?php echo $nik; ?></span></td>
    </tr>
    <tr>
      <td><span class="style10">Lokasi Unit </span></td>
      <td><span class="style10"><?php echo $nmhunian; ?></span></td>
    </tr>
    <tr>
      <td><span class="style10"></span></td>
      <td><span class="style10"><?php echo $alamat; ?></span></td>
    </tr>
    <tr>
      <td><span class="style10">Tanggal Pendaftaran </span></td>
      <td><span class="style10"><?php echo $tgpemohon; ?></span></td>
    </tr>
    <tr>
      <td><span class="style10">Kode Pendaftaran </span></td>
      <td><span class="style11"><?php echo $id; ?></span></td>
    </tr>
    <tr>
      <td colspan="2" bgcolor="#999999">&nbsp;</td>
      </tr>
    <tr>
      <td colspan="2"><p>Selamat, anda telah terdaftar sebagai pemohon fasilitas perolehan rumah bagi MBR.<br>
	  Permohonan anda akan kami proses untuk validasi dan analisa kelayakan pembiayaan.<br>
	  Simpan dengan baik kode pendaftaran anda. Gunakan kode pendaftaran untuk melihat status permohonan anda pada http://samawa.jakarta.go.id<br>
	  Terima kasih
	   </p>        </td>
      </tr>
    <tr>
      <td colspan="2" bgcolor="#999999">&nbsp;</td>
    </tr>
  </table>
  <h5 style="color:red;">
  <?php
	if($kdpetugas==''){
		echo 'Registrasi Mandiri';
	}
	else{
		echo 'Registrasi Oleh Petugas '.$nmpetugas.' ('.$kdpetugas.')';
	}
  ?>
  </h5>
  <?php echo DNS2D::getBarcodeSVG($qrcode, "QRCODE" , 4, 4); ?>
  <br>
  <br>
  <button onClick="myFunction()">Print</button>

<script>
function myFunction() {
    window.print();
}
</script>
  </div>
<div align="center"></div>
  <o:p>&nbsp;</o:p></p>

<div align="center" style="margin-bottom: 0cm"><span
style='font-family:Symbol;mso-fareast-font-family:Symbol;mso-bidi-font-family:
Symbol'><span style='mso-list:Ignore'><span style='font:7.0pt "Times New Roman"'>&nbsp;
  </span></span></span>
  <![endif]>
</div>
<p align="center" class=MsoNormal style='margin-right:472.25pt'><o:p>&nbsp;</o:p></p>

<p align="center" class=MsoNormal style='margin-right:472.25pt'><![if !vml]><span style='mso-ignore:vglayout'>



</span><![endif]><o:p>&nbsp;</o:p></p>

<p align="center" class=MsoNormal style='margin-right:472.25pt'><o:p>&nbsp;</o:p></p>

<p align="center" class=MsoNormal style='margin-right:472.25pt'><o:p>&nbsp;</o:p></p>

</div>

</body>

</html>