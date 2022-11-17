<?php
	require_once("../db/DbPacientes.php");
	require_once("../db/DbPlanes.php");
	require_once("../db/DbTiposLiquidacionQx.php");
	require_once("../db/DbLiquidacionesBase.php");
	require_once("../db/DbISS2001.php");
	require_once("../db/DbListasPrecios.php");
	require_once("../db/DbPagos.php");
	require_once("../db/DbMaestroProcedimientos.php");
	require_once("../db/DbListas.php");
	
	class LiquidadorPrecios {
		private $uvr_esp;
		private $uvr_ane;
		private $lista_uvr_ds;
		private $lista_uvr_m;
		
		//Función que liquida un grupo de procedimientos de acuerdo con la configuración de liquidación de un plan
		public function liquidar_cirugias($id_plan, $lista_procedimientos) {
			$dbPlanes = new DbPlanes();
			$dbTiposLiquidacionQx = new DbTiposLiquidacionQx();
			$dbISS2001 = new DbISS2001();
			$dbListasPrecios = new DbListasPrecios();
			
			//Se verifica si el plan tiene configuración de liquidación asociada
			$plan_obj = $dbPlanes->getPlan($id_plan);
			
			$ind_iss = $plan_obj["ind_iss"];
			$frac_iss = 1;
			if ($ind_iss == "1") {
				$frac_iss = $plan_obj["porc_iss"] / 100 + 1;
			}
			$id_liq_qx = $plan_obj["id_liq_qx"];
						
			$mapa_resultados = array();
			$mapa_liq_qx_det = array();
						
			if ($id_liq_qx != "") {
				//Se obtiene la configuración de liquidación específica
				$lista_liq_qx_det = $dbTiposLiquidacionQx->getListasTiposLiquidacionQxDet($id_liq_qx, 1);
								
				foreach ($lista_liq_qx_det as $liq_aux) {
					if (isset($mapa_liq_qx_det[$liq_aux["cod_componente"]])) {
						$arr_aux = $mapa_liq_qx_det[$liq_aux["cod_componente"]];
					} else {
						$arr_aux = array();
					}
					$liq_aux["num_aplicaciones"] = 0;
					array_push($arr_aux, $liq_aux);
					$mapa_liq_qx_det[$liq_aux["cod_componente"]] = $arr_aux;
				}
				
				//Se cargan los valores base de liquidación específicos
				if (isset($_SESSION["uvr_esp"])) {
					//Los valores se obtienen de la sesión
					$this->uvr_esp = $_SESSION["uvr_esp"];
					$this->uvr_ane = $_SESSION["uvr_ane"];
					$this->lista_uvr_ds = $_SESSION["lista_uvr_ds"];
					$this->lista_uvr_m = $_SESSION["lista_uvr_m"];
				} else {
					//Especialista
					$this->uvr_esp = 0;
					if (isset($mapa_liq_qx_det["E"])) {
						$arr_aux = $dbISS2001->getISS2001SP("39101");
						$this->uvr_esp = $arr_aux["uvr_iss_2001_s_p"];
					}
					
					//Anestesiólogo
					$this->uvr_ane = 0;
					if (isset($mapa_liq_qx_det["A"])) {
						$arr_aux = $dbISS2001->getISS2001SP("39102");
						$this->uvr_ane = $arr_aux["uvr_iss_2001_s_p"];
					}
					
					//Derechos de sala
					$this->lista_uvr_ds = array();
					if (isset($mapa_liq_qx_det["S"])) {
						$this->lista_uvr_ds = $dbISS2001->getListaISS2001DS();
					}
					
					//Materiales
					$this->lista_uvr_m = array();
					if (isset($mapa_liq_qx_det["M"])) {
						$this->lista_uvr_m = $dbISS2001->getListaISS2001M();
					}
					
					//Los valores se guardan en la sesión
					$_SESSION["uvr_esp"] = $this->uvr_esp;
					$_SESSION["uvr_ane"] = $this->uvr_ane;
					$_SESSION["lista_uvr_ds"] = $this->lista_uvr_ds;
					$_SESSION["lista_uvr_m"] = $this->lista_uvr_m;
					
				}
			
				//Se cargan los precios configurados y las UVR de los procedimientos
				$arr_cod_procs = array();
				foreach ($lista_procedimientos as $proc_aux) {
					array_push($arr_cod_procs, $proc_aux["cod_procedimiento"]);
				}
				
				$lista_valores_liq = $dbListasPrecios->getListaValoresLiqQx($id_plan, $arr_cod_procs);		
				
				//Se unifican las tablas de valores solicitados y valores configurados
				$lista_procedimientos_cx = array();
				foreach ($lista_procedimientos as $proc_aux) {
					foreach ($lista_valores_liq as $valor_liq_aux) {
						if ($valor_liq_aux["cod_procedimiento"] == $proc_aux["cod_procedimiento"] && $valor_liq_aux["tipo_bilateral"] == $proc_aux["tipo_bilateral"]) {
							$proc_aux["id_especialidad"] = $valor_liq_aux["id_especialidad"];
							$proc_aux["id_via"] = $valor_liq_aux["id_via"];
							$proc_aux["tipo_valor_iss_proc"] = $valor_liq_aux["tipo_valor_iss_proc"];
							$proc_aux["valor_iss_proc"] = $valor_liq_aux["valor_iss_proc"];
							$proc_aux["id_precio"] = $valor_liq_aux["id_precio"];
							$proc_aux["valor"] = $valor_liq_aux["valor"];
							array_push($lista_procedimientos_cx, $proc_aux);
							break;
						}
					}
				}
								
								
				if (count($lista_procedimientos_cx) == 0) {
					return $lista_procedimientos_cx;
				}
				
				
				//Se ordenan los registros de forma descendente por valor
				$lista_procedimientos_cx = $this->ordenar_procedimientos($lista_procedimientos_cx, $plan_obj["ind_iss"]);
								
				//Se obtienen la especialidad y la vía del procedimiento de mayor valor
				$id_especialidad_m = $lista_procedimientos_cx[0]["id_especialidad"];
				$id_via_m = $lista_procedimientos_cx[0]["id_via"];
				
				//Se organizan los procedimientos por especialidades y vías
				$mapa_procedimientos_qx = array();
				foreach ($lista_procedimientos_cx as $proc_aux) {
					if (isset($mapa_procedimientos_qx[$proc_aux["id_especialidad"]])) {
						$mapa_esp_aux = $mapa_procedimientos_qx[$proc_aux["id_especialidad"]];
					} else {
						$mapa_esp_aux = array();
					}
					if (isset($mapa_esp_aux[$proc_aux["id_via"]])) {
						$lista_via_aux = $mapa_esp_aux[$proc_aux["id_via"]];
					} else {
						$lista_via_aux = array();
					}
					array_push($lista_via_aux, $proc_aux);
					$mapa_esp_aux[$proc_aux["id_via"]] = $lista_via_aux;
					$mapa_procedimientos_qx[$proc_aux["id_especialidad"]] = $mapa_esp_aux;
				}
				
				foreach ($mapa_procedimientos_qx as $id_especialidad_aux => $mapa_vias_aux) {
					$id_via_esp_m = "";
					foreach ($mapa_vias_aux as $id_via_aux => $lista_procs_aux) {
						$orden_aux = 1;
						foreach ($lista_procs_aux as $proc_aux) {
							if ($id_via_esp_m == "") {
								$id_via_esp_m = $proc_aux["id_via"];
							}
							//Indicadores de misma especialidad y misma vía
							$ind_esp_aux = $id_especialidad_aux == $id_especialidad_m ? 1 : 0;
							if ($ind_esp_aux == 1) {
								$ind_via_aux = $id_via_aux == $id_via_m ? 1 : 0;
							} else {
								$ind_via_aux = $id_via_aux == $id_via_esp_m ? 1 : 0;
							}
							//echo("<br />Procedimiento: ".$proc_aux["cod_procedimiento"]." - Valor: ".$proc_aux["valor_iss_proc"]." - Orden: ".$orden_aux." - Especialidad: ".$ind_esp_aux." - V&iacute;a: ".$ind_via_aux."<br />");
							
							//Se liquida el valor por componente
							foreach ($mapa_liq_qx_det as $tipo_componente => $lista_porcentajes_aux) {
								//Se busca el porcentaje que mejor se ajuste al componente
								$porc_comp_aux = 0;
								$porc_bila_aux = 0;
								foreach ($lista_porcentajes_aux as $indice_aux => $porcentaje_aux) {
									//Se verifica que el orden coincida
									if ($porcentaje_aux["orden_proc_qx"] == $orden_aux || ($porcentaje_aux["orden_proc_qx"] < $orden_aux && $porcentaje_aux["ind_igual_sig"] == "1")) {
										if (($porcentaje_aux["ind_especialidad"] == $ind_esp_aux || $porcentaje_aux["ind_especialidad"] == "2") &&
												($porcentaje_aux["ind_via"] == $ind_via_aux || $porcentaje_aux["ind_via"] == "2")) {
											//Verificación de aplicación única
											if ($porcentaje_aux["ind_una_vez"] == "1" && $porcentaje_aux["num_aplicaciones"] >= 1) {
												continue;
											}
											$porc_comp_aux = $porcentaje_aux["porcentaje"];
											
											if ($proc_aux["tipo_bilateral"] == "2") {
												$porc_bila_aux = $porcentaje_aux["porc_bilateral"];
											} else {
												$porc_bila_aux = 0;
											}
											$porcentaje_aux["num_aplicaciones"]++;
											$lista_porcentajes_aux[$indice_aux] = $porcentaje_aux;
											$mapa_liq_qx_det[$tipo_componente] = $lista_porcentajes_aux;
											break;
										}
									}
								}
								
								//Se calcula el valor del componente
								$valor_comp_aux = $this->calcular_valor_componente($proc_aux, $ind_iss, $frac_iss, $tipo_componente, $porc_comp_aux, $porc_bila_aux);
								
								
								//Se agrega el valor calculado al mapa de resultados
								if (isset($mapa_resultados[$proc_aux["orden"]])) {
									$arr_proc_aux = $mapa_resultados[$proc_aux["orden"]];
									
									
								} else {
									$arr_proc_aux = array();
									$arr_proc_aux["cod_procedimiento"] = $proc_aux["cod_procedimiento"];
									$arr_proc_aux["componentes"] = array();
									$arr_proc_aux["valor_total"] = 0;
								}
								$arr_proc_aux["componentes"][$tipo_componente] = $valor_comp_aux;
								$arr_proc_aux["valor_total"] += $valor_comp_aux;
								$mapa_resultados[$proc_aux["orden"]] = $arr_proc_aux;
								
								
							}
							$orden_aux++;
							}
						}
					}
				}
		
			if(isset($proc_aux["valor_insumos_adicionales"])){
				
				$mapa_resultados[1]["valor_total"] = $proc_aux["valor_insumos_adicionales"] + $mapa_resultados[1]["valor_total"];
			}
			
			return $mapa_resultados;
		}        
		 
		private function ordenar_procedimientos($lista_procedimientos_cx, $ind_iss) {
			
			$lista_resultado = array();
			while (count($lista_procedimientos_cx) > 0) {
				//Se busca el registro mayor entre los exstentes
				$mayor_aux = 0;
				$indice_aux = -1;
				foreach ($lista_procedimientos_cx as $indice => $proc_aux) {   
					
					if ($ind_iss == "1") {
						if ($indice_aux == -1 || $proc_aux["valor_iss_proc"] > $mayor_aux) {
							$indice_aux = $indice;
							$mayor_aux = $proc_aux["valor_iss_proc"];
						}
					} else {
						if ($indice_aux == -1 || $proc_aux["valor"] > $mayor_aux) {
							$indice_aux = $indice;
							$mayor_aux = $proc_aux["valor"];
							
						}
					}
				
				}
				
				if ($indice_aux >= 0) {
					array_push($lista_resultado, $lista_procedimientos_cx[$indice_aux]);
					unset($lista_procedimientos_cx[$indice_aux]);
				}
			}
			
			return $lista_resultado;
		}
	
		private function calcular_valor_componente($procedimiento_obj, $ind_iss, $frac_iss, $tipo_componente, $porcentaje, $porc_bilateral) {
		
			$valor_calc = 0;
			
			if ($ind_iss == "1") {
					
				if ($procedimiento_obj["tipo_valor_iss_proc"] == "1") {
					//Valor en UVR
					
					switch ($tipo_componente) {
						case "E": //Especialista
							$valor_calc = $procedimiento_obj["valor_iss_proc"] * $this->uvr_esp * ($porcentaje / 100) * ($porc_bilateral / 100 + 1) * $frac_iss;
							//echo("\n"."Especialista ".$valor_calc." \n");
							break;
							
						case "A": //Anestesiólogo
							$valor_calc = $procedimiento_obj["valor_iss_proc"] * $this->uvr_ane * ($porcentaje / 100) * ($porc_bilateral / 100 + 1) * $frac_iss;
							//echo("Anestesia ".$valor_calc." \n");
							break;
													
						case "S": //Derechos de sala
					
							foreach ($this->lista_uvr_ds as $rango_aux) {
								if ($procedimiento_obj["valor_iss_proc"] <= $rango_aux["uvr2_iss_2001_d_s"]) {
									$rango_sel = $rango_aux;
									break;
								}
							}
							
							if (isset($rango_sel["valor_iss_2001_d_s"])) {
								$valor_calc_s = $rango_sel["valor_iss_2001_d_s"] * ($porcentaje / 100) * ($porc_bilateral / 100 + 1) * $frac_iss;
												
							//Se busca el rango de valor
							$rango_sel = array();
								if ($rango_sel["tipo_valor_iss_2001_d_s"] == "1") {
									$valor_calc_s *= $procedimiento_obj["valor_iss_proc"];
									
								}
							
							}
							//echo("Derechos de sala ".$valor_calc_s." \n");
							
							
							//Materiales
							//Se une Materiales con derechos de sala.
							$rango_sel_m = array();
							foreach ($this->lista_uvr_m as $rango_aux_m) {
								if ($procedimiento_obj["valor_iss_proc"] <= $rango_aux_m["uvr2_iss_2001_m"]) {
									$rango_sel_m = $rango_aux_m;
									break;
								}
							}
							if (isset($rango_sel_m["valor_iss_2001_m"])) {
								$valor_calc_m = $rango_sel_m["valor_iss_2001_m"] * ($porcentaje / 100) * ($porc_bilateral / 100 + 1) * $frac_iss;
								if ($rango_sel_m["tipo_valor_iss_2001_m"] == "1") {
									$valor_calc_m *= $procedimiento_obj["valor_iss_proc"];
								}
							}
							
							
							if($procedimiento_obj["orden"] == "1"){
								
							$valor_ad=$procedimiento_obj["valor"] + $procedimiento_obj["valor_insumos_adicionales"];	
							 									
							$procedimiento_obj["valor"]  =  $valor_ad;
							
							}
							
							//echo("Materiales ".$valor_calc_s." \n");							
								
							$valor_calc = $valor_calc_m + $valor_calc_s;
							
							break;
					}
				}else if(($procedimiento_obj["tipo_valor_iss_proc"] == "2") && $tipo_componente <> "T"){
					
					switch ($tipo_componente) {
						case "E": //Todos los componentes en uno
							//echo($procedimiento_obj["valor_iss_proc"]);
								
								$valor_calc = $procedimiento_obj["valor_iss_proc"] * ($porcentaje / 100) * ($porc_bilateral / 100 + 1);
														
							break;
					}
						
					
				} else {
										
					switch ($tipo_componente) {
						case "T": //Todos los componentes en uno
							//echo($procedimiento_obj["valor_iss_proc"]);
							$valor_calc = $procedimiento_obj["valor_iss_proc"] * ($porcentaje / 100) * ($porc_bilateral / 100 + 1) * $frac_iss;
														
							break;
					}
				}
			} else {
				//var_dump($tipo_componente);
				switch ($tipo_componente) {
					case "T": //Todos los componentes en uno
						$valor_calc = $procedimiento_obj["valor"] * ($porcentaje / 100) * ($porc_bilateral / 100 + 1);
						break;
					}
			}
		
			return round($valor_calc);
		}
		
		
		//Función que liquida los copagos y cuotas moderadoras de un grupo de productos
		public function liquidar_cop_cm($id_paciente, $lista_productos, $tipo_coti_paciente = "", $rango_paciente = "") {
			$dbPacientes = new DbPacientes();
			$dbLiquidacionesBase = new DbLiquidacionesBase();
			$dbPlanes = new DbPlanes();
			$dbPagos = new DbPagos();
			$dbMaestroProcedimientos = new DbMaestroProcedimientos();
			$dbListas = new DbListas();
			$mapa_resultados = array();
			
			//Se obtienen los datos del paciente
			$paciente_obj = $dbPacientes->getExistepaciente3($id_paciente);
		
			if (isset($paciente_obj["id_paciente"])) {
				if ($tipo_coti_paciente == "") {
					$tipo_coti_paciente = $paciente_obj["tipo_coti_paciente"];
				}
				if ($rango_paciente == "") {
					$rango_paciente = $paciente_obj["rango_paciente"];
				}
				$exento_pamo_paciente = $paciente_obj["exento_pamo_paciente"];
			} else {
				$exento_pamo_paciente = "1";
			}
			
			if(intval($tipo_coti_paciente)>3){
				
				$tipo_coti_paciente_obj = $dbListas->getDetalle($tipo_coti_paciente);
				$tipo_coti_paciente = $tipo_coti_paciente_obj["codigo_detalle"]; 
			}
			
			//Se separan los productos por plan
			$mapa_productos = array();
		
			foreach ($lista_productos as $producto_aux) {
				if (isset($mapa_productos[$producto_aux["id_plan"]])) {
					$arr_aux = $mapa_productos[$producto_aux["id_plan"]];
				} else {
					$arr_aux = array();
				}
				
				//Para el caso de los procedimientos, se verifica si son quirúrgicos
				if ($producto_aux["tipo_precio"] == "P") {
					//Se verifica si es un procedimiento quirúrgico
					$procedimiento_obj = $dbMaestroProcedimientos->getProcedimiento($producto_aux["cod_producto"]);
					
					if (isset($procedimiento_obj["ind_proc_qx"])) {
						$producto_aux["ind_proc_qx"] = $procedimiento_obj["ind_proc_qx"];
					} else {
						$producto_aux["ind_proc_qx"] = "";
					}
					
					//Se verifica si es un procedimiento exámen.
					if (isset($procedimiento_obj["ind_proc_examen"])) {
						$producto_aux["ind_proc_examen"] = $procedimiento_obj["ind_proc_examen"];
					} else {
						$producto_aux["ind_proc_examen"] = "";
					}
					
				} else {
					$producto_aux["ind_proc_examen"] = "";
				}
				
				if($procedimiento_obj["cod_procedimiento"] <> "890226"){//Si el procedimiento es una CONSULTA DE PRIMERA VEZ POR ESPECIALISTA EN ANESTESIOLOGÍA no se le cobra cuota ni copago.
					$producto_aux["tipo_procedimiento"] = $procedimiento_obj["tipo_procedimiento"];
				}else{
					$producto_aux["tipo_procedimiento"] = "";
				}
					
				
				
				
				array_push($arr_aux, $producto_aux);
				$mapa_productos[$producto_aux["id_plan"]] = $arr_aux;
			}
			
			//Se cargan los valores base de copagos y cuotas moderadoras
			
			if($tipo_coti_paciente == 1 || $exento_pamo_paciente == 0){//Si el paciente es contributivo cotizante no paga copago.
				$copago_base_obj = array();
			}else{
				$copago_base_obj = $dbLiquidacionesBase->getCopagoBaseActual($tipo_coti_paciente, $rango_paciente);	
			}
						
			
			if($exento_pamo_paciente == 0){// Si el paciente es exento de pago
				$cuota_moderadora_base_obj = array();	
			}else{
				$cuota_moderadora_base_obj = $dbLiquidacionesBase->getCuotaModeradoraBaseActual($tipo_coti_paciente, $rango_paciente);
			}
		
			
			foreach ($mapa_productos as $id_plan => $lista_productos_aux) {
				//Se cargan los datos del plan
				$plan_obj = $dbPlanes->getPlan($id_plan);
				
				if ($plan_obj["ind_calc_cc"] == "1") {
					//El plan requiere cálculo del copago y la cuota moderadora
					//Copago
					if (isset($copago_base_obj["tipo_cotizante"])) {
					
						//Se halla el máximo a pagar por copago teniendo en cuenta el copago pagado por el usuario en el año
						$obj_aux = $dbPagos->get_total_copago($id_paciente, "");
						
						$valor_max = $copago_base_obj["val_max_ano"] - $obj_aux["valor_cuota"];
					
						if ($copago_base_obj["val_max_evento"] < $valor_max) {
							$valor_max = $copago_base_obj["val_max_evento"];
						}
						if ($valor_max < 0) {
							$valor_max = 0;
						}
						
						
						$fraccion = $copago_base_obj["porcentaje"] / 100;
						
						foreach ($lista_productos_aux as $producto_aux) {
							
						
							
							if ($producto_aux["tipo_precio"] == "P" && ($producto_aux["ind_proc_qx"] == "1" || 
								$producto_aux["ind_proc_examen"] != "1" && $producto_aux["tipo_procedimiento"] == "P") ) {
								$valor_copago_aux = $producto_aux["valor"] * $producto_aux["cantidad"] * $fraccion;
								if ($valor_copago_aux > $valor_max) {
									$valor_copago_aux = $valor_max;
								}
								
								$valor_copago_aux = round($valor_copago_aux, 0);
								$valor_max -= $valor_copago_aux;
								
								$mapa_resultados[$producto_aux["orden"]]["tipo"] = "CO";
								$mapa_resultados[$producto_aux["orden"]]["tipo_precio"] = $producto_aux["tipo_precio"];
								$mapa_resultados[$producto_aux["orden"]]["cod_producto"] = $producto_aux["cod_producto"];
								$mapa_resultados[$producto_aux["orden"]]["valor_cuota"] = $valor_copago_aux;
								
								
							}
							
							//Valor de copago moderadora para exámenes 
							else if ($producto_aux["tipo_precio"] == "P" && $producto_aux["ind_proc_examen"] == "1") {
							
								$valor_copago_aux = $producto_aux["valor"] * $producto_aux["cantidad"] * $fraccion;
								
								if ($valor_copago_aux > $valor_max) {
									$valor_copago_aux = $valor_max;
								}
								$valor_copago_aux = round($valor_copago_aux, 0);
								$valor_max -= $valor_copago_aux;
								
								$mapa_resultados[$producto_aux["orden"]]["tipo"] = "CO";
								$mapa_resultados[$producto_aux["orden"]]["tipo_precio"] = $producto_aux["tipo_precio"];
								$mapa_resultados[$producto_aux["orden"]]["cod_producto"] = $producto_aux["cod_producto"];
								$mapa_resultados[$producto_aux["orden"]]["valor_cuota"] = $valor_copago_aux;
							}
													
							
						}
					} else {
						
						//El valor a cobrar es cero
						foreach ($lista_productos_aux as $producto_aux) {
							//var_dump($producto_aux);
							if ($producto_aux["tipo_precio"] == "P" && ($producto_aux["ind_proc_qx"] == "1" || $producto_aux["ind_proc_examen"] == "1")) {
								$mapa_resultados[$producto_aux["orden"]]["tipo"] = "CO";
								$mapa_resultados[$producto_aux["orden"]]["tipo_precio"] = $producto_aux["tipo_precio"];
								$mapa_resultados[$producto_aux["orden"]]["cod_producto"] = $producto_aux["cod_producto"];
								$mapa_resultados[$producto_aux["orden"]]["valor_cuota"] = 0;
							}
						}
					}
				
				
					//Cuota moderadora
					//if($producto_aux["id_proc_examen"] != 1){
						
						if (isset($cuota_moderadora_base_obj["tipo_cotizante"]) && $exento_pamo_paciente == "1" && $producto_aux["ind_proc_examen"] != 1 && $tipo_coti_paciente <> 4 ) {
						
							//La cuota moderadora solo se cobra una vez, se asignará al registro con el mayor valor
							$indice_aux = -1;
							$valor_aux = 0;
							foreach ($lista_productos_aux as $i => $producto_aux) {
								if ($producto_aux["tipo_precio"] != "I" && ($producto_aux["tipo_precio"] != "P" || $producto_aux["ind_proc_qx"] != "1")) {
									if (($producto_aux["valor"] * $producto_aux["cantidad"]) > $valor_aux) {
										$valor_aux = $producto_aux["valor"] * $producto_aux["cantidad"];
										$indice_aux = $i;
									}
								}
							}
							foreach ($lista_productos_aux as $i => $producto_aux) {
								if ($producto_aux["tipo_precio"] != "P" || $producto_aux["ind_proc_qx"] != "1" && $producto_aux["tipo_procedimiento"] == "C"  ) {
									$mapa_resultados[$producto_aux["orden"]]["tipo"] = "CM";
									$mapa_resultados[$producto_aux["orden"]]["tipo_precio"] = $producto_aux["tipo_precio"];
									$mapa_resultados[$producto_aux["orden"]]["cod_producto"] = $producto_aux["cod_producto"];
									if ($i == $indice_aux) {
										$mapa_resultados[$producto_aux["orden"]]["valor_cuota"] = $cuota_moderadora_base_obj["valor"];
									} else {
										$mapa_resultados[$producto_aux["orden"]]["valor_cuota"] = 0;
									}
								}
							}
						} else {
							
							//El valor a cobrar es cero
							foreach ($lista_productos_aux as $producto_aux) {
								if ($producto_aux["tipo_precio"] != "P" || $producto_aux["ind_proc_qx"] != "1" && $producto_aux["ind_proc_examen"] != 1 && $tipo_coti_paciente <> 4) {
									
									$mapa_resultados[$producto_aux["orden"]]["tipo"] = "CM";
									$mapa_resultados[$producto_aux["orden"]]["tipo_precio"] = $producto_aux["tipo_precio"];
									$mapa_resultados[$producto_aux["orden"]]["cod_producto"] = $producto_aux["cod_producto"];
									$mapa_resultados[$producto_aux["orden"]]["valor_cuota"] = 0;
								}
							}
						}
					//}
				}
			}
			
			return $mapa_resultados;
		}
		
	}
?>
