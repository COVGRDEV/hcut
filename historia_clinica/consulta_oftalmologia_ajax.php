<?php session_start();
	/*
	  Pagina para crear consulta de oftalmolgia 
	  Autor: Helio Ruber López - 22/01/2014
	 */
	
 	header("Content-Type: text/xml; charset=UTF-8");

	require_once("../db/DbConsultaOftalmologia.php");
	require_once("../db/DbMenus.php");
    require_once("../db/DbVariables.php");
	require_once("../db/DbAdmision.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbAntecedentes.php");
	
	require_once("antecedentes_funciones.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/Class_Color_Pick.php");
	require_once("../funciones/Class_Atencion_Remision.php");
	require_once("../funciones/Class_Formulacion.php");
	require_once("../funciones/Class_Componente_Rec_Oft.php");
	require_once("../funciones/Class_Tonometrias.php");
	require_once("../funciones/Class_Solic_Procs.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/Utilidades.php");
	
	$dbConsultaOftalmologia = new DbConsultaOftalmologia();
	$dbMenus = new DbMenus();
	$dbVariables = new Dbvariables();
	$dbAdmision = new DbAdmision();
	$dbListas = new DbListas();
	
	$contenido = new ContenidoHtml();
	$contenido->validar_seguridad(1);
	$funciones_persona = new FuncionesPersona();
	$utilidades = new Utilidades();
	
	$combo = new Combo_Box();
	$colorPick = new Color_Pick(array(), 0);
	$class_tonometrias = new Class_Tonometrias();
	
	$opcion = $_POST["opcion"];
	
	function cambiar_mas($texto) {
	   $resultado = str_replace("|mas", "+", $texto);
	   return $resultado;	
	}

	function cambiar_espacio($texto, $cambio) {
		$valor = "";
		if ($texto == "") {
			$valor = $cambio;
		} else {
			$valor = $texto;
		}
		return $valor;
	}
	
	switch ($opcion) {
	case "1": //Guardar Consulta de Oftalmologia
		$id_usuario_crea = $_SESSION["idUsuario"];
		
		$mapa_listas_rec = array();
		
	    @$hdd_id_hc_consulta = $utilidades->str_decode($_POST["hdd_id_hc_consulta"]);
	    @$hdd_id_admision = $utilidades->str_decode($_POST["hdd_id_admision"]);
	    @$hdd_id_paciente = $utilidades->str_decode($_POST["hdd_id_paciente"]);
		@$enfermedad_actual = $utilidades->str_decode($_POST["enfermedad_actual"]);
		@$muscular_balance = $utilidades->str_decode($_POST["muscular_balance"]);
		@$mapa_listas_rec["muscular_balance"]["valor"] = $muscular_balance;
		@$muscular_motilidad = $utilidades->str_decode($_POST["muscular_motilidad"]);
		@$muscular_ppc = $utilidades->str_decode($_POST["muscular_ppc"]);
		
		@$mapa_listas_rec["muscular_balance"]["id_lista"] = $utilidades->str_decode($_POST["id_lista_muscular_balance"]);
		
		@$biomi_orbita_parpados_od = $utilidades->str_decode($_POST["biomi_orbita_parpados_od"]);
		@$mapa_listas_rec["biomi_orbita_parpados_od"]["valor"] = $biomi_orbita_parpados_od;
		@$biomi_sist_lagrimal_od = $utilidades->str_decode($_POST["biomi_sist_lagrimal_od"]);
		@$mapa_listas_rec["biomi_sist_lagrimal_od"]["valor"] = $biomi_sist_lagrimal_od;
		@$biomi_conjuntiva_od = $utilidades->str_decode($_POST["biomi_conjuntiva_od"]);
		@$mapa_listas_rec["biomi_conjuntiva_od"]["valor"] = $biomi_conjuntiva_od;
		@$biomi_cornea_od = $utilidades->str_decode($_POST["biomi_cornea_od"]);
		@$mapa_listas_rec["biomi_cornea_od"]["valor"] = $biomi_cornea_od;
		@$biomi_cam_anterior_od = $utilidades->str_decode($_POST["biomi_cam_anterior_od"]);
		@$mapa_listas_rec["biomi_cam_anterior_od"]["valor"] = $biomi_cam_anterior_od;
		@$biomi_iris_od = $utilidades->str_decode($_POST["biomi_iris_od"]);
		@$mapa_listas_rec["biomi_iris_od"]["valor"] = $biomi_iris_od;
		@$biomi_cristalino_od = $utilidades->str_decode($_POST["biomi_cristalino_od"]);
		@$mapa_listas_rec["biomi_cristalino_od"]["valor"] = $biomi_cristalino_od;
		@$biomi_vanherick_od = $utilidades->str_decode($_POST["biomi_vanherick_od"]);
		@$biomi_orbita_parpados_oi = $utilidades->str_decode($_POST["biomi_orbita_parpados_oi"]);
		@$mapa_listas_rec["biomi_orbita_parpados_oi"]["valor"] = $biomi_orbita_parpados_oi;
		@$biomi_sist_lagrimal_oi = $utilidades->str_decode($_POST["biomi_sist_lagrimal_oi"]);
		@$mapa_listas_rec["biomi_sist_lagrimal_oi"]["valor"] = $biomi_sist_lagrimal_oi;
		@$biomi_conjuntiva_oi = $utilidades->str_decode($_POST["biomi_conjuntiva_oi"]);
		@$mapa_listas_rec["biomi_conjuntiva_oi"]["valor"] = $biomi_conjuntiva_oi;
		@$biomi_cornea_oi = $utilidades->str_decode($_POST["biomi_cornea_oi"]);
		@$mapa_listas_rec["biomi_cornea_oi"]["valor"] = $biomi_cornea_oi;
		@$biomi_cam_anterior_oi = $utilidades->str_decode($_POST["biomi_cam_anterior_oi"]);
		@$mapa_listas_rec["biomi_cam_anterior_oi"]["valor"] = $biomi_cam_anterior_oi;
		@$biomi_iris_oi = $utilidades->str_decode($_POST["biomi_iris_oi"]);
		@$mapa_listas_rec["biomi_iris_oi"]["valor"] = $biomi_iris_oi;
		@$biomi_cristalino_oi = $utilidades->str_decode($_POST["biomi_cristalino_oi"]);
		@$mapa_listas_rec["biomi_cristalino_oi"]["valor"] = $biomi_cristalino_oi;
		@$biomi_vanherick_oi = $utilidades->str_decode($_POST["biomi_vanherick_oi"]);
		
		@$mapa_listas_rec["biomi_orbita_parpados_od"]["id_lista"] = $utilidades->str_decode($_POST["id_lista_biomi_orbita_parpados_od"]);
		@$mapa_listas_rec["biomi_sist_lagrimal_od"]["id_lista"] = $utilidades->str_decode($_POST["id_lista_biomi_sist_lagrimal_od"]);
		@$mapa_listas_rec["biomi_conjuntiva_od"]["id_lista"] = $utilidades->str_decode($_POST["id_lista_biomi_conjuntiva_od"]);
		@$mapa_listas_rec["biomi_cornea_od"]["id_lista"] = $utilidades->str_decode($_POST["id_lista_biomi_cornea_od"]);
		@$mapa_listas_rec["biomi_cam_anterior_od"]["id_lista"] = $utilidades->str_decode($_POST["id_lista_biomi_cam_anterior_od"]);
		@$mapa_listas_rec["biomi_iris_od"]["id_lista"] = $utilidades->str_decode($_POST["id_lista_biomi_iris_od"]);
		@$mapa_listas_rec["biomi_cristalino_od"]["id_lista"] = $utilidades->str_decode($_POST["id_lista_biomi_cristalino_od"]);
		@$mapa_listas_rec["biomi_orbita_parpados_oi"]["id_lista"] = $utilidades->str_decode($_POST["id_lista_biomi_orbita_parpados_oi"]);
		@$mapa_listas_rec["biomi_sist_lagrimal_oi"]["id_lista"] = $utilidades->str_decode($_POST["id_lista_biomi_sist_lagrimal_oi"]);
		@$mapa_listas_rec["biomi_conjuntiva_oi"]["id_lista"] = $utilidades->str_decode($_POST["id_lista_biomi_conjuntiva_oi"]);
		@$mapa_listas_rec["biomi_cornea_oi"]["id_lista"] = $utilidades->str_decode($_POST["id_lista_biomi_cornea_oi"]);
		@$mapa_listas_rec["biomi_cam_anterior_oi"]["id_lista"] = $utilidades->str_decode($_POST["id_lista_biomi_cam_anterior_oi"]);
		@$mapa_listas_rec["biomi_iris_oi"]["id_lista"] = $utilidades->str_decode($_POST["id_lista_biomi_iris_oi"]);
		@$mapa_listas_rec["biomi_cristalino_oi"]["id_lista"] = $utilidades->str_decode($_POST["id_lista_biomi_cristalino_oi"]);
		
		@$goniosco_superior_od = $utilidades->str_decode($_POST["goniosco_superior_od"]);
		@$goniosco_inferior_od = $utilidades->str_decode($_POST["goniosco_inferior_od"]);
		@$goniosco_nasal_od = $utilidades->str_decode($_POST["goniosco_nasal_od"]);
		@$goniosco_temporal_od = $utilidades->str_decode($_POST["goniosco_temporal_od"]);
		@$goniosco_superior_oi = $utilidades->str_decode($_POST["goniosco_superior_oi"]);
		@$goniosco_inferior_oi = $utilidades->str_decode($_POST["goniosco_inferior_oi"]);
		@$goniosco_nasal_oi = $utilidades->str_decode($_POST["goniosco_nasal_oi"]);
		@$goniosco_temporal_oi = $utilidades->str_decode($_POST["goniosco_temporal_oi"]);
		
		@$tonometria_nervio_optico_od = $utilidades->str_decode($_POST["tonometria_nervio_optico_od"]);
		@$mapa_listas_rec["tonometria_nervio_optico_od"]["valor"] = $tonometria_nervio_optico_od;
		@$tonometria_macula_od = $utilidades->str_decode($_POST["tonometria_macula_od"]);
		@$mapa_listas_rec["tonometria_macula_od"]["valor"] = $tonometria_macula_od;
		@$tonometria_periferia_od = $utilidades->str_decode($_POST["tonometria_periferia_od"]);
		@$mapa_listas_rec["tonometria_periferia_od"]["valor"] = $tonometria_periferia_od;
		@$tonometria_vitreo_od = $utilidades->str_decode($_POST["tonometria_vitreo_od"]);
		@$mapa_listas_rec["tonometria_vitreo_od"]["valor"] = $tonometria_vitreo_od;
		@$tonometria_nervio_optico_oi = $utilidades->str_decode($_POST["tonometria_nervio_optico_oi"]);
		@$mapa_listas_rec["tonometria_nervio_optico_oi"]["valor"] = $tonometria_nervio_optico_oi;
		@$tonometria_macula_oi = $utilidades->str_decode($_POST["tonometria_macula_oi"]);
		@$mapa_listas_rec["tonometria_macula_oi"]["valor"] = $tonometria_macula_oi;
		@$tonometria_periferia_oi = $utilidades->str_decode($_POST["tonometria_periferia_oi"]);
		@$mapa_listas_rec["tonometria_periferia_oi"]["valor"] = $tonometria_periferia_oi;
		@$tonometria_vitreo_oi = $utilidades->str_decode($_POST["tonometria_vitreo_oi"]);
		@$mapa_listas_rec["tonometria_vitreo_oi"]["valor"] = $tonometria_vitreo_oi;
		
		@$mapa_listas_rec["tonometria_nervio_optico_od"]["id_lista"] = $utilidades->str_decode($_POST["id_lista_tonometria_nervio_optico_od"]);
		@$mapa_listas_rec["tonometria_macula_od"]["id_lista"] = $utilidades->str_decode($_POST["id_lista_tonometria_macula_od"]);
		@$mapa_listas_rec["tonometria_periferia_od"]["id_lista"] = $utilidades->str_decode($_POST["id_lista_tonometria_periferia_od"]);
		@$mapa_listas_rec["tonometria_vitreo_od"]["id_lista"] = $utilidades->str_decode($_POST["id_lista_tonometria_vitreo_od"]);
		@$mapa_listas_rec["tonometria_nervio_optico_oi"]["id_lista"] = $utilidades->str_decode($_POST["id_lista_tonometria_nervio_optico_oi"]);
		@$mapa_listas_rec["tonometria_macula_oi"]["id_lista"] = $utilidades->str_decode($_POST["id_lista_tonometria_macula_oi"]);
		@$mapa_listas_rec["tonometria_periferia_oi"]["id_lista"] = $utilidades->str_decode($_POST["id_lista_tonometria_periferia_oi"]);
		@$mapa_listas_rec["tonometria_vitreo_oi"]["id_lista"] = $utilidades->str_decode($_POST["id_lista_tonometria_vitreo_oi"]);
		
		@$diagnostico_oftalmo = $utilidades->str_decode($_POST["diagnostico_oftalmo"]);
		@$solicitud_examenes = $utilidades->str_decode($_POST["solicitud_examenes"]);
		@$tratamiento_oftalmo = $utilidades->str_decode($_POST["tratamiento_oftalmo"]);
		@$tipo_guardar = $utilidades->str_decode($_POST["tipo_guardar"]);
		@$img_biomiocroscopia = $utilidades->str_decode($_POST["img_biomiocroscopia"]);
		@$img_tonometria_od = $utilidades->str_decode($_POST["img_tonometria_od"]);
		@$img_tonometria_oi = $utilidades->str_decode($_POST["img_tonometria_oi"]);
		@$observaciones_gonioscopia = $utilidades->str_decode($_POST["observaciones_gonioscopia"]);
		@$medicamentos_oftalmo = $utilidades->str_decode($_POST["medicamentos_oftalmo"]);
		@$nombre_usuario_alt = $utilidades->str_decode($_POST["nombre_usuario_alt"]);
		@$nombre_usuario_preconsulta = $utilidades->str_decode($_POST["nombre_usuario_preconsulta"]);
		@$observaciones_tonometria = $utilidades->str_decode($_POST["observaciones_tonometria"]);
		@$ind_formula_gafas = $utilidades->str_decode($_POST["ind_formula_gafas"]);
		@$ind_eval_muscular = $utilidades->str_decode($_POST["ind_eval_muscular"]);
		
		$fecha_hoy = $dbVariables->getAnoMesDia();
		$anio = $fecha_hoy["anio_actual"];
		$mes = $fecha_hoy["mes_actual"];
		$dia = $fecha_hoy["dia_actual"];
		
		$arr_ruta_base = $dbVariables->getVariable(17);
		$ruta_base = $arr_ruta_base["valor_variable"];
		$img_biomiocroscopia = $ruta_base."/".$anio."/".$mes."/".$dia."/".$hdd_id_paciente."/".$hdd_id_hc_consulta."_biomiocroscopia.png";
		$img_tonometria_od = $ruta_base."/".$anio."/".$mes."/".$dia."/".$hdd_id_paciente."/".$hdd_id_hc_consulta."_tonometriaod.png";
		$img_tonometria_oi = $ruta_base."/".$anio."/".$mes."/".$dia."/".$hdd_id_paciente."/".$hdd_id_hc_consulta."_tonometriaoi.png";
		
		//Diagnósticos
		$cant_ciex = $utilidades->str_decode($_POST["cant_ciex"]);
		$array_diagnosticos = array();
		for ($i = 1; $i <= $cant_ciex; $i++) {
		 	if(isset($_POST["cod_ciex_".$i])){
			 	$ciex_diagnostico = $utilidades->str_decode($_POST["cod_ciex_".$i]);
				$valor_ojos = $utilidades->str_decode($_POST["val_ojos_".$i]);
				$array_diagnosticos[$i][0]=$ciex_diagnostico;
				$array_diagnosticos[$i][1]=$valor_ojos;
			}	
		}
		
        //Antecedentes
        $cant_antecedentes = $_POST["cant_antecedentes"];
        $array_antecedentes = array();
        for ($i = 0; $i < $cant_antecedentes; $i++) {
            $array_antecedentes[$i]["id_antecedentes_medicos"] = $utilidades->str_decode($_POST["id_antecedentes_medicos_".$i]);
            $array_antecedentes[$i]["texto_antecedente"] = $utilidades->str_decode($_POST["texto_antecedente_".$i]);
        }
		
		//Se obtienen los valores de tonometría aplanática
		$array_tonometria = $class_tonometrias->obtener_listado_tonometria_guardar($_POST);
		
		//Se crea un mapa de valores de listas recursivas
		$lista_recursiva_det = $dbListas->getListaDetallesRecCompleto(1);
		$mapa_listas_rec_det = array();
		foreach ($lista_recursiva_det as $valor_rec_det) {
			if (!isset($mapa_listas_rec_det[$valor_rec_det["id_lista"]])) {
				$mapa_listas_rec_det[$valor_rec_det["id_lista"]] = array();
			}
			array_push($mapa_listas_rec_det[$valor_rec_det["id_lista"]], $valor_rec_det);
		}
		
		$componente_rec_oft = new Class_Componente_Rec_Oft();
		
		//Se identifican los valores a guardar de las listas recursivas
		$mapa_hc_listas_rec = array();
		foreach ($mapa_listas_rec as $grupo_registro_aux => $lista_rec_aux) {
			if (isset($mapa_listas_rec_det[$lista_rec_aux["id_lista"]])) {
				if ($lista_rec_aux["valor"] != "") {
					$mapa_hc_listas_rec[$grupo_registro_aux] = $componente_rec_oft->obtener_lista_valores_sel($lista_rec_aux["valor"], $mapa_listas_rec_det[$lista_rec_aux["id_lista"]]);
				} else {
					$mapa_hc_listas_rec[$grupo_registro_aux] = array();
				}
			}
		}
		
		//Se obtienen los colores a guardar
		$cadena_colores = $utilidades->str_decode($_POST["cadena_colores"]);
		
		$arr_cadenas_colores = $colorPick->getListasColores($cadena_colores);
		
		$ind_opt = $dbConsultaOftalmologia->EditarConsultaOftalmologia($hdd_id_hc_consulta, $hdd_id_admision, $enfermedad_actual, $muscular_balance, $muscular_motilidad,
					$muscular_ppc, $biomi_orbita_parpados_od, $biomi_sist_lagrimal_od, $biomi_conjuntiva_od, $biomi_cornea_od, $biomi_cam_anterior_od, $biomi_iris_od,
					$biomi_cristalino_od, $biomi_vanherick_od, $biomi_orbita_parpados_oi, $biomi_sist_lagrimal_oi, $biomi_conjuntiva_oi, $biomi_cornea_oi,
					$biomi_cam_anterior_oi, $biomi_iris_oi, $biomi_cristalino_oi, $biomi_vanherick_oi, $goniosco_superior_od, $goniosco_inferior_od, $goniosco_nasal_od,
					$goniosco_temporal_od, $goniosco_superior_oi, $goniosco_inferior_oi, $goniosco_nasal_oi, $goniosco_temporal_oi, 
					$tonometria_nervio_optico_od, $tonometria_macula_od, $tonometria_periferia_od, $tonometria_vitreo_od,
					$tonometria_nervio_optico_oi, $tonometria_macula_oi, $tonometria_periferia_oi, $tonometria_vitreo_oi, $diagnostico_oftalmo, $solicitud_examenes,
					$tratamiento_oftalmo, $array_antecedentes, $img_biomiocroscopia, $img_tonometria_od,
					$img_tonometria_oi, $tipo_guardar, $id_usuario_crea, $observaciones_gonioscopia, $medicamentos_oftalmo,
					$array_diagnosticos, $nombre_usuario_alt, $nombre_usuario_preconsulta, $array_tonometria, $observaciones_tonometria, $ind_formula_gafas,
					$ind_eval_muscular, $mapa_hc_listas_rec);
			
		//Se guardan los datos de los colores
		$resul_aux = $dbConsultaOftalmologia->crear_editar_colores_hc($hdd_id_hc_consulta, $arr_cadenas_colores, $id_usuario_crea);
		
		if ($ind_opt > 0) {
			//Oftalmología pediátrica
			@$tipo_reg_adicional = $utilidades->str_decode($_POST["tipo_reg_adicional"]);
			if ($tipo_reg_adicional == "1" || $ind_eval_muscular == "1") {
				@$metodo_ofp = $utilidades->str_decode($_POST["metodo_ofp"]);
				@$ind_ortotropia = $utilidades->str_decode($_POST["ind_ortotropia"]);
				@$id_correccion = $utilidades->str_decode($_POST["id_correccion"]);
				@$id_ojo_fijador = $utilidades->str_decode($_POST["id_ojo_fijador"]);
				@$lejos_h = $utilidades->str_decode($_POST["lejos_h"]);
				@$lejos_h_delta = $utilidades->str_decode($_POST["lejos_h_delta"]);
				@$lejos_v = $utilidades->str_decode($_POST["lejos_v"]);
				@$lejos_v_delta = $utilidades->str_decode($_POST["lejos_v_delta"]);
				@$cerca_h = $utilidades->str_decode($_POST["cerca_h"]);
				@$cerca_h_delta = $utilidades->str_decode($_POST["cerca_h_delta"]);
				@$cerca_v = $utilidades->str_decode($_POST["cerca_v"]);
				@$cerca_v_delta = $utilidades->str_decode($_POST["cerca_v_delta"]);
				@$cerca_c_h = $utilidades->str_decode($_POST["cerca_c_h"]);
				@$cerca_c_h_delta = $utilidades->str_decode($_POST["cerca_c_h_delta"]);
				@$cerca_c_v = $utilidades->str_decode($_POST["cerca_c_v"]);
				@$cerca_c_v_delta = $utilidades->str_decode($_POST["cerca_c_v_delta"]);
				@$cerca_b_h = $utilidades->str_decode($_POST["cerca_b_h"]);
				@$cerca_b_h_delta = $utilidades->str_decode($_POST["cerca_b_h_delta"]);
				@$cerca_b_v = $utilidades->str_decode($_POST["cerca_b_v"]);
				@$cerca_b_v_delta = $utilidades->str_decode($_POST["cerca_b_v_delta"]);
				@$derecha_alto_h = $utilidades->str_decode($_POST["derecha_alto_h"]);
				@$derecha_alto_h_delta = $utilidades->str_decode($_POST["derecha_alto_h_delta"]);
				@$derecha_alto_v = $utilidades->str_decode($_POST["derecha_alto_v"]);
				@$derecha_alto_v_delta = $utilidades->str_decode($_POST["derecha_alto_v_delta"]);
				@$derecha_medio_h = $utilidades->str_decode($_POST["derecha_medio_h"]);
				@$derecha_medio_h_delta = $utilidades->str_decode($_POST["derecha_medio_h_delta"]);
				@$derecha_medio_v = $utilidades->str_decode($_POST["derecha_medio_v"]);
				@$derecha_medio_v_delta = $utilidades->str_decode($_POST["derecha_medio_v_delta"]);
				@$derecha_bajo_h = $utilidades->str_decode($_POST["derecha_bajo_h"]);
				@$derecha_bajo_h_delta = $utilidades->str_decode($_POST["derecha_bajo_h_delta"]);
				@$derecha_bajo_v = $utilidades->str_decode($_POST["derecha_bajo_v"]);
				@$derecha_bajo_v_delta = $utilidades->str_decode($_POST["derecha_bajo_v_delta"]);
				@$centro_alto_h = $utilidades->str_decode($_POST["centro_alto_h"]);
				@$centro_alto_h_delta = $utilidades->str_decode($_POST["centro_alto_h_delta"]);
				@$centro_alto_v = $utilidades->str_decode($_POST["centro_alto_v"]);
				@$centro_alto_v_delta = $utilidades->str_decode($_POST["centro_alto_v_delta"]);
				@$centro_medio_h = $utilidades->str_decode($_POST["centro_medio_h"]);
				@$centro_medio_h_delta = $utilidades->str_decode($_POST["centro_medio_h_delta"]);
				@$centro_medio_v = $utilidades->str_decode($_POST["centro_medio_v"]);
				@$centro_medio_v_delta = $utilidades->str_decode($_POST["centro_medio_v_delta"]);
				@$centro_bajo_h = $utilidades->str_decode($_POST["centro_bajo_h"]);
				@$centro_bajo_h_delta = $utilidades->str_decode($_POST["centro_bajo_h_delta"]);
				@$centro_bajo_v = $utilidades->str_decode($_POST["centro_bajo_v"]);
				@$centro_bajo_v_delta = $utilidades->str_decode($_POST["centro_bajo_v_delta"]);
				@$izquierda_alto_h = $utilidades->str_decode($_POST["izquierda_alto_h"]);
				@$izquierda_alto_h_delta = $utilidades->str_decode($_POST["izquierda_alto_h_delta"]);
				@$izquierda_alto_v = $utilidades->str_decode($_POST["izquierda_alto_v"]);
				@$izquierda_alto_v_delta = $utilidades->str_decode($_POST["izquierda_alto_v_delta"]);
				@$izquierda_medio_h = $utilidades->str_decode($_POST["izquierda_medio_h"]);
				@$izquierda_medio_h_delta = $utilidades->str_decode($_POST["izquierda_medio_h_delta"]);
				@$izquierda_medio_v = $utilidades->str_decode($_POST["izquierda_medio_v"]);
				@$izquierda_medio_v_delta = $utilidades->str_decode($_POST["izquierda_medio_v_delta"]);
				@$izquierda_bajo_h = $utilidades->str_decode($_POST["izquierda_bajo_h"]);
				@$izquierda_bajo_h_delta = $utilidades->str_decode($_POST["izquierda_bajo_h_delta"]);
				@$izquierda_bajo_v = $utilidades->str_decode($_POST["izquierda_bajo_v"]);
				@$izquierda_bajo_v_delta = $utilidades->str_decode($_POST["izquierda_bajo_v_delta"]);
				@$alto_derecha_od = $utilidades->str_decode($_POST["alto_derecha_od"]);
				@$alto_centro_od = $utilidades->str_decode($_POST["alto_centro_od"]);
				@$alto_izquierda_od = $utilidades->str_decode($_POST["alto_izquierda_od"]);
				@$medio_derecha_od = $utilidades->str_decode($_POST["medio_derecha_od"]);
				@$medio_izquierda_od = $utilidades->str_decode($_POST["medio_izquierda_od"]);
				@$bajo_derecha_od = $utilidades->str_decode($_POST["bajo_derecha_od"]);
				@$bajo_centro_od = $utilidades->str_decode($_POST["bajo_centro_od"]);
				@$bajo_izquierda_od = $utilidades->str_decode($_POST["bajo_izquierda_od"]);
				@$dvd_od = $utilidades->str_decode($_POST["dvd_od"]);
				@$alto_derecha_oi = $utilidades->str_decode($_POST["alto_derecha_oi"]);
				@$alto_centro_oi = $utilidades->str_decode($_POST["alto_centro_oi"]);
				@$alto_izquierda_oi = $utilidades->str_decode($_POST["alto_izquierda_oi"]);
				@$medio_derecha_oi = $utilidades->str_decode($_POST["medio_derecha_oi"]);
				@$medio_izquierda_oi = $utilidades->str_decode($_POST["medio_izquierda_oi"]);
				@$bajo_derecha_oi = $utilidades->str_decode($_POST["bajo_derecha_oi"]);
				@$bajo_centro_oi = $utilidades->str_decode($_POST["bajo_centro_oi"]);
				@$bajo_izquierda_oi = $utilidades->str_decode($_POST["bajo_izquierda_oi"]);
				@$dvd_oi = $utilidades->str_decode($_POST["dvd_oi"]);
				@$observaciones_oft_pediat = $utilidades->str_decode($_POST["observaciones_oft_pediat"]);
				@$inclinacion_der_h = $utilidades->str_decode($_POST["inclinacion_der_h"]);
				@$inclinacion_der_h_delta = $utilidades->str_decode($_POST["inclinacion_der_h_delta"]);
				@$inclinacion_der_v = $utilidades->str_decode($_POST["inclinacion_der_v"]);
				@$inclinacion_der_v_delta = $utilidades->str_decode($_POST["inclinacion_der_v_delta"]);
				@$inclinacion_izq_h = $utilidades->str_decode($_POST["inclinacion_izq_h"]);
				@$inclinacion_izq_h_delta = $utilidades->str_decode($_POST["inclinacion_izq_h_delta"]);
				@$inclinacion_izq_v = $utilidades->str_decode($_POST["inclinacion_izq_v"]);
				@$inclinacion_izq_v_delta = $utilidades->str_decode($_POST["inclinacion_izq_v_delta"]);
				@$ind_nistagmo = $utilidades->str_decode($_POST["ind_nistagmo"]);
				@$texto_nistagmo = $utilidades->str_decode($_POST["texto_nistagmo"]);
				@$ind_pac = $utilidades->str_decode($_POST["ind_pac"]);
				@$texto_pac = $utilidades->str_decode($_POST["texto_pac"]);
				@$conv_fusional_lejos = $utilidades->str_decode($_POST["conv_fusional_lejos"]);
				@$conv_fusional_cerca = $utilidades->str_decode($_POST["conv_fusional_cerca"]);
				@$div_fusional_lejos = $utilidades->str_decode($_POST["div_fusional_lejos"]);
				@$div_fusional_cerca = $utilidades->str_decode($_POST["div_fusional_cerca"]);
				@$id_worth_lejos = $utilidades->str_decode($_POST["id_worth_lejos"]);
				@$id_worth_cerca = $utilidades->str_decode($_POST["id_worth_cerca"]);
				@$id_estereopsis_mosca = $utilidades->str_decode($_POST["id_estereopsis_mosca"]);
				@$valor_estereopsis_animales = $utilidades->str_decode($_POST["valor_estereopsis_animales"]);
				@$valor_estereopsis_circulos = $utilidades->str_decode($_POST["valor_estereopsis_circulos"]);
				@$id_maddox_der = $utilidades->str_decode($_POST["id_maddox_der"]);
				@$valor_maddox_der = $utilidades->str_decode($_POST["valor_maddox_der"]);
				@$id_maddox_izq = $utilidades->str_decode($_POST["id_maddox_izq"]);
				@$valor_maddox_izq = $utilidades->str_decode($_POST["valor_maddox_izq"]);
				
				$resultado_aux = $dbConsultaOftalmologia->editarConsultaOftalmologiaPediatrica($hdd_id_hc_consulta, $metodo_ofp, $ind_ortotropia, $id_correccion, $id_ojo_fijador,
						$lejos_h, $lejos_h_delta, $lejos_v, $lejos_v_delta, $cerca_h, $cerca_h_delta, $cerca_v, $cerca_v_delta, $cerca_c_h, $cerca_c_h_delta, $cerca_c_v, $cerca_c_v_delta,
						$cerca_b_h, $cerca_b_h_delta, $cerca_b_v, $cerca_b_v_delta, $derecha_alto_h, $derecha_alto_h_delta, $derecha_alto_v, $derecha_alto_v_delta,
						$derecha_medio_h, $derecha_medio_h_delta, $derecha_medio_v, $derecha_medio_v_delta, $derecha_bajo_h, $derecha_bajo_h_delta, $derecha_bajo_v, $derecha_bajo_v_delta,
						$centro_alto_h, $centro_alto_h_delta, $centro_alto_v, $centro_alto_v_delta, $centro_medio_h, $centro_medio_h_delta, $centro_medio_v, $centro_medio_v_delta,
						$centro_bajo_h, $centro_bajo_h_delta, $centro_bajo_v, $centro_bajo_v_delta, $izquierda_alto_h, $izquierda_alto_h_delta, $izquierda_alto_v, $izquierda_alto_v_delta,
						$izquierda_medio_h, $izquierda_medio_h_delta, $izquierda_medio_v, $izquierda_medio_v_delta,
						$izquierda_bajo_h, $izquierda_bajo_h_delta, $izquierda_bajo_v, $izquierda_bajo_v_delta,
						$alto_derecha_od, $alto_centro_od, $alto_izquierda_od, $medio_derecha_od, $medio_izquierda_od, $bajo_derecha_od, $bajo_centro_od, $bajo_izquierda_od, $dvd_od,
						$alto_derecha_oi, $alto_centro_oi, $alto_izquierda_oi, $medio_derecha_oi, $medio_izquierda_oi, $bajo_derecha_oi, $bajo_centro_oi, $bajo_izquierda_oi, $dvd_oi,
						$observaciones_oft_pediat, $inclinacion_der_h, $inclinacion_der_h_delta, $inclinacion_der_v, $inclinacion_der_v_delta,
						$inclinacion_izq_h, $inclinacion_izq_h_delta, $inclinacion_izq_v, $inclinacion_izq_v_delta, $ind_nistagmo, $texto_nistagmo, $ind_pac, $texto_pac,
						$conv_fusional_lejos, $conv_fusional_cerca, $div_fusional_lejos, $div_fusional_cerca, $id_worth_lejos, $id_worth_cerca,
						$id_estereopsis_mosca, $valor_estereopsis_animales, $valor_estereopsis_circulos, $id_maddox_der, $valor_maddox_der, $id_maddox_izq, $valor_maddox_izq,
						$id_usuario_crea);
	?>
    <input type="hidden" name="hdd_exito_reg_adicional" id="hdd_exito_reg_adicional" value="<?php echo($resultado_aux); ?>" />
    <?php
			} else {
	?>
    <input type="hidden" name="hdd_exito_reg_adicional" id="hdd_exito_reg_adicional" value="1" />
    <?php
			}
			
			//Formulación de medicamentos
			$class_formulacion = new Class_Formulacion();
			$resultado_aux = $class_formulacion->guardarFormulacionHC($hdd_id_hc_consulta, $id_usuario_crea);
	?>
    <input type="hidden" name="hdd_exito_formulacion_fm" id="hdd_exito_formulacion_fm" value="<?php echo($resultado_aux); ?>" />
    <?php
			//Solicitud de procedimientos
			$class_solic_procs = new Class_Solic_Procs();
			$resultado_aux = $class_solic_procs->guardarHCProcedimientosSolic($hdd_id_hc_consulta, $id_usuario_crea);
	?>
    <input type="hidden" name="hdd_exito_hc_procedimientos_solic" id="hdd_exito_hc_procedimientos_solic" value="<?php echo($resultado_aux); ?>" />
    <?php
		} else {
	?>
    <input type="hidden" name="hdd_exito_reg_adicional" id="hdd_exito_reg_adicional" value="1" />
    <input type="hidden" name="hdd_exito_formulacion_fm" id="hdd_exito_formulacion_fm" value="1" />
    <input type="hidden" name="hdd_exito_hc_procedimientos_solic" id="hdd_exito_hc_procedimientos_solic" value="1" />
    <?php
		}
		
		$reg_menu = $dbMenus->getMenu(13);
		$url_menu = $reg_menu["pagina_menu"];
	?>
	<input type="hidden" name="hdd_exito" id="hdd_exito" value="<?php echo($ind_opt); ?>" />
    <input type="hidden" name="hdd_exito_colores" id="hdd_exito_colores" value="<?php echo($resul_aux); ?>" />
	<input type="hidden" name="hdd_url_menu" id="hdd_url_menu" value="<?php echo($url_menu); ?>" />
	<input type="hidden" name="hdd_tipo_guardar" id="hdd_tipo_guardar" value="<?php echo($tipo_guardar); ?>" />
	<div class="contenedor_error" id="contenedor_error"></div>
	<div class="contenedor_exito" id="contenedor_exito"></div>
	<?php
		break;		
	
	case "2":
		$id_hc = $_POST["id_hc"];
		//Obtener el listado de los antecedentes medicos
		$tabla_antecedentes_medicos = $dbConsultaOftalmologia->getAntecedentesMedicos($id_hc, 2, "");
	?>
	<table border="0" cellpadding="3" cellspacing="0" align="center" style="width:80%;">
		<tr>
			<td colspan="3"><h4>Otros antecedentes medicos</h4></td>
		</tr>	
	   	<tr>
	   	<?php
	   	    $k = 0;
		   	foreach ($tabla_antecedentes_medicos as $fila_medicos) {
		   		$id_antecedente_medico = $fila_medicos["id_antecedentes_medicos"];
				$nombre_antecedente_medico = $fila_medicos["nombre_antecedentes_medicos"];
				$checked = "";	
				
				if ($k == 2) {
		?>
		</tr><tr>
			<td style="width:30%;text-align: left;margin-left: 25px">
				<input type="checkbox" name="check_medicos_otros" id="check_medicos_otros_<?php echo $id_antecedente_medico; ?>" value="<?php echo $id_antecedente_medico; ?>" <?php echo $checked; ?> />
				<label for="check_medicos_otros_<?php echo $id_antecedente_medico; ?>"  style="font-size: 12px;"> <?php echo $nombre_antecedente_medico; ?></label>
			</td>	
			<?php
					$k = 0;
				} else {
			?>
			<td style="width:30%;text-align: left;margin-left: 25px">
				<input type="checkbox" name="check_medicos_otros" id="check_medicos_otros_<?php echo $id_antecedente_medico; ?>" value="<?php echo $id_antecedente_medico; ?>" <?php echo $checked; ?> />
				<label for="check_medicos_otros_<?php echo $id_antecedente_medico; ?>" style="font-size: 12px;"> <?php echo $nombre_antecedente_medico; ?></label>
			</td>	
			<?php
				}
				$k = $k + 1;
			}
	   		?>
	   	</tr>
	</table>
	<table border="0" cellpadding="3" cellspacing="0" align="left" style="width:100%;">
	   	<tr>
			<td align="right">
				<input class="btnPrincipal peq" type="button" id="btn_crear" nombre="btn_crear" value="Agregar" onclick="agregar_antecedentes_medicos(<?php echo($id_hc);?>);"/>	
			</td>
			<td align="left">
				<input class="btnSecundario peq" type="button" id="btn_cancelar" nombre="btn_cancelar" value="Cancelar" onclick="mostrar_formulario_flotante(0);"/>
			</td>
		</tr>
	</table>
	<?php		
    	break;
	
	case "3":
		$id_hc = $_POST["id_hc"];
		$array_antecedentes_medicos_ids = $_POST["array_antecedentes_medicos_ids"];
		$array_antecedentes_medicos_val = $_POST["array_antecedentes_medicos_val"];
		
		//Obtener el listado de los antecedentes medicos
		$tabla_antecedentes_medicos = $dbConsultaOftalmologia->getAntecedentesMedicos($id_hc, 3, $array_antecedentes_medicos_ids);
	?>
	<table border="0" cellpadding="3" cellspacing="0" align="left" style="width:100%;">
	   	<tr>
	   	    <?php
				if (count($tabla_antecedentes_medicos) > 0) {
					$k = 0;
					foreach ($tabla_antecedentes_medicos as $fila_medicos) {
						$id_antecedente_medico = $fila_medicos["id_antecedentes_medicos"];
						$nombre_antecedente_medico = $fila_medicos["nombre_antecedentes_medicos"];
						
						if ($k == 3) {
					?>
		</tr><tr>
			<td style="width:30%;">
				<input type="checkbox" name="check_medicos" id="check_medicos_<?php echo $id_antecedente_medico; ?>" value="<?php echo $id_antecedente_medico; ?>" checked onchange="validar_check_ant_medicos();" />
				<label for="check_medicos_<?php echo $id_antecedente_medico; ?>"  style="font-size: 12px;"> <?php echo $nombre_antecedente_medico; ?></label>
			</td>	
			<?php
							$k = 0;
						} else {
			?>
			<td style="width:30%;">
				<input type="checkbox" name="check_medicos" id="check_medicos_<?php echo $id_antecedente_medico; ?>" value="<?php echo $id_antecedente_medico; ?>" checked onchange="validar_check_ant_medicos();" />
				<label for="check_medicos_<?php echo $id_antecedente_medico; ?>" style="font-size: 12px;"> <?php echo $nombre_antecedente_medico; ?></label>
			</td>	
			<?php
						}
						$k++;
					}
		
					$j = 3-$k;
					for ($i = 1; $i <= $j; $i++) {
			?>
			<td style="width:30%;">&nbsp;</td>
            <?php
					}
				}
	   		?>
	   	</tr>
	</table>
	<?php
		break;
	
	case "4":
		$id_hc = $_POST["id_hc"];
		//Obtener el listado de los antecedentes otros
		$tabla_antecedentes_otros = $dbConsultaOftalmologia->getAntecedentesOtros($id_hc, 2, "");
	?>
		<table border="0" cellpadding="3" cellspacing="0" align="center" style="width:80%;">
		<tr>
			<td colspan="3"><h4>Otros antecedentes</h4></td>
		</tr>	
	   	<tr>
	   	<?php
	   	    $k = 0;
		   	foreach ($tabla_antecedentes_otros as $fila_otros) {
		   		$id_antecedente_otros = $fila_otros["id_antecedentes_otros"];
				$nombre_antecedente_otros = $fila_otros["nombre_antecedentes_otros"];
				$checked = "";	
				
				if ($k == 2) {
					?>
					</tr><tr>
					<td style="width:30%;text-align: left;margin-left: 25px">
						<input type="checkbox" name="check_otros_" id="check_otros_<?php echo $id_antecedente_otros; ?>" value="<?php echo $id_antecedente_otros; ?>" <?php echo $checked; ?> />
						<label for="check_otros_<?php echo $id_antecedente_otros; ?>"  style="font-size: 12px;"> <?php echo $nombre_antecedente_otros; ?></label>
					</td>	
					<?php
					$k=0;
				}
				else{
					?>
					<td style="width:30%;text-align: left;margin-left: 25px">
						<input type="checkbox" name="check_otros_" id="check_otros_<?php echo $id_antecedente_otros; ?>" value="<?php echo $id_antecedente_otros; ?>" <?php echo $checked; ?> />
						<label for="check_otros_<?php echo $id_antecedente_otros; ?>" style="font-size: 12px;"> <?php echo $nombre_antecedente_otros; ?></label>
					</td>	
					<?php
				}
		   	$k=$k+1;
			}
	   	?>
	   	</tr>
	   	</table>
	   	<table border="0" cellpadding="3" cellspacing="0" align="left" style="width:100%;">
	   	<tr>
			<td align="right">
				<input class="btnPrincipal peq" type="button" id="btn_crear" nombre="btn_crear" value="Agregar" onclick="agregar_otros_antecedentes_otros(<?php echo($id_hc);?>);"/>	
			</td>
			<td align="left">
				<input class="btnSecundario peq" type="button" id="btn_cancelar" nombre="btn_cancelar" value="Cancelar" onclick="mostrar_formulario_flotante(0);"/>
			</td>
		</tr>
	   	</table>
	<?php		
		break;		
		
	case "5":
		$id_hc = $_POST["id_hc"];
		$array_antecedentes_otros_ids = $_POST["array_antecedentes_otros_ids"];
		$array_antecedentes_otros_val = $_POST["array_antecedentes_otros_val"];
		
		//Obtener el listado de los antecedentes medicos
		$tabla_antecedentes_otros = $dbConsultaOftalmologia->getAntecedentesOtros($id_hc, 3, $array_antecedentes_otros_ids);
	?>
	<table border="0" cellpadding="3" cellspacing="0" align="left" style="width:100%;">
	   	<tr>
		   	<?php
	 			if (count($tabla_antecedentes_otros) > 0) {
			?>
            <table border="0" cellpadding="3" cellspacing="0" align="left" style="width:100%;">
				<?php
					foreach ($tabla_antecedentes_otros as $fila_otros) {
						$id_antecedente_otros = $fila_otros["id_antecedentes_otros"];
						$nombre_antecedente_otros = $fila_otros["nombre_antecedentes_otros"];
				?>
				<tr>
				    <td align="left" style="width:180px;">
					    <label>
                            <b><?php echo($nombre_antecedente_otros);?>:</b>
						    <div class="d_sano_oftalmo" style="float: right;" onclick="marcar_nopresenta_oft('antecedentes_otros_<?php echo ($id_antecedente_otros);?>');"></div>
					    </label>
				    </td>
                    <td align="left">
				        <textarea class="textarea" id="antecedentes_otros_<?php echo ($id_antecedente_otros);?>" nombre="antecedentes_otros_<?php echo ($id_antecedente_otros);?>" style="height:50px;padding: 5px;width:100%;" onblur="convertirAMayusculas(this);trim_cadena(this);" tabindex="" ></textarea>
				    </td>
				</tr>	
				<?php
					}
				?>
            </table>
			<?php
				}
			?>
	   	</tr>
	</table>
	<?php
		break;
		
	case "6": //Opciones de flujos alternativos
		$id_hc = $_POST["id_hc"];
		$id_admision = $_POST["id_admision"];
		$ind_preconsulta = $_POST["ind_preconsulta"];
		
		$tipo_guardar = "5";
		if ($ind_preconsulta == "1") {
			$tipo_guardar = "6";
		}
		$atencion_remision = new Class_Atencion_Remision();
		$atencion_remision->getFormularioRemisiones($id_hc, $id_admision, "crear_oftalmologia(".$tipo_guardar.", 0);", "hdd_exito");
		break;
	}
?>
