<?php
	require_once("../db/DbConsultasPterigio.php");
	require_once("../db/DbListas.php");
	require_once("../funciones/Class_Combo_Box.php");	
	
	$dbConsultasPterigio = new dbConsultasPterigio();	
	$dbListas = new DbListas();	
	
	$combo = new Combo_Box();
	//echo "<br>".$id_hc_consulta."<br>"; 		
	
	//Lista para campos Sí/No 
	$lista_si_no = array(); 
	$lista_si_no[0]["id"] = "1"; 
	$lista_si_no[0]["valor"] = "S&iacute;"; 
	$lista_si_no[1]["id"] = "0"; 
	$lista_si_no[1]["valor"] = "No"; 	

	// Inicializar variables del formulario 
	$pterigio_obj = $dbConsultasPterigio->getConsultaPterigio($id_hc_consulta); 	
	
	if (isset($pterigio_obj["id_hc"])) { 
		//echo "<br>ver HC-Pterigio".$id_hc_consulta."<br>";
		//$exoftalmometria_base = $oculoplastia_obj["exoftalmometria_base"]; 
		$grado_pterigio_oi = 2; 
		$grado_pterigio_od = 3; 
		$ind_reproducido_od = "S"; 
		$ind_reproducido_oi = "N"; 
		$conjuntiva_sup_oi = 1; 
		$conjuntiva_sup_od = 2; 
		$ind_astigmatismo_oi = ""; 
		$ind_astigmatismo_od = "S"; 
	} else { 
		//echo "<br>nueva consulta HC-Pterigio".$id_hc_consulta."<br>";
		$grado_pterigio_oi = ""; 
		$grado_pterigio_od = ""; 
		$ind_reproducido_od = ""; 
		$ind_reproducido_oi = ""; 
		$conjuntiva_sup_oi = ""; 
		$conjuntiva_sup_od = ""; 
		$ind_astigmatismo_oi = ""; 
		$ind_astigmatismo_od = ""; 
	} 
?>
<table border="1" cellpadding="3" cellspacing="0" align="center" style="width:98%;">
    <tr>
        <td align="center" colspan="4">
            <h5 style="margin: 10px">Neoplastia Escamosa de la Superficie Ocular (NESO)</h5>
        </td>
    </tr>
    <tr>
		<td align="right" width="35%">Usa Interfer&oacute;n</td> 
		<td align="left" width="15%"> 
			<?php
				$combo->getComboDb("cmb_interferon", $ind_reproducido_oi, $lista_si_no, "id,valor", "--Seleccione--", "", 1, "width:120px", "", "select_hc no-margin");
			?>
		</td> 
		<td align="right" width="15%">N&uacute;mero de Dosis</td> 
		<td align="left" width="35%"> 
			<input type="text" name="txt_dosis" id="txt_dosis" value="" maxlength="3" class="input input_hc" onkeypress="return solo_numeros(event, false);"> 
		</td> 
    </tr>	
    <!--<tr>
		<td align="right" width="50%">N&uacute;mero de Dosis</td>
		<td align="left" width="50%">
			<input type="text" name="txt_dosis" id="txt_dosis" value="" maxlength="3" class="input input_hc" onkeypress="return solo_numeros(event, false);">
		</td>        
    </tr>		-->
</table>
<br>
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
<br>
<table border="0" cellpadding="3" cellspacing="0" align="center" style="width:98%;">	
    <tr>
		<td>
			<?php
				$lista_grados_pterigio = $dbListas->getListaDetalles(58);
				$combo->getComboDb("cmb_grado_oi", $grado_pterigio_oi, $lista_grados_pterigio, "id_detalle, nombre_detalle", " ", "", "", "", "", "width: 50%;");
				//$combo->getComboDb("cmb_grado_oi", $romano1, $lista_grados_pterigio, "id_detalle, nombre_detalle", " ", "cambiar_lista_locs3(this.value);", "", "", "", "select_hc");
				//$colorPick->getColorPick("avsc_lejos_od", 1); 
			?>		
		</td>
        <td align="center" colspan="3">Grado</td>
		<td>
			<?php
				$combo->getComboDb("cmb_grado_od", $grado_pterigio_od, $lista_grados_pterigio, "id_detalle, nombre_detalle", " ", "", "", "", "", "");				
			?>		
		</td>

    </tr>
    <tr>
		<td>
			<?php			
				$lista_conjuntiva_sup = $dbListas->getListaDetalles(59);				
				$combo->getComboDb("cmb_conjuntiva_sup_oi", $conjuntiva_sup_oi, $lista_conjuntiva_sup, "id_detalle, nombre_detalle", " ", "", "", "", "", "");	
			?>
		</td>
        <td align="center" colspan="3">Conjuntiva Superior</td>
		<td>
			<?php
				$combo->getComboDb("cmb_conjuntiva_sup_od", $conjuntiva_sup_od, $lista_conjuntiva_sup, "id_detalle, nombre_detalle", " ", "", "", "", "", "");	
			?>
		</td>
    </tr>		
    <tr>
		<td>
			<?php				
				$combo->getComboDb("cmb_astigmatismo_oi", $ind_astigmatismo_oi, $lista_si_no, "id,valor", "--Seleccione--", "", 1, "", "", "");
			?>
		</td>
        <td align="center" colspan="3">Astigmatismo Inducido</td>
		<td>
			<?php
				$combo->getComboDb("cmb_astigmatismo_od", $ind_astigmatismo_od, $lista_si_no, "id,valor", "--Seleccione--", "", 1, "", "", "");
			?>
		</td>
    </tr>		
    <tr>
		<td>[ Adjuntar archivos ]</td>
        <td align="center" colspan="3">Foto Pterigio</td>
		<td>[ Adjuntar archivos ]</td>
    </tr>		
</table>