<?php session_start();
	/*
	 * Pagina para crear consulta de optometria
	 * Autor: Feisar Moreno - 14/02/2014
	 */
 	header('Content-Type: text/xml; charset=UTF-8');
	
	require_once("../db/DbConsultaPreqxCatarata.php");
	require_once("../db/DbMenus.php");
	require_once("../db/DbVariables.php");
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/Class_Atencion_Remision.php");
	require_once("../funciones/Class_Formulacion.php");
	require_once("../funciones/Class_Solic_Procs.php");
	
	function cambiar_mas($texto){
	   $resultado = str_replace('|mas', '+', $texto);
	   return $resultado;	
	}
	
	$dbConsultaPreqxCatarata = new DbConsultaPreqxCatarata();
	$dbMenus = new DbMenus();
	$dbVariables = new Dbvariables();
	$utilidades = new Utilidades();
	
	$opcion = $_POST["opcion"];
	
	switch ($opcion) {
		case "1": //Guardar Consulta de Evolución
			@$id_usuario = $_SESSION["idUsuario"];
			@$id_hc = $utilidades->str_decode($_POST["id_hc"]);
			@$id_admision = $utilidades->str_decode($_POST["id_admision"]);
			@$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
			@$id_locs3 = $utilidades->str_decode($_POST["id_locs3"]);
			@$val_locs3 = $utilidades->str_decode($_POST["val_locs3"]);
			@$val_rec_endotelial = $utilidades->str_decode($_POST["val_rec_endotelial"]);
			@$val_paquimetria = $utilidades->str_decode($_POST["val_paquimetria"]);
			@$id_plegables = $utilidades->str_decode($_POST["id_plegables"]);
			@$id_rigido = $utilidades->str_decode($_POST["id_rigido"]);
			@$id_especiales = $utilidades->str_decode($_POST["id_especiales"]);
			@$texto_evolucion = $utilidades->str_decode($_POST["texto_evolucion"]);
			@$id_anestesia = $utilidades->str_decode($_POST["id_anestesia"]);
			@$querato_val_biometria_od = $utilidades->str_decode($_POST["querato_val_biometria_od"]);
			@$querato_eje_biometria_od = $utilidades->str_decode($_POST["querato_eje_biometria_od"]);
			@$querato_val_iol_master_od = $utilidades->str_decode($_POST["querato_val_iol_master_od"]);
			@$querato_eje_iol_master_od = $utilidades->str_decode($_POST["querato_eje_iol_master_od"]);
			@$querato_val_topografia_od = $utilidades->str_decode($_POST["querato_val_topografia_od"]);
			@$querato_eje_topografia_od = $utilidades->str_decode($_POST["querato_eje_topografia_od"]);
			@$querato_val_definitiva_od = $utilidades->str_decode($_POST["querato_val_definitiva_od"]);
			@$querato_eje_definitiva_od = $utilidades->str_decode($_POST["querato_eje_definitiva_od"]);
			@$querato_val_biometria_oi = $utilidades->str_decode($_POST["querato_val_biometria_oi"]);
			@$querato_eje_biometria_oi = $utilidades->str_decode($_POST["querato_eje_biometria_oi"]);
			@$querato_val_iol_master_oi = $utilidades->str_decode($_POST["querato_val_iol_master_oi"]);
			@$querato_eje_iol_master_oi = $utilidades->str_decode($_POST["querato_eje_iol_master_oi"]);
			@$querato_val_topografia_oi = $utilidades->str_decode($_POST["querato_val_topografia_oi"]);
			@$querato_eje_topografia_oi = $utilidades->str_decode($_POST["querato_eje_topografia_oi"]);
			@$querato_val_definitiva_oi = $utilidades->str_decode($_POST["querato_val_definitiva_oi"]);
			@$querato_eje_definitiva_oi = $utilidades->str_decode($_POST["querato_eje_definitiva_oi"]);
			@$img_queratometria_od = $utilidades->str_decode($_POST["img_queratometria_od"]);
			@$img_queratometria_oi = $utilidades->str_decode($_POST["img_queratometria_oi"]);
			@$ind_incision_arq = $utilidades->str_decode($_POST["ind_incision_arq"]);
			@$val_incision_arq = $utilidades->str_decode($_POST["val_incision_arq"]);
			@$observaciones_preqx = $utilidades->str_decode($_POST["observaciones_preqx"]);
			@$diagnostico_preqx_catarata = $utilidades->str_decode($_POST["diagnostico_preqx_catarata"]); 
			@$solicitud_examenes_preqx_catarata = $utilidades->str_decode($_POST["solicitud_examenes_preqx_catarata"]);
			@$tratamiento_preqx_catarata = $utilidades->str_decode($_POST["tratamiento_preqx_catarata"]);
			@$medicamentos_preqx_catarata = $utilidades->str_decode($_POST["medicamentos_preqx_catarata"]);
			@$nombre_usuario_alt = $utilidades->str_decode($_POST["nombre_usuario_alt"]);
			@$tipo_guardar = $utilidades->str_decode($_POST["tipo_guardar"]);
			
			$fecha_hoy = $dbVariables->getAnoMesDia();
			$anio = $fecha_hoy['anio_actual'];
			$mes = $fecha_hoy['mes_actual'];
			$dia = $fecha_hoy['dia_actual'];
			
			$arr_ruta_base = $dbVariables->getVariable(17);
			$ruta_base = $arr_ruta_base["valor_variable"];
			$dir_imagenes = $ruta_base."/".$anio."/".$mes."/".$dia."/".$id_paciente;
			$img_queratometria_od = $dir_imagenes."/".$id_hc."_queratometriaod.png";
			$img_queratometria_oi = $dir_imagenes."/".$id_hc."_queratometriaoi.png";
			
			$cant_ciex = intval($utilidades->str_decode($_POST['cant_ciex']), 10);
			$array_diagnosticos = array();
			$cont_aux = 0;
			for ($i = 1; $i <= $cant_ciex; $i++) {
				if (isset($_POST['cod_ciex_'.$i])) {
					@$ciex_diagnostico = $utilidades->str_decode($_POST['cod_ciex_'.$i]);
					@$valor_ojos = $utilidades->str_decode($_POST['val_ojos_'.$i]);
					$array_diagnosticos[$cont_aux][0] = $ciex_diagnostico;
					$array_diagnosticos[$cont_aux][1] = $valor_ojos;
					$cont_aux++;
				}
			}
			
			$resultado = $dbConsultaPreqxCatarata->editar_consulta_preqx_catarata($id_hc, $id_admision, $id_locs3, $val_locs3, $val_rec_endotelial, $val_paquimetria,
						 $id_plegables, $id_rigido, $id_especiales, $texto_evolucion, $id_anestesia, $querato_val_biometria_od, $querato_eje_biometria_od, $querato_val_iol_master_od,
						 $querato_eje_iol_master_od, $querato_val_topografia_od, $querato_eje_topografia_od, $querato_val_definitiva_od, $querato_eje_definitiva_od,
						 $querato_val_biometria_oi, $querato_eje_biometria_oi, $querato_val_iol_master_oi, $querato_eje_iol_master_oi, $querato_val_topografia_oi,
						 $querato_eje_topografia_oi, $querato_val_definitiva_oi, $querato_eje_definitiva_oi, $img_queratometria_od, $img_queratometria_oi, $ind_incision_arq,
						 $val_incision_arq, $observaciones_preqx, $diagnostico_preqx_catarata, $solicitud_examenes_preqx_catarata, $tratamiento_preqx_catarata,
						 $medicamentos_preqx_catarata, $nombre_usuario_alt, $array_diagnosticos, $tipo_guardar, $id_usuario);
			
			if ($resultado > 0) {
				//Formulación de medicamentos
				$class_formulacion = new Class_Formulacion();
				$resultado_aux = $class_formulacion->guardarFormulacionHC($id_hc, $id_usuario);
		?>
        <input type="hidden" name="hdd_exito_formulacion_fm" id="hdd_exito_formulacion_fm" value="<?php echo($resultado_aux); ?>" />
        <?php
				//Solicitud de procedimientos
				$class_solic_procs = new Class_Solic_Procs();
				$resultado_aux = $class_solic_procs->guardarHCProcedimientosSolic($id_hc, $id_usuario);
		?>
    	<input type="hidden" name="hdd_exito_hc_procedimientos_solic" id="hdd_exito_hc_procedimientos_solic" value="<?php echo($resultado_aux); ?>" />
        <?php
			} else {
		?>
        <input type="hidden" name="hdd_exito_formulacion_fm" id="hdd_exito_formulacion_fm" value="1" />
        <input type="hidden" name="hdd_exito_hc_procedimientos_solic" id="hdd_exito_hc_procedimientos_solic" value="1" />
        <?php
			}
			
			$reg_menu = $dbMenus->getMenu(13);
		?>
		<input type="hidden" name="hdd_exito" id="hdd_exito" value="<?php echo($resultado); ?>" />
		<input type="hidden" name="hdd_url_menu" id="hdd_url_menu" value="<?php echo($reg_menu['pagina_menu']); ?>" />
		<input type="hidden" name="hdd_tipo_guardar" id="hdd_tipo_guardar" value="<?php echo($tipo_guardar); ?>" />
		<div class="contenedor_error" id="contenedor_error"></div>
		<div class="contenedor_exito" id="contenedor_exito"></div>
		<?php
			break;
			
		case "2": //Opciones de flujos alternativos
			$id_hc = $_POST["id_hc"];
			$id_admision = $_POST["id_admision"];
			
			$atencion_remision = new Class_Atencion_Remision();
			$atencion_remision->getFormularioRemisiones($id_hc, $id_admision, "guardar_consulta(4, 0);", "hdd_exito");
			break;
	}
?>
