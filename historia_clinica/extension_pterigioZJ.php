<?php
	require_once("../db/DbConsultasPterigio.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbArchivos.php");
	require_once("../funciones/Class_Combo_Box.php");	
	require_once("../funciones/Utilidades.php");	
	
	$dbConsultasPterigio = new dbConsultasPterigio();	
	$dbListas = new DbListas();		
	$dbLoteArchivos = new DBRegistroArchivos(); 
	//$dbArchivos = new DBArchivo(); 
	$combo = new Combo_Box();	
	$utilidades = new Utilidades();						
	
	//Lista para campos Sí/No 
	$lista_si_no = $dbListas->getListaDetalles(61);

	// Inicializar variables del formulario 
	$pterigio_obj = $dbConsultasPterigio->getConsultaPterigio($id_hc_consulta); 	
	
	if (isset($pterigio_obj["id_hc"])) { 
		$pte_grado_od = $pterigio_obj["grado_od"]; 
		$pte_ind_reproducido_od = $pterigio_obj["ind_reproducido_od"]; 		
		$pte_conjuntiva_sup_od = $pterigio_obj["mov_conjuntiva_sup_od"]; 
		$pte_ind_astigmatismo_od = $pterigio_obj["ind_astigmatismo_ind_od"]; 		
		$pte_grado_oi = $pterigio_obj["grado_oi"]; 
		$pte_ind_reproducido_oi = $pterigio_obj["ind_reproducido_oi"]; 
		$pte_conjuntiva_sup_oi = $pterigio_obj["mov_conjuntiva_sup_oi"]; 
		$pte_ind_astigmatismo_oi = $pterigio_obj["ind_astigmatismo_ind_oi"]; 
		$pte_observaciones = $pterigio_obj["observaciones"]; 		
		
		// Obtener información de adjuntos: 
		$pte_fotos_od = $dbLoteArchivos->getRegistroArchivosHC($pterigio_obj["id_hc"], 1); //pte_od
		$pte_fotos_oi = $dbLoteArchivos->getRegistroArchivosHC($pterigio_obj["id_hc"], 2); //pte_oi		
	} else { 
		$pte_grado_oi = ""; 
		$pte_grado_od = ""; 
		$pte_ind_reproducido_od = ""; 
		$pte_ind_reproducido_oi = ""; 
		$pte_conjuntiva_sup_oi = ""; 
		$pte_conjuntiva_sup_od = ""; 
		$pte_ind_astigmatismo_oi = ""; 
		$pte_ind_astigmatismo_od = ""; 
		$pte_observaciones = ""; 
		$pte_fotos_od["id_reg_archivos"] = "";
		$pte_fotos_oi["id_reg_archivos"] = "";
	} 
	
	$pte_fotos_od_tipo_archivo=1;
	$pte_fotos_oi_tipo_archivo=2;
?>
<table border="0" cellpadding="0" cellspacing="0" align="center" style="width:98%;">
    <tr>
        <td colspan="3">
            <h5 style="margin: 10px">Pterigio</h5>
        </td>
    </tr>
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
		<td style="width:40%">
			<?php
				$lista_grados_pterigio = $dbListas->getListaDetalles(58);
				$combo->getComboDb("pte_grado_od", $pte_grado_od, $lista_grados_pterigio, "id_detalle, nombre_detalle", " ", "", "", "width:75px", "", "");				
			?>		
		</td>
        <td style="width:20%"><label><span id="label_grado">&nbsp; Grado* &nbsp;</span></label></td> 
		<td style="width:40%">
			<?php				
				$combo->getComboDb("pte_grado_oi", $pte_grado_oi, $lista_grados_pterigio, "id_detalle, nombre_detalle", " ", "", "", "width:75px", "", "");
				//$combo->getComboDb("cmb_grado_oi", $romano1, $lista_grados_pterigio, "id_detalle, nombre_detalle", " ", "cambiar_lista_locs3(this.value);", "", "", "", "select");
				//$colorPick->getColorPick("avsc_lejos_od", 1); 
			?>		
		</td>
    </tr>
    <tr>
		<td>
			<?php
				$combo->getComboDb("pte_reproducido_od", $pte_ind_reproducido_od, $lista_si_no, "codigo_detalle, nombre_detalle", "--Seleccione--", "config_reproducido('OD')", 1, "", "", "select");
			?>
		</td>	
        <td><label>Reproducido</label></td>
		<td>
			<?php
				$combo->getComboDb("pte_reproducido_oi", $pte_ind_reproducido_oi, $lista_si_no, "codigo_detalle, nombre_detalle", "--Seleccione--", "config_reproducido('OI')", 1, "", "", "select");
			?>
		</td>
    </tr>	
    <tr>
		<td>
			<?php
				$lista_conjuntiva_sup = $dbListas->getListaDetalles(59);				
				$combo->getComboDb("pte_conjuntiva_sup_od", $pte_conjuntiva_sup_od, $lista_conjuntiva_sup, "id_detalle, nombre_detalle", " ", "", "", "", "", "select");	
			?>
		</td>	
        <td><label>Conjuntiva Superior*</label></td>
		<td>
			<?php 
				$combo->getComboDb("pte_conjuntiva_sup_oi", $pte_conjuntiva_sup_oi, $lista_conjuntiva_sup, "id_detalle, nombre_detalle", " ", "", "", "", "", "select");	
			?>
		</td>		
    </tr>		
    <tr>
		<td>
			<?php
				$combo->getComboDb("pte_astigmatismo_od", $pte_ind_astigmatismo_od, $lista_si_no, "codigo_detalle, nombre_detalle", "--Seleccione--", "", 1, "", "", "select");
			?>
		</td>	
        <td><label>Astigmatismo Inducido*</label></td>
		<td>
			<?php				
				$combo->getComboDb("pte_astigmatismo_oi", $pte_ind_astigmatismo_oi, $lista_si_no, "codigo_detalle, nombre_detalle", "--Seleccione--", "", 1, "", "", "select");
			?>
		</td>		
	</tr>
	<tr>
		<td colspan="3">
			<!--<div class="div_separador"></div>-->
			<table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
				<tr>
					<td align="center" class="">
						<h5 style="margin: 0px">
							Observaciones: 
						</h5>
						<div id="txt_observaciones_pte"><?php echo($utilidades->ajustar_texto_wysiwyg($pte_observaciones)); ?></div>
					</td>
				</tr>
			</table>	
			<div class="div_separador"></div>
		</td>
	</tr>
	<tr>
		<td valign="top">
			<div id="pte_uploader_od"></div>			
			<input type="hidden" id="pte_foto_tipo_arch_od" name="pte_foto_tipo_arch_od" value="<?php echo $pte_fotos_od_tipo_archivo; ?>"> 
			<input type="hidden" id="pte_foto_id_lote_od" name="pte_foto_id_lote_od" value="<?php echo $pte_fotos_od["id_reg_archivos"]?>"> 
			<script>				
				var uploader_pte_od_responseJSON; 
				
				var uploader_pte_od_default_params = {
					opcion: 91, 
					idTipoAdjunto: <?php echo $pte_fotos_od_tipo_archivo; ?>, 
					idHc: <?php echo $id_hc_consulta?>
				}
				
				var uploader_pte_od = new qq.FineUploader({					
					element: document.getElementById("pte_uploader_od"), 					
					debug: true, 
					//autoUpload: false, //uploader.uploadStoredFiles()	
					session: {
						endpoint: 'extension_pterigio_ajax.php', 
						params: {
							opcion: 92, 
							idHc: <?php echo $id_hc_consulta?>
						}
					},
					request: {							
						endpoint: 'extension_pterigio_ajax.php', 
						method: 'POST', 						
						inputName: "pte_foto_od",  
						params: uploader_pte_od_default_params 
					}, 
					deleteFile: {
						enabled: true, 
						endpoint: 'extension_pterigio_ajax.php', 
						method: 'POST',  
						params: uploader_pte_od_default_params, 
						paramsInBody: true, 
						forceConfirm: true
					}, 
					messages: {
						emptyError: "vacío???", 
						onLeave: "Los archivos se están cargando, si abandona la página se cancelará el cargue.", 
						typeError: "Extension no válida. Extensiones permitidas: {extensions}.", 
						sizeError: "{file} es muy grande, el tamaño máximo es {sizeLimit}", 
						retryFailTooManyItemsError: "Reintento fallido - Ha alcanzado el límite de archivos", 							
						tooManyItemsError: "Too many items ({netItems}) would be uploaded. Item limit is {itemLimit}."
					}, 					
					validation: {							
						allowedExtensions: ["jpeg", "jpg", "tiff", "gif", "pdf", "xls", "xlsx"],
						//acceptFiles: "audio/*, video/*, image/*, MIME_type", 
						sizeLimit: 25000000 // 5 MiB
					}, 
					callbacks: {
						onUpload: function(id, name) {
							console.log("enviando id="+id+"; name="+name);
						}, 
						onComplete: function(id, fileName, responseJSON, xhr) { 
							console.log("carga finalizada para id="+id); 
						}						
					}					
				})								
			</script>			
		</td>
		<td><label>Foto Pterigio</label></td>
		<td valign="top">
			<div id="pte_uploader_oi"></div>
			<input type="hidden" id="pte_foto_tipo_arch_oi" name="pte_foto_tipo_arch_oi" value="2"> 
			<input type="hidden" id="pte_foto_id_lote_oi" name="pte_foto_id_lote_oi" value="<?php echo $pte_fotos_oi["id_reg_archivos"];?>"> 			
			<script>
				var uploader_pte_oi = new qq.FineUploader({
					element: document.getElementById("pte_uploader_oi"), 					
					debug: true, 					
					request: {							
						endpoint: 'extension_pterigio_ajax_FineUp.php'
					}, 
					deleteFile: {
						enabled: true, 		
						endpoint: 'extension_pterigio_ajax_FineUp.php'							
					}						
				})
			</script>
		</td>
    </tr>	
</table>
<script type='text/javascript' src='../js/foundation.min.js'></script>
<script id="ajax">
	$(document).foundation();
	
	initCKEditorPte("txt_observaciones_pte");
</script>