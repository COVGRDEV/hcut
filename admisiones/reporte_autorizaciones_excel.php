<?php
	session_start();
	
	require_once("../db/DbAutorizaciones.php");
	require_once("../funciones/PHPExcel/Classes/PHPExcel.php");

	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	$tipoReporte = $_POST["tipoReporte"];

	switch ($tipoReporte) {
		case "1": //Reporte tiempos de atención 
			require_once("../db/DbConvenios.php");
			require_once("../db/DbPlanes.php");
			require_once("../funciones/Utilidades.php");

			$utilidades = new Utilidades(); 
			$dbConvenios = new DbConvenios(); 
			$dbPlanes = new DbPlanes(); 
			$DbAutorizaciones = new DbAutorizaciones(); 
			
			$fechaInicial = $utilidades->str_decode($_POST["hddfechaInicial"]);
			$fechaFinal = $utilidades->str_decode($_POST["hddfechaFinal"]);
			$id_convenio = $utilidades->str_decode($_POST["hddconvenio"]);
			$id_plan = $utilidades->str_decode($_POST["hddplan"]);
			
			$vec_tiempos = [];
			$vec_estadisticas_estado = [];
			$subindice = "";			
			$subindice_e = ""; 
			$fecha_referencia = "01-01-1970 00:00:00"; 
			
			//Se obtiene el listado base del reporte
			$rta_autorizaciones = $DbAutorizaciones->getReporteAutorizaciones($fechaInicial, $fechaFinal, $id_convenio, $id_plan);
					
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("A")->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("B")->setWidth(10);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("C")->setWidth(10);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("D")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("E")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("F")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("G")->setWidth(20);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("H")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("I")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("J")->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("K")->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("L")->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("M")->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("N")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("O")->setWidth(7);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("P")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("Q")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("R")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("S")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("T")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("U")->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("V")->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("W")->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("X")->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("Y")->setWidth(20);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("Z")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("AA")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("AB")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("AC")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("AD")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("AE")->setWidth(20);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("AF")->setWidth(35);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("AG")->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("AH")->setWidth(15);
			$max_col_letra = "AH";
			
			//Se agregan los filtros seleccionados al reporte
			$contador_linea = 1;
			if ($fechaInicial != "") {
				$arr_aux = explode("-", $fechaInicial);
				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A".$contador_linea, "Fecha inicial:")
							->setCellValue("B".$contador_linea, $arr_aux[2]."/".$arr_aux[1]."/".$arr_aux[0]);
				$contador_linea++;
			}
			if ($fechaFinal != "") {
				$arr_aux = explode("-", $fechaFinal);
				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A".$contador_linea, "Fecha final:")
							->setCellValue("B".$contador_linea, $arr_aux[2]."/".$arr_aux[1]."/".$arr_aux[0]);
				$contador_linea++;
			}
			if ($id_convenio != "") {
				$convenio_obj = $dbConvenios->getConvenio($id_convenio);
				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A".$contador_linea, "Convenio:")
							->setCellValue("B".$contador_linea, $convenio_obj["nombre_convenio"]);
				$contador_linea++;
			}
			if ($id_plan != "") {
				$plan_obj = $dbPlanes->getPlan($id_plan);
				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A".$contador_linea, "Plan:")
							->setCellValue("B".$contador_linea, $plan_obj["nombre_plan"]);
				$contador_linea++;
			}
			
			$contador_linea++;
			$objPHPExcel->getActiveSheet()						 
						->setCellValue("A".$contador_linea, "No. AUTORIZACIÓN")
						->setCellValue("B".$contador_linea, "FECHA")
						->setCellValue("C".$contador_linea, "HORA")
						->setCellValue("D".$contador_linea, "USUARIO AUTORIZACIÓN")
						->setCellValue("E".$contador_linea, "CONVENIO")
						->setCellValue("F".$contador_linea, "PLAN") 
						->setCellValue("G".$contador_linea, "ESTADO PACIENTE") 		
						->setCellValue("H".$contador_linea, "PROVEEDOR SALA") 
						->setCellValue("I".$contador_linea, "PROVEEDOR ANESTESIA") 
						->setCellValue("J".$contador_linea, "PROVEEDOR ESPECIALISTA")
						->setCellValue("K".$contador_linea, "CONCEPTOS AUTORIZADOS")
						->setCellValue("L".$contador_linea, "DESCRIPCIÓN CONCEPTOS")
						->setCellValue("M".$contador_linea, "ESTADO AUTORIZACIÓN")
						->setCellValue("N".$contador_linea, "CANCELADA")
						->setCellValue("O".$contador_linea, "MOTIVA CANCELA")
						->setCellValue("P".$contador_linea, "TIPO DOCUMENTO")
						->setCellValue("Q".$contador_linea, "NUMERO DOCUMENTO")  
						->setCellValue("R".$contador_linea, "PACIENTE") 
						->setCellValue("S".$contador_linea, "EDAD") 		
						->setCellValue("T".$contador_linea, "GENERO") 
						->setCellValue("U".$contador_linea, "RANGO PACIENTE") 
						->setCellValue("V".$contador_linea, "MUNICIPIO")
						->setCellValue("W".$contador_linea, "TIPO COTIZANTE")
						->setCellValue("X".$contador_linea, "EXENTO CUOTA")
						->setCellValue("Y".$contador_linea, "PROFESIONAL")
						->setCellValue("Z".$contador_linea, "FECHA FORMULACIÓN")
						->setCellValue("AA".$contador_linea, "OBSERVACIÓN AUTORIZACIÓN")
						->setCellValue("AB".$contador_linea, "OJO")
						->setCellValue("AC".$contador_linea, "DIAGNÓSTICO")  
						->setCellValue("AD".$contador_linea, "LATERALIDAD") 
						->setCellValue("AE".$contador_linea, "VALOR") 		
						->setCellValue("AF".$contador_linea, "NÚM. FACTURA") 
						->setCellValue("AG".$contador_linea, "NUM. PEDIDO") 
						->setCellValue("AH".$contador_linea, "ESTADO PAGO"); 
			
			$objPHPExcel->getActiveSheet()->getStyle("A".$contador_linea.":".$max_col_letra.$contador_linea)->getFont()->setBold(true);
			
			$contador_linea++;
			$edad=""; $tipo_edad="";
			foreach ($rta_autorizaciones as $value) {
				$edad = substr($value["edad"],0,2);
				switch(substr($value["edad"],3,1)){
					case 1:
						 $tipo_edad=" años";
					break;
					case 2:
						 $tipo_edad=" meses";
					break;
					case 3:
						 $tipo_edad=" días";
					break;
					
				}
				$objPHPExcel->getActiveSheet()
					->setCellValue("A".$contador_linea, $value["numero_auto"])
					->setCellValue("B".$contador_linea, $value["fecha_auto"])
					->setCellValue("C".$contador_linea, $value["hora_auto"])
					->setCellValue("D".$contador_linea,	$value["usuario_auto"])
					->setCellValue("E".$contador_linea,	$value["convenio"])
					->setCellValue("F".$contador_linea,	$value["plan"])
					->setCellValue("G".$contador_linea,	$value["estado_paciente"])
					->setCellValue("H".$contador_linea,	$value["proveedor_sala"])
					->setCellValue("I".$contador_linea,	$value["proveedor_anestesia"])
					->setCellValue("J".$contador_linea,	$value["proveedor_especialista"])
					->setCellValue("K".$contador_linea,	$value["codigo_concepto"])
					->setCellValue("L".$contador_linea, $value["desc_concepto"])
					->setCellValue("M".$contador_linea, $value["estado_auto"])
					->setCellValue("N".$contador_linea, $value["cancelada"])
					->setCellValue("O".$contador_linea,	$value["motivo_cancela"])
					->setCellValue("P".$contador_linea,	$value["tipo_documento"])
					->setCellValue("Q".$contador_linea,	$value["documento"])
					->setCellValue("R".$contador_linea,	$value["paciente"])
					->setCellValue("S".$contador_linea,	$edad."".$tipo_edad)
					->setCellValue("T".$contador_linea,	$value["genero"])
					->setCellValue("U".$contador_linea,	$value["rango_paciente"])
					->setCellValue("V".$contador_linea,	$value["municipio"])
					->setCellValue("W".$contador_linea,	$value["tipo_cotizante"])
					->setCellValue("X".$contador_linea, $value["exento_cuota"])
					->setCellValue("Y".$contador_linea, $value["profesional"])
					->setCellValue("Z".$contador_linea, $value["fecha_form"])
					->setCellValue("AA".$contador_linea, $value["observ_auto"])
					->setCellValue("AB".$contador_linea, $value["ojo_auto"])
					->setCellValue("AC".$contador_linea, $value["diagnostico"])
					->setCellValue("AD".$contador_linea, $value["lateralidad"])
					->setCellValue("AE".$contador_linea, $value["valor"])
					->setCellValue("AF".$contador_linea, $value["num_factura"])
					->setCellValue("AG".$contador_linea, $value["num_pedido"])
					->setCellValue("AH".$contador_linea, $value["estado_pago"]);
					$contador_linea++;
					
			}						
		
			//Se renombra la hoja actual
			$objPHPExcel->getActiveSheet()->setTitle("Reporte de autorizaciones");

			//Set document properties
			$objPHPExcel->getProperties()->setCreator("OSPS")
					->setLastModifiedBy("OSPS")
					->setTitle("Office 2007 XLSX")
					->setSubject("Office 2007 XLSX")
					->setDescription("Document for Office 2007 XLSX.")
					->setKeywords("office 2007")
					->setCategory("result");
			
			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);

			//Se borra el reporte previamente generado por el usuario
			@unlink("./tmp/reporte_autorizaciones_".$id_usuario.".xlsx");
			
			// Save Excel 2007 file
			$id_usuario = $_SESSION["idUsuario"];
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
			$objWriter->save("./tmp/reporte_autorizaciones_".$id_usuario.".xlsx");
			
		?>
			<form name="frm_reporte_autorizaciones" id="frm_reporte_autorizaciones" method="post" action="tmp/reporte_autorizaciones_<?php echo($id_usuario); ?>.xlsx"></form>
		<script id="ajax" type="text/javascript">
			document.getElementById("frm_reporte_autorizaciones").submit();
		</script>
		<?php
			break;
	}
	
	exit;
?>