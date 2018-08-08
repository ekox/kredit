<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransaksiRapatController extends Controller {

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
			$aColumns = array('id','id_rko','nosurat','tgsurat','tgrapat','jamrapat','jmlhari','jmljam','ket','status');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "	SELECT	id,
							id_rko,
							nosurat,
							DATE_FORMAT(tgsurat,'%d-%m-%Y') AS tgsurat,
							DATE_FORMAT(tgrapat,'%d-%m-%Y') AS tgrapat,
							jamrapat,
							jmlhari,
							jmljam,
							ket,
							if(ifnull(id_kuitansi,'')='','Belum','Sudah') as status
						FROM d_rapat a
						WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."'
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
									<li><a href="transaksi/rapat/'.$row->id.'/absen" target="_blank" title="Cetak data?">Cetak Daftar Absen</a></li>
									<li><a href="transaksi/rapat/'.$row->id.'/download" target="_blank" title="Cetak data?">Cetak Daftar Nominatif</a></li>
								</ul>
							</div>
							<div class="dropdown pull-right" style="height:1.5vw !important;">
								<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-pencil"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="javascript:;" id="'.$row->id.'" title="Ubah data?" class="ubah">Ubah Rapat</a></li>
									<li><a href="javascript:;" id="'.$row->id.'" title="Tambah detil?" class="tambah-detil">Tambah detil</a></li>
									<li><a href="javascript:;" id="'.$row->id.'" title="Hapus data?" class="hapus">Hapus Rapat</a></li>
								</ul>
							</div>
						</center>';
				
				$output['aaData'][] = array(
					$row->id,
					$row->id_rko,
					$row->nosurat,
					$row->tgsurat,
					$row->tgrapat,
					$row->jamrapat,
					$row->jmlhari,
					$row->jmljam,
					$row->ket,
					$row->status,
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
						id_rko,
						kdppk,
						kdlokasi,
						kdkabkota,
						nosurat,
						date_format(tgsurat, '%d-%m-%Y') as tgsurat,
						date_format(tgrapat, '%d-%m-%Y') as tgrapat,
						jamrapat,
						jmlhari,
						jmljam,
						ket
				FROM d_rapat
				WHERE id=?
			",[
				$id
			]);
			
			if(count($rows)==1){
				return response()->json($rows[0]);
			}
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function simpan(Request $request)
	{
		try{
			DB::beginTransaction();
			
			$update= DB::update("
				update d_rapat
				set kdlokasi=?,
					kdkabkota=?,
					nosurat=?,
					tgsurat=str_to_date(?, '%d-%m-%Y'),
					tgrapat=str_to_date(?, '%d-%m-%Y'),
					jamrapat=?,
					jmlhari=?,
					jmljam=?,
					ket=?
				where id=?
			",[
				$request->input('kdlokasi'),
				$request->input('kdkabkota'),
				$request->input('nosurat'),
				$request->input('tgsurat'),
				$request->input('tgrapat'),
				$request->input('jamrapat'),
				$request->input('jmlhari'),
				$request->input('jmljam'),
				$request->input('ket'),
				$request->input('inp-id')
			]);
			
			if($update){
				DB::commit();
				return 'success';
			}
			else{
				return 'Data gagal diubah!';
			}
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function simpan_detil(Request $request)
	{
		try{
			$pegawai = $request->input('pilih_pegawai');
			$arr_jabatan = $request->input('jabatan_pegawai');
			$arr_nilai = $request->input('nilai_pegawai');
			$arr_pajak = $request->input('pph21_pegawai');
			
			$pegawai1 = $request->input('pilih_pegawai1');
			$arr_jabatan1 = $request->input('jabatan_pegawai1');
			$arr_nilai1 = $request->input('nilai_pegawai1');
			$arr_pajak1 = $request->input('pph21_pegawai1');
			
			$instansi = '';
			
			DB::beginTransaction();
			
			$id_rapat = $request->input('id_rapat');
			
			if(count($pegawai)>0 || count($pegawai1)>0){
					
				$valid_peg=true;
				$valid_peg_non=true;
				
				//jika ada pegawai
				if(count($pegawai)>0){
					
					$arr_pegawai = array_keys($pegawai);
		
					$arr_data = array();
					for($i=0;$i<count($arr_pegawai);$i++){
						$arr_data[]=" (".$id_rapat.",'".$instansi."','".$arr_pegawai[$i]."','".$arr_jabatan[$arr_pegawai[$i]]."',".preg_replace("/[^0-9 \d]/i", "", $arr_nilai[$arr_pegawai[$i]]).",".preg_replace("/[^0-9 \d]/i", "", $arr_pajak[$arr_pegawai[$i]]).") ";
					}
					
					$query = "insert into d_rapat_detil(id_rapat,instansi,nip,kdjab,nilai,pajak)
							values".implode(",", $arr_data);
				
					$insert=DB::insert($query);
					
					$valid_peg = false;
					if($insert){
						$valid_peg = true;
					}
					
				}
				
				//jika ada non pegawai
				if(count($pegawai1)>0){
					
					$arr_pegawai1 = array_keys($pegawai1);
				
					$arr_data1 = array();
					for($i=0;$i<count($arr_pegawai1);$i++){
						$arr_data1[]=" (".$id_rapat.",'".$instansi."','".$arr_pegawai1[$i]."','".$arr_jabatan1[$arr_pegawai1[$i]]."',".preg_replace("/[^0-9 \d]/i", "", $arr_nilai1[$arr_pegawai1[$i]]).",".preg_replace("/[^0-9 \d]/i", "", $arr_pajak1[$arr_pegawai1[$i]]).") ";
					}
					
					$query1 = "insert into d_rapat_detil(id_rapat,instansi,id_peg_non,kdjab,nilai,pajak)
								values".implode(",", $arr_data1);
					
					$insert1=DB::insert($query1);
					
					$valid_peg_non = false;
					if($insert1){
						$valid_peg_non = true;
					}
				
				}
				
				if($valid_peg && $valid_peg_non){
					DB::commit();
					return 'success';
				}
				else{
					return 'Data gagal disimpan!';
				}
				
			}
			else{
				return 'Anda belum memilih pegawai!';
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
					delete from d_rapat_detil where id_rapat=?
				",[
					$request->input('id')
				]);
				
				$delete = DB::delete("
					delete from d_rapat where id=?
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
	
	public function hapus_detil(Request $request)
	{
		try{
			DB::beginTransaction();
			
			if(count($request->input('pilih_hapus'))){
				$arr_id = implode(",", $request->input('pilih_hapus'));
				
				$delete = DB::delete("
					delete from d_rapat_detil
					where id in(".$arr_id.")
				");
				
				if($delete){
					DB::commit();
					return 'success';
				}
				else{
					return 'Proses hapus gagal!';
				}
			}
			else{
				return 'Tidak ada data yang dipilih!';
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
					b.nmjab,
					if(d.id_rapat is null,'Belum terdaftar',concat('Sudah terdaftar : ',d.id_rapat)) as uraian_rapat,
					if(e.id_perjadin is null,'Belum terdaftar',concat('Sudah terdaftar : ',e.id_perjadin)) as uraian_perjadin
				FROM d_rapat_detil a
				LEFT OUTER JOIN t_jab_kegiatan b ON(a.kdjab=b.kdjab)
				LEFT OUTER JOIN t_pegawai c ON(a.nip=c.nip)
				LEFT OUTER JOIN(
					
					SELECT	a.nip,
						GROUP_CONCAT(a.id_rapat ORDER BY a.id_rapat SEPARATOR ',') AS id_rapat
					FROM(
						SELECT	a.id_rapat,
							a.nip,
							b.tgrapat AS tanggal1,
							DATE_ADD(b.tgrapat, INTERVAL b.jmlhari DAY) AS tanggal2
						FROM d_rapat_detil a
						LEFT OUTER JOIN d_rapat b ON(a.id_rapat=b.id)
						WHERE a.id_rapat<>?
					) a,
					(
						SELECT	tgrapat,
							jmlhari
						FROM d_rapat
						WHERE id=?
					) b
					WHERE 	a.tanggal1>=b.tgrapat AND
						a.tanggal1<DATE_ADD(b.tgrapat, INTERVAL b.jmlhari DAY) AND
						a.tanggal2>=b.tgrapat AND
						a.tanggal2<DATE_ADD(b.tgrapat, INTERVAL b.jmlhari DAY)
					GROUP BY a.nip
					
				) d ON(a.nip=d.nip)
				LEFT OUTER JOIN(
					
					SELECT	a.nip,
						GROUP_CONCAT(a.id_perjadin ORDER BY a.id_perjadin SEPARATOR ',') AS id_perjadin
					FROM(
						SELECT	a.id_perjadin,
							a.nip,
							a.tanggal AS tanggal1,
							DATE_ADD(a.tanggal, INTERVAL a.jmlhari DAY) AS tanggal2
						FROM d_perjadin_detil a
					) a,
					(
						SELECT	tgrapat,
							jmlhari
						FROM d_rapat
						WHERE id=?
					) b
					WHERE 	a.tanggal1>=b.tgrapat AND
						a.tanggal1<DATE_ADD(b.tgrapat, INTERVAL b.jmlhari DAY) AND
						a.tanggal2>=b.tgrapat AND
						a.tanggal2<DATE_ADD(b.tgrapat, INTERVAL b.jmlhari DAY)
					GROUP BY a.nip
					
				) e ON(a.nip=e.nip)
				WHERE a.id_rapat=?
				ORDER BY c.nama
			",[
				$id,
				$id,
				$id,
				$id
			]);
			
			if(count($rows)>0){
				
				$data=' <div class="pull-right">
							<button type="button" id="'.$id.'" class="btn btn-xs btn-danger hapus-semua"><i class="fa fa-trash-o"></i> Hapus</button>
							<button type="button" id="'.$id.'" class="btn btn-xs btn-primary pilih-semua"><i class="fa fa-check"></i> Check/Uncheck all</button>
						</div>
						<br>
						<form id="form-'.$id.'">
						<table class="table table-bordered">
						<thead>
							<tr>
								<th>No</th>
								<th>Nama</th>
								<th>NIP</th>
								<th>Duplikasi Rapat</th>
								<th>Duplikasi Perjadin</th>
								<th>Jabatan</th>
								<th>Nilai</th>
								<th>Hapus</th>
							</tr>
						</thead>
						<tbody>';
				
				$i=1;
				foreach($rows as $row){
					$data .= '<tr>
									<td>'.$i++.'</td>
									<td>'.$row->nama.'</td>
									<td>'.$row->nip.'</td>
									<td>'.$row->uraian_rapat.'</td>
									<td>'.$row->uraian_perjadin.'</td>
									<td>'.$row->nmjab.'</td>
									<td style="text-align:right;">'.number_format($row->nilai).'</td>
									<td style="text-align:center;"><input type="checkbox" name="pilih_hapus[]" class="pilih-hapus-'.$id.'" value="'.$row->id.'"></td>
								</tr>';
				}
				
				$data .= '</tbody></table></form>';
				
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
	
	public function cek_status($id_rapat)
	{
		try{
			$rows = DB::select("
				SELECT	count(*) as jml
				FROM d_rapat
				WHERE id=? and id_kuitansi is null
			",[
				$id_rapat
			]);
			
			if($rows[0]->jml==1){
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
						LEFT OUTER JOIN d_perjadin f ON(a.id=f.id_rko)
						WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."' AND a.kdalur IN('02') AND b.nourut=12 AND e.id_rko is null AND f.id_rko is null
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
				$aksi='<!--<center><a href="javascript:;" id="'.$row->id.'-'.$row->nospby.'-'.$row->kdppk.'" class="btn btn-xs btn-success pilih-rko"><i class="fa fa-check"></i></a></center>-->';
							
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
	
	public function tarikRKO(Request $request)
	{
		try{
			$arr_id = explode(".", $request->input('id'));
			$arr_rko = explode("-", $arr_id[0]);
			$arr_pagu = explode("-", $arr_id[1]);
			$id_rko = $arr_rko[0];
			$kdprogram = $arr_pagu[0];
			$kdgiat = $arr_pagu[1];
			$kdoutput = $arr_pagu[2];
			$kdsoutput = $arr_pagu[3];
			$kdkmpnen = $arr_pagu[4];
			$kdskmpnen = $arr_pagu[5];
			$kdakun = $arr_pagu[6];
			
			$rows = DB::select("
				select	id,
						tgrko,
						tanggal1,
						urrko,
						kdlokasi,
						kdkabkota,
						IFNULL(DATEDIFF(tanggal2,tanggal1)+1,0) AS jmlhari
				from d_rko
				where id=?
			",[
				$id_rko
			]);
			
			if(count($rows)>0){
				
				$now = new \DateTime();
				$created_at = $now->format('Y-m-d H:i:s');
				
				DB::beginTransaction();
				
				$id_rapat = DB::table('d_rapat')->insertGetId(
					array(
						'id_rko' => $rows[0]->id,
						'nosurat' => '',
						'tgsurat' => $rows[0]->tgrko,
						'tgrapat' => $rows[0]->tanggal1,
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
						'kdakun' => $kdakun,
						'ket' => $rows[0]->urrko,
						'kdlokasi' => $rows[0]->kdlokasi,
						'kdkabkota' => $rows[0]->kdkabkota,
						'jamrapat' => '17:00:00',
						'jmlhari' => $rows[0]->jmlhari,
						'jmljam' => 3,
						'id_user' => session('id_user'),
						'created_at' => $created_at,
						'updated_at' => $created_at
					)
				);
				
				if($id_rapat){
					
					$insert = DB::insert("
						insert into d_rapat_detil(id_rapat,nip,id_peg_non,kdjab,instansi,nilai,pajak,ket)
						select	? as id_rapat,
								a.nip,
								a.id_peg_non,
								a.kdjab,
								ifnull(b.instansi,c.instansi) as instansi,
								a.nilai,
								a.pph_21,
								'' as ket
						from d_rko_pagu2 a
						left outer join t_pegawai b on(a.nip=b.nip)
						left outer join t_pegawai_non c on(a.id_peg_non=c.id)
						where a.id_rko=? and a.kdspj='01' and a.kdprogram=? and a.kdgiat=? and a.kdoutput=? and a.kdsoutput=? and a.kdkmpnen=? and a.kdskmpnen=? and a.kdakun=?
					",[
						$id_rapat,
						$id_rko,
						$kdprogram,
						$kdgiat,
						$kdoutput,
						$kdsoutput,
						$kdkmpnen,
						$kdskmpnen,
						$kdakun
					]);
					
					if($insert){
						DB::commit();
						return 'success';
					}
					else{
						return 'Data detil gagal disimpan!';
					}
					
				}
				else{
					return 'Data gagal disimpan!';
				}
				
			}
			else{
				return 'Data tidak ditemukan!';
			}
		}
		catch(\Exception $e){
			return $e;
		}
	}
}