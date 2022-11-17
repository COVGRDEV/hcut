<?php
require_once("DbConexion.php");

class DbAntecedentes extends DbConexion {
	/**
	 * Tipos de antecedentes:
	 * m - médicos
	 * o - otros
	 * t - todos
	 */
    public function get_lista_antecedentes($tipo_antecedente, $ind_activo = "", $ind_inicial = "") {
        try {
			$sql = "";
			if ($tipo_antecedente == "m" || $tipo_antecedente == "t") {
	            $sql = "SELECT id_antecedentes_medicos AS id_antecedente, nombre_antecedentes_medicos AS nombre_antecedente, ".
    	               "ind_activo, ind_inicial, 'm' AS tipo_antecedente, orden ".
        	           "FROM antecedentes_medicos ";
				$sql_aux = "WHERE ";
				if ($ind_activo != "") {
					$sql .= $sql_aux."ind_activo=".$ind_activo." ";
					$sql_aux = "AND ";
				}
				if ($ind_inicial != "") {
					$sql .= $sql_aux."ind_inicial=".$ind_inicial." ";
				}
			}
			if ($tipo_antecedente == "o" || $tipo_antecedente == "t") {
				if ($sql != "") {
					$sql .= "UNION ALL ";
				}
	            $sql .= "SELECT id_antecedentes_otros AS id_antecedente, nombre_antecedentes_otros AS nombre_antecedente, ".
						"ind_activo, ind_inicial, 'o' AS tipo_antecedente, orden ".
						"FROM antecedentes_otros ";
				$sql_aux = "WHERE ";
				if ($ind_activo != "") {
					$sql .= $sql_aux."ind_activo=".$ind_activo." ";
					$sql_aux = "AND ";
				}
				if ($ind_inicial != "") {
					$sql .= $sql_aux."ind_inicial=".$ind_inicial." ";
				}
			}
			if ($sql != "") {
				$sql .= "ORDER BY tipo_antecedente, orden, nombre_antecedente";
			}
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
    public function get_antecedente($id_antecedente, $tipo_antecedente) {
        try {
			$sql = "";
			switch ($tipo_antecedente) {
				case "m":
					$sql = "SELECT id_antecedentes_medicos AS id_antecedente, nombre_antecedentes_medicos AS nombre_antecedente, ".
    	            	   "ind_activo, ind_inicial, orden ".
	        	           "FROM antecedentes_medicos ".
						   "WHERE id_antecedentes_medicos=".$id_antecedente;
					break;
				case "o":
		            $sql = "SELECT id_antecedentes_otros AS id_antecedente, nombre_antecedentes_otros AS nombre_antecedente, ".
						   "ind_activo, ind_inicial, orden ".
						   "FROM antecedentes_otros ".
						   "WHERE id_antecedentes_otros=".$id_antecedente;
			}
			
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
    public function actualizar_antecedente($id_antecedente, $tipo_antecedente, $nombre_antecedente, $orden, $ind_inicial, $ind_activo, $id_usuario) {
		try {
			$sql = "CALL pa_editar_antecedente(".$id_antecedente.", '".$tipo_antecedente."', '".$nombre_antecedente."', ".
				   $orden.", ".$ind_inicial.", ".$ind_activo.", ".$id_usuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		} catch (Exception $e) {
			return -2;
		}
	}
	
    public function get_lista_antecedentes_medicos_hc($id_hc) {
        try {
			$sql = "SELECT H.*, AM.nombre_antecedentes_medicos
					FROM hc_antecedentes_medicos H
					INNER JOIN antecedentes_medicos AM ON H.id_antecedentes_medicos=AM.id_antecedentes_medicos
					WHERE H.id_hc=".$id_hc."
					ORDER BY AM.orden, AM.nombre_antecedentes_medicos";
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
    public function get_lista_antecedentes_otros_hc($id_hc) {
        try {
			$sql = "SELECT H.*, AO.nombre_antecedentes_otros
					FROM hc_antecedentes_otros H
					INNER JOIN antecedentes_otros AO ON H.id_antecedentes_otros=AO.id_antecedentes_otros
					WHERE H.id_hc=".$id_hc."
					ORDER BY AO.orden, AO.nombre_antecedentes_otros";
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
    public function get_lista_antecedentes_medicos_hc2($id_hc) {
        try {
			$sql = "SELECT AM.*, H.valor_antecedentes_medicos, H.texto_antecedente
					FROM antecedentes_medicos AM
					LEFT JOIN hc_antecedentes_medicos H ON AM.id_antecedentes_medicos=H.id_antecedentes_medicos AND H.id_hc=".$id_hc."
					WHERE AM.ind_activo=1
					AND AM.ind_inicial=1
					ORDER BY AM.nivel, AM.id_antecedentes_medicos_padre, AM.orden, AM.nombre_antecedentes_medicos";
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
    public function get_lista_antecedentes_med_contactos($arr_antecedentes_med) {
        try {
			$cadena_aux = "";
			foreach ($arr_antecedentes_med as $antecedente_aux) {
				if ($cadena_aux != "") {
					$cadena_aux .= ", ";
				}
				$cadena_aux .= $antecedente_aux;
			}
			
			$sql = "SELECT AM.*, CM.id_contacto, CM.nombre_contacto, CM.especialidad, CM.direccion, CM.telefono_1, CM.telefono_2, CM.email
					FROM antecedentes_med_contactos MC
					INNER JOIN contactos_medicos CM ON MC.id_contacto=CM.id_contacto
					INNER JOIN antecedentes_medicos AM ON MC.id_antecedentes_medicos=AM.id_antecedentes_medicos
					INNER JOIN antecedentes_medicos AP ON AM.id_antecedentes_medicos_padre=AP.id_antecedentes_medicos
					WHERE MC.id_antecedentes_medicos IN (".$cadena_aux.")
					AND MC.ind_activo=1
					AND CM.ind_activo=1
					ORDER BY AP.orden, AM.orden, CM.nombre_contacto";
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
	public function guardar_temporal_antec_med_det($id_hc, $mapa_antec_med_det, $id_usuario) {
		try {
			//Se limpia la tabla temporal de detalle de antecedentes
			$sql = "DELETE FROM temporal_hc_antecedentes_medicos_det
					WHERE id_usuario=".$id_usuario."
					AND id_hc=".$id_hc;
			
			$this->ejecutarSentencia($sql, array());
			
			//Se crea la sentencia bulk insert
			$sql = "INSERT INTO temporal_hc_antecedentes_medicos_det
					(id_usuario, id_hc, id_antecedentes_medicos, nombre_tipo, nombre_profesional, fecha_det, orden)
					VALUES ";
			$sql_extra = "";
			foreach ($mapa_antec_med_det as $id_antecedentes_medicos => $arr_detalle_aux) {
				foreach ($arr_detalle_aux as $cont_aux => $detalle_aux) {
					$fecha_det_aux = $detalle_aux["fecha_det"];
					if ($fecha_det_aux != "") {
						$fecha_det_aux = "STR_TO_DATE('".$fecha_det_aux."', '%d/%m/%Y')";
					} else {
						$fecha_det_aux = "NULL";
					}
					$sql .= $sql_extra.
							"(".$id_usuario.", ".$id_hc.", ".$id_antecedentes_medicos.", '".$detalle_aux["nombre_tipo"]."', '".
							$detalle_aux["nombre_profesional"]."', ".$fecha_det_aux.", ".$cont_aux.")";
					
					$sql_extra = ", ";
				}
			}
			//echo($sql."<br />");
			
			$this->ejecutarSentencia($sql, array());
			
			return 1;
		} catch (Exception $e) {
            return -2;
        }
	}
	
    public function get_lista_hc_antecedentes_medicos_det($id_hc) {
        try {
			$sql = "SELECT HA.id_antecedentes_medicos, AM.nombre_antecedentes_medicos, HA.valor_antecedentes_medicos, MD.id_antec_med_det, MD.id_hc,
					MD.nombre_tipo, MD.nombre_profesional, MD.fecha_det, DATE_FORMAT(MD.fecha_det, '%d/%m/%Y') AS fecha_det_t
					FROM hc_antecedentes_medicos HA
					INNER JOIN antecedentes_medicos AM ON HA.id_antecedentes_medicos=AM.id_antecedentes_medicos
					LEFT JOIN hc_antecedentes_medicos_det MD ON HA.id_hc=MD.id_hc AND HA.id_antecedentes_medicos=MD.id_antecedentes_medicos
					LEFT JOIN antecedentes_medicos AP ON AM.id_antecedentes_medicos_padre=AP.id_antecedentes_medicos
					WHERE HA.id_hc=".$id_hc."
					AND AM.tipo_extension<>0
					ORDER BY IFNULL(AP.orden, AM.orden), AM.orden, MD.fecha_det, MD.id_antec_med_det";
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
}
?>
