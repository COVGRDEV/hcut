<?php
	header("Content-Type: text/xml; charset=UTF-8");
	session_start();
	
	require_once("../db/DbProgramacionCx.php");
	require_once("../db/DbListas.php");
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/FuncionesPersona.php");
	
	$dbProgramacionCx = new DbProgramacionCx();
	$dbListas = new DbListas();
	
	$utilidades = new Utilidades();
	$funciones_persona = new FuncionesPersona();
	
	$opcion = $_POST["opcion"];
	
	switch ($opcion) {
		case "1": //Búsqueda de procedimientos
	?>
    <div class="encabezado">
        <h3>Buscar Conceptos</h3>
    </div>
    <div>
        <table>
            <tr>
                <td id="advertenciasg">
                    <div class="contenedor_error" id="contenedor_error"></div>
                </td>
            </tr>
        </table>
        <table style="width: 100%;">
            <tr>
                <td>
                    <input type="text" id="txt_concepto_b" name="txt_concepto_b" placeholder="C&oacute;digo o nombre del procedimiento, medicamento o insumo" onblur="trim_cadena(this);" />
                </td>
                <td style="width: 8%;">
                    <input type="button" id="btnBuscar" nombre="btn_buscar" value="Buscar" class="btnPrincipal peq" onclick="buscar_conceptos();" />
                </td>
            </tr>
        </table>
        <div id="d_buscar_conceptos"></div>
    </div>    
    <?php
			break;
			
		case "2": //Resultados de búsqueda de procedimientos
			@$texto_b = trim($utilidades->str_decode($_POST["texto_b"]));
			
			require_once("../db/DbMaestroProcedimientos.php");
			$dbMaestroProcedimientos = new DbMaestroProcedimientos();
			
			$lista_procedimientos = $dbMaestroProcedimientos->getListaConceptos($texto_b);
	?>
    <table class="paginated modal_table" style="width: 99%; margin: auto;">
        <thead>
            <tr>
                <th style="width:15%;">C&oacute;digo</th>
                <th style="width:85%;">Nombre</th>
            </tr>
        </thead>
        <?php
			if (count($lista_procedimientos) > 0) {
				foreach ($lista_procedimientos as $proc_aux) {
		?>
        <tr onclick="seleccionar_concepto('<?php echo($proc_aux["tipo_precio"]); ?>', '<?php echo($proc_aux["cod_insumo"]); ?>', '<?php echo($proc_aux["nombre_insumo"]); ?>');">
        	<td align="center"><?php echo($proc_aux["cod_insumo"]); ?></td>
           	<td align="left"><?php echo($proc_aux["nombre_insumo"]); ?></td>
        </tr>
        <?php
				}
			} else {
		?>
        <tr>
	        <td colspan="2">No se encontraron resultados</td>
        </tr>
        <?php
			}
        ?>
    </table>
    <script id="ajax">
		//<![CDATA[ 
		$(function() {
			$(".paginated", "table").each(function(i) {
				$(this).text(i + 1);
			});
			
			$("table.paginated").each(function() {
				var currentPage = 0;
				var numPerPage = 10;
				var $table = $(this);
				$table.bind("repaginate", function() {
					$table.find("tbody tr").hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
				});
				$table.trigger("repaginate");
				var numRows = $table.find("tbody tr").length;
				var numPages = Math.ceil(numRows / numPerPage);
				var $pager = $('<div class="pager"></div>');
				for (var page = 0; page < numPages; page++) {
					$('<span class="page-number"></span>').text(page + 1).bind("click", {
						newPage: page
					}, function(event) {
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
			
		case "3": //Reporte general
			@$fecha_ini = $utilidades->str_decode($_POST["fecha_ini"]);
			@$fecha_fin = $utilidades->str_decode($_POST["fecha_fin"]);
			@$tipo_fecha = $utilidades->str_decode($_POST["tipo_fecha"]);
			@$tipo_concepto = $utilidades->str_decode($_POST["tipo_concepto"]);
			@$cod_concepto = $utilidades->str_decode($_POST["cod_concepto"]);
			@$id_usuario_prof = $utilidades->str_decode($_POST["id_usuario_prof"]);
			@$cant_estados_prog = intval($_POST["hdd_cant_estados_prog"], 10);
			
			$lista_estados = array();
			for ($i = 0; $i < $cant_estados_prog; $i++) {
				$lista_estados[$i]["id_estado_prog"] = $utilidades->str_decode($_POST["hdd_estado_prog_".$i]);
				$lista_estados[$i]["sel_estado_prog"] = intval($_POST["hdd_sel_estado_prog_".$i], 10);
			}
			
			//Se obtiene el listado de estados de programación
			$lista_estados_prog = $dbListas->getListaDetalles(71, 1);
			
			//Se obtiene el listado de las programaciones de cirugía
			$lista_programacion_cx = $dbProgramacionCx->getListaProgramacionCxFechas($fecha_ini, $fecha_fin, $tipo_fecha, $tipo_concepto, $cod_concepto, $id_usuario_prof, $lista_estados);
		?>
        <table class="paginated modal_table" style="width: 99%; margin: auto;">
        	<thead>
                <tr>
                    <th style="width:11%;">Fecha programada</th>
                    <th style="width:12%;">Documento</th>
                    <th style="width:20%;">Nombre Completo</th>
                    <th style="width:32%;">Concepto(s)</th>
                    <th style="width:15%;">Profesional</th>
                    <th style="width:10%;">Estado</th>
                </tr>
            </thead>
            <?php
            	if (count($lista_programacion_cx) > 0) {
					foreach ($lista_programacion_cx as $prog_cx_aux) {
						$lista_programacion_cx_det = $dbProgramacionCx->getListaProgramacionCxDet($prog_cx_aux["id_prog_cx"]);
						
						$cadena_productos = "<ul class=\"no-margin\">";
						if (count($lista_programacion_cx_det) > 0) {
							foreach ($lista_programacion_cx_det as $prog_det_aux) {
								$cadena_productos .= "<li>";
								switch ($prog_det_aux["tipo_elemento"]) {
									case "P":
										$cadena_productos .= $prog_det_aux["cod_procedimiento"]." - ".$prog_det_aux["nombre_procedimiento"];
										break;
									case "M":
										$cadena_productos .= $prog_det_aux["cod_medicamento"]." - ".$prog_det_aux["nombre_comercial"]." (".$prog_det_aux["nombre_generico"].") - ".$prog_det_aux["presentacion"];
										break;
									case "I":
										$cadena_productos .= $prog_det_aux["cod_insumo"]." - ".$prog_det_aux["nombre_insumo"];
										break;
								}
								$cadena_productos .= "</li>";
							}
						}
						$cadena_productos .= "</ul>";
						
						$nombre_completo = $funciones_persona->obtenerNombreCompleto($prog_cx_aux["nombre_1"], $prog_cx_aux["nombre_2"], $prog_cx_aux["apellido_1"], $prog_cx_aux["apellido_2"]);
			?>
            <tr>
            	<td align="center"><?php echo($prog_cx_aux["fecha_prog_t"]." ".$prog_cx_aux["hora_prog_t"]); ?></td>
            	<td align="center"><?php echo($prog_cx_aux["cod_tipo_documento"]." ".$prog_cx_aux["numero_documento"]); ?></td>
            	<td align="left"><?php echo($nombre_completo); ?></td>
            	<td align="left"><?php echo($cadena_productos); ?></td>
            	<td align="left"><?php echo($prog_cx_aux["nombre_usuario_prof"]." ".$prog_cx_aux["apellido_usuario_prof"]); ?></td>
            	<td align="center"><?php echo($prog_cx_aux["estado_prog"]); ?></td>
            </tr>
            <?php
					}
				} else {
			?>
            <tr>
            	<td align="center" colspan="6">
                	No se encontraron programaciones con los par&aacute;metros dados
                </td>
            </tr>
            <?php
				}
			?>
		</table>
        <script id="ajax">
			//<![CDATA[ 
			$(function() {
				$(".paginated", "table").each(function(i) {
					$(this).text(i + 1);
				});
				
				$("table.paginated").each(function() {
					var currentPage = 0;
					var numPerPage = 10;
					var $table = $(this);
					$table.bind("repaginate", function() {
						$table.find("tbody tr").hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
					});
					$table.trigger("repaginate");
					var numRows = $table.find("tbody tr").length;
					var numPages = Math.ceil(numRows / numPerPage);
					var $pager = $('<div class="pager"></div>');
					for (var page = 0; page < numPages; page++) {
						$('<span class="page-number"></span>').text(page + 1).bind("click", {
							newPage: page
						}, function(event) {
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
        <br />
        <?php
        	if (count($lista_programacion_cx) > 0) {
		?>
        <input type="hidden" id="reporte" name="reporte" />
        <input type="button" value="Generar Excel" class="btnPrincipal" onclick="generar_reporte_excel();" />
        <form name="frm_excel_general" id="frm_excel_general" action="reporte_programacion_cx_excel.php" method="post" style="display:none;" target="_blank">
            <input type="hidden" id="hdd_tipo_reporte" name="hdd_tipo_reporte" value="1" />
            <input type="hidden" id="hdd_fecha_ini" name="hdd_fecha_ini" />
            <input type="hidden" id="hdd_fecha_fin" name="hdd_fecha_fin" />
            <input type="hidden" id="hdd_tipo_fecha" name="hdd_tipo_fecha" />
            <input type="hidden" id="hdd_cod_concepto_r" name="hdd_cod_concepto_r" />
            <input type="hidden" id="hdd_tipo_concepto_r" name="hdd_tipo_concepto_r" />
            <input type="hidden" id="hdd_id_usuario_prof" name="hdd_id_usuario_prof" />
            <input type="hidden" id="hdd_cant_estados_prog" name="hdd_cant_estados_prog" value="<?php echo(count($lista_estados_prog));?>" />
            <?php
				for ($i = 0; $i < count($lista_estados_prog); $i++) {
					$estado_aux = $lista_estados_prog[$i];
			?>
            <input type="hidden" id="hdd_estado_prog_<?php echo($i); ?>" name="hdd_estado_prog_<?php echo($i); ?>" value="<?php echo($estado_aux["id_detalle"]); ?>" />
            <input type="hidden" id="hdd_sel_estado_prog_<?php echo($i); ?>" name="hdd_sel_estado_prog_<?php echo($i); ?>" />
            <?php
				}
			?>
        </form>
        <?php
			}
			break;
	}
?>
