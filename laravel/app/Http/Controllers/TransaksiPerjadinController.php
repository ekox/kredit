<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransaksiPerjadinController extends Controller {

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
			$aColumns = array('id','id_rko','nosurat','tgsurat','ket','status');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "	SELECT	id,
								id_rko,
								nosurat,
								DATE_FORMAT(tgsurat,'%d-%m-%Y') AS tgsurat,
								ket,
								IF(IFNULL(id_kuitansi,'')='','Belum','Sudah') AS status
						FROM d_perjadin a
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
									<li><a href="transaksi/perjadin/'.$row->id.'/download" target="_blank" title="Cetak data?">Cetak Daftar</a></li>
								</ul>
							</div>
							<div class="dropdown pull-right" style="height:1.5vw !important;">
								<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-pencil"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="javascript:;" id="'.$row->id.'" title="Ubah data?" class="ubah">Ubah Perjadin</a></li>
									<li><a href="javascript:;" id="'.$row->id.'" title="Tambah detil?" class="tambah-detil">Tambah detil</a></li>
									<li><a href="javascript:;" id="'.$row->id.'" title="Hapus data?" class="hapus">Hapus Perjadin</a></li>
								</ul>
							</div>
						</center>';
				
				$output['aaData'][] = array(
					$row->id,
					$row->id_rko,
					$row->nosurat,
					$row->tgsurat,
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
	
	public function pilih_perjadin(Request $request)
	{
		try{
			$aColumns = array('id','kdakun','id_rko','nosurat','tgsurat','ket','nilai','pajak','kdppk','nospby','nmppk');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "	SELECT	a.id,
							concat(kdprogram,'-',kdgiat,'-',kdoutput,'-',kdsoutput,'-',kdkmpnen,'-',kdskmpnen,'-',kdakun) as kdakun,
							a.id_rko,
							a.nosurat,
							DATE_FORMAT(a.tgsurat,'%d-%m-%Y') AS tgsurat,
							a.ket,
							b.kdppk,
							b.nospby,
							c.nmppk,
							ifnull(d.nilai,0) as nilai,
							0 as pajak
						FROM d_perjadin a
						LEFT OUTER JOIN d_rko b ON(a.id_rko=b.id)
						LEFT OUTER JOIN t_ppk c ON(a.kdppk=c.kdppk)
						LEFT OUTER JOIN(
							SELECT	id_perjadin,
									SUM(nilai) as nilai
							FROM d_perjadin_detil
							GROUP BY id_perjadin
						) d ON(a.id=d.id_perjadin)
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
				$aksi='<center><a href="javascript:;" id="'.$row->id_rko.'-'.$row->nospby.'-'.$row->kdppk.'.'.$row->kdakun.'.'.number_format($row->nilai).'.'.number_format($row->pajak).'.'.$row->id.'" class="btn btn-xs btn-success pilih-perjadin"><i class="fa fa-check"></i></a></center>';
				
				$output['aaData'][] = array(
					$row->no,
					$row->kdakun,
					$row->id_rko,
					$row->nosurat,
					$row->tgsurat,
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
						nosurat,
						date_format(tgsurat, '%d-%m-%Y') as tgsurat,
						ket
				FROM d_perjadin
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
			DB::beginTransaction();
			
			$update= DB::update("
				update d_perjadin
				set nosurat=?,
					tgsurat=str_to_date(?, '%d-%m-%Y'),
					ket=?
				where id=?
			",[
				$request->input('nosurat'),
				$request->input('tgsurat'),
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
			$arr_jenis = $request->input('jenis_perjadin_pegawai');
			$arr_tingkat = $request->input('tingkat_perjadin_pegawai');
			$arr_kdkabkota1 = $request->input('kdkabkota1_pegawai');
			$arr_kdkabkota2 = $request->input('kdkabkota2_pegawai');
			$arr_tanggal = $request->input('tanggal_pegawai');
			$arr_jmlhari = $request->input('jmlhari_pegawai');
			$arr_nilai = $request->input('nilai_pegawai');
			
			$pegawai1 = $request->input('pilih_pegawai1');
			$arr_jenis1 = $request->input('jenis_perjadin_pegawai1');
			$arr_tingkat1 = $request->input('tingkat_perjadin_pegawai1');
			$arr_kdkabkota11 = $request->input('kdkabkota1_pegawai1');
			$arr_kdkabkota21 = $request->input('kdkabkota2_pegawai1');
			$arr_tanggal1 = $request->input('tanggal_pegawai1');
			$arr_jmlhari1 = $request->input('jmlhari_pegawai1');
			$arr_nilai1 = $request->input('nilai_pegawai1');
			
			DB::beginTransaction();
			
			$id_perjadin = $request->input('id_perjadin');
			
			if(count($pegawai)>0 || count($pegawai1)>0){
					
				$valid_peg=true;
				$valid_peg_non=true;
				
				//jika ada pegawai
				if(count($pegawai)>0){
					
					$arr_pegawai = array_keys($pegawai);
					$arr_data = array();
					for($i=0;$i<count($arr_pegawai);$i++){
						
						$arr_data[]=" (".$id_perjadin.",
										  '".$arr_pegawai[$i]."',
										  '".$arr_jenis[$arr_pegawai[$i]]."',
										  '".$arr_tingkat[$arr_pegawai[$i]]."',
										  '".$arr_kdkabkota1[$arr_pegawai[$i]]."',
										  '".$arr_kdkabkota2[$arr_pegawai[$i]]."',
										  str_to_date('".$arr_tanggal[$arr_pegawai[$i]]."','%d-%m-%Y'),
										  '".$arr_jmlhari[$arr_pegawai[$i]]."',
										  ".preg_replace("/[^0-9 \d]/i", "", $arr_nilai[$arr_pegawai[$i]]).") ";
						
					}
					
					$query = "insert into d_perjadin_detil
							(id_perjadin,nip,jenis_perjadin,tingkat_perjadin,kdkabkota_dari,kdkabkota_tujuan,tanggal,jmlhari,nilai)
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
						
						$arr_data1[]=" (".$id_perjadin.",
										  '".$arr_pegawai1[$i]."',
										  '".$arr_jenis1[$arr_pegawai1[$i]]."',
										  '".$arr_tingkat1[$arr_pegawai1[$i]]."',
										  '".$arr_kdkabkota11[$arr_pegawai1[$i]]."',
										  '".$arr_kdkabkota21[$arr_pegawai1[$i]]."',
										  str_to_date('".$arr_tanggal1[$arr_pegawai1[$i]]."','%d-%m-%Y'),
										  '".$arr_jmlhari1[$arr_pegawai1[$i]]."',
										  ".preg_replace("/[^0-9 \d]/i", "", $arr_nilai1[$arr_pegawai1[$i]]).") ";
						
					}
					
					$query1 = "insert into d_perjadin_detil
								(id_perjadin,id_peg_non,jenis_perjadin,tingkat_perjadin,kdkabkota_dari,kdkabkota_tujuan,tanggal,jmlhari,nilai)
								values".implode(",", $arr_data1);
					
					$insert1=DB::insert($query1);
					
					$valid_peg_non = false;
					if($insert1){
						$valid_peg_non = true;
					}
					
				}
				
				//var_dump($query);
				
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
					delete from d_perjadin_detil where id_perjadin=?
				",[
					$request->input('id')
				]);
				
				$delete = DB::delete("
					delete from d_perjadin where id=?
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
				SELECT
					a.id,
					IFNULL(b.nama,e.nama) AS nama,
					IFNULL(a.nip,e.nip) AS nip,
					IFNULL(b.kdgol,e.kdgol) AS kdgol,
					c.nmkabkota AS nmkabkota1,
					d.nmkabkota AS nmkabkota2,
					DATE_FORMAT(a.tanggal,'%d-%m-%Y') AS tanggal,
					a.jmlhari,
					a.uang_muka,
					a.nilai,
					if(f.id_rapat is null,'Belum terdaftar',concat('Sudah terdaftar : ',f.id_rapat)) as uraian_rapat,
					if(g.id_perjadin is null,'Belum terdaftar',concat('Sudah terdaftar : ',g.id_perjadin)) as uraian_perjadin
				FROM d_perjadin_detil a
				LEFT OUTER JOIN t_pegawai b ON(a.nip=b.nip)
				LEFT OUTER JOIN(
					SELECT	DISTINCT CONCAT(kdlokasi,kdkabkota) AS kdkabkota,
						nmkabkota
					FROM t_kabkota
				) c ON(a.kdkabkota_dari=c.kdkabkota)
				LEFT OUTER JOIN(
					SELECT	DISTINCT CONCAT(kdlokasi,kdkabkota) AS kdkabkota,
						nmkabkota
					FROM t_kabkota
				) d ON(a.kdkabkota_tujuan=d.kdkabkota)
				LEFT OUTER JOIN t_pegawai_non e ON(a.id_peg_non=e.id)
				LEFT OUTER JOIN(
					
					SELECT	a.nip,
						GROUP_CONCAT(b.id_rapat ORDER BY b.id_rapat SEPARATOR ',') AS id_rapat
					FROM(
						SELECT	nip,
							tanggal,
							jmlhari
						FROM d_perjadin_detil
						WHERE id_perjadin=?
					) a
					LEFT OUTER JOIN(
						SELECT	a.id_rapat,
							a.nip,
							b.tgrapat AS tanggal1,
							DATE_ADD(b.tgrapat, INTERVAL b.jmlhari DAY) AS tanggal2
						FROM d_rapat_detil a
						LEFT OUTER JOIN d_rapat b ON(a.id_rapat=b.id)
					) b ON(a.nip=b.nip)
					WHERE 	b.tanggal1>=a.tanggal AND
						b.tanggal1<DATE_ADD(a.tanggal, INTERVAL a.jmlhari DAY) AND
						b.tanggal2>=a.tanggal AND
						b.tanggal2<DATE_ADD(a.tanggal, INTERVAL a.jmlhari DAY)
					GROUP BY a.nip
					
				) f ON(a.nip=f.nip)
				LEFT OUTER JOIN(
					
					SELECT	a.nip,
						GROUP_CONCAT(b.id_perjadin ORDER BY b.id_perjadin SEPARATOR ',') AS id_perjadin
					FROM(
						SELECT	nip,
							tanggal,
							jmlhari
						FROM d_perjadin_detil
						WHERE id_perjadin=?
					) a
					LEFT OUTER JOIN(
						SELECT	id_perjadin,
							nip,
							tanggal AS tanggal1,
							DATE_ADD(tanggal, INTERVAL jmlhari DAY) AS tanggal2
						FROM d_perjadin_detil
						WHERE id_perjadin<>?
					) b ON(a.nip=b.nip)
					WHERE 	b.tanggal1>=a.tanggal AND
						b.tanggal1<DATE_ADD(a.tanggal, INTERVAL a.jmlhari DAY) AND
						b.tanggal2>=a.tanggal AND
						b.tanggal2<DATE_ADD(a.tanggal, INTERVAL a.jmlhari DAY)
					GROUP BY a.nip 
					
				) g ON(a.nip=g.nip)
				WHERE a.id_perjadin=?
				ORDER BY b.nama,e.nama ASC
			",[
				$id,
				$id,
				$id,
				$id
			]);
			
			if(count($rows)>0){
				
				$data='<div class="pull-right">
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
								<th>Gol</th>
								<th>Dari</th>
								<th>Ke</th>
								<th>Tanggal</th>
								<th>Hari</th>
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
									<td>'.$row->kdgol.'</td>
									<td>'.$row->nmkabkota1.'</td>
									<td>'.$row->nmkabkota2.'</td>
									<td>'.$row->tanggal.'</td>
									<td>'.$row->jmlhari.'</td>
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
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function hapus_detil(Request $request)
	{
		try{
			DB::beginTransaction();
			
			if(count($request->input('pilih_hapus'))){
				$arr_id = implode(",", $request->input('pilih_hapus'));
				
				$delete = DB::delete("
					delete from d_perjadin_detil
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
	
	public function cek_status($id_perjadin)
	{
		try{
			$rows = DB::select("
				SELECT	count(*) as jml
				FROM d_perjadin a
				WHERE a.id=? and id_kuitansi is null
			",[
				$id_perjadin
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
				
				$id_perjadin = DB::table('d_perjadin')->insertGetId(
					array(
						'id_rko' => $rows[0]->id,
						'nosurat' => '',
						'tgsurat' => $rows[0]->tgrko,
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
						'id_user' => session('id_user'),
						'created_at' => $created_at,
						'updated_at' => $created_at
					)
				);
				
				if($id_perjadin){
					
					$insert = DB::insert("
						insert into d_perjadin_detil(id_perjadin,nip,id_peg_non,jenis_perjadin,tingkat_perjadin,kdkabkota_dari,kdkabkota_tujuan,tanggal,jmlhari,nilai,ket)
						select	? as id_perjadin,
								a.nip,
								a.id_peg_non,
								'DN' as jenis_perjadin,
								'' as tingkat_perjadin,
								'0151' as kdkabkota_dari,
								'".$rows[0]->kdlokasi.$rows[0]->kdkabkota."' as kdkabkota_tujuan,
								'".$rows[0]->tanggal1."' as tanggal,
								'".$rows[0]->jmlhari."' as jmlhari,
								a.nilai,
								'' as ket
						from d_rko_pagu2 a
						left outer join t_pegawai b on(a.nip=b.nip)
						left outer join t_pegawai_non c on(a.id_peg_non=c.id)
						where a.id_rko=? and a.kdspj='01' and a.kdprogram=? and a.kdgiat=? and a.kdoutput=? and a.kdsoutput=? and a.kdkmpnen=? and a.kdskmpnen=? and a.kdakun=?
					",[
						$id_perjadin,
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