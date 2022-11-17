<?php
require_once("../db/DbConexion.php");
//require_once("DbConexion.php");

class DbLlaves extends DbConexion {
	public function getLlave() {
		try {
			$sql = "SELECT * FROM llaves
					LIMIT 1";
			
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}
}
?>
