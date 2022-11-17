<?php
session_start();
/*
  Pagina listado de perfiles, muestra los perfiles existentes, para modificar o crear uno nuevo
  Autor: Helio Ruber LÃ³pez - 16/09/2013
 */

require_once("../db/DbVariables.php");
require_once("../principal/ContenidoHtml.php");
require_once '../funciones/Class_Combo_Box.php';
require_once '../db/DbAsignarCitas.php';
require_once '../db/DbListas.php';
require_once '../db/DbTiposCitas.php';
$variables = new DbVariables();
$contenido = new ContenidoHtml();
$combo = new Combo_Box();
$asignarCitas = new DbAsignarCitas();
$listas = new DbListas();
$tiposCitas = new DbTiposCitas();


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


        <script type='text/javascript' src='../js/validaFecha.js'></script>
        <script type='text/javascript' src='../js/foundation-datepicker.js'></script>
        <script type="text/javascript" src="../js/jquery.maskedinput.js"></script>

        <script type='text/javascript' src='reporte_citas_v1.1.js'></script>

        <link href="../src/skin-vista/ui.dynatree.css" rel="stylesheet" type="text/css" >
            <script src="../src/jquery.dynatree.js" type="text/javascript"></script>

    </head>
    <body>
        <?php
        $contenido->validar_seguridad(0);
        $contenido->cabecera_html();
        ?>
        <script type='text/javascript'>cargar_antecedentes();</script>
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


                            <table style="width: 100%;">
                                <tr>
                                    <td>
                                        <label class="inline">Fecha inicial:</label>
                                    </td>
                                    <td>
                                        <input type="text" class="input" maxlength="10" style="width:120px;" name="fechaInicial" id="fechaInicial" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                                    </td>
                                    <td>
                                        <label class="inline">Fecha final:</label>
                                    </td>
                                    <td>
                                        <input type="text" class="input" maxlength="10" style="width:120px;" name="fechaFinal" id="fechaFinal" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                                    </td>
                                </tr>
                            </table>
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
                            <label class="inline">Tipo de cita:</label>
                        </td>
                        <td style="width: 40%; text-align: left;">
                            <?php $combo->getComboDb("cmb_tipo_cita", "", $tiposCitas->getTiposcitas(), "id_tipo_cita, nombre_tipo_cita", "Seleccione el tipo de cita", "", "", "width: 200px;"); ?>
                        </td>

                    </tr>
                    <tr>
                        <td style="width: 10%;">
                            <label class="inline">Lugar:</label>
                        </td>
                        <td style="width: 40%;">
                            <?php $combo->getComboDb("cmb_lugar", "", $listas->getListaDetalles(12), "id_detalle, nombre_detalle", "Seleccione el modulo", "", "", "width: 200px;"); ?>
                        </td>

                    </tr>
                </table>

                <table style="width: 100%;">
                    <tr>
                        <td style="width: 100px;">
                            <label class="inline">Cita:</label>
                        </td>
                        <td>
                            <input type="text" class="input" style="width:100%;"  name="cita" id="cita" placeholder="Identificaci&oacute;n, nombre, telefono del paciente, observaci&oacute;n o ID de la cita" />

                        </td>
                    </tr>
                </table>


                <fieldset style="text-align: left; padding-top: 0;">
                    <legend>Estado de la cita:</legend>
                    <table style="width: 80%; margin: auto;">
                        <?php
                        $rta_aux = $listas->getListaDetalles(4);

                        $numero_tr = count($rta_aux) / 2;
                        $contador = 0;

                        for ($i = 0; $i <= $numero_tr; $i++) {
                            ?>
                            <tr>
                                <?php
                                for ($e = 0; $e <= 1; $e++) {
                                    if ($contador <= (count($rta_aux) - 1)) {
                                        ?>
                                        <td style="width: 25%;"><label class="inline" style="font-weight: 700;text-align: right;"><?php echo $rta_aux[$contador]['nombre_detalle'] ?></label></td>
                                        <td style="width: 25%;"><input type="checkbox" name="e<?php echo $contador; ?>" id="e<?php echo $contador; ?>" value="<?php echo $rta_aux[$contador]['id_detalle'] ?>"></td>
                                        <?php
                                    }
                                    $contador++;
                                }
                                ?>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </fieldset>




                <table style="width:100%;">
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

            $(function() {
                window.prettyPrint && prettyPrint();

                $('#fechaInicial').fdatepicker({
                    format: 'dd/mm/yyyy'
                });
                $('#fechaFinal').fdatepicker({
                    format: 'dd/mm/yyyy'
                });

            });
            new imagen_muscular.GuardandoPNGs().resetCanvasMuscular();
            new imagen_tonometria_od.GuardandoPNGs().resetCanvasTonometria_od();
            new imagen_tonometria_oi.GuardandoPNGs().resetCanvasTonometria_oi();
        </script>


        <?php
        $contenido->footer();
        ?>  
    </body>
</html>