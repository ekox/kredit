<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RefPPKController extends Controller {

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
			// $aColumns = array('id','nmalur','nmjenisgiat','urrko','tgrko','created_at');
			// /* Indexed column (used for fast and accurate table cardinality) */
			// $sIndexColumn = "id";
			// /* DB table to use */
			// $sTable = "SELECT	a.id,
			// 					b.nmalur,
			// 					c.nmjenisgiat,
			// 					a.urrko,
			// 					FORMAT(a.tgrko,'%d-%m-%Y') AS tgrko,
			// 					created_at
			// 			FROM d_rko a
			// 			LEFT OUTER JOIN t_alur b ON(a.kdalur=b.kdalur)
			// 			LEFT OUTER JOIN t_jenisgiat c ON(a.jenisgiat=c.jenisgiat)
			// 			ORDER BY a.id DESC";

			$aColumns = array('kdppk','nmppk');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "kdppk";
			/* DB table to use */
			$sTable = "SELECT 
						  kdppk,
						  nmppk 
						FROM
						  t_ppk 
						WHERE kdsatker = '".session('kdsatker')."' 
						  AND thang = '".session('tahun')."' 
						ORDER BY kdppk ";
			
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
				// $aksi='<center>
				// 			<a href="javascript:;" id="'.$row->id.'" title="Ubah data" class="btn btn-primary ubah"><i class="fa fa-pencil"></i></a>
				// 			<a href="javascript:;" id="'.$row->id.'" title="Hapus" class="btn btn-danger hapus"><i class="fa fa-times"></i></a>
				// 		</center>';
							
				$output['aaData'][] = array(
					$row->kdppk,
					$row->nmppk
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
				from t_ppk
				where thang=?
				order by kdppk asc
			",[
			    session('tahun')
			]);
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->kdppk.'">'.$row->nmppk.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return $e;
		}
	}
	
	/**
	 * description 
	 */
	public function hapus_sesi_upload(Request $request)
	{
		try{
			session(['upload_ref_ppk' => null]);
			return 'success';
		}
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	/**
	 * upload data ppk ke direktory data/adk/referensi 
	 */
	public function upload(Request $request)
	{
		try{
			session(['upload_ref_ppk' => null]);
		
			$targetFolder = 'data\adk\referensi\\'; // Relative to the root
			
			if(!empty($_FILES)) {
				$file_name = $_FILES['file']['name'];
				$tempFile = $_FILES['file']['tmp_name'];
				$targetFile = $targetFolder.$file_name;
				$fileTypes = array('zip','ZIP'); // File extensions
				$fileParts = pathinfo($_FILES['file']['name']);
				$fileSize = $_FILES['file']['size'];
				//type file sesuai..??	
				if(in_array($fileParts['extension'],$fileTypes)) {
					
					//isi kosong..??
					if($fileSize>0){
						
						$now = new \DateTime();
						$tglupload = $now->format('YmdHis');
						
						$file_name_baru=$file_name;
						move_uploaded_file($tempFile,$targetFolder.$file_name_baru);
						
						if(file_exists($targetFolder.$file_name_baru)){
							session(['upload_ref_ppk' => $file_name_baru]);
							return '1';
						}
						else{
							return 'ADK gagal diupload!';
						}
						
					}
					else{
						return 'Isi file kosong, periksa data anda.';
					}
				}
				else{
					return 'Tipe file tidak sesuai.';
				}
			}
			else{
				return 'Tidak ada file yang diupload.';
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	/**
	 * simpan data referensi ppk ke tabel 
	 */
	public function simpan(Request $request)
	{
		try{
			$table_name = "t_ppk";
			
			if(session('upload_ref_ppk')!==null){
				
				$path = 'data/adk/referensi/'.session('upload_ref_ppk');
				$arr_file = explode(".", session('upload_ref_ppk'));
				$nmfile = $arr_file[0].'.json';
				
				//ekstrak zip
				$zip = new \ZipArchive;
				$res = $zip->open($path);
			
				if ($res === TRUE) {
					
					$zip->extractTo('data/adk/referensi/');
					$zip->close();
					
					$json = json_decode(file_get_contents('data/adk/referensi/'.$nmfile), true);
				
					if($json){
						
						//var_dump($json);
						
						//mencari nama key dari file json
						for($i = 0; $i < count($json); $i++) {
							$j = count($json[$i]);
							$arrcolumns = array_keys($json[$i]);
						} 
						
						$column_name = implode(", ", $arrcolumns);
						
						//mencari nilai yang akan dimasukkan ke kolom suatu tabel
						for($x = 0; $x < count($json); $x++) {
							$y = count($json[$x]);
							$arrvalues[] = array_values($json[$x]);
							$arv[] = "('".implode("', '",$arrvalues[$x])."','".session('tahun')."')";
						} 
						
						$data_values = implode(", ", $arv);
						
						DB::beginTransaction();
						
						//query untuk pengosongan tabel
						$truncate = "DELETE FROM ".$table_name." WHERE kdsatker='".session('kdsatker')."' AND thang='".session('tahun')."'";
						$sql_truncate = DB::delete($truncate);
						
						//query rekam ke tabel
						$insert = "INSERT INTO ".$table_name." (".$column_name.",thang) VALUES ".$data_values;
						
						//eksekusi query
						$sql_insert = DB::insert($insert);
						
						if($sql_insert){
							DB::commit();
							return 'success';
						}
						else{
							return 'Data gagal disimpan!';
						}
						
					}
					else{
						return 'Data kosong atau proses ekstrak gagal!';
					}
					
				}
				else{
					return 'ADK ZIP gagal diekstrak!';
				}
				
			}
			else{
				return 'Anda belum mengupload file ADK untuk PPK!';
			}
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
}
