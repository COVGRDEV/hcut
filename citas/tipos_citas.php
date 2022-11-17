<?php
session_start();
/*
  Pagina listado de perfiles, muestra los perfiles existentes, para modificar o crear uno nuevo
  Autor: Helio Ruber López - 16/09/2013
 */

require_once("../db/DbVariables.php");
require_once("../principal/ContenidoHtml.php");
require_once '../funciones/Class_Combo_Box.php';

$variables = new DbVariables();
$contenido = new ContenidoHtml();

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
        <script type='text/javascript' src='../js/jquery.min.js'></script>
        <script type="text/javascript" src="../js/jquery-ui.custom.js"></script>
        <script type="text/javascript" src="../js/jquery.cookie.js"></script>
        <script type='text/javascript' src='../js/jquery.validate.js'></script>
        <script type='text/javascript' src='../js/jquery.validate.add.js'></script>
        <script type='text/javascript' src='../js/ajax.js'></script>
        <script type='text/javascript' src='../js/funciones.js'></script>
        <script type='text/javascript' src='tipos_citas_v1.5.js'></script>
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
                        <li class="breadcrumb_on">Tipos de citas</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="contenedor_principal volumen">
            <div class="padding">
                <form id="frmBuscarTipoCita" name="frmBuscarTipoCita"> 
                    <table style="width: 100%;">
                        <tr valign="middle">
                            <td align="center" colspan="4">
                                <div id="advertenciasg">
                                    <div class='contenedor_error' id='contenedor_error'></div>
                                    <div class='contenedor_exito' id='contenedor_exito'></div>
                                </div> 
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" id="txtParametro" name="txtParametro" placeholder="Codigo o nombre del tipo de cita" onblur="trim_cadena(this);" />
                            </td>
                            <td style="width: 10%;">
                                <input type="submit" id="btnBuscar" nombre="btnBuscar" value="Buscar" class="btnPrincipal peq" onclick="buscarTipoCita();" />
                            </td>
                            <td style="width: 10%;">
                                <input type="button" id="btn_buscar_usuario" nombre="btn_buscar_usuario" value="Ver todos" class="btnPrincipal peq" onclick="mostrar_tipos_citas();" />
                            </td>
                        </tr>
                    </table>
                </form>
                <div id="reporte_citas"></div>
            </div>
        </div>
        <div id="d_guardar_tipo_cita" style="display:none;"></div>
        <div id="fondo_negro_procedimientos" class="d_fondo_negro"></div>
        <div class="div_centro" id="d_centro_procedimientos" style="display:none;">
            <a name="a_cierre_panel" id="a_cierre_panel" href="#" onclick="seleccionarProcedimiento(0);"></a>
            <div class="div_interno" id="d_interno_procedimientos"></div>
        </div>
        <?php
        $contenido->footer();
        ?>  
    </body>
</html>