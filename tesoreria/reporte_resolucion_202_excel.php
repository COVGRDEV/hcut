<?php
	session_start();
	
	require_once("../db/DbPagos.php");
	require_once("../db/DbTiposPago.php");
	
	require_once '../funciones/PHPExcel/Classes/PHPExcel.php';
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/pdf/funciones.php");
	
	$dbPagos = new DbPagos();
	$dbTiposPago = new DbTiposPago();
	
	$funciones_persona = new FuncionesPersona();
	
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	
	$tipoReporte = $_POST['tipoReporte'];
	
	switch ($tipoReporte) {
		case "1": //Reporte general
			require_once("../funciones/pdf/funciones.php");
			require_once("../db/DbPagos.php");
			require_once("../db/DbPacientes.php");
			require_once("../db/DbTiposPago.php");
			require_once("../db/DbConvenios.php");
			require_once("../db/DbPlanes.php");
			require_once("../db/DbMaestroProcedimientos.php");
			require_once("../db/DbMaestroMedicamentos.php");
			require_once("../db/DbMaestroInsumos.php");
			require_once("../db/DbUsuarios.php");
			require_once("../db/DbListas.php");
			require_once("../db/DbAnticipos.php");
			require_once '../funciones/Utilidades.php';
			require_once '../db/DbVariables.php';
			
			$utilidades = new Utilidades();
			$dbPagos = new DbPagos();
			$tiposPago = new DbTiposPago();
			$dbConvenios = new DbConvenios();
			$dbPlanes = new DbPlanes();
			$dbMaestroProcedimientos = new DbMaestroProcedimientos();
			$dbMaestroMedicamentos = new DbMaestroMedicamentos();
			$dbMaestroInsumos = new DbMaestroInsumos();
			$dbUsuarios = new DbUsuarios();
			$dbListas = new DbListas();
			$dbAnticipos = new DbAnticipos();
			$dbVariables = new DbVariables();
			$dbPacientes = new DbPacientes();
			
			$fecha_inicial = $utilidades->str_decode($_POST['hddfechaInicial']);
			$fecha_final = $utilidades->str_decode($_POST['hddfechaFinal']);
			$id_convenio = $utilidades->str_decode($_POST['hddconvenio']);
						
			$indice_act = 0;


			$arr_diferencia = $dbVariables->getDiferenciaFechas($fecha_inicial, $fecha_final, 2);
			$diferencia_dias = intval($arr_diferencia["dias"], 10);
			
			if($diferencia_dias >= 34){
				//Mostrar error
				?>
					<script id="ajax" type="text/javascript">
						alert("Existe más de un mes entre las fechas seleccionadas");
						window.close();
					</script>
				<?php
				
			}else{
				
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(18);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(18);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(50);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(15);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(15);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(15);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(15);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(15);
	
					
					//Se agregan los filtros seleccionados al reporte
					$contador_linea = 1;
					if ($fecha_inicial != "") {
						$arr_aux = explode("-", $fecha_inicial);
						$objPHPExcel->setActiveSheetIndex(0)
									->setCellValue('A'.$contador_linea, 'Fecha inicial:')
									->setCellValue('B'.$contador_linea, $arr_aux[2]."/".$arr_aux[1]."/".$arr_aux[0]);
						$contador_linea++;
					}
					if ($fecha_final != "") {
						$arr_aux = explode("-", $fecha_final);
						$objPHPExcel->setActiveSheetIndex(0)
									->setCellValue('A'.$contador_linea, 'Fecha final:')
									->setCellValue('B'.$contador_linea, $arr_aux[2]."/".$arr_aux[1]."/".$arr_aux[0]);
						$contador_linea++;
					}
					if ($id_convenio != "") {
						$convenio_obj = $dbConvenios->getConvenio($id_convenio);
						$objPHPExcel->setActiveSheetIndex(0)
									->setCellValue('A'.$contador_linea, 'Convenio:')
									->setCellValue('B'.$contador_linea, $convenio_obj["nombre_convenio"]);
						$contador_linea++;
					}
					$contador_linea++;	
					
					$pacientes_obj = $dbPacientes->reportePacientesAVSCLejos($fecha_inicial, $fecha_final, $id_convenio);
					
					if (count($pacientes_obj) > 0) {
										
							$objPHPExcel->getActiveSheet()
							->setCellValue("A".$contador_linea, "TIPO DOCUMENTO")
							->setCellValue("B".$contador_linea, "NÚMERO DE DOCUMENTO")
							->setCellValue("C".$contador_linea, "NOMBRES")
							->setCellValue("D".$contador_linea, "FECHA NACIMIENTO")
							->setCellValue("E".$contador_linea, "GÉNERO")
							->setCellValue("F".$contador_linea, "AVSC LEJOS OI")
							->setCellValue("G".$contador_linea, "AVSC LEJOS OD")
							->setCellValue("H".$contador_linea, "FECHA VALORACIÓN");
							$objPHPExcel->getActiveSheet()->getStyle("A".$contador_linea.":H".$contador_linea)->getFont()->setBold(true);
							
						$contador_linea++;	
						
								
						foreach ($pacientes_obj as $paciente) {
							$valor_aux = "";
						
							
							$objPHPExcel->getActiveSheet()
										->setCellValue('A'.$contador_linea, $paciente['tipo_documento'])
										->setCellValue('B'.$contador_linea, $paciente['numero_documento'])
										->setCellValue('C'.$contador_linea, mb_strtoupper($funciones_persona->obtenerNombreCompleto($paciente['apellido_1'], $paciente['apellido_2'], $paciente['nombre_1'], $paciente['nombre_2']), "UTF-8"))
										->setCellValue('D'.$contador_linea, $paciente['fecha_nacimiento'])
										->setCellValue('E'.$contador_linea, $paciente['sexo'])
										->setCellValue('F'.$contador_linea, $paciente['avsc_lejos_oi'])
										->setCellValue('G'.$contador_linea, $paciente['avsc_lejos_od'])
										->setCellValue('H'.$contador_linea, $paciente['fecha_valoracion']);
							
							$contador_linea++;
						}
						
						$objPHPExcel->getActiveSheet()->setTitle('Reporte pacientes AVSC lejos');
					}
					
					//Set document properties
					$objPHPExcel->getProperties()->setCreator("OSPS")
							->setLastModifiedBy("OSPS")
							->setTitle("Office 2007 XLSX")
							->setSubject("Office 2007 XLSX")
							->setDescription("Document for Office 2007 xlsx")
							->setKeywords("office 2007")
							->setCategory("result");
					
					// Set active sheet index to the first sheet, so Excel opens this as the first sheet
					$objPHPExcel->setActiveSheetIndex(0);
					
					// Save Excel 2007 file
					$id_usuario = $_SESSION["idUsuario"];
					
					//Se borra el reporte previamente generado por el usuario
					@unlink("./tmp/reporte_tesoreria_".$id_usuario.".xlsx");
					
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save("./tmp/reporte_resolucion_202_".$id_usuario.".xlsx");
				?>
				<form name="frm_reporte_resolucion" id="frm_reporte_resolucion" method="post" action="tmp/reporte_resolucion_202_<?php echo($id_usuario); ?>.xlsx">
				</form>
				<script id="ajax" type="text/javascript">
					document.getElementById("frm_reporte_resolucion").submit();
				</script>
				<?php		
			}
				
			break;
		
	}
	
	function operacion_horas($hora, $minutos, $tipo, $cantidad, $formato) {
		if ($tipo == 1) { //Sumar minutos
			$horaInicial = $hora . ":" . $minutos;
			$segundos_horaInicial = strtotime($horaInicial);
			$segundos_minutoAnadir = $cantidad * 60;
			$nuevaHora = date("H:i", $segundos_horaInicial + $segundos_minutoAnadir);
		} else if ($tipo == 2) { //Restar minutos
			$horaInicial = $hora . ":" . $minutos;
			$segundos_horaInicial = strtotime($horaInicial);
			$segundos_minutoAnadir = $cantidad * 60;
			$nuevaHora = date("H:i", $segundos_horaInicial - $segundos_minutoAnadir);
		}
	
		if ($formato == 12) {
			$hora_nueva = explode(":", $nuevaHora);
			$hora_resultado = mostrar_hora_format($hora_nueva[0], $hora_nueva[1]);
		} else {
			$hora_resultado = $nuevaHora;
		}
	
		return $hora_resultado;
	}
	
	//Devulve la hora en formato 12 horas con la jornada
	function mostrar_hora_format($hora, $minutos) {
		$hora = cifras_numero($hora, 2);
		$minutos = cifras_numero($minutos, 2);
	
		$hora_res = '';
		if ($hora > 12) {
			$hora = $hora - 12;
			$hora_res = $hora . ":" . $minutos . " PM";
		} else {
			$hora_res = $hora . ":" . $minutos . " AM";
		}
	
		return $hora_res;
	}
	
	function cifras_numero($consecutivo, $cifras) {
		$longitud = strlen($consecutivo);
		while ($longitud <= $cifras - 1) {
			$consecutivo = "0" . $consecutivo;
			$longitud = strlen($consecutivo);
		}
		return $consecutivo;
	}
	
	exit;
?>
