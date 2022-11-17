<?php
	require_once("DbConexion.php");
	
	class DbRemisiones extends DbConexion {
		
		public function guardarEditarRemision($idHc, $idRemision, $tipoRemision, $desc, $usuario, $indEstado, $idLugar) {
			try {
				$idHc == "" ? $idHc = "NULL" : $idHc = $idHc;
				$idRemision == 0 ? $idRemision = "NULL" : $idRemision = $idRemision;
				$tipoRemision == "" ? $tipoRemision = "NULL" : $tipoRemision = $tipoRemision;
				$desc == "" ? $desc = "NULL" : $desc = "'" . $desc . "'";
				$usuario == "" ? $usuario = "NULL" : $usuario = $usuario;
				$indEstado == "" ? $indEstado = "NULL" : $indEstado = $indEstado;
				$idLugar == "" ? $idLugar = "NULL" : $idLugar = $idLugar;
				
				$sql = "CALL pa_crear_remisiones(".$idHc.", ".$idRemision.", ".$tipoRemision.", ".$desc.", ".$usuario.", ".$indEstado.", ".$idLugar.", @id)";
				
				echo $sql;
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado_out = $arrResultado["@id"];
				
				return $resultado_out;
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getRemisionesByHc($idHc) {
			try {
				$sql = "SELECT *
						FROM hc_remisiones
						WHERE id_hc = $idHc
						ORDER BY id_remision;";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getRemisionesActivasByHc($idHc) {
			try {
				$sql = "SELECT *
						FROM hc_remisiones
						WHERE id_hc = $idHc
						AND ind_estado = 1    
						ORDER BY id_remision;";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getRemisionById($idRemision) {
			try {
				$sql = "SELECT HCR.*, UPPER(TR.nombre_detalle) AS tipoRemisionAux, U.ind_anonimo, U.nombre_usuario, U.apellido_usuario,
						U.num_reg_medico, RG.nombre_detalle AS tipo_num_reg, SD.dir_logo_sede_det, SD.dir_sede_det, SD.tel_sede_det,
						DATE_FORMAT(HCR.fecha_crea, '%d/%m/%Y %h:%i:%s %p') AS fechaCreacionAux, CV.nombre_convenio AS nombreConvenioAux,
						PL.nombre_plan AS nombrePlanAux
						FROM hc_remisiones HCR
						INNER JOIN listas_detalle TR ON HCR.id_tipo_remision=TR.id_detalle
						INNER JOIN usuarios U ON HCR.id_usuario_crea=U.id_usuario
						LEFT JOIN listas_detalle RG ON U.id_tipo_num_reg=RG.id_detalle
						LEFT JOIN sedes_det SD ON SD.id_detalle=HCR.id_lugar_remision
						LEFT JOIN convenios CV ON CV.id_convenio=HCR.id_convenio
						LEFT JOIN planes PL ON PL.id_plan=HCR.id_plan
						WHERE HCR.id_remision=".$idRemision;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
	}
?>
