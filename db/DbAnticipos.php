<?php
	require_once("DbConexion.php");
	
	class DbAnticipos extends DbConexion {
		public function get_anticipo($id_anticipo) {
			try {
				$sql = "SELECT A.*, PC.nombre_1, PC.nombre_2, PC.apellido_1, PC.apellido_2, PC.id_tipo_documento, PC.numero_documento,
						PC.direccion, PC.telefono_1, PC.telefono_2, LD.nombre_detalle AS tipo_documento, LD.codigo_detalle AS cod_tipo_documento,
						M.nom_mun AS nom_mun_t, D.nom_dep AS nom_dep_t, PC.nom_dep, PC.nom_mun, PA.nombre_pais, M.cod_mun_dane,
						DATE_FORMAT(A.fecha_crea, '%d/%m/%Y') AS fecha_crea_t, UC.nombre_usuario AS nombre_usuario_crea,
						UC.apellido_usuario AS apellido_usuario_crea, UA.nombre_usuario AS nombre_usuario_anula,
						UA.apellido_usuario AS apellido_usuario_anula, DATE_FORMAT(A.fecha_anula, '%d/%m/%Y %h:%i:%s %p') AS fecha_anula_t,
						DATE_FORMAT(A.fecha_mod, '%d/%m/%Y %h:%i:%s %p') AS fecha_mod_t, UM.nombre_usuario AS nombre_usuario_mod,
						UM.apellido_usuario AS apellido_usuario_mod, DATE_FORMAT(A.fecha_crea, '%h:%i:%s %p') AS hora_crea_t,
						T.id_tipo_documento AS id_tipo_documento_tercero, DT.codigo_detalle AS cod_tipo_documento_tercero,
						DT.nombre_detalle AS tipo_documento_tercero, T.numero_documento AS numero_documento_tercero, T.nombre_tercero,
						(YEAR(IFNULL(A.fecha_crea, CURDATE()))-YEAR(PC.fecha_nacimiento))-(RIGHT(IFNULL(A.fecha_crea, CURDATE()), 5)<RIGHT(PC.fecha_nacimiento, 5)) AS edad
						FROM anticipos A
						INNER JOIN pacientes PC ON A.id_paciente=PC.id_paciente
						INNER JOIN listas_detalle LD ON PC.id_tipo_documento=LD.id_detalle
						LEFT JOIN municipios M ON PC.cod_mun=M.cod_mun_dane
						LEFT JOIN departamentos D ON PC.cod_dep=D.cod_dep
						LEFT JOIN paises PA ON PC.id_pais=PA.id_pais
						LEFT JOIN usuarios UC ON A.id_usuario_crea=UC.id_usuario
						LEFT JOIN usuarios UM ON A.id_usuario_mod=UM.id_usuario
						LEFT JOIN usuarios UA ON A.id_usuario_anula=UA.id_usuario
						LEFT JOIN terceros T ON A.id_tercero=T.id_tercero
						LEFT JOIN listas_detalle DT ON T.id_tipo_documento=DT.id_detalle
						WHERE A.id_anticipo=".$id_anticipo;
				
				//echo($sql."<br />");
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_lista_anticipos_pago($id_pago, $ind_activo = "") {
			try {
				$sql = "SELECT A.*, DATE_FORMAT(A.fecha_crea, '%d/%m/%Y') AS fecha_crea_t,
						DATE_FORMAT(A.fecha_crea, '%h:%i:%s %p') AS hora_crea_t, PA.valor AS valor_pago,
						T.id_tipo_documento AS id_tipo_documento_tercero, DT.codigo_detalle AS cod_tipo_documento_tercero,
						DT.nombre_detalle AS tipo_documento_tercero, T.numero_documento AS numero_documento_tercero, T.nombre_tercero
						FROM pagos_anticipos PA
						INNER JOIN anticipos A ON PA.id_anticipo=A.id_anticipo
						LEFT JOIN terceros T ON A.id_tercero=T.id_tercero
						LEFT JOIN listas_detalle DT ON T.id_tipo_documento=DT.id_detalle
						WHERE PA.id_pago=".$id_pago." ";
				if ($ind_activo != "") {
					$sql .= "AND PA.ind_activo=".$ind_activo." ";
				}
				$sql .= "ORDER BY A.id_anticipo";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_lista_anticipos($parametro) {
			try {
				$parametro = str_replace(" ", "%", $parametro);
				
				$concatenacion = "(PC.numero_documento='".$parametro."'
								  OR CONCAT(PC.nombre_1, ' ', IFNULL(PC.nombre_2, ''), ' ', PC.apellido_1, ' ', IFNULL(PC.apellido_2, '')) LIKE '%".$parametro."%')
								  OR A.id_anticipo=".intval($parametro, 10);
				
				$sql = "SELECT A.*, PC.nombre_1, PC.nombre_2, PC.apellido_1, PC.apellido_2, PC.id_tipo_documento, PC.numero_documento,
						PC.direccion, PC.telefono_1, PC.telefono_2, LD.nombre_detalle AS tipo_documento, LD.codigo_detalle AS cod_tipo_documento,
						M.nom_mun AS nom_mun_t, D.nom_dep AS nom_dep_t, PC.nom_dep, PC.nom_mun, PA.nombre_pais, M.cod_mun_dane,
						DATE_FORMAT(A.fecha_crea, '%d/%m/%Y') AS fecha_crea_t, UC.nombre_usuario AS nombre_usuario_crea,
						UC.apellido_usuario AS apellido_usuario_crea, UA.nombre_usuario AS nombre_usuario_anula,
						UA.apellido_usuario AS apellido_usuario_anula, DATE_FORMAT(A.fecha_anula, '%d/%m/%Y %h:%i:%s %p') AS fecha_anula_t,
						DATE_FORMAT(A.fecha_mod, '%d/%m/%Y %h:%i:%s %p') AS fecha_mod_t, UM.nombre_usuario AS nombre_usuario_mod,
						UM.apellido_usuario AS apellido_usuario_mod, DATE_FORMAT(A.fecha_crea, '%h:%i:%s %p') AS hora_crea_t,
						T.id_tipo_documento AS id_tipo_documento_tercero, DT.codigo_detalle AS cod_tipo_documento_tercero,
						DT.nombre_detalle AS tipo_documento_tercero, T.nombre_tercero,
						(YEAR(IFNULL(A.fecha_crea, CURDATE()))-YEAR(PC.fecha_nacimiento))-(RIGHT(IFNULL(A.fecha_crea, CURDATE()), 5)<RIGHT(PC.fecha_nacimiento, 5)) AS edad
						FROM anticipos A
						INNER JOIN pacientes PC ON A.id_paciente=PC.id_paciente
						INNER JOIN listas_detalle LD ON PC.id_tipo_documento=LD.id_detalle
						LEFT JOIN municipios M ON PC.cod_mun=M.cod_mun_dane
						LEFT JOIN departamentos D ON PC.cod_dep=D.cod_dep
						LEFT JOIN paises PA ON PC.id_pais=PA.id_pais
						LEFT JOIN usuarios UC ON A.id_usuario_crea=UC.id_usuario
						LEFT JOIN usuarios UM ON A.id_usuario_mod=UM.id_usuario
						LEFT JOIN usuarios UA ON A.id_usuario_anula=UA.id_usuario
						LEFT JOIN terceros T ON A.id_tercero=T.id_tercero
						LEFT JOIN listas_detalle DT ON T.id_tipo_documento=DT.id_detalle
						WHERE ".$concatenacion."
						ORDER BY PC.nombre_1, PC.nombre_2, PC.apellido_1, PC.apellido_2, -A.fecha_crea";
				//echo("<textarea>".$sql."</textarea>");
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function registrar_anticipo($id_paciente, $id_lugar, $id_usuario_prof, $num_anticipo, $observaciones_anticipo, $id_tercero, $arr_medios_pago, $id_usuario) {
			try {
				//Se limpia la tabla temporal de medios de pago
				$sql = "DELETE FROM temporal_pagos_det_medios
						WHERE id_usuario=".$id_usuario;
				
				$arr_resultado = $this->ejecutarSentencia($sql, array());
				
				//Se agregan los medios de pago a la tabla temporal
				$valor_total = 0;
				if (count($arr_medios_pago) > 0) {
					foreach ($arr_medios_pago as $medio_pago_aux) {
						if ($medio_pago_aux["banco_pago"] == "") {
							$medio_pago_aux["banco_pago"] = "NULL";
						}
						if ($medio_pago_aux["id_usuario_autoriza"] == "") {
							$medio_pago_aux["id_usuario_autoriza"] = "NULL";
						}
						if ($medio_pago_aux["num_cheque"] == "") {
							$medio_pago_aux["num_cheque"] = "NULL";
						} else {
							$medio_pago_aux["num_cheque"] = "'".$medio_pago_aux["num_cheque"]."'";
						}
						if ($medio_pago_aux["num_cuenta"] == "") {
							$medio_pago_aux["num_cuenta"] = "NULL";
						} else {
							$medio_pago_aux["num_cuenta"] = "'".$medio_pago_aux["num_cuenta"]."'";
						}
						if ($medio_pago_aux["cod_seguridad"] == "") {
							$medio_pago_aux["cod_seguridad"] = "NULL";
						} else {
							$medio_pago_aux["cod_seguridad"] = "'".$medio_pago_aux["cod_seguridad"]."'";
						}
						if ($medio_pago_aux["num_autoriza"] == "") {
							$medio_pago_aux["num_autoriza"] = "NULL";
						} else {
							$medio_pago_aux["num_autoriza"] = "'".$medio_pago_aux["num_autoriza"]."'";
						}
						if ($medio_pago_aux["ano_vence"] == "") {
							$medio_pago_aux["ano_vence"] = "NULL";
						} else {
							$medio_pago_aux["ano_vence"] = "'".$medio_pago_aux["ano_vence"]."'";
						}
						if ($medio_pago_aux["mes_vence"] == "") {
							$medio_pago_aux["mes_vence"] = "NULL";
						} else {
							$medio_pago_aux["mes_vence"] = "'".$medio_pago_aux["mes_vence"]."'";
						}
						if ($medio_pago_aux["referencia"] == "") {
							$medio_pago_aux["referencia"] = "NULL";
						} else {
							$medio_pago_aux["referencia"] = "'".$medio_pago_aux["referencia"]."'";
						}
						if ($medio_pago_aux["fecha_consigna"] == "") {
							$medio_pago_aux["fecha_consigna"] = "NULL";
						} else {
							$medio_pago_aux["fecha_consigna"] = "STR_TO_DATE('".$medio_pago_aux["fecha_consigna"]."', '%d/%m/%Y')";
						}
						if ($medio_pago_aux["id_franquicia_tc"] == "") {
							$medio_pago_aux["id_franquicia_tc"] = "NULL";
						}
						$sql = "INSERT INTO temporal_pagos_det_medios
								(id_usuario, id_medio_pago, id_banco, valor_pago, id_usuario_autoriza, num_cheque, num_cuenta,
								cod_seguridad, num_autoriza, ano_vence, mes_vence, referencia, fecha_consigna, id_franquicia_tc)
								VALUES (".$id_usuario.", ".$medio_pago_aux["tipo_pago"].", ".$medio_pago_aux["banco_pago"].", ".
								$medio_pago_aux["valor_pago"].", ".$medio_pago_aux["id_usuario_autoriza"].", ".$medio_pago_aux["num_cheque"].", ".
								$medio_pago_aux["num_cuenta"].", ".$medio_pago_aux["cod_seguridad"].", ".$medio_pago_aux["num_autoriza"].", ".
								$medio_pago_aux["ano_vence"].", ".$medio_pago_aux["mes_vence"].", ".$medio_pago_aux["referencia"].", ".
								$medio_pago_aux["fecha_consigna"].", ".$medio_pago_aux["id_franquicia_tc"].")";
						
						//echo($sql."<br />");
						$arr_resultado = $this->ejecutarSentencia($sql, array());
						
						$valor_total += $medio_pago_aux["valor_pago"];
					}
					
					if ($id_lugar == "") {
						$id_lugar = "NULL";
					}
					if ($id_usuario_prof == "") {
						$id_usuario_prof = "NULL";
					}
					if ($observaciones_anticipo == "") {
						$observaciones_anticipo = "NULL";
					} else {
						$observaciones_anticipo = "'".$observaciones_anticipo."'";
					}
					if ($id_tercero == "") {
						$id_tercero = "NULL";
					}
					
					$sql = "CALL pa_crear_anticipo(".$id_paciente.", ".$id_lugar.", ".$id_usuario_prof.", '".$num_anticipo."', ".
							$valor_total.", ".$observaciones_anticipo.", ".$id_tercero.", ".$id_usuario.", @id)";
					//echo($sql."<br />");
					
					$arrCampos[0] = "@id";
					$arr_resultado = $this->ejecutarSentencia($sql, $arrCampos);
					$resultado_out = $arr_resultado["@id"];
					
					return $resultado_out;
				} else {
					return -4;
				}
			} catch (Exception $e) {
				return -2;
			}
		}
		
		//Función que marca como anulado un anticipo
		public function anular_anticipo($id_anticipo, $observaciones_anticipo, $id_causal_anula, $id_usuario) {
			try {
				$sql = "CALL pa_anular_anticipo(".$id_anticipo.", '".$observaciones_anticipo."', ".$id_causal_anula.", ".$id_usuario.", @id)";
				//echo($sql);
				
				$arr_campos[0] = "@id";
				$arr_resultado = $this->ejecutarSentencia($sql, $arr_campos);
				$resultado_out = $arr_resultado["@id"];
	
				return $resultado_out;
			} catch (Exception $e) {
				return array();
			}
		}
		
		/*
		Función que retorna los conceptos de pago asociados a un anticipo
		$tipo_orden
		1 - Por identificador de detalle
		2 - Por tipo de concepto de pago
		*/
		public function get_lista_anticipos_det_medios($id_anticipo, $tipo_orden = 1) {
			try {
				$sql = "SELECT AD.*, TP.nombre AS nombre_tipo_pago, TP.abrev_pago, BN.codigo_detalle AS codigo_banco,
						BN.nombre_detalle AS nombre_banco, T.nombre_tercero, TP.ind_negativo, TP.id_tipo_concepto,
						TC.nombre_detalle AS tipo_concepto, DATE_FORMAT(AD.fecha_consigna, '%d/%m/%Y') AS fecha_consigna_t
						FROM anticipos_det_medios AD
						INNER JOIN tipos_pago TP ON AD.id_medio_pago=TP.id
						LEFT JOIN listas_detalle BN ON AD.id_banco=BN.id_detalle
						LEFT JOIN terceros T ON AD.id_tercero=T.id_tercero
						LEFT JOIN listas_detalle TC ON TP.id_tipo_concepto=TC.id_detalle
						WHERE AD.id_anticipo=".$id_anticipo." ";
				switch ($tipo_orden) {
					case 1: //Por identificador de detalle
						$sql .= "ORDER BY AD.id_det_medio";
						break;
					case 2: //Por tipo de concepto de pago
						$sql .= "ORDER BY TC.orden, TP.id";
						break;
				}
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Función que trae los anticipos con saldo para un paciente y/o un tercero
		public function get_lista_anticipos_disponibles($id_paciente, $id_tercero) {
			try {
				if ($id_paciente == "") {
					$id_paciente = "-1";
				}
				if ($id_tercero == "") {
					$id_tercero = "-1";
				}
				$sql = "SELECT A.*, TD.codigo_detalle AS cod_tipo_documento, TD.nombre_detalle AS tipo_documento, T.numero_documento, T.nombre_tercero,
						DATE_FORMAT(A.fecha_crea, '%d/%m/%Y') AS fecha_crea_t, DATE_FORMAT(A.fecha_crea, '%h:%i:%s %p') AS hora_crea_t
						FROM anticipos A
						LEFT JOIN terceros T ON A.id_tercero=T.id_tercero
						LEFT JOIN listas_detalle TD ON T.id_tipo_documento=TD.id_detalle
						WHERE (A.id_paciente=".$id_paciente."
						OR A.id_tercero=".$id_tercero.")
						AND A.estado_anticipo=1
						AND A.saldo>0
						ORDER BY A.id_anticipo";
				//echo("<textarea>".$sql."</textarea>");
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_lista_anticipos_det_medios_fechas($fecha_ini, $fecha_fin, $id_lugar, $id_usuario_crea, $ind_anulado) {
			try {
				$sql = "SELECT A.*, AD.id_det_medio, id_medio_pago, valor_pago, PC.nombre_1, PC.nombre_2, PC.apellido_1, PC.apellido_2,
						PC.id_tipo_documento, PC.numero_documento, LD.nombre_detalle AS tipo_documento, LD.codigo_detalle AS cod_tipo_documento,
						DATE_FORMAT(A.fecha_crea, '%d/%m/%Y') AS fecha_crea_t, DATE_FORMAT(A.fecha_crea, '%h:%i:%s %p') AS hora_crea_t,
						UC.nombre_usuario AS nombre_usuario_crea, UC.apellido_usuario AS apellido_usuario_crea,
						T.id_tipo_documento AS id_tipo_documento_tercero, DT.codigo_detalle AS cod_tipo_documento_tercero,
						DT.nombre_detalle AS tipo_documento_tercero, T.nombre_tercero, T.numero_documento AS numero_documento_tercero
						FROM anticipos A
						INNER JOIN anticipos_det_medios AD ON A.id_anticipo=AD.id_anticipo
						INNER JOIN pacientes PC ON A.id_paciente=PC.id_paciente
						INNER JOIN listas_detalle LD ON PC.id_tipo_documento=LD.id_detalle
						LEFT JOIN usuarios UC ON A.id_usuario_crea=UC.id_usuario
						LEFT JOIN terceros T ON A.id_tercero=T.id_tercero
						LEFT JOIN listas_detalle DT ON T.id_tipo_documento=DT.id_detalle
						WHERE A.fecha_crea BETWEEN '".$fecha_ini." 00:00:00' AND '".$fecha_fin." 23:59:59' ";
				if ($id_lugar != "") {
					$sql .= "AND A.id_lugar=".$id_lugar." ";
				}
				if ($id_usuario_crea != "") {
					$sql .= "AND A.id_usuario_crea=".$id_usuario_crea." ";
				}
				if ($ind_anulado == 0) {
					$sql .= "AND A.estado_anticipo IN (1, 2) ";
				}
				$sql .= "ORDER BY DATE(A.fecha_crea), PC.apellido_1, PC.apellido_2, PC.nombre_1, PC.nombre_2";
				
				//echo($sql."<br />");
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
	}
?>
