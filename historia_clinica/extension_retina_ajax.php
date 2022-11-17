<?php session_start();
	/*
	 * Pagina para crear registros extendidos de retina
	 * Autor: Feisar Moreno - 15/03/2017
	 */
 	header("Content-Type: text/xml; charset=UTF-8");
	
	require_once("../db/DbConsultasOftalmologiaRetina.php");
	require_once("../funciones/Utilidades.php");
	
	function guardar_consulta_oftalmologia_retina($arr_post, $id_usuario) {
		$dbConsultasOftalmologiaRetina = new DbConsultasOftalmologiaRetina();
		$utilidades = new Utilidades();
		
		@$id_hc = $utilidades->str_decode($arr_post["id_hc"]);
		@$ind_laser_ret = $utilidades->str_decode($arr_post["ind_laser_ret"]);
		@$ind_intravitreas_ret = $utilidades->str_decode($arr_post["ind_intravitreas_ret"]);
		@$cant_intr_od_ret = $utilidades->str_decode($arr_post["cant_intr_od_ret"]);
		@$cant_intr_oi_ret = $utilidades->str_decode($arr_post["cant_intr_oi_ret"]);
		@$ind_cx_retina = $utilidades->str_decode($arr_post["ind_cx_retina"]);
		@$cant_retina_cx = intval($arr_post["cant_retina_cx"]);
		
		$arr_cx_ret = array();
		for ($i = 0; $i < $cant_retina_cx; $i++) {
			@$arr_cx_ret[$i]["texto_cx"] = $utilidades->str_decode($arr_post["texto_cx_ret_".$i]);
			@$arr_cx_ret[$i]["fecha_cx"] = $utilidades->str_decode($arr_post["fecha_cx_ret_".$i]);
		}
		
		$resultado = $dbConsultasOftalmologiaRetina->crearEditarConsultaOftalmologiaRetina($id_hc, $ind_laser_ret,
				$ind_intravitreas_ret, $cant_intr_od_ret, $cant_intr_oi_ret, $ind_cx_retina, $arr_cx_ret, $id_usuario);
		
		return $resultado;	
	}
?>
