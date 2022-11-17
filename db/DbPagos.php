<?php
	require_once("DbConexion.php");
	
	class DbPagos extends DbConexion {
		public function get_pago($id_admision) {
			try {
				$sql = "SELECT P.*, DATE_FORMAT(P.fecha_pago, '%d/%m/%Y') AS fecha_pago_t,
						DATE_FORMAT(P.fecha_pago, '%h:%i:%s %p') AS hora_pago_t, C.nombre_convenio, PL.nombre_plan,
						T1.nombre AS nombre_medio_pago_1, T2.nombre AS nombre_medio_pago_2, T3.nombre AS nombre_medio_pago_3
						FROM pagos P
						INNER JOIN convenios C ON P.id_convenio=C.id_convenio
						INNER JOIN planes PL ON P.id_plan=PL.id_plan
						LEFT JOIN tipos_pago T1 ON P.medio_pago_1=T1.id
						LEFT JOIN tipos_pago T2 ON P.medio_pago_2=T2.id
						LEFT JOIN tipos_pago T3 ON P.medio_pago_3=T3.id
						WHERE P.id_admision=".$id_admision;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_lista_pagos($id_admision) {
			try {
				$sql = "SELECT P.*, DATE_FORMAT(P.fecha_pago, '%d/%m/%Y') AS fecha_pago_t,
						DATE_FORMAT(P.fecha_pago, '%h:%i:%s %p') AS hora_pago_t, C.nombre_convenio, PL.nombre_plan,
						T1.nombre AS nombre_medio_pago_1, T2.nombre AS nombre_medio_pago_2, T3.nombre AS nombre_medio_pago_3
						FROM pagos P
						INNER JOIN convenios C ON P.id_convenio=C.id_convenio
						INNER JOIN planes PL ON P.id_plan=PL.id_plan
						LEFT JOIN tipos_pago T1 ON P.medio_pago_1=T1.id
						LEFT JOIN tipos_pago T2 ON P.medio_pago_2=T2.id
						LEFT JOIN tipos_pago T3 ON P.medio_pago_3=T3.id
						WHERE P.id_admision=".$id_admision."
						ORDER BY P.id_pago";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function pagosPendientes($parametro, $id_admision, $accion, $id_pago = "0") {
			try {
				if ($accion == "0") {
					$parametro = str_replace(" ", "%", $parametro);
	
					$concatenacion = "";
					if ($id_pago != "0") {
						$concatenacion = "P.id_pago=".$id_pago." ";
					} else if ($id_admision != "0") {
						$concatenacion = "P.id_admision=".$id_admision."
										  AND P.estado_pago<>3 ";
					} else {
						$concatenacion = "(PC.numero_documento='".$parametro."'
										  OR CONCAT(PC.nombre_1, ' ', IFNULL(PC.nombre_2, ''), ' ', PC.apellido_1, ' ', IFNULL(PC.apellido_2, '')) LIKE '%".$parametro."%')
										  OR P.id_pago=".intval($parametro, 10);
					}
					
					$sql = "SELECT P.*, PC.nombre_1, PC.nombre_2, PC.apellido_1, PC.apellido_2, PC.id_tipo_documento, PC.numero_documento,
							PC.direccion, PC.telefono_1, PC.telefono_2, LD.nombre_detalle AS tipo_documento, LD.codigo_detalle AS cod_tipo_documento,
							M.nom_mun AS nom_mun_t, D.nom_dep AS nom_dep_t, PC.nom_dep, PC.nom_mun, PC.email, PA.nombre_pais, M.cod_mun_dane,
							DATE_FORMAT(P.fecha_pago, '%d/%m/%Y') AS fecha_pago_t, TC.nombre_tipo_cita, U.nombre_usuario, U.apellido_usuario,
							DATE_FORMAT(A.fecha_estado , '%d/%m/%Y') AS fecha_estado_t, UB.nombre_usuario AS nombre_usuario_borra,
							UB.apellido_usuario AS apellido_usuario_borra, DATE_FORMAT(P.fecha_crea, '%d/%m/%Y %h:%i:%s %p') AS fecha_crea_t,
							DATE_FORMAT(P.fecha_borra, '%d/%m/%Y %h:%i:%s %p') AS fecha_borra_t, UA.nombre_usuario AS nombre_usuario_admision,
							UA.apellido_usuario AS apellido_usuario_admision, DATE_FORMAT(A.fecha_crea, '%d/%m/%Y %h:%i:%s %p') AS fecha_crea_admision_t,
							DATE_FORMAT(P.fecha_mod, '%d/%m/%Y %h:%i:%s %p') AS fecha_mod_pago_t, UC.nombre_usuario AS nombre_usuario_crea,
							UC.apellido_usuario AS apellido_usuario_crea, A.num_carnet, UP.nombre_usuario AS nombre_usuario_pago,
							UP.apellido_usuario AS apellido_usuario_pago, DATE_FORMAT(P.fecha_pago, '%d/%m/%Y %h:%i:%s %p') AS fecha_hora_pago_t,
							T.id_tipo_documento, DT.codigo_detalle AS cod_tipo_documento_tercero, DT.nombre_detalle AS tipo_documento_tercero, T.nombre_tercero,
							(YEAR(IFNULL(P.fecha_pago, CURDATE()))-YEAR(PC.fecha_nacimiento))-(RIGHT(IFNULL(P.fecha_pago, CURDATE()), 5)<RIGHT(PC.fecha_nacimiento, 5)) AS edad,
							PL.ind_desc_cc
							FROM pagos P
							LEFT JOIN admisiones A ON A.id_admision=P.id_admision
							INNER JOIN pacientes PC ON PC.id_paciente=P.id_paciente
							INNER JOIN listas_detalle LD ON LD.id_detalle=PC.id_tipo_documento
							INNER JOIN planes PL ON P.id_plan=PL.id_plan
							LEFT JOIN tipos_citas TC ON A.id_tipo_cita=TC.id_tipo_cita
							LEFT JOIN municipios M ON PC.cod_mun=M.cod_mun_dane
							LEFT JOIN departamentos D ON PC.cod_dep=D.cod_dep
							LEFT JOIN paises PA ON PC.id_pais=PA.id_pais
							LEFT JOIN usuarios UC ON P.id_usuario_crea=UC.id_usuario
							LEFT JOIN usuarios U ON P.id_usuario_mod=U.id_usuario
							LEFT JOIN usuarios UB ON P.id_usuario_borra=UB.id_usuario
							LEFT JOIN usuarios UA ON A.id_usuario_crea=UA.id_usuario
							LEFT JOIN usuarios UP ON P.id_usuario_pago=UP.id_usuario
							LEFT JOIN terceros T ON P.id_tercero=T.id_tercero
							LEFT JOIN listas_detalle DT ON T.id_tipo_documento=DT.id_detalle
							WHERE ".$concatenacion."
							ORDER BY PC.nombre_1, PC.nombre_2, PC.apellido_1, PC.apellido_2, P.estado_pago, -P.fecha_pago";
				} else if ($accion == '1') {
					$sql = "SELECT PC.nombre_1, PC.nombre_2, PC.apellido_1, PC.apellido_2, PC.id_tipo_documento,
							PC.numero_documento, PC.direccion, PC.telefono_1, PC.telefono_2, LD.nombre_detalle AS tipo_documento,
							M.nom_mun AS nom_mun_t, D.nom_dep AS nom_dep_t, PC.nom_dep, PC.nom_mun, PA.nombre_pais,
							M.cod_mun_dane, DATE_FORMAT(P.fecha_pago, '%d/%m/%Y') AS fecha_pago_t
							FROM pagos P
							INNER JOIN pacientes PC ON PC.id_paciente=P.id_paciente
							INNER JOIN listas_detalle LD ON LD.id_detalle=PC.id_tipo_documento
							LEFT JOIN municipios M ON PC.cod_mun=M.cod_mun_dane
							LEFT JOIN departamentos D ON PC.cod_dep=D.cod_dep
							LEFT JOIN paises PA ON PC.id_pais=PA.id_pais
							WHERE P.estado_pago=1
							AND (PC.numero_documento LIKE '%".$parametro."%'
							OR CONCAT(PC.nombre_1, ' ', IFNULL(PC.nombre_2, ''), ' ', PC.apellido_1, ' ', IFNULL(PC.apellido_2, '')) LIKE '%".$parametro."%')
							GROUP BY PC.id_paciente";
				}
				//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function pagosPendientesDetalle($parametro, $id_admision, $id_pago = "0") {
			try {
				$parametro = str_replace(" ", "%", $parametro);
				
				$concatenacion = "";
				if ($id_pago != "0") {
					$concatenacion = "P.id_pago=".$id_pago." ";
				} else if ($id_admision != "0") {
					$concatenacion = "P.id_admision=".$id_admision." ";
				} else {
					$concatenacion = "(PC.numero_documento='".$parametro."'
									  OR CONCAT(PC.nombre_1, ' ', IFNULL(PC.nombre_2, ''), ' ', PC.apellido_1, ' ', IFNULL(PC.apellido_2, '')) LIKE '%".$parametro."%')
									  OR P.id_pago=".intval($parametro, 10);
				}
				
				$sql = "SELECT PD.*, MD.nombre_procedimiento, MM.nombre_generico, MI.nombre_insumo, (PD.cantidad*PD.valor) AS total
						FROM pagos P
						INNER JOIN pacientes PC ON PC.id_paciente=P.id_paciente
						INNER JOIN pagos_detalle PD ON P.id_pago=PD.id_pago
						LEFT JOIN maestro_procedimientos MP ON MP.cod_procedimiento=PD.cod_procedimiento
						LEFT JOIN maestro_procedimientos_det MD ON MP.cod_procedimiento=MD.cod_procedimiento AND P.fecha_crea BETWEEN MD.fecha_ini AND IFNULL(MD.fecha_fin, P.fecha_crea)
						LEFT JOIN maestro_medicamentos MM ON MM.cod_medicamento=PD.cod_medicamento
						LEFT JOIN maestro_insumos MI ON MI.cod_insumo=PD.cod_insumo
						WHERE ".$concatenacion."
						ORDER BY P.id_pago, PD.id_detalle_precio";
				//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function pagosPendientes2($idPaciente, $idAdmision) {
			try {
				$concatenacion = '';
				if ($idAdmision != '0') {
					$concatenacion = "P.id_admision = $idAdmision";
				} else if ($idAdmision == '0') {
					$concatenacion = "P.id_paciente = '$idPaciente'";
				}
				
				$sql = "SELECT P.*, TC.nombre_tipo_cita, DATE_FORMAT(A.fecha_estado , '%d/%m/%Y') AS fecha_aux, A.id_convenio
						FROM pagos P
						INNER JOIN admisiones A ON A.id_admision = P.id_admision
						INNER JOIN tipos_citas TC ON TC.id_tipo_cita = A.id_tipo_cita
						WHERE P.estado_pago = 1 AND $concatenacion";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function pagosDetalle($id_admision) {
			try {
				$sql = "SELECT PD.*, MD.nombre_procedimiento, MM.nombre_generico, MI.nombre_insumo, (PD.cantidad*PD.valor) AS total
						FROM pagos_detalle PD
						LEFT JOIN maestro_procedimientos MP ON MP.cod_procedimiento = PD.cod_procedimiento
						LEFT JOIN maestro_procedimientos_det MD ON MP.cod_procedimiento=MD.cod_procedimiento AND PD.fecha_crea BETWEEN MD.fecha_ini AND IFNULL(MD.fecha_fin, PD.fecha_crea)
						LEFT JOIN maestro_medicamentos MM ON MM.cod_medicamento = PD.cod_medicamento
						LEFT JOIN maestro_insumos MI ON MI.cod_insumo = PD.cod_insumo
						WHERE PD.id_admision=".$id_admision;
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		public function pagosDetalleByIdPago($id_pago) {
			try {
				$sql = "SELECT PD.*, MD.nombre_procedimiento, MM.nombre_generico, MI.nombre_insumo, (PD.cantidad*PD.valor) AS total
						FROM pagos_detalle PD
						LEFT JOIN maestro_procedimientos MP ON MP.cod_procedimiento = PD.cod_procedimiento
						LEFT JOIN maestro_procedimientos_det MD ON MP.cod_procedimiento=MD.cod_procedimiento AND PD.fecha_crea BETWEEN MD.fecha_ini AND IFNULL(MD.fecha_fin, PD.fecha_crea)
						LEFT JOIN maestro_medicamentos MM ON MM.cod_medicamento = PD.cod_medicamento
						LEFT JOIN maestro_insumos MI ON MI.cod_insumo = PD.cod_insumo
						WHERE PD.id_pago=".$id_pago;
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_lista_pagos_detalle($id_pago) {
			try {
				$sql = "SELECT PD.*, MD.nombre_procedimiento, MM.nombre_generico, MI.nombre_insumo, (PD.cantidad*PD.valor) AS total
						FROM pagos_detalle PD
						LEFT JOIN maestro_procedimientos MP ON MP.cod_procedimiento = PD.cod_procedimiento
						LEFT JOIN maestro_procedimientos_det MD ON MP.cod_procedimiento=MD.cod_procedimiento AND PD.fecha_crea BETWEEN MD.fecha_ini AND IFNULL(MD.fecha_fin, PD.fecha_crea)
						LEFT JOIN maestro_medicamentos MM ON MM.cod_medicamento = PD.cod_medicamento
						LEFT JOIN maestro_insumos MI ON MI.cod_insumo = PD.cod_insumo
						WHERE PD.id_pago=".$id_pago;
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_pago_id($id_pago) {
			try {
				$sql = "SELECT P.*, DATE_FORMAT(P.fecha_pago, '%d/%m/%Y') AS fecha_pago_t,
						DATE_FORMAT(P.fecha_pago, '%h:%i:%s %p') AS hora_pago_t, C.nombre_convenio, PL.nombre_plan,
						PA.nombre_1, PA.nombre_2, PA.apellido_1, PA.apellido_2, PA.numero_documento,
						T.nombre_tercero, T.numero_documento AS numero_documento_tercero,
						UP.nombre_usuario, UP.apellido_usuario,
						T1.nombre AS nombre_medio_pago_1, T2.nombre AS nombre_medio_pago_2, T3.nombre AS nombre_medio_pago_3,PM.id_medio_pago
						FROM pagos P
						INNER JOIN convenios C ON P.id_convenio=C.id_convenio
						INNER JOIN planes PL ON P.id_plan=PL.id_plan
						INNER JOIN pacientes PA ON P.id_paciente=PA.id_paciente
						LEFT JOIN terceros T ON P.id_tercero=T.id_tercero
						LEFT JOIN usuarios UP ON P.id_usuario_pago=UP.id_usuario
						LEFT JOIN tipos_pago T1 ON P.medio_pago_1=T1.id
						LEFT JOIN tipos_pago T2 ON P.medio_pago_2=T2.id
						LEFT JOIN tipos_pago T3 ON P.medio_pago_3=T3.id
						LEFT JOIN pagos_det_medios PM ON P.id_pago = PM.id_pago
						WHERE P.id_pago=".$id_pago;
					//echo $sql;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaPagosDetalle($id_pago) {
			try {
				$sql = "SELECT PD.*, MD.nombre_procedimiento, MM.nombre_generico, MI.nombre_insumo, (PD.cantidad*PD.valor) AS total
						FROM pagos_detalle PD
						LEFT JOIN maestro_procedimientos MP ON MP.cod_procedimiento = PD.cod_procedimiento
						LEFT JOIN maestro_procedimientos_det MD ON MP.cod_procedimiento=MD.cod_procedimiento AND PD.fecha_crea BETWEEN MD.fecha_ini AND IFNULL(MD.fecha_fin, PD.fecha_crea)
						LEFT JOIN maestro_medicamentos MM ON MM.cod_medicamento = PD.cod_medicamento
						LEFT JOIN maestro_insumos MI ON MI.cod_insumo = PD.cod_insumo
						WHERE PD.id_pago=".$id_pago;
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function registrarPagos($idPago, $idAdmision, $idPaciente, $idUsuarioCrea, $tipoPago1, $BancoPago1, $valorPago1,
				$tipoPago2, $BancoPago2, $valorPago2, $tipoPago3, $BancoPago3, $valorPago3, $idPlan, $idConvenio, $num_factura,
				$cmbEntidad, $observacionesPago, $estado_pago, $arr_pagos_detalle = array(), $arr_medios_pago = array(),
				$idLugarCita = "", $idUsuarioProf = "", $id_tercero = "", $num_pedido = "", $arr_autorizaciones = array(), 
				$num_mipress="",$num_ent_mipress="",$num_poliza="") {
			try {
				//Se obtiene un número de semilla
				$semilla = mt_rand(1, 1000000);
				
				//Se limpia el temporal de detalle de pagos
				$this->borrar_temporal_pagos_detalle_pagos($idUsuarioCrea, $semilla);
				
				$resultado_aux = 0;
				for ($i = 0; $i < count($arr_pagos_detalle); $i++) {
					$detalle_aux = $arr_pagos_detalle[$i];
					
					$resultado_aux = $this->crearTemporalPagosDetallePagos2($idAdmision, $detalle_aux["id_detalle_precio"], $detalle_aux["cod_servicio"],
							$detalle_aux["tipo_bilateral"], $detalle_aux["num_autorizacion"], $detalle_aux["cantidad"], $detalle_aux["valor"],
							$detalle_aux["valor_cuota"], $detalle_aux["tipo_precio"], $idUsuarioCrea, $semilla);
					
					$resultado_aux = intval($resultado_aux, 10);
					if ($resultado_aux <= 0) {
						break;
					}
				}
				
				if ($resultado_aux > 0 && count($arr_pagos_detalle) > 0) {
					//Se limpia la tabla temporal de medios de pago
					$sql = "DELETE FROM temporal_pagos_det_medios
							WHERE id_usuario=".$idUsuarioCrea;
					
					$arrResultado = $this->ejecutarSentencia($sql, array());
					
					//Se limpia la tabla temporal de pago-anticipos
					$sql = "DELETE FROM temporal_pagos_anticipos
							WHERE id_usuario=".$idUsuarioCrea;
					
					$arrResultado = $this->ejecutarSentencia($sql, array());
					
					//Se agregan los medios de pago a la tabla temporal
					if (count($arr_medios_pago) > 0) {
						foreach ($arr_medios_pago as $medio_pago_aux) {
							if ($medio_pago_aux["BancoPago"] == "") {
								$medio_pago_aux["BancoPago"] = "NULL";
							}
							if ($medio_pago_aux["IdUsuarioAutoriza"] == "") {
								$medio_pago_aux["IdUsuarioAutoriza"] = "NULL";
							}
							if ($medio_pago_aux["numCheque"] == "") {
								$medio_pago_aux["numCheque"] = "NULL";
							} else {
								$medio_pago_aux["numCheque"] = "'".$medio_pago_aux["numCheque"]."'";
							}
							if ($medio_pago_aux["numCuenta"] == "") {
								$medio_pago_aux["numCuenta"] = "NULL";
							} else {
								$medio_pago_aux["numCuenta"] = "'".$medio_pago_aux["numCuenta"]."'";
							}
							if ($medio_pago_aux["codSeguridad"] == "") {
								$medio_pago_aux["codSeguridad"] = "NULL";
							} else {
								$medio_pago_aux["codSeguridad"] = "'".$medio_pago_aux["codSeguridad"]."'";
							}
							if ($medio_pago_aux["numAutoriza"] == "") {
								$medio_pago_aux["numAutoriza"] = "NULL";
							} else {
								$medio_pago_aux["numAutoriza"] = "'".$medio_pago_aux["numAutoriza"]."'";
							}
							if ($medio_pago_aux["anoVence"] == "") {
								$medio_pago_aux["anoVence"] = "NULL";
							} else {
								$medio_pago_aux["anoVence"] = "'".$medio_pago_aux["anoVence"]."'";
							}
							if ($medio_pago_aux["mesVence"] == "") {
								$medio_pago_aux["mesVence"] = "NULL";
							} else {
								$medio_pago_aux["mesVence"] = "'".$medio_pago_aux["mesVence"]."'";
							}
							if ($medio_pago_aux["referencia"] == "") {
								$medio_pago_aux["referencia"] = "NULL";
							} else {
								$medio_pago_aux["referencia"] = "'".$medio_pago_aux["referencia"]."'";
							}
							if ($medio_pago_aux["fechaConsigna"] == "") {
								$medio_pago_aux["fechaConsigna"] = "NULL";
							} else {
								$medio_pago_aux["fechaConsigna"] = "STR_TO_DATE('".$medio_pago_aux["fechaConsigna"]."', '%d/%m/%Y')";
							}
							if ($medio_pago_aux["idFranquiciaTC"] == "") {
								$medio_pago_aux["idFranquiciaTC"] = "NULL";
							}
							$sql = "INSERT INTO temporal_pagos_det_medios
									(id_usuario, id_medio_pago, id_banco, valor_pago, id_usuario_autoriza, num_cheque, num_cuenta,
									cod_seguridad, num_autoriza, ano_vence, mes_vence, referencia, fecha_consigna, id_franquicia_tc)
									VALUES (".$idUsuarioCrea.", ".$medio_pago_aux["tipoPago"].", ".$medio_pago_aux["BancoPago"].", ".
									$medio_pago_aux["valorPago"].", ".$medio_pago_aux["IdUsuarioAutoriza"].", ".$medio_pago_aux["numCheque"].", ".
									$medio_pago_aux["numCuenta"].", ".$medio_pago_aux["codSeguridad"].", ".$medio_pago_aux["numAutoriza"].", ".
									$medio_pago_aux["anoVence"].", ".$medio_pago_aux["mesVence"].", ".$medio_pago_aux["referencia"].", ".
									$medio_pago_aux["fechaConsigna"].", ".$medio_pago_aux["idFranquiciaTC"].")";
							
							
							$arrResultado = $this->ejecutarSentencia($sql, array());
							
							//Se verifica si hay anticipos relacionados
							$cant_anticipos_aux = intval($medio_pago_aux["cant_anticipos"], 10);
							if ($cant_anticipos_aux > 0) {
								$arr_anticipos_aux = $medio_pago_aux["lista_anticipos"];
								foreach ($arr_anticipos_aux as $anticipo_aux) {
									$sql = "INSERT INTO temporal_pagos_anticipos
											(id_usuario, id_anticipo, valor)
											VALUES (".$idUsuarioCrea.", ".$anticipo_aux["id_anticipo"].", ".$anticipo_aux["valor"].")";
									
									$arrResultado = $this->ejecutarSentencia($sql, array());
								}
							}
						}
					}
					
					//Se limpia la tabla temporal de autorizaciones
					$sql = "DELETE FROM temporal_autorizaciones
							WHERE id_usuario=".$idUsuarioCrea;
			
					$arrResultado = $this->ejecutarSentencia($sql, array());
					$id_autorizacion="";
					//Se agregan las autorizaciones a la tabla temporal
					if (count($arr_autorizaciones) > 0) {
						foreach ($arr_autorizaciones as $autorizacion_aux) {
							$id_autorizacion=$autorizacion_aux;
							$sql = "INSERT INTO temporal_autorizaciones
									(id_usuario, id_auto, ind_estado_auto)
									VALUES (".$idUsuarioCrea.", ".$autorizacion_aux.", 2)";
							
							$arrResultado = $this->ejecutarSentencia($sql, array());
						}
					}
					
					if ($idPago == "") {
						$idPago = "NULL";
					}
					if ($idAdmision == "") {
						$idAdmision = "NULL";
					}
					if ($idLugarCita == "") {
						$idLugarCita = "NULL";
					}
					if ($idUsuarioProf == "") {
						$idUsuarioProf = "NULL";
					}
					if ($observacionesPago == "") {
						$observacionesPago = "NULL";
					} else {
						$observacionesPago = "'".$observacionesPago."'";
					}
					if ($num_factura == "") {
						$num_factura = "NULL";
					} else {
						$num_factura = "'".$num_factura."'";
					}
					if ($cmbEntidad == "") {
						$cmbEntidad = "NULL";
					}
					if ($id_tercero == "") {
						$id_tercero = "NULL";
					}
					if ($num_pedido == "") {
						$num_pedido = "NULL";
					} else {
						$num_pedido = "'".$num_pedido."'";
					}
					if ($id_autorizacion == "") {
						$id_autorizacion = "NULL";
					} else {
						$id_autorizacion = "'".$id_autorizacion."'";
					}
					if ($num_mipress == "") {
						$num_mipress = "NULL";
					} else {
						$num_mipress = "'".$num_mipress."'";
					}
					if ($num_ent_mipress == "") {
						$num_ent_mipress = "NULL";
					} else {
						$num_ent_mipress = "'".$num_ent_mipress."'";
					}
					if ($num_poliza == "") {
						$num_poliza = "NULL";
					} else {
						$num_poliza = "'".$num_poliza."'";
					}
										
					$sql = "CALL pa_pagos(".$idPago.", ".$idAdmision.", ".$idPaciente.", ".$idLugarCita.", ".$idUsuarioProf.", ".$idConvenio.", ".$idPlan.", 
					".$num_factura.", ".$cmbEntidad.", ".$observacionesPago.", ".$estado_pago.", ".$id_tercero.", ".$num_pedido.",".$num_mipress.",
					".$num_ent_mipress.",".$num_poliza.", ".$id_autorizacion.", ".$idUsuarioCrea.", ".$semilla.", @id)";
					
					$arrCampos[0] = "@id";
					$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
					$resultado_out = $arrResultado["@id"];
					
					return $resultado_out;
				} else {
					return -4;
				}
			} catch (Exception $e) {
				return -2;
			}
		}
		
		/* Funcion que guarda los precios en la tabla temporal_pagos_detalle */
		public function borrar_pago($id_pago, $id_usuario, $observaciones_pago = "", $id_causal_borra = "NULL", $num_nota_credito = "") {
			try {
				if ($num_nota_credito == "") {
					$num_nota_credito = "NULL";
				} else {
					$num_nota_credito = "'".$num_nota_credito."'";
				}
				
				$sql = "CALL pa_borrar_pago(".$id_pago.", '".$observaciones_pago."', ".$id_causal_borra.", ".$num_nota_credito.", ".$id_usuario.", @id)";
				//echo($sql."<br />");
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado_out = $arrResultado["@id"];
	
				return $resultado_out;
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function reporteTesoseriaProcedimientos($fechaInicial, $fechaFinal, $id_convenio, $cod_insumo, $tipo_precio, $idUsuario, $idUsuarioAdm = "") {
				return $this->reporteTesoseriaProcedimientosPlanes($fechaInicial, $fechaFinal, $id_convenio, "", "", $cod_insumo, $tipo_precio, $idUsuario, $idUsuarioAdm);
		}
		
		public function reporteTesoseriaProcedimientosPlanes($fechaInicial, $fechaFinal, $id_convenio, $id_plan, $id_lugar_cita, $cod_insumo, $tipo_precio, $idUsuario, $idUsuarioAdm = "") {
			try {
				$sql = "";
				if ($tipo_precio == "" || $tipo_precio == "P") {
					//Procedimientos
					$sql = "SELECT PD.cod_procedimiento AS cod_insumo, MD.nombre_procedimiento AS nombre_insumo, PD.tipo_precio
							FROM pagos_detalle PD
							INNER JOIN pagos P ON PD.id_pago=P.id_pago
							INNER JOIN maestro_procedimientos MP ON MP.cod_procedimiento=PD.cod_procedimiento
							LEFT JOIN maestro_procedimientos_det MD ON MP.cod_procedimiento=MD.cod_procedimiento AND P.fecha_crea BETWEEN MD.fecha_ini AND IFNULL(MD.fecha_fin, P.fecha_crea)
							INNER JOIN pacientes PC ON PC.id_paciente=P.id_paciente
							LEFT JOIN admisiones A ON P.id_admision=A.id_admision
							WHERE P.fecha_pago BETWEEN '".$fechaInicial."' AND '".$fechaFinal." 23:59:59'
							AND P.estado_pago=2 ";
					if ($id_convenio != "") {
						$sql .= "AND P.id_convenio=".$id_convenio." ";
					}
					if ($id_plan != "") {
						$sql .= "AND P.id_plan=".$id_plan." ";
					}
					if ($id_lugar_cita != "") {
						$sql .= "AND P.id_lugar_cita=".$id_lugar_cita." ";
					}
					if ($cod_insumo != "") {
						$sql .= "AND MP.cod_procedimiento='".$cod_insumo."' ";
					}
					if ($idUsuarioAdm != '') {
						$sql .= "AND IFNULL(A.id_usuario_crea, P.id_usuario_crea)=".$idUsuarioAdm." ";
					}
					if ($idUsuario != '') {
						$sql .= "AND P.id_usuario_pago=".$idUsuario." ";
					}
					$sql .= "GROUP BY PD.cod_procedimiento, MP.nombre_procedimiento ";
				}
				
				if ($tipo_precio == "" || $tipo_precio == "M") {
					//Medicamentos
					if ($sql != "") {
						$sql .= "UNION ALL ";
					}
					$sql .= "SELECT PD.cod_medicamento AS cod_insumo, CONCAT(MM.nombre_generico, ' - ', MM.nombre_comercial) AS nombre_insumo, PD.tipo_precio
							FROM pagos_detalle PD
							INNER JOIN pagos P ON PD.id_pago=P.id_pago
							INNER JOIN maestro_medicamentos MM ON MM.cod_medicamento=PD.cod_medicamento
							INNER JOIN pacientes PC ON PC.id_paciente=P.id_paciente
							LEFT JOIN admisiones A ON P.id_admision=A.id_admision
							WHERE P.fecha_pago BETWEEN '".$fechaInicial."' AND '".$fechaFinal." 23:59:59'
							AND P.estado_pago=2 ";
					if ($id_convenio != "") {
						$sql .= "AND P.id_convenio=".$id_convenio." ";
					}
					if ($id_plan != "") {
						$sql .= "AND P.id_plan=".$id_plan." ";
					}
					if ($id_lugar_cita != "") {
						$sql .= "AND P.id_lugar_cita=".$id_lugar_cita." ";
					}
					if ($cod_insumo != "") {
						$sql .= "AND MM.cod_medicamento=".$cod_insumo." ";
					}
					if ($idUsuarioAdm != '') {
						$sql .= "AND IFNULL(A.id_usuario_crea, P.id_usuario_crea)=".$idUsuarioAdm." ";
					}
					if ($idUsuario != '') {
						$sql .= "AND P.id_usuario_pago=".$idUsuario." ";
					}
					$sql .= "GROUP BY PD.cod_medicamento, CONCAT(MM.nombre_generico, ' - ', MM.nombre_comercial) ";
				}
				
				if ($tipo_precio == "" || $tipo_precio == "I") {
					//Insumos
					if ($sql != "") {
						$sql .= "UNION ALL ";
					}
					$sql .= "SELECT PD.cod_insumo, MI.nombre_insumo, PD.tipo_precio
							FROM pagos_detalle PD
							INNER JOIN pagos P ON PD.id_pago=P.id_pago
							INNER JOIN maestro_insumos MI ON MI.cod_insumo=PD.cod_insumo
							INNER JOIN pacientes PC ON PC.id_paciente=P.id_paciente
							LEFT JOIN admisiones A ON P.id_admision=A.id_admision
							WHERE P.fecha_pago BETWEEN '".$fechaInicial."' AND '".$fechaFinal." 23:59:59'
							AND P.estado_pago=2 ";
					if ($id_convenio != "") {
						$sql .= "AND P.id_convenio=".$id_convenio." ";
					}
					if ($id_plan != "") {
						$sql .= "AND P.id_plan=".$id_plan." ";
					}
					if ($id_lugar_cita != "") {
						$sql .= "AND P.id_lugar_cita=".$id_lugar_cita." ";
					}
					if ($cod_insumo != "") {
						$sql .= "AND MI.cod_insumo=".$cod_insumo." ";
					}
					if ($idUsuarioAdm != '') {
						$sql .= "AND IFNULL(A.id_usuario_crea, P.id_usuario_crea)=".$idUsuarioAdm." ";
					}
					if ($idUsuario != '') {
						$sql .= "AND P.id_usuario_pago=".$idUsuario." ";
					}
					$sql .= "GROUP BY PD.cod_insumo, MI.nombre_insumo ";
				}
				
				$sql .= "ORDER BY nombre_insumo";
				//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function reporteTesoseriaProcedimientosPacientes($fechaInicial, $fechaFinal, $id_convenio, $cod_insumo, $tipo_pago, $idUsuario, $idUsuarioAdm = "") {
			return reporteTesoseriaProcedimientosPacientesPlanes($fechaInicial, $fechaFinal, $id_convenio, "", "", $cod_insumo, $tipo_pago, $idUsuario, $idUsuarioAdm);
		}
		
		public function reporteTesoseriaProcedimientosPacientesPlanes($fechaInicial, $fechaFinal, $id_convenio, $id_plan, $id_lugar_cita, $cod_insumo, $tipo_pago, $idUsuario, $idUsuarioAdm = "") {
			try {
				$sql = "SELECT PA.id_pago, PDA.cod_insumo, PDA.nombre_insumo, PAS.nombre_1, PAS.nombre_2, PAS.apellido_1, PAS.apellido_2,
						PDA.tipo_precio, CA.nombre_convenio, PL.nombre_plan, PAS.id_paciente, LD.nombre_detalle AS tipo_documento_aux, PAS.numero_documento,
						PDA.id_admision, PDA.valor, PDA.valor_cuota, PDA.cantidad, DM.id_medio_pago, TP.nombre AS f_pago, TP.id AS t_pago,
						DM.valor_pago, PA.num_factura, PA.entidad, EN.nombre_detalle AS nombre_entidad, PL.ind_tipo_pago, PDA.valor_b,
						PDA.valor_cuota_b, PA.observaciones_pago, IFNULL(TD.total_pago, 0) AS total_pago, IFNULL(TD.total_cuota, 0) AS total_cuota,
						IFNULL(T.ind_boleta, 0) AS ind_boleta, DATE_FORMAT(PA.fecha_pago, '%d/%m/%Y') AS fecha_pago_t,
						DT.nombre_detalle AS tipo_documento_tercero, TR.numero_documento AS numero_documento_tercero, TR.numero_verificacion, TR.nombre_tercero
						FROM (";
				
				$bol_hallado = false;
				//Procedimientos
				if ($tipo_pago == "" || $tipo_pago == "P") {
					$sql .= "SELECT PD.cod_procedimiento AS cod_insumo, MD.nombre_procedimiento AS nombre_insumo,
							PD.id_pago, PD.tipo_precio, PD.id_admision, PD.valor, PD.valor_cuota, PD.cantidad,
							LP.valor AS valor_b, LP.valor_cuota AS valor_cuota_b
							FROM pagos_detalle PD
							INNER JOIN pagos PA ON PD.id_pago=PA.id_pago
							INNER JOIN maestro_procedimientos MP ON PD.cod_procedimiento=MP.cod_procedimiento
							LEFT JOIN maestro_procedimientos_det MD ON MP.cod_procedimiento=MD.cod_procedimiento AND PA.fecha_crea BETWEEN MD.fecha_ini AND IFNULL(MD.fecha_fin, PA.fecha_crea)
							LEFT JOIN admisiones A ON PA.id_admision=A.id_admision
							LEFT JOIN listas_precios LP ON PA.id_plan=LP.id_plan AND PD.cod_procedimiento=LP.cod_procedimiento
							AND PD.tipo_bilateral=LP.tipo_bilateral
							AND LP.fecha_ini<=DATE(PA.fecha_pago) AND IFNULL(LP.fecha_fin, DATE(PA.fecha_pago))>=DATE(PA.fecha_pago)
							WHERE PA.fecha_pago BETWEEN '".$fechaInicial."' AND '".$fechaFinal." 23:59:59'
							AND PA.estado_pago=2 ";
					if ($id_convenio != "") {
						$sql .= "AND PA.id_convenio=".$id_convenio." ";
					}
					if ($id_plan != "") {
						$sql .= "AND PA.id_plan=".$id_plan." ";
					}
					if ($id_lugar_cita != "") {
						$sql .= "AND PA.id_lugar_cita=".$id_lugar_cita." ";
					}
					if ($cod_insumo != "") {
						$sql .= "AND MP.cod_procedimiento='".$cod_insumo."' ";
					}
					if ($idUsuarioAdm != "") {
						$sql .= "AND IFNULL(A.id_usuario_crea, PA.id_usuario_crea)=".$idUsuarioAdm." ";
					}
					if ($idUsuario != "") {
						$sql .= "AND PA.id_usuario_pago=".$idUsuario." ";
					}
					
					$bol_hallado = true;
				}
				
				//Medicamentos
				if ($tipo_pago == "" || $tipo_pago == "M") {
					if ($bol_hallado) {
						$sql .= "UNION ALL ";
					}
					$sql .= "SELECT PD.cod_medicamento AS cod_insumo, CONCAT(MM.nombre_generico, ' - ', MM.nombre_comercial) AS nombre_insumo,
							PD.id_pago, PD.tipo_precio, PD.id_admision, PD.valor, PD.valor_cuota, PD.cantidad,
							LP.valor AS valor_b, LP.valor_cuota AS valor_cuota_b
							FROM pagos_detalle PD
							INNER JOIN pagos PA ON PD.id_pago=PA.id_pago
							INNER JOIN maestro_medicamentos MM ON PD.cod_medicamento=MM.cod_medicamento
							LEFT JOIN admisiones A ON PA.id_admision=A.id_admision
							LEFT JOIN listas_precios LP ON PA.id_plan=LP.id_plan AND PD.cod_medicamento=LP.cod_medicamento
							AND PD.tipo_bilateral=LP.tipo_bilateral
							AND LP.fecha_ini<=DATE(PA.fecha_pago) AND IFNULL(LP.fecha_fin, DATE(PA.fecha_pago))>=DATE(PA.fecha_pago)
							WHERE PA.fecha_pago BETWEEN '".$fechaInicial."' AND '".$fechaFinal." 23:59:59'
							AND PA.estado_pago=2 ";
					if ($id_convenio != "") {
						$sql .= "AND PA.id_convenio=".$id_convenio." ";
					}
					if ($id_plan != "") {
						$sql .= "AND PA.id_plan=".$id_plan." ";
					}
					if ($id_lugar_cita != "") {
						$sql .= "AND PA.id_lugar_cita=".$id_lugar_cita." ";
					}
					if ($cod_insumo != "") {
						$sql .= "AND MM.cod_medicamento=".$cod_insumo." ";
					}
					if ($idUsuarioAdm != "") {
						$sql .= "AND IFNULL(A.id_usuario_crea, PA.id_usuario_crea)=".$idUsuarioAdm." ";
					}
					if ($idUsuario != "") {
						$sql .= "AND PA.id_usuario_pago=".$idUsuario." ";
					}
					
					$bol_hallado = true;
				}
				
				//Insumos
				if ($tipo_pago == "" || $tipo_pago == "I") {
					if ($bol_hallado) {
						$sql .= "UNION ALL ";
					}
					$sql .= "SELECT PD.cod_insumo, MI.nombre_insumo, PD.id_pago, PD.tipo_precio, PD.id_admision,
							PD.valor, PD.valor_cuota, PD.cantidad, LP.valor AS valor_b, LP.valor_cuota AS valor_cuota_b
							FROM pagos_detalle PD
							INNER JOIN pagos PA ON PD.id_pago=PA.id_pago
							INNER JOIN maestro_insumos MI ON PD.cod_insumo=MI.cod_insumo
							LEFT JOIN admisiones A ON PA.id_admision=A.id_admision
							LEFT JOIN listas_precios LP ON PA.id_plan=LP.id_plan AND PD.cod_insumo=LP.cod_insumo
							AND PD.tipo_bilateral=LP.tipo_bilateral
							AND LP.fecha_ini<=DATE(PA.fecha_pago) AND IFNULL(LP.fecha_fin, DATE(PA.fecha_pago))>=DATE(PA.fecha_pago)
							WHERE PA.fecha_pago BETWEEN '".$fechaInicial."' AND '".$fechaFinal." 23:59:59'
							AND PA.estado_pago=2 ";
					if ($id_convenio != "") {
						$sql .= "AND PA.id_convenio=".$id_convenio." ";
					}
					if ($id_plan != "") {
						$sql .= "AND PA.id_plan=".$id_plan." ";
					}
					if ($id_lugar_cita != "") {
						$sql .= "AND PA.id_lugar_cita=".$id_lugar_cita." ";
					}
					if ($cod_insumo != "") {
						$sql .= "AND MI.cod_insumo=".$cod_insumo." ";
					}
					if ($idUsuarioAdm != "") {
						$sql .= "AND IFNULL(A.id_usuario_crea, PA.id_usuario_crea)=".$idUsuarioAdm." ";
					}
					if ($idUsuario != "") {
						$sql .= "AND PA.id_usuario_pago=".$idUsuario." ";
					}
				}
				
				$sql .= ") PDA
						INNER JOIN pagos PA ON PDA.id_pago=PA.id_pago
						INNER JOIN pacientes PAS ON PA.id_paciente=PAS.id_paciente
						INNER JOIN convenios CA ON PA.id_convenio=CA.id_convenio
						INNER JOIN planes PL ON PA.id_plan=PL.id_plan
						INNER JOIN (
							SELECT PD.id_pago, SUM(PD.valor * PD.cantidad) AS total_pago, SUM(PD.valor_cuota * PD.cantidad) AS total_cuota
							FROM pagos PA
							INNER JOIN pagos_detalle PD ON PA.id_pago=PD.id_pago
							WHERE PA.fecha_pago BETWEEN '".$fechaInicial."' AND '".$fechaFinal." 23:59:59'
							AND PA.estado_pago=2
							GROUP BY PD.id_pago
						) TD ON PA.id_pago=TD.id_pago
						LEFT JOIN listas_detalle LD ON LD.id_detalle=PAS.id_tipo_documento
						LEFT JOIN listas_detalle EN ON PA.entidad=EN.id_detalle
						LEFT JOIN pagos_det_medios DM ON PA.id_pago=DM.id_pago
						LEFT JOIN tipos_pago TP ON DM.id_medio_pago=TP.id
						LEFT JOIN (
							SELECT PA.id_pago, SUM(CASE DM.id_medio_pago WHEN 0 THEN 1 ELSE 0 END) AS ind_boleta
							FROM pagos PA
							LEFT JOIN pagos_det_medios DM ON PA.id_pago=DM.id_pago
							WHERE PA.fecha_pago BETWEEN '".$fechaInicial."' AND '".$fechaFinal." 23:59:59'
							GROUP BY PA.id_pago
						) T ON PA.id_pago=T.id_pago
						LEFT JOIN terceros TR ON PA.id_tercero=TR.id_tercero
						LEFT JOIN listas_detalle DT ON TR.id_tipo_documento=DT.id_detalle
						ORDER BY DATE(PA.fecha_pago), PAS.apellido_1, PAS.apellido_2, PAS.nombre_1, PAS.nombre_2, PDA.nombre_insumo";
				//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Funcion para el reporte de tesorería estadistico por paciente
		public function reporteTesoseriaEstadisticoPaciente($fechaInicial, $fechaFinal, $idPaciente) {
			try {
				$sql = "SELECT DATE_FORMAT(PA.fecha_pago , '%d/%m/%Y') AS fecha_pago_aux, PDA.cod_insumo, PDA.nombre_insumo,
						PAS.nombre_1, PAS.nombre_2, PAS.apellido_1, PAS.apellido_2, PDA.tipo_precio, CA.nombre_convenio, PAS.id_paciente,
						LD.nombre_detalle AS tipo_documento_aux, PAS.numero_documento, PDA.id_admision, PDA.valor, PDA.cantidad, TP.nombre AS f_pago,
						TP2.nombre AS f_pago2, TP3.nombre AS f_pago3, PA.num_factura
						FROM (
							SELECT PD.id_pago, PD.cod_procedimiento AS cod_insumo, MD.nombre_procedimiento AS nombre_insumo,
							PD.tipo_precio, PD.id_admision, PD.valor, PD.cantidad
							FROM pagos_detalle PD
							INNER JOIN pagos PA ON PA.id_pago=PD.id_pago
							INNER JOIN maestro_procedimientos MP ON MP.cod_procedimiento=PD.cod_procedimiento
							LEFT JOIN maestro_procedimientos_det MD ON MP.cod_procedimiento=MD.cod_procedimiento AND PD.fecha_crea BETWEEN MD.fecha_ini AND IFNULL(MD.fecha_fin, PD.fecha_crea)
							WHERE DATE(PA.fecha_pago) BETWEEN '".$fechaInicial."' AND '".$fechaFinal."'
							AND PA.estado_pago=2
							AND PA.id_paciente=".$idPaciente."
							UNION ALL
							SELECT PD.id_pago, PD.cod_medicamento, CONCAT(MM.nombre_generico, ' - ', MM.nombre_comercial),
							PD.tipo_precio, PD.id_admision, PD.valor, PD.cantidad
							FROM pagos_detalle PD
							INNER JOIN pagos PA ON PA.id_pago=PD.id_pago
							INNER JOIN maestro_medicamentos MM ON MM.cod_medicamento=PD.cod_medicamento
							WHERE DATE(PA.fecha_pago) BETWEEN '".$fechaInicial."' AND '".$fechaFinal."'
							AND PA.estado_pago=2
							AND PA.id_paciente=".$idPaciente."
							UNION ALL
							SELECT PD.id_pago, PD.cod_insumo, MI.nombre_insumo, PD.tipo_precio, PD.id_admision, PD.valor, PD.cantidad
							FROM pagos_detalle PD
							INNER JOIN pagos PA ON PA.id_pago=PD.id_pago
							INNER JOIN maestro_insumos MI ON MI.cod_insumo=PD.cod_insumo
							WHERE DATE(PA.fecha_pago) BETWEEN '".$fechaInicial."' AND '".$fechaFinal."'
							AND PA.estado_pago=2
							AND PA.id_paciente=".$idPaciente."
						) PDA
						INNER JOIN pagos PA ON PA.id_pago=PDA.id_pago
						INNER JOIN pacientes PAS ON PAS.id_paciente=PA.id_paciente
						INNER JOIN convenios CA ON CA.id_convenio=PA.id_convenio
						INNER JOIN listas_detalle LD ON LD.id_detalle=PAS.id_tipo_documento
						LEFT JOIN tipos_pago TP ON TP.id=PA.medio_pago_1
						LEFT JOIN tipos_pago TP2 ON TP2.id=PA.medio_pago_2
						LEFT JOIN tipos_pago TP3 ON TP3.id=PA.medio_pago_3
						ORDER BY PA.fecha_pago, PDA.cod_insumo, PDA.nombre_insumo";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Funcion para el reporte de auditoría de tesorería
		public function getListaReporteTesoseriaAuditoria($fecha_ini, $fecha_fin, $id_convenio, $estado_pago) {
			try {
				$sql = "SELECT P.*, DATE_FORMAT(P.fecha_pago, '%d/%m/%Y') AS fecha_pago_t, PA.nombre_1, PA.nombre_2, PA.apellido_1,
						PA.apellido_2, PA.id_tipo_documento, TD.nombre_detalle AS tipo_documento, PA.numero_documento, PD.tipo_precio,
						PD.cod_insumo, PD.nombre_insumo, TP.nombre AS nombre_pago, PD.valor, PD.valor_cuota, PD.cantidad, PD.valor_base,
						PD.valor_cuota_base, TT.total_pago, TT.total_cuota, DM.valor_pago, DM.id_medio_pago,
						IFNULL(T.ind_boleta, 0) AS ind_boleta, CONCAT(UA.nombre_usuario, ' ', UA.apellido_usuario) AS nombre_completo_crea,
						CONCAT(UM.nombre_usuario, ' ', UM.apellido_usuario) AS nombre_completo_mod, C.nombre_convenio, PL.nombre_plan,
						CONCAT(UP.nombre_usuario, ' ', UP.apellido_usuario) AS nombre_completo_pago, DT.nombre_detalle AS tipo_documento_tercero,
						TR.numero_documento AS numero_documento_tercero, TR.numero_verificacion, TR.nombre_tercero,
						IFNULL(TP.ind_negativo, 0) AS ind_negativo
						FROM (
							SELECT P.id_pago, PD.cod_procedimiento AS cod_insumo, MD.nombre_procedimiento AS nombre_insumo,
							PD.tipo_precio, PD.valor, PD.valor_cuota, PD.cantidad, LP.valor AS valor_base, LP.valor_cuota AS valor_cuota_base
							FROM pagos P
							INNER JOIN pagos_detalle PD ON P.id_pago=PD.id_pago
							INNER JOIN maestro_procedimientos MP ON PD.cod_procedimiento=MP.cod_procedimiento
							LEFT JOIN maestro_procedimientos_det MD ON MP.cod_procedimiento=MD.cod_procedimiento AND P.fecha_crea BETWEEN MD.fecha_ini AND IFNULL(MD.fecha_fin, P.fecha_crea)
							LEFT JOIN listas_precios LP ON P.id_plan=LP.id_plan AND PD.cod_procedimiento=LP.cod_procedimiento AND PD.tipo_bilateral=LP.tipo_bilateral
							AND DATE(P.fecha_pago) BETWEEN LP.fecha_ini AND IFNULL(LP.fecha_fin, DATE(P.fecha_pago))
							WHERE DATE(P.fecha_pago) BETWEEN '".$fecha_ini."' AND '".$fecha_fin."'
							AND P.estado_pago=".$estado_pago." ";
				if ($id_convenio != "" && $id_convenio > 0) {
					$sql .= "AND P.id_convenio=".$id_convenio." ";
				}
				$sql .=
							"UNION ALL
							SELECT P.id_pago, PD.cod_medicamento AS cod_insumo, MM.nombre_comercial AS nombre_insumo,
							PD.tipo_precio, PD.valor, PD.valor_cuota, PD.cantidad, LP.valor AS valor_base, LP.valor_cuota AS valor_cuota_base
							FROM pagos P
							INNER JOIN pagos_detalle PD ON P.id_pago=PD.id_pago
							INNER JOIN maestro_medicamentos MM ON PD.cod_medicamento=MM.cod_medicamento
							LEFT JOIN listas_precios LP ON P.id_plan=LP.id_plan AND PD.cod_medicamento=LP.cod_medicamento AND PD.tipo_bilateral=LP.tipo_bilateral
							AND DATE(P.fecha_pago) BETWEEN LP.fecha_ini AND IFNULL(LP.fecha_fin, DATE(P.fecha_pago))
							WHERE DATE(P.fecha_pago) BETWEEN '".$fecha_ini."' AND '".$fecha_fin."'
							AND P.estado_pago=".$estado_pago." ";
				if ($id_convenio != "" && $id_convenio > 0) {
					$sql .= "AND P.id_convenio=".$id_convenio." ";
				}
				$sql .=
							"UNION ALL
							SELECT P.id_pago, PD.cod_insumo, MI.nombre_insumo,
							PD.tipo_precio, PD.valor, PD.valor_cuota, PD.cantidad, LP.valor AS valor_base, LP.valor_cuota AS valor_cuota_base
							FROM pagos P
							INNER JOIN pagos_detalle PD ON P.id_pago=PD.id_pago
							INNER JOIN maestro_insumos MI ON PD.cod_insumo=MI.cod_insumo
							LEFT JOIN listas_precios LP ON P.id_plan=LP.id_plan AND PD.cod_insumo=LP.cod_insumo AND PD.tipo_bilateral=LP.tipo_bilateral
							AND DATE(P.fecha_pago) BETWEEN LP.fecha_ini AND IFNULL(LP.fecha_fin, DATE(P.fecha_pago))
							WHERE DATE(P.fecha_pago) BETWEEN '".$fecha_ini."' AND '".$fecha_fin."'
							AND P.estado_pago=".$estado_pago." ";
				if ($id_convenio != "" && $id_convenio > 0) {
					$sql .= "AND P.id_convenio=".$id_convenio." ";
				}
				$sql .= ") PD
						INNER JOIN pagos P ON PD.id_pago=P.id_pago
						INNER JOIN pacientes PA ON P.id_paciente=PA.id_paciente
						LEFT JOIN listas_detalle TD ON PA.id_tipo_documento=TD.id_detalle
						INNER JOIN (
							SELECT PD.id_pago, SUM(PD.valor * PD.cantidad) AS total_pago, SUM(PD.valor_cuota * PD.cantidad) AS total_cuota
							FROM pagos PA
							INNER JOIN pagos_detalle PD ON PA.id_pago=PD.id_pago
							WHERE DATE(PA.fecha_pago) BETWEEN '".$fecha_ini."' AND '".$fecha_fin."'
							AND PA.estado_pago=".$estado_pago."
							GROUP BY PD.id_pago
						) TT ON P.id_pago=TT.id_pago
						LEFT JOIN pagos_det_medios DM ON P.id_pago=DM.id_pago
						LEFT JOIN (
							SELECT PA.id_pago, SUM(CASE DM.id_medio_pago WHEN 0 THEN 1 ELSE 0 END) AS ind_boleta
							FROM pagos PA
							LEFT JOIN pagos_det_medios DM ON PA.id_pago=DM.id_pago
							WHERE DATE(PA.fecha_pago) BETWEEN '".$fecha_ini."' AND '".$fecha_fin."'
							AND PA.estado_pago=".$estado_pago."
							GROUP BY PA.id_pago
						) T ON P.id_pago=T.id_pago
						LEFT JOIN tipos_pago TP ON DM.id_medio_pago=TP.id
						INNER JOIN usuarios UA ON P.id_usuario_crea=UA.id_usuario
						LEFT JOIN usuarios UM ON P.id_usuario_mod=UM.id_usuario
						LEFT JOIN usuarios UP ON P.id_usuario_pago=UP.id_usuario
						INNER JOIN convenios C ON P.id_convenio=C.id_convenio
						INNER JOIN planes PL ON P.id_plan=PL.id_plan
						LEFT JOIN terceros TR ON P.id_tercero=TR.id_tercero
						LEFT JOIN listas_detalle DT ON TR.id_tipo_documento=DT.id_detalle
						ORDER BY P.fecha_pago, P.id_pago, PD.nombre_insumo";
				
				//echo($sql);
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Función que retorna los pagos de consultas de un convenio en un rango de fechas
		public function getListaPagosConsultasConvenioFechas($fecha_ini, $fecha_fin, $id_convenio) {
			try {
				$sql = "SELECT P.*, PA.nombre_1, PA.nombre_2, PA.apellido_1, PA.apellido_2, PA.id_tipo_documento,
						TD.codigo_detalle AS cod_tipo_documento, TD.nombre_detalle AS tipo_documento, PA.numero_documento,
						PA.fecha_nacimiento, DATE_FORMAT(PA.fecha_nacimiento, '%d/%m/%Y') AS fecha_nacimiento_t,
						PA.cod_dep_nac, PA.cod_mun_nac, PA.nom_dep_nac, PA.nom_mun_nac, DN.nom_dep AS nom_dep_n,
						MN.nom_mun AS nom_mun_n, fu_calcular_edad(PA.fecha_nacimiento, P.fecha_pago) AS edad,
						EC.nombre_detalle AS estado_civil, PA.profesion, A.nombre_acompa, PA.direccion,
						PA.cod_dep, PA.cod_mun, PA.nom_dep, PA.nom_mun, DR.nom_dep AS nom_dep_r, MR.nom_mun AS nom_mun_r,
						PA.telefono_1, PA.telefono_2, IFNULL(PD.num_autorizacion, A.num_autorizacion) AS num_autorizacion, A.num_carnet,
						DATE_FORMAT(P.fecha_pago, '%d/%m/%Y') AS fecha_pago_t, CX.nombre AS nombre_ciex
						FROM pagos P
						INNER JOIN pacientes PA ON P.id_paciente=PA.id_paciente
						LEFT JOIN admisiones A ON P.id_admision=A.id_admision
						LEFT JOIN listas_detalle TD ON PA.id_tipo_documento=TD.id_detalle
						LEFT JOIN (
							SELECT HC.id_admision, MAX(D.id_hc) AS id_hc
							FROM historia_clinica HC
							INNER JOIN diagnosticos_hc D ON HC.id_hc=D.id_hc
							GROUP BY HC.id_admision
						) HC ON P.id_admision=HC.id_admision
						LEFT JOIN diagnosticos_hc DX ON HC.id_hc=DX.id_hc AND DX.orden=1
						LEFT JOIN vi_ciex CX ON DX.cod_ciex=CX.codciex
						LEFT JOIN departamentos DN ON PA.cod_dep_nac=DN.cod_dep
						LEFT JOIN municipios MN ON PA.cod_mun_nac=MN.cod_mun_dane
						LEFT JOIN listas_detalle EC ON PA.id_estado_civil=EC.id_detalle
						LEFT JOIN departamentos DR ON PA.cod_dep=DR.cod_dep
						LEFT JOIN municipios MR ON PA.cod_mun=MR.cod_mun_dane
						LEFT JOIN (
							SELECT id_tipo_cita, MAX(cod_procedimiento) AS cod_procedimiento
							FROM tipos_citas_det
							WHERE ind_usuario_alt=0
							GROUP BY id_tipo_cita
						) CD ON A.id_tipo_cita=CD.id_tipo_cita
						LEFT JOIN pagos_detalle PD ON P.id_pago=PD.id_pago AND CD.cod_procedimiento=PD.cod_procedimiento
						WHERE P.id_convenio=".$id_convenio."
						AND P.fecha_pago BETWEEN STR_TO_DATE('".$fecha_ini."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_fin." 23:59:59', '%d/%m/%Y %H:%i:%s')
						AND P.estado_pago=2
						AND EXISTS (
							SELECT PD.id_pago
							FROM pagos_detalle PD
							INNER JOIN maestro_procedimientos MP ON PD.cod_procedimiento=MP.cod_procedimiento
							WHERE MP.tipo_procedimiento='C'
							AND PD.id_pago=P.id_pago
						)
						ORDER BY P.fecha_pago";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Función que retorna los pagos de consultas de un convenio en un rango de fechas
		public function getCantidadPagosConsultasAnteriores($id_convenio, $id_paciente, $fecha_pago) {
			try {
				$sql = "SELECT COUNT(*) AS cantidad
						FROM pagos P
						INNER JOIN pagos_detalle PD ON P.id_pago=PD.id_pago
						INNER JOIN maestro_procedimientos MP ON PD.cod_procedimiento=MP.cod_procedimiento
						WHERE P.id_convenio=".$id_convenio."
						AND P.id_paciente=".$id_paciente."
						AND MP.tipo_procedimiento='C'
						AND P.fecha_pago<'".$fecha_pago."'";
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Función que retorna los pagos de consultas de un convenio en un rango de fechas
		public function getListaPagosConsultasProcedimientos($fecha_ini, $fecha_fin, $id_convenio, $tipo_procedimiento) {
			try {
				$sql = "SELECT P.id_pago, PD.id_detalle_precio, A.id_admision, PA.id_tipo_documento, TD.nombre_detalle AS tipo_documento, PA.numero_documento,
						PA.nombre_1, PA.nombre_2, PA.apellido_1, PA.apellido_2, DATE_FORMAT(P.fecha_pago, '%d/%m/%Y') AS fecha_pago_t, P.id_plan,
						PL.nombre_plan, fu_calcular_edad(PA.fecha_nacimiento, DATE(P.fecha_pago)) AS edad, SX.codigo_detalle AS sexo,
						IFNULL(PD.num_autorizacion, A.num_autorizacion) AS num_autorizacion,
						PD.cod_procedimiento, PD.valor, IFNULL(PD.valor_cuota, 0) AS valor_cuota, PL.ind_tipo_pago, HC.id_hc, CX.id_hc AS id_hc_cx,
						HC.id_tipo_reg, TR.id_clase_reg
						FROM pagos P
						INNER JOIN pagos_detalle PD ON P.id_pago=PD.id_pago
						INNER JOIN planes PL ON P.id_plan=PL.id_plan
						INNER JOIN maestro_procedimientos MP ON PD.cod_procedimiento=MP.cod_procedimiento
						INNER JOIN pacientes PA ON P.id_paciente=PA.id_paciente
						LEFT JOIN admisiones A ON P.id_admision=A.id_admision
						LEFT JOIN listas_detalle TD ON PA.id_tipo_documento=TD.id_detalle
						LEFT JOIN listas_detalle SX ON PA.sexo=SX.id_detalle
						LEFT JOIN tipos_citas_det CD ON A.id_tipo_cita=CD.id_tipo_cita
						LEFT JOIN historia_clinica HC ON P.id_admision=HC.id_admision AND CD.id_tipo_reg=HC.id_tipo_reg
						LEFT JOIN cirugias CX ON P.id_admision=CX.id_admision_preqx
						LEFT JOIN tipos_registros_hc TR ON HC.id_tipo_reg=TR.id_tipo_reg
						WHERE P.id_convenio=".$id_convenio."
						AND P.fecha_pago BETWEEN '".$fecha_ini."' AND '".$fecha_fin." 23:59:59'
						AND P.estado_pago=2
						AND MP.tipo_procedimiento='".$tipo_procedimiento."'
						ORDER BY P.id_pago, PD.id_detalle_precio, HC.id_hc DESC";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Función que retorna los pagos de insumos de un convenio en un rango de fechas
		public function getListaPagosConsultasInsumos($fecha_ini, $fecha_fin, $id_convenio) {
			try {
				$sql = "SELECT P.id_pago, PD.id_detalle_precio, A.id_admision, PA.id_tipo_documento, TD.nombre_detalle AS tipo_documento, PA.numero_documento,
						PA.nombre_1, PA.nombre_2, PA.apellido_1, PA.apellido_2, DATE_FORMAT(P.fecha_pago, '%d/%m/%Y') AS fecha_pago_t, P.id_plan,
						PL.nombre_plan, fu_calcular_edad(PA.fecha_nacimiento, DATE(P.fecha_pago)) AS edad, SX.codigo_detalle AS sexo,
						IFNULL(PD.num_autorizacion, A.num_autorizacion) AS num_autorizacion, PD.cod_insumo, MI.nombre_insumo, PD.cantidad, PD.valor,
						IFNULL(PD.valor_cuota, 0) AS valor_cuota, PL.ind_tipo_pago
						FROM pagos P
						INNER JOIN pagos_detalle PD ON P.id_pago=PD.id_pago
						INNER JOIN planes PL ON P.id_plan=PL.id_plan
						INNER JOIN maestro_insumos MI ON PD.cod_insumo=MI.cod_insumo
						INNER JOIN pacientes PA ON P.id_paciente=PA.id_paciente
						LEFT JOIN admisiones A ON P.id_admision=A.id_admision
						LEFT JOIN listas_detalle TD ON PA.id_tipo_documento=TD.id_detalle
						LEFT JOIN listas_detalle SX ON PA.sexo=SX.id_detalle
						LEFT JOIN tipos_citas_det CD ON A.id_tipo_cita=CD.id_tipo_cita
						WHERE P.id_convenio=".$id_convenio."
						AND P.fecha_pago BETWEEN '".$fecha_ini."' AND '".$fecha_fin." 23:59:59'
						AND P.estado_pago=2
						ORDER BY P.id_pago, PD.id_detalle_precio";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/*
		Función que retorna los conceptos de pago asociados a un pago
		$tipo_orden
		1 - Por identificador de detalle
		2 - Por tipo de concepto de pago
		*/
		public function getListaPagosDetMedios($id_pago, $tipo_orden = 1) {
			try {
				$sql = "SELECT PD.*, TP.nombre AS nombre_tipo_pago, TP.abrev_pago, BN.codigo_detalle AS codigo_banco,
						BN.nombre_detalle AS nombre_banco, T.nombre_tercero, TP.ind_negativo, TP.id_tipo_concepto,
						TC.nombre_detalle AS tipo_concepto, DATE_FORMAT(PD.fecha_consigna, '%d/%m/%Y') AS fecha_consigna_t
						FROM pagos_det_medios PD
						INNER JOIN tipos_pago TP ON PD.id_medio_pago=TP.id
						LEFT JOIN listas_detalle BN ON PD.id_banco=BN.id_detalle
						LEFT JOIN terceros T ON PD.id_tercero=T.id_tercero
						LEFT JOIN listas_detalle TC ON TP.id_tipo_concepto=TC.id_detalle
						WHERE PD.id_pago=".$id_pago." ";
				switch ($tipo_orden) {
					case 1: //Por identificador de detalle
						$sql .= "ORDER BY PD.id_det_pago";
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
		
		//funcion para el reposte de tesorería
		public function reporteTesoseriaPaciente($fechaInicial, $fechaFinal, $idPaciente) {
			try {
				$sql = "SELECT PA.id_pago, PDA.cod_insumo, PDA.nombre_insumo, PAS.nombre_1, PAS.nombre_2, PAS.apellido_1, PAS.apellido_2,
						PDA.tipo_precio, CA.nombre_convenio, PL.nombre_plan, PAS.id_paciente, LD.nombre_detalle AS tipo_documento_aux, PAS.numero_documento,
						PDA.id_admision, PDA.valor, PDA.valor_cuota, PDA.cantidad, DM.id_medio_pago, TP.nombre AS f_pago, TP.id AS t_pago,
						DM.valor_pago, PA.num_factura, PA.entidad, EN.nombre_detalle AS nombre_entidad, PL.ind_tipo_pago, PDA.valor_b,
						PDA.valor_cuota_b, PA.observaciones_pago, IFNULL(TD.total_pago, 0) AS total_pago, IFNULL(TD.total_cuota, 0) AS total_cuota,
						IFNULL(T.ind_boleta, 0) AS ind_boleta, DATE_FORMAT(PA.fecha_pago, '%d/%m/%Y') AS fecha_pago_t
						FROM (
							SELECT PD.cod_procedimiento AS cod_insumo, MD.nombre_procedimiento AS nombre_insumo,
							PD.id_pago, PD.tipo_precio, PD.id_admision, PD.valor, PD.valor_cuota, PD.cantidad,
							LP.valor AS valor_b, LP.valor_cuota AS valor_cuota_b
							FROM pagos_detalle PD
							INNER JOIN pagos PA ON PD.id_pago=PA.id_pago
							INNER JOIN maestro_procedimientos MP ON PD.cod_procedimiento=MP.cod_procedimiento
							LEFT JOIN maestro_procedimientos_det MD ON MP.cod_procedimiento=MD.cod_procedimiento AND PA.fecha_crea BETWEEN MD.fecha_ini AND IFNULL(MD.fecha_fin, PA.fecha_crea)
							LEFT JOIN admisiones A ON PA.id_admision=A.id_admision
							LEFT JOIN listas_precios LP ON PA.id_plan=LP.id_plan AND PD.cod_procedimiento=LP.cod_procedimiento
							AND PD.tipo_bilateral=LP.tipo_bilateral
							AND LP.fecha_ini<=DATE(PA.fecha_pago) AND IFNULL(LP.fecha_fin, DATE(PA.fecha_pago))>=DATE(PA.fecha_pago)
							WHERE DATE(PA.fecha_pago) BETWEEN '".$fechaInicial."' AND '".$fechaFinal."'
							AND PA.estado_pago=2
							AND PA.id_paciente=".$idPaciente."
							
							UNION ALL
							
							SELECT PD.cod_medicamento AS cod_insumo, CONCAT(MM.nombre_generico, ' - ', MM.nombre_comercial) AS nombre_insumo,
							PD.id_pago, PD.tipo_precio, PD.id_admision, PD.valor, PD.valor_cuota, PD.cantidad,
							LP.valor AS valor_b, LP.valor_cuota AS valor_cuota_b
							FROM pagos_detalle PD
							INNER JOIN pagos PA ON PD.id_pago=PA.id_pago
							INNER JOIN maestro_medicamentos MM ON PD.cod_medicamento=MM.cod_medicamento
							LEFT JOIN admisiones A ON PA.id_admision=A.id_admision
							LEFT JOIN listas_precios LP ON PA.id_plan=LP.id_plan AND PD.cod_medicamento=LP.cod_medicamento
							AND PD.tipo_bilateral=LP.tipo_bilateral
							AND LP.fecha_ini<=DATE(PA.fecha_pago) AND IFNULL(LP.fecha_fin, DATE(PA.fecha_pago))>=DATE(PA.fecha_pago)
							WHERE DATE(PA.fecha_pago) BETWEEN '".$fechaInicial."' AND '".$fechaFinal."'
							AND PA.estado_pago=2
							AND PA.id_paciente=".$idPaciente."
							
							UNION ALL
							
							SELECT PD.cod_insumo, MI.nombre_insumo, PD.id_pago, PD.tipo_precio, PD.id_admision,
							PD.valor, PD.valor_cuota, PD.cantidad, LP.valor AS valor_b, LP.valor_cuota AS valor_cuota_b
							FROM pagos_detalle PD
							INNER JOIN pagos PA ON PD.id_pago=PA.id_pago
							INNER JOIN maestro_insumos MI ON PD.cod_insumo=MI.cod_insumo
							LEFT JOIN admisiones A ON PA.id_admision=A.id_admision
							LEFT JOIN listas_precios LP ON PA.id_plan=LP.id_plan AND PD.cod_insumo=LP.cod_insumo
							AND PD.tipo_bilateral=LP.tipo_bilateral
							AND LP.fecha_ini<=DATE(PA.fecha_pago) AND IFNULL(LP.fecha_fin, DATE(PA.fecha_pago))>=DATE(PA.fecha_pago)
							WHERE DATE(PA.fecha_pago) BETWEEN '".$fechaInicial."' AND '".$fechaFinal."'
							AND PA.estado_pago=2
							AND PA.id_paciente=".$idPaciente."
						) PDA
						INNER JOIN pagos PA ON PDA.id_pago=PA.id_pago
						INNER JOIN pacientes PAS ON PA.id_paciente=PAS.id_paciente
						INNER JOIN convenios CA ON PA.id_convenio=CA.id_convenio
						INNER JOIN planes PL ON PA.id_plan=PL.id_plan
						INNER JOIN (
							SELECT PD.id_pago, SUM(PD.valor * PD.cantidad) AS total_pago, SUM(PD.valor_cuota * PD.cantidad) AS total_cuota
							FROM pagos PA
							INNER JOIN pagos_detalle PD ON PA.id_pago=PD.id_pago
							WHERE DATE(PA.fecha_pago) BETWEEN '".$fechaInicial."' AND '".$fechaFinal."'
							AND PA.estado_pago=2
							GROUP BY PD.id_pago
						) TD ON PA.id_pago=TD.id_pago
						LEFT JOIN listas_detalle LD ON LD.id_detalle = PAS.id_tipo_documento
						LEFT JOIN listas_detalle EN ON PA.entidad=EN.id_detalle
						LEFT JOIN pagos_det_medios DM ON PA.id_pago=DM.id_pago
						LEFT JOIN tipos_pago TP ON DM.id_medio_pago=TP.id
						LEFT JOIN (
							SELECT PA.id_pago, SUM(CASE DM.id_medio_pago WHEN 0 THEN 1 ELSE 0 END) AS ind_boleta
							FROM pagos PA
							LEFT JOIN pagos_det_medios DM ON PA.id_pago=DM.id_pago
							WHERE DATE(PA.fecha_pago) BETWEEN '".$fechaInicial."' AND '".$fechaFinal."'
							GROUP BY PA.id_pago
						) T ON PA.id_pago=T.id_pago
						ORDER BY PDA.nombre_insumo, PA.fecha_pago";
				//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function crearTemporalPagosDetallePagos($id_admision, $id_detalle_precio, $cod_servicio, $tipo_bilateral,
				$cantidad, $valor, $valor_cuota, $tipo_precio, $id_usuario, $semilla) {
			return $this->crearTemporalPagosDetallePagos2($id_admision, $id_detalle_precio, $cod_servicio, $tipo_bilateral,
					"", $cantidad, $valor, $valor_cuota, $tipo_precio, $id_usuario, $semilla);
		}
		
		public function crearTemporalPagosDetallePagos2($id_admision, $id_detalle_precio, $cod_servicio, $tipo_bilateral,
				$num_autorizacion, $cantidad, $valor, $valor_cuota, $tipo_precio, $id_usuario, $semilla) {
			try {
				if ($id_admision == "") {
					$id_admision = "NULL";
				}
				if ($num_autorizacion != "") {
					$num_autorizacion = "'".$num_autorizacion."'";
				} else {
					$num_autorizacion = "NULL";
				}
				if ($valor_cuota == "") {
					$valor_cuota = "0";
				}
				$sql = "CALL pa_crear_editar_pagos_detalle(".$id_admision.", ".$id_detalle_precio.", '".$cod_servicio."', ".
						$tipo_bilateral.", ".$num_autorizacion.", ".$cantidad.", ".$valor.", ".$valor_cuota.", '".$tipo_precio."', ".$id_usuario.", ".$semilla.", @id)";
				//echo($sql);
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado_out = $arrResultado["@id"];
				
				return $resultado_out;
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function borrar_temporal_pagos_detalle_pagos($id_usuario, $semilla) {
			try {
				$sql = "DELETE FROM temporal_pagos_detalle_pagos
						WHERE id_usuario_crea=".$id_usuario."
						AND semilla=".$semilla;
				
				$arrResultado = $this->ejecutarSentencia($sql, array());
				
				return 1;
			} catch (Exception $e) {
				return -1;
			}
		}
		
		public function get_total_pago($id_admision) {
			try {
				$sql = "SELECT SUM(PD.cantidad*PD.valor) AS total
						FROM pagos P
						INNER JOIN pagos_detalle PD ON P.id_pago=PD.id_pago
						WHERE P.id_admision=".$id_admision."
						AND P.estado_pago=2";
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function reporteTesoseriaAtenciones($fechaInicial, $fechaFinal, $id_convenio, $id_plan, $cod_insumo, $tipo_precio, $usuario_atiende, $lugar_cita) {
			try {
				$sql = "";
				if ($tipo_precio == "" || $tipo_precio == "P") {
					//Procedimientos
					$sql = "SELECT P.id_pago, P.fecha_pago, CO.nombre_convenio, PL.nombre_plan, LD.nombre_detalle AS sede,
							CONCAT(UP.nombre_usuario, ' ', UP.apellido_usuario) AS usuario_registra_pago,
							CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS medico, PD.cod_procedimiento AS cod_insumo,
							MD.nombre_procedimiento AS nombre_insumo, PD.tipo_precio, PD.tipo_bilateral, PC.nombre_1, PC.nombre_2, PC.apellido_1, PC.apellido_2,
							CONCAT(PC.nombre_1, ' ', IFNULL(PC.nombre_2, ''), ' ', PC.apellido_1, ' ', IFNULL(PC.apellido_2, '')) AS nombre_completo,
							TD.nombre_detalle AS tipo_documento, PC.numero_documento, PC.ind_habeas_data, PD.cantidad, PD.valor, PD.valor_cuota,
							TC.nombre_tipo_cita, PL.ind_tipo_pago, P.num_factura, T.id_tipo_documento,
							DT.codigo_detalle AS cod_tipo_documento_tercero, DT.nombre_detalle AS tipo_documento_tercero,
							T.numero_documento AS numero_documento_tercero, T.numero_verificacion, T.nombre_tercero, A.id_admision
							FROM pagos_detalle PD
							INNER JOIN pagos P ON PD.id_pago=P.id_pago
							INNER JOIN maestro_procedimientos MP ON MP.cod_procedimiento=PD.cod_procedimiento
							LEFT JOIN maestro_procedimientos_det MD ON MP.cod_procedimiento=MD.cod_procedimiento AND P.fecha_crea BETWEEN MD.fecha_ini AND IFNULL(MD.fecha_fin, P.fecha_crea)
							INNER JOIN pacientes PC ON PC.id_paciente=P.id_paciente
							LEFT JOIN listas_detalle TD ON PC.id_tipo_documento=TD.id_detalle
							LEFT JOIN terceros T ON P.id_tercero=T.id_tercero
							LEFT JOIN listas_detalle DT ON T.id_tipo_documento=DT.id_detalle
							LEFT JOIN admisiones A ON P.id_admision=A.id_admision
							LEFT JOIN listas_detalle LD ON LD.id_detalle=P.id_lugar_cita
							LEFT JOIN tipos_citas TC ON A.id_tipo_cita=TC.id_tipo_cita
							LEFT JOIN usuarios U ON U.id_usuario=P.id_usuario_prof
							LEFT JOIN convenios CO ON CO.id_convenio=P.id_convenio
							LEFT JOIN planes PL ON PL.id_plan=P.id_plan
							LEFT JOIN usuarios UP ON UP.id_usuario= P.id_usuario_pago 
							WHERE P.fecha_pago BETWEEN '".$fechaInicial."' AND '".$fechaFinal." 23:59:59'
							AND P.estado_pago=2 ";
					if ($id_convenio != "") {
						$sql .= "AND P.id_convenio=".$id_convenio." ";
					}
					if ($id_plan != "") {
						$sql .= "AND P.id_plan=".$id_plan." ";
					}
					if ($cod_insumo != "") {
						$sql .= "AND MP.cod_procedimiento='".$cod_insumo."' ";
					}
					if ($lugar_cita != '') {
						$sql .= "AND P.id_lugar_cita=".$lugar_cita." ";
					}
					if ($usuario_atiende != '') {
						$sql .= "AND IFNULL(A.id_usuario_prof, P.id_usuario_prof)=".$usuario_atiende." ";
					}
				}
				
				if ($tipo_precio == "" || $tipo_precio == "M") {
					//Medicamentos
					if ($sql != "") {
						$sql .= "UNION ALL ";
					}
					$sql .= "SELECT P.id_pago, P.fecha_pago, CO.nombre_convenio, PL.nombre_plan, LD.nombre_detalle AS sede,
							CONCAT(UP.nombre_usuario, ' ', UP.apellido_usuario) AS usuario_registra_pago,
							CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS medico, PD.cod_medicamento AS cod_insumo,
							CONCAT(MM.nombre_generico, ' - ', MM.nombre_comercial) AS nombre_insumo, PD.tipo_precio, PD.tipo_bilateral,
							PC.nombre_1, PC.nombre_2, PC.apellido_1, PC.apellido_2,
							CONCAT(PC.nombre_1, ' ', IFNULL(PC.nombre_2, ''), ' ', PC.apellido_1, ' ', IFNULL(PC.apellido_2, '')) AS nombre_completo,
							TD.nombre_detalle AS tipo_documento, PC.numero_documento, PC.ind_habeas_data, PD.cantidad, PD.valor, PD.valor_cuota,
							TC.nombre_tipo_cita, PL.ind_tipo_pago, P.num_factura, T.id_tipo_documento,
							DT.codigo_detalle AS cod_tipo_documento_tercero, DT.nombre_detalle AS tipo_documento_tercero,
							T.numero_documento AS numero_documento_tercero, T.numero_verificacion, T.nombre_tercero, A.id_admision
							FROM pagos_detalle PD
							INNER JOIN pagos P ON PD.id_pago=P.id_pago
							INNER JOIN maestro_medicamentos MM ON MM.cod_medicamento=PD.cod_medicamento
							INNER JOIN pacientes PC ON PC.id_paciente=P.id_paciente
							LEFT JOIN listas_detalle TD ON PC.id_tipo_documento=TD.id_detalle
							LEFT JOIN terceros T ON P.id_tercero=T.id_tercero
							LEFT JOIN listas_detalle DT ON T.id_tipo_documento=DT.id_detalle
							LEFT JOIN admisiones A ON P.id_admision=A.id_admision
							LEFT JOIN listas_detalle LD ON LD.id_detalle=P.id_lugar_cita
							LEFT JOIN tipos_citas TC ON A.id_tipo_cita=TC.id_tipo_cita
							LEFT JOIN usuarios U ON U.id_usuario=P.id_usuario_prof
							LEFT JOIN convenios CO ON CO.id_convenio=P.id_convenio
							LEFT JOIN planes PL ON PL.id_plan=P.id_plan
							LEFT JOIN usuarios UP ON UP.id_usuario= P.id_usuario_pago
							WHERE P.fecha_pago BETWEEN '".$fechaInicial."' AND '".$fechaFinal." 23:59:59'
							AND P.estado_pago=2 ";
					if ($id_convenio != "") {
						$sql .= "AND P.id_convenio=".$id_convenio." ";
					}
					if ($id_plan != "") {
						$sql .= "AND P.id_plan=".$id_plan." ";
					}
					if ($cod_insumo != "") {
						$sql .= "AND MM.cod_medicamento=".$cod_insumo." ";
					}
					if ($lugar_cita != '') {
						$sql .= "AND P.id_lugar_cita=".$lugar_cita." ";
					}
					if ($usuario_atiende != '') {
						$sql .= "AND IFNULL(A.id_usuario_prof, P.id_usuario_prof)=".$usuario_atiende." ";
					}
				}
				
				if ($tipo_precio == "" || $tipo_precio == "I") {
					//Insumos
					if ($sql != "") {
						$sql .= "UNION ALL ";
					}
					$sql .= "SELECT P.id_pago, P.fecha_pago, CO.nombre_convenio, PL.nombre_plan, LD.nombre_detalle AS sede,
							CONCAT(UP.nombre_usuario, ' ', UP.apellido_usuario) AS usuario_registra_pago,
							CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS medico, PD.cod_insumo, MI.nombre_insumo, PD.tipo_precio, PD.tipo_bilateral,
							PC.nombre_1, PC.nombre_2, PC.apellido_1, PC.apellido_2,
							CONCAT(PC.nombre_1, ' ', IFNULL(PC.nombre_2, ''), ' ', PC.apellido_1, ' ', IFNULL(PC.apellido_2, '')) AS nombre_completo,
							TD.nombre_detalle AS tipo_documento, PC.numero_documento, PC.ind_habeas_data, PD.cantidad, PD.valor, PD.valor_cuota,
							TC.nombre_tipo_cita, PL.ind_tipo_pago, P.num_factura, T.id_tipo_documento,
							DT.codigo_detalle AS cod_tipo_documento_tercero, DT.nombre_detalle AS tipo_documento_tercero,
							T.numero_documento AS numero_documento_tercero, T.numero_verificacion, T.nombre_tercero, A.id_admision
							FROM pagos_detalle PD
							INNER JOIN pagos P ON PD.id_pago=P.id_pago
							INNER JOIN maestro_insumos MI ON MI.cod_insumo=PD.cod_insumo
							INNER JOIN pacientes PC ON PC.id_paciente=P.id_paciente
							LEFT JOIN listas_detalle TD ON PC.id_tipo_documento=TD.id_detalle
							LEFT JOIN terceros T ON P.id_tercero=T.id_tercero
							LEFT JOIN listas_detalle DT ON T.id_tipo_documento=DT.id_detalle
							LEFT JOIN admisiones A ON P.id_admision=A.id_admision
							LEFT JOIN listas_detalle LD ON LD.id_detalle=P.id_lugar_cita
							LEFT JOIN tipos_citas TC ON A.id_tipo_cita=TC.id_tipo_cita
							LEFT JOIN usuarios U ON U.id_usuario=P.id_usuario_prof
							LEFT JOIN convenios CO ON CO.id_convenio=P.id_convenio
							LEFT JOIN planes PL ON PL.id_plan=P.id_plan
							LEFT JOIN usuarios UP ON UP.id_usuario= P.id_usuario_pago
							WHERE P.fecha_pago BETWEEN '".$fechaInicial."' AND '".$fechaFinal." 23:59:59'
							AND P.estado_pago=2 ";
					if ($id_convenio != "") {
						$sql .= "AND P.id_convenio=".$id_convenio." ";
					}
					if ($id_plan != "") {
						$sql .= "AND P.id_plan=".$id_plan." ";
					}
					if ($cod_insumo != "") {
						$sql .= "AND MI.cod_insumo=".$cod_insumo." ";
					}
					if ($lugar_cita != '') {
						$sql .= "AND P.id_lugar_cita=".$lugar_cita." ";
					}
					if ($usuario_atiende != '') {
						$sql .= "AND IFNULL(A.id_usuario_prof, P.id_usuario_prof)=".$usuario_atiende." ";
					}
				}
				$sql .= "ORDER BY apellido_1, apellido_2, nombre_1, nombre_2, fecha_pago";
				//echo("<textarea>".$sql."</textarea>");
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaPagosDetMediosReporteAtenciones($fecha_ini, $fecha_fin, $id_convenio, $id_plan, $id_usuario_prof, $id_lugar_cita) {
			try {
				$sql = "SELECT P.id_pago, DM.id_medio_pago, TP.nombre AS medio_pago, DM.id_banco,
						B.nombre_detalle AS nombre_banco, DM.valor_pago, TP.ind_negativo
						FROM pagos P
						INNER JOIN pagos_det_medios DM ON P.id_pago=DM.id_pago
						INNER JOIN tipos_pago TP ON DM.id_medio_pago=TP.id
						LEFT JOIN admisiones A ON P.id_admision=A.id_admision
						LEFT JOIN listas_detalle B ON DM.id_banco=B.id_detalle
						WHERE P.fecha_pago BETWEEN '".$fecha_ini."' AND '".$fecha_fin." 23:59:59'
						AND P.estado_pago=2 ";
				if ($id_convenio != "") {
					$sql .= "AND P.id_convenio=".$id_convenio." ";
				}
				if ($id_plan != "") {
					$sql .= "AND P.id_plan=".$id_plan." ";
				}
				if ($id_usuario_prof != "") {
					$sql .= "AND IFNULL(A.id_usuario_prof, P.id_usuario_prof)=".$id_usuario_prof." ";
				}
				if ($id_lugar_cita != "") {
					$sql .= "AND IFNULL(P.id_lugar_cita, A.id_lugar_cita)=".$id_lugar_cita." ";
				}
				$sql .= "ORDER BY P.id_pago, DM.id_medio_pago";
				//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Función que retorna los pagos de procedimientos (incluye consultas) de un convenio en un rango de fechas
		public function getListaPagosProcedimientosConvenioFechas($fecha_ini, $fecha_fin, $id_convenio, $ind_excluye_np) {
			try {
				$sql = "SELECT P.*, PA.nombre_1, PA.nombre_2, PA.apellido_1, PA.apellido_2, PA.id_tipo_documento,
						TD.codigo_detalle AS cod_tipo_documento, TD.nombre_detalle AS tipo_documento, PA.numero_documento,
						PA.fecha_nacimiento, DATE_FORMAT(PA.fecha_nacimiento, '%d/%m/%Y') AS fecha_nacimiento_t,
						PA.cod_dep_nac, PA.cod_mun_nac, PA.nom_dep_nac, PA.nom_mun_nac, DN.nom_dep AS nom_dep_n,
						MN.nom_mun AS nom_mun_n, fu_calcular_edad(PA.fecha_nacimiento, P.fecha_pago) AS edad,
						EC.nombre_detalle AS estado_civil, PA.profesion, A.nombre_acompa, PA.direccion,
						PA.cod_dep, PA.cod_mun, PA.nom_dep, PA.nom_mun, DR.nom_dep AS nom_dep_r, MR.nom_mun AS nom_mun_r,
						PA.telefono_1, PA.telefono_2, IFNULL(PD.num_autorizacion, A.num_autorizacion) AS num_autorizacion,
						A.num_carnet, DATE_FORMAT(P.fecha_pago, '%d/%m/%Y') AS fecha_pago_t, DX.cod_ciex,
						CX.nombre AS nombre_ciex, PD.cod_procedimiento, MD.nombre_procedimiento, PD.valor, PD.valor_cuota
						FROM pagos P
						INNER JOIN pagos_detalle PD ON P.id_pago=PD.id_pago
						INNER JOIN pacientes PA ON P.id_paciente=PA.id_paciente
						INNER JOIN maestro_procedimientos MP ON PD.cod_procedimiento=MP.cod_procedimiento
						LEFT JOIN maestro_procedimientos_det MD ON MP.cod_procedimiento=MD.cod_procedimiento AND P.fecha_crea BETWEEN MD.fecha_ini AND IFNULL(MD.fecha_fin, P.fecha_crea)
						LEFT JOIN admisiones A ON P.id_admision=A.id_admision
						LEFT JOIN listas_detalle TD ON PA.id_tipo_documento=TD.id_detalle
						LEFT JOIN (
							SELECT HC.id_admision, MAX(D.id_hc) AS id_hc
							FROM historia_clinica HC
							INNER JOIN diagnosticos_hc D ON HC.id_hc=D.id_hc
							GROUP BY HC.id_admision
						) HC ON P.id_admision=HC.id_admision
						LEFT JOIN diagnosticos_hc DX ON HC.id_hc=DX.id_hc AND DX.orden=1
						LEFT JOIN vi_ciex CX ON DX.cod_ciex=CX.codciex
						LEFT JOIN departamentos DN ON PA.cod_dep_nac=DN.cod_dep
						LEFT JOIN municipios MN ON PA.cod_mun_nac=MN.cod_mun_dane
						LEFT JOIN listas_detalle EC ON PA.id_estado_civil=EC.id_detalle
						LEFT JOIN departamentos DR ON PA.cod_dep=DR.cod_dep
						LEFT JOIN municipios MR ON PA.cod_mun=MR.cod_mun_dane
						WHERE P.id_convenio=".$id_convenio."
						AND P.fecha_pago BETWEEN STR_TO_DATE('".$fecha_ini."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_fin." 23:59:59', '%d/%m/%Y %H:%i:%s')
						AND P.estado_pago=2 ";
				if ($ind_excluye_np == 1) {
					$sql .= "AND NOT EXISTS (
								SELECT DM.id_pago
								FROM pagos_det_medios DM
								WHERE DM.id_medio_pago=99
								AND DM.id_pago=P.id_pago
							) ";
				}
				$sql .= "ORDER BY PA.apellido_1, PA.apellido_2, PA.nombre_1, PA.nombre_2, P.fecha_pago";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaPagosEstado($fecha_ini, $fecha_fin, $estado_pago, $id_convenio, $id_plan,
				$id_lugar_cita, $id_usuario_adm, $id_usuario_pago, $tipo_precio, $cod_concepto) {
			try {
				$sql = "SELECT P.id_pago, P.id_paciente, TD.codigo_detalle AS codigo_tipo_documento, TD.nombre_detalle AS tipo_documento,
						PA.numero_documento, PA.nombre_1, PA.nombre_2, PA.apellido_1, PA.apellido_2, P.id_convenio, C.nombre_convenio,
						P.id_plan, PL.nombre_plan, P.id_lugar_cita, LC.nombre_detalle AS lugar_cita, PL.ind_tipo_pago,
						P.observaciones_pago, SUM(PD.valor) AS valor, SUM(PD.valor_cuota) AS valor_cuota
						FROM pagos P
						INNER JOIN pagos_detalle PD ON P.id_pago=PD.id_pago
						INNER JOIN pacientes PA ON P.id_paciente=PA.id_paciente
						INNER JOIN listas_detalle TD ON PA.id_tipo_documento=TD.id_detalle
						INNER JOIN convenios C ON P.id_convenio=C.id_convenio
						INNER JOIN planes PL ON P.id_plan=PL.id_plan
						INNER JOIN listas_detalle LC ON P.id_lugar_cita=LC.id_detalle
						LEFT JOIN admisiones A ON P.id_admision=A.id_admision
						WHERE P.fecha_crea BETWEEN '".$fecha_ini."' AND '".$fecha_fin." 23:59:59' ";
				if ($estado_pago != "") {
					$sql .= "AND P.estado_pago=".$estado_pago." ";
				}
				if ($id_convenio != "") {
					$sql .= "AND P.id_convenio=".$id_convenio." ";
				}
				if ($id_plan != "") {
					$sql .= "AND P.id_plan=".$id_plan." ";
				}
				if ($id_lugar_cita != "") {
					$sql .= "AND P.id_lugar_cita=".$id_lugar_cita." ";
				}
				if ($id_usuario_adm != "") {
					$sql .= "AND A.id_usuario_crea=".$id_usuario_adm." ";
				}
				if ($id_usuario_pago != "") {
					$sql .= "AND P.id_usuario_pago=".$id_usuario_pago." ";
				}
				if ($tipo_precio != "" && $cod_concepto != "") {
					switch ($tipo_precio) {
						case "P":
							$sql .= "AND EXISTS (
										SELECT * FROM pagos_detalle D2
										WHERE D2.tipo_precio='".$tipo_precio."'
										AND D2.cod_procedimiento='".$cod_concepto."'
										AND D2.id_pago=P.id_pago
									) ";
							break;
						case "M":
							$sql .= "AND EXISTS (
										SELECT * FROM pagos_detalle D2
										WHERE D2.tipo_precio='".$tipo_precio."'
										AND D2.cod_medicamento='".$cod_concepto."'
										AND D2.id_pago=P.id_pago
									) ";
							break;
						case "I":
							$sql .= "AND EXISTS (
										SELECT * FROM pagos_detalle D2
										WHERE D2.tipo_precio='".$tipo_precio."'
										AND D2.cod_insumo='".$cod_concepto."'
										AND D2.id_pago=P.id_pago
									) ";
							break;
					}
				}
				$sql .= "GROUP BY P.id_pago, P.id_paciente, TD.codigo_detalle, TD.nombre_detalle,
						PA.numero_documento, PA.nombre_1, PA.nombre_2, PA.apellido_1, PA.apellido_2, P.id_convenio, C.nombre_convenio,
						P.id_plan, PL.nombre_plan";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Función que obtiene el total pagado por un paciente en un año dado por concepto de copago
		public function get_total_copago($id_paciente, $ano) {
			try {
				$sql = "SELECT IFNULL(SUM(PD.valor_cuota), 0) AS valor_cuota
						FROM pagos P
						INNER JOIN pagos_detalle PD ON P.id_pago=PD.id_pago
						INNER JOIN maestro_procedimientos MP ON PD.cod_procedimiento=MP.cod_procedimiento
						WHERE P.id_paciente=".$id_paciente."
						AND P.estado_pago=2 ";
				if ($ano != "") {
					$sql .= "AND YEAR(P.fecha_pago)=".$ano." ";
				} else {
					$sql .= "AND YEAR(P.fecha_pago)=YEAR(CURDATE()) ";
				}
				$sql .= "AND MP.ind_proc_qx=1";
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Función que obtiene los pagos con pedido para una compañía en un rango de fechas dado
		public function get_lista_pagos_pedidos_compania($id_compania, $fecha_ini, $fecha_fin) {
			try {
				$sql = "SELECT P.*, PA.nombre_1, PA.nombre_2, Pa.apellido_1, PA.apellido_2, TD.codigo_detalle AS cod_tipo_documento,
						TD.nombre_detalle AS tipo_documento, PA.numero_documento, LG.nombre_detalle AS lugar_cita, UP.nombre_usuario,
						UP.apellido_usuario, DATE_FORMAT(P.fecha_pago, '%d/%m/%Y') AS fecha_pago_t, DATE_FORMAT(P.fecha_pago, '%h:%i:%s %p') AS hora_pago_t
						FROM pagos P
						INNER JOIN sedes_det SD ON P.id_lugar_cita=SD.id_detalle
						INNER JOIN pacientes PA ON P.id_paciente=PA.id_paciente
						LEFT JOIN listas_detalle TD ON PA.id_tipo_documento=TD.id_detalle
						LEFT JOIN listas_detalle LG ON P.id_lugar_cita=LG.id_detalle
						LEFT JOIN usuarios UP ON P.id_usuario_pago=UP.id_usuario
						WHERE P.fecha_pago BETWEEN STR_TO_DATE('".$fecha_ini." 00:00:00', '%d/%m/%Y %H:%i:%s')
						AND STR_TO_DATE('".$fecha_fin." 23:59:59', '%d/%m/%Y %H:%i:%s')
						AND SD.id_compania=".$id_compania."
						AND P.num_pedido<>'-1'
						ORDER BY CAST(P.num_pedido AS UNSIGNED)";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Función que obtiene los pagos con pedido erroneo (-1) para una compañía en un rango de fechas dado
		public function get_lista_pagos_pedido_error_compania($id_compania, $fecha_ini, $fecha_fin) {
			try {
				$sql = "SELECT P.*, PA.nombre_1, PA.nombre_2, Pa.apellido_1, PA.apellido_2, TD.codigo_detalle AS cod_tipo_documento,
						TD.nombre_detalle AS tipo_documento, PA.numero_documento, LG.nombre_detalle AS lugar_cita, UP.nombre_usuario,
						UP.apellido_usuario, DATE_FORMAT(P.fecha_pago, '%d/%m/%Y') AS fecha_pago_t, DATE_FORMAT(P.fecha_pago, '%h:%i:%s %p') AS hora_pago_t
						FROM pagos P
						INNER JOIN sedes_det SD ON P.id_lugar_cita=SD.id_detalle
						INNER JOIN pacientes PA ON P.id_paciente=PA.id_paciente
						LEFT JOIN listas_detalle TD ON PA.id_tipo_documento=TD.id_detalle
						LEFT JOIN listas_detalle LG ON P.id_lugar_cita=LG.id_detalle
						LEFT JOIN usuarios UP ON P.id_usuario_pago=UP.id_usuario
						WHERE P.fecha_pago BETWEEN STR_TO_DATE('".$fecha_ini." 00:00:00', '%d/%m/%Y %H:%i:%s')
						AND STR_TO_DATE('".$fecha_fin." 23:59:59', '%d/%m/%Y %H:%i:%s')
						AND SD.id_compania=".$id_compania."
						AND P.num_pedido='-1'
						ORDER BY P.id_pago";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Función que obtiene el último pedido anterior a una fecha dada para una compañía
		public function get_ultimo_pedido_ant_compania($id_compania, $fecha) {
			try {
				$sql = "SELECT MAX(CAST(num_pedido AS UNSIGNED)) AS num_pedido
						FROM pagos P
						INNER JOIN sedes_det SD ON P.id_lugar_cita=SD.id_detalle
						WHERE P.fecha_pago>=STR_TO_DATE('10/06/2019', '%d/%m/%Y')
						AND P.fecha_pago<STR_TO_DATE('".$fecha."', '%d/%m/%Y')
						AND SD.id_compania=".$id_compania."
						AND P.num_pedido<>'-1'";
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Función que obtiene el primer pedido posterior a una fecha dada para una compañía
		public function get_primer_pedido_post_compania($id_compania, $fecha) {
			try {
				$sql = "SELECT MIN(CAST(num_pedido AS UNSIGNED)) AS num_pedido
						FROM pagos P
						INNER JOIN sedes_det SD ON P.id_lugar_cita=SD.id_detalle
						WHERE P.fecha_pago>=STR_TO_DATE('10/06/2019', '%d/%m/%Y')
						AND P.fecha_pago>STR_TO_DATE('".$fecha." 23:59:59', '%d/%m/%Y %H:%i:%s')
						AND SD.id_compania=".$id_compania."
						AND P.num_pedido<>'-1'";
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Función que obtiene los pagos con factura para una compañía en un rango de fechas dado
		public function get_lista_pagos_facturas_compania($id_compania, $fecha_ini, $fecha_fin) {
			try {
				$sql = "SELECT P.*, PA.nombre_1, PA.nombre_2, Pa.apellido_1, PA.apellido_2, TD.codigo_detalle AS cod_tipo_documento,
						TD.nombre_detalle AS tipo_documento, PA.numero_documento, LG.nombre_detalle AS lugar_cita, UP.nombre_usuario,
						UP.apellido_usuario, DATE_FORMAT(P.fecha_pago, '%d/%m/%Y') AS fecha_pago_t, DATE_FORMAT(P.fecha_pago, '%h:%i:%s %p') AS hora_pago_t
						FROM pagos P
						INNER JOIN sedes_det SD ON P.id_lugar_cita=SD.id_detalle
						INNER JOIN pacientes PA ON P.id_paciente=PA.id_paciente
						LEFT JOIN listas_detalle TD ON PA.id_tipo_documento=TD.id_detalle
						LEFT JOIN listas_detalle LG ON P.id_lugar_cita=LG.id_detalle
						LEFT JOIN usuarios UP ON P.id_usuario_pago=UP.id_usuario
						WHERE P.fecha_pago BETWEEN STR_TO_DATE('".$fecha_ini." 00:00:00', '%d/%m/%Y %H:%i:%s')
						AND STR_TO_DATE('".$fecha_fin." 23:59:59', '%d/%m/%Y %H:%i:%s')
						AND SD.id_compania=".$id_compania."
						AND P.num_factura<>'-1'
						ORDER BY CAST(P.num_factura AS UNSIGNED)";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Función que obtiene los pagos con factura erronea (-1) para una compañía en un rango de fechas dado
		public function get_lista_pagos_facturas_error_compania($id_compania, $fecha_ini, $fecha_fin) {
			try {
				$sql = "SELECT P.*, PA.nombre_1, PA.nombre_2, Pa.apellido_1, PA.apellido_2, TD.codigo_detalle AS cod_tipo_documento,
						TD.nombre_detalle AS tipo_documento, PA.numero_documento, LG.nombre_detalle AS lugar_cita, UP.nombre_usuario,
						UP.apellido_usuario, DATE_FORMAT(P.fecha_pago, '%d/%m/%Y') AS fecha_pago_t, DATE_FORMAT(P.fecha_pago, '%h:%i:%s %p') AS hora_pago_t
						FROM pagos P
						INNER JOIN sedes_det SD ON P.id_lugar_cita=SD.id_detalle
						INNER JOIN pacientes PA ON P.id_paciente=PA.id_paciente
						LEFT JOIN listas_detalle TD ON PA.id_tipo_documento=TD.id_detalle
						LEFT JOIN listas_detalle LG ON P.id_lugar_cita=LG.id_detalle
						LEFT JOIN usuarios UP ON P.id_usuario_pago=UP.id_usuario
						WHERE P.fecha_pago BETWEEN STR_TO_DATE('".$fecha_ini." 00:00:00', '%d/%m/%Y %H:%i:%s')
						AND STR_TO_DATE('".$fecha_fin." 23:59:59', '%d/%m/%Y %H:%i:%s')
						AND SD.id_compania=".$id_compania."
						AND P.num_factura='-1'
						ORDER BY P.id_pago";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Función que obtiene la última factura anterior a una fecha dada para una compañía
		public function get_ultima_factura_ant_compania($id_compania, $fecha) {
			try {
				$sql = "SELECT MAX(CAST(num_factura AS UNSIGNED)) AS num_factura
						FROM pagos P
						INNER JOIN sedes_det SD ON P.id_lugar_cita=SD.id_detalle
						WHERE P.fecha_pago>=STR_TO_DATE('10/06/2019', '%d/%m/%Y')
						AND P.fecha_pago<STR_TO_DATE('".$fecha."', '%d/%m/%Y')
						AND SD.id_compania=".$id_compania."
						AND P.num_factura<>'-1'";
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Función que obtiene la primera factura posterior a una fecha dada para una compañía
		public function get_primera_factura_post_compania($id_compania, $fecha) {
			try {
				$sql = "SELECT MIN(CAST(num_factura AS UNSIGNED)) AS num_factura
						FROM pagos P
						INNER JOIN sedes_det SD ON P.id_lugar_cita=SD.id_detalle
						WHERE P.fecha_pago>=STR_TO_DATE('10/06/2019', '%d/%m/%Y')
						AND P.fecha_pago>STR_TO_DATE('".$fecha." 23:59:59', '%d/%m/%Y %H:%i:%s')
						AND SD.id_compania=".$id_compania."
						AND P.num_factura<>'-1'";
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function actualizarPagoDatos($id_pago, $num_pedido, $num_factura, $num_nota) {
			try {
					if ($num_pedido == "") {
						$num_pedido = "NULL";
					}
					
					if ($num_factura != "") {
						$num_factura = "'".$num_factura."'";
					} else {
						$num_factura = "NULL";
					}
					
					if ($num_nota == "") {
						$num_nota = "NULL";
					}
				
				
				$sql = "CALL pa_actualizar_datos_pago(".$id_pago.", ".$num_pedido.", ".$num_factura.", ".$num_nota.", @id)";
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado_out = $arrResultado["@id"];
				
				return $resultado_out;
			} catch (Exception $e) {
				return array();
			}
		}
		
		
		
	}
?>
