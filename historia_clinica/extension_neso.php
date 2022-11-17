<?php
	require_once("../db/DbConsultasNeso.php");
	require_once("../db/DbListas.php");
	require_once("../funciones/Class_Combo_Box.php");	
	require_once("../funciones/Utilidades.php");
	
	function getFormularioLesionNeso($consecutivo_frm, $registro_neso, $id_ojo_nuevo_registro=null) {
		
		$dbListas = new DbListas(); 
		$combo = new Combo_Box();
		
		$lista_husos = $dbListas->getListaDetalles(69); 
					
		if (!is_null($registro_neso)) {
			$neso_idojo = $registro_neso['id_ojo']; 
			$neso_nhusos = $registro_neso['canti_husos']; 
			$neso_husoini = $registro_neso['id_huso_ini']; 
			$neso_mm = $registro_neso['cornea_comprometida']; 
		} else {	
			$neso_idojo = $id_ojo_nuevo_registro; 
			$neso_nhusos = ""; 
			$neso_husoini = ""; 
			$neso_mm = ""; 
		}
		
		if ($neso_idojo==79) { $sufijo='od'; } else { $sufijo='oi'; }
		
		$i = $consecutivo_frm; 
		?>
		<div>
			<tr>					
				<td>
				<input type="hidden" name="neso_hdd_idojo_<?php echo $sufijo."_".$i?>" id="neso_hdd_idojo_<?php echo $sufijo."_".$i?>" value="<?php echo $neso_idojo;?>">
				<label><input type="text" name="neso_nhusos_<?php echo $sufijo."_".$i?>" id="neso_nhusos_<?php echo $sufijo."_".$i?>" value="<?php echo $neso_nhusos;?>" maxlength="2" class="input input_hc ui-autocomplete-input" onBlur="validar_array(array_valores_cantidad_husos, this)" onChange="dibujarNeso(<?php echo $neso_idojo?>)" style="display:inline"></label></td> 
				<!--onkeypress="return solo_numeros(event, false);"-->
				<td>
				<?php
					$combo->getComboDb("neso_husoini_".$sufijo."_".$i, $neso_husoini, $lista_husos, "codigo_detalle, nombre_detalle", "--Seleccione--", "dibujarNeso($neso_idojo)", 1, "width:120px", "", "select_hc no-margin");
				?>						
				</td>
				<td><label><input type="text" name="neso_mmcornea_<?php echo $sufijo."_".$i?>" id="neso_mmcornea_<?php echo $sufijo."_".$i?>" value="<?php echo $neso_mm;?>" maxlength="2" class="input input_hc ui-autocomplete-input" onBlur="validar_array(array_valores_cantidad_husos, this)" onChange="dibujarNeso(<?php echo $neso_idojo?>)" style="display:inline"> mm</label></td>
			</tr>			
		</div>
		<?php 	
	}					
	
	
	$dbConsultasNeso = new dbConsultasNeso();	
	$dbListas = new DbListas();		
	$combo = new Combo_Box();	
	$utilidades = new Utilidades();						
	
	//Listas
	$lista_si_no = $dbListas->getListaDetalles(61); 
	//$lista_husos = $dbListas->getListaDetalles(69); 

	// Inicializar variables del formulario 
	$neso_obj = $dbConsultasNeso->getConsultaNeso($id_hc_consulta); 	
	
	if (isset($neso_obj["id_hc"])) { 	
		$neso_ind_interferon_od = $neso_obj["ind_interferon_od"]; 
		$neso_cantidad_dosis_od = $neso_obj["cantidad_dosis_od"]; 
		$neso_img_od = $neso_obj["img_neso_od"]; 
		$neso_ind_recidivante_od = $neso_obj["ind_recidivante_od"]; 
		$neso_ind_interferon_oi = $neso_obj["ind_interferon_oi"]; 
		$neso_cantidad_dosis_oi = $neso_obj["cantidad_dosis_oi"]; 
		$neso_img_oi = $neso_obj["img_neso_oi"]; 
		$neso_ind_recidivante_oi = $neso_obj["ind_recidivante_oi"]; 
		$neso_observaciones = $utilidades->str_decode($neso_obj["observaciones"]); 
	} else { 
		$neso_ind_interferon_od = ""; 
		$neso_cantidad_dosis_od = ""; 
		$neso_img_od = ""; 
		$neso_ind_recidivante_od = ""; 
		$neso_ind_interferon_oi = ""; 
		$neso_cantidad_dosis_oi = ""; 
		$neso_img_oi = ""; 
		$neso_ind_recidivante_oi = ""; 
		$neso_observaciones = "";  
	} 	
	
	//Se borran las imágenes temporales creadas por el usuario actual
	$id_usuario_crea = $_SESSION["idUsuario"];
	$ruta_tmp = "../historia_clinica/tmp/".$id_usuario_crea;
	/*if (file_exists($ruta_tmp)) {
		@array_map("unlink", glob($ruta_tmp."/img*.*"));
	}*/	
	
	@mkdir($ruta_tmp);
	
	//Se obtiene la ruta actual de las imágenes
	$arr_ruta_base = $dbVariables->getVariable(17);
	$ruta_base = $arr_ruta_base["valor_variable"];
	
	//Se crea una copia local de las imágenes a mostrar
	if ($neso_img_od != "") {
		$neso_img_od = str_replace("../imagenes/imagenes_hce", $ruta_base, $neso_img_od);
		@copy($neso_img_od, $ruta_tmp."/img_neso_od_".$id_hc_consulta.".png");
		$neso_img_od = $ruta_tmp."/img_neso_od_".$id_hc_consulta.".png";
	}
	if ($neso_img_oi != "") {
		$neso_img_oi = str_replace("../imagenes/imagenes_hce", $ruta_base, $neso_img_oi);
		@copy($neso_img_oi, $ruta_tmp."/img_neso_oi_".$id_hc_consulta.".png");
		$neso_img_oi = $ruta_tmp."/img_neso_oi_".$id_hc_consulta.".png";
	}	
	
	//Array valores Cantidad Husos
	$cadena_valores_canti_husos = "";
	for ($i = 1; $i <= 12; $i++) {		    
		$cadena_valores_canti_husos .= "'".$i."',";
	}
	$cadena_valores_canti_husos = substr($cadena_valores_canti_husos, 0, -1); 	
?>
<script id="ajax">
	var array_valores_cantidad_husos = [<?php echo($cadena_valores_canti_husos) ?>];	
	var Tags_valores_cantidad_husos = [<?php echo($cadena_valores_canti_husos) ?>];	
	
	$(function() {		
		//Para Cantidad husos comprometidos
		$("[id^='neso_nhusos_']").autocomplete({ source: Tags_valores_cantidad_husos });
		//Para mm córnea
		$("[id^='neso_mmcornea_']").autocomplete({ source: Tags_valores_cantidad_husos });		
	});	
</script>
<table border="0" cellpadding="3" cellspacing="0" align="center" style="width:98%;">
    <tr>
        <td align="center" colspan="4">
            <h5 style="margin: 10px">Neoplasia Escamosa de la Superficie Ocular</h5>
        </td>
    </tr>
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
    <tr valign="top">
		<td align="center" width="48%"> 
			<?php
				$combo->getComboDb("neso_ind_interferon_od", $neso_ind_interferon_od, $lista_si_no, "codigo_detalle, nombre_detalle", "--Seleccione--", "", 1, "width:120px", "", "select_hc no-margin");
			?>
		</td> 	
		<td align="center" width="4%">Usa Interfer&oacute;n*</td> 
		<td align="center" width="48%"> 
			<?php
				$combo->getComboDb("neso_ind_interferon_oi", $neso_ind_interferon_oi, $lista_si_no, "codigo_detalle, nombre_detalle", "--Seleccione--", "", 1, "width:120px", "", "select_hc no-margin");
			?>
		</td> 
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
		<td align="center"> 
			<input type="text" name="neso_dosis_od" id="neso_dosis_od" value="<?php echo $neso_cantidad_dosis_od;?>" maxlength="3" class="input input_hc" onkeypress="return solo_numeros(event, false);"> 
		</td> 	
		<td align="center" nowrap>N&uacute;mero de Dosis</td> 
		<td align="center"> 
			<input type="text" name="neso_dosis_oi" id="neso_dosis_oi" value="<?php echo $neso_cantidad_dosis_oi;?>" maxlength="3" class="input input_hc" onkeypress="return solo_numeros(event, false);"> 
		</td> 
    </tr>	
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr><td colspan="3"><label><span id="label_husos">Husos horarios comprometidos*: </span></label></td></tr>
    <tr>
		<td>
			<table id="table_husos_od" border="0" style="width:100%">
				<tr valign="top">						
					<td width="25%">Cantidad Husos Comprometidos</td> 
					<td width="40%">A partir del Huso</td> 
					<td width="35%">C&oacute;rnea comprometida<br>a partir del limbo</td>
				</tr>
				<?php
					$max_lesiones=3;
					
					// Dibujar los formularios de lesiones NESO registradas para OD			
					$tabla_lesiones = $dbConsultasNeso->getLesionesIdOjo($id_hc_consulta, 79); 					
					$canti = count($tabla_lesiones); 
					
					$id_nueva_fila = 0; //índice de los objetos a pintar 
					foreach ($tabla_lesiones as $registro) { 
						$id_nueva_fila++; 
						getFormularioLesionNeso($id_nueva_fila, $registro); 
					} 
				
					// Dibujar los demás formularios para posibles lesiones (considerando que máximo habrá una lesión por huso horario) 
					$inicio=$canti+1; 
					for ($j=$inicio; $j<=$max_lesiones; $j++) { 
						$id_nueva_fila++; 
						getFormularioLesionNeso($id_nueva_fila, null, 79); 
					}
				?>					
			</table>		
		</td>	
        <td></td>
		<td>
			<table id="table_husos_oi" border="0" style="width:100%">
				<tr valign="top">						
					<td width="25%">Cantidad Husos Comprometidos</td> 
					<td width="40%">A partir del Huso</td> 
					<td width="35%">C&oacute;rnea comprometida<br>a partir del limbo</td>
				</tr>
				<?php 
					// Dibujar los formularios de lesiones NESO registradas para OD			
					$tabla_lesiones = $dbConsultasNeso->getLesionesIdOjo($id_hc_consulta, 80); 					
					$canti = count($tabla_lesiones); 
					
					$id_nueva_fila = 0; //índice de los objetos a pintar 
					//$canti=0;
					foreach ($tabla_lesiones as $registro) { 
						$id_nueva_fila++; 
						getFormularioLesionNeso($id_nueva_fila, $registro); 
						//$canti++; 
					} 
				
					// Dibujar los demás formularios para posibles lesiones (considerando que máximo habrá una lesión por huso horario) 
					$inicio=$canti+1; 
					for ($j=$inicio; $j<=$max_lesiones; $j++) { 
						$id_nueva_fila++; 
						getFormularioLesionNeso($id_nueva_fila, null, 80); 
					}
				?>	
			</table>
		</td>
    </tr>	
    <tr>
		<td align="center">
			<?php
			if (isset($_POST["hdd_id_paciente"])) {
				$id_paciente = $_POST["hdd_id_paciente"];
			}				
			$params = "&id_paciente=".$id_paciente.
					  "&id_imagen=".$id_hc_consulta."_neso_od".
					  "&nombre_imagen=".$neso_img_od.
					  "&nombre_imagen_base=../imagenes/husos_neso.png".
					  "&ancho_img=380". 
					  "&alto_img=270";
			?>
			<input type="hidden" name="neso_img_od" id="neso_img_od" value="<?php echo($neso_img_od); ?>" />
			<iframe id="ifr_neso_img_od" width="100%" height="370" style="border-width:0;" src="../funciones/wPaint/Class_Pintar.php?<?php echo($params); ?>"></iframe>
		</td>
        <td></td>
		<td align="center">
			<?php		
			$params = "&id_paciente=".$id_paciente.
					  "&id_imagen=".$id_hc_consulta."_neso_oi".
					  "&nombre_imagen=".$neso_img_oi.
					  "&nombre_imagen_base=../imagenes/husos_neso.png".
					  "&ancho_img=380".
					  "&alto_img=270";
			?>
			<input type="hidden" name="neso_img_oi" id="neso_img_oi" value="<?php echo($neso_img_oi); ?>" />
			<iframe id="ifr_neso_img_oi" width="100%" height="370" style="border-width:0;" src="../funciones/wPaint/Class_Pintar.php?<?php echo($params); ?>"></iframe>
			<input type="hidden" name="hdd_neso_id_paciente" id="hdd_neso_id_paciente" value="<?php echo($id_paciente); ?>" />
		</td>
	</tr>
    <tr>
		<td>
			<?php
				$combo->getComboDb("neso_ind_recidivante_od", $neso_ind_recidivante_od, $lista_si_no, "codigo_detalle, nombre_detalle", "--Seleccione--", "", 1, "width:120px", "", "select");
			?>
		</td>	
        <td nowrap><label>Lesi&oacute;n recidivante*</label></td>
		<td>
			<?php				
				$combo->getComboDb("neso_ind_recidivante_oi", $neso_ind_recidivante_oi, $lista_si_no, "codigo_detalle, nombre_detalle", "--Seleccione--", "", 1, "width:120px", "", "select");
			?>
		</td>		
	</tr>	
	<tr>
		<td colspan="3">
			<table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
				<tr>
					<td align="center" class="">
						<h5 style="margin: 0px">
							Observaciones: 
						</h5>
						<div id="neso_observaciones"><?php echo($utilidades->ajustar_texto_wysiwyg($neso_observaciones)); ?></div>					
					</td>
				</tr>
			</table>	
			<div class="div_separador"></div>
		</td>
	</tr>
</table>
<script type='text/javascript' src='../js/foundation.min.js'></script>
<script id="ajax">
	$(document).foundation();
	
	initCKEditorNeso("neso_observaciones");
</script>