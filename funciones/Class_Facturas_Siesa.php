<?php
	/**
	 * Description of Class_Facturas_Siesa
	 *
	 * @author Feisar Moreno
	 */
	require_once("../funciones/Class_Conector_Siesa.php");
	require_once("../funciones/Class_Consultas_Siesa.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../db/DbPacientes.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbMaestroProcedimientos.php");
	require_once("../db/DbMaestroInsumos.php");
	require_once("../db/DbTiposPago.php");
	require_once("../db/DbAnticipos.php");
	require_once("../db/DbComunicacionSiesa.php");
	
	
	class Class_Facturas_Siesa {
		
		public function crearFactura($compania, $co_ventas, $bodega_ventas, $nit_tercero, $num_doc_usuario, $id_pago,
				$id_convenio, $id_plan, $ind_tipo_pago, $id_paciente, $lista_productos, $lista_medios_pago,
				$lista_anticipos, $id_usuario) {
					
			$classConectorSiesa = new Class_Conector_Siesa();
			$classConsultasSiesa = new Class_Consultas_Siesa();
			$funcionesPersona = new FuncionesPersona();
			$dbPacientes = new DbPacientes();
			$dbListas = new DbListas();
			$dbMaestroProcedimientos = new DbMaestroProcedimientos();
			$dbMaestroInsumos = new DbMaestroInsumos();
			$dbTiposPago = new DbTiposPago();
			$dbAnticipos = new DbAnticipos();
			$dbComunicacionSiesa = new DbComunicacionSiesa();
			$db_pagos = new DbPagos();
			
			$resultado = -1;
			/*Este id se usa como identificador interno en SIESA debido que, al hacer la integración de las dos historias clínicas, 
			 como aún se manejan 2 bases de datos diferentes, el id del pago puede cruzarse, 
			 con la base de datos de HCEO y habrán conflictos al momento de consultar el pago en SIESA. */
			$id_pago_siesa = $id_pago."-2";
			
			$pago_obj = $db_pagos->get_pago_id($id_pago);
			$fecha_pago = $pago_obj["fecha_pago"];
			//Consulta la condición de pago del tercero
			//$condicion_pago = $classConsultasSiesa->consultarCondicionPagoTercero($compania, $nit_tercero);
			
			//Se obtienen los datos del paciente
			$paciente_obj = $dbPacientes->getExistepaciente3($id_paciente);
			$nombre_completo = $funcionesPersona->obtenerNombreCompleto($paciente_obj["nombre_1"], $paciente_obj["nombre_2"], $paciente_obj["apellido_1"], $paciente_obj["apellido_2"]);
			
			$fecha_actual = $classConectorSiesa->getFechaActual();
			
			//Se halla el valor total a cruzar con los anticipos
			$total_anticipos = 0;
			foreach ($lista_anticipos as $anticipo_aux) {
				$total_anticipos += intval($anticipo_aux["valor"], 10);
			}
			
			//Array de datos para creación de factura directa
			$arr_datos = array(
				"Inicial" => array(
					"COMPANIA" => $compania
				),
				"Final" => array(
					"COMPANIA" => $compania
				),
				"Documentos" => array(
					"COMPANIA" => $compania,
					"CENTRO_OPERACION_FACTURA" => $co_ventas,
					"TIPO_DOCUMENTO_FACTURA" => "FEC",
					"CONSECUTIVO_FACTURA" => "1",
					"FECHA_FACTURA" => $fecha_actual,
					"TERCERO_FACTURA" => $nit_tercero,
					"SUCURSAL_FACTURA" => "001",
					"TIPO_CLIENTE" => "0001",
					"TERCERO_REMISION" => $nit_tercero,
					"SUCURSAL_REMISION" => "001",
					"TERCERO_VENDEDOR" => $num_doc_usuario,
					"PLAN" => $id_plan,
					"CONVENIO" => $id_convenio,
					"CONDICION_PAGO" => "CON",
					"NOTAS_FACTURA" => $id_pago_siesa
				)
			);
			
			//Se crea el array de movimientos
			$arr_movimientos = array();
			$cont_aux = 1;
			foreach ($lista_productos as $prod_aux) {
				if (($ind_tipo_pago == "1" && $prod_aux["valor_cuota"] > 0) || ($ind_tipo_pago == "2" && $prod_aux["valor"] > 0)) {
					switch ($prod_aux["tipo_precio"]) {
						case "P": //Procedimientos
							$prod_obj = $dbMaestroProcedimientos->getProcedimiento($prod_aux["cod_servicio"]);
							$cod_und_negocios = $prod_obj["cod_und_negocios"];
							$cod_centro_costos = $prod_obj["cod_centro_costos"];
							break;
						case "I": //Insumos
							$prod_obj = $dbMaestroInsumos->getInsumo($prod_aux["cod_servicio"]);
							$cod_und_negocios = $prod_obj["cod_und_negocios"];
							$cod_centro_costos = $prod_obj["cod_centro_costos"];
							break;
						case "C": //Copagos / cuotas moderadoras
							$cod_und_negocios = $prod_aux["cod_und_negocios"];
							$cod_centro_costos = $prod_aux["cod_centro_costos"];
							break;
						default:
							$cod_und_negocios = "";
							$cod_centro_costos = "";
							break;
					}
					
					if ($ind_tipo_pago == "1") {
						$valor_unitario = $prod_aux["valor_cuota"] / $prod_aux["cantidad"];
					} else {
						$valor_unitario = $prod_aux["valor"];
					}
					
					$arr_aux = array(
						"COMPANIA" => $compania,
						"CENTRO_OPERACION_FACTURA" => $co_ventas,
						"TIPO_DOCUMENTO_FACTURA" => "FEC",
						"CONSECUTIVO_FACTURA" => "1",
						"NRO_REGISTRO" => $cont_aux,
						"ID_BODEGA" => $bodega_ventas,
						"IND_OBSEQUIO" => "0",
						"CO_MOVIMIENTO" => $co_ventas,
						"CCOSTO_MOVIMIENTO" => $cod_centro_costos,
						"LISTA_PRECIO" => ($prod_aux["tipo_bilateral"] == "0" ? "3" : $prod_aux["tipo_bilateral"]),
						"UNIDAD_MEDIDA" => "UND",
						"CANTIDAD" => $prod_aux["cantidad"],
						"VLR_BRUTO" => $valor_unitario,
						"NOTAS_MOVIMIENTO" => $nombre_completo,
						"REFERENCIA" => $prod_aux["cod_servicio"],
						"UNIDAD_NEGOCIO" => $cod_und_negocios,
						"TIPO_PRECIO" => $prod_aux["tipo_precio"]
					);
					$arr_movimientos[$cont_aux] = $arr_aux;
					$cont_aux++;
				}
			}
			$arr_datos["Movimientos"] = $arr_movimientos;
			
			//Se crea el array de caja
			$arr_caja = array();
			$cont_aux = 1;
			$valor_descuento = 0;
			foreach ($lista_medios_pago as $medio_aux) {
				if ($medio_aux["tipoPago"] == "0") {
					//No se incluye el tipo de pago Boleta
					continue;
				}
				
				if ($medio_aux["tipoPago"] == "97") {
					//Descuento
					$valor_descuento += $medio_aux["valorPago"];
				} else {
					//Demás medios de pago
					switch ($medio_aux["tipoPago"]) {
						case "50": //Consignación nacional
							$medio_obj = $dbListas->getDetalle($medio_aux["BancoPago"]);
							$cod_medio_pago = $medio_obj["text_adicional_detalle"];
							break;
							
						case "60": //Trajeta de crédito
							$medio_obj = $dbListas->getDetalle($medio_aux["idFranquiciaTC"]);
							$cod_medio_pago = $medio_obj["text_adicional_detalle"];
							break;
							
						default:
							$medio_obj = $dbTiposPago->getTipoPago($medio_aux["tipoPago"]);
							$cod_medio_pago = $medio_obj["cod_integ_siesa"];
							break;
					}
					
					if ($cod_medio_pago != "") {
						if ($medio_aux["BancoPago"] != "") {
							$banco_obj = $dbListas->getDetalle($medio_aux["BancoPago"]);
							$codigo_banco = $banco_obj["codigo_detalle"];
						} else {
							$codigo_banco = "";
						}
						
						if ($medio_aux["anoVence"] != "" && $medio_aux["mesVence"] != "") {
							$fecha_vence = $medio_aux["anoVence"]."/".$medio_aux["mesVence"];
						} else {
							$fecha_vence = "";
						}
						
						if ($medio_aux["fechaConsigna"] != "") {
							$fecha_consigna = $classConectorSiesa->formatearFechaDMA($medio_aux["fechaConsigna"]);
						} else {
							$fecha_consigna = "";
						}
						
						$arr_aux = array(
							"COMPANIA" => $compania,
							"CENTRO_OPERACION_FACTURA" => $co_ventas,
							"TIPO_DOCUMENTO_FACTURA" => "FEC",
							"CONSECUTIVO_FACTURA" => "1",
							"MEDIO_PAGO" => $cod_medio_pago,
							"MONEDA" => "COP",
							"VALOR_MEDIO_PAGO" => $medio_aux["valorPago"],
							"ID_BANCO" => $codigo_banco,
							"NRO_CHEQUE" => ($medio_aux["numCheque"] != "" ? $medio_aux["numCheque"] : "0"),
							"NRO_CUENTA" => $medio_aux["numCuenta"],
							"COD_SEGURIDAD" => $medio_aux["codSeguridad"],
							"NRO_AUTORIZACION" => $medio_aux["numAutoriza"],
							"FECHA_VCTO" => $fecha_vence,
							"REFERENCIA_CONSIGNACION" => $medio_aux["referencia"],
							"FECHA_CONSIGNACION" => $fecha_consigna,
							"CASUAL_DEVOLUCION" => "",
							"TERCERO_CHEQUE_DEVUELTO" => "",
							"NOTAS" => "",
							"CCOSTOS_BANCO" => ""
						);
						$arr_caja[$cont_aux] = $arr_aux;
						$cont_aux++;
					}
				}
			}
			$arr_datos["Caja"] = $arr_caja;
			
			if ($valor_descuento > 0) {
				//Se halla el valor total sin incluir insumos y el de solo insumos
				$valor_ni_aux = 0;
				$valor_i_aux = 0;
				foreach ($arr_movimientos as $mov_aux) {
					if ($mov_aux["TIPO_PRECIO"] != "I") {
						$valor_ni_aux += ($mov_aux["CANTIDAD"] * $mov_aux["VLR_BRUTO"]);
					} else {
						$valor_i_aux += ($mov_aux["CANTIDAD"] * $mov_aux["VLR_BRUTO"]);
					}
				}
				if ($valor_ni_aux > 0) {
					$valor_total_aux = $valor_ni_aux;
					$bol_desc_insumos = false;
				} else {
					$valor_total_aux = $valor_i_aux;
					$bol_desc_insumos = true;
				}
				
				$saldo_aux = $valor_descuento;
				$lista_descuentos = array();
				foreach ($arr_movimientos as $mov_aux) {
					if (($mov_aux["TIPO_PRECIO"] == "I") == $bol_desc_insumos) {
						//Se calcula el descuento proporcional para el producto
						$valor_aux = $mov_aux["CANTIDAD"] * $mov_aux["VLR_BRUTO"];
						$valor_desc_aux = round(($valor_aux / $valor_total_aux) * $valor_descuento, 0);
						$saldo_aux -= $valor_desc_aux;
						
						$arr_aux = array();
						$arr_aux["NRO_REGISTRO"] = $mov_aux["NRO_REGISTRO"];
						$arr_aux["CANTIDAD"] = $mov_aux["CANTIDAD"];
						$arr_aux["VLR_TOTAL"] = $valor_desc_aux;
						
						array_push($lista_descuentos, $arr_aux);
					}
				}
				
				if ($saldo_aux != 0) {
					//Se agrega el saldo a alguno de los porductos con descuento
					foreach ($lista_descuentos as $indice => $descuento_aux) {
						if ($descuento_aux["VLR_TOTAL"] + $saldo_aux > 0) {
							$descuento_aux["VLR_TOTAL"] += $saldo_aux;
							$lista_descuentos[$indice] = $descuento_aux;
							break;
						}
					}
				}
				
				//Se crea el array de CuotasCxC
				$arr_descuentos = array();
				$cont_aux = 1;
				foreach ($lista_descuentos as $descuento_aux) {
					$arr_aux = array(
						"COMPANIA" => $compania,
						"CENTRO_OPERACION" => $co_ventas,
						"TIPO_DOCUMENTO" => "FEC",
						"CONSECUTIVO_DOCUMENTO" => "1",
						"NRO_REGISTRO" => $descuento_aux["NRO_REGISTRO"],
						"ORDEN" => 1,
						"VALR_UNITARIO" => $descuento_aux["VLR_TOTAL"] / $descuento_aux["CANTIDAD"],
						"VLR_TOTAL" => $descuento_aux["VLR_TOTAL"]
					);
					$arr_descuentos[$cont_aux] = $arr_aux;
					$cont_aux++;
				}
				$arr_datos["Descuentos"] = $arr_descuentos;
			}
			
			if (count($lista_anticipos) > 0) {
				//Se crea el array de CuotasCxC
				$arr_cuotas_cxc = array();
				$arr_aux = array(
					"COMPANIA" => $compania,
					"CENTRO_OPERACION_FACTURA" => $co_ventas,
					"TIPO_DOCUMENTO_FACTURA" => "FEC",
					"CONSECUTIVO_FACTURA" => "1",
					"VLR_CRUCE" => $total_anticipos,
					"FECHA_VCTO" => $fecha_actual,
					"FECHA_DSCTO_PP" => $fecha_actual
				);
				$arr_cuotas_cxc[1] = $arr_aux;
				$arr_datos["CuotasCxC"] = $arr_cuotas_cxc;
			}
			
			$resultado_aux = $classConectorSiesa->enviarXML(5, $arr_datos, $compania, 0);
			
			
			//Valida el resultado
			$resultado_1 = ($resultado_aux['ImportarDatosXMLResult'] == "Importacion exitosa") ? 1 : $resultado_aux['ImportarDatosXMLResult'];
			
			if ($resultado_1 == 1) {
				//Se consulta el número de la factura generada
				$num_factura = $classConsultasSiesa->consultarFacturaPago($compania, $id_pago,$fecha_pago);
			} else {
				$num_factura = "";
				
				//Se crea el log del error
				$dbComunicacionSiesa->insertar_comunicacion_siesa(5, $id_paciente, "", $id_pago, "", $resultado_1, $id_usuario);
			}
			
			$resultado_2 = -1;
			
			if ($resultado_1 == 1 && count($lista_anticipos) > 0) {
				//Se consultan datos adicionales de los anticipos
				$texto_notas = "FEC-".$num_factura;
				for ($i = 0; $i < count($lista_anticipos); $i++) {
					$anticipo_aux = $lista_anticipos[$i];
					$anticipo_obj = $dbAnticipos->get_anticipo($anticipo_aux["id_anticipo"]);
					$anticipo_aux["num_anticipo"] = $anticipo_obj["num_anticipo"];
					if ($anticipo_obj["numero_documento_tercero"] != "") {
						$anticipo_aux["nit_tercero"] = $anticipo_obj["numero_documento_tercero"];
					} else {
						$anticipo_aux["nit_tercero"] = $anticipo_obj["numero_documento"];
					}
					
					$lista_anticipos[$i] = $anticipo_aux;
					$texto_notas .= ", RCA-".$anticipo_obj["num_anticipo"];
				}

				//Array de datos para creación de entidad para cruce con anticipos
				$arr_datos = array(
					"Inicial" => array(
						"COMPANIA" => $compania
					),
					"Final" => array(
						"COMPANIA" => $compania
					),
					"Documentos" => array(
						"COMPANIA" => $compania,
						"CENTRO_OPERACION" => $co_ventas,
						"TIPO_DOCUMENTO" => "NTC",
						"CONSECUTIVO_DOCUMENTO" => "1",
						"FECHA_DOCUMENTO" => $fecha_actual,
						"TERCERO_DOCTO" => $nit_tercero,
						"NOTAS_DOCTO" => $texto_notas
					)
				);
				
				//Se crea el array de Cuotas CxC
				$arr_cuotas_cxc = array();
				$cont_aux = 1;
				
				//Movimiento crédito
				$arr_aux = array(
					"COMPANIA" => $compania,
					"CENTRO_OPERACION" => $co_ventas,
					"TIPO_DOCUMENTO" => "NTC",
					"CONSECUTIVO_DOCUMENTO" => "1",
					"AUXILIAR" => "13012606",
					"TERCERO" => $nit_tercero,
					"CO_MOVIMIENTO" => $co_ventas,
					"VALOR_DEBITO" => 0,
					"VALOR_CREDITO" => $total_anticipos,
					"NOTAS_MOVTO" => "Movimiento credito",
					"SUCURSAL" => "001",
					"TIPO_DOCTO_CRUCE" => "FEC",
					"CONSECUTIVO_DOCTO_CRUCE" => $num_factura,
					"FECHA_VCTO" => $fecha_actual,
					"FECHA_DESCT_PP" => $fecha_actual,
					"TERCERO_VENDEDOR" => $nit_tercero,
					"NOTAS_CXC" => "FEC-".$num_factura
				);
				$arr_cuotas_cxc[$cont_aux] = $arr_aux;
				$cont_aux++;
				
				//Movimientos débito
				foreach ($lista_anticipos as $anticipo_aux) {
					$arr_aux = array(
						"COMPANIA" => $compania,
						"CENTRO_OPERACION" => $co_ventas,
						"TIPO_DOCUMENTO" => "NTC",
						"CONSECUTIVO_DOCUMENTO" => "1",
						"AUXILIAR" => "2502080601",
						"TERCERO" => $anticipo_aux["nit_tercero"],
						"CO_MOVIMIENTO" => $co_ventas,
						"VALOR_DEBITO" => $anticipo_aux["valor"],
						"VALOR_CREDITO" => 0,
						"NOTAS_MOVTO" => "Movimiento debito",
						"SUCURSAL" => "001",
						"TIPO_DOCTO_CRUCE" => "RCA",
						"CONSECUTIVO_DOCTO_CRUCE" => $anticipo_aux["num_anticipo"],
						"FECHA_VCTO" => $fecha_actual,
						"FECHA_DESCT_PP" => $fecha_actual,
						"TERCERO_VENDEDOR" => $anticipo_aux["nit_tercero"],
						"NOTAS_CXC" => "FEC-".$num_factura
					);
					$arr_cuotas_cxc[$cont_aux] = $arr_aux;
					$cont_aux++;
				}
				$arr_datos["CuotasCxC"] = $arr_cuotas_cxc;
				
				$resultado_aux = $classConectorSiesa->enviarXML(18, $arr_datos, $compania, 0);
				
				//Valida el resultado
				$resultado_2 = ($resultado_aux['ImportarDatosXMLResult'] == "Importacion exitosa") ? 1 : $resultado_aux['ImportarDatosXMLResult'];
				
				if ($resultado_2 != 1) {
					//Se crea el log del error
					$dbComunicacionSiesa->insertar_comunicacion_siesa(18, $id_paciente, "", $id_pago, "", $resultado_2, $id_usuario);
				}
			} else {
				$resultado_2 = 1;
			}
			
			$resultado_aux = -1;
			if ($resultado_1 == 1 && $resultado_2 == 1) {
				$resultado_aux = 1;
			} else {
				$resultado_aux = "";
				if ($resultado_2 != 1) {
					$resultado_aux = $resultado_2;
				}
				if ($resultado_1 == 1) {
					$resultado_aux .= "<br /><b>Se gener&oacute; la factura n&uacute;mero ".$num_factura.", por favor realice la anulaci&oacute;n manual de la misma.</b>";
				} else {
					$resultado_aux = $resultado_1."<br />".$resultado_aux;
				}
			}
			
			$resultado = array();
			$resultado[0] = $resultado_aux;
			$resultado[1] = $num_factura;
			
			return $resultado;
		}
		
		public function crearEntidadFactura($compania, $cod_prestador, $co_ventas, $num_factura, $bodega_ventas, $arr_productos_aux, $num_doc_tercero, $num_doc_usuario, 
			$id_pago, $idConvenio, $idPlan, $ind_tipo_pago, $idPaciente, $id_usuario, $tipo_entidad) {
			
		 	$classConsultasSiesa = new Class_Consultas_Siesa();
			$classConectorSiesa = new Class_Conector_Siesa();
			$db_convenios = new DbConvenios();
			$db_pacientes = new DbPacientes();
			$db_admision = new DbAdmision();
			$dbComunicacionSiesa = new DbComunicacionSiesa();
			$db_pagos = new DbPagos();
			$db_planes = new DbPlanes();
						
			$rowid_movimiento = $classConsultasSiesa->consultarFacturasDetalle($compania, $num_factura);
			$convenio_obj = $db_convenios->getConvenio($idConvenio);
			$paciente_obj = $db_pacientes->getExistepaciente3($idPaciente);
			$admision_obj = $db_admision->get_ultima_admision($idPaciente);
			$pago_obj = $db_pagos->get_pago_id($id_pago);
			$plan_obj = $db_planes->getPlanDetalle($idPlan);
			
			$fecha_ini="";
			$fecha_fin="";
			$fecha_ini = strlen($convenio_obj["fact_inicial"])==1 ? "0".$convenio_obj["fact_inicial"] : $convenio_obj["fact_inicial"];
			$fecha_fin = strlen($convenio_obj["fact_final"])==1 ? "0".$convenio_obj["fact_final"] : $convenio_obj["fact_final"];
								
			$fecha_ini = substr(date("Ymd H:i:s"), 0, 6).$fecha_ini;
			$fecha_fin = substr(date("Ymd H:i:s"), 0, 6).$fecha_fin;						
			
			switch ($tipo_entidad) {//Tipo de la entidad es si va a nivel del documento o del movimiento.

				case 1 : 
						 $arr_datos = array(
							"Inicial" => array(
								"COMPANIA" => $compania
							),
							"Final" => array(
								"COMPANIA" => $compania
							),
							"Entidades" => array(
								1 => array(
									"COMPANIA" => $compania,
									"CENTRO_DE_OPERACION" => $co_ventas,
									"TIPO_DOCTO" => "FEC",
									"CONSECUTIVO" => $num_factura,
									"CLASE_DOCTO" => 522,
									"ROWID_MOVIMIENTO" => "0",
									"GRUPO_ENTIDAD" => "ANEXO FACTURA DOC",
									"ENTIDAD" => "ANEXO FACTURA",
									"ATRIBUTO" => 1,
									"DATO_NUMERICO" => "",
									"DATO_TEXTO" => $convenio_obj["contratacion_nombre"],
									"DATO_FECHA_HORA" => "",
									"TIPO_ENTIDAD" => "G504_1"
								),
								2 => array(
									"COMPANIA" => $compania,
									"CENTRO_DE_OPERACION" => $co_ventas,
									"TIPO_DOCTO" => "FEC",
									"CONSECUTIVO" => $num_factura,
									"CLASE_DOCTO" => 522,
									"ROWID_MOVIMIENTO" => "0",
									"GRUPO_ENTIDAD" => "ANEXO FACTURA DOC",
									"ENTIDAD" => "ANEXO FACTURA",
									"ATRIBUTO" => 2,
									"DATO_NUMERICO" => "",
									"DATO_TEXTO" => $plan_obj["cobertura_nombre"],
									"DATO_FECHA_HORA" => "",
									"TIPO_ENTIDAD" => "G504_1"
								),
								3 => array(
									"COMPANIA" => $compania,
									"CENTRO_DE_OPERACION" => $co_ventas,
									"TIPO_DOCTO" => "FEC",
									"CONSECUTIVO" => $num_factura,
									"CLASE_DOCTO" => 522,
									"ROWID_MOVIMIENTO" => "0",
									"GRUPO_ENTIDAD" => "ANEXO FACTURA DOC",
									"ENTIDAD" => "ANEXO FACTURA",
									"ATRIBUTO" => 3,
									"DATO_NUMERICO" => "",
									"DATO_TEXTO" => $convenio_obj["num_contrato"],
									"DATO_FECHA_HORA" => "",
									"TIPO_ENTIDAD" => "G504_1"
								),
								4 => array(
									"COMPANIA" => $compania,
									"CENTRO_DE_OPERACION" => $co_ventas,
									"TIPO_DOCTO" => "FEC",
									"CONSECUTIVO" => $num_factura,
									"CLASE_DOCTO" => 522,
									"ROWID_MOVIMIENTO" => "0",
									"GRUPO_ENTIDAD" => "ANEXO FACTURA DOC",
									"ENTIDAD" => "ANEXO FACTURA",
									"ATRIBUTO" => 4,
									"DATO_NUMERICO" => "",
									"DATO_TEXTO" => $paciente_obj["tipo_cotizante"],
									"DATO_FECHA_HORA" => "",
									"TIPO_ENTIDAD" => "G504_1"
								),
								5 => array(
									"COMPANIA" => $compania,
									"CENTRO_DE_OPERACION" => $co_ventas,
									"TIPO_DOCTO" => "FEC",
									"CONSECUTIVO" => $num_factura,
									"CLASE_DOCTO" => 522,
									"ROWID_MOVIMIENTO" => "0",
									"GRUPO_ENTIDAD" => "ANEXO FACTURA DOC",
									"ENTIDAD" => "ANEXO FACTURA",
									"ATRIBUTO" => 5,
									"DATO_NUMERICO" => "",
									"DATO_TEXTO" => $pago_obj["num_poliza"],
									"DATO_FECHA_HORA" => "",
									"TIPO_ENTIDAD" => "G504_1"
								),
								6 => array(
									"COMPANIA" => $compania,
									"CENTRO_DE_OPERACION" => $co_ventas,
									"TIPO_DOCTO" => "FEC",
									"CONSECUTIVO" => $num_factura,
									"CLASE_DOCTO" => 522,
									"ROWID_MOVIMIENTO" => "0",
									"GRUPO_ENTIDAD" => "ANEXO FACTURA DOC",
									"ENTIDAD" => "ANEXO FACTURA",
									"ATRIBUTO" => 6,
									"DATO_NUMERICO" => "",
									"DATO_TEXTO" => "",
									"DATO_FECHA_HORA" => $fecha_ini,
									"TIPO_ENTIDAD" => "G504_1"
								),
								7 => array(
									"COMPANIA" => $compania,
									"CENTRO_DE_OPERACION" => $co_ventas,
									"TIPO_DOCTO" => "FEC",
									"CONSECUTIVO" => $num_factura,
									"CLASE_DOCTO" => 522,
									"ROWID_MOVIMIENTO" => "0",
									"GRUPO_ENTIDAD" => "ANEXO FACTURA DOC",
									"ENTIDAD" => "ANEXO FACTURA",
									"ATRIBUTO" => 7,
									"DATO_NUMERICO" => "",
									"DATO_TEXTO" => "",
									"DATO_FECHA_HORA" => $fecha_fin,
									"TIPO_ENTIDAD" => "G504_1"
								),
								8 => array(
									"COMPANIA" => $compania,
									"CENTRO_DE_OPERACION" => $co_ventas,
									"TIPO_DOCTO" => "FEC",
									"CONSECUTIVO" => $num_factura,
									"CLASE_DOCTO" => 522,
									"ROWID_MOVIMIENTO" => "0",
									"GRUPO_ENTIDAD" => "ANEXO FACTURA DOC",
									"ENTIDAD" => "ANEXO FACTURA",
									"ATRIBUTO" => 8,
									"DATO_NUMERICO" => "",
									"DATO_TEXTO" => $cod_prestador,
									"DATO_FECHA_HORA" => "",
									"TIPO_ENTIDAD" => "G504_1"
								),
								
							)
						);
						
						$resultado_aux = $classConectorSiesa->enviarXML(22, $arr_datos, $compania, 0);
						$resultado = ($resultado_aux['ImportarDatosXMLResult'] == "Importacion exitosa") ? 1 : $resultado_aux['ImportarDatosXMLResult'];

						if ($resultado <> 1) {
							$resultado = "En factura, ha fallado al intentar enviar la entidad del documento ".$resultado;
							$dbComunicacionSiesa->insertar_comunicacion_siesa(22, $id_paciente, "", $id_pago, "", $resultado, $id_usuario);
						}
						return $resultado;
									
					break;
					case 2:
						$pago_det_obj = $db_pagos->pagosDetalleByIdPago($id_pago);
						$posicion=0;
						$arr_entidades = array();
						if(is_array($rowid_movimiento)){
							foreach($arr_productos_aux as $producto){
																
										$arr_aux = array(
											"COMPANIA" => $compania,
											"CENTRO_DE_OPERACION" => $co_ventas,
											"TIPO_DOCTO" => "FEC",
											"CONSECUTIVO" => $num_factura,
											"CLASE_DOCTO" => 522,
											"ROWID_MOVIMIENTO" => $rowid_movimiento[$posicion]["ROWID_MOVTO"],
											"GRUPO_ENTIDAD" => "ANEXO FACTURA",
											"ENTIDAD" => "ANEXO FACTURA MOV",
											"ATRIBUTO" => 1,
											"DATO_NUMERICO" => "",
											"DATO_TEXTO" => $pago_det_obj[$posicion]["num_autorizacion"],
											"DATO_FECHA_HORA" => "",
											"TIPO_ENTIDAD" => "G504_2"
										);
										
										array_push($arr_entidades, $arr_aux);
										
										$arr_aux = array(
											"COMPANIA" => $compania,
											"CENTRO_DE_OPERACION" => $co_ventas,
											"TIPO_DOCTO" => "FEC",
											"CONSECUTIVO" => $num_factura,
											"CLASE_DOCTO" => 522,
											"ROWID_MOVIMIENTO" => $rowid_movimiento[$posicion]["ROWID_MOVTO"],
											"GRUPO_ENTIDAD" => "ANEXO FACTURA",
											"ENTIDAD" => "ANEXO FACTURA MOV",
											"ATRIBUTO" => 2,
											"DATO_NUMERICO" => "",
											"DATO_TEXTO" => $pago_obj["num_mipress"],
											"DATO_FECHA_HORA" => "",
											"TIPO_ENTIDAD" => "G504_2"
										);
										
										array_push($arr_entidades, $arr_aux);
										
										$arr_aux = array(
											"COMPANIA" => $compania,
											"CENTRO_DE_OPERACION" => $co_ventas,
											"TIPO_DOCTO" => "FEC",
											"CONSECUTIVO" => $num_factura,
											"CLASE_DOCTO" => 522,
											"ROWID_MOVIMIENTO" => $rowid_movimiento[$posicion]["ROWID_MOVTO"],
											"GRUPO_ENTIDAD" => "ANEXO FACTURA",
											"ENTIDAD" => "ANEXO FACTURA MOV",
											"ATRIBUTO" => 3,
											"DATO_NUMERICO" => "",
											"DATO_TEXTO" => $pago_obj["num_ent_mipress"],
											"DATO_FECHA_HORA" => "",
											"TIPO_ENTIDAD" => "G504_2"
										);
										
										
										array_push($arr_entidades, $arr_aux);
										$posicion++;
									}
						}

						$arr_datos = array(
							"Inicial" => array(
								"COMPANIA" => $compania
							),
							"Final" => array(
								"COMPANIA" => $compania
							),
							"Entidades" => $arr_entidades
							
						);
						
						
						
						$resultado_aux = $classConectorSiesa->enviarXML(22, $arr_datos, $compania, 0);
						
						$resultado = ($resultado_aux['ImportarDatosXMLResult'] == "Importacion exitosa") ? 1 : $resultado_aux['ImportarDatosXMLResult'];
						if($resultado <> 1){
							$resultado = "En factura, ha fallado al intentar enviar la entidad del movimiento ".$resultado;
							$dbComunicacionSiesa->insertar_comunicacion_siesa(22, $idPaciente, "", $idPago, "", $resultado, $id_usuario);
						}
						
						return $resultado;
						
						break;
					}	
					
			}
		
	}
?>
