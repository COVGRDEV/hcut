<?php
header('Content-Type: text/xml; charset=UTF-8');
session_start();

require_once("../db/DbListasEspera.php");
require_once("../db/DbListas.php");
require_once("../db/DbVariables.php");
require_once("../db/DbPacientes.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../funciones/Utilidades.php");
require_once("../funciones/FuncionesPersona.php");
require_once("../principal/ContenidoHtml.php");

$dbListasEspera = new DbListasEspera();
$dbListas = new DbListas();
$dbVariables = new Dbvariables();
$dbPacientes = new DbPacientes();
$combo = new Combo_Box();
$utilidades = new Utilidades();
$funcionesPersona = new FuncionesPersona();
$contenido = new ContenidoHtml();
$contenido->validar_seguridad(1);
$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);

$opcion = $utilidades->str_decode($_POST["opcion"]);

switch ($opcion) {
    case "1": //Buscar en la lista de espera
        @$parametro = $utilidades->str_decode($_POST["parametro"]);
		@$id_tipo_lista = $utilidades->str_decode($_POST["id_tipo_lista"]);
		
		$lista_espera = $dbListasEspera->get_listas_espera($parametro, $id_tipo_lista, 235);
	?>
    <table class="paginated modal_table" style="width: 99%; margin: auto;">
        <thead>
            <tr><th colspan="5">Lista de espera - <?php echo(count($lista_espera)." registros"); ?></th></tr>
            <tr>
                <th style="width:12%;">Fecha de inscripci&oacute;n</th>
                <th style="width:18%;">N&uacute;mero de documento</th>
                <th style="width:44%;">Nombre</th>
                <th style="width:18%;">Cirug&iacute;a</th>
                <th style="width:8%;">&nbsp;</th>
            </tr>
        </thead>
        <?php
			if (count($lista_espera) >= 1) {
				foreach ($lista_espera as $espera_aux) {
		?>
        <tr>
            <td align="center" onclick="seleccionar_espera('<?php echo($espera_aux["id_reg_lista"]); ?>');">
				<?php echo($espera_aux["fecha_lista_t"]); ?>
            </td>
            <td align="center" onclick="seleccionar_espera('<?php echo($espera_aux["id_reg_lista"]); ?>');">
				<?php echo($espera_aux["cod_tipo_documento"]." ".$espera_aux["numero_documento"]); ?>
            </td>
            <td align="left" onclick="seleccionar_espera('<?php echo($espera_aux["id_reg_lista"]); ?>');">
				<?php echo($funcionesPersona->obtenerNombreCompleto($espera_aux["nombre_1"], $espera_aux["nombre_2"], $espera_aux["apellido_1"], $espera_aux["apellido_2"])); ?>
            </td>
            <td align="center" onclick="seleccionar_espera('<?php echo($espera_aux["id_reg_lista"]); ?>');">
				<?php echo($espera_aux["tipo_cirugia"]); ?>
            </td>
            <td align="center">
            	<?php
                	if ($tipo_acceso_menu == "2") {
				?>
            	<img src="../imagenes/cancelar.png" title="Borrar registro de lista de espera" onclick="borrar_lista_espera('<?php echo($espera_aux["id_reg_lista"]); ?>');" />
                <?php
					}
				?>
            </td>
        </tr>
        <?php
				}
			} else {
		?>
        <tr>
        	<td colspan="5">No se encontraron registros</td>
        </tr>
        <?php
			}
		?>
    </table>
    <?php
    	if (count($lista_espera) >= 1) {
	?>
    <br />
    <div style="width:99%; text-align:center">
        <input type="button" id="btn_exportar_lista" value="Exportar a Excel" class="btnPrincipal peq" onclick="exportar_excel_lista();" />
        <form id="frm_reporte_espera" name="frm_reporte_espera" action="listas_espera_excel.php" method="post" style="display:none;" target="_blank"> 
            <input type="hidden" id="hdd_parametro_e" name="hdd_parametro_e" />
            <input type="hidden" id="hdd_tipo_lista_e" name="hdd_tipo_lista_e" />
            <input type="hidden" id="hdd_tipo_reporte_e" name="hdd_tipo_reporte_e" value="1" />
        </form>
    </div>
    <?php
		}
	?>
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
		
    case "2": //Crear o modificar registro de espera
		@$accion = $utilidades->str_decode($_POST["accion"]);
		@$id_reg_lista = $utilidades->str_decode($_POST["id_reg_lista"]);
		
		$fecha_lista = "";
		$id_paciente = "";
        $nombre_1 = "";
        $nombre_2 = "";
        $apellido_1 = "";
        $apellido_2 = "";
		$id_tipo_documento = "";
		$numero_documento = "";
		$telefono_contacto = "";
		$id_tipo_cirugia = "";
		
        if ($accion == "1") {
			$fecha_obj = $dbVariables->getFechaActualMostrar();
            $fecha_lista = $fecha_obj["fecha_actual_mostrar"];
        } else if ($accion == "2") {
			$registro_espera_obj = $dbListasEspera->get_registro_espera($id_reg_lista);
			
			$fecha_lista = $registro_espera_obj["fecha_lista_t"];
			$id_paciente = $registro_espera_obj["id_paciente"];
			$nombre_1 = $registro_espera_obj["nombre_1"];
			$nombre_2 = $registro_espera_obj["nombre_2"];
			$apellido_1 = $registro_espera_obj["apellido_1"];
			$apellido_2 = $registro_espera_obj["apellido_2"];
			$id_tipo_documento = $registro_espera_obj["id_tipo_documento"];
			$numero_documento = $registro_espera_obj["numero_documento"];
			$telefono_contacto = $registro_espera_obj["telefono_contacto"];
			$id_tipo_cirugia = $registro_espera_obj["id_tipo_cirugia"];
        }
        ?>
        <form id="frm_lista_espera" name="frm_lista_espera">
            <div style="text-align:left;">    
                <fieldset style="">
                	<?php
                    	if ($accion == "1") {
					?>
                    <legend>Nuevo registro</legend>
                    <?php
						} else if ($accion == "2") {
					?>
                    <legend>Modificar paciente en lista de espera</legend>
                    <?php
						}
					?>
                    <input type="hidden" id="hdd_reg_lista" value="<?php echo($id_reg_lista); ?>" />
                    <input type="hidden" id="hdd_paciente" value="<?php echo($id_paciente); ?>" />
					<table border='0' style="width:100%; margin:auto; font-size:10pt;">
                        <tr>
                            <td align="right" style="width:15%;">
                                <label class="inline">Tipo de identificaci&oacute;n:</label>
                            </td>
                            <td align="left" style="width:35%;">
                            	<?php
                                	$lista_tipos_doc = $dbListas->getListaDetalles(2);
									
									$combo->getComboDb("cmb_tipo_documento", $id_tipo_documento, $lista_tipos_doc, "id_detalle, nombre_detalle", "--Seleccione--", "", true);
								?>
                            </td>
                            <td align="right" style="width:15%;">
                                <label>No. de identificaci&oacute;n:</label>
                            </td>
                            <td align="left" style="width:35%;">
                                <input type="text" id="txt_numero_documento" value="<?php echo($numero_documento); ?>" onblur="trim_cadena(this); buscar_paciente();" style="width:150px;" maxlength="20">
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                                <label class="inline">Primer nombre:</label>
                            </td>
                            <td align="left">
                                <input type="text" id="txt_nombre_1" value="<?php echo($nombre_1); ?>" onblur="trim_cadena(this);" />
                            </td>
                            <td align="right">
                                <label class="inline">Segundo nombre:</label>
                            </td>
                            <td align="left">
                                <input type="text" id="txt_nombre_2" value="<?php echo($nombre_2); ?>" onblur="trim_cadena(this);" />
                            </td>
                        </tr>
                        <tr>
                            <td align="right" style="width:15%;">
                                <label class="inline">Primer apellido:</label>
                            </td>
                            <td align="left" style="width:35%;">
                                <input type="text" id="txt_apellido_1" value="<?php echo($apellido_1); ?>" onblur="trim_cadena(this);" />
                            </td>
                            <td align="right" style="width:15%;">
                                <label class="inline">Segundo apellido:</label>
                            </td>
                            <td align="left" style="width:35%;">
                                <input type="text" id="txt_apellido_2" value="<?php echo($apellido_2); ?>" onblur="trim_cadena(this);" />
                            </td>
                        </tr>
                        <tr>
                        	<td align="right">
                            	<label class="inline">Fecha de inscripci&oacute;n:</label>
                            </td>
                            <td align="left">
                            	<input type="text" id="txt_fecha_lista" value="<?php echo($fecha_lista); ?>" class="input" maxlength="10" style="width:120px;" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" />
								<script id="ajax" type="text/javascript">
									$(function() {
										$('#txt_fecha_lista').fdatepicker({
											format: 'dd/mm/yyyy'
										});
									});
                                </script>
                            </td>
                            <td align="right">
                                <label class="inline">Tipo de cirug&iacute;a:</label>
                            </td>
                            <td align="left">
                            	<?php
                                	$lista_tipos_cirugias = $dbListas->getListaDetalles(42);
									
									$combo->getComboDb("cmb_tipo_cirugia", $id_tipo_cirugia, $lista_tipos_cirugias, "id_detalle, nombre_detalle", "--Seleccione--", "", true, "width:100%;");
								?>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                                <label>Tel&eacute;fono de contacto:</label>
                            </td>
                            <td align="left">
                                <input type="text" id="txt_telefono_contacto" value="<?php echo($telefono_contacto); ?>" onblur="trim_cadena(this);" maxlength="50">
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </div>
            <input type="button" id="btnCancelarConvenio" nombre="btnCancelarConvenio" value="Cancelar" class="btnSecundario peq" onclick="buscar_lista_espera()" />
            <?php
				if ($tipo_acceso_menu == "2") {
	            	if ($accion == "1") {
			?>
            <input type="button" id="btnGuardarConvenio" nombre="btnGuardarConvenio" value="Guardar" class="btnPrincipal peq" onclick="crear_lista_espera();" />
            <?php
					} else {
			?>
            <input type="button" id="btnGuardarConvenio" nombre="btnGuardarConvenio" value="Guardar" class="btnPrincipal peq" onclick="modificar_lista_espera();" />
            <?php
					}
				}
			?>
        </form>
        <?php
        break;
		
    case "3": //Modificar lista de espera
		$id_usuario = $_SESSION["idUsuario"];
		@$id_reg_lista = $utilidades->str_decode($_POST["id_reg_lista"]);
		@$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
		@$id_tipo_documento = $utilidades->str_decode($_POST["id_tipo_documento"]);
		@$numero_documento = $utilidades->str_decode($_POST["numero_documento"]);
		@$nombre_1 = $utilidades->str_decode($_POST["nombre_1"]);
		@$nombre_2 = $utilidades->str_decode($_POST["nombre_2"]);
		@$apellido_1 = $utilidades->str_decode($_POST["apellido_1"]);
		@$apellido_2 = $utilidades->str_decode($_POST["apellido_2"]);
		@$fecha_lista = $utilidades->str_decode($_POST["fecha_lista"]);
		@$id_tipo_cirugia = $utilidades->str_decode($_POST["id_tipo_cirugia"]);
		@$telefono_contacto = $utilidades->str_decode($_POST["telefono_contacto"]);
		
		$resultado = $dbListasEspera->editar_lista_espera($id_reg_lista, $id_paciente, $id_tipo_documento, $numero_documento,
				$nombre_1, $nombre_2, $apellido_1, $apellido_2, $fecha_lista, $id_tipo_cirugia, $telefono_contacto, 235, $id_usuario);
	?>
    <input type="hidden" id="hdd_resul_guardar_lista" value="<?php echo($resultado); ?>" />
    <?php
        break;
		
    case "4": //Crear lista de espera
		$id_usuario = $_SESSION["idUsuario"];
		@$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
		@$id_tipo_documento = $utilidades->str_decode($_POST["id_tipo_documento"]);
		@$numero_documento = $utilidades->str_decode($_POST["numero_documento"]);
		@$nombre_1 = $utilidades->str_decode($_POST["nombre_1"]);
		@$nombre_2 = $utilidades->str_decode($_POST["nombre_2"]);
		@$apellido_1 = $utilidades->str_decode($_POST["apellido_1"]);
		@$apellido_2 = $utilidades->str_decode($_POST["apellido_2"]);
		@$fecha_lista = $utilidades->str_decode($_POST["fecha_lista"]);
		@$id_tipo_cirugia = $utilidades->str_decode($_POST["id_tipo_cirugia"]);
		@$telefono_contacto = $utilidades->str_decode($_POST["telefono_contacto"]);
		
		$resultado = $dbListasEspera->crear_lista_espera($id_paciente, $id_tipo_documento, $numero_documento, $nombre_1, $nombre_2,
				$apellido_1, $apellido_2, $fecha_lista, $id_tipo_cirugia, $telefono_contacto, 235, $id_usuario);
	?>
    <input type="hidden" id="hdd_resul_guardar_lista" value="<?php echo($resultado); ?>" />
    <?php
        break;
		
    case "5": //Buscar paciente por tipo y número de documento
		@$id_tipo_documento = $utilidades->str_decode($_POST["id_tipo_documento"]);
		@$numero_documento = $utilidades->str_decode($_POST["numero_documento"]);
		
		//Se buscan los pacientes que tengan el mismo número de documento
		$lista_pacientes = $dbPacientes->getExistepaciente4($numero_documento, $id_tipo_documento);
		
		$id_paciente = "";
		$id_tipo_documento = "";
		$nombre_1 = "";
		$nombre_2 = "";
		$apellido_1 = "";
		$apellido_2 = "";
		$telefono_contacto = "";
		if (count($lista_pacientes) > 0) {
			$id_paciente = $lista_pacientes[0]["id_paciente"];
			$id_tipo_documento = $lista_pacientes[0]["id_tipo_documento"];
			$nombre_1 = $lista_pacientes[0]["nombre_1"];
			$nombre_2 = $lista_pacientes[0]["nombre_2"];
			$apellido_1 = $lista_pacientes[0]["apellido_1"];
			$apellido_2 = $lista_pacientes[0]["apellido_2"];
			$telefono_contacto = $lista_pacientes[0]["telefono_1"];
			if ($lista_pacientes[0]["telefono_2"] != "") {
				$telefono_contacto .= " - ".$lista_pacientes[0]["telefono_2"];
			}
		}
	?>
    <input type="hidden" id="hdd_paciente_b" value="<?php echo($id_paciente); ?>" />
    <input type="hidden" id="hdd_tipo_documento_b" value="<?php echo($id_tipo_documento); ?>" />
    <input type="hidden" id="hdd_nombre_1_b" value="<?php echo($nombre_1); ?>" />
    <input type="hidden" id="hdd_nombre_2_b" value="<?php echo($nombre_2); ?>" />
    <input type="hidden" id="hdd_apellido_1_b" value="<?php echo($apellido_1); ?>" />
    <input type="hidden" id="hdd_apellido_2_b" value="<?php echo($apellido_2); ?>" />
    <input type="hidden" id="hdd_telefono_contacto_b" value="<?php echo($telefono_contacto); ?>" />
    <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:98%;">
	    <tr>
	        <th align="center">
                <h4>Existe un paciente con este n&uacute;mero de documento:<br />
                <?php
					echo $numero_documento." (".$funcionesPersona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2).")";
				?>
                ,<br/>&iquest;Desea cargar estos datos al formulario?</h4>
            </th>
        </tr>
	    <tr>
	        <th align="center" style="width:5%">
	           	<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" value="Si" onclick="cargar_datos_paciente();" class="btnPrincipal" />
	           	<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" value="No" onclick="cerrar_div_centro();" class="btnSecundario" />
	        </th>
	    </tr>
	</table>     
    <?php
        break;
		
    case "6": //Borrar un registro de lista de espera
		$id_usuario = $_SESSION["idUsuario"];
		@$id_reg_lista = $utilidades->str_decode($_POST["id_reg_lista"]);
		
		$resultado = $dbListasEspera->borrar_lista_espera($id_reg_lista, $id_usuario);
	?>
    <input type="hidden" id="hdd_resul_borrar_lista" value="<?php echo($resultado); ?>" />
    <?php
        break;
}
?>
