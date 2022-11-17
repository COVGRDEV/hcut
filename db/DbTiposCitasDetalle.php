<?php
	require_once("DbConexion.php");
	
	class DbTiposCitasDetalle extends DbConexion {
		public function get_tipos_citas_detalles($id) {//Muestra el listado de la tabla: tipos_citas_det con base en el campo: id_tipo_cita
			try {
				$sql = "SELECT TCD.*, TRH.nombre_tipo_reg, TRH.tipo_reg_adicional, EA.nombre_estado, MP.nombre_procedimiento 
						FROM tipos_citas_det TCD
						LEFT JOIN tipos_registros_hc TRH ON TRH.id_tipo_reg=TCD.id_tipo_reg
						LEFT JOIN estados_atencion EA ON EA.id_estado_atencion=TCD.id_estado_atencion
						LEFT JOIN maestro_procedimientos MP ON MP.cod_procedimiento=TCD.cod_procedimiento
						WHERE TCD.id_tipo_cita=".$id."
						ORDER BY TCD.orden";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_lista_tipos_citas_detalles($id_tipo_cita, $ind_obligatorio = "") {
			try {
				$sql = "SELECT TCD.*, TRH.nombre_tipo_reg, EA.nombre_estado, MP.nombre_procedimiento, EA.orden AS orden_estado_atencion
						FROM tipos_citas_det TCD
						LEFT JOIN tipos_registros_hc TRH ON TRH.id_tipo_reg=TCD.id_tipo_reg
						LEFT JOIN estados_atencion EA ON EA.id_estado_atencion=TCD.id_estado_atencion
						LEFT JOIN maestro_procedimientos MP ON MP.cod_procedimiento=TCD.cod_procedimiento
						WHERE TCD.id_tipo_cita=".$id_tipo_cita." ";
				if ($ind_obligatorio != "") {
					$sql .= "AND TCD.ind_obligatorio=1 ";
				}
				$sql .= "ORDER BY TCD.orden";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_tipos_citas_detalle($idTipoCita, $idTipoReg) {//Muestra el registro de la tabla: tipos_citas_det con base en el campo: id_tipo_cita y id_tiporeg
			try {
				$sql = "SELECT TCD.*, TRH.*, EA.*, MP.*, TCD.orden AS orden_aux
						FROM tipos_citas_det TCD
						LEFT JOIN tipos_registros_hc TRH ON TRH.id_tipo_reg=TCD.id_tipo_reg
						LEFT JOIN estados_atencion EA ON EA.id_estado_atencion=TCD.id_estado_atencion
						LEFT JOIN maestro_procedimientos MP ON MP.cod_procedimiento=TCD.cod_procedimiento                   
						WHERE TCD.id_tipo_reg=".$idTipoReg."
						AND TCD.id_tipo_cita=".$idTipoCita."
						ORDER BY TCD.id_tipo_cita";
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function crea_eliminar_edita_tipos_citas_detalle($idTipoCita, $idTipoRegOri, $idTipoReg, $accion, $orden, $estadoAtencion,
				  $procedimiento, $idUsuario, $usuarioAlt, $ind_obligatorio = "1", $arr_det_remisiones = array()) {
			try {
				if ($orden == "") {
					$orden = "NULL";
				}
				if ($estadoAtencion == "") {
					$estadoAtencion = "NULL";
				} else {
					$estadoAtencion = "".$estadoAtencion."";
				}
				if ($procedimiento == "") {
					$procedimiento = "NULL";
				} else {
					$procedimiento = "'".$procedimiento."'";
				}
				if ($idTipoRegOri == "") {
					$idTipoRegOri = "NULL";
				}
				if ($idTipoReg == "") {
					$idTipoReg = "NULL";
				}
				if ($usuarioAlt == "") {
					$usuarioAlt = "NULL";
				}
				if ($ind_obligatorio == "") {
					$ind_obligatorio = "NULL";
				}
				
				$sql = "CALL pa_tipos_cita_detalle(".$idTipoCita.", ".$idTipoRegOri.", ".$idTipoReg.", ".$accion.", ".$orden.", ".
						$estadoAtencion.", ".$procedimiento.", ".$idUsuario.", ".$usuarioAlt.", ".$ind_obligatorio.", @id)";
				//echo($sql);
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado_out = $arrResultado["@id"];
				
				//Se borran los registros de detalle de remisiones existentes
				$sql = "DELETE FROM tipos_citas_det_remisiones
						WHERE id_tipo_cita=".$idTipoCita."
						AND id_tipo_reg=".$idTipoReg;
				
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				
				//Se actualizan los campos de remisiones del detalle
				if (count($arr_det_remisiones) > 0) {
					foreach ($arr_det_remisiones as $det_remision_aux) {
						$sql = "INSERT INTO tipos_citas_det_remisiones
								(id_tipo_cita, id_tipo_reg, id_tipo_cita_dest, id_tipo_reg_dest, id_usuario_crea, fecha_crea)
								VALUES (".$idTipoCita.", ".$idTipoReg.", ".$det_remision_aux["id_tipo_cita_dest"].", ".$det_remision_aux["id_tipo_reg_hc_dest"].", ".$idUsuario.", NOW())";
						
						$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
					}
				}
				
				return $resultado_out;
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_lista_tipos_citas_det_remisiones($id_tipo_cita, $id_tipo_reg) {
			try {
				$sql = "SELECT DR.*, TC.nombre_tipo_cita, TR.nombre_tipo_reg, TC.ind_examenes
						FROM tipos_citas_det_remisiones DR
						INNER JOIN tipos_citas TC ON DR.id_tipo_cita_dest=TC.id_tipo_cita
						INNER JOIN tipos_registros_hc TR ON DR.id_tipo_reg_dest=TR.id_tipo_reg
						WHERE DR.id_tipo_cita=".$id_tipo_cita."
						AND DR.id_tipo_reg=".$id_tipo_reg."
						ORDER BY TC.nombre_tipo_cita, TR.nombre_tipo_reg";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_lista_tipos_citas_det_clases($id_tipo_cita, $id_clase_reg) {
			try {
				$sql = "SELECT CD.*, TR.*
						FROM tipos_citas_det CD
						INNER JOIN tipos_registros_hc TR ON CD.id_tipo_reg=TR.id_tipo_reg
						WHERE CD.id_tipo_cita=".$id_tipo_cita."
						AND CD.ind_obligatorio=1
						AND TR.id_clase_reg=".$id_clase_reg."
						ORDER BY CD.orden";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_tipo_cita_detalle_estado_atencion($id_tipo_cita, $id_estado_atencion) {
			try {
				$sql = "SELECT CD.*, TR.*
						FROM tipos_citas_det CD
						INNER JOIN tipos_registros_hc TR ON CD.id_tipo_reg=TR.id_tipo_reg
						WHERE CD.id_tipo_cita=".$id_tipo_cita."
						AND CD.id_estado_atencion=CASE WHEN ".$id_estado_atencion."=11 THEN 5 ELSE ".$id_estado_atencion." END
						ORDER BY CD.orden";
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_lista_tipos_citas_detalles_usuarios_prof($id_admision, $id_tipo_cita, $ind_obligatorio) {
			try {
				$sql = "SELECT TCD.*, TRH.nombre_tipo_reg, EA.nombre_estado, MP.nombre_procedimiento, EA.orden AS orden_estado_atencion, AE.id_usuario_prof
						FROM tipos_citas_det TCD
						LEFT JOIN tipos_registros_hc TRH ON TRH.id_tipo_reg=TCD.id_tipo_reg
						LEFT JOIN estados_atencion EA ON EA.id_estado_atencion=TCD.id_estado_atencion
						LEFT JOIN maestro_procedimientos MP ON MP.cod_procedimiento=TCD.cod_procedimiento
						LEFT JOIN admisiones_estados_atencion AE ON EA.id_estado_atencion=AE.id_estado_atencion AND AE.id_admision=".$id_admision."
						WHERE TCD.id_tipo_cita=".$id_tipo_cita." ";
				if ($ind_obligatorio != "") {
					$sql .= "AND TCD.ind_obligatorio=".$ind_obligatorio." ";
				}
				$sql .= "ORDER BY TCD.orden";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	}
?>
