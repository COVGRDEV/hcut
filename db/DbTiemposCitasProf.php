<?php

require_once("DbConexion.php");

class DbTiemposCitasProf extends DbConexion {

    public function getTiemposcitasprofe() {
        try {
            $sql = "SELECT u.id_usuario, u.nombre_usuario, u.apellido_usuario, tp.nombre_tipo_cita, tcp.tiempo_tipo_cita, tcp.ind_activo, tcp.id_tipo_cita tcpIdtipocita, tcp.id_usuario tcpIdusuario
                FROM tiempos_citas_prof tcp
                INNER JOIN tipos_citas tp ON tp.id_tipo_cita = tcp.id_tipo_cita
                INNER JOIN usuarios u ON u.id_usuario = tcp.id_usuario";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    //Funcion del datagrid
    public function getTiemposcitasprofe2($id_perfil, $id_usuario, $id_tipo_cita) {
        try {
            $sql = "SELECT DISTINCT u.id_usuario, u.nombre_usuario, u.apellido_usuario, tp.nombre_tipo_cita, " .
                    "tcp.tiempo_tipo_cita, tcp.ind_activo, tcp.id_tipo_cita, tcp.id_usuario, " .
                    "tcp.id_tipo_cita tcpIdtipocita, tcp.id_usuario tcpIdusuario  " .
                    "FROM tiempos_citas_prof tcp " .
                    "INNER JOIN tipos_citas tp ON tp.id_tipo_cita = tcp.id_tipo_cita " .
                    "INNER JOIN usuarios u ON u.id_usuario = tcp.id_usuario " .
                    "INNER JOIN usuarios_perfiles up ON u.id_usuario=up.id_usuario ";
            $sql_aux = "WHERE ";
            if ($id_perfil != "") {
                $sql .= $sql_aux . "up.id_perfil=" . $id_perfil . " ";
                $sql_aux = "AND ";
            }
            if (strlen($id_usuario) > 0) {
                $sql .= $sql_aux . "u.id_usuario=" . $id_usuario . " ";
                $sql_aux = "AND ";
            }
            if (strlen($id_tipo_cita) > 0) {
                $sql .= $sql_aux . "tp.id_tipo_cita=" . $id_tipo_cita . " ";
            }
            $sql .= "ORDER BY u.nombre_usuario, u.apellido_usuario, tp.nombre_tipo_cita";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function UpdateTiemposcitasprofe($id_tipo_cita, $id_usuario, $tiempo_tipo_cita, $ind_activo) {
        try {
            $sql = "UPDATE tiempos_citas_prof 
                    SET tiempo_tipo_cita = $tiempo_tipo_cita, ind_activo = $ind_activo
                    WHERE id_tipo_cita = $id_tipo_cita AND id_usuario = $id_usuario";

            $arrCampos[0] = "@id";
            $this->ejecutarSentencia($sql, $arrCampos);
        } catch (Exception $e) {
            return array();
        }
    }

    public function CreateTiemposcitasprofe($id_usuario, $cmb_tiposcitas2, $cmbTiempo, $id_usuario_crea) {
        try {
            $sql = "CALL pa_crear_tiempo_cita_prof(" . $cmb_tiposcitas2 . ", " . $id_usuario . ", " . $cmbTiempo . ", " . $id_usuario_crea . ", @id)";

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);

            if (isset($arrResultado["@id"])) {
                return $arrResultado["@id"];
            } else {
                return $arrResultado;
            }
        } catch (Exception $e) {
            return -3;
        }
    }

    public function getCitasdisponibilidad() {
        try {
            $sql = "SELECT u.id_usuario, u.nombre_usuario, u.apellido_usuario, p.id_perfil, p.nombre_perfil
                    FROM usuarios u 
                    INNER JOIN usuarios_perfiles up ON up.id_usuario = u.id_usuario 
                    INNER JOIN perfiles p ON p.id_perfil = up.id_perfil 
                    WHERE p.ind_atiende = 1 AND u.ind_activo = 1
                    GROUP BY u.nombre_usuario, u.id_usuario, p.nombre_perfil
					ORDER BY u.nombre_usuario";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    //Funcion que despliega el listado de citas en el formulario admision.php (cuando este no recibe parametros por post)
    public function getTiemposcitasprofeAdmision($usuario) {
        try {

            $sql = "SELECT u.id_usuario, u.nombre_usuario, u.apellido_usuario, tp.nombre_tipo_cita, tcp.tiempo_tipo_cita,
					tcp.ind_activo, tcp.id_tipo_cita, tcp.id_usuario, tcp.id_tipo_cita tcpIdtipocita, tcp.id_usuario tcpIdusuario 
					FROM tiempos_citas_prof tcp
					INNER JOIN tipos_citas tp ON tp.id_tipo_cita = tcp.id_tipo_cita
					INNER JOIN usuarios u ON u.id_usuario = tcp.id_usuario
					WHERE u.id_usuario=".$usuario."
					AND tcp.ind_activo=1
					ORDER BY tp.nombre_tipo_cita";
			//echo $sql;		

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

}

?>
