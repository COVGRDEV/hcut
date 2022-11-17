<?php
//HCUT
require_once("DbConexion.php");

class DbAdmision extends DbConexion {
    public function CrearEditarAdmision($in_nombre_1, $in_nombre_2, $in_apellido_1, $in_apellido_2, $in_id_tipo_documento, $in_numero_documento,
			$in_telefono_1, $in_telefono_2, $in_email, $in_fecha_nacimiento, $in_id_pais_nac, $in_cod_dep_nac, $in_cod_mun_nac, $in_nom_dep_nac,
			$in_nom_mun_nac, $in_id_pais, $in_cod_dep, $in_cod_mun, $in_nom_dep, $in_nom_mun, $in_direccion, $in_sexo, $in_ind_desplazado,
			$in_id_etnia, $in_id_zona, $in_id_paciente_cita, $in_id_cita, $in_id_tipo_cita, $ind_preconsulta, $in_cmb_lugar_cita,
			$in_hdd_paciente_existe, $in_id_plan, $in_presion_arterial, $in_pulso, $in_txt_nombre_cirugia, $in_txt_fecha_cirugia, $in_cmb_ojo,
			$in_cmb_num_cirugia, $in_txt_nombre_med_orden, $in_tipo_sangre, $in_numero_hijos, $in_numero_hijas, $in_numero_hermanos,
			$in_numero_hermanas, $in_nombre_acompa, $in_factor_rh, $cmb_convenio, $txtprofesion, $txtmconsulta, $txt_observaciones_admision,
			$cmb_medio_pago, $post, $cmbprofesionalAtiende, $arr_usuarios_citas_det, $arr_examenes, $in_id_usuario_crea, $hdd_id_admision_paciente,
			$hdd_estado_consulta, $in_id_estado_civil, $in_num_carnet, $tipo_coti_paciente, $rango_paciente, $foto_paciente,$num_poliza,$num_mipress,$num_ent_mipress) {
		
		//Se validan las correspondencias de edaddes, números y tipos de documentos
		$sql = "SELECT fu_validar_edad_documento(".$in_id_tipo_documento.", '".$in_numero_documento."',
				STR_TO_DATE('".$in_fecha_nacimiento."', '%d/%m/%Y'), NULL) AS resultado";
			
			//echo($sql);
		$arrResultado = $this->getUnDato($sql);
		$resultado_aux = intval($arrResultado["resultado"], 10);
		//var_dump($resultado_aux);
		if ($resultado_aux > 0) {
			
			$resultado_out=-200;			
			//Se limpia la tabla temporal de estados de atención
			$sql = "DELETE FROM temporal_admisiones_estados_atencion
					WHERE id_usuario=".$in_id_usuario_crea;
			$this->ejecutarSentencia($sql, array());			
			$resultado_out=-201;
			
			//Se agregan los registros de estados de atención
			if (count($arr_usuarios_citas_det) > 0) {
				$resultado_out=-202;
				foreach ($arr_usuarios_citas_det as $usuario_aux) {
					$resultado_out=-203;
					$id_estado_atencion_aux = intval($usuario_aux["id_estado_atencion"], 10);
					$sql = "INSERT INTO temporal_admisiones_estados_atencion
							(id_usuario, id_estado_atencion, id_usuario_prof)
							VALUES (".$in_id_usuario_crea.", ".$id_estado_atencion_aux.", ".$usuario_aux["id_usuario_prof"].")";
					$this->ejecutarSentencia($sql, array());
					
					if ($id_estado_atencion_aux == 5 && $ind_preconsulta == "1") {
						$sql = "INSERT INTO temporal_admisiones_estados_atencion
								(id_usuario, id_estado_atencion, id_usuario_prof)
								VALUES (".$in_id_usuario_crea.", 11, ".$usuario_aux["id_usuario_prof"].")";
						$this->ejecutarSentencia($sql, array());
						
						$sql = "INSERT INTO temporal_admisiones_estados_atencion
								(id_usuario, id_estado_atencion, id_usuario_prof)
								VALUES (".$in_id_usuario_crea.", 12, ".$usuario_aux["id_usuario_prof"].")";
						$this->ejecutarSentencia($sql, array());
					}
					
					$id_estado_atencion_aux++;
					$sql = "INSERT INTO temporal_admisiones_estados_atencion
							(id_usuario, id_estado_atencion, id_usuario_prof)
							VALUES (".$in_id_usuario_crea.", ".$id_estado_atencion_aux.", ".$usuario_aux["id_usuario_prof"].")";
					$this->ejecutarSentencia($sql, array());
				}
				$resultado_out=-204;
			}
			
			//Se limpia la tabla temporal de examenes
			$sql = "DELETE FROM temporal_admisiones_examenes
					WHERE id_usuario=".$in_id_usuario_crea;
			$this->ejecutarSentencia($sql, array());			
			$resultado_out=-205;
			
			//Se agregan los registros de exámenes
			if (count($arr_examenes) > 0) {
				$resultado_out=-206;
				foreach ($arr_examenes as $examen_aux) {
					$resultado_out=-207;
					$sql = "INSERT INTO temporal_admisiones_examenes
							(id_usuario, id_examen, id_ojo)
							VALUES (".$in_id_usuario_crea.", ".$examen_aux["id_examen"].", ".$examen_aux["id_ojo"].")";
					$this->ejecutarSentencia($sql, array());
				}
			}
			
			$resultado_out=-208;			
			$in_ind_desplazado == "" ? $in_ind_desplazado = "NULL" : $in_ind_desplazado = $in_ind_desplazado;
			$in_id_etnia == "" ? $in_id_etnia = "NULL" : $in_id_etnia = $in_id_etnia;
			$in_cod_dep_nac == "" ? $in_cod_dep_nac = "NULL" : $in_cod_dep_nac = "'".$in_cod_dep_nac."'";
			$in_id_pais_nac == "" ? $in_id_pais_nac = "NULL" : $in_id_pais_nac = $in_id_pais_nac;
			$in_cod_mun_nac == "" ? $in_cod_mun_nac = "NULL" : $in_cod_mun_nac = "'".$in_cod_mun_nac."'";
			$in_nom_dep_nac == "" ? $in_nom_dep_nac = "NULL" : $in_nom_dep_nac = "'".$in_nom_dep_nac."'";
			$in_nom_mun_nac == "" ? $in_nom_mun_nac = "NULL" : $in_nom_mun_nac = "'".$in_nom_mun_nac."'";
			$in_id_pais == "" ? $in_id_pais = "NULL" : $in_id_pais = $in_id_pais;
			$in_cod_dep == "" ? $in_cod_dep = "NULL" : $in_cod_dep = "'".$in_cod_dep."'";
			$in_cod_mun == "" ? $in_cod_mun = "NULL" : $in_cod_mun = "'".$in_cod_mun."'";
			$in_nom_dep == "" ? $in_nom_dep = "NULL" : $in_nom_dep = "'".$in_nom_dep."'";
			$in_nom_mun == "" ? $in_nom_mun = "NULL" : $in_nom_mun = "'".$in_nom_mun."'";
			$in_id_zona == "" ? $in_id_zona = "NULL" : $in_id_zona = $in_id_zona;
			$txtmconsulta == "" ? $txtmconsulta = "NULL" : $txtmconsulta = "'".$txtmconsulta."'";
			$txt_observaciones_admision == "" ? $txt_observaciones_admision = "NULL" : $txt_observaciones_admision = "'".$txt_observaciones_admision."'";
			$cmb_medio_pago == "" ? $cmb_medio_pago = "NULL" : $cmb_medio_pago = $cmb_medio_pago;
			$cmbprofesionalAtiende == "" ? $cmbprofesionalAtiende = 0 : $cmbprofesionalAtiende = $cmbprofesionalAtiende;
			$in_presion_arterial == "" ? $in_presion_arterial = "NULL" : $in_presion_arterial = "'".$in_presion_arterial."'";
			$in_pulso == "" ? $in_pulso = "NULL" : $in_pulso = $in_pulso;
			$in_txt_nombre_cirugia == "" ? $in_txt_nombre_cirugia = "NULL" : $in_txt_nombre_cirugia = "'".$in_txt_nombre_cirugia."'";
			$in_txt_fecha_cirugia == "" ? $in_txt_fecha_cirugia = "NULL" : $in_txt_fecha_cirugia = "STR_TO_DATE('".$in_txt_fecha_cirugia."', '%d/%m/%Y')";
			$in_txt_nombre_med_orden == "" ? $in_txt_nombre_med_orden = "NULL" : $in_txt_nombre_med_orden = "'".$in_txt_nombre_med_orden."'";
			$in_id_estado_civil == "" ? $in_id_estado_civil = "NULL" : $in_id_estado_civil = $in_id_estado_civil;
			$in_num_carnet == "" ? $in_num_carnet = "NULL" : $in_num_carnet = "'".$in_num_carnet."'";
			$txtprofesion == "" ? $txtprofesion = "NULL" : $txtprofesion = "'".$txtprofesion."'";
			$in_cmb_ojo == "" ? $in_cmb_ojo = "NULL" : $in_cmb_ojo = $in_cmb_ojo;
			$in_cmb_num_cirugia == "" ? $in_cmb_num_cirugia = "NULL" : $in_cmb_num_cirugia = $in_cmb_num_cirugia;
			$in_tipo_sangre == "" ? $in_tipo_sangre = "NULL" : $in_tipo_sangre = $in_tipo_sangre;
			$in_factor_rh == "" ? $in_factor_rh = "NULL" : $in_factor_rh = $in_factor_rh;
			$in_numero_hijos == "" ? $in_numero_hijos = "NULL" : $in_numero_hijos = $in_numero_hijos;
			$in_numero_hijas == "" ? $in_numero_hijas = "NULL" : $in_numero_hijas = $in_numero_hijas;
			$in_numero_hermanos == "" ? $in_numero_hermanos = "NULL" : $in_numero_hermanos = $in_numero_hermanos;
			$in_numero_hermanas == "" ? $in_numero_hermanas = "NULL" : $in_numero_hermanas = $in_numero_hermanas;
			$foto_paciente==  "" ? $foto_paciente = "NULL" : $foto_paciente = "'".$foto_paciente."'";
			$num_poliza==  "" ? $num_poliza = "NULL" : $num_poliza = "'".$num_poliza."'";
			$num_mipress==  "" ? $num_mipress = "NULL" : $num_mipress = "'".$num_mipress."'";
			$num_ent_mipress==  "" ? $num_ent_mipress = "NULL" : $num_ent_mipress = "'".$num_ent_mipress."'";
			
			$resultado_out=-209;			
			strlen($in_email) >= 1 ? $in_email = "'".$in_email."'" : $in_email = "NULL";
			strlen($in_nombre_2) >= 1 ? $in_nombre_2 = "'".$in_nombre_2."'" : $in_nombre_2 = "NULL";
			strlen($in_apellido_2) >= 1 ? $in_apellido_2 = "'".$in_apellido_2."'" : $in_apellido_2 = "NULL";
			strlen($in_telefono_2) >= 1 ? $in_telefono_2 = "'".$in_telefono_2."'" : $in_telefono_2 = "NULL";
			strlen($in_nombre_acompa) >= 1 ? $in_nombre_acompa = "'".$in_nombre_acompa."'" : $in_nombre_acompa = "NULL";
			
			$resultado_out=-210;			
			try {
				$resultado_out=-211;				
				if ($hdd_id_admision_paciente > 0) {
					$sql = "CALL pa_editar_admision('".$in_nombre_1."', ".$in_nombre_2.", '".$in_apellido_1."', ".$in_apellido_2.", ".$in_id_tipo_documento.", '".
					   $in_numero_documento."', '".$in_telefono_1."', ".$in_telefono_2.", ".$in_email.", STR_TO_DATE('".$in_fecha_nacimiento."', '%d/%m/%Y'), ".
					   $in_id_pais_nac.", ".$in_cod_dep_nac.", ".$in_cod_mun_nac.", ".$in_nom_dep_nac.", ".$in_nom_mun_nac.", ".$in_id_pais.", ".$in_cod_dep.", ".
					   $in_cod_mun.", ".$in_nom_dep.", ".$in_nom_mun.", '".$in_direccion."', ".$in_sexo.", ".$in_ind_desplazado.", ".$in_id_etnia.", ".$in_id_zona.", ".
					   $in_id_paciente_cita.", ".$in_id_cita.", ".$in_id_tipo_cita.", ".$in_cmb_lugar_cita.", ".$in_hdd_paciente_existe.", ".$in_id_plan.", ".
					   $in_presion_arterial.", ".$in_pulso.", ".$in_txt_nombre_cirugia.", ".$in_txt_fecha_cirugia.", ".$in_cmb_ojo.", ".$in_cmb_num_cirugia.", ".
					   $in_txt_nombre_med_orden.", ".$in_tipo_sangre.", ".$in_factor_rh.", ".$in_numero_hijos.", ".$in_numero_hijas.", ".$in_numero_hermanos.", ".
					   $in_numero_hermanas.", ".$in_nombre_acompa.", ".$cmb_convenio.", '".$txtprofesion."', ".$txtmconsulta.", ".$txt_observaciones_admision.", ".
					   $cmb_medio_pago.", ".$post.", ".$cmbprofesionalAtiende.", ".$in_id_estado_civil.", ".$in_id_usuario_crea.", ".$hdd_id_admision_paciente.", ".
					   $hdd_estado_consulta.", ".$in_num_carnet.", ".$tipo_coti_paciente.", ".$rango_paciente.", '".$foto_paciente."', ".$num_poliza.", ".$num_mipress.",
					   ".$num_ent_mipress.", @id)";
				} else {
					$sql = "CALL pa_crear_admision('".$in_nombre_1."', ".$in_nombre_2.", '".$in_apellido_1."', ".$in_apellido_2.", ".$in_id_tipo_documento.", '".
					   $in_numero_documento."', '".$in_telefono_1."', ".$in_telefono_2.", ".$in_email.", STR_TO_DATE('".$in_fecha_nacimiento."', '%d/%m/%Y'), ".
					   $in_id_pais_nac.", ".$in_cod_dep_nac.", ".$in_cod_mun_nac.", ".$in_nom_dep_nac.", ".$in_nom_mun_nac.", ".$in_id_pais.", ".$in_cod_dep.", ".
					   $in_cod_mun.", ".$in_nom_dep.", ".$in_nom_mun.", '".$in_direccion."', ".$in_sexo.", ".$in_ind_desplazado.", ".$in_id_etnia.", ".$in_id_zona.", ".
					   $in_id_paciente_cita.", ".$in_id_cita.", ".$in_id_tipo_cita.", ".$in_cmb_lugar_cita.", ".$in_hdd_paciente_existe.", ".$in_id_plan.", ".
					   $in_presion_arterial.", ".$in_pulso.", ".$in_txt_nombre_cirugia.", ".$in_txt_fecha_cirugia.", ".$in_cmb_ojo.", ".$in_cmb_num_cirugia.", ".
					   $in_txt_nombre_med_orden.", ".$in_tipo_sangre.", ".$in_factor_rh.", ".$in_numero_hijos.", ".$in_numero_hijas.", ".$in_numero_hermanos.", ".
					   $in_numero_hermanas.", ".$in_nombre_acompa.", ".$cmb_convenio.", ".$txtprofesion.", ".$txtmconsulta.", ".$txt_observaciones_admision.", ".
					   $cmb_medio_pago.", ".$post.", ".$cmbprofesionalAtiende.", ".$in_id_estado_civil.", ".$in_num_carnet.", ".$tipo_coti_paciente.", ".
					   $rango_paciente.", ".$foto_paciente.", ".$in_id_usuario_crea.", ".$num_poliza.", ".$num_mipress.", ".$num_ent_mipress.", @id)";
				}
				//echo($sql);
				$resultado_out=-212; 
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado_out = $arrResultado["@id"];
			
				
				return $resultado_out;
			} catch (Exception $e) {
				return -2;
			}
		} else {			
			//20170621-ZJ: Log para seguimiento a error reportado por Paula(COTA) ("error -10"): 
			$sql = "INSERT INTO log_errores_reportados (fecha_log, id_usuario_crea, script_ln, ssql, variables) 
					VALUES (NOW(), $in_id_usuario_crea, 'DbAdmision-linea136', '".addslashes($sql)."', 'arrResultado[resultado]=".$arrResultado["resultado"]."; resultado_aux=$resultado_aux')";
			$this->ejecutarSentencia($sql, array()); 
			
			return $resultado_aux - 10;
		}
	}
	
	
	public function CrearEditarDatosMercadeo($cmb_lista_merc, $cmb_subcategoria_merc, $txt_remitido_merc, $txt_referido_merc ,$rta_aux){
		try{
			
			strcasecmp($cmb_lista_merc, "0") === 0 ? $cmb_lista_merc = "NULL" : $cmb_lista_merc = $cmb_lista_merc;
			strcasecmp($cmb_subcategoria_merc, "0") === 0 ?  $cmb_subcategoria_merc = "NULL" : $cmb_subcategoria_merc = $cmb_subcategoria_merc; 
			strcasecmp($txt_remitido_merc, "0") === 0 ? $txt_remitido_merc = "NULL" : $txt_remitido_merc = $txt_remitido_merc;
			strcasecmp($txt_referido_merc, "0") === 0 ? $txt_referido_merc = "NULL" : $txt_referido_merc = $txt_referido_merc;
			
			$sql = "CALL pa_crear_mercadeo_datos(".$cmb_lista_merc.",".$cmb_subcategoria_merc.", '".$txt_remitido_merc."', '".$txt_referido_merc."', ".$rta_aux.", @id)";
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			$resultado_out = $arrResultado["@id"];	
			return $resultado_out;
			//echo($sql);
				
		} catch (Exception $e) {
			return -2;
		}			
	}
	
	//Funcion que guarda los precios en la tabla temporal_pagos_detalle
	public function guardarTemporalPagosDetalle($idUsuario, $idListaPrecios, $idConvenio, $idPlan, $numAutorizacion, $tipoBilateral, $cantidad, $tipoPrecio, $valor, $indTipoPago,
			$valorCuota = "0", $cod_procedimiento = "", $cod_medicamento = "", $cod_insumo = "") {
		try {
			if ($numAutorizacion != "") {
				$numAutorizacion = "'".$numAutorizacion."'";
			} else {
				$numAutorizacion = "NULL";
			}
			if ($cod_procedimiento != "") {
				$cod_procedimiento = "'".$cod_procedimiento."'";
			} else {
				$cod_procedimiento = "NULL";
			}
			if ($cod_medicamento == "") {
				$cod_medicamento = "NULL";
			}
			if ($cod_insumo == "") {
				$cod_insumo = "NULL";
			}
			
			$sql = "CALL pa_guardar_temporal_pagos_detalle(".$idUsuario.", ".$idListaPrecios.", ".$idConvenio.", ".$idPlan.", ".
					$numAutorizacion.", ".$tipoBilateral.", ".$cantidad.", '".$tipoPrecio."', ".$valor.", ".$valorCuota.", ".$indTipoPago.", ".
					$cod_procedimiento.", ".$cod_medicamento.", ".$cod_insumo.", @id)";
			//echo($sql);
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			$resultado_out = $arrResultado["@id"];
			
	
			return $resultado_out;
		} catch (Exception $e) {
			return -2;
		}
	}
	
	/**
	 * Elimina registros de la tabla temporal_pagos_detalle
	 */
	public function deleteTemporalPagosDetalle($idUsuario) {
		try {
			$sql = "DELETE FROM temporal_pagos_detalle WHERE id_usuario_crea = $idUsuario";
			//return $this->getDatos($sql);
			$this->ejecutarSentencia($sql, array());
			
		} catch (Exception $e) {
			return array();
		}
	}
	
	public function getAdmisionEstadoAtencionUsuario($id_admision, $id_estado_atencion, $id_usuario_prof) {
		try {
			$sql = "SELECT * FROM admisiones_estados_atencion ".
				   "WHERE id_admision=".$id_admision." ".
				   "AND id_estado_atencion=".$id_estado_atencion." ".
				   "AND id_usuario_prof=".$id_usuario_prof;
			//echo($sql);
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	public function get_admision($id_admision) {
		try {
			$sql = "SELECT A.*, C.observacion_cita, DATE_FORMAT(A.fecha_cirugia, '%d/%m/%Y') AS fecha_cirugia_t,
					DATE_FORMAT(A.fecha_admision, '%d/%m/%Y') AS fecha_admision_t, O.nombre_detalle AS ojo, CO.nombre_convenio,
					PL.nombre_plan, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS profesional_atiende_admision,
					MP.codigo_detalle AS cod_medio_pago, MP.nombre_detalle AS medio_pago, DATE_FORMAT(A.fecha_admision, '%h:%i:%s %p') AS hora_admision_t,
					CONCAT(UA.nombre_usuario, ' ', UA.apellido_usuario) AS usuario_crea_admision,
					CONCAT(UC.nombre_usuario, ' ', UC.apellido_usuario) AS usuario_crea_cita,
					TC.nombre_tipo_cita, DATE_FORMAT(C.fecha_crea, '%d/%m/%Y') AS fecha_crea_cita_t,
					DATE_FORMAT(C.fecha_crea, '%h:%i:%s %p') AS hora_crea_cita_t, CO.ind_num_aut, CO.ind_num_aut_obl,
					CO.ind_num_carnet, CO.ind_num_carnet_obl, EA.nombre_estado, SD.tel_sede_det, SD.dir_sede_det, SD.dir_logo_sede_det
					FROM admisiones A
					LEFT JOIN citas C ON A.id_cita=C.id_cita
					LEFT JOIN listas_detalle O ON A.id_ojo=O.id_detalle
					LEFT JOIN convenios CO ON A.id_convenio=CO.id_convenio
					LEFT JOIN planes PL ON A.id_plan=PL.id_plan
					LEFT JOIN listas_detalle MP ON A.id_medio_pago=MP.id_detalle
					INNER JOIN usuarios U ON A.id_usuario_prof=U.id_usuario
					INNER JOIN usuarios UA ON A.id_usuario_crea=UA.id_usuario
					LEFT JOIN usuarios UC ON C.id_usuario_crea=UC.id_usuario
					INNER JOIN tipos_citas TC ON A.id_tipo_cita=TC.id_tipo_cita
					INNER JOIN estados_atencion EA ON A.id_estado_atencion=EA.id_estado_atencion
					LEFT JOIN sedes_det SD ON SD.id_detalle = A.id_lugar_cita
					WHERE A.id_admision=".$id_admision;
			
			//echo($sql);
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	public function get_lista_admisiones_preqx($id_paciente, $dias_limite) {
		try {
			$sql = "SELECT A.*, HC.id_hc, TR.id_tipo_reg, M.pagina_menu,
					DATE_FORMAT(A.fecha_admision, '%d/%m/%Y') AS fecha_admision_t
					FROM admisiones A
					INNER JOIN tipos_citas TC ON A.id_tipo_cita=TC.id_tipo_cita
					LEFT JOIN tipos_registros_hc TR ON TC.id_tipo_reg_cx=TR.id_tipo_reg
					LEFT JOIN menus M ON TR.id_menu=M.id_menu
					LEFT JOIN (
						SELECT HC.id_hc, HC.id_admision
						FROM historia_clinica HC
						INNER JOIN tipos_registros_hc TR ON HC.id_tipo_reg=TR.id_tipo_reg
						WHERE TR.id_clase_reg=2
					) HC ON A.id_admision=HC.id_admision
					WHERE A.id_paciente=".$id_paciente."
					AND TC.ind_preqx=1 ";
			if ($dias_limite > 0) {
				$sql .= "AND A.fecha_admision>=(CURDATE() - INTERVAL ".$dias_limite." DAY) ";
			}
			$sql .= "ORDER BY A.fecha_admision DESC";
			
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	public function get_lista_proc_adicionales_adm($id_admision) {
		try {
			$sql = "SELECT P.*
					FROM pagos_detalle PD
					INNER JOIN maestro_procedimientos P ON PD.cod_procedimiento=P.cod_procedimiento
					WHERE PD.id_admision=".$id_admision."
					AND NOT EXISTS (
						SELECT CD.cod_procedimiento
						FROM admisiones A
						INNER JOIN tipos_citas_det CD ON A.id_tipo_cita=CD.id_tipo_cita
						WHERE A.id_admision=PD.id_admision
						AND CD.cod_procedimiento=PD.cod_procedimiento
					)
					ORDER BY P.cod_procedimiento";
			
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	public function get_ultima_admision($id_paciente) {
		try {
            $sql = "SELECT A.*, C.observacion_cita, DATE_FORMAT(A.fecha_cirugia, '%d/%m/%Y') AS fecha_cirugia_t,
					DATE_FORMAT(A.fecha_admision, '%d/%m/%Y') AS fecha_admision_t,
					O.nombre_detalle AS ojo
					FROM admisiones A
					LEFT JOIN citas C ON A.id_cita=C.id_cita
					LEFT JOIN listas_detalle O ON A.id_ojo=O.id_detalle
					WHERE A.id_paciente=".$id_paciente."
					ORDER BY fecha_admision DESC
					LIMIT 1";
			
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	/*
	Método que busca la última admisión con un límite máximo de tiempo contando desde el presente
	$Unidad: D - Días; M - Meses; A - Años
	*/
	public function get_ultima_admision_tiempo($id_paciente, $unidad, $num_unidades) {
		try {
			$sqlAux = "";
			switch ($unidad) {
				case "D":
					$sqlAux = "AND DATE(A.fecha_admision)>=DATE_SUB(CURDATE(), INTERVAL ".$num_unidades." DAY)";
					break;
				case "M":
					$sqlAux = "AND DATE(A.fecha_admision)>=DATE_SUB(CURDATE(), INTERVAL ".$num_unidades." MONTH)";
					break;
				case "A":
					$sqlAux = "AND DATE(A.fecha_admision)>=DATE_SUB(CURDATE(), INTERVAL ".$num_unidades." YEAR)";
					break;
			}
            $sql = "SELECT A.*, FM.id_formulario_mercadeo AS formulario_mercadeo, FM.categoria, FM.subcategoria, FM.remitido, FM.otro, FM.referido, C.observacion_cita, DATE_FORMAT(A.fecha_cirugia, '%d/%m/%Y') AS fecha_cirugia_t,
					DATE_FORMAT(A.fecha_admision, '%d/%m/%Y') AS fecha_admision_t,
					O.nombre_detalle AS ojo
					FROM admisiones A
					LEFT JOIN citas C ON A.id_cita=C.id_cita
					LEFT JOIN listas_detalle O ON A.id_ojo=O.id_detalle
					RIGHT JOIN formulario_mercadeo FM ON FM.id_admision= A.id_admision
					WHERE A.id_paciente=".$id_paciente."
					".$sqlAux."
					ORDER BY fecha_admision DESC
					LIMIT 1";
		
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	public function get_ultima_admision_convenio($id_paciente, $id_convenio) {
		try {
            $sql = "SELECT A.*, C.observacion_cita, DATE_FORMAT(A.fecha_cirugia, '%d/%m/%Y') AS fecha_cirugia_t,
					DATE_FORMAT(A.fecha_admision, '%d/%m/%Y') AS fecha_admision_t,
					O.nombre_detalle AS ojo
					FROM admisiones A
					LEFT JOIN citas C ON A.id_cita=C.id_cita
					LEFT JOIN listas_detalle O ON A.id_ojo=O.id_detalle
					WHERE A.id_paciente=".$id_paciente."
					AND A.id_convenio=".$id_convenio."
					ORDER BY fecha_admision DESC
					LIMIT 1";
			
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	/**
	 * Obtener el usuario optómetra que atendió
	 */
	public function get_profesional_optometra($id_admision) {
		try {
			$sql = "SELECT U.*, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS profesional_optometra
					FROM admisiones_estados_atencion AE
					INNER JOIN usuarios U ON U.id_usuario=AE.id_usuario_prof
					WHERE AE.id_admision=".$id_admision."
					AND AE.id_estado_atencion=4";
			
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	public function get_IdUsuarios_admision($id_admision){
		try {
			$sql = "SELECT id_usuario_prof 
					FROM admisiones_estados_atencion WHERE id_admision = $id_admision
					GROUP BY id_usuario_prof";
			
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	public function get_examenes_admision($id_admision){
		try {
			$sql = "SELECT *, (@rownum:=@rownum+1) AS numero_examen
					FROM (SELECT @rownum:=0) r, admisiones_examenes  
					WHERE id_admision = $id_admision
					ORDER BY id_examen";
			
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	public function editar_admision_estado($id_admision, $id_estado_atencion, $ind_commit, $id_usuario) {
		try {
			$sql = "CALL pa_editar_admision_estado(".$id_admision.", ".$id_estado_atencion.", ".$ind_commit.", ".$id_usuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			$resultado_out = $arrResultado["@id"];
			
			return $resultado_out;
		} catch (Exception $e) {
			return -2;
		}
	}
	
    public function crear_admision_remision($id_admision_base, $id_tipo_cita, $observaciones_remision, $arr_examenes, $in_id_usuario, $id_lugar_rem = "") {
        try {
			//Se limpia la tabla temporal de examenes
			$sql = "DELETE FROM temporal_admisiones_examenes
					WHERE id_usuario=".$in_id_usuario;
			$this->ejecutarSentencia($sql, array());
			
			//Se agregan los registros de exámenes
			if (count($arr_examenes) > 0) {
				foreach ($arr_examenes as $examen_aux) {
					$sql = "INSERT INTO temporal_admisiones_examenes
							(id_usuario, id_examen, id_ojo)
							VALUES (".$in_id_usuario.", ".$examen_aux["id_examen"].", ".$examen_aux["id_ojo"].")";
					$this->ejecutarSentencia($sql, array());
				}
			}
			
			if (trim($observaciones_remision) != "") {
				$observaciones_remision = "'".$observaciones_remision."'";
			} else {
				$observaciones_remision = "NULL";
			}
			if ($id_lugar_rem == "") {
				$id_lugar_rem = "NULL";
			}
			
			$sql = "CALL pa_crear_admision_remision(".$id_admision_base.", ".$id_tipo_cita.", ".$observaciones_remision.", ".$id_lugar_rem.", ".$in_id_usuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			$resultado_out = $arrResultado["@id"];
			
            return $resultado_out;
        } catch (Exception $e) {
            return -2;
        }
    }
	
    public function get_admision_id_cita($id_cita) {
        try {
            $sql = "SELECT * FROM admisiones
					WHERE id_cita=".$id_cita;
			
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
	//Retorna las últimas 20 admisiones de un paciente, indicando cuales son candidatas para cambio de estado
    public function get_lista_admisiones_cambio_estado($parametro) {
        try {
			$parametro = str_replace(" ", "%", $parametro);
            $sql = "SELECT TD.codigo_detalle AS cod_tipo_documento, TD.nombre_detalle AS tipo_documento, P.numero_documento,
					P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, EA.nombre_estado, TC.nombre_tipo_cita,
					DATE_FORMAT(A.fecha_admision, '%d/%m/%Y') AS fecha_admision_t, A.*, IFNULL(CE.ind_cambio_estado, 0) AS ind_cambio_estado
					FROM admisiones A
					LEFT JOIN (
						SELECT A.id_admision, 1 AS ind_cambio_estado
						FROM admisiones A
						LEFT JOIN citas C ON A.id_cita=C.id_cita
						WHERE A.id_estado_atencion NOT IN (1, 9, 10, 17)
						AND IFNULL(C.id_estado_cita, 16)=16
						AND A.fecha_admision>=DATE_ADD(CURDATE(), INTERVAL -7 DAY)
						UNION
						SELECT id_admision, 1
						FROM admisiones
						WHERE id_estado_atencion=9
						AND fecha_admision>=DATE_ADD(CURDATE(), INTERVAL -7 DAY)
					) CE ON A.id_admision=CE.id_admision
					INNER JOIN pacientes P ON A.id_paciente=P.id_paciente
					LEFT JOIN listas_detalle TD ON P.id_tipo_documento=TD.id_detalle
					INNER JOIN estados_atencion EA ON A.id_estado_atencion=EA.id_estado_atencion
					INNER JOIN tipos_citas TC ON A.id_tipo_cita=TC.id_tipo_cita
					WHERE (P.numero_documento LIKE '".$parametro."%'
					OR CONCAT(IFNULL(P.nombre_1,''),' ',IFNULL(P.nombre_2,''),' ',IFNULL(P.apellido_1,''),' ',IFNULL(P.apellido_2,'')) LIKE '%".$parametro."%')
					ORDER BY P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, A.fecha_admision DESC
					LIMIT 50";
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
    public function get_lista_estados_mover_admision($id_admision, $id_estado_atencion_act) {
        try {
			$sql = "SELECT EA.*
					FROM admisiones_estados_tiempos ET
					INNER JOIN estados_atencion EA ON ET.id_estado_atencion=EA.id_estado_atencion
					WHERE ET.id_admision=".$id_admision."
					AND EA.ind_sel_cambio=1
					/*AND EA.id_estado_atencion<>".$id_estado_atencion_act."*/
					UNION
					SELECT *
					FROM estados_atencion
					WHERE ind_sel_cambio=2
					/*AND id_estado_atencion<>".$id_estado_atencion_act."*/
					ORDER BY orden";
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
	public function guardar_cambio_estado_admision($id_admision, $id_estado_atencion, $id_usuario_prof, $id_lugar_cita, $id_usuario) {
		try {
			if ($id_usuario_prof == "") {
				$id_usuario_prof = "NULL";
			}
			if ($id_lugar_cita == "") {
				$id_lugar_cita = "NULL";
			}
			$sql = "CALL pa_guardar_cambio_estado_admision(".$id_admision.", ".$id_estado_atencion.", ".$id_usuario_prof.", ".$id_lugar_cita.", ".$id_usuario.", @id)";
			//echo($sql);
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			$resultado = $arrResultado["@id"];
			
			return $resultado;
		} catch (Exception $e) {
			return -2;
		}
	}
	
	public function reporteTiemposAtencion($fecha_ini, $fecha_fin, $id_convenio, $id_plan) {
		try {
			
			$sql_filtros_busq = "AND A.fecha_admision BETWEEN '".$fecha_ini."' AND '".$fecha_fin." 23:59:59' ";
			
			if ($id_convenio != "") {
				$sql_filtros_busq .= "AND A.id_convenio=".$id_convenio." ";
			}
			if ($id_plan != "") {
				$sql_filtros_busq .= "AND A.id_plan=".$id_plan." ";
			}
					
			$sql = "SELECT P.nombre_1,P.nombre_2,P.apellido_1,P.apellido_2,P.numero_documento, TEA.id_admision, TEA.id_estado_atencion, EA.nombre_estado, TEA.fecha_estado, 				                    CO.id_convenio, CO.nombre_convenio, PL.id_plan, PL.nombre_plan, EA.orden AS orden_estado 
					FROM  admisiones A, admisiones_estados_tiempos TEA, estados_atencion EA, convenios CO, planes PL, pacientes P
					WHERE A.id_paciente=P.id_paciente 
					AND TEA.id_admision=A.id_admision 
					AND EA.id_estado_atencion=TEA.id_estado_atencion 
					AND CO.id_convenio=A.id_convenio 
					AND PL.id_plan=A.id_plan 
					AND CO.id_convenio=PL.id_convenio 
					AND EXISTS (SELECT 1 FROM admisiones_estados_tiempos TEA1 WHERE TEA1.id_admision=TEA.id_admision AND TEA1.id_estado_atencion IN (16, 9)) 
					".$sql_filtros_busq." 
					ORDER BY TEA.id_admision, TEA.fecha_estado "; 
			
			//echo($sql);		
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}	
	
	public function reporteTiemposAtencionDr($fecha_ini, $fecha_fin, $id_convenio, $id_plan, $id_usuario_prof) {
		try {
			
			$sql_filtros_busq = "AND A.fecha_admision BETWEEN '".$fecha_ini."' AND '".$fecha_fin." 23:59:59' ";
			
			if ($id_convenio != "") {
				$sql_filtros_busq .= "AND A.id_convenio=".$id_convenio." ";
			}
			if ($id_plan != "") {
				$sql_filtros_busq .= "AND A.id_plan=".$id_plan." ";
			}
					
			$sql = "SELECT TEA.id_admision, 
						A.id_usuario_prof, CONCAT(UP.apellido_usuario, ' ', UP.nombre_usuario) AS nombre_completo, UP.nombre_usuario, UP.apellido_usuario,  
						TEA.id_estado_atencion, EA.nombre_estado, TEA.fecha_estado, CO.id_convenio, CO.nombre_convenio, PL.id_plan, PL.nombre_plan, EA.orden orden_estado 
					FROM citas C, admisiones A, admisiones_estados_tiempos_tmpzj TEA, estados_atencion EA, convenios CO, planes PL, usuarios UP  
					WHERE A.id_cita=C.id_cita AND TEA.id_admision=A.id_admision AND EA.id_estado_atencion=TEA.id_estado_atencion AND CO.id_convenio=A.id_convenio AND PL.id_plan=A.id_plan AND CO.id_convenio=PL.id_convenio
						AND UP.id_usuario=A.id_usuario_prof
					AND EXISTS (SELECT 1 FROM admisiones_estados_tiempos_tmpzj TEA1 WHERE TEA1.id_admision=TEA.id_admision AND TEA1.id_estado_atencion IN (16, 9)) 
					".$sql_filtros_busq." 
					ORDER BY TEA.id_admision, TEA.fecha_estado "; 
					
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}		
	
	public function editar_admision_paciente_hc($id_admision, $nombre_acompa, $numero_hijos, $numero_hijas, $numero_hermanos, $numero_hermanas,
			$presion_arterial, $pulso, $observaciones_admision, $motivo_consulta, $cadena_colores_adm, $nombre_1, $nombre_2, $apellido_1, $apellido_2,
			$id_estado_civil, $profesion, $id_pais_nac, $cod_dep_nac, $cod_mun_nac, $nom_dep_nac, $nom_mun_nac, $fecha_nacimiento, $sexo, $direccion,
			$id_pais_res, $cod_dep_res, $cod_mun_res, $nom_dep_res, $nom_mun_res, $telefono_1, $telefono_2, $email, $cadena_colores_pac, $id_usuario,
			$observ_paciente = "") {
		try {
			if ($nombre_acompa != "") {
				$nombre_acompa = "'".$nombre_acompa."'";
			} else {
				$nombre_acompa = "NULL";
			}
			if ($presion_arterial != "") {
				$presion_arterial = "'".$presion_arterial."'";
			} else {
				$presion_arterial = "NULL";
			}
			if ($pulso == "") {
				$pulso = "NULL";
			}
			if ($observaciones_admision != "") {
				$observaciones_admision = "'".$observaciones_admision."'";
			} else {
				$observaciones_admision = "NULL";
			}
			if ($nombre_2 != "") {
				$nombre_2 = "'".$nombre_2."'";
			} else {
				$nombre_2 = "NULL";
			}
			if ($apellido_2 != "") {
				$apellido_2 = "'".$apellido_2."'";
			} else {
				$apellido_2 = "NULL";
			}
			if ($cod_dep_nac != "") {
				$cod_dep_nac = "'".$cod_dep_nac."'";
			} else {
				$cod_dep_nac = "NULL";
			}
			if ($cod_mun_nac != "") {
				$cod_mun_nac = "'".$cod_mun_nac."'";
			} else {
				$cod_mun_nac = "NULL";
			}
			if ($nom_dep_nac != "") {
				$nom_dep_nac = "'".$nom_dep_nac."'";
			} else {
				$nom_dep_nac = "NULL";
			}
			if ($nom_mun_nac != "") {
				$nom_mun_nac = "'".$nom_mun_nac."'";
			} else {
				$nom_mun_nac = "NULL";
			}
			if ($cod_dep_res != "") {
				$cod_dep_res = "'".$cod_dep_res."'";
			} else {
				$cod_dep_res = "NULL";
			}
			if ($cod_mun_res != "") {
				$cod_mun_res = "'".$cod_mun_res."'";
			} else {
				$cod_mun_res = "NULL";
			}
			if ($nom_dep_res != "") {
				$nom_dep_res = "'".$nom_dep_res."'";
			} else {
				$nom_dep_res = "NULL";
			}
			if ($nom_mun_res != "") {
				$nom_mun_res = "'".$nom_mun_res."'";
			} else {
				$nom_mun_res = "NULL";
			}
			if ($telefono_2 != "") {
				$telefono_2 = "'".$telefono_2."'";
			} else {
				$telefono_2 = "NULL";
			}
			if ($observ_paciente != "") {
				$observ_paciente = "'".$observ_paciente."'";
			} else {
				$observ_paciente = "NULL";
			}
			
			$sql = "CALL pa_editar_admision_paciente_hc(".$id_admision.", ".$nombre_acompa.", ".$numero_hijos.", ".$numero_hijas.", ".$numero_hermanos.", ".
					$numero_hermanas.", ".$presion_arterial.", ".$pulso.", ".$observaciones_admision.", '".$motivo_consulta."', '".$cadena_colores_adm."', '".
					$nombre_1."', ".$nombre_2.", '".$apellido_1."', ".$apellido_2.", ".$id_estado_civil.", '".$profesion."', ".$id_pais_nac.", ".$cod_dep_nac.", ".
					$cod_mun_nac.", ".$nom_dep_nac.", ".$nom_mun_nac.", STR_TO_DATE('".$fecha_nacimiento."', '%d/%m/%Y'), '".$sexo."', '".$direccion."', ".
					$id_pais_res.", ".$cod_dep_res.", ".$cod_mun_res.", ".$nom_dep_res.", ".$nom_mun_res.", '".$telefono_1."', ".$telefono_2.", '".
					$email."', ".$observ_paciente.", '".$cadena_colores_pac."', ".$id_usuario.", @id)";
			//echo($sql);
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			$resultado = $arrResultado["@id"];
			
			return $resultado;
		} catch (Exception $e) {
			return -2;
		}
	}
	
    public function get_admision_hc($id_hc) {
        try {
            $sql = "SELECT A.*, C.observacion_cita, DATE_FORMAT(A.fecha_cirugia, '%d/%m/%Y') AS fecha_cirugia_t,
					DATE_FORMAT(A.fecha_admision, '%d/%m/%Y') AS fecha_admision_t, O.nombre_detalle AS ojo, CO.nombre_convenio,
					PL.nombre_plan, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS profesional_atiende_admision,
					MP.codigo_detalle AS cod_medio_pago, MP.nombre_detalle AS medio_pago, DATE_FORMAT(A.fecha_admision, '%h:%i:%s %p') AS hora_admision_t,
					CONCAT(UA.nombre_usuario, ' ', UA.apellido_usuario) AS usuario_crea_admision,
					CONCAT(UC.nombre_usuario, ' ', UC.apellido_usuario) AS usuario_crea_cita,
					TC.nombre_tipo_cita, DATE_FORMAT(C.fecha_crea, '%d/%m/%Y') AS fecha_crea_cita_t,
					DATE_FORMAT(C.fecha_crea, '%h:%i:%s %p') AS hora_crea_cita_t, CO.ind_num_aut, CO.ind_num_aut_obl,
					CO.ind_num_carnet, CO.ind_num_carnet_obl, EA.nombre_estado
					FROM historia_clinica HC
					INNER JOIN admisiones A ON HC.id_admision=A.id_admision
					LEFT JOIN citas C ON A.id_cita=C.id_cita
					LEFT JOIN listas_detalle O ON A.id_ojo=O.id_detalle
					LEFT JOIN convenios CO ON A.id_convenio=CO.id_convenio
					LEFT JOIN planes PL ON A.id_plan=PL.id_plan
					LEFT JOIN listas_detalle MP ON A.id_medio_pago=MP.id_detalle
					INNER JOIN usuarios U ON A.id_usuario_prof=U.id_usuario
					INNER JOIN usuarios UA ON A.id_usuario_crea=UA.id_usuario
					LEFT JOIN usuarios UC ON C.id_usuario_crea=UC.id_usuario
					INNER JOIN tipos_citas TC ON A.id_tipo_cita=TC.id_tipo_cita
					INNER JOIN estados_atencion EA ON A.id_estado_atencion=EA.id_estado_atencion
					WHERE HC.id_hc=".$id_hc;
			
			//echo($sql);
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
	//Retorna los convenios y planes de pagos asociados a una admisión
    public function get_lista_convenios_planes_admision($id_admision) {
		try {
			$sql = "SELECT DISTINCT P.id_convenio, C.nombre_convenio, P.id_plan, PL.nombre_plan
					FROM pagos P
					INNER JOIN convenios C ON P.id_convenio=C.id_convenio
					INNER JOIN planes PL ON P.id_plan=PL.id_plan
					WHERE P.id_admision=".$id_admision."
					AND P.estado_pago<>3
					ORDER BY C.nombre_convenio, PL.nombre_plan";
			
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	public function guardar_cambio_convenio_plan_admision($id_admision, $id_convenio, $id_plan, $id_usuario) {
		try {
			$sql = "CALL pa_guardar_cambio_convenio_plan_admision(".$id_admision.", ".$id_convenio.", ".$id_plan.", ".$id_usuario.", @id)";
			//echo($sql);
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			$resultado = $arrResultado["@id"];
			
			return $resultado;
		} catch (Exception $e) {
			return -2;
		}
	}
	
    //Funcion que registra el lugar actual en el que se encuetra un paciente dentro de una admisión
    public function crearEditarAdmisionLugar($id_admision, $direccion_ip, $id_usuario) {
        try {
			$sql = "CALL pa_crear_editar_admision_lugar(".$id_admision.", '".$direccion_ip."', ".$id_usuario.", @id)";
			//echo($sql);
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			$resultado_out = $arrResultado["@id"];
			
			return $resultado_out;
		} catch (Exception $e) {
			return -2;
		}
	}
	
	//Retorna los últimos lugares registrados para una admisión. Límite menor o igual a cero devuelve todos los registros
    public function get_lista_lugares_admision($id_admision, $limite) {
		try {
			$sql = "SELECT AL.*, L.nombre_lugar, S.nombre_detalle AS nombre_sede
					FROM admisiones_lugares AL
					INNER JOIN lugares L ON AL.id_lugar=L.id_lugar
					INNER JOIN listas_detalle S ON L.id_sede=S.id_detalle
					WHERE AL.id_admision=".$id_admision."
					ORDER BY fecha_lugar DESC";
			if ($limite >= 0) {
				$sql .= " LIMIT ".$limite;
			}
			
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	public function editar_admision_espera($id_admision, $id_tipo_espera, $id_usuario) {
		try {
			if ($id_tipo_espera == "") {
				$id_tipo_espera = "NULL";
			}
			$sql = "CALL pa_editar_admision_espera(".$id_admision.", ".$id_tipo_espera.", ".$id_usuario.", @id)";
			//echo($sql."<br />");
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			$resultado = $arrResultado["@id"];
			
			return $resultado;
		} catch (Exception $e) {
			return "";
		}
	}
	
	public function getMercadeo($fechaInicial, $fechaFinal, $id_plan, $id_convenio,  $id_mercadeo, $id_lugar) {
		try{
			$sql = "SELECT DATE_FORMAT(A.fecha_admision, '%d/%m/%Y') AS fecha_admision_t,LG.nombre_detalle AS sede, P.numero_documento, P.nombre_1, P.nombre_2, P.apellido_1, 			                    P.apellido_2, P.email, P.telefono_1, fu_calcular_edad(P.fecha_nacimiento,CURDATE()) AS edad,P.direccion, P.id_pais,PS.nombre_pais AS pais, DP.nom_dep ,CD.nom_mun                    ,P.nom_dep AS nom_dep_tex, P.nom_mun AS nom_mun_tex , CV.nombre_convenio, PL.nombre_plan, CONCAT(UP.nombre_usuario, ' ', UP.apellido_usuario) AS                    nombre_usuario_prof, TC.nombre_tipo_cita, SX.nombre_detalle AS genero, FM.*, LS.nombre_lista AS nom_categoria,                    SC.nombre_detalle AS nom_subcategoria 
                    FROM admisiones A 
                    INNER JOIN pacientes P ON A.id_paciente= P.id_paciente 
                    INNER JOIN listas_detalle LG ON A.id_lugar_cita=LG.id_detalle 
                    INNER JOIN formulario_mercadeo FM ON A.id_admision = FM.id_admision 
                    LEFT JOIN listas LS ON FM.categoria = LS.id_lista 
                    LEFT JOIN listas_detalle SC ON FM.subcategoria = SC.id_detalle 
                    INNER JOIN usuarios UP ON UP.id_usuario=A.id_usuario_prof 
                    INNER JOIN tipos_citas TC ON TC.id_tipo_cita=A.id_tipo_cita 
                    LEFT JOIN listas_detalle SX ON P.sexo = SX.id_detalle 
                    LEFT JOIN departamentos DP ON P.cod_dep= DP.cod_dep 
                    LEFT JOIN paises PS ON P.id_pais = PS.id_pais 
                    LEFT JOIN municipios CD ON P.cod_mun = CD.cod_mun_dane 
                    LEFT JOIN convenios CV ON CV.id_convenio = A.id_convenio 
                    LEFT JOIN planes PL ON A.id_plan=PL.id_plan  
					WHERE A.fecha_admision BETWEEN '".$fechaInicial." 00:00:00' AND '".$fechaFinal." 23:59:59'
					AND FM.categoria IS NOT NULL ";
							
							if ($id_convenio != ""){
								$sql .= "AND A.id_convenio=".$id_convenio." ";
							}
							if ($id_plan != "") {
								$sql .= "AND A.id_plan = ".$id_plan." ";
							}
							if ($id_mercadeo != ""){
								$sql .="AND A.id_mercadeo =".$id_mercadeo." ";
							}
							if ($id_lugar != ""){
								$sql .= "AND A.id_lugar_cita =".$id_lugar." ";
							}
							
							$sql.=	"ORDER BY P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2";  
						
						//echo($sql);		


				return $this->getDatos($sql);
				} catch (Exception $e) {
					return array();
				}
			}
	
	public function get_oportunidad_atencion($fechaInicial, $fechaFinal, $id_convenio, $id_plan, $id_lugar_cita){
	
	try{
		
			$sql = "SELECT A.id_admision, TC.nombre_tipo_cita, T34.fecha_inicial_opt, T34.fecha_final_opt,  DATE_FORMAT(TIMEDIFF(T34.fecha_final_opt,T34.fecha_inicial_opt), 					'%H horas %i minutos') AS tiempo_opt, T56.fecha_inicial_oftan, T56.fecha_final_oftan, DATE_FORMAT( TIMEDIFF(T56.fecha_final_oftan,T56.fecha_inicial_oftan), '%H horas %i minutos') 					AS tiempo_oftan, LG.id_detalle  AS id_sede, LG.nombre_detalle AS sede, TD.nombre_detalle AS tipo_documento,P.numero_documento, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, 
					CV.nombre_convenio, CV.id_convenio, PL.nombre_plan, PL.id_plan
					FROM admisiones A 
					INNER JOIN pacientes P ON A.id_paciente= P.id_paciente 
					INNER JOIN listas_detalle TD ON P.id_tipo_documento=TD.id_detalle 
					INNER JOIN listas_detalle LG ON A.id_lugar_cita=LG.id_detalle 
					INNER JOIN tipos_citas TC ON A.id_tipo_cita=TC.id_tipo_cita
					LEFT JOIN convenios CV ON CV.id_convenio = A.id_convenio 
					LEFT JOIN planes PL ON A.id_plan=PL.id_plan
					LEFT JOIN (
						SELECT A.id_admision, TI.fecha_estado AS fecha_inicial_opt, MIN(TF.fecha_estado) AS fecha_final_opt
						FROM admisiones A 
						INNER JOIN admisiones_estados_tiempos TI ON A.id_admision=TI.id_admision
						INNER JOIN admisiones_estados_tiempos T2 ON A.id_admision=T2.id_admision
						INNER JOIN admisiones_estados_tiempos TF ON A.id_admision=TF.id_admision AND T2.id_adm_est_tiempo < TF.id_adm_est_tiempo
						WHERE A.fecha_admision BETWEEN STR_TO_DATE('".$fechaInicial." 00:00:00', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('".$fechaFinal." 23:59:59', '%Y-%m-%d %H:%i:%s')
						AND TI.id_estado_atencion=3
						AND T2.id_estado_atencion=4
						GROUP BY A.id_admision, TI.fecha_estado
					) T34 ON A.id_admision=T34.id_admision
					LEFT JOIN (
						SELECT A.id_admision, TI.fecha_estado AS fecha_inicial_oftan, MIN(TF.fecha_estado) AS fecha_final_oftan
						FROM admisiones A 
						INNER JOIN admisiones_estados_tiempos TI ON A.id_admision=TI.id_admision
						INNER JOIN admisiones_estados_tiempos T2 ON A.id_admision=T2.id_admision
						INNER JOIN admisiones_estados_tiempos TF ON A.id_admision=TF.id_admision AND T2.id_adm_est_tiempo < TF.id_adm_est_tiempo
						WHERE A.fecha_admision BETWEEN STR_TO_DATE('".$fechaInicial." 00:00:00', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('".$fechaFinal." 23:59:59', '%Y-%m-%d %H:%i:%s')
						AND TI.id_estado_atencion=5
						AND T2.id_estado_atencion=6
						GROUP BY A.id_admision, TI.fecha_estado
						)T56 ON A.id_admision= T56.id_admision
				WHERE A.fecha_admision BETWEEN STR_TO_DATE('".$fechaInicial." 00:00:00', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('".$fechaFinal." 23:59:59', '%Y-%m-%d %H:%i:%s')";
					if ($id_convenio != "") {
						$sql .= "AND A.id_convenio=".$id_convenio." ";
								}
					if ($id_plan != "") {
						$sql .= "AND A.id_plan=".$id_plan." ";
		                        }
					if ($id_lugar_cita != "") {
						$sql .= "AND A.id_lugar_cita=".$id_lugar_cita." ";
		                        };
		
			//echo($sql);
	     return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
	}
	
}
?>
