<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransaksiKuitansiController extends Controller {

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
	public function index(Request $request)
	{
		try{
			$aColumns = array('id','id_rko','tgkuitansi','kdakun','uraiben','untuk','tgbyr','totnilmak','nmdok','tgl_update','status');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "	SELECT	a.id,
								a.id_rko,
								DATE_FORMAT(a.tgkuitansi,'%d-%m-%Y') AS tgkuitansi,
								CONCAT(a.kdprogram,'.',a.kdoutput,'.',a.kdsoutput,'.',a.kdkmpnen,'.',a.kdskmpnen,'.',a.kdmak) AS kdakun,
								a.uraiben,
								a.untuk,
								DATE_FORMAT(a.tgbyr,'%d-%m-%Y') AS tgbyr,
								a.totnilmak,
								ifnull(d.nmdok,'N/A') as nmdok,
								DATE_FORMAT(a.created_at,'%d-%m-%Y') AS tgl_update,
								c.status
						FROM d_transaksi a
						LEFT OUTER JOIN(
							SELECT	a.id_trans,
									b.nourut
							FROM(
								SELECT	id_trans,
										max(id) as max_id
								FROM d_transaksi_status
								GROUP BY id_trans
							) a
							LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
						) b on(a.id=b.id_trans)
						LEFT OUTER JOIN t_alur_status c ON(a.kdalur=c.kdalur AND b.nourut=c.nourut)
						LEFT OUTER JOIN(
							SELECT	a.id_trans,
									GROUP_CONCAT('<li><a target=\"_blank\" href=\"transaksi/kuitansi/dok/',a.nmfile,'/download\">',b.nmdok,'</li>' ORDER BY a.id SEPARATOR '') AS nmdok
							FROM d_transaksi_dok a
							LEFT OUTER JOIN t_dok b ON(a.id_dok=b.id)
							GROUP BY a.id_trans
						) d on(a.id=d.id_trans)
						WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."'
						ORDER BY a.id DESC";
			
			/*
			 * Paging
			 */
			$sLimit = "";
			$iDisplayStart=$request->input('iDisplayStart');
			$iDisplayLength=$request->input('iDisplayLength');
			if ( isset( $iDisplayStart ) &&  $iDisplayLength != '-1' ) 
			{
				 $sLimit = "LIMIT ".intval( $request->input('iDisplayStart') ).", ".
					intval( $request->input('iDisplayLength') );
			}
			 
			 
			/*
			 * Ordering
			 */
			$sOrder = "";
			$iSortCol_0=$request->input('iSortCol_0');
			if ( isset($iSortCol_0  ) )
			{
				$sOrder = " ORDER BY ".$aColumns[ intval( $request->input('iSortCol_0') ) ]."
							".($request->input('sSortDir_0')==='asc' ? 'asc' : 'desc') ." ";
			}
			 
			 
			/*
			 * Filtering
			 */
			$sWhere = "";
			$sSearch=$request->input('sSearch');
			if ( isset($sSearch) && $sSearch != "" )
			{
				$sWhere = "WHERE (";
				for ( $i=0 ; $i<count($aColumns) ; $i++ )
				{
				 $bSearchable_i=$request->input('bSearchable_'.$i);
					if ( isset($bSearchable_i) && $bSearchable_i == "true" )
					{
						$sWhere .= $aColumns[$i]." LIKE '%".( $request->input('sSearch') )."%' OR ";           
					}
				}
				$sWhere = substr_replace( $sWhere, "", -3 );
				$sWhere .= ')';
			}
			 
			/* Individual column filtering */
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if ( isset($bSearchable_i) && $bSearchable_i == "true" && $request->input('sSearch_'.$i) != '' )
				{
					if ( $sWhere == "" )
					{
						$sWhere = "WHERE ";
					}
					else
					{
						$sWhere .= " AND ";
					}
					$sWhere .= $aColumns[$i]." LIKE '%".($request->input('sSearch_'.$i))."%' ";
				}
			}

			/* Data set length after filtering */
			$iFilteredTotal = 0;
			$rows = DB::select("
				SELECT COUNT(*) as jumlah FROM (".$sTable.") a
			");
			$result = (array)$rows[0];
			if($result){
				$iFilteredTotal = $result['jumlah'];
			}
			
			/* Total data set length */
			$iTotal = 0;
			$rows = DB::select("
				SELECT COUNT(".$sIndexColumn.") as jumlah FROM (".$sTable.") a
			");
			$result = (array)$rows[0];
			if($result){
				$iTotal = $result['jumlah'];
			}
		   
			/*
			 * Format Output
			 */
			$output = array(
			"sEcho" => intval($request->input('sEcho')),
				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
			);

			/*
			 * SQL queries
			 */
			$sQuery = "
				SELECT SQL_CALC_FOUND_ROWS no,".str_replace(" , ", " ", implode(", ", $aColumns))."
				FROM   (
					SELECT @rownum:=@rownum+1 as no,a.*
					FROM(".
						$sTable
					.") a,
					(SELECT @rownum:=0) as r
				) a
				$sWhere
				$sOrder
				$sLimit
			";
			
			$rows = DB::select($sQuery);
			
			foreach( $rows as $row )
			{			
				$aksi='<center style="width:100px;">
							<div class="dropdown pull-right">
								<button class="btn btn-success btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-print"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="transaksi/kuitansi/'.$row->id.'/download" target="_blank" title="Cetak data?">Cetak Kuitansi</a></li>
								</ul>
							</div>
							<div class="dropdown pull-right">
								<button class="btn btn-danger btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-plus"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="javascript:;" id="'.$row->id.'" title="Upload data?" class="upload">Upload Dokumen</a></li>
								</ul>
							</div>
							<div class="dropdown pull-right" style="height:1.5vw !important;">
								<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-pencil"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="javascript:;" id="'.$row->id.'" title="Ubah data?" class="ubah">Ubah Kuitansi</a></li>
									<li><a href="javascript:;" id="'.$row->id.'" title="Hapus data?" class="hapus">Hapus Kuitansi</a></li>
								</ul>
							</div>
						</center>';
							
				$output['aaData'][] = array(
					$row->id,
					$row->id_rko,
					$row->tgkuitansi,
					$row->kdakun,
					$row->uraiben,
					$row->untuk,
					$row->tgbyr,
					'<div style="text-align:right;">'.number_format($row->totnilmak).'</div>',
					$row->nmdok,
					$row->tgl_update,
					$row->status,
					$aksi
				);
			}
			
			return response()->json($output);
		}
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function monitoring(Request $request)
	{
		try{
			$aColumns = array('id','id_rko','nokuitansi','tgkuitansi','kdakun','uraiben','untuk','tgbyr','totnilmak','tgl_update','status');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "	SELECT	a.id,
								a.id_rko,
								a.nokuitansi,
								DATE_FORMAT(a.tgkuitansi,'%d-%m-%Y') AS tgkuitansi,
								CONCAT(a.kdprogram,'.',a.kdoutput,'.',a.kdsoutput,'.',a.kdkmpnen,'.',a.kdskmpnen,'.',a.kdmak) AS kdakun,
								a.uraiben,
								a.untuk,
								DATE_FORMAT(a.tgbyr,'%d-%m-%Y') AS tgbyr,
								a.totnilmak,
								DATE_FORMAT(a.created_at,'%d-%m-%Y') AS tgl_update,
								c.status
						FROM d_transaksi a
						LEFT OUTER JOIN(
							SELECT	a.id_trans,
									b.nourut
							FROM(
								SELECT	id_trans,
										max(id) as max_id
								FROM d_transaksi_status
								GROUP BY id_trans
							) a
							LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
						) b on(a.id=b.id_trans)
						LEFT OUTER JOIN t_alur_status c ON(a.kdalur=c.kdalur AND b.nourut=c.nourut)
						WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."'
						ORDER BY a.id DESC";
			
			/*
			 * Paging
			 */
			$sLimit = "";
			$iDisplayStart=$request->input('iDisplayStart');
			$iDisplayLength=$request->input('iDisplayLength');
			if ( isset( $iDisplayStart ) &&  $iDisplayLength != '-1' ) 
			{
				 $sLimit = "LIMIT ".intval( $request->input('iDisplayStart') ).", ".
					intval( $request->input('iDisplayLength') );
			}
			 
			 
			/*
			 * Ordering
			 */
			$sOrder = "";
			$iSortCol_0=$request->input('iSortCol_0');
			if ( isset($iSortCol_0  ) )
			{
				$sOrder = " ORDER BY ".$aColumns[ intval( $request->input('iSortCol_0') ) ]."
							".($request->input('sSortDir_0')==='asc' ? 'asc' : 'desc') ." ";
			}
			 
			 
			/*
			 * Filtering
			 */
			$sWhere = "";
			$sSearch=$request->input('sSearch');
			if ( isset($sSearch) && $sSearch != "" )
			{
				$sWhere = "WHERE (";
				for ( $i=0 ; $i<count($aColumns) ; $i++ )
				{
				 $bSearchable_i=$request->input('bSearchable_'.$i);
					if ( isset($bSearchable_i) && $bSearchable_i == "true" )
					{
						$sWhere .= $aColumns[$i]." LIKE '%".( $request->input('sSearch') )."%' OR ";           
					}
				}
				$sWhere = substr_replace( $sWhere, "", -3 );
				$sWhere .= ')';
			}
			 
			/* Individual column filtering */
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if ( isset($bSearchable_i) && $bSearchable_i == "true" && $request->input('sSearch_'.$i) != '' )
				{
					if ( $sWhere == "" )
					{
						$sWhere = "WHERE ";
					}
					else
					{
						$sWhere .= " AND ";
					}
					$sWhere .= $aColumns[$i]." LIKE '%".($request->input('sSearch_'.$i))."%' ";
				}
			}

			/* Data set length after filtering */
			$iFilteredTotal = 0;
			$rows = DB::select("
				SELECT COUNT(*) as jumlah FROM (".$sTable.") a
			");
			$result = (array)$rows[0];
			if($result){
				$iFilteredTotal = $result['jumlah'];
			}
			
			/* Total data set length */
			$iTotal = 0;
			$rows = DB::select("
				SELECT COUNT(".$sIndexColumn.") as jumlah FROM (".$sTable.") a
			");
			$result = (array)$rows[0];
			if($result){
				$iTotal = $result['jumlah'];
			}
		   
			/*
			 * Format Output
			 */
			$output = array(
			"sEcho" => intval($request->input('sEcho')),
				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
			);

			/*
			 * SQL queries
			 */
			$sQuery = "
				SELECT SQL_CALC_FOUND_ROWS no,".str_replace(" , ", " ", implode(", ", $aColumns))."
				FROM   (
					SELECT @rownum:=@rownum+1 as no,a.*
					FROM(".
						$sTable
					.") a,
					(SELECT @rownum:=0) as r
				) a
				$sWhere
				$sOrder
				$sLimit
			";
			
			$rows = DB::select($sQuery);
			
			foreach( $rows as $row )
			{			
				$aksi='<center style="width:100px;">
							<div class="dropdown pull-right">
								<button class="btn btn-success btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-print"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="transaksi/kuitansi/'.$row->id.'/download" target="_blank" title="Cetak data?">Cetak Kuitansi</a></li>
								</ul>
							</div>
							<div class="dropdown pull-right" style="height:1.5vw !important;">
								<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-pencil"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="javascript:;" id="'.$row->id.'" title="Ubah data?" class="ubah">Ubah Kuitansi</a></li>
									<li><a href="javascript:;" id="'.$row->id.'" title="Hapus data?" class="hapus">Hapus Kuitansi</a></li>
								</ul>
							</div>
						</center>';
							
				$output['aaData'][] = array(
					$row->no,
					$row->id_rko,
					$row->nokuitansi,
					$row->tgkuitansi,
					$row->kdakun,
					$row->uraiben,
					$row->untuk,
					$row->tgbyr,
					'<div style="text-align:right;">'.number_format($row->totnilmak).'</div>',
					$row->tgl_update,
					$row->status,
					$aksi
				);
			}
			
			return response()->json($output);
		}
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function pilih($id)
	{
		try{
			$rows = DB::select("
				SELECT	id,
					id_rko,
					kdppk,
					concat(kdprogram,'-',kdgiat,'-',kdoutput,'-',kdsoutput,'-',kdkmpnen,'-',kdskmpnen,'-',kdmak) as id_pagu,
					totnilmak,
					DATE_FORMAT(tgkuitansi,'%d-%m-%Y') AS tgkuitansi,
					kdlokasi,
					kdkabkota,
					uraiben,
					untuk,
					kdnpwp,
					kdlokasibyr,
					kdkabkotabyr,
					DATE_FORMAT(tgbyr,'%d-%m-%Y') AS tgbyr,
					id_ref_pajak,
					ppn,
					pph_21,
					pph_22,
					pph_23,
					pph_24,
					nospby,
					nip_penerima,
					nip_ppk
				FROM d_transaksi
				WHERE id=?
			",[
				$id
			]);
			
			if(count($rows)==1){
				return response()->json($rows[0]);
			}
		}
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function simpan(Request $request)
	{
		try{
			if($request->input('inp-rekambaru')=='1'){ //tambah
				
				//cek uang muka
				$totnilmak = (int)preg_replace("/[^0-9 \d]/i", "", $request->input('totnilmak'));
				$nilai = (int)preg_replace("/[^0-9 \d]/i", "", $request->input('nilai'));
				
				if($totnilmak<=$nilai){
					
					DB::beginTransaction();
				
					$arr_tanggal1 = explode("-", $request->input('tgkuitansi'));
					$tgkuitansi = $arr_tanggal1[2].'-'.$arr_tanggal1[1].'-'.$arr_tanggal1[0];
					
					$arr_tanggal2 = explode("-", $request->input('tgbyr'));
					$tgbyr = $arr_tanggal2[2].'-'.$arr_tanggal2[1].'-'.$arr_tanggal2[0];
					
					$arr_akun = explode("-", $request->input('id_pagu'));
					$kdprogram = $arr_akun[0];
					$kdgiat = $arr_akun[1];
					$kdoutput = $arr_akun[2];
					$kdsoutput = $arr_akun[3];
					$kdkmpnen = $arr_akun[4];
					$kdskmpnen = $arr_akun[5];
					$kdakun = $arr_akun[6];
					$nilai = preg_replace("/[^0-9 \d]/i", "", $request->input('totnilmak'));
					$ppn = preg_replace("/[^0-9 \d]/i", "", $request->input('ppn'));
					$pph_21 = preg_replace("/[^0-9 \d]/i", "", $request->input('pph_21'));
					$pph_22 = preg_replace("/[^0-9 \d]/i", "", $request->input('pph_22'));
					$pph_23 = preg_replace("/[^0-9 \d]/i", "", $request->input('pph_23'));
					$pph_24 = preg_replace("/[^0-9 \d]/i", "", $request->input('pph_24'));
					
					$now = new \DateTime();
					$created_at = $now->format('Y-m-d H:i:s');
					
					$input = array(
						'kdalur' => '04',
						'id_rko' => $request->input('id_rko'),
						'nospby' => $request->input('nospby'),
						'nip_ppk' => '',
						'kddept' => session('kddept'),
						'kdunit' => session('kdunit'),
						'kdsatker' => session('kdsatker'),
						'kddekon' => session('kddekon'),
						'thang' => session('tahun'),
						'kdppk' => session('kdppk'),
						'kdprogram' => $kdprogram,
						'kdgiat' => $kdgiat,
						'kdoutput' => $kdoutput,
						'kdsoutput' => $kdsoutput,
						'kdkmpnen' => $kdkmpnen,
						'kdskmpnen' => $kdskmpnen,
						'kdmak' => $kdakun,
						'tgkuitansi' => $tgkuitansi,
						'totnilmak' => $nilai,
						'kdlokasi' => $request->input('kdlokasi'),
						'kdkabkota' => $request->input('kdkabkota'),
						'kdlokasibyr' => $request->input('kdlokasibyr'),
						'kdkabkotabyr' => $request->input('kdkabkotabyr'),
						'uraiben' => $request->input('uraiben'),
						'untuk' => $request->input('untuk'),
						'kdnpwp' => $request->input('kdnpwp'),
						'tgbyr' => $tgbyr,
						'id_ref_pajak' => $request->input('id_ref_pajak'),
						'ppn' => $ppn,
						'pph_21' => $pph_21,
						'pph_22' => $pph_22,
						'pph_23' => $pph_23,
						'pph_24' => $pph_24,
						'nip_jubar' => session('nip'),
						'nip_penerima' => $request->input('nip_penerima'),
						'id_user' => session('id_user'),
						'created_at' => $created_at,
						'updated_at' => $created_at
					);
						
					$id_trans = DB::table('d_transaksi')->insertGetId(
						$input
					);
					
					if($id_trans){
						
						//jika rapat
						if($request->input('id_rapat')!=''){
							
							$update = DB::update("
								update d_rapat set id_kuitansi=? where id=?
							",[
								$id_trans,
								$request->input('id_rapat')
							]);
							
						}
						
						//jika perjadin
						if($request->input('id_perjadin')!=''){
							
							$update = DB::update("
								update d_perjadin set id_kuitansi=? where id=?
							",[
								$id_trans,
								$request->input('id_perjadin')
							]);
							
						}
						
						$insert = DB::table('d_transaksi_status')->insertGetId(
							array(
								'id_trans' => $id_trans,
								'nourut' => 0,
								'terima' => '1',
								'id_user' => session('id_user'),
								'created_at' => $created_at,
								'updated_at' => $created_at
							)
						);
						
						if($insert){
							DB::commit();
							return 'success';
						}
						else{
							return 'Proses simpan status gagal!';
						}
						
					}
					else{
						return 'Proses simpan gagal!';
					}
					
				}
				else{
					return 'Realisasi kuitansi melebihi uang muka kerja!';
				}
				
			}
			else{ //ubah
				
				if($this->cek_status($request->input('id'))){
					
					DB::beginTransaction();
				
					$update = DB::update("
						update d_transaksi
						set kdlokasi=?,
							kdkabkota=?,
							tgkuitansi=STR_TO_DATE(?, '%d-%m-%Y'),
							untuk=?,
							uraiben=?,
							id_ref_pajak=?,
							ppn=?,
							pph_21=?,
							pph_22=?,
							pph_23=?,
							pph_24=?,
							tgbyr=STR_TO_DATE(?, '%d-%m-%Y'),
							kdlokasibyr=?,
							kdkabkotabyr=?,
							kdnpwp=?,
							nip_penerima=?,
							id_user=?,
							updated_at=now()
						where id=?
					",[
						$request->input('kdlokasi'),
						$request->input('kdkabkota'),
						$request->input('tgkuitansi'),
						$request->input('untuk'),
						$request->input('uraiben'),
						$request->input('id_ref_pajak'),
						preg_replace("/[^0-9 \d]/i", "", $request->input('ppn')),
						preg_replace("/[^0-9 \d]/i", "", $request->input('pph_21')),
						preg_replace("/[^0-9 \d]/i", "", $request->input('pph_22')),
						preg_replace("/[^0-9 \d]/i", "", $request->input('pph_23')),
						preg_replace("/[^0-9 \d]/i", "", $request->input('pph_24')),
						$request->input('tgbyr'),
						$request->input('kdlokasibyr'),
						$request->input('kdkabkotabyr'),
						$request->input('kdnpwp'),
						$request->input('nip_penerima'),
						session('id_user'),
						$request->input('inp-id')
					]);
					
					if($update){
						DB::commit();
						return 'success';
					}
					else{
						return 'Proses simpan gagal!';
					}
					
				}
				else{
					return 'Data tidak dapat diubah!';
				}
				
			}
			
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function hapus(Request $request)
	{
		try{
			DB::beginTransaction();
			
			if($this->cek_status($request->input('id'))){
				
				$delete = DB::delete("
					delete from d_transaksi_status where id_trans=?
				",[
					$request->input('id')
				]);
				
				$delete = DB::delete("
					delete from d_transaksi where id=?
				",[
					$request->input('id')
				]);
				
				if($delete){
					DB::commit();
					return 'success';
				}
				else{
					return 'Proses hapus gagal!';
				}
				
			}
			else{
				return 'Data tidak dapat dihapus!';
			}
		}
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function detil($id)
	{
		try{
			$rows = DB::select("
				select	a.*,
						b.nmspj
				from d_rko_pagu2 a
				left outer join t_spj b on(a.kdspj=b.kdspj)
				where a.id_rko=?
				order by a.id desc
			",[
				$id
			]);
			
			if(count($rows)>0){
				
				$data='<table class="table table-bordered">
						<thead>
							<tr>
								<th>No</th>
								<th>Akun</th>
								<th>Jenis</th>
								<th>Nama</th>
								<th>NIP</th>
								<th>Uraian</th>
								<th>Nilai</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>';
				
				$i=1;
				foreach($rows as $row){
					$data .= '<tr>
									<td>'.$i++.'</td>
									<td>'.$row->kdakun.'</td>
									<td>'.$row->nmspj.'</td>
									<td>'.$row->nama.'</td>
									<td>'.$row->nip.'</td>
									<td>'.$row->uraian.'</td>
									<td style="text-align:right;">'.number_format($row->nilai).'</td>
									<td>
										<center>
											<a href="javascript:;" id="'.$row->id.'" class="btn btn-xs btn-warning hapus-detil"><i class="fa fa-times"></i></a>
										</center>
									</td>
								</tr>';
				}
				
				$data .= '</tbody></table>';
				
				return $data;
				
			}
			else{
				return 'Data detil tidak ada';
			}
			
		}
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function cek_status($id_trans)
	{
		try{
			$rows = DB::select("
				SELECT	nourut
				FROM(
					SELECT	MAX(id) AS id
					FROM d_transaksi_status
					WHERE id_trans=?
				) a
				LEFT OUTER JOIN d_transaksi_status b ON(a.id=b.id)
			",[
				$id_trans
			]);
			
			if(count($rows)>0){
				
				if($rows[0]->nourut==0){
					return true;
				}
				
			}
			else{
				return true;
			}
			
		}
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function upload(Request $request)
	{
		try{
			$arr_data = explode("-", $request->input('data'));
			$id_trans = $arr_data[0];
			$kddok = $arr_data[1];
			
			$rows = DB::select("
				select	tipe
				from t_dok
				where id=?
			",[
				$kddok
			]);
			
			if(count($rows)>0){
				
				$tipe = explode(",", $rows[0]->tipe);
				
				$targetFolder = 'data\dok\transaksi\\'; // Relative to the root
			
				if(!empty($_FILES)) {
					$file_name = $_FILES['file']['name'];
					$tempFile = $_FILES['file']['tmp_name'];
					$targetFile = $targetFolder.$file_name;
					$fileTypes = $tipe; // File extensions
					$fileParts = pathinfo($_FILES['file']['name']);
					$fileSize = $_FILES['file']['size'];
					//type file sesuai
					
					if(in_array($fileParts['extension'],$fileTypes)) {
						
						//isi kosong..??
						if($fileSize>0){
							
							$now = new \DateTime();
							$tglupload = $now->format('YmdHis');
							
							$file_name_baru=$id_trans.'_'.$kddok.'_'.session('kdsatker').'_'.$tglupload.'.'.$fileParts['extension'];
							move_uploaded_file($tempFile,$targetFolder.$file_name_baru);
							
							if(file_exists($targetFolder.$file_name_baru)){
								
								$insert = DB::insert("
									insert into d_transaksi_dok(id_trans,id_dok,nmfile,id_user,created_at,updated_at)
									values(?,?,?,?,now(),now())
								",[
									$id_trans,
									$kddok,
									$file_name_baru,
									session('id_user')
								]);
								
								if($insert){
									return '1';
								}
								else{
									return 'File berhasil diupload, tetapi gagal disimpan!';
								}
								
							}
							else{
								return 'File gagal diupload!';
							}
							
						}
						else{
							return 'Isi file kosong, periksa data anda.';
						}
					}
					else{
						return 'Tipe file tidak sesuai.';
					}
				}
				else{
					return 'Tidak ada file yang diupload.';
				}
				
			}
			else{
				return 'Referensi jenis dokumen tidak ditemukan!';
			}
			
		}
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function download_dok($param)
	{
		try{
			$path='data/dok/transaksi/';

			$log = $path.$param;
								
			header('Content-Description:Berkas Dokumen Transaksi');
			header('Content-Type:application/octet-stream');
			header('Content-Disposition:attachment;filename=' . basename($param));
			header('Content-Transfer-Encoding:binary');
			header('Expires:0');
			header('Cahce-Control:must-revalidate');
			header('Pragma:public');
			header('Content-Length:'.filesize($log));
			readfile($log);
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
}