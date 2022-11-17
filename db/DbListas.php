<?php
	require_once("DbConexion.php");
	
	class DbListas extends DbConexion {//Clase que hace referencia a la tabla: listas_detalle
		public function getListaDetalles($idLista, $indActivo = 1) {
			try {
				$sql = "SELECT id_detalle, codigo_detalle, nombre_detalle, orden
						FROM listas_detalle
						WHERE id_lista=".$idLista."
						AND ind_activo=".$indActivo."
						ORDER BY orden";
				
				//echo($sql);
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			
			}
		}
		
		public function getListaLugaresMercadeo($indActivo = 1) {
			try {
				$sql = "SELECT id_detalle, codigo_detalle, nombre_detalle, orden
						FROM listas_detalle
						WHERE id_detalle IN (464,491, 466, 465, 511)
						AND ind_activo=".$indActivo."
						ORDER BY orden";
				
				//echo($sql);
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			
			}
		}
		
		
		//Esta funcion me retorna los valores desde id_detalle 3 hasta ide_detalle 10
		public function getTipodocumento() {
			try {
				$sql = "SELECT *
						FROM listas_detalle
						WHERE id_lista=2
						AND ind_activo=1
						ORDER BY orden";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Esta funcion retorna los valores con id_lista = 6 
		public function getListaEtnia() {
			try {
				$sql = "SELECT * 
						FROM listas_detalle
						WHERE id_lista=6
						AND ind_activo=1
						ORDER BY orden";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Esta funcion retorna los valores con id_lista = 5 
		public function getListaZona() {
			try {
				$sql = "SELECT * 
						FROM listas_detalle
						WHERE id_lista=5
						AND ind_activo=1
						ORDER BY orden";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaMercadeo() {
			try {
				$sql = "SELECT *FROM listas
						WHERE id_lista IN (80,86,87,88,89,90,91)
						ORDER BY nombre_lista";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListasById($id_lista) {
			try {
				$sql = "SELECT * 
						FROM listas
						WHERE id_lista=".$id_lista;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Esta funcion retorna los valores con id_lista = 5 
		public function getListaTipoSangre($idTipoSangre) {
			try {
				$sql = "SELECT * 
						FROM listas_detalle
						WHERE id_lista=".$idTipoSangre."
						AND ind_activo=1
						ORDER BY orden";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Esta funcion retorna los valores con id_lista = 1 
		public function getListaRh($idLista) {
			try {
				$sql = "SELECT * 
						FROM listas_detalle
						WHERE id_lista=".$idLista."
						AND ind_activo=1
						ORDER BY orden";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Esta funcion retorna los valores con id_lista = 1 
		public function getTipoSexo() {
			try {
				$sql = "SELECT * 
						FROM listas_detalle
						WHERE id_lista=1
						AND ind_activo=1
						ORDER BY orden";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Esta funcion retorna los valores con id_lista = 1 
		public function getListaDesplazado() {
			try {
				$sql = "SELECT * 
						FROM listas_detalle
						WHERE id_lista=10
						AND ind_activo=1
						ORDER BY orden";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getDetalle($id_detalle) {
			try {
				$sql = "SELECT * FROM listas_detalle
						WHERE id_detalle=".$id_detalle;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
        
		public function getListaRecursiva($id_lista) {
			try {
				$sql = "SELECT * FROM listas_recursivas
						WHERE id_lista=".$id_lista;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaDetallesRec($id_lista, $ind_activo) {
			try {
				$sql = "SELECT * FROM listas_recursivas_det
						WHERE id_lista=".$id_lista." ";
				if ($ind_activo != "") {
					$sql .= "AND ind_activo=".$ind_activo." ";
				}
				$sql .= "ORDER BY id_detalle_base, orden";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaDetallesRecBase($id_lista, $id_detalle_base, $ind_activo) {
			try {
				$sql = "SELECT * FROM listas_recursivas_det
						WHERE id_lista=".$id_lista." ";
				if ($id_detalle_base != "") {
					$sql .= "AND id_detalle_base=".$id_detalle_base." ";
				} else {
					$sql .= "AND id_detalle_base IS NULL ";
				}
				if ($ind_activo != "") {
					$sql .= "AND ind_activo=".$ind_activo." ";
				}
				$sql .= "ORDER BY orden";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaDetallesRecHijos($id_lista, $ind_activo) {
			try {
				$sql = "SELECT * FROM listas_recursivas_det
						WHERE id_lista=".$id_lista." 
						AND id_detalle_base IS NOT NULL ";
				if ($ind_activo != "") {
					$sql .= "AND ind_activo=".$ind_activo." ";
				}
				$sql .= "ORDER BY id_detalle_base, orden";				
				//echo $sql;				
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}		
		
		public function getListaDetallesRecCompleto($ind_activo) {
			try {
				$sql = "SELECT * FROM listas_recursivas_det ";
				if ($ind_activo != "") {
					$sql .= "WHERE ind_activo=".$ind_activo." ";
				}
				$sql .= "ORDER BY id_lista, id_detalle_base, orden";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getDetalleRec($id_detalle) {
			try {
				$sql = "SELECT * FROM listas_recursivas_det
						WHERE id_detalle=".$id_detalle;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
        //Consulta la tabla: sedes_detalle
        public function getSedesDetalle($id_detalle) {
			try {
				$sql = "SELECT SD.*, DE.cod_prestador, DE.nombre_prestador, DE.id_tipo_documento, TD.codigo_detalle AS codigo_tipo_documento,
						TD.nombre_detalle AS tipo_documento, DE. numero_documento, DATE_FORMAT(SD.fecha_resol_facturas, '%d/%m/%Y') AS fecha_resol_faturas_t,
						DATE_FORMAT(SD.fecha_resol_vence, '%d/%m/%Y') AS fecha_resol_vence_t
						FROM sedes_det SD
						LEFT JOIN datos_entidad DE ON SD.id_compania=DE.id_prestador
						LEFT JOIN listas_detalle TD ON DE.id_tipo_documento=TD.id_detalle
						WHERE SD.id_detalle=".$id_detalle;
				//echo($sql);
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
        //Consulta la tabla: sedes_detalle por identificador principal
        public function getSedesDetalleId($id_sede_det) {
			try {
				$sql = "SELECT SD.*, DE.cod_prestador, DE.nombre_prestador, DE.id_tipo_documento, TD.codigo_detalle AS codigo_tipo_documento,
						TD.nombre_detalle AS tipo_documento, DE. numero_documento, DATE_FORMAT(SD.fecha_resol_facturas, '%d/%m/%Y') AS fecha_resol_faturas_t,
						DATE_FORMAT(SD.fecha_resol_vence, '%d/%m/%Y') AS fecha_resol_vence_t
						FROM sedes_det SD
						LEFT JOIN datos_entidad DE ON SD.id_compania=DE.id_prestador
						LEFT JOIN listas_detalle TD ON DE.id_tipo_documento=TD.id_detalle
						WHERE SD.id_sede_det=".$id_sede_det;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		
		public function getSedesDetalleIncapacidad($id_detalle) {
			try {
				$sql = "SELECT SD.*, DE.cod_prestador, DE.nombre_prestador, DE.id_tipo_documento, TD.codigo_detalle AS codigo_tipo_documento,
					   TD.nombre_detalle AS lugar_sede, DE. numero_documento, DATE_FORMAT(SD.fecha_resol_facturas, '%d/%m/%Y') AS fecha_resol_faturas_t,
					   DATE_FORMAT(SD.fecha_resol_vence, '%d/%m/%Y') AS fecha_resol_vence_
					   FROM sedes_det SD
					   LEFT JOIN datos_entidad DE ON SD.id_compania=DE.id_prestador
					   LEFT JOIN listas_detalle TD ON SD.id_detalle=TD.id_detalle
					   WHERE SD.id_detalle=".$id_detalle;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
	public function getListasMercadeoAdmision(){
			try {
				$sql = "SELECT * FROM listas
						WHERE id_lista IN(95,96,100,91,101)
						ORDER BY nombre_lista ASC";
				
				//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
			
			
		}
	
	}
?>
