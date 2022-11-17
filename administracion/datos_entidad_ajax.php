<?php
	session_start();
	
	header('Content-Type: text/xml; charset=UTF-8');
	
	require_once("../db/DbDatosEntidad.php");
	require_once("../db/DbListas.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/Utilidades.php");
	
	$dbDatosEntidad = new DbdatosEntidad();
	$dbListas = new DbListas();
	$combo = new Combo_Box();
	$utilidades = new Utilidades();
	
	$opcion = $_POST["opcion"];
	
	switch ($opcion) {
		case "1": //Carga el listado de la tabla: datos_entidad
			$lista_entidades = $dbDatosEntidad->getListaDatosEntidad();
        ?>
        <table class="paginated modal_table" style="width: 99%; margin: auto;">
            <thead>
                <tr>
                    <th style="width:10%;">C&oacute;digo</th>
                    <th style="width:20;">N&uacute;mero documento</th>
                    <th style="width:45%;">Nombre</th>
                    <th style="width:15%;">Abreviatura</th>
                    <th style="width:10%;">Estado</th>
                </tr>
            </thead>
            <?php
				if (count($lista_entidades) > 0) {
					for ($i = 0; $i < count($lista_entidades); $i++) {
						$entidad_aux = $lista_entidades[$i];
						
						if ($entidad_aux["ind_activo"] == "1") {
							$estado = "Activo";
							$class_estado = "activo";
						} else {
							$estado = "No Activo";
							$class_estado = "inactivo";
						}
			?>
            <tr onclick="mostrar_ventana_modificar(2, <?php echo $entidad_aux["id_prestador"]; ?>)">
            	<td align="center"><?php echo($entidad_aux["cod_prestador"]); ?></td>
                <td align="center"><?php echo($entidad_aux["cod_tipo_documento"]." ".$entidad_aux["numero_documento"]); ?></td>
                <td align="left"><?php echo($entidad_aux["nombre_prestador"]); ?></td>
                <td align="center"><?php echo($entidad_aux["sigla_prestador"]); ?></td>
                <td align="center"><span class="<?php echo($class_estado); ?>"><?php echo($estado); ?></span></td>
            </tr>
            <?php
					}
				} else {
			?>
            <tr>
                <td colspan="5">No hay resultados</td>
            </tr>
            <?php
				}
			?>
        </table>
        <?php
			break;
			
		case "2": //Carga el detalle de: datos_entidad en  la ventana flotante
			$id_prestador = $utilidades->str_decode($_POST['id_prestador']);
			$tipo = intval($_POST['tipo'], 10);
			//Tipo: 1: Crear - 2: Modificar
			
			$datos_entidad_obj = array();
			if ($tipo == 2) {
				$datos_entidad_obj = $dbDatosEntidad->getDatosEntidadId($id_prestador);
			}
			
			$cod_prestador = "";
			$nombre_prestador = "";
			$sigla_prestador = "";
			$id_tipo_documento = "";
			$numero_documento = "";
			$ind_activo = "1";
			if (count($datos_entidad_obj) > 0) {
				$cod_prestador = $datos_entidad_obj["cod_prestador"];
				$nombre_prestador = $datos_entidad_obj["nombre_prestador"];
				$sigla_prestador = $datos_entidad_obj["sigla_prestador"];
				$id_tipo_documento = $datos_entidad_obj["id_tipo_documento"];
				$numero_documento = $datos_entidad_obj["numero_documento"];
				$ind_activo = $datos_entidad_obj["ind_activo"];
			}
		?>
        <div class="encabezado">
            <h3>Datos de la entidad</h3>
        </div>
        <div style="width:98%; margin:auto;">
			<div class="contenedor_error" id="contenedor_error2"></div>
            <table style="width: 100%;">
            	<tr>
                	<td align="right" style="width:18%;">
                    	<label class="inline">C&oacute;digo del prestador:&nbsp;</label>
                    </td>
                    <td align="left" style="width:32%;">
                    	<input type="hidden" id="hdd_id_prestador" value="<?php echo($id_prestador); ?>" />
                        <input type="text" id="txt_cod_prestador" value="<?php echo($cod_prestador); ?>" maxlength="12" onkeypress="return solo_numeros(event, false);" />
                    </td>
                    <td align="right" style="width:18%;">
                    	<label class="inline">Activo:&nbsp;</label>
                    </td>
                    <td align="left" style="width:32%;">
                    	<input type="checkbox" id="chk_activo" name="chk_activo" <?php if ($ind_activo == "1") { ?>checked="checked"<?php }	?> />
                    </td>
                </tr>
                <tr>
                	<td align="right">
                    	<label class="inline">Nombre:&nbsp;</label>
                    </td>
                    <td align="left">
                    	<input type="text" id="txt_nombre_prestador" value="<?php echo($nombre_prestador); ?>" maxlength="60" onblur="trim_cadena(this);" />
                    </td>
                    <td align="right">
                    	<label class="inline">Abreviatura:&nbsp;</label>
                    </td>
                    <td align="left">
                    	<input type="text" id="txt_sigla_prestador" value="<?php echo($sigla_prestador); ?>" maxlength="10" onblur="trim_cadena(this);" />
                    </td>
                </tr>
                <tr>
                	<td align="right">
                    	<label class="inline">Tipo de documento:&nbsp;</label>
                    </td>
                    <td align="left">
                    	<?php $combo->getComboDb("cmb_tipo_documento", $id_tipo_documento, $dbListas->getListaDetalles(27), "id_detalle, nombre_detalle", "Seleccione el tipo de documento", "", "", "width: 220px;"); ?>
                    </td>
                    <td align="right">
                    	<label>N&uacute;mero de documento:&nbsp;</label>
                    </td>
                    <td align="left"> 
                    	<input type="text" id="txt_numero_documento" value="<?php echo($numero_documento); ?>" maxlength="20" onblur="trim_cadena(this);" />
                    </td>
                </tr>
                <tr>
                	<td colspan="4">
                    	<?php
							switch ($tipo) {
								case 1: //Crear
						?>
                        <input type="button" class="btnPrincipal" id="btnCrearDatoEntidad" name="btnCrearDatoEntidad" value="Crear" onclick="modificar_datos_entidad();" />
                        <?php
									break;
								case 2: //Modificar
						?>
                        <input type="button" class="btnPrincipal" id="btnModificarDatoEntidad" name="btnModificarDatoEntidad" value="Modificar" onclick="modificar_datos_entidad();" />
                        <?php
									break;
							}
						?>
                    </td>
                </tr>
            </table>
        </div>
        <div id="d_guardar_datos_entidad" style="display:none;"></div>
		<?php
        	break;
			
		case "3": //Modificar un registro
			$id_usuario = $_SESSION["idUsuario"];
			@$id_prestador = $utilidades->str_decode($_POST["id_prestador"]);
			@$cod_prestador = $utilidades->str_decode($_POST["cod_prestador"]);
			@$nombre_prestador = $utilidades->str_decode($_POST["nombre_prestador"]);
			@$sigla_prestador = $utilidades->str_decode($_POST["sigla_prestador"]);
			@$id_tipo_documento = $utilidades->str_decode($_POST["id_tipo_documento"]);
			@$numero_documento = $utilidades->str_decode($_POST["numero_documento"]);
			@$ind_activo = $utilidades->str_decode($_POST["ind_activo"]);
			
			$resultado_aux = $dbDatosEntidad->guardaDatosEntidad($id_prestador, $cod_prestador, $nombre_prestador,
					$sigla_prestador, $id_tipo_documento, $numero_documento, $ind_activo, $id_usuario);
		?>
        <input type="hidden" id="hdd_resul_guardar" value="<?php echo($resultado_aux); ?>" />
        <?php
			break;
	}
?>
