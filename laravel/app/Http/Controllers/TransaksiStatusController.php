<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransaksiStatusController extends Controller {

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
			$aColumns = array('id','id_rko','nmalur','tgkuitansi','uraiben','untuk','totnilmak','created_at','nourut_setuju','nourut_tolak','status');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			
			$sTable = "	SELECT	a.*
						FROM(
							SELECT	a.id,
									a.id_rko,
									b.nmalur,
									DATE_FORMAT(a.tgkuitansi,'%d-%m-%Y') AS tgkuitansi,
									a.uraiben,
									a.untuk,
									a.totnilmak,
									d.nourut,
									d.created_at,
									h.nourut_setuju,
									h.nourut_tolak,
									concat(h.status,' -',if(d.terima='0','Berkas Dikembalikan','')) as status,
									if(h.is_ppk='1',
										if(a.kdppk='".session('kdppk')."',
											'1',
											'0'
										),
										'1'
									) as akses
							FROM d_transaksi a
							LEFT OUTER JOIN t_alur b ON(a.kdalur=b.kdalur)
							LEFT OUTER JOIN(
								SELECT	a.id_trans,
										b.nourut,
										b.terima,
										DATE_FORMAT(b.created_at,'%d-%m-%Y %H:%i:%s') as created_at
								FROM(
									SELECT	id_trans,
											MAX(id) AS id
									FROM d_transaksi_status
									GROUP BY id_trans
								) a
								LEFT OUTER JOIN d_transaksi_status b ON(a.id_trans=b.id_trans AND a.id=b.id)
							) d ON(a.id=d.id_trans)
							LEFT OUTER JOIN t_alur_status h ON(a.kdalur=h.kdalur AND d.nourut=h.nourut)
							WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND h.kdlevel='".session('kdlevel')."'
							ORDER BY a.id DESC
						) a
						WHERE a.akses='1'";
			
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
				$cek = '<a href="javascript:;" id="'.$row->id.'" title="Proses berkas ini?" class="btn btn-xs btn-danger cek"><i class="fa fa-check"></i></a>';
				
				$aksi='<center>
							'.$cek.'
							<a href="" target="_blank" id="'.$row->id.'" title="Cetak routing slip?" class="btn btn-xs btn-success"><i class="fa fa-print"></i></a>
						</center>';
							
				$output['aaData'][] = array(
					$row->id,
					$row->id_rko,
					$row->nmalur,
					//$row->nokuitansi,
					$row->tgkuitansi,
					$row->uraiben,
					$row->untuk,
					'<div style="text-align:right;">'.number_format($row->totnilmak).'</div>',
					$row->created_at,
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
	
	public function monitoring(Request $request)
	{
		try{
			$aColumns = array('id','id_rko','nmalur','tgkuitansi','uraiben','untuk','totnilmak','created_at','nmlevel','status');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "	SELECT	a.id,
									a.id_rko,
									b.nmalur,
									DATE_FORMAT(a.tgkuitansi,'%d-%m-%Y') AS tgkuitansi,
									a.uraiben,
									a.untuk,
									a.totnilmak,
									d.nourut,
									d.created_at,
									h.nourut_setuju,
									h.nourut_tolak,
									i.nmlevel,
									concat(h.status,' -',if(d.terima='0','Berkas Dikembalikan','')) as status
							FROM d_transaksi a
							LEFT OUTER JOIN t_alur b ON(a.kdalur=b.kdalur)
							LEFT OUTER JOIN(
								SELECT	a.id_trans,
										b.nourut,
										b.terima,
										DATE_FORMAT(b.created_at,'%d-%m-%Y %H:%i:%s') as created_at
								FROM(
									SELECT	id_trans,
											MAX(id) AS id
									FROM d_transaksi_status
									GROUP BY id_trans
								) a
								LEFT OUTER JOIN d_transaksi_status b ON(a.id_trans=b.id_trans AND a.id=b.id)
							) d ON(a.id=d.id_trans)
							LEFT OUTER JOIN t_alur_status h ON(a.kdalur=h.kdalur AND d.nourut=h.nourut)
							LEFT OUTER JOIN t_level i ON(h.kdlevel=i.kdlevel)
							WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."'
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
				$aksi='<center>
							<a href="transaksi/status/routing-slip/'.$row->id.'" target="_blank" title="Cetak routing slip?" class="btn btn-xs btn-success"><i class="fa fa-print"></i></a>
						</center>';
							
				$output['aaData'][] = array(
					$row->id,
					$row->id_rko,
					$row->nmalur,
					$row->tgkuitansi,
					$row->uraiben,
					$row->untuk,
					'<div style="text-align:right;">'.number_format($row->totnilmak).'</div>',
					$row->created_at,
					$row->nmlevel,
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
	
	public function cek($id_trans)
	{
		try{
			$rows = DB::select("
				SELECT	a.id,
						date_format(a.tgkuitansi,'%d-%m-%Y') as tgkuitansi,
						CONCAT(a.kdprogram,'.',a.kdoutput,'.',a.kdsoutput,'.',a.kdkmpnen,'.',a.kdskmpnen,'.',a.kdmak) AS id_pagu,
						a.totnilmak,
						a.uraiben,
						a.untuk,
						a.nospby,
						ifnull(b.nmdok,'N/A') as nmdok
				FROM d_transaksi a
				LEFT OUTER JOIN(
					SELECT	a.id_trans,
							GROUP_CONCAT('<li><a target=\"_blank\" href=\"transaksi/kuitansi/dok/',a.nmfile,'/download\">',b.nmdok,'</li>' ORDER BY a.id SEPARATOR '') AS nmdok
					FROM d_transaksi_dok a
					LEFT OUTER JOIN t_dok b ON(a.id_dok=b.id)
					GROUP BY a.id_trans
				) b on(a.id=b.id_trans)
				where a.id=?
			",[
				$id_trans
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
	
	public function simpan(Request $request)
	{
		try{
			$tanggal = '';
			if($request->input('tgsurat')!=''){
				$arr_tanggal = explode("-", $request->input('tgsurat'));
				$tanggal = $arr_tanggal[2].'-'.$arr_tanggal[1].'-'.$arr_tanggal[0];
			}
			
			$rows = DB::select("
				SELECT	b.id_trans,
						b.nourut,
						d.nourut_setuju,
						d.nourut_tolak,
						d.is_rekam_setuju,
						d.is_rekam_tolak,
						ifnull(d.ur_setuju_tolak,'-') as ur_setuju_tolak,
						c.kdalur
				FROM(
					SELECT	MAX(id) AS id
					FROM d_transaksi_status
					WHERE id_trans=?
				) a
				LEFT OUTER JOIN d_transaksi_status b ON(a.id=b.id)
				LEFT OUTER JOIN d_transaksi c ON(b.id_trans=c.id)
				LEFT OUTER JOIN t_alur_status d ON(c.kdalur=d.kdalur AND b.nourut=d.nourut)
			",[
				$request->input('id_trans')
			]);
			
			if(count($rows)>0){
				
				if($request->input('status')=='0'){ //tolak
					
					if($rows[0]->nourut_tolak!=='x'){
						
						if($rows[0]->is_rekam_tolak!=='1'){
							
							$insert = DB::insert("
								insert into d_transaksi_status(id_trans,nourut,terima,id_user,created_at,updated_at,nosurat,tgsurat,ketsurat)
								values(?,?,?,?,now(),now(),?,?,?)
							",[
								$rows[0]->id_trans,
								$rows[0]->nourut_tolak,
								'0',
								session('id_user'),
								$request->input('nosurat'),
								$tanggal,
								$request->input('uraian')
							]);
							
							if($insert){
								DB::commit();
								return 'success';
							}
							else{
								return 'Proses simpan gagal!';
							}
							
						}
						else{
							return $rows[0]->ur_setuju_tolak;
						}
						
					}
					else{
						return 'Data tidak dapat ditolak! (Status Awal)';
					}
					
				}
				else{
					
					if($rows[0]->nourut_setuju!=='x'){
						
						if($rows[0]->is_rekam_setuju!=='1'){
							
							$insert = DB::insert("
								insert into d_transaksi_status(id_trans,nourut,terima,id_user,created_at,updated_at,nosurat,tgsurat,ketsurat)
								values(?,?,?,?,now(),now(),?,?,?)
							",[
								$rows[0]->id_trans,
								$rows[0]->nourut_setuju,
								'1',
								session('id_user'),
								$request->input('nosurat'),
								$tanggal,
								$request->input('uraian')
							]);
							
							if($insert){
								DB::commit();
								return 'success';
							}
							else{
								return 'Proses simpan gagal!';
							}
							
						}
						else{
							return $rows[0]->ur_setuju_tolak;
						}
						
					}
					else{
						return 'Data tidak dapat disetujui! (Status Akhir)';
					}
					
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
}