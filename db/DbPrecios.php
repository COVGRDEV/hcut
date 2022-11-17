<?php

require_once("DbConexion.php");

class DbPrecios extends DbConexion {
    /*
     * Esta funcion obtiene los precios para el formulario admisiones/estado.php
     */
    public function getPrecios($id_tipo_cita, $id_plan, $cant_examenes = 0, $arr_examenes = array(), $id_admision_paciente = 0) {
        try {
            if ($id_admision_paciente > 0) {
                $sql = "SELECT '99' AS orden, MP.cod_procedimiento, MP.nombre_procedimiento, pd.valor, 
						pd.valor_cuota, LP.id_precio, LP.cod_procedimiento, LP.cod_medicamento, 
						LP.cod_insumo, p.ind_tipo_pago, pd.tipo_precio, pd.tipo_bilateral, 0 AS id_examen,
						pd.cantidad, pd.num_autorizacion
						FROM  maestro_procedimientos MP 
						INNER JOIN listas_precios LP ON LP.cod_procedimiento=MP.cod_procedimiento 
						INNER JOIN pagos_detalle pd ON pd.cod_procedimiento = MP.cod_procedimiento
						INNER JOIN pagos p ON p.id_admision = pd.id_admision
						WHERE LP.id_plan=".$id_plan."
						AND pd.tipo_bilateral = LP.tipo_bilateral
						AND LP.fecha_ini<=DATE(NOW())
						AND IFNULL(LP.fecha_fin, DATE(NOW()))>=DATE(NOW())
						AND p.id_admision=".$id_admision_paciente." ";
            } else {
                $sql = "SELECT TCD.orden, MP.cod_procedimiento, MP.nombre_procedimiento, LP.valor, LP.valor_cuota, LP.id_precio,
						LP.cod_procedimiento, LP.cod_medicamento, LP.cod_insumo, P.ind_tipo_pago, LP.tipo_precio, LP.tipo_bilateral,
						0 AS id_examen, 1 AS cantidad, NULL AS id_pago, NULL AS id_convenio, NULL AS id_plan, NULL AS num_autorizacion
						FROM tipos_citas_det TCD
						INNER JOIN maestro_procedimientos MP ON MP.cod_procedimiento=TCD.cod_procedimiento
						INNER JOIN listas_precios LP ON LP.cod_procedimiento=MP.cod_procedimiento
						INNER JOIN planes P ON P.id_plan=LP.id_plan
						WHERE TCD.id_tipo_cita=".$id_tipo_cita."
						AND LP.id_plan=".$id_plan."
						AND LP.fecha_ini<=DATE(NOW())
						AND IFNULL(LP.fecha_fin, DATE(NOW()))>=DATE(NOW()) ";

                if ($cant_examenes > 0) {
                    $cadena_examenes = "";
                    for ($i = 0; $i < $cant_examenes; $i++) {
                        if ($cadena_examenes != "") {
                            $cadena_examenes .= ", ";
                        }
                        $cadena_examenes .= $arr_examenes[$i];
                    }

                    $sql .= "UNION ALL
							 SELECT 99 AS orden, MP.cod_procedimiento, MP.nombre_procedimiento, LP.valor, LP.valor_cuota, LP.id_precio,
							 LP.cod_procedimiento, LP.cod_medicamento, LP.cod_insumo, P.ind_tipo_pago, LP.tipo_precio, LP.tipo_bilateral,
							 ME.id_examen, 1 AS cantidad, NULL AS id_pago, NULL AS id_convenio, NULL AS id_plan, NULL AS num_autorizacion
							 FROM maestro_examenes ME
							 INNER JOIN maestro_procedimientos MP ON ME.cod_procedimiento=MP.cod_procedimiento
							 INNER JOIN listas_precios LP ON ME.cod_procedimiento=LP.cod_procedimiento
							 INNER JOIN planes P ON P.id_plan=LP.id_plan
							 WHERE ME.id_examen IN (".$cadena_examenes.")
							 AND LP.id_plan=".$id_plan."
							 AND LP.fecha_ini<=DATE(NOW())
							 AND IFNULL(LP.fecha_fin, DATE(NOW()))>=DATE(NOW()) ";
                }
            }

            $sql .= "ORDER BY orden, nombre_procedimiento, tipo_bilateral DESC";
            //echo($sql);

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getTodosLosPrecios($id_plan, $parametro, $ind_activo = "") {
        try {
            $parametro = str_replace(" ", "%", $parametro);   
            $sql = "SELECT LP.*, MP.nombre_procedimiento, P.ind_tipo_pago
					FROM listas_precios LP
					INNER JOIN maestro_procedimientos MP ON LP.cod_procedimiento=MP.cod_procedimiento
					INNER JOIN planes P ON LP.id_plan=P.id_plan
					WHERE LP.id_plan=".$id_plan."
					AND LP.fecha_ini<=DATE(NOW())
					AND IFNULL(LP.fecha_fin, DATE(NOW()))>=DATE(NOW()) ";
			if ($ind_activo != "") {
				$sql .= "AND MP.ind_activo=".$ind_activo." ";
			}
			$sql .= "AND (LP.cod_procedimiento LIKE '%".$parametro."%'
					OR MP.nombre_procedimiento LIKE '%".$parametro."%')
					
					UNION ALL
					
					SELECT LP.*, CONCAT(MM.nombre_comercial, ' (', MM.nombre_generico, ') - ', MM.presentacion), P.ind_tipo_pago
					FROM listas_precios LP
					INNER JOIN maestro_medicamentos MM ON LP.cod_medicamento=MM.cod_medicamento
					INNER JOIN planes P ON LP.id_plan=P.id_plan
					WHERE LP.id_plan=".$id_plan."
					AND LP.fecha_ini<=DATE(NOW())
					AND IFNULL(LP.fecha_fin, DATE(NOW()))>=DATE(NOW())
					AND (LP.cod_medicamento LIKE '%".$parametro."%'
					OR MM.nombre_generico LIKE '%".$parametro."%'
					OR MM.nombre_comercial LIKE '%".$parametro."%')
					
					UNION ALL
					
					SELECT LP.*, MI.nombre_insumo, P.ind_tipo_pago
					FROM listas_precios LP
					INNER JOIN maestro_insumos MI ON LP.cod_insumo=MI.cod_insumo
					INNER JOIN planes P ON LP.id_plan=P.id_plan
					WHERE LP.id_plan=".$id_plan."
					AND LP.fecha_ini<=DATE(NOW())
					AND IFNULL(LP.fecha_fin, DATE(NOW()))>=DATE(NOW())
					AND (LP.cod_insumo LIKE '%".$parametro."%'
					OR MI.nombre_insumo LIKE '%".$parametro."%')
					
					ORDER BY nombre_procedimiento";
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaPreciosOtrosPlanes($id_plan, $parametro, $ind_activo = "") {
        try {
            $parametro = str_replace(" ", "%", $parametro);   
            $sql = "SELECT DISTINCT NULL AS id_precio, P.id_plan, MP.cod_procedimiento, NULL AS cod_medicamento, NULL AS cod_insumo,
					NULL AS fecha_ini, NULL AS fecha_fin, 0 AS valor, 0 AS valor_cuota, 'P' AS tipo_precio,
					TB.tipo_bilateral, MP.nombre_procedimiento, P.ind_tipo_pago
					FROM maestro_procedimientos MP
					INNER JOIN listas_precios LP ON MP.cod_procedimiento=LP.cod_procedimiento
					CROSS JOIN planes P
					INNER JOIN (
						SELECT 'P' AS tipo_procedimiento, 0 AS tipo_bilateral
						UNION ALL
						SELECT 'P' AS tipo_procedimiento, 1 AS tipo_bilateral
						UNION ALL
						SELECT 'P' AS tipo_procedimiento, 2 AS tipo_bilateral
						UNION ALL
						SELECT 'C' AS tipo_procedimiento, 0 AS tipo_bilateral
					) TB ON MP.tipo_procedimiento=TB.tipo_procedimiento
					WHERE LP.fecha_ini<=DATE(NOW())
					AND IFNULL(LP.fecha_fin, DATE(NOW()))>=DATE(NOW()) ";
			if ($ind_activo != "") {
				$sql .= "AND MP.ind_activo=".$ind_activo." ";
			}
			$sql .= "AND (MP.cod_procedimiento LIKE '%".$parametro."%'
					OR MP.nombre_procedimiento LIKE '%".$parametro."%')
					AND P.id_plan=".$id_plan."
					
					UNION ALL
					
					SELECT DISTINCT NULL AS id_precio, P.id_plan, NULL, MM.cod_medicamento, NULL, NULL, NULL,
					0 AS valor, 0 AS valor_cuota, 'M' AS tipo_precio,
					0 AS tipo_bilateral, CONCAT(MM.nombre_comercial, ' (', MM.nombre_generico, ') - ', MM.presentacion), P.ind_tipo_pago
					FROM maestro_medicamentos MM
					INNER JOIN listas_precios LP ON MM.cod_medicamento=LP.cod_medicamento
					CROSS JOIN planes P
					WHERE LP.fecha_ini<=DATE(NOW())
					AND IFNULL(LP.fecha_fin, DATE(NOW()))>=DATE(NOW())
					AND (MM.cod_medicamento LIKE '%".$parametro."%'
					OR MM.nombre_generico LIKE '%".$parametro."%'
					OR MM.nombre_comercial LIKE '%".$parametro."%')
					AND P.id_plan=".$id_plan."
					
					UNION ALL
					
					SELECT DISTINCT NULL AS id_precio, P.id_plan, NULL, NULL, MI.cod_insumo, NULL, NULL,
					0 AS valor, 0 AS valor_cuota, 'I' AS tipo_precio,
					TB.tipo_bilateral, MI.nombre_insumo, P.ind_tipo_pago
					FROM maestro_insumos MI
					INNER JOIN listas_precios LP ON MI.cod_insumo=LP.cod_insumo
					CROSS JOIN planes P
					CROSS JOIN (
						SELECT 0 AS tipo_bilateral
						UNION ALL
						SELECT 1 AS tipo_bilateral
						UNION ALL
						SELECT 2 AS tipo_bilateral
					) TB
					WHERE LP.fecha_ini<=DATE(NOW())
					AND IFNULL(LP.fecha_fin, DATE(NOW()))>=DATE(NOW())
					AND (MI.cod_insumo LIKE '%".$parametro."%'
					OR MI.nombre_insumo LIKE '%".$parametro."%')
					AND P.id_plan=".$id_plan."
					
					ORDER BY nombre_procedimiento";
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /*
     * Esta funcion obtiene los precios para el formulario admisiones/estado.php
     */
    public function getPreciosAdmision($id_admision) {
        try {
            $sql = "SELECT P.id_pago, P.id_admision, '99' AS orden, MP.cod_procedimiento, MP.nombre_procedimiento, PD.valor, PD.valor_cuota,
					LP.id_precio, MP.cod_procedimiento, LP.cod_medicamento, LP.cod_insumo, P.ind_tipo_pago, PD.tipo_precio, PD.tipo_bilateral,
					0 AS id_examen, PD.cantidad, P.id_convenio, P.id_plan, PD.num_autorizacion
					FROM pagos P
					INNER JOIN pagos_detalle PD ON P.id_pago=PD.id_pago
					INNER JOIN maestro_procedimientos MP ON PD.cod_procedimiento=MP.cod_procedimiento
					LEFT JOIN listas_precios LP ON PD.cod_procedimiento=LP.cod_procedimiento AND PD.tipo_bilateral=LP.tipo_bilateral AND P.id_plan=LP.id_plan 
					AND LP.fecha_ini<=CURDATE() AND IFNULL(LP.fecha_fin, CURDATE())>=CURDATE()
					WHERE P.id_admision=".$id_admision."
					AND P.estado_pago=1
					
					UNION ALL
					
					SELECT P.id_pago, P.id_admision, '99' AS orden, MM.cod_medicamento,
					CONCAT(MM.nombre_comercial, ' (', MM.nombre_generico, ') - ', MM.presentacion), PD.valor, PD.valor_cuota,
					LP.id_precio, LP.cod_procedimiento, MM.cod_medicamento, LP.cod_insumo, P.ind_tipo_pago, PD.tipo_precio, PD.tipo_bilateral,
					0 AS id_examen, PD.cantidad, P.id_convenio, P.id_plan, PD.num_autorizacion
					FROM pagos P
					INNER JOIN pagos_detalle PD ON P.id_pago=PD.id_pago
					INNER JOIN maestro_medicamentos MM ON PD.cod_medicamento=MM.cod_medicamento
					LEFT JOIN listas_precios LP ON PD.cod_medicamento=LP.cod_medicamento AND PD.tipo_bilateral=LP.tipo_bilateral AND P.id_plan=LP.id_plan
					AND LP.fecha_ini<=CURDATE() AND IFNULL(LP.fecha_fin, CURDATE())>=CURDATE()
					WHERE P.id_admision=".$id_admision."
					AND P.estado_pago=1
					
					UNION ALL
					
					SELECT P.id_pago, P.id_admision, '99' AS orden, MI.cod_insumo, MI.nombre_insumo, PD.valor, PD.valor_cuota,
					LP.id_precio, LP.cod_procedimiento, LP.cod_medicamento, MI.cod_insumo, P.ind_tipo_pago, PD.tipo_precio, PD.tipo_bilateral,
					0 AS id_examen, PD.cantidad, P.id_convenio, P.id_plan, PD.num_autorizacion
					FROM pagos P
					INNER JOIN pagos_detalle PD ON P.id_pago=PD.id_pago
					INNER JOIN maestro_insumos MI ON PD.cod_insumo=MI.cod_insumo
					LEFT JOIN listas_precios LP ON PD.cod_insumo=LP.cod_insumo AND PD.tipo_bilateral=LP.tipo_bilateral AND P.id_plan=LP.id_plan
					AND LP.fecha_ini<=CURDATE() AND IFNULL(LP.fecha_fin, CURDATE())>=CURDATE()
					WHERE P.id_admision=".$id_admision."
					AND P.estado_pago=1
					
					ORDER BY id_pago, nombre_procedimiento, tipo_bilateral DESC";
            //echo($sql);

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaProductosBusq($parametro, $limite) {
        try {
            $parametro = str_replace(" ", "%", $parametro);
            $sql = "SELECT 'P' AS tipo_elemento, MP.cod_procedimiento, NULL AS cod_medicamento, NULL AS cod_insumo, MP.nombre_procedimiento AS nombre_producto, NULL AS id_tipo_insumo_p
					FROM maestro_procedimientos MP
					WHERE MP.cod_procedimiento LIKE '%".$parametro."%'
					OR MP.nombre_procedimiento LIKE '%".$parametro."%'
					
					UNION ALL
					
					SELECT 'M', NULL, MM.cod_medicamento, NULL, CONCAT(MM.nombre_comercial, ' (', MM.nombre_generico, ') - ', MM.presentacion), NULL
					FROM maestro_medicamentos MM
					WHERE MM.cod_medicamento LIKE '%".$parametro."%'
					OR MM.nombre_generico LIKE '%".$parametro."%'
					OR MM.nombre_comercial LIKE '%".$parametro."%'
					
					UNION ALL
					
					SELECT 'I', NULL, NULL, MI.cod_insumo, MI.nombre_insumo, MI.id_tipo_insumo_p
					FROM maestro_insumos MI
					WHERE MI.cod_insumo LIKE '%".$parametro."%'
					OR MI.nombre_insumo LIKE '%".$parametro."%'
					
					ORDER BY nombre_producto";
            if ($limite > 0) {
                $sql .= " LIMIT ".$limite;
            }

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
    public function importarISS2001($id_plan, $usuarioCrea) {
        try {
            $id_plan == "NULL" ? $id_plan = "NULL" : $id_plan = "" . $id_plan . "";
            $usuarioCrea == "NULL" ? $usuarioCrea = "NULL" : $usuarioCrea = "" . $usuarioCrea . "";

            $sql = "CALL pa_importar_iss2001($id_plan,$usuarioCrea, @id)";
            //echo($sql);

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $resultado_out = $arrResultado["@id"];

            return $resultado_out;
        } catch (Exception $e) {
            return array();
        }
    }
    
    public function cargarPreciosCSV($idUsuario, $idPlan, $arrDatos, $filas, $tipoPrecio) {
		try {

			$sql = "CALL pa_cargar_precios(".$idUsuario.", ".$idPlan.", '".$arrDatos."', ".$filas.", '".$tipoPrecio."', @id)";
			//echo($sql);
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			$resultado_out = $arrResultado["@id"];
			
			return $resultado_out;
		} catch (Exception $e) {
			return array();
		}
    }
	
	public function consultarDatosListaPrecios($id_plan,$tipo_servicio){
		try{
			
			$sql = "SELECT * FROM listas_precios LP
					LEFT JOIN maestro_procedimientos MP ON MP.cod_procedimiento = LP.cod_procedimiento 
					WHERE LP.id_plan=".$id_plan." AND LP.tipo_precio = '$tipo_servicio' AND LP.fecha_fin 
					IS NULL  AND NOT MP.ind_activo=0 ORDER BY LP.cod_procedimiento";
	
				//echo($sql);
							
			return $this->getDatos($sql);
			
		}catch(Exception $e){
			return array();
		}
	}
	

}
?>
