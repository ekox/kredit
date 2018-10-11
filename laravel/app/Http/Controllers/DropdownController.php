<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DropdownController extends Controller {

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
	
	public function hunian()
	{
		try{
			$rows = DB::select("
				select	id as kode,
						nmhunian as nilai
				from d_hunian
				order by id desc
			");
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->kode.'">'.$row->nilai.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function harga($id)
	{
		try{
			if($id!=='xxx'){
				
				$rows = DB::select("
					SELECT	a.id,
							CONCAT('TIPE ',a.kdkategori) AS kdkategori,
							a.kdtipe,
							b.ukuran,
							a.niljual,
							a.jmlunit
					FROM d_hunian_dtl a
					LEFT OUTER JOIN t_tipe b ON(a.kdtipe=b.kdtipe)
					WHERE a.id_hunian=?
				",[
					$id
				]);
				
				if(count($rows)>0){
					
					$data = '<table class="table table-bordered">
								<thead>
									<tr>
										<th>Kategori</th>
										<th>Tipe</th>
										<th>Ukuran</th>
										<th>Harga</th>
										<th>Jml.Unit</th>
										<th>Pilih</th>
									</tr>
								</thead>
								<tbody>';
					
					$i = 1;
					foreach($rows as $row){
						$data .= '<tr>
									<td>'.$row->kdkategori.'</td>
									<td>'.$row->kdtipe.'</td>
									<td style="text-align:right;">'.$row->ukuran.'</td>
									<td style="text-align:right;">'.number_format($row->niljual).'</td>
									<td style="text-align:right;">'.number_format($row->jmlunit).'</td>
									<td>
										<center>
											<input id="'.$row->id.'" type="radio" name="id_hunian_dtl" value="'.$row->id.'" class="pilih_hunian">
										</center>
									</td>
								  </tr>';
					}
					
					$data .= '</tbody></table>';
					
					return $data;
					
				}
				else{
					return 'Data harga hunian tidak ditemukan!';
				}
				
			}
			else{
				return 'Data hunian tidak dapat dikosongkan!';
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function jenis_kredit()
	{
		try{
			$rows = DB::select("
				select	kdjenkredit as kode,
						nmjenkredit as nilai
				from t_jenkredit
				where aktif='1'
			");
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->kode.'">'.$row->nilai.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function tipe_kredit()
	{
		try{
			$rows = DB::select("
				select	kdtipe as kode,
							nmtipe as nilai
				from t_tipe_kredit
			");
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->kode.'">'.$row->nilai.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function form_kredit()
	{
		try{
			if(session('kdlevel')=='01'){
				
				$rows = DB::select("
					select	a.id as kode,
							concat(a.kdpetugas,a.tahun,lpad(a.nourut,5,'0')) as nilai
					from d_form a
					left outer join d_form_debitur b on(a.id=b.id_form)
					where a.kdpetugas=? and b.id is null
				",[
					session('kdpetugas')
				]);
				
				if(count($rows)>0){
					
					$data = '<option value="" style="display:none;">Pilih Data</option>';
					foreach($rows as $row){
						$data .= '<option value="'.$row->kode.'">'.$row->nilai.'</option>';
					}
					
					return $data;
					
				}
				
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function jenis_kelamin()
	{
		try{
			$rows = DB::select("
				select	kdkelamin as kode,
						nmkelamin as nilai
				from t_kelamin
			");
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->kode.'">'.$row->nilai.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function agama()
	{
		try{
			$rows = DB::select("
				select	kdagama as kode,
						nmagama as nilai
				from t_agama
			");
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->kode.'">'.$row->nilai.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function pendidikan()
	{
		try{
			$rows = DB::select("
				select	kdpendidikan as kode,
						nmpendidikan as nilai
				from t_pendidikan
			");
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->kode.'">'.$row->nilai.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function pekerjaan()
	{
		try{
			$rows = DB::select("
				select	kdpekerjaan as kode,
						nmpekerjaan as nilai
				from t_pekerjaan
			");
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->kode.'">'.$row->nilai.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function kawin()
	{
		try{
			$rows = DB::select("
				select	kdkawin as kode,
						nmkawin as nilai
				from t_kawin
			");
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->kode.'">'.$row->nilai.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function bpjs()
	{
		try{
			$rows = DB::select("
				select	kdbpjs as kode,
						nmbpjs as nilai
				from t_bpjs
			");
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->kode.'">'.$row->nilai.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function prop()
	{
		try{
			$rows = DB::select("
				select	kdprop as kode,
						nmprop as nilai
				from t_prop
			");
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->kode.'">'.$row->nilai.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function kabkota()
	{
		try{
			$rows = DB::select("
				select	kdkabkota as kode,
						nmkabkota as nilai
				from t_kabkota
			");
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->kode.'">'.$row->nilai.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function kabkota_param($param1)
	{
		try{
			$rows = DB::select("
				select	kdkabkota as kode,
						nmkabkota as nilai
				from t_kabkota
				where kdprop=?
			",[
				$param1
			]);
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->kode.'">'.$row->nilai.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function kecamatan()
	{
		try{
			$rows = DB::select("
				select	kdkec as kode,
						nmkec as nilai
				from t_kecamatan
			");
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->kode.'">'.$row->nilai.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function kecamatan_param($param1,$param2)
	{
		try{
			$rows = DB::select("
				select	kdkec as kode,
						nmkec as nilai
				from t_kecamatan
				where kdprop=? and kdkabkota=?
			",[
				$param1,
				$param2
			]);
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->kode.'">'.$row->nilai.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function kelurahan()
	{
		try{
			$rows = DB::select("
				select	kdkel as kode,
						nmkel as nilai
				from t_kelurahan
			");
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->kode.'">'.$row->nilai.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function kelurahan_param($param1,$param2,$param3)
	{
		try{
			$rows = DB::select("
				select	kdkel as kode,
						nmkel as nilai
				from t_kelurahan
				where kdprop=? and kdkabkota=? and kdkec=?
			",[
				$param1,
				$param2,
				$param3
			]);
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->kode.'">'.$row->nilai.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function hutang()
	{
		try{
			$rows = DB::select("
				select	kdhutang as kode,
						nmhutang as nilai
				from t_hutang
			");
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->kode.'">'.$row->nilai.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function kdkreditur()
	{
		try{
			$rows = DB::select("
				select	kdkreditur as kode,
						nmkreditur as nilai
				from t_kreditur
			");
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->kode.'">'.$row->nilai.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function status_skoring()
	{
		try{
			$rows = DB::select("
				select	status as kode,
						nmstatus as nilai
				from t_status_debitur
				where skoring='1'
				order by status asc
			");
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->kode.'">'.$row->nilai.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function level()
	{
		try{
			$rows = DB::select("
				select	kdlevel as kode,
						nmlevel as nilai
				from t_level
				order by kdlevel asc
			");
			
			if(count($rows)>0){
				
				$data = '<option value="" style="display:none;">Pilih Data</option>';
				foreach($rows as $row){
					$data .= '<option value="'.$row->kode.'">'.$row->nilai.'</option>';
				}
				
				return $data;
				
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
}