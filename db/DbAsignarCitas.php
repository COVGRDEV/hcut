<?php

require_once("DbConexion.php");

class DbAsignarCitas extends DbConexion {
    /*
     * Muestra la disponibilidad de un usuario en un mes especifico
     */

    public function getDisponibilidadUsuariosMes($mes, $anio, $id_usuario) {
        try {
            $mes_fin = intval($mes, 10) + 1;
            $anio_fin = intval($anio);
            if ($mes_fin > 12) {
                $mes_fin = "01";
                $anio_fin++;
            } else {
                $mes_fin = substr("0" . $mes_fin, -2);
            }
            $sql = "SELECT D.*
					FROM disponibilidad_prof D
					WHERE D.fecha_cal BETWEEN '" . $anio . "-" . $mes . "-01' AND DATE_SUB('" . $anio_fin . "-" . $mes_fin . "-01', INTERVAL 1 DAY) ";
            if ($id_usuario > 0) {
                $sql .= "AND D.id_usuario=" . $id_usuario . " ";
            }
            $sql .= "ORDER BY D.id_usuario, D.fecha_cal";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /*
     * Muestra la disponibilidad de un usuario en un mes especifico
     */

    public function getDisponibilidadDetaUsuariosMes($mes, $anio, $id_usuario) {
        try {
            $mes_fin = intval($mes, 10) + 1;
            $anio_fin = intval($anio);
            if ($mes_fin > 12) {
                $mes_fin = "01";
                $anio_fin++;
            } else {
                $mes_fin = substr("0" . $mes_fin, -2);
            }
            $sql = "SELECT D.id_usuario, D.fecha_cal, DD.*
					FROM disponibilidad_prof D
					INNER JOIN disponibilidad_prof_det DD ON D.id_disponibilidad=DD.id_disponibilidad
					WHERE D.fecha_cal BETWEEN '" . $anio . "-" . $mes . "-01' AND DATE_SUB('" . $anio_fin . "-" . $mes_fin . "-01', INTERVAL 1 DAY) ";
            if ($id_usuario > 0) {
                $sql .= "AND D.id_usuario=" . $id_usuario . " ";
            }
            $sql .= "ORDER BY D.id_usuario, D.fecha_cal, DD.hora_ini";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Muestra la lista de los usuarios que tienen tiempos en citas
     */
    public function getListaUsuariosCitas($id_usuario, $ind_citas = -1, $ind_anonimo = -1) {
        try {
            $sql = "SELECT DISTINCT U.*, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_completo,
					CASE WHEN TD.id_usuario IS NOT NULL THEN 1 ELSE 0 END AS ind_disponible
					FROM usuarios U
					INNER JOIN usuarios_perfiles UP ON U.id_usuario=UP.id_usuario
					INNER JOIN perfiles P ON UP.id_perfil=P.id_perfil
					LEFT JOIN (
						SELECT DISTINCT id_usuario
						FROM disponibilidad_prof
						WHERE fecha_cal>=DATE(NOW())
						AND id_tipo_disponibilidad<>13
					) TD ON U.id_usuario=TD.id_usuario
					WHERE U.ind_activo=1
					AND P.ind_atiende=1 ";
            if (trim($id_usuario) != "") {
                $sql .= "AND U.id_usuario=" . $id_usuario . " ";
            }
            if (trim($ind_anonimo) >= 0) {
                $sql .= "AND U.ind_anonimo=" . $ind_anonimo . " ";
            }
            if ($ind_citas >= 0) {
                $sql .= "AND P.ind_citas=" . $ind_citas . " ";
            }
            $sql .= "ORDER BY U.nombre_usuario, U.apellido_usuario";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Muestra la lista de los usuarios y perfiles activos que pueden asignar citas
     */
    public function getListaUsuariosCitasActivo() {
        try {
            $sql = "SELECT DISTINCT U.*, CONCAT(U.nombre_usuario,' ',U.apellido_usuario) AS nombre_completo_aux
					FROM usuarios U
					INNER JOIN usuarios_perfiles UP ON UP.id_usuario = U.id_usuario
					INNER JOIN perfiles P ON P.id_perfil = UP.id_perfil
					WHERE U.ind_activo=1
					AND P.ind_activo=1
					AND P.ind_atiende=1
					ORDER BY U.nombre_usuario, U.apellido_usuario";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Obtien la disponibildad de uno o todos los usuario en un dia
     */
    public function getDisponibilidadUsuariosDia($fecha, $id_usuario) {
        try {
            $sql = "SELECT DISTINCT U.*, D.*, LD.nombre_detalle AS lugar_disp
					FROM usuarios U
					INNER JOIN disponibilidad_prof D ON U.id_usuario=D.id_usuario
					INNER JOIN listas_detalle LD ON D.id_lugar_disp=LD.id_detalle
					INNER JOIN usuarios_perfiles UP ON U.id_usuario=UP.id_usuario
					INNER JOIN perfiles P ON UP.id_perfil=P.id_perfil
					WHERE D.fecha_cal='" . $fecha . "'
					AND P.ind_citas=1 ";
            if ($id_usuario > 0) {
                $sql .= "AND U.id_usuario=" . $id_usuario . " ";
            }
            $sql .= "AND (D.id_tipo_disponibilidad<>13
					 OR U.id_usuario IN (
						SELECT id_usuario_prof FROM citas
						WHERE fecha_cita BETWEEN '" . $fecha . " 00:00:00' AND '" . $fecha . " 23:59:59'
						AND ind_activo=1
					 ))
					 ORDER BY U.nombre_usuario, U.apellido_usuario";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Obtiene la disponibilidad de los usuarios despues del dia actual
     */
    public function getDisponibilidadUsuarios($id_usuario) {
        try {
            $sql = "SELECT u.*, d.*, ld.nombre_detalle AS lugar_disp
					FROM usuarios u
					INNER JOIN disponibilidad_prof d ON d.id_usuario = u.id_usuario
					INNER JOIN listas_detalle ld ON ld.id_detalle=d.id_lugar_disp
					INNER JOIN calendarios c ON d.fecha_cal=c.fecha_cal
					WHERE d.fecha_cal>=DATE(NOW())
					AND u.id_usuario=" . $id_usuario . "
					AND d.id_tipo_disponibilidad<>13
					AND c.ind_laboral=1
					ORDER BY d.fecha_cal";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Muestra el detalle de disponibilidad
     */
    public function getDisponibilidadUsuariosDiaDetalle($id_disponibilidad) {
        try {
            $sql = "SELECT * FROM disponibilidad_prof_det
					WHERE id_disponibilidad = $id_disponibilidad
					ORDER BY hora_ini";

            //echo $sql;
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Devuelve las citas que tiene un usuario de una fecha especifica
     */
    public function getCitaFecha($id_usuario, $fecha) {
        try {
            $sql = "SELECT c.*, tc.nombre_tipo_cita, u.nombre_usuario, u.apellido_usuario,
					DATE_FORMAT(c.fecha_crea, '%d/%m/%Y %h:%i:%s %p') AS fecha_crea_t
					FROM citas c
					INNER JOIN tiempos_citas_prof t ON t.id_tipo_cita=c.id_tipo_cita AND c.id_usuario_prof=t.id_usuario
					INNER JOIN tipos_citas tc ON tc.id_tipo_cita = t.id_tipo_cita
					INNER JOIN usuarios u ON c.id_usuario_crea=u.id_usuario
					WHERE c.id_usuario_prof=" . $id_usuario . "
					AND c.fecha_cita='" . $fecha . "'
					AND c.ind_activo=1";

            //echo $sql."<br />";
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Devuelve las citas que tiene un usuario de una fecha especifica
     */
    public function getListaCitasFecha($id_usuario, $fecha) {
        try {
            $sql = "SELECT C.id_cita, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, P.id_tipo_documento, P.numero_documento,
					C.telefono_contacto, C.ind_lentes, P.fecha_nacimiento, P.id_paciente, C.fecha_cita, C.id_tipo_cita, C.tiempo_cita,
					C.id_usuario_prof, C.ind_activo, C.id_estado_cita, C.id_convenio, C.observacion_cita, C.observacion_cancela,
					C.id_lugar_cita, C.fecha_llegada, C.fecha_ingreso, C.id_cita_ori, C.ind_confirmada, C.id_usuario_confirma,
					C.fecha_confirma, C.id_tipo_cancela, C.ind_no_pago, C.id_prog_cx, TC.nombre_tipo_cita, E.nombre_convenio,
					DATE_ADD(C.fecha_cita, INTERVAL C.tiempo_cita MINUTE) AS fecha_fin_cita, C.id_usuario_crea, C.fecha_crea,
					C.id_usuario_mod, C.fecha_mod
					FROM citas C
					INNER JOIN pacientes P ON C.id_paciente=P.id_paciente
					INNER JOIN tiempos_citas_prof T ON T.id_tipo_cita=C.id_tipo_cita AND C.id_usuario_prof=T.id_usuario
					INNER JOIN tipos_citas TC ON TC.id_tipo_cita=T.id_tipo_cita
					LEFT JOIN convenios E ON C.id_convenio=E.id_convenio
					WHERE c.id_usuario_prof=" . $id_usuario . "
					AND c.fecha_cita BETWEEN '" . $fecha . " 00:00:00' AND '" . $fecha . " 23:59:59'
					AND c.ind_activo=1
					ORDER BY c.fecha_cita, c.id_cita";

            //echo($sql."<br />");
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getCantidadCitasEstadoFecha($id_usuario, $id_estado_cita, $fecha) {
        try {
            $sql = "SELECT COUNT(*) AS cantidad
					FROM citas
					WHERE id_usuario_prof=" . $id_usuario . "
					AND fecha_cita BETWEEN '" . $fecha . " 00:00:00' AND '" . $fecha . " 23:59:59'
					AND ind_activo=1
					AND id_estado_cita=" . $id_estado_cita;

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Devuelve las citas de un mes dado para un usuario
     */
    public function getListaCitasMes($mes, $anio, $id_usuario) {
        try {
            $mes_fin = intval($mes, 10) + 1;
            $anio_fin = intval($anio);
            if ($mes_fin > 12) {
                $mes_fin = "01";
                $anio_fin++;
            } else {
                $mes_fin = substr("0" . $mes_fin, -2);
            }
            $sql = "SELECT C.*, DATE_ADD(C.fecha_cita, INTERVAL C.tiempo_cita MINUTE) AS fecha_fin_cita
					FROM citas C
					WHERE C.fecha_cita BETWEEN '" . $anio . "-" . $mes . "-01' AND DATE_SUB('" . $anio_fin . "-" . $mes_fin . "-01', INTERVAL 1 SECOND)
					AND C.ind_activo=1 ";
            if ($id_usuario > 0) {
                $sql .= "AND C.id_usuario_prof=" . $id_usuario . " ";
            }
            $sql .= "ORDER BY C.id_usuario_prof, C.fecha_cita, C.id_cita";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Verificar si existe una cita en una franja de horas
     */
    public function getVerificarExisteCitas($fecha_hora_desde, $fecha_hora_hasta) {
        try {
            $sql = "SELECT DATE_FORMAT(fecha_cita, '%H') AS hora_cita, DATE_FORMAT(fecha_cita, '%i') AS minuto_cita  FROM citas
					WHERE fecha_cita BETWEEN '$fecha_hora_desde' AND '$fecha_hora_hasta'
					AND ind_activo = 1
					ORDER BY fecha_cita ASC LIMIT 1";

            //echo $sql."<br />";
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Devuelve los tipos de citas que tiene un usuario
     */
    public function getTiposCitasUsuario($id_usuario) {
        try {
            $sql = "SELECT *, 'Minutos' FROM usuarios u
					INNER JOIN tiempos_citas_prof tc ON tc.id_usuario = u.id_usuario
					INNER JOIN tipos_citas tt ON tt.id_tipo_cita = tc.id_tipo_cita
					WHERE u.id_usuario = $id_usuario
					AND u.ind_activo = 1
					AND tc.ind_activo = 1
					AND tt.ind_activo = 1
					ORDER BY tt.nombre_tipo_cita";

            //echo $sql."<br />";
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Obtener el tiempo de un tipo de cita de un usuario
     */
    public function getTiempoTipoCita($id_usuario, $tipo_cita) {
        try {
            $sql = "SELECT * FROM tiempos_citas_prof
					WHERE id_usuario = $id_usuario
					AND id_tipo_cita = $tipo_cita";

            //echo $sql."<br />";
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Para crear una nueva cita
     */
    public function crearCita($txt_primer_nombre, $txt_segundo_nombre, $txt_primer_apellido, $txt_segundo_apellido, $cmb_tipo_documento, 
            $txt_numero_documento, $txt_numero_telefono, $cmb_convenio, $cmb_lentes, $txt_observacion_cita, $fecha_hora_cita, $cmb_tipo_cita, 
            $tiempo_cita, $hdd_id_usuario, $id_usuario_crea, $hdd_id_paciente, $hdd_lugar_cita, $ind_reasignar, $id_cita_reasignar, $chk_no_pago, 
            $id_prog_cx, $sexo, $fecha_nacimiento, $pais, $status_seguro, $cmb_cod_dep_res, $cmb_cod_mun_res, $direccion, $exento_convenio, $rango, 
            $tipoCotizante, $txt_nom_dep_res, $txt_nom_mun_res, $cmb_cod_plan,$cmb_sede_alter) {

        $txt_primer_nombre == "" ? $txt_primer_nombre = "NULL" : $txt_primer_nombre = "'" . $txt_primer_nombre . "'";
        $txt_segundo_nombre == "" ? $txt_segundo_nombre = "NULL" : $txt_segundo_nombre = "'" . $txt_segundo_nombre . "'";
        $txt_primer_apellido == "" ? $txt_primer_apellido = "NULL" : $txt_primer_apellido = "'" . $txt_primer_apellido . "'";
        $txt_segundo_apellido == "" ? $txt_segundo_apellido = "NULL" : $txt_segundo_apellido = "'" . $txt_segundo_apellido . "'";
        $cmb_tipo_documento == "" ? $cmb_tipo_documento = "NULL" : $cmb_tipo_documento = "" . $cmb_tipo_documento . "";
        $txt_numero_documento == "" ? $txt_numero_documento = "NULL" : $txt_numero_documento = "'" . $txt_numero_documento . "'";
        $txt_numero_telefono == "" ? $txt_numero_telefono = "NULL" : $txt_numero_telefono = "'" . $txt_numero_telefono . "'";
        $cmb_convenio == "" ? $cmb_convenio = "NULL" : $cmb_convenio = "" . $cmb_convenio . "";
        $cmb_lentes == "" ? $cmb_lentes = "NULL" : $cmb_lentes = "" . $cmb_lentes . "";
        $txt_observacion_cita == "" ? $txt_observacion_cita = "NULL" : $txt_observacion_cita = "'" . $txt_observacion_cita . "'";
        $fecha_hora_cita == "" ? $fecha_hora_cita = "NULL" : $fecha_hora_cita = "'" . $fecha_hora_cita . "'";
        $cmb_tipo_cita == "" ? $cmb_tipo_cita = "NULL" : $cmb_tipo_cita = "" . $cmb_tipo_cita . "";
        $tiempo_cita == "" ? $tiempo_cita = "NULL" : $tiempo_cita = "" . $tiempo_cita . "";
        $hdd_id_usuario == "" ? $hdd_id_usuario = "NULL" : $hdd_id_usuario = "" . $hdd_id_usuario . "";
        $id_usuario_crea == "" ? $id_usuario_crea = "NULL" : $id_usuario_crea = "" . $id_usuario_crea . "";
        $hdd_id_paciente == "" ? $hdd_id_paciente = "NULL" : $hdd_id_paciente = "" . $hdd_id_paciente . "";
        $hdd_lugar_cita == "" ? $hdd_lugar_cita = "NULL" : $hdd_lugar_cita = "" . $hdd_lugar_cita . "";
        $ind_reasignar == "" ? $ind_reasignar = "NULL" : $ind_reasignar = "" . $ind_reasignar . "";

        $chk_no_pago == "" ? $chk_no_pago = "NULL" : $chk_no_pago = "" . $chk_no_pago . "";
        $id_prog_cx == "" ? $id_prog_cx = "NULL" : $id_prog_cx = "" . $id_prog_cx . "";
        $sexo == "" ? $sexo = "NULL" : $sexo = "" . $sexo . "";
        $cmb_lentes = "NULL";
        $fecha_nacimiento == "" ? $fecha_nacimiento = "NULL" : $fecha_nacimiento = "STR_TO_DATE('" . $fecha_nacimiento . "', '%d/%m/%Y')";
        $pais == "" ? $pais = "NULL" : $pais = "" . $pais . "";
        $status_seguro == "" ? $status_seguro = "NULL" : $status_seguro = "" . $status_seguro . "";

        $cmb_cod_dep_res == "" ? $cmb_cod_dep_res = "NULL" : $cmb_cod_dep_res = "" . $cmb_cod_dep_res . "";
        $cmb_cod_mun_res == "" ? $cmb_cod_mun_res = "NULL" : $cmb_cod_mun_res = "'" . $cmb_cod_mun_res . "'";
        $direccion == "" ? $direccion = "NULL" : $direccion = "'" . $direccion . "'";

        $exento_convenio == "" ? $exento_convenio = "NULL" : $exento_convenio = "" . $exento_convenio . "";
        $rango == "" ? $rango = 0 : $rango = "" . $rango . "";
        $tipoCotizante == "" ? $tipoCotizante = 0 : $tipoCotizante = "" . $tipoCotizante . "";

        $txt_nom_dep_res == "" ? $txt_nom_dep_res = "NULL" : $txt_nom_dep_res = "'" . $txt_nom_dep_res . "'";
        $txt_nom_mun_res == "" ? $txt_nom_mun_res = "NULL" : $txt_nom_mun_res = "'" . $txt_nom_mun_res . "'";
        $cmb_cod_plan == "" ? $cmb_cod_plan = "NULL" : $cmb_cod_plan = "" . $cmb_cod_plan . "";
		$cmb_sede_alter == "" ? $cmb_sede_alter = "NULL" : $cmb_sede_alter = "" . $cmb_sede_alter . "";

        try {

            if (intval($id_cita_reasignar) == 0) {
                $id_cita_reasignar = "NULL";
            }

            $sql = "CALL pa_crear_cita(" . $txt_primer_nombre . "," . $txt_segundo_nombre . "," . $txt_primer_apellido . "," . $txt_segundo_apellido . "," .
                    $cmb_tipo_documento . "," . $txt_numero_documento . "," . $txt_numero_telefono . "," . $cmb_lentes . "," . $chk_no_pago . "," . $cmb_convenio . "," .
                    $txt_observacion_cita . "," . $fecha_hora_cita . "," . $cmb_tipo_cita . "," . $tiempo_cita . "," . $hdd_id_usuario . "," . $hdd_id_paciente . "," .
                    $hdd_lugar_cita . "," . $ind_reasignar . "," . $id_cita_reasignar . "," . $id_prog_cx . "," . $sexo . "," . $fecha_nacimiento . "," . $pais . "," .
                    $status_seguro . "," . $id_usuario_crea . "," . $cmb_cod_dep_res . "," . $cmb_cod_mun_res . "," . $direccion . "," . $exento_convenio . "," . $rango . "," . $tipoCotizante . ", " .
                    $txt_nom_dep_res . "," . $txt_nom_mun_res . "," . $cmb_cod_plan . ", " . $cmb_sede_alter . ", @id)";

          
            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $respuesta = $arrResultado["@id"];



            return $respuesta;
        } catch (Exception $e) {
            return -2;
        }
    }

    /**
     * Para editar cita
     */
    public function actualizarCita($txt_primer_nombre, $txt_segundo_nombre, $txt_primer_apellido, $txt_segundo_apellido, $cmb_tipo_documento, 
                                    $txt_numero_documento, $txt_numero_telefono, $cmb_convenio, $cmb_lentes, $txt_observacion_cita, $id_usuario_edita, 
                                    $hdd_id_cita, $hdd_lugar_cita, $chk_no_pago, $hdd_id_paciente, $sexo, $fecha_nacimiento, $pais, $estatus_seguro, $departamento, 
                                    $municipio, $direccion, $excento, $rango, $tipoCotizante,$txt_nom_dep_res,$txt_nom_mun_res,$cmb_cod_plan) {
        try {

            $txt_primer_nombre == "" ? $txt_primer_nombre = "NULL" : $txt_primer_nombre = "'" . $txt_primer_nombre . "'";
            $txt_segundo_nombre == "" ? $txt_segundo_nombre = "NULL" : $txt_segundo_nombre = "'" . $txt_segundo_nombre . "'";
            $txt_primer_apellido == "" ? $txt_primer_apellido = "NULL" : $txt_primer_apellido = "'" . $txt_primer_apellido . "'";
            $txt_segundo_apellido == "" ? $txt_segundo_apellido = "NULL" : $txt_segundo_apellido = "'" . $txt_segundo_apellido . "'";
            $cmb_tipo_documento == "" ? $cmb_tipo_documento = "NULL" : $cmb_tipo_documento = "" . $cmb_tipo_documento . "";
            $txt_numero_documento == "" ? $txt_numero_documento = "NULL" : $txt_numero_documento = "'" . $txt_numero_documento . "'";
            $txt_numero_telefono == "" ? $txt_numero_telefono = "NULL" : $txt_numero_telefono = "'" . $txt_numero_telefono . "'";
            $txt_observacion_cita == "" ? $txt_observacion_cita = "NULL" : $txt_observacion_cita = "'" . $txt_observacion_cita . "'";
            $id_usuario_edita == "" ? $id_usuario_edita = "NULL" : $id_usuario_edita = "" . $id_usuario_edita . "";
            $hdd_id_cita == "" ? $hdd_id_cita = "NULL" : $hdd_id_cita = "" . $hdd_id_cita . "";
            $hdd_lugar_cita == "" ? $hdd_lugar_cita = "NULL" : $hdd_lugar_cita = "" . $hdd_lugar_cita . "";
            $chk_no_pago == "" ? $chk_no_pago = "NULL" : $chk_no_pago = "" . $chk_no_pago . "";
            $hdd_id_paciente == "" ? $hdd_id_paciente = "NULL" : $hdd_id_paciente = "" . $hdd_id_paciente . "";
            $cmb_lentes = "NULL"; /* -> HCUT - variable deshabilitada */
            $sexo == "" ? $sexo = "NULL" : $sexo = "" . $sexo . "";
            $fecha_nacimiento == "" ? $fecha_nacimiento = "NULL" : $fecha_nacimiento = "STR_TO_DATE('" . $fecha_nacimiento . "', '%d/%m/%Y')";
            $pais == "" ? $pais = "NULL" : $pais = "" . $pais . "";
            $estatus_seguro == "" ? $estatus_seguro = "NULL" : $estatus_seguro = "" . $estatus_seguro . "";
            $departamento == "" ? $departamento = "NULL" : $departamento = "" . $departamento . "";
            $municipio == "" ? $municipio = "NULL" : $municipio = "'" . $municipio . "'";
            $direccion == "" ? $direccion = "NULL" : $direccion = "'" . $direccion . "'";
            $excento == "" ? $excento = "NULL" : $excento = "" . $excento . "";
            $rango == "" ? $rango = 0 : $rango = "" . $rango . "";
            $tipoCotizante == "" ? $tipoCotizante = 0 : $tipoCotizante = "" . $tipoCotizante . "";
                       
            $txt_nom_dep_res == "" ? $txt_nom_dep_res = "NULL" : $txt_nom_dep_res = "'" . $txt_nom_dep_res . "'";
            $txt_nom_mun_res == "" ? $txt_nom_mun_res = "NULL" : $txt_nom_mun_res = "'" . $txt_nom_mun_res . "'";
            $cmb_cod_plan == "" ? $cmb_cod_plan = "NULL" : $cmb_cod_plan = "" . $cmb_cod_plan . "";

            if ($hdd_id_paciente == '') {
                $hdd_id_paciente = "NULL";
            }
            $sql = "CALL pa_editar_cita(" . $txt_primer_nombre . "," . $txt_segundo_nombre . "," . $txt_primer_apellido . "," . $txt_segundo_apellido . "," .
                    $cmb_tipo_documento . "," . $txt_numero_documento . ",". $txt_numero_telefono . "," . $cmb_convenio . "," . $cmb_lentes . "," .
                    $chk_no_pago . "," . $txt_observacion_cita . "," . $hdd_id_paciente . "," . $id_usuario_edita . "," . $hdd_id_cita . "," . $hdd_lugar_cita . "," .
                    $sexo . "," . $fecha_nacimiento . "," . $pais . "," . $estatus_seguro . "," . $departamento . "," . $municipio . "," . $direccion . "," . $excento . ", " . 
                    $rango . "," . $tipoCotizante ."," . $txt_nom_dep_res ."," . $txt_nom_mun_res ."," . $cmb_cod_plan . ", @id)";
            
            //echo $sql;
            
            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $resultado_out = $arrResultado["@id"];
            
            return $resultado_out;
        } catch (Exception $e) {
            return -2;
        }
    }

    /**
     * Obtener una cita
     */
    public function getCita($id_cita) {
        try {
            $sql = "SELECT C.*, DATE_FORMAT(PC.fecha_nacimiento, '%d/%m/%Y') AS fecha_nac, 
						TC.nombre_tipo_cita, U.nombre_usuario, U.apellido_usuario, DATE_FORMAT(C.fecha_crea, '%d/%m/%Y %h:%i:%s %p') AS fecha_crea_t, 
						UC.nombre_usuario AS nombre_confirma, UC.apellido_usuario AS apellido_confirma, DATE_FORMAT(C.fecha_confirma, '%d/%m/%Y %h:%i:%s %p') AS fecha_confirma_t, 
						EC.nombre_detalle AS estado_cita, lc.nombre_detalle AS nombre_lugar_cita, PC.nombre_1 AS Cnombre1, PC.nombre_2 AS Cnombre2, 
						PC.apellido_1 AS Capellido1, PC.apellido_2 AS Capellido2, PC.sexo AS Csexo, PC.telefono_1 AS Ctelefono, 
						PC.id_pais AS Cpais, PC.status_convenio_paciente AS CstatusConvenio, PC.id_convenio_paciente AS Cconvenio, 
						PC.exento_pamo_paciente AS CconvenioExento, PC.cod_dep AS Cdepartamento, PC.cod_mun AS Cmunicipio, PC.direccion AS Cdireccion, 
						PC.rango_paciente AS Prango, PC.tipo_coti_paciente AS PtipoCoti, PC.tipo_coti_paciente, PC.nom_dep AS nom_dep_aux, SA.sede AS sede_alter, PC.nom_mun AS nom_mun_aux 
					FROM citas C 
					INNER JOIN tipos_citas TC ON C.id_tipo_cita=TC.id_tipo_cita 
					INNER JOIN usuarios U ON C.id_usuario_crea=U.id_usuario 
					LEFT JOIN usuarios UC ON C.id_usuario_confirma=UC.id_usuario 
					INNER JOIN listas_detalle EC ON C.id_estado_cita=EC.id_detalle 
					INNER JOIN listas_detalle lc ON C.id_lugar_cita = lc.id_detalle
					INNER JOIN pacientes PC ON C.id_paciente = PC.id_paciente 
					LEFT JOIN sedes_alternativas SA ON C.id_lugar_cita_alter = SA.id
					WHERE C.id_cita=" . $id_cita;

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getBuscarDocumentoCita($txt_buscar, $id_cita, $fecha, $tipo_accion) {
        try {
            $sql = "SELECT * FROM citas
					WHERE numero_documento='" . $txt_buscar . "'
					AND DATE(fecha_cita)='" . $fecha . "'
					AND id_estado_cita<>15";
            if ($tipo_accion == 2) { //Para EDITAR CITA
                $sql .= " AND id_cita<>" . $id_cita;
            }

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Metodo para cancelar la cita seleccionada
     */
    public function cancelarCita($id_usuario, $id_cita, $id_tipo_cancela, $observacion_cancela) {
        try {
            $sql = "CALL pa_cancelar_cita(" . $id_usuario . ", " . $id_cita . ", " . $id_tipo_cancela . ", '" . $observacion_cancela . "', 1, @id)";

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $resultado_out = $arrResultado["@id"];

            return $resultado_out;
        } catch (Exception $e) {
            return -2;
        }
    }

    /**
     * Buscar personas citas
     */
    public function getBuscarCitasPersonas($txt_buscar, $id_usuario) {
        try {
            $sql = "SELECT * FROM (
						SELECT c.*, u.id_usuario, u.nombre_usuario, u.apellido_usuario, tc.nombre_tipo_cita, ld.id_detalle, ld.id_lista, ld.codigo_detalle, ld.nombre_detalle, ld.orden
						FROM citas c
						INNER JOIN usuarios u ON u.id_usuario=c.id_usuario_prof
						INNER JOIN tipos_citas tc ON tc.id_tipo_cita=c.id_tipo_cita
						INNER JOIN listas_detalle ld ON ld.id_detalle=c.id_estado_cita
						WHERE c.ind_activo=1
						AND c.fecha_cita>=(DATE(NOW()) - INTERVAL 2 MONTH)
						AND (LOWER(CONCAT(c.nombre_1, ' ', IFNULL(c.nombre_2, ''), ' ', c.apellido_1, ' ', IFNULL(c.apellido_2, ''))) LIKE LOWER(CONCAT('%', REPLACE('" . $txt_buscar . "', ' ', '%'), '%'))
						OR c.numero_documento='" . $txt_buscar . "'
						OR c.telefono_contacto LIKE '%" . $txt_buscar . "%'
						OR c.observacion_cita LIKE '%" . $txt_buscar . "%') ";
            if ($id_usuario != '') {
                $sql .= "AND c.id_usuario_prof=" . $id_usuario . " ";
            }
            $sql .= "ORDER BY c.fecha_cita DESC, c.nombre_1, c.nombre_2, apellido_1, apellido_2
					) T
					LIMIT 10";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * FUNCIÓN DESACONSEJADA - POR FAVOR USAR getFechaHoraCita2
     * Función que obtiene las horas de cita iniciales y finales para un usuario y día dados
     * Tipos de tiempos:
     * 1 - Inicio de cita
     * 2 - Inicio de bloque (am o pm)
     * 3 - Fin de cita
     * 4 - Fin de bloque (am o pm)
     * 5 - Hora actual
     */
    public function getFechaHoraCita($id_usuario, $fecha_ini_am, $fecha_fin_am, $fecha_ini_pm, $fecha_fin_pm, $ind_actual) {
        $arr_fechas = array();
        if ($fecha_ini_am != "") {
            array_push($arr_fechas, $fecha_ini_am, $fecha_fin_am);
        }
        if ($fecha_ini_pm != "") {
            array_push($arr_fechas, $fecha_ini_pm, $fecha_fin_pm);
        }
        return $this->getFechaHoraCita2($id_usuario, $arr_fechas, $ind_actual);
    }

    /**
     * Función que obtiene las horas de cita iniciales y finales para un usuario y día dados
     * Tipos de tiempos:
     * 1 - Inicio de cita
     * 2 - Inicio de bloque (am o pm)
     * 3 - Fin de cita
     * 4 - Fin de bloque (am o pm)
     * 5 - Hora actual
     */
    public function getFechaHoraCita2($id_usuario, $arr_fechas, $ind_actual) {
        try {
            $fecha_aux = $arr_fechas[0];
            $sql = "SELECT fecha_cita, MIN(tipo_tiempo) AS tipo_tiempo
					FROM (
					SELECT c.fecha_cita, 1 AS tipo_tiempo
					FROM citas c
					WHERE c.id_usuario_prof=" . $id_usuario . "
					AND c.ind_activo=1
					AND c.fecha_cita BETWEEN DATE('" . $fecha_aux . "') AND DATE_ADD(DATE('" . $fecha_aux . "'), INTERVAL 1 DAY)
					UNION
					SELECT c.fecha_cita + INTERVAL c.tiempo_cita MINUTE, 3
					FROM citas c
					WHERE c.id_usuario_prof=" . $id_usuario . "
					AND c.ind_activo=1
					AND c.fecha_cita BETWEEN DATE('" . $fecha_aux . "') AND DATE_ADD(DATE('" . $fecha_aux . "'), INTERVAL 1 DAY) ";
            for ($i = 0; $i < count($arr_fechas); $i++) {
                $sql .= "UNION
						SELECT STR_TO_DATE('" . $arr_fechas[$i] . "', '%Y-%m-%d %H:%i'), " . ((($i % 2) + 1) * 2) . " ";
            }
            if ($ind_actual == 1) {
                $sql .= "UNION SELECT NOW(), 5";
            }
            $sql .= ") T ";
            if ($ind_actual == 1) {
                $sql .= "WHERE fecha_cita>=NOW() ";
            }
            $sql .= "GROUP BY fecha_cita
					 ORDER BY fecha_cita";
            //echo($sql);

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Obtiene el tiempo en minutos entre dos fechas
     */
    public function getTiempoFechas($fecha_ini, $fecha_fin) {
        try {
            $sql = "SELECT TIMESTAMPDIFF(MINUTE, '" . $fecha_ini . "', '" . $fecha_fin . "') AS tiempo_minutos";
            //echo $sql."<br />";
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getBuscarPaciente($documento) {
        try {
            $sql = "SELECT * FROM pacientes WHERE numero_documento = '$documento'";
            //echo $sql."<br/>";
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getPaciente($id_paciente) {
        try {
            $sql = "SELECT *, DATE_FORMAT(fecha_nacimiento, '%d/%m/%Y') as fecha_nac FROM pacientes WHERE id_paciente = $id_paciente";
            //echo $sql."<br/>";
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getUltimaCitaPersona($documento, $tipo_documento) {
        try {
            $sql = "SELECT * FROM citas
					WHERE numero_documento='" . $documento . "'
					AND id_tipo_documento=" . $tipo_documento . "
					ORDER BY fecha_cita DESC";

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Metodo para verificar que un horario está disponible
     */
    public function marcar_horario_cita($id_usuario, $id_usuario_prof, $fecha_ini, $id_tipo_cita) {
        try {
            if ($fecha_ini == "") {
                $fecha_ini = "NULL";
            } else {
                $fecha_ini = "'" . $fecha_ini . "'";
            }
            $sql = "CALL pa_marcar_horario_cita(" . $id_usuario . ", " . $id_usuario_prof . ", " . $fecha_ini . ", " . $id_tipo_cita . ", @id)";

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $resultado_out = $arrResultado["@id"];

            return $resultado_out;
        } catch (Exception $e) {
            return -2;
        }
    }

    public function get_temporal_cita($id_usuario) {
        try {
            $sql = "SELECT TC.*, DATE_FORMAT(TC.fecha_ini, '%h:%i %p') AS hora_ini,
					DATE_FORMAT(TC.fecha_fin, '%h:%i %p') AS hora_fin, U.nombre_usuario, U.apellido_usuario
					FROM temporal_citas TC
					INNER JOIN usuarios U ON TC.id_usuario=U.id_usuario
					WHERE TC.id_usuario=" . $id_usuario;

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function borrar_temporal_cita($id_usuario) {
        try {
            $sql = "DELETE FROM temporal_citas
					WHERE id_usuario=" . $id_usuario;

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaCitasProgramadasPersona($numero_documento, $id_cita, $tipo_accion) {
        try {
            $sql = "SELECT * FROM citas
					WHERE numero_documento='" . $numero_documento . "'
					AND id_estado_cita NOT IN (15, 159)
					AND fecha_cita>=DATE(NOW()) ";

            if ($tipo_accion == 2) { //Para EDITAR CITA
                $sql .= "AND id_cita<>" . $id_cita . " ";
            }
            $sql .= "ORDER BY fecha_cita";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function confirmarCita($id_cita, $id_usuario, $txt_observacion_cita) {
        try {
			
            $sql = "CALL pa_confirmar_cita(" . $id_cita . ", " . $id_usuario . ", '" . $txt_observacion_cita . "',  1, @id)";
			//echo($sql);

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $resultado_out = $arrResultado["@id"];

            return $resultado_out;
        } catch (Exception $e) {
            return -2;
        }
    }

    public function getListaCitasEnCurso($fecha_hora = "", $id_usuario_prof = 0) {
        try {
            $sql = "SELECT * FROM citas
					WHERE id_estado_cita IN (14, 16, 130) ";
            if ($id_usuario_prof > 0) {
                $sql .= "AND id_usuario_prof=" . $id_usuario_prof . " ";
            }
            if ($fecha_hora == "") {
                $sql .= "AND fecha_cita<=NOW()
						AND (fecha_cita+INTERVAL tiempo_cita MINUTE)>NOW() ";
            } else {
                $sql .= "AND fecha_cita<='" . $fecha_hora . "'
						AND (fecha_cita+INTERVAL tiempo_cita MINUTE)>'" . $fecha_hora . "' ";
            }
            $sql .= "ORDER BY fecha_cita";
            //echo($sql);

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

}
