<?php
	require_once("../db/DbConsultasPterigio.php");
	require_once("../db/DbListas.php");
	require_once("../funciones/Class_Combo_Box.php");	
	require_once("../funciones/Utilidades.php");		
	
	$id_hc_consulta=711;
	$id_admision=498;	
	
	$dbConsultasPterigio = new dbConsultasPterigio();	
	$dbListas = new DbListas();		
	$combo = new Combo_Box();	
	$utilidades = new Utilidades();	
	
	//Lista para campos Sí/No 
	$lista_si_no = array(); 
	$lista_si_no[0]["id"] = "1"; 
	$lista_si_no[0]["valor"] = "S&iacute;"; 
	$lista_si_no[1]["id"] = "0"; 
	$lista_si_no[1]["valor"] = "No"; 	

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
	} 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $titulo['valor_variable']; ?></title>
    <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
    <link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
    <link href="../css/azul.css" rel="stylesheet" type="text/css" />
    <link href="../css/Class_Color_Pick.css" rel="stylesheet" type="text/css" />
    <script type='text/javascript' src='../js/jquery_autocompletar.js'></script>
    <script type='text/javascript' src='../js/jquery-ui.js'></script>
    <script type='text/javascript' src='../js/jquery.validate.js'></script>
    <script type='text/javascript' src='../js/jquery.validate.add.js'></script>
    <script type='text/javascript' src='../js/ajax2017.js'></script>
    <script type='text/javascript' src='../js/funciones.js'></script>
    <script type='text/javascript' src='../js/validaFecha.js'></script>
    <script type='text/javascript' src='../js/Class_Diagnosticos_v1.2.js'></script>
    <script type='text/javascript' src='../js/Class_Formulas_Medicas.js'></script>
    <script type='text/javascript' src='../js/Class_Atencion_Remision_v1.2.js'></script>
    <script type='text/javascript' src='../js/Class_Formulacion_v1.3.js'></script>
    <script type='text/javascript' src='../funciones/ckeditor/ckeditor.js'></script>
    <script type='text/javascript' src='../funciones/ckeditor/config.js'></script>
    <script type='text/javascript' src='../js/Class_Color_Pick.js'></script>
    <script type='text/javascript' src='historia_clinica_v1.1.js'></script>
    <script type='text/javascript' src='FuncionesHistoriaClinica.js'></script>
    <script type='text/javascript' src='evolucion_v1.14.js'></script>
    <script type='text/javascript' src='antecedentes_v1.0.js'></script>
    <script type='text/javascript' src='extension_retina.js'></script>
    <script type='text/javascript' src='extension_oculoplastia.js'></script>
    <script type='text/javascript' src='extension_pterigio.js'></script>
	<script id="ajax">
		function obtener_parametros_consulta_pterigio() {
			var txt_observaciones_pte = str_encode(CKEDITOR.instances.txt_observaciones_pte.getData());
			var params = "&pte_grado_od=" + $("#pte_grado_od").val() + 
						 "&pte_ind_reproducido_od=" + $("#pte_reproducido_od").val() + 
						 "&pte_conjuntiva_sup_od=" + $("#pte_conjuntiva_sup_od").val() + 
						 "&pte_ind_astigmatismo_od=" + $("#pte_astigmatismo_od").val() + 
						 "&pte_grado_oi=" + $("#pte_grado_oi").val() + 
						 "&pte_ind_reproducido_oi=" + $("#pte_reproducido_oi").val() + 
						 "&pte_conjuntiva_sup_oi=" + $("#pte_conjuntiva_sup_oi").val() + 
						 "&pte_ind_astigmatismo_oi=" + $("#pte_astigmatismo_oi").val() +
						 "&pte_observaciones=" + txt_observaciones_pte; 	
			
			return params; 
		}
		
		function validar_exito(opc){
			console.log("cargue finalizado!");
		}
	</script>		
</head>
<body>

	<div class="contenedor_principal" id="id_contenedor_principal">	
    	<div id="d_guardar_evolucion" style="width: 100%; display: block;">
        	<div class='contenedor_error' id='contenedor_error'></div>
            <div class='contenedor_exito' id='contenedor_exito'></div>
        </div>
		<div id="divBarraProgreso">Espere un momento, no desespere!!!!!!!</div>
        <div class="formulario" id="principal_evolucion" style="width:100%; display:block;">
        	<?php
				//Se inserta el registro de ingreso a la historia clínica
//				$dbConsultaEvolucion->crear_ingreso_hc($id_usuario, $id_paciente, $id_admision, $id_hc_consulta, 160);
	        ?>
            <form id='frm_consulta_evolucion' name='frm_consulta_evolucion' method="post">
	            <input type="hidden" name="hdd_id_hc_consulta" id="hdd_id_hc_consulta" value="<?php echo($id_hc_consulta); ?>" />
    	        <input type="hidden" name="hdd_id_admision" id="hdd_id_admision" value="<?php echo($id_admision); ?>" />
				
				<input type="hidden" name="hdd_numero_menu" id="hdd_numero_menu" value="13" />
				<input type="hidden" name="credencial" id="credencial" value="13" />					

<!-- ---------------------------------------------------------------------------------------------------------------------------------------------- -->
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
					$combo->getComboDb("pte_reproducido_od", $pte_ind_reproducido_od, $lista_si_no, "id,valor", "--Seleccione--", "config_reproducido('OD')", 1, "", "", "select");
				?>
			</td>	
			<td><label>Reproducido</label></td>
			<td>
				<?php
					$combo->getComboDb("pte_reproducido_oi", $pte_ind_reproducido_oi, $lista_si_no, "id,valor", "--Seleccione--", "config_reproducido('OI')", 1, "", "", "select");
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
			<td><label>Conjuntiva Superior</label></td>
			<td>
				<?php 
					$combo->getComboDb("pte_conjuntiva_sup_oi", $pte_conjuntiva_sup_oi, $lista_conjuntiva_sup, "id_detalle, nombre_detalle", " ", "", "", "", "", "select");	
				?>
			</td>		
		</tr>		
		<tr>
			<td>
				<?php
					$combo->getComboDb("pte_astigmatismo_od", $pte_ind_astigmatismo_od, $lista_si_no, "id,valor", "--Seleccione--", "", 1, "", "", "select");
				?>
			</td>	
			<td><label>Astigmatismo Inducido</label></td>
			<td>
				<?php				
					$combo->getComboDb("pte_astigmatismo_oi", $pte_ind_astigmatismo_oi, $lista_si_no, "id,valor", "--Seleccione--", "", 1, "", "", "select");
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
			<td>[ Adjuntar archivos ]</td>
			<td><label>Foto Pterigio</label></td>
			<td>[ Adjuntar archivos ]
	<!-- -->
			<?php
				$indice="";
				$j=1;
			?>
			<table border="1" width="100%">
				<tr id="tr_archivo_<?php echo($indice+"_"+$j); ?>">
					<td>
						<label class="inline">Cargar archivos (<?php echo($j + 1); ?>)</label>
					</td>
					<td align="left" style="width:85%;" colspan="3">
						<label>Un archivo: </label>
						<input type="file" id="file1" accept="image/*, video/*, audio/*, .pdf" />
						<input type="file" id="file2" name="file2" accept="image/*, video/*, audio/*, .pdf" />
						<br><br>
						
						<label>Múltiple: </label>
						<input type="file" id="filemu" name="filemu" multiple="multiple" accept="image/*, video/*, audio/*, .pdf" />
						<input type="file" id="filemuc" name="filemuc[]" multiple="multiple" accept="image/*, video/*, audio/*, .pdf" />
						
						<label>Vectorial Múltiple: </label>
						<input type="file" id="filemuv1" name="filemuv[]" multiple="multiple" accept="image/*, video/*, audio/*, .pdf" />
						<input type="file" id="filemuv2" name="filemuv[]" multiple="multiple" accept="image/*, video/*, audio/*, .pdf" />
					</td>
				</tr>
	<!-- --></table>
			<script id="ajax">
				function enviar22(){ 
					var params="";
					llamarAjaxUploadFiles22("extension_pterigio_ajax1.php", "opcion=22&"+params, "d_guardar_evolucion", "validar_exito(" + 1 + ")", "divBarraProgreso", "");
					return "terminado!!!";
				} 

				function enviar0(){ 

					params="";
					params=obtener_parametros_consulta_pterigio();
					params="&opcion=0&"+params;
					console.log("params Armado = "+params);
				
					//llamarAjax("evolucion_ajax.php", params, "d_guardar_evolucion", "validar_exito(" + ind_imprimir + ")");					
					llamarAjaxUploadFiles("extension_pterigio_ajax1.php", params, "d_guardar_evolucion", "validar_exito(" + 1 + ")", "divBarraProgreso", "file1;file2"); 
					// err! llamarAjaxUploadFiles("extension_pterigio_ajax.php", params, "d_guardar_evolucion", "validar_exito(" + 1 + ")", "divBarraProgreso", "file1;file2;filemu[]"); //error xq el id filemenu[] no existe
					// err! llamarAjaxUploadFiles("extension_pterigio_ajax.php", params, "d_guardar_evolucion", "validar_exito(" + 1 + ")", "divBarraProgreso", "file1;file2;filemu"); // en js sólo procesa ['filemu'][0], los demás no los envía
					//llamarAjaxUploadFiles("extension_pterigio_ajax.php", params, "d_guardar_evolucion", "validar_exito(" + 1 + ")", "divBarraProgreso", "filemu"); // en js sólo procesa ['filemu'][0], los demás no los envía
					return "terminado!!!";
				}
				
				function enviar1(){ 

					params="";
					params=obtener_parametros_consulta_pterigio();
					params="&opcion=1&"+params;
					console.log("params Armado = "+params);
				
					//llamarAjax("evolucion_ajax.php", params, "d_guardar_evolucion", "validar_exito(" + ind_imprimir + ")");					
					//llamarAjaxUploadFiles1("extension_pterigio_ajax.php", params, "d_guardar_evolucion", "validar_exito(" + 1 + ")", "divBarraProgreso", "file1;filemuc"); //ok!
					llamarAjaxUploadFiles1("extension_pterigio_ajax1.php", params, "d_guardar_evolucion", "validar_exito(" + 1 + ")", "divBarraProgreso", "file1;file2;filemu;filemuc;filemuv1"); 
					return "terminado!!!";
				}				
			</script>		
			</td>
		</tr>	
	</table>
<!-- ---------------------------------------------------------------------------------------------------------------------------------------------- -->
			</form>
		</div>
	</div>
	
	<script type='text/javascript' src='../js/foundation.min.js'></script>
	<script id="ajax">
		$(document).foundation();
		
		initCKEditorPte("txt_observaciones_pte");
	</script>	
</body>	