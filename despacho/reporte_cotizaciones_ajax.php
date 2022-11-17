<?php
	session_start();
	/*
	  Autor: Feisar Moreno - 14/12/2015
	 */
	
	header("Content-Type: text/xml; charset=UTF-8");
	
	require_once("../db/DbDespacho.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/Utilidades.php");
	
	$dbDespacho = new DbDespacho();
	
	$contenido = new ContenidoHtml();
	$contenido->validar_seguridad(1);
	$funciones_persona = new FuncionesPersona();
	$utilidades = new Utilidades();
	
	$opcion = $utilidades->str_decode($_POST["opcion"]);
	
	switch ($opcion) {
		case "1": //Búsqueda de cotizaciones
			@$txt_paciente = $utilidades->str_decode($_POST["txt_paciente"]);
			@$fecha_ini = $utilidades->str_decode($_POST["fecha_ini"]);
			@$fecha_fin = $utilidades->str_decode($_POST["fecha_fin"]);
			@$id_proc_cotiz = $utilidades->str_decode($_POST["id_proc_cotiz"]);
			@$observaciones_cotiz = $utilidades->str_decode($_POST["observaciones_cotiz"]);
			@$id_usuario_prof = $utilidades->str_decode($_POST["id_usuario_prof"]);
			@$id_lugar_cita = $utilidades->str_decode($_POST["id_lugar_cita"]);
			
			//Se obtiene el listado de cotizaciones
			$lista_cotizaciones = $dbDespacho->getListaDespachoCotizacionesParams($txt_paciente, $id_proc_cotiz,
					$observaciones_cotiz, $fecha_ini, $fecha_fin, $id_usuario_prof, $id_lugar_cita);
			
			if (count($lista_cotizaciones) > 0) {
		?>
        <table id="tabla_persona_hc" border="0" class="paginated modal_table" align="center" style="width:99%;">
            <thead>
                <tr class="headegrid">
                    <th class="headegrid" align="center" style="width:10%;">Fecha</th>	
                    <th class="headegrid" align="center" style="width:14%;">No. Documento</th>
                    <th class="headegrid" align="center" style="width:20%;">Nombre</th>
                    <th class="headegrid" align="center" style="width:16%;">Procedimiento</th>
                    <th class="headegrid" align="center" style="width:15%;">Profesional</th>
                    <th class="headegrid" align="center" style="width:15%;">Sede</th>
                    <th class="headegrid" align="center" style="width:10%;">Valor Cotizaci&oacute;n</th>
                </tr>
            </thead>
            <?php
				foreach ($lista_cotizaciones as $cotiz_aux) {
					$nombre_completo = $funciones_persona->obtenerNombreCompleto($cotiz_aux["nombre_1"], $cotiz_aux["nombre_2"], $cotiz_aux["apellido_1"], $cotiz_aux["apellido_2"]);
			?>
            <tr class="celdagrid">
                <td align="center"><?php echo($cotiz_aux["fecha_despacho_t"]); ?></td>	
                <td align="center"><?php echo($cotiz_aux["cod_tipo_documento"]." ".$cotiz_aux["numero_documento"]); ?></td>	
                <td align="left"><?php echo($nombre_completo); ?></td>	
                <td align="left"><?php echo($cotiz_aux["nombre_proc_cotiz"]); ?></td>	
                <td align="left"><?php echo($cotiz_aux["nombre_usuario"]." ".$cotiz_aux["apellido_usuario"]); ?></td>
                <td align="left"><?php echo($cotiz_aux["lugar_cita"]); ?></td>
                <td align="right">$<?php echo(str_replace(",", ".", number_format($cotiz_aux["valor_cotiz"]))); ?></td>	
            </tr>
            <?php
				}
			?>
        </table>
		<script id="ajax">
			$(function() {
				$(".paginated", "tabla_persona_hc").each(function(i) {
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
		</script>
        <br />
        <input type="button" id="btn_generar_excel" name="btn_generar_excel" class="btnPrincipal" value="Generar Excel" onclick="exportar_a_excel();" />
        <form id="frm_reporte_excel" name="frm_reporte_excel" action="reporte_cotizaciones_excel.php" method="post" style="display:none;" target="_blank">
            <input type="hidden" id="hdd_opcion" name="hdd_opcion" value="1" />
            <input type="hidden" id="hdd_txt_paciente_e" name="hdd_txt_paciente_e" value="<?php echo($txt_paciente); ?>" />
            <input type="hidden" id="hdd_fecha_ini_e" name="hdd_fecha_ini_e" value="<?php echo($fecha_ini); ?>" />
            <input type="hidden" id="hdd_fecha_fin_e" name="hdd_fecha_fin_e" value="<?php echo($fecha_fin); ?>" />
            <input type="hidden" id="hdd_id_proc_cotiz_e" name="hdd_id_proc_cotiz_e" value="<?php echo($id_proc_cotiz); ?>" />
            <input type="hidden" id="hdd_id_usuario_prof_e" name="hdd_id_usuario_prof_e" value="<?php echo($id_usuario_prof); ?>" />
            <input type="hidden" id="hdd_id_lugar_cita_e" name="hdd_id_lugar_cita_e" value="<?php echo($id_lugar_cita); ?>" />
            <input type="hidden" id="hdd_observaciones_cotiz_e" name="hdd_observaciones_cotiz_e" value="<?php echo($observaciones_cotiz); ?>" />
        </form>
        <?php
			} else {
		?>
	    <div class="msj-vacio">
	    	<p>No se encontraron registros de cotizaciones</p>
	    </div>
		<?php
			}
			break;
	}
?>
