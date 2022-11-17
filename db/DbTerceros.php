<?php
	require_once("DbConexion.php");
	
	class DbTerceros extends DbConexion {
		public function getListasTercerosParametro($parametro, $ind_activo) {
			try {
				$parametro = trim(str_replace(" ", "%", $parametro));
				
				$sql = "SELECT T.*, TD.codigo_detalle AS cod_tipo_documento, TD.nombre_detalle AS tipo_documento
						FROM terceros T
						LEFT JOIN listas_detalle TD ON T.id_tipo_documento=TD.id_detalle
						WHERE (T.numero_documento LIKE '".$parametro."%'
						OR T.nombre_tercero LIKE '%".$parametro."%') ";
				if ($ind_activo != "") {
					$sql .= "AND T.ind_activo=".$ind_activo." ";
				}
				$sql .= "ORDER BY T.nombre_tercero";
				//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getTercero($id_tercero) {
			try {
				$sql = "SELECT T.*, TD.codigo_detalle AS cod_tipo_documento, TD.nombre_detalle AS tipo_documento, TD.text_adicional_detalle AS codigo_doc_siesa
						FROM terceros T
						LEFT JOIN listas_detalle TD ON T.id_tipo_documento=TD.id_detalle
						WHERE T.id_tercero=".$id_tercero;
				//echo($sql);
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function crear_tercero($id_tipo_documento, $numero_documento, $numero_verificacion, $nombre_tercero,
				$nombre_1, $nombre_2, $apellido_1, $apellido_2, $email, $id_paciente, $ind_activo, $id_usuario,$obligaciones, $det_tributarios, $ind_iva) {
			try {
				if ($numero_verificacion != "") {
					$numero_verificacion = "'".$numero_verificacion."'";
				} else {
					$numero_verificacion = "NULL";
				}
				if ($id_paciente == "") {
					$id_paciente = "NULL";
				}
				if ($id_tipo_documento != "146") {
					$nombre_tercero = $nombre_1.($nombre_2 != "" ? " ".$nombre_2 : "")." ".$apellido_1.($apellido_2 != "" ? " ".$apellido_2 : "");
				}
				if ($nombre_1 != "") {
					$nombre_1 = "'".$nombre_1."'";
				} else {
					$nombre_1 = "NULL";
				}
				if ($nombre_2 != "") {
					$nombre_2 = "'".$nombre_2."'";
				} else {
					$nombre_2 = "NULL";
				}
				if ($apellido_1 != "") {
					$apellido_1 = "'".$apellido_1."'";
				} else {
					$apellido_1 = "NULL";
				}
				if ($apellido_2 != "") {
					$apellido_2 = "'".$apellido_2."'";
				} else {
					$apellido_2 = "NULL";
				}
				if ($obligaciones != "") {
					$obligaciones = "'".$obligaciones."'";
				} else {
					$obligaciones = "NULL";
				}
				if ($det_tributarios != "") {
					$det_tributarios = "'".$det_tributarios."'";
				} else {
					$det_tributarios = "NULL";
				}
				if ($ind_iva != "") {
					$ind_iva = "'".$ind_iva."'";
				} else {
					$ind_iva = "NULL";
				}				
				
				$sql = "CALL pa_crear_tercero(".$id_tipo_documento.", '".$numero_documento."', ".$numero_verificacion.", '".$nombre_tercero."', ".
						$nombre_1.", ".$nombre_2.", ".$apellido_1.", ".$apellido_2.", '".$email."', ".$id_paciente.", ".$ind_activo.", ".$id_usuario.", ".$obligaciones.", ".$det_tributarios.",".$ind_iva.", @id)";
				
				//echo($sql);
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado = $arrResultado["@id"];
				
				return $resultado;
			} catch (Exception $e) {
				return -2;
			}
		}
		
		public function editar_tercero($id_tercero, $id_tipo_documento, $numero_documento, $numero_verificacion, $nombre_tercero,$nombre_1, $nombre_2, $apellido_1, $apellido_2, $email, $id_paciente, $ind_activo, $id_usuario, $obligaciones, $det_tributarios, $ind_iva) {
			try {
				if ($numero_verificacion != "") {
					$numero_verificacion = "'".$numero_verificacion."'";
				} else {
					$numero_verificacion = "NULL";
				}
				if ($id_paciente == "") {
					$id_paciente = "NULL";
				}
				if ($id_tipo_documento != "146") {
					$nombre_tercero = $nombre_1.($nombre_2 != "" ? " ".$nombre_2 : "")." ".$apellido_1.($apellido_2 != "" ? " ".$apellido_2 : "");
				}
				if ($nombre_1 != "") {
					$nombre_1 = "'".$nombre_1."'";
				} else {
					$nombre_1 = "NULL";
				}
				if ($nombre_2 != "") {
					$nombre_2 = "'".$nombre_2."'";
				} else {
					$nombre_2 = "NULL";
				}
				if ($apellido_1 != "") {
					$apellido_1 = "'".$apellido_1."'";
				} else {
					$apellido_1 = "NULL";
				}
				if ($apellido_2 != "") {
					$apellido_2 = "'".$apellido_2."'";
				} else {
					$apellido_2 = "NULL";
				}
				
				if ($obligaciones != "") {
					$obligaciones = "'".$obligaciones."'";
				} else {
					$obligaciones = "NULL";
				}
				if ($det_tributarios != "") {
					$det_tributarios = "'".$det_tributarios."'";
				} else {
					$det_tributarios = "NULL";
				}
				if ($ind_iva != "") {
					$ind_iva = "'".$ind_iva."'";
				} else {
					$ind_iva = "NULL";
				}
				
				
				$sql = "CALL pa_editar_tercero(".$id_tercero.", ".$id_tipo_documento.", '".$numero_documento."', ".$numero_verificacion.", '".$nombre_tercero."', ".$nombre_1.", ".$nombre_2.", ".$apellido_1.", ".$apellido_2.", '".$email."', ".$id_paciente.", ".$ind_activo.", ".$id_usuario.", ".$obligaciones.", ".$det_tributarios.", ".$ind_iva.", @id)";
				
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
