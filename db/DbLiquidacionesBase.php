<?php
	require_once("DbConexion.php");
	
	class DbLiquidacionesBase extends DbConexion {
		public function getCopagoBaseActual($tipo_cotizante, $rango_valor) {
			try {
				$sql = "SELECT * FROM copagos_base
						WHERE CURDATE() BETWEEN fecha_ini_valor AND IFNULL(fecha_fin_valor, CURDATE())
						AND tipo_cotizante=".$tipo_cotizante."
						AND rango_valor=".$rango_valor;
				//echo($sql);
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getCuotaModeradoraBaseActual($tipo_cotizante, $rango_valor) {
			try {
				$sql = "SELECT * FROM cuotas_moderadoras_base
						WHERE CURDATE() BETWEEN fecha_ini_valor AND IFNULL(fecha_fin_valor, CURDATE())
						AND tipo_cotizante=".$tipo_cotizante."
						AND rango_valor=".$rango_valor;
				//echo($sql);
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	}
?>
