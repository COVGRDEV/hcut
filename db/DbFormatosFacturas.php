<?php
	require_once("DbConexion.php");
	
	class DbFormatosFacturas extends DbConexion {
		public function getListaFormatosFacturas($id_lugar_cita, $id_prestador, $id_convenio) {
			try {
				$sql = "SELECT FF.*
						FROM formatos_facturas FF
						WHERE (FF.id_lugar_cita=".$id_lugar_cita." OR FF.id_lugar_cita IS NULL)
						AND (FF.id_prestador=".$id_prestador." OR FF.id_prestador IS NULL)
						AND ((EXISTS (
							SELECT * FROM formatos_facturas_det FD
							WHERE FD.id_formato_factura=FF.id_formato_factura
							AND FD.id_convenio=".$id_convenio."
							AND FD.ind_activo=1
						)
						AND FF.ind_tipo_inclusion=1)
						OR (NOT EXISTS (
							SELECT * FROM formatos_facturas_det FD
							WHERE FD.id_formato_factura=FF.id_formato_factura
							AND FD.id_convenio=".$id_convenio."
							AND FD.ind_activo=1
						)
						AND FF.ind_tipo_inclusion=0)
						OR NOT EXISTS (
							SELECT * FROM formatos_facturas_det FD
							WHERE FD.id_formato_factura=FF.id_formato_factura
							AND FD.ind_activo=1
						)
						)
						AND FF.ind_activo=1
						ORDER BY FF.id_lugar_cita DESC, FF.id_prestador DESC, FF.tipo_copago";
				
				//echo($sql);
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	}
?>
