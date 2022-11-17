<?php
session_start();

require_once("../db/DbVariables.php");
require_once("../db/DbDatosEntidad.php");
require_once("../principal/ContenidoHtml.php");
require_once("../funciones/Class_Combo_Box.php");

$dbVariables = new DbVariables();
$dbDatosEntidad = new DbDatosEntidad();

$contenido = new ContenidoHtml();
$combo = new Combo_Box();


//variables
$titulo = $dbVariables->getVariable(1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $titulo["valor_variable"]; ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
        <link href="../css/azul.css" rel="stylesheet" type="text/css" />
        <link href="../css/foundation-datepicker.css" rel="stylesheet" type="text/css" />
		<link href="http://netdna.bootstrapcdn.com/font-awesome/3.0.2/css/font-awesome.css" rel="stylesheet">
        
        <script type="text/javascript" src="../js/jquery.min.js"></script>
        <script type="text/javascript" src="../js/jquery-ui.custom.js"></script>
        <script type="text/javascript" src="../js/jquery.cookie.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.add.js"></script>
        <script type="text/javascript" src="../js/ajax.js"></script>
        <script type="text/javascript" src="../js/funciones.js"></script>

        <script type="text/javascript" src="../js/validaFecha.js"></script>
        <script type="text/javascript" src="../js/foundation-datepicker.js"></script>
        <script type="text/javascript" src="../js/jquery.maskedinput.js"></script>

        <script type="text/javascript" src="reporte_integracion_v1.0.js"></script>

        <link href="../src/skin-vista/ui.dynatree.css" rel="stylesheet" type="text/css" >
        <script src="../src/jquery.dynatree.js" type="text/javascript"></script>
    </head>
    <body>
        <?php
			$contenido->validar_seguridad(0);
			$contenido->cabecera_html();
        ?>
        <div class="title-bar">
            <div class="wrapper">
                <div class="breadcrumb">
                    <ul>
                        <li class="breadcrumb_on">Reportes de integraci&oacute;n</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="contenedor_principal volumen">
            <div class="padding">     
                <div class="contenedor_error" id="contenedor_error"></div>
                <div class="contenedor_exito" id="contenedor_exito"></div>
                <fieldset style="text-align: left;">
                    <legend>Reporte General de Tesorer&iacute;a</legend>
                    <div>
                        <table border="0" cellpadding="5" cellspacing="0" style="width:100%;">
                            <tr>
                                <td align="right" style="width:20%;">
                                	<label class="inline">Fecha inicial</label>
                                </td>
                                <td style="width:30%;">
                                    <input type="text" name="txt_fecha_ini" id="txt_fecha_ini" maxlength="10" style="width:120px;" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                                </td>
                                <td align="right" style="width:20%;">
                                	<label class="inline">Fecha final</label>
                                </td>
                                <td style="width:30%;">
                                    <input type="text" name="txt_fecha_fin" id="txt_fecha_fin" maxlength="10" style="width:120px;" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                                </td>
                            </tr>
                            <tr>
                                <td align="right" style="width:20%;">
                                	<label class="inline">Entidad</label>
                                </td>
                                <td style="width:30%;">
                                    <?php
										$lista_entidades = $dbDatosEntidad->getListaDatosEntidad(1);
										
										$combo->getComboDb("cmb_entidad", "", $lista_entidades, "id_prestador,nombre_prestador", "--Seleccione--", "", "", "width:100%;");
									?>
                                </td>
                                <td align="center" colspan="2">
                                    <input type="button" value="Consultar" class="btnPrincipal" onclick="consultar_integracion();" /><br/>
                                </td>
                            </tr>
                        </table>
                        <div id="d_reporte"></div>
                    </div>
                </fieldset>
            </div>
        </div>
        <script type="text/javascript" src="../js/foundation.min.js"></script>
        <script>
			$(document).foundation();
			
			$(function() {
				window.prettyPrint && prettyPrint();
				
				$("#txt_fecha_ini").fdatepicker({
					format: "dd/mm/yyyy"
				});
				
				$("#txt_fecha_fin").fdatepicker({
					format: "dd/mm/yyyy"
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