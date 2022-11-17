<?php
session_start();
/*
  Pagina listado de perfiles, muestra los perfiles existentes, para modificar o crear uno nuevo
  Autor: Helio Ruber López - 16/09/2013
 */

require_once("../db/DbVariables.php");
require_once("../funciones/Utilidades.php");
require_once("../principal/ContenidoHtml.php");
$variables = new DbVariables();
$utilidades = new Utilidades();
$contenido = new ContenidoHtml();
$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);

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
        <script type='text/javascript' src='../js/jquery.js'></script>
        <script type="text/javascript" src="../js/jquery-ui.custom.js"></script>
        <script type="text/javascript" src="../js/jquery.cookie.js"></script>
        <script type='text/javascript' src='../js/jquery.validate.js'></script>
        <script type='text/javascript' src='../js/jquery.validate.add.js'></script>
        <script type='text/javascript' src='../js/ajax.js'></script>
        <script type='text/javascript' src='../js/funciones.js'></script>
        <script type='text/javascript' src="../js/sweetalert2.all.min.js"></script>   
        <script type='text/javascript' src='convenios_v1.7.js'></script>

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
                        <li class="breadcrumb_on">Administraci&oacute;n de convenios</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="contenedor_principal volumen">
            <div class="padding">
                <form id="frmBuscarConvenio" name="frmBuscarConvenio"> 
                    <table style="width: 100%;">
                        <tr valign="middle">
                            <td align="center" colspan="2">
                                <div id="advertenciasg">
                                    <div class='contenedor_error' id='contenedor_error'></div>
                                    <div class='contenedor_exito' id='contenedor_exito'></div>
                                </div> 
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" id="txtParametro" name="txtParametro" placeholder="Codigo o nombre del convenio" onblur="trim_cadena(this);" />
                            </td>
                            <td style="width: 10%;">
                                <input type="submit" id="btnBuscar" nombre="btnBuscar" value="Buscar" class="btnPrincipal peq" onclick="buscarConvenio();"/>

                            </td>
                            <td style="width: 21%;">
                                <input type="button" id="btn_buscar_usuario" nombre="btn_buscar_usuario" value="Ver todos" class="btnSecundario peq" onclick="muestra_convenios();"/>
                                <?php
                                if ($tipo_acceso_menu == 2) {
                                    ?>
                                    <input type="button" id="btn_buscar_usuario" nombre="btn_buscar_usuario" value="Nuevo convenio" class="btnPrincipal peq" onclick="formNuevoConvenio('-5');"/>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                </form>
                <div id="principal_convenios"></div>
                <input type="hidden" id="hdd_resultado" name="hdd_resultado" />

            </div>

        </div>
        <?php
        $contenido->footer();
        ?>  
    </body>
</html>