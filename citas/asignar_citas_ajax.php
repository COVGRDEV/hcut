<?php
session_start();
/*
  Pagina Asignar citas
  Autor: Helio Ruber López - 16/09/2013
 */

header("Content-Type: text/xml; charset=UTF-8");

require_once "../db/DbUsuarios.php";
require_once "../db/DbListas.php";
require_once "../db/DbVariables.php";
require_once "../db/DbCalendario.php";
require_once "../db/DbAsignarCitas.php";
require_once "../db/DbConvenios.php";
require_once "../db/DbPacientes.php";
require_once "../db/DbDisponibilidadProf.php";
require_once "../db/DbTiposCitas.php";
require_once "../db/DbListasEspera.php";
require_once "../db/DbAdmision.php";
require_once "../funciones/Class_Combo_Box.php";
require_once "../funciones/FuncionesPersona.php";
require_once "../funciones/Utilidades.php";
require_once "../principal/ContenidoHtml.php";
require_once "../db/DbPaises.php";
require_once "../db/DbDepartamentos.php";
require_once "../db/DbMunicipios.php";
require_once("../db/DbDepMuni.php");
require_once("../db/DbCitas.php");
require_once("../principal/ContenidoHtml.php");
require_once("../db/DbPlanes.php");


$dbDepMuni = new DbDepMuni();
$dbUsuarios = new DbUsuarios();
$dbListas = new DbListas();
$dbVariables = new Dbvariables();
$dbCalendario = new DbCalendario();
$dbAsignarCitas = new DbAsignarCitas();
$dbConvenios = new DbConvenios();
$dbPacientes = new DbPacientes();
$dbDisponibilidadProf = new DbDisponibilidadProf();
$dbTiposCitas = new DbTiposCitas();
$dbListasEspera = new DbListasEspera();
$dbAdmision = new DbAdmision();
$contenido = new ContenidoHtml();
$contenido->validar_seguridad(1);
$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);
$funciones_persona = new FuncionesPersona();
$utilidades = new Utilidades();
$dbPaises = new DbPaises();
$dbDepartamentos = new DbDepartamentos();
$dbMunicipios = new DbMunicipios();
$dbCitas = new DbCitas();
$contenidoHtml = new ContenidoHtml();
$dbPlanes = new DbPlanes();

$opcion = $_POST["opcion"];

//Devulve la hora en formato 12 horas con la jornada
function mostrar_hora_format($hora, $minutos) {
    $hora = cifras_numero($hora, 2);
    $minutos = cifras_numero($minutos, 2);

    $hora_res = "";
    if ($hora > 12) {
        $hora = $hora - 12;
        $hora_res = $hora . ":" . $minutos . " PM";
    } else {
        $hora_res = $hora . ":" . $minutos . " AM";
    }

    return $hora_res;
}

/*
 * Funcion para sumar y restas minutos
 * $tipo: 1=sumar; 2=restar
 * $cantidad: minutos que se van  aumentar
 * $formato: 12 o 24
 *
 */

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

/**
 * Función para generar un numero con ceros a la izquierda
 * @return
 * @param $consecutivo Object
 * @param $cifras Object
 */
function cifras_numero($consecutivo, $cifras) {
    $longitud = strlen($consecutivo);
    while ($longitud <= $cifras - 1) {
        $consecutivo = "0" . $consecutivo;
        $longitud = strlen($consecutivo);
    }
    return $consecutivo;
}

/**
 * Función que valida si un usuario cumple con las restricciones de perfil para asignación de citas en una franja de horario
 */
function validar_perfil_asignacion_citas($fecha_cal, $hora_ini, $hora_final, $id_usuario_prof, $lista_perfiles, $ind_fecha_hora) {
    $dbDisponibilidadProf = new DbDisponibilidadProf();
    $bol_autorizado = true;

    if ($ind_fecha_hora == 0) {
        $hora_ini = $fecha_cal . " " . $hora_ini;
        $hora_final = $fecha_cal . " " . $hora_final;
    }

    //Se obtiene el listado de perfiles con permisos para la franja
    $lista_perfiles_autorizados = $dbDisponibilidadProf->getListaDisponibilidadProfPerfilesUsuarioHora($id_usuario_prof, $fecha_cal, $hora_ini, $hora_final);
    if (count($lista_perfiles_autorizados) > 0) {
        $bol_autorizado = false;
        foreach ($lista_perfiles as $perfil_aux) {
            foreach ($lista_perfiles_autorizados as $perfil_aut_aux) {
                if ($perfil_aux["id_perfil"] == $perfil_aut_aux["id_perfil"]) {
                    $bol_autorizado = true;
                    break;
                }
            }
            if ($bol_autorizado) {
                break;
            }
        }
    }

    return $bol_autorizado;
}

switch ($opcion) {
    case "1": //Cargar el calendario
        $horarios_lun_vie = $dbVariables->getVariable(2);
        $lista_horarios_lun_vie = explode(";", $horarios_lun_vie["valor_variable"]);
        $horarios_sab = $dbVariables->getVariable(3);
        $lista_horarios_sab = explode(";", $horarios_sab["valor_variable"]);

        /**
         * Función para buscar la disponibilidad de un día específico
         * Valores de retorno:
         * 1: Disponible
         * 2: Disponible - Citas por cancelar
         * 3: No disponible
         * 4: No disponible - Citas por cancelar
         * 5: Sin atención
         * 6: Sin atención - Citas por cancelar
         * 7: No laboral
         */
        function buscar_disponibilidad_dia($calendario_disponible, $lista_usuarios, $lista_usuarios_det, $lista_citas, $lista_horarios_lun_vie, $lista_horarios_sab) {
            $tipo_disponibilidad = 7;
            $ind_cancelar = 0;
            //echo($calendario_disponible["fecha_cal"]);
            //Es un día laboral
            if ($calendario_disponible["ind_laboral"] == "1") {
                $tipo_disponibilidad = 5;
                $lista_horas_completa = array();
                switch ($calendario_disponible["dia_semana"]) {
                    case "2": //Lunes
                    case "3": //Martes
                    case "4": //Miércoles
                    case "5": //Jueves
                    case "6": //Viernes
                        $lista_horas_completa = array(array($lista_horarios_lun_vie[0], $lista_horarios_lun_vie[1]), array($lista_horarios_lun_vie[2], $lista_horarios_lun_vie[3]));
						
                        break;
                    case "7": //Sábado
                        $lista_horas_completa = array(array($lista_horarios_sab[0], $lista_horarios_sab[1]), array("", ""));
                        break;
                }
                $lista_horas_no_disponible = array(array("", ""), array("", ""));
				//var_dump($lista_horas_no_disponible);
                //Se agrupan los datos de las listas por usuario
                $lista_usuarios_det_aux = array();
                foreach ($lista_usuarios_det as $usuario_det_aux) {
                    if (!isset($lista_usuarios_det_aux[$usuario_det_aux["id_usuario"]])) {
                        $lista_usuarios_det_aux[$usuario_det_aux["id_usuario"]] = array();
                    }
                    array_push($lista_usuarios_det_aux[$usuario_det_aux["id_usuario"]], $usuario_det_aux);
                }
                $lista_citas_aux = array();
                foreach ($lista_citas as $cita_aux) {
                    if (!isset($lista_citas_aux[$cita_aux["id_usuario_prof"]])) {
                        $lista_citas_aux[$cita_aux["id_usuario_prof"]] = array();
                    }
                    array_push($lista_citas_aux[$cita_aux["id_usuario_prof"]], $cita_aux);
                }

                //Se verifican las horas límite dependiendo del tipo de disponibilidad y el día de la semana
                foreach ($lista_usuarios as $usuario_aux) {
                    $id_usuario_aux = $usuario_aux["id_usuario"];
                    $lista_horas_dia = array();
                    switch ($usuario_aux["id_tipo_disponibilidad"]) {
                        case "11": //Completa
                            $lista_horas_dia = $lista_horas_completa;
                            if ($tipo_disponibilidad != 1 && $tipo_disponibilidad != 99) {
                                $tipo_disponibilidad = 3;
                            }
                            break;
                        case "12": //Parcial
                            if (isset($lista_usuarios_det_aux[$id_usuario_aux])) {
                                foreach ($lista_usuarios_det_aux[$id_usuario_aux] as $detalle_aux) {
                                    $hora_ini_aux = substr($detalle_aux["hora_ini"], 11, 5);
                                    $hora_fin_aux = substr($detalle_aux["hora_final"], 11, 5);
                                    array_push($lista_horas_dia, array($hora_ini_aux, $hora_fin_aux));
                                }
                            } else {
                                $lista_horas_dia = $lista_horas_no_disponible;
                            }

                            if ($tipo_disponibilidad != 1 && $tipo_disponibilidad != 99) {
                                $tipo_disponibilidad = 3;
                            }
                            break;
                        default: //No disponible
                            $lista_horas_dia = $lista_horas_no_disponible;
                            if ($tipo_disponibilidad != 1 && $tipo_disponibilidad != 99) {
                                $tipo_disponibilidad = 5;
                            }
                            break;
                    }

                    if (isset($lista_citas_aux[$id_usuario_aux])) {
                        //El usuario tiene citas programadas
                        if ($lista_horas_dia[0][0] == "") {
                            $ind_cancelar = 1;
                        } else {
                            //Se buscan citas fuera de tiempo
                            foreach ($lista_citas_aux[$id_usuario_aux] as $cita_aux) {
                                $hora_cita_aux = substr($cita_aux["fecha_cita"], 11, 5);
                                for ($k = 0; $k < count($lista_horas_dia); $k++) {
                                    if ($hora_cita_aux < $lista_horas_dia[$k][0] && ($k == 0 || $hora_cita_aux >= $lista_horas_dia[$k - 1][1])) {
                                        $ind_cancelar = 1;
                                        break;
                                    } else if ($hora_cita_aux >= $lista_horas_dia[$k][1] && ($k == (count($lista_horas_dia) - 1) || $hora_cita_aux < $lista_horas_dia[$k + 1][0])) {
                                        $ind_cancelar = 1;
                                        break;
                                    }
                                }
                            }

                            //Se buscan espacios disponibles en las citas
                            $bol_disponible = false;
                            foreach ($lista_horas_dia as $horas_dia_aux) {
								
                                if ($bol_disponible) {
                                    break;
                                }
                                if ($horas_dia_aux[0] != "") {
                                    $arr_hora_ini_aux = explode(":", $horas_dia_aux[0]);
                                    $arr_hora_fin_aux = explode(":", $horas_dia_aux[1]);
                                    $hora_ini_aux = intval($arr_hora_ini_aux[0]);
                                    $hora_fin_aux = intval($arr_hora_fin_aux[0]);

                                    $minuto_fin_aux = intval($arr_hora_fin_aux[1]);
                                    $hora_fin_aux2 = $hora_fin_aux;
                                    if ($minuto_fin_aux > 0) {
                                        $hora_fin_aux2++;
                                    }

                                    for ($hora_aux = $hora_ini_aux; $hora_aux < $hora_fin_aux2 && !$bol_disponible; $hora_aux++) {
                                        $minuto_ini_aux = 0;
                                        $minuto_fin_aux = 60;
                                        if ($hora_aux == $hora_ini_aux) {
                                            $minuto_ini_aux = intval($arr_hora_ini_aux[1]);
                                        }
                                        if ($hora_aux == $hora_fin_aux && intval($arr_hora_fin_aux[1]) > 0) {
                                            $minuto_fin_aux = intval($arr_hora_fin_aux[1]);
                                        }

                                        for ($minuto_aux = $minuto_ini_aux; $minuto_aux < $minuto_fin_aux && !$bol_disponible; $minuto_aux += 5) {
                                            $hora_consulta = cifras_numero($hora_aux, 2) . ":" . cifras_numero($minuto_aux, 2);
											
                                            //Se busca en las citas del día si hay espacio disponible
                                            $bol_disponible = true;
                                            foreach ($lista_citas_aux[$id_usuario_aux] as $cita_aux) {
                                                $hora_ini_cita_aux = substr($cita_aux["fecha_cita"], 11, 5);
                                                $hora_fin_cita_aux = substr($cita_aux["fecha_fin_cita"], 11, 5);

                                                if ($hora_consulta >= $hora_ini_cita_aux && $hora_consulta < $hora_fin_cita_aux) {
                                                    $bol_disponible = false;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            if ($bol_disponible) {
                                $tipo_disponibilidad = 1;
                            }
                        }
                    } else {
                        //El usuario no tiene citas programadas
                        if ($lista_horas_dia[0][0] != "") {
                            $tipo_disponibilidad = 99;
                        }
                    }
                }
            }

            if ($tipo_disponibilidad == 99) {
                $tipo_disponibilidad = 1;
            }
            if ($ind_cancelar == 1) {
                switch ($tipo_disponibilidad) {
                    case 1: //Disponible
                    case 3: //No disponible
                    case 5: //Sin atención
                        $tipo_disponibilidad++;
                        break;
                }
            }

            //echo("-".$tipo_disponibilidad."<br />");
            return $tipo_disponibilidad;
        }

        //Usuario selecionado
        $id_usuario = $utilidades->str_decode($_POST["usuario"]);

        //Array para guardar la fecha
        $fecha_calendario = array();

        //Variables de reasignación de citas
        @$ind_reasignar = $utilidades->str_decode($_POST["ind_reasignar"]);
        @$id_cita_reasignar = $utilidades->str_decode($_POST["id_cita_reasignar"]);

        /* Vericicacion de el mes u el anio */
        if ($_POST["mes"] == "" || $_POST["anio"] == "") {
            $fecha_hoy = $dbCalendario->getFechaActualComponentes();

            $fecha_calendario[1] = intval($fecha_hoy["mes"]);
            if ($fecha_calendario[1] < 10) {
                $fecha_calendario[1] = "0" . $fecha_calendario[1];
            }
            $fecha_calendario[0] = $fecha_hoy["anio"];
        } else {
            $fecha_calendario[1] = intval($_POST["mes"]);
            if ($fecha_calendario[1] < 10) {
                $fecha_calendario[1] = "0" . $fecha_calendario[1];
            } else {
                $fecha_calendario[1] = $fecha_calendario[1];
            }
            $fecha_calendario[0] = $utilidades->str_decode($_POST["anio"]);
        }
        $fecha_calendario[2] = "01";

        /* obtenemos el dia de la semana del 1 del mes actual */
        $primeromes = date("N", mktime(0, 0, 0, $fecha_calendario[1], 1, $fecha_calendario[0]));

        /* comprobamos si el anio es bisiesto y creamos array de d�¿ias */
        if (($fecha_calendario[0] % 4 == 0) && (($fecha_calendario[0] % 100 != 0) || ($fecha_calendario[0] % 400 == 0))) {
            $dias = array("", "31", "29", "31", "30", "31", "30", "31", "31", "30", "31", "30", "31");
        } else {
            $dias = array("", "31", "28", "31", "30", "31", "30", "31", "31", "30", "31", "30", "31");
        }

        $meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

        /* calculamos los dias de la semana anterior al dia 1 del mes en curso */
        $diasantes = $primeromes - 1;

        /* los dias totales de la tabla siempre seran maximo 42 (7 dias x 6 filas maximo) */
        $diasdespues = 42;

        //Calculamos las filas de la tabla
        $tope = $dias[intval($fecha_calendario[1])] + $diasantes;
        if ($tope % 7 != 0) {
            $totalfilas = intval(($tope / 7) + 1);
        } else {
            $totalfilas = intval(($tope / 7));
        }

        //Se obtiene la disponibilidad del CALENDARIO
        $calendario_disponible = $dbCalendario->getCalendario($fecha_calendario[1], $fecha_calendario[0]);
        $lista_calendario_disponible = array();
        foreach ($calendario_disponible as $calendario_aux) {
            $lista_calendario_disponible[$calendario_aux["fecha_cal"]] = $calendario_aux;
        }

        //Se obtiene la disponibilidad de los USUARIOS
        $calendario_usuarios = $dbAsignarCitas->getDisponibilidadUsuariosMes($fecha_calendario[1], $fecha_calendario[0], $id_usuario);
        $lista_calendario_usuarios = array();
        foreach ($calendario_usuarios as $calendario_aux) {
            if (!isset($lista_calendario_usuarios[$calendario_aux["fecha_cal"]])) {
                $lista_calendario_usuarios[$calendario_aux["fecha_cal"]] = array();
            }
            array_push($lista_calendario_usuarios[$calendario_aux["fecha_cal"]], $calendario_aux);
        }

        //Se obtiene la disponibilidad de detalle de los USUARIOS
        $calendario_usuarios_det = $dbAsignarCitas->getDisponibilidadDetaUsuariosMes($fecha_calendario[1], $fecha_calendario[0], $id_usuario);
        $lista_calendario_usuarios_det = array();
        foreach ($calendario_usuarios_det as $calendario_aux) {
            if (!isset($lista_calendario_usuarios_det[$calendario_aux["fecha_cal"]])) {
                $lista_calendario_usuarios_det[$calendario_aux["fecha_cal"]] = array();
            }
            array_push($lista_calendario_usuarios_det[$calendario_aux["fecha_cal"]], $calendario_aux);
        }

        //Se obtienen las citas del mes
        $citas_mes = $dbAsignarCitas->getListaCitasMes($fecha_calendario[1], $fecha_calendario[0], $id_usuario);
        $lista_citas_mes = array();
        foreach ($citas_mes as $cita_aux) {
            $fecha_aux = substr($cita_aux["fecha_cita"], 0, 10);
            if (!isset($lista_citas_mes[$fecha_aux])) {
                $lista_citas_mes[$fecha_aux] = array();
            }
            array_push($lista_citas_mes[$fecha_aux], $cita_aux);
        }

        $fechaanterior = date("Y-m-d", mktime(0, 0, 0, $fecha_calendario[1] - 1, 01, $fecha_calendario[0]));
        $fechasiguiente = date("Y-m-d", mktime(0, 0, 0, $fecha_calendario[1] + 1, 01, $fecha_calendario[0]));

        $anio_siguiente = substr($fechasiguiente, 0, 4);
        $mes_siguiente = substr($fechasiguiente, 5, 2);

        $anio_anterior = substr($fechaanterior, 0, 4);
        $mes_anterior = substr($fechaanterior, 5, 2);

        //Empezamos a pintar la tabla
        ?>
        <div class="contenido wrapper clearfix">
            <div class="calendario-container volumen" style="width:66%;">
                <div class="encabezado">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td align="center" valign="bottom" width="10%">
                                <a href="#" title="Mes anterior" onclick="calendario('<?php echo $mes_anterior ?>', '<?php echo $anio_anterior ?>');"><img border="0" src="../imagenes/btn-mes-ant.png" /></a>
                            </td>
                            <td align="center" width="80%"><h2><?php echo ($meses[intval($fecha_calendario[1])] . " " . $fecha_calendario[0]); ?></h2></td>
                            <td align="center" valign="bottom" width="10%">
                                <a href="#" title="Mes siguiente" onclick="calendario('<?php echo $mes_siguiente ?>', '<?php echo $anio_siguiente ?>');"><img border="0" src="../imagenes/btn-mes-sig.png" /></a>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
                if (isset($mostrar)) {
                    echo $mostrar;
                }
                ?>
                <table class="calendario" cellspacing="0" cellpadding="0">
                    <tr>
                        <th style="width:14%;">LUN</th>
                        <th style="width:14%;">MAR</th>
                        <th style="width:14%;">MI&Eacute;</th>
                        <th style="width:14%;">JUE</th>
                        <th style="width:14%;">VIE</th>
                        <th style="width:14%;">S&Aacute;B</th>
                        <th style="width:14%;">DOM</th>
                    </tr>
                    <tr>
                        <?php
//inicializamos filas de la tabla
                        $tr = 0;
                        $dia = 1;

                        //Se busca la fecha actual
                        $fecha_actual = $dbCalendario->getFechaActual();

                        for ($i = 1; $i <= $diasdespues; $i++) {
                            if ($tr < $totalfilas) {
                                if ($i >= $primeromes && $i <= $tope) {
                                    //creamos fecha completa
                                    if ($dia < 10) {
                                        $dia_actual = "0" . $dia;
                                    } else {
                                        $dia_actual = $dia;
                                    }
                                    $fecha_completa = $fecha_calendario[0] . "-" . $fecha_calendario[1] . "-" . $dia_actual;

                                    //Se busca la disponibilidad del la fecha
                                    $lista_disponible_aux = array();
                                    if (isset($lista_calendario_disponible[$fecha_completa])) {
                                        $calendario_disponible_aux = $lista_calendario_disponible[$fecha_completa];
                                    }

                                    $lista_usuarios_aux = array();
                                    if (isset($lista_calendario_usuarios[$fecha_completa])) {
                                        $lista_usuarios_aux = $lista_calendario_usuarios[$fecha_completa];
                                    }

                                    $lista_usuarios_det_aux = array();
                                    if (isset($lista_calendario_usuarios_det[$fecha_completa])) {
                                        $lista_usuarios_det_aux = $lista_calendario_usuarios_det[$fecha_completa];
                                    }

                                    $lista_citas_aux = array();
                                    if (isset($lista_citas_mes[$fecha_completa])) {
                                        $lista_citas_aux = $lista_citas_mes[$fecha_completa];
                                    }

                                    $estado = buscar_disponibilidad_dia($calendario_disponible_aux, $lista_usuarios_aux, $lista_usuarios_det_aux, $lista_citas_aux, $lista_horarios_lun_vie, $lista_horarios_sab);
                                    $estilo_celda_aux = "";
                                    $funcion_celda_aux = "";
                                    switch ($estado) {
                                        case 1: //Disponible
                                            $estilo_celda_aux = "disponible";
                                            $funcion_celda_aux = "disponible";
                                            break;
                                        case 2: //Disponible - Citas por cancelar
                                            $estilo_celda_aux = "disponible citas_por_cancelar";
                                            $funcion_celda_aux = "disponible";
                                            break;
                                        case 3: //No disponible
                                            $estilo_celda_aux = "no_disponible";
                                            $funcion_celda_aux = "no_disponible";
                                            break;
                                        case 4: //No disponible - Citas por cancelar
                                            $estilo_celda_aux = "no_disponible citas_por_cancelar";
                                            $funcion_celda_aux = "no_disponible";
                                            break;
                                        case 5: //Sin atención
                                            $estilo_celda_aux = "sin_atencion";
                                            $funcion_celda_aux = "no_disponible";
                                            break;
                                        case 6: //Sin atención - Citas por cancelar
                                            $estilo_celda_aux = "sin_atencion citas_por_cancelar";
                                            $funcion_celda_aux = "no_disponible";
                                            break;
                                        case 7: //No laboral
                                            $estilo_celda_aux = "sin_atencion";
                                            $funcion_celda_aux = "sin_atencion";
                                            break;
                                    }

                                    $estilo_celda_aux2 = "";
                                    if ($fecha_actual["fecha_hoy"] > $fecha_completa) {
                                        $estilo_celda_aux2 = " opaco";
                                    }

                                    $estilo_completo_aux = $estilo_celda_aux . $estilo_celda_aux2;
                                    if ($fecha_actual["fecha_hoy"] == $fecha_completa) {
                                        $estilo_completo_aux .= " hoy";
                                    }
                                    ?>
                                    <td class="<?php echo ($estilo_completo_aux); ?>" onclick="<?php echo $funcion_celda_aux; ?>('<?php echo $fecha_completa; ?>', 0);">
                                        <?php echo $dia; ?>
                                    </td>
                                    <?php
                                    $dia += 1;
                                } else {
                                    ?>
                                    <td class="desactivada" style="width:14%">&nbsp;</td>
                                    <?php
                                }

                                if ($i == 7 || $i == 14 || $i == 21 || $i == 28 || $i == 35 || $i == 42) {
                                    ?>
                                <tr>
                                    <?php
                                    $tr += 1;
                                }
                            }
                        }
                        ?>
                </table>
                <div class="convenciones clearfix">
                    <div class="disponibles">
                        <p>Citas Disponibles</p>
                    </div>
                    <div class="no-disponibles">
                        <p>Citas No Disponibles</p>
                    </div>
                    <div class="sin-atencion">
                        <p>D&iacute;a Sin Atenci&oacute;n</p>
                    </div>
                    <div class="sin-asignar">
                        <p>Citas Pendientes por Cancelar</p>
                    </div>
                </div>
                <p>&laquo; <a href="#" onclick="calendario('<?php echo $mes_anterior ?>', '<?php echo $anio_anterior ?>');" class="anterior">Mes Anterior</a> -
                    <a href="#" onclick="calendario('<?php echo $mes_siguiente ?>', '<?php echo $anio_siguiente ?>');" class="siguiente">Mes Siguiente</a> &raquo;</p>
            </div>
            <?php
//Se obtiene el listado de perfiles del usuario
            $lista_perfiles_usuario = $dbUsuarios->getListaPerfilUsuarios($_SESSION["idUsuario"]);
            ?>
            <div class="disponibilidad volumen">
                <div class="encabezado">
                    <h6>Disponibilidad m&aacute;s cercana</h6>
                </div>
                <div class="listado-disponibilidad">
                    <ul>
                        <?php
                        $tabla_usuarios_citas = array(); //$dbAsignarCitas->getListaUsuariosCitas($id_usuario, 1);

                        foreach ($tabla_usuarios_citas as $fila_usuarios) { //Inicio For usuario citas
                            $usuario = $fila_usuarios["id_usuario"];
                            $nombre_completo = $fila_usuarios["nombre_completo"];
                            $nombre_usuario = $fila_usuarios["nombre_usuario"];
                            $apellido_usuario = $fila_usuarios["apellido_usuario"];
                            $ind_disponible = intval($fila_usuarios["ind_disponible"]);

                            if ($ind_disponible == 0) {
                                ?>
                                <li><?php echo $nombre_usuario . " " . $apellido_usuario ?><span>No tiene disponibilidad cercana</span></li>
                                <?php
                                $tabla_disponibilidad = array();
                            } else {
                                $tabla_disponibilidad = $dbAsignarCitas->getDisponibilidadUsuarios($usuario);
                            }

                            foreach ($tabla_disponibilidad as $fila_usuarios_disponibles) { //Inicio For disponibilidad
                                $nombre_usuario = $fila_usuarios_disponibles["nombre_usuario"];
                                $apellido_usuario = $fila_usuarios_disponibles["apellido_usuario"];
                                $tipo_disponibilidad = $fila_usuarios_disponibles["id_tipo_disponibilidad"];
                                $id_disponibilidad = $fila_usuarios_disponibles["id_disponibilidad"];
                                $id_lugar_disp = $fila_usuarios_disponibles["id_lugar_disp"];
                                $lugar_disp = $fila_usuarios_disponibles["lugar_disp"];
                                $fecha = date($fila_usuarios_disponibles["fecha_cal"]);
                                $i = strtotime($fecha);
                                $dia_semana = jddayofweek(cal_to_jd(CAL_GREGORIAN, date("m", $i), date("d", $i), date("Y", $i)), 0); //Devuelve un valor entre 1 y 7

                                if ($dia_semana == 6) { //Para atencion los sabados
                                    $horarios = $horarios_sab;
                                } else if ($dia_semana >= 1 && $dia_semana <= 5) { //Para atencion de lunes a viernes
                                    $horarios = $horarios_lun_vie;
                                } else {
                                    $horarios = $horarios_lun_vie;
                                }

                                //Se divide la cadena de la varariable hora para tener las horas disponibles
                                $horas = explode(";", $horarios["valor_variable"]);
                                $horarios_disponibles = $horarios["valor_variable"];

                                if ($tipo_disponibilidad == 12) { //La disponibilidad de la cita tiene detalle
                                    $detalle_disponibilidad = $dbAsignarCitas->getDisponibilidadUsuariosDiaDetalle($id_disponibilidad);
                                    $horarios_disponibles = "";
                                    foreach ($detalle_disponibilidad as $fila_detalle) {
                                        $fecha_ini = $fila_detalle["hora_ini"];
                                        $fecha_fin = $fila_detalle["hora_final"];
                                        $hora_ini = substr($fecha_ini, 11, 5);
                                        $hora_fin = substr($fecha_fin, 11, 5);

                                        //Se verifica si el perfil tiene autorización en la franja
                                        $bol_perfil_autorizado = validar_perfil_asignacion_citas($fila_usuarios_disponibles["fecha_cal"], $fila_detalle["hora_ini"], $fila_detalle["hora_final"], $fila_usuarios_disponibles["id_usuario"], $lista_perfiles_usuario, 1);

                                        if ($bol_perfil_autorizado) {
                                            if ($horarios_disponibles != "") {
                                                $horarios_disponibles .= ";";
                                            }
                                            $horarios_disponibles .= $hora_ini . ";" . $hora_fin;
                                        }
                                    }

                                    //Se divide la cadena de la varariable hora para tener las horas disponibles
                                    if ($horarios_disponibles != "") {
                                        $horas = explode(";", $horarios_disponibles);
                                    } else {
                                        $horas = array();
                                    }
                                } //Fin del IF

                                if (count($horas) > 0) {
                                    $arr_fechas_horas_aux = array();
                                    foreach ($horas as $hora_aux) {
                                        array_push($arr_fechas_horas_aux, $fecha . " " . $hora_aux);
                                    }
                                    $tabla_horas_citas = $dbAsignarCitas->getFechaHoraCita2($usuario, $arr_fechas_horas_aux, 1);
                                } else {
                                    $tabla_horas_citas = array();
                                }
                                if (count($tabla_horas_citas) > 0) {
                                    $tiempo_diponible = 0;
                                    for ($k = 0; $k < count($tabla_horas_citas); $k++) {
                                        $fila_horas_citas = $tabla_horas_citas[$k];
                                        $fecha_hora_cita = $fila_horas_citas["fecha_cita"];
                                        $fecha_cita = substr($fecha_hora_cita, 0, 10);
                                        $hora_aux = substr($fecha_hora_cita, 11, 5);
                                        $tipo_tiempo = intval($fila_horas_citas["tipo_tiempo"]);

                                        if ($fecha_cita == $fecha) {
                                            $bol_continuar_aux = false;
                                            if (isset($horas[2]) && $horas[2] != "") {
                                                if (($hora_aux >= $horas[0] && $hora_aux < $horas[1]) || ($hora_aux >= $horas[2] && $hora_aux < $horas[3])) {
                                                    $bol_continuar_aux = true;
                                                }
                                            } else {
                                                if ($hora_aux >= $horas[0] && $hora_aux < $horas[1]) {
                                                    $bol_continuar_aux = true;
                                                }
                                            }

                                            if ($bol_continuar_aux) {
                                                //Si es una hora de inicio de bloque vacío
                                                if ($tipo_tiempo != 2 && $tipo_tiempo != 3 && $tipo_tiempo != 5) {
                                                    $bol_continuar_aux = false;
                                                }
                                            }

                                            if ($bol_continuar_aux) {
                                                //Se valida que no hayan citas en curso en la hora a revisar
                                                $lista_citas_en_curso = $dbAsignarCitas->getListaCitasEnCurso($fecha_hora_cita, $usuario);
                                                if (count($lista_citas_en_curso) > 0) {
                                                    $bol_continuar_aux = false;
                                                }
                                            }

                                            if ($bol_continuar_aux) {
                                                //Se verifica si se debe ajustar la hora de la cita
                                                $val_hora_aux = substr($fecha_hora_cita, 11, 8);
                                                $arr_hora_aux = explode(":", $val_hora_aux);
                                                if (intval($arr_hora_aux[1]) % 5 != 0 || intval($arr_hora_aux[2]) != 0) {
                                                    $hora_aux = intval($arr_hora_aux[0]);
                                                    $minuto_aux = (floor(intval($arr_hora_aux[1]) / 5) + 1) * 5;
                                                    if ($minuto_aux >= 60) {
                                                        $hora_aux++;
                                                        $minuto_aux = 0;
                                                    }
                                                    $hora_aux = cifras_numero($hora_aux, 2);
                                                    $minuto_aux = cifras_numero($minuto_aux, 2);
                                                    $val_fecha_aux = substr($fecha_hora_cita, 0, 10);

                                                    $fecha_hora_cita = $val_fecha_aux . " " . $hora_aux . ":" . $minuto_aux . ":00";
                                                }

                                                //Se valida si el bloque disponible sirve dependiendo del tipo de tiempo del próximo registro
                                                $ind_hallado = false;
                                                if (isset($tabla_horas_citas[$k + 1])) {
                                                    $tipo_tiempo_prox = $tabla_horas_citas[$k + 1]["tipo_tiempo"];
                                                    if ($fecha_hora_cita != $tabla_horas_citas[$k + 1]["fecha_cita"] && ($tipo_tiempo_prox == 1 || $tipo_tiempo_prox == 4)) {
                                                        $ind_hallado = true;
                                                    }
                                                }

                                                if ($ind_hallado) {
                                                    $tiempo_diponible = $dbAsignarCitas->getTiempoFechas($fecha_hora_cita, $tabla_horas_citas[$k + 1]["fecha_cita"]);
                                                    $tiempo_diponible = $tiempo_diponible["tiempo_minutos"];
                                                    if ($tiempo_diponible > 0) {
                                                        $horamin_inicial_cita = explode(":", substr($fecha_hora_cita, 11, 5));
                                                        $hora_inicial_cita = $horamin_inicial_cita[0];
                                                        $minuto_inicial_cita = $horamin_inicial_cita[1];

                                                        $hora_desde = "";
                                                        $hora_desde = operacion_horas($hora_inicial_cita, $minuto_inicial_cita, 1, 0, 12);
                                                        $hora_hasta = "";
                                                        $hora_hasta = operacion_horas($hora_inicial_cita, $minuto_inicial_cita, 1, $tiempo_diponible, 12);

                                                        //Horas para crear citas
                                                        $hora_desde_cita = operacion_horas($hora_inicial_cita, $minuto_inicial_cita, 1, 0, 24);
                                                        $hora_hasta_cita = operacion_horas($hora_inicial_cita, $minuto_inicial_cita, 1, $tiempo_diponible, 24);

                                                        $fecha_mostrar = $funciones_persona->obtenerFecha4(strtotime($fecha));
                                                        ?>
                                                        <li
                                                        <?php
                                                        if ($tipo_acceso_menu == 2) {
                                                            ?>
                                                                onclick='
                                                                <?php
                                                                if ($ind_reasignar != 1) {
                                                                    ?>
                                                                                                                        abrir_crear_cita("<?php echo $fecha; ?>", "<?php echo $hora_desde_cita; ?>", "<?php echo $hora_hasta_cita; ?>", "<?php echo $usuario; ?>", "<?php echo ($id_lugar_disp); ?>");
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                                                                        abrir_crear_cita_paciente("<?php echo ($fecha); ?>", "<?php echo ($hora_desde_cita); ?>", "<?php echo ($hora_hasta_cita); ?>", "<?php echo ($usuario); ?>", "", "<?php echo ($id_cita_reasignar); ?>", "<?php echo ($id_lugar_disp); ?>", <?php echo ($ind_reasignar); ?>);
                                                                    <?php
                                                                }
                                                                ?>
                                                                '
                                                                <?php
                                                            }
                                                            ?>
                                                            >
                                                            <?php echo $nombre_usuario . " " . $apellido_usuario ?><span><?php echo ($lugar_disp . "<br />" . $fecha_mostrar . " - " . $hora_desde); ?></span>
                                                        </li>
                                                        <?php
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if ($tiempo_diponible > 0) {
                                        break;
                                    }
                                }
                            } //Fin For disponibilidad
                        } //Fin For usuario citas
                        ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php
        break;

    case "2": //Cargar el horario y las citas que tienen asignadas cada usuario
        //Fecha del dia actual
        $fecha_actual = $dbCalendario->getFechaActual();

        $fecha = $utilidades->str_decode($_POST["fecha"]);
        $usuario = $utilidades->str_decode($_POST["usuario"]);
        $id_usuario_prof = $utilidades->str_decode($_POST["id_usuario_prof"]);
        $ind_reasignar = intval($_POST["ind_reasignar"], 10);
        $id_cita_reasignar = intval($_POST["id_cita_reasignar"], 10);

        $fecha_mostrar = $funciones_persona->obtenerFecha5(strtotime($fecha));

        $permitir_accion = 1;
        if ($fecha_actual["fecha_hoy"] > $fecha) {
            $permitir_accion = 0;
        }
        ?>
        <div class="encabezado">
            <h3>Disponibilidad de Citas - <?php echo ($fecha_mostrar); ?></h3>
            <?php
            if ($permitir_accion == 0) {
                ?>
                <h4>D&iacute;a anterior a la fecha actual, no se pueden realizar cambios</h4>
                <?php
            }
            ?>
        </div>
        <?php
        //Se obtiene el listado de perfiles del usuario
        $lista_perfiles_usuario = $dbUsuarios->getListaPerfilUsuarios($_SESSION["idUsuario"]);

        $i = strtotime($fecha);
        $dia_semana = jddayofweek(cal_to_jd(CAL_GREGORIAN, date("m", $i), date("d", $i), date("Y", $i)), 0); //Devuelve un valor entre 1 y 7
        //Disponibilidad de los usuarios en el dia
        $tabla_usuarios_disponibles = $dbAsignarCitas->getDisponibilidadUsuariosDia($fecha, $usuario);

        //Si hay más de 3 usuarios se muestran botones de desplazamiento
        $indice_mostrar_ini = 0;
        if (count($tabla_usuarios_disponibles) > 3) {
            if ($id_usuario_prof != "0") {
                //Se busca el inicio de la vista para que muestre el usuario
                for ($i = 0; $i < count($tabla_usuarios_disponibles); $i++) {
                    $usuario_aux = $tabla_usuarios_disponibles[$i];
                    if ($usuario_aux["id_usuario"] == $id_usuario_prof) {
                        $indice_mostrar_ini = $i;
                        break;
                    }
                }
            }
            if ($indice_mostrar_ini < 3) {
                $indice_mostrar_ini = 0;
            } else if ($indice_mostrar_ini > count($tabla_usuarios_disponibles) - 3) {
                $indice_mostrar_ini = count($tabla_usuarios_disponibles) - 3;
            }
            ?>
            <script id="ajax">
                cont_cal_actual = <?php echo ($indice_mostrar_ini); ?>;
                cont_cal_total = <?php echo (count($tabla_usuarios_disponibles)); ?>;
            </script>
            <?php
            $val_ocultar_ini = "";
            $val_ocultar_fin = "";
            if ($indice_mostrar_ini == 0) {
                $val_ocultar_ini = " ocultar";
            }
            if ($indice_mostrar_ini >= count($tabla_usuarios_disponibles) - 3) {
                $val_ocultar_fin = " ocultar";
            }
            ?>
            <a id="a_btn_prev" title="" href="#" class="btn_prev<?php echo ($val_ocultar_ini); ?>" onclick="mostrar_cal_anterior();"></a>
            <a id="a_btn_next" title="" href="#" class="btn_next<?php echo ($val_ocultar_fin); ?>" onclick="mostrar_cal_siguiente();"></a>
            <?php
        }
        ?>
        <div id="d_horarios_profesionales" class="horarios_profesionales">
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <?php
                    $cont_usuarios_cal = 0;
                    foreach ($tabla_usuarios_disponibles as $fila_usuarios_disponibles) {
                        $id_usuario = $fila_usuarios_disponibles["id_usuario"];
                        $nombre_usuario = $fila_usuarios_disponibles["nombre_usuario"];
                        $apellido_usuario = $fila_usuarios_disponibles["apellido_usuario"];
                        $tipo_disponibilidad = $fila_usuarios_disponibles["id_tipo_disponibilidad"];
                        $id_disponibilidad = $fila_usuarios_disponibles["id_disponibilidad"];
                        $id_lugar_disp = $fila_usuarios_disponibles["id_lugar_disp"];
                        $lugar_disp = $fila_usuarios_disponibles["lugar_disp"];

                        //Si la disponibilidad del día es parcial se verifica si hay más de un lugar
                        $arr_lugar_disp = array();
                        $bol_lugar_mult = false;
                        if ($tipo_disponibilidad == "12") {
                            $lista_disp_prof_det = $dbDisponibilidadProf->getDisponibilidadProfDet($id_disponibilidad);

                            if (count($lista_disp_prof_det) > 0) {
                                $id_lugar_disp = $lista_disp_prof_det[0]["id_lugar_disp"];
                                $lugar_disp = $lista_disp_prof_det[0]["lugar_disp"];

                                //Se verifica si hay más de un lugar de atención
                                if (count($lista_disp_prof_det) > 1) {
                                    foreach ($lista_disp_prof_det as $lugar_disp_aux) {
                                        if (!in_array($lugar_disp_aux["lugar_disp"], $arr_lugar_disp)) {
                                            array_push($arr_lugar_disp, $lugar_disp_aux["lugar_disp"]);
                                        }
                                    }
                                    if (count($arr_lugar_disp) > 1) {
                                        $bol_lugar_mult = true;
                                    }
                                }
                            }
                        }
                        //Se buscan las citas que el usuario tiene durante el día
                        $lista_citas_dia = $dbAsignarCitas->getListaCitasFecha($id_usuario, $fecha);
                        $estilo_visible_aux = "block";
                        if ($cont_usuarios_cal < $indice_mostrar_ini || $cont_usuarios_cal > $indice_mostrar_ini + 2) {
                            $estilo_visible_aux = "none";
                        }
                        ?>
                        <td valign="top">
                            <div class="profesional" id="d_profesional_<?php echo ($cont_usuarios_cal); ?>" style="display:<?php echo ($estilo_visible_aux); ?>;">
                                <div class="nombre">
                                    <?php
                                    $texto_lugar_disp = "";
                                    if ($bol_lugar_mult) {
                                        foreach ($arr_lugar_disp as $lugar_disp_aux) {
                                            if ($texto_lugar_disp != "") {
                                                $texto_lugar_disp .= " - ";
                                            }
                                            $texto_lugar_disp .= $lugar_disp_aux;
                                        }
                                    } else {
                                        $texto_lugar_disp = $lugar_disp;
                                    }
                                    ?>
                                    <h4><?php echo ($nombre_usuario . " " . $apellido_usuario . " (" . count($lista_citas_dia) . ")"); ?></h4><br />
                                    <h5><?php echo ($texto_lugar_disp); ?></h5>
                                </div>
                                <div class="detalle">
                                    <?php
//DISPONIBILIDAD COMPLETA
                                    if ($dia_semana == 6) { //Para atencion los sabados
                                        $horarios = $dbVariables->getVariable(3);
                                    } else { //Los demás días
                                        $horarios = $dbVariables->getVariable(2);
                                    }

                                    //Se divide la cadena de la varariable hora para tener las horas disponibles
                                    $horas_completas = explode(";", $horarios["valor_variable"]);

                                    //DISPONIBILIDAD PARCIAL
                                    if ($tipo_disponibilidad == 12) { //La disponibilidad de la cita tiene detalle
                                        $detalle_disponibilidad = $dbAsignarCitas->getDisponibilidadUsuariosDiaDetalle($id_disponibilidad);
										
                                        $horarios_disponibles = "";
                                        $cant_dis = count($detalle_disponibilidad);
                                        $band_fin = 1;
                                        foreach ($detalle_disponibilidad as $fila_detalle) {
                                            $fecha_ini = $fila_detalle["hora_ini"];
                                            $fecha_fin = $fila_detalle["hora_final"];
                                            $hora_ini = substr($fecha_ini, 11, 5);
                                            $hora_fin = substr($fecha_fin, 11, 5);

                                            if ($band_fin == $cant_dis) {
                                                $horarios_disponibles .= $hora_ini . ";" . $hora_fin;
                                            } else {
                                                $horarios_disponibles .= $hora_ini . ";" . $hora_fin . ";";
                                            }
                                            $band_fin++;
                                        }

                                        //Se divide la cadena de la varariable hora para tener las horas disponibles
                                        $horas_parciales = explode(";", $horarios_disponibles);
                                    } else {
                                        $horas_parciales = $horas_completas;
                                    }

                                    $horas = $horas_parciales;
								

                                    //Se busca si hay citas que inicien antes de la hora inicial o terminen después de la hora final
                                    foreach ($lista_citas_dia as $cita_aux) {
                                        $hora_ini_cita_aux = substr($cita_aux["fecha_cita"], 11, 5);
                                        $hora_fin_cita_aux = substr($cita_aux["fecha_fin_cita"], 11, 5);
                                        for ($k = 0; $k < count($horas); $k++) {
                                            if ($k % 2 == 0) {
                                                //Inicios de bloque
                                                if ($hora_ini_cita_aux < $horas[$k]) {
                                                    //Citas anteriores a horas iniciales de bloque
                                                    if ($k == 0) {
                                                        $horas[$k] = $hora_ini_cita_aux;
                                                        break;
                                                    } else if ($hora_ini_cita_aux >= $horas[$k - 1]) {
                                                        $horas[$k] = $hora_ini_cita_aux;
                                                        break;
                                                    } else if ($hora_fin_cita_aux > $horas[$k - 1]) {
                                                        $horas[$k] = $horas[$k - 1];
                                                        break;
                                                    }
                                                }
                                            } else {
                                                //Finales de bloque
                                                if ($hora_fin_cita_aux > $horas[$k]) {
                                                    //Citas posteriores a horas finales de bloque
                                                    if ($k == count($horas) - 1) {
                                                        $horas[$k] = $hora_fin_cita_aux;
                                                        break;
                                                    } else if ($hora_fin_cita_aux < $horas[$k + 1]) {
                                                        $horas[$k] = $hora_fin_cita_aux;
                                                        break;
                                                    } else if ($hora_fin_cita_aux == $horas[$k + 1]) {
                                                        if ($hora_ini_cita_aux < $horas[$k]) {
                                                            $horas[$k] = $hora_fin_cita_aux;
                                                            break;
                                                        }
                                                    } else if ($hora_ini_cita_aux < $horas[$k]) {
                                                        $horas[$k] = $horas[$k + 1];
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    //NO DISPONIBLE
                                    if ($tipo_disponibilidad == 13) {
                                        $horas_completas = array("", "", "", "");
                                        $horas_parciales = $horas_completas;
                                    }

                                    //Se toma la primera hora para iniciar el recorrido
                                    $hora_ini = explode(":", $horas[0]);

                                    //Se divide para obtener el valor entero
                                    $hora_anterior = intval($hora_ini[0]);

                                    $tamanio_array_horas = count($horas);
                                    $bandera_fin = 1;
                                    $tiempo_disponible = 0; //Sumador del tiempo disponible del usuario

                                    $fila_hora_ant = $horas[0];
                                    $indice_cita = 1;
                                    for ($contHoras = 1; $contHoras < count($horas); $contHoras++) {
                                        $fila_hora = $horas[$contHoras];
                                        $hora = explode(":", $fila_hora);
                                        $hora_actual = intval($hora[0]);

                                        //Se verifica si la hora final tiene minutos diferentes a 00
                                        $hora_fin_bloque = $hora_actual;
                                        $hora_fin_bloque2 = $hora_actual;
                                        $minuto_fin_aux = intval($hora[1]);
                                        if ($minuto_fin_aux > 0) {
                                            $hora_fin_bloque2++;
                                        }

                                        $hora_anterior = explode(":", $fila_hora_ant);
                                        $hora_anterior = intval($hora_anterior[0]);
                                        $hora_ini_bloque = $hora_anterior;
                                        while ($hora_anterior < $hora_fin_bloque2 && $contHoras % 2 == 1) { //Mientras que la hora anterior sea menor que la hora que se recorre
                                            //Se calculan los minutos iniciales y finales de la hora
                                            $minuto_ini_aux = 0;
                                            $minuto_fin_aux = 59;
                                            if ($hora_ini_bloque == $hora_anterior) {
                                                $minuto_ini_aux = intval(substr($fila_hora_ant, 3, 2));
                                            }
                                            if ($hora_fin_bloque != $hora_fin_bloque2 && $hora_anterior == $hora_fin_bloque) {
                                                $minuto_fin_aux = intval(substr($fila_hora, 3, 2)) - 1;
                                            }
                                            for ($i = $minuto_ini_aux; $i <= $minuto_fin_aux; $i += 1) { //Teniendo la hora se recorre los minutos cada 1 para buscar las citas que tienen
                                                //Fecha para consultar
                                                $hora_consulta = cifras_numero($hora_anterior, 2);
                                                $minutos_consulta = cifras_numero($i, 2);
                                                $fecha_hora_consulta = $fecha . " " . $hora_consulta . ":" . $minutos_consulta . ":00";

                                                //Se obtiene las citas de una hora y dia especificos
                                                $cita_hora = array();
                                                if (count($lista_citas_dia) > 0) {
                                                    foreach ($lista_citas_dia as $cita_aux) {
                                                        if ($cita_aux["fecha_cita"] == $fecha_hora_consulta) {
                                                            array_push($cita_hora, $cita_aux);
                                                        }
                                                    }
                                                }

                                                if (count($cita_hora) > 0) { //Si se encontraron citas
                                                    if ($tiempo_disponible > 0) {
                                                        $celdas = $tiempo_disponible;
                                                        $tamanio_celda = $celdas;
                                                        $hora_desde = operacion_horas($hora_consulta, $minutos_consulta, 2, $tiempo_disponible, 12);
                                                        $hora_hasta = operacion_horas($hora_consulta, $minutos_consulta, 2, 0, 12);

                                                        //Horas para crear citas
                                                        $hora_desde_cita = operacion_horas($hora_consulta, $minutos_consulta, 2, $tiempo_disponible, 24);
                                                        $hora_hasta_cita = operacion_horas($hora_consulta, $minutos_consulta, 2, 0, 24);

                                                        $tamano_aux = "";
                                                        if ($tamanio_celda > 20) {
                                                            $tamano_aux = "height:" . $tamanio_celda . "px";
                                                        }

                                                        //Se verifica si el perfil del usuario permite asignar citas en esta franja
                                                        $bol_perfil_autorizado = validar_perfil_asignacion_citas($fecha, $hora_desde_cita, $hora_hasta_cita, $id_usuario, $lista_perfiles_usuario, 0);
                                                        ?>
                                                        <div class="espacio disponible" style="<?php echo ($tamano_aux); ?>;"
                                                        <?php
                                                        if ($permitir_accion == 1 && $tipo_acceso_menu == 2 && $bol_perfil_autorizado) {
                                                            ?>
                                                                 onclick='
                                                                 <?php
                                                                 if ($ind_reasignar != 1) {
                                                                     ?>
                                                                                                                 abrir_crear_cita("<?php echo ($fecha); ?>", "<?php echo ($hora_desde_cita); ?>", "<?php echo ($hora_hasta_cita); ?>", "<?php echo ($id_usuario); ?>", "<?php echo ($id_lugar_disp); ?>");
                                                                     <?php
                                                                 } else {
                                                                     ?>
                                                                                                                 abrir_crear_cita_paciente("<?php echo ($fecha); ?>", "<?php echo ($hora_desde_cita); ?>", "<?php echo ($hora_hasta_cita); ?>", "<?php echo ($id_usuario); ?>", "", "<?php echo ($id_cita_reasignar); ?>", "<?php echo ($id_lugar_disp); ?>", <?php echo ($ind_reasignar); ?>);
                                                                     <?php
                                                                 }
                                                                 ?>
                                                                 '
                                                                 <?php
                                                             }
                                                             ?>
                                                             >
                                                            <span class="time"><?php echo $hora_desde . " - " . $hora_hasta; ?></span>
                                                            <p class="estado-espacio">
                                                                <?php
                                                                echo ($bol_perfil_autorizado ? "Disponible" : '<span class="texto-rojo">No Autorizado</span>');
                                                                ?>
                                                            </p>
                                                        </div>
                                                        <?php
                                                        $tiempo_disponible = 0;
                                                    }

                                                    foreach ($cita_hora as $fila_cita) {
                                                        //Se verifica si se trata de una cita fuera de tiempo
                                                        $bol_a_cancelar = false;
                                                        $hora_ini_aux = substr($fila_cita["fecha_cita"], 11, 5);
                                                        $hora_fin_aux = substr($fila_cita["fecha_fin_cita"], 11, 5);
                                                        if ($tipo_disponibilidad == 13) {
                                                            $bol_a_cancelar = true;
                                                        } else {
                                                            for ($k = 0; $k < count($horas_parciales); $k++) {
                                                                if ($k % 2 == 0) {
                                                                    //Horas iniciales de bloque
                                                                    if ($hora_ini_aux < $horas_parciales[$k] && ($k == 0 || $hora_ini_aux >= $horas_parciales[$k - 1])) {
                                                                        $bol_a_cancelar = true;
                                                                        break;
                                                                    }
                                                                } else {
                                                                    //Horas finales de bloque
                                                                    if ($hora_ini_aux >= $horas_parciales[$k] && ($k == (count($horas_parciales) - 1) || $hora_ini_aux < $horas_parciales[$k + 1])) {
                                                                        $bol_a_cancelar = true;
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                        }

                                                        $nombres_paciente = $fila_cita["nombre_1"] . " " . $fila_cita["nombre_2"] . " " . $fila_cita["apellido_1"] . " " . $fila_cita["apellido_2"];
                                                        $tiempo_cita = $fila_cita["tiempo_cita"];
                                                        $nombre_tipo_cita = $fila_cita["nombre_tipo_cita"];
                                                        $nombre_convenio = $fila_cita["nombre_convenio"];
                                                        $observacion_cita = strip_tags($fila_cita["observacion_cita"]);
                                                        $id_cita = $fila_cita["id_cita"];
															
														
														
                                                        $celdas = $tiempo_cita;
                                                        $tamanio_celda = $celdas;

                                                        $hora_hasta = operacion_horas($hora_consulta, $minutos_consulta, 1, $tiempo_cita, 12);
                                                        $hora_desde = operacion_horas($hora_consulta, $minutos_consulta, 1, 0, 12);

                                                        //Horas para crear citas
                                                        $hora_hasta_cita = operacion_horas($hora_consulta, $minutos_consulta, 1, $tiempo_cita, 24);
                                                        $hora_desde_cita = operacion_horas($hora_consulta, $minutos_consulta, 1, 0, 24);

                                                        $estilo_aux = "";
                                                        if ($fila_cita["id_estado_cita"] == "17") {
                                                            $estilo_aux = " cita_atendida";
                                                        } else if ($bol_a_cancelar) {
                                                            $estilo_aux = " cita_a_cancelar";
                                                        }
                                                        ?>
                                                        <div class="espacio cita<?php echo ($estilo_aux); ?>" title="<?php echo (($fila_cita["id_estado_cita"] == "17" ? "(CITA ATENDIDA) " : "") . $observacion_cita); ?>" style="z-index:50;"
                                                        <?php
                                                        if ($ind_reasignar != 1) {
                                                            ?>
                                                                 onclick='abrir_editar_cita("<?php echo $id_cita; ?>", "<?php echo $id_usuario; ?>", "<?php echo ($id_lugar_disp); ?>");'
                                                                 <?php
                                                             }
                                                             ?>
                                                             >
                                                            <span class="time"><?php echo $hora_desde . " - " . $hora_hasta; ?></span>
                                                            <?php
                                                            echo($nombre_tipo_cita);
                                                            if ($fila_cita["ind_confirmada"] == "1") {
                                                                ?>
                                                                <span style="color:#FF0000;"><b> - CONFIRMADA</b></span>
                                                                <?php
                                                            }
                                                            ?>
                                                            <br />
                                                            <?php echo($nombres_paciente); ?>
                                                            <p class="aseguradora"><?php echo ($nombre_convenio); ?></p>
                                                            <span class="contador_cita"><?php echo ($indice_cita); ?></span>
                                                        </div>
                                                        <?php
                                                        $indice_cita++;
                                                        $tiempo_disponible = 0;
                                                    }
                                                } else {
                                                    //Se verifica que no haya citas
                                                    $hora_consulta_aux = substr($fecha_hora_consulta, 11, 5);
                                                    $bol_hallado_aux = false;
                                                    foreach ($lista_citas_dia as $cita_aux) {
                                                        $hora_ini_aux = substr($cita_aux["fecha_cita"], 11, 5);
                                                        $hora_fin_aux = substr($cita_aux["fecha_fin_cita"], 11, 5);
                                                        if ($hora_consulta_aux >= $hora_ini_aux && $hora_consulta_aux < $hora_fin_aux) {
                                                            $bol_hallado_aux = true;
                                                            break;
                                                        }
                                                    }
                                                    if (!$bol_hallado_aux) {
                                                        //Se verifica si corresponde a un horario en el que hay atención
                                                        $bol_a_cancelar = false;
                                                        for ($k = 0; $k < count($horas_parciales); $k++) {
                                                            if ($k % 2 == 0) {
                                                                //Horas iniciales de bloque
                                                                if ($hora_consulta_aux < $horas_parciales[$k] && ($k == 0 || $hora_consulta_aux >= $horas_parciales[$k - 1])) {
                                                                    $bol_a_cancelar = true;
                                                                    break;
                                                                }
                                                            } else {
                                                                //Horas finales de bloque
                                                                if ($hora_consulta_aux >= $horas_parciales[$k] && ($k == (count($horas_parciales) - 1) || $hora_consulta_aux < $horas_parciales[$k + 1])) {
                                                                    $bol_a_cancelar = true;
                                                                    break;
                                                                }
                                                            }
                                                        }

                                                        if ($bol_a_cancelar) {
                                                            //Se imprime la franja disponible
                                                            if ($tiempo_disponible > 0) {
                                                                $celdas = $tiempo_disponible;
                                                                $tamanio_celda = $celdas;
                                                                $hora_desde = "";
                                                                $hora_desde = operacion_horas($hora_consulta, $minutos_consulta, 2, $tiempo_disponible, 12);
                                                                $hora_hasta = "";
                                                                $hora_hasta = operacion_horas($hora_consulta, $minutos_consulta, 2, 0, 12);

                                                                //Horas para crear citas
                                                                $hora_desde_cita = operacion_horas($hora_consulta, $minutos_consulta, 2, $tiempo_disponible, 24);
                                                                $hora_hasta_cita = operacion_horas($hora_consulta, $minutos_consulta, 2, 0, 24);

                                                                $tamano_aux = "";
                                                                if ($tamanio_celda > 20) {
                                                                    $tamano_aux = "height:" . $tamanio_celda . "px";
                                                                }

                                                                //Se verifica si el perfil del usuario permite asignar citas en esta franja
                                                                $bol_perfil_autorizado = validar_perfil_asignacion_citas($fecha, $hora_desde_cita, $hora_hasta_cita, $id_usuario, $lista_perfiles_usuario, 0);
                                                                ?>
                                                                <div class="espacio disponible" style="<?php echo ($tamano_aux); ?>;"
                                                                <?php
                                                                if ($permitir_accion == 1 && $tipo_acceso_menu == 2 && $bol_perfil_autorizado) {
                                                                    ?>
                                                                         onclick='
                                                                         <?php
                                                                         if ($ind_reasignar != 1) {
                                                                             ?>
                                                                                                                                 abrir_crear_cita("<?php echo $fecha; ?>", "<?php echo $hora_desde_cita; ?>", "<?php echo $hora_hasta_cita; ?>", "<?php echo $id_usuario; ?>", "<?php echo ($id_lugar_disp); ?>");
                                                                             <?php
                                                                         } else {
                                                                             ?>
                                                                                                                                 abrir_crear_cita_paciente("<?php echo ($fecha); ?>", "<?php echo ($hora_desde_cita); ?>", "<?php echo ($hora_hasta_cita); ?>", "<?php echo ($id_usuario); ?>", "", "<?php echo ($id_cita_reasignar); ?>", "<?php echo ($id_lugar_disp); ?>", <?php echo ($ind_reasignar); ?>);
                                                                             <?php
                                                                         }
                                                                         ?>
                                                                         '
                                                                         <?php
                                                                     }
                                                                     ?>
                                                                     >
                                                                    <span class="time"><?php echo $hora_desde . " - " . $hora_hasta; ?></span>
                                                                    <p class="estado-espacio">
                                                                        <?php
                                                                        echo ($bol_perfil_autorizado ? "Disponible" : '<span class="texto-rojo">No Autorizado</span>');
                                                                        ?>
                                                                    </p>
                                                                </div>
                                                                <?php
                                                                $tiempo_disponible = 0;
                                                            }
                                                        } else {
                                                            $tiempo_disponible += 1;
                                                        }
                                                    }
                                                }

                                                if ($hora_anterior == $hora_fin_bloque2 - 1 && $i == $minuto_fin_aux) {
                                                    if ($tiempo_disponible > 0) {
                                                        $celdas = $tiempo_disponible;
                                                        $tamanio_celda = $celdas;

                                                        $hora_consulta2 = $hora_anterior;
                                                        $minutos_consulta2 = $i + 1;
                                                        if ($minutos_consulta2 >= 60) {
                                                            $minutos_consulta2 = 0;
                                                            $hora_consulta2++;
                                                        }

                                                        $hora_desde = "";
                                                        $hora_desde = operacion_horas($hora_consulta2, $minutos_consulta2, 2, $tiempo_disponible, 12);
                                                        $hora_hasta = "";
                                                        $hora_hasta = operacion_horas($hora_consulta2, $minutos_consulta2, 2, 0, 12);

                                                        //Horas para crear citas
                                                        $hora_desde_cita = operacion_horas($hora_consulta2, $minutos_consulta2, 2, $tiempo_disponible, 24);
                                                        $hora_hasta_cita = operacion_horas($hora_consulta2, $minutos_consulta2, 2, 0, 24);
                                                        ?>
                                                        <?php
                                                        $tamano_aux = "";
                                                        if ($tamanio_celda > 20) {
                                                            $tamano_aux = "height:" . $tamanio_celda . "px";
                                                        }

                                                        //Se verifica si el perfil del usuario permite asignar citas en esta franja
                                                        $bol_perfil_autorizado = validar_perfil_asignacion_citas($fecha, $hora_desde_cita, $hora_hasta_cita, $id_usuario, $lista_perfiles_usuario, 0);
                                                        ?>
                                                        <div class="espacio disponible" style="<?php echo ($tamano_aux); ?>;"
                                                        <?php
                                                        if ($permitir_accion == 1 && $tipo_acceso_menu == 2 && $bol_perfil_autorizado) {
                                                            ?>
                                                                 onclick='
                                                                 <?php
                                                                 if ($ind_reasignar != 1) {
                                                                     ?>
                                                                                                                 abrir_crear_cita("<?php echo ($fecha); ?>", "<?php echo ($hora_desde_cita); ?>", "<?php echo ($hora_hasta_cita); ?>", "<?php echo ($id_usuario); ?>", "<?php echo ($id_lugar_disp); ?>");
                                                                     <?php
                                                                 } else {
                                                                     ?>
                                                                                                                 abrir_crear_cita_paciente("<?php echo ($fecha); ?>", "<?php echo ($hora_desde_cita); ?>", "<?php echo ($hora_hasta_cita); ?>", "<?php echo ($id_usuario); ?>", "", "<?php echo ($id_cita_reasignar); ?>", "<?php echo ($id_lugar_disp); ?>", <?php echo ($ind_reasignar); ?>);
                                                                     <?php
                                                                 }
                                                                 ?>
                                                                 '
                                                                 <?php
                                                             }
                                                             ?>
                                                             >
                                                            <span class="time"><?php echo $hora_desde . " - " . $hora_hasta; ?></span>
                                                            <p class="estado-espacio">
                                                                <?php
                                                                echo ($bol_perfil_autorizado ? "Disponible" : '<span class="texto-rojo">No Autorizado</span>');
                                                                ?>
                                                            </p>
                                                        </div>
                                                        <?php
                                                    }

                                                    $tiempo_disponible = 0;

                                                    if ($contHoras < count($horas) - 1) {
                                                        ?>
                                                        <div class="espacio no_disponible" style="height:24px;"></div>
                                                        <?php
                                                    }
                                                } else if ($bandera_fin == $tamanio_array_horas && $hora_anterior == (intval($hora[0]) - 1) && $i == 59) {
                                                    //cuando llegue al final se pinta el bloque que falta si hay tiempo disponible
                                                    if ($tiempo_disponible > 0) {
                                                        $celdas = $tiempo_disponible;
                                                        $tamanio_celda = $celdas;

                                                        $hora_desde = operacion_horas($hora_anterior + 1, 0, 2, $tiempo_disponible + 1, 12);
                                                        $hora_hasta = operacion_horas($hora_anterior + 1, 0, 2, 0, 12);

                                                        //Horas para crear citas
                                                        $hora_desde_cita = operacion_horas($hora_anterior + 1, 0, 2, $tiempo_disponible + 1, 24);
                                                        $hora_hasta_cita = operacion_horas($hora_anterior + 1, 0, 2, 0, 24);

                                                        $tamano_aux = "";
                                                        if ($tamanio_celda > 20) {
                                                            $tamano_aux = "height:" . $tamanio_celda . "px";
                                                        }

                                                        //Se verifica si el perfil del usuario permite asignar citas en esta franja
                                                        $bol_perfil_autorizado = validar_perfil_asignacion_citas($fecha, $hora_desde_cita, $hora_hasta_cita, $id_usuario, $lista_perfiles_usuario, 0);
                                                        ?>
                                                        <div class="espacio disponible" style="<?php echo ($tamano_aux); ?>;"
                                                        <?php
                                                        if ($permitir_accion == 1 && $tipo_acceso_menu == 2 && $bol_perfil_autorizado) {
                                                            ?>
                                                                 onclick='
                                                                 <?php
                                                                 if ($ind_reasignar != "") {
                                                                     ?>
                                                                                                                 abrir_crear_cita("<?php echo $fecha; ?>", "<?php echo $hora_desde_cita; ?>", "<?php echo $hora_hasta_cita; ?>", "<?php echo $id_usuario; ?>", "<?php echo ($id_lugar_disp); ?>");
                                                                     <?php
                                                                 } else {
                                                                     ?>
                                                                                                                 abrir_crear_cita_paciente("<?php echo ($fecha); ?>", "<?php echo ($hora_desde_cita); ?>", "<?php echo ($hora_hasta_cita); ?>", "<?php echo ($id_usuario); ?>", "", "<?php echo ($id_cita_reasignar); ?>", "<?php echo ($id_lugar_disp); ?>", <?php echo ($ind_reasignar); ?>);
                                                                     <?php
                                                                 }
                                                                 ?>
                                                                 '
                                                                 <?php
                                                             }
                                                             ?>
                                                             >
                                                            <span class="time"><?php echo $hora_desde . " - " . $hora_hasta; ?></span>
                                                            <p class="estado-espacio">
                                                                <?php
                                                                echo ($bol_perfil_autorizado ? "Disponible" : '<span class="texto-rojo">No Autorizado</span>');
                                                                ?>
                                                            </p>
                                                        </div>
                                                        <?php
                                                    }
                                                    $tiempo_disponible = 0;
                                                }
                                            } //Fin del for
                                            $hora_anterior++;
                                        } //Fin del while
                                        $bandera_fin++;
                                        $fila_hora_ant = $horas[$contHoras];
                                    }
                                    ?>
                                </div>
                            </div>
                        </td>
                        <?php
                        $cont_usuarios_cal++;
                    }
                    ?>
                </tr>
            </table>
        </div>
        <?php
        break;

    case "3": //cargar el formulario de creacion y edicion de citas
        $combo = new Combo_Box();
        $tipo_accion = "";
        $ind_reasignar = 0;
        $bol_editar = true;
        $id_tipo_disp_aux = "";
        $nombre_lugar_cita = "";
        $rango = "";
        $tipoCotizante = "";
        $recomendaciones = 0;
        $cmb_paises = "";
        $nom_dep = "";
        $nom_mun = "";
        $txt_observacion_cita = "";
        $plan = "";
        $cmb_dep = "";
        $cmb_mun = "";
        $cmb_statusSeguro = 1;
		$cmb_sede_alter="";
		$sede_alter="";

        if (isset($_POST["id_cita"])) {
            //Modificación
            $id_paciente = "";
            $id_cita = $utilidades->str_decode($_POST["id_cita"]);
            $id_usuario = $utilidades->str_decode($_POST["id_usuario"]);

            $titulo_formulario = "Modificar Cita";
            $tipo_accion = 2; //Editar cita
            $tabla_cita = $dbAsignarCitas->getCita($id_cita);
            $id_paciente = $tabla_cita["id_paciente"];
            $id_lugar_disp = $tabla_cita["id_lugar_cita"];
            $txt_primer_nombre = $tabla_cita["Cnombre1"];
            $txt_segundo_nombre = $tabla_cita["Cnombre2"];
            $txt_primer_apellido = $tabla_cita["Capellido1"];
            $txt_segundo_apellido = $tabla_cita["Capellido2"];
            $cmb_tipo_documento = $tabla_cita["id_tipo_documento"];
            $txt_numero_documento = $tabla_cita["numero_documento"];
            $txt_numero_telefono = $tabla_cita["Ctelefono"];
			$cmb_convenio = $tabla_cita["id_convenio"];
            //$cmb_convenio = $tabla_cita["Cconvenio"];
            $txt_observacion_cita = trim($tabla_cita["observacion_cita"]);
            $cmb_lentes = $tabla_cita["ind_lentes"];
            $chk_no_pago = $tabla_cita["ind_no_pago"];
            $txt_nombre_tipo_cita = trim($tabla_cita["nombre_tipo_cita"]);
            $fecha_cita = substr($tabla_cita["fecha_cita"], 0, 10);
            $hora_cita = substr($tabla_cita["fecha_cita"], 11, 5);
            $hora_ini = $hora_cita;
            $hora_fin = $hora_cita;
            $id_tipo_cita = "";
            $nombre_usuario_crea = $tabla_cita["nombre_usuario"] . " " . $tabla_cita["apellido_usuario"];
            $fecha_crea = $tabla_cita["fecha_crea_t"];
            $ind_confirmada = intval($tabla_cita["ind_confirmada"], 10);
            $nombre_usuario_confirma = $tabla_cita["nombre_confirma"] . " " . $tabla_cita["apellido_confirma"];
            $fecha_confirma = $tabla_cita["fecha_confirma_t"];
            $nombre_estado_cita = $tabla_cita["estado_cita"];

            $cmb_sexo = $tabla_cita["Csexo"];
            $fecha_nacimiento = $tabla_cita["fecha_nac"];
            $cmb_paises = $tabla_cita["Cpais"];

            $cmb_seguro = $tabla_cita["Cconvenio"];
            $cmb_statusSeguro = $tabla_cita["CstatusConvenio"];
            $cmb_exentoSeguro = $tabla_cita["CconvenioExento"];

            $cmb_dep = $tabla_cita["Cdepartamento"];
            $cmb_mun = $tabla_cita["Cmunicipio"];
            $txt_direccion = $tabla_cita["Cdireccion"];
            $rango = $tabla_cita["Prango"];
            $tipoCotizante = $tabla_cita["PtipoCoti"];
            $nom_dep = $tabla_cita["nom_dep_aux"];
            $nom_mun = $tabla_cita["nom_mun_aux"];
            $plan = $tabla_cita["id_plan"];
			$cmb_sede_alter=$tabla_cita["id_lugar_cita_alter"];
			$sede_alter=$tabla_cita["sede_alter"];
			
            //Se verifica si la cita se puede editar
            if ($tabla_cita["id_estado_cita"] != "14" && $tabla_cita["id_estado_cita"] != "130" && $tabla_cita["id_estado_cita"] != "258") {
                $bol_editar = false;
            } else {
                //Si es una cita pasada no se debe poder editar
                $fecha_actual = $dbCalendario->getFechaActual();
                if ($fecha_actual["fecha_hoy"] > $fecha_cita) {
                    $bol_editar = false;
                }
            }
            $nombre_lugar_cita = $tabla_cita["nombre_lugar_cita"];
        } else if (isset($_POST["id_paciente"]) || (isset($_POST["id_paciente_prog_cx"]) && $_POST["id_paciente_prog_cx"] != "")) {
            //Reasignación
            if (isset($_POST["id_paciente"])) {
                $id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
            } else {
                $id_paciente = $utilidades->str_decode($_POST["id_paciente_prog_cx"]);
            }
            @$id_cita_ant = $utilidades->str_decode($_POST["id_cita_ant"]);

            $id_cita = "";
            $fecha_cita = $utilidades->str_decode($_POST["fecha"]);
            $hora_ini = $utilidades->str_decode($_POST["hora_ini"]);
            $hora_fin = $utilidades->str_decode($_POST["hora_fin"]);
            $id_usuario = $utilidades->str_decode($_POST["id_usuario"]);
            $id_lugar_disp = $utilidades->str_decode($_POST["id_lugar_disp"]);

            $titulo_formulario = "Nueva Cita";
            $tipo_accion = 1; //Crear cita

            $txt_primer_nombre = "";
            $txt_segundo_nombre = "";
            $txt_primer_apellido = "";
            $txt_segundo_apellido = "";
            $cmb_tipo_documento = "";
            $txt_numero_documento = "";
            $txt_numero_telefono = "";
            $cmb_convenio = "";
            $txt_observacion_cita = "";
            $cmb_lentes = "";
            $chk_no_pago = "";
            $txt_nombre_tipo_cita = "";
            $hora_cita = "";
            $id_tipo_cita = "";
            $nombre_usuario_crea = "";
            $fecha_crea = "";
            $ind_confirmada = 0;
            $nombre_usuario_confirma = "";
            $fecha_confirma = "";
            $nombre_estado_cita = "";
            $fecha_nacimiento = "";

            $cmb_seguro = "";
            $cmb_statusSeguro = "";
            $cmb_exentoSeguro = "";
            $rango = "";
			$cmb_sede_alter="";
			$sede_alter="";

            if ($id_paciente != "") {
                $tabla_paciente = $dbAsignarCitas->getPaciente($id_paciente);
                $txt_primer_nombre = $tabla_paciente[0]["nombre_1"];
                $txt_segundo_nombre = $tabla_paciente[0]["nombre_2"];
                $txt_primer_apellido = $tabla_paciente[0]["apellido_1"];
                $txt_segundo_apellido = $tabla_paciente[0]["apellido_2"];
                $cmb_tipo_documento = $tabla_paciente[0]["id_tipo_documento"];
                $txt_numero_documento = $tabla_paciente[0]["numero_documento"];
                $txt_numero_telefono = $tabla_paciente[0]["telefono_1"];
                $admision_obj = $dbAdmision->get_ultima_admision($id_paciente);
                /* if (isset($admision_obj["id_admision"])) {
                  $cmb_convenio = $admision_obj["id_convenio"];
                  } */
            } else if ($id_cita_ant != "") {
                $ind_reasignar = $utilidades->str_decode($_POST["ind_reasignar"]);
                $tabla_paciente = $dbAsignarCitas->getCita($id_cita_ant);
                $txt_primer_nombre = $tabla_paciente["Cnombre1"];
                $txt_segundo_nombre = $tabla_paciente["Cnombre2"];
                $txt_primer_apellido = $tabla_paciente["Capellido1"];
                $txt_segundo_apellido = $tabla_paciente["Capellido2"];
                $cmb_tipo_documento = $tabla_paciente["id_tipo_documento"];
                $txt_numero_documento = $tabla_paciente["numero_documento"];
                $txt_numero_telefono = $tabla_paciente["Ctelefono"];

                $cmb_convenio = $tabla_paciente["Cconvenio"];

                $cmb_lentes = $tabla_paciente["ind_lentes"];
                $chk_no_pago = $tabla_paciente["ind_no_pago"];
                $txt_observacion_cita = $tabla_paciente["observacion_cita"];
                $id_tipo_cita = $tabla_paciente["id_tipo_cita"];
                $cmb_sexo = $tabla_paciente["Csexo"];

                $fecha_nacimiento = $tabla_paciente["fecha_nac"];
                $cmb_paises = $tabla_paciente["Cpais"];
                $cmb_statusSeguro = $tabla_paciente["CstatusConvenio"];
                $cmb_exentoSeguro = $tabla_paciente["CconvenioExento"];
                $rango = $tabla_paciente["Prango"];
                $cmb_dep = $tabla_paciente["Cdepartamento"];
                $cmb_mun = $tabla_paciente["Cmunicipio"];
                $txt_direccion = $tabla_paciente["Cdireccion"];
                $tipoCotizante = $tabla_paciente["tipo_coti_paciente"];
                $plan = $tabla_paciente["id_plan"];
				$cmb_sede_alter= $tabla_paciente["id_lugar_cita_alter"];
				$sede_alter=$tabla_paciente["sede_alter"];
            }
            ?>
            <script id="ajax" type="text/javascript">
                validar_documento_cita("<?php echo ($txt_numero_documento); ?>", "<?php echo ($tipo_accion); ?>", "", "<?php echo ($fecha_cita); ?>");
            </script>
            <?php
        } else {
            //Nueva citas
            $recomendaciones = $dbCitas->getRecomendacionesActivas();

            $id_paciente = "";
            $id_cita = "";
            $fecha_cita = $utilidades->str_decode($_POST["fecha"]);
            $hora_ini = $utilidades->str_decode($_POST["hora_ini"]);
            $hora_fin = $utilidades->str_decode($_POST["hora_fin"]);
            $id_usuario = $utilidades->str_decode($_POST["id_usuario"]);
            $id_lugar_disp = $utilidades->str_decode($_POST["id_lugar_disp"]);

            $titulo_formulario = "Nueva Cita";
            $tipo_accion = 1; //Crear cita
            $txt_primer_nombre = "";
            $txt_segundo_nombre = "";
            $txt_primer_apellido = "";
            $txt_segundo_apellido = "";
            $cmb_tipo_documento = "";
            $txt_numero_documento = "";
            $txt_numero_telefono = "";
            $cmb_convenio = "";
            $txt_observacion_cita = "";
            $cmb_lentes = "";
            $chk_no_pago = "";
            $txt_nombre_tipo_cita = "";
            $hora_cita = "";
            $id_tipo_cita = "";
            $nombre_usuario_crea = "";
            $fecha_crea = "";
            $ind_confirmada = 0;
            $nombre_usuario_confirma = "";
            $fecha_confirma = "";
            $nombre_estado_cita = "";
            $cmb_sexo = "";
            $cmb_paises = "";
            $fecha_nacimiento = "";
			$cmb_sede_alter="";
			$sede_alter="";
        }

        $tabla_usuario = $dbUsuarios->getUsuario($id_usuario);
        $nombre_usuario = $tabla_usuario["nombre_usuario"];
        $apellido_usuario = $tabla_usuario["apellido_usuario"];

        $fecha_mostrar = strtotime($fecha_cita);
        $anio = date("Y", $fecha_mostrar); // Year
        $mes = date("m", $fecha_mostrar); // Month
        $dia = date("d", $fecha_mostrar); // day
        $fecha_mostrar = $funciones_persona->obtenerFecha5($fecha_mostrar);

        //Se verifica el tipo de disponibilidad de la cita
        $disponibilidad_prof_obj = $dbDisponibilidadProf->getDisponibilidadProfDia($id_usuario, $fecha_cita);
        if (count($disponibilidad_prof_obj) > 0) {
            //Si la disponibilidad es parcial se busca el lugar
            if ($disponibilidad_prof_obj["id_tipo_disponibilidad"] == "12") {
                $disponibilidad_prof_det_obj = $dbDisponibilidadProf->getDisponibilidadProfDetFechaHora($disponibilidad_prof_obj["id_disponibilidad"], $fecha_cita, $hora_ini);

                if (count($disponibilidad_prof_det_obj) > 0) {
                    $id_lugar_disp = $disponibilidad_prof_det_obj["id_lugar_disp"];
                }
            }
        }

        $tabla_lugares = $dbListas->getDetalle($id_lugar_disp);
        $nombre_lugar_cita = $tabla_lugares["nombre_detalle"];

        $array_sino = array();
        $array_sino[0][0] = "1";
        $array_sino[0][1] = "SI";
        $array_sino[1][0] = "0";
        $array_sino[1][1] = "NO";

        //Se obtienen los listados permitidos de tipos de citas y convenios
        $lista_convenios_autorizados = $dbDisponibilidadProf->getListaDisponibilidadProfConveniosUsuarioHora($id_usuario, $fecha_cita, $fecha_cita . " " . $hora_ini, $fecha_cita . " " . $hora_fin);
        $lista_tipos_citas_autorizados = $dbDisponibilidadProf->getListaDisponibilidadProfTiposCitasUsuarioHora($id_usuario, $fecha_cita, $fecha_cita . " " . $hora_ini, $fecha_cita . " " . $hora_fin);
        $tmpConvenioAutorizados = count($lista_convenios_autorizados);
        ?>

        <div class="formulario" style="width:100%; display:block;height: 700px;overflow: overlay;">
            <div class="formulario" id="documento_persona" style="width:750px; height:100%; display:none;position: absolute;"></div>
            <div class="contenedor_error" id="contenedor_error"></div>
            <div class="contenedor_exito" id="contenedor_exito"></div>
            <div class="contenedor_advertencia" id="contenedor_advertencia"></div>
            <form id="frm_citas" name="frm_citas" method="post">
                <div id="div_documento_existe">
                    <input type="hidden" value="false" name="hdd_documento_existe" id="hdd_documento_existe" />
                </div>
                <input type="hidden" value="<?php echo ($id_cita); ?>" name="hdd_id_cita" id="hdd_id_cita" />
                <input type="hidden" value="<?php echo ($id_paciente); ?>" name="hdd_id_paciente" id="hdd_id_paciente" />
                <input type="hidden" value="<?php echo ($id_usuario); ?>" name="hdd_id_usuario" id="hdd_id_usuario" />
                <input type="hidden" value="<?php echo ($fecha_cita); ?>" name="hdd_fecha_cita" id="hdd_fecha_cita" />
                <input type="hidden" value="<?php echo ($hora_ini); ?>" name="hdd_hora_ini" id="hdd_hora_ini" />
                <input type="hidden" value="<?php echo ($hora_fin); ?>" name="hdd_hora_fin" id="hdd_hora_fin" />
                <input type="hidden" value="<?php echo ($id_lugar_disp); ?>" name="hdd_lugar_disp" id="hdd_lugar_disp" />
                <input type="hidden" value="<?= $tmpConvenioAutorizados > 0 ?  1 : 0 ?>" name="hdd_ind_convenios_autorizados" id="hdd_ind_convenios_autorizados" />
                <div class="encabezado">
                    <h3><?php echo ($titulo_formulario); ?></h3>
                </div>               
                <table border="0" cellpadding="0" cellspacing="0" align="center" style="width:95%">
                    <?php                  
                    if ($tmpConvenioAutorizados > 0) {
                        $conveniosPorAtenderNombre = "";
                        $conveniosPorAtender = "";
                        $contadorAux = 0;
                        foreach ($lista_convenios_autorizados as $convenioAux) {
                            $contadorAux++;
                            $conveniosPorAtenderNombre .= "*".$convenioAux['nombre_convenio'] . ($contadorAux < $tmpConvenioAutorizados ? ", " : "");
                            $conveniosPorAtender .= $convenioAux['id_convenio'] . ($contadorAux < $tmpConvenioAutorizados ? ";" : "");                            
                        }
                        ?>
                        <tr id="" style="">
                            <td colspan="4">
                                <div style="margin-bottom: 10px;padding: 5px;">                            
                                    <h5 style="font-weight: bold;">El usuario profesional &uacute;nicamente puede atender los siguientes convenios: [<span style="color: #FF0000;"><?= $conveniosPorAtenderNombre ?></span>]</h5>
                                    <input type="hidden" value="<?= $conveniosPorAtender ?>" name="hdd_convenios_autorizados" id="hdd_convenios_autorizados" /> 
                                </div>
                            </td>
                        </tr>    
                        <?php
                    }
                    ?>

                    <tr>
                        <th colspan="2" align="left" style="width:50%;">
                            <h5>Doctor(a):&nbsp;<?php echo ($nombre_usuario . " " . $apellido_usuario); ?></h5>
                            <h5>Lugar:&nbsp;<?php echo ($nombre_lugar_cita); ?></h5>
                        </th>
                        <th colspan="2" align="right" style="width:50%;">
                            <h5><?php echo ($fecha_mostrar); ?></h5>
                        </th>
                    </tr>
                    <tr id="trHistoricoPacientes" style="display: none;">
                        <td colspan="4">
                            <input class="btnPrincipal" type="button" value="Ver hist&oacute;rico de citas" id="btn_consultar" name="btn_consultar" onclick="historicoCitas();" />
                        </td>
                    </tr>                
                    <?php
                    if ($tipo_accion == 2) {
                        ?>
                        <tr>
                            <td align="right">
                                <label class="inline" for="cmb_tipo_cita"><b>Creada por&nbsp;</b></label>
                            </td>
                            <td align="left">
                                <label class="inline" for="cmb_tipo_cita"><?php echo ($nombre_usuario_crea); ?></label>
                            </td>
                            <td align="right">
                                <label class="inline" for="cmb_tipo_cita"><b>Fecha de creaci&oacute;n&nbsp;</b></label>
                            </td>
                            <td align="left">
                                <label class="inline" for="cmb_tipo_cita"><?php echo ($fecha_crea); ?></label>
                            </td>
                        </tr>
                        <?php
                        if ($ind_confirmada == 1) {
                            ?>
                            <tr>
                                <td align="right">
                                    <label class="inline" for="cmb_tipo_cita"><b>Confirmada por&nbsp;</b></label>
                                </td>
                                <td align="left">
                                    <label class="inline" for="cmb_tipo_cita"><?php echo ($nombre_usuario_confirma); ?></label>
                                </td>
                                <td align="right">
                                    <label class="inline" for="cmb_tipo_cita"><b>Fecha de confirmaci&oacute;n&nbsp;</b></label>
                                </td>
                                <td align="left">
                                    <label class="inline" for="cmb_tipo_cita"><?php echo ($fecha_confirma); ?></label>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>

                    <tr id="trEstadoInactivo" style="display:none;">
                        <td colspan="4">
                            <div style="margin-bottom: 10px;padding: 5px;">                            
                                <h5 style="font-weight: bold;">El paciente se encuentra <span style="color:#FF0000;">inactivo</span> para el convenio <span id="spn_nombre_convenio_inactivo_aux" style="color:#FF0000;">.</span> y el plan <span id="spn_nombre_plan_inactivo_aux" style="color:#FF0000;">.</span></h5>
                            </div>
                        </td>
                    </tr>

                    <tr id="trEstadoActivo" style="display:none;">
                        <td colspan="4">
                            <div style="margin-bottom: 10px;padding: 5px;">                           
                                <h5 style="font-weight: bold;">El paciente se encuentra <span style="color:#FF0000;">activo</span> para el convenio <span id="spn_nombre_convenio_activo_aux" style="color:#FF0000;">.</span> y el plan <span id="spn_nombre_plan_activo_aux" style="color:#FF0000;">.</span></h5>
                            </div>
                        </td>
                    </tr>

                    <tr id="trRecomendacion" style="display:none;">
                        <td colspan="4">
                            <div style="margin-bottom: 10px;padding: 5px;">
                                <h5 style="color:#FF0000;">El paciente no se encuentra en la base de datos, ser&aacute; registrado con base en la informaci&oacute;n que suministre</h5>
                            </div>
                        </td>
                    </tr>
                    <?php if($id_lugar_disp=="464" || $id_lugar_disp=="466" || $id_lugar_disp=="491" || $id_lugar_disp=="511" || $id_lugar_disp=="556"){ ?>
                    <tr valign="top">
                    	<td colspan="3" align="right" style="width:100%;">
                        	 <p style="color:#FF0000; font-size:13px;">La sede alternativa se selecciona cuando el profesional se encuentre en alguna de estas ópticas.</p>
                        </td>
                    </tr>
					<tr valign="top">
                        <?php if ($tipo_accion == 1 ||  $sede_alter != ""){ ?>
                             <td align="right" style="width:18%;">
                                <label class="inline" for="cmb_sede_alter"><b>Sede alternativa&nbsp;</b></label>
                            </td>
						<?php }?>
                        <td align="left" style="width:32%;">
                        <?php if($tipo_accion ==1){
								$lista_sedes_alternativas = $dbCitas->getSedesAlternativas();
								$combo->getComboDb("cmb_sede_alter", $cmb_sede_alter, $lista_sedes_alternativas, "id, sede", "--Seleccione--", "", "", "width:100%;");
							}else if ($tipo_accion == 2) { //Para editar una cita
                              ?>
                                <label class="inline" for="cmb_sede_alter"><?=  $sede_alter ?></label>
                                <?php
                            }
							
						?> 
                        </td>
                    </tr>
                    <?php } ?>
                    <tr valign="top">
                        <td align="right" style="width:18%;">
                            <label class="inline" for="cmb_tipo_cita"><b>Tipo de cita&nbsp;</b></label>
                        </td>
                        <td align="left" style="width:32%;">
                            <?php
                            if ($tipo_accion == 1) { //Para crear una cita
                                $lista_tipo_cita_usuario_base = $dbAsignarCitas->getTiposCitasUsuario($id_usuario);
                                if (count($lista_tipos_citas_autorizados) > 0) {
                                    //Se filtran los tipos de citas autorizados
                                    $lista_tipo_cita_usuario = array();
									
                                    foreach ($lista_tipo_cita_usuario_base as $tipo_cita_aux) {
                                        foreach ($lista_tipos_citas_autorizados as $tipo_cita_aut_aux) {
                                            if ($tipo_cita_aux["id_tipo_cita"] == $tipo_cita_aut_aux["id_tipo_cita"]) {
                                                array_push($lista_tipo_cita_usuario, $tipo_cita_aux);
                                                break;
                                            }
                                        }
                                    }
                                } else {
									
                                    $lista_tipo_cita_usuario = $lista_tipo_cita_usuario_base;	
                                }				
								
								/**Tomar la hora del campo espacio disponible de citas y convertir a minutos **/
								$hora_ini_t = new DateTime($hora_ini);
								$hora_fin_t = new DateTime($hora_fin);
								//Hallar la diferencia entre horas y establecer formato
								$rango_hora = ($hora_ini_t->diff($hora_fin_t)->format('%H:%i'));
								//Separar horas de minutos
								list($horas, $minutos) = explode(':', $rango_hora);
								//convertir las horas en minutos
								$hora_minutos =($horas*60)+$minutos; 
							
								$lista_tipo_cita_usuario_aux = array();	
								foreach($lista_tipo_cita_usuario as $index => $value){
									$tiempo_cita = $value['tiempo_tipo_cita'];
									if($tiempo_cita<=$hora_minutos){		
		                         		array_push($lista_tipo_cita_usuario_aux, $value);
									}
								}
                                $combo->getComboDb("cmb_tipo_cita", $id_tipo_cita, $lista_tipo_cita_usuario_aux, "id_tipo_cita, nombre_tipo_cita, tiempo_tipo_cita, Minutos", "--Seleccione--", "cargar_horario(1, 0, 0); validar_lista_espera();", "", "width:100%;");
								
                                if ($id_tipo_cita != "") {
                                    ?>
                                    <script id="ajax" type="text/javascript">
                                        cargar_horario(1, 0, 0);
                                    </script>
                                    <?php
                                }
                            } else if ($tipo_accion == 2) { //Para EDITAR una cita
                                ?>
                                <label class="inline" for="cmb_tipo_cita"><?php echo ($txt_nombre_tipo_cita); ?></label>
                                <?php
                            }
                            ?>
                        </td>
                        <td align="right" style="width:18%;">
                            <label class="inline" for="cmb_horario_cita"><b>Hora&nbsp;</b></label>
                        </td>
                        <td align="left" style="width:32%;">
                            <?php
                            if ($tipo_accion == 1) { //Para CREAR una cita
                                ?>
                                <table border="0" cellpadding="0" cellspacing="0" width="1%">
                                    <tr>
                                        <td>
                                            <div id="div_horas_cita">
                                                <?php
                                                $combo->getComboDb("cmb_hora_cita", "", array(), "", "", "", "", "width:70px;");
                                                ?>
                                            </div>
                                        </td>
                                        <td><label class="inline" for="cmb_horario_cita">&nbsp;:&nbsp;</label></td>
                                        <td>
                                            <div id="div_minutos_cita">
                                                <?php
                                                $combo->getComboDb("cmb_minuto_cita", "", array(), "", "", "", "", "width:70px;");
                                                ?>
                                            </div>
                                        </td>
                                        <td><label id="lb_ampm_cita" class="inline" for="cmb_horario_cita"></label></td>
                                    </tr>
                                </table>
                                <?php
                            } else if ($tipo_accion == 2) { //Para EDITAR una cita
                                $arr_hora_aux = explode(":", $hora_cita);
                                ?>
                                <label class="inline" for="cmb_horario_cita"><?php echo (mostrar_hora_format($arr_hora_aux[0], $arr_hora_aux[1])); ?></label>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td align="right">
                            <label class="inline" for="cmb_tipo_documento"><b>Tipo de documento&nbsp;</b></label>
                        </td>
                        <td align="left">
                            <?php
                            $lista_tipo_documento = $dbListas->getListaDetalles(2);
                            if ($ind_reasignar != 1) {

                                $ind_disabled_aux = 1;
                                if ($tipo_accion == 2) {
                                    $ind_disabled_aux = 0;
                                }
                                $combo->getComboDb("cmb_tipo_documento", $cmb_tipo_documento, $lista_tipo_documento, "id_detalle, nombre_detalle", "--Seleccione--", "", $ind_disabled_aux, "width:170px;");
                            } else {
                                foreach ($lista_tipo_documento as $tipo_documento_aux) {
                                    if ($tipo_documento_aux["id_detalle"] == $cmb_tipo_documento) {
                                        ?>
                                        <label class="inline" for="cmb_tipo_documento"><?php echo ($tipo_documento_aux["nombre_detalle"]); ?></label>
                                        <input type="hidden" name="cmb_tipo_documento" id="cmb_tipo_documento" value="<?php echo ($tipo_documento_aux["id_detalle"]); ?>" />
                                        <?php
                                    }
                                }
                            }
                            ?>
                        </td>
                        <td align="right">
                            <label class="inline" for="txt_numero_documento"><b>N&uacute;mero de documento&nbsp;</b></label>
                        </td>
                        <td align="left">
                            <?php
                            if ($ind_reasignar != 1) {
                                $ind_disabled_aux = "";
                                if ($tipo_accion == 2) {
                                    $ind_disabled_aux = "disabled";
                                }
                                ?>
                                <input type="text" class="input required" value="<?php echo $txt_numero_documento; ?>" name="txt_numero_documento" id="txt_numero_documento" maxlength="20" size="20" onblur="trim_cadena(this);
                                                    quitar_espacios(this);
                                                    validar_documento_cita(this.value, '<?php echo $tipo_accion; ?>', '<?php echo $id_cita; ?>', '<?php echo $fecha_cita; ?>');
                                                    buscar_documento_paciente(this.value, <?php echo ($id_lugar_disp); ?>);
                                       " <?= $ind_disabled_aux ?>/>
                                       <?php
                                   } else {
                                       ?>
                                <label class="inline" for="txt_numero_documento"><?php echo ($txt_numero_documento); ?></label>
                                <input type="hidden" name="txt_numero_documento" id="txt_numero_documento" value="<?php echo ($txt_numero_documento); ?>" />
                                <?php
                            }
                            ?>
                        </td>
                    </tr>                   

                    <tr valign="top">
                        <td align="right">
                            <label class="inline" for="txt_primer_nombre"><b>Primer nombre&nbsp;*</b></label>
                        </td>
                        <td align="left">
                            <?php
                            if ($ind_reasignar != 1) {
                                ?>
                                <input type="text" class="input required" value="<?php echo $txt_primer_nombre; ?>" name="txt_primer_nombre" id="txt_primer_nombre" maxlength="30" size="20"  onblur="trim_cadena(this);" />
                                <?php
                            } else {
                                ?>
                                <label class="inline" for="txt_primer_nombre"><?php echo ($txt_primer_nombre); ?></label>
                                <input type="hidden" name="txt_primer_nombre" id="txt_primer_nombre" value="<?php echo ($txt_primer_nombre); ?>" />
                                <?php
                            }
                            ?>
                        </td>
                        <td align="right">
                            <label class="inline" for="txt_segundo_nombre"><b>Segundo nombre&nbsp;</b></label>
                        </td>
                        <td align="left">
                            <?php
                            if ($ind_reasignar != 1) {
                                ?>
                                <input type="text" class="input" value="<?php echo $txt_segundo_nombre; ?>" name="txt_segundo_nombre" id="txt_segundo_nombre" maxlength="30" size="20" onblur="trim_cadena(this);" />
                                <?php
                            } else {
                                ?>
                                <label class="inline" for="txt_segundo_nombre"><?php echo ($txt_segundo_nombre); ?></label>
                                <input type="hidden" name="txt_segundo_nombre" id="txt_segundo_nombre" value="<?php echo ($txt_segundo_nombre); ?>" />
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td align="right">
                            <label class="inline" for="txt_primer_apellido"><b>Primer apellido&nbsp;*</b></label>
                        </td>
                        <td align="left">
                            <?php
                            if ($ind_reasignar != 1) {
                                ?>
                                <input type="text" class="input required" value="<?php echo $txt_primer_apellido; ?>" name="txt_primer_apellido" id="txt_primer_apellido" maxlength="30" size="20"  onblur="trim_cadena(this);" />
                                <?php
                            } else {
                                ?>
                                <label class="inline" for="txt_primer_apellido"><?php echo ($txt_primer_apellido); ?></label>
                                <input type="hidden" name="txt_primer_apellido" id="txt_primer_apellido" value="<?php echo ($txt_primer_apellido); ?>" />
                                <?php
                            }
                            ?>
                        </td>
                        <td align="right">
                            <label class="inline" for="txt_segundo_apellido"><b>Segundo apellido&nbsp;</b></label>
                        </td>
                        <td align="left">
                            <?php
                            if ($ind_reasignar != 1) {
                                ?>
                                <input type="text" class="input" value="<?php echo $txt_segundo_apellido; ?>" name="txt_segundo_apellido" id="txt_segundo_apellido" maxlength="30" size="20"  onblur="trim_cadena(this);" />
                                <?php
                            } else {
                                ?>
                                <label class="inline" for="txt_segundo_apellido"><?php echo ($txt_segundo_apellido); ?></label>
                                <input type="hidden" name="txt_segundo_apellido" id="txt_segundo_apellido" value="<?php echo ($txt_segundo_apellido); ?>" />
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">
                            <label class="inline" for="cmb_sexo"><b>Sexo&nbsp;</b></label>
                        </td>
                        <td align="left">
                            <?php
                            $disabled = 1;
                            if ($ind_reasignar == 1) {
                                $disabled = 0;
                            }

                            $lista_sexo = $dbListas->getListaDetalles(1);
                            $combo->getComboDb("cmb_sexo", $cmb_sexo, $lista_sexo, "id_detalle, nombre_detalle", "--Seleccione--", "", $disabled, "width:170px;");
                            ?>
                        </td>
                        <td align="right">
                            <label class="inline" for="txt_fecha_nacimiento"><b>Fecha de nacimiento</b></label>
                        </td>
                        <td align="left">
                            <?php
                            $disabled = "";
                            if ($ind_reasignar == 1) {
                                $disabled = "disabled";
                            }
                            ?>
                            <input type="text" class="input required"  name="txt_fecha_nacimiento" id="txt_fecha_nacimiento" value="<?php echo($fecha_nacimiento); ?>" maxlength="10" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" style="width:120px;" <?= $disabled ?> />
                        </td>
                    </tr>

                    <tr valign="top">
                        <td align="right">
                            <label class="inline" for="txt_numero_telefono"><b>Tel&eacute;fono(s) de contacto&nbsp;*</b></label>
                        </td>
                        <td align="left">
                            <?php
                            $disabled = "";
                            if ($ind_reasignar == 1) {
                                $disabled = "disabled";
                            }
                            ?>
                            <input type="text" class="input required" value="<?php echo $txt_numero_telefono; ?>" name="txt_numero_telefono" id="txt_numero_telefono" maxlength="20" size="20" onkeypress="" <?= $disabled ?> />
                        </td>
                        <td align="right">
                            <label class="inline"><b>Pa&iacute;s residencia;</b></label>
                        </td>
                        <td align="left">
                            <?php
                            $disabled = 1;
                            if ($ind_reasignar == 1) {
                                $disabled = 0;
                            }

                            $cmb_paises == "" ? $cmb_paises = "1" : $cmb_paises = $cmb_paises;
                            $lista_paises = $dbPaises->getPaises();
                            $combo->getComboDb("cmb_pais", $cmb_paises, $lista_paises, "id_pais, nombre_pais", "--Seleccione--", "seleccionar_pais(this.value)", $disabled, "width:170px;");
                            ?>
                        </td>    
                    </tr>


                    <tr id="tr_dep_mun_col_val_res" style="display:<?php if ($cmb_paises != "1") { ?>none<?php } else { ?>content<?php } ?>;" >
                        <td align="right">
                            <label class="inline"><b>Departamento residencia</b></label>
                        </td>
                        <td align="left">
                            <?php
                            $disabled = 1;
                            if ($ind_reasignar == 1) {
                                $disabled = 0;
                            }

                            $lista_departamentos = $dbDepartamentos->getDepartamentos();
                            $combo->getComboDb("cmb_cod_dep_res", $cmb_dep, $lista_departamentos, "cod_dep, nom_dep", "-- Seleccione el Departamento --", "seleccionar_departamento(this.value);", $disabled, "");
                            ?>
                        </td>                        
                        <td align="right">
                            <label class="inline"><b>Municipio residencia</b></label>
                        </td>
                        <td align="left">
                            <div id="d_municipio">
                                <?php
                                $disabled = 1;
                                if ($ind_reasignar == 1) {
                                    $disabled = 0;
                                }

                                if ($cmb_mun == "") {
                                    $lista_municipios = $dbDepMuni->getListaMunicipiosAlfabetico();
                                } else {
                                    $lista_municipios = $dbDepMuni->getMunicipiosDepartamento($cmb_dep);
                                }

                                $combo->getComboDb("cmb_cod_mun_res", $cmb_mun, $lista_municipios, "cod_mun_dane,nom_mun", "-- Seleccione el Municipio --", "", $disabled, "width:100%;");
                                ?>
                            </div>
                        </td>
                    </tr>

                    <tr id="tr_dep_mun_otro_val_res" style="display:<?php if ($cmb_paises != "1") { ?>content<?php } else { ?>none<?php } ?>;">
                        <td align="right">
                            <label class="inline"><b>Estado/regi&oacute;n de residencia*</b></label>
                        </td>
                        <td align="left" id="td_dep_otro_val_res">
                            <input type="text" id="txt_nom_dep_res" name="txt_nom_dep_res" value="<?php echo($nom_dep); ?>" maxlength="50" style="width:100%;" onblur="trim(this.value);" />
                        </td>
                        <td align="right">
                            <label class="inline"><b>Municipio de residencia*</b></label>
                        </td>
                        <td align="left" id="td_mun_otro_val_res">
                            <input type="text" id="txt_nom_mun_res" name="txt_nom_mun_res" value="<?php echo($nom_mun); ?>" maxlength="50" style="width:100%;" onblur="trim(this.value);" />
                        </td>
                    </tr>

                    <tr>
                        <td align="right">
                            <label class="inline" for="cmb_convenio"><b>Convenio/Entidad&nbsp;</b></label>
                        </td>
                        <td align="left">
                            <?php
                            $disabled = 1;
                            if ($ind_reasignar == 1 || $tipo_accion == 2) {
                                $disabled = 0;
                            }

                            //Se carga el listado de convenios
                            $lista_convenios_base = $dbConvenios->getListaConveniosActivos();
                            $lista_convenios = $lista_convenios_base;

                            /*
                              if (count($lista_convenios_autorizados) > 0) {
                              //Se filtran los convenios autorizados
                              $lista_convenios = array();
                              foreach ($lista_convenios_base as $convenio_aux) {
                              foreach ($lista_convenios_autorizados as $convenio_aut_aux) {
                              if ($convenio_aux["id_convenio"] == $convenio_aut_aux["id_convenio"]) {
                              array_push($lista_convenios, $convenio_aux);
                              break;
                              }
                              }
                              }
                              } else {
                              $lista_convenios = $lista_convenios_base;
                              }
                             */
							 
							 //Se modifico el combo-box temporalmente para las citas
							 
                           /** $combo->getComboDb("cmb_convenio", $cmb_convenio, $lista_convenios, "id_convenio, nombre_convenio", "--Seleccione--", "seleccionar_convenio(this.value)",  
							 $disabled, "width:170px;");**/
                           
							 
							$id_usuario = $_SESSION["idUsuario"];
                            $combo->getComboDb("cmb_convenio", $cmb_convenio, $lista_convenios, "id_convenio, nombre_convenio", "--Seleccione--", "seleccionar_convenio(this.value)",  
							    /*$id_usuario == "105" ? $enabled : $disabled*/$disabled, "width:170px;");
                            ?>
                        </td>


                        <td align="right">
                            <label class="inline" for="cmb_convenio"><b>Plan*:</b></label>
                        </td>
                        <td align="left">
                            <div id="d_plan">                                                            
                                <?php
                                $disabled = 1;
                                if ($ind_reasignar == 1 || $tipo_accion == 2) {
                                    $disabled = 0;
                                }

                                $combo = new Combo_Box();
                                $lista_planes = $dbPlanes->getListaPlanesActivos($cmb_convenio);
                                $combo->getComboDb("cmb_cod_plan", $plan, $lista_planes, "id_plan,nombre_plan", "-- Seleccione --", "", $disabled, "width:100%;");
                                ?>
                            </div>
                        </td>                                                                        
                    </tr>

                    <tr>
                        <td align="right">
                            <label class="inline" for="cmb_convenio"><b>Tipo de cotizante*:</b></label>
                        </td>
                        <td align="left">
                            <?php
                            $disabled = 1;
                            if ($ind_reasignar == 1 || $tipo_accion == 2) {
                                $disabled = 0;
                            }
							$lista_tipo_cotizante = $dbListas->getListaDetalles(99);
							$combo->getComboDb("cmb_tipoCotizante", $tipoCotizante, $lista_tipo_cotizante, "id_detalle,nombre_detalle", "-- Seleccione --", "", $disabled, "width:100%;");
                            /*$array_tipoCotizante = array();
                            $array_tipoCotizante[0][0] = "0";
                            $array_tipoCotizante[0][1] = "No aplica";
                            $array_tipoCotizante[1][0] = "1";
                            $array_tipoCotizante[1][1] = "Cotizante";
                            $array_tipoCotizante[2][0] = "2";
                            $array_tipoCotizante[2][1] = "Beneficiario";
							
                            $combo->get("cmb_tipoCotizante", $tipoCotizante, $array_tipoCotizante, "-- Seleccione --", "", $disabled);*/
                            ?>
                        </td>

                        <td align="right">
                            <label class="inline" for="cmb_convenio"><b>Rango*:</b></label>
                        </td>
                        <td align="left">
                            <?php
                            $disabled = 1;
                            if ($ind_reasignar == 1  || $tipo_accion == 2) {
                                $disabled = 0;
                            }

                            $array_rango = array();
                            $array_rango[0][0] = "0";
                            $array_rango[0][1] = "No aplica";
                            $array_rango[1][0] = "1";
                            $array_rango[1][1] = "Uno";
                            $array_rango[2][0] = "2";
                            $array_rango[2][1] = "Dos";
                            $array_rango[3][0] = "3";
                            $array_rango[3][1] = "Tres";

                            $combo->get("cmb_rango", $rango, $array_rango, "-- Seleccione --", "", $disabled);
                            ?>

                        </td>
                    </tr>
                    <tr>
                        <td align="right">
                            <label class="inline" for="cmb_convenio"><b>Exento</b></label>
                        </td>
                        <td align="left">
                            <?php
                            $disabled = 0;
                            $array_exento = array();
                            $array_exento[0][0] = "0";
                            $array_exento[0][1] = "Sí";
                            $array_exento[1][0] = "1";
                            $array_exento[1][1] = "No";

                            $cmb_exentoSeguro == "" ? $cmb_exentoSeguro = 1 : $cmb_exentoSeguro = $cmb_exentoSeguro;

                            $combo->get("cmb_exento_convenio", $cmb_exentoSeguro, $array_exento, "-- Seleccione --", "", $disabled);
                            ?>

                        </td>
                        <td align="right">
                            <label class="inline" for="cmb_pais"><b>Estado aseguradora</b></label>
                        </td>
                        <td align="left">
                            <?php
                            $disabled = 0;
                            //if ($ind_reasignar == 1) {
                            //    $disabled = 0;
                            //}                            

                            $array_statusAseguradora = array();
                            $array_statusAseguradora[0][0] = "1";
                            $array_statusAseguradora[0][1] = "Activo";
                            $array_statusAseguradora[1][0] = "2";
                            $array_statusAseguradora[1][1] = "Inactivo";
                            $array_statusAseguradora[2][0] = "3";
                            $array_statusAseguradora[2][1] = "Atención especial";
                            $array_statusAseguradora[3][0] = "4";
                            $array_statusAseguradora[3][1] = "Retirado";

                            $combo->get("cmb_estatus_convenio", $cmb_statusSeguro, $array_statusAseguradora, "-- Seleccione el estado del seguro --", "", $disabled);
                            ?>
                        </td>

                    </tr>                  
                    <tr valign="top">
                        <td align="right">
                            <label class="inline"><b>Direcci&oacute;n</b></label>
                        </td>
                        <td align="left" colspan="3">
                            <?php
                            $disabled = "";
                            if ($ind_reasignar == 1) {
                                $disabled = "disabled";
                            }
                            ?>
                            <input type="text" class="input required" value="<?php echo $txt_direccion; ?>" name="txt_direccion" id="txt_direccion" maxlength="200" size="200"  onblur="trim_cadena(this);" <?= $disabled ?> />
                        </td>
                    </tr>

                    <tr valign="top">
                        <td align="right">
                            <label class="inline"><b>Observaciones&nbsp;</b></label>
                        </td>
                        <td align="left" colspan="3">
                            <div id="txt_observacion_cita" name="txt_observacion_cita"><?= $utilidades->ajustar_texto_wysiwyg($txt_observacion_cita) ?></div>
                            <br>
                        </td>
                    </tr>
                    <tr valign="top">
                        <?php
                        if ($tipo_accion == 1) { //Para CREAR una cita
                            if ($tipo_acceso_menu == 2) {
                                ?>
                                <td colspan="4">
                                    <?php
                                    if ($ind_reasignar != 1) {
                                        ?>
                                        <input type="submit" id="btn_crear_cita" nombre="btn_crear_cita" value="Crear cita" onclick="validar_crear_cita('<?php echo ($_POST["hdd_numero_menu"]); ?>');" class="btnPrincipal" />
                                        <?php
                                    } else {
                                        ?>
                                        <input type="submit" id="btn_reasignar_cita" nombre="btn_reasignar_cita" value="Reasignar cita" onclick="validar_reasignar_cita('<?php echo ($_POST["hdd_numero_menu"]); ?>');" class="btnPrincipal" />
                                        <?php
                                    }
                                    ?>
                                </td>
                                <?php
                            }
                        } else if ($tipo_accion == 2) { //Para EDITAR una cita
                            ?>
                            <td colspan="4" align="center">
                                <?php
                                if ($tipo_acceso_menu == 2 && $bol_editar) {
                                    ?>
                                    <input type="submit" id="btn_editar_cita" nombre="btn_editar_cita" value="Guardar cambios" onclick="validar_editar_cita();" class="btnPrincipal"/>
                                    &nbsp;&nbsp;
                                    <input type="submit" id="btn_cancelar_cita" nombre="btn_cancelar_cita" value="Cancelar cita" onclick="abrir_cancelar_cita();" class="btnPrincipal"/>
                                    &nbsp;&nbsp;
                                    <input type="button" id="btn_reasignar_cita" nombre="btn_reasignar_cita" value="Reasignar cita" onclick="abrir_reasignar_cita('<?php echo ($_POST["hdd_numero_menu"]); ?>');"  class="btnPrincipal"/>
                                    <?php
                                    if ($ind_confirmada == 0) {
                                        ?>
                                        &nbsp;&nbsp;
                                        <input type="button" id="btn_confirmar_cita" nombre="btn_confirmar_cita" value="Confirmar cita" onclick="confirmar_cita();" class="btnPrincipal"/>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                } else {
                                    ?>
                                    <label class="inline" for="cmb_horario_cita"><?php echo ("<b>Estado de la cita:</b> " . $nombre_estado_cita); ?></label>
                                    <?php
                                }
                                ?>
                            </td>
                            <?php
                        }
                        ?>
                    </tr>
                </table>
            </form>

            <?php
            if ($tipo_accion == 1) {
                ?>
                <div>
                    <table id="tblRecomendaciones" class="paginated modal_table" style="width: 90%; margin: auto;">
                        <thead>
                            <tr>
                                <th style="" class="th_reducido">Código</th>
                                <th style="" class="th_reducido">Recomendaciones</th>                            
                            </tr>
                        </thead>
                        <?php
                        if (count($recomendaciones) >= 1) {
                            foreach ($recomendaciones as $recomendacion) {
                                ?>
                                <tr onclick="cargar_recomendacion(<?= $recomendacion['id_cita_recom'] ?>);">
                                    <td style="width: 10%;">
                                        <?= $recomendacion['id_cita_recom'] ?>                                   
                                    </td>
                                    <td style="text-align: left;">
                                        <?= $recomendacion['titulo_cita_recom'] ?>
                                        <input type="hidden" value="<?= $recomendacion['text_cita_recom'] ?>" id="hdd_citaRecomendacion_<?= $recomendacion['id_cita_recom'] ?>" name="hdd_citaRecomendacion_<?= $recomendacion['id_cita_recom'] ?>" />
                                    </td>
                                </tr>

                                <?php
                            }
                        }
                        ?>
                    </table>
                    <script id='ajax'>
                        //<![CDATA[ 
                        $(function () {
                            $('#tblRecomendaciones', 'table').each(function (i) {
                                $(this).text(i + 1);
                            });

                            $('table.paginated').each(function () {
                                var currentPage = 0;
                                var numPerPage = 20;
                                var $table = $(this);
                                $table.bind('repaginate', function () {
                                    $table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
                                });
                                $table.trigger('repaginate');
                                var numRows = $table.find('tbody tr').length;
                                var numPages = Math.ceil(numRows / numPerPage);
                                var $pager = $('<div class="pager"></div>');
                                for (var page = 0; page < numPages; page++) {
                                    $('<span class="page-number"></span>').text(page + 1).bind('click', {
                                        newPage: page
                                    }, function (event) {
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
                </div>
                <?php
            }
            ?>

            <script id='ajax'>
                initCKEditorCitas("txt_observacion_cita");
            </script>    
            <br /><br />
        </div>

        <div id="d_cargar_hc_adm"></div>    

        <?php
        break;

    case "4": //Cargar el horario de la cita dependiento del tipo de cita
        $combo = new Combo_Box();
        $tipo_cita = $utilidades->str_decode($_POST["tipo_cita"]);
        $fecha_cita = $utilidades->str_decode($_POST["fecha_cita"]);
        $horamin_ini = $utilidades->str_decode($_POST["hora_ini"]);
        $horamin_fin = $utilidades->str_decode($_POST["hora_fin"]);
        $usuario = $utilidades->str_decode($_POST["usuario"]);
        $tipo_horario = $utilidades->str_decode($_POST["tipo_horario"]);
        $horas = intval($_POST["horas"], 10);
        $ind_buscar_horario = intval($_POST["ind_buscar_horario"], 10);

        $array_tiempo_cita = $dbAsignarCitas->getTiempoTipoCita($usuario, $tipo_cita);
        $tiempo_cita = $array_tiempo_cita["tiempo_tipo_cita"];

        //Se verifica si hay que buscar horarios disponibles
        if ($tipo_horario == 1 && $ind_buscar_horario == 1) {
            //Se obtiene la disponibilidad del especialista para el día actual
            $disponibilidad_prof_obj = $dbDisponibilidadProf->getDisponibilidadProfDia($usuario, $fecha_cita);
            if (isset($disponibilidad_prof_obj["id_disponibilidad"])) {
                $arr_horas_aux = array();
                switch ($disponibilidad_prof_obj["id_tipo_disponibilidad"]) {
                    case "11": //Completa
                        //Se verifica el día de la semana
                        $dia_semana = intval(date_format(date_create($fecha_cita), "w"), 10);

                        //Se busca la hora inicial y final del día
                        switch ($dia_semana) {
                            case 6:
                                $variable_obj = $dbVariables->getVariable(3);
                                break;
                            default:
                                $variable_obj = $dbVariables->getVariable(2);
                                break;
                        }

                        $arr_horas_aux = explode(";", $variable_obj["valor_variable"]);
                        for ($i = 0; $i < count($arr_horas_aux); $i++) {
                            $arr_horas_aux[$i] .= ":00";
                        }
                        break;
                    case "12": //Parcial
                        //Se busca la disponibilidad del usuario en el día seleccionado
                        $lista_disp_prof_det = $dbDisponibilidadProf->getDisponibilidadProfDet($disponibilidad_prof_obj["id_disponibilidad"]);
                        if (isset($lista_disp_prof_det[0])) {
                            $cont_horas = 0;
                            foreach ($lista_disp_prof_det as $disp_aux) {
                                $arr_horas_aux[$cont_horas] = $disp_aux["hora_ini_t"] . ":00";
                                $arr_horas_aux[$cont_horas + 1] = $disp_aux["hora_final_t"] . ":00";

                                $cont_horas += 2;
                            }
                        }
                        break;
                    default:
                        break;
                }

                //Se completa hasta 4 horas con espacios vacíos
                for ($i = 0; $i < 4; $i++) {
                    if (isset($arr_horas_aux[$i])) {
                        $arr_horas_aux[$i] = $fecha_cita . " " . $arr_horas_aux[$i];
                    } else {
                        $arr_horas_aux[$i] = "";
                    }
                }

                //Se buscan las disponibilidades del día
                $lista_horas_aux = $dbAsignarCitas->getFechaHoraCita2($usuario, $arr_horas_aux, 0);

                //Se empieza buscando entre las horas superiores a la hora seleccionada
                $horamin_ini = "";
                $horamin_fin = "";
                for ($i = 0; $i < count($lista_horas_aux); $i++) {
                    $hora_aux = $lista_horas_aux[$i];
                    if ($hora_aux["fecha_cita"] >= $fecha_cita . " " . $horamin_ini) {
                        if ($hora_aux["tipo_tiempo"] == "2" || $hora_aux["tipo_tiempo"] == "3" || $hora_aux["tipo_tiempo"] == "5") {
                            $horamin_ini = substr($hora_aux["fecha_cita"], 11, 5);
                            $horamin_fin = substr($lista_horas_aux[$i + 1]["fecha_cita"], 11, 5);
                            break;
                        }
                    }
                }

                //Si no se halló, se busca desde la hora inicial del día
                if ($horamin_ini == "") {
                    for ($i = 0; $i < count($lista_horas_aux); $i++) {
                        $hora_aux = $lista_horas_aux[$i];
                        if ($hora_aux["fecha_cita"] >= $fecha_cita . " 00:00:00") {
                            if ($hora_aux["tipo_tiempo"] == "2" || $hora_aux["tipo_tiempo"] == "3" || $hora_aux["tipo_tiempo"] == "5") {
                                $horamin_ini = substr($hora_aux["fecha_cita"], 11, 5);
                                $horamin_fin = substr($lista_horas_aux[$i + 1]["fecha_cita"], 11, 5);
                                break;
                            }
                        }
                    }
                }

                if ($horamin_ini != "") {
                    $arr_horas_aux = explode(":", $horamin_ini);
                    $arr_horas_aux[0] = intval($arr_horas_aux[0], 10);
                    $arr_horas_aux[1] = intval($arr_horas_aux[1], 10);
                }

                //Se actualizan las horas en el formulario
                ?>
                <script id="ajax" type="text/javascript">
                    $("#hdd_hora_ini").val("<?php echo ($horamin_ini); ?>");
                    $("#hdd_hora_fin").val("<?php echo ($horamin_fin); ?>");
                </script>
                <?php
            }
        }

        //Operacion con las horas y los minutos
        $hora_minuto_inicio = explode(":", $horamin_ini);
        $hora_minuto_final = explode(":", $horamin_fin);
        $hora_inicial = $hora_minuto_inicio[0];
        $minuto_inicial = $hora_minuto_inicio[1];
        $hora_final = $hora_minuto_final[0];
        $minuto_final = $hora_minuto_final[1];

        //Se resta el tiempo del tipo de cita a la hora final disponible para tener el espacio para la cita
        $horaminhasta = operacion_horas($hora_final, $minuto_final, 2, $tiempo_cita, 24);
        $hora_minuto_hasta = explode(":", $horaminhasta);
        $hora_hasta = $hora_minuto_hasta[0];
        $minuto_hasta = $hora_minuto_hasta[1];

        if (Date($hora_hasta . ":" . $minuto_hasta) < Date($hora_inicial . ":" . $minuto_inicial)) {
            $hora_hasta = $hora_final;
            $minuto_hasta = $minuto_final;
        }

        $minutos = intval($minuto_inicial);
        $x = 0;
        $tabla_horas_minutos = array();
        switch ($tipo_horario) {
            case "1": //Horas
                for ($i = intval($hora_inicial); $i <= intval($hora_hasta); $i++) {
                    $hora_aux = $i;
                    $hora_mostrar_aux = $i;
                    if ($hora_aux < 10) {
                        $hora_aux = "0" . $hora_aux;
                    }
                    if ($hora_mostrar_aux > 12) {
                        $hora_mostrar_aux -= 12;
                    }
                    if ($hora_mostrar_aux < 10) {
                        $hora_mostrar_aux = "0" . $hora_mostrar_aux;
                    }
                    $tabla_horas_minutos[$x][0] = $hora_aux;
                    $tabla_horas_minutos[$x][1] = $hora_mostrar_aux;
                    $x++;
                }

                $combo->get("cmb_hora_cita", "", $tabla_horas_minutos, "&nbsp;", "cargar_horario(2, this.value, 0)", "", "width:70px;");
                break;
            case "2": //Minutos
                $minutos_ini = 0;
                $minutos_fin = 59;
                if ($horas == intval($hora_inicial, 10)) {
                    $minutos_ini = $minutos;
                }
                if ($horas == intval($hora_hasta, 10)) {
                    $minutos_fin = intval($minuto_hasta, 10);
                }
                if ($minutos_ini % 5 != 0) {
                    $minuto_aux = $minutos_ini;
                    if ($minuto_aux < 10) {
                        $minuto_aux = "0" . $minuto_aux;
                    }
                    $tabla_horas_minutos[$x][0] = $minuto_aux;
                    $tabla_horas_minutos[$x][1] = $minuto_aux;
                    $x++;

                    $minutos_ini += (5 - ($minutos_ini % 5));
                }
                for ($i = $minutos_ini; $i <= $minutos_fin; $i += 5) {
                    $minuto_aux = $i;
                    if ($minuto_aux < 10) {
                        $minuto_aux = "0" . $minuto_aux;
                    }
                    $tabla_horas_minutos[$x][0] = $minuto_aux;
                    $tabla_horas_minutos[$x][1] = $minuto_aux;
                    $x++;
                }

                $combo->get("cmb_minuto_cita", "", $tabla_horas_minutos, "&nbsp;", "verificar_hora_disponible(this.value);", "", "width:70px;");
                break;
        }
        break;

    case "5": //Crear nueva cita (incluye reasignación)
        $txt_primer_nombre = $utilidades->str_decode($_POST["txt_primer_nombre"]);
        $txt_segundo_nombre = $utilidades->str_decode($_POST["txt_segundo_nombre"]);
        $txt_primer_apellido = $utilidades->str_decode($_POST["txt_primer_apellido"]);
        $txt_segundo_apellido = $utilidades->str_decode($_POST["txt_segundo_apellido"]);
        $cmb_tipo_documento = $utilidades->str_decode($_POST["cmb_tipo_documento"]);
        $txt_numero_documento = $utilidades->str_decode($_POST["txt_numero_documento"]);
        $txt_numero_telefono = $utilidades->str_decode($_POST["txt_numero_telefono"]);
        $cmb_tipo_cita = $utilidades->str_decode($_POST["cmb_tipo_cita"]);
        $cmb_horario_cita = $utilidades->str_decode($_POST["cmb_horario_cita"]);
        $hdd_fecha_cita = $utilidades->str_decode($_POST["hdd_fecha_cita"]);
        $hdd_id_usuario = $utilidades->str_decode($_POST["hdd_id_usuario"]);
        $hdd_id_paciente = $utilidades->str_decode($_POST["hdd_id_paciente"]);
        $id_usuario_crea = $utilidades->str_decode($_SESSION["idUsuario"]);
        $cmb_convenio = $utilidades->str_decode($_POST["cmb_convenio"]);
        $cmb_lentes = $utilidades->str_decode($_POST["cmb_lentes"]);
        $chk_no_pago = $utilidades->str_decode($_POST["chk_no_pago"]);
        $txt_observacion_cita = $utilidades->str_decode($_POST["txt_observacion_cita"]);
        $fecha_hora_cita = $hdd_fecha_cita . " " . $cmb_horario_cita;
        $hdd_lugar_disp = $utilidades->str_decode($_POST["hdd_lugar_disp"]);
        $ind_reasignar = $utilidades->str_decode($_POST["ind_reasignar"]);
        $id_cita_reasignar = $utilidades->str_decode($_POST["id_cita_reasignar"]);
        $ind_quitar_lista_cx = $utilidades->str_decode($_POST["ind_quitar_lista_cx"]);
        $id_reg_lista = $utilidades->str_decode($_POST["id_reg_lista"]);
        $id_prog_cx = $utilidades->str_decode($_POST["id_prog_cx"]);
        $sexo = $utilidades->str_decode($_POST["cmb_sexo"]);
        $pais = $utilidades->str_decode($_POST["cmb_pais"]);
        $fecha_nacimiento = $utilidades->str_decode($_POST["txt_fecha_nacimiento"]);
        $cmb_estatus_seguro = $utilidades->str_decode($_POST["cmb_estatus_seguro"]);
        $cmb_cod_dep_res = $utilidades->str_decode($_POST["cmb_cod_dep_res"]);
        $cmb_cod_mun_res = $utilidades->str_decode($_POST["cmb_cod_mun_res"]);
        $txt_nom_dep_res = $utilidades->str_decode($_POST["txt_nom_dep_res"]);
        $txt_nom_mun_res = $utilidades->str_decode($_POST["txt_nom_mun_res"]);
        $cmb_cod_plan = $utilidades->str_decode($_POST["cmb_cod_plan"]);
		$cmb_sede_alter = $utilidades->str_decode($_POST["cmb_sede_alter"]);
	
        $txt_direccion = $utilidades->str_decode($_POST["txt_direccion"]);
        //$cmb_exento_convenio = $utilidades->str_decode($_POST["cmb_exento_convenio"]);
        $cmb_exento_convenio = "NULL";
        $rango = $utilidades->str_decode($_POST["rango"]);
        $cmb_tipoCotizante = $utilidades->str_decode($_POST["cmb_tipoCotizante"]);

        //Tiempo de la cita
        $array_tiempo_cita = $dbAsignarCitas->getTiempoTipoCita($hdd_id_usuario, $cmb_tipo_cita);
        $tiempo_cita = $array_tiempo_cita["tiempo_tipo_cita"];

        $msg_resultado = $dbAsignarCitas->crearCita($txt_primer_nombre, $txt_segundo_nombre, $txt_primer_apellido, $txt_segundo_apellido, $cmb_tipo_documento, $txt_numero_documento, $txt_numero_telefono, $cmb_convenio, $cmb_lentes, $txt_observacion_cita, $fecha_hora_cita, $cmb_tipo_cita, $tiempo_cita, $hdd_id_usuario, $id_usuario_crea, $hdd_id_paciente, $hdd_lugar_disp, $ind_reasignar, $id_cita_reasignar, $chk_no_pago, $id_prog_cx, $sexo, $fecha_nacimiento, $pais, $cmb_estatus_seguro, $cmb_cod_dep_res, $cmb_cod_mun_res, $txt_direccion, $cmb_exento_convenio, $rango, $cmb_tipoCotizante, $txt_nom_dep_res, $txt_nom_mun_res, $cmb_cod_plan,$cmb_sede_alter);

        if ($ind_quitar_lista_cx == "1") {
            $dbListasEspera->marcar_preqx_lista_espera($id_reg_lista, $id_usuario_crea);
        }
        ?>
        <input type="hidden" value="<?php echo $msg_resultado; ?>" name="hdd_exito" id="hdd_exito" />
        <input type="hidden" value="<?php echo $hdd_fecha_cita; ?>" name="hdd_fecha" id="hdd_fecha" />
        <?php
        break;

    case "6": //Opcion para validar documento existente
        $txt_documento_cita = $utilidades->str_decode($_POST["documento_cita"]);
        $tipo = $utilidades->str_decode($_POST["tipo"]);
        $id_cita = $utilidades->str_decode($_POST["id_cita"]);
        $fecha_cita = $utilidades->str_decode($_POST["fecha_cita"]);
        //$tabla_busca_documento = $dbAsignarCitas->getBuscarDocumentoCita($txt_documento_cita, $id_cita, $fecha_cita, $tipo);
        $tabla_busca_documento = $dbAsignarCitas->getListaCitasProgramadasPersona($txt_documento_cita, $id_cita, $tipo);

        $cantidad = count($tabla_busca_documento);
        if ($cantidad >= 1) {
            $cita_aux = $tabla_busca_documento[0];
            $nombre = $funciones_persona->obtenerNombreCompleto($cita_aux["nombre_1"], $cita_aux["nombre_2"], $cita_aux["apellido_1"], $cita_aux["apellido_2"]);
            $fecha = substr($cita_aux["fecha_cita"], 0, 10);
            $hora = substr($cita_aux["fecha_cita"], 11, 2);
            $minutos = substr($cita_aux["fecha_cita"], 14, 2);
            $hora_cita = operacion_horas($hora, $minutos, 1, 0, 12);

            $ind_mismo_dia = 0;
            $fecha_mostrar = "";
            if ($fecha_cita == $fecha) {
                $ind_mismo_dia = 1;
            } else {
                $fecha_mostrar = $funciones_persona->obtenerFecha5(strtotime($fecha));
            }
            ?>
            <input type="hidden" name="hdd_documento_existe" id="hdd_documento_existe" value="1" />
            <input type="hidden" name="hdd_nombre_existe" id="hdd_nombre_existe" value="<?php echo ($nombre); ?>" />
            <input type="hidden" name="hdd_fecha_existe" id="hdd_fecha_existe" value="<?php echo ($fecha_mostrar); ?>" />
            <input type="hidden" name="hdd_hora_existe" id="hdd_hora_existe" value="<?php echo ($hora_cita); ?>" />
            <input type="hidden" name="hdd_mismo_dia_existe" id="hdd_mismo_dia_existe" value="<?php echo ($ind_mismo_dia); ?>" />
            <?php
        } else if ($cantidad == 0) {
            ?>
            <input type="hidden" name="hdd_documento_existe" id="hdd_documento_existe" value="0" />
            <?php
        }
        break;

    case "7": //Guardar cambios en una citas
        $txt_primer_nombre = $utilidades->str_decode($_POST["txt_primer_nombre"]);
        $txt_segundo_nombre = $utilidades->str_decode($_POST["txt_segundo_nombre"]);
        $txt_primer_apellido = $utilidades->str_decode($_POST["txt_primer_apellido"]);
        $txt_segundo_apellido = $utilidades->str_decode($_POST["txt_segundo_apellido"]);
        $cmb_tipo_documento = $utilidades->str_decode($_POST["cmb_tipo_documento"]);
        $txt_numero_documento = $utilidades->str_decode($_POST["txt_numero_documento"]);
        $txt_numero_telefono = $utilidades->str_decode($_POST["txt_numero_telefono"]);
        $hdd_fecha_cita = $utilidades->str_decode($_POST["hdd_fecha_cita"]);
        $id_usuario_edita = $utilidades->str_decode($_SESSION["idUsuario"]);
        $cmb_convenio = $utilidades->str_decode($_POST["cmb_convenio"]);
        $cmb_lentes = $utilidades->str_decode($_POST["cmb_lentes"]);
        $chk_no_pago = $utilidades->str_decode($_POST["chk_no_pago"]);
        $txt_observacion_cita = trim($utilidades->str_decode($_POST["txt_observacion_cita"]));
        $hdd_id_cita = $utilidades->str_decode($_POST["hdd_id_cita"]);
        $hdd_lugar_disp = $utilidades->str_decode($_POST["hdd_lugar_disp"]);
        $hdd_id_paciente = $utilidades->str_decode($_POST["hdd_id_paciente"]);
        $sexo = $utilidades->str_decode($_POST["cmb_sexo"]);
        $pais = $utilidades->str_decode($_POST["cmb_pais"]);
        $fecha_nacimiento = $utilidades->str_decode($_POST["txt_fecha_nacimiento"]);
        $cmb_estatus_seguro = $utilidades->str_decode($_POST["cmb_estatus_seguro"]);
        $cmb_exento_convenio = $utilidades->str_decode($_POST["cmb_exento_convenio"]);
        $txt_direccion = $utilidades->str_decode($_POST["txt_direccion"]);
        $rango = $utilidades->str_decode($_POST["rango"]);
        $cmb_tipoCotizante = $utilidades->str_decode($_POST["cmb_tipoCotizante"]);

        $cmb_cod_dep_res = $utilidades->str_decode($_POST["cmb_cod_dep_res"]);
        $cmb_cod_mun_res = $utilidades->str_decode($_POST["cmb_cod_mun_res"]);
        $txt_nom_dep_res = $utilidades->str_decode($_POST["txt_nom_dep_res"]);
        $txt_nom_mun_res = $utilidades->str_decode($_POST["txt_nom_mun_res"]);
        $cmb_cod_plan = $utilidades->str_decode($_POST["cmb_cod_plan"]);


        $msg_resultado = $dbAsignarCitas->actualizarCita($txt_primer_nombre, $txt_segundo_nombre, $txt_primer_apellido, $txt_segundo_apellido, $cmb_tipo_documento, $txt_numero_documento, $txt_numero_telefono, $cmb_convenio, $cmb_lentes, $txt_observacion_cita, $id_usuario_edita, $hdd_id_cita, $hdd_lugar_disp, $chk_no_pago, $hdd_id_paciente, $sexo, $fecha_nacimiento, $pais, $cmb_estatus_seguro, $cmb_cod_dep_res, $cmb_cod_mun_res, $txt_direccion, $cmb_exento_convenio, $rango, $cmb_tipoCotizante, $txt_nom_dep_res, $txt_nom_mun_res, $cmb_cod_plan);
        ?>
        <input type="hidden" value="<?php echo $msg_resultado; ?>" name="hdd_exito" id="hdd_exito" />
        <input type="hidden" value="<?php echo $hdd_fecha_cita; ?>" name="hdd_fecha" id="hdd_fecha" />
        <?php
        break;

    case "8": //Confirmación de cancelación de cita
        $hdd_id_cita = $utilidades->str_decode($_POST["hdd_id_cita"]);
        $hdd_id_usuario = $utilidades->str_decode($_POST["hdd_id_usuario"]);
        $hdd_fecha_cita = $utilidades->str_decode($_POST["hdd_fecha_cita"]);
        $hdd_lugar_disp = $utilidades->str_decode($_POST["hdd_lugar_disp"]);

        $combo = new Combo_Box();

        //Listado de tipos de cancelaciones
        $lista_tipos_cancela = $dbListas->getListaDetalles(31);
        ?>
        <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:98%">
            <tr>
                <td colspan="2" align="center"><h4>&iquest;Est&aacute; seguro de cancelar esta cita?</h4></td>
            </tr>
            <tr>
                <td align="left" style="width:16%;">
                    <label class="inline" for="cmb_tipo_cancela"><b>Tipo de cancelaci&oacute;n</b></label>
                </td>
                <td align="left" style="width:84%;">
                    <?php
                    $combo->getComboDb("cmb_tipo_cancela", "", $lista_tipos_cancela, "id_detalle, nombre_detalle", " ", "", true);
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="left" style="width:80%;">
                    <label class="inline" for="txt_observacion_cancela"><b>Observaci&oacute;n</b></label>
                    <textarea name="txt_observacion_cancela" id="txt_observacion_cancela" class="textarea_ajustable required" onblur="trim_cadena(this);"></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" value="Si" class="btnPrincipal" onclick="cancelar_cita(<?php echo ($hdd_id_cita); ?>, '<?php echo ($hdd_fecha_cita); ?>', <?php echo ($hdd_id_usuario); ?>);"/>
                    <input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" value="No" class="btnPrincipal" onclick="abrir_editar_cita(<?php echo ($hdd_id_cita); ?>, <?php echo ($hdd_id_usuario); ?>, <?php echo ($hdd_lugar_disp); ?>);"/>
                </td>
            </tr>
        </table>
        <?php
        break;

    case "9": //Cancelación de una cita
        $id_usuario = $_SESSION["idUsuario"];
        @$id_cita = $utilidades->str_decode($_POST["id_cita"]);
        @$fecha_cita = $utilidades->str_decode($_POST["fecha_cita"]);
        @$id_tipo_cancela = $utilidades->str_decode($_POST["id_tipo_cancela"]);
        @$observacion_cancela = trim($utilidades->str_decode($_POST["observacion_cancela"]));

        $msg_resultado = $dbAsignarCitas->cancelarCita($id_usuario, $id_cita, $id_tipo_cancela, $observacion_cancela);
        ?>
        <input type="hidden" value="<?php echo $msg_resultado; ?>" name="hdd_exito" id="hdd_exito" />
        <input type="hidden" value="<?php echo $fecha_cita; ?>" name="hdd_fecha" id="hdd_fecha" />
        <?php
        break;

    case "10": //Búsqueda de citas
        $txt_busca_usuario = $utilidades->str_decode($_POST["txt_busca_usuario"]);
        $cmb_lista_usuarios = $utilidades->str_decode($_POST["cmb_lista_usuarios"]);
        $tabla_buscar = $dbAsignarCitas->getBuscarCitasPersonas($txt_busca_usuario, $cmb_lista_usuarios);
        $cantidad_registro = count($tabla_buscar);
        ?>
        <div class="encabezado">
            <h3>Citas</h3>
        </div>
        <div class="cuerpo">
            <table class="modal_table">
                <tr>
                    <th align="center" style="width:15%;"><p>Fecha</p></td>
                    <th align="center" style="width:11%;"><p>Hora</p></td>
                    <th align="center" style="width:20%;"><p>Profesional</p></td>
                    <th align="center" style="width:24%;"><p>Paciente</p></td>
                    <th align="center" style="width:15%;"><p>Tipo de cita</p></td>
                    <th align="center" style="width:15%;"><p>Estado</p></td>
                </tr>
                <?php
                if ($cantidad_registro > 0) {
                    foreach ($tabla_buscar as $fila_buscar) {
                        $id_cita = $fila_buscar["id_cita"];
                        $nombres_paciente = $fila_buscar["nombre_1"] . " " . $fila_buscar["nombre_2"] . " " . $fila_buscar["apellido_1"] . " " . $fila_buscar["apellido_2"];
                        $id_usuario_prof = $fila_buscar["id_usuario_prof"];
                        $nombre_tipo_cita = $fila_buscar["nombre_tipo_cita"];
                        $nombre_profesional = $fila_buscar["nombre_usuario"] . " " . $fila_buscar["apellido_usuario"];
                        $id_lugar_cita = $fila_buscar["id_lugar_cita"];
                        $fecha_cita = substr($fila_buscar["fecha_cita"], 0, 10);
                        $fecha_cita = strtotime($fecha_cita);
                        $anio_cita = date("Y", $fecha_cita); // Year (2003)
                        $mes_cita = date("m", $fecha_cita); // Month (12)
                        $dia_cita = date("d", $fecha_cita); // day (14)
                        $fecha_cita = $funciones_persona->obtenerFecha4($fecha_cita);
                        $fecha_cita2 = $funciones_persona->obtenerFecha2($dia_cita, $mes_cita, $anio_cita);

                        $hora = substr($fila_buscar["fecha_cita"], 11, 2);
                        $minutos = substr($fila_buscar["fecha_cita"], 14, 2);
                        $hora_cita = operacion_horas(intval($hora), intval($minutos), 1, 0, 12);
                        $estado_cita = $fila_buscar["nombre_detalle"];
                        $fecha_actual = $dbCalendario->getFechaActual();
                        $permitir_accion = 1;

                        if ($fecha_actual["fecha_hoy"] > $fecha_cita2) {
                            $permitir_accion = 0;
                        }

                        if ($permitir_accion == 1) {
                            ?>
                            <tr onclick="abrir_editar_cita('<?php echo ($id_cita); ?>', '<?php echo ($id_usuario_prof); ?>', <?php echo ($id_lugar_cita); ?>);" title="<?php echo ($fila_buscar["observacion_cita"]); ?>">
                                <?php
                            } else {
                                ?>
                            <tr class="inactiva" title="<?php echo ($fila_buscar["observacion_cita"]); ?>">
                                <?php
                            }
                            ?>
                            <td align="left"><?php echo $fecha_cita; ?></td>
                            <td align="center"><?php echo $hora_cita; ?></td>
                            <td align="left"><?php echo $nombre_profesional; ?></td>
                            <td align="left"><?php echo $nombres_paciente; ?></td>
                            <td align="center"><?php echo $nombre_tipo_cita; ?></td>
                            <td align="center"><?php echo $estado_cita; ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr class="celdagrid">
                        <td align="center" colspan="6">No existen datos</td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <?php
            break;

        case "11": //Buscar persona de cita en pacientes y citas anteriores
            $documento = $utilidades->str_decode($_POST["documento"]);
            $tipo_documento = $utilidades->str_decode($_POST["tipo_documento"]);
            $hdd_fecha_cita = $utilidades->str_decode($_POST["hdd_fecha_cita"]);
            $hdd_hora_ini = $utilidades->str_decode($_POST["hdd_hora_ini"]);
            $hdd_hora_fin = $utilidades->str_decode($_POST["hdd_hora_fin"]);
            $hdd_id_usuario = $utilidades->str_decode($_POST["hdd_id_usuario"]);
            $id_lugar_cita = $utilidades->str_decode($_POST["id_lugar_cita"]);

            $id_paciente = "";
            $id_cita = "";
            $id_tipo_documento = "";
            $numero_documento = "";
            $nombre1 = "";
            $nombre2 = "";
            $apellido1 = "";
            $apellido2 = "";
            $telefono = "";
            $ind_cargar_datos = false;
            $sexo = "";
            $fecha_nacimiento = "";
            $pais = "";
            /* HCUT */
            $id_convenio = "";
            $status_convenio = "";
            $cod_dep = "";
            $cod_mun = "";
            $nom_dep = "";
            $nom_mun = "";
            $direccion = "";
            $observacion_cita = "";
            $rango = "";
            $tipoCotizante = "";
            $plan = "";

            $nom_convenio_aux = "";
            $nom_plan_aux = "";
            /* END HCUT */

            $tabla_persona = $dbPacientes->getVerificacionPost($documento, $tipo_documento);
			
            if (count($tabla_persona) > 0) {
                $id_paciente = $tabla_persona["id_paciente"];
                $id_tipo_documento = $tabla_persona["id_tipo_documento"];
                $numero_documento = $tabla_persona["numero_documento"];
                $nombre1 = $tabla_persona["nombre_1"];
                $nombre2 = $tabla_persona["nombre_2"];
                $apellido1 = $tabla_persona["apellido_1"];
                $apellido2 = $tabla_persona["apellido_2"];
                $telefono = $tabla_persona["telefono_1"];

                /* HCUT */
                $sexo = $tabla_persona["sexo"];
                $fecha_nacimiento = $tabla_persona["fecha_nacimiento_aux"];
                $pais = $tabla_persona["id_pais"];
                $id_convenio = $tabla_persona["id_convenio_paciente"];
                $status_convenio = $tabla_persona["status_convenio_paciente"];
                $exento_convenio = $tabla_persona["exento_pamo_paciente"];
                $rango = $tabla_persona["rango_paciente"];
                $cod_dep = $tabla_persona["cod_dep"];
                $cod_mun = $tabla_persona["cod_mun"];
                $nom_dep = $tabla_persona["nom_dep"];
                $nom_mun = $tabla_persona["nom_mun"];
                $direccion = $tabla_persona["direccion"];
                $txt_query_convenio = $tabla_persona["Txtconvenio"];
                $tipoCotizante = $tabla_persona["tipo_coti_paciente"];
                $nom_convenio_aux = $tabla_persona["Txtconvenio"];
                $nom_plan_aux = $tabla_persona["nombre_plan_aux"];
                $plan = $tabla_persona["id_plan"];
				
                /* END HCUT */
                //Se busca la información de la entidad de la última admisión
                /* $admision_obj = $dbAdmision->get_ultima_admision($id_paciente);
                  if (isset($admision_obj["id_admision"])) {
                  $id_convenio = $admision_obj["id_convenio"];
                  } else {
                  //Se busca la información de la entidad de la última cita
                  $cita_obj = $dbAsignarCitas->getUltimaCitaPersona($documento, $tipo_documento);

                  if (isset($cita_obj)) {
                  $id_convenio = $cita_obj["id_convenio"];
                  }
                  } */

                $ind_cargar_datos = true;
            } else {
                //Se busca la información en citas anteriores
                $tabla_persona = $dbAsignarCitas->getUltimaCitaPersona($documento, $tipo_documento);

                if (count($tabla_persona) > 0) {
                    $id_cita = $tabla_persona["id_cita"];
                    $id_tipo_documento = $tabla_persona["id_tipo_documento"];
                    $numero_documento = $tabla_persona["numero_documento"];
                    $nombre1 = $tabla_persona["nombre_1"];
                    $nombre2 = $tabla_persona["nombre_2"];
                    $apellido1 = $tabla_persona["apellido_1"];
                    $apellido2 = $tabla_persona["apellido_2"];
                    $telefono = $tabla_persona["telefono_contacto"];
                    $id_convenio = $tabla_persona["id_convenio"];
                    $ind_cargar_datos = true;

                    /* HCUT */
                    $sexo = $tabla_persona["sexo"];
                    $fecha_nacimiento = $tabla_persona["fecha_nacimiento_aux"];
                    $pais = $tabla_persona["id_pais"];
                    $status_seguro = $tabla_persona["status_seguro_paciente"];
                    /* END HCUT */
                }
            }
            if ($ind_cargar_datos) {
                ?>
                <input type="hidden" name="hdd_documento_persona" id="hdd_documento_persona" value="true" />
                <input type="hidden" name="hdd_id_paciente_pc" id="hdd_id_paciente_pc" value="<?php echo ($id_paciente); ?>" />
                <input type="hidden" name="hdd_id_cita_pc" id="hdd_id_cita_pc" value="<?php echo ($id_cita); ?>" />
                <input type="hidden" name="hdd_id_tipo_documento_pc" id="hdd_id_tipo_documento_pc" value="<?php echo ($id_tipo_documento); ?>" />
                <input type="hidden" name="hdd_numero_documento_pc" id="hdd_numero_documento_pc" value="<?php echo ($numero_documento); ?>" />
                <input type="hidden" name="hdd_nombre1_pc" id="hdd_nombre1_pc" value="<?php echo ($nombre1); ?>" />
                <input type="hidden" name="hdd_nombre2_pc" id="hdd_nombre2_pc" value="<?php echo ($nombre2); ?>" />
                <input type="hidden" name="hdd_apellido1_pc" id="hdd_apellido1_pc" value="<?php echo ($apellido1); ?>" />
                <input type="hidden" name="hdd_apellido2_pc" id="hdd_apellido2_pc" value="<?php echo ($apellido2); ?>" />
                <input type="hidden" name="hdd_telefono_pc" id="hdd_telefono_pc" value="<?php echo ($telefono); ?>" />
                <input type="hidden" name="hdd_id_convenio_pc" id="hdd_id_convenio_pc" value="<?php echo ($id_convenio); ?>" />
                <input type="hidden" name="hdd_id_plan_pc" id="hdd_id_plan_pc" value="<?php echo ($plan); ?>" />
                <input type="hidden" name="hdd_sexo" id="hdd_sexo" value="<?php echo ($sexo); ?>" />
                <input type="hidden" name="hdd_fecha_nacimiento" id="hdd_fecha_nacimiento" value="<?php echo ($fecha_nacimiento); ?>" />
                <input type="hidden" name="hdd_pais" id="hdd_pais" value="<?php echo ($pais); ?>" />
                <input type="hidden" name="hdd_status_convenio" id="hdd_status_convenio" value="<?php echo ($status_convenio); ?>" />
                <input type="hidden" name="hdd_exento_convenio" id="hdd_exento_convenio" value="<?php echo ($exento_convenio); ?>" />
                <input type="hidden" name="hdd_rango" id="hdd_rango" value="<?php echo ($rango); ?>" />
                <input type="hidden" name="hdd_cod_dep" id="hdd_cod_dep" value="<?php echo ($cod_dep); ?>" />
                <input type="hidden" name="hdd_cod_mun" id="hdd_cod_mun" value="<?php echo ($cod_mun); ?>" />
                <input type="hidden" name="hdd_nom_dep" id="hdd_nom_dep" value="<?php echo ($nom_dep); ?>" />
                <input type="hidden" name="hdd_nom_mun" id="hdd_nom_mun" value="<?php echo ($nom_mun); ?>" />
                <input type="hidden" name="hdd_direccion" id="hdd_direccion" value="<?php echo ($direccion); ?>" />
                <input type="hidden" name="hdd_tipoCotizante" id="hdd_tipoCotizante" value="<?php echo ($tipoCotizante); ?>" />
                <input type="hidden" name="hdd_nom_convenio" id="hdd_nom_convenio" value="<?php echo ($nom_convenio_aux); ?>" />
                <input type="hidden" name="hdd_nom_plan" id="hdd_nom_plan" value="<?php echo ($nom_plan_aux); ?>" />

                <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:98%;">
                    <tr>
                        <th align="center">
                            <h4>Existe un paciente con este n&uacute;mero de documento:</h4>
                            <h4><?= $documento . " (<span style='font-weight: bold;'>" . $nombre1 . " " . $nombre2 . " " . $apellido1 . " " . $apellido2 . "</span>)" ?></h4>

                            <h4>Convenio: <span style="color: #399000"><?= $txt_query_convenio ?></span></h4>
                            <?php
                            $texto_estado_seguro = "";
                            $estilo_texto_estado_seguro = "color: #DD5043;";
                            switch ($status_convenio) {
                                case 1:
                                    $texto_estado_seguro = "Activo";
                                    $estilo_texto_estado_seguro = "color: #399000;";
                                    break;
                                case 2:
                                    $texto_estado_seguro = "Inactivo";
                                    break;
                                case 3:
                                    $texto_estado_seguro = "Atención especial";
                                    break;
                                case 4:
                                    $texto_estado_seguro = "Retirado";
                                    break;
                            }
                            ?>


                            <h4>Estado del seguro: <span style="<?= $estilo_texto_estado_seguro ?>"><?= $texto_estado_seguro ?></span></h4>
                            <h4><span style="color: #E3B02F;">&iquest;Desea cargar estos datos al formulario?</span></h4>
                        </th>
                    </tr>
                    <tr>
                        <th align="center" style="width:5%">
                            <input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" value="Si" onclick="cargar_datos_paciente();
                                                validar_lista_espera();" class="btnPrincipal" />
                            <input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" value="No" onclick="cerrar_documento_persona();" class="btnPrincipal" />
                        </th>
                    </tr>
                </table>
                <?php
            } else {
                ?>
                <input type="hidden" name="hdd_documento_persona" id="hdd_documento_persona" value="false" />
                <?php
            }

            break;

        case "12": //Manejo de horas de cita seleccionadas
            $id_usuario = $_SESSION["idUsuario"];
            @$id_usuario_prof = $utilidades->str_decode($_POST["id_usuario_prof"]);
            @$fecha_cita = $utilidades->str_decode($_POST["fecha_cita"]);
            @$hora_cita = $utilidades->str_decode($_POST["hora_cita"]);
            @$minuto_cita = $utilidades->str_decode($_POST["minuto_cita"]);
            @$id_tipo_cita = $utilidades->str_decode($_POST["id_tipo_cita"]);

            $fecha_cita_aux = "";
            if ($hora_cita != "" && $minuto_cita != "") {
                $fecha_cita_aux = $fecha_cita . " " . $hora_cita . ":" . $minuto_cita . ":00";
            }

            //Se marca la cita
            $id_usuario_aux = $dbAsignarCitas->marcar_horario_cita($id_usuario, $id_usuario_prof, $fecha_cita_aux, $id_tipo_cita);
            $nombre_usuario_aux = "";
            $hora_ini_aux = "";
            $hora_fin_aux = "";
            if ($id_usuario_aux != "0" && $id_usuario_aux != $id_usuario) {
                //Se buscan los datos del bloqueo
                $temporal_cita_obj = $dbAsignarCitas->get_temporal_cita($id_usuario_aux);
                if (isset($temporal_cita_obj["id_usuario"])) {
                    $nombre_usuario_aux = $temporal_cita_obj["nombre_usuario"] . " " . $temporal_cita_obj["apellido_usuario"];
                    $hora_ini_aux = $temporal_cita_obj["hora_ini"];
                    $hora_fin_aux = $temporal_cita_obj["hora_fin"];
                }
            } else {
                $id_usuario_aux = 0;
            }
            ?>
            <input type="hidden" name="hdd_usuario_horario_cita" id="hdd_usuario_horario_cita" value="<?php echo ($id_usuario_aux); ?>" />
            <input type="hidden" name="hdd_nombre_usuario_horario_cita" id="hdd_nombre_usuario_horario_cita" value="<?php echo ($nombre_usuario_aux); ?>" />
            <input type="hidden" name="hdd_hora_ini_horario_cita" id="hdd_hora_ini_horario_cita" value="<?php echo ($hora_ini_aux); ?>" />
            <input type="hidden" name="hdd_hora_fin_horario_cita" id="hdd_hora_fin_horario_cita" value="<?php echo ($hora_fin_aux); ?>" />
            <?php
            break;

        case "13": //Liberación de horarios seleccionados
            $id_usuario = $_SESSION["idUsuario"];
            $dbAsignarCitas->borrar_temporal_cita($id_usuario);
            break;

        case "14": //Confirmación de cita
            $id_usuario = $_SESSION["idUsuario"];
            @$id_cita = $_POST["id_cita"];
            @$fecha_cita = $_POST["fecha_cita"];
            @$txt_observacion_cita = trim($utilidades->str_decode($_POST["txt_observacion_cita"]));
			
            $resultado = $dbAsignarCitas->confirmarCita($id_cita, $id_usuario, $txt_observacion_cita);
            ?>
            <input type="hidden" value="<?php echo $resultado; ?>" name="hdd_exito" id="hdd_exito" />
            <input type="hidden" value="<?php echo $fecha_cita; ?>" name="hdd_fecha" id="hdd_fecha" />
            <?php
            break;

        case "15": //Mostrar menjase en citas
            $mensaje_citas = $dbVariables->getVariable(10);
            if ($mensaje_citas["valor_variable"] != "") {
                ?>
                <div class="div_mensaje_citas"><?php echo $mensaje_citas["valor_variable"]; ?></div>
                <?php
            }
            break;

        case "16": //Se valida si la persona seleccionada se encuentra en alguna lista de espera de cirugías
            @$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
            @$id_tipo_cita = $utilidades->str_decode($_POST["id_tipo_cita"]);

            //Se buscan los datos del tipo de cita
            $tipo_cita_obj = $dbTiposCitas->get_tipo_cita($id_tipo_cita);
            $ind_hallado = false;
            if (isset($tipo_cita_obj["ind_preqx"]) && $tipo_cita_obj["ind_preqx"] == "1") {
                //Se busca el paciente en las listas de espera
                $espera_obj = $dbListasEspera->get_registro_espera_paciente_estado($id_paciente, 235);

                if (isset($espera_obj["id_reg_lista"])) {
                    $ind_hallado = true;
                    ?>
                    <input type="hidden" id="hdd_reg_lista_b" value="<?php echo ($espera_obj["id_reg_lista"]); ?>" />
                    <input type="hidden" id="hdd_tipo_cirugia_b" value="<?php echo ($espera_obj["tipo_cirugia"]); ?>" />
                    <input type="hidden" id="hdd_fecha_lista_b" value="<?php echo ($espera_obj["fecha_lista_t"]); ?>" />
                    <?php
                }
            }

            if (!$ind_hallado) {
                ?>
                <input type="hidden" id="hdd_reg_lista_b" value="0" />
                <?php
            }
            break;

        case "17": //Se carga el combo de municipios
            @$cod_dep = $utilidades->str_decode($_POST["cod_dep"]);

            $combo = new Combo_Box();
            $lista_municipios = $dbDepMuni->getMunicipiosDepartamento($cod_dep);
            $combo->getComboDb("cmb_cod_mun_res", "", $lista_municipios, "cod_mun_dane,nom_mun", "-- Seleccione --", "", 1, "width:100%;");
            break;

        case "18":
            @$idPaciente = $utilidades->str_decode($_POST["idPaciente"]);
            $rta_aux = $dbCitas->getHistoricoCitasByIdPaciente($idPaciente);

            //echo var_dump($rta_aux);
            ?>
            <div style="width: 100%;overflow: overlay;">
                <table id="tblHistoricoCitas" class="paginated modal_table" style="margin: auto;">
                    <thead>
                        <tr>
                            <td colspan="7" align="left">
                                <?php
                                if (count($rta_aux) >= $cantidad_total_aux) {
                                    ?>
                                    Se encontraron <b><?php echo(count($rta_aux)) ?></b> registros
                                    <?php
                                } else {
                                    ?>
                                    Se muestran los primeros <b><?php echo(count($rta_aux)) ?></b> registros de <?php echo($cantidad_total_aux); ?> encontrados
                                    <?php
                                }
                                ?>
                            </td>
                        <tr>
                        <tr>
                            <th style="" class="th_reducido">Fecha</th>
                            <th style="" class="th_reducido">Hora</th>                          
                            <th style="" class="th_reducido">Paciente</th>
                            <th style="" class="th_reducido">Lugar cita</th>
                            <th style="" class="th_reducido">Tipo de cita</th>
                            <th style="" class="th_reducido">Estado</th>
                        </tr>
                    </thead>
                    <?php
                    if (count($rta_aux) >= 1) {
                        foreach ($rta_aux as $value) {
                            ?>
                            <tr onclick="generarPDFCita(<?php echo $value['id_cita']; ?>)" title="<?php
                            //Imprime la Observacion de la cita en el tooltip
                            if ($value['id_estado_cita'] == 14 || $value['id_estado_cita'] == 16 || $value['id_estado_cita'] == 17 || $value['id_estado_cita'] == 82) {//Muestra la Observación
                                echo strip_tags($value['observacion_cita']);
                            }
                            if ($value['id_estado_cita'] == 15 || $value['id_estado_cita'] == 18) {//Muestra la Observación
                                echo strip_tags($value['observacion_cancela']);
                            }
                            $lugar_cita = $dbListas->getDetalle($value['id_lugar_cita']);
                            ?>">
                                <td class="td_reducido"><?php
                                    $fecha_cita_aux = substr($value['fecha_cita'], 0, 10);
                                    $fecha_cita_aux = strtotime($fecha_cita_aux);
                                    echo $funciones_persona->obtenerFecha4($fecha_cita_aux);
                                    ?></td>
                                <td style="" class="td_reducido"><?php
                                    $hora = substr($value['hora_aux'], 0, 2);
                                    $minutos = substr($value['hora_aux'], 3, 4);
                                    $hora_cita = operacion_horas(intval($hora), intval($minutos), 1, 0, 12);
                                    echo $hora_cita;
                                    ?></td>
                                <td class="td_reducido"><?php echo $value['profesional_aux']; ?></td>                                
                                <td class="td_reducido"><?php echo $lugar_cita['nombre_detalle']; ?></td>
                                <td class="td_reducido"><?php echo $value['nombre_tipo_cita']; ?></td>
                                <td class="td_reducido"><?php echo $value['nombre_detalle']; ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <td colspan="7">No hay resultados</td>
                        <?php
                    }
                    ?>
                </table>
                <script id='ajax'>
                            //<![CDATA[ 
                            $(function () {
                                $('#tblHistoricoCitas', 'table').each(function (i) {
                                    $(this).text(i + 1);
                                });

                                $('#tblHistoricoCitas').each(function () {
                                    var currentPage = 0;
                                    var numPerPage = 5;
                                    var $table = $(this);
                                    $table.bind('repaginate', function () {
                                        $table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
                                    });
                                    $table.trigger('repaginate');
                                    var numRows = $table.find('tbody tr').length;
                                    var numPages = Math.ceil(numRows / numPerPage);
                                    var $pager = $('<div class="pager"></div>');
                                    for (var page = 0; page < numPages; page++) {
                                        $('<span class="page-number"></span>').text(page + 1).bind('click', {
                                            newPage: page
                                        }, function (event) {
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
            </div>
            <?php
            break;

        case "19"://Cargar el componente de historia clínica para un paciente dado. d_cargar_hc_adm
            @$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
            $contenidoHtml->ver_historia($id_paciente);
            break;

        case "20": //Se carga el combo de planes
            @$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);

            $combo = new Combo_Box();
            $lista_planes = $dbPlanes->getListaPlanesActivos($id_convenio);
            $combo->getComboDb("cmb_cod_plan", "", $lista_planes, "id_plan,nombre_plan", "-- Seleccione --", "", 1, "width:100%;");
            break;
    }
    ?>
