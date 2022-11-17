<?php
session_start();
/*
  Pagina listado de perfiles, muestra los perfiles existentes, para modificar o crear uno nuevo
  Autor: Helio Ruber LÃ³pez - 16/09/2013
 */
require_once("../db/DbVariables.php");
require_once("../funciones/Utilidades.php");
require_once("../principal/ContenidoHtml.php");

$variables = new DbVariables();
$utilidades = new Utilidades();
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
        <script type='text/javascript' src='../js/jquery.js'></script>
        <script type="text/javascript" src="../js/jquery-ui.custom.js"></script>
        <script type="text/javascript" src="../js/jquery.cookie.js"></script>
        <script type='text/javascript' src='../js/jquery.validate.js'></script>
        <script type='text/javascript' src='../js/jquery.validate.add.js'></script>
        <script type='text/javascript' src='../js/ajax.js'></script>
        <script type='text/javascript' src='../js/funciones.js'></script>
        <script type='text/javascript' src='../funciones/ckeditor/ckeditor.js'></script>
        <script type='text/javascript' src='../funciones/ckeditor/config.js'></script>
        <script type='text/javascript' src='plantillas_formulas_v1.3.js'></script>
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
                        <li class="breadcrumb_on">Plantillas de f&oacute;rmulas m&eacute;dicas</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="contenedor_principal volumen">
            <div class="padding">
                <div class='contenedor_exito' id='contenedor_exito'></div>
                <form id="frmBuscarPlantilla" name="frmBuscarPlantilla"> 
                    <table style="width: 100%;">
                        <tr valign="middle">
                            <td align="center" colspan="2">
                                <div id="advertenciasg"></div> 
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" id="txtParametro" name="txtParametro" placeholder="Codigo o texto de la f&oacute;rmula m&eacute;dica" onblur="convertirAMayusculas(this); trim_cadena(this);" />
                            </td>
                            <td style="width: 10%;">
                                <input type="submit" id="btnBuscar" nombre="btnBuscar" value="Buscar" class="btnPrincipal peq" onclick="buscarFormulas();"/>

                            </td>
                            <td style="width: 21%;">
                                <input type="button" id="btn_buscar_usuario" nombre="btn_buscar_usuario" value="Ver todos" class="btnSecundario peq" onclick="muestra_formulas_medicas();"/>
                                <input type="button" id="btn_buscar_usuario" nombre="btn_buscar_usuario" value="Nueva f&oacute;rmula" class="btnPrincipal peq" onclick="ventanaNuevo();"/>
                            </td>
                        </tr>
                    </table>
                </form>
                <div id="principaltk"></div>
            </div>
        </div>
        <?php
        $contenido->footer();
        ?>  
    </body>
</html>
