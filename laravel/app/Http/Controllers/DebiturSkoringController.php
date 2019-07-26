<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;
use mPDF;
use clsTinyButStrong;

class DebiturSkoringController extends Controller {

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
	
	private function query_skoring($status,$param1,$param2)
	{
		
	}
	
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
			$aColumns = array('nik','nama','nmhunian','alamat','jmltanggung','jmlkjp','jmltinggal','nilai','status','nmstatus','warna');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "nik";
			/* DB table to use */
			$sTable = "SELECT	a.nik,
								b.nama,
								e.nmhunian,
								e.alamat,
								b.jmltanggung,
								b.jmlkjp,
								b.jmltinggal,
								a.nilai,
								f.status,
								f.nmstatus,
								f.warna
					FROM d_debitur_skoring a
					LEFT OUTER JOIN d_debitur b ON(a.nik=b.nik)
					LEFT OUTER JOIN d_debitur_hunian c ON(b.nik=c.nik)
					LEFT OUTER JOIN d_hunian_dtl d ON(c.id_hunian_dtl=d.id)
					LEFT OUTER JOIN d_hunian e ON(d.id_hunian=e.id)
					LEFT OUTER JOIN t_status_skoring f ON(a.nilai BETWEEN f.range1 AND f.range2)
					ORDER BY a.nilai DESC";
			
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
							<div class="dropdown pull-right" style="height:1.5vw !important;">
								<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-check"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="javascript:;" id="'.$row->nik.'" title="Hapus data skoring?" class="hapus">Hapus Data Skoring</a></li>
								</ul>
							</div>
						</center>';
				
				$output['aaData'][] = array(
					$row->nik,
					$row->nama,
					$row->nmhunian,
					$row->alamat,
					$row->jmltanggung,
					$row->jmlkjp,
					$row->jmltinggal,
					'<div style="text-align:right;">'.$row->nilai.'%</div>',
					'<div style="background-color: '.$row->warna.';color: #FFF;padding:3px;">'.$row->nmstatus.'</div>',
					$aksi
				);
			}
			
			return response()->json($output);
		}
		catch(\Exception $e){
			return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function pilih($param1,$param2)
	{
		try{
			$arr_tanggal1 = explode("-", $param1);
			$tgpemohon1 = $arr_tanggal1[2].'-'.$arr_tanggal1[1].'-'.$arr_tanggal1[0];
			
			$arr_tanggal1 = explode("-", $param2);
			$tgpemohon2 = $arr_tanggal1[2].'-'.$arr_tanggal1[1].'-'.$arr_tanggal1[0];
		
			$rows = DB::select("
				select	ifnull(a.jml,0) as totdata,
							ifnull(b.jml,0) as totvalid,
							ifnull(c.jml,0) as totskoring
				from(
					select	count(nik) as jml
					from d_debitur
					where tgpemohon between ? and ?
				) a,
				(
					select	count(nik) as jml
					from d_debitur
					where tgpemohon between ? and ? and status='2'
				) b,
				(
					select	count(a.nik) as jml
					from d_debitur_skoring a
					left outer join d_debitur b on(a.nik=b.nik)
					where b.tgpemohon between ? and ?
				) c
			",[
				$tgpemohon1,
				$tgpemohon2,
				$tgpemohon1,
				$tgpemohon2,
				$tgpemohon1,
				$tgpemohon2
			]);
			
			if(count($rows)>0){
				return response()->json($rows[0]);
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
	
	public function proses($status,$param1,$param2)
	{
		try{
			if($status!=='' && $param1!=='' && $param2!==''){
				
				$arr_tanggal1 = explode("-", $param1);
				$tgpemohon1 = $arr_tanggal1[2].'-'.$arr_tanggal1[1].'-'.$arr_tanggal1[0];
				
				$arr_tanggal1 = explode("-", $param2);
				$tgpemohon2 = $arr_tanggal1[2].'-'.$arr_tanggal1[1].'-'.$arr_tanggal1[0];
				
				$rows = DB::select("
					select	a.*,
								b.status,
								b.nmstatus,
								b.warna
					from(
						select	a.nik,
									a.nama,
									a.jmltanggung,
									a.jmltinggal,
									a.jmlkjp,
									a.lokasi1,
									a.lokasi2,
									round((
									((a.nilai1/b.max1)*(b.bobot1))+
									((a.nilai2/b.max2)*(b.bobot2))+
									((a.nilai3/b.max3)*(b.bobot3))
									),0) as skoring
						from(
							SELECT	a.nik,
										a.nama,
										a.jmltanggung,
										IFNULL(b.nilai,5) AS nilai1,
										a.jmltinggal,
										IFNULL(c.nilai,0) AS nilai2,
										concat(d.kdkabkota,'.',d.kdkec,'.',d.kdkel) as lokasi1,
										concat(e.kdkabkota,'.',e.kdkec,'.',e.kdkel) as lokasi2,
										IF(d.kdprop=e.kdprop AND d.kdkabkota=e.kdkabkota AND d.kdkec=e.kdkec AND d.kdkel=e.kdkel,
											5, IF(d.kdprop=e.kdprop AND d.kdkabkota=e.kdkabkota AND d.kdkec=e.kdkec,
												3, IF(d.kdprop=e.kdprop AND d.kdkabkota=e.kdkabkota,
													2,
													0
												)
											)
										) AS nilai3,
										a.jmlkjp
							FROM d_debitur a
							LEFT OUTER JOIN t_skoring_tanggungan b ON(a.jmltanggung=b.jmltanggung)
							LEFT OUTER JOIN t_skoring_lama_tinggal c ON(a.jmltinggal BETWEEN c.tahun1 AND c.tahun2)
							LEFT OUTER JOIN d_debitur_alamat d ON(d.kdalamat='1' AND a.nik=d.nik)
							LEFT OUTER JOIN d_debitur_dukcapil e ON(a.nik=e.nik)
							WHERE a.status=? AND a.tgpemohon BETWEEN ? AND ?
						) a,
						(
							select	a.*,
										b.*,
										c.*
							from(
								select	max as max1,
											bobot as bobot1
								from t_skoring
								where kdskoring='1'
							) a,
							(
								select	max as max2,
											bobot as bobot2
								from t_skoring
								where kdskoring='2'
							) b,
							(
								select	max as max3,
											bobot as bobot3
								from t_skoring
								where kdskoring='3'
							) c
						) b
					) a
					left outer join t_status_skoring b on(a.skoring between b.range1 and b.range2)
					order by a.skoring desc
				",[
					$status,
					$tgpemohon1,
					$tgpemohon2
				]);
				
				$data = '<table class="table table-hover">
							<thead>
								<tr>
									<th>No</th>
									<th>NIK</th>
									<th>Nama</th>
									<th>Keluarga</th>
									<th>Lama Tinggal</th>
									<th>Lokasi</th>
									<th>KJP</th>
									<th>Skoring</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>';
				
				if(count($rows)>0){
					
					$i = 1;
					foreach($rows as $row){
						
						$data .= '<tr>
									<td>'.$i++.'</td>
									<td>'.$row->nik.'</td>
									<td>'.$row->nama.'</td>
									<td style="text-align:right;">'.$row->jmltanggung.'</td>
									<td style="text-align:right;">'.$row->jmltinggal.'</td>
									<td>
										<ul>
											<li>KTP '.$row->lokasi1.'</li>
											<li>Dukcapil '.$row->lokasi2.'</li>
										</ul>
									</td>
									<td style="text-align:right;">'.$row->jmlkjp.'</td>
									<td style="text-align:right;">'.$row->skoring.'%</td>
									<td>
										<div style="background-color: '.$row->warna.';color: #FFF;padding:3px;">'.$row->nmstatus.'</div>
									</td>
									<td>
										<center>
											<input type="checkbox" id="skoring-'.$row->nik.'" name="skoring['.$row->nik.']" value="1">
										</center>
									</td>
								  </tr>';
						
					}
				
				}
				
				$data .= '</tbody></table>';
				
				return $data;
				
			}
			else{
				return 'Kolom parameter tidak dapat dikosongkan!';
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
			
			$delete = DB::delete("
				delete from d_debitur_skoring where nik=?
			",[
				$request->input('id')
			]);
			
			if($delete){
				
				$update = DB::update("
					update d_debitur set status=2 where nik=?
				",[
					$request->input('id')
				]);
				
				if($update){
					DB::commit();
					return 'success';
				}	
				else{
					return 'Status debitur gagal diupdate!';
				}
				
			}
			else{
				return 'Proses hapus gagal!';
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function simpan(Request $request)
	{
		try{
			if($request->input('status')!=='' && $request->input('tgpemohon1')!=='' && $request->input('tgpemohon2')!==''){
				
				if(count($request->input('skoring'))>0){
					
					$arr_skoring = array_keys($request->input('skoring'));
					$status = $request->input('status');
					$param1 = $request->input('tgpemohon1');
					$param2 = $request->input('tgpemohon2');
					
					$arr_tanggal1 = explode("-", $param1);
					$tgpemohon1 = $arr_tanggal1[2].'-'.$arr_tanggal1[1].'-'.$arr_tanggal1[0];
					
					$arr_tanggal1 = explode("-", $param2);
					$tgpemohon2 = $arr_tanggal1[2].'-'.$arr_tanggal1[1].'-'.$arr_tanggal1[0];
					
					$where = "'".implode("','", $arr_skoring)."'";
					
					DB::beginTransaction();
					
					$delete = DB::delete("
						delete from d_debitur_skoring where nik in(".$where.")
					");
					
					$insert = DB::insert("
						insert into d_debitur_skoring(nik,nilai,created_at,updated_at)
						select	a.nik,
								round((
								((a.nilai1/b.max1)*(b.bobot1))+
								((a.nilai2/b.max2)*(b.bobot2))+
								((a.nilai3/b.max3)*(b.bobot3))
								),0) as skoring,
								now() as created_at,
								now() as updated_at
						from(
							SELECT	a.nik,
										a.nama,
										a.jmltanggung,
										IFNULL(b.nilai,5) AS nilai1,
										a.jmltinggal,
										IFNULL(c.nilai,0) AS nilai2,
										concat(d.kdkabkota,'.',d.kdkec,'.',d.kdkel) as lokasi1,
										concat(e.kdkabkota,'.',e.kdkec,'.',e.kdkel) as lokasi2,
										IF(d.kdprop=e.kdprop AND d.kdkabkota=e.kdkabkota AND d.kdkec=e.kdkec AND d.kdkel=e.kdkel,
											5, IF(d.kdprop=e.kdprop AND d.kdkabkota=e.kdkabkota AND d.kdkec=e.kdkec,
												3, IF(d.kdprop=e.kdprop AND d.kdkabkota=e.kdkabkota,
													2,
													0
												)
											)
										) AS nilai3,
										a.jmlkjp
							FROM d_debitur a
							LEFT OUTER JOIN t_skoring_tanggungan b ON(a.jmltanggung=b.jmltanggung)
							LEFT OUTER JOIN t_skoring_lama_tinggal c ON(a.jmltinggal BETWEEN c.tahun1 AND c.tahun2)
							LEFT OUTER JOIN d_debitur_alamat d ON(d.kdalamat='1' AND a.nik=d.nik)
							LEFT OUTER JOIN d_debitur_dukcapil e ON(a.nik=e.nik)
							WHERE a.status=".$status." AND a.tgpemohon BETWEEN '".$tgpemohon1."' AND '".$tgpemohon2."'
						) a,
						(
							select	a.*,
										b.*,
										c.*
							from(
								select	max as max1,
											bobot as bobot1
								from t_skoring
								where kdskoring='1'
							) a,
							(
								select	max as max2,
											bobot as bobot2
								from t_skoring
								where kdskoring='2'
							) b,
							(
								select	max as max3,
											bobot as bobot3
								from t_skoring
								where kdskoring='3'
							) c
						) b
						where a.nik in(".$where.")
					");
					
					if($insert){
						
						$update = DB::update("
							update d_debitur
							set status=4
							where nik in(".$where.")
						");
						
						if($update){
							DB::commit();
							return 'success';
						}
						else{
							return 'Proses update status debitur gagal!';
						}
					
					}
					else{
						return 'Proses simpan skoring debitur gagal!';
					}
					
				}
				else{
					return 'Anda belum memilih debitur yang akan disimpan!';
				}
				
			}
			else{
				return 'Kolom tidak dapat dikosongkan!';
			}
		}
		catch(\Exception $e){
			return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function download($param)
	{
		try{
			$rows = DB::select("
				select	kdpetugas,
						tahun,
						nourut
				from d_form
				where id=?
			",[
				$param
			]);
		
			if(count($rows)>0){
				
				$TBS = new clsTinyButStrong();
				$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
				
				$_value = (object)array(
					'kdpetugas'=>$rows[0]->kdpetugas,
					'id_form'=>str_pad($rows[0]->nourut, 5, '0', STR_PAD_LEFT),
				);
				
				$value[] = $_value;
				
				$TBS->LoadTemplate('reports/template_form_debitur.docx');
				
				$TBS->MergeBlock('v', $value);
				
				//download file
				$TBS->Show(OPENTBS_DOWNLOAD,'form_debitur_'.$rows[0]->kdpetugas.$rows[0]->tahun.str_pad($rows[0]->nourut, 5, '0', STR_PAD_LEFT).'.docx');
				
			}
			else{
				return 'Data tidak ditemukan!';
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
}