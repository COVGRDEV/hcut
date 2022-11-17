<?php
	require_once("DbConexion.php");
	
	class DbNotasNoAsistenciales extends DbConexion {
		public function getNotaNoAsistencial($id_nota) {
			try {
				$sql = "SELECT * FROM notas_no_asistenciales
						WHERE id_nota=".$id_nota;
				
				//echo($sql);
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaNotasNoAsistencialesDet($id_nota) {
			try {
				$sql = "SELECT *, DATE_FORMAT(fecha_nota, '%d/%m/%Y') AS fecha_nota_t
						FROM notas_no_asistenciales_det
						WHERE id_nota=".$id_nota."
						ORDER BY id_nota_det";
				
				//echo($sql);
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getNotaNoAsistencialDet($id_nota, $num_nota_det) {
			try {
				$sql = "SELECT *, DATE_FORMAT(fecha_nota, '%d/%m/%Y') AS fecha_nota_t
						FROM notas_no_asistenciales_det
						WHERE id_nota=".$id_nota."
						AND num_nota_det=".$num_nota_det;
				
				//echo($sql);
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaNotasNoAsistencialesB($parametro, $fecha_admision) {
			try {
				$parametro = str_replace(" ", "%", $parametro);
				$sql = "SELECT A.id_admision, A.fecha_admision, DATE_FORMAT(A.fecha_admision, '%d/%m/%Y') AS fecha_admision_t,
						P.id_paciente, P.id_tipo_documento, TD.codigo_detalle AS codigo_tipo_documento, TD.nombre_detalle AS tipo_documento,
						P.numero_documento, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, NA.id_nota, TC.nombre_tipo_cita
						FROM pacientes P
						INNER JOIN admisiones A ON P.id_paciente=A.id_paciente
						LEFT JOIN tipos_citas TC ON A.id_tipo_cita=TC.id_tipo_cita
						LEFT JOIN notas_no_asistenciales NA ON P.id_paciente=NA.id_paciente AND A.id_admision=NA.id_admision
						LEFT JOIN listas_detalle TD ON P.id_tipo_documento=TD.id_detalle
						WHERE (CONCAT(P.nombre_1, ' ', IFNULL(P.nombre_2, ''), ' ', P.apellido_1, ' ', IFNULL(P.apellido_2, '')) LIKE '%".$parametro."%'
						OR P.numero_documento='".$parametro."') ";
				if ($fecha_admision != "") {
					$sql .= "AND A.fecha_admision BETWEEN STR_TO_DATE('".$fecha_admision."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_admision." 23:59:59', '%d/%m/%Y %H:%i:%s') ";
				}
				$sql .= "ORDER BY P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, A.fecha_admision DESC";
				
				//echo($sql);
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function crearEditarNotaNoAsistencial($id_nota, $id_admision, $id_paciente, $arr_notas_det, $id_usuario) {
			try {
				//Se limpia la tabla temporal de notas
				$sql = "DELETE FROM temporal_notas_no_asistenciales_det
						WHERE id_usuario=".$id_usuario;
				
				$this->ejecutarSentencia($sql, array());
				
				$bol_continuar = true;
				//Se insertan los textos de las notas en la tabla temporal de detalle
				for ($i = 0; $i < count($arr_notas_det); $i++) {
					$reg_aux = $arr_notas_det[$i];
					
					$sql = "CALL pa_crear_temporal_nota_no_asistencial_det(".$id_usuario.", ".($i + 1).", '".$reg_aux["texto_nota"].
							"', STR_TO_DATE('".$reg_aux["fecha_nota"]."', '%d/%m/%Y'), @id)";
					echo($sql."<br />");
					
					$arrCampos[0] = "@id";
					$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
					$resul_aux = intval($arrResultado["@id"], 10);
					
					if ($resul_aux <= 0) {
						$bol_continuar = false;
						break;
					}
				}
				
				if ($bol_continuar) {
					if ($id_nota == "") {
						$id_nota = "NULL";
					}
					$sql = "CALL pa_crear_editar_nota_no_asistencial(".$id_nota.", ".$id_admision.", ".$id_paciente.", ".$id_usuario.", @id)";
					
					$arrCampos[0] = "@id";
					$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
					$resultado = $arrResultado["@id"];
					
					return $resultado;
				} else {
					//Error al registrar los temporales de notas_det
					return -3;
				}
	        } catch (Exception $e) {
	            return -2;
	        }
		}
	}
?>
