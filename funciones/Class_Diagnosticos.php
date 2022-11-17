<?php
/*
  Pagina para crear diagnosticos generales
  Autor: Helio Ruber López - 14/02/2014
 */
require_once("../db/DbListas.php");
require_once("../db/DbDiagnosticos.php");
require_once("../db/DbHistoriaClinica.php");
require_once("Class_Combo_Box.php");

class Class_Diagnosticos {

    public function getFormularioDiagnosticos($id_hc, $ind_ojos = true) {
        $dbListas = new DbListas();
        $dbDiagnosticos = new DbDiagnosticos();
        $dbHistoriClinica = new DbHistoriaClinica();
        $combo = new Combo_Box();

        $tabla_ojos = $dbListas->getListaDetalles(14);
        $tabla_hc_diagnosticos = $dbDiagnosticos->getHcDiagnostico($id_hc);

        if (count($tabla_hc_diagnosticos) == 0) {
            //Se buscan diagnósticos en registros de historia clínica anteriores dentro de la misma atención
            $hc_ant_obj = $dbHistoriClinica->getHistoriaClinicaRegAnt($id_hc);
            if (isset($hc_ant_obj["id_hc"])) {
                $tabla_hc_diagnosticos = $dbDiagnosticos->getHcDiagnostico($hc_ant_obj["id_hc"]);
            }
        }
        $cant_diagnosticos = count($tabla_hc_diagnosticos);
        if ($cant_diagnosticos <= 4) {
            $cant_diagnosticos = 4;
        }
        ?>
        <input type="hidden" name="lista_tabla" id="lista_tabla" value="<?php echo($cant_diagnosticos); ?>" />
        <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:90%;">
            <tr>
                <td colspan="4">
                    <div id="d_diagnosticos_ant" style="display:none;"></div>
                    <div class="texto_subtitulo" style="float:right; cursor:pointer;" onclick="copiar_dignosticos_ant(<?php echo($id_hc); ?>);" >
                        Copiar diagn&oacute;sticos anteriores
                        <img src="../imagenes/copy_opt.png" class="img_button no-margin" title="Copiar diagn&oacute;sticos anteriores" />
                    </div>
                </td>
            </tr>
            <tr>
                <td align="center" style="width:12%;">
            <h7><b>C&oacute;digo CIEX</b></h7>
        </td>
        <?php
        if ($ind_ojos) {
            ?>
            <td align="center" style="width:76%;" colspan="2">
            <h7><b>Diagn&oacute;stico</b></h7>
            </td>
            <td align="center" style="width:12%;">
            <h7><b>Ojo</b></h7>
            </td>
            <?php
        } else {
            ?>
            <td align="center" style="width:88%;" colspan="2">
            <h7><b>Diagn&oacute;stico</b></h7>
            </td>
            <?php
        }
        ?>
        </tr>
        </table>
        <div id="texto_diagnostico"></div>
        <?php
        for ($i = 1; $i <= 10; $i++) {
            $tabla_diagnosticos = $this->obtener_diagnostico($tabla_hc_diagnosticos, $i);
            if (count($tabla_diagnosticos) == 0) {
                $cod_ciex = "";
                $val_ojo = "";
                $texto_ciex = "";
            } else {
                $cod_ciex = $tabla_diagnosticos[0];
                $val_ojo = $tabla_diagnosticos[1];
                $texto_ciex = $tabla_diagnosticos[2];
            }
            ?>
            <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:90%; display:none;" id="tabla_diag_<?php echo($i); ?>">
                <tr>
                    <td align="center" style="width:12%;">
                        <input type="text" name="ciex_diagnostico_<?php echo($i); ?>" id="ciex_diagnostico_<?php echo($i); ?>" value="<?php echo($cod_ciex); ?>" style="width:100px;" maxlength="6" class="input no-margin" onblur="convertirAMayusculas(this); trim_cadena(this); buscar_diagnosticos_ciex('<?php echo($i); ?>');" />
                        <input type="hidden" name="hdd_ciex_diagnostico_<?php echo($i); ?>" id="hdd_ciex_diagnostico_<?php echo($i); ?>" value="<?php echo($cod_ciex); ?>" />
                    </td>
                    <?php
                    if ($ind_ojos) {
                        $porcentaje_aux = 71;
                    } else {
                        $porcentaje_aux = 83;
                    }
                    ?>
                    <td align="left" style="width:<?php echo($porcentaje_aux); ?>%;">
                        <h6 class="no-margin">
                            <div id="texto_diagnostico_<?php echo($i); ?>"><?php echo($texto_ciex); ?></div>
                        </h6>
                    </td>
                    <td align="center" style="width:5%;">
                        <div class="d_buscar" onclick="abrir_buscar_diagnostico('<?php echo($i); ?>');"></div>
                    </td>
                    <?php
                    if ($ind_ojos) {
                        ?>
                        <td align="center" style="width:12%;">
                            <?php
                            $combo->getComboDb("valor_ojos_" . $i, $val_ojo, $tabla_ojos, "id_detalle, nombre_detalle", " ", "", "", "width:90px;", "", "select no-margin");
                            ?>
                        </td>
                        <?php
                    } else {
                        ?>
                    <input id="valor_ojos_<?php echo($i); ?>" name="valor_ojos_<?php echo($i); ?>" value="" type="hidden" />
                    <?php
                }
                ?>
            </tr>
            </table>
            <?php
        }
        ?>
        <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:90%;">
            <tr>
                <td>
                    <div class="agregar_alemetos" onclick="agregar_tabla_daig();"></div>
                    <div class="restar_alemetos" onclick="restar_tabla_daig();"></div>
                </td>
            </tr>
        </table>
        <script>
            mostrar_lista_tabla();
        </script>
        <?php
    }

    /**
     * Se obtiene le diagnosticos a partir del array y el orden
     */
    public function obtener_diagnostico($tabla_diagnostico_hc, $orden) {
        $array_diagnosticos_hc = array();
        foreach ($tabla_diagnostico_hc as $fila_diagnostico_hc) {
            $cod_ciex = $fila_diagnostico_hc["cod_ciex"];
            $id_ojo = $fila_diagnostico_hc["id_ojo"];
            $orden_hc = $fila_diagnostico_hc["orden"];
            $texto_ciex = $fila_diagnostico_hc["nombre"];
            $array_diagnosticos_hc = array();
            if ($orden == $orden_hc) {
                $array_diagnosticos_hc[0] = $cod_ciex;
                $array_diagnosticos_hc[1] = $id_ojo;
                $array_diagnosticos_hc[2] = $texto_ciex;
                break;
            }
        }
        return $array_diagnosticos_hc;
    }

}
?>
