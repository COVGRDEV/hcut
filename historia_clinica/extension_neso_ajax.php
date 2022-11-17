<?php @session_start();
	/*
	 * Pagina para crear registros extendidos de NESO
	 * Autor: ZJJC - 04/08/2017
	 */
 	header("Content-Type: text/plain; charset=UTF-8");
	
	require_once("../db/DbConsultasNeso.php"); 
	require_once("../db/DbVariables.php");
	require_once("../funciones/Utilidades.php");	
	
	function guardar_consulta_neso($arr_post, $id_usuario) {	
		
		$dbConsultasNeso = new DbConsultasNeso(); 
		$dbVariables = new DbVariables();
		$utilidades = new Utilidades(); 

		@$id_hc = $utilidades->str_decode($arr_post["id_hc"]);
	    @$hdd_id_hc_consulta = $id_hc;
	    @$hdd_id_paciente = $utilidades->str_decode($_POST["neso_id_paciente"]);
		
		@$ind_interferon_od = $arr_post["neso_ind_interferon_od"]; 
		@$cantidad_dosis_od = $arr_post["neso_dosis_od"]; 
		@$img_neso_od = $utilidades->str_decode($_POST["neso_img_od"]); 
		@$ind_recidivante_od = $arr_post["neso_ind_recidivante_od"]; 		
		@$ind_interferon_oi = $arr_post["neso_ind_interferon_oi"]; 
		@$cantidad_dosis_oi = $arr_post["neso_dosis_oi"]; 
		@$img_neso_oi = $utilidades->str_decode($_POST["neso_img_oi"]); 
		@$ind_recidivante_oi = $arr_post["neso_ind_recidivante_oi"]; 
		@$observaciones = $utilidades->str_decode($arr_post["neso_observaciones"]); 
		
		$fecha_hoy = $dbVariables->getAnoMesDia();
		$anio = $fecha_hoy["anio_actual"];
		$mes = $fecha_hoy["mes_actual"];
		$dia = $fecha_hoy["dia_actual"];
		
		$arr_ruta_base = $dbVariables->getVariable(17);
		$ruta_base = $arr_ruta_base["valor_variable"];
		$img_neso_od = $ruta_base."/".$anio."/".$mes."/".$dia."/".$hdd_id_paciente."/".$hdd_id_hc_consulta."_neso_od.png";
		$img_neso_oi = $ruta_base."/".$anio."/".$mes."/".$dia."/".$hdd_id_paciente."/".$hdd_id_hc_consulta."_neso_oi.png";		
		
		// Procesar registros de detalles - lesiones NESO			
		$array_lesiones = array();
		$array_lesiones["idojos"] = $utilidades->str_decode($_POST["array_lesiones_idojo"]);; 
		$array_lesiones["nhusos"] = $utilidades->str_decode($_POST["array_lesiones_nhusos"]); 
		$array_lesiones["husoini"] = $utilidades->str_decode($_POST["array_lesiones_husoini"]); 
		$array_lesiones["mmcornea"] = $utilidades->str_decode($_POST["array_lesiones_mmcornea"]); 			
			
		// Guardar consulta de NESO
		$resultado = $dbConsultasNeso->crearEditarConsultaNeso($id_hc, 
				$ind_interferon_od, $cantidad_dosis_od, $img_neso_od, $ind_recidivante_od, 
				$ind_interferon_oi, $cantidad_dosis_oi, $img_neso_oi, $ind_recidivante_oi, 
				$array_lesiones, $observaciones, $id_usuario); 
		
		return $resultado;
	}
?>