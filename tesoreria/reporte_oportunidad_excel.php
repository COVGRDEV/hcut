<?php
	session_start();
	
	require_once("../db/DbCitas.php");
	require_once("../db/DbPagos.php");
	require_once("../db/DbDiagnosticos.php");
	require_once("../db/DbCirugias.php");
	require_once("../db/DbExamenesOptometria.php");
	require_once("../db/DbConvenios.php");
	require_once("../db/DbPlanes.php");
	require_once("../funciones/PHPExcel/Classes/PHPExcel.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/pdf/funciones.php");
	require_once("../funciones/Utilidades.php");
	require_once("../db/DbVariables.php");
	
	$dbCitas = new DbCitas();
	$dbPagos = new DbPagos();
	$dbDiagnosticos = new DbDiagnosticos();
	$dbCirugias = new DbCirugias();
	$dbExamenesOptometria = new DbExamenesOptometria();
	$dbConvenios = new DbConvenios();
	$dbPlanes = new DbPlanes ();
	$funcionesPersona = new FuncionesPersona();
	$utilidades = new Utilidades();
	$dbVariables = new Dbvariables();
	
	
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	
	@$tipoReporte = $utilidades->str_decode($_POST["tipoReporte"]);
	
	switch ($tipoReporte) {
		case "1": //Reporte de oportunidad 
		
			//var_dump($_POST);
			@$fecha_inicial = $utilidades->str_decode($_POST["fechaInicial2"]);
			@$fecha_final = $utilidades->str_decode($_POST["fechaFinal2"]);
			@$id_convenio = $utilidades->str_decode($_POST["convenio2"]);
			@$id_plan = $utilidades->str_decode($_POST["plan"]);
			
			//echo $fechaInicial."#".$fechaFinal."#".$tipo_formato;
			$arr_diferencia = $dbVariables->getDiferenciaFechas($fecha_inicial, $fecha_final, 2);
			$diferencia_dias = intval($arr_diferencia["dias"], 10);
			
			if($diferencia_dias >= 34){
				//Arroja error
				?>
				<script id="ajax" type="text/javascript">
					alert("Existe m\xe1s de un mes entre las fechas seleccionadas");
					window.close();
				</script>
				<?php
				
			}else{	
			
			//Registros con pago
			$lista_oportunidad_1 = $dbCitas->getReporteOportunidad($fecha_inicial, $fecha_final, $id_convenio, $id_plan, 1);
			//Registros sin pago
			$lista_oportunidad_2 = $dbCitas->getReporteOportunidad($fecha_inicial, $fecha_final, $id_convenio, $id_plan, 0);
			
			$arr_oportunidad = array();
			array_push($arr_oportunidad, $lista_oportunidad_1);
			array_push($arr_oportunidad, $lista_oportunidad_2);
			
			$arr_tutulos_hojas = array("Con pago", "Sin pago");
			
			for ($i = 0; $i < 2; $i++) {
				$rta_aux = $arr_oportunidad[$i];
				
				if ($i > 0) {
					$objPHPExcel->createSheet($i);
				}
				$objPHPExcel->setActiveSheetIndex($i);
				$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(13);
				$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(8);
				$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(8);
				$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(12);
				$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(12);
				$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(9);
				$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(12);
				$objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(9);
				$objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(12);
				$objPHPExcel->getActiveSheet()->getColumnDimension("L")->setWidth(35);
				$objPHPExcel->getActiveSheet()->getColumnDimension("M")->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension("N")->setWidth(25);
				$objPHPExcel->getActiveSheet()->getColumnDimension("O")->setWidth(9);
				$objPHPExcel->getActiveSheet()->getColumnDimension("P")->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension("Q")->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension("R")->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension("S")->setWidth(15);

				
				
				$objPHPExcel->getActiveSheet()
						->setCellValue("A1", "NOMBRE COMPLETO DE BENEFICIARIO")
						->setCellValue("B1", "APELLIDOS DE BENEFICIARIO")
						->setCellValue("C1", "DOCUMENTO DE IDENTIDAD")
						->setCellValue("D1", "EDAD")
						->setCellValue("E1", "SEXO")
						->setCellValue("F1", "FECHA EN LA QUE SE SOLICITO EL SERVICIO")
						->setCellValue("G1", "FECHA EN LA QUE SE REPROGRAMÓ EL SERVICIO")
						->setCellValue("H1", "FECHA DE LA ATENCION")
						->setCellValue("I1", "FECHA REPROGRAMACIÓN DE LA ATENCION")
						->setCellValue("J1", "CALCULO DE OPORTUNIDAD")
						->setCellValue("K1", "DIAGNOSTICO CIE 10")
						->setCellValue("L1", "DESCRIPCION DE DIAGNOSTICO")
						->setCellValue("M1", "CODIGO CUPS")
						->setCellValue("N1", "DESCRIPCION DE CUPS")
						->setCellValue("O1", "CANTIDAD")
						->setCellValue("P1", "MEDICO QUE EMITIO LA ORDEN")
						->setCellValue("Q1", "OBSERVACIONES")
						->setCellValue("R1", "NOMBRE CONVENIO")
						->setCellValue("S1", "NOMBRE PLAN");

				
				/*Agrega colores*/
				cellColor("A1", "04B431");
				cellColor("B1", "04B431");
				cellColor("C1", "04B431");
				cellColor("D1", "04B431");
				cellColor("E1", "04B431");
				cellColor("F1", "04B431");
				cellColor("G1", "04B431");
				cellColor("H1", "04B431");
				cellColor("I1", "04B431");
				cellColor("J1", "04B431");
				cellColor("K1", "04B431");
				cellColor("L1", "04B431");
				cellColor("M1", "04B431");
				cellColor("N1", "04B431");
				cellColor("O1", "04B431");
				cellColor("P1", "04B431");
				cellColor("Q1", "04B431");
				cellColor("R1", "04B431");
				cellColor("S1", "04B431");
				
				$contador = 2;
				$edad_aux = "";
				foreach ($rta_aux as $value) {
					//Procesa la edad
					$edad = explode("/", $value["edad_aux"]);
					
					$objPHPExcel->getActiveSheet()
						->setCellValue("A".$contador, $value["nombre_1"]." ".$value["nombre_2"])
						->setCellValue("B".$contador, $value["apellido_1"]." ".$value["apellido_2"])
						->setCellValue("C".$contador, $value["numero_documento"])
						->setCellValue("D".$contador, $edad[0])
						->setCellValue("E".$contador, $value["sexo_aux"])
						->setCellValue("F".$contador, $value["fecha_ori"])
						->setCellValue("G".$contador, $value["fecha_repro"])
						->setCellValue("H".$contador, $value["fecha_cita_ori"]) 
						->setCellValue("I".$contador, $value["fecha_cita_repro"])
						//->setCellValue("J".$contador, "=G".$contador."-F".$contador)//Hace el calculo
						->setCellValue("K".$contador, $value["cod_ciex"])
						->setCellValue("L".$contador, $value["descripcion_diagnostico"])
						->setCellValue("M".$contador, $value["cod_procedimiento"])
						->setCellValue("N".$contador, $value["nombre_procedimiento"])
						->setCellValue("O".$contador, $value["cantidad"])
						->setCellValue("P".$contador, $value["nombre_med_orden"])
					    ->setCellValue("Q".$contador, $value["observacion_cita"]." ".$value["motivo_consulta"])
						->setCellValue("R".$contador, $value["nombre_convenio"])
						->setCellValue("S".$contador, $value["nombre_plan"]);
					$contador++;
				}
				
				// Rename worksheet
				$objPHPExcel->getActiveSheet()->setTitle($arr_tutulos_hojas[$i]);
			}
			
			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);
			
			// Set document properties
			$objPHPExcel->getProperties()->setCreator("OSPS")
					->setLastModifiedBy("OSPS")
					->setTitle("Office 2007 XLSX")
					->setSubject("Office 2007 XLSX")
					->setDescription("Document for Office 2007 XLSX.")
					->setKeywords("office 2007")
					->setCategory("result");
			
			//Se borra el reporte previamente generado por el usuario
			@unlink("./tmp/reporte_oportunidad_".$id_usuario.".xlsx");
			
			// Save Excel 2007 file
			$id_usuario = $_SESSION["idUsuario"];
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
			$objWriter->save("./tmp/reporte_oportunidad_".$id_usuario.".xlsx");
		?>
        <form name="frm_reporte_oportunidad" id="frm_reporte_oportunidad" method="post" action="tmp/reporte_oportunidad_<?php echo($id_usuario); ?>.xlsx">
        </form>
        <script id="ajax" type="text/javascript">
			document.getElementById("frm_reporte_oportunidad").submit();
		</script>
		<?php
			break;
		}
		case "2": //Reporte FOSCAL
		
			
			@$fecha_ini = $utilidades->str_decode($_POST["hdd_fecha_ini_foscal"]);
			@$fecha_fin = $utilidades->str_decode($_POST["hdd_fecha_fin_foscal"]);
			@$id_convenio = $utilidades->str_decode($_POST["hdd_convenio_foscal"]);
			
			//echo $fechaInicial."#".$fechaFinal."#".$tipo_formato;
			$arr_diferencia = $dbVariables->getDiferenciaFechas($fecha_ini, $fecha_fin, 2);
			$diferencia_dias = intval($arr_diferencia["dias"], 10);
			
			if($diferencia_dias >= 34){
				//Arroja error
				?>
				<script id="ajax" type="text/javascript">
					alert("Existe m\xe1s de un mes entre las fechas seleccionadas");
					window.close();
				</script>
				<?php
				
			}else{	
			
			//Se obtienen los componentes de la fecha inicial
			$arr_fecha_ini = explode("-", $fecha_ini);
			$mes_aux = strtoupper($funcionesPersona->obtenerNombreMes($arr_fecha_ini[1], 1));
			$ano_aux = $arr_fecha_ini[0];
			
			/***************/
			/***CONSULTAS***/
			/***************/
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(4);
			$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(3);
			$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(4.5);
			$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(3);
			$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(3);
			$objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension("L")->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension("M")->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension("N")->setWidth(3);
			$objPHPExcel->getActiveSheet()->getColumnDimension("O")->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension("P")->setWidth(3);
			$objPHPExcel->getActiveSheet()->getColumnDimension("Q")->setWidth(11);
			$objPHPExcel->getActiveSheet()->getColumnDimension("R")->setWidth(11);
			$objPHPExcel->getActiveSheet()->getColumnDimension("S")->setWidth(11);
			
			$objPHPExcel->getActiveSheet()
						->setCellValue("A1", "FUNDACIÓN OFTALMOLÓGICA DE SANTANDER")
						->setCellValue("A2", "Clínica Carlos Ardila Lülle")
						->setCellValue("A3", "FOSCAL - VIRGILIO GALVIS RAMÍREZ")
						->setCellValue("A4", "Registro Diario de Consulta Médica")
						->setCellValue("A5", "VIRGILIO GALVIS RAMÍREZ CC 8.276.990")
						->setCellValue("Q5", $mes_aux)
						->setCellValue("Q6", "Año ".$ano_aux);
			
			$objPHPExcel->getActiveSheet()->getStyle("A3:S3")->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->mergeCells("A1:S1");
			$objPHPExcel->getActiveSheet()->mergeCells("A2:S2");
			$objPHPExcel->getActiveSheet()->mergeCells("A3:S3");
			$objPHPExcel->getActiveSheet()->mergeCells("A4:S4");
			$objPHPExcel->getActiveSheet()->mergeCells("A5:I5");
			$objPHPExcel->getActiveSheet()->mergeCells("Q5:S5");
			$objPHPExcel->getActiveSheet()->mergeCells("Q6:S6");
			
			$objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle("A2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle("A3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle("A4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle("A5")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()
						->setCellValue("A7", "Item")
						->setCellValue("B7", "Día")
						->setCellValue("C7", "No Identidad")
						->setCellValue("D7", "EPS/ARS")
						->setCellValue("E7", "Tipo Usuario")
						->setCellValue("F7", "Apellidos y Nombres")
						->setCellValue("G7", "Edad")
						->setCellValue("H7", "Medida Edad")
						->setCellValue("I7", "Sexo")
						->setCellValue("J7", "RIA")
						->setCellValue("K7", "No Factura")
						->setCellValue("L7", "Hoja de admisión")
						->setCellValue("M7", "Cod. Cons")
						->setCellValue("N7", "Causa Ext.")
						->setCellValue("O7", "Cod Dx")
						->setCellValue("P7", "Tipo Dx")
						->setCellValue("Q7", "Valor")
						->setCellValue("R7", "Cuota Mod")
						->setCellValue("S7", "Valor Neto");
			
			//Se rotan algunos encabezados de la tabla
			$objPHPExcel->getActiveSheet()->getStyle("A7")->getAlignment()->setTextRotation(90);
			$objPHPExcel->getActiveSheet()->getStyle("E7")->getAlignment()->setTextRotation(90);
			$objPHPExcel->getActiveSheet()->getStyle("H7")->getAlignment()->setTextRotation(90);
			$objPHPExcel->getActiveSheet()->getStyle("I7")->getAlignment()->setTextRotation(90);
			$objPHPExcel->getActiveSheet()->getStyle("J7")->getAlignment()->setTextRotation(90);
			$objPHPExcel->getActiveSheet()->getStyle("N7")->getAlignment()->setTextRotation(90);
			$objPHPExcel->getActiveSheet()->getStyle("P7")->getAlignment()->setTextRotation(90);
			
			//Se centran los encabezados
			$objPHPExcel->getActiveSheet()->getStyle("A7:S7")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->getStyle("L7")->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->getStyle("A7:S7")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			
			//Se obtiene el listado de consultas
			$lista_pagos_c = $dbPagos->getListaPagosConsultasProcedimientos($fecha_ini, $fecha_fin, $id_convenio, "C");
			
			$id_detalle_ant = "0";
			$num_linea = 8;
			foreach($lista_pagos_c as $pago_aux) {
				if ($pago_aux["id_detalle_precio"] != $id_detalle_ant) {
					$id_detalle_ant = $pago_aux["id_detalle_precio"];
					
					$dia_aux = substr($pago_aux["fecha_pago_t"], 0, 2);
					$arr_edad = explode("/", $pago_aux["edad"]);
					
					//Se obtiene el diagnóstico proncipal
					$lista_diagnosticos = $dbDiagnosticos->getHcDiagnostico($pago_aux["id_hc"]);
					$cod_ciex_aux = "";
					if (count($lista_diagnosticos) > 0) {
						$cod_ciex_aux = $lista_diagnosticos[0]["codciexori"];
					}
					
					$objPHPExcel->getActiveSheet()
								->setCellValue("A".$num_linea, $num_linea - 7)
								->setCellValue("B".$num_linea, $dia_aux)
								->setCellValue("C".$num_linea, $pago_aux["numero_documento"])
								->setCellValue("D".$num_linea, $pago_aux["nombre_plan"])
								->setCellValue("E".$num_linea, "")
								->setCellValue("F".$num_linea, $funcionesPersona->obtenerNombreCompleto($pago_aux["apellido_1"], $pago_aux["apellido_2"], $pago_aux["nombre_1"], $pago_aux["nombre_2"]))
								->setCellValue("G".$num_linea, $arr_edad[0])
								->setCellValue("H".$num_linea, $arr_edad[1])
								->setCellValue("I".$num_linea, $pago_aux["sexo"])
								->setCellValue("J".$num_linea, "")
								->setCellValue("K".$num_linea, "")
								->setCellValue("L".$num_linea, $pago_aux["num_autorizacion"])
								->setCellValue("M".$num_linea, $pago_aux["cod_procedimiento"])
								->setCellValue("N".$num_linea, "")
								->setCellValue("O".$num_linea, $cod_ciex_aux)
								->setCellValue("P".$num_linea, "")
								->setCellValue("Q".$num_linea, $pago_aux["valor"])
								->setCellValue("R".$num_linea, $pago_aux["valor_cuota"])
								->setCellValue("S".$num_linea, $pago_aux["valor"] - $pago_aux["valor_cuota"]);
					
					$objPHPExcel->getActiveSheet()->getStyle("A".$num_linea.":S".$num_linea)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					
					$num_linea++;
				}
			}
			
			//Se renombra la hoja
			$objPHPExcel->getActiveSheet()->setTitle("Consultas");
			
			/********************/
			/***PROCEDIMIENTOS***/
			/********************/
			$objPHPExcel->createSheet();
			$objPHPExcel->setActiveSheetIndex(1);
			$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(4);
			$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(3);
			$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(4.5);
			$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(3);
			$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(3);
			$objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension("L")->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension("M")->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension("N")->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension("O")->setWidth(4);
			$objPHPExcel->getActiveSheet()->getColumnDimension("P")->setWidth(3);
			$objPHPExcel->getActiveSheet()->getColumnDimension("Q")->setWidth(3);
			$objPHPExcel->getActiveSheet()->getColumnDimension("R")->setWidth(11);
			
			$objPHPExcel->getActiveSheet()
						->setCellValue("A1", "FUNDACIÓN OFTALMOLÓGICA DE SANTANDER")
						->setCellValue("A2", "Clínica Carlos Ardila Lülle")
						->setCellValue("A3", "FOSCAL - VIRGILIO GALVIS RAMÍREZ")
						->setCellValue("A4", "Registro Diario de Procedimiento")
						->setCellValue("B5", "Mes:")
						->setCellValue("D5", $mes_aux)
						->setCellValue("B6", "Año")
						->setCellValue("D6", $ano_aux);
			
			$objPHPExcel->getActiveSheet()->getStyle("A3:R3")->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->mergeCells("A1:R1");
			$objPHPExcel->getActiveSheet()->mergeCells("A2:R2");
			$objPHPExcel->getActiveSheet()->mergeCells("A3:R3");
			$objPHPExcel->getActiveSheet()->mergeCells("A4:R4");
			$objPHPExcel->getActiveSheet()->mergeCells("B5:C5");
			$objPHPExcel->getActiveSheet()->mergeCells("B6:C6");
			
			$objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle("A2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle("A3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle("A4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle("D6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			
			$objPHPExcel->getActiveSheet()
						->setCellValue("A7", "Item")
						->setCellValue("B7", "Día")
						->setCellValue("C7", "No Identidad")
						->setCellValue("D7", "EPS/ARS")
						->setCellValue("E7", "Tipo Usuario")
						->setCellValue("F7", "Apellidos y Nombres")
						->setCellValue("G7", "Edad")
						->setCellValue("H7", "Medida Edad")
						->setCellValue("I7", "Sexo")
						->setCellValue("J7", "RIA")
						->setCellValue("K7", "No Factura")
						->setCellValue("L7", "Hoja de admisión")
						->setCellValue("M7", "Cod. Proc.")
						->setCellValue("N7", "Cod Dx")
						->setCellValue("O7", "Ojo")
						->setCellValue("P7", "Causa de Proc. Ext.")
						->setCellValue("Q7", "Finalidad Proc.")
						->setCellValue("R7", "Valor");
			
			//Se rotan algunos encabezados de la tabla
			$objPHPExcel->getActiveSheet()->getStyle("A7")->getAlignment()->setTextRotation(90);
			$objPHPExcel->getActiveSheet()->getStyle("E7")->getAlignment()->setTextRotation(90);
			$objPHPExcel->getActiveSheet()->getStyle("H7")->getAlignment()->setTextRotation(90);
			$objPHPExcel->getActiveSheet()->getStyle("I7")->getAlignment()->setTextRotation(90);
			$objPHPExcel->getActiveSheet()->getStyle("J7")->getAlignment()->setTextRotation(90);
			$objPHPExcel->getActiveSheet()->getStyle("P7")->getAlignment()->setTextRotation(90);
			$objPHPExcel->getActiveSheet()->getStyle("Q7")->getAlignment()->setTextRotation(90);
			
			//Se centran los encabezados
			$objPHPExcel->getActiveSheet()->getStyle("A7:R7")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->getStyle("L7")->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->getStyle("A7:R7")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			
			//Se obtiene el listado de procedimientos
			$lista_pagos_p = $dbPagos->getListaPagosConsultasProcedimientos($fecha_ini, $fecha_fin, $id_convenio, "P");
			
			$id_detalle_ant = "0";
			$num_linea = 8;
			foreach($lista_pagos_p as $pago_aux) {
				if ($pago_aux["id_detalle_precio"] != $id_detalle_ant) {
					$id_detalle_ant = $pago_aux["id_detalle_precio"];
					
					$dia_aux = substr($pago_aux["fecha_pago_t"], 0, 2);
					$arr_edad = explode("/", $pago_aux["edad"]);
					
					$ojo_aux = "";
					$amb_rea_aux = "1";
					$fin_pro_aux = "1";
					if ($pago_aux["id_hc_cx"] != "") {
						//Se obtiene el diagnóstico principal
						$lista_diagnosticos = $dbDiagnosticos->getHcDiagnostico($pago_aux["id_hc_cx"]);
						
						//Se busca el ojo sobre el que se realizó el procedimiento
						$cx_proc_obj = $dbCirugias->get_cirugia_procedimiento($pago_aux["id_hc_cx"], $pago_aux["cod_procedimiento"]);
						if (isset($cx_proc_obj["ojo"])) {
							$ojo_aux = $cx_proc_obj["ojo"];
						}
						
						//Se buscan los datos de ámbito y finalidad
						$cirugia_obj = $dbCirugias->get_cirugia($pago_aux["id_hc_cx"]);
						if (isset($cirugia_obj["id_hc"])) {
							$amb_rea_aux = $cirugia_obj["cod_amb_rea"];
							$fin_pro_aux = $cirugia_obj["cod_fin_pro"];
						}
					} else {
						//Se obtiene el diagnóstico principal
						$lista_diagnosticos = $dbDiagnosticos->getHcDiagnostico($pago_aux["id_hc"]);
						
						//Se busca el ojo sobre el que se realizó el procedimiento
						$examen_obj = $dbExamenesOptometria->get_examen_optometria_hc_proc($pago_aux["id_hc"], $pago_aux["cod_procedimiento"]);
						if (isset($examen_obj["ojo"])) {
							$ojo_aux = $examen_obj["ojo"];
						}
					}
					$cod_ciex_aux = "";
					if (count($lista_diagnosticos) > 0) {
						$cod_ciex_aux = $lista_diagnosticos[0]["codciexori"];
					}
					
					$objPHPExcel->getActiveSheet()
								->setCellValue("A".$num_linea, $num_linea - 7)
								->setCellValue("B".$num_linea, $dia_aux)
								->setCellValue("C".$num_linea, $pago_aux["numero_documento"])
								->setCellValue("D".$num_linea, $pago_aux["nombre_plan"])
								->setCellValue("E".$num_linea, "")
								->setCellValue("F".$num_linea, $funcionesPersona->obtenerNombreCompleto($pago_aux["apellido_1"], $pago_aux["apellido_2"], $pago_aux["nombre_1"], $pago_aux["nombre_2"]))
								->setCellValue("G".$num_linea, $arr_edad[0])
								->setCellValue("H".$num_linea, $arr_edad[1])
								->setCellValue("I".$num_linea, $pago_aux["sexo"])
								->setCellValue("J".$num_linea, "")
								->setCellValue("K".$num_linea, "")
								->setCellValue("L".$num_linea, $pago_aux["num_autorizacion"])
								->setCellValue("M".$num_linea, $pago_aux["cod_procedimiento"])
								->setCellValue("N".$num_linea, $cod_ciex_aux)
								->setCellValue("O".$num_linea, $ojo_aux)
								->setCellValue("P".$num_linea, $amb_rea_aux)
								->setCellValue("Q".$num_linea, $fin_pro_aux)
								->setCellValue("R".$num_linea, $pago_aux["valor"]);
					
					$objPHPExcel->getActiveSheet()->getStyle("A".$num_linea.":R".$num_linea)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					
					$num_linea++;
				}
			}
			
			//Se renombra la hoja
			$objPHPExcel->getActiveSheet()->setTitle("Procedimientos");
			
			/*********************/
			/***OTROS SERVICIOS***/
			/*********************/
			$objPHPExcel->createSheet();
			$objPHPExcel->setActiveSheetIndex(2);
			$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(4);
			$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(3);
			$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(4.5);
			$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(3);
			$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(3);
			$objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension("L")->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension("M")->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension("N")->setWidth(35);
			$objPHPExcel->getActiveSheet()->getColumnDimension("O")->setWidth(11);
			$objPHPExcel->getActiveSheet()->getColumnDimension("P")->setWidth(11);
			$objPHPExcel->getActiveSheet()->getColumnDimension("Q")->setWidth(11);
			
			$objPHPExcel->getActiveSheet()
						->setCellValue("A1", "FUNDACIÓN OFTALMOLÓGICA DE SANTANDER")
						->setCellValue("A2", "Clínica Carlos Ardila Lülle")
						->setCellValue("A3", "FOSCAL - VIRGILIO GALVIS RAMÍREZ")
						->setCellValue("A4", "Registro Diario de Otros Servicios")
						->setCellValue("B5", "Mes:")
						->setCellValue("D5", $mes_aux)
						->setCellValue("B6", "Año")
						->setCellValue("D6", $ano_aux);
			
			$objPHPExcel->getActiveSheet()->getStyle("A3:Q3")->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->mergeCells("A1:Q1");
			$objPHPExcel->getActiveSheet()->mergeCells("A2:Q2");
			$objPHPExcel->getActiveSheet()->mergeCells("A3:Q3");
			$objPHPExcel->getActiveSheet()->mergeCells("A4:Q4");
			$objPHPExcel->getActiveSheet()->mergeCells("B5:C5");
			$objPHPExcel->getActiveSheet()->mergeCells("B6:C6");
			
			$objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle("A2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle("A3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle("A4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle("D6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			
			$objPHPExcel->getActiveSheet()
						->setCellValue("A7", "Item")
						->setCellValue("B7", "Día")
						->setCellValue("C7", "No Identidad")
						->setCellValue("D7", "EPS/ARS")
						->setCellValue("E7", "Tipo Usuario")
						->setCellValue("F7", "Apellidos y Nombres")
						->setCellValue("G7", "Edad")
						->setCellValue("H7", "Medida Edad")
						->setCellValue("I7", "Sexo")
						->setCellValue("J7", "RIA")
						->setCellValue("K7", "No Factura")
						->setCellValue("L7", "Hoja de admisión")
						->setCellValue("M7", "Cod. Serv.")
						->setCellValue("N7", "Nombre Servicio")
						->setCellValue("O7", "Cantidad")
						->setCellValue("P7", "Valor unitario")
						->setCellValue("Q7", "Valor total");
			
			//Se rotan algunos encabezados de la tabla
			$objPHPExcel->getActiveSheet()->getStyle("A7")->getAlignment()->setTextRotation(90);
			$objPHPExcel->getActiveSheet()->getStyle("E7")->getAlignment()->setTextRotation(90);
			$objPHPExcel->getActiveSheet()->getStyle("H7")->getAlignment()->setTextRotation(90);
			$objPHPExcel->getActiveSheet()->getStyle("I7")->getAlignment()->setTextRotation(90);
			$objPHPExcel->getActiveSheet()->getStyle("J7")->getAlignment()->setTextRotation(90);
			
			//Se centran los encabezados
			$objPHPExcel->getActiveSheet()->getStyle("A7:Q7")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->getStyle("L7")->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->getStyle("P7")->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->getStyle("A7:Q7")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			
			//Se obtiene el listado de otros servicios
			$lista_pagos_i = $dbPagos->getListaPagosConsultasInsumos($fecha_ini, $fecha_fin, $id_convenio);
			
			$id_detalle_ant = "0";
			$num_linea = 8;
			foreach($lista_pagos_i as $pago_aux) {
				if ($pago_aux["id_detalle_precio"] != $id_detalle_ant) {
					$id_detalle_ant = $pago_aux["id_detalle_precio"];
					
					$dia_aux = substr($pago_aux["fecha_pago_t"], 0, 2);
					$arr_edad = explode("/", $pago_aux["edad"]);
					
					$objPHPExcel->getActiveSheet()
								->setCellValue("A".$num_linea, $num_linea - 7)
								->setCellValue("B".$num_linea, $dia_aux)
								->setCellValue("C".$num_linea, $pago_aux["numero_documento"])
								->setCellValue("D".$num_linea, $pago_aux["nombre_plan"])
								->setCellValue("E".$num_linea, "")
								->setCellValue("F".$num_linea, $funcionesPersona->obtenerNombreCompleto($pago_aux["apellido_1"], $pago_aux["apellido_2"], $pago_aux["nombre_1"], $pago_aux["nombre_2"]))
								->setCellValue("G".$num_linea, $arr_edad[0])
								->setCellValue("H".$num_linea, $arr_edad[1])
								->setCellValue("I".$num_linea, $pago_aux["sexo"])
								->setCellValue("J".$num_linea, "")
								->setCellValue("K".$num_linea, "")
								->setCellValue("L".$num_linea, $pago_aux["num_autorizacion"])
								->setCellValue("M".$num_linea, $pago_aux["cod_insumo"])
								->setCellValue("N".$num_linea, $pago_aux["nombre_insumo"])
								->setCellValue("O".$num_linea, $pago_aux["cantidad"])
								->setCellValue("P".$num_linea, $pago_aux["valor"])
								->setCellValue("Q".$num_linea, intval($pago_aux["cantidad"], 10) * intval($pago_aux["valor"], 10));
					
					$objPHPExcel->getActiveSheet()->getStyle("A".$num_linea.":Q".$num_linea)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					
					$num_linea++;
				}
			}
			
			//Se renombra la hoja
			$objPHPExcel->getActiveSheet()->setTitle("Otros Servicios");
			
			// Set document properties
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
			@unlink("./tmp/reporte_foscal_".$id_usuario.".xlsx");
			
			// Save Excel 2007 file
			$id_usuario = $_SESSION["idUsuario"];
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
			$objWriter->save("./tmp/reporte_foscal_".$id_usuario.".xlsx");
		?>
        <form name="frm_reporte_foscal" id="frm_reporte_foscal" method="post" action="tmp/reporte_foscal_<?php echo($id_usuario); ?>.xlsx">
        </form>
        <script id="ajax" type="text/javascript">
			document.getElementById("frm_reporte_foscal").submit();
		</script>
		<?php
			break;
		}
		case "3": //Reporte para Colpatria
			@$fecha_ini = $utilidades->str_decode($_POST["hdd_fecha_ini_colpatria_1"]);
			@$fecha_fin = $utilidades->str_decode($_POST["hdd_fecha_fin_colpatria_1"]);
			@$id_convenio = $utilidades->str_decode($_POST["hdd_convenio_colpatria_1"]);
			/*
			//echo $fechaInicial."#".$fechaFinal."#".$tipo_formato;
			$arr_diferencia = $dbVariables->getDiferenciaFechas($fecha_ini, $fecha_fin, 2);
			$diferencia_dias = intval($arr_diferencia["dias"], 10);
			
			if($diferencia_dias >= 34){
				//Arroja error
				?>
				<script id="ajax" type="text/javascript">
					alert("Existe m\xe1s de un mes entre las fechas seleccionadas");
					window.close();
				</script>
				<?php
				
			}else{	*/	
			//Se obtienen los datos del convenio y de los pagos
			$convenio_obj = $dbConvenios->getConvenio($id_convenio);
			$lista_pagos = $dbPagos->getListaPagosConsultasConvenioFechas($fecha_ini, $fecha_fin, $id_convenio);
			
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(35);
			$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(50);
			
			$objPHPExcel->getActiveSheet()
						->setCellValue("A1", strtoupper($convenio_obj["tipo_documento"]))
						->setCellValue("B1", $convenio_obj["numero_documento"])
						->setCellValue("A2", $convenio_obj["nombre_convenio"]);
			
			$objPHPExcel->getActiveSheet()
						->setCellValue("A4", "CONSULTAS")
						->setCellValue("A5", $fecha_ini." - ".$fecha_fin);
			
			$objPHPExcel->getActiveSheet()
						->setCellValue("A6", "Nombre")
						->setCellValue("B6", "No. Carnet")
						->setCellValue("C6", "Fecha")
						->setCellValue("D6", "1Vez")
						->setCellValue("E6", "Diagnóstico");
			
			//Se centran los encabezados
			$objPHPExcel->getActiveSheet()->getStyle("A6:E6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->getStyle("A1:E6")->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle("A6:E6")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			
			$id_detalle_ant = "0";
			$num_linea = 7;
			for ($i = 0; $i < count($lista_pagos); $i++) {
				$pago_aux = $lista_pagos[$i];
				
				//Se verifica si hay consultas anteriores para el paciente
				$reg_aux = $dbPagos->getCantidadPagosConsultasAnteriores($id_convenio, $pago_aux["id_paciente"], $pago_aux["fecha_pago"]);
				$marca_1vez = "X";
				if (isset($reg_aux["cantidad"]) && $reg_aux["cantidad"] != "0") {
					$marca_1vez = "";
				}
				
				$objPHPExcel->getActiveSheet()
							->setCellValue("A".$num_linea, $funcionesPersona->obtenerNombreCompleto($pago_aux["nombre_1"], $pago_aux["nombre_2"], $pago_aux["apellido_1"], $pago_aux["apellido_2"]))
							->setCellValue("B".$num_linea, $pago_aux["num_carnet"])
							->setCellValue("C".$num_linea, $pago_aux["fecha_pago_t"])
							->setCellValue("D".$num_linea, $marca_1vez)
							->setCellValue("E".$num_linea, $pago_aux["nombre_ciex"]);
				
				$objPHPExcel->getActiveSheet()->getStyle("A".$num_linea.":E".$num_linea)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					
				$num_linea++;
			}
			
			//Se centran unas columnas
			$objPHPExcel->getActiveSheet()->getStyle("B7:D".($num_linea - 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			//Se renombra la hoja
			$objPHPExcel->getActiveSheet()->setTitle("Colpatria");
			
			// Set document properties
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
			@unlink("./tmp/reporte_colpatria_".$id_usuario.".xlsx");
			
			// Save Excel 2007 file
			$id_usuario = $_SESSION["idUsuario"];
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
			$objWriter->save("./tmp/reporte_colpatria_".$id_usuario.".xlsx");
		?>
        <form name="frm_reporte_colpatria" id="frm_reporte_colpatria" method="post" action="tmp/reporte_colpatria_<?php echo($id_usuario); ?>.xlsx">
        </form>
        <script id="ajax" type="text/javascript">
			document.getElementById("frm_reporte_colpatria").submit();
		</script>
		<?php
			break;
		//}
		case "4": //Reporte para Colpatria 2
			@$fecha_ini = $utilidades->str_decode($_POST["hdd_fecha_ini_colpatria_2"]);
			@$fecha_fin = $utilidades->str_decode($_POST["hdd_fecha_fin_colpatria_2"]);
			@$id_convenio = $utilidades->str_decode($_POST["hdd_convenio_colpatria_2"]);
			
			/*
			//echo $fechaInicial."#".$fechaFinal."#".$tipo_formato;
			$arr_diferencia = $dbVariables->getDiferenciaFechas($fecha_ini, $fecha_fin, 2);
			$diferencia_dias = intval($arr_diferencia["dias"], 10);
			
			if($diferencia_dias >= 34){
				//Arroja error
				?>
				<script id="ajax" type="text/javascript">
					alert("Existe m\xe1s de un mes entre las fechas seleccionadas");
					window.close();
				</script>
				<?php
				
			}else{	*/
			//Se obtienen los datos del convenio y de los pagos
			$convenio_obj = $dbConvenios->getConvenio($id_convenio);
			$lista_pagos = $dbPagos->getListaPagosProcedimientosConvenioFechas($fecha_ini, $fecha_fin, $id_convenio, 1);
			
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(18);
			$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(16);
			$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(14);
			$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(20);
			
			$objPHPExcel->getActiveSheet()
						->setCellValue("A1", "Entidad")
						->setCellValue("B1", $convenio_obj["nombre_convenio"])
						->setCellValue("A2", "Fechas")
						->setCellValue("B2", $fecha_ini." - ".$fecha_fin);
			
			$objPHPExcel->getActiveSheet()
						->setCellValue("A4", "CARNET")
						->setCellValue("B4", "APELLIDOS Y NOMBRES DEL USUARIO")
						->setCellValue("C4", "IDENTIFICACIÓN")
						->setCellValue("D4", "FECHA DE ATENCIÓN")
						->setCellValue("E4", "CUPS")
						->setCellValue("F4", "DIAGNÓSTICO CIE 10")
						->setCellValue("G4", "DIAGNÓSTICO EN LETRAS")
						->setCellValue("H4", "VALOR BRUTO POR SERVICIO PRESTADO")
						->setCellValue("I4", "VALOR COMPROBANTE DE ATENCIÓN")
						->setCellValue("J4", "VALOR NETO");
			
			//Se centran los encabezados
			$objPHPExcel->getActiveSheet()->getStyle("A4:J4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle("A4:J4")->getAlignment()->setWrapText(true);
			
			$objPHPExcel->getActiveSheet()->getStyle("A1:J4")->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle("A4:J4")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			
			$id_detalle_ant = "0";
			$num_linea = 5;
			for ($i = 0; $i < count($lista_pagos); $i++) {
				$pago_aux = $lista_pagos[$i];
				
				$objPHPExcel->getActiveSheet()
							->setCellValue("A".$num_linea, $pago_aux["num_carnet"])
							->setCellValue("B".$num_linea, $funcionesPersona->obtenerNombreCompleto($pago_aux["apellido_1"], $pago_aux["apellido_2"], $pago_aux["nombre_1"], $pago_aux["nombre_2"]))
							->setCellValue("C".$num_linea, $pago_aux["numero_documento"])
							->setCellValue("D".$num_linea, $pago_aux["fecha_pago_t"])
							->setCellValue("E".$num_linea, $pago_aux["cod_procedimiento"])
							->setCellValue("F".$num_linea, $pago_aux["cod_ciex"])
							->setCellValue("G".$num_linea, $pago_aux["nombre_ciex"])
							->setCellValue("H".$num_linea, floatval($pago_aux["valor"]))
							->setCellValue("I".$num_linea, floatval($pago_aux["valor_cuota"]))
							->setCellValue("J".$num_linea, floatval($pago_aux["valor"]) - floatval($pago_aux["valor_cuota"]));
				
				$objPHPExcel->getActiveSheet()->getStyle("A".$num_linea.":J".$num_linea)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					
				$num_linea++;
			}
			
			//Se renombra la hoja
			$objPHPExcel->getActiveSheet()->setTitle("Colpatria");
			
			// Set document properties
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
			@unlink("./tmp/reporte_colpatria_2_".$id_usuario.".xlsx");
			
			// Save Excel 2007 file
			$id_usuario = $_SESSION["idUsuario"];
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
			$objWriter->save("./tmp/reporte_colpatria_2_".$id_usuario.".xlsx");
		?>
        <form name="frm_reporte_colpatria_2" id="frm_reporte_colpatria_2" method="post" action="tmp/reporte_colpatria_2_<?php echo($id_usuario); ?>.xlsx">
        </form>
        <script id="ajax" type="text/javascript">
			document.getElementById("frm_reporte_colpatria_2").submit();
		</script>
		<?php
			break;
		//}
	}
	function cellColor($cells,$color){
		global $objPHPExcel;
		$objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()
			->applyFromArray(array("type" => PHPExcel_Style_Fill::FILL_SOLID,
			"startcolor" => array("rgb" => $color)
			));
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
	
		$hora_res = "";
		if ($hora > 12) {
			$hora = $hora - 12;
			$hora_res = $hora.":".$minutos." PM";
		} else {
			$hora_res = $hora.":".$minutos." AM";
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
