<?php
	require_once("../db/DbServiciosIntegracion.php");
	require_once("../db/DbVariables.php");
	require_once("../funciones/nusoap.php");
	
	/**
	 * Description of Class_Conector_Siesa
	 *
	 * @author Sistemas2
	 */
	class Class_Conector_Siesa {
	
		public $dbServiciosIntegracion;
	
		function __construct() {
			$this->dbServiciosIntegracion = new DbServiciosIntegracion();
		}
	
		public function enviarXML($codigoServicio, $array, $idCompania, $testClase = 0) {
			$resultado = $this->importarXML($array, $idCompania, $codigoServicio, $testClase);
			return $resultado;
		}
		
		public function pedidos($array, $idCompania, $testClase = 0) {
			$codigoServicio = 4;
			$resultado = $this->importarXML($array, $idCompania, $codigoServicio, $testClase);
			return $resultado;
		}
	
		public function salidasPorDispensacion($array, $idCompania, $testClase = 0) {//Despacho de medicamentos
			$codigoServicio = 8;
			$resultado = $this->importarXML($array, $idCompania, $codigoServicio, $testClase);
			return $resultado;
		}
		
		public function tercerosClientes($array, $idCompania, $testClase = 0) {//
			$codigoServicio = 14;
			$resultado = $this->importarXML($array, $idCompania, $codigoServicio, $testClase);
			return $resultado;
		}
		
		public function tercerosVigara($array, $idCompania, $testClase = 0) {//
			$codigoServicio = 13;
			$resultado = $this->importarXML($array, $idCompania, $codigoServicio, $testClase);
			return $resultado;
		}
	
		/* Conector para pedidos a SIESA */
		/* ----------------------------------------- */
		private function importarXML($array, $idCompania, $codigoServicio, $testClase) {
			$resultado = -1;
			$servicioIntegracion = $this->dbServiciosIntegracion->getServicioIntegracion($codigoServicio); //Id del conector
			if (count($servicioIntegracion) > 0) {//Si el conector con el ID existe en la base de datos
				$xml = $this->armarXML($servicioIntegracion['id_si'], $array, 1);
	
				if ($testClase == 0) {//Con valor 1 es para probar y ver la estructura xml que será enviada
					$url = $servicioIntegracion['url_wsdl'];
					try {
						
						$client = new nusoap_client($url, 'wsdl');
						$client->soap_defencoding = 'UTF-8';
						$client->decode_utf8 = FALSE;
	
						$array = explode(";", $servicioIntegracion['parametros_sim']);
						$array['idCompania'] = $servicioIntegracion['idCompania_si'];//Siempre debe ser 2 - lo que trae por base de datos
						$array['idDocumento'] = $servicioIntegracion['idDocumento_si'];
						$array['Path'] = $servicioIntegracion['path_wsdl'];
						$array['strClave'] = $servicioIntegracion['strClave_si'];
						$array['strUsuario'] = $servicioIntegracion['strUsuario_si'];
						$array['strCompania'] = $idCompania;//Id de la compañia, debe ser variable en conjunto con el xml
						$array['strFuenteDatos'] = $xml;
						$array['strNombreDocumento'] = $servicioIntegracion['strNombreDocumento_si'];
						
						$resultado = $client->call($servicioIntegracion['nom_sim'], $array);     
												
					} catch (Exception $ex) {
						$resultado = $ex->getMessage();
					}
				} else {
					echo htmlentities($xml);
				}
			}
			return $resultado;
		}
	
		private function armarXML($idSi, $array, $tipoXML = 1) {
			$domDoc = new DOMDocument;
	
			if ($tipoXML == 1) {//Define si asigna la etiqueta llamda: "Conector"
				$conectorElt = $domDoc->createElement('Conector');
				$conectorNode = $domDoc->appendChild($conectorElt);
			}
	
			$componentes = $this->dbServiciosIntegracion->getServicioIntegracionComponentes($idSi);
			
	
			foreach ($componentes as $componente) {
				$seccion = $componente['seccion_sic'];
				$idSeccion = $componente['id_sic'];
	
				$dbComponentesDet = $this->dbServiciosIntegracion->getServicioIntegracionComponentesDet($idSeccion);
	
				switch ($seccion) {
					case "Descuentos":
					case "Movimientos":
					case "Clientes":
					case "CxC":
					case "CuotasCxC":
					case "Caja":
					case "Entidad":
					case "Terceros":
					case "Entidades":
						if (array_key_exists($seccion, $array)) {//Valida secciones
							$cantMovimientos = $array[$seccion];
							foreach ($array[$seccion] as $valor_aux) {
								$componente = $conectorNode->appendChild($domDoc->createElement($seccion));
								foreach ($dbComponentesDet as $componenteDet) {
									$seccionDet = $componenteDet['campo_sid'];
	
									$elementoComponenteDet = $domDoc->createElement($seccionDet);
	
									if (array_key_exists($seccionDet, $valor_aux)) {//Valida secciones detalle                       
										$valorElementoComponenteDet = $domDoc->createTextNode($valor_aux[$seccionDet]);
										$elementoComponenteDet->appendChild($valorElementoComponenteDet);
									}
									$componente->appendChild($elementoComponenteDet);
								}
							}
						}
						break;
						
					default:
						if ($tipoXML == 1) {//Define si asigna la etiqueta llamda: "Conector"
							$componente = $conectorNode->appendChild($domDoc->createElement($seccion));
						} else if ($tipoXML == 2) {
							$conectorElt = $domDoc->createElement($seccion);
							
							$componente = $domDoc->appendChild($conectorElt);
							
						}
	
						foreach ($dbComponentesDet as $componenteDet) {
							$seccionDet = $componenteDet['campo_sid'];
							$elementoComponenteDet = $domDoc->createElement($seccionDet);
	
							if ($tipoXML == 1) {//1= XML de envio
								if (array_key_exists($seccion, $array)) {//Valida secciones
									if (array_key_exists($seccionDet, $array[$seccion])) {//Valida secciones detalle                       
										$valorElementoComponenteDet = $domDoc->createTextNode($array[$seccion][$seccionDet]);
										$elementoComponenteDet->appendChild($valorElementoComponenteDet);
										//$componente->appendChild($elementoComponenteDet);
									}
								}
							} else if ($tipoXML == 2) {//1= XML de consultas SQL
								if (array_key_exists($seccionDet, $array)) {//Valida secciones detalle
									switch ($seccionDet) {
										case "Parametros":
											foreach ($array[$seccionDet] as $key => $value) {//Crea el elemento
												$elementoComponenteDetParametro = $domDoc->createElement($key);
												$valorElementoComponenteDetParametro = $domDoc->createTextNode($value); //Asigna el valor al elemento
												$elementoComponenteDetParametro->appendChild($valorElementoComponenteDetParametro);
												$elementoComponenteDet->appendChild($elementoComponenteDetParametro);
											}
											break;
										default:
											$valorElementoComponenteDet = $domDoc->createTextNode($array[$seccionDet]);
											$elementoComponenteDet->appendChild($valorElementoComponenteDet);
											break;
									}
								}
							}
	
	
							$componente->appendChild($elementoComponenteDet);
						}
						break;
				}
			}
			return $domDoc->saveXML();
			echo htmlentities($domDoc->saveXML());
		}
	
		//Obtiene le fecha actual con formato SIESA
		public function getFechaActual() {
			$dbVariables = new Dbvariables();
			$obj_aux = $dbVariables->getFechaactual();
			$fecha_actual = $this->formatearFechaAMD($obj_aux["fecha_actual"]);
			
			return $fecha_actual;
		}
	
		/* Conector para las Factura Directa a SIESA */
		/* ----------------------------------------- */
	
		public function ejecutarConsulta($idConsulta, $compania, $arrayParametros = NULL) {
			$dbConsulta = $this->dbServiciosIntegracion->getServicioIntegracionConsulta($idConsulta); //Id de la consulta

			$array = array();
			$array['Conexion'] = $dbConsulta['nombreConexion_sico'];
			$array['IdCia'] = 1;
			$array['IdProveedor'] = $dbConsulta['idProveedor_sico'];
			$array['IdConsulta'] = $dbConsulta['idConsulta_sico'];
			$array['Usuario'] = $dbConsulta['usuario_sico'];
			$array['Clave'] = $dbConsulta['clave_sico'];
			isset($arrayParametros) ? $array['Parametros'] = $arrayParametros : '';
						
			//var_dump($dbConsulta['idConsulta_sico']);
			$parametros = $this->configurarArray($dbConsulta['idConsulta_sico'], $array);
			$parametrosSOAP	= $this->armarArray($parametros);
			
			$url = $dbConsulta['url_wsdl'];
			$soapclient = new nusoap_client($url, "wsdl");
			
			$respuesta = $soapclient->call($dbConsulta["idConsulta_sico"], $parametrosSOAP);
			
			$result = $this->validarRespuestaSOAP($dbConsulta["idConsulta_sico"],$respuesta);
			
			return $result;
		}
	
		public function configurarArray($id_sico, $arrayParametros){
			
			if(isset($arrayParametros["Parametros"])){
				
				switch($id_sico){
					case "CONSULTA_FACTURA_REFERENCIA"://LLeva 6 campos
					case "CONSULTA_PEDIDO_REFERENCIA":
					case "CONSULTA_PEDIDO_ESTADOS":
						$arrayParametros["Parametros"]["CAMPO_1"] = "ID_PAGO";
						$arrayParametros["Parametros"]["CAMPO_2"] = "CIA";	
						$arrayParametros["Parametros"]["DATO_1"] = $arrayParametros["Parametros"]["ID_PAGO"];
						$arrayParametros["Parametros"]["DATO_2"] = $arrayParametros["Parametros"]["CIA"];
					break;
					case "CONSULTA_TERCEROS"://LLeva 6 campos
					case "CONSULTA_CONDICIONES_PAGO":
					case "CONSULTA_RCA_INTEGRACION":
						$arrayParametros["Parametros"]["CAMPO_1"] = "CIA";
						$arrayParametros["Parametros"]["CAMPO_2"] = "NIT";	
						$arrayParametros["Parametros"]["DATO_1"] = $arrayParametros["Parametros"]["CIA"];
						$arrayParametros["Parametros"]["DATO_2"] = $arrayParametros["Parametros"]["NIT"];						
					break;
					case "CONSULTA_FACTURAS_DETALLE"://LLeva 6 campos
						$arrayParametros["Parametros"]["CAMPO_1"] = "CIA";
						$arrayParametros["Parametros"]["CAMPO_2"] = "CONSEC";
						$arrayParametros["Parametros"]["DATO_1"] = $arrayParametros["Parametros"]["CIA"];
						$arrayParametros["Parametros"]["DATO_2"] = $arrayParametros["Parametros"]["CONSEC"];						
					break;
					case "CONSULTA_MOVIMIENTOS_PEDIDO_INTEGRACION"://LLeva 6 campos
						$arrayParametros["Parametros"]["CAMPO_1"] = "CIA";
						$arrayParametros["Parametros"]["CAMPO_2"] = "CONSECUTIVO";
						$arrayParametros["Parametros"]["DATO_1"] = $arrayParametros["Parametros"]["CIA"];
						$arrayParametros["Parametros"]["DATO_2"] = $arrayParametros["Parametros"]["CONSECUTIVO"];	
					break;
					case "CONSULTA_SALIDAS_INTEGRACION": //LLeva 12 campos
						$arrayParametros["Parametros"]["CAMPO_1"] = "CIA";
						$arrayParametros["Parametros"]["CAMPO_2"] = "CO";
						$arrayParametros["Parametros"]["CAMPO_3"] = "TIPO_DOCTO";
						$arrayParametros["Parametros"]["CAMPO_4"] = "NIT";
						$arrayParametros["Parametros"]["DATO_1"] = $arrayParametros["Parametros"]["CIA"];
						$arrayParametros["Parametros"]["DATO_2"] = $arrayParametros["Parametros"]["CO"];	
						$arrayParametros["Parametros"]["DATO_3"] = $arrayParametros["Parametros"]["TIPO_DOCTO"];
						$arrayParametros["Parametros"]["DATO_4"] = $arrayParametros["Parametros"]["NIT"];
						
					break;
					case "CONSULTA_INVENTARIO_X_REFERENCIA": //LLeva 12 campos
						$arrayParametros["Parametros"]["CAMPO_1"] = "REFERENCIA";
						$arrayParametros["Parametros"]["CAMPO_2"] = "CIA";
						$arrayParametros["Parametros"]["CAMPO_3"] = "BODEGA";
						$arrayParametros["Parametros"]["CAMPO_4"] = "LOTE";
						$arrayParametros["Parametros"]["DATO_1"] = $arrayParametros["Parametros"]["REFERENCIA"];
						$arrayParametros["Parametros"]["DATO_2"] = $arrayParametros["Parametros"]["CIA"];	
						$arrayParametros["Parametros"]["DATO_3"] = $arrayParametros["Parametros"]["BODEGA"];
						$arrayParametros["Parametros"]["DATO_4"] = $arrayParametros["Parametros"]["LOTE"];
						
					break;
					
				}
				
			}
				
			return $arrayParametros;
			
		}
		public function armarArray($parametros){
			
			switch(count($parametros["Parametros"])){
				case "6":
					$parametrosSOAP = Array(
						"Usuario" => $parametros["Usuario"],
						"Clave" => $parametros["Clave"],
						"Conexion" => $parametros["Conexion"],
						$parametros["Parametros"]["CAMPO_1"] => $parametros["Parametros"]["DATO_1"],
						$parametros["Parametros"]["CAMPO_2"] => $parametros["Parametros"]["DATO_2"],
					);
					
				break;
				case "12":
					$parametrosSOAP = Array(
						"Usuario" => $parametros["Usuario"],
						"Clave" => $parametros["Clave"],
						"Conexion" => $parametros["Conexion"],
						$parametros["Parametros"]["CAMPO_1"] => $parametros["Parametros"]["DATO_1"],
						$parametros["Parametros"]["CAMPO_2"] => $parametros["Parametros"]["DATO_2"],
						$parametros["Parametros"]["CAMPO_3"] => $parametros["Parametros"]["DATO_3"],
						$parametros["Parametros"]["CAMPO_4"] => $parametros["Parametros"]["DATO_4"],
					);
					
				break;
				default:
					$parametrosSOAP = Array();
				break;
			}
			return $parametrosSOAP;
			
		}
		
		public function validarRespuestaSOAP($id_si,$respuesta){
			
			$array = $this->convertirXMLtoArray($id_si,$respuesta);
			
			return $array;	
		}
			
		public function convertirXMLtoArray($id_si,$respuesta){//Transformar la respuesta string del SOAP en un array
			
			$convmap = array (0, 0x10FFFF, 0, 0xFFFFFF);
			
			$respuesta = mb_decode_numericentity($respuesta[$id_si."Result"], $convmap, 'UTF-8');
			$array_response = array();
		
			if(!empty($respuesta)){
				
				$xml = new SimpleXMLElement( $respuesta);
				$array_response = $this->XMLObjectToArray($xml);
				$array_response = $array_response["Resultado"];
						
			}
			
			
			return $array_response;
			
			
		}
		
		function XMLObjectToArray( $xmlObject, $out = array () ){//Convertir de XMLelement a Array
			foreach ( (array) $xmlObject as $index => $node )
				$out[$index] = ( is_object ( $node ) ) ? $this->XMLObjectToArray ( $node ) : $node;
				
			return $out;
		}
		
	
		//Formatea una fecha dd/mm/aaaa al formato de SIESA (aaaammdd)
		public function formatearFechaDMA($fecha) {
			$dia_aux = substr($fecha, 0, 2);
			$mes_aux = substr($fecha, 3, 2);
			$ano_aux = substr($fecha, 6, 4);
			return $ano_aux.$mes_aux.$dia_aux;
		}
		
		//Formatea una fecha aaaa-mm-dd al formato de SIESA (aaaammdd)
		public function formatearFechaAMD($fecha) {
			$dia_aux = substr($fecha, 8, 2);
			$mes_aux = substr($fecha, 5, 2);
			$ano_aux = substr($fecha, 0, 4);
			return $ano_aux.$mes_aux.$dia_aux;
		}
		
	}
?>
