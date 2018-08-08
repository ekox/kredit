<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransaksiSPPController extends Controller {

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
			$aColumns = array('nospp','nodok','tgdok','tgspp','nospm','tgspm','nosp2d','tgsp2d','kdjenis','nmjenis','jumlah','nilai');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "nospp";
			/* DB table to use */
			$sTable = "	SELECT	'1' AS kdjenis,
								'UP/TUP' AS nmjenis,
								IF(IFNULL(a.nospp,'')='','',b.nospp) AS nospp,
								date_format(b.tgspp,'%d-%m-%Y') as tgspp,
								IFNULL(b.nodok,'') AS nodok,
								date_format(b.tgdok,'%d-%m-%Y') as tgdok,
								IFNULL(b.nospm,'') AS nospm,
								date_format(b.tgspm,'%d-%m-%Y') as tgspm,
								IFNULL(b.nosp2d,'') AS nosp2d,
								date_format(b.tgsp2d,'%d-%m-%Y') as tgsp2d,
								IFNULL(a.jml,0) AS jumlah,
								IFNULL(a.nilai,0) AS nilai
						FROM(
							SELECT	kddept,
									kdunit,
									kdsatker,
									thang,
									nospp,
									COUNT(*) AS jml,
									SUM(nilai) AS nilai
							FROM d_rekap_up a
							WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."'
							GROUP BY kddept,kdunit,kdsatker,thang,nospp
						) a
						LEFT OUTER JOIN d_spp b ON(a.kddept=b.kddept AND a.kdunit=b.kdunit AND a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.nospp=b.nospp)
						
						UNION ALL

						SELECT	'2' AS kdjenis,
								'GUP' AS nmjenis,
								IF(IFNULL(a.nospp,'')='','',b.nospp) AS nospp,
								date_format(b.tgspp,'%d-%m-%Y') as tgspp,
								IFNULL(b.nodok,'') AS nodok,
								date_format(b.tgdok,'%d-%m-%Y') as tgdok,
								IFNULL(b.nospm,'') AS nospm,
								date_format(b.tgspm,'%d-%m-%Y') as tgspm,
								IFNULL(b.nosp2d,'') AS nosp2d,
								date_format(b.tgsp2d,'%d-%m-%Y') as tgsp2d,
								IFNULL(a.jml,0) AS jumlah,
								IFNULL(a.nilai,0) AS nilai
						FROM(
							SELECT	a.kddept,
									a.kdunit,
									a.kdsatker,
									a.thang,
									a.nospp,
									COUNT(*) AS jml,
									SUM(b.nilai) AS nilai
							FROM d_drpp a
							LEFT OUTER JOIN(
								SELECT 	kddept,
										kdunit,
										kdsatker,
										thang,
										nodrpp,
										SUM(totnilmak) AS nilai
								FROM d_transaksi
								GROUP BY kddept,kdunit,kdsatker,thang,nodrpp
							) b ON(a.kddept=b.kddept AND a.kdunit=b.kdunit AND a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.nodrpp=b.nodrpp)
							WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."'
							GROUP BY a.kddept,a.kdunit,a.kdsatker,a.thang,a.nospp
						) a
						LEFT OUTER JOIN d_spp b ON(a.kddept=b.kddept AND a.kdunit=b.kdunit AND a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.nospp=b.nospp)
						
						UNION ALL
						
						SELECT	a.jenis AS kdjenis,
								IF(a.jenis='3','LS Bendahara','LS Non Bendahara') AS nmjenis,
								a.nospp,
								IFNULL(DATE_FORMAT(a.tgspp,'%d-%m-%Y'),'') AS tgspp,
								IFNULL(a.nodok,'') AS nodok,
								IFNULL(DATE_FORMAT(a.tgdok,'%d-%m-%Y'),'') AS tgdok,
								IFNULL(a.nospm,'') AS nospm,
								IFNULL(DATE_FORMAT(a.tgspm,'%d-%m-%Y'),'') AS tgspm,
								IFNULL(a.nosp2d,'') AS nosp2d,
								IFNULL(DATE_FORMAT(a.tgsp2d,'%d-%m-%Y'),'') AS tgsp2d,
								IFNULL(b.jml,0) AS jumlah,
								IFNULL(b.nilai,0) AS nilai
						FROM d_spp a
						LEFT OUTER JOIN(
							SELECT	a.kdsatker,
									a.thang,
									a.nospp_ls AS nospp,
									COUNT(b.id) AS jml,		
									SUM(b.nilai) AS nilai
							FROM d_rko a
							LEFT OUTER JOIN d_rko_pagu2 b ON(a.id=b.id_rko)
							GROUP BY a.kdsatker,a.thang,a.nospp_ls
						) b ON(a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.nospp=b.nospp)
						WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."' AND a.jenis IN('3','4')";
						
			
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
				$aksi='<center>
							<a id="'.$row->kdjenis.'-'.$row->nospp.'" title="Simpan" class="simpan btn btn-xs btn-primary"><i class="fa fa-check"></i></a>
						</center>';
						
				if($row->nospp!=''){
					$aksi='<center>
								<!--<a href="cetak/spp/'.$row->nospp.'" target="_blank" title="Cetak data?" class="btn btn-xs btn-primary"><i class="fa fa-print"></i></a>-->
								<a id="'.$row->nospp.'" href="javascript:;" title="Catat Nomor SPM dan SP2D?" class="btn btn-xs btn-success catat"><i class="fa fa-edit"></i></a>
								<a id="'.$row->kdjenis.'-'.$row->nospp.'" title="Hapus data?" class="hapus btn btn-xs btn-danger"><i class="fa fa-times"></i></a>
							</center>';
				}
				$aColumns = array('nospp','nospm','nosp2d','kdjenis','nmjenis','jumlah','nilai');
				$output['aaData'][] = array(
					$row->kdjenis.'-'.$row->nospp,
					'<input type="text" id="nospp-'.$row->kdjenis.'-'.$row->nospp.'" name="nospp" value="'.$row->nospp.'" class="val_num" disabled maxlength="5">',
					'<input type="text" id="tgspp-'.$row->kdjenis.'-'.$row->nospp.'" name="tgspp" value="'.$row->tgspp.'" disabled>',
					$row->nodok,
					$row->tgdok,
					$row->nospm,
					$row->tgspm,
					$row->nosp2d,
					$row->tgsp2d,
					$row->kdjenis,
					$row->nmjenis,
					'<div style="text-align:right;">'.number_format($row->jumlah).'</div>',
					'<div style="text-align:right;">'.number_format($row->nilai).'</div>',
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
	
	public function rko()
	{
		try{
			$rows = DB::select("
				SELECT	
					a.id_rko,
					c.urrko,
					DATE_FORMAT(c.tgrko,'%d-%m-%Y') AS tgrko,
					d.nilai
				FROM(
					SELECT	id_rko,
						MAX(id) AS max_id
					FROM d_rko_status
					GROUP BY id_rko
				) a
				LEFT OUTER JOIN d_rko_status b ON(a.max_id=b.id)
				LEFT OUTER JOIN d_rko c ON(a.id_rko=c.id)
				LEFT OUTER JOIN(
					SELECT	id_rko,
						SUM(nilai) AS nilai
					FROM d_rko_pagu2
					GROUP BY id_rko
				) d ON(a.id_rko=d.id_rko)
				LEFT OUTER JOIN(
					SELECT	DISTINCT id_rko
					FROM d_transaksi
					WHERE nospp_ls IS NULL
				) e ON(a.id_rko=e.id_rko)
				WHERE c.kdsatker=? and c.thang=? and c.kdalur='03' AND b.nourut=5 AND e.id_rko IS NULL
			",[
				session('kdsatker'),
				session('tahun')
			]);
			
			$data = '<option value="" style="display:none;">Pilih Data</option>';
			foreach($rows as $row){
				$data .= '<option value="'.$row->id_rko.'">'.$row->id_rko.' - '.$row->urrko.' - '.$row->tgrko.' - '.$row->nilai.'</option>';
			}
			
			return $data;
		}
		catch(\Exception $e){
			return $e;
		}
	}
	
	public function detil($id)
	{
		$arr_data=explode('-', $id);
		
		if($arr_data[1]==''){//belum SPP
			
			if($arr_data[0]=='1'){ //UP/TUP
				
				$rows = DB::select("
					SELECT	a.id,
							a.nosurat,
							date_format(a.tgsurat,'%d-%m-%Y') as tgsurat,
							a.uraian,
							a.nilai
					FROM d_rekap_up a
					WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."'
							AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."' AND a.nospp IS NULL
					ORDER BY a.id ASC
				");
				
				$data = '<form id="form-'.$id.'">
							<input type="hidden" name="nospp" id="nospp1-'.$id.'">
							<input type="hidden" name="tgspp" id="tgspp1-'.$id.'">
							<input type="hidden" name="tipe" value="1">
							<table class="table table-bordered">
										<thead>
											<tr>
												<th>No Surat</th>
												<th>Tgl Surat</th>
												<th>Uraian</th>
												<th>Nilai</th>
												<th>Aksi</th>
											</tr>
										</thead>
										<tbody>';
							
				$i=1;
				foreach( $rows as $row )
				{
					$data .= '<tr>
									<td>'.$row->nosurat.'</td>
									<td>'.$row->tgsurat.'</td>
									<td>'.$row->uraian.'</td>
									<td style="text-align:right;">'.number_format($row->nilai).'</td>
									<td><center><input type="checkbox" name="drpp[]" value="'.$row->id.'"></center></td>
								</tr>';
					$i++;
				}
				
				$data .= '</tbody></table></form>';
				
				$output['tabel']=$data;
				
				return response()->json($output);
				
			}
			elseif($arr_data[0]=='2'){ //GUP
				
				$rows = DB::select("
					SELECT	a.kddept,
						a.kdunit,
						a.kdsatker,
						a.thang,
						a.nodrpp,
						COUNT(*) AS jml,
						SUM(b.nilai) AS nilai
					FROM d_drpp a
					LEFT OUTER JOIN(
						SELECT 	kddept,
							kdunit,
							kdsatker,
							thang,
							nodrpp,
							SUM(totnilmak) AS nilai
						FROM d_transaksi
						GROUP BY kddept,kdunit,kdsatker,thang,nodrpp
					) b ON(a.kddept=b.kddept AND a.kdunit=b.kdunit AND a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.nodrpp=b.nodrpp)
					WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."'
							AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."' AND a.nospp IS NULL
					GROUP BY a.kddept,a.kdunit,a.kdsatker,a.thang,a.nodrpp
				");
				
				$data = '<form id="form-'.$id.'">
							<input type="hidden" name="nospp" id="nospp1-'.$id.'">
							<input type="hidden" name="tgspp" id="tgspp1-'.$id.'">
							<input type="hidden" name="tipe" value="2">
							<table class="table table-bordered">
										<thead>
											<tr>
												<th>No DRPP</th>
												<th>Jumlah</th>
												<th>Nilai</th>
												<th>Aksi</th>
											</tr>
										</thead>
										<tbody>';
							
				$i=1;
				foreach( $rows as $row )
				{
					$data .= '<tr>
									<td>'.$row->nodrpp.'</td>
									<td style="text-align:right;">'.number_format($row->jml).'</td>
									<td style="text-align:right;">'.number_format($row->nilai).'</td>
									<td><center><input type="checkbox" name="drpp[]" value="'.$row->nodrpp.'"></center></td>
								</tr>';
					$i++;
				}
				
				$data .= '</tbody></table></form>';
				
				$output['tabel']=$data;
				$output['nospp']='';
				$output['nospm']='';
				$output['nosp2d']='';
				
				return response()->json($output);
				
			}
			
		}
		else{//sudah SPP
		
			if($arr_data[0]=='1'){ //UP/TUP
				
				$rows = DB::select("
					SELECT	a.id,
							a.nosurat,
							date_format(a.tgsurat,'%d-%m-%Y') as tgsurat,
							a.uraian,
							a.nilai
					FROM d_rekap_up a
					WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."'
							AND a.thang='".session('tahun')."' AND a.nospp=?
				",[
					$arr_data[1]
				]);
				
				$data = '<form id="form-'.$id.'">
							<table class="table table-bordered">
										<thead>
											<tr>
												<th>No.Surat</th>
												<th>Tgl.Surat</th>
												<th>Uraian</th>
												<th>Nilai</th>
											</tr>
										</thead>
										<tbody>';
							
				$i=1;
				foreach( $rows as $row )
				{
					$data .= '<tr>
									<td>'.$row->nosurat.'</td>
									<td>'.$row->tgsurat.'</td>
									<td>'.$row->uraian.'</td>
									<td style="text-align:right;">'.number_format($row->nilai).'</td>
								</tr>';
					$i++;
				}
				
				$data .= '</tbody></table></form>';
				
				$output['tabel']=$data;
				
				return response()->json($output);
				
			}
			elseif($arr_data[0]=='2'){ //GUP
				
				$rows = DB::select("
					SELECT	a.nodrpp,
						DATE_FORMAT(a.tgdrpp,'%d-%m-%Y') AS tgdrpp,
						COUNT(*) AS jml,
						SUM(b.nilai) AS nilai
					FROM d_drpp a
					LEFT OUTER JOIN(
						SELECT 	kddept,
							kdunit,
							kdsatker,
							thang,
							nodrpp,
							SUM(totnilmak) AS nilai
						FROM d_transaksi
						GROUP BY kddept,kdunit,kdsatker,thang,nodrpp
					) b ON(a.kddept=b.kddept AND a.kdunit=b.kdunit AND a.kdsatker=b.kdsatker AND a.thang=b.thang AND a.nodrpp=b.nodrpp)
					WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."'
							AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."' AND a.nospp=?
					GROUP BY a.nodrpp,DATE_FORMAT(a.tgdrpp,'%d-%m-%Y')
				",[
					$arr_data[1]
				]);
				
				$data = '<form id="form-'.$id.'">
							<table class="table table-bordered">
										<thead>
											<tr>
												<th>No DRPP</th>
												<th>Tgl DRPP</th>
												<th>Jumlah</th>
												<th>Nilai</th>
											</tr>
										</thead>
										<tbody>';
							
				$i=1;
				foreach( $rows as $row )
				{
					$data .= '<tr>
									<td>'.$row->nodrpp.'</td>
									<td>'.$row->tgdrpp.'</td>
									<td style="text-align:right;">'.number_format($row->jml).'</td>
									<td style="text-align:right;">'.number_format($row->nilai).'</td>
								</tr>';
					$i++;
				}
				
				$data .= '</tbody></table></form>';
				
				$output['tabel']=$data;
				
				return response()->json($output);
				
			}
			elseif($arr_data[0]=='3' || $arr_data[0]=='4'){ //LS
				
				$rows = DB::select("
					SELECT	
						a.id_rko,
						c.urrko,
						DATE_FORMAT(c.tgrko,'%d-%m-%Y') AS tgrko,
						d.nilai
					FROM(
						SELECT	id_rko,
							MAX(id) AS max_id
						FROM d_rko_status
						GROUP BY id_rko
					) a
					LEFT OUTER JOIN d_rko_status b ON(a.max_id=b.id)
					LEFT OUTER JOIN d_rko c ON(a.id_rko=c.id)
					LEFT OUTER JOIN(
						SELECT	id_rko,
							SUM(nilai) AS nilai
						FROM d_rko_pagu2
						GROUP BY id_rko
					) d ON(a.id_rko=d.id_rko)
					WHERE c.kdsatker='".session('kdsatker')."' AND c.thang='".session('tahun')."' AND c.kdalur='03' AND b.nourut=6 AND c.nospp_ls=?
				",[
					$arr_data[1]
				]);
				
				$data = '<form id="form-'.$id.'">
							<table class="table table-bordered">
										<thead>
											<tr>
												<th>ID.RKO</th>
												<th>Uraian RKO</th>
												<th>Tgl.RKO</th>
												<th>Nilai</th>
											</tr>
										</thead>
										<tbody>';
							
				$i=1;
				foreach( $rows as $row )
				{
					$data .= '<tr>
									<td>'.$row->id_rko.'</td>
									<td>'.$row->tgrko.'</td>
									<td>'.$row->urrko.'</td>
									<td style="text-align:right;">'.number_format($row->nilai).'</td>
								</tr>';
					$i++;
				}
				
				$data .= '</tbody></table></form>';
				
				$output['tabel']=$data;
				
				return response()->json($output);
				
			}
			
		}
	}
	
	public function simpan(Request $request)
	{
		try{
			if(count($request->input('drpp'))>0){
			
				if($request->input('nospp')!=''){
					
					$drpp = implode(",", $request->input('drpp'));
					
					DB::beginTransaction();
					
					$insert = DB::insert("
						insert into d_spp(kddept,kdunit,kdsatker,kddekon,thang,kdppk,nospp,tgspp,jenis,id_user,created_at,updated_at)
						values(?,?,?,?,?,?,?,str_to_date(?,'%d-%m-%Y'),?,?,now(),now())
					",[
						session('kddept'),
						session('kdunit'),
						session('kdsatker'),
						session('kddekon'),
						session('tahun'),
						session('kdppk'),
						$request->input('nospp'),
						$request->input('tgspp'),
						$request->input('tipe'),
						session('id_user')
					]);
					
					if($insert){
						
						if($request->input('tipe')=='1'){ //UP
							
							$update = DB::update("
								update d_rekap_up
								set nospp=?
								where id in(".$drpp.")
							",[
								$request->input('nospp')
							]);
							
							if($update){
								DB::commit();
								return 'success';
							}
							else{
								return 'Proses update data rekap UP gagal!';
							}
							
						}
						elseif($request->input('tipe')=='2'){ //GUP
							
							$update = DB::update("
								update d_drpp
								set nospp=?
								where nodrpp in(".$drpp.")
							",[
								$request->input('nospp')
							]);
							
							if($update){
								
								$insert = DB::insert("
									INSERT INTO d_transaksi_status(id_trans,nourut,terima,id_user,created_at,updated_at)
									SELECT	id,
										4 AS nourut,
										'1' AS terima,
										'' AS id_user,
										NOW() AS created_at,
										NOW() AS updated_at
									FROM d_transaksi a
									WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."'
											AND a.thang='".session('tahun')."' AND nodrpp IN(".$drpp.")
								");
								
								if($insert){
									DB::commit();
									return 'success';
								}
								else{
									return 'Data berhasil disimpan tetapi gagal mengupdate status!';
								}
							
							}
							else{
								return 'Proses update data DRPP gagal!';
							}
							
						}
						else{
							return 'Tidak diijinkan!';
						}
						
					}
					else{
						return 'Proses insert SPP gagal!';
					}
					
				}
				else{
					return 'Silahkan isi terlebih dahulu nomor SPP dan tanggalnya.';
				}
				
			}
			else{
				return 'Anda belum memilih Transaksi!';
			}
		
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function rekam(Request $request)
	{
		try{
			if($request->input('id_rko')!='' && $request->input('nospp1')!=''){
				
				DB::beginTransaction();
				
				$insert = DB::insert("
					insert into d_spp(kddept,kdunit,kdsatker,kddekon,thang,kdppk,nospp,nodok,tgdok,jenis,id_user,created_at,updated_at)
					values(?,?,?,?,?,?,?,?,str_to_date(?,'%d-%m-%Y'),?,?,now(),now())
				",[
					session('kddept'),
					session('kdunit'),
					session('kdsatker'),
					session('kddekon'),
					session('tahun'),
					session('kdppk'),
					$request->input('nospp1'),
					$request->input('nodok'),
					$request->input('tgdok'),
					$request->input('jenis'),
					session('id_user')
				]);
				
				if($insert){
					
					$update = DB::update("
						update d_rko
						set nospp_ls=?
						where id=?
					",[
						$request->input('nospp1'),
						$request->input('id_rko')
					]);
					
					if($update){
						
						$now = new \DateTime();
						$created_at = $now->format('Y-m-d H:i:s');
						
						$insert = DB::table('d_rko_status')->insertGetId(
							array(
								'id_rko' => $request->input('id_rko'),
								'nourut' => 6,
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
							return 'Proses simpan gagal!';
						}
					}
					else{
						return 'Status RKO LS gagal diupdate!';
					}
				
				}
				else{
					return 'Proses insert SPP gagal!';
				}
				
			}
			else{
				return 'Silahkan isi terlebih dahulu nomor SPPnya.';
			}
		}
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function catat(Request $request)
	{
		try{
			$update = DB::update("
				update d_spp
				set nospm=?,
					nosp2d=?,
					tgspp=str_to_date(?, '%d-%m-%Y'),
					tgspm=str_to_date(?, '%d-%m-%Y'),
					tgsp2d=str_to_date(?, '%d-%m-%Y'),
					tgbuku=str_to_date(?, '%d-%m-%Y'),
					id_user=?,
					updated_at=now()
				where kdsatker=? and thang=? and kdppk=? and nospp=?
			",[
				$request->input('nospm'),
				$request->input('nosp2d'),
				$request->input('tgspp'),
				$request->input('tgspm'),
				$request->input('tgsp2d'),
				$request->input('tgbuku'),
				session('id_user'),
				session('kdsatker'),
				session('tahun'),
				session('kdppk'),
				$request->input('nospp')
			]);
			
			if($update){
				return 'success';
			}
			else{
				return 'Data gagal disimpan!';
			}
		
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function pilih_transaksi(Request $request, $param)
	{
		try{
			if($param!='xxx'){
				
				$aColumns = array('id','kdppk','kode','nilai');
				/* Indexed column (used for fast and accurate table cardinality) */
				$sIndexColumn = "id";
				/* DB table to use */
				$sTable = "SELECT	a.*,
									c.*
							FROM d_rko a
							LEFT OUTER JOIN(
								SELECT	a.id_rko,
										b.nourut
								FROM(
									SELECT	id_rko,
											MAX(id) AS id
									FROM d_rko_status
									GROUP BY id_rko
								) a
								LEFT OUTER JOIN d_rko_status b ON(a.id=b.id)
							) b ON(a.id=b.id_rko)
							LEFT OUTER JOIN(
								SELECT	a.id_rko,
										GROUP_CONCAT(a.kode) AS kode,
										GROUP_CONCAT(a.nilai) AS nilai
								FROM(
									SELECT	id_rko,
											CONCAT(kdprogram,'.',kdgiat,'.',kdoutput,'.',kdsoutput,'.',kdkmpnen,'.',kdskmpnen,'.',kdakun) AS kode,
											SUM(nilai) AS nilai
									FROM d_rko_pagu2
									GROUP BY id_rko,CONCAT(kdprogram,'.',kdgiat,'.',kdoutput,'.',kdsoutput,'.',kdkmpnen,'.',kdskmpnen,'.',kdakun)
								) a
								GROUP BY a.id_rko
							)c ON(a.id=c.id_rko)
							WHERE a.kddept='".session('kddept')."' AND a.kdunit='".session('kdunit')."' AND a.kdsatker='".session('kdsatker')."' AND a.thang='".session('tahun')."' AND a.kdppk='".session('kdppk')."' AND a.kdalur='02' AND b.nourut=5";
				
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
					$isikode = '<ul>';
					$arr_kode = explode(",", $row->kode);
					for($i=0;$i<count($arr_kode);$i++){
						$isikode .= '<li>'.$arr_kode[$i].'</li>';
					}
					$isikode .= '</ul>';
					
					$isinilai = '<ul>';
					$arr_nilai = explode(",", $row->nilai);
					for($i=0;$i<count($arr_nilai);$i++){
						$isinilai .= '<li style="text-align:right;">'.number_format($arr_nilai[$i]).'</li>';
					}
					$isinilai .= '</ul>';
					
					$output['aaData'][] = array(
						$row->no,
						$row->id,
						$row->kdppk,
						$isikode,
						$isinilai,
						'<center>
							<input type="checkbox" name="pilih_rko['.$row->id.']">
						</center>'
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
						tempat,
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
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function simpan_lama(Request $request)
	{
		try{
			if($request->input('inp-rekambaru')=='1'){ //tambah
				
				DB::beginTransaction();
				
				$arr_tanggal = explode("-", $request->input('tanggal'));
				$tanggal = $arr_tanggal[2].'-'.$arr_tanggal[1].'-'.$arr_tanggal[0];
				
				$now = new \DateTime();
				$created_at = $now->format('Y-m-d H:i:s');
				
				$id_rekap = DB::table('d_spby')->insertGetId(
					array(
						'kddept' => session('kddept'),
						'kdunit' => session('kdunit'),
						'kdsatker' => session('kdsatker'),
						'kddekon' => session('kddekon'),
						'thang' => session('tahun'),
						'kdalur' => '01',
						'kdppk' => session('kdppk'),
						'nospby' => $request->input('nomor'),
						'tgspby' => $tanggal,
						'urspby' => $request->input('uraian'),
						'id_user' => session('id_user'),
						'created_at' => $created_at,
						'updated_at' => $created_at
					)
				);
				
				if($id_rekap){
					
					$arr_rko = array_keys($request->input('pilih_rko'));
					
					for($i=0;$i<count($arr_rko);$i++){
						$query_status[] = "(".$arr_rko[$i].",6,'1',".session('id_user').",now(),now())"; 
					}
					
					$arr_rko = implode(",", $arr_rko);
					
					$update = DB::update("
						update d_rko
						set nospby=?
						where id in(".$arr_rko.")
					",[
						$request->input('nomor')
					]);
					
					if($update){
						
						$insert = DB::insert("
							insert into d_rko_status(id_rko,nourut,terima,id_user,created_at,updated_at)
							values".implode(",", $query_status)."
						");
						
						if($insert){
							DB::commit();
							return 'success';
						}
						else{
							return 'Data berhasil disimpan tetapi gagal mengupdate status!';
						}
					}
					else{
						return 'Proses update data RKO gagal!';
					}
					
				}
				else{
					return 'Proses simpan rekap gagal!';
				}
				
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
			
			$arr_id = explode("-", $request->input('id'));
			$kdjenis = $arr_id[0];
			$nospp = $arr_id[1];
			
			if($kdjenis=='1'){
				
				$rows = DB::select("
					select	distinct id_rko
					from d_rko_pagu1
					where id_rekap_up in(
						select	distinct id
						from d_rekap_up
						where kdsatker=? and thang=? and kdppk=? and nospp=?
					)
				",[
					session('kdsatker'),
					session('tahun'),
					session('kdppk'),
					$nospp
				]);
				
				$update = DB::update("
					update d_rekap_up set nospp=null where nospp=?
				",[
					$nospp
				]);
				
				$delete = DB::delete("
					delete from d_spp where kddept=? and kdunit=? and kdsatker=? and thang=? and nospp=?
				",[
					session('kddept'),
					session('kdunit'),
					session('kdsatker'),
					session('tahun'),
					$nospp
				]);
				
				if($delete){
					DB::commit();
					return 'success';
				}
				else{
					return 'Data gagal dihapus!';
				}
				
			}
			elseif($kdjenis=='2'){
				
				if($this->cek_status($nospp)){
				
					$rows = DB::select("
						select	id
						from d_transaksi
						where nodrpp in(
							select	distinct nodrpp
							from d_drpp
							where kdsatker=? and thang=? and kdppk=? and nospp=?
						)
					",[
						session('kdsatker'),
						session('tahun'),
						session('kdppk'),
						$nospp
					]);
					
					$update = DB::update("
						update d_drpp set nospp=null where nospp=?
					",[
						$nospp
					]);
					
					$delete = DB::delete("
						delete from d_spp where kddept=? and kdunit=? and kdsatker=? and thang=? and nospp=?
					",[
						session('kddept'),
						session('kdunit'),
						session('kdsatker'),
						session('tahun'),
						$nospp
					]);
					
					$query_status = array();
					foreach($rows as $row){
						$query_status[] = "(".$row->id.",3,'0',".session('id_user').",now(),now())"; 
					}
					
					$insert = DB::insert("
						insert into d_transaksi_status(id_trans,nourut,terima,id_user,created_at,updated_at)
						values".implode(",", $query_status)."
					");
					
					if($insert){
						DB::commit();
						return 'success';
					}
					else{
						return 'Data gagal dihapus!';
					}
					
				}
				else{
					return 'Data tidak dapat dihapus!';
				}
				
			}
			elseif($kdjenis=='3'){
				
				$rows = DB::select("
					select	id
					from d_rko
					where kdsatker=? and thang=? and nospp_ls=?
				",[
					session('kdsatker'),
					session('tahun'),
					$nospp
				]);
				
				if(count($rows)>0){
					
					$id_rko = $rows[0]->id;
					
					$delete = DB::delete("
						delete from d_spp where kddept=? and kdunit=? and kdsatker=? and thang=? and nospp=?
					",[
						session('kddept'),
						session('kdunit'),
						session('kdsatker'),
						session('tahun'),
						$nospp
					]);
					
					if($delete){
						
						$update = DB::update("
							update d_rko set nospp_ls=null where kdsatker=? and thang=? and nospp_ls=?
						",[
							session('kdsatker'),
							session('tahun'),
							$nospp
						]);
						
						if($update){
							
							$now = new \DateTime();
							$created_at = $now->format('Y-m-d H:i:s');
							
							$insert = DB::table('d_rko_status')->insertGetId(
								array(
									'id_rko' => $id_rko,
									'nourut' => 5,
									'terima' => '0',
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
								return 'Proses hapus SPP gagal!';
							}
							
						}
						else{
							return 'Data NOSPP RKO gagal dihapus!';
						}
						
					}
					else{
						return 'Data SPP gagal dihapus!';
					}
					
				}
				else{
					return 'Data RKO tidak ditemukan!';
				}
			
			}
			else{
				return 'Jenis lainnya tidak diperkenankan!';
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
			
			if($this->cek_status($request->input('id'))){
				
				$update = DB::update("
					update d_transaksi set nodrpp=null where id=?
				",[
					$request->input('id')
				]);
				
				$delete = DB::delete("
					delete from d_drpp where kddept=? and kdunit=? and kdsatker=? and thang=? and nodrpp=?
				",[
					session('kddept'),
					session('kdunit'),
					session('kdsatker'),
					session('tahun'),
					
				]);
				
				$insert = DB::insert("
					insert d_transaksi_status(id_trans,nourut,terima,id_user,created_at,updated_at)
					values(?,?,?,?,now(),now())
				",[
					$request->input('id'),
					1,
					'0',
					session('id_user')
				]);
				
				if($insert){
					DB::commit();
					return 'success';
				}
				else{
					return 'Data gagal dihapus!';
				}
				
			}
			else{
				return 'Data tidak dapat dihapus!';
			}
			
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function cek_status($nospp)
	{
		try{
			$rows = DB::select("
				SELECT	DISTINCT b.nourut
				FROM d_transaksi a
				LEFT OUTER JOIN(
					SELECT	a.id_trans,
							b.nourut
					FROM(
						SELECT	id_trans,
								MAX(id) AS id
						FROM d_transaksi_status
						GROUP BY id_trans
					) a
					LEFT OUTER JOIN d_transaksi_status b ON(a.id=b.id)
				) b on(a.id=b.id_trans)
				WHERE a.kddept=? and a.kdunit=? and a.kdsatker=? and a.thang=? and a.nodrpp in(
					select distinct nodrpp
					from d_drpp
					WHERE a.kddept=? and a.kdunit=? and a.kdsatker=? and a.thang=? and a.kdppk=? and nospp=?
				)
			",[
				session('kddept'),
				session('kdunit'),
				session('kdsatker'),
				session('tahun'),
				session('kddept'),
				session('kdunit'),
				session('kdsatker'),
				session('tahun'),
				session('kdppk'),
				$nospp
			]);
			
			if($rows[0]->nourut==4 || $rows[0]->nourut==4){
				return true;
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
	
	public function cek_detil($nospby)
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
						WHERE id_rko in (
							select distinct id as id_rko
							from d_rko
							where nospby=?
						)
					) a
					LEFT OUTER JOIN d_rko_status b ON(a.id=b.id)
				) a,
				(
					SELECT	COUNT(*) AS jml
					FROM d_rko
					WHERE nospby=?
				) b
			",[
				$nospby,
				$nospby
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
	
	public function tarik_data_sas($nospp)
	{
		try{
			$rows = DB::select("
				select	*
				from t_api_sisfospm
			");
			
			if(count($rows)>0){
				
				$url = $rows[0]->url.'/'.session('kdsatker').'/'.session('tahun').'/'.$nospp;
				
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				$resp = curl_exec($curl);
				
				if(json_decode($resp)){
					
					$data = (array)json_decode($resp);
					
					if($data['error']==false){
						
						$data = (array)$data['data'];
						
						return response()->json($data);
						
					}
					else{
						$data['message'] = 'Data tidak ditemukan!';
						return response()->json($data);
					}
					
				}
				else{
					$data['message'] = 'Koneksi pada SISFOSPM terputus!';
					return response()->json($data);
				}
				
			}
			else{
				$data['message'] = 'Setting API SISFOSPM belum ada, hubungi Admin!';
				return response()->json($data);
			}
		}
		catch(\Exception $e){
			$data['message'] = 'Kesalahan lainnya!';
			return response()->json($data);
		}
	}

}