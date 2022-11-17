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
	
    <!--<script type='text/javascript' src='../js/jquery.min.js'></script>-->
    <script type='text/javascript' src='../js/foundation-datepicker.js'></script>
    <!--<script type="text/javascript" src="../js/jquery.maskedinput.js"></script>-->
    
    <script type='text/javascript' src='../js/jquery.validate.js'></script>
    <script type='text/javascript' src='../js/jquery.validate.add.js'></script>
    
    <script type='text/javascript' src='../js/ajax.js'></script>
    <script type='text/javascript' src='../js/funciones.js'></script>
    <script type='text/javascript' src='../js/validaFecha.js'></script>
    <script type='text/javascript' src='../js/Class_Diagnosticos_v1.2.js'></script>
    <script type='text/javascript' src='../js/Class_Color_Pick.js'></script>
    <script type='text/javascript' src='historia_clinica_v1.1.js'></script>
    <script type='text/javascript' src='procedimiento_qx_v1.5.js'></script>
    
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
</head>
<body>
	<?php
		@$id_admision = $utilidades->str_decode($_POST["hdd_id_admision"]);
		@$id_hc = $utilidades->str_decode($_POST["hdd_id_hc"]);
		@$credencial = $utilidades->str_decode($_POST["credencial"]);
		@$hdd_numero_menu = $utilidades->str_decode($_POST["hdd_numero_menu"]);
		
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
			$hc_obj = $dbCirugias->getHistoriaClinicaAdmisionQx($id_admision, 4);
			if (isset($hc_obj["id_hc"])) {
				$id_hc = $hc_obj["id_hc"];
			}
		}
		
		//Datos de la cirugía
		$cirugia_obj = $dbCirugias->get_cirugia($id_hc);
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
                <li class="breadcrumb_on">Registro de Cirug&iacute;as</li>
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
            <input type="hidden" value="<?php echo($id_hc); ?>" name="hdd_id_hc" id="hdd_id_hc" />
            <div class="tabs-container">
                <dl class="tabs" data-tab>
                    <dd id="panel_proce_1" class="active"><a href="#panel2-1">Procedimientos</a></dd>
                    <dd id="panel_proce_2"><a href="#panel2-2">Archivos Adjuntos</a></dd>
                    <dd id="panel_proce_3"><a href="#panel2-3">Diagn&oacute;stico</a></dd>
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
                                                <input type="text" class="input input_hc" maxlength="10" style="width:120px;" name="txt_fecha_cx" id="txt_fecha_cx" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" value="<?php echo($fecha_cx); ?>" />
                                            </td>
                                            <td align="right" style="width:17%;">
                                                <label>Convenio:</label>
                                            </td>
                                            <td align="left" style="width:33%;">
                                                <?php
													//Se obtiene el listado de convenios
													$lista_convenios = $dbConvenios->getListaConveniosActivos();
													
													$combo->getComboDb("cmb_convenio", $id_convenio, $lista_convenios, "id_convenio, nombre_convenio", "Seleccione un convenio", "", true, "width:240px;", "", "select_hc");
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
													
													$combo->getComboDb("cmb_amb_rea", $id_amb_rea, $lista_ambitos, "id_detalle, nombre_detalle", "Seleccione un tipo de atenci&oacute;n", "", true, "width:240px;", "", "select_hc");
												?>
											</td>
											<td align="right">
												<label>Finalidad:</label>
											</td>
											<td align="left">
												<?php
													//Se obtiene el listado de finalidades
													$lista_finalidades = $dbListas->getListaDetalles(25);
													
													$combo->getComboDb("cmb_fin_pro", $id_fin_pro, $lista_finalidades, "id_detalle, nombre_detalle", "Seleccione una finalidad", "", true, "width:240px;", "", "select_hc");
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
													
													$combo->getComboDb("cmb_usuario_prof", $id_usuario_prof, $lista_usuarios_prof, "id_usuario, nombre_completo", "Seleccione un profesional", "", true, "width:240px;", "", "select_hc");
												?>
											</td>
											<td align="right">
												<label>Es reoperaci&oacute;n:</label>
											</td>
											<td align="left">
												<?php
													$combo->getComboDb("cmb_reoperacion", $ind_reoperacion, $lista_sino, "id, valor", " ", "seleccionar_reoperacion(this.value);", true, "width:100px;", "", "select_hc");
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
													<div class="Add-icon full" title="Agregar procedimiento quir&uacute;rgico" onclick="mostrar_agregar_procedimiento();"></div>
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
													$combo->getComboDb("cmb_ojo_".$i, $id_ojo_aux, $lista_ojos, "id_detalle, nombre_detalle", " ", "", true, "width:70px;");
												?>
											</td>
											<td class="td_reducido" align="center" valign="middle">
												<?php
													$combo->getComboDb("cmb_via_".$i, $via_aux, $lista_vias, "id, id", " ", "", true, "width:70px;");
												?>
											</td>
											<td class="td_reducido" align="center" valign="middle">
												<input type="hidden" name="hdd_cod_procedimiento_<?php echo($i); ?>" id="hdd_cod_procedimiento_<?php echo($i); ?>" value="<?php echo($proc_aux["cod_procedimiento"]); ?>" />
												<div class="Error-icon" title="Borrar procedimiento quir&uacute;rgico" onclick="quitar_procedimiento(<?php echo($i); ?>);"></div>
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
												<textarea class="textarea_alto" id="txt_observaciones_cx" nombre="txt_observaciones_cx" onblur="trim_cadena(this);" style="width:100%;"><?php echo($observaciones_cx); ?></textarea>
											</td>
										</tr>
                                     </table>
								</td>
							</tr>
						</table>
					</div>
					<div class="content" id="panel2-2">
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
                                        <tr style="height:15px;"></tr>
                                    	<tr>
                                        	<td align="center" colspan="2">
                                            	<h6><b>Adjunto SAP</b></h6>
                                            </td>
                                        </tr>
										<tr>
											<td align="right" style="width:15%;">
												<label class="inline" for="cmb_examen_<?php echo($i); ?>">Cargar Archivo</label>
											</td>
											<td align="left" style="width:85%;">
												<input type="hidden" name="hdd_ruta_arch_cx" id="hdd_ruta_arch_cx" value="<?php echo($ruta_arch_cx); ?>" />
												<form name="frm_arch_cx" id="frm_arch_cx" target="ifr_carga_arch" action="procedimiento_qx_ajax.php" method="post" enctype="multipart/form-data">
													<input type="hidden" name="opcion" id="opcion" value="4" />
													<input type="hidden" name="hdd_id_hc_cx" id="hdd_id_hc_cx" value="<?php echo($id_hc); ?>" />
													<input type="hidden" name="hdd_id_admision" id="hdd_id_admision" value="<?php echo($id_admision); ?>" />
													<input type="file" name="fil_arch_cx" id="fil_arch_cx" />
												</form>
												<div style="display:none;">
													<iframe name="ifr_carga_arch" id="ifr_carga_arch"></iframe>
												</div>
											</td>
										</tr>
										<tr>
											<td align="center" colspan="2">
												<div id="d_archivo_cx" class="div_marco" style="height:350px;"></div>
												<script id="ajax" type="text/javascript">
													cargar_archivo(0, 2);
												</script>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
					<div class="content" id="panel2-3">
						<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:98%;">
							<tr>
								<td align="center" colspan="3">
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
            <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:98%;">
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
						?>
                    </td>
                </tr>
   	        </table>
	    </div>
    </div>
    <script type='text/javascript' src='../js/foundation.min.js'></script>
    <script>
		$(document).foundation();
		
		$(function() {
			window.prettyPrint && prettyPrint();
			
			$('#txt_fecha_cx').fdatepicker({
				format: 'dd/mm/yyyy'
			});
			$('#txt_fecha_cx_ant').fdatepicker({
				format: 'dd/mm/yyyy'
			});
		});
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
