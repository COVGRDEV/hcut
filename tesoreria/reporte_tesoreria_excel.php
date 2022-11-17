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
			
			$fecha_inicial = $utilidades->str_decode($_POST['hddfechaInicial']);
			$fecha_final = $utilidades->str_decode($_POST['hddfechaFinal']);
			$id_convenio = $utilidades->str_decode($_POST['hddconvenio']);
			$id_plan = $utilidades->str_decode($_POST['hddplan']);
			$id_lugar_cita = $utilidades->str_decode($_POST['hddlugarcita']);
			$cod_insumo = $utilidades->str_decode($_POST['hddcodinsumo']);
			$tipo_precio = $utilidades->str_decode($_POST['hddtipoprecio']);
			$id_usuario_adm = $utilidades->str_decode($_POST['hddusuarioadm']);
			$id_usuario = $utilidades->str_decode($_POST['hddusuario']);
			
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
				
				$rta_tipos_pagos_aux = $tiposPago->getListaTiposPagoConcepto();
				$rta_procedimientos_aux = $dbPagos->reporteTesoseriaProcedimientosPlanes($fecha_inicial, $fecha_final, $id_convenio, $id_plan, $id_lugar_cita, $cod_insumo, $tipo_precio, $id_usuario, $id_usuario_adm);
				$rta_pacientes_aux = $dbPagos->reporteTesoseriaProcedimientosPacientesPlanes($fecha_inicial, $fecha_final, $id_convenio, $id_plan, $id_lugar_cita, $cod_insumo, $tipo_precio, $id_usuario, $id_usuario_adm);
				
				//Descargar archivo
						
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(18);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(18);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(30);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(15);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(15);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(15);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(45);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(15);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(15);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setWidth(15);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('K')->setWidth(15);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('L')->setWidth(10);
					
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
					if ($id_plan != "") {
						$plan_obj = $dbPlanes->getPlan($id_plan);
						$objPHPExcel->setActiveSheetIndex(0)
									->setCellValue('A'.$contador_linea, 'Plan:')
									->setCellValue('B'.$contador_linea, $plan_obj["nombre_plan"]);
						$contador_linea++;
					}
					if ($id_lugar_cita != "") {
						$lugar_obj = $dbListas->getDetalle($id_lugar_cita);
						$objPHPExcel->setActiveSheetIndex(0)
									->setCellValue('A'.$contador_linea, 'Sede:')
									->setCellValue('B'.$contador_linea, $lugar_obj["nombre_detalle"]);
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
										->setCellValue('A'.$contador_linea, 'Concepto:')
										->setCellValue('B'.$contador_linea, $cod_insumo." - ".$nombre_aux);
							$contador_linea++;
						}
					}
					if ($id_usuario_adm != "") {
						$usuario_obj = $dbUsuarios->getUsuario($id_usuario_adm);
						$objPHPExcel->setActiveSheetIndex(0)
									->setCellValue('A'.$contador_linea, 'Usuario admisión:')
									->setCellValue('B'.$contador_linea, $usuario_obj["nombre_usuario"]." ".$usuario_obj["apellido_usuario"]);
						$contador_linea++;
					}
					if ($id_usuario != "") {
						$usuario_obj = $dbUsuarios->getUsuario($id_usuario);
						$objPHPExcel->setActiveSheetIndex(0)
									->setCellValue('A'.$contador_linea, 'Usuario registro pago:')
									->setCellValue('B'.$contador_linea, $usuario_obj["nombre_usuario"]." ".$usuario_obj["apellido_usuario"]);
						$contador_linea++;
					}
					
					$contador_linea++;
					$lista_cambios_precios = array();
					$arr_totales_fp = array();
					for ($a = 0; $a < count($rta_tipos_pagos_aux); $a++) {
						$pasaporte_titulo_tpago = 0;
						$valor_por_pagar = 0;
						
						for ($i = 0; $i < count($rta_procedimientos_aux); $i++) {
							$pasaporte_titulo_cups = 0;
							$pasaporte_totales = false;
							$contador_cantidades = 0;
							$contador_precios = 0;
							$pasaporte_cantidades = 0;
							
							for ($e = 0; $e < count($rta_pacientes_aux); $e++) {
								if ($rta_pacientes_aux[$e]['cod_insumo'] == $rta_procedimientos_aux[$i]['cod_insumo'] && $rta_pacientes_aux[$e]["tipo_precio"] == $rta_procedimientos_aux[$i]["tipo_precio"]) {
									if ($pasaporte_titulo_cups == 0) {
										$pasaporte_titulo_cups = 1;
									}
									
									$n1 = 0;
									if ($rta_tipos_pagos_aux[$a]['id'] == $rta_pacientes_aux[$e]['id_medio_pago']
											|| ($rta_tipos_pagos_aux[$a]["id"] == "99" && trim($rta_pacientes_aux[$e]['id_medio_pago']) == "")) {
										//Si el tipo de pago es igual
										if ($pasaporte_titulo_tpago == 0) {
											$pasaporte_titulo_tpago = 1;
										}
										
										//Calcula el valor por pagar para el tipo de pago
										$valor_por_pagar = 0;
										$abrev_pago = "";
										if ($rta_pacientes_aux[$e]['t_pago'] == $rta_tipos_pagos_aux[$a]['id']) {
											$valor_por_pagar = $rta_pacientes_aux[$e]['valor_pago'];
										} else if ($rta_pacientes_aux[$e]['valor_pago'] > 0) {
											//Se obtiene la abreviatura del tipo de pago
											$tipo_pago_aux = $tiposPago->getTipoPago($rta_pacientes_aux[$e]['id_medio_pago']);
											$abrev_pago = $tipo_pago_aux["abrev_pago"];
										}
										
										$valor_total_aux = intval($rta_pacientes_aux[$e]['total_pago'], 10);
										if ($rta_pacientes_aux[$e]['ind_boleta'] == '1') {
											if ($rta_pacientes_aux[$e]['id_medio_pago'] == '0') {
												$valor_por_pagar = (intval($rta_pacientes_aux[$e]['valor'], 10) - intval($rta_pacientes_aux[$e]['valor_cuota'], 10)) * intval($rta_pacientes_aux[$e]["cantidad"], 10);
											} else if (intval($rta_pacientes_aux[$e]["total_cuota"], 10) > 0) {
												$valor_por_pagar = intval($rta_pacientes_aux[$e]['valor_cuota'], 10) * $rta_pacientes_aux[$e]["cantidad"] * $rta_pacientes_aux[$e]["valor_pago"] / $rta_pacientes_aux[$e]["total_cuota"];
											} else {
												$valor_por_pagar = 0;
											}
										} else {
											if ($valor_total_aux > 0) {
												$valor_por_pagar *= (($rta_pacientes_aux[$e]["valor"] * $rta_pacientes_aux[$e]["cantidad"]) / $valor_total_aux);
											}
										}
										
										$valor_base_aux = $rta_pacientes_aux[$e]["valor_b"];
										
										//Se marcan los valores que no correspondan al valor base
										if ($valor_base_aux != $rta_pacientes_aux[$e]["valor"] /*&& intval($valor_base_aux, 10) != 0*/) {
											$lista_cambios_precios[$rta_pacientes_aux[$e]["id_pago"]] = $rta_pacientes_aux[$e];
										}
										
										if ($valor_por_pagar > 0 || $rta_pacientes_aux[$e]['ind_boleta'] == "0") {
											//Encabezado
											if ($pasaporte_titulo_tpago == 1) {//Imprime el titulo del tipo de pago
												$objPHPExcel->setActiveSheetIndex(0)
														->setCellValue('A'.$contador_linea, 'FORMA DE PAGO:')
														->setCellValue('B'.$contador_linea, $rta_tipos_pagos_aux[$a]['nombre']);
												
												$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$contador_linea.':B'.$contador_linea)->getFont()->setBold(true);
												$contador_linea++;
												$pasaporte_titulo_tpago++;
											}
											
											if ($pasaporte_titulo_cups == 1) {//Imprime el titulo del cups y titulos de nombre completo, documento, precio ,etc...
												$objPHPExcel->setActiveSheetIndex(0)
														->setCellValue('A'.$contador_linea, $rta_procedimientos_aux[$i]['cod_insumo'])
														->setCellValue('B'.$contador_linea, strtoupper($rta_procedimientos_aux[$i]['nombre_insumo']))
														->setCellValue('A'.($contador_linea + 1), 'Fecha')
														->setCellValue('B'.($contador_linea + 1), 'No. recibo')
														->setCellValue('C'.($contador_linea + 1), 'Nombre completo')
														->setCellValue('D'.($contador_linea + 1), 'Documento')
														->setCellValue('E'.($contador_linea + 1), 'Factura')
														->setCellValue('F'.($contador_linea + 1), 'Entidad')
														->setCellValue('G'.($contador_linea + 1), 'Convenio')
														->setCellValue('H'.($contador_linea + 1), 'Valor base')
														->setCellValue('I'.($contador_linea + 1), 'Valor pagado')
														->setCellValue('J'.($contador_linea + 1), 'Cantidad')
														->setCellValue('K'.($contador_linea + 1), 'Valor total pagado');
												
												$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$contador_linea.':K'.($contador_linea + 1))->getFont()->setBold(true);
												$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B'.$contador_linea.':K'.$contador_linea);
												
												$contador_linea += 2;
												$pasaporte_titulo_cups++;
											}
											
											$nombre_aux = mb_strtoupper($funciones_persona->obtenerNombreCompleto($rta_pacientes_aux[$e]["apellido_1"], $rta_pacientes_aux[$e]["apellido_2"], $rta_pacientes_aux[$e]["nombre_1"], $rta_pacientes_aux[$e]["nombre_2"]), "UTF-8");
											
											//Valores
											$pasaporte_cantidades++;
											$objPHPExcel->setActiveSheetIndex(0)
													->setCellValue('A'.$contador_linea, $rta_pacientes_aux[$e]['fecha_pago_t'])
													->setCellValue('B'.$contador_linea, $rta_pacientes_aux[$e]['id_pago'])
													->setCellValue('C'.$contador_linea, $nombre_aux)
													->setCellValue('D'.$contador_linea, $rta_pacientes_aux[$e]["numero_documento"])
													->setCellValue('E'.$contador_linea, $rta_pacientes_aux[$e]['num_factura'])
													->setCellValue('F'.$contador_linea, $rta_pacientes_aux[$e]['nombre_entidad'])
													->setCellValue('G'.$contador_linea, $rta_pacientes_aux[$e]['nombre_convenio']." - ".$rta_pacientes_aux[$e]['nombre_plan'])
													->setCellValue('H'.$contador_linea, round($valor_base_aux))
													->setCellValue('I'.$contador_linea, round($valor_por_pagar / $rta_pacientes_aux[$e]['cantidad']))
													->setCellValue('J'.$contador_linea, $rta_pacientes_aux[$e]['cantidad'])
													->setCellValue('K'.$contador_linea, round($valor_por_pagar))
													->setCellValue('L'.$contador_linea, $abrev_pago);
											
											$contador_linea++;
											
											//Datos del tercero
											if ($rta_pacientes_aux[$e]["tipo_documento_tercero"] != "") {
												$numero_documento_aux = $rta_pacientes_aux[$e]["numero_documento_tercero"];
												if ($rta_pacientes_aux[$e]["numero_verificacion"] != "") {
													$numero_documento_aux .= "-".$rta_pacientes_aux[$e]["numero_verificacion"];
												}
												$nombre_aux = mb_strtoupper($rta_pacientes_aux[$e]["nombre_tercero"]);
												
												$objPHPExcel->setActiveSheetIndex(0)
														->setCellValue('C'.$contador_linea, $nombre_aux)
														->setCellValue('D'.$contador_linea, $numero_documento_aux);
												
												$contador_linea++;
											}
											
											$contador_cantidades += $rta_pacientes_aux[$e]['cantidad'];
											$contador_precios += $valor_por_pagar;
											$pasaporte_totales = true;
										}
									}
								}
								if ($e >= (count($rta_pacientes_aux) - 1) && $pasaporte_totales) {
									$objPHPExcel->setActiveSheetIndex(0)
												->setCellValue('I'.$contador_linea, "Total")
												->setCellValue('J'.$contador_linea, $contador_cantidades)
												->setCellValue('K'.$contador_linea, round($contador_precios));
									
									$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$contador_linea.':K'.$contador_linea)->getFont()->setBold(true);
									
									//Se acumula en el total por tipo de pago
									if (isset($arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]])) {
										$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["cantidad"] += $contador_cantidades;
										$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["valor"] += $contador_precios;
									} else {
										$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["id_tipo_concepto"] = $rta_tipos_pagos_aux[$a]["id_tipo_concepto"];
										$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["tipo_concepto"] = $rta_tipos_pagos_aux[$a]["tipo_concepto"];
										$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["nombre"] = $rta_tipos_pagos_aux[$a]["nombre"];
										$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["ind_negativo"] = $rta_tipos_pagos_aux[$a]["ind_negativo"];
										$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["cantidad"] = $contador_cantidades;
										$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["valor"] = $contador_precios;
									}
									
									$contador_linea += 2;
								}
							}
						}
						
						//Total por tipo de pago
						if (isset($arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]])) {
							$objPHPExcel->setActiveSheetIndex(0)
										->setCellValue('I'.$contador_linea, "Total ".$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["nombre"])
										->setCellValue('K'.$contador_linea, round($arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["valor"]));
							
							$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$contador_linea.':K'.$contador_linea)->getFont()->setBold(true);
							$contador_linea += 2;
						}
					}
					
					//Se agregan los subtotales
					$total_aux = 0;
					$id_tipo_concepto_ant = "";
					$contador_linea++;
					foreach ($arr_totales_fp as $total_fp_aux) {
						if ($total_fp_aux["id_tipo_concepto"] != $id_tipo_concepto_ant) {
							//Se agrega el nombre del concepto
							$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$contador_linea.':H'.$contador_linea)->getFont()->setBold(true);
							$objPHPExcel->setActiveSheetIndex(0)
										->setCellValue('G'.$contador_linea, $total_fp_aux["tipo_concepto"]);
							$contador_linea++;
						}
						if ($total_fp_aux["ind_negativo"] == "1") {
							$objPHPExcel->setActiveSheetIndex(0)
										->setCellValue('G'.$contador_linea, "Subtotal ".$total_fp_aux["nombre"])
										->setCellValue('H'.$contador_linea, -round($total_fp_aux["valor"]));
							
							$objPHPExcel->getActiveSheet()->getStyle("G".$contador_linea)->getFont()->getColor()->setRGB("FF0000");
							$objPHPExcel->getActiveSheet()->getStyle("H".$contador_linea)->getFont()->getColor()->setRGB("FF0000");
							
							$total_aux -= $total_fp_aux["valor"];
						} else {
							$objPHPExcel->setActiveSheetIndex(0)
										->setCellValue('G'.$contador_linea, "Subtotal ".$total_fp_aux["nombre"])
										->setCellValue('H'.$contador_linea, round($total_fp_aux["valor"]));
							
							$total_aux += $total_fp_aux["valor"];
						}
						$id_tipo_concepto_ant = $total_fp_aux["id_tipo_concepto"];
						$contador_linea++;
					}
					$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$contador_linea.':H'.$contador_linea)->getFont()->setBold(true);
					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('G'.$contador_linea, "TOTAL")
								->setCellValue('H'.$contador_linea, round($total_aux));
					
					//Se renombra la hoja actual
					$objPHPExcel->getActiveSheet()->setTitle('Reporte de tesorería');
					
					//Se agrega una nueva hoja con los anticipos recaudados si no se seleccionaron convenio, plan, usuario de admisión o concepto
					if ($id_convenio == "" && $id_plan == "" && $cod_insumo == "" && $id_usuario_adm == "") {
						$lista_anticipos_det_medios = $dbAnticipos->get_lista_anticipos_det_medios_fechas($fecha_inicial, $fecha_final, $id_lugar_cita, $id_usuario, 0);
						if (count($lista_anticipos_det_medios) > 0) {
							$objPHPExcel->createSheet();
							$indice_act++;
							$objPHPExcel->setActiveSheetIndex($indice_act);
							
							$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(18);
							$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(18);
							$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
							$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
							$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
							$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
							$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(60);
							
							//Se calculan los subtotales y totales
							$total_anticipos = 0;
							$arr_totales_anticipos = array();
							foreach ($lista_anticipos_det_medios as $det_medio_aux) {
								$total_anticipos += $det_medio_aux["valor_pago"];
								
								if (isset($arr_totales_anticipos[$det_medio_aux["id_medio_pago"]])) {
									$arr_totales_anticipos[$det_medio_aux["id_medio_pago"]]["valor"] += $det_medio_aux["valor_pago"];
								} else {
									//Se busca el valor del medio de pago
									$nombre_aux = "";
									foreach ($rta_tipos_pagos_aux as $tipo_pago_aux) {
										if ($tipo_pago_aux["id"] == $det_medio_aux["id_medio_pago"]) {
											$nombre_aux = $tipo_pago_aux["nombre"];
											break;
										}
									}
									$arr_totales_anticipos[$det_medio_aux["id_medio_pago"]]["nombre"] = $nombre_aux;
									$arr_totales_anticipos[$det_medio_aux["id_medio_pago"]]["valor"] = $det_medio_aux["valor_pago"];
								}
							}
							
							$contador_linea = 1;
							
							//Se agregan los datos al reporte por medio de pago
							foreach ($rta_tipos_pagos_aux as $tipo_pago_aux) {
								$fecha_ant = "";
								$bol_agregado = false;
								foreach ($lista_anticipos_det_medios as $det_medio_aux) {
									if ($tipo_pago_aux["id"] == $det_medio_aux["id_medio_pago"]) {
										if (!$bol_agregado) {
											$objPHPExcel->getActiveSheet()->getStyle('A'.$contador_linea.':G'.($contador_linea + 1))->getFont()->setBold(true);
											
											//Se agrega el título del medio de pago
											$objPHPExcel->getActiveSheet()
														->setCellValue('A'.$contador_linea, "FORMA DE PAGO: ".$tipo_pago_aux["nombre"]);
											$contador_linea++;
											
											$objPHPExcel->getActiveSheet()
														->setCellValue('A'.$contador_linea, 'Fecha')
														->setCellValue('B'.$contador_linea, 'Anticipo')
														->setCellValue('C'.$contador_linea, 'Nombre completo')
														->setCellValue('D'.$contador_linea, 'Documento')
														->setCellValue('E'.$contador_linea, 'No. externo')
														->setCellValue('F'.$contador_linea, 'Valor')
														->setCellValue('G'.$contador_linea, 'Observaciones');
											$contador_linea++;
											
											$bol_agregado = true;
										}
										
										$nombre_aux = mb_strtoupper($funciones_persona->obtenerNombreCompleto($det_medio_aux["apellido_1"], $det_medio_aux["apellido_2"], $det_medio_aux["nombre_1"], $det_medio_aux["nombre_2"]), "UTF-8");
										$objPHPExcel->getActiveSheet()
													->setCellValue('A'.$contador_linea, $det_medio_aux["fecha_crea_t"])
													->setCellValue('B'.$contador_linea, $det_medio_aux["id_anticipo"])
													->setCellValue('C'.$contador_linea, $nombre_aux)
													->setCellValue('D'.$contador_linea, $det_medio_aux["numero_documento"])
													->setCellValue('E'.$contador_linea, $det_medio_aux["num_anticipo"])
													->setCellValue('F'.$contador_linea, $det_medio_aux["valor_pago"])
													->setCellValue('G'.$contador_linea, $det_medio_aux["observaciones_anticipo"]);
										$contador_linea++;
										
										if ($det_medio_aux["id_tercero"] != "") {
											$objPHPExcel->getActiveSheet()
														->setCellValue('C'.$contador_linea, mb_strtoupper($det_medio_aux["nombre_tercero"], "UTF-8"))
														->setCellValue('D'.$contador_linea, $det_medio_aux["numero_documento_tercero"]);
											$contador_linea++;
										}
										$contador_linea++;
									}
								}
								
								//Se agrega el total del medio de pago
								if (isset($arr_totales_anticipos[$tipo_pago_aux["id"]])) {
									$total_tipo_pago_aux = $arr_totales_anticipos[$tipo_pago_aux["id"]];
									
									$objPHPExcel->getActiveSheet()->getStyle('E'.$contador_linea.':F'.$contador_linea)->getFont()->setBold(true);
									
									$objPHPExcel->getActiveSheet()
												->setCellValue('E'.$contador_linea, "Total ".$total_tipo_pago_aux["nombre"])
												->setCellValue('F'.$contador_linea, $total_tipo_pago_aux["valor"]);
									$contador_linea += 2;
								}
							}
							
							//Se imprime el total por concepto de anticipos
							foreach ($rta_tipos_pagos_aux as $tipo_pago_aux) {
								if (isset($arr_totales_anticipos[$tipo_pago_aux["id"]])) {
									$total_anticipo_aux = $arr_totales_anticipos[$tipo_pago_aux["id"]];
									
									$objPHPExcel->getActiveSheet()
												->setCellValue('E'.$contador_linea, "Subtotal ".$total_tipo_pago_aux["nombre"])
												->setCellValue('F'.$contador_linea, $total_tipo_pago_aux["valor"]);
									$contador_linea++;
								}
							}
							
							$objPHPExcel->getActiveSheet()->getStyle('E'.$contador_linea.':F'.$contador_linea)->getFont()->setBold(true);
							
							$objPHPExcel->getActiveSheet()
										->setCellValue('E'.$contador_linea, "TOTAL RECAUDO ANTICIPOS")
										->setCellValue('F'.$contador_linea, $total_anticipos);
							$contador_linea++;
							
							$objPHPExcel->getActiveSheet()->setTitle('Anticipos');
						}
					}
					
					//Se crea una nueva hoja para los valores base modificados
					if (count($lista_cambios_precios) > 0) {
						$objPHPExcel->createSheet();
						$indice_act++;
						$objPHPExcel->setActiveSheetIndex($indice_act);
						
						$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(18);
						$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
						$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
						$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
						$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
						$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
						$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(60);
						
						$contador_linea = 1;
						
						$objPHPExcel->getActiveSheet()
									->setCellValue('A'.$contador_linea, 'VALORES BASE MODIFICADOS')
									->setCellValue('A'.($contador_linea + 1), 'No. recibo')
									->setCellValue('B'.($contador_linea + 1), 'Nombre completo')
									->setCellValue('C'.($contador_linea + 1), 'Documento')
									->setCellValue('D'.($contador_linea + 1), 'Convenio')
									->setCellValue('E'.($contador_linea + 1), 'Valor base')
									->setCellValue('F'.($contador_linea + 1), 'Valor unitario')
									->setCellValue('G'.($contador_linea + 1), 'Observaciones');
						
						$objPHPExcel->getActiveSheet()->getStyle('A'.$contador_linea.':G'.($contador_linea + 1))->getFont()->setBold(true);
						$objPHPExcel->getActiveSheet()->mergeCells('A'.$contador_linea.':G'.$contador_linea);
						
						$contador_linea += 2;
						
						foreach ($lista_cambios_precios as $precio_aux) {
							$valor_base_aux = "";
							switch ($precio_aux["ind_tipo_pago"]) {
								case "1": //Cuota moderadora
									$valor_base_aux = $precio_aux["valor_cuota_b"];
									break;
								case "2": //Valor total
									$valor_base_aux = $precio_aux["valor_b"];
									break;
							}
							
							$objPHPExcel->getActiveSheet()
										->setCellValue('A'.$contador_linea, $precio_aux['id_pago'])
										->setCellValue('B'.$contador_linea, mb_strtoupper($funciones_persona->obtenerNombreCompleto($precio_aux['apellido_1'], $precio_aux['apellido_2'], $precio_aux['nombre_1'], $precio_aux['nombre_2']), "UTF-8"))
										->setCellValue('C'.$contador_linea, $precio_aux['numero_documento'])
										->setCellValue('D'.$contador_linea, $precio_aux['nombre_convenio'])
										->setCellValue('E'.$contador_linea, round($valor_base_aux))
										->setCellValue('F'.$contador_linea, round($precio_aux['valor']))
										->setCellValue('G'.$contador_linea, $precio_aux['observaciones_pago']);
							
							$contador_linea++;
						}
						
						$objPHPExcel->getActiveSheet()->setTitle('Revisión');
					}
					
					//Se agrega la página de los pagos no realizados
					$lista_pagos_pendientes = $dbPagos->getListaPagosEstado($fecha_inicial, $fecha_final, 1, $id_convenio, $id_plan, $id_lugar_cita, $id_usuario_adm, "", $tipo_precio, $cod_insumo);
					if (count($lista_pagos_pendientes) > 0) {
						$objPHPExcel->createSheet();
						$indice_act++;
						$objPHPExcel->setActiveSheetIndex($indice_act);
						
						$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(18);
						$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
						$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
						$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
						$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
						$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(60);
						
						$contador_linea = 1;
						
						$objPHPExcel->getActiveSheet()
									->setCellValue('A'.$contador_linea, 'PAGOS PENDIENTES')
									->setCellValue('A'.($contador_linea + 1), 'No. recibo')
									->setCellValue('B'.($contador_linea + 1), 'Nombre completo')
									->setCellValue('C'.($contador_linea + 1), 'Documento')
									->setCellValue('D'.($contador_linea + 1), 'Convenio')
									->setCellValue('E'.($contador_linea + 1), 'Valor')
									->setCellValue('F'.($contador_linea + 1), 'Observaciones');
						
						$objPHPExcel->getActiveSheet()->getStyle('A'.$contador_linea.':F'.($contador_linea + 1))->getFont()->setBold(true);
						$objPHPExcel->getActiveSheet()->mergeCells('A'.$contador_linea.':F'.$contador_linea);
						
						$contador_linea += 2;
						
						foreach ($lista_pagos_pendientes as $pago_aux) {
							$valor_aux = "";
							switch ($pago_aux["ind_tipo_pago"]) {
								case "1": //Cuota moderadora
									$valor_aux = $pago_aux["valor_cuota"];
									break;
								case "2": //Valor total
									$valor_aux = $pago_aux["valor"];
									break;
							}
							
							$objPHPExcel->getActiveSheet()
										->setCellValue('A'.$contador_linea, $pago_aux['id_pago'])
										->setCellValue('B'.$contador_linea, mb_strtoupper($funciones_persona->obtenerNombreCompleto($pago_aux['apellido_1'], $pago_aux['apellido_2'], $pago_aux['nombre_1'], $pago_aux['nombre_2']), "UTF-8"))
										->setCellValue('C'.$contador_linea, $pago_aux['numero_documento'])
										->setCellValue('D'.$contador_linea, $pago_aux['nombre_convenio'])
										->setCellValue('E'.$contador_linea, round($valor_aux))
										->setCellValue('F'.$contador_linea, $pago_aux['observaciones_pago']);
							
							$contador_linea++;
						}
						
						$objPHPExcel->getActiveSheet()->setTitle('Pendientes');
					}
					
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
					
					// Save Excel 2007 file
					$id_usuario = $_SESSION["idUsuario"];
					
					//Se borra el reporte previamente generado por el usuario
					@unlink("./tmp/reporte_tesoreria_".$id_usuario.".xlsx");
					
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save("./tmp/reporte_tesoreria_".$id_usuario.".xlsx");
				?>
				<form name="frm_reporte_tesoreria" id="frm_reporte_tesoreria" method="post" action="tmp/reporte_tesoreria_<?php echo($id_usuario); ?>.xlsx">
				</form>
				<script id="ajax" type="text/javascript">
					document.getElementById("frm_reporte_tesoreria").submit();
				</script>
				<?php		
			}
				
			break;
			
		case "2": //Reporte por usuario
			require_once("../db/DbPacientes.php");
			require_once("../db/DbPagos.php");
			require_once("../db/DbTiposPago.php");
			require_once("../funciones/pdf/funciones.php");
			require_once '../funciones/Utilidades.php';
			
			$utilidades = new Utilidades();
			$dbPacientes = new DbPacientes();
			$dbPagos = new DbPagos();
			$tiposPago = new DbTiposPago();
			
			$fecha_inicial = $_POST['fechaInicial22'];
			$fecha_final = $_POST['fechaFinal22'];
			$idPaciente = $_POST['txtIdPaciente2'];
			
			$paciente_obj = $dbPacientes->getExistepaciente3($idPaciente);
			$rta_tipos_pagos_aux = $tiposPago->getListaTiposPagoConcepto();
			$rta_pacientes_aux = $dbPagos->reporteTesoseriaPaciente($fecha_inicial, $fecha_final, $idPaciente);
			
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(18);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(18);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(10);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(35);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(45);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('K')->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('L')->setWidth(10);
			
			$arr_totales_fp = array();
			
			$nombre_paciente = mb_strtoupper($funciones_persona->obtenerNombreCompleto($paciente_obj["nombre_1"], $paciente_obj["nombre_2"], $paciente_obj["apellido_1"], $paciente_obj["apellido_2"]), "UTF-8");
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A1', "Paciente")
						->setCellValue('B1', $nombre_paciente);
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:B1')->getFont()->setBold(true);
			$contador_linea = 2;
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
			$contador_linea++;
			
			for ($a = 0; $a < count($rta_tipos_pagos_aux); $a++) {
				$pasaporte_titulo_tpago = 0;
				$valor_por_pagar = 0;
				
				$pasaporte_titulo_cups = 0;
				$pasaporte_totales = false;
				$contador_cantidades = 0;
				$contador_precios = 0;
				$pasaporte_cantidades = 0;
				
				for ($e = 0; $e < count($rta_pacientes_aux); $e++) {
					if ($pasaporte_titulo_cups == 0) {
						$pasaporte_titulo_cups = 1;
					}
					
					$n1 = 0;
					if ($rta_tipos_pagos_aux[$a]['id'] == $rta_pacientes_aux[$e]['id_medio_pago']
							|| ($rta_tipos_pagos_aux[$a]["id"] == "99" && trim($rta_pacientes_aux[$e]['id_medio_pago']) == "")) {
						//Si el tipo de pago es igual
						if ($pasaporte_titulo_tpago == 0) {
							$pasaporte_titulo_tpago = 1;
						}
						
						if ($pasaporte_titulo_tpago == 1) {//Imprime el titulo del tipo de pago
							$objPHPExcel->setActiveSheetIndex(0)
										->setCellValue('A'.$contador_linea, 'FORMA DE PAGO:')
										->setCellValue('B'.$contador_linea, $rta_tipos_pagos_aux[$a]['nombre']);
							
							$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$contador_linea.':B'.$contador_linea)->getFont()->setBold(true);
							$contador_linea++;
							$pasaporte_titulo_tpago++;
						}
						
						if ($pasaporte_titulo_cups == 1) {//Imprime el titulo del cups y titulos de nombre completo, documento, precio ,etc...
							$objPHPExcel->setActiveSheetIndex(0)
										->setCellValue('A'.$contador_linea, 'Fecha')
										->setCellValue('B'.$contador_linea, 'No. recibo')
										->setCellValue('C'.$contador_linea, 'Código')
										->setCellValue('D'.$contador_linea, 'Concepto')
										->setCellValue('E'.$contador_linea, 'Factura')
										->setCellValue('F'.$contador_linea, 'Entidad')
										->setCellValue('G'.$contador_linea, 'Convenio')
										->setCellValue('H'.$contador_linea, 'Valor base')
										->setCellValue('I'.$contador_linea, 'Valor pagado')
										->setCellValue('J'.$contador_linea, 'Cantidad')
										->setCellValue('K'.$contador_linea, 'Valor total pagado');
							
							$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$contador_linea.':K'.$contador_linea)->getFont()->setBold(true);
							
							$contador_linea++;
							$pasaporte_titulo_cups++;
						}
						
						//Calcula el valor por pagar para el tipo de pago
						$valor_por_pagar = 0;
						$abrev_pago = "";
						if ($rta_pacientes_aux[$e]['t_pago'] == $rta_tipos_pagos_aux[$a]['id']) {
							$valor_por_pagar = $rta_pacientes_aux[$e]['valor_pago'];
						} else if ($rta_pacientes_aux[$e]['valor_pago'] > 0) {
							//Se obtiene la abreviatura del tipo de pago
							$tipo_pago_aux = $tiposPago->getTipoPago($rta_pacientes_aux[$e]['id_medio_pago']);
							$abrev_pago = $tipo_pago_aux["abrev_pago"];
						}
						
						$valor_total_aux = intval($rta_pacientes_aux[$e]['total_pago'], 10);
						if ($rta_pacientes_aux[$e]['ind_boleta'] == '1') {
							if ($rta_pacientes_aux[$e]['id_medio_pago'] == '0') {
								$valor_por_pagar = (intval($rta_pacientes_aux[$e]['valor'], 10) - intval($rta_pacientes_aux[$e]['valor_cuota'], 10)) * intval($rta_pacientes_aux[$e]["cantidad"], 10);
							} else if (intval($rta_pacientes_aux[$e]["total_cuota"], 10) > 0) {
								$valor_por_pagar = intval($rta_pacientes_aux[$e]['valor_cuota'], 10) * $rta_pacientes_aux[$e]["cantidad"] * $rta_pacientes_aux[$e]["valor_pago"] / $rta_pacientes_aux[$e]["total_cuota"];
							} else {
								$valor_por_pagar = 0;
							}
						} else {
							if ($valor_total_aux > 0) {
								$valor_por_pagar *= (($rta_pacientes_aux[$e]["valor"] * $rta_pacientes_aux[$e]["cantidad"]) / $valor_total_aux);
							}
						}
						
						$valor_base_aux = $rta_pacientes_aux[$e]["valor_b"];
						
						if ($valor_por_pagar > 0 || $rta_pacientes_aux[$e]['ind_boleta'] == "0") {
							$pasaporte_cantidades++;
							$objPHPExcel->setActiveSheetIndex(0)
										->setCellValue('A'.$contador_linea, $rta_pacientes_aux[$e]['fecha_pago_t'])
										->setCellValue('B'.$contador_linea, $rta_pacientes_aux[$e]['id_pago'])
										->setCellValue('C'.$contador_linea, $rta_pacientes_aux[$e]['cod_insumo'])
										->setCellValue('D'.$contador_linea, $rta_pacientes_aux[$e]['nombre_insumo'])
										->setCellValue('E'.$contador_linea, $rta_pacientes_aux[$e]['num_factura'])
										->setCellValue('F'.$contador_linea, $rta_pacientes_aux[$e]['nombre_entidad'])
										->setCellValue('G'.$contador_linea, $rta_pacientes_aux[$e]['nombre_convenio']." - ".$rta_pacientes_aux[$e]['nombre_plan'])
										->setCellValue('H'.$contador_linea, round($valor_base_aux))
										->setCellValue('I'.$contador_linea, round($valor_por_pagar / $rta_pacientes_aux[$e]['cantidad']))
										->setCellValue('J'.$contador_linea, $rta_pacientes_aux[$e]['cantidad'])
										->setCellValue('K'.$contador_linea, round($valor_por_pagar))
										->setCellValue('L'.$contador_linea, $abrev_pago);
							
							$contador_linea++;
							$contador_cantidades += $rta_pacientes_aux[$e]['cantidad'];
							$contador_precios += $valor_por_pagar;
						}
						$pasaporte_totales = true;
					}
					
					if ($e >= (count($rta_pacientes_aux) - 1) && $pasaporte_totales) {
						$objPHPExcel->setActiveSheetIndex(0)
									->setCellValue('I'.$contador_linea, "Total")
									->setCellValue('J'.$contador_linea, $contador_cantidades)
									->setCellValue('K'.$contador_linea, round($contador_precios));
						
						$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$contador_linea.':K'.$contador_linea)->getFont()->setBold(true);
						
						//Se acumula en el total por tipo de pago
						if (isset($arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]])) {
							$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["cantidad"] += $contador_cantidades;
							$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["valor"] += $contador_precios;
						} else {
							$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["id_tipo_concepto"] = $rta_tipos_pagos_aux[$a]["id_tipo_concepto"];
							$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["tipo_concepto"] = $rta_tipos_pagos_aux[$a]["tipo_concepto"];
							$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["nombre"] = $rta_tipos_pagos_aux[$a]["nombre"];
							$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["ind_negativo"] = $rta_tipos_pagos_aux[$a]["ind_negativo"];
							$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["cantidad"] = $contador_cantidades;
							$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["valor"] = $contador_precios;
						}
						
						$contador_linea += 2;
					}
				}
			}
			
			//Se agregan los subtotales
			$total_aux = 0;
			$id_tipo_concepto_ant = "";
			$contador_linea++;
			foreach ($arr_totales_fp as $total_fp_aux) {
				if ($total_fp_aux["id_tipo_concepto"] != $id_tipo_concepto_ant) {
					//Se agrega el nombre del concepto
					$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$contador_linea.':H'.$contador_linea)->getFont()->setBold(true);
					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('G'.$contador_linea, $total_fp_aux["tipo_concepto"]);
					$contador_linea++;
				}
				if ($total_fp_aux["ind_negativo"] == "1") {
					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('G'.$contador_linea, "Subtotal ".$total_fp_aux["nombre"])
								->setCellValue('H'.$contador_linea, -round($total_fp_aux["valor"]));
					
					$objPHPExcel->getActiveSheet()->getStyle("G".$contador_linea)->getFont()->getColor()->setRGB("FF0000");
					$objPHPExcel->getActiveSheet()->getStyle("H".$contador_linea)->getFont()->getColor()->setRGB("FF0000");
					
					$total_aux -= $total_fp_aux["valor"];
				} else {
					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('G'.$contador_linea, "Subtotal ".$total_fp_aux["nombre"])
								->setCellValue('H'.$contador_linea, round($total_fp_aux["valor"]));
					
					$total_aux += $total_fp_aux["valor"];
				}
				$id_tipo_concepto_ant = $total_fp_aux["id_tipo_concepto"];
				$contador_linea++;
			}
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$contador_linea.':H'.$contador_linea)->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('G'.$contador_linea, "TOTAL")
						->setCellValue('H'.$contador_linea, round($total_aux));
			
			/*//Se agregan los subtotales
			$total_aux = 0;
			$contador_linea++;
			foreach ($arr_totales_fp as $total_fp_aux) {
				if ($total_fp_aux["ind_negativo"] == "1") {
					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('G'.$contador_linea, "Subtotal ".$total_fp_aux["nombre"])
								->setCellValue('H'.$contador_linea, -round($total_fp_aux["valor"]));
					
					$objPHPExcel->getActiveSheet()->getStyle("G".$contador_linea)->getFont()->getColor()->setRGB("FF0000");
					$objPHPExcel->getActiveSheet()->getStyle("H".$contador_linea)->getFont()->getColor()->setRGB("FF0000");
					
					$total_aux -= $total_fp_aux["valor"];
				} else {
					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('G'.$contador_linea, "Subtotal ".$total_fp_aux["nombre"])
								->setCellValue('H'.$contador_linea, round($total_fp_aux["valor"]));
					
					$total_aux += $total_fp_aux["valor"];
				}
				$contador_linea++;
			}
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$contador_linea.':H'.$contador_linea)->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('G'.$contador_linea, "TOTAL")
						->setCellValue('H'.$contador_linea, round($total_aux));*/
			
			//Se renombra la hoja actual
			$objPHPExcel->getActiveSheet()->setTitle('Reporte por paciente');
			
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
			
			// Save Excel 2007 file
			$id_usuario = $_SESSION["idUsuario"];
			
			//Se borra el reporte previamente generado por el usuario
			@unlink("./tmp/reporte_paciente_".$id_usuario.".xlsx");
			
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save("./tmp/reporte_paciente_".$id_usuario.".xlsx");
		?>
		<form name="frm_reporte_paciente" id="frm_reporte_paciente" method="post" action="tmp/reporte_paciente_<?php echo($id_usuario); ?>.xlsx">
		</form>
		<script id="ajax" type="text/javascript">
			document.getElementById("frm_reporte_paciente").submit();
		</script>
        <?php
			break;
			
		case "3": //Reporte de auditoría
			require_once("../funciones/pdf/funciones.php");
			require_once("../db/DbPagos.php");
			require_once("../funciones/Utilidades.php");
			
			$utilidades = new Utilidades();
			$dbPagos = new DbPagos();
			
			$fecha_ini = $utilidades->str_decode($_POST["hdd_fecha_ini_aud"]);
			$fecha_fin = $utilidades->str_decode($_POST["hdd_fecha_fin_aud"]);
			$id_convenio = $utilidades->str_decode($_POST["hdd_id_convenio_aud"]);
			
			//Se crea el array de hojas a incluir
			$arr_hojas[0]["estado_pago"] = 2;
			$arr_hojas[0]["nombre_hoja"] = "Pagos";
			$arr_hojas[1]["estado_pago"] = 3;
			$arr_hojas[1]["nombre_hoja"] = "Borrados";
			
			$tipo_pago_np_obj = $dbTiposPago->getTipoPago("99");
			
			foreach ($arr_hojas as $indice => $hoja_aux) {
				$lista_pagos = $dbPagos->getListaReporteTesoseriaAuditoria($fecha_ini, $fecha_fin, $id_convenio, $hoja_aux["estado_pago"]);
				
				$contador_linea = 1;
				
				if ($indice > 0) {
					$objPHPExcel->createSheet();
				}
				
				$objPHPExcel->setActiveSheetIndex($indice);
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
				$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
				$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(35);
				$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
				$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
				$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
				$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
				$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(40);
				
				$objPHPExcel->getActiveSheet()
							->setCellValue('A'.$contador_linea, "Fecha")
							->setCellValue('B'.$contador_linea, "No. Recibo")
							->setCellValue('C'.$contador_linea, "Paciente")
							->setCellValue('D'.$contador_linea, "Tipo documento")
							->setCellValue('E'.$contador_linea, "Número documento")
							->setCellValue('F'.$contador_linea, "Tercero")
							->setCellValue('G'.$contador_linea, "Tipo documento")
							->setCellValue('H'.$contador_linea, "Número documento")
							->setCellValue('I'.$contador_linea, "Número factura")
							->setCellValue('J'.$contador_linea, "Cód. concepto")
							->setCellValue('K'.$contador_linea, "Nombre concepto")
							->setCellValue('L'.$contador_linea, "Tipo pago")
							->setCellValue('M'.$contador_linea, "Usuario admisión")
							->setCellValue('N'.$contador_linea, "Usuario pago")
							->setCellValue('O'.$contador_linea, "Usuario última modificación")
							->setCellValue('P'.$contador_linea, "Convenio")
							->setCellValue('Q'.$contador_linea, "Plan")
							->setCellValue('R'.$contador_linea, "Valor base")
							->setCellValue('S'.$contador_linea, "Valor base cuota mod.")
							->setCellValue('T'.$contador_linea, "Valor pagado")
							->setCellValue('U'.$contador_linea, "Valor pagado cuota mod.")
							->setCellValue('V'.$contador_linea, "Cantidad")
							->setCellValue('W'.$contador_linea, "Valor total")
							->setCellValue('X'.$contador_linea, "Observaciones");
				$objPHPExcel->getActiveSheet()->getStyle('A'.$contador_linea.':X'.$contador_linea)->getFont()->setBold(true);
				$contador_linea++;
				
				foreach ($lista_pagos as $pago_aux) {
					//Valor total del pago
					$valor_total_aux = intval($pago_aux["total_pago"], 10);
					
					//Se obtienen los valores pagados para el primer tipo de pago
					$valor_insumo_aux = 0;
					$valor_cuota_aux = 0;
					$valor_neto_aux = 0;
					
					$valor_base_aux = $pago_aux["valor"];
					$valor_base_cuota_aux = $pago_aux["valor_cuota"];
					
					if ($pago_aux['ind_boleta'] == '1') {
						if ($pago_aux['id_medio_pago'] == '0') {
							$valor_insumo_aux = intval($pago_aux['valor'], 10) - intval($pago_aux['valor_cuota'], 10);
							$valor_cuota_aux = 0;
						} else if (intval($pago_aux["total_cuota"], 10) > 0) {
							$valor_insumo_aux = 0;
							$valor_cuota_aux = intval($pago_aux['valor_cuota'], 10) * intval($pago_aux["valor_pago"], 10) / intval($pago_aux["total_cuota"], 10);
						} else {
							$valor_insumo_aux = 0;
							$valor_cuota_aux = 0;
						}
					} else {
						if ($valor_total_aux > 0) {
							$valor_insumo_aux = $valor_base_aux * (intval($pago_aux["valor_pago"], 10) / $valor_total_aux);
							$valor_cuota_aux = $valor_base_cuota_aux * (intval($pago_aux["valor_pago"], 10) / $valor_total_aux);
						} else {
							$valor_insumo_aux = $valor_base_aux;
							$valor_cuota_aux = $valor_base_cuota_aux;
						}
						$valor_neto_aux = ($valor_insumo_aux - $valor_cuota_aux) * $pago_aux["cantidad"];
					}
					
					$factor_aux = 1;
					if ($pago_aux["ind_negativo"] == "1") {
						$factor_aux = -1;
						
						$objPHPExcel->getActiveSheet()->getStyle("L".$contador_linea)->getFont()->getColor()->setRGB("FF0000");
						$objPHPExcel->getActiveSheet()->getStyle("T".$contador_linea)->getFont()->getColor()->setRGB("FF0000");
						$objPHPExcel->getActiveSheet()->getStyle("U".$contador_linea)->getFont()->getColor()->setRGB("FF0000");
						$objPHPExcel->getActiveSheet()->getStyle("W".$contador_linea)->getFont()->getColor()->setRGB("FF0000");
					}
					
					//Variables de identificación del paciente/tercero
					$numero_documento_tercero_aux = "";
					if ($pago_aux["tipo_documento_tercero"] != "") {
						$numero_documento_tercero_aux = $pago_aux["numero_documento_tercero"];
						if ($pago_aux["numero_verificacion"] != "") {
							$numero_documento_tercero_aux .= "-".$pago_aux["numero_verificacion"];
						}
					}
					
					$nombre_aux = mb_strtoupper($funciones_persona->obtenerNombreCompleto($pago_aux["apellido_1"], $pago_aux["apellido_2"], $pago_aux["nombre_1"], $pago_aux["nombre_2"]), "UTF-8");
					$nombre_tercero_aux = mb_strtoupper($pago_aux["nombre_tercero"]);
					
					//Se agrega el producto
					$objPHPExcel->getActiveSheet()
								->setCellValue('A'.$contador_linea, $pago_aux["fecha_pago_t"])
								->setCellValue('B'.$contador_linea, $pago_aux["id_pago"])
								->setCellValue('C'.$contador_linea, $nombre_aux)
								->setCellValue('D'.$contador_linea, $pago_aux["tipo_documento"])
								->setCellValue('E'.$contador_linea, $pago_aux["numero_documento"])
								->setCellValue('F'.$contador_linea, $nombre_tercero_aux)
								->setCellValue('G'.$contador_linea, $pago_aux["tipo_documento_tercero"])
								->setCellValue('H'.$contador_linea, $numero_documento_tercero_aux)
								->setCellValue('I'.$contador_linea, $pago_aux["num_factura"])
								->setCellValue('J'.$contador_linea, $pago_aux["cod_insumo"])
								->setCellValue('K'.$contador_linea, strtoupper($pago_aux["nombre_insumo"]))
								->setCellValue('L'.$contador_linea, ($pago_aux["nombre_pago"] != "" ? $pago_aux["nombre_pago"] : $tipo_pago_np_obj["nombre"]))
								->setCellValue('M'.$contador_linea, $pago_aux["nombre_completo_crea"])
								->setCellValue('N'.$contador_linea, $pago_aux["nombre_completo_pago"])
								->setCellValue('O'.$contador_linea, $pago_aux["nombre_completo_mod"])
								->setCellValue('P'.$contador_linea, $pago_aux["nombre_convenio"])
								->setCellValue('Q'.$contador_linea, $pago_aux["nombre_plan"])
								->setCellValue('R'.$contador_linea, round($pago_aux["valor_base"]))
								->setCellValue('S'.$contador_linea, round($pago_aux["valor_cuota_base"]))
								->setCellValue('T'.$contador_linea, $factor_aux * round($valor_insumo_aux))
								->setCellValue('U'.$contador_linea, $factor_aux * round($valor_cuota_aux))
								->setCellValue('V'.$contador_linea, $pago_aux["cantidad"])
								->setCellValue('W'.$contador_linea, "=(T".$contador_linea."+U".$contador_linea.")*V".$contador_linea)
								->setCellValue('X'.$contador_linea, $pago_aux["observaciones_pago"]);
					$contador_linea++;
				}
				
				//Se renombra la hoja actual
				$objPHPExcel->getActiveSheet()->setTitle($hoja_aux["nombre_hoja"]);
			}
			
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
			
			// Save Excel 2007 file
			$id_usuario = $_SESSION["idUsuario"];
			
			//Se borra el reporte previamente generado por el usuario
			@unlink("./tmp/reporte_auditoria_".$id_usuario.".xlsx");
			
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save("./tmp/reporte_auditoria_".$id_usuario.".xlsx");
		?>
		<form name="frm_reporte_auditoria" id="frm_reporte_auditoria" method="post" action="tmp/reporte_auditoria_<?php echo($id_usuario); ?>.xlsx">
		</form>
		<script id="ajax" type="text/javascript">
			document.getElementById("frm_reporte_auditoria").submit();
		</script>
		<?php
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
