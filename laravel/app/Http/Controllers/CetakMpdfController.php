<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use mPDF;

class CetakMpdfController extends Controller {
	private $huruf=array('','Satu','Dua','Tiga','Empat','Lima','Enam', 'Tujuh', ' Delapan','Sembilan','Sepuluh','Sebelas');
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
	 public function routingSlipTransaksi($param)
	{
		$rows = DB::select("
			select concat(kddept,'-',kdunit,'-',kdsatker)identitas,
			kdmak,
			thang,
			'' as nokuitansi,
			IFNULL(DATE_FORMAT(tgkuitansi,'%d-%m-%Y'),'N/A')tgkuitansi,totnilmak from d_transaksi where id=?
		", [
			$param
		]);
		if(count($rows) != 0) {
			$rows = $rows;
			$rows1 = DB::select("
			SELECT	IFNULL(a.nosurat,'N/A') AS nosurat,
						IFNULL(DATE_FORMAT(a.tgsurat,'%d-%m-%Y'),'N/A') AS tgsurat,
						IFNULL(a.ketsurat,'N/A') AS ketsurat,
						c.status,
						IF(a.terima='1','Dilanjutkan','Dikembalikan') AS lanjut,
						d.nama,
						e.nmlevel,
						DATE_FORMAT(a.created_at,'%d-%m-%Y') created_at,
						c.norma
				FROM d_transaksi_status a
				LEFT OUTER JOIN d_transaksi b ON(a.id_trans=b.id)
				LEFT OUTER JOIN t_alur_status c ON(b.kdalur=c.kdalur AND a.nourut=c.nourut)
				LEFT OUTER JOIN t_user d ON(a.id_user=d.id)
				LEFT OUTER JOIN t_level e ON(c.kdlevel=e.kdlevel)
				WHERE a.id_trans= ?
				ORDER BY a.id ASC
		", [
			$param
		]);
		} else {
			$rows = DB::select("
				select 'N/A' identitas,'N/A' kdmak,'N/A' thang,'N/A' nokuitansi,'N/A' tgkuitansi,null totnilmak from DUAL
			",[]);
			$rows1 = DB::select("
				SELECT	'-' nosurat,
						'-' tgsurat,
						'-' ketsurat,
						'-' status,
						'-' lanjut,
						'-' nama,
						'-' nmlevel,
						'-' created_at,
						'-' norma
				FROM DUAL
			",[]);
		}
		
		
		
		
		
		$html_out = '<p style="font-size:80%; font-weight:bold;">Routing Slip Transaksi</p>';
		
		$html_out .= '
			<style>
				#tbl-content, th {
					border: 0px solid black;
					border-collapse: collapse;
					font-size:70%;
					vertical-align: middle;
				}
				#tbl-content, td {
					border: 0px solid black;
					border-collapse: collapse;
					font-size:70%;
					vertical-align: top;
				}
			</style>
			<table style="border:0px solid #000;border-collapse:collapse; width:100%; font-size:80%;">';
		$nilai=$rows[0]->totnilmak===null?'N/A': $rows[0]->totnilmak===''?'N/A': $rows[0]->totnilmak==='-'?'N/A': 
			number_format($rows[0]->totnilmak, 0, ",", ".");
		$html_out .= '
				<thead>
					<tr>
						<td style="text-align:left; width:20%;">dept-unit-satker</td>
						<td style="text-align:left; width:2%;">:</td>
						<td style="text-align:left;">'.$rows[0]->identitas.'</td>
					</tr>
					<tr>
						<td style="text-align:left; width:20%;">kode MAK</td>
						<td style="text-align:left; width:2%;">:</td>
						<td style="text-align:left;">'.$rows[0]->kdmak.'</td>
					</tr>
					<tr>
						<td style="text-align:left; width:20%;">Tahun Anggaran</td>
						<td style="text-align:left; width:2%;">:</td>
						<td style="text-align:left;">'.$rows[0]->thang.'</td>
					</tr>
					<tr>
						<td style="text-align:left; width:20%;">ID Kuitansi</td>
						<td style="text-align:left; width:2%;">:</td>
						<td style="text-align:left;">'.$param.'</td>
					</tr>
					<tr>
						<td style="text-align:left; width:20%;">Tanggal Kuitansi</td>
						<td style="text-align:left; width:2%;">:</td>
						<td style="text-align:left;">'.$rows[0]->tgkuitansi.'</td>
					</tr>
					<tr>
						<td style="text-align:left; width:20%;">Nilai</td>
						<td style="text-align:left; width:2%;">:</td>
						<td style="text-align:left;">'.$nilai.'</td>
					</tr>
				</thead>
			</table>';
			
		$html_out .= '
			
			<table id="tbl-content" style="border:1px solid #000;border-collapse:collapse; width:100%">';
			
		$html_out .= '
				<thead>
					<tr>
						<th style="border:1px solid #000;border-collapse:collapse; text-align:center; padding:4px;">Nomor Surat</th>
						<th style="border:1px solid #000;border-collapse:collapse; text-align:center; padding:4px;">Tanggal Surat</th>
						<th style="border:1px solid #000;border-collapse:collapse; text-align:center; padding:4px;">Keterangan Surat</th>
						<th style="border:1px solid #000;border-collapse:collapse; text-align:center; padding:4px;">Status</th>
						<th style="border:1px solid #000;border-collapse:collapse; text-align:center; padding:4px;">Lanjut</th>
						<th style="border:1px solid #000;border-collapse:collapse; text-align:center; padding:4px;">Nama</th>
						<th style="border:1px solid #000;border-collapse:collapse; text-align:center; padding:4px;">Nama Level</th>
						<th style="border:1px solid #000;border-collapse:collapse; text-align:center; padding:4px;">created_at</th>
						<th style="border:1px solid #000;border-collapse:collapse; text-align:center; padding:4px;">Norma</th>
					</tr>
				</thead>
		';
		
		$html_out .= '
				<tbody>';
				
		foreach($rows1 as $row) {
			$html_out .= '
					<tr>
						<td style="border:1px solid #000;border-collapse:collapse; text-align:right; padding:4px;">'.$row->nosurat.'.</td>
						<td style="border:1px solid #000;border-collapse:collapse; text-align:right; padding:4px;">'.$row->tgsurat.'</td>
						<td style="border:1px solid #000;border-collapse:collapse; text-align:right; padding:4px;">'.$row->ketsurat.'</td>
						<td style="border:1px solid #000;border-collapse:collapse; text-align:right; padding:4px;">'.$row->status.'</td>
						<td style="border:1px solid #000;border-collapse:collapse; text-align:right; padding:4px;">'.$row->lanjut.'</td>
						<td style="border:1px solid #000;border-collapse:collapse; text-align:right; padding:4px;">'.$row->nama.'</td>
						<td style="border:1px solid #000;border-collapse:collapse; text-align:right; padding:4px;">'.$row->nmlevel.'</td>
						<td style="border:1px solid #000;border-collapse:collapse; text-align:right; padding:4px;">'.$row->created_at.'</td>
						<td style="border:1px solid #000;border-collapse:collapse; text-align:right; padding:4px;">'.$row->norma.'</td>
					</tr>
			';
		} 
		
		
		
		$html_out .= '
				</tbody>';
			
		$html_out .= '
			</table>';
		
		//render PDF
		$mpdf = new mPDF("en", "A4", "15");
		$mpdf->AddPage('P');
		$mpdf->writeHTML($html_out);
		$mpdf->Output('Routing Slip.pdf', 'D');
		exit; 
	}
	
	public function routingSlipRko($param)
	{
		$rows = DB::select("
			select concat(kddept,'-',kdunit,'-',kdsatker)identitas,
			thang,
			periode1,
			b.nmjenisgiat,
			urrko,
			IFNULL(DATE_FORMAT(tgrko,'%d-%m-%Y'),'N/A')tgrko
			from d_rko a
			left join t_jenisgiat b
			on a.jenisgiat=b.jenisgiat
			 where id=?
		", [
			$param
		]);
		if(count($rows) != 0) {
			$rows = $rows;
			$rows1 = DB::select("
			SELECT	IFNULL(a.nosurat,'N/A') AS nosurat,
						IFNULL(DATE_FORMAT(a.tgsurat,'%d-%m-%Y'),'N/A') AS tgsurat,
						IFNULL(a.ketsurat,'N/A') AS ketsurat,
						c.status,
						IF(a.terima='1','Dilanjutkan','Dikembalikan') AS lanjut,
						d.nama,
						e.nmlevel,
						a.created_at,
						c.norma
				FROM d_rko_status a
				LEFT OUTER JOIN d_rko b ON(a.id_rko=b.id)
				LEFT OUTER JOIN t_alur_status c ON(b.kdalur=c.kdalur AND a.nourut=c.nourut)
				LEFT OUTER JOIN t_user d ON(a.id_user=d.id)
				LEFT OUTER JOIN t_level e ON(c.kdlevel=e.kdlevel)
				WHERE a.id_rko=? and b.id=?
				ORDER BY a.id ASC
		", [
			$param,$param
		]);
		} else {
			$rows = DB::select("
				select 'N/A' identitas,'N/A' thang,'N/A' periode1,'N/A' nmjenisgiat,'N/A' urrko, 'N/A' tgrko from DUAL
			",[]);
			$rows1 = DB::select("
				SELECT	'-' nosurat,
						'-' tgsurat,
						'-' ketsurat,
						'-' status,
						'-' lanjut,
						'-' nama,
						'-' nmlevel,
						'-' created_at,
						'-' norma
				FROM DUAL
			",[]);
		}
		
		
		
		
		
		$html_out = '<p style="font-size:80%; font-weight:bold;">Routing Slip Transaksi</p>';
		
		$html_out .= '
			<style>
				#tbl-content, th {
					border: 0px solid black;
					border-collapse: collapse;
					font-size:70%;
					vertical-align: middle;
				}
				#tbl-content, td {
					border: 0px solid black;
					border-collapse: collapse;
					font-size:70%;
					vertical-align: top;
				}
			</style>
			<table style="border:0px solid #000;border-collapse:collapse; width:100%; font-size:80%;">';
		
		$html_out .= '
				<thead>
					<tr>
						<td style="text-align:left; width:20%;">dept-unit-satker</td>
						<td style="text-align:left; width:2%;">:</td>
						<td style="text-align:left;">'.$rows[0]->identitas.'</td>
					</tr>
					<tr>
						<td style="text-align:left; width:20%;">Periode</td>
						<td style="text-align:left; width:2%;">:</td>
						<td style="text-align:left;">'.$rows[0]->periode1.'</td>
					</tr>
					<tr>
						<td style="text-align:left; width:20%;">Tahun Anggaran</td>
						<td style="text-align:left; width:2%;">:</td>
						<td style="text-align:left;">'.$rows[0]->thang.'</td>
					</tr>
					<tr>
						<td style="text-align:left; width:20%;">Jenis RKO</td>
						<td style="text-align:left; width:2%;">:</td>
						<td style="text-align:left;">'.$rows[0]->nmjenisgiat.'</td>
					</tr>
					<tr>
						<td style="text-align:left; width:20%;">Uraian RKO</td>
						<td style="text-align:left; width:2%;">:</td>
						<td style="text-align:left;">'.$rows[0]->urrko.'</td>
					</tr>
					<tr>
						<td style="text-align:left; width:20%;">Tanggal RKO</td>
						<td style="text-align:left; width:2%;">:</td>
						<td style="text-align:left;">'.$rows[0]->tgrko.'</td>
					</tr>
				</thead>
			</table>';
			
		$html_out .= '
			
			<table id="tbl-content" style="border:1px solid #000;border-collapse:collapse; width:100%">';
			
		$html_out .= '
				<thead>
					<tr>
						<th style="border:1px solid #000;border-collapse:collapse; text-align:center; padding:4px;">Nomor Surat</th>
						<th style="border:1px solid #000;border-collapse:collapse; text-align:center; padding:4px;">Tanggal Surat</th>
						<th style="border:1px solid #000;border-collapse:collapse; text-align:center; padding:4px;">Keterangan Surat</th>
						<th style="border:1px solid #000;border-collapse:collapse; text-align:center; padding:4px;">Status</th>
						<th style="border:1px solid #000;border-collapse:collapse; text-align:center; padding:4px;">Lanjut</th>
						<th style="border:1px solid #000;border-collapse:collapse; text-align:center; padding:4px;">Nama</th>
						<th style="border:1px solid #000;border-collapse:collapse; text-align:center; padding:4px;">Nama Level</th>
						<th style="border:1px solid #000;border-collapse:collapse; text-align:center; padding:4px;">created_at</th>
						<th style="border:1px solid #000;border-collapse:collapse; text-align:center; padding:4px;">Norma</th>
					</tr>
				</thead>
		';
		
		$html_out .= '
				<tbody>';
				
		foreach($rows1 as $row) {
			$html_out .= '
					<tr>
						<td style="border:1px solid #000;border-collapse:collapse; text-align:right; padding:4px;">'.$row->nosurat.'.</td>
						<td style="border:1px solid #000;border-collapse:collapse; text-align:right; padding:4px;">'.$row->tgsurat.'</td>
						<td style="border:1px solid #000;border-collapse:collapse; text-align:right; padding:4px;">'.$row->ketsurat.'</td>
						<td style="border:1px solid #000;border-collapse:collapse; text-align:right; padding:4px;">'.$row->status.'</td>
						<td style="border:1px solid #000;border-collapse:collapse; text-align:right; padding:4px;">'.$row->lanjut.'</td>
						<td style="border:1px solid #000;border-collapse:collapse; text-align:right; padding:4px;">'.$row->nama.'</td>
						<td style="border:1px solid #000;border-collapse:collapse; text-align:right; padding:4px;">'.$row->nmlevel.'</td>
						<td style="border:1px solid #000;border-collapse:collapse; text-align:right; padding:4px;">'.$row->created_at.'</td>
						<td style="border:1px solid #000;border-collapse:collapse; text-align:right; padding:4px;">'.$row->norma.'</td>
					</tr>
			';
		} 
		
		
		
		$html_out .= '
				</tbody>';
			
		$html_out .= '
			</table>';
		
		//render PDF
		$mpdf = new mPDF("en", "A4", "15");
		$mpdf->SetTitle('Form Routing Slip RKO');
		$mpdf->AddPage('P');
		$mpdf->writeHTML($html_out);
		$mpdf->Output('Routing Slip.pdf', 'I');

	}
	
	public function kuitansi($param)
	{
		$rows = DB::select("
			select a.thang,a.id as nokuitansi,DATE_FORMAT(tgkuitansi,'%d %M %Y')tgkuitansi,totnilmak,untuk,ppn, pph_21+pph_22+pph_23+pph_24 as pph,
						IFNULL(d.nmsatker,'N/A')nmsatker,kdmak,
				case b.jenisgiat when 06 or 07 then CONCAT('NIP.',nip_penerima)else '' end as nip_penerima2,
				case b.jenisgiat when 06 or 07 then c.nama else '' end as nama_penerima2,
				case b.jenisgiat when 06 or 07 then uraiben else 'Penerima uang' end as jab_penerima1,
				case b.jenisgiat when 06 or 07 then '' else uraiben end as nama_penerima1,
				  e.nmppk,
				  e.nipppk
						from d_transaksi a
					join d_rko b
						on a.id_rko=b.id
						left outer join t_pegawai c
						on a.nip_penerima=c.nip
						LEFT outer JOIN T_SATKER d
						ON a.kdsatker=d.kdsatker
				left OUTER JOIN (
					SELECT	
						a.kdsatker,
						a.thang,
						a.kdppk,
						a.nmppk,
						b.nip AS nipppk
					FROM t_ppk a
					LEFT OUTER JOIN(
						SELECT	kdsatker,
							kdppk,
							nip
						FROM t_user
						WHERE kdlevel='06'
					) b ON(a.kdsatker=b.kdsatker AND a.thang='".session('tahun')."' AND a.kdppk=b.kdppk)
				) e on b.kdsatker=e.kdsatker and a.thang=e.thang and b.kdppk=e.kdppk
			where a.id=?
		", [
			$param
		]);
		if(empty($rows)) return 'tidak ada data';
		
		$html_out = '
			<style>
				#tbl-content, th {
					border: 0px solid black;
					border-collapse: collapse;
					font-size:70%;
					vertical-align: middle;
				}
				#tbl-content, td {
					border: 0px solid black;
					border-collapse: collapse;
					font-size:70%;
					vertical-align: top;
				}
				 #wrapper {
				  overflow: auto;
				  left: 10%;
				  right: 10%;
				  top: 5%;
				  padding-left: 1%;
				  padding-right: 1%;
				  padding-bottom: 2%;
				  border: 1px solid;
				}
			</style>
			';
		$terbilang=$this->angkaToTerbilang($rows[0]->totnilmak);
		$html_out .= '
			<div id="wrapper">
			<p align="center" style="font-size: 70%;"><b>KUITANSI PEMBAYARAN LANGSUNG</b></p>
			<table style="border:0px solid #000;border-collapse:collapse; width:100%;font-size: 90%;">
					<tr>
						<td style="width:70%;"></td>
						<td style="text-align:left; width:15%;">TA</td>
						<td style="text-align:left; width:2%;">:</td>
						<td style="text-align:left;">'.$rows[0]->thang.'</td>
					</tr>
					<tr>
						<td style="width:60%;"></td>
						<td style="text-align:left; width:15%;">Nomor Bukti</td>
						<td style="text-align:left; width:2%;">:</td>
						<td style="text-align:left;">'.$rows[0]->nokuitansi.'</td>
					</tr>
					<tr>
						<td style="width:60%;"></td>
						<td style="text-align:left; width:15%;">Mata Anggaran</td>
						<td style="text-align:left; width:2%;">:</td>
						<td style="text-align:left;">'.$rows[0]->kdmak.'</td>
					</tr>				
			</table>
			<p align="center" style="font-size: 70%;"><b>KUITANSI/BUKTI PEMBAYARAN</b></p>
			<br>';
		$html_out .= '
			<table style="border:0px solid #000;border-collapse:collapse; width:100%;font-size: 90%;">
				<tr>
					<td style="text-align:left; width:20%;height:2em;">Sudah diterima dari</td>
					<td style="text-align:left; width:2%;height:2em;">:</td>
					<td style="text-align:left;height:2em;">Pejabat Pembuat Komitmen<br>Satker '.$rows[0]->nmsatker.'</td>
				</tr>
				<tr>
					<td style="text-align:left; width:20%;height:2em;">Jumlah uang</td>
					<td style="text-align:left; width:2%;height:2em;">:</td>
					<td style="text-align:left;height:2em;">Rp. '.number_format($rows[0]->totnilmak, 0, ",", ".").'</td>
				</tr>
				<tr>
					<td style="text-align:left; width:20%;height:2em;">Terbilang</td>
					<td style="text-align:left; width:2%;height:2em;">:</td>
					<td style="text-align:left;height:2em;">'.$terbilang.' Rupiah</td>
				</tr>
				<br>
				<tr>
					<td style="text-align:left; width:20%;height:2em;">Untuk pembayaran</td>
					<td style="text-align:left; width:2%;height:2em;">:</td>
					<td style="text-align:left;height:2em;">'.$rows[0]->untuk.'</td>
				</tr>
					
			</table>
			<br><br><br>';
			$html_out .= '
				<div style="padding-left: 5%;padding-right: 5%;">
					<table style="border:0px solid #000;border-collapse:collapse; width:100%; font-size:80%;">
						<tbody>
							<tr>
								<td style="text-align:left;">a.n.Kuasa Pengguna Anggaran</td>
								<td style="width:2%;"></td>
								<td></td>
								<td style="width:2%;"></td>
								<td style="text-align:left;">Jakarta, '.$rows[0]->tgkuitansi.'</td>
							</tr>
							<tr> 
								<td style="text-align:left;">Pejabat Pembuat Komitmen</td>
								<td style="width:2%;"></td>
								<td style="text-align:left;">Juru Bayar</td>
								<td style="width:2%;"></td>
								<td style="text-align:left;">'.$rows[0]->jab_penerima1.'</td>
							</tr>
							<br><br><br>
							<tr>
								<td style="text-align:left;">'.$rows[0]->nmppk.'</td>
								<td style="width:2%;"></td>
								<td style="text-align:left;">'.Session::get('nama').'</td>
								<td style="width:2%;"></td>
								<td style="text-align:left;">'.$rows[0]->nama_penerima1.'</td>
							</tr>
							<tr>
								<td style="text-align:left;">NIP.'.$rows[0]->nipppk.'</td>
								<td style="width:2%;"></td>
								<td style="text-align:left;">NIP.'.Session::get('nip').'</td>
								<td style="width:2%;"></td>
								<td style="text-align:left;"></td>
							</tr>
						</tbody>
					</table>
				</div>
				</div>
				<div id="wrapper">
				<p style="font-size: 60%;">Barang/pekerjaan tersebut telah diterima/diselesaikan dengan lengkap dan baik.<br>
				Penerima barang
				<br><br><br>
				'.$rows[0]->nama_penerima2.'<br>
				'.$rows[0]->nip_penerima2.'</p>
				</div>
				';
		
		
		//render PDF
		$mpdf = new mPDF("en", "A4", "15");
		$mpdf->AddPage('P');
		$mpdf->writeHTML($html_out);
		$mpdf->Output('Kuitansi.pdf', 'D');
		exit; 
	}
	
	public function drpp($param)
	{
		$rows1 = DB::select("
			select a.kddept,ifnull(d.nmdept,'N/A')nmdept,a.kdunit,ifnull(e.nmunit,'N/A')nmunit,a.kdsatker,ifnull(f.nmsatker,'N/A')nmsatker,a.kddekon,a.thang,a.nodrpp,
			DATE_FORMAT(a.tgdrpp,'%d %M %Y')tgdrpp,a.urdrpp,b.kdgiat,b.kdoutput,c.nodok,DATE_FORMAT(c.tgdok,'%d %M %Y')tgdok,paguakhir,
			ifnull(f.kdlokasi,'N/A')kdlokasi,ifnull(g.nmlokasi,'N/A')nmlokasi,DATE_FORMAT(a.tgdrpp,'%d')bulan,b.totnilmak,b.cnt,
			DATE_FORMAT(SYSDATE(),'%d %M %Y') skrg,h.nmppk,h.nipppk from
			(select kddept,kdunit,kdsatker,kddekon,thang,nodrpp,tgdrpp,urdrpp,nospp,kdppk from d_drpp where nodrpp=?)a
			left join (select nodrpp,kdgiat,kdoutput,sum(totnilmak)totnilmak,count(*) cnt from d_transaksi where nodrpp=?
			group by nodrpp,kdgiat,kdoutput)b 
			on a.nodrpp=b.nodrpp
			left join(select kddept,kdunit,kdsatker,kddekon,thang,kdgiat,kdoutput,nodok,tgdok,paguakhir from d_pagu where lvl='3')c
			on a.kddept=c.kddept and a.kdunit=c.kdunit and a.kdsatker=c.kdsatker and a.thang=c.thang and b.kdgiat=c.kdgiat and b.kdoutput=c.kdoutput
			left join t_dept d
			on a.kddept=d.kddept
			left join t_unit e
			on a.kdunit=e.kdunit and a.kddept=e.kddept
			left join t_satker f
			on a.kdsatker=f.kdsatker
			left join t_lokasi g
			on f.kdlokasi= g.kdlokasi
			left join (
				SELECT	
						a.kdsatker,
						a.thang,
						a.kdppk,
						a.nmppk,
						b.nip AS nipppk
					FROM t_ppk a
					LEFT OUTER JOIN(
						SELECT	kdsatker,
							kdppk,
							nip
						FROM t_user
						WHERE kdlevel='06'
					) b ON(a.kdsatker=b.kdsatker AND a.thang='".session('tahun')."' AND a.kdppk=b.kdppk)
			) h
			on a.kdsatker=h.kdsatker and a.thang=h.thang and a.kdppk= h.kdppk
		", [
			$param,$param
		]);
		if(empty($rows1)) return 'tidak ada data';
		
		$rows2 = DB::select("
			select id as nokuitansi,DATE_FORMAT(tgkuitansi,'%d %M %Y')tgkuitansi,uraiben,untuk,kdnpwp,kdmak,totnilmak 
			from d_transaksi where nodrpp=?
			order by id", [
			$param,
			]);
		$html_out = '
			<style>
				#tbl-content, th {
					border: 0px solid black;
					border-collapse: collapse;
					font-size:70%;
					vertical-align: middle;
				}
				#tbl-content, td {
					border: 0px solid black;
					border-collapse: collapse;
					font-size:70%;
					vertical-align: top;
				}
			</style>
			<table style="border:0px solid #000;border-collapse:collapse; width:100%;">
				<tr>
					<td colspan="4" style="text-align:center;"><h3>DAFTAR RINCIAN PERMINTAAN PEMBAYARAN</h3></td>
				</tr>
				<tr>
					<td colspan="4" style="text-align:center;"><h4>NOMOR : '.$rows1[0]->nodrpp.'</h4></td>
				</tr>
			</table>
			';
		$html_out .= '
			<table style="border:1px solid #000;border-collapse:collapse; width:100%;">
					
					<tr>
						<td style="text-align:left;font-size: 11px;">Kementerian</td>
						<td style="text-align:left; font-size: 11px;">:</td>
						<td style="text-align:left;font-size: 11px;">('.$rows1[0]->kddept.')</td>
						<td style="text-align:left;font-size: 11px;">'.$rows1[0]->nmdept.'</td>
						<td style="text-align:left;font-size: 11px;">Jenis SPP</td>
						<td style="text-align:left; font-size: 11px;">:</td>
						<td style="text-align:left; font-size: 11px;"></td>
						<td style="text-align:left; font-size: 11px;">DIPA</td>
						<td style="text-align:left; font-size: 11px;">:</td>
						<td style="text-align:left; font-size: 11px;">'.$rows1[0]->nodok.'</td>
					</tr>
					<tr>
						<td style="text-align:left;font-size: 11px;">Unit Organisasi</td>
						<td style="text-align:left; font-size: 11px;">:</td>
						<td style="text-align:left;font-size: 11px;">('.$rows1[0]->kdunit.')</td>
						<td style="text-align:left;font-size: 11px;">'.$rows1[0]->nmunit.'</td>
						<td style="text-align:left;font-size: 11px;"></td>
						<td style="text-align:left; font-size: 11px;"></td>
						<td style="text-align:left; font-size: 11px;"></td>
						<td style="text-align:left; font-size: 11px;"></td>
						<td style="text-align:left; font-size: 11px;"></td>
						<td style="text-align:left; font-size: 11px;">'.$rows1[0]->tgdok.'</td>
					</tr>
					<tr>
						<td style="text-align:left;font-size: 11px;">Lokasi</td>
						<td style="text-align:left; font-size: 11px;">:</td>
						<td style="text-align:left;font-size: 11px;">('.$rows1[0]->kdlokasi.')</td>
						<td style="text-align:left;font-size: 11px;">'.$rows1[0]->nmlokasi.'</td>
						<td style="text-align:left;font-size: 11px;"></td>
						<td style="text-align:left; font-size: 11px;"></td>
						<td style="text-align:left; font-size: 11px;"></td>
						<td style="text-align:left; font-size: 11px;">Kode Kegiatan</td>
						<td style="text-align:left; font-size: 11px;">:</td>
						<td style="text-align:left; font-size: 11px;">'.$rows1[0]->kdgiat.'</td>
					</tr>
					<tr>
						<td style="text-align:left;font-size: 11px;">Satuan Kerja</td>
						<td style="text-align:left; font-size: 11px;">:</td>
						<td style="text-align:left;font-size: 11px;">('.$rows1[0]->kdsatker.')</td>
						<td style="text-align:left;font-size: 11px;">'.$rows1[0]->nmsatker.'</td>
						<td style="text-align:left;font-size: 11px;"></td>
						<td style="text-align:left; font-size: 11px;"></td>
						<td style="text-align:left; font-size: 11px;"></td>
						<td style="text-align:left; font-size: 11px;">Kode Output</td>
						<td style="text-align:left; font-size: 11px;">:</td>
						<td style="text-align:left; font-size: 11px;">'.$rows1[0]->kdoutput.'</td>
					</tr>
					<tr>
						<td style="text-align:left;font-size: 11px;">Alamat</td>
						<td style="text-align:left; font-size: 11px;">:</td>
						<td style="text-align:left;font-size: 11px;"></td>
						<td style="text-align:left;font-size: 11px;"></td>
						<td style="text-align:left;font-size: 11px;">Pagu Output</td>
						<td style="text-align:left; font-size: 11px;">:</td>
						<td style="text-align:left; font-size: 11px;">'.number_format($rows1[0]->paguakhir, 0, ",", ".").'</td>
						<td style="text-align:left; font-size: 11px;">Bulan</td>
						<td style="text-align:left; font-size: 11px;">:</td>
						<td style="text-align:left; font-size: 11px;">'.$rows1[0]->bulan.'</td>
					</tr>
					<br><br>
					
			</table>
			<table style="border:0px solid #000;border-collapse:collapse; width:100%;">
				<tr>
					<td colspan="4" style="text-align:center;"><h4>Bukti Pengeluaran</h4></td>
				</tr>
			</table>
			';
		$html_out .= '
			<table style="border:1px solid #000;border-collapse:collapse; width:100%;">
					<thead>
						<tr>
							<th style="border:1px solid #000;border-collapse:collapse;text-align:left;font-size: 11px;">No</th>
							<th style="border:1px solid #000;border-collapse:collapse;text-align:left;font-size: 11px;">Tgl dan No Bukti</th>
							<th style="border:1px solid #000;border-collapse:collapse;text-align:left;font-size: 11px;">Nama Penerima dan keperluan</th>
							<th style="border:1px solid #000;border-collapse:collapse;text-align:left;font-size: 11px;">NPWP</th>
							<th style="border:1px solid #000;border-collapse:collapse;text-align:left;font-size: 11px;">AKUN</th>
							<th style="border:1px solid #000;border-collapse:collapse;text-align:left;font-size: 11px;">Jumlah Kotor</th>
						</tr>
						</thead>
					<tbody>';
		$i=1;			
		foreach($rows2 as $row) {
			$html_out .= '
					<tr>
						<td style="border:1px solid #000;border-collapse:collapse; text-align:right; padding:4px;">'.$i.'.</td>
						<td style="border:1px solid #000;border-collapse:collapse; text-align:right; padding:4px;">'.$row->nokuitansi.'<br>'.$row->tgkuitansi.'</td>
						<td style="border:1px solid #000;border-collapse:collapse; text-align:right; padding:4px;">'.$row->uraiben.'<br>'.$row->untuk.'</td>
						<td style="border:1px solid #000;border-collapse:collapse; text-align:right; padding:4px;">'.$row->kdnpwp.'</td>
						<td style="border:1px solid #000;border-collapse:collapse; text-align:right; padding:4px;">'.$row->kdmak.'</td>
						<td style="border:1px solid #000;border-collapse:collapse; text-align:right; padding:4px;">'.number_format($row->totnilmak, 0, ",", ".").'</td>

					</tr>';
			$i++;
		} 
		
		$html_out .='
					</tbody>
					<thead>
						<tr>
							<th style="text-align:left;font-size: 11px;" colspan="2">Jumlah Lampiran '.$rows1[0]->cnt.'<br>Lembar</th>
							<th style="text-align:right;font-size: 11px;" colspan="3">Jumlah SPP ini:<br>
							Jumlah s.d. lalu atas beban output ini:<br>
							JUmlah s.d. SPP ini atas beban output ini:</th>
							<th style="text-align:right;font-size: 11px;">'.number_format($rows1[0]->totnilmak, 0, ",", ".").'<br>
							'.number_format($rows1[0]->paguakhir, 0, ",", ".").'<br>
							'.number_format($rows1[0]->paguakhir-$rows1[0]->totnilmak, 0, ",", ".").'</th>
							<
						</tr>
						</thead>
					<tbody>
			</table>
			';
				
		$html_out .='
			<br><br>
			<table style="border:0px solid #000;border-collapse:collapse; width:100%;">
				<tr>
					<td style="text-align:left;width:50%;"></td>
					<td  style="text-align:left;font-size: 11px;width:50%;">'.$rows1[0]->nmlokasi.', '.$rows1[0]->skrg.'</td>
				</tr>
				<tr>
					<td  style="text-align:left;width:50%;"></td>
					<td  style="text-align:left;font-size: 11px;width:50%;">a.n. Kuasa Pengguna Anggaran</td>
				</tr>
				<tr>
					<td  style="text-align:left;width:50%;"></td>
					<td  style="text-align:left;font-size: 11px;width:50%;">PEJABAT PEMBUAT KOMITMEN</td>
				</tr>
				<br><br><br>
				<tr>
					<td  style="text-align:left;width:50%;"></td>
					<td  style="text-align:left;font-size: 11px;width:50%;">'.$rows1[0]->nmppk.'</td>
				</tr>
				<tr>
					<td  style="text-align:left;width:50%;"></td>
					<td  style="text-align:left;font-size: 11px;width:50%;">NIP. '.$rows1[0]->nipppk.'</td>
				</tr>
			</table>
		';
		
		//render PDF
		$mpdf = new mPDF("en", "A4", "15");
		$mpdf->AddPage('P');
		$mpdf->writeHTML($html_out);
		$mpdf->Output('DRPP.pdf', 'D');
		exit; 
	}
	private function angkaToTerbilang($x)
	{
		$angka=(float)$x;
		if($angka<12)
			return $this->huruf[$angka];
		elseif ($angka<20)
			return $this->huruf[$angka-10].' Belas';
		elseif ($angka<100)
			return $this->angkaToTerbilang($angka/10).' Puluh '.$this->huruf[$angka%10];
		elseif ($angka<200)
			return 'Seratus '.$this->angkaToTerbilang($angka-100);
		elseif ($angka<1000)
			return $this->angkaToTerbilang($angka/100).' Ratus '.$this->angkaToTerbilang($angka%100);
		elseif ($angka<2000)
			return 'Seribu '.$this->angkaToTerbilang($angka-1000);
		elseif($angka < 1000000)
			return $this->angkaToTerbilang($angka / 1000) . " Ribu " . $this->angkaToTerbilang($angka % 1000);
		elseif ($angka < 1000000000)
			return $this->angkaToTerbilang($angka / 1000000) . " Juta " . $this->angkaToTerbilang($angka % 1000000);
		elseif ($angka < 1000000000000)
			return $this->angkaToTerbilang($angka / 1000000000) . " Milyar " . $this->angkaToTerbilang(fmod($angka , 1000000000));
		elseif ($angka < 1000000000000000)
			return $this->angkaToTerbilang($angka / 1000000000000) . " Triliun " . $this->angkaToTerbilang(fmod($angka , 1000000000000));
		else
		return '';
	}
	private function bulan($x)
	{
		if($x=='01')return 'Januari';
		elseif ($x=='02')return 'Februari';
		elseif ($x=='03')return 'Maret';
		elseif ($x=='04')return 'April';
		elseif ($x=='05')return 'Mei';
		elseif ($x=='06')return 'Juni';
		elseif ($x=='07')return 'Juli';
		elseif ($x=='08')return 'Agustus';
		elseif ($x=='09')return 'September';
		elseif ($x=='10')return 'Oktober';
		elseif ($x=='11')return 'Nopember';
		elseif ($x=='12')return 'Desember';
		else return '';
	}
	
	public function suratRKO($param)
	{
		$rows = DB::select("
			SELECT DISTINCT
a.id,a.id_unit,m.is_sekre,n.nama AS nama_ppk,n.nip AS nip_ppk,o.nama AS nama_pk2,o.nip AS nip_pk2,
DATE_FORMAT(tanggal1,'%d %M %Y')tanggal1,DATE_FORMAT(tanggal2,'%d %M %Y')tanggal2,periode1,a.thang,IFNULL(c.nmsatker,'#N/A')nmsatker,IFNULL(d.nmkabkota,'#N/A')nmkabkota,IFNULL(e.nmprogram,'#N/A')nmprogram,
			IFNULL(f.nmgiat,'#N/A')nmgiat,IFNULL(g.nmoutput,'#N/A')nmoutput,IFNULL(h.ursoutput,'#N/A')ursoutput,IFNULL(i.urkmpnen,'#N/A')urkmpnen,
			IFNULL(j.urskmpnen,'#N/A')urskmpnen,a.kddept,a.kdunit,b.kdprogram,b.kdgiat,b.kdoutput,b.kdsoutput,b.kdkmpnen,b.kdskmpnen,b.nilai,a.nip_pk1,k.nama,
			DATE_FORMAT(NOW(),'%d %M %Y')skrg,l.nama AS nama_user,l.nip AS nip_user FROM
			(SELECT id,kddept,kdunit,tanggal1,tanggal2,kdlokasi,kdkabkota,periode1,thang,kdsatker,nip_pk1,id_user,id_unit,kdppk FROM d_rko WHERE id=?)a
			LEFT OUTER JOIN
			(SELECT id_rko,kdprogram,kdgiat,kdoutput,kdsoutput,kdkmpnen,kdskmpnen,SUM(nilai)nilai FROM(
			SELECT id_rko,kdprogram,kdgiat,kdoutput,kdsoutput,kdkmpnen,kdskmpnen,nilai FROM d_rko_pagu1 WHERE id_rko=?
			UNION ALL
			SELECT id_rko,kdprogram,kdgiat,kdoutput,kdsoutput,kdkmpnen,kdskmpnen,nilai FROM d_rko_pagu2 WHERE id_rko=?)X
			GROUP BY id_rko,kdprogram,kdgiat,kdoutput,kdsoutput,kdkmpnen,kdskmpnen)b
			ON a.id=b.id_rko
			LEFT OUTER JOIN t_satker c
			ON a.kdsatker=c.kdsatker
			LEFT OUTER JOIN t_kabkota d
			ON a.kdlokasi=d.kdlokasi AND a.kdkabkota=d.kdkabkota
			LEFT OUTER JOIN t_program e
			ON a.kddept=e.kddept AND b.kdprogram=e.kdprogram
			LEFT OUTER JOIN t_giat f
			ON b.kdgiat=f.kdgiat AND b.kdprogram=f.kdprogram
			LEFT OUTER JOIN t_output g
			ON g.kdoutput=b.kdoutput AND b.kdgiat=g.kdgiat 
			LEFT OUTER JOIN t_soutput h
			ON b.kdsoutput=h.kdsoutput AND b.kdoutput=h.kdoutput AND b.kdgiat=h.kdgiat AND b.kdprogram=h.kdprogram
			LEFT OUTER JOIN t_kmpnen i
			ON b.kdkmpnen=i.kdkmpnen AND b.kdsoutput=i.kdsoutput AND b.kdoutput=i.kdoutput AND b.kdgiat=i.kdgiat AND b.kdprogram=i.kdprogram
			LEFT OUTER JOIN t_skmpnen j
			ON b.kdskmpnen=j.kdskmpnen AND b.kdkmpnen=j.kdkmpnen AND b.kdsoutput=j.kdsoutput AND b.kdoutput=j.kdoutput AND b.kdgiat=j.kdgiat AND b.kdprogram=j.kdprogram
			LEFT OUTER JOIN t_pegawai k
			ON a.nip_pk1=k.nip
			LEFT OUTER JOIN t_user l
			ON a.id_user=l.id
			LEFT OUTER JOIN t_unit_instansi m
			ON a.id_unit=m.id_unit
			LEFT OUTER JOIN(
			     SELECT kdsatker,kdppk,nama,nip
                             FROM t_user
                             WHERE kdlevel='06' AND aktif='1'
			) n ON(a.kdsatker=n.kdsatker AND a.kdppk=n.kdppk)
			LEFT OUTER JOIN(
			     SELECT kdsatker,kdppk,id_unit,nama,nip
                             FROM t_user
                             WHERE kdlevel='03' AND aktif='1'
			) o ON(a.kdsatker=o.kdsatker AND a.kdppk=o.kdppk AND LEFT(a.id_unit,8)=LEFT(o.id_unit,8))
		", [
			$param,$param,$param
		]);
		if(count($rows) == 0) {
			return 'tidak ada data';
		}
		$id_unit = substr(Session::get('id_unit'),0,7);
		if(strlen($id_unit) > 5) {
			$id_unit = $id_unit;
		} else {
			$id_unit = 'H0.01.00';
		}
		$thang = Session::get('thang');
		//jika sekretariat
		if($rows[0]->is_sekre=='1'){
			
			$html_out = '
			<style>
				#tbl-content, th {
					border: 0px solid black;
					border-collapse: collapse;
					font-size:70%;
					vertical-align: middle;
				}
				#tbl-content, td {
					border: 0px solid black;
					border-collapse: collapse;
					vertical-align: top;
				}
			</style>
			<table style="border:0px solid #000;border-collapse:collapse; width:100%;" cellspacing="0" cellpadding="0">';
		$inputPath='template/img/tut wuri handayani1.jpeg';
		$html_out .= '
				
					<tr>
						<td rowspan="5"><img style="width:100px;height:100px;" src="'.$inputPath.'"/></td>
						<td style="text-align:center;font-size:14px;">KEMENTERIAN PENDIDIKAN DAN KEBUDAYAAN</td>
					</tr>
					<tr>
						<td style="text-align:center;font-size:12px;font-weight:bold;">'.$this->namaUnit()['nama_unit'].'</td>
					</tr>
					<tr>
						<td style="text-align:center;font-size:12px">Jalan Jenderal Sudirman Senayan, Kotak Pos 4104, Jakarta 1204</td>
					</tr>
					<tr>
						<td style="text-align:center;font-size:12px">Telp. (021)5731665 (3 Saluran) 5737102, 5733129, 5736365, 5731177</td>
					</tr>
					<tr>
						<td style="text-align:center;font-size:12px">Faximili: (021) 5721243, 5721244, 5741664</td>
					</tr>
					
				
				</table>
				<hr>';
			$html_out .= '
				<table style="border:0px solid #000;border-collapse:collapse; width:100%;font-size:12px">
					<tr>
						<td style="text-align:left;">Nomor</td>
						<td style="text-align:left;">:</td>
						<td style="text-align:left;">'.$rows[0]->id.'/'.$id_unit.'/TL/'.$thang.'</td>
						<td style="text-align:left;"></td>
						<td style="text-align:right;">'.$rows[0]->skrg.'</td>
					</tr>
					<tr>
						<td style="text-align:left;">Lampiran</td>
						<td style="text-align:left;">:</td>
						<td style="text-align:left;">1 (satu) Berkas</td>
						<td style="text-align:left;"></td>
						<td style="text-align:right;"></td>
					</tr>
					<tr>
						<td style="text-align:left;">Perihal</td>
						<td style="text-align:left;">:</td>
						<td style="text-align:left;">Permohonan Biaya Kegiatan</td>
						<td style="text-align:left;"></td>
						<td style="text-align:right;"></td>
					</tr>
					
				</table>';
			$html_out .= '
			<br>
			<p style="font-size:12px;margin:0;padding:0;">Yth.</p>
			<p style="font-size:12px;margin:0;padding:0;">Pejabat Pembuat Komitmen/PPK Sekretariat</p>
			<p style="font-size:12px;margin:0;padding:0;">di Jakarta</p>
			<br>
			<p style="font-size:12px">Bersama ini kami mengajukan permohonan biaya kegiatan pada '.$this->namaUnit()['nama_unit'].' yang 
			akan dilaksanakan bulan '.$this->bulan($rows[0]->periode1).' '.$rows[0]->thang.' dengan perincian sebagai berikut.</p>
			';
			$html_out .= '
				<table style="border:0px solid #000;border-collapse:collapse; width:100%;font-size:12px">
					<tr>
						<td style="text-align:left;">Nama Program</td>
						<td style="text-align:left;">:</td>
						<td style="text-align:left;">'.$rows[0]->nmprogram.' ('.$rows[0]->kddept.'.'.$rows[0]->kdunit.'.'.$rows[0]->kdprogram.')</td>
					</tr>
					<tr>
						<td style="text-align:left;">Kegiatan</td>
						<td style="text-align:left;">:</td>
						<td style="text-align:left;">'.$rows[0]->nmgiat.' ('.$rows[0]->kdgiat.')</td>
					</tr>
					<tr>
						<td style="text-align:left;">Output</td>
						<td style="text-align:left;">:</td>
						<td style="text-align:left;">'.$this->refUraianOutput($rows[0]->kdgiat, $rows[0]->kdoutput).' ('.$rows[0]->kdgiat.'.'.$rows[0]->kdoutput.')</td>
					</tr>
					<tr>
						<td style="text-align:left;">Tahap SubOutput</td>
						<td style="text-align:left;">:</td>
						<td style="text-align:left;">'.$this->refUraianSOutput($rows[0]->kdgiat, $rows[0]->kdoutput, $rows[0]->kdsoutput).' ('.$rows[0]->kdgiat.'.'.$rows[0]->kdoutput.'.'.$rows[0]->kdsoutput.')</td>
					</tr>
					<tr>
						<td style="text-align:left;">Tahap Kegiatan</td>
						<td style="text-align:left;">:</td>
						<td style="text-align:left;">'.$this->refUraianSkmpnen($rows[0]->kdgiat, $rows[0]->kdoutput, $rows[0]->kdsoutput, $rows[0]->kdkmpnen, $rows[0]->kdskmpnen).' ('.$rows[0]->kdgiat.'.'.$rows[0]->kdoutput.'.'.$rows[0]->kdsoutput.'.'.$rows[0]->kdskmpnen.')</td>
					</tr>
					<tr>
						<td style="text-align:left;">Tanggal / Tempat</td>
						<td style="text-align:left;">:</td>
						<td style="text-align:left;">'.$rows[0]->tanggal1.' sd '.$rows[0]->tanggal2.' / '.$rows[0]->nmkabkota.'</td>
					</tr>
					<tr>
						<td style="text-align:left;">Jumlah Dana</td>
						<td style="text-align:left;">:</td>
						<td style="text-align:left;">Rp. '.number_format($rows[0]->nilai, 0, ",", ".").'<br>'.
						$this->angkaToTerbilang($rows[0]->nilai).' Rupiah</td>
					</tr>
				</table>
				';
		$html_out .='
			<p style="font-size:12px">Atas perhatian dan kerjasamanya, kami mengucapkan terima kasih.</p>
			<br>
				';
		
		$html_out .= '
			<p style="font-size:12px;margin-left:30em;padding:0;">Koordinator Pelaksana Kegiatan</p>
			<br><br>
			<p style="font-size:12px;margin-left:30em;padding:0;">'.$rows[0]->nama_pk2.'</p>
			<p style="font-size:12px;margin-left:30em;padding:0;">NIP. '.$rows[0]->nip_pk2.'</p>
			';		
		$html_out .= '
			<p style="font-size:12px;margin:0;padding:0;">Tembusan:</p>
			<p style="font-size:12px;margin:0;padding:0;">1. Sekretaris Balitbang (sebagai Laporan)</p>
			<!--<p style="font-size:12px;margin:0;padding:0;">2. Kepala Bagian Keuagan</p>
			<p style="font-size:12px;margin:0;padding:0;">3. Kepala '.$rows[0]->nmsatker.'</p>
			<p style="font-size:12px;margin:0;padding:0;">4. Bendahara Pembantu Pengeluaran/BPP</p>
			<p style="font-size:12px;margin:0;padding:0;">5. Arsip</p>-->
			';
			
		}
		else{
			
			$html_out = '
			<style>
				#tbl-content, th {
					border: 0px solid black;
					border-collapse: collapse;
					font-size:70%;
					vertical-align: middle;
				}
				#tbl-content, td {
					border: 0px solid black;
					border-collapse: collapse;
					vertical-align: top;
				}
			</style>
			<table style="border:0px solid #000;border-collapse:collapse; width:100%;" cellspacing="0" cellpadding="0">';
		$inputPath='template/img/tut wuri handayani1.jpeg';
		$html_out .= '
				
					<tr>
						<td rowspan="5"><img style="width:100px;height:100px;" src="'.$inputPath.'"/></td>
						<td style="text-align:center;font-size:14px;">KEMENTERIAN PENDIDIKAN DAN KEBUDAYAAN</td>
					</tr>
					<tr>
						<td style="text-align:center;font-size:12px;font-weight:bold;">BADAN PENELITIAN DAN PENGEMBANGAN</td>
					</tr>
					<tr>
						<td style="text-align:center;font-size:12px">Jalan Jenderal Sudirman Senayan, Kotak Pos 4104, Jakarta 1204</td>
					</tr>
					<tr>
						<td style="text-align:center;font-size:12px">Telp. (021)5731665 (3 Saluran) 5737102, 5733129, 5736365, 5731177</td>
					</tr>
					<tr>
						<td style="text-align:center;font-size:12px">Faximili: (021) 5721243, 5721244, 5741664</td>
					</tr>
					
				
				</table>
				<hr>';
			$html_out .= '
				<table style="border:0px solid #000;border-collapse:collapse; width:100%;font-size:12px">
					<tr>
						<td style="text-align:left;">Nomor</td>
						<td style="text-align:left;">:</td>
						<td style="text-align:left;">'.$rows[0]->id.'/H1.3/TL/2017</td>
						<td style="text-align:left;"></td>
						<td style="text-align:right;">'.$rows[0]->skrg.'</td>
					</tr>
					<tr>
						<td style="text-align:left;">Lampiran</td>
						<td style="text-align:left;">:</td>
						<td style="text-align:left;">1 (satu) Berkas</td>
						<td style="text-align:left;"></td>
						<td style="text-align:right;"></td>
					</tr>
					<tr>
						<td style="text-align:left;">Perihal</td>
						<td style="text-align:left;">:</td>
						<td style="text-align:left;">Permohonan Biaya Kegiatan</td>
						<td style="text-align:left;"></td>
						<td style="text-align:right;"></td>
					</tr>
					
				</table>';
			$html_out .= '
			<br>
			<p style="font-size:12px;margin:0;padding:0;">Yth.</p>
			<p style="font-size:12px;margin:0;padding:0;">Kuasa Pengguna Anggaran</p>
			<p style="font-size:12px;margin:0;padding:0;">di Jakarta</p>
			<br>
			<p style="font-size:12px">Bersama ini kami mengajukan permohonan biaya kegiatan pada '.$rows[0]->nmsatker.' yang 
			akan dilaksanakan bulan '.$this->bulan($rows[0]->periode1).' '.$rows[0]->thang.' dengan perincian sebagai berikut.</p>
			';
			$html_out .= '
				<table style="border:0px solid #000;border-collapse:collapse; width:100%;font-size:12px">
					<tr>
						<td style="text-align:left;">Nama Program</td>
						<td style="text-align:left;">:</td>
						<td style="text-align:left;">'.$rows[0]->nmprogram.' ('.$rows[0]->kddept.'.'.$rows[0]->kdunit.'.'.$rows[0]->kdprogram.')</td>
					</tr>
					<tr>
						<td style="text-align:left;">Kegiatan</td>
						<td style="text-align:left;">:</td>
						<td style="text-align:left;">'.$rows[0]->nmgiat.' ('.$rows[0]->kdgiat.')</td>
					</tr>
					<tr>
						<td style="text-align:left;">Output</td>
						<td style="text-align:left;">:</td>
						<td style="text-align:left;">'.$this->refUraianOutput($rows[0]->kdgiat, $rows[0]->kdoutput).' ('.$rows[0]->kdgiat.'.'.$rows[0]->kdoutput.')</td>
					</tr>
					<tr>
						<td style="text-align:left;">Tahap SubOutput</td>
						<td style="text-align:left;">:</td>
						<td style="text-align:left;">'.$this->refUraianSOutput($rows[0]->kdgiat, $rows[0]->kdoutput, $rows[0]->kdsoutput).' ('.$rows[0]->kdgiat.'.'.$rows[0]->kdoutput.'.'.$rows[0]->kdsoutput.')</td>
					</tr>
					<tr>
						<td style="text-align:left;">Tahap Kegiatan</td>
						<td style="text-align:left;">:</td>
						<td style="text-align:left;">'.$this->refUraianSkmpnen($rows[0]->kdgiat, $rows[0]->kdoutput, $rows[0]->kdsoutput, $rows[0]->kdkmpnen, $rows[0]->kdskmpnen).' ('.$rows[0]->kdgiat.'.'.$rows[0]->kdoutput.'.'.$rows[0]->kdsoutput.'.'.$rows[0]->kdskmpnen.')</td>
					</tr>
					<tr>
						<td style="text-align:left;">Tanggal / Tempat</td>
						<td style="text-align:left;">:</td>
						<td style="text-align:left;">'.$rows[0]->tanggal1.' sd '.$rows[0]->tanggal2.' / '.$rows[0]->nmkabkota.'</td>
					</tr>
					<tr>
						<td style="text-align:left;">Jumlah Dana</td>
						<td style="text-align:left;">:</td>
						<td style="text-align:left;">Rp. '.number_format($rows[0]->nilai, 0, ",", ".").'<br>'.
						$this->angkaToTerbilang($rows[0]->nilai).' Rupiah</td>
					</tr>
				</table>
				';
		$html_out .='
			<p style="font-size:12px">Atas perhatian dan kerjasamanya, kami mengucapkan terima kasih.</p>
			<br>
				';
		
		$html_out .= '
			<p style="font-size:12px;margin-left:30em;padding:0;">Pejabat Pembuat Komitmen</p>
			<br><br>
			<p style="font-size:12px;margin-left:30em;padding:0;">'.$rows[0]->nama_ppk.'</p>
			<p style="font-size:12px;margin-left:30em;padding:0;">NIP. '.$rows[0]->nip_ppk.'</p>
			';
			
		}
		
		//render PDF
		$mpdf = new mPDF("en", "A4", "15");
		$mpdf->AddPage('P');
		$mpdf->writeHTML($html_out);
		$mpdf->Output('Surat.pdf', 'I');
		exit; 
	
	}
	
	public function cek($param)
	{
		$rows = DB::select("
			select  DATE_FORMAT(now(),'%d %M %Y') skrg,a.thang,a.kdsatker,a.nocek,IFNULL(DATE_FORMAT(a.tgcek,'%d-%m-%Y'),'N/A') tgcek,
			b.nmsatker,e.nmbpp,f.nama namabp, f.nip nipbp,g.nama namabpp, g.nip nipbpp, h.nama namapk1, h.nip nippk1,
			if(a.kdcek='01',sum(d.nilai),if(a.kdcek='02',i.nilai,0)) as niltot
			from d_cek a
			left outer join t_satker b
			on a.kdsatker=b.kdsatker
			left outer join d_spby c
			on a.nocek=c.nocek
			left outer join (
				SELECT	a.id,
						a.tgrko,
						a.nospby,
						SUM(b.nilai) AS nilai
				FROM d_rko a
				LEFT OUTER JOIN(
					SELECT	id_rko,
						SUM(nilai) AS nilai
					FROM d_rko_pagu2
					GROUP BY id_rko			
				) b ON(a.id=b.id_rko)
				WHERE  a.kddept= '".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' 
				GROUP BY a.id,a.tgrko,a.nospby
			)d
			on c.nospby=d.nospby
			left outer join t_bpp e
			on a.kdbpp=e.kdbpp
			left outer join (select kdsatker,nama,nip from t_user where kdlevel='04') f
			on a.kdsatker=f.kdsatker
			left outer join (select kdsatker,nama,nip,kdbpp from t_user where kdlevel='05') g
			on a.kdsatker=g.kdsatker and a.kdbpp=g.kdbpp
			left outer join (select kdsatker,nama,nip from t_user where kdlevel='02') h
			on a.kdsatker=h.kdsatker
			left outer join(
				SELECT	a.kdsatker,
						a.thang,
						a.nospp,
						a.nocek_ls,
						IFNULL(b.nilai,0) AS nilai
					FROM d_spp a
					LEFT OUTER JOIN(
						SELECT	a.kdsatker,
							a.thang,
							a.nospp_ls,
							SUM(b.nilai) AS nilai
						FROM d_rko a
						LEFT OUTER JOIN d_rko_pagu2 b ON(a.id=b.id_rko)
						GROUP BY a.kdsatker,a.thang,a.nospp_ls
					) b ON(a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.nospp=b.nospp_ls)
			) i on(a.kdsatker=i.kdsatker and a.thang=i.thang and a.nocek=i.nocek_ls)
			WHERE A.nocek=? and a.kddept= '".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."'
			group by a.thang,a.kdsatker,a.nocek,a.tgcek, a.nilai,b.nmsatker,e.nmbpp,f.nama, f.nip
		", [
			$param
		]);
		
		$rows2 = DB::select("
			select
					if(a.kdcek='01',c.nospby,'-') as nospby,
					if(a.kdcek='01',d.id,e.id) as id,
					if(a.kdcek='01',IFNULL(DATE_FORMAT(d.tgrko,'%d-%m-%Y'),'N/A'),IFNULL(DATE_FORMAT(e.tgrko,'%d-%m-%Y'),'N/A')) as tgrko,
					if(a.kdcek='01',d.nilai,e.nilai) as niltot
			from d_cek a
			left outer join d_spby c on a.nocek=c.nocek
			left outer join (
				SELECT	a.id,
						a.tgrko,
						a.nospby,
						SUM(b.nilai) AS nilai
				FROM d_rko a
				LEFT OUTER JOIN(
					SELECT	id_rko,
							SUM(nilai) AS nilai
					FROM d_rko_pagu2
					GROUP BY id_rko
				) b ON(a.id=b.id_rko)
				LEFT OUTER JOIN(
					select	b.id_rko,
							b.nourut
					from(
						SELECT	id_rko,
								max(id) as max_id
						FROM d_rko_status
						GROUP BY id_rko
					) a
					left outer join d_rko_status b on(a.max_id=b.id)
				) c ON(a.id=c.id_rko)
				WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."'
				GROUP BY a.id,a.tgrko,a.nospby
			)d on c.nospby=d.nospby
			left outer join (
				SELECT	a.kdsatker,
					a.thang,
					a.nospp,
					a.nocek_ls,
					b.id,
					b.tgrko,
					IFNULL(b.nilai,0) AS nilai
				FROM d_spp a
				LEFT OUTER JOIN(
					SELECT	
						a.id,
						a.tgrko,
						a.kdsatker,
						a.thang,
						a.nospp_ls,
						IFNULL(b.nilai,0) AS nilai
					FROM d_rko a
					LEFT OUTER JOIN(
						SELECT	id_rko,
								SUM(nilai) AS nilai
						FROM d_rko_pagu2
						GROUP BY id_rko
					) b ON(a.id=b.id_rko)
				) b ON(a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.nospp=b.nospp_ls)
			) e on(a.kdsatker=e.kdsatker and a.thang=e.thang and a.nocek=e.nocek_ls)
			WHERE A.nocek=? and a.kddept= '".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."'  
		", [
			$param
		]);
		
		if(count($rows) == 0 || count($rows2) == 0) {
			return 'tidak ada data'.session('kddept').session('kdunit').session('kdsatker').session('tahun');
		}
		
		$html_out = '
			<style>
				#tbl-content, th {
					border: 0px solid black;
					border-collapse: collapse;
					font-size:70%;
					vertical-align: middle;
				}
				#tbl-content, td {
					border: 0px solid black;
					border-collapse: collapse;
					vertical-align: top;
				}
			</style>
			<table style="border:0px solid #000;border-collapse:collapse; width:100%;" cellspacing="0" cellpadding="0">
				
					<tr>
						<td colspan="3" style="text-align:center;font-size:14px;font-weight:bold;">UANG MUKA KERJA</td>						
					</tr>
					<tr>
						<td width="60%"></td>
						<td style="text-align:left;font-size:8px;">Tahun Anggaran:</td>
						<td style="text-align:left;font-size:8px;">'.$rows[0]->thang.'</td>
					</tr>
					<tr>
						<td width="60%"></td>
						<td style="text-align:left;font-size:8px;">No Bukti MAK:</td>
						<td style="text-align:left;font-size:8px;"></td>
					</tr>
					
				</table>
				<br>
				<table style="border:0px solid #000;border-collapse:collapse; width:100%;" cellspacing="0" cellpadding="0">
				
					<tr>
						<td style="text-align:left;font-size:10px;padding-bottom:1em;" width="20%">SUDAH DITERIMA DARI</td>
						<td style="text-align:left;font-size:10px;" width="5%">:</td>
						<td style="text-align:left;font-size:10px;">KUASA PENGGUNA ANGGARAN '.$rows[0]->nmsatker.'</td>
					</tr>
					
					<tr>
					
						<td style="text-align:left;font-size:10px;padding-bottom:1em;"  width="20%">JUMLAH UANG</td>
						<td style="text-align:left;font-size:10px;" width="5%">:</td>
						<td style="text-align:left;font-size:10px;bold:bold;">Rp. '.number_format($rows[0]->niltot, 0, ",", ".").'</td>
					</tr>
					<tr>
						<td style="text-align:left;font-size:10px;padding-bottom:1em;" width="20%">TERBILANG</td>
						<td style="text-align:left;font-size:10px;" width="5%">:</td>
						<td style="text-align:left;font-size:10px;bold:bold;">'.$this->angkaToTerbilang($rows[0]->niltot).' Rupiah</td>
					<tr>
						<td style="text-align:left;font-size:10px;" width="20%">UNTUK PEMBAYARAN</td>
						<td style="text-align:left;font-size:10px;" width="5%">:</td>
						<td style="text-align:left;font-size:10px;">UMK dengan keterangan sbb:</td>
					
				</table>';
				
				$html_out .= '
				<table style="border:1px solid #000;border-collapse:collapse; width:100%;margin-left:8em;">
					<thead>
						<tr style="border:1px solid #000;border-collapse:collapse;">
							<th style="text-align:center;font-size: 11px;">No</th>
							<th style="text-align:center;font-size: 11px;">KETERANGAN</th>
							<th style="text-align:center;font-size: 11px;">NO SPBY</th>
							<th style="text-align:center;font-size: 11px;">JUMLAH</th>							
						</tr>
						</thead>
					<tbody>';
		$i=1;			
		foreach($rows2 as $row) {
			$html_out .= '
					<tr style="border:1px solid #000;border-collapse:collapse;">
						<td style=" solid #000;border-collapse:collapse; text-align:center; font-size: 11px;">'.$i.'.</td>
						<td style=" solid #000;border-collapse:collapse; text-align:left; font-size: 11px;">RKO Nomor '.$row->id.' tanggal '.$row->tgrko.'</td>
						<td style=" solid #000;border-collapse:collapse; text-align:center; font-size: 11px;">'.$row->nospby.'</td>
						<td style=" solid #000;border-collapse:collapse; text-align:right; font-size: 11px;">'.number_format($row->niltot, 0, ",", ".").'</td>

					</tr>';
			$i++;
		} 
		
		$html_out .='
					</tbody>
					<thead>
						<tr style="border:1px solid #000;border-collapse:collapse;">
							<th></th>
							<th style="text-align:left;font-size: 11px;" colspan="2">Total UMK yang diterima:</th>
							<th style="text-align:right;font-size: 11px;">'.number_format($rows[0]->niltot, 0, ",", ".").'</th>							
						</tr>
						<tr>
							<th></th>
							<th style="text-align:left;font-size: 11px;" >Pembayaran melalui cek No:</th>
							<th style="text-align:right;font-size: 11px;">'.$rows[0]->nocek.'</th>
							<th></th>							
						</tr>
						</thead>
			</table>
			<br>
			<br>			
			';
			
			$html_out .='
			<table style="border:0px solid #000;border-collapse:collapse; width:100%;" cellspacing="0" cellpadding="0">
				<tr >
					<td colspan="3"></td>
					<td style="text-align:center;font-size: 11px;">Jakarta, '.$rows[0]->skrg.'</td>							
				</tr>
				<tr>
					<td colspan="2" style="text-align:center;font-size: 11px;border:1px solid #000;border-collapse:collapse;">
					MENGETAHUI/MENYETUJUI<br>Paraf dan Tanggal</td>
					<td></td>
					<td style="text-align:left;font-size: 11px;"><br>Pengambil Uang Muka,</td>
				</tr>
				<tr>
					<td width="25%" style="text-align:center;font-size: 11px;border:1px solid #000;border-collapse:collapse;">
					Atasan Langsung<br><br><br><br><br>'.$rows[0]->namapk1.'<br>NIP.'.$rows[0]->nippk1.'</td>
					<td width="25%" style="text-align:center;font-size: 11px;border:1px solid #000;border-collapse:collapse;">
					Bendahara Pengeluaran<br><br><br><br><br>'.$rows[0]->namabp.'<br>NIP.'.$rows[0]->nipbp.'</td>
					<td width="20%"></td>
					<td width="30%" style="text-align:center;font-size: 11px;">
					Bendahara Pengeluaran Pembantu<br><br><br><br><br>'.$rows[0]->namabpp.'<br>NIP.'.$rows[0]->nipbpp.'</td>
				</tr>
			</table>
			';
					//render PDF
		$mpdf = new mPDF("en", "A4", "15");
		$mpdf->AddPage('P');
		$mpdf->writeHTML($html_out);
		$mpdf->Output('Cek_UMK.pdf', 'D');
		exit; 
	}
	
	/**
	 * description 
	 */
	public function refUraianOutput($kdgiat, $kdoutput)
	{
		$thang = Session::get('tahun');
		$kdsatker = Session::get('kdsatker');
		
		$row = DB::select("
			select trim(kode) as kode, trim(uraian) as uraian 
			  from d_pagu
			 where     kdsatker='".$kdsatker."' 
			       and thang=".$thang." 
			       and substr(kode,1,8) = concat('".$kdgiat."', '-', '".$kdoutput."');
		");
		
		if(count($row) == 0) {
			return '#N/A';
		} else {
			return $row[0]->uraian;
		}
	}
	
	/**
	 * description 
	 */
	public function refUraianSOutput($kdgiat, $kdoutput, $kdsoutput)
	{
		$thang = Session::get('tahun');
		$kdsatker = Session::get('kdsatker');
		
		$row = DB::select("
			select trim(kode) as kode, trim(uraian) as uraian 
			  from d_pagu
			 where     kdsatker='".$kdsatker."' 
			       and thang=".$thang." 
			       and trim(kode) = concat('".$kdgiat."', '-', '".$kdoutput."', '-', '".$kdsoutput."');
		");
		
		if(count($row) == 0) {
			return '#N/A';
		} else {
			return $row[0]->uraian;
		}
	}
	
	/**
	 * description 
	 */
	public function refUraianSkmpnen($kdgiat, $kdoutput, $kdsoutput, $kdkmpnen, $kdskmpnen)
	{
		$thang = Session::get('tahun');
		$kdsatker = Session::get('kdsatker');
		
		$rows = DB::select("
			select trim(kode) as kode, trim(uraian) as uraian, a.* from d_pagu a
			 where thang='".$thang."' 
				   and kdsatker='".$kdsatker."'
				   and kdgiat='".$kdgiat."' 
				   and kdoutput='".$kdoutput."' 
				   and kdsoutput='".$kdsoutput."' 
				   and kdkmpnen='".$kdkmpnen."' 
				   and kdskmpnen='".$kdskmpnen."' 
				   and length(trim(kode))=5
		");
		
		if(count($rows) == 0) {
			return '#N/A';
		} else {
			return $rows[0]->uraian;
		}
	}
	
	/**
	 * description 
	 */
	public function namaUnit()
	{
		$rows = DB::select("
			SELECT uraian AS nama_unit, IFNULL(nodok, '-') AS nomor_dipa, thang
			  FROM d_pagu
			 WHERE     lvl=0 
				   AND kdsatker = ?
				   AND kddept = ? 
				   AND kdunit = ?
				   AND thang = ?
		",
		[Session::get('kdsatker'),
		 Session::get('kddept'),
		 Session::get('kdunit'),
		 Session::get('tahun')]);
		 
		if(count($rows)>0) {
			$result = array( 'nama_unit'=>$rows[0]->nama_unit, 'nomor_sipa'=>$rows[0]->nomor_dipa, );
		} else {
			$result = array( 'nama_unit'=>'BADAN PENELITIAN DAN PENGEMBANGAN', 'nomor_dipa'=>'-', );
		}
		
		return $result;
	}
}
