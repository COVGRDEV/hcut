<?php
session_start();
/*
  Pagina listado de perfiles, 
  Autor: Helio Ruber López - 16/09/2013
 */

header('Content-Type: text/xml; charset=UTF-8');

require_once("../db/DbAntecedentes.php");
require_once("../funciones/Utilidades.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../principal/ContenidoHtml.php");

$antecedentes = new DbAntecedentes();
$utilidades = new Utilidades();
$contenido = new ContenidoHtml();
$contenido->validar_seguridad(1);
$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);

$opcion = $_POST["opcion"];

switch ($opcion) {
    case "1": //Listado de antecedentes
        $lista_antecedentes = $antecedentes->get_lista_antecedentes("t");
        ?>
        <table class="paginated modal_table" style="width: 70%;  margin: auto;">
        	<thead>
        	<tr><th colspan='5'>Antecedentes</th></tr>	
        	<tr>
				<th style="width: 10%;">Orden</th>	
				<th style="width: 45%;">Nombre</th>	
				<th style="width: 15%;">Tipo</th>
				<th style="width: 15%;">Por defecto</th>
				<th style="width: 15%;">Estado</th>
			</tr>
			 </thead>
			<?php
				foreach ($lista_antecedentes as $antecedente_aux) {
					$tipo_variable_txt = "Otro";
					switch ($antecedente_aux["tipo_antecedente"]) {
						case "m":
							$tipo_antecedente_txt = "M&eacute;dico";
							break;
						case "o":
							$tipo_antecedente_txt = "Otro";
							break;
					}
					
					$inicial = '&nbsp;';
					$class_inicial = '';
					if ($antecedente_aux["ind_inicial"] == 1) {
						$inicial = 'Si';
						$class_inicial = 'activo';
					} else if ($antecedente_aux["ind_inicial"] == 0) {
						$inicial = 'No';
						$class_inicial = 'inactivo';
					}
					
					$activo = '&nbsp;';
					$class_activo = '';
					if ($antecedente_aux["ind_activo"] == 1) {
						$activo = 'Activo';
						$class_activo = 'activo';
					} else if ($antecedente_aux["ind_activo"] == 0) {
						$activo = 'No activo';
						$class_activo = 'inactivo';
					}
				?>
				<tr onclick="cargar_formulario_editar(<?php echo($antecedente_aux["id_antecedente"]); ?>, '<?php echo($antecedente_aux["tipo_antecedente"]); ?>');" style="cursor: pointer;">
					<td align="center"><?php echo($antecedente_aux["orden"]); ?></td>
					<td align="left"><?php echo($antecedente_aux["nombre_antecedente"]); ?></td>
					<td align="center"><?php echo($tipo_antecedente_txt); ?></td>
					<td align="center"><span class="<?php echo($class_inicial); ?>"><?php echo($inicial); ?></span></td>
					<td align="center"><span class="<?php echo($class_activo); ?>"><?php echo($activo); ?></span></td>
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
		
    case "2": //Formulario de actialización de antecedentes
		@$id_antecedente = $_POST["id_antecedente"];
		@$tipo_antecedente = $_POST["tipo_antecedente"];
		
		$combo = new Combo_Box();
		
		$antecedente_obj = $antecedentes->get_antecedente($id_antecedente, $tipo_antecedente);
    ?>
	<form id="frm_antecedentes" name="frm_antecedentes" method="post">
    	<input type="hidden" value="0" name="hdd_exito" id="hdd_exito" />
        <input type="hidden" name="hdd_id_antecedente" id="hdd_id_antecedente" value="<?php echo($id_antecedente); ?>" />
        <input type="hidden" name="hdd_tipo_antecedente" id="hdd_tipo_antecedente" value="<?php echo($tipo_antecedente); ?>" />
        <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:95%;">
        	<tr>
            	<th colspan="4" align="center"><h3>Modificar antecedente</h3></th>
            </tr>
            <tr><td>&nbsp;</td></tr>
            <tr valign="top">
            	<td align="right" style="width:20%;">
                	<label class="inline" for="txt_nombre_antecedente">Nombre del antecedente</label>	
                </td>
                <td align="left" style="width:30%;">
                	<input type="text" class="input required" value="<?php echo($antecedente_obj["nombre_antecedente"]); ?>" name="txt_nombre_antecedente" id="txt_nombre_antecedente" onblur="trim_cadena(this);" />
                </td>
            	<td align="right" style="width:20%;">
                	<label class="inline" for="txt_ind_inicial">Tipo</label>	
                </td>
                <td align="left" style="width:30%;">
                	<?php
                    	$tipo_antecedente_txt = "";
						switch ($tipo_antecedente) {
							case "m":
								$tipo_antecedente_txt = "M&eacute;dico";
								break;
							case "o":
								$tipo_antecedente_txt = "Otro";
								break;
						}
					?>
                    <label class="inline"><b><?php echo($tipo_antecedente_txt); ?></b></label>
                </td>
            </tr>
            <tr valign="top">
                <td align="right">
                    <label class="inline" for="chk_inicial">Orden</label>	
                </td>
                <td align="left">
                    <input type="text" class="input required" value="<?php echo($antecedente_obj["orden"]); ?>" name="txt_orden" id="txt_orden" maxlength="3" onkeypress="return solo_numeros(event, false);" style="width:50px;" />
                </td>
                <td align="right">
                    <label class="inline" for="chk_inicial">Por defecto</label>	
                </td>
                <td align="left">
                	<?php
                    	$checked_aux = "";
						if ($antecedente_obj["ind_inicial"] == 1) {
							$checked_aux = " checked=\"checked\"";
						}
					?>
                	<label class="inline">
	                    <input type="checkbox" name="chk_inicial" id="chk_inicial"<?php echo($checked_aux); ?> />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <td align="right">
                    <label class="inline" for="chk_activo">Activo</label>	
                </td>
	            <td align="left">
                	<?php
                    	$checked_aux = "";
						if ($antecedente_obj["ind_activo"] == 1) {
							$checked_aux = " checked=\"checked\"";
						}
					?>
                	<label class="inline">
	                    <input type="checkbox" name="chk_activo" id="chk_activo"<?php echo($checked_aux); ?> />
                    </label>
                </td>
            </tr>
            <tr valign="top">
	            <td align="center" colspan="4">
                    <?php
                       	if ($tipo_acceso_menu == 2) {
					?>
	               	<input type="submit" id="btn_editar_antecedente" nombre="btn_editar_antecedente" value="Guardar" class="btnPrincipal" onclick='validar_editar_antecedente();'/>
                    <?php
						}
					?>
	               	<input type="button" id="btn_cancelar" nombre="btn_cancelar" value="Cancelar" class="btnSecundario" onclick="cargar_antecedentes();" />
	            </td>
            </tr>
        </table>
        <br />
    </form>
    <?php
        break;
		
	case "3": //Editar antecedente
        $id_usuario = $_SESSION["idUsuario"];
		$id_antecedente = $utilidades->str_decode($_POST["id_antecedente"]);
		$nombre_antecedente = $utilidades->str_decode($_POST["nombre_antecedente"]);
		$orden = $utilidades->str_decode($_POST["orden"]);
		$tipo_antecedente = $utilidades->str_decode($_POST["tipo_antecedente"]);
		$ind_inicial = $utilidades->str_decode($_POST["ind_inicial"]);
		$ind_activo = $utilidades->str_decode($_POST["ind_activo"]);
		
		$valor_exito = $antecedentes->actualizar_antecedente($id_antecedente, $tipo_antecedente, $nombre_antecedente, $orden, $ind_inicial, $ind_activo, $id_usuario);
	?>
    <input type="hidden" name="hdd_exito" id="hdd_exito" value="<?php echo($valor_exito); ?>" />
	<?php
		break;
		
    case "4": //Formulario de creación de antecedentes
		$combo = new Combo_Box();
    ?>
	<form id="frm_antecedentes" name="frm_antecedentes" method="post">
    	<input type="hidden" value="0" name="hdd_exito" id="hdd_exito" />
        <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:95%;">
        	<tr>
            	<th colspan="4" align="center"><h3>Crear antecedente</h3></th>
            </tr>
            <tr><td>&nbsp;</td></tr>
            <tr valign="top">
            	<td align="right" style="width:20%;">
                	<label class="inline" for="txt_nombre_antecedente">Nombre del antecedente</label>	
                </td>
                <td align="left" style="width:30%;">
                	<input type="text" class="input required" value="" name="txt_nombre_antecedente" id="txt_nombre_antecedente" onblur="trim_cadena(this);" />
                </td>
            	<td align="right" style="width:20%;">
                	<label class="inline" for="txt_ind_inicial">Tipo</label>	
                </td>
                <td align="left" style="width:30%;">
                	<select name="cmb_tipo_antecedente" id="cmb_tipo_antecedente" style="width:150px;">
                    	<option value="-1">--Seleccione--</option>
                    	<option value="m">M&eacute;dico</option>
                    	<option value="o">Otro</option>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <td align="right">
                    <label class="inline" for="chk_inicial">Orden</label>	
                </td>
                <td align="left">
                    <input type="text" class="input required" value="<?php echo($antecedente_obj["orden"]); ?>" name="txt_orden" id="txt_orden" maxlength="3" onkeypress="return solo_numeros(event, false);" style="width:50px;" />
                </td>
                <td align="right">
                    <label class="inline" for="chk_inicial">Por defecto</label>	
                </td>
                <td align="left">
                	<label class="inline">
	                    <input type="checkbox" name="chk_inicial" id="chk_inicial" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <td align="right">
                    <label class="inline" for="chk_activo">Activo</label>	
                </td>
	            <td align="left">
                	<label class="inline">
	                    <input type="checkbox" name="chk_activo" id="chk_activo" checked="checked" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
	            <td align="center" colspan="4">
                    <?php
                       	if ($tipo_acceso_menu == 2) {
					?>
	               	<input type="submit" id="btn_crear_antecedente" nombre="btn_crear_antecedente" value="Crear" class="btnPrincipal" onclick='validar_crear_antecedente();'/>
                    <?php
						}
					?>
	               	<input type="button" id="btn_cancelar" nombre="btn_cancelar" value="Cancelar" class="btnSecundario" onclick="cargar_antecedentes();" />
	            </td>
            </tr>
        </table>
        <br />
    </form>
    <?php
        break;
		
	case "5": //Crear antecedente
        $id_usuario = $_SESSION["idUsuario"];
		$tipo_antecedente = $utilidades->str_decode($_POST["tipo_antecedente"]);
		$nombre_antecedente = $utilidades->str_decode($_POST["nombre_antecedente"]);
		$orden = $utilidades->str_decode($_POST["orden"]);
		$ind_inicial = $utilidades->str_decode($_POST["ind_inicial"]);
		$ind_activo = $utilidades->str_decode($_POST["ind_activo"]);
		
		$valor_exito = $antecedentes->actualizar_antecedente(0,$tipo_antecedente, $nombre_antecedente, $orden, $ind_inicial, $ind_activo, $id_usuario);
	?>
    <input type="hidden" name="hdd_exito" id="hdd_exito" value="<?php echo($valor_exito); ?>" />
	<?php
		break;
}
?>
