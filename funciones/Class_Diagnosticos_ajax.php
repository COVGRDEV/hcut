<?php
	session_start();
	//Autor: Helio Ruber López - 17/02/2014
	
	header("Content-Type: text/xml; charset=UTF-8");
	
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbDiagnosticos.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/Utilidades.php");
	require_once("../principal/ContenidoHtml.php");
	
	$dbDiagnosticos = new DbDiagnosticos();
	
	$utilidades = new Utilidades();
	$contenido = new ContenidoHtml();
	$contenido->validar_seguridad(1);
	$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);
	
	$opcion = $_POST["opcion"];
	
	switch ($opcion) {
		case "1": //Opcion para buscar diagnosticos
			$num = $utilidades->str_decode($_POST["num"]);
			$txt_busca_diagnostico = $utilidades->str_decode($_POST["txt_busca_diagnostico"]);
			$tabla_diagnosticos = $dbDiagnosticos->getListaDiagnosticosDetParam($txt_busca_diagnostico);
        ?>
        <br />
        <table class="paginated modal_table" style="width: 80%; margin: auto;">
        	<thead>
            	<tr><th colspan="2">Diagnosticos CIEX</th></tr>
                <tr>
                    <th style="width:5%;">C&oacute;digo CIEX</th>
                    <th style="width:36%;">Nombre</th>
                </tr>
            </thead>
            <?php
				if (count($tabla_diagnosticos) > 0) {
					foreach ($tabla_diagnosticos as $fila_ciex) {
						@$cod_ciex = $fila_ciex["codciex"];
						@$nombre_ciex = $fila_ciex["nombre"];
            ?>
            <tr style="cursor:pointer;" onclick="seleccionar_diagnostico_ciex('<?php echo $cod_ciex; ?>', '<?php echo $nombre_ciex; ?>', '<?php echo $num; ?>');">
            	<td align="center"><?php echo $cod_ciex; ?></td>
                <td align="left"><?php echo $nombre_ciex; ?></td>
            </tr>
            <?php
					}
				} else {
			?>
            <tr>
            	<td align="center" colspan="2">No se encontraron datos</td>
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
			
		case "2": //Buscar diagnosticos por ciex
			$cod_ciex = $utilidades->str_decode($_POST["cod_ciex"]);
			$num = $utilidades->str_decode($_POST["num"]);
			
			$tabla_diagnostico = $dbDiagnosticos->getDiagnosticoCiexDet($cod_ciex);
			$texto_ciex = $tabla_diagnostico["nombre"];
			$cod_ciex = $tabla_diagnostico["codciex"];
			
			if ($cod_ciex == "") {
				$texto_ciex="C&oacute;digo CIEX incorrecto";
			}
		?>
		<input type="hidden" value="<?php echo($texto_ciex);?>" name="hdd_texto_ciex_<?php echo($num);?>" id="hdd_texto_ciex_<?php echo($num);?>" />
		<input type="hidden" value="<?php echo($cod_ciex);?>" name="hdd_codigo_ciex_<?php echo($num);?>" id="hdd_codigo_ciex_<?php echo($num);?>" />
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
