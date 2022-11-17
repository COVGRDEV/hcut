<?php
	require_once("DbConexion.php");
	
	class DbDepMuni extends DbConexion {
		public function getListaMunicipiosAlfabetico() {
			try {
				$sql = "SELECT M.cod_mun_dane, M.nom_mun, D.nom_dep " .
						"FROM municipios M " .
						"INNER JOIN departamentos D ON M.cod_dep=D.cod_dep " .
						"ORDER BY M.nom_mun, D.nom_dep";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getMunicipiosDepartamento($cod_dep) {
			try {
				$sql = "SELECT *
						FROM municipios
						WHERE cod_dep='".$cod_dep."'
						ORDER BY cod_mun";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getMunicipioReg($cod_mun_reg) {
			try {
				$sql = "SELECT * FROM municipios
						WHERE cod_mun_reg='".$cod_mun_reg."'";
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	}
?>
