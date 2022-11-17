<?php
	require_once("../db/DbConsultasOftalmologiaRetina.php");
	require_once("../funciones/Class_Combo_Box.php");
	
	$dbRetina = new DbConsultasOftalmologiaRetina();
	
	$combo = new Combo_Box();
	
	//Lista para campos Sí/No
	$lista_si_no_ret = array();
	$lista_si_no_ret[0]["id"] = "1";
	$lista_si_no_ret[0]["valor"] = "S&iacute;";
	$lista_si_no_ret[1]["id"] = "0";
	$lista_si_no_ret[1]["valor"] = "No";
	
	$retina_obj = $dbRetina->getConsultaOftalmologiaRetina($id_hc_consulta);
	$lista_retina_cx = array();
	
	if (isset($retina_obj["id_hc"])) {
		$lista_retina_cx = $dbRetina->getListaConsultasOftalmologiaRetinaCx($id_hc_consulta);
		
		$ind_laser = $retina_obj["ind_laser"];
		$ind_intravitreas = $retina_obj["ind_intravitreas"];
		$cant_intr_od = $retina_obj["cant_intr_od"];
		$cant_intr_oi = $retina_obj["cant_intr_oi"];
		$ind_cx_retina = $retina_obj["ind_cx_retina"];
	} else {
		$ind_laser = "";
		$ind_intravitreas = "";
		$cant_intr_od = "";
		$cant_intr_oi = "";
		$ind_cx_retina = "";
	}
?>
<table border="0" cellpadding="3" cellspacing="0" align="center" style="width:98%;">
    <tr>
        <td align="left" style="width:18%;">
            <label class="no-margin">L&aacute;ser</label>
        </td>
        <td align="left" style="width:15%;">
        	<?php
            	$combo->getComboDb("cmb_laser_ret", $ind_laser, $lista_si_no_ret, "id,valor", "--Seleccione--", "", 1, "", "", "no-margin");
			?>
        </td>
        <td align="left" style="width:10%;"></td>
        <td align="center" style="width:10%;">
        	<label class="no-margin">OD</label>
        </td>
        <td align="center" style="width:10%;">
        	<label class="no-margin">OI</label>
        </td>
        <td align="center" style="width:37%;"></td>
    </tr>
    <tr>
    	<td align="left">
        	<label class="no-margin">Inyecciones intrav&iacute;treas</label>
        </td>
        <td align="left">
        	<?php
            	$combo->getComboDb("cmb_intravitreas", $ind_intravitreas, $lista_si_no_ret, "id,valor", "--Seleccione--", "seleccionar_intravitreas_retina(this.value);", 1, "", "", "no-margin");
			?>
        </td>
        <td align="right">
        	<label class="no-margin">Cantidad</label>
        </td>
        <td align="center">
        	<input type="text" id="txt_cant_intr_od" value="<?php echo($cant_intr_od); ?>" maxlength="2" onkeypress="return solo_numeros(event, false);" class="no-margin" style="width:50px;" />
        </td>
        <td align="center">
        	<input type="text" id="txt_cant_intr_oi" value="<?php echo($cant_intr_oi); ?>" maxlength="2" onkeypress="return solo_numeros(event, false);" class="no-margin" style="width:50px;" />
        </td>
    </tr>
    <tr>
    	<td align="left">
        	<label class="no-margin">Cirug&iacute;as de retina</label>
        </td>
        <td align="left">
        	<?php
				$combo->getComboDb("cmb_cx_retina", $ind_cx_retina, $lista_si_no_ret, "id,valor", "--Seleccione--", "seleccionar_cx_retina(this.value);", 1, "", "", "no-margin");
			?>
        </td>
    </tr>
</table>
<br />
<input type="hidden" id="hdd_cant_retina_cx" value="<?php echo(count($lista_retina_cx) > 0 ? count($lista_retina_cx) : 1); ?>" />
<table border="0" cellpadding="3" cellspacing="0" align="center" style="width:98%;">
	<tr>
    	<td align="center" style="width:80%;"><h6 class="no-margin">Cirug&iacute;a</h6>
    	<td align="center" style="width:20%;"><h6 class="no-margin">Fecha (dd/mm/aaaa)</h6>
    </tr>
    <?php
		for ($i = 0; $i < count($lista_retina_cx); $i++) {
			$retina_cx_aux = $lista_retina_cx[$i];
	?>
    <tr id="tr_retina_cx_<?php echo($i); ?>">
    	<td align="center">
        	<input type="text" id="txt_texto_cx_<?php echo($i); ?>" value="<?php echo($retina_cx_aux["texto_cx"]); ?>" maxlength="100" class="no-margin" style="width:100%;" />
        </td>
        <td align="center">
        	<input type="text" id="txt_fecha_cx_<?php echo($i);?>" class="input input_hc" maxlength="10" style="width:100px;" value="<?php echo($retina_cx_aux["fecha_cx_t"]); ?>" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" />
        </td>
    </tr>
    <?php
		}
		
		for ($i = count($lista_retina_cx); $i < 20; $i++) {
			$visible_aux = "none";
			if ($i == 0) {
				$visible_aux = "table-row";
			}
	?>
    <tr id="tr_retina_cx_<?php echo($i); ?>" style="display:<?php echo($visible_aux); ?>;">
    	<td align="center">
        	<input type="text" id="txt_texto_cx_<?php echo($i); ?>" value="" maxlength="100" class="no-margin" style="width:100%;" />
        </td>
        <td align="center">
        	<input type="text" id="txt_fecha_cx_<?php echo($i);?>" class="input input_hc" maxlength="10" style="width:100px;" value="" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" />
        </td>
    </tr>
    <?php
		}
	?>
</table>
<div id="d_agregar_quitar_cx_ret">
    <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:98%;">
        <tr>
            <td>
                <div class="agregar_alemetos" onclick="agregar_cx_retina();"></div>
                <div class="restar_alemetos" onclick="restar_cx_retina();"></div>
            </td>
        </tr>
    </table>
</div>
<script type="text/javascript">
	seleccionar_intravitreas_retina("<?php echo($ind_intravitreas); ?>");
	seleccionar_cx_retina("<?php echo($ind_cx_retina); ?>");
</script>
