<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RefPegawaiController extends Controller {

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
			$aColumns = array('nip','npwp','nama','nmgol','unit_id','uraian');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "nama";
			/* DB table to use */
			$sTable = "SELECT 
						  a.nip,
						  a.npwp,
						  a.nama,
						  b.nmgol,
						  a.unit_id,
						  c.uraian,
						  a.aktif 
						FROM
						  t_pegawai a 
						  LEFT OUTER JOIN t_gol b 
						    ON a.kdgol = b.kdgol
						  LEFT OUTER JOIN t_unit_instansi c
						    ON a.unit_id = c.id_unit
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
						<a href="javascript:;" id="'.$row->nip.'" title="Ubah data" class="btn btn-xs btn-primary ubah"><i class="fa fa-pencil"></i></a>
					</center>';
							
				$output['aaData'][] = array(
					$row->nip,
					$row->npwp,
					$row->nama,
					$row->nmgol,
					$row->unit_id,
					$row->uraian,
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
	
	public function getAllPegawai(Request $request)
	{
		try{
			$aColumns = array('id','nama','nip','kdgol','jenis','uraian');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "SELECT	CONCAT(a.kode,'-',
								IF(a.kode='1',
									a.nip,
									a.id
								)
							) AS id,
							a.nama,
							a.nip,
							a.kdgol,
							IF(a.kode='1','Peg.Int','Peg.Eks') AS jenis,
							a.uraian
						FROM(
							SELECT	'1' AS kode,
								NULL AS id,
								a.nip,
								a.nama,
								a.kdgol,
								b.uraian
							FROM t_pegawai a
							LEFT OUTER JOIN t_unit_instansi b ON(a.unit_id=b.id_unit)

							UNION ALL

							SELECT	'2' AS kode,
								a.id,
								a.nip,
								a.nama,
								a.kdgol,
								a.instansi
							FROM t_pegawai_non a
						) a
						ORDER BY a.nama ASC";
			
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
			
			$i = 1;
			foreach( $rows as $row )
			{			
				$aksi = '<center><input type="checkbox" name="pilih_pegawai_temp['.$row->id.']" class="pilih-pegawai-temp"></center>';
							
				$output['aaData'][] = array(
					$i++,
					$row->nama,
					$row->nip,
					$row->kdgol,
					$row->jenis,
					$row->uraian,
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
	
	public function pilih(Request $request, $id)
	{
		try{
			$rows = DB::select("
				select	nama,
						nip,
						npwp,
						instansi,
						kdgol,
						aktif,
						unit_id
				from t_pegawai
				where nip=?
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
	
	public function simpan(Request $request)
	{
		try{
			$update = DB::update("
				update t_pegawai
				set npwp=?,
					unit_id=?
				where nip=?
			",[
				$request->input('npwp'),
				$request->input('unit_id'),
				$request->input('nip')
			]);
			
			if($update==true) {
				return 'success';
			}
			else {
				return 'Proses simpan gagal. Hubungi Administrator.';
			}
		}
		catch(\Exception $e){
			return $e;
		}		
	}

	public function kegiatan(Request $request, $tipe, $param, $param1, $jenis, $tanggal)
	{
		try{
			if($param1!='xxx' && $jenis!='x' && $tanggal!='xxxxxxxx'){
				
				if($jenis=='1'){ //jika RKO
					
					$where = "SELECT	a.nip,
										GROUP_CONCAT(a.id_rko ORDER BY a.id_rko SEPARATOR ',') AS id_rko
							FROM d_rko_pagu2 a
							LEFT OUTER JOIN d_rko b ON(a.id_rko=b.id)
							WHERE 	b.tanggal1>=STR_TO_DATE('".$tanggal."','%d-%m-%Y') AND
									b.tanggal1<DATE_ADD(STR_TO_DATE('".$tanggal."','%d-%m-%Y'), INTERVAL ".$param1." DAY) AND
									b.tanggal2>=STR_TO_DATE('".$tanggal."','%d-%m-%Y') AND
									b.tanggal2<DATE_ADD(STR_TO_DATE('".$tanggal."','%d-%m-%Y'), INTERVAL ".$param1." DAY) AND
									a.kdspj='01' AND
									a.nip IS NOT NULL
							GROUP BY a.nip";
					
				}
				elseif($jenis=='2'){ //jika Transaksi
					
					$where = "
							SELECT	a.nip,
									GROUP_CONCAT(a.id_rko SEPARATOR ',') AS id_rko
							FROM(
								SELECT	a.nip,
									GROUP_CONCAT(concat('ID.Rapat : ',a.id_rapat) ORDER BY a.id_rapat SEPARATOR ',') AS id_rko
								FROM(
									SELECT	a.id_rapat,
										a.nip,
										b.tgrapat AS tanggal1,
										DATE_ADD(b.tgrapat, INTERVAL b.jmlhari DAY) AS tanggal2
									FROM d_rapat_detil a
									LEFT OUTER JOIN d_rapat b ON(a.id_rapat=b.id)
								) a
								WHERE 	a.tanggal1>=STR_TO_DATE('".$tanggal."','%d-%m-%Y') AND
									a.tanggal1<DATE_ADD(STR_TO_DATE('".$tanggal."','%d-%m-%Y'), INTERVAL ".$param1." DAY) AND
									a.tanggal2>=STR_TO_DATE('".$tanggal."','%d-%m-%Y') AND
									a.tanggal2<DATE_ADD(STR_TO_DATE('".$tanggal."','%d-%m-%Y'), INTERVAL ".$param1." DAY)
								GROUP BY a.nip
								
								UNION ALL
								
								SELECT	a.nip,
									GROUP_CONCAT(concat('ID.Perjadin : ',a.id_perjadin) ORDER BY a.id_perjadin SEPARATOR ',') AS id_rko
								FROM(
									SELECT	a.id_perjadin,
										a.nip,
										a.tanggal AS tanggal1,
										DATE_ADD(a.tanggal, INTERVAL a.jmlhari DAY) AS tanggal2
									FROM d_perjadin_detil a
								) a
								WHERE 	a.tanggal1>=STR_TO_DATE('".$tanggal."','%d-%m-%Y') AND
									a.tanggal1<DATE_ADD(STR_TO_DATE('".$tanggal."','%d-%m-%Y'), INTERVAL ".$param1." DAY) AND
									a.tanggal2>=STR_TO_DATE('".$tanggal."','%d-%m-%Y') AND
									a.tanggal2<DATE_ADD(STR_TO_DATE('".$tanggal."','%d-%m-%Y'), INTERVAL ".$param1." DAY)
								GROUP BY a.nip
							) a
							GROUP BY a.nip							
							";
					
				}
				
				$aColumns = array('nama','nip','nmgol','nilai','pph21','status_rko','uraian');
				/* Indexed column (used for fast and accurate table cardinality) */
				$sIndexColumn = "nama";
				/* DB table to use */
				$sTable = "	SELECT	a.*,
									c.biaya*".$param1." AS nilai,
									ROUND(IF(c.is_pajak='1',c.biaya*".$param1."*pph21,0)) AS pph21,
									0 AS pph22,
									0 AS pph23,
									0 AS pph24,
									b.nmgol,
									if(d.id_rko is null,'0','1') as status_rko,
									if(d.id_rko is null,'Available',concat('N/A : ',d.id_rko)) as uraian
							FROM t_pegawai a
							LEFT OUTER JOIN t_gol b ON(a.kdgol=b.kdgol)
							LEFT OUTER JOIN t_ref_biaya_rapat c ON(SUBSTR(a.kdgol,1,1)=SUBSTR(c.kdgol,1,1))
							LEFT OUTER JOIN(".$where.") d on(a.nip=d.nip)
							ORDER BY a.nama ASC ";
				
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
					$aksi = '';
					$uraian = '<label class="label label-danger">'.$row->uraian.'</label>';
					if($tipe=='2' || $row->status_rko=='0'){
						$aksi = '<input type="checkbox" name="pilih_pegawai['.$row->nip.']" class="pilih-pegawai" value="'.$row->nip.'">';
						$uraian = '<label class="label label-success">'.$row->uraian.'</label>';
					}
					
					$output['aaData'][] = array(
						$row->nama,
						$row->nip,
						$row->nmgol,
						$uraian,
						'<select id="jabatan_pegawai_'.$row->nip.'" name="jabatan_pegawai['.$row->nip.']" value="06">'.$jabatan.'</select>',
						'<input type="text" id="nilai_pegawai_'.$row->nip.'" name="nilai_pegawai['.$row->nip.']" class="val_num uang" style="text-align:right;" value="'.$row->nilai.'">',
						'<input type="text" id="pph21_pegawai_'.$row->nip.'" name="pph21_pegawai['.$row->nip.']" class="val_num uang" style="text-align:right;" value="'.$row->pph21.'">',
						/*'<input type="text" name="pph22_pegawai['.$row->nip.']" class="val_num uang" style="text-align:right;" value="0">',
						'<input type="text" name="pph23_pegawai['.$row->nip.']" class="val_num uang" style="text-align:right;" value="0">',
						'<input type="text" name="pph24_pegawai['.$row->nip.']" class="val_num uang" style="text-align:right;" value="0">',*/
						'<center>
							'.$aksi.'
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
	
	public function kegiatan1(Request $request, $tipe, $param, $jenis, $tanggal)
	{
		try{
			if($param!='xxx' && $jenis!='x' && $tanggal!='xxxxxxxx'){
				
				if($jenis=='1'){ //jika RKO
					
					$where = "SELECT	a.id_peg_non,
										GROUP_CONCAT(a.id_rko ORDER BY a.id_rko SEPARATOR ',') AS id_rko
							FROM d_rko_pagu2 a
							LEFT OUTER JOIN d_rko b ON(a.id_rko=b.id)
							WHERE 	b.tanggal1>=STR_TO_DATE('".$tanggal."','%d-%m-%Y') AND
									b.tanggal1<DATE_ADD(STR_TO_DATE('".$tanggal."','%d-%m-%Y'), INTERVAL ".$param." DAY) AND
									b.tanggal2>=STR_TO_DATE('".$tanggal."','%d-%m-%Y') AND
									b.tanggal2<DATE_ADD(STR_TO_DATE('".$tanggal."','%d-%m-%Y'), INTERVAL ".$param." DAY) AND
									a.kdspj='01' AND
									a.id_peg_non IS NOT NULL
							GROUP BY a.id_peg_non";
					
				}
				elseif($jenis=='2'){ //jika Transaksi
					
					$where = "SELECT	a.id_peg_non,
									GROUP_CONCAT(a.id_rko SEPARATOR ',') AS id_rko
							FROM(
								SELECT	a.id_peg_non,
									GROUP_CONCAT(concat('ID.Rapat : ',a.id_rapat) ORDER BY a.id_rapat SEPARATOR ',') AS id_rko
								FROM(
									SELECT	a.id_rapat,
										a.id_peg_non,
										b.tgrapat AS tanggal1,
										DATE_ADD(b.tgrapat, INTERVAL b.jmlhari DAY) AS tanggal2
									FROM d_rapat_detil a
									LEFT OUTER JOIN d_rapat b ON(a.id_rapat=b.id)
								) a
								WHERE 	a.tanggal1>=STR_TO_DATE('".$tanggal."','%d-%m-%Y') AND
									a.tanggal1<DATE_ADD(STR_TO_DATE('".$tanggal."','%d-%m-%Y'), INTERVAL ".$param." DAY) AND
									a.tanggal2>=STR_TO_DATE('".$tanggal."','%d-%m-%Y') AND
									a.tanggal2<DATE_ADD(STR_TO_DATE('".$tanggal."','%d-%m-%Y'), INTERVAL ".$param." DAY)
								GROUP BY a.id_peg_non
								
								UNION ALL
								
								SELECT	a.id_peg_non,
									GROUP_CONCAT(concat('ID.Perjadin : ',a.id_perjadin) ORDER BY a.id_perjadin SEPARATOR ',') AS id_rko
								FROM(
									SELECT	a.id_perjadin,
										a.id_peg_non,
										a.tanggal AS tanggal1,
										DATE_ADD(a.tanggal, INTERVAL a.jmlhari DAY) AS tanggal2
									FROM d_perjadin_detil a
								) a
								WHERE 	a.tanggal1>=STR_TO_DATE('".$tanggal."','%d-%m-%Y') AND
									a.tanggal1<DATE_ADD(STR_TO_DATE('".$tanggal."','%d-%m-%Y'), INTERVAL ".$param." DAY) AND
									a.tanggal2>=STR_TO_DATE('".$tanggal."','%d-%m-%Y') AND
									a.tanggal2<DATE_ADD(STR_TO_DATE('".$tanggal."','%d-%m-%Y'), INTERVAL ".$param." DAY)
								GROUP BY a.id_peg_non
							) a
							GROUP BY a.id_peg_non";
					
				}
				
				$aColumns = array('id','nama','instansi','nilai','pph21','status_rko','uraian');
				/* Indexed column (used for fast and accurate table cardinality) */
				$sIndexColumn = "id";
				/* DB table to use */
				$sTable = "	SELECT	a.*,
									ifnull(b.biaya*".$param.",0) AS nilai,
									ROUND(IF(b.is_pajak='1',b.biaya*".$param."*b.pph21,0)) AS pph21,
									0 AS pph22,
									0 AS pph23,
									0 AS pph24,
									if(d.id_rko is null,'0','1') as status_rko,
									if(d.id_rko is null,'Available',concat('N/A : ',d.id_rko)) as uraian
								FROM t_pegawai_non a
								LEFT OUTER JOIN t_ref_biaya_rapat b ON(SUBSTR(a.kdgol,1,1)=SUBSTR(b.kdgol,1,1))
								LEFT OUTER JOIN(".$where.") d on(a.id=d.id_peg_non)
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
					$aksi = '';
					$uraian = '<label class="label label-danger">'.$row->uraian.'</label>';
					if($tipe=='2' || $row->status_rko=='0'){
						$aksi = '<input type="checkbox" name="pilih_pegawai1['.$row->id.']" class="pilih-pegawai" value="'.$row->id.'">';
						$uraian = '<label class="label label-success">'.$row->uraian.'</label>';
					}
					
					$output['aaData'][] = array(
						$row->nama,
						//$row->nip,
						//$row->kdgol,
						$uraian,
						$row->instansi,
						'<select id="jabatan_pegawai1_'.$row->id.'" name="jabatan_pegawai1['.$row->id.']">'.$jabatan.'</select>',
						'<input type="text" id="nilai_pegawai1_'.$row->id.'" name="nilai_pegawai1['.$row->id.']" class="val_num uang" style="text-align:right;" value="'.$row->nilai.'">',
						'<input type="text" id="pph21_pegawai1_'.$row->id.'" name="pph21_pegawai1['.$row->id.']" class="val_num uang" style="text-align:right;" value="'.$row->pph21.'">',
						/*'<input type="text" name="pph22_pegawai1['.$row->id.']" class="val_num uang" style="text-align:right;" value="0">',
						'<input type="text" name="pph23_pegawai1['.$row->id.']" class="val_num uang" style="text-align:right;" value="0">',
						'<input type="text" name="pph24_pegawai1['.$row->id.']" class="val_num uang" style="text-align:right;" value="0">',*/
						'<center>
							'.$aksi.'
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
	
	public function transaksi(Request $request, $id_rko, $param)
	{
		try{
			if($id_rko!='xxx'){
				$aColumns = array('nama','nip','nmgol','kdjab','nilai','pajak','status');
				/* Indexed column (used for fast and accurate table cardinality) */
				$sIndexColumn = "nama";
				/* DB table to use */
				$sTable = "	SELECT	a.*,
									b.kdjab,
									b.nilai,
									IFNULL(b.pph_21,0) as pajak,
									IF(IFNULL(b.nip,'')='','0','1') AS status,
									c.nmgol
							FROM t_pegawai a
							LEFT OUTER JOIN d_rko_pagu2 b ON(b.id_rko=".$id_rko." AND a.nip=b.nip)
							LEFT OUTER JOIN t_gol c ON(a.kdgol=c.kdgol)
							WHERE a.unit_id='".$param."'
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
					
					$output['aaData'][] = array(
						$row->nama,
						$row->nip,
						$row->nmgol,
						'<select name="jabatan_pegawai['.$row->nip.']">'.$jabatan.'</select>',
						'<input type="text" name="nilai_pegawai['.$row->nip.']" class="val_num uang" style="text-align:right;" value="'.$row->nilai.'">',
						'<input type="text" name="pajak_pegawai['.$row->nip.']" class="val_num uang" style="text-align:right;" value="'.$row->pajak.'">',
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
				$aColumns = array('nama','nip','kdgol','instansi','kdjab','nilai','pajak','id','status');
				/* Indexed column (used for fast and accurate table cardinality) */
				$sIndexColumn = "nama";
				/* DB table to use */
				$sTable = "	SELECT	a.*,
									b.kdjab,
									b.nilai,
									IFNULL(b.pph_21,0) as pajak,
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
					
					$output['aaData'][] = array(
						$row->nama,
						$row->nip,
						$row->kdgol,
						$row->instansi,
						'<select name="jabatan_pegawai1['.$row->id.']">'.$jabatan.'</select>',
						'<input type="text" name="nilai_pegawai1['.$row->id.']" class="val_num uang" style="text-align:right;" value="'.$row->nilai.'">',
						'<input type="text" name="pajak_pegawai1['.$row->id.']" class="val_num uang" style="text-align:right;" value="'.$row->pajak.'">',
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
	
	public function transaksi2(Request $request, $id_rko, $param)
	{
		try{
			$aColumns = array('nama','nip','nmgol','nilai','status');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "nama";
			/* DB table to use */
			$sTable = "	SELECT	a.*,
								0 as nilai,
								'' AS status,
								c.nmgol
						FROM t_pegawai a
						LEFT OUTER JOIN t_gol c ON(a.kdgol=c.kdgol)
						WHERE a.unit_id='".$param."'
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
				$output['aaData'][] = array(
					$row->nama,
					$row->nip,
					$row->nmgol,
					'<select name="jenis_perjadin_pegawai['.$row->nip.']">'.$jenis.'</select>',
					'<select name="tingkat_perjadin_pegawai['.$row->nip.']">'.$tingkat.'</select>',
					'<select name="kdkabkota1_pegawai['.$row->nip.']" class="pilihan">'.$kdkabkota1.'</select>',
					'<select name="kdkabkota2_pegawai['.$row->nip.']" class="pilihan">'.$kdkabkota2.'</select>',
					'<input type="text" name="tanggal_pegawai['.$row->nip.']" class="tanggal">',
					'<input type="text" name="jmlhari_pegawai['.$row->nip.']" class="val_num uang" style="text-align:right;">',
					'<input type="text" name="nilai_pegawai['.$row->nip.']" class="val_num uang" style="text-align:right;" value="'.$row->nilai.'">',
					'<center>
						<input type="checkbox" name="pilih_pegawai['.$row->nip.']" class="pilih-pegawai">
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
	
	public function transaksi3(Request $request, $id_rko)
	{
		try{
			$aColumns = array('id','nama','nip','kdgol','nilai','status');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "	SELECT	a.*,
								0 as nilai,
								'' AS status
						FROM t_pegawai_non a
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
					'<input type="text" name="nilai_pegawai1['.$row->id.']" class="val_num uang" style="text-align:right;" value="'.$row->nilai.'">',
					'<center>
						<input type="checkbox" name="pilih_pegawai1['.$row->id.']" class="pilih-pegawai">
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
	
	public function dropdown()
	{
		try{
			$rows = DB::select("
				select	*
				from t_pegawai
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
	
	public function group_dropdown()
	{
		try{
			$rows = DB::select("
				SELECT	DISTINCT id_unit,uraian
				FROM t_unit_instansi
				ORDER BY id_unit ASC
			");
			
			if(count($rows)>0){
				
				$data = '<option value="xxx">Semua Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->id_unit.'">'.$row->id_unit.' - '.$row->uraian.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return $e;
		}
	}

}