<?php
	require_once("DbHistoriaClinica.php");
	
	class DbCirugias extends DbHistoriaClinica {
		public function get_cirugia($id_hc) {
	        try {
	            $sql = "SELECT CX.*, AR.codigo_detalle AS cod_amb_rea, AR.nombre_detalle AS amb_rea, FP.codigo_detalle AS cod_fin_pro,
						FP.nombre_detalle AS fin_pro, UP.nombre_usuario, UP.apellido_usuario, DATE_FORMAT(CX.fecha_cx, '%d/%m/%Y') AS fecha_cx_t,
						DATE_FORMAT(CX.fecha_cx_ant, '%d/%m/%Y') AS fecha_cx_ant_t
						FROM cirugias CX
						LEFT JOIN listas_detalle AR ON CX.id_amb_rea=AR.id_detalle
						LEFT JOIN listas_detalle FP ON CX.id_fin_pro=FP.id_detalle
						LEFT JOIN usuarios UP ON CX.id_usuario_prof=UP.id_usuario
						WHERE CX.id_hc=".$id_hc;
				
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		public function get_cirugia_laser($id_hc) {
	        try {
	            $sql = "SELECT CX.*, CL.*, AR.nombre_detalle AS amb_rea, FP.nombre_detalle AS fin_pro, TL.nombre_detalle AS tipo_laser,
						OJ.nombre_detalle AS ojo, TD.nombre_detalle AS tecnica_od, TI.nombre_detalle AS tecnica_oi,
						UP.nombre_usuario, UP.apellido_usuario, UE.nombre_usuario AS nombre_usuario_ev, UE.apellido_usuario AS apellido_usuario_ev,
						DATE_FORMAT(CX.fecha_cx, '%d/%m/%Y') AS fecha_cx_t, DATE_FORMAT(CX.fecha_cx_ant, '%d/%m/%Y') AS fecha_cx_ant_t
						FROM cirugias CX
						LEFT JOIN cirugias_laser CL ON CX.id_hc=CL.id_hc
						LEFT JOIN listas_detalle AR ON CX.id_amb_rea=AR.id_detalle
						LEFT JOIN listas_detalle FP ON CX.id_fin_pro=FP.id_detalle
						LEFT JOIN listas_detalle TL ON CL.id_tipo_laser=TL.id_detalle
						LEFT JOIN listas_detalle OJ ON CL.id_ojo=OJ.id_detalle
						LEFT JOIN listas_detalle TD ON CL.id_tecnica_od=TD.id_detalle
						LEFT JOIN listas_detalle TI ON CL.id_tecnica_oi=TI.id_detalle
						LEFT JOIN usuarios UP ON CX.id_usuario_prof=UP.id_usuario
						LEFT JOIN usuarios UE ON CL.id_usuario_ev=UE.id_usuario
						WHERE CX.id_hc=".$id_hc;
				
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		public function get_lista_cirugias_procedimientos($id_hc) {
	        try {
	            $sql = "SELECT CP.*, P.nombre_procedimiento, OJ.nombre_detalle AS ojo
						FROM cirugias_procedimientos CP
						INNER JOIN maestro_procedimientos P ON CP.cod_procedimiento=P.cod_procedimiento
						LEFT JOIN listas_detalle OJ ON CP.id_ojo=OJ.id_detalle
						WHERE CP.id_hc=".$id_hc."
						ORDER BY CP.id_cx_proc";
				
	            return $this->getDatos($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		public function get_cirugia_procedimiento($id_hc, $cod_procedimiento) {
	        try {
	            $sql = "SELECT CP.*, P.nombre_procedimiento, OJ.nombre_detalle AS ojo
						FROM cirugias_procedimientos CP
						INNER JOIN maestro_procedimientos P ON CP.cod_procedimiento=P.cod_procedimiento
						LEFT JOIN listas_detalle OJ ON CP.id_ojo=OJ.id_detalle
						WHERE CP.id_hc=".$id_hc."
						AND CP.cod_procedimiento='".$cod_procedimiento."'";
				
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		//Crear cirugía
		public function editar_cirugia($id_hc, $id_admision, $fecha_cx, $id_convenio, $id_amb_rea, $id_fin_pro,
				$id_usuario_prof, $ind_reoperacion, $ind_reop_ent, $fecha_cx_ant, $observaciones_cx, $arch_cx,
				$arch_stickers, $array_procedimientos, $array_diagnosticos, $tipo_guardar, $id_usuario) {
			try {
				if ($fecha_cx == "") {
					$fecha_cx = "NULL";
				} else {
					$fecha_cx = "STR_TO_DATE('".$fecha_cx."', '%d/%m/%Y')";
				}
				if ($id_convenio == "") {
					$id_convenio = "NULL";
				}
				if ($id_amb_rea == "") {
					$id_amb_rea = "NULL";
				}
				if ($id_fin_pro == "") {
					$id_fin_pro = "NULL";
				}
				if ($id_usuario_prof == "") {
					$id_usuario_prof = "NULL";
				}
				if ($ind_reoperacion == "") {
					$ind_reoperacion = "NULL";
				}
				if ($ind_reop_ent == "") {
					$ind_reop_ent = "NULL";
				}
				if ($fecha_cx_ant == "") {
					$fecha_cx_ant = "NULL";
				} else {
					$fecha_cx_ant = "STR_TO_DATE('".$fecha_cx_ant."', '%d/%m/%Y')";
				}
				if ($arch_cx == "") {
					$arch_cx = "NULL";
				} else {
					$arch_cx = "'".$arch_cx."'";
				}
				if ($arch_stickers == "") {
					$arch_stickers = "NULL";
				} else {
					$arch_stickers = "'".$arch_stickers."'";
				}
				
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
				
				//Temporal de procedimiento quirúrgicos
				$sql = "DELETE FROM temporal_cirugias_procedimientos
						WHERE id_usuario=".$id_usuario;
				
				$arrCampos[0] = "@id";
				if ($this->ejecutarSentencia($sql, $arrCampos)) {
					$j = 1;
					foreach ($array_procedimientos as $procedimiento_aux) {
						if ($procedimiento_aux["id_ojo"] == "") {
							$procedimiento_aux["id_ojo"] = "NULL";
						}
						if ($procedimiento_aux["via"] == "") {
							$procedimiento_aux["via"] = "NULL";
						} else {
							$procedimiento_aux["via"] = "'".$procedimiento_aux["via"]."'";
						}
						$sql = "INSERT INTO temporal_cirugias_procedimientos
								(id_usuario, cod_procedimiento, id_ojo, via_procedimiento)
								VALUES (".$id_usuario.", '".$procedimiento_aux["cod_procedimiento"]."', ".$procedimiento_aux["id_ojo"].", ".$procedimiento_aux["via"].")";
						
						$arrCampos[0] = "@id";
	                	$this->ejecutarSentencia($sql, $arrCampos);
						$j++;						  
					}
				}
				
				$sql = "CALL pa_crear_editar_cirugia(".$id_hc.", ".$id_admision.", ".$fecha_cx.", ".$id_convenio.", ".$id_amb_rea.", ".
					   $id_fin_pro.", ".$id_usuario_prof.", ".$ind_reoperacion.", ".$ind_reop_ent.", ".$fecha_cx_ant.", '".
					   $observaciones_cx."', ".$arch_cx.", ".$arch_stickers.", NULL, ".$id_usuario.", ".$tipo_guardar.", 1, @id)";
				//echo($sql);
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado = $arrResultado["@id"];
				
				return $resultado;
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Crear cirugía
		public function editar_cirugia_laser($id_hc, $id_admision, $fecha_cx, $id_convenio, $id_amb_rea, $id_fin_pro,
				$id_usuario_prof, $ind_reoperacion, $ind_reop_ent, $fecha_cx_ant, $observaciones_cx, $arch_stickers, $id_tipo_laser,
				$id_ojo, $num_turno, $id_tecnica_od, $microquerato_od, $num_placas_od, $tiempo_vacio_od, $uso_cuchilla_od,
				$bisagra_od, $tiempo_qx_od, $tipo_od, $esfera_od, $cilindro_od, $eje_od, $zona_optica_od, $ablacion_od,
				$esp_corneal_base_od, $humedad_od, $temperatura_od, $wtw_od, $id_tecnica_oi, $microquerato_oi,
				$num_placas_oi, $tiempo_vacio_oi, $uso_cuchilla_oi, $bisagra_oi, $tiempo_qx_oi, $tipo_oi, $esfera_oi,
				$cilindro_oi, $eje_oi, $zona_optica_oi, $ablacion_oi, $esp_corneal_base_oi, $humedad_oi, $temperatura_oi,
				$wtw_oi, $array_procedimientos, $array_diagnosticos, $tipo_guardar, $id_usuario) {
			try {
				if ($fecha_cx == "") {
					$fecha_cx = "NULL";
				} else {
					$fecha_cx = "STR_TO_DATE('".$fecha_cx."', '%d/%m/%Y')";
				}
				if ($id_convenio == "") {
					$id_convenio = "NULL";
				}
				if ($id_amb_rea == "") {
					$id_amb_rea = "NULL";
				}
				if ($id_fin_pro == "") {
					$id_fin_pro = "NULL";
				}
				if ($id_usuario_prof == "") {
					$id_usuario_prof = "NULL";
				}
				if ($ind_reoperacion == "") {
					$ind_reoperacion = "NULL";
				}
				if ($ind_reop_ent == "") {
					$ind_reop_ent = "NULL";
				}
				if ($fecha_cx_ant == "") {
					$fecha_cx_ant = "NULL";
				} else {
					$fecha_cx_ant = "STR_TO_DATE('".$fecha_cx_ant."', '%d/%m/%Y')";
				}
				if ($arch_stickers == "") {
					$arch_stickers = "NULL";
				} else {
					$arch_stickers = "'".$arch_stickers."'";
				}
				if ($id_tipo_laser == "") {
					$id_tipo_laser = "NULL";
				}
				if ($id_ojo == "") {
					$id_ojo = "NULL";
				}
				if ($num_turno == "") {
					$num_turno = "NULL";
				}
				if ($id_tecnica_od == "") {
					$id_tecnica_od = "NULL";
				}
				if ($num_placas_od == "") {
					$num_placas_od = "NULL";
				}
				if ($tiempo_vacio_od == "") {
					$tiempo_vacio_od = "NULL";
				}
				if ($uso_cuchilla_od == "") {
					$uso_cuchilla_od = "NULL";
				}
				if ($tiempo_qx_od == "") {
					$tiempo_qx_od = "NULL";
				}
				if ($ablacion_od == "") {
					$ablacion_od = "NULL";
				}
				if ($esp_corneal_base_od == "") {
					$esp_corneal_base_od = "NULL";
				}
				if ($id_tecnica_oi == "") {
					$id_tecnica_oi = "NULL";
				}
				if ($num_placas_oi == "") {
					$num_placas_oi = "NULL";
				}
				if ($tiempo_vacio_oi == "") {
					$tiempo_vacio_oi = "NULL";
				}
				if ($uso_cuchilla_oi == "") {
					$uso_cuchilla_oi = "NULL";
				}
				if ($tiempo_qx_oi == "") {
					$tiempo_qx_oi = "NULL";
				}
				if ($ablacion_oi == "") {
					$ablacion_oi = "NULL";
				}
				if ($esp_corneal_base_oi == "") {
					$esp_corneal_base_oi = "NULL";
				}
				
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
				
				//Temporal de procedimiento quirúrgicos
				$sql = "DELETE FROM temporal_cirugias_procedimientos
						WHERE id_usuario=".$id_usuario;
				
				$arrCampos[0] = "@id";
				if ($this->ejecutarSentencia($sql, $arrCampos)) {
					$j = 1;
					foreach ($array_procedimientos as $procedimiento_aux) {
						if ($procedimiento_aux["id_ojo"] == "") {
							$procedimiento_aux["id_ojo"] = "NULL";
						}
						if ($procedimiento_aux["via"] == "") {
							$procedimiento_aux["via"] = "NULL";
						} else {
							$procedimiento_aux["via"] = "'".$procedimiento_aux["via"]."'";
						}
						$sql = "INSERT INTO temporal_cirugias_procedimientos
								(id_usuario, cod_procedimiento, id_ojo, via_procedimiento)
								VALUES (".$id_usuario.", '".$procedimiento_aux["cod_procedimiento"]."', ".$procedimiento_aux["id_ojo"].", ".$procedimiento_aux["via"].")";
						
						$arrCampos[0] = "@id";
	                	$this->ejecutarSentencia($sql, $arrCampos);
						$j++;						  
					}
				}
				
				$sql = "CALL pa_crear_editar_cirugia_laser(".$id_hc.", ".$id_admision.", ".$fecha_cx.", ".$id_convenio.", ".$id_amb_rea.", ".
					   $id_fin_pro.", ".$id_usuario_prof.", ".$ind_reoperacion.", ".$ind_reop_ent.", ".$fecha_cx_ant.", '".$observaciones_cx."', ".
					   $arch_stickers.", ".$id_tipo_laser.", ".$id_ojo.", ".$num_turno.", ".$id_tecnica_od.", '".$microquerato_od."', ".
					   $num_placas_od.", ".$tiempo_vacio_od.", ".$uso_cuchilla_od.", '".$bisagra_od."', ".$tiempo_qx_od.", '".$tipo_od."', '".
					   $esfera_od."', '".$cilindro_od."', '".$eje_od."', '".$zona_optica_od."', ".$ablacion_od.", ".$esp_corneal_base_od.", '".
					   $humedad_od."', '".$temperatura_od."', '".$wtw_od."', ".$id_tecnica_oi.", '".$microquerato_oi."', ".$num_placas_oi.", ".
					   $tiempo_vacio_oi.", ".$uso_cuchilla_oi.", '".$bisagra_oi."', ".$tiempo_qx_oi.", '".$tipo_oi."', '".$esfera_oi."', '".
					   $cilindro_oi."', '".$eje_oi."', '".$zona_optica_oi."', ".$ablacion_oi.", ".$esp_corneal_base_oi.", '".$humedad_oi."', '".
					   $temperatura_oi."', '".$wtw_oi."', ".$id_usuario.", ".$tipo_guardar.", @id)";
				//echo($sql);
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado = $arrResultado["@id"];
				
				return $resultado;
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Editar evaluacion cirugía
		public function editar_evaluacion_cirugia_laser($id_hc, $id_admision, $txt_anotaciones_ev, $id_usuario) {
			try {
				
				if ($txt_anotaciones_ev == "") {
					$txt_anotaciones_ev = "NULL";
				}
				$sql = "CALL pa_editar_evaluacion_cirugia_laser(".$id_hc.", ".$id_admision.", '".$txt_anotaciones_ev."', ".$id_usuario.", @id)";
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado = $arrResultado["@id"];
				
				return $resultado;
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function editar_cirugia_stickers($id_hc, $ruta_arch_stickers, $id_usuario) {
			try {
				$sql = "CALL pa_editar_cirugia_stickers(".$id_hc.", '".$ruta_arch_stickers."', ".$id_usuario.", @id)";
				echo($sql);
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado = $arrResultado["@id"];
				
				return $resultado;
			} catch (Exception $e) {
				return "-2";
			}
		}
    }
?>
