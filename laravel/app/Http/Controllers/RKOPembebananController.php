<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RKOPembebananController extends Controller {

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
							FROM d_rko_pagu2
							GROUP BY id_rko
						) e ON(a.id=e.id_rko)
						LEFT OUTER JOIN t_alur_status f ON(a.kdalur=f.kdalur AND d.nourut=f.nourut)
						WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."' AND a.kdalur IN('02','03') AND d.nourut=3
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
				$aksi='<center style="width:50px;">
							<div class="dropdown pull-right" style="height:1.5vw !important;">
								<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-pencil"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="javascript:;" id="'.$row->id.'" title="Ubah data?" class="ubah">Ubah RKO</a></li>
								</ul>
							</div>
						</center>';
							
				$output['aaData'][] = array(
					$row->id,
					$row->id,
					$row->nmalur,
					$row->nmjenisgiat,
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
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function sisa_pagu($id_rko)
	{
		try{
			$rows = DB::select("
				SELECT	concat(a.kdprogram,'-',a.kdgiat,'-',a.kdoutput,'-',a.kdsoutput,'-',a.kdkmpnen,'-',a.kdskmpnen,'-',a.kdakun) as kdakun,
						b.pagu,
						b.realisasi,
						b.rko,
						b.sisa,
						a.nilai,
						b.sisa-a.nilai as sisa_ini
					FROM(
						SELECT	b.kdsatker,
							b.thang,
							a.kdprogram,
							a.kdgiat,
							a.kdoutput,
							a.kdsoutput,
							a.kdkmpnen,
							a.kdskmpnen,
							a.kdakun,
							SUM(nilai) AS nilai
						FROM d_rko_pagu2 a
						LEFT OUTER JOIN d_rko b ON(a.id_rko=b.id)
						WHERE a.id_rko=?
						GROUP BY b.kdsatker,
							b.thang,
							a.kdprogram,
							a.kdgiat,
							a.kdoutput,
							a.kdsoutput,
							a.kdkmpnen,
							a.kdskmpnen,
							a.kdakun
					) a
					LEFT OUTER JOIN(

						SELECT	a.kdsatker,
							a.thang,
							a.kdprogram,
							a.kdgiat,
							a.kdoutput,
							a.kdsoutput,
							a.kdkmpnen,
							a.kdskmpnen,
							a.kdakun,
							a.pagu,
							IFNULL(b.nilai,0) AS realisasi,
							IFNULL(c.nilai,0) AS rko,
							a.pagu-IFNULL(b.nilai,0)-IFNULL(c.nilai,0) AS sisa
						FROM(
							/* pagu akhir dipa */
							SELECT	kdsatker,
								thang,
								kdprogram,
								kdgiat,
								kdoutput,
								kdsoutput,
								kdkmpnen,
								kdskmpnen,
								kdakun,
								SUM(paguakhir) AS pagu
							FROM d_pagu
							WHERE lvl=7
							GROUP BY kdsatker,
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
							
							/* realisasi dari kuitansi */
							SELECT	kdsatker,
								thang,
								kdprogram,
								kdgiat,
								kdoutput,
								kdsoutput,
								kdkmpnen,
								kdskmpnen,
								kdmak,
								SUM(totnilmak) AS nilai
							FROM d_transaksi
							WHERE nodrpp IS NOT NULL AND nodrpp<>''
							GROUP BY kdsatker,
								thang,
								kdprogram,
								kdgiat,
								kdoutput,
								kdsoutput,
								kdkmpnen,
								kdskmpnen,
								kdmak
								
						) b ON(a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.kdprogram=b.kdprogram AND a.kdgiat=b.kdgiat AND a.kdoutput=b.kdoutput AND
							a.kdsoutput=b.kdsoutput AND a.kdkmpnen=b.kdkmpnen AND a.kdskmpnen=b.kdskmpnen AND a.kdakun=b.kdmak)
						LEFT OUTER JOIN(
							
							/* realisasi dari rko */
							SELECT	a.kdsatker,
								a.thang,
								b.kdprogram,
								b.kdgiat,
								b.kdoutput,
								b.kdsoutput,
								b.kdkmpnen,
								b.kdskmpnen,
								b.kdakun,
								SUM(b.nilai) AS nilai
							FROM d_rko_pagu2 b
							LEFT OUTER JOIN d_rko a ON(b.id_rko=a.id)
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
							) c ON(a.id=c.id_rko)
							LEFT OUTER JOIN(
								SELECT DISTINCT id_rko
								FROM d_transaksi
								WHERE nodrpp IS NULL OR nodrpp=''
							) d ON(a.id=d.id_rko)
							WHERE c.nourut>=4 AND d.id_rko IS NOT NULL AND d.id_rko<>''
							GROUP BY a.kdsatker,
								a.thang,
								b.kdprogram,
								b.kdgiat,
								b.kdoutput,
								b.kdsoutput,
								b.kdkmpnen,
								b.kdskmpnen,
								b.kdakun
							
						) c ON(a.kdsatker=c.kdsatker AND a.thang=c.thang AND a.kdprogram=c.kdprogram AND a.kdgiat=c.kdgiat AND a.kdoutput=c.kdoutput AND
							a.kdsoutput=c.kdsoutput AND a.kdkmpnen=c.kdkmpnen AND a.kdskmpnen=c.kdskmpnen AND a.kdakun=c.kdakun)
					) b ON(a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.kdprogram=b.kdprogram AND a.kdgiat=b.kdgiat AND a.kdoutput=b.kdoutput AND
						a.kdsoutput=b.kdsoutput AND a.kdkmpnen=b.kdkmpnen AND a.kdskmpnen=b.kdskmpnen AND a.kdakun=b.kdakun)
			",[
				$id_rko
			]);
			
			return $rows;
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
				
				$sisa_pagu = $this->sisa_pagu($id);
				
				if(count($sisa_pagu)>0){
				
					$data='<table class="table table-bordered">
							<thead>
								<tr>
									<th>No</th>
									<th>Akun</th>
									<th>Pagu</th>
									<th>Realisasi</th>
									<th>RKO</th>
									<th>Sisa Pagu</th>
									<th>RKO Ini</th>
									<th>Sisa Ini</th>
								</tr>
							</thead>
							<tbody>';
					
					$i=1;
					foreach($sisa_pagu as $row){
						$data .= '<tr>
										<td>'.$i++.'</td>
										<td>'.$row->kdakun.'</td>
										<td style="text-align:right;">'.number_format($row->pagu).'</td>
										<td style="text-align:right;">'.number_format($row->realisasi).'</td>
										<td style="text-align:right;">'.number_format($row->rko).'</td>
										<td style="text-align:right;">'.number_format($row->sisa).'</td>
										<td style="text-align:right;">'.number_format($row->nilai).'</td>
										<td style="text-align:right;">'.number_format($row->sisa_ini).'</td>
									</tr>';
					}
					
					$data .= '</tbody></table>';
					
					$rows[0]->sisa_pagu = $data;
				
					return response()->json($rows[0]);
					
				}
				else{
					return 'Data sisa pagu tidak ditemukan!';
				}
				
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
			$arr_tgrko = explode("-", $request->input('tgrko'));
			$tgrko = $arr_tgrko[2].'-'.$arr_tgrko[1].'-'.$arr_tgrko[0];
			
			$arr_tanggal1 = explode("-", $request->input('tanggal1'));
			$tanggal1 = $arr_tanggal1[2].'-'.$arr_tanggal1[1].'-'.$arr_tanggal1[0];
			
			$arr_tanggal2 = explode("-", $request->input('tanggal2'));
			$tanggal2 = $arr_tanggal2[2].'-'.$arr_tanggal2[1].'-'.$arr_tanggal2[0];
			
			$now = new \DateTime();
			$created_at = $now->format('Y-m-d H:i:s');
			
			$sisa_pagu = $this->sisa_pagu($request->input('inp-id'));
			
			if(count($sisa_pagu)>0){
				
				$lanjut = true;
				foreach($sisa_pagu as $row){
					if($row->sisa_ini<0){
						$lanjut = false;
					}
				}
				
				if($lanjut){
					
					DB::beginTransaction();
					
					$insert = DB::insert("
						insert into d_rko_status(id_rko,nourut,terima,id_user,created_at,updated_at)
						values(?,?,?,?,now(),now())
					",[
						$request->input('inp-id'),
						4,
						'1',
						session('id_user')
					]);
					
					if($insert){
						
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
								id_jubar=?,
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
							$request->input('id_jubar'),
							session('id_user'),
							$request->input('inp-id')
						]);
						
						DB::commit();
						return 'success';
						
					}
					else{
						return 'Proses simpan status gagal!';
					}
					
				}
				else{
					return 'Terdapat pagu minus, pembebanan tidak dapat dilakukan!';
				}
				
			}
			else{
				return 'Data sisa pagu tidak ditemukan!';
			}
			
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
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
	
}