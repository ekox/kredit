<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;
use mPDF;

class RKOGUPLSController extends Controller {

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
			$aColumns = array('id','nmjenisgiat','urrko','tgrko','nmdok','tanggal1','jmlhari','jml','nilai','nmstatus');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "	SELECT	a.id,
								b.nmalur,
								c.nmjenisgiat,
								a.urrko,
								DATE_FORMAT(a.tgrko,'%d-%m-%Y') AS tgrko,
								IFNULL(e.jml,0) AS jml,
								DATE_FORMAT(a.tgrko,'%d-%m-%Y') AS tanggal1,
								IFNULL(DATEDIFF(tanggal2,tanggal1)+1,0) as jmlhari,
								IFNULL(e.nilai,0) AS nilai,
								a.created_at,
								f.status as nmstatus,
								ifnull(g.nmdok,'N/A') as nmdok
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
							FROM d_rko_pagu2
							GROUP BY id_rko
						) e ON(a.id=e.id_rko)
						LEFT OUTER JOIN t_alur_status f ON(a.kdalur=f.kdalur AND d.nourut=f.nourut)
						LEFT OUTER JOIN(
							SELECT	a.id_rko,
									GROUP_CONCAT('<li><a target=\"_blank\" href=\"rko/gup-ls/dok/',a.nmfile,'/download\">',b.nmdok,'</li>' ORDER BY a.id SEPARATOR '') AS nmdok
							FROM d_rko_dok a
							LEFT OUTER JOIN t_dok b ON(a.id_dok=b.id)
							GROUP BY a.id_rko
						) g ON(a.id=g.id_rko)
						WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."' AND a.id_unit='".session('id_unit')."' AND a.kdalur IN('02','03')
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
									<li><a href="rko/cetak/'.$row->id.'" target="_blank" title="Cetak data?">Cetak Lampiran</a></li>
								</ul>
							</div>
							<div class="dropdown pull-right">
								<button class="btn btn-danger btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-plus"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="javascript:;" id="'.$row->id.'.'.$row->jmlhari.'.'.$row->tanggal1.'" title="Tambah data?" class="tambah">Tambah Pagu</a></li>
									<li><a href="javascript:;" id="'.$row->id.'" title="Upload data?" class="upload">Upload Dokumen</a></li>
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
				
				$urrko = $row->urrko;
				if(strlen($urrko)>50){
					$urrko = substr($row->urrko,0,50).'<a href="javascript:;" title="'.$row->urrko.'">...dst</a>';
				}
				
				$output['aaData'][] = array(
					$row->id,
					$row->id,
					$row->nmjenisgiat,
					$urrko,
					//$row->tgrko,
					$row->nmdok,
					//$row->tanggal1,
					//'<div style="text-align:right;">'.number_format($row->jmlhari).'</div>',
					//'<div style="text-align:right;">'.number_format($row->jml).'</div>',
					'<div style="text-align:right;">'.number_format($row->nilai).'</div>',
					//$row->created_at,
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
	
	public function detil_lagi(Request $request, $arr_rko, $tipe)
	{
		try{
			if($arr_rko!=='xxx' && $tipe!=='x'){
				
				$arr_rko = explode(".", $arr_rko);
				$id_rko = $arr_rko[0];
				$akun = $arr_rko[1];
				
				$aColumns = array('id','jenis','nip','uraian','instansi','nilai','pajak','kdspj','id_ref_pajak','thang','periode','ppn','pph_21','pph_22','pph_23','pph_24','nama','harga','jumlah','uraian1');
				/* Indexed column (used for fast and accurate table cardinality) */
				$sIndexColumn = "id";
				/* DB table to use */
				$sTable = "SELECT	a.id,
									f.nmspj as jenis,
									IF(c.nama<>'',
											c.nip,
											IFNULL(d.nip,'-')
									) AS nip,
									IF(a.kdspj='01',
										IF(c.nama<>'',
												c.nip,
												IFNULL(d.nip,'-')
										),
										IF(a.kdspj='02',
											concat(a.uraian,' (@',a.harga,' x ',a.jumlah,')'),
											a.uraian
										)
									) as uraian,
									IF(a.kdspj='01',
										IF(c.nama<>'',
											c.nama,
											d.nama
										),
										IF(a.kdspj='02',
											'',
											a.nama
										)
									) AS nama,
									IF(a.kdspj='02',
										'-',
										IF(c.nama<>'',
											e.uraian,
											a.instansi
										)
									) AS instansi,
									a.nilai,
									IFNULL(a.ppn,0)+IFNULL(a.pph_21,0)+IFNULL(a.pph_22,0)+IFNULL(a.pph_23,0)+IFNULL(a.pph_24,0) AS pajak,
									a.kdspj,
									a.id_ref_pajak,
									a.thang,
									a.periode,
									a.ppn,
									a.pph_21,
									a.pph_22,
									a.pph_23,
									a.pph_24,
									a.harga,
									a.jumlah,
									a.uraian as uraian1
								FROM d_rko_pagu2 a
								LEFT OUTER JOIN t_pegawai c ON(a.nip=c.nip)
								LEFT OUTER JOIN t_pegawai_non d ON(a.id_peg_non=d.id)
								LEFT OUTER JOIN t_unit_instansi e ON(c.unit_id=e.id_unit)
								LEFT OUTER JOIN t_spj f ON(a.kdspj=f.kdspj)
								WHERE a.id_rko=".$id_rko." AND CONCAT(a.kdprogram,'-',a.kdgiat,'-',a.kdoutput,'-',a.kdsoutput,'-',a.kdkmpnen,'-',a.kdskmpnen,'-',a.kdakun)='".$akun."'
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
					order by id desc
					$sLimit
				";
				
				$rows = DB::select($sQuery);

				$i=1;
				foreach( $rows as $row )
				{
					$ubah = '';
					
					$id = $row->id.'|'.$row->kdspj.'|'.$row->id_ref_pajak.'|'.$row->thang.'|'.$row->periode.'|'.number_format($row->nilai).'|'.number_format($row->ppn).'|'.number_format($row->pph_21).'|'.number_format($row->pph_22).'|'.number_format($row->pph_23).'|'.number_format($row->pph_24).'|'.$row->nip.'|'.$row->nama.'|'.number_format($row->harga).'|'.number_format($row->jumlah).'|'.$row->uraian1;
					
					if($tipe=='1'){
						$ubah = '<center>
									<a href="javascript:;" id="'.$id.'" class="btn btn-xs btn-primary ubah-detil-akun"><i class="fa fa-pencil"></i></a>
								</center>';
					}
					
					$aksi='<center>
								<input type="checkbox" name="pilih_akun_detil['.$row->id.']" class="pilih_akun_detil" value="'.$row->id.'">
							</center>';
					
					$output['aaData'][] = array(
						$i++,
						$row->jenis,
						$row->nip,
						$row->uraian,
						$row->instansi,
						'<div style="text-align:right;">'.number_format($row->nilai).'</div>',
						'<div style="text-align:right;">'.number_format($row->pajak).'</div>',
						$ubah,
						$aksi
					);
				}
				
				return response()->json($output);
				
			}
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function detil_temp(Request $request, $arr_rko)
	{
		try{
			if($arr_rko!=='xxx'){
				
				$arr_rko = explode(".", $arr_rko);
				$id_rko = $arr_rko[0];
				$akun = $arr_rko[1];
				
				$aColumns = array('id','jenis','nip','uraian','instansi','nilai','pajak');
				/* Indexed column (used for fast and accurate table cardinality) */
				$sIndexColumn = "id";
				/* DB table to use */
				$sTable = "SELECT	a.id,
									IF(a.uraian<>'',
										'Barang/Jasa',
										IF(c.nama<>'',
											'Peg.Int',
											'Peg.Eks'
										)
									) AS jenis,
									IF(a.uraian<>'',
										'-',
										IF(c.nama<>'',
											c.nip,
											IFNULL(d.nip,'-')
										)
									) AS nip,
									IF(a.uraian<>'',
										a.uraian,
										IF(c.nama<>'',
											c.nama,
											d.nama
										)
									) AS uraian,
									IF(a.uraian<>'',
										'-',
										IF(c.nama<>'',
											e.uraian,
											a.instansi
										)
									) AS instansi,
									a.nilai,
									IFNULL(a.ppn,0)+IFNULL(a.pph_21,0)+IFNULL(a.pph_22,0)+IFNULL(a.pph_23,0)+IFNULL(a.pph_24,0) AS pajak
								FROM d_rko_pagu2 a
								LEFT OUTER JOIN t_pegawai c ON(a.nip=c.nip)
								LEFT OUTER JOIN t_pegawai_non d ON(a.id_peg_non=d.id)
								LEFT OUTER JOIN t_unit_instansi e ON(c.unit_id=e.id_unit)
								WHERE a.id_rko=".$id_rko." AND CONCAT(a.kdprogram,'-',a.kdgiat,'-',a.kdoutput,'-',a.kdsoutput,'-',a.kdkmpnen,'-',a.kdskmpnen,'-',a.kdakun)='".$akun."'
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
				
				$i=1;
				foreach( $rows as $row )
				{			
					$aksi='<center style="width:100px;">
								<input type="checkbox" name="pilih_akun_detil['.$row->id.']">
							</center>';
					
					$output['aaData'][] = array(
						$i++,
						$row->jenis,
						$row->nip,
						$row->uraian,
						$row->instansi,
						'<div style="text-align:right;">'.number_format($row->nilai).'</div>',
						'<div style="text-align:right;">'.number_format($row->pajak).'</div>',
						$aksi
					);
				}
				
				return response()->json($output);
				
			}
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
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function simpan(Request $request)
	{
		try{
			//cek apakah ada sisa
			if($this->sisa()){
				
				if($request->input('inp-rekambaru')=='1'){ //tambah
				
					DB::beginTransaction();
					
					$arr_tgrko = explode("-", $request->input('tgrko'));
					$tgrko = $arr_tgrko[2].'-'.$arr_tgrko[1].'-'.$arr_tgrko[0];
					
					$arr_tanggal1 = explode("-", $request->input('tanggal1'));
					$tanggal1 = $arr_tanggal1[2].'-'.$arr_tanggal1[1].'-'.$arr_tanggal1[0];
					
					$arr_tanggal2 = explode("-", $request->input('tanggal2'));
					$tanggal2 = $arr_tanggal2[2].'-'.$arr_tanggal2[1].'-'.$arr_tanggal2[0];
					
					$now = new \DateTime();
					$created_at = $now->format('Y-m-d H:i:s');
					
					$id_rko = DB::table('d_rko')->insertGetId(
						array(
							'kddept' => session('kddept'),
							'kdunit' => session('kdunit'),
							'kdsatker' => session('kdsatker'),
							'kddekon' => session('kddekon'),
							'kdalur' => $request->input('kdalur'),
							'jenisgiat' => $request->input('jenisgiat'),
							'urrko' => $request->input('urrko'),
							'tgrko' => $tgrko,
							'tanggal1' => $tanggal1,
							'tanggal2' => $tanggal2,
							'thang' => session('tahun'),
							'kdlokasi' => $request->input('kdlokasi'),
							'kdkabkota' => $request->input('kdkabkota'),
							'periode1' => $request->input('periode1'),
							'periode2' => $request->input('periode2'),
							'kdppk' => session('kdppk'),
							'kdbpp' => session('kdbpp'),
							'id_unit' => session('id_unit'),
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
					
					if($this->cek_status($request->input('inp-id'))){
						
						DB::beginTransaction();
					
						$update = DB::update("
							update d_rko
							set kdalur=?,
								jenisgiat=?,
								urrko=?,
								tgrko=STR_TO_DATE(?, '%d-%m-%Y'),
								tanggal1=STR_TO_DATE(?, '%d-%m-%Y'),
								tanggal2=STR_TO_DATE(?, '%d-%m-%Y'),
								kdlokasi=?,
								kdkabkota=?,
								periode1=?,
								periode2=?,
								kdppk=?,
								kdbpp=?,
								id_user=?,
								updated_at=now()
							where id=?
						",[
							$request->input('kdalur'),
							$request->input('jenisgiat'),
							$request->input('urrko'),
							$request->input('tgrko'),
							$request->input('tanggal1'),
							$request->input('tanggal2'),
							$request->input('kdlokasi'),
							$request->input('kdkabkota'),
							$request->input('periode1'),
							$request->input('periode2'),
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
			else{
				return 'Terdapat Uang Muka Kerja yang belum rampung dan melebihi batas 10 hari!';
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
				select	a.id,
						concat(a.kdprogram,'-',a.kdgiat,'-',a.kdoutput,'-',a.kdsoutput,'-',a.kdkmpnen,'-',a.kdskmpnen,'-',a.kdakun) as kdakun,
						a.nip,
						a.uraian,
						b.nmspj,
						ifnull(c.kdgol,d.kdgol) as kdgol,
						if(a.kdspj='01',ifnull(c.nama,d.nama),ifnull(a.nama,'-')) as nama,
						ifnull(a.nilai,0) as nilai,
						ifnull(a.ppn,0) as ppn,
						ifnull(a.pph_21,0) as pph21,
						ifnull(a.pph_22,0) as pph22,
						ifnull(a.pph_23,0) as pph23,
						ifnull(a.pph_24,0) as pph24,
						ifnull(e.sisapagu,0) as sisapagu,
						IFNULL(DATEDIFF(f.tanggal2,f.tanggal1)+1,0) as jmlhari,
						DATE_FORMAT(f.tanggal1,'%d-%m-%Y') as tanggal
				from d_rko_pagu2 a
				left outer join t_spj b on(a.kdspj=b.kdspj)
				left outer join t_pegawai c on(a.nip=c.nip)
				left outer join t_pegawai_non d on(a.id_peg_non=d.id)
				left outer join(
					SELECT	a.*,
							a.paguakhir-IFNULL(b.nilai,0) AS sisapagu
					FROM(
						SELECT	
							kdsatker,
							thang,
							kdprogram,
							kdgiat,
							kdoutput,
							kdsoutput,
							kdkmpnen,
							kdskmpnen,
							kdakun,
							SUM(paguakhir) AS paguakhir
						FROM d_pagu
						WHERE kdsatker='".session('kdsatker')."' and thang='".session('tahun')."' and lvl='7'
						GROUP BY 
							kdsatker,
							thang,
							kdprogram,
							kdgiat,
							kdoutput,
							kdsoutput,
							kdkmpnen,
							kdskmpnen,
							kdakun
					) a
					LEFT OUTER JOIN(
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
					) b ON(a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.kdprogram=b.kdprogram AND a.kdgiat=b.kdgiat AND a.kdoutput=b.kdoutput AND a.kdsoutput=b.kdsoutput AND a.kdkmpnen=b.kdkmpnen AND a.kdskmpnen=b.kdskmpnen AND a.kdakun=b.kdakun)
				) e on(a.kdprogram=e.kdprogram and a.kdgiat=e.kdgiat and a.kdoutput=e.kdoutput and a.kdsoutput=e.kdsoutput and a.kdkmpnen=e.kdkmpnen and a.kdskmpnen=e.kdskmpnen and a.kdakun=e.kdakun)
				left outer join d_rko f on(a.id_rko=f.id)
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
								<th>Gol</th>
								<th>Uraian</th>
								<th>Nilai</th>
								<th>PPN</th>
								<th>PPh21</th>
								<th>PPh22</th>
								<th>PPh23</th>
								<th>PPh24</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>';
				
				$i=1;
				foreach($rows as $row){
					$data .= '<tr>
									<td>'.$i++.'</td>
									<td><a href="javascript:;" id="'.$id.'.'.$row->kdakun.'.'.number_format($row->sisapagu).'.'.$row->jmlhari.'.'.$row->tanggal.'" class="btn btn-xs btn-primary tambah-detil-akun"><i class="fa fa-plus"></i></a> '.$row->kdakun.'</td>
									<td>'.$row->nmspj.'</td>
									<td>'.$row->nama.'</td>
									<td>'.$row->nip.'</td>
									<td>'.$row->kdgol.'</td>
									<td>'.$row->uraian.'</td>
									<td style="text-align:right;">'.number_format($row->nilai).'</td>
									<td style="text-align:right;">'.number_format($row->ppn).'</td>
									<td style="text-align:right;">'.number_format($row->pph21).'</td>
									<td style="text-align:right;">'.number_format($row->pph22).'</td>
									<td style="text-align:right;">'.number_format($row->pph23).'</td>
									<td style="text-align:right;">'.number_format($row->pph24).'</td>
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
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function detil_baru($id)
	{
		try{
			$rows = DB::select("
				select	IFNULL(DATEDIFF(f.tanggal2,f.tanggal1)+1,0) as jmlhari,
						DATE_FORMAT(f.tanggal1,'%d-%m-%Y') as tanggal
				from d_rko f
				where f.id=?
			",[
				$id
			]);
			
			if(count($rows)>0){
				
				$jmlhari = $rows[0]->jmlhari;
				$tanggal = $rows[0]->tanggal;
				
				$rows = DB::select("
					SELECT	a.*,
							e.paguakhir,
							e.sisapagu
					FROM(
						SELECT	kdprogram,
								kdgiat,
								kdoutput,
								kdsoutput,
								kdkmpnen,
								kdskmpnen,
								kdakun,
								COUNT(*) AS jml,
								SUM(nilai) AS nilai
						FROM d_rko_pagu2
						WHERE id_rko=?
						GROUP BY kdprogram,
							kdgiat,
							kdoutput,
							kdsoutput,
							kdkmpnen,
							kdskmpnen,
							kdakun
					) a
					left outer join(
						SELECT	a.*,
								a.paguakhir-IFNULL(b.nilai,0) AS sisapagu
						FROM(
							SELECT	
								kdsatker,
								thang,
								kdprogram,
								kdgiat,
								kdoutput,
								kdsoutput,
								kdkmpnen,
								kdskmpnen,
								kdakun,
								SUM(paguakhir) AS paguakhir
							FROM d_pagu
							WHERE kdsatker='".session('kdsatker')."' and thang='".session('tahun')."' and lvl='7'
							GROUP BY 
								kdsatker,
								thang,
								kdprogram,
								kdgiat,
								kdoutput,
								kdsoutput,
								kdkmpnen,
								kdskmpnen,
								kdakun
						) a
						LEFT OUTER JOIN(
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
						) b ON(a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.kdprogram=b.kdprogram AND a.kdgiat=b.kdgiat AND a.kdoutput=b.kdoutput AND a.kdsoutput=b.kdsoutput AND a.kdkmpnen=b.kdkmpnen AND a.kdskmpnen=b.kdskmpnen AND a.kdakun=b.kdakun)
					) e on(a.kdprogram=e.kdprogram and a.kdgiat=e.kdgiat and a.kdoutput=e.kdoutput and a.kdsoutput=e.kdsoutput and a.kdkmpnen=e.kdkmpnen and a.kdskmpnen=e.kdskmpnen and a.kdakun=e.kdakun)
				",[
					$id
				]);
				
				if(count($rows)>0){
					
					$data='<table class="table table-bordered">
							<thead>
								<tr>
									<th>No</th>
									<th>Akun</th>
									<th>Jumlah</th>
									<th>Nilai</th>
									<th>Sisa Pagu</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>';
					
					$i=1;
					foreach($rows as $row){
						
						$akun = $row->kdprogram.'-'.$row->kdgiat.'-'.$row->kdoutput.'-'.$row->kdsoutput.'-'.$row->kdkmpnen.'-'.$row->kdskmpnen.'-'.$row->kdakun;
						$id1 = $id.'.'.$akun.'.'.number_format($row->sisapagu).'.'.$jmlhari.'.'.$tanggal;
						
						$data .= '<tr>
										<td>'.$i++.'</td>
										<td>'.$akun.'</td>
										<td style="text-align:right;">'.number_format($row->jml).'</td>
										<td style="text-align:right;">'.number_format($row->nilai).'</td>
										<td style="text-align:right;">'.number_format($row->sisapagu).'</td>
										<td>
											<center>
												<a href="javascript:;" id="'.$id1.'" class="btn btn-xs btn-warning tambah-detil-akun"><i class="fa fa-plus"></i></a>
												<a href="javascript:;" id="'.$id1.'" class="btn btn-xs btn-warning hapus-detil-all"><i class="fa fa-times"></i></a>
												<a href="javascript:;" id="'.$id1.'" class="btn btn-xs btn-warning lihat-detil"><i class="fa fa-search"></i></a>
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
			else{
				return 'Data header tidak ditemukan!';
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
			$id_rko = $request->input('id_rko');
			
			$rows = DB::select("
				SELECT	b.nourut
				FROM(
					SELECT	id_rko,
							MAX(id) AS id
					FROM d_rko_status
					GROUP BY id_rko
				) a
				LEFT OUTER JOIN d_rko_status b ON(a.id_rko=b.id_rko AND a.id=b.id)
				WHERE a.id_rko=?
			",[
				$id_rko
			]);
			
			if($rows[0]->nourut==0){
				
				if($request->input('inp-rekambaru1')=='1'){//rekam baru
					
					$kdspj = $request->input('kdspj');
				
					//get json detil string to array
					$detil = $request->input('detil'); 
					$detil = (array)json_decode($detil);
					$pegawai = $detil['data1'];
					$pegawai1 = $detil['data2'];
					
					//$pegawai = $request->input('pilih_pegawai');
					$arr_jabatan = $request->input('jabatan_pegawai');
					$arr_nilai = $request->input('nilai_pegawai');
					$arr_pph21 = $request->input('pph21_pegawai');
					$arr_pph22 = $request->input('pph22_pegawai');
					$arr_pph23 = $request->input('pph23_pegawai');
					$arr_pph24 = $request->input('pph24_pegawai');
					
					//$pegawai1 = $request->input('pilih_pegawai1');
					$arr_jabatan1 = $request->input('jabatan_pegawai1');
					$arr_nilai1 = $request->input('nilai_pegawai1');
					$arr_pph211 = $request->input('pph21_pegawai1');
					$arr_pph221 = $request->input('pph22_pegawai1');
					$arr_pph231 = $request->input('pph23_pegawai1');
					$arr_pph241 = $request->input('pph24_pegawai1');
					
					$tahun = $request->input('tahun');
					$bulan = $request->input('bulan');
					$instansi = $request->input('instansi');
					$nip = $request->input('nip');
					$nama = $request->input('nama');
					$kdjab = $request->input('kdjab');
					$uraian = $request->input('uraian');
					$harga = preg_replace("/[^0-9 \d]/i", "", $request->input('satuan'));
					$jumlah = preg_replace("/[^0-9 \d]/i", "", $request->input('jumlah'));
					$nilai = preg_replace("/[^0-9 \d]/i", "", $request->input('nilai'));
					$id_ref_pajak = $request->input('id_ref_pajak');
					$sisa_pagu = preg_replace("/[^0-9 \d]/i", "", $request->input('sisa_pagu'));
					$ppn = preg_replace("/[^0-9 \d]/i", "", $request->input('ppn'));
					$pph_21 = preg_replace("/[^0-9 \d]/i", "", $request->input('pph_21'));
					$pph_22 = preg_replace("/[^0-9 \d]/i", "", $request->input('pph_22'));
					$pph_23 = preg_replace("/[^0-9 \d]/i", "", $request->input('pph_23'));
					$pph_24 = preg_replace("/[^0-9 \d]/i", "", $request->input('pph_24'));
					$arr_pagu = explode("-", $request->input('id_pagu'));
					$kdprogram = $arr_pagu[0];
					$kdgiat = $arr_pagu[1];
					$kdoutput = $arr_pagu[2];
					$kdsoutput = $arr_pagu[3];
					$kdkmpnen = $arr_pagu[4];
					$kdskmpnen = $arr_pagu[5];
					$kdakun = $arr_pagu[6];
					
					if($kdspj=='01'){ //pilih pegawai
						
						if(count($pegawai)>0 || count($pegawai1)>0){
							
							DB::beginTransaction();
							
							$valid_peg=true;
							$valid_peg_non=true;
							$nilai_total = 0;
							$nilai_total1 = 0;
							
							//jika ada pegawai
							if(count($pegawai)>0){
								
								$arr_pegawai = (array)$pegawai;
							
								$error = false;
							
								$arr_data = array();
								foreach($arr_pegawai as $row_pegawai){
									
									$row_pegawai = (array)$row_pegawai;
									
									if(isset($row_pegawai['jab']) && isset($row_pegawai['nilai']) && isset($row_pegawai['pph21'])){
										
										$arr_data[]=" (".$id_rko.",
														'".$kdspj."',
														'".$kdprogram."',
														'".$kdgiat."',
														'".$kdoutput."',
														'".$kdsoutput."',
														'".$kdkmpnen."',
														'".$kdskmpnen."',
														'".$kdakun."',
														'".$tahun."',
														'".$bulan."',
														'".$instansi."',
														'".$uraian."',
														'".$row_pegawai['id']."',
														'".$row_pegawai['jab']."',
														'".preg_replace("/[^0-9 \d]/i", "", $row_pegawai['nilai'])."',
														'".preg_replace("/[^0-9 \d]/i", "", $row_pegawai['pph21'])."',
														'0',
														'0',
														'0',
														".session('id_user').",
														now(),
														now()) ";
														
										$nilai_total += (int)preg_replace("/[^0-9 \d]/i", "", $row_pegawai['nilai']);
										
									}
									else{
										$error = true;
										break;
									}
									
								}
								
								if(!$error){
									
									$query = "insert into d_rko_pagu2(id_rko,kdspj,kdprogram,kdgiat,kdoutput,kdsoutput,kdkmpnen,kdskmpnen,kdakun,thang,periode,instansi,uraian,nip,kdjab,nilai,pph_21,pph_22,pph_23,pph_24,id_user,created_at,updated_at)
											values".implode(",", $arr_data);
								
									$insert=DB::insert($query);
									
									$valid_peg = false;
									if($insert){
										$valid_peg = true;
									}
									
								}
								else{
									return 'Data pegawai internal tidak valid!';
								}
								
							}
							
							//jika ada non pegawai
							if(count($pegawai1)>0){
								
								$arr_pegawai1 = (array)$pegawai1;
								
								$error = false;
								
								$arr_data1 = array();
								foreach($arr_pegawai1 as $row_pegawai1){
									
									$row_pegawai1 = (array)$row_pegawai1;
									
									if(isset($row_pegawai1['jab']) && isset($row_pegawai1['nilai']) && isset($row_pegawai1['pph21'])){
										
										$arr_data1[]=" (".$id_rko.",
														'".$kdspj."',
														'".$kdprogram."',
														'".$kdgiat."',
														'".$kdoutput."',
														'".$kdsoutput."',
														'".$kdkmpnen."',
														'".$kdskmpnen."',
														'".$kdakun."',
														'".$tahun."',
														'".$bulan."',
														'".$instansi."',
														'".$uraian."',
														'".$row_pegawai1['id']."',
														'".$row_pegawai1['jab']."',
														'".preg_replace("/[^0-9 \d]/i", "", $row_pegawai1['nilai'])."',
														'".preg_replace("/[^0-9 \d]/i", "", $row_pegawai1['pph21'])."',
														'0',
														'0',
														'0',
														".session('id_user').",
														now(),
														now()) ";
														
										$nilai_total1 += (int)preg_replace("/[^0-9 \d]/i", "", $row_pegawai1['nilai']);
										
									}
									else{
										$error = true;
										break;
									}
									
								}
								
								if(!$error){
									
									$query1 = "insert into d_rko_pagu2(id_rko,kdspj,kdprogram,kdgiat,kdoutput,kdsoutput,kdkmpnen,kdskmpnen,kdakun,thang,periode,instansi,uraian,id_peg_non,kdjab,nilai,pph_21,pph_22,pph_23,pph_24,id_user,created_at,updated_at)
											values".implode(",", $arr_data1);
								
									$insert1=DB::insert($query1);
									
									$valid_peg_non = false;
									if($insert1){
										$valid_peg_non = true;
									}
									
								}
								else{
									return 'Data pegawai eksternal tidak valid!';
								}
								
							}
							
							if(($nilai_total+$nilai_total1)<=$sisa_pagu){
								
								if($valid_peg && $valid_peg_non){
									DB::commit();
									return 'success';
								}
								else{
									return 'Data gagal disimpan!';
								}
								
							}
							else{
								return 'Nilai RKO melebihi pagu!';
							}
							
						}
						else{
							return 'Anda belum memilih pegawai!';
						}
						
					}
					else{ //tidak pilih pegawai
						
						if($nilai<=$sisa_pagu){
							
							$query = "	insert into d_rko_pagu2(id_rko,kdspj,kdprogram,kdgiat,kdoutput,kdsoutput,kdkmpnen,kdskmpnen,kdakun,thang,periode,instansi,uraian,nip,nama,kdjab,nilai,ppn,pph_21,pph_22,pph_23,pph_24,id_user,created_at,updated_at,harga,jumlah,id_ref_pajak)
										value(".$id_rko.",'".$kdspj."','".$kdprogram."','".$kdgiat."','".$kdoutput."','".$kdsoutput."','".$kdkmpnen."','".$kdskmpnen."','".$kdakun."','".$tahun."','".$bulan."','".$instansi."','".$uraian."','".$nip."','".$nama."','".$kdjab."','".preg_replace("/[^0-9 \d]/i", "", $nilai)."','".preg_replace("/[^0-9 \d]/i", "", $ppn)."','".preg_replace("/[^0-9 \d]/i", "", $pph_21)."','".preg_replace("/[^0-9 \d]/i", "", $pph_22)."','".preg_replace("/[^0-9 \d]/i", "", $pph_23)."','".preg_replace("/[^0-9 \d]/i", "", $pph_24)."',".session('id_user').",now(),now(),'".preg_replace("/[^0-9 \d]/i", "", $harga)."','".preg_replace("/[^0-9 \d]/i", "", $jumlah)."','".$id_ref_pajak."')";
										
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
						else{
							return 'Nilai RKO melebihi pagu!';
						}
						
					}
					
				}
				else{//ubah data
					
					$uraian = '';
					if($request->input('kdspj')!=='01'){
						$uraian = $request->input('uraian');
					}
					
					$update = DB::update("
						update d_rko_pagu2
						set uraian=?,
							harga=?,
							jumlah=?,
							nilai=?,
							id_ref_pajak=?,
							ppn=?,
							pph_21=?,
							pph_22=?,
							pph_23=?,
							pph_24=?
						where id=?
					",[
						$uraian,
						preg_replace("/[^0-9 \d]/i", "", $request->input('satuan')),
						preg_replace("/[^0-9 \d]/i", "", $request->input('jumlah')),
						preg_replace("/[^0-9 \d]/i", "", $request->input('nilai')),
						$request->input('id_ref_pajak'),
						preg_replace("/[^0-9 \d]/i", "", $request->input('ppn')),
						preg_replace("/[^0-9 \d]/i", "", $request->input('pph_21')),
						preg_replace("/[^0-9 \d]/i", "", $request->input('pph_22')),
						preg_replace("/[^0-9 \d]/i", "", $request->input('pph_23')),
						preg_replace("/[^0-9 \d]/i", "", $request->input('pph_24')),
						$request->input('inp-id1')
					]);
					
					if($update){
						return 'success';
					}
					else{
						return 'Data detil gagal diupdate!';
					}
					
				}
				
			}
			else{
				return 'Data detil tidak dapat ditambahkan lagi!';
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
				from d_rko_pagu2
				where id=?
			",[
				$request->input('id')
			]);
			
			if(count($rows)>0){
				
				$id_rko = $rows[0]->id_rko;
				
				if($this->cek_status($id_rko)){
					
					$delete = DB::delete("
						delete from d_rko_pagu2 where id=?
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
	
	public function hapus_detil_baru(Request $request)
	{
		try{
			DB::beginTransaction();
			
			$detil = $request->input('detil'); 
			$detil = (array)json_decode($detil);
			
			$id = $detil[0];
			
			$str_id = implode(",", $detil);
			
			$rows = DB::select("
				select	id_rko
				from d_rko_pagu2
				where id=?
			",[
				$id
			]);
			
			if(count($rows)>0){
				
				$id_rko = $rows[0]->id_rko;
				
				if($this->cek_status($id_rko)){
					
					$delete = DB::delete("
						delete from d_rko_pagu2 where id in(".$str_id.")
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
	
	public function hapus_detil_all(Request $request)
	{
		try{
			DB::beginTransaction();
			
			$arr_id = explode(".", $request->input('id'));
			
			$id_rko = $arr_id[0];
			$akun = $arr_id[1];
			
			if($this->cek_status($id_rko)){
				
				$delete = DB::delete("
					delete from d_rko_pagu2
					where id_rko=? and concat(kdprogram,'-',kdgiat,'-',kdoutput,'-',kdsoutput,'-',kdkmpnen,'-',kdskmpnen,'-',kdakun)=?
				",[
					$id_rko,
					$akun
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
					FROM d_rko_pagu2
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
					FROM d_rko_pagu2
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
				
				$tipe = explode(",", $rows[0]->tipe);
				
				$targetFolder = 'data\dok\rko\\'; // Relative to the root
			
				if(!empty($_FILES)) {
					$file_name = $_FILES['file']['name'];
					$tempFile = $_FILES['file']['tmp_name'];
					$targetFile = $targetFolder.$file_name;
					$fileTypes = $tipe; // File extensions
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
						WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."' AND a.kdalur IN('02') AND b.nourut=12
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
	
	public function pilih_rko_detil($id)
	{
		try{
			$arr_id = explode("-", $id);
			$id_rko = $arr_id[0];
			
			$rows = DB::select("
				select	a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						a.kdkmpnen,
						a.kdskmpnen,
						a.kdakun,
						sum(a.nilai) as nilai
				from d_rko_pagu2 a
				left outer join d_rko b on(a.id_rko=b.id)
				where a.id_rko=?
				group by a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						a.kdkmpnen,
						a.kdskmpnen,
						a.kdakun
				order by a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						a.kdkmpnen,
						a.kdskmpnen,
						a.kdakun
			",[
				$id_rko
			]);
			
			if(count($rows)>0){
				
				$data='<table class="table table-bordered">
						<thead>
							<tr>
								<th>No</th>
								<th>Program</th>
								<th>Kegiatan</th>
								<th>Output</th>
								<th>S.Output</th>
								<th>Komponen</th>
								<th>S.Komponen</th>
								<th>Akun</th>
								<th>Nilai</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>';
				
				$i=1;
				foreach($rows as $row){
					$data .= '<tr>
									<td>'.$i++.'</td>
									<td>'.$row->kdprogram.'</td>
									<td>'.$row->kdgiat.'</td>
									<td>'.$row->kdoutput.'</td>
									<td>'.$row->kdsoutput.'</td>
									<td>'.$row->kdkmpnen.'</td>
									<td>'.$row->kdskmpnen.'</td>
									<td>'.$row->kdakun.'</td>
									<td style="text-align:right;">'.number_format($row->nilai).'</td>
									<td>
										<center>
											<a href="javascript:;" id="'.$id.'.'.$row->kdprogram.'-'.$row->kdgiat.'-'.$row->kdoutput.'-'.$row->kdsoutput.'-'.$row->kdkmpnen.'-'.$row->kdskmpnen.'-'.$row->kdakun.'.'.number_format($row->nilai).'" class="btn btn-xs btn-warning pilih-rko-detil"><i class="fa fa-check"></i></a>
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
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function download($param)
	{
		//~ try{
			$css = new RKOSPBYController;
			$html_out = $css->css();
			
			$rows_satker = DB::select("
				select	kdsatker,
						uraian as nmsatker
			from d_pagu
			where kdsatker=? and thang=? and lvl='0'
			",[
				session('kdsatker'),
				session('tahun')
			]);
			
			$rows_program = DB::select("
				select 	CONCAT('023','.','11','.',a.kdprogram) as kode,
						a.kdprogram,
						b.nmprogram,
						c.kdppk,
						d.nama as nama_ppk,
						d.nip as nip_ppk,
						c.kdbpp,
						c.id_unit,
						e.nama as nama_bpp,
						e.nip as nip_bpp,
						f.nama as nama_pk2,
						f.nip as nip_pk2,
						g.nama as nama_pk1,
						g.nip as nip_pk1,
						h.is_sekre,
						i.uraian as nama_unit,
						sum(a.nilai) as nilai,
						sum(a.ppn) as ppn,
						sum(a.pph_21) as pph_21,
						sum(a.pph_22) as pph_22,
						sum(a.pph_23) as pph_23,
						sum(a.pph_24) as pph_24
				from d_rko_pagu2 a 
				left outer join (select kdprogram, nmprogram
					from t_program where kddept='023' and kdunit='11'
				) b on a.kdprogram=b.kdprogram
				left outer join d_rko c on a.id_rko=c.id
				LEFT OUTER JOIN(
					SELECT kdsatker,kdppk,nama,nip
					 FROM t_user
					 WHERE kdlevel='06' AND aktif='1'
				) d ON(c.kdsatker=d.kdsatker AND c.kdppk=d.kdppk)
				LEFT OUTER JOIN(
					SELECT kdsatker,kdbpp,nama,nip
					 FROM t_user
					 WHERE kdlevel='05' AND aktif='1'
				) e ON(c.kdsatker=e.kdsatker AND c.kdbpp=e.kdbpp)
				LEFT OUTER JOIN(
					 SELECT kdsatker,kdppk,id_unit,nama,nip
								 FROM t_user
								 WHERE kdlevel='03' AND aktif='1'
				) f ON(c.kdsatker=f.kdsatker AND c.kdppk=f.kdppk AND LEFT(c.id_unit,8)=LEFT(f.id_unit,8))
				LEFT OUTER JOIN(
					 SELECT kdsatker,kdppk,id_unit,nama,nip
								 FROM t_user
								 WHERE kdlevel='02' AND aktif='1'
				) g ON(c.kdsatker=g.kdsatker AND c.kdppk=g.kdppk AND c.id_unit=g.id_unit)
				LEFT OUTER JOIN t_unit_instansi h on(c.id_unit=h.id_unit)
				LEFT OUTER JOIN(
					select *
					from t_unit_instansi
					where substr(id_unit,7,5)='00.00'
				) i ON(substr(c.id_unit,1,5)=substr(i.id_unit,1,5))
				where a.id_rko=".$param."
				group by CONCAT('023','.','11','.',a.kdprogram), a.kdprogram, b.nmprogram, c.kdppk, c.kdbpp, c.id_unit
			");
			
			if(count($rows_program)==0) {
				//~ new Exception('No data program found');
				return "tidak ada data";
			} 
			
			$rows_akun = DB::select("
				SELECT	a.*,
						b.nmakun
				FROM(
					SELECT	a.id_rko,
							a.kdakun,
							SUM(nilai) AS nilai,
							sum(a.ppn) as ppn,
							sum(a.pph_21) as pph_21,
							sum(a.pph_22) as pph_22,
							sum(a.pph_23) as pph_23,
							sum(a.pph_24) as pph_24
					FROM d_rko_pagu2 a
					WHERE a.id_rko=?
					GROUP BY a.id_rko,a.kdakun
				) a
				LEFT OUTER JOIN t_akun b ON(a.kdakun=b.kdakun)
				ORDER BY a.kdakun ASC
			",[
				$param
			]);
			
			if(count($rows_akun)==0) {
				//~ new Exception('No data akun found');
				return "tidak ada data";
			} 
			
			function rows_uraian($id_rko, $kdakun) 
			{
				return DB::select("
					select 	if(a.kdspj='01',
								if(a.nip is null,
									concat(c.nama),
									concat(b.nama)
								),
								if(a.kdspj='02',
									a.uraian,
									if(a.nama is null,
										a.uraian,
										a.nama
									)
								)
							) as uraian,
							a.nilai,
							a.ppn,
							a.pph_21,
							a.pph_22,
							a.pph_23,
							a.pph_24,
							substr(b.kdgol,1,1) as kdgol
					from d_rko_pagu2 a
					left outer join t_pegawai b on(a.nip=b.nip)
					left outer join t_pegawai_non c on(a.id_peg_non=c.id)
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
				$html_out.= '<th class="tl">SP DIPA- 023.11.1.137608/2018 Tgl. 2 Juli 2018</th>';
			$html_out.= '</tr>';
			$html_out.= '<tr>';
				$html_out.= '<th class="tl">JUMLAH UP-RM</th>';
				$html_out.= '<th class="tc">:</th>';
				$html_out.= '<th class="tl">'.number_format($rows_program[0]->nilai,0,",",".").'</th>';
			$html_out.= '</tr>';
			$html_out.= '<tr>';
				$html_out.= '<th class="tl">UNIT KERJA</th>';
				$html_out.= '<th class="tc">:</th>';
				$html_out.= '<th class="tl">'.$rows_program[0]->nama_unit.'</th>';
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
					$html_out .= '<th class="bd pd wd10">JUMLAH YANG DIMINTAKAN</th>';
					$html_out .= '<th class="bd pd wd7">Gol.</th>';
					$html_out .= '<th class="bd pd wd7">PPN</th>';
					$html_out .= '<th class="bd pd wd7">PPh21</th>';
					$html_out .= '<th class="bd pd wd7">PPh22</th>';
					$html_out .= '<th class="bd pd wd7">PPh23</th>';
					//~ $html_out .= '<th class="bd pd wd7">PPh24</th>';
				$html_out.= '</tr>';
				$html_out.= '<tr>';
					$html_out .= '<th class="fz60 bd">1</th>';
					$html_out .= '<th class="fz60 bd">2</th>';
					$html_out .= '<th class="fz60 bd">3</th>';
					$html_out .= '<th class="fz60 bd">4</th>';
					$html_out .= '<th class="fz60 bd">5</th>';
					$html_out .= '<th class="fz60 bd">6</th>';
					$html_out .= '<th class="fz60 bd">7</th>';
					$html_out .= '<th class="fz60 bd">8</th>';
					$html_out .= '<th class="fz60 bd">9</th>';
					$html_out .= '<th class="fz60 bd">10</th>';
				$html_out.= '</tr>';
			$html_out.= '</thead>';
			$html_out.= '<tbody>';
				$html_out.= '<tr>';
					$html_out.= '<td class="bdlr vt fwb">&nbsp;</td>';
					$html_out.= '<td class="bdlr vt tc fwb">'.$rows_program[0]->kode.'</td>';
					$html_out.= '<td class="bdlr vt tc fwb">&nbsp;</td>';
					$html_out.= '<td class="bdlr vt tl fwb">'.$rows_program[0]->nmprogram.'</td>';
					$html_out.= '<td class="bdlr vt tr fwb">'.number_format($rows_program[0]->nilai,0,',','.').'</td>';
					$html_out.= '<td class="bdlr vt fwb">&nbsp;</td>';
					$html_out.= '<td class="bdlr vt fwb">&nbsp;</td>';
					$html_out.= '<td class="bdlr vt fwb">&nbsp;</td>';
					$html_out.= '<td class="bdlr vt fwb">&nbsp;</td>';
					$html_out.= '<td class="bdlr vt fwb">&nbsp;</td>';
				$html_out.= '</tr>';
			
			foreach($rows_akun as $ra) {
				$html_out.= '<tr>';
					$html_out.= '<td class="bdlr fwb">&nbsp;</td>';
					$html_out.= '<td class="bdlr fwb">&nbsp;</td>';
					$html_out.= '<td class="bdlr tr fwb">'.$ra->kdakun.'</td>';
					$html_out.= '<td class="bdlr tl fwb">'.$ra->nmakun.'</td>';
					$html_out.= '<td class="bdlr tr fwb">'.number_format($ra->nilai,0,',','.').'</td>';
					$html_out.= '<td class="bdlr tr fwb">&nbsp;</td>';
					$html_out.= '<td class="bdlr tr fwb">'.number_format($ra->ppn,0,',','.').'</td>';
					$html_out.= '<td class="bdlr tr fwb">'.number_format($ra->pph_21,0,',','.').'</td>';
					$html_out.= '<td class="bdlr tr fwb">'.number_format($ra->pph_22,0,',','.').'</td>';
					$html_out.= '<td class="bdlr tr fwb">'.number_format($ra->pph_23,0,',','.').'</td>';
					//~ $html_out.= '<td class="bdlr tr fwb">'.number_format($ra->pph_24,0,',','.').'</td>';
				$html_out.= '</tr>';
				
				foreach(rows_uraian($ra->id_rko, $ra->kdakun) as $ru) {
					$html_out.= '<tr>';
						$html_out.= '<td class="bdlr ">&nbsp;</td>';
						$html_out.= '<td class="bdlr ">&nbsp;</td>';
						$html_out.= '<td class="bdlr tr">&nbsp;</td>';
						$html_out.= '<td class="bdlr tl">&nbsp;&nbsp; - &nbsp;'.$ru->uraian.'</td>';
						$html_out.= '<td class="bdlr tr">'.number_format($ru->nilai,0,',','.').'</td>';
						$html_out.= '<td class="bdlr tc">'.$ru->kdgol.'</td>';
						$html_out.= '<td class="bdlr tr">'.number_format($ru->ppn,0,',','.').'</td>';
						$html_out.= '<td class="bdlr tr">'.number_format($ru->pph_21,0,',','.').'</td>';
						$html_out.= '<td class="bdlr tr">'.number_format($ru->pph_22,0,',','.').'</td>';
						$html_out.= '<td class="bdlr tr">'.number_format($ru->pph_23,0,',','.').'</td>';
						//~ $html_out.= '<td class="bdlr tr">'.number_format($ru->pph_24,0,',','.').'</td>';
					$html_out.= '</tr>';
				}
			}
			
			$html_out.= '<tr>';
				$html_out.= '<td class="pd bd fwb">&nbsp;</td>';
				$html_out.= '<td class="pd bd fwb">&nbsp;</td>';
				$html_out.= '<td colspan="2" class="pd bd fwb">JUMLAH</td>';
				$html_out.= '<td class="pd bd tr fwb">'.number_format($rows_program[0]->nilai,0,',','.').'</td>';
				$html_out.= '<td class="pd bd tr fwb">&nbsp;</td>';
				$html_out.= '<td class="pd bd tr fwb">'.number_format($rows_program[0]->ppn,0,',','.').'</td>';
				$html_out.= '<td class="pd bd tr fwb">'.number_format($rows_program[0]->pph_21,0,',','.').'</td>';
				$html_out.= '<td class="pd bd tr fwb">'.number_format($rows_program[0]->pph_22,0,',','.').'</td>';
				$html_out.= '<td class="pd bd tr fwb">'.number_format($rows_program[0]->pph_23,0,',','.').'</td>';
				//~ $html_out.= '<td class="pd bd tr fwb">'.number_format($rows_program[0]->pph_24,0,',','.').'</td>';
			$html_out.= '</tr>';
			
			$html_out.= '</tbody>';
			$html_out.= '</table>';
			$obj_ppk = HTMLController::refPPK();
			$obj_bpp = HTMLController::refBPP();
			
			if($rows_program[0]->is_sekre=='1'){
				
				$html_out.= '<table class="fz60">
							<tbody>
								<tr>
									<td colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td class="">&nbsp;</td>
									<td class="">&nbsp;</td>
									<td class="">&nbsp;</td>
									<td class="">Jakarta, '.HTMLController::today().'</td>
								</tr>
								<tr>
									<td class="">Pejabat Pembuat Komitmen</td>
									<td class="">Bendahara Pengeluaran Pembantu</td>
									<td class="">Pelaksana Kegiatan II</td>
									<td class="">Pelaksana Kegiatan I</td>
								</tr>
								<tr>
									<td class="" colspan="4">&nbsp;</td>
								</tr>
								<tr>
									<td class="" colspan="4">&nbsp;</td>
								</tr>
								<tr>
									<td class="" colspan="4">&nbsp;</td>
								</tr>
								<tr>
									<td class="wd30">'.$rows_program[0]->nama_ppk.'</td>
									<td class="wd30">'.$rows_program[0]->nama_bpp.'</td>
									<td class="wd30">'.$rows_program[0]->nama_pk2.'</td>
									<td class="wd30">'.$rows_program[0]->nama_pk1.'</td>
								</tr>
								<tr>
									<td class="wd30">NIP '.$rows_program[0]->nip_ppk.'</td>
									<td class="wd30">NIP '.$rows_program[0]->nip_bpp.'</td>
									<td class="wd30">NIP '.$rows_program[0]->nip_pk2.'</td>
									<td class="wd30">NIP '.$rows_program[0]->nip_pk1.'</td>
								</tr>
							</tbody>
						</table>';
				
			}
			else{
				
				$html_out.= '<table class="fz60">
							<tbody>
								<tr>
									<td colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td class="">&nbsp;</td>
									<td class="">&nbsp;</td>
									<td class="">&nbsp;</td>
									<td class="">Jakarta, '.HTMLController::today().'</td>
								</tr>
								<tr>
									<td class="">Pejabat Pembuat Komitmen</td>
									<td class="">&nbsp;</td>
									<td class="">&nbsp;</td>
									<td class="">Bendahara Pengeluaran Pembantu</td>
								</tr>
								<tr>
									<td class="" colspan="4">&nbsp;</td>
								</tr>
								<tr>
									<td class="" colspan="4">&nbsp;</td>
								</tr>
								<tr>
									<td class="" colspan="4">&nbsp;</td>
								</tr>
								<tr>
									<td class="wd30">'.$rows_program[0]->nama_ppk.'</td>
									<td class="wd30">&nbsp;</td>
									<td class="wd30">&nbsp;</td>
									<td class="wd30">'.$rows_program[0]->nama_bpp.'</td>
								</tr>
								<tr>
									<td class="wd30">NIP '.$rows_program[0]->nip_ppk.'</td>
									<td class="wd30">&nbsp;</td>
									<td class="wd30">&nbsp;</td>
									<td class="wd30">NIP '.$rows_program[0]->nip_bpp.'</td>
								</tr>
							</tbody>
						</table>';
				
			}
			
			$mpdf = new mPDF("en", "A4", "15");
			$mpdf->SetTitle('Form RKO');
			
			$mpdf->AddPage('L');
			$mpdf->writeHTML($html_out);
			$mpdf->Output('Form_RKO_GUP_'.$id.'.pdf','I');
		//~ }
		//~ catch(\Exception $e){
			//~ return $e;
			//~ //return 'Terdapat kesalahan lainnya!';
		//~ }
	}
	
	public function download_dok($param)
	{
		try{
			$path='data/dok/rko/';

			$log = $path.$param;
								
			header('Content-Description:Berkas Dokumen RKO');
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
	
	public function sisa()
	{
		try{
			$rows = DB::select("
				SELECT	SUM(IF(a.sisa_umk>0 AND a.selisih_hari>10,1,0)) AS cek
				FROM(
					SELECT	a.id_rko,
						IFNULL(DATEDIFF(d.created_at,b.created_at)+1,0) AS selisih_hari,
						IFNULL(c.nilai,0)-IFNULL(d.nilai,0)-IFNULL(e.nilai,0) AS sisa_umk
					FROM(
						SELECT	id_rko,
							MAX(id) AS max_id
						FROM d_rko_status
						WHERE nourut=10
						GROUP BY id_rko
					) a
					LEFT OUTER JOIN d_rko_status b ON(a.max_id=b.id)
					LEFT OUTER JOIN(
						SELECT	id_rko,
							SUM(nilai) AS nilai
						FROM d_rko_pagu2
						GROUP BY id_rko
					) c ON(a.id_rko=c.id_rko)
					LEFT OUTER JOIN(
						SELECT	c.id_rko,
							MAX(b.created_at) AS created_at,
							SUM(c.totnilmak) AS nilai
						FROM(
							SELECT	id_trans,
								MAX(id) AS max_id
							FROM d_transaksi_status
							WHERE nourut=2
							GROUP BY id_trans
						) a
						LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
						LEFT OUTER JOIN d_transaksi c ON(a.id_trans=c.id)
						GROUP BY c.id_rko
					) d ON(a.id_rko=d.id_rko)
					LEFT OUTER JOIN(
						SELECT	id_rko,
							SUM(nilai) AS nilai
						FROM d_cek_bpp
						GROUP BY id_rko
					) e ON(a.id_rko=e.id_rko)
				) a
				LEFT OUTER JOIN d_rko b ON(a.id_rko=b.id)
				WHERE b.kdppk=?
			",[
				session('kdppk')
			]);
			
			if(count($rows)>0){
				if($rows[0]->cek==0){
					return true;
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
			
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function sisa_pagu()
	{
		try{
			$rows = DB::select("
				SELECT	CONCAT(a.kdprogram,'-',a.kdgiat,'-',a.kdoutput,'-',a.kdsoutput,'-',a.kdkmpnen,'-',a.kdskmpnen,'-',a.kdakun) AS kode,
					IFNULL(b.nmakun,'Referensi tidak ada') AS uraian,
					a.jumlah AS pagu,
					IFNULL(a.nilai,0) AS realisasi
				FROM(
					SELECT	a.*,
						b.nilai
					FROM(
						/* cari pagu */
						SELECT 	kddept,
							kdunit,
							kdsatker,
							thang,
							kdprogram,
							kdgiat,
							kdoutput,
							kdsoutput,
							kdkmpnen,
							kdskmpnen,
							kdakun,
							SUM(paguakhir) AS jumlah
						FROM d_pagu
						WHERE lvl=7
						GROUP BY kddept,kdunit,kdsatker,thang,kdprogram,kdgiat,kdoutput,kdsoutput,kdkmpnen,kdskmpnen,kdakun
						ORDER BY kddept,kdunit,kdsatker,thang,kdprogram,kdgiat,kdoutput,kdsoutput,kdkmpnen,kdskmpnen,kdakun
					) a
					LEFT OUTER JOIN(
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
						WHERE c.nourut>=4 AND a.kdprogram='".$kdprogram."' AND a.kdgiat='".$kdgiat."' AND a.kdoutput='".$kdoutput."' AND a.kdsoutput='".$kdsoutput."' AND a.kdkmpnen='".$kdkmpnen."' AND a.kdskmpnen='".$kdskmpnen."' AND a.kdakun='".$kdakun."'
						GROUP BY b.kdsatker,b.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen,a.kdkmpnen,a.kdskmpnen,a.kdakun
					) b ON(a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.kdprogram=b.kdprogram AND a.kdgiat=b.kdgiat AND a.kdoutput=b.kdoutput AND a.kdsoutput=b.kdsoutput AND a.kdkmpnen=b.kdkmpnen AND a.kdskmpnen=b.kdskmpnen AND a.kdakun=b.kdakun)
				) a
				LEFT OUTER JOIN t_akun b ON(a.kdakun=b.kdakun)
				WHERE a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdprogram='".$kdprogram."' AND a.kdgiat='".$kdgiat."' AND a.kdoutput='".$kdoutput."' AND a.kdsoutput='".$kdsoutput."' AND a.kdkmpnen='".$kdkmpnen."' AND a.kdskmpnen='".$kdskmpnen."' AND a.kdakun='".$kdakun."'
			");
			
			if(count($rows)>0){
				if($rows[0]->cek==0){
					return true;
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
			
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
}

