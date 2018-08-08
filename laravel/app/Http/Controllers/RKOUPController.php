<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use mPDF;

class RKOUPController extends Controller {

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
			$where = " WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."' AND a.kdalur IN('01') ";
			if(session('kdlevel')=='04'){
				$where = " WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdalur IN('06') ";
			}
			
			$aColumns = array('id','nmalur','nmjenisgiat','urrko','tgrko','jml','nilai','created_at','nmstatus');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "	SELECT	a.id,
								b.nmalur,
								c.nmjenisgiat,
								a.urrko,
								DATE_FORMAT(a.tgrko,'%d-%m-%Y') AS tgrko,
								IFNULL(e.jml,0) AS jml,
								IFNULL(e.nilai,0) AS nilai,
								a.created_at,
								f.status as nmstatus
						FROM d_rko a
						LEFT OUTER JOIN t_alur b ON(a.kdalur=b.kdalur)
						LEFT OUTER JOIN t_jenisgiat c ON(a.jenisgiat=c.jenisgiat)
						LEFT OUTER JOIN(
							SELECT	a.id_rko,
									b.nourut
							FROM(
								SELECT	id_rko,
										MAX(id) AS id
								FROM d_rko_status
								GROUP BY id_rko
							) a
							LEFT OUTER JOIN d_rko_status b ON(a.id_rko=b.id_rko AND a.id=b.id)
						) d ON(a.id=d.id_rko)
						LEFT OUTER JOIN(
							SELECT	id_rko,
									COUNT(*) AS jml,
									SUM(nilai) AS nilai
							FROM d_rko_pagu1
							GROUP BY id_rko
						) e ON(a.id=e.id_rko)
						LEFT OUTER JOIN t_alur_status f ON(a.kdalur=f.kdalur AND d.nourut=f.nourut)
						".$where."
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
						$sort_col-=1;
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
				$aksi='<center style="width:100px;">
							<div class="dropdown pull-right">
								<button class="btn btn-success btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-print"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="rko/status/surat/'.$row->id.'" target="_blank" title="Cetak data?">Cetak RKO</a></li>
									<li><a href="rko/up-tup/'.$row->id.'/download" target="_blank" title="Cetak data?">Cetak Lampiran</a></li>
								</ul>
							</div>
							<div class="dropdown pull-right">
								<button class="btn btn-danger btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-plus"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="javascript:;" id="'.$row->id.'" title="Tambah data?" class="tambah">Tambah Pagu</a></li>
								</ul>
							</div>
							<div class="dropdown pull-right" style="height:1.5vw !important;">
								<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-pencil"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="javascript:;" id="'.$row->id.'" title="Ubah data?" class="ubah">Ubah RKO</a></li>
									<li><a href="javascript:;" id="'.$row->id.'" title="Hapus data?" class="hapus">Hapus RKO</a></li>
								</ul>
							</div>
						</center>';
							
				$output['aaData'][] = array(
					$row->id,
					$row->id,
					$row->nmalur,
					$row->urrko,
					$row->tgrko,
					'<div style="text-align:right;">'.number_format($row->jml).'</div>',
					'<div style="text-align:right;">'.number_format($row->nilai).'</div>',
					$row->created_at,
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
	
	public function pilih($id)
	{
		try{
			$rows = DB::select("
				SELECT	id,
						kdalur,
						jenisgiat,
						urrko,
						DATE_FORMAT(tgrko,'%d-%m-%Y') AS tgrko,
						kdlokasi,
						kdkabkota,
						thang,
						DATE_FORMAT(tanggal1,'%d-%m-%Y') AS tanggal1,
						DATE_FORMAT(tanggal2,'%d-%m-%Y') AS tanggal2,
						periode1,
						periode2,
						nip_pk1,
						nip_pk2,
						kdbpp,
						kdppk
				FROM d_rko
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
				
				$kdalur = '01';
				if(session('kdlevel')=='04'){
					$kdalur = '06';
				}
				
				$arr_tgrko = explode("-", $request->input('tgrko'));
				$tgrko = $arr_tgrko[2].'-'.$arr_tgrko[1].'-'.$arr_tgrko[0];
				
				$now = new \DateTime();
				$created_at = $now->format('Y-m-d H:i:s');
				
				$id_rko = DB::table('d_rko')->insertGetId(
					array(
						'kddept' => session('kddept'),
						'kdunit' => session('kdunit'),
						'kdsatker' => session('kdsatker'),
						'kddekon' => session('kddekon'),
						'kdalur' => $kdalur,
						'urrko' => $request->input('urrko'),
						'tgrko' => $tgrko,
						'thang' => session('tahun'),
						'kdppk' => session('kdppk'),
						'kdbpp' => session('kdbpp'),
						'id_user' => session('id_user'),
						'created_at' => $created_at,
						'updated_at' => $created_at
					)
				);
				
				if($id_rko){
					
					$insert = DB::table('d_rko_status')->insertGetId(
						array(
							'id_rko' => $id_rko,
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
			else{ //ubah
				
				if($this->cek_status($request->input('id'))){
					
					DB::beginTransaction();
				
					$update = DB::update("
						update d_rko
						set	urrko=?,
							tgrko=STR_TO_DATE(?, '%d-%m-%Y'),
							kdppk=?,
							kdbpp=?,
							id_user=?,
							updated_at=now()
						where id=?
					",[
						$request->input('urrko'),
						$request->input('tgrko'),
						session('kdppk'),
						session('kdbpp'),
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
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function hapus(Request $request)
	{
		try{
			DB::beginTransaction();
			
			if($this->cek_status($request->input('id')) && $this->cek_detil($request->input('id'))){
				
				$delete = DB::delete("
					delete from d_rko_status where id_rko=?
				",[
					$request->input('id')
				]);

				$delete = DB::delete("
					delete from d_rko where id=?
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
				select	a.*
				from d_rko_pagu1 a
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
									<td>'.$row->kdprogram.'.'.$row->kdgiat.'.'.$row->kdoutput.'.'.$row->kdsoutput.'.'.$row->kdkmpnen.'.'.$row->kdskmpnen.'.'.$row->kdakun.'</td>
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
	
	public function simpan_detil(Request $request)
	{
		try{
			$id_rko = $request->input('id_rko');
			$uraian = $request->input('uraian');
			$nilai = preg_replace("/[^0-9 \d]/i", "", $request->input('nilai'));
			$arr_pagu = explode("-", $request->input('id_pagu'));
			$kdprogram = $arr_pagu[0];
			$kdgiat = $arr_pagu[1];
			$kdoutput = $arr_pagu[2];
			$kdsoutput = $arr_pagu[3];
			$kdkmpnen = $arr_pagu[4];
			$kdskmpnen = $arr_pagu[5];
			$kdakun = $arr_pagu[6];
			
			$query = "insert into d_rko_pagu1(id_rko,kdprogram,kdgiat,kdoutput,kdsoutput,kdkmpnen,kdskmpnen,kdakun,thang,uraian,nilai,id_user,created_at,updated_at)
					value(".$id_rko.",'".$kdprogram."','".$kdgiat."','".$kdoutput."','".$kdsoutput."','".$kdkmpnen."','".$kdskmpnen."','".$kdakun."','".session('tahun')."','".$uraian."',".$nilai.",".session('id_user').",now(),now())";
							
			DB::beginTransaction();
			
			$insert=DB::insert($query);
			
			if($insert){
				DB::commit();
				return 'success';
			}
			else{
				return 'Data gagal disimpan!';
			}
			
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}

	public function hapus_detil(Request $request)
	{
		try{
			DB::beginTransaction();
			
			$rows = DB::select("
				select	id_rko
				from d_rko_pagu1
				where id=?
			",[
				$request->input('id')
			]);
			
			if(count($rows)>0){
				
				$id_rko = $rows[0]->id_rko;
				
				if($this->cek_status($id_rko)){
					
					$delete = DB::delete("
						delete from d_rko_pagu1 where id=?
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
			else{
				return 'Data tidak ditemukan!';
			}
			
		}
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function cek_status($id_rko)
	{
		try{
			$rows = DB::select("
				SELECT	a.nourut,
						b.jml
				FROM(
					SELECT	nourut
					FROM(
						SELECT	MAX(id) AS id
						FROM d_rko_status
						WHERE id_rko=?
					) a
					LEFT OUTER JOIN d_rko_status b ON(a.id=b.id)
				) a,
				(
					SELECT	COUNT(*) AS jml
					FROM d_rko_pagu1
					WHERE id_rko=?
				) b
			",[
				$id_rko,
				$id_rko
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
	
	public function cek_detil($id_rko)
	{
		try{
			$rows = DB::select("
				SELECT	a.nourut,
						b.jml
				FROM(
					SELECT	nourut
					FROM(
						SELECT	MAX(id) AS id
						FROM d_rko_status
						WHERE id_rko=?
					) a
					LEFT OUTER JOIN d_rko_status b ON(a.id=b.id)
				) a,
				(
					SELECT	COUNT(*) AS jml
					FROM d_rko_pagu1
					WHERE id_rko=?
				) b
			",[
				$id_rko,
				$id_rko
			]);
			
			if(count($rows)>0){
				
				if($rows[0]->jml==0){
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
	
	/**
	 * description 
	 */
	public function download($param)
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
				SELECT   CONCAT('023', '.', '11', '.', k.kdprogram) AS kode, k.kdprogram, l.nmprogram, SUM(pagu) AS pagu,sum(realisasi) as realisasi, sum(dimintakan)dimintakan,
				sum(sisa)sisa
				FROM       (select a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.kdlokasi, a.kdkabkota, a.nip_pk1, a.nip_pk2,
								   a.kdbpp, a.kdppk, a.kdprogram, a.kdgiat, a.kdoutput, a.kdkmpnen, a.kdskmpnen, a.kdakun, a.uraian, b.paguakhir  pagu,c.realisasi,
				a.nilai dimintakan,b.paguakhir-c.realisasi-a.nilai sisa from
				(SELECT a.id, a.kddept, a.kdunit, a.kdsatker, a.thang, a.kdalur, a.jenisgiat, a.urrko, a.tgrko, a.kdlokasi, a.kdkabkota, a.nip_pk1, a.nip_pk2,
								   a.kdbpp, a.kdppk, b.kdprogram, b.kdgiat, b.kdoutput, b.kdkmpnen, b.kdskmpnen, b.kdakun, b.uraian, b.nilai
							FROM   d_rko a LEFT JOIN d_rko_pagu1 b ON a.id = b.id_rko
							WHERE  a.kdalur = '01' AND a.id = ".$param.")a
					left join d_pagu b
					on a.kddept=b.kddept and a.kdunit=b.kdunit and b.kdprogram=''
					left join (select kddept,kdunit,sum(totnilmak)realisasi from d_transaksi
					group by kddept,kdunit)c
					on a.kddept=c.kddept and a.kdunit=c.kdunit
					) k
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
					<th class="vt tl">'.$this->rupiah(rprogram($param)[0]->dimintakan).'</th>
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

					<td class="wd12">&nbsp;</td>
					<td class="wd12">&nbsp;</td>
					<td class="wd12">&nbsp;</td>
					<td class="wd12">&nbsp;</td>
				</tr>
				<tr>
					<th class="bd">NO.</th>
					<th class="bd">KODE</th>
					<th class="bd" colspan="5">URAIAN</th>

					<th class="bd">PAGU</th>
					<th class="bd">REALISASI</th>
					<th class="bd">JUMLAH DIMINTAKAN</th>
					<th class="bd">SISA</th>
				</tr>
				';
				$i=1;
		foreach(rprogram($param) as $rp) {
			$html_out.= '
				<tr>
					<td class="bdlr">'.$i++.'</td>
					<td class="bdlr vt tl fwb">'.$rp->kode.'</td>
					<td class="bdlr vt fwb" colspan="5">'.$rp->nmprogram.'</td>
					
					<td class="bdlr vt tr fwb">'.$this->rupiah($rp->pagu).'</td>
					<td class="bdlr vt tr fwb">'.$this->rupiah($rp->realisasi).'</td>
					<td class="bdlr vt tr fwb">'.$this->rupiah($rp->dimintakan).'</td>
					<td class="bdlr vt tr fwb">'.$this->rupiah($rp->sisa).'</td>
				</tr>
			';
			
			foreach(rgiat($param) as $rg) {
				$html_out.= '
				<tr>
					<td class="bdlr bdt bdb"></td>
					<td class="bdlr bdt bdb vt tl fwb">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$rg->kode.'</td>
					<td class="bdlr bdt bdb vt fwb" colspan="5">'.$rg->nmgiat.'</td>
					<!--<td class="bdlr bdt bdb">&nbsp;</td>
					<td class="bdlr bdt bdb">&nbsp;</td>-->
					<td class="bdlr bdt bdb vt tr fwb">'.$this->rupiah($rg->pagu).'</td>
					<td class="bdlr bdt bdb vt tr fwb">'.$this->rupiah($rg->realisasi).'</td>
					<td class="bdlr bdt bdb vt tr fwb">'.$this->rupiah($rg->dimintakan).'</td>
					<td class="bdlr bdt bdb vt tr fwb">'.$this->rupiah($rg->sisa).'</td>
				</tr>
				';
				
				foreach(routput($param, $rg->kdgiat) as $ro) {
					$html_out.= '
				<tr>
					<td class="bdlr bdt bdb"></td>
					<td class="bdlr bdt bdb vt tc fwb">'.$ro->kode.'</td>
					<td class="bdlr bdt bdb vt fwb" colspan="5">'.$ro->nmoutput.'</td>
					<!--<td class="bdlr bdt bdb">&nbsp;</td>
					<td class="bdlr bdt bdb">&nbsp;</td>-->
					<td class="bdlr bdt bdb vt tr fwb">'.$this->rupiah($ro->pagu).'</td>
					<td class="bdlr bdt bdb vt tr fwb">'.$this->rupiah($ro->realisasi).'</td>
					<td class="bdlr bdt bdb vt tr fwb">'.$this->rupiah($ro->dimintakan).'</td>
					<td class="bdlr bdt bdb vt tr fwb">'.$this->rupiah($ro->sisa).'</td>
				</tr>	
					';
					
					foreach(rkomponen($param, $rg->kdgiat, $ro->kdoutput) as $rk) {
						$html_out.= '
				<tr>
					<td class="bdlr bdb"></td>
					<td class="bdlr bdb vt tr fwb">'.$rk->kdkmpnen.'</td>
					<td class="bdlr bdb vt fwb" colspan="5">'.$rk->urkmpnen.'</td>
					<!--<td class="bdlr bdb fwb">&nbsp;</td>
					<td class="bdlr bdb fwb">&nbsp;</td>-->
					<td class="bdlr bdb vt tr fwb">'.$this->rupiah($rk->pagu).'</td>
					<td class="bdlr bdb vt tr fwb">'.$this->rupiah($rk->realisasi).'</td>
					<td class="bdlr bdb vt tr fwb">'.$this->rupiah($rk->dimintakan).'</td>
					<td class="bdlr bdb vt tr fwb">'.$this->rupiah($rk->sisa).'</td>
				</tr>
						';
						
						foreach(rskomponen($param, $rg->kdgiat, $ro->kdoutput, $rk->kdkmpnen) as $rs) {
							$html_out.= '
				<tr>
					<td class="bdlr bdb"></td>
					<td class="bdlr bdb"></td>
					<td class="bdl bdb vt tr">'.$rs->kdskmpnen.'</td>
					<td class="bdr bdb vt tl" colspan="4">'.$rs->urskmpnen.'</td>
					<!--<td class="bdlr bdb">&nbsp;</td>
					<td class="bdlr bdb">&nbsp;</td>-->
					<td class="bdlr bdb vt tr">'.$this->rupiah($rs->pagu).'</td>
					<td class="bdlr bdb vt tr">'.$this->rupiah($rs->realisasi).'</td>
					<td class="bdlr bdb vt tr">'.$this->rupiah($rs->dimintakan).'</td>
					<td class="bdlr bdb vt tr">'.$this->rupiah($rs->sisa).'</td>
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
					<!--<td class="bdlr">&nbsp;</td>
					<td class="bdlr">&nbsp;</td>-->
					<td class="bdlr vt tr">'.$this->rupiah($rm->pagu).'</td>
					<td class="bdlr vt tr">'.$this->rupiah($rm->realisasi).'</td>
					<td class="bdlr vt tr">'.$this->rupiah($rm->dimintakan).'</td>
					<td class="bdlr vt tr">'.$this->rupiah($rm->sisa).'</td>
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
					<!--<td class="bdlr">&nbsp;</td>
					<td class="bdlr">&nbsp;</td>-->
					<td class="bdlr vt tr">'.$this->rupiah($ru->pagu).'</td>
					<td class="bdlr vt tr">'.$this->rupiah($ru->realisasi).'</td>
					<td class="bdlr vt tr">'.$this->rupiah($ru->dimintakan).'</td>
					<td class="bdlr vt tr">'.$this->rupiah($ru->sisa).'</td>
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
					<td class="bdt" colspan="4">&nbsp;</td>
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
		$obj_ppk = HTMLController::refPPK();
		$obj_bpp = HTMLController::refBPP();
		$html_out.= '<table border="0" cellpadding="2" cellspacing="-1" class="fz50">
			<tbody>
				<tr>
					<td class="wd30">&nbsp;</td>
					<td class="wd40">&nbsp;</td>
					<td class="wd30 tl">Jakarta, '.HTMLController::today().'</td>
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
					<td class="wd30">'.$obj_ppk['nmppk'].'</td>
					<td class="wd40">&nbsp;</td>
					<td class="wd30">'.$obj_bpp['nmbpp'].'</td>
				</tr>
				<tr>
					<td class="wd30">NIP '.$obj_ppk['nipppk'].'</td>
					<td class="wd40">&nbsp;</td>
					<td class="wd30">NIP '.$obj_bpp['nipbpp'].'</td>
				</tr>
			</tbody>
		</table>';
		//~ return $html_out;
		$mpdf = new mPDF("en", "A4", "15");
		$mpdf->SetTitle('Form RKO_UP');
		$mpdf->AddPage('P');
		$mpdf->writeHTML($html_out);
		$mpdf->Output('Rincian_RKO_UP_TUP_'.$id.'.pdf','I');
	}
	
	/**
	 * description 
	 */
	public function rupiah($angka)
	{
		return HTMLController::rupiah($angka, 0, ',', '.');
	}
}
