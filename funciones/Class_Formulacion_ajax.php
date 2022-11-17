<?php
	session_start();
	//Autor: Feisar Moreno - 31/03/2017
	
	header("Content-Type: text/xml; charset=UTF-8");
	
	require_once("../db/DbFormulacionHC.php");
	require_once("../db/DbMaestroMedicamentos.php");
	require_once("../db/DbAdmision.php");
	require_once("../db/DbListas.php");
	
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Utilidades.php");
	require_once("Class_Combo_Box.php");
	
	$dbFormulacionHC = new DbFormulacionHC();
	$dbMaestroMedicamentos = new DbMaestroMedicamentos();
	$dbAdmision = new DbAdmision();
	$dbListas = new DbListas();
	
	$contenido = new ContenidoHtml();
	$utilidades = new Utilidades();
	$combo = new Combo_Box();
	
	$contenido->validar_seguridad(1);
	$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);
	
	$opcion = $_POST["opcion"];
	
	switch ($opcion) {
		case "1": //Búsqueda de la fórmulación anterior
			@$id_hc = $utilidades->str_decode($_POST["id_hc"]);
			
			//Se obtiene el listado de fórmulas médicas anterior
			$lista_formulacion = $dbFormulacionHC->getListaFormulacionHCAnterior($id_hc);
			
			$cant_ant_fm = count($lista_formulacion);
		?>
        <input type="hidden" id="hdd_cant_ant_fm" value="<?php echo($cant_ant_fm); ?>" />
        <?php
			if ($cant_ant_fm > 0) {
				for ($i = 0; $i < $cant_ant_fm; $i++) {
					$formulacion_aux = $lista_formulacion[$i];
		?>
        <input type="hidden" id="hdd_id_formulacion_ant_fm_<?php echo($i); ?>" value="<?php echo($formulacion_aux["id_formulacion"]); ?>" />
        <input type="hidden" id="hdd_cod_medicamento_ant_fm_<?php echo($i); ?>" value="<?php echo($formulacion_aux["cod_medicamento"]); ?>" />
        <input type="hidden" id="hdd_nombre_medicamento_ant_fm_<?php echo($i); ?>" value="<?php echo($formulacion_aux["nombre_medicamento"]); ?>" />
        <input type="hidden" id="hdd_cod_tipo_medicamento_ant_fm_<?php echo($i); ?>" value="<?php echo($formulacion_aux["cod_tipo_medicamento"]); ?>" />
        <input type="hidden" id="hdd_presentacion_ant_fm_<?php echo($i); ?>" value="<?php echo($formulacion_aux["presentacion"]); ?>" />
        <input type="hidden" id="hdd_cantidad_ant_fm_<?php echo($i); ?>" value="<?php echo($formulacion_aux["cantidad"]); ?>" />
        <input type="hidden" id="hdd_dosificacion_ant_fm_<?php echo($i); ?>" value="<?php echo($formulacion_aux["dosificacion"]); ?>" />
        <input type="hidden" id="hdd_unidades_ant_fm_<?php echo($i); ?>" value="<?php echo($formulacion_aux["unidades"]); ?>" />
        <input type="hidden" id="hdd_duracion_ant_fm_<?php echo($i); ?>" value="<?php echo($formulacion_aux["duracion"]); ?>" />
        <?php
				}
			}
			break;
			
		case "2": //Formulario de creación de medicamentos
			@$id_hc = $utilidades->str_decode($_POST["id_hc"]);
			
			//Se hallan los datos de la admisión
			$admision_obj = $dbAdmision->get_admision_hc($id_hc);
			$id_lugar_cita = $admision_obj["id_lugar_cita"];
			
			//Lista Sí/No
			$lista_si_no = array();
			$lista_si_no[0][0] = "1";
			$lista_si_no[0][1] = "SI";
			$lista_si_no[1][0] = "0";
			$lista_si_no[1][1] = "NO";
		?>
        <div class="encabezado" style="width:100%;">
        	<h3>Crear Medicamento</h3>
        </div>
        <div style="width:100%; display:block;">
            <div class="contenedor_error" id="d_contenedor_error_fm"></div>
            <div class="contenedor_exito" id="d_contenedor_exito_fm"></div>
        </div>
		<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">
			<tr>
            	<td align="right" style="width:15%">
                	<label>Nombre comercial*</label>
                </td>
				<td align="left" style="width:85%" colspan="3">
					<input type="text" name="txt_nombre_comercial_fm" id="txt_nombre_comercial_fm" style="width:100%;" maxlength="256">
				</td>
			</tr>
			<tr>
            	<td align="right">
                	<label>Nombre gen&eacute;rico*</label>
                </td>
				<td align="left" colspan="3">
					<input type="text" name="txt_nombre_generico_fm" id="txt_nombre_generico_fm" style="width:100%;" maxlength="256">
				</td>
			</tr>
			<tr>
            	<td align="right">
                	<label>Presentaci&oacute;n*</label>
                </td>
				<td align="left" style="width:35%;">
					<input type="text" name="txt_presentacion_fm" id="txt_presentacion_fm" style="width:100%;" maxlength="256">
				</td>
				<td align="right" style="width:15%;">
					<label>Concentraci&oacute;n</label>
				</td>
				<td align="left" style="width:35%;">
					<input type="text" name="txt_concentracion_fm" id="txt_concentracion_fm" style="width:100%;" maxlength="50">
				</td>
			</tr>
			<tr>
            	<td align="right">
                	<label>Grupo terap&eacute;utico</label>
                </td>
				<td align="left">
					<input type="text" name="txt_grupo_terapeutico_fm" id="txt_grupo_terapeutico_fm" style="width:100%;" maxlength="100">
				</td>
				<td align="right">
					<label>Laboratorio</label>
				</td>
				<td align="left">
					<input type="text" name="txt_laboratorio_fm" id="txt_laboratorio_fm" style="width:100%;" maxlength="100">
				</td>
			</tr>
			<tr>
            	<td align="right">
                	<label>Medicamento POS*</label>
                </td>
				<td align="left">
                    <?php
						$combo->get("cmb_pos_fm", "0", $lista_si_no, "--Seleccione--");
					?>
				</td>
				<td align="right">
					<label>Lugar de aplicaci&oacute;n</label>
				</td>
				<td align="left">
                    <?php
						$lista_lugares_citas = $dbListas->getListaDetalles(12, 1);
						$combo->getComboDb("cmb_lugar_cita_fm", $id_lugar_cita, $lista_lugares_citas, "id_detalle,nombre_detalle", "--Seleccione--");
					?>
				</td>
			</tr>
            <tr>
            	<td align="center" colspan="4">
                	<input type="button" id="btn_crear_medicamento_fm" name="btn_crear_medicamento_fm" value="Crear" class="btnPrincipal no_margin" onclick="crear_medicamento_fm();" />
                    <div id="d_crear_medicamento_fm" style="display:none;"></div>
                </td>
            </tr>
		</table>
		<div id="d_buscar_diagnosticos"></div>
        <?php
			break;
			
		case "3": //Crear medicamento
			$id_usuario = $_SESSION["idUsuario"];
			@$nombre_comercial = $utilidades->str_decode($_POST["nombre_comercial"]);
			@$nombre_generico = $utilidades->str_decode($_POST["nombre_generico"]);
			@$presentacion = $utilidades->str_decode($_POST["presentacion"]);
			@$concentracion = $utilidades->str_decode($_POST["concentracion"]);
			@$grupo_terapeutico = $utilidades->str_decode($_POST["grupo_terapeutico"]);
			@$laboratorio = $utilidades->str_decode($_POST["laboratorio"]);
			@$ind_pos = $utilidades->str_decode($_POST["ind_pos"]);
			@$id_lugar_cita = $utilidades->str_decode($_POST["id_lugar_cita"]);
			
			$cod_medicamento = $dbMaestroMedicamentos->crear_medicamento($nombre_generico, $nombre_comercial, $presentacion,
					$concentracion, $grupo_terapeutico, $laboratorio, $ind_pos, $id_lugar_cita, $id_usuario);
		?>
        <input type="hidden" id="hdd_cod_medicamento_r_fm" name="hdd_cod_medicamento_r_fm" value="<?php echo($cod_medicamento); ?>" />
        <input type="hidden" id="hdd_nombre_generico_r_fm" name="hdd_nombre_generico_r_fm" value="<?php echo($nombre_generico); ?>" />
        <input type="hidden" id="hdd_nombre_comercial_r_fm" name="hdd_nombre_comercial_r_fm" value="<?php echo($nombre_comercial); ?>" />
        <input type="hidden" id="hdd_presentacion_r_fm" name="hdd_presentacion_r_fm" value="<?php echo($presentacion); ?>" />
        <?php
			break;
	}
?>
