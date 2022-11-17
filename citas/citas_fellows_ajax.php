<?php
/*
  Página que permite los procedimiento ajax
  Autor: Juan Pablo Gomez Quiroga - 01/10/2013
 */

header('Content-Type: text/xml; charset=UTF-8');

session_start();

require_once '../db/DbUsuarios.php';
require_once("../principal/ContenidoHtml.php");
require_once '../funciones/FuncionesPersona.php';
require_once("../db/DbVariables.php");
require_once("../db/DbCitas.php");
require_once '../db/DbUsuariosPerfiles.php';
require_once '../db/DbAsignarCitas.php';
require_once '../db/DbUsuarios.php';
require_once("../db/DbDisponibilidadProf.php");
require_once("../db/DbCalendario.php");
require_once("../db/DbListas.php");

$contenido = new ContenidoHtml();
$contenido->validar_seguridad(1);
$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);

$funciones_persona = new FuncionesPersona();
$variables = new Dbvariables();
$citas = new DbCitas();
$usuarios_perfiles = new DbUsuariosPerfiles();
$asignar_citas = new DbAsignarCitas();
$usuarios = new DbUsuarios();
$disponibilidad_prof = new DbDisponibilidadProf();
$calendario = new DbCalendario();

$opcion = $_POST["opcion"];

//Funcion calendario
function buscar_calendario($fecha, $calendario) {
    $resultado = 'sin_atencion';
    foreach ($calendario as $fila_calendario) {
        $fecha_calendario = $fila_calendario['fecha_cal'];
        $indicador = $fila_calendario['ind_laboral'];

        if ($indicador == 0) {
            $indicador = 'sin_atencion';
        } else if ($indicador == 1) {
            $indicador = 'disponible';
        }
        if ($fecha == $fecha_calendario) {
            $resultado = $indicador;
        }
    }
    return $resultado;
}

switch ($opcion) {
    case "1"://Crea el calendario de citas del fellow
        $fecha_actual_aux = $variables->getFechaactual();
        
        $db_listas = new DbListas();
        $lista_disponibilidades = $db_listas->getListaDetalles(3);
		
        $fecha_calendario = array();
        if ($_POST["mes"] == "" || $_POST["anio"] == "") {
            $mes_hoy = $calendario->getMesActual();
            $anio_hoy = $calendario->getAnioActual();

            $fecha_calendario[1] = intval($mes_hoy['mes']);
            if ($fecha_calendario[1] < 10) {
                $fecha_calendario[1] = "0".$fecha_calendario[1];
			}
            $fecha_calendario[0] = $anio_hoy['anio'];
        } else {
            $fecha_calendario[1] = intval($_POST["mes"]);
            if ($fecha_calendario[1] < 10) {
                $fecha_calendario[1] = "0".$fecha_calendario[1];
			} else {
                $fecha_calendario[1] = $fecha_calendario[1];
			}
            $fecha_calendario[0] = $_POST["anio"];
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
        $calendario_disponible = $calendario->getCalendario($fecha_calendario[1], $fecha_calendario[0]);
		
        //recibe el id del usuario del hidden hdd_id_usuario
        $id_fellow_g = $_POST["id_fellow_g"];
		
        //Sentencia que obtiene el registro de la tabla disponibilidad_prof donde el usuario sea = $id_fellow_g
		$lista_citas_mes = $citas->getConsolidadoCitasUsuarioMes($id_fellow_g, $fecha_calendario[0], $fecha_calendario[1]);
		
        $fechaanterior = date("Y-m-d", mktime(0, 0, 0, $fecha_calendario[1] - 1, 01, $fecha_calendario[0]));
        $fechasiguiente = date("Y-m-d", mktime(0, 0, 0, $fecha_calendario[1] + 1, 01, $fecha_calendario[0]));
		
        $anio_siguiente = substr($fechasiguiente, 0, 4);
        $mes_siguiente = substr($fechasiguiente, 5, 2);
		
        $anio_anterior = substr($fechaanterior, 0, 4);
        $mes_anterior = substr($fechaanterior, 5, 2);
        ?>
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
		<table class='calendario' style='width: 900px;' cellspacing='0' cellpadding='0'>
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
		//Se obtiene el perfil de fellow
		$id_perfil_fellow = $variables->getVariable(5);
		
        //inicializamos filas de la tabla
        $tr = 0;
        $dia = 1;

        for ($i = 1; $i <= $diasdespues; $i++) {
            if ($tr < $totalfilas) {
                if ($i >= $primeromes && $i <= $tope) {
                    /* creamos fecha completa */
                    if ($dia < 10) {
                        $dia_actual = "0".$dia;
					} else {
                        $dia_actual = $dia;
					}
					
                    $fecha_completa = $fecha_calendario[0]."-".$fecha_calendario[1]."-".$dia_actual;
                    /* si es hoy coloreamos la celda */
                    $fecha_actual = $calendario->getFechaActual();
					
                    //Se busca la disponibilidad del la fecha
                    $estado = buscar_calendario($fecha_completa, $calendario_disponible);
					
                    //Agrega el combo_box dependiendo de la clase.
                    $combo_box = '';
                    $estilo = "sin_seleccion";
                    $id_disponibilidad = -1;
                    $disponibilidad_sel = -1;
					
					if ($estado == "disponible") {
                        $disponibilidad_sel = 0;
                        for ($a = 0; $a <= count($lista_citas_mes) - 1; $a++) {
                            if ($fecha_completa == $lista_citas_mes[$a]['fecha_cal']) {
                                $cant_citas = $lista_citas_mes[$a]['cant_citas'];
                                if ($cant_citas > 0) {//Si es igual a no disponible
                                    $estilo = "disponible";
								}
                            }
                        }
						
						$bol_disponible = false;
                        if ($fecha_completa >= $fecha_actual_aux['fecha_actual']) { //Si fecha es menor o igual a la fecha actual
							$bol_disponible = true;
						}
                    }
					
					$estilo2 = "";
					if ($fecha_completa < $fecha_actual_aux['fecha_actual']) {
						$estilo2 = " opaco";
					}
					
					if ($fecha_actual['fecha_hoy'] == $fecha_completa) {
						$estilo2 .= " hoy";
					}
                    ?>
                <td class="<?php echo($estilo.$estilo2); ?>" name="d_dia_<?php echo($dia) ?>" id="d_dia_<?php echo($dia) ?>">
                        <?php echo($dia); ?><br />
                        <?php
                        	if ($estilo == "disponible") {
						?>
	                    <select name="cmb_usuario_fellow_<?php echo($dia); ?>" id="cmb_usuario_fellow_<?php echo($dia); ?>" class="no_padding" style="width:120px;" <?php if ($bol_disponible && $tipo_acceso_menu == 2) { ?>onchange="mover_citas_fellow('<?php echo($fecha_completa); ?>', this.value);" <?php } else { ?>disabled="disabled"<?php } ?>>
    	                	<option value="-1">--Asignar--</option>
                            <?php
								$lista_usuarios = $disponibilidad_prof->getListaUsuariosDisponiblesFecha($id_perfil_fellow["valor_variable"], $fecha_completa);
								
                            	foreach($lista_usuarios as $usuario_aux) {
									if ($usuario_aux["id_usuario"] != $id_fellow_g) {
							?>
                            <option value="<?php echo($usuario_aux["id_usuario"]); ?>"><?php echo($usuario_aux["nombre_completo"]); ?></option>
                            <?php
									}
								}
							?>
        	            </select>
                        <?php
							}
						?>
                        <input type="hidden" name="hdd_id_disponibilidad_<?php echo($dia) ?>" id="hdd_id_disponibilidad_<?php echo($dia) ?>" value="<?php echo($id_disponibilidad); ?>" />
                        <?php
                        	if ($disponibilidad_sel != -1) {
                        ?>
                        <input type="hidden" name="hdd_tipo_disponibilidad_<?php echo($dia); ?>" id="hdd_tipo_disponibilidad_<?php echo($dia); ?>" value="<?php echo($disponibilidad_sel); ?>" />
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
        <p>&laquo; <a href='#' onclick="calendario('<?php echo $mes_anterior; ?>', '<?php echo $anio_anterior; ?>');" class='anterior' >Mes Anterior</a> - 
            <a href='#' onclick="calendario('<?php echo $mes_siguiente; ?>', '<?php echo $anio_siguiente; ?>');" class='siguiente'>Mes Siguiente</a> &raquo;</p>
        </div>
        <?php
        break;
		
	case "2": //Confirmación mover citas fellows
	    $fecha = $_POST["fecha"];
		$id_usuario_dest = $_POST["id_usuario"];
		$id_fellow_g = $_POST["id_fellow_g"];
		
		$campos_fecha = explode("-", $fecha);
		$fecha_texto = strtolower($funciones_persona->obtenerFecha3($campos_fecha[2], $campos_fecha[1], $campos_fecha[0]));
		//Se busca el nombre del usuario de destino
		$usuario_obj = $usuarios->getUsuario($id_usuario_dest);
		
		//Se busca el total de citas pendientes que tiene el usuario destino durante el día
		$lista_citas_aux = $asignar_citas->getCantidadCitasEstadoFecha($id_usuario_dest, 14, $fecha);
		$cant_citas = intval($lista_citas_aux["cantidad"]);
		$texto_usuario = $usuario_obj["nombre_usuario"]." ".$usuario_obj["apellido_usuario"]." (".$cant_citas." citas)";
	?>
	<div class='contenedor_error' id='contenedor_error'></div>
	<div class='contenedor_exito' id='contenedor_exito'></div>
    <div id="d_mover_citas">
		<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:98%">
			<tr>
        		<th align="center">
            		<h4>&iquest;Est&aacute; seguro de reasignar las citas del d&iacute;a<br /><?php echo($fecha_texto); ?><br />al usuario <?php echo($texto_usuario); ?>?</h4>
	            </th>
			</tr>
			<tr>
				<th align="center">
					<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" class="btnPrincipal" value="Si" onclick="cambiar_citas('<?php echo($fecha); ?>', <?php echo($id_usuario_dest); ?>, <?php echo($id_fellow_g); ?>);"/>
                    <input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" class="btnSecundario" value="No" onclick="cancelar_cambio_citas('<?php echo($campos_fecha[2]); ?>');"/>
				</th>
			</tr>
		</table>
    </div>
	<?php
		break;
		
	case "3"://Mover citas fellows
	    $id_usuario = $_SESSION["idUsuario"];
		$fecha = $_POST["fecha"];
		$id_usuario_dest = $_POST["id_usuario"];
		$id_fellow_g = $_POST["id_fellow_g"];
		
		//Se mueven las citas
		$resultado = $citas->mover_citas_pendientes($fecha, $id_usuario_dest, $id_fellow_g, $id_usuario);
	?>
    <input type="hidden" name="hdd_resultado_mover" id="hdd_resultado_mover" value="<?php echo($resultado); ?>" />
    <?php
		break;
}
?>
