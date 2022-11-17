<?php
	session_start();
	
	header("Content-Type: text/xml; charset=UTF-8");
	
	require_once("../db/DbMaestroProcedimientos.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbEspecialidades.php");
	
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/Class_Barra_Progreso.php");
	
	$dbMaestroProcedimientos = new DbMaestroProcedimientos();
	$dbListas = new DbListas();
	$dbEspecialidades = new DbEspecialidades();
	
	$utilidades = new Utilidades();
	$combo = new Combo_Box();
	$barra_progreso = new Barra_Progreso();
	
	$opcion = $_POST["opcion"];
	
	switch ($opcion) {
		case "1": //Carga el listado de procedimientos
			@$parametro = $utilidades->str_decode($_POST["parametro"]);
			
			$listaProcedimientos = $dbMaestroProcedimientos->getProcedimientos($parametro, "");
        ?>
        <table class="paginated modal_table" style="width: 99%; margin: auto;">
            <thead>
                <tr><th colspan="5">Listado de procedimientos - <?php echo(count($listaProcedimientos)." registros"); ?></th></tr>
                <tr>
                    <th style="width:9%;">Codigo</th>
                    <th style="width:55%;">Nombre</th>
                    <th style="width:15%;">Especialidad</th>
                    <th style="width:12%;">V&iacute;a</th>
                    <th style="width:9%;">Estado</th>
                </tr>
            </thead>
            <?php
				if (count($listaProcedimientos) >= 1) {
					foreach ($listaProcedimientos as $proc_aux) {
						$estado = $proc_aux["ind_activo"];
						if ($estado == 1) {
							$estado = "Activo";
							$class_estado = "activo";
						} else if ($estado == 0) {
							$estado = "No Activo";
							$class_estado = "inactivo";
						}
			?>
            <tr onclick="seleccionar_procedimiento('<?php echo($proc_aux["cod_procedimiento"]); ?>');">
                <td align="center"><?php echo($proc_aux["cod_procedimiento"]); ?></td>
                <td align="left"><?php echo($proc_aux["nombre_procedimiento"]); ?></td>
                <td align="center"><?php echo($proc_aux["nombre_especialidad"]); ?></td>
                <td align="center"><?php echo($proc_aux["nombre_via"]); ?></td>
                <td align="center"><span class="<?php echo($class_estado); ?>"><?php echo($estado); ?></span></td>
            </tr>
            <?php
					}
				} else {
			?>
            <tr>
                <td colspan="5">No hay resultados</td>
            </tr>
            <?php
				}
			?>
        </table>
        <script id="ajax">
			//<![CDATA[ 
			$(function () {
				$(".paginated", "table").each(function (i) {
					$(this).text(i + 1);
				});
				
				$("table.paginated").each(function () {
					var currentPage = 0;
					var numPerPage = 10;
					var $table = $(this);
					$table.bind("repaginate", function () {
						$table.find("tbody tr").hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
					});
					$table.trigger("repaginate");
					var numRows = $table.find("tbody tr").length;
					var numPages = Math.ceil(numRows / numPerPage);
					var $pager = $('<div class="pager"></div>');
					for (var page = 0; page < numPages; page++) {
						$('<span class="page-number"></span>').text(page + 1).bind("click", {
							newPage: page
						}, function (event) {
							currentPage = event.data["newPage"];
							$table.trigger("repaginate");
							$(this).addClass("active").siblings().removeClass("active");
						}).appendTo($pager).addClass("clickable");
					}
					$pager.insertBefore($table).find("span.page-number:first").addClass("active");
				});
			});
			//]]>
        </script>
        <?php
			break;
			
		case "2": //Administra procedimientos, crear y editar.
			@$tipo = $utilidades->str_decode($_POST["tipo"]);
			@$cod_procedimiento = $utilidades->str_decode($_POST["cod_procedimiento"]);
			
			$nombre_procedimiento = "";
			
			$estiloOculto = "display:none";
			$reg_activo = "checked";
			$ind_proc_qx = "checked";
			$ind_lateralidad = "checked";
			$accion = "crear_procedimiento";
			
			//Si el procedimiento ya existe
			if ($tipo == "0") {
				$procedimiento_obj = $dbMaestroProcedimientos->getProcedimiento($cod_procedimiento);
				
				$estilo_oculto = "display:table-row";
				$nombre_procedimiento = $procedimiento_obj["nombre_procedimiento"];
				$id_especialidad = $procedimiento_obj["id_especialidad"];
				$id_via = $procedimiento_obj["id_via"];
				$cod_und_negocios = $procedimiento_obj["cod_und_negocios"];
				$cod_centro_costos = $procedimiento_obj["cod_centro_costos"];
				
				//Si el registro esta activado
				if ($procedimiento_obj["ind_activo"] == "0") {
					$reg_activo = "";
				}
				
				//Si el registro es quirúrgico
				if ($procedimiento_obj["ind_proc_qx"] == "0") {
					$ind_proc_qx = "";
				}
				
				//Si el registro tiene lateralidad
				if ($procedimiento_obj["ind_lateralidad"] == "0") {
					$ind_lateralidad = "";
				}
				
				$accion = "editar_procedimiento";
			}
			
			//Listados
			$lista_especialidades = $dbEspecialidades->getListaEspecialidades(1);
			$lista_unidades_negocio = $dbListas->getListaDetalles(81, 1);
			$lista_centros_costo = $dbListas->getListaDetalles(82, 1);
        ?>
        <div class="encabezado">
            <h3>Procedimientos (CUPS)</h3>
        </div>
        <div class="contenedor_error" id="contenedor_error"></div>
        <div class="contenedor_exito" id="contenedor_exito"></div>
        <div style="width:98%; margin:auto;">
            <form id="frm_nuevo_procedimiento" name="frm_nuevo_procedimiento">
                <div style="text-align: left;">    
                    <fieldset>
                        <table style="width:100%;">
                            <tr style="<?php echo($estilo_oculto); ?>">
                                <td style="width:15%;">
                                    <label class="inline"><b>C&oacute;digo:</b></label>
                                </td>
                                <td style="width:59%;">
                                	<?php
                                    	if ($tipo == "0") {
									?>
                                    <input type="hidden" id="txt_cod_procedimiento" name="txt_cod_procedimiento" value="<?php echo($cod_procedimiento); ?>" />
                                    <label class="inline"><?php echo($cod_procedimiento); ?></label>
                                    <?php
										} else {
									?>
                                    <input type="text" id="txt_cod_procedimiento" name="txt_cod_procedimiento" value="" onkeypress="return solo_alfanumericos(event);" maxlength="6" style="width:100px;" />
                                    <?php
										}
									?>
                                </td>
                                <td align="right" style="width:20%;">
                                    <label class="inline"><b>Registro activo:</b></label>
                                </td>
                                <td style="width:6%;">
                                    <label style="margin: 6px 0 0 0;">
                                        <input type="checkbox" name="chk_activo" id="chk_activo" <?php echo($reg_activo); ?> />
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="inline"><b>Nombre*:</b></label>
                                </td>
                                <td colspan="2">
                                    <input type="text" id="txt_nombre_procedimiento" name="txt_nombre_procedimiento" value="<?php echo($nombre_procedimiento); ?>" onblur="trim_cadena(this);" maxlength="200" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="inline"><b>Especialidad:</b></label>
                                </td>
                                <td>
                                    <?php
                                    	$combo->getComboDb("cmb_especialidad", $id_especialidad, $lista_especialidades, "id_especialidad,nombre_especialidad", "--Seleccione--", "seleccionar_especialidad(this.value);", 1, "width:300px;");
									?>
                                </td>
                                <td align="right">
                                    <label class="inline"><b>Procedimiento quir&uacute;rgico:</b></label>
                                </td>
                                <td>
                                    <label style="margin: 6px 0 0 0;">
                                        <input type="checkbox" name="chk_proc_qx" id="chk_proc_qx" <?php echo($ind_proc_qx); ?> />
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="inline"><b>V&iacute;a:</b></label>
                                </td>
                                <td>
                                	<div id="d_via_especialidad">
                                    	<?php
                                        	if ($id_especialidad != "") {
												$lista_vias = $dbEspecialidades->getListaVias($id_especialidad, 1);
												$combo->getComboDb("cmb_via", $id_via, $lista_vias, "id_via,nombre_via", "--Seleccione--", "", 1, "width:300px;");
											} else {
										?>
                                        <select id="cmb_via" name="cmb_via" style="width:300px;">
                                            <option value="">--Seleccione--</option>
                                        </select>
                                        <?php
											}
										?>
                                    </div>
                                </td>
                                <td align="right">
                                    <label class="inline"><b>Lateralidad:</b></label>
                                </td>
                                <td>
                                    <label style="margin: 6px 0 0 0;">
                                        <input type="checkbox" name="chk_lateralidad" id="chk_lateralidad" <?php echo($ind_lateralidad); ?> />
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="inline"><b>Unidad de negocio:</b></label>
                                </td>
                                <td>
                                    <?php
                                    	$combo->getComboDb("cmb_cod_und_negocios", $cod_und_negocios, $lista_unidades_negocio, "codigo_detalle,nombre_detalle", "--Seleccione--", "", 1, "width:300px;");
									?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="inline"><b>Centro de costos:</b></label>
                                </td>
                                <td>
                                    <?php
                                    	$combo->getComboDb("cmb_cod_centro_costos", $cod_centro_costos, $lista_centros_costo, "codigo_detalle,nombre_detalle", "--Seleccione--", "", 1, "width:300px;");
									?>
                                </td>
                            </tr>
                        </table>
                        <div style="text-align:center;">
                            <input type="submit" id="btn_guardar_procedimiento" nombre="btn_guardar_procedimiento" value="Guardar" class="btnPrincipal" onclick="<?php echo($accion); ?>();"/>
                        </div>
                    </fieldset>
                </div>
            </form>
        </div>
        <?php
			break;
			
		case "3": //Guardar/Editar Exámenes
			$id_usuario = $_SESSION["idUsuario"];
			@$tipo = $utilidades->str_decode($_POST["tipo"]);
			@$cod_procedimiento = $utilidades->str_decode($_POST["cod_procedimiento"]);
			@$nombre_procedimiento = $utilidades->str_decode($_POST["nombre_procedimiento"]);
			@$id_especialidad = $utilidades->str_decode($_POST["id_especialidad"]);
			@$id_via = $utilidades->str_decode($_POST["id_via"]);
			@$ind_proc_qx = $utilidades->str_decode($_POST["ind_proc_qx"]);
			@$ind_lateralidad = $utilidades->str_decode($_POST["ind_lateralidad"]);
			@$cod_und_negocios = $utilidades->str_decode($_POST["cod_und_negocios"]);
			@$cod_centro_costos = $utilidades->str_decode($_POST["cod_centro_costos"]);
			@$ind_activo = $utilidades->str_decode($_POST["ind_activo"]);
			
			if ($tipo == "1") {//Guarda
				$resultado = $dbMaestroProcedimientos->crear_procedimiento($cod_procedimiento, $nombre_procedimiento, $id_especialidad,
						$id_via, $ind_proc_qx, $ind_lateralidad, $cod_und_negocios, $cod_centro_costos, $ind_activo, $id_usuario);
			} else if ($tipo == "0") {//Edita
				$resultado = $dbMaestroProcedimientos->editar_procedimiento($cod_procedimiento, $nombre_procedimiento, $id_especialidad,
						$id_via, $ind_proc_qx, $ind_lateralidad, $cod_und_negocios, $cod_centro_costos, $ind_activo, $id_usuario);
			}
		?>
        <input type="hidden" id="hdd_resul_procedimiento" name="hdd_resul_procedimiento" value="<?php echo($resultado); ?>" />
        <?php
			break;
			
		case "4": //Formulario de carga de archivos
		?>
        <div class="encabezado">
            <h3>Procedimientos (CUPS)</h3>
        </div>
        <div class="contenedor_error" id="contenedor_error"></div>
        <div class="contenedor_exito" id="contenedor_exito"></div>
        <div style="width:98%; margin:auto; text-align:left;">
        	<br />
        	<input type="file" id="fil_actualizacion" name="fil_actualizacion[]" accept=".csv" />
            <br />
            <input type="checkbox" name="chk_inhabilitar" id="chk_inhabilitar" />
            <label>Inhabilitar procedimientos que no se encuentren en el archivo</label>
        </div>
        <div style="width:98%; margin:auto; text-align:center">
        	<input type="button" id="btn_procesar_arch" nombre="btn_procesar_arch" value="Procesar archivo" class="btnPrincipal peq" onclick="procesar_archivo();"/>
            <br />
            <?php
				$barra_progreso->get("d_barra_progreso_adj", "50%", false, 0);
			?>
            <br />
        </div>
        <?php
			break;
			
		case "5": //Carga de archivos
			$id_usuario = $_SESSION["idUsuario"];
			@$ind_inhabilitar = $utilidades->str_decode($_POST["ind_inhabilitar"]);
			
			//Listado de formatos permitidos
			$arr_formatos = array("csv");
			
			$resultado = 0;
			
			if (isset($_FILES["fil_actualizacion"])) {
				//Se cargan los nombres de los archivos
				$arr_nombres_aux = $_FILES["fil_actualizacion"]["name"];
				$arr_tmp_nombres_aux = $_FILES["fil_actualizacion"]["tmp_name"];
				
				if (!is_array($arr_nombres_aux)) {
					$arr_nombres_aux = array($arr_nombres_aux);
					$arr_tmp_nombres_aux = array($arr_tmp_nombres_aux);
				}
				
				$nombre_ori_aux = $arr_nombres_aux[0];
				$extension_aux = $utilidades->get_extension_arch($nombre_ori_aux);
				
				if (in_array($extension_aux, $arr_formatos)) {
					@$nombre_tmp = $arr_tmp_nombres_aux[0];
					
					if ($fh = fopen($nombre_tmp, "r")) {
						//Se lee el archivo de actualización de procedimientos
						$cont_aux = -1;
						$arr_procedimientos = array();
						while (!feof($fh)) {
							$linea = iconv('windows-1250', 'utf-8', fgets($fh));
							if ($cont_aux >= 0 && $linea != "") {
								$arr_campos = explode(";", $linea);
								$arr_procedimientos[$cont_aux]["cod_procedimiento"] = $arr_campos[0];
								$arr_procedimientos[$cont_aux]["nombre_procedimiento"] = $arr_campos[1];
							}
							$cont_aux++;
						}
						fclose($fh);
						
						//Se actualizan los procedimientos
						$resultado = $dbMaestroProcedimientos->actualizar_procedimientos($arr_procedimientos, $ind_inhabilitar, $id_usuario);
					}
				}
			}
		?>
        <input type="hidden" id="hdd_resul_actualizar" name="hdd_resul_actualizar" value="<?php echo($resultado); ?>" />
        <?php
			break;
			
		case "6": //Carga del combo de vías de una especialidad
			@$id_especialidad = $utilidades->str_decode($_POST["id_especialidad"]);
			if ($id_especialidad != "") {
				$lista_vias = $dbEspecialidades->getListaVias($id_especialidad, 1);
				$combo->getComboDb("cmb_via", $id_via, $lista_vias, "id_via,nombre_via", "--Seleccione--", "", 1, "width:300px;");
			} else {
		?>
        <select id="cmb_via" name="cmb_via" style="width:300px;">
            <option value="">--Seleccione--</option>
        </select>
        <?php
			}
			break;
	}
?>
