<?php session_start();
	/*
	  Pagina para crear consulta de optometria de control
	  Autor: Feisar Moreno - 21/08/2015
	*/
	require_once("../db/DbVariables.php");
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbAdmision.php");
	require_once("../db/DbConsultaControlOptometria.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbDiagnosticos.php");
	require_once("../db/DbTiposCitasDetalle.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/Class_Color_Pick.php");
	require_once("../funciones/Class_Diagnosticos.php");
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("FuncionesHistoriaClinica.php");
	
	$dbVariables = new Dbvariables();
	$dbUsuarios = new DbUsuarios();
	$dbAdmision = new DbAdmision();
	$dbConsultaControlOptometria = new DbConsultaControlOptometria();
	$dbListas = new DbListas();
	$dbDiagnosticos = new DbDiagnosticos();
	$dbTiposCitasDetalle = new DbTiposCitasDetalle();
	$contenido = new ContenidoHtml();
	$combo = new Combo_Box();
	$class_diagnosticos = new Class_Diagnosticos();
	$utilidades = new Utilidades();
	
	//variables
	$titulo = $dbVariables->getVariable(1);
	$horas_edicion = $dbVariables->getVariable(7);
	
	//Cambiar las variables get a post
	$utilidades->get_a_post();
	$funciones_personas=new FuncionesPersona();
	$funciones_hc = new FuncionesHistoriaClinica();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $titulo['valor_variable']; ?></title>
    <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
    <link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
    <link href="../css/azul.css" rel="stylesheet" type="text/css" />
    <link href="../css/Class_Color_Pick.css" rel="stylesheet" type="text/css" />
    <!--Para autocompletar DEBE IR DE PRIMERO-->
    <script type='text/javascript' src='../js/jquery_autocompletar.js'></script>
    <script type='text/javascript' src='../js/jquery-ui.js'></script>
    <!--Para validar DEBE IR DE SEGUNDO-->
    <script type='text/javascript' src='../js/jquery.validate.js'></script>
    <script type='text/javascript' src='../js/jquery.validate.add.js'></script>
    <!--Para funciones de optometria DEBE IR DE TERCERO-->
    <script type='text/javascript' src='../js/ajax.js'></script>
    <script type='text/javascript' src='../js/funciones.js'></script>
    <script type='text/javascript' src='../js/validaFecha.js'></script>
    <script type='text/javascript' src='../js/Class_Diagnosticos_v1.2.js'></script>
    <script type='text/javascript' src='../js/Class_Atencion_Remision_v1.3.js'></script>
    <script type='text/javascript' src='../funciones/ckeditor/ckeditor.js'></script>
    <script type='text/javascript' src='../funciones/ckeditor/config.js'></script>
    <script type='text/javascript' src='../js/Class_Color_Pick.js'></script>
    <script type='text/javascript' src='historia_clinica_v1.1.js'></script>
    <script type='text/javascript' src='FuncionesHistoriaClinica.js'></script>
    <script type='text/javascript' src='control_optometria_v1.8.js'></script>       
    <?php
		$tabla_diagnosticos = $dbDiagnosticos->getDiagnosticoCiexTotal();
		$i = 0;
		$cadena_diagnosticos = '';
		foreach ($tabla_diagnosticos as $fila_diagnosticos){
			$cod_ciex = $fila_diagnosticos['codciex'];
			$nom_ciex = $fila_diagnosticos['nombre'];
			
			if ($cadena_diagnosticos != "") {
				$cadena_diagnosticos .= ",";
			}
			$cadena_diagnosticos .= "'".$nom_ciex." | ".$cod_ciex."'";
			$i++;
		}
	?>
	<script>
       	$(function() {
			var Tags_diagnosticos = [<?php echo($cadena_diagnosticos) ?>];
			
			for (k = 1; k <= 10; k++) {
				$("#txt_busca_diagnostico_" + k).autocomplete({ source: Tags_diagnosticos });
			}
		});
	</script>
    <?php
		//Array valores esfera
		$cadena_esfera = "'0'";
		for ($i = 0; $i <= 30; $i++) {
			if ($cadena_esfera != "") {
				$cadena_esfera .= ",";
			}
			if ($i > 0) {
				$cadena_esfera .= "'-".$i.",00',";
			}
			$cadena_esfera .= "'-".$i.",25',";
			$cadena_esfera .= "'-".$i.",50',";
			$cadena_esfera .= "'-".$i.",75',";
			if ($i > 0) {
				$cadena_esfera .= "'+".$i.",00',";
			}
			$cadena_esfera .= "'+".$i.",25',";
			$cadena_esfera .= "'+".$i.",50',";
			$cadena_esfera .= "'+".$i.",75'";
		}
		$cadena_esfera .= ",'NP/NA'";
		
		//Array valores cilindro
		$cadena_cilindro = "'0'";
		for ($i = 0; $i <= 10; $i++) {
			if ($cadena_cilindro != "") {
				$cadena_cilindro .= ",";
			}
			if ($i > 0) {
				$cadena_cilindro .= "'-".$i.",00',";
			}
			$cadena_cilindro .= "'-".$i.",25',";
			$cadena_cilindro .= "'-".$i.",50',";
			$cadena_cilindro .= "'-".$i.",75'";
		}
		$cadena_cilindro .= ",'NP/NA'";
		
		//Array valores eje
		$cadena_eje = "'0'";
		for ($i = 1; $i <= 180; $i++) {
			if ($cadena_eje != "") {
				$cadena_eje .= ",";
			}
			$cadena_eje .= "'".$i."'";
		}
		$cadena_eje .= ",'NP/NA'";
		
		//Array valores adicion
		$cadena_adicion = "'0'";
		for ($i = 0; $i <= 3; $i++) {
			if ($cadena_adicion != "") {
				$cadena_adicion .= ",";
			}
			if ($i > 0) {
				$cadena_adicion .= "'".$i.",00',";
			}
			$cadena_adicion .= "'".$i.",25',";
			$cadena_adicion .= "'".$i.",50',";
			$cadena_adicion .= "'".$i.",75'";
		}
		$cadena_adicion .= ",'NP/NA'";
		
		//Array valores querato
		$cadena_querato = "";
		for ($i = 30; $i <= 65; $i++) {
			if ($cadena_querato != "") {
				$cadena_querato .= ",";
			}
			$cadena_querato .= "'".$i.",00',";
			$cadena_querato .= "'".$i.",25',";
			$cadena_querato .= "'".$i.",50',";
			$cadena_querato .= "'".$i.",75'";
		}
		$cadena_querato .= ",'NP/NA'";
	?>
    <script>
		var array_esfera = [<?php echo($cadena_esfera) ?>];
		var array_cilindro = [<?php echo($cadena_cilindro) ?>];
		var array_eje = [<?php echo($cadena_eje) ?>];
		var array_adicion = [<?php echo($cadena_adicion) ?>];
		var array_querato = [<?php echo($cadena_querato) ?>];
		
		$(function() {
			var Tags_esfera = [<?php echo($cadena_esfera) ?>];
			var Tags_cilindro = [<?php echo($cadena_cilindro) ?>];
			var Tags_eje = [<?php echo($cadena_eje) ?>];
			var Tags_adicion = [<?php echo($cadena_adicion) ?>];
			var Tags_querato = [<?php echo($cadena_querato) ?>];
			
			//Para Lensometría OD
			$("#lenso_esfera_od").autocomplete({ source: Tags_esfera });
			$("#lenso_cilindro_od").autocomplete({ source: Tags_cilindro });
			$("#lenso_adicion_od").autocomplete({ source: Tags_adicion });
			
			//Para Lensometría OI
			$("#lenso_esfera_oi").autocomplete({ source: Tags_esfera });
			$("#lenso_cilindro_oi").autocomplete({ source: Tags_cilindro });
			$("#lenso_adicion_oi").autocomplete({ source: Tags_adicion });
			
			//Para Queratometria OD
			$("#querato_dif_od").autocomplete({ source: Tags_cilindro });
			$("#querato_k1_od").autocomplete({ source: Tags_querato });
			
			//Para Queratometria OI
			$("#querato_dif_oi").autocomplete({ source: Tags_cilindro });
			$("#querato_k1_oi").autocomplete({ source: Tags_querato });
			
			//Para Cicloplejia OD
            $("#cicloplejio_esfera_od").autocomplete({ source: Tags_esfera });
            $("#cicloplejio_cilindro_od").autocomplete({ source: Tags_cilindro });
            $("#cicloplejio_adicion_od").autocomplete({ source: Tags_adicion });
			
            $("#cicloplejio_esfera_oi").autocomplete({ source: Tags_esfera });
            $("#cicloplejio_cilindro_oi").autocomplete({ source: Tags_cilindro });
            $("#cicloplejio_adicion_oi").autocomplete({ source: Tags_adicion });
			
			//Para Subjetivo OD
			$("#subjetivo_esfera_od").autocomplete({ source: Tags_esfera });
			$("#subjetivo_cilindro_od").autocomplete({ source: Tags_cilindro });
			$("#subjetivo_adicion_od").autocomplete({ source: Tags_adicion });
			
			//Para Subjetivo OI
			$("#subjetivo_esfera_oi").autocomplete({ source: Tags_esfera });
			$("#subjetivo_cilindro_oi").autocomplete({ source: Tags_cilindro });
			$("#subjetivo_adicion_oi").autocomplete({ source: Tags_adicion });
		});
	</script>
</head>
<body>
	<?php
		$contenido->validar_seguridad(0);
		if (!isset($_POST['tipo_entrada'])) {
			$contenido->cabecera_html();	
		}
		
		$valores_av = $dbListas->getListaDetalles(11);
		$id_tipo_reg = 19;
		$id_usuario = $_SESSION["idUsuario"];
		
		//Variables de Control de Optometria
		if (isset($_POST['hdd_id_paciente'])) {
			$id_paciente = $_POST['hdd_id_paciente'];
			$nombre_paciente = $_POST['hdd_nombre_paciente'];
			$id_admision = $_POST['hdd_id_admision'];
			
			//Se obtienen los datos de la admision
			$admision_obj = $dbAdmision->get_admision($id_admision);
			
			if (!isset($_POST['tipo_entrada'])) {
				$tabla_hc = $dbConsultaControlOptometria->getHistoriaClinicaAdmision($id_admision, $id_tipo_reg);
			} else {
				$id_hc = $_POST['hdd_id_hc'];
				$tabla_hc = $dbConsultaControlOptometria->getHistoriaClinicaId($id_hc);
			}
			
			if (count($tabla_hc) > 0) { //Si se encuentra una HC para este paciente se carga los datos de lo contrario se crea la HC
				$tipo_accion = '2'; //Editar consulta optometria
				$id_hc_consulta = $tabla_hc['id_hc'];
				
				//se obtiene el registro de la consulta de optometria a partir del ID de la Historia Clinica 
				$tabla_optometria = $dbConsultaControlOptometria->getConsultaControlOptometria($id_hc_consulta);	
				$txt_anamnesis = $tabla_optometria['anamnesis'];
				$avsc_lejos_od = $tabla_optometria['avsc_lejos_od'];
				$avsc_ph_od = $tabla_optometria['avsc_ph_od'];
				$avsc_cerca_od = $tabla_optometria['avsc_cerca_od'];
				$avsc_lejos_oi = $tabla_optometria['avsc_lejos_oi'];
				$avsc_ph_oi = $tabla_optometria['avsc_ph_oi'];
				$avsc_cerca_oi = $tabla_optometria['avsc_cerca_oi'];
				$lenso_esfera_od = $tabla_optometria['lenso_esfera_od'];
				$lenso_cilindro_od = $tabla_optometria['lenso_cilindro_od'];
				$lenso_eje_od = $tabla_optometria['lenso_eje_od'];
				$lenso_lejos_od = $tabla_optometria['lenso_lejos_od'];
				$lenso_ph_od = $tabla_optometria['lenso_ph_od'];
				$lenso_adicion_od = $tabla_optometria['lenso_adicion_od'];
				$lenso_cerca_od = $tabla_optometria['lenso_cerca_od'];
				$lenso_esfera_oi = $tabla_optometria['lenso_esfera_oi'];
				$lenso_cilindro_oi = $tabla_optometria['lenso_cilindro_oi'];
				$lenso_eje_oi = $tabla_optometria['lenso_eje_oi'];
				$lenso_lejos_oi = $tabla_optometria['lenso_lejos_oi'];
				$lenso_ph_oi = $tabla_optometria['lenso_ph_oi'];
				$lenso_adicion_oi = $tabla_optometria['lenso_adicion_oi'];
				$lenso_cerca_oi = $tabla_optometria['lenso_cerca_oi'];
				$querato_k1_od = $tabla_optometria['querato_k1_od'];
				$querato_ejek1_od = $tabla_optometria['querato_ejek1_od'];
				$querato_dif_od = $tabla_optometria['querato_dif_od'];
				$querato_k1_oi = $tabla_optometria['querato_k1_oi'];
				$querato_ejek1_oi = $tabla_optometria['querato_ejek1_oi'];
				$querato_dif_oi = $tabla_optometria['querato_dif_oi'];
				$cicloplejio_esfera_od = $tabla_optometria['cicloplejio_esfera_od'];
				$cicloplejio_cilindro_od = $tabla_optometria['cicloplejio_cilindro_od'];
				$cicloplejio_eje_od = $tabla_optometria['cicloplejio_eje_od'];
				$cicloplejio_lejos_od = $tabla_optometria['cicloplejio_lejos_od'];
				$cicloplejio_ph_od = $tabla_optometria['cicloplejio_ph_od'];
				$cicloplejio_adicion_od = $tabla_optometria['cicloplejio_adicion_od'];
				$cicloplejio_esfera_oi = $tabla_optometria['cicloplejio_esfera_oi'];
				$cicloplejio_cilindro_oi = $tabla_optometria['cicloplejio_cilindro_oi'];
				$cicloplejio_eje_oi = $tabla_optometria['cicloplejio_eje_oi'];
				$cicloplejio_lejos_oi = $tabla_optometria['cicloplejio_lejos_oi'];
				$cicloplejio_ph_oi = $tabla_optometria['cicloplejio_ph_oi'];
				$cicloplejio_adicion_oi = $tabla_optometria['cicloplejio_adicion_oi'];
				$subjetivo_esfera_od = $tabla_optometria['subjetivo_esfera_od'];
				$subjetivo_cilindro_od = $tabla_optometria['subjetivo_cilindro_od'];
				$subjetivo_eje_od = $tabla_optometria['subjetivo_eje_od'];
				$subjetivo_lejos_od = $tabla_optometria['subjetivo_lejos_od'];
				$subjetivo_ph_od = $tabla_optometria['subjetivo_ph_od'];
				$subjetivo_adicion_od = $tabla_optometria['subjetivo_adicion_od'];
				$subjetivo_cerca_od = $tabla_optometria['subjetivo_cerca_od'];
				$subjetivo_esfera_oi = $tabla_optometria['subjetivo_esfera_oi'];
				$subjetivo_cilindro_oi = $tabla_optometria['subjetivo_cilindro_oi'];
				$subjetivo_eje_oi = $tabla_optometria['subjetivo_eje_oi'];
				$subjetivo_lejos_oi = $tabla_optometria['subjetivo_lejos_oi'];
				$subjetivo_ph_oi = $tabla_optometria['subjetivo_ph_oi'];
				$subjetivo_adicion_oi = $tabla_optometria['subjetivo_adicion_oi'];
				$subjetivo_cerca_oi = $tabla_optometria['subjetivo_cerca_oi'];
				$tipo_lente = $tabla_optometria["tipo_lente"];
				
				$id_tipo_lente = $tabla_optometria["tipos_lentes_slct"];
				$id_tipo_filtro = $tabla_optometria["tipo_filtro_slct"];
				$id_tiempo_vigencia = $tabla_optometria["tiempo_vigencia_slct"];
				$id_tiempo_periodo = $tabla_optometria["tiempo_periodo"];
				$distancia_pupilar = $tabla_optometria["distancia_pupilar"];
				$form_cantidad	= $tabla_optometria["form_cantidad"];
				$txt_observaciones_subjetivo = $tabla_optometria['observaciones_subjetivo'];
				$diagnostico_optometria = $tabla_optometria['diagnostico_optometria'];
				$id_ojo = $tabla_optometria['id_ojo'];
				$cmb_validar_consulta = $tabla_optometria['validar_completa'];
				
				//Se verifica si se debe actualizar el estado de la admisión asociada
				$en_atencion = "0";
				if (isset($_POST["hdd_en_atencion"])) {
					$en_atencion = $_POST["hdd_en_atencion"];
				}
				
				if ($en_atencion == "1") {
					$dbAdmision->editar_admision_estado($id_admision, 4, 1, $id_usuario);
				}
			} else {//Entre en procesos de crear HC
				$tipo_accion = '1'; //Crear consulta optometria
				
				//Se crea la historia clinica y se inicia la consulta de optometria
				$id_hc_consulta = $dbConsultaControlOptometria->crearConsultaControlOptometria($id_paciente, $id_admision, $id_tipo_reg, $id_usuario);
				
				if ($id_hc_consulta < 0) { //Ninguna accion Error
					$tipo_accion = '0';
				} else {
					$tabla_optometria = $dbConsultaControlOptometria->getConsultaControlOptometria($id_hc_consulta);
				}
				//Variables de inicio de conuslta de optometria
				$txt_anamnesis = "";
				$avsc_lejos_od = "";
				$avsc_ph_od = "";
				$avsc_cerca_od = "";
				$avsc_lejos_oi = "";
				$avsc_ph_oi = "";
				$avsc_cerca_oi = "";
				$lenso_esfera_od = "";
				$lenso_cilindro_od = "";
				$lenso_eje_od = "";
				$lenso_lejos_od = "";
				$lenso_ph_od = "";
				$lenso_adicion_od = "";
				$lenso_cerca_od = "";
				$lenso_esfera_oi = "";
				$lenso_cilindro_oi = "";
				$lenso_eje_oi = "";
				$lenso_lejos_oi = "";
				$lenso_ph_oi = "";
				$lenso_adicion_oi = "";
				$lenso_cerca_oi = "";
				$querato_k1_od = "";
				$querato_ejek1_od = "";
				$querato_dif_od = "";
				$querato_k1_oi = "";
				$querato_ejek1_oi = "";
				$querato_dif_oi = "";
				$cicloplejio_esfera_od = "";
				$cicloplejio_cilindro_od = "";
				$cicloplejio_eje_od = "";
				$cicloplejio_lejos_od = "";
				$cicloplejio_ph_od = "";
				$cicloplejio_adicion_od = "";
				$cicloplejio_esfera_oi = "";
				$cicloplejio_cilindro_oi = "";
				$cicloplejio_eje_oi = "";
				$cicloplejio_lejos_oi = "";
				$cicloplejio_ph_oi = "";
				$cicloplejio_adicion_oi = "";
				$subjetivo_esfera_od = "";
				$subjetivo_cilindro_od = "";
				$subjetivo_eje_od = "";
				$subjetivo_lejos_od = "";
				$subjetivo_ph_od = "";
				$subjetivo_adicion_od = "";
				$subjetivo_cerca_od = "";
				$subjetivo_esfera_oi = "";
				$subjetivo_cilindro_oi = "";
				$subjetivo_eje_oi = "";
				$subjetivo_lejos_oi = "";
				$subjetivo_ph_oi = "";
				$subjetivo_adicion_oi = "";
				$subjetivo_cerca_oi = "";
				
				$tipo_lente = "";
				$id_tipo_lente = '';
				$id_tipo_filtro = '';
				$id_tiempo_vigencia = '';
				$id_tiempo_periodo = '';
				$distancia_pupilar = '';
				$form_cantidad	= '';
				
				$txt_observaciones_subjetivo = "";
				$diagnostico_optometria = "";
				$id_ojo = "81";
				$cmb_validar_consulta = "";
			}
			
			//Se obtienen los datos del registro de historia clínica
			$historia_clinica_obj = $dbConsultaControlOptometria->getHistoriaClinicaId($id_hc_consulta);
		} else {
			$tipo_accion = '0'; //Ninguna accion Error
		}
		
		if (!isset($_POST['tipo_entrada'])) {
	?>
    <div class="title-bar title_hc">
        <div class="wrapper">
            <div class="breadcrumb">
                <ul>
                    <li class="breadcrumb_on">Consulta de Control (Optometr&iacute;a)</li>
                </ul>
            </div>
        </div>
    </div>
    <?php
		}
		
        if ($tipo_accion > 0) {
			/********************************************************/
			/*Variable que contiene la cantidad de campos de colores*/
			/********************************************************/
			$cantidad_campos_colores = 53;
			
			//Se obtiene el listado de colores
			$arr_colores = array();
			$arr_cadenas_colores = array();
			$lista_cadenas = $dbConsultaControlOptometria->getListaHistoriaClinicaColoresCampos($id_hc_consulta);
			if (count($lista_cadenas) > 0) {
				foreach ($lista_cadenas as $reg_cadena) {
					array_push($arr_cadenas_colores, $reg_cadena["cadena_colores"]);
				}
			}
			
			//Se instancia la clase que administrará los colores de los campos
			$colorPick = new Color_Pick($arr_cadenas_colores, $cantidad_campos_colores);
			$arr_colores = $colorPick->getArrayColores();
			
			//fecha de la historia clinica
			$fecha_hc_t = $tabla_optometria['fecha_hc_t'];		
			
			//Nombre del profesional que atiende la consulta
			$id_usuario_profesional = $tabla_optometria['id_usuario_crea'];
			$tabla_usuario_profesional = $dbUsuarios->getUsuario($id_usuario_profesional);
			$nombre_usuario_profesional = $tabla_usuario_profesional['nombre_usuario'].' '.$tabla_usuario_profesional['apellido_usuario'];
			
			//Para verificaro que tiene permiso de hacer cambio
			$ind_editar = $dbConsultaControlOptometria->getIndicadorEdicion($id_hc_consulta, $horas_edicion['valor_variable']);
			$ind_editar_enc_hc = $ind_editar;
			if ($ind_editar == 1 && isset($_POST['tipo_entrada'])) {
				$ind_editar_enc_hc = 0;
			}
			
			if (!isset($_POST['tipo_entrada']) || $_POST['tipo_entrada'] == 1) {
				$funciones_hc->encabezado_historia_clinica($id_paciente, $id_admision, $id_hc_consulta, $ind_editar_enc_hc, false);
			}
    ?>
    <div class="contenedor_principal" id="id_contenedor_principal">	
	    <div id="guardar_optometria" style="width: 100%; display: block;">
        	<div class='contenedor_error' id='contenedor_error'></div>
        	<div class='contenedor_exito' id='contenedor_exito'></div>
        </div>	
        <div id="imprimir_formula" style="width: 100%; display: none;"></div>
        <div class="formulario" id="principal_optometria" style="width: 100%; display: block;">
	        <?php
				//Se inserta el registro de ingreso a la historia clínica
				$dbConsultaControlOptometria->crear_ingreso_hc($id_usuario, $id_paciente, $id_admision, $id_hc_consulta, 160);
			?>
            <form id='frm_consulta_control_optometria' name='frm_consulta_control_optometria' method="post">
                <input type="hidden" name="hdd_id_hc_consulta" id="hdd_id_hc_consulta" value="<?php echo($id_hc_consulta); ?>" />
                <input type="hidden" name="hdd_id_admision" id="hdd_id_admision" value="<?php echo($id_admision); ?>" />
                <input type="hidden" name="hdd_id_paciente" id="hdd_id_paciente" value="<?php echo($id_paciente); ?>" />
                <input type="hidden" name="hdd_id_convenio" id="hdd_id_convenio" value="<?php echo($admision_obj["id_convenio"]); ?>" />
                <input type="hidden" name="hdd_id_plan" id="hdd_id_plan" value="<?php echo($admision_obj["id_plan"]); ?>" />
            	<table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
                    <tr>
                        <th align="left" style="width:60%;">
                            <h6 style="margin: 1px;"><b>Opt&oacute;metra: </b><?php echo($nombre_usuario_profesional); ?></h6>
                        </th>
                        <th align="left" style="width:40%;">
                            <h6 style="margin: 1px;">
                                <b>Consulta completa:</b> 
                                <select id="cmb_validar_consulta" class="select_hc" style="width: 60px; margin: 1px;">
                        			<?php
										for ($i = 1; $i <= 2; $i++) {
											$selected = "";
											if($i==1){$text='Si';}else if($i==2){$text='No';}
											
											if ($i == $cmb_validar_consulta) {
												$selected = " selected=\"selected\"";
											}
									?>
                                    <option value="<?php echo $i; ?>"<?php echo($selected); ?>><?php echo $text; ?></option>
                                    <?php
										}
									?>
                                </select>
                            </h6>
                        </th>
                    </tr>
                    <?php
						if (trim($historia_clinica_obj["observaciones_remision"]) != "") {
					?>
                    <tr>
                        <th align="left" colspan="2">
                            <h6 style="margin: 1px;"><b>Observaciones de atenci&oacute;n: </b><?php echo($historia_clinica_obj["observaciones_remision"]); ?></h6>
                        </th>
                    </tr>
                    <?php
						}
					?>
                </table>
                <!--INICIO ANAMNESIS-->
                <table border="0" cellpadding="0" cellspacing="0" align="center" style="width:98%;">
                    <tr>
                        <td align="center" colspan="3" class="">
                            <h5 style="margin: 10px">Anamnesis *</h5>
                            <div id="txt_anamnesis"><?php echo($utilidades->ajustar_texto_wysiwyg($txt_anamnesis)); ?></div>
                            <br /><br />
                        </td>
                    </tr>
                </table>
                <!--FIN ANAMNESIS-->
                <?php
					$lista_ojos = $dbListas->getListaDetalles(14);
				?>
                <input type="hidden" id="hdd_cant_color_pick" value="<?php echo($cantidad_campos_colores); ?>" />
                <table border="0" cellpadding="0" cellspacing="0" align="center" style="width:98%;">
                    <tr>
                        <td align="right" style="width:5%;">
                            <h6 style="margin: 1px;"><b>Ojo:&nbsp;</b></h6>
                        </td>
                        <td align="left" style="width:95%;">
                            <?php
								if ($id_ojo == "") {
									$id_ojo = "81";
								}
                                $combo->getComboDb("cmb_ojo_control", $id_ojo, $lista_ojos, "id_detalle, nombre_detalle", "", "seleccionar_ojo_co(this.value);", true, "", "", "select_hc componente_color_pick_".$arr_colores[0]);
								$colorPick->getColorPick("cmb_ojo_control", 0);
                            ?>
                        </td>
                    </tr>
                    <tr style="height:5px;"></tr>
                    <tr>
                        <td align="center" colspan="2" class="td_tabla">
                            <div class="odoi_t">
                            <div class="od_t"><h5 style="margin: 0px;">OD</h5></div>
                            <div class="oi_t"><h5 style="margin: 0px;">OI</h5></div>
                            </div>
                        </td>
                    </tr>
                </table>
                <br />
                <!--INICIO AVSC-->
                <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
                    <tr>
                        <td align="left" style="width:40%;">
                            <table border="0" cellpadding="0" cellspacing="0" align="center" style="width:100%;">
                                <tr>
                                <td align="center">
                                    LEJOS *<br />
                                    <?php
										$combo->getComboDb('avsc_lejos_od', $avsc_lejos_od, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '1', 'select_hc componente_color_pick_'.$arr_colores[1]);
										$colorPick->getColorPick("avsc_lejos_od", 1);
                                    ?>
                                </td>
                                <td align="center">
                                    PIN HOLE<br />
                                    <?php
										$combo->getComboDb('avsc_ph_od', $avsc_ph_od, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '2', 'select_hc componente_color_pick_'.$arr_colores[2]);
										$colorPick->getColorPick("avsc_ph_od", 2);
                                    ?>
                                </td>
                                <td align="center">
                                    CERCA *<br />
                                    <?php
										$combo->getComboDb('avsc_cerca_od', $avsc_cerca_od, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '3', 'select_hc componente_color_pick_'.$arr_colores[3]);
										$colorPick->getColorPick("avsc_cerca_od", 3);
                                    ?>
                                </td>
                            </tr>
                        </table>
                        </td>
                        <td align="center" style="width:20%;"><h5 style="margin: 0px">AVSC</h5></td>	
                        <td align="left" style="width:40%;">
                            <table border="0" cellpadding="0" cellspacing="0" align="center" style="width:100%;">
                                <tr>	
                                    <td align="center">
                                        LEJOS *<br />
                                        <?php
											$combo->getComboDb('avsc_lejos_oi', $avsc_lejos_oi, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '4', 'select_hc componente_color_pick_'.$arr_colores[4]);
											$colorPick->getColorPick("avsc_lejos_oi", 4);
                                        ?>
                                    </td>
                                    <td align="center">
                                        PIN HOLE<br />
                                        <?php
											$combo->getComboDb('avsc_ph_oi', $avsc_ph_oi, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '5', 'select_hc componente_color_pick_'.$arr_colores[5]);
											$colorPick->getColorPick("avsc_ph_oi", 5);
                                        ?>
                                    </td>
                                    <td align="center">
                                        CERCA *<br />
                                        <?php
											$combo->getComboDb('avsc_cerca_oi', $avsc_cerca_oi, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '6', 'select_hc componente_color_pick_'.$arr_colores[6]);
											$colorPick->getColorPick("avsc_cerca_oi", 6);
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <!--FIN AVSC-->
		   	<!--INICIO LENSO-->
		   	<table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;" class="opt_panel_1">
		   	<tr>
		   		<td align="left" style="width:40%;">
		   			<table border="0" cellpadding="1" cellspacing="0" align="center" style="width:100%;">
			   		<tr>
			   			<td align="center">
					   		ESFERA<br />
					   		<input type="text" name="lenso_esfera_od" id="lenso_esfera_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[7]); ?>" value="<?php echo $lenso_esfera_od; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);" />
					   		<?php
                            	$colorPick->getColorPick("lenso_esfera_od", 7);
							?>
				   		</td>
				   		<td align="center">
					   		CILINDRO<br />
					   		<input type="text" name="lenso_cilindro_od" id="lenso_cilindro_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[8]); ?>" value="<?php echo $lenso_cilindro_od; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);" />
                            <?php
                            	$colorPick->getColorPick("lenso_cilindro_od", 8);
							?>
				   		</td>
				   		<td align="center">
					   		EJE<br />
					   		<input type="text" name="lenso_eje_od" id="lenso_eje_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[9]); ?>" value="<?php echo $lenso_eje_od; ?>" maxlength="3" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);" />
                            <?php
                            	$colorPick->getColorPick("lenso_eje_od", 9);
							?>
				   		</td>
				   	</tr>
				   	</table>
				   	<table border="0" cellpadding="1" cellspacing="0" align="center" style="width:100%;">
				   	<tr>
				   		<td align="center">
					   		LEJOS<br />
					   		<?php
								$combo->getComboDb('lenso_lejos_od', $lenso_lejos_od, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '', 'select_hc componente_color_pick_'.$arr_colores[10]);
								$colorPick->getColorPick("lenso_lejos_od", 10);
		                    ?>
				   		</td>
				   		<td align="center">
					   		PIN HOLE<br />
					   		<?php
								$combo->getComboDb('lenso_ph_od', $lenso_ph_od, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '', 'select_hc componente_color_pick_'.$arr_colores[11]);
								$colorPick->getColorPick("lenso_ph_od", 11);
		                    ?>
				   		</td>
				   		<td align="center" valign="top">
					   		ADICI&Oacute;N<br />
					   		<input type="text" name="lenso_adicion_od" id="lenso_adicion_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[12]); ?>" value="<?php echo $lenso_adicion_od; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_adicion, this);" />
                            <?php
                            	$colorPick->getColorPick("lenso_adicion_od", 12);
							?>
				   		</td>
				   		<td align="center">
					   		CERCA<br />
					   		<?php
								$combo->getComboDb('lenso_cerca_od', $lenso_cerca_od, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '', 'select_hc componente_color_pick_'.$arr_colores[13]);
								$colorPick->getColorPick("lenso_cerca_od", 13);
		                    ?>
				   		</td>
			   		</tr>
				   	</table>		
		   		</td>
		   		<td align="center" style="width:20%;"><h5 style="margin: 0px">Lensometr&iacute;a</h5></td>
		   		<td align="left" style="width:40%;">
		   			<table border="0" cellpadding="1" cellspacing="0" align="center" style="width:100%;">
			   		<tr>
			   			<td align="center">
					   		ESFERA<br />
					   		<input type="text" name="lenso_esfera_oi" id="lenso_esfera_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[14]); ?>" value="<?php echo $lenso_esfera_oi; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);" />
                            <?php
                            	$colorPick->getColorPick("lenso_esfera_oi", 14);
							?>
				   		</td>
				   		<td align="center">
					   		CILINDRO<br />
					   		<input type="text" name="lenso_cilindro_oi" id="lenso_cilindro_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[15]); ?>" value="<?php echo $lenso_cilindro_oi; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);" />
                            <?php
                            	$colorPick->getColorPick("lenso_cilindro_oi", 15);
							?>
				   		</td>
				   		<td align="center">
					   		EJE<br />
					   		<input type="text" name="lenso_eje_oi" id="lenso_eje_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[16]); ?>" value="<?php echo $lenso_eje_oi; ?>" maxlength="3" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);" />
                            <?php
                            	$colorPick->getColorPick("lenso_eje_oi", 16);
							?>
				   		</td>
				   	</tr>
				   	</table>
				   	<table border="0" cellpadding="1" cellspacing="0" align="center" style="width:100%;">
				   	<tr>	
				   		<td align="center">
					   		LEJOS<br />
					   		<?php
								$combo->getComboDb('lenso_lejos_oi', $lenso_lejos_oi, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '', 'select_hc componente_color_pick_'.$arr_colores[17]);
								$colorPick->getColorPick("lenso_lejos_oi", 17);
		                    ?>
				   		</td>
				   		<td align="center">
					   		PIN HOLE
					   		<br />
					   		<?php
								$combo->getComboDb('lenso_ph_oi', $lenso_ph_oi, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '', 'select_hc componente_color_pick_'.$arr_colores[18]);
								$colorPick->getColorPick("lenso_ph_oi", 18);
		                    ?>
				   		</td>
				   		<td align="center" valign="top">
					   		ADICI&Oacute;N<br />
					   		<input type="text" name="lenso_adicion_oi" id="lenso_adicion_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[19]); ?>" value="<?php echo $lenso_adicion_oi; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_adicion, this);" />
                            <?php
                            	$colorPick->getColorPick("lenso_adicion_oi", 19);
							?>
				   		</td>
				   		<td align="center">
					   		CERCA<br />
					   		<?php
								$combo->getComboDb('lenso_cerca_oi', $lenso_cerca_oi, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '', 'select_hc componente_color_pick_'.$arr_colores[20]);
								$colorPick->getColorPick("lenso_cerca_oi", 20);
		                    ?>
				   		</td>
			   		</tr>
				   	</table>
		   		</td>
		   	</tr>
		   	</table>
		  <!--FIN LENSO-->
                <!--INICIO QUERATO-->
                <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;" >
                    <tr>
                        <td align="left" style="width:40%;">
                            <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:100%;">
                                <tr>
                                    <td align="center" valign="top">
                                        CILINDRO *<br />
                                        <input type="text" name="querato_dif_od" id="querato_dif_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[21]); ?>" value="<?php echo $querato_dif_od; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);" />
                                        <?php
                                        	$colorPick->getColorPick("querato_dif_od", 21);
										?>
                                    </td>
                                    <td align="center" valign="top">
                                        EJE *<br />
                                        <input type="text" name="querato_ejek1_od" id="querato_ejek1_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[22]); ?>" value="<?php echo $querato_ejek1_od; ?>" maxlength="10" onBlur="validar_array(array_eje, this);" />
                                        <?php
                                        	$colorPick->getColorPick("querato_ejek1_od", 22);
										?>
                                    </td>
                                    <td align="center" valign="top">
                                        K+PLANO *<br />
                                        <input type="text" name="querato_k1_od" id="querato_k1_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[23]); ?>" value="<?php echo $querato_k1_od; ?>" maxlength="10" onBlur="validar_array(array_querato, this);" />
                                        <?php
                                        	$colorPick->getColorPick("querato_k1_od", 23);
										?>
                                    </td>
                                </tr>
                            </table>	
                        </td>
                        <td align="center" style="width:20%;"><h5>Queratometr&iacute;a</h5></td>
                        <td align="left" style="width:40%;">
                            <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:100%;">
                                <tr>
                                    <td align="center" colspan="4" valign="top">
                                        CILINDRO *<br />
                                        <input type="text" name="querato_dif_oi" id="querato_dif_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[24]); ?>" value="<?php echo $querato_dif_oi; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);" />
                                        <?php
                                        	$colorPick->getColorPick("querato_dif_oi", 24);
										?>
                                    </td>
                                    <td align="center" valign="top">
                                        EJE *<br />
                                        <input type="text" name="querato_ejek1_oi" id="querato_ejek1_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[25]); ?>" value="<?php echo $querato_ejek1_oi; ?>" maxlength="10" onBlur="validar_array(array_eje, this);" />
                                        <?php
                                        	$colorPick->getColorPick("querato_ejek1_oi", 25);
										?>
                                    </td>
                                    <td align="center" valign="top">
                                        K+PLANO *<br />
                                        <input type="text" name="querato_k1_oi" id="querato_k1_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[26]); ?>" value="<?php echo $querato_k1_oi; ?>" maxlength="10" onBlur="validar_array(array_querato, this);" />
                                        <?php
                                        	$colorPick->getColorPick("querato_k1_oi", 26);
										?>
                                    </td>
                                </tr>
                            </table>	
                        </td>
                    </tr>
                </table>
                <!--FIN QUERATO-->
                <!--INICIO SUBJETIVO-->
                <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;" class="opt_panel_1">
                    <tr>
                        <td align="left" style="width:40%;">
                            <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:100%;">
                                <tr>
                                    <td align="center">
                                        ESFERA *<br />
                                        <input type="text" name="subjetivo_esfera_od" id="subjetivo_esfera_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[39]); ?>" value="<?php echo $subjetivo_esfera_od; ?>" tabindex="45" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);" />
                                        <?php
                                        	$colorPick->getColorPick("subjetivo_esfera_od", 39);
										?>
                                    </td>
                                    <td align="center">
                                        CILINDRO<br />
                                        <input type="text" name="subjetivo_cilindro_od" id="subjetivo_cilindro_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[40]); ?>" value="<?php echo $subjetivo_cilindro_od; ?>" tabindex="46" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);" />
                                        <?php
                                        	$colorPick->getColorPick("subjetivo_cilindro_od", 40);
										?>
                                    </td>
                                    <td align="center">
                                        EJE<br />
                                        <input type="text" name="subjetivo_eje_od" id="subjetivo_eje_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[41]); ?>" value="<?php echo $subjetivo_eje_od; ?>" tabindex="47" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);" />
                                        <?php
                                        	$colorPick->getColorPick("subjetivo_eje_od", 41);
										?>
                                    </td>
                                </tr>
                            </table>
                            <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:100%;">
                                <tr>	
                                    <td align="center">
                                        LEJOS *<br />
                                        <?php
											$combo->getComboDb('subjetivo_lejos_od', $subjetivo_lejos_od, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '48', 'select_hc componente_color_pick_'.$arr_colores[42]);
											$colorPick->getColorPick("subjetivo_lejos_od", 42);
                                        ?>
                                    </td>
                                    <td align="center">
                                        PIN HOLE
                                        <br />
                                        <?php
											$combo->getComboDb('subjetivo_ph_od', $subjetivo_ph_od, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '49', 'select_hc componente_color_pick_'.$arr_colores[43]);
											$colorPick->getColorPick("subjetivo_ph_od", 43);
                                        ?>
                                    </td>
                                    <td align="center" valign="top">
                                        ADICI&Oacute;N<br />
                                        <input type="text" name="subjetivo_adicion_od" id="subjetivo_adicion_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[44]); ?>" value="<?php echo $subjetivo_adicion_od; ?>" tabindex="50" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_adicion, this);" />
                                        <?php
                                        	$colorPick->getColorPick("subjetivo_adicion_od", 44);
										?>
                                    </td>
                                    <td align="center">
                                        CERCA *<br />
                                        <?php
											$combo->getComboDb('subjetivo_cerca_od', $subjetivo_cerca_od, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '51', 'select_hc componente_color_pick_'.$arr_colores[45]);
											$colorPick->getColorPick("subjetivo_cerca_od", 45);
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td align="center" style="width:20%;"><h5 style="margin: 0px">Subjetivo</h5></td>
                        <td align="left" style="width:40%;">
                            <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:100%;">
                                <tr>
                                    <td align="center">
                                        ESFERA *<br />
                                        <input type="text" name="subjetivo_esfera_oi" id="subjetivo_esfera_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[46]); ?>" value="<?php echo $subjetivo_esfera_oi; ?>" tabindex="52" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);" />
                                        <?php
                                        	$colorPick->getColorPick("subjetivo_esfera_oi", 46);
										?>
                                    </td>
                                    <td align="center">
                                        CILINDRO<br />
                                        <input type="text" name="subjetivo_cilindro_oi" id="subjetivo_cilindro_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[47]); ?>" value="<?php echo $subjetivo_cilindro_oi; ?>" tabindex="53" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);" />
                                        <?php
                                        	$colorPick->getColorPick("subjetivo_cilindro_oi", 47);
										?>
                                    </td>
                                    <td align="center">
                                        EJE<br />
                                        <input type="text" name="subjetivo_eje_oi" id="subjetivo_eje_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[48]); ?>" value="<?php echo $subjetivo_eje_oi; ?>" tabindex="54" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);" />
                                        <?php
                                        	$colorPick->getColorPick("subjetivo_eje_oi", 48);
										?>
                                    </td>
                                </tr>
                            </table>
                            <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:100%;">
                                <tr>	
                                    <td align="center">
                                        LEJOS *<br />
                                        <?php
											$combo->getComboDb('subjetivo_lejos_oi', $subjetivo_lejos_oi, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '55', 'select_hc componente_color_pick_'.$arr_colores[49]);
											$colorPick->getColorPick("subjetivo_lejos_oi", 49);
                                        ?>
                                    </td>
                                    <td align="center">
                                        PIN HOLE
                                        <br />
                                        <?php
											$combo->getComboDb('subjetivo_ph_oi', $subjetivo_ph_oi, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '56', 'select_hc componente_color_pick_'.$arr_colores[50]);
											$colorPick->getColorPick("subjetivo_ph_oi", 50);
                                        ?>
                                    </td>
                                    <td align="center" valign="top">
                                        ADICI&Oacute;N<br />
                                        <input type="text" name="subjetivo_adicion_oi" id="subjetivo_adicion_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[51]); ?>" value="<?php echo $subjetivo_adicion_oi; ?>" tabindex="57" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_adicion, this);" />
                                        <?php
                                        	$colorPick->getColorPick("subjetivo_adicion_oi", 51);
										?>
                                    </td>
                                    <td align="center">
                                        CERCA *<br />
                                        <?php
											$combo->getComboDb('subjetivo_cerca_oi', $subjetivo_cerca_oi, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '58', 'select_hc componente_color_pick_'.$arr_colores[52]);
											$colorPick->getColorPick("subjetivo_cerca_oi", 52);
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <?php
                    	if (!isset($_POST['tipo_entrada']) || $_POST['tipo_entrada'] != "2") {
					?>
                    <tr>
                        <td align="center" colspan='3' class="">
                            <h6 style="margin: 0px; width: 340px;">
                                Observaciones f&oacute;rmula de gafas Subjetivo
                                   <?php
                                      if ($txt_observaciones_subjetivo != '') {
                                   ?>
                                   <div class="ind_observaciones">&nbsp;</div>
                                   <?php
                                     }
                                   ?>
                             </h6>
                             <div id="txt_observaciones_subjetivo"><?php echo($utilidades->ajustar_texto_wysiwyg($txt_observaciones_subjetivo)); ?></div>
                         </td>
                    </tr> 
                      <tr>
                             <td align="center" colspan="3">
                                    <table border="0" cellpadding="0" cellspacing="5" style="width:100%;">
                                        
                                            <td align="left" style="width:15%;">
                                                <h6 style="margin:0px;">Especificaciones del lente*:&nbsp;</h6>
                                            </td>
                                            <td align="left" style="width:85%;">
                                                <input type="text" name="txt_tipo_lente" id="txt_tipo_lente" class="componente_color_pick_<?php echo($arr_colores[80]); ?>" style="width:90%; margin:0;" value="<?php echo($tipo_lente); ?>" maxlength="250" />
                                             </td>
                                      </table>
                                      <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                                        <tr>	
                                            <td align="left" >
                                                <h6 style="margin:0px;">Tipos de lentes*:&nbsp;</h6>
                                            </td>
                                            <td align="left" >
                                                <?php $lista_tipo_lente = $dbListas->getListaDetalles(102);
                                                $combo->getComboDb("cmb_tipo_lente",$id_tipo_lente, $lista_tipo_lente, "id_detalle, nombre_detalle", "Seleccione el tipo de lente", "", true, "width: 210px;");
                                                ?>
                                            </td>
                                            <td align="left">
                                                <h6 style="margin:0px;">Vigencia*:&nbsp;</h6>
                                            </td>
                                            <td align="left" >
                                             <?php $lista_tiempo_vigencia = $dbListas->getListaDetalles(103);
                                                $combo->getComboDb("cmb_tiempo_vigencia",$id_tiempo_vigencia, $lista_tiempo_vigencia, "id_detalle, nombre_detalle", "Seleccione vigencia formula", "", true, "width: 210px;");?>
                                            </td>
                                            <td align="left" >
                                                <h6 style="margin:0px;">Filtro*:&nbsp;</h6>
                                            </td>
                                            <td align="left" >
                                            <?php $lista_tipo_filtro = $dbListas->getListaDetalles(60);
                                                $combo->getComboDb("cmb_tipo_filtro",$id_tipo_filtro, $lista_tipo_filtro, "id_detalle, nombre_detalle", "Seleccione el tipo del filtro", "", true, "width: 210px;");?>
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left" >
                                                <h6 style="margin:0px;">Tiempo formulación*:&nbsp;</h6>
                                            </td>
                                            <td align="left" >
                                            
                                            <?php $lista_tiempo_periodo = $dbListas->getListaDetalles(103);
                                            $combo->getComboDb("cmb_timepo_periodo",$id_tiempo_periodo, $lista_tiempo_periodo,"id_detalle, nombre_detalle", "Seleccione el tiempo de formulación", "", true, "width: 210px;")?>
                                            </td>
                                            <td align="left">
                                                <h6 style="margin:0px;">Distancia pupilar*:&nbsp;</h6>
                                            </td>
                                            <td align="left" >
                                                <input type="text" name="txt_distancia_pupilar" id="txt_distancia_pupilar" class="componente_color_pick_<?php echo($arr_colores[80]); ?>" style="width:90%; margin:0;" value="<?php echo($distancia_pupilar); ?>" maxlength="200" />
                                                
                                            </td>
                                            <td align="left" >
        
                                                <h6 style="margin:0px;">Cantidad*:&nbsp;</h6>
                                            </td>
                                            <td align="left" >
                                                <input type="text" name="txt_cantidad" id="txt_cantidad" class="componente_color_pick_<?php echo($arr_colores[80]); ?>" style="width:90%; margin:0;" value="<?php echo($form_cantidad); ?>" maxlength="200" />
                                                
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                         </tr>      
                     <tr>    
                        <td align="center" colspan="3">   
                            <input type="button" id="btn_imprimir_subjetivo" nombre="btn_imprimir_subjetivo" class="btnPrincipal peq" style="font-size: 16px;" value="Imprimir f&oacute;rmula" onclick="generar_formula_gafas('txt_observaciones_subjetivo', '<?php echo($fecha_hc_t);?>', '<?php echo($nombre_paciente);?>', '<?php echo($nombre_usuario_profesional); ?>', 'subjetivo_esfera_od', 'subjetivo_cilindro_od', 'subjetivo_eje_od', 'subjetivo_adicion_od', 'subjetivo_esfera_oi', 'subjetivo_cilindro_oi', 'subjetivo_eje_oi', 'subjetivo_adicion_oi', '<?php echo($id_admision); ?>');"/> 
                        </td>
                    </tr>
                    <?php
						}
					?>
                </table>
                <!--FIN SUBJETIVO-->
			<!--INICIO CICLOPLEJIA-->
		   	<table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
		   	<tr>
		   		<td align="left" style="width:40%;">
		   			<table border="0" cellpadding="1" cellspacing="0" align="center" style="width:100%;">
			   		<tr>
			   			<td align="center">
					   		ESFERA<br />
					   		<input type="text" name="cicloplejio_esfera_od" id="cicloplejio_esfera_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[27]); ?>" value="<?php echo $cicloplejio_esfera_od; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);" />
                            <?php
                            	$colorPick->getColorPick("cicloplejio_esfera_od", 27);
							?>
				   		</td>
				   		<td align="center">
					   		CILINDRO<br />
					   		<input type="text" name="cicloplejio_cilindro_od" id="cicloplejio_cilindro_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[28]); ?>" value="<?php echo $cicloplejio_cilindro_od; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);" />
                            <?php
                            	$colorPick->getColorPick("cicloplejio_cilindro_od", 28);
							?>
				   		</td>
				   		<td align="center">
					   		EJE<br />
					   		<input type="text" name="cicloplejio_eje_od" id="cicloplejio_eje_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[29]); ?>"  value="<?php echo $cicloplejio_eje_od; ?>" maxlength="3" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);" />
                            <?php
                            	$colorPick->getColorPick("cicloplejio_eje_od", 29);
							?>
				   		</td>
				   	</tr>
				   	</table>
				   	<table border="0" cellpadding="3" cellspacing="0" align="center" style="width:100%;">
				   	<tr>	
				   		<td align="center">
					   		LEJOS<br />
					   		<?php
								$combo->getComboDb('cicloplejio_lejos_od', $cicloplejio_lejos_od, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '', 'select_hc componente_color_pick_'.$arr_colores[30]);
								$colorPick->getColorPick("cicloplejio_lejos_od", 30);
		                    ?>
				   		</td>
				   		<td align="center">
					   		PIN HOLE
					   		<br />
					   		<?php
								$combo->getComboDb('cicloplejio_ph_od', $cicloplejio_ph_od, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '', 'select_hc componente_color_pick_'.$arr_colores[31]);
								$colorPick->getColorPick("cicloplejio_ph_od", 31);
		                    ?>
				   		</td>
				   		<td align="center" valign="top">
					   		ADICI&Oacute;N<br />
					   		<input type="text" name="cicloplejio_adicion_od" id="cicloplejio_adicion_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[32]); ?>" value="<?php echo $cicloplejio_adicion_od; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_adicion, this);" />
                            <?php
                            	$colorPick->getColorPick("cicloplejio_adicion_od", 32);
							?>
				   		</td>
			   		</tr>
				   	</table>
		   		</td>
		   		<td align="center" style="width:20%;"><h5 style="margin: 0px">Cicloplejia</h5></td>
		   		<td align="left" style="width:40%;">
		   			<table border="0" cellpadding="1" cellspacing="0" align="center" style="width:100%;">
			   		<tr>
			   			<td align="center">
					   		ESFERA<br />
					   		<input type="text" name="cicloplejio_esfera_oi" id="cicloplejio_esfera_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[33]); ?>" value="<?php echo $cicloplejio_esfera_oi; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);" />
                            <?php
                            	$colorPick->getColorPick("cicloplejio_esfera_oi", 33);
							?>
				   		</td>
				   		<td align="center">
					   		CILINDRO<br />
					   		<input type="text" name="cicloplejio_cilindro_oi" id="cicloplejio_cilindro_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[34]); ?>" value="<?php echo $cicloplejio_cilindro_oi; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);" />
                            <?php
                            	$colorPick->getColorPick("cicloplejio_cilindro_oi", 34);
							?>
				   		</td>
				   		<td align="center">
					   		EJE<br />
					   		<input type="text" name="cicloplejio_eje_oi" id="cicloplejio_eje_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[35]); ?>" value="<?php echo $cicloplejio_eje_oi; ?>" maxlength="3" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);" />
                            <?php
                            	$colorPick->getColorPick("cicloplejio_eje_oi", 35);
							?>
				   		</td>
				   	</tr>
				   	</table>
				   	<table border="0" cellpadding="3" cellspacing="0" align="center" style="width:100%;">
				   	<tr>	
				   		<td align="center">
					   		LEJOS<br />
					   		<?php
								$combo->getComboDb('cicloplejio_lejos_oi', $cicloplejio_lejos_oi, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '', 'select_hc componente_color_pick_'.$arr_colores[36]);
								$colorPick->getColorPick("cicloplejio_lejos_oi", 36);
		                    ?>
				   		</td>
				   		<td align="center">
					   		PIN HOLE
					   		<br />
					   		<?php
								$combo->getComboDb('cicloplejio_ph_oi', $cicloplejio_ph_oi, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '', 'select_hc componente_color_pick_'.$arr_colores[37]);
								$colorPick->getColorPick("cicloplejio_ph_oi", 37);
		                    ?>
				   		</td>
				   		<td align="center">
					   		ADICI&Oacute;N<br />
					   		<input type="text" name="cicloplejio_adicion_oi" id="cicloplejio_adicion_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[38]); ?>" value="<?php echo $cicloplejio_adicion_oi; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_adicion, this);" />
                            <?php
                            	$colorPick->getColorPick("cicloplejio_adicion_oi", 38);
							?>
				   		</td>
			   		</tr>
				   	</table>
		   		</td>
		   	</tr>
		   	</table>
            <!--FIN CICLOPLEJIA-->
                <?php
                	if (!isset($_POST['tipo_entrada']) || $_POST['tipo_entrada'] != "2") {
				?>
                <!--INICIO DIAGNOSTICO-->
                <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
                    <tr>
                        <td align="left" class="" colspan='3'>
                            <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:100%;">	
                                <tr>
                                    <td align="center" style="width:30%;"><h6>Diagn&oacute;sticos</h6>
                                        <?php
											$class_diagnosticos->getFormularioDiagnosticos($id_hc_consulta);
                                        ?>
                                        <label><b>Otros diagn&oacute;sticos y an&aacute;lisis</b></label>
                                        <div id="txt_diagnostico_optometria"><?php echo($utilidades->ajustar_texto_wysiwyg($diagnostico_optometria)); ?></div>
                                        <!--<textarea style="text-align: justify;" class="textarea_oftalmo" id="txt_diagnostico_optometria" nombre="txt_diagnostico_optometria" onblur="trim_cadena(this);" tabindex="" ><?php echo $diagnostico_optometria;?></textarea>-->
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <!--FIN DIAGNOSTICO-->
                <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:90%;">
                    <tr valign="top">
                        <td colspan='3'>
                            <?php
								if (!isset($_POST['tipo_entrada'])) {
							?>
                            <input class="btnPrincipal" type="button" id="btn_imprimir" nombre="btn_imprimir" value="Imprimir" onclick="validar_crear_control_optometria(2, 1);" />
                            <?php
								} else {
							?>
                            <input class="btnPrincipal" type="button" id="btn_imprimir" nombre="btn_imprimir" value="Imprimir Optometr&iacute;a" onclick="imprimir_control_optometria();" />
                            <?php
								}
								
								if ($ind_editar == 1) {
									if (!isset($_POST['tipo_entrada'])) {
							?>
                            <input type="button" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Guardar cambios" onclick="validar_crear_control_optometria(2, 0);" />
                            <?php
										$id_tipo_cita = $admision_obj["id_tipo_cita"];
										$lista_tipos_citas_det_remisiones = $dbTiposCitasDetalle->get_lista_tipos_citas_det_remisiones($id_tipo_cita, $id_tipo_reg);
										
										if (count($lista_tipos_citas_det_remisiones) > 0) {
							?>
                            <input class="btnPrincipal" type="button" id="btn_enviar_a" nombre="btn_enviar_a" value="Enviar a ..." onclick="enviar_a_estados();" />
                            <?php
										}
							?>
                            <input type="button" id="btn_finalizar" nombre="btn_finalizar" class="btnPrincipal" value="Finalizar consulta" onclick="validar_crear_control_optometria(1, 0);" />
                            <?php
									} else {
							?>
                            <input type="button" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Guardar Optometr&iacute;a" onclick="validar_crear_control_optometria(3, 0);"/>
                            <?php
									}
								}
							?>
                        </td>
                    </tr>
                </table>
                <br/><br/>
                <?php
					}
				?>
            </form>
        </div>
        <?php
			} else {
				echo"<div class='contenedor_error' style='display:block;'>Error al ingresar a la consulta de control de optometr&iacute;a</div>";
			}
		?>        	
    </div>
    <script type='text/javascript' src='../js/foundation.min.js'></script>
	<script>
		seleccionar_ojo_co("<?php echo($id_ojo); ?>");
		
		$(document).foundation();
		
		initCKEditorAnamnesis();
		initCKEditorSubjetivo();
		initCKEditorDiagOpt();
	</script>
	<?php
		if (!isset($_POST['tipo_entrada'])) {
			$contenido->ver_historia($id_paciente);
			$contenido->footer();
		} else {
			$contenido->footer_iframe();
		}
	?>
</body>
</html>
