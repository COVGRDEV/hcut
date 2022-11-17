<?php
	require_once("DbConexion.php");
	
	class DbSistemasRevision extends DbConexion {
		public function getListaSistemasRevision($ind_activo) {
			try {
				$sql = "SELECT * FROM sistemas_revision ";
				if ($ind_activo != "") {
					$sql .= "WHERE ind_activo=".$ind_activo." ";
				}
				$sql .= "ORDER BY orden";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	}
?>
