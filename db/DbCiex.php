<?php

require_once("DbConexion.php");

class DbCiex extends DbConexion {
    /*
     * Esta funcion obtiene todo el listado de procedimientos
     */

    public function buscarDiagnosticos($parametro) {
        try {
            $parametro = str_replace(" ", "%", $parametro);

            $sql = "SELECT *
						FROM ciex
						WHERE (nombre LIKE '%" . $parametro . "%'
						OR codciex LIKE '" . $parametro . "%') ";
            $sql .= "ORDER BY nombre LIMIT 100";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

}
