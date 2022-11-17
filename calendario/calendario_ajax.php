<?php
/*
  Página que permite los procedimiento ajax
  Autor: Juan Pablo Gomez Quiroga - 01/10/2013
 */

header('Content-Type: text/xml; charset=UTF-8');

session_start();

require_once("../principal/ContenidoHtml.php");
require_once '../funciones/FuncionesPersona.php';
require_once("../db/DbCalendario.php");
require_once("../db/DbVariables.php");
require_once("../db/DbListas.php");

$contenido = new ContenidoHtml();
$contenido->validar_seguridad(1);
$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);

$funciones_persona = new FuncionesPersona();
$calendario = new DbCalendario();
$variables = new Dbvariables();

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
    case "1"://Crea el calendario de disponibilidad laboral
		$id_usuario = $_SESSION["idUsuario"];
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
		
		//Se hace el llamado al procedimiento que llena los días de disponibilidad no creados
		$calendario->crear_mes_calendario($fecha_calendario[0], $fecha_calendario[1], $id_usuario);
		
        //Se obtiene la disponibilidad del calendario
        $calendario_disponible = $calendario->getCalendario($fecha_calendario[1], $fecha_calendario[0]);
		
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
							
							//Se obtiene el indicador de laboral
							$ind_laboral = 0;
							if ($estado == "disponible") {
					            $ind_laboral = 1;
							}
							
	    	                //Agrega el combo_box dependiendo de la clase.
    	    	            $combo_box = '';
        	    	        $estilo = $estado;
            	    	    $id_disponibilidad = -1;
                	    	$disponibilidad_sel = -1;
							
							$bol_disponible = false;
							if ($fecha_completa > $fecha_actual_aux['fecha_actual'] && $tipo_acceso_menu == 2) { //Si fecha es menor o igual a la fecha actual
								$bol_disponible = true;
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
				<select name="cmb_laboral_<?php echo($dia); ?>" id="cmb_laboral_<?php echo($dia); ?>" class="no_padding" style="width:120px;" onchange="modificar_calendario('<?php echo($fecha_completa); ?>', this.value);" <?php if (!$bol_disponible) { ?>disabled="disabled"<?php } ?>>
					<option value="1"<?php if ($ind_laboral == 1) { ?> selected="selected"<?php } ?>>Laboral</option>
                    <option value="0"<?php if ($ind_laboral == 0) { ?> selected="selected"<?php } ?>>No laboral</option>
				</select>
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
		
	case "2": //Modificar calendario
	    $id_usuario = $_SESSION["idUsuario"];
	    $fecha = $_POST["fecha"];
		$ind_laboral = $_POST["ind_laboral"];
		
		//Se mueven las citas
		$resultado = $calendario->modificar_calendario($fecha, $ind_laboral, $id_usuario);
	?>
	<div class='contenedor_error' id='contenedor_error'></div>
	<div class='contenedor_exito' id='contenedor_exito'></div>
    <input type="hidden" name="hdd_modificar_calendario" id="hdd_modificar_calendario" value="<?php echo($resultado); ?>" />
    <?php
		break;
}
?>
