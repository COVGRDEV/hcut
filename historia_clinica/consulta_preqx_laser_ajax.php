<?php session_start();
	/*
	  Pagina para crear consulta prequirurgica de laser 
	  Autor: Helio Ruber López - 21/02/2014
	 */
	
 	header('Content-Type: text/xml; charset=UTF-8');
	
    require_once("../db/DbUsuarios.php");
    require_once("../db/DbListas.php");
	require_once("../db/DbConsultaPreqxLaser.php");
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
	$preqxlaser = new DbConsultaPreqxLaser();
	$menus = new DbMenus();
	$variables = new Dbvariables();
	$calendario = new DbCalendario();
	$asignar_citas = new DbAsignarCitas();
	$contenido = new ContenidoHtml();
	$combo = new Combo_Box();
	$funciones_persona = new FuncionesPersona();
	$utilidades = new Utilidades();
	
	$contenido->validar_seguridad(1);
	
	$opcion = $_POST["opcion"];
	
	function cambiar_mas($texto){
	   $resultado = str_replace('|mas', '+', $texto);
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
		case "1": //Guardar Consulta de Optometria
			$hdd_id_hc_consulta = $_POST["hdd_id_hc_consulta"];
			$hdd_id_admision = $_POST["hdd_id_admision"];
			$querato_cilindro_od = $utilidades->str_decode($_POST["querato_cilindro_od"]);
			$querato_eje_od = $utilidades->str_decode($_POST["querato_eje_od"]);
			$querato_kplano_od = $utilidades->str_decode($_POST["querato_kplano_od"]);
			$querato_cilindro_oi = $utilidades->str_decode($_POST["querato_cilindro_oi"]);
			$querato_eje_oi = $utilidades->str_decode($_POST["querato_eje_oi"]);
			$querato_kplano_oi = $utilidades->str_decode($_POST["querato_kplano_oi"]);
			$refraccion_esfera_od = $utilidades->str_decode($_POST["refraccion_esfera_od"]);
			$refraccion_cilindro_od = $utilidades->str_decode($_POST["refraccion_cilindro_od"]);
			$refraccion_eje_od = $utilidades->str_decode($_POST["refraccion_eje_od"]);
			$refraccion_lejos_od = $utilidades->str_decode(cambiar_espacio($_POST["refraccion_lejos_od"], 0));
			$refraccion_cerca_od = $utilidades->str_decode(cambiar_espacio($_POST["refraccion_cerca_od"], 0));
			$refraccion_esfera_oi = $utilidades->str_decode($_POST["refraccion_esfera_oi"]);
			$refraccion_cilindro_oi = $utilidades->str_decode($_POST["refraccion_cilindro_oi"]);
			$refraccion_eje_oi = $utilidades->str_decode($_POST["refraccion_eje_oi"]);
			$refraccion_lejos_oi = $utilidades->str_decode(cambiar_espacio($_POST["refraccion_lejos_oi"], 0));
			$refraccion_cerca_oi = $utilidades->str_decode(cambiar_espacio($_POST["refraccion_cerca_oi"], 0));
			$cicloplejio_esfera_od = $utilidades->str_decode($_POST["cicloplejio_esfera_od"]);
			$cicloplejio_cilindro_od = $utilidades->str_decode($_POST["cicloplejio_cilindro_od"]);
			$cicloplejio_eje_od = $utilidades->str_decode($_POST["cicloplejio_eje_od"]);
			$cicloplejio_avcc_lejos_od = $utilidades->str_decode(cambiar_espacio($_POST["cicloplejio_avcc_lejos_od"], 0));
			$cicloplejio_esfera_oi = $utilidades->str_decode($_POST["cicloplejio_esfera_oi"]);
			$cicloplejio_cilindro_oi = $utilidades->str_decode($_POST["cicloplejio_cilindro_oi"]);
			$cicloplejio_eje_oi = $utilidades->str_decode($_POST["cicloplejio_eje_oi"]);
			$cicloplejio_avcc_lejos_oi = $utilidades->str_decode(cambiar_espacio($_POST["cicloplejio_avcc_lejos_oi"], 0));
			$refractivo_deseado_od = $utilidades->str_decode($_POST["refractivo_deseado_od"]);
			$refractivo_esfera_od = $utilidades->str_decode($_POST["refractivo_esfera_od"]);
			$refractivo_cilindro_od = $utilidades->str_decode($_POST["refractivo_cilindro_od"]);
			$refractivo_eje_od = $utilidades->str_decode($_POST["refractivo_eje_od"]);
			$refractivo_deseado_oi = $utilidades->str_decode($_POST["refractivo_deseado_oi"]);
			$refractivo_esfera_oi = $utilidades->str_decode($_POST["refractivo_esfera_oi"]);
			$refractivo_cilindro_oi = $utilidades->str_decode($_POST["refractivo_cilindro_oi"]);
			$refractivo_eje_oi = $utilidades->str_decode($_POST["refractivo_eje_oi"]);
			$nomograma_equipo = $utilidades->str_decode(cambiar_espacio($_POST["nomograma_equipo"], 0));
			$nomograma_esfera_od = $utilidades->str_decode($_POST["nomograma_esfera_od"]);
			$nomograma_cilindro_od = $utilidades->str_decode($_POST["nomograma_cilindro_od"]);
			$nomograma_eje_od = $utilidades->str_decode($_POST["nomograma_eje_od"]);
			$nomograma_esfera_oi = $utilidades->str_decode($_POST["nomograma_esfera_oi"]);
			$nomograma_cilindro_oi = $utilidades->str_decode($_POST["nomograma_cilindro_oi"]);
			$nomograma_eje_oi = $utilidades->str_decode($_POST["nomograma_eje_oi"]);
			$patologia_ocular_valor = $utilidades->str_decode(cambiar_espacio($_POST["patologia_ocular_valor"], 0));
			$patologia_ocular_descripcion = $utilidades->str_decode($_POST["patologia_ocular_descripcion"]);
			$cirugia_ocular_valor = $utilidades->str_decode(cambiar_espacio($_POST["cirugia_ocular_valor"], 0));
			$cirugia_ocular_descripcion = $utilidades->str_decode($_POST["cirugia_ocular_descripcion"]);
			$paquimetria_central_od = $utilidades->str_decode($_POST["paquimetria_central_od"]);
			$paquimetria_central_oi = $utilidades->str_decode($_POST["paquimetria_central_oi"]);
			$diagnostico_preqx_laser = $utilidades->str_decode($_POST["diagnostico_preqx_laser"]);
			$id_usuario_crea = $_SESSION["idUsuario"];
			$tipo_guardar = $_POST["tipo_guardar"];
			$paquimetria_periferica_od = $utilidades->str_decode($_POST["paquimetria_periferica_od"]);
			$paquimetria_periferica_oi = $utilidades->str_decode($_POST["paquimetria_periferica_oi"]);
			
			$cant_ciex = $_POST['cant_ciex'];
			$array_diagnosticos = array();
			for ($i = 1; $i <= $cant_ciex; $i++) {
				if(isset($_POST['cod_ciex_'.$i])){
					$ciex_diagnostico = $_POST['cod_ciex_'.$i];
					$valor_ojos = $_POST['val_ojos_'.$i];
					$array_diagnosticos[$i][0] = $ciex_diagnostico;
					$array_diagnosticos[$i][1] = $valor_ojos;
				}	
			}
			
			$ind_opt = $preqxlaser->EditarConsultaPreqxLaser($hdd_id_hc_consulta, $hdd_id_admision, $querato_cilindro_od, $querato_eje_od, $querato_kplano_od, $querato_cilindro_oi,
					$querato_eje_oi, $querato_kplano_oi, $refraccion_esfera_od, $refraccion_cilindro_od, $refraccion_eje_od, $refraccion_lejos_od, $refraccion_cerca_od,
					$refraccion_esfera_oi, $refraccion_cilindro_oi, $refraccion_eje_oi, $refraccion_lejos_oi, $refraccion_cerca_oi, $cicloplejio_esfera_od, $cicloplejio_cilindro_od,
					$cicloplejio_eje_od, $cicloplejio_avcc_lejos_od, $cicloplejio_esfera_oi, $cicloplejio_cilindro_oi, $cicloplejio_eje_oi, $cicloplejio_avcc_lejos_oi,
					$refractivo_deseado_od, $refractivo_esfera_od, $refractivo_cilindro_od, $refractivo_eje_od, $refractivo_deseado_oi, $refractivo_esfera_oi, $refractivo_cilindro_oi,
					$refractivo_eje_oi, $nomograma_equipo, $nomograma_esfera_od, $nomograma_cilindro_od, $nomograma_eje_od, $nomograma_esfera_oi, $nomograma_cilindro_oi,
					$nomograma_eje_oi, $patologia_ocular_valor, $patologia_ocular_descripcion, $cirugia_ocular_valor, $cirugia_ocular_descripcion, $paquimetria_central_od,
					$paquimetria_central_oi, $diagnostico_preqx_laser, $array_diagnosticos, $id_usuario_crea, $tipo_guardar, $paquimetria_periferica_od, $paquimetria_periferica_oi);
			
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
			
		case "2"://Guardar o Editar Complemento de Consulta de Optometria
			$hdd_id_hc_consulta = $_POST["hdd_id_hc_consulta"];
			$refraccion_esfera_od = $utilidades->str_decode($_POST["refraccion_esfera_od"]);
			$refraccion_cilindro_od = $utilidades->str_decode($_POST["refraccion_cilindro_od"]);
			$refraccion_eje_od = $utilidades->str_decode($_POST["refraccion_eje_od"]);
			$refraccion_lejos_od = $utilidades->str_decode(cambiar_espacio($_POST["refraccion_lejos_od"], 0));
			$refraccion_cerca_od = $utilidades->str_decode(cambiar_espacio($_POST["refraccion_cerca_od"], 0));
			$refraccion_esfera_oi = $utilidades->str_decode($_POST["refraccion_esfera_oi"]);
			$refraccion_cilindro_oi = $utilidades->str_decode($_POST["refraccion_cilindro_oi"]);
			$refraccion_eje_oi = $utilidades->str_decode($_POST["refraccion_eje_oi"]);
			$refraccion_lejos_oi = $utilidades->str_decode(cambiar_espacio($_POST["refraccion_lejos_oi"], 0));
			$refraccion_cerca_oi = $utilidades->str_decode(cambiar_espacio($_POST["refraccion_cerca_oi"], 0));
			$id_usuario_crea = $_SESSION["idUsuario"];
			
			$ind_opt = $preqxlaser->CrearEditarComplementoConsultaPreqxLaser($hdd_id_hc_consulta,  
																			 $refraccion_esfera_od, $refraccion_cilindro_od, $refraccion_eje_od, $refraccion_lejos_od, $refraccion_cerca_od, 
																			 $refraccion_esfera_oi, $refraccion_cilindro_oi, $refraccion_eje_oi, $refraccion_lejos_oi, $refraccion_cerca_oi,
																			 $id_usuario_crea);
			echo "dgdfgf".$ind_opt;																		 
		?>
		<input type="hidden" value="<?php echo $ind_opt; ?>" name="hdd_exito_complemento" id="hdd_exito_complemento" />	
		<div class='contenedor_error' id='contenedor_error_complemento'></div>
		<div class='contenedor_exito' id='contenedor_exito_complemento'></div>
		<?php
			break;
			
		case "3": //Opciones de flujos alternativos
			$id_hc = $_POST["id_hc"];
			$id_admision = $_POST["id_admision"];
			
			$atencion_remision = new Class_Atencion_Remision();
			$atencion_remision->getFormularioRemisiones($id_hc, $id_admision, "crear_preqx_laser(4, 0);", "hdd_exito");
			break;
	}
?>
