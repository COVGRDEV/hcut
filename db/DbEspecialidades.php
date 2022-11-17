<?php
	require_once("DbConexion.php");
	
	class DbEspecialidades extends DbConexion {
		//Función que obtiene el listado de especialidades
		public function getListaEspecialidades($ind_activo) {
			try {
				$sql = "SELECT *
						FROM especialidades ";
				if ($ind_activo != "") {
					$sql .= "WHERE ind_activo=".$ind_activo." ";
				}
				$sql .= "ORDER BY nombre_especialidad";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Función que obtiene el listado de vías de una especialidad
		public function getListaVias($id_especialidad, $ind_activo) {
			try {
				$sql = "SELECT *
						FROM vias_especialidades
						WHERE id_especialidad=".$id_especialidad." ";
				if ($ind_activo != "") {
					$sql .= "AND ind_activo=".$ind_activo." ";
				}
				$sql .= "ORDER BY nombre_via";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
	}
?>
