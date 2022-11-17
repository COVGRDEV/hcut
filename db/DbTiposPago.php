<?php
require_once("DbConexion.php");

class DbTiposPago extends DbConexion {
	//Lista completa de todos los tipos de pago
	public function getListaTiposPago() {
		try {
			$sql = "SELECT * FROM tipos_pago
					ORDER BY id";
			
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	public function getListaTiposPagoAct($ind_activo, $id_tipo_concepto = "", $ind_boleta = true, $ind_np = true) {
		try {
			$sql = "SELECT * FROM tipos_pago
					WHERE ind_activo=".$ind_activo." ";
			if ($id_tipo_concepto != "") {
				$sql .= "AND id_tipo_concepto=".$id_tipo_concepto." ";
			}
			if (!$ind_boleta) {
				$sql .= "AND id<>0 ";
			}
			if (!$ind_np) {
				$sql .= "AND id<>99 ";
			}
			$sql .= "ORDER BY id";
			//echo($sql);
			
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	/*Devuelve todos los tiposde pago con NP de primero*/
	public function getListaTiposPagoNP() {
		try {
			$sql = "SELECT * FROM tipos_pago
					ORDER BY CASE WHEN id=99 THEN 0 ELSE 1 END, id";
			
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	//obtiene un tipo de pago especifico
	public function getTipoPago($idTipoPago) {
		try {
			$sql = "SELECT * FROM tipos_pago
					WHERE id=".$idTipoPago;
			
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	/*Devuelve todos los tiposde pago con NP de primero*/
	public function getListaTiposPagoConcepto() {
		try {
			$sql = "SELECT TP.*, TC.nombre_detalle AS tipo_concepto
					FROM tipos_pago TP
					INNER JOIN listas_detalle TC ON TP.id_tipo_concepto=TC.id_detalle
					ORDER BY TC.orden, CASE WHEN TP.id=99 THEN 0 ELSE 1 END, TP.id";
			
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}
}
?>
