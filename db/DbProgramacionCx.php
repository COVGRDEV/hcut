<?php
	require_once("DbConexion.php");
	
	class DbProgramacionCx extends DbConexion {
		//Retorna un registro de programación de cirugía por su ID
		public function getProgramacionCx($id_prog_cx) {
			try {
				$sql = "SELECT PR.*, UP.nombre_usuario AS nombre_usuario_prof, UP.apellido_usuario AS apellido_usuario_prof,
						DATE_FORMAT(PR.fecha_prog, '%d/%m/%Y') AS fecha_prog_t, DATE_FORMAT(PR.fecha_prog, '%H:%i') AS hora_prog_t,
						EP.nombre_detalle AS estado_prog, C.nombre_convenio, PL.nombre_plan, MC.nombre_motivo, UC.nombre_usuario AS nombre_usuario_cancela,
						UC.apellido_usuario AS apellido_usuario_cancela, DATE_FORMAT(PR.fecha_cancela, '%d/%m/%Y %h:%i %p') AS fecha_cancela_t
						FROM programacion_cx PR
						INNER JOIN usuarios UP ON PR.id_usuario_prof=UP.id_usuario
						INNER JOIN listas_detalle EP ON PR.id_estado_prog=EP.id_detalle
						LEFT JOIN convenios C ON PR.id_convenio=C.id_convenio
						LEFT JOIN planes PL ON PR.id_plan=PL.id_plan
						LEFT JOIN motivos_cancela MC ON PR.id_motivo_cancela=MC.id_motivo
						LEFT JOIN usuarios UC ON PR.id_usuario_cancela=UC.id_usuario
						WHERE PR.id_prog_cx=".$id_prog_cx;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Retorna las programaciones de cirugía de un paciente
		public function getListaProgramacionCxPaciente($id_paciente) {
			try {
				$sql = "SELECT PR.*, UP.nombre_usuario AS nombre_usuario_prof, UP.apellido_usuario AS apellido_usuario_prof,
						DATE_FORMAT(PR.fecha_prog, '%d/%m/%Y %h:%i %p') AS fecha_prog_t, EP.nombre_detalle AS estado_prog,
						C.nombre_convenio, PL.nombre_plan, MC.nombre_motivo, UC.nombre_usuario AS nombre_usuario_cancela,
						UC.apellido_usuario AS apellido_usuario_cancela, DATE_FORMAT(PR.fecha_cancela, '%d/%m/%Y %h:%i %p') AS fecha_cancela_t
						FROM programacion_cx PR
						INNER JOIN usuarios UP ON PR.id_usuario_prof=UP.id_usuario
						INNER JOIN listas_detalle EP ON PR.id_estado_prog=EP.id_detalle
						LEFT JOIN convenios C ON PR.id_convenio=C.id_convenio
						LEFT JOIN planes PL ON PR.id_plan=PL.id_plan
						LEFT JOIN motivos_cancela MC ON PR.id_motivo_cancela=MC.id_motivo
						LEFT JOIN usuarios UC ON PR.id_usuario_cancela=UC.id_usuario
						WHERE PR.id_paciente=".$id_paciente."
						ORDER BY PR.fecha_prog DESC";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Retorna el detalle de una programación de cirugía
		public function getListaProgramacionCxDet($id_prog_cx) {
			try {
				$sql = "SELECT PD.*, MP.nombre_procedimiento, MM.nombre_comercial, MM.nombre_generico, MM.presentacion, MI.nombre_insumo, MI.id_tipo_insumo_p
						FROM programacion_cx_det PD
						LEFT JOIN maestro_procedimientos MP ON PD.cod_procedimiento=MP.cod_procedimiento
						LEFT JOIN maestro_medicamentos MM ON PD.cod_medicamento=MM.cod_medicamento
						LEFT JOIN maestro_insumos MI ON PD.cod_insumo=MI.cod_insumo
						WHERE PD.id_prog_cx=".$id_prog_cx."
						ORDER BY MI.nombre_insumo, MM.nombre_generico, MP.nombre_procedimiento";
				
				//echo($sql);
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Retorna los valores asociados a un detalle de una programación de cirugía
		public function getListaProgramacionCxDetValores($id_prog_cx_det) {
			try {
				$sql = "SELECT * FROM programacion_cx_det_valores
						WHERE id_prog_cx_det=".$id_prog_cx_det."
						ORDER BY num_valor";
				
				//echo($sql);
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/*
		Método que crea o edita un registro de programación de cirugía junto con sus registros de detalle
		Retorno:
		>0: Creación/edición realizada (id_prog_cx)
		0: No ocurre
		-1: Error al crear el registro principal de programación
		-2: Error en PHP
		-3: Error al borrar los registros de detalle
		-4: Error al crear los registros de detalle
		-5: Error al crear los registros de valor de detalle
		*/
		public function crearEditarProgramacionCx($id_prog_cx, $id_paciente, $id_usuario_prof, $fecha_prog, $hora_prog, $id_estado_prog,
				$id_convenio, $id_plan, $fecha_ini_cx, $hora_ini_cx, $fecha_fin_cx, $hora_fin_cx, $id_motivo_cancela, $arr_elementos, $id_usuario) {
			try {
				if ($id_prog_cx == "") {
					$id_prog_cx = "NULL";
				}
				if ($hora_prog != "") {
					$fecha_prog = "STR_TO_DATE('".$fecha_prog." ".$hora_prog."', '%d/%m/%Y %H:%i')";
				} else {
					$fecha_prog = "STR_TO_DATE('".$fecha_prog."', '%d/%m/%Y')";
				}
				if ($id_estado_prog == "") {
					$id_estado_prog = "NULL";
				}
				if ($id_plan == "") {
					$id_plan = "NULL";
				}
				if ($fecha_ini_cx != "") {
					$fecha_ini_cx = "STR_TO_DATE('".$fecha_ini_cx." ".$hora_ini_cx."', '%d/%m/%Y %H:%i')";
				} else {
					$fecha_ini_cx = "NULL";
				}
				if ($fecha_fin_cx != "") {
					$fecha_fin_cx = "STR_TO_DATE('".$fecha_fin_cx." ".$hora_fin_cx."', '%d/%m/%Y %H:%i')";
				} else {
					$fecha_fin_cx = "NULL";
				}
				if ($id_motivo_cancela != "") {
					$id_motivo_cancela = "'".$id_motivo_cancela."'";
				} else {
					$id_motivo_cancela = "NULL";
				}
				$sql = "CALL pa_crear_editar_programacion_cx(".$id_prog_cx.", ".$id_paciente.", ".$id_usuario_prof.", ".$fecha_prog.", ".
						$id_estado_prog.", ".$id_convenio.", ".$id_plan.", ".$fecha_ini_cx.", ".$fecha_fin_cx.", ".$id_motivo_cancela.", ".$id_usuario.", @id)";
				
				//echo($sql."<br />");
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado = $arrResultado["@id"];
				
				if ($resultado > 0) {
					if ($id_prog_cx == "NULL") {
						$id_prog_cx = $resultado;
					} else {
						//Se borra el detalle anterior de la programación
						$sql = "CALL pa_borrar_programacion_cx_det(".$id_prog_cx.", ".$id_usuario.", @id)";
						
						//echo($sql."<br />");
						$arrCampos[0] = "@id";
						$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
						$resultado_aux = $arrResultado["@id"];
						
						if ($resultado_aux < 0) {
							$resultado = -3;
						}
					}
					
					if ($resultado > 0) {
						//Se crea el detalle de la programación
						foreach ($arr_elementos as $elemento_aux) {
							$cod_procedimiento = "NULL";
							$cod_medicamento = "NULL";
							$cod_insumo = "NULL";
							switch ($elemento_aux["tipo_elemento"]) {
								case "P":
									$cod_procedimiento = "'".$elemento_aux["cod_elemento"]."'";
									break;
								case "M":
									$cod_medicamento = $elemento_aux["cod_elemento"];
									break;
								case "I":
									$cod_insumo = $elemento_aux["cod_elemento"];
									break;
							}
							
							$sql = "CALL pa_crear_programacion_cx_det(".$id_prog_cx.", ".$cod_procedimiento.", ".$cod_medicamento.", ".$cod_insumo.", '".
									$elemento_aux["tipo_elemento"]."', ".$elemento_aux["tipo_bilateral"].", ".$elemento_aux["cantidad"].", ".$id_usuario.", @id)";
							
							//echo($sql."<br />");
							$arrCampos[0] = "@id";
							$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
							$resultado_aux = $arrResultado["@id"];
							
							if ($resultado_aux < 0) {
								$resultado = -4;
								break;
							}
							
							//Se agregan los valores de lentes asociados al detalle
							if (count($elemento_aux["det_val"]) > 0) {
								$id_prog_cx_det = $resultado_aux;
								for ($i = 0; $i < count($elemento_aux["det_val"]); $i++) {
									$det_val_aux = $elemento_aux["det_val"][$i];
									if ($det_val_aux["tipo_lente"] != "") {
										$det_val_aux["tipo_lente"] = "'".$det_val_aux["tipo_lente"]."'";
									} else {
										$det_val_aux["tipo_lente"] = "NULL";
									}
									if ($det_val_aux["serial_lente"] != "") {
										$det_val_aux["serial_lente"] = "'".$det_val_aux["serial_lente"]."'";
									} else {
										$det_val_aux["serial_lente"] = "NULL";
									}
									if ($det_val_aux["poder_lente"] != "") {
										$det_val_aux["poder_lente"] = "'".$det_val_aux["poder_lente"]."'";
									} else {
										$det_val_aux["poder_lente"] = "NULL";
									}
									$sql = "CALL pa_crear_programacion_cx_det_valores(".$id_prog_cx_det.", ".($i + 1).", ".$det_val_aux["tipo_lente"].", ".
											$det_val_aux["serial_lente"].", ".$det_val_aux["poder_lente"].", ".$id_usuario.", @id)";
									
									//echo($sql."<br />");
									$arrCampos[0] = "@id";
									$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
									$resultado_aux = $arrResultado["@id"];
									
									if ($resultado_aux < 0) {
										$resultado = -5;
										break;
									}
								}
							}
							
							if ($resultado_aux < 0) {
								break;
							}
						}
					}
				}
				
				return $resultado;
			} catch (Exception $e) {
				return -2;
			}
		}
		
		//Retorna el listado de motivos de cancelación
		public function getListaMotivosCancela($ind_activo, $ind_activo_tipo) {
			try {
				$sql = "SELECT MC.*, TM.nombre_tipo_motivo
						FROM motivos_cancela MC
						INNER JOIN tipos_motivos_cancela TM ON MC.id_tipo_motivo=TM.id_tipo_motivo ";
				$conector = "WHERE";
				if ($ind_activo != "") {
					$sql .= $conector." MC.ind_activo=".$ind_activo." ";
					$conector = "AND";
				}
				if ($ind_activo_tipo != "") {
					$sql .= $conector." TM.ind_activo=".$ind_activo_tipo." ";
				}
				$sql .= "ORDER BY MC.nombre_motivo";
				
				//echo($sql);
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Retorna las programaciones de cirugía por rangos de fechas, conceptos, profesionales y estados
		public function getListaProgramacionCxFechas($fecha_ini, $fecha_fin, $tipo_fecha, $tipo_concepto, $cod_concepto, $id_usuario_prof, $lista_estados_prog) {
			try {
				$sql = "SELECT DISTINCT PC.*, TD.codigo_detalle AS cod_tipo_documento, TD.nombre_detalle AS tipo_documento, P.numero_documento, P.nombre_1, P.nombre_2,
						P.apellido_1, P.apellido_2, P.telefono_1, P.telefono_2, UP.nombre_usuario AS nombre_usuario_prof, UP.apellido_usuario AS apellido_usuario_prof,
						EP.nombre_detalle AS estado_prog, C.nombre_convenio, DATE_FORMAT(PC.fecha_prog, '%d/%m/%Y') AS fecha_prog_t, DATE_FORMAT(PC.fecha_prog, '%h:%i %p') AS hora_prog_t,
						UC.nombre_usuario AS nombre_usuario_cancela, UC.apellido_usuario AS apellido_usuario_cancela, MC.nombre_motivo,
						DATE_FORMAT(PC.fecha_crea, '%d/%m/%Y') AS fecha_crea_t, DATE_FORMAT(PC.fecha_crea, '%h:%i %p') AS hora_crea_t,
						DATE_FORMAT(PC.fecha_cancela, '%d/%m/%Y') AS fecha_cancela_t, DATE_FORMAT(PC.fecha_cancela, '%h:%i %p') AS hora_cancela_t
						FROM programacion_cx PC
						INNER JOIN pacientes P ON PC.id_paciente=P.id_paciente
						LEFT JOIN listas_detalle TD ON P.id_tipo_documento=TD.id_detalle
						INNER JOIN listas_detalle EP ON PC.id_estado_prog=EP.id_detalle
						INNER JOIN usuarios UP ON PC.id_usuario_prof=UP.id_usuario
						INNER JOIN convenios C ON PC.id_convenio=C.id_convenio
						LEFT JOIN usuarios UC ON PC.id_usuario_cancela=UC.id_usuario
						LEFT JOIN motivos_cancela MC ON PC.id_motivo_cancela=MC.id_motivo ";
				if ($cod_concepto != "") {
					$sql .= "INNER JOIN programacion_cx_det CD ON PC.id_prog_cx=CD.id_prog_cx ";
				}
				if ($tipo_fecha == "1") {
					//Fecha de programación
					$sql .= "WHERE PC.fecha_prog BETWEEN STR_TO_DATE('".$fecha_ini." 00:00:00', '%d/%m/%Y %H:%i:%s') AND STR_TO_DATE('".$fecha_fin." 23:59:59', '%d/%m/%Y %H:%i:%s') ";
				} else {
					//Fecha de registro
					$sql .= "WHERE PC.fecha_crea BETWEEN STR_TO_DATE('".$fecha_ini." 00:00:00', '%d/%m/%Y %H:%i:%s') AND STR_TO_DATE('".$fecha_fin." 23:59:59', '%d/%m/%Y %H:%i:%s') ";
				}
				if ($cod_concepto != "") {
					switch ($tipo_concepto) {
						case "P":
							$sql .= "AND CD.cod_procedimiento='".$cod_concepto."' ";
							break;
						case "M":
							$sql .= "AND CD.cod_medicamento='".$cod_concepto."' ";
							break;
						case "I":
							$sql .= "AND CD.cod_insumo='".$cod_concepto."' ";
							break;
					}
				}
				if ($id_usuario_prof != "") {
					$sql .= "AND PC.id_usuario_prof=".$id_usuario_prof." ";
				}
				$cadena_aux = "";
				$bol_vacios = false;
				foreach ($lista_estados_prog as $estado_aux) {
					if ($estado_aux["sel_estado_prog"] == 1) {
						if ($cadena_aux != "") {
							$cadena_aux .= ", ";
						}
						$cadena_aux .= $estado_aux["id_estado_prog"];
					} else {
						$bol_vacios = true;
					}
				}
				if ($cadena_aux != "" && $bol_vacios) {
					$sql .= "AND PC.id_estado_prog IN (".$cadena_aux.") ";
				}
				$sql .= "ORDER BY PC.fecha_prog, P.numero_documento";
				
				//echo($sql);
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	}
?>
