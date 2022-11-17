<?php
	require_once("DbHistoriaClinica.php");
	
	class DbConsultaPreqxLaser extends DbHistoriaClinica {
		/**
		 * Obtener los datos de a consulta de optometria a partir del ID de la HC
		 */
		public function getConsultaPreqxLaser($id_hc) {
	        try {
	            $sql = "SELECT CC.*, O.nombre_detalle AS ojo, EQ.nombre_detalle AS nombre_nomograma_equipo,
						PO.nombre_detalle AS patologia_ocular, CP.nombre_detalle AS cirugia_ocular,
						DATE_FORMAT(CC.fecha_cirugia, '%d/%m/%Y') AS fecha_cirugia_t
						FROM consultas_preqx_laser CC
						LEFT JOIN listas_detalle O ON CC.id_ojo=O.id_detalle
						LEFT JOIN listas_detalle EQ ON CC.nomograma_equipo=EQ.id_detalle 
						LEFT JOIN listas_detalle PO ON CC.patologia_ocular_valor=PO.id_detalle
						LEFT JOIN listas_detalle CP ON CC.cirugia_ocular_valor=CP.id_detalle
						WHERE CC.id_hc=".$id_hc;
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		//Crear consulta de optometria
		public function CrearConsultaPreqxLaser($id_paciente, $id_tipo_reg, $id_usuario_crea, $id_admision) {
	        try {
	            $sql = "CALL pa_crear_consultas_preqxlaser($id_paciente, $id_admision, $id_tipo_reg, $id_usuario_crea, @id)";
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$id_historia_clinica=$arrResultado["@id"];
				
				return $id_historia_clinica;
	            
	        } catch (Exception $e) {
	            return -2;
	        }
    	}
		
		//Crear consulta de optometria
		public function EditarConsultaPreqxLaser($hdd_id_hc_consulta, $hdd_id_admision, $querato_cilindro_od, $querato_eje_od, $querato_kplano_od,
				$querato_cilindro_oi, $querato_eje_oi, $querato_kplano_oi, $refraccion_esfera_od, $refraccion_cilindro_od, $refraccion_eje_od,
				$refraccion_lejos_od, $refraccion_cerca_od, $refraccion_esfera_oi, $refraccion_cilindro_oi, $refraccion_eje_oi, $refraccion_lejos_oi,
				$refraccion_cerca_oi, $cicloplejio_esfera_od, $cicloplejio_cilindro_od, $cicloplejio_eje_od, $cicloplejio_avcc_lejos_od,
				$cicloplejio_esfera_oi, $cicloplejio_cilindro_oi, $cicloplejio_eje_oi, $cicloplejio_avcc_lejos_oi, $refractivo_deseado_od,
				$refractivo_esfera_od, $refractivo_cilindro_od, $refractivo_eje_od, $refractivo_deseado_oi, $refractivo_esfera_oi, $refractivo_cilindro_oi,
				$refractivo_eje_oi, $nomograma_equipo, $nomograma_esfera_od, $nomograma_cilindro_od, $nomograma_eje_od, $nomograma_esfera_oi,
				$nomograma_cilindro_oi, $nomograma_eje_oi, $patologia_ocular_valor, $patologia_ocular_descripcion, $cirugia_ocular_valor,
				$cirugia_ocular_descripcion, $paquimetria_central_od, $paquimetria_central_oi, $diagnostico_preqx_laser, $array_diagnosticos,
				$id_usuario_crea, $tipo_guardar, $paquimetria_periferica_od, $paquimetria_periferica_oi ) {
			try {
				
				
				$sql_delete_diagnosticos = "DELETE FROM temporal_diagnosticos WHERE id_hc = $hdd_id_hc_consulta AND id_usuario = $id_usuario_crea";
				$arrCampos_delete[0] = "@id";
				if($this->ejecutarSentencia($sql_delete_diagnosticos, $arrCampos_delete)){
					$j=1;
					foreach($array_diagnosticos as $fila_diagnosticos){
						$ciex_diagnostico = $fila_diagnosticos[0];
						$valor_ojos = $fila_diagnosticos[1];
						$sql_insert_diagnosticos="INSERT INTO temporal_diagnosticos (id_hc, id_usuario, cod_ciex, id_ojo, orden)
												  VALUES ($hdd_id_hc_consulta, $id_usuario_crea, '$ciex_diagnostico', '$valor_ojos', $j)";
						//echo $sql_insert_diagnosticos."<br />";						  
						$arrCampos[0] = "@id";
	                	$this->ejecutarSentencia($sql_insert_diagnosticos, $arrCampos);
						$j=$j+1;						  
					}
				}
				$sql = "CALL pa_editar_consultas_preqxlaser($hdd_id_hc_consulta, $hdd_id_admision, '".$querato_cilindro_od."', '".$querato_eje_od."', '".$querato_kplano_od."', '".
						$querato_cilindro_oi."', '".$querato_eje_oi."', '".$querato_kplano_oi."', '".$refraccion_esfera_od."', '".$refraccion_cilindro_od."', '".$refraccion_eje_od."', ".
						$refraccion_lejos_od.", ".$refraccion_cerca_od.", '".$refraccion_esfera_oi."', '".$refraccion_cilindro_oi."', '".$refraccion_eje_oi."', ".$refraccion_lejos_oi.", ".
						$refraccion_cerca_oi.", '".$cicloplejio_esfera_od."', '".$cicloplejio_cilindro_od."', '".$cicloplejio_eje_od."', ".$cicloplejio_avcc_lejos_od.", '".
						$cicloplejio_esfera_oi."', '".$cicloplejio_cilindro_oi."', '".$cicloplejio_eje_oi."', ".$cicloplejio_avcc_lejos_oi.", '".$refractivo_deseado_od."', '".
						$refractivo_esfera_od."', '".$refractivo_cilindro_od."', '".$refractivo_eje_od."', '".$refractivo_deseado_oi."', '".$refractivo_esfera_oi."', '".
						$refractivo_cilindro_oi."', '".$refractivo_eje_oi."', ".$nomograma_equipo.", '".$nomograma_esfera_od."', '".$nomograma_cilindro_od."', '".$nomograma_eje_od."', '".
						$nomograma_esfera_oi."', '".$nomograma_cilindro_oi."', '".$nomograma_eje_oi."', ".$patologia_ocular_valor.", '".$patologia_ocular_descripcion."', ".
						$cirugia_ocular_valor.", '".$cirugia_ocular_descripcion."', '".$paquimetria_central_od."', '".$paquimetria_central_oi."', '".$diagnostico_preqx_laser."', ".
						$id_usuario_crea.", ".$tipo_guardar.", '".$paquimetria_periferica_od."', '".$paquimetria_periferica_oi."', @id)";
                $arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$out_ind_opt=$arrResultado["@id"];
				return $out_ind_opt;
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Validar que el usuario sea optometra y diferente al que la creo  
		 */
		public function ValidarUsuarioOptometra($id_hc, $id_usuario_sesion, $id_menu){
			try {
				$ind_datos_adicionales = 0;
				
				//Obtener el id del usuario que creo la consulta
	           	$sql = "SELECT id_usuario_crea FROM consultas_preqx_laser
						WHERE id_hc = $id_hc";
	            $tabla_usuario_crea = $this->getUnDato($sql);
				$id_usuario_crea = $tabla_usuario_crea['id_usuario_crea'];
								
				if($id_usuario_crea != $id_usuario_sesion){ //Si el usuario que inicio seesion es diferente al usuario que creo la consulta puede continuar
					//Conocer si el usuario que inicio session tiene permisos para la ventana de consulta_perqx_laser
					$sql_permiso="SELECT COUNT(*) AS ind_cantidad FROM usuarios u
								  INNER JOIN usuarios_perfiles up ON up.id_usuario = u.id_usuario
								  INNER JOIN permisos p ON p.id_perfil = up.id_perfil
								  WHERE u.id_usuario = $id_usuario_sesion
								  AND p.id_menu = $id_menu";
					$tabla_permisos = $this->getUnDato($sql_permiso);		
					//Retorna un 1 si el usuario si tiene permisos y 0 si no los tiene			
					$ind_datos_adicionales = $tabla_permisos['ind_cantidad'];			  
				}
	            return $ind_datos_adicionales;
	      	} catch (Exception $e) {
	            return array();
	        }
		}


		/**
		 * Obtener la informacion del complemento de la comsulta preqx laser
		 */
		public function getComplementoPreqxLaser($id_hc) {
	        try {
	            $sql = "SELECT * 
	            		FROM complemento_preqx_laser c
						WHERE c.id_hc = $id_hc";
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
	    
	    
	    /**
		 * Crear o editar los datos complementarios de la consulta preqx laser
		 */
	    public function CrearEditarComplementoConsultaPreqxLaser($hdd_id_hc_consulta, $refraccion_esfera_od, $refraccion_cilindro_od, $refraccion_eje_od,
				$refraccion_lejos_od, $refraccion_cerca_od, $refraccion_esfera_oi, $refraccion_cilindro_oi, $refraccion_eje_oi, $refraccion_lejos_oi,
				$refraccion_cerca_oi, $id_usuario_crea) {
			try {
				$sql = "CALL pa_crear_editar_complemento_preqxlaser(".$hdd_id_hc_consulta.", '".$refraccion_esfera_od."', '".$refraccion_cilindro_od."', '".
						$refraccion_eje_od."', ".$refraccion_lejos_od.", ".$refraccion_cerca_od.", '".$refraccion_esfera_oi."', '".$refraccion_cilindro_oi."', '".
						$refraccion_eje_oi."', ".$refraccion_lejos_oi.", ".$refraccion_cerca_oi.", ".$id_usuario_crea.", @id)";
                $arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$out_ind_complemento=$arrResultado["@id"];
				return $out_ind_complemento;
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Obtener los datos de a consulta de optometria a partir del ID de la HC
		 */
		public function getConsultaPreqxLaserAdmision($id_admision) {
	        try {
	            $sql = "SELECT PX.*
						FROM admisiones A
						INNER JOIN historia_clinica HC ON A.id_admision=HC.id_admision
						INNER JOIN consultas_preqx_laser PX ON HC.id_hc=PX.id_hc
						WHERE A.id_admision=".$id_admision;
				
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		
		
    }
?>
