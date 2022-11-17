<?php session_start();
	/*
	 * Pagina para registro de cirugías
	 * Autor: Feisar Moreno - 04/04/2014
	 */
 	header('Content-Type: text/xml; charset=UTF-8');
	
	require_once("../db/DbCirugias.php");
	require_once("../db/DbMaestroProcedimientos.php");
	require_once("../db/DbMenus.php");
	require_once("../db/DbVariables.php");
	require_once("../funciones/Utilidades.php");
	
	$dbCirugias = new DbCirugias();
	$dbMaestroProcedimientos = new DbMaestroProcedimientos();
	$dbMenus = new DbMenus();
	$utilidades = new Utilidades();
	
	$opcion = $_POST["opcion"];
	
	switch ($opcion) {
		case "1"://Dibuja el div flotante para buscar procedimientos
			?>
			<div class="encabezado">
				<h3>Listado de procedimientos</h3>
			</div>
			<br/>
			<form id="frmBuscarProcedimiento">
				<table>
					<tr>
						<td>
							<input type="text" id="txt_identificacion_interno" name="txt_identificacion_interno" placeholder="Digite el codigo o nombre del procedimiento" style="width:400px;" />
						</td>
						<td>
							<input type="submit" value="Buscar" class="btnPrincipal peq" onclick="validar_buscar_procedimientos();" />
						</td>
					</tr>
				</table>
			</form>
			<br/>
			<div style="clear: both;"></div>
			<br/>
			<div id="d_procedimientos_b" style="height:350px; overflow:auto;">
			</div>
			<?php
			break;
			
		case "2": //Resultados de búsqueda de procedimientos
			$parametro = trim($utilidades->str_decode($_POST['parametro']));
		?>
		<table class="modal_table" id="tablaPreciosModal" style="width:100%;">
			<tr id="tabla_encabezado">
				<th style="width:10%;">C&oacute;digo</th>
				<th style="width:90%;">Procedimiento</th>
			</tr>
	        <?php
				$lista_procedimientos = $dbMaestroProcedimientos->getProcedimientos($parametro);
				
				if (count($lista_procedimientos) > 0) {
					$cont = 0;
					foreach ($lista_procedimientos as $proc_aux) {
			?>
			<tr onclick="seleccionar_procedimiento(<?php echo($cont); ?>);">
				<td align="center">
					<input type="hidden" name="hdd_cod_procedimiento_b_<?php echo($cont); ?>" id="hdd_cod_procedimiento_b_<?php echo($cont); ?>" value="<?php echo($proc_aux["cod_procedimiento"]); ?>" />
					<?php echo($proc_aux['cod_procedimiento']); ?>
				</td>
				<td align="left">
					<input type="hidden" name="hdd_nombre_procedimiento_b_<?php echo($cont); ?>" id="hdd_nombre_procedimiento_b_<?php echo($cont); ?>" value="<?php echo($proc_aux["nombre_procedimiento"]); ?>" />
					<?php echo($proc_aux['nombre_procedimiento']); ?>
				</td>
			</tr>
			<?php
						$cont++;
					}
				} else {
			?>
			<tr>
				<td colspan="2">No se encontraron procedimientos</td>
			</tr>
            <?php
				}
			?>
		</table>
		<?php
			break;
			
		case "3": //Guardar Cirugía
			$id_usuario = $_SESSION["idUsuario"];
			@$id_hc = $utilidades->str_decode($_POST["id_hc"]);
			@$id_admision = $utilidades->str_decode($_POST["id_admision"]);
			@$tipo_guardar = $utilidades->str_decode($_POST["tipo_guardar"]);
			@$fecha_cx = $utilidades->str_decode($_POST["fecha_cx"]);
			@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
			@$id_amb_rea = $utilidades->str_decode($_POST["id_amb_rea"]);
			@$id_fin_pro = $utilidades->str_decode($_POST["id_fin_pro"]);
			@$id_usuario_prof = $utilidades->str_decode($_POST["id_usuario_prof"]);
			@$ind_reoperacion = $utilidades->str_decode($_POST["ind_reoperacion"]);
			@$ind_reop_ent = $utilidades->str_decode($_POST["ind_reop_ent"]);
			@$fecha_cx_ant = $utilidades->str_decode($_POST["fecha_cx_ant"]);
			@$observaciones_cx = trim($utilidades->str_decode($_POST["observaciones_cx"]));
			@$arch_stickers = $utilidades->str_decode($_POST["arch_stickers"]);
			
			@$id_tipo_laser = $utilidades->str_decode($_POST["id_tipo_laser"]);
			@$id_ojo = $utilidades->str_decode($_POST["id_ojo"]);
			@$num_turno = $utilidades->str_decode($_POST["num_turno"]);
			
			@$id_tecnica_od = $utilidades->str_decode($_POST["id_tecnica_od"]);
			@$microquerato_od = trim($utilidades->str_decode($_POST["microquerato_od"]));
			@$num_placas_od = $utilidades->str_decode($_POST["num_placas_od"]);
			@$tiempo_vacio_od = $utilidades->str_decode($_POST["tiempo_vacio_od"]);
			@$uso_cuchilla_od = $utilidades->str_decode($_POST["uso_cuchilla_od"]);
			@$bisagra_od = trim($utilidades->str_decode($_POST["bisagra_od"]));
			@$tiempo_qx_od = $utilidades->str_decode($_POST["tiempo_qx_od"]);
			@$tipo_od = trim($utilidades->str_decode($_POST["tipo_od"]));
			@$esfera_od = $utilidades->str_decode($_POST["esfera_od"]);
			@$cilindro_od = $utilidades->str_decode($_POST["cilindro_od"]);
			@$eje_od = $utilidades->str_decode($_POST["eje_od"]);
			@$zona_optica_od = $utilidades->str_decode($_POST["zona_optica_od"]);
			@$ablacion_od = $utilidades->str_decode($_POST["ablacion_od"]);
			@$esp_corneal_base_od = $utilidades->str_decode($_POST["esp_corneal_base_od"]);
			@$humedad_od = $utilidades->str_decode($_POST["humedad_od"]);
			@$temperatura_od = $utilidades->str_decode($_POST["temperatura_od"]);
			@$wtw_od = $utilidades->str_decode($_POST["wtw_od"]);
			
			@$id_tecnica_oi = $utilidades->str_decode($_POST["id_tecnica_oi"]);
			@$microquerato_oi = trim($utilidades->str_decode($_POST["microquerato_oi"]));
			@$num_placas_oi = $utilidades->str_decode($_POST["num_placas_oi"]);
			@$tiempo_vacio_oi = $utilidades->str_decode($_POST["tiempo_vacio_oi"]);
			@$uso_cuchilla_oi = $utilidades->str_decode($_POST["uso_cuchilla_oi"]);
			@$bisagra_oi = trim($utilidades->str_decode($_POST["bisagra_oi"]));
			@$tiempo_qx_oi = $utilidades->str_decode($_POST["tiempo_qx_oi"]);
			@$tipo_oi = trim($utilidades->str_decode($_POST["tipo_oi"]));
			@$esfera_oi = $utilidades->str_decode($_POST["esfera_oi"]);
			@$cilindro_oi = $utilidades->str_decode($_POST["cilindro_oi"]);
			@$eje_oi = $utilidades->str_decode($_POST["eje_oi"]);
			@$zona_optica_oi = $utilidades->str_decode($_POST["zona_optica_oi"]);
			@$ablacion_oi = $utilidades->str_decode($_POST["ablacion_oi"]);
			@$esp_corneal_base_oi = $utilidades->str_decode($_POST["esp_corneal_base_oi"]);
			@$humedad_oi = $utilidades->str_decode($_POST["humedad_oi"]);
			@$temperatura_oi = $utilidades->str_decode($_POST["temperatura_oi"]);
			@$wtw_oi = $utilidades->str_decode($_POST["wtw_oi"]);
			
			@$cant_procedimientos = intval($utilidades->str_decode($_POST['cant_procedimientos']), 10);
			$array_procedimientos = array();
			$cont_aux = 0;
			for ($i = 0; $i < $cant_procedimientos; $i++) {
				if (isset($_POST["cod_procedimiento_".$i])) {
					@$cod_procedimiento_aux = $utilidades->str_decode($_POST["cod_procedimiento_".$i]);
					@$id_ojo_aux = $utilidades->str_decode($_POST["id_ojo_".$i]);
					@$via_aux = $utilidades->str_decode($_POST["via_".$i]);
					$array_procedimientos[$cont_aux]["cod_procedimiento"] = $cod_procedimiento_aux;
					$array_procedimientos[$cont_aux]["id_ojo"] = $id_ojo_aux;
					$array_procedimientos[$cont_aux]["via"] = $via_aux;
					$cont_aux++;
				}
			}
			$cant_procedimientos = $cont_aux;
			
			@$cant_ciex = intval($utilidades->str_decode($_POST['cant_ciex']), 10);
			$array_diagnosticos = array();
			$cont_aux = 0;
			for ($i = 1; $i <= $cant_ciex; $i++) {
				if (isset($_POST['cod_ciex_'.$i])) {
					@$ciex_diagnostico = $utilidades->str_decode($_POST['cod_ciex_'.$i]);
					@$valor_ojos = $utilidades->str_decode($_POST['val_ojos_'.$i]);
					$array_diagnosticos[$cont_aux][0] = $ciex_diagnostico;
					$array_diagnosticos[$cont_aux][1] = $valor_ojos;
					$cont_aux++;
				}
			}
			
			$resultado = $dbCirugias->editar_cirugia_laser($id_hc, $id_admision, $fecha_cx, $id_convenio, $id_amb_rea, $id_fin_pro,
						 $id_usuario_prof, $ind_reoperacion, $ind_reop_ent, $fecha_cx_ant, $observaciones_cx, $arch_stickers, $id_tipo_laser,
						 $id_ojo, $num_turno, $id_tecnica_od, $microquerato_od, $num_placas_od, $tiempo_vacio_od, $uso_cuchilla_od,
						 $bisagra_od, $tiempo_qx_od, $tipo_od, $esfera_od, $cilindro_od, $eje_od, $zona_optica_od, $ablacion_od,
						 $esp_corneal_base_od, $humedad_od, $temperatura_od, $wtw_od, $id_tecnica_oi, $microquerato_oi,
						 $num_placas_oi, $tiempo_vacio_oi, $uso_cuchilla_oi, $bisagra_oi, $tiempo_qx_oi, $tipo_oi, $esfera_oi,
						 $cilindro_oi, $eje_oi, $zona_optica_oi, $ablacion_oi, $esp_corneal_base_oi, $humedad_oi, $temperatura_oi,
						 $wtw_oi, $array_procedimientos, $array_diagnosticos, $tipo_guardar, $id_usuario);
		?>
		<input type="hidden" name="hdd_resul_guardar" id="hdd_resul_guardar" value="<?php echo($resultado); ?>" />
		<input type="hidden" name="hdd_tipo_guardar" id="hdd_tipo_guardar" value="<?php echo($tipo_guardar); ?>" />
		<div class="contenedor_error" id="contenedor_error"></div>
		<div class="contenedor_exito" id="contenedor_exito"></div>
		<?php
			break;
			
		case "4": //Guardar Evaluacion Cirugía
		
			$id_usuario = $_SESSION["idUsuario"];
			@$id_hc = $utilidades->str_decode($_POST["id_hc"]);
			@$id_admision = $utilidades->str_decode($_POST["id_admision"]);
			@$txt_anotaciones_ev = $utilidades->str_decode($_POST["txt_anotaciones_ev"]);			
			$resultado = $dbCirugias->editar_evaluacion_cirugia_laser($id_hc, $id_admision, $txt_anotaciones_ev, $id_usuario);
			echo $resultado;
			
		?>
		<input type="hidden" name="hdd_resul_guardar" id="hdd_resul_guardar" value="<?php echo($resultado); ?>" />
		<div class="contenedor_error" id="contenedor_error"></div>
		<div class="contenedor_exito" id="contenedor_exito"></div>
		<?php
			
			
		break;		
			
			
	}
?>
