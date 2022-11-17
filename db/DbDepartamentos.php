<?php
	require_once("DbConexion.php");
	
	class DbDepartamentos extends DbConexion {
		public function getDepartamentos() {
			try {
				$sql = "SELECT * FROM departamentos
						ORDER BY nom_dep";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	}
?>
