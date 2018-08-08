<?php namespace App\Http\Controllers;

use DB;
use Session;
use Storage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaguController extends Controller {

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
			$aColumns = array(
				'kode','kdppk','kdindex','lvl','kddept','kdunit','kdsatker','nodok','tgdok','revisike','norevisi','tgrevisi','kdprogram',
				'kdgiat','kdoutput','kdsoutput','kdkmpnen','kdskmpnen','kdakun','uraian','paguakhir','nilblokir','identif'
			);
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "identif";
			/* DB table to use */

			$sTable = " 
				SELECT t.*
				  FROM (SELECT kdindex,
				               kode,
							   kdppk,
							   lvl,
							   kddept,
							   kdunit,
							   kdsatker,
							   nodok,
							   tgdok,
							   revisike,
							   norevisi,
							   tgrevisi,
							   kdprogram,
							   kdgiat,
							   kdoutput,
							   kdsoutput,
							   kdkmpnen,
							   kdskmpnen,
							   kdakun,
							   TRIM(uraian) AS uraian,
							   paguakhir,
							   nilblokir,
							   CONCAT(kdprogram,
									   '-',
									   kdgiat,
									   '-',
									   kdoutput,
									   '-',
									   kdsoutput,
									   '-',
									   kdkmpnen,
									   '-',
									   kdskmpnen,
									   '-',
									   kdakun)
								  AS identif
						  FROM d_pagu
						 WHERE kdsatker='".session('kdsatker')."' and thang='".session('tahun')."' and lvl = 7) t";
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
				$aksi='<center>
							<!--<a href="javascript:;" id="'.$row->kdindex.'" title="Ubah data" class="btn btn-xs btn-primary ubah"><i class="fa fa-pencil"></i></a>
							<a href="javascript:;" id="'.$row->kdindex.'" title="Hapus" class="btn btn-xs btn-danger hapus"><i class="fa fa-times"></i></a>-->
						</center>';
							
				$output['aaData'][] = array(
					$row->kode,
					$row->kdppk,
					//~ $row->kddept,
					//~ $row->kdunit,
					//~ $row->kdsatker,
					$row->revisike,
					$row->kdprogram,
					$row->kdgiat,
					$row->kdoutput,
					$row->kdsoutput,
					$row->kdkmpnen,
					$row->kdskmpnen,
					$row->kdakun,
					'<div style="text-align:right;">'.number_format($row->paguakhir,0,',','.').'</div>',
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
	
	public function hapus_sesi_upload(Request $request)
	{
		try{
			session(['upload_pagu' => null]);
			return 'success';
		}
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function hapus_sesi_upload_revisi(Request $request)
	{
		try{
			session(['upload_pagu_rev' => null]);
			return 'success';
		}
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function upload(Request $request)
	{
		try{
			session(['upload_pagu' => null]);
		
			$targetFolder = 'data\adk\pagu\\'; // Relative to the root
			
			if(!empty($_FILES)) {
				$file_name = $_FILES['file']['name'];
				$tempFile = $_FILES['file']['tmp_name'];
				$targetFile = $targetFolder.$file_name;
				$fileTypes = array('zip','ZIP'); // File extensions
				$fileParts = pathinfo($_FILES['file']['name']);
				$fileSize = $_FILES['file']['size'];
				//type file sesuai..??	
				if(in_array($fileParts['extension'],$fileTypes)) {
					
					//isi kosong..??
					if($fileSize>0){
						
						$file_name_baru=$file_name;
						move_uploaded_file($tempFile,$targetFolder.$file_name_baru);
						
						if(file_exists($targetFolder.$file_name_baru)){
							session(['upload_pagu' => $file_name_baru]);
							return '1';
						}
						else{
							return 'ADK gagal diupload!';
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
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function upload_revisi(Request $request)
	{
		try{
			session(['upload_pagu_rev' => null]);
		
			$targetFolder = 'data\adk\pagu\\'; // Relative to the root
			
			if(!empty($_FILES)) {
				$file_name = $_FILES['file']['name'];
				$tempFile = $_FILES['file']['tmp_name'];
				$targetFile = $targetFolder.$file_name;
				$fileTypes = array('zip','ZIP'); // File extensions
				$fileParts = pathinfo($_FILES['file']['name']);
				$fileSize = $_FILES['file']['size'];
				//type file sesuai..??	
				if(in_array($fileParts['extension'],$fileTypes)) {
					
					//isi kosong..??
					if($fileSize>0){
						
						$now = new \DateTime();
						$tglupload = $now->format('YmdHis');
						
						$file_name_baru=$file_name;
						move_uploaded_file($tempFile,$targetFolder.$file_name_baru);
						
						if(file_exists($targetFolder.$file_name_baru)){
							session(['upload_pagu_rev' => $file_name_baru]);
							return '1';
						}
						else{
							return 'ADK gagal diupload!';
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
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function simpan(Request $request)
	{
		try{
			if(session('upload_pagu')!==null){
				$table_name = 'd_pagu';
				$path = 'data/adk/pagu/'.session('upload_pagu');
				$arr_file = explode(".", session('upload_pagu'));
				$nmfile = $arr_file[0].'.json';
				
				//ekstrak zip
				$zip = new \ZipArchive;
				$res = $zip->open($path);
			
				if ($res === TRUE) {
					
					$zip->extractTo('data/adk/pagu/');
					$zip->close();
					
					$json = json_decode(file_get_contents('data/adk/pagu/'.$nmfile), true);
					
					if($json){
						
						//mencari nama key dari file json
						for($i = 0; $i < count($json); $i++) {
							$j = count($json[$i]);
							$arrcolumns = array_keys($json[$i]);
						} 
						
						$column_name = implode(", ", $arrcolumns);
						
						//mencari nilai yang akan dimasukkan ke kolom suatu tabel
						for($x = 0; $x < count($json); $x++) {
							$y = count($json[$x]);
							$arrvalues[] = array_values($json[$x]);
							$arv[] = "('".implode("', '",$arrvalues[$x])."')";
						} 
						
						$data_values = implode(", ", $arv);
						
						DB::beginTransaction();
						
						//query untuk pengosongan tabel
						$truncate = "DELETE FROM ".$table_name." WHERE kdsatker='".session('kdsatker')."' AND thang='".session('tahun')."'";
						$sql_truncate = DB::delete($truncate);
						
						//query rekam ke tabel
						$insert = "INSERT INTO ".$table_name." (".$column_name.") VALUES ".$data_values;
						
						//eksekusi query
						$sql_insert = DB::insert($insert);
						
						if($sql_insert){
							DB::commit();
							return 'success';
						}
						else{
							return 'Data gagal disimpan!';
						}
						
					}
					else{
						return 'Proses ekstrak gagal!';
					}
				
				}
				else{
					return 'ADK ZIP gagal diekstrak!';
				}
				
			}
			else{
				return 'Anda belum mengupload file pagu!';
			}
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function simpan_revisi(Request $request)
	{
		try{
			if(session('upload_pagu_rev')!==null){
				
				$table_name = 'h_pagu';
				$path = 'data/adk/pagu/'.session('upload_pagu_rev');
				$arr_file = explode(".", session('upload_pagu_rev'));
				$nmfile = $arr_file[0].'.json';
				
				//ekstrak zip
				$zip = new \ZipArchive;
				$res = $zip->open($path);
			
				if ($res === TRUE) {
					
					$zip->extractTo('data/adk/pagu/');
					$zip->close();
					
					$json = json_decode(file_get_contents('data/adk/pagu/'.$nmfile), true);
				
					if($json){
						
						//var_dump($json);
						
						//mencari nama key dari file json
						for($i = 0; $i < count($json); $i++) {
							$j = count($json[$i]);
							$arrcolumns = array_keys($json[$i]);
						} 
						
						$column_name = implode(", ", $arrcolumns);
						
						//mencari nilai yang akan dimasukkan ke kolom suatu tabel
						for($x = 0; $x < count($json); $x++) {
							$y = count($json[$x]);
							$arrvalues[] = array_values($json[$x]);
							$arv[] = "('".implode("', '",$arrvalues[$x])."')";
						} 
						
						$data_values = implode(", ", $arv);
						
						DB::beginTransaction();
						
						//query untuk pengosongan tabel
						$truncate = "DELETE FROM ".$table_name." WHERE kdsatker='".session('kdsatker')."' AND thang='".session('tahun')."'";
						$sql_truncate = DB::delete($truncate);
						
						//query rekam ke tabel
						$insert = "INSERT INTO ".$table_name." (".$column_name.") VALUES ".$data_values;
						
						//eksekusi query
						$sql_insert = DB::insert($insert);
						
						if($sql_insert){
							DB::commit();
							return 'success';
						}
						else{
							return 'Data gagal disimpan!';
						}
						
					}
					else {
						return 'Hasil ekstrak kosong atau ekstrak gagal!';
					}
					
				}
				else{
					return 'ADK ZIP gagal diekstrak!';
				}

			}
			else {
				return 'Anda belum mengupload file pagu!';
			}
		} catch(\Exception $e) {
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function getBreakdown(Request $request, $param)
	{
		try{
			if($param=='xxx'){ //get program
				return $this->program(session('kddept'),session('kdunit'),session('kdsatker'),session('tahun'),session('kdppk'));
			}
			else{
				$param=explode("-", $param);
				if(count($param)==1){//get kegiatan
					return $this->kegiatan(session('kddept'),session('kdunit'),session('kdsatker'),session('tahun'),$param[0],session('kdppk')); //param=program
				}
				elseif(count($param)==2){
					return $this->output(session('kddept'),session('kdunit'),session('kdsatker'),session('tahun'),$param[0],$param[1],session('kdppk')); //param=program-kegiatan
				}
				elseif(count($param)==3){
					return $this->soutput(session('kddept'),session('kdunit'),session('kdsatker'),session('tahun'),$param[0],$param[1],$param[2],session('kdppk')); //param=program-kegiatan-output
				}
				elseif(count($param)==4){
					return $this->komponen(session('kddept'),session('kdunit'),session('kdsatker'),session('tahun'),$param[0],$param[1],$param[2],$param[3],session('kdppk')); //param=program-kegiatan-output-soutput
				}
				elseif(count($param)==5){
					return $this->skomponen(session('kddept'),session('kdunit'),session('kdsatker'),session('tahun'),$param[0],$param[1],$param[2],$param[3],$param[4],session('kdppk')); //param=program-kegiatan-output-soutput-kompenen
				}
				elseif(count($param)==6){
					return $this->akun(session('kddept'),session('kdunit'),session('kdsatker'),session('tahun'),$param[0],$param[1],$param[2],$param[3],$param[4],$param[5],session('kdppk')); //param=program-kegiatan-output-soutput-kompenen-skompnen
				}
			}
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function program($kddept, $kdunit, $kdsatker, $tahun, $kdppk)
	{
		$rows = DB::select("
			SELECT	a.kdprogram as kode,
					ifnull(b.uraian,'Referensi tidak ada') as uraian,
					a.jumlah,
					c.nilai as realisasi
			FROM(
				SELECT	a.*
				FROM(
					SELECT 	kddept,
							kdunit,
							kdsatker,
							thang,
							kdprogram,
							SUM(paguakhir) AS jumlah
					FROM d_pagu
					WHERE lvl=7 AND kddept=? AND kdunit=? AND kdsatker=? AND thang=? AND kdppk=?
					GROUP BY kddept,kdunit,kdsatker,thang,kdprogram
					ORDER BY kddept,kdunit,kdsatker,thang,kdprogram
				) a
			) a
			LEFT OUTER JOIN(
				SELECT kdprogram,uraian FROM d_pagu WHERE lvl=1 AND kdsatker=? AND thang=?
			) b ON(a.kdprogram=b.kdprogram)
			LEFT OUTER JOIN(
				SELECT	b.kdsatker,
					b.thang,
					a.kdprogram,
					SUM(a.nilai) AS nilai
				FROM d_rko_pagu2 a
				LEFT OUTER JOIN d_rko b ON(a.id_rko=b.id)
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
				) c ON(a.id_rko=c.id_rko)
				WHERE c.nourut>=4
				GROUP BY b.kdsatker,b.thang,a.kdprogram
			) c ON(a.kdsatker=c.kdsatker AND a.thang=c.thang AND a.kdprogram=c.kdprogram)
		", [
			$kddept,
			$kdunit,
			$kdsatker,
			$tahun,
			$kdppk,
			$kdsatker,
			$tahun
		]);
		
		if(count($rows)>0){
			$data='';
			foreach($rows as $row){
				$data.='<li style="list-style: none; margin-left: 0;">
							<div class="row">
								<div class="col-lg-1">'.$row->kode.'</div>
								<div class="col-lg-4">
									<a href="javascript:;" id="'.$row->kode.'" class="breakdown">'.$row->uraian.'</a>
								</div>
								<div class="col-lg-2" style="text-align:right;">'.number_format($row->jumlah).'</div>
								<div class="col-lg-2" style="text-align:right;">'.number_format($row->realisasi).'</div>
								<div class="col-lg-2" style="text-align:right;">'.number_format($row->jumlah-$row->realisasi).'</div>
								<div class="col-lg-1" style="text-align:right;"><!--<a href="javascript:;" id="'.$row->kode.'" class="pilih"><span class="label label-primary"><i class="fa fa-pencil"></i></span></a>--></div>
							</div>
						</li>
						<ul id="ul-'.$row->kode.'"></ul>
						<hr>';
			}
			
			return $data;
		}
		else{
			return 'Data tidak ditemukan!';
		}
	}
	
	public function kegiatan($kddept, $kdunit, $kdsatker, $tahun, $kdprogram, $kdppk)
	{
		$rows = DB::select("
			SELECT	concat(a.kdprogram,'-',a.kdgiat) as kode,
					a.kdprogram,
					a.kdgiat,
					ifnull(b.uraian,'Referensi tidak ada') as uraian,
					a.jumlah,
					c.nilai as realisasi
			FROM(
				SELECT	kdsatker,
						thang,
						kdprogram,
						kdgiat,
						SUM(paguakhir) AS jumlah
				FROM d_pagu
				WHERE  lvl=7 AND	kddept=? AND kdunit=? AND kdsatker=? AND thang=? AND
						kdprogram=? AND kdppk=?
				GROUP BY kdsatker,thang,kdprogram,kdgiat
				ORDER BY kdsatker,thang,kdprogram,kdgiat
			) a
			LEFT OUTER JOIN (
				SELECT kdprogram,kdgiat,uraian FROM d_pagu WHERE lvl=2 AND kdsatker=? AND thang=?
			) b ON(a.kdprogram=b.kdprogram and a.kdgiat=b.kdgiat)
			LEFT OUTER JOIN (
				SELECT	b.kdsatker,
						b.thang,
						a.kdprogram,
						a.kdgiat,
						SUM(a.nilai) AS nilai
					FROM d_rko_pagu2 a
					LEFT OUTER JOIN d_rko b ON(a.id_rko=b.id)
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
					) c ON(a.id_rko=c.id_rko)
					WHERE c.nourut>=4
					GROUP BY b.kdsatker,b.thang,a.kdprogram,a.kdgiat
			) c ON(a.kdsatker=c.kdsatker AND a.thang=c.thang AND a.kdprogram=c.kdprogram AND a.kdgiat=c.kdgiat)
		", [
			$kddept,
			$kdunit,
			$kdsatker,
			$tahun,
			$kdprogram,
			$kdppk,
			$kdsatker,
			$tahun
		]);
		
		$data='';
		foreach($rows as $row){
			$data.='<li style="list-style: none; margin-left: 15px;">
						<div class="row">
							<div class="col-lg-1">'.$row->kdgiat.'</div>
							<div class="col-lg-4">
								<a href="javascript:;" id="'.$row->kode.'" class="breakdown">'.$row->uraian.'</a>
							</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->jumlah).'</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->realisasi).'</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->jumlah-$row->realisasi).'</div>
							<div class="col-lg-1" style="text-align:right;"><!--<a href="javascript:;" id="'.$row->kode.'" class="pilih"><span class="label label-primary"><i class="fa fa-pencil"></i></span></a>--></div>
						</div>
					</li>
					<ul id="ul-'.$row->kode.'"></ul>
					<hr>';
		}
		
		return $data;
	}
	
	public function output($kddept, $kdunit, $kdsatker, $tahun, $kdprogram, $kdgiat, $kdppk)
	{
		$rows = DB::select("
			SELECT	concat(a.kdprogram,'-',a.kdgiat,'-',a.kdoutput) as kode,
					a.kdprogram,
					a.kdgiat,
					a.kdoutput,
					ifnull(b.uraian,'Referensi tidak ada') as uraian,
					a.jumlah,
					c.nilai as realisasi
			FROM(
				SELECT	kdsatker,
						thang,
						kdprogram,
						kdgiat,
						kdoutput,
						SUM(paguakhir) AS jumlah
				FROM d_pagu
				WHERE  lvl=7 AND	kddept=? AND kdunit=? AND kdsatker=? AND thang=? AND
					kdprogram=? AND kdgiat=? AND kdppk=?
				GROUP BY kdsatker,thang,kdprogram,kdgiat,kdoutput
				ORDER BY kdsatker,thang,kdprogram,kdgiat,kdoutput
			) a
			LEFT OUTER JOIN(
				SELECT kdprogram,kdgiat,kdoutput,kdlokasi,kdkabkota,uraian FROM d_pagu WHERE lvl=3 AND kdsatker=? AND thang=?
			) b ON(a.kdprogram=b.kdprogram and a.kdgiat=b.kdgiat AND a.kdoutput=b.kdoutput)
			LEFT OUTER JOIN (
				SELECT	b.kdsatker,
						b.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						SUM(a.nilai) AS nilai
					FROM d_rko_pagu2 a
					LEFT OUTER JOIN d_rko b ON(a.id_rko=b.id)
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
					) c ON(a.id_rko=c.id_rko)
					WHERE c.nourut>=4
					GROUP BY b.kdsatker,b.thang,a.kdprogram,a.kdgiat,a.kdoutput
			) c ON(a.kdsatker=c.kdsatker AND a.thang=c.thang AND a.kdprogram=c.kdprogram AND a.kdgiat=c.kdgiat AND a.kdoutput=c.kdoutput)
		", [
			$kddept,
			$kdunit,
			$kdsatker,
			$tahun,
			$kdprogram,
			$kdgiat,
			$kdppk,
			$kdsatker,
			$tahun
		]);
		
		$data='';
		foreach($rows as $row){
			$data.='<li style="list-style: none; margin-left: 30px;">
						<div class="row">
							<div class="col-lg-1">'.$row->kdoutput.'</div>
							<div class="col-lg-4">
								<a href="javascript:;" id="'.$row->kode.'" class="breakdown">'.$row->uraian.'</a>
							</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->jumlah).'</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->realisasi).'</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->jumlah-$row->realisasi).'</div>
							<div class="col-lg-1" style="text-align:right;"><!--<a href="javascript:;" id="'.$row->kode.'" class="pilih"><span class="label label-primary"><i class="fa fa-pencil"></i></span></a>--></div>
						</div>
					</li>
					<ul id="ul-'.$row->kode.'"></ul>
					<hr>';
		}
		
		return $data;
	}
	
	public function soutput($kddept, $kdunit, $kdsatker, $tahun, $kdprogram, $kdgiat, $kdoutput, $kdppk)
	{
		$rows = DB::select("
			SELECT	concat(a.kdprogram,'-',a.kdgiat,'-',a.kdoutput,'-',a.kdsoutput) as kode,
					a.kdprogram,
					a.kdgiat,
					a.kdoutput,
					a.kdsoutput,
					ifnull(b.uraian,'Referensi tidak ada') as uraian,
					a.jumlah,
					c.nilai as realisasi
			FROM(
				SELECT	kdsatker,
						thang,
						kdprogram,
						kdgiat,
						kdoutput,
						kdsoutput,
						SUM(paguakhir) AS jumlah
				FROM d_pagu
				WHERE  lvl=7 AND	kddept=? AND kdunit=? AND kdsatker=? AND thang=? AND
					kdprogram=? AND kdgiat=? AND kdoutput=? AND kdppk=?
				GROUP BY kdsatker,thang,kdprogram,kdgiat,kdoutput,kdsoutput
				ORDER BY kdsatker,thang,kdprogram,kdgiat,kdoutput,kdsoutput
			) a
			LEFT OUTER JOIN(
				SELECT kdprogram,kdgiat,kdoutput,kdlokasi,kdkabkota,kdsoutput,uraian FROM d_pagu WHERE lvl=4 AND kdsatker=? AND thang=?
			) b ON(a.kdprogram=b.kdprogram and a.kdgiat=b.kdgiat AND a.kdoutput=b.kdoutput AND
							a.kdsoutput=b.kdsoutput)
			LEFT OUTER JOIN (
				SELECT	b.kdsatker,
						b.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						SUM(a.nilai) AS nilai
					FROM d_rko_pagu2 a
					LEFT OUTER JOIN d_rko b ON(a.id_rko=b.id)
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
					) c ON(a.id_rko=c.id_rko)
					WHERE c.nourut>=4
					GROUP BY b.kdsatker,b.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput
			) c ON(a.kdsatker=c.kdsatker AND a.thang=c.thang AND a.kdprogram=c.kdprogram AND a.kdgiat=c.kdgiat AND a.kdoutput=c.kdoutput AND a.kdsoutput=c.kdsoutput)
		", [
			$kddept,
			$kdunit,
			$kdsatker,
			$tahun,
			$kdprogram,
			$kdgiat,
			$kdoutput,
			$kdppk,
			$kdsatker,
			$tahun
		]);
		
		$data='';
		foreach($rows as $row){
			$data.='<li style="list-style: none; margin-left: 45px;">
						<div class="row">
							<div class="col-lg-1">'.$row->kdsoutput.'</div>
							<div class="col-lg-4">
								<a href="javascript:;" id="'.$row->kode.'" class="breakdown">'.$row->uraian.'</a>
							</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->jumlah).'</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->realisasi).'</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->jumlah-$row->realisasi).'</div>
							<div class="col-lg-1" style="text-align:right;"><!--<a href="javascript:;" id="'.$row->kode.'" class="pilih"><span class="label label-primary"><i class="fa fa-pencil"></i></span></a>--></div>
						</div>
					</li>
					<ul id="ul-'.$row->kode.'"></ul>
					<hr>';
		}
		
		return $data;
	}
	
	public function komponen($kddept, $kdunit, $kdsatker, $tahun, $kdprogram, $kdgiat, $kdoutput, $kdsoutput, $kdppk)
	{
		$rows = DB::select("
			SELECT	CONCAT(a.kdprogram,'-',a.kdgiat,'-',a.kdoutput,'-',a.kdsoutput,'-',a.kdkmpnen) AS kode,
					a.kdprogram,
					a.kdgiat,
					a.kdoutput,
					a.kdsoutput,
					a.kdkmpnen,
					ifnull(b.uraian,'Referensi tidak ada') AS uraian,
					a.jumlah,
					c.nilai as realisasi
			FROM(
				SELECT	kdsatker,
						thang,
						kdprogram,
						kdgiat,
						kdoutput,
						kdsoutput,
						kdkmpnen,
						SUM(paguakhir) AS jumlah
				FROM d_pagu
				WHERE  lvl=7 AND	kddept=? AND kdunit=? AND kdsatker=? AND thang=? AND
					kdprogram=? AND kdgiat=? AND kdoutput=? AND kdsoutput=? AND kdppk=?
				GROUP BY kdsatker,thang,kdprogram,kdgiat,kdoutput,kdsoutput,kdkmpnen
				ORDER BY kdsatker,thang,kdprogram,kdgiat,kdoutput,kdsoutput,kdkmpnen
			) a
			LEFT OUTER JOIN(
				SELECT kdprogram,kdgiat,kdoutput,kdlokasi,kdkabkota,kdsoutput,kdkmpnen,uraian FROM d_pagu WHERE lvl=5 AND kdsatker=? AND thang=?
			) b ON(a.kdprogram=b.kdprogram and a.kdgiat=b.kdgiat AND
					a.kdoutput=b.kdoutput AND a.kdsoutput=b.kdsoutput AND a.kdkmpnen=b.kdkmpnen)
			LEFT OUTER JOIN (
				SELECT	b.kdsatker,
						b.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						a.kdkmpnen,
						SUM(a.nilai) AS nilai
					FROM d_rko_pagu2 a
					LEFT OUTER JOIN d_rko b ON(a.id_rko=b.id)
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
					) c ON(a.id_rko=c.id_rko)
					WHERE c.nourut>=4
					GROUP BY b.kdsatker,b.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen,a.kdkmpnen
			) c ON(a.kdsatker=c.kdsatker AND a.thang=c.thang AND a.kdprogram=c.kdprogram AND a.kdgiat=c.kdgiat AND a.kdoutput=c.kdoutput AND a.kdsoutput=c.kdsoutput AND a.kdkmpnen=c.kdkmpnen)
		", [
			$kddept,
			$kdunit,
			$kdsatker,
			$tahun,
			$kdprogram,
			$kdgiat,
			$kdoutput,
			$kdsoutput,
			$kdppk,
			$kdsatker,
			$tahun
		]);
		
		$data='';
		foreach($rows as $row){
			$data.='<li style="list-style: none; margin-left: 60px;">
						<div class="row">
							<div class="col-lg-1">'.$row->kdkmpnen.'</div>
							<div class="col-lg-4">
								<a href="javascript:;" id="'.$row->kode.'" class="breakdown">'.$row->uraian.'</a>
							</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->jumlah).'</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->realisasi).'</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->jumlah-$row->realisasi).'</div>
							<div class="col-lg-1" style="text-align:right;"><!--<a href="javascript:;" id="'.$row->kode.'" class="pilih"><span class="label label-primary"><i class="fa fa-pencil"></i></span></a>--></div>
						</div>
					</li>
					<ul id="ul-'.$row->kode.'"></ul>
					<hr>';
		}
		
		return $data;
	}
	
	public function skomponen($kddept, $kdunit, $kdsatker, $tahun, $kdprogram, $kdgiat, $kdoutput, $kdsoutput, $kdkmpnen, $kdppk)
	{
		$rows = DB::select("
			SELECT	CONCAT(a.kdprogram,'-',a.kdgiat,'-',a.kdoutput,'-',a.kdsoutput,'-',a.kdkmpnen,'-',trim(a.kdskmpnen)) AS kode,
					a.kdprogram,
					a.kdgiat,
					a.kdoutput,
					a.kdsoutput,
					a.kdkmpnen,
					a.kdskmpnen,
					ifnull(b.uraian,'Referensi tidak ada') AS uraian,
					a.jumlah,
					c.nilai as realisasi
			FROM(
				SELECT	kdsatker,
						thang,
						kdprogram,
						kdgiat,
						kdoutput,
						kdsoutput,
						kdkmpnen,
						kdskmpnen,
						SUM(paguakhir) AS jumlah
				FROM d_pagu
				WHERE  lvl=7 AND	kddept=? AND kdunit=? AND kdsatker=? AND thang=? AND
					kdprogram=? AND kdgiat=? AND kdoutput=? AND kdsoutput=? AND
					kdkmpnen=? AND kdppk=?
				GROUP BY kdsatker,thang,kdprogram,kdgiat,kdoutput,kdsoutput,kdkmpnen,kdskmpnen
				ORDER BY kdsatker,thang,kdprogram,kdgiat,kdoutput,kdsoutput,kdkmpnen,kdskmpnen
			) a
			LEFT OUTER JOIN(
				SELECT kdprogram,kdgiat,kdoutput,kdlokasi,kdkabkota,kdsoutput,kdkmpnen,kdskmpnen,uraian FROM d_pagu WHERE lvl=6 AND kdsatker=? AND thang=?
			) b ON(a.kdprogram=b.kdprogram AND a.kdgiat=b.kdgiat AND
					a.kdoutput=b.kdoutput AND a.kdsoutput=b.kdsoutput AND a.kdkmpnen=b.kdkmpnen AND
					a.kdskmpnen=b.kdskmpnen)
			LEFT OUTER JOIN (
				SELECT	b.kdsatker,
						b.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						a.kdkmpnen,
						a.kdskmpnen,
						SUM(a.nilai) AS nilai
					FROM d_rko_pagu2 a
					LEFT OUTER JOIN d_rko b ON(a.id_rko=b.id)
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
					) c ON(a.id_rko=c.id_rko)
					WHERE c.nourut>=4
					GROUP BY b.kdsatker,b.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen,a.kdkmpnen,a.kdskmpnen
			) c ON(a.kdsatker=c.kdsatker AND a.thang=c.thang AND a.kdprogram=c.kdprogram AND a.kdgiat=c.kdgiat AND a.kdoutput=c.kdoutput AND a.kdsoutput=c.kdsoutput AND a.kdkmpnen=c.kdkmpnen AND a.kdskmpnen=c.kdskmpnen)
		", [
			$kddept,
			$kdunit,
			$kdsatker,
			$tahun,
			$kdprogram,
			$kdgiat,
			$kdoutput,
			$kdsoutput,
			$kdkmpnen,
			$kdppk,
			$kdsatker,
			$tahun
		]);
		
		$data='';
		foreach($rows as $row){
			$data.='<li style="list-style: none; margin-left: 75px;">
						<div class="row">
							<div class="col-lg-1">'.$row->kdskmpnen.'</div>
							<div class="col-lg-4">
								<a href="javascript:;" id="'.$row->kode.'" class="breakdown">'.$row->uraian.'</a>
							</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->jumlah).'</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->realisasi).'</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->jumlah-$row->realisasi).'</div>
							<div class="col-lg-1" style="text-align:right;"><!--<a href="javascript:;" id="'.$row->kode.'" class="pilih"><span class="label label-primary"><i class="fa fa-pencil"></i></span></a>--></div>
						</div>
					</li>
					<ul id="ul-'.$row->kode.'"></ul>
					<hr>';
		}
		
		return $data;
	}
	
	public function akun($kddept, $kdunit, $kdsatker, $tahun, $kdprogram, $kdgiat, $kdoutput, $kdsoutput, $kdkmpnen, $kdskmpnen, $kdppk)
	{
		$rows = DB::select("
			select	a.*,
					nmjnsbelanja,
					c.nilai as realisasi
			from(
				SELECT	a.*,
						b.nmmak
				FROM(
					SELECT	kdsatker,
							thang,
							CONCAT(kdprogram,'-',kdgiat,'-',kdoutput,'-',kdsoutput,'-',kdkmpnen,'-',trim(kdskmpnen),'-',kdakun) AS kode,
							kdprogram,
							kdgiat,
							kdoutput,
							kdsoutput,
							kdkmpnen,
							kdskmpnen,
							kdakun,
							SUM(paguakhir) AS jumlah
					FROM d_pagu
					WHERE  lvl=7 AND	kddept=? AND kdunit=? AND kdsatker=? AND thang=? AND
							kdprogram=? AND kdgiat=? AND kdoutput=? AND kdsoutput=? AND
							kdkmpnen=? AND trim(kdskmpnen)=? AND kdppk=?
					GROUP BY kdsatker,thang,kdprogram,kdgiat,kdoutput,kdsoutput,kdkmpnen,kdskmpnen,kdakun
				) a
				LEFT OUTER JOIN(
					SELECT	DISTINCT kdmak,nmmak
					FROM t_mak
				) b ON(a.kdakun=b.kdmak)
			) a
			left outer join t_jnsbelanja b on(a.kdakun=b.jnsbelanja)
			LEFT OUTER JOIN (
				SELECT	b.kdsatker,
						b.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						a.kdkmpnen,
						a.kdskmpnen,
						a.kdakun,
						SUM(a.nilai) AS nilai
					FROM d_rko_pagu2 a
					LEFT OUTER JOIN d_rko b ON(a.id_rko=b.id)
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
					) c ON(a.id_rko=c.id_rko)
					WHERE c.nourut>=4
					GROUP BY b.kdsatker,b.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen,a.kdkmpnen,a.kdskmpnen,a.kdakun
			) c ON(a.kdsatker=c.kdsatker AND a.thang=c.thang AND a.kdprogram=c.kdprogram AND a.kdgiat=c.kdgiat AND a.kdoutput=c.kdoutput AND a.kdsoutput=c.kdsoutput AND a.kdkmpnen=c.kdkmpnen AND a.kdskmpnen=c.kdskmpnen AND a.kdakun=c.kdakun)
		", [
			$kddept,
			$kdunit,
			$kdsatker,
			$tahun,
			$kdprogram,
			$kdgiat,
			$kdoutput,
			$kdsoutput,
			$kdkmpnen,
			$kdskmpnen,
			$kdppk
		]);
		
		$data='';
		foreach($rows as $row){
			$data.='<li style="list-style: none; margin-left: 90px;">
						<div class="row">
							<div class="col-lg-1">'.$row->kdakun.'</div>
							<div class="col-lg-4">
								<a href="javascript:;" id="'.$row->kdakun.'" class="breakdown">'.$row->nmmak.'</a>
							</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->jumlah).'</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->realisasi).'</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->jumlah-$row->realisasi).'</div>
							<div class="col-lg-1" style="text-align:right;"><a href="javascript:;" id="'.$row->kode.'.'.number_format($row->jumlah-$row->realisasi).'" class="pilih"><span class="label label-primary"><i class="fa fa-pencil"></i></span></a></div>
						</div>
					</li>
					<ul id="ul-'.$row->kode.'"></ul>
					<hr>';
		}
		
		return $data;
	}
	
	public function revisi(Request $request)
	{
		try{
			$aColumns = array(
				'kode','kdppk','kdindex','lvl','kddept','kdunit','kdsatker','nodok','tgdok','revisike','norevisi','tgrevisi','kdprogram',
				'kdgiat','kdoutput','kdsoutput','kdkmpnen','kdskmpnen','kdakun','uraian','paguakhir','nilblokir','identif'
			);
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "kode";
			/* DB table to use */

			$sTable = " 
				SELECT t.*
				  FROM (SELECT kdindex,
				               kode,
							   kdppk,
							   lvl,
							   kddept,
							   kdunit,
							   kdsatker,
							   nodok,
							   tgdok,
							   revisike,
							   norevisi,
							   tgrevisi,
							   kdprogram,
							   kdgiat,
							   kdoutput,
							   kdsoutput,
							   kdkmpnen,
							   kdskmpnen,
							   kdakun,
							   TRIM(uraian) AS uraian,
							   paguakhir,
							   nilblokir,
							   CONCAT(kdprogram,
									   '-',
									   kdgiat,
									   '-',
									   kdoutput,
									   '-',
									   kdsoutput,
									   '-',
									   kdkmpnen,
									   '-',
									   kdskmpnen,
									   '-',
									   kdakun)
								  AS identif
						  FROM h_pagu
						 WHERE kdsatker='".session('kdsatker')."' and thang='".session('tahun')."' and lvl = 7) t";
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
				$aksi='<center>
							<!--<a href="javascript:;" id="'.$row->kdindex.'" title="Ubah data" class="btn btn-xs btn-primary ubah"><i class="fa fa-pencil"></i></a>
							<a href="javascript:;" id="'.$row->kdindex.'" title="Hapus" class="btn btn-xs btn-danger hapus"><i class="fa fa-times"></i></a>-->
						</center>';
							
				$output['aaData'][] = array(
					$row->kode,
					$row->kdppk,
					//~ $row->kddept,
					//~ $row->kdunit,
					//~ $row->kdsatker,
					$row->revisike,
					$row->kdprogram,
					$row->kdgiat,
					$row->kdoutput,
					$row->kdsoutput,
					$row->kdkmpnen,
					$row->kdskmpnen,
					$row->kdakun,
					'<div style="text-align:right;">'.number_format($row->paguakhir,0,',','.').'</div>',
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
}
