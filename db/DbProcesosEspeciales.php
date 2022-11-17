<?php
	require_once("DbConexion.php");
	
	class DbProcesosEspeciales extends DbConexion {
		public function getListaAnexosPendientes() {
			try {
				$sql = "SELECT HC.*
						FROM historia_clinica HC
						WHERE HC.id_tipo_reg=18
						AND HC.ruta_arch_adjunto LIKE '%.pdf'
						ORDER BY HC.id_hc";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function guardar_cambio_estado_cita($id_cita, $id_estado_cita, $id_usuario) {
			try {
				$sql = "CALL pa_guardar_cambio_estado_cita(".$id_cita.", ".$id_estado_cita.", ".$id_usuario.", @id)";
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado = $arrResultado["@id"];
				
				return $resultado;
			} catch (Exception $e) {
				return -2;
			}
		}
	}
?>
