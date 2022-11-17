<?php
	require_once("DbConexion.php");
	
	class DbProcedimientosCotizaciones extends DbConexion {
		public function getListaProcedimientosCotizaciones($ind_activo) {
			try {
				$sql = "SELECT * FROM procedimientos_cotizaciones ";
				if ($ind_activo != "") {
					$sql .= "WHERE ind_activo=".$ind_activo." ";
				}
				$sql .= "ORDER BY nombre_proc_cotiz";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getProcedimientoCotizacion($id_proc_cotiz) {
			try {
				$sql = "SELECT * FROM procedimientos_cotizaciones
						WHERE id_proc_cotiz=".$id_proc_cotiz;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaProcedimientosCotizacionesNombre($nombre_proc_cotiz) {
			try {
				$nombre_proc_cotiz = str_replace(" ", "%", trim($nombre_proc_cotiz));
				$sql = "SELECT * FROM procedimientos_cotizaciones
						WHERE nombre_proc_cotiz LIKE '%".$nombre_proc_cotiz."%'
						ORDER BY nombre_proc_cotiz";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function actualizarProcedimientoCotizacion($id_proc_cotiz, $nombre_proc_cotiz, $ind_activo, $id_usuario) {
			try {
				$sql = "CALL pa_editar_procedimiento_cotizacion(".$id_proc_cotiz.", '".$nombre_proc_cotiz."', ".$ind_activo.", ".$id_usuario.", @id)";
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado = $arrResultado["@id"];
				
				return $resultado;
			} catch (Exception $e) {
				return "-2";
			}
		}
		
		public function crearProcedimientoCotizacion($nombre_proc_cotiz, $id_usuario) {
			try {
				$sql = "CALL pa_crear_procedimiento_cotizacion('".$nombre_proc_cotiz."', ".$id_usuario.", @id)";
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado = $arrResultado["@id"];
				
				return $resultado;
			} catch (Exception $e) {
				return "-2";
			}
		}
	}
?>
