<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EvaluasiController extends Controller {

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
			$aColumns = array('id', 'bulan', 'kode', 'kdppk', 'kendala', 'sebab', 'rekomendasi', 'catatan', 'arahan', 'status', 'nmstatus');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			
			$sTable = "	SELECT	id,
							bulan,
							CONCAT(kdprogram, '.', kdgiat, '.', kdoutput) as kode,
							kdppk,
							kendala,
							sebab,
							rekomendasi,
							catatan,
							arahan,
							a.status as status,
							nmstatus
						FROM d_evaluasi a
						INNER JOIN t_status_evaluasi b on a.status=b.status
						INNER JOIN t_periode c on a.periode=c.periode
						WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."'
						ORDER BY a.id DESC";
			
			/*
			 * Paging
			 */
			$sLimit = "";
			$iDisplayStart=$request->input('start');
			$iDisplayLength=$request->input('length');
			if ( isset( $iDisplayStart ) &&  $iDisplayLength != '-1' ) 
			{
				 $sLimit = "LIMIT ".intval( $request->input('start') ).", ".
					intval( $request->input('length') );
			}
			 
			 
			/*
			 * Ordering
			 */
			$sOrder = "";
			if ( $request->input('order') !==null )
			{
				$arr_order=$request->input('order');
				
				if(isset($arr_order[0]['column'])){
					$sort_col=$arr_order[0]['column'];
					
					$sort_dir='desc';
					if(isset($arr_order[0]['dir'])){
						$sort_dir=$arr_order[0]['dir'];
					}
					
					$sOrder = " ORDER BY a.".$aColumns[ intval( $sort_col ) ]." ".($sort_dir==='asc' ? 'asc' : 'desc') ." ";
				}
			}
			 
			 
			/*
			 * Filtering
			 */
			$sWhere = "";
			$arr_search=$request->input('search');
			$sSearch=$arr_search['value'];
			if ( isset($sSearch) && $sSearch != "" )
			{
				for ( $i=0 ; $i<count($aColumns) ; $i++ )
				{
					$arr_where[] = $aColumns[$i]." LIKE '%".( $sSearch )."%' ";
				}
				
				$sWhere .= "WHERE ".implode(" OR ", $arr_where);
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
				SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
				FROM   ($sTable) a
				$sWhere
				$sOrder
				$sLimit
			";
			
			$rows = DB::select($sQuery);
			
			foreach( $rows as $row )
			{		
				$setuju='';
				$ubah='';
				$hapus='';
				$aksi='<center>-</center>';
				
				if(session('kdlevel')=='12'){
					if($row->status!=='0'){
						$setuju='';
						$ubah='';
						$hapus='';
					}
					else{
						$hapus='<div class="dropdown pull-right">
								<button class="btn btn-danger btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-trash"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="javascript:;" id="'.$row->id.'" title="Hapus Rekomendasi?" class="hapus">Hapus Rekomendasi</a></li>
								</ul>
							</div>';
						$ubah='<div class="dropdown pull-right" style="height:1.5vw !important;">
								<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-pencil"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="javascript:;" id="'.$row->id.'" title="Ubah rekomendasi?" class="ubah">Ubah Rekomendasi</a></li>
									<!--<li><a href="javascript:;" id="'.$row->id.'" title="Hapus rekomendasi?" class="hapus">Hapus Rekomendasi</a></li>-->
								</ul>
							</div>';
						$setuju='<div class="dropdown pull-right" style="height:1.5vw !important;">
								<button class="btn btn-success btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-check"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<!--<li><a href="javascript:;" id="'.$row->id.'" title="Ubah data?" class="ubah">Ubah Rapat</a></li>-->
									<li><a href="javascript:;" id="'.$row->id.'" title="Setujui rekomendasi?" class="setuju">Setujui Rekomendasi</a></li>
								</ul>
							</div>';
						$aksi='<center style="width:120px;">
							'.$hapus.'
							'.$ubah.'
							'.$setuju.'
						</center>';
					}
				}
				
				else if((session('kdlevel')=='13' && $row->status!=='1') || (session('kdlevel')=='09' && $row->status!=='2')){
						$setuju='';
						$ubah='';
						$hapus='';
					}else{
						$setuju='<div class="dropdown pull-right" style="height:1.5vw !important;">
								<button class="btn btn-success btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-check"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<!--<li><a href="javascript:;" id="'.$row->id.'" title="Ubah data?" class="ubah">Ubah Rapat</a></li>-->
									<li><a href="javascript:;" id="'.$row->id.'" title="Setujui rekomendasi?" class="ubah">Setujui Rekomendasi</a></li>
								</ul>
							</div>';
						$aksi='<center style="width:120px;">
							'.$hapus.'
							'.$ubah.'
							'.$setuju.'
						</center>';
					}
				
				$output['aaData'][] = array(
					$row->id,
					$row->bulan,
					$row->kode,
					$row->kdppk,
					$row->kendala,
					$row->sebab,
					$row->rekomendasi,
					$row->catatan,
					$row->arahan,
					$row->nmstatus,
					$aksi
				);
			}
			
			return response()->json($output);
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function pilih_rapat(Request $request)
	{
		try{
			$aColumns = array('id','kdakun','id_rko','nosurat','tgsurat','tgrapat','ket','nilai','pajak','kdppk','nospby','nmppk');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "	SELECT	a.id,
							concat(kdprogram,'-',kdgiat,'-',kdoutput,'-',kdsoutput,'-',kdkmpnen,'-',kdskmpnen,'-',kdakun) as kdakun,
							a.id_rko,
							a.nosurat,
							DATE_FORMAT(a.tgsurat,'%d-%m-%Y') AS tgsurat,
							DATE_FORMAT(a.tgrapat,'%d-%m-%Y') AS tgrapat,
							a.ket,
							b.kdppk,
							b.nospby,
							c.nmppk,
							ifnull(d.nilai,0) as nilai,
							ifnull(d.pajak,0) as pajak
						FROM d_rapat a
						LEFT OUTER JOIN d_rko b ON(a.id_rko=b.id)
						LEFT OUTER JOIN t_ppk c ON(a.kdppk=c.kdppk)
						LEFT OUTER JOIN(
							SELECT	id_rapat,
									SUM(nilai) as nilai,
									SUM(pajak) as pajak
							FROM d_rapat_detil
							GROUP BY id_rapat
						) d ON(a.id=d.id_rapat)
						WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."' AND ifnull(a.id_kuitansi,'')=''
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
				$aksi='<center><a href="javascript:;" id="'.$row->id_rko.'-'.$row->nospby.'-'.$row->kdppk.'.'.$row->kdakun.'.'.number_format($row->nilai).'.'.number_format($row->pajak).'.'.$row->id.'" class="btn btn-xs btn-success pilih-rapat"><i class="fa fa-check"></i></a></center>';
				
				$output['aaData'][] = array(
					$row->no,
					$row->kdakun,
					$row->id_rko,
					$row->nosurat,
					$row->tgsurat,
					$row->tgrapat,
					$row->ket,
					'<div style="text-align:right;">'.number_format($row->nilai).'</div>',
					'<div style="text-align:right;">'.number_format($row->pajak).'</div>',
					$aksi
				);
			}
			
			return response()->json($output);
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function pilih($id)
	{
		try{
			$rows = DB::select("
				SELECT	id,
					kdprogram,
					kdgiat,
					kdoutput,
					kdsoutput,
					kdkmpnen,
					kdppk,
					periode,
					kendala,
					sebab,
					rekomendasi,
					catatan,
					arahan,
					id_kategori
				FROM d_evaluasi
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
				
				DB::beginTransaction();
				
				/* $arr_tanggal1 = explode("-", $request->input('tgkuitansi'));
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
				$pph_24 = preg_replace("/[^0-9 \d]/i", "", $request->input('pph_24')); */
				
				$now = new \DateTime();
				$created_at = $now->format('Y-m-d H:i:s');
				
				$input = array(
					'kddept' => session('kddept'),
					'kdunit' => session('kdunit'),
					'kdsatker' => session('kdsatker'),
					'kddekon' => session('kddekon'),
					'thang' => session('tahun'),
					'periode' => $request->input('periode'),
					'kdppk' => $request->input('kdppk'),
					'id_kategori' => $request->input('id_kategori'),
					'kdprogram' => $request->input('program'),
					'kdgiat' => $request->input('kegiatan'),
					'kdoutput' => $request->input('output'),
					'kdsoutput' => $request->input('suboutput'),
					'kdkmpnen' => $request->input('komponen'),
					'kendala' => $request->input('kendala'),
					'sebab' => $request->input('sebab'),
					'rekomendasi' => $request->input('rekomendasi'),
					'status' => '0',
					'id_user' => session('id_user'),
					'created_at' => $created_at,
					'updated_at' => $created_at
				);
					
				$id_evaluasi = DB::table('d_evaluasi')->insertGetId(
					$input
				);
				
				if($id_evaluasi){
					
					//jika rapat
					/* if($request->input('id_rapat')!=''){
						
						$update = DB::update("
							update d_rapat set id_kuitansi=? where id=?
						",[
							$id_trans,
							$request->input('id_rapat')
						]);
						
					} */
					
					//jika perjadin
					/* if($request->input('id_perjadin')!=''){
						
						$update = DB::update("
							update d_perjadin set id_kuitansi=? where id=?
						",[
							$id_trans,
							$request->input('id_perjadin')
						]);
						
					} */
					
					/* $insert = DB::table('d_transaksi_status')->insertGetId(
						array(
							'id_trans' => $id_trans,
							'nourut' => 0,
							'terima' => '1',
							'id_user' => session('id_user'),
							'created_at' => $created_at,
							'updated_at' => $created_at
						)
					); */
					
					/* if($insert){
						DB::commit();
						return 'success';
					}
					else{
						return 'Proses simpan status gagal!';
					} */
					
					DB::commit();
					return 'success';
					
				}
				else{
					return 'Proses simpan gagal!';
				}
				
			}
			else{ //ubah
				
				if($this->cek_status($request->input('id'))){
					
					DB::beginTransaction();
				
					$update = DB::update("
						update d_evaluasi
						set kdprogram=?,
							kdoutput=?,
							kdsoutput=?,
							kdkmpnen=?,
							id_kategori=?,
							kendala=?,
							sebab=?,
							rekomendasi=?,
							id_user=?,
							updated_at=now()
						where id=?
					",[
						$request->input('program'),
						$request->input('output'),
						$request->input('suboutput'),
						$request->input('komponen'),
						$request->input('id_kategori'),
						$request->input('kendala'),
						$request->input('sebab'),
						$request->input('rekomendasi'),
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

	public function setuju(Request $request)
	{
		try{
			DB::beginTransaction();
			
			if($this->cek_status($request->input('id'))){
				
				if(session('kdlevel')=='12'){
					$update = DB::update("
						update d_evaluasi
						set status='1',
							updated_at=now()
						where id=?
					",[
						$request->input('id')
					]);
					
					if($update){
						DB::commit();
						return 'success';
					}
					else{
						return 'Proses simpan gagal!';
					}
				}
				
				if(session('kdlevel')=='13'){
					if($request->input('optstatus')=='1'){
						$update = DB::update("
							update d_evaluasi
							set status='2',
								catatan=?,
								updated_at=now()
							where id=?
						",[
							$request->input('catatan'),
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
					
					if($request->input('optstatus')=='0'){
						$update = DB::update("
							update d_evaluasi
							set status='0',
								catatan=?,
								updated_at=now()
							where id=?
						",[
							$request->input('catatan'),
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
					
				}
				
				if(session('kdlevel')=='09'){
					if($request->input('optstatus')=='1'){
						$update = DB::update("
							update d_evaluasi
							set status='3',
								arahan=?,
								updated_at=now()
							where id=?
						",[
							$request->input('arahan'),
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
					
					if($request->input('optstatus')=='0'){
						$update = DB::update("
							update d_evaluasi
							set status='1',
								arahan=?,
								updated_at=now()
							where id=?
						",[
							$request->input('arahan'),
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
					
				}
			}
			else{
				return 'Data tidak dapat diubah!';
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
				
				/* $delete = DB::delete("
					delete from d_evaluasi where id_rapat=?
				",[
					$request->input('id')
				]); */
				
				$delete = DB::delete("
					delete from d_evaluasi where id=?
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
				SELECT	a.*,
					c.nama,
					b.nmjab
				FROM d_rapat_detil a
				LEFT OUTER JOIN t_jab_kegiatan b ON(a.kdjab=b.kdjab)
				LEFT OUTER JOIN t_pegawai c ON(a.nip=c.nip)
				WHERE a.id_rapat=?
				ORDER BY c.nama
			",[
				$id
			]);
			
			if(count($rows)>0){
				
				$data='<table class="table table-bordered">
						<thead>
							<tr>
								<th>No</th>
								<th>Nama</th>
								<th>NIP</th>
								<th>Jabatan</th>
								<th>Nilai</th>
							</tr>
						</thead>
						<tbody>';
				
				$i=1;
				foreach($rows as $row){
					$data .= '<tr>
									<td>'.$i++.'</td>
									<td>'.$row->nama.'</td>
									<td>'.$row->nip.'</td>
									<td>'.$row->nmjab.'</td>
									<td style="text-align:right;">'.number_format($row->nilai).'</td>
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
	
	public function cek_status($id_evaluasi)
	{
		try{
			$rows = DB::select("
				SELECT	status
				FROM d_evaluasi a
				WHERE a.id=?
			",[
				$id_evaluasi
			]);
			
			if($rows[0]->status!==3){
				
				return true;
				
			}
			else{
				return false;
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
			$id_rko = $arr_data[0];
			$kddok = $arr_data[1];
			
			$rows = DB::select("
				select	tipe
				from t_dok
				where id=?
			",[
				$kddok
			]);
			
			if(count($rows)>0){
				
				$tipe = $rows[0]->tipe;
				
				$targetFolder = 'data\dok\\'; // Relative to the root
			
				if(!empty($_FILES)) {
					$file_name = $_FILES['file']['name'];
					$tempFile = $_FILES['file']['tmp_name'];
					$targetFile = $targetFolder.$file_name;
					$fileTypes = array($tipe); // File extensions
					$fileParts = pathinfo($_FILES['file']['name']);
					$fileSize = $_FILES['file']['size'];
					//type file sesuai..??	
					if(in_array($fileParts['extension'],$fileTypes)) {
						
						//isi kosong..??
						if($fileSize>0){
							
							$now = new \DateTime();
							$tglupload = $now->format('YmdHis');
							
							$file_name_baru=$id_rko.'_'.$kddok.'_'.session('kdsatker').'_'.$tglupload.'.'.$fileParts['extension'];
							move_uploaded_file($tempFile,$targetFolder.$file_name_baru);
							
							if(file_exists($targetFolder.$file_name_baru)){
								
								$insert = DB::insert("
									insert into d_rko_dok(id_rko,id_dok,nmfile,id_user,created_at,updated_at)
									values(?,?,?,?,now(),now())
								",[
									$id_rko,
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
	
	public function pilih_rko(Request $request)
	{
		try{
			$aColumns = array('id','nmjenisgiat','urrko','tgrko','nospby','kdppk','nmppk');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "	SELECT	a.id,
							c.nmjenisgiat,
							a.urrko,
							DATE_FORMAT(a.tgrko,'%d-%m-%Y') AS tgrko,
							a.nospby,
							a.kdppk,
							ifnull(d.nmppk,'000000000000000000') as nmppk
						FROM d_rko a
						LEFT OUTER JOIN(
							SELECT	a.id_rko,
								b.nourut
							FROM(
								SELECT	id_rko,
									MAX(id) AS max_id
								FROM d_rko_status
								GROUP BY id_rko
							) a
							LEFT OUTER JOIN d_rko_status b ON(a.max_id=b.id)
						) b ON(a.id=b.id_rko)
						LEFT OUTER JOIN t_jenisgiat c ON(a.jenisgiat=c.jenisgiat)
						LEFT OUTER JOIN t_ppk d ON(a.kdsatker=d.kdsatker AND a.thang=d.thang AND a.kdppk=d.kdppk)
						LEFT OUTER JOIN d_rapat e ON(a.id=e.id_rko)
						WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."' AND a.kdalur IN('02') AND b.nourut=12 AND e.id_rko is null
						ORDER BY a.id DESC";
			
			/*
			 * Paging
			 */
			$sLimit = "";
			$iDisplayStart=$request->input('start');
			$iDisplayLength=$request->input('length');
			if ( isset( $iDisplayStart ) &&  $iDisplayLength != '-1' ) 
			{
				 $sLimit = "LIMIT ".intval( $request->input('start') ).", ".
					intval( $request->input('length') );
			}
			 
			 
			/*
			 * Ordering
			 */
			$sOrder = "";
			if ( $request->input('order') !==null )
			{
				$arr_order=$request->input('order');
				
				if(isset($arr_order[0]['column'])){
					$sort_col=$arr_order[0]['column'];
					
					$sort_dir='desc';
					if(isset($arr_order[0]['dir'])){
						$sort_dir=$arr_order[0]['dir'];
					}
					
					$sOrder = " ORDER BY a.".$aColumns[ intval( $sort_col ) ]." ".($sort_dir==='asc' ? 'asc' : 'desc') ." ";
				}
			}
			 
			 
			/*
			 * Filtering
			 */
			$sWhere = "";
			$arr_search=$request->input('search');
			$sSearch=$arr_search['value'];
			if ( isset($sSearch) && $sSearch != "" )
			{
				for ( $i=0 ; $i<count($aColumns) ; $i++ )
				{
					$arr_where[] = $aColumns[$i]." LIKE '%".( $sSearch )."%' ";
				}
				
				$sWhere .= "WHERE ".implode(" OR ", $arr_where);
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
				SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
				FROM   ($sTable) a
				$sWhere
				$sOrder
				$sLimit
			";
			
			$rows = DB::select($sQuery);
			
			foreach( $rows as $row )
			{			
				$aksi='<center><a href="javascript:;" id="'.$row->id.'-'.$row->nospby.'-'.$row->kdppk.'" class="btn btn-xs btn-success pilih-rko"><i class="fa fa-check"></i></a></center>';
							
				$output['aaData'][] = array(
					$row->id.'-'.$row->nospby.'-'.$row->kdppk,
					$row->id,
					$row->nmjenisgiat,
					$row->urrko,
					$row->tgrko,
					$row->nospby,
					$aksi
				);
			}
			
			return response()->json($output);
		}
		catch(\Exception $e){
			return $e;
		}
	}
}