<?php
	require_once("DbConexion.php");
	
	class DbMaestroInsumos extends DbConexion {
		/*
		 * Esta funcion obtiene listado de servicios segun la variable $parametro
		 */
		public function getInsumos($parametro) {
			try {
				$parametro = str_replace(" ", "%", $parametro);   
				
				$sql = "SELECT *
						FROM maestro_insumos
						WHERE nombre_insumo LIKE '%".$parametro."%'
						OR cod_insumo='".$parametro."'
						LIMIT 100";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Función que busca un insumo por su código
		public function getInsumo($cod_insumo) {
			try {
				$sql = "SELECT *
						FROM maestro_insumos
						WHERE cod_insumo='".$cod_insumo."'";
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	}
?>
