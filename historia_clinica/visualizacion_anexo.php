<?php session_start();
	/*
	 * Pagina para crear consulta de evolución 
	 * Autor: Feisar Moreno - 14/02/2014
	 */
	require_once("../db/DbVariables.php");
	require_once("../db/DbHistoriaClinica.php");
	require_once("../db/DbPacientes.php");
	require_once("../db/DbUsuarios.php");
	
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/Class_Examenes_Op.php");
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/Class_Diagnosticos.php");
	require_once("FuncionesHistoriaClinica.php");
	
	$dbVariables = new Dbvariables();
	$dbHistoriaClinica = new DbHistoriaClinica();
	$dbPacientes = new DbPacientes();
	$dbUsuarios = new DbUsuarios();
	
	$contenido = new ContenidoHtml();
	$utilidades = new Utilidades();
	$class_diagnosticos = new Class_Diagnosticos();
	$funciones_hc = new FuncionesHistoriaClinica();
	
	//Variables
	$titulo = $dbVariables->getVariable(1);
	$horas_edicion = $dbVariables->getVariable(7);
	
	//Cambiar las variables get a post
	$utilidades->get_a_post();
	$combo = new Combo_Box();
	$class_examenes_op = new Class_Examenes_Op();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $titulo['valor_variable']; ?></title>
    <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
    <link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
    <link href="../css/azul.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="../js/jquery_autocompletar.js"></script>
    <script type="text/javascript" src="../js/jquery-min.js"></script>
    <script type="text/javascript" src="../js/jquery-ui.js"></script>
    <script type="text/javascript" src="../js/jquery.validate.js"></script>
    <script type="text/javascript" src="../js/jquery.validate.add.js"></script>
    <script type="text/javascript" src="../js/ajax.js"></script>
    <script type="text/javascript" src="../js/funciones.js"></script>
    <script type="text/javascript" src="../js/validaFecha.js"></script>
    <script type='text/javascript' src='historia_clinica_v1.8.js'></script>
    <script type="text/javascript" src="visualizacion_anexo_v1.4.js"></script>
</head>
<body>
	<?php
		function cambiar_enter_br($texto) {
			$texto_alt = str_replace(chr(10), "<br />", $texto);
			if ($texto == $texto_alt) {
				$texto_alt = str_replace(chr(13), "<br />", $texto);
			}
			
			return $texto_alt;
		}
		
		$contenido->validar_seguridad(0);
		if (!isset($_POST['tipo_entrada'])) {
	    	$contenido->cabecera_html();	
    	}
		$id_tipo_reg = 10;
		$id_usuario = $_SESSION["idUsuario"];
		$id_hc = "0";
		
		if (isset($_POST['hdd_id_paciente'])) {
			$id_paciente = $utilidades->str_decode($_POST['hdd_id_paciente']);
			$nombre_paciente = $utilidades->str_decode($_POST['hdd_nombre_paciente']);
			$id_admision = $utilidades->str_decode($_POST['hdd_id_admision']);
			
			$id_hc = $_POST['hdd_id_hc'];
			$tabla_hc = $dbHistoriaClinica->getHistoriaClinicaId($id_hc);
			$id_tipo_reg = $tabla_hc["id_tipo_reg"];
		}
		
		//Edad del paciente
		$datos_paciente = $dbPacientes->getEdadPaciente($id_paciente, '');
		$edad_paciente = $datos_paciente['edad'];
		
		//Se obtiene la ruta actual de las imágenes
		$arr_ruta_base = $dbVariables->getVariable(17);
		$ruta_base = $arr_ruta_base["valor_variable"];
		
		//Nombre del profesional que atiende la consulta
		$id_usuario_profesional = $tabla_hc['id_usuario_crea'];
		$tabla_usuario_profesional = $dbUsuarios->getUsuario($id_usuario_profesional);
		$nombre_usuario_profesional = $tabla_usuario_profesional['nombre_usuario'].' '.$tabla_usuario_profesional['apellido_usuario'];
		
		$funciones_hc->encabezado_historia_clinica($id_paciente, $id_admision);
    ?>
	<div class="contenedor_principal" id="id_contenedor_principal">
        <div class="formulario" id="principal_evolucion" style="width:100%; display:block;">
        	<?php
				//Se inserta el registro de ingreso a la historia clínica
				$dbHistoriaClinica->crear_ingreso_hc($id_usuario, $id_paciente, $id_admision, $id_hc, 160);
	        ?>
            <input type="hidden" name="hdd_id_hc" id="hdd_id_hc" value="<?php echo($id_hc); ?>" />
            <div id="d_consulta_evolucion" name="d_consulta_evolucion">
        	    <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
                    <tr>
						<th align="left">
                            <h6 style="margin: 1px;">
                            	<b>Anexo agregado por:</b>&nbsp;<?php echo($nombre_usuario_profesional); ?>
                                <br />
                                <b>Tipo de anexo:</b>&nbsp;<?php echo($tabla_hc["nombre_alt_tipo_reg"]); ?>
                            </h6>
                        </th>
                    </tr>
                    <tr style="height:10px;"></tr>
                    <?php
						$observaciones_hc = $tabla_hc["observaciones_hc"];
                    	if ($observaciones_hc == "") {
							$observaciones_hc = "-";
						}
					?>
                    <tr>
                    	<td align="left">
                        	<label><b>Observaciones:&nbsp;</b><?php echo(cambiar_enter_br($observaciones_hc)); ?></label>
                        </td>
                    <tr>
                </table>
                <?php
					//Se borran las imágenes temporales creadas por el usuario actual
					$ruta_tmp = "../historia_clinica/tmp/".$id_usuario;
					@mkdir($ruta_tmp);
					
					//Se obtienen los archivos adjuntos
					$lista_arch_adjuntos = $dbHistoriaClinica->getListaHCArchivosAdjuntos($id_hc);
					

					if (count($lista_arch_adjuntos) == 0 && $tabla_hc["ruta_arch_adjunto"] != "") {
						$arr_aux = array("ruta_archivo" => $tabla_hc["ruta_arch_adjunto"], "cont_arch" => "0");
						array_push($lista_arch_adjuntos, $arr_aux);
					}
				?>
                <input type="hidden" id="hdd_cant_archivos_anexos" value="<?php echo(count($lista_arch_adjuntos)); ?>" />
                <?php
					//Se verifica si hay algún archivo que mostrar
					$ind_imprimir = false;
					if (count($lista_arch_adjuntos) > 0) {
						foreach ($lista_arch_adjuntos as $i => $arch_adjunto) {
							$ruta_arch_adjunto = $arch_adjunto["ruta_archivo"];
							
							//Se obtiene el tipo de archivo
							$extension = $utilidades->get_extension_arch($ruta_arch_adjunto);
							
							$ruta_arch_adjunto = str_replace("../imagenes/imagenes_hce", $ruta_base, $ruta_arch_adjunto);
							copy($ruta_arch_adjunto, $ruta_tmp."/img_adjunto_".$id_hc."_".$arch_adjunto["cont_arch"].".".$extension);
							$ruta_arch_adjunto = $ruta_tmp."/img_adjunto_".$id_hc."_".$arch_adjunto["cont_arch"].".".$extension;
							
							$ancho_max = 990;
							
							$display_aux = "none";
							$display_aux2 = "none";
							if ($i == 0) {
								$display_aux = "block";
								$display_aux2 = "table-row";
							}
				?>
                <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
                    <tr id="tr_zoom_in_<?php echo($i); ?>" style="display:<?php echo($display_aux2); ?>;">
                    	<td align="right">
                        	<img id="img_zoom_in_<?php echo($i); ?>" src="../imagenes/icon-zoom-in.png" title="Pantalla completa" class="img_button" style="margin:0; width:24px;" onClick="mostrar_archivo_pantalla_completa(<?php echo($i); ?>);" />
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <?php
								switch ($extension) {
									case "jpg":
									case "png":
									case "bmp":
									case "gif":
									case "tiff":
										$ind_imprimir = true;
										
										//Se obtienen las dimensiones del archivo
										@$arr_prop_imagen = getimagesize($ruta_arch_adjunto);
										$ancho_aux = $arr_prop_imagen[0];
										$alto_aux = $arr_prop_imagen[1];
							?>
                            <div id="d_archivo_completo_<?php echo($i); ?>" class="div_pantalla_completa" style="display:none;">
                                <input type="hidden" id="hdd_zoom_act_<?php echo($i); ?>" value="0" />
                                <input type="hidden" id="hdd_ancho_act_<?php echo($i); ?>" value="<?php echo($ancho_aux); ?>" />
                                <input type="hidden" id="hdd_alto_act_<?php echo($i); ?>" value="<?php echo($alto_aux); ?>" />
                                <div style="width:100%; height:35px;">
                                    <img src="../imagenes/icon-zoom-out.png" title="Vista normal" class="img_button" style="margin:5px; width:24px; float:right;" onClick="ocultar_archivo_pantalla_completa(<?php echo($i); ?>);" />
                                    <div id="d_label_zoom_<?php echo($i); ?>" style="margin:10px; float:right;"><b>Zoom: 100%</b></div>
                                </div>
                                <div class="div_img_pantalla_completa" style="width:99%; height:calc(99vh - 35px);">
                                    <img id="img_drag_<?php echo($i); ?>" src="<?php echo($ruta_arch_adjunto); ?>" class="ui-widget-content" style="cursor:all-scroll;" onDblClick="ocultar_archivo_pantalla_completa(<?php echo($i); ?>);" />
                                </div>
                                <script id="ajax" type="text/javascript">
									$(function() {
										$("#img_drag_<?php echo($i); ?>").draggable();
									});
									
									$("#img_drag_<?php echo($i); ?>").bind("mousewheel",
										function(e) {
											var direccion = "";
											if (e.originalEvent.wheelDelta > 0) {
												//scroll down
												direccion = "D";
											} else {
												//scroll up
												direccion = "U";
											}
											
											manejar_zoom_imagen(direccion, <?php echo($i); ?>);
											
											return false;
										}
									);
								</script>
                            </div>
                            <?php
										if ($ancho_aux > $ancho_max) {
											$ancho_aux = $ancho_max;
										}
							?>
                            <div id="d_archivo_muestra_<?php echo($i); ?>" class="div_contenedor_archivo" style="height:450px; display:<?php echo($display_aux); ?>">
				            	<img src="<?php echo($ruta_arch_adjunto); ?>" style="width:<?php echo($ancho_aux); ?>px;" onDblClick="mostrar_archivo_pantalla_completa(<?php echo($i); ?>);" />
                            </div>
							<?php
										break;
										
									case "pdf":
										$ind_imprimir = true;
							?>
                            <div id="d_archivo_completo_<?php echo($i); ?>" class="div_pantalla_completa" style="display:none;">
                                <div style="width:100%; height:35px;">
                                    <img src="../imagenes/icon-zoom-out.png" title="Vista normal" class="img_button" style="margin:5px; width:24px; float:right;" onClick="ocultar_archivo_pantalla_completa(<?php echo($i); ?>);" />
                                </div>
                                <div style="margin:auto; width:99%; height:calc(99vh - 35px); border:1px solid #333; overflow:auto;">
                                    <embed src="<?php echo($ruta_arch_adjunto); ?>" width="100%" height="100%"></embed>
                                </div>
                            </div>
                            <div id="d_archivo_muestra_<?php echo($i); ?>" class="div_contenedor_archivo" style="height:450px; display:<?php echo($display_aux); ?>">
                            	<embed src="<?php echo($ruta_arch_adjunto); ?>" width="<?php echo($ancho_max); ?>" height="445"></embed>
                            </div>
                			<?php
										break;
										
									default:
										echo("Formato de archivo no soportado (".$extension.")");
										break;
								}
							?>
                        </td>
                    </tr>
                </table>
                <?php
						}
					}
				?>
        	    <input type="hidden" id="hdd_archivo_visible" value="0" />
                <?php
					//Se crea una barra para mostrar los diferentes archivos
					if (count($lista_arch_adjuntos) > 1) {
				?>
                <div class="pager">
                    <?php
						for ($i = 0; $i < count($lista_arch_adjuntos); $i++) {
							$activo_aux = "";
							if ($i == 0) {
								$activo_aux = "active";
							}
					?>
            	    <span id="sp_archivo_adjunto_<?php echo($i); ?>" class="page-number clickable <?php echo($activo_aux); ?>" onClick="mostrar_archivo_anexo(<?php echo($i); ?>);"><?php echo($i + 1); ?></span>
            	    <?php
						}
					?>
        	    </div>
        	    <?php
					}
				?>
                <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:98%;">
                	<tr valign="top">
                    	<td>
			                <?php
            			    	if ($ind_imprimir) {
							?>
							<input class="btnPrincipal" type="button" id="btn_imprimir" nombre="btn_imprimir" value="Imprimir" onclick="imprimir_anexo_hc();" />
			                <?php
								}
							?>
                        </td>
                    </tr>
                    <tr>
                    	<td align="center">
                        	<div id="d_img_espera_anexo" style="display:none;">
                            	<img src="../imagenes/ajax-loader.gif" />
                            </div>
                        </td>
                    </tr>
                </table>
			</div>
	    </div>
    </div>
    <script type='text/javascript' src='../js/foundation.min.js'></script>
    <script>
		$(document).foundation();
    </script>
    <?php
	   if(!isset($_POST['tipo_entrada'])){
	    	$contenido->ver_historia($id_paciente);
		    $contenido->footer();
	   }
	   else{
			$contenido->footer_iframe();
	   }
	?>
</body>
</html>
