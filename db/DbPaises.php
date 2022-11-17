<?php
	require_once("DbConexion.php");
	
	class DbPaises extends DbConexion {
		public function getPaises() {
			try {
				$sql = "SELECT * FROM paises
						ORDER BY id_pais";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	}
?>
