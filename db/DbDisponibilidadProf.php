<?php

/*
  Página que permite las funciones en la tabla disponibilidad_prof
  Autor: Juan Pablo Gomez Quiroga - 02/10/2013
 */
require_once("DbConexion.php");

class DbDisponibilidadProf extends DbConexion {

    public function crearDisponibilidadProf($id_usuario_prof, $fecha_cal, $id_tipo_disponibilidad, $id_lugar_disp, $hora_ini, $hora_fin, $lugar_disp_d, $hora_ini2, $hora_fin2, $lugar_disp_d2, $id_usuario_crea) {

        $lista_disp_prof_det = array();
        $cont_aux = 0;
        if ($hora_ini != "-1") {
            $lista_disp_prof_det[$cont_aux]["hora_ini"] = $hora_ini;
            $lista_disp_prof_det[$cont_aux]["hora_fin"] = $hora_fin;
            $lista_disp_prof_det[$cont_aux]["id_lugar_disp"] = $lugar_disp_d;
            $cont_aux++;
        }
        if ($hora_ini2 != "-1") {
            $lista_disp_prof_det[$cont_aux]["hora_ini"] = $hora_ini2;
            $lista_disp_prof_det[$cont_aux]["hora_fin"] = $hora_fin2;
            $lista_disp_prof_det[$cont_aux]["id_lugar_disp"] = $lugar_disp_d2;
            $cont_aux++;
        }

        return crearDisponibilidadProf2($id_usuario_prof, $fecha_cal, $id_tipo_disponibilidad, $id_lugar_disp, $lista_disp_prof_det, $id_usuario_crea);
    }

    public function crearDisponibilidadProf2($id_usuario_prof, $fecha_cal, $id_tipo_disponibilidad, $id_lugar_disp, $lista_disp_prof_det, $id_usuario_crea) {
        try {
            //Se crea el registro general de disponibilidad
            $sql = "CALL pa_crear_disponibilidad_prof(" . $id_usuario_prof . ", '" . $fecha_cal . "', " . $id_tipo_disponibilidad . ", " . $id_lugar_disp . ", " . $id_usuario_crea . ", @id)";
            echo($sql."<br />");

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $id_resultado = $arrResultado["@id"];

            if ($id_resultado > 0) {
                //Se crea el detalle si existe
                if (count($lista_disp_prof_det) > 0) {
                    $id_disponibilidad_prof = $id_resultado;
                    foreach ($lista_disp_prof_det as $disp_prof_det_aux) {
                        if ($disp_prof_det_aux["hora_ini"] == "-1") {
                            $disp_prof_det_aux["hora_ini"] = "NULL";
                        } else {
                            $disp_prof_det_aux["hora_ini"] = "STR_TO_DATE('" . $fecha_cal . " " . $disp_prof_det_aux["hora_ini"] . "', '%Y-%m-%d %H:%i')";
                        }
                        if ($disp_prof_det_aux["hora_fin"] == "-1") {
                            $disp_prof_det_aux["hora_fin"] = "NULL";
                        } else {
                            $disp_prof_det_aux["hora_fin"] = "STR_TO_DATE('" . $fecha_cal . " " . $disp_prof_det_aux["hora_fin"] . "', '%Y-%m-%d %H:%i')";
                        }
                        if ($disp_prof_det_aux["id_lugar_disp"] == "-1") {
                            $disp_prof_det_aux["id_lugar_disp"] = "NULL";
                        }

                        $sql = "CALL pa_crear_disponibilidad_prof_det(" . $id_disponibilidad_prof . ", " . $disp_prof_det_aux["hora_ini"] . ", " . $disp_prof_det_aux["hora_fin"] . ", " .
                                $disp_prof_det_aux["id_lugar_disp"] . ", " . $id_usuario_prof . ", " . $id_usuario_crea . ", @id)";
                        //echo($sql."<br />");

                        $arrCampos[0] = "@id";
                        $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
                        $id_resultado = $arrResultado["@id"];

                        if ($id_resultado > 0) {
                            $id_disponibilidad_det = $id_resultado;

                            if (count($disp_prof_det_aux["arr_convenios"]) > 0) {
                                foreach ($disp_prof_det_aux["arr_convenios"] as $id_convenio_aux) {
                                    $sql = "CALL pa_crear_disponibilidad_prof_convenios(" . $id_disponibilidad_det . ", " . $id_convenio_aux . ", " . $id_usuario_crea . ", @id)";

                                    $arrCampos[0] = "@id";
                                    $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
                                    $id_resultado = $arrResultado["@id"];

                                    if ($id_resultado <= 0) {
                                        break;
                                    }
                                }
                            }

                            if (count($disp_prof_det_aux["arr_perfiles"]) > 0 && $id_resultado >= 0) {
                                foreach ($disp_prof_det_aux["arr_perfiles"] as $id_perfil_aux) {
                                    $sql = "CALL pa_crear_disponibilidad_prof_perfiles(" . $id_disponibilidad_det . ", " . $id_perfil_aux . ", " . $id_usuario_crea . ", @id)";

                                    $arrCampos[0] = "@id";
                                    $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
                                    $id_resultado = $arrResultado["@id"];

                                    if ($id_resultado <= 0) {
                                        break;
                                    }
                                }
                            }

                            if (count($disp_prof_det_aux["arr_tipos_citas"]) > 0 && $id_resultado >= 0) {
                                foreach ($disp_prof_det_aux["arr_tipos_citas"] as $id_tipo_cita_aux) {
                                    $sql = "CALL pa_crear_disponibilidad_prof_tipos_citas(" . $id_disponibilidad_det . ", " . $id_tipo_cita_aux . ", " . $id_usuario_crea . ", @id)";

                                    $arrCampos[0] = "@id";
                                    $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
                                    $id_resultado = $arrResultado["@id"];

                                    if ($id_resultado <= 0) {
                                        break;
                                    }
                                }
                            }
                        }

                        if ($id_resultado <= 0) {
                            break;
                        }
                    }
                }
            }

            return $id_resultado;
        } catch (Exception $e) {
            return -2;
        }
    }

    public function getDisponibilidadProf($id_usuario, $ano, $mes) {
        try {
            $sql = "SELECT id_disponibilidad, id_usuario, fecha_cal, id_tipo_disponibilidad, id_lugar_disp
					FROM disponibilidad_prof
					WHERE id_usuario=" . $id_usuario . "
					AND DATE_FORMAT(fecha_cal, '%Y')=" . $ano . "
					AND DATE_FORMAT(fecha_cal, '%m')=" . $mes . "
					ORDER BY fecha_cal";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return -2;
        }
    }

    public function getDisponibilidadProfDia($id_usuario, $fecha) {
        try {
            $sql = "SELECT id_disponibilidad, id_usuario, fecha_cal, id_tipo_disponibilidad, id_lugar_disp
					FROM disponibilidad_prof
					WHERE id_usuario=" . $id_usuario . "
					AND fecha_cal='" . $fecha . "'";

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return -2;
        }
    }

    public function getDisponibilidadProfDet($id_disponibilidad) {
        try {
            $sql = "SELECT DD.*, DATE_FORMAT(DD.hora_ini, '%H:%i') AS hora_ini_t,
					DATE_FORMAT(DD.hora_final, '%H:%i') AS hora_final_t, LD.nombre_detalle AS lugar_disp
					FROM disponibilidad_prof_det DD
					INNER JOIN listas_detalle LD ON DD.id_lugar_disp=LD.id_detalle
					WHERE DD.id_disponibilidad=" . $id_disponibilidad . "
					ORDER BY DD.hora_ini";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return -2;
        }
    }

    //Funcion pervia en el evento clic del combobox que examina si hay registros relacionados
    public function modifica_disponibilidad_prof($id_usuario, $fecha_cal, $id_tipo_disponibilidad, $proceso, $id_usuario_crea) {
        try {
            $sql = "CALL pa_modifica_disponibilidad_prof($id_usuario, '" . $fecha_cal . "', $id_tipo_disponibilidad, $proceso, $id_usuario_crea, @id)";
            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $id_usuario_creado = $arrResultado["@id"];

            return $id_usuario_creado;
        } catch (Exception $e) {
            return -2;
        }
    }

    public function getDisponibilidadProfActual() {//Consulta, disponibilidad profesional admision.php
        try {
            $sql = "SELECT U.*, CONCAT(U.nombre_usuario,' ', U.apellido_usuario) AS nombre_del_usuario
                    FROM disponibilidad_prof D
                    INNER JOIN usuarios U ON D.id_usuario=U.id_usuario
                    WHERE D.fecha_cal=DATE(NOW())
                    AND D.id_tipo_disponibilidad=11

                    UNION

                    SELECT U.*, CONCAT(U.nombre_usuario,' ', U.apellido_usuario) AS nombre_del_usuario
                    FROM disponibilidad_prof D
                    INNER JOIN usuarios U ON D.id_usuario=U.id_usuario
                    INNER JOIN disponibilidad_prof_det DD ON D.id_disponibilidad=DD.id_disponibilidad
                    WHERE D.fecha_cal=DATE(NOW())
                    AND D.id_tipo_disponibilidad=12
                    AND NOW() BETWEEN DD.hora_ini AND DD.hora_final

                    ORDER BY nombre_usuario, apellido_usuario";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return -2;
        }
    }

    public function getListaUsuariosDisponiblesFecha($id_perfil, $fecha_cal) {
        try {
            $sql = "SELECT DISTINCT u.*, CONCAT(u.nombre_usuario, ' ', u.apellido_usuario) AS nombre_completo, dp.id_tipo_disponibilidad
					FROM usuarios_perfiles up
					INNER JOIN usuarios u ON u.id_usuario=up.id_usuario
					INNER JOIN perfiles p on p.id_perfil=up.id_perfil
					INNER JOIN disponibilidad_prof dp ON dp.id_usuario=u.id_usuario
					WHERE up.id_perfil=" . $id_perfil . "
					AND u.ind_activo=1
					AND dp.fecha_cal=STR_TO_DATE('" . $fecha_cal . "', '%Y-%m-%d')
					AND dp.id_tipo_disponibilidad<>13
					ORDER BY u.nombre_usuario, u.apellido_usuario";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function duplicar_semana_disponibilidad_prof($id_usuario_prof, $mes, $ano, $id_usuario) {
        try {
            $sql = "CALL pa_duplicar_semana_disponibilidad_prof(" . $id_usuario_prof . ", " . $mes . ", " . $ano . ", " . $id_usuario . ", @id)";
            echo($sql);

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $id_resultado = $arrResultado["@id"];

            return $id_resultado;
        } catch (Exception $e) {
            return -2;
        }
    }

    public function getDisponibilidadProfDetFechaHora($id_disponibilidad, $fecha, $hora) {
        try {
            $sql = "SELECT DD.*, DATE_FORMAT(DD.hora_ini, '%H:%i') AS hora_ini_t,
					DATE_FORMAT(DD.hora_final, '%H:%i') AS hora_final_t, LD.nombre_detalle AS lugar_disp
					FROM disponibilidad_prof_det DD
					INNER JOIN listas_detalle LD ON DD.id_lugar_disp=LD.id_detalle
					WHERE DD.id_disponibilidad=" . $id_disponibilidad . "
					AND STR_TO_DATE('" . $fecha . " " . $hora . "', '%Y-%m-%d %H:%i') BETWEEN DD.hora_ini AND DD.hora_final
					ORDER BY DD.hora_ini";

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return -2;
        }
    }

    public function getListaUsuariosDisponiblesTipoReg($id_tipo_reg, $id_lugar_disp) {
        try {
            $sql = "SELECT U.*
					FROM tipos_registros_hc TR
					INNER JOIN permisos PE ON TR.id_menu=PE.id_menu
					INNER JOIN usuarios_perfiles UP ON PE.id_perfil=UP.id_perfil
					INNER JOIN usuarios U ON UP.id_usuario=U.id_usuario
					INNER JOIN disponibilidad_prof DP ON U.id_usuario=DP.id_usuario
					WHERE TR.id_tipo_reg=" . $id_tipo_reg . "
					AND PE.tipo_acceso=2
					AND DP.id_tipo_disponibilidad=11
					AND DP.fecha_cal=CURDATE()
					AND DP.id_lugar_disp=" . $id_lugar_disp . "
					
					UNION ALL
					
					SELECT U.*
					FROM tipos_registros_hc TR
					INNER JOIN permisos PE ON TR.id_menu=PE.id_menu
					INNER JOIN usuarios_perfiles UP ON PE.id_perfil=UP.id_perfil
					INNER JOIN usuarios U ON UP.id_usuario=U.id_usuario
					INNER JOIN disponibilidad_prof DP ON U.id_usuario=DP.id_usuario
					INNER JOIN disponibilidad_prof_det PD ON DP.id_disponibilidad=PD.id_disponibilidad
					WHERE TR.id_tipo_reg=" . $id_tipo_reg . "
					AND PE.tipo_acceso=2
					AND DP.id_tipo_disponibilidad=12
					AND NOW() BETWEEN PD.hora_ini AND PD.hora_final
					AND PD.id_lugar_disp=" . $id_lugar_disp . "
					
					ORDER BY nombre_usuario, apellido_usuario";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return -2;
        }
    }

    //Esta función devuelve los usuarios de todas las sedes poniendo en primer lugar los de la sede especificada
    public function getListaUsuariosDisponiblesTipoReg2($id_tipo_reg, $id_lugar_disp) {
        try {
            $sql = "SELECT U.*, DP.id_lugar_disp, LD.nombre_detalle AS lugar_disp
					FROM tipos_registros_hc TR
					INNER JOIN permisos PE ON TR.id_menu=PE.id_menu
					INNER JOIN usuarios_perfiles UP ON PE.id_perfil=UP.id_perfil
					INNER JOIN usuarios U ON UP.id_usuario=U.id_usuario
					INNER JOIN disponibilidad_prof DP ON U.id_usuario=DP.id_usuario
					INNER JOIN listas_detalle LD ON DP.id_lugar_disp=LD.id_detalle
					WHERE TR.id_tipo_reg=" . $id_tipo_reg . "
					AND PE.tipo_acceso=2
					AND DP.id_tipo_disponibilidad=11
					AND DP.fecha_cal=CURDATE()
					
					UNION ALL
					
					SELECT U.*, DP.id_lugar_disp, LD.nombre_detalle AS lugar_disp
					FROM tipos_registros_hc TR
					INNER JOIN permisos PE ON TR.id_menu=PE.id_menu
					INNER JOIN usuarios_perfiles UP ON PE.id_perfil=UP.id_perfil
					INNER JOIN usuarios U ON UP.id_usuario=U.id_usuario
					INNER JOIN disponibilidad_prof DP ON U.id_usuario=DP.id_usuario
					INNER JOIN disponibilidad_prof_det PD ON DP.id_disponibilidad=PD.id_disponibilidad
					INNER JOIN listas_detalle LD ON DP.id_lugar_disp=LD.id_detalle
					WHERE TR.id_tipo_reg=" . $id_tipo_reg . "
					AND PE.tipo_acceso=2
					AND DP.id_tipo_disponibilidad=12
					AND NOW() BETWEEN PD.hora_ini AND PD.hora_final
					
					ORDER BY CASE id_lugar_disp WHEN " . $id_lugar_disp . " THEN 1 ELSE 2 END, id_lugar_disp, nombre_usuario, apellido_usuario";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return -2;
        }
    }

    public function getListaDisponibilidadProfConvenios($id_disponibilidad_det) {
        try {
            $sql = "SELECT PC.*, C.nombre_convenio
					FROM disponibilidad_prof_convenios PC
					INNER JOIN convenios C ON PC.id_convenio=C.id_convenio
					WHERE PC.id_disponibilidad_det=" . $id_disponibilidad_det . "
					ORDER BY C.nombre_convenio";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaDisponibilidadProfPerfiles($id_disponibilidad_det) {
        try {
            $sql = "SELECT PP.*, P.nombre_perfil
					FROM disponibilidad_prof_perfiles PP
					INNER JOIN perfiles P ON PP.id_perfil=P.id_perfil
					WHERE PP.id_disponibilidad_det=" . $id_disponibilidad_det . "
					ORDER BY P.nombre_perfil";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaDisponibilidadProfTiposCitas($id_disponibilidad_det) {
        try {
            $sql = "SELECT PT.*, TC.nombre_tipo_cita
					FROM disponibilidad_prof_tipos_citas PT
					INNER JOIN tipos_citas TC ON PT.id_tipo_cita=TC.id_tipo_cita
					WHERE PT.id_disponibilidad_det=" . $id_disponibilidad_det . "
					ORDER BY TC.nombre_tipo_cita";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaDisponibilidadProfConveniosUsuarioHora($id_usuario_prof, $fecha_cal, $hora_ini, $hora_final) {
        try {
            $sql = "SELECT DC.*, C.nombre_convenio
					FROM disponibilidad_prof D
					INNER JOIN disponibilidad_prof_det DD ON D.id_disponibilidad=DD.id_disponibilidad
					INNER JOIN disponibilidad_prof_convenios DC ON DD.id_disponibilidad_det=DC.id_disponibilidad_det
					INNER JOIN convenios C ON DC.id_convenio=C.id_convenio
					WHERE D.id_usuario=" . $id_usuario_prof . "
					AND D.fecha_cal='" . $fecha_cal . "'
					AND DD.hora_ini<='" . $hora_ini . "'
					AND DD.hora_final>='" . $hora_final . "'
					ORDER BY C.nombre_convenio";

            //echo($sql."<br />");
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaDisponibilidadProfPerfilesUsuarioHora($id_usuario_prof, $fecha_cal, $hora_ini, $hora_final) {
        try {
            $sql = "SELECT DP.*, P.nombre_perfil
					FROM disponibilidad_prof D
					INNER JOIN disponibilidad_prof_det DD ON D.id_disponibilidad=DD.id_disponibilidad
					INNER JOIN disponibilidad_prof_perfiles DP ON DD.id_disponibilidad_det=DP.id_disponibilidad_det
					INNER JOIN perfiles P ON DP.id_perfil=P.id_perfil
					WHERE D.id_usuario=" . $id_usuario_prof . "
					AND D.fecha_cal='" . $fecha_cal . "'
					AND DD.hora_ini<='" . $hora_ini . "'
					AND DD.hora_final>='" . $hora_final . "'
					ORDER BY P.nombre_perfil";

            //echo($sql."<br />");
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaDisponibilidadProfTiposCitasUsuarioHora($id_usuario_prof, $fecha_cal, $hora_ini, $hora_final) {
        try {
            $sql = "SELECT DT.*, TC.nombre_tipo_cita
					FROM disponibilidad_prof D
					INNER JOIN disponibilidad_prof_det DD ON D.id_disponibilidad=DD.id_disponibilidad
					INNER JOIN disponibilidad_prof_tipos_citas DT ON DD.id_disponibilidad_det=DT.id_disponibilidad_det
					INNER JOIN tipos_citas TC ON DT.id_tipo_cita=TC.id_tipo_cita
					WHERE D.id_usuario=" . $id_usuario_prof . "
					AND D.fecha_cal='" . $fecha_cal . "'
					AND DD.hora_ini<='" . $hora_ini . "'
					AND DD.hora_final>='" . $hora_final . "'
					ORDER BY TC.nombre_tipo_cita";

            //echo($sql."<br />");
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

}

?>
