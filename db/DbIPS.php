<?php

require_once("DbConexion.php");

class DbIPS extends DbConexion {

    public function getIPS($idConvenio, $indActivo = 1) {
        try {
            $sql = "SELECT *
                    FROM IPS
                    WHERE id_convenio = $idConvenio AND ind_activo_ips = $indActivo
                    ORDER BY nom_ips";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

}

?>