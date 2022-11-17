<?php
session_start();

header('Content-Type: text/xml; charset=UTF-8');

require_once("../db/DbListas.php");
require_once("../db/DbHistoriaClinica.php");
require_once("../db/DbVariables.php");
require_once("../db/DbPacientes.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../principal/ContenidoHtml.php");
require_once("../funciones/FuncionesPersona.php");
require_once("../funciones/Utilidades.php");

$dbListas = new DbListas();
$dbHistoriaClinica = new DbHistoriaClinica();
$dbVariables = new Dbvariables();
$dbPacientes = new DbPacientes();
$contenido = new ContenidoHtml();
$contenido->validar_seguridad(1);
$funciones_persona = new FuncionesPersona();
$utilidades = new Utilidades();
$combo = new Combo_Box();
$opcion = $utilidades->str_decode($_POST["opcion"]);

function ver_historia_clinica($id_paciente, $indice) {
	$dbListas = new DbListas();
	$dbHistoriaClinica = new DbHistoriaClinica();
	$dbVariables = new Dbvariables();
	$dbPacientes = new DbPacientes();
    $funciones_persona = new FuncionesPersona();
	$utilidades = new Utilidades();
	$contenido = new ContenidoHtml();
	$combo = new Combo_Box();
	$tipo_acceso_menu = $contenido->obtener_permisos_menu($utilidades->str_decode($_POST["hdd_numero_menu"]));
    
	//Se obtienen los datos del paciente
	$paciente_obj = $dbPacientes->getExistepaciente3($id_paciente);
	$id_tipo_documento = $paciente_obj["id_tipo_documento"];
	$numero_documento = $paciente_obj["numero_documento"];
	$nombre_1 = $paciente_obj["nombre_1"];
	$nombre_2 = $paciente_obj["nombre_2"];
	$apellido_1 = $paciente_obj["apellido_1"];
	$apellido_2 = $paciente_obj["apellido_2"];
	$sexo = $paciente_obj["sexo"];
	$telefono_1 = $paciente_obj["telefono_1"];
	$telefono_2 = $paciente_obj["telefono_2"];
	$fecha_nacimiento = $paciente_obj["fecha_nacimiento_aux"];
	
    $id_usuario = $_SESSION["idUsuario"];
    @$credencial = $utilidades->str_decode($_POST["credencial"]);
    @$id_menu = $utilidades->str_decode($_POST["hdd_numero_menu"]);
	
    //Se inserta el registro de ingreso a la historia clínica
    $dbHistoriaClinica->crear_ingreso_hc($id_usuario, $id_paciente, "", "", 161);
	
    $tabla_registro_hc = $dbHistoriaClinica->getRegistrosHistoriaClinica($id_paciente);
    ?>
    <input type="hidden" id="hdd_id_paciente_<?php echo($indice); ?>" value="<?php echo($id_paciente); ?>" />
    <fieldset style="width: 90%; margin: auto;">
        <legend>Datos del paciente <?php echo($indice) ?>:</legend>
        <table style="width: 100%; margin: auto; font-size: 10pt;">
            <tr>
                <td align="right" style="width:30%"><label class="inline">Tipo documento*:</label></td>
                <td align="left" style="width:70%">
                	<?php
						//Se carga la lista de tipos de documento
						$lista_tipos_documento = $dbListas->getListaDetalles(2, 1);
						$combo->getComboDb("cmb_tipo_documento_".$indice, $id_tipo_documento, $lista_tipos_documento, "id_detalle,nombre_detalle", "", "", 1, "");
					?>
                </td>
            </tr>
            <tr>
                <td align="right"><label class="inline">N&uacute;mero documento*:</label></td>
                <td align="left">
                	<input type="text" id="txt_numero_documento_<?php echo($indice); ?>" value="<?php echo($numero_documento); ?>" maxlength="20" onblur="trim(this.value);" />
                </td>
            </tr>
            <tr>
                <td align="right"><label class="inline">Nombres*:</label></td>
                <td align="left">
                	<input type="text" id="txt_nombre_1_<?php echo($indice); ?>" value="<?php echo($nombre_1); ?>" maxlength="100" style="width:48%; display:inline;" onblur="trim(this.value);" />
                	<input type="text" id="txt_nombre_2_<?php echo($indice); ?>" value="<?php echo($nombre_2); ?>" maxlength="100" style="width:48%; display:inline;" onblur="trim(this.value);" />
                </td>
            </tr>
            <tr>
                <td align="right"><label class="inline">Apellidos*:</label></td>
                <td align="left">
                	<input type="text" id="txt_apellido_1_<?php echo($indice); ?>" value="<?php echo($apellido_1); ?>" maxlength="100" style="width:48%; display:inline;" onblur="trim(this.value);" />
                	<input type="text" id="txt_apellido_2_<?php echo($indice); ?>" value="<?php echo($apellido_2); ?>" maxlength="100" style="width:48%; display:inline;" onblur="trim(this.value);" />
                </td>
            </tr>
            <tr>
                <td align="right"><label class="inline">Sexo*:</label></td>
                <td align="left">
                	<?php
						//Se carga la lista de sexos
						$lista_sexos = $dbListas->getListaDetalles(1, 1);
						$combo->getComboDb("cmb_sexo_".$indice, $sexo, $lista_sexos, "id_detalle,nombre_detalle", "", "", 1, "");
					?>
                </td>
            </tr>
            <tr>
                <td align="right"><label class="inline">Fecha nacimiento*:</label></td>
                <td align="left">
                	<input type="text" class="input" maxlength="10" style="width:120px;" id="txt_fecha_nacimiento_<?php echo($indice); ?>" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" value="<?php echo($fecha_nacimiento); ?>" />
					<script id="ajax">
                        $(function() {
                            window.prettyPrint && prettyPrint();
                            $('#txt_fecha_nacimiento_<?php echo($indice); ?>').fdatepicker({
                                format: 'dd/mm/yyyy'
                            });
                        });
					</script>
                </td>
            </tr>
            <tr>
                <td align="right"><label class="inline">Tel&eacute;fono(s)*:</label></td>
                <td align="left">
                	<input type="text" id="txt_telefono_1_<?php echo($indice); ?>" value="<?php echo($telefono_1); ?>" maxlength="10" style="width:48%; display:inline;" onkeypress="return solo_caracteres(event, '0123456789');" onblur="trim(this.value);" />
                	<input type="text" id="txt_telefono_2_<?php echo($indice); ?>" value="<?php echo($telefono_2); ?>" maxlength="10" style="width:48%; display:inline;" onkeypress="return solo_caracteres(event, '0123456789');" onblur="trim(this.value);" />
                </td>
            </tr>
        </table>
    </fieldset>
    <div style="width:100%; margin:auto; height:320px; overflow:auto;">
    <table class="modal_table" style="width:99%; margin:auto;" align="left">
        <thead>
            <tr>
                <th class="th_reducido" align="center" style="width:15%;">Fecha</th>
                <th class="th_reducido" align="center" style="width:84%;">Tipo de registro</th>
                <th class="th_reducido" align="center" style="width:1%;"></th>
            </tr>
        </thead>
        <?php
        if (count($tabla_registro_hc) > 0) {
            foreach ($tabla_registro_hc as $fila_registro_hc) {
                $id_paciente = $fila_registro_hc['id_paciente'];
                $nombre_1 = $fila_registro_hc['nombre_1'];
                $nombre_2 = $fila_registro_hc['nombre_2'];
                $apellido_1 = $fila_registro_hc['apellido_1'];
                $apellido_2 = $fila_registro_hc['apellido_2'];
                $nombre_persona = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
                $id_admision = $fila_registro_hc['id_admision'];
                $pagina_consulta = $fila_registro_hc['pagina_menu'];
				
                $id_hc = $fila_registro_hc['id_hc'];
                $id_tipo_reg = $fila_registro_hc['id_tipo_reg'];
                $nombre_tipo_reg = $fila_registro_hc['nombre_tipo_reg'];
				if ($fila_registro_hc["nombre_alt_tipo_reg"] != "") {
					$nombre_tipo_reg .= " (".$fila_registro_hc["nombre_alt_tipo_reg"].")";
				}
				
                $fecha_hc = $fila_registro_hc['fecha_hora_hc_t'];
                $estado_hc = $fila_registro_hc['ind_estado'];
				
                if ($estado_hc == 1) {
                    $img_estado = "<img src='../imagenes/icon-convencion-no-disponible.png' />";
                } else if ($estado_hc == 2) {
                    $img_estado = "<img src='../imagenes/icon-convencion-disponible.png' />";
                }
				
				$extension_arch = "";
				if ($fila_registro_hc["ruta_arch_adjunto"] != "") {
					$extension_arch = strtolower($utilidades->get_extension_arch($fila_registro_hc["ruta_arch_adjunto"]));
				}
				
				if ($id_tipo_reg == 17) { // Si es HC fisica
					$tabla_hc_fisica = $dbHistoriaClinica->getHistoriaFisica($id_hc);
					$tabla_ruta_hc = $dbVariables->getVariable(11);
					$ruta_hc = $tabla_ruta_hc['valor_variable'];
					$ruta_hc_new = str_replace('\\', '/', $ruta_hc)."/".$tabla_hc_fisica['archivo_hc'];
					?>
					<tr>
	                    <td class="td_reducido" align="left" ><?php echo($fecha_hc); ?></td>
	                    <td class="td_reducido" align="left" >  
	                    	 <a href="../historia_clinica/abrir_pdf.php?ruta=<?php echo($ruta_hc_new);?>" target="_blank">
	                    	 	<?php
                                	echo($nombre_tipo_reg);
								?>
	                    	 </a>
                    	</td>
	                    <td class="td_reducido" align="center"><?php echo($img_estado); ?></td>
	                    	
	                </tr>
	                <?php	
				} else {
					?>
	                <tr>
	                    <td class="td_reducido" align="left" onclick="mostrar_consultas_div(<?php echo($id_paciente); ?>, '<?php echo($nombre_persona); ?>', <?php echo(intval($id_admision, 10)); ?>, '<?php echo($pagina_consulta); ?>', <?php echo($id_hc); ?>, <?php echo($credencial); ?>, <?php echo($id_menu); ?>);"><?php echo($fecha_hc); ?></td>
	                    <td class="td_reducido" align="left" onclick="mostrar_consultas_div(<?php echo($id_paciente); ?>, '<?php echo($nombre_persona); ?>', <?php echo(intval($id_admision, 10)); ?>, '<?php echo($pagina_consulta); ?>', <?php echo($id_hc); ?>, <?php echo($credencial); ?>, <?php echo($id_menu); ?>);"><?php echo($nombre_tipo_reg); ?></td>
	                    <td class="td_reducido" align="center"><?php echo($img_estado); ?></td>
	                </tr>
	                <?php
                }
            }
        } else {
            //Si no se encontraron registros de historia clinica
            ?>
            <tr>
                <td colspan="5">
                    <div class="msj-vacio">
                        <p>No hay HC para este paciente</p>
                    </div>
                </td>
            </tr>
            <?php
        }
        ?>
    </table>
    </div>
    <br />
    <div id="d_unificar_hc_<?php echo($indice); ?>">
	    <input type="button" value="Unificar historias cl&iacute;nicas en el paciente <?php echo($indice); ?>" class="btnPrincipal" onclick="confirmar_unificar_hc(<?php echo($indice); ?>);" />
    </div>
    <div id="d_esperar_unificar_hc_<?php echo($indice); ?>" style="display:none;">
    	<img src="../imagenes/ajax-loader.gif" />
    </div>
    <?php
}

switch ($opcion) {
    case "1": //Consultar HC del paciente	
        $indice = intval($_POST["indice"], 10);
        $texto_busqueda = $utilidades->str_decode($_POST["texto_busqueda"]);
		
        $tabla_personas = $dbHistoriaClinica->getPacientesHistoriaClinica($texto_busqueda);
        $cantidad_datos = count($tabla_personas);

        if ($cantidad_datos == 1) {//Si se encontro un solo registro
            $id_paciente = $tabla_personas[0]['id_paciente'];
			
            ver_historia_clinica($id_paciente, $indice);
        } else if ($cantidad_datos > 1) {
            ?>
            <table id="tabla_persona_hc_<?php echo($indice); ?>" border="0" class="paginated modal_table" style="width:100%;">
                <thead>
                    <tr class="headegrid">
                        <th class="headegrid" align="center">Documento</th>	
                        <th class="headegrid" align="center">Pacientes</th>
                    </tr>
                </thead>
                <?php
					foreach ($tabla_personas as $fila_personas) {
						$id_paciente = $fila_personas['id_paciente'];
						$nombre_1 = $fila_personas['nombre_1'];
						$nombre_2 = $fila_personas['nombre_2'];
						$apellido_1 = $fila_personas['apellido_1'];
						$apellido_2 = $fila_personas['apellido_2'];
						$numero_documento = $fila_personas['numero_documento'];
						$nombres_apellidos = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
                ?>
                <tr class="celdagrid" onclick="ver_registros_hc(<?php echo($id_paciente); ?>, <?php echo($indice); ?>);">
                    <td align="left"><?php echo($numero_documento); ?></td>	
                    <td align="left"><?php echo($nombres_apellidos); ?></td>
                </tr>
                <?php
					}
				?>
            </table>
            <script id='ajax'>
                //<![CDATA[ 
                $(function() {
                    $('.paginated', 'tabla_persona_hc_<?php echo($indice); ?>').each(function(i) {
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
        @$indice = intval($_POST["indice"], 10);
        @$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
        ver_historia_clinica($id_paciente, $indice);
     	break;
		
	case "3": //Unificar historias clínicas
		$id_usuario = $_SESSION["idUsuario"];
		@$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
		@$id_paciente_2 = $utilidades->str_decode($_POST["id_paciente_2"]);
		@$id_tipo_documento = $utilidades->str_decode($_POST["id_tipo_documento"]);
		@$numero_documento = $utilidades->str_decode($_POST["numero_documento"]);
		@$nombre_1 = $utilidades->str_decode($_POST["nombre_1"]);
		@$nombre_2 = $utilidades->str_decode($_POST["nombre_2"]);
		@$apellido_1 = $utilidades->str_decode($_POST["apellido_1"]);
		@$apellido_2 = $utilidades->str_decode($_POST["apellido_2"]);
		@$sexo = $utilidades->str_decode($_POST["sexo"]);
		@$fecha_nacimiento = $utilidades->str_decode($_POST["fecha_nacimiento"]);
		@$telefono_1 = $utilidades->str_decode($_POST["telefono_1"]);
		@$telefono_2 = $utilidades->str_decode($_POST["telefono_2"]);
		
		$resultado = $dbHistoriaClinica->unificar_historias_clinicas($id_paciente, $id_paciente_2, $id_tipo_documento, $numero_documento,
					 $nombre_1, $nombre_2, $apellido_1, $apellido_2, $sexo, $fecha_nacimiento, $telefono_1, $telefono_2, $id_usuario);
	?>
    <input type="hidden" id="hdd_resultado_unificar_hc" value="<?php echo($resultado); ?>" />
    <?php
		break;
}
?>
