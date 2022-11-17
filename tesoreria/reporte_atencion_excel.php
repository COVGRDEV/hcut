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
			
			$utilidades = new Utilidades();
			$dbPagos = new DbPagos();
			$dbTiposPago = new DbTiposPago();
			$dbConvenios = new DbConvenios();
			$dbPlanes = new DbPlanes();
			$dbMaestroProcedimientos = new DbMaestroProcedimientos();
			$dbMaestroMedicamentos = new DbMaestroMedicamentos();
			$dbMaestroInsumos = new DbMaestroInsumos();
			$dbUsuarios = new DbUsuarios();
			$dbListas = new DbListas();
			$dbVariables = new DbVariables();
			
			$fechaInicial = $utilidades->str_decode($_POST["hddfechaInicial"]);
			$fechaFinal = $utilidades->str_decode($_POST["hddfechaFinal"]);
			$id_convenio = $utilidades->str_decode($_POST["hddconvenio"]);
			$id_plan = $utilidades->str_decode($_POST["hddplan"]);
			$cod_insumo = $utilidades->str_decode($_POST["hddcodinsumo"]);
			$tipo_precio = $utilidades->str_decode($_POST["hddtipoprecio"]);
			$usuario_atiende = $utilidades->str_decode($_POST["hdd_usuarios"]);
			$lugar_cita = $utilidades->str_decode($_POST["hdd_lugar_cita"]);
		
			
			$arr_diferencia = $dbVariables->getDiferenciaFechas($fechaInicial, $fechaFinal, 2);
			$diferencia_dias = intval($arr_diferencia["dias"], 10);
			
			
			if($diferencia_dias >= 34){
				//Mostrar mensaje de error
				?>
					<script id="ajax" type="text/javascript">
						alert("Existe más de un mes entre las fechas seleccionadas");	
						window.close();
					</script>
				<?php
			}else{
				
						//Se obtiene el listado de tipos de pago activos
					$lista_tipos_pago = $dbTiposPago->getListaTiposPagoAct(1);
					
					//Se obtiene el listado de detalles de pago
					$rta_atenciones_aux = $dbPagos->reporteTesoseriaAtenciones($fechaInicial, $fechaFinal, $id_convenio, $id_plan, $cod_insumo, $tipo_precio, $usuario_atiende, $lugar_cita);
									
					//var_dump($rta_atenciones_aux);					
					//Se obtiene el listado de detalle de medios de los pagos
					$lista_pagos_det_medios = $dbPagos->getListaPagosDetMediosReporteAtenciones($fechaInicial, $fechaFinal, $id_convenio, $id_plan, $usuario_atiende, $lugar_cita);
					$mapa_pagos_det_medios = array();
					foreach ($lista_pagos_det_medios as $pago_det_medio_aux) {
						if (!isset($mapa_pagos_det_medios[$pago_det_medio_aux["id_pago"]])) {
							$mapa_pagos_det_medios[$pago_det_medio_aux["id_pago"]] = array();
						}
						array_push($mapa_pagos_det_medios[$pago_det_medio_aux["id_pago"]], $pago_det_medio_aux);
					}
						
				
				//Descargar archivo
				
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("A")->setWidth(18);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("B")->setWidth(20);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("C")->setWidth(30);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("D")->setWidth(10);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("E")->setWidth(15);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("F")->setWidth(20);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("G")->setWidth(26);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("H")->setWidth(26);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("I")->setWidth(26);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("J")->setWidth(18);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("K")->setWidth(45);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("L")->setWidth(10);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("M")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("N")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("O")->setWidth(30);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("P")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("Q")->setWidth(22);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("R")->setWidth(30);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("S")->setWidth(26);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("T")->setWidth(10);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("U")->setWidth(16);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("V")->setWidth(16);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("W")->setWidth(16);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("AH")->setWidth(30);
				
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
				if ($cod_insumo != "" & $tipo_precio != "") {
					$nombre_aux = "";
					switch ($tipo_precio) {
						case "P":
							$obj_aux = $dbMaestroProcedimientos->getProcedimiento($cod_insumo);
							if (isset($obj_aux["nombre_procedimiento"])) {
								$nombre_aux = $obj_aux["nombre_procedimiento"];
							}
							break;
						case "M":
							$obj_aux = $dbMaestroMedicamentos->getMedicamentos($cod_insumo);
							if (isset($obj_aux[0]["nombre_generico"])) {
								$nombre_aux = $obj_aux[0]["nombre_generico"]." - ".$obj_aux[0]["nombre_comercial"];
							}
							break;
						case "I":
							$obj_aux = $dbMaestroInsumos->getInsumos($cod_insumo);
							if (isset($obj_aux[0]["nombre_insumo"])) {
								$nombre_aux = $obj_aux[0]["nombre_insumo"];
							}
							break;
					}
					if ($nombre_aux != "") {
						$objPHPExcel->setActiveSheetIndex(0)
									->setCellValue("A".$contador_linea, "Concepto:")
									->setCellValue("B".$contador_linea, $cod_insumo." - ".$nombre_aux);
						$contador_linea++;
					}
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
				
				//Color rojo para las celdas NP
				$color_np = "FF9999";
				//Nombre para las celdas NP
				$letra_np = "No pago";
				
				$objPHPExcel->getActiveSheet()
							->setCellValue("A".$contador_linea, "FECHA DE PAGO")
							->setCellValue("B".$contador_linea, "CONVENIO")
							->setCellValue("C".$contador_linea, "PLAN")
							->setCellValue("D".$contador_linea, "ID PAGO")
							->setCellValue("E".$contador_linea, "FACTURA")
							->setCellValue("F".$contador_linea, "SEDE")
							->setCellValue("G".$contador_linea, "PROFESIONAL QUE ATIENDE")
							->setCellValue("H".$contador_linea, "OPTÓMETRA")
							->setCellValue("I".$contador_linea, "TIPO DE ATENCIÓN")
							->setCellValue("J".$contador_linea, "CÓDIGO CONCEPTO")
							->setCellValue("K".$contador_linea, "NOMBRE CONCEPTO")
							->setCellValue("L".$contador_linea, "BILATERAL")
							->setCellValue("M".$contador_linea, "TIPO DOCUMENTO")
							->setCellValue("N".$contador_linea, "NÚMERO DOCUMENTO")
							->setCellValue("O".$contador_linea, "NOMBRE PACIENTE")
							->setCellValue("P".$contador_linea, "TIPO DOCUMENTO TERCERO")
							->setCellValue("Q".$contador_linea, "NÚMERO DOCUMENTO TERCERO")
							->setCellValue("R".$contador_linea, "NOMBRE TERCERO")
							->setCellValue("S".$contador_linea, "NOMBRE REG. PAGO")
							->setCellValue("T".$contador_linea, "CANTIDAD")
							->setCellValue("U".$contador_linea, "VALOR UNITARIO")
							->setCellValue("V".$contador_linea, "VALOR TOTAL")
							->setCellValue("W".$contador_linea, "COPAGO")
							->setCellValue("AH".$contador_linea, "COPAGO");
				
				$arr_letras = array("X", "Y", "Z", "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AI", "AJ", "AK", "AL", "AM", "AN", "AO", "AP", "AQ", "AR", "AS", "AT", "AU", "AV", "AW", "AX", "AY", "AZ");
				
				$max_col_letra = "W";
				$objPHPExcel->getActiveSheet()->getStyle("AH".$contador_linea)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->setCellValue("AH".$contador_linea, "INDICADOR HABEAS DATA");
				
				//Se agregan los encabezados y anchos de columna de los detalles de medios de pago
				foreach ($lista_tipos_pago as $i => $tipo_pago_aux) {
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($arr_letras[$i])->setWidth(18);
					$objPHPExcel->getActiveSheet()->setCellValue($arr_letras[$i].$contador_linea, $tipo_pago_aux["nombre"]);
					$max_col_letra = $arr_letras[$i];
				}
				
				$objPHPExcel->getActiveSheet()->getStyle("A".$contador_linea.":".$max_col_letra.$contador_linea)->getFont()->setBold(true);
				
				$contador_linea++;
				
				foreach ($rta_atenciones_aux as $value) {
					$tipo_bilateral_aux = "";
					switch ($value["tipo_bilateral"]) {
						case "1":
							$tipo_bilateral_aux = "No";
							break;
						case "2":
							$tipo_bilateral_aux = "Sí";
							break;
						default:
							$tipo_bilateral_aux = "No aplica";
							break;
					}
					
					$numero_documento_tercero = $value["numero_documento_tercero"];
					if ($value["numero_verificacion"] != "") {
						$numero_documento_tercero .= "-".$value["numero_verificacion"];
					}
					
					//Se obtiene el nombre del optómetra
					$nombre_optometra = "";
					if ($value["id_admision"] != "") {
						$lista_usuarios_aux = $dbUsuarios->getListaUsuariosAdmisionPerfil($value["id_admision"], 4);
						if (count($lista_usuarios_aux) > 0) {
							$nombre_optometra = $lista_usuarios_aux[0]["nombre_usuario"]." ".$lista_usuarios_aux[0]["apellido_usuario"];
						}
					}
					if($value["ind_habeas_data"] == "1"){
						$mensaje = "AUTORIZA USO DE DATOS";
						$objPHPExcel->getActiveSheet()
								->getStyle("AH".$contador_linea)
								->applyFromArray(
									array(
										"fill" => array(
											"type" => PHPExcel_Style_Fill::FILL_SOLID,
											"color" => array("rgb" => "D9F2FF")
										)
									)
								);

					}else{
						$mensaje = "NO AUTORIZA";
					}
					
					$objPHPExcel->getActiveSheet()
						->setCellValue("A".$contador_linea, $value["fecha_pago"])
						->setCellValue("B".$contador_linea, $value["nombre_convenio"])
						->setCellValue("C".$contador_linea, $value["nombre_plan"])
						->setCellValue("D".$contador_linea, $value["id_pago"])
						->setCellValue("E".$contador_linea, $value["num_factura"])
						->setCellValue("F".$contador_linea, $value["sede"])
						->setCellValue("G".$contador_linea, $value["medico"])
						->setCellValue("H".$contador_linea, $nombre_optometra)
						->setCellValue("I".$contador_linea, ($value["nombre_tipo_cita"] != "" ? $value["nombre_tipo_cita"] : "(Sin admisión)"))
						->setCellValue("J".$contador_linea, $value["cod_insumo"])
						->setCellValue("K".$contador_linea, $value["nombre_insumo"])
						->setCellValue("L".$contador_linea, $tipo_bilateral_aux)
						->setCellValue("M".$contador_linea, $value["tipo_documento"])
						->setCellValue("N".$contador_linea, $value["numero_documento"])
						->setCellValue("O".$contador_linea, $funciones_persona->obtenerNombreCompleto($value["apellido_1"], $value["apellido_2"], $value["nombre_1"], $value["nombre_2"]))
						->setCellValue("P".$contador_linea, $value["tipo_documento_tercero"])
						->setCellValue("Q".$contador_linea, $numero_documento_tercero)
						->setCellValue("R".$contador_linea, $value["nombre_tercero"])
						->setCellValue("S".$contador_linea, $value["usuario_registra_pago"])
						->setCellValue("T".$contador_linea, $value["cantidad"])
						->setCellValue("U".$contador_linea, $value["valor"])
						->setCellValue("V".$contador_linea, $value["cantidad"] * $value["valor"])
						->setCellValue("W".$contador_linea, $value["cantidad"] * $value["valor_cuota"])
						->setCellValue("AH".$contador_linea,$mensaje);
					
					foreach ($lista_tipos_pago as $i => $tipo_pago_aux) {
						$valor_pago_aux = 0;
						$ind_color_np = false;
						$ind_pago_contingencia = false;
						if (isset($mapa_pagos_det_medios[$value["id_pago"]])) {
							//Se halla el valor total del pago
							$valor_total_aux = 0;
							$valor_cuota_aux = 0;
							$medio_pago = "";
							foreach ($mapa_pagos_det_medios[$value["id_pago"]] as $pago_det_medio_aux) {
								$medio_pago = $pago_det_medio_aux["id_medio_pago"];
								if ($pago_det_medio_aux["ind_negativo"] == "1") {
									$valor_total_aux -= $pago_det_medio_aux["valor_pago"];
									if ($value["ind_tipo_pago"] == "1" && $pago_det_medio_aux["id_medio_pago"] != "0") {
										$valor_cuota_aux -= $pago_det_medio_aux["valor_pago"];
									}
								} else {
									$valor_total_aux += $pago_det_medio_aux["valor_pago"];
									if ($value["ind_tipo_pago"] == "1" && $pago_det_medio_aux["id_medio_pago"] != "0") {
										$valor_cuota_aux += $pago_det_medio_aux["valor_pago"];
									}
								}
							}
							
							if ($valor_total_aux != 0) {
								//Se busca el valor que corresponda al tipo de pago de la columna actual
								foreach ($mapa_pagos_det_medios[$value["id_pago"]] as $pago_det_medio_aux) {
									if ($pago_det_medio_aux["id_medio_pago"] == $tipo_pago_aux["id"]) {
										
										if ($value["ind_tipo_pago"] == "1") {
											if ($tipo_pago_aux["id"] == "0") {
												$valor_pago_aux = round($pago_det_medio_aux["valor_pago"] * $value["cantidad"] * ($value["valor"] - $value["valor_cuota"]) / ($valor_total_aux - $valor_cuota_aux), 2);
											} else if ($valor_cuota_aux != 0) {
												$valor_pago_aux = round($pago_det_medio_aux["valor_pago"] * $value["cantidad"] * $value["valor_cuota"] / $valor_cuota_aux, 2);
											} else {
												$valor_pago_aux = 0;
											}
										} else {
											$valor_pago_aux = round($pago_det_medio_aux["valor_pago"] * $value["cantidad"] * $value["valor"] / $valor_total_aux, 2);
										}
										break;
									}
								}
							} else if($medio_pago=="99"){
								$ind_color_np = true;
							}else if($medio_pago=="100"){
								$ind_pago_contingencia = true;
							}
						}
						
						//Si se trata de un no pago se colorea la celda
						if ($ind_color_np && $tipo_pago_aux["id"] == "99") {
							$objPHPExcel->getActiveSheet()
								->getStyle($arr_letras[$i].$contador_linea)
								->applyFromArray(
									array(
										"fill" => array(
											"type" => PHPExcel_Style_Fill::FILL_SOLID,
											"color" => array("rgb" => $color_np)
											
										)
									)
								);
						}
						if ($ind_pago_contingencia && $tipo_pago_aux["id"] == "100") {
							$objPHPExcel->getActiveSheet()
								->getStyle($arr_letras[$i].$contador_linea)
								->applyFromArray(
									array(
										"fill" => array(
											"type" => PHPExcel_Style_Fill::FILL_SOLID,
											"color" => array("rgb" => "EAF8FF")
										)
									)
								);
						}
						
						//Si se trata de un valor negativo, se cambia el signo y el color de fuente de la celda
						if ($tipo_pago_aux["ind_negativo"] == "1" && $valor_pago_aux != 0) {
							$valor_pago_aux *= -1;
							$objPHPExcel->getActiveSheet()->getStyle($arr_letras[$i].$contador_linea)->getFont()->getColor()->setRGB("FF0000");
						}
						//Se pintan los tipos de pago en el Excel, con cero.
						if ($tipo_pago_aux["id"] == "99") {
							if ($ind_color_np) {
								$objPHPExcel->getActiveSheet()->setCellValue($arr_letras[$i].$contador_linea, "NP");
							}
						}else{
							$objPHPExcel->getActiveSheet()->setCellValue($arr_letras[$i].$contador_linea, $valor_pago_aux);
						
						}
						if($medio_pago == "100"){
							if ($ind_pago_contingencia) {
								$objPHPExcel->getActiveSheet()->setCellValue("AG".$contador_linea, "SÍ");
							}
						}
					}
					
					$contador_linea++;
				}
				
				//Se renombra la hoja actual
				$objPHPExcel->getActiveSheet()->setTitle("Reporte de Atenciones");
				
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
				@unlink("./tmp/reporte_atencion_".$id_usuario.".xlsx");
				
				// Save Excel 2007 file
				$id_usuario = $_SESSION["idUsuario"];
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
				$objWriter->save("./tmp/reporte_atencion_".$id_usuario.".xlsx");
			?>
			<form name="frm_reporte_atencion" id="frm_reporte_atencion" method="post" action="tmp/reporte_atencion_<?php echo($id_usuario); ?>.xlsx">
			</form>
			<script id="ajax" type="text/javascript">
				document.getElementById("frm_reporte_atencion").submit();
			</script>
			<?php
				
			}
			
			break;
	}
	
	exit;
?>
