<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RefUserController extends Controller {

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
			$aColumns = array('id','username','nama','nik','nmlevel','status','aktif','kdlevel');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "select	a.id,
								a.username,
								a.nama,
								a.nik,
								b.nmlevel,
								b.kdlevel,
								a.aktif,
								if(a.aktif='1','Aktif','Tidak Aktif') as status
					from t_user a
					left outer join(
						select	a.id_user,
									group_concat(a.kdlevel) as kdlevel,
									group_concat(b.nmlevel) as nmlevel
						from t_user_level a
						left outer join t_level b on(a.kdlevel=b.kdlevel)
						group by a.id_user
					) b on(a.id=b.id_user)
					order by a.id desc";
			
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
				$nmlevel = '<ul>';
				$arr_nmlevel = explode(",", $row->nmlevel);
				for($i=0;$i<count($arr_nmlevel);$i++){
					$nmlevel .= '<li>'.$arr_nmlevel[$i].'</li>';
				}
				$nmlevel .= '</ul>';
				
				$aksi='<center>
							<a href="javascript:;" id="'.$row->id.'" title="Ubah data" class="btn btn-xs btn-primary ubah"><i class="fa fa-pencil"></i></a>
							<a href="javascript:;" id="'.$row->id.'" title="Reset data" class="btn btn-xs btn-success reset"><i class="fa fa-refresh"></i></a>
							<a href="javascript:;" id="'.$row->id.'" title="Hapus" class="btn btn-xs btn-danger hapus"><i class="fa fa-times"></i></a>
						</center>';
							
				$output['aaData'][] = array(
					$row->no,
					$row->username,
					$row->nama,
					$row->nik,
					$nmlevel,
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
	
	public function pilih(Request $request, $id)
	{
		try{
			$rows = DB::select("
				select	id,
						nama,
						nik,
						username,
						email,
						alamat,
						telp,
						aktif
				from t_user
				where id=?
			",[
				$id
			]);
			
			if(count($rows)>0){
				
				$rows_detil = DB::select("
					select	kdlevel
					from t_user_level
					where id_user=?
				",[
					$id
				]);
				
				if(count($rows_detil)>0){
				
					$detil = array();
					foreach($rows_detil as $row_detil){
						$detil[] = $row_detil->kdlevel;
					}
					
					$data = (array)$rows[0];
					$data['kdlevel'] = implode(",", $detil);
					
					return response()->json($data);
					
				}
				
			}
			
		}
		catch(\Exception $e){
			return $e;
		}
	}
	
	public function simpan(Request $request)
	{
		try{
			DB::beginTransaction();
			
			if(count($request->input('kdlevel'))>0){
				
				$arr_level = $request->input('kdlevel');
				
				if($request->input('inp-rekambaru')=='1'){
				
					$password = md5('samawa2018');
					
					$rows = DB::select("
						select	count(*) as jml
						from t_user
						where username=? or nik=? or email=?
					",[
						$request->input('username'),
						$request->input('nik'),
						$request->input('email')
					]);
					
					if($rows[0]->jml==0){
						
						$id_user=DB::table('t_user')->insertGetId([
							'username' => $request->input('username'),
							'password' => $password,
							'nama' => $request->input('nama'),
							'nik' => $request->input('nik'),
							'telp' => $request->input('telp'),
							'alamat' => $request->input('alamat'),
							'email' => $request->input('email'),
							'foto' => 'no-image.png',
							'aktif' => '1'
						]);
						
						if($id_user) {
							
							for($i=0;$i<count($arr_level);$i++){
								
								$aktif = '0';
								if($i==0){
									$aktif = '1';
								}
								
								$insert = DB::insert("
									insert into t_user_level(id_user,kdlevel,aktif)
									values(?,?,?)
								",[
									$id_user,
									$arr_level[$i],
									$aktif
								]);
								
							}
							
							if($insert){
								DB::commit();
								return 'success';
							}
							else{
								return 'Data level gagal disimpan!';
							}
							
						}
						else {
							return 'Proses simpan gagal. Hubungi Administrator.';
						}
						
					}
					else{
						return 'Username/NIK/Email ini sudah ada!';
					}
					
				}
				else{
					
					$update = DB::update("
						update t_user
						set nama=?,
							nik=?,
							telp=?,
							alamat=?,
							email=?,
							aktif=?
						where id=?
					",[
						$request->input('nama'),
						$request->input('nik'),
						$request->input('telp'),
						$request->input('alamat'),
						$request->input('email'),
						$request->input('aktif'),
						$request->input('inp-id')
					]);
					
					$delete = DB::delete("
						delete from t_user_level where id_user=?
					",[
						$request->input('inp-id')
					]);
					
					for($i=0;$i<count($arr_level);$i++){
						
						$aktif = '0';
						if($i==0){
							$aktif = '1';
						}
						
						$insert = DB::insert("
							insert into t_user_level(id_user,kdlevel,aktif)
							values(?,?,?)
						",[
							$request->input('inp-id'),
							$arr_level[$i],
							$aktif
						]);
						
					}
					
					if($insert){
						DB::commit();
						return 'success';
					}
					else{
						return 'Data level gagal disimpan!';
					}
					
				}
				
			}
			else{
				return 'Kode level belum dipilih!';
			}
			
		}
		catch(\Exception $e){
			return $e;
		}		
	}
	
	public function hapus(Request $request)
	{
		try{
			DB::beginTransaction();
			$delete = DB::delete("
				delete from t_user_level
				where id_user=?
			",[
				$request->input('id')
			]);
			
			if($delete){
				
				$delete = DB::delete("
					delete from t_user
					where id=?
				",[
					$request->input('id')
				]);
				
				if($delete==true) {
					DB::commit();
					return 'success';
				}
				else {
					return 'Proses hapus gagal. Hubungi Administrator.';
				}
				
			}
			else{
				return 'Hapus data level user gagal!';
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
				select	id,
						nama,
						nik
				from t_user
				order by nama asc
			");
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->id.'">'.$row->nama.' | '.$row->nik.'</option>';
				}
				
				return $data;
				
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