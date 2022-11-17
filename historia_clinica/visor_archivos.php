<?php session_start();
	/*
	 * Pagina TEMPORAL para la visualización de archivos asociados a la historia clínica
	 * Autor: ZJJC - 07/2017
	 */
	require_once("../db/DbVariables.php");
	require_once("../db/DbPermisos.php");
	require_once("../db/DbHistoriaClinica.php");
	require_once("../db/DbPacientes.php");
	require_once("../db/DbExamenesOptometria.php");
	require_once("../db/DbArchivos.php");
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../principal/ContenidoHtml.php");
	
	$dbVariables = new Dbvariables();
	$dbPermisos = new DbPermisos();
	$DbHistoriaClinica = new DbHistoriaClinica();
	$dbPacientes = new DbPacientes();
	
	$dbExamenesOptometria = new DbExamenesOptometria();	
	$dbLoteArchivos = new DBRegistroArchivos(); 
	$dbArchivos = new DBArchivo(); 	
	
	$utilidades = new Utilidades();
	$funciones_persona = new FuncionesPersona();
	$contenidoHtml = new ContenidoHtml();
	
	//variables
	$titulo = $dbVariables->getVariable(1);
	
	//Cambiar las variables get a post
	$utilidades->get_a_post();
	
	$id_usuario = $_SESSION["idUsuario"];
	$id_menu = 65;	
	$id_registro_archivo = $_POST["hdd_id_reg_archivos"]; 	
	$modulo=$_POST["hdd_modulo"]; 
	$id_modulo=$_POST["hdd_id_modulo"]; 
	
	$id_hc=$id_modulo; 
	$resultado=$DbHistoriaClinica->getHistoriaClinicaId($id_hc);	
	$id_paciente=$resultado["id_paciente"];
	
	$reg_lote_archivos=$dbLoteArchivos->getRegistroArchivos($id_registro_archivo); 
	$tipo_registro_archivo=$reg_lote_archivos["id_tipo_archivo"]; 
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
    <script type="text/javascript" src="visor_archivos.js"></script>
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
					$id_paciente = $utilidades->str_decode($id_paciente);
					$paciente_obj = $dbPacientes->getEdadPaciente($id_paciente, "");
			?>
            <table border="0" cellpadding="2" cellspacing="0" align="center" style="width:100%;">
            	<tr>
					<td style="width:35%">
						<table class="modal_table">
							<th><b>Foto Pterigio</b></th>
						</table>
					</td>
					<td style="width:5%"></td>
                	<td align="left" valign="top" style="width:30%">
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
                	<td align="left" valign="top" style="width:30%">
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
                	//Se obtiene el listado de archivos
					$lista_archivos = $dbArchivos->getArchivosHc($id_hc); 
					if (count($lista_archivos) == 0) {
						?>
						<b>No hay archivos registrados para el paciente.</b>
						<?php
					}
					else {					
				?>			
					<br>				
<!--                <div id="d_btn_impr_img_hc_1" style="display:block; height:40px;">
                    <input type="button" value="Imprimir im&aacute;genes" class="btnPrincipal peq" onclick="imprimir_imagenes_hc(<?php echo($id_paciente); ?>);" />
                </div>-->
                <div id="d_btn_impr_img_hc_2" style="display:none; height:40px;">
                    <img src="../imagenes/ajax-loader.gif" />
                </div>
                <div id="d_impresion_img_hc" style="display:none;"></div>
                <?php
						//Se crea el mapa de archivos por TipoArchivo/Fecha
						$mapa_archivos = array();
						$mapa_fechas = array();
						foreach ($lista_archivos as $archivo) {							
							$subindice_nodo="nodo".$archivo["id_tipo_archivo"];
							if (!isset($mapa_archivos[$subindice_nodo])) {
								$mapa_archivos[$subindice_nodo] = array();
							}
							array_push($mapa_archivos[$subindice_nodo], $archivo);
							
							if (isset($mapa_fechas[$subindice_nodo][$archivo["fecha"]])) {
								$valor_aux = $mapa_fechas[$subindice_nodo][$archivo["fecha"]];
								$valor_aux++;
								$mapa_fechas[$subindice_nodo][$archivo["fecha"]] = $valor_aux; 
							} else { 
								$mapa_fechas[$subindice_nodo][$archivo["fecha"]] = 1; 
							} 
							//echo "<br>nodo ".$subindice_nodo." archivo=".$archivo["nombre_original"]." (".$archivo["fecha"]."-".$mapa_fechas[$subindice_nodo][$archivo["fecha"]]."-)"; 
						}
						
						//Se dibuja el componente de listas de archivos
						$cont_aux = 0;					
						foreach ($mapa_archivos as $subindice_nodo => $lista_archivos_aux) {	//clave/valor						
							//echo "<br>mapa($subindice_nodo): ";//" array=<br>".var_dump($lista_archivos_aux[$subindice_nodo]); 
							//echo var_dump($lista_archivos_aux);
							// Información del lote de archivos: 
							$reg_lote_archivos=$dbLoteArchivos->getRegistroArchivos($lista_archivos_aux[0]["id_reg_archivos"]); 
							$nombre_nodo=$reg_lote_archivos["nombre"]; 
					?>
                    <div class="div_titulo_imagen_hc" onclick="mostrar_detalle_nodo('<?php echo($subindice_nodo); ?>');">
                    	<input type="hidden" id="hdd_titulo_imagen_hc_<?php echo($subindice_nodo); ?>" value="<?php echo($cont_aux == 0 ? 1 : 0); ?>" />
                        <div class="div_titulo_imagen_hc_interno"><b><?php echo($nombre_nodo); ?></b></div>
                        <?php                        								
							if ($reg_lote_archivos["id_tipo_archivo"]<>$tipo_registro_archivo) {								
						?>
                        <img id="img_titulo_imagen_hc_down_<?php echo($subindice_nodo); ?>" src="../imagenes/icon-blue-down.png" style="display:none;" />
                        <img id="img_titulo_imagen_hc_up_<?php echo($subindice_nodo); ?>" src="../imagenes/icon-blue-up.png" style="display:inline-block;" />
                        <?php
								$display_aux = "none"; 
							} else {								
						?>
                        <img id="img_titulo_imagen_hc_down_<?php echo($subindice_nodo); ?>" src="../imagenes/icon-blue-down.png" style="display:inline-block;" />
                        <img id="img_titulo_imagen_hc_up_<?php echo($subindice_nodo); ?>" src="../imagenes/icon-blue-up.png" style="display:none;" />
                        <?php
								$display_aux = "block";
							}
						?>
                    </div>
                    <div id="d_lista_imagen_hc_<?php echo($subindice_nodo); ?>" class="div_lista_imagen_hc" style="display:<?php echo($display_aux); ?>;">
                    	<ul>
                        	<?php
								$subindice_nodo_ant = "";
								$cont_aux = 1;
                            	foreach ($lista_archivos_aux as $archivo_aux) {
									//echo "<br>id_archi=".$archivo_aux["id_archivo"]." -".$archivo_aux["fecha"];
									if ($archivo_aux["fecha"] != $subindice_nodo_ant) {
										$cont_aux = 1;
									}
									
									$clase_aux = "li_base";
									if ($cont_aux == $mapa_fechas[$subindice_nodo][$archivo_aux["fecha"]]) {
										$clase_aux = "li_fin";
									}
									$texto_aux = $funciones_persona->obtenerFecha6($archivo_aux["fecha"]);
									if ($mapa_fechas[$subindice_nodo][$archivo_aux["fecha"]] > 1) {
										$texto_aux .= " (".$cont_aux.")";
									}
							?>
                            <li class="<?php echo($clase_aux); ?>" onclick="mostrar_archivo('<?php echo($archivo_aux["id_archivo"]); ?>');">
                            	<?php
									echo($texto_aux);
								?>
                            </li>
                            <?php
									$cont_aux++;
									$subindice_nodo_ant = $archivo_aux["fecha"];
								}
							?>
                        </ul>
                    </div>
                    <?php
							$cont_aux++;
						}
					}					
				?>
            </div>
            <div id="d_ver_imagenes_hc"></div>
            <script type="text/javascript">			
				cargar_archivos("HC", <?php echo $id_hc.", ".$id_registro_archivo; ?>, 100);
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
