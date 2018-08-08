<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TargetController extends Controller {

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
			$aColumns = array('id', 'kode', 'volume', 'satuan', 'ket', 'status', 'nmstatus');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			
			$sTable = "	SELECT	id,
							CONCAT(kdprogram, '.', kdgiat, '.', kdoutput) as kode,
							volume,
							satuan,
							ket,
							a.status as status,
							nmstatus
						FROM d_target a
						INNER JOIN t_status_target b on a.status=b.status
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
									<li><a href="javascript:;" id="'.$row->id.'" title="Hapus Target?" class="hapus">Hapus Target</a></li>
								</ul>
							</div>';
						$ubah='<div class="dropdown pull-right" style="height:1.5vw !important;">
								<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-pencil"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="javascript:;" id="'.$row->id.'" title="Ubah Target?" class="ubah">Ubah Target</a></li>
									<!--<li><a href="javascript:;" id="'.$row->id.'" title="Hapus rekomendasi?" class="hapus">Hapus Rekomendasi</a></li>-->
								</ul>
							</div>';
						$aksi='<center style="width:120px;">
							'.$hapus.'
							'.$ubah.'
						</center>';
					}
				}			
				elseif((session('kdlevel')=='06' && $row->status=='1')){
					$ubah='<div class="dropdown pull-right" style="height:1.5vw !important;">
								<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-pencil"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="javascript:;" id="'.$row->id.'" title="Ubah Target?" class="ubah">Ubah Target</a></li>
								</ul>
							</div>';
						$aksi='<center style="width:120px;">
							'.$ubah.'
						</center>';
				}
				elseif((session('kdlevel')=='06' && $row->status=='0')){
					$setuju='<div class="dropdown pull-right" style="height:1.5vw !important;">
							<button class="btn btn-success btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
								<i class="fa fa-check"></i>
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu dropdown-menu-right">
								<!--<li><a href="javascript:;" id="'.$row->id.'" title="Ubah data?" class="ubah">Ubah Rapat</a></li>-->
								<li><a href="javascript:;" id="'.$row->id.'" title="Setujui target?" class="setuju">Setujui Target</a></li>
							</ul>
						</div>';
					$tolak='<div class="dropdown pull-right" style="height:1.5vw !important;">
							<button class="btn btn-danger btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
								<i class="fa fa-remove"></i>
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu dropdown-menu-right">
								<!--<li><a href="javascript:;" id="'.$row->id.'" title="Ubah data?" class="ubah">Ubah Rapat</a></li>-->
								<li><a href="javascript:;" id="'.$row->id.'" title="Tolak target?" class="tolak">Tolak Target</a></li>
							</ul>
						</div>';
					$aksi='<center style="width:120px;">
						'.$tolak.'
						'.$setuju.'
					</center>';
				}
				
				$output['aaData'][] = array(
					$row->id,
					$row->id,
					$row->kode,
					$row->volume,
					$row->satuan,
					$row->ket,
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
					kdprogram,
					kdgiat,
					kdoutput,
					volume,
					satuan,
					ket
				FROM d_target
				WHERE id=?
			",[
				$id
			]);
			
			$rows_detil = DB::select("
				SELECT	a.*,
						ifnull(b.rencana,0) as rencana,
						ifnull(b.rencana1,0) as rencana1
				FROM t_periode a
				LEFT OUTER JOIN(
					SELECT 	periode,
							rencana,
							rencana1
					FROM d_target_detil
					WHERE id_target=?
				) b ON(a.periode=b.periode)
				ORDER BY a.periode
			",[
				$id
			]);
			
			$data = '';
			foreach($rows_detil as $row){
				$data .= '<tr>
							<td>'.$row->bulan.'</td>
							<td><input type="text" name="rencana['.$row->periode.']" class="form-control val_num uang" value="'.number_format($row->rencana).'" style="text-align:right;"></td>
							<td><input type="text" name="rencana1['.$row->periode.']" class="form-control val_num uang" value="'.number_format($row->rencana1).'" style="text-align:right;"></td>
						 </tr>';
			}
			
			if(count($rows)==1){
				$rows[0]->detil = $data;
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
				
				$now = new \DateTime();
				$created_at = $now->format('Y-m-d H:i:s');
				
				$input = array(
					'kddept' => session('kddept'),
					'kdunit' => session('kdunit'),
					'kdsatker' => session('kdsatker'),
					'kddekon' => session('kddekon'),
					'thang' => session('tahun'),
					'kdppk' => session('kdppk'),
					'kdprogram' => $request->input('program'),
					'kdgiat' => $request->input('kegiatan'),
					'kdoutput' => $request->input('output'),
					'satuan' => $request->input('satuan'),
					'volume' => preg_replace("/[^0-9 \d]/i", "", $request->input('volume')),
					'ket' => $request->input('ket'),
					'status' => '0',
					'id_user' => session('id_user'),
					'created_at' => $created_at,
					'updated_at' => $created_at
				);
					
				$id_target = DB::table('d_target')->insertGetId(
					$input
				);
				
				if($id_target){
					
					$arr_rencana = $request->input('rencana');
					$arr_rencana1 = $request->input('rencana1');
					$arr_detil = array_keys($arr_rencana);
					
					for($i=0;$i<count($arr_detil);$i++){
						$arr_insert_detil[] = "(".$id_target.",'".$arr_detil[$i]."',".preg_replace("/[^0-9 \d]/i", "", $arr_rencana[$arr_detil[$i]]).",".preg_replace("/[^0-9 \d]/i", "", $arr_rencana1[$arr_detil[$i]]).",".session('id_user').")";
					}
					
					$insert = DB::insert("
						insert into d_target_detil(id_target,periode,rencana,rencana1,id_user)
						values".implode(",", $arr_insert_detil)."
					");
					
					if($insert){
						DB::commit();
						return 'success';
					}
					else{
						return 'Simpan detil target gagal!';
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
						update d_target
						set volume=?,
							ket=?,
							status='0',
							id_user=?,
							updated_at=now()
						where id=?
					",[
						preg_replace("/[^0-9 \d]/i", "", $request->input('volume')),
						$request->input('ket'),
						session('id_user'),
						$request->input('inp-id')
					]);
					
					$arr_rencana = $request->input('rencana');
					$arr_rencana1 = $request->input('rencana1');
					$arr_detil = array_keys($arr_rencana);
					
					for($i=0;$i<count($arr_detil);$i++){
						$arr_insert_detil[] = "(".$request->input('inp-id').",'".$arr_detil[$i]."',".preg_replace("/[^0-9 \d]/i", "", $arr_rencana[$arr_detil[$i]]).",".preg_replace("/[^0-9 \d]/i", "", $arr_rencana1[$arr_detil[$i]]).",".session('id_user').")";
					}
					
					$delete = DB::delete("delete from d_target_detil where id_target=?",[$request->input('inp-id')]);
					
					$insert = DB::insert("
						insert into d_target_detil(id_target,periode,rencana,rencana1,id_user)
						values".implode(",", $arr_insert_detil)."
					");
					
					if($insert){
						DB::commit();
						return 'success';
					}
					else{
						return 'Simpan detil target gagal!';
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
					update d_target
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
					update d_target
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
					delete from d_target where id=?
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
				FROM d_target a
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
	
	public function detil($id_target)
	{
		try{
			$rows = DB::select("
				SELECT	a.*,
						ifnull(b.rencana,0) as rencana,
						ifnull(b.rencana1,0) as rencana1
				FROM t_periode a
				LEFT OUTER JOIN(
					SELECT 	periode,
							rencana,
							rencana1
					FROM d_target_detil
					WHERE id_target=?
				) b ON(a.periode=b.periode)
				ORDER BY a.periode
			",[
				$id_target
			]);
			
			$data = '<table class="table table-bordered">
						<thead>
							<tr>
								<th>Periode</th>
								<th>Fisik</th>
								<th>Nilai</th>
							</tr>
						</thead>
						<tbody>';
			
			foreach($rows as $row){
				$data .= '<tr>
							<td>'.$row->bulan.'</td>
							<td style="text-align:right;">'.number_format($row->rencana).'</td>
							<td style="text-align:right;">'.number_format($row->rencana1).'</td>
						 </tr>';
			}
			
			$data .= '</tbody></table>';
			
			return $data;
			
		}
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function bulanan()
	{
		try{
			$rows = DB::select("
				SELECT	*
				FROM t_periode
				ORDER BY periode
			");
			
			$data = '';
			foreach($rows as $row){
				$data .= '<tr>
							<td>'.$row->bulan.'</td>
							<td><input type="text" name="rencana['.$row->periode.']" class="form-control val_num uang" value="0" style="text-align:right;"></td>
							<td><input type="text" name="rencana1['.$row->periode.']" class="form-control val_num uang" value="0" style="text-align:right;"></td>
						 </tr>';
			}
			
			return $data;
			
		}
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
}