<?php
	require_once("DbHistoriaClinica.php");
	
	class DbConsultaControlLaserOf extends DbHistoriaClinica {
		/**
		 * Obtener los datos de a consulta de optometria a partir del ID de la HC
		 */
		public function getConsultaControlLaserOf($id_hc) {
	        try {
	            $sql = "SELECT CC.*, O.nombre_detalle AS ojo, DATE_FORMAT(CC.fecha_cirugia, '%d/%m/%Y') AS fecha_cirugia_t
	            		FROM consultas_control_laser_of CC
	            		LEFT JOIN listas_detalle O ON CC.id_ojo=O.id_detalle 
	            		WHERE CC.id_hc=".$id_hc;
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		//Crear consulta de optometria
		public function CrearConsultaControlLaserOf($id_paciente, $id_tipo_reg, $id_usuario_crea, $id_admision, $ind_preconsulta) {
	        try {
	            $sql = "CALL pa_crear_consultas_control_laser_of(".$id_paciente.", ".$id_admision.", ".$id_tipo_reg.", ".$ind_preconsulta.", ".$id_usuario_crea.", @id)";
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$id_historia_clinica=$arrResultado["@id"];
				
				return $id_historia_clinica;
	        } catch (Exception $e) {
	            return -2;
	        }
    	}
		
		//Crear consulta de oftalmologia
		public function EditarConsultaControlLaserOf($hdd_id_hc_consulta, $hdd_id_admision,	$presion_intraocular_aplanatica_od, $presion_intraocular_aplanatica_oi,
				$hallazgos_control_laser, $diagnostico_control_laser_of, $solicitud_examenes_control_laser, $tratamiento_control_laser, $medicamentos_control_laser,
				$nombre_usuario_alt, $array_diagnosticos, $id_usuario_crea, $tipo_guardar, $ind_formula_gafas = 0) {
			try {
				$sql_delete_diagnosticos = "DELETE FROM temporal_diagnosticos WHERE id_hc=".$hdd_id_hc_consulta." AND id_usuario=".$id_usuario_crea;
				$arrCampos_delete[0] = "@id";
				if ($this->ejecutarSentencia($sql_delete_diagnosticos, $arrCampos_delete)) {
					$j = 1;
					foreach($array_diagnosticos as $fila_diagnosticos){
						$ciex_diagnostico = $fila_diagnosticos[0];
						$valor_ojos = $fila_diagnosticos[1];
						$sql_insert_diagnosticos = "INSERT INTO temporal_diagnosticos (id_hc, id_usuario, cod_ciex, id_ojo, orden)
													VALUES (".$hdd_id_hc_consulta.", ".$id_usuario_crea.", '".$ciex_diagnostico."', '".$valor_ojos."', ".$j.")";
						//echo $sql_insert_diagnosticos."<br />";
						$arrCampos[0] = "@id";
	                	$this->ejecutarSentencia($sql_insert_diagnosticos, $arrCampos);
						$j++;
					}
				}
				
				if ($nombre_usuario_alt != "") {
					$nombre_usuario_alt = "'".$nombre_usuario_alt."'";
				} else {
					$nombre_usuario_alt = "NULL";
				}
				if ($ind_formula_gafas == "") {
					$ind_formula_gafas = "NULL";
				}
				
				$sql = "CALL pa_editar_consultas_controllaser_of(".$hdd_id_hc_consulta.", ".$hdd_id_admision.", '".
						$presion_intraocular_aplanatica_od."', '".$presion_intraocular_aplanatica_oi."', '".$hallazgos_control_laser."', '".
						$diagnostico_control_laser_of."', '".$solicitud_examenes_control_laser."', '".$tratamiento_control_laser."', '".
						$medicamentos_control_laser."', ".$nombre_usuario_alt.", ".$ind_formula_gafas.", ".$id_usuario_crea.", ".$tipo_guardar.", @id)";
				
                $arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$out_ind_opt=$arrResultado["@id"];
				return $out_ind_opt;
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Obtener una consulta de optometria a partir del id del paciente y el id de la admision
		 */
		public function getOptometriaPaciente($id_paciente, $id_admision) {
	        try {
	            $sql = "SELECT o.*
						FROM consultas_optometria o
						INNER JOIN historia_clinica h ON h.id_hc=o.id_hc
						WHERE h.id_paciente=".$id_paciente."
						AND h.id_admision=".$id_admision;
				
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		/**
		 * Obtener una consulta de control laser de optometria a partir del id del paciente y el id de la admision
		 */
		public function getControlLaserPaciente($id_paciente, $id_admision) {
	        try {
	            $sql = "SELECT o.*
						FROM consultas_control_laser o
						INNER JOIN historia_clinica h ON h.id_hc=o.id_hc
						WHERE h.id_paciente=".$id_paciente."
						AND h.id_admision=".$id_admision;
				
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
    }
?>
