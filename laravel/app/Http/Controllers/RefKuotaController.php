<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RefKuotaController extends Controller {

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
			$aColumns = array('id','kdpetugas','nmpetugas','tahun','kuota','realisasi');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "select	a.id,
								a.kdpetugas,
								b.nmpetugas,
								a.tahun,
								a.kuota,
								ifnull(c.jml,0) as realisasi
					from t_petugas_kuota a
					left outer join t_petugas b on(a.kdpetugas=b.kdpetugas)
					left outer join(
						select	kdpetugas,
									tahun,
									count(*) as jml
						from d_form
						group by kdpetugas,tahun
					) c on(a.kdpetugas=c.kdpetugas and a.tahun=c.tahun)
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
				$aksi='<center>
							-
						</center>';
							
				$output['aaData'][] = array(
					$row->no,
					$row->kdpetugas,
					$row->nmpetugas,
					$row->tahun,
					'<div style="text-align:right;">'.number_format($row->kuota).'</div>',
					'<div style="text-align:right;">'.number_format($row->realisasi).'</div>',
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
	
	public function cari($param1,$param2)
	{
		try{
			$rows = DB::select("
				select	a.*,
							ifnull(b.jml,0) as realisasi,
							ifnull(a.kuota-ifnull(b.jml,0),0) as sisa
				from t_petugas_kuota a
				left outer join(
					select	kdpetugas,
								tahun,
								count(*) as jml
					from d_form
					group by kdpetugas,tahun
				) b on(a.kdpetugas=b.kdpetugas and a.tahun=b.tahun)
				where a.kdpetugas=? and a.tahun=?
			",[
				$param1,
				$param2
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
			$rows = DB::select("
				select	a.*,
							ifnull(b.jml,0) as realisasi,
							ifnull(a.kuota-ifnull(b.jml,0),0) as sisa
				from t_petugas_kuota a
				left outer join(
					select	kdpetugas,
								tahun,
								count(*) as jml
					from d_form
					group by kdpetugas,tahun
				) b on(a.kdpetugas=b.kdpetugas and a.tahun=b.tahun)
				where a.kdpetugas=? and a.tahun=?
			",[
				$request->input('kdpetugas'),
				$request->input('tahun')
			]);
			
			$lanjut = true;
			
			if(count($rows)>0){
				
				//cek sisa
				if($request->kuota<$rows[0]->realisasi){
					$lanjut = false;
				}
				
			}
			
			if($lanjut){
				
				DB::beginTransaction();
				
				$delete = DB::delete("
					delete from t_petugas_kuota
					where kdpetugas=? and tahun=?
				",[
					$request->input('kdpetugas'),
					$request->input('tahun')
				]);
				
				$insert = DB::insert("
					insert into t_petugas_kuota(kdpetugas,tahun,kuota)
					value(?,?,?)
				",[
					$request->input('kdpetugas'),
					$request->input('tahun'),
					$request->input('kuota')
				]);
				
				if($insert==true) {
					DB::commit();
					return 'success';
				}
				else {
					return 'Proses simpan gagal. Hubungi Administrator.';
				}
				
			}
			else{
				return 'Jumlah kuota kurang dari realisasi!';
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
}