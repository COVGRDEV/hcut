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
					case "Caja":
					case "CxC":
					case "CuotasCxC":
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
			//echo htmlentities($domDoc->saveXML());
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
			
	
			$tmpWsdl = $dbConsulta['url_wsdl'];
			$array = array();
			$array['NombreConexion'] = $dbConsulta['nombreConexion_sico'];
			$array['IdCia'] = 1;
			$array['IdProveedor'] = $dbConsulta['idProveedor_sico'];
			$array['IdConsulta'] = $dbConsulta['idConsulta_sico'];
			$array['Usuario'] = $dbConsulta['usuario_sico'];
			$array['Clave'] = $dbConsulta['clave_sico'];
			isset($arrayParametros) ? $array['Parametros'] = $arrayParametros : '';
			$xml = $this->armarXML($dbConsulta['id_si'], $array, 2);
			$client = new nusoap_client($tmpWsdl, 'wsdl');
			$client->soap_defencoding = 'UTF-8';
			$client->decode_utf8 = FALSE;
			
			$arrayParametrosConsulta = explode(";", $dbConsulta['parametros_sim']);
			$arrayParametrosConsulta['pvstrxmlParametros'] = $xml;
			$result = $client->call("EjecutarConsultaXML", $arrayParametrosConsulta);
			$array = $result['EjecutarConsultaXMLResult']['diffgram']['NewDataSet']['Resultado'];
			
			/*
			  NOTA:
			  Sí la variable $array retorna como valor un número 1 sin ser un array significa que no existe
			 */
		  
			if (!is_array($array)) {
				$array = -1;
			}
	
			return $array;
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
