<?php
	require_once("DbHistoriaClinica.php");
	
	class DbConsultaPreqxCatarata extends DbHistoriaClinica {
		/**
		 * Obtener los datos de a consulta de optometria a partir del ID de la HC
		 */
		public function get_consulta_preqx_catarata($id_hc) {
	        try {
	            $sql = "SELECT CC.*, O.nombre_detalle AS ojo, LC.nombre_detalle AS locs3,
						PL.nombre_detalle AS plegables, RI.nombre_detalle AS rigido,
						ES.nombre_detalle AS especiales, AN.nombre_detalle AS anestesia,
						DATE_FORMAT(CC.fecha_cirugia, '%d/%m/%Y') AS fecha_cirugia_t
						FROM consultas_preqx_catarata CC
						LEFT JOIN listas_detalle O ON CC.id_ojo=O.id_detalle
						LEFT JOIN listas_detalle LC ON CC.id_locs3=LC.id_detalle
						LEFT JOIN listas_detalle PL ON CC.id_plegables=PL.id_detalle
						LEFT JOIN listas_detalle RI ON CC.id_rigido=RI.id_detalle
						LEFT JOIN listas_detalle ES ON CC.id_especiales=ES.id_detalle
						LEFT JOIN listas_detalle AN ON CC.id_anestesia=AN.id_detalle
						WHERE CC.id_hc=".$id_hc;
				
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		//Crear consulta de optometria
		public function crear_consulta_preqx_catarata($id_paciente, $id_tipo_reg, $id_usuario_crea, $id_admision) {
	        try {
	            $sql = "CALL pa_crear_consulta_preqx_catarata(".$id_paciente.", ".$id_admision.", ".$id_tipo_reg.", ".$id_usuario_crea.", @id)";
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$id_historia_clinica=$arrResultado["@id"];
				
				return $id_historia_clinica;
	        } catch (Exception $e) {
	            return -2;
	        }
    	}
		
		//Crear consulta de optometria
		public function editar_consulta_preqx_catarata($id_hc, $id_admision, $id_locs3, $val_locs3, $val_rec_endotelial, $val_paquimetria, $id_plegables,
				$id_rigido, $id_especiales, $texto_evolucion, $id_anestesia, $querato_val_biometria_od, $querato_eje_biometria_od, $querato_val_iol_master_od,
				$querato_eje_iol_master_od, $querato_val_topografia_od, $querato_eje_topografia_od, $querato_val_definitiva_od, $querato_eje_definitiva_od,
				$querato_val_biometria_oi, $querato_eje_biometria_oi, $querato_val_iol_master_oi, $querato_eje_iol_master_oi, $querato_val_topografia_oi,
				$querato_eje_topografia_oi, $querato_val_definitiva_oi, $querato_eje_definitiva_oi, $img_queratometria_od, $img_queratometria_oi,
				$ind_incision_arq, $val_incision_arq, $observaciones_preqx, $diagnostico_preqx_catarata, $solicitud_examenes_preqx_catarata,
				$tratamiento_preqx_catarata, $medicamentos_preqx_catarata, $nombre_usuario_alt, $array_diagnosticos, $tipo_guardar, $id_usuario) {
			try {
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
				
				$id_locs3 = trim($id_locs3) == "" ? "NULL" : $id_locs3;
				$val_locs3 = trim($val_locs3) == "" ? "NULL" : "'".$val_locs3."'";
				$val_rec_endotelial = trim($val_rec_endotelial) == "" ? "NULL" : $val_rec_endotelial;
				$val_paquimetria = trim($val_paquimetria) == "" ? "NULL" : $val_paquimetria;
				$id_plegables = trim($id_plegables) == "" ? "NULL" : $id_plegables;
				$id_rigido = trim($id_rigido) == "" ? "NULL" : $id_rigido;
				$id_especiales = trim($id_especiales) == "" ? "NULL" : $id_especiales;
				$texto_evolucion = trim($texto_evolucion) == "" ? "NULL" : "'".$texto_evolucion."'";
				$id_anestesia = trim($id_anestesia) == "" ? "NULL" : $id_anestesia;
				$querato_val_biometria_od = trim($querato_val_biometria_od) == "" ? "NULL" : "'".$querato_val_biometria_od."'";
				$querato_eje_biometria_od = trim($querato_eje_biometria_od) == "" ? "NULL" : "'".$querato_eje_biometria_od."'";
				$querato_val_iol_master_od = trim($querato_val_iol_master_od) == "" ? "NULL" : "'".$querato_val_iol_master_od."'";
				$querato_eje_iol_master_od = trim($querato_eje_iol_master_od) == "" ? "NULL" : "'".$querato_eje_iol_master_od."'";
				$querato_val_topografia_od = trim($querato_val_topografia_od) == "" ? "NULL" : "'".$querato_val_topografia_od."'";
				$querato_eje_topografia_od = trim($querato_eje_topografia_od) == "" ? "NULL" : "'".$querato_eje_topografia_od."'";
				$querato_val_definitiva_od = trim($querato_val_definitiva_od) == "" ? "NULL" : "'".$querato_val_definitiva_od."'";
				$querato_eje_definitiva_od = trim($querato_eje_definitiva_od) == "" ? "NULL" : "'".$querato_eje_definitiva_od."'";
				$querato_val_biometria_oi = trim($querato_val_biometria_oi) == "" ? "NULL" : "'".$querato_val_biometria_oi."'";
				$querato_eje_biometria_oi = trim($querato_eje_biometria_oi) == "" ? "NULL" : "'".$querato_eje_biometria_oi."'";
				$querato_val_iol_master_oi = trim($querato_val_iol_master_oi) == "" ? "NULL" : "'".$querato_val_iol_master_oi."'";
				$querato_eje_iol_master_oi = trim($querato_eje_iol_master_oi) == "" ? "NULL" : "'".$querato_eje_iol_master_oi."'";
				$querato_val_topografia_oi = trim($querato_val_topografia_oi) == "" ? "NULL" : "'".$querato_val_topografia_oi."'";
				$querato_eje_topografia_oi = trim($querato_eje_topografia_oi) == "" ? "NULL" : "'".$querato_eje_topografia_oi."'";
				$querato_val_definitiva_oi = trim($querato_val_definitiva_oi) == "" ? "NULL" : "'".$querato_val_definitiva_oi."'";
				$querato_eje_definitiva_oi = trim($querato_eje_definitiva_oi) == "" ? "NULL" : "'".$querato_eje_definitiva_oi."'";
				$img_queratometria_od = trim($img_queratometria_od) == "" ? "NULL" : "'".$img_queratometria_od."'";
				$img_queratometria_oi = trim($img_queratometria_oi) == "" ? "NULL" : "'".$img_queratometria_oi."'";
				$ind_incision_arq = trim($ind_incision_arq) == "" ? "NULL" : $ind_incision_arq;
				$val_incision_arq = trim($val_incision_arq) == "" ? "NULL" : $val_incision_arq;
				$observaciones_preqx = trim($observaciones_preqx) == "" ? "NULL" : "'".$observaciones_preqx."'";
				$diagnostico_preqx_catarata = trim($diagnostico_preqx_catarata) == "" ? "NULL" : "'".$diagnostico_preqx_catarata."'";
				$solicitud_examenes_preqx_catarata = trim($solicitud_examenes_preqx_catarata) == "" ? "NULL" : "'".$solicitud_examenes_preqx_catarata."'";
				$tratamiento_preqx_catarata = trim($tratamiento_preqx_catarata) == "" ? "NULL" : "'".$tratamiento_preqx_catarata."'";
				$medicamentos_preqx_catarata = trim($medicamentos_preqx_catarata) == "" ? "NULL" : "'".$medicamentos_preqx_catarata."'";
				
				if ($nombre_usuario_alt != "") {
					$nombre_usuario_alt = "'".$nombre_usuario_alt."'";
				} else {
					$nombre_usuario_alt = "NULL";
				}
				$sql = "CALL pa_editar_consulta_preqx_catarata(".$id_hc.", ".$id_admision.", ".$id_locs3.", ".$val_locs3.", ".$val_rec_endotelial.", ".$val_paquimetria.", ".
					   $id_plegables.", ".$id_rigido.", ".$id_especiales.", ".$texto_evolucion.", ".$id_anestesia.", ".$querato_val_biometria_od.", ".$querato_eje_biometria_od.", ".
					   $querato_val_iol_master_od.", ".$querato_eje_iol_master_od.", ".$querato_val_topografia_od.", ".$querato_eje_topografia_od.", ".$querato_val_definitiva_od.", ".
					   $querato_eje_definitiva_od.", ".$querato_val_biometria_oi.", ".$querato_eje_biometria_oi.", ".$querato_val_iol_master_oi.", ".$querato_eje_iol_master_oi.", ".
					   $querato_val_topografia_oi.", ".$querato_eje_topografia_oi.", ".$querato_val_definitiva_oi.", ".$querato_eje_definitiva_oi.", ".$img_queratometria_od.", ".
					   $img_queratometria_oi.", ".$ind_incision_arq.", ".$val_incision_arq.", ".$observaciones_preqx.", ".$diagnostico_preqx_catarata.", ".
					   $solicitud_examenes_preqx_catarata.", ".$tratamiento_preqx_catarata.", ".$medicamentos_preqx_catarata.", ".$nombre_usuario_alt.", ".$id_usuario.", ".
					   $tipo_guardar.", @id)";
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado = $arrResultado["@id"];
				
				return $resultado;
			} catch (Exception $e) {
				return array();
			}
		}
    }
?>
