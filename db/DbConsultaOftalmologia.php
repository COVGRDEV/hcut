<?php
	require_once("DbHistoriaClinica.php");
	
	class DbConsultaOftalmologia extends DbHistoriaClinica {
		/**
		 * Obtener los datos de a consulta de oftalmologia a partir del ID de la HC
		 */
		public function getConsultaOftalmologia($id_hc) {
	        try {
	            $sql = "SELECT *, DATE_FORMAT(tonometria_fecha_hora, '%d/%m/%Y %H:%i') AS fecha_hora_tonometria
						FROM consultas_oftalmologia
						WHERE id_hc=".$id_hc;
				
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		//Crear consulta de oftalmologia
		public function CrearConsultaOftalmologia($id_paciente, $id_tipo_reg, $id_usuario_crea, $id_admision, $ind_preconsulta) {
	        try {
	            $sql = "CALL pa_crear_consultas_oftalmologia(".$id_paciente.", ".$id_admision.", ".$id_tipo_reg.", ".$ind_preconsulta.", ".$id_usuario_crea.", @id)";
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$id_historia_clinica=$arrResultado["@id"];
				
				return $id_historia_clinica;
	        } catch (Exception $e) {
	            return -2;
	        }
    	}
		
		//Crear consulta de oftalmologia
		public function EditarConsultaOftalmologia($id_hc, $hdd_id_admision, $enfermedad_actual, $muscular_balance, $muscular_motilidad, $muscular_ppc,
				$biomi_orbita_parpados_od, $biomi_sist_lagrimal_od, $biomi_conjuntiva_od, $biomi_cornea_od, $biomi_cam_anterior_od, $biomi_iris_od, $biomi_cristalino_od,
				$biomi_vanherick_od, $biomi_orbita_parpados_oi, $biomi_sist_lagrimal_oi, $biomi_conjuntiva_oi, $biomi_cornea_oi, $biomi_cam_anterior_oi, $biomi_iris_oi,
				$biomi_cristalino_oi, $biomi_vanherick_oi, $goniosco_superior_od, $goniosco_inferior_od, $goniosco_nasal_od, $goniosco_temporal_od, $goniosco_superior_oi,
				$goniosco_inferior_oi, $goniosco_nasal_oi, $goniosco_temporal_oi, 
				$tonometria_nervio_optico_od, $tonometria_macula_od, $tonometria_periferia_od, $tonometria_vitreo_od,
				$tonometria_nervio_optico_oi, $tonometria_macula_oi, $tonometria_periferia_oi, $tonometria_vitreo_oi, $diagnostico_oftalmo, $solicitud_examenes,
				$tratamiento_oftalmo, $array_antecedentes, $img_biomiocroscopia, $img_tonometria_od,
				$img_tonometria_oi, $tipo_guardar, $id_usuario_crea, $observaciones_gonioscopia, $medicamentos_oftalmo,
				$array_diagnosticos, $nombre_usuario_alt, $nombre_usuario_preconsulta, $array_tonometria, $observaciones_tonometria, $ind_formula_gafas,
				$ind_eval_muscular, $mapa_hc_listas_rec) {
			try {
				//Para tonometria
				$this->crear_registros_temp_tonometria($id_hc, $array_tonometria, $id_usuario_crea);
				
				//Para Diagnosticos
				$sql = "DELETE FROM temporal_diagnosticos
						WHERE id_hc=".$id_hc."
						AND id_usuario=".$id_usuario_crea;
				$arrCampos_delete[0] = "@id";
				if ($this->ejecutarSentencia($sql, $arrCampos_delete)) {
					$j = 1;
					foreach ($array_diagnosticos as $fila_diagnosticos) {
						$ciex_diagnostico = $fila_diagnosticos[0];
						$valor_ojos = $fila_diagnosticos[1];
						$sql = "INSERT INTO temporal_diagnosticos (id_hc, id_usuario, cod_ciex, id_ojo, orden)
								VALUES (".$id_hc.", ".$id_usuario_crea.", '".$ciex_diagnostico."', '".$valor_ojos."', $j)";
						
						$arrCampos[0] = "@id";
	                	$this->ejecutarSentencia($sql, $arrCampos);
						$j++;
					}
				}
				
				//Antedentes medicos
				$sql = "DELETE FROM temporal_antecedentes
						WHERE id_hc=".$id_hc."
						AND id_usuario=".$id_usuario_crea."
						AND tipo_antecedente=2";
				
				//echo($sql."<br />");
				$arrCampos_delete[0] = "@id";
				if ($this->ejecutarSentencia($sql, $arrCampos_delete)) {
					for ($i = 0; $i < count($array_antecedentes); $i++) {
						if ($array_antecedentes[$i]["texto_antecedente"] != "") {
							$sql = "INSERT INTO temporal_antecedentes
									(id_hc, id_usuario, tipo_antecedente, id_antecedente, val_texto)
									VALUES (".$id_hc.", ".$id_usuario_crea.", 2, ".
									$array_antecedentes[$i]["id_antecedentes_medicos"].", '".$array_antecedentes[$i]["texto_antecedente"]."')";
							
							//echo($sql."<br />");
							$arrCampos[0] = "@id";
							$this->ejecutarSentencia($sql, $arrCampos);
						}
					}
				}
				
				//Para listas recursivas
				$sql = "DELETE FROM temporal_hc_listas_recursivas
						WHERE id_usuario=".$id_usuario_crea."
						AND id_hc=".$id_hc;
				$arrCampos_delete[0] = "@id";
				
				//$dbListas
				if ($this->ejecutarSentencia($sql, $arrCampos_delete)) {
					foreach ($mapa_hc_listas_rec as $grupo_registro_aux => $lista_rec_aux) {
						foreach ($lista_rec_aux as $arr_valores_aux) {
							$detalle_aux = $arr_valores_aux["lista"];
							$valor_aux = $arr_valores_aux["valor"];
							if ($valor_aux != "") {
								$valor_aux = "'".$valor_aux."'";
							} else {
								$valor_aux = "NULL";
							}
							$sql = "INSERT INTO temporal_hc_listas_recursivas
									(id_usuario, id_hc, grupo_registro, id_detalle, id_lista, valor)
									VALUES (".$id_usuario_crea.", ".$id_hc.", '".$grupo_registro_aux."', ".
									$detalle_aux["id_detalle"].", ".$detalle_aux["id_lista"].", ".$valor_aux.")";
							
							$arrCampos[0] = "@id";
							$this->ejecutarSentencia($sql, $arrCampos);
						}
					}
				}
				
				if ($nombre_usuario_alt != "") {
					$nombre_usuario_alt = "'".$nombre_usuario_alt."'";
				} else {
					$nombre_usuario_alt = "NULL";
				}
				if ($nombre_usuario_preconsulta != "") {
					$nombre_usuario_preconsulta = "'".$nombre_usuario_preconsulta."'";
				} else {
					$nombre_usuario_preconsulta = "NULL";
				}
				if ($ind_formula_gafas == "") {
					$ind_formula_gafas = "NULL";
				}
				
				$sql = "CALL pa_editar_consultas_oftalmologia(".$id_hc.", ".$hdd_id_admision.", '".$enfermedad_actual."', '".$muscular_balance."', '".
						$muscular_motilidad."', '".$muscular_ppc."', '".$biomi_orbita_parpados_od."', '".$biomi_sist_lagrimal_od."', '".$biomi_conjuntiva_od."', '".
						$biomi_cornea_od."', '".$biomi_cam_anterior_od."', '".$biomi_iris_od."', '".$biomi_cristalino_od."', '".$biomi_vanherick_od."', '".
						$biomi_orbita_parpados_oi."', '".$biomi_sist_lagrimal_oi."', '".$biomi_conjuntiva_oi."', '".$biomi_cornea_oi."', '".$biomi_cam_anterior_oi."', '".
						$biomi_iris_oi."', '".$biomi_cristalino_oi."', '".$biomi_vanherick_oi."', '".$goniosco_superior_od."', '".$goniosco_inferior_od."', '".
						$goniosco_nasal_od."', '".$goniosco_temporal_od."', '".$goniosco_superior_oi."', '".$goniosco_inferior_oi."', '".$goniosco_nasal_oi."', '".
						$goniosco_temporal_oi."', '".$tonometria_nervio_optico_od."', '".$tonometria_macula_od."', '".$tonometria_periferia_od."', '".
						$tonometria_vitreo_od."', '".$tonometria_nervio_optico_oi."', '".$tonometria_macula_oi."', '".$tonometria_periferia_oi."', '".
						$tonometria_vitreo_oi."', '".$diagnostico_oftalmo."', '".$solicitud_examenes."', '".$tratamiento_oftalmo."', '".$img_biomiocroscopia."', '".
						$img_tonometria_od."', '".$img_tonometria_oi."', '".$observaciones_gonioscopia."', '".$medicamentos_oftalmo."', ".
						$nombre_usuario_alt.", ".$nombre_usuario_preconsulta.", '".$observaciones_tonometria."', ".$ind_formula_gafas.", ".$ind_eval_muscular.", ".
						$tipo_guardar.", ".$id_usuario_crea.", @id)";
				
				//echo($sql);
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$out_ind_oft = $arrResultado["@id"];
				return $out_ind_oft;
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Obtener el listado de los antecedentes medicos
		 * $tipo: 1=listado formulario; 2=Listado agregar; 3=Listado agregado
		 */
		public function getAntecedentesMedicos($id_hc, $tipo, $array){
			try {
	            if ($id_hc == '') {
	            	$id_hc = 0;
	            }
				if ($tipo == 1) { //Listado formulario
					$sql = "SELECT a.*, hca.valor_antecedentes_medicos
							FROM antecedentes_medicos a
							LEFT JOIN hc_antecedentes_medicos hca ON a.id_antecedentes_medicos = hca.id_antecedentes_medicos AND id_hc=".$id_hc."
							WHERE a.ind_activo=1
							AND a.ind_inicial=1
							UNION
							SELECT a.*, hca.valor_antecedentes_medicos
							FROM antecedentes_medicos a
							INNER JOIN hc_antecedentes_medicos hca ON a.id_antecedentes_medicos = hca.id_antecedentes_medicos AND id_hc=".$id_hc."
							WHERE a.ind_activo=1
							AND a.ind_inicial=0
							ORDER BY orden, nombre_antecedentes_medicos";
				} else if ($tipo == 2) { //Listado para agregar
					$sql = "SELECT a.*
							FROM antecedentes_medicos a
							WHERE a.ind_activo=1
							AND a.ind_inicial=0
							AND a.id_antecedentes_medicos NOT IN (SELECT hca.id_antecedentes_medicos FROM hc_antecedentes_medicos hca WHERE hca.id_hc=".$id_hc.")
							ORDER BY a.orden, a.nombre_antecedentes_medicos";
				} else if ($tipo == 3) { //Listado agregado
					$sql = "SELECT a.*
							FROM antecedentes_medicos a
							WHERE a.ind_activo=1
							AND a.id_antecedentes_medicos IN (".$array.")
							ORDER BY a.orden, a.nombre_antecedentes_medicos";
				}
				
	            return $this->getDatos($sql);
	        } catch (Exception $e) {
	            return array();
	        }
		}
		
		/**
		 * Obtener el listado de los otros antecedentes
		 * $tipo: 1=listado formulario; 2=Listado agregar; 3=Listado agregado
		 */
		public function getAntecedentesOtros($id_hc, $tipo, $array){
			try {
	            if($id_hc == '') {
	            	$id_hc = 0;
	            }
	            if ($tipo == 1) { //Listado formulario
					$sql = "SELECT a.*, hca.valor_antecedentes_otros FROM antecedentes_otros a
							LEFT JOIN  hc_antecedentes_otros hca  ON a.id_antecedentes_otros = hca.id_antecedentes_otros AND id_hc=".$id_hc."
							WHERE a.ind_activo=1
							AND a.ind_inicial=1
							UNION 
							SELECT a.*, hca.valor_antecedentes_otros FROM antecedentes_otros a
							INNER JOIN  hc_antecedentes_otros hca  ON a.id_antecedentes_otros = hca.id_antecedentes_otros AND id_hc=".$id_hc."
							WHERE a.ind_activo=1
							AND a.ind_inicial=0
							ORDER BY orden, nombre_antecedentes_otros";
				} else if ($tipo == 2) { //Listado para agregar
					$sql = "SELECT a.*
							FROM antecedentes_otros a
							WHERE a.ind_activo=1
							AND a.ind_inicial=0
							AND a.id_antecedentes_otros NOT IN (SELECT hca.id_antecedentes_otros FROM hc_antecedentes_otros hca WHERE hca.id_hc=".$id_hc.")
							ORDER BY a.orden, a.nombre_antecedentes_otros";
				} else if ($tipo == 3) { //Listado agregado
					$sql = "SELECT a.*
							FROM antecedentes_otros a
							WHERE a.ind_activo=1
							AND a.id_antecedentes_otros IN (".$array.")
							ORDER BY a.orden, a.nombre_antecedentes_otros";
				}
	            
	            return $this->getDatos($sql);
	        } catch (Exception $e) {
	            return array();
	        }
		}
		
		/**
		 * Obtener una cnsulta de optometria a partir del id del paciente y el id de la admision
		 */
		public function getOptometriaPaciente($id_paciente, $id_admision) {
			try {
				$sql = "SELECT o.* lstp.nombre_detalle AS slct_lente, lstf.nombre_detalle AS slct_filtro, lstv.nombre_detalle AS tiempo_vigencia, lstm.nombre_detalle AS periodo
				        FROM consultas_optometria o
						LEFT JOIN historia_clinica h ON h.id_hc = o.id_hc
						LEFT JOIN listas_detalle lstp ON o.tipos_lentes_slct = lstp.id_detalle
						LEFT JOIN listas_detalle lstf ON o.tipo_filtro_slct = lstf.id_detalle
						LEFT JOIN listas_detalle lstv ON o.tiempo_vigencia_slct = lstv.id_detalle
						LEFT JOIN listas_detalle lstm ON o.tiempo_periodo = lstm.id_detalle
						WHERE h.id_paciente = '".$id_paciente."' AND h.id_admision = '".$id_admision."'  ";
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Obtener las tonometrias 
		 */
		public function getTonometria($id_hc) {
			try {
				$sql = "SELECT *, DATE_FORMAT(tonometria_fecha_hora, '%d/%m/%Y %H:%i') AS fecha_hora_tonometria
						FROM consultas_oftalmologia_tono
						WHERE id_hc = $id_hc
						ORDER BY orden";
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getConsultaOftalmologiaPediatrica($id_hc) {
	        try {
	            $sql = "SELECT OP.*, CASE OP.ind_ortotropia WHEN 1 THEN 'Sí' WHEN 0 THEN 'No' ELSE '-' END AS ortotropia,
						CR.nombre_detalle AS correccion, OJ.nombre_detalle AS ojo_fijador,
						CASE OP.ind_nistagmo WHEN 1 THEN 'Sí' WHEN 0 THEN 'No' ELSE '-' END AS nistagmo,
						CASE OP.ind_pac WHEN 1 THEN 'Sí' WHEN 0 THEN 'No' ELSE '-' END AS pac, WL.nombre_detalle AS worth_lejos,
						WC.nombre_detalle AS worth_cerca, EM.nombre_detalle AS estereopsis_mosca,
						MD.nombre_detalle AS maddox_der, MI.nombre_detalle AS maddox_izq
						FROM consultas_oftalmologia_pediatrica OP
						LEFT JOIN listas_detalle CR ON OP.id_correccion=CR.id_detalle
						LEFT JOIN listas_detalle OJ ON OP.id_ojo_fijador=OJ.id_detalle
						LEFT JOIN listas_detalle WL ON OP.id_worth_lejos=WL.id_detalle
						LEFT JOIN listas_detalle WC ON OP.id_worth_cerca=WC.id_detalle
						LEFT JOIN listas_detalle EM ON OP.id_estereopsis_mosca=EM.id_detalle
						LEFT JOIN listas_detalle MD ON OP.id_maddox_der=MD.id_detalle
						LEFT JOIN listas_detalle MI ON OP.id_maddox_izq=MI.id_detalle
						WHERE OP.id_hc=".$id_hc;
				
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		public function editarConsultaOftalmologiaPediatrica($id_hc, $metodo_ofp, $ind_ortotropia, $id_correccion, $id_ojo_fijador,
				$lejos_h, $lejos_h_delta, $lejos_v, $lejos_v_delta, $cerca_h, $cerca_h_delta, $cerca_v, $cerca_v_delta, $cerca_c_h, $cerca_c_h_delta, $cerca_c_v, $cerca_c_v_delta,
				$cerca_b_h, $cerca_b_h_delta, $cerca_b_v, $cerca_b_v_delta, $derecha_alto_h, $derecha_alto_h_delta, $derecha_alto_v, $derecha_alto_v_delta,
				$derecha_medio_h, $derecha_medio_h_delta, $derecha_medio_v, $derecha_medio_v_delta, $derecha_bajo_h, $derecha_bajo_h_delta, $derecha_bajo_v, $derecha_bajo_v_delta,
				$centro_alto_h, $centro_alto_h_delta, $centro_alto_v, $centro_alto_v_delta, $centro_medio_h, $centro_medio_h_delta, $centro_medio_v, $centro_medio_v_delta,
				$centro_bajo_h, $centro_bajo_h_delta, $centro_bajo_v, $centro_bajo_v_delta, $izquierda_alto_h, $izquierda_alto_h_delta, $izquierda_alto_v, $izquierda_alto_v_delta,
				$izquierda_medio_h, $izquierda_medio_h_delta, $izquierda_medio_v, $izquierda_medio_v_delta, $izquierda_bajo_h, $izquierda_bajo_h_delta, $izquierda_bajo_v, $izquierda_bajo_v_delta,
				$alto_derecha_od, $alto_centro_od, $alto_izquierda_od, $medio_derecha_od, $medio_izquierda_od, $bajo_derecha_od, $bajo_centro_od, $bajo_izquierda_od, $dvd_od,
				$alto_derecha_oi, $alto_centro_oi, $alto_izquierda_oi, $medio_derecha_oi, $medio_izquierda_oi, $bajo_derecha_oi, $bajo_centro_oi, $bajo_izquierda_oi, $dvd_oi,
				$observaciones_oft_pediat, $inclinacion_der_h, $inclinacion_der_h_delta, $inclinacion_der_v, $inclinacion_der_v_delta,
				$inclinacion_izq_h, $inclinacion_izq_h_delta, $inclinacion_izq_v, $inclinacion_izq_v_delta, $ind_nistagmo, $texto_nistagmo, $ind_pac, $texto_pac,
				$conv_fusional_lejos, $conv_fusional_cerca, $div_fusional_lejos, $div_fusional_cerca, $id_worth_lejos, $id_worth_cerca,
				$id_estereopsis_mosca, $valor_estereopsis_animales, $valor_estereopsis_circulos, $id_maddox_der, $valor_maddox_der, $id_maddox_izq, $valor_maddox_izq, $id_usuario_crea) {
			try {
				if ($metodo_ofp != "") {
					$metodo_ofp = "'".$metodo_ofp."'";
				} else {
					$metodo_ofp = "NULL";
				}
				if ($ind_ortotropia == "") {
					$ind_ortotropia = "NULL";
				}
				if ($id_correccion == "") {
					$id_correccion = "NULL";
				}
				if ($id_ojo_fijador == "") {
					$id_ojo_fijador = "NULL";
				}
				if ($observaciones_oft_pediat != "") {
					$observaciones_oft_pediat = "'".$observaciones_oft_pediat."'";
				} else {
					$observaciones_oft_pediat = "NULL";
				}
				if ($ind_nistagmo == "") {
					$ind_nistagmo = "NULL";
				}
				if ($texto_nistagmo != "") {
					$texto_nistagmo = "'".$texto_nistagmo."'";
				} else {
					$texto_nistagmo = "NULL";
				}
				if ($ind_pac == "") {
					$ind_pac = "NULL";
				}
				if ($texto_pac != "") {
					$texto_pac = "'".$texto_pac."'";
				} else {
					$texto_pac = "NULL";
				}
				if ($id_worth_lejos == "") {
					$id_worth_lejos = "NULL";
				}
				if ($id_worth_cerca == "") {
					$id_worth_cerca = "NULL";
				}
				if ($id_estereopsis_mosca == "") {
					$id_estereopsis_mosca = "NULL";
				}
				if ($id_maddox_der == "") {
					$id_maddox_der = "NULL";
				}
				if ($id_maddox_izq == "") {
					$id_maddox_izq = "NULL";
				}
				
				$sql = "CALL pa_editar_consultas_oftalmologia_pediatrica(".$id_hc.", ".$metodo_ofp.", ".$ind_ortotropia.", ".$id_correccion.", ".$id_ojo_fijador.", '".
						$lejos_h."', '".$lejos_h_delta."', '".$lejos_v."', '".$lejos_v_delta."', '".$cerca_h."', '".$cerca_h_delta."', '".$cerca_v."', '".$cerca_v_delta."', '".
						$cerca_c_h."', '".$cerca_c_h_delta."', '".$cerca_c_v."', '".$cerca_c_v_delta."', '".$cerca_b_h."', '".$cerca_b_h_delta."', '".$cerca_b_v."', '".$cerca_b_v_delta."', '".
						$derecha_alto_h."', '".$derecha_alto_h_delta."', '".$derecha_alto_v."', '".$derecha_alto_v_delta."', '".
						$derecha_medio_h."', '".$derecha_medio_h_delta."', '".$derecha_medio_v."', '".$derecha_medio_v_delta."', '".
						$derecha_bajo_h."', '".$derecha_bajo_h_delta."', '".$derecha_bajo_v."', '".$derecha_bajo_v_delta."', '".
						$centro_alto_h."', '".$centro_alto_h_delta."', '".$centro_alto_v."', '".$centro_alto_v_delta."', '".
						$centro_medio_h."', '".$centro_medio_h_delta."', '".$centro_medio_v."', '".$centro_medio_v_delta."', '".
						$centro_bajo_h."', '".$centro_bajo_h_delta."', '".$centro_bajo_v."', '".$centro_bajo_v_delta."', '".
						$izquierda_alto_h."', '".$izquierda_alto_h_delta."', '".$izquierda_alto_v."', '".$izquierda_alto_v_delta."', '".
						$izquierda_medio_h."', '".$izquierda_medio_h_delta."', '".$izquierda_medio_v."', '".$izquierda_medio_v_delta."', '".
						$izquierda_bajo_h."', '".$izquierda_bajo_h_delta."', '".$izquierda_bajo_v."', '".$izquierda_bajo_v_delta."', '".
						$alto_derecha_od."', '".$alto_centro_od."', '".$alto_izquierda_od."', '".$medio_derecha_od."', '".$medio_izquierda_od."', '".
						$bajo_derecha_od."', '".$bajo_centro_od."', '".$bajo_izquierda_od."', '".$dvd_od."', '".
						$alto_derecha_oi."', '".$alto_centro_oi."', '".$alto_izquierda_oi."', '".$medio_derecha_oi."', '".$medio_izquierda_oi."', '".
						$bajo_derecha_oi."', '".$bajo_centro_oi."', '".$bajo_izquierda_oi."', '".$dvd_oi."', ".$observaciones_oft_pediat.", '".
						$inclinacion_der_h."', '".$inclinacion_der_h_delta."', '".$inclinacion_der_v."', '".$inclinacion_der_v_delta."', '".
						$inclinacion_izq_h."', '".$inclinacion_izq_h_delta."', '".$inclinacion_izq_v."', '".$inclinacion_izq_v_delta."', ".
						$ind_nistagmo.", ".$texto_nistagmo.", ".$ind_pac.", ".$texto_pac.", '".$conv_fusional_lejos."', '".$conv_fusional_cerca."', '".$div_fusional_lejos."', '".$div_fusional_cerca."', ".
						$id_worth_lejos.", ".$id_worth_cerca.", ".$id_estereopsis_mosca.", '".$valor_estereopsis_animales."', '".$valor_estereopsis_circulos."', ".
						$id_maddox_der.", '".$valor_maddox_der."', ".$id_maddox_izq.", '".$valor_maddox_izq."', ".$id_usuario_crea.", @id)";
				
				//echo($sql);
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado = $arrResultado["@id"];
				
				return $resultado;
			} catch (Exception $e) {
				return -2;
			}
		}
		
		public function getListaTonometriasAnteriores($id_hc) {
			try {
				$sql = "SELECT OT.*, DATE_FORMAT(H2.fecha_hora_hc, '%d/%m/%Y') AS fecha_hc_t, DATE_FORMAT(H2.fecha_hora_hc, '%h:%i %p') AS hora_hc_t,
						DATE_FORMAT(OT.tonometria_fecha_hora, '%d/%m/%Y') AS fecha_tono_t, DATE_FORMAT(OT.tonometria_fecha_hora, '%h:%i %p') AS hora_tono_t,
						IFNULL(CO.observaciones_tonometria, CE.observaciones_tonometria) AS observaciones_tonometria
						FROM historia_clinica HC
						INNER JOIN historia_clinica H2 ON HC.id_paciente=H2.id_paciente AND HC.id_hc>H2.id_hc
						INNER JOIN consultas_oftalmologia_tono OT ON H2.id_hc=OT.id_hc
						LEFT JOIN consultas_oftalmologia CO ON H2.id_hc=CO.id_hc
						LEFT JOIN consultas_evoluciones CE ON H2.id_hc=CE.id_hc
						WHERE HC.id_hc=".$id_hc."
						ORDER BY H2.fecha_hora_hc, H2.id_hc, OT.tonometria_fecha_hora";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
    }
?>
