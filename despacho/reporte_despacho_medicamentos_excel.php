<?php
	session_start();
	
	require_once("../db/DbPagos.php");
	require_once("../funciones/PHPExcel/Classes/PHPExcel.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/pdf/funciones.php");
	
	$dbPagos = new DbPagos();
	
	$funciones_persona = new FuncionesPersona();
	
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	
	$tipoReporte = $_POST["tipoReporte"];
	
	switch ($tipoReporte) {
		case "1": //Reporte general
			require_once("../db/DbTiposPago.php");
			require_once("../db/DbConvenios.php");
			require_once("../db/DbPlanes.php");
			require_once("../db/DbMaestroProcedimientos.php");
			require_once("../db/DbMaestroMedicamentos.php");
			require_once("../db/DbMaestroInsumos.php");
			require_once("../db/DbUsuarios.php");
			require_once("../db/DbListas.php");
			require_once("../funciones/Utilidades.php");
			require_once '../db/DbVariables.php';
			require_once("../db/DbFormulasMedicamentos.php");
			
			$utilidades = new Utilidades();
			$dbPagos = new DbPagos();
			$dbTiposPago = new DbTiposPago();
			$dbConvenios = new DbConvenios();
			$dbPlanes = new DbPlanes();
			$dbMaestroInsumos = new DbMaestroInsumos();
			$dbUsuarios = new DbUsuarios();
			$dbListas = new DbListas(); 
			$dbVariables = new DbVariables();
			$dbFormulasMedicamentos = new DbFormulasMedicamentos();
		
			$fechaInicial = $utilidades->str_decode($_POST["hddfechaInicial"]);
			$fechaFinal= $utilidades->str_decode($_POST["hddfechaFinal"]);
			$id_convenio = $utilidades->str_decode($_POST["hddconvenio"]);
			$id_plan = $utilidades->str_decode($_POST["hddplan"]);
			
	        /*echo $fechaInicial." ";
			echo $fechaFinal." ";
			echo $id_convenio." ";
			echo $id_plan." ";*/
			
			$arr_diferencia = $dbVariables->getDiferenciaFechas($fechaInicial, $fechaFinal, 2);
			$diferencia_dias = intval($arr_diferencia["dias"], 10);
			
			if($diferencia_dias >= 34){
				//Mostrar mensaje de error
				?>
					<script id="ajax" type="text/javascript">
						alert("Existe m\xe1s de un mes entre las fechas seleccionadas");	
						window.close();
					</script>
				<?php
			}else{
				//Se obtiene el listado de medicamentos
				$lista_medicamentos = $dbFormulasMedicamentos->getMedicamentosFechaConvenioPlan($fechaInicial, $fechaFinal,$id_convenio,$id_plan);
								
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("A")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("B")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("C")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("D")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("E")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("F")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("G")->setWidth(26);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("H")->setWidth(26);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("I")->setWidth(40);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("J")->setWidth(40);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("K")->setWidth(40);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("L")->setWidth(40);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("M")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("N")->setWidth(22);
				
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
							->setCellValue("A".$contador_linea, "FECHA DE FORMULACIÓN")
							->setCellValue("B".$contador_linea, "SEDE")
							->setCellValue("C".$contador_linea, "NUMERO DE DOCUMENTO")
							->setCellValue("D".$contador_linea, "PRIMER NOMBRE")
							->setCellValue("E".$contador_linea, "SEGUNDO NOMBRE")
							->setCellValue("F".$contador_linea, "PRIMER APELLIDO")
							->setCellValue("G".$contador_linea, "SEGUNDO APELLIDO 2")
							->setCellValue("H".$contador_linea, "NOMBRE DEL MEDICO")
							->setCellValue("I".$contador_linea, "CÓDIGO DEL MEDICAMENTO")
							->setCellValue("J".$contador_linea, "NOMBRE COMERCIAL DEL MEDICAMENTO")
							->setCellValue("K".$contador_linea, "NOMBRE GENERICO DEL MEDICAMENTO")
							->setCellValue("L".$contador_linea, "PRESENTACIÓN DEL MEDICAMENTO")
							->setCellValue("M".$contador_linea, "CANTIDAD ORDEN")
							->setCellValue("N".$contador_linea, "CANTIDAD ENTREGADA");
				
				$objPHPExcel->getActiveSheet()->getStyle("A".$contador_linea.":O".$contador_linea)->getFont()->setBold(true);
				
				$contador_linea++;
					
				foreach ($lista_medicamentos as $reporte_medicamentos) {
			
					$objPHPExcel->getActiveSheet()
						->setCellValue("A".$contador_linea, $reporte_medicamentos["fecha_formula"])
						->setCellValue("B".$contador_linea, $reporte_medicamentos["sede"])
						->setCellValue("C".$contador_linea, $reporte_medicamentos["numero_documento"])
						->setCellValue("D".$contador_linea, $reporte_medicamentos["nombre_1"])
						->setCellValue("E".$contador_linea, $reporte_medicamentos["nombre_2"])
						->setCellValue("F".$contador_linea, $reporte_medicamentos["apellido_1"])
						->setCellValue("G".$contador_linea, $reporte_medicamentos["apellido_2"])
						->setCellValue("H".$contador_linea, $reporte_medicamentos["nombre_medico"])
						->setCellValue("I".$contador_linea, $reporte_medicamentos["cod_medicamento"])
						->setCellValue("J".$contador_linea, $reporte_medicamentos["nombre_comercial"])
						->setCellValue("K".$contador_linea, $reporte_medicamentos["nombre_generico"])
						->setCellValue("L".$contador_linea,	$reporte_medicamentos["presentacion"])
						->setCellValue("M".$contador_linea, $reporte_medicamentos["cantidad_orden"])
						->setCellValue("N".$contador_linea, $reporte_medicamentos["cantidad_entregada"]);
					
						$contador_linea++;
					
					}
				//Se renombra la hoja actual
				$objPHPExcel->getActiveSheet()->setTitle("Reporte de Medicamentos");
				
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
				@unlink("./tmp/reporte_medicamentos_".$id_usuario.".xlsx");
				
				// Save Excel 2007 file
				$id_usuario = $_SESSION["idUsuario"];
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
				$objWriter->save("./tmp/reporte_medicamentos_".$id_usuario.".xlsx");
			?>
			<form name="frm_reporte_medicamentos" id="frm_reporte_medicamentos" method="post" action="tmp/reporte_medicamentos_<?php echo($id_usuario); ?>.xlsx">
			</form>
			<script id="ajax" type="text/javascript">
				document.getElementById("frm_reporte_medicamentos").submit();
			</script>
			<?php
				
			}
			
			break;
	}
	exit;
?>
