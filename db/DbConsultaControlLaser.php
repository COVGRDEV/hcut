<?php
	require_once("DbHistoriaClinica.php");
	
	class DbConsultaControlLaser extends DbHistoriaClinica {
		/**
		 * Obtener los datos de a consulta de optometria a partir del ID de la HC
		 */
		public function getConsultaControlLaser($id_hc) {
	        try {
	            $sql = "SELECT CC.*, O.nombre_detalle AS ojo, DATE_FORMAT(CC.fecha_cirugia, '%d/%m/%Y') AS fecha_cirugia_t,
						DATE_FORMAT(HC.fecha_hora_hc, '%d/%m/%Y') AS fecha_hc_t
	            		FROM consultas_control_laser CC
						INNER JOIN historia_clinica HC ON CC.id_hc=HC.id_hc 
	            		LEFT JOIN listas_detalle O ON CC.id_ojo=O.id_detalle 
	            		WHERE CC.id_hc=".$id_hc;
				
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		/**
		 * Obtener los datos de a consulta de optometria a partir del ID de la HC
		 */
		public function getConsultaControlLaserAdmision($id_admision) {
	        try {
	            $sql = "SELECT CC.*, O.nombre_detalle AS ojo, DATE_FORMAT(CC.fecha_cirugia, '%d/%m/%Y') AS fecha_cirugia_t,
						DATE_FORMAT(HC.fecha_hora_hc, '%d/%m/%Y') AS fecha_hc_t
						FROM consultas_control_laser CC
						INNER JOIN historia_clinica HC ON CC.id_hc=HC.id_hc
						LEFT JOIN listas_detalle O ON CC.id_ojo=O.id_detalle 
						WHERE HC.id_admision=".$id_admision;
				
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		//Crear consulta de optometria
		public function CrearConsultaControlLaser($id_paciente, $id_tipo_reg, $id_usuario_crea, $id_admision) {
	        try {
	            $sql = "CALL pa_crear_consultas_control_laser($id_paciente, $id_admision, $id_tipo_reg, $id_usuario_crea, @id)";
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$id_historia_clinica=$arrResultado["@id"];
				
				return $id_historia_clinica;
	            
	        } catch (Exception $e) {
	            return -2;
	        }
    	}
		
		//Crear consulta de optometria
		public function EditarConsultaControlLaser($hdd_id_hc_consulta, $hdd_id_admision, $anamnesis, $avsc_lejos_od, $avsc_cerca_od, $avsc_lejos_oi, $avsc_cerca_oi, 
                                                  $querato_cilindro_od, $querato_eje_od, $querato_mplano_od, $querato_cilindro_oi, $querato_eje_oi, $querato_mplano_oi, 
                                                  $avc_esfera_od, $avc_cilindro_od, $avc_eje_od, $avcc_lejos_od, $avcc_adicion_od, $avcc_cerca_od, 
                                                  $avc_esfera_oi, $avc_cilindro_oi, $avc_eje_oi, $avcc_lejos_oi, $avcc_adicion_oi, $avcc_cerca_oi, 
                                                  $diagnostico_control_laser, $observaciones_avc, $array_diagnosticos, $id_usuario_crea, $tipo_guardar) {
			try {
				$sql = "DELETE FROM temporal_diagnosticos
						WHERE id_hc=".$hdd_id_hc_consulta."
						AND id_usuario=".$id_usuario_crea;
				
				$arrCampos_delete[0] = "@id";
				if ($this->ejecutarSentencia($sql, $arrCampos_delete)) {
					$j = 1;
					foreach ($array_diagnosticos as $fila_diagnosticos) {
						$ciex_diagnostico = $fila_diagnosticos[0];
						$valor_ojos = $fila_diagnosticos[1];
						$sql = "INSERT INTO temporal_diagnosticos (id_hc, id_usuario, cod_ciex, id_ojo, orden)
												  VALUES ($hdd_id_hc_consulta, $id_usuario_crea, '$ciex_diagnostico', '$valor_ojos', $j)";
						
						//echo($sql."<br />");
						$arrCampos[0] = "@id";
	                	$this->ejecutarSentencia($sql, $arrCampos);
						$j++;
					}
				}
				
				$sql = "CALL pa_editar_consultas_controllaser(".$hdd_id_hc_consulta.", ".$hdd_id_admision.", '".$anamnesis."', ".$avsc_lejos_od.", ".
						$avsc_cerca_od.", ".$avsc_lejos_oi.", ".$avsc_cerca_oi.", '".$querato_cilindro_od."', '".$querato_eje_od."', '".$querato_mplano_od."', '".
						$querato_cilindro_oi."', '".$querato_eje_oi."', '".$querato_mplano_oi."', '".$avc_esfera_od."', '".$avc_cilindro_od."', '".
						$avc_eje_od."', ".$avcc_lejos_od.", '".$avcc_adicion_od."', ".$avcc_cerca_od.", '".$avc_esfera_oi."', '".$avc_cilindro_oi."', '".
						$avc_eje_oi."', ".$avcc_lejos_oi.", '".$avcc_adicion_oi."', ".$avcc_cerca_oi.", '".$diagnostico_control_laser."', '".
						$observaciones_avc."', ".$id_usuario_crea.", ".$tipo_guardar.", @id)";
				
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
