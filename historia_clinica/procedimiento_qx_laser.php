<?php session_start();
	/*
	 * Pagina para crear registros de cirugía
	 * Autor: Feisar Moreno - 04/04/2014
	 */
	require_once("../db/DbVariables.php");
	require_once("../db/DbPacientes.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbConvenios.php");
	require_once("../db/DbPagos.php");
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbAdmision.php");
	require_once("../db/DbCirugias.php");
	require_once("../db/DbConsultaPreqxLaser.php");
	require_once("../db/DbDiagnosticos.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/Class_Diagnosticos.php");
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("FuncionesHistoriaClinica.php");
	
	$dbVariables = new Dbvariables();
	$dbPacientes = new DbPacientes();
	$dbListas = new DbListas();
	$dbConvenios = new DbConvenios();
	$dbPagos = new DbPagos();
	$dbUsuarios = new DbUsuarios();
	$dbAdmision = new DbAdmision();
	$dbCirugias = new DbCirugias();
	$dbConsultaPreqxLaser = new DbConsultaPreqxLaser();
	$db_diagnosticos = new DbDiagnosticos();
	
	$contenido = new ContenidoHtml();
	$utilidades = new Utilidades();
	$funciones_persona = new FuncionesPersona();
	$funciones_hc = new FuncionesHistoriaClinica();
	
	//variables
	$titulo = $dbVariables->getVariable(1);
	$horas_edicion = $dbVariables->getVariable(7);
	//Cambiar las variables get a post
	$utilidades->get_a_post();
	
	$combo = new Combo_Box();
	$class_diagnosticos = new Class_Diagnosticos();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $titulo['valor_variable']; ?></title>
    <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
    <link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
    <link href="../css/azul.css" rel="stylesheet" type="text/css" />
    <link href="../css/foundation-datepicker.css" rel="stylesheet" type="text/css" />
    <link href="../css/Class_Color_Pick.css" rel="stylesheet" type="text/css" />
	
    <script type='text/javascript' src='../js/jquery_autocompletar.js'></script>
    <script type='text/javascript' src='../js/jquery-ui.js'></script>
	
    <!--<script type='text/javascript' src='../js/jquery.min.js'></script>
    <script type='text/javascript' src='../js/foundation-datepicker.js'></script>
    <script type="text/javascript" src="../js/jquery.maskedinput.js"></script>-->
    
    <script type='text/javascript' src='../js/jquery.validate.js'></script>
    <script type='text/javascript' src='../js/jquery.validate.add.js'></script>
    
    <script type='text/javascript' src='../js/ajax.js'></script>
    <script type='text/javascript' src='../js/funciones.js'></script>
    <script type='text/javascript' src='../js/validaFecha.js'></script>
    <script type='text/javascript' src='../js/Class_Diagnosticos_v1.2.js'></script>
    <script type='text/javascript' src='../js/Class_Color_Pick.js'></script>
    <script type='text/javascript' src='historia_clinica_v1.1.js'></script>
    <script type='text/javascript' src='procedimiento_qx_laser_v1.5.js'></script>
    
    <?php
    $tabla_diagnosticos = $db_diagnosticos->getDiagnosticoCiexTotal();
	$i=0;
	$cadena_diagnosticos = '';
	foreach($tabla_diagnosticos as $fila_diagnosticos){
	 	$cod_ciex = $fila_diagnosticos['codciex'];
		$nom_ciex = $fila_diagnosticos['nombre'];
		
		if ($cadena_diagnosticos != "") {
			$cadena_diagnosticos .= ",";
		}
		$cadena_diagnosticos .= "'".$nom_ciex." | ".$cod_ciex."'";
		//if(count($tabla_diagnosticos) == $i){}
		$i=$i+1;
	}
	?>
	<script>
    	$(function() {
		var Tags_diagnosticos = [<?php echo($cadena_diagnosticos) ?>];
		
		for (k=1;k<=10;k++) {
			$("#txt_busca_diagnostico_"+k).autocomplete({ source: Tags_diagnosticos });
		}
		
		});
	</script>
    
    <?php
        //Array valores esfera
        $cadena_esfera = "'0'";
        for ($i = 1; $i <= 30; $i++) {
		    if ($cadena_esfera != "") {
				$cadena_esfera .= ",";
			}
			$cadena_esfera .= "'-".$i.",00',";
			$cadena_esfera .= "'-".$i.",25',";
			$cadena_esfera .= "'-".$i.",50',";
			$cadena_esfera .= "'-".$i.",75',";
			$cadena_esfera .= "'+".$i.",00',";
			$cadena_esfera .= "'+".$i.",25',";
			$cadena_esfera .= "'+".$i.",50',";
			$cadena_esfera .= "'+".$i.",75'";
		}
		//Array valores cilindro
		$cadena_cilindro = "'0'";
        for ($i = 1; $i <= 9; $i++) {
		    if ($cadena_cilindro != "") {
				$cadena_cilindro .= ",";
			}
			$cadena_cilindro .= "'-".$i.",00',";
			$cadena_cilindro .= "'-".$i.",25',";
			$cadena_cilindro .= "'-".$i.",50',";
			$cadena_cilindro .= "'-".$i.",75'";
		}
		//Array valores eje
		$cadena_eje = "'0'";
        for ($i = 1; $i <= 180; $i++) {
		    if ($cadena_eje != "") {
				$cadena_eje .= ",";
			}
			$cadena_eje .= "'".$i."'";
		}
		//Array valores zona óptica
		$cadena_zona_op = "";
        for ($i = 6; $i <= 8; $i++) {
			for ($j = 0; $j <= 9; $j++) {
			    if ($cadena_zona_op != "") {
					$cadena_zona_op .= ",";
				}
				if ($j == 0) {
					$cadena_zona_op .= "'".$i."',";
				}
				$cadena_zona_op .= "'".$i.",".$j."'";
			}
		}
		//Array valores ablación
		$cadena_ablacion = "";
        for ($i = 10; $i <= 200; $i++) {
		    if ($cadena_ablacion != "") {
				$cadena_ablacion .= ",";
			}
			$cadena_ablacion .= "'".$i."'";
		}
		//Array valores espesor corneal base
		$cadena_ecb = "";
        for ($i = 200; $i <= 600; $i++) {
		    if ($cadena_ecb != "") {
				$cadena_ecb .= ",";
			}
			$cadena_ecb .= "'".$i."'";
		}
		//Array valores humedad
		$cadena_humedad = "";
        for ($i = 35; $i <= 69; $i++) {
			for ($j = 0; $j <= 9; $j++) {
			    if ($cadena_humedad != "") {
					$cadena_humedad .= ",";
				}
				if ($j == 0) {
					$cadena_humedad .= "'".$i."',";
				}
				$cadena_humedad .= "'".$i.",".$j."'";
			}
		}
		//Array valores temperatura
		$cadena_temperatura = "";
        for ($i = 14; $i <= 23; $i++) {
			for ($j = 0; $j <= 9; $j++) {
			    if ($cadena_temperatura != "") {
					$cadena_temperatura .= ",";
				}
				if ($j == 0) {
					$cadena_temperatura .= "'".$i."',";
				}
				$cadena_temperatura .= "'".$i.",".$j."'";
			}
		}
		//Array valores white to white
		$cadena_wtw = "";
        for ($i = 10; $i <= 13; $i++) {
			for ($j = 0; $j <= 9; $j++) {
			    if ($cadena_wtw != "") {
					$cadena_wtw .= ",";
				}
				if ($j == 0) {
					$cadena_wtw .= "'".$i."',";
				}
				$cadena_wtw .= "'".$i.",".$j."'";
			}
		}
	?>
    <script>
		var array_esfera = [<?php echo($cadena_esfera) ?>];
		var array_cilindro = [<?php echo($cadena_cilindro) ?>];
		var array_eje = [<?php echo($cadena_eje) ?>];
		var array_zona_op = [<?php echo($cadena_zona_op) ?>];
		var array_ablacion = [<?php echo($cadena_ablacion) ?>];
		var array_ecb = [<?php echo($cadena_ecb) ?>];
		var array_humedad = [<?php echo($cadena_humedad) ?>];
		var array_temperatura = [<?php echo($cadena_temperatura) ?>];
		var array_wtw = [<?php echo($cadena_wtw) ?>];
		
		$(function() {
			var Tags_esfera = [<?php echo($cadena_esfera) ?>];
			var Tags_cilindro = [<?php echo($cadena_cilindro) ?>];
			var Tags_zona_op = [<?php echo($cadena_zona_op) ?>];
			var Tags_ablacion = [<?php echo($cadena_ablacion) ?>];
			var Tags_ecb = [<?php echo($cadena_ecb) ?>];
			var Tags_humedad = [<?php echo($cadena_humedad) ?>];
			var Tags_temperatura = [<?php echo($cadena_temperatura) ?>];
			var Tags_wtw = [<?php echo($cadena_wtw) ?>];
			
			//OD
			$("#txt_esfera_od").autocomplete({ source: Tags_esfera });
			$("#txt_cilindro_od").autocomplete({ source: Tags_cilindro });
			$("#txt_zona_optica_od").autocomplete({ source: Tags_zona_op });
			$("#txt_ablacion_od").autocomplete({ source: Tags_ablacion });
			$("#txt_esp_corneal_base_od").autocomplete({ source: Tags_ecb });
			$("#txt_humedad_od").autocomplete({ source: Tags_humedad });
			$("#txt_temperatura_od").autocomplete({ source: Tags_temperatura });
			$("#txt_wtw_od").autocomplete({ source: Tags_wtw });
			//OI
			$("#txt_esfera_oi").autocomplete({ source: Tags_esfera });
			$("#txt_cilindro_oi").autocomplete({ source: Tags_cilindro });
			$("#txt_zona_optica_oi").autocomplete({ source: Tags_zona_op });
			$("#txt_ablacion_oi").autocomplete({ source: Tags_ablacion });
			$("#txt_esp_corneal_base_oi").autocomplete({ source: Tags_ecb });
			$("#txt_humedad_oi").autocomplete({ source: Tags_humedad });
			$("#txt_temperatura_oi").autocomplete({ source: Tags_temperatura });
			$("#txt_wtw_oi").autocomplete({ source: Tags_wtw });
		});
	</script>
</head>
<body>
	<?php
		@$id_admision = $utilidades->str_decode($_POST["hdd_id_admision"]);
		@$id_hc = $utilidades->str_decode($_POST["hdd_id_hc"]);
		@$credencial = $utilidades->str_decode($_POST["credencial"]);
		@$hdd_numero_menu = $utilidades->str_decode($_POST["hdd_numero_menu"]);
		
		//Obtener el valor si es Indicador de complemento
		/**
		 * 1 = Entra para ingresar complemento
		 * 0 = Entra y no ingresa Complemento
		 */
		$ind_complemento = 0;
		if (isset($_POST['ind_complemento'])) {
			$ind_complemento = intval($_POST['ind_complemento'], 10);
		}
		
		//Tipo de entrada (1: Historia Clínica - 2: Registro Cx)
		$tipo_entrada = 0;
		if (isset($_POST['tipo_entrada'])) {
			@$tipo_entrada = intval($utilidades->str_decode($_POST["tipo_entrada"]), 10);
		}
		
		$contenido->validar_seguridad(0);
	    if ($tipo_entrada == 0) {
	    	$contenido->cabecera_html();	
    	}
		
		$id_tipo_reg = 4; // Tipo de registros PROCEDIMIENTO QUIRÚRGICO
		$id_usuario = $_SESSION["idUsuario"];
		
		if ($id_hc <= 0) {
			//Se verifica si existe el registro de historia clínica
			$hc_obj = $dbCirugias->getHistoriaClinicaAdmisionQx($id_admision, 11);
			if (isset($hc_obj["id_hc"])) {
				$id_hc = $hc_obj["id_hc"];
			}
		}
		
		//Datos de la cirugía
		$cirugia_obj = $dbCirugias->get_cirugia_laser($id_hc);
		$fecha_cx = "";
		$id_convenio = "";
		$id_amb_rea = "";
		$id_fin_pro = "";
		$id_usuario_prof = "";
		$ind_reoperacion = "";
		$ind_reop_ent = "";
		$fecha_cx_ant = "";
		$observaciones_cx = "";
		$ruta_arch_cx = "";
		$ruta_arch_stickers = "";
		
		$id_tipo_laser = "";
		$id_ojo = "";
		$num_turno = "";
		$id_tecnica_od = "";
		$id_tecnica_oi = "";
		$microquerato_od = "";
		$microquerato_oi = "";
		$num_placas_od = "";
		$num_placas_oi = "";
		$tiempo_vacio_od = "";
		$tiempo_vacio_oi = "";
		$uso_cuchilla_od = "";
		$uso_cuchilla_oi = "";
		$bisagra_od = "";
		$bisagra_oi = "";
		$tiempo_qx_od = "";
		$tiempo_qx_oi = "";
		$tipo_od = "";
		$tipo_oi = "";
		$esfera_od = "";
		$esfera_oi = "";
		$cilindro_od = "";
		$cilindro_oi = "";
		$eje_od = "";
		$eje_oi = "";
		$zona_optica_od = "";
		$zona_optica_oi = "";
		$ablacion_od = "";
		$ablacion_oi = "";
		$esp_corneal_base_od = "";
		$esp_corneal_base_oi = "";
		$humedad_od = "";
		$humedad_oi = "";
		$temperatura_od = "";
		$temperatura_oi = "";
		$wtw_od = "";
		$wtw_oi = "";
		$anotaciones_ev = "";
		
		if (isset($cirugia_obj["id_hc"])) {
			$fecha_cx = trim($cirugia_obj["fecha_cx_t"]);
			$id_convenio = trim($cirugia_obj["id_convenio"]);
			$id_amb_rea = trim($cirugia_obj["id_amb_rea"]);
			$id_fin_pro = trim($cirugia_obj["id_fin_pro"]);
			$id_usuario_prof = trim($cirugia_obj["id_usuario_prof"]);
			$ind_reoperacion = trim($cirugia_obj["ind_reoperacion"]);
			$ind_reop_ent = trim($cirugia_obj["ind_reop_ent"]);
			$fecha_cx_ant = trim($cirugia_obj["fecha_cx_ant_t"]);
			$observaciones_cx = trim($cirugia_obj["observaciones_cx"]);
			$ruta_arch_cx = trim($cirugia_obj["ruta_arch_cx"]);
			$ruta_arch_stickers = trim($cirugia_obj["ruta_arch_stickers"]);
			
			$id_tipo_laser = trim($cirugia_obj["id_tipo_laser"]);
			$id_ojo = trim($cirugia_obj["id_ojo"]);
			$num_turno = trim($cirugia_obj["num_turno"]);
			$id_tecnica_od = trim($cirugia_obj["id_tecnica_od"]);
			$id_tecnica_oi = trim($cirugia_obj["id_tecnica_oi"]);
			$microquerato_od = trim($cirugia_obj["microquerato_od"]);
			$microquerato_oi = trim($cirugia_obj["microquerato_oi"]);
			$num_placas_od = trim($cirugia_obj["num_placas_od"]);
			$num_placas_oi = trim($cirugia_obj["num_placas_oi"]);
			$tiempo_vacio_od = trim($cirugia_obj["tiempo_vacio_od"]);
			$tiempo_vacio_oi = trim($cirugia_obj["tiempo_vacio_oi"]);
			$uso_cuchilla_od = trim($cirugia_obj["uso_cuchilla_od"]);
			$uso_cuchilla_oi = trim($cirugia_obj["uso_cuchilla_oi"]);
			$bisagra_od = trim($cirugia_obj["bisagra_od"]);
			$bisagra_oi = trim($cirugia_obj["bisagra_oi"]);
			$tiempo_qx_od = trim($cirugia_obj["tiempo_qx_od"]);
			$tiempo_qx_oi = trim($cirugia_obj["tiempo_qx_oi"]);
			$tipo_od = trim($cirugia_obj["tipo_od"]);
			$tipo_oi = trim($cirugia_obj["tipo_oi"]);
			$esfera_od = trim($cirugia_obj["esfera_od"]);
			$esfera_oi = trim($cirugia_obj["esfera_oi"]);
			$cilindro_od = trim($cirugia_obj["cilindro_od"]);
			$cilindro_oi = trim($cirugia_obj["cilindro_oi"]);
			$eje_od = trim($cirugia_obj["eje_od"]);
			$eje_oi = trim($cirugia_obj["eje_oi"]);
			$zona_optica_od = trim($cirugia_obj["zona_optica_od"]);
			$zona_optica_oi = trim($cirugia_obj["zona_optica_oi"]);
			$ablacion_od = trim($cirugia_obj["ablacion_od"]);
			$ablacion_oi = trim($cirugia_obj["ablacion_oi"]);
			$esp_corneal_base_od = trim($cirugia_obj["esp_corneal_base_od"]);
			$esp_corneal_base_oi = trim($cirugia_obj["esp_corneal_base_oi"]);
			$humedad_od = trim($cirugia_obj["humedad_od"]);
			$humedad_oi = trim($cirugia_obj["humedad_oi"]);
			$temperatura_od = trim($cirugia_obj["temperatura_od"]);
			$temperatura_oi = trim($cirugia_obj["temperatura_oi"]);
			$wtw_od = trim($cirugia_obj["wtw_od"]);
			$wtw_oi = trim($cirugia_obj["wtw_oi"]);
			$anotaciones_ev = trim($cirugia_obj["anotaciones_ev"]);
		}
		
		if ($ind_reoperacion == "") {
			$ind_reoperacion = "0";
		}
		if ($ind_reop_ent == "") {
			$ind_reop_ent = "-1";
		}
		
		//Datos del paciente
		$paciente_obj = $dbPacientes->getBuscarPacientesPost($id_admision);
		$paciente_obj = $paciente_obj[0];
		$id_paciente = $paciente_obj["id_paciente"];
		$nombre_paciente = $funciones_persona->obtenerNombreCompleto($paciente_obj["nombre_1"], $paciente_obj["nombre_2"], $paciente_obj["apellido_1"], $paciente_obj["apellido_2"]);
		
		$datos_paciente = $dbPacientes->getEdadPaciente($id_paciente, '');
		$edad_paciente = $datos_paciente['edad'];
		
		if ($id_convenio == "") {
			//Datos del pago
			$pago_obj = $dbPagos->get_pago($id_admision);
			$id_convenio = "";
			if (isset($pago_obj["id_convenio"])) {
				$id_convenio = $pago_obj["id_convenio"];
			}
		}
		
		//Array Si/No
		$lista_sino = array();
		$lista_sino[0]["id"] = 1;
		$lista_sino[0]["valor"] = "Si";
		$lista_sino[1]["id"] = 0;
		$lista_sino[1]["valor"] = "No";
		
		if ($tipo_entrada == 0) {
    ?>
    <div class="title-bar title_hc">
        <div class="wrapper">
            <div class="breadcrumb">
            <ul>
                <li class="breadcrumb_on">Registro de Cirug&iacute;as L&aacute;ser</li>
            </ul>
        </div>
        </div>
    </div>
    <?php
		}
		
		if ($id_hc > 0) {
			//Se inserta el registro de ingreso a la historia clínica
			$dbCirugias->crear_ingreso_hc($id_usuario, $id_paciente, $id_admision, $id_hc, 160);
		}
		
		$funciones_hc->encabezado_historia_clinica($id_paciente, $id_admision);
		
		//Se obtienen los datos del registro de historia clínica
		$admision_obj = $dbAdmision->get_admision($id_admision);
	?>
    <div class="contenedor_principal" id="id_contenedor_principal">
    	<div id="d_guardar_cx" style="width: 100%; display: block;">
        	<div class="contenedor_error" id="contenedor_error"></div>
            <div class="contenedor_exito" id="contenedor_exito"></div>
        </div>	
        <div class="formulario" id="principal_cx" style="width:100%; display:block;">
            <input type="hidden" name="hdd_id_hc" id="hdd_id_hc" value="<?php echo($id_hc); ?>" />
            <input type="hidden" name="hdd_id_admision" id="hdd_id_admision" value="<?php echo($id_admision); ?>" />
            <div class="tabs-container">
                <dl class="tabs" data-tab>
                    <dd id="proce_laser_1" class="active"><a href="#panel2-1">Procedimientos</a></dd>
                    <dd id="proce_laser_2"><a href="#panel2-2">L&aacute;ser Quir&uacute;rgico</a></dd>
                    <dd id="proce_laser_3"><a href="#panel2-3">Archivos Adjuntos</a></dd>
                    <dd id="proce_laser_4"><a href="#panel2-4">Diagn&oacute;stico</a></dd>
                </dl>
                <div class="tabs-content">
                    <div class="content active" id="panel2-1">
                        <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
                            <tr>
                                <td align="center" colspan="3">
                                    <table border="0" cellpadding="1" style="width:100%;">
                                        <tr>
                                            <td align="right" style="width:17%;">
                                                <label>Fecha de la cirug&iacute;a:</label>
                                            </td>
                                            <td align="left" style="width:33%;">
                                                <input type="text" class="input input_hc" maxlength="10" style="width:120px;" name="txt_fecha_cx" id="txt_fecha_cx" 
                                                	   onkeyup="DateFormat(this, this.value, event, false, '3');" 
                                                	   onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');"
                                                	   <?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?> 
                                                	   value="<?php echo($fecha_cx); ?>" />
                                            </td>
                                            <td align="right" style="width:17%;">
                                                <label>Convenio:</label>
                                            </td>
                                            <td align="left" style="width:33%;">
                                                <?php
													//Se obtiene el listado de convenios
													$lista_convenios = $dbConvenios->getListaConveniosActivos();
													if ($ind_complemento == 1) {
														$combo->getComboDb("cmb_convenio", $id_convenio, $lista_convenios, "id_convenio, nombre_convenio", "Seleccione un convenio", "", false, "width:240px;", "", "select_hc");	
													} else {
														$combo->getComboDb("cmb_convenio", $id_convenio, $lista_convenios, "id_convenio, nombre_convenio", "Seleccione un convenio", "", true, "width:240px;", "", "select_hc");
													}
												?>
											</td>
										</tr>
										<tr>
											<td align="right">
												<label>Tipo de atenci&oacute;n:</label>
											</td>
											<td align="left">
												<?php
													//Se obtiene el listado de ámbitos de realización
													$lista_ambitos = $dbListas->getListaDetalles(24);
													if ($ind_complemento == 1) {
														$combo->getComboDb("cmb_amb_rea", $id_amb_rea, $lista_ambitos, "id_detalle, nombre_detalle", "Seleccione un tipo de atenci&oacute;n", "", false, "width:240px;", "", "select_hc");
													} else {
														$combo->getComboDb("cmb_amb_rea", $id_amb_rea, $lista_ambitos, "id_detalle, nombre_detalle", "Seleccione un tipo de atenci&oacute;n", "", true, "width:240px;", "", "select_hc");
													}
												?>
											</td>
											<td align="right">
												<label>Finalidad:</label>
											</td>
											<td align="left">
												<?php
													//Se obtiene el listado de finalidades
													$lista_finalidades = $dbListas->getListaDetalles(25);
													if ($ind_complemento == 1) {
														$combo->getComboDb("cmb_fin_pro", $id_fin_pro, $lista_finalidades, "id_detalle, nombre_detalle", "Seleccione una finalidad", "", false, "width:240px;", "", "select_hc");
													} else {
														$combo->getComboDb("cmb_fin_pro", $id_fin_pro, $lista_finalidades, "id_detalle, nombre_detalle", "Seleccione una finalidad", "", true, "width:240px;", "", "select_hc");
													}
												?>
											</td>
										</tr>
										<tr>
											<td align="right">
												<label>Opera:</label>
											</td>
											<td align="left">
												<?php
													//Se obtiene la lista de usuarios que realizan cirugías
													$lista_usuarios_prof = $dbUsuarios->getListaUsuariosCirugia(1);
													if ($ind_complemento == 1) {
														$combo->getComboDb("cmb_usuario_prof", $id_usuario_prof, $lista_usuarios_prof, "id_usuario, nombre_completo", "Seleccione un profesional", "", false, "width:240px;", "", "select_hc");
													} else {
														$combo->getComboDb("cmb_usuario_prof", $id_usuario_prof, $lista_usuarios_prof, "id_usuario, nombre_completo", "Seleccione un profesional", "", true, "width:240px;", "", "select_hc");
													}
												?>
											</td>
											<td align="right">
												<label>Es reoperaci&oacute;n:</label>
											</td>
											<td align="left">
												<?php
													if ($ind_complemento == 1) {
														$combo->getComboDb("cmb_reoperacion", $ind_reoperacion, $lista_sino, "id, valor", " ", "seleccionar_reoperacion(this.value);", false, "width:100px;", "", "select_hc");
													} else {
														$combo->getComboDb("cmb_reoperacion", $ind_reoperacion, $lista_sino, "id, valor", " ", "seleccionar_reoperacion(this.value);", true, "width:100px;", "", "select_hc");
													}
												?>
											</td>
										</tr>
										<tr>
											<td align="right">
												<label>Del consultorio:</label>
											</td>
											<td align="left">
												<?php
													$combo->getComboDb("cmb_reop_ent", $ind_reop_ent, $lista_sino, "id, valor", " ", "", ($ind_reoperacion == "1"), "width:100px;", "", "select_hc");
												?>
											</td>
											<td align="right">
												<label>Fecha cirug&iacute;a anterior:</label>
											</td>
											<td align="left">
												<input type="text" class="input input_hc" maxlength="10" style="width:120px;" name="txt_fecha_cx_ant" id="txt_fecha_cx_ant" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" <?php if ($ind_reoperacion != "1") { ?>disabled="disabled"<?php } ?> value="<?php echo($fecha_cx_ant); ?>" />
											</td>
										</tr>
									</table>
									<br />
									<?php
										//Se carga el listado de procedimientos
										if ($id_hc > 0) {
											$lista_procedimientos = $dbCirugias->get_lista_cirugias_procedimientos($id_hc);
										} else {
											$lista_procedimientos = $dbAdmision->get_lista_proc_adicionales_adm($id_admision);
										}
									?>
									<input type="hidden" name="hdd_cant_procedimientos" id="hdd_cant_procedimientos" value="<?php echo(count($lista_procedimientos)); ?>" />
									<table class="modal_table" style="width: 100%; margin: auto;">
										<thead>
											<tr>
												<th class="th_reducido" align="center" style="width:10%;">C&oacute;digo</th>
												<th class="th_reducido" align="center" style="width:60%;">Procedimiento</th>
												<th class="th_reducido" align="center" style="width:10%;">Ojo</th>
												<th class="th_reducido" align="center" style="width:10%;">V&iacute;a</th>
												<th class="th_reducido" align="center" style="width:10%;">
													<?php
														if ($ind_complemento == 0) {
													?>
													<div class="Add-icon full" title="Agregar procedimiento quir&uacute;rgico" onclick="mostrar_agregar_procedimiento();"></div>
													<?php  	
														}
													?>
												</th>
											</tr>
										</thead>
										<?php
											//Se obtiene el listado de ojos
											$lista_ojos = $dbListas->getListaDetalles(14);
											
											//Se construye el listado de vías
											$lista_vias = array();
											$lista_vias[0]["id"] = "A";
											$lista_vias[1]["id"] = "B";
											$lista_vias[2]["id"] = "C";
											$lista_vias[3]["id"] = "D";
											$lista_vias[4]["id"] = "E";
											
											if (count($lista_procedimientos) > 0) {
												for ($i = 0; $i < count($lista_procedimientos); $i++) {
													$proc_aux = $lista_procedimientos[$i];
													$id_ojo_aux = "";
													$via_aux = "";
													if (isset($proc_aux["via_procedimiento"])) {
														$id_ojo_aux = $proc_aux["id_ojo"];
														$via_aux = $proc_aux["via_procedimiento"];
													}
										?>
										<tr id="tr_proc_cx_<?php echo($i); ?>">
											<td id="td_cod_procedimiento_<?php echo($i); ?>" class="td_reducido" align="center" valign="middle">
												<?php echo($proc_aux["cod_procedimiento"]); ?>
											</td>
											<td id="td_nombre_procedimiento_<?php echo($i); ?>" class="td_reducido" align="left" valign="middle">
												<?php echo($proc_aux["nombre_procedimiento"]); ?>
											</td>
											<td class="td_reducido" align="center" valign="middle">
												<?php
													if ($ind_complemento == 1) {
														$combo->getComboDb("cmb_ojo_".$i, $id_ojo_aux, $lista_ojos, "id_detalle, nombre_detalle", " ", "", false, "width:70px;");
													} else {
														$combo->getComboDb("cmb_ojo_".$i, $id_ojo_aux, $lista_ojos, "id_detalle, nombre_detalle", " ", "", true, "width:70px;");
													}
												?>
											</td>
											<td class="td_reducido" align="center" valign="middle">
												<?php
													if ($ind_complemento == 1) {
														$combo->getComboDb("cmb_via_".$i, $via_aux, $lista_vias, "id, id", " ", "", false, "width:70px;");
													} else {
														$combo->getComboDb("cmb_via_".$i, $via_aux, $lista_vias, "id, id", " ", "", true, "width:70px;");
													}
												?>
											</td>
											<td class="td_reducido" align="center" valign="middle">
												<input type="hidden" name="hdd_cod_procedimiento_<?php echo($i); ?>" id="hdd_cod_procedimiento_<?php echo($i); ?>" value="<?php echo($proc_aux["cod_procedimiento"]); ?>" />
												<?php
													if ($ind_complemento == 0) {
												?>
												<div class="Error-icon" title="Borrar procedimiento quir&uacute;rgico" onclick="quitar_procedimiento(<?php echo($i); ?>);"></div>
												<?php
													}
												?>
											</td>
										</tr>
										<?php
												}
											}
											
											for ($i = count($lista_procedimientos); $i < 50; $i++) {
										?>
										<tr id="tr_proc_cx_<?php echo($i); ?>" style="display:none;">
											<td id="td_cod_procedimiento_<?php echo($i); ?>" class="td_reducido" align="center" valign="middle"></td>
											<td id="td_nombre_procedimiento_<?php echo($i); ?>" class="td_reducido" align="left" valign="middle"></td>
											<td class="td_reducido" align="center" valign="middle">
												<?php
													$combo->getComboDb("cmb_ojo_".$i, "", $lista_ojos, "id_detalle, nombre_detalle", " ", "", true, "width:70px;");
												?>
											</td>
											<td class="td_reducido" align="center" valign="middle">
												<?php
													$combo->getComboDb("cmb_via_".$i, "", $lista_vias, "id, id", " ", "", true, "width:70px;");
												?>
											</td>
											<td class="td_reducido" align="center" valign="middle">
												<input type="hidden" name="hdd_cod_procedimiento_<?php echo($i); ?>" id="hdd_cod_procedimiento_<?php echo($i); ?>" value="" />
												<div class="Error-icon" title="Borrar procedimiento quir&uacute;rgico" onclick="quitar_procedimiento(<?php echo($i); ?>);"></div>
											</td>
										</tr>
										<?php
											}
										?>
									</table>
                                    <table border="0" cellpadding="3" style="width:100%;">
										<tr>
											<td align="center" colspan="4">
												<h6>Observaciones</h6>
											</td>
										</tr>
										<tr>
											<td align="center" colspan="4">
												<textarea class="textarea_alto" id="txt_observaciones_cx" nombre="txt_observaciones_cx" onblur="trim_cadena(this);" style="width:100%;"<?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?> ><?php echo($observaciones_cx); ?></textarea>
											</td>
										</tr>
                                     </table>
								</td>
							</tr>
						</table>
					</div>
					<div class="content" id="panel2-2">
						<table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
							<tr>
								<td align="center" colspan="3">
                                    <table border="0" cellpadding="1" style="width:100%;">
                                        <tr>
                                            <td align="right" style="width:17%;">
                                                <label>Tipo de l&aacute;ser:</label>
                                            </td>
                                            <td align="left" style="width:33%;">
                                                <?php
													//Se obtiene el listado de tipos de láser
													$lista_tipos_laser = $dbListas->getListaDetalles(16);
													if ($ind_complemento == 1) {
														$combo->getComboDb("cmb_tipo_laser", $id_tipo_laser, $lista_tipos_laser, "id_detalle, nombre_detalle", "Seleccione un tipo", "", false, "width:240px;", "", "select_hc");
													} else {
														$combo->getComboDb("cmb_tipo_laser", $id_tipo_laser, $lista_tipos_laser, "id_detalle, nombre_detalle", "Seleccione un tipo", "", true, "width:240px;", "", "select_hc");
													}
												?>
                                            </td>
                                            <td align="right" style="width:17%;">
                                                <label>Ojo a operar:</label>
                                            </td>
                                            <td align="left" style="width:33%;">
                                                <?php
													if ($ind_complemento == 1) {
														$combo->getComboDb("cmb_ojo", $id_ojo, $lista_ojos, "id_detalle, nombre_detalle", "Seleccione un ojo", "seleccionar_ojo(this.value, 1);", false, "width:240px;", "", "select_hc");
													} else {
														$combo->getComboDb("cmb_ojo", $id_ojo, $lista_ojos, "id_detalle, nombre_detalle", "Seleccione un ojo", "seleccionar_ojo(this.value, 0);", true, "width:240px;", "", "select_hc");
													}
												?>
											</td>
										</tr>
										<tr>
											<td align="right">
												<label>Turno:</label>
											</td>
											<td align="left">
												<input type="text" name="txt_num_turno" id="txt_num_turno" 
												  value="<?php echo($num_turno); ?>" 
												  onkeypress="return solo_numeros(event, false);"
												  <?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?>
												  maxlength="4" style="width:50px;" class="input input_hc" />
											</td>
										</tr>
                                        <tr>
                                        	<td align="center" colspan="4">
                                            	<table border="0" cellpadding="0" cellspacing="1" style="width:100%;">
                                                    <tr>
                                                        <td align="center" colspan="3" class="td_tabla">
                                                            <div class="odoi_t">
                                                            <div class="od_t"><h5 style="margin: 0px;">OD</h5></div>
                                                            <div class="oi_t"><h5 style="margin: 0px;">OI</h5></div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr style="height:10px;"></tr>
                                                    <?php
                                                    	//Se obtiene la lista de técnicas de cirugía láser
														$lista_tecnicas_laser = $dbListas->getListaDetalles(26);
													?>
                                                    <tr>
                                                    	<td align="center" style="width:33%;">
                                                        	<?php
																if ($ind_complemento == 1) {
																	$combo->getComboDb("cmb_tecnica_od", $id_tecnica_od, $lista_tecnicas_laser, "id_detalle,nombre_detalle", " ", "", false, "width:240px;", "", "select_hc");
																} else {
																	$combo->getComboDb("cmb_tecnica_od", $id_tecnica_od, $lista_tecnicas_laser, "id_detalle,nombre_detalle", " ", "", true, "width:240px;", "", "select_hc");
																}
															?>
                                                        </td>
                                                        <td align="center" style="width:34%;">
                                                        	<label>T&eacute;cnica</label>
                                                        </td>
                                                    	<td align="center" style="width:33%;">
                                                        	<?php
																if ($ind_complemento == 1) {
																	$combo->getComboDb("cmb_tecnica_oi", $id_tecnica_oi, $lista_tecnicas_laser, "id_detalle,nombre_detalle", " ", "", false, "width:240px;", "", "select_hc");
																} else {
																	$combo->getComboDb("cmb_tecnica_oi", $id_tecnica_oi, $lista_tecnicas_laser, "id_detalle,nombre_detalle", " ", "", true, "width:240px;", "", "select_hc");
																}
															?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                    	<td align="center">
                                                        	<input type="text" name="txt_microquerato_od" id="txt_microquerato_od" 
                                                        		value="<?php echo($microquerato_od); ?>" 
                                                        		maxlength="200" style="width:240px;"
                                                        		<?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?> 
                                                        		onblur="trim_cadena(this);" class="input input_hc" />
                                                        </td>
                                                        <td align="center">
                                                        	<label>Microquerato</label>
                                                        </td>
                                                    	<td align="center">
                                                        	<input type="text" name="txt_microquerato_oi" id="txt_microquerato_oi" 
                                                        	   value="<?php echo($microquerato_oi); ?>" 
                                                        	   maxlength="200" style="width:240px;" 
                                                        	   <?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?>
                                                        	   onblur="trim_cadena(this);" class="input input_hc" />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                    	<td align="center">
                                                        	<input type="text" name="txt_num_placas_od" id="txt_num_placas_od" 
                                                        		value="<?php echo($num_placas_od); ?>" 
                                                        		maxlength="4" style="width:80px;"
                                                        		<?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?> 
                                                        		onkeypress="return solo_numeros(event, false);" class="input input_hc" />
                                                        </td>
                                                        <td align="center">
                                                        	<label>Placas</label>
                                                        </td>
                                                    	<td align="center">
                                                        	<input type="text" name="txt_num_placas_oi" id="txt_num_placas_oi" 
                                                        		value="<?php echo($num_placas_oi); ?>" 
                                                        		maxlength="4" style="width:80px;" 
                                                        		<?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?>
                                                        		onkeypress="return solo_numeros(event, false);" class="input input_hc" />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                    	<td align="center">
                                                        	<input type="text" name="txt_tiempo_vacio_od" id="txt_tiempo_vacio_od" 
                                                        		value="<?php echo($tiempo_vacio_od); ?>" 
                                                        		maxlength="3" style="width:80px;" 
                                                        		<?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?>
                                                        		onkeypress="return solo_numeros(event, false);" class="input input_hc" />
                                                        </td>
                                                        <td align="center">
                                                        	<label>Tiempo vac&iacute;o</label>
                                                        </td>
                                                    	<td align="center">
                                                        	<input type="text" name="txt_tiempo_vacio_oi" id="txt_tiempo_vacio_oi" 
                                                        		value="<?php echo($tiempo_vacio_oi); ?>" 
                                                        		maxlength="3" style="width:80px;" 
                                                        		<?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?>
                                                        		onkeypress="return solo_numeros(event, false);" class="input input_hc" />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                    	<td align="center">
                                                        	<input type="text" name="txt_uso_cuchilla_od" id="txt_uso_cuchilla_od" 
                                                        		value="<?php echo($uso_cuchilla_od); ?>" 
                                                        		maxlength="1" style="width:80px;" 
                                                        		<?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?>
                                                        		onkeypress="return solo_numeros(event, false);" class="input input_hc" />
                                                        </td>
                                                        <td align="center">
                                                        	<label>Uso de cuchilla</label>
                                                        </td>
                                                    	<td align="center">
                                                        	<input type="text" name="txt_uso_cuchilla_oi" id="txt_uso_cuchilla_oi" 
                                                        		value="<?php echo($uso_cuchilla_oi); ?>" 
                                                        		maxlength="1" style="width:80px;"
                                                        		<?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?> 
                                                        		onkeypress="return solo_numeros(event, false);" class="input input_hc" />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                    	<td align="center">
                                                        	<input type="text" name="txt_bisagra_od" id="txt_bisagra_od" 
                                                        		value="<?php echo($bisagra_od); ?>" 
                                                        		maxlength="200" style="width:240px;"
                                                        		<?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?> 
                                                        		onblur="trim_cadena(this);" class="input input_hc" />
                                                        </td>
                                                        <td align="center">
                                                        	<label>Bisagra</label>
                                                        </td>
                                                    	<td align="center">
                                                        	<input type="text" name="txt_bisagra_oi" id="txt_bisagra_oi" 
                                                        		value="<?php echo($bisagra_oi); ?>" 
                                                        		maxlength="200" style="width:240px;"
                                                        		<?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?> 
                                                        		onblur="trim_cadena(this);" class="input input_hc" />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                    	<td align="center">
                                                        	<input type="text" name="txt_tiempo_qx_od" id="txt_tiempo_qx_od" 
                                                        		value="<?php echo($tiempo_qx_od); ?>" 
                                                        		maxlength="3" style="width:80px;"
                                                        		<?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?> 
                                                        		onkeypress="return solo_numeros(event, false);" class="input input_hc" />
                                                        </td>
                                                        <td align="center">
                                                        	<label>Tiempo quir&uacute;rgico *</label>
                                                        </td>
                                                    	<td align="center">
                                                        	<input type="text" name="txt_tiempo_qx_oi" id="txt_tiempo_qx_oi" 
                                                        		value="<?php echo($tiempo_qx_oi); ?>" 
                                                        		maxlength="3" style="width:80px;"
                                                        		<?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?> 
                                                        		onkeypress="return solo_numeros(event, false);" class="input input_hc" />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                    	<td align="left" colspan="3">
                                                        	* Tiempo prel&aacute;ser (Tiempo entre el levantamiento del disco y la aplicaci&oacute;n del l&aacute;ser)
                                                        </td>
                                                    </tr>
                                                    <tr style="height:20px;"></tr>
                                                    <tr>
                                                    	<td align="center" colspan="3">
                                                        	<h6><b>Datos de la Operaci&oacute;n Digitados</b></h6>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center" colspan="3" class="td_tabla">
                                                            <div class="odoi_t">
                                                            <div class="od_t"><h5 style="margin: 0px;">OD</h5></div>
                                                            <div class="oi_t"><h5 style="margin: 0px;">OI</h5></div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr style="height:10px;"></tr>
                                                    <tr>
                                                        <td align="center">
                                                            <input type="text" name="txt_tipo_od" id="txt_tipo_od" 
                                                                value="<?php echo($tipo_od); ?>" 
                                                                maxlength="200" style="width:240px;"
                                                                <?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?> 
                                                                onblur="trim_cadena(this);" class="input input_hc" />
                                                        </td>
                                                        <td align="center">
                                                            <label>Tipo</label>
                                                        </td>
                                                        <td align="center">
                                                            <input type="text" name="txt_tipo_oi" id="txt_tipo_oi" 
                                                                value="<?php echo($tipo_oi); ?>" 
                                                                maxlength="200" style="width:240px;"
                                                                <?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?> 
                                                                onblur="trim_cadena(this);" class="input input_hc" />
                                                        </td>
                                                    </tr>
                                                    <?php
														if ($id_hc == 0) {
															//Se buscan los datos de esfera, cilindro y eje en la consulta prequirúrgica
															$cons_preqx_laser_obj = $dbConsultaPreqxLaser->getConsultaPreqxLaserAdmision($id_admision);
															
															if (isset($cons_preqx_laser_obj["id_hc"])) {
																$esfera_od = $cons_preqx_laser_obj["nomograma_esfera_od"];
																$esfera_oi = $cons_preqx_laser_obj["nomograma_esfera_oi"];
																$cilindro_od = $cons_preqx_laser_obj["nomograma_cilindro_od"];
																$cilindro_oi = $cons_preqx_laser_obj["nomograma_cilindro_oi"];
																$eje_od = $cons_preqx_laser_obj["nomograma_eje_od"];
																$eje_oi = $cons_preqx_laser_obj["nomograma_eje_oi"];
															}
														}
													?>
                                                    <tr>
                                                        <td align="center">
                                                            <input type="text" name="txt_esfera_od" id="txt_esfera_od" 
                                                                class="input_hc input" style="width:80px;" 
                                                                value="<?php echo($esfera_od); ?>" maxlength="10" 
                                                                onkeypress="formato_hc(event, this);" 
                                                                <?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?>
                                                                onBlur="validar_array(array_esfera, this);" />
                                                        </td>
                                                        <td align="center">
                                                            <label>Esfera</label>
                                                        </td>
                                                        <td align="center">
                                                            <input type="text" name="txt_esfera_oi" id="txt_esfera_oi" 
                                                                class="input_hc input" style="width:80px;" 
                                                                value="<?php echo($esfera_oi); ?>" maxlength="10" 
                                                                onkeypress="formato_hc(event, this);" 
                                                                <?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?>
                                                                onBlur="validar_array(array_esfera, this);" />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center">
                                                            <input type="text" name="txt_cilindro_od" id="txt_cilindro_od" 
                                                                class="input_hc input" style="width:80px;" 
                                                                value="<?php echo($cilindro_od); ?>" maxlength="10" 
                                                                onkeypress="formato_hc(event, this);"
                                                                <?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?> 
                                                                onBlur="validar_array(array_cilindro, this);" />
                                                        </td>
                                                        <td align="center">
                                                            <label>Cilindro</label>
                                                        </td>
                                                        <td align="center">
                                                            <input type="text" name="txt_cilindro_oi" id="txt_cilindro_oi" 
                                                                class="input_hc input" style="width:80px;" 
                                                                value="<?php echo($cilindro_oi); ?>" maxlength="10" 
                                                                onkeypress="formato_hc(event, this);" 
                                                                <?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?>
                                                                onBlur="validar_array(array_cilindro, this);" />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center">
                                                            <input type="text" name="txt_eje_od" id="txt_eje_od" 
                                                                class="input_hc input" style="width:80px;" 
                                                                value="<?php echo($eje_od); ?>" maxlength="10" 
                                                                onkeypress="formato_hc(event, this);" 
                                                                <?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?>
                                                                onBlur="validar_array(array_eje, this);" />
                                                        </td>
                                                        <td align="center">
                                                            <label>Eje</label>
                                                        </td>
                                                        <td align="center">
                                                            <input type="text" name="txt_eje_oi" id="txt_eje_oi" 
                                                                class="input_hc input" style="width:80px;" 
                                                                value="<?php echo($eje_oi); ?>" maxlength="10" 
                                                                onkeypress="formato_hc(event, this);" 
                                                                <?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?>
                                                                onBlur="validar_array(array_eje, this);" />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center">
                                                            <input type="text" name="txt_zona_optica_od" id="txt_zona_optica_od" 
                                                                class="input_hc input" style="width:80px;" 
                                                                value="<?php echo($zona_optica_od); ?>" maxlength="10" 
                                                                onkeypress="formato_hc(event, this);" 
                                                                <?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?>
                                                                onBlur="validar_array(array_zona_op, this);" />
                                                        </td>
                                                        <td align="center">
                                                            <label>Zona &oacute;ptica</label>
                                                        </td>
                                                        <td align="center">
                                                            <input type="text" name="txt_zona_optica_oi" id="txt_zona_optica_oi" 
                                                                class="input_hc input" style="width:80px;" 
                                                                value="<?php echo($zona_optica_oi); ?>" maxlength="10" 
                                                                onkeypress="formato_hc(event, this);" 
                                                                <?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?>
                                                                onBlur="validar_array(array_zona_op, this);" />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center">
                                                            <input type="text" name="txt_ablacion_od" id="txt_ablacion_od" 
                                                                class="input_hc input" style="width:80px;" 
                                                                value="<?php echo($ablacion_od); ?>" maxlength="3" 
                                                                onkeypress="formato_hc(event, this);" 
                                                                <?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?>
                                                                onblur="validar_array(array_ablacion, this);" />
                                                        </td>
                                                        <td align="center">
                                                            <label>Ablaci&oacute;n</label>
                                                        </td>
                                                        <td align="center">
                                                            <input type="text" name="txt_ablacion_oi" id="txt_ablacion_oi" 
                                                                class="input_hc input" style="width:80px;" 
                                                                value="<?php echo($ablacion_oi); ?>" maxlength="3" 
                                                                onkeypress="formato_hc(event, this);" 
                                                                <?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?>
                                                                onblur="validar_array(array_ablacion, this);" />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center">
                                                            <input type="text" name="txt_esp_corneal_base_od" id="txt_esp_corneal_base_od" 
                                                                class="input_hc input" style="width:80px;" 
                                                                value="<?php echo($esp_corneal_base_od); ?>" maxlength="3" 
                                                                onkeypress="formato_hc(event, this);" 
                                                                <?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?>
                                                                onblur="validar_array(array_ecb, this);" />
                                                        </td>
                                                        <td align="center">
                                                            <label>Espesor corneal base</label>
                                                        </td>
                                                        <td align="center">
                                                            <input type="text" name="txt_esp_corneal_base_oi" id="txt_esp_corneal_base_oi" 
                                                                class="input_hc input" style="width:80px;" 
                                                                value="<?php echo($esp_corneal_base_oi); ?>" maxlength="3" 
                                                                onkeypress="formato_hc(event, this);" 
                                                                <?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?>
                                                                onblur="validar_array(array_ecb, this);" />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center">
                                                            <input type="text" name="txt_humedad_od" id="txt_humedad_od" 
                                                                class="input_hc input" style="width:80px;" 
                                                                value="<?php echo($humedad_od); ?>" maxlength="10" 
                                                                onkeypress="formato_hc(event, this);" 
                                                                <?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?>
                                                                onBlur="validar_array(array_humedad, this);" />
                                                        </td>
                                                        <td align="center">
                                                            <label>Humedad</label>
                                                        </td>
                                                        <td align="center">
                                                            <input type="text" name="txt_humedad_oi" id="txt_humedad_oi" 
                                                                class="input_hc input" style="width:80px;" 
                                                                value="<?php echo($humedad_oi); ?>" maxlength="10" 
                                                                onkeypress="formato_hc(event, this);" 
                                                                <?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?>
                                                                onBlur="validar_array(array_humedad, this);" />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center">
                                                            <input type="text" name="txt_temperatura_od" id="txt_temperatura_od" 
                                                                class="input_hc input" style="width:80px;" 
                                                                value="<?php echo($temperatura_od); ?>" maxlength="10" 
                                                                onkeypress="formato_hc(event, this);" 
                                                                <?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?>
                                                                onBlur="validar_array(array_temperatura, this);" />
                                                        </td>
                                                        <td align="center">
                                                            <label>Temperatura</label>
                                                        </td>
                                                        <td align="center">
                                                            <input type="text" name="txt_temperatura_oi" id="txt_temperatura_oi" 
                                                                class="input_hc input" style="width:80px;" 
                                                                value="<?php echo($temperatura_oi); ?>" maxlength="10" 
                                                                onkeypress="formato_hc(event, this);" 
                                                                <?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?>
                                                                onBlur="validar_array(array_temperatura, this);" />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center">
                                                            <input type="text" name="txt_wtw_od" id="txt_wtw_od" 
                                                                class="input_hc input" style="width:80px;" 
                                                                value="<?php echo($wtw_od); ?>" maxlength="10" 
                                                                onkeypress="formato_hc(event, this);" 
                                                                <?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?>
                                                                onBlur="validar_array(array_wtw, this);" />
                                                        </td>
                                                        <td align="center">
                                                            <label>White to white</label>
                                                        </td>
                                                        <td align="center">
                                                            <input type="text" name="txt_wtw_oi" id="txt_wtw_oi" 
                                                                class="input_hc input" style="width:80px;" 
                                                                value="<?php echo($wtw_oi); ?>" maxlength="10" 
                                                                onkeypress="formato_hc(event, this);" 
                                                                <?php if ($ind_complemento == 1) { ?> disabled="disabled"<?php } ?>
                                                                onBlur="validar_array(array_wtw, this);" />
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <br />
                                    <table border="0" cellpadding="3" style="width:100%;">
										<tr>
											<td align="center" colspan="4">
												<h6><b>Evaluaci&oacute;n</b></h6>
											</td>
										</tr>
										<tr>
											<td align="center" colspan="4">
												<textarea class="textarea_alto" id="txt_anotaciones_ev" nombre="txt_anotaciones_ev" 
													onblur="trim_cadena(this);" 
													style="width:100%;" 
													<?php if ($ind_complemento == 0) { ?> disabled="disabled"<?php } ?>   ><?php echo($anotaciones_ev); ?></textarea>
											</td>
										</tr>
										<?php
											if ($ind_complemento == 1) {
												// Si es para realizar el complemento
										?>
										<tr>
											<td align="center" colspan="4">
												<input type="button" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Guardar Evaluaci&oacute;n" onclick="guardar_evaluacion_qx();" />
											</td>
										</tr>
										<?php
											}
										?>
                                     </table>
                                </td>
                            </tr>
                        </table>
                    </div>
					<div class="content" id="panel2-3">
						<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:98%;">
							<tr>
								<td align="center" colspan="3">
									<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%;">
                                    	<tr>
                                        	<td align="center" colspan="2">
                                            	<h6><b>Hoja de Stickers</b></h6>
                                            </td>
                                        </tr>
										<tr>
											<td align="right" style="width:15%;">
												<label class="inline" for="cmb_examen_<?php echo($i); ?>">Cargar Archivo</label>
											</td>
											<td align="left" style="width:85%;">
												<input type="hidden" name="hdd_ruta_arch_stickers" id="hdd_ruta_arch_stickers" value="<?php echo($ruta_arch_stickers); ?>" />
												<form name="frm_arch_stickers" id="frm_arch_stickers" target="ifr_arch_stickers" action="procedimiento_qx_ajax.php" method="post" enctype="multipart/form-data">
													<input type="hidden" name="opcion" id="opcion" value="6" />
													<input type="hidden" name="hdd_id_hc_stickers" id="hdd_id_hc_stickers" value="<?php echo($id_hc); ?>" />
													<input type="file" name="fil_hoja_stickers" id="fil_hoja_stickers" />
												</form>
												<div style="display:none;">
													<iframe name="ifr_arch_stickers" id="ifr_arch_stickers"></iframe>
												</div>
											</td>
										</tr>
										<tr>
											<td align="center" colspan="2">
												<div id="d_archivo_stickers" class="div_marco" style="height:350px;"></div>
												<script id="ajax" type="text/javascript">
													cargar_archivo(0, 1);
												</script>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
                    </div>
					<div class="content" id="panel2-4">
						<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:90%;">
							<tr>
								<td align="center" colspan="3" class="td_tabla tabs-content">
									<h5 style="margin: 10px"></h5>
									<?php
										$class_diagnosticos->getFormularioDiagnosticos($id_hc);
									?>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
            <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:90%;">
   	            <tr valign="top">
       	            <td colspan='3'>
                        <?php
							switch ($tipo_entrada) {
								case 1: //Historia clínica
						?>
						<input class="btnPrincipal" type="button" id="btn_imprimir" nombre="btn_imprimir" value="Imprimir" onclick="imprimir_cirugia();" />
						<?php
									break;
								case 2: //Registro Cx
						?>
						<input class="btnPrincipal" type="button" id="btn_imprimir" nombre="btn_imprimir" value="Imprimir" onclick="guardar_cirugia(2, 1);" />
						<?php
									break;
							}
							
							//Para verificar que tiene permiso de hacer cambio
							$ind_editar = 1;
							if ($id_hc > 0) {
								$ind_editar = $dbCirugias->getIndicadorEdicion($id_hc, $horas_edicion['valor_variable']);
							}
							
							if ($ind_complemento == 0) {
								//Si no es para realizar el complemento		
								if ($ind_editar == 1) {
									switch ($tipo_entrada) {
										case 1: //Historia clínica
						?>
                   	    <input type="submit" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Guardar" onclick="guardar_cirugia(3, 0);" />
                        <?php
											break;
											
										case 2: //Registro Cx
						?>
           	            <input type="button" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Guardar cambios" onclick="guardar_cirugia(2, 0);" />
                   	    <input type="submit" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Finalizar cirug&iacute;a" onclick="guardar_cirugia(1, 0);" />
                        <?php
											break;
									}
								}
							}
						?>
                    </td>
                </tr>
   	        </table>
	    </div>
    </div>
    <?php
    if($id_ojo != ''){
		?>
	    <script id='ajax'>
			seleccionar_ojo('<?php echo($id_ojo); ?>', '<?php echo($ind_complemento); ?>');
		</script>
		<?php
    }
	?>
    
    
    <script type='text/javascript' src='../js/foundation.min.js'></script>
    <script>
		$(document).foundation();
		
		/*$(function() {
			window.prettyPrint && prettyPrint();
			
			$('#txt_fecha_cx').fdatepicker({
				format: 'dd/mm/yyyy'
			});
			$('#txt_fecha_cx_ant').fdatepicker({
				format: 'dd/mm/yyyy'
			});
		});*/
    </script>
    <?php
	   if ($tipo_entrada == 0) {
	    	$contenido->ver_historia($id_paciente);
		    $contenido->footer();
	   } else {
			$contenido->footer_iframe();
	   }
	?>
</body>
</html>
