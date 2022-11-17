<?php
	require_once("DbHistoriaClinica.php");
	
	class DbExamenesOptometria extends DbHistoriaClinica {
		public function get_examen_optometria($id_hc) {
	        try {
	            $sql = "SELECT EO.*
						FROM examenes_optometria EO
						WHERE EO.id_hc=".$id_hc;
				
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		public function get_examen_optometria_hc($id_hc, $id_examen, $id_ojo) {
	        try {
	            $sql = "SELECT *
						FROM examenes_optometria_hc
						WHERE id_hc=".$id_hc." ";
				if (trim($id_examen) != "") {
					$sql .= "AND id_examen=".$id_examen." ";
				} else {
					$sql .= "AND id_examen IS NULL ";
				}
				if (trim($id_ojo) != "") {
					$sql .= "AND id_ojo=".$id_ojo;
				} else {
					$sql .= "AND id_ojo IS NULL";
				}
				
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		public function get_lista_examenes_optometria_hc($id_hc) {
	        try {
	            $sql = "SELECT HC.*, ME.nombre_examen, ME.id_examen_compl, ME.observacion_predef, OJ.nombre_detalle AS ojo
						FROM examenes_optometria_hc HC
						INNER JOIN maestro_examenes ME ON HC.id_examen=ME.id_examen
						LEFT JOIN listas_detalle OJ ON HC.id_ojo=OJ.id_detalle
						WHERE HC.id_hc=".$id_hc."
						ORDER BY HC.id_examen_hc";
				
	            return $this->getDatos($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		public function get_examen_optometria_hc_proc($id_hc, $cod_procedimiento) {
			try {
	            $sql = "SELECT E.*, ME.nombre_examen, OJ.nombre_detalle AS ojo
						FROM examenes_optometria_hc E
						INNER JOIN maestro_examenes ME ON E.id_examen=ME.id_examen
						LEFT JOIN listas_detalle OJ ON E.id_ojo=OJ.id_detalle
						WHERE E.id_hc=".$id_hc."
						AND ME.cod_procedimiento='".$cod_procedimiento."'";
				
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		public function get_lista_examenes_optometria_tmp($id_usuario) {
	        try {
	            $sql = "SELECT * FROM temporal_examenes_op_hc
						WHERE id_usuario=".$id_usuario."
						ORDER BY id_reg";
				
	            return $this->getDatos($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		public function crear_examen_optometria($id_paciente, $id_tipo_reg, $id_usuario_crea, $id_admision) {
	        try {
	            $sql = "CALL pa_crear_examen_optometria(".$id_paciente.", ".$id_admision.", ".$id_tipo_reg.", ".$id_usuario_crea.", @id)";
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$id_historia_clinica=$arrResultado["@id"];
				
				return $id_historia_clinica;
	        } catch (Exception $e) {
	            return -2;
	        }
    	}
		
		public function editar_examen_optometria($id_hc, $id_admision, $nombre_usuario_alt, $array_diagnosticos, $tipo_guardar, $id_usuario) {
			try {
				/*Para Diagnosticos*/
				$sql = "DELETE FROM temporal_diagnosticos
						WHERE id_hc=".$id_hc." 
						AND id_usuario=".$id_usuario;
				$arrCampos[0] = "@id";
				if ($this->ejecutarSentencia($sql, $arrCampos)) {
					$j = 1;
					foreach ($array_diagnosticos as $fila_diagnosticos) {
						$ciex_diagnostico = $fila_diagnosticos[0];
						$valor_ojos = $fila_diagnosticos[1];
						$sql = "INSERT INTO temporal_diagnosticos (id_hc, id_usuario, cod_ciex, id_ojo, orden)
								VALUES (".$id_hc.", ".$id_usuario.", '".$ciex_diagnostico."', '".$valor_ojos."', ".$j.")";
						
						$arrCampos[0] = "@id";
	                	$this->ejecutarSentencia($sql, $arrCampos);
						$j++;
					}
				}
				
				if ($nombre_usuario_alt != "") {
					$nombre_usuario_alt = "'".$nombre_usuario_alt."'";
				} else {
					$nombre_usuario_alt = "NULL";
				}
				$sql = "CALL pa_editar_examen_optometria(".$id_hc.", ".$id_admision.", ".$nombre_usuario_alt.", ".$id_usuario.", ".$tipo_guardar.", @id)";
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado = $arrResultado["@id"];
				
				return $resultado;
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function borrar_temporal_examenes($id_usuario) {
	        try {
	            $sql = "DELETE FROM temporal_examenes_op_hc
						WHERE id_usuario=".$id_usuario;
				
				$this->ejecutarSentencia($sql, array());
				
	            return 1;
	        } catch (Exception $e) {
	            return -2;
	        }
		}
		
		public function crear_temporal_examen_op_hc($id_usuario, $id_examen, $id_ojo, $observaciones_examen, $id_examen_hc, $pu_od, $pu_oi) {
	        try {
				if ($id_examen == "") {
					$id_examen = "NULL";
				}
				if ($id_ojo == "") {
					$id_ojo = "NULL";
				}
				if ($observaciones_examen == "") {
					$observaciones_examen = "NULL";
				} else {
					$observaciones_examen = "'".$observaciones_examen."'";
				}
				if ($id_examen_hc == "") {
					$id_examen_hc = "NULL";
				}
				if ($pu_od == "") {
					$pu_od = "NULL";
				} else {
					$pu_od = "'".$pu_od."'";
				}
				if ($pu_oi == "") {
					$pu_oi = "NULL";
				} else {
					$pu_oi = "'".$pu_oi."'";
				}
				
				$sql = "INSERT INTO temporal_examenes_op_hc
						(id_usuario, id_examen, id_ojo, observaciones_examen, id_examen_hc, pu_od, pu_oi)
						VALUES (".$id_usuario.", ".$id_examen.", ".$id_ojo.", ".$observaciones_examen.", ".$id_examen_hc.", ".$pu_od.", ".$pu_oi.")";
				
				$this->ejecutarSentencia($sql, array());
				
	            return 1;
	        } catch (Exception $e) {
	            return -2;
	        }
		}
		
		public function get_lista_examenes_optometria_hc_det($id_hc) {
	        try {
	            $sql = "SELECT D.*
						FROM examenes_optometria_hc E
						INNER JOIN examenes_optometria_hc_det D ON E.id_examen_hc=D.id_examen_hc
						WHERE E.id_hc=".$id_hc."
						ORDER BY D.id_examen_hc, D.id_examen_hc_det";
				
	            return $this->getDatos($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		public function get_lista_examenes_optometria_hc_det2($id_examen_hc) {
	        try {
	            $sql = "SELECT * FROM examenes_optometria_hc_det
						WHERE id_examen_hc=".$id_examen_hc."
						ORDER BY id_examen_hc_det";
				
	            return $this->getDatos($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		public function get_siguiente_cont_arch_det($id_examen_hc) {
	        try {
	            $sql = "SELECT IFNULL(MAX(cont_arch) + 1, 0) AS cont_arch
						FROM examenes_optometria_hc_det
						WHERE id_examen_hc=".$id_examen_hc;
				
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		public function crear_examen_optometria_hc_det($id_examen_hc, $ruta_arch_examen, $cont_arch, $id_usuario) {
	        try {
				$sql = "INSERT INTO examenes_optometria_hc_det
						(id_examen_hc, ruta_arch_examen, cont_arch, id_usuario_crea, fecha_crea)
						VALUES (".$id_examen_hc.", '".$ruta_arch_examen."', ".$cont_arch.", ".$id_usuario.", NOW())";
				
				$this->ejecutarSentencia($sql, array());
				
	            return 1;
	        } catch (Exception $e) {
	            return -2;
	        }
		}
		
		public function borrar_examen_optometria_hc_det($id_examen_hc_det, $id_usuario) {
	        try {
	            $sql = "CALL pa_borrar_examen_optometria_hc_det(".$id_examen_hc_det.", ".$id_usuario.", @id)";
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado = $arrResultado["@id"];
				
				return $resultado;
	        } catch (Exception $e) {
	            return -2;
	        }
		}
		
		public function get_lista_examenes_optometria_paciente($id_paciente) {
	        try {
	            $sql = "SELECT EX.id_hc, EX.id_examen, EX.id_examen_hc, ME.nombre_examen, EX.id_ojo, OJ.nombre_detalle AS ojo
						FROM historia_clinica HC
						INNER JOIN examenes_optometria_hc EX ON HC.id_hc=EX.id_hc
						INNER JOIN maestro_examenes ME ON EX.id_examen=ME.id_examen
						LEFT JOIN listas_detalle OJ ON EX.id_ojo=OJ.id_detalle
						WHERE HC.id_paciente=".$id_paciente."
						ORDER BY EX.id_hc, EX.id_examen_hc";
				
	            return $this->getDatos($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		public function get_lista_examenes_optometria_hc_det_paciente($id_paciente) {
	        try {
	            $sql = "SELECT OD.*, HC.id_hc, DATE_FORMAT(HC.fecha_hora_hc, '%d/%m/%Y') AS fecha_hc_t,
						OH.id_examen, OH.observaciones_examen, ME.nombre_examen, OJ.nombre_detalle AS ojo
						FROM historia_clinica HC
						INNER JOIN examenes_optometria_hc OH ON HC.id_hc=OH.id_hc
						INNER JOIN examenes_optometria_hc_det OD ON OH.id_examen_hc=OD.id_examen_hc
						INNER JOIN maestro_examenes ME ON OH.id_examen=ME.id_examen
						LEFT JOIN listas_detalle OJ ON OH.id_ojo=OJ.id_detalle
						WHERE HC.id_paciente=".$id_paciente."
						ORDER BY ME.nombre_examen, OH.id_examen, OH.id_hc, OD.cont_arch";
				
	            return $this->getDatos($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
    }
?>
