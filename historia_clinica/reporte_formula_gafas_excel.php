<?php
	session_start();
	
	require_once("../db/DbFormulacion.php");
	require_once("../funciones/PHPExcel/Classes/PHPExcel.php");
	require_once("../funciones/pdf/funciones.php");
	
	$dbFormulacion = new dbFormulacion();
	
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	$vec_oftalmologos = array();
	$vec_optometras = array();
	$subindice = "";
	
	$tipoReporte = $_POST["tipoReporte"];

	switch ($tipoReporte) {
		case "1": //Reporte formulación gafas
			require_once("../db/DbConvenios.php");
			require_once("../db/DbPlanes.php");
			require_once("../db/DbUsuarios.php");
			require_once("../db/DbListas.php");
			require_once("../funciones/Utilidades.php");

			$utilidades = new Utilidades();
			$dbConvenios = new DbConvenios();
			$dbPlanes = new DbPlanes();
			$dbUsuarios = new DbUsuarios();
			$dbListas = new DbListas();
			
			$fechaInicial = $utilidades->str_decode($_POST["hddfechaInicial"]);
			$fechaFinal = $utilidades->str_decode($_POST["hddfechaFinal"]);
			$id_convenio = $utilidades->str_decode($_POST["hddconvenio"]);
			$id_plan = $utilidades->str_decode($_POST["hddplan"]);
			$usuario_atiende = $utilidades->str_decode($_POST["hdd_usuarios"]);
			$lugar_cita = $utilidades->str_decode($_POST["hdd_lugar_cita"]);
			
			//Se obtiene el listado del reporte
			$rta_reporte_formulacion_gafas = $dbFormulacion->reporteFormulacionGafas($fechaInicial, $fechaFinal, $id_convenio, $id_plan, $usuario_atiende, $lugar_cita);
					
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("A")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("B")->setWidth(20);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("C")->setWidth(20);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("D")->setWidth(20);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("E")->setWidth(20);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("F")->setWidth(20);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("G")->setWidth(18);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("H")->setWidth(20);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("I")->setWidth(26);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("J")->setWidth(26);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("K")->setWidth(26);
			
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
			if ($usuario_atiende != "") {
				$usuario_obj = $dbUsuarios->getUsuario($usuario_atiende);
				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A".$contador_linea, "Profesional que atiende:")
							->setCellValue("B".$contador_linea, $usuario_obj["nombre_usuario"]." ".$usuario_obj["apellido_usuario"]);
				$contador_linea++;
			}
			if ($lugar_cita != "") {
				$lugar_obj = $dbListas->getDetalle($lugar_cita);
				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A".$contador_linea, "Lugar de la cita:")
							->setCellValue("B".$contador_linea, $lugar_obj["nombre_detalle"]);
				$contador_linea++;
			}			
			
			$contador_linea++;
			
			$objPHPExcel->getActiveSheet()
						->setCellValue("A".$contador_linea, "TIPO DOCUMENTO")
						->setCellValue("B".$contador_linea, "DOCUMENTO")
						->setCellValue("C".$contador_linea, "PRIMER NOMBRE")
						->setCellValue("D".$contador_linea, "SEGUNDO NOMBRE")
						->setCellValue("E".$contador_linea, "PRIMER APELLIDO")
						->setCellValue("F".$contador_linea, "SEGUNDO APELLIDO")
						->setCellValue("G".$contador_linea, "FECHA HC")
						->setCellValue("H".$contador_linea, "LUGAR DE LA CITA")
						->setCellValue("I".$contador_linea, "TIPO CITA")
						->setCellValue("J".$contador_linea, "OFTALMOLOGO")
						->setCellValue("K".$contador_linea, "OPTOMETRA");						
			
			$max_col_letra = "K";
			
			$objPHPExcel->getActiveSheet()->getStyle("A".$contador_linea.":".$max_col_letra.$contador_linea)->getFont()->setBold(true);
			
			$contador_linea++;				
						
			foreach ($rta_reporte_formulacion_gafas as $value) {				
				$objPHPExcel->getActiveSheet()					
					->setCellValue("A".$contador_linea, $value["tipo_documento"])
					->setCellValue("B".$contador_linea, $value["numero_documento"])
					->setCellValue("C".$contador_linea, $value["nombre_1"])
					->setCellValue("D".$contador_linea, $value["nombre_2"])
					->setCellValue("E".$contador_linea, $value["apellido_1"])
					->setCellValue("F".$contador_linea, $value["apellido_2"])
					->setCellValue("G".$contador_linea, $value["fecha_hc_t"])					
					->setCellValue("H".$contador_linea, $value["lugar_cita"])
					->setCellValue("I".$contador_linea, ($value["nombre_tipo_cita"] != "" ? $value["nombre_tipo_cita"] : "(Sin admisión)"))
					->setCellValue("J".$contador_linea, $value["oftalmologo"])
					->setCellValue("K".$contador_linea, $value["optometra"]);
	
				$subindice = $value["oftalmologo"];
				if (array_key_exists($subindice, $vec_oftalmologos)) {
					$vec_oftalmologos[$subindice] = $vec_oftalmologos[$subindice] + 1; 
				} else {
					$vec_oftalmologos[$subindice] = 1; 
				}
				//echo "\n".$subindice." ".$vec_oftalmologos[$subindice];
				
				$subindice = $value["optometra"];
				if (array_key_exists($subindice, $vec_optometras)) {
					$vec_optometras[$subindice] = $vec_optometras[$subindice] + 1; 
				} else {
					$vec_optometras[$subindice] = 1; 
				}
				//echo "\n".$subindice." ".$vec_optometras[$subindice];
	
				$contador_linea++;
			}

			ksort($vec_oftalmologos);
			ksort($vec_optometras);

			//Se renombra la hoja actual
			$objPHPExcel->getActiveSheet()->setTitle("Reporte de Formulación Gafas");
			
			
			//Generar la segunda hoja: Resumen Oftalmólogos		
			$objPHPExcel->createSheet(); 
			
			$objPHPExcel->setActiveSheetIndex(1)->getColumnDimension("A")->setWidth(40);
			$objPHPExcel->setActiveSheetIndex(1)->getColumnDimension("B")->setWidth(30);
			
			$contador_linea = 1;
			$objPHPExcel->setActiveSheetIndex(1)
						->setCellValue("A1", 'OFTALMOLOGO')
						->setCellValue("B1", 'TOTAL GAFAS FORMULADAS');
			$contador_linea++;
			
			$objPHPExcel->getActiveSheet()->getStyle("A1:B1")->getFont()->setBold(true);
			
			foreach($vec_oftalmologos as $clave=>$valor){
				$objPHPExcel->getActiveSheet() 
					->setCellValue("A".$contador_linea, $clave)
					->setCellValue("B".$contador_linea, $valor);	
				$contador_linea++; 
			}
			
			$objPHPExcel->getActiveSheet()->setTitle("Resumen por Oftalmólogo"); 			
		
						
			//Generar la tercera hoja: Resumen Optómetras 			
			$objPHPExcel->createSheet(); 
			
			$objPHPExcel->setActiveSheetIndex(2)->getColumnDimension("A")->setWidth(40);
			$objPHPExcel->setActiveSheetIndex(2)->getColumnDimension("B")->setWidth(30);
			
			$contador_linea = 1;
			$objPHPExcel->setActiveSheetIndex(2)
						->setCellValue("A1", 'OPTOMETRA')
						->setCellValue("B1", 'TOTAL GAFAS FORMULADAS');
			$contador_linea++;
			
			$objPHPExcel->getActiveSheet()->getStyle("A1:B1")->getFont()->setBold(true);
			
			foreach($vec_optometras as $clave=>$valor){
				$objPHPExcel->getActiveSheet() 
					->setCellValue("A".$contador_linea, $clave)
					->setCellValue("B".$contador_linea, $valor);	
				$contador_linea++; 
			}
			
			$objPHPExcel->getActiveSheet()->setTitle("Resumen por Optómetra"); 
			
			
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
			@unlink("./tmp/reporte_formulacion_gafas_".$id_usuario.".xlsx");
			
			// Save Excel 2007 file
			$id_usuario = $_SESSION["idUsuario"];
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
			$objWriter->save("./tmp/reporte_formulacion_gafas_".$id_usuario.".xlsx");
		?>
		<form name="frm_reporte_formulacion_gafas" id="frm_reporte_formulacion_gafas" method="post" action="tmp/reporte_formulacion_gafas_<?php echo($id_usuario); ?>.xlsx">
		</form>
		<script id="ajax" type="text/javascript">
			document.getElementById("frm_reporte_formulacion_gafas").submit();
		</script>
		<?php
			break;
	}
	
	exit;
?>
