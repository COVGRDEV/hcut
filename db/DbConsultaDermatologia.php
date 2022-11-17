<?php
	require_once("DbHistoriaClinica.php");
	
	class DbConsultaDermatologia extends DbHistoriaClinica {
		/**
		 * Obtener los datos de a consulta de dermatología a partir del ID de la HC
		 */
		public function getConsultaDermatologia($id_hc) {
	        try {
	            $sql = "SELECT CD.*, LU.nombre_detalle AS ludwig
						FROM consultas_dermatologia CD
						LEFT JOIN listas_detalle LU ON CD.id_ludwig=LU.id_detalle
						WHERE CD.id_hc=".$id_hc;
				
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		//Crear consulta de dermatología
		public function CrearConsultaDermatologia($id_paciente, $id_tipo_reg, $id_usuario, $id_admision) {
	        try {
	            $sql = "CALL pa_crear_consultas_dermatologia(".$id_paciente.", ".$id_admision.", ".$id_tipo_reg.", ".$id_usuario.", @id)";
				
				//echo($sql."<br />");
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$id_historia_clinica=$arrResultado["@id"];
				
				return $id_historia_clinica;
	        } catch (Exception $e) {
	            return -2;
	        }
    	}
		
		//Editar consulta de dermatología
		public function EditarConsultaDermatologia($id_hc, $id_admision, $peso, $talla, $id_ludwig, $fg_labio_superior,
				$fg_mejilla, $fg_torax, $fg_espalda_superior, $fg_espalda_inferior, $fg_abdomen_superior, $fg_abdomen_inferior,
				$fg_brazo, $fg_muslo, $descripcion_cara, $descripcion_cuerpo, $desc_antecedentes_medicos, $array_antecedentes_medicos_ids,
				$array_antecedentes_medicos_val, $diagnostico_dermat, $solicitud_examenes, $tratamiento_dermat, $array_diagnosticos,
				$tipo_guardar, $id_usuario) {
			try {
				//Diagnósticos
				$sql = "DELETE FROM temporal_diagnosticos
						WHERE id_hc=".$id_hc."
						AND id_usuario=".$id_usuario;
				
				$arrCampos[0] = "@id";
				if ($this->ejecutarSentencia($sql, $arrCampos)) {
					$j = 1;
					foreach($array_diagnosticos as $fila_diagnosticos){
						$ciex_diagnostico = $fila_diagnosticos[0];
						$valor_ojos = $fila_diagnosticos[1];
						if ($valor_ojos == "") {
							$valor_ojos = "NULL";
						}
						$sql = "INSERT INTO temporal_diagnosticos (id_hc, id_usuario, cod_ciex, id_ojo, orden)
								VALUES (".$id_hc.", ".$id_usuario.", '".$ciex_diagnostico."', ".$valor_ojos.", ".$j.")";
						
						//echo($sql."<br />");
						$arrCampos[0] = "@id";
	                	$this->ejecutarSentencia($sql, $arrCampos);
						$j++;
					}
				}
				
				//Para antedentes medicos
				$sql = "DELETE FROM temporal_antecedentes
						WHERE id_hc=".$id_hc."
						AND id_usuario=".$id_usuario."
						AND tipo_antecedente=1";
				
				$arrCampos[0] = "@id";
				if ($this->ejecutarSentencia($sql, $arrCampos)) {
					$array_antecedentes_medicos_ids = explode(",", $array_antecedentes_medicos_ids);
					$array_antecedentes_medicos_val = explode(",", $array_antecedentes_medicos_val);
					for ($i = 0; $i <= count($array_antecedentes_medicos_ids) - 1; $i++) {
					    if ($array_antecedentes_medicos_val[$i] == 'true') {
							$val_medicos = 1;
						} else {
							$val_medicos = 0;
						}
						if ($val_medicos==1) {
							$sql = "INSERT INTO temporal_antecedentes
									(id_hc, id_usuario, tipo_antecedente, id_antecedente, val_numer)
									VALUES (".$id_hc.", ".$id_usuario.", 1, ".$array_antecedentes_medicos_ids[$i].", ".$val_medicos.")";
							
							//echo($sql."<br />");
							$arrCampos[0] = "@id";
	                		$this->ejecutarSentencia($sql, $arrCampos);
						}
					}
				}
				
				if ($peso == "") {
					$peso = "NULL";
				}
				if ($talla == "") {
					$talla = "NULL";
				}
				if ($id_ludwig == "") {
					$id_ludwig = "NULL";
				}
				if ($fg_labio_superior == "") {
					$fg_labio_superior = "NULL";
				}
				if ($fg_mejilla == "") {
					$fg_mejilla = "NULL";
				}
				if ($fg_torax == "") {
					$fg_torax = "NULL";
				}
				if ($fg_espalda_superior == "") {
					$fg_espalda_superior = "NULL";
				}
				if ($fg_espalda_inferior == "") {
					$fg_espalda_inferior = "NULL";
				}
				if ($fg_abdomen_superior == "") {
					$fg_abdomen_superior = "NULL";
				}
				if ($fg_abdomen_inferior == "") {
					$fg_abdomen_inferior = "NULL";
				}
				if ($fg_brazo == "") {
					$fg_brazo = "NULL";
				}
				if ($fg_muslo == "") {
					$fg_muslo = "NULL";
				}
				if ($descripcion_cara != "") {
					$descripcion_cara = "'".$descripcion_cara."'";
				} else {
					$descripcion_cara = "NULL";
				}
				if ($descripcion_cuerpo != "") {
					$descripcion_cuerpo = "'".$descripcion_cuerpo."'";
				} else {
					$descripcion_cuerpo = "NULL";
				}
				if ($desc_antecedentes_medicos != "") {
					$desc_antecedentes_medicos = "'".$desc_antecedentes_medicos."'";
				} else {
					$desc_antecedentes_medicos = "NULL";
				}
				if ($diagnostico_dermat != "") {
					$diagnostico_dermat = "'".$diagnostico_dermat."'";
				} else {
					$diagnostico_dermat = "NULL";
				}
				if ($solicitud_examenes != "") {
					$solicitud_examenes = "'".$solicitud_examenes."'";
				} else {
					$solicitud_examenes = "NULL";
				}
				if ($tratamiento_dermat != "") {
					$tratamiento_dermat = "'".$tratamiento_dermat."'";
				} else {
					$tratamiento_dermat = "NULL";
				}
				
				$sql = "CALL pa_editar_consultas_dermatologia(".$id_hc.", ".$id_admision.", ".$peso.", ".$talla.", ".$id_ludwig.", ".$fg_labio_superior.", ".
						$fg_mejilla.", ".$fg_torax.", ".$fg_espalda_superior.", ".$fg_espalda_inferior.", ".$fg_abdomen_superior.", ".$fg_abdomen_inferior.", ".
						$fg_brazo.", ".$fg_muslo.", ".$descripcion_cara.", ".$descripcion_cuerpo.", ".$desc_antecedentes_medicos.", ".$diagnostico_dermat.", ".
						$solicitud_examenes.", ".$tratamiento_dermat.", ".$tipo_guardar.", ".$id_usuario.", @id)";
				
				//echo($sql ."<br />");
                $arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$out_ind_opt=$arrResultado["@id"];
				
				return $out_ind_opt;
			} catch (Exception $e) {
				return array();
			}
		}
    }
?>
