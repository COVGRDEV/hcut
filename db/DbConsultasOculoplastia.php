<?php
	require_once("DbHistoriaClinica.php");
	
	class DbConsultasOculoplastia extends DbHistoriaClinica {
		public function getConsultaOculoplastia($id_hc) {
	        try {
	            $sql = "SELECT *
						FROM consultas_oculoplastia
						WHERE id_hc=".$id_hc;
				
				//echo($sql);
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		public function getListaConsultasOculoplastiaAntec($id_hc) {
	        try {
	            $sql = "SELECT AO.*, CA.id_det_antec, CA.texto_antec_ocp, CA.fecha_antec_ocp,
						DATE_FORMAT(CA.fecha_antec_ocp, '%d/%m/%Y') AS fecha_antec_ocp_t
						FROM listas_detalle AO
						LEFT JOIN consultas_oculoplastia_antec CA ON AO.id_detalle=CA.id_antec_ocp AND CA.id_hc=".$id_hc."
						WHERE AO.id_lista=56
						AND (AO.ind_activo=1 OR CA.texto_antec_ocp IS NOT NULL)
						ORDER BY AO.orden";
				
				//echo($sql);
	            return $this->getDatos($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		public function getListaConsultasOculoplastiaCompl($id_hc) {
	        try {
	            $sql = "SELECT CO.*, CC.ind_compl_ocp
						FROM listas_detalle CO
						LEFT JOIN consultas_oculoplastia_compl CC ON CO.id_detalle=CC.id_compl_ocp AND CC.id_hc=".$id_hc."
						WHERE CO.id_lista=57
						AND (CO.ind_activo=1 OR CC.ind_compl_ocp=1)
						ORDER BY CO.orden";
				
				//echo($sql);
	            return $this->getDatos($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		public function crearConsultaOculoplastia($id_hc, $id_usuario) {
			try {
				$sql = "CALL pa_crear_consulta_oculoplastia(".$id_hc.", ".$id_usuario.", @id)";
				
				//echo($sql);
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$out_ind_oft = $arrResultado["@id"];
				return $out_ind_oft;
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function crearEditarConsultaOculoplastia($id_hc, $exoftalmometria_od, $exoftalmometria_base, $exoftalmometria_oi, $observ_orbita,
				$observ_cejas, $fme_od, $fme_oi, $dmr_od, $dmr_oi, $fen_od, $fen_oi, $observ_parpados, $observ_pestanas, $gm_expresibilidad_od,
				$gm_expresibilidad_oi, $gm_calidad_expr_od, $gm_calidad_expr_oi, $observ_glandulas_meib, $prueba_irrigacion_od,
				$prueba_irrigacion_oi, $observ_via_lagrimal, $arr_oculoplastia_antec, $arr_oculoplastia_compl, $id_usuario) {
			try {
				//Para antecedentes
				$sql = "DELETE FROM temporal_consultas_oculoplastia_antec
						WHERE id_usuario=".$id_usuario."
						AND id_hc=".$id_hc;
				
				$arrCampos[0] = "@id";
				
				if ($this->ejecutarSentencia($sql, $arrCampos)) {
					$j = 1;
					foreach ($arr_oculoplastia_antec as $antec_aux) {
						$id_antec_ocp = $antec_aux["id_antec_ocp"];
						$texto_antec_ocp = $antec_aux["texto_antec_ocp"];
						if ($texto_antec_ocp != "") {
							$texto_antec_ocp = "'".$texto_antec_ocp."'";
						} else {
							$texto_antec_ocp = "NULL";
						}
						$fecha_antec_ocp = $antec_aux["fecha_antec_ocp"];
						if ($fecha_antec_ocp != "") {
							$fecha_antec_ocp = "STR_TO_DATE('".$fecha_antec_ocp."', '%d/%m/%Y')";
						} else {
							$fecha_antec_ocp = "NULL";
						}
						$sql = "INSERT INTO temporal_consultas_oculoplastia_antec
								(id_usuario, id_hc, id_antec_ocp, texto_antec_ocp, fecha_antec_ocp)
								VALUES (".$id_usuario.", ".$id_hc.", ".$id_antec_ocp.", ".$texto_antec_ocp.", ".$fecha_antec_ocp.")";
						
						$arrCampos[0] = "@id";
	                	$this->ejecutarSentencia($sql, $arrCampos);
						$j++; 
					}
				}
				
				/*Para complemento de antecedentes*/
				$sql = "DELETE FROM temporal_consultas_oculoplastia_compl
						WHERE id_usuario=".$id_usuario."
						AND id_hc=".$id_hc;
				
				$arrCampos[0] = "@id";
				
				if ($this->ejecutarSentencia($sql, $arrCampos)) {
					$j = 1;
					foreach ($arr_oculoplastia_compl as $compl_aux) {
						$id_compl_ocp = $compl_aux["id_compl_ocp"];
						$ind_compl_ocp = $compl_aux["ind_compl_ocp"];
						if ($ind_compl_ocp == "") {
							$ind_compl_ocp = "0";
						}
						$sql = "INSERT INTO temporal_consultas_oculoplastia_compl
								(id_usuario, id_hc, id_compl_ocp, ind_compl_ocp)
								VALUES (".$id_usuario.", ".$id_hc.", ".$id_compl_ocp.", ".$ind_compl_ocp.")";
						
						$arrCampos[0] = "@id";
	                	$this->ejecutarSentencia($sql, $arrCampos);
						$j++; 
					}
				}
				
				if ($exoftalmometria_od != "") {
					$exoftalmometria_od = "'".$exoftalmometria_od."'";
				} else {
					$exoftalmometria_od = "NULL";
				}
				if ($exoftalmometria_base != "") {
					$exoftalmometria_base = "'".$exoftalmometria_base."'";
				} else {
					$exoftalmometria_base = "NULL";
				}
				if ($exoftalmometria_oi != "") {
					$exoftalmometria_oi = "'".$exoftalmometria_oi."'";
				} else {
					$exoftalmometria_oi = "NULL";
				}
				if ($observ_orbita != "") {
					$observ_orbita = "'".$observ_orbita."'";
				} else {
					$observ_orbita = "NULL";
				}
				if ($observ_cejas != "") {
					$observ_cejas = "'".$observ_cejas."'";
				} else {
					$observ_cejas = "NULL";
				}
				if ($fme_od != "") {
					$fme_od = "'".$fme_od."'";
				} else {
					$fme_od = "NULL";
				}
				if ($fme_oi != "") {
					$fme_oi = "'".$fme_oi."'";
				} else {
					$fme_oi = "NULL";
				}
				if ($dmr_od != "") {
					$dmr_od = "'".$dmr_od."'";
				} else {
					$dmr_od = "NULL";
				}
				if ($dmr_oi != "") {
					$dmr_oi = "'".$dmr_oi."'";
				} else {
					$dmr_oi = "NULL";
				}
				if ($fen_od != "") {
					$fen_od = "'".$fen_od."'";
				} else {
					$fen_od = "NULL";
				}
				if ($fen_oi != "") {
					$fen_oi = "'".$fen_oi."'";
				} else {
					$fen_oi = "NULL";
				}
				if ($observ_parpados != "") {
					$observ_parpados = "'".$observ_parpados."'";
				} else {
					$observ_parpados = "NULL";
				}
				if ($observ_pestanas != "") {
					$observ_pestanas = "'".$observ_pestanas."'";
				} else {
					$observ_pestanas = "NULL";
				}
				if ($gm_expresibilidad_od != "") {
					$gm_expresibilidad_od = "'".$gm_expresibilidad_od."'";
				} else {
					$gm_expresibilidad_od = "NULL";
				}
				if ($gm_expresibilidad_oi != "") {
					$gm_expresibilidad_oi = "'".$gm_expresibilidad_oi."'";
				} else {
					$gm_expresibilidad_oi = "NULL";
				}
				if ($gm_calidad_expr_od != "") {
					$gm_calidad_expr_od = "'".$gm_calidad_expr_od."'";
				} else {
					$gm_calidad_expr_od = "NULL";
				}
				if ($gm_calidad_expr_oi != "") {
					$gm_calidad_expr_oi = "'".$gm_calidad_expr_oi."'";
				} else {
					$gm_calidad_expr_oi = "NULL";
				}
				if ($observ_glandulas_meib != "") {
					$observ_glandulas_meib = "'".$observ_glandulas_meib."'";
				} else {
					$observ_glandulas_meib = "NULL";
				}
				if ($prueba_irrigacion_od != "") {
					$prueba_irrigacion_od = "'".$prueba_irrigacion_od."'";
				} else {
					$prueba_irrigacion_od = "NULL";
				}
				if ($prueba_irrigacion_oi != "") {
					$prueba_irrigacion_oi = "'".$prueba_irrigacion_oi."'";
				} else {
					$prueba_irrigacion_oi = "NULL";
				}
				if ($observ_via_lagrimal != "") {
					$observ_via_lagrimal = "'".$observ_via_lagrimal."'";
				} else {
					$observ_via_lagrimal = "NULL";
				}
				
				$sql = "CALL pa_crear_editar_consulta_oculoplastia(".$id_hc.", ".$exoftalmometria_od.", ".$exoftalmometria_base.", ".
						$exoftalmometria_oi.", ".$observ_orbita.", ".$observ_cejas.", ".$fme_od.", ".$fme_oi.", ".$dmr_od.", ".
						$dmr_oi.", ".$fen_od.", ".$fen_oi.", ".$observ_parpados.", ".$observ_pestanas.", ".$gm_expresibilidad_od.", ".
						$gm_expresibilidad_oi.", ".$gm_calidad_expr_od.", ".$gm_calidad_expr_oi.", ".$observ_glandulas_meib.", ".
						$prueba_irrigacion_od.", ".$prueba_irrigacion_oi.", ".$observ_via_lagrimal.", ".$id_usuario.", @id)";
				
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
