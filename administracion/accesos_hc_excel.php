<?php session_start();
	require_once '../db/DbHistoriaClinica.php';
	require_once '../funciones/PHPExcel/Classes/PHPExcel.php';
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/pdf/funciones.php");
	
	$dbHistoriaClinica = new DbHistoriaClinica();
	$funciones_persona = new FuncionesPersona();
	$utilidades = new Utilidades();
	
	@$id_usuario = $utilidades->str_decode($_POST["hdd_id_usuario_e"]);
	@$id_paciente = $utilidades->str_decode($_POST["hdd_id_paciente_e"]);
	@$fecha_inicial = $utilidades->str_decode($_POST["hdd_fecha_inicial_e"]);
	@$fecha_final = $utilidades->str_decode($_POST["hdd_fecha_final_e"]);
	
	$lista_ingresos = $dbHistoriaClinica->getListaIngresosHistoriaClinica($id_usuario, $id_paciente, $fecha_inicial, $fecha_final);
	
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	
	$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(25);
	$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(32);
	$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(5);
	$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(15);
	$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(32);
	$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(55);
	
	$cont_linea = 1;
	
	if ($id_usuario != "") {
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A".$cont_linea, "USUARIO")
					->setCellValue("B".$cont_linea, $lista_ingresos[0]["nombre_usuario"]." ".$lista_ingresos[0]["apellido_usuario"]);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$cont_linea.':A'.$cont_linea)->getFont()->setBold(true);
		
		$cont_linea++;
	}
	
	if ($id_paciente != "") {
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A".$cont_linea, "PACIENTE")
					->setCellValue("B".$cont_linea, $funciones_persona->obtenerNombreCompleto($lista_ingresos[0]["nombre_1"], $lista_ingresos[0]["nombre_2"], $lista_ingresos[0]["apellido_1"], $lista_ingresos[0]["apellido_2"]))
					->setCellValue("A".($cont_linea + 1), $lista_ingresos[0]["tipo_documento"])
					->setCellValue("B".($cont_linea + 1), $lista_ingresos[0]["numero_documento"]);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$cont_linea.':A'.($cont_linea + 1))->getFont()->setBold(true);
		
		$cont_linea += 2;
	}
	if ($fecha_inicial != "") {
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A".$cont_linea, "FECHA INICIAL")
					->setCellValue("B".$cont_linea, $fecha_inicial);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$cont_linea.':A'.$cont_linea)->getFont()->setBold(true);
		
		$cont_linea++;
	}
	if ($fecha_final != "") {
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A".$cont_linea, "FECHA FINAL")
					->setCellValue("B".$cont_linea, $fecha_final);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$cont_linea.':A'.$cont_linea)->getFont()->setBold(true);
		
		$cont_linea++;
	}
	
	$cont_linea++;
	
	//Encabezados
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A".$cont_linea, "Fecha/Hora")
				->setCellValue("B".$cont_linea, "Usuario")
				->setCellValue("C".$cont_linea, "Paciente")
				->setCellValue("F".$cont_linea, "Tipo acceso");
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C'.$cont_linea.':E'.$cont_linea);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$cont_linea.':F'.$cont_linea)->getFont()->setBold(true);
	
	$cont_linea++;
	
	foreach ($lista_ingresos as $ingreso_aux) {
		$nombre_aux = $funciones_persona->obtenerNombreCompleto($ingreso_aux["nombre_1"], $ingreso_aux["nombre_2"], $ingreso_aux["apellido_1"], $ingreso_aux["apellido_2"]);
		$tipo_ingreso_aux = "";
		switch ($ingreso_aux["id_tipo_ingreso"]) {
			case "160": //Registro detallado
				$tipo_ingreso_aux = $ingreso_aux["nombre_tipo_reg"]." (".$ingreso_aux["fecha_hc"].")";
				break;
			case "164": //Fórmula médica
				$tipo_ingreso_aux = $ingreso_aux["tipo_ingreso"]." (".$ingreso_aux["fecha_despacho"].")";
				break;
			default:
				$tipo_ingreso_aux = $ingreso_aux["tipo_ingreso"];
				break;
		}
		
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A".$cont_linea, $ingreso_aux["fecha_ingreso_t"])
					->setCellValue("B".$cont_linea, $ingreso_aux["nombre_usuario"]." ".$ingreso_aux["apellido_usuario"])
					->setCellValue("C".$cont_linea, $ingreso_aux["cod_tipo_documento"])
					->setCellValue("D".$cont_linea, $ingreso_aux["numero_documento"])
					->setCellValue("E".$cont_linea, $nombre_aux)
					->setCellValue("F".$cont_linea, $tipo_ingreso_aux);
		
		$cont_linea++;
	}
	
	// Set document properties
	$objPHPExcel->getProperties()->setCreator("OSPS")
			->setLastModifiedBy("OSPS")
			->setTitle("Reporte Accesos HC")
			->setSubject("Reporte Accesos HC")
			->setDescription("Reporte de Accesos a la Historia Clinica")
			->setKeywords("accesos historia clinica")
			->setCategory("result");
	
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle('Accesos HC');
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
	
	// Redirect output to a client's web browser (Excel2007)
	/*header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Reporte Accesos HC.xlsx"');
	header('Cache-Control: max-age=0');*/
	
	//Se borra el reporte previamente generado por el usuario
	@unlink("./tmp/reporte_accesos_hc_".$id_usuario_crea.".xlsx");
	
	// Save Excel 2007 file
	$id_usuario_crea = $_SESSION["idUsuario"];
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("./tmp/reporte_accesos_hc_".$id_usuario_crea.".xlsx");
?>
<form name="frm_reporte_hc" id="frm_reporte_hc" method="post" action="tmp/reporte_accesos_hc_<?php echo($id_usuario_crea); ?>.xlsx">
</form>
<script id="ajax" type="text/javascript">
	document.getElementById("frm_reporte_hc").submit();
</script>
<?php
	exit;
?>
