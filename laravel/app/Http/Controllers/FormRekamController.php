<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;
use mPDF;
use clsTinyButStrong;

class FormRekamController extends Controller {

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
			$where = "";
			if(session('kdlevel')=='01'){
				$where = "where a.kdpetugas='".session('kdpetugas')."' and a.tahun='".session('tahun')."'";
			}
			
			$aColumns = array('id','nmpetugas','nourut','nik','nama','nmstatus','status');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "id";
			/* DB table to use */
			$sTable = "select	a.id,
								d.nmpetugas,
								a.nourut,
								ifnull(c.nik,'N/A') as nik,
								ifnull(c.nama,'N/A') as nama,
								e.nmstatus,
								a.status
					from d_form a
					left outer join d_form_debitur b on(a.id=b.id_form)
					left outer join d_debitur c on(b.nik=c.nik)
					left outer join t_petugas d on(a.kdpetugas=d.kdpetugas)
					left outer join t_status_form e on(a.status=e.status)
					".$where."
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
				$aksi='<center style="width:100px;">
							<div class="dropdown pull-right" style="height:1.5vw !important;">
								<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-check"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="javascript:;" id="'.$row->id.'" title="Hapus data?" class="hapus">Hapus Data</a></li>
									<li><a href="form/rekam/download/'.$row->id.'" target="_blank" title="Cetak form?">Cetak Form</a></li>
								</ul>
							</div>
						</center>';
				
				
				$output['aaData'][] = array(
					$row->id,
					$row->nmpetugas,
					str_pad($row->nourut, 5, '0', STR_PAD_LEFT),
					$row->nik,
					$row->nama,
					$row->nmstatus,
					$aksi
				);
			}
			
			return response()->json($output);
		}
		catch(\Exception $e){
			//return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function kuota()
	{
		try{
			$rows = DB::select("
				select	a.kuota,
							b.realisasi,
							a.kuota-b.realisasi as sisa
				from(
					select	kuota
					from t_petugas_kuota
					where kdpetugas=? and tahun=?
				) a,
				(
					select	count(id) as realisasi
					from d_form
					where kdpetugas=? and tahun=?
				) b
			",[
				session('kdpetugas'),
				session('tahun'),
				session('kdpetugas'),
				session('tahun')
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
	
	public function simpan(Request $request)
	{
		try{
			if($request->input('inp-rekambaru')=='1'){ //tambah
				
				$rows = DB::select("
					select	a.kuota,
								b.realisasi,
								a.kuota-b.realisasi as sisa,
								b.maxurut
					from(
						select	kuota
						from t_petugas_kuota
						where kdpetugas=? and tahun=?
					) a,
					(
						select	count(id) as realisasi,
								max(nourut) as maxurut
						from d_form
						where kdpetugas=? and tahun=?
					) b
				",[
					session('kdpetugas'),
					session('tahun'),
					session('kdpetugas'),
					session('tahun')
				]);
				
				if(count($rows)>0){
					
					if($request->input('generate')>0){
					
						if($rows[0]->sisa>=$request->input('generate')){
							
							for($i=1;$i<=$request->input('generate');$i++){
								
								$sisa = (int)$rows[0]->maxurut+$i;
								
								$arr_insert[] = "('".session('kdpetugas')."','".session('tahun')."',".$sisa.",0,now(),now())";
							
							}
							
							if(count($arr_insert)>0){
								
								$query = "insert into d_form(kdpetugas,tahun,nourut,status,created_at,updated_at)
										  values ".implode(",", $arr_insert);
								
								$insert = DB::insert($query);
								
								if($insert){
									DB::commit();
									return 'success';
								}
								else{
									return 'Proses generate form gagal!';
								}
								
							}
							else{
								return 'Jumlah generate form tidak valid!';
							}
							
						}
						else{
							return 'Generate form melebihi jumlah kuota!';
						}
					
					}
					else{
						return 'Generate form minimal 1 data!';
					}
					
				}
				else{
					return 'Data kuota tidak ditemukan!';
				}
				
			}
			else{
				return 'Fitur belum tersedia!';
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
			$rows = DB::select("
				select	a.*,
						b.*,
						c.*
				from(
					select count(*) as jml
					from d_form_debitur
					where id_form=?
				) a,
				(
					select max(nourut) as maxurut
					from d_form
					where kdpetugas=? and tahun=?
				) b,
				(
					select nourut
					from d_form
					where id=?
				) c
			",[
				$request->input('id'),
				session('kdpetugas'),
				session('tahun'),
				$request->input('id')
			]);
			
			if($rows[0]->jml==0){
				
				if($rows[0]->nourut==$rows[0]->maxurut){
					
					$delete = DB::delete("
						delete from d_form where id=?
					",[
						$request->input('id')
					]);
					
					if($delete){
						return 'success';
					}
					else{
						return 'Proses hapus gagal!';
					}
					
				}
				else{
					return 'Hapus dulu dari nomor urut terakhir!';
				}
				
			}
			else{
				return 'Data form ini digunakan untuk pendaftaran debitur!';
			}
			
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function download($param)
	{
		try{
			$rows = DB::select("
				select	kdpetugas,
						tahun,
						nourut
				from d_form
				where id=?
			",[
				$param
			]);
		
			if(count($rows)>0){
				
				$TBS = new clsTinyButStrong();
				$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
				
				$_value = (object)array(
					'kdpetugas'=>$rows[0]->kdpetugas,
					'id_form'=>str_pad($rows[0]->nourut, 5, '0', STR_PAD_LEFT),
				);
				
				$value[] = $_value;
				
				$TBS->LoadTemplate('reports/template_form_debitur.docx');
				
				$TBS->MergeBlock('v', $value);
				
				//download file
				$TBS->Show(OPENTBS_DOWNLOAD,'form_debitur_'.$rows[0]->kdpetugas.$rows[0]->tahun.str_pad($rows[0]->nourut, 5, '0', STR_PAD_LEFT).'.docx');
				
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