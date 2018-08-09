<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;
use mPDF;
use App\Services\APIDukcapil;

class DebiturRekamController extends Controller {

	private function validasi($param1, $param2)
	{
		$arr_param = explode(",", $param2);
		
		$lanjut = true;
		for($i=0;$i<count($arr_param);$i++){
			
			if($param1[$arr_param[$i]]==''){
				$lanjut = false;
				break;
			}
			
		}
		
		return $lanjut;
	}
	
	public function __construct()
	{
	
	}
	
	public function index(Request $request)
	{
		try{
			$where = "";
			if(session('kdlevel')=='01'){
				$where = "where g.kdpetugas='".session('kdpetugas')."'";
			}
			
			$aColumns = array('nik','nama','nmjenkredit','nmtipe','nmhunian','tgpemohon','id_form','nmstatus','status');
			/* Indexed column (used for fast and accurate table cardinality) */
			$sIndexColumn = "nik";
			/* DB table to use */
			$sTable = "select	a.nik,
								a.nama,
								c.nmjenkredit,
								d.nmtipe,
								e.nmhunian,
								date_format(a.tgpemohon,'%d-%m-%Y') as tgpemohon,
								ifnull(concat(g.kdpetugas,g.tahun,LPAD(g.nourut,5,'0')),'00000000000') as id_form,
								b.nmstatus,
								a.status
					from d_debitur a
					left outer join t_status_debitur b on(a.status=b.status)
					left outer join t_jenkredit c on(a.kdjenkredit=c.kdjenkredit)
					left outer join t_tipe_kredit d on(a.kdtipe=d.kdtipe)
					left outer join d_hunian e on(a.id_hunian=e.id)
					left outer join d_form_debitur f on(a.nik=f.nik)
					left outer join d_form g on(f.id_form=g.id)
					".$where."
					order by a.created_at desc";
			
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
				$aksi1 = '';
				if($row->status=='0' || $row->status=='1'){
					$aksi1 = '<li><a href="javascript:;" id="'.$row->nik.'" title="pengecekan DUKCAPIL?" class="validasi">Validasi</a></li>
							  <li><a href="javascript:;" id="'.$row->nik.'" title="Kembalikan data ini?" class="penolakan">Kembalikan Data</a></li>';
				}
				elseif($row->status=='2' && session('kdlevel')=='00'){
					$aksi1 = '<li><a href="javascript:;" id="'.$row->nik.'" title="Batalkan validasi?" class="batal-validasi">Batal Validasi</a></li>';
				}
				
				$aksi='<center style="width:100px;">
							<div class="dropdown pull-right" style="height:1.5vw !important;">
								<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-check"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									'.$aksi1.'
								</ul>
							</div>
							<div class="dropdown pull-right" style="height:1.5vw !important;">
								<button class="btn btn-success btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
									<i class="fa fa-print"></i>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="registrasi/debitur/tanda-terima/'.$row->nik.'" target="_blank" title="cetak tanda terima?">Tanda Terima</a></li>
									<li><a href="javascript:;" id="'.$row->nik.'" title="Lihat detil?" class="detil">Lihat Detil</a></li>
								</ul>
							</div>
						</center>';
				
				$id_form = $row->id_form;
				if($row->id_form=='00000000000'){
					$id_form = '<div style="background-color: #2d89ef;color: #FFF;padding:3px;">'.$row->id_form.'</div>';
				}
				
				$output['aaData'][] = array(
					$row->nik,
					$row->nama,
					$row->nmjenkredit,
					$row->nmtipe,
					$row->nmhunian,
					$row->tgpemohon,
					$id_form,
					$row->nmstatus,
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
	
	public function otorisasi()
	{
		return session('kdlevel');
	}
	
	public function baru()
	{
		try{
			session(array(
				'sesi_upload' => md5(time())
			));
			
			$rows = DB::select("
				select	*
				from t_dok
				order by id asc
			");
			
			if(count($rows)>0){
				
				$data=array();

				$data['htmlout']='';
				$data['jquery']='';
				foreach($rows as $row) {
					
					$data['htmlout'] .= '
						<div class="form-group">
							<label class="control-label col-md-3">'.$row->nmdok.' (*'.$row->tipe.')</label>
							<div class="col-md-6" id="div2-upload-foto">
								<span class="btn btn-primary fileinput-button">
									<i class="fa fa-upload"></i>
									<span>Browse File</span>
									<input id="fileupload-'.$row->id.'" type="file" name="file-'.$row->id.'">
								</span>
								<!-- The global progress bar -->
								<div id="files-'.$row->id.'" class="files"></div>
								<div id="progress-'.$row->id.'" class="progress">
									<div class="progress-bar progress-bar-success"></div>
								</div>
							</div>
							<div class="col-lg-3" id="nmfile-'.$row->id.'"></div>
						</div>';
					
					$data['jquery'] .= "
						jQuery('#fileupload-".$row->id."').click(function(){
							jQuery('#progress-".$row->id." .progress-bar').css('width', 0);
							jQuery('#progress-".$row->id." .progress-bar').html('');
							jQuery('#nmfile-".$row->id."').html('');
						});
		
						jQuery('#fileupload-".$row->id."').fileupload({
							url:'../upload',
							dataType: 'json',
							formData: {
								_token:'".csrf_token()."',
								id_dok:".$row->id."
							},
							done: function (e, data) {
								jQuery('#nmfile-".$row->id."').html(data.files[0].name);
								alertify.log('Upload file '+data.files[0].name+' berhasil!');
							},
							error: function(error) {
								alertify.log(error.responseText);
							},
							progressall: function (e, data) {
								var progress = parseInt(data.loaded / data.total * 100, 10);
								jQuery('#progress-".$row->id." .progress-bar').css('width',progress + '%');
							}
						}).prop('disabled', !$.support.fileInput)
						  .parent().addClass($.support.fileInput ? undefined : 'disabled');
					";
					
				}
				
				$kembali = '<a href="../home">
								Kembali
							</a>';
				
				if(session('authenticated')){
					$kembali = '<a href="../#/debitur/rekam">
									Kembali
								</a>';
				}
			
				return view('debitur', array(
					'kembali' => $kembali,
					'html_upload' => $data['htmlout'],
					'jquery_upload' => $data['jquery']
				));
				
			}
			else{
				return 'Referensi dokumen tidak ditemukan!';
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
			$rows = DB::select("
				select count(*) as jml
				from d_debitur
				where nik=?
			",[
				$request->input('nik')
			]);
			
			//cek duplikasi data
			if($rows[0]->jml==0){
				
				$data = $request->input();
			
				$lanjut = $this->validasi($data, 'kdjenkredit,id_hunian,kdtipe,nik,nokk,nama,kotlhr,tglhr,nmibu,kdkelamin,kdagama,kdpendidikan,kdkawin,kdbpjs,nohp,email,jmltinggal,kdprop,kdkabkota,kdkec,kdkel,kodepos,telp,alamat,kdprop1,kdkabkota1,kdkec1,kdkel1,kodepos1,telp1,alamat1,kdpekerjaan,nmkantor,bidang,jenis,alamat_k,jabatan,atasan,telp_k,tgkerja,penghasilan,jmlkjp,jmlbpjs,jmltanggung,jmlrmh,jmlroda2,jmlroda4,pengeluaran,tgpemohon');
				
				//cek seluruh kolom
				if($lanjut){
					
					//jika pilih tipe
					if($request->input('kdtipe')=='2' || $request->input('kdkawin')=='2'){
						
						$valid = $this->validasi($data, 'nik_p,nama_p,kotlhr_p,tglhr_p,kdkelamin_p,kdagama_p,kdpendidikan_p,nohp_p,email_p,kdprop_p,kdkabkota_p,kdkec_p,kdkel_p,kodepos_p,telp_p,alamat_p,kdpekerjaan_p,nmkantor_p,bidang_p,jenis_p,alamat_k_p,jabatan_p,atasan_p,telp_k_p,tgkerja_p,penghasilan_p');
						
						if(!$valid){
							$lanjut = false;
						}
					}
					
					//cek tipe kredit
					if($lanjut){
						
						if($request->input('kdtipe')=='2' && $request->input('kdkawin')!='2'){
							$lanjut = false;
						}
						
						//cek status kawin
						if($lanjut){
							
							if($request->input('kdkelamin')==$request->input('kdkelamin_p')){
								$lanjut = false;
							}
							
							//cek jenis kelamin pasangan
							if($lanjut){
								
								if($request->input('kdhutang')!==''){
									if($request->input('total')=='' || $request->input('total')=='0' || $request->input('angsuran')=='' || $request->input('angsuran')=='0'){
										$lanjut = false;
									}
								}
								
								//cek hutang
								if($lanjut){
									
									if($request->input('kdbpjs')=='0'){
										if($request->input('jmlbpjs')!=='0'){
											$lanjut = false;
										}
									}
									else{
										if($request->input('jmlbpjs')=='0'){
											$lanjut = false;
										}
									}
									
									//cek bpjs
									if($lanjut){
										
										$rows = DB::select("
											select	1 as id,
													group_concat(nmdok separator ', ') as nmdok
											from t_dok a
											left outer join(
												select	distinct id_dok
												from d_debitur_dok_temp
												where sesi_upload=?
											) b on(a.id=b.id_dok)
											where a.is_wajib='1' and b.id_dok is null
										",[
											session('sesi_upload')
										]);
										
										if(count($rows)>0){
											
											if($rows[0]->nmdok==''){
												
												//cek usia debitur
												$date = date_create($data['tglhr']);
												$usia =  date_diff($date, date_create('today'))->y;
												
												if($usia>=17){
													
													DB::beginTransaction();
												
													$arr_tanggal1 = explode("-", $request->input('tglhr'));
													$tglhr = $arr_tanggal1[2].'-'.$arr_tanggal1[1].'-'.$arr_tanggal1[0];
													
													$arr_tanggal1 = explode("-", $request->input('tgpemohon'));
													$tgpemohon = $arr_tanggal1[2].'-'.$arr_tanggal1[1].'-'.$arr_tanggal1[0];
													
													$arr_tanggal1 = explode("-", $request->input('tgkerja'));
													$tgkerja = $arr_tanggal1[2].'-'.$arr_tanggal1[1].'-'.$arr_tanggal1[0];
													
													$status = '0';
													if(isset($data['id_form'])){
														if($request->input('id_form')!==''){
															$status = '1';
														}
													}
													
													//debitur
													$insert = DB::insert("
														insert into d_debitur
														(kdjenkredit,id_hunian,kdtipe,nik,nokk,npwp,nama,kotalhr,tglhr,nmibu,
														kdkelamin,kdagama,kdpendidikan,kdpekerjaan,kdkawin,kdbpjs,nohp,email,jmltinggal,
														jmlkjp,jmlbpjs,jmltanggung,jmlrmh,jmlroda2,jmlroda4,pengeluaran,tgpemohon,
														status,created_at,updated_at)
														values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,".$status.",now(),now())
													",[
														$data['kdjenkredit'],$data['id_hunian'],$data['kdtipe'],$data['nik'],$data['nokk'],$data['npwp'],$data['nama'],$data['kotlhr'],$tglhr,$data['nmibu'],
														$data['kdkelamin'],$data['kdagama'],$data['kdpendidikan'],$data['kdpekerjaan'],$data['kdkawin'],$data['kdbpjs'],$data['nohp'],$data['email'],
														preg_replace("/[^0-9 \d]/i", "", $data['jmltinggal']),
														preg_replace("/[^0-9 \d]/i", "", $data['jmlkjp']),
														preg_replace("/[^0-9 \d]/i", "", $data['jmlbpjs']),
														preg_replace("/[^0-9 \d]/i", "", $data['jmltanggung']),
														preg_replace("/[^0-9 \d]/i", "", $data['jmlrmh']),
														preg_replace("/[^0-9 \d]/i", "", $data['jmlroda2']),
														preg_replace("/[^0-9 \d]/i", "", $data['jmlroda4']),
														preg_replace("/[^0-9 \d]/i", "", $data['pengeluaran']),
														$tgpemohon
													]);
													
													if($insert){
													
														//alamat ktp
														$insert1 = DB::insert("
															insert into d_debitur_alamat
															(nik,kdalamat,kdprop,kdkabkota,kdkec,kdkel,kodepos,telp,alamat,created_at,updated_at)
															values(?,'1',?,?,?,?,?,?,?,now(),now())
														",[
															$data['nik'],$data['kdprop'],$data['kdkabkota'],$data['kdkec'],
															$data['kdkel'],$data['kodepos'],$data['telp'],$data['alamat']
														]);
														
														//alamat domisili
														$insert2 = DB::insert("
															insert into d_debitur_alamat
															(nik,kdalamat,kdprop,kdkabkota,kdkec,kdkel,kodepos,telp,alamat,created_at,updated_at)
															values(?,'2',?,?,?,?,?,?,?,now(),now())
														",[
															$data['nik'],$data['kdprop1'],$data['kdkabkota1'],$data['kdkec1'],
															$data['kdkel1'],$data['kodepos1'],$data['telp1'],$data['alamat1']
														]);
														
														if($insert1&&$insert2){
															
															if($request->input('kdkawin')=='2'){
															
																$insert = DB::insert("
																	insert into d_debitur_pasangan
																	(nik,nik_p,nama,kotalhr,tglhr,kdkelamin,kdagama,
																	kdpendidikan,nohp,email,
																	kdprop,kdkabkota,kdkec,kdkel,kodepos,telp,alamat,
																	created_at,updated_at)
																	values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,now(),now())
																",[
																	$data['nik'],$data['nik_p'],$data['nama_p'],$data['kotlhr_p'],$data['tglhr_p'],
																	$data['kdkelamin_p'],$data['kdagama_p'],$data['kdpendidikan_p'],$data['nohp_p'],$data['email_p'],$data['kdprop_p'],$data['kdkabkota_p'],$data['kdkec_p'],$data['kdkel_p'],$data['kodepos_p'],$data['telp_p'],$data['alamat_p']
																	
																]);
																
																if(!$insert){
																	$lanjut = false;
																}
																
															}
															
															//cek data pasangan
															if($lanjut){
																
																$insert = DB::insert("
																	insert into d_debitur_pekerjaan
																	(nik,nmkantor,bidang,jenis,alamat,jabatan,atasan,telp,
																	tgkerja,penghasilan,created_at,updated_at)
																	values(?,?,?,?,?,?,?,?,?,?,now(),now())
																",[
																	$data['nik'],$data['nmkantor'],$data['bidang'],$data['jenis'],$data['alamat_k'],
																	$data['jabatan'],$data['atasan'],$data['telp_k'],$tgkerja,
																	preg_replace("/[^0-9 \d]/i", "", $data['penghasilan'])
																]);
																
																if($insert){
																	
																	if($request->input('kdtipe')=='2'){
																		
																		$arr_tanggal1 = explode("-", $request->input('tgkerja_p'));
																		$tgkerja_p = $arr_tanggal1[2].'-'.$arr_tanggal1[1].'-'.$arr_tanggal1[0];
																		
																		$insert = DB::insert("
																			insert into d_debitur_pekerjaan_p
																			(nik,nmkantor,bidang,jenis,alamat,jabatan,atasan,telp,
																			tgkerja,penghasilan,created_at,updated_at)
																			values(?,?,?,?,?,?,?,?,?,?,now(),now())
																		",[
																			$data['nik'],$data['nmkantor'],$data['bidang'],$data['jenis'],$data['alamat_k'],
																			$data['jabatan'],$data['atasan'],$data['telp'],$tgkerja_p,
																			preg_replace("/[^0-9 \d]/i", "", $data['penghasilan_p'])
																		]);
																		
																		if(!$insert){
																			$lanjut = false;
																		}
																		
																	}
																	
																	if($lanjut){
																		
																		if($request->input('kdhutang')!==''){
																			
																			$insert = DB::insert("
																				insert into d_debitur_hutang
																				(nik,kdhutang,total,angsuran,created_at,updated_at)
																				values(?,?,?,?,now(),now())
																			",[
																				$data['nik'],$data['kdhutang'],
																				preg_replace("/[^0-9 \d]/i", "", $data['total']),
																				preg_replace("/[^0-9 \d]/i", "", $data['angsuran'])
																			]);
																			
																			if(!$insert){
																				$lanjut = false;
																			}
																			
																		}
																		
																		if($lanjut){
																		
																			$insert = DB::insert("
																				insert into d_debitur_dok(nik,id_dok,nmfile,created_at,updated_at)
																				select	distinct
																						? as nik,
																						id_dok,
																						nmfile,
																						created_at,
																						updated_at
																				from d_debitur_dok_temp
																				where sesi_upload=?
																			",[
																				$data['nik'],session('sesi_upload')
																			]);
																		
																			if($insert){
																			
																				$delete = DB::delete("
																					delete from d_debitur_dok_temp
																					where sesi_upload=?
																				",[
																					session('sesi_upload')
																				]);
																				
																				if($delete){
																					
																					if(isset($data['id_form'])){
																						if($request->input('id_form')!==''){
																						
																							$insert = DB::insert("
																								insert into d_form_debitur
																								(id_form,nik,created_at,updated_at)
																								values(?,?,now(),now())
																							",[
																								$data['id_form'],$data['nik']
																							]);
																							
																							$update = DB::update("
																								update d_form
																								set status=1,
																									updated_at=now()
																								where id=?
																							",[
																								$data['id_form']
																							]);
																							
																							if(!$insert || !$update){
																								$lanjut = false;
																							}
																							
																						}
																					}
																					
																					if($lanjut){
																						DB::commit();
																						return 'success';
																					}
																					else{
																						return 'Data debitur gagal ditambahkan kedalam form!';
																					}
																					
																				}
																				else{
																					return 'Data temporari gagal dihapus!';
																				}
																			
																			}
																			else{
																				return 'Data dokumen gagal ditambahkan!';
																			}
																			
																		}
																		else{
																			return 'Data hutang gagal ditambahkan!';
																		}
																		
																	}
																	else{
																		return 'Data pekerjaan pasangan gagal ditambahkan!';
																	}
																	
																}
																else{
																	return 'Data pekerjaan debitur gagal ditambahkan!';
																}
																
															}
															else{
																return 'Data pasangan debitur gagal ditambahkan!';
															}
															
														}
														else{
															return 'Alamat debitur gagal ditambahkan!';
														}
														
													}
													else{
														return 'Data debitur gagal ditambahkan!';
													}
													
												}
												else{
													return 'Usia debitur tidak valid!';
												}
												
											}
											else{
												return 'Dokumen belum diupload : '.$rows[0]->nmdok;
											}
											
										}
										else{
											return 'Referensi dokumen tidak ditemukan!';
										}
										
									}
									else{
										return 'Kode BPJS tidak sesuai dengan jumlahnya!';
									}
									
								}
								else{
									return 'Jumlah total dan angsuran hutang wajib diisi!';
								}
								
							}
							else{
								return 'Jenis kelamin pasangan tidak valid!';
							}
							
						}
						else{
							return 'Status kawin tidak valid untuk tipe kredit ini!';
						}
						
					}
					else{
						return 'Data pasangan wajib diisi untuk tipe kredit ini!';
					}
					
				}
				else{
					return 'Selain data pasangan dan hutang, seluruh kolom wajib diisi!';
				}
				
			}
			else{
				return 'Data NIK ini sudah terdaftar di sistem!';
			}
			
		}
		catch(\Exception $e){
			if($e->getCode()==23000){
				return 'Duplikasi data!';
			}
			else{
				return 'Kesalahan lainnya! code:'.$e->getCode();
			}
		}
	}
	
	public function validasi_dukcapil(Request $request, APIDukcapil $apidukcapil)
	{
		try{
			$nik = $request->input('id');
		
			//ambil nokk debitur
			$rows = DB::select("
				select nokk
				from d_debitur
				where nik=?
			",[
				$nik
			]);
			
			if(count($rows)>0){
				
				$nokk = $rows[0]->nokk;
				
				//cek di data internal
				$rows = DB::select("
					select	*
					from d_debitur_dukcapil
					where nik=?
				",[
					$nik
				]);
				
				if(count($rows)>0){ //jika ditemukan tidak perlu cek dukcapil
					
					if($rows[0]->nokk==$nokk){
						
						$update = DB::update("
							update d_debitur
							set status=2
							where nik=?
						",[
							$nik
						]);
						
						if($update){
							return 'success';
						}
						else{
							return 'Proses simpan validasi gagal! (server lokal)';
						}
						
					}
					else{
						return 'Nomor KK debitur tidak valid! (server lokal)';
					}
					
				}
				else{ //jika tidak ditemukan perlu cek dukcapil
					
					$rows = DB::select("
						select	*
						from t_api_setting
						where id=1
					");
					
					if(count($rows)>0){
						
						$url = $rows[0]->url;
						$user = $rows[0]->user;
						$pass = $rows[0]->pass;
						$token = md5('0'.md5(date('dmy')).'Dp');
						
						//return $user.' | '.$pass.' | '.$token.' | ';
						
						$data = '';
						if($rows[0]->aktif=='1'){
							
							$ip = $url;
							
							$post = '{
								"PROC":"GETNIK",
								"APP":"SI RUMAH DP0",
								"PROSWS":"Pendaftaran DP0",
								"USRAPP":"admin",
								"NIK":"'.$nik.'"
							}';
							
							$handle = curl_init($ip);
							curl_setopt($handle, CURLOPT_POST, true);
							curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($handle, CURLOPT_POSTFIELDS, $post);
							curl_setopt($handle, CURLOPT_HTTPHEADER, array(
								'Accept: application/json',
								'Content-Type: application/json',
								'USER: '.$user,
								'PASS: '.$pass,
								'PKEY: '.$token
							));
							
							$resp = curl_exec($handle);
							
							curl_close($handle);
							
							//host to host dukcapil
							$data = str_replace("[", "", $resp);
							$data = str_replace("]", "", $data);
							
						}
						else{ //kebutuhan testing
							if($rows[0]->jenis=='1'){
								$data = file_get_contents('data/json/dukcapil-valid.json');
							}
							else{
								$data = file_get_contents('data/json/dukcapil-unvalid.json');
							}
						}
						
						if(json_decode($data)){
							
							$data = (array)json_decode($data);
							
							if($data['NIK']!==null){
								
								if($data['NO_KK']==$nokk){
									
									$arr_tanggal1 = explode("/", $data['TGL_LHR']);
									$tglhr = $arr_tanggal1[2].'-'.$arr_tanggal1[1].'-'.$arr_tanggal1[0];
									
									DB::beginTransaction();
								
									$insert = DB::insert("
										insert into d_debitur_dukcapil(nik,nokk,nama,tglhr,kotalhr,kdkelamin,kdagama,kdpekerjaan,kdkawin,kdprop,kdkabkota,kdkec,kdkel,alamat,rt,rw,created_at,updated_at)
										values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,now(),now())
									",[
										$data['NIK'],
										$data['NO_KK'],
										$data['NAMA_LGKP'],
										$tglhr,
										$data['TMPT_LHR'],
										$data['JENIS_KLMIN'],
										$data['AGAMA'],
										$data['JENIS_PKRJN'],
										$data['STAT_KWN'],
										str_pad($data['NO_PROP'], 2, '0', STR_PAD_LEFT),
										str_pad($data['NO_KAB'], 2, '0', STR_PAD_LEFT),
										str_pad($data['NO_KEC'], 2, '0', STR_PAD_LEFT),
										str_pad($data['NO_KEL'], 4, '0', STR_PAD_LEFT),
										$data['ALAMAT'],
										$data['NO_RT'],
										$data['NO_RW']
									]);
									
									if($insert){
										
										$update = DB::update("
											update d_debitur
											set status=2
											where nik=?
										",[
											$nik
										]);
										
										if($update){
											DB::commit();
											return 'success';
										}
										else{
											return 'Proses simpan validasi gagal! (server Dukcapil)';
										}
										
									}
									else{
										return 'Data Dukcapil gagal disimpan ke sever lokal!';
									}
									
								}
								else{
									return 'Nomor KK debitur tidak valid! (server Dukcapil)';
								}
								
							}
							else{
								return 'Data debitur tidak valid!';
							}
							
						}
						else{
							return 'Webservis DUKCAPIL tidak aktif. Hubungi Administrator DUKCAPIL.';
						}
						
					}
					else{
						return 'Settingan API tidak ditemukan!';
					}
					
				}
				
			}
			else{
				return 'Data tidak ditemukan!';
			}
		
		}
		catch(\Exception $e){
			return $e;
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function batal_validasi(Request $request, APIDukcapil $apidukcapil)
	{
		try{
			$nik = $request->input('id');
		
			$update = DB::update("
				update d_debitur
				set status=1
				where nik=?
			",[
				$nik
			]);
			
			if($update){
				return 'success';
			}
			else{
				return 'Proses pembatalan validasi gagal!';
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function penolakan(Request $request, APIDukcapil $apidukcapil)
	{
		try{
			$nik = $request->input('id');
		
			$update = DB::update("
				update d_debitur
				set status=3
				where nik=?
			",[
				$nik
			]);
			
			if($update){
				return 'success';
			}
			else{
				return 'Proses penolakan gagal!';
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function tanda_terima($param)
	{
		try{
			$rows = DB::select("
				select	md5(a.nik) as id,
						a.nik,
						a.nokk,
						a.nama,
						d.nmjenkredit,
						f.nmhunian,
						f.alamat,
						e.nmtipe,
						date_format(a.tgpemohon,'%d-%m-%Y') as tgpemohon,
						date_format(a.created_at,'%d-%m-%Y') as created_at,
						c.kdpetugas,
						c.tahun,
						c.nourut
				from d_debitur a
				left outer join d_form_debitur b on(a.nik=b.nik)
				left outer join d_form c on(b.id_form=c.id)
				left outer join t_jenkredit d on(a.kdjenkredit=d.kdjenkredit)
				left outer join t_tipe_kredit e on(a.kdtipe=e.kdtipe)
				left outer join d_hunian f on(a.id_hunian=f.id)
				where a.nik=?
			",[
				$param
			]);
		
			if(count($rows)>0){
				
				$data = (array)$rows[0];
				
				$html_out = view('tanda-terima-debitur', $data);
				
				return $html_out;
				
				/*$mpdf = new mPDF("en", "A4", "15");
				$mpdf->SetTitle('Form RKO');
				
				$mpdf->AddPage('P');
				$mpdf->writeHTML($html_out);
				$mpdf->Output('Tanda_terima_debitur_'.$param.'.pdf','I');*/
				
			}
			else{
				return 'Data tidak ditemukan!';
			}
		}
		catch(\Exception $e){
			return $e;
			//return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function detil1($param)
	{
		try{
			$rows = DB::select("
				select	a.nik,
						a.nokk,
						a.npwp,
						a.nama,
						a.kotalhr,
						date_format(a.tglhr,'%d-%m-%Y') as tglhr,
						b.nmkelamin,
						a.nmibu,
						c.nmagama,
						d.nmpendidikan,
						e.nmpekerjaan,
						f.nmkawin,
						g.nmbpjs,
						h.nmjenkredit,
						i.nmtipe,
						a.email,
						a.nohp,
						a.jmltanggung,
						a.jmlkjp,
						a.jmlbpjs,
						a.jmltinggal,
						a.jmlroda2,
						a.jmlroda4,
						a.jmlrmh,
						a.pengeluaran,
						date_format(a.tgpemohon, '%d-%m-%Y') as tgpemohon,
						j.nmhunian,
						k.nmstatus
			from d_debitur a
			left outer join t_kelamin b on(a.kdkelamin=b.kdkelamin)
			left outer join t_agama c on(a.kdagama=c.kdagama)
			left outer join t_pendidikan d on(a.kdpendidikan=d.kdpendidikan)
			left outer join t_pekerjaan e on(a.kdpekerjaan=e.kdpekerjaan)
			left outer join t_kawin f on(a.kdkawin=f.kdkawin)
			left outer join t_bpjs g on(a.kdbpjs=g.kdbpjs)
			left outer join t_jenkredit h on(a.kdjenkredit=h.kdjenkredit)
			left outer join t_tipe_kredit i on(a.kdtipe=i.kdtipe)
			left outer join d_hunian j on(a.id_hunian=j.id)
			left outer join t_status_debitur k on(a.status=k.status)
			where a.nik=?
			",[
				$param
			]);
		
			if(count($rows)>0){
				
				$data = (array)$rows[0];
				
				$kolom = array_keys($data);
				
				$html = '<legend>Informasi Debitur</legend>
						 <table class="table table-bordered table-hovered">
							<tbody>';
							
				for($i=0;$i<count($kolom);$i++){
					$html .= '<tr>
								<td>'.strtoupper($kolom[$i]).'</td>
								<td>'.$data[$kolom[$i]].'</td>
							  </tr>';
				}
				
				$html .= '</tbody></table>';
				
				return $html;
				
			}
			else{
				return 'Data tidak ditemukan!';
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function detil2($param)
	{
		try{
			$rows = DB::select("
				select	b.nmalamat,
						c.nmprop,
						d.nmkabkota,
						e.nmkec,
						f.nmkel,
						a.kodepos,
						a.alamat
			from d_debitur_alamat a
			left outer join t_alamat b on(a.kdalamat=b.kdalamat)
			left outer join t_prop c on(a.kdprop=c.kdprop)
			left outer join t_kabkota d on(a.kdprop=d.kdprop and a.kdkabkota=d.kdkabkota)
			left outer join t_kecamatan e on(a.kdprop=e.kdprop and a.kdkabkota=e.kdkabkota and a.kdkec=e.kdkec)
			left outer join t_kelurahan f on(a.kdprop=f.kdprop and a.kdkabkota=f.kdkabkota and a.kdkec=f.kdkec and a.kdkel=f.kdkel)
			where a.nik=?
			",[
				$param
			]);
		
			if(count($rows)>0){
				
				$data = (array)$rows[0];
				
				$kolom = array_keys($data);
				
				$html = '<legend>Informasi Alamat</legend>
						 <table class="table table-bordered table-hovered">
							<thead>
								<tr>
									<th>Jenis</th>
									<th>Prop</th>
									<th>Kab/Kota</th>
									<th>Kec</th>
									<th>Kel</th>
									<th>Kodepos</th>
									<th>Alamat</th>
							</thead>
							<tbody>';
							
				foreach($rows as $row){
					$html .= '<tr>
								<td>'.$row->nmalamat.'</td>
								<td>'.$row->nmprop.'</td>
								<td>'.$row->nmkabkota.'</td>
								<td>'.$row->nmkec.'</td>
								<td>'.$row->nmkel.'</td>
								<td>'.$row->kodepos.'</td>
								<td>'.$row->alamat.'</td>
							  </tr>';
				}
				
				$html .= '</tbody></table>';
				
				return $html;
				
			}
			else{
				return 'Data tidak ditemukan!';
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function detil3($param)
	{
		try{
			$rows = DB::select("
				SELECT	a.nik,
						a.nama,
						a.kotalhr, DATE_FORMAT(a.tglhr,'%d-%m-%Y') AS tglhr,
						b.nmkelamin,
						c.nmagama,
						d.nmpendidikan,
						ifnull('-',e.nmpekerjaan) as nmpekerjaan,
						a.email,
						a.nohp,
						ifnull(f.penghasilan,0) as penghasilan
				FROM d_debitur_pasangan a
				LEFT OUTER JOIN t_kelamin b ON(a.kdkelamin=b.kdkelamin)
				LEFT OUTER JOIN t_agama c ON(a.kdagama=c.kdagama)
				LEFT OUTER JOIN t_pendidikan d ON(a.kdpendidikan=d.kdpendidikan)
				LEFT OUTER JOIN t_pekerjaan e ON(a.kdpekerjaan=e.kdpekerjaan)
				LEFT OUTER JOIN d_debitur_pekerjaan_p f on(a.nik=f.nik)
				WHERE a.nik=?
			",[
				$param
			]);
		
			if(count($rows)>0){
				
				$data = (array)$rows[0];
				
				$kolom = array_keys($data);
				
				$html = '<legend>Informasi Pasangan</legend>
						 <table class="table table-bordered table-hovered">
							<tbody>';
							
				for($i=0;$i<count($kolom);$i++){
					$html .= '<tr>
								<td>'.strtoupper($kolom[$i]).'</td>
								<td>'.$data[$kolom[$i]].'</td>
							  </tr>';
				}
				
				$html .= '</tbody></table>';
				
				return $html;
				
			}
			else{
				return 'Data tidak ditemukan!';
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function detil4($param)
	{
		try{
			$rows = DB::select("
				select	a.nmkantor,
							a.bidang,
							a.jenis,
							a.alamat,
							a.atasan,
							a.telp,
							date_format(a.tgkerja, '%d-%m-%Y') as tgkerja,
							a.penghasilan
				from d_debitur_pekerjaan a
				where a.nik=?
			",[
				$param
			]);
		
			if(count($rows)>0){
				
				$data = (array)$rows[0];
				
				$kolom = array_keys($data);
				
				$html = '<legend>Informasi Pekerjaan</legend>
						 <table class="table table-bordered table-hovered">
							<tbody>';
							
				for($i=0;$i<count($kolom);$i++){
					$html .= '<tr>
								<td>'.strtoupper($kolom[$i]).'</td>
								<td>'.$data[$kolom[$i]].'</td>
							  </tr>';
				}
				
				$html .= '</tbody></table>';
				
				return $html;
				
			}
			else{
				return 'Data tidak ditemukan!';
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function detil5($param)
	{
		try{
			$rows = DB::select("
				select	b.nmhutang,
							a.total,
							a.angsuran
				from d_debitur_hutang a
				left outer join t_hutang b on(a.kdhutang=b.kdhutang)
				where a.nik=?
			",[
				$param
			]);
		
			if(count($rows)>0){
				
				$data = (array)$rows[0];
				
				$kolom = array_keys($data);
				
				$html = '<legend>Informasi Hutang</legend>
						 <table class="table table-bordered table-hovered">
							<tbody>';
							
				for($i=0;$i<count($kolom);$i++){
					$html .= '<tr>
								<td>'.strtoupper($kolom[$i]).'</td>
								<td>'.$data[$kolom[$i]].'</td>
							  </tr>';
				}
				
				$html .= '</tbody></table>';
				
				return $html;
				
			}
			else{
				return 'Data tidak ditemukan!';
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function detil6($param)
	{
		try{
			$rows = DB::select("
				select	b.nmdok,
						a.nmfile,
						date_format(a.created_at,'%d%m%Y') as tgupload
				from d_debitur_dok a
				left outer join t_dok b on(a.id_dok=b.id)
				where a.nik=?
			",[
				$param
			]);
		
			if(count($rows)>0){
				
				$html = '<legend>Informasi Dokumen Upload</legend>
						 <table class="table table-bordered table-hovered">
							<tbody>';
							
				foreach($rows as $row){
					$html .= '<tr>
								<td>'.$row->nmdok.'</td>
								<td>
									<a href="data/debitur/upload/'.$row->tgupload.'/'.$row->nmfile.'" target="_blank" class="btn btn-xs btn-success"><i class="fa fa-download"></i></a>
								</td>
							  </tr>';
				}
				
				$html .= '</tbody></table>';
				
				return $html;
				
			}
			else{
				return 'Data tidak ditemukan!';
			}
		}
		catch(\Exception $e){
			return 'Terdapat kesalahan lainnya!';
		}
	}
	
	public function download($param)
	{
		
			$css = new RKOSPBYController;
			$html_out = $css->css();
			
			$rows_satker = DB::select("
				select	kdsatker,
						uraian as nmsatker
			from d_pagu
			where kdsatker=? and thang=? and lvl='0'
			",[
				session('kdsatker'),
				session('tahun')
			]);
			
			$rows_program = DB::select("
				select 	CONCAT('023','.','11','.',a.kdprogram) as kode,
						a.kdprogram,
						b.nmprogram,
						c.kdppk,
						d.nama as nama_ppk,
						d.nip as nip_ppk,
						c.kdbpp,
						c.id_unit,
						e.nama as nama_bpp,
						e.nip as nip_bpp,
						f.nama as nama_pk2,
						f.nip as nip_pk2,
						g.nama as nama_pk1,
						g.nip as nip_pk1,
						h.is_sekre,
						i.uraian as nama_unit,
						sum(a.nilai) as nilai,
						sum(a.ppn) as ppn,
						sum(a.pph_21) as pph_21,
						sum(a.pph_22) as pph_22,
						sum(a.pph_23) as pph_23,
						sum(a.pph_24) as pph_24
				from d_rko_pagu2 a 
				left outer join (select kdprogram, nmprogram
					from t_program where kddept='023' and kdunit='11'
				) b on a.kdprogram=b.kdprogram
				left outer join d_rko c on a.id_rko=c.id
				LEFT OUTER JOIN(
					SELECT kdsatker,kdppk,nama,nip
					 FROM t_user
					 WHERE kdlevel='06' AND aktif='1'
				) d ON(c.kdsatker=d.kdsatker AND c.kdppk=d.kdppk)
				LEFT OUTER JOIN(
					SELECT kdsatker,kdbpp,nama,nip
					 FROM t_user
					 WHERE kdlevel='05' AND aktif='1'
				) e ON(c.kdsatker=e.kdsatker AND c.kdbpp=e.kdbpp)
				LEFT OUTER JOIN(
					 SELECT kdsatker,kdppk,id_unit,nama,nip
								 FROM t_user
								 WHERE kdlevel='03' AND aktif='1'
				) f ON(c.kdsatker=f.kdsatker AND c.kdppk=f.kdppk AND LEFT(c.id_unit,8)=LEFT(f.id_unit,8))
				LEFT OUTER JOIN(
					 SELECT kdsatker,kdppk,id_unit,nama,nip
								 FROM t_user
								 WHERE kdlevel='02' AND aktif='1'
				) g ON(c.kdsatker=g.kdsatker AND c.kdppk=g.kdppk AND c.id_unit=g.id_unit)
				LEFT OUTER JOIN t_unit_instansi h on(c.id_unit=h.id_unit)
				LEFT OUTER JOIN(
					select *
					from t_unit_instansi
					where substr(id_unit,7,5)='00.00'
				) i ON(substr(c.id_unit,1,5)=substr(i.id_unit,1,5))
				where a.id_rko=".$param."
				group by CONCAT('023','.','11','.',a.kdprogram), a.kdprogram, b.nmprogram, c.kdppk, c.kdbpp, c.id_unit
			");
			
			if(count($rows_program)==0) {
				//~ new Exception('No data program found');
				return "tidak ada data";
			} 
			
			$rows_akun = DB::select("
				SELECT	a.*,
						b.nmakun
				FROM(
					SELECT	a.id_rko,
							a.kdakun,
							SUM(nilai) AS nilai,
							sum(a.ppn) as ppn,
							sum(a.pph_21) as pph_21,
							sum(a.pph_22) as pph_22,
							sum(a.pph_23) as pph_23,
							sum(a.pph_24) as pph_24
					FROM d_rko_pagu2 a
					WHERE a.id_rko=?
					GROUP BY a.id_rko,a.kdakun
				) a
				LEFT OUTER JOIN t_akun b ON(a.kdakun=b.kdakun)
				ORDER BY a.kdakun ASC
			",[
				$param
			]);
			
			if(count($rows_akun)==0) {
				//~ new Exception('No data akun found');
				return "tidak ada data";
			} 
			
			function rows_uraian($id_rko, $kdakun) 
			{
				return DB::select("
					select 	if(a.kdspj='01',
								if(a.nip is null,
									concat(c.nama),
									concat(b.nama)
								),
								if(a.kdspj='02',
									a.uraian,
									if(a.nama is null,
										a.uraian,
										a.nama
									)
								)
							) as uraian,
							a.nilai,
							a.ppn,
							a.pph_21,
							a.pph_22,
							a.pph_23,
							a.pph_24,
							substr(b.kdgol,1,1) as kdgol
					from d_rko_pagu2 a
					left outer join t_pegawai b on(a.nip=b.nip)
					left outer join t_pegawai_non c on(a.id_peg_non=c.id)
					where a.id_rko=".$id_rko." and a.kdakun='".$kdakun."'
				");
			}
			
			$html_out.= '<table cellpadding="0" cellspacing="0" class="fz60">';
			$html_out.= '<thead>';
			
			$html_out.= '<tr>';
				$html_out.= '<th class="wd20 tl">NAMA SATKER</th>';
				$html_out.= '<th class="wd2 tc">:</th>';
				$html_out.= '<th class="tl">'.$rows_satker[0]->nmsatker.'</th>';
			$html_out.= '</tr>';
			$html_out.= '<tr>';
				$html_out.= '<th class="tl">KODE SATKER</th>';
				$html_out.= '<th class="tc">:</th>';
				$html_out.= '<th class="tl">'.$rows_satker[0]->kdsatker.'</th>';
			$html_out.= '</tr>';
			$html_out.= '<tr>';
				$html_out.= '<th class="tl">NOMOR DAN TANGGAL DIPA</th>';
				$html_out.= '<th class="tc">:</th>';
				$html_out.= '<th class="tl">SP DIPA- 023.11.1.137608/2018 Tgl. 2 Juli 2018</th>';
			$html_out.= '</tr>';
			$html_out.= '<tr>';
				$html_out.= '<th class="tl">JUMLAH UP-RM</th>';
				$html_out.= '<th class="tc">:</th>';
				$html_out.= '<th class="tl">'.number_format($rows_program[0]->nilai,0,",",".").'</th>';
			$html_out.= '</tr>';
			$html_out.= '<tr>';
				$html_out.= '<th class="tl">UNIT KERJA</th>';
				$html_out.= '<th class="tc">:</th>';
				$html_out.= '<th class="tl">'.$rows_program[0]->nama_unit.'</th>';
			$html_out.= '</tr>';
			
			$html_out.= '</thead>';
			$html_out.= '</table>';
			
			$html_out.= '<br/>';
			
			$html_out.= '<table cellspacing="0" cellpadding="0" class="fz60 bd">';
			$html_out.= '<thead>';
				$html_out.= '<tr>';
					$html_out .= '<th class="bd pd wd3">NO</th>';
					$html_out .= '<th class="bd pd wd8">KODE</th>';
					$html_out .= '<th class="bd pd wd8">KELOMPOK AKUN</th>';
					$html_out .= '<th class="bd pd">URAIAN</th>';
					$html_out .= '<th class="bd pd wd10">JUMLAH YANG DIMINTAKAN</th>';
					$html_out .= '<th class="bd pd wd7">Gol.</th>';
					$html_out .= '<th class="bd pd wd7">PPN</th>';
					$html_out .= '<th class="bd pd wd7">PPh21</th>';
					$html_out .= '<th class="bd pd wd7">PPh22</th>';
					$html_out .= '<th class="bd pd wd7">PPh23</th>';
					//~ $html_out .= '<th class="bd pd wd7">PPh24</th>';
				$html_out.= '</tr>';
				$html_out.= '<tr>';
					$html_out .= '<th class="fz60 bd">1</th>';
					$html_out .= '<th class="fz60 bd">2</th>';
					$html_out .= '<th class="fz60 bd">3</th>';
					$html_out .= '<th class="fz60 bd">4</th>';
					$html_out .= '<th class="fz60 bd">5</th>';
					$html_out .= '<th class="fz60 bd">6</th>';
					$html_out .= '<th class="fz60 bd">7</th>';
					$html_out .= '<th class="fz60 bd">8</th>';
					$html_out .= '<th class="fz60 bd">9</th>';
					$html_out .= '<th class="fz60 bd">10</th>';
				$html_out.= '</tr>';
			$html_out.= '</thead>';
			$html_out.= '<tbody>';
				$html_out.= '<tr>';
					$html_out.= '<td class="bdlr vt fwb">&nbsp;</td>';
					$html_out.= '<td class="bdlr vt tc fwb">'.$rows_program[0]->kode.'</td>';
					$html_out.= '<td class="bdlr vt tc fwb">&nbsp;</td>';
					$html_out.= '<td class="bdlr vt tl fwb">'.$rows_program[0]->nmprogram.'</td>';
					$html_out.= '<td class="bdlr vt tr fwb">'.number_format($rows_program[0]->nilai,0,',','.').'</td>';
					$html_out.= '<td class="bdlr vt fwb">&nbsp;</td>';
					$html_out.= '<td class="bdlr vt fwb">&nbsp;</td>';
					$html_out.= '<td class="bdlr vt fwb">&nbsp;</td>';
					$html_out.= '<td class="bdlr vt fwb">&nbsp;</td>';
					$html_out.= '<td class="bdlr vt fwb">&nbsp;</td>';
				$html_out.= '</tr>';
			
			foreach($rows_akun as $ra) {
				$html_out.= '<tr>';
					$html_out.= '<td class="bdlr fwb">&nbsp;</td>';
					$html_out.= '<td class="bdlr fwb">&nbsp;</td>';
					$html_out.= '<td class="bdlr tr fwb">'.$ra->kdakun.'</td>';
					$html_out.= '<td class="bdlr tl fwb">'.$ra->nmakun.'</td>';
					$html_out.= '<td class="bdlr tr fwb">'.number_format($ra->nilai,0,',','.').'</td>';
					$html_out.= '<td class="bdlr tr fwb">&nbsp;</td>';
					$html_out.= '<td class="bdlr tr fwb">'.number_format($ra->ppn,0,',','.').'</td>';
					$html_out.= '<td class="bdlr tr fwb">'.number_format($ra->pph_21,0,',','.').'</td>';
					$html_out.= '<td class="bdlr tr fwb">'.number_format($ra->pph_22,0,',','.').'</td>';
					$html_out.= '<td class="bdlr tr fwb">'.number_format($ra->pph_23,0,',','.').'</td>';
					//~ $html_out.= '<td class="bdlr tr fwb">'.number_format($ra->pph_24,0,',','.').'</td>';
				$html_out.= '</tr>';
				
				foreach(rows_uraian($ra->id_rko, $ra->kdakun) as $ru) {
					$html_out.= '<tr>';
						$html_out.= '<td class="bdlr ">&nbsp;</td>';
						$html_out.= '<td class="bdlr ">&nbsp;</td>';
						$html_out.= '<td class="bdlr tr">&nbsp;</td>';
						$html_out.= '<td class="bdlr tl">&nbsp;&nbsp; - &nbsp;'.$ru->uraian.'</td>';
						$html_out.= '<td class="bdlr tr">'.number_format($ru->nilai,0,',','.').'</td>';
						$html_out.= '<td class="bdlr tc">'.$ru->kdgol.'</td>';
						$html_out.= '<td class="bdlr tr">'.number_format($ru->ppn,0,',','.').'</td>';
						$html_out.= '<td class="bdlr tr">'.number_format($ru->pph_21,0,',','.').'</td>';
						$html_out.= '<td class="bdlr tr">'.number_format($ru->pph_22,0,',','.').'</td>';
						$html_out.= '<td class="bdlr tr">'.number_format($ru->pph_23,0,',','.').'</td>';
						//~ $html_out.= '<td class="bdlr tr">'.number_format($ru->pph_24,0,',','.').'</td>';
					$html_out.= '</tr>';
				}
			}
			
			$html_out.= '<tr>';
				$html_out.= '<td class="pd bd fwb">&nbsp;</td>';
				$html_out.= '<td class="pd bd fwb">&nbsp;</td>';
				$html_out.= '<td colspan="2" class="pd bd fwb">JUMLAH</td>';
				$html_out.= '<td class="pd bd tr fwb">'.number_format($rows_program[0]->nilai,0,',','.').'</td>';
				$html_out.= '<td class="pd bd tr fwb">&nbsp;</td>';
				$html_out.= '<td class="pd bd tr fwb">'.number_format($rows_program[0]->ppn,0,',','.').'</td>';
				$html_out.= '<td class="pd bd tr fwb">'.number_format($rows_program[0]->pph_21,0,',','.').'</td>';
				$html_out.= '<td class="pd bd tr fwb">'.number_format($rows_program[0]->pph_22,0,',','.').'</td>';
				$html_out.= '<td class="pd bd tr fwb">'.number_format($rows_program[0]->pph_23,0,',','.').'</td>';
				//~ $html_out.= '<td class="pd bd tr fwb">'.number_format($rows_program[0]->pph_24,0,',','.').'</td>';
			$html_out.= '</tr>';
			
			$html_out.= '</tbody>';
			$html_out.= '</table>';
			$obj_ppk = HTMLController::refPPK();
			$obj_bpp = HTMLController::refBPP();
			
			if($rows_program[0]->is_sekre=='1'){
				
				$html_out.= '<table class="fz60">
							<tbody>
								<tr>
									<td colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td class="">&nbsp;</td>
									<td class="">&nbsp;</td>
									<td class="">&nbsp;</td>
									<td class="">Jakarta, '.HTMLController::today().'</td>
								</tr>
								<tr>
									<td class="">Pejabat Pembuat Komitmen</td>
									<td class="">Bendahara Pengeluaran Pembantu</td>
									<td class="">Pelaksana Kegiatan II</td>
									<td class="">Pelaksana Kegiatan I</td>
								</tr>
								<tr>
									<td class="" colspan="4">&nbsp;</td>
								</tr>
								<tr>
									<td class="" colspan="4">&nbsp;</td>
								</tr>
								<tr>
									<td class="" colspan="4">&nbsp;</td>
								</tr>
								<tr>
									<td class="wd30">'.$rows_program[0]->nama_ppk.'</td>
									<td class="wd30">'.$rows_program[0]->nama_bpp.'</td>
									<td class="wd30">'.$rows_program[0]->nama_pk2.'</td>
									<td class="wd30">'.$rows_program[0]->nama_pk1.'</td>
								</tr>
								<tr>
									<td class="wd30">NIP '.$rows_program[0]->nip_ppk.'</td>
									<td class="wd30">NIP '.$rows_program[0]->nip_bpp.'</td>
									<td class="wd30">NIP '.$rows_program[0]->nip_pk2.'</td>
									<td class="wd30">NIP '.$rows_program[0]->nip_pk1.'</td>
								</tr>
							</tbody>
						</table>';
				
			}
			else{
				
				$html_out.= '<table class="fz60">
							<tbody>
								<tr>
									<td colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td class="">&nbsp;</td>
									<td class="">&nbsp;</td>
									<td class="">&nbsp;</td>
									<td class="">Jakarta, '.HTMLController::today().'</td>
								</tr>
								<tr>
									<td class="">Pejabat Pembuat Komitmen</td>
									<td class="">&nbsp;</td>
									<td class="">&nbsp;</td>
									<td class="">Bendahara Pengeluaran Pembantu</td>
								</tr>
								<tr>
									<td class="" colspan="4">&nbsp;</td>
								</tr>
								<tr>
									<td class="" colspan="4">&nbsp;</td>
								</tr>
								<tr>
									<td class="" colspan="4">&nbsp;</td>
								</tr>
								<tr>
									<td class="wd30">'.$rows_program[0]->nama_ppk.'</td>
									<td class="wd30">&nbsp;</td>
									<td class="wd30">&nbsp;</td>
									<td class="wd30">'.$rows_program[0]->nama_bpp.'</td>
								</tr>
								<tr>
									<td class="wd30">NIP '.$rows_program[0]->nip_ppk.'</td>
									<td class="wd30">&nbsp;</td>
									<td class="wd30">&nbsp;</td>
									<td class="wd30">NIP '.$rows_program[0]->nip_bpp.'</td>
								</tr>
							</tbody>
						</table>';
				
			}
			
			$mpdf = new mPDF("en", "A4", "15");
			$mpdf->SetTitle('Form RKO');
			
			$mpdf->AddPage('L');
			$mpdf->writeHTML($html_out);
			$mpdf->Output('Form_RKO_GUP_'.$id.'.pdf','I');
		
	}
	
}