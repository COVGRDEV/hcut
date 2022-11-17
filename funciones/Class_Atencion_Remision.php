<?php
/*
  Pagina para crear formularios de remisiones en citas
  Autor: Feisar Moreno - 01/06/2015
 */
require_once("../db/DbAdmision.php");
require_once("../db/DbHistoriaClinica.php");
require_once("../db/DbMaestroExamenes.php");
require_once("../db/DbTiposCitasDetalle.php");
require_once("../db/DbListas.php");
require_once("Class_Combo_Box.php");
require_once("../funciones/Utilidades.php");
require_once("../db/Dbremisiones.php");

class Class_Atencion_Remision {

    public function getFormularioRemisiones($id_hc, $id_admision, $funcion_finalizar, $obj_exito) {

        $dbAdmision = new DbAdmision();
        $dbHistoriaClinica = new DbHistoriaClinica();
        $dbMaestroExamenes = new DbMaestroExamenes();
        $dbTiposCitasDetalle = new DbTiposCitasDetalle();
        $dbListas = new DbListas();
        $combo = new Combo_Box();

        $admision_obj = $dbAdmision->get_admision($id_admision);
        $id_tipo_cita = $admision_obj["id_tipo_cita"];
        $id_lugar_cita = $admision_obj["id_lugar_cita"];

        $hc_obj = $dbHistoriaClinica->getHistoriaClinicaId($id_hc);
        $id_tipo_reg = $hc_obj["id_tipo_reg"];

        $lista_tipos_citas_det_remisiones = $dbTiposCitasDetalle->get_lista_tipos_citas_det_remisiones($id_tipo_cita, $id_tipo_reg);
        $lista_lugares_remision = $dbListas->getListaDetalles(12, 1);
        ?>
        <div class="formulario" style="width:100%; display:block;">
            <div class="encabezado">
                <h3>Enviar paciente a</h3>
            </div>
            <br />
            <div style="width: 100%; display: block;">
                <div class="contenedor_error" id="d_contenedor_error_rem"></div>
                <div class="contenedor_exito" id="d_contenedor_exito_rem"></div>
            </div>
            <input type="hidden" id="hdd_id_tipo_cita_origen" value="<?php echo($id_tipo_cita); ?>" />
            <input type="hidden" id="hdd_id_tipo_reg_origen" value="<?php echo($id_tipo_reg); ?>" />
            <input type="hidden" id="hdd_id_lugar_cita_origen" value="<?php echo($id_lugar_cita); ?>" />
            <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:95%;">
                <tr>
                    <td align="right" style="width:20%;">
                        <label class="inline" for="cmb_tipo_cita_reg"><b>Enviar a*</b></label>
                    </td>
                    <td align="left" style="width:80%;">
                        <select id="cmb_tipo_cita_rem" class="select" style="width:200px;" onchange="seleccionar_dest_remision(this.value, '<?php echo($id_admision); ?>');">
                            <option value="-1">&lt;Seleccione&gt;</option>
                            <?php
                            foreach ($lista_tipos_citas_det_remisiones as $reg_aux) {
                                $texto_aux = $reg_aux["nombre_tipo_reg"];
                                if ($id_tipo_cita != $reg_aux["id_tipo_cita_dest"]) {
                                    $texto_aux = $reg_aux["nombre_tipo_cita"];
                                }
                                ?>
                                <option value="<?php echo($reg_aux["id_tipo_cita_dest"] . "-" . $reg_aux["id_tipo_reg_dest"] . "-" . $reg_aux["ind_examenes"]); ?>"><?php echo($texto_aux); ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr id="tr_usuario_atencion_remision" style="display:none;">
                    <td align="right">
                        <label class="inline" for="cmb_usuario_atencion_remision"><b>Usuario que atender&aacute;*</b></label>
                    </td>
                    <td align="left">
                        <div id="d_usuario_atencion_remision">
                            <select id="cmb_usuario_rem">
                                <option value="">&lt;No se encontraron usuarios disponibles&gt;</option>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr id="tr_lugar_remision" style="display:none;">
                    <td align="right">
                        <label class="inline" for="cmb_lugar_remision"><b>Lugar*</b></label>
                    </td>
                    <td align="left">
                        <?php
                        $combo->getComboDb("cmb_lugar_remision", "", $lista_lugares_remision, "id_detalle,nombre_detalle", "--Seleccione--", "", 1, "width:200px;", "", "select");
                        ?>
                    </td>
                </tr>
                <tr id="tr_cant_examenes_remision" style="display:none;">
                    <td align="right">
                        <label class="inline" for="cmb_cant_examenes_remision"><b>Cantidad de ex&aacute;menes*</b></label>
                    </td>
                    <td align="left">
                        <select id="cmb_cant_examenes_remision" class="select" style="width:200px;" onchange="mostrar_examenes_remision(this.value);">
                            <option value="0">&lt;Seleccione&gt;</option>
                            <?php
                            for ($i = 1; $i <= 10; $i++) {
                                ?>
                                <option value="<?php echo($i); ?>"><?php echo($i); ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr id="tr_examenes_remision" style="display:none;">
                    <td align="center" colspan="2">
                        <table class="modal_table">
                            <tr>
                                <th align="center" style="width:85%;">Examen*</th>
                                <th align="center" style="width:15%;">Ojo*</th>
                            </tr>
                            <?php
                            $lista_examenes = $dbMaestroExamenes->get_lista_examenes(1);
                            $lista_ojos = $dbListas->getListaDetalles(14);

                            for ($i = 0; $i < 10; $i++) {
                                ?>
                                <tr id="tr_examen_remision_<?php echo($i); ?>" style="display:none">
                                    <td align="center">
                                        <?php
                                        echo($combo->getComboDb("cmb_examen_remision_" . $i, "", $lista_examenes, "id_examen, nombre_examen", "&lt;Seleccione un examen&gt;", "", true, "width:95%;"));
                                        ?>
                                    </td>
                                    <td align="center">
                                        <?php
                                        echo($combo->getComboDb("cmb_ojo_examen_remision_" . $i, "", $lista_ojos, "id_detalle, nombre_detalle", "&lt;Seleccione&gt;", "", true, "width:95%;"));
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            <tr id="tr_examen_remision_vacio">
                                <td align="center" colspan="2">Seleccione una cantidad</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        <label class="inline" for="cmb_tipo_cita_reg"><b>Observaciones</b></label>
                    </td>
                    <td align="left">
                        <textarea id="txt_observaciones_remision" class="textarea_op" onblur="trim_cadena(this);"></textarea>
                    </td>
                </tr>
                <tr id="tr_examenes_remision_guardando" style="display:none;">
                    <td align="center" colspan="2">
                        <img src="../imagenes/ajax-loader.gif" />
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="2">
                        <input class="btnPrincipal" type="button" id="btn_enviar_remision" value="Enviar" onclick="guardar_atencion_remision(<?php echo($id_hc); ?>, <?php echo($id_admision); ?>, '', '');"/>
                        &nbsp;&nbsp;
                        <input class="btnPrincipal" type="button" id="btn_finalizar_enviar_remision" value="Finalizar y Enviar" onclick="guardar_atencion_remision(<?php echo($id_hc); ?>, <?php echo($id_admision); ?>, '<?php echo($funcion_finalizar); ?>', '<?php echo($obj_exito); ?>');"/>
                    </td>
                </tr>
            </table>
            <div id="d_guardar_atencion_remision" style="display:none;"></div>
        </div>
        <?php
    }

}
?>
