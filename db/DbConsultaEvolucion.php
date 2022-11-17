<?php
	require_once("DbHistoriaClinica.php");
	
	class DbConsultaEvolucion extends DbHistoriaClinica {
		/**
		 * Obtener los datos de a consulta de optometria a partir del ID de la HC
		 */
		public function get_consulta_evolucion($id_hc) {
	        try {
	            $sql = "SELECT CE.*, O.nombre_detalle AS ojo,
						DATE_FORMAT(CE.fecha_cirugia, '%d/%m/%Y') AS fecha_cirugia_t
						FROM consultas_evoluciones CE
						LEFT JOIN listas_detalle O ON CE.id_ojo=O.id_detalle
						WHERE CE.id_hc=".$id_hc;
				
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		//Crear consulta de optometria
		public function crear_consulta_evolucion($id_paciente, $id_tipo_reg, $id_usuario_crea, $id_admision) {
	        try {
				if ($id_admision == "") {
					$id_admision = "NULL";
				}
				
	            $sql = "CALL pa_crear_consulta_evolucion(".$id_paciente.", ".$id_admision.", ".$id_tipo_reg.", ".$id_usuario_crea.", @id)";
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$id_historia_clinica=$arrResultado["@id"];
				
				return $id_historia_clinica;
	        } catch (Exception $e) {
	            return -2;
	        }
    	}
		
		//Crear consulta de optometria
		public function editar_consulta_evolucion($id_hc, $id_admision, $texto_evolucion, $array_diagnosticos, $diagnostico_evolucion,
				$solicitud_examenes_evolucion, $tratamiento_evolucion, $medicamentos_evolucion, $nombre_usuario_alt, $tipo_guardar,
				$ind_formula_gafas, $array_antecedentes, $array_tonometria, $observaciones_tonometria, $id_usuario) {
			try {
				//Para tonometria
				$this->crear_registros_temp_tonometria($id_hc, $array_tonometria, $id_usuario);
				
				//Temporal de diagnósticos
				$sql = "DELETE FROM temporal_diagnosticos
						WHERE id_hc=".$id_hc."
						AND id_usuario=".$id_usuario;
				
				$arrCampos[0] = "@id";
				if ($this->ejecutarSentencia($sql, $arrCampos)) {
					$j = 1;
					foreach ($array_diagnosticos as $diagnostico_aux) {
						$ciex_diagnostico = $diagnostico_aux[0];
						$valor_ojos = $diagnostico_aux[1];
						$sql = "INSERT INTO temporal_diagnosticos
								(id_hc, id_usuario, cod_ciex, id_ojo, orden)
								VALUES (".$id_hc.", ".$id_usuario.", '".$ciex_diagnostico."', '".$valor_ojos."', ".$j.")";
						
						$arrCampos[0] = "@id";
	                	$this->ejecutarSentencia($sql, $arrCampos);
						$j++;						  
					}
				}
				
				//Antedentes medicos
				$sql = "DELETE FROM temporal_antecedentes
						WHERE id_hc=".$id_hc."
						AND id_usuario=".$id_usuario."
						AND tipo_antecedente=2";
				
				//echo($sql."<br />");
				$arrCampos_delete[0] = "@id";
				if ($this->ejecutarSentencia($sql, $arrCampos_delete)) {
					for ($i = 0; $i < count($array_antecedentes); $i++) {
						if ($array_antecedentes[$i]["texto_antecedente"] != "") {
							$sql = "INSERT INTO temporal_antecedentes
									(id_hc, id_usuario, tipo_antecedente, id_antecedente, val_texto)
									VALUES (".$id_hc.", ".$id_usuario.", 2, ".
									$array_antecedentes[$i]["id_antecedentes_medicos"].", '".$array_antecedentes[$i]["texto_antecedente"]."')";
							
							//echo($sql."<br />");
							$arrCampos[0] = "@id";
							$this->ejecutarSentencia($sql, $arrCampos);
						}
					}
				}
				
				if ($id_admision == "") {
					$id_admision = "NULL";
				}
				if ($nombre_usuario_alt != "") {
					$nombre_usuario_alt = "'".$nombre_usuario_alt."'";
				} else {
					$nombre_usuario_alt = "NULL";
				}
				if ($ind_formula_gafas == "") {
					$ind_formula_gafas = "NULL";
				}
				if ($observaciones_tonometria == "") {
					$observaciones_tonometria = "NULL";
				} else {
					$observaciones_tonometria = "'".$observaciones_tonometria."'";
				}
				
				$sql = "CALL pa_editar_consulta_evolucion(".$id_hc.", ".$id_admision.", '".$texto_evolucion."', '".$diagnostico_evolucion."', '".
						$solicitud_examenes_evolucion."', '".$tratamiento_evolucion."', '".$medicamentos_evolucion."', ".$nombre_usuario_alt.", ".
						$ind_formula_gafas.", ".$observaciones_tonometria.", ".$id_usuario.", ".$tipo_guardar.", @id)";
				//echo($sql);
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado = $arrResultado["@id"];
				
				return $resultado;
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Obtener una cnsulta de control de optometria a partir del id del paciente y el id de la admision
		 */
		public function getOptometriaControlPaciente($id_paciente, $id_admision) {
			try {
				$sql = "SELECT O.*
						FROM consultas_control_optometria O
						INNER JOIN historia_clinica H ON O.id_hc=H.id_hc
						WHERE H.id_paciente=".$id_paciente."
						AND H.id_admision=".$id_admision;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Obtener una cnsulta de optometria a partir del id del paciente y el id de la admision
		 */
		public function getOptometriaPaciente($id_paciente, $id_admision) {
			try {
				$sql = "SELECT O.*
						FROM consultas_optometria O
						INNER JOIN historia_clinica H ON O.id_hc=H.id_hc
						WHERE H.id_paciente=".$id_paciente."
						AND H.id_admision=".$id_admision;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		
		
		/**
		 * Obtener personas que se matricularon a postqx de catarata
		 */
		public function getPostQxCatarata($id_paciente) {
			try {
				$sql = "SELECT p.*, COUNT(r.id) AS cantidad_respuestas
						FROM seguimiento_postqx_catarata p
						LEFT JOIN registro_foto r ON r.id_paciente = p.id_paciente
						WHERE p.id_paciente = ".$id_paciente."
						AND (DATE(NOW()) BETWEEN DATE(p.fecha_seguimiento_uno) AND DATE(p.fecha_seguimiento_dos))";
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		
		
		
		
		
    }
?>
