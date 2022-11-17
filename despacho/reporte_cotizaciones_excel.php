<?php
	session_start();
	
	require_once("../db/DbDespacho.php");
	require_once("../db/DbProcedimientosCotizaciones.php");
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbListas.php");
	require_once("../funciones/PHPExcel/Classes/PHPExcel.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/pdf/funciones.php");
	require_once("../funciones/Utilidades.php");
	
	$dbDespacho = new DbDespacho();
	$dbProcedimientosCotizaciones = new DbProcedimientosCotizaciones();
	$dbUsuarios = new DbUsuarios();
	$dbListas = new DbListas();
	
	$funciones_persona = new FuncionesPersona();
	$utilidades = new Utilidades();
	
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	
	$opcion = $utilidades->str_decode($_POST["hdd_opcion"]);
	
	switch ($opcion) {
		case "1": //Exportación a Excel
			@$txt_paciente = $utilidades->str_decode($_POST["hdd_txt_paciente_e"]);
			@$fecha_ini = $utilidades->str_decode($_POST["hdd_fecha_ini_e"]);
			@$fecha_fin = $utilidades->str_decode($_POST["hdd_fecha_fin_e"]);
			@$id_proc_cotiz = $utilidades->str_decode($_POST["hdd_id_proc_cotiz_e"]);
			@$id_usuario_prof = $utilidades->str_decode($_POST["hdd_id_usuario_prof_e"]);
			@$id_lugar_cita = $utilidades->str_decode($_POST["hdd_id_lugar_cita_e"]);
			@$observaciones_cotiz = $utilidades->str_decode($_POST["hdd_observaciones_cotiz_e"]);
			
			//Se obtiene el listado de cotizaciones
			$lista_cotizaciones = $dbDespacho->getListaDespachoCotizacionesParams($txt_paciente, $id_proc_cotiz,
					$observaciones_cotiz, $fecha_ini, $fecha_fin, $id_usuario_prof, $id_lugar_cita);
			
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(35);
			$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(15);
			
			//Se agregan los parámetros del reporte
			$contador = 1;
			$objPHPExcel->getActiveSheet()
						->setCellValue("A".$contador, "Fecha inicial")
						->setCellValue("B".$contador, $fecha_ini);
			$contador++;
			$objPHPExcel->getActiveSheet()
						->setCellValue("A".$contador, "Fecha final")
						->setCellValue("B".$contador, $fecha_fin);
			$contador++;
			if ($txt_paciente != "") {
				$objPHPExcel->getActiveSheet()
							->setCellValue("A".$contador, "Paciente")
							->setCellValue("B".$contador, $txt_paciente);
				$contador++;
			}
			if ($id_proc_cotiz != "") {
				//Se busca el nombre del procedimiento
				$proc_cotiz_obj = $dbProcedimientosCotizaciones->getProcedimientoCotizacion($id_proc_cotiz);
				if (isset($proc_cotiz_obj["nombre_proc_cotiz"])) {
					$objPHPExcel->getActiveSheet()
								->setCellValue("A".$contador, "Procedimiento")
								->setCellValue("B".$contador, $proc_cotiz_obj["nombre_proc_cotiz"]);
					$contador++;
				}
			}
			if ($id_usuario_prof != "") {
				//Se busca el nombre del usuario
				$usuario_obj = $dbUsuarios->getUsuario($id_usuario_prof);
				if (isset($usuario_obj["nombre_usuario"])) {
					$objPHPExcel->getActiveSheet()
								->setCellValue("A".$contador, "Atendió")
								->setCellValue("B".$contador, $usuario_obj["nombre_usuario"]." ".$usuario_obj["apellido_usuario"]);
					$contador++;
				}
			}
			if ($id_lugar_cita != "") {
				//Se busca el nombre de la sede
				$lugar_cita_obj = $dbListas->getDetalle($id_lugar_cita);
				if (isset($lugar_cita_obj["nombre_detalle"])) {
					$objPHPExcel->getActiveSheet()
								->setCellValue("A".$contador, "Sede")
								->setCellValue("B".$contador, $lugar_cita_obj["nombre_detalle"]);
					$contador++;
				}
			}
			if ($observaciones_cotiz != "") {
				$objPHPExcel->getActiveSheet()
							->setCellValue("A".$contador, "Observaciones")
							->setCellValue("B".$contador, $observaciones_cotiz);
				$contador++;
			}
			
			$objPHPExcel->getActiveSheet()->getStyle("A1:A".($contador - 1))->getFont()->setBold(true);
			
			$contador++;
			$objPHPExcel->getActiveSheet()
						->setCellValue("A".$contador, "Fecha")
						->setCellValue("B".$contador, "Tipo documento")
						->setCellValue("C".$contador, "Número documento")
						->setCellValue("D".$contador, "Nombre completo")
						->setCellValue("E".$contador, "Teléfono(s)")
						->setCellValue("F".$contador, "Procedimiento")
						->setCellValue("G".$contador, "Profesional")
						->setCellValue("H".$contador, "Sede")
						->setCellValue("I".$contador, "Valor cotización");
			
			$objPHPExcel->getActiveSheet()->getStyle("A".$contador.":I".$contador)->getFont()->setBold(true);
			
			$contador++;
			foreach ($lista_cotizaciones as $cotiz_aux) {
				$nombre_completo = $funciones_persona->obtenerNombreCompleto($cotiz_aux["nombre_1"], $cotiz_aux["nombre_2"], $cotiz_aux["apellido_1"], $cotiz_aux["apellido_2"]);
				$telefonos = $cotiz_aux["telefono_1"];
				if ($cotiz_aux["telefono_2"] != "") {
					$telefonos .= " - ".$cotiz_aux["telefono_2"];
				}
				
				$objPHPExcel->getActiveSheet()
							->setCellValue("A".$contador, $cotiz_aux["fecha_despacho_t"])
							->setCellValue("B".$contador, $cotiz_aux["tipo_documento"])
							->setCellValue("C".$contador, $cotiz_aux["numero_documento"])
							->setCellValue("D".$contador, $nombre_completo)
							->setCellValue("E".$contador, $telefonos)
							->setCellValue("F".$contador, $cotiz_aux["nombre_proc_cotiz"])
							->setCellValue("G".$contador, $cotiz_aux["nombre_usuario"]." ".$cotiz_aux["apellido_usuario"])
							->setCellValue("H".$contador, $cotiz_aux["lugar_cita"])
							->setCellValue("I".$contador, $cotiz_aux["valor_cotiz"]);
				$contador++;
			}
			
			// Rename worksheet
			$objPHPExcel->getActiveSheet()->setTitle("Cotizaciones");
			
			// Set document properties
			$objPHPExcel->getProperties()->setCreator("OSPS")
					->setLastModifiedBy("OSPS")
					->setTitle("Office 2007 XLSX")
					->setSubject("Office 2007 XLSX")
					->setDescription("Document for Office 2007 XLSX.")
					->setKeywords("office 2007")
					->setCategory("result");
			
			//Se borra el reporte previamente generado por el usuario
			$id_usuario = $_SESSION["idUsuario"];
			@unlink("./tmp/reporte_cotizaciones_".$id_usuario.".xlsx");
			
			// Save Excel 2007 file
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
			$objWriter->save("./tmp/reporte_cotizaciones_".$id_usuario.".xlsx");
		?>
        <form name="frm_reporte_cotizaciones" id="frm_reporte_cotizaciones" method="post" action="tmp/reporte_cotizaciones_<?php echo($id_usuario); ?>.xlsx">
        </form>
        <script id="ajax" type="text/javascript">
			document.getElementById("frm_reporte_cotizaciones").submit();
		</script>
		<?php
			break;
	}
	
	exit;
?>
