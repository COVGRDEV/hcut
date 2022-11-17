<?php
	require_once("DbConexion.php");
	
	class DbImportarHc extends DbConexion {
		public function importar_hc($id_paciente, $tipo_identificacion, $identificacion, $sexo, $nombre_1, $nombre_2, $apellido_1, $apellido_2, $id_usuario_crea) {
			try {
				$sql = "CALL pa_crear_editar_importa_hc(".$id_paciente.", ".$tipo_identificacion.", '".$identificacion."', '".$sexo."', '".
						$nombre_1."', '".$nombre_2."', '".$apellido_1."', '".$apellido_2."', ".$id_usuario_crea.", @id)";
				
				//echo($sql);
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado = $arrResultado["@id"];
				
				return $resultado;
			} catch (Exception $e) {
				return -1;
			}
		}
		
		public function editar_importar_hc($id_hc, $nombre_archivo, $id_usuario) {
			try {
				$sql = "CALL pa_editar_importa_hc(".$id_hc.", '".$nombre_archivo."', ".$id_usuario.", @id)";
				
				//echo($sql);
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado = $arrResultado["@id"];
				
				return $resultado;
			} catch (Exception $e) {
				return -1;
			}
		}
		
		public function getListaHCAntiguasMover() {
			try {
				$sql = "SELECT P.id_paciente, F.*
						FROM consultas_hc_fisica F
						INNER JOIN historia_clinica HC ON F.id_hc=HC.id_hc
						INNER JOIN pacientes P ON HC.id_paciente=P.id_paciente
						WHERE HC.ind_borrado=0
						AND IFNULL(HC.ruta_arch_adjunto, '')=''";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
   	}
?>
