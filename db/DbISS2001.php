<?php
	require_once("DbConexion.php");
	
	class DbISS2001 extends DbConexion {
		
		public function getISS2001SP($refISS201SP) {
			try {
				$sql = "SELECT * FROM iss_2001_s_p
						WHERE ref_iss_2001_s_p='".$refISS201SP."'";
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaISS2001DS() {
			try {
				$sql = "SELECT * FROM iss_2001_d_s
						ORDER BY uvr2_iss_2001_d_s";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaISS2001M() {
			try {
				$sql = "SELECT * FROM iss_2001_m
						ORDER BY uvr2_iss_2001_m";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
	}
?>
