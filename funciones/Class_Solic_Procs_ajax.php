<?php
	session_start();
	//Autor: Feisar Moreno - 27/02/2018
	
	header("Content-Type: text/xml; charset=UTF-8");
	
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbMaestroProcedimientos.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/Utilidades.php");
	require_once("../principal/ContenidoHtml.php");
	
	$dbMaestroProcedimientos = new DbMaestroProcedimientos();
	
	$utilidades = new Utilidades();
	$contenido = new ContenidoHtml();
	$contenido->validar_seguridad(1);
	$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);
	
	$opcion = $_POST["opcion"];
	
	switch ($opcion) {
		case "1": //Opcion para buscar diagnosticos
			$texto_busc = $utilidades->str_decode($_POST["texto_busc"]);
			$indice = $utilidades->str_decode($_POST["indice"]);
			
			$lista_procedimientos = $dbMaestroProcedimientos->getProcedimientos($texto_busc);
        ?>
        <br />
        <table class="paginated modal_table" style="width: 80%; margin: auto;">
        	<thead>
            	<tr><th colspan="2">Procedimientos CUPS</th></tr>
                <tr>
                    <th style="width:15%;">C&oacute;digo CUPS</th>
                    <th style="width:85%;">Nombre</th>
                </tr>
            </thead>
            <?php
				if (count($lista_procedimientos) > 0) {
					foreach ($lista_procedimientos as $procedimiento_aux) {
						@$cod_procedimiento = $procedimiento_aux["cod_procedimiento"];
						@$nombre_procedimiento = $procedimiento_aux["nombre_procedimiento"];
            ?>
            <tr style="cursor:pointer;" onclick="seleccionar_cups_solic('<?php echo($cod_procedimiento); ?>', '<?php echo($nombre_procedimiento); ?>', '<?php echo($indice); ?>');">
            	<td align="center"><?php echo($cod_procedimiento); ?></td>
                <td align="left"><?php echo($nombre_procedimiento); ?></td>
            </tr>
            <?php
					}
				} else {
			?>
            <tr>
            	<td align="center" colspan="2">No se encontraron procedimientos</td>
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
			break;
			
		case "2": //Buscar procedimiento por código CUPS
			$cod_procedimiento = $utilidades->str_decode($_POST["cod_procedimiento"]);
			$indice = $utilidades->str_decode($_POST["indice"]);
			
			$procedimiento_obj = $dbMaestroProcedimientos->getProcedimiento($cod_procedimiento);
			if (isset($procedimiento_obj["cod_procedimiento"])) {
				$nombre_procedimiento = $procedimiento_obj["nombre_procedimiento"];
			} else {
				$cod_procedimiento = "";
				$nombre_procedimiento="C&oacute;digo CUPS incorrecto";
			}
		?>
		<input type="hidden" id="hdd_cod_procedimiento_b_<?php echo($indice);?>" name="hdd_cod_procedimiento_b_<?php echo($indice);?>" value="<?php echo($cod_procedimiento);?>" />
		<input type="hidden" id="hdd_nombre_procedimiento_b_<?php echo($indice);?>" name="hdd_nombre_procedimiento_b_<?php echo($indice);?>" value="<?php echo($nombre_procedimiento);?>" />
		<?php
			break;
			
		case "3": //Búsqueda de diagnósticos anteriores
			$id_hc = $utilidades->str_decode($_POST["id_hc"]);
			
			//Se obtiene el listado de diagnósticos anterior
			$lista_diagnosticos = $dbDiagnosticos->getListaHCDiagnosticosAnterior($id_hc);
			
			$cant_ant_dg = count($lista_diagnosticos);
		?>
        <input type="hidden" id="hdd_cant_ant_dg" value="<?php echo($cant_ant_dg); ?>" />
        <?php
			if ($cant_ant_dg > 0) {
				for ($i = 1; $i <= $cant_ant_dg; $i++) {
					$diagnostico_aux = $lista_diagnosticos[$i - 1];
		?>
        <input type="hidden" id="hdd_id_diagnostico_ant_dg_<?php echo($i); ?>" value="<?php echo($diagnostico_aux["id_diagnostico"]); ?>" />
        <input type="hidden" id="hdd_cod_ciex_ant_dg_<?php echo($i); ?>" value="<?php echo($diagnostico_aux["cod_ciex"]); ?>" />
        <input type="hidden" id="hdd_nom_ciex_ant_dg_<?php echo($i); ?>" value="<?php echo($diagnostico_aux["nom_ciex"]); ?>" />
        <input type="hidden" id="hdd_ojo_ant_dg_<?php echo($i); ?>" value="<?php echo($diagnostico_aux["id_ojo"]); ?>" />
        <?php
				}
			}
			break;
	}
?>
