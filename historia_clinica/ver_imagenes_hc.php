<?php session_start();
	/*
	 * Pagina para la visualización de imágenes asociadas a la historia clínica
	 * Autor: Feisar Moreno - 23/01/2017
	 */
	require_once("../db/DbVariables.php");
	require_once("../db/DbPermisos.php");
	require_once("../db/DbPacientes.php");
	require_once("../db/DbExamenesOptometria.php");
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../principal/ContenidoHtml.php");
	
	$dbVariables = new Dbvariables();
	$dbPermisos = new DbPermisos();
	$dbPacientes = new DbPacientes();
	$dbExamenesOptometria = new DbExamenesOptometria();
	
	$utilidades = new Utilidades();
	$funciones_persona = new FuncionesPersona();
	$contenidoHtml = new ContenidoHtml();
	
	//variables
	$titulo = $dbVariables->getVariable(1);
	
	//Cambiar las variables get a post
	$utilidades->get_a_post();
	
	$id_usuario = $_SESSION["idUsuario"];
	$id_menu = 65;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo($titulo); ?></title>
    <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
    <link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
    <link href="../css/azul.css" rel="stylesheet" type="text/css" />
    
    <script type="text/javascript" src="../js/jquery_autocompletar.js"></script>
    <script type="text/javascript" src="../js/jquery-ui.js"></script>
    <script type="text/javascript" src="../js/jquery.validate.js"></script>
    <script type="text/javascript" src="../js/jquery.validate.add.js"></script>
    <script type="text/javascript" src="../js/ajax.js"></script>
    <script type="text/javascript" src="../js/funciones.js"></script>
    <script type="text/javascript" src="FuncionesHistoriaClinica.js"></script>
    <script type="text/javascript" src="ver_imagenes_hc.js"></script>
</head>
<body>
	<input type="hidden" id="credencial" value="<?php echo($id_usuario); ?>" />
	<input type="hidden" id="hdd_numero_menu" value="<?php echo($id_menu); ?>" />
    <div class="div_pantalla_completa" id="id_contenedor_principal">
        <div class='contenedor_error' id='contenedor_error'></div>
        <div class='contenedor_exito' id='contenedor_exito'></div>
        <div class="formulario" id="principal_imagenes_hc" style="width: 100%; display: block;">
	        <?php
				//Se valida que se tengan los permisos apropiados
				$permiso_obj = $dbPermisos->getPermisoUsuarioMenu($id_usuario, $id_menu);
				if (isset($permiso_obj["tipo_acceso"]) && ($permiso_obj["tipo_acceso"] == "1" || $permiso_obj["tipo_acceso"] == "2")) {
					$id_paciente = $utilidades->str_decode($_POST["hdd_id_paciente"]);
					$paciente_obj = $dbPacientes->getEdadPaciente($id_paciente, "");
			?>
            <table border="0" cellpadding="2" cellspacing="0" align="center" style="width:95%;">
            	<tr>
                	<td align="left" valign="top" style="width:50%;">
                    	<?php
                        	$nombre_completo = $funciones_persona->obtenerNombreCompleto($paciente_obj["nombre_1"], $paciente_obj["nombre_2"], $paciente_obj["apellido_1"], $paciente_obj["apellido_2"]);
							$telefonos = $paciente_obj["telefono_1"];
							if ($paciente_obj["telefono_2"] != "") {
								$telefonos .= " - ".$paciente_obj["telefono_2"];
							}
						?>
                    	<h6>
                        	<b>Paciente:</b>&nbsp;<?php echo($nombre_completo); ?><br />
                            <b>Tipo de documento:</b>&nbsp;<?php echo($paciente_obj["tipo_documento"]); ?><br />
                            <b>Tel&eacute;fono(s):</b>&nbsp;<?php echo($telefonos); ?>
                        </h6>
                    </td>
                	<td align="left" valign="top" style="width:50%;">
                    	<h6>
                        	<b>Edad:</b>&nbsp;<?php echo($paciente_obj["edad"]); ?>&nbsp;a&ntilde;os<br />
                            <b>N&uacute;mero de identificaci&oacute;n:</b>&nbsp;<?php echo($paciente_obj["numero_documento"]); ?><br />
                            <b>Fecha de nacimiento:</b>&nbsp;<?php echo($funciones_persona->obtenerFecha6($paciente_obj["fecha_nacimiento_t"])); ?>
                        </h6>
                    </td>
                </tr>
            </table>
            <div id="d_lista_imagenes_hc">
            	<?php
                	//Se obtiene el listado de imágenes
					$lista_examenes = $dbExamenesOptometria->get_lista_examenes_optometria_hc_det_paciente($id_paciente);
					if (count($lista_examenes) > 0) {
				?>
                <div id="d_btn_impr_img_hc_1" style="display:block; height:40px;">
                    <input type="button" value="Imprimir im&aacute;genes" class="btnPrincipal peq" onclick="imprimir_imagenes_hc(<?php echo($id_paciente); ?>);" />
                </div>
                <div id="d_btn_impr_img_hc_2" style="display:none; height:40px;">
                    <img src="../imagenes/ajax-loader.gif" />
                </div>
                <div id="d_impresion_img_hc" style="display:none;"></div>
                <?php
						//Se crea el mapa de exámenes
						$mapa_examenes = array();
						$mapa_fechas = array();
						foreach ($lista_examenes as $examen_aux) {
							if (!isset($mapa_examenes[$examen_aux["id_examen"]])) {
								$mapa_examenes[$examen_aux["id_examen"]] = array();
							}
							array_push($mapa_examenes[$examen_aux["id_examen"]], $examen_aux);
							
							if (isset($mapa_fechas[$examen_aux["id_examen"]][$examen_aux["fecha_hc_t"]])) {
								$valor_aux = $mapa_fechas[$examen_aux["id_examen"]][$examen_aux["fecha_hc_t"]];
								$valor_aux++;
								$mapa_fechas[$examen_aux["id_examen"]][$examen_aux["fecha_hc_t"]] = $valor_aux;
							} else {
								$mapa_fechas[$examen_aux["id_examen"]][$examen_aux["fecha_hc_t"]] = 1;
							}
						}
						
						//Se dibuja el componente de listas de exámenes
						$cont_aux = 0;
						foreach ($mapa_examenes as $id_examen => $lista_examenes_aux) {
					?>
                    <div class="div_titulo_imagen_hc" onclick="mostrar_detalle_imagen_hc(<?php echo($id_examen); ?>);">
                    	<input type="hidden" id="hdd_titulo_imagen_hc_<?php echo($id_examen); ?>" value="<?php echo($cont_aux == 0 ? 1 : 0); ?>" />
                        <div class="div_titulo_imagen_hc_interno"><b><?php echo($lista_examenes_aux[0]["nombre_examen"]); ?></b></div>
                        <?php
                        	if ($cont_aux == 0) {
						?>
                        <img id="img_titulo_imagen_hc_down_<?php echo($id_examen); ?>" src="../imagenes/icon-blue-down.png" style="display:none;" />
                        <img id="img_titulo_imagen_hc_up_<?php echo($id_examen); ?>" src="../imagenes/icon-blue-up.png" style="display:inline-block;" />
                        <?php
							} else {
						?>
                        <img id="img_titulo_imagen_hc_down_<?php echo($id_examen); ?>" src="../imagenes/icon-blue-down.png" style="display:inline-block;" />
                        <img id="img_titulo_imagen_hc_up_<?php echo($id_examen); ?>" src="../imagenes/icon-blue-up.png" style="display:none;" />
                        <?php
							}
						?>
                    </div>
                    <?php
                    	$display_aux = "none";
						if ($cont_aux == 0) {
							$display_aux = "block";
						}
					?>
                    <div id="d_lista_imagen_hc_<?php echo($id_examen); ?>" class="div_lista_imagen_hc" style="display:<?php echo($display_aux); ?>;">
                    	<ul>
                        	<?php
								$id_examen_hc_ant = "";
								$cont_aux = 1;
                            	foreach ($lista_examenes_aux as $examen_aux) {
									if ($examen_aux["id_examen_hc"] != $id_examen_hc_ant) {
										$cont_aux = 1;
									}
									
									$clase_aux = "li_base";
									if ($cont_aux == $mapa_fechas[$id_examen][$examen_aux["fecha_hc_t"]]) {
										$clase_aux = "li_fin";
									}
									$texto_aux = $funciones_persona->obtenerFecha6($examen_aux["fecha_hc_t"]);
									if ($mapa_fechas[$id_examen][$examen_aux["fecha_hc_t"]] > 1) {
										$texto_aux .= " (".$cont_aux.")";
									}
							?>
                            <li class="<?php echo($clase_aux); ?>" onclick="mostrar_archivo_imagen_hc('<?php echo($examen_aux["id_examen_hc_det"]); ?>');">
                            	<?php
									echo($texto_aux);
								?>
                            </li>
                            <?php
									$cont_aux++;
									$id_examen_hc_ant = $examen_aux["id_examen_hc"];
								}
							?>
                        </ul>
                    </div>
                    <?php
							$cont_aux++;
						}
					} else {
				?>
                <b>No se hallaron im&aacute;genes de ex&aacute;menes para el paciente.</b>
                <?php
					}
				?>
            </div>
            <div id="d_ver_imagenes_hc"></div>
            <script type="text/javascript">
				cargar_archivos_imagen_hc(<?php echo($id_paciente); ?>, 100);
			</script>
            <?php
				} else {
			?>
            <h6>No tiene permisos para abrir esta ventana.</h6>
            <?php
				}
			?>
        </div>
    </div>
</body>
</html>
