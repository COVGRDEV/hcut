<?php
	require_once("DbConexion.php");
	
	class DbListasEspera extends DbConexion {
		public function get_listas_espera($parametro, $id_tipo_lista, $id_estado_lista) {
			try {
				$parametro = str_replace(" ", "%", $parametro);
				$sql = "SELECT LE.*, TD.codigo_detalle AS cod_tipo_documento, TD.nombre_detalle AS tipo_documento,
						TC.nombre_detalle AS tipo_cirugia, DATE_FORMAT(LE.fecha_lista, '%d/%m/%Y') AS fecha_lista_t
						FROM listas_espera LE
						INNER JOIN listas_detalle TD ON LE.id_tipo_documento=TD.id_detalle
						INNER JOIN listas_detalle TC ON LE.id_tipo_cirugia=TC.id_detalle
						WHERE LE.id_estado_lista=".$id_estado_lista." ";
				if ($id_tipo_lista != "") {
					$sql .= "AND LE.id_tipo_cirugia=".$id_tipo_lista." ";
				}
				if ($parametro != "") {
					$sql .= "AND (CONCAT(LE.nombre_1, ' ', IFNULL(LE.nombre_2, ''), ' ', LE.apellido_1, ' ', IFNULL(LE.apellido_2, '')) LIKE '%".$parametro."%'
							OR LE.numero_documento LIKE '%".$parametro."%') ";
				}
				$sql .= "ORDER BY LE.fecha_lista, LE.id_reg_lista";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_registro_espera($id_reg_lista) {
			try {
				$sql = "SELECT LE.*, TD.codigo_detalle AS cod_tipo_documento, TD.nombre_detalle AS tipo_documento,
						TC.nombre_detalle AS tipo_cirugia, DATE_FORMAT(LE.fecha_lista, '%d/%m/%Y') AS fecha_lista_t
						FROM listas_espera LE
						INNER JOIN listas_detalle TD ON LE.id_tipo_documento=TD.id_detalle
						INNER JOIN listas_detalle TC ON LE.id_tipo_cirugia=TC.id_detalle
						WHERE LE.id_reg_lista=".$id_reg_lista;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_registro_espera_paciente_estado($id_paciente, $id_estado_lista) {
			try {
				$sql = "SELECT LE.*, TD.codigo_detalle AS cod_tipo_documento, TD.nombre_detalle AS tipo_documento,
						TC.nombre_detalle AS tipo_cirugia, DATE_FORMAT(LE.fecha_lista, '%d/%m/%Y') AS fecha_lista_t
						FROM listas_espera LE
						INNER JOIN listas_detalle TD ON LE.id_tipo_documento=TD.id_detalle
						INNER JOIN listas_detalle TC ON LE.id_tipo_cirugia=TC.id_detalle
						WHERE LE.id_paciente=".$id_paciente."
						AND LE.id_estado_lista=".$id_estado_lista;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function editar_lista_espera($id_reg_lista, $id_paciente, $id_tipo_documento, $numero_documento, $nombre_1, $nombre_2,
				$apellido_1, $apellido_2, $fecha_lista, $id_tipo_cirugia, $telefono_contacto, $id_estado_lista, $id_usuario) {
			try {
				if ($id_paciente == "") {
					$id_paciente = "NULL";
				}
				if ($nombre_2 == "") {
					$nombre_2 = "NULL";
				} else {
					$nombre_2 = "'".$nombre_2."'";
				}
				if ($apellido_2 == "") {
					$apellido_2 = "NULL";
				} else {
					$apellido_2 = "'".$apellido_2."'";
				}
				
				$sql = "CALL pa_editar_lista_espera(".$id_reg_lista.", ".$id_paciente.", ".$id_tipo_documento.", '".$numero_documento."', '".
						$nombre_1."', ".$nombre_2.", '".$apellido_1."', ".$apellido_2.", STR_TO_DATE('".$fecha_lista."', '%d/%m/%Y'), ".
						$id_tipo_cirugia.", '".$telefono_contacto."', ".$id_estado_lista.", ".$id_usuario.", @id)";
				//echo($sql);
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				
				return $arrResultado["@id"];
			} catch (Exception $e) {
				return -2;
			}
		}
		
		public function crear_lista_espera($id_paciente, $id_tipo_documento, $numero_documento, $nombre_1, $nombre_2,
				$apellido_1, $apellido_2, $fecha_lista, $id_tipo_cirugia, $telefono_contacto, $id_estado_lista, $id_usuario) {
			try {
				if ($id_paciente == "") {
					$id_paciente = "NULL";
				}
				if ($nombre_2 == "") {
					$nombre_2 = "NULL";
				} else {
					$nombre_2 = "'".$nombre_2."'";
				}
				if ($apellido_2 == "") {
					$apellido_2 = "NULL";
				} else {
					$apellido_2 = "'".$apellido_2."'";
				}
				
				$sql = "CALL pa_crear_lista_espera(".$id_paciente.", ".$id_tipo_documento.", '".$numero_documento."', '".
						$nombre_1."', ".$nombre_2.", '".$apellido_1."', ".$apellido_2.", STR_TO_DATE('".$fecha_lista."', '%d/%m/%Y'), ".
						$id_tipo_cirugia.", '".$telefono_contacto."', ".$id_estado_lista.", ".$id_usuario.", @id)";
				//echo($sql);
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				
				return $arrResultado["@id"];
			} catch (Exception $e) {
				return -2;
			}
		}
		
		public function borrar_lista_espera($id_reg_lista, $id_usuario) {
			try {
				$sql = "CALL pa_borrar_lista_espera(".$id_reg_lista.", ".$id_usuario.", @id)";
				//echo($sql);
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				
				return $arrResultado["@id"];
			} catch (Exception $e) {
				return -2;
			}
		}
		
		public function marcar_preqx_lista_espera($id_reg_lista, $id_usuario) {
			try {
				$sql = "CALL pa_marcar_preqx_lista_espera(".$id_reg_lista.", ".$id_usuario.", @id)";
				//echo($sql);
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				
				return $arrResultado["@id"];
			} catch (Exception $e) {
				return -2;
			}
		}
	}
?>
