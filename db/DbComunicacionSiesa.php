<?php
	require_once("DbConexion.php");
	
	class DbComunicacionSiesa extends DbConexion {
		
		public function insertar_comunicacion_siesa($id_si, $id_paciente, $id_tercero, $id_pago, $id_anticipo, $mensaje_error, $id_usuario) {
			try {
				if ($id_paciente == "") {
					$id_paciente = "NULL";
				}
				if ($id_tercero == "") {
					$id_tercero = "NULL";
				}
				if ($id_pago == "") {
					$id_pago = "NULL";
				}
				if ($id_anticipo == "") {
					$id_anticipo = "NULL";
				}
				$mensaje_error = str_replace("'", "", $mensaje_error);
				
				$sql = "CALL pa_crear_log_comunicacion_siesa(".$id_si.", ".$id_paciente.", ".$id_tercero.", ".
						$id_pago.", ".$id_anticipo.", '".$mensaje_error."', ".$id_usuario.", 1, @id)";
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$id_log = $arrResultado["@id"];
				
				return $id_log;
			} catch (Exception $e) {
				return -2;
			}
		}
			
	}
?>
