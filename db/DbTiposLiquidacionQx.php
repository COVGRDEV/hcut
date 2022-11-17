<?php
	require_once("DbConexion.php");
	
	class DbTiposLiquidacionQx extends DbConexion {
		public function getListasTiposLiquidacionQx($ind_activo) {
			try {
				$sql = "SELECT * FROM tipos_liquidacion_qx ";
				if ($ind_activo != "") {
					$sql .= "WHERE ind_activo=".$ind_activo." ";
				}
				$sql .= "ORDER BY nombre_liq_qx";
				//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListasTiposLiquidacionQxDet($id_liq_qx, $ind_activo) {
			try {
				$sql = "SELECT C.codigo_detalle AS cod_componente, QD.*
						FROM tipos_liquidacion_qx_det QD
						INNER JOIN listas_detalle C ON QD.id_componente=C.id_detalle
						WHERE QD.id_liq_qx=".$id_liq_qx." ";
				if ($ind_activo != "") {
					$sql .= "AND QD.ind_activo=".$ind_activo." ";
				}
				$sql .= "ORDER BY C.orden, QD.orden_proc_qx, QD.ind_especialidad, QD.ind_via";
				//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	}
?>
