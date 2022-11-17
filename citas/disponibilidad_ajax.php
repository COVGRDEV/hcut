<?php
/*
  Página que permite los procedimiento ajax
  Autor: Juan Pablo Gomez Quiroga - 01/10/2013
 */

header("Content-Type: text/xml; charset=UTF-8");

session_start();

require_once("../db/DbUsuarios.php");
require_once("../db/DbCalendario.php");
require_once("../db/DbVariables.php");
require_once("../db/DbDisponibilidadProf.php");
require_once("../db/DbListas.php");
require_once("../db/DbTiemposCitasProf.php");
require_once("../db/DbConvenios.php");
require_once("../db/DbPerfiles.php");
require_once("../principal/ContenidoHtml.php");
require_once("../funciones/Utilidades.php");

$dbUsuarios = new DbUsuarios();
$dbCalendario = new DbCalendario();
$dbVariables = new Dbvariables();
$dbDisponibilidadProf = new DbDisponibilidadProf();
$dbListas = new DbListas();
$dbTiemposCitasProf = new DbTiemposCitasProf();
$dbConvenios = new DbConvenios();
$dbPerfiles = new DbPerfiles();

$contenido = new ContenidoHtml();
$utilidades = new Utilidades();

$contenido->validar_seguridad(1);
$tipo_acceso_menu = $contenido->obtener_permisos_menu($utilidades->str_decode($_POST["hdd_numero_menu"]));

$opcion = $utilidades->str_decode($_POST["opcion"]);

//Funcion calendario
function buscar_calendario($fecha, $calendario) {
    $resultado = "sin_atencion";
    foreach ($calendario as $fila_calendario) {
        $fecha_calendario = $fila_calendario["fecha_cal"];
        $indicador = $fila_calendario["ind_laboral"];

        if ($indicador == 0) {
            $indicador = "sin_atencion";
        } else if ($indicador == 1) {
            $indicador = "disponible";
        }
        if ($fecha == $fecha_calendario) {
            $resultado = $indicador;
        }
    }
    return $resultado;
}

function obtener_dias_mes($mes, $ano) {
	$num_dias = 0;
	switch ($mes) {
		case 1:
		case 3:
		case 5:
		case 7:
		case 8:
		case 10:
		case 12:
			$num_dias = 31;
			break;
		case 4:
		case 6:
		case 9:
		case 11:
			$num_dias = 30;
			break;
		case 2:
			if (($ano % 4 == 0) && ($ano % 100 != 0 || $ano % 400 == 0)) {
				$num_dias = 29;
			} else {
				$num_dias = 28;
			}
			break;
	}
	
	return $num_dias;
}

switch ($opcion) {
    case "1"://Crea el div Disponibilidad de especialistas
        //variables recibidas
        @$profesional = $utilidades->str_decode($_POST["profesional"]);
		@$idusuario = $utilidades->str_decode($_POST["idusuario"]);
		@$mes = $utilidades->str_decode($_POST["mes"]);
		@$ano = $utilidades->str_decode($_POST["ano"]);
		
        $lista_disponibilidades = $dbListas->getListaDetalles(3);
		$lista_lugares_disp = $dbListas->getListaDetalles(12);
		
		if (trim($profesional) == "") {
			//Se busc el nombre del usuario
			$usuario_obj = $dbUsuarios->getUsuario($idusuario);
			$profesional = $usuario_obj["nombre_usuario"]." ".$usuario_obj["apellido_usuario"];
		}
        ?>
        <script type="text/javascript" id="ajax">
            //Llama la funcion calendario
            $(document).ready(function() {
                calendario("<?php echo($mes); ?>", "<?php echo($ano); ?>");
            });
        </script>
        <table border="0" width="90%" cellpadding="0" cellspacing="0" align="center">
            <tr>
                <td align="left" width="90%">
                    <h5 id="profesional"><?php echo($profesional); ?></h5>
                </td>
                <td align="right" width="5%">
                	<a href="#" onclick="regresar_disponibilidad();"><img border="0" src="../imagenes/btn-mes-ant.png" /></a>
                </td>
                <td align="left" width="5%"><h5 style="cursor: pointer;" onclick="regresar_disponibilidad();">&nbsp;Volver</h5></td>
            </tr>
            <tr>
            	<td align="left">
                	<table border="0" cellpadding="5" cellspacing="0" width="100%">
                    	<tr>
                        	<td style="width:1%;">
			                	<label class="inline">Disponibilidad&nbsp;para&nbsp;todo&nbsp;el&nbsp;mes</label>
                            </td>
                            <td style="width:1%;">
                                <select name="cmb_disponibilidad_mes" id="cmb_disponibilidad_mes">
                                    <?php
                                        foreach ($lista_disponibilidades as $disponibilidad_aux) {
                                            if ($disponibilidad_aux["id_detalle"] != "12") {
                                    ?>
                                    <option value="<?php echo($disponibilidad_aux["id_detalle"]); ?>"><?php echo($disponibilidad_aux["nombre_detalle"]); ?></option>
                                    <?php
                                            }
                                        }
                                    ?>
                                </select>
                            </td>
                        	<td style="width:1%;">
			                	<label class="inline">lugar</label>
                            </td>
                            <td style="width:1%;">
                                <select name="cmb_lugar_disp_mes" id="cmb_lugar_disp_mes">
                                    <?php
                                        foreach ($lista_lugares_disp as $lugar_aux) {
                                    ?>
                                    <option value="<?php echo($lugar_aux["id_detalle"]); ?>"><?php echo($lugar_aux["nombre_detalle"]); ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </td>
                            <td style="width:1%;">
                            	<input type="button" class="btnPrincipal peq" onclick="validar_aplicar_disponibilidad_mes();" value="Aplicar" />
                            </td><td style="width:94%;"></td>
                            </td><td style="width:1%;">
                            	<input type="button" class="btnPrincipal peq" onclick="validar_duplicar_disp_ult_semana();" value="Duplicar &uacute;ltima semana" />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div id="agenda"></div>
                    <input type="hidden" value="<?php echo $idusuario; ?>" name="hdd_id_usuario" id="hdd_id_usuario" />
                    <input type="hidden" value="" name="hdd_pa_rta" id="hdd_pa_rta" />
                </td>
            </tr>
        </table>
        <?php
        break;
		
    case "2"://Crea el calendario de Disponibilidad de especialistas
        $fecha_actual_aux = $dbVariables->getFechaactual();
		
        $lista_disponibilidades = $dbListas->getListaDetalles(3);
        $lista_lugares_disp = $dbListas->getListaDetalles(12);
		
        $fecha_calendario = array();
        if ($utilidades->str_decode($_POST["mes"]) == "" || $utilidades->str_decode($_POST["anio"]) == "") {
            $mes_hoy = $dbCalendario->getMesActual();
            $anio_hoy = $dbCalendario->getAnioActual();

            $fecha_calendario[1] = intval($mes_hoy["mes"]);
            if ($fecha_calendario[1] < 10)
                $fecha_calendario[1] = "0" . $fecha_calendario[1];
            $fecha_calendario[0] = $anio_hoy["anio"];
        } else {
            $fecha_calendario[1] = intval($utilidades->str_decode($_POST["mes"]));
            if ($fecha_calendario[1] < 10)
                $fecha_calendario[1] = "0" . $fecha_calendario[1];
            else
                $fecha_calendario[1] = $fecha_calendario[1];
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
		
        /* calculamos las filas de la tabla */
        $tope = $dias[intval($fecha_calendario[1])] + $diasantes;
        if ($tope % 7 != 0) {
            $totalfilas = intval(($tope / 7) + 1);
		} else {
            $totalfilas = intval(($tope / 7));
		}
		
        /* Se obtiene la disponibilidad del calendario */
        $calendario_disponible = $dbCalendario->getCalendario($fecha_calendario[1], $fecha_calendario[0]);
		
        //recibe el id del usuario del hidden hdd_id_usuario
        $hdd_id_usuario = $utilidades->str_decode($_POST["hdd_id_usuario"]);
        //Sentencia que obtiene el registro de la tabla disponibilidad_prof donde el usuario sea = $hdd_id_usuario 
        $rta = $dbDisponibilidadProf->getDisponibilidadProf($hdd_id_usuario, $fecha_calendario[0], $fecha_calendario[1]);
		
        $fechaanterior = date("Y-m-d", mktime(0, 0, 0, $fecha_calendario[1] - 1, 01, $fecha_calendario[0]));
        $fechasiguiente = date("Y-m-d", mktime(0, 0, 0, $fecha_calendario[1] + 1, 01, $fecha_calendario[0]));
		
        $anio_siguiente = substr($fechasiguiente, 0, 4);
        $mes_siguiente = substr($fechasiguiente, 5, 2);
		
        $anio_anterior = substr($fechaanterior, 0, 4);
        $mes_anterior = substr($fechaanterior, 5, 2);
        ?>
        <input type="hidden" name="hdd_mes_sel" id="hdd_mes_sel" value="<?php echo($utilidades->str_decode($_POST["mes"])); ?>" />
        <input type="hidden" name="hdd_ano_sel" id="hdd_ano_sel" value="<?php echo($utilidades->str_decode($_POST["anio"])); ?>" />
        <div class="calendario-container volumen">
            <div class="encabezado">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td align="center" valign="bottom" width="10%">
                        	<a href="#" title="Mes anterior" onclick="calendario('<?php echo $mes_anterior ?>', '<?php echo $anio_anterior ?>');"><img border="0" src="../imagenes/btn-mes-ant.png" /></a>
                        </td>
                        <td align="center" width="80%"><h2><?php echo($meses[intval($fecha_calendario[1])]." ".$fecha_calendario[0]); ?></h2></td>
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
            <table class="calendario" style="width: 900px;" cellspacing="0" cellpadding="0">
                <tr>
                    <th style="width:14%;">Lunes</th>
                    <th style="width:14%;">Martes</th>
                    <th style="width:14%;">Mi&eacute;rcoles</th>
                    <th style="width:14%;">Jueves</th>
                    <th style="width:14%;">Viernes</th>
                    <th style="width:14%;">S&aacute;bado</th>
                    <th style="width:14%;">Domingo</th>
                </tr>
                <tr>
                    <?php
                    /* inicializamos filas de la tabla */
                    $tr = 0;
                    $dia = 1;

                    for ($i = 1; $i <= $diasdespues; $i++) {
                        if ($tr < $totalfilas) {
                            if ($i >= $primeromes && $i <= $tope) {
                                /* creamos fecha completa */
                                if ($dia < 10) {
                                    $dia_actual = "0" . $dia;
                                } else {
                                    $dia_actual = $dia;
                                }

                                $fecha_completa = $fecha_calendario[0] . "-" . $fecha_calendario[1] . "-" . $dia_actual;
                                /* si es hoy coloreamos la celda */
                                $fecha_actual = $dbCalendario->getFechaActual();

                                //Se busca la disponibilidad del la fecha
                                $estado = buscar_calendario($fecha_completa, $calendario_disponible);

                                //Agrega el combo_box dependiendo de la clase.
                                $combo_box = "";
                                $estilo = $estado;
                                $id_disponibilidad = -1;
                                $disponibilidad_sel = -1;
								$lugar_disp_sel = -1;
                                /*                                 * ******************************* */
                                if ($estado == "disponible") {
                                    $disponibilidad_sel = 0;
									$lugar_disp_sel = 0;
                                    for ($a = 0; $a <= count($rta) - 1; $a++) {
                                        if ($fecha_completa == $rta[$a]["fecha_cal"]) {
                                            $id_disponibilidad = $rta[$a]["id_disponibilidad"];
                                            $disponibilidad_sel = $rta[$a]["id_tipo_disponibilidad"];
											$lugar_disp_sel = $rta[$a]["id_lugar_disp"];
                                            if ($rta[$a]["id_tipo_disponibilidad"] == 13) {//Si es igual a no disponible
                                                //Asigna el estilo
                                                $estilo = "no_disponible";
                                            } else if ($rta[$a]["id_tipo_disponibilidad"] == 11) {//Si es igual a completa
                                                //Asigna el estilo
                                                $estilo = "completa";
                                            } else if ($rta[$a]["id_tipo_disponibilidad"] == 12) {//Si es igual a parcial
                                                //Asigna el estilo
                                                $estilo = "parcial";
                                            }
                                        }
                                    }

                                    if ($estilo == "disponible") {
                                        $estilo = "sin_seleccion";
                                    }

                                    //Crea el combo box
                                    $ancho_aux = 120;
                                    if ($disponibilidad_sel == 12) {
                                        $ancho_aux = 80;
                                    }

                                    if ($fecha_completa >= $fecha_actual_aux["fecha_actual"] && $tipo_acceso_menu == 2) { //Si fecha es menor o igual a la fecha actual
                                        $combo_box = '<select name="cmbDisponibilidad'.$dia.'" id="cmbDisponibilidad'.$dia.'" style="width: '.$ancho_aux.'px;" onChange="guardar_disponibilidad(\''.$fecha_calendario[0].'\', \''.$fecha_calendario[1].'\', \''.$dia.'\')" class="no_padding"><option value="0"></option>';
                                        foreach ($lista_disponibilidades as $disp_aux) {
                                            $selected_aux = "";
                                            if ($disp_aux["id_detalle"] == $disponibilidad_sel) {
                                                $selected_aux = " selected";
                                            }
                                            $combo_box .= "<option value='".$disp_aux["id_detalle"]."'".$selected_aux.">".$disp_aux["nombre_detalle"]."</option>";
                                        }
                                        $combo_box .= "</select>";
                                    } else {
                                        $combo_box = '<select disabled="disabled" name="cmbDisponibilidad'.$dia.'" id="cmbDisponibilidad'.$dia.'" style="width: '.$ancho_aux.'px;" class="no_padding"><option value="0"></option>';
                                        foreach ($lista_disponibilidades as $disp_aux) {
                                            $selected_aux = "";
                                            if ($disp_aux["id_detalle"] == $disponibilidad_sel) {
                                                $selected_aux = " selected";
                                            }
                                            $combo_box .= "<option value='".$disp_aux["id_detalle"]."'".$selected_aux.">".$disp_aux["nombre_detalle"]."</option>";
                                        }
                                        $combo_box .= "</select>";
                                    }
                                } else if ($estado == "sin_atencion") {
                                    //Crea el combo box
                                    $combo_box = "";
                                }

                                if ($fecha_completa < $fecha_actual_aux["fecha_actual"]) {
                                    $estilo .= " opaco";
                                }

                                if ($fecha_actual["fecha_hoy"] == $fecha_completa) {
                                    $estilo .= " hoy";
                                }
                                ?>
                                <td class="<?php echo($estilo); ?>" name="d_dia_<?php echo($dia) ?>" id="d_dia_<?php echo($dia) ?>">
                                    <?php echo($dia); ?><br />
                                    <?php echo($combo_box); ?>
                                    <input type="hidden" name="hdd_id_disponibilidad_<?php echo($dia) ?>" id="hdd_id_disponibilidad_<?php echo($dia) ?>" value="<?php echo($id_disponibilidad); ?>" />
                                    <?php
                                    if ($disponibilidad_sel == 12) {
                                        ?>
                                        <input type="button" name="btn_asignar_parcial_<?php echo($dia) ?>" id="btn_asignar_parcial_<?php echo($dia) ?>" value="..." onclick="configurar_disponibilidad(<?php echo($fecha_calendario[0]); ?>, <?php echo($fecha_calendario[1]); ?>, <?php echo($dia); ?>, <?php echo($lugar_disp_sel); ?>);" class="btnGeneral" />
                                        <?php
                                    }
                                    if ($disponibilidad_sel != -1) {
                                        ?>
                                        <input type="hidden" name="hdd_tipo_disponibilidad_<?php echo($dia); ?>" id="hdd_tipo_disponibilidad_<?php echo($dia); ?>" value="<?php echo($disponibilidad_sel); ?>" />
                                        <?php
                                    }
									
									if ($combo_box != "") {
										$disabled_aux = "";
										if ($fecha_completa < $fecha_actual_aux["fecha_actual"] || $tipo_acceso_menu != 2) {
											$disabled_aux = " disabled=\"disabled\"";
										}
                                    ?>
                                    <select name="cmb_lugar_disp_<?php echo($dia); ?>" id="cmb_lugar_disp_<?php echo($dia); ?>" class="no_padding" style="width:120px;"<?php echo($disabled_aux); ?> onchange="guardar_disponibilidad('<?php echo($fecha_calendario[0]); ?>', '<?php echo($fecha_calendario[1]); ?>', '<?php echo($dia); ?>');">
                                        <?php
                                        	foreach ($lista_lugares_disp as $lugar_aux) {
												$selected_aux = "";
												if ($lugar_disp_sel == $lugar_aux["id_detalle"]) {
													$selected_aux = " selected=\"selected\"";
													
												}
										?>
                                        <option value="<?php echo($lugar_aux["id_detalle"]); ?>"<?php echo($selected_aux); ?>><?php echo($lugar_aux["nombre_detalle"]); ?></option>
                                        <?php
											}
										?>
                                    </select>
                                    <?php
									}
									?>
                                </td>
                                <?php
                                $dia++;
                            } else {
                                ?>
                                <td class="sin_atencion" style="width:14%;">&nbsp;</td>
                                <?php
                            }

                            if ($i == 7 || $i == 14 || $i == 21 || $i == 28 || $i == 35 || $i == 42) {
                                ?>
                            </tr>
                            <tr>
                                <?php
                                $tr++;
                            }
                        }
                    }
                    ?>
            </table>
            <p>&laquo; <a href="#" onclick="calendario('<?php echo $mes_anterior; ?>', '<?php echo $anio_anterior; ?>');" class="anterior">Mes Anterior</a> - 
                <a href="#" onclick="calendario('<?php echo $mes_siguiente; ?>', '<?php echo $anio_siguiente; ?>');" class="siguiente">Mes Siguiente</a> &raquo;</p>
        </div>
        <?php
        break;
		
    case "3"://Guarda la disponibilidad del profesional
        //variables
        $opCombo = $utilidades->str_decode($_POST["opCombo"]);
        $lugar_disp = $utilidades->str_decode($_POST["lugar_disp"]);
		$anio = trim($utilidades->str_decode($_POST["anio"]));
        $mes = trim($utilidades->str_decode($_POST["mes"]));
        $dia = trim($utilidades->str_decode($_POST["dia"]));
        $hdd_id_usuario = $utilidades->str_decode($_POST["hdd_id_usuario"]);
        $id_usuario_crea = $_SESSION["idUsuario"];
        $fecha_calendario = $anio."-".$mes."-".$dia;
		
        if (isset($opCombo)) {//Si el valor del combo box cmbDisponibilidad != ""
            if ($opCombo == 13) {//Si la opcion del combo box cmbDisponibilidad = no_disponible  
                $rta2 = $dbDisponibilidadProf->crearDisponibilidadProf2($hdd_id_usuario, $fecha_calendario, $opCombo, $lugar_disp, array(), $id_usuario_crea);
            } else if ($opCombo == 11) {//Si la opcion del combo box cmbDisponibilidad = completa   
                //Guarda el registro
                $rta2 = $dbDisponibilidadProf->crearDisponibilidadProf2($hdd_id_usuario, $fecha_calendario, $opCombo, $lugar_disp, array(), $id_usuario_crea);
            } else if ($opCombo == 12) {//Si la opcion del combo box cmbDisponibilidad = parcial
                $rta2 = 1;
            }
            ?>
            <input type="hidden" name="rta_crearDisponibilidadProf" id="rta_crearDisponibilidadProf" value="<?php echo($rta2); ?>" />
            <?php
        }
        break;
		
    case "4"://Crea el div configurar disponibilidad especialistas
        $id_usuario_prof = $utilidades->str_decode($_POST["id_usuario_prof"]);
        $id_disponibilidad = $utilidades->str_decode($_POST["id_disponibilidad"]);
        $anio = $utilidades->str_decode($_POST["anio"]);
        $mes = $utilidades->str_decode($_POST["mes"]);
        $dia = $utilidades->str_decode($_POST["dia"]);
        $profesional = $utilidades->str_decode($_POST["profesional"]);
        $name_mes = $utilidades->str_decode($_POST["name_mes"]);
		$id_lugar_disp = $utilidades->str_decode($_POST["id_lugar_disp"]);
		
        //Obtiene el dia para saber si es sabado
        $fecha = $anio."-".$mes."-".$dia;
        $diasemana = date("w", strtotime($fecha));

        $disabled = "";
        $bol_guardar = true;
        $texto_boton = "Cancelar";

        $fecha_actual_aux = $dbVariables->getFechaactual();
		
        if (($anio."-".($mes <= 9 ? "0".$mes : $mes)."-".($dia <= 9 ? "0".$dia : $dia)) < $fecha_actual_aux["fecha_actual"]) {
            $disabled = 'disabled="disabled"';
            $bol_guardar = false;
            $texto_boton = "Regresar";
        }
		
        $lista_disp_prof_det = $dbDisponibilidadProf->getDisponibilidadProfDet($id_disponibilidad);
		$cant_disp_prof_det = count($lista_disp_prof_det);
		if ($cant_disp_prof_det == 0) {
			$cant_disp_prof_det++;
		}
		
        $aux_pasaporte = NULL;
        if (count($lista_disp_prof_det) >= 1) {
            $aux_pasaporte = TRUE;
        }
		
		//Listado de lugares
		$lista_lugares_disp = $dbListas->getListaDetalles(12);
        ?>
        <div id="d_datagrid" style="width: 925px; margin: auto; text-align: center;">
            <h4><?php echo $profesional; ?></h4>
            <h5>Disponibilidad parcial para el d&iacute;a <?php echo($dia." de ".$name_mes); ?></h5>
            <input type="hidden" id="hdd_id_usuario" value="<?php echo($id_usuario_prof); ?>" />
            <input type="hidden" id="hdd_fecha_cal" value="<?php echo(trim($anio)."-".trim($mes)."-".trim($dia)); ?>" />
            <input type="hidden" id="hdd_id_lugar_disp" value="<?php echo($id_lugar_disp); ?>" />
	        <input type="hidden" id="hdd_mes_sel" value="<?php echo($utilidades->str_decode($_POST["mes"])); ?>" />
    	    <input type="hidden" id="hdd_ano_sel" value="<?php echo($utilidades->str_decode($_POST["anio"])); ?>" />
            <input type="hidden" id="hdd_cant_disp_prof_det" value="<?php echo($cant_disp_prof_det); ?>" />
            <div id="d_cmb">
                <table align="center">
                    <tr>
                        <td><h6>Hora inicial</h6></td>
                        <td><h6>Hora Final</h6></td>
                        <td><h6>Lugar</h6></td>
                        <td><h6>Restricciones</h6></td>
                    </tr>
                    <?php
						$horas_aux = "";
						if ($diasemana == 6) {
							$horas_aux = $dbVariables->getVariables(3);
						} else {
							$horas_aux = $dbVariables->getVariables(2);
						}
						$arr_horas_disp = explode(";", $horas_aux[0]["valor_variable"]);
						
						for ($i = 0; $i < 20; $i++) {
							$bol_sel_disp = false;
							$display_aux = "table-row";
							if ($i >= $cant_disp_prof_det) {
								$display_aux = "none";
							}
							
							$id_disponibilidad_det = "0";
							if (isset($lista_disp_prof_det[$i])) {
								$id_disponibilidad_det = $lista_disp_prof_det[$i]["id_disponibilidad_det"];
							}
					?>
                    <tr id="tr_disp_<?php echo($i); ?>" style="display:<?php echo($display_aux); ?>;">
                        <td>
                            <select class="select" id="cmb_hinicio_<?php echo($i); ?>" <?php echo($disabled); ?>>
                                <option value="-1">--Seleccione--</option>
                                <?php
									foreach ($arr_horas_disp as $j => $hora_disp_aux) {
										if ($j % 2 == 0) {
											//Horas iniciales de bloque
											$hora_aux = $arr_horas_disp[$j];
											while ($hora_aux < $arr_horas_disp[$j + 1]) {
												$arr_aux = explode(":", $hora_aux);
												
												//Se construye la hora am/pm
												$hora_impr_aux = "";
												if ($arr_aux[0] <= "11") { //11 en el reloj
													$hora_impr_aux = $hora_aux." A.M.";
												} else {
													if ($arr_aux[0] > "12") {
														$hora_impr_aux = "0".(intval($arr_aux[0]) - 12);
														$hora_impr_aux = substr($hora_impr_aux, strlen($hora_impr_aux) - 2);
													} else {
														$hora_impr_aux = $arr_aux[0];
													}
													$hora_impr_aux .= ":".$arr_aux[1]." P.M.";
												}
												
												//Muestra el valor del registro en la base de datos si se ha creado
												$selected_aux = "";
												if ($aux_pasaporte) {
													$aux_hora_ini2 = explode(":", $arr_horas_disp[$j]); //Rango de horas almacenado en variables de base de datos.
													$aux_hora_final2 = explode(":", $arr_horas_disp[$j + 1]);
													
													if (isset($lista_disp_prof_det[$i])) {
														$hora_final_t = explode(":", $lista_disp_prof_det[$i]["hora_ini_t"]);
														
														if ($hora_final_t[0] >= $aux_hora_ini2[0] && $hora_final_t[0] <= $aux_hora_final2[0]) {
															if ($lista_disp_prof_det[$i]["hora_ini_t"] == $hora_aux) {
																$selected_aux = " selected";
																$bol_sel_disp = true;
															}
														}
													}
												}
                                ?>
                                <option value="<?php echo($hora_aux); ?>" <?php echo($selected_aux); ?> ><?php echo($hora_impr_aux); ?></option>
                                <?php
												$arr_aux[1] += 5;
												if ($arr_aux[1] >= 60) {
													$arr_aux[1] = "00";
													$arr_aux[0] = "0".(intval($arr_aux[0], 10) + 1);
													$arr_aux[0] = substr($arr_aux[0], strlen($arr_aux[0]) - 2);
												} else if ($arr_aux[1] < 10) {
													$arr_aux[1] = "0".$arr_aux[1];
												}
												
												$hora_aux = $arr_aux[0].":".$arr_aux[1];
											}
										}
									}
                                ?>
                            </select>
                        </td>
                        <td>
                            <select class="select" id="cmb_hfinal_<?php echo($i); ?>" <?php echo($disabled); ?>>
                                <option value="-1" >--Seleccione--</option>
                                <?php
									foreach ($arr_horas_disp as $j => $hora_disp_aux) {
										if ($j % 2 == 0) {
											$hora_aux = $arr_horas_disp[$j];
											while ($hora_aux < $arr_horas_disp[$j + 1]) {
												$arr_aux = explode(":", $hora_aux);
												
												$arr_aux[1] += 5;
												if ($arr_aux[1] >= 60) {
													$arr_aux[1] = "00";
													$arr_aux[0] = "0".(intval($arr_aux[0], 10) + 1);
													$arr_aux[0] = substr($arr_aux[0], strlen($arr_aux[0]) - 2);
												} else if ($arr_aux[1] < 10) {
													$arr_aux[1] = "0".$arr_aux[1];
												}
												
												$hora_aux = $arr_aux[0].":".$arr_aux[1];
												
												//Se construye la hora am/pm
												$hora_impr_aux = "";
												if ($arr_aux[0] <= "11") {
													$hora_impr_aux = $hora_aux." A.M.";
												} else {
													if ($arr_aux[0] > "12") {
														$hora_impr_aux = "0".(intval($arr_aux[0]) - 12);
														$hora_impr_aux = substr($hora_impr_aux, strlen($hora_impr_aux) - 2);
													} else {
														$hora_impr_aux = $arr_aux[0];
													}
													$hora_impr_aux .= ":".$arr_aux[1]." P.M.";
												}
												
												//Muestra el valor del registro en la base de datos si se ha creado
												$selected_aux = "";
												if ($aux_pasaporte) {
													$aux_hora_ini2 = explode(":", $arr_horas_disp[$j]); //Rango de horas almacenado en variables de base de datos.
													$aux_hora_final2 = explode(":", $arr_horas_disp[$j + 1]);
													
													if (isset($lista_disp_prof_det[$i])) {
														$hora_final_t = explode(":", $lista_disp_prof_det[$i]["hora_final_t"]);
														if ($hora_final_t[0] >= $aux_hora_ini2[0] && $hora_final_t[0] <= $aux_hora_final2[0]) {
															if ($lista_disp_prof_det[$i]["hora_final_t"] == $hora_aux) {
																$selected_aux = " selected";
															}
														}
													}
												}
                                ?>
                                <option value="<?php echo($hora_aux); ?>" <?php echo($selected_aux); ?> ><?php echo($hora_impr_aux); ?></option>
                                <?php
											}
										}
									}
								?>   
                            </select>
                        </td>
                        <td>
                        	<select id="cmb_lugar_disp_parc_<?php echo($i); ?>" <?php echo($disabled); ?>>
                            	<option value="-1">--Seleccione--</option>
                                <?php
                                	if (count($lista_lugares_disp) > 0) {
										foreach ($lista_lugares_disp as $lugar_aux) {
											$selected_aux = "";
											if ($bol_sel_disp && isset($lista_disp_prof_det[$i]) && $lista_disp_prof_det[$i]["id_lugar_disp"] == $lugar_aux["id_detalle"]) {
												$selected_aux = " selected";
											}
								?>
                                <option value="<?php echo($lugar_aux["id_detalle"]); ?>"<?php echo($selected_aux); ?>><?php echo($lugar_aux["nombre_detalle"]); ?></option>
                                <?php
										}
									}
								?>
                            </select>
                        </td>
                        <td align="center" valign="top">
                        	<?php
                            	$cadena_convenios = "";
                            	$cadena_perfiles = "";
                            	$cadena_tipos_citas = "";
								if ($id_disponibilidad_det != "0") {
									$lista_disponibilidades_det_convenios = $dbDisponibilidadProf->getListaDisponibilidadProfConvenios($id_disponibilidad_det);
									if (count($lista_disponibilidades_det_convenios) > 0) {
										foreach ($lista_disponibilidades_det_convenios as $disp_conv_aux) {
											if ($cadena_convenios != "") {
												$cadena_convenios .= ",";
											}
											$cadena_convenios .= $disp_conv_aux["id_convenio"];
										}
									}
									
									$lista_disponibilidades_det_perfiles = $dbDisponibilidadProf->getListaDisponibilidadProfPerfiles($id_disponibilidad_det);
									if (count($lista_disponibilidades_det_perfiles) > 0) {
										foreach ($lista_disponibilidades_det_perfiles as $disp_perf_aux) {
											if ($cadena_perfiles != "") {
												$cadena_perfiles .= ",";
											}
											$cadena_perfiles .= $disp_perf_aux["id_perfil"];
										}
									}
									
									$lista_disponibilidades_det_tipos_citas = $dbDisponibilidadProf->getListaDisponibilidadProfTiposCitas($id_disponibilidad_det);
									if (count($lista_disponibilidades_det_tipos_citas) > 0) {
										foreach ($lista_disponibilidades_det_tipos_citas as $disp_tc_aux) {
											if ($cadena_tipos_citas != "") {
												$cadena_tipos_citas .= ",";
											}
											$cadena_tipos_citas .= $disp_tc_aux["id_tipo_cita"];
										}
									}
								}
							?>
                            <input type="hidden" id="hdd_cadena_convenios_<?php echo($i); ?>" value="<?php echo($cadena_convenios); ?>" />
                            <input type="hidden" id="hdd_cadena_perfiles_<?php echo($i); ?>" value="<?php echo($cadena_perfiles); ?>" />
                            <input type="hidden" id="hdd_cadena_tipos_citas_<?php echo($i); ?>" value="<?php echo($cadena_tipos_citas); ?>" />
                        	<img id="img_filtros_<?php echo($i); ?>" src="../imagenes/Add-icon.png" class="img_button" title="Ver restricciones" onclick="ver_restricciones(<?php echo($i); ?>, <?php echo($id_disponibilidad_det); ?>);" />
                        </td>
                    </tr>
                    <?php
						}
					?>
			    	<tr>
			        	<td colspan="3">
			            	<div class="agregar_alemetos" onclick="agregar_disponibilidad_parcial();" title="Agregar disponibilidad"></div>
			                <div class="restar_alemetos" onclick="restar_disponibilidad_parcial();" title="Borrar disponibilidad"></div>
			            </td>
			        </tr>
                </table>
                <?php
                	if ($bol_guardar && $tipo_acceso_menu == 2) {
				?>
            	<input type="button" class="btnPrincipal" onclick="guardar_disponibilidad_prof_det()" value="Guardar" />
                &nbsp;&nbsp;
            	<?php
                	}
            	?>
            	<input type="button" class="btnPrincipal" onclick="ver_disponibilidad($('#hdd_id_usuario').val(), $('h4').text(), '<?php echo($utilidades->str_decode($_POST["mes"])); ?>', '<?php echo($utilidades->str_decode($_POST["anio"])); ?>');" value="<?php echo($texto_boton); ?>" />
            </div>
        </div>
        <?php
        break;

    case "5"://Guarda disponibilidad_prof_det
		$id_usuario_crea = $_SESSION["idUsuario"];
		$id_usuario_prof = $utilidades->str_decode($_POST["id_usuario_prof"]);
		$fecha_cal = $utilidades->str_decode($_POST["fecha_cal"]);
		$cant_disp_prof_det = intval($_POST["cant_disp_prof_det"], 10);
		$lista_disp_prof_det = array();
		for ($i = 0; $i < $cant_disp_prof_det; $i++) {
			@$cadena_convenios_aux = $utilidades->str_decode($_POST["cadena_convenios_".$i]);
			if ($cadena_convenios_aux != "") {
				$arr_convenios = explode(",", $cadena_convenios_aux);
			} else {
				$arr_convenios = array();
			}
			@$cadena_perfiles_aux = $utilidades->str_decode($_POST["cadena_perfiles_".$i]);
			if ($cadena_perfiles_aux != "") {
				$arr_perfiles = explode(",", $cadena_perfiles_aux);
			} else {
				$arr_perfiles = array();
			}
			@$cadena_tipos_citas_aux = $utilidades->str_decode($_POST["cadena_tipos_citas_".$i]);
			if ($cadena_tipos_citas_aux != "") {
				$arr_tipos_citas = explode(",", $cadena_tipos_citas_aux);
			} else {
				$arr_tipos_citas = array();
			}
			
			@$lista_disp_prof_det[$i]["hora_ini"] = $utilidades->str_decode($_POST["hora_ini_".$i]);
			@$lista_disp_prof_det[$i]["hora_fin"] = $utilidades->str_decode($_POST["hora_fin_".$i]);
			@$lista_disp_prof_det[$i]["id_lugar_disp"] = $utilidades->str_decode($_POST["id_lugar_disp_".$i]);
			@$lista_disp_prof_det[$i]["arr_convenios"] = $arr_convenios;
			@$lista_disp_prof_det[$i]["arr_perfiles"] = $arr_perfiles;
			@$lista_disp_prof_det[$i]["arr_tipos_citas"] = $arr_tipos_citas;
		}
		
		$id_lugar_disp = $lista_disp_prof_det[0]["id_lugar_disp"];
		
		//Guarda el registro
		$resultado = $dbDisponibilidadProf->crearDisponibilidadProf2($id_usuario_prof, $fecha_cal, 12, $id_lugar_disp, $lista_disp_prof_det, $id_usuario_crea);
	?>
	<input type="hidden" name="hdd_pa_rta" id="hdd_pa_rta" value="<?php echo($resultado); ?>" />
	<input type="hidden" name="hdd_fecha_cal" id="hdd_fecha_cal" value="<?php echo($fecha_cal); ?>" />
	<?php
		break;
		
    case "6":
        ?>
        <table class="paginated modal_table" style="width: 60%; margin: auto;">
            <thead>
                <tr>
                    <th scope="col" style="width:60%;">Usuario</th>
                    <th scope="col" style="width:40%;">Perfil</th>  
                </tr>
            </thead>
            <tbody>
                <?php
                //recibe el resultado
                $rta = $dbTiemposCitasProf->getCitasdisponibilidad();
                $usuarios = array();
                $perfiles = array();
                $idusuarios = array();

                //Concatena los perfiles
                foreach ($rta as $value) {
                    if (!isset($usuarios[$value["id_usuario"]])) {
                        $usuarios[$value["id_usuario"]] = $value["nombre_usuario"] . " " . $value["apellido_usuario"];
                        $idusuarios[$value["id_usuario"]] = $value["id_usuario"];
                    }
                    if (!isset($perfiles[$value["id_usuario"]])) {
                        $perfiles[$value["id_usuario"]] = $value["nombre_perfil"];
                    } else {
                        $perfiles[$value["id_usuario"]] .= ", " . $value["nombre_perfil"];
                    }
                }

                //Crea el datagrid
                foreach ($usuarios as $key => $value) {
                    ?>
                    <tr id="<?php echo $idusuarios[$key]; ?>" onclick="ver_disponibilidad(<?php echo $idusuarios[$key]; ?>, '<?php echo($value); ?>')">
                        <td><?php echo $value; ?></td>
                        <td><?php echo $perfiles[$key]; ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <script id="ajax" type="text/javascript">
                    //<![CDATA[ 
                    var numTotalItems = <?php echo(count($usuarios)); ?>;
                    var numPerPage = 10;
                    $(function() {
                        if (numTotalItems > numPerPage) {
                            $(".paginated", "table").each(function(i) {
                                $(this).text(i + 1);
                            });

                            $("table.paginated").each(function() {
                                var currentPage = 0;
                                var $table = $(this);
                                $table.bind("repaginate", function() {
                                    $table.find("tbody tr").hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
                                });
                                $table.trigger("repaginate");
                                var numRows = $table.find("tbody tr").length;
                                var numPages = Math.ceil(numRows / numPerPage);
                                var $pager = $('<div class="pager"></div>');
                                for (var page = 0; page < numPages; page++) {
                                    $('<span class="page-number"></span>').text(page + 1).bind("click", {
                                        newPage: page
                                    }, function(event) {
                                        currentPage = event.data["newPage"];
                                        $table.trigger("repaginate");
                                        $(this).addClass("active").siblings().removeClass("active");
                                    }).appendTo($pager).addClass("clickable");
                                }
                                $pager.insertBefore($table).find("span.page-number:first").addClass("active");
                            });
                        }
                    });
                    //]]>
        </script>
        <?php
        break;
		
	case "7": //Aplicar disponibilidad a todo el mes
		$id_usuario = $_SESSION["idUsuario"];
		@$id_usuario_prof = $utilidades->str_decode($_POST["id_usuario_prof"]);
		@$id_tipo_disponibilidad = $utilidades->str_decode($_POST["id_tipo_disponibilidad"]);
		@$id_lugar_disp = $utilidades->str_decode($_POST["id_lugar_disp"]);
		@$mes = trim($utilidades->str_decode($_POST["mes"]));
		@$ano = $utilidades->str_decode($_POST["ano"]);
		
		$fecha_actual = $dbVariables->getFechaactual();
		$fecha_actual = explode("-", $fecha_actual["fecha_actual"]);
		for ($i = 0; $i < count($fecha_actual); $i++) {
			$fecha_actual[$i] = intval($fecha_actual[$i], 10);
		}
		
		//Si el mes es vacío se obtiene el mes actual
		if ($mes == "") {
			$mes = $fecha_actual[1];
			$ano = $fecha_actual[0];
		} else {
			$mes = intval($mes, 10);
			$ano = intval($ano, 10);
		}
		
		//Se obtiene el número de días del mes
		$num_dias = obtener_dias_mes($mes, $ano);
		
		$resultado_aux = 0;
		if ($fecha_actual[0] < $ano || ($fecha_actual[0] == $ano && $fecha_actual[1] <= $mes)) {
			$dia_inicial = 1;
			if ($fecha_actual[0] == $ano && $fecha_actual[1] == $mes) {
				$dia_inicial = $fecha_actual[2];
			}
			for ($i = $dia_inicial; $i <= $num_dias; $i++) {
				$resultado_aux = $dbDisponibilidadProf->crearDisponibilidadProf2($id_usuario_prof, $ano."-".$mes."-".$i, $id_tipo_disponibilidad, $id_lugar_disp, array(), $id_usuario);
				if ($resultado_aux <= 0) {
					break;
				}
			}
			//Se hace el llamado al procedimiento que llena los días de disponibilidad no creados
			$dbCalendario->crear_mes_calendario($ano,$mes,$id_usuario);
		}
	?>
    <input type="hidden" name="hdd_resultado_mes" id="hdd_resultado_mes" value="<?php echo($resultado_aux); ?>" />
    <?php
		break;
		
	case "8": //Aplicar disponibilidad de la última semana
		$id_usuario = $_SESSION["idUsuario"];
		@$id_usuario_prof = $utilidades->str_decode($_POST["id_usuario_prof"]);
		@$mes = trim($utilidades->str_decode($_POST["mes"]));
		@$ano = $utilidades->str_decode($_POST["ano"]);
		
		$fecha_actual = $dbVariables->getFechaactual();
		$fecha_actual = explode("-", $fecha_actual["fecha_actual"]);
		for ($i = 0; $i < count($fecha_actual); $i++) {
			$fecha_actual[$i] = intval($fecha_actual[$i], 10);
		}
		
		//Si el mes es vacío se obtiene el mes actual
		if ($mes == "") {
			$mes = $fecha_actual[1];
			$ano = $fecha_actual[0];
		} else {
			$mes = intval($mes, 10);
			$ano = intval($ano, 10);
		}
		
		//Se obtiene el número de días del mes
		$num_dias = obtener_dias_mes($mes, $ano);
		
		$resultado_aux = 0;
		if ($fecha_actual[0] < $ano || ($fecha_actual[0] == $ano && $fecha_actual[1] <= $mes)) {
			$resultado_aux = $dbDisponibilidadProf->duplicar_semana_disponibilidad_prof($id_usuario_prof, $mes, $ano, $id_usuario);
		}
	?>
    <input type="hidden" name="hdd_resultado_semana" id="hdd_resultado_semana" value="<?php echo($resultado_aux); ?>" />
    <?php
		break;
		
	case "9": //Restricciones
		@$indice = $utilidades->str_decode($_POST["indice"]);
		@$id_usuario_prof = $utilidades->str_decode($_POST["id_usuario_prof"]);
		@$id_disponibilidad_det = $utilidades->str_decode($_POST["id_disponibilidad_det"]);
		@$cadena_convenios = $utilidades->str_decode($_POST["cadena_convenios"]);
		@$cadena_perfiles = $utilidades->str_decode($_POST["cadena_perfiles"]);
		@$cadena_tipos_citas = $utilidades->str_decode($_POST["cadena_tipos_citas"]);
		
		//Se obtienen los listados de valores
		$lista_convenios = $dbConvenios->getListaConveniosActivos();
		$lista_perfiles = $dbPerfiles->getListaPerfilesMenu(9, 2);
		$lista_tipos_citas = $dbTiemposCitasProf->getTiemposcitasprofeAdmision($id_usuario_prof);
		
		//Se obtienen las cadenas de restricciones
		if ($cadena_convenios != "") {
			$arr_convenios = explode(",", $cadena_convenios);
		} else {
			$arr_convenios = array();
		}
		if ($cadena_perfiles != "") {
			$arr_perfiles = explode(",", $cadena_perfiles);
		} else {
			$arr_perfiles = array();
		}
		if ($cadena_tipos_citas != "") {
			$arr_tipos_citas = explode(",", $cadena_tipos_citas);
		} else {
			$arr_tipos_citas = array();
		}
	?>
    <div class="encabezado">
        <h3>Restricciones</h3>
    </div>
    <fieldset style="width:95%; margin:auto; padding:1.25rem;">
        <div style="max-height:180px; overflow:auto;">
            <?php
				$ind_todos = !(count($arr_convenios) > 0);
			?>
            <input type="hidden" id="hdd_cant_convenio_filtros" value="<?php echo(count($lista_convenios)); ?>" />
            <table border="0" style="width:100%;">
                <tr>
                    <td align="left" style="width:25%;">
                        <h6 class="no-margin"><b>Convenios:</b></h6>
                    </td>
                    <td align="left" style="width:25%;">
                        <input type="checkbox" id="chk_convenio_todos" class="no-margin" <?php if ($ind_todos) { ?>checked="checked"<?php } ?> onchange="seleccionar_fitro_disponibilidad('convenio', 'todos');" />
                        <b>TODOS</b>
                    </td>
                    <td align="left" style="width:25%;"></td>
                    <td align="left" style="width:25%;"></td>
                </tr>
                <tr style="height:10px;"></tr>
                <?php
					$i = 0;
					foreach ($lista_convenios as $convenio_aux) {
						if ($i % 4 == 0) {
				?>
                <tr>
                    <?php
						}
					?>
                    <td align="left">
                    	<?php
							$checked_aux = "";
							if ($ind_todos || in_array($convenio_aux["id_convenio"], $arr_convenios)) {
	                        	$checked_aux = "checked=\"checked\"";
							}
						?>
                        <input type="hidden" id="hdd_convenio_<?php echo($i); ?>" value="<?php echo($convenio_aux["id_convenio"]); ?>" />
                        <input type="checkbox" id="chk_convenio_<?php echo($i) ?>" class="no-margin" <?php echo($checked_aux); ?> onchange="seleccionar_fitro_disponibilidad('convenio', <?php echo($i) ?>);" />
                        <?php echo($convenio_aux["nombre_convenio"]); ?>
                    </td>
                    <?php
						if ($i % 4 == 3 || $i == count($lista_convenios) - 1) {
					?>
                </tr>
                <?php
						}
						
						$i++;
					}
				?>
            </table>
        </div>
        <br />
        <div style="max-height:180px; overflow:auto;">
            <?php
				$ind_todos = !(count($arr_perfiles) > 0);
			?>
            <input type="hidden" id="hdd_cant_perfil_filtros" value="<?php echo(count($lista_perfiles)); ?>" />
            <table border="0" style="width:100%;">
                <tr>
                    <td align="left" style="width:25%;">
                        <h6 class="no-margin"><b>Perfiles:</b></h6>
                    </td>
                    <td align="left" style="width:25%;">
                        <input type="checkbox" id="chk_perfil_todos" class="no-margin" <?php if ($ind_todos) { ?>checked="checked"<?php } ?> onchange="seleccionar_fitro_disponibilidad('perfil', 'todos');" />
                        <b>TODOS</b>
                    </td>
                    <td align="left" style="width:25%;"></td>
                    <td align="left" style="width:25%;"></td>
                </tr>
                <tr style="height:10px;"></tr>
                <?php
					$i = 0;
					foreach ($lista_perfiles as $perfil_aux) {
						if ($i % 4 == 0) {
				?>
                <tr>
                    <?php
						}
					?>
                    <td align="left">
                    	<?php
							$checked_aux = "";
							if ($ind_todos || in_array($perfil_aux["id_perfil"], $arr_perfiles)) {
	                        	$checked_aux = "checked=\"checked\"";
							}
						?>
                        <input type="hidden" id="hdd_perfil_<?php echo($i); ?>" value="<?php echo($perfil_aux["id_perfil"]); ?>" />
                        <input type="checkbox" id="chk_perfil_<?php echo($i) ?>" class="no-margin" <?php echo($checked_aux); ?> onchange="seleccionar_fitro_disponibilidad('perfil', <?php echo($i) ?>);" />
                        <?php echo($perfil_aux["nombre_perfil"]); ?>
                    </td>
                    <?php
						if ($i % 4 == 3 || $i == count($lista_perfiles) - 1) {
					?>
                </tr>
                <?php
						}
						
						$i++;
					}
				?>
            </table>
        </div>
        <br />
        <div style="max-height:180px; overflow:auto;">
            <?php
				$ind_todos = !(count($arr_tipos_citas) > 0);
			?>
            <input type="hidden" id="hdd_cant_tipo_cita_filtros" value="<?php echo(count($lista_tipos_citas)); ?>" />
            <table border="0" style="width:100%;">
                <tr>
                    <td align="left" style="width:25%;">
                        <h6 class="no-margin"><b>Tipos de citas:</b></h6>
                    </td>
                    <td align="left" style="width:25%;">
                        <input type="checkbox" id="chk_tipo_cita_todos" class="no-margin" <?php if ($ind_todos) { ?>checked="checked"<?php } ?> onchange="seleccionar_fitro_disponibilidad('tipo_cita', 'todos');" />
                        <b>TODOS</b>
                    </td>
                    <td align="left" style="width:25%;"></td>
                    <td align="left" style="width:25%;"></td>
                </tr>
                <tr style="height:10px;"></tr>
                <?php
					$i = 0;
					foreach ($lista_tipos_citas as $tipo_cita_aux) {
						if ($i % 4 == 0) {
				?>
                <tr>
                    <?php
						}
					?>
                    <td align="left">
                    	<?php
							$checked_aux = "";
							if ($ind_todos || in_array($tipo_cita_aux["id_tipo_cita"], $arr_tipos_citas)) {
	                        	$checked_aux = "checked=\"checked\"";
							}
						?>
                        <input type="hidden" id="hdd_tipo_cita_<?php echo($i); ?>" value="<?php echo($tipo_cita_aux["id_tipo_cita"]); ?>" />
                        <input type="checkbox" id="chk_tipo_cita_<?php echo($i) ?>" class="no-margin" <?php echo($checked_aux); ?> onchange="seleccionar_fitro_disponibilidad('tipo_cita', <?php echo($i) ?>);" />
                        <?php echo($tipo_cita_aux["nombre_tipo_cita"]); ?>
                    </td>
                    <?php
						if ($i % 4 == 3 || $i == count($lista_tipos_citas) - 1) {
					?>
                </tr>
                <?php
						}
						
						$i++;
					}
				?>
            </table>
        </div>
        <br />
    	<input type="button" value="Aceptar" class="btnPrincipal" onclick="aplicar_restricciones_disp(<?php echo($indice); ?>);" />
        &nbsp;&nbsp;
    	<input type="button" value="Cancelar" class="btnPrincipal" onclick="cerrar_div_centro();" />
    </fieldset>
    <?php
		break;
}
?>
