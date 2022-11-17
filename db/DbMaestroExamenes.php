<?php
	require_once("DbConexion.php");
	
	class DbMaestroExamenes extends DbConexion {
		/*
		 * Esta funcion obtiene listado de servicios segun la variable $parametro
		 */
		public function get_lista_examenes($ind_activo) {
			try {
				$sql = "SELECT * FROM maestro_examenes ";
				if ($ind_activo == 1 || $ind_activo == 0) {
					$sql .= "WHERE ind_activo=" . $ind_activo . " ";
				}
				$sql .= "ORDER BY nombre_examen";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Funcion para buscar examenes
		public function get_buscar_examenes($txt_buscar) {
			try {
				$txt_buscar = str_replace(" ", "%", $txt_buscar);
				$sql = "SELECT * FROM maestro_examenes
						WHERE id_examen LIKE '%".$txt_buscar."%'
						OR nombre_examen LIKE '%".$txt_buscar."%'
						ORDER BY id_examen, nombre_examen";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Funcion para buscar un exámen por código del exámen
		public function get_buscar_examen($id_examen) {
			try {
				$sql = "SELECT me.*, mp.*
						FROM maestro_examenes me
						INNER JOIN maestro_procedimientos mp ON mp.cod_procedimiento = me.cod_procedimiento
						WHERE me.id_examen=".$id_examen."
						ORDER BY me.id_examen";
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/*Crea el examen*/
		public function crear_examen($nombre_examen, $cod_procedimiento, $ind_activo, $id_usuario, $id_examen_compl = "", $observacion_predef = "") {
			try {
				if ($id_examen_compl == "") {
					$id_examen_compl = "NULL";
				}
				if ($observacion_predef != "") {
					$observacion_predef = "'".$observacion_predef."'";
				} else {
					$observacion_predef = "NULL";
				}
				$sql = "CALL pa_crear_examen('".$nombre_examen."', '".$cod_procedimiento."', ".$id_examen_compl.", ".
						$observacion_predef.", ".$ind_activo.", ".$id_usuario.", @id)";
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado_out = $arrResultado["@id"];
				
				return $resultado_out;
			} catch (Exception $e) {
				return array();
			}
		}
		
		/*Edita el examen*/
		public function editar_examen($cod_examen, $nombre_examen, $cod_procedimiento, $ind_activo, $id_usuario, $id_examen_compl = "", $observacion_predef = "") {
			try {
				if ($id_examen_compl == "") {
					$id_examen_compl = "NULL";
				}
				if ($observacion_predef != "") {
					$observacion_predef = "'".$observacion_predef."'";
				} else {
					$observacion_predef = "NULL";
				}
				$sql = "CALL pa_editar_examen(".$cod_examen.", '".$nombre_examen."', '".$cod_procedimiento."', ".
						$id_examen_compl.", ".$observacion_predef.", ".$ind_activo.", ".$id_usuario.", @id)";
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado_out = $arrResultado["@id"];
				
				return $resultado_out;
			} catch (Exception $e) {
				return array();
			}
		}
	}
?>
