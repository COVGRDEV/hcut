<?php
	require_once("DbConexion.php");
	
	class DbMaestroProcedimientos extends DbConexion {
		/*
		 * Esta funcion obtiene un procedimiento por su código
		 */
		public function getProcedimiento($cod_procedimiento, $ind_activo = "") {
			try {
				$sql = "SELECT MP.*, E.nombre_especialidad, VE.nombre_via
						FROM maestro_procedimientos MP
						LEFT JOIN especialidades E ON MP.id_especialidad=E.id_especialidad
						LEFT JOIN vias_especialidades VE ON MP.id_via=VE.id_via AND MP.id_especialidad=VE.id_especialidad
						WHERE MP.cod_procedimiento='".$cod_procedimiento."'";
				if ($ind_activo != "") {
					$sql .= "";
				}
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/*
		 * Esta funcion obtiene listado de servicios segun la variable $parametro
		 */
		public function getProcedimientos($parametro, $ind_activo = "") {
			try {
				$parametro = str_replace(" ", "%", $parametro);
				
				$sql = "SELECT MP.*, E.nombre_especialidad, VE.nombre_via
						FROM maestro_procedimientos MP
						LEFT JOIN especialidades E ON MP.id_especialidad=E.id_especialidad
						LEFT JOIN vias_especialidades VE ON MP.id_via=VE.id_via AND MP.id_especialidad=VE.id_especialidad
						WHERE (MP.nombre_procedimiento LIKE '%".$parametro."%'
						OR MP.cod_procedimiento LIKE '".$parametro."%') ";
				if ($ind_activo != "") {
					$sql .= "AND MP.ind_activo=".$ind_activo." ";
				}
				$sql .= "ORDER BY MP.cod_procedimiento LIMIT 100";
				
				//echo($sql);
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/*
		 * Esta funcion obtiene listado de procedimientos del Manual ISS 2001
		 */
		public function getProcedimientosIss2001($parametro) {
			try {
				$parametro = str_replace(" ", "%", $parametro);
	
				$sql = "SELECT *
						FROM maestro_procedimientos
						WHERE (nombre_procedimiento LIKE '%".$parametro."%'
						OR cod_procedimiento LIKE '%".$parametro."%')
						ORDER BY cod_procedimiento
						LIMIT 100";
				//echo $sql;
	
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	
		/*
		 * Esta funcion obtiene todo el listado de procedimientos
		 */
		public function getListaProcedimientos() {
			try {
				$sql = "SELECT *
						FROM maestro_procedimientos 
						ORDER BY nombre_procedimiento";
	
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	
		/*
		 * Esta funcion obtiene listado de servicios segun la variable $parametro
		 */
		public function getProcedimientosEcopetrol($parametro, $ind_activo = "") {
			try {
				$parametro = str_replace(" ", "%", $parametro);
	
				$sql = "SELECT PE.cod_eco, P.cod_procedimiento, P.nombre_procedimiento
						FROM maestro_procedimientos_eco PE
						INNER JOIN maestro_procedimientos P ON PE.cod_procedimiento=P.cod_procedimiento
						WHERE (P.nombre_procedimiento LIKE '%".$parametro."%'
						OR P.cod_procedimiento LIKE '%".$parametro."%') ";
				if ($ind_activo != "") {
					$sql .= "AND P.ind_activo=".$ind_activo." ";
				}
				$sql .= "ORDER BY PE.cod_eco";
	
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	
		/*
		 * Retorna los conceptos (procedimientos, medicamentos, insumos) que se ajusten a un parámetro
		 */
		public function getListaConceptos($parametro) {
			try {
				$parametro = str_replace(" ", "%", $parametro);
	
				$sql = "SELECT cod_procedimiento AS cod_insumo, nombre_procedimiento AS nombre_insumo, 'P' AS tipo_precio
						FROM maestro_procedimientos
						WHERE (nombre_procedimiento LIKE '%".$parametro."%'
						OR cod_procedimiento LIKE '%".$parametro."%')
						UNION ALL
						SELECT cod_medicamento, CONCAT(nombre_generico, ' - ', nombre_comercial), 'M'
						FROM maestro_medicamentos
						WHERE nombre_generico LIKE '%".$parametro."%'
						OR nombre_comercial LIKE '%".$parametro."%'
						OR cod_medicamento='".$parametro."'
						UNION ALL
						SELECT cod_insumo, nombre_insumo, 'I'
						FROM maestro_insumos
						WHERE nombre_insumo LIKE '%".$parametro."%'
						OR cod_insumo='".$parametro."'
						ORDER BY cod_insumo, nombre_insumo
						LIMIT 100";
	
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	
		/*Método que retorna el listado de procedimientos solicitados en un registro de historia clínica */
		public function getListaHCProcedimientosSolic($id_hc) {
			try {
				$sql = "SELECT PS.*, MP.nombre_procedimiento, MP.tipo_procedimiento, OJ.nombre_detalle AS ojo
						FROM hc_procedimientos_solic PS
						INNER JOIN maestro_procedimientos MP ON PS.cod_procedimiento=MP.cod_procedimiento
						LEFT JOIN listas_detalle OJ ON PS.id_ojo=OJ.id_detalle
						WHERE PS.id_hc=".$id_hc."
						ORDER BY PS.orden";
	
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	
		public function crearEditarHCProcedimientosSolic($id_hc, $lista_procedimientos_solic, $id_usuario) {
			try {
				$sql = "DELETE FROM temporal_hc_procedimientos_solic
						WHERE id_hc=".$id_hc."
						AND id_usuario=".$id_usuario;
				$arrCampos_delete[0] = "@id";
	
				$j = 1;
				foreach ($lista_procedimientos_solic as $procedimiento_aux) {
					$cod_procedimiento_aux = $procedimiento_aux["cod_procedimiento"];
					$id_ojo_aux = $procedimiento_aux["id_ojo"];
	
					if ($id_ojo_aux == "") {
						$id_ojo_aux = "NULL";
					}
	
					$sql = "CALL pa_crear_temporal_hc_procedimientos_solic(".$id_hc.", ".$id_usuario.", '".$cod_procedimiento_aux."', ".$id_ojo_aux.", ".$j.", @id)";
	
					$arrCampos[0] = "@id";
					$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
					$resultado = $arrResultado["@id"];
	
					if ($resultado <= 0) {
						return -4;
					}
					$j++;
				}
	
				$sql = "CALL pa_editar_hc_procedimientos_solic(".$id_hc.", ".$id_usuario.", @id)";
	
				//echo($sql);
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado = $arrResultado["@id"];
	
				return $resultado;
			} catch (Exception $e) {
				return -2;
			}
		}
	
		//Crea el procedimiento
		public function crear_procedimiento($cod_procedimiento, $nombre_procedimiento, $id_especialidad, $id_via,
				$ind_proc_qx, $ind_lateralidad, $cod_und_negocios, $cod_centro_costos, $ind_activo, $id_usuario) {
			try {
				if ($id_especialidad == "") {
					$id_especialidad = "NULL";
				}
				if ($id_via == "") {
					$id_via = "NULL";
				}
				if ($cod_und_negocios == "") {
					$cod_und_negocios = "NULL";
				} else {
					$cod_und_negocios = "'".$cod_und_negocios."'";
				}
				if ($cod_centro_costos == "") {
					$cod_centro_costos = "NULL";
				} else {
					$cod_centro_costos = "'".$cod_centro_costos."'";
				}
				$sql = "CALL pa_crear_procedimiento('".$cod_procedimiento."', '".$nombre_procedimiento."', ".
						$id_especialidad.", ".$id_via.", ".$ind_proc_qx.", ".$ind_lateralidad.", ".$cod_und_negocios.", ".
						$cod_centro_costos.", ".$ind_activo.", ".$id_usuario.", 1, @id)";
	
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado_out = $arrResultado["@id"];
	
				return $resultado_out;
			} catch (Exception $e) {
				return array();
			}
		}
	
		//Edita el procedimiento
		public function editar_procedimiento($cod_procedimiento, $nombre_procedimiento, $id_especialidad, $id_via,
				$ind_proc_qx, $ind_lateralidad, $cod_und_negocios, $cod_centro_costos, $ind_activo, $id_usuario) {
			try {
				if ($id_especialidad == "") {
					$id_especialidad = "NULL";
				}
				if ($id_via == "") {
					$id_via = "NULL";
				}
				if ($cod_und_negocios == "") {
					$cod_und_negocios = "NULL";
				} else {
					$cod_und_negocios = "'".$cod_und_negocios."'";
				}
				if ($cod_centro_costos == "") {
					$cod_centro_costos = "NULL";
				} else {
					$cod_centro_costos = "'".$cod_centro_costos."'";
				}
				$sql = "CALL pa_editar_procedimiento('".$cod_procedimiento."', '".$nombre_procedimiento."', ".
						$id_especialidad.", ".$id_via.", ".$ind_proc_qx.", ".$ind_lateralidad.", ".$cod_und_negocios.", ".
						$cod_centro_costos.", ".$ind_activo.", ".$id_usuario.", 1, @id)";
				//echo($sql);
	
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado_out = $arrResultado["@id"];
	
				return $resultado_out;
			} catch (Exception $e) {
				return array();
			}
		}
	
		//Método que actualiza los procedimientos de acuerdo al contenido de un vector
		public function actualizar_procedimientos($arr_procedimientos, $ind_inhabilitar, $id_usuario) {
			try {
				//Se limpia el temporal de procedimientos
				$sql = "DELETE FROM temporal_maestro_procedimientos
						WHERE id_usuario=".$id_usuario;
				$arrCampos[0] = "@id";
				if ($this->ejecutarSentencia($sql, $arrCampos)) {
					$sql_base = "INSERT INTO temporal_maestro_procedimientos
								 (id_usuario, cod_procedimiento, nombre_procedimiento, tipo_procedimiento)
								 VALUES ";
					$sql = "";
					$cont_aux = 0;
					foreach ($arr_procedimientos as $proc_aux) {
						if ($sql != "" && $cont_aux % 200 == 0) {
							$sql = $sql_base.$sql;
							//echo $sql."<br />";						  
							$this->ejecutarSentencia($sql, $arrCampos);
	
							$sql = "";
						}
						if ($sql != "") {
							$sql .= ", ";
						}
						$sql .= "(".$id_usuario.", '".$proc_aux["cod_procedimiento"]."', '".$proc_aux["nombre_procedimiento"]."',
								CASE WHEN '".$proc_aux["cod_procedimiento"]."' LIKE '890%' THEN 'C' ELSE 'P' END)";
	
						$cont_aux++;
					}
	
					if ($sql != "") {
						$sql = $sql_base.$sql;
						//echo $sql."<br />";						  
						$this->ejecutarSentencia($sql, $arrCampos);
					}
				}
	
				$sql = "CALL pa_actualizar_procedimientos(".$ind_inhabilitar.", ".$id_usuario.", @id)";
				echo($sql);
	
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado_out = $arrResultado["@id"];
	
				return $resultado_out;
			} catch (Exception $e) {
				return array();
			}
		}
		
	}
?>
