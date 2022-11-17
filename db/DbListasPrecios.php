<?php
	require_once("DbConexion.php");
	
	class DbListasPrecios extends DbConexion {
		//Listado precios por idConvenio y Tipo de servicio
		public function getListasPreciosServicio($idPlan, $tipoServicio, $accion, $parametro) {
			try {
				$parametro = str_replace(" ", "%", $parametro);
				$solicitud_aux = "";
				$join_aux = "";
				$campo_aux = "";
				$buscaPrecio = "";
				$orderBy = "";
				
				if ($tipoServicio == "P") {
					$solicitud_aux = "LP.cod_procedimiento";
					$join_aux = "LEFT JOIN maestro_procedimientos MS ON MS.cod_procedimiento=LP.cod_procedimiento
								 INNER JOIN (
									SELECT L2.cod_procedimiento, L2.tipo_bilateral, MAX(L2.fecha_ini) AS fecha_ini
									FROM listas_precios L2
									WHERE L2.id_plan=".$idPlan."
									GROUP BY L2.cod_procedimiento, L2.tipo_bilateral
								 ) T ON LP.cod_procedimiento=T.cod_procedimiento AND LP.tipo_bilateral=T.tipo_bilateral AND LP.fecha_ini=T.fecha_ini ";
					$campo_aux = "MS.nombre_procedimiento AS nombre_aux, MS.ind_activo AS ind_activo_aux";
					
					if ($parametro != "") {
						$buscaPrecio = "AND (MS.nombre_procedimiento LIKE '%".$parametro."%'
										OR MS.cod_procedimiento LIKE '".$parametro."%') ";
					}
					$buscaPrecio .= "GROUP BY LP.cod_procedimiento, LP.tipo_bilateral ";
					$buscaPrecio .= "ORDER BY MS.nombre_procedimiento, LP.fecha_ini DESC, LP.tipo_bilateral ";
				} else if ($tipoServicio == "I") {
					$solicitud_aux = "LP.cod_insumo";
					$join_aux = " LEFT JOIN maestro_insumos MS ON MS.cod_insumo=LP.cod_insumo
								  INNER JOIN (
										SELECT L2.cod_insumo, L2.tipo_bilateral, MAX(L2.fecha_ini) AS fecha_ini
										FROM listas_precios L2
										WHERE L2.id_plan=".$idPlan."
										GROUP BY L2.cod_insumo, L2.tipo_bilateral
								  ) T ON LP.cod_insumo=T.cod_insumo AND LP.tipo_bilateral=T.tipo_bilateral AND LP.fecha_ini=T.fecha_ini ";
					$campo_aux = "MS.nombre_insumo AS nombre_aux, 1 AS ind_activo_aux";
					
					if ($parametro != "") {
						$buscaPrecio = "AND (MS.nombre_insumo LIKE '%".$parametro."%'
										OR MS.cod_insumo='".$parametro."') ";
					}
					$buscaPrecio .= "GROUP BY LP.cod_insumo, LP.tipo_bilateral ";
					$buscaPrecio .= "ORDER BY MS.nombre_insumo, LP.fecha_ini DESC, LP.tipo_bilateral ";
				} else if ($tipoServicio == "M") {
					$solicitud_aux = "LP.cod_medicamento";
					$join_aux = "LEFT JOIN maestro_medicamentos MS ON MS.cod_medicamento=LP.cod_medicamento
								 INNER JOIN (
										   SELECT L2.cod_medicamento, L2.tipo_bilateral, MAX(L2.fecha_ini) AS fecha_ini
										   FROM listas_precios L2
										   WHERE L2.id_plan=".$idPlan."
										   GROUP BY L2.cod_medicamento, L2.tipo_bilateral
										) T ON LP.cod_medicamento=T.cod_medicamento AND LP.tipo_bilateral=T.tipo_bilateral AND LP.fecha_ini=T.fecha_ini ";
					$campo_aux = "MS.nombre_generico AS nombre_aux, 1 AS ind_activo_aux";
					
					if ($parametro != "") {
						$buscaPrecio = "AND (MS.nombre_generico LIKE '%".$parametro."%'
										OR MS.nombre_comercial LIKE '%".$parametro."%'
										OR MS.cod_medicamento='".$parametro."') ";
					}
					$buscaPrecio .= "GROUP BY LP.cod_medicamento, LP.tipo_bilateral ";
					$buscaPrecio .= "ORDER BY MS.nombre_generico, LP.fecha_ini DESC, LP.tipo_bilateral ";
				} else if ($tipoServicio == "Q") {
					$solicitud_aux = "LP.id_paquete_p";
					$join_aux = "LEFT JOIN paquetes_procedimientos MQ ON MQ.id_paquete_p=LP.id_paquete_p
								 INNER JOIN (
										   SELECT L2.id_paquete_p, L2.tipo_bilateral, MAX(L2.fecha_ini) AS fecha_ini
										   FROM listas_precios L2
										   WHERE L2.id_plan=".$idPlan."
										   GROUP BY L2.id_paquete_p, L2.tipo_bilateral
										) T ON LP.id_paquete_p=T.id_paquete_p AND LP.tipo_bilateral=T.tipo_bilateral AND LP.fecha_ini=T.fecha_ini ";
					$campo_aux = "MQ.nom_paquete_p AS nombre_aux, 1 AS ind_activo_aux";
					
					if ($parametro != "") {
						$buscaPrecio = "AND (MQ.nom_paquete_p LIKE '%".$parametro."%'
										OR MQ.id_paquete_p='".$parametro."') ";
					}
					$buscaPrecio .= "GROUP BY LP.id_paquete_p, LP.tipo_bilateral ";
					$buscaPrecio .= "ORDER BY MQ.nom_paquete_p, LP.fecha_ini DESC, LP.tipo_bilateral ";
				}
				
				$sql = "SELECT LP.*, ".$campo_aux.", DATE_FORMAT(LP.fecha_ini, '%d/%m/%Y') AS fecha_ini_aux,
						DATE_FORMAT(LP.fecha_fin, '%d/%m/%Y') AS fecha_final_aux 
						FROM listas_precios LP ".
						$join_aux.
						"WHERE LP.id_plan=".$idPlan."
						AND ".$solicitud_aux." IS NOT NULL 
						AND LP.fecha_fin IS NULL ".
						$buscaPrecio;
				//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	
		/* Funcion que guarda y/o Edita los precios */
		public function guardarEditarListasPrecios($accion, $tipoServicio, $fechaInicial, $fechaFinal,
				$codServicio, $plan, $tipoBilateral, $valorTotal, $valorCuota, $idUsuarioCrea, $idPrecio) {
			try {
				if ($fechaFinal == "") {
					$fechaFinal = "NULL";
				} else {
					$fechaFinal = "'".$fechaFinal."'";
				}
	
				$sql = "CALL pa_crear_editar_listas_precios(".$accion.", '".$tipoServicio."', '".$fechaInicial."', ".$fechaFinal.", '" .
						$codServicio."', ".$plan.", ".$tipoBilateral.", ".$valorTotal.", ".$valorCuota.", ".$idUsuarioCrea.", ".$idPrecio.", @id)";
	
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado_out = $arrResultado["@id"];
	
				return $resultado_out;
			} catch (Exception $e) {
				return array();
			}
		}
	
		//Datos del registro seleccionado en el formulario Precios x convenio
		public function getPrecio($idPrecio) {
			try {
				$sql = "SELECT LP.*, DATE_FORMAT(LP.fecha_ini, '%d/%m/%Y') AS fecha_ini_aux,
						DATE_FORMAT(LP.fecha_fin, '%d/%m/%Y') AS fecha_fin_aux, MP.nombre_procedimiento,
						MM.nombre_generico, MI.nombre_insumo, MQ.nom_paquete_p
						FROM listas_precios LP
						LEFT JOIN maestro_procedimientos MP ON MP.cod_procedimiento=LP.cod_procedimiento
						LEFT JOIN maestro_medicamentos MM ON MM.cod_medicamento=LP.cod_medicamento
						LEFT JOIN maestro_insumos MI ON MI.cod_insumo=LP.cod_insumo
						LEFT JOIN paquetes_procedimientos MQ ON MQ.id_paquete_p=LP.id_paquete_p
						WHERE LP.id_precio=".$idPrecio;
	
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	
		//Listado precios por idConvenio y parametro ingresaod para buscar
		public function getListasPreciosPorPlanParametro($id_plan, $parametro, $fecha = "", $ind_activo = "") {
			try {
				$parametro = trim(str_replace(" ", "%", $parametro));
				if ($fecha != "") {
					$fecha = "'".$fecha."'";
				} else {
					$fecha = "DATE(NOW())";
				}
				
				$sql = "SELECT LP.*, MP.nombre_procedimiento, CONCAT(MM.nombre_comercial, ' - ', MM.nombre_generico) AS nombre_medicamento_aux,
						MI.nombre_insumo, MQ.nom_paquete_p, P.ind_tipo_pago
						FROM listas_precios LP
						INNER JOIN planes P ON LP.id_plan=P.id_plan
						LEFT JOIN maestro_procedimientos MP ON MP.cod_procedimiento=LP.cod_procedimiento ";
				if ($ind_activo != "") {
					$sql .= "AND MP.ind_activo=".$ind_activo." ";
				}
				$sql .= "LEFT JOIN maestro_medicamentos MM ON MM.cod_medicamento=LP.cod_medicamento
						LEFT JOIN maestro_insumos MI ON MI.cod_insumo=LP.cod_insumo
						LEFT JOIN paquetes_procedimientos MQ ON MQ.id_paquete_p=LP.id_paquete_p
						WHERE LP.id_plan=".$id_plan." ";
				if ($parametro != "") {
					$sql .= "AND (MP.nombre_procedimiento LIKE '%".$parametro."%'
							OR CONCAT(MM.nombre_comercial, ' ', MM.nombre_generico) LIKE '%".$parametro."%'
							OR MI.nombre_insumo LIKE '%".$parametro."%'
							OR MP.cod_procedimiento='".$parametro."'
							OR MM.cod_medicamento='".$parametro."'
							OR MI.cod_insumo='".$parametro."'
							OR MQ.id_paquete_p='".$parametro."') ";
				}
				$sql .= "AND DATE(LP.fecha_ini)<=".$fecha."
						AND DATE(IFNULL(LP.fecha_fin, ".$fecha."))>=".$fecha."
						ORDER BY LP.tipo_precio DESC, MP.nombre_procedimiento, nombre_medicamento_aux, MI.nombre_insumo";
				//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	
		/**
		 * Ver historial de un procedimeitno de un convenio y plan especifico
		 */
		public function getHistorialPrecios($cod_procedimiento, $id_plan, $tipoServicio, $tipo_bilateral) {
			try {
				$solicitud_aux = "";
				$join_aux = "";
				$campo_aux = "";
				$buscaPrecio = "";
				$orderBy = "";
	
				if ($tipo_bilateral == "-1") {
					$tipo_bilateral = 0;
				}
				
				if ($tipoServicio == "P") {
					$solicitud_aux = "LP.cod_procedimiento";
					$join_aux = "LEFT JOIN maestro_procedimientos MP ON MP.cod_procedimiento=LP.cod_procedimiento";
					$campo_aux = "MP.nombre_procedimiento AS nombre_aux";
					$buscaPrecio .= "ORDER BY MP.nombre_procedimiento, LP.fecha_ini DESC, LP.tipo_bilateral ";
				} else if ($tipoServicio == "M") {
					$solicitud_aux = "LP.cod_medicamento";
					$join_aux = "LEFT JOIN maestro_medicamentos MM ON MM.cod_medicamento=LP.cod_medicamento";
					$campo_aux = "MM.nombre_generico AS nombre_aux";
					$buscaPrecio .= "ORDER BY MM.nombre_generico, LP.fecha_ini DESC, LP.tipo_bilateral ";
				} else if ($tipoServicio == "I") {
					$solicitud_aux = "LP.cod_insumo";
					$join_aux = " LEFT JOIN maestro_insumos MI ON MI.cod_insumo=LP.cod_insumo";
					$campo_aux = "MI.nombre_insumo AS nombre_aux";
					$buscaPrecio .= "ORDER BY MI.nombre_insumo, LP.fecha_ini DESC, LP.tipo_bilateral ";
				} else if ($tipoServicio == "Q") {
					$solicitud_aux = "LP.id_paquete_p";
					$join_aux = " LEFT JOIN paquetes_procedimientos MQ ON MQ.id_paquete_p=LP.id_paquete_p";
					$campo_aux = "MQ.nom_paquete_p AS nombre_aux";
					$buscaPrecio .= "ORDER BY MQ.nom_paquete_p, LP.fecha_ini DESC, LP.tipo_bilateral ";
				}
	
				$sql = "SELECT LP.*, $campo_aux, DATE_FORMAT(LP.fecha_ini, '%d/%m/%Y') AS fecha_ini_aux,
						DATE_FORMAT(LP.fecha_fin, '%d/%m/%Y') AS fecha_final_aux 
						FROM listas_precios LP
						$join_aux
						WHERE LP.id_plan=".$id_plan."
						AND ".$solicitud_aux." = $cod_procedimiento
						AND LP.tipo_bilateral = $tipo_bilateral
						$buscaPrecio";
				//echo($sql);
	
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	
		/**
		 * Guardar el precio de un procedimiento
		 */
		function GuardarListasPrecios($tipoServicio, $fechaInicial, $fechaFinal, $fechaFinalAnterior, $codServicio,
				$plan, $tipoBilateral, $valorTotal, $valorCuota, $idUsuario, $idPrecio) {
			try {
				if ($fechaFinal == "") {
					$fechaFinal = "NULL";
				} else {
					$fechaFinal = "'".$fechaFinal."'";
				}
	
				if ($valorCuota == "") {
					$valorCuota = 0;
				}
	
				if ($fechaFinalAnterior == "") {
					$ind_fecha_anterior = 1;
				} else {
					$ind_fecha_anterior = 0;
				}
	
				$sql = "CALL pa_crear_listas_precios('".$tipoServicio."', STR_TO_DATE('".$fechaInicial."', '%d/%m/%Y'), STR_TO_DATE(".$fechaFinal.", '%d/%m/%Y'), " .
						$ind_fecha_anterior.", STR_TO_DATE('".$fechaFinalAnterior."', '%d/%m/%Y'), '".$codServicio."', ".$plan.", ".$tipoBilateral.", " .
						$valorTotal.", ".$valorCuota.", ".$idUsuario.", ".$idPrecio.", @id)";
				//echo($sql);
	
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado_out = $arrResultado["@id"];
	
				return $resultado_out;
			} catch (Exception $e) {
				return -2;
			}
		}
		
		public function getPrecioFecha($id_plan, $codigo, $tipo_precio, $tipo_bilateral, $fecha = "") {
			try {
				if ($fecha != "") {
					$fecha = "STR_TO_DATE('".$fecha."', '%d/%m/%Y')";
				} else {
					$fecha = "CURDATE()";
				}
				
				$sql = "SELECT LP.*, MP.nombre_procedimiento, CONCAT(MM.nombre_comercial, ' - ', MM.nombre_generico) AS nombre_medicamento_aux,
						MI.nombre_insumo, MQ.nom_paquete_p
						FROM listas_precios LP
						LEFT JOIN maestro_procedimientos MP ON MP.cod_procedimiento=LP.cod_procedimiento
						LEFT JOIN maestro_medicamentos MM ON MM.cod_medicamento=LP.cod_medicamento
						LEFT JOIN maestro_insumos MI ON MI.cod_insumo=LP.cod_insumo
						LEFT JOIN paquetes_procedimientos MQ ON MQ.id_paquete_p=LP.id_paquete_p
						WHERE LP.id_plan=".$id_plan."
						AND DATE(LP.fecha_ini)<=".$fecha."
						AND IFNULL(DATE(LP.fecha_fin), ".$fecha.")>=".$fecha."
						AND LP.tipo_bilateral=".$tipo_bilateral." ";
				switch ($tipo_precio) {
					case "P":
						$sql .= "AND LP.cod_procedimiento='".$codigo."'";
						break;
					case "M":
						$sql .= "AND LP.cod_medicamento='".$codigo."'";
						break;
					case "I":
						$sql .= "AND LP.cod_insumo='".$codigo."'";
						break;
				}
				//echo($sql);
	
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Función que retorna el listado de precios configurados en el plan y la configuración de UVR ISS 2001 de un listado de procedimientos
		public function getListaValoresLiqQx($id_plan, $arr_procedimientos_cx) {
			try {
				$cadena_aux = "";
				foreach ($arr_procedimientos_cx as $proc_aux) {
					if ($cadena_aux != "") {
						$cadena_aux .= ", ";
					}
					$cadena_aux .= "'".$proc_aux."'";
				}
				
				$sql = "SELECT MP.cod_procedimiento, MP.id_especialidad, MP.id_via, I.tipo_valor_iss_proc, I.valor_iss_proc, LP.id_precio, LP.valor, LP.tipo_bilateral
						FROM maestro_procedimientos MP
						LEFT JOIN listas_precios LP ON MP.cod_procedimiento=LP.cod_procedimiento
						AND LP.id_plan=".$id_plan." AND CURDATE() BETWEEN LP.fecha_ini AND IFNULL(LP.fecha_fin, CURDATE())
						LEFT JOIN iss_2001_proc I ON MP.cod_procedimiento=I.cod_cups_ma_proc
						WHERE MP.cod_procedimiento IN (".$cadena_aux.")
						AND MP.tipo_procedimiento='P'
						AND MP.ind_proc_qx=1 /* OR MP.ind_proc_examen=1 */
						AND MP.ind_activo=1
						ORDER BY MP.cod_procedimiento, LP.tipo_bilateral";
				
				//echo($sql);
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Función que halla los precios vigentes de los componentes de un paquete dentro de un plan
		function getListaPreciosPaquetePlan($id_paquete, $id_plan) {
			try {
				$sql = "SELECT PD.*, MP.nombre_procedimiento, MI.nombre_insumo, P.ind_tipo_pago, LP.id_precio,
						IFNULL(LP.tipo_bilateral, 0) AS tipo_bilateral, IFNULL(LP.valor, 0) AS valor_lista, IFNULL(LP.valor_cuota, 0) AS valor_cuota
						FROM paquetes_procedimientos_det PD
						CROSS JOIN planes P
						LEFT JOIN listas_precios LP ON (PD.cod_procedimiento=LP.cod_procedimiento OR PD.cod_insumo=LP.cod_insumo)
						AND P.id_plan=LP.id_plan AND CURDATE() BETWEEN LP.fecha_ini AND IFNULL(LP.fecha_fin, CURDATE())
						LEFT JOIN maestro_procedimientos MP ON PD.cod_procedimiento=MP.cod_procedimiento
						LEFT JOIN maestro_insumos MI ON PD.cod_insumo=MI.cod_insumo
						WHERE PD.id_paquete_p=".$id_paquete."
						AND P.id_plan=".$id_plan."
						ORDER BY PD.cod_insumo, PD.cod_procedimiento, LP.tipo_bilateral";
				
				//echo($sql);
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
	}
?>
