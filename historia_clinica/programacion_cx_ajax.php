<?php session_start();
	/*
	  Pagina para la programación de cirugías
	  Autor: Feisar Moreno - 21/11/2017
	 */
	
	//header("Content-Type: text/xml; charset=UTF-8");
	
	require_once("../db/DbProgramacionCx.php");	
	require_once("../db/DbPacientes.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbPaises.php");
	require_once("../db/DbDepartamentos.php");
	require_once("../db/DbDepMuni.php");
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbConvenios.php");
	require_once("../db/DbAdmision.php");
	require_once("../db/DbPrecios.php");
	require_once("../db/DbCitas.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/Utilidades.php");
	
	$dbProgramacionCx = new DbProgramacionCx();
	$dbPacientes = new DbPacientes();
	$dbListas = new DbListas();
	$dbPaises = new DbPaises();
	$dbDepartamentos = new DbDepartamentos();
	$dbDepMuni = new DbDepMuni();
	$dbUsuarios = new DbUsuarios();
	$dbConvenios = new DbConvenios();
	$dbAdmision = new DbAdmision();
	$dbPrecios = new DbPrecios();
	$dbCitas = new DbCitas();
	
	$funciones_persona = new FuncionesPersona();
	$utilidades = new Utilidades();
	$combo = new Combo_Box();
	$contenido = new ContenidoHtml();
	
	$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);
	
	$opcion = $utilidades->str_decode($_POST["opcion"]);
	
	function ver_programaciones_cx($id_paciente) {
		$dbPacientes = new DbPacientes();
		$dbProgramacionCx = new DbProgramacionCx();
		$funciones_persona = new FuncionesPersona();
		
		$id_usuario = $_SESSION["idUsuario"];
		@$credencial = $_POST["credencial"];
		@$id_menu = $_POST["hdd_numero_menu"];
		
		$paciente_obj = $dbPacientes->getExistepaciente3($id_paciente);
		$lista_programacion_cx = $dbProgramacionCx->getListaProgramacionCxPaciente($id_paciente);
?>
<fieldset>
	<legend>Datos del paciente:</legend>
	<table border="0" style="width: 500px; margin: auto; font-size: 10pt;">
		<tr>
			<td align="right" style="width:35%;">
				Tipo de documento*:
			</td>
			<td align="left" style="width:65%;">
				<b><?php echo($paciente_obj["tipodocumento"]); ?></b>
			</td>
		</tr>
		<tr>
			<td align="right">
				N&uacute;mero de identificaci&oacute;n*:
			</td>
			<td align="left">
                <b><?php echo($paciente_obj["numero_documento"]); ?></b>
			</td>
		</tr>
		<tr>
			<td align="right">
				Nombre completo:
			</td>
			<td align="left">
				<b><?php echo($funciones_persona->obtenerNombreCompleto($paciente_obj["nombre_1"], $paciente_obj["nombre_2"], $paciente_obj["apellido_1"], $paciente_obj["apellido_2"])); ?></b>
			</td>
		</tr>
        <tr>
			<td align="right">
				Edad:
			</td>
			<td align="left">
               	<?php
					$arr_edad = explode("/", $paciente_obj["edad"]);
					$unidad_edad = "";
					switch ($arr_edad[1]) {
						case "1":
							$unidad_edad = "a&ntilde;os";
							break;
						case "2":
							$unidad_edad = "meses";
							break;
						case "3":
							$unidad_edad = "d&iacute;as";
							break;
					}
                ?>
                <b><?php echo($arr_edad[0]." ".$unidad_edad); ?></b>
			</td>
        </tr>
		<tr>
			<td align="right">
				Direcci&oacute;n*:
			</td>
			<td align="left">
				<b><?php echo($paciente_obj["direccion"]); ?></b>
			</td>
		</tr>
		<tr>
			<td align="right">
				Pa&iacute;s*:
			</td>
			<td align="left">
                <b><?php echo($paciente_obj["nombre_pais"]); ?></b>
			</td>
		</tr>
		<tr>
			<td align="right">
				Departamento / Estado*:
			</td>
			<td align="left">
				<?php
					$departamento_aux = "";
					if (trim($paciente_obj["nom_dep_t"]) != "") {
						$departamento_aux = $paciente_obj["nom_dep_t"];
					} else {
						$departamento_aux = $paciente_obj["nom_dep"];
					}
				?>
                <b><?php echo($departamento_aux); ?></b>
			</td>
		</tr>
		<tr>
			<td align="right">
				Municipio*:
			</td>
			<td align="left">
				<?php
					$municipio_aux = "";
					if (trim($paciente_obj["nom_mun_t"]) != "") {
						$municipio_aux = $paciente_obj["nom_mun_t"];
					} else {
						$municipio_aux = $paciente_obj["nom_mun"];
					}
				?>
                <b><?php echo($municipio_aux); ?></b>
			</td>
		</tr>
		<tr>
			<td align="right">
				Tel&eacute;fono(s)*:
			</td>
			<td align="left">
				<?php
					$telefono_aux = $paciente_obj["telefono_1"];
					if (trim($paciente_obj["telefono_2"]) != "") {
						$telefono_aux .= " - ".$paciente_obj["telefono_2"];
					}
				?>
                <b><?php echo($telefono_aux); ?></b>
			</td>
		</tr>
	</table>
</fieldset>
<div id="d_lista_prog_cx_paciente">
    <table class="modal_table" style="width: 95%; margin: auto;">
        <thead>
            <tr>
                <th class="th_reducido" align="center" style="width:15%;">Fecha/hora</th>
                <th class="th_reducido" align="center" style="width:20%;">Profesional</th>
                <th class="th_reducido" align="center" style="width:50%;">Procedimientos/Productos</th>
                <th class="th_reducido" align="center" style="width:15%;">Estado</th>
            </tr>
        </thead>
        <?php
			if (count($lista_programacion_cx) > 0) {
				foreach ($lista_programacion_cx as $prog_aux) {
					$id_prog_cx = $prog_aux["id_prog_cx"];
		?>
        <tr onclick="mostrar_editar_prog_cx(<?php echo($id_prog_cx); ?>);">
            <td class="td_reducido" align="center" valign="middle">
                <?php echo($prog_aux["fecha_prog_t"]); ?>
            </td>
            <td class="td_reducido" align="left" valign="middle">
                <?php echo($prog_aux["nombre_usuario_prof"]." ".$prog_aux["apellido_usuario_prof"]); ?>
            </td>
            <td class="td_reducido" align="left" valign="middle">
                <?php
					//Se carga el detalle de la programación
					$lista_prog_cx_det = $dbProgramacionCx->getListaProgramacionCxDet($id_prog_cx);
					
					if (count($lista_prog_cx_det) > 0) {
				?>
                <ul>
                    <?php
						foreach ($lista_prog_cx_det as $prog_det_aux) {
							$nombre_elemento_aux = "";
							switch($prog_det_aux["tipo_elemento"]) {
								case "P":
									$nombre_elemento_aux = $prog_det_aux["nombre_procedimiento"];
									break;
								case "M":
									$nombre_elemento_aux = $prog_det_aux["nombre_comercial"]." (".$prog_det_aux["nombre_generico"].")";
									break;
								case "I":
									$nombre_elemento_aux = $prog_det_aux["nombre_insumo"];
									break;
							}
					?>
                    <li><?php echo($nombre_elemento_aux); ?></li>
                    <?php
						}
					?>
                </ul>
                <?php
					} else {
						echo("-");
					}
				?>
            </td>
            <td class="td_reducido" align="center" valign="middle">
                <?php echo($prog_aux["estado_prog"]); ?>
            </td>
        </tr>
        <?php
				}
			} else {
				//Si no se encontraron registros
		?>
        <tr>
            <td colspan="4">
                <div class="msj-vacio">
                    <p>No hay registros de programaci&oacute;n de cirug&iacute;as para este paciente</p>
                </div>
            </td>
        </tr>
        <?php
			}
		?>
    </table>
</div>
<div id="d_programacion_cx_paciente" style="display:none;"></div>
<br />
<?php
	}
	
	function ver_programacion_cx_det($id_prog_cx) {
		$dbProgramacionCx = new DbProgramacionCx();
		$combo = new Combo_Box();
		
		//Se obtienen los datos del detalle de la programación
		if ($id_prog_cx != "") {
			$lista_programacion_cx_det = $dbProgramacionCx->getListaProgramacionCxDet($id_prog_cx);
		} else {
			$lista_programacion_cx_det = array();
		}
?>
<input type="hidden" id="hdd_cant_productos" name="hdd_cant_productos" value="<?php echo(count($lista_programacion_cx_det)); ?>" />
<table class="modal_table">
    <tr id="tabla_encabezado" style="cursor:default;">
        <th style="width:65%;">Procedimiento/Producto</th>
        <th style="width:15%;">Tipo</th>
        <th style="width:10%;">Cantidad</th>
        <th style="width:10%;" id="icono">
            <div class="Add-icon full" onclick="agregar_elemento();" title="Agregar elemento"></div>
        </th>
    </tr>
    <?php
		//Lista de bilateralidades
		$arr_bilateralidades = array();
		$arr_bilateralidades[0]["id"] = 0;
		$arr_bilateralidades[0]["valor"] = "No aplica";
		$arr_bilateralidades[1]["id"] = 1;
		$arr_bilateralidades[1]["valor"] = "Unilateral";
		$arr_bilateralidades[2]["id"] = 2;
		$arr_bilateralidades[2]["valor"] = "Bilateral";
		
        for ($i = 0; $i < 20; $i++) {
			if (isset($lista_programacion_cx_det[$i])) {
				$prog_cx_det_aux = $lista_programacion_cx_det[$i];
				$visible_det = "table-row";
				$tipo_elemento = $prog_cx_det_aux["tipo_elemento"];
				switch ($tipo_elemento) {
					case "P":
						$cod_elemento = $prog_cx_det_aux["cod_procedimiento"];
						$nombre_elemento = $prog_cx_det_aux["nombre_procedimiento"];
						break;
					case "M":
						$cod_elemento = $prog_cx_det_aux["cod_medicamento"];
						$nombre_elemento = $prog_cx_det_aux["nombre_comercial"]." (".$prog_cx_det_aux["nombre_generico"]." ) - ".$prog_cx_det_aux["presentacion"];
						break;
					case "I":
						$cod_elemento = $prog_cx_det_aux["cod_insumo"];
						$nombre_elemento = $prog_cx_det_aux["nombre_insumo"];
						break;
					default:
						$cod_elemento = "";
						$nombre_elemento = "";
						break;
				}
				$tipo_bilateral = $prog_cx_det_aux["tipo_bilateral"];
				$cantidad = $prog_cx_det_aux["cantidad"];
				$id_tipo_insumo = $prog_cx_det_aux["id_tipo_insumo_p"];
				if ($id_tipo_insumo == "3") {
					$visible_det_val = "table-row";
					
					//Se cargan los valores asociados al detalle
					$lista_prog_cx_det_val = $dbProgramacionCx->getListaProgramacionCxDetValores($prog_cx_det_aux["id_prog_cx_det"]);
				} else {
					$visible_det_val = "none";
					$lista_prog_cx_det_val = array();
				}
			} else {
				$visible_det = "none";
				$tipo_elemento = "";
				$cod_elemento = "";
				$nombre_elemento = "";
				$tipo_bilateral = "-1";
				$cantidad = "1";
				$id_tipo_insumo = "";
				$visible_det_val = "none";
				$lista_prog_cx_det_val = array();
			}
	?>
    <tr id="tr_producto_<?php echo($i); ?>" style="display:<?php echo($visible_det); ?>;">
        <td align="left" valign="middle">
            <input type="hidden" id="hdd_tipo_producto_<?php echo($i); ?>" name="hdd_tipo_producto_<?php echo($i); ?>" value="<?php echo($tipo_elemento); ?>" />
            <input type="hidden" id="hdd_cod_producto_<?php echo($i); ?>" name="hdd_cod_producto_<?php echo($i); ?>" value="<?php echo($cod_elemento); ?>" />
            <span id="sp_producto_<?php echo($i); ?>"><?php echo($nombre_elemento); ?></span>
        </td>
        <td align="center" valign="middle">
            <?php
				$combo->getComboDb("cmb_tipo_bilateral_".$i, $tipo_bilateral, $arr_bilateralidades, "id,valor", "-Seleccione-", "", 1, "width:100%;", "", "no-margin");
			?>
        </td>
        <td align="center" valign="middle">
            <input type="text" name="txt_cantidad_producto_<?php echo($i); ?>" id="txt_cantidad_producto_<?php echo($i); ?>" value="<?php echo($cantidad); ?>" class="texto_centrado no-margin" style="width:60px;" maxlength="3" onkeypress="return solo_numeros(event, false);" onchange="agregar_filas_tabla_det_val(<?php echo($i); ?>, this.value);" />
        </td>
        <td align="center" valign="middle">
            <div class="Error-icon" onclick="borrar_elemento(<?php echo($i); ?>);" title="Borrar elemento"></div>
        </td>
    </tr>
    <tr id="tr_producto_val_<?php echo($i); ?>" style="display:<?php echo($visible_det_val); ?>;">
        <td align="center" colspan="4">
        	<?php
            	$cantidad_det_val_aux = count($lista_prog_cx_det_val);
				if ($cantidad_det_val_aux == 0) {
					$cantidad_det_val_aux = 1;
				}
				
				for ($j = 0; $j < $cantidad_det_val_aux; $j++) {
					if (isset($lista_prog_cx_det_val[$j])) {
						$prog_cx_det_val_aux = $lista_prog_cx_det_val[$j];
						$tipo_lente_aux = $prog_cx_det_val_aux["tipo_lente"];
						$serial_lente_aux = $prog_cx_det_val_aux["serial_lente"];
						$poder_lente_aux = $prog_cx_det_val_aux["poder_lente"];
					} else {
						$tipo_lente_aux = "";
						$serial_lente_aux = "";
						$poder_lente_aux = "";
					}
			?>
            <table id="tbl_productos_val_<?php echo($i); ?>" class="modal_table" style="width:66%;">
                <tr id="tr_det_val_<?php echo($i."_".$j); ?>">
                    <td align="center">1</td>
                    <td align="right">&nbsp;Tipo:&nbsp;</td>
                    <td align="center">
                        <input type="text" id="txt_tipo_lente_<?php echo($i."_".$j); ?>" name="txt_tipo_lente_<?php echo($i."_".$j); ?>" value="<?php echo($tipo_lente_aux); ?>" maxlength="50" class="no-margin" style="width:100%;" onblur="trim(this.value);" />
                    </td>
                    <td align="right">&nbsp;Serial:&nbsp;</td>
                    <td align="center">
                        <input type="text" id="txt_serial_lente_<?php echo($i."_".$j); ?>" name="txt_serial_lente_<?php echo($i."_".$j); ?>" value="<?php echo($serial_lente_aux); ?>" maxlength="50" class="no-margin" style="width:100%;" onblur="trim(this.value);" />
                    </td>
                    <td align="right">&nbsp;Poder:&nbsp;</td>
                    <td align="center">
                        <input type="text" id="txt_poder_lente_<?php echo($i."_".$j); ?>" name="txt_poder_lente_<?php echo($i."_".$j); ?>" value="<?php echo($poder_lente_aux); ?>" maxlength="10" class="no-margin" style="width:70px;" onblur="trim(this.value);" />
                    </td>
                </tr>
            </table>
            <?php
				}
			?>
        </td>
    </tr>
    <?php
		}
	?>
</table>
<?php
	}
	
	switch ($opcion) {
		case "1": //Consultar HC del paciente
			@$paciente_hc = $utilidades->str_decode($_POST["paciente_hc"]);
			@$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
			
			if ($paciente_hc != "") {
				$lista_pacientes = $dbPacientes->getListaPacientesBusc($paciente_hc, 50);
			} else {
				$paciente_obj = $dbPacientes->getExistepaciente3($id_paciente);
				$lista_pacientes = array();
				array_push($lista_pacientes, $paciente_obj);
			}
			if (count($lista_pacientes) == 1) { //Si se encontro un solo registro
				ver_programaciones_cx($lista_pacientes[0]["id_paciente"]);
			} else if(count($lista_pacientes) > 1) {
		?>
        <table class="paginated modal_table" style="width:60%; margin:auto;">
        	<thead>
            	<tr class="headegrid">
                	<th class="headegrid" align="center" style="width:25%;">Documento</th>
                    <th class="headegrid" align="center" style="width:75%">Pacientes</th>
                </tr>
            </thead>
			<?php
				foreach ($lista_pacientes as $paciente_aux) {
					$id_paciente = $paciente_aux["id_paciente"];
					$cod_tipo_documento = $paciente_aux["cod_tipo_documento"];
					$numero_documento = $paciente_aux["numero_documento"];
					$nombre_1 = $paciente_aux["nombre_1"];
					$nombre_2 = $paciente_aux["nombre_2"];
					$apellido_1 = $paciente_aux["apellido_1"];
					$apellido_2 = $paciente_aux["apellido_2"];
					$nombre_completo = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
			?>
            <tr class="celdagrid" onclick="ver_registro_prog_cx_paciente(<?php echo($id_paciente);?>);">
            	<td align="left"><?php echo($cod_tipo_documento." ".$numero_documento); ?></td>
                <td align="left"><?php echo($nombre_completo); ?></td>
            </tr>
            <?php
				}
			?>
        </table>
        <script id="ajax">
			//<![CDATA[ 
			$(function() {
			    $(".paginated", "tabla_persona_hc").each(function(i) {
			        $(this).text(i + 1);
			    });
				
			    $("table.paginated").each(function() {
			        var currentPage = 0;
			        var numPerPage = 5;
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
		<?php
			} else if (count($lista_pacientes) == 0) {
		?>
        <div class="msj-vacio">
        	<p>No se encontraron pacientes</p>
        </div>
        <?php
			}
			break;		
			
		case "2": //Carga de ficha de paciente
			$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
			ver_programaciones_cx($id_paciente);
			break;
			
		case "3": //Formulario de creación de programación
			$id_usuario = $_SESSION["idUsuario"];
			
			$id_pais = "1";
			$cod_dep = "68";
			
			$lista_tipos_documento = $dbListas->getListaDetalles(2);
			$lista_paises = $dbPaises->getPaises();
			$lista_departamentos = $dbDepartamentos->getDepartamentos();
			$lista_municipios = $dbDepMuni->getMunicipiosDepartamento($cod_dep);
			$lista_sexos = $dbListas->getTipoSexo();
		?>
        <fieldset>
            <legend>Datos del paciente:</legend>
            <table border="0" style="width:100%; margin: auto; font-size: 10pt;">
                <tr>
                    <td align="left" style="width:25%;">Tipo de documento*:</td>
                    <td align="left" style="width:25%;">N&uacute;mero de identificaci&oacute;n*:</td>
                    <td align="left" style="width:25%;"></td>
                    <td align="left" style="width:25%;"></td>
                </tr>
                <tr>
                    <td align="left">
	                    <input type="hidden" id="hdd_id_paciente" name="hdd_id_paciente" value="" />
                        <?php
							$combo->getComboDb("cmb_tipo_documento", "", $lista_tipos_documento, "id_detalle, nombre_detalle", "-Seleccione-", "", true, "width: 100%;");
						?>
                    </td>
                    <td align="left">
                        <input type="text" id="txt_numero_documento" name="txt_numero_documento" value="" maxlength="20" style="width:100%;" onchange="limpiar_id_paciente();" onblur="trim_cadena(this); verificar_paciente(this.value);" />
                        <div id="d_val_paciente" style="display:none;"></div>
                    </td>
                </tr>
                <tr>
                    <td align="left" style="width:25%;">Primer nombre*</td>
                    <td align="left" style="width:25%;">Segundo nombre</td>
                    <td align="left" style="width:25%;">Primer apellido*</td>
                    <td align="left" style="width:25%;">Segundo apellido</td>
                </tr>
                <tr>
                    <td align="left">
                        <input type="text" id="txt_nombre_1" value="" maxlength="100" style="width:100%;" onblur="trim_cadena(this);" />
                    </td>
                    <td align="left">
                        <input type="text" id="txt_nombre_2" value="" maxlength="100" style="width:100%;" onblur="trim_cadena(this);" />
                    </td>
                    <td align="left">
                        <input type="text" id="txt_apellido_1" value="" maxlength="100" style="width:100%;" onblur="trim_cadena(this);" />
                    </td>
                    <td align="left">
                        <input type="text" id="txt_apellido_2" value="" maxlength="100" style="width:100%;" onblur="trim_cadena(this);" />
                    </td>
                </tr>
                <tr>
                    <td align="left">G&eacute;nero*:</td>
                    <td align="left">Fecha de nacimiento*:</td>
                    <td align="left">Tipo de Sangre*:</td>
                    <td align="left">Factor RH*:</td>
                </tr>
                <tr>
                    <td align="left">
                        <?php
                        	$combo->getComboDb("cmb_sexo", "", $lista_sexos, "id_detalle, nombre_detalle", "-Seleccione-", "", "", "width:100%;");
						?>
                    </td>
                    <td align="left">
                        <input type="text" class="input required" name="txt_fecha_nacimiento" id="txt_fecha_nacimiento" value="" maxlength="10" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" style="width:120px;" />
                    </td>
                    <td align="left">
                        <?php $combo->getComboDb("cmb_tipo_sangre", "", $dbListas->getListaDetalles(7), "id_detalle, nombre_detalle", "-Seleccione-", "", "", "width:100%;", ""); ?>
                    </td>
                    <td align="left">
                        <?php $combo->getComboDb("cmb_factor_rh", "", $dbListas->getListaDetalles(9), "id_detalle, nombre_detalle", "-Seleccione-", "", "", "width:100%;", ""); ?>
                    </td>
                </tr>
                <tr>
                    <td align="left">Pa&iacute;s de nacimiento*:</td>
                    <td align="left" id="td_dep_col_nac" style="display:table-cell;">Departamento de nacimiento*:</td>
                    <td align="left" id="td_mun_col_nac" style="display:table-cell;">Municipio de nacimiento*:</td>
                    <td align="left" id="td_dep_otro_nac" style="display:none;">Estado/regi&oacute;n de nacimiento*:</td>
                    <td align="left" id="td_mun_otro_nac" style="display:none;">Municipio de nacimiento*:</td>
                </tr>
                <tr>
                    <td align="left">
                        <?php
							$combo->getComboDb("cmb_pais_nac", $id_pais, $lista_paises, "id_pais,nombre_pais", "-Seleccione-", "seleccionar_pais(this.value, 'nac');", 1, "width:100%;");
						?>
                    </td>
                    <td align="left" id="td_dep_col_val_nac" style="display:table-cell;">
                        <?php
							$combo->getComboDb("cmb_cod_dep_nac", $cod_dep, $lista_departamentos, "cod_dep,nom_dep", "-Seleccione-", "seleccionar_departamento(this.value, '', 'nac');", 1, "width:100%;");
						?>
                    </td>
                    <td align="left" id="td_mun_col_val_nac" style="display:table-cell;">
                        <div id="d_municipio_nac">
                            <?php
								$combo->getComboDb("cmb_cod_mun_nac", "", $lista_municipios, "cod_mun_dane,nom_mun", "-Seleccione-", "", 1, "width:100%;");
							?>
                        </div>
                    </td>
                    <td align="left" id="td_dep_otro_val_nac" style="display:none;">
                        <input type="text" id="txt_nom_dep_nac" value="" maxlength="50" style="width:100%;" onblur="trim(this.value);" />
                    </td>
                    <td align="left" id="td_mun_otro_val_nac" style="display:none;">
                        <input type="text" id="txt_nom_mun_nac" value="" maxlength="50" style="width:100%;" onblur="trim(this.value);" />
                    </td>
                </tr>
                <tr>
                    <td align="left">Pa&iacute;s de residencia*:</td>
                    <td align="left" id="td_dep_col_res" style="display:table-cell;">Departamento de residencia*:</td>
                    <td align="left" id="td_mun_col_res" style="display:table-cell;">Municipio de residencia*:</td>
                    <td align="left" id="td_dep_otro_res" style="display:none;">Estado/regi&oacute;n de residencia*:</td>
                    <td align="left" id="td_mun_otro_res" style="display:none;">Municipio de residencia*:</td>
                    <td align="left">Zona de residencia*:</td>
                </tr>
                <tr>
                    <td align="left">
                        <?php
							$combo->getComboDb("cmb_pais_res", $id_pais, $lista_paises, "id_pais,nombre_pais", "-Seleccione-", "seleccionar_pais(this.value, 'res');", 1, "width:100%;");
						?>
                    </td>
                    <td align="left" id="td_dep_col_val_res" style="display:table-cell;">
                        <?php
							$combo->getComboDb("cmb_cod_dep_res", $cod_dep, $lista_departamentos, "cod_dep,nom_dep", "-Seleccione-", "seleccionar_departamento(this.value, '', 'res');", 1, "width:100%;");
						?>
                    </td>
                    <td align="left" id="td_mun_col_val_res" style="display:table-cell;">
                        <div id="d_municipio_res">
                            <?php
								$combo->getComboDb("cmb_cod_mun_res", "", $lista_municipios, "cod_mun_dane,nom_mun", "-Seleccione-", "", 1, "width:100%;");
							?>
                        </div>
                    </td>
                    <td align="left" id="td_dep_otro_val_res" style="display:none;">
                        <input type="text" id="txt_nom_dep_res" value="" maxlength="50" style="width:100%;" onblur="trim(this.value);" />
                    </td>
                    <td align="left" id="td_mun_otro_val_res" style="display:none;">
                        <input type="text" id="txt_nom_mun_res" value="" maxlength="50" style="width:100%;" onblur="trim(this.value);" />
                    </td>
                    <td align="left">
                        <?php
							$lista_zonas = $dbListas->getListaZona();
							$combo->getComboDb("cmb_zona", "", $lista_zonas, "id_detalle,nombre_detalle", "-Seleccione-", "", 1, "width:100%;");
						?>
                    </td>
                </tr>
                <tr>
                    <td align="left">Direcci&oacute;n*:</td>
                    <td align="left">e-mail*:</td>
                    <td align="left">Tel&eacute;fono 1*:</td>
                    <td align="left">Tel&eacute;fono 2:</td>
                </tr>
                <tr>
                    <td align="left">
                        <input type="text" id="txt_direccion" value="" maxlength="200" style="width:100%;" onblur="trim(this.value);" />
                    </td>
                    <td align="left">
                        <input type="text" id="txt_email" value="" maxlength="50" style="width:100%;" onblur="trim(this.value);" />
                    </td>
                    <td align="left">
                        <input type="text" id="txt_telefono_1" value="" maxlength="10" style="width:100%;" onkeypress="return solo_caracteres(event, '0123456789');" onblur="trim(this.value);" />
                    </td>
                    <td align="left">
                        <input type="text" id="txt_telefono_2" value="" maxlength="10" style="width:100%;" onkeypress="return solo_caracteres(event, '0123456789');" onblur="trim(this.value);" />
                    </td>
                </tr>
                <tr>
                    <td align="left">Profesi&oacute;n*:</td>
                    <td align="left">Estado civil*:</td>
                    <td align="left">Desplazado*:</td>
                    <td align="left">Etnia*:</td>
                </tr>
                <tr>
                    <td align="left">
                        <input type="text" id="txt_profesion" value="" maxlength="100" style="width:100%;" onblur="trim(this.value);" />
                    </td>
                    <td align="left">
                        <?php
							$lista_estados_civiles = $dbListas->getListaDetalles(40, 1);
							$combo->getComboDb("cmb_estado_civil", "", $lista_estados_civiles, "id_detalle,nombre_detalle", "-Seleccione-", "", 1, "width:100%;");
						?>
                    </td>
                    <td align="left">
                        <?php
							$lista_desplazados = $dbListas->getListaDesplazado();
							$combo->getComboDb("cmb_desplazado", "46", $lista_desplazados, "id_detalle,nombre_detalle", "-Seleccione-", "", 1, "width:100%;");
						?>
                    </td>
                    <td align="left">
                        <?php
							$lista_etnias = $dbListas->getListaEtnia();
							$combo->getComboDb("cmb_etnia", "21", $lista_etnias, "id_detalle,nombre_detalle", "-Seleccione-", "", 1, "width:100%;");
						?>
                    </td>
                </tr>
            </table>
        </fieldset>
        <fieldset>
            <legend>Datos de la programaci&oacute;n:</legend>
            <table border="0" style="width:100%; margin: auto; font-size: 10pt;">
                <tr>
                    <td align="left" style="width:25%;">Profesional*:</td>
                    <td align="left" style="width:25%;">Fecha*:</td>
                    <td align="left" style="width:25%;">Hora:</td>
                    <td align="left" style="width:25%;">Convenio*:</td>
                </tr>
                <tr>
                    <td align="left">
	                    <input type="hidden" id="hdd_id_paciente" name="hdd_id_paciente" value="" />
                        <?php
							$lista_usuarios_cx = $dbUsuarios->getListaUsuariosCirugia(1);
							$combo->getComboDb("cmb_usuario_prof", "", $lista_usuarios_cx, "id_usuario, nombre_completo", "-Seleccione-", "", true, "width: 100%;");
						?>
                    </td>
                    <td align="left">
                        <input type="text" id="txt_fecha_prog" name="txt_fecha_prog" class="input" value="" maxlength="10" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" style="width:120px;" />
                    </td>
                    <td align="left">
                    	<input type="text" id="txt_hora_prog" name="txt_hora_prog" class="input" value="" maxlength="5" style="width:65px;" onblur="validar_hora(this);" tabindex="" />
                    </td>
                    <td align="left">
                        <?php
							$lista_convenios = $dbConvenios->getListaConveniosActivos();
							$combo->getComboDb("cmb_convenio", "", $lista_convenios, "id_convenio, nombre_convenio", "-Seleccione-", "", true, "width: 100%;");
						?>
                    </td>
                </tr>
                <tr>
                	<td align="center" colspan="4">
                    	<?php
                        	ver_programacion_cx_det("");
						?>
                    </td>
                </tr>
                <tr style="height:10px;"></tr>
                <tr>
                	<td align="center" colspan="4">
                    	<?php
                        	if ($tipo_acceso_menu == "2") {
						?>
                    	<input type="button" id="btn_crear_prog_cx" name="btn_crear_prog_cx" value="Crear programaci&oacute;n" class="btnPrincipal" onclick="crear_prog_cx();" />
                        <?php
							}
						?>
                        <div id="d_crear_prog_cx" style="display:none;"></div>
                    </td>
                </tr>
            </table>
        </fieldset>
        <?php
			break;
			
		case "4": //Búsqueda de datos del paciente
			@$numero_documento = $utilidades->str_decode($_POST["numero_documento"]);
			
			//Se buscan los datos del paciente
			$paciente_obj = $dbPacientes->getPacienteNumeroDocumento($numero_documento);
			if (isset($paciente_obj["id_paciente"])) {
		?>
        <input type="hidden" id="hdd_id_paciente_b" name="hdd_id_paciente_b" value="<?php echo($paciente_obj["id_paciente"]); ?>" />
        <input type="hidden" id="hdd_id_tipo_documento_b" name="hdd_id_tipo_documento_b" value="<?php echo($paciente_obj["id_tipo_documento"]); ?>" />
        <input type="hidden" id="hdd_tipo_documento_b" name="hdd_tipo_documento_b" value="<?php echo($paciente_obj["tipodocumento"]); ?>" />
        <input type="hidden" id="hdd_numero_documento_b" name="hdd_numero_documento_b" value="<?php echo($paciente_obj["numero_documento"]); ?>" />
        <input type="hidden" id="hdd_nombre_1_b" name="hdd_nombre_1_b" value="<?php echo($paciente_obj["nombre_1"]); ?>" />
        <input type="hidden" id="hdd_nombre_2_b" name="hdd_nombre_2_b" value="<?php echo($paciente_obj["nombre_2"]); ?>" />
        <input type="hidden" id="hdd_apellido_1_b" name="hdd_apellido_1_b" value="<?php echo($paciente_obj["apellido_1"]); ?>" />
        <input type="hidden" id="hdd_apellido_2_b" name="hdd_apellido_2_b" value="<?php echo($paciente_obj["apellido_2"]); ?>" />
        <input type="hidden" id="hdd_sexo_b" name="hdd_sexo_b" value="<?php echo($paciente_obj["sexo"]); ?>" />
        <input type="hidden" id="hdd_fecha_nacimiento_b" name="hdd_fecha_nacimiento_b" value="<?php echo($paciente_obj["fecha_nacimiento_aux"]); ?>" />
        <input type="hidden" id="hdd_tipo_sangre_b" name="hdd_tipo_sangre_b" value="<?php echo($paciente_obj["tipo_sangre"]); ?>" />
        <input type="hidden" id="hdd_factor_rh_b" name="hdd_factor_rh_b" value="<?php echo($paciente_obj["factor_rh"]); ?>" />
        <input type="hidden" id="hdd_id_pais_nac_b" name="hdd_id_pais_nac_b" value="<?php echo($paciente_obj["id_pais_nac"]); ?>" />
        <input type="hidden" id="hdd_cod_dep_nac_b" name="hdd_cod_dep_nac_b" value="<?php echo($paciente_obj["cod_dep_nac"]); ?>" />
        <input type="hidden" id="hdd_cod_mun_nac_b" name="hdd_cod_mun_nac_b" value="<?php echo($paciente_obj["cod_mun_nac"]); ?>" />
        <input type="hidden" id="hdd_nom_dep_nac_b" name="hdd_nom_dep_nac_b" value="<?php echo($paciente_obj["nom_dep_nac"]); ?>" />
        <input type="hidden" id="hdd_nom_mun_nac_b" name="hdd_nom_mun_nac_b" value="<?php echo($paciente_obj["nom_mun_nac"]); ?>" />
        <input type="hidden" id="hdd_id_pais_b" name="hdd_id_pais_b" value="<?php echo($paciente_obj["id_pais"]); ?>" />
        <input type="hidden" id="hdd_cod_dep_b" name="hdd_cod_dep_b" value="<?php echo($paciente_obj["cod_dep"]); ?>" />
        <input type="hidden" id="hdd_cod_mun_b" name="hdd_cod_mun_b" value="<?php echo($paciente_obj["cod_mun"]); ?>" />
        <input type="hidden" id="hdd_nom_dep_b" name="hdd_nom_dep_b" value="<?php echo($paciente_obj["nom_dep"]); ?>" />
        <input type="hidden" id="hdd_nom_mun_b" name="hdd_nom_mun_b" value="<?php echo($paciente_obj["nom_mun"]); ?>" />
        <input type="hidden" id="hdd_id_zona_b" name="hdd_id_zona_b" value="<?php echo($paciente_obj["id_zona"]); ?>" />
        <input type="hidden" id="hdd_direccion_b" name="hdd_direccion_b" value="<?php echo($paciente_obj["direccion"]); ?>" />
        <input type="hidden" id="hdd_email_b" name="hdd_email_b" value="<?php echo($paciente_obj["email"]); ?>" />
        <input type="hidden" id="hdd_telefono_1_b" name="hdd_telefono_1_b" value="<?php echo($paciente_obj["telefono_1"]); ?>" />
        <input type="hidden" id="hdd_telefono_2_b" name="hdd_telefono_2_b" value="<?php echo($paciente_obj["telefono_2"]); ?>" />
        <input type="hidden" id="hdd_profesion_b" name="hdd_profesion_b" value="<?php echo($paciente_obj["profesion"]); ?>" />
        <input type="hidden" id="hdd_id_estado_civil_b" name="hdd_id_estado_civil_b" value="<?php echo($paciente_obj["id_estado_civil"]); ?>" />
        <input type="hidden" id="hdd_ind_desplazado_b" name="hdd_ind_desplazado_b" value="<?php echo($paciente_obj["ind_desplazado"]); ?>" />
        <input type="hidden" id="hdd_id_etnia_b" name="hdd_id_etnia_b" value="<?php echo($paciente_obj["id_etnia"]); ?>" />
        <?php
				//Se busca la última admisión del paciente para obtener el convenio
				$admision_obj = $dbAdmision->get_ultima_admision($paciente_obj["id_paciente"]);
				if (isset($admision_obj["id_admision"])) {
		?>
        <input type="hidden" id="hdd_id_convenio_b" name="hdd_id_convenio_b" value="<?php echo($admision_obj["id_convenio"]); ?>" />
        <?php
				} else {
		?>
        <input type="hidden" id="hdd_id_convenio_b" name="hdd_id_convenio_b" value="" />
        <?php
				}
			} else {
		?>
        <input type="hidden" id="hdd_id_paciente_b" name="hdd_id_paciente_b" value="0" />
        <?php
			}
			break;
			
		case "5": //Combo de municipios de un departamento
			@$cod_dep = $utilidades->str_decode($_POST["cod_dep"]);
			@$sufijo = $utilidades->str_decode($_POST["sufijo"]);
			
			$lista_municipios = $dbDepMuni->getMunicipiosDepartamento($cod_dep);
			$combo->getComboDb("cmb_cod_mun_".$sufijo, "", $lista_municipios, "cod_mun_dane,nom_mun", "-Seleccione-", "", 1, "width:100%;");
			break;
			
		case "6": //Formulario de búsqueda de elementos
	?>
    <div class="encabezado">
        <h3>Listado de productos y servicios</h3>
    </div>
    <br/>
    <table>
        <tr>
            <td>
                <input type="text" id="txt_elemento_b" name="txt_elemento_b" placeholder="C&oacute;digo o nombre del producto o servicio" style="width:350px;" />
            </td>
            <td>
                <input type="button" value="Buscar" class="btnPrincipal peq" onclick="buscar_elemento();" />
            </td>
        </tr>
    </table>
    <br/>
    <div id="d_elementos_b"></div>
    <?php
			break;
			
		case "7": //Listado de elementos buscados
			$parametro = $utilidades->str_decode($_POST["parametro"]);
			
			$lista_productos = $dbPrecios->getListaProductosBusq($parametro, 25);
		?>
        <div style="max-height:280px; overflow:auto;">
            <table class="modal_table" id="tablaPreciosModal" style="width:99%;" align="center">
                <tr id="tabla_encabezado">
                    <th style="width:20%;">C&oacute;digo</th>
                    <th style="width:80%;">Nombre</th>
                </tr>
                <?php
					if (count($lista_productos) > 0) {
						foreach ($lista_productos as $producto_aux) {
							$cod_servicio_aux = "";
							switch ($producto_aux["tipo_elemento"]) {
								case "P":
									$cod_servicio_aux = $producto_aux["cod_procedimiento"];
									break;
								case "M":
									$cod_servicio_aux = $producto_aux["cod_medicamento"];
									break;
								case "I":
									$cod_servicio_aux = $producto_aux["cod_insumo"];
									break;
							}
                ?>
                <tr onclick="seleccionar_producto_b('<?php echo($producto_aux["tipo_elemento"]); ?>', '<?php echo($cod_servicio_aux); ?>', '<?php echo($producto_aux["nombre_producto"]); ?>', '<?php echo($producto_aux["id_tipo_insumo_p"]); ?>');">
                    <td align="center" valign="middle">
        	           	<?php echo($cod_servicio_aux); ?>
                    </td>
                    <td align="left">
						<?php echo($producto_aux["nombre_producto"]); ?>
                    </td>
                </tr>
                <?php
            	    	}
					} else {
                ?>
                <tr>
                    <td align="center" colspan="2">
        	           	No se hallaron productos y servicios con el par&aacute;metro de b&uacute;squeda dado.
                    </td>
                </tr>
                <?php
					}
                ?>
            </table>
        </div>
        <br/>
        <?php
			break;
			
		case "8": //Creación de programación de cirugía
			$id_usuario = $_SESSION["idUsuario"];
			@$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
			@$id_tipo_documento = $utilidades->str_decode($_POST["id_tipo_documento"]);
			@$numero_documento = $utilidades->str_decode($_POST["numero_documento"]);
			@$nombre_1 = $utilidades->str_decode($_POST["nombre_1"]);
			@$nombre_2 = $utilidades->str_decode($_POST["nombre_2"]);
			@$apellido_1 = $utilidades->str_decode($_POST["apellido_1"]);
			@$apellido_2 = $utilidades->str_decode($_POST["apellido_2"]);
			@$sexo = $utilidades->str_decode($_POST["sexo"]);
			@$fecha_nacimiento = $utilidades->str_decode($_POST["fecha_nacimiento"]);
			@$tipo_sangre = $utilidades->str_decode($_POST["tipo_sangre"]);
			@$factor_rh = $utilidades->str_decode($_POST["factor_rh"]);
			@$id_pais_nac = $utilidades->str_decode($_POST["id_pais_nac"]);
			@$cod_dep_nac = $utilidades->str_decode($_POST["cod_dep_nac"]);
			@$cod_mun_nac = $utilidades->str_decode($_POST["cod_mun_nac"]);
			@$nom_dep_nac = $utilidades->str_decode($_POST["nom_dep_nac"]);
			@$nom_mun_nac = $utilidades->str_decode($_POST["nom_mun_nac"]);
			@$id_pais_res = $utilidades->str_decode($_POST["id_pais_res"]);
			@$cod_dep_res = $utilidades->str_decode($_POST["cod_dep_res"]);
			@$cod_mun_res = $utilidades->str_decode($_POST["cod_mun_res"]);
			@$nom_dep_res = $utilidades->str_decode($_POST["nom_dep_res"]);
			@$nom_mun_res = $utilidades->str_decode($_POST["nom_mun_res"]);
			@$id_zona = $utilidades->str_decode($_POST["id_zona"]);
			@$direccion = $utilidades->str_decode($_POST["direccion"]);
			@$email = $utilidades->str_decode($_POST["email"]);
			@$telefono_1 = $utilidades->str_decode($_POST["telefono_1"]);
			@$telefono_2 = $utilidades->str_decode($_POST["telefono_2"]);
			@$profesion = $utilidades->str_decode($_POST["profesion"]);
			@$id_estado_civil = $utilidades->str_decode($_POST["id_estado_civil"]);
			@$ind_desplazado = $utilidades->str_decode($_POST["ind_desplazado"]);
			@$id_etnia = $utilidades->str_decode($_POST["id_etnia"]);
			@$id_usuario_prof = $utilidades->str_decode($_POST["id_usuario_prof"]);
			@$fecha_prog = $utilidades->str_decode($_POST["fecha_prog"]);
			@$hora_prog = $utilidades->str_decode($_POST["hora_prog"]);
			@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
			@$cant_det = intval($_POST["cant_det"], 10);
			
			$arr_elementos = array();
			for ($i = 0; $i < $cant_det; $i++) {
				@$arr_elementos[$i]["tipo_elemento"] = $utilidades->str_decode($_POST["tipo_elemento_".$i]);
				@$arr_elementos[$i]["cod_elemento"] = $utilidades->str_decode($_POST["cod_elemento_".$i]);
				@$arr_elementos[$i]["tipo_bilateral"] = $utilidades->str_decode($_POST["tipo_bilateral_".$i]);
				@$arr_elementos[$i]["cantidad"] = intval($_POST["cantidad_".$i], 10);
				@$cant_det_val = intval($_POST["cant_det_val_".$i], 10);
				
				for ($j = 0; $j < $cant_det_val; $j++) {
					@$arr_elementos[$i]["det_val"][$j]["tipo_lente"] = $utilidades->str_decode($_POST["tipo_lente_".$i."_".$j]);
					@$arr_elementos[$i]["det_val"][$j]["serial_lente"] = $utilidades->str_decode($_POST["serial_lente_".$i."_".$j]);
					@$arr_elementos[$i]["det_val"][$j]["poder_lente"] = $utilidades->str_decode($_POST["poder_lente_".$i."_".$j]);
				}
			}
			
			$resultado = 0;
			if ($id_paciente != "") {
				$resultado = $dbPacientes->editar_paciente($id_paciente, $id_tipo_documento, $numero_documento, $nombre_1, $nombre_2, $apellido_1, $apellido_2,
						$sexo, $fecha_nacimiento, $tipo_sangre, $factor_rh, $id_pais_nac, $cod_dep_nac, $cod_mun_nac, $nom_dep_nac, $nom_mun_nac,
						$id_pais_res, $cod_dep_res, $cod_mun_res, $nom_dep_res, $nom_mun_res, $id_zona, $direccion, $email, $telefono_1, $telefono_2,
						$profesion, $id_estado_civil, $ind_desplazado, $id_etnia, $id_usuario, "","");
			} else {
				$id_paciente = $dbPacientes->crear_paciente($id_tipo_documento, $numero_documento, $nombre_1, $nombre_2, $apellido_1, $apellido_2,
						$sexo, $fecha_nacimiento, $tipo_sangre, $factor_rh, $id_pais_nac, $cod_dep_nac, $cod_mun_nac, $nom_dep_nac, $nom_mun_nac,
						$id_pais_res, $cod_dep_res, $cod_mun_res, $nom_dep_res, $nom_mun_res, $id_zona, $direccion, $email, $telefono_1, $telefono_2,
						$profesion, $id_estado_civil, $ind_desplazado, $id_etnia, $id_usuario, "","");
				
				$resultado = $id_paciente;
			}
		?>
        <input type="hidden" id="hdd_result_paciente_prog_cx" name="hdd_result_paciente_prog_cx" value="<?php echo($resultado); ?>" />
        <?php
			$id_prog_cx = 0;
			if ($resultado > 0) {
				$id_prog_cx = $dbProgramacionCx->crearEditarProgramacionCx("", $id_paciente, $id_usuario_prof, $fecha_prog, $hora_prog, 432, $id_convenio, "", "", "", "", "", "", $arr_elementos, $id_usuario);
			}
		?>
        <input type="hidden" id="hdd_result_crear_prog_cx" name="hdd_result_crear_prog_cx" value="<?php echo($id_prog_cx); ?>" />
        <?php
			break;
			
		case "9": //Formulario de edición de programación de cirugías
			@$id_prog_cx = $utilidades->str_decode($_POST["id_prog_cx"]);
			
			//Se obtienen los datos de la programación
			$programación_cx_obj = $dbProgramacionCx->getProgramacionCx($id_prog_cx);
		?>
        <fieldset>
            <legend>Datos de la programaci&oacute;n:</legend>
            <table border="0" style="width:100%; margin: auto; font-size: 10pt;">
                <tr>
                    <td align="left" style="width:25%;">Profesional*:</td>
                    <td align="left" style="width:25%;">Fecha*:</td>
                    <td align="left" style="width:25%;">Hora:</td>
                    <td align="left" style="width:25%;">Convenio*:</td>
                </tr>
                <tr>
                    <td align="left">
	                    <input type="hidden" id="hdd_id_prog_cx" name="hdd_id_prog_cx" value="<?php echo($id_prog_cx); ?>" />
	                    <input type="hidden" id="hdd_id_paciente" name="hdd_id_paciente" value="<?php echo($programación_cx_obj["id_paciente"]); ?>" />
                        <?php
							$lista_usuarios_cx = $dbUsuarios->getListaUsuariosCirugia(1);
							$combo->getComboDb("cmb_usuario_prof", $programación_cx_obj["id_usuario_prof"], $lista_usuarios_cx, "id_usuario, nombre_completo", "-Seleccione-", "", true, "width: 100%;");
						?>
                    </td>
                    <td align="left">
                        <input type="text" id="txt_fecha_prog" name="txt_fecha_prog" class="input" value="<?php echo($programación_cx_obj["fecha_prog_t"]); ?>" maxlength="10" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" style="width:120px;" />
                    </td>
                    <td align="left">
                    	<input type="text" id="txt_hora_prog" name="txt_hora_prog" class="input" value="<?php echo($programación_cx_obj["hora_prog_t"]); ?>" maxlength="5" style="width:65px;" onblur="validar_hora(this);" tabindex="" />
                    </td>
                    <td align="left">
                        <?php
							$lista_convenios = $dbConvenios->getListaConveniosActivos();
							
							$combo->getComboDb("cmb_convenio", $programación_cx_obj["id_convenio"], $lista_convenios, "id_convenio, nombre_convenio", "-Seleccione-", "", true, "width: 100%;");
						?>
                    </td>
                </tr>
                <tr>
                	<td align="left">
                    	<?php
                        	$id_estado_prog = $programación_cx_obj["id_estado_prog"];
						?>
                    	<input type="hidden" id="hdd_id_estado_prog" name="hdd_id_estado_prog" value="<?php echo($id_estado_prog); ?>" />
                    	Estado:&nbsp;<b><span id="sp_estado_prog"><?php echo($programación_cx_obj["estado_prog"]) ?></span></b>
                    </td>
                    <td align="right">
                    	Cambiar estado a:&nbsp;
                    </td>
                    <td align="left">
                    	<?php
                        	$lista_estados_prog = $dbListas->getListaDetalles(71, 1);
						?>
                        <select id="cmb_estado_prog" name="cmb_estado_prog" class="no-margin" onchange="seleccionar_estado_prog(this.value);">
                        	<option value=""></option>
							<?php
                            	foreach ($lista_estados_prog as $estado_aux) {
									if ($estado_aux["id_detalle"] != "432" && ($id_estado_prog != "433" || $estado_aux["id_detalle"] != "433")) {
							?>
                            <option value="<?php echo($estado_aux["id_detalle"]); ?>"><?php echo($estado_aux["nombre_detalle"]); ?></option>
                            <?php
									}
								}
							?>
                        </select>
                    </td>
                    <td align="left">
                    	<?php
                        	//Se buscan las citas asociadas a la programación
							$lista_citas = $dbCitas->getListaCitasProgCx($id_prog_cx);
							
							if (count($lista_citas) > 0) {
								$cita_aux = $lista_citas[0];
						?>
                        <b>Cita:</b>&nbsp;<?php echo($cita_aux["fecha_cita_t"]." ".$cita_aux["hora_cita_t"]); ?>
                        <br />
                        <b>Lugar:</b>&nbsp;<?php echo($cita_aux["lugar_cita"]); ?>
                        <?php
							} else if ($tipo_acceso_menu == "2" && ($id_estado_prog == "432" || $id_estado_prog == "433")) {
						?>
                        <input type="button" id="btn_programar_cita_preqx" name="btn_programar_cita_preqx" value="Programar cita prequir&uacute;rgica" class="btnPrincipal peq no-margin" onclick="programar_cita_preqx();" />
                        <?php
							}
						?>
                    </td>
                </tr>
                <?php
                	$display_aux = "none";
					if ($programación_cx_obj["id_estado_prog"] == "435") {
						$display_aux = "table-row";
					}
				?>
                <tr id="tr_motivo_cancela" style="display:<?php echo($display_aux); ?>;">
                	<td></td>
                    <td align="right">Motivo de cancelaci&oacute;n*:</td>
                    <td align="left">
                    	<?php
                        	//Lista de motivos de cancelación
							$lista_motivos_cancela = $dbProgramacionCx->getListaMotivosCancela(1, 1);
							
							$combo->getComboDb("cmb_motivo_cancela", $programación_cx_obj["id_motivo_cancela"], $lista_motivos_cancela, "id_motivo, nombre_motivo", "-Seleccione-", "", true, "width: 100%;");
						?>
                    </td>
                </tr>
                <tr style="height:10px;"></tr>
                <tr>
                	<td align="center" colspan="4">
                    	<?php
                        	ver_programacion_cx_det($programación_cx_obj["id_prog_cx"]);
						?>
                    </td>
                </tr>
                <tr style="height:10px;"></tr>
                <tr>
                	<td align="center" colspan="4">
                    	<?php
                        	if ($tipo_acceso_menu == "2") {
						?>
                    	<input type="button" id="btn_editar_prog_cx" name="btn_editar_prog_cx" value="Guardar cambios" class="btnPrincipal" onclick="editar_prog_cx();" />
                        &nbsp;&nbsp;
                        <?php
							}
						?>
                        <input type="button" id="btn_cancelar_prog_cx" name="btn_cancelar_prog_cx" value="Volver" class="btnPrincipal" onclick="ocultar_prog_cx();" />
                        <div id="d_editar_prog_cx" style="display:none;"></div>
                    </td>
                </tr>
            </table>
        </fieldset>
        <?php
			break;
			
		case "10": //Edición de programación de cirugía
			$id_usuario = $_SESSION["idUsuario"];
			@$id_prog_cx = $utilidades->str_decode($_POST["id_prog_cx"]);
			@$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
			@$id_usuario_prof = $utilidades->str_decode($_POST["id_usuario_prof"]);
			@$fecha_prog = $utilidades->str_decode($_POST["fecha_prog"]);
			@$hora_prog = $utilidades->str_decode($_POST["hora_prog"]);
			@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
			@$id_estado_prog = $utilidades->str_decode($_POST["id_estado_prog"]);
			@$id_motivo_cancela = intval($_POST["id_motivo_cancela"], 10);
			@$cant_det = intval($_POST["cant_det"], 10);
			
			$arr_elementos = array();
			for ($i = 0; $i < $cant_det; $i++) {
				@$arr_elementos[$i]["tipo_elemento"] = $utilidades->str_decode($_POST["tipo_elemento_".$i]);
				@$arr_elementos[$i]["cod_elemento"] = $utilidades->str_decode($_POST["cod_elemento_".$i]);
				@$arr_elementos[$i]["tipo_bilateral"] = $utilidades->str_decode($_POST["tipo_bilateral_".$i]);
				@$arr_elementos[$i]["cantidad"] = intval($_POST["cantidad_".$i], 10);
				@$cant_det_val = intval($_POST["cant_det_val_".$i], 10);
				
				for ($j = 0; $j < $cant_det_val; $j++) {
					@$arr_elementos[$i]["det_val"][$j]["tipo_lente"] = $utilidades->str_decode($_POST["tipo_lente_".$i."_".$j]);
					@$arr_elementos[$i]["det_val"][$j]["serial_lente"] = $utilidades->str_decode($_POST["serial_lente_".$i."_".$j]);
					@$arr_elementos[$i]["det_val"][$j]["poder_lente"] = $utilidades->str_decode($_POST["poder_lente_".$i."_".$j]);
				}
			}
			
			$id_prog_cx = $dbProgramacionCx->crearEditarProgramacionCx($id_prog_cx, $id_paciente, $id_usuario_prof, $fecha_prog, $hora_prog, $id_estado_prog, $id_convenio, "", "", "", "", "", $id_motivo_cancela, $arr_elementos, $id_usuario);
			
			//Se busca el nombre del estado de programación
			$nombre_estado_prog = "";
			if ($id_estado_prog != "") {
				$detalle_obj = $dbListas->getDetalle($id_estado_prog);
				$nombre_estado_prog = $detalle_obj["nombre_detalle"];
			}
		?>
        <input type="hidden" id="hdd_result_editar_prog_cx" name="hdd_result_editar_prog_cx" value="<?php echo($id_prog_cx); ?>" />
        <input type="hidden" id="hdd_result_id_estado_prog" name="hdd_result_id_estado_prog" value="<?php echo($id_estado_prog); ?>" />
        <input type="hidden" id="hdd_result_nombre_estado_prog" name="hdd_result_nombre_estado_prog" value="<?php echo($nombre_estado_prog); ?>" />
        <?php
			break;
	}
?>
