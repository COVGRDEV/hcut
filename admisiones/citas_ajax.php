<?php
/*
  Página que permite los procedimiento ajax de estado
  Autor: Juan Pablo Gomez Quiroga - 01/10/2013
 */

header('Content-Type: text/xml; charset=UTF-8');

session_start();

require_once("../principal/ContenidoHtml.php");
require_once("../db/DbCitas.php");
require_once("../funciones/FuncionesPersona.php");
require_once("../db/DbCitas.php");
require_once '../funciones/Utilidades.php';

$contenido = new ContenidoHtml();
$contenido->validar_seguridad(1);
$utilidades = new Utilidades();

$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);

$citas = new DbCitas();
$funcionesPersona = new FuncionesPersona();
$citas = new DbCitas();

$opcion = $_POST["opcion"];

switch ($opcion) {
    case "1"://Imprime las citas que estan con estado: PROGRAMADAS del día actual.
        $citas_aux = $citas->getEstadoagendados2();
        ?>
        <table class="paginated modal_table">
            <thead>
                <tr>

                    <th style="width:12%;"><p>Documento identidad</p></th>
        <th style="width:20%;"><p>Usuario</p></th>
        <th style="width:10%;"><p>Hora cita</p></th>
        <th style="width:18%;"><p>Profesional</p></th>
        <th style="width:18%;"><p>Convenio</p></th>
        <th style="width:15%;"><p>Llegada</p></th>
        <th style="width:15%;"><p>Ingreso</p></th>
        <th style="width:15%;"><p>Estado</p></th>
        </tr>
        </thead>
        <tbody>
            <?php
            if (count($citas_aux) > 0) {
                $contador = 0;
                ?>

            <div style="text-align: left;font-weight: 600; font-size: 10pt;">
                <span>Resultados: <?php echo count($citas_aux); ?></span>
            </div>
            <?php
            foreach ($citas_aux as $value) {
                $contador++;
                ?>

                <tr id="<?php echo $contador; ?>" onclick="detalle_cita(<?php echo $value['id_cita']; ?>)">
                    <td style="text-align: left;"><?php echo($value['numero_documento']); ?></td>
                    <td style="text-align: left;">
                        <?php echo $funcionesPersona->obtenerNombreCompleto($value['nombre_1'], $value['nombre_2'], $value['apellido_1'], $value['apellido_2']); ?>
                        <input type="hidden" id="idCita" name="idCita" value="<?php echo $value['id_cita'] ?>" />
                    </td>

                    <td align="center"><?php echo($value['hora_cita']); ?></td>
                    <td align="left"><?php echo($value['nombre_usuario'] . " " . $value['apellido_usuario']); ?></td>
                    <td align="center"><?php echo($value['nombre_convenio']); ?></td>
                    <td align="center"><?php echo($value['fecha_llegada2_aux']); ?></td>
                    <td align="center"><?php echo($value['fecha_ingreso2_aux']); ?></td>
                    <td align="center"><?php echo($value['nombre_detalle']); ?></td>

                </tr>
                <?php
            }
        } else {
            ?>
            <tr>
                <td colspan="7">No hay resultados</td>
            </tr>
            <?php
        }
        ?>
        </tbody>
        </table>
        <script type="text/javascript" id="ajax">
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

    case "2":
        $id = $_POST['idCita'];
        $rta_aux = $citas->getCita($id);

        $mensajeBoton = '';

        if ($rta_aux['id_estado_cita'] == '14') {//Si igual a: PROGRAMADA
            $mensajeBoton = 'Registrar llegada';
        } else if ($rta_aux['id_estado_cita'] == '130') {
            $mensajeBoton = 'Registrar ingreso';
        }
        ?>
        <div class="encabezado">
            <h3>Detalles de la cita</h3>
        </div>
        <div id="dContenedorInterno">
            <div id="d_loader" style="height: 16px; width: 16px; margin: auto; padding: 10px 0 0 0;">
            </div>    
            <?php
            if (count($rta_aux) > 0) {
                ?>
                <table style="width: 750px; margin: auto; font-size: 9pt;">   
                    <tr>
                        <td colspan="2">
                            <h5>Creado por: <?php echo($rta_aux['usuario_creador']); ?></h5>
                        </td>
                        <td colspan="2">
                            <h5><?php echo $funcionesPersona->obtenerFecha5(strtotime($rta_aux['fecha_crea'])); ?></h5>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right; width: 200px; padding-right: 15px;"><p>N&uacute;mero de Identificaci&oacute;n : </p></td>
                        <td style="text-align: left; font-weight: 600;"><?php echo $rta_aux['numero_documento']; ?></td>
                    <input type="hidden" id="idCita2" name="idCita2" value="<?php echo $id; ?>" />

                    <td style="text-align: right; width: 200px; padding-right: 15px;"><p>Tipo de documento : </p></td>
                    <td style="text-align: left; font-weight: 600;"><?php echo $rta_aux['nombre_detalle']; ?></td>
                    </tr>

                    <tr>
                        <td style="text-align: right; width: 200px; padding-right: 15px;"><p>Nombre del paciente : </p></td>
                        <td style="text-align: left; font-weight: 600;"><?php echo $funcionesPersona->obtenerNombreCompleto($rta_aux['nombre_1'], $rta_aux['nombre_2'], $rta_aux['apellido_1'], $rta_aux['apellido_2']) ?></td>

                        <td style="text-align: right; width: 200px; padding-right: 15px;"><p>Tel&eacute;fono de contacto : </p></td>
                        <td style="text-align: left; font-weight: 600;"><?php echo $rta_aux['telefono_contacto']; ?></td>
                    </tr>

                    <tr>
                        <td style="text-align: right; width: 200px; padding-right: 15px;"><p>Usa lentes : </p></td>
                        <td style="text-align: left; font-weight: 600;"><?php echo $rta_aux['ind_lentes'] == 1 ? 'Si' : 'No'; ?></td>

                        <td style="text-align: right; width: 200px; padding-right: 15px;"><p>Fecha de nacimiento : </p></td>
                        <td style="text-align: left; font-weight: 600;"><?php echo $rta_aux['fecha_nacimiento_aux']; ?></td>
                    </tr>

                    <tr>
                        <td style="text-align: right; width: 200px; padding-right: 15px;"><p>Tiempo de cita : </p></td>
                        <td style="text-align: left; font-weight: 600;"><?php echo $rta_aux['tiempo_cita'] . ' Minutos'; ?></td>

                        <td style="text-align: right; width: 200px; padding-right: 15px;"><p>Convenio : </p></td>
                        <td style="text-align: left; font-weight: 600;"><?php echo $rta_aux['nombre_convenio_aux']; ?></td>
                    </tr>


                    <tr>
                        <td style="text-align: right; width: 200px; padding-right: 15px;"><p>Profesional que atiende : </p></td>
                        <td style="text-align: left; font-weight: 600;"><?php echo $rta_aux['profesional_atiende']; ?></td>

                        <td style="text-align: right; width: 200px; padding-right: 15px;"><p>Hora de llegada : </p></td>
                        <td style="text-align: left; font-weight: 600;"><?php echo $rta_aux['fecha_llegada2_aux']; ?></td>

                    </tr>

                    <tr>
                        <td style="text-align: right; width: 200px; padding-right: 15px;"><p>Hora de ingreso : </p></td>
                        <td style="text-align: left; font-weight: 600;"><?php echo $rta_aux['fecha_ingreso2_aux']; ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight: 600;" colspan="4"><p>Observaci&oacute;n : </p></td>
                    </tr>
                    <tr>
                        <td style="text-align: justify;font-size: 12pt;" colspan="4">
                            
                            
                            <textarea style="height: 150px;" readonly="" rows="4" cols="50"><?php echo $rta_aux['observacion_cita'] != '' ? $rta_aux['observacion_cita'] : 'No hay resultados para la observaci&oacute;n...'; ?></textarea>
                            
                        </td>
                    </tr>

                </table>
                <br/>
                <br/>
                <?php
                //if ($tipo_acceso_menu == 2) {

                if ($rta_aux['id_estado_cita'] != '17' && $rta_aux['id_estado_cita'] != '16') {
                    ?>
                    <input class="btnPrincipal" type="submit" value="<?php echo $mensajeBoton; ?>" onclick="cambiarEstado(<?php echo $rta_aux['id_estado_cita']; ?>)" />
                    <?php
                }
                //}
                ?>
            </div>
            <?php
        }
        break;

    case "3":
        $idCita = $_POST['idCita'];
        $idEstado = $_POST['idEstado'];

        $rta_aux = $citas->marca_cita_atendida($idCita, $_SESSION["idUsuario"], $idEstado);

        echo $rta_aux;
        break;


    case "4":
        $parametro = $utilidades->str_decode($_POST['parametro']);

        $rta_aux = $citas->buscarEstadoagendados(14, $parametro);
        ?>
        <table class="paginated modal_table">
            <thead>
                <tr>

                    <th style="width:12%;"><p>Documento identidad</p></th>
        <th style="width:20%;"><p>Usuario</p></th>
        <th style="width:10%;"><p>Hora cita</p></th>
        <th style="width:18%;"><p>Profesional</p></th>
        <th style="width:15%;"><p>Llegada</p></th>
        <th style="width:15%;"><p>Ingreso</p></th>
        <th style="width:15%;"><p>Estado</p></th>
        </tr>
        </thead>
        <tbody>
            <?php
            if (count($rta_aux) > 0) {
                $contador = 0;
                foreach ($rta_aux as $value) {
                    $contador++;
                    ?>
                    <tr id="<?php echo $contador; ?>" onclick="detalle_cita(<?php echo $value['id_cita']; ?>)">
                        <td style="text-align: left;"><?php echo($value['numero_documento']); ?></td>
                        <td style="text-align: left;">
                            <?php echo $funcionesPersona->obtenerNombreCompleto($value['nombre_1'], $value['nombre_2'], $value['apellido_1'], $value['apellido_2']); ?>
                            <input type="hidden" id="idCita" name="idCita" value="<?php echo $value['id_cita'] ?>" />
                        </td>

                        <td align="center"><?php echo($value['hora_cita']); ?></td>
                        <td align="left"><?php echo($value['nombre_usuario'] . " " . $value['apellido_usuario']); ?></td>
                        <td align="center"><?php echo($value['fecha_llegada2_aux']); ?></td>
                        <td align="center"><?php echo($value['fecha_ingreso2_aux']); ?></td>
                        <td align="center"><?php echo($value['nombre_detalle']); ?></td>

                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="7">No hay resultados</td>
                </tr>
                <?php
            }
            ?>
        </tbody>
        </table>
        <script type="text/javascript" id="ajax">
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
}
?>
