<?php
	session_start();
	
	require_once("../db/DbVariables.php");
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbConsultaDermatologia.php");
	require_once("../db/DbAdmision.php");
	require_once("../db/DbTiposCitas.php");
	require_once("../db/DbPacientes.php");
	require_once("../db/DbDiagnosticos.php");
	require_once("../db/DbTiposCitasDetalle.php");
	require_once("../db/DbPlanes.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/Class_Diagnosticos.php");
	require_once("../funciones/Class_Formulacion.php");
	require_once("../funciones/Class_Espera_Dilatacion.php");
	require_once("../funciones/Class_Solic_Procs.php");
	require_once("../funciones/Utilidades.php");
	require_once("FuncionesHistoriaClinica.php");
	require_once("antecedentes_funciones.php");
	
	$dbVariables = new Dbvariables();
	$dbUsuarios = new DbUsuarios();
	$dbAdmision = new DbAdmision();
	$dbTiposCitas = new DbTiposCitas();
	$dbConsultaDermatologia = new DbConsultaDermatologia();
	$dbListas = new DbListas();
	$dbPacientes = new DbPacientes();
	$db_diagnosticos = new DbDiagnosticos();
	$dbTiposCitasDetalle = new DbTiposCitasDetalle();
	$dbPlanes = new DbPlanes();
	
	$contenido = new ContenidoHtml();
	$utilidades = new Utilidades();
	$class_diagnosticos = new Class_Diagnosticos();
	$class_formulacion = new Class_Formulacion();
	$class_espera_dilatacion = new Class_Espera_Dilatacion();
	$class_solic_procs = new Class_Solic_Procs();
	
	$combo = new Combo_Box();
	$funciones_hc = new FuncionesHistoriaClinica();
	
	//variables
	$titulo = $dbVariables->getVariable(1);
	$horas_edicion = $dbVariables->getVariable(7);
	
	//Cambiar las variables get a post
	$utilidades->get_a_post();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $titulo["valor_variable"]; ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
        <link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
        <link href="../css/azul.css" rel="stylesheet" type="text/css" />
        <link href="../css/Class_Color_Pick.css" rel="stylesheet" type="text/css" />
        <!--Para autocompletar DEBE IR DE PRIMERO-->
        <script type="text/javascript" src="../js/jquery_autocompletar.js"></script>
        <script type="text/javascript" src="../js/jquery-ui.js"></script>
        <!--Para validar DEBE IR DE SEGUNDO-->
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.add.js"></script>
        <!--Para funciones de optometria DEBE IR DE TERCERO-->
        <script type="text/javascript" src="../js/ajax.js"></script>
        <script type="text/javascript" src="../js/funciones.js"></script>
        <script type="text/javascript" src="../js/validaFecha.js"></script>
        <script type="text/javascript" src="../js/Class_Diagnosticos_v1.3.js"></script>
        <script type="text/javascript" src="../js/Class_Formulas_Medicas.js"></script>
        <script type="text/javascript" src="../js/Class_Atencion_Remision_v1.3.js"></script>
        <script type="text/javascript" src="../js/Class_Formulacion_v1.5.js"></script>
        <script type="text/javascript" src="../funciones/ckeditor/ckeditor.js"></script>
        <script type="text/javascript" src="../funciones/ckeditor/config.js"></script>
        <script type="text/javascript" src="../js/Class_Color_Pick.js"></script>
        <script type="text/javascript" src="../js/Class_Espera_Dilatacion.js"></script>
        <script type="text/javascript" src="../js/Class_Solic_Procs.js"></script>
        <script type="text/javascript" src="historia_clinica_v1.1.js"></script>
        <script type="text/javascript" src="FuncionesHistoriaClinica.js"></script>
        <script type="text/javascript" src="consulta_dermatologia.js"></script>
        <script type="text/javascript" src="antecedentes_v1.0.js"></script>
        <?php
			$lista_fg = array();
			$lista_fg[0]["id"] = "1";
			$lista_fg[0]["valor"] = "1";
			$lista_fg[1]["id"] = "2";
			$lista_fg[1]["valor"] = "2";
			$lista_fg[2]["id"] = "3";
			$lista_fg[2]["valor"] = "3";
			$lista_fg[3]["id"] = "4";
			$lista_fg[3]["valor"] = "4";
			
			$tabla_diagnosticos = $db_diagnosticos->getDiagnosticoCiexTotal();
			$i = 0;
			$cadena_diagnosticos = "";
			foreach($tabla_diagnosticos as $fila_diagnosticos){
				$cod_ciex = $fila_diagnosticos["codciex"];
				$nom_ciex = $fila_diagnosticos["nombre"];
				
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
    </head>
    <body onload="ajustar_textareas(); ocultar_panels_dermatologia();">
    	<?php
			$contenido->validar_seguridad(0);
			if (!isset($_POST["tipo_entrada"])) {
				$contenido->cabecera_html();	
			}
			$valores_av = $dbListas->getListaDetalles(11);
			$equipos_nomograma = $dbListas->getListaDetalles(16);
			$valores_sino = $dbListas->getListaDetalles(10);
			$tabla_ojos = $dbListas->getListaDetalles(14);
			$id_tipo_reg = 56; // Tipo de registros Tabla:tipos_registros_hc= CONSULTA CONTROL LÁSER (OFTALMOLOGIA) para control de laser
			$id_usuario = $_SESSION["idUsuario"];
			
			//Variables de dermatología
			if (isset($_POST["hdd_id_paciente"])) {
				$id_paciente = $_POST["hdd_id_paciente"];
				$nombre_paciente = $_POST["hdd_nombre_paciente"];
				$id_admision = $_POST["hdd_id_admision"];
				
				//Se obtienen los datos de la admision
				$admision_obj = $dbAdmision->get_admision($id_admision);
				
				//Se obtienen los datos del tipo de cita
				$tipo_cita_obj = $dbTiposCitas->get_tipo_cita($admision_obj["id_tipo_cita"]);
				
				if (!isset($_POST["tipo_entrada"])) {
					$tabla_hc = $dbConsultaDermatologia->getHistoriaClinicaAdmision($id_admision, $id_tipo_reg);
				} else {
					$id_hc = $_POST["hdd_id_hc"];
					$tabla_hc = $dbConsultaDermatologia->getHistoriaClinicaId($id_hc);
				}
				
				if (count($tabla_hc) > 0) { //Si se encuentra una HC para este paciente se carga los datos de lo contrario se crea la HC
					$tipo_accion = "2"; //Editar consulta dermatológica
					$id_hc_consulta = $tabla_hc["id_hc"];
					
					//se obtiene el registro de la consulta dermatológica a partir del ID de la Historia Clinica
					$consulta_dermatologia_obj = $dbConsultaDermatologia->getConsultaDermatologia($id_hc_consulta);
					$peso = $consulta_dermatologia_obj["peso"];
					$talla = $consulta_dermatologia_obj["talla"];
					if ($talla != "") {
						$talla = intval($talla, 10);
					}
					$imc = $consulta_dermatologia_obj["imc"];
					$id_ludwig = $consulta_dermatologia_obj["id_ludwig"];
					$fg_labio_superior = $consulta_dermatologia_obj["fg_labio_superior"];
					$fg_mejilla = $consulta_dermatologia_obj["fg_mejilla"];
					$fg_torax = $consulta_dermatologia_obj["fg_torax"];
					$fg_espalda_superior = $consulta_dermatologia_obj["fg_espalda_superior"];
					$fg_espalda_inferior = $consulta_dermatologia_obj["fg_espalda_inferior"];
					$fg_abdomen_superior = $consulta_dermatologia_obj["fg_abdomen_superior"];
					$fg_abdomen_inferior = $consulta_dermatologia_obj["fg_abdomen_inferior"];
					$fg_brazo = $consulta_dermatologia_obj["fg_brazo"];
					$fg_muslo = $consulta_dermatologia_obj["fg_muslo"];
					$fg_total = $consulta_dermatologia_obj["fg_total"];
					$descripcion_cara = $consulta_dermatologia_obj["descripcion_cara"];
					$descripcion_cuerpo = $consulta_dermatologia_obj["descripcion_cuerpo"];
					$desc_antecedentes_medicos = $consulta_dermatologia_obj["desc_antecedentes_medicos"];
					$diagnostico_dermat = $consulta_dermatologia_obj["diagnostico_dermat"];
					$solicitud_examenes = $consulta_dermatologia_obj["solicitud_examenes"];
					$tratamiento_dermat = $consulta_dermatologia_obj["tratamiento_dermat"];

					
					//Se verifica si se debe actualizar el estado de la admisión asociada
					$en_atencion = "0";
					if (isset($_POST["hdd_en_atencion"])) {
						$en_atencion = $_POST["hdd_en_atencion"];
					}
					
					if ($en_atencion == "1") {
						$dbAdmision->editar_admision_estado($id_admision, 6, 1, $id_usuario);
					}
				} else { //Entre en procesos de crear HC
					$tipo_accion = "1";//Crear consulta optometria para control de laser
					//Se crea la historia clinica y se inicia la consulta de optometria
					$id_hc_consulta = $dbConsultaDermatologia->CrearConsultaDermatologia($id_paciente, $id_tipo_reg, $id_usuario, $id_admision);
					if ($id_hc_consulta < 0) { //Ninguna accion Error
						$tipo_accion = "0";
					} else {
						$consulta_dermatologia_obj = $dbConsultaDermatologia->getConsultaDermatologia($id_hc_consulta);
					}
					//Variables de inicio de consulta dermatológica
					$peso = "";
					$talla = "";
					$imc = "";
					$id_ludwig = "";
					$fg_labio_superior = "";
					$fg_mejilla = "";
					$fg_torax = "";
					$fg_espalda_superior = "";
					$fg_espalda_inferior = "";
					$fg_abdomen_superior = "";
					$fg_abdomen_inferior = "";
					$fg_brazo = "";
					$fg_muslo = "";
					$fg_total = "";
					$descripcion_cara = "";
					$descripcion_cuerpo = "";
					$desc_antecedentes_medicos = $consulta_dermatologia_obj["desc_antecedentes_medicos"];
					$diagnostico_dermat = "";
					$solicitud_examenes = "";
					$tratamiento_dermat = "";
				}
				
				//Se obtienen los datos del registro de historia clínica
				$historia_clinica_obj = $dbConsultaDermatologia->getHistoriaClinicaId($id_hc_consulta);
			} else {
				$tipo_accion = "0"; //Ninguna acción Error
			}
			
			//Se inicia el componente de formulación de medicamentos
			$cod_tipo_medicamento = "";
			if (isset($_POST["hdd_id_admision"])) {
				$admision_obj = $dbAdmision->get_admision($_POST["hdd_id_admision"]);
				$plan_obj = $dbPlanes->getPlan($admision_obj["id_plan"]);
				
				$cod_tipo_medicamento = $plan_obj["cod_tipo_medicamento"];
			}
			$class_formulacion->iniciarComponentesFormulacion($cod_tipo_medicamento, $id_hc_consulta);
			
			//Edad del paciente
			$datos_paciente = $dbPacientes->getEdadPaciente($id_paciente, "");
			$edad_paciente = $datos_paciente["edad"];
			$profesion_paciente = $datos_paciente["profesion"];
			
			//Nombre del profesional que atiende la consulta
			$id_usuario_profesional = $consulta_dermatologia_obj["id_usuario_crea"];
			$usuario_profesional_obj = $dbUsuarios->getUsuario($id_usuario_profesional);
			$nombre_usuario_profesional = $usuario_profesional_obj["nombre_usuario"]." ".$usuario_profesional_obj["apellido_usuario"];
			
			if(!isset($_POST["tipo_entrada"])){
	    ?>
        <div class="title-bar title_hc">
            <div class="wrapper">
                <div class="breadcrumb">
                <ul>
                    <li class="breadcrumb_on">Consulta Dermatol&oacute;gica</li>
                </ul>
            </div>
            </div>
        </div>
        <?php
			}
			
			if ($tipo_accion > 0) {
				//Para verificaro que tiene permiso de hacer cambio
				$ind_editar = $dbConsultaDermatologia->getIndicadorEdicion($id_hc_consulta, $horas_edicion["valor_variable"]);
				$ind_editar_enc_hc = $ind_editar;
				if ($ind_editar == 1 && isset($_POST["tipo_entrada"])) {
					$ind_editar_enc_hc = 0;
				}
				
				$funciones_hc->encabezado_historia_clinica($id_paciente, $id_admision, $id_hc_consulta, $ind_editar_enc_hc, false);
        ?>
	    <div class="contenedor_principal" id="id_contenedor_principal">
	        <div id="guardar_dermatologia" style="width:100%; display:block;">
            	<div class="contenedor_error" id="contenedor_error"></div>
            	<div class="contenedor_exito" id="contenedor_exito"></div>
	        </div>
        	<div class="formulario" id="d_principal_dermatologia" style="width:100%; display:block;">
        		<?php
					//Se inserta el registro de ingreso a la historia clínica
					$dbConsultaDermatologia->crear_ingreso_hc($id_usuario, $id_paciente, $id_admision, $id_hc_consulta, 160);
				?>
                <input type="hidden" name="hdd_id_hc_consulta" id="hdd_id_hc_consulta" value="<?php echo($id_hc_consulta); ?>" />
                <input type="hidden" name="hdd_id_admision" id="hdd_id_admision" value="<?php echo($id_admision); ?>" />
	    	    <input type="hidden" name="hdd_id_convenio" id="hdd_id_convenio" value="<?php echo($admision_obj["id_convenio"]); ?>" />
    		    <input type="hidden" name="hdd_id_plan" id="hdd_id_plan" value="<?php echo($admision_obj["id_plan"]); ?>" />
                <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
                    <tr>
			        	<th colspan="3" align="left">
                            <h6 style="margin: 1px;">
                                <input type="hidden" id="hdd_usuario_anonimo" value="<?php echo($usuario_profesional_obj["ind_anonimo"]); ?>" />
                                <b>Profesional que atiende: </b><?php echo($nombre_usuario_profesional); ?>
                            </h6>
                        </th>
                    </tr>
            	</table>
                <br />
                <div class="tabs-container">
                    <dl class="tabs" data-tab>
                        <dd id="panel_derma_1" class="active" onclick="setTimeout(function() { ajustar_textareas(); }, 100);"><a href="#panel2-1">Antecedentes</a></dd>
                        <dd id="panel_derma_2" onclick="setTimeout(function() { ajustar_textareas(); }, 100);"><a href="#panel2-2">Examen F&iacute;sico</a></dd>
                        <dd id="panel_derma_3" onclick="setTimeout(function() { ajustar_textareas(); }, 100);"><a href="#panel2-3">Diagn&oacute;stico</a></dd>
                    </dl>
                    <div class="tabs-content" style="padding:0px;margin: 0px;">
                        <div class="content active" id="panel2-1">
                        	<?php
								$ind_sin_refrac_ant = true;
								
								require("antecedentes.php");
							?>
                        </div>
                        <div class="content active" id="panel2-2">
                            <table border="0" cellpadding="0" cellspacing="0" align="center" style="width:98%;">
                                <tr>
                                    <td align="right" style="width:10%;">Peso (Kg)*:&nbsp;</td>
                                    <td align="left" style="width:13%;">
                                        <input type="text" id="txt_peso" name="txt_peso" style="width:100px;" class="input_hc" value="<?php echo($peso); ?>" maxlength="5" onkeypress="solo_numeros(event, true);" onblur="calcular_imc();" />
                                    </td>
                                    <td align="right" style="width:10%;">Talla (cm)*:&nbsp;</td>
                                    <td align="left" style="width:14%;">
                                        <input type="text" id="txt_talla" name="txt_talla" style="width:100px;" class="input_hc" value="<?php echo($talla); ?>" maxlength="3" onkeypress="solo_numeros(event, false);" onblur="calcular_imc();" />
                                    </td>
                                    <td align="right" style="width:10%;">IMC:&nbsp;</td>
                                    <td align="left" style="width:13%;">
                                        <input type="text" id="txt_imc" name="txt_imc" style="width:100px;" class="input_hc" value="<?php echo($imc); ?>" readonly="readonly" />
                                    </td>
                                </tr>
                                <tr style="height:5px;"></tr>
                                <tr>
                                    <td align="right">Presi&oacute;n arterial:&nbsp;</td>
                                    <td align="left">
                                        <input type="text" id="txt_pa" name="txt_pa" style="width:100px;" class="input_hc" value="<?php echo($admision_obj["presion_arterial"]); ?>" readonly="readonly" />
                                    </td>
                                    <td align="right">Pulso:&nbsp;</td>
                                    <td align="left">
                                        <input type="text" id="txt_pulso" name="txt_pulso" style="width:100px;" class="input_hc" value="<?php echo($admision_obj["pulso"]); ?>" readonly="readonly" />
                                    </td>
                                    <td align="right">Escala Ludwig:&nbsp;</td>
                                    <td align="left">
                                        <?php
											$lista_ludwig = $dbListas->getListaDetalles(72, 1);
                                        	$combo->getComboDb("cmb_ludwig", $id_ludwig, $lista_ludwig, "id_detalle,nombre_detalle", "--Seleccione--", "", 1, "", "", "no-margin");
										?>
                                    </td>
                                </tr>
                                <tr style="height:20px;"></tr>
                                <tr>
                                	<td align="center" colspan="6">
                                        <table class="modal_table" style="width: 70%; margin: auto;">
                                            <thead>
                                            	<tr>
                                                	<th align="center" colspan="4">Escala Ferriman y Gallway</th>
                                                </tr>
                                                <tr>
                                                    <th style="width:35%;">Regi&oacute;n</th>
                                                    <th style="width:15%;">Grado</th>
                                                    <th style="width:35%;">Regi&oacute;n</th>
                                                    <th style="width:15%;">Grado</th>
                                                </tr>
                                            </thead>
                                            <tr>
                                            	<td align="left">Labio superior</td>
                                                <td align="center">
                                                	<?php
														$combo->getComboDb("cmb_fg_labio_superior", $fg_labio_superior, $lista_fg, "id,valor", "-", "calcular_fg();", 1, "", "", "no-margin");
													?>
                                                </td>
                                            	<td align="left">Mejilla</td>
                                                <td align="center">
                                                	<?php
														$combo->getComboDb("cmb_fg_mejilla", $fg_mejilla, $lista_fg, "id,valor", "-", "calcular_fg();", 1, "", "", "no-margin");
													?>
                                                </td>
                                            </tr>
                                            <tr>
                                            	<td align="left">T&oacute;rax</td>
                                                <td align="center">
                                                	<?php
														$combo->getComboDb("cmb_fg_torax", $fg_torax, $lista_fg, "id,valor", "-", "calcular_fg();", 1, "", "", "no-margin");
													?>
                                                </td>
                                            	<td align="left">Espalda superior</td>
                                                <td align="center">
                                                	<?php
														$combo->getComboDb("cmb_fg_espalda_superior", $fg_espalda_superior, $lista_fg, "id,valor", "-", "calcular_fg();", 1, "", "", "no-margin");
													?>
                                                </td>
                                            </tr>
                                            <tr>
                                            	<td align="left">Espalda inferior</td>
                                                <td align="center">
                                                	<?php
														$combo->getComboDb("cmb_fg_espalda_inferior", $fg_espalda_inferior, $lista_fg, "id,valor", "-", "calcular_fg();", 1, "", "", "no-margin");
													?>
                                                </td>
                                            	<td align="left">Abdomen superior</td>
                                                <td align="center">
                                                	<?php
														$combo->getComboDb("cmb_fg_abdomen_superior", $fg_abdomen_superior, $lista_fg, "id,valor", "-", "calcular_fg();", 1, "", "", "no-margin");
													?>
                                                </td>
                                            </tr>
                                            <tr>
                                            	<td align="left">Abdomen inferior</td>
                                                <td align="center">
                                                	<?php
														$combo->getComboDb("cmb_fg_abdomen_inferior", $fg_abdomen_inferior, $lista_fg, "id,valor", "-", "calcular_fg();", 1, "", "", "no-margin");
													?>
                                                </td>
                                            	<td align="left">Brazo</td>
                                                <td align="center">
                                                	<?php
														$combo->getComboDb("cmb_fg_brazo", $fg_brazo, $lista_fg, "id,valor", "-", "calcular_fg();", 1, "", "", "no-margin");
													?>
                                                </td>
                                            </tr>
                                            <tr>
                                            	<td align="left">Muslo</td>
                                                <td align="center">
                                                	<?php
														$combo->getComboDb("cmb_fg_muslo", $fg_muslo, $lista_fg, "id,valor", "-", "calcular_fg();", 1, "", "", "no-margin");
													?>
                                                </td>
                                            	<td align="left"><span class="verde">Total</span></td>
                                                <td align="center"><span id="sp_fg_total" class="verde"><?php echo($fg_total); ?></span></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr style="height:20px;"></tr>
                                <tr>
                                	<td align="center" colspan="6">
		   								<h5 style="margin: 10px">Cara</h5>
                    					<?php
											$descripcion_cara = $utilidades->ajustar_texto_wysiwyg($descripcion_cara);
										?>
                    					<div id="txt_descripcion_cara"><?php echo($descripcion_cara); ?></div>
                                    </td>
                                </tr>
                                <tr>
                                	<td align="center" colspan="6">
		   								<h5 style="margin: 10px">Cuerpo</h5>
                    					<?php
											$descripcion_cuerpo = $utilidades->ajustar_texto_wysiwyg($descripcion_cuerpo);
										?>
                    					<div id="txt_descripcion_cuerpo"><?php echo($descripcion_cuerpo); ?></div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="content active" id="panel2-3">
		  					<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:98%;">
		  						<tr>
                                    <td align="left" colspan="3">
                                        <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:100%;">	
                                            <tr>
                                                <td align="center" colspan="2">
                                                    <h6>Diagn&oacute;sticos</h6>
                                					<?php
														$class_diagnosticos->getFormularioDiagnosticos($id_hc_consulta, false);
													?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" colspan="2">
                                                    <label><b>Otros diagn&oacute;sticos y an&aacute;lisis</b></label>
                                                    <div id="txt_diagnostico_dermat"><?php echo($utilidades->ajustar_texto_wysiwyg($diagnostico_dermat)); ?></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" colspan="2">
                                                    <label><b>Solicitud de Procedimientos y Ex&aacute;menes Complemetarios</b></label>
                                                    <?php
														$class_solic_procs->getFormularioSolicitud($id_hc_consulta, false);
													?>
                                                    <div id="txt_solicitud_examenes"><?php echo($utilidades->ajustar_texto_wysiwyg($solicitud_examenes)); ?></div>
                                                </td>
                                            </tr>
                                            <tr>	
                                                <td align="center" colspan="2">
                                                    <label><b>Recomendaciones Cl&iacute;nicas, M&eacute;dicas y Quir&uacute;rgicas&nbsp;</b></label>
                                                    <div id="txt_tratamiento_dermat"><?php echo($utilidades->ajustar_texto_wysiwyg($tratamiento_dermat)); ?></div>
                                                </td>
                                            </tr>
                                            <tr><td><div class="div_separador"></div></td></tr>
                                            <tr>
                                                <td align="center" colspan="2">
                                                    <label style="display:inline;"><b>Formulaci&oacute;n de Medicamentos</b></label>
                                                    <?php
														$class_formulacion->getFormularioFormulacion($id_hc_consulta);
													?>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:90%;">
                        <tr valign="top">
                            <td colspan="3">
                                <?php
									if (!isset($_POST["tipo_entrada"])) {
								?>
                                <input class="btnPrincipal" type="button" id="btn_imprimir" nombre="btn_imprimir" value="Imprimir" onclick="crear_dermatologia(2, 1);" />
                                <?php
									} else {
								?>
                                <input class="btnPrincipal" type="button" id="btn_imprimir" nombre="btn_imprimir" value="Imprimir" onclick="imprimir_dermatologia();" />
                                <?php
									}
									
									if ($ind_editar == 1) {
										if (!isset($_POST["tipo_entrada"])) {
								?>
                                <input type="button" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Guardar cambios" onclick="crear_dermatologia(2, 0);" />
                                <?php
											$id_tipo_cita = $admision_obj["id_tipo_cita"];
											$lista_tipos_citas_det_remisiones = $dbTiposCitasDetalle->get_lista_tipos_citas_det_remisiones($id_tipo_cita, $id_tipo_reg);
											
											if (count($lista_tipos_citas_det_remisiones) > 0) {
								?>
                                <input class="btnPrincipal" type="button" id="btn_enviar_a" nombre="btn_enviar_a" value="Enviar a ..." onclick="enviar_a_estados();" />
                                <?php
											}
								?>
                                <input class="btnPrincipal" type="button" id="btn_crear" nombre="btn_crear" value="Finalizar consulta" onclick="crear_dermatologia(1, 0);" />
                                <?php
										} else {
								?>
                                <input type="button" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Guardar" onclick="crear_dermatologia(3, 0);" />
                                <?php
										}
									}
								?>
                            </td>
                        </tr>
                    </table>
                </div>
		    </div>
	    </div>
    	<?php
			} else {
		?>
		<div class="contenedor_principal">
			<div class="contenedor_error" style="display:block;">Error al ingresar a la consulta de control l&aacute;ser</div>
		</div>
		<?php
			}
		?>
	   <script type="text/javascript" src="../js/foundation.min.js"></script>
       <script>
            $(document).foundation();
			
			initCKEditorControl("txt_desc_antecedentes_medicos");
			initCKEditorControl("txt_descripcion_cara");
			initCKEditorControl("txt_descripcion_cuerpo");
			initCKEditorControl("txt_diagnostico_dermat");
			initCKEditorControl("txt_solicitud_examenes");
			initCKEditorControl("txt_tratamiento_dermat");
       </script>
    	<?php
		   if(!isset($_POST["tipo_entrada"])){
				$contenido->ver_historia($id_paciente);
				$contenido->footer();
		   }
		   else{
				$contenido->footer_iframe();
		   }
		?>
	</body>
</html>
