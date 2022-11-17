<?php
session_start();
/*
  Pagina listado de perfiles, muestra los perfiles existentes, para modificar o crear uno nuevo
  Autor: Helio Ruber López - 16/09/2013
 */

require_once("../db/DbVariables.php");
require_once("../principal/ContenidoHtml.php");
require_once '../funciones/Class_Combo_Box.php';
require_once '../db/DbAsignarCitas.php';
require_once '../db/DbListas.php';
require_once '../db/DbTiposCitas.php';
require_once '../db/DbConvenios.php';
require_once("../funciones/FuncionesPersona.php");

$variables = new Dbvariables();
$contenido = new ContenidoHtml();
$combo = new Combo_Box();
$asignarCitas = new DbAsignarCitas();
$listas = new DbListas();
$tiposCitas = new DbTiposCitas();
$convenios = new DbConvenios();

$funciones_persona = new FuncionesPersona();

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

            <script type='text/javascript' src='reporte_citas_v1.6.js'></script>

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
                                    <li class="breadcrumb_on">Reporte de citas</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="contenedor_principal volumen">
                        <div class="padding">

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
                                    <td style="width: 10%;">
                                        <label class="inline">Profesional:</label>
                                    </td>
                                    <td style="width: 40%;">
                                        <?php $combo->getComboDb("cmb_profesional", "", $asignarCitas->getListaUsuariosCitasActivo(), "id_usuario, nombre_completo_aux", "Seleccione el usuario", "", "", "width: 200px;"); ?>
                                    </td>
                                    <td colspan="2">
                                        <?php
                                        $arr_fechas = $variables->getFechaActualMostrar();
                                        $fecha_fin = $arr_fechas["fecha_actual_mostrar"];
                                        $ano_aux = substr($fecha_fin, 6, 4);
                                        $fecha_ini = "01/01/" . $ano_aux;
                                        ?>
                                        <table style="width: 100%;">
                                            <tr>
                                                <td>
                                                    <label class="inline">Fecha inicial:</label>
                                                </td>
                                                <td>
                                                    <input type="text" class="input" maxlength="10" style="width:120px;" name="fechaInicial" id="fechaInicial" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" value="<?php echo($fecha_fin); ?>" />
                                                </td>
                                                <td>
                                                    <label class="inline">Fecha final:</label>
                                                </td>
                                                <td>
                                                    <input type="text" class="input" maxlength="10" style="width:120px;" name="fechaFinal" id="fechaFinal" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" value="<?php echo($fecha_fin); ?>" />
                                                </td>
                                            </tr>
                                        </table>

                                    </td>

                                </tr>
                                <tr>
                                    <td style="width: 10%;">
                                        <label class="inline">Lugar:</label>
                                    </td>
                                    <td style="width: 40%;">
                                        <?php $combo->getComboDb("cmb_lugar", "", $listas->getListaDetalles(12), "id_detalle, nombre_detalle", "Seleccione el modulo", "", "", "width: 200px;"); ?>

                                    </td>
                                    <td style="width: 10%;">
                                        <label class="inline">Tipo de cita:</label>
                                    </td>
                                    <td style="width: 40%; text-align: left;">
                                        <?php $combo->getComboDb("cmb_tipo_cita", "", $tiposCitas->getTiposcitas(), "id_tipo_cita, nombre_tipo_cita", "Seleccione el tipo de cita", "", "", "width: 200px;"); ?>
                                    </td>

                                </tr>
                                <tr>
                                    <td style="width: 10%;">
                                        <label class="inline">Hora:</label>
                                    </td>
                                    <td style="width: 40%;">
                                        <select id="cmb_hora" name="cmb_hora">
                                            <option value>Seleccione el rango de hora</option>
                                            <option value="1">A.M.</option>
                                            <option value="2">P.M.</option>
                                        </select>
                                    </td>
                                    <td style="width: 10%;">
                                        <label class="inline">Convenio:</label>
                                    </td>
                                    <td style="width: 40%; text-align: left;">
                                        <?php $combo->getComboDb("cmb_convenio", "", $convenios->getListaConveniosActivos(), "id_convenio, nombre_convenio", "Seleccione el convenio", "", "", "width: 200px;"); ?>

                                    </td>
                                </tr>
                            </table>
                            <?php
                            $rta_eestado_cita_aux = $listas->getListaDetalles(4);
                            ?>
                            <fieldset>
                                <legend style="text-align: left;">Estado de la cita</legend>

                                <div style="position: relative; width: 100%;">
                                    <?php
                                    $contador = 1;
                                    foreach ($rta_eestado_cita_aux as $value) {
                                        ?>
                                        <div class="div_en_linea" style="width: 150px; height: 50px; float: center;">
                                            <table style="width: 100%;">
                                                <tr>
                                                    <td style="text-align: right;">
                                                        <label class="inline"><?php echo $value['nombre_detalle']; ?></label>
                                                    </td>
                                                    <td style="width: 20px;">                             
                                                        <input type="checkbox" name="tp<?php echo $contador; ?>" id="tp<?php echo $contador; ?>" value="<?php echo $value['id_detalle']; ?>" checked="checked" /> 
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <?php
                                        $contador++;
                                    }
                                    ?>
                                    <div class="div_en_linea" style="width: 200px; height: 50px; float: center;">
                                        <table style="width: 100%;">
                                            <tr>
                                                <td style="text-align: right;">
                                                    <label class="inline"><b>TODOS LOS ESTADOS</b></label>
                                                </td>
                                                <td style="width: 20px;">                             
                                                    <input type="checkbox" name="tp_todos" id="tp_todos" value="" onchange="seleccionar_todos_estados();" checked="checked" /> 
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <input type="hidden" id="hdd_cant_estados_citas" name="hdd_cant_estados_citas" value="<?php echo count($rta_eestado_cita_aux); ?>" />
                            </fieldset>
                            <div style="clear: both;"></div>
                            <?php
                            ?>
                            <table style="width:100%;">
                                <tr>
                                    <td style="width: 100px;">
                                        <label class="inline">Cita:</label>
                                    </td>
                                    <td>
                                        <input type="text" class="input" style="width:100%;"  name="cita" id="cita" placeholder="Identificaci&oacute;n, nombre, telefono del paciente, observaci&oacute;n o ID de la cita" />

                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <input type="button" class="btnPrincipal" value="Buscar" onclick="buscar();" />
                                    </td>
                                </tr>
                            </table>

                            <div id="reporte_citas"></div>

                        </div>
                    </div>
                    <script type='text/javascript' src='../js/foundation.min.js'></script>


                    <script>
                                            $(document).foundation();

                                            $(function () {
                                                window.prettyPrint && prettyPrint();

                                                $('#fechaInicial').fdatepicker({
                                                    format: 'dd/mm/yyyy'
                                                });
                                                $('#fechaFinal').fdatepicker({
                                                    format: 'dd/mm/yyyy'
                                                });

                                            });
                    </script>
                    <?php
                    $contenido->footer();
                    ?>  
                </body>
                </html>