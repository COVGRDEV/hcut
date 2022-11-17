<?php
	require_once("DbConexion.php");
	
	class DbEstadosAtencion extends DbConexion {
		/*
		 * Muestra el listado de estados_atencion
		 */
		public function getEstadosatencion() {
			try {
				$sql = "SELECT ES.*, M.nombre_menu, M.pagina_menu, M.ind_visible
						FROM estados_atencion ES
						LEFT JOIN menus M ON ES.id_menu=M.id_menu
						ORDER BY ES.orden";
				//echo $sql;
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/*
		 * Muestra el listado de estados_atencion
		 */
		public function getListaEstadosAtencion($ind_activo, $ind_visible) {
			try {
				$sql = "SELECT ES.*, M.nombre_menu, M.pagina_menu, M.ind_visible
						FROM estados_atencion ES
						LEFT JOIN menus M ON ES.id_menu=M.id_menu
						WHERE ES.ind_activo=".$ind_activo."
						AND ES.ind_visible=".$ind_visible."
						ORDER BY ES.orden";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Seleccionar los profesionales que atienden
		 */
		public function getProfesionalesAtencion($id_admision) {
			try {
				$sql = "SELECT MAX(id_estado_atencion) AS estado, CONCAT(u.nombre_usuario, ' ', u.apellido_usuario) AS profesionales FROM usuarios u
						INNER JOIN admisiones_estados_atencion ae ON ae.id_usuario_prof = u.id_usuario
						WHERE id_admision = $id_admision 
						GROUP BY CONCAT(u.nombre_usuario, ' ', u.apellido_usuario)
						ORDER BY estado DESC";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/*
		 * Muestra el estado de atención solicitado
		 */
		public function getEstadoAtencion($id_estado_atencion) {
			try {
				$sql = "SELECT ES.*, M.nombre_menu, M.pagina_menu, M.ind_visible
						FROM estados_atencion ES
						LEFT JOIN menus M ON ES.id_menu=M.id_menu
						WHERE ES.id_estado_atencion=".$id_estado_atencion;
				//echo($sql);
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	}
?>
