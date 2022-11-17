<?php
header('Content-Type: text/xml; charset=UTF-8');
session_start();

require_once("../db/DbFormulasPredef.php");
require_once("../funciones/Utilidades.php");

$formulas = new DbFormulasPredef();
$utilidades = new Utilidades();

$opcion = $_POST["opcion"];

switch ($opcion) {
	case "1": //Carga el listado de la tabla: datos_entidad
		$rta_aux = $formulas->getFormulas();
	?>
    <table class="paginated modal_table" style="width: 99%; margin: auto;">
        <thead>
            <tr>
                <th align="center" style="width:8%;">C&oacute;digo</th>
                <th align="center" style="width:25%;">F&oacute;rmula</th>
                <th align="center" style="width:55%;">Texto</th>
                <th align="center" style="width:12%;">Estado</th>
            </tr>
        </thead>
        <?php
			if (count($rta_aux) >= 1) {
				foreach ($rta_aux as $value) {
					$estado = $value['ind_activo'];
					if ($estado == 1) {
						$estado = 'Activo';
						$class_estado = 'activo';
					} else if ($estado == 0) {
						$estado = 'No Activo';
						$class_estado = 'inactivo';
					}
					$texto_formula = substr(strip_tags($value['text_formulas']), 0, 200) . "...";
		?>
        <tr onclick="ventanaModificar(<?php echo($value['id_formula']); ?>)">
            <td align="center"><?php echo $value['id_formula']; ?></td>
            <td align="left"><?php echo($value["titulo_formula"]); ?></td>
            <td align="left"><?php echo($texto_formula); ?></td>
			<td align="center"><span class="<?php echo $class_estado; ?>"><?php echo($estado); ?></span></td>
        </tr>
        <?php
				}
			} else {
        ?>
        <td colspan="4">No hay resultados</td>
        <?php
            }
        ?>
    </table>
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
		
	case "2": //Muestra el resultado de la búsqueda
		$parametro = $utilidades->str_decode($_POST['parametro']);
		
		$rta_aux = $formulas->buscarFormulas($parametro);
	?>
    <table class="paginated modal_table" style="width: 99%; margin: auto;">
    	<thead>
        	<tr>
                <th align="center" style="width:8%;">C&oacute;digo</th>
                <th align="center" style="width:25%;">F&oacute;rmula</th>
                <th align="center" style="width:55%;">Texto</th>
                <th align="center" style="width:12%;">Estado</th>
            </tr>
        </thead>
        <?php
			if (count($rta_aux) >= 1) {
				foreach ($rta_aux as $value) {
					$estado = $value['ind_activo'];
					if ($estado == 1) {
						$estado = 'Activo';
						$class_estado = 'activo';
					} else if ($estado == 0) {
						$estado = 'No Activo';
						$class_estado = 'inactivo';
					}
					
					$texto_formula = substr(strip_tags($value['text_formulas']), 0, 200) . "...";
		?>
        <tr onclick="ventanaModificar(<?php echo $value['id_formula']; ?>)">
            <td align="center"><?php echo $value['id_formula']; ?></td>
            <td align="left"><?php echo($value["titulo_formula"]); ?></td>
            <td align="left"><?php echo($texto_formula); ?></td>
			<td align="center"><span class="<?php echo $class_estado; ?>"><?php echo($estado); ?></span></td>
        </tr>
        <?php
				}
			} else {
		?>
        <td colspan="4">No hay resultados</td>
        <?php
			}
		?>
    </table>
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
		
	case "3": //Muestra ventana editar
        $idFormula = $utilidades->str_decode($_POST['idFormula']);
		
        $rta_aux = $formulas->buscarFormula($idFormula);
		
        $indEco = '';
        if ($rta_aux['ind_activo'] == '1') {
            $indEco = 'checked';
        } else {
            $indEco = '';
        }
        ?>
        <div class="encabezado">
            <h3>Editar Plantilla</h3>
        </div>
        <br/>
        <br/>
        <div class='contenedor_error' id='contenedor_error'></div>
        <table border="0" style="width:100%;">
            <tr valign="top">
                <td align="right" style="width:60px;">
                    <label>Codigo:&nbsp;</label>
                </td>
                <td align="left">
                    <label><b><?php echo $rta_aux['id_formula']; ?></b></label>
                    <input type="hidden" name="hddidFormula" id="hddidFormula" value="<?php echo $rta_aux['id_formula']; ?>" />
                </td>
                <td align="right" style="width:60px;">
                    <label>Activo:&nbsp;</label>
                </td>
                <td align="left" style="width:60px;">
                	<label>
	                    <input type="checkbox" name="indEstado" id="indEstado" <?php echo $indEco; ?> value="<?php echo $indEco; ?>" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
            	<td align="right">
                	<label class="inline">T&iacute;tulo:&nbsp;</label>
                </td>
                <td align="left" colspan="3">
	                	<input type="text" name="txt_titulo_formula" id="txt_titulo_formula" value="<?php echo($rta_aux["titulo_formula"]); ?>" class="input" style="width:500px;" maxlength="200" />
                </td>
            </tr>
            <tr>
                <td colspan="4">
                	<?php
						$text_formulas = $utilidades->ajustar_texto_wysiwyg($rta_aux['text_formulas']);
					?>
                    <div id="text_formula"><?php echo($text_formulas); ?></div>
                    <!--<textarea style="height: 300px; width: 100%;" id="text_formula" nombre="text_formula" ><?php echo $rta_aux['text_formulas']; ?></textarea>-->
                </td>
            </tr>               
        </table>
        <br/>
        <br/>
        <table style="width:100%;">
        	<tr>
                <td align="center">
                    <input type="button" id="btnGuardar" name="btnGuardar" value="Modificar" class="btnPrincipal" onclick="guardarPlantilla(2);" />
                    <input type="hidden" id="hddResultado" name="hddResultado" />
                </td>
            </tr>
        </table>
        <script id="ajax">
			initCKEditorFormulas("text_formula");
		</script>
        <?php
        break;
		
    case "4": //Guarda registro
		$idUsuario = $_SESSION["idUsuario"];
        @$accion = $utilidades->str_decode($_POST["accion"]);
        @$indActivo = $utilidades->str_decode($_POST["indEstado"]);
		@$tituloFormula = $utilidades->str_decode(trim($_POST["titulo_formula"]));
        @$textFormula = $utilidades->str_decode(str_replace('"', '', $_POST["text_formula"]));
		@$idFormula = $utilidades->str_decode($_POST["idFormula"]);
		
        $rta_aux = $formulas->guardarModificarFormula($accion, $idFormula, $tituloFormula, $textFormula, $indActivo, $idUsuario);
		echo $rta_aux;
        break;
		
    case "5": //Muestra ventana crear
        ?>
        <div class="encabezado">
            <h3>Crear Plantilla</h3>
        </div>
        <br/>
        <br/>
        <div class='contenedor_error' id='contenedor_error'></div>
        <div style="width: 800px;margin: 0 auto;">
            <table style="width: 100%;">
                <tr valign="top">
                    <td align="right" style="width:60px;">
                        <label class="inline">T&iacute;tulo:&nbsp;</label>
                    </td>
                    <td align="left">
                        <input type="text" name="txt_titulo_formula" id="txt_titulo_formula" value="" class="input" style="width:500px;" maxlength="200" />
                    </td>
                    <td align="right" style="width:60px;">
                        <label>Activo:&nbsp;</label>
                    </td>
                    <td align="left" style="width:60px;">
                        <label>
                            <input type="checkbox" name="indEstado" id="indEstado" value="" checked="checked" />
                        </label>
                        <input type="hidden" id="hddidFormula" name="hddidFormula" value="0" />
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                    	<div id="text_formula"></div>
                        <!--<textarea class="textarea_oftalmo" style="text-align:left; height:300px; width:100%;" id="text_formula" nombre="text_formula"></textarea>-->
                    </td>
                </tr>               
            </table>
            <br/>
            <br/>
            <table style="width: 100%;" border='0'>
                <tr>
                    <!--<td align="center">
                        <input type="button" id="btnGuardar" name="btnGuardar" value="Imprimir" class="btnPrincipal" />
                    </td>-->
                    <td align="center">
                        <input type="button" id="btnGuardar" name="btnGuardar" value="Guardar" class="btnPrincipal" onclick="guardarPlantilla(1);" />
                    </td>
                </tr>
                <tr>
                	<td colspan="2">
                        <input type="hidden" id="hddResultado" name="hddResultado" />
                    </td>
                </tr>
            </table>
        </div>
        <script id="ajax">
			initCKEditorFormulas("text_formula");
		</script>
        <?php
        break;
		
    case "6": //Funcion que procesa el tipo de impresion
        $tipoImpresion = $_POST['tipoImpresion'];
        $idFormula = $_POST['idFormula'];
		$text_despacho = $_POST['text_despacho'];
		
		if ($tipoImpresion == '1') { //Consultorio
            ?>
	        <div id='campo_imprimir' style="display:none;">
	        	<center>
    			<table style='width:83%;margin-top:12%;' border='0'>
				<tr>
					<td>&nbsp;&nbsp;</td>
					<td><?php //echo ($hdd_nombre_paciente);?></td>
					<td align="right"><?php //echo ($hdd_fecha_admision);?></td>
				</tr>
				</table>
				</center>
				<table style='width: 100%;' border='0'>
				<tr>
					<td colspan='4'>Documento: <?php echo (/*$hdd_documento_paciente.*/'<br />'.$text_despacho);?></td>
				</tr>
				
    			</table>
    		</div>
    		<?php
        }else if($tipoImpresion == '2'){//Ecopetrol
            ?>
			<div id='campo_imprimir' style="display:none;">
	        	<center>
    			<table style='width:100%; margin-top: -5px;' border='0'>
				<tr>
					<td>&nbsp;&nbsp;</td>
					<td><?php //echo ($hdd_nombre_paciente);?></td>
					<td>&nbsp;&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;</td>
					<td><?php //echo ($hdd_nombre_profesional);?></td>
					<td align="right" style="margin-right: -5px;"><?php //echo ($hdd_fecha_admision);?></td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;</td>
					<td><?php //echo ($text_remitido);?></td>
					<td>&nbsp;&nbsp;</td>
				</tr>
				</table>
				</center>
				<table style='width: 98%;' border='0'>
				<tr>
					<td colspan='4'>&nbsp;&nbsp;</td>
				</tr>
				<tr>
					<td colspan='4'>&nbsp;&nbsp;</td>
				</tr>	
				<tr>
					<td colspan='4'>Documento: <?php echo (/*$hdd_documento_paciente.*/'<br />'.$text_despacho);?></td>
				</tr>
    			</table>
    		</div>
	        <?php
        }
        break;
}
?>
