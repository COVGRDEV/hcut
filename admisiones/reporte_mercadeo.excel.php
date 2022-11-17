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
			require_once("../db/DbAdmision.php");
			
			$utilidades = new Utilidades();
			$dbAdmisiones =  new DbAdmision();
			$dbPagos = new DbPagos();
			$dbTiposPago = new DbTiposPago();
			$dbConvenios = new DbConvenios();
			$dbPlanes = new DbPlanes();
			$dbMaestroInsumos = new DbMaestroInsumos();
			$dbUsuarios = new DbUsuarios();
			$dbListas = new DbListas(); 
			$dbVariables = new DbVariables();
		
		
			$fechaInicial = $utilidades->str_decode($_POST["hddfechaInicial"]);
			$fechaFinal= $utilidades->str_decode($_POST["hddfechaFinal"]);
			$id_convenio = $utilidades->str_decode($_POST["hddconvenio"]);
			$id_plan = $utilidades->str_decode($_POST["hddplan"]);
			$id_mercadeo = $utilidades->str_decode($_POST["hddmercadeo"]);
			$id_lugar = $utilidades->str_decode($_POST["hdd_lugar_cita"]);
			
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
				$lista_mercadeo = $dbAdmisiones->getMercadeo($fechaInicial, $fechaFinal, $id_plan, $id_convenio,  $id_mercadeo, $id_lugar);
								
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("A")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("B")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("C")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("D")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("E")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("F")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("G")->setWidth(26);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("H")->setWidth(45);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("I")->setWidth(18);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("J")->setWidth(18);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("K")->setWidth(35);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("L")->setWidth(10);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("M")->setWidth(20);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("N")->setWidth(18);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("O")->setWidth(18);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("P")->setWidth(30);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("Q")->setWidth(30);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("R")->setWidth(30);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("S")->setWidth(30);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("T")->setWidth(10);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("U")->setWidth(20);
				
				
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
											
				if ($id_mercadeo != "") {
					$mercadeo_obj = $dbListas->getDetalle($id_mercadeo);
					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue("A".$contador_linea, "Tipo de mercadeo aplicado:")
								->setCellValue("B".$contador_linea, $mercadeo_obj["nombre_detalle"]);
					$contador_linea++;
				}
				if ($id_lugar != "") {
					$lugar_obj = $dbListas->getDetalle($id_lugar);
					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue("A".$contador_linea, "Lugar de la cita:")
								->setCellValue("B".$contador_linea, $lugar_obj["nombre_detalle"]);
					$contador_linea++;
				}
				
				$contador_linea++;
				
				$objPHPExcel->getActiveSheet()
							->setCellValue("A".$contador_linea, "FECHA DE ATENCIÓN")
							->setCellValue("B".$contador_linea, "SEDE")
							->setCellValue("C".$contador_linea, "NUMERO DOCUMENTO")
							->setCellValue("D".$contador_linea, "PRIMER NOMBRE")
							->setCellValue("E".$contador_linea, "SEGUNDO NOMBRE")
							->setCellValue("F".$contador_linea, "PRIMER APELLIDO")
							->setCellValue("G".$contador_linea, "SEGUNDO APELLIDO")
							->setCellValue("H".$contador_linea, "CORREO ELECTRONICO")
							->setCellValue("I".$contador_linea, "TELÉFONO 1")
							->setCellValue("J".$contador_linea, "EDAD")
							->setCellValue("K".$contador_linea, "PAIS")
							->setCellValue("L".$contador_linea, "DEPARTAMENTO")
							->setCellValue("M".$contador_linea, "MUNICIPIO")
							->setCellValue("N".$contador_linea, "CATEGORIA MERCADEO")
							->setCellValue("O".$contador_linea, "SUBCATEGORIA MERCADEO")
							->setCellValue("P".$contador_linea, "CONVENIO")
							->setCellValue("Q".$contador_linea, "PLAN")
							->setCellValue("R".$contador_linea, "NOMBRE DEL PROFESIONAL")
							->setCellValue("S".$contador_linea, "MOTIVO DE LA CITA")
							->setCellValue("T".$contador_linea, "GENERO")
							->setCellValue("U".$contador_linea, "HABEAS DATA");
							
							
				
				$objPHPExcel->getActiveSheet()->getStyle("A".$contador_linea.":X".$contador_linea)->getFont()->setBold(true);
				
				$contador_linea++;
					
				foreach ($lista_mercadeo as $reporte_mercadeo) {
					$arr_edad = explode("/", $reporte_mercadeo["edad"]);
					
				//var_dump(is_null($reporte_mercadeo["subcategoria"]));
					
					if ($arr_edad[1] == "1") {
						$edad_aux = $arr_edad[0];
					} else {
						$edad_aux = "0";
					}
					if ($reporte_mercadeo["id_pais"] == "1") {
						$nom_dep_aux = $reporte_mercadeo["nom_dep"];
						$nom_mun_aux = $reporte_mercadeo["nom_mun"];
					} else {
						$nom_dep_aux = $reporte_mercadeo["nom_dep_tex"];
						$nom_mun_aux = $reporte_mercadeo["nom_mun_tex"];
					}
				
					if (is_null($reporte_mercadeo["subcategoria"])){
						if (!is_null($reporte_mercadeo["remitido"])){
									$nom_subc_aux = $reporte_mercadeo["remitido"];	
								}
							else if(!is_null($reporte_mercadeo["referido"])){
									$nom_subc_aux = $reporte_mercadeo["referido"];	
								}
							}
					else{
						
						$nom_subc_aux = $reporte_mercadeo["nom_subcategoria"];
						}	
					
					if($reporte_mercadeo["ind_habeas_data"] == 1){
						cellColor("U".$contador_linea, 'D9F2FF');
						$mensaje = "AUTORIZA USO DE DATOS(Habeas Data)";
					}else{
						 $mensaje = "NO AUTORIZA";
					}
					
			
					$objPHPExcel->getActiveSheet()
						->setCellValue("A".$contador_linea, $reporte_mercadeo["fecha_admision_t"])
						->setCellValue("B".$contador_linea, $reporte_mercadeo["sede"])
						->setCellValue("C".$contador_linea, $reporte_mercadeo["numero_documento"])
						->setCellValue("D".$contador_linea, $reporte_mercadeo["nombre_1"])
						->setCellValue("E".$contador_linea, $reporte_mercadeo["nombre_2"])
						->setCellValue("F".$contador_linea, $reporte_mercadeo["apellido_1"])
						->setCellValue("G".$contador_linea, $reporte_mercadeo["apellido_2"])
						->setCellValue("H".$contador_linea, $reporte_mercadeo["email"])
						->setCellValue("I".$contador_linea, $reporte_mercadeo["telefono_1"])
						->setCellValue("J".$contador_linea,	$edad_aux)
						->setCellValue("K".$contador_linea, $reporte_mercadeo["pais"])
						->setCellValue("L".$contador_linea, $nom_dep_aux)	
						->setCellValue("M".$contador_linea, $nom_mun_aux)
						->setCellValue("N".$contador_linea, $reporte_mercadeo["nom_categoria"])
						->setCellValue("O".$contador_linea, $nom_subc_aux)
						->setCellValue("P".$contador_linea, $reporte_mercadeo["nombre_convenio"])	
						->setCellValue("Q".$contador_linea, $reporte_mercadeo["nombre_plan"])	
						->setCellValue("R".$contador_linea, $reporte_mercadeo["nombre_usuario_prof"])
						->setCellValue("S".$contador_linea, $reporte_mercadeo["nombre_tipo_cita"])
						->setCellValue("T".$contador_linea, $reporte_mercadeo["genero"])
						->setCellValue("U".$contador_linea, $mensaje)
						;
						$contador_linea++;
					
					}
				//Se renombra la hoja actual
				$objPHPExcel->getActiveSheet()->setTitle("Reporte de Mercadeo");
				
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
				@unlink("./tmp/reporte_mercadeo_".$id_usuario.".xlsx");
				
				// Save Excel 2007 file
				$id_usuario = $_SESSION["idUsuario"];
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
				$objWriter->save("./tmp/reporte_mercadeo_".$id_usuario.".xlsx");
			?>
			<form name="frm_reporte_mercadeo" id="frm_reporte_mercadeo" method="post" action="tmp/reporte_mercadeo_<?php echo($id_usuario); ?>.xlsx">
			</form>
			<script id="ajax" type="text/javascript">
				document.getElementById("frm_reporte_mercadeo").submit();
			</script>
			<?php
				
			}
			
			break;
	}
	exit;
	
function cellColor($cells,$color){
    global $objPHPExcel;

    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
             'rgb' => $color
        )
    ));
}
?>
