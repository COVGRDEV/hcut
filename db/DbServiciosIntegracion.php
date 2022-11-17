<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("DbConexion.php");

/**
 * Description of DbServiciosIntegracion
 *
 * @author Sistemas2
 */
class DbServiciosIntegracion extends DbConexion {

    public function getServicioIntegracion($idSi) {
        try {
            $sql = "SELECT SI.*, SIM.nom_sim, SIM.parametros_sim, SIM.idCompania_si, SIM.strUsuario_si, SIM.strClave_si, SW.nom_wsdl, SW.url_wsdl, SW.path_wsdl
                    FROM servicios_integracion SI
                    INNER JOIN servicios_integracion_metodos SIM ON SI.id_sim = SIM.id_sim
                    INNER JOIN servicios_integracion_wsdl SW ON SIM.id_wsdl = SW.id_wsdl
                    WHERE SI.id_si = $idSi;";

            $arrResultado = $this->getUnDato($sql);
            //echo $sql;
            return $arrResultado;
        } catch (Exception $e) {
            return array();
        }
    }

    public function getServicioIntegracionComponentes($idSi) {
        try {
            $sql = "SELECT * FROM servicios_integracion_componentes
                    WHERE id_si = $idSi
                    ORDER BY orden_sic;";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getServicioIntegracionComponentesDet($idSic) {
        try {
            $sql = "SELECT SICD.campo_sid
                    FROM servicios_integracion_componentes_det SICD
                    INNER JOIN servicios_integracion_componentes SIC ON SICD.id_sic = SIC.id_sic
                    WHERE SIC.id_sic = $idSic";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    public function getServicioIntegracionConsulta($idConsulta) {
        try {
            $sql = "SELECT * FROM
                    servicios_integracion_consultas SIC
                    INNER JOIN servicios_integracion SI ON SIC.id_si = SI.id_si
                    INNER JOIN servicios_integracion_metodos SIM ON SI.id_sim = SIM.id_sim
                    INNER JOIN servicios_integracion_wsdl SIW ON SIM.id_wsdl = SIW.id_wsdl
                    WHERE SIC.id_sico = $idConsulta;";

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

}
