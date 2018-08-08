<?php namespace App\Http\Controllers;

use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DashboardController extends Controller {

	//index
	public function index()
	{
		try{
			return view('dashboard-baru');
		}
		catch(\Exception $e){
			return 'Koneksi terputus!';
		}
	}
	
	public function dataSarju()
	{
		$lanjut=true;
		if($lanjut){
			$rows = DB::select("
				SELECT 
				  periode,
				  real_rko,
				  (
					real_rko / 
					(SELECT 
					  SUM(paguakhir) pagu 
					FROM
					  d_pagu 
					WHERE lvl = 7 
					  AND kdsatker = '".session('kdsatker')."' 
					  AND thang='".session('tahun')."') * 100
				  ) persen 
				FROM 
				(SELECT 
				  'Triwulan I' periode,
				  SUM(real_rko) real_rko 
				FROM
				  (SELECT 
					xx.periode,
					IFNULL(yy.real_rko, 0) real_rko 
				  FROM
					t_periode xx 
					LEFT JOIN 
					  (SELECT 
						a.periode,
						SUM(b.real_rko) real_rko 
					  FROM
						(SELECT 
						  id,
						  MONTH(tgrko) periode 
						FROM
						  d_rko 
						WHERE thang = '".session('tahun')."' 
						  AND kdsatker = '".session('kdsatker')."') a 
						INNER JOIN 
						  (SELECT 
							id_rko,
							SUM(nilai) real_rko 
						  FROM
							d_rko_pagu2 
						  GROUP BY id_rko) b 
						  ON a.id = b.id_rko 
					  GROUP BY periode) yy 
					  ON xx.periode = yy.periode 
				  ORDER BY periode) c 
				WHERE periode <= 3 
				UNION
				ALL 
				SELECT 
				  'Triwulan II' periode,
				  SUM(real_rko) real_rko 
				FROM
				  (SELECT 
					xx.periode,
					IFNULL(yy.real_rko, 0) real_rko 
				  FROM
					t_periode xx 
					LEFT JOIN 
					  (SELECT 
						a.periode,
						SUM(b.real_rko) real_rko 
					  FROM
						(SELECT 
						  id,
						  MONTH(tgrko) periode 
						FROM
						  d_rko 
						WHERE thang = '".session('tahun')."'
						  AND kdsatker = '".session('kdsatker')."') a 
						INNER JOIN 
						  (SELECT 
							id_rko,
							SUM(nilai) real_rko 
						  FROM
							d_rko_pagu2 
						  GROUP BY id_rko) b 
						  ON a.id = b.id_rko 
					  GROUP BY periode) yy 
					  ON xx.periode = yy.periode 
				  ORDER BY periode) c 
				WHERE periode <= 6 
				UNION
				ALL 
				SELECT 
				  'Triwulan III' periode,
				  SUM(real_rko) real_rko 
				FROM
				  (SELECT 
					xx.periode,
					IFNULL(yy.real_rko, 0) real_rko 
				  FROM
					t_periode xx 
					LEFT JOIN 
					  (SELECT 
						a.periode,
						SUM(b.real_rko) real_rko 
					  FROM
						(SELECT 
						  id,
						  MONTH(tgrko) periode 
						FROM
						  d_rko 
						WHERE thang = '".session('tahun')."'
						  AND kdsatker = '".session('kdsatker')."') a 
						INNER JOIN 
						  (SELECT 
							id_rko,
							SUM(nilai) real_rko 
						  FROM
							d_rko_pagu2 
						  GROUP BY id_rko) b 
						  ON a.id = b.id_rko 
					  GROUP BY periode) yy 
					  ON xx.periode = yy.periode 
				  ORDER BY periode) c 
				WHERE periode <= 9 
				UNION
				ALL 
				SELECT 
				  'Triwulan IV' periode,
				  SUM(real_rko) real_rko 
				FROM
				  (SELECT 
					xx.periode,
					IFNULL(yy.real_rko, 0) real_rko 
				  FROM
					t_periode xx 
					LEFT JOIN 
					  (SELECT 
						a.periode,
						SUM(b.real_rko) real_rko 
					  FROM
						(SELECT 
						  id,
						  MONTH(tgrko) periode 
						FROM
						  d_rko 
						WHERE thang = '".session('tahun')."' 
						  AND kdsatker = '".session('kdsatker')."') a 
						INNER JOIN 
						  (SELECT 
							id_rko,
							SUM(nilai) real_rko 
						  FROM
							d_rko_pagu2 
						  GROUP BY id_rko) b 
						  ON a.id = b.id_rko 
					  GROUP BY periode) yy 
					  ON xx.periode = yy.periode 
				  ORDER BY periode) c 
				WHERE periode <= 12) d
			");
			
			foreach($rows as $row){
				$data['persen'][] = $row->persen;
			}
			
			$rows = DB::select("
				SELECT 
* 
			FROM
			  (SELECT 
				* 
			  FROM
				(SELECT 
				  periode,
				  'Triwulan I' triwulan,
				  aa.rencana,
				  aa.progres 
				FROM
				  (SELECT 
					1 idx,
					periode,
					SUM(rencana) / 
					(SELECT 
					  COUNT(*) JML 
					FROM
					  (SELECT DISTINCT 
						id 
					  FROM
						(SELECT 
						  c.id,
						  c.thang,
						  c.kdsatker,
						  c.kdprogram,
						  c.kdgiat,
						  c.kdoutput,
						  c.periode,
						  c.rencana,
						  IFNULL(d.progres, 0) progres 
						FROM
						  (SELECT 
							a.id,
							a.thang,
							a.kdsatker,
							a.kdprogram,
							a.kdgiat,
							a.kdoutput,
							a.periode,
							IFNULL(b.rencana, 0) rencana,
							a.idx 
						  FROM
							(SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '01' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'01'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '02' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'02'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '03' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'03'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '04' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'04'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '05' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'05'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '06' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'06'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '07' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'07'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '08' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'08'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '09' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'09'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '10' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'10'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '11' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'11'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '12' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'12'
							  ) idx 
							FROM
							  d_target 
							ORDER BY id,
							  periode) a 
							LEFT JOIN 
							  (SELECT 
								id_target,
								periode,
								rencana,
								CONCAT(
								  CAST(
									id_target AS CHAR CHARACTER SET latin1
								  ),
								  periode
								) idx 
							  FROM
								d_target_detil) b 
							  ON a.idx = b.idx) c 
						  LEFT JOIN 
							(SELECT 
							  CONCAT(
								CAST(
								  id_target AS CHAR CHARACTER SET latin1
								),
								periode
							  ) idx,
							  progres 
							FROM
							  d_progres) d 
							ON c.idx = d.idx 
						ORDER BY id,
						  periode) w) v) rencana,
					SUM(progres) progres 
				  FROM
					(SELECT 
					  c.id,
					  c.thang,
					  c.kdsatker,
					  c.kdprogram,
					  c.kdgiat,
					  c.kdoutput,
					  c.periode,
					  c.rencana,
					  IFNULL(d.progres, 0) progres 
					FROM
					  (SELECT 
						a.id,
						a.thang,
						a.kdsatker,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.periode,
						IFNULL(b.rencana, 0) rencana,
						a.idx 
					  FROM
						(SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '01' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'01'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '02' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'02'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '03' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'03'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '04' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'04'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '05' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'05'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '06' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'06'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '07' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'07'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '08' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'08'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '09' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'09'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '10' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'10'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '11' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'11'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '12' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'12'
						  ) idx 
						FROM
						  d_target 
						ORDER BY id,
						  periode) a 
						LEFT JOIN 
						  (SELECT 
							id_target,
							periode,
							rencana,
							CONCAT(
							  CAST(
								id_target AS CHAR CHARACTER SET latin1
							  ),
							  periode
							) idx 
						  FROM
							d_target_detil) b 
						  ON a.idx = b.idx) c 
					  LEFT JOIN 
						(SELECT 
						  CONCAT(
							CAST(
							  id_target AS CHAR CHARACTER SET latin1
							),
							periode
						  ) idx,
						  progres 
						FROM
						  d_progres) d 
						ON c.idx = d.idx 
					ORDER BY id,
					  periode) xx 
				  WHERE kdsatker = '".session('kdsatker')."' 
					AND thang = '".session('tahun')."' 
					AND periode IN ('01', '02', '03') 
				  GROUP BY periode) aa 
				  INNER JOIN 
					(SELECT 
					  1 idx,
					  MAX(progres) progres 
					FROM
					  (SELECT 
						periode,
						SUM(rencana) / 
						(SELECT 
						  COUNT(*) JML 
						FROM
						  (SELECT DISTINCT 
							id 
						  FROM
							(SELECT 
							  c.id,
							  c.thang,
							  c.kdsatker,
							  c.kdprogram,
							  c.kdgiat,
							  c.kdoutput,
							  c.periode,
							  c.rencana,
							  IFNULL(d.progres, 0) progres 
							FROM
							  (SELECT 
								a.id,
								a.thang,
								a.kdsatker,
								a.kdprogram,
								a.kdgiat,
								a.kdoutput,
								a.periode,
								IFNULL(b.rencana, 0) rencana,
								a.idx 
							  FROM
								(SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '01' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'01'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '02' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'02'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '03' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'03'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '04' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'04'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '05' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'05'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '06' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'06'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '07' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'07'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '08' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'08'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '09' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'09'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '10' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'10'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '11' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'11'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '12' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'12'
								  ) idx 
								FROM
								  d_target 
								ORDER BY id,
								  periode) a 
								LEFT JOIN 
								  (SELECT 
									id_target,
									periode,
									rencana,
									CONCAT(
									  CAST(
										id_target AS CHAR CHARACTER SET latin1
									  ),
									  periode
									) idx 
								  FROM
									d_target_detil) b 
								  ON a.idx = b.idx) c 
							  LEFT JOIN 
								(SELECT 
								  CONCAT(
									CAST(
									  id_target AS CHAR CHARACTER SET latin1
									),
									periode
								  ) idx,
								  progres 
								FROM
								  d_progres) d 
								ON c.idx = d.idx 
							ORDER BY id,
							  periode) w) v) rencana,
						SUM(progres) progres 
					  FROM
						(SELECT 
						  c.id,
						  c.thang,
						  c.kdsatker,
						  c.kdprogram,
						  c.kdgiat,
						  c.kdoutput,
						  c.periode,
						  c.rencana,
						  IFNULL(d.progres, 0) progres 
						FROM
						  (SELECT 
							a.id,
							a.thang,
							a.kdsatker,
							a.kdprogram,
							a.kdgiat,
							a.kdoutput,
							a.periode,
							IFNULL(b.rencana, 0) rencana,
							a.idx 
						  FROM
							(SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '01' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'01'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '02' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'02'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '03' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'03'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '04' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'04'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '05' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'05'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '06' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'06'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '07' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'07'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '08' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'08'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '09' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'09'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '10' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'10'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '11' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'11'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '12' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'12'
							  ) idx 
							FROM
							  d_target 
							ORDER BY id,
							  periode) a 
							LEFT JOIN 
							  (SELECT 
								id_target,
								periode,
								rencana,
								CONCAT(
								  CAST(
									id_target AS CHAR CHARACTER SET latin1
								  ),
								  periode
								) idx 
							  FROM
								d_target_detil) b 
							  ON a.idx = b.idx) c 
						  LEFT JOIN 
							(SELECT 
							  CONCAT(
								CAST(
								  id_target AS CHAR CHARACTER SET latin1
								),
								periode
							  ) idx,
							  progres 
							FROM
							  d_progres) d 
							ON c.idx = d.idx 
						ORDER BY id,
						  periode) xx 
					  WHERE kdsatker = '".session('kdsatker')."' 
						AND thang = '".session('tahun')."' 
						AND periode IN ('01', '02', '03') 
					  GROUP BY periode) vv 
					GROUP BY idx) bb 
					ON aa.idx = bb.idx 
					AND aa.progres = bb.progres 
				ORDER BY periode DESC) hh 
			  LIMIT 1) ii 
			UNION
			ALL 
			SELECT 
			  * 
			FROM
			  (SELECT 
				* 
			  FROM
				(SELECT 
				  periode,
				  'Triwulan II' triwulan,
				  aa.rencana,
				  aa.progres 
				FROM
				  (SELECT 
					1 idx,
					periode,
					SUM(rencana) / 
					(SELECT 
					  COUNT(*) JML 
					FROM
					  (SELECT DISTINCT 
						id 
					  FROM
						(SELECT 
						  c.id,
						  c.thang,
						  c.kdsatker,
						  c.kdprogram,
						  c.kdgiat,
						  c.kdoutput,
						  c.periode,
						  c.rencana,
						  IFNULL(d.progres, 0) progres 
						FROM
						  (SELECT 
							a.id,
							a.thang,
							a.kdsatker,
							a.kdprogram,
							a.kdgiat,
							a.kdoutput,
							a.periode,
							IFNULL(b.rencana, 0) rencana,
							a.idx 
						  FROM
							(SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '01' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'01'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '02' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'02'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '03' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'03'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '04' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'04'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '05' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'05'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '06' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'06'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '07' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'07'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '08' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'08'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '09' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'09'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '10' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'10'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '11' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'11'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '12' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'12'
							  ) idx 
							FROM
							  d_target 
							ORDER BY id,
							  periode) a 
							LEFT JOIN 
							  (SELECT 
								id_target,
								periode,
								rencana,
								CONCAT(
								  CAST(
									id_target AS CHAR CHARACTER SET latin1
								  ),
								  periode
								) idx 
							  FROM
								d_target_detil) b 
							  ON a.idx = b.idx) c 
						  LEFT JOIN 
							(SELECT 
							  CONCAT(
								CAST(
								  id_target AS CHAR CHARACTER SET latin1
								),
								periode
							  ) idx,
							  progres 
							FROM
							  d_progres) d 
							ON c.idx = d.idx 
						ORDER BY id,
						  periode) w) v) rencana,
					SUM(progres) progres 
				  FROM
					(SELECT 
					  c.id,
					  c.thang,
					  c.kdsatker,
					  c.kdprogram,
					  c.kdgiat,
					  c.kdoutput,
					  c.periode,
					  c.rencana,
					  IFNULL(d.progres, 0) progres 
					FROM
					  (SELECT 
						a.id,
						a.thang,
						a.kdsatker,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.periode,
						IFNULL(b.rencana, 0) rencana,
						a.idx 
					  FROM
						(SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '01' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'01'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '02' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'02'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '03' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'03'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '04' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'04'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '05' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'05'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '06' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'06'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '07' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'07'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '08' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'08'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '09' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'09'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '10' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'10'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '11' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'11'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '12' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'12'
						  ) idx 
						FROM
						  d_target 
						ORDER BY id,
						  periode) a 
						LEFT JOIN 
						  (SELECT 
							id_target,
							periode,
							rencana,
							CONCAT(
							  CAST(
								id_target AS CHAR CHARACTER SET latin1
							  ),
							  periode
							) idx 
						  FROM
							d_target_detil) b 
						  ON a.idx = b.idx) c 
					  LEFT JOIN 
						(SELECT 
						  CONCAT(
							CAST(
							  id_target AS CHAR CHARACTER SET latin1
							),
							periode
						  ) idx,
						  progres 
						FROM
						  d_progres) d 
						ON c.idx = d.idx 
					ORDER BY id,
					  periode) xx 
				  WHERE kdsatker = '".session('kdsatker')."' 
					AND thang = '".session('tahun')."' 
					AND periode IN ('04', '05', '06') 
				  GROUP BY periode) aa 
				  INNER JOIN 
					(SELECT 
					  1 idx,
					  MAX(progres) progres 
					FROM
					  (SELECT 
						periode,
						SUM(rencana) / 
						(SELECT 
						  COUNT(*) JML 
						FROM
						  (SELECT DISTINCT 
							id 
						  FROM
							(SELECT 
							  c.id,
							  c.thang,
							  c.kdsatker,
							  c.kdprogram,
							  c.kdgiat,
							  c.kdoutput,
							  c.periode,
							  c.rencana,
							  IFNULL(d.progres, 0) progres 
							FROM
							  (SELECT 
								a.id,
								a.thang,
								a.kdsatker,
								a.kdprogram,
								a.kdgiat,
								a.kdoutput,
								a.periode,
								IFNULL(b.rencana, 0) rencana,
								a.idx 
							  FROM
								(SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '01' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'01'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '02' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'02'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '03' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'03'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '04' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'04'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '05' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'05'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '06' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'06'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '07' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'07'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '08' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'08'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '09' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'09'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '10' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'10'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '11' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'11'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '12' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'12'
								  ) idx 
								FROM
								  d_target 
								ORDER BY id,
								  periode) a 
								LEFT JOIN 
								  (SELECT 
									id_target,
									periode,
									rencana,
									CONCAT(
									  CAST(
										id_target AS CHAR CHARACTER SET latin1
									  ),
									  periode
									) idx 
								  FROM
									d_target_detil) b 
								  ON a.idx = b.idx) c 
							  LEFT JOIN 
								(SELECT 
								  CONCAT(
									CAST(
									  id_target AS CHAR CHARACTER SET latin1
									),
									periode
								  ) idx,
								  progres 
								FROM
								  d_progres) d 
								ON c.idx = d.idx 
							ORDER BY id,
							  periode) w) v) rencana,
						SUM(progres) progres 
					  FROM
						(SELECT 
						  c.id,
						  c.thang,
						  c.kdsatker,
						  c.kdprogram,
						  c.kdgiat,
						  c.kdoutput,
						  c.periode,
						  c.rencana,
						  IFNULL(d.progres, 0) progres 
						FROM
						  (SELECT 
							a.id,
							a.thang,
							a.kdsatker,
							a.kdprogram,
							a.kdgiat,
							a.kdoutput,
							a.periode,
							IFNULL(b.rencana, 0) rencana,
							a.idx 
						  FROM
							(SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '01' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'01'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '02' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'02'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '03' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'03'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '04' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'04'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '05' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'05'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '06' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'06'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '07' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'07'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '08' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'08'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '09' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'09'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '10' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'10'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '11' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'11'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '12' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'12'
							  ) idx 
							FROM
							  d_target 
							ORDER BY id,
							  periode) a 
							LEFT JOIN 
							  (SELECT 
								id_target,
								periode,
								rencana,
								CONCAT(
								  CAST(
									id_target AS CHAR CHARACTER SET latin1
								  ),
								  periode
								) idx 
							  FROM
								d_target_detil) b 
							  ON a.idx = b.idx) c 
						  LEFT JOIN 
							(SELECT 
							  CONCAT(
								CAST(
								  id_target AS CHAR CHARACTER SET latin1
								),
								periode
							  ) idx,
							  progres 
							FROM
							  d_progres) d 
							ON c.idx = d.idx 
						ORDER BY id,
						  periode) xx 
					  WHERE kdsatker = '".session('kdsatker')."' 
						AND thang = '".session('tahun')."' 
						AND periode IN ('04', '05', '06') 
					  GROUP BY periode) vv 
					GROUP BY idx) bb 
					ON aa.idx = bb.idx 
					AND aa.progres = bb.progres 
				ORDER BY periode DESC) hh 
			  LIMIT 1) ii 
			UNION
			ALL 
			SELECT 
			  * 
			FROM
			  (SELECT 
				* 
			  FROM
				(SELECT 
				  periode,
				  'Triwulan III' triwulan,
				  aa.rencana,
				  aa.progres 
				FROM
				  (SELECT 
					3 idx,
					periode,
					SUM(rencana) / 
					(SELECT 
					  COUNT(*) JML 
					FROM
					  (SELECT DISTINCT 
						id 
					  FROM
						(SELECT 
						  c.id,
						  c.thang,
						  c.kdsatker,
						  c.kdprogram,
						  c.kdgiat,
						  c.kdoutput,
						  c.periode,
						  c.rencana,
						  IFNULL(d.progres, 0) progres 
						FROM
						  (SELECT 
							a.id,
							a.thang,
							a.kdsatker,
							a.kdprogram,
							a.kdgiat,
							a.kdoutput,
							a.periode,
							IFNULL(b.rencana, 0) rencana,
							a.idx 
						  FROM
							(SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '01' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'01'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '02' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'02'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '03' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'03'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '04' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'04'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '05' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'05'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '06' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'06'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '07' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'07'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '08' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'08'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '09' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'09'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '10' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'10'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '11' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'11'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '12' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'12'
							  ) idx 
							FROM
							  d_target 
							ORDER BY id,
							  periode) a 
							LEFT JOIN 
							  (SELECT 
								id_target,
								periode,
								rencana,
								CONCAT(
								  CAST(
									id_target AS CHAR CHARACTER SET latin1
								  ),
								  periode
								) idx 
							  FROM
								d_target_detil) b 
							  ON a.idx = b.idx) c 
						  LEFT JOIN 
							(SELECT 
							  CONCAT(
								CAST(
								  id_target AS CHAR CHARACTER SET latin1
								),
								periode
							  ) idx,
							  progres 
							FROM
							  d_progres) d 
							ON c.idx = d.idx 
						ORDER BY id,
						  periode) w) v) rencana,
					SUM(progres) progres 
				  FROM
					(SELECT 
					  c.id,
					  c.thang,
					  c.kdsatker,
					  c.kdprogram,
					  c.kdgiat,
					  c.kdoutput,
					  c.periode,
					  c.rencana,
					  IFNULL(d.progres, 0) progres 
					FROM
					  (SELECT 
						a.id,
						a.thang,
						a.kdsatker,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.periode,
						IFNULL(b.rencana, 0) rencana,
						a.idx 
					  FROM
						(SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '01' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'01'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '02' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'02'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '03' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'03'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '04' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'04'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '05' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'05'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '06' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'06'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '07' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'07'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '08' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'08'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '09' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'09'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '10' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'10'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '11' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'11'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '12' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'12'
						  ) idx 
						FROM
						  d_target 
						ORDER BY id,
						  periode) a 
						LEFT JOIN 
						  (SELECT 
							id_target,
							periode,
							rencana,
							CONCAT(
							  CAST(
								id_target AS CHAR CHARACTER SET latin1
							  ),
							  periode
							) idx 
						  FROM
							d_target_detil) b 
						  ON a.idx = b.idx) c 
					  LEFT JOIN 
						(SELECT 
						  CONCAT(
							CAST(
							  id_target AS CHAR CHARACTER SET latin1
							),
							periode
						  ) idx,
						  progres 
						FROM
						  d_progres) d 
						ON c.idx = d.idx 
					ORDER BY id,
					  periode) xx 
				  WHERE kdsatker = '".session('kdsatker')."' 
					AND thang = '".session('tahun')."' 
					AND periode IN ('07', '08', '09') 
				  GROUP BY periode) aa 
				  INNER JOIN 
					(SELECT 
					  3 idx,
					  MAX(progres) progres 
					FROM
					  (SELECT 
						periode,
						SUM(rencana) / 
						(SELECT 
						  COUNT(*) JML 
						FROM
						  (SELECT DISTINCT 
							id 
						  FROM
							(SELECT 
							  c.id,
							  c.thang,
							  c.kdsatker,
							  c.kdprogram,
							  c.kdgiat,
							  c.kdoutput,
							  c.periode,
							  c.rencana,
							  IFNULL(d.progres, 0) progres 
							FROM
							  (SELECT 
								a.id,
								a.thang,
								a.kdsatker,
								a.kdprogram,
								a.kdgiat,
								a.kdoutput,
								a.periode,
								IFNULL(b.rencana, 0) rencana,
								a.idx 
							  FROM
								(SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '01' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'01'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '02' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'02'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '03' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'03'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '04' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'04'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '05' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'05'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '06' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'06'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '07' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'07'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '08' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'08'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '09' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'09'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '10' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'10'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '11' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'11'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '12' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'12'
								  ) idx 
								FROM
								  d_target 
								ORDER BY id,
								  periode) a 
								LEFT JOIN 
								  (SELECT 
									id_target,
									periode,
									rencana,
									CONCAT(
									  CAST(
										id_target AS CHAR CHARACTER SET latin1
									  ),
									  periode
									) idx 
								  FROM
									d_target_detil) b 
								  ON a.idx = b.idx) c 
							  LEFT JOIN 
								(SELECT 
								  CONCAT(
									CAST(
									  id_target AS CHAR CHARACTER SET latin1
									),
									periode
								  ) idx,
								  progres 
								FROM
								  d_progres) d 
								ON c.idx = d.idx 
							ORDER BY id,
							  periode) w) v) rencana,
						SUM(progres) progres 
					  FROM
						(SELECT 
						  c.id,
						  c.thang,
						  c.kdsatker,
						  c.kdprogram,
						  c.kdgiat,
						  c.kdoutput,
						  c.periode,
						  c.rencana,
						  IFNULL(d.progres, 0) progres 
						FROM
						  (SELECT 
							a.id,
							a.thang,
							a.kdsatker,
							a.kdprogram,
							a.kdgiat,
							a.kdoutput,
							a.periode,
							IFNULL(b.rencana, 0) rencana,
							a.idx 
						  FROM
							(SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '01' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'01'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '02' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'02'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '03' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'03'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '04' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'04'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '05' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'05'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '06' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'06'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '07' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'07'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '08' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'08'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '09' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'09'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '10' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'10'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '11' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'11'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '12' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'12'
							  ) idx 
							FROM
							  d_target 
							ORDER BY id,
							  periode) a 
							LEFT JOIN 
							  (SELECT 
								id_target,
								periode,
								rencana,
								CONCAT(
								  CAST(
									id_target AS CHAR CHARACTER SET latin1
								  ),
								  periode
								) idx 
							  FROM
								d_target_detil) b 
							  ON a.idx = b.idx) c 
						  LEFT JOIN 
							(SELECT 
							  CONCAT(
								CAST(
								  id_target AS CHAR CHARACTER SET latin1
								),
								periode
							  ) idx,
							  progres 
							FROM
							  d_progres) d 
							ON c.idx = d.idx 
						ORDER BY id,
						  periode) xx 
					  WHERE kdsatker = '".session('kdsatker')."' 
						AND thang = '".session('tahun')."'
						AND periode IN ('07', '08', '09') 
					  GROUP BY periode) vv 
					GROUP BY idx) bb 
					ON aa.idx = bb.idx 
					AND aa.progres = bb.progres 
				ORDER BY periode DESC) hh 
			  LIMIT 1) ii 
			UNION
			ALL 
			SELECT 
			  * 
			FROM
			  (SELECT 
				* 
			  FROM
				(SELECT 
				  periode,
				  'Triwulan IV' triwulan,
				  aa.rencana,
				  aa.progres 
				FROM
				  (SELECT 
					4 idx,
					periode,
					SUM(rencana) / 
					(SELECT 
					  COUNT(*) JML 
					FROM
					  (SELECT DISTINCT 
						id 
					  FROM
						(SELECT 
						  c.id,
						  c.thang,
						  c.kdsatker,
						  c.kdprogram,
						  c.kdgiat,
						  c.kdoutput,
						  c.periode,
						  c.rencana,
						  IFNULL(d.progres, 0) progres 
						FROM
						  (SELECT 
							a.id,
							a.thang,
							a.kdsatker,
							a.kdprogram,
							a.kdgiat,
							a.kdoutput,
							a.periode,
							IFNULL(b.rencana, 0) rencana,
							a.idx 
						  FROM
							(SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '01' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'01'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '02' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'02'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '03' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'03'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '04' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'04'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '05' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'05'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '06' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'06'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '07' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'07'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '08' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'08'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '09' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'09'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '10' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'10'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '11' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'11'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '12' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'12'
							  ) idx 
							FROM
							  d_target 
							ORDER BY id,
							  periode) a 
							LEFT JOIN 
							  (SELECT 
								id_target,
								periode,
								rencana,
								CONCAT(
								  CAST(
									id_target AS CHAR CHARACTER SET latin1
								  ),
								  periode
								) idx 
							  FROM
								d_target_detil) b 
							  ON a.idx = b.idx) c 
						  LEFT JOIN 
							(SELECT 
							  CONCAT(
								CAST(
								  id_target AS CHAR CHARACTER SET latin1
								),
								periode
							  ) idx,
							  progres 
							FROM
							  d_progres) d 
							ON c.idx = d.idx 
						ORDER BY id,
						  periode) w) v) rencana,
					SUM(progres) progres 
				  FROM
					(SELECT 
					  c.id,
					  c.thang,
					  c.kdsatker,
					  c.kdprogram,
					  c.kdgiat,
					  c.kdoutput,
					  c.periode,
					  c.rencana,
					  IFNULL(d.progres, 0) progres 
					FROM
					  (SELECT 
						a.id,
						a.thang,
						a.kdsatker,
						a.kdprogram,
						a.kdgiat,
						a.kdoutput,
						a.periode,
						IFNULL(b.rencana, 0) rencana,
						a.idx 
					  FROM
						(SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '01' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'01'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '02' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'02'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '03' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'03'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '04' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'04'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '05' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'05'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '06' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'06'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '07' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'07'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '08' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'08'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '09' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'09'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '10' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'10'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '11' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'11'
						  ) idx 
						FROM
						  d_target 
						UNION
						ALL 
						SELECT 
						  id,
						  thang,
						  kdsatker,
						  kdprogram,
						  kdgiat,
						  kdoutput,
						  '12' periode,
						  CONCAT(
							CAST(id AS CHAR CHARACTER SET latin1),
							'12'
						  ) idx 
						FROM
						  d_target 
						ORDER BY id,
						  periode) a 
						LEFT JOIN 
						  (SELECT 
							id_target,
							periode,
							rencana,
							CONCAT(
							  CAST(
								id_target AS CHAR CHARACTER SET latin1
							  ),
							  periode
							) idx 
						  FROM
							d_target_detil) b 
						  ON a.idx = b.idx) c 
					  LEFT JOIN 
						(SELECT 
						  CONCAT(
							CAST(
							  id_target AS CHAR CHARACTER SET latin1
							),
							periode
						  ) idx,
						  progres 
						FROM
						  d_progres) d 
						ON c.idx = d.idx 
					ORDER BY id,
					  periode) xx 
				  WHERE kdsatker = '".session('kdsatker')."'
					AND thang = '".session('tahun')."' 
					AND periode IN ('10', '11', '12') 
				  GROUP BY periode) aa 
				  INNER JOIN 
					(SELECT 
					  4 idx,
					  MAX(progres) progres 
					FROM
					  (SELECT 
						periode,
						SUM(rencana) / 
						(SELECT 
						  COUNT(*) JML 
						FROM
						  (SELECT DISTINCT 
							id 
						  FROM
							(SELECT 
							  c.id,
							  c.thang,
							  c.kdsatker,
							  c.kdprogram,
							  c.kdgiat,
							  c.kdoutput,
							  c.periode,
							  c.rencana,
							  IFNULL(d.progres, 0) progres 
							FROM
							  (SELECT 
								a.id,
								a.thang,
								a.kdsatker,
								a.kdprogram,
								a.kdgiat,
								a.kdoutput,
								a.periode,
								IFNULL(b.rencana, 0) rencana,
								a.idx 
							  FROM
								(SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '01' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'01'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '02' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'02'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '03' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'03'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '04' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'04'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '05' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'05'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '06' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'06'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '07' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'07'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '08' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'08'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '09' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'09'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '10' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'10'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '11' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'11'
								  ) idx 
								FROM
								  d_target 
								UNION
								ALL 
								SELECT 
								  id,
								  thang,
								  kdsatker,
								  kdprogram,
								  kdgiat,
								  kdoutput,
								  '12' periode,
								  CONCAT(
									CAST(id AS CHAR CHARACTER SET latin1),
									'12'
								  ) idx 
								FROM
								  d_target 
								ORDER BY id,
								  periode) a 
								LEFT JOIN 
								  (SELECT 
									id_target,
									periode,
									rencana,
									CONCAT(
									  CAST(
										id_target AS CHAR CHARACTER SET latin1
									  ),
									  periode
									) idx 
								  FROM
									d_target_detil) b 
								  ON a.idx = b.idx) c 
							  LEFT JOIN 
								(SELECT 
								  CONCAT(
									CAST(
									  id_target AS CHAR CHARACTER SET latin1
									),
									periode
								  ) idx,
								  progres 
								FROM
								  d_progres) d 
								ON c.idx = d.idx 
							ORDER BY id,
							  periode) w) v) rencana,
						SUM(progres) progres 
					  FROM
						(SELECT 
						  c.id,
						  c.thang,
						  c.kdsatker,
						  c.kdprogram,
						  c.kdgiat,
						  c.kdoutput,
						  c.periode,
						  c.rencana,
						  IFNULL(d.progres, 0) progres 
						FROM
						  (SELECT 
							a.id,
							a.thang,
							a.kdsatker,
							a.kdprogram,
							a.kdgiat,
							a.kdoutput,
							a.periode,
							IFNULL(b.rencana, 0) rencana,
							a.idx 
						  FROM
							(SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '01' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'01'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '02' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'02'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '03' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'03'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '04' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'04'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '05' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'05'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '06' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'06'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '07' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'07'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '08' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'08'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '09' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'09'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '10' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'10'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '11' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'11'
							  ) idx 
							FROM
							  d_target 
							UNION
							ALL 
							SELECT 
							  id,
							  thang,
							  kdsatker,
							  kdprogram,
							  kdgiat,
							  kdoutput,
							  '12' periode,
							  CONCAT(
								CAST(id AS CHAR CHARACTER SET latin1),
								'12'
							  ) idx 
							FROM
							  d_target 
							ORDER BY id,
							  periode) a 
							LEFT JOIN 
							  (SELECT 
								id_target,
								periode,
								rencana,
								CONCAT(
								  CAST(
									id_target AS CHAR CHARACTER SET latin1
								  ),
								  periode
								) idx 
							  FROM
								d_target_detil) b 
							  ON a.idx = b.idx) c 
						  LEFT JOIN 
							(SELECT 
							  CONCAT(
								CAST(
								  id_target AS CHAR CHARACTER SET latin1
								),
								periode
							  ) idx,
							  progres 
							FROM
							  d_progres) d 
							ON c.idx = d.idx 
						ORDER BY id,
						  periode) xx 
					  WHERE kdsatker = '".session('kdsatker')."' 
						AND thang = '".session('tahun')."' 
						AND periode IN ('10', '11', '12') 
					  GROUP BY periode) vv 
					GROUP BY idx) bb 
					ON aa.idx = bb.idx 
					AND aa.progres = bb.progres 
				ORDER BY periode DESC) hh 
			  LIMIT 1) ii 
			");
			
			$data['rencana'] = array();
			$data['progres'] = array();
			foreach($rows as $row){
				$data['rencana'][] = $row->rencana;
				$data['progres'][] = $row->progres;
			}
			
			return view('dashboard', $data);
		}
		else{
			return 'Gagal';
		}
	}
	
	public function dataRealRKO()
	{
		/*$rows = DB::select("
				SELECT 
				  ROUND(IFNULL(SUM(nilai)/1000000,0), 1) JML_REAL_RKO
				FROM
				  d_rko_pagu2
		");*/
		
		$rows = DB::select("
			SELECT	ROUND(IFNULL(SUM(a.nilai)/1000000,0),1) AS JML_REAL_RKO
			FROM d_rko_pagu2 a
			LEFT OUTER JOIN d_rko b ON(a.id_rko=b.id)
			LEFT OUTER JOIN(
				SELECT	a.id_rko,
					b.nourut
				FROM(
					SELECT	id_rko,
						MAX(id) AS max_id
					FROM d_rko_status
					GROUP BY id_rko
				) a
				LEFT OUTER JOIN d_rko_status b ON(a.max_id=b.id)
			) c ON(a.id_rko=c.id_rko)
			WHERE b.kdsatker=? AND b.thang=? AND c.nourut>=4
		",[
			session('kdsatker'),
			session('tahun')
		]);
			
		return $rows[0]->JML_REAL_RKO;
		
	}
	
	public function dataRealTransaksi()
	{
		$rows = DB::select("
			SELECT	ROUND(IFNULL(SUM(a.nilai)/1000000,0),1) AS JML_REAL_RKO
			FROM d_rko_pagu2 a
			LEFT OUTER JOIN d_rko b ON(a.id_rko=b.id)
			LEFT OUTER JOIN(
				SELECT	a.id_rko,
					b.nourut
				FROM(
					SELECT	id_rko,
						MAX(id) AS max_id
					FROM d_rko_status
					GROUP BY id_rko
				) a
				LEFT OUTER JOIN d_rko_status b ON(a.max_id=b.id)
			) c ON(a.id_rko=c.id_rko)
			WHERE b.kdsatker=? AND b.thang=? AND c.nourut>=4
		",[
			session('kdsatker'),
			session('tahun')
		]);
			
		return $rows[0]->JML_REAL_RKO;
		
	}
	
	public function dataJmlRKO()
	{
		
		$rows = DB::select("
				SELECT 
				  IFNULL(COUNT(*), 0) JML_RKO 
				FROM
				  d_rko");
			
		return $rows[0]->JML_RKO;
		
	}
	
	public function dataJmlTransaksi()
	{
		
		$rows = DB::select("
				SELECT 
				  IFNULL(COUNT(*), 0) JML_TRAN 
				FROM
				  d_transaksi");
			
		return $rows[0]->JML_TRAN;
		
	}
	
	public function dataJmlEvaluasi()
	{
		
		$rows = DB::select("
				SELECT 
				  IFNULL(COUNT(*), 0) JML_EVAL 
				FROM
				  d_evaluasi");
			
		return $rows[0]->JML_EVAL;
		
	}
	
	public function capaian()
	{
		$rows = DB::select("
			SELECT	a.*,
					IF(a.persen_realisasi>=a.persen_target,
						'1',
						IF((a.persen_target-a.persen_realisasi)<=10,
							'2',
							'3'
						)
					) AS status_anggaran,
					IF(a.realisasi1>=a.target1,
						'1',
						IF((a.target1-a.realisasi1)<=10,
							'2',
							'3'
						)
					) AS status_kinerja
				FROM(
					SELECT	a.*,
						b.target,
						ROUND(b.target/a.paguakhir*100) AS persen_target,
						b.realisasi,
						ROUND(b.realisasi/a.paguakhir*100) AS persen_realisasi,
						b.target1,
						b.realisasi1
					FROM(
						SELECT	kode,
							uraian,
							paguakhir
						FROM d_pagu
						WHERE kdsatker='".session('kdsatker')."' AND thang='".session('tahun')."' AND lvl='3'
					) a
					LEFT OUTER JOIN(
						SELECT	kode,
							target,
							realisasi,
							target1,
							realisasi1
						FROM d_capaian
						WHERE kdsatker='".session('kdsatker')."' AND thang='".session('tahun')."'
					) b ON(a.kode=b.kode)
				) a
		");
		
		$data = '';
		foreach($rows as $row){
			$data .= '<tr>
						<td>'.$row->kode.'</td>
						<td>'.$row->uraian.'</td>
						<td style="text-align:right;">'.number_format($row->paguakhir).'</td>
						<td style="text-align:right;">'.number_format($row->target).'</td>
						<td style="text-align:right;">'.number_format($row->realisasi).'</td>
						<td style="text-align:center;"><div class="circleBase type'.number_format($row->status_anggaran).'"></div></td>
						<td style="text-align:right;">'.number_format($row->target1).'</td>
						<td style="text-align:right;">'.number_format($row->realisasi1).'</td>
						<td style="text-align:center;"><div class="circleBase type'.number_format($row->status_kinerja).'"></div></td>
					  </tr>';
		}
			
		return $data;	
	}

}