<?php session_start();
	require_once("../db/DbVariables.php");
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbConsultaPreqxLaserOf.php");
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
	
	$dbVariables = new Dbvariables();
	$dbUsuarios = new DbUsuarios();
	$dbListas = new DbListas();
	$dbConsultaPreqxLaserOf = new DbConsultaPreqxLaserOf();
	$dbAdmision = new DbAdmision();
	$dbTiposCitas = new DbTiposCitas();
	$dbPacientes = new DbPacientes();
	$dbDiagnosticos = new DbDiagnosticos();
	$dbTiposCitasDetalle = new DbTiposCitasDetalle();
	$dbPlanes = new DbPlanes();
	
	$contenido = new ContenidoHtml();
	$combo = new Combo_Box();
	$class_diagnosticos=new Class_Diagnosticos();
	$class_formulacion = new Class_Formulacion();
	$class_espera_dilatacion = new Class_Espera_Dilatacion();
	$class_solic_procs = new Class_Solic_Procs();
	
	$utilidades = new Utilidades();
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
        <script type="text/javascript" src="../js/Class_Diagnosticos_v1.2.js"></script>
        <script type="text/javascript" src="../js/Class_Formulas_Medicas.js"></script>
        <script type="text/javascript" src="../js/Class_Atencion_Remision_v1.3.js"></script>
        <script type="text/javascript" src="../js/Class_Formulacion_v1.5.js"></script>
        <script type="text/javascript" src="../funciones/ckeditor/ckeditor.js"></script>
        <script type="text/javascript" src="../funciones/ckeditor/config.js"></script>
        <script type="text/javascript" src="../js/Class_Color_Pick.js"></script>
        <script type="text/javascript" src="../js/Class_Espera_Dilatacion.js"></script>    
        <script type="text/javascript" src="../js/Class_Solic_Procs.js"></script>
        <script type="text/javascript" src="../js/jquery.textarea_autosize.js"></script>
        <script type="text/javascript" src="historia_clinica_v1.1.js"></script>
        <script type="text/javascript" src="FuncionesHistoriaClinica.js"></script>
        <script type="text/javascript" src="consulta_preqx_laser_of_v1.10.js"></script>
        <?php
			$tabla_diagnosticos = $dbDiagnosticos->getDiagnosticoCiexTotal();
			$i = 0;
			$cadena_diagnosticos = "";
			foreach ($tabla_diagnosticos as $fila_diagnosticos) {
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
    <body onload="ajustar_textareas();">
    	<?php
			$contenido->validar_seguridad(0);
			if (!isset($_POST["tipo_entrada"])) {
				$contenido->cabecera_html();	
			}
			$valores_sino = $dbListas->getListaDetalles(10);
			$tabla_ojos = $dbListas->getListaDetalles(14);
			$id_tipo_reg = 7; // Tipo de registros Tabla:tipos_registros_hc= CONSULTA PREQUIRÚRGICA LÁSER (OFTALMOLOGÍA) para prequirúrgico de laser
			$id_usuario_crea = $_SESSION["idUsuario"];
			
			//Variables de CONSULTA PREQUIRÚRGICA LÁSER (OFTALMOLOGÍA)
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
					$tabla_hc = $dbConsultaPreqxLaserOf->getHistoriaClinicaAdmision($id_admision, $id_tipo_reg);
				} else {
					$id_hc = $_POST["hdd_id_hc"];
					$tabla_hc = $dbConsultaPreqxLaserOf->getHistoriaClinicaId($id_hc);
				}
				
				if (count($tabla_hc) > 0) { //Si se encuentra una HC para este paciente se carga los datos de lo contrario se crea la HC
					$tipo_accion = "2"; //Editar consulta optometria para prequirúrgico de laser
					$id_hc_consulta = $tabla_hc["id_hc"];
					
					//se obtiene el registro de la consulta de optometria a partir del ID de la Historia Clinica para prequirúrgico de laser
					$tabla_preqx_laser = $dbConsultaPreqxLaserOf->getConsultaPreqxLaserOf($id_hc_consulta);	
					$id_ojo = $tabla_preqx_laser["id_ojo"];
					$preqx_laser_subjetivo = $tabla_preqx_laser["preqx_laser_subjetivo"];
					$preqx_laser_biomiocroscopia = $tabla_preqx_laser["preqx_laser_biomiocroscopia"];
					$presion_intraocular_od = $tabla_preqx_laser["presion_intraocular_od"];
					$presion_intraocular_oi = $tabla_preqx_laser["presion_intraocular_oi"];
					$fondo_ojo_nervio_optico_od = $tabla_preqx_laser["fondo_ojo_nervio_optico_od"];
					$fondo_ojo_macula_od = $tabla_preqx_laser["fondo_ojo_macula_od"];
					$fondo_ojo_periferia_od = $tabla_preqx_laser["fondo_ojo_periferia_od"];
					$fondo_ojo_vitreo_od = $tabla_preqx_laser["fondo_ojo_vitreo_od"];
					$fondo_ojo_nervio_optico_oi = $tabla_preqx_laser["fondo_ojo_nervio_optico_oi"];
					$fondo_ojo_macula_oi = $tabla_preqx_laser["fondo_ojo_macula_oi"];
					$fondo_ojo_periferia_oi = $tabla_preqx_laser["fondo_ojo_periferia_oi"];
					$fondo_ojo_vitreo_oi = $tabla_preqx_laser["fondo_ojo_vitreo_oi"];
					$preqx_laser_plan = $tabla_preqx_laser["preqx_laser_plan"];
					$diagnostico_preqx_laser_of = $tabla_preqx_laser["diagnostico_preqx_laser_of"];
					$solicitud_examenes_preqx_laser = $tabla_preqx_laser["solicitud_examenes_preqx_laser"]; 
					$tratamiento_preqx_laser = $tabla_preqx_laser["tratamiento_preqx_laser"];
					$medicamentos_preqx_laser = $tabla_preqx_laser["medicamentos_preqx_laser"];
					$nombre_usuario_alt = $tabla_hc["nombre_usuario_alt"];
					
					//Se verifica si se debe actualizar el estado de la admisión asociada
					$en_atencion = "0";
					if (isset($_POST["hdd_en_atencion"])) {
						$en_atencion = $_POST["hdd_en_atencion"];
					}
					
					if ($en_atencion == "1") {
						$dbAdmision->editar_admision_estado($id_admision, 6, 1, $id_usuario_crea);
					}
				} else { //Entre en procesos de crear HC
					$tipo_accion = "1"; //Crear consulta oftalmologia para prequirúrgico de laser
					//Se crea la historia clinica y se inicia la consulta de optometria
					$id_hc_consulta = $dbConsultaPreqxLaserOf->CrearConsultaPreqxLaserOf($id_paciente, $id_tipo_reg, $id_usuario_crea, $id_admision, $ind_preconsulta);
					
					if ($id_hc_consulta < 0) { //Ninguna accion Error
						$tipo_accion = "0";
					} else {
						$tabla_preqx_laser = $dbConsultaPreqxLaserOf->getConsultaPreqxLaserOf($id_hc_consulta);
					}
					
					//Variables de inicio de consulta de prequirúrgico de laser oftalmologia  
					$id_ojo = "81";
					$preqx_laser_subjetivo = "";
					$preqx_laser_biomiocroscopia = "";
					$presion_intraocular_od = "";
					$presion_intraocular_oi = "";
					$fondo_ojo_nervio_optico_od = "";
					$fondo_ojo_macula_od = "";
					$fondo_ojo_periferia_od = "";
					$fondo_ojo_vitreo_od = "";
					$fondo_ojo_nervio_optico_oi = "";
					$fondo_ojo_macula_oi = "";
					$fondo_ojo_periferia_oi = "";
					$fondo_ojo_vitreo_oi = "";
					$preqx_laser_plan = "";
					$diagnostico_preqx_laser_of = "";
					$solicitud_examenes_preqx_laser = ""; 
					$tratamiento_preqx_laser = "";
					$medicamentos_preqx_laser = "";
					$nombre_usuario_alt = $tabla_hc["nombre_usuario_alt"];
				}
				
				//Se obtienen los datos del registro de historia clínica
				$historia_clinica_obj = $dbConsultaPreqxLaserOf->getHistoriaClinicaId($id_hc_consulta);
			} else {
				$tipo_accion = "0"; //Ninguna accion Error
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
			$datos_preqx_laser_opt = $dbConsultaPreqxLaserOf->getPreqxLaserOptometriaPaciente($id_paciente, $id_admision);
			$id_hc_optometria = $datos_preqx_laser_opt["id_hc"];
			
			//Edad del paciente
			$datos_paciente = $dbPacientes->getEdadPaciente($id_paciente, "");
			$edad_paciente = $datos_paciente["edad"];
			$profesion_paciente = $datos_paciente["profesion"];
			
			//Nombre del profesional que atiende la consulta
			$id_usuario_profesional = $tabla_preqx_laser["id_usuario_crea"];
			$usuario_profesional_obj = $dbUsuarios->getUsuario($id_usuario_profesional);
			$nombre_usuario_profesional = $usuario_profesional_obj["nombre_usuario"]." ".$usuario_profesional_obj["apellido_usuario"];
			
			if (!isset($_POST["tipo_entrada"])) {
		?>
	    <div class="title-bar title_hc">
        	<div class="wrapper">
            	<div class="breadcrumb">
                	<ul>
                    	<li class="breadcrumb_on">Consulta Prequir&uacute;rgica L&aacute;ser (Oftalmolog&iacute;a)</li>
                    </ul>
                </div>
            </div>
        </div>
	    <?php
			}
			
			if ($tipo_accion > 0) {
				//Para verificaro que tiene permiso de hacer cambio
				$ind_editar = $dbConsultaPreqxLaserOf->getIndicadorEdicion($id_hc_consulta, $horas_edicion["valor_variable"]);
				$ind_editar_enc_hc = $ind_editar;
				if ($ind_editar == 1 && isset($_POST["tipo_entrada"])) {
					$ind_editar_enc_hc = 0;
				}
				
				$funciones_hc->encabezado_historia_clinica($id_paciente, $id_admision, $id_hc_consulta, $ind_editar_enc_hc, false);
		?>
        <div class="contenedor_principal" id="id_contenedor_principal">
	        <div id="guardar_preqx_laser" style="width: 100%; display: block;">
            	<div class="contenedor_error" id="contenedor_error"></div>
            	<div class="contenedor_exito" id="contenedor_exito"></div>
	        </div>	
	        <div class="formulario" id="principal_optometria" style="width: 100%; display: block;">
        		<?php
					//Se inserta el registro de ingreso a la historia clínica
					$dbConsultaPreqxLaserOf->crear_ingreso_hc($id_usuario_crea, $id_paciente, $id_admision, $id_hc_consulta, 160);
					
					//Se verifica la información de que ojo se va a solicitar
					$bol_od = false;
					$bol_oi = false;
					switch ($id_ojo) {
						case "79":
							$bol_od = true;
							break;
						case "80":
							$bol_oi = true;
							break;
						case "81":
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
						switch ($id_ojo) {
							case "79":
					?>
					bol_od = true;
					<?php
								break;
							case "80":
					?>
					bol_oi = true;
					<?php
								break;
							case "81":
					?>
					bol_od = true;
					bol_oi = true;
					<?php
								break;
						}
					?>
				</script>
                <form id="frm_consulta_preqx_laser_of" name="frm_consulta_preqx_laser_of" method="post">
                    <input type="hidden" name="hdd_id_hc_consulta" id="hdd_id_hc_consulta" value="<?php echo($id_hc_consulta); ?>" />
                    <input type="hidden" name="hdd_id_admision" id="hdd_id_admision" value="<?php echo($id_admision); ?>" />
	    	        <input type="hidden" name="hdd_id_convenio" id="hdd_id_convenio" value="<?php echo($admision_obj["id_convenio"]); ?>" />
    		        <input type="hidden" name="hdd_id_plan" id="hdd_id_plan" value="<?php echo($admision_obj["id_plan"]); ?>" />
                    <input type="hidden" name="hdd_ind_preconsulta" id="hdd_ind_preconsulta" value="<?php echo($ind_preconsulta); ?>" />
                    <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
                        <tr>
                            <th align="left" colspan="2">
                                <h6 style="margin:1px;">
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
                        <tr>
                        	<th align="left" style="width:75%;">    
                                <h6 style="margin:1px;">
                                    <b>Cirug&iacute;a:</b> <?php echo($tabla_preqx_laser["nombre_cirugia"]); ?>
                                    <br />
                                    <b>Fecha de la cirug&iacute;a:</b> <?php echo($tabla_preqx_laser["fecha_cirugia_t"]); ?>
                                </h6>
                            </th>   
                            <th align="left" style="width:25%;">   
                                <h6 style="margin:1px;">
                                    <b>Ojo:</b>&nbsp;
                                    <?php
										if ($id_ojo == "") {
											$id_ojo = "81";
										}
										$combo->getComboDb("cmb_ojo_laser", $id_ojo, $tabla_ojos, "id_detalle, nombre_detalle", "", "seleccionar_ojo_of(this.value);", true, "", "", "select_hc");
									?>
                                    <br />
                                    <?php echo($tabla_preqx_laser["num_cirugia"]); ?>a cirug&iacute;a
                                </h6>
                            </th>
                        </tr>
                    </table>
              <div class="tabs-container">
                <dl class="tabs" data-tab>
				  <dd id="panel_oft_2" class="active"><a href="#panel2-2" onclick="setTimeout(function() { ajustar_textareas(); }, 100);">Oftalmolog&iacute;a</a></dd>
				  <dd id="panel_oft_1" onclick="ajustar_div_optometria();"><a href="#panel2-1">Optometr&iacute;a</a></dd>
				</dl>
				<div class="tabs-content" style="padding:0px;margin: 0px;">
					<div class="content" id="panel2-1">
						<div id="div_consulta_optometria"></div>
						<?php
                            $id_menu_aux = "13";
                            if (isset($_POST["hdd_numero_menu"]) && trim($_POST["hdd_numero_menu"]) != "") {
                                $id_menu_aux = $_POST["hdd_numero_menu"];
                            }
                        ?>
					  	<script type="text/javascript">
					   		mostrar_consulta_iframe(<?php echo($id_paciente);?>, "<?php echo($nombre_paciente);?>", <?php echo($id_admision);?>, "../historia_clinica/consulta_preqx_laser.php", <?php echo($id_hc_optometria);?>, <?php echo($_POST["credencial"]);?>, <?php echo($id_menu_aux);?>, "div_consulta_optometria");
					   </script>
					</div>
					<div class="content active" id="panel2-2">
                    <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
                        <tr>
                            <td align="center" colspan="3">
                                <h5>Subjetivo</h5>
								<?php
                                    $preqx_laser_subjetivo = $utilidades->ajustar_texto_wysiwyg($preqx_laser_subjetivo);
                                ?>
                                <div id="preqx_laser_subjetivo"><?php echo($preqx_laser_subjetivo); ?></div>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" colspan="3">
                                <h5 style="margin: 10px">Biomicroscop&iacute;a</h5>
								<?php
                                    $preqx_laser_biomiocroscopia = $utilidades->ajustar_texto_wysiwyg($preqx_laser_biomiocroscopia);
                                ?>
                                <div id="preqx_laser_biomiocroscopia"><?php echo($preqx_laser_biomiocroscopia); ?></div>
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
                        <tr>
                            <td align="center" style="width:40%;">
                                <table border="0" cellpadding="1" cellspacing="0" align="center">
                                    <tr>
                                        <td align="right" style="width:60%;">	
                                            <input type="text" style="width:100px;" class="input input_hc" value="<?php echo $presion_intraocular_od; ?>" name="presion_intraocular_od" id="presion_intraocular_od" maxlength="5" onkeypress="formato_hc(event, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                        <td align="left" style="width:40%;"> 
                                            <label>mmHg</label>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td align="center" style="width:20%;"><h5>Presi&oacute;n Intraocular Neum&aacute;tica</h5></td>
                            <td align="center" style="width:40%;">
                                <table border="0" cellpadding="1" cellspacing="0" align="center">
                                    <tr>
                                        <td align="right" style="width:60%;">
                                            <input type="text" style="width: 100px;" class="input input_hc" value="<?php echo $presion_intraocular_oi; ?>" name="presion_intraocular_oi" id="presion_intraocular_oi" maxlength="5" onkeypress="formato_hc(event, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                        <td align="left" style="width:40%;"> 
                                            <label>mmHg</label>
                                        </td>
                                    </tr>
                                </table>	
                            </td>
                        </tr>
                        <tr>
                            <td align="center" colspan="3">
                                <h5>Fondo de Ojo</h5>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <textarea id="fondo_ojo_nervio_optico_od" nombre="fondo_ojo_nervio_optico_od" class="textarea" style="float:left;padding:0px;width:100%;margin:0px;" onblur="trim_cadena(this);" <?php if (!$bol_od) {?> disabled="disabled"<?php } ?>><?php echo $fondo_ojo_nervio_optico_od;?></textarea>
                                <script>
                                    $("#fondo_ojo_nervio_optico_od").textareaAutoSize();
                                    arr_textarea_ids.push("fondo_ojo_nervio_optico_od");
                                </script>
                            </td>
                            <td align="center"><label><b>Nervio &Oacute;ptico</b></label></td>
                            <td align="center">
                                <textarea id="fondo_ojo_nervio_optico_oi" nombre="fondo_ojo_nervio_optico_oi" class="textarea" style="float:left;padding:0px;width:100%;margin:0px;" onblur="trim_cadena(this);" <?php if (!$bol_oi) {?> disabled="disabled"<?php } ?>><?php echo $fondo_ojo_nervio_optico_oi;?></textarea>
                              <script>
                                    $("#fondo_ojo_nervio_optico_oi").textareaAutoSize();
                                    arr_textarea_ids.push("fondo_ojo_nervio_optico_oi");
                                </script>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <textarea id="fondo_ojo_macula_od" nombre="fondo_ojo_macula_od" class="textarea" style="float:left;padding:0px;width:100%;margin:0px;" onblur="trim_cadena(this);" <?php if (!$bol_od) {?> disabled="disabled"<?php } ?>><?php echo $fondo_ojo_macula_od;?></textarea>
                                <script>
                                    $("#fondo_ojo_macula_od").textareaAutoSize();
                                    arr_textarea_ids.push("fondo_ojo_macula_od");
                                </script>
                            </td>
                            <td align="center"><label><b>M&aacute;cula</b></label></td>
                            <td align="center">
                                <textarea id="fondo_ojo_macula_oi" nombre="fondo_ojo_macula_oi" class="textarea" style="float:left;padding:0px;width:100%;margin:0px;" onblur="trim_cadena(this);" <?php if (!$bol_oi) {?> disabled="disabled"<?php } ?>><?php echo $fondo_ojo_macula_oi;?></textarea>
                                <script>
                                    $("#fondo_ojo_macula_oi").textareaAutoSize();
                                    arr_textarea_ids.push("fondo_ojo_macula_oi");
                                </script>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <textarea id="fondo_ojo_periferia_od" nombre="fondo_ojo_periferia_od" class="textarea" style="float:left;padding:0px;width:100%;margin:0px;" onblur="trim_cadena(this);" <?php if (!$bol_od) {?> disabled="disabled"<?php } ?>><?php echo $fondo_ojo_periferia_od;?></textarea>
                                <script>
                                    $("#fondo_ojo_periferia_od").textareaAutoSize();
                                    arr_textarea_ids.push("fondo_ojo_periferia_od");
                                </script>
                            </td>
                            <td align="center"><label><b>Periferia</b></label></td>
                            <td align="center">
                                <textarea id="fondo_ojo_periferia_oi" nombre="fondo_ojo_periferia_oi" class="textarea" style="float:left;padding:0px;width:100%;margin:0px;" onblur="trim_cadena(this);" <?php if (!$bol_oi) {?> disabled="disabled"<?php } ?>><?php echo $fondo_ojo_periferia_oi;?></textarea>
							    <script>
                                    $("#fondo_ojo_periferia_oi").textareaAutoSize();
                                    arr_textarea_ids.push("fondo_ojo_periferia_oi");
                                </script>
                            </td>
                        </tr>
                        <tr>
                            <th align="center">
                                <textarea id="fondo_ojo_vitreo_od" nombre="fondo_ojo_vitreo_od" class="textarea" style="float:left;padding:0px;width:100%;margin:0px;" onblur="trim_cadena(this);" <?php if (!$bol_od) {?> disabled="disabled"<?php } ?>><?php echo $fondo_ojo_vitreo_od;?></textarea>
                                <script>
                                    $("#fondo_ojo_vitreo_od").textareaAutoSize();
                                    arr_textarea_ids.push("fondo_ojo_vitreo_od");
                                </script>
                            </th>
                            <th align="center"><label><b>V&iacute;treo</b></label></th>
                            <th align="center">
                                <textarea id="fondo_ojo_vitreo_oi" nombre="fondo_ojo_vitreo_oi" class="textarea" style="float:left;padding:0px;width:100%;margin:0px;" onblur="trim_cadena(this);" <?php if (!$bol_oi) {?> disabled="disabled"<?php } ?>><?php echo $fondo_ojo_vitreo_oi;?></textarea>
                                <script>
                                    $("#fondo_ojo_vitreo_oi").textareaAutoSize();
                                    arr_textarea_ids.push("fondo_ojo_vitreo_oi");
                                </script>
                            </th>
                        </tr>
                        <tr>
                            <td align="center" colspan="3">
                                <h5>Plan</h5>
								<?php
                                    $preqx_laser_plan = $utilidades->ajustar_texto_wysiwyg($preqx_laser_plan);
                                ?>
                                <div id="preqx_laser_plan"><?php echo($preqx_laser_plan); ?></div>
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
                        <tr>
                            <td align="left" colspan="3">
                                <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:100%;">	
                                    <tr>
                                        <td align="center" style="width:30%;">
                                            <h6>Diagn&oacute;sticos</h6>
                                            <?php
												$class_diagnosticos->getFormularioDiagnosticos($id_hc_consulta);
                                            ?>
                                            <label><b>Otros diagn&oacute;sticos y an&aacute;lisis</b></label>
											<?php
                                                $diagnostico_preqx_laser_of = $utilidades->ajustar_texto_wysiwyg($diagnostico_preqx_laser_of);
                                            ?>
                                            <div id="diagnostico_preqx_laser_of"><?php echo($diagnostico_preqx_laser_of); ?></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="width:30%;">
                                            <label><b>Solicitud de Procedimientos y Ex&aacute;menes Complemetarios</b></label>
											<?php
												$class_solic_procs->getFormularioSolicitud($id_hc_consulta);
												
                                                $solicitud_examenes_preqx_laser = $utilidades->ajustar_texto_wysiwyg($solicitud_examenes_preqx_laser);
                                            ?>
                                            <div id="solicitud_examenes_preqx_laser"><?php echo($solicitud_examenes_preqx_laser); ?></div>
                                        </td>
                                    </tr>
                                    <tr>	
                                        <td align="center" style="width:30%;">
                                            <label><b>Recomendaciones Cl&iacute;nicas, M&eacute;dicas, Optom&eacute;tricas y Quir&uacute;rgicas&nbsp;</b></label>
											<?php
                                                $tratamiento_preqx_laser = $utilidades->ajustar_texto_wysiwyg($tratamiento_preqx_laser);
                                            ?>
                                            <div id="tratamiento_preqx_laser"><?php echo($tratamiento_preqx_laser); ?></div>
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
                                        <td align="center" style="width:30%;">
                                            <label><b>F&oacute;rmula M&eacute;dica</b></label>
                                            <textarea style="text-align: justify;" class="textarea_oftalmo" id="medicamentos_preqx_laser" nombre="medicamentos_preqx_laser" onblur="trim_cadena(this);"><?php echo $medicamentos_preqx_laser;?></textarea>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:90%;">
                      <tr valign="top">
                            <td colspan="3">
								<?php
									if (!isset($_POST["tipo_entrada"])) {
								?>
                                <input class="btnPrincipal" type="button" id="btn_imprimir" nombre="btn_imprimir" value="Imprimir" onclick="crear_preqx_laser_of(2, 1);" />
                                <?php
									} else {
								?>
                                <input class="btnPrincipal" type="button" id="btn_imprimir" nombre="btn_imprimir" value="Imprimir" onclick="imprimir_preqx_laser_of();" />
                                <?php
									}
									
									if ($ind_editar == 1) {
										if (!isset($_POST["tipo_entrada"])) {
								?>
                                <input type="button" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Guardar cambios" onclick="crear_preqx_laser_of(2, 0);"/>
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
                                <input class="btnPrincipal" type="button" id="btn_crear" nombre="btn_crear" value="Finalizar preconsulta" onclick="crear_preqx_laser_of(4, 0);" />
                                <?php
											} else {
								?>
                                <input class="btnPrincipal" type="button" id="btn_crear" nombre="btn_crear" value="Finalizar consulta" onclick="crear_preqx_laser_of(1, 0);" />
                                <?php
											}
										} else {
								?>
                                <input type="button" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Guardar" onclick="crear_preqx_laser_of(3, 0);"/>
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
				</form>
            </div>
        </div>
        <?php
			} else {
		?>
        <div class="contenedor_principal" id="id_contenedor_principal">
            <div class="contenedor_error" style="display:block;">Error al ingresar a la consulta prequir&uacute;rgica l&aacute;ser</div>
        </div>
        <?php
			}
    	?>
	   <script type="text/javascript" src="../js/foundation.min.js"></script>
       <script>
            $(document).foundation();
			
			initCKEditorPreQx("preqx_laser_subjetivo");
			initCKEditorPreQx("preqx_laser_biomiocroscopia");
			initCKEditorPreQx("preqx_laser_plan");
			initCKEditorPreQx("diagnostico_preqx_laser_of");
			initCKEditorPreQx("solicitud_examenes_preqx_laser");
			initCKEditorPreQx("tratamiento_preqx_laser");
       </script>
    	<?php
			if (!isset($_POST["tipo_entrada"])) {
				$contenido->ver_historia($id_paciente);
				$contenido->footer();
			} else {
				$contenido->footer_iframe();
			}
		?>
    </body>
</html>
