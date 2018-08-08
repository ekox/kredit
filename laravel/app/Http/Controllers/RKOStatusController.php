<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RKOStatusController extends Controller {

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
			if(session('kdlevel')=='03'){
				$akses_unit = "left(a.id_unit,8)=left('".session('id_unit')."',8)";;
			}
			else{
				$akses_unit = "a.id_unit='".session('id_unit')."'";;
			}
			
			$aColumns = array('id','nmalur','nmjenisgiat','urrko','tgrko','nilai','created_at','nourut','nourut_setuju','nourut_tolak','status');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "	SELECT	a.*
						FROM(
							SELECT	a.id,
									b.nmalur,
									c.nmjenisgiat,
									a.urrko,
									DATE_FORMAT(a.tgrko,'%d-%m-%Y') AS tgrko,
									IF(a.kdalur='01',IFNULL(e.jml,0),IFNULL(f.jml,0)) AS jml,
									IF(a.kdalur='01',IFNULL(e.nilai,0),IFNULL(f.nilai,0)) AS nilai,
									DATE_FORMAT(d.created_at,'%d-%m-%Y %H:%i:%s') as created_at,
									d.nourut,
									h.nourut_setuju,
									h.nourut_tolak,
									concat(h.status,' -',if(d.terima='0','Berkas Dikembalikan','')) as status,
									if(h.is_ppk='1',
										if(a.kdppk='".session('kdppk')."',
											'1',
											'0'
										),
										'1'
									) as akses,
									if(h.is_jubar='1',
										if(a.id_jubar='".session('id_user')."',
											'1',
											'0'
										),
										'1'
									) as akses1,
									if(h.is_unit='1',
										if(".$akses_unit.",
											'1',
											'0'
										),
										'1'
									) as akses2
							FROM d_rko a
							LEFT OUTER JOIN t_alur b ON(a.kdalur=b.kdalur)
							LEFT OUTER JOIN t_jenisgiat c ON(a.jenisgiat=c.jenisgiat)
							LEFT OUTER JOIN(
								SELECT	a.id_rko,
										b.nourut,
										b.terima,
										b.created_at
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
								FROM d_rko_pagu1
								GROUP BY id_rko
							) e ON(a.id=e.id_rko)
							LEFT OUTER JOIN(
								SELECT	id_rko,
										COUNT(*) AS jml,
										SUM(nilai) AS nilai
								FROM d_rko_pagu2
								GROUP BY id_rko
							) f ON(a.id=f.id_rko)
							LEFT OUTER JOIN t_alur_status h ON(a.kdalur=h.kdalur AND d.nourut=h.nourut)
							WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND h.kdlevel='".session('kdlevel')."'
							ORDER BY d.created_at DESC
						) a
						WHERE a.akses='1' AND a.akses1='1' AND a.akses2='1' ";
			
			
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
			else{
				$sOrder = " ORDER BY STR_TO_DATE(a.created_at,'%d-%m-%Y %H:%i:%s') DESC ";
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
							<div class="dropdown pull-right">
								<button class="btn btn-success btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-print"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="rko/status/surat/'.$row->id.'" target="_blank" title="Cetak data?">Cetak RKO</a></li>
									<li><a href="rko/cetak/'.$row->id.'" target="_blank" title="Cetak data?">Cetak Lampiran</a></li>
									<li><a href="rko/status/routing-slip/'.$row->id.'" target="_blank" title="Cetak routing slip?">Cetak Routing Slip</a></li>
								</ul>
							</div>
						</center>';
							
				$output['aaData'][] = array(
					$row->id,
					$row->nmalur,
					$row->nmjenisgiat,
					$row->urrko,
					$row->tgrko,
					'<div style="text-align:right;">'.number_format($row->nilai).'</div>',
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
	
	public function monitoring(Request $request, $id_unit, $bulan)
	{
		try{
			$where1 = "";
			if($id_unit!=='xxx'){
				$where1 = "and a.id_unit='".$id_unit."'";
			}
			
			$where2 = "";
			if($bulan!=='xxx'){
				$where2 = "and date_format(a.tgrko,'%m')='".$bulan."'";
			}
			
			$aColumns = array('id','nmunit','nmalur','nmjenisgiat','urrko','tgrko','nilai','jml1','nilai1','created_at','nmlevel','nmstatus');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "	SELECT	a.id,
								k.uraian as nmunit,
								b.nmalur,
								c.nmjenisgiat,
								a.urrko,
								DATE_FORMAT(a.tgrko,'%d-%m-%Y') AS tgrko,
								IF(a.kdalur='01',IFNULL(e.nilai,0),IFNULL(f.nilai,0)) AS nilai,
								IFNULL(j.jml1,0) as jml1,
								IFNULL(j.nilai1,0) as nilai1,
								DATE_FORMAT(d.created_at,'%d-%m-%Y %H:%i:%s') AS created_at,
								if(h.kdlevel='11',concat(h.nmlevel,' - ',i.nama),h.nmlevel) as nmlevel,
								concat(g.status,' -',if(d.terima='0','Berkas Dikembalikan','')) as nmstatus
							FROM d_rko a
							LEFT OUTER JOIN t_alur b ON(a.kdalur=b.kdalur)
							LEFT OUTER JOIN t_jenisgiat c ON(a.jenisgiat=c.jenisgiat)
							LEFT OUTER JOIN(
								SELECT	a.id_rko,
										b.nourut,
										b.terima,
										b.created_at
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
								FROM d_rko_pagu1
								GROUP BY id_rko
							) e ON(a.id=e.id_rko)
							LEFT OUTER JOIN(
								SELECT	id_rko,
										COUNT(*) AS jml,
										SUM(nilai) AS nilai
								FROM d_rko_pagu2
								GROUP BY id_rko
							) f ON(a.id=f.id_rko)
							LEFT OUTER JOIN t_alur_status g ON(a.kdalur=g.kdalur AND d.nourut=g.nourut)
							LEFT OUTER JOIN t_level h ON(g.kdlevel=h.kdlevel)
							LEFT OUTER JOIN t_user i ON(a.id_jubar=i.id)
							LEFT OUTER JOIN(
								SELECT	id_rko,
										COUNT(*) AS jml1,
										SUM(totnilmak) AS nilai1
								FROM d_transaksi
								GROUP BY id_rko
							) j ON(a.id=j.id_rko)
							LEFT OUTER JOIN t_unit_instansi k ON(a.id_unit=k.id_unit)
							WHERE a.kdsatker='".session('kdsatker')."' and a.thang='".session('tahun')."' ".$where1." ".$where2." ";

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
			else{
				$sOrder = " ORDER BY STR_TO_DATE(a.created_at,'%d-%m-%Y %H:%i:%s') DESC ";
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
			
			//return $sQuery;
			
			$rows = DB::select($sQuery);
			
			foreach( $rows as $row )
			{
				$aksi='<center>
							<a href="rko/status/routing-slip/'.$row->id.'" target="_blank" id="'.$row->id.'" title="Cetak routing slip?" class="btn btn-xs btn-success"><i class="fa fa-print"></i></a>
						</center>';
						
				$urrko = $row->urrko;
				if(strlen($urrko)>50){
					$urrko = substr($row->urrko,0,50).'<a href="javascript:;" title="'.$row->urrko.'">...dst</a>';
				}
							
				$output['aaData'][] = array(
					$row->id,
					$row->nmunit,
					'<ul>
						<li>Alur  : '.$row->nmalur.'</li>
						<li>Jenis : '.$row->nmjenisgiat.'</li>
						<li>Tgl	  : '.$row->tgrko.'</li>
						<li>Ket   : '.$urrko.'</li>
					</ul>',
					'<div style="text-align:right;">'.number_format($row->nilai).'</div>',
					'<ul>
						<li>'.$row->created_at.'</li>
						<li>'.$row->nmlevel.'</li>
						<li>'.$row->nmstatus.'</li>
					</ul>',
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
	
	public function cek($id_rko)
	{
		try{
			$rows = DB::select("
				SELECT	a.id,
						DATE_FORMAT(a.tgrko,'%d-%m-%Y') AS tgrko,
						IFNULL(a.nospby,'N/A') as nospby,
						b.nmalur,
						IFNULL(c.nmjenisgiat,'-') AS nmjenisgiat,
						a.urrko,
						IF(a.kdalur='01',
							IFNULL(d.nilai,0),
							IFNULL(e.nilai,0)
						) AS nilai,
						ifnull(f.nmdok,'') as nmdok,
						ifnull(concat(g.nama,' - ',g.nip),'N/A') as jubar
				FROM d_rko a
				LEFT OUTER JOIN t_alur b ON(a.kdalur=b.kdalur)
				LEFT OUTER JOIN t_jenisgiat c ON(a.jenisgiat=c.jenisgiat)
				LEFT OUTER JOIN(
					SELECT	id_rko,
							SUM(nilai) AS nilai
					FROM d_rko_pagu1
					GROUP BY id_rko
				) d ON(a.id=d.id_rko)
				LEFT OUTER JOIN(
					SELECT	id_rko,
							SUM(nilai) AS nilai
					FROM d_rko_pagu2
					GROUP BY id_rko
				) e ON(a.id=e.id_rko)
				LEFT OUTER JOIN(
					SELECT	a.id_rko,
							GROUP_CONCAT('<li><a target=\"_blank\" href=\"rko/gup-ls/dok/',a.nmfile,'/download\">',b.nmdok,'</li>' ORDER BY a.id SEPARATOR '') AS nmdok
					FROM d_rko_dok a
					LEFT OUTER JOIN t_dok b ON(a.id_dok=b.id)
					GROUP BY a.id_rko
				) f ON(a.id=f.id_rko)
				LEFT OUTER JOIN t_user g on(a.id_jubar=g.id)
				WHERE a.id=?
			",[
				$id_rko
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
	
	public function detil(Request $request, $id_rko)
	{
		try{
			if($id_rko!='xxx'){
				
				$rows=DB::select("
					select	kdalur
					from d_rko
					where id=?
				",[
					$id_rko
				]);
				
				if(count($rows)>0){
					
					$kdalur = $rows[0]->kdalur;
					
					if($kdalur=='01'){
						
						$aColumns = array('nmspj','kdakun','nama','nip','kdgol','uraian','nilai','ppn','pph21','pph22','pph23','pph24');
						/* Indexed column (used for fast and accurate table cardinality) */
						$sIndexColumn = "nmspj";
						/* DB table to use */
						$sTable = "select	a.id,
											concat(a.kdprogram,'-',a.kdgiat,'-',a.kdoutput,'-',a.kdsoutput,'-',a.kdkmpnen,'-',a.kdskmpnen,'-',a.kdakun) as kdakun,
											'' as nip,
											a.uraian,
											'' as nmspj,
											'' as kdgol,
											'' as nama,
											ifnull(a.nilai,0) as nilai,
											'' as ppn,
											'' as pph21,
											'' as pph22,
											'' as pph23,
											'' as pph24
									from d_rko_pagu1 a
									where a.id_rko=".$id_rko."
									order by a.id desc";

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
							$output['aaData'][] = array(
								$row->nmspj,
								$row->kdakun,
								$row->nama,
								$row->nip,
								$row->uraian,
								'<div style="text-align:right;">'.number_format($row->nilai).'</div>',
								'',
								'',
								'',
								'',
								''
							);
						}
						
						return response()->json($output);
						
					}
					else{
						
						$aColumns = array('nmspj','kdakun','nama','nip','kdgol','uraian','nilai','ppn','pph21','pph22','pph23','pph24');
						/* Indexed column (used for fast and accurate table cardinality) */
						$sIndexColumn = "nmspj";
						/* DB table to use */
						$sTable = "select	a.id,
											concat(a.kdprogram,'-',a.kdgiat,'-',a.kdoutput,'-',a.kdsoutput,'-',a.kdkmpnen,'-',a.kdskmpnen,'-',a.kdakun) as kdakun,
											a.nip,
											a.uraian,
											b.nmspj,
											ifnull(e.nmgol,f.nmgol) as kdgol,
											if(a.kdspj='01',ifnull(c.nama,d.nama),ifnull(a.nama,'-')) as nama,
											ifnull(a.nilai,0) as nilai,
											ifnull(a.ppn,0) as ppn,
											ifnull(a.pph_21,0) as pph21,
											ifnull(a.pph_22,0) as pph22,
											ifnull(a.pph_23,0) as pph23,
											ifnull(a.pph_24,0) as pph24
									from d_rko_pagu2 a
									left outer join t_spj b on(a.kdspj=b.kdspj)
									left outer join t_pegawai c on(a.nip=c.nip)
									left outer join t_pegawai_non d on(a.id_peg_non=d.id)
									left outer join t_gol e on(c.kdgol=e.kdgol)
									left outer join t_gol f on(d.kdgol=f.kdgol)
									where a.id_rko=".$id_rko."
									order by a.id desc";

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
							$output['aaData'][] = array(
								$row->nmspj,
								$row->kdakun,
								$row->nama,
								$row->nip,
								$row->kdgol,
								$row->uraian,
								'<div style="text-align:right;">'.number_format($row->nilai).'</div>',
								'<div style="text-align:right;">'.number_format($row->ppn).'</div>',
								'<div style="text-align:right;">'.number_format($row->pph21).'</div>',
								'<div style="text-align:right;">'.number_format($row->pph22).'</div>',
								'<div style="text-align:right;">'.number_format($row->pph23).'</div>',
								'<div style="text-align:right;">'.number_format($row->pph24).'</div>'
							);
						}
						
						return response()->json($output);
						
					}
					
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
			$tanggal = '';
			if($request->input('tgsurat')!=''){
				$arr_tanggal = explode("-", $request->input('tgsurat'));
				$tanggal = $arr_tanggal[2].'-'.$arr_tanggal[1].'-'.$arr_tanggal[0];
			}
			
			$rows = DB::select("
				SELECT	b.id_rko,
						b.nourut,
						d.nourut_setuju,
						d.nourut_setuju_non_sekre,
						d.nourut_tolak,
						d.nourut_tolak_non_sekre,
						d.is_rekam_setuju,
						d.is_rekam_tolak,
						d.is_non_sekre,
						ifnull(d.ur_setuju_tolak,'-') as ur_setuju_tolak,
						c.kdalur,
						ifnull(e.nilai,0)+ifnull(f.nilai,0) as nilai,
						ifnull(g.is_sekre,'') as is_sekre
				FROM(
					SELECT	MAX(id) AS id
					FROM d_rko_status
					WHERE id_rko=?
				) a
				LEFT OUTER JOIN d_rko_status b ON(a.id=b.id)
				LEFT OUTER JOIN d_rko c ON(b.id_rko=c.id)
				LEFT OUTER JOIN t_alur_status d ON(c.kdalur=d.kdalur AND b.nourut=d.nourut)
				LEFT OUTER JOIN(
					select id_rko,
							sum(nilai) as nilai
					from d_rko_pagu2
					group by id_rko
				) e ON(b.id_rko=e.id_rko)
				LEFT OUTER JOIN(
					select id_rko,
							sum(nilai) as nilai
					from d_rko_pagu1
					group by id_rko
				) f ON(b.id_rko=f.id_rko)
				LEFT OUTER JOIN t_unit_instansi g ON(c.id_unit=g.id_unit)
			",[
				$request->input('id_rko')
			]);
			
			if(count($rows)>0){
				
				if($rows[0]->nilai>0){
					
					if($request->input('status')=='0'){ //tolak
					
						if($rows[0]->nourut_tolak!=='x'){
							
							if($rows[0]->is_rekam_tolak!=='1'){
								
								$nourut_tolak = $rows[0]->nourut_tolak;
								
								//cek sekre bukan
								if($rows[0]->is_sekre!='1'){
									
									if($rows[0]->is_non_sekre=='1'){
										$nourut_tolak = $rows[0]->nourut_tolak_non_sekre;
									}
									
								}
								
								$insert = DB::insert("
									insert into d_rko_status(id_rko,nourut,terima,id_user,created_at,updated_at,nosurat,tgsurat,ketsurat)
									values(?,?,?,?,now(),now(),?,?,?)
								",[
									$rows[0]->id_rko,
									$nourut_tolak,
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
								
								$nourut_setuju = $rows[0]->nourut_setuju;
								
								//cek sekre bukan
								if($rows[0]->is_sekre!='1'){
									
									if($rows[0]->is_non_sekre=='1'){
										$nourut_setuju = $rows[0]->nourut_setuju_non_sekre;
									}
									
								}
								
								$insert = DB::insert("
									insert into d_rko_status(id_rko,nourut,terima,id_user,created_at,updated_at,nosurat,tgsurat,ketsurat)
									values(?,?,?,?,now(),now(),?,?,?)
								",[
									$rows[0]->id_rko,
									$nourut_setuju,
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
					return 'Nilai RKO 0, data tidak dapat dilanjutkan!';
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
	
	public function routing_slip($param)
	{
		try{
			$rows = DB::select("
				SELECT	IFNULL(a.nosurat,'N/A') AS nosurat,
						IFNULL(DATE_FORMAT(a.tgsurat,'%d-%m-%Y'),'N/A') AS tgsurat,
						IFNULL(a.ketsurat,'N/A') AS ketsurat,
						c.status,
						IF(a.terima='1','Dilanjutkan','Dikembalikan') AS lanjut,
						d.nama,
						e.nmlevel,
						a.created_at,
						c.norma
				FROM d_rko_status a
				LEFT OUTER JOIN d_rko b ON(a.id_rko=b.id)
				LEFT OUTER JOIN t_alur_status c ON(b.kdalur=c.kdalur AND a.nourut=c.nourut)
				LEFT OUTER JOIN t_user d ON(a.id_user=d.id)
				LEFT OUTER JOIN t_level e ON(c.kdlevel=e.kdlevel)
				WHERE a.id_rko=?
				ORDER BY a.id ASC
			",[
				$param
			]);
			
			if(count($rows)>0){
				
				$data='<table class="table table-bordered">
						<thead>
							<tr>
								<th>No</th>
								<th>No.Surat</th>
								<th>Tgl.Surat</th>
								<th>Ket</th>
								<th>Status</th>
								<th>Terima/Tolak</th>
								<th>Nama</th>
								<th>Posisi</th>
								<th>Tgl.Proses</th>
								<th>Norma Waktu</th>
							</tr>
						</thead>
						<tbody>';
				
				$i=1;
				foreach($rows as $row){
					$data .= '<tr>
									<td>'.$i++.'</td>
									<td>'.$row->nosurat.'</td>
									<td>'.$row->tgsurat.'</td>
									<td>'.$row->ketsurat.'</td>
									<td>'.$row->status.'</td>
									<td>'.$row->lanjut.'</td>
									<td>'.$row->nama.'</td>
									<td>'.$row->nmlevel.'</td>
									<td>'.$row->created_at.'</td>
									<td>'.$row->norma.'</td>
								</tr>';
				}
				
				$data .= '</tbody></table>';
				
				return $data;
				
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
	
	public function unit_dropdown()
	{
		
	}
	
}