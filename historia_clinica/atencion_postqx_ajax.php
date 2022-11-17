<?php
session_start();
/*
  Pagina para ver y matricular pacientes para atencion post QX de Catarata
  Autor: Helio Ruber López - 27/04/2016
 */

header('Content-Type: text/xml; charset=UTF-8');

require_once("../db/DbHistoriaClinica.php");
require_once("../db/DbPacientes.php");
require_once("../db/DbVariables.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../funciones/FuncionesPersona.php");
require_once("../funciones/Utilidades.php");
require_once("../principal/ContenidoHtml.php");
require_once("FuncionesHistoriaClinica.php");
require_once("../db/DbListas.php");
require_once("../db/DbCalendario.php");
require_once("../db/DbAtencionPostqx.php");
require_once("../funciones/Class_Generar_Clave.php");

$dbHistoriaClinica = new DbHistoriaClinica();
$dbPacientes = new DbPacientes();
$dbListas = new DbListas();
$dbCalendario = new DbCalendario();
$dbPostQx = new DbAtencionPostqx();

$combo = new Combo_Box();
$funciones_persona = new FuncionesPersona();
$utilidades = new Utilidades();
$contenido = new ContenidoHtml();
$contenido->validar_seguridad(1);
$funciones_hc = new FuncionesHistoriaClinica();

$opcion = $utilidades->str_decode($_REQUEST["opcion"]);

function ver_historia_clinica($id_paciente, $nombre_persona, $documento_persona, $tipo_documento, $telefonos, $fecha_nacimiento, $edad_paciente) {
	$dbHistoriaClinica = new DbHistoriaClinica();
	$dbVariables = new Dbvariables();
	$dbPostQx = new DbAtencionPostqx();
	
    $funciones_persona = new FuncionesPersona();
	$utilidades = new Utilidades();
	$contenido = new ContenidoHtml();
	$tipo_acceso_menu = $contenido->obtener_permisos_menu($utilidades->str_decode($_POST["hdd_numero_menu"]));
	
	//Datos de pacientes vinculados a seguimiento post qx
	$tabla_seguimiento = $dbPostQx->getPacienteVinculado($id_paciente);
	
	
	//Datos de pacientes vinculados a seguimiento post qx
	$tabla_respuestas = $dbPostQx->getRespuestaPaciente($id_paciente);
	
	$id_usuario = $_SESSION["idUsuario"];
    @$credencial = $utilidades->str_decode($_POST["credencial"]);
    @$id_menu = $utilidades->str_decode($_POST["hdd_numero_menu"]);

    
    ?>
    <fieldset style="width: 90%; margin: auto;">
        <legend>Datos del paciente:</legend>
        <table style="width: 500px; margin: auto; font-size: 10pt;">
            <tr>
                <td align="right" style="width:40%">Tipo de documento:</td>
                <td align="left" style="width:60%"><b><?php echo($tipo_documento); ?></b></td>
            </tr>
            <tr>
                <td align="right">N&uacute;mero de identificaci&oacute;n:</td>
                <td align="left"><b><?php echo($documento_persona); ?></b></td>
            </tr>
            <tr>
                <td align="right">Nombre completo:</td>
                <td align="left"><b><?php echo($nombre_persona); ?></b></td>
            </tr>
            <tr>
                <td align="right">Fecha de nacimiento:</td>
                <td align="left"><b><?php echo($fecha_nacimiento); ?></b></td>
            </tr>
            <tr>
                <td align="right">Edad:</td>
                <td align="left"><b><?php echo($edad_paciente); ?> a&ntilde;os</b></td>
            </tr>
            <tr>
                <td align="right">Tel&eacute;fonos:</td>
                <td align="left"><b><?php echo($telefonos); ?></b></td>
            </tr>
        <?php
        if (count($tabla_seguimiento) == 0){
			$id_seguimiento = 0;
		?>
			<tr>
            	<td align="center" colspan="2">
            		<br />
               		<input type="button" id="btn_vincular_paciente" value="Vincular Paciente" class="btnPrincipal" onclick="vincular_paciente(<?php echo($id_paciente); ?>, '<?php echo($nombre_persona); ?>', '<?php echo($id_seguimiento); ?>');" />
                </td>
            </tr>
		<?php
        }
		else{
			$hora_preferible = $tabla_seguimiento['hora_preferible'];
			$hora_preferible = explode(":", $hora_preferible);
			$hora = $hora_preferible[0]; 
			$minutos =  $hora_preferible[1];
			$segundos =  $hora_preferible[2];
			
			if($hora > 12){
				$hora = $hora - 12;
				$jornada = 'pm';
			}
			else{
				$jornada = 'am';
			}
			
			$hora_preferible = $hora.":".$minutos.":".$jornada;
			
		?>
			<tr>
            	<th align="center" colspan="2">
               		<h4>Datos de configuraci&oacute;n</h4> 
                </th>
            </tr>
            <tr>
                <td align="right" style="width:40%;font-size: 17px">Clave de verificaci&oacute;n:</td>
                <td align="left" style="width:60%;font-size: 20px"><b><?php echo($tabla_seguimiento['clave_verificacion']); ?></b></td>
            </tr>
            <tr>
                <td align="right">Hora preferible para responer:</td>
                <td align="left"><b><?php echo($hora_preferible); ?></b></td>
            </tr>
            <tr>
                <td align="right">Fecha de seguimiento uno:</td>
                <td align="left"><b><?php echo($tabla_seguimiento['fecha_seguimiento_uno_t']); ?></b></td>
            </tr>
            <tr>
                <td align="right">Fecha de seguimiento dos:</td>
                <td align="left"><b><?php echo($tabla_seguimiento['fecha_seguimiento_dos_t']); ?></b></td>
            </tr>
            <tr>
                <td align="right">Fecha de seguimiento tres:</td>
                <td align="left"><b><?php echo($tabla_seguimiento['fecha_seguimiento_tres_t']); ?></b></td>
            </tr>
            <tr>
                <td align="right">Ojo Intervenido:</td>
                <td align="left"><b><?php echo($tabla_seguimiento['id_ojo_t']); ?></b></td>
            </tr>
            <tr>
                <td align="right">Ayuda de voz Activada:</td>
                <td align="left"><b><?php echo($tabla_seguimiento['ind_voz_t']); ?></b></td>
            </tr>
            <tr>
                <td align="right">Paciente excluido del programa:</td>
                <td align="left"><b><?php echo($tabla_seguimiento['ind_inclusion_t']); ?></b></td>
            </tr>
            <tr>
            	<td align="center" colspan="2">
            		<br />
               		<input type="button" id="btn_vincular_paciente" value="Editar datos de Configuraci&oacute;n" class="btnPrincipal" onclick="vincular_paciente(<?php echo($id_paciente); ?>, '<?php echo($nombre_persona); ?>', '<?php echo($tabla_seguimiento['id_seguimiento']); ?>');" />
                </td>
            </tr>
 		<?php	
 		
 		
 		if (count($tabla_respuestas) > 0){
 			
			
		?>
        <br/>
        <table class="paginated modal_table" style="width: 100%;  margin: auto;">
        	<thead>
        	<tr><th colspan='8'>Encuestas Efectuadas</th></tr>	
        	<tr>
				<th style="width: 5%;">Fecha</th>	
				<th style="width: 5%;">OJO</th>	
				<th style="width: 15%;">Ha sentido dolor intenso</th>
				<th style="width: 15%;">Ha notado su ojo muy enrojecido</th>
				<th style="width: 15%;">Ha tenido abundante secreci&oacute;n (pus o laga&ntilde;as)</th>
				<th style="width: 15%;">La visi&oacute;n no ha mejorado o se ha empeorado</th>
				<th style="width: 15%;">Se est&aacute; aplicando las gotas exactamente de acuerdo a lo indicado</th>
				<th style="width: 15%;">Foto</th>
			</tr>
			 </thead>
			<?php
			$rta1='';
			$rta2='';
			$rta3='';
			$rta4='';
			$rta5='';
			foreach($tabla_respuestas as $fila_perfiles){
				
				$rta1=$fila_perfiles['pregunta_1'];
				$rta2=$fila_perfiles['pregunta_2'];
				$rta3=$fila_perfiles['pregunta_3'];
				$rta4=$fila_perfiles['pregunta_4'];
				$rta5=$fila_perfiles['pregunta_5'];
				
				
				/*if($rta1 == 1){ $rta1 = 'Si';}else{ $rta1 = 'No';}
				if($rta2 == 1){ $rta2 = 'Si';}else{ $rta2 = 'No';}
				if($rta3 == 1){ $rta3 = 'Si';}else{ $rta3 = 'No';}
				if($rta4 == 1){ $rta4 = 'Si';}else{ $rta4 = 'No';}
				if($rta5 == 1){ $rta5 = 'Si';}else{ $rta5 = 'No';}
				*/
				
				$id_ojo=$fila_perfiles['id_ojo'];
				$fecha_respuesta=$fila_perfiles['fecha_respuesta_t'];
				
				$valor_imagen=$fila_perfiles['valor_imagen'];
				
				
				
				if($id_ojo==1){
					$id_ojo='OD';
				}
				else{
					$id_ojo='OI';
				}
					
					?>
					<tr>
						<td align="left"><?php echo $fecha_respuesta; ?></td>
						<td align="left"><?php echo $id_ojo; ?></td>
						<td align="center"><?php echo $rta1; ?></td>
						<td align="center"><?php echo $rta2; ?></td>
						<td align="center"><?php echo $rta3; ?></td>
						<td align="center"><?php echo $rta4; ?></td>
						<td align="center"><?php echo $rta5; ?></td>
						<td align="center" onclick='mostrar_foto("<?php echo($valor_imagen); ?>")' ><img src="<?php echo $valor_imagen; ?>" height="100" width="100"></td>
					</tr>
					<?php
					$rta1='';
					$rta2='';
					$rta3='';
					$rta4='';
					$rta5='';
					
				//}
				
					
			}
			?>
		</table>
        <br/>
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
 		}
		}
        ?>
        </table>
    </fieldset>
    <div style="width:70%; margin:auto; height:320px; overflow:auto;">
    </div>
    <?php
}


switch ($opcion) {
    case "1": //Consultar HC del paciente	
        $txt_paciente_hc = $utilidades->str_decode($_POST["txt_paciente_hc"]);
        $id_usuario_crea = $_SESSION["idUsuario"];
        $tabla_personas = $dbHistoriaClinica->getPacientesHistoriaClinica($txt_paciente_hc);
        $cantidad_datos = count($tabla_personas);

        if ($cantidad_datos == 1) {//Si se encontro un solo registro
            $id_paciente = $tabla_personas[0]['id_paciente'];
            $nombre_1 = $tabla_personas[0]['nombre_1'];
            $nombre_2 = $tabla_personas[0]['nombre_2'];
            $apellido_1 = $tabla_personas[0]['apellido_1'];
            $apellido_2 = $tabla_personas[0]['apellido_2'];
            $numero_documento = $tabla_personas[0]['numero_documento'];
            $tipo_documento = $tabla_personas[0]['tipo_documento'];
            $fecha_nacimiento = $tabla_personas[0]['fecha_nac_persona'];
            $telefonos = $tabla_personas[0]['telefono_1'];
			if ($tabla_personas[0]['telefono_2'] != "") {
	            $telefonos .= " - ".$tabla_personas[0]['telefono_2'];
			}
            $nombres_apellidos = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
            //Edad del paciente
            $datos_paciente = $dbPacientes->getEdadPaciente($id_paciente, '');
            $edad_paciente = $datos_paciente['edad'];

            ver_historia_clinica($tabla_personas[0]['id_paciente'], $nombres_apellidos, $numero_documento, $tipo_documento, $telefonos, $fecha_nacimiento, $edad_paciente);
        } else if ($cantidad_datos > 1) {
            ?>
            <table id='tabla_persona_hc'  border='0' class="paginated modal_table" style="width: 70%; margin: auto;">
                <thead>
                    <tr class='headegrid'>
                        <th class="headegrid" align="center">Documento</th>	
                        <th class="headegrid" align="center">Pacientes</th>
                    </tr>
                </thead>
                <?php
                foreach ($tabla_personas as $fila_personas) {
                    $id_personas = $fila_personas['id_paciente'];
                    $nombre_1 = $fila_personas['nombre_1'];
                    $nombre_2 = $fila_personas['nombre_2'];
                    $apellido_1 = $fila_personas['apellido_1'];
                    $apellido_2 = $fila_personas['apellido_2'];
                    $numero_documento = $fila_personas['numero_documento'];
                    $tipo_documento = $fila_personas['tipo_documento'];
                    $nombres_apellidos = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
                    $fecha_nacimiento = $fila_personas['fecha_nac_persona'];
                    $telefonos = $fila_personas['telefono_1'] . " - " . $fila_personas['telefono_2'];
                    $nombres_apellidos = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
                    //Edad del paciente
                    $datos_paciente = $dbPacientes->getEdadPaciente($id_personas, '');
                    $edad_paciente = $datos_paciente['edad'];
                    ?>
                    <tr class='celdagrid' onclick="ver_registros_hc(<?php echo($id_personas); ?>, '<?php echo($nombres_apellidos); ?>', '<?php echo($numero_documento); ?>', '<?php echo($tipo_documento); ?>', '<?php echo($telefonos); ?>', '<?php echo($fecha_nacimiento); ?>', '<?php echo($edad_paciente); ?>');">
                        <td align="left"><?php echo $numero_documento; ?></td>	
                        <td align="left"><?php echo $nombres_apellidos; ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>

            <script id='ajax'>
                //<![CDATA[ 
                $(function() {
                    $('.paginated', 'tabla_persona_hc').each(function(i) {
                        $(this).text(i + 1);
                    });

                    $('table.paginated').each(function() {
                        var currentPage = 0;
                        var numPerPage = 5;
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
        } else if ($cantidad_datos == 0) {
            echo"<div class='msj-vacio'>
					<p>No se encontraron pacientes</p>
			     </div>";
        }
        break;
		
    case "2": //Mostrar los registros de historia clínica de un paciente
        $id_persona = $utilidades->str_decode($_POST['id_persona']);
        $nombre_persona = $utilidades->str_decode($_POST['nombre_persona']);
        $documento_persona = $utilidades->str_decode($_POST['documento_persona']);
        $tipo_documento = $utilidades->str_decode($_POST['tipo_documento']);
        $telefonos = $utilidades->str_decode($_POST['telefonos']);
        $fecha_nacimiento = $utilidades->str_decode($_POST['fecha_nacimiento']);
        $edad_paciente = $utilidades->str_decode($_POST['edad_paciente']);
        ver_historia_clinica($id_persona, $nombre_persona, $documento_persona, $tipo_documento, $telefonos, $fecha_nacimiento, $edad_paciente);
     	break;
		
	case "3": //Formulario de Vincular paciente a postqx
		
		$tabla_fecha_actual = $dbCalendario->getFechaActual();
		$id_paciente = $utilidades->str_decode($_POST['id_paciente']);
        $nombre_paciente = $utilidades->str_decode($_POST['nombre_paciente']);
		$id_seguimiento = $utilidades->str_decode($_POST['id_seguimiento']);
		
		if($id_seguimiento == 0){
			$fecha_actual = $tabla_fecha_actual['fecha_hoy'];
			$fecha_actual = date("d/m/Y", strtotime($fecha_actual));
			$txt_fecha_primer=$fecha_actual;
			$txt_fecha_segundo='';
			$txt_fecha_tercer='';
			$hora_preferible='';
			$id_ojos = 0;
			$sino_voz=0;
			$sino_excluye=0;
		}
		else{
			$tabla_seguimiento = $dbPostQx->getSeguimientoPaciente($id_seguimiento);
			$txt_fecha_primer = $tabla_seguimiento['fecha_seguimiento_uno_t'];
			$txt_fecha_segundo = $tabla_seguimiento['fecha_seguimiento_dos_t'];
			$txt_fecha_tercer = $tabla_seguimiento['fecha_seguimiento_tres_t'];
			
			
			$id_ojos = $tabla_seguimiento['id_ojos'];
			$sino_voz = $tabla_seguimiento['ind_voz'];;
			$sino_excluye = $tabla_seguimiento['ind_inclusion'];
			
			$hora_preferible = $tabla_seguimiento['hora_preferible'];
			$hora_preferible = explode(":", $hora_preferible);
			$hora = $hora_preferible[0]; 
			$minutos =  $hora_preferible[1];
			$segundos =  $hora_preferible[2];
			
			if($hora > 12){
				$hora = $hora - 12;
				$jornada = 'pm';
			}
			else{
				$jornada = 'am';
			}
			
			$hora_preferible = $hora.":".$minutos.":".$jornada;
			
			
			
		}
		
		
		
			
	?>
	
	<?php
    if($id_seguimiento == 0){
		?>
		<div class="encabezado">
			<h3>Vincular a: <?php echo($nombre_paciente); ?> <br />para atenci&oacute;n Post-Quir&uacute;rgica de Catarata</h3>
		</div>
		<?php
	}
	else{
		?>
		<div class="encabezado">
			<h3>Actualizar datos de: <?php echo($nombre_paciente); ?> <br />para atenci&oacute;n Post-Quir&uacute;rgica de Catarata</h3>
		</div>
		<?php
	}
    ?>
	
	
    
    <form id="frm_vincular" name="frm_vincular">
        
        <fieldset style="width: 75%; margin: auto;">
        	<legend>Configuraci&oacute;n</legend>
        	<input type="hidden" id="hdd_id_paciente" value="<?php echo($id_paciente); ?>" />
        	<input type="hidden" id="hdd_id_seguimiento" value="<?php echo($id_seguimiento); ?>" />
        	
			<table border='0' style="width:70%; margin:auto; font-size:10pt;">
                
                <tr>
                    <td align="right"  colspan="2">
                        <label class="inline">Hora preferible para responer *:</label>
                    </td>
                    <td align="left"  colspan="2">
                    	<input type="text" id="txt_hora_encuesta" value="<?php echo($hora_preferible); ?>" class="time" maxlength="10" style="width:100px;" onBlur="valida_hora_campo(this.value);" />
                    	<script id="ajax" type="text/javascript">
			                $(function() {
			                    $('#txt_hora_encuesta').timepicker();
			                });
			            </script>
                    </td>
                </tr>
                <tr>
                    <td align="right" style="width:20%;">
                        <label class="inline">Fecha primer control *:</label>
                    </td>
                    <td align="left" style="width:5%;">
                    	<input type="text" id="txt_fecha_primer" value="<?php echo($txt_fecha_primer); ?>" class="input" maxlength="10" style="width:100px;" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" />
						<script id="ajax" type="text/javascript">
							$(function() {
								$('#txt_fecha_primer').fdatepicker({
									format: 'dd/mm/yyyy'
								});
							});
                        </script>
                    </td>
                    <td align="right" style="width:20%;">
                        <label>Fecha segundo control <br /> (cuarto o quinto d&iacute;a) *:</label>
                    </td>
                    <td align="left" style="width:5%;">
                        <input type="text" id="txt_fecha_segundo" value="<?php echo($txt_fecha_segundo); ?>" class="input" maxlength="10" style="width:100px;" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" />
						<script id="ajax" type="text/javascript">
							$(function() {
								$('#txt_fecha_segundo').fdatepicker({
									format: 'dd/mm/yyyy'
								});
							});
                        </script>	
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        <label class="inline">Fecha tercer control (Opcinal):</label>
                    </td>
                    <td align="left">
                        <input type="text" id="txt_fecha_tercer" value="<?php echo($txt_fecha_tercer); ?>" class="input" maxlength="10" style="width:100px;" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" />
						<script id="ajax" type="text/javascript">
							$(function() {
								$('#txt_fecha_tercer').fdatepicker({
									format: 'dd/mm/yyyy'
								});
							});
                        </script>	
                    </td>
                    <td align="right">
                        <label class="inline">Ojo Intervenido *:</label>
                    </td>
                    <td align="left">
                        <?php
                        	$lista_ojos = $dbListas->getListaDetalles(14);
							$combo->getComboDb("cmb_ojos", $id_ojos, $lista_ojos, "id_detalle, nombre_detalle", "--Seleccione--", "", true, "");
						?>
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        <label class="inline">Activar ayuda de voz *:</label>
                    </td>
                    <td align="left">
                        <?php
                        	$lista_sino = $dbListas->getListaDetalles(10);
							$combo->getComboDb("cmb_sino_voz", $sino_voz, $lista_sino, "id_detalle, nombre_detalle", "--Seleccione--", "", true, "");
						?>
                    </td>
                    <td align="right">
                        <label class="inline">Paciente excluido del programa  *:</label>
                    </td>
                    <td align="left">
                        <?php
                        	$combo->getComboDb("cmb_sino_excluye", $sino_excluye, $lista_sino, "id_detalle, nombre_detalle", "--Seleccione--", "", true, "");
						?>
                    </td>
                </tr>
                
            </table>
                    
            <?php
            if($id_seguimiento == 0){
				?><input type="button" id="btnVincularPaciente" nombre="btnVincularPaciente" value="Vincular" class="btnPrincipal peq" onclick="validar_vincular_paciente()" /><?php
			}
			else{
				?><input type="button" id="btnVincularPaciente" nombre="btnVincularPaciente" value="Actualizar" class="btnPrincipal peq" onclick="validar_vincular_paciente()" /><?php
			}
            ?>
                    
                    
                    
                    
                    
                    
        </fieldset>
    	
    </form>    
	
    <?php
	break;
		
	case "4": //Vincular Paciente
		$id_usuario_crea = $_SESSION["idUsuario"];
		$hdd_id_paciente = $_POST["hdd_id_paciente"];
		$hdd_id_seguimiento = $_POST["hdd_id_seguimiento"];
		$txt_fecha_primer = $_POST["txt_fecha_primer"];
		$txt_fecha_segundo = $_POST["txt_fecha_segundo"];
		$txt_fecha_tercer = $_POST["txt_fecha_tercer"];
		$cmb_ojos = $_POST["cmb_ojos"];
		$cmb_sino_voz = $_POST["cmb_sino_voz"];
		$cmb_sino_excluye = $_POST["cmb_sino_excluye"];
		
		
		/*Para generar clave del paciente*/
		$clave_paciente = new Class_Generar_Clave();
		$InitalizationKey = $clave_paciente->generate_secret_key(16);
		$TimeStamp = $clave_paciente->get_timestamp();
		$secretkey = $clave_paciente->base32_decode($InitalizationKey);
		$clave_verificacion = $clave_paciente->oath_hotp($secretkey, $TimeStamp);
		
		$ind_vin = $dbPostQx->vincular_paciente_postqx($hdd_id_paciente, $hdd_id_seguimiento, $txt_fecha_primer, $txt_fecha_segundo, $txt_fecha_tercer, $cmb_ojos, $cmb_sino_voz, $cmb_sino_excluye, $id_usuario_crea, $clave_verificacion);
		
		?>
		<input type="hidden" name="hdd_exito" id="hdd_exito" value="<?php echo($ind_vin); ?>" />
		<div class='contenedor_error' id='contenedor_error'></div>
		<div class='contenedor_exito' id='contenedor_exito'></div>
		<?php
		
	
	break;
		
	
	
	
	case "5": //
		
	break;
}
?>
