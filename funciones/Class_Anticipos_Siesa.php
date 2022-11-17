<?php
	/**
	 * Description of Class_Anticipos_Siesa
	 *
	 * @author Feisar Moreno
	 */
	require_once("../funciones/Class_Conector_Siesa.php");
	require_once("../funciones/Class_Consultas_Siesa.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../db/DbPacientes.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbTiposPago.php");
	require_once("../db/DbComunicacionSiesa.php");
	
	class Class_Anticipos_Siesa {
		
		public function crearAnticipo($compania, $co_ventas, $bodega_ventas, $nit_tercero, $num_doc_usuario, $id_paciente, $observaciones_anticipo, $lista_medios_pago, $id_usuario, $id_usuario_siesa) {
			$classConectorSiesa = new Class_Conector_Siesa();
			$classConsultasSiesa = new Class_Consultas_Siesa();
			$funcionesPersona = new FuncionesPersona();
			$dbPacientes = new DbPacientes();
			$dbListas = new DbListas();
			$dbTiposPago = new DbTiposPago();
			$dbComunicacionSiesa = new DbComunicacionSiesa();
			
			$resultado = -1;
			
			//Se obtienen los datos del paciente
			$paciente_obj = $dbPacientes->getExistepaciente3($id_paciente);
			$nombre_completo = $funcionesPersona->obtenerNombreCompleto($paciente_obj["nombre_1"], $paciente_obj["nombre_2"], $paciente_obj["apellido_1"], $paciente_obj["apellido_2"]);
			
			$fecha_actual = $classConectorSiesa->getFechaActual();
			
			//Se halla el valor total sumando los valores de los medios de pago
			$valor_total = 0;
			foreach ($lista_medios_pago as $medio_aux) {
				$valor_total += $medio_aux["valor_pago"];
			}
			
			//Array de datos para creación de anticipos
			$arr_datos = array(
				"Inicial" => array(
					"COMPANIA" => $compania
				),
				"Final" => array(
					"COMPANIA" => $compania
				),
				"Recibo" => array(
					"COMPANIA" => $compania,
					"CO" => $co_ventas,
					"CONSECUTIVO_RCA" => "1",
					"FECHA_RCA" => $fecha_actual,
					"FECHA_RECAUDO_RCA" => $fecha_actual,
					"TERCERO_RCA" => $nit_tercero,
					"VALOR_INGRESO" => $valor_total,
					"VALOR_APLICAR" => $valor_total,
					"ID_COBRADOR" => $id_usuario_siesa,
					"ID_FLUJO_EFECTIVO" => "1101",
					"NOTAS_RCA" => $observaciones_anticipo,
					"AUXILIAR_OTROS_INGRESOS" => "2502080601",
					"TERCERO_OTROS_INGRESOS" => $nit_tercero,
					"SUCURSAL_OTROS_INGRESOS" => "001",
					"F351_ID_CO_OTRO_ING" => $co_ventas,
					"UN_OTROS_INGRESOS" => "U99"
				)
			);
			
			//Se crea el array de caja
			$arr_caja = array();
			$cont_aux = 1;
			foreach ($lista_medios_pago as $medio_aux) {
				if ($medio_aux["tipo_pago"] != "0") { //No se incluye el tipo de pago Boleta
					switch ($medio_aux["tipo_pago"]) {
						case "50": //Consignación nacional
							$medio_obj = $dbListas->getDetalle($medio_aux["banco_pago"]);
							$cod_medio_pago = $medio_obj["text_adicional_detalle"];
							break;
							
						case "60": //Trajeta de crédito
							$medio_obj = $dbListas->getDetalle($medio_aux["id_franquicia_tc"]);
							$cod_medio_pago = $medio_obj["text_adicional_detalle"];
							break;
							
						default:
							$medio_obj = $dbTiposPago->getTipoPago($medio_aux["tipo_pago"]);
							$cod_medio_pago = $medio_obj["cod_integ_siesa"];
							break;
					}
					
					if ($medio_aux["banco_pago"] != "") {
						$banco_obj = $dbListas->getDetalle($medio_aux["banco_pago"]);
						$codigo_banco = $banco_obj["codigo_detalle"];
					} else {
						$codigo_banco = "";
					}
					
					if ($medio_aux["ano_vence"] != "" && $medio_aux["mes_vence"] != "") {
						$fecha_vence = $medio_aux["ano_vence"]."/".$medio_aux["mes_vence"];
					} else {
						$fecha_vence = "";
					}
					
					if ($medio_aux["fecha_consigna"] != "") {
						$fecha_consigna = $classConectorSiesa->formatearFechaDMA($medio_aux["fecha_consigna"]);
					} else {
						$fecha_consigna = "";
					}
					
					$arr_aux = array(
						"COMPANIA" => $compania,
						"CO" => $co_ventas,
						"CONSECUTIVO_RCA" => "1",
						"MEDIO_PAGO" => $cod_medio_pago,
						"VALOR" => $medio_aux["valor_pago"],
						"ID_BANCO" => $codigo_banco,
						"NRO_CHEQUE" => ($medio_aux["num_cheque"] != "" ? $medio_aux["num_cheque"] : "0"),
						"NRO_CUENTA" => $medio_aux["num_cuenta"],
						"COD_SEGURIDAD" => $medio_aux["cod_seguridad"],
						"NRO_AUTORIZACION" => $medio_aux["num_autoriza"],
						"FECHA_VCTO" => $fecha_vence,
						"REFERENCIA_OTROS" => $medio_aux["referencia"],
						"FECHA_CONSIGNACION" => $fecha_consigna,
						"CAUSALES_DEV" => "",
						"TERCERO_DEV" => "",
						"NOTAS_CAJA" => "",
						"DOCTO_BANCO_CG" => ($fecha_consigna == "" ? "" : "CG")
					);
					$arr_caja[$cont_aux] = $arr_aux;
					$cont_aux++;
				}
			}
			$arr_datos["Caja"] = $arr_caja;
			
			//var_dump($arr_datos);
			$resultado_aux = $classConectorSiesa->enviarXML(17, $arr_datos, $compania, 0);
			
			//Valida el resultado
			$resultado = ($resultado_aux['ImportarDatosXMLResult'] == "Importacion exitosa") ? 1 : $resultado_aux['ImportarDatosXMLResult'];
			if($resultado != 1){
				$dbComunicacionSiesa->insertar_comunicacion_siesa(17, $id_paciente, "", "", "", $resultado, $id_usuario);
			}
			return $resultado;
		}
		
	}
?>
