<?php session_start();
 	header('Content-Type: text/xml; charset=UTF-8');
	
    require_once("../db/DbUsuarios.php");
    require_once("../db/DbListas.php");
	require_once("../db/DbConsultaPreqxLaserOf.php");
	require_once("../db/DbMenus.php");
    require_once("../db/DbVariables.php");
	require_once("../db/DbCalendario.php");
	require_once("../db/DbAsignarCitas.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/Class_Atencion_Remision.php");
	require_once("../funciones/Class_Formulacion.php");
	require_once("../funciones/Class_Solic_Procs.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/Utilidades.php");
	
	$usuarios = new DbUsuarios();
	$listas = new DbListas();
	$preqxlaser_of = new DbConsultaPreqxLaserOf();
	$menus = new DbMenus();
	$variables = new Dbvariables();
	$calendario = new DbCalendario();
	$asignar_citas = new DbAsignarCitas();
	$contenido = new ContenidoHtml();
	$funciones_persona = new FuncionesPersona();
	$utilidades = new Utilidades();
	
	$contenido->validar_seguridad(1);
	
	$combo = new Combo_Box();
	
	$opcion = $_POST["opcion"];
	
	function cambiar_mas($texto) {
		$resultado = str_replace('|mas', '+', $texto);
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
		case "1": //Guardar Consulta de Optometria
			$hdd_id_hc_consulta = $utilidades->str_decode($_POST["hdd_id_hc_consulta"]);
			$hdd_id_admision = $utilidades->str_decode($_POST["hdd_id_admision"]);
			$id_ojo = $utilidades->str_decode($_POST["id_ojo"]);
			$preqx_laser_subjetivo = $utilidades->str_decode($_POST["preqx_laser_subjetivo"]);
			$preqx_laser_biomiocroscopia = $utilidades->str_decode($_POST["preqx_laser_biomiocroscopia"]);
			$presion_intraocular_od = $utilidades->str_decode($_POST["presion_intraocular_od"]);
			$presion_intraocular_oi = $utilidades->str_decode($_POST["presion_intraocular_oi"]);
			$fondo_ojo_nervio_optico_od = $utilidades->str_decode($_POST["fondo_ojo_nervio_optico_od"]);
			$fondo_ojo_macula_od = $utilidades->str_decode($_POST["fondo_ojo_macula_od"]);
			$fondo_ojo_periferia_od = $utilidades->str_decode($_POST["fondo_ojo_periferia_od"]);
			$fondo_ojo_vitreo_od = $utilidades->str_decode($_POST["fondo_ojo_vitreo_od"]);
			$fondo_ojo_nervio_optico_oi = $utilidades->str_decode($_POST["fondo_ojo_nervio_optico_oi"]);
			$fondo_ojo_macula_oi = $utilidades->str_decode($_POST["fondo_ojo_macula_oi"]);
			$fondo_ojo_periferia_oi = $utilidades->str_decode($_POST["fondo_ojo_periferia_oi"]);
			$fondo_ojo_vitreo_oi = $utilidades->str_decode($_POST["fondo_ojo_vitreo_oi"]);
			$preqx_laser_plan = $utilidades->str_decode($_POST["preqx_laser_plan"]);
			$diagnostico_preqx_laser_of = $utilidades->str_decode($_POST["diagnostico_preqx_laser_of"]);
			$solicitud_examenes_preqx_laser = $utilidades->str_decode($_POST["solicitud_examenes_preqx_laser"]); 
			$tratamiento_preqx_laser = $utilidades->str_decode($_POST["tratamiento_preqx_laser"]);
			$medicamentos_preqx_laser = $utilidades->str_decode($_POST["medicamentos_preqx_laser"]);
			$nombre_usuario_alt = $utilidades->str_decode($_POST["nombre_usuario_alt"]);
			
			$id_usuario_crea = $_SESSION["idUsuario"];
			$tipo_guardar = $_POST["tipo_guardar"];
			
			$cant_ciex = $_POST['cant_ciex'];
			$array_diagnosticos = array();
			for ($i = 1; $i <= $cant_ciex; $i++) {
				if (isset($_POST['cod_ciex_'.$i])) {
					$ciex_diagnostico = $_POST['cod_ciex_'.$i];
					$valor_ojos = $_POST['val_ojos_'.$i];
					$array_diagnosticos[$i][0] = $ciex_diagnostico;
					$array_diagnosticos[$i][1] = $valor_ojos;
				}	
			}
			
			$ind_opt = $preqxlaser_of->EditarConsultaPreqxLaserOf($hdd_id_hc_consulta, $hdd_id_admision, $preqx_laser_subjetivo, $preqx_laser_biomiocroscopia,
					$presion_intraocular_od, $presion_intraocular_oi, $fondo_ojo_nervio_optico_od, $fondo_ojo_macula_od, $fondo_ojo_periferia_od, $fondo_ojo_vitreo_od,
					$fondo_ojo_nervio_optico_oi, $fondo_ojo_macula_oi, $fondo_ojo_periferia_oi, $fondo_ojo_vitreo_oi, $preqx_laser_plan, $diagnostico_preqx_laser_of,
					$solicitud_examenes_preqx_laser, $tratamiento_preqx_laser, $medicamentos_preqx_laser, $nombre_usuario_alt, $array_diagnosticos, $id_usuario_crea,
					$tipo_guardar, $id_ojo);
			
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
			
			$reg_menu = $menus->getMenu(13);
			$url_menu = $reg_menu['pagina_menu'];
		?>
		<input type="hidden" value="<?php echo $ind_opt; ?>" name="hdd_exito" id="hdd_exito" />
		<input type="hidden" value="<?php echo $url_menu; ?>" name="hdd_url_menu" id="hdd_url_menu" />
		<input type="hidden" value="<?php echo $tipo_guardar; ?>" name="hdd_tipo_guardar" id="hdd_tipo_guardar" />
		<div class='contenedor_error' id='contenedor_error'></div>
		<div class='contenedor_exito' id='contenedor_exito'></div>
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
			$atencion_remision->getFormularioRemisiones($id_hc, $id_admision, "crear_preqx_laser_of(".$tipo_guardar.", 0);", "hdd_exito");
			break;
	}
?>
