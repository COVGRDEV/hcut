<?php
	require_once("DbConexion.php");
	
	class DbDiagnosticos extends DbConexion {
		public function getBuscarDiagnosticos($texto) {
			try {
				$texto = str_replace(" ", "%", $texto);
				$sql = "SELECT * FROM ciex
						WHERE codciex LIKE '%".$texto."%' 
						OR nombre LIKE '%".$texto."%'
						ORDER BY codciex
						LIMIT 50";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaDiagnosticosDetParam($texto) {
			try {
				$texto = str_replace(" ", "%", $texto);
				$sql = "SELECT * FROM ciex_consolidado
						WHERE codciex LIKE '%".$texto."%' 
						OR nombre LIKE '%".$texto."%'
						ORDER BY codciex
						LIMIT 50";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getDiagnosticoCiex($cod_ciex) {
			try {
				$sql = "SELECT * FROM ciex
						WHERE codciex='".$cod_ciex."'";
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getDiagnosticoCiexDet($cod_ciex) {
			try {
				$sql = "SELECT * FROM ciex_consolidado
						WHERE codciex='".$cod_ciex."'";
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getHcDiagnostico($id_hc) {
			try {
				$sql = "SELECT HC.*, CX.nombre, CX.codciexori, OJ.nombre_detalle AS ojo
						FROM diagnosticos_hc HC
						INNER JOIN ciex_consolidado CX ON HC.cod_ciex=CX.codciex
						LEFT JOIN listas_detalle OJ ON HC.id_ojo=OJ.id_detalle
						WHERE HC.id_hc=".$id_hc."
						ORDER BY HC.orden";
						
				//echo($sql);
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Obtener los ciex completos
		 */
		public function getDiagnosticoCiexTotal() {
			try {
				$sql = "SELECT * FROM ciex";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaHCDiagnosticosAnterior($id_hc) {
			try {
				$sql = "SELECT D.*, CX.nombre AS nom_ciex
						FROM diagnosticos_hc D
						INNER JOIN ciex_consolidado CX ON D.cod_ciex=CX.codciex
						WHERE D.id_hc=(
							SELECT MAX(H2.id_hc)
							FROM historia_clinica HC
							INNER JOIN historia_clinica H2 ON HC.id_paciente=H2.id_paciente
							INNER JOIN diagnosticos_hc D ON H2.id_hc=D.id_hc
							WHERE HC.id_hc=".$id_hc."
							AND HC.id_hc>H2.id_hc
							AND DATE(HC.fecha_hora_hc)>DATE(H2.fecha_hora_hc)
						)
						ORDER BY D.orden";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	}
?>
