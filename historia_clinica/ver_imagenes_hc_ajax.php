<?php session_start();
	/**
	 * Autor: Feisar Moreno - 27/03/2014
	 */
	header('Content-Type: text/xml; charset=UTF-8');
	
	require_once("../db/DbExamenesOptometria.php");
	require_once("../db/DbVariables.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/Utilidades.php");
	
	$dbExamenesOptometria = new DbExamenesOptometria();
	$dbVariables = new Dbvariables();
	
	$contenido = new ContenidoHtml();
	$contenido->validar_seguridad(1);
	$funciones_persona = new FuncionesPersona();
	$utilidades = new Utilidades();
	
	$opcion = $utilidades->str_decode($_POST["opcion"]);
	
	switch ($opcion) {
		case "1": //Cargar los archivos
			$id_usuario = $_SESSION["idUsuario"];
			@$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
			
			//Se buscan las imágenes existentes
			$lista_examenes = $dbExamenesOptometria->get_lista_examenes_optometria_hc_det_paciente($id_paciente);
			if (count($lista_examenes) > 0) {
				//Se obtiene la ruta actual de las imágenes
				$arr_ruta_base = $dbVariables->getVariable(17);
				$ruta_base = $arr_ruta_base["valor_variable"];
				
				$ruta_tmp = "../historia_clinica/tmp/".$id_usuario;
				
				//Se arma el mapa de fechas
				$mapa_fechas = array();
				foreach ($lista_examenes as $examen_aux) {
					if (isset($mapa_fechas[$examen_aux["id_examen"]][$examen_aux["fecha_hc_t"]])) {
						$valor_aux = $mapa_fechas[$examen_aux["id_examen"]][$examen_aux["fecha_hc_t"]];
						$valor_aux++;
						$mapa_fechas[$examen_aux["id_examen"]][$examen_aux["fecha_hc_t"]] = $valor_aux;
					} else {
						$mapa_fechas[$examen_aux["id_examen"]][$examen_aux["fecha_hc_t"]] = 1;
					}
				}
				
				$id_examen_hc_ant = "";
				$cont_aux = 1;
			?>
            <input type="hidden" id="hdd_cant_archivos_imagen_hc" value="<?php echo(count($lista_examenes)); ?>" />
            <?php
				foreach ($lista_examenes as $i => $examen_aux) {
					$id_examen_hc_det = $examen_aux["id_examen_hc_det"];
					if ($examen_aux["id_examen_hc"] != $id_examen_hc_ant) {
						$cont_aux = 1;
					}
					
                    $display_aux = "none";
					if ($i == 0) {
						$display_aux = "block";
					}
			?>
            <div id="d_archivo_imagen_hc_<?php echo($id_examen_hc_det); ?>" class="div_marco no-border" style="display:<?php echo($display_aux); ?>;">
            	<input type="hidden" id="hdd_id_examen_hc_det_<?php echo($i); ?>" value="<?php echo($id_examen_hc_det); ?>" />
                <table border="0" cellpadding="0" cellspacing="0" width="99%" align="center">
                	<tr>
                    	<td align="right" style="width:11%;">
                        	<?php
                            	if ($i > 0) {
							?>
                        	<img src="../imagenes/btn-mes-ant.png" class="img_button no-margin" title="Imagen anterior" onclick="mostrar_archivo_imagen_hc_indice(<?php echo($i - 1); ?>);" />
                            <?php
								}
							?>
                        </td>
                    	<td align="center" style="width:78%;">
                            <h6 style="background-color:#ddd;"><b>
                                <?php
                                    $texto_aux = $examen_aux["nombre_examen"]." - ".$funciones_persona->obtenerFecha6($examen_aux["fecha_hc_t"]);
                                    if ($mapa_fechas[$examen_aux["id_examen"]][$examen_aux["fecha_hc_t"]] > 1) {
                                        $texto_aux .= " (".$cont_aux.")";
                                    }
                                    echo($texto_aux);
                                ?>
                            </b></h6>
                		</td>
                        <td align="left" style="width:3%;">
                        	<?php
                            	if ($i < count($lista_examenes) - 1) {
							?>
                        	<img src="../imagenes/btn-mes-sig.png" class="img_button no-margin" title="Siguiente imagen" onclick="mostrar_archivo_imagen_hc_indice(<?php echo($i + 1); ?>);" />
                            <?php
								}
							?>
                        </td>
                        <td align="right" style="width:8%;">
                            <div id="d_label_zoom_<?php echo($id_examen_hc_det); ?>" style="margin:10px; float:right;"></div>
                        </td>
                    </tr>
                </table>
                <div class="div_contenedor_archivo" style="margin:auto; height:calc(100vh - 135px);">
					<?php
						$ruta_arch_examen = trim($examen_aux["ruta_arch_examen"]);
						$ruta_arch_examen = str_replace("../imagenes/imagenes_hce", $ruta_base, $ruta_arch_examen);
						
						//Se obtiene el tipo de archivo
						$extension = $utilidades->get_extension_arch($ruta_arch_examen);
						
						@copy($ruta_arch_examen, $ruta_tmp."/img_examen_".$id_examen_hc_det.".".$extension);
						$ruta_arch_examen = $ruta_tmp."/img_examen_".$id_examen_hc_det.".".$extension;
						
						$ancho_max = 850;
						
						switch ($extension) {
							case "jpg":
							case "png":
							case "bmp":
							case "gif":
								//Se obtienen las dimensiones del archivo
								@$arr_prop_imagen = getimagesize($ruta_arch_examen);
								$ancho_aux = $arr_prop_imagen[0];
								$alto_aux = $arr_prop_imagen[1];
					?>
                    <input type="hidden" id="hdd_zoom_act_<?php echo($id_examen_hc_det); ?>" value="0" />
                    <input type="hidden" id="hdd_ancho_act_<?php echo($id_examen_hc_det); ?>" value="<?php echo($ancho_aux); ?>" />
                    <input type="hidden" id="hdd_alto_act_<?php echo($id_examen_hc_det); ?>" value="<?php echo($alto_aux); ?>" />
                    <div class="div_img_pantalla_completa" style="width:99%; height:calc(100vh - 250px);">
                        <img id="img_drag_<?php echo($id_examen_hc_det); ?>" src="<?php echo($ruta_arch_examen); ?>" class="ui-widget-content" style="cursor:all-scroll;" />
                    </div>
                	<script id="ajax" type="text/javascript">
						$("#d_label_zoom_" + <?php echo($id_examen_hc_det); ?>).html("<b>Zoom: 100%</b>");
						
						$(function() {
							$("#img_drag_<?php echo($id_examen_hc_det); ?>").draggable();
						});
						
						$("#img_drag_<?php echo($id_examen_hc_det); ?>").bind("mousewheel",
							function(e) {
								var direccion = "";
								if (e.originalEvent.wheelDelta > 0) {
									//scroll down
									direccion = "D";
								} else {
									//scroll up
									direccion = "U";
								}
								
								manejar_zoom_imagen(direccion, <?php echo($id_examen_hc_det); ?>);
								
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
                        <embed src="<?php echo($ruta_arch_examen); ?>" width="100%" height="100%"></embed>
                    </div>
					<?php
								break;
								
							default:
								echo("Formato de archivo no soportado (".$extension.").");
								break;
						}
					?>                	
                    <div class="div_cont_observaciones_hc">
                        <?php echo($examen_aux["observaciones_examen"]); ?>
                    </div>
                </div>
            </div>
            <?php
					$cont_aux++;
					$id_examen_hc_ant = $examen_aux["id_examen_hc"];
				}
			}
			break;
	}
?>
