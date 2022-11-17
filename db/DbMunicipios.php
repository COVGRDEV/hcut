<?php
	require_once("DbConexion.php");
	
	class DbMunicipios extends DbConexion {
		public function getMunicipios() {
			try {
				$sql = "SELECT * FROM municipios
                                        order by nom_mun";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	}
?>
