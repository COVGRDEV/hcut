<?php

require_once("DbConexion.php");

class DbHistoriaClinica extends DbConexion {

    public function getHistoriaClinica($id_paciente, $id_tipo_reg) {
        try {
            $sql = "SELECT * FROM historia_clinica hc
						WHERE hc.id_paciente=" . $id_paciente . "
						AND hc.id_tipo_reg=" . $id_tipo_reg . "
						AND DATE(hc.fecha_hora_hc)=CURDATE()
						ORDER BY fecha_hora_hc DESC";

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getHistoriaClinicaAdmision($id_admision, $id_tipo_reg) {
        try {
            $sql = "SELECT * FROM historia_clinica hc
						WHERE hc.id_admision=" . $id_admision . "
						AND hc.id_tipo_reg=" . $id_tipo_reg . "
						ORDER BY fecha_hora_hc DESC";

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getRegistrosHistoriaClinica($id_paciente) {
        try {
            $sql = "SELECT HC.*, TR.nombre_tipo_reg, TR.id_clase_reg, IFNULL(TR.id_tipo_reg_base, HC.id_tipo_reg) AS id_tipo_reg_base,
						C.nombre_convenio, PL.nombre_plan, DATE_FORMAT(HC.fecha_hora_hc, '%d/%m/%Y') AS fecha_hc_t,
						DATE_FORMAT(HC.fecha_hora_hc, '%h:%i:%s %p') AS hora_hc_t, HF.archivo_hc,
						DATE_FORMAT(HC.fecha_hora_hc, '%d/%m/%Y') AS fecha_hora_hc_t, M.*, PA.nombre_1, PA.nombre_2, PA.apellido_1, PA.apellido_2,
						PA.tipo_sangre, PA.factor_rh, PA.id_tipo_documento, PA.numero_documento, PA.telefono_1, PA.telefono_2, PA.email,
						PA.fecha_nacimiento, PA.id_pais, PA.cod_dep, PA.cod_mun, PA.nom_dep, PA.nom_mun, PA.direccion, PA.sexo, PA.ind_desplazado,
						PA.id_etnia, PA.id_zona, PA.profesion, PA.id_estado_civil, PA.id_pais_nac, PA.cod_dep_nac, PA.cod_mun_nac, PA.nom_dep_nac,
						PA.nom_mun_nac, PA.observ_paciente, U.ind_anonimo,
						CASE TR.id_clase_reg WHEN 2 THEN UX.id_usuario ELSE U.id_usuario END AS id_usuario_reg,
						CASE TR.id_clase_reg WHEN 2 THEN UX.nombre_usuario ELSE U.nombre_usuario END AS nombre_usuario,
						CASE TR.id_clase_reg WHEN 2 THEN UX.apellido_usuario ELSE U.apellido_usuario END AS apellido_usuario
						FROM historia_clinica HC
						INNER JOIN pacientes PA ON HC.id_paciente=PA.id_paciente
						LEFT JOIN tipos_registros_hc TR ON HC.id_tipo_reg=TR.id_tipo_reg
						LEFT JOIN menus M ON TR.id_menu=M.id_menu
						LEFT JOIN admisiones A ON HC.id_admision=A.id_admision
						LEFT JOIN convenios C ON A.id_convenio=C.id_convenio
						LEFT JOIN planes PL ON A.id_plan=PL.id_plan
						LEFT JOIN consultas_hc_fisica HF ON HC.id_hc=HF.id_hc
						LEFT JOIN usuarios U ON HC.id_usuario_crea=U.id_usuario
						LEFT JOIN cirugias CX ON HC.id_hc=CX.id_hc
						LEFT JOIN usuarios UX ON CX.id_usuario_prof=UX.id_usuario
						WHERE HC.id_paciente=" . $id_paciente . "
						AND HC.ind_borrado=0
						ORDER BY CASE TR.id_clase_reg WHEN 5 THEN 0 ELSE 1 END, HC.fecha_hora_hc";

            //echo $sql;		
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getRegistrosExamenes($id_paciente) {
        try {
            $sql = "SELECT HC.*, TR.nombre_tipo_reg, TR.id_clase_reg, C.nombre_convenio, PL.nombre_plan,
						DATE_FORMAT(HC.fecha_hora_hc, '%d/%m/%Y') AS fecha_hc_t,
						DATE_FORMAT(HC.fecha_hora_hc, '%h:%i:%s %p') AS hora_hc_t, HF.archivo_hc,
						DATE_FORMAT(HC.fecha_hora_hc, '%d/%m/%Y') AS fecha_hora_hc_t, M.*, PA.*
						FROM historia_clinica HC
						INNER JOIN pacientes PA ON HC.id_paciente=PA.id_paciente
						LEFT JOIN tipos_registros_hc TR ON HC.id_tipo_reg=TR.id_tipo_reg
						LEFT JOIN menus M ON TR.id_menu=M.id_menu
						LEFT JOIN admisiones A ON HC.id_admision=A.id_admision
						LEFT JOIN convenios C ON A.id_convenio=C.id_convenio
						LEFT JOIN planes PL ON A.id_plan=PL.id_plan
						LEFT JOIN consultas_hc_fisica HF ON HC.id_hc=HF.id_hc
						WHERE HC.id_paciente=" . $id_paciente . "
						AND HC.ind_borrado=0
						AND HC.id_tipo_reg IN (10, 12)
						ORDER BY HC.fecha_hora_hc";

            //echo $sql;		
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getHistoriaClinicaId($id_hc) {
        try {
            $sql = "SELECT HC.*, TR.nombre_tipo_reg, TR.id_clase_reg, C.nombre_convenio, PL.nombre_plan,
						IFNULL(TR.id_tipo_reg_base, HC.id_tipo_reg) AS id_tipo_reg_base,
						DATE_FORMAT(HC.fecha_hora_hc, '%d/%m/%Y') AS fecha_hc_t,
						DATE_FORMAT(HC.fecha_hora_hc, '%h:%i:%s %p') AS hora_hc_t, HF.archivo_hc
						FROM historia_clinica HC
						LEFT JOIN tipos_registros_hc TR ON HC.id_tipo_reg=TR.id_tipo_reg
						LEFT JOIN admisiones A ON HC.id_admision=A.id_admision
						LEFT JOIN convenios C ON A.id_convenio=C.id_convenio
						LEFT JOIN planes PL ON A.id_plan=PL.id_plan
						LEFT JOIN consultas_hc_fisica HF ON HC.id_hc=HF.id_hc
						WHERE HC.id_hc=" . $id_hc;

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

	public function getIncapacidades($id_admision){
		
		try{
			$sql = "SELECT I.*, LD.nombre_detalle AS nombre_atencion, LDT.nombre_detalle AS nombre_origen_inc FROM incapacidades I
					LEFT JOIN listas_detalle LD ON I.tipo_atencion = LD.id_detalle
					LEFT JOIN listas_detalle LDT ON I.origen_incapacidad = LDT.id_detalle
					WHERE I.id_admision= ".$id_admision;
					
			return $this->getUnDato($sql);	
		}catch(Exception $e){
			
			return array();
		}
		
	}
	public function getIncapacidadesByHC($id_hc){
		
		try{
			$sql = "SELECT I.*, LD.nombre_detalle AS nombre_atencion, LDT.nombre_detalle AS nombre_origen_inc FROM incapacidades I
					LEFT JOIN listas_detalle LD ON I.tipo_atencion = LD.id_detalle
					LEFT JOIN listas_detalle LDT ON I.origen_incapacidad = LDT.id_detalle
					WHERE I.id_hc= ".$id_hc;
					
			return $this->getUnDato($sql);	
		}catch(Exception $e){
			
			return array();
		}
		
	}

	public function getIncapacidadesDet($id_incapacidad){
		
		try{
			$sql = "SELECT I.*, TA.nombre_detalle AS nombre_atencion, OI.nombre_detalle AS nombre_origen_inc,
						   LC.nombre_detalle AS lugar_cita, CN.nombre_convenio, PL.nombre_plan, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS profesional, U.reg_firma, U.num_reg_medico AS reg_profesional
					FROM incapacidades I
						LEFT JOIN listas_detalle TA ON I.tipo_atencion = TA.id_detalle
						LEFT JOIN listas_detalle OI ON I.origen_incapacidad = OI.id_detalle
						LEFT JOIN listas_detalle LC ON I.id_lugar_cita = LC.id_detalle
						LEFT JOIN convenios CN ON I.id_convenio = CN.id_convenio
						LEFT JOIN planes PL ON I.id_plan = PL.id_plan
						LEFT JOIN usuarios U ON I.id_profesional = U.id_usuario
						WHERE I.id_incapacidad= ".$id_incapacidad;
									
			return $this->getUnDato($sql);	
		}catch(Exception $e){
			
			return array();
		}
		
	}
	
    /**
     * Indica si se puede editar una HC dependiendo de las horas predeterminadas
     */
    public function getIndicadorHoras($id_hc, $hora_edicion) {
        try {
            $sql = "SELECT (fecha_crea + INTERVAL $hora_edicion HOUR) - NOW() AS diferencia
						FROM historia_clinica
						WHERE id_hc=$id_hc";
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Indica si un usuario puede o no hacer acciones en la HC 
     */
    public function getIndicadorHcUsuario($id_hc, $id_usuario) {
        try {
            $sql = "SELECT COUNT(*) AS ind_pertenece
						FROM historia_clinica HC
						WHERE HC.id_hc = $id_hc AND HC.id_usuario_crea = $id_usuario";
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Indica cual otra HC puede editar 
     */
    public function getIndicadorHcOtra($id_hc, $id_usuario) {
        try {
            $sql = "SELECT COUNT(*) AS ind_otra_hc
						FROM historia_clinica HC
						INNER JOIN admisiones A ON HC.id_admision=A.id_admision
						INNER JOIN tipos_citas_det CD ON A.id_tipo_cita=CD.id_tipo_cita AND HC.id_tipo_reg=CD.id_tipo_reg
						INNER JOIN tipos_citas_det C2 ON A.id_tipo_cita=C2.id_tipo_cita AND CD.orden<C2.orden
						INNER JOIN historia_clinica H2 ON A.id_admision=H2.id_admision AND C2.id_tipo_reg=H2.id_tipo_reg
						WHERE HC.id_hc=$id_hc
						AND H2.id_usuario_crea=$id_usuario";
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Funcion para indicar que se puedo editar un HC
     */
    public function getIndicadorEdicion($id_hc, $hora_edicion) {
        $id_usuario = $_SESSION["idUsuario"];
        $ind_edicion = 0;
        $ind_hora_editar = $this->getIndicadorHoras($id_hc, $hora_edicion);

        if ($ind_hora_editar['diferencia'] >= 0) { //Si se puede editar dependiendo de las horas para editar
            $ind_usuario = $this->getIndicadorHcUsuario($id_hc, $id_usuario);

            //echo "ind_usuario: "; $ind_usuario."<br />";

            if ($ind_usuario['ind_pertenece'] > 0) { //Si se puede editar dependiendo del usuario que inicio sesion
                $ind_edicion = 1;
            } else {
                $ind_hc_otra = $this->getIndicadorHcOtra($id_hc, $id_usuario);
                if ($ind_hc_otra['ind_otra_hc'] > 0) { //Si el usuario tiene la posibilidad de editar un HC que no le pertenece a este usuario
                    $ind_edicion = 1;
                }
            }
        }
        return $ind_edicion;
    }

    public function getPacientesHistoriaClinica($txt_paciente_hc) {
        $txt_paciente_hc = str_replace(" ", "%", $txt_paciente_hc);
        try {
            $sql = "SELECT P.*, TD.nombre_detalle AS tipo_documento, TD.codigo_detalle AS cod_tipo_documento,
						DATE_FORMAT(P.fecha_nacimiento, '%d/%m/%Y') AS fecha_nac_persona, IFNULL(HC.cantidad_hc_ant, 0) AS cantidad_hc_ant
						FROM pacientes P
						INNER JOIN listas_detalle TD ON P.id_tipo_documento=TD.id_detalle
						LEFT JOIN (
							SELECT id_paciente, COUNT(*) AS cantidad_hc_ant
							FROM historia_clinica
							WHERE id_tipo_reg=17
							AND ind_borrado=0
							GROUP BY id_paciente
						) HC ON P.id_paciente=HC.id_paciente
						WHERE (P.numero_documento LIKE '%" . $txt_paciente_hc . "%'
						OR CONCAT(IFNULL(P.nombre_1, ''), ' ', IFNULL(P.nombre_2, ''), ' ', IFNULL(P.apellido_1, ''), ' ', IFNULL(P.apellido_2, '')) LIKE '%" . $txt_paciente_hc . "%')
						ORDER BY P.nombre_1, P.apellido_1
                                                LIMIT 20";
       		//echo $sql;
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function construir_nombre_arch($id_hc, $nombre_arch_ori, $prefijo_arch, $indice) {
        try {
            if ($indice == "") {
                $indice = "NULL";
            }

            $sql = "CALL pa_construir_nombre_arch(" . $id_hc . ", '" . $nombre_arch_ori . "', '" . $prefijo_arch . "', " . $indice . ", @id)";
            //echo($sql);

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $resultado = $arrResultado["@id"];

            return $resultado;
        } catch (Exception $e) {
            return "";
        }
    }

    public function getHistoriaClinicaAdmisionQx($id_admision, $id_tipo_reg) {
        try {
            $sql = "SELECT HC.*
						FROM admisiones A
						INNER JOIN tipos_citas TC ON A.id_tipo_cita=TC.id_tipo_cita
						INNER JOIN tipos_registros_hc TR ON TC.id_tipo_reg_cx=TR.id_tipo_reg
						INNER JOIN (
							SELECT HC.id_hc, HC.id_admision
							FROM historia_clinica HC
							INNER JOIN tipos_registros_hc TR ON HC.id_tipo_reg=TR.id_tipo_reg
							WHERE TR.id_clase_reg=2
						) HC ON A.id_admision=HC.id_admision
						WHERE A.id_admision=" . $id_admision . "
						AND TC.ind_preqx=1
						AND TR.id_tipo_reg=" . $id_tipo_reg;

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function crear_ingreso_hc($id_usuario, $id_paciente, $id_admision, $id_hc, $id_tipo_ingreso) {
        try {
            if ($id_admision == "" || $id_admision == 0) {
                $id_admision = "NULL";
            }
            if ($id_hc == "" || $id_hc == 0) {
                $id_hc = "NULL";
            }

            $sql = "CALL pa_crear_ingreso_hc(" . $id_usuario . ", " . $id_paciente . ", " . $id_admision . ", " . $id_hc . ", " . $id_tipo_ingreso . ", @id)";
            //echo($sql);

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $resultado = $arrResultado["@id"];

            return $resultado;
        } catch (Exception $e) {
            return "";
        }
    }

    public function getListaIngresosHistoriaClinica($id_usuario, $id_paciente, $fecha_inicial, $fecha_final) {
        try {
            $sql = "SELECT * FROM (
							SELECT I.*, U.nombre_usuario, U.apellido_usuario, TD.nombre_detalle AS tipo_documento,
							DATE_FORMAT(I.fecha_ingreso, '%d/%m/%Y %h:%i:%s %p') AS fecha_ingreso_t,
							TD.codigo_detalle AS cod_tipo_documento, P.numero_documento, P.nombre_1, P.nombre_2,
							P.apellido_1, P.apellido_2, TI.nombre_detalle AS tipo_ingreso, TR.nombre_tipo_reg,
							DATE_FORMAT(HC.fecha_hora_hc, '%d/%m/%Y') AS fecha_hc, DATE_FORMAT(D.fecha_despacho, '%d/%m/%Y') AS fecha_despacho
							FROM ingresos_hc I
							INNER JOIN usuarios U ON I.id_usuario=U.id_usuario
							INNER JOIN pacientes P ON I.id_paciente=P.id_paciente
							LEFT JOIN listas_detalle TD ON P.id_tipo_documento=TD.id_detalle
							INNER JOIN listas_detalle TI ON I.id_tipo_ingreso=TI.id_detalle
							LEFT JOIN historia_clinica HC ON I.id_hc=HC.id_hc
							LEFT JOIN tipos_registros_hc TR ON HC.id_tipo_reg=TR.id_tipo_reg
							LEFT JOIN despacho D ON I.id_admision=D.id_admision ";
            $conector = "WHERE";
            if ($id_usuario != "") {
                $sql .= $conector . " I.id_usuario=" . $id_usuario . " ";
                $conector = "AND";
            }
            if ($id_paciente != "") {
                $sql .= $conector . " I.id_paciente=" . $id_paciente . " ";
                $conector = "AND";
            }
            if ($fecha_inicial != "") {
                $sql .= $conector . " I.fecha_ingreso>=STR_TO_DATE('" . $fecha_inicial . "', '%d/%m/%Y') ";
                $conector = "AND";
            }
            if ($fecha_final != "") {
                $sql .= $conector . " I.fecha_ingreso<=STR_TO_DATE('" . $fecha_final . " 23:59:59', '%d/%m/%Y %H:%i:%s') ";
                $conector = "AND";
            }
            $sql .= "	ORDER BY I.fecha_ingreso
						) T
						LIMIT 300";
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Obyener la HC fisica
     */
    public function getHistoriaFisica($id_hc) {
        try {
            $sql = "SELECT * FROM consultas_hc_fisica
						WHERE id_hc = " . $id_hc;
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function crear_historia_clinica_alt($id_paciente, $id_tipo_reg, $ind_estado, $id_admision, $nombre_alt_tipo_reg, $ruta_arch_adjunto, $id_usuario, $observaciones_hc = "",$ind_habeas_data) {
        try {
            if ($id_admision == "" || $id_admision == 0) {
                $id_admision = "NULL";
            }
            if ($ruta_arch_adjunto != "") {
                $ruta_arch_adjunto = "'" . $ruta_arch_adjunto . "'";
            } else {
                $ruta_arch_adjunto = "NULL";
            }
            if ($observaciones_hc != "") {
                $observaciones_hc = "'" . $observaciones_hc . "'";
            } else {
                $observaciones_hc = "NULL";
            }
			if ($ind_habeas_data != "") {
                $ind_habeas_data = "'" . $ind_habeas_data . "'";
            } else {
                $ind_habeas_data = "NULL";
            }

            $sql = "CALL pa_crear_historia_clinica_alt(" . $id_paciente . ", " . $id_tipo_reg . ", NOW(), " . $ind_estado . ", " . $id_admision . ", '" .
                    $nombre_alt_tipo_reg . "', " . $ruta_arch_adjunto . ", " . $observaciones_hc . ", " . $id_usuario . ", " . $ind_habeas_data . ", @id)";
					
            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $resultado = $arrResultado["@id"];

            return $resultado;
        } catch (Exception $e) {
            return -2;
        }
    }

    public function editar_historia_clinica_alt($id_hc, $ind_estado, $nombre_alt_tipo_reg, $ruta_arch_adjunto, $id_usuario, $observaciones_hc = "") {
        try {
            if ($observaciones_hc != "") {
                $observaciones_hc = "'" . $observaciones_hc . "'";
            } else {
                $observaciones_hc = "NULL";
            }

            $sql = "CALL pa_editar_historia_clinica_alt(" . $id_hc . ", " . $ind_estado . ", '" . $nombre_alt_tipo_reg . "', '" . $ruta_arch_adjunto . "', " . $observaciones_hc . ", " . $id_usuario . ", @id)";

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $resultado = $arrResultado["@id"];

            return $resultado;
        } catch (Exception $e) {
            return -2;
        }
    }

    public function editar_historia_clinica_rem($id_admision, $id_tipo_cita, $id_tipo_reg, $id_usuario_rem, $observaciones_remision, $id_usuario) {
        try {
            if ($id_usuario_rem == "") {
                $id_usuario_rem = "NULL";
            }
            if ($observaciones_remision != "") {
                $observaciones_remision = "'" . $observaciones_remision . "'";
            } else {
                $observaciones_remision = "NULL";
            }

            $sql = "CALL pa_editar_historia_clinica_rem(" . $id_admision . ", " . $id_tipo_cita . ", " . $id_tipo_reg . ", " . $id_usuario_rem . ", " . $observaciones_remision . ", " . $id_usuario . ", @id)";

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $resultado = $arrResultado["@id"];

            return $resultado;
        } catch (Exception $e) {
            return -2;
        }
    }

    public function borrar_historia_clinica($id_hc, $observaciones_hc, $id_usuario) {
        try {
            $sql = "CALL pa_borrar_historia_clinica(" . $id_hc . ", '" . $observaciones_hc . "', " . $id_usuario . ", @id)";

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $resultado = $arrResultado["@id"];

            return $resultado;
        } catch (Exception $e) {
            return -2;
        }
    }

    public function unificar_historias_clinicas($id_paciente, $id_paciente_2, $id_tipo_documento, $numero_documento, $nombre_1, $nombre_2, $apellido_1, $apellido_2, $sexo, $fecha_nacimiento, $telefono_1, $telefono_2, $id_usuario) {
        try {
            if ($nombre_2 != "") {
                $nombre_2 = "'" . $nombre_2 . "'";
            } else {
                $nombre_2 = "NULL";
            }
            if ($apellido_2 != "") {
                $apellido_2 = "'" . $apellido_2 . "'";
            } else {
                $apellido_2 = "NULL";
            }
            if ($telefono_2 != "") {
                $telefono_2 = "'" . $telefono_2 . "'";
            } else {
                $telefono_2 = "NULL";
            }

            $sql = "CALL pa_unificar_historias_clinicas(" . $id_paciente . ", " . $id_paciente_2 . ", " . $id_tipo_documento . ", '" . $numero_documento . "', '" .
                    $nombre_1 . "', " . $nombre_2 . ", '" . $apellido_1 . "', " . $apellido_2 . ", '" . $sexo . "', STR_TO_DATE('" . $fecha_nacimiento . "', '%d/%m/%Y'), '" .
                    $telefono_1 . "', " . $telefono_2 . ", " . $id_usuario . ", @id)";

            echo($sql);
            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $resultado = $arrResultado["@id"];

            return $resultado;
        } catch (Exception $e) {
            return -2;
        }
    }

    public function crear_editar_colores_hc($id_hc, $arr_cadenas_colores, $id_usuario) {
        try {
            for ($i = 0; $i < count($arr_cadenas_colores); $i++) {
                $cadena_colores = $arr_cadenas_colores[$i];
                $sql = "CALL pa_crear_editar_colores_hc(" . $id_hc . ", " . $i . ", '" . $cadena_colores . "', " . $id_usuario . ", @id)";
                //echo($sql."<br />");

                $arrCampos[0] = "@id";
                $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
                $resultado = $arrResultado["@id"];
                if (intval($resultado) < 0) {
                    break;
                }
            }

            return $resultado;
        } catch (Exception $e) {
            return "-2";
        }
    }

    public function getListaHistoriaClinicaColoresCampos($id_hc) {
        try {
            $sql = "SELECT * FROM historia_clinica_colores_campos
						WHERE id_hc=" . $id_hc . "
						ORDER BY id_orden";
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaCiexAdmision($id_admision) {
        try {
            $sql = "SELECT HC.id_hc, CX.*
						FROM admisiones A
						INNER JOIN tipos_citas_det CD ON A.id_tipo_cita=CD.id_tipo_cita
						INNER JOIN historia_clinica HC ON A.id_admision=HC.id_admision AND CD.id_tipo_reg=HC.id_tipo_reg
						INNER JOIN diagnosticos_hc DH ON HC.id_hc=DH.id_hc
						INNER JOIN ciex_consolidado CX ON DH.cod_ciex=CX.codciex
						WHERE A.id_admision=" . $id_admision . "
						AND CD.ind_obligatorio=1
						ORDER BY CD.orden DESC, DH.orden";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getHistoriaClinicaRegAnt($id_hc) {
        try {
            $sql = "SELECT HC2.*
						FROM historia_clinica HC
						INNER JOIN historia_clinica HC2 ON HC.id_admision=HC2.id_admision AND HC.id_hc>HC2.id_hc
						WHERE HC.id_hc=" . $id_hc . "
						ORDER BY HC2.id_hc DESC";

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    //Método que retorna los registros de historia clínica creados por un usuario dentro de una admisión
    public function getListaHistoriaClinicaAdmUsuario($id_admision, $id_usuario) {
        try {
            $sql = "SELECT * FROM historia_clinica
						WHERE id_admision=" . $id_admision . "
						AND id_usuario_crea=" . $id_usuario . "
						ORDER BY id_hc";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getRegistrosHistoriaClinicaExamenes($id_paciente) {
        try {
            $sql = "SELECT HC.*, TR.nombre_tipo_reg, TR.id_clase_reg, IFNULL(TR.id_tipo_reg_base, HC.id_tipo_reg) AS id_tipo_reg_base,
						C.nombre_convenio, PL.nombre_plan, DATE_FORMAT(HC.fecha_hora_hc, '%d/%m/%Y') AS fecha_hc_t,
						DATE_FORMAT(HC.fecha_hora_hc, '%h:%i:%s %p') AS hora_hc_t, HF.archivo_hc,
						DATE_FORMAT(HC.fecha_hora_hc, '%d/%m/%Y') AS fecha_hora_hc_t, M.*, PA.*, OH.id_examen_hc
						FROM historia_clinica HC
						INNER JOIN pacientes PA ON HC.id_paciente=PA.id_paciente
						INNER JOIN examenes_optometria_hc OH ON HC.id_hc=OH.id_hc
						INNER JOIN maestro_examenes ME ON OH.id_examen=ME.id_examen
						LEFT JOIN tipos_registros_hc TR ON HC.id_tipo_reg=TR.id_tipo_reg
						LEFT JOIN menus M ON TR.id_menu=M.id_menu
						LEFT JOIN admisiones A ON HC.id_admision=A.id_admision
						LEFT JOIN convenios C ON A.id_convenio=C.id_convenio
						LEFT JOIN planes PL ON A.id_plan=PL.id_plan
						LEFT JOIN consultas_hc_fisica HF ON HC.id_hc=HF.id_hc
						WHERE HC.id_paciente=" . $id_paciente . "
						AND HC.ind_borrado=0
						AND EXISTS (
							SELECT * FROM examenes_optometria_hc_det OD
							WHERE OD.id_examen_hc=OH.id_examen_hc
						)
						ORDER BY ME.nombre_examen, OH.id_examen, OH.id_hc";

            //echo $sql;		
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaArchivosHCAntiguas($txt_archivo_hc, $limite) {
        try {
            $txt_archivo_hc = str_replace(" ", "%", $txt_archivo_hc);
            $sql = "SELECT *
						FROM archivos_hc_antiguas
						WHERE numero_documento LIKE '" . $txt_archivo_hc . "%'
						OR nombre_paciente LIKE '%" . $txt_archivo_hc . "%'
						ORDER BY nombre_paciente";
            if ($limite != "") {
                $sql .= " LIMIT " . $limite;
            }

            //echo $sql;		
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getSiguienteContArchAdjunto($id_hc) {
        try {
            $sql = "SELECT IFNULL(MAX(cont_arch) + 1, 0) AS cont_arch
						FROM hc_archivos_adjuntos
						WHERE id_hc=" . $id_hc;

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function crearHCArchivoAdjunto($id_hc, $ruta_archivo, $cont_arch, $ind_activo, $id_usuario) {
        try {
            $sql = "CALL pa_crear_hc_archivo_adjunto(" . $id_hc . ", '" . $ruta_archivo . "', " . $cont_arch . ", " . $ind_activo . ", " . $id_usuario . ", @id)";
            //echo($sql."<br />");

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $resultado = $arrResultado["@id"];

            return $resultado;
        } catch (Exception $e) {
            return "";
        }
    }

    public function borrarHCArchivoAdjunto($id_archivo, $id_hc, $id_usuario) {
        try {
            if ($id_archivo == "") {
                $id_archivo = "NULL";
            }
            $sql = "CALL pa_borrar_hc_archivo_adjunto(" . $id_archivo . ", " . $id_hc . ", " . $id_usuario . ", @id)";
            //echo($sql."<br />");

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $resultado = $arrResultado["@id"];

            return $resultado;
        } catch (Exception $e) {
            return "";
        }
    }

    public function getListaHCArchivosAdjuntos($id_hc) {
        try {
            $sql = "SELECT * FROM hc_archivos_adjuntos
						WHERE id_hc=" . $id_hc . "
						ORDER BY cont_arch";

            //echo $sql;		
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    //Función que inserta los registros de tonometría aplanática en una tabla temporal
    public function crear_registros_temp_tonometria($id_hc, $array_tonometria, $id_usuario) {
        try {
            $sql = "DELETE FROM temporal_consultas_oftalmologia_tono
						WHERE id_hc=" . $id_hc . "
						AND id_usuario=" . $id_usuario;
            $arrCampos_delete[0] = "@id";

            if ($this->ejecutarSentencia($sql, $arrCampos_delete)) {
                $j = 1;
                foreach ($array_tonometria as $fila_tonometria) {
                    $valor_od = $fila_tonometria["valor_od"];
                    $dilatado_od = $fila_tonometria["dilatado_od"];
                    $valor_oi = $fila_tonometria["valor_oi"];
                    $dilatado_oi = $fila_tonometria["dilatado_oi"];
                    $fecha = $fila_tonometria["fecha"];
                    $hora = $fila_tonometria["hora"];
                    if ($fecha == "") {
                        $fecha_hora = "";
                    } else {
                        $fecha_hora = $fecha . " " . $hora;
                    }
                    $sql = "INSERT INTO temporal_consultas_oftalmologia_tono
								(id_hc, id_usuario, tonometria_fecha_hora, tonometria_valor_od, tonometria_dilatado_od, tonometria_valor_oi, tonometria_dilatado_oi, orden)
								VALUES (" . $id_hc . ", " . $id_usuario . ", STR_TO_DATE('" . $fecha_hora . "', '%d/%m/%Y %H:%i'), '" .
                            $valor_od . "', '" . $dilatado_od . "', '" . $valor_oi . "', '" . $dilatado_oi . "', $j)";

                    $arrCampos[0] = "@id";
                    $this->ejecutarSentencia($sql, $arrCampos);
                    $j++;
                }
            }

            return 1;
        } catch (Exception $e) {
            return -2;
        }
    }

    public function consultarHistoriasRemisionesActivas($param) {
        try {
            $sql = "SELECT DISTINCT p.*, l.nombre_detalle AS tipo_documento, DATE_FORMAT(p.fecha_nacimiento, '%d/%m/%Y') AS fecha_nac_persona, CV.nombre_convenio 
                    FROM pacientes p
                    INNER JOIN admisiones a ON a.id_paciente = p.id_paciente
                    INNER JOIN listas_detalle l ON l.id_detalle = p.id_tipo_documento
                    INNER JOIN historia_clinica HC ON P.id_paciente = HC.id_paciente
                    INNER JOIN hc_remisiones HCR ON HC.id_hc = HCR.id_hc
                    INNER JOIN convenios CV ON p.id_convenio_paciente = CV.id_convenio
                    WHERE (p.numero_documento LIKE '%$param%' OR CONCAT(IFNULL(p.nombre_1,''),' ', IFNULL(p.nombre_2,''),' ', IFNULL(p.apellido_1,''),' ', IFNULL(p.apellido_2,'')) LIKE '%$param%')
                    AND HCR.ind_estado = 1
                    ORDER BY p.nombre_1, p.nombre_2, p.apellido_1, p.apellido_2
                    LIMIT 5;";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getHistoriasRemisionesActivas($idPaciente) {
        try {
            $sql = "SELECT HC.*, TRHC.nombre_tipo_reg, DATE_FORMAT(HC.fecha_crea,'%d/%m/%Y') AS fecha_HC
                    FROM historia_clinica HC
                    INNER JOIN hc_remisiones HCR ON HC.id_hc = HCR.id_hc
                    INNER JOIN tipos_registros_hc TRHC ON HC.id_tipo_reg = TRHC.id_tipo_reg
                    WHERE HCR.ind_estado = 1 AND HC.id_paciente = $idPaciente
                    GROUP BY HC.id_hc
                    ORDER BY fecha_crea LIMIT 100;";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
	
	public function getHistoriaClinicaDet($id_hc){
		try{
			$sql = "SELECT P.*, HC.fecha_hora_hc, HC.ind_estado, HC.ruta_arch_adjunto, AD.id_tipo_cita, AD.id_lugar_cita, AD.id_usuario_prof, AD.id_cita, AD.id_admision 
					FROM historia_clinica HC
					LEFT JOIN pacientes P ON HC.id_paciente = P.id_paciente
					LEFT JOIN admisiones AD ON AD.id_admision = HC.id_admision
					WHERE HC.id_hc=".$id_hc;
					
			return $this->getUnDato($sql);
			
		}catch(Exception $e){
		 return array();	
		}
	}
	
	public function getHistoriaClinicaEvolucion($id_hc){
		try{
			$sql = "SELECT P.*, HC.fecha_hora_hc, HC.ind_estado, HC.ruta_arch_adjunto, CE.id_usuario_crea AS profesional
					FROM historia_clinica HC 
					LEFT JOIN pacientes P ON HC.id_paciente = P.id_paciente 
					LEFT JOIN consultas_evoluciones CE ON CE.id_hc = HC.id_hc 
					WHERE HC.id_hc=".$id_hc;
			//echo($sql);
					
			return $this->getUnDato($sql);
			
		}catch(Exception $e){
		 return array();
			
			
		}
		
		
	}

}

?>
