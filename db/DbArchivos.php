<?php		
	require_once("DbConexion.php");	
	
	class DbRegistroArchivos extends DbConexion {
		
		//Mapeo de la tabla 
		public $id_reg_archivos; 
		public $id_tipo_archivo; 		
		public $prefijo;
		public $observaciones; 
		public $nombre_original; 
				
		//Crear registro de archivos (cargue/lote)
		/* @modulo: módulo/tabla del sistema con el cual se va a relacionar el lote de archivos
		   @id_modulo: ID de registro en tabla del módulo
		*/
		public function CrearEditarRegistroArchivos($id_usuario, $modulo, $id_modulo) {
	        try {
	            $sql = "CALL pa_crear_editar_registro_archivos($this->id_reg_archivos, $this->id_tipo_archivo, '$this->observaciones', '$modulo', $id_modulo, $id_usuario, @id)"; 
				$arrCampos[0] = "@id"; 
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$id_registro_archivos=$arrResultado["@id"];
				//echo "<br>$sql";
				return $id_registro_archivos; 
	        } catch (Exception $e) {  	
	            return -2;
	        }
    	}
		
		/* Consultar Registro de Archivos HC */ 
		public function getRegistroArchivosHC($id_hc, $id_tipo_archivo=null) {
			try {			
				$sql = "SELECT R.id_reg_archivos, R.id_tipo_archivo, R.observaciones 
						FROM registro_archivos R, hc_registro_archivos RHC 
						WHERE R.id_reg_archivos=RHC.id_reg_archivos AND RHC.id_hc=".$id_hc;						
				if (!is_null($id_tipo_archivo)) {
					$sql .= " AND id_tipo_archivo=".$id_tipo_archivo;
				}				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}			
		} 
		
		/* Consultar último consecutivo registrado para un un Registro de Archivos */ 
		public function getMaximoConsecutivoArchivos() {
			try {			
				$sql = "SELECT MAX(SUBSTRING_INDEX(SUBSTRING_INDEX(A.ruta, '_', -1), '.', 1)) AS max_consecutivo 
						FROM archivos A, registro_archivos R, hc_registro_archivos AR 
						WHERE A.id_reg_archivos=R.id_reg_archivos AND R.id_reg_archivos=AR.id_reg_archivos 
						AND R.id_reg_archivos=".$this->id_reg_archivos; 
				//echo "<br>".$sql;
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}			
		} 			
		
		/* Consultar Registro de Archivos */ 
		public function getRegistroArchivos($id_reg_archivo) {
			try {			
				$sql = "SELECT R.id_reg_archivos, R.id_tipo_archivo, R.observaciones, MR.nombre 
						FROM registro_archivos R, maestro_tipos_archivo MR 
						WHERE R.id_tipo_archivo=MR.id_tipo_archivo AND R.id_reg_archivos=".$id_reg_archivo; 
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			} 
		} 		
	}	
	
	class DbArchivo extends DbRegistroArchivos { 
		
		/* Mapeo de la tabla */
		public $id_archivo; 
		public $id_reg_archivos; 
		public $ruta; 
				
		/* Crear registro de archivo */
		public function CrearEditarArchivo($id_usuario) {
	        try {
				$this->nombre_original=substr($this->nombre_original, 0, 200); 
	            $sql = "CALL pa_crear_editar_archivo($this->id_archivo, $this->id_reg_archivos, '$this->ruta', '$this->nombre_original', $id_usuario, @id)"; 
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$id_archivo=$arrResultado["@id"];
				//echo "<br>$sql"; 
				return $id_archivo; 
	        } catch (Exception $e) {	        	        	
	            return -2;
	        }
    	}	
		
		/* Borrar registro; guarda log (PENDIENTE) */
		public function BorrarArchivo($id_usuario) {
	        try {
	            $sql = "CALL pa_borrar_archivo($this->id_archivo, $id_usuario, @id)"; 
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$id_archivo=$arrResultado["@id"];
				//echo "<br>$sql"; 
				return $id_archivo; 
	        } catch (Exception $e) {	        	        	
	            return -2;
	        }
    	} 

		/* Consultar archivos en una HC */ 
		public function getArchivosHc($id_hc, $id_tipo_archivo=null) {
			try {			
				$sql = "SELECT A.id_archivo, A.id_reg_archivos, A.ruta, A.nombre_original, R.id_tipo_archivo, 
							A.id_usuario_crea, DATE_FORMAT(A.fecha_crea, '%d/%m/%Y') fecha 
						FROM archivos A, registro_archivos R, hc_registro_archivos AR 
						WHERE A.id_reg_archivos=R.id_reg_archivos AND R.id_reg_archivos=AR.id_reg_archivos 
							AND AR.id_hc=".$id_hc; 	  
				if (!is_null($id_tipo_archivo)) {
					$sql .= " AND R.id_tipo_archivo=".$id_tipo_archivo;
				}
				$sql .= " ORDER BY A.id_reg_archivos ASC, fecha DESC"; 
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		} 
		
		/* Consultar archivos de un registro */ 
		/*public function getArchivosRegistroHc($id_paciente=null, $id_hc=null, $id_registro_archivos=null) {
			try {			
				$sql = "SELECT A.id_reg_archivos, R.id_tipo_archivo, MR.nombre, A.id_archivo, A.ruta, A.nombre_original, 
							A.id_usuario_crea, DATE_FORMAT(A.fecha_crea, '%d/%m/%Y') fecha 
						FROM archivos A 
						INNER JOIN registro_archivos R ON (A.id_reg_archivos=R.id_reg_archivos) 
						INNER JOIN maestro_tipos_archivo MR ON (MR.id_tipo_archivo=R.id_tipo_archivo) 
						INNER JOIN hc_registro_archivos AR ON ( 
							R.id_reg_archivos=AR.id_reg_archivos 							
							AND AR.id_hc=".$id_hc." 
						)";  
				echo "<br>".$sql; 
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		} 		*/
		
		/* Consultar archivo */ 
		public function getArchivo() {
			try {			
				$sql = "SELECT A.* FROM archivos A WHERE A.id_archivo=".$this->id_archivo;
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}			
		} 		
	}
?>