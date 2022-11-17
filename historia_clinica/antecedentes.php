<?php
	require_once("../db/DbAntecedentes.php");
	require_once("antecedentes_funciones.php");
	
	$dbAntecedentes = new DbAntecedentes();
	
	//Formato actual de antecedentes
	$lista_antecedentes = $dbAntecedentes->get_lista_antecedentes_medicos_hc2($id_hc_consulta);
	$cantidad_antecedentes = count($lista_antecedentes);
?>
<input type="hidden" id="hdd_cant_antecedentes" value="<?php echo(count($lista_antecedentes)); ?>" />
<table border="0" cellpadding="3" cellspacing="0" align="center" style="width:98%;">
    <tr>
        <td align="center">
            <h5 style="margin:0px; display:inline;">Antecedentes</h5>
        </td>
    </tr>
    <tr>
        <td align="left">
            <div id="d_lista_antecedentes">
                <input type="hidden" id="hdd_cant_antecedentes" name="hdd_cant_antecedentes" value="<?php echo($cantidad_antecedentes); ?>" />
                <!--<img src="../imagenes/add_elemento.png" class="img_button" style="float:right;" title="Ver contactos" onclick="abrir_cerrar_contactos_antecedentes_med();" />-->
                <h6>
                    <table border="0" style="width:100%; margin:auto;">
                        <?php
                        $arr_antec_extensiones = array();
                        for ($i = 0; $i < $cantidad_antecedentes; $i++) {
                            $antecedente_aux = $lista_antecedentes[$i];
                            ?>
                            <tr>
                                <td align="left" style="width:12%;">
                                    <input type="hidden" id="hdd_antecedente_medico_<?php echo($i); ?>" value="<?php echo($antecedente_aux["id_antecedentes_medicos"]); ?>" />
                                    <b><?php echo($antecedente_aux["titulo_antecedente_medico"] . ":"); ?></b>
                                </td>
                                <td align="left" style="width:88%;">
                                    <div id="txt_texto_antecedente_<?php echo($i); ?>"><?php echo($antecedente_aux["texto_antecedente"]); ?></div>
                                </td>
                            </tr>
                            <tr style="height:10px;"></tr>
                            <?php
                        }
                        ?>
                    </table>
                </h6>
            </div>
        </td>
    </tr>
</table>
