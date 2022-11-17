<?php
	require_once("DbConexion.php");
	
	class DbAutorizaciones extends DbConexion {
		
		/*
		 * Esta funcion obtiene todo el listado de procedimientos
		 */
		 
	 
		public function autorizarProcedimiento($tipoAuto, $idOrdenMedica, $idOrdenMedicaDet, $ciex, $idProveedorSala, $idProveedorAnestesiologo, $idProveedorEspecialista, $observacion, $fechaVenc, $usuarioCrea, $tipoProcedimiento, $codProcedimiento, $idPaquete, $lugarAutorizacion, $ojo) {
			try {
	
				$tipoAuto == "" ? $tipoAuto = "NULL" : $tipoAuto = "" . $tipoAuto . "";
				$idOrdenMedica == "" ? $idOrdenMedica = "NULL" : $idOrdenMedica = "" . $idOrdenMedica . "";
				$idOrdenMedicaDet == "" ? $idOrdenMedicaDet = "NULL" : $idOrdenMedicaDet = "" . $idOrdenMedicaDet . "";
				$ciex == "" ? $ciex = "NULL" : $ciex = "'" . $ciex . "'";
				$idProveedorSala == "" ? $idProveedorSala = "NULL" : $idProveedorSala = "" . $idProveedorSala . "";
				$idProveedorAnestesiologo == "" ? $idProveedorAnestesiologo = "NULL" : $idProveedorAnestesiologo = "" . $idProveedorAnestesiologo . "";
				$idProveedorEspecialista == "" ? $idProveedorEspecialista = "NULL" : $idProveedorEspecialista = "" . $idProveedorEspecialista . "";
			
				$observacion == "" ? $observacion = "NULL" : $observacion = "'" . $observacion . "'";
				$fechaVenc == "" ? $fechaVenc = "NULL" : $fechaVenc = "'" . $fechaVenc . "'";
				$usuarioCrea == "" ? $usuarioCrea = "NULL" : $usuarioCrea = "" . $usuarioCrea . "";
				$tipoProcedimiento == "" ? $tipoProcedimiento = "NULL" : $tipoProcedimiento = "" . $tipoProcedimiento . "";
				$codProcedimiento == "" ? $codProcedimiento = "NULL" : $codProcedimiento = "'" . $codProcedimiento . "'";
				$idPaquete == "" ? $idPaquete = "NULL" : $idPaquete = "" . $idPaquete . "";
				$lugarAutorizacion == "" ? $lugarAutorizacion = "NULL" : $lugarAutorizacion = "" . $lugarAutorizacion . "";
				$ojo == "" ? $ojo = "NULL" : $ojo = "" . $ojo . "";
				
				$sql = "CALL pa_autorizar_procedimiento(".$tipoAuto.", ".$idOrdenMedica.", ".$idOrdenMedicaDet.", ".$ciex.", ".$idProveedorSala.",  ".$idProveedorAnestesiologo.", 
				".$idProveedorEspecialista.", ".$observacion.", ".$fechaVenc.", ".$usuarioCrea.", ".$tipoProcedimiento.", ".$codProcedimiento.", ".$idPaquete.", ".$lugarAutorizacion.", ".$ojo.", @id)";
				echo($sql);
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado = $arrResultado["@id"];
				
				return $resultado;
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function renovarAutorizacion($cantidad_meses, $id_ordern_medica){
			try {
			$sql = "CALL pa_renovar_autorizaciones_procedimientos(".$cantidad_meses.",".$id_ordern_medica.",@id)";
			$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado = $arrResultado["@id"];
				return $resultado;
			} catch (Exception $e) {
				return array();
			}
			
		}
			
		
		public function getAutorizacionesByOrdenMedica($id_orden) {
			try {
				$sql = "SELECT A.*, MP.nombre_procedimiento, DATE_FORMAT(A.fecha_crea_auto, '%d/%m/%Y %H:%i:%s %p') AS fechaAutorizacion,
						L.nombre_detalle, OM.id_convenio, OM.id_plan, PP.nom_paquete_p
						FROM autorizaciones A
						INNER JOIN ordenes_medicas OM ON OM.id_orden_m=A.id_orden_m
						LEFT JOIN maestro_procedimientos MP ON A.cod_procedimiento=MP.cod_procedimiento
						LEFT JOIN paquetes_procedimientos PP ON PP.id_paquete_p=A.id_paquete_p
						INNER JOIN listas_detalle L ON A.id_lugar_auto=L.id_detalle
						WHERE A.id_orden_m=".$id_orden."
						ORDER BY A.ind_estado_auto DESC, A.fecha_crea_auto DESC";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getAutorizacionById($id_auto) {
			try {
				$sql = "SELECT A.*, MP.nombre_procedimiento, PP.nom_paquete_p, DATE_FORMAT(A.fecha_crea_auto,'%d/%m/%Y %H:%i:%s %p') AS fechaAutorizacion, 
						L.nombre_detalle, CONCAT(CA.codciex,' - ',UPPER(CA.nombre)) AS nom_ciex_autorizacion_aux, OM.id_paciente, 
						IF(OM.id_hc IS NULL, 'Homologada', TC.nombre_tipo_cita) AS servicio, SD.dir_logo_sede_det, SD.dir_sede_det, SD.tel_sede_det, 
						CONCAT(U.nombre_usuario,' ',U.apellido_usuario) AS usuario_auto_aux, DATE_FORMAT(A.fecha_mod_auto, '%d/%m/%Y %H:%i:%s %p') AS fechaModificacion, 
						UMOD.nombre_usuario AS usuarioModifica, CONCAT(COMD.codciex,' - ',UPPER(COMD.nombre)) AS nom_ciex_ordenmedica_det_aux, OM.tipo_orden_medica AS tipo_orden_medica_aux, 
						HC.id_admision AS id_admision_aux, DATE_FORMAT(A.fecha_venc_auto,'%d/%m/%Y') AS fechaVencimiento, PPA.nombre_proveedor_procedimiento AS proveedor_anestesia,
						PPS.nombre_proveedor_procedimiento AS proveedor_sala, PPE.nombre_proveedor_procedimiento AS proveedor_especialista, PPM.nombre_proveedor_procedimiento AS proveedor_materiales,
						 PA.estado_pago 
						FROM autorizaciones A 
						LEFT JOIN maestro_procedimientos MP ON A.cod_procedimiento = MP.cod_procedimiento 
						LEFT JOIN paquetes_procedimientos PP ON A.id_paquete_p = PP.id_paquete_p 
						INNER JOIN listas_detalle L ON A.id_lugar_auto = L.id_detalle 
						LEFT JOIN vi_ciex CA ON A.codciex = CA.codciex 
						INNER JOIN ordenes_medicas OM ON A.id_orden_m = OM.id_orden_m 
						LEFT JOIN ordenes_medicas_det OMD ON OMD.id_orden_m_det = A.id_orden_m_det 
						LEFT JOIN vi_ciex COMD ON OMD.ciex_orden_m_det = COMD.codciex 
						LEFT JOIN historia_clinica HC ON OM.id_hc = HC.id_hc 
						LEFT JOIN tipos_citas TC ON HC.id_tipo_reg = TC.id_tipo_cita 
						LEFT JOIN sedes_det SD ON SD.id_detalle = A.id_lugar_auto 
						INNER JOIN usuarios U ON U.id_usuario = A.usuario_crea_auto 
						LEFT JOIN usuarios UMOD ON UMOD.id_usuario = A.usuario_modifica_auto 
						LEFT JOIN pagos PA ON A.id_pago = PA.id_pago 
						LEFT JOIN proveedores_procedimientos  PPA ON PPA.id_proveedor_procedimiento = A.id_proveedor_anestesiologo
						LEFT JOIN proveedores_procedimientos  PPS ON PPS.id_proveedor_procedimiento = A.id_proveedor_sala
						LEFT JOIN proveedores_procedimientos  PPE ON PPE.id_proveedor_procedimiento = A.id_proveedor_especialista
						LEFT JOIN proveedores_procedimientos  PPM ON PPM.id_proveedor_procedimiento = A.id_proveedor_materiales
						WHERE A.id_auto=".$id_auto."
						ORDER BY A.ind_estado_auto";
				
				//echo $sql;
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getDetAutorizaciones($id_auto){ 
			
			try{
				$sql = "SELECT A.*, SUM(PD.valor) AS valor_liq, SUM(PD.valor_cuota) AS valor_cuota_liq, P.num_factura 
						FROM autorizaciones A 
						INNER JOIN pagos P ON A.id_pago = P.id_pago
						INNER JOIN pagos_detalle PD ON A.id_pago = PD.id_pago	
						WHERE A.id_auto =".$id_auto."	
						ORDER BY id_auto";
			
				//echo($sql);
				return $this->getUnDato($sql);
			}catch (Exception $e){
				return array();
			}
				
		}
		
		public function desautorizarProcedimiento($idAutorizacion, $observacion, $usuario, $idLugar, $idOrdenMedica) {
			try {
				$idAutorizacion == "" ? $idAutorizacion = "NULL" : $idAutorizacion = "" . $idAutorizacion . "";
				$observacion == "" ? $observacion = "NULL" : $observacion = "'" . $observacion . "'";
				$usuario == "" ? $usuario = "NULL" : $usuario = "" . $usuario . "";
				$idLugar == "" ? $idLugar = "NULL" : $idLugar = "" . $idLugar . "";
				$idOrdenMedica == "" ? $idOrdenMedica = "NULL" : $idOrdenMedica = "" . $idOrdenMedica . "";
				$sql = "CALL pa_desautorizar_procedimiento(" . $idAutorizacion . "," . $observacion . "," . $usuario. "," . $idLugar . ",".$idOrdenMedica.", @id)";
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado = $arrResultado["@id"];
				
				return $resultado;
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaAutorizacionesDet($id_auto) {
			try {
				$sql = "SELECT AD.*, A.bilateralidad_auto, A.cant, OM.id_lugar_orden_m
						FROM autorizaciones A
						INNER JOIN autorizaciones_det AD ON A.id_auto=AD.cod_auto
						INNER JOIN ordenes_medicas OM ON A.id_orden_m=OM.id_orden_m
						WHERE A.id_auto=".$id_auto."
						ORDER BY AD.cod_insumo, AD.cod_procedimiento";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getReporteAutorizaciones($fechaInicial, $fechaFinal, $id_convenio, $id_plan) {
			try {
				
				$sql_2="";
				if($fechaInicial<>"" && $fechaFinal<>""){
					$sql_2.=" AND OM.fecha_crea BETWEEN '".$fechaInicial." 00:00:00' AND '".$fechaFinal." 23:59:59'";	
				}
				if($id_convenio<>""){
					$sql_2.=" AND OM.id_convenio=".$id_convenio;
				}
				if($id_plan<>""){
					 $sql_2.=" AND OM.id_plan=".$id_plan;
				}
				$sql = "SELECT PG.id_pago, A.id_auto AS numero_auto, 
							CASE WHEN fecha_renovacion_auto IS NULL THEN DATE(A.fecha_crea_auto) ELSE DATE(fecha_renovacion_auto) END AS fecha_auto,
							DATE_FORMAT(fecha_crea_auto, '%H:%i' ) AS hora_auto, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS usuario_auto,
							CN.nombre_convenio AS convenio, PL.nombre_plan AS plan, 
							CASE WHEN P.status_convenio_paciente=1 THEN 'Activo' WHEN P.status_convenio_paciente=2 THEN 'Inactivo' WHEN P.status_convenio_paciente=3 THEN 'Atencion especial' END AS estado_paciente,
							PS.nombre_proveedor_procedimiento AS proveedor_sala, 
							PA.nombre_proveedor_procedimiento AS proveedor_anestesia,
							PE.nombre_proveedor_procedimiento AS proveedor_especialista,
							IF(A.cod_procedimiento IS NULL, A.id_paquete_p, A.cod_procedimiento) AS codigo_concepto, IF(MAP.nombre_procedimiento IS NULL,PP.nom_paquete_p, MAP.nombre_procedimiento) AS desc_concepto, 
							CASE WHEN ind_estado_auto=1 THEN 'Pago pendiente' WHEN ind_estado_auto=2 THEN 'Con pago asignado' WHEN ind_estado_auto=3 THEN 'Anulada' END AS estado_auto,
							IF(ind_estado_auto=0, 'Sí', 'No') AS cancelada,A.nota_aclaratoria_auto AS motivo_cancela,
							TD.nombre_detalle AS tipo_documento, P.numero_documento AS documento, 
							CONCAT(P.nombre_1, ' ', IF(P.nombre_2 IS NULL, '', P.nombre_2),' ', P.apellido_1, ' ', IF(P.apellido_2 IS NULL, '', P.apellido_2)) AS paciente,
							fu_calcular_edad(P.fecha_nacimiento,DATE(NOW())) AS edad, CASE WHEN sexo=1 THEN 'Femenino' WHEN sexo=2 THEN 'Masculino' ELSE 'No registra' END AS genero,
							CASE WHEN P.rango_paciente=1 THEN 'Rango 1' WHEN P.rango_paciente=2 THEN 'Rango 2' WHEN P.rango_paciente=3 THEN 'Rango 3' WHEN P.rango_paciente=0 THEN 'No aplica' END AS rango_paciente,
							MU.nom_mun AS municipio,   
							CASE WHEN P.tipo_coti_paciente=1 THEN 'Cotizante' WHEN P.tipo_coti_paciente=2 THEN 'Beneficiario' WHEN P.tipo_coti_paciente=3 THEN 'Subsidiado' WHEN P.tipo_coti_paciente=0 THEN 'No aplica' END AS tipo_cotizante,
							CASE WHEN P.exento_pamo_paciente=1 THEN 'No' WHEN P.exento_pamo_paciente=2 THEN 'Sí' END AS exento_cuota, CONCAT(PF.nombre_usuario, ' ', PF.apellido_usuario) AS profesional, 
							DATE(OM.fecha_crea) AS fecha_form, A.observ_auto, 
							CASE WHEN A.ojo_auto=1 THEN 'Ambos ojos' WHEN A.ojo_auto=2 THEN 'Ojo izquierdo' WHEN A.ojo_auto=3 THEN 'Ojo derecho'  ELSE 'No aplica' END AS ojo_auto,
							CCX.nombre AS diagnostico, CASE WHEN A.bilateralidad_auto=1 THEN 'Unilateral' WHEN  A.bilateralidad_auto=2 THEN 'Bilateral' ELSE 'No aplica' END AS lateralidad, SUM(PDT.valor)/SQRT(COUNT(PDT.cantidad)) AS valor,
							PG.num_factura, PG.num_pedido, 
							CASE WHEN PG.estado_pago=1 THEN 'Pendiente' WHEN PG.estado_pago=2 THEN 'Pagado' WHEN PG.estado_pago=3 THEN 'Anulado' END AS estado_pago  
							FROM autorizaciones A
							INNER JOIN autorizaciones_det AD ON A.id_auto=AD.cod_auto
							INNER JOIN ordenes_medicas OM ON A.id_orden_m=OM.id_orden_m
							INNER JOIN convenios CN ON CN.id_convenio = OM.id_convenio
							INNER JOIN planes PL ON PL.id_plan = OM.id_plan
							INNER JOIN usuarios U ON U.id_usuario = A.usuario_crea_auto
							INNER JOIN pacientes P ON P.id_paciente = OM.id_paciente
							INNER JOIN listas_detalle TD ON TD.id_detalle = P.id_tipo_documento 
							LEFT JOIN historia_clinica HC ON HC.id_hc = OM.id_hc
							LEFT JOIN usuarios PF ON PF.id_usuario = HC.id_usuario_crea
							LEFT JOIN ciex_consolidado CCX ON CCX.codciex = A.codciex
							LEFT JOIN pagos PG ON PG.id_pago = A.id_pago
							LEFT JOIN pagos_detalle PDT ON PDT.id_pago = PG.id_pago
							LEFT JOIN proveedores_procedimientos PS ON PS.id_proveedor_procedimiento = A.id_proveedor_sala
							LEFT JOIN proveedores_procedimientos PA ON PA.id_proveedor_procedimiento = A.id_proveedor_anestesiologo
							LEFT JOIN proveedores_procedimientos PE ON PE.id_proveedor_procedimiento = A.id_proveedor_especialista
							LEFT JOIN maestro_procedimientos MAP ON MAP.cod_procedimiento = A.cod_procedimiento
							LEFT JOIN paquetes_procedimientos PP ON PP.id_paquete_p = A.id_paquete_p
							INNER JOIN municipios MU ON MU.cod_mun_dane = P.cod_mun  
							WHERE OM.id_hc IS NOT NULL ".$sql_2." GROUP BY A.id_auto
							UNION 
							SELECT PG.id_pago,A.id_auto AS numero_auto, 
							CASE WHEN fecha_renovacion_auto IS NULL THEN DATE(A.fecha_crea_auto) ELSE DATE(fecha_renovacion_auto) END AS fecha_auto,
							DATE_FORMAT(fecha_crea_auto, '%H:%i' ) AS hora_auto, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS usuario_auto,
							CN.nombre_convenio AS convenio, PL.nombre_plan AS plan, 
							CASE WHEN P.status_convenio_paciente=1 THEN 'Activo' WHEN P.status_convenio_paciente=2 THEN 'Inactivo' WHEN P.status_convenio_paciente=3 THEN 'Atencion especial' END AS estado_paciente,
							PS.nombre_proveedor_procedimiento AS proveedor_sala,
							PA.nombre_proveedor_procedimiento AS proveedor_anestesia,
							PE.nombre_proveedor_procedimiento AS proveedor_especialista,
							IF(A.cod_procedimiento IS NULL, A.id_paquete_p, A.cod_procedimiento) AS codigo_concepto, IF(MAP.nombre_procedimiento IS NULL,PP.nom_paquete_p, MAP.nombre_procedimiento) AS desc_concepto, 
							CASE WHEN ind_estado_auto=1 THEN 'Pago pendiente'  WHEN ind_estado_auto=2 THEN 'Con pago asignado' WHEN ind_estado_auto=3 THEN 'Anulada' END AS estado_auto,
							IF(ind_estado_auto=0, 'Sí', 'No') AS cancelada,A.nota_aclaratoria_auto AS motivo_cancela, 
							TD.nombre_detalle AS tipo_documento, P.numero_documento AS documento, 
							CONCAT(P.nombre_1, ' ', IF(P.nombre_2 IS NULL, '', P.nombre_2),' ', P.apellido_1, ' ', IF(P.apellido_2 IS NULL, '', P.apellido_2)) AS paciente,
							fu_calcular_edad(P.fecha_nacimiento,DATE(NOW())) AS edad, CASE WHEN sexo=1 THEN 'Femenino' WHEN sexo=2 THEN 'Masculino' ELSE 'No registra' END AS genero,
							CASE WHEN P.rango_paciente=1 THEN 'Rango 1' WHEN P.rango_paciente=2 THEN 'Rango 2' WHEN P.rango_paciente=3 THEN 'Rango 3' WHEN P.rango_paciente=0 THEN 'No aplica' END AS rango_paciente,
							MU.nom_mun AS municipio,  
							CASE WHEN P.tipo_coti_paciente=1 THEN 'Cotizante' WHEN P.tipo_coti_paciente=2 THEN 'Beneficiario' WHEN P.tipo_coti_paciente=3 THEN 'Subsidiado' WHEN P.tipo_coti_paciente=0 THEN 'No aplica' END AS tipo_cotizante,
							CASE WHEN P.exento_pamo_paciente=1 THEN 'No' WHEN P.exento_pamo_paciente=2 THEN 'Sí' END AS exento_cuota, 
							OM.medico_homologacion AS profesional, 
							DATE(OM.fecha_crea) AS fecha_form, A.observ_auto, 
							CASE WHEN A.ojo_auto=1 THEN 'Ambos ojos' WHEN A.ojo_auto=2 THEN 'Ojo izquierdo' WHEN A.ojo_auto=3 THEN 'Ojo derecho'  ELSE 'No aplica' END AS ojo_auto,
							CCX.nombre AS diagnostico, CASE WHEN A.bilateralidad_auto=1 THEN 'Unilateral' WHEN  A.bilateralidad_auto=2 THEN 'Bilateral' ELSE 'No aplica' END AS lateralidad, SUM(PDT.valor)/SQRT(COUNT(PDT.cantidad)) AS valor,
							PG.num_factura, PG.num_pedido, 
							CASE WHEN PG.estado_pago=1 THEN 'Pendiente' WHEN PG.estado_pago=2 THEN 'Pagado' WHEN PG.estado_pago=3 THEN 'Anulado' END AS estado_pago  
							FROM autorizaciones A
							INNER JOIN autorizaciones_det AD ON A.id_auto=AD.cod_auto
							INNER JOIN ordenes_medicas OM ON A.id_orden_m=OM.id_orden_m
							INNER JOIN convenios CN ON CN.id_convenio = OM.id_convenio
							INNER JOIN planes PL ON PL.id_plan = OM.id_plan
							INNER JOIN usuarios U ON U.id_usuario = A.usuario_crea_auto
							INNER JOIN pacientes P ON P.id_paciente = OM.id_paciente
							INNER JOIN listas_detalle TD ON TD.id_detalle = P.id_tipo_documento 
							LEFT  JOIN historia_clinica HC ON HC.id_hc = OM.id_hc
							LEFT JOIN usuarios PF ON PF.id_usuario = HC.id_usuario_crea
							INNER JOIN ciex_consolidado CCX ON CCX.codciex = A.codciex
							LEFT JOIN pagos PG ON PG.id_pago = A.id_pago
							LEFT JOIN pagos_detalle PDT ON PDT.id_pago = PG.id_pago
							LEFT JOIN proveedores_procedimientos PS ON PS.id_proveedor_procedimiento = A.id_proveedor_sala
							LEFT JOIN proveedores_procedimientos PA ON PA.id_proveedor_procedimiento = A.id_proveedor_anestesiologo
							LEFT JOIN proveedores_procedimientos PE ON PE.id_proveedor_procedimiento = A.id_proveedor_especialista
							LEFT JOIN maestro_procedimientos MAP ON MAP.cod_procedimiento = A.cod_procedimiento
							LEFT JOIN paquetes_procedimientos PP ON PP.id_paquete_p = A.id_paquete_p
							INNER JOIN municipios MU ON MU.cod_mun_dane = P.cod_mun  
							WHERE OM.id_hc IS NULL AND OM.medico_homologacion IS NOT NULL ".$sql_2." GROUP BY A.id_auto";
							
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function actualizarAutorizacion($id_auto,$id_prov_sala, $id_prov_anes, $id_prov_esp, $id_prov_ma, $id_ojo) {
			try {
				$id_prov_sala == "" ? $id_prov_sala = "NULL" : $id_prov_sala = "" . $id_prov_sala . "";
				$id_prov_anes == "" ? $id_prov_anes = "NULL" : $id_prov_anes = "'" . $id_prov_anes . "'";
				$id_prov_esp == "" ? $id_prov_esp = "NULL" : $id_prov_esp = "" . $id_prov_esp . "";
				$id_prov_mat == "" ? $id_prov_mat = "NULL" : $id_prov_mat = "" . $id_prov_mat . "";
				$id_ojo == "" ? $id_ojo = "NULL" : $id_ojo = "" . $id_ojo . "";
			
				$sql = "CALL pa_actualizar_autorizaciones(" . $id_auto . "," . $id_prov_sala . "," . $id_prov_anes . "," . $id_prov_esp. "
				," . $id_prov_mat . "," . $id_ojo . ", @id)";
			
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado = $arrResultado["@id"];
				
				return $resultado;
			} catch (Exception $e) {
				return array();
			}
		}
		
		
		
	}
?>
