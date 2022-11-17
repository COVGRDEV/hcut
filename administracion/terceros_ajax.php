<?php
	session_start();
	
	header("Content-Type: text/xml; charset=UTF-8");
	
	require_once("../db/DbTerceros.php");
	require_once("../db/DbPacientes.php");
	require_once("../db/DbListas.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/Class_Combo_Box.php");
	
	$dbTerceros = new DbTerceros();
	$dbPacientes = new DbPacientes();
	$dbListas = new DbListas();
	
	$contenido_html = new ContenidoHtml();
	$utilidades = new Utilidades();
	$funcionesPersona = new FuncionesPersona();
	$combo = new Combo_Box();
	
	$tipo_acceso_menu = $contenido_html->obtener_permisos_menu($_POST["hdd_numero_menu"]);
	
	$opcion = $_POST["opcion"];
	
	switch ($opcion) {
		case "1": //Carga el listado de exámenes, la tabla: maestro_examenes
			$parametro = $utilidades->str_decode($_POST["parametro"]);
			
			$lista_terceros = $dbTerceros->getListasTercerosParametro($parametro, "");
        ?>
        <table class="paginated modal_table" style="width:99%; margin:auto;">
            <thead>
                <tr><th colspan="4">Listado de terceros - <?php echo(count($lista_terceros)." registros"); ?></th></tr>
                <tr>
                    <th style="width:10%;">Tipo de documento</th>
                    <th style="width:12%;">N&uacute;mero de documento</th>
                    <th style="width:42%;">Nombre</th>
                    <th style="width:6%;">Estado</th>
                </tr>
            </thead>
            <?php
            if (count($lista_terceros) >= 1) {
                foreach ($lista_terceros as $tercero_aux) {
                    $estado = $tercero_aux["ind_activo"];
                    if ($estado == 1) {
                        $estado = "Activo";
                        $class_estado = "activo";
                    } else if ($estado == 0) {
                        $estado = "No Activo";
                        $class_estado = "inactivo";
                    }
                    ?>
                    <tr onclick="seleccionar_tercero('<?php echo($tercero_aux["id_tercero"]); ?>', 1);">
                        <td align="center"><?php echo($tercero_aux["tipo_documento"]); ?></td>
                        <td align="center">
							<?php
								$numero_documento_aux = $tercero_aux["numero_documento"];
								if ($tercero_aux["numero_verificacion"] != "") {
									$numero_documento_aux .= "-".$tercero_aux["numero_verificacion"];
								}
								echo($numero_documento_aux);
							?>
                        </td>
                        <td align="left"><?php echo($tercero_aux["nombre_tercero"]); ?></td>
                        <td align="center"><span class="<?php echo($class_estado); ?>"><?php echo($estado); ?></span></td>

                    </tr>
                    <?php
                }
            } else {
                ?>
                <td colspan="4">No se encontraron terceros</td>
                <?php
            }
            ?>
        </table>
        <script id="ajax">
            //<![CDATA[ 
            $(function () {
                $(".paginated", "table").each(function (i) {
                    $(this).text(i + 1);
                });

                $("table.paginated").each(function () {
                    var currentPage = 0;
                    var numPerPage = 10;
                    var $table = $(this);
                    $table.bind("repaginate", function () {
                        $table.find("tbody tr").hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
                    });
                    $table.trigger("repaginate");
                    var numRows = $table.find("tbody tr").length;
                    var numPages = Math.ceil(numRows / numPerPage);
                    var $pager = $('<div class="pager"></div>');
                    for (var page = 0; page < numPages; page++) {
                        $('<span class="page-number"></span>').text(page + 1).bind("click", {
                            newPage: page
                        }, function (event) {
                            currentPage = event.data["newPage"];
                            $table.trigger("repaginate");
                            $(this).addClass("active").siblings().removeClass("active");
                        }).appendTo($pager).addClass("clickable");
                    }
                    $pager.insertBefore($table).find("span.page-number:first").addClass("active");
                });
            });
            //]]>
        </script>
        <?php
			break;
			
		case "2": //Formulario de creación y edición de terceros
			$tipo = $utilidades->str_decode($_POST["tipo"]);
			@$id_tercero_p = $utilidades->str_decode($_POST["id_tercero"]);
			@$id_contador = $utilidades->str_decode($_POST["id_contador"]);
			
			$id_tercero = "";
			$id_tipo_documento = "";
			$numero_documento = "";
			$numero_verificacion = "";
			$nombre_tercero = "";
			$email_tercero = "";
			$id_paciente = "";
			
			$regActivo = "checked";
			
			if ($tipo == "0") {
				$tercero_obj = $dbTerceros->getTercero($id_tercero_p);
				
				$id_tercero = $id_tercero_p;
				$id_tipo_documento = $tercero_obj["id_tipo_documento"];
				$numero_documento = $tercero_obj["numero_documento"];
				$numero_verificacion = $tercero_obj["numero_verificacion"];
				$nombre_tercero = $tercero_obj["nombre_tercero"];
				$nombre_1 = $tercero_obj["nombre_1"];
				$nombre_2 = $tercero_obj["nombre_2"];
				$apellido_1 = $tercero_obj["apellido_1"];
				$apellido_2 = $tercero_obj["apellido_2"];
				$email_tercero = $tercero_obj["email"];
				$id_paciente = $tercero_obj["id_paciente"];
				$cod_obligacion = $tercero_obj["cod_obligacion"];
				$cod_tributario = $tercero_obj["cod_tributario"];
				if($tercero_obj["ind_iva"] == "1"){
					$reg_iva = "checked";
				}else{
					$reg_iva = "";
				}
				
				//Si el registro esta activado
				if ($tercero_obj["ind_activo"] == "0") {
					$regActivo = "";
				}
			}
        ?>
        <div class="encabezado">
        	<h3>
            	<?php
                	if ($tipo == "0") {
						echo("Edici&oacute;n de Terceros");
					} else {
						echo("Creaci&oacute;n de Terceros");
					}
				?>
            </h3>
        </div>
        <div class="contenedor_error" id="d_contenedor_error_2"></div>
        <div class="contenedor_exito" id="d_contenedor_exito_2"></div>
        <div style="width:98%; margin:auto;">
            <form id="frm_nuevo_tercero" name="frm_nuevo_tercero">
                <table style="width:100%;">
                    <tr>
                        <td align="left" style="width:18%;">
                            <input type="hidden" id="hdd_id_tercero_t" name="hdd_id_tercero_t" value="<?php echo($id_tercero); ?>" />
                            <label class="inline">Tipo de documento*:</label>
                        </td>
                        <td align="left" style="width:57%" colspan="3">
                            <?php
								$lista_tipos_documento = $dbListas->getListaDetalles(27, 1);
								
								$combo->getComboDb("cmb_tipo_documento_t", $id_tipo_documento, $lista_tipos_documento, "id_detalle, nombre_detalle", "--Seleccione--", "seleccionar_tipo_doc(this.value);", 1, "", "", "no-margin");
							?>
                        </td>
                        <td align="right" style="width:20%;">
                        	<label class="inline">Registro Activo:</label>
                        </td>
                        <td align="left" style="width:5%;">
                        	<input type="checkbox" name="chk_activo_t" id="chk_activo_t" <?php echo($regActivo); ?> />
                        </td>
                    </tr>
                    <tr>
                        <td align="left">
                            <label class="inline">N&uacute;mero de documento*:</label>
                        </td>
                        <td align="left" style="width:15%">
                            <input type="text" id="txt_numero_documento_t" name="txt_numero_documento_t" value="<?php echo($numero_documento); ?>" maxlength="15" onkeypress="return solo_alfanumericos(event);" onblur="trim_cadena(this);" style="width:100%;" />
                        </td>
                        <td align="center" style="width:2%;"><label class="inline">-</label></td>
                        <td align="left" style="width:65%" colspan="3">
                            <input type="text" id="txt_numero_verificacion_t" name="txt_numero_verificacion_t" value="<?php echo($numero_verificacion); ?>" maxlength="1" onkeypress="return solo_numeros(event, false);" style="width:6%;" />
                        </td>
                    </tr>
                    <?php
                    	if ($id_tipo_documento == "146") {
							$display_rs = "table-row";
							$display_nc = "none";
						} else {
							$display_rs = "none";
							$display_nc = "table-row";
						}
					?>
                    <tr id="tr_razon_social" style="display:<?= $display_rs ?>">
                        <td align="left">
                            <label class="inline">Raz&oacute;n social*:</label>
                        </td>
                        <td align="left" colspan="5">
                            <input type="text" id="txt_nombre_tercero_t" name="txt_nombre_tercero_t" value="<?php echo($nombre_tercero); ?>" maxlength="200" onblur="trim_cadena(this);" />
                        </td>
                    </tr>
                    <tr id="tr_nombre_completo" style="display:<?= $display_nc ?>">
                        <td align="left">
                            <label class="inline">Nombre completo*:</label>
                        </td>
                        <td align="left" colspan="5">
                        	<table style="width:100%;">
                            	<tr>
                                	<td style="width:25%">
			                            <input type="text" id="txt_nombre_1_t" name="txt_nombre_1_t" value="<?php echo($nombre_1); ?>" maxlength="100" onblur="trim_cadena(this);" placeholder="Primer nombre" />
                                    </td>
                                	<td style="width:25%">
			                            <input type="text" id="txt_nombre_2_t" name="txt_nombre_2_t" value="<?php echo($nombre_2); ?>" maxlength="100" onblur="trim_cadena(this);" placeholder="Segundo nombre" />
                                    </td>
                                	<td style="width:25%">
			                            <input type="text" id="txt_apellido_1_t" name="txt_apellido_1_t" value="<?php echo($apellido_1); ?>" maxlength="100" onblur="trim_cadena(this);" placeholder="Primer apellido" />
                                    </td>
                                	<td style="width:25%">
			                            <input type="text" id="txt_apellido_2_t" name="txt_apellido_2_t" value="<?php echo($apellido_2); ?>" maxlength="100" onblur="trim_cadena(this);" placeholder="Segundo apellido" />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="left">
                            <label class="inline">e-mail*:</label>
                        </td>
                        <td colspan="3">
                        	<input type="text" id="txt_email_tercero_t" name="txt_email_tercero_t" value="<?php echo($email_tercero); ?>" maxlength="50" onblur="trim_cadena(this);" style="width:300px;" />
                        </td>
                    </tr>
                     <tr id="tr_obligaciones_terceros" style="display:<?= $display_rs ?>">
                        <td align="left" colspan="5">
                            <table style="width:100%; ">
                                <tr>
                                    <td style="width:19%">
                                        <label class="inline">Obligaciones*:</label>
                                    </td>
                                    <td style="width:30%">
                                         <?php
											$lista_obligaciones_terceros = $dbListas->getListaDetalles(93, 1);
											
											$combo->getComboDb("cmb_obligaciones_terceros", $cod_obligacion, $lista_obligaciones_terceros, "codigo_detalle, nombre_detalle", "--Seleccione--", "", 1, "", "", "no-margin");
										?>
                                    </td>
                                    <td style="width:17%">
                                         <label class="inline">Detalles tributarios*:</label>
                                    </td>
                                    <td style="width:30%">
                                        <?php
											$lista_detalles_contributarios = $dbListas->getListaDetalles(94, 1);
											
											$combo->getComboDb("cmb_detalles_tributarios_t", $cod_tributario, $lista_detalles_contributarios, "codigo_detalle, nombre_detalle", "--Seleccione--", "", 1, "", "", "no-margin");
										?>
                                    </td>
                                    
                                    <td align="right" style="width:10%;">
                                        <label class="inline">IVA:*</label>
                                    </td>
                                    <td align="left" style="width:5%;">
                                        <input type="checkbox" name="chk_ind_iva" id="chk_ind_iva" <?=$reg_iva?> value="1" />
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <input type="hidden" name="hdd_obligaciones_terceros" id="hdd_obligaciones_terceros" value="R-99-PN" />
                         <input type="hidden" name="hdd_detalles_tributarios" id="hdd_detalles_tributarios" value="ZZ" />
                    </tr>
 
                    <?php
                    	if ($id_contador == "") {
							$nombre_paciente = "";
							if ($id_paciente != "") {
								$paciente_obj = $dbPacientes->getExistepaciente3($id_paciente);
								if (isset($paciente_obj["id_paciente"])) {
									$nombre_paciente = $funcionesPersona->obtenerNombreCompleto($paciente_obj["nombre_1"], $paciente_obj["nombre_2"], $paciente_obj["apellido_1"], $paciente_obj["apellido_2"]);
								}
							}
					?>
                    <tr>
                        <td align="left">
                            <label class="inline">Paciente:</label>
                        </td>
                        <td align="left" colspan="5">
                            <div id="d_paciente">
                                <h6>
                                    <input type="hidden" id="hdd_id_paciente_t" name="hdd_id_paciente_t" value="<?php echo($id_paciente); ?>" />
                                    <div id="d_nombre_paciente" style="float:left; max-width:85%; text-align:left;"><?php echo($nombre_paciente); ?></div>
                                    <img id="img_borrar_paciente" src="../imagenes/Error-icon.png" style="float:right;" onclick="borrar_paciente();" title="Quitar paciente" />
                                    <div id="d_buscar_paciente" class="d_buscar" style="float:right;" onclick="mostrar_buscar_paciente();" title="Buscar paciente"></div>
                                </h6>
                            </div>
                        </td>
                    </tr>
                    <?php
						} else {
					?>
                    <input type="hidden" id="hdd_id_paciente_t" name="hdd_id_paciente_t" value="<?php echo($id_paciente); ?>" />
                    <?php
						}
					?>
                    <tr>
                        <td align="center" colspan="6">
            	            <?php
								if ($tipo_acceso_menu == "2") {
									if ($tipo == "0") {
							?>
                            <input type="button" id="btn_guardar_tercero" nombre="btn_guardar_tercero" value="Guardar cambios" class="btnPrincipal peq" onclick="editar_tercero();"/>
                            <?php
									} else if ($id_contador == "") {
							?>
                            <input type="button" id="btn_guardar_tercero" nombre="btn_guardar_tercero" value="Crear tercero" class="btnPrincipal peq" onclick="crear_tercero();"/>
                            <?php
									} else {
							?>
                            <input type="button" id="btn_guardar_tercero" nombre="btn_guardar_tercero" value="Crear tercero" class="btnPrincipal peq" onclick="crear_tercero_pago(<?php echo($id_contador); ?>, 0);"/>
                            <?php
									}
								}
							?>
                            <div id="d_resultado_tercero_t" style="display:none;"></div>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <?php
			break;
			
		case "3": //Crear/editar tercero
			$id_usuario = $_SESSION["idUsuario"];
			@$tipo = $utilidades->str_decode($_POST["tipo"]);
			@$id_tercero = $utilidades->str_decode($_POST["id_tercero"]);
			@$id_tipo_documento = $utilidades->str_decode($_POST["id_tipo_documento"]);
			@$numero_documento = $utilidades->str_decode($_POST["numero_documento"]);
			@$numero_verificacion = $utilidades->str_decode($_POST["numero_verificacion"]);
			@$nombre_tercero = $utilidades->str_decode($_POST["nombre_tercero"]);
			@$nombre_1 = $utilidades->str_decode($_POST["nombre_1"]);
			@$nombre_2 = $utilidades->str_decode($_POST["nombre_2"]);
			@$apellido_1 = $utilidades->str_decode($_POST["apellido_1"]);
			@$apellido_2 = $utilidades->str_decode($_POST["apellido_2"]);
			@$email = $utilidades->str_decode($_POST["email"]);
			@$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
			@$ind_activo = $utilidades->str_decode($_POST["ind_activo"]);
			@$id_contador = $utilidades->str_decode($_POST["id_contador"]);
			@$indice = $utilidades->str_decode($_POST["indice"]);
			
			@$obligaciones = $utilidades->str_decode($_POST["obligaciones"]);
			@$det_tributarios = $utilidades->str_decode($_POST["det_tributarios"]);
			@$ind_iva = $utilidades->str_decode($_POST["ind_iva"]);
			
			if ($tipo == "1") {//Crear
				$resultado = $dbTerceros->crear_tercero($id_tipo_documento, $numero_documento, $numero_verificacion, $nombre_tercero,$nombre_1, $nombre_2, $apellido_1, $apellido_2, $email, $id_paciente, $ind_activo, $id_usuario, $obligaciones, $det_tributarios, $ind_iva);
				
				if ($resultado > 0 && $id_contador != "") {
					//Se buscan los datos del tercero
					$tercero_obj = $dbTerceros->getTercero($resultado);
		?>
        <input type="hidden" id="hdd_nombre_tercero_crea<?php echo($id_contador."_".$indice); ?>" value="<?php echo($tercero_obj["nombre_tercero"]); ?>" />
        <?php
				}
			} else if ($tipo == "0") {//Editar
				$resultado = $dbTerceros->editar_tercero($id_tercero, $id_tipo_documento, $numero_documento, $numero_verificacion, $nombre_tercero, $nombre_1, $nombre_2, $apellido_1, $apellido_2, $email, $id_paciente, $ind_activo, $id_usuario, $obligaciones, $det_tributarios, $ind_iva);
			}
		?>
        <input type="hidden" id="hdd_guardar_tercero" name="hdd_guardar_tercero" value="<?php echo($resultado); ?>" />
        <?php
			break;
			
		case "4"://Imprime formulario flotante de búsqueda de pacientes
		?>
		<div class="encabezado">
			<h3>Pacientes</h3>
		</div>
		<div>
			<form id="frmListadoPacientes" name="frmListadoPacientes">
				<table style="width: 100%;">
					<tbody>
                    	<tr valign="middle">
							<td align="center" colspan="2">
								<div id="advertenciasg"></div>
							</td>
						</tr>
						<tr>
							<td>
								<input type="text" id="txtParametroPacientes" name="txtParametroPacientes" placeholder="N&uacute;mero de documento o nombre del paciente" onblur="trim_cadena(this);" />
							</td>
							<td style="width: 8%;">
								<input type="submit" value="Buscar" class="btnPrincipal peq" onclick="buscar_pacientes_terceros();" />
							</td>
						</tr>
					</tbody>
                </table>
			</form>
			<div id="d_resultado_b_pacientes"></div>
		</div>    
		<?php
			break;
			
		case "5": //Resultados de búsqueda de pacientes
			@$parametro_pacientes = $utilidades->str_decode($_POST["parametro_pacientes"]);
			
			$lista_pacientes = $dbPacientes->getBuscarpacientes($parametro_pacientes);
		?>
		<table class="paginated modal_table" style="width:80%; margin:auto;">
			<thead>
				<tr>
					<th style="width:15%;">Tipo de documento</th>
					<th style="width:20%;">N&uacute;mero de documento</th>
					<th style="width:65%;">Nombre</th>
				</tr>
			</thead>
			<?php
				if (count($lista_pacientes) > 0) {
					foreach ($lista_pacientes as $paciente_aux) {
						$nombre_paciente_aux = $funcionesPersona->obtenerNombreCompleto($paciente_aux["nombre_1"], $paciente_aux["nombre_2"], $paciente_aux["apellido_1"], $paciente_aux["apellido_2"]);
			?>
			<tr onclick="agregar_paciente(<?php echo($paciente_aux["id_paciente"]); ?>, '<?php echo($nombre_paciente_aux); ?>');">
				<td align="center"><?php echo($paciente_aux["nombre_detalle"]); ?></td>
				<td align="center"><?php echo($paciente_aux["numero_documento"]); ?></td>
				<td align="left"><?php echo($nombre_paciente_aux); ?></td>
			</tr>
			<?php
					}
				} else {
			?>
			<tr>
				<td colspan="3">No se hallaron pacientes</td>
			</tr>
			<?php
				}
			?>
		</table>
        <br />
        <script id="ajax">
			//<![CDATA[ 
			$(function() {
				$(".paginated", "table").each(function(i) {
					$(this).text(i + 1);
				});
				
				$("table.paginated").each(function() {
					var currentPage = 0;
					var numPerPage = 10;
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
			});
			//]]>
		</script>
        <br />
		<?php
			break;
	}
?>
