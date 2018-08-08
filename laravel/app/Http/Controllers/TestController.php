<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use mPDF;

class TestController extends Controller {

	/**
	 * format tanggal Indonesia dengan 
	 */
	public function tglIndo($date) // yyyy-mm-dd
	{
		$tanggal = substr($date,8,2);
		$arr_bulan = [
			'01'=>'Januari', '02'=>'Pebruari', '03'=>'Maret', '04'=>'April',
			'05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus',
			'09'=>'September', '10'=>'Oktober', '11'=>'Nopember', '12'=>'Desember',
		];
		$rom_bulan = [
			'01'=>'I', '02'=>'II', '03'=>'III', '04'=>'IV',
			'05'=>'V', '06'=>'VI', '07'=>'VII', '08'=>'VIII',
			'09'=>'IX', '10'=>'X', '11'=>'XI', '12'=>'II',
		];
		$bln = substr($date,5,2);
		$this->tanggal = $tanggal;
		$this->bulan = $arr_bulan[$bln];
		$this->romawi = $rom_bulan[$bln];
		$this->tahun = substr($date,0,4);
		return $this;
	}
	
	/**
	 * description 
	 */
	function terbilang($nilai) 
	{
		return HTMLController::terbilang($nilai);
	}
	
	/**
	 * description 
	 */
	public function css()
	{
		return HTMLController::css();
	}
	
	/**
	 * description 
	 */
	public function rkoKegiatan($id_rko=2)
	{
		$rows = DB::select("
			select a.kdprogram, a.kdgiat, b.nmgiat, sum(nilai) as nilai
			from d_rko_pagu1 a
			left join (select kdgiat, trim(nmgiat) as nmgiat from t_giat where kddept='023' and kdunit='11') b on a.kdgiat=b.kdgiat
			where id_rko = '".$id_rko."'
			group by a.kdprogram, a.kdgiat, b.nmgiat
		");
		
		return $rows;
	}
	
	/**
	 * description 
	 */
	public function rkoOutput($id_rko=2, $kdgiat)
	{
		$rows = DB::select("
			select a.kdprogram, a.kdgiat, a.kdoutput, b.nmoutput, sum(nilai) as nilai
			from d_rko_pagu1 a
			left join (select * from t_output where kdgiat='".$kdgiat."') b on a.kdoutput=b.kdoutput
			where a.kdprogram = '04' and a.kdgiat = '".$kdgiat."' and id_rko = '".$id_rko."' 
			group by a.kdprogram, a.kdgiat, a.kdoutput, b.nmoutput	
		");
		return $rows;
	}

	/**
	 * description 
	 */
	public function download($param)
	{
			$html_out = HTMLController::css();
			
			$rows_satker = DB::select("
				select a.kdsatker, a.nmsatker
				from t_satker a
				where a.kddept='".Session::get('kddept')."' and a.kdunit='".Session::get('kdunit')."' and a.kdsatker='".Session::get('kdsatker')."'
			");
			
			$rows_program = DB::select("
				select CONCAT('023','.','11','.',a.kdprogram) as kode, a.kdprogram, b.nmprogram, c.kdppk, d.nmppk, c.kdbpp, e.nmbpp, sum(a.nilai) as nilai 
				from d_rko_pagu1 a 
				left join (select kdprogram, nmprogram
					from t_program where kddept='023' and kdunit='11'
				) b on a.kdprogram=b.kdprogram
				left join d_rko c on a.id_rko=c.id
				left join t_ppk d on c.kdppk=d.kdppk
				left join t_bpp e on c.kdbpp=e.kdbpp
				where a.id_rko=2
				group by CONCAT('023','.','11','.',a.kdprogram), a.kdprogram, b.nmprogram, c.kdppk, d.nmppk, c.kdbpp, e.nmbpp
			");
			
			$rows_akun = DB::select("
				select a.id, a.id_rko, a.thang, a.kdprogram, a.kdgiat, a.kdoutput, a.kdsoutput, a.kdkmpnen, a.kdskmpnen, a.kdakun, a.nilai, b.nmakun
				from d_rko_pagu1 a 
				left join (select kdmak, nmmak as nmakun 
						   from t_mak
				) b on a.kdakun=b.kdmak
				where a.id_rko=2
			");
			
			function rows_uraian($id_rko, $kdakun) 
			{
				return DB::select("
					select a.kdakun, a.uraian, a.nilai
					from d_rko_pagu1 a
					where a.id_rko=".$id_rko." and a.kdakun='".$kdakun."'
				");
			}
			
			$html_out.= '<table cellpadding="0" cellspacing="0" class="fz60">';
			$html_out.= '<thead>';
			
			$html_out.= '<tr>';
				$html_out.= '<th class="wd20 tl">NAMA SATKER</th>';
				$html_out.= '<th class="wd2 tc">:</th>';
				$html_out.= '<th class="tl">'.$rows_satker[0]->nmsatker.'</th>';
			$html_out.= '</tr>';
			$html_out.= '<tr>';
				$html_out.= '<th class="tl">KODE SATKER</th>';
				$html_out.= '<th class="tc">:</th>';
				$html_out.= '<th class="tl">'.$rows_satker[0]->kdsatker.'</th>';
			$html_out.= '</tr>';
			$html_out.= '<tr>';
				$html_out.= '<th class="tl">NOMOR DAN TANGGAL DIPA</th>';
				$html_out.= '<th class="tc">:</th>';
				$html_out.= '<th class="tl">SP DIPA- 023.11.1.137608/2017 Tgl. 07 Desember 2016</th>';
			$html_out.= '</tr>';
			$html_out.= '<tr>';
				$html_out.= '<th class="tl">JUMLAH UP-RM</th>';
				$html_out.= '<th class="tc">:</th>';
				$html_out.= '<th class="tl">'.number_format($rows_program[0]->nilai,0,",",".").'</th>';
			$html_out.= '</tr>';
			$html_out.= '<tr>';
				$html_out.= '<th class="tl">UNIT KERJA</th>';
				$html_out.= '<th class="tc">:</th>';
				$html_out.= '<th class="tl">Badan Standar Nasional Pendidikan (BSNP)</th>';
			$html_out.= '</tr>';
			
			$html_out.= '</thead>';
			$html_out.= '</table>';
			
			$html_out.= '<br/>';
			
			$html_out.= '<table cellspacing="0" cellpadding="0" class="fz60 bd">';
			$html_out.= '<thead>';
				$html_out.= '<tr>';
					$html_out .= '<th class="bd pd wd3">NO</th>';
					$html_out .= '<th class="bd pd wd8">KODE</th>';
					$html_out .= '<th class="bd pd wd8">KELOMPOK AKUN</th>';
					$html_out .= '<th class="bd pd">URAIAN</th>';
					$html_out .= '<th class="bd pd wd7">VOLUME</th>';
					$html_out .= '<th class="bd pd wd7">SATUAN BIAYA</th>';
					$html_out .= '<th class="bd pd wd10">JUMLAH YANG DIMINTAKAN</th>';
				$html_out.= '</tr>';
				$html_out.= '<tr>';
					$html_out .= '<th class="fz60 bd">1</th>';
					$html_out .= '<th class="fz60 bd">2</th>';
					$html_out .= '<th class="fz60 bd">3</th>';
					$html_out .= '<th class="fz60 bd">4</th>';
					$html_out .= '<th class="fz60 bd">5</th>';
					$html_out .= '<th class="fz60 bd">6</th>';
					$html_out .= '<th class="fz60 bd">7</th>';
				$html_out.= '</tr>';
			$html_out.= '</thead>';
			$html_out.= '<tbody>';
				$html_out.= '<tr>';
					$html_out.= '<td class="bdlr vt fwb">&nbsp;</td>';
					$html_out.= '<td class="bdlr vt tc fwb">'.$rows_program[0]->kode.'</td>';
					$html_out.= '<td colspan="2" class="bdlr vt tl fwb">'.$rows_program[0]->nmprogram.'</td>';
					$html_out.= '<td class="bdlr vt fwb">&nbsp;</td>';
					$html_out.= '<td class="bdlr vt fwb">&nbsp;</td>';
					$html_out.= '<td class="bdlr vt tr fwb">'.number_format($rows_program[0]->nilai,0,',','.').'</td>';
				$html_out.= '</tr>';
			
			foreach($rows_akun as $ra) {
				$html_out.= '<tr>';
					$html_out.= '<td class="bdlr fwb">&nbsp;</td>';
					$html_out.= '<td class="bdlr fwb">&nbsp;</td>';
					$html_out.= '<td class="bdlr tr fwb">'.$ra->kdakun.'</td>';
					$html_out.= '<td class="bdlr tl fwb">'.$ra->nmakun.'</td>';
					$html_out.= '<td class="bdlr fwb">-</td>';
					$html_out.= '<td class="bdlr fwb">-</td>';
					$html_out.= '<td class="bdlr tr fwb">'.number_format($ra->nilai,0,',','.').'</td>';
				$html_out.= '</tr>';
				
				foreach(rows_uraian($ra->id_rko, $ra->kdakun) as $ru) {
					$html_out.= '<tr>';
						$html_out.= '<td class="bdlr ">&nbsp;</td>';
						$html_out.= '<td class="bdlr ">&nbsp;</td>';
						$html_out.= '<td class="bdlr tr">&nbsp;</td>';
						$html_out.= '<td class="bdlr tl">&nbsp;&nbsp; - &nbsp;'.$ru->uraian.'</td>';
						$html_out.= '<td class="bdlr ">-</td>';
						$html_out.= '<td class="bdlr ">-</td>';
						$html_out.= '<td class="bdlr tr">'.number_format($ru->nilai,0,',','.').'</td>';
					$html_out.= '</tr>';
				}
			}
			
				$html_out.= '<tr>';
					$html_out.= '<td class="pd bd fwb">&nbsp;</td>';
					$html_out.= '<td class="pd bd fwb">&nbsp;</td>';
					$html_out.= '<td colspan="2" class="pd bd fwb">JUMLAH UP</td>';
					$html_out.= '<td class="pd bd fwb">-</td>';
					$html_out.= '<td class="pd bd fwb">-</td>';
					$html_out.= '<td class="pd bd tr fwb">'.number_format($rows_program[0]->nilai,0,',','.').'</td>';
				$html_out.= '</tr>';
			
			$html_out.= '</tbody>';
			$html_out.= '</table>';
			$html_out.= '<table class="fz60">
				<tbody>
					<tr>
						<td class="">&nbsp;</td>
						<td class="">&nbsp;</td>
						<td class="">&nbsp;</td>
					</tr>
					<tr>
						<td class="" colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td class="">Pejabat Pembuat Komitmen</td>
						<td class="">&nbsp;</td>
						<td class="">Bendahara Pengeluaran Pembantu</td>
					</tr>
					<tr>
						<td class="" colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td class="" colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td class="" colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td class="" colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td class="wd30">'.$rows_program[0]->nmppk.'</td>
						<td class="">&nbsp;</td>
						<td class="wd30">'.$rows_program[0]->nmbpp.'</td>
					</tr>
					<tr>
						<td class="wd30">NIP </td>
						<td class="">&nbsp;</td>
						<td class="wd30">NIP </td>
					</tr>
				</tbody>
			</table>';
			
			$mpdf = new mPDF("en", "A4", "15");
			$mpdf->SetTitle('Form RKO');
			
			$mpdf->AddPage('L');
			$mpdf->writeHTML($html_out);
			$mpdf->Output('Form_RKO_UP_'.$id.'.pdf','I');
	}
	
	/**
	 * description 
	 */
	/*public function spby($param)
	{
		$rows =	DB::select("
			SELECT k.thang, nospby, tgspby, urspby, k.kdppk, k.kdbpp, nip_pk1, nip_pk2, nmppk, nmbpp, SUM(nilai) AS nilai
			FROM( SELECT a.id_rko, b.thang, a.kdspj, a.id_peg_non, a.nip, a.kdakun, a.nilai, b.urrko, b.tgrko, b.kdbpp, b.kdalur, b.kdppk, b.nip_pk1, b.nip_pk2, c.nospby, c.tgspby, c.nocek, c.urspby
					FROM d_rko_pagu2 a
					LEFT JOIN d_rko b ON a.id_rko = b.id
					LEFT JOIN d_spby c ON b.nospby = c.nospby
					WHERE b.nospby = '".$param."'
			) k
			LEFT JOIN t_ppk l ON k.kdppk=l.kdppk
			LEFT JOIN t_bpp m ON m.kdbpp=k.kdbpp
			GROUP BY k.thang, nospby, tgspby, k.kdppk, k.kdbpp, nip_pk1, nip_pk2, urspby, nmppk, nmbpp
		");
		
		$rkos = DB::select("
			SELECT a.id, a.tgrko, a.urrko
			FROM d_rko a
			WHERE a.nospby='".$param."'
		");
		
		$html_out = HTMLController::css();
		$html_out.= '<table class="fz60" border="">
			<tbody>
				<tr>
					<td class="wd20 vm tc" rowspan="2"><img src="../template/img/logokemendikbud.png"></td>
					<td class="tc fwb fz110">
						KEMENTERIAN PENDIDIKAN DAN KEBUDAYAAN<br/>
						BADAN PENELITIAN DAN PENGEMBANGAN<br/>
					</td>
				</tr>
				<tr>
					<td class="tc fz80">
						Alamat Kantor : Jl. Jenderal Sudirman, Senayan, Kotak Pos 4104, Jakarta 12041 <br/>
						Telepon : (021) 572-5031,573-3129, 573-7102, 579-00313; Fax: 572-1245, 572-1244, 579-00313 <br/>
						Laman : http://litbang.kemdikbud.go.id
					</td>
				</tr>
			</tbody>
		</table>
		<hr/>';
		
		$html_out.= '<p class="tc fz60">SURAT PERINTAH BAYAR';
		$html_out.= '<br/>';
		$html_out.= 'Tanggal '.$this->tglIndo($rows[0]->tgspby)->tanggal.' '.$this->tglIndo($rows[0]->tgspby)->bulan.' '.$rows[0]->thang.' Nomor '.$rows[0]->nospby.'/KU-SPBy/PPK-BSNP/'.$this->tglIndo($rows[0]->tgspby)->romawi.'/'.$rows[0]->thang.' </p>';
		$html_out.= '<br/>';
		$html_out.= '<p class="tj fz60">Saya yang bertanda tangan dibawah ini selaku Pejabat Pembuat Komitmen memerintahkan Bendahara Pengeluaran agar melakukan pembayaran sejumlah</p>';
		$html_out.= '<p class="fz60">Rp'.number_format($rows[0]->nilai,0,",",".").'</p>';
		$html_out.= '<p class="fz60">*** '.strtoupper($this->terbilang($rows[0]->nilai)).' RUPIAH ***</p>';
		$html_out.= '<hr/>';
		$html_out.= '<table class="fz60">
			<tbody>
				<tr>
					<td colspan="" class="">Kepada</td>
					<td colspan="">: Bendahara Pengeluaran Pembantu BSNP</td>
				</tr>
				<tr>
					<td colspan="" class="">Untuk pembayaran</td>
					<td colspan="">: '.$rows[0]->urspby.'</td>
				</tr>
				<tr>
					<td colspan="2" class="">&nbsp;</td>
				</tr>
			</tbody>
		</table>
		<table class="fz60" border="0">
			<tbody>
				<tr>
					<td colspan="5" class="">Atas dasar :</td>
				</tr>
				<tr>
					<td colspan="" class="wd40">1.&nbsp;Kuitansi/bukti pembayaran</td>
					<td colspan="" class="wd2">:</td>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="" class="wd40">2.&nbsp;Nota/bukti penerimaan barang/jasa</td>
					<td colspan="" class="wd2">:</td>
					<td colspan="3">&nbsp;</td>
				</tr>
				';
				foreach($rkos as $rk) {
					$html_out.= '
						<tr>
							<td colspan="" class="wd40"></td>
							<td colspan="" class="wd2"></td>
							<td colspan="" class="wd2">-</td>
							<td colspan="2">RKO Nomor '.$rk->id.' Tanggal '.$rk->tgrko.'</td>
						</tr>';
				}
				
				$html_out.='
				<!--<tr>
					<td colspan="">&nbsp;</td>
					<td colspan=""></td>
					<td colspan=""></td>
					<td colspan="">-</td>
					<td colspan="">RKO Nomor ... Tanggal ...</td>
				</tr>
				<tr>
					<td colspan="">&nbsp;</td>
					<td colspan=""></td>
					<td colspan=""></td>
					<td colspan="">-</td>
					<td colspan="">RKO Nomor ... Tanggal ...</td>
				</tr>-->
				<tr>
					<td colspan="5" class="">&nbsp;</td>
				</tr>
			</tbody>
		</table>
		<table class="fz60">
			<tbody>
				<tr>
					<td colspan="3" class="">Dibebankan pada :</td>
				</tr>
				<tr>
					<td class="wd20">Kegiatan, Output, MAK</td>
					<td class="wd2">:</td>
					<td class=""></td>
				</tr>
				<tr>
					<td class="wd20">Kode</td>
					<td class="wd2">:</td>
					<td class=""></td>
				</tr>
				<tr>
					<td colspan="3" class="">&nbsp;</td>
				</tr>
			</tbody>
		</table>
		';
		$html_out.= '<hr/>';
		$html_out.= '<table class="fz60">
			<tbody>
				<tr>
					<td class="tl wd40"></td>
					<td class="tl wd30"></td>
					<td class="tl">Jakarta, </td>
				</tr>
				<tr>
					<td class="tl">Setuju/lunas dibayar, tanggal</td>
					<td class="tl">Diterima tanggal</td>
					<td class="tl">a.n. Kuasa Pengguna Anggaran</td>
				</tr>
				<tr>
					<td class="">Bendahara Pengeluaran</td>
					<td class="">Penerima Uang Muka Kerja</td>
					<td class="">Pejabat Pembuat Komitmen BSNP</td>
				</tr>
				<tr>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
				</tr>
				<tr>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
				</tr>
				<tr>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
				</tr>
				<tr>
					<td class="">'.''.'</td>
					<td class="">'.$rows[0]->nmbpp.'</td>
					<td class="">'.$rows[0]->nmppk.'</td>
				</tr>
				<tr>
					<td class="">NIP </td>
					<td class="">NIP </td>
					<td class="">NIP </td>
				</tr>
			</tbody>
		</table>';
		//~ return $html_out;
		
		$mpdf = new mPDF("en", "A4", "15");
		$mpdf->SetTitle('Form SPBy');
		$mpdf->AddPage('P');
		$mpdf->writeHTML($html_out);
		$mpdf->Output('Form_SPBy_'.$id.'.pdf','I');
	}*/
	
	/**
	 * description 
	 */
	public function kuitansi($param)
	{
		$html_out = HTMLController::css();
		//~ $html_out.= '<p class="tc fz60 fwb"></p>';
		$html_out.= '<table border="0" class="fz50">';
		$html_out.= '
			<tbody>
				<tr>
					<td class="tc fwb fz110" colspan="10">UANG MUKA KERJA</td>
				</tr>
				<tr>
					<td class="" colspan="10">&nbsp;</td>
				</tr>
				<tr>
					<td class="" colspan="10">&nbsp;</td>
				</tr>
				<tr>
					<td class=""></td>
					<td class="wd8"></td>
					<td class="wd2"></td>
					<td class="wd3"></td>
					<td class=""></td>
					<td class="fwb"></td>
					<td class="fwb">Tahun Anggaran</td>
					<td class="wd2 wb">:</td>
					<td class="fwb">2017</td>
					<td class="fwb"></td>
				</tr>
				<tr>
					<td class=""></td>
					<td class=""></td>
					<td class=""></td>
					<td class=""></td>
					<td class=""></td>
					<td class="fwb"></td>
					<td class="fwb">No. Bukti/MAK</td>
					<td class="wd2 fwb">:</td>
					<td class="fwb">0227</td>
					<td class="fwb"></td>
				</tr>
				<tr>
					<td class="" colspan="10">&nbsp;</td>
				</tr>
				<tr>
					<td class="" colspan="10">&nbsp;</td>
				</tr>
				<tr>
					<td class="" colspan="2">Sudah terima dari</td>
					<td class="">:</td>
					<td class="" colspan="7">KPA</td>
				</tr>
				<tr>
					<td class="" colspan="2">Jumlah uang</td>
					<td class="">:</td>
					<td class="" colspan="7">Rp 391.000.000</td>
				</tr>
				<tr>
					<td class="" colspan="2">Terbilang</td>
					<td class="">:</td>
					<td class="" colspan="7">*** '.strtoupper($this->terbilang(391000000)).' RUPIAH ***</td>
				</tr>
				<tr>
					<td class="" colspan="2">Untuk pembayaran</td>
					<td class="">:</td>
					<td class="" colspan="7">UMK LSBP BSNP dengan keterangan sbb:</td>
				</tr>
				<tr>
					<td class=""></td>
					<td class=""></td>
					<td class=""></td>
					<td class="bd fwb tc">No</td>
					<td class="bd fwb tc" colspan="3">Keterangan</td>
					<td class="bd fwb tc" colspan="2">No. SPBy</td>
					<td class="bd fwb tc">Jumlah</td>
				</tr>
				<tr>
					<td class=""></td>
					<td class=""></td>
					<td class=""></td>
					<td class="bdlr">1.</td>
					<td class="bdlr" colspan="3">RKO Tanggal ... Nomor ... </td>
					<td class="bdlr" colspan="2">00001</td>
					<td class="bdlr tr">200.000.000</td>
				</tr>
				<tr>
					<td class=""></td>
					<td class=""></td>
					<td class=""></td>
					<td class="bdlr">2.</td>
					<td class="bdlr" colspan="3">RKO Tanggal ... Nomor ... </td>
					<td class="bdlr" colspan="2">00002</td>
					<td class="bdlr tr">100.000.000</td>
				</tr>
				<tr>
					<td class=""></td>
					<td class=""></td>
					<td class=""></td>
					<td class="bdlr bdb">3.</td>
					<td class="bdlr bdb" colspan="3">RKO Tanggal ... Nomor ... </td>
					<td class="bdlr bdb" colspan="2">00003</td>
					<td class="bdlr bdb tr">91.000.000</td>
				</tr>
				<tr>
					<td class="" colspan="10">&nbsp;</td>
				</tr>
				<tr>
					<td class="" colspan="10">&nbsp;</td>
				</tr>
				<tr>
					<td class=""></td>
					<td class=""></td>
					<td class=""></td>
					<td class="bdt bdb" colspan="4">Total UMK yang diterima</td>
					<td class="bdt bdb tr" colspan="3">391.000.000</td>
				</tr>
				<tr>
					<td class=""></td>
					<td class=""></td>
					<td class=""></td>
					<td class="bdt" colspan="4">Pembayaran melalui cek nomor</td>
					<td class="bdt fwb" colspan="3">CFV10587</td>
				</tr>
				<tr>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
				</tr>
				<tr>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
				</tr>
				<tr>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
				</tr>
				<tr>
					<td class="bdlr bdt tc" colspan="6">Mengetahui/Menyetujui</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
				</tr>
				<tr>
					<td class="bdlr bdt tc" colspan="6">paraf dan tanggal</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="tc" colspan="2">Pengambil Uang Muka,</td>
				</tr>
				<tr>
					<td class="bdlr bdt tc" colspan="3">Atasan Langsung</td>
					<td class="bdlr bdt tc" colspan="3">Bendahara Pengeluaran</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="tc" colspan="2">Bendahara Pengeluaran Pembantu</td>
				</tr>
				<tr>
					<td class="bdlr tc" colspan="3">Kasubbag Perbendaharaan</td>
					<td class="bdlr tc" colspan="3">Sekretariat Balitbang</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="tc" colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td class="bdlr tc" colspan="3">&nbsp;</td>
					<td class="bdlr tc" colspan="3">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="tc" colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td class="bdlr tc" colspan="3">&nbsp;</td>
					<td class="bdlr tc" colspan="3">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="tc" colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td class="bdlr tc" colspan="3">&nbsp;</td>
					<td class="bdlr tc" colspan="3">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="tc" colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td class="bdlr tc" colspan="3">Chandra</td>
					<td class="bdlr tc" colspan="3">Kania</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="tc" colspan="2">Dani</td>
				</tr>
				<tr>
					<td class="bdlr bdb tc" colspan="3">NIP 1983042273875323</td>
					<td class="bdlr bdb tc" colspan="3">NIP 1986042373875323</td>
					<td class="">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="tc" colspan="2">NIP 1993021473875323</td>
				</tr>
			</tbody>
		';
		$html_out.= '</table>';
		
		//~ return $html_out;
		
		$mpdf = new mPDF("en", "A4", "15");
		$mpdf->SetTitle('Form SPBy');
		$mpdf->AddPage('P');
		$mpdf->writeHTML($html_out);
		$mpdf->Output('Form_Kuitansi_'.$param.'.pdf','I');
	}
	
	/**
	 * description 
	 */
	public function rkoup($param)
	{
		function satker($param) { 
			return DB::select("
				SELECT    a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdbpp, d.nmbpp, a.kdppk, e.nmppk, c.nmsatker
				FROM      d_rko a 
				LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
				LEFT JOIN t_satker c ON a.kdsatker = c.kdsatker
				LEFT JOIN t_bpp d ON a.kdbpp = d.kdbpp
				LEFT JOIN t_ppk e ON a.kdppk = e.kdppk
				WHERE     a.kdalur = '01' AND a.id = ".$param."
			");
		}
		
		function rprogram($param) {
			return DB::select("
				SELECT   CONCAT('023', '.', '11', '.', k.kdprogram) AS kode, k.kdprogram, l.nmprogram, SUM(nilai) AS nilai
				FROM       (SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.kdlokasi, a.kdkabkota, a.nip_pk1, a.nip_pk2,
								   a.kdbpp, a.kdppk, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, b.uraian, b.nilai
							FROM   d_rko a LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
							WHERE  a.kdalur = '01' AND a.id = ".$param.") k
						 LEFT JOIN t_program l
							  ON l.kddept = '023' AND l.kdunit = '11' AND k.kdprogram = l.kdprogram
				GROUP BY CONCAT('023', '.', '11', '.', k.kdprogram), k.kdprogram, l.nmprogram
			");
		}
		
		function rgiat($param) {
			return DB::select("
				SELECT   CONCAT('023', '.', '11', '.', k.kdprogram, '.', k.kdgiat) AS kode, k.kdprogram, k.kdgiat, l.nmgiat, sum(nilai) AS nilai
				FROM       (SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.kdlokasi, a.kdkabkota, a.nip_pk1, a.nip_pk2,
								   a.kdbpp, a.kdppk, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, b.uraian, b.nilai
							FROM   d_rko a LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
							WHERE  a.kdalur = '01' AND a.id = ".$param.") k
						 LEFT JOIN t_giat l
							  ON k.kdprogram = l.kdprogram AND k.kdgiat = l.kdgiat
				WHERE    k.kddept = '023' AND k.kdunit = '11' AND k.kdprogram = '04'
				GROUP BY CONCAT('023', '.', '11', '.', k.kdprogram, '.', k.kdgiat), k.kdprogram, k.kdgiat, l.nmgiat
			");
		}
		
		function routput($param, $kdgiat)
		{
			return DB::select("
				SELECT   CONCAT('023', '.', '11', '.', k.kdprogram, '.', k.kdgiat, '.', k.kdoutput) AS kode, k.kdprogram, k.kdgiat, k.kdoutput,
						 if(isnull(l.nmoutput), 'Tidak ada di referensi', l.nmoutput) AS nmoutput, sum(nilai) AS nilai
				FROM       (SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.kdlokasi, a.kdkabkota, a.nip_pk1, a.nip_pk2,
								   a.kdbpp, a.kdppk, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, b.uraian, b.nilai
							FROM   d_rko a LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
							WHERE  a.kdalur = '01' AND a.id = ".$param.") k
						 LEFT JOIN t_output l
							  ON k.kdoutput = l.kdoutput AND k.kdgiat = l.kdgiat
				WHERE    k.kddept = '023' AND k.kdunit = '11' AND k.kdprogram = '04' AND k.kdgiat = '".$kdgiat."'
				GROUP BY CONCAT('023', '.', '11', '.', k.kdprogram, '.', k.kdgiat, '.', k.kdoutput), k.kdprogram, k.kdgiat, k.kdoutput, if(isnull(l.nmoutput), 'Tidak ada di referensi', l.nmoutput)
			");
		}
		
		function rkomponen($param, $kdgiat, $kdoutput)
		{
			return DB::select("
				SELECT   k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen,
						 if(isnull(l.urkmpnen), 'Tidak ada di referensi', l.urkmpnen) AS urkmpnen, sum(nilai) AS nilai
				FROM       (SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.kdlokasi, a.kdkabkota, a.nip_pk1, a.nip_pk2,
								   a.kdbpp, a.kdppk, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, b.uraian, b.nilai
							FROM   d_rko a LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
							WHERE  a.kdalur = '01' AND a.id = ".$param.") k
						 LEFT JOIN t_kmpnen l
							  ON k.kdgiat = l.kdgiat AND k.kdoutput=l.kdoutput AND k.kdkmpnen = l.kdkmpnen
				WHERE    k.kddept = '023' AND k.kdunit = '11' AND k.kdprogram = '04' AND k.kdgiat = '".$kdgiat."' AND k.kdoutput = '".$kdoutput."'
				GROUP BY k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, if(isnull(l.urkmpnen), 'Tidak ada di referensi', l.urkmpnen)
			");
		}
		
		function rskomponen($param, $kdgiat, $kdoutput, $kdkmpnen)
		{
			return DB::select("
				SELECT   k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, k.kdskmpnen, if(isnull(l.urskmpnen), 'Tidak ada di referensi', l.urskmpnen) AS urskmpnen,
						 sum(nilai) AS nilai
				FROM       (SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.kdlokasi,
								   a.kdkabkota, a.nip_pk1, a.nip_pk2, a.kdbpp, a.kdppk, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen,
								   b.kdakun, b.uraian, b.nilai
							FROM   d_rko a LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
							WHERE  a.kdalur = '01' AND a.id = ".$param.") k
						 LEFT JOIN t_skmpnen l
							  ON k.kdgiat = l.kdgiat AND k.kdoutput = l.kdoutput AND k.kdkmpnen = l.kdkmpnen AND k.kdskmpnen = l.kdskmpnen
				WHERE    k.kddept = '023' AND k.kdunit = '11' AND k.kdprogram = '04' AND k.kdgiat = '".$kdgiat."' AND k.kdoutput = '".$kdoutput."' AND k.kdkmpnen = '".$kdkmpnen."'
				GROUP BY k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, k.kdskmpnen, if(isnull(l.urskmpnen), 'Tidak ada di referensi', l.urskmpnen)
			");
		}
		
		function rmak($param, $kdgiat, $kdoutput, $kdkmpnen, $kdskmpnen)
		{
			return DB::select("
				SELECT   k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, k.kdskmpnen, k.kdakun,
						 if(isnull(l.nmmak), 'Tidak ada di referensi', l.nmmak) AS nmmak, sum(nilai) AS nilai
				FROM       (SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.kdlokasi, a.kdkabkota, a.nip_pk1, a.nip_pk2,
								   a.kdbpp, a.kdppk, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, b.uraian, b.nilai
							FROM   d_rko a LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
							WHERE  a.kdalur = '01' AND a.id = ".$param.") k
						 LEFT JOIN t_mak l
							  ON k.kdakun = l.kdmak
				WHERE    k.kddept = '023' AND k.kdunit = '11' AND k.kdprogram = '04' AND k.kdgiat = '".$kdgiat."' AND k.kdoutput = '".$kdoutput."' AND k.kdkmpnen = '".$kdkmpnen."' AND k.kdskmpnen = '".$kdskmpnen."'
				GROUP BY k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, k.kdskmpnen, k.kdakun, if(isnull(l.nmmak), 'Tidak ada di referensi', l.nmmak)
			");	
		}
		
		function ruraian($param, $kdgiat, $kdoutput, $kdkmpnen, $kdskmpnen, $kdakun)
		{
			return DB::select("
				SELECT   k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, k.kdskmpnen, k.kdakun, k.uraian,
						 sum(nilai) AS nilai
				FROM       (SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.kdlokasi, a.kdkabkota, a.nip_pk1, a.nip_pk2,
								   a.kdbpp, a.kdppk, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, b.uraian, b.nilai
							FROM   d_rko a LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
							WHERE  a.kdalur = '01' AND a.id = ".$param.") k
				WHERE    k.kddept = '023' AND k.kdunit = '11' AND k.kdprogram = '04' AND k.kdgiat = '".$kdgiat."' AND k.kdoutput = '".$kdoutput."' AND k.kdkmpnen = '".$kdkmpnen."' AND k.kdskmpnen = '".$kdskmpnen."' AND k.kdakun = '".$kdakun."'
				GROUP BY k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, k.kdskmpnen, k.kdakun, k.uraian
			");
		}
		
		$html_out = HTMLController::css();
		$html_out.= '<table border="0" class="fz50">
			<thead>
				<tr>
					<th class="" colspan="3">
						RINCIAN RENCANA PENGGUNAAN UANG PERSEDIAAN (UP)<br/>
						SUMBER DANA : RUPIAH MURNI (RM) TAHUN '.Session::get('tahun').'<br/>
					</th>
				</tr>
				<tr>
					<th class="" colspan="3">&nbsp;</th>
				</tr>
				<tr>
					<th class="vt tl wd30">NAMA SATKER</th>
					<th class="vt tl wd2">:</th>
					<th class="vt tl">Badan Penelitian dan Pengembangan Pendidikan dan Kebudayaan</th>
				</tr>
				<tr>
					<th class="vt tl">KODE SATKER</th>
					<th class="vt tl">:</th>
					<th class="vt tl">'.satker($param)[0]->kdsatker.'</th>
				</tr>
				<tr>
					<th class="vt tl">NOMOR DAN TANGGAL DIPA</th>
					<th class="vt tl">:</th>
					<th class="vt tl">SP DIPA- 023.11.1.137608/2017 Tgl. 07 Desember 2016</th>
				</tr>
				<tr>
					<th class="vt tl">JUMLAH UP-RM</th>
					<th class="vt tl">:</th>
					<th class="vt tl">'.$this->rupiah(rprogram($param)[0]->nilai).'</th>
				</tr>
				<tr>
					<th class="vt tl">UNIT KERJA</th>
					<th class="vt tl">:</th>
					<th class="vt tl">BADAN STANDAR NASIONAL PENDIDIKAN (BSNP)</th>
				</tr>
			</thead>
		</table>';
		$html_out.= '<table border="0" cellpadding="2" cellspacing="-1" class="fz40">
			<tbody>
				<tr>
					<td class="wd5">&nbsp;</td>
					<td class="wd25">&nbsp;</td>
					<td class="wd3">&nbsp;</td>
					<td class="wd7">&nbsp;</td>
					<td class="wd25">&nbsp;</td>
					<td class="wd10">&nbsp;</td>
					<td class="wd8">&nbsp;</td>
					<td class="wd8">&nbsp;</td>
					<td class="wd7">&nbsp;</td>
					<td class="wd12">&nbsp;</td>
				</tr>
				<tr>
					<th class="bd">NO.</th>
					<th class="bd">KODE</th>
					<!--<th class="bd" colspan="2">KELOMPOK AKUN</th>-->
					<th class="bd" colspan="5">URAIAN</th>
					<th class="bd">VOLUME</th>
					<th class="bd">SATUAN</th>
					<th class="bd">JUMLAH DIMINTAKAN</th>
				</tr>
				';
		foreach(rprogram($param) as $rp) {
			$html_out.= '
				<tr>
					<td class="bdlr"></td>
					<td class="bdlr vt tl fwb">'.$rp->kode.'</td>
					<td class="bdlr vt fwb" colspan="5">'.$rp->nmprogram.'</td>
					<td class="bdlr">&nbsp;</td>
					<td class="bdlr">&nbsp;</td>
					<td class="bdlr vt tr fwb">'.$this->rupiah($rp->nilai).'</td>
				</tr>
			';
			
			foreach(rgiat($param) as $rg) {
				$html_out.= '
				<tr>
					<td class="bdlr bdt bdb"></td>
					<td class="bdlr bdt bdb vt tl fwb">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$rg->kode.'</td>
					<td class="bdlr bdt bdb vt fwb" colspan="5">'.$rg->nmgiat.'</td>
					<td class="bdlr bdt bdb">&nbsp;</td>
					<td class="bdlr bdt bdb">&nbsp;</td>
					<td class="bdlr bdt bdb vt tr fwb">'.$this->rupiah($rg->nilai).'</td>
				</tr>
				';
				
				foreach(routput($param, $rg->kdgiat) as $ro) {
					$html_out.= '
				<tr>
					<td class="bdlr bdt bdb"></td>
					<td class="bdlr bdt bdb vt tc fwb">'.$ro->kode.'</td>
					<td class="bdlr bdt bdb vt fwb" colspan="5">'.$ro->nmoutput.'</td>
					<td class="bdlr bdt bdb">&nbsp;</td>
					<td class="bdlr bdt bdb">&nbsp;</td>
					<td class="bdlr bdt bdb vt tr fwb">'.$this->rupiah($ro->nilai).'</td>
				</tr>	
					';
					
					foreach(rkomponen($param, $rg->kdgiat, $ro->kdoutput) as $rk) {
						$html_out.= '
				<tr>
					<td class="bdlr bdb"></td>
					<td class="bdlr bdb vt tr fwb">'.$rk->kdkmpnen.'</td>
					<td class="bdlr bdb vt fwb" colspan="5">'.$rk->urkmpnen.'</td>
					<td class="bdlr bdb fwb">&nbsp;</td>
					<td class="bdlr bdb fwb">&nbsp;</td>
					<td class="bdlr bdb vt tr fwb">'.$this->rupiah($rk->nilai).'</td>
				</tr>
						';
						
						foreach(rskomponen($param, $rg->kdgiat, $ro->kdoutput, $rk->kdkmpnen) as $rs) {
							$html_out.= '
				<tr>
					<td class="bdlr bdb"></td>
					<td class="bdlr bdb"></td>
					<td class="bdl bdb vt tr">'.$rs->kdskmpnen.'</td>
					<td class="bdr bdb vt tl" colspan="4">'.$rs->urskmpnen.'</td>
					<td class="bdlr bdb">&nbsp;</td>
					<td class="bdlr bdb">&nbsp;</td>
					<td class="bdlr bdb vt tr">'.$this->rupiah($rs->nilai).'</td>
				</tr>
							';
							
							foreach(rmak($param, $rg->kdgiat, $ro->kdoutput, $rk->kdkmpnen, $rs->kdskmpnen) as $rm) {
								$html_out.= '
				<tr>
					<td class="bdlr"></td>
					<td class="bdlr"></td>
					<td class="bdl vt tr">&nbsp;</td>
					<td class="vt tr">'.$rm->kdakun.'</td>
					<td class="bdr vt" colspan="3">'.$rm->nmmak.'</td>
					<td class="bdlr">&nbsp;</td>
					<td class="bdlr">&nbsp;</td>
					<td class="bdlr vt tr">'.$this->rupiah($rm->nilai).'</td>
				</tr>
								';
								
								foreach(ruraian($param, $rg->kdgiat, $ro->kdoutput, $rk->kdkmpnen, $rs->kdskmpnen, $rm->kdakun) as $ru) {
									$html_out.= '
				<tr>
					<td class="bdlr"></td>
					<td class="bdlr"></td>
					<td class="bdl vt tr">&nbsp;</td>
					<td class="vt tr">&nbsp;</td>
					<td class="bdr vt" colspan="3">-&nbsp;'.$ru->uraian.'</td>
					<td class="bdlr">&nbsp;</td>
					<td class="bdlr">&nbsp;</td>
					<td class="bdlr vt tr">'.$this->rupiah($ru->nilai).'</td>
				</tr>
									';
								}
							}
						}
					}
				}
			}
		}
		$html_out.= '
				<tr>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
				</tr>
				';
		$html_out.= '
			</tbody>
		</table>';
		$html_out.= '<table border="0" cellpadding="2" cellspacing="-1" class="fz50">
			<tbody>
				<tr>
					<td class="wd30">&nbsp;</td>
					<td class="wd40">&nbsp;</td>
					<td class="wd30 tl">Jakarta, </td>
				</tr>
				<tr>
					<td class="wd30" colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td class="wd30">Pejabat Pembuat Komitmen</td>
					<td class="wd40">&nbsp;</td>
					<td class="wd30">Bendahara Pengeluaran Pembantu</td>
				</tr>
				<tr>
					<td class="wd30" colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td class="wd30" colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td class="wd30" colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td class="wd30">'.satker($param)[0]->nmppk.'</td>
					<td class="wd40">&nbsp;</td>
					<td class="wd30">'.satker($param)[0]->nmbpp.'</td>
				</tr>
				<tr>
					<td class="wd30">NIP</td>
					<td class="wd40">&nbsp;</td>
					<td class="wd30">NIP</td>
				</tr>
			</tbody>
		</table>';
		//~ return $html_out;
		$mpdf = new mPDF("en", "A4", "15");
		$mpdf->SetTitle('Form RKO_UP');
		$mpdf->AddPage('P');
		$mpdf->writeHTML($html_out);
		$mpdf->Output('Form_RKOUP_'.$id.'.pdf','I');
	}
	
	/**
	 * description 
	 */
	public function rkols($param)
	{
		function satker($param)
		{
			return DB::select("
				SELECT    a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdbpp, d.nmbpp, a.kdppk, e.nmppk, c.nmsatker
				FROM      d_rko a 
				LEFT JOIN d_rko_pagu2 b ON a.id = b.id_rko
				LEFT JOIN t_satker c ON a.kdsatker = c.kdsatker
				LEFT JOIN t_bpp d ON a.kdbpp = d.kdbpp
				LEFT JOIN t_ppk e ON a.kdppk = e.kdppk
				WHERE     a.kdalur = '02' AND a.id = ".$param."
			");
		}

		function rprogram($param)
		{
			return DB::select("
				SELECT  CONCAT('023.11.', k.kdprogram) AS kode, k.kdprogram, IF(ISNULL(l.nmprogram), 'Tidak ada di referensi', l.nmprogram) AS nmprogram, SUM(nilai) AS nilai
				FROM	(SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.nospby, a.kdbpp, a.kdppk,
						b.kdspj, b.id_peg_non, b.nip, b.kdjab, b.uraian, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, 
						b.nilai
						FROM d_rko a
						LEFT JOIN d_rko_pagu2 b ON a.id=b.id_rko
						WHERE a.kdalur='02' AND a.id=".$param.") k
				LEFT JOIN t_program l ON k.kddept=l.kddept AND k.kdunit=l.kdunit AND k.kdprogram=l.kdprogram
				WHERE k.kddept='023' AND k.kdunit='11'
				GROUP BY CONCAT('023.11.', k.kdprogram), k.kdprogram, IF(ISNULL(l.nmprogram), 'Tidak ada di referensi', l.nmprogram)
			");
		}

		function rgiat($param, $kdprogram)
		{
			return DB::select("
				SELECT  CONCAT('023.11.04.', k.kdgiat) AS kode, k.kdprogram, k.kdgiat, IF(ISNULL(l.nmgiat), 'Tidak ada di referensi', l.nmgiat) AS nmgiat, SUM(nilai) AS nilai
				FROM	(SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.nospby, a.kdbpp, a.kdppk,
						b.kdspj, b.id_peg_non, b.nip, b.kdjab, b.uraian, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, 
						b.nilai
						FROM d_rko a
						LEFT JOIN d_rko_pagu2 b ON a.id=b.id_rko
						WHERE a.kdalur='02' AND a.id=".$param.") k
				LEFT JOIN t_giat l ON k.kddept=l.kddept AND k.kdunit=l.kdunit AND k.kdprogram=l.kdprogram AND k.kdgiat=l.kdgiat
				WHERE k.kddept='023' AND k.kdunit='11' AND k.kdprogram='".$kdprogram."'
				GROUP BY CONCAT('023.11.04.', k.kdgiat), k.kdprogram, k.kdgiat, IF(ISNULL(l.nmgiat), 'Tidak ada di referensi', l.nmgiat)
			");
		}

		function routput($param, $kdprogram, $kdgiat)
		{
			return DB::select("
				SELECT  CONCAT('023.11.04.', k.kdgiat, '.', k.kdoutput) AS kode, k.kdprogram, k.kdgiat, k.kdoutput, IF(ISNULL(l.nmoutput), 'Tidak ada di referensi', l.nmoutput) AS nmoutput, SUM(nilai) AS nilai
				FROM	(SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.nospby, a.kdbpp, a.kdppk,
						b.kdspj, b.id_peg_non, b.nip, b.kdjab, b.uraian, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, 
						b.nilai
						FROM d_rko a
						LEFT JOIN d_rko_pagu2 b ON a.id=b.id_rko
						WHERE a.kdalur='02' AND a.id=".$param.") k
				LEFT JOIN t_output l ON k.kdgiat=l.kdgiat AND k.kdoutput=l.kdoutput
				WHERE k.kddept='023' AND k.kdunit='11' AND k.kdprogram='".$kdprogram."' AND k.kdgiat='".$kdgiat."'
				GROUP BY CONCAT('023.11.04.', k.kdgiat, '.', k.kdoutput), k.kdprogram, k.kdgiat, k.kdoutput, IF(ISNULL(l.nmoutput), 'Tidak ada di referensi', l.nmoutput)			
			");
		}

		function rkomponen($param, $kdprogram, $kdgiat, $kdoutput)
		{
			return DB::select("
				SELECT  k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, IF(ISNULL(l.urkmpnen), 'Tidak ada di referensi', l.urkmpnen) AS urkmpnen, SUM(nilai) AS nilai
				FROM	(SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.nospby, a.kdbpp, a.kdppk,
						b.kdspj, b.id_peg_non, b.nip, b.kdjab, b.uraian, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, 
						b.nilai
						FROM d_rko a
						LEFT JOIN d_rko_pagu2 b ON a.id=b.id_rko
						WHERE a.kdalur='02' AND a.id=".$param.") k
				LEFT JOIN t_kmpnen l ON k.kddept=l.kddept AND k.kdunit=l.kdunit AND k.kdprogram=l.kdprogram AND k.kdgiat=l.kdgiat AND k.kdoutput=l.kdoutput
				WHERE k.kddept='023' AND k.kdunit='11' AND k.kdprogram='".$kdprogram."' AND k.kdgiat='".$kdgiat."' AND k.kdoutput='".$kdoutput."'
				GROUP BY k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, IF(ISNULL(l.urkmpnen), 'Tidak ada di referensi', l.urkmpnen)
			");
		}

		function rskomponen($param, $kdprogram, $kdgiat, $kdoutput, $kdkmpnen)
		{
			return DB::select("
				SELECT  k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, k.kdskmpnen, IF(ISNULL(l.urskmpnen), 'Tidak ada di referensi', l.urskmpnen) AS urskmpnen, SUM(nilai) AS nilai
				FROM	(SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.nospby, a.kdbpp, a.kdppk,
						b.kdspj, b.id_peg_non, b.nip, b.kdjab, b.uraian, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, 
						b.nilai
						FROM d_rko a
						LEFT JOIN d_rko_pagu2 b ON a.id=b.id_rko
						WHERE a.kdalur='02' AND a.id=".$param.") k
				LEFT JOIN t_skmpnen l ON k.kddept=l.kddept AND k.kdunit=l.kdunit AND k.kdsatker=l.kdsatker AND k.kdprogram=l.kdprogram AND k.kdgiat=l.kdgiat AND k.kdoutput=l.kdoutput AND k.kdkmpnen=l.kdkmpnen
				WHERE k.kddept='023' AND k.kdunit='11' AND k.kdprogram='".$kdprogram."' AND k.kdgiat='".$kdgiat."' AND k.kdoutput='".$kdoutput."' AND k.kdkmpnen='".$kdkmpnen."'
				GROUP BY k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, k.kdskmpnen, IF(ISNULL(l.urskmpnen), 'Tidak ada di referensi', l.urskmpnen)
			");
		}

		function rmak($param, $kdprogram, $kdgiat, $kdoutput, $kdkmpnen, $kdskmpnen)
		{
			return DB::select("
				SELECT   k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, k.kdskmpnen, k.kdakun, IF(ISNULL(l.nmmak), 'Tidak ada di referensi', l.nmmak) AS nmmak, SUM(nilai) AS nilai
				FROM	(SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.nospby, a.kdbpp, a.kdppk,
						 b.kdspj, b.id_peg_non, b.nip, b.kdjab, b.uraian, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, 
						 b.nilai
						FROM d_rko a
						LEFT JOIN d_rko_pagu2 b ON a.id=b.id_rko
						WHERE a.kdalur='02' AND a.id=".$param.") k
				LEFT JOIN t_mak l ON k.kdakun=l.kdmak
				WHERE k.kddept='023' AND k.kdunit='11' AND k.kdprogram='".$kdprogram."' AND k.kdgiat='".$kdgiat."' AND k.kdoutput='".$kdoutput."' AND k.kdkmpnen='".$kdkmpnen."' AND k.kdskmpnen='".$kdskmpnen."'
				GROUP BY k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, k.kdskmpnen, k.kdakun, IF(ISNULL(l.nmmak), 'Tidak ada di referensi', l.nmmak)
			");
		}
		
		function rdetail($param, $kdprogram, $kdgiat, $kdoutput, $kdkmpnen, $kdskmpnen, $kdakun)
		{
			return DB::select("
				SELECT   k.kdakun, k.kdjab,
						 l.kdgol AS gol_peg, m.kdgol AS gol_non,
						 k.nip, k.id_peg_non,
						 k.uraian, if(k.uraian = '', if(isnull(k.nip), m.nama, if(isnull(k.id_peg_non), l.nama, NULL)), k.uraian) AS isian,
						 sum(k.nilai) AS nilai
				FROM     (SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.nospby,
								 a.kdbpp, a.kdppk, b.kdspj, b.id_peg_non, b.nip, b.kdjab, b.uraian, b.kdprogram, b.kdgiat, b.kdoutput,
								 b.kdkmpnen, b.kdskmpnen, b.kdakun, b.nilai
						  FROM   d_rko a LEFT JOIN d_rko_pagu2 b ON a.id = b.id_rko
						  WHERE  a.kdalur = '02' AND a.id = ".$param.") k
						 LEFT JOIN t_pegawai l
						   ON k.nip = l.nip
						 LEFT JOIN t_pegawai_non m
						   ON k.id_peg_non = m.id
				WHERE k.kddept='023' AND k.kdunit='11' AND k.kdprogram='".$kdprogram."' AND k.kdgiat='".$kdgiat."' AND k.kdoutput='".$kdoutput."' AND k.kdkmpnen='".$kdkmpnen."' AND k.kdskmpnen='".$kdskmpnen."' AND k.kdakun='".$kdakun."'
				GROUP BY k.kdakun, k.kdjab, l.kdgol, m.kdgol, k.nip, k.id_peg_non, k.uraian, if(k.uraian = '', if(isnull(k.nip), m.nama, if(isnull(k.id_peg_non), l.nama, NULL)), k.uraian)
			");
		}
		
		$html_out = HTMLController::css();
		$html_out.= '<table border="0" cellpadding="2" cellspacing="-1" class="fz50">
			<thead>
				<tr>
					<th class="" colspan="3">
						RINCIAN RENCANA PENGGUNAAN UANG PERSEDIAAN (UP)<br/>
						SUMBER DANA : RUPIAH MURNI (RM) TAHUN '.Session::get('tahun').'<br/>
					</th>
				</tr>
				<tr>
					<th class="" colspan="3">&nbsp;</th>
				</tr>
				<tr>
					<th class="vt tl wd30">NAMA SATKER</th>
					<th class="vt tl wd2">:</th>
					<th class="vt tl">Badan Penelitian dan Pengembangan Pendidikan dan Kebudayaan</th>
				</tr>
				<tr>
					<th class="vt tl">KODE SATKER</th>
					<th class="vt tl">:</th>
					<th class="vt tl">'.satker($param)[0]->kdsatker.'</th>
				</tr>
				<tr>
					<th class="vt tl">NOMOR DAN TANGGAL DIPA</th>
					<th class="vt tl">:</th>
					<th class="vt tl">SP DIPA- 023.11.1.137608/2017 Tgl. 07 Desember 2016</th>
				</tr>
				<tr>
					<th class="vt tl">JUMLAH UP-RM</th>
					<th class="vt tl">:</th>
					<th class="vt tl">'.$this->rupiah(rprogram($param)[0]->nilai).'</th>
				</tr>
				<tr>
					<th class="vt tl">UNIT KERJA</th>
					<th class="vt tl">:</th>
					<th class="vt tl">BADAN STANDAR NASIONAL PENDIDIKAN (BSNP)</th>
				</tr>
			</thead>
		</table>';
		$html_out.= '<table border="0" cellpadding="2" cellspacing="-1" class="fz40">
			<tbody>
				<tr>
					<td class="wd5">&nbsp;</td>
					<td class="wd20">&nbsp;</td>
					<td class="wd3">&nbsp;</td>
					<td class="wd7">&nbsp;</td>
					<td class="wd15">&nbsp;</td>
					<td class="wd10">&nbsp;</td>
					<td class="wd10">&nbsp;</td>
					<td class="wd10">&nbsp;</td>
					<td class="wd10">&nbsp;</td>
					<td class="wd10">&nbsp;</td>
				</tr>
				<tr>
					<th class="bd">NO</th>
					<th class="bd">KODE</th>
					<th class="bd" colspan="4">URAIAN</th>
					<th class="bd">JUMLAH BRUTO</th>
					<th class="bd">PPN</th>
					<th class="bd">PPh</th>
					<th class="bd">JUMLAH NETO</th>
				</tr>
				';
		foreach(rprogram($param) as $rp) {
			$html_out.= '
				<tr>
					<td class="bdlr bdb">&nbsp;</td>
					<td class="bdlr bdb vt fwb">'.$rp->kode.'</td>
					<td class="bdlr bdb vt fwb" colspan="4">'.$rp->nmprogram.'</td>
					<td class="bdlr bdb vt tr fwb">'.$this->rupiah($rp->nilai).'</td>
					<td class="bdlr bdb">&nbsp;</td>
					<td class="bdlr bdb">&nbsp;</td>
					<td class="bdlr bdb vt tr fwb">'.$this->rupiah($rp->nilai).'</td>
				</tr>
			';
			
			foreach(rgiat($param, $rp->kdprogram) as $rg) {
				$html_out.= '
				<tr>
					<td class="bdlr bdb">&nbsp;</td>
					<td class="bdlr bdb vt fwb">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$rg->kode.'</td>
					<td class="bdlr bdb vt fwb" colspan="4">'.$rg->nmgiat.'</td>
					<td class="bdlr bdb vt tr fwb">'.$this->rupiah($rg->nilai).'</td>
					<td class="bdlr bdb">&nbsp;</td>
					<td class="bdlr bdb">&nbsp;</td>
					<td class="bdlr bdb vt tr fwb">'.$this->rupiah($rg->nilai).'</td>
				</tr>
				';
				
				foreach(routput($param, $rp->kdprogram, $rg->kdgiat) as $ro) {
					$html_out.= '
				<tr>
					<td class="bdlr bdb">&nbsp;</td>
					<td class="bdlr bdb vt tc fwb">'.$ro->kode.'</td>
					<td class="bdlr bdb vt fwb" colspan="4">'.$ro->nmoutput.'</td>
					<td class="bdlr bdb vt tr fwb">'.$this->rupiah($ro->nilai).'</td>
					<td class="bdlr bdb">&nbsp;</td>
					<td class="bdlr bdb">&nbsp;</td>
					<td class="bdlr bdb vt tr fwb">'.$this->rupiah($ro->nilai).'</td>
				</tr>
					';
					
					foreach(rkomponen($param, $rp->kdprogram, $rg->kdgiat, $ro->kdoutput) as $rk) {
						$html_out.= '
				<tr>
					<td class="bdlr bdb">&nbsp;</td>
					<td class="bdlr bdb vt tr fwb">'.$rk->kdkmpnen.'</td>
					<td class="bdlr bdb vt fwb" colspan="4">'.$rk->urkmpnen.'</td>
					<td class="bdlr bdb vt tr fwb">'.$this->rupiah($rk->nilai).'</td>
					<td class="bdlr bdb">&nbsp;</td>
					<td class="bdlr bdb">&nbsp;</td>
					<td class="bdlr bdb vt tr fwb">'.$this->rupiah($rk->nilai).'</td>
				</tr>
						';	
						
						foreach(rskomponen($param, $rp->kdprogram, $rg->kdgiat, $ro->kdoutput, $rk->kdkmpnen) as $rs) {
							$html_out.= '
				<tr>
					<td class="bdlr bdb">&nbsp;</td>
					<td class="bdlr bdb">&nbsp;</td>
					<td class="bdl bdb tr">'.$rs->kdskmpnen.'</td>
					<td class="bdr bdb" colspan="3">'.$rs->urskmpnen.'</td>
					<td class="bdlr bdb tr">'.$this->rupiah($rs->nilai).'</td>
					<td class="bdlr bdb">&nbsp;</td>
					<td class="bdlr bdb">&nbsp;</td>
					<td class="bdlr bdb tr">'.$this->rupiah($rs->nilai).'</td>
				</tr>
							';
							
							foreach(rmak($param, $rp->kdprogram, $rg->kdgiat, $ro->kdoutput, $rk->kdkmpnen, $rs->kdskmpnen) as $rm) {
								$html_out.= '
				<tr>
					<td class="bdlr">&nbsp;</td>
					<td class="bdlr">&nbsp;</td>
					<td class="bdl">&nbsp;</td>
					<td class="tr">'.$rm->kdakun.'</td>
					<td class="" colspan="2">'.$rm->nmmak.'</td>
					<td class="bdlr tr">'.$this->rupiah($rm->nilai).'</td>
					<td class="bdlr">&nbsp;</td>
					<td class="bdlr">&nbsp;</td>
					<td class="bdlr tr">'.$this->rupiah($rm->nilai).'</td>
				</tr>
								';
								
								foreach(rdetail($param, $rp->kdprogram, $rg->kdgiat, $ro->kdoutput, $rk->kdkmpnen, $rs->kdskmpnen, $rm->kdakun) as $rd) {
									$html_out.= '
				<tr>
					<td class="bdlr">&nbsp;</td>
					<td class="bdlr">&nbsp;</td>
					<td class="bdl">&nbsp;</td>
					<td class="">&nbsp;</td>
					<td class="tl">-&nbsp;'.$rd->isian.'</td>
					<td class="bdr">&nbsp;</td>
					<td class="bdlr tr">'.$this->rupiah($rd->nilai).'</td>
					<td class="bdlr">&nbsp;</td>
					<td class="bdlr">&nbsp;</td>
					<td class="bdlr tr">'.$this->rupiah($rd->nilai).'</td>
				</tr>
									';
								}
							}
						}
					}
				}
			}
		}
		$html_out.= '
				<tr>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
				</tr>
			</tbody>
		</table>';
		$html_out.= '<table border="0" cellpadding="2" cellspacing="-1" class="fz50">
			<tbody>
				<tr>
					<td class="wd30">&nbsp;</td>
					<td class="wd40">&nbsp;</td>
					<td class="wd30 tl">Jakarta, </td>
				</tr>
				<tr>
					<td class="wd30" colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td class="wd30">Pejabat Pembuat Komitmen</td>
					<td class="wd40">&nbsp;</td>
					<td class="wd30">Bendahara Pengeluaran Pembantu</td>
				</tr>
				<tr>
					<td class="wd30" colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td class="wd30" colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td class="wd30" colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td class="wd30">'.satker($param)[0]->nmppk.'</td>
					<td class="wd40">&nbsp;</td>
					<td class="wd30">'.satker($param)[0]->nmbpp.'</td>
				</tr>
				<tr>
					<td class="wd30">NIP</td>
					<td class="wd40">&nbsp;</td>
					<td class="wd30">NIP</td>
				</tr>
			</tbody>
		</table>';
		//~ return $html_out;
		$mpdf = new mPDF("en", "A4", "15");
		$mpdf->SetTitle('Form RKO_GULS');
		$mpdf->AddPage('P');
		$mpdf->writeHTML($html_out);
		$mpdf->Output('Form_RKOGULS_'.$id.'.pdf','I');
	}
	
	/**
	 * description 
	 */
	public function rekapRKOUP($param)
	{
		function satker($param) { 
			return DB::select("
				SELECT    a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdbpp, d.nmbpp, a.kdppk, e.nmppk, c.nmsatker
				FROM      d_rko a 
				LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
				LEFT JOIN t_satker c ON a.kdsatker = c.kdsatker
				LEFT JOIN t_bpp d ON a.kdbpp = d.kdbpp
				LEFT JOIN t_ppk e ON a.kdppk = e.kdppk
				WHERE     a.kdalur = '01' AND b.id_rekap_up = ".$param."
			");
		}
		
		function rprogram($param) {
			return DB::select("
				SELECT   CONCAT('023', '.', '11', '.', k.kdprogram) AS kode, k.kdprogram, l.nmprogram, SUM(nilai) AS nilai
				FROM       (SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.kdlokasi, a.kdkabkota, a.nip_pk1, a.nip_pk2,
								   a.kdbpp, a.kdppk, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, b.uraian, b.nilai, b.id_rekap_up
							FROM   d_rko a LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
							WHERE  a.kdalur = '01' AND b.id_rekap_up = ".$param.") k
						 LEFT JOIN t_program l
							  ON l.kddept = '023' AND l.kdunit = '11' AND k.kdprogram = l.kdprogram
				GROUP BY CONCAT('023', '.', '11', '.', k.kdprogram), k.kdprogram, l.nmprogram
			");
		}
		
		function rgiat($param) {
			return DB::select("
				SELECT   CONCAT('023', '.', '11', '.', k.kdprogram, '.', k.kdgiat) AS kode, k.kdprogram, k.kdgiat, l.nmgiat, sum(nilai) AS nilai
				FROM       (SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.kdlokasi, a.kdkabkota, a.nip_pk1, a.nip_pk2,
								   a.kdbpp, a.kdppk, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, b.uraian, b.nilai, b.id_rekap_up
							FROM   d_rko a LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
							WHERE  a.kdalur = '01' AND b.id_rekap_up = ".$param.") k
						 LEFT JOIN t_giat l
							  ON k.kdprogram = l.kdprogram AND k.kdgiat = l.kdgiat
				WHERE    k.kddept = '023' AND k.kdunit = '11' AND k.kdprogram = '04'
				GROUP BY CONCAT('023', '.', '11', '.', k.kdprogram, '.', k.kdgiat), k.kdprogram, k.kdgiat, l.nmgiat
			");
		}
		
		function routput($param, $kdgiat)
		{
			return DB::select("
				SELECT   CONCAT('023', '.', '11', '.', k.kdprogram, '.', k.kdgiat, '.', k.kdoutput) AS kode, k.kdprogram, k.kdgiat, k.kdoutput,
						 if(isnull(l.nmoutput), 'Tidak ada di referensi', l.nmoutput) AS nmoutput, sum(nilai) AS nilai
				FROM       (SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.kdlokasi, a.kdkabkota, a.nip_pk1, a.nip_pk2,
								   a.kdbpp, a.kdppk, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, b.uraian, b.nilai, b.id_rekap_up
							FROM   d_rko a LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
							WHERE  a.kdalur = '01' AND b.id_rekap_up = ".$param.") k
						 LEFT JOIN t_output l
							  ON k.kdoutput = l.kdoutput AND k.kdgiat = l.kdgiat
				WHERE    k.kddept = '023' AND k.kdunit = '11' AND k.kdprogram = '04' AND k.kdgiat = '".$kdgiat."'
				GROUP BY CONCAT('023', '.', '11', '.', k.kdprogram, '.', k.kdgiat, '.', k.kdoutput), k.kdprogram, k.kdgiat, k.kdoutput, if(isnull(l.nmoutput), 'Tidak ada di referensi', l.nmoutput)
			");
		}
		
		function rkomponen($param, $kdgiat, $kdoutput)
		{
			return DB::select("
				SELECT   k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen,
						 if(isnull(l.urkmpnen), 'Tidak ada di referensi', l.urkmpnen) AS urkmpnen, sum(nilai) AS nilai
				FROM       (SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.kdlokasi, a.kdkabkota, a.nip_pk1, a.nip_pk2,
								   a.kdbpp, a.kdppk, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, b.uraian, b.nilai, b.id_rekap_up
							FROM   d_rko a LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
							WHERE  a.kdalur = '01' AND b.id_rekap_up = ".$param.") k
						 LEFT JOIN t_kmpnen l
							  ON k.kdgiat = l.kdgiat AND k.kdoutput=l.kdoutput AND k.kdkmpnen = l.kdkmpnen
				WHERE    k.kddept = '023' AND k.kdunit = '11' AND k.kdprogram = '04' AND k.kdgiat = '".$kdgiat."' AND k.kdoutput = '".$kdoutput."'
				GROUP BY k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, if(isnull(l.urkmpnen), 'Tidak ada di referensi', l.urkmpnen)
			");
		}
		
		function rskomponen($param, $kdgiat, $kdoutput, $kdkmpnen)
		{
			return DB::select("
				SELECT   k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, k.kdskmpnen, if(isnull(l.urskmpnen), 'Tidak ada di referensi', l.urskmpnen) AS urskmpnen,
						 sum(nilai) AS nilai
				FROM       (SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.kdlokasi,
								   a.kdkabkota, a.nip_pk1, a.nip_pk2, a.kdbpp, a.kdppk, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen,
								   b.kdakun, b.uraian, b.nilai, b.id_rekap_up
							FROM   d_rko a LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
							WHERE  a.kdalur = '01' AND b.id_rekap_up = ".$param.") k
						 LEFT JOIN t_skmpnen l
							  ON k.kdgiat = l.kdgiat AND k.kdoutput = l.kdoutput AND k.kdkmpnen = l.kdkmpnen AND k.kdskmpnen = l.kdskmpnen
				WHERE    k.kddept = '023' AND k.kdunit = '11' AND k.kdprogram = '04' AND k.kdgiat = '".$kdgiat."' AND k.kdoutput = '".$kdoutput."' AND k.kdkmpnen = '".$kdkmpnen."'
				GROUP BY k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, k.kdskmpnen, if(isnull(l.urskmpnen), 'Tidak ada di referensi', l.urskmpnen)
			");
		}
		
		function rmak($param, $kdgiat, $kdoutput, $kdkmpnen, $kdskmpnen)
		{
			return DB::select("
				SELECT   k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, k.kdskmpnen, k.kdakun,
						 if(isnull(l.nmmak), 'Tidak ada di referensi', l.nmmak) AS nmmak, sum(nilai) AS nilai
				FROM       (SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.kdlokasi, a.kdkabkota, a.nip_pk1, a.nip_pk2,
								   a.kdbpp, a.kdppk, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, b.uraian, b.nilai, b.id_rekap_up
							FROM   d_rko a LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
							WHERE  a.kdalur = '01' AND b.id_rekap_up = ".$param.") k
						 LEFT JOIN t_mak l
							  ON k.kdakun = l.kdmak
				WHERE    k.kddept = '023' AND k.kdunit = '11' AND k.kdprogram = '04' AND k.kdgiat = '".$kdgiat."' AND k.kdoutput = '".$kdoutput."' AND k.kdkmpnen = '".$kdkmpnen."' AND k.kdskmpnen = '".$kdskmpnen."'
				GROUP BY k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, k.kdskmpnen, k.kdakun, if(isnull(l.nmmak), 'Tidak ada di referensi', l.nmmak)
			");	
		}
		
		function ruraian($param, $kdgiat, $kdoutput, $kdkmpnen, $kdskmpnen, $kdakun)
		{
			return DB::select("
				SELECT   k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, k.kdskmpnen, k.kdakun, k.uraian,
						 sum(nilai) AS nilai
				FROM       (SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.kdlokasi, a.kdkabkota, a.nip_pk1, a.nip_pk2,
								   a.kdbpp, a.kdppk, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, b.uraian, b.nilai, b.id_rekap_up
							FROM   d_rko a LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
							WHERE  a.kdalur = '01' AND b.id_rekap_up = ".$param.") k
				WHERE    k.kddept = '023' AND k.kdunit = '11' AND k.kdprogram = '04' AND k.kdgiat = '".$kdgiat."' AND k.kdoutput = '".$kdoutput."' AND k.kdkmpnen = '".$kdkmpnen."' AND k.kdskmpnen = '".$kdskmpnen."' AND k.kdakun = '".$kdakun."'
				GROUP BY k.kdprogram, k.kdgiat, k.kdoutput, k.kdkmpnen, k.kdskmpnen, k.kdakun, k.uraian
			");
		}
		
		$html_out = HTMLController::css();
		$html_out.= '<table border="0" class="fz50">
			<thead>
				<tr>
					<th class="" colspan="3">
						RINCIAN RENCANA PENGGUNAAN UANG PERSEDIAAN (UP)<br/>
						SUMBER DANA : RUPIAH MURNI (RM) TAHUN '.Session::get('tahun').'<br/>
					</th>
				</tr>
				<tr>
					<th class="" colspan="3">&nbsp;</th>
				</tr>
				<tr>
					<th class="vt tl wd30">NAMA SATKER</th>
					<th class="vt tl wd2">:</th>
					<th class="vt tl">Badan Penelitian dan Pengembangan Pendidikan dan Kebudayaan</th>
				</tr>
				<tr>
					<th class="vt tl">KODE SATKER</th>
					<th class="vt tl">:</th>
					<th class="vt tl">'.satker($param)[0]->kdsatker.'</th>
				</tr>
				<tr>
					<th class="vt tl">NOMOR DAN TANGGAL DIPA</th>
					<th class="vt tl">:</th>
					<th class="vt tl">SP DIPA- 023.11.1.137608/2017 Tgl. 07 Desember 2016</th>
				</tr>
				<tr>
					<th class="vt tl">JUMLAH UP-RM</th>
					<th class="vt tl">:</th>
					<th class="vt tl">'.$this->rupiah(rprogram($param)[0]->nilai).'</th>
				</tr>
				<tr>
					<th class="vt tl">UNIT KERJA</th>
					<th class="vt tl">:</th>
					<th class="vt tl">BADAN STANDAR NASIONAL PENDIDIKAN (BSNP)</th>
				</tr>
			</thead>
		</table>';
		$html_out.= '<table border="0" cellpadding="2" cellspacing="-1" class="fz40">
			<tbody>
				<tr>
					<td class="wd5">&nbsp;</td>
					<td class="wd25">&nbsp;</td>
					<td class="wd3">&nbsp;</td>
					<td class="wd7">&nbsp;</td>
					<td class="wd25">&nbsp;</td>
					<td class="wd10">&nbsp;</td>
					<td class="wd8">&nbsp;</td>
					<td class="wd8">&nbsp;</td>
					<td class="wd7">&nbsp;</td>
					<td class="wd12">&nbsp;</td>
				</tr>
				<tr>
					<th class="bd">NO.</th>
					<th class="bd">KODE</th>
					<!--<th class="bd" colspan="2">KELOMPOK AKUN</th>-->
					<th class="bd" colspan="7">URAIAN</th>
					<!--<th class="bd">VOLUME</th>
					<th class="bd">SATUAN</th>-->
					<th class="bd">JUMLAH DIMINTAKAN</th>
				</tr>
				';
		foreach(rprogram($param) as $rp) {
			$html_out.= '
				<tr>
					<td class="bdlr"></td>
					<td class="bdlr vt tl fwb">'.$rp->kode.'</td>
					<td class="bdlr vt fwb" colspan="7">'.$rp->nmprogram.'</td>
					<!--<td class="bdlr">&nbsp;</td>
					<td class="bdlr">&nbsp;</td>-->
					<td class="bdlr vt tr fwb">'.$this->rupiah($rp->nilai).'</td>
				</tr>
			';
			
			foreach(rgiat($param) as $rg) {
				$html_out.= '
				<tr>
					<td class="bdlr bdt bdb"></td>
					<td class="bdlr bdt bdb vt tl fwb">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$rg->kode.'</td>
					<td class="bdlr bdt bdb vt fwb" colspan="7">'.$rg->nmgiat.'</td>
					<!--<td class="bdlr bdt bdb">&nbsp;</td>
					<td class="bdlr bdt bdb">&nbsp;</td>-->
					<td class="bdlr bdt bdb vt tr fwb">'.$this->rupiah($rg->nilai).'</td>
				</tr>
				';
				
				foreach(routput($param, $rg->kdgiat) as $ro) {
					$html_out.= '
				<tr>
					<td class="bdlr bdt bdb"></td>
					<td class="bdlr bdt bdb vt tc fwb">'.$ro->kode.'</td>
					<td class="bdlr bdt bdb vt fwb" colspan="7">'.$ro->nmoutput.'</td>
					<!--<td class="bdlr bdt bdb">&nbsp;</td>
					<td class="bdlr bdt bdb">&nbsp;</td>-->
					<td class="bdlr bdt bdb vt tr fwb">'.$this->rupiah($ro->nilai).'</td>
				</tr>	
					';
					
					foreach(rkomponen($param, $rg->kdgiat, $ro->kdoutput) as $rk) {
						$html_out.= '
				<tr>
					<td class="bdlr bdb"></td>
					<td class="bdlr bdb vt tr fwb">'.$rk->kdkmpnen.'</td>
					<td class="bdlr bdb vt fwb" colspan="7">'.$rk->urkmpnen.'</td>
					<!--<td class="bdlr bdb fwb">&nbsp;</td>
					<td class="bdlr bdb fwb">&nbsp;</td>-->
					<td class="bdlr bdb vt tr fwb">'.$this->rupiah($rk->nilai).'</td>
				</tr>
						';
						
						foreach(rskomponen($param, $rg->kdgiat, $ro->kdoutput, $rk->kdkmpnen) as $rs) {
							$html_out.= '
				<tr>
					<td class="bdlr bdb"></td>
					<td class="bdlr bdb"></td>
					<td class="bdl bdb vt tr">'.$rs->kdskmpnen.'</td>
					<td class="bdr bdb vt tl" colspan="6">'.$rs->urskmpnen.'</td>
					<!--<td class="bdlr bdb">&nbsp;</td>
					<td class="bdlr bdb">&nbsp;</td>-->
					<td class="bdlr bdb vt tr">'.$this->rupiah($rs->nilai).'</td>
				</tr>
							';
							
							foreach(rmak($param, $rg->kdgiat, $ro->kdoutput, $rk->kdkmpnen, $rs->kdskmpnen) as $rm) {
								$html_out.= '
				<tr>
					<td class="bdlr"></td>
					<td class="bdlr"></td>
					<td class="bdl vt tr">&nbsp;</td>
					<td class="vt tr">'.$rm->kdakun.'</td>
					<td class="bdr vt" colspan="5">'.$rm->nmmak.'</td>
					<!--<td class="bdlr">&nbsp;</td>
					<td class="bdlr">&nbsp;</td>-->
					<td class="bdlr vt tr">'.$this->rupiah($rm->nilai).'</td>
				</tr>
								';
								
								foreach(ruraian($param, $rg->kdgiat, $ro->kdoutput, $rk->kdkmpnen, $rs->kdskmpnen, $rm->kdakun) as $ru) {
									$html_out.= '
				<tr>
					<td class="bdlr"></td>
					<td class="bdlr"></td>
					<td class="bdl vt tr">&nbsp;</td>
					<td class="vt tr">&nbsp;</td>
					<td class="bdr vt" colspan="5">-&nbsp;'.$ru->uraian.'</td>
					<!--<td class="bdlr">&nbsp;</td>
					<td class="bdlr">&nbsp;</td>-->
					<td class="bdlr vt tr">'.$this->rupiah($ru->nilai).'</td>
				</tr>
									';
								}
							}
						}
					}
				}
			}
		}
		$html_out.= '
				<tr>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
					<td class="bdt">&nbsp;</td>
				</tr>
				';
		$html_out.= '
			</tbody>
		</table>';
		$html_out.= '<table border="0" cellpadding="2" cellspacing="-1" class="fz50">
			<tbody>
				<tr>
					<td class="wd30">&nbsp;</td>
					<td class="wd40">&nbsp;</td>
					<td class="wd30 tl">Jakarta, </td>
				</tr>
				<tr>
					<td class="wd30">Kuasa Pengguna Anggaran,</td>
					<td class="wd40">&nbsp;</td>
					<td class="wd30">Bendahara Pengeluaran</td>
				</tr>
				<tr>
					<td class="wd30" colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td class="wd30" colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td class="wd30" colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td class="wd30">'.HTMLController::KPA()->nmkpa.'</td>
					<td class="wd40">&nbsp;</td>
					<td class="wd30">'.HTMLController::BP()->nmbp.'</td>
				</tr>
				<tr>
					<td class="wd30">NIP '.HTMLController::KPA()->nipkpa.'</td>
					<td class="wd40">&nbsp;</td>
					<td class="wd30">NIP '.HTMLController::BP()->nipbp.'</td>
				</tr>
			</tbody>
		</table>';
		//~ return $html_out;
		$mpdf = new mPDF("en", "A4", "15");
		$mpdf->SetTitle('Form RKO_UP');
		$mpdf->AddPage('P');
		$mpdf->writeHTML($html_out);
		$mpdf->Output('Form_RKOUP_'.$id.'.pdf','I');
	}
	
	/**
	 * description 
	 */
	public function dus($param)
	{
		//~ return json_encode(HTMLController::KPA()[0]);
		$obj = HTMLController::KPA()[0]->nmkpa;
		return $obj;
	}
	
	/**
	 * description 
	 */
	public function rupiah($angka)
	{
		return HTMLController::rupiah($angka, 0, ',', '.');
	}
}
