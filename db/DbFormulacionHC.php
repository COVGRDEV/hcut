<?php
	require_once("DbConexion.php");
	
	class DbFormulacionHC extends DbConexion {
		public function getListaFormulacionHC($id_hc) {
			try {
				$sql = "SELECT F.*
						FROM formulacion_hc F
						WHERE F.id_hc=".$id_hc."
						ORDER BY F.orden";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaFormulacionHCAnterior($id_hc) {
			try {
				$sql = "SELECT F.*
						FROM formulacion_hc F
						WHERE F.id_hc=(
							SELECT MAX(H2.id_hc)
							FROM historia_clinica HC
							INNER JOIN historia_clinica H2 ON HC.id_paciente=H2.id_paciente
							INNER JOIN formulacion_hc F ON H2.id_hc=F.id_hc
							WHERE HC.id_hc=".$id_hc."
							AND HC.id_hc>H2.id_hc
						)
						ORDER BY F.orden";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function crearEditarFormulacionesHC($id_hc, $lista_formulaciones, $id_usuario) {
			try {
				$sql = "DELETE FROM temporal_formulacion_hc
						WHERE id_hc=".$id_hc."
						AND id_usuario=".$id_usuario;
				$arrCampos_delete[0] = "@id";
				
				$j = 1;
				foreach ($lista_formulaciones as $formulacion_aux) {
					$cod_medicamento_aux = $formulacion_aux["cod_medicamento"];
					$nombre_medicamento_aux = $formulacion_aux["nombre_medicamento"];
					$cod_tipo_medicamento_aux = $formulacion_aux["cod_tipo_medicamento"];
					$presentacion_aux = $formulacion_aux["presentacion"];
					$cantidad_aux = $formulacion_aux["cantidad"];
					$dosificacion_aux = $formulacion_aux["dosificacion"];
					$unidades_aux = $formulacion_aux["unidades"];
					$duracion_aux = $formulacion_aux["duracion"];
					
					if ($cod_medicamento_aux == "") {
						$cod_medicamento_aux = "NULL";
					}
					if ($nombre_medicamento_aux != "") {
						$nombre_medicamento_aux = "'".$nombre_medicamento_aux."'";
					} else {
						$nombre_medicamento_aux = "NULL";
					}
					if ($cod_tipo_medicamento_aux == "") {
						$cod_tipo_medicamento_aux = "NULL";
					}
					if ($presentacion_aux != "") {
						$presentacion_aux = "'".$presentacion_aux."'";
					} else {
						$presentacion_aux = "NULL";
					}
					if ($dosificacion_aux != "") {
						$dosificacion_aux = "'".$dosificacion_aux."'";
					} else {
						$dosificacion_aux = "NULL";
					}
					if ($unidades_aux != "") {
						$unidades_aux = "'".$unidades_aux."'";
					} else {
						$unidades_aux = "NULL";
					}
					if ($duracion_aux != "") {
						$duracion_aux = "'".$duracion_aux."'";
					} else {
						$duracion_aux = "NULL";
					}
					
					$sql = "CALL pa_crear_temporal_formulacion_hc(".$id_hc.", ".$id_usuario.", ".$cod_medicamento_aux.", ".$nombre_medicamento_aux.", ".
							$cod_tipo_medicamento_aux.", ".$presentacion_aux.", ".$cantidad_aux.", ".$dosificacion_aux.", ".$unidades_aux.", ".$duracion_aux.", ".$j.", @id)";
					
					$arrCampos[0] = "@id";
					$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
					$resultado = $arrResultado["@id"];
					
					if ($resultado <= 0) {
						return -3;
					}
					$j++;
				}
				
				$sql = "CALL pa_editar_formulaciones_hc(".$id_hc.", ".$id_usuario.", @id)";
				
				//echo($sql);
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
