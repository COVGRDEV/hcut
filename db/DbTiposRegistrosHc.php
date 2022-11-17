<?php
	require_once("DbConexion.php");
	
	class DbTiposRegistrosHc extends DbConexion {
		public function getTiposRegistrosHc() {
			try {
				$sql = "SELECT *
						FROM tipos_registros_hc
						ORDER BY nombre_tipo_reg";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getTipoRegistroHc($id_tipo_reg) {
			try {
				$sql = "SELECT * FROM tipos_registros_hc
						WHERE id_tipo_reg=".$id_tipo_reg;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getTiposRegistrosHcClaseReg($idClase) {
			try {
				$sql = "SELECT *
						FROM tipos_registros_hc
						WHERE id_clase_reg = $idClase
						ORDER BY nombre_tipo_reg";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getTipoRegistroHcCitaOrden($idTipoCita, $orden) {
			try {
				$sql = "SELECT TR.*
						FROM tipos_citas_det CD
						INNER JOIN tipos_registros_hc TR ON CD.id_tipo_reg=TR.id_tipo_reg
						WHERE CD.id_tipo_cita=".$idTipoCita."
						AND CD.orden=".$orden;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getTipoRegistroHcCitaUltimo($idTipoCita) {
			try {
				$sql = "SELECT * FROM (
							SELECT TR.*
							FROM tipos_citas_det CD
							INNER JOIN tipos_registros_hc TR ON CD.id_tipo_reg=TR.id_tipo_reg
							WHERE CD.id_tipo_cita=".$idTipoCita."
							AND CD.ind_obligatorio=1
							ORDER BY CD.orden DESC
						) T
						LIMIT 1";
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getTipoRegistroHcCitaMenu($idTipoCita, $idMenu) {
			try {
				$sql = "SELECT * FROM (
							SELECT TR.*
							FROM tipos_citas_det CD
							INNER JOIN tipos_registros_hc TR ON CD.id_tipo_reg=TR.id_tipo_reg
							WHERE CD.id_tipo_cita=".$idTipoCita."
							AND TR.id_menu=".$idMenu."
						) T
						LIMIT 1";
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaTiposRegistroHcTipoCita($id_tipo_cita) {
			try {
				$sql = "SELECT TR.*
						FROM tipos_citas_det CD
						INNER JOIN tipos_registros_hc TR ON CD.id_tipo_reg=TR.id_tipo_reg
						WHERE CD.id_tipo_cita=".$id_tipo_cita."
						ORDER BY CD.orden";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	}
?>
