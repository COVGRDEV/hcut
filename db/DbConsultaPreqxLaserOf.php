<?php
	require_once("DbHistoriaClinica.php");
	
	class DbConsultaPreqxLaserOf extends DbHistoriaClinica {
		/**
		 * Obtener los datos de a consulta de optometria a partir del ID de la HC
		 */
		public function getConsultaPreqxLaserOf($id_hc) {
	        try {
	            $sql = "SELECT CC.*, O.nombre_detalle AS ojo, DATE_FORMAT(CC.fecha_cirugia, '%d/%m/%Y') AS fecha_cirugia_t
	            		FROM consultas_preqx_laser_of CC
	            		LEFT JOIN listas_detalle O ON CC.id_ojo=O.id_detalle 
	            		WHERE CC.id_hc=".$id_hc;
	            
				return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		//Crear consulta de optometria
		public function CrearConsultaPreqxLaserOf($id_paciente, $id_tipo_reg, $id_usuario_crea, $id_admision, $ind_preconsulta) {
	        try {
	            $sql = "CALL pa_crear_consultas_preqxlaser_of(".$id_paciente.", ".$id_admision.", ".$id_tipo_reg.", ".$ind_preconsulta.", ".$id_usuario_crea.", @id)";
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$id_historia_clinica=$arrResultado["@id"];
				
				return $id_historia_clinica;
	        } catch (Exception $e) {
	            return -2;
	        }
    	}
		
		//Crear consulta de optometria
		public function EditarConsultaPreqxLaserOf($hdd_id_hc_consulta, $hdd_id_admision, $preqx_laser_subjetivo, $preqx_laser_biomiocroscopia,
				$presion_intraocular_od, $presion_intraocular_oi, $fondo_ojo_nervio_optico_od, $fondo_ojo_macula_od, $fondo_ojo_periferia_od,
				$fondo_ojo_vitreo_od, $fondo_ojo_nervio_optico_oi, $fondo_ojo_macula_oi, $fondo_ojo_periferia_oi, $fondo_ojo_vitreo_oi,
				$preqx_laser_plan, $diagnostico_preqx_laser_of, $solicitud_examenes_preqx_laser, $tratamiento_preqx_laser, $medicamentos_preqx_laser,
				$nombre_usuario_alt, $array_diagnosticos, $id_usuario_crea, $tipo_guardar, $id_ojo) {
			try {
				$sql_delete_diagnosticos = "DELETE FROM temporal_diagnosticos
											WHERE id_hc=".$hdd_id_hc_consulta."
											AND id_usuario=".$id_usuario_crea;
				
				$arrCampos_delete[0] = "@id";
				if ($this->ejecutarSentencia($sql_delete_diagnosticos, $arrCampos_delete)) {
					$j = 1;
					foreach ($array_diagnosticos as $fila_diagnosticos) {
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
				$sql = "CALL pa_editar_consultas_preqxlaser_of(".$hdd_id_hc_consulta.", ".$hdd_id_admision.", '".$preqx_laser_subjetivo."', '".
						$preqx_laser_biomiocroscopia."', '".$presion_intraocular_od."', '".$presion_intraocular_oi."', '".$fondo_ojo_nervio_optico_od."', '".
						$fondo_ojo_macula_od."', '".$fondo_ojo_periferia_od."', '".$fondo_ojo_vitreo_od."', '".$fondo_ojo_nervio_optico_oi."', '".$fondo_ojo_macula_oi."', '".
						$fondo_ojo_periferia_oi."', '".$fondo_ojo_vitreo_oi."', '".$preqx_laser_plan."', '".$diagnostico_preqx_laser_of."', '".
						$solicitud_examenes_preqx_laser."', '".$tratamiento_preqx_laser."', '".$medicamentos_preqx_laser."', ".$nombre_usuario_alt.", ".
						$id_usuario_crea.", ".$tipo_guardar.", ".$id_ojo.", @id)";
				
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
		public function getPreqxLaserOptometriaPaciente($id_paciente, $id_admision) {
	        try {
	            $sql = "SELECT o.* FROM consultas_preqx_laser o
						INNER JOIN historia_clinica h ON h.id_hc = o.id_hc
						WHERE h.id_paciente = '".$id_paciente."' AND h.id_admision = '".$id_admision."'  ";
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }








    }
?>
