<?php namespace App\Http\Controllers;

use DB;
use Session;
use Storage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RealisasiController extends Controller {

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
						 WHERE lvl = 7) t";
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
	
	public function getBreakdown(Request $request, $param)
	{
		try{
			$param = explode("-", $param);
			if($param[1]=='xxx'){
				return $this->program(session('kddept'),session('kdunit'),session('kdsatker'),session('tahun'),$param[0]);
			}
			elseif(count($param)==2){//get kegiatan
				return $this->kegiatan(session('kddept'),session('kdunit'),session('kdsatker'),session('tahun'),$param[0],$param[1]); //param=program
			}
			elseif(count($param)==3){
				return $this->output(session('kddept'),session('kdunit'),session('kdsatker'),session('tahun'),$param[0],$param[1],$param[2]); //param=program-kegiatan
			}
			elseif(count($param)==4){
				return $this->soutput(session('kddept'),session('kdunit'),session('kdsatker'),session('tahun'),$param[0],$param[1],$param[2],$param[3]); //param=program-kegiatan-output
			}
			elseif(count($param)==5){
				return $this->komponen(session('kddept'),session('kdunit'),session('kdsatker'),session('tahun'),$param[0],$param[1],$param[2],$param[3],$param[4]); //param=program-kegiatan-output-soutput
			}
			elseif(count($param)==6){
				return $this->skomponen(session('kddept'),session('kdunit'),session('kdsatker'),session('tahun'),$param[0],$param[1],$param[2],$param[3],$param[4],$param[5]); //param=program-kegiatan-output-soutput-kompenen
			}
			elseif(count($param)==7){
				return $this->akun(session('kddept'),session('kdunit'),session('kdsatker'),session('tahun'),$param[0],$param[1],$param[2],$param[3],$param[4],$param[5],$param[6]); //param=program-kegiatan-output-soutput-kompenen-skompnen
			}
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function program($kddept, $kdunit, $kdsatker, $tahun, $status)
	{
		$where = "";
		if($status=='1'){ //sd RKO
		
			$where = "/* cari realisasi */
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
					GROUP BY b.kdsatker,b.thang,a.kdprogram";
		
		}
		elseif($status=='2'){ //sd UMK
			
			$where = "/* cari realisasi */
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
					WHERE c.nourut>=11
					GROUP BY b.kdsatker,b.thang,a.kdprogram";
		
		}
		elseif($status=='3'){ //sd Kuitansi
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN(
						SELECT	a.id_trans,
							b.nourut
						FROM(
							SELECT	id_trans,
								MAX(id) AS max_id
							FROM d_transaksi_status b
							GROUP BY id_trans
						) a
						LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
					) b ON(a.id=b.id_trans)
					WHERE b.nourut>=0
					GROUP BY a.kdsatker,a.thang,a.kdprogram";
		
		}
		elseif($status=='4'){ //sd DRPP
		
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN(
						SELECT	a.id_trans,
							b.nourut
						FROM(
							SELECT	id_trans,
								MAX(id) AS max_id
							FROM d_transaksi_status b
							GROUP BY id_trans
						) a
						LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
					) b ON(a.id=b.id_trans)
					WHERE b.nourut>=3
					GROUP BY a.kdsatker,a.thang,a.kdprogram";
		
		}
		elseif($status=='5'){ //sd SPP
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN(
						SELECT	a.id_trans,
							b.nourut
						FROM(
							SELECT	id_trans,
								MAX(id) AS max_id
							FROM d_transaksi_status b
							GROUP BY id_trans
						) a
						LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
					) b ON(a.id=b.id_trans)
					WHERE b.nourut>=5
					GROUP BY a.kdsatker,a.thang,a.kdprogram";
		
		}
		elseif($status=='6'){ //sd SPM
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN d_drpp b ON(a.nodrpp=b.nodrpp)
					LEFT OUTER JOIN d_spp c ON(b.nospp=c.nospp)
					WHERE c.nospm IS NOT NULL
					GROUP BY a.kdsatker,a.thang,a.kdprogram";
			
		}
		elseif($status=='7'){ //sd SP2D
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN d_drpp b ON(a.nodrpp=b.nodrpp)
					LEFT OUTER JOIN d_spp c ON(b.nospp=c.nospp)
					WHERE c.nosp2d IS NOT NULL
					GROUP BY a.kdsatker,a.thang,a.kdprogram";
		
		}
		
		$rows = DB::select("
			SELECT	a.kdprogram AS kode,
				IFNULL(b.uraian,'Referensi tidak ada') AS uraian,
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
						SUM(paguakhir) AS jumlah
					FROM d_pagu
					WHERE lvl=7
					GROUP BY kddept,kdunit,kdsatker,thang,kdprogram
					ORDER BY kddept,kdunit,kdsatker,thang,kdprogram
				) a
				LEFT OUTER JOIN(
					".$where."
				) b ON(a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.kdprogram=b.kdprogram)
			) a
			LEFT OUTER JOIN(
				SELECT kdprogram,uraian FROM d_pagu WHERE lvl=1
			) b ON(a.kdprogram=b.kdprogram)
			WHERE a.kdsatker=? and a.thang=?
		", [
			$kdsatker,
			$tahun
		]);
		
		if(count($rows)>0){
			$data='';
			foreach($rows as $row){
				$data.='<li style="list-style: none; margin-left: 0;">
							<div class="row">
								<div class="col-lg-2">'.$row->kode.'</div>
								<div class="col-lg-4">
									<a href="javascript:;" id="'.$row->kode.'" class="breakdown">'.$row->uraian.'</a>
								</div>
								<div class="col-lg-2" style="text-align:right;">'.number_format($row->pagu).'</div>
								<div class="col-lg-2" style="text-align:right;">'.number_format($row->realisasi).'</div>
								<div class="col-lg-2" style="text-align:right;">'.number_format($row->pagu-$row->realisasi).'</div>
							</div>
						</li>
						<ul id="ul-'.$status.'-'.$row->kode.'"></ul>
						<hr>';
			}
			
			return $data;
		}
		else{
			return 'Data tidak ditemukan!';
		}
	}
	
	public function kegiatan($kddept, $kdunit, $kdsatker, $tahun, $status, $kdprogram)
	{
		$where = "";
		if($status=='1'){ //sd RKO
		
			$where = "/* cari realisasi */
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
					WHERE c.nourut>=4 and a.kdprogram='".$kdprogram."'
					GROUP BY b.kdsatker,b.thang,a.kdprogram,a.kdgiat";
		
		}
		elseif($status=='2'){ //sd UMK
			
			$where = "/* cari realisasi */
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
					WHERE c.nourut>=11 and a.kdprogram='".$kdprogram."'
					GROUP BY b.kdsatker,b.thang,a.kdprogram,a.kdgiat";
		
		}
		elseif($status=='3'){ //sd Kuitansi
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN(
						SELECT	a.id_trans,
							b.nourut
						FROM(
							SELECT	id_trans,
								MAX(id) AS max_id
							FROM d_transaksi_status b
							GROUP BY id_trans
						) a
						LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
					) b ON(a.id=b.id_trans)
					WHERE b.nourut>=0 and a.kdprogram='".$kdprogram."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat";
		
		}
		elseif($status=='4'){ //sd DRPP
		
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN(
						SELECT	a.id_trans,
							b.nourut
						FROM(
							SELECT	id_trans,
								MAX(id) AS max_id
							FROM d_transaksi_status b
							GROUP BY id_trans
						) a
						LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
					) b ON(a.id=b.id_trans)
					WHERE b.nourut>=3 and a.kdprogram='".$kdprogram."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat";
		
		}
		elseif($status=='5'){ //sd SPP
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN(
						SELECT	a.id_trans,
							b.nourut
						FROM(
							SELECT	id_trans,
								MAX(id) AS max_id
							FROM d_transaksi_status b
							GROUP BY id_trans
						) a
						LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
					) b ON(a.id=b.id_trans)
					WHERE b.nourut>=5 and a.kdprogram='".$kdprogram."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat";
		
		}
		elseif($status=='6'){ //sd SPM
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN d_drpp b ON(a.nodrpp=b.nodrpp)
					LEFT OUTER JOIN d_spp c ON(b.nospp=c.nospp)
					WHERE c.nospm IS NOT NULL and a.kdprogram='".$kdprogram."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat";
			
		}
		elseif($status=='7'){ //sd SP2D
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN d_drpp b ON(a.nodrpp=b.nodrpp)
					LEFT OUTER JOIN d_spp c ON(b.nospp=c.nospp)
					WHERE c.nosp2d IS NOT NULL and a.kdprogram='".$kdprogram."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat";
		
		}
		
		$rows = DB::select("
			SELECT	CONCAT(a.kdprogram,'-',a.kdgiat) AS kode,
				IFNULL(b.uraian,'Referensi tidak ada') AS uraian,
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
						SUM(paguakhir) AS jumlah
					FROM d_pagu
					WHERE lvl=7
					GROUP BY kddept,kdunit,kdsatker,thang,kdprogram,kdgiat
					ORDER BY kddept,kdunit,kdsatker,thang,kdprogram,kdgiat
				) a
				LEFT OUTER JOIN(
					".$where."
				) b ON(a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.kdprogram=b.kdprogram AND a.kdgiat=b.kdgiat)
			) a
			LEFT OUTER JOIN(
				SELECT kdprogram,kdgiat,uraian FROM d_pagu WHERE lvl=2
			) b ON(a.kdprogram=b.kdprogram AND a.kdgiat=b.kdgiat)
			WHERE a.kdsatker=? AND a.thang=? AND a.kdprogram=?
		", [
			$kdsatker,
			$tahun,
			$kdprogram
		]);
		
		$data='';
		foreach($rows as $row){
			$data.='<li style="list-style: none; margin-left: 20px;">
						<div class="row">
							<div class="col-lg-2">'.$row->kode.'</div>
							<div class="col-lg-4">
								<a href="javascript:;" id="'.$row->kode.'" class="breakdown">'.$row->uraian.'</a>
							</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->pagu).'</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->realisasi).'</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->pagu-$row->realisasi).'</div>
						</div>
					</li>
					<ul id="ul-'.$status.'-'.$row->kode.'"></ul>
					<hr>';
		}
		
		return $data;
	}
	
	public function output($kddept, $kdunit, $kdsatker, $tahun, $status, $kdprogram, $kdgiat)
	{
		$where = "";
		if($status=='1'){ //sd RKO
		
			$where = "/* cari realisasi */
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
					WHERE c.nourut>=4 and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."'
					GROUP BY b.kdsatker,b.thang,a.kdprogram,a.kdgiat,a.kdoutput";
		
		}
		elseif($status=='2'){ //sd UMK
			
			$where = "/* cari realisasi */
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
					WHERE c.nourut>=11  and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."'
					GROUP BY b.kdsatker,b.thang,a.kdprogram,a.kdgiat,a.kdoutput";
		
		}
		elseif($status=='3'){ //sd Kuitansi
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN(
						SELECT	a.id_trans,
							b.nourut
						FROM(
							SELECT	id_trans,
								MAX(id) AS max_id
							FROM d_transaksi_status b
							GROUP BY id_trans
						) a
						LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
					) b ON(a.id=b.id_trans)
					WHERE b.nourut>=0 and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput";
		
		}
		elseif($status=='4'){ //sd DRPP
		
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN(
						SELECT	a.id_trans,
							b.nourut
						FROM(
							SELECT	id_trans,
								MAX(id) AS max_id
							FROM d_transaksi_status b
							GROUP BY id_trans
						) a
						LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
					) b ON(a.id=b.id_trans)
					WHERE b.nourut>=3 and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput";
		
		}
		elseif($status=='5'){ //sd SPP
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN(
						SELECT	a.id_trans,
							b.nourut
						FROM(
							SELECT	id_trans,
								MAX(id) AS max_id
							FROM d_transaksi_status b
							GROUP BY id_trans
						) a
						LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
					) b ON(a.id=b.id_trans)
					WHERE b.nourut>=5 and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput";
		
		}
		elseif($status=='6'){ //sd SPM
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN d_drpp b ON(a.nodrpp=b.nodrpp)
					LEFT OUTER JOIN d_spp c ON(b.nospp=c.nospp)
					WHERE c.nospm IS NOT NULL and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput";
			
		}
		elseif($status=='7'){ //sd SP2D
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN d_drpp b ON(a.nodrpp=b.nodrpp)
					LEFT OUTER JOIN d_spp c ON(b.nospp=c.nospp)
					WHERE c.nosp2d IS NOT NULL and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput";
		
		}
		
		$rows = DB::select("
			SELECT	CONCAT(a.kdprogram,'-',a.kdgiat,'-',a.kdoutput) AS kode,
				IFNULL(b.uraian,'Referensi tidak ada') AS uraian,
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
						SUM(paguakhir) AS jumlah
					FROM d_pagu
					WHERE lvl=7
					GROUP BY kddept,kdunit,kdsatker,thang,kdprogram,kdgiat,kdoutput
					ORDER BY kddept,kdunit,kdsatker,thang,kdprogram,kdgiat,kdoutput
				) a
				LEFT OUTER JOIN(
					".$where."
				) b ON(a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.kdprogram=b.kdprogram AND a.kdgiat=b.kdgiat AND a.kdoutput=b.kdoutput)
			) a
			LEFT OUTER JOIN(
				SELECT kdprogram,kdgiat,kdoutput,kdlokasi,kdkabkota,uraian FROM d_pagu WHERE lvl=3
			) b ON(a.kdprogram=b.kdprogram and a.kdgiat=b.kdgiat and a.kdoutput=b.kdoutput)
			WHERE a.kdsatker=? AND a.thang=? AND a.kdprogram=? and a.kdgiat=?
		", [
			$kdsatker,
			$tahun,
			$kdprogram,
			$kdgiat
		]);
		
		$data='';
		foreach($rows as $row){
			$data.='<li style="list-style: none; margin-left: 40px;">
						<div class="row">
							<div class="col-lg-2">'.$row->kode.'</div>
							<div class="col-lg-4">
								<a href="javascript:;" id="'.$row->kode.'" class="breakdown">'.$row->uraian.'</a>
							</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->pagu).'</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->realisasi).'</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->pagu-$row->realisasi).'</div>
						</div>
					</li>
					<ul id="ul-'.$status.'-'.$row->kode.'"></ul>
					<hr>';
		}
		
		return $data;
	}
	
	public function soutput($kddept, $kdunit, $kdsatker, $tahun, $status, $kdprogram, $kdgiat, $kdoutput)
	{
		$where = "";
		if($status=='1'){ //sd RKO
		
			$where = "/* cari realisasi */
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
					WHERE c.nourut>=4 and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."'
					GROUP BY b.kdsatker,b.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput";
		
		}
		elseif($status=='2'){ //sd UMK
			
			$where = "/* cari realisasi */
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
					WHERE c.nourut>=11  and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."'
					GROUP BY b.kdsatker,b.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput";
		
		}
		elseif($status=='3'){ //sd Kuitansi
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN(
						SELECT	a.id_trans,
							b.nourut
						FROM(
							SELECT	id_trans,
								MAX(id) AS max_id
							FROM d_transaksi_status b
							GROUP BY id_trans
						) a
						LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
					) b ON(a.id=b.id_trans)
					WHERE b.nourut>=0 and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput";
		
		}
		elseif($status=='4'){ //sd DRPP
		
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN(
						SELECT	a.id_trans,
							b.nourut
						FROM(
							SELECT	id_trans,
								MAX(id) AS max_id
							FROM d_transaksi_status b
							GROUP BY id_trans
						) a
						LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
					) b ON(a.id=b.id_trans)
					WHERE b.nourut>=3 and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput";
		
		}
		elseif($status=='5'){ //sd SPP
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN(
						SELECT	a.id_trans,
							b.nourut
						FROM(
							SELECT	id_trans,
								MAX(id) AS max_id
							FROM d_transaksi_status b
							GROUP BY id_trans
						) a
						LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
					) b ON(a.id=b.id_trans)
					WHERE b.nourut>=5 and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput";
		
		}
		elseif($status=='6'){ //sd SPM
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN d_drpp b ON(a.nodrpp=b.nodrpp)
					LEFT OUTER JOIN d_spp c ON(b.nospp=c.nospp)
					WHERE c.nospm IS NOT NULL and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput";
			
		}
		elseif($status=='7'){ //sd SP2D
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN d_drpp b ON(a.nodrpp=b.nodrpp)
					LEFT OUTER JOIN d_spp c ON(b.nospp=c.nospp)
					WHERE c.nosp2d IS NOT NULL and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput";
		
		}
		
		$rows = DB::select("
			SELECT	CONCAT(a.kdprogram,'-',a.kdgiat,'-',a.kdoutput,'-',a.kdsoutput) AS kode,
				IFNULL(b.uraian,'Referensi tidak ada') AS uraian,
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
						SUM(paguakhir) AS jumlah
					FROM d_pagu
					WHERE lvl=7
					GROUP BY kddept,kdunit,kdsatker,thang,kdprogram,kdgiat,kdoutput,kdsoutput
					ORDER BY kddept,kdunit,kdsatker,thang,kdprogram,kdgiat,kdoutput,kdsoutput
				) a
				LEFT OUTER JOIN(
					".$where."
				) b ON(a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.kdprogram=b.kdprogram AND a.kdgiat=b.kdgiat and a.kdoutput=b.kdoutput and a.kdsoutput=b.kdsoutput)
			) a
			LEFT OUTER JOIN(
				SELECT kdprogram,kdgiat,kdoutput,kdlokasi,kdkabkota,kdsoutput,uraian FROM d_pagu WHERE lvl=4
			) b ON(a.kdprogram=b.kdprogram and a.kdgiat=b.kdgiat and a.kdoutput=b.kdoutput and a.kdsoutput=b.kdsoutput)
			WHERE a.kdsatker=? AND a.thang=? AND a.kdprogram=? and a.kdgiat=? and a.kdoutput=?
		", [
			$kdsatker,
			$tahun,
			$kdprogram,
			$kdgiat,
			$kdoutput
		]);
		
		$data='';
		foreach($rows as $row){
			$data.='<li style="list-style: none; margin-left: 60px;">
						<div class="row">
							<div class="col-lg-2">'.$row->kode.'</div>
							<div class="col-lg-4">
								<a href="javascript:;" id="'.$row->kode.'" class="breakdown">'.$row->uraian.'</a>
							</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->pagu).'</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->realisasi).'</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->pagu-$row->realisasi).'</div>
						</div>
					</li>
					<ul id="ul-'.$status.'-'.$row->kode.'"></ul>
					<hr>';
		}
		
		return $data;
	}
	
	public function komponen($kddept, $kdunit, $kdsatker, $tahun, $status, $kdprogram, $kdgiat, $kdoutput, $kdsoutput)
	{
		$where = "";
		if($status=='1'){ //sd RKO
		
			$where = "/* cari realisasi */
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
					WHERE c.nourut>=4 and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."' and a.kdsoutput='".$kdsoutput."'
					GROUP BY b.kdsatker,b.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen,a.kdkmpnen";
		
		}
		elseif($status=='2'){ //sd UMK
			
			$where = "/* cari realisasi */
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
					WHERE c.nourut>=11  and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."' and a.kdsoutput='".$kdsoutput."'
					GROUP BY b.kdsatker,b.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen";
		
		}
		elseif($status=='3'){ //sd Kuitansi
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						a.kdkmpnen,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN(
						SELECT	a.id_trans,
							b.nourut
						FROM(
							SELECT	id_trans,
								MAX(id) AS max_id
							FROM d_transaksi_status b
							GROUP BY id_trans
						) a
						LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
					) b ON(a.id=b.id_trans)
					WHERE b.nourut>=0 and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."' and a.kdsoutput='".$kdsoutput."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen";
		
		}
		elseif($status=='4'){ //sd DRPP
		
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						a.kdkmpnen,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN(
						SELECT	a.id_trans,
							b.nourut
						FROM(
							SELECT	id_trans,
								MAX(id) AS max_id
							FROM d_transaksi_status b
							GROUP BY id_trans
						) a
						LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
					) b ON(a.id=b.id_trans)
					WHERE b.nourut>=3 and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."' and a.kdsoutput='".$kdsoutput."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen";
		
		}
		elseif($status=='5'){ //sd SPP
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						a.kdkmpnen,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN(
						SELECT	a.id_trans,
							b.nourut
						FROM(
							SELECT	id_trans,
								MAX(id) AS max_id
							FROM d_transaksi_status b
							GROUP BY id_trans
						) a
						LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
					) b ON(a.id=b.id_trans)
					WHERE b.nourut>=5 and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."' and a.kdsoutput='".$kdsoutput."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen";
		
		}
		elseif($status=='6'){ //sd SPM
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						a.kdkmpnen,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN d_drpp b ON(a.nodrpp=b.nodrpp)
					LEFT OUTER JOIN d_spp c ON(b.nospp=c.nospp)
					WHERE c.nospm IS NOT NULL and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."' and a.kdsoutput='".$kdsoutput."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen";
			
		}
		elseif($status=='7'){ //sd SP2D
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						a.kdkmpnen,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN d_drpp b ON(a.nodrpp=b.nodrpp)
					LEFT OUTER JOIN d_spp c ON(b.nospp=c.nospp)
					WHERE c.nosp2d IS NOT NULL and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."' and a.kdsoutput='".$kdsoutput."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen";
		
		}
		
		$rows = DB::select("
			SELECT	CONCAT(a.kdprogram,'-',a.kdgiat,'-',a.kdoutput,'-',a.kdsoutput,'-',a.kdkmpnen) AS kode,
				IFNULL(b.uraian,'Referensi tidak ada') AS uraian,
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
						SUM(paguakhir) AS jumlah
					FROM d_pagu
					WHERE lvl=7
					GROUP BY kddept,kdunit,kdsatker,thang,kdprogram,kdgiat,kdoutput,kdsoutput,kdkmpnen
					ORDER BY kddept,kdunit,kdsatker,thang,kdprogram,kdgiat,kdoutput,kdsoutput,kdkmpnen
				) a
				LEFT OUTER JOIN(
					".$where."
				) b ON(a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.kdprogram=b.kdprogram AND a.kdgiat=b.kdgiat and a.kdoutput=b.kdoutput and a.kdsoutput=b.kdsoutput and a.kdkmpnen=b.kdkmpnen)
			) a
			LEFT OUTER JOIN(
				SELECT kdprogram,kdgiat,kdoutput,kdlokasi,kdkabkota,kdsoutput,kdkmpnen,uraian FROM d_pagu WHERE lvl=5
			) b ON(a.kdprogram=b.kdprogram and a.kdgiat=b.kdgiat and a.kdoutput=b.kdoutput and a.kdsoutput=b.kdsoutput and a.kdkmpnen=b.kdkmpnen)
			WHERE a.kdsatker=? AND a.thang=? AND a.kdprogram=? and a.kdgiat=? and a.kdoutput=? and a.kdsoutput=?
		", [
			$kdsatker,
			$tahun,
			$kdprogram,
			$kdgiat,
			$kdoutput,
			$kdsoutput
		]);
		
		$data='';
		foreach($rows as $row){
			$data.='<li style="list-style: none; margin-left: 80px;">
						<div class="row">
							<div class="col-lg-2">'.$row->kode.'</div>
							<div class="col-lg-4">
								<a href="javascript:;" id="'.$row->kode.'" class="breakdown">'.$row->uraian.'</a>
							</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->pagu).'</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->realisasi).'</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->pagu-$row->realisasi).'</div>
						</div>
					</li>
					<ul id="ul-'.$status.'-'.$row->kode.'"></ul>
					<hr>';
		}
		
		return $data;
	}
	
	public function skomponen($kddept, $kdunit, $kdsatker, $tahun,$status, $kdprogram, $kdgiat, $kdoutput, $kdsoutput, $kdkmpnen)
	{
		$where = "";
		if($status=='1'){ //sd RKO
		
			$where = "/* cari realisasi */
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
					WHERE c.nourut>=4 and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."' and a.kdsoutput='".$kdsoutput."' and a.kdkmpnen='".$kdkmpnen."'
					GROUP BY b.kdsatker,b.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen,a.kdkmpnen,a.kdskmpnen";
		
		}
		elseif($status=='2'){ //sd UMK
			
			$where = "/* cari realisasi */
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
					WHERE c.nourut>=11  and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."' and a.kdsoutput='".$kdsoutput."' and a.kdkmpnen='".$kdkmpnen."'
					GROUP BY b.kdsatker,b.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen,a.kdskmpnen";
		
		}
		elseif($status=='3'){ //sd Kuitansi
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						a.kdkmpnen,
						a.kdskmpnen,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN(
						SELECT	a.id_trans,
							b.nourut
						FROM(
							SELECT	id_trans,
								MAX(id) AS max_id
							FROM d_transaksi_status b
							GROUP BY id_trans
						) a
						LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
					) b ON(a.id=b.id_trans)
					WHERE b.nourut>=0 and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."' and a.kdsoutput='".$kdsoutput."' and a.kdkmpnen='".$kdkmpnen."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen,a.kdskmpnen";
		
		}
		elseif($status=='4'){ //sd DRPP
		
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						a.kdkmpnen,
						a.kdskmpnen,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN(
						SELECT	a.id_trans,
							b.nourut
						FROM(
							SELECT	id_trans,
								MAX(id) AS max_id
							FROM d_transaksi_status b
							GROUP BY id_trans
						) a
						LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
					) b ON(a.id=b.id_trans)
					WHERE b.nourut>=3 and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."' and a.kdsoutput='".$kdsoutput."' and a.kdkmpnen='".$kdkmpnen."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen,a.kdskmpnen";
		
		}
		elseif($status=='5'){ //sd SPP
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						a.kdkmpnen,
						a.kdskmpnen,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN(
						SELECT	a.id_trans,
							b.nourut
						FROM(
							SELECT	id_trans,
								MAX(id) AS max_id
							FROM d_transaksi_status b
							GROUP BY id_trans
						) a
						LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
					) b ON(a.id=b.id_trans)
					WHERE b.nourut>=5 and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."' and a.kdsoutput='".$kdsoutput."' and a.kdkmpnen='".$kdkmpnen."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen,a.kdskmpnen";
		
		}
		elseif($status=='6'){ //sd SPM
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						a.kdkmpnen,
						a.kdskmpnen,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN d_drpp b ON(a.nodrpp=b.nodrpp)
					LEFT OUTER JOIN d_spp c ON(b.nospp=c.nospp)
					WHERE c.nospm IS NOT NULL and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."' and a.kdsoutput='".$kdsoutput."' and a.kdkmpnen='".$kdkmpnen."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen,a.kdskmpnen";
			
		}
		elseif($status=='7'){ //sd SP2D
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						a.kdkmpnen,
						a.kdskmpnen,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN d_drpp b ON(a.nodrpp=b.nodrpp)
					LEFT OUTER JOIN d_spp c ON(b.nospp=c.nospp)
					WHERE c.nosp2d IS NOT NULL and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."' and a.kdsoutput='".$kdsoutput."' and a.kdkmpnen='".$kdkmpnen."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen,a.kdskmpnen";
		
		}
		
		$rows = DB::select("
			SELECT	CONCAT(a.kdprogram,'-',a.kdgiat,'-',a.kdoutput,'-',a.kdsoutput,'-',a.kdkmpnen,'-',a.kdskmpnen) AS kode,
				IFNULL(b.uraian,'Referensi tidak ada') AS uraian,
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
						SUM(paguakhir) AS jumlah
					FROM d_pagu
					WHERE lvl=7
					GROUP BY kddept,kdunit,kdsatker,thang,kdprogram,kdgiat,kdoutput,kdsoutput,kdkmpnen,kdskmpnen
					ORDER BY kddept,kdunit,kdsatker,thang,kdprogram,kdgiat,kdoutput,kdsoutput,kdkmpnen,kdskmpnen
				) a
				LEFT OUTER JOIN(
					".$where."
				) b ON(a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.kdprogram=b.kdprogram AND a.kdgiat=b.kdgiat and a.kdoutput=b.kdoutput and a.kdsoutput=b.kdsoutput and a.kdkmpnen=b.kdkmpnen and a.kdskmpnen=b.kdskmpnen)
			) a
			LEFT OUTER JOIN(
				SELECT kdprogram,kdgiat,kdoutput,kdlokasi,kdkabkota,kdsoutput,kdkmpnen,kdskmpnen,uraian FROM d_pagu WHERE lvl=6
			) b ON(a.kdprogram=b.kdprogram and a.kdgiat=b.kdgiat and a.kdoutput=b.kdoutput and a.kdsoutput=b.kdsoutput and a.kdkmpnen=b.kdkmpnen and a.kdskmpnen=b.kdskmpnen)
			WHERE a.kdsatker=? AND a.thang=? AND a.kdprogram=? and a.kdgiat=? and a.kdoutput=? and a.kdsoutput=? and a.kdkmpnen=?
		", [
			$kdsatker,
			$tahun,
			$kdprogram,
			$kdgiat,
			$kdoutput,
			$kdsoutput,
			$kdkmpnen
		]);
		
		$data='';
		foreach($rows as $row){
			$data.='<li style="list-style: none; margin-left: 100px;">
						<div class="row">
							<div class="col-lg-2">'.$row->kode.'</div>
							<div class="col-lg-4">
								<a href="javascript:;" id="'.$row->kode.'" class="breakdown">'.$row->uraian.'</a>
							</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->pagu).'</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->realisasi).'</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->pagu-$row->realisasi).'</div>
						</div>
					</li>
					<ul id="ul-'.$status.'-'.$row->kode.'"></ul>
					<hr>';
		}
		
		return $data;
	}
	
	public function akun($kddept, $kdunit, $kdsatker, $tahun, $status, $kdprogram, $kdgiat, $kdoutput, $kdsoutput, $kdkmpnen, $kdskmpnen)
	{
		$where = "";
		if($status=='1'){ //sd RKO
		
			$where = "/* cari realisasi */
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
					WHERE c.nourut>=4 and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."' and a.kdsoutput='".$kdsoutput."' and a.kdkmpnen='".$kdkmpnen."' and a.kdskmpnen='".$kdskmpnen."'
					GROUP BY b.kdsatker,b.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen,a.kdkmpnen,a.kdskmpnen,a.kdakun";
		
		}
		elseif($status=='2'){ //sd UMK
			
			$where = "/* cari realisasi */
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
					WHERE c.nourut>=11  and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."' and a.kdsoutput='".$kdsoutput."' and a.kdkmpnen='".$kdkmpnen."' and a.kdskmpnen='".$kdskmpnen."'
					GROUP BY b.kdsatker,b.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen,a.kdskmpnen,a.kdakun";
		
		}
		elseif($status=='3'){ //sd Kuitansi
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						a.kdkmpnen,
						a.kdskmpnen,
						a.kdmak as kdakun,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN(
						SELECT	a.id_trans,
							b.nourut
						FROM(
							SELECT	id_trans,
								MAX(id) AS max_id
							FROM d_transaksi_status b
							GROUP BY id_trans
						) a
						LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
					) b ON(a.id=b.id_trans)
					WHERE b.nourut>=0 and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."' and a.kdsoutput='".$kdsoutput."' and a.kdkmpnen='".$kdkmpnen."' and a.kdskmpnen='".$kdskmpnen."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen,a.kdskmpnen,a.kdmak";
		
		}
		elseif($status=='4'){ //sd DRPP
		
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						a.kdkmpnen,
						a.kdskmpnen,
						a.kdmak as kdakun,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN(
						SELECT	a.id_trans,
							b.nourut
						FROM(
							SELECT	id_trans,
								MAX(id) AS max_id
							FROM d_transaksi_status b
							GROUP BY id_trans
						) a
						LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
					) b ON(a.id=b.id_trans)
					WHERE b.nourut>=3 and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."' and a.kdsoutput='".$kdsoutput."' and a.kdkmpnen='".$kdkmpnen."' and a.kdskmpnen='".$kdskmpnen."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen,a.kdskmpnen,a.kdmak";
		
		}
		elseif($status=='5'){ //sd SPP
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						a.kdkmpnen,
						a.kdskmpnen,
						a.kdmak as kdakun,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN(
						SELECT	a.id_trans,
							b.nourut
						FROM(
							SELECT	id_trans,
								MAX(id) AS max_id
							FROM d_transaksi_status b
							GROUP BY id_trans
						) a
						LEFT OUTER JOIN d_transaksi_status b ON(a.max_id=b.id)
					) b ON(a.id=b.id_trans)
					WHERE b.nourut>=5 and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."' and a.kdsoutput='".$kdsoutput."' and a.kdkmpnen='".$kdkmpnen."' and a.kdskmpnen='".$kdskmpnen."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen,a.kdskmpnen,a.kdmak";
		
		}
		elseif($status=='6'){ //sd SPM
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						a.kdkmpnen,
						a.kdskmpnen,
						a.kdmak as kdakun,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN d_drpp b ON(a.nodrpp=b.nodrpp)
					LEFT OUTER JOIN d_spp c ON(b.nospp=c.nospp)
					WHERE c.nospm IS NOT NULL and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."' and a.kdsoutput='".$kdsoutput."' and a.kdkmpnen='".$kdkmpnen."' and a.kdskmpnen='".$kdskmpnen."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen,a.kdskmpnen,a.kdmak";
			
		}
		elseif($status=='7'){ //sd SP2D
			
			$where = "/* cari realisasi */
					SELECT	a.kdsatker,
						a.thang,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.kdsoutput,
						a.kdkmpnen,
						a.kdskmpnen,
						a.kdmak as kdakun,
						SUM(a.totnilmak) AS nilai
					FROM d_transaksi a
					LEFT OUTER JOIN d_drpp b ON(a.nodrpp=b.nodrpp)
					LEFT OUTER JOIN d_spp c ON(b.nospp=c.nospp)
					WHERE c.nosp2d IS NOT NULL and a.kdprogram='".$kdprogram."' and a.kdgiat='".$kdgiat."' and a.kdoutput='".$kdoutput."' and a.kdsoutput='".$kdsoutput."' and a.kdkmpnen='".$kdkmpnen."' and a.kdskmpnen='".$kdskmpnen."'
					GROUP BY a.kdsatker,a.thang,a.kdprogram,a.kdgiat,a.kdoutput,a.kdsoutput,a.kdkmpnen,a.kdskmpnen,a.kdmak";
		
		}
		
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
					".$where."
				) b ON(a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.kdprogram=b.kdprogram AND a.kdgiat=b.kdgiat and a.kdoutput=b.kdoutput and a.kdsoutput=b.kdsoutput and a.kdkmpnen=b.kdkmpnen and a.kdskmpnen=b.kdskmpnen and a.kdakun=b.kdakun)
			) a
			LEFT OUTER JOIN t_akun b ON(a.kdakun=b.kdakun)
			WHERE a.kdsatker=? AND a.thang=? AND a.kdprogram=? and a.kdgiat=? and a.kdoutput=? and a.kdsoutput=? and a.kdkmpnen=? and a.kdskmpnen=?
		", [
			$kdsatker,
			$tahun,
			$kdprogram,
			$kdgiat,
			$kdoutput,
			$kdsoutput,
			$kdkmpnen,
			$kdskmpnen
		]);
		
		$data='';
		foreach($rows as $row){
			$data.='<li style="list-style: none; margin-left: 120px;">
						<div class="row">
							<div class="col-lg-2">'.$row->kode.'</div>
							<div class="col-lg-4">
								<a href="javascript:;" id="'.$row->kode.'">'.$row->uraian.'</a>
							</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->pagu).'</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->realisasi).'</div>
							<div class="col-lg-2" style="text-align:right;">'.number_format($row->pagu-$row->realisasi).'</div>
						</div>
					</li>
					<ul id="ul-'.$status.'-'.$row->kode.'"></ul>
					<hr>';
		}
		
		return $data;
	}

}
