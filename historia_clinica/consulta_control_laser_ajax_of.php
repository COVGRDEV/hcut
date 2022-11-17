<?php session_start();
	header("Content-Type: text/xml; charset=UTF-8");
	
	require_once("../db/DbConsultaControlLaserOf.php");
	require_once("../db/DbMenus.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Class_Atencion_Remision.php");
	require_once("../funciones/Class_Formulacion.php");
	require_once("../funciones/Class_Solic_Procs.php");
	require_once("../funciones/Utilidades.php");
	
	$dbConsultaControlLaserOf = new DbConsultaControlLaserOf();
	$dbMenus = new DbMenus();
	
	$contenido = new ContenidoHtml();
	$contenido->validar_seguridad(1);
	$utilidades = new Utilidades();
	
	$opcion=$_POST["opcion"];
	
	function cambiar_mas($texto){
		$resultado = str_replace("|mas", "+", $texto);
		return $resultado;	
	}
	
	function cambiar_espacio($texto, $cambio){
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
			@$hdd_id_hc_consulta = $utilidades->str_decode($_POST["hdd_id_hc_consulta"]);
			@$hdd_id_admision = $utilidades->str_decode($_POST["hdd_id_admision"]);
			@$presion_intraocular_aplanatica_od = $utilidades->str_decode($_POST["presion_intraocular_aplanatica_od"]);
			@$presion_intraocular_aplanatica_oi = $utilidades->str_decode($_POST["presion_intraocular_aplanatica_oi"]);
			@$hallazgos_control_laser = $utilidades->str_decode($_POST["hallazgos_control_laser"]);
			@$diagnostico_control_laser_of = $utilidades->str_decode($_POST["diagnostico_control_laser_of"]);
			@$tipo_guardar = $utilidades->str_decode($_POST["tipo_guardar"]);
			@$solicitud_examenes_control_laser = $utilidades->str_decode($_POST["solicitud_examenes_control_laser"]);
			@$tratamiento_control_laser = $utilidades->str_decode($_POST["tratamiento_control_laser"]);
			@$medicamentos_control_laser = $utilidades->str_decode($_POST["medicamentos_control_laser"]);
			@$nombre_usuario_alt = $utilidades->str_decode($_POST["nombre_usuario_alt"]);
			@$ind_formula_gafas = $utilidades->str_decode($_POST["ind_formula_gafas"]);
			
			@$cant_ciex = $_POST["cant_ciex"];
			$array_diagnosticos = array();
			for ($i = 1; $i <= $cant_ciex; $i++) {
				if (isset($_POST["cod_ciex_".$i])) {
					@$array_diagnosticos[$i][0] = $utilidades->str_decode($_POST["cod_ciex_".$i]);
					@$array_diagnosticos[$i][1] = $utilidades->str_decode($_POST["val_ojos_".$i]);
				}	
			}
			
			$ind_opt = $dbConsultaControlLaserOf->EditarConsultaControlLaserOf($hdd_id_hc_consulta, $hdd_id_admision,	
					$presion_intraocular_aplanatica_od, $presion_intraocular_aplanatica_oi,
					$hallazgos_control_laser, $diagnostico_control_laser_of,
					$solicitud_examenes_control_laser, $tratamiento_control_laser, $medicamentos_control_laser,
					$nombre_usuario_alt, $array_diagnosticos, $id_usuario_crea, $tipo_guardar, $ind_formula_gafas);
			
			if ($ind_opt > 0) {
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
        <input type="hidden" name="hdd_exito_formulacion_fm" id="hdd_exito_formulacion_fm" value="1" />
        <input type="hidden" name="hdd_exito_hc_procedimientos_solic" id="hdd_exito_hc_procedimientos_solic" value="1" />
        <?php
			}
			
			$reg_menu = $dbMenus->getMenu(13);
			$url_menu = $reg_menu["pagina_menu"];
		?>
		<input type="hidden" value="<?php echo $ind_opt; ?>" name="hdd_exito" id="hdd_exito" />
		<input type="hidden" value="<?php echo $url_menu; ?>" name="hdd_url_menu" id="hdd_url_menu" />
		<input type="hidden" value="<?php echo $tipo_guardar; ?>" name="hdd_tipo_guardar" id="hdd_tipo_guardar" />
		<div class="contenedor_error" id="contenedor_error"></div>
		<div class="contenedor_exito" id="contenedor_exito"></div>
		<?php
			break;
			
		case "2": //Opciones de flujos alternativos
			$id_hc = $_POST["id_hc"];
			$id_admision = $_POST["id_admision"];
			$ind_preconsulta = $_POST["ind_preconsulta"];
			
			$tipo_guardar = "5";
			if ($ind_preconsulta == "1") {
				$tipo_guardar = "6";
			}
			$atencion_remision = new Class_Atencion_Remision();
			$atencion_remision->getFormularioRemisiones($id_hc, $id_admision, "crear_control_laser_of(".$tipo_guardar.", 0);", "hdd_exito");
			break;
	}
?>
