<?php
	session_start();
	/*
	  Pagina para cambiar admisiones de estado
	  Autor: Feisar Moreno - 21/10/2015
	 */
 	header('Content-Type: text/xml; charset=UTF-8');
	
	require_once("../db/DbAdmision.php");
	require_once("../db/DbCitas.php");
	require_once("../db/DbPacientes.php");
	require_once("../db/DbTiposCitasDetalle.php");
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbListas.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/Utilidades.php");
	
	$dbAdmision = new DbAdmision();
	$dbCitas = new DbCitas();
	$dbPacientes = new DbPacientes();
	$dbTiposCitasDetalle = new DbTiposCitasDetalle();
	$dbUsuarios = new DbUsuarios();
	$dbListas = new DbListas();
	
	$funciones_persona = new FuncionesPersona();
	$utilidades = new Utilidades();
	$combo=new Combo_Box();
	$contenido = new ContenidoHtml();
	$contenido->validar_seguridad(1);
	
	$opcion=$_POST["opcion"];
	
	$tipo_acceso_menu = $contenido->obtener_permisos_menu($utilidades->str_decode($_POST["hdd_numero_menu"]));
	
	switch ($opcion) {
		case "1": //Buscar admisiones y citas
			@$parametro = $utilidades->str_decode($_POST["parametro"]);
			
			$lista_admisiones = $dbAdmision->get_lista_admisiones_cambio_estado($parametro);
			$lista_citas = $dbCitas->get_lista_citas_cambio_estado($parametro);
			
			if (count($lista_admisiones) > 0 || count($lista_citas) > 0) {
		?>
		<table id='tabla_persona_hc'  border='0' class="paginated modal_table" style="width: 95%; margin: auto;">
	        <thead>
	        	<tr class='headegrid'>
					<th class="headegrid" align="center" style="width:13%;">Documento</th>	
					<th class="headegrid" align="center" style="width:27%;">Paciente</th>
					<th class="headegrid" align="center" style="width:10%;">Fecha</th>
					<th class="headegrid" align="center" style="width:15%;">Tipo de cita</th>
					<th class="headegrid" align="center" style="width:20%;">Estado actual</th>
					<th class="headegrid" align="center" style="width:15%;">Cambio estado</th>
				</tr>
			</thead>
			<?php
				if (count($lista_admisiones) > 0) {
					foreach ($lista_admisiones as $admision_aux) {
						$nombre_completo = $funciones_persona->obtenerNombreCompleto($admision_aux["nombre_1"], $admision_aux["nombre_2"], $admision_aux["apellido_1"], $admision_aux["apellido_2"]);
			?>
			<tr class="celdagrid" onclick="cargar_admision_cita(<?php echo($admision_aux["id_admision"]); ?>, 0, <?php echo($admision_aux["ind_cambio_estado"]); ?>);">
				<td align="center"><?php echo($admision_aux["cod_tipo_documento"]." ".$admision_aux["numero_documento"]); ?></td>	
				<td align="left"><?php echo($nombre_completo); ?></td>
				<td align="center"><?php echo($admision_aux["fecha_admision_t"]); ?></td>
				<td align="left"><?php echo($admision_aux["nombre_tipo_cita"]); ?></td>
				<td align="left"><?php echo($admision_aux["nombre_estado"]); ?></td>
                <td align="center">
					<?php
						if ($admision_aux["ind_cambio_estado"] == 1) {
					?>
                    <span class="activo">S&iacute;</span>
                    <?php
						} else {
					?>
                    <span class="inactivo">No</span>
                    <?php
						}
					?>
                </td>
			</tr>
			<?php
					}
				}
				
				if (count($lista_citas) > 0) {
					foreach ($lista_citas as $cita_aux) {
						$nombre_completo = $funciones_persona->obtenerNombreCompleto($cita_aux["nombre_1"], $cita_aux["nombre_2"], $cita_aux["apellido_1"], $cita_aux["apellido_2"]);
			?>
			<tr class="celdagrid" onclick="cargar_admision_cita(0, <?php echo($cita_aux["id_cita"]); ?>, 1);">
				<td align="center"><?php echo($cita_aux["cod_tipo_documento"]." ".$cita_aux["numero_documento"]); ?></td>	
				<td align="left"><?php echo($nombre_completo); ?></td>
				<td align="center"><?php echo($cita_aux["fecha_cita_t"]); ?></td>
				<td align="left"><?php echo($cita_aux["nombre_tipo_cita"]); ?></td>
				<td align="left"><?php echo($cita_aux["nombre_estado_cita"]); ?></td>
                <td align="center">S&iacute;</td>
			</tr>
			<?php
					}
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
			} else {
		?>
        <div class='msj-vacio'>
			<p>No se encontraron pacientes</p>
        </div>
        <?php
			}
			break;
			
		case "2": //Formulario de cambio de estado
			@$id_admision = intval($_POST["id_admision"], 10);
			@$id_cita = intval($_POST["id_cita"], 10);
			@$ind_cambio_estado = intval($_POST["ind_cambio_estado"], 10);
			
			if ($id_admision > 0) {
				//Se cargan los datos de la admisión y del paciente
				$admision_obj = $dbAdmision->get_admision($id_admision);
				$id_paciente = $admision_obj["id_paciente"];
				$paciente_obj = $dbPacientes->getExistepaciente3($id_paciente);
				
				$tipo_documento = $paciente_obj["tipodocumento"];
				$numero_documento = $paciente_obj["numero_documento"];
				$nombre_completo = $funciones_persona->obtenerNombreCompleto($paciente_obj["nombre_1"], $paciente_obj["nombre_2"], $paciente_obj["apellido_1"], $paciente_obj["apellido_2"]);
				$telefonos = $paciente_obj["telefono_1"];
				if ($paciente_obj["telefono_2"] != "") {
					$telefonos .= " - ".$paciente_obj["telefono_2"];
				}
				$nombre_tipo_cita = $admision_obj["nombre_tipo_cita"];
				$nombre_estado = $admision_obj["nombre_estado"];
				
				//Se obtienen los estados a los que puede ir la admisión
				$lista_estados = $dbAdmision->get_lista_estados_mover_admision($id_admision, $admision_obj["id_estado_atencion"]);
			} else {
				//Se cargan los datos de la cita
				$cita_obj = $dbCitas->getCita($id_cita);
				
				$tipo_documento = $cita_obj["nombre_detalle"];
				$numero_documento = $cita_obj["numero_documento"];
				$nombre_completo = $funciones_persona->obtenerNombreCompleto($cita_obj["nombre_1"], $cita_obj["nombre_2"], $cita_obj["apellido_1"], $cita_obj["apellido_2"]);
				$telefonos = $cita_obj["telefono_contacto"];
				$nombre_tipo_cita = $cita_obj["nombre_tipo_cita"];
				$nombre_estado = $cita_obj["nombre_estado_cita"];
				
				//Se obtienen los estados a los que puede ir la cita
				$lista_estados = $dbCitas->get_lista_estados_mover_cita_atencion($cita_obj["id_estado_cita"]);
			}
   		?>
        <input type="hidden" id="hdd_id_admision" value="<?php echo($id_admision); ?>" />
        <input type="hidden" id="hdd_id_cita" value="<?php echo($id_cita); ?>" />
        <table style="width: 500px; margin: auto; font-size: 10pt;">
            <tr>
                <td align="right" style="width:40%">Tipo de documento:</td>
                <td align="left" style="width:60%"><b><?php echo($tipo_documento); ?></b></td>
            </tr>
            <tr>
                <td align="right">N&uacute;mero de identificaci&oacute;n:</td>
                <td align="left"><b><?php echo($numero_documento); ?></b></td>
            </tr>
            <tr>
                <td align="right">Nombre completo:</td>
                <td align="left"><b><?php echo($nombre_completo); ?></b></td>
            </tr>
            <tr>
                <td align="right">Tel&eacute;fonos:</td>
                <td align="left"><b><?php echo($telefonos); ?></b></td>
            </tr>
            <tr>
                <td align="right">Tipo de cita:</td>
                <td align="left"><b><?php echo($nombre_tipo_cita); ?></b></td>
            </tr>
            <tr>
                <td align="right">Estado de la atenci&oacute;n:</td>
                <td align="left"><b><?php echo($nombre_estado); ?></b></td>
            </tr>
            <?php
				if ($ind_cambio_estado == 1) {
					//Se permite cambio de estado de atenciones
			?>
            <tr>
                <td align="right">Mover a:</td>
                <td align="left">
                	<?php
						if ($id_admision > 0) {
	                    	$combo->getComboDb("cmb_estado_atencion", "", $lista_estados, "id_estado_atencion, nombre_estado", "--Seleccione--", "seleccionar_estado_atencion(this.value);", true, "width:200px;", "", "no-margin");
						} else {
							$combo->getComboDb("cmb_estado_atencion", "", $lista_estados, "id_detalle, nombre_detalle", "--Seleccione--", "seleccionar_estado_atencion(this.value);", true, "width:200px;", "", "no-margin");
						}
					?>
                </td>
            </tr>
            <tr>
                <td align="right">Profesional que atiende:</td>
                <td align="left">
                    <div id="d_usuario_prof">
                		<?php
							$combo->getComboDb("cmb_usuario_prof", "", array(), "id, nombre", "--Seleccione--", "", false, "width:200px;", "", "no-margin");
						?>
                    </div>
                </td>
            </tr>
            <tr>
                <td align="right">Sede:</td>
                <td align="left">
               		<?php
						//Listado de sedes
						$lista_sedes = $dbListas->getListaDetalles(12, 1);
						$combo->getComboDb("cmb_lugares_citas", "", $lista_sedes, "id_detalle, nombre_detalle", "--Seleccione--", "", false, "width:200px;", "", "no-margin");
					?>
                </td>
            </tr>
            <?php
	            	if ($tipo_acceso_menu == "2") {
			?>
            <tr>
            	<td align="center" colspan="2">
                    <?php
						$tipo_cambio = "C";
                    	if ($id_admision > 0) {
							$tipo_cambio = "A";
						}
					?>
                	<input type="button" id="btn_mover_atencion" value="Mover atenci&oacute;n" class="btnPrincipal" onclick="mover_atencion('<?php echo($tipo_cambio); ?>');" />
                    <div id="d_mover_admision"></div>
                </td>
            </tr>
            <?php
					}
				}
				
				//Registros con admisión
				if ($id_admision > 0) {
			?>
            <tr style="height:10px;"></tr>
            <tr>
            	<td align="center" colspan="2">
                	<table class="paginated modal_table" style="width: 95%; margin: auto;">
                        <thead>
                            <tr class='headegrid'>
                                <th class="headegrid" align="center" style="width:10%;"></th>	
                                <th class="headegrid" align="center" style="width:45%;">Convenio</th>
                                <th class="headegrid" align="center" style="width:45%;">Plan</th>
                            </tr>
                        </thead>
                        <?php
                        	//Se obtiene el listado de convenios y planes de la admisión
							$lista_convenios_planes = $dbAdmision->get_lista_convenios_planes_admision($id_admision);
							if (count($lista_convenios_planes) > 0) {
						?>
                        <input type="hidden" id="hdd_cantidad_cp" value="<?php echo(count($lista_convenios_planes)); ?>" />
                        <?php
								foreach ($lista_convenios_planes as $i => $convenio_plan_aux) {
									$checked_aux = "";
									if ($convenio_plan_aux["id_convenio"] == $admision_obj["id_convenio"] && $convenio_plan_aux["id_plan"] == $admision_obj["id_plan"]) {
										$checked_aux = 'checked="checked"';
									}
						?>
                        <tr>
                        	<td align="center">
                            	<input type="checkbox" id="chk_seleccionado_cp_<?php echo($i); ?>" class="no-margin" <?php echo($checked_aux); ?> onchange="seleccionar_convenio_plan(<?php echo($i); ?>);" />
                                <input type="hidden" id="hdd_id_convenio_<?php echo($i); ?>" value="<?php echo($convenio_plan_aux["id_convenio"]); ?>" />
                                <input type="hidden" id="hdd_id_plan_<?php echo($i); ?>" value="<?php echo($convenio_plan_aux["id_plan"]); ?>" />
                            </td>
                            <td align="left"><?php echo($convenio_plan_aux["nombre_convenio"]); ?></td>
                            <td align="left"><?php echo($convenio_plan_aux["nombre_plan"]); ?></td>
                        </tr>
                        <?php
								}
								
	            				if ($tipo_acceso_menu == "2") {
						?>
                        <tr style="height:10px;"></tr>
                        <tr>
                            <td align="center" colspan="3">
                                <input type="button" id="btn_cambiar_convenio_plan" value="Guardar convenio/plan" class="btnPrincipal" onclick="guardar_convenio_plan();" />
                                <div id="d_cambiar_convenio_plan"></div>
                            </td>
                        </tr>
                        <?php
								}
							} else {
						?>
                        <tr>
                        	<td align="left" colspan="3">No se encontraron pagos asociados</td>
                        </tr>
                        <?php
							}
						?>
                    </table>
                </td>
            </tr>
            <?php
				}
			?>
        </table>
        <br />
        <?php
			break;
			
		case "3": //Registrar el cambio de estado
			$id_usuario = $_SESSION["idUsuario"];
			@$id_admision = intval($_POST["id_admision"], 10);
			@$id_cita = intval($_POST["id_cita"], 10);
			@$id_estado_atencion = $utilidades->str_decode($_POST["id_estado_atencion"]);
			@$id_usuario_prof = $utilidades->str_decode($_POST["id_usuario_prof"]);
			@$id_lugar_cita = $utilidades->str_decode($_POST["id_lugar_cita"]);
			@$tipo_cambio = $utilidades->str_decode($_POST["tipo_cambio"]);
			
			//Se registra el cambio de estado
			if ($id_admision > 0) {
				$resultado = $dbAdmision->guardar_cambio_estado_admision($id_admision, $id_estado_atencion, $id_usuario_prof, $id_lugar_cita, $id_usuario);
			} else {
				$resultado = $dbCitas->guardar_cambio_estado_cita($id_cita, $id_estado_atencion, $id_usuario);
			}
		?>
        <input type="hidden" id="hdd_resul_cambio_estado" value="<?php echo($resultado); ?>" />
        <?php
			break;
			
		case "4": //Combo de usuarios profesionales
			@$id_admision = intval($_POST["id_admision"], 10);
			@$id_cita = intval($_POST["id_cita"], 10);
			@$id_estado_atencion = $utilidades->str_decode($_POST["id_estado_atencion"]);
			
			if ($id_admision > 0) {
				$admision_obj = $dbAdmision->get_admision($id_admision);
				$id_tipo_cita = $admision_obj["id_tipo_cita"];
				
				//Se cargan los datos del detalle de tipo de cita correspondiente al estado de atención
				$tipo_cita_detalle_obj = $dbTiposCitasDetalle->get_tipo_cita_detalle_estado_atencion($id_tipo_cita, $id_estado_atencion);
				$id_menu = $tipo_cita_detalle_obj["id_menu"];
				
				//Se buscan los datos del usuario base
				$usuario_obj = $dbUsuarios->getUsuarioEstadoAtencion($id_admision, $id_estado_atencion);
				$id_usuario_prof = $usuario_obj["id_usuario"];
				
				//Se carga el listado de usuarios con disponibilidad y permisos para el estado de atención
				$lista_usuarios = $dbUsuarios->getListaUsuariosMenuDisponibilidad($id_menu, $id_usuario_prof, $admision_obj["id_lugar_cita"]);
				
				foreach($lista_usuarios as $usuario_aux) {
			?>
            <input type="hidden" id="hdd_lugar_cita_<?php echo($usuario_aux["id_usuario"]); ?>" value="<?php echo($usuario_aux["id_lugar_disp"]); ?>" />
            <?php
				}
				
				$combo->getComboDb("cmb_usuario_prof", $id_usuario_prof, $lista_usuarios, "id_usuario, nombre_completo", "--Seleccione--", "seleccionar_usuario_prof();", true, "width:200px;", "", "no-margin");
			} else {
				$combo->getComboDb("cmb_usuario_prof", "", array(), "id_usuario, nombre_completo", "--Seleccione--", "", false, "width:200px;", "", "no-margin");
			}
			break;
			
		case "5": //Registrar el cambio de convenio/plan
			$id_usuario = $_SESSION["idUsuario"];
			@$id_admision = intval($_POST["id_admision"], 10);
			@$id_convenio = intval($_POST["id_convenio"], 10);
			@$id_plan = intval($_POST["id_plan"], 10);
			
			//Se registra el cambio de convenio/plan
			$resultado = $dbAdmision->guardar_cambio_convenio_plan_admision($id_admision, $id_convenio, $id_plan, $id_usuario);
		?>
        <input type="hidden" id="hdd_resul_cambio_convenio_plan" value="<?php echo($resultado); ?>" />
        <?php
			break;
	}
?>
