<?php
	session_start();
	
	require_once("../db/DbVariables.php");
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbConsultaControlLaserOf.php");
	require_once("../db/DbAdmision.php");
	require_once("../db/DbTiposCitas.php");
	require_once("../db/DbMenus.php");
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
	
	$variables = new Dbvariables();
	$usuarios = new DbUsuarios();
	$dbAdmision = new DbAdmision();
	$dbTiposCitas = new DbTiposCitas();
	$dbConsultaControlLaserOf = new DbConsultaControlLaserOf();
	$menus = new DbMenus();
	$listas = new DbListas();
	$pacientes = new DbPacientes();
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
	$titulo = $variables->getVariable(1);
	$horas_edicion = $variables->getVariable(7);
	
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
        <script type="text/javascript" src="../js/Class_Diagnosticos_v1.2.js"></script>
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
        <script type="text/javascript" src="consulta_control_laser_of_v1.11.js"></script>
        <?php
			$lista_si_no = array();
			$lista_si_no[0]["id"] = "1";
			$lista_si_no[0]["valor"] = "S&iacute;";
			$lista_si_no[1]["id"] = "0";
			$lista_si_no[1]["valor"] = "No";
			
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
    <body>
    	<?php
			$contenido->validar_seguridad(0);
			if (!isset($_POST["tipo_entrada"])) {
				$contenido->cabecera_html();	
			}
			$valores_av = $listas->getListaDetalles(11);
			$equipos_nomograma = $listas->getListaDetalles(16);
			$valores_sino = $listas->getListaDetalles(10);
			$tabla_ojos = $listas->getListaDetalles(14);
			$id_tipo_reg = 9; // Tipo de registros Tabla:tipos_registros_hc= CONSULTA CONTROL LÁSER (OFTALMOLOGIA) para control de laser
			$id_usuario_crea = $_SESSION["idUsuario"];
			
			//Variables de Optometria
			if (isset($_POST["hdd_id_paciente"])) {
				$id_paciente = $_POST["hdd_id_paciente"];
				$nombre_paciente = $_POST["hdd_nombre_paciente"];
				$id_admision = $_POST["hdd_id_admision"];
				
				$ind_preconsulta = "0";
				if (isset($_GET["ind_preconsulta"])) {
					$ind_preconsulta = $_GET["ind_preconsulta"];
				}
				
				//Se obtienen los datos de la admision
				$admision_obj = $dbAdmision->get_admision($id_admision);
				
				//Se obtienen los datos del tipo de cita
				$tipo_cita_obj = $dbTiposCitas->get_tipo_cita($admision_obj["id_tipo_cita"]);
				
				if (!isset($_POST["tipo_entrada"])) {
					$tabla_hc = $dbConsultaControlLaserOf->getHistoriaClinicaAdmision($id_admision, $id_tipo_reg);
				} else {
					$id_hc = $_POST["hdd_id_hc"];
					$tabla_hc = $dbConsultaControlLaserOf->getHistoriaClinicaId($id_hc);
				}
				
				if (count($tabla_hc) > 0) { //Si se encuentra una HC para este paciente se carga los datos de lo contrario se crea la HC
					$tipo_accion = "2"; //Editar consulta optometria para prequirúrgico de laser
					$id_hc_consulta = $tabla_hc["id_hc"];
					
					//se obtiene el registro de la consulta de optometria a partir del ID de la Historia Clinica para prequirúrgico de laser
					$tabla_control_laser_of = $dbConsultaControlLaserOf->getConsultaControlLaserOf($id_hc_consulta);
					$presion_intraocular_aplanatica_od = $tabla_control_laser_of["presion_intraocular_aplanatica_od"];
					$presion_intraocular_aplanatica_oi = $tabla_control_laser_of["presion_intraocular_aplanatica_oi"];
					$hallazgos_control_laser = $tabla_control_laser_of["hallazgos_control_laser"];
					$diagnostico_control_laser_of = $tabla_control_laser_of["diagnostico_control_laser_of"];
					$solicitud_examenes_control_laser = $tabla_control_laser_of["solicitud_examenes_control_laser"]; 
					$tratamiento_control_laser = $tabla_control_laser_of["tratamiento_control_laser"];
					$medicamentos_control_laser = $tabla_control_laser_of["medicamentos_control_laser"];
					$nombre_usuario_alt = $tabla_hc["nombre_usuario_alt"];
					$ind_formula_gafas = $tabla_control_laser_of["ind_formula_gafas"];
					
					//Se verifica si se debe actualizar el estado de la admisión asociada
					$en_atencion = "0";
					if (isset($_POST["hdd_en_atencion"])) {
						$en_atencion = $_POST["hdd_en_atencion"];
					}
					
					if ($en_atencion == "1") {
						$dbAdmision->editar_admision_estado($id_admision, 6, 1, $id_usuario_crea);
					}
				} else { //Entre en procesos de crear HC
					$tipo_accion = "1";//Crear consulta optometria para control de laser
					//Se crea la historia clinica y se inicia la consulta de optometria
					$id_hc_consulta = $dbConsultaControlLaserOf->CrearConsultaControlLaserOf($id_paciente, $id_tipo_reg, $id_usuario_crea, $id_admision, $ind_preconsulta);
					
					if ($id_hc_consulta < 0) { //Ninguna accion Error
						$tipo_accion = "0";
					} else {
						$tabla_control_laser_of = $dbConsultaControlLaserOf->getConsultaControlLaserOf($id_hc_consulta);
					}
					//Variables de inicio de conuslta de optometria
					$presion_intraocular_aplanatica_od = "";
					$presion_intraocular_aplanatica_oi = "";
					$hallazgos_control_laser = "";
					$diagnostico_control_laser_of = "";
					$solicitud_examenes_control_laser = ""; 
					$tratamiento_control_laser = "";
					$medicamentos_control_laser = "";
					$nombre_usuario_alt = $tabla_hc["nombre_usuario_alt"];
					$ind_formula_gafas = "0";
				}
				
				//Se obtienen los datos del registro de historia clínica
				$historia_clinica_obj = $dbConsultaControlLaserOf->getHistoriaClinicaId($id_hc_consulta);
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
			
			//Obtener los datos de la consulta de optometria
			$nombre_pagina_op = "";
			$alto_frame = 0;
			$datos_optometria = $dbConsultaControlLaserOf->getOptometriaPaciente($id_paciente, $id_admision);
			if (isset($datos_optometria["id_hc"])) {
				$id_hc_optometria = $datos_optometria["id_hc"];
				$nombre_pagina_op = "../historia_clinica/consulta_optometria.php";
				$alto_frame = 1850;
			} else {
				$datos_optometria = $dbConsultaControlLaserOf->getControlLaserPaciente($id_paciente, $id_admision);
				$id_hc_optometria = $datos_optometria["id_hc"];
				$nombre_pagina_op = "../historia_clinica/consulta_control_laser.php";
				$alto_frame = 1300;
			}
			
			//Edad del paciente
			$datos_paciente = $pacientes->getEdadPaciente($id_paciente, "");
			$edad_paciente = $datos_paciente["edad"];
			$profesion_paciente = $datos_paciente["profesion"];
			
			//Nombre del profesional que atiende la consulta
			$id_usuario_profesional = $tabla_control_laser_of["id_usuario_crea"];
			$usuario_profesional_obj = $usuarios->getUsuario($id_usuario_profesional);
			$nombre_usuario_profesional = $usuario_profesional_obj["nombre_usuario"]." ".$usuario_profesional_obj["apellido_usuario"];
			
			if(!isset($_POST["tipo_entrada"])){
	    ?>
        <div class="title-bar title_hc">
            <div class="wrapper">
                <div class="breadcrumb">
                <ul>
                    <li class="breadcrumb_on">Consulta Control L&aacute;ser (Oftalmolog&iacute;a)</li>
                </ul>
            </div>
            </div>
        </div>
        <?php
			}
			
			if ($tipo_accion > 0) {
				//Para verificaro que tiene permiso de hacer cambio
				$ind_editar = $dbConsultaControlLaserOf->getIndicadorEdicion($id_hc_consulta, $horas_edicion["valor_variable"]);
				$ind_editar_enc_hc = $ind_editar;
				if ($ind_editar == 1 && isset($_POST["tipo_entrada"])) {
					$ind_editar_enc_hc = 0;
				}
				
				$funciones_hc->encabezado_historia_clinica($id_paciente, $id_admision, $id_hc_consulta, $ind_editar_enc_hc, false);
        ?>
	    <div class="contenedor_principal" id="id_contenedor_principal">
	        <div id="guardar_control_laser_of" style="width: 100%; display: block;">
            	<div class="contenedor_error" id="contenedor_error"></div>
            	<div class="contenedor_exito" id="contenedor_exito"></div>
	        </div>
        	<div class="formulario" id="principal_optometria" style="width: 100%; display: block;">
        		<?php
					//Se inserta el registro de ingreso a la historia clínica
					$dbConsultaControlLaserOf->crear_ingreso_hc($id_usuario_crea, $id_paciente, $id_admision, $id_hc_consulta, 160);
					
					//Se verifica la información de que ojo se va a solicitar
					$bol_od = false;
					$bol_oi = false;
					switch ($tabla_control_laser_of["ojo"]) {
						case "OD":
							$bol_od = true;
							break;
						case "OI":
							$bol_oi = true;
							break;
						case "AO":
							$bol_od = true;
							$bol_oi = true;
							break;
					}
				?>
				<script type="text/javascript">
					//Se verifica la información de que ojo se va a solicitar
					var bol_od = false;
					var bol_oi = false;
					<?php
						switch ($tabla_control_laser_of["ojo"]) {
							case "OD":
					?>
					bol_od = true;
					<?php
								break;
							case "OI":
					?>
					bol_oi = true;
					<?php
								break;
							case "AO":
					?>
					bol_od = true;
					bol_oi = true;
					<?php
								break;
						}
					?>
				</script>
                <form id="frm_consulta_control_laser_of" name="frm_consulta_control_laser_of" method="post">
                    <input type="hidden" name="hdd_id_hc_consulta" id="hdd_id_hc_consulta" value="<?php echo($id_hc_consulta); ?>" />
                    <input type="hidden" name="hdd_id_admision" id="hdd_id_admision" value="<?php echo($id_admision); ?>" />
	    	        <input type="hidden" name="hdd_id_convenio" id="hdd_id_convenio" value="<?php echo($admision_obj["id_convenio"]); ?>" />
    		        <input type="hidden" name="hdd_id_plan" id="hdd_id_plan" value="<?php echo($admision_obj["id_plan"]); ?>" />
                    <input type="hidden" name="hdd_ind_preconsulta" id="hdd_ind_preconsulta" value="<?php echo($ind_preconsulta); ?>" />
                    <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
                        <tr>
			            	<th colspan="3" align="left">
                                <h6 style="margin: 1px;">
                                    <input type="hidden" id="hdd_usuario_anonimo" value="<?php echo($usuario_profesional_obj["ind_anonimo"]); ?>" />
                                    <b>Profesional que atiende: </b>
                                    <?php
                                        if ($usuario_profesional_obj["ind_anonimo"] == "0") {
                                    ?>
                                    <input type="hidden" id="txt_nombre_usuario_alt" value="" />
                                    <?php
                                            echo($nombre_usuario_profesional);
                                        } else {
                                    ?>
                                    <input type="text" id="txt_nombre_usuario_alt" maxlength="100" value="<?php echo($nombre_usuario_alt); ?>" style="width:60%; display:inline;" onblur="trim_cadena(this);" />
                                    <?php
                                        }
                                    ?>
                                </h6>
                            </th>
                        </tr>
            		</table>
                    <br />
                    <div class="tabs-container">
                        <dl class="tabs" data-tab>
                            <dd id="panel_oft_2" class="active" onclick="setTimeout(function() { ajustar_textareas(); }, 100);"><a href="#panel2-2">Oftalmolog&iacute;a</a></dd>
                            <dd id="panel_oft_1" onclick="ajustar_div_optometria();"><a href="#panel2-1">Optometr&iacute;a</a></dd>
                        </dl>
                        <div class="tabs-content" style="padding:0px;margin: 0px;">
                            <div class="content" id="panel2-1" >
                                <div id="div_consulta_optometria"></div>
								<?php
                                    $id_menu_aux = "13";
                                    if (isset($_POST["hdd_numero_menu"]) && trim($_POST["hdd_numero_menu"]) != "") {
                                        $id_menu_aux = $_POST["hdd_numero_menu"];
                                    }
                                ?>
                                <script type="text/javascript">
									mostrar_consulta_iframe(<?php echo($id_paciente);?>, "<?php echo($nombre_paciente);?>", <?php echo($id_admision);?>, "<?php echo($nombre_pagina_op); ?>", <?php echo($id_hc_optometria);?>, <?php echo($_POST["credencial"]);?>, <?php echo($id_menu_aux);?>, "div_consulta_optometria");
								</script>
                            </div>
                            <div class="content active" id="panel2-2">
                                <table border="0" cellpadding="0" cellspacing="0" align="center" style="width:98%;">
                                    <tr>
                                        <td align="center" colspan="3" class="td_tabla">
                                            <div class="odoi_t">
                                            <div class="od_t"><h5 style="margin: 0px;">OD</h5></div>
                                            <div class="oi_t"><h5 style="margin: 0px;">OI</h5></div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <br />
                                <table border="0" cellpadding="0" cellspacing="0" align="center" style="width:98%;">
                                    <tr>
                                        <td align="center" style="width:35%;">
                                            <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                                                <tr>
                                                    <td align="right" style="width:75%;">   
                                                        <input type="text" style="width: 100px;" class="input input_hc" value="<?php echo $presion_intraocular_aplanatica_od; ?>" name="presion_intraocular_aplanatica_od" id="presion_intraocular_aplanatica_od" maxlength="5" onkeypress="formato_hc(event, this);" />
                                                    </td>
                                                    <td align="left" style="width:25%;"> 
                                                        <label>&nbsp;&nbsp;mmHg</label>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td align="center" style="width:30%;">
                                            <h5 style="margin:0;">Presi&oacute;n Intraocular Aplan&aacute;tica *</h5>
                                        </td>
                                        <td align="center" style="width:35%;">
                                            <table border="0" cellpadding="1" cellspacing="0" style="width:100%;">
                                                <tr>
                                                    <td align="right" style="width:40%;">
                                                        <input type="text" style="width: 100px;" class="input input_hc" value="<?php echo $presion_intraocular_aplanatica_oi; ?>" name="presion_intraocular_aplanatica_oi" id="presion_intraocular_aplanatica_oi" maxlength="5" onkeypress="formato_hc(event, this);"/>
                                                    </td>
                                                    <td align="left" style="width:60%;"> 
                                                        <label>&nbsp;&nbsp;mmHg</label>
                                                    </td>
                                                </tr>
                                            </table>    
                                        </td>
                                    </tr>
                                </table>
                                <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:98%;">
                                    <tr>
                                        <td align="center">
                                            <h5>Hallazgos *</h5>
                                            <?php
												$hallazgos_control_laser = $utilidades->ajustar_texto_wysiwyg($hallazgos_control_laser);
											?>
                                            <div id="hallazgos_control_laser"><?php echo($hallazgos_control_laser); ?></div>
                                        </td>
                                    </tr>
									<?php
										if (!isset($_POST["tipo_entrada"])) {
									?>
                                    <tr style="height:10px;"></tr>
                                    <tr>
                                        <td align="center" colspan="3">
                                            <?php
												//Se carga el componente indicador de espera por dilatación de pupila
												$class_espera_dilatacion->getEsperaDilatacion($id_admision, $admision_obj["id_tipo_espera"] != "" ? 1 : 0);
											?>
                                        </td>
                                    </tr>
                                    <?php
										}
									?>
                                </table>
                                <table border="0" cellpadding="0" cellspacing="0" align="center" style="width:98%;">
                                    <tr>
                                        <td align="left" colspan="3">
                                            <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:100%;">	
                                                <tr>
                                                    <td align="center" colspan="2">
                                                        <h5>Diagn&oacute;sticos</h5>
                                                        <?php
															$class_diagnosticos->getFormularioDiagnosticos($id_hc_consulta);
														?>
                                                    </td>
                                                </tr>
                    							<?php
													//Se verifica si hay un registro de optometría asociado
													$bol_optometria = $funciones_hc->tieneConsultaOptometria($id_admision);
													
													if ($bol_optometria) {
												?>
                                                <tr>
                                                    <td align="right" style="width:25%;">
                                                        <h6 class="no-margin"><b>Entregar f&oacute;rmula de gafas:</b></h6>
                                                    </td>
                                                    <td align="left" style="width:75%;">
                            							<?php
															$combo->getComboDb("cmb_formula_gafas", $ind_formula_gafas, $lista_si_no, "id,valor", "--Seleccione--", "", 1, "", "", "no-margin");
														?>
                                                    </td>
                                                </tr>
                                                <?php
													} else {
												?>
                                                <input type="hidden" id="cmb_formula_gafas" name="cmb_formula_gafas" value="" />
                                                <?php
													}
												?>
                                                <input type="hidden" id="hdd_ind_optometria" name="hdd_ind_optometria" value="<?php echo($bol_optometria ? 1 : 0) ?>" />
                                                <tr>
                                                    <td align="center" colspan="2">
                                                        <label><b>Otros diagn&oacute;sticos y an&aacute;lisis</b></label>
                                                        <?php
															$diagnostico_control_laser_of = $utilidades->ajustar_texto_wysiwyg($diagnostico_control_laser_of);
														?>
                                                        <div id="diagnostico_control_laser_of"><?php echo($diagnostico_control_laser_of); ?></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align="center" colspan="2">
                                                        <label><b>Solicitud de Procedimientos y Ex&aacute;menes Complemetarios</b></label>
                                						<?php
				                                			$class_solic_procs->getFormularioSolicitud($id_hc_consulta);
															
															$solicitud_examenes_control_laser = $utilidades->ajustar_texto_wysiwyg($solicitud_examenes_control_laser);
														?>
                                                        <div id="solicitud_examenes_control_laser"><?php echo($solicitud_examenes_control_laser); ?></div>
                                                    </td>
                                                </tr>
                                                <tr>	
                                                    <td align="center" colspan="2">
                                                        <label><b>Recomendaciones Cl&iacute;nicas, M&eacute;dicas, Optom&eacute;tricas y Quir&uacute;rgicas&nbsp;</b></label>
                                                        <?php
															$tratamiento_control_laser = $utilidades->ajustar_texto_wysiwyg($tratamiento_control_laser);
														?>
                                                        <div id="tratamiento_control_laser"><?php echo($tratamiento_control_laser); ?></div>
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
                                                <tr style="display:none;">	
                                                    <td align="center" colspan="2">
                                                        <label><b>F&oacute;rmula M&eacute;dica</b></label>
                                                        <textarea style="text-align: justify;" class="textarea_oftalmo" id="medicamentos_control_laser" nombre="medicamentos_control_laser" onblur="trim_cadena(this);"><?php echo $medicamentos_control_laser;?></textarea>
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
                                    <input class="btnPrincipal" type="button" id="btn_imprimir" nombre="btn_imprimir" value="Imprimir" onclick="crear_control_laser_of(2, 1);" />
                                    <?php
										} else {
									?>
                                    <input class="btnPrincipal" type="button" id="btn_imprimir" nombre="btn_imprimir" value="Imprimir" onclick="imprimir_control_laser_of();" />
                                    <?php
										}
										
										if ($ind_editar == 1) {
											if (!isset($_POST["tipo_entrada"])) {
									?>
                                    <input type="button" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Guardar cambios" onclick="crear_control_laser_of(2, 0);" />
                                    <?php
												$id_tipo_cita = $admision_obj["id_tipo_cita"];
												$lista_tipos_citas_det_remisiones = $dbTiposCitasDetalle->get_lista_tipos_citas_det_remisiones($id_tipo_cita, $id_tipo_reg);
												
												if (count($lista_tipos_citas_det_remisiones) > 0) {
									?>
                                    <input class="btnPrincipal" type="button" id="btn_enviar_a" nombre="btn_enviar_a" value="Enviar a ..." onclick="enviar_a_estados();" />
                                    <?php
												}
												
												if ($ind_preconsulta == "1") {
									?>
                                    <input class="btnPrincipal" type="button" id="btn_crear" nombre="btn_crear" value="Finalizar preconsulta" onclick="crear_control_laser_of(4, 0);" />
                                    <?php
												} else {
									?>
                                    <input class="btnPrincipal" type="button" id="btn_crear" nombre="btn_crear" value="Finalizar consulta" onclick="crear_control_laser_of(1, 0);" />
                                    <?php
												}
											} else {
									?>
                                    <input type="button" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Guardar" onclick="crear_control_laser_of(3, 0);" />
                                    <?php
											}
										}
									?>
                                </td>
                            </tr>
                        </table>
                    </div>
				</form>
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
			
			initCKEditorControl("hallazgos_control_laser");
			initCKEditorControl("diagnostico_control_laser_of");
			initCKEditorControl("solicitud_examenes_control_laser");
			initCKEditorControl("tratamiento_control_laser");
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
