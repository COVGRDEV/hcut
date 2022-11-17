<?php session_start();
	/*
	 * Pagina para crear consulta de evolución 
	 * Autor: Feisar Moreno - 14/02/2014
	 */
	require_once("../db/DbVariables.php");
	require_once("../db/DbExamenesOptometria.php");
	require_once("../db/DbAdmision.php");
	require_once("../db/DbPacientes.php");
	require_once("../db/DbTiposCitas.php");
	require_once("../db/DbTiposCitasDetalle.php");
	require_once("../db/DbMaestroExamenes.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/Class_Barra_Progreso.php");
	require_once("../funciones/Class_Examenes_Op.php");
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/Class_Diagnosticos.php");
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbMenus.php");
	require_once("FuncionesHistoriaClinica.php");
	
	$dbVariables = new Dbvariables();
	$dbExamenesOptometria = new DbExamenesOptometria();
	$dbAdmision = new DbAdmision();
	$dbPacientes = new DbPacientes();
	$dbTiposCitas = new DbTiposCitas();
	$dbTiposCitasDetalle = new DbTiposCitasDetalle();
	$dbMaestroExamenes = new DbMaestroExamenes();
	
	$contenido = new ContenidoHtml();
	$combo = new Combo_Box();
	$barra_progreso = new Barra_Progreso();
	$utilidades = new Utilidades();
	$class_diagnosticos = new Class_Diagnosticos();
	$usuarios = new DbUsuarios();
	$menus = new DbMenus();
	$funciones_hc = new FuncionesHistoriaClinica();
	
	//variables
	$titulo = $dbVariables->getVariable(1);
	$horas_edicion = $dbVariables->getVariable(7);
	
	//Cambiar las variables get a post
	$utilidades->get_a_post();
	$class_examenes_op = new Class_Examenes_Op();
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
    <script type="text/javascript" src="../js/jquery_autocompletar.js"></script>
    <script type="text/javascript" src="../js/jquery-min.js"></script>
    <script type="text/javascript" src="../js/jquery-ui.js"></script>
    <script type="text/javascript" src="../js/jquery.validate.js"></script>
    <script type="text/javascript" src="../js/jquery.validate.add.js"></script>
    <script type="text/javascript" src="../js/ajax.js"></script>
    <script type="text/javascript" src="../js/funciones.js"></script>
    <script type="text/javascript" src="../js/validaFecha.js"></script>
    <script type="text/javascript" src="../funciones/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="../funciones/ckeditor/config.js"></script>
    <script type="text/javascript" src="historia_clinica_v1.1.js"></script>
    <script type="text/javascript" src="../js/Class_Diagnosticos_v1.2.js"></script>
    <script type="text/javascript" src="../js/Class_Examenes_Op_v1.11.js"></script>
    <script type="text/javascript" src="../js/Class_Color_Pick.js"></script>
    <script type="text/javascript" src="FuncionesHistoriaClinica.js"></script>
    <script type="text/javascript" src="registro_examen_op_v1.11.js"></script>
</head>
<body>
	<?php
		$contenido->validar_seguridad(0);
		if (!isset($_POST["tipo_entrada"])) {
	    	$contenido->cabecera_html();	
    	}
		$id_tipo_reg = 10;
		$id_usuario = $_SESSION["idUsuario"];
		
		if (isset($_POST["hdd_id_paciente"])) {
			$id_paciente = $utilidades->str_decode($_POST["hdd_id_paciente"]);
			$nombre_paciente = $utilidades->str_decode($_POST["hdd_nombre_paciente"]);
			$id_admision = $utilidades->str_decode($_POST["hdd_id_admision"]);
			
			//Se obtienen los datos de la admision
			$admision_obj = $dbAdmision->get_admision($id_admision);
			
			//Se obtienen los datos del tipo de cita
			$tipo_cita_obj = $dbTiposCitas->get_tipo_cita($admision_obj["id_tipo_cita"]);
			
			//Se obtiene el tipo de registro
			$lista_tipo_cita_detalle_aux = $dbTiposCitasDetalle->get_lista_tipos_citas_det_clases($admision_obj["id_tipo_cita"], 3);
			if (count($lista_tipo_cita_detalle_aux) > 0) {
				$id_tipo_reg = $lista_tipo_cita_detalle_aux[0]["id_tipo_reg"];
			}
			
			$id_examen = "";
			$observaciones_examen = "";
			$ruta_arch_examen = "";
			
			if (!isset($_POST["tipo_entrada"])) {
				$tabla_hc = $dbExamenesOptometria->getHistoriaClinicaAdmision($id_admision, $id_tipo_reg);
	    	} else {
				$id_hc = $_POST["hdd_id_hc"];
				$tabla_hc = $dbExamenesOptometria->getHistoriaClinicaId($id_hc);
			}
			
			if (count($tabla_hc) > 0) { //Si se encuentra una HC para este paciente se carga los datos de lo contrario se crea la HC
				$tipo_accion = "2"; //Editar consulta de evolución
				$id_hc_examen = $tabla_hc["id_hc"];
				$nombre_usuario_alt = $tabla_hc["nombre_usuario_alt"];
				
				//se obtiene el registro a partir del ID de la Historia Clinica 
				$examen_obj = $dbExamenesOptometria->get_examen_optometria($id_hc_examen);
				
				//Se verifica si se debe actualizar el estado de la admisión asociada
				$en_atencion = "0";
				if (isset($_POST["hdd_en_atencion"])) {
					$en_atencion = $_POST["hdd_en_atencion"];
				}
				
				if ($en_atencion == "1") {
					$dbAdmision->editar_admision_estado($id_admision, 14, 1, $id_usuario);
				}
			} else { //Entre en procesos de crear HC
				$tipo_accion = "1";
				
				$nombre_usuario_alt = $tabla_hc["nombre_usuario_alt"];
				
				//Se crea la historia clinica y se inicia el exámen
				$id_hc_examen = $dbExamenesOptometria->crear_examen_optometria($id_paciente, $id_tipo_reg, $id_usuario, $id_admision);
				if ($id_hc_examen > 0) {
					$examen_obj = $dbExamenesOptometria->get_examen_optometria($id_hc_examen);
				} else {
					$tipo_accion = "0";
				}
			}
		
			//Se obtienen los datos del registro de historia clínica
			$historia_clinica_obj = $dbExamenesOptometria->getHistoriaClinicaId($id_hc_examen);
		} else {
			$tipo_accion = "0"; //Ninguna accion Error
		}
		
		//Edad del paciente
		$datos_paciente = $dbPacientes->getEdadPaciente($id_paciente, "");
		$edad_paciente = $datos_paciente["edad"];
		
		//Nombre del profesional que atiende la consulta
		$id_usuario_profesional = $examen_obj["id_usuario_crea"];
		$usuario_profesional_obj = $usuarios->getUsuario($id_usuario_profesional);
		$nombre_usuario_profesional = $usuario_profesional_obj["nombre_usuario"]." ".$usuario_profesional_obj["apellido_usuario"];
		
		if (!isset($_POST["tipo_entrada"])) {
    ?>
    <div class="title-bar title_hc">
        <div class="wrapper">
            <div class="breadcrumb">
                <ul>
                    <li class="breadcrumb_on">Ex&aacute;men - Optometr&iacute;a</li>
                </ul>
            </div>
        </div>
    </div>
    <?php
		}
		
		if ($tipo_accion > 0) {
			//Para verificaro que tiene permiso de hacer cambio
			$ind_editar = $dbExamenesOptometria->getIndicadorEdicion($id_hc_examen, $horas_edicion["valor_variable"]);
			$ind_editar_enc_hc = $ind_editar;
			if ($ind_editar == 1 && isset($_POST["tipo_entrada"])) {
				$ind_editar_enc_hc = 0;
			}
			
			$funciones_hc->encabezado_historia_clinica($id_paciente, $id_admision, $id_hc_examen, $ind_editar_enc_hc, false);
    ?>
	<div class="contenedor_principal" id="id_contenedor_principal">
    	<div id="d_guardar_examen" style="width: 100%; display: block;">
        	<div class="contenedor_error" id="contenedor_error"></div>
            <div class="contenedor_exito" id="contenedor_exito"></div>
        </div>	
        <div class="formulario" id="principal_evolucion" style="width:100%; display:block;">
        	<?php
				//Se inserta el registro de ingreso a la historia clínica
				$dbExamenesOptometria->crear_ingreso_hc($id_usuario, $id_paciente, $id_admision, $id_hc_examen, 160);
	        ?>
            <input type="hidden" name="hdd_id_hc_examen" id="hdd_id_hc_examen" value="<?php echo($id_hc_examen); ?>" />
            <div id="d_consulta_evolucion" name="d_consulta_evolucion">
        	    <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
            		<tr valign="middle">
		                <td align="center" colspan="2">
        		            <div class="contenedor_error" id="contenedor_error"></div>
                		    <div class="contenedor_exito" id="contenedor_exito"></div>
		                </td>
        		    </tr>	
                    <tr>
						<th align="left" colspan="2">
                        	<?php
								$observacion_aux = trim($admision_obj["observacion_cita"]);
								if ($observacion_aux == "") {
									$observacion_aux = "-";
								}
							?>
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
                    <?php
                    	if ($tipo_cita_obj["ind_preqx"] == "1") {
					?>
                    <tr>
                    	<th align="left" style="width:90%;">
                        	<h6>
                            	<b>Cirug&iacute;a:</b> <?php echo($examen_obj["nombre_cirugia"]); ?>
                                <br />
                                <b>Fecha de la cirug&iacute;a:</b> <?php echo($examen_obj["fecha_cirugia_t"]); ?>
                            </h6>
                        </th>
                        <th align="left" style="width:10%;">
                        	<h6>
                            	<b>Ojo:</b> <?php echo($examen_obj["ojo"]); ?>
                                <br />
                                <?php echo($examen_obj["num_cirugia"]); ?>a cirug&iacute;a
                            </h6>
                        </th>
                    </tr>
                    <?php
						}
					?>
                </table>
                <div class="tabs-container">
                    <dl class="tabs" data-tab>
                        <dd id="panel_oft_1" class="active"><a href="#panel2-1">Ex&aacute;menes</a></dd>
                        <dd id="panel_oft_2"><a href="#panel2-2">Diagn&oacute;stico</a></dd>
                    </dl>
                    <div class="tabs-content" style="padding:0px;margin: 0px;">
                    	<div class="content active" id="panel2-1">
                            <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
                                <tr>
                                    <td align="center">
                                        <h5 style="margin: 10px"></h5>
                                        <?php
                                            $class_examenes_op->getFormularioExamenesOp($id_hc_examen, $id_admision);
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="content" id="panel2-2">
                            <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:98%;">
                                <tr>
                                    <td align="center">
                                    	<h6>Diagn&oacute;sticos</h6>
                                        <?php
											$class_diagnosticos->getFormularioDiagnosticos($id_hc_examen);
										?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:98%;">
                	<tr valign="top">
                    	<td>
						   <?php
								if (!isset($_POST["tipo_entrada"])) {
							?>
							<input type="button" id="btn_imprimir" nombre="btn_imprimir" class="btnPrincipal" value="Imprimir" onclick="guardar_examen(2, 1);" />
							<?php
								} else {
							?>
							<input type="button" id="btn_imprimir" nombre="btn_imprimir" class="btnPrincipal" value="Imprimir" onclick="imprimir_examen();" />
							<?php
								}
								
                               if ($ind_editar == 1) {
                                   if (!isset($_POST["tipo_entrada"])) {
                            ?>
                        	<input type="button" id="btn_guardar" nombre="btn_guardar" class="btnPrincipal" value="Guardar cambios" onclick="guardar_examen(2, 0);" />
                            <input type="button" id="btn_finalizar" nombre="btn_finalizar" class="btnPrincipal" value="Finalizar examen" onclick="guardar_examen(1, 0);" />
							<?php
                                    } else {
                            ?>
                        	<input type="button" id="btn_finalizar" nombre="btn_finalizar" class="btnPrincipal" value="Guardar" onclick="guardar_examen(3, 0);" />
							<?php
                                    }
                                }
                            ?>
                        </td>
                    </tr>
                    <tr>
                    	<td align="center">
                        	<?php
                            	$barra_progreso->get("d_barra_progreso_ex", "50%", false, 0);
							?>
                        </td>
                    </tr>
                    <tr>
                    	<td align="center">
                        	<div id="d_img_espera_examen" style="display:none;">
                            	<img src="../imagenes/ajax-loader.gif" />
                            </div>
                        </td>
                    </tr>
                </table>
			</div>
	    </div>
    </div>
	<?php
		} else {
	?>
    <div class="contenedor_principal" id="id_contenedor_principal">
	    <div class="contenedor_error" style="display:block;">Error al ingresar al registro de ex&aacute;menes de optometr&iacute;a</div>
    </div>
    <?php
		}
	?>
    <script type="text/javascript" src="../js/foundation.min.js"></script>
    <script>
		$(document).foundation();
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
