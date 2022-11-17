<?php
	require_once("DbConexion.php");
	
	class DbConvenios extends DbConexion {
		
		//Muestra el listado completo de convenios
		public function getConvenios() {
			try {
				$sql = "SELECT C.*, IFNULL(P.cantidad, 0) AS cantidad
						FROM convenios C
						LEFT JOIN (
							SELECT id_convenio, COUNT(*) AS cantidad
							FROM planes
							GROUP BY id_convenio
						) P ON C.id_convenio=P.id_convenio
						ORDER BY CASE C.id_convenio WHEN 1 THEN 1 ELSE 2 END, C.nombre_convenio";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		public function getListaConveniosActivosAutorizaciones() {
			try {
				$sql = "SELECT C.id_convenio, SUBSTRING(C.nombre_convenio, 1, 22) AS nombre_convenio, C.ind_activo,                        C.id_usuario_crea, C.fecha_crea,
                       C.id_usuario_mod, C.fecha_mod, C.ind_eco, C.cod_administradora, C.ind_num_aut,                        C.id_tipo_documento, C.numero_documento FROM convenios C 
                       WHERE C.ind_activo=1 
                       ORDER BY CASE C.id_convenio WHEN 1 THEN 1 ELSE 2 END, C.nombre_convenio";
				//echo($sql);
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaConveniosActivos() {
			try {
				$sql = "SELECT C.*
						FROM convenios C
						WHERE C.ind_activo=1
						ORDER BY CASE C.id_convenio WHEN 1 THEN 1 ELSE 2 END, C.nombre_convenio";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Muestra el listado de convenios segun el parametro
		public function getConveniosBuscar($parametro) {
			$parametro = str_replace(" ", "%", $parametro);
			try {
				$sql = "SELECT C.*, IFNULL(P.cantidad, 0) AS cantidad
						FROM convenios C
						LEFT JOIN (
							SELECT id_convenio, COUNT(*) AS cantidad
							FROM planes
							GROUP BY id_convenio
						) P ON C.id_convenio=P.id_convenio
						WHERE C.id_convenio LIKE '%$parametro%' OR C.nombre_convenio LIKE '%$parametro%'
						ORDER BY C.nombre_convenio";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Guarda Nuevo convenio
		public function guardarModificaConvenio($nombre, $indActivo, $idUsuarioCrea, $idAdmision, $tipoAccion, $indEco,
				$txtCodAdministradora, $indNumAut, $indNumAutObl, $indNumCarnet, $indNumCarnetObl, $idTipoDocumento, $numeroDocumento,
				$fec_ini_fac, $fec_fin_fac, $contratacion, $num_contrato) {
			try {
				if ($idTipoDocumento == "") {
					$idTipoDocumento = "NULL";
				}
				if ($numeroDocumento == "") {
					$numeroDocumento = "NULL";
				} else {
					$numeroDocumento = "'".$numeroDocumento."'";
				}
				
				if ($fec_ini_fac == "") {
					$fec_ini_fac = "NULL";
				} else {
					$fec_ini_fac = "'".$fec_ini_fac."'";
				}
				if ($fec_fin_fac == "") {
					$fec_fin_fac = "NULL";
				} else {
					$fec_fin_fac = "'".$fec_fin_fac."'";
				}
				if ($contratacion == "") {
					$contratacion = "NULL";
				} else {
					$contratacion = "'".$contratacion."'";
				}
				if ($num_contrato == "") {
					$num_contrato = "NULL";
				} else {
					$num_contrato = "'".$num_contrato."'";
				}
				
				$sql = "CALL pa_crear_modificar_convenio('".$nombre."', ".$indActivo.", ".$idUsuarioCrea.", ".$idAdmision.", ".$tipoAccion.", " .
						$indEco.", '".$txtCodAdministradora."', ".$indNumAut.", ".$indNumAutObl.", ".$indNumCarnet.", ".$indNumCarnetObl.", " .
						$idTipoDocumento.", ".$numeroDocumento.",  ".$fec_ini_fac.",  ".$fec_fin_fac.", ".$contratacion.", ".$num_contrato.", @id)";
			
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado = $arrResultado["@id"];
				
				return $resultado;
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Muestra el listado completo de convenios
		public function getConvenio($id_convenio) {
			try {
				$sql = "SELECT C.*, TD.codigo_detalle AS cod_tipo_documento, TD.nombre_detalle AS tipo_documento, TD.text_adicional_detalle AS codigo_doc_siesa,
						TC.nombre_detalle AS contratacion_nombre
						FROM convenios C
						LEFT JOIN listas_detalle TD ON C.id_tipo_documento=TD.id_detalle
						LEFT JOIN listas_detalle TC ON C.contratacion = TC.id_detalle
						WHERE C.id_convenio=".$id_convenio;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}

		
		
		//Lista de Planes que tiene un convenio
		public function getListaPlanes($id_convenio) {
			try {
				$sql = "SELECT P.*, TU.nombre_detalle AS tipo_usuario, TM.nombre_detalle AS tipo_medicamento
						FROM planes P
						LEFT JOIN listas_detalle TU ON P.id_tipo_usuario=TU.id_detalle
						LEFT JOIN listas_detalle TM ON TM.id_lista=53 AND P.cod_tipo_medicamento=TM.codigo_detalle
						WHERE P.id_convenio=".$id_convenio."
						ORDER BY P.nombre_plan";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Lista de Planes que tiene un convenio
		public function getPlanesEcopetrol() {
			try {
				$sql = "SELECT *
						FROM convenios 
						WHERE ind_eco=1
						ORDER BY nombre_convenio";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Guarda | Modifica los Planes
		public function crearModificarPlan($txtNombrePlan, $tipoPago, $indActivoPlan, $idConvenio, $idUsuarioCrea, $tipoAccion, $idPlan, $tipoUsuario,
				$codTipoMedicamento = "", $indISS2001 = "0", $iss2001porc = "", $idLiqQx = "", $indCalcCC = "0", $indDespachoMedicamentos = "0",
				$indDescCC = "1", $cmb_cobertura="") {
			try {
				if ($codTipoMedicamento != "") {
					$codTipoMedicamento = "'".$codTipoMedicamento."'";
				} else {
					$codTipoMedicamento = "NULL";
				}
				if ($indISS2001 == "") {
					$indISS2001 = "0";
				}
				if ($iss2001porc == "") {
					$iss2001porc = "NULL";
				}
				if ($idLiqQx == "") {
					$idLiqQx = "NULL";
				}
				if ($indCalcCC == "") {
					$indCalcCC = "0";
				}
				if ($indDescCC == "") {
					$indDescCC = "1";
				}
				if ($cmb_cobertura != "") {
					$cmb_cobertura = "'".$cmb_cobertura."'";
				} else {
					$cmb_cobertura = "NULL";
				}
				
				$idPlan == 0 || $idPlan == "" ? $idPlan = "NULL" : $idPlan = $idPlan;
				
				$sql = "CALL pa_crear_planes('".$txtNombrePlan."', ".$tipoPago.", ".$indActivoPlan.", ".$idConvenio.", ".$idUsuarioCrea.", ".$tipoAccion.", ".
						$idPlan.", ".$tipoUsuario.", ".$codTipoMedicamento.", ".$indISS2001.", ".$iss2001porc.", ".$idLiqQx.", ".$indCalcCC.", ".$indDescCC.", ".
						$indDespachoMedicamentos.", ".$cmb_cobertura.", @id)";
				
				//echo $sql;
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$id_usuario_creado = $arrResultado["@id"];
				
				return $id_usuario_creado;
			} catch (Exception $e) {
				return array();
			}
		}						
	}
?>
