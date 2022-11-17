<?php

require_once("DbConexion.php");

class DbTiposCitas extends DbConexion {

    public function getTiposcitas() {
        try {
            $sql = "SELECT TC.*, TCH.nombre_tipo_reg
						FROM tipos_citas TC
						LEFT JOIN tipos_registros_hc TCH ON TCH.id_tipo_reg = TC.id_tipo_reg_cx
						ORDER BY TC.ind_activo DESC, TC.nombre_tipo_cita;";
            
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function get_tipo_cita($id_tipo_cita) {
        try {
            $sql = "SELECT * FROM tipos_citas
						WHERE id_tipo_cita=" . $id_tipo_cita;

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    //Funcion que busca por codigo o nombre del tipo de cita
    public function buscarTipoCita($parametro) {
        try {
            $sql = "SELECT TC.*, TCH.nombre_tipo_reg
						FROM tipos_citas TC
						LEFT JOIN tipos_registros_hc TCH ON TCH.id_tipo_reg = TC.id_tipo_reg_cx
						WHERE TC.id_tipo_cita LIKE '%$parametro%' OR TC.nombre_tipo_cita LIKE '%$parametro%'
						ORDER BY TC.id_tipo_cita";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /* Funcion que guarda un nuevo tipo de cita */

    public function guardaTipoCita($id_tipo_cita, $accion, $nombre_tipo_cita, $ind_activo, $ind_preconsulta, $ind_examenes, $ind_signos_vitales, $ind_preqx, $ind_despacho, $id_tipo_reg_cx, $id_usuario) {
        try {
            if ($id_tipo_cita == "") {
                $id_tipo_cita = "NULL";
            }
            if ($id_tipo_reg_cx == "") {
                $id_tipo_reg_cx = "NULL";
            }

            $sql = "CALL pa_crear_tipo_cita(" . $id_tipo_cita . ", " . $accion . ", '" . $nombre_tipo_cita . "', " . $ind_activo . ", " . $ind_preconsulta . ", " .
                    $ind_examenes . ", " . $ind_signos_vitales . ", " . $ind_preqx . ", " . $ind_despacho . ", " . $id_tipo_reg_cx . ", " . $id_usuario . ", @id)";
            //echo($sql);

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $resultado_out = $arrResultado["@id"];

            return $resultado_out;
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaTiposCitas($ind_activo) {
        try {
            $sql = "SELECT TC.*
						FROM tipos_citas TC ";
            if ($ind_activo != "") {
                $sql .= "WHERE ind_activo=" . $ind_activo . " ";
            }
            $sql .= "ORDER BY TC.nombre_tipo_cita";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

}

?>
