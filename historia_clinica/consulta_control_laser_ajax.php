<?php
	session_start();
	/*
	  Pagina para crear consulta prequirurgica de laser 
	  Autor: Helio Ruber López - 21/02/2014
	 */
	header("Content-Type: text/xml; charset=UTF-8");
	
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbConsultaControlLaser.php");
	require_once("../db/DbMenus.php");
	require_once("../db/DbVariables.php");
	require_once("../db/DbCalendario.php");
	require_once("../db/DbAsignarCitas.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/Class_Atencion_Remision.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/Utilidades.php");
	
	$usuarios = new DbUsuarios();
	$listas = new DbListas();
	$controllaser = new DbConsultaControlLaser();
	$menus = new DbMenus();
	$variables = new Dbvariables();
	$calendario = new DbCalendario();
	$asignar_citas = new DbAsignarCitas();
	$contenido = new ContenidoHtml();
	$contenido->validar_seguridad(1);
	$funciones_persona = new FuncionesPersona();
	$utilidades = new Utilidades();
	
	$combo = new Combo_Box();
	
	$opcion = $utilidades->str_decode($_POST["opcion"]);
	
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
		case "1": //Guardar Consulta de Optometria
			$id_usuario_crea = $_SESSION["idUsuario"];
			$hdd_id_hc_consulta = $utilidades->str_decode($_POST["hdd_id_hc_consulta"]);
			$hdd_id_admision = $utilidades->str_decode($_POST["hdd_id_admision"]);
			$anamnesis = $utilidades->str_decode($_POST["anamnesis"]);
			$avsc_lejos_od = $utilidades->str_decode(cambiar_espacio($_POST["avsc_lejos_od"], 0));
			$avsc_cerca_od = $utilidades->str_decode(cambiar_espacio($_POST["avsc_cerca_od"], 0));
			$avsc_lejos_oi = $utilidades->str_decode(cambiar_espacio($_POST["avsc_lejos_oi"], 0));
			$avsc_cerca_oi = $utilidades->str_decode(cambiar_espacio($_POST["avsc_cerca_oi"], 0));
			$querato_cilindro_od = $utilidades->str_decode($_POST["querato_cilindro_od"]);
			$querato_eje_od = $utilidades->str_decode($_POST["querato_eje_od"]);
			$querato_mplano_od = $utilidades->str_decode($_POST["querato_mplano_od"]);
			$querato_cilindro_oi = $utilidades->str_decode($_POST["querato_cilindro_oi"]);
			$querato_eje_oi = $utilidades->str_decode($_POST["querato_eje_oi"]);
			$querato_mplano_oi = $utilidades->str_decode($_POST["querato_mplano_oi"]);
			$avc_esfera_od = $utilidades->str_decode($_POST["avc_esfera_od"]);
			$avc_cilindro_od = $utilidades->str_decode($_POST["avc_cilindro_od"]);
			$avc_eje_od = $utilidades->str_decode($_POST["avc_eje_od"]);
			$avcc_lejos_od = $utilidades->str_decode(cambiar_espacio($_POST["avcc_lejos_od"], 0));
			$avcc_adicion_od = $utilidades->str_decode($_POST["avcc_adicion_od"]);
			$avcc_cerca_od = $utilidades->str_decode(cambiar_espacio($_POST["avcc_cerca_od"], 0));
			$avc_esfera_oi = $utilidades->str_decode($_POST["avc_esfera_oi"]);
			$avc_cilindro_oi = $utilidades->str_decode($_POST["avc_cilindro_oi"]);
			$avc_eje_oi = $utilidades->str_decode($_POST["avc_eje_oi"]);
			$avcc_lejos_oi = $utilidades->str_decode(cambiar_espacio($_POST["avcc_lejos_oi"], 0));
			$avcc_adicion_oi = $utilidades->str_decode($_POST["avcc_adicion_oi"]);
			$avcc_cerca_oi = $utilidades->str_decode(cambiar_espacio($_POST["avcc_cerca_oi"], 0));
			$diagnostico_control_laser = $utilidades->str_decode($_POST["diagnostico_control_laser"]);
			$observaciones_avc = $utilidades->str_decode($_POST["observaciones_avc"]);
			$tipo_guardar = $utilidades->str_decode($_POST["tipo_guardar"]);
			
			$cant_ciex = $_POST["cant_ciex"];
			$array_diagnosticos = array();
			for ($i = 1; $i <= $cant_ciex; $i++) {
				if(isset($_POST["cod_ciex_".$i])){
					$ciex_diagnostico = $_POST["cod_ciex_".$i];
					$valor_ojos = $_POST["val_ojos_".$i];
					$array_diagnosticos[$i][0]=$ciex_diagnostico;
					$array_diagnosticos[$i][1]=$valor_ojos;
				}	
			}
			
			$ind_opt = $controllaser->EditarConsultaControlLaser($hdd_id_hc_consulta, $hdd_id_admision, $anamnesis, $avsc_lejos_od, $avsc_cerca_od, $avsc_lejos_oi,
					$avsc_cerca_oi, $querato_cilindro_od, $querato_eje_od, $querato_mplano_od, $querato_cilindro_oi, $querato_eje_oi, $querato_mplano_oi,
					$avc_esfera_od, $avc_cilindro_od, $avc_eje_od, $avcc_lejos_od, $avcc_adicion_od, $avcc_cerca_od, $avc_esfera_oi, $avc_cilindro_oi, $avc_eje_oi,
					$avcc_lejos_oi, $avcc_adicion_oi, $avcc_cerca_oi, $diagnostico_control_laser, $observaciones_avc, $array_diagnosticos, $id_usuario_crea, $tipo_guardar);
			
			$reg_menu=$menus->getMenu(13);
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
			
			$atencion_remision = new Class_Atencion_Remision();
			$atencion_remision->getFormularioRemisiones($id_hc, $id_admision, "crear_control_laser(4, 0);", "hdd_exito");
			break;
	}
?>
