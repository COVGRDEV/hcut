<?php session_start();
	/**
	 * Autor: Feisar Moreno - 27/03/2014
	 */
	header("Content-Type: text/xml; charset=UTF-8");
	
	require_once("../db/DbExamenesOptometria.php");
	require_once("../db/DbMaestroExamenes.php");
	require_once("../db/DbVariables.php");
	require_once("../funciones/Utilidades.php");
	
	function get_ruta_archivo($id_hc) {
		$dbVariables = new Dbvariables();
		
		//Se obtiene la fecha actual
		$arr_fecha_act = $dbVariables->getAnoMesDia();
		
		//Se obtiene la ruta actual de las imágenes
		$arr_ruta_base = $dbVariables->getVariable(17);
		$ruta_base = $arr_ruta_base["valor_variable"];
		
		//Se obtienen los datos de la historia clinica
		$dbExamenesOptometria = new DbExamenesOptometria();
		$hc_obj = $dbExamenesOptometria->getHistoriaClinicaId($id_hc);
		
		$ruta = $ruta_base."/".$arr_fecha_act["anio_actual"]."/".$arr_fecha_act["mes_actual"]."/".
				 $arr_fecha_act["dia_actual"]."/".$hc_obj["id_paciente"]."/";
		
		return $ruta;
	}
	
	$dbExamenesOptometria = new DbExamenesOptometria();
	$dbMaestroExamenes = new DbMaestroExamenes();
	$dbVariables = new Dbvariables();
	$utilidades = new Utilidades();
	
	$opcion = $utilidades->str_decode($_POST["opcion"]);
	
	switch ($opcion) {
		case "1": //Subir el archivo al servidor
			$id_usuario = $_SESSION["idUsuario"];
			@$indice = $utilidades->str_decode($_POST["hdd_indice_examen"]);
			@$id_hc = $utilidades->str_decode($_POST["hdd_id_hc_consulta_".$indice]);
			@$id_examen_hc = $utilidades->str_decode($_POST["hdd_id_examen_hc_".$indice]);
			@$id_examen = $utilidades->str_decode($_POST["hdd_id_examen_sel_".$indice]);
			@$id_ojo = $utilidades->str_decode($_POST["hdd_id_ojo_sel_".$indice]);
			@$cant_archivos = intval($_POST["hdd_cant_archivos_".$indice], 10);
			@$ind_actualizar = intval($_POST["hdd_ind_actualizar_".$indice], 10);
		?>
        <html><body>
        <?php
			$ruta_arch_examen = get_ruta_archivo($id_hc);
			
			//Si el identificador del examen hc es vacío, se busca
			if ($id_examen_hc == "") {
				$obj_aux = $dbExamenesOptometria->get_examen_optometria_hc($id_hc, $id_examen, $id_ojo);
				if (isset($obj_aux["id_examen_hc"])) {
					$id_examen_hc = $obj_aux["id_examen_hc"];
				}
			}
			
			if ($id_examen != "") {
				//Se crea el directorio del archivo
				@mkdir($ruta_arch_examen, 0755, true);
				
				//Se busca el siguiente contador de archivos para el examen
				$cont_aux_obj = $dbExamenesOptometria->get_siguiente_cont_arch_det($id_examen_hc);
				$cont_aux = $cont_aux_obj["cont_arch"];
				
				$arr_formatos = array("jpg", "png", "bmp", "gif", /*"tif", "tiff", */"mp3", "mp4", "avi", "wmv", "pdf");
				for ($i = 0; $i < $cant_archivos; $i++) {
					foreach ($_FILES["fil_arch_examen_".$indice."_".$i]["name"] as $f => $nombre_ori) {
						//Se verifica si es un formato válido
						$extension_aux = $utilidades->get_extension_arch($nombre_ori);
						if (in_array($extension_aux, $arr_formatos)) {
							@$nombre_tmp = $_FILES["fil_arch_examen_".$indice."_".$i]["tmp_name"][$f];
							
							//Se genera el nombre del archivo
							$nombre_aux = $ruta_arch_examen.$id_hc."_examen_op_".$indice."_".$cont_aux.".".$extension_aux;
							
							//Se copia el archivo
							copy($nombre_tmp, $nombre_aux);
							
							//Se agrega el registro de archivo a la base de datos
							$dbExamenesOptometria->crear_examen_optometria_hc_det($id_examen_hc, $nombre_aux, $cont_aux, $id_usuario);
							
							$cont_aux++;
						}
					}
				}
			}
			echo($ruta_arch_examen);
		?>
        </body></html>
    	<?php
			break;
			
		case "2": //Mostrar los archivos correspondientes al examen
			@$id_hc = $utilidades->str_decode($_POST["id_hc"]);
			@$id_examen_hc = $utilidades->str_decode($_POST["id_examen_hc"]);
			@$indice = $utilidades->str_decode($_POST["indice"]);
			
			//Se obtiene la ruta actual de las imágenes
			$arr_ruta_base = $dbVariables->getVariable(17);
			$ruta_base = $arr_ruta_base["valor_variable"];
			
			$ruta_tmp = "../historia_clinica/tmp/".$_SESSION["idUsuario"];
			
			//Se buscan las rutas de los archivos
			$lista_examenes_optometria_hc_det = $dbExamenesOptometria->get_lista_examenes_optometria_hc_det2($id_examen_hc);
			
			if (count($lista_examenes_optometria_hc_det) > 0) {
				$arr_id_archivos = array();
				$cont_aux = 0;
			?>
            <input type="hidden" id="hdd_cant_archivos_examen_<?php echo($indice); ?>" value="<?php echo(count($lista_examenes_optometria_hc_det)); ?>" />
            <?php
				foreach ($lista_examenes_optometria_hc_det as $j => $registro_aux) {
					$ruta_arch_examen = trim($registro_aux["ruta_arch_examen"]);
					$ruta_arch_examen = str_replace("../imagenes/imagenes_hce", $ruta_base, $ruta_arch_examen);
					
					//Se agrega el id al array que de archivos a mostrar
					array_push($arr_id_archivos, $cont_aux);
					
					//Se obtiene el tipo de archivo
					$extension = $utilidades->get_extension_arch($ruta_arch_examen);
					
					copy($ruta_arch_examen, $ruta_tmp."/img_examen_".$id_hc."_".$id_examen_hc."_".$indice."_".$j.".".$extension);
					$ruta_arch_examen = $ruta_tmp."/img_examen_".$id_hc."_".$id_examen_hc."_".$indice."_".$j.".".$extension;
					
					$ancho_max = 850;
					$display_aux = "none";
					if ($cont_aux == 0) {
						$display_aux = "block";
					}
			?>
            <input type="hidden" id="hdd_id_examen_hc_det_<?php echo($indice."_".$cont_aux); ?>" value="<?php echo($registro_aux["id_examen_hc_det"]); ?>" />
			<?php
					switch ($extension) {
						case "jpg":
						case "png":
						case "bmp":
						case "gif":
							//Se obtienen las dimensiones del archivo
							$arr_prop_imagen = getimagesize($ruta_arch_examen);
							$ancho_aux = $arr_prop_imagen[0];
							$alto_aux = $arr_prop_imagen[1];
				?>
                <div id="d_archivo_completo_<?php echo($indice."_".$cont_aux); ?>" class="div_pantalla_completa" style="display:none;">
                	<input type="hidden" id="hdd_zoom_act_<?php echo($indice."_".$cont_aux); ?>" value="0" />
                	<input type="hidden" id="hdd_ancho_act_<?php echo($indice."_".$cont_aux); ?>" value="<?php echo($ancho_aux); ?>" />
                	<input type="hidden" id="hdd_alto_act_<?php echo($indice."_".$cont_aux); ?>" value="<?php echo($alto_aux); ?>" />
                    <div style="width:100%; height:35px;">
                    	<img src="../imagenes/icon-zoom-out.png" title="Vista normal" class="img_button" style="margin:5px; width:24px; float:right;" onClick="ocultar_archivo_pantalla_completa(<?php echo($indice); ?>);" />
                        <div id="d_label_zoom_<?php echo($indice."_".$cont_aux); ?>" style="margin:10px; float:right;"><b>Zoom: 100%</b></div>
                    </div>
                	<div class="div_img_pantalla_completa" style="width:99%; height:calc(99vh - 35px);">
                    	<img id="img_drag_<?php echo($indice."_".$cont_aux); ?>" src="<?php echo($ruta_arch_examen); ?>" class="ui-widget-content" style="cursor:all-scroll;" onDblClick="ocultar_archivo_pantalla_completa(<?php echo($indice); ?>);" />
                    </div>
                    <script id="ajax" type="text/javascript">
						$(function() {
							$("#img_drag_<?php echo($indice."_".$cont_aux); ?>").draggable();
						});
						
						$("#img_drag_<?php echo($indice."_".$cont_aux); ?>").bind("mousewheel",
							function(e) {
								var direccion = "";
								if (e.originalEvent.wheelDelta > 0) {
									//scroll down
									direccion = "D";
								} else {
									//scroll up
									direccion = "U";
								}
								
								manejar_zoom_imagen(direccion, <?php echo($indice); ?>, <?php echo($cont_aux); ?>);
								
								return false;
							}
						);
					</script>
                </div>
                <?php
							//Se ajustan las dimensiones al espacio disponible
							if ($ancho_aux > $ancho_max) {
								$ancho_aux = $ancho_max;
							}
				?>
                <div id="d_archivo_muestra_<?php echo($indice."_".$cont_aux); ?>" class="div_contenedor_archivo" style="height:420px; display:<?php echo($display_aux); ?>">
					<img src="<?php echo($ruta_arch_examen); ?>" style="width:<?php echo($ancho_aux); ?>px;" onDblClick="mostrar_archivo_pantalla_completa(<?php echo($indice); ?>);" />
                </div>
				<?php
							break;
							
						case "mp4":
						case "avi":
						case "wmv":
				?>
                <div id="d_archivo_completo_<?php echo($indice."_".$cont_aux); ?>" class="div_pantalla_completa" style="display:none;">
                	<a href="<?php echo($ruta_arch_examen); ?>" target="_blank">Para abrir el archivo de video haga click aqu&iacute;.</a>
                </div>
                <div id="d_archivo_muestra_<?php echo($indice."_".$cont_aux); ?>" class="div_contenedor_archivo" style="height:420px; display:<?php echo($display_aux); ?>">
                    <a href="<?php echo($ruta_arch_examen); ?>" target="_blank">Para abrir el archivo de video haga click aqu&iacute;.</a>
                </div>
				<?php
							break;
							
						case "mp3":
				?>
                <div id="d_archivo_completo_<?php echo($indice."_".$cont_aux); ?>" class="div_pantalla_completa" style="display:none;">
                	<a href="<?php echo($ruta_arch_examen); ?>" target="_blank">Para abrir el archivo de audio haga click aqu&iacute;.</a>
                </div>
                <div id="d_archivo_muestra_<?php echo($indice."_".$cont_aux); ?>" class="div_contenedor_archivo" style="height:420px; display:<?php echo($display_aux); ?>">
                    <a href="<?php echo($ruta_arch_examen); ?>" target="_blank">Para abrir el archivo de audio haga click aqu&iacute;.</a>
                </div>
				<?php
							break;
							
						case "pdf":
				?>
                <div id="d_archivo_completo_<?php echo($indice."_".$cont_aux); ?>" class="div_pantalla_completa" style="display:none;">
                	<div style="width:100%; height:35px;">
                    	<img src="../imagenes/icon-zoom-out.png" title="Vista normal" class="img_button" style="margin:5px; width:24px; float:right;" onClick="ocultar_archivo_pantalla_completa(<?php echo($indice); ?>);" />
                    </div>
                	<div style="margin:auto; width:99%; height:calc(99vh - 35px); border:1px solid #333; overflow:auto;">
                		<embed src="<?php echo($ruta_arch_examen); ?>" width="100%" height="100%"></embed>
                    </div>
                </div>
                <div id="d_archivo_muestra_<?php echo($indice."_".$cont_aux); ?>" class="div_contenedor_archivo" style="height:420px; display:<?php echo($display_aux); ?>">
					<embed src="<?php echo($ruta_arch_examen); ?>" width="<?php echo($ancho_max); ?>" height="415"></embed>
                </div>
				<?php
							break;
							
						default:
							echo("Formato de archivo no soportado (".$extension.").");
							break;
					}
					$cont_aux++;
				}
			?>
        	<input type="hidden" id="hdd_archivo_visible_<?php echo($indice); ?>" value="0" />
        	<?php
				//Se crea una barra para mostrar los diferentes archivos
				if (count($arr_id_archivos) > 1) {
			?>
        	<div class="div_barra_archivos pager">
           		<?php
					foreach ($arr_id_archivos as $contador) {
						$activo_aux = "";
						if ($contador == 0) {
							$activo_aux = "active";
						}
				?>
            	<span id="sp_archivo_examen_<?php echo($indice."_".$contador); ?>" class="page-number clickable <?php echo($activo_aux); ?>" onClick="mostrar_archivo_examen(<?php echo($indice); ?>, <?php echo($contador); ?>);"><?php echo($contador + 1); ?></span>
            	<?php
					}
				?>
        	</div>
        	<?php
				}
				
				//Se obtiene el número de días de edición de historia clínica
				$horas_edicion = $dbVariables->getVariable(7);
				$ind_editar = $dbExamenesOptometria->getIndicadorEdicion($id_hc, $horas_edicion["valor_variable"]);
				if ($ind_editar == 1) {
			?>
        	<img src="../imagenes/cancelar.png" class="img_button" style="position:absolute; top:2px; left:2px;" title="Borrar archivo" onClick="borrar_archivo_examen(<?php echo($indice); ?>);" />
        	<script id="ajax" type="text/javascript">
				$("#d_archivo_examen_<?php echo($indice); ?>").css("display", "block");
			</script>
        	<?php
				}
			} else {
		?>
        <script id="ajax" type="text/javascript">
			$("#d_archivo_examen_<?php echo($indice); ?>").css("display", "none");
		</script>
        <?php
			}
			break;
			
		case "3": //Cargar componentes de búsqueda de archivos
			@$indice = $utilidades->str_decode($_POST["indice"]);
			@$ind_mostrar = intval($_POST["ind_mostrar"], 10);
			
			$cant_archivos = 1;
			if ($ind_mostrar == 0) {
				$cant_archivos = 0;
			}
		?>
        <input type="hidden" name="hdd_cant_archivos_<?php echo($indice); ?>" id="hdd_cant_archivos_<?php echo($indice); ?>" value="<?php echo($cant_archivos); ?>" />
        <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%;">
			<?php
				for ($j = 0; $j < 10; $j++) {
					$display_aux = "none";
					if ($j == 0 && $ind_mostrar == 1) {
						$display_aux = "table-row";
					}
			?>
            <tr id="tr_archivo_<?php echo($indice."_".$j); ?>" style="display:<?php echo($display_aux); ?>">
                <td align="right" style="width:15%;">
                    <label class="inline">Cargar archivos (<?php echo($j + 1); ?>)</label>
                </td>
                <td align="left" style="width:85%;" colspan="3">
                    <input type="file" id="fil_arch_examen_<?php echo($indice."_".$j); ?>" name="fil_arch_examen_<?php echo($indice."_".$j); ?>[]" multiple="multiple" accept="image/*, video/*, audio/*, .pdf" /> 
                </td>
            </tr>
            <?php
				}
			?>
            <tr>
                <td></td>
                <td style="width:79%">
                    <div class="agregar_alemetos" onClick="agregar_archivos_examen(<?php echo($indice); ?>);" title="Agregar archivos"></div> 
                    <div class="restar_alemetos" onClick="restar_archivos_examen(<?php echo($indice); ?>);" title="Borrar archivos"></div>
                </td>
                <td align="right" style="width:3%">
                    <img id="img_refrescar_archivos_<?php echo($indice); ?>" src="../imagenes/refresh-icon.png" title="Refrescar" class="img_button" style="margin:0;" onClick="cargar_archivo(<?php echo($indice); ?>, 0);" />
                </td>
                <td align="right" style="width:3%">
                	<img id="img_zoom_in_<?php echo($indice); ?>" src="../imagenes/icon-zoom-in.png" title="Pantalla completa" class="img_button" style="margin:0; width:24px;" onClick="mostrar_archivo_pantalla_completa(<?php echo($indice); ?>);" />
                </td>
            </tr>
        </table>
		<?php
			break;
			
		case "4": //Borrar uno de los archivos de examen
			$id_usuario = $_SESSION["idUsuario"];
			@$id_examen_hc_det = $utilidades->str_decode($_POST["id_examen_hc_det"]);
			
			$resultado = $dbExamenesOptometria->borrar_examen_optometria_hc_det($id_examen_hc_det, $id_usuario);
		?>
        <input type="hidden" id="hdd_resul_borrar_arch_examen" value="<?php echo($resultado); ?>" />
        <?php
			break;
			
		case "5": //Valores de complementos y observaciones predefinidas
			@$id_examen = $utilidades->str_decode($_POST["id_examen"]);
			@$indice = $utilidades->str_decode($_POST["indice"]);
			
			$examen_obj = $dbMaestroExamenes->get_buscar_examen($id_examen);
		?>
        <input type="hidden" id="hdd_examen_compl_b_<?php echo($indice); ?>" value="<?php echo($examen_obj["id_examen_compl"]); ?>" />
        <div id="d_observacion_predef_b_<?php echo($indice); ?>"><?php echo($examen_obj["observacion_predef"]); ?></div>
        <?php
			break;
	}
?>
