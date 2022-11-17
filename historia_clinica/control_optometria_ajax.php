<?php session_start();
/*
  Pagina para crear consulta de optometria 
  Autor: Helio Ruber López - 15/11/2013
 */
 
 	header('Content-Type: text/xml; charset=UTF-8');

    require_once("../db/DbUsuarios.php");
    require_once("../db/DbListas.php");
	require_once("../db/DbConsultaControlOptometria.php");
	require_once("../db/DbMenus.php");
    require_once("../db/DbVariables.php");
	require_once("../db/DbCalendario.php");
	require_once("../db/DbAsignarCitas.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/Class_Color_Pick.php");
	require_once("../funciones/Class_Atencion_Remision.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/Utilidades.php");
	
	$usuarios = new DbUsuarios();
	$listas = new DbListas();
	$dbConsultaControlOptometria = new DbConsultaControlOptometria();
	$menus = new DbMenus();
	$variables = new Dbvariables();
	$calendario = new DbCalendario();
	$asignar_citas = new DbAsignarCitas();
	$contenido = new ContenidoHtml();
	$contenido->validar_seguridad(1);
	$funciones_persona = new FuncionesPersona();
	$utilidades = new Utilidades();
	
	$combo=new Combo_Box();
	$colorPick = new Color_Pick(array(), 0);
	
	$opcion = $_POST["opcion"];
	
	function cambiar_mas($texto){
	   $resultado = str_replace('|mas', '+', $texto);
	   return $resultado;	
	}

	function cambiar_espacio($texto, $cambio) {
		$valor = '';
		if ($texto == "") {
			$valor = $cambio;
		} else {
			$valor = $texto;
		}
		
		return $valor;
	}
	
	switch ($opcion) {
	case "1": //Guardar Consulta de Optometria
		$id_usuario = $_SESSION["idUsuario"];
		
	    $id_hc = $utilidades->str_decode($_POST["id_hc"]);
	    $id_admision = $utilidades->str_decode($_POST["id_admision"]);
		$validar_completa = $utilidades->str_decode($_POST["validar_completa"]);
		$anamnesis = $utilidades->str_decode($_POST["anamnesis"]);
		$id_ojo = $utilidades->str_decode($_POST["id_ojo"]);
		
		$avsc_lejos_od = cambiar_espacio($utilidades->str_decode($_POST["avsc_lejos_od"]), 0);
		$avsc_ph_od = cambiar_espacio($utilidades->str_decode($_POST["avsc_ph_od"]), 0);
		$avsc_cerca_od = cambiar_espacio($utilidades->str_decode($_POST["avsc_cerca_od"]), 0);
		$avsc_lejos_oi = cambiar_espacio($utilidades->str_decode($_POST["avsc_lejos_oi"]), 0);
		$avsc_ph_oi = cambiar_espacio($utilidades->str_decode($_POST["avsc_ph_oi"]), 0);
		$avsc_cerca_oi = cambiar_espacio($utilidades->str_decode($_POST["avsc_cerca_oi"]), 0);
		
		$lenso_esfera_od = $utilidades->str_decode($_POST["lenso_esfera_od"]);
		$lenso_cilindro_od = $utilidades->str_decode($_POST["lenso_cilindro_od"]);
		$lenso_eje_od = $utilidades->str_decode($_POST["lenso_eje_od"]);
		$lenso_lejos_od = cambiar_espacio($utilidades->str_decode($_POST["lenso_lejos_od"]), 0);
		$lenso_ph_od = cambiar_espacio($utilidades->str_decode($_POST["lenso_ph_od"]), 0);
		$lenso_adicion_od = $utilidades->str_decode($_POST["lenso_adicion_od"]);
		$lenso_cerca_od = cambiar_espacio($utilidades->str_decode($_POST["lenso_cerca_od"]), 0);
		$lenso_esfera_oi = $utilidades->str_decode($_POST["lenso_esfera_oi"]);
		$lenso_cilindro_oi = $utilidades->str_decode($_POST["lenso_cilindro_oi"]);
		$lenso_eje_oi = $utilidades->str_decode($_POST["lenso_eje_oi"]);
		$lenso_lejos_oi = cambiar_espacio($utilidades->str_decode($_POST["lenso_lejos_oi"]), 0);
		$lenso_ph_oi = cambiar_espacio($utilidades->str_decode($_POST["lenso_ph_oi"]), 0);
		$lenso_adicion_oi = $utilidades->str_decode($_POST["lenso_adicion_oi"]);
		$lenso_cerca_oi = cambiar_espacio($utilidades->str_decode($_POST["lenso_cerca_oi"]), 0);
		
		$querato_k1_od = $utilidades->str_decode($_POST["querato_k1_od"]);
		$querato_ejek1_od = $utilidades->str_decode($_POST["querato_ejek1_od"]);
		$querato_k2_od = "";
		$querato_ejek2_od = "";
		$querato_dif_od = $utilidades->str_decode($_POST["querato_dif_od"]);
		$querato_k1_oi = $utilidades->str_decode($_POST["querato_k1_oi"]);
		$querato_ejek1_oi = $utilidades->str_decode($_POST["querato_ejek1_oi"]);
		$querato_k2_oi = "";
		$querato_ejek2_oi = "";
		$querato_dif_oi = $utilidades->str_decode($_POST["querato_dif_oi"]);
		
		$cicloplejio_esfera_od = $utilidades->str_decode($_POST["cicloplejio_esfera_od"]);
		$cicloplejio_cilindro_od = $utilidades->str_decode($_POST["cicloplejio_cilindro_od"]);
		$cicloplejio_eje_od = $utilidades->str_decode($_POST["cicloplejio_eje_od"]);
		$cicloplejio_lejos_od = cambiar_espacio($utilidades->str_decode($_POST["cicloplejio_lejos_od"]), 0);
		$cicloplejio_ph_od = cambiar_espacio($utilidades->str_decode($_POST["cicloplejio_ph_od"]), 0);
		$cicloplejio_adicion_od = $utilidades->str_decode($_POST["cicloplejio_adicion_od"]);
		$cicloplejio_esfera_oi = $utilidades->str_decode($_POST["cicloplejio_esfera_oi"]);
		$cicloplejio_cilindro_oi = $utilidades->str_decode($_POST["cicloplejio_cilindro_oi"]);
		$cicloplejio_eje_oi = $utilidades->str_decode($_POST["cicloplejio_eje_oi"]);
		$cicloplejio_lejos_oi = cambiar_espacio($utilidades->str_decode($_POST["cicloplejio_lejos_oi"]), 0);
		$cicloplejio_ph_oi = cambiar_espacio($utilidades->str_decode($_POST["cicloplejio_ph_oi"]), 0);
		$cicloplejio_adicion_oi = $utilidades->str_decode($_POST["cicloplejio_adicion_oi"]);
		
		$subjetivo_esfera_od = $utilidades->str_decode($_POST["subjetivo_esfera_od"]);
		$subjetivo_cilindro_od = $utilidades->str_decode($_POST["subjetivo_cilindro_od"]);
		$subjetivo_eje_od = $utilidades->str_decode($_POST["subjetivo_eje_od"]);
		$subjetivo_lejos_od = cambiar_espacio($utilidades->str_decode($_POST["subjetivo_lejos_od"]), 0);
		$subjetivo_ph_od = cambiar_espacio($utilidades->str_decode($_POST["subjetivo_ph_od"]), 0);
		$subjetivo_adicion_od = $utilidades->str_decode($_POST["subjetivo_adicion_od"]);
		$subjetivo_cerca_od = cambiar_espacio($utilidades->str_decode($_POST["subjetivo_cerca_od"]), 0);
		$subjetivo_esfera_oi = $utilidades->str_decode($_POST["subjetivo_esfera_oi"]);
		$subjetivo_cilindro_oi = $utilidades->str_decode($_POST["subjetivo_cilindro_oi"]);
		$subjetivo_eje_oi = $utilidades->str_decode($_POST["subjetivo_eje_oi"]);
		$subjetivo_lejos_oi = cambiar_espacio($utilidades->str_decode($_POST["subjetivo_lejos_oi"]), 0);
		$subjetivo_ph_oi = cambiar_espacio($utilidades->str_decode($_POST["subjetivo_ph_oi"]), 0);
		$subjetivo_adicion_oi = $utilidades->str_decode($_POST["subjetivo_adicion_oi"]);
		$subjetivo_cerca_oi = cambiar_espacio($utilidades->str_decode($_POST["subjetivo_cerca_oi"]), 0);
		
		$tipo_lente = $utilidades->str_decode($_POST["tipo_lente"]);
		$tipos_lentes_slct = $utilidades->str_decode($_POST["tipos_letnes_slct"]);
		$tipo_filtro_slct = $utilidades->str_decode($_POST["tipo_filtro_slct"]);
		$tiempo_vigencia_slct = $utilidades->str_decode($_POST["tiempo_vigencia_slct"]);
		$tiempo_periodo = $utilidades->str_decode($_POST["tiempo_periodo"]);
		$distancia_pupilar = $utilidades->str_decode($_POST["distancia_pupilar"]);
		$form_cantidad = $utilidades->str_decode($_POST["form_cantidad"]);
     	
	
	 	$observaciones_avsc = "";
		$observaciones_queratometria = "";
		$observaciones_subjetivo = $utilidades->str_decode($_POST['observaciones_subjetivo']);
		$observaciones_subjetivo_2 = "";
		$diagnostico_optometria = $utilidades->str_decode($_POST['diagnostico_optometria']);		
		
		$tipo_guardar = $utilidades->str_decode($_POST["tipo_guardar"]);
		
		$cant_ciex = $utilidades->str_decode($_POST['cant_ciex']);
		$array_diagnosticos = array();
		for ($i = 1; $i <= $cant_ciex; $i++) {
		 	if (isset($_POST['cod_ciex_'.$i])) {
			 	$ciex_diagnostico = $utilidades->str_decode($_POST['cod_ciex_'.$i]);
				$valor_ojos = $utilidades->str_decode($_POST['val_ojos_'.$i]);
				$array_diagnosticos[$i][0] = $ciex_diagnostico;
				$array_diagnosticos[$i][1] = $valor_ojos;
			}
		}
		
		$cadena_colores = $utilidades->str_decode($_POST['cadena_colores']);
		
		$arr_cadenas_colores = $colorPick->getListasColores($cadena_colores);
		
		$ind_opt = $dbConsultaControlOptometria->editarConsultaControlOptometria($id_hc, $id_admision, $validar_completa, $anamnesis, $id_ojo,
				$avsc_lejos_od, $avsc_ph_od, $avsc_cerca_od, $avsc_lejos_oi, $avsc_ph_oi, $avsc_cerca_oi, $lenso_esfera_od, $lenso_cilindro_od,
				$lenso_eje_od, $lenso_lejos_od, $lenso_ph_od, $lenso_adicion_od, $lenso_cerca_od, $lenso_esfera_oi, $lenso_cilindro_oi,
				$lenso_eje_oi, $lenso_lejos_oi, $lenso_ph_oi, $lenso_adicion_oi, $lenso_cerca_oi, $querato_k1_od, $querato_ejek1_od,
				$querato_k2_od, $querato_ejek2_od, $querato_dif_od, $querato_k1_oi, $querato_ejek1_oi, $querato_k2_oi, $querato_ejek2_oi,
				$querato_dif_oi, $cicloplejio_esfera_od, $cicloplejio_cilindro_od, $cicloplejio_eje_od, $cicloplejio_lejos_od, $cicloplejio_ph_od,
				$cicloplejio_adicion_od, $cicloplejio_esfera_oi, $cicloplejio_cilindro_oi, $cicloplejio_eje_oi, $cicloplejio_lejos_oi,
				$cicloplejio_ph_oi, $cicloplejio_adicion_oi, $subjetivo_esfera_od, $subjetivo_cilindro_od, $subjetivo_eje_od, $subjetivo_lejos_od,
				$subjetivo_ph_od, $subjetivo_adicion_od, $subjetivo_cerca_od, $subjetivo_esfera_oi, $subjetivo_cilindro_oi, $subjetivo_eje_oi,
				$subjetivo_lejos_oi, $subjetivo_ph_oi, $subjetivo_adicion_oi, $subjetivo_cerca_oi, $tipo_lente,$tipos_lentes_slct,$tipo_filtro_slct, $tiempo_vigencia_slct,                $tiempo_periodo, $distancia_pupilar,$form_cantidad ,  $observaciones_avsc, $observaciones_queratometria,
				$observaciones_subjetivo, $observaciones_subjetivo_2, $diagnostico_optometria, $array_diagnosticos, $id_usuario, $tipo_guardar);
				
				
		
		//Se guardan los datos de los colores
		$resul_aux = $dbConsultaControlOptometria->crear_editar_colores_hc($id_hc, $arr_cadenas_colores, $id_usuario);
		
		$reg_menu = $menus->getMenu(13);
		$url_menu = $reg_menu['pagina_menu'];
	?>
	<input type="hidden" name="hdd_exito" id="hdd_exito" value="<?php echo $ind_opt; ?>" />
    <input type="hidden" name="hdd_exito_colores" id="hdd_exito_colores" value="<?php echo($resul_aux); ?>" />
	<input type="hidden" name="hdd_url_menu" id="hdd_url_menu" value="<?php echo $url_menu; ?>" />
	<input type="hidden" name="hdd_tipo_guardar" id="hdd_tipo_guardar" value="<?php echo $tipo_guardar; ?>" />
	<div class='contenedor_error' id='contenedor_error'></div>
	<div class='contenedor_exito' id='contenedor_exito'></div>
	<?php
		break;		
		
	case "3": //Opciones de flujos alternativos
		$id_hc = $_POST["id_hc"];
		$id_admision = $_POST["id_admision"];
		
		$atencion_remision = new Class_Atencion_Remision();
		$atencion_remision->getFormularioRemisiones($id_hc, $id_admision, "validar_crear_control_optometria(4, 0);", "hdd_exito");
		break;
	}
?>
