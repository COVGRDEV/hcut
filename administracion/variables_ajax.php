<?php
session_start();
/*
  Pagina listado de perfiles, 
  Autor: Helio Ruber López - 16/09/2013
 */

header('Content-Type: text/xml; charset=UTF-8');

require_once("../db/DbVariables.php");
require_once("../funciones/Utilidades.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../principal/ContenidoHtml.php");

$variables = new Dbvariables();
$utilidades = new Utilidades();
$contenido = new ContenidoHtml();
$contenido->validar_seguridad(1);
$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);

$opcion = $_POST["opcion"];

switch ($opcion) {
    case "1": //Listado de variables
        $lista_variables = $variables->getListaVariables();
        ?>
        <table class="paginated modal_table" style="width: 95%;  margin: auto;">
        	<thead>
        	<tr><th colspan='5'>Variables</th></tr>	
        	<tr>
				<th style="width: 20%;">Variable</th>	
				<th style="width: 25%;">Descripci&oacute;n</th>
				<th style="width: 15%;">Tipo</th>
				<th style="width: 30%;">Valor(es)</th>
			</tr>
			 </thead>
			<?php
			foreach ($lista_variables as $variable_aux) {
				$tipo_variable_txt = "Otro";
				switch ($variable_aux["tipo_variable"]) {
					case "1":
						$tipo_variable_txt = "Un valor";
						break;
					case "2":
						$tipo_variable_txt = "Multivaluada";
						break;
				}
				?>
				<tr onclick='cargar_formulario_editar(<?php echo($variable_aux["id_variable"]); ?>);' style="cursor: pointer;">
					<td align="left"><?php echo($variable_aux["nombre_variable"]); ?></td>
					<td align="left"><?php echo($variable_aux["descripcion_variable"]); ?></td>
					<td align="center"><?php echo($tipo_variable_txt); ?></td>
					<td align="left"><?php echo(str_replace(";", "&nbsp;&nbsp;&nbsp; - &nbsp;&nbsp;&nbsp;", $variable_aux["valor_variable"])); ?></td>
				</tr>
				<?php	
			}
			?>
		</table>
        <br/>
		<script id='ajax'>
			//<![CDATA[ 
			$(function() {
			    $('.paginated', 'table').each(function(i) {
			        $(this).text(i + 1);
			    });
			
			    $('table.paginated').each(function() {
			        var currentPage = 0;
			        var numPerPage = 10;
			        var $table = $(this);
			        $table.bind('repaginate', function() {
			            $table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
			        });
			        $table.trigger('repaginate');
			        var numRows = $table.find('tbody tr').length;
			        var numPages = Math.ceil(numRows / numPerPage);

			        var $pager = $('<div class="pager"></div>');
			        for (var page = 0; page < numPages; page++) {
			            $('<span class="page-number"></span>').text(page + 1).bind('click', {
			                newPage: page
			            }, function(event) {
			                currentPage = event.data['newPage'];
			                $table.trigger('repaginate');
			                $(this).addClass('active').siblings().removeClass('active');
			            }).appendTo($pager).addClass('clickable');
			        }
			        $pager.insertBefore($table).find('span.page-number:first').addClass('active');
			    });
			});
			//]]>
		</script>
    	<?php
    	break;
		
    case "2": //Formulario de actialización de variables
		@$id_variable = $_POST["id_variable"];
		
		$combo = new Combo_Box();
		
		$variable_obj = $variables->getVariable($id_variable);
		
		$arr_variable = explode(";", $variable_obj["valor_variable"]);
		
    ?>
	<form id="frm_variables" name="frm_variables" method="post">
    	<input type="hidden" value="0" name="hdd_exito" id="hdd_exito" />
        <input type="hidden" name="hdd_id_variable" id="hdd_id_variable" value="<?php echo($variable_obj["id_variable"]); ?>" />
        <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:95%;">
        	<tr>
            	<th colspan="4" align="center"><h3>Modificar variable</h3></th>
            </tr>
            <tr><td>&nbsp;</td></tr>
            <tr valign="top">
            	<td align="right" style="width:15%;">
                	<label class="inline" for="txt_nombre_variable">Nombre de la variable</label>	
                </td>
                <td align="left" style="width:35%;">
                	<input type="text" class="input required" value="<?php echo($variable_obj["nombre_variable"]); ?>" name="txt_nombre_variable" id="txt_nombre_variable" onblur="trim_cadena(this);" />
                </td>
            	<td align="right" style="width:15%;">
                	<label class="inline" for="txt_descripcion_variable">Descripci&oacute;n</label>	
                </td>
                <td align="left" style="width:35%;">
                	<input type="text" class="input required" value="<?php echo($variable_obj["descripcion_variable"]); ?>" name="txt_descripcion_variable" id="txt_descripcion_variable" onblur="trim_cadena(this);" />
                    </td>
                </tr>
                <tr valign="top">
                    <td align="right">
                        <label class="inline" for="cmb_tipo_variable">Tipo</label>	
                    </td>
                    <td align="left">
                        <select name="cmb_tipo_variable" id="cmb_tipo_variable" style="width:200px;" onchange="seleccionar_tipo_variable(this.value);">
                        	<option value="">--Seleccione--</option>
                            <option value="1"<?php if ($variable_obj["tipo_variable"] == 1) { ?> selected="selected"<?php } ?>>Un valor</option>
                            <option value="2"<?php if ($variable_obj["tipo_variable"] == 2) { ?> selected="selected"<?php } ?>>Multivaluada</option>
                        </select>
                    </td>
                    <td align="right">
                        <label class="inline" for="cmb_cantidad_variable">Cantidad</label>	
                    </td>
	                <td align="left">
                        <select name="cmb_cantidad_variable" id="cmb_cantidad_variable" style="width:150px;"<?php if ($variable_obj["tipo_variable"] != "2") { ?> disabled="disabled"<?php } ?> onchange="seleccionar_cantidad_variables(this.value);">
                        	<option value="">--Seleccione--</option>
                            <?php
								for ($k = 1; $k <= 20; $k++) {
							?>
                            <option value="<?php echo($k); ?>"<?php if ($k == count($arr_variable)) { ?> selected="selected"<?php } ?>><?php echo($k); ?></option>
                            <?php
								}
							?>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <td align="right">
                        <label class="inline" for="cmb_cantidad_variable">Valor(es)</label>
                    </td>
	                <td align="left" colspan="3">
                        <?php
                            for ($k = 0; $k < 20; $k++) {
                            	$valor_aux = "";
								$tamanio = "150px";
								if(count($arr_variable) == 1 && $k == 0){
									$tamanio = "800px";
								}
								
								
								
								if (isset($arr_variable[$k])) {
									$valor_aux = $arr_variable[$k];
								}
								$visible_aux = "";
								if ($k >= count($arr_variable)) {
									$visible_aux = " display: none;";
								}
								
								
								
						?>
                        <input type="text" name="txt_valor_variable_<?php echo($k); ?>" id="txt_valor_variable_<?php echo($k); ?>" value="<?php echo($valor_aux); ?>" class="txt_no_block" style="width: <?php echo($tamanio); ?>;<?php echo($visible_aux); ?>" />
                        &nbsp;&nbsp;
                        <?php
							}
						?>
                    </td>
                </tr>    
                <tr valign="top">
	                <td align="center" colspan="4">
	                	<input type="hidden"  id="hdd_idmenus" nombre="hdd_idmenus" value="<?php echo $ids_menus;?>" />
                        <?php
                        	if ($tipo_acceso_menu == 2) {
						?>
	                	<input type="submit" id="btn_editar_perfil" nombre="btn_editar_perfil" value="Guardar" class="btnPrincipal" onclick='validar_editar_variable();'/>
                        <?php
							}
						?>
	                	<input type="button" id="btn_cancelar" nombre="btn_cancelar" value="Cancelar" class="btnSecundario" onclick="cargar_variables();" />
	                </td>
                </tr>
            </table>
            <br />
            </form>
        <?php
        break;
		
	case "3": //Editar variable
        $id_usuario = $_SESSION["idUsuario"];
		$id_variable = $_POST["id_variable"];
		$nombre_variable = trim(str_replace("'", "", $_POST["nombre_variable"]));
		$descripcion_variable = trim(str_replace("'", "", $_POST["descripcion_variable"]));
		$tipo_variable = $_POST["tipo_variable"];
		$cantidad_variable = intval($_POST["cantidad_variable"], 10);
		
		$valor_variable = "";
		for ($i = 0; $i < $cantidad_variable; $i++) {
			if ($valor_variable != "") {
				$valor_variable .= ";";
			}
			$valor_variable .= urldecode($_POST["valor_variable_".$i]);
		}
		
		$valor_exito = $variables->actualizar_variable($id_variable, $nombre_variable, $descripcion_variable, $tipo_variable, $valor_variable, $id_usuario);
	?>
    <input type="hidden" name="hdd_exito" id="hdd_exito" value="<?php echo($valor_exito); ?>" />
	<?php
		break;
}
?>
