<?php
	require_once("Configuracion.php");
	
	class DbConexionOld {
		protected $conexion;
		
		private $servidor;
		private $puerto;
		private $baseDatos;
		private $usuario;
		private $clave;
		
		public function __construct() {
			$this->servidor = Configuracion::$SERVIDOR;
			$this->puerto = Configuracion::$PUERTO;
			$this->baseDatos = Configuracion::$BASE_DATOS;
			$this->usuario = Configuracion::$USUARIOS;
			$this->clave = Configuracion::$PASS;
		}
		
		/**
		 * Método que crea una conexión con la base de datos
		 */
		protected function crearConexion() {
			$this->conexion = mysql_connect($this->servidor.":".$this->puerto, $this->usuario, $this->clave) or die('cannot connect : '. mysql_error());
            mysql_select_db($this->baseDatos);
			mysql_query("SET NAMES utf8", $this->conexion);
			mysql_query("SET CHARACTER SET utf8", $this->conexion);
		}
		
		/**
		 * Método que cierra la conexión con la base de datos
		 */
		protected function cerrarConexion() {
			mysql_close($this->conexion);
		}
		
		/**
		 * Metodo que realiza consultas en la base de datos
		 * @param $sql SQL con la consulta a realizar
		 * @return Resultado de la consulta SQL
		 */
		public function getDatos($sql) {
			$tabla = array();
			
			try {
				$this->crearConexion();
				
				$resultado = mysql_query($sql, $this->conexion);
				if ($resultado) {
					$numFilas = mysql_num_rows($resultado);
					$fila = 0;
					while ($fila < $numFilas) {
						$aux = mysql_fetch_array($resultado, MYSQL_ASSOC);
						$tabla[$fila]=$aux;
						$fila++;
					}
				}
				
				$this->cerrarConexion();
				return $tabla;
			} catch (Exception $e) {
				$this->cerrarConexion();
				return array();
			}
		}
		
		/**
		 * Metodo que realiza consultas en la base de datos que retornan un único registro
		 * @param $sql SQL con la consulta a realizar
		 * @return Resultado de la consulta SQL
		 */
		public function getUnDato($sql) {
			$fila = null;
			
			try {
				$this->crearConexion();
				
				$resultado = mysql_query($sql, $this->conexion);
				
				if ($resultado) {
					$numFilas = mysql_num_rows($resultado);
					if ($numFilas > 0){
						$fila = mysql_fetch_array($resultado, MYSQL_ASSOC);
					}
				}
				
				$this->cerrarConexion();
				return $fila;
			} catch (Exception $e) {
				$this->cerrarConexion();
				return null;
			}
		}
		
		/**
		 * Metodo que ejecuta una sentencia SQL
		 * @param $sql SQL con la sentencia a ejecutar
		 * @param $arrCampos array con los campos de salida
		 * @return Resultado de la sentencia SQL
		 */
		public function ejecutarSentencia($sql, $arrCampos) {
			$resultado = 0;
			try {
				$this->crearConexion();
				
				$resulAux = mysql_query($sql, $this->conexion);
				
				if ($resulAux) {
					if (count($arrCampos) > 0) {
						//Se ejecuta la segunda sentencia
						$cadenaAux = "";
						foreach ($arrCampos as $campoAux) {
							if ($cadenaAux != "") {
								$cadenaAux .= ", ";
							}
							$cadenaAux .= $campoAux;
						}
						
						$fila = null;
						$resulAux = mysql_query("SELECT ".$cadenaAux, $this->conexion);
						if ($resulAux) {
							$numFilas = mysql_num_rows($resulAux);
							if ($numFilas > 0){
								$resultado = mysql_fetch_array($resulAux, MYSQL_ASSOC);
							}
						}
					} else {
						$resultado = 1;
					}
				} else {
					$resultado = 0;
				}
				
				$this->cerrarConexion();
				
			} catch (Exception $e) {
				$this->cerrarConexion();
				$resultado = -1;
			}
			
			return $resultado;
		}
	}
?>
