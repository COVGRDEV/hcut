<?php
header('Content-Type: text/xml; charset=UTF-8');
session_start();

require_once("../db/DbTiposCitas.php");
require_once("../db/DbTiposCitasDetalle.php");
require_once("../db/DbMaestroProcedimientos.php");
require_once("../db/DbTiposRegistrosHc.php");
require_once("../db/DbEstadosAtencion.php");
require_once("../funciones/Utilidades.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../principal/ContenidoHtml.php");

$dbTiposCitas = new DbTiposCitas();
$dbTiposCitasDet = new DbTiposCitasDetalle();
$dbMaestroProcedimientos = new DbMaestroProcedimientos();
$dbTiposRegistrosHc = new DbTiposRegistrosHc();
$dbEstadosAtencion = new DbEstadosAtencion();
$utilidades = new Utilidades();
$combo = new Combo_Box();
$contenido = new ContenidoHtml();

$opcion = $_POST["opcion"];
$usuario = $_SESSION["idUsuario"];

$tipo_acceso_menu = "0";
if (isset($_POST["hdd_numero_menu"])) {
	$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);
}

switch ($opcion) {
    case "1": //Carga el listado de tipos de citas
        $parametro = $utilidades->str_decode($_POST['parametro']);

        if ($parametro == '-1') {
            $rta_aux = $dbTiposCitas->getTiposcitas();
        } else if ($parametro != '-1') {
            $rta_aux = $dbTiposCitas->buscarTipoCita($parametro);
        }
        ?>
        <table class="paginated modal_table" style="width: 99%; margin: auto;">
            <thead>
                <tr>
                	<th colspan="8">
                    	Tipos de citas - <?php echo count($rta_aux) . ' registros'; ?>
                        <?php
                        	if ($tipo_acceso_menu == "2") {
						?>
                        <img style="float: right;margin: 0 20px 0 0;" onclick="nuevoTipoCita()" src="../imagenes/Add-icon.png" title="Nuevo tipo de cita" />
                        <?php
							}
						?>
                    </th>
                </tr>
                <tr>
                    <th style="width:8%;">C&oacute;digo</th>
                    <th style="width:28%;">Nombre</th>
                    <th style="width:10%;">Preconsulta</th>
                    <th style="width:10%;">Prequir&uacute;rgico</th>
                    <th style="width:20%;">Tipo de registro quir&uacute;rgico</th>
                    <th style="width:8%;">Signos vitales</th>
                    <th style="width:8%;">Despacho</th>
                    <th style="width:8%;">Estado</th>
                </tr>
            </thead>
            <?php
            if (count($rta_aux) >= 1) {
                foreach ($rta_aux as $value) {
                    $estado = $value['ind_activo'];
                    if ($estado == 1) {
                        $estado = 'Activo';
                        $class_estado = 'activo';
                    } else if ($estado == 0) {
                        $estado = 'No Activo';
                        $class_estado = 'inactivo';
                    }
                    ?>
                    <tr onclick="muestraTipoCita(<?php echo $value['id_tipo_cita']; ?>);">
                        <td align="center"><?php echo $value['id_tipo_cita']; ?></td>
                        <td align="left"><?php echo $value['nombre_tipo_cita']; ?></td>
                        <td align="center">
							<?php
								if ($value['ind_preconsulta'] == '1') {
									echo 'Si';
								} else if ($value['ind_preconsulta'] == '0') {
									echo 'No';
								}
                            ?>
                        </td>
                        <td align="center">
							<?php
								if ($value['ind_preqx'] == '1') {
									echo 'Si';
								} else if ($value['ind_preqx'] == '0') {
									echo 'No';
								}
                            ?>
                        </td>
                        <td align="center"><?php echo($value['nombre_tipo_reg']); ?></td>
                        <td align="center">
							<?php
								if ($value['ind_signos_vitales'] == '1') {
									echo 'Si';
								} else if ($value['ind_signos_vitales'] == '0') {
									echo 'No';
								}
                            ?>
                        </td>
                        <td align="center">
							<?php
								if ($value['ind_despacho'] == '1') {
									echo 'Si';
								} else if ($value['ind_despacho'] == '0') {
									echo 'No';
								}
                            ?>
                        </td>
                        <td><span class="<?php echo $class_estado; ?>"><?php echo $estado; ?></span></td>
                    </tr>

                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="8">
                        No hay resultados
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
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

    case "2"://Imprime el formulario para agregar un nuevo tipo de cita
        ?>
        <div class="encabezado">
            <h3>Nuevo Tipo de cita</h3>
        </div>
        <br/>
        <div>
            <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%;">
                <tr>
                    <td align="right" style="width:30%;">
                    	<label class="inline"><b>Nombre</b></label>
                    </td>
                    <td align="left" style="width:30%;">
                    	<input type="text" id="txt_nombre_tipo_cita" onblur="trim_cadena(this);" maxlength="50" />
                    </td>
                    <td align="right" style="width:12%;">
                    </td>
                    <td align="left" style="width:28%;">
                    </td>
                </tr> 
                <tr>
                    <td align="right">
                    	<label class="inline"><b>Preconsulta</b></label>
                    </td>
                    <td align="left">
                    	<input type="checkbox" id="chk_preconsulta" />
                    </td>
                    <td align="right">
                    	<label class="inline"><b>Ex&aacute;menes</b></label>
                    </td>
                    <td align="left">
                    	<input type="checkbox" id="chk_examenes" />
                    </td>
                </tr>
                <tr>
                    <td align="right">
                    	<label class="inline"><b>Toma de signos vitales</b></label>
                    </td>
                    <td align="left">
                    	<input type="checkbox" id="chk_signos_vitales" />
                    </td>
                    <td align="right">
                    	<label class="inline"><b>Cita prequir&uacute;rgica</b></label>
                    </td>
                    <td align="left">
                    	<input type="checkbox" id="chk_preqx" onclick="seleccionar_preqx();" />
                    </td>
                </tr>
                <tr>
                    <td align="right">
                    	<label class="inline"><b>Despacho</b></label>
                    </td>
                    <td align="left">
                    	<input type="checkbox" id="chk_despacho" checked="checked" />
                    </td>
                    <td align="right">
                    	<label class="inline"><b>Activo</b></label>
                    </td>
                    <td align="left">
                    	<input type="checkbox" id="chk_activo" checked="checked" />
                    </td>
                </tr> 
                <tr>
                    <td align="right">
                    	<label class="inline"><b>Registro quir&uacute;rgico</b></label>
                    </td>
                    <td align="left" colspan="2">
						<?php
                        	$combo->getComboDb("cmb_tipo_reg_cx", "", $dbTiposRegistrosHc->getTiposRegistrosHcClaseReg(2), "id_tipo_reg, nombre_tipo_reg", "&lt;Seleccione el tipo de registro&gt;", "", false, "width: 300px;");
						?>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <input type="button" id="btn_crear" class="btnPrincipal" value="Guardar" onclick="guardarNuevoTipoCita();" />
                    </td>
                </tr>
            </table>
        </div>
        <?php
        break;

    case "3"://Guarda el nuevo registro
		$id_usuario = $_SESSION["idUsuario"];
		@$accion = $utilidades->str_decode($_POST["accion"]);
		@$id_tipo_cita = $utilidades->str_decode($_POST["id_tipo_cita"]);
		@$nombre_tipo_cita = $utilidades->str_decode($_POST["nombre_tipo_cita"]);
		@$ind_activo = $utilidades->str_decode($_POST["ind_activo"]);
		@$ind_preconsulta = $utilidades->str_decode($_POST["ind_preconsulta"]);
		@$ind_examenes = $utilidades->str_decode($_POST["ind_examenes"]);
		@$ind_signos_vitales = $utilidades->str_decode($_POST["ind_signos_vitales"]);
		@$ind_preqx = $utilidades->str_decode($_POST["ind_preqx"]);
		@$ind_despacho = $utilidades->str_decode($_POST["ind_despacho"]);
		@$id_tipo_reg_cx = $utilidades->str_decode($_POST["id_tipo_reg_cx"]);
		
        $resultado_aux = $dbTiposCitas->guardaTipoCita($id_tipo_cita, $accion, $nombre_tipo_cita, $ind_activo, $ind_preconsulta,
				$ind_examenes, $ind_signos_vitales, $ind_preqx, $ind_despacho, $id_tipo_reg_cx, $id_usuario);
	?>
    <input type="hidden" id="hdd_resultado_guardar" value="<?php echo($resultado_aux); ?>" />
    <?php
        break;
		
    case "4"://Muestra el detalle del tipo de cita
        $idTipoCita = $_POST["idTipoCita"];
		
        $rta_aux = $dbTiposCitas->get_tipo_cita($idTipoCita);
		
        $check_activo = "";
        $check_preconsulta = "";
        $check_examenes = "";
        $check_signos_vitales = "";
        $check_preqx = "";
		$check_despacho = "";
		if ($rta_aux["ind_activo"] == "1") {
            $check_activo = "checked";
        }
		if ($rta_aux["ind_preconsulta"] == "1") {
            $check_preconsulta = "checked";
        }
        if ($rta_aux["ind_examenes"] == "1") {
            $check_examenes = "checked";
        }
        if ($rta_aux["ind_signos_vitales"] == "1") {
            $check_signos_vitales = "checked";
        }
        if ($rta_aux["ind_preqx"] == "1") {
            $check_preqx = "checked";
        }
        if ($rta_aux["ind_despacho"] == "1") {
            $check_despacho = "checked";
        }
        ?>
        <div>
            <input type="hidden" id="hdd_id_tipo_cita" value="<?php echo($idTipoCita); ?>" />
            <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%;">
                <tr>
                    <td align="right" style="width:30%;">
                    	<label class="inline"><b>Nombre</b></label>
                    </td>
                    <td align="left" style="width:30%;">
                    	<input type="text" id="txt_nombre_tipo_cita" onblur="trim_cadena(this);" value="<?php echo($rta_aux["nombre_tipo_cita"]); ?>" maxlength="50" />
                    </td>
                    <td align="right" style="width:12%;">
                    </td>
                    <td align="left" style="width:28%;">
                    </td>
                </tr>
                <tr>
                    <td align="right">
                    	<label class="inline"><b>Preconsulta</b></label>
                    </td>
                    <td align="left">
                    	<input type="checkbox" id="chk_preconsulta" <?php echo($check_preconsulta); ?> />
                    </td>
                    <td align="right">
                    	<label class="inline"><b>Ex&aacute;menes</b></label>
                    </td>
                    <td align="left">
                    	<input type="checkbox" id="chk_examenes" <?php echo($check_examenes); ?> />
                    </td>
                </tr>
                <tr>
                    <td align="right">
                    	<label class="inline"><b>Toma de signos vitales</b></label>
                    </td>
                    <td align="left">
                    	<input type="checkbox" id="chk_signos_vitales" <?php echo($check_signos_vitales); ?> />
                    </td>
                    <td align="right">
                    	<label class="inline"><b>Cita prequir&uacute;rgica</b></label>
                    </td>
                    <td align="left">
                    	<input type="checkbox" id="chk_preqx" onclick="seleccionar_preqx();" <?php echo($check_preqx); ?> />
                    </td>
                </tr>
                <tr>                    
                    <td align="right">
                    	<label class="inline"><b>Despacho</b></label>
                    </td>
                    <td align="left">
                    	<input type="checkbox" id="chk_despacho" <?php echo($check_despacho); ?> />
                    </td>
                    <td align="right">
                    	<label class="inline"><b>Activo</b></label>
                    </td>
                    <td align="left">
                    	<input type="checkbox" id="chk_activo" <?php echo($check_activo); ?> />
                    </td>
                </tr>
                <tr>
                    <td align="right">
                    	<label class="inline"><b>Registro quir&uacute;rgico</b></label>
                    </td>
                    <td align="left" colspan="2">
						<?php
							$bol_preqx = false;
							if ($check_preqx != "") {
								$bol_preqx = true;
							}
                        	$combo->getComboDb("cmb_tipo_reg_cx", $rta_aux["id_tipo_reg_cx"], $dbTiposRegistrosHc->getTiposRegistrosHcClaseReg(2), "id_tipo_reg, nombre_tipo_reg", "&lt;Seleccione el tipo de registro&gt;", "", $bol_preqx, "width: 300px;");
						?>
                    </td>
                </tr>
                <tr>                   
                    <td colspan="4">
                    	<?php
                        	if ($tipo_acceso_menu == "2") {
						?>
                        <input type="button" id="btnGuardar" name="btnGuardar" class="btnPrincipal" value="Guardar cambios" onclick="modificaTipoCita();" />
                        <?php
							}
						?>
                    </td>
                </tr>
            </table>
            <fieldset style="">
                <legend>Detalle:</legend>
                <?php
                $rta2_aux = $dbTiposCitasDet->get_tipos_citas_detalles($idTipoCita);
                ?>

                <table class="paginated modal_table" style="width: 99%; margin: auto;" id="tablaTiposCitaDetalles">
                    <thead>
                        <tr>
                        	<th colspan='5'>
                            	Tipos de citas detalle - <?php echo count($rta2_aux) . ' registros'; ?>
                                <?php
                                	if ($tipo_acceso_menu == "2") {
								?>
                            	<img style="float: right;margin: 0 20px 0 0;" onclick="tiposCitaDetalleNuevo(<?php echo $rta_aux['id_tipo_cita']; ?>)" src="../imagenes/Add-icon.png" title="Nuevo registro de detalle" />
                                <?php
									}
								?>
                        	</th>
                        </tr>
                        <tr>
                            <th style="width:30%;">Tipo registro</th>
                            <th style="width:30%;">Estado de atenci&oacute;n</th>
                            <th style="width:30%;">Procedimiento</th>
                            <th style="width:5%;">Orden</th>
                        </tr>
                    </thead>

                    <?php
                    if (count($rta2_aux) >= 1) {

                        foreach ($rta2_aux as $value) {
                            ?>
                            <tr onclick="tiposCitaDetalle(<?php echo $value['id_tipo_cita']; ?>, <?php echo $value['id_tipo_reg']; ?>);" id="<?php echo $value['id_tipo_reg']; ?>">
                                <td style="text-align: left;">
                                    <?php echo $value['nombre_tipo_reg']; ?>
                                </td>
                                <td style="text-align: left;">
                                    <?php echo $value['nombre_estado']; ?>
                                </td>
                                <td style="text-align: left;">
                                    <?php echo $value['nombre_procedimiento']; ?>
                                </td>
                                <td id="orden">
                                    <?php echo $value['orden']; ?>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="4">
                                No hay resultados
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </fieldset>
        </div>
        <?php
        break;
		
    case "5"://Imprime formulario de tipos de cita detalle
		@$tipo = intval($_POST['tipo'], 10);
        @$idTipoCita = $utilidades->str_decode($_POST['idTipoCita']);
        @$idTipoRegistro = $utilidades->str_decode($_POST['idTipoRegistro']);
		
		if ($tipo == 1) {
			$id_tipo_reg = "";
			$nombre_tipo_reg = "";
			$id_estado_atencion = "";
			$nombre_estado = "";
			$cod_procedimiento = "";
			$nombre_procedimiento = "";
			$ind_usuario_alt = "";
			$ind_obligatorio = "";
			$orden = "";
    ?>
    <div class="encabezado">
        <h3>Nuevo detalle de cita</h3>
    </div>
    <?php
		} else {
			$rta2_aux = $dbTiposCitasDet->get_tipos_citas_detalle($idTipoCita, $idTipoRegistro);
			
			$id_tipo_reg = $rta2_aux["id_tipo_reg"];
			$nombre_tipo_reg = $rta2_aux["nombre_tipo_reg"];
			$id_estado_atencion = $rta2_aux["id_estado_atencion"];
			$nombre_estado = $rta2_aux["nombre_estado"];
			$cod_procedimiento = $rta2_aux["cod_procedimiento"];
			$nombre_procedimiento = $rta2_aux["nombre_procedimiento"];
			$ind_usuario_alt = $rta2_aux["ind_usuario_alt"];
			$ind_obligatorio = $rta2_aux["ind_obligatorio"];
			$orden = $rta2_aux["orden_aux"];
    ?>
    <div class="encabezado">
        <h3>Detalle de cita</h3>
    </div>
    <?php
		}
	?>
    <div>
        <table border="0" style="width: 95%;margin: auto;">
        	<tr>
            	<td colspan="6">
                    <div id="advertenciasg">
                        <div class='contenedor_error' id='d_contenedor_error_rem'></div>
                        <div class='contenedor_exito' id='d_contenedor_exito_rem'></div>
                    </div> 
                </td>
            </tr>
            <tr>
                <td style="width: 25%;">
                    <label class="inline right">Tipo de registro*: </label>
                </td>
                <td colspan="3" style="width:63%;">
                    <input type="hidden" id="hdd_TipoRegistroOri" name="hdd_TipoRegistroOri" value="<?php echo($id_tipo_reg); ?>" />
                    <input type="hidden" id="hdd_TipoRegistro" name="hdd_TipoRegistro" value="<?php echo($id_tipo_reg); ?>" />
                    <label class="inline left" id="txtTipoRegistro" name="txtTipoRegistro" style="font-weight: 600;"><?php echo($nombre_tipo_reg); ?></label>
                </td>
                <td style="width:5%;"></td>
                <td style="width:7%;">
                    <img src="../imagenes/Search-icon.png" onclick="seleccionarTipoRegistro(1);" />
                </td>
            </tr>
            <tr>
                <td>  
                    <label class="inline right">Estado atenci&oacute;n*: </label>
                </td>
                <td colspan="3">
                    <input type="hidden" id="hdd_EstadoAtencion" name="hdd_EstadoAtencion" value="<?php echo($id_estado_atencion); ?>" />
                    <label class="inline left " id="txtEstadoAtencion" name="txtEstadoAtencion" style="font-weight: 600;"><?php echo($nombre_estado); ?></label>
                </td>
                <td></td>
                <td>
                    <img src="../imagenes/Search-icon.png" onclick="seleccionarEstadoAtencion(1);" />
                </td>
            </tr>
            <tr>
                <td>
                    <label class="inline right">Procedimiento: </label>
                </td>
                <td style="text-align: left;" colspan="3">
                    <input type="hidden" id="hdd_Procedimiento" name="hdd_Procedimiento" value="<?php echo($cod_procedimiento); ?>" />
                    <label class="inline left" id="txtProcedimientos" name="txtProcedimientos" style="font-weight: 600;" ><?php echo($nombre_procedimiento); ?></label>
                </td>
                <td>
                    <img src="../imagenes/icon-error.png" onclick="eliminarProcedimiento();" id="btnEliminarProcedimiento" />
                </td>
                <td>
                    <img src="../imagenes/Search-icon.png" onclick="seleccionarProcedimiento(1);" />
                </td>
            </tr>
            <tr>
                <td>
                    <label class="inline right">Atiende otro usuario profesional*:</label>
                </td>
                <td align="left" style="width:15%;">
                    <select id="cmb_usuarioAlt" name="cmb_usuarioAlt">
                        <option value>--Seleccione--</option>
                        <option value="1" <?php echo($ind_usuario_alt == '1' ? 'selected' : ''); ?>>Si</option>
                        <option value="0" <?php echo($ind_usuario_alt == '0' ? 'selected' : ''); ?>>No</option>
                    </select>
                </td>
                <td style="width:33%;">
                    <label class="inline right">Obligatorio en atenci&oacute;n*:</label>
                </td>
                <td align="left" style="width:15%;">
                    <select id="cmb_obligatorio" name="cmb_obligatorio">
                        <option value>--Seleccione--</option>
                        <option value="1" <?php echo($ind_obligatorio == '1' ? 'selected' : ''); ?>>Si</option>
                        <option value="0" <?php echo($ind_obligatorio == '0' ? 'selected' : ''); ?>>No</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="inline right">Orden*: </label>
                </td>
                <td align="left">
                    <input type="text" id="txtOrden" name="txtOrden" style="width: 40px;" value="<?php echo trim($orden); ?>" onkeypress="solo_numeros(event, false);" maxlength="2" />
                </td>
            </tr>
            <tr>
            	<td colspan="6">
                	<div style="max-height:300px; overflow:auto;">
                    <table class="modal_table" style="width:99%;">
                        <tr id="tabla_encabezado">
                            <th style="width:47%;">Tipo de cita destino</th>
                            <th style="width:47%;">Tipo de registro de destino</th>
                            <th style="width:6%;" id="icono">
                            	<?php
                                	if ($tipo_acceso_menu == "2") {
								?>
                                <div class="Add-icon full" onclick="agregar_reg_remision();" title="Nuevo registro de remisi&oacute;n"></div>                    	
                                <?php
									}
								?>
                            </th>
                        </tr>
                        <?php
							//Listado de tipos de citas
							$lista_tipos_citas = $dbTiposCitas->getTiposcitas();
							$lista_tipos_reg_hc_base = $dbTiposRegistrosHc->getListaTiposRegistroHcTipoCita($idTipoCita);
							
							$cant_remisiones = 0;
							if ($tipo == 2) {
								//Se buscan los registros de detalle de remisiones
								$lista_tipos_citas_det_remisiones = $dbTiposCitasDet->get_lista_tipos_citas_det_remisiones($idTipoCita, $idTipoRegistro);
								
								$cant_remisiones = count($lista_tipos_citas_det_remisiones);
								if ($cant_remisiones > 0) {
									for ($i = 0; $i < $cant_remisiones; $i++) {
										$det_remision_aux = $lista_tipos_citas_det_remisiones[$i];
										
						?>
                        <tr id="tr_det_remision_<?php echo($i); ?>">
                        	<td align="left" class="td_reducido">
                            	<?php
                                	$combo->getComboDb("cmb_tipo_cita_dest_".$i, $det_remision_aux["id_tipo_cita_dest"], $lista_tipos_citas, "id_tipo_cita,nombre_tipo_cita", "--Seleccione--", "seleccionar_tipo_cita_dest(this.value, ".$i.");", true, "width:100%;");
								?>
                            </td>
                        	<td align="left" class="td_reducido">
                            	<div id="d_tipo_reg_hc_dest_<?php echo($i); ?>">
                                	<?php
										$lista_tipos_reg_hc_aux = $dbTiposRegistrosHc->getListaTiposRegistroHcTipoCita($det_remision_aux["id_tipo_cita_dest"]);
										
										$combo->getComboDb("cmb_tipo_reg_hc_dest_".$i, $det_remision_aux["id_tipo_reg_dest"], $lista_tipos_reg_hc_aux, "id_tipo_reg,nombre_tipo_reg", "--Seleccione--", "", true, "width:100%;");
									?>
                                </div>
                            </td>
                    		<td align="center">
                            	<?php
                                	if ($tipo_acceso_menu == "2") {
								?>
								<div class="Error-icon" onclick="borrar_reg_remision(<?php echo($i); ?>);" title="Borrar registro de remisi&oacute;n"></div>
                                <?php
									}
								?>
		                    </td>
                        </tr>
                        <?php
									}
								}
							}
							
							for ($i = $cant_remisiones; $i < 20; $i++) {
						?>
                        <tr id="tr_det_remision_<?php echo($i); ?>" style="display:none;">
                        	<td align="left" class="td_reducido">
                            	<?php
                                	$combo->getComboDb("cmb_tipo_cita_dest_".$i, $idTipoCita, $lista_tipos_citas, "id_tipo_cita,nombre_tipo_cita", "--Seleccione--", "seleccionar_tipo_cita_dest(this.value, ".$i.");", true, "width:100%;");
								?>
                            </td>
                        	<td align="left" class="td_reducido">
                            	<div id="d_tipo_reg_hc_dest_<?php echo($i); ?>">
	                            	<?php
										$combo->getComboDb("cmb_tipo_reg_hc_dest_".$i, "", $lista_tipos_reg_hc_base, "id_tipo_reg,nombre_tipo_reg", "--Seleccione--", "", true, "width:100%;");
									?>
                                </div>
                            </td>
                    		<td align="center">
                            	<?php
                                	if ($tipo_acceso_menu == "2") {
								?>
								<div class="Error-icon" onclick="borrar_reg_remision(<?php echo($i); ?>);" title="Borrar registro de remisi&oacute;n"></div>
                                <?php
									}
								?>
		                    </td>
                        </tr>
                        <?php
							}
						?>
                    </table>
                    <input type="hidden" id="hdd_cant_reg_remision" value="<?php echo($cant_remisiones); ?>" />
                    </div>
                    <br />
                </td>
            </tr>
            <tr>
                <td colspan="6">
					<?php
						if ($tipo_acceso_menu == "2") {
	                       	if ($tipo == 1) {
					?>
                    <input type="button" id="btnGuardar" name="btnGuardar" class="btnPrincipal" value="Crear registro" onclick="nuevoTipoCitaDetalle(<?php echo $idTipoCita; ?>);" />
                    <?php	
							} else {
					?>
                    <input type="button" id="btnGuardar" name="btnGuardar" class="btnPrincipal" value="Guardar cambios" onclick="guardarTipoCitaDetalle(<?php echo($idTipoCita); ?>, <?php echo($idTipoRegistro); ?>);" />
                    <input type="button" id="btnGuardar" name="btnGuardar" class="btnPrincipal" value="Eliminar registro" onclick="eliminarTipoCitaDetalle(<?php echo($idTipoCita); ?>, <?php echo($idTipoRegistro); ?>)" />
                    <?php
							}
						}
					?>
                </td>
            </tr>
        </table>
        <input type="hidden" id="hdd_resultado_agregar" name="hdd_resultado_agregar" />
    </div>
    <?php
        break;

    case "6"://Imprime formulario de maestro prodecimientos
        ?>
        <div class="encabezado">
            <h3>Procedimientos</h3>
        </div>
        <div>
            <form id="frmBuscarProcedimiento" name="frmBuscarProcedimiento"> 
                <table style="width: 100%;">
                    <tr>
                        <td>                       
                            <input type="text" id="txtProcedimiento" name="txtProcedimiento" placeholder="Codigo o nombre del procedimiento" onblur="trim_cadena(this);" />
                        </td>
                        <td style="width: 20%;">
                            <input type="submit" id="btnBuscarProcedimiento" nombre="btnBuscarProcedimiento" value="Buscar" class="btnPrincipal peq" onclick="buscarProcedimiento();"/>              
                        </td>
                    </tr>
                </table>
            </form>
            <div id="procedimientos"></div>
        </div>
        <?php
        break;
		
    case "7"://Imprime listado de la tabla maestro prodecimientos
        $parametro = $utilidades->str_decode($_POST['parametro']);

        $rta_aux = $dbMaestroProcedimientos->getProcedimientos($parametro);
        ?>
        <table class="paginated modal_table" style="width: 99%; margin: auto;">
            <thead>
                <tr>
                    <th style="width:5%;">C&oacute;digo</th>
                    <th style="width:36%;">Nombre</th>
                </tr>
            </thead>
            <?php
            if (count($rta_aux) >= 1) {
                foreach ($rta_aux as $value) {
                    ?>
                    <tr onclick="agregarProcedimiento('<?php echo $value['cod_procedimiento']; ?>', '<?php echo $value['nombre_procedimiento']; ?>')">
                        <td><?php echo $value['cod_procedimiento']; ?></td>
                        <td style="text-align: left;"><?php echo $value['nombre_procedimiento']; ?></td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="2">
                        No hay resultados
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>


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
		
    case "8"://Imprime formulario de Estados de Atencion
        $rta_aux = $dbEstadosAtencion->getEstadosatencion();
        ?>
        <div class="encabezado">
            <h3>Estados de atenci&oacute;n</h3>
        </div>
        <br />
        <table class="paginated modal_table" style="width: 99%; margin: auto;">
            <thead>
                <tr>
                    <th style="width:5%;">C&oacute;digo</th>
                    <th style="width:36%;">Nombre</th>
                </tr>
            </thead>
            <?php
            if (count($rta_aux) >= 1) {
                foreach ($rta_aux as $value) {
                    ?>
                    <tr onclick="agregarEstadosAtencion(<?php echo $value['id_estado_atencion']; ?>, '<?php echo $value['nombre_estado']; ?>');">
                        <td><?php echo $value['id_estado_atencion']; ?></td>
                        <td style="text-align: left;"><?php echo $value['nombre_estado']; ?></td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="2">
                        No hay resultados
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>

        <?php
        break;

    case "9"://Imprime formulario de Estados de Atencion
        $rta_aux = $dbTiposRegistrosHc->getTiposRegistrosHc();
        ?>
        <div class="encabezado">
            <h3>Tipo de registro Historia Clinica</h3>
        </div>
        <div>
            <div id="procedimientos"></div>
        </div>
        <br />
        <div style="max-height:300px; overflow:auto;">
        <table class="paginated modal_table" style="width: 99%; margin: auto;">
            <thead>
                <tr>
                    <th style="width:5%;">C&oacute;digo</th>
                    <th style="width:36%;">Nombre</th>
                </tr>
            </thead>
            <?php
            if (count($rta_aux) >= 1) {
                foreach ($rta_aux as $value) {
                    ?>
                    <tr onclick="agregarTipoRegistroHC(<?php echo $value['id_tipo_reg']; ?>, '<?php echo $value['nombre_tipo_reg']; ?>');">
                        <td><?php echo $value['id_tipo_reg']; ?></td>
                        <td style="text-align: left;"><?php echo $value['nombre_tipo_reg']; ?></td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="2">
                        No hay resultados
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
		</div>
        <?php
        break;
		
    case "10"://Eliminar|Edita|Crea Tipo de cita detalle
		@$id_tipo_cita = $utilidades->str_decode($_POST['id_tipo_cita']);
		@$id_tipo_reg_ori = $utilidades->str_decode($_POST['id_tipo_reg_ori']);
		@$id_tipo_reg = $utilidades->str_decode($_POST['id_tipo_reg']);
		@$accion = $utilidades->str_decode($_POST['accion']);
		@$estadoAtencion = $utilidades->str_decode($_POST['estadoAtencion']);
		@$procedimiento = $utilidades->str_decode($_POST['procedimiento']);
		@$orden = $utilidades->str_decode($_POST['orden']);
		@$usuarioAlt = $utilidades->str_decode($_POST['usuarioAlt']);
		@$ind_obligatorio = $utilidades->str_decode($_POST['ind_obligatorio']);
		@$cant_reg_remision = intval($_POST['cant_reg_remision'], 10);
		
		$arr_det_remisiones = array();
		for ($i = 0; $i < $cant_reg_remision; $i++) {
			$arr_det_remisiones[$i]["id_tipo_cita_dest"] = $utilidades->str_decode($_POST['id_tipo_cita_dest_'.$i]);
			$arr_det_remisiones[$i]["id_tipo_reg_hc_dest"] = $utilidades->str_decode($_POST['id_tipo_reg_hc_dest_'.$i]);
		}
		
		$rta_aux = $dbTiposCitasDet->crea_eliminar_edita_tipos_citas_detalle($id_tipo_cita, $id_tipo_reg_ori, $id_tipo_reg, $accion, $orden, $estadoAtencion,
				$procedimiento, $usuario, $usuarioAlt, $ind_obligatorio, $arr_det_remisiones);
		
		echo $rta_aux;
		break;
		
	case "11": //Combo de tipos de registros para remisiones
		@$id_tipo_cita = $utilidades->str_decode($_POST["id_tipo_cita"]);
		@$indice = $utilidades->str_decode($_POST["indice"]);
		
		//Se obtiene la lista de tipos de registros
		$lista_tipos_reg_hc = $dbTiposRegistrosHc->getListaTiposRegistroHcTipoCita($id_tipo_cita);
		
		$combo->getComboDb("cmb_tipo_reg_hc_dest_".$indice, "", $lista_tipos_reg_hc, "id_tipo_reg,nombre_tipo_reg", "--Seleccione--", "", true, "width:100%;");
		break;
}
?>
