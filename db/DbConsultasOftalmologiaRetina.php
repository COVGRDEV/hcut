<?php
	require_once("DbHistoriaClinica.php");
	
	class DbConsultasOftalmologiaRetina extends DbHistoriaClinica {
		public function getConsultaOftalmologiaRetina($id_hc) {
	        try {
	            $sql = "SELECT *
						FROM consultas_oftalmologia_retina
						WHERE id_hc=".$id_hc;
				
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		public function getListaConsultasOftalmologiaRetinaCx($id_hc) {
	        try {
	            $sql = "SELECT *, DATE_FORMAT(fecha_cx, '%d/%m/%Y') AS fecha_cx_t
						FROM consultas_oftalmologia_retina_cx
						WHERE id_hc=".$id_hc."
						ORDER BY id_det_cx";
				
	            return $this->getDatos($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		public function crearEditarConsultaOftalmologiaRetina($id_hc, $ind_laser_ret, $ind_intravitreas_ret, $cant_intr_od_ret,
				$cant_intr_oi_ret, $ind_cx_retina, $arr_cx_ret, $id_usuario) {
			try {
				/*Para cirugías de retina*/
				$sql = "DELETE FROM temporal_consultas_oftalmologia_retina_cx
						WHERE id_usuario=".$id_usuario."
						AND id_hc=".$id_hc;
				
				$arrCampos[0] = "@id";
				
				if ($this->ejecutarSentencia($sql, $arrCampos)) {
					$j = 1;
					foreach ($arr_cx_ret as $cx_ret) {
						$texto_cx = $cx_ret["texto_cx"];
						if ($texto_cx != "") {
							$texto_cx = "'".$texto_cx."'";
						} else {
							$texto_cx = "NULL";
						}
						$fecha_cx = $cx_ret["fecha_cx"];
						if ($fecha_cx != "") {
							$fecha_cx = "STR_TO_DATE('".$fecha_cx."', '%d/%m/%Y')";
						} else {
							$fecha_cx = "NULL";
						}
						$sql = "INSERT INTO temporal_consultas_oftalmologia_retina_cx
								(id_usuario, id_hc, orden, texto_cx, fecha_cx)
								VALUES (".$id_usuario.", ".$id_hc.", ".$j.", ".$texto_cx.", ".$fecha_cx.")";
						
						$arrCampos[0] = "@id";
	                	$this->ejecutarSentencia($sql, $arrCampos);
						$j++; 
					}
				}
				
				if ($ind_laser_ret == "") {
					$ind_laser_ret = "NULL";
				}
				if ($ind_intravitreas_ret == "") {
					$ind_intravitreas_ret = "NULL";
				}
				if ($cant_intr_od_ret == "") {
					$cant_intr_od_ret = "NULL";
				}
				if ($cant_intr_oi_ret == "") {
					$cant_intr_oi_ret = "NULL";
				}
				if ($ind_cx_retina == "") {
					$ind_cx_retina = "NULL";
				}
				
				$sql = "CALL pa_crear_editar_consulta_oftalmologia_retina(".$id_hc.", ".$ind_laser_ret.", ".$ind_intravitreas_ret.", ".
						$cant_intr_od_ret.", ".$cant_intr_oi_ret.", ".$ind_cx_retina.", ".$id_usuario.", @id)";
				
				//echo($sql);
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$out_ind_oft = $arrResultado["@id"];
				return $out_ind_oft;
			} catch (Exception $e) {
				return array();
			}
		}
	}
?>
