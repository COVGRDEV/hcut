<?php
	require_once("DbHistoriaClinica.php");
	
	class DbConsultaOptometria extends DbHistoriaClinica {
		
		/**
		 * Obtener los datos de la consulta de optometría a partir del ID de la HC
		 */
		public function getConsultaOptometria($id_hc) {
			try {
				$sql = "SELECT O.*, DATE_FORMAT(HC.fecha_hora_hc, '%d/%m/%Y') AS fecha_hc_t,
						DM.nombre_detalle AS dominancia_ocular, lstp.nombre_detalle AS slct_lente, lstf.nombre_detalle AS slct_filtro, lstv.nombre_detalle AS tiempo_vigencia, 
						lstm.nombre_detalle AS periodo, LC.nombre_detalle AS lugar_cita, SD.tel_sede_det , SD.dir_sede_det, SD.email_sede_det, 
						sblod.nombre_detalle AS agudezaL_ojoD, sbcod.nombre_detalle AS agudezaC_ojoD,sbloi.nombre_detalle AS agudezaL_ojoI, sbcoi.nombre_detalle AS agudezaC_ojoI, A.id_lugar_cita
						FROM consultas_optometria O
						INNER JOIN historia_clinica HC ON O.id_hc=HC.id_hc
						LEFT JOIN listas_detalle lstp ON O.tipos_lentes_slct = lstp.id_detalle
						LEFT JOIN listas_detalle lstf ON O.tipo_filtro_slct = lstf.id_detalle
						LEFT JOIN listas_detalle lstv ON O.tiempo_vigencia_slct = lstv.id_detalle
						LEFT JOIN listas_detalle lstm ON O.tiempo_periodo = lstm.id_detalle
						LEFT JOIN listas_detalle DM ON O.cmb_dominancia_ocular=DM.id_detalle
						LEFT JOIN listas_detalle sblod ON O.subjetivo_lejos_od = sblod.id_detalle
						LEFT JOIN listas_detalle sbcod ON O.subjetivo_cerca_od = sbcod.id_detalle
						LEFT JOIN listas_detalle sbloi ON O.subjetivo_lejos_oi = sbloi.id_detalle
						LEFT JOIN listas_detalle sbcoi ON O.subjetivo_cerca_oi = sbcoi.id_detalle
						INNER JOIN admisiones A ON HC.id_admision = A.id_admision
						LEFT JOIN listas_detalle LC ON A.id_lugar_cita = LC.id_detalle
						LEFT JOIN sedes_det SD ON LC.id_detalle = SD.id_detalle
						WHERE O.id_hc=".$id_hc;
				//echo($sql);
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Obtener los datos de la última consulta de optometría hecha antes de un registro de historia clínica dado
		 */
		public function getConsultaOptometriaAnterior($id_hc) {
			try {
				$sql = "SELECT O.*, DATE_FORMAT(H2.fecha_hora_hc, '%d/%m/%Y') AS fecha_hc_t
						FROM historia_clinica HC
						INNER JOIN historia_clinica H2 ON HC.id_paciente=H2.id_paciente AND HC.fecha_hora_hc>H2.fecha_hora_hc
						INNER JOIN consultas_optometria O ON H2.id_hc=O.id_hc
						WHERE HC.id_hc=".$id_hc."
						AND H2.id_tipo_reg=1
						ORDER BY H2.fecha_hora_hc DESC";
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Obtener los datos de la consulta de optometría a partir del ID de la admisión asociada
		 */
		public function getConsultaOptometriaAdmision($id_admision) {
			try {
				$sql = "SELECT CO.*, DATE_FORMAT(HC.fecha_hora_hc, '%d/%m/%Y') AS fecha_hc_t,
						DM.nombre_detalle AS dominancia_ocular
						FROM consultas_optometria CO
						INNER JOIN historia_clinica HC ON CO.id_hc=HC.id_hc
						LEFT JOIN listas_detalle DM ON CO.cmb_dominancia_ocular=DM.id_detalle
						WHERE HC.id_admision=".$id_admision;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Crear consulta de optometria
		public function CrearConsultaOptometria($id_paciente, $id_tipo_reg, $id_usuario_crea, $id_admision) {
			try {
				$sql = "CALL pa_crear_consultas_optometria($id_paciente, $id_admision, $id_tipo_reg, $id_usuario_crea, @id)";
				//echo($sql);
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$id_historia_clinica = $arrResultado["@id"];
				
				return $id_historia_clinica;
			} catch (Exception $e) {
				return -2;
			}
		}
		
		public function EditarConsultaOptometria($id_hc_consulta, $hdd_id_admision, $txt_anamnesis, $avsc_lejos_od, $avsc_media_od, $avsc_cerca_od, $avsc_lejos_oi,
				$avsc_media_oi, $avsc_cerca_oi, $querato_k1_od, $querato_ejek1_od, $querato_dif_od, $querato_k1_oi, $querato_ejek1_oi, $querato_dif_oi, $refraobj_esfera_od,
				$refraobj_cilindro_od, $refraobj_eje_od, $refraobj_lejos_od, $refraobj_esfera_oi, $refraobj_cilindro_oi, $refraobj_eje_oi, $refraobj_lejos_oi,
				$subjetivo_esfera_od, $subjetivo_cilindro_od, $subjetivo_eje_od, $subjetivo_lejos_od, $subjetivo_media_od, $subjetivo_ph_od, $subjetivo_adicion_od,
				$subjetivo_cerca_od, $subjetivo_esfera_oi, $subjetivo_cilindro_oi, $subjetivo_eje_oi, $subjetivo_lejos_oi, $subjetivo_media_oi, $subjetivo_ph_oi,
				$subjetivo_adicion_oi, $subjetivo_cerca_oi, $cicloplejio_esfera_od, $cicloplejio_cilindro_od, $cicloplejio_eje_od, $cicloplejio_lejos_od,
				$cicloplejio_esfera_oi, $cicloplejio_cilindro_oi, $cicloplejio_eje_oi, $cicloplejio_lejos_oi, $refrafinal_esfera_od, $refrafinal_cilindro_od,
				$refrafinal_eje_od, $refrafinal_adicion_od, $refrafinal_esfera_oi, $refrafinal_cilindro_oi, $refrafinal_eje_oi, $refrafinal_adicion_oi, $id_usuario_crea,
				$tipo_guardar, /* $presion_intraocular_od, $presion_intraocular_oi, */ $diagnostico_optometria, $array_diagnosticos, $txt_observaciones_subjetivo,
				$txt_observaciones_rxfinal, $cmb_validar_consulta, $cmb_examinado_antes, $rx_gafas, $rx_lc, $rx_refractiva, $rx_ayudas_bv, $array_gafas, $array_ldc,
				$array_cxr, $array_ayudas_bv, $array_lenso, /* $cmb_paciente_dilatado, */ $observaciones_optometricas_finales, $cmb_dominancia_ocular,
				$observaciones_admision, $nombre_usuario_alt, $tipo_lente,$tipos_lentes_slct,$tipo_filtro_slct, $tiempo_vigencia_slct, $tiempo_periodo, $distancia_pupilar, 					                $form_cantidad,  $txt_observaciones_optometria, $array_antecedentes) {
			try {
				//Antedentes medicos
				$sql = "DELETE FROM temporal_antecedentes
						WHERE id_hc=".$id_hc_consulta."
						AND id_usuario=".$id_usuario_crea."
						AND tipo_antecedente=2";
				//echo($sql."<br />");
				$arrCampos_delete[0] = "@id";
				if ($this->ejecutarSentencia($sql, $arrCampos_delete)) {
					for ($i = 0; $i < count($array_antecedentes); $i++) {
						if ($array_antecedentes[$i]["texto_antecedente"] != "") {
							$sql = "INSERT INTO temporal_antecedentes
									(id_hc, id_usuario, tipo_antecedente, id_antecedente, val_texto)
									VALUES (".$id_hc_consulta.", ".$id_usuario_crea.", 2, ".
									$array_antecedentes[$i]["id_antecedentes_medicos"].", '".$array_antecedentes[$i]["texto_antecedente"]."')";
							
							//echo($sql."<br />");
							$arrCampos[0] = "@id";
							$this->ejecutarSentencia($sql, $arrCampos);
						}
					}
				}
				
				//Diagnósticos
				$sql_delete_diagnosticos = "DELETE FROM temporal_diagnosticos
												WHERE id_hc=".$id_hc_consulta."
												AND id_usuario=".$id_usuario_crea;
				//echo($sql."<br />");
				$arrCampos_delete[0] = "@id";
				if ($this->ejecutarSentencia($sql_delete_diagnosticos, $arrCampos_delete)) {
					$j = 1;
					foreach ($array_diagnosticos as $fila_diagnosticos) {
						$ciex_diagnostico = $fila_diagnosticos[0];
						$valor_ojos = $fila_diagnosticos[1];
						$sql_insert_diagnosticos = "INSERT INTO temporal_diagnosticos (id_hc, id_usuario, cod_ciex, id_ojo, orden)
													VALUES (".$id_hc_consulta.", ".$id_usuario_crea.", '".$ciex_diagnostico."', '".$valor_ojos."', ".$j.")";
						
						//echo $sql_insert_diagnosticos."<br />";						  
						$arrCampos[0] = "@id";
						$this->ejecutarSentencia($sql_insert_diagnosticos, $arrCampos);
						$j = $j + 1;
					}
				}
				
				// Procesar gafas: 
				$sql_delete_detalle_tmp = "DELETE FROM consultas_optometria_gafas_tmp
										   WHERE id_hc=".$id_hc_consulta."
										   AND id_usuario_crea=".$id_usuario_crea;
				
				$arrCampos_delete[0] = "@id";
				if ($this->ejecutarSentencia($sql_delete_detalle_tmp, $arrCampos_delete)) {
					$j = 1;
					foreach ($array_gafas as $fila_detalle) {
						$tipo_lente2 = $fila_detalle[0];
						$tipo_lente_det = $fila_detalle[1];
						$tipo_lente_otro = $fila_detalle[2];
						$tipo_filtro = $fila_detalle[3];
						$tiempo_rx = $fila_detalle[4];
						$und_tiempo_rx = $fila_detalle[5];
						$grado_satisfacc = $fila_detalle[6];
						
						$lenso_esfera_od = $fila_detalle[7];
						$lenso_cilindro_od = $fila_detalle[8];
						$lenso_eje_od = $fila_detalle[9];
						$lenso_adicion_od = $fila_detalle[10];
						$lenso_lejos_od = $fila_detalle[11];
						$lenso_media_od = $fila_detalle[12];
						$lenso_cerca_od = $fila_detalle[13];
						$lenso_esfera_oi = $fila_detalle[14];
						$lenso_cilindro_oi = $fila_detalle[15];
						$lenso_eje_oi = $fila_detalle[16];
						$lenso_adicion_oi = $fila_detalle[17];
						$lenso_lejos_oi = $fila_detalle[18];
						$lenso_media_oi = $fila_detalle[19];
						$lenso_cerca_oi = $fila_detalle[20];
						
						$prisma_tipo_od = $fila_detalle[21];
						$prisma_potencia_od = $fila_detalle[22];
						$prisma_base_od = $fila_detalle[23];
						$prisma_tipo_oi = $fila_detalle[24];
						$prisma_potencia_oi = $fila_detalle[25];
						$prisma_base_oi = $fila_detalle[26];
						
						$ind_presentes = $fila_detalle[27];
						
						if ($tipo_lente2 == "") {
							$tipo_lente2 = "NULL";
						}
						if ($tipo_lente_det == "") {
							$tipo_lente_det = "NULL";
						}
						if ($tipo_lente_otro == "") {
							$tipo_lente_otro = "";
						}
						if ($tipo_filtro == "") {
							$tipo_filtro = "NULL";
						}
						if ($tiempo_rx == "") {
							$tiempo_rx = "NULL";
						}
						if ($und_tiempo_rx == "") {
							$und_tiempo_rx = "NULL";
						}
						if ($grado_satisfacc == "") {
							$grado_satisfacc = "NULL";
						}
						
						if ($lenso_esfera_od == "") {
							$lenso_esfera_od = "";
						}
						if ($lenso_cilindro_od == "") {
							$lenso_cilindro_od = "";
						}
						if ($lenso_eje_od == "") {
							$lenso_eje_od = "";
						}
						if ($lenso_adicion_od == "") {
							$lenso_adicion_od = "";
						}
						if ($lenso_lejos_od == "") {
							$lenso_lejos_od = "NULL";
						}
						if ($lenso_media_od == "") {
							$lenso_media_od = "NULL";
						}
						if ($lenso_cerca_od == "") {
							$lenso_cerca_od = "NULL";
						}
						if ($lenso_esfera_oi == "") {
							$lenso_esfera_oi = "";
						}
						if ($lenso_cilindro_oi == "") {
							$lenso_cilindro_oi = "";
						}
						if ($lenso_eje_oi == "") {
							$lenso_eje_oi = "";
						}
						if ($lenso_adicion_oi == "") {
							$lenso_adicion_oi = "";
						}
						if ($lenso_lejos_oi == "") {
							$lenso_lejos_oi = "NULL";
						}
						if ($lenso_media_oi == "") {
							$lenso_media_oi = "NULL";
						}
						if ($lenso_cerca_oi == "") {
							$lenso_cerca_oi = "NULL";
						}
						
						if ($prisma_tipo_od == "") {
							$prisma_tipo_od = "NULL";
						}
						if ($prisma_potencia_od == "") {
							$prisma_potencia_od = "NULL";
						}
						if ($prisma_base_od == "") {
							$prisma_base_od = "NULL";
						}
						if ($prisma_tipo_oi == "") {
							$prisma_tipo_oi = "NULL";
						}
						if ($prisma_potencia_oi == "") {
							$prisma_potencia_oi = "NULL";
						}
						if ($prisma_base_oi == "") {
							$prisma_base_oi = "NULL";
						}
	
						if ($ind_presentes == "") {
							$ind_presentes = "1";
						}
						
						$sql_insert_detalle = "INSERT INTO consultas_optometria_gafas_tmp
												(id_hc, ind_presentes, tipo_lente, tipo_lente_det, tipo_lente_otro, tipo_filtro, tiempo_rx, und_tiempo_rx, grado_satisfacc,
												lenso_esfera_od, lenso_cilindro_od, lenso_eje_od, lenso_adicion_od, lenso_lejos_od, lenso_media_od, lenso_cerca_od,
												lenso_esfera_oi, lenso_cilindro_oi, lenso_eje_oi, lenso_adicion_oi, lenso_lejos_oi, lenso_media_oi, lenso_cerca_oi,
												prisma_tipo_od, prisma_potencia_od, prisma_base_od, prisma_tipo_oi, prisma_potencia_oi, prisma_base_oi, id_usuario_crea)
												VALUES (".$id_hc_consulta.", ".$ind_presentes.", ".$tipo_lente2.", ".$tipo_lente_det.", '".
												$tipo_lente_otro."', ".$tipo_filtro.", ".$tiempo_rx.", ".$und_tiempo_rx.", ".$grado_satisfacc.", '".
												$lenso_esfera_od."', '".$lenso_cilindro_od."', '".$lenso_eje_od."', '".$lenso_adicion_od."', ".
												$lenso_lejos_od.", ".$lenso_media_od.", ".$lenso_cerca_od.", '".
												$lenso_esfera_oi."', '".$lenso_cilindro_oi."', '".$lenso_eje_oi."', '".$lenso_adicion_oi."', ".
												$lenso_lejos_oi.", ".$lenso_media_oi.", ".$lenso_cerca_oi.", ".
												$prisma_tipo_od.", ".$prisma_potencia_od.", ".$prisma_base_od.", ".
												$prisma_tipo_oi.", ".$prisma_potencia_oi.", ".$prisma_base_oi.", ".$id_usuario_crea.")";
						
						//echo "<br>ins TMP detalle GAFAS: ".$sql_insert_detalle; 
						$arrCampos[0] = "@id";
						$this->ejecutarSentencia($sql_insert_detalle, $arrCampos);
						$j = $j + 1;
					}
				}
				
				// Procesar lentes de contacto: 
				$sql_delete_detalle_tmp = "DELETE FROM consultas_optometria_ldc_tmp
										   WHERE id_hc=".$id_hc_consulta."
										   AND id_usuario_crea=".$id_usuario_crea;
				
				$arrCampos_delete[0] = "@id";
				if ($this->ejecutarSentencia($sql_delete_detalle_tmp, $arrCampos_delete)) {
					$j = 1;
					foreach ($array_ldc as $fila_detalle) {
						$tipo_ldc = $fila_detalle[0];
						$tipo_ldc_det = $fila_detalle[1];
						$disenio = $fila_detalle[2];
						$tipo_duracion = $fila_detalle[3];
						$ojo = $fila_detalle[4];
						$tiempo_uso = $fila_detalle[5];
						$und_tiempo_uso = $fila_detalle[6];
						$tiempo_no_uso = $fila_detalle[7];
						$und_tiempo_no_uso = $fila_detalle[8];
						$modalidad_uso = $fila_detalle[9];
						$tiempo_reemplazo = $fila_detalle[10];
						$grado_satisfacc = $fila_detalle[11];
						$tipo_ldc_otro = $fila_detalle[12];
						$tipo_duracion_det = $fila_detalle[13];
						$ind_presentes = $fila_detalle[14];
						
						if ($tipo_ldc == "") {
							$tipo_ldc = "NULL";
						}
						if ($tipo_ldc_det == "") {
							$tipo_ldc_det = "NULL";
						}
						if ($disenio == "") {
							$disenio = "NULL";
						}
						if ($tipo_duracion == "") {
							$tipo_duracion = "NULL";
						}
						if ($ojo == "") {
							$ojo = "NULL";
						}
						if ($tiempo_uso == "") {
							$tiempo_uso = "NULL";
						}
						if ($und_tiempo_uso == "") {
							$und_tiempo_uso = "NULL";
						}
						if ($tiempo_no_uso == "") {
							$tiempo_no_uso = "NULL";
						}
						if ($und_tiempo_no_uso == "") {
							$und_tiempo_no_uso = "NULL";
						}
						if ($modalidad_uso == "") {
							$modalidad_uso = "NULL";
						}
						if ($tiempo_reemplazo == "") {
							$tiempo_reemplazo = "NULL";
						}
						if ($grado_satisfacc == "") {
							$grado_satisfacc = "NULL";
						}
						if ($tipo_ldc_otro == "") {
							$tipo_ldc_otro = "";
						}
						if ($tipo_duracion_det == "") {
							$tipo_duracion_det = "NULL";
						}
						if ($ind_presentes == "") {
							$ind_presentes = "1";
						}
						
						$sql_insert_detalle = "INSERT INTO consultas_optometria_ldc_tmp (id_hc, ind_presentes, tipo_ldc, tipo_ldc_det, tipo_ldc_otro, disenio,
												tipo_duracion, tipo_duracion_det, ojo, tiempo_uso, und_tiempo_uso, tiempo_no_uso, und_tiempo_no_uso, 
												modalidad_uso, tiempo_reemplazo, grado_satisfacc, id_usuario_crea)
												VALUES (".$id_hc_consulta.", ".$ind_presentes.", ".$tipo_ldc.", ".$tipo_ldc_det.", '".$tipo_ldc_otro."', ".$disenio.", ".
												$tipo_duracion.", ".$tipo_duracion_det.", ".$ojo.", ".$tiempo_uso.", ".$und_tiempo_uso.", ".$tiempo_no_uso.", ".
												$und_tiempo_no_uso.", ".$modalidad_uso.", ".$tiempo_reemplazo.", ".$grado_satisfacc.", ".$id_usuario_crea.")";
						
						//echo "<br>ins TMP detalle LDC: ".$sql_insert_detalle; 
						$arrCampos[0] = "@id";
						$this->ejecutarSentencia($sql_insert_detalle, $arrCampos);
						$j = $j + 1;
					}
				}
				
				// Procesar ayudas baja visión: 
				$sql_delete_detalle_tmp = "DELETE FROM consultas_optometria_ayudas_bv_tmp
										   WHERE id_hc=".$id_hc_consulta."
										   AND id_usuario_crea=".$id_usuario_crea;
				$arrCampos_delete[0] = "@id";
				if ($this->ejecutarSentencia($sql_delete_detalle_tmp, $arrCampos_delete)) {
					$j = 1;
					foreach ($array_ayudas_bv as $fila_detalle) {
						$tipo_abv = $fila_detalle[0];
						$tipo_abv_det = $fila_detalle[1];
						$grado_satisfacc = $fila_detalle[2];
						$tipo_abv_otro = $fila_detalle[3];
						$ind_presentes = $fila_detalle[4];
						
						if ($tipo_abv == "") {
							$tipo_abv = "NULL";
						}
						if ($tipo_abv_det == "") {
							$tipo_abv_det = "NULL";
						}
						if ($grado_satisfacc == "") {
							$grado_satisfacc = "NULL";
						}
						if ($tipo_abv_otro == "") {
							$tipo_abv_otro = "";
						}
						if ($ind_presentes == "") {
							$ind_presentes = "1";
						}
						
						$sql_insert_detalle = "INSERT INTO consultas_optometria_ayudas_bv_tmp
												(id_hc, ind_presentes, tipo_abv, tipo_abv_det, tipo_abv_otro, grado_satisfacc, id_usuario_crea)
												VALUES (".$id_hc_consulta.", ".$ind_presentes.", ".$tipo_abv.", ".
												$tipo_abv_det.", '".$tipo_abv_otro."', ".$grado_satisfacc.", ".$id_usuario_crea.")";
						
						//echo "<br>ins TMP detalle ABV: ".$sql_insert_detalle; 
						$arrCampos[0] = "@id";
						$this->ejecutarSentencia($sql_insert_detalle, $arrCampos);
						$j = $j + 1;
					}
				}
				
				// Procesar cirugía refractivas: 
				$sql_delete_detalle_tmp = "DELETE FROM consultas_optometria_cxr_tmp
										   WHERE id_hc=".$id_hc_consulta."
										   AND id_usuario_crea=".$id_usuario_crea;
				$arrCampos_delete[0] = "@id";
				if ($this->ejecutarSentencia($sql_delete_detalle_tmp, $arrCampos_delete)) {
					$j = 1;
					foreach ($array_cxr as $fila_detalle) {
						$tipo_cxr = $fila_detalle[0];
						$tipo_cxr_det = $fila_detalle[1];
						$ind_ajuste = $fila_detalle[2];
						$ojo = $fila_detalle[3];
						$tiempo_uso = $fila_detalle[4];
						$und_tiempo_uso = $fila_detalle[5];
						$entidad = $fila_detalle[6];
						$id_usuario_cirujano = $fila_detalle[7];
						$cirujano_otro = $fila_detalle[8];
						$grado_satisfacc = $fila_detalle[9];
						$tipo_cxr_otro = $fila_detalle[10];
						
						if ($tipo_cxr == "") {
							$tipo_cxr = "NULL";
						}
						if ($tipo_cxr_det == "") {
							$tipo_cxr_det = "NULL";
						}
						if ($ind_ajuste == "") {
							$ind_ajuste = "0";
						}
						if ($ojo == "") {
							$ojo = "NULL";
						}
						if ($tiempo_uso == "") {
							$tiempo_uso = "NULL";
						}
						if ($und_tiempo_uso == "") {
							$und_tiempo_uso = "NULL";
						}
						if ($entidad == "") {
							$entidad = "NULL";
						}
						if ($id_usuario_cirujano == "") {
							$id_usuario_cirujano = "NULL";
						}
						if ($cirujano_otro == "") {
							$cirujano_otro = "";
						}
						if ($grado_satisfacc == "") {
							$grado_satisfacc = "NULL";
						}
						if ($tipo_cxr_otro == "") {
							$tipo_cxr_otro = "";
						}
						
						$sql_insert_detalle = "INSERT INTO consultas_optometria_cxr_tmp
											(id_hc, tipo_cxr, tipo_cxr_det, tipo_cxr_otro, ind_ajuste, ojo, tiempo_uso,
											und_tiempo_uso, entidad, id_usuario_cirujano, cirujano_otro, grado_satisfacc, id_usuario_crea)
											VALUES (".$id_hc_consulta.", ".$tipo_cxr.", ".$tipo_cxr_det.", '".$tipo_cxr_otro."', ".$ind_ajuste.", ".$ojo.", ".$tiempo_uso.", ".
											$und_tiempo_uso.", ".$entidad.", ".$id_usuario_cirujano.", '".$cirujano_otro."', ".$grado_satisfacc.", ".$id_usuario_crea.")";
						
						//echo "<br>ins TMP detalle cxr: ".$sql_insert_detalle; 
						$arrCampos[0] = "@id";
						$this->ejecutarSentencia($sql_insert_detalle, $arrCampos);
						$j = $j + 1;
					}
				}
				
				if ($cmb_examinado_antes == "") {
					$cmb_examinado_antes = 0;
				}
				if ($cmb_paciente_dilatado == "") {
					$cmb_paciente_dilatado = 0;
				}
				
				if ($cmb_dominancia_ocular == "") {
					$cmb_dominancia_ocular = "NULL";
				}
				
				if ($nombre_usuario_alt != "") {
					$nombre_usuario_alt = "'".$nombre_usuario_alt."'";
				} else {
					$nombre_usuario_alt = "NULL";
				}
				
				if ($observaciones_admision != "") {
					$observaciones_admision = "'".$observaciones_admision."'";
				} else {
					$observaciones_admision = "NULL";
				}
				
				if ($tipo_lente != "") {
					$tipo_lente = "'".$tipo_lente."'";
				} else {
					$tipo_lente = "NULL";
				}
				if ($tipos_lentes_slct != "") {
					$tipos_lentes_slct = "'".$tipos_lentes_slct."'";
				} else {
					$tipos_lentes_slct = "NULL";
				}
				
				if ($tipo_filtro_slct != "") {
					$tipo_filtro_slct = "'".$tipo_filtro_slct."'";
				} else {
					$tipo_filtro_slct = "NULL";
				}
				
				if ($tiempo_vigencia_slct != "") {
					$tiempo_vigencia_slct = "'".$tiempo_vigencia_slct."'";
				} else {
					$tiempo_vigencia_slct = "NULL";
				}
				
				if ($tiempo_periodo != "") {
					$tiempo_periodo = "'".$tiempo_periodo."'";
				} else {
					$tiempo_periodo = "NULL";
				}
				
				if ($distancia_pupilar != "") {
					$distancia_pupilar = "'".$distancia_pupilar."'";
				} else {
					$distancia_pupilar = "NULL";
				}
				
				if ($form_cantidad != "") {
					$form_cantidad = "'".$form_cantidad."'";
				} else {
					$form_cantidad = "NULL";
				}
				
				if ($txt_observaciones_optometria != "") {
					$txt_observaciones_optometria = "'".$txt_observaciones_optometria."'";
				} else {
					$txt_observaciones_optometria = "NULL";
				}
				
				$sql = "CALL pa_editar_consultas_optometria(".$id_hc_consulta.", ".$hdd_id_admision.", '".$txt_anamnesis."', ".
						$avsc_lejos_od.", ".$avsc_media_od.", ".$avsc_cerca_od.", ".$avsc_lejos_oi.", ".$avsc_media_oi.", ".$avsc_cerca_oi.", '".
						$querato_k1_od."', '".$querato_ejek1_od."', '".$querato_dif_od."', '".$querato_k1_oi."', '".$querato_ejek1_oi."', '".$querato_dif_oi."', '".
						$refraobj_esfera_od."', '".$refraobj_cilindro_od."', '".$refraobj_eje_od."', ".$refraobj_lejos_od.", '".
						$refraobj_esfera_oi."', '".$refraobj_cilindro_oi."', '".$refraobj_eje_oi."', ".$refraobj_lejos_oi.", '".
						$subjetivo_esfera_od."', '".$subjetivo_cilindro_od."', '".$subjetivo_eje_od."', ".$subjetivo_lejos_od.", ".
						$subjetivo_media_od.", ".$subjetivo_ph_od.", '".$subjetivo_adicion_od."', ".$subjetivo_cerca_od.", '".
						$subjetivo_esfera_oi."', '".$subjetivo_cilindro_oi."', '".$subjetivo_eje_oi."', ".$subjetivo_lejos_oi.", ".
						$subjetivo_media_oi.", ".$subjetivo_ph_oi.", '".$subjetivo_adicion_oi."', ".$subjetivo_cerca_oi.", '".
						$cicloplejio_esfera_od."', '".$cicloplejio_cilindro_od."', '".$cicloplejio_eje_od."', ".$cicloplejio_lejos_od.", '".
						$cicloplejio_esfera_oi."', '".$cicloplejio_cilindro_oi."', '".$cicloplejio_eje_oi."', ".$cicloplejio_lejos_oi.", '".
						$refrafinal_esfera_od."', '".$refrafinal_cilindro_od."', '".$refrafinal_eje_od."', '".$refrafinal_adicion_od."', '".
						$refrafinal_esfera_oi."', '".$refrafinal_cilindro_oi."', '".$refrafinal_eje_oi."', '".$refrafinal_adicion_oi."', ".
						$id_usuario_crea.", ".$tipo_guardar.", '"./* $presion_intraocular_od."', '".$presion_intraocular_oi."', '". */$diagnostico_optometria."', '".
						$txt_observaciones_subjetivo."', '".$txt_observaciones_rxfinal."', ".$cmb_validar_consulta.", ".$cmb_examinado_antes.", ".$rx_gafas.", ".
						$rx_lc.", ".$rx_refractiva.", ".$rx_ayudas_bv./* ", ".$cmb_paciente_dilatado. */", '".$observaciones_optometricas_finales."', ".
						$cmb_dominancia_ocular.",".$observaciones_admision.", ".$nombre_usuario_alt.", ".$tipo_lente.", ".$tipos_lentes_slct.",".$tipo_filtro_slct.",".                        $tiempo_vigencia_slct." ,".$tiempo_periodo.", ".$distancia_pupilar.", ".$form_cantidad.",  ".$txt_observaciones_optometria.", @id)";
				//echo($sql."<br />");
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$out_ind_opt = $arrResultado["@id"];
				
				return $out_ind_opt;
			} catch (Exception $e) {
				return array();
			}
		}
		
		/* Consultar gafas en la HC */
		public function getHcGafas($id_hc) {
			try {
				$sql = "SELECT C.*
						FROM consultas_optometria_gafas C, historia_clinica HC
						WHERE C.id_hc=".$id_hc."
							AND C.id_hc=HC.id_hc";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/* Consultar lentes de contacto en la HC */
		public function getHcLentesDeContacto($id_hc) {
			try {
				$sql = "SELECT C.*
							FROM consultas_optometria_ldc C, historia_clinica HC
							WHERE C.id_hc=".$id_hc."
							AND C.id_hc=HC.id_hc";
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/* Consultar ayudas en baja visión en la HC */
		public function getHcAyudasBajaVision($id_hc) {
			try {
				$sql = "SELECT C.*
							FROM consultas_optometria_ayudas_bv C, historia_clinica HC
							WHERE C.id_hc=".$id_hc."
							AND C.id_hc=HC.id_hc";
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/* Consultar cirugías refractivas en la HC */
		public function getHcCirugiasRefractivas($id_hc) {
			try {
				$sql = "SELECT C.*
							FROM consultas_optometria_cxr C, historia_clinica HC
							WHERE C.id_hc=".$id_hc."
							AND C.id_hc=HC.id_hc";
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
	}
?>
