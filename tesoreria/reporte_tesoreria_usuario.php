<?php
session_start();

require_once("../db/DbVariables.php");
require_once("../principal/ContenidoHtml.php");
require_once '../funciones/Class_Combo_Box.php';
require_once("../db/DbConvenios.php");
require_once("../db/DbMaestroProcedimientos.php");
require_once("../db/DbUsuarios.php");

$variables = new DbVariables();
$contenido = new ContenidoHtml();
$combo = new Combo_Box();
$convenios = new DbConvenios();
$maestroProcedimientos = new DbMaestroProcedimientos();
$usuarios = new DbUsuarios();


//variables
$titulo = $variables->getVariable(1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $titulo['valor_variable']; ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
        <link href="../css/azul.css" rel="stylesheet" type="text/css" />
        <link href="../css/foundation-datepicker.css" rel="stylesheet" type="text/css" />
		<link href="http://netdna.bootstrapcdn.com/font-awesome/3.0.2/css/font-awesome.css" rel="stylesheet">
        
        <script type='text/javascript' src='../js/jquery.min.js'></script>
        <script type="text/javascript" src="../js/jquery-ui.custom.js"></script>
        <script type="text/javascript" src="../js/jquery.cookie.js"></script>
        <script type='text/javascript' src='../js/jquery.validate.js'></script>
        <script type='text/javascript' src='../js/jquery.validate.add.js'></script>
        <script type='text/javascript' src='../js/ajax.js'></script>
        <script type='text/javascript' src='../js/funciones.js'></script>

        <script type='text/javascript' src='../js/validaFecha.js'></script>
        <script type='text/javascript' src='../js/foundation-datepicker.js'></script>
        <script type="text/javascript" src="../js/jquery.maskedinput.js"></script>

        <script type='text/javascript' src='reporte_tesoreria_usuario_v1.1.js'></script>

        <link href="../src/skin-vista/ui.dynatree.css" rel="stylesheet" type="text/css" >
        <script src="../src/jquery.dynatree.js" type="text/javascript"></script>
    </head>
    <body>
        <?php
        $contenido->validar_seguridad(0);
        $contenido->cabecera_html();
		
		//Se obtiene el listado de convenios
		$lista_convenios = $convenios->getConvenios()
        ?>
        <div class="title-bar">
            <div class="wrapper">
                <div class="breadcrumb">
                    <ul>
                        <li class="breadcrumb_on">Reportes de tesorer&iacute;a - Usuario</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="contenedor_principal volumen">
            <div class="padding">     
                <fieldset style="text-align: left;">
                    <legend>Reporte General de Tesorer&iacute;a</legend>
                    <div>
                        <table border="0" cellpadding="5" cellspacing="0" style="width:100%;">
                            <tr>
                                <td align="right" style="width:15%;">
                                	<label class="inline">Fecha</label>
                                </td>
                                <td style="width:20%;">
                                    <input type="text" class="input" maxlength="10" style="width:120px;" name="txt_fecha" id="txt_fecha" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                                </td>
                                <td align="right" style="width:10%;">
                                	<label class="inline">Convenio</label>
                                </td>
                                <td style="width:35%;">
                                    <?php
										$combo->getComboDb("cmb_convenio", '', $lista_convenios, "id_convenio, nombre_convenio", "--Todos los convenios--", "", "", "width:100%;");
									?>
                                </td>
                                <td align="center" style="width:20%;">
                                    <div id="d_reporte" style="display:none;"></div>
                                    <input type="button" value="Generar PDF" class="btnPrincipal" onclick="reporteGeneral();" /><br/>
                                </td>
                            </tr>
                        </table>
                    </div>
                </fieldset>
            </div>
        </div>
        <script type='text/javascript' src='../js/foundation.min.js'></script>
        <script>
			$(document).foundation();
				$(function() {
					window.prettyPrint && prettyPrint();
					
					$('#txt_fecha').fdatepicker({
						format: 'dd/mm/yyyy'
					});
				});
        </script>
        <div id="fondo_negro_pacientes" class="d_fondo_negro"></div>
        <div class="div_centro" id="d_centro_pacientes" style="display:none;">
            <a name="a_cierre_panel" id="a_cierre_panel" href="#" onclick="ventanaPacientes(0);"></a>
            <div class="div_interno" id="d_interno_pacientes"></div>
        </div>
        <div id="fondo_negro_conceptos" class="d_fondo_negro"></div>
        <div class="div_centro" id="d_centro_conceptos" style="display:none;">
            <a name="a_cierre_panel" id="a_cierre_panel" href="#" onclick="mostrar_formulario_conceptos(0);"></a>
            <div class="div_interno" id="d_interno_conceptos"></div>
        </div>
        <?php
        $contenido->footer();
        ?>  
    </body>
</html>