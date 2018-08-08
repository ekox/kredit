<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProgresController extends Controller {

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
			$aColumns = array('id', 'kode', 'nmoutput', 'target', 'periode', 'rencana', 'progres', 'capaian_fisik', 'status', 'nmstatus');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			
			$sTable = "	SELECT	a.id as id,
							CONCAT(b.kdprogram, '.', b.kdgiat, '.', b.kdoutput) as kode,
							c.nmoutput as nmoutput,
							CONCAT (b.volume, ' ', b.satuan) as target,
							e.bulan as periode,
							f.rencana,
							a.progres as progres,
							a.capaian_fisik as capaian_fisik,
							a.status as status,
							d.nmstatus as nmstatus
						FROM d_progres a
						INNER JOIN d_target b on a.id_target=b.id
						INNER JOIN t_output c on b.kdgiat=c.kdgiat and b.kdoutput=c.kdoutput
						INNER JOIN t_status_target d on a.status=d.status
						INNER JOIN t_periode e on a.periode=e.periode
						LEFT OUTER JOIN d_target_detil f on a.id_target=f.id_target and a.periode=f.periode
						WHERE b.kddept='".session('kddept')."' AND b.kdunit='".session('kdunit')."' AND b.kdsatker='".session('kdsatker')."' AND b.thang='".session('tahun')."' AND b.kdppk='".session('kdppk')."'
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
				
				if(session('kdlevel')=='01'){
					if($row->status=='1'){
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
									<li><a href="javascript:;" id="'.$row->id.'" title="Hapus Progres?" class="hapus">Hapus Progres</a></li>
								</ul>
							</div>';
						$ubah='<div class="dropdown pull-right" style="height:1.5vw !important;">
								<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-pencil"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="javascript:;" id="'.$row->id.'" title="Ubah Progres?" class="ubah">Ubah Progres</a></li>
									<!--<li><a href="javascript:;" id="'.$row->id.'" title="Hapus rekomendasi?" class="hapus">Hapus Rekomendasi</a></li>-->
								</ul>
							</div>';
						$aksi='<center style="width:120px;">
							'.$hapus.'
							'.$ubah.'
						</center>';
					}
				}
				
				else if((session('kdlevel')=='06' && $row->status!=='0')){
						$setuju='';
						$tolak='';
					}else{
						$setuju='<div class="dropdown pull-right" style="height:1.5vw !important;">
								<button class="btn btn-success btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-check"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<!--<li><a href="javascript:;" id="'.$row->id.'" title="Ubah data?" class="ubah">Ubah Rapat</a></li>-->
									<li><a href="javascript:;" id="'.$row->id.'" title="Setujui progres?" class="setuju">Setujui Progres</a></li>
								</ul>
							</div>';
						$tolak='<div class="dropdown pull-right" style="height:1.5vw !important;">
								<button class="btn btn-danger btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-remove"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<!--<li><a href="javascript:;" id="'.$row->id.'" title="Ubah data?" class="ubah">Ubah Rapat</a></li>-->
									<li><a href="javascript:;" id="'.$row->id.'" title="Tolak progres?" class="tolak">Tolak Progres</a></li>
								</ul>
							</div>';
						$aksi='<center style="width:120px;">
							'.$tolak.'
							'.$setuju.'
						</center>';
					}
				
				$output['aaData'][] = array(
					$row->id,
					$row->kode,
					$row->nmoutput,
					$row->target,
					$row->periode,
					$row->rencana,
					$row->progres,
					$row->capaian_fisik,
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
				SELECT	a.id,
					a.id_target,
					b.volume,
					b.satuan,
					b.ket,
					a.periode,
					a.progres,
					a.capaian_fisik
				FROM d_progres a
				INNER JOIN d_target b on a.id_target=b.id
				INNER JOIN t_output c on b.kdgiat=c.kdgiat and b.kdoutput=c.kdoutput
				WHERE a.id=?
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
					'id_target' => $request->input('id_target'),
					'periode' => $request->input('periode'),
					'progres' => $request->input('progres'),
					'capaian_fisik' => $request->input('capaian_fisik'),
					'status' => '0',
					'id_user' => session('id_user'),
					'created_at' => $created_at,
					'updated_at' => $created_at
				);
					
				$id_evaluasi = DB::table('d_progres')->insertGetId(
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
						update d_progres
						set progres=?,
							capaian_fisik=?,
							status='0',
							id_user=?,
							updated_at=now()
						where id=?
					",[
						$request->input('progres'),
						$request->input('capaian_fisik'),
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
				
				$update = DB::update("
					update d_progres
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
			else{
				return 'Data tidak dapat diubah!';
			}
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function tolak(Request $request)
	{
		try{
			DB::beginTransaction();
			
			if($this->cek_status($request->input('id'))){
				
				$update = DB::update("
					update d_progres
					set status='2',
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
					delete from d_progres where id=?
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
		
	public function cek_status($id_evaluasi)
	{
		try{
			$rows = DB::select("
				SELECT	status
				FROM d_progres a
				WHERE a.id=?
			",[
				$id_evaluasi
			]);
			
			if($rows[0]->status!==1){
				
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
	
}