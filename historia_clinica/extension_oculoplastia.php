<?php
	require_once("../db/DbConsultasOculoplastia.php");
	require_once("../funciones/Class_Combo_Box.php");
	
	$dbConsultasOculoplastia = new DbConsultasOculoplastia();
	
	$combo = new Combo_Box();
	
	//Lista para campos Sí/No
	$lista_si_no_ret = array();
	$lista_si_no_ret[0]["id"] = "1";
	$lista_si_no_ret[0]["valor"] = "S&iacute;";
	$lista_si_no_ret[1]["id"] = "0";
	$lista_si_no_ret[1]["valor"] = "No";
	
	//Array valores párpados
	$cadena_parpados = "'0'";
	for ($i = 0; $i <= 5; $i++) {
		if ($cadena_parpados != "") {
			$cadena_parpados .= ",";
		}
		if ($i > 0 && $i < 5) {
			$cadena_parpados .= "'-".$i.",0',";
			$cadena_parpados .= "'-".$i.",5',";
			$cadena_parpados .= "'+".$i.",0',";
			$cadena_parpados .= "'+".$i.",5'";
		} else if ($i == 0) {
			$cadena_parpados .= "'-".$i.",5',";
			$cadena_parpados .= "'+".$i.",5'";
		} else {
			$cadena_parpados .= "'-".$i.",0',";
			$cadena_parpados .= "'+".$i.",0'";
		}
	}
?>
<script id="ajax">
	var array_parpados = [<?php echo($cadena_parpados) ?>];
	
	$(function() {
		var Tags_parpados = [<?php echo($cadena_parpados) ?>];
		
		$("#txt_dmr_od").autocomplete({ source: Tags_parpados });
		$("#txt_dmr_oi").autocomplete({ source: Tags_parpados });
		$("#txt_fen_od").autocomplete({ source: Tags_parpados });
		$("#txt_fen_oi").autocomplete({ source: Tags_parpados });
	});
</script>
<?php
	$oculoplastia_obj = $dbConsultasOculoplastia->getConsultaOculoplastia($id_hc_consulta);
	$lista_oculoplastia_antec = $dbConsultasOculoplastia->getListaConsultasOculoplastiaAntec($id_hc_consulta);
	$lista_oculoplastia_compl = $dbConsultasOculoplastia->getListaConsultasOculoplastiaCompl($id_hc_consulta);
	
	if (isset($oculoplastia_obj["id_hc"])) {
		$exoftalmometria_base = $oculoplastia_obj["exoftalmometria_base"];
		$exoftalmometria_od = $oculoplastia_obj["exoftalmometria_od"];
		$exoftalmometria_oi = $oculoplastia_obj["exoftalmometria_oi"];
		$observ_orbita = $oculoplastia_obj["observ_orbita"];
		$observ_cejas = $oculoplastia_obj["observ_cejas"];
		$fme_od = $oculoplastia_obj["fme_od"];
		$fme_oi = $oculoplastia_obj["fme_oi"];
		$dmr_od = $oculoplastia_obj["dmr_od"];
		$dmr_oi = $oculoplastia_obj["dmr_oi"];
		$fen_od = $oculoplastia_obj["fen_od"];
		$fen_oi = $oculoplastia_obj["fen_oi"];
		$observ_parpados = $oculoplastia_obj["observ_parpados"];
		$observ_pestanas = $oculoplastia_obj["observ_pestanas"];
		$gm_expresibilidad_od = $oculoplastia_obj["gm_expresibilidad_od"];
		$gm_expresibilidad_oi = $oculoplastia_obj["gm_expresibilidad_oi"];
		$gm_calidad_expr_od = $oculoplastia_obj["gm_calidad_expr_od"];
		$gm_calidad_expr_oi = $oculoplastia_obj["gm_calidad_expr_oi"];
		$observ_glandulas_meib = $oculoplastia_obj["observ_glandulas_meib"];
		$prueba_irrigacion_od = $oculoplastia_obj["prueba_irrigacion_od"];
		$prueba_irrigacion_oi = $oculoplastia_obj["prueba_irrigacion_oi"];
		$observ_via_lagrimal = $oculoplastia_obj["observ_via_lagrimal"];
	} else {
		$exoftalmometria_base = "";
		$exoftalmometria_od = "";
		$exoftalmometria_oi = "";
		$observ_orbita = "";
		$observ_cejas = "";
		$fme_od = "";
		$fme_oi = "";
		$dmr_od = "";
		$dmr_oi = "";
		$fen_od = "";
		$fen_oi = "";
		$observ_parpados = "";
		$observ_pestanas = "";
		$gm_expresibilidad_od = "";
		$gm_expresibilidad_oi = "";
		$gm_calidad_expr_od = "";
		$gm_calidad_expr_oi = "";
		$observ_glandulas_meib = "";
		$prueba_irrigacion_od = "";
		$prueba_irrigacion_oi = "";
		$observ_via_lagrimal = "";
	}
?>
<input type="hidden" id="hdd_cant_oculoplastia_antec" value="<?php echo(count($lista_oculoplastia_antec)); ?>" />
<input type="hidden" id="hdd_cant_oculoplastia_compl" value="<?php echo(count($lista_oculoplastia_compl)); ?>" />
<table border="0" cellpadding="3" cellspacing="0" align="center" style="width:98%;">
    <tr>
        <td align="center" colspan="3">
            <h5 style="margin: 10px">Antecedentes de Oculoplastia</h5>
        </td>
    </tr>
    <?php
    	for ($i = 0; $i < count($lista_oculoplastia_antec); $i++) {
			$oculoplastia_antec_aux = $lista_oculoplastia_antec[$i];
	?>
    <tr>
    	<td align="left" style="width:22%;">
        	<?php echo($oculoplastia_antec_aux["nombre_detalle"]); ?>:
        </td>
        <td align="left" style="width:63%;">
        	<input type="hidden" id="hdd_id_antec_ocp_<?php echo($i); ?>" value="<?php echo($oculoplastia_antec_aux["id_detalle"]); ?>" />
        	<input type="text" id="txt_texto_antec_ocp_<?php echo($i); ?>" value="<?php echo($oculoplastia_antec_aux["texto_antec_ocp"]); ?>" maxlength="200" class="no-margin" style="width:99%;" />
        </td>
        <td align="left" style="width:15%;">
        	<input type="text" id="txt_fecha_antec_ocp_<?php echo($i);?>" class="input input_hc" maxlength="10" style="width:100px;" value="<?php echo($oculoplastia_antec_aux["fecha_antec_ocp_t"]); ?>" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" />
        </td>
    </tr>
    <?php
		}
	?>
	<tr>
    	<td align="left">
        	Está tomando
        </td>
        <td align="center" colspan="2">
        	<table border="0" cellpadding="3" cellspacing="0" align="center" style="width:98%;">
            	<?php
                	for ($i = 0; $i < count($lista_oculoplastia_compl); $i++) {
						$oculoplastia_compl_aux = $lista_oculoplastia_compl[$i];
						
						if ($i % 3 == 0) {
				?>
                <tr>
                	<?php
						}
						
						$checked_aux = "";
						if ($oculoplastia_compl_aux["ind_compl_ocp"] == "1") {
							$checked_aux = 'checked="checked" ';
						}
					?>
                    <td align="left" style="width:33%;">
                    	<input type="hidden" id="hdd_id_compl_ocp_<?php echo($i); ?>" value="<?php echo($oculoplastia_compl_aux["id_detalle"]); ?>" />
                    	<input type="checkbox" id="chk_compl_ocp_<?php echo($i); ?>" class="no-margin" <?php echo($checked_aux); ?>/>
						<?php echo($oculoplastia_compl_aux["nombre_detalle"]); ?>
                    </td>
                    <?php
						if ($i % 3 == 2 || $i == count($lista_oculoplastia_compl) - 1) {
					?>
                </tr>
                <?php
						}
					}
				?>
            </table>
        </td>
    </tr>
</table>
<br />
<table border="0" cellpadding="0" cellspacing="0" align="center" style="width:98%;">
	<tr>
		<td align="center" class="td_tabla">
			<div class="odoi_t">
				<div class="od_t"><h5 style="margin: 0px;">OD</h5></div>
				<div class="oi_t"><h5 style="margin: 0px;">OI</h5></div>
			</div>
		</td>
	</tr>
</table>
<table border="0" cellpadding="3" cellspacing="0" align="center" style="width:98%;">
    <tr>
        <td align="center" colspan="4">
            <h5 style="margin:10px">&Oacute;rbita</h5>
        </td>
    </tr>
    <tr>
        <td align="center" colspan="4">
            <b>Exoftalmometr&iacute;a</b>
        </td>
    </tr>
    <tr>
    	<td align="center" style="width:35%;">
        	<div style="float:left;"><img src="../imagenes/copy_opt.png" onclick='copiar_campo_oculoplastia("txt_exoftalmometria_oi", "txt_exoftalmometria_od");'></div>
        	<input type="text" id="txt_exoftalmometria_od" value="<?php echo($exoftalmometria_od); ?>" maxlength="3" onkeypress="return solo_numeros(event, false);" class="no-margin" style="width:50px;" />
        </td>
    	<td align="right" style="width:15%;">Base</td>
    	<td align="left" style="width:15%;">
        	<input type="text" id="txt_exoftalmometria_base" value="<?php echo($exoftalmometria_base); ?>" maxlength="3" onkeypress="return solo_numeros(event, false);" class="no-margin" style="width:50px;" />
        </td>
    	<td align="center" style="width:35%;">
        	<div style="float:right;"><img src="../imagenes/copy_opt.png" onclick='copiar_campo_oculoplastia("txt_exoftalmometria_od", "txt_exoftalmometria_oi");'></div>
        	<input type="text" id="txt_exoftalmometria_oi" value="<?php echo($exoftalmometria_oi); ?>" maxlength="3" onkeypress="return solo_numeros(event, false);" class="no-margin" style="width:50px;" />
        </td>
    </tr>
    <tr>
    	<td align="center" colspan="4">
        	<div id="txt_observ_orbita"><?php echo($utilidades->ajustar_texto_wysiwyg($observ_orbita)); ?></div>
        </td>
    </tr>
    <tr>
        <td align="center" colspan="4">
            <h5 style="margin:10px">Cejas</h5>
        </td>
    </tr>
    <tr>
    	<td align="center" colspan="4">
        	<div id="txt_observ_cejas"><?php echo($utilidades->ajustar_texto_wysiwyg($observ_cejas)); ?></div>
        </td>
    </tr>
    <tr>
        <td align="center" colspan="4">
            <h5 style="margin:10px">P&aacute;rpados</h5>
        </td>
    </tr>
    <tr>
    	<td align="center">
        	<div style="float:left;"><img src="../imagenes/copy_opt.png" onclick='copiar_campo_oculoplastia("txt_fme_oi", "txt_fme_od");'></div>
        	<input type="text" id="txt_fme_od" value="<?php echo($fme_od); ?>" maxlength="2" onkeypress="return solo_numeros(event, false);" class="no-margin" style="width:50px;" />
        </td>
    	<td align="center" colspan="2">Funci&oacute;n elevador (FME)</td>
    	<td align="center">
        	<div style="float:right;"><img src="../imagenes/copy_opt.png" onclick='copiar_campo_oculoplastia("txt_fme_od", "txt_fme_oi");'></div>
        	<input type="text" id="txt_fme_oi" value="<?php echo($fme_oi); ?>" maxlength="2" onkeypress="return solo_numeros(event, false);" class="no-margin" style="width:50px;" />
        </td>
    </tr>
    <tr>
    	<td align="center">
        	<div style="float:left;"><img src="../imagenes/copy_opt.png" onclick='copiar_campo_oculoplastia("txt_dmr_oi", "txt_dmr_od");'></div>
        	<input type="text" id="txt_dmr_od" value="<?php echo($dmr_od); ?>" maxlength="4" class="no-margin" style="width:50px;" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_parpados, this);" />
        </td>
    	<td align="center" colspan="2">Distancia margen reflejo (DMR)</td>
    	<td align="center">
        	<div style="float:right;"><img src="../imagenes/copy_opt.png" onclick='copiar_campo_oculoplastia("txt_dmr_od", "txt_dmr_oi");'></div>
        	<input type="text" id="txt_dmr_oi" value="<?php echo($dmr_oi); ?>" maxlength="4" class="no-margin" style="width:50px;" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_parpados, this);" />
        </td>
    </tr>
    <tr>
    	<td align="center">
        	<div style="float:left;"><img src="../imagenes/copy_opt.png" onclick='copiar_campo_oculoplastia("txt_fen_oi", "txt_fen_od");'></div>
        	<input type="text" id="txt_fen_od" value="<?php echo($fen_od); ?>" maxlength="4" class="no-margin" style="width:50px;" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_parpados, this);" />
        </td>
    	<td align="center" colspan="2">Respuesta fenilefrina (FEN)</td>
    	<td align="center">
        	<div style="float:right;"><img src="../imagenes/copy_opt.png" onclick='copiar_campo_oculoplastia("txt_fen_od", "txt_fen_oi");'></div>
        	<input type="text" id="txt_fen_oi" value="<?php echo($fen_oi); ?>" maxlength="4" class="no-margin" style="width:50px;" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_parpados, this);" />
        </td>
    </tr>
    <tr>
    	<td align="center" colspan="4">
        	<div id="txt_observ_parpados"><?php echo($utilidades->ajustar_texto_wysiwyg($observ_parpados)); ?></div>
        </td>
    </tr>
    <tr>
        <td align="center" colspan="4">
            <h5 style="margin:10px">Pesta&ntilde;as</h5>
        </td>
    </tr>
    <tr>
    	<td align="center" colspan="4">
        	<div id="txt_observ_pestanas"><?php echo($utilidades->ajustar_texto_wysiwyg($observ_pestanas)); ?></div>
        </td>
    </tr>
    <tr>
        <td align="center" colspan="4">
            <h5 style="margin:10px">Gl&aacute;ndulas de Meibomio</h5>
        </td>
    </tr>
    <tr>
    	<td align="center">
        	<div style="float:left;"><img src="../imagenes/copy_opt.png" onclick='copiar_campo_oculoplastia("txt_gm_expresibilidad_oi", "txt_gm_expresibilidad_od");'></div>
        	<input type="text" id="txt_gm_expresibilidad_od" value="<?php echo($gm_expresibilidad_od); ?>" maxlength="200" class="no-margin" style="width:80%;" />
        </td>
    	<td align="center" colspan="2">Expresibilidad</td>
    	<td align="center">
        	<div style="float:right;"><img src="../imagenes/copy_opt.png" onclick='copiar_campo_oculoplastia("txt_gm_expresibilidad_od", "txt_gm_expresibilidad_oi");'></div>
        	<input type="text" id="txt_gm_expresibilidad_oi" value="<?php echo($gm_expresibilidad_oi); ?>" maxlength="200" class="no-margin" style="width:80%;" />
        </td>
    </tr>
    <tr>
    	<td align="center">
        	<div style="float:left;"><img src="../imagenes/copy_opt.png" onclick='copiar_campo_oculoplastia("txt_gm_calidad_expr_oi", "txt_gm_calidad_expr_od");'></div>
        	<input type="text" id="txt_gm_calidad_expr_od" value="<?php echo($gm_calidad_expr_od); ?>" maxlength="200" class="no-margin" style="width:80%;" />
        </td>
    	<td align="center" colspan="2">Calidad de expresi&oacute;n</td>
    	<td align="center">
        	<div style="float:right;"><img src="../imagenes/copy_opt.png" onclick='copiar_campo_oculoplastia("txt_gm_calidad_expr_od", "txt_gm_calidad_expr_oi");'></div>
        	<input type="text" id="txt_gm_calidad_expr_oi" value="<?php echo($gm_calidad_expr_oi); ?>" maxlength="200" class="no-margin" style="width:80%;" />
        </td>
    </tr>
    <tr>
    	<td align="center" colspan="4">
        	<div id="txt_observ_glandulas_meib"><?php echo($utilidades->ajustar_texto_wysiwyg($observ_glandulas_meib)); ?></div>
        </td>
    </tr>
    <tr>
        <td align="center" colspan="4">
            <h5 style="margin:10px">V&iacute;a Lagrimal</h5>
        </td>
    </tr>
    <tr>
    	<td align="center">
        	<div style="float:left;"><img src="../imagenes/copy_opt.png" onclick='copiar_campo_oculoplastia("txt_prueba_irrigacion_oi", "txt_prueba_irrigacion_od");'></div>
        	<input type="text" id="txt_prueba_irrigacion_od" value="<?php echo($prueba_irrigacion_od); ?>" maxlength="200" class="no-margin" style="width:80%;" />
        </td>
    	<td align="center" colspan="2">Prueba de irrigaci&oacute;n</td>
    	<td align="center">
        	<div style="float:right;"><img src="../imagenes/copy_opt.png" onclick='copiar_campo_oculoplastia("txt_prueba_irrigacion_od", "txt_prueba_irrigacion_oi");'></div>
        	<input type="text" id="txt_prueba_irrigacion_oi" value="<?php echo($prueba_irrigacion_oi); ?>" maxlength="200" class="no-margin" style="width:80%;" />
        </td>
    </tr>
    <tr>
    	<td align="center" colspan="4">
        	<div id="txt_observ_via_lagrimal"><?php echo($utilidades->ajustar_texto_wysiwyg($observ_via_lagrimal)); ?></div>
        </td>
    </tr>
</table>
<input type="hidden" id="hdd_cant_retina_cx" value="<?php echo(count($lista_retina_cx) > 0 ? count($lista_retina_cx) : 1); ?>" />
<script type="text/javascript">
	/*seleccionar_intravitreas_retina("<?php echo($ind_intravitreas); ?>");
	seleccionar_cx_retina("<?php echo($ind_cx_retina); ?>");*/
</script>
