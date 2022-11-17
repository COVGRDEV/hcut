<?php
	/**
	 * Description of Class_Pedidos_Siesa
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
	require_once("../db/DbConvenios.php");
	require_once("../db/DbPlanes.php");
	require_once("../db/DbPagos.php");
	require_once("../db/DbAdmision.php");
	require_once("../db/DbDatosEntidad.php");
	require_once("../db/DbComunicacionSiesa.php");
	
	class Class_Pedidos_Siesa {
		
		public function crearPedido($compania, $co_ventas, $bodega_ventas, $num_doc_usuario, $id_pago, $id_convenio, $id_plan, $id_paciente, $condicion_pago, $lista_productos, $id_usuario) {
			$classConectorSiesa = new Class_Conector_Siesa();
			$classConsultasSiesa = new Class_Consultas_Siesa();
			$funcionesPersona = new FuncionesPersona();
			$dbPacientes = new DbPacientes();
			$dbListas = new DbListas();
			$dbMaestroProcedimientos = new DbMaestroProcedimientos();
			$dbMaestroInsumos = new DbMaestroInsumos();
			$dbTiposPago = new DbTiposPago();
			$dbConvenios = new DbConvenios();
			$dbPlanes = new DbPlanes();
			$dbDatosEntidad = new DbDatosEntidad();
			$dbComunicacionSiesa = new DbComunicacionSiesa();
			
			$resultado = -1;
			/*Este id se usa como identificador interno en SIESA debido que, al hacer la integración de las dos historias clínicas, 
			 como aún se manejan 2 bases de datos diferentes, el id del pago puede cruzarse, 
			 con la base de datos de HCEO y habrán conflictos al momento de consultar el pago en SIESA. */
			$id_pago_siesa = $id_pago."-2";
			
			//Se halla el NIT de la entidad
			$entidad_obj = $dbDatosEntidad->getDatosEntidadId($compania);
			$nit_entidad = $entidad_obj["numero_documento"];
			$pos_aux = strpos($nit_entidad, "-");
			if ($pos_aux != false) {
				$nit_entidad = substr($nit_entidad, 0, $pos_aux);
			}
			
			//Se halla el NIT del tercero entidad
			$convenio_obj = $dbConvenios->getConvenio($id_convenio);
			$plan_obj = $dbPlanes->getPlan($id_plan);
			$nit_tercero = $convenio_obj["numero_documento"];
			
			//Se obtienen los datos del paciente
			$paciente_obj = $dbPacientes->getExistepaciente3($id_paciente);
			$num_doc_paciente = $paciente_obj["numero_documento"];
			$nombre_completo = $funcionesPersona->obtenerNombreCompleto($paciente_obj["nombre_1"], $paciente_obj["nombre_2"], $paciente_obj["apellido_1"], $paciente_obj["apellido_2"]);
			
			//Se obtiene la fecha actual
			$fecha_actual = $classConectorSiesa->getFechaActual();
			
			//Array de datos para creación del pedido
			$arr_datos = array(
				"Inicial" => array(
					"COMPANIA" => $compania
				),
				"Final" => array(
					"COMPANIA" => $compania
				),
				"Pedidos" => array(
					"COMPANIA" => $compania,
					"CO_FACTURA" => $co_ventas,
					"FECHA_DEL_DOCUMENTO" => $fecha_actual,
					"FECHA_ENTREGA_PEDIDO" => $fecha_actual,
					"TERCERO_DESPACHO" => $nit_tercero,
					"TERCERO_FACTURAR" => $nit_tercero,
					"CEDULA_VENDEDOR" => $nit_entidad,
					"CONVENIO" => $id_convenio,
					"PLAN" => $id_plan,
					"CONDICION_DE_PAGO" => $condicion_pago,
					"OBSERVACIONES" => $id_pago_siesa
				)
			);
			
			//Se crean los arrays de movimientos y descuentos
			$arr_movimientos = array();
			$arr_descuentos = array();
			$cont_aux = 1;
			foreach ($lista_productos as $prod_aux) {
				if ($prod_aux["valor"] > 0) {
					switch ($prod_aux["tipo_precio"]) {
						case "P":
							$prod_obj = $dbMaestroProcedimientos->getProcedimiento($prod_aux["cod_servicio"]);
							$cod_und_negocios = $prod_obj["cod_und_negocios"];
							break;
						case "I":
							$prod_obj = $dbMaestroInsumos->getInsumo($prod_aux["cod_servicio"]);
							$cod_und_negocios = $prod_obj["cod_und_negocios"];
							break;
						default:
							$cod_und_negocios = "";
							break;
					}
					$arr_aux = array(
						"COMPANIA" => $compania,
						"CO_MOVIMIENTO" => $co_ventas,
						"NUM_REGISTRO" => $cont_aux,
						"BODEGA" => $bodega_ventas,
						"LISTA_DE_PRECIO" => ($prod_aux["tipo_bilateral"] == "0" ? "3" : $prod_aux["tipo_bilateral"]),
						"UNIDAD_MEDIDA" => "UND",
						"CANTIDAD" => $prod_aux["cantidad"],
						"PRECIO_UNITARIO" => $prod_aux["valor"],
						"REFERENCIA" => $prod_aux["cod_servicio"],
						"UNIDAD_NEGOCIO_MOVTO" => $cod_und_negocios,
						"NOMBRE_PACIENTE" => $nombre_completo,
						"CEDULA_PACIENTE" => $num_doc_paciente,
						"FECHA_ENTREGA_PEDIDO" => $fecha_actual,
						"CEDULA_VENDEDOR" => $num_doc_usuario
					);
					
					$bol_agregar_mov = true;
					if ($prod_aux["valor_cuota"] > 0 && $plan_obj["ind_desc_cc"] == "1") {
						$valor_aux = $prod_aux["valor"] * $prod_aux["cantidad"];
						if ($valor_aux > $prod_aux["valor_cuota"]) {
							$arr_aux2 = array(
								"COMPANIA" => $compania,
								"NUM_REGISTRO" => $cont_aux,
								"COPAGO" => $prod_aux["valor_cuota"]
							);
							array_push($arr_descuentos, $arr_aux2);
						} else {
							$bol_agregar_mov = false;
						}
					}
					
					if ($bol_agregar_mov) {
						array_push($arr_movimientos, $arr_aux);
						
						$cont_aux++;
					}
				}
			}
			
			if (count($arr_movimientos) > 0) {
				$arr_datos["Movimientos"] = $arr_movimientos;
				$arr_datos["Descuentos"] = $arr_descuentos;
				
				//var_dump($arr_datos);
				$resultado_aux = $classConectorSiesa->enviarXML(4, $arr_datos, $compania, 0);
								
				//Valida el resultado
				$resultado = ($resultado_aux['ImportarDatosXMLResult'] == "Importacion exitosa") ? 1 : $resultado_aux['ImportarDatosXMLResult'];
				
				if ($resultado != 1) {
					//Se crea el log del error
					$dbComunicacionSiesa->insertar_comunicacion_siesa(4, $id_paciente, "", $id_pago, "", $resultado, $id_usuario);
				}
			} else {
				$resultado = 2;
			}
			
			return $resultado;
		}
				
		
	public function crearEntidadPedidoDocumento($compania, $cod_prestador, $co_ventas, $bodega_ventas, $numero_documento, $id_pago, $id_convenio, $id_plan, $id_paciente, $id_usuario,$num_pedido){
			$classConectorSiesa = new Class_Conector_Siesa();
			$classConsultasSiesa = new Class_Consultas_Siesa();
			$dbPagos = new DbPagos();
			$dbAdmision = new DbAdmision();
			$dbComunicacionSiesa = new DbComunicacionSiesa();
			$db_convenios = new DbConvenios();
			$db_pacientes = new DbPacientes();
			$db_planes = new DbPlanes();
			$convenio_obj = $db_convenios->getConvenio($id_convenio);
			$plan_obj = $db_planes->getPlanDetalle($id_plan);	
						
			$paciente_obj = $db_pacientes->getExistepaciente3($id_paciente);
			$lista_pedidos_detalle = $classConsultasSiesa->consultarPedidosDetalle($compania, $num_pedido);
			$pago_obj = $dbPagos->get_pago_id($id_pago);
			
			$fecha_ini="";
			$fecha_fin="";
			
			$fecha_ini = strlen($convenio_obj["fact_inicial"])==1 ? "0".$convenio_obj["fact_inicial"] : $convenio_obj["fact_inicial"];
			$fecha_fin = strlen($convenio_obj["fact_final"])==1 ? "0".$convenio_obj["fact_final"] : $convenio_obj["fact_final"];
								
			$fecha_ini = substr(date("Ymd H:i:s"), 0, 6).$fecha_ini;
			$fecha_fin = substr(date("Ymd H:i:s"), 0, 6).$fecha_fin;	
			 
			$arr_datos = array(
							"Inicial" => array(
								"COMPANIA" => $compania
							),
							"Final" => array(
								"COMPANIA" => $compania
							),
							"Entidad" => array(
								1 => array(
									"COMPANIA" => $compania,
									"CO" => $co_ventas,
									"TIPO_DOCTO" => "PVV",
									"CONSECUTIVO_PVV" => $num_pedido,
									"CLASE_DOCTO" => 502,
									"ROW_ID_MOVTO" => 0,
									"GRUPO_ENTIDAD" => "PEDIDODOC",
									"ENTIDAD" => "PEDIDOFE",
									"COD_ATRIBUTO" => 1,
									"CONTENIDO" => $convenio_obj["contratacion_nombre"],
									"DATO_FECHA_HORA" => "",
									"TIPO_ENTIDAD" => "G502_1"
									
								),
								2 => array(
									"COMPANIA" => $compania,
									"CO" => $co_ventas,
									"TIPO_DOCTO" => "PVV",
									"CONSECUTIVO_PVV" => $num_pedido,
									"CLASE_DOCTO" => 502,
									"ROW_ID_MOVTO" => 0,
									"GRUPO_ENTIDAD" => "PEDIDODOC",
									"ENTIDAD" => "PEDIDOFE",
									"COD_ATRIBUTO" => 2,
									"CONTENIDO" => $plan_obj["cobertura_nombre"],
									"DATO_FECHA_HORA" => "",
									"TIPO_ENTIDAD" => "G502_1"
								),
								3 => array(
									"COMPANIA" => $compania,
									"CO" => $co_ventas,
									"TIPO_DOCTO" => "PVV",
									"CONSECUTIVO_PVV" => $num_pedido,
									"CLASE_DOCTO" => 502,
									"ROW_ID_MOVTO" => 0,
									"GRUPO_ENTIDAD" => "PEDIDODOC",
									"ENTIDAD" => "PEDIDOFE",
									"COD_ATRIBUTO" => 3,
									"CONTENIDO" => $convenio_obj["num_contrato"],
									"DATO_FECHA_HORA" => "",
									"TIPO_ENTIDAD" => "G502_1"
								),
								4 => array(
									"COMPANIA" => $compania,
									"CO" => $co_ventas,
									"TIPO_DOCTO" => "PVV",
									"CONSECUTIVO_PVV" => $num_pedido,
									"CLASE_DOCTO" => 502,
									"ROW_ID_MOVTO" => 0,
									"GRUPO_ENTIDAD" => "PEDIDODOC",
									"ENTIDAD" => "PEDIDOFE",
									"COD_ATRIBUTO" => 4,
									"CONTENIDO" => $paciente_obj["tipo_cotizante"],
									"DATO_FECHA_HORA" => "",
									"TIPO_ENTIDAD" => "G502_1"
								),
								5 => array(
									"COMPANIA" => $compania,
									"CO" => $co_ventas,
									"TIPO_DOCTO" => "PVV",
									"CONSECUTIVO_PVV" => $num_pedido,
									"CLASE_DOCTO" => 502,
									"ROW_ID_MOVTO" => 0,
									"GRUPO_ENTIDAD" => "PEDIDODOC",
									"ENTIDAD" => "PEDIDOFE",
									"COD_ATRIBUTO" => 5,
									"CONTENIDO" => $pago_obj["num_poliza"],
									"DATO_FECHA_HORA" => "",
									"TIPO_ENTIDAD" => "G502_1"
								),
								6 => array(
									"COMPANIA" => $compania,
									"CO" => $co_ventas,
									"TIPO_DOCTO" => "PVV",
									"CONSECUTIVO_PVV" => $num_pedido,
									"CLASE_DOCTO" => 502,
									"ROW_ID_MOVTO" => 0,
									"GRUPO_ENTIDAD" => "PEDIDODOC",
									"ENTIDAD" => "PEDIDOFE",
									"COD_ATRIBUTO" => 6,
									"CONTENIDO" => "",
									"DATO_FECHA_HORA" => $fecha_ini,
									"TIPO_ENTIDAD" => "G502_1"
									
								),
								7 => array(
									"COMPANIA" => $compania,
									"CO" => $co_ventas,
									"TIPO_DOCTO" => "PVV",
									"CONSECUTIVO_PVV" => $num_pedido,
									"CLASE_DOCTO" => 502,
									"ROW_ID_MOVTO" => 0,
									"GRUPO_ENTIDAD" => "PEDIDODOC",
									"ENTIDAD" => "PEDIDOFE",
									"COD_ATRIBUTO" => 7,
									"CONTENIDO" => "",
									"DATO_FECHA_HORA" => $fecha_fin,
									"TIPO_ENTIDAD" => "G502_1"
								),
								8 => array(
									"COMPANIA" => $compania,
									"CO" => $co_ventas,
									"TIPO_DOCTO" => "PVV",
									"CONSECUTIVO_PVV" => $num_pedido,
									"CLASE_DOCTO" => 502,
									"ROW_ID_MOVTO" => 0,
									"GRUPO_ENTIDAD" => "PEDIDODOC",
									"ENTIDAD" => "PEDIDOFE",
									"COD_ATRIBUTO" => 8,
									"CONTENIDO" => $cod_prestador,
									"DATO_FECHA_HORA" => "",
									"TIPO_ENTIDAD" => "G502_1"
								),
								
							)
						);
				
				$resultado_aux = $classConectorSiesa->enviarXML(15, $arr_datos, $compania, 0);
				
				$resultado = ($resultado_aux['ImportarDatosXMLResult'] == "Importacion exitosa") ? 1 : $resultado_aux['ImportarDatosXMLResult'];
				
				if ($resultado <> 1) {
					$resultado = "En Pedido, ha fallado al intentar enviar la entidad del documento ".$resultado;
					$dbComunicacionSiesa->insertar_comunicacion_siesa(15, $id_paciente, "", $id_pago, "", $resultado, $id_usuario);
				}
				return $resultado;
				
			}
			
	public function crearEntidadPedido($compania, $co_ventas, $bodega_ventas, $id_pago, $id_usuario,$num_pedido){
				
			$classConectorSiesa = new Class_Conector_Siesa();
			$classConsultasSiesa = new Class_Consultas_Siesa();
			$dbPagos = new DbPagos();
			$dbAdmision = new DbAdmision();
			$dbComunicacionSiesa = new DbComunicacionSiesa();
			//Se obtienen los valores de entidades asociadas al pedido (autorización y carnet)
			$pago_obj = $dbPagos->get_pago_id($id_pago);
			$admision_obj = $dbAdmision->get_admision($pago_obj["id_admision"]);
			if (!isset($admision_obj["num_carnet"])) {
				//Se busca el número de carnet en la ultima admisión que tuviera el paciente con el mismo convenio
				$admision_obj = $dbAdmision->get_ultima_admision_convenio($pago_obj["id_paciente"], $pago_obj["id_convenio"]);
			}
			if (isset($admision_obj["num_carnet"])) {
				$num_carnet = $admision_obj["num_carnet"];
			} else {
				$num_carnet = "";
			}
			
			$lista_pagos_detalle = $dbPagos->get_lista_pagos_detalle($id_pago);
			$mapa_autorizaciones = array();
			foreach ($lista_pagos_detalle as $detalle_aux) {
				$cod_servicio = "";
				switch ($detalle_aux["tipo_precio"]) {
					case "P":
						$cod_servicio = $detalle_aux["cod_procedimiento"];
						break;
					case "M":
						$cod_servicio = $detalle_aux["cod_medicamento"];
						break;
					case "I":
						$cod_servicio = $detalle_aux["cod_insumo"];
						break;
				}
				
				if ($cod_servicio != "") {
					$mapa_autorizaciones[$cod_servicio] = $detalle_aux["num_autorizacion"];
				}
			}
			
			//Se obtiene el listado de items del pedido
			$lista_pedidos_detalle = $classConsultasSiesa->consultarPedidosDetalle($compania, $num_pedido);
			
			$bol_entidades = ($num_carnet != "");
			$arr_entidades = array();
			foreach ($lista_pedidos_detalle as $pedido_det_aux) {
				$num_autorizacion = $mapa_autorizaciones[trim($pedido_det_aux["REFERENCIA"])];
				if ($num_autorizacion != "") {
					$bol_entidades = true;
										
					$arr_aux = array(
						"COMPANIA" => $compania,
						"CO" => $co_ventas,
						"TIPO_DOCTO" => "PVV",
						"CONSECUTIVO_PVV" => $num_pedido,
						"CLASE_DOCTO" => 502,
						"ROW_ID_MOVTO" => $pedido_det_aux["ROWID_MOVTO"],
						"GRUPO_ENTIDAD" => "ANEXO PEDIDO",
						"ENTIDAD" => "ANEXO PEDIDO MOV",
						"COD_ATRIBUTO" => 1,
						"CONTENIDO" => substr($num_autorizacion, 0, 30),
						"DATO_FECHA_HORA" => "",
						"TIPO_ENTIDAD" => "G502_2"
						
					);
					
					array_push($arr_entidades, $arr_aux);
				}
				
				if ($num_carnet != "") {
					$arr_aux = array(
						"COMPANIA" => $compania,
						"CO" => $co_ventas,
						"TIPO_DOCTO" => "PVV",
						"CONSECUTIVO_PVV" => $num_pedido,
						"CLASE_DOCTO" => 502,
						"ROW_ID_MOVTO" => $pedido_det_aux["ROWID_MOVTO"],
						"GRUPO_ENTIDAD" => "ANEXO PEDIDO",
						"ENTIDAD" => "ANEXO PEDIDO MOV",
						"COD_ATRIBUTO" => 2,
						"CONTENIDO" => substr($num_carnet, 0, 20),
						"DATO_FECHA_HORA" => "",
						"TIPO_ENTIDAD" => "G502_2"
					);
					
					array_push($arr_entidades, $arr_aux);
				}
				
			   $arr_aux = array(
					"COMPANIA" => $compania,
					"CO" => $co_ventas,
					"TIPO_DOCTO" => "PVV",
					"CONSECUTIVO_PVV" => $num_pedido,
					"CLASE_DOCTO" => 502,
					"ROW_ID_MOVTO" => $pedido_det_aux["ROWID_MOVTO"],
					"GRUPO_ENTIDAD" => "ANEXO PEDIDO",
					"ENTIDAD" => "ANEXO PEDIDO MOV",
					"COD_ATRIBUTO" => 3,
					"CONTENIDO" => $pago_obj["num_mipress"],
					"DATO_FECHA_HORA" => "",
					"TIPO_ENTIDAD" => "G502_2"
				);
					
				array_push($arr_entidades, $arr_aux);
					
			   $arr_aux = array(
					"COMPANIA" => $compania,
					"CO" => $co_ventas,
					"TIPO_DOCTO" => "PVV",
					"CONSECUTIVO_PVV" => $num_pedido,
					"CLASE_DOCTO" => 502,
					"ROW_ID_MOVTO" => $pedido_det_aux["ROWID_MOVTO"],
					"GRUPO_ENTIDAD" => "ANEXO PEDIDO",
					"ENTIDAD" => "ANEXO PEDIDO MOV",
					"COD_ATRIBUTO" => 4,
					"CONTENIDO" => $pago_obj["num_ent_mipress"],
					"DATO_FECHA_HORA" => "",
					"TIPO_ENTIDAD" => "G502_2"
				);
				
				array_push($arr_entidades, $arr_aux);
			}
			
			/*if ($bol_entidades) {*/
				//Array de datos para las entidades
				$arr_datos = array(
					"Inicial" => array(
						"COMPANIA" => $compania
					),
					"Final" => array(
						"COMPANIA" => $compania
					),
					"Entidad" => $arr_entidades
				);
				
				
				$resultado_aux = $classConectorSiesa->enviarXML(15, $arr_datos, $compania,0);
				
				//Valida el resultado
				$resultado = ($resultado_aux['ImportarDatosXMLResult'] == "Importacion exitosa") ? 1 : $resultado_aux['ImportarDatosXMLResult'];
				
				if($resultado != 1){
					$resultado =  "En pedido, ha fallado al intentar enviar la entidad del movimiento ".$resultado;
					$dbComunicacionSiesa->insertar_comunicacion_siesa(15, $pago_obj["id_paciente"], "", $id_pago, "", $resultado, $id_usuario);
				}
				
				return $resultado;
		
			}
	}
?>
