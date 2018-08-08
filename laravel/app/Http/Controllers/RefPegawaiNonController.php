<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RefPegawaiNonController extends Controller {

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
			$aColumns = array('id','nip','npwp','nama','nmgol','instansi','status');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "SELECT 
						  a.id,
						  a.nip,
						  a.npwp,
						  a.nama,
						  IFNULL(b.nmgol,'') nmgol,
						  IFNULL(a.instansi,'') instansi,
						  CASE WHEN a.aktif = '1' THEN 'Aktif' ELSE 'Tidak Aktif' END status
						FROM
						  t_pegawai_non a 
						  LEFT JOIN t_gol b 
						    ON a.kdgol = b.kdgol 
						ORDER BY nama ";
			
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
				$aksi='<center>
							<a href="javascript:;" id="'.$row->id.'" title="Ubah data" class="btn btn-xs btn-primary ubah"><i class="fa fa-pencil"></i></a>
							<a href="javascript:;" id="'.$row->id.'" title="Hapus" class="btn btn-xs btn-danger hapus"><i class="fa fa-times"></i></a>
						</center>';
							
				$output['aaData'][] = array(
					$row->id,
					$row->nip,
					$row->npwp,
					$row->nama,
					$row->nmgol,
					$row->instansi,
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

	public function hapus(Request $request)
	{
		try{
			$rows = DB::select("
				SELECT	COUNT(*) AS jml
				FROM d_rko_pagu2
				WHERE id_peg_non=?
			",[
				$request->input('id')
			]);
			
			if($rows[0]->jml==0){
				
				$rows = DB::select("
					SELECT	COUNT(*) AS jml
					FROM d_rapat_detil
					WHERE id_peg_non=?
				",[
					$request->input('id')
				]);
				
				if($rows[0]->jml==0){
					
					$rows = DB::select("
						SELECT	COUNT(*) AS jml
						FROM d_perjadin_detil
						WHERE id_peg_non=?
					",[
						$request->input('id')
					]);
					
					if($rows[0]->jml==0){
						
						$delete = DB::delete("
							delete from t_pegawai_non
							where id=?
						",[
							$request->input('id')
						]);
						
						if($delete==true) {
							return 'success';
						}
						else {
							return 'Proses hapus gagal. Hubungi Administrator.';
						}
						
					}
					else{
						return 'Pegawai ini terdaftar di Realisasi Perjadin, data tidak dapat dihapus!';
					}
					
				}
				else{
					return 'Pegawai ini terdaftar di Realisasi Rapat, data tidak dapat dihapus!';
				}
				
			}
			else{
				return 'Pegawai ini terdaftar di RKO, data tidak dapat dihapus!';
			}
			
		}
		catch(\Exception $e){
			return $e;
		}		
	}	

	public function simpan(Request $request)
	{
		try{
			if($request->input('inp-rekambaru')=='1'){
								
				$rows = DB::select("
					select	count(*) as jml
					from t_pegawai_non
					where id=?
				",[
					$request->input('inp-id')
				]);
				
				if($rows[0]->jml==0){
					
					$insert = DB::insert("
						insert into t_pegawai_non
						(nama, nip, npwp, kdgol,instansi, aktif) 
						values ('".$request->input('nama')."',
								'".$request->input('nip')."',
								'".$request->input('npwp')."',
								'".$request->input('kdgol')."',
								'".$request->input('instansi')."',
								'".$request->input('aktif')."')"
					);
					
					if($insert==true) {
						return 'success';
					}
					else {
						return 'Proses simpan gagal. Hubungi Administrator.';
					}
					
				}
				else{
					return 'Id pegawai ini sudah ada!';
				}
				
			}
			else{
				
				$update = DB::update("
					update t_pegawai_non
					set nama=?,
						nip=?,
						npwp=?,
						kdgol=?,
						instansi=?,	
						aktif=?
					where id=?
				",[
					$request->input('nama'),
					$request->input('nip'),
					$request->input('npwp'),
					$request->input('kdgol'),					
					$request->input('instansi'),
					$request->input('aktif'),
					$request->input('inp-id')
				]);
				
				if($update==true) {
					return 'success';
				}
				else {
					return 'Proses simpan gagal. Hubungi Administrator.';
				}
				
			}
			
		}
		catch(\Exception $e){
			return $e;
		}		
	}

	public function pilih(Request $request, $id)
	{
		try{
			$rows = DB::select("
				select	id,
						nama,
						nip,
						npwp,
						instansi,
						kdgol,
						aktif
				from t_pegawai_non
				where id=?
			",[
				$id
			]);
			
			if(count($rows)>0){
				return response()->json($rows[0]);
			}
			
		}
		catch(\Exception $e){
			return $e;
		}
	}

	// dari sini ke bawah gak tau dipakai atau nggak ...
	public function kegiatan(Request $request, $id_rko)
	{
		try{
			$aColumns = array('nama','nip','kdgol','nilai','pph21','pph22','pph23','pph24');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "nama";
			/* DB table to use */
			$sTable = "	SELECT	a.*,
								0 as nilai,
								0 as pph21,
								0 as pph22,
								0 as pph23,
								0 as pph24
						FROM t_pegawai a
						WHERE a.aktif='1'
						ORDER BY a.nama ASC";
			
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
			
			$rows1 = DB::select("
				SELECT	*
				FROM t_jab_kegiatan
				ORDER BY kdjab
			");
			
			$jabatan='<option value="" style="display:none;">Pilih jabatan</option>';
			foreach($rows1 as $row1){
				$cek='';
				if($row1->kdjab=='06'){
					$cek='selected="selected"';
				}
				$jabatan.='<option value="'.$row1->kdjab.'" '.$cek.'>'.$row1->nmjab.'</option>';
			}
			
			foreach( $rows as $row )
			{
				$output['aaData'][] = array(
					$row->nama,
					$row->nip,
					$row->kdgol,
					'<select name="jabatan_pegawai['.$row->nip.']" value="06">'.$jabatan.'</select>',
					'<input type="text" name="nilai_pegawai['.$row->nip.']" class="val_num uang" style="text-align:right;" value="0">',
					'<input type="text" name="pph21_pegawai['.$row->nip.']" class="val_num uang" style="text-align:right;" value="0">',
					'<input type="text" name="pph22_pegawai['.$row->nip.']" class="val_num uang" style="text-align:right;" value="0">',
					'<input type="text" name="pph23_pegawai['.$row->nip.']" class="val_num uang" style="text-align:right;" value="0">',
					'<input type="text" name="pph24_pegawai['.$row->nip.']" class="val_num uang" style="text-align:right;" value="0">',
					'<center>
						<input type="checkbox" name="pilih_pegawai['.$row->nip.']">
					</center>'
				);
			}
			
			return response()->json($output);
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function kegiatan1(Request $request, $id_rko)
	{
		try{
			$aColumns = array('id','nama','nip','kdgol','instansi','nilai','pph21','pph22','pph23','pph24');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "	SELECT	a.*,
								0 as nilai,
								0 as pph21,
								0 as pph22,
								0 as pph23,
								0 as pph24
						FROM t_pegawai_non a
						WHERE a.aktif='1'
						ORDER BY a.nama ASC";
			
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
			
			$rows1 = DB::select("
				SELECT	*
				FROM t_jab_kegiatan
				ORDER BY kdjab
			");
			
			$jabatan='<option value="" style="display:none;">Pilih jabatan</option>';
			foreach($rows1 as $row1){
				$cek='';
				if($row1->kdjab=='06'){
					$cek='selected="selected"';
				}
				$jabatan.='<option value="'.$row1->kdjab.'" '.$cek.'>'.$row1->nmjab.'</option>';
			}
			
			foreach( $rows as $row )
			{
				$output['aaData'][] = array(
					$row->nama,
					$row->nip,
					$row->kdgol,
					$row->instansi,
					'<select name="jabatan_pegawai1['.$row->id.']">'.$jabatan.'</select>',
					'<input type="text" name="nilai_pegawai1['.$row->id.']" class="val_num uang" style="text-align:right;">',
					'<input type="text" name="pph21_pegawai1['.$row->id.']" class="val_num uang" style="text-align:right;" value="0">',
					'<input type="text" name="pph22_pegawai1['.$row->id.']" class="val_num uang" style="text-align:right;" value="0">',
					'<input type="text" name="pph23_pegawai1['.$row->id.']" class="val_num uang" style="text-align:right;" value="0">',
					'<input type="text" name="pph24_pegawai1['.$row->id.']" class="val_num uang" style="text-align:right;" value="0">',
					'<center>
						<input type="checkbox" name="pilih_pegawai1['.$row->id.']">
					</center>'
				);
			}
			
			return response()->json($output);
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function transaksi(Request $request, $id_rko)
	{
		try{
			if($id_rko!='xxx'){
				$aColumns = array('nama','nip','kdgol','kdjab','nilai','status');
				/* Indexed column (used for fast and accurate table cardinality) */
				$sIndexColumn = "nama";
				/* DB table to use */
				$sTable = "	SELECT	a.*,
									b.kdjab,
									b.nilai,
									IF(IFNULL(b.nip,'')='','0','1') AS status
							FROM t_pegawai a
							LEFT OUTER JOIN d_rko_pagu2 b ON(b.id_rko=".$id_rko." AND a.nip=b.nip)
							ORDER BY a.nama";
				
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
				
				$rows1 = DB::select("
					SELECT	*
					FROM t_jab_kegiatan
					ORDER BY kdjab
				");
				
				foreach( $rows as $row )
				{
					$jabatan='<option value="" style="display:none;">Pilih jabatan</option>';
					foreach($rows1 as $row1){
						$jabatan.='<option value="'.$row1->kdjab.'">'.$row1->nmjab.'</option>';
						if($row1->kdjab==$row->kdjab){
							$jabatan.='<option value="'.$row1->kdjab.'" selected="selected">'.$row1->nmjab.'</option>';
						}
					}
					
					$cek = '';
					if($row->status=='1'){
						$cek = 'checked';
					}
					
					$pajak = 0;
					if(substr($row->kdgol,0,1)=='3'){
						$pajak = $row->nilai*0.05;
					}
					elseif(substr($row->kdgol,0,1)=='4'){
						$pajak = $row->nilai*0.15;
					}
					
					$output['aaData'][] = array(
						$row->nama,
						$row->nip,
						$row->kdgol,
						'<select name="jabatan_pegawai['.$row->nip.']">'.$jabatan.'</select>',
						'<input type="text" name="nilai_pegawai['.$row->nip.']" class="val_num uang" style="text-align:right;" value="'.$row->nilai.'">',
						'<input type="text" name="pajak_pegawai['.$row->nip.']" class="val_num uang" style="text-align:right;" value="'.$pajak.'">',
						'<center>
							<input type="checkbox" name="pilih_pegawai['.$row->nip.']" '.$cek.'>
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
	
	public function transaksi1(Request $request, $id_rko)
	{
		try{
			if($id_rko!='xxx'){
				$aColumns = array('nama','nip','kdgol','instansi','kdjab','nilai','id','status');
				/* Indexed column (used for fast and accurate table cardinality) */
				$sIndexColumn = "nama";
				/* DB table to use */
				$sTable = "	SELECT	a.*,
									b.kdjab,
									b.nilai,
									IF(IFNULL(b.id_peg_non,'')='','0','1') AS status
							FROM t_pegawai_non a
							LEFT OUTER JOIN d_rko_pagu2 b ON(b.id_rko=".$id_rko." AND a.id=b.id_peg_non)
							ORDER BY a.nama";
				
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
				
				$rows1 = DB::select("
					SELECT	*
					FROM t_jab_kegiatan
					ORDER BY kdjab
				");
				
				foreach( $rows as $row )
				{
					$jabatan='<option value="" style="display:none;">Pilih jabatan</option>';
					foreach($rows1 as $row1){
						$jabatan.='<option value="'.$row1->kdjab.'">'.$row1->nmjab.'</option>';
						if($row1->kdjab==$row->kdjab){
							$jabatan.='<option value="'.$row1->kdjab.'" selected="selected">'.$row1->nmjab.'</option>';
						}
					}
					
					$cek = '';
					if($row->status=='1'){
						$cek = 'checked';
					}
					
					$pajak = 0;
					if(substr($row->kdgol,0,1)=='3'){
						$pajak = $row->nilai*0.05;
					}
					elseif(substr($row->kdgol,0,1)=='4'){
						$pajak = $row->nilai*0.15;
					}
					
					$output['aaData'][] = array(
						$row->nama,
						$row->nip,
						$row->kdgol,
						$row->instansi,
						'<select name="jabatan_pegawai1['.$row->id.']">'.$jabatan.'</select>',
						'<input type="text" name="nilai_pegawai1['.$row->id.']" class="val_num uang" style="text-align:right;" value="'.$row->nilai.'">',
						'<input type="text" name="pajak_pegawai1['.$row->id.']" class="val_num uang" style="text-align:right;" value="'.$pajak.'">',
						'<center>
							<input type="checkbox" name="pilih_pegawai1['.$row->id.']" '.$cek.'>
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
	
	public function transaksi2(Request $request, $id_rko)
	{
		try{
			if($id_rko!='xxx'){
				$aColumns = array('nama','nip','kdgol','kdjab','nilai','status');
				/* Indexed column (used for fast and accurate table cardinality) */
				$sIndexColumn = "nama";
				/* DB table to use */
				$sTable = "	SELECT	a.*,
									b.kdjab,
									IFNULL(b.nilai,0) as nilai,
									IF(IFNULL(b.nip,'')='','0','1') AS status
							FROM t_pegawai a
							LEFT OUTER JOIN d_rko_pagu2 b ON(b.id_rko=".$id_rko." AND a.nip=b.nip)
							ORDER BY a.nama";
				
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
				
				$rows1 = DB::select("
					SELECT	DISTINCT CONCAT(kdlokasi,kdkabkota) AS kdkabkota,
							nmkabkota
					FROM t_kabkota
					ORDER BY kdlokasi,kdkabkota
				");
				
				$rows2 = DB::select("
					SELECT	*
					FROM t_jenis_perjadin
				");
				
				$rows3 = DB::select("
					SELECT	*
					FROM t_tingkat_perjadin
				");
				
				$kdkabkota1='<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows1 as $row1){
					$kdkabkota1.='<option value="'.$row1->kdkabkota.'">'.$row1->nmkabkota.'</option>';
				}
				
				$kdkabkota2='<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows1 as $row2){
					$kdkabkota2.='<option value="'.$row2->kdkabkota.'">'.$row2->nmkabkota.'</option>';
				}
				
				$jenis='<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows2 as $row1){
					$jenis.='<option value="'.$row1->jenis_perjadin.'">'.$row1->jenis_perjadin.'</option>';
				}
				
				$tingkat='<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows3 as $row1){
					$tingkat.='<option value="'.$row1->tingkat_perjadin.'">'.$row1->tingkat_perjadin.'</option>';
				}
				
				foreach( $rows as $row )
				{
					$cek = '';
					if($row->status=='1'){
						$cek = 'checked';
					}
					
					$output['aaData'][] = array(
						$row->nama,
						$row->nip,
						$row->kdgol,
						'<select name="jenis_perjadin_pegawai['.$row->nip.']">'.$jenis.'</select>',
						'<select name="tingkat_perjadin_pegawai['.$row->nip.']">'.$tingkat.'</select>',
						'<select name="kdkabkota1_pegawai['.$row->nip.']" class="pilihan">'.$kdkabkota1.'</select>',
						'<select name="kdkabkota2_pegawai['.$row->nip.']" class="pilihan">'.$kdkabkota2.'</select>',
						'<input type="text" name="tanggal_pegawai['.$row->nip.']" class="tanggal">',
						'<input type="text" name="jmlhari_pegawai['.$row->nip.']" class="val_num uang" style="text-align:right;">',
						'<input type="text" name="uang_muka_pegawai['.$row->nip.']" class="val_num uang" style="text-align:right;">',
						'<input type="text" name="nilai_pegawai['.$row->nip.']" class="val_num uang" style="text-align:right;" value="'.$row->nilai.'">',
						'<center><input type="checkbox" name="pilih_pegawai['.$row->nip.']" '.$cek.'></center>'
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
	
	public function transaksi3(Request $request, $id_rko)
	{
		try{
			if($id_rko!='xxx'){
				$aColumns = array('id','nama','nip','kdgol','kdjab','nilai','status');
				/* Indexed column (used for fast and accurate table cardinality) */
				$sIndexColumn = "id";
				/* DB table to use */
				$sTable = "	SELECT	a.*,
									b.kdjab,
									IFNULL(b.nilai,0) as nilai,
									IF(IFNULL(b.id_peg_non,'')='','0','1') AS status
							FROM t_pegawai_non a
							LEFT OUTER JOIN d_rko_pagu2 b ON(b.id_rko=".$id_rko." AND a.id=b.id_peg_non)
							ORDER BY a.nama";
				
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
				
				$rows1 = DB::select("
					SELECT	DISTINCT CONCAT(kdlokasi,kdkabkota) AS kdkabkota,
							nmkabkota
					FROM t_kabkota
					ORDER BY kdlokasi,kdkabkota
				");
				
				$rows2 = DB::select("
					SELECT	*
					FROM t_jenis_perjadin
				");
				
				$rows3 = DB::select("
					SELECT	*
					FROM t_tingkat_perjadin
				");
				
				$kdkabkota1='<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows1 as $row1){
					$kdkabkota1.='<option value="'.$row1->kdkabkota.'">'.$row1->nmkabkota.'</option>';
				}
				
				$kdkabkota2='<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows1 as $row2){
					$kdkabkota2.='<option value="'.$row2->kdkabkota.'">'.$row2->nmkabkota.'</option>';
				}
				
				$jenis='<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows2 as $row1){
					$jenis.='<option value="'.$row1->jenis_perjadin.'">'.$row1->jenis_perjadin.'</option>';
				}
				
				$tingkat='<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows3 as $row1){
					$tingkat.='<option value="'.$row1->tingkat_perjadin.'">'.$row1->tingkat_perjadin.'</option>';
				}
				
				foreach( $rows as $row )
				{
					$cek = '';
					if($row->status=='1'){
						$cek = 'checked';
					}
					
					$output['aaData'][] = array(
						$row->nama,
						$row->nip,
						$row->kdgol,
						'<select name="jenis_perjadin_pegawai1['.$row->id.']">'.$jenis.'</select>',
						'<select name="tingkat_perjadin_pegawai1['.$row->id.']">'.$tingkat.'</select>',
						'<select name="kdkabkota1_pegawai1['.$row->id.']" class="pilihan1">'.$kdkabkota1.'</select>',
						'<select name="kdkabkota2_pegawai1['.$row->id.']" class="pilihan1">'.$kdkabkota2.'</select>',
						'<input type="text" name="tanggal_pegawai1['.$row->id.']" class="tanggal1">',
						'<input type="text" name="jmlhari_pegawai1['.$row->id.']" class="val_num uang" style="text-align:right;">',
						'<input type="text" name="uang_muka_pegawai1['.$row->id.']" class="val_num uang" style="text-align:right;">',
						'<input type="text" name="nilai_pegawai1['.$row->id.']" class="val_num uang" style="text-align:right;" value="'.$row->nilai.'">',
						'<center><input type="checkbox" name="pilih_pegawai1['.$row->id.']" '.$cek.'></center>'
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
	
	public function dropdown()
	{
		try{
			$rows = DB::select("
				select	*
				from t_pegawai_non
				order by nama asc
			");
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->nip.'">'.$row->nip.' - '.$row->nama.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return $e;
		}
	}

}