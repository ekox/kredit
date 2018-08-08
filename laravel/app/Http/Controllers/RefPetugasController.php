<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RefPetugasController extends Controller {

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
			$aColumns = array('kdpetugas','nmpetugas','nama','nik');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "kdpetugas";
			/* DB table to use */
			$sTable = "select	a.kdpetugas,
									a.nmpetugas,
									c.nama,
									c.nik
						from t_petugas a
						left outer join t_user_petugas b on(a.kdpetugas=b.kdpetugas)
						left outer join t_user c on(b.id_user=c.id)
						order by a.kdpetugas asc";
			
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
							<a href="javascript:;" id="'.$row->kdpetugas.'" title="Ubah data" class="btn btn-xs btn-primary ubah"><i class="fa fa-pencil"></i></a>
							<a href="javascript:;" id="'.$row->kdpetugas.'" title="Hapus" class="btn btn-xs btn-danger hapus"><i class="fa fa-times"></i></a>
						</center>';
							
				$output['aaData'][] = array(
					$row->no,
					$row->kdpetugas,
					$row->nmpetugas,
					$row->nama,
					$row->nik,
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
	
	public function dropdown_jubar()
	{
		try{
			$rows = DB::select("
				SELECT	id,
						nama,
						nip
				FROM t_user
				WHERE kdsatker=? and kdlevel='11' AND kdbpp=?
			",[
				session('kdsatker'),
				session('kdbpp')
			]);
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->id.'">'.$row->nama.' - '.$row->nip.'</option>';
				}
				
				return $data;
				
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
						username,
						email,
						alamat,
						telp,
						kdlevel,
						kdppk,
						kdbpp,
						aktif
				from t_user
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
	
	public function simpan(Request $request)
	{
		try{
			if($request->input('inp-rekambaru')=='1'){
				
				$password = md5('p4ssw0rd!');
				
				$rows = DB::select("
					select	count(*) as jml
					from t_user
					where username=?
				",[
					$request->input('username')
				]);
				
				if($rows[0]->jml==0){
					
					$insert = DB::insert("
						insert into t_user
						(username, password, nama, nip, telp, alamat, email, kdlevel, kddept, kdunit, kdsatker, kddekon, kdppk, kdbpp, foto, aktif) 
						values ('".$request->input('username')."',
								'".$password."', '".$request->input('nama')."',
								'".$request->input('nip')."',
								'".$request->input('telp')."',
								'".$request->input('alamat')."',
								'".$request->input('email')."',
								'".$request->input('kdlevel')."',
								'".session('kddept')."',
								'".session('kdunit')."',
								'".session('kdsatker')."',
								'".session('kddekon')."',
								'".$request->input('kdppk')."',
								'".$request->input('kdbpp')."',
								'no-image.png', 1)"
					);
					
					if($insert==true) {
						return 'success';
					}
					else {
						return 'Proses simpan gagal. Hubungi Administrator.';
					}
					
				}
				else{
					return 'Username ini sudah ada!';
				}
				
			}
			else{
				
				$update = DB::update("
					update t_user
					set nama=?,
						nip=?,
						telp=?,
						alamat=?,
						email=?,
						kdlevel=?,
						kdppk=?,
						kdbpp=?,
						aktif=?
					where id=?
				",[
					$request->input('nama'),
					$request->input('nip'),
					$request->input('telp'),
					$request->input('alamat'),
					$request->input('email'),
					$request->input('kdlevel'),
					$request->input('kdppk'),
					$request->input('kdbpp'),
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
	
	public function hapus(Request $request)
	{
		try{
			$delete = DB::delete("
				delete from t_user
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
		catch(\Exception $e){
			return $e;
		}		
	}
	
	public function reset(Request $request)
	{
		try{
			$update = DB::update("
				update t_user
				set password=?
				where id=?
			",[
				$password = md5('p4ssw0rd!'),
				$request->input('id')
			]);
			
			if($update==true) {
				return 'success';
			}
			else {
				return 'Proses reset gagal. Hubungi Administrator.';
			}
			
		}
		catch(\Exception $e){
			return $e;
		}		
	}
	
	public function dropdown()
	{
		try{
			$rows = DB::select("
				select	*
				from t_petugas
				order by kdpetugas
			");
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->kdpetugas.'">'.$row->kdpetugas.' - '.$row->nmpetugas.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
}