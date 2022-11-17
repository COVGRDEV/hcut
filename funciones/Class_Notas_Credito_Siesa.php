<?php
	/**
	 * Description of Class_Notas_Credito_Siesa
	 *
	 * @author Feisar Moreno
	 */
	require_once("../funciones/Class_Conector_Siesa.php");
	require_once("../funciones/Class_Consultas_Siesa.php");
	require_once("../db/DbPagos.php");
	require_once("../db/DbAnticipos.php");
	require_once("../db/DbPacientes.php");
	require_once("../db/DbTerceros.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbMaestroProcedimientos.php");
	require_once("../db/DbMaestroInsumos.php");
	require_once("../db/DbTiposPago.php");
	require_once("../db/DbVariables.php");
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbComunicacionSiesa.php");
		
	class Class_Notas_Credito_Siesa {
		
		public function crearNotaCredito($compania, $co_ventas, $bodega_ventas, $num_doc_usuario, $id_pago, $ind_tipo_pago, $id_causal_borra, $id_usuario) {
			$classConectorSiesa = new Class_Conector_Siesa();
			$classConsultasSiesa = new Class_Consultas_Siesa();
			$dbPagos = new DbPagos();
			$dbAnticipos = new DbAnticipos();
			$dbPacientes = new DbPacientes();
			$dbTerceros = new DbTerceros();
			$dbListas = new DbListas();
			$dbMaestroProcedimientos = new DbMaestroProcedimientos();
			$dbMaestroInsumos = new DbMaestroInsumos();
			$dbTiposPago = new DbTiposPago();
			$dbVariables = new Dbvariables();
			$dbUsuarios = new DbUsuarios();
			$dbComunicacionSiesa = new DbComunicacionSiesa();
			
			$resultado = -1;
			
			//Se cargan los datos del pago
			$pago_obj = $dbPagos->get_pago_id($id_pago);
			$lista_pagos_detalle = $dbPagos->get_lista_pagos_detalle($id_pago);
			//$lista_pagos_det_medios = $dbPagos->getListaPagosDetMedios($id_pago);
			$usuario_pago_obj = $dbUsuarios->getUsuario($pago_obj["id_usuario_pago"]);
			
			//Se halla el nit del tercero paciente/cliente
			if ($pago_obj["id_tercero"] != "") {
				$tercero_obj = $dbTerceros->getTercero($pago_obj["id_tercero"]);
				$nit_tercero = $tercero_obj["numero_documento"];
			} else {
				//Se busca el número de documento del paciente
				$paciente_obj = $dbPacientes->getExistepaciente3($pago_obj["id_paciente"]);
				$nit_tercero = $paciente_obj["numero_documento"];
			}
			
			//Consulta la condición de pago del tercero
			$condicion_pago = $classConsultasSiesa->consultarCondicionPagoTercero($compania, $nit_tercero);
			
			//Se halla el código de la causal de devolución
			$causal_obj = $dbListas->getDetalle($id_causal_borra);
			$cod_causal_devolucion = $causal_obj["text_adicional_detalle"];
			
			$fecha_actual = $classConectorSiesa->getFechaActual();
			
			//Array de datos para creación de nota crédito directa
			$arr_datos = array(
				"Inicial" => array(
					"COMPANIA" => $compania
				),
				"Final" => array(
					"COMPANIA" => $compania
				),
				"Documentos" => array(
					"COMPANIA" => $compania,
					"CONSECUTIVO_DOCUMENTO" => 1,
					"FECHA_DOCUMENTO" => $fecha_actual,
					"TIPO_DOCTO_FACTURA" => "FEC",
					"CONSECUTIVO_DOCTO_FACTURA" => $pago_obj["num_factura"]
				)
			);
			
			if ($ind_tipo_pago == "1") {
				//Se anula el concepto de cuota moderadora / copago
				$variable_obj_aux = $dbVariables->getVariable(22);
				$cod_servicio_cc = $variable_obj_aux["valor_variable"];
				
				//Se busca el concepto con el mayor valor, se tomarán de él la unidad de negocios y el centro de costos
				$valor_max_aux = -1;
				$tipo_precio_aux = "";
				$cod_servicio_max_aux = "";
				$valor_copagos = 0;
				foreach ($lista_pagos_detalle as $detalle_aux) {
					$valor_copagos += $detalle_aux["valor_cuota"];
					if ($detalle_aux["valor"] > $valor_max_aux) {
						$valor_max_aux = $detalle_aux["valor"];
						$tipo_precio_aux = $detalle_aux["tipo_precio"];
						switch ($tipo_precio_aux) {
							case "P": //Procedimientos
								$cod_servicio_max_aux = $detalle_aux["cod_procedimiento"];
								break;
							case "I": //Insumos
								$cod_servicio_max_aux = $detalle_aux["cod_insumo"];
								break;
							default:
								$cod_servicio_max_aux = "";
								break;

						}
					}
				}
				
				switch ($tipo_precio_aux) {
					case "P": //Procedimientos
						$prod_obj_aux = $dbMaestroProcedimientos->getProcedimiento($cod_servicio_max_aux);
						$cod_und_negocios_aux = $prod_obj_aux["cod_und_negocios"];
						$cod_centro_costos_aux = $prod_obj_aux["cod_centro_costos"];
						break;
					case "I": //Insumos
						$prod_obj_aux = $dbMaestroInsumos->getInsumo($cod_servicio_max_aux);
						$cod_und_negocios_aux = $prod_obj_aux["cod_und_negocios"];
						$cod_centro_costos_aux = $prod_obj_aux["cod_centro_costos"];
						break;
					default:
						$cod_und_negocios_aux = "";
						$cod_centro_costos_aux = "";
						break;
				}
				
				//Se crea el registro
				$arr_aux = array();
				$arr_aux["cod_servicio"] = $cod_servicio_cc;
				$arr_aux["tipo_precio"] = "C"; //Tipo copago / cuota moderadora
				$arr_aux["tipo_bilateral"] = "0";
				$arr_aux["cantidad"] = 1;
				$arr_aux["valor"] = 0;
				$arr_aux["valor_cuota"] = $valor_copagos;
				$arr_aux["cod_und_negocios"] = $cod_und_negocios_aux;
				$arr_aux["cod_centro_costos"] = $cod_centro_costos_aux;
				
				$lista_productos_aux = array();
				array_push($lista_productos_aux, $arr_aux);
			} else {
				//Se anulan los conceptos facturados
				$lista_productos_aux = $lista_pagos_detalle;
			}
			
			//Se obtiene el listado de movimientos de SIESA
			$lista_detalle_externo = $classConsultasSiesa->consultarFacturasDetalle($compania, $pago_obj["num_factura"]);
			$mapa_servicios_rowid = array();
			foreach ($lista_detalle_externo as $det_ext_aux) {
				$mapa_servicios_rowid[trim($det_ext_aux["REFERENCIA"])] = $det_ext_aux["ROWID_MOVTO"];
			}
			
			//Se crea el array de movimientos
			$arr_movimientos = array();
			$cont_aux = 1;
			$valor_total = 0;
			foreach ($lista_productos_aux as $indice => $prod_aux) {
				if (($ind_tipo_pago == "1" && $prod_aux["valor_cuota"] > 0) || $prod_aux["valor"] > 0) {
					switch ($prod_aux["tipo_precio"]) {
						case "P": //Procedimiento
							$prod_aux["cod_servicio"] = $prod_aux["cod_procedimiento"];
							$prod_obj = $dbMaestroProcedimientos->getProcedimiento($prod_aux["cod_servicio"]);
							$prod_aux["cod_und_negocios"] = $prod_obj["cod_und_negocios"];
							$prod_aux["cod_centro_costos"] = $prod_obj["cod_centro_costos"];
							break;
						case "I": //Insumo
							$prod_aux["cod_servicio"] = $prod_aux["cod_insumo"];
							$prod_obj = $dbMaestroInsumos->getInsumo($prod_aux["cod_servicio"]);
							$prod_aux["cod_und_negocios"] = $prod_obj["cod_und_negocios"];
							$prod_aux["cod_centro_costos"] = $prod_obj["cod_centro_costos"];
							break;
						case "C": //Copago / cuota moderadora
							break;
						default:
							$prod_aux["cod_und_negocios"] = "";
							$prod_aux["cod_centro_costos"] = "";
							break;
					}
					$lista_productos_aux[$indice] = $prod_aux;
					
					if ($ind_tipo_pago == "1") {
						$valor_unitario = $prod_aux["valor_cuota"] / $prod_aux["cantidad"];
						$valor_total += $prod_aux["valor_cuota"];
					} else {
						$valor_unitario = $prod_aux["valor"];
						$valor_total += ($prod_aux["valor"] * $prod_aux["cantidad"]);
					}
					
					$arr_aux = array(
						"COMPANIA" => $compania,
						"CONSECUTIVO_DOCUMENTO" => 1,
						"NRO_REGISTRO" => $cont_aux,
						"REFERENCIA" => $prod_aux["cod_servicio"],
						"BODEGA" => $bodega_ventas,
						"LOTE" => "",
						"MOTIVO" => "1",
						"IND_OBSEQUIO" => "0",
						"CO_MOVTO" => $co_ventas,
						"UN_MOVTO" => $prod_aux["cod_und_negocios"],
						"CCOSTO_MOVTO" => $prod_aux["cod_centro_costos"],
						"UNIDAD_MEDIDA" => "UND",
						"CANTIDAD_BASE" => $prod_aux["cantidad"],
						"IND_IMPTO_ASUMIDO" => "0",
						"NOTAS" => "",
						"CAUSAL_DEVOLUCION" => $cod_causal_devolucion,
						"ROWID_MOVTO" => $mapa_servicios_rowid[$prod_aux["cod_servicio"]]
					);
					$arr_movimientos[$cont_aux] = $arr_aux;
					$cont_aux++;
				}
			}
			$arr_datos["Movimientos"] = $arr_movimientos;
			
			//Se verifica si el pago tiene anticipos asociados
			//$lista_anticipos = $dbAnticipos->get_lista_anticipos_pago($id_pago, 1);
			
			//Se crea el array de CxC
			$arr_cxc = array();
			$cont_aux = 1;
			if ($valor_total > 0) {
				//Se agrega la factura
				$arr_aux = array(
					"COMPANIA" => $compania,
					"CENTRO_OPERACION" => $co_ventas,
					"TIPO_DOCTO" => "NCE",
					"CONSEC_DOCTO" => "1",
					"TIPO_DOCTO_CRUCE" => "",
					"CONSEC_DOCTO_CRUCE" => 1,
					"VLR_CRUCE" => $valor_total,
					"FECHA_VCTO" => $fecha_actual,
					"FECHA_DSCTO_PP" => $fecha_actual
				);
				$arr_cxc[$cont_aux] = $arr_aux;
			}
			
			$arr_datos["CuotasCxC"] = $arr_cxc;
			
			//var_dump($arr_datos);
			$resultado_aux = $classConectorSiesa->enviarXML(6, $arr_datos, $compania, 0);
			
			//Valida el resultado
			$resultado_1 = ($resultado_aux['ImportarDatosXMLResult'] == "Importacion exitosa") ? 1 : $resultado_aux['ImportarDatosXMLResult'];
			
			if ($resultado_1 == 1) {
				//Se consulta el número de la nota crédito generada
				$num_nota_credito = $classConsultasSiesa->consultarUltimoDocumentoTercero($compania, $co_ventas, "NCE", $nit_tercero);
			} else {
				$num_nota_credito = "";
				
				//Crear log de error
				$dbComunicacionSiesa->insertar_comunicacion_siesa(6, "", "", $id_pago, "", $resultado_1, $id_usuario);
			}
					
			$resultado_2 = -1;
			if ($resultado_1 == 1) {
				//Array de datos para la creación del cruce contable
				$arr_datos = array(
					"Inicial" => array(
						"COMPANIA" => $compania
					),
					"Final" => array(
						"COMPANIA" => $compania
					),
					"Documentos" => array(
						"COMPANIA" => $compania,
						"FECHA_DOC" => $fecha_actual,
						"TERCERO" => $nit_tercero,
						"NOTAS" => "-"
					)
				);
				
				$arr_movimientos = array();
				$cont_aux = 1;
				foreach ($lista_productos_aux as $prod_aux) {
					if (($ind_tipo_pago == "1" && $prod_aux["valor_cuota"] > 0) || $prod_aux["valor"] > 0) {
						if ($ind_tipo_pago == "1") {
							$valor_debito = $prod_aux["valor_cuota"];
						} else {
							$valor_debito = $prod_aux["valor"] * $prod_aux["cantidad"];
						}
						
						$arr_aux = array(
							"COMPANIA" => $compania,
							"TERCERO" => $nit_tercero,
							"CENTRO_OPERACION" => $co_ventas,
							"UNIDAD_DE_NEGOCIO" => $prod_aux["cod_und_negocios"],
							"VLR_DEBITO" => $valor_debito,
							"NOTAS" => "",
							"NUM_DOC_CRUCE" => $num_nota_credito,
							"FECHA_VENCIMIENTO" => $fecha_actual,
							"FECHA_PRONTO_PAGO" => $fecha_actual,
							"VENDEDOR" => $usuario_pago_obj["numero_documento"]
						);
						$arr_movimientos[$cont_aux] = $arr_aux;
						$cont_aux++;
					}
				}
				$arr_datos["Movimientos"] = $arr_movimientos;
				
				$arr_caja = array();
				$arr_aux = array(
					"COMPANIA" => $compania,
					"CENTRO_OPERACION" => $co_ventas,
					"UNIDAD_NEGOCIO" => "U99",
					"VLR_CREDITO" => $valor_total,
					"NOTAS" => ""
				);
				$arr_caja[1] = $arr_aux;
				$arr_datos["Caja"] = $arr_caja;
				
				$resultado_aux = $classConectorSiesa->enviarXML(19, $arr_datos, $compania, 0);
				
				//Valida el resultado
				$resultado_2 = ($resultado_aux['ImportarDatosXMLResult'] == "Importacion exitosa") ? 1 : $resultado_aux['ImportarDatosXMLResult'];
				
				if ($resultado_2 != 1) {
					
					$dbComunicacionSiesa->insertar_comunicacion_siesa(19, "", "", $id_pago, "", $resultado_2, $id_usuario);
				}
			}
			
			//Lectura de XML factura alojado en el servidor de siesa para obtener el CUFE
			$cufe = $this->cargarXMLFactura($pago_obj["num_factura"], $compania);
			$fecha_facura_base = $this->convertirFechaSIESA($pago_obj["fecha_pago"]);
					
			//Enviar Entidad FE_NOTA_CREDITO
			$resultado_3 = -1;
			if($resultado_2 == 1){
				
				$arr_fe_nota_cred = array(
					"Inicial" => array(
						"COMPANIA" => $compania
					),
					"Final" => array(
						"COMPANIA" => $compania
					),
					
					"Entidades" => array(
						1 => array(
							"COMPANIA" => $compania,
							"CENTRO_OPERACION" => 100,
							"CONSECUTIVO" => $num_nota_credito,
							"CODIGO_ENTIDAD" => "EUNOECO022",
							"ATRIBUTO" => "co022_co_docto_base",
							"DATO_TEXTO" => 100,
							"FECHA_FACTURA_BASE" => "",
							"CODIGO_MAESTRO" => "",
							"DETALLE_MAESTRO" => ""	
						),
						2 => array(
							"COMPANIA" => $compania,
							"CENTRO_OPERACION" => 100,
							"CONSECUTIVO" => $num_nota_credito,
							"CODIGO_ENTIDAD" => "EUNOECO022",
							"ATRIBUTO" => "co022_tipo_docto_base",
							"DATO_TEXTO" => "FEC",
							"FECHA_FACTURA_BASE" => "",
							"CODIGO_MAESTRO" => "",
							"DETALLE_MAESTRO" => ""	
						),	
						3 => array(
							"COMPANIA" => $compania,
							"CENTRO_OPERACION" => 100,
							"CONSECUTIVO" => $num_nota_credito,
							"CODIGO_ENTIDAD" => "EUNOECO022",
							"ATRIBUTO" => "co022_docto_base",
							"DATO_TEXTO" => $pago_obj["num_factura"],
							"FECHA_FACTURA_BASE" => "",
							"CODIGO_MAESTRO" => "",
							"DETALLE_MAESTRO" => ""	
						),
						4 => array(
							"COMPANIA" => $compania,
							"CENTRO_OPERACION" => 100,
							"CONSECUTIVO" => $num_nota_credito,
							"CODIGO_ENTIDAD" => "EUNOECO022",
							"ATRIBUTO" => "co022_tipo_fact",
							"DATO_TEXTO" => "",
							"FECHA_FACTURA_BASE" => "",
							"CODIGO_MAESTRO" => "MUNOECO027",
							"DETALLE_MAESTRO" => "01"	
						),	
						5 => array(
							"COMPANIA" => $compania,
							"CENTRO_OPERACION" => 100,
							"CONSECUTIVO" => $num_nota_credito,
							"CODIGO_ENTIDAD" => "EUNOECO022",
							"ATRIBUTO" => "co022_uuid_docto_base",
							"DATO_TEXTO" =>$cufe,
							"FECHA_FACTURA_BASE" => "",
							"CODIGO_MAESTRO" => "",
							"DETALLE_MAESTRO" => ""	
						),
						6 => array(
							"COMPANIA" => $compania,
							"CENTRO_OPERACION" => 100,
							"CONSECUTIVO" => $num_nota_credito,
							"CODIGO_ENTIDAD" => "EUNOECO022",
							"ATRIBUTO" => "co022_fecha_docto_base",
							"DATO_TEXTO" => "",
							"FECHA_FACTURA_BASE" => $fecha_facura_base,
							"CODIGO_MAESTRO" => "",
							"DETALLE_MAESTRO" => ""	
						),	
						
						7 => array(
							"COMPANIA" => $compania,
							"CENTRO_OPERACION" => 100,
							"CONSECUTIVO" => $num_nota_credito,
							"CODIGO_ENTIDAD" => "EUNOECO022",
							"ATRIBUTO" => "co022_ind_sin_fe_ref",
							"DATO_TEXTO" => "SI",
							"FECHA_FACTURA_BASE" => "",
							"CODIGO_MAESTRO" => "",
							"DETALLE_MAESTRO" => ""	
						),	
						
						8 => array(
							"COMPANIA" => $compania,
							"CENTRO_OPERACION" => 100,
							"CONSECUTIVO" => $num_nota_credito,
							"CODIGO_ENTIDAD" => "EUNOECO015",
							"ATRIBUTO" => "co015_concepto_nc",
							"DATO_TEXTO" => "",
							"FECHA_FACTURA_BASE" => "",
							"CODIGO_MAESTRO" => "MUNOECO017",
							"DETALLE_MAESTRO" => "2"	
						),	
									
					)
				);
				
				$resultado_aux = $classConectorSiesa->enviarXML(20, $arr_fe_nota_cred, $compania, 0);
				$resultado_3 = ($resultado_aux['ImportarDatosXMLResult'] == "Importacion exitosa") ? 1 : $resultado_aux['ImportarDatosXMLResult'];
				
				if($resultado_3 != 1){
					$dbComunicacionSiesa->insertar_comunicacion_siesa(20, "", "", $id_pago, "", $resultado_3, $id_usuario);
				}
				
			}
			
			$resultado_aux = -1;
			if ($resultado_1 == 1 && $resultado_2 == 1 && $resultado_3 == 1 && $cufe <> "") {
				$resultado_aux = 1;
			} else {
				$resultado_aux = "";
				if ($resultado_2 != 1) {
					$resultado_aux = $resultado_2;
				}
				if($resultado_3 != 1){
					$resultado_aux = $resultado_3;	
				}
				if($cufe == "" && $resultado_3 == 1){
					$resultado_aux .= "<br/><b>La nota cr&eacute;dito se ha creado sin el n&uacute;mero CUFE, por favor realice la asignaci&oacute;n manualmente.</b>";
				}
				
				if ($resultado_1 == 1 && ($resultado_2 <> 1 || $resultado_3 <> 1)) {
					$resultado_aux .= "<br /><b>Se gener&oacute; la nota cr&eacute;dito n&uacute;mero ".$num_nota_credito.", por favor realice la anulaci&oacute;n manual de la misma.</b>";
				} else {
					$resultado_aux = $resultado_1."<br/>".$resultado_aux;
				}
			}
			
			$resultado = array();
			$resultado[0] = $resultado_aux;
			$resultado[1] = $num_nota_credito;
			
			return $resultado;
		}
		
		public function cargarXMLFactura($num_factura, $compania){
			$dbVariables = new Dbvariables();
			$obj_variable = $dbVariables->getVariable(25);
			$url = $obj_variable["valor_variable"];
			
			switch($compania){
				case "2": //VG & AT
					$url = $url."UT/XML/";
				break;
				case "3": //SAN GIL
					$url = $url."COS/XML/";
				break;	
			}
			
			$nombre_archivo = $compania."100FEC".$num_factura.".xml";
			$archivo = $url.$nombre_archivo;
			$facturaXML = simplexml_load_file($archivo);
			
			if(is_object($facturaXML)){
				$namespaces = $facturaXML->getNameSpaces(true);
				$cbc = $facturaXML->children($namespaces['cbc']);
				$cufe = $cbc->UUID;
			}else{
				$cufe = "";
			}
		
			return $cufe;
		
		}
		
		public function convertirFechaSIESA($fecha){
			
			if($fecha <> ""){
				$fecha = substr($fecha, 0, -9); 
				$fecha = str_replace("-", "", $fecha);
			}else{
				$fecha = "";
			}
			return $fecha;
			
		}		
	}
?>
