<?php session_start();
	/*
	 * Pagina para registro de cirugías
	 * Autor: Feisar Moreno - 04/04/2014
	 */
 	header('Content-Type: text/xml; charset=UTF-8');
	
	require_once("../db/DbCirugias.php");
	require_once("../db/DbMaestroProcedimientos.php");
	require_once("../db/DbMenus.php");
	require_once("../db/DbVariables.php");
	require_once("../funciones/Utilidades.php");
	
	$dbCirugias = new DbCirugias();
	$dbMaestroProcedimientos = new DbMaestroProcedimientos();
	$dbMenus = new DbMenus();
	$dbVariables = new Dbvariables();
	
	$utilidades = new Utilidades();
	
	$opcion = $_POST["opcion"];
	
	switch ($opcion) {
		case "1"://Dibuja el div flotante para buscar procedimientos
			?>
			<div class="encabezado">
				<h3>Listado de procedimientos</h3>
			</div>
			<br/>
			<form id="frmBuscarProcedimiento">
				<table>
					<tr>
						<td>
							<input type="text" id="txt_identificacion_interno" name="txt_identificacion_interno" placeholder="Digite el codigo o nombre del procedimiento" style="width:400px;" />
						</td>
						<td>
							<input type="submit" value="Buscar" class="btnPrincipal peq" onclick="validar_buscar_procedimientos();" />
						</td>
					</tr>
				</table>
			</form>
			<br/>
			<div style="clear: both;"></div>
			<br/>
			<div id="d_procedimientos_b" style="height:350px; overflow:auto;">
			</div>
			<?php
			break;
			
		case "2": //Resultados de búsqueda de procedimientos
			$parametro = trim($utilidades->str_decode($_POST['parametro']));
		?>
		<table class="modal_table" id="tablaPreciosModal" style="width:100%;">
			<tr id="tabla_encabezado">
				<th style="width:10%;">C&oacute;digo</th>
				<th style="width:90%;">Procedimiento</th>
			</tr>
	        <?php
				$lista_procedimientos = $dbMaestroProcedimientos->getProcedimientos($parametro);
				
				if (count($lista_procedimientos) > 0) {
					$cont = 0;
					foreach ($lista_procedimientos as $proc_aux) {
			?>
			<tr onclick="seleccionar_procedimiento(<?php echo($cont); ?>);">
				<td align="center">
					<input type="hidden" name="hdd_cod_procedimiento_b_<?php echo($cont); ?>" id="hdd_cod_procedimiento_b_<?php echo($cont); ?>" value="<?php echo($proc_aux["cod_procedimiento"]); ?>" />
					<?php echo($proc_aux['cod_procedimiento']); ?>
				</td>
				<td align="left">
					<input type="hidden" name="hdd_nombre_procedimiento_b_<?php echo($cont); ?>" id="hdd_nombre_procedimiento_b_<?php echo($cont); ?>" value="<?php echo($proc_aux["nombre_procedimiento"]); ?>" />
					<?php echo($proc_aux['nombre_procedimiento']); ?>
				</td>
			</tr>
			<?php
						$cont++;
					}
				} else {
			?>
			<tr>
				<td colspan="2">No se encontraron procedimientos</td>
			</tr>
            <?php
				}
			?>
		</table>
		<?php
			break;
			
		case "3": //Guardar Cirugía
			$id_usuario = $_SESSION["idUsuario"];
			@$id_hc = $utilidades->str_decode($_POST["id_hc"]);
			@$id_admision = $utilidades->str_decode($_POST["id_admision"]);
			@$tipo_guardar = $utilidades->str_decode($_POST["tipo_guardar"]);
			@$fecha_cx = $utilidades->str_decode($_POST["fecha_cx"]);
			@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
			@$id_amb_rea = $utilidades->str_decode($_POST["id_amb_rea"]);
			@$id_fin_pro = $utilidades->str_decode($_POST["id_fin_pro"]);
			@$id_usuario_prof = $utilidades->str_decode($_POST["id_usuario_prof"]);
			@$ind_reoperacion = $utilidades->str_decode($_POST["ind_reoperacion"]);
			@$ind_reop_ent = $utilidades->str_decode($_POST["ind_reop_ent"]);
			@$fecha_cx_ant = $utilidades->str_decode($_POST["fecha_cx_ant"]);
			@$observaciones_cx = trim($utilidades->str_decode($_POST["observaciones_cx"]));
			@$arch_cx = $utilidades->str_decode($_POST["arch_cx"]);
			@$arch_stickers = $utilidades->str_decode($_POST["arch_stickers"]);
			
			@$cant_procedimientos = intval($utilidades->str_decode($_POST['cant_procedimientos']), 10);
			$array_procedimientos = array();
			$cont_aux = 0;
			for ($i = 0; $i < $cant_procedimientos; $i++) {
				if (isset($_POST["cod_procedimiento_".$i])) {
					@$cod_procedimiento = $utilidades->str_decode($_POST["cod_procedimiento_".$i]);
					@$id_ojo = $utilidades->str_decode($_POST["id_ojo_".$i]);
					@$via = $utilidades->str_decode($_POST["via_".$i]);
					$array_procedimientos[$cont_aux]["cod_procedimiento"] = $cod_procedimiento;
					$array_procedimientos[$cont_aux]["id_ojo"] = $id_ojo;
					$array_procedimientos[$cont_aux]["via"] = $via;
					$cont_aux++;
				}
			}
			$cant_procedimientos = $cont_aux;
			
			@$cant_ciex = intval($utilidades->str_decode($_POST['cant_ciex']), 10);
			$array_diagnosticos = array();
			$cont_aux = 0;
			for ($i = 1; $i <= $cant_ciex; $i++) {
				if (isset($_POST['cod_ciex_'.$i])) {
					@$ciex_diagnostico = $utilidades->str_decode($_POST['cod_ciex_'.$i]);
					@$valor_ojos = $utilidades->str_decode($_POST['val_ojos_'.$i]);
					$array_diagnosticos[$cont_aux][0] = $ciex_diagnostico;
					$array_diagnosticos[$cont_aux][1] = $valor_ojos;
					$cont_aux++;
				}
			}
			
			$resultado = $dbCirugias->editar_cirugia($id_hc, $id_admision, $fecha_cx, $id_convenio, $id_amb_rea, $id_fin_pro,
						 $id_usuario_prof, $ind_reoperacion, $ind_reop_ent, $fecha_cx_ant, $observaciones_cx, $arch_cx,
						 $arch_stickers, $array_procedimientos, $array_diagnosticos, $tipo_guardar, $id_usuario);
		?>
		<input type="hidden" name="hdd_resul_guardar" id="hdd_resul_guardar" value="<?php echo($resultado); ?>" />
		<input type="hidden" name="hdd_tipo_guardar" id="hdd_tipo_guardar" value="<?php echo($tipo_guardar); ?>" />
		<div class="contenedor_error" id="contenedor_error"></div>
		<div class="contenedor_exito" id="contenedor_exito"></div>
		<?php
			break;
			
		case "4": //Subir el archivo al servidor
			@$id_hc = $utilidades->str_decode($_POST["hdd_id_hc_cx"]);
			@$nombre_tmp = $_FILES["fil_arch_cx"]["tmp_name"];
			@$nombre_ori = $_FILES["fil_arch_cx"]["name"];
			
			//Se obtiene el nombre que tendrá el archivo
			$nombre_arch = $dbCirugias->construir_nombre_arch($id_hc, $nombre_ori, "cirugia", "");
			
			//Se crea el directorio del archivo
			$pos_aux = strrpos($nombre_arch, "/", -1);
			$ruta_arch_examen = substr($nombre_arch, 0, $pos_aux);
			mkdir($ruta_arch_examen, 0755, true);
			
			//Se copia el archivo
			copy($nombre_tmp, $nombre_arch);
		/*?>
        <html>
            <body><?php echo($nombre_arch); ?></body>
        </html>
    	<?php*/
			break;
			
		case "5": //Mostrar el archivo correspondiente a la cirugía
			$id_usuario = $_SESSION["idUsuario"];
			@$id_hc = $utilidades->str_decode($_POST["id_hc"]);
			
			//Se busca la ruta de la imagen
			$cirugia_obj = $dbCirugias->get_cirugia($id_hc);
			
			$ruta_arch_cx = "";
			if (isset($cirugia_obj["ruta_arch_cx"])) {
				$ruta_arch_cx = trim($cirugia_obj["ruta_arch_cx"]);
			}
			
			//Se verifica si hay algún archivo que mostrar
			if ($ruta_arch_cx != "") {
				//Se borran las imágenes temporales creadas por el usuario actual
				$ruta_tmp = "../historia_clinica/tmp/".$id_usuario;
				/*if (file_exists($ruta_tmp)) {
					@array_map('unlink', glob($ruta_tmp."/img*.*"));
				}*/
				@mkdir($ruta_tmp);
				
				//Se obtiene la ruta actual de las imágenes
				$arr_ruta_base = $dbVariables->getVariable(17);
				$ruta_base = $arr_ruta_base["valor_variable"];
				
				//Se obtiene el tipo de archivo
				$extension = $utilidades->get_extension_arch($ruta_arch_cx);
				
				$ruta_arch_cx = str_replace("../imagenes/imagenes_hce", $ruta_base, $ruta_arch_cx);
				copy($ruta_arch_cx, $ruta_tmp."/img_sap_".$id_hc.".".$extension);
				$ruta_arch_cx = $ruta_tmp."/img_sap_".$id_hc.".".$extension;
				
				$ancho_max = 850;
				
				switch ($extension) {
					case "jpg":
					case "png":
					case "bmp":
					case "gif":
						//Se obtienen las dimensiones del archivo
						@$arr_prop_imagen = getimagesize($ruta_arch_cx);
						$ancho_aux = $arr_prop_imagen[0];
						$alto_aux = $arr_prop_imagen[1];
						
						if ($ancho_aux > $ancho_max) {
							$ancho_aux = $ancho_max;
						}
				?>
				<img src="<?php echo($ruta_arch_cx); ?>" style="width:<?php echo($ancho_aux); ?>px;" />
				<?php
						break;
						
					case "pdf":
				?>
                <embed src="<?php echo($ruta_arch_cx); ?>" width="<?php echo($ancho_max); ?>" height="345">
                <?php
						break;
						
					default:
						echo("Formato de archivo no soportado (".$extension.")");
						break;
				}
			}
			break;
			
		case "6": //Subir el archivo de stickers al servidor
			@$id_hc = $utilidades->str_decode($_POST["hdd_id_hc_stickers"]);
			@$nombre_tmp = $_FILES["fil_hoja_stickers"]["tmp_name"];
			@$nombre_ori = $_FILES["fil_hoja_stickers"]["name"];
			
			//Se obtiene el nombre que tendrá el archivo
			$nombre_arch = $dbCirugias->construir_nombre_arch($id_hc, $nombre_ori, "stickers", "");
			
			//Se crea el directorio del archivo
			$pos_aux = strrpos($nombre_arch, "/", -1);
			$ruta_arch_examen = substr($nombre_arch, 0, $pos_aux);
			mkdir($ruta_arch_examen, 0755, true);
			
			//Se copia el archivo
			copy($nombre_tmp, $nombre_arch);
		/*?>
        <html>
            <body><?php echo($nombre_arch); ?></body>
        </html>
    	<?php*/
			break;
			
		case "7": //Mostrar el archivo de stickers
			$id_usuario = $_SESSION["idUsuario"];
			@$id_hc = $utilidades->str_decode($_POST["id_hc"]);
			
			//Se busca la ruta de la imagen
			$cirugia_obj = $dbCirugias->get_cirugia($id_hc);
			
			$ruta_arch_stickers = "";
			if (isset($cirugia_obj["ruta_arch_stickers"])) {
				$ruta_arch_stickers = trim($cirugia_obj["ruta_arch_stickers"]);
			}
			
			//Se verifica si hay algún archivo que mostrar
			if ($ruta_arch_stickers != "") {
				//Se borran las imágenes temporales creadas por el usuario actual
				$ruta_tmp = "../historia_clinica/tmp/".$id_usuario;
				/*if (file_exists($ruta_tmp)) {
					@array_map('unlink', glob($ruta_tmp."/img*.*"));
				}*/
				@mkdir($ruta_tmp);
				
				//Se obtiene la ruta actual de las imágenes
				$arr_ruta_base = $dbVariables->getVariable(17);
				$ruta_base = $arr_ruta_base["valor_variable"];
				
				//Se obtiene el tipo de archivo
				$extension = $utilidades->get_extension_arch($ruta_arch_stickers);
				
				$ruta_arch_stickers = str_replace("../imagenes/imagenes_hce", $ruta_base, $ruta_arch_stickers);
				copy($ruta_arch_stickers, $ruta_tmp."/img_stickers_".$id_hc.".".$extension);
				$ruta_arch_stickers = $ruta_tmp."/img_stickers_".$id_hc.".".$extension;
				
				$ancho_max = 850;
				
				switch ($extension) {
					case "jpg":
					case "png":
					case "bmp":
					case "gif":
						//Se obtienen las dimensiones del archivo
						@$arr_prop_imagen = getimagesize($ruta_arch_stickers);
						$ancho_aux = $arr_prop_imagen[0];
						$alto_aux = $arr_prop_imagen[1];
						
						if ($ancho_aux > $ancho_max) {
							$ancho_aux = $ancho_max;
						}
				?>
				<img src="<?php echo($ruta_arch_stickers); ?>" style="width:<?php echo($ancho_aux); ?>px;" />
				<?php
						break;
						
					case "pdf":
				?>
                <embed src="<?php echo($ruta_arch_stickers); ?>" width="<?php echo($ancho_max); ?>" height="345">
                <?php
						break;
						
					default:
						echo("Formato de archivo no soportado (".$extension.")");
						break;
				}
			}
			break;
	}
?>
