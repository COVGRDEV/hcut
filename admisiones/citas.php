<?php
session_start();
require_once("../db/DbVariables.php");
require_once '../principal/ContenidoHtml.php';
require_once("../db/DbEstadosAtencion.php");
require_once("../db/DbCitas.php");
require_once("../funciones/FuncionesPersona.php");
require_once("../db/DbUsuarios.php");
require_once("../db/DbAsignarCitas.php");
require_once("../funciones/Class_Combo_Box.php");

$contenidoHtml = new ContenidoHtml();

$variables = new Dbvariables();
$estadosAtencion = new DbEstadosAtencion();
$citas = new DbCitas();
$funcionesPersona = new FuncionesPersona();
$usuarios = new DbUsuarios();
$combo = new Combo_Box();
$asignar_citas = new DbAsignarCitas();

//variables
$titulo = $variables->getVariable(1);
$listadoEstadosAtencion = $estadosAtencion->getEstadosatencion();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $titulo['valor_variable']; ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
        <link href="../css/azul.css" rel="stylesheet" type="text/css" />
        <script type='text/javascript' src='../js/jquery.js'></script>
        <script type='text/javascript' src='../js/funciones.js'></script>
        <script type='text/javascript' src='../js/ajax.js'></script>
        <script type='text/javascript' src='../js/jquery.validate.js'></script>
        <script type='text/javascript' src='citas_v1.3.js'></script>
        <script type='text/javascript' src='../js/validaFecha.js'></script>
    </head>
    <body>
        <?php
        $contenidoHtml->validar_seguridad(0);
        $contenidoHtml->cabecera_html();
        ?>
        <div class="title-bar">
            <div class="wrapper">
                <div class="breadcrumb breadcrumb-width" style="width: 300px; float: left;">
                    <ul>
                        <li class="breadcrumb_on">Llegada a citas</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="contenedor_principal volumen">
            <div class="padding">

                <form id="frmBuscarCita" name="frmBuscarCita"> 
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

                                <input type="text" id="txtParametro" name="txtParametro" placeholder="N&uacute;mero de documento o nombre del paciente" onblur="convertirAMayusculas(this);
                                        trim_cadena(this);" />

                            </td>
                            <td style="width: 10%;">
                                <input type="submit" id="btnBuscar" nombre="btnBuscar" value="Buscar" class="btnPrincipal peq" onclick="buscarCitas();"/>

                            </td>
                            <td style="width: 10%;">
                                <input type="button" id="btn_buscar_usuario" nombre="btn_buscar_usuario" value="Ver todos" class="btnSecundario peq" onclick="verTodos();"/>

                            </td>
                        </tr>
                    </table>
                </form>

                <div id="contenedor">
                </div>
            </div>
        </div>
        <?php
        $contenidoHtml->footer();
        ?>
    </body>
</html>
