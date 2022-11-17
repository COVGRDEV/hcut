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
			require_once("../db/DbAdmision.php");
			
			$utilidades = new Utilidades();
			$dbConvenios = new DbConvenios();
			$dbPlanes = new DbPlanes();
			$dbAdmision = new DbAdmision();
			$dbUsuarios = new DbUsuarios();
			$dbListas = new DbListas(); 
			$dbVariables = new DbVariables();
			
			//var_dump($_POST);
			$fechaInicial = $utilidades->str_decode($_POST["hddfechaInicial"]);
			$fechaFinal= $utilidades->str_decode($_POST["hddfechaFinal"]);
			$id_convenio = $utilidades->str_decode($_POST["hddconvenio"]);
			$id_plan = $utilidades->str_decode($_POST["hddplan"]);
			$id_lugar_cita = $utilidades->str_decode($_POST["hddsede"]);
			
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
				$oport_aten = $dbAdmision->get_oportunidad_atencion($fechaInicial,$fechaFinal, $id_convenio, $id_plan, $id_lugar_cit);
								
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("A")->setWidth(40);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("B")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("C")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("D")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("E")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("F")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("G")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("H")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("I")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("J")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("K")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("L")->setWidth(22);
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
				if ($id_lugar_cita != "") {
					
					$lugar_obj = $dbListas->getDetalle($id_lugar);
					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue("A".$contador_linea, "Lugar de la cita:")
								->setCellValue("B".$contador_linea, $lugar_obj["nombre_detalle"]);
					$contador_linea++;
				}
				$contador_linea++;
				
				$objPHPExcel->getActiveSheet()
							->setCellValue("A".$contador_linea, "TIPO DE CITA")
							->setCellValue("B".$contador_linea, "TIEMPO OPTOMETRÍA")
							->setCellValue("C".$contador_linea, "TIEMPO OFTALMOLOGÍA")
							->setCellValue("D".$contador_linea, "SEDE")
							->setCellValue("E".$contador_linea, "TIPO DE DOCUMENTO")
							->setCellValue("F".$contador_linea, "NÚMERO DE DOCUMENTO")
							->setCellValue("G".$contador_linea, "PRIMER NOMBRE")
							->setCellValue("H".$contador_linea, "SEGUNDO NOMBRE")
							->setCellValue("I".$contador_linea, "PRIMER APELLIDO")
							->setCellValue("J".$contador_linea, "SEGUNDO APELLIDO")
							->setCellValue("K".$contador_linea, "NOMBRE DEL CONVENIO")
							->setCellValue("L".$contador_linea, "NOMBRE DEL PLAN");
						
				
				$objPHPExcel->getActiveSheet()->getStyle("A".$contador_linea.":V".$contador_linea)->getFont()->setBold(true);
				
				$contador_linea++;
					
				foreach ($oport_aten as $datos_oportunidad_atencion) {
			
					$objPHPExcel->getActiveSheet()
						->setCellValue("A".$contador_linea, $datos_oportunidad_atencion["nombre_tipo_cita"])
						->setCellValue("B".$contador_linea, $datos_oportunidad_atencion["tiempo_opt"])
						->setCellValue("C".$contador_linea, $datos_oportunidad_atencion["tiempo_oftan"])
						->setCellValue("D".$contador_linea,	$datos_oportunidad_atencion["sede"])
						->setCellValue("E".$contador_linea,	$datos_oportunidad_atencion["tipo_documento"])
						->setCellValue("F".$contador_linea,	$datos_oportunidad_atencion["numero_documento"])
						->setCellValue("G".$contador_linea,	$datos_oportunidad_atencion["nombre_1"])
						->setCellValue("H".$contador_linea,	$datos_oportunidad_atencion["nombre_2"])
						->setCellValue("I".$contador_linea,	$datos_oportunidad_atencion["apellido_1"])
						->setCellValue("J".$contador_linea,	$datos_oportunidad_atencion["apellido_2"])
						->setCellValue("K".$contador_linea,	$datos_oportunidad_atencion["nombre_convenio"])
						->setCellValue("L".$contador_linea,	$datos_oportunidad_atencion["nombre_plan"]);
		
						$contador_linea++;
					
					}
				//Se renombra la hoja actual
				$objPHPExcel->getActiveSheet()->setTitle("Rep oport atencion");
				
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
				@unlink("./tmp/reporte_oportunidad_atencion_".$id_usuario.".xlsx");
				
				// Save Excel 2007 file
				$id_usuario = $_SESSION["idUsuario"];
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
				$objWriter->save("./tmp/reporte_oportunidad_atencion_".$id_usuario.".xlsx");
			?>
			<form name="frm_reporte_oportunidad_atencion" id="frm_reporte_oportunidad_atencion" method="post" action="tmp/reporte_oportunidad_atencion_<?php echo($id_usuario); ?>.xlsx">
			</form>
			<script id="ajax" type="text/javascript">
				document.getElementById("frm_reporte_oportunidad_atencion").submit();
			</script>
			<?php
				
			}
			
			break;
	}
	exit;
?>
