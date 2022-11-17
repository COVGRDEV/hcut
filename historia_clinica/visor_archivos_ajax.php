<?php session_start();
	/**
	 * Autor: Feisar Moreno - 27/03/2014
	 */
	header('Content-Type: text/xml; charset=UTF-8');
	
	require_once("../db/DbArchivos.php");
	require_once("../db/DbVariables.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/Utilidades.php");
	
	$dbVariables = new Dbvariables();
	$dbLoteArchivos = new DBRegistroArchivos(); 
	$dbArchivos = new DBArchivo(); 		
	
	$contenido = new ContenidoHtml();
	$contenido->validar_seguridad(1);
	$funciones_persona = new FuncionesPersona();
	$utilidades = new Utilidades();
	
	$opcion = $utilidades->str_decode($_POST["opcion"]);
	$modulo=$_POST["modulo"]; 
	$id_modulo=$_POST["id_modulo"]; 
	$id_activo=$_POST["id_activo"];	
	//echo "<br>$opcion - $modulo - $id_modulo";
	
	switch ($opcion) {
		case "1": //Cargar los archivos
			$id_usuario = $_SESSION["idUsuario"];
			$id_hc=$id_modulo; 					
			
			//Se buscan las imágenes existentes			
			$lista_archivos = $dbArchivos->getArchivosHc($id_hc); 
			if (count($lista_archivos) > 0) {
				//Se obtiene la ruta actual de las imágenes
				$arr_ruta_base = $dbVariables->getVariable(17);
				$ruta_base = $arr_ruta_base["valor_variable"];
				
				$ruta_tmp = "../historia_clinica/tmp/".$id_usuario;
				
				//Se arma el mapa de fechas
				$mapa_fechas = array();
				foreach ($lista_archivos as $archivo_aux) {
					$subindice_nodo="n".$archivo_aux["id_tipo_archivo"];
					
					if (!isset($mapa_fechas[$subindice_nodo]["nombre_nodo"])) { 
						$reg_lote_archivos=$dbLoteArchivos->getRegistroArchivos($archivo_aux["id_reg_archivos"]); 
						$mapa_fechas[$subindice_nodo]["nombre_nodo"]=$reg_lote_archivos["nombre"]; 						
					}
					
					if (isset($mapa_fechas[$subindice_nodo][$archivo_aux["fecha"]])) {
						$valor_aux = $mapa_fechas[$subindice_nodo][$archivo_aux["fecha"]];
						$valor_aux++;
						$mapa_fechas[$subindice_nodo][$archivo_aux["fecha"]] = $valor_aux;
					} else {
						$mapa_fechas[$subindice_nodo][$archivo_aux["fecha"]] = 1;
					}
					//echo "<br>nodo $subindice_nodo: ".$archivo_aux['fecha']." etiq_nodo: ".$mapa_fechas[$subindice_nodo]["nombre_nodo"];
				}
				
				$subindice_nodo_ant = "";
				$cont_aux = 1;
				$indice_activo = false; 
			?>
            <input type="hidden" id="hdd_cant_archivos_imagen_hc" value="<?php echo(count($lista_archivos)); ?>" />
            <?php
				foreach ($lista_archivos as $i => $archivo_aux) {
					$id_archivo = $archivo_aux["id_archivo"];
					$subindice_nodo="n".$archivo_aux["id_tipo_archivo"];
					
					if ($archivo_aux["fecha"] != $subindice_nodo_ant) {
						$cont_aux = 1;
					}
					
					// Visualizar, por defecto, el primer archivo del nodo activo 
                    $display_aux = "none"; 
					if ($indice_activo==false && $archivo_aux["id_reg_archivos"] == $id_activo) { 
						$display_aux = "block"; 
						$indice_activo = true; 
					} 
					//echo "<br>".$id_archivo." - ".$id_activo." - i=".$indice_activo; 
			?>
            <div id="d_archivo_imagen_hc_<?php echo($id_archivo); ?>" class="div_marco no-border" style="display:<?php echo($display_aux); ?>;">
            	<input type="hidden" id="hdd_id_examen_hc_det_<?php echo($i); ?>" value="<?php echo($id_archivo); ?>" />
                <table border="0" cellpadding="0" cellspacing="0" width="99%" align="center">
                	<tr>
                    	<td align="right" style="width:11%;">
                        	<?php
                            	if ($i > 0) {
							?>
                        	<img src="../imagenes/btn-mes-ant.png" class="img_button no-margin" title="Imagen anterior" onclick="mostrar_archivo_indice(<?php echo($i - 1); ?>);" />
                            <?php
								}
							?>
                        </td>
                    	<td align="center" style="width:78%;">
                            <h6 style="background-color:#ddd;"><b>
                                <?php
                                    $texto_aux = $mapa_fechas[$subindice_nodo]["nombre_nodo"]."&nbsp;&nbsp; - &nbsp;&nbsp;".$funciones_persona->obtenerFecha6($archivo_aux["fecha"]);									
                                    if ($mapa_fechas[$subindice_nodo][$archivo_aux["fecha"]] > 1) {
                                        $texto_aux .= " (".$cont_aux." de ".$mapa_fechas[$subindice_nodo][$archivo_aux["fecha"]].")";
                                    }
									//$texto_aux .= "&nbsp;&nbsp; - &nbsp;&nbsp;".basename($archivo_aux["ruta"]); //."  (".$archivo_aux["nombre_original"].")"; 
                                    echo($texto_aux);
                                ?>
                            </b></h6>							
                		</td>
                        <td align="left" style="width:3%;">
                        	<?php
                            	if ($i < count($lista_archivos) - 1) {
							?>
                        	<img src="../imagenes/btn-mes-sig.png" class="img_button no-margin" title="Siguiente imagen" onclick="mostrar_archivo_indice(<?php echo($i + 1); ?>);" />
                            <?php
								}
							?>
                        </td>
                        <td align="right" style="width:8%;">
                            <div id="d_label_zoom_<?php echo($id_archivo); ?>" style="margin:10px; float:right;"></div>
                        </td>
                    </tr>
                </table>
                <div class="div_contenedor_archivo" style="margin:auto; height:calc(100vh - 135px);">
					<?php
						$ruta_archivo = trim($archivo_aux["ruta"]);
						$ruta_archivo = str_replace("../imagenes/imagenes_hce", $ruta_base, $ruta_archivo);
						
						//Se obtiene el tipo de archivo
						$extension = $utilidades->get_extension_arch($ruta_archivo);
						
						@copy($ruta_archivo, $ruta_tmp."/archivo_".$id_archivo.".".$extension);
						$ruta_archivo = $ruta_tmp."/archivo_".$id_archivo.".".$extension;
						
						$ancho_max = 850;
						
						switch ($extension) {
							case "jpg":
							case "png":
							case "bmp":
							case "gif":
								//Se obtienen las dimensiones del archivo
								@$arr_prop_imagen = getimagesize($ruta_archivo);
								$ancho_aux = $arr_prop_imagen[0];
								$alto_aux = $arr_prop_imagen[1];
					?>
                    <input type="hidden" id="hdd_zoom_act_<?php echo($id_archivo); ?>" value="0" />
                    <input type="hidden" id="hdd_ancho_act_<?php echo($id_archivo); ?>" value="<?php echo($ancho_aux); ?>" />
                    <input type="hidden" id="hdd_alto_act_<?php echo($id_archivo); ?>" value="<?php echo($alto_aux); ?>" />
                    <div class="div_img_pantalla_completa" style="width:99%; height:calc(100vh - 250px);">
                        <img id="img_drag_<?php echo($id_archivo); ?>" src="<?php echo($ruta_archivo); ?>" class="ui-widget-content" style="cursor:all-scroll;" />
                    </div>
                	<script id="ajax" type="text/javascript">
						$("#d_label_zoom_" + <?php echo($id_archivo); ?>).html("<b>Zoom: 100%</b>");
						
						$(function() {
							$("#img_drag_<?php echo($id_archivo); ?>").draggable();
						});
						
						$("#img_drag_<?php echo($id_archivo); ?>").bind("mousewheel",
							function(e) {
								var direccion = "";
								if (e.originalEvent.wheelDelta > 0) {
									//scroll down
									direccion = "D";
								} else {
									//scroll up
									direccion = "U";
								}
								
								manejar_zoom_archivo(direccion, <?php echo($id_archivo); ?>);
								
								return false;
							}
						);
					</script>
                	<?php
								//Se ajustan las dimensiones al espacio disponible
								if ($ancho_aux > $ancho_max) {
									$ancho_aux = $ancho_max;
								}
								break;
								
							case "pdf":
					?>
                    <div style="margin:auto; width:99%; height:calc(100vh - 250px); border:1px solid #333; overflow:auto;">
                        <embed src="<?php echo($ruta_archivo); ?>" width="100%" height="100%"></embed>
                    </div>
					<?php
								break;
								
							default:
								echo("Formato de archivo no soportado (".$extension.").");
								break;
						}
					?>                	
                    <div class="div_cont_observaciones_hc">
                        <?php 
							//echo "<b><i>".basename($archivo_aux["ruta"])."  (".$archivo_aux["nombre_original"].").</i></b><br><br>"; 
							echo "Sin observaciones en el registro"/*$archivo_aux["observaciones_examen"]*/;
						?>
                    </div>
                </div>
				<!--<script type="text/javascript">
					//cargar_archivos("HC", <?php echo($id_hc); ?>, 100);
					//mostrar_archivo_indice(<?php echo $indice_activo; ?>);
				</script>	-->				
            </div>
            <?php
					$cont_aux++;
					$subindice_nodo_ant = $archivo_aux["fecha"];
				}
			}
			break;
	}
?>
