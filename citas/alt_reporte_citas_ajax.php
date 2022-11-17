<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

header('Content-Type: text/xml; charset=UTF-8');
session_start();
require_once("../db/DbCitas.php");
require_once("../funciones/FuncionesPersona.php");
require_once '../funciones/Utilidades.php';

$citas = new DbCitas();
$funciones_persona = new FuncionesPersona();
$utilidades = new Utilidades();

$opcion = $_POST["opcion"];

function operacion_horas($hora, $minutos, $tipo, $cantidad, $formato) {
    if ($tipo == 1) { //Sumar minutos
        $horaInicial = $hora . ":" . $minutos;
        $segundos_horaInicial = strtotime($horaInicial);
        $segundos_minutoAnadir = $cantidad * 60;
        $nuevaHora = date("H:i", $segundos_horaInicial + $segundos_minutoAnadir);
    } else if ($tipo == 2) { //Restar minutos
        $horaInicial = $hora . ":" . $minutos;
        $segundos_horaInicial = strtotime($horaInicial);
        $segundos_minutoAnadir = $cantidad * 60;
        $nuevaHora = date("H:i", $segundos_horaInicial - $segundos_minutoAnadir);
    }

    if ($formato == 12) {
        $hora_nueva = explode(":", $nuevaHora);
        $hora_resultado = mostrar_hora_format($hora_nueva[0], $hora_nueva[1]);
    } else {
        $hora_resultado = $nuevaHora;
    }

    return $hora_resultado;
}

//Devulve la hora en formato 12 horas con la jornada
function mostrar_hora_format($hora, $minutos) {
    $hora = cifras_numero($hora, 2);
    $minutos = cifras_numero($minutos, 2);

    $hora_res = '';
    if ($hora > 12) {
        $hora = $hora - 12;
        $hora_res = $hora . ":" . $minutos . " PM";
    } else {
        $hora_res = $hora . ":" . $minutos . " AM";
    }

    return $hora_res;
}

function cifras_numero($consecutivo, $cifras) {
    $longitud = strlen($consecutivo);
    while ($longitud <= $cifras - 1) {
        $consecutivo = "0" . $consecutivo;
        $longitud = strlen($consecutivo);
    }
    return $consecutivo;
}

switch ($opcion) {
    case "1": //Carga el listado total de convenios / El resultado de buscar convenios

        $profesional = $utilidades->str_decode($_POST['profesional']);
        $lugar = $utilidades->str_decode($_POST['lugar']);
        //$estado = $utilidades->str_decode($_POST['estado']);
        $fechaInicial = $utilidades->str_decode($_POST['fechaInicial']);
        $fechaFinal = $utilidades->str_decode($_POST['fechaFinal']);
        $cita = $utilidades->str_decode($_POST['cita']);
        $tipoCita = $utilidades->str_decode($_POST['tipoCita']);
        $hora = $utilidades->str_decode($_POST['hora']);
        
        
        $programada = $utilidades->str_decode($_POST['programada']);
        $cancelada = $utilidades->str_decode($_POST['cancelada']);
        $atencion = $utilidades->str_decode($_POST['atencion']);
        $noAsistio = $utilidades->str_decode($_POST['noAsistio']);
        $noFinalizada = $utilidades->str_decode($_POST['noFinalizada']);
        $sePresento= $utilidades->str_decode($_POST['sePresento']);
        $atendida= $utilidades->str_decode($_POST['atendida']);
     

        $rta_aux = $citas->getReporteCitas($profesional, $lugar, $fechaInicial, $fechaFinal, $cita, 0, $tipoCita, $hora, $programada, $cancelada, $atencion, $noAsistio, $noFinalizada, $sePresento, $atendida);
        ?>
        <table class="paginated modal_table" style="width: 99%; margin: auto;">
            <thead>
                <tr>
                    <th style="">Fecha</th>
                    <th style="">Hora</th>
                    <th style="">Profesional</th>
                    <th style="">Paciente</th>
                    <th style="">Tipo de cita</th>
                    <th style="">Estado</th>
                </tr>
            </thead>
            <?php
            if (count($rta_aux) >= 1) {

                foreach ($rta_aux as $value) {
                    ?>
                    <tr onclick="detalleCita(<?php echo $value['id_cita']; ?>)" title="<?php
                    //Imprime la Observacion de la cita en el tooltip
                    if ($value['id_estado_cita'] == 14 || $value['id_estado_cita'] == 16 || $value['id_estado_cita'] == 17 || $value['id_estado_cita'] == 82) {//Muestra la Observación
                        echo $value['observacion_cita'];
                    }
                    if ($value['id_estado_cita'] == 15 || $value['id_estado_cita'] == 18) {//Muestra la Observación
                        echo $value['observacion_cancela'];
                    }
                    ?>">
                        <td><?php
                            $fecha_cita_aux = substr($value['fecha_cita'], 0, 10);
                            $fecha_cita_aux = strtotime($fecha_cita_aux);
                            echo $funciones_persona->obtenerFecha4($fecha_cita_aux);
                            ?></td>
                        <td style=""><?php
                            $hora = substr($value['hora_aux'], 0, 2);
                            $minutos = substr($value['hora_aux'], 3, 4);
                            $hora_cita = operacion_horas(intval($hora), intval($minutos), 1, 0, 12);
                            echo $hora_cita;
                            ?></td>
                        <td><?php echo $value['profesional_aux']; ?></td>
                        <td><?php echo $value['nombre_1'] . ' ' . $value['nombre_2'] . ' ' . $value['apellido_1'] . ' ' . $value['apellido_2']; ?></td>
                        <td><?php echo $value['nombre_tipo_cita']; ?></td>
                        <td><?php echo $value['nombre_detalle']; ?></td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <td colspan="6">No hay resultados</td>
                <?php
            }
            ?>
        </table>
        <br/>
        <br/>
        <br/>
        <input type="button" onclick="generarPDF()" value="Generar reporte" class="btnPrincipal" />
        <div id="d_generar_pdf" style="display: none;">

        </div>

        <script id='ajax'>
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

    case "2": //Informacion del detalle de la cita
        $idCita = $_POST['idCita'];

        $rta_aux = $citas->getCita($idCita);

        if (count($rta_aux) >= 1) {
            ?>
            <div class="encabezado">
                <h3>C&oacute;digo de cita: <?php echo $rta_aux['id_cita']; ?></h3>
            </div>
            <div>
                <table style="width: 100%;">
                    <tr>
                        <td colspan="2" style="text-align: left;">
                            <h5>Doctor(a): <?php echo $rta_aux['profesional_atiende']; ?></h5>
                        </td>
                        <td colspan="2" style="text-align: right;">
                            <h5><?php
                                $fecha_cita_aux = substr($rta_aux['fecha_cita'], 0, 10);
                                $fecha_cita_aux = strtotime($fecha_cita_aux);
                                echo $funciones_persona->obtenerFecha4($fecha_cita_aux);
                                ?></h5>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">
                            <label class="inline"><b>Creado por: </b></label>
                        </td>
                        <td>
                            <label class="inline"><?php echo $rta_aux['usuario_creador']; ?></label>
                        </td>
                        <td style="text-align: right;">
                            <label class="inline"><b>Fecha creaci&oacute;n: </b></label>
                        </td>
                        <td>
                            <label class="inline"><?php echo $rta_aux['fecha_creacion_aux']; ?></label>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">
                            <label class="inline"><b>Tipo de cita: </b></label>
                        </td>
                        <td>
                            <label class="inline"><?php echo $rta_aux['nombre_tipo_cita']; ?></label>
                        </td>
                        <td style="text-align: right;">
                            <label class="inline"><b>Hora: </b></label>
                        </td>
                        <td>
                            <label class="inline"><?php
                                $hora = substr($rta_aux['hora_consulta'], 0, 2);
                                $minutos = substr($rta_aux['hora_consulta'], 3, 4);
                                $hora_cita = operacion_horas(intval($hora), intval($minutos), 1, 0, 12);
                                echo $hora_cita;
                                ?></label>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">
                            <label class="inline"><b>Tipo de documento: </b></label>
                        </td>
                        <td>
                            <label class="inline"><?php echo $rta_aux['nombre_detalle']; ?></label>
                        </td>
                        <td style="text-align: right;">
                            <label class="inline"><b>N&uacute;mero de documento: </b></label>
                        </td>
                        <td>
                            <label class="inline"><?php echo $rta_aux['numero_documento']; ?></label>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">
                            <label class="inline"><b>Primer nombre: </b></label>
                        </td>
                        <td>
                            <label class="inline"><?php echo $rta_aux['nombre_1']; ?></label>
                        </td>
                        <td style="text-align: right;">
                            <label class="inline"><b>Segundo nombre: </b></label>
                        </td>
                        <td>
                            <label class="inline"><?php echo $rta_aux['nombre_2']; ?></label>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">
                            <label class="inline"><b>Primer apellido: </b></label>
                        </td>
                        <td>
                            <label class="inline"><?php echo $rta_aux['apellido_1']; ?></label>
                        </td>
                        <td style="text-align: right;">
                            <label class="inline"><b>Segundo apellido: </b></label>
                        </td>
                        <td>
                            <label class="inline"><?php echo $rta_aux['apellido_2']; ?></label>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">
                            <label class="inline"><b>Tel&eacute;fono de contacto: </b></label>
                        </td>
                        <td>
                            <label class="inline"><?php echo $rta_aux['telefono_contacto']; ?></label>
                        </td>
                        <td style="text-align: right;">
                            <label class="inline"><b>Convenio/Entidad: </b></label>
                        </td>
                        <td>
                            <label class="inline"><?php echo $rta_aux['nombre_convenio_aux']; ?></label>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">
                            <label class="inline"><b>Usa lentes: </b></label>
                        </td>
                        <td style="text-align: left;">
                            <label class="inline"><?php
                                if ($rta_aux['ind_lentes'] == '1') {
                                    echo 'Si';
                                } else {
                                    echo 'No';
                                };
                                ?></label>
                        </td>
                        <td style="text-align: right;" colspan="2">
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">
                            <label class="inline"><b>Observaci&oacute;n de la cita: </b></label>
                        </td>
                        <td>
                            <label class="inline"><?php echo $rta_aux['observacion_cita']; ?></label>
                        </td>
                        <td style="text-align: right;">
                            <label class="inline"><b>Observaci&oacute;n de cita cancelada: </b></label>
                        </td>
                        <td>
                            <label class="inline"><?php echo $rta_aux['observacion_cancela']; ?></label>
                        </td>
                    </tr>
                </table>
            </div>
            <?php
        } else {
            ?>
            <h3>No hay resultados</h3>
            <?php
        }

        break;




    case "3": //Reporte en formato PDF
        require_once("../funciones/pdf/fpdf.php");
        require_once("../funciones/pdf/makefont/makefont.php");
        require_once("../funciones/pdf/funciones.php");

        $profesional = $_POST['profesional'];
        $lugar = $_POST['lugar'];
        $estado = $_POST['estado'];
        $fechaInicial = $_POST['fechaInicial'];
        $fechaFinal = $_POST['fechaFinal'];
        $cita = $_POST['cita'];
        $tipoCita = $_POST['tipoCita'];
        $hora = $utilidades->str_decode($_POST['hora']);
         
        $programada = $utilidades->str_decode($_POST['programada']);
        $cancelada = $utilidades->str_decode($_POST['cancelada']);
        $atencion = $utilidades->str_decode($_POST['atencion']);
        $noAsistio = $utilidades->str_decode($_POST['noAsistio']);
        $noFinalizada = $utilidades->str_decode($_POST['noFinalizada']);
        $sePresento= $utilidades->str_decode($_POST['sePresento']);
        $atendida= $utilidades->str_decode($_POST['atendida']);

        $rta_aux = $citas->getReporteCitas($profesional, $lugar, $fechaInicial, $fechaFinal, $cita, 1, $tipoCita, $hora, $programada, $cancelada, $atencion, $noAsistio, $noFinalizada, $sePresento, $atendida);
      //$rta_aux = $citas->getReporteCitas($profesional, $lugar, $fechaInicial, $fechaFinal, $cita, 0, $tipoCita, $hora, $programada, $cancelada, $atencion, $noAsistio, $noFinalizada, $sePresento, $atendida);

        //Genera el PDF
        $pdf = new FPDF('L', 'mm', array(216, 328));
        $pdf->AddPage();

        //Se agregan los logos
        $pdf->Image("../imagenes/logo-color.png", 250, 10, 40);
        //$pdf->Cell(80, 5, "", 0, 0, "C");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 0, "REPORTE DE CITAS", 0, 0, "C");
        $pdf->Ln(0);

        $pdf->SetFont('Arial', "", 10);
        if ($profesional != '') {
            $pdf->Cell(0, 0, ajustarCaracteres("Profesional: " . $_POST['profesionalNombre']), 0, 0, "L");
            $pdf->Ln(3);
        } else {
            $pdf->Cell(0, 0, '.', 0, 0, "L");
            $pdf->Ln(3);
        }
        if ($lugar != '') {
            $pdf->Cell(0, 0, ajustarCaracteres("Lugar: " . $_POST['lugarNombre']), 0, 0, "L");
            $pdf->Ln(3);
        } else {
            $pdf->Cell(0, 0, '.', 0, 0, "L");
            $pdf->Ln(3);
        }
        if ($estado != '') {
            $pdf->Cell(0, 0, ajustarCaracteres("Estado de la cita: " . $_POST['estadoNombre']), 0, 0, "L");
            $pdf->Ln(3);
        } else {
            $pdf->Cell(0, 0, '.', 0, 0, "L");
            $pdf->Ln(3);
        }
        if ($cita != '') {
            $pdf->Cell(0, 0, ajustarCaracteres("Palabra clave: " . $cita), 0, 0, "L");
            $pdf->Ln(3);
        } else {
            $pdf->Cell(0, 0, '.', 0, 0, "L");
            $pdf->Ln(3);
        }
        if ($fechaInicial != '' && $fechaFinal == '') {
            $pdf->Cell(0, 0, "Fecha inicial: " . $fechaInicial, 0, 0, "L");
            $pdf->Ln(3);
        } else {
            $pdf->Cell(0, 0, '.', 0, 0, "L");
            $pdf->Ln(3);
        }
        if ($fechaInicial == '' && $fechaFinal != '') {
            $pdf->Cell(0, 0, "Fecha final: " . $fechaFinal, 0, 0, "L");
            $pdf->Ln(3);
        } else {
            $pdf->Cell(0, 0, '.', 0, 0, "L");
            $pdf->Ln(3);
        }
        if ($fechaInicial != '' && $fechaFinal != '') {
            $pdf->Cell(0, 0, "Desde: " . $fechaInicial . " hasta: " . $fechaFinal, 0, 0, "L");
            $pdf->Ln(3);
        } else {
            $pdf->Cell(0, 0, '.', 0, 0, "L");
            $pdf->Ln(3);
        }

        $pdf->Cell(0, 0, "Resultados: " . count($rta_aux), 0, 0, "L");
        $pdf->Ln(3);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(33, 7, 'Fecha', 1, 0, "C");
        $pdf->Cell(16, 7, 'Hora', 1, 0, "C");

        $pdf->Cell(25, 7, 'Documento', 1, 0, "L");
        $pdf->Cell(70, 7, 'Paciente', 1, 0, "C");
        $pdf->Cell(50, 7, 'Convenio', 1, 0, "L");
        $pdf->Cell(50, 7, 'Profesional', 1, 0, "C");
       //$pdf->Cell(30, 7, 'Estado', 1, 0, "C");
        $pdf->Ln();


        $contador = 1;
        foreach ($rta_aux as $value) {
            $pdf->SetFont('Arial', '', 9);
            $fecha_cita_aux = substr($value['fecha_cita'], 0, 10);
            $fecha_cita_aux = strtotime($fecha_cita_aux);
            $pdf->Cell(33, 5, ajustarCaracteres(html_entity_decode($funciones_persona->obtenerFecha4($fecha_cita_aux))), 1, 0, "L");
            //Formato de la hora
            $hora = substr($value['hora_aux'], 0, 2);
            $minutos = substr($value['hora_aux'], 3, 4);
            $hora_cita = operacion_horas(intval($hora), intval($minutos), 1, 0, 12);
            ////////////////////////////////
            $pdf->Cell(16, 5, $hora_cita, 1, 0, "L");


            $pdf->Cell(25, 5, $value['numero_documento'], 1, 0, "L");
            $pdf->Cell(70, 5, ajustarCaracteres($value['nombre_1'] . ' ' . $value['nombre_2'] . ' ' . $value['apellido_1']), 1, 0, "L");
            $pdf->Cell(50, 5, ajustarCaracteres($value['nombre_convenio_aux']), 1, 0, "L");
            $pdf->Cell(50, 5, ajustarCaracteres($value['profesional_aux']), 1, 0, "C");
            //$pdf->Cell(30, 5, ajustarCaracteres($value['nombre_detalle']), 1, 0, "L");

            $pdf->Ln();

            if ($contador >= 30) {//imprime de neuvo el pie de página
                $pdf->AddPage();
                //Se agregan los logos
                $pdf->Image("../imagenes/logo-color.png", 250, 10, 40);

                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(0, 0, "REPORTE DE CITAS", 0, 0, "C");
                $pdf->Ln(0);

                $pdf->SetFont('Arial', "", 10);
                if ($profesional != '') {
                    $pdf->Cell(0, 0, ajustarCaracteres("Profesional: " . $_POST['profesionalNombre']), 0, 0, "L");
                    $pdf->Ln(3);
                } else {
                    $pdf->Cell(0, 0, '.', 0, 0, "L");
                    $pdf->Ln(3);
                }
                if ($lugar != '') {
                    $pdf->Cell(0, 0, ajustarCaracteres("Lugar: " . $_POST['lugarNombre']), 0, 0, "L");
                    $pdf->Ln(3);
                } else {
                    $pdf->Cell(0, 0, '.', 0, 0, "L");
                    $pdf->Ln(3);
                }
                if ($estado != '') {
                    $pdf->Cell(0, 0, ajustarCaracteres("Estado de la cita: " . $_POST['estadoNombre']), 0, 0, "L");
                    $pdf->Ln(3);
                } else {
                    $pdf->Cell(0, 0, '.', 0, 0, "L");
                    $pdf->Ln(3);
                }
                if ($cita != '') {
                    $pdf->Cell(0, 0, ajustarCaracteres("Palabra clave: " . $cita), 0, 0, "L");
                    $pdf->Ln(3);
                } else {
                    $pdf->Cell(0, 0, '.', 0, 0, "L");
                    $pdf->Ln(3);
                }
                if ($fechaInicial != '' && $fechaFinal == '') {
                    $pdf->Cell(0, 0, "Fecha inicial: " . $fechaInicial, 0, 0, "L");
                    $pdf->Ln(3);
                } else {
                    $pdf->Cell(0, 0, '.', 0, 0, "L");
                    $pdf->Ln(3);
                }
                if ($fechaInicial == '' && $fechaFinal != '') {
                    $pdf->Cell(0, 0, "Fecha final: " . $fechaFinal, 0, 0, "L");
                    $pdf->Ln(3);
                } else {
                    $pdf->Cell(0, 0, '.', 0, 0, "L");
                    $pdf->Ln(3);
                }
                if ($fechaInicial != '' && $fechaFinal != '') {
                    $pdf->Cell(0, 0, "Desde: " . $fechaInicial . " hasta: " . $fechaFinal, 0, 0, "L");
                    $pdf->Ln(3);
                } else {
                    $pdf->Cell(0, 0, '.', 0, 0, "L");
                    $pdf->Ln(3);
                }

                $pdf->Cell(0, 0, "Resultados: " . count($rta_aux), 0, 0, "L");
                $pdf->Ln(3);


                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(33, 7, 'Fecha', 1, 0, "C");
                $pdf->Cell(16, 7, 'Hora', 1, 0, "C");

                $pdf->Cell(25, 7, 'Documento', 1, 0, "L");
                $pdf->Cell(70, 7, 'Paciente', 1, 0, "C");
                $pdf->Cell(50, 7, 'Convenio', 1, 0, "L");
                $pdf->Cell(50, 7, 'Profesional', 1, 0, "C");
                //$pdf->Cell(30, 7, 'Estado', 1, 0, "C");
                $pdf->Ln();

                $contador = 0;
            }

            $contador++;
        }




        $pdf->Output();
        //Se guarda el documento pdf
        $nombreArchivo = "../tmp/encuestas_seguimiento_" . $_SESSION["idUsuario"] . ".pdf";
        $pdf->Output($nombreArchivo, "F");
        ?>

        <input type="hidden" name="hdd_archivo_pdf" id="hdd_archivo_pdf" value="<?php echo($nombreArchivo); ?>" />

        <?php
        break;
}
?>
