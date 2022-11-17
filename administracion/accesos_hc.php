<?php
session_start();
/**
 * Generación de RIPS
 * Autor: Feisar Moreno - 15/04/2014
 */

require_once("../db/DbUsuarios.php");
require_once("../db/DbVariables.php");
require_once("../principal/ContenidoHtml.php");
require_once '../funciones/Class_Combo_Box.php';

$dbUsuarios = new DbUsuarios();
$dbVariables = new Dbvariables();
$contenido = new ContenidoHtml();
$combo = new Combo_Box();

//variables
$titulo = $dbVariables->getVariable(1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $titulo['valor_variable']; ?></title>
    <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
    <link href="../css/azul.css" rel="stylesheet" type="text/css" />
    <link href="../css/foundation-datepicker.css" rel="stylesheet" type="text/css" />
    
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
    
    <script type='text/javascript' src='accesos_hc.js'></script>
    
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
                	<li class="breadcrumb_on">Reporte de Accesos a la Historia Cl&iacute;nica</li>
                </ul>
            </div>
        </div>
    </div>
        <div class="contenedor_principal volumen">
            <div class="padding">
                <table style="width: 100%;" border="0">
                    <tr valign="middle">
                        <td align="center" colspan="4">
                            <div id="advertenciasg">
                                <div class='contenedor_error' id='contenedor_error'></div>
                                <div class='contenedor_exito' id='contenedor_exito'></div>
                            </div> 
                        </td>
                    </tr>
                    <tr>
                        <td align="right" style="width:12%;">
                            <label class="inline">Usuario:&nbsp;</label>
                        </td>
                        <td align="left" style="width:38%;">
                            <?php
                            	$combo->getComboDb("cmb_usuario", "", $dbUsuarios->getUsuarios(), "id_usuario, nombre_completo", "Seleccione un usuario", "", "", "width: 280px;");
							?>
                        </td>
                        <td align="right" style="width:12%;">
                            <label class="inline">Paciente:&nbsp;</label>
                        </td>
                        <td align="left" style="width:38%;">
							<table border="0" cellpadding="0" cellspacing="0" width="100%">
                            	<tr>
                                	<td style="width:50%;">
                                    	<input type="hidden" name="hdd_paciente" id="hdd_paciente" value="" />
                                        <input type="text" name="txt_paciente" id="txt_paciente" value="" readonly="readonly" style="width:280px;" />
                                    </td>
                                    <td valign="top" style="width:1%">
                                    	<a href="#" onclick="abrir_buscar_paciente();"><img src="../imagenes/Search-icon.png" style="padding: 0 0 0 5px;" title="Buscar paciente" /></a>
                                    </td>
                                    <td valign="top" style="width:49%">
                                    	<a href="#" onclick="limpiar_paciente();"><img src="../imagenes/borrador.png" style="padding: 0 0 0 5px;" title="Limpiar" /></a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table style="width: 100%;" border="0">
                                <tr>
                                    <td align="right" style="width:23%;">
                                        <label class="inline">Fecha inicial:&nbsp;</label>
                                    </td>
                                    <td align="left" style="width:27%;">
                                        <input type="text" class="input" maxlength="10" style="width:120px;" name="txt_fecha_inicial" id="txt_fecha_inicial" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                                    </td>
                                    <td align="right" style="width:18%;">
                                        <label class="inline">Fecha final:&nbsp;</label>
                                    </td>
                                    <td align="left" style="width:32%;">
                                        <input type="text" class="input" maxlength="10" style="width:120px;" name="txt_fecha_final" id="txt_fecha_final" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <input type="button" name="btn_buscar_accesos" id="btn_buscar_accesos" class="btnPrincipal" value="Buscar" onclick="buscar_accesos();" />
                        </td>
                    </tr>
                </table>
                <div id="d_accesos_hc"></div>
            </div>
        </div>
        <script type='text/javascript' src='../js/foundation.min.js'></script>
        <script>
            $(document).foundation();
			
            $(function() {
                window.prettyPrint && prettyPrint();

                $('#txt_fecha_inicial').fdatepicker({
                    format: 'dd/mm/yyyy'
                });
                $('#txt_fecha_final').fdatepicker({
                    format: 'dd/mm/yyyy'
                });

            });
        </script>
        <div id="fondo_negro_pacientes" class="d_fondo_negro"></div>
        <div class="div_centro" id="d_centro_pacientes" style="display:none;">
            <a name="a_cierre_panel" id="a_cierre_panel" href="#" onclick="mostrar_formulario_pacientes(0);"></a>
            <div class="div_interno" id="d_interno_pacientes"></div>
        </div>
        <?php
        $contenido->footer();
        ?> 
    </body>
</html>