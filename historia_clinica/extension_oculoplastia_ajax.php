<?php @session_start();
	/*
	 * Pagina para crear registros extendidos de oculoplastia
	 * Autor: Feisar Moreno - 22/03/2017
	 */
 	header("Content-Type: text/xml; charset=UTF-8");
	
	require_once("../db/DbConsultasOculoplastia.php");
	require_once("../funciones/Utilidades.php");
	
	function guardar_consulta_oculoplastia($arr_post, $id_usuario) {
		$dbConsultasOculoplastia = new DbConsultasOculoplastia();
		$utilidades = new Utilidades();
		
		@$id_hc = $utilidades->str_decode($arr_post["id_hc"]);
		@$exoftalmometria_od = $utilidades->str_decode($arr_post["exoftalmometria_od"]);
		@$exoftalmometria_base = $utilidades->str_decode($arr_post["exoftalmometria_base"]);
		@$exoftalmometria_oi = $utilidades->str_decode($arr_post["exoftalmometria_oi"]);
		@$observ_orbita = $utilidades->str_decode($arr_post["observ_orbita"]);
		@$observ_cejas = $utilidades->str_decode($arr_post["observ_cejas"]);
		@$fme_od = $utilidades->str_decode($arr_post["fme_od"]);
		@$fme_oi = $utilidades->str_decode($arr_post["fme_oi"]);
		@$dmr_od = $utilidades->str_decode($arr_post["dmr_od"]);
		@$dmr_oi = $utilidades->str_decode($arr_post["dmr_oi"]);
		@$fen_od = $utilidades->str_decode($arr_post["fen_od"]);
		@$fen_oi = $utilidades->str_decode($arr_post["fen_oi"]);
		@$observ_parpados = $utilidades->str_decode($arr_post["observ_parpados"]);
		@$observ_pestanas = $utilidades->str_decode($arr_post["observ_pestanas"]);
		@$gm_expresibilidad_od = $utilidades->str_decode($arr_post["gm_expresibilidad_od"]);
		@$gm_expresibilidad_oi = $utilidades->str_decode($arr_post["gm_expresibilidad_oi"]);
		@$gm_calidad_expr_od = $utilidades->str_decode($arr_post["gm_calidad_expr_od"]);
		@$gm_calidad_expr_oi = $utilidades->str_decode($arr_post["gm_calidad_expr_oi"]);
		@$observ_glandulas_meib = $utilidades->str_decode($arr_post["observ_glandulas_meib"]);
		@$prueba_irrigacion_od = $utilidades->str_decode($arr_post["prueba_irrigacion_od"]);
		@$prueba_irrigacion_oi = $utilidades->str_decode($arr_post["prueba_irrigacion_oi"]);
		@$observ_via_lagrimal = $utilidades->str_decode($arr_post["observ_via_lagrimal"]);
		
		@$cant_oculoplastia_antec = intval($arr_post["cant_oculoplastia_antec"], 10);
		$arr_oculoplastia_antec = array();
		for ($i = 0; $i < $cant_oculoplastia_antec; $i++) {
			@$arr_oculoplastia_antec[$i]["id_antec_ocp"] = $utilidades->str_decode($arr_post["id_antec_ocp_".$i]);
			@$arr_oculoplastia_antec[$i]["texto_antec_ocp"] = $utilidades->str_decode($arr_post["texto_antec_ocp_".$i]);
			@$arr_oculoplastia_antec[$i]["fecha_antec_ocp"] = $utilidades->str_decode($arr_post["fecha_antec_ocp_".$i]);
		}
		
		@$cant_oculoplastia_compl = intval($arr_post["cant_oculoplastia_compl"], 10);
		$arr_oculoplastia_compl = array();
		for ($i = 0; $i < $cant_oculoplastia_compl; $i++) {
			@$arr_oculoplastia_compl[$i]["id_compl_ocp"] = $utilidades->str_decode($arr_post["id_compl_ocp_".$i]);
			@$arr_oculoplastia_compl[$i]["ind_compl_ocp"] = $utilidades->str_decode($arr_post["ind_compl_ocp_".$i]);
		}
		
		$resultado = $dbConsultasOculoplastia->crearEditarConsultaOculoplastia($id_hc, $exoftalmometria_od, $exoftalmometria_base, $exoftalmometria_oi, $observ_orbita,
				$observ_cejas, $fme_od, $fme_oi, $dmr_od, $dmr_oi, $fen_od, $fen_oi, $observ_parpados, $observ_pestanas, $gm_expresibilidad_od,
				$gm_expresibilidad_oi, $gm_calidad_expr_od, $gm_calidad_expr_oi, $observ_glandulas_meib, $prueba_irrigacion_od,
				$prueba_irrigacion_oi, $observ_via_lagrimal, $arr_oculoplastia_antec, $arr_oculoplastia_compl, $id_usuario);
		
		return $resultado;	
	}
?>
