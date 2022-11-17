<?php
	require_once("../db/DbConsultasPterigio.php");
	require_once("../db/DbListas.php");
	require_once("../funciones/Class_Combo_Box.php");	
	require_once("../funciones/Utilidades.php");		
	
/**/
function crearMiniatura($file, $width){
	 
	// Ponemos el . antes del nombre del archivo porque estamos considerando que la ruta está a partir del archivo thumb.php
	$file_info = getimagesize($file);
	// Obtenemos la relación de aspecto
	$ratio = $file_info[0] / $file_info[1];
echo "<br>".$file_info[0]." / ".$file_info[1]; 
	 
	// Calculamos las nuevas dimensiones
	$newwidth = $width;
	$newheight = round($newwidth / $ratio);
echo "<br>".$newwidth." / ".$newheight; 	
	 
	// Sacamos la extensión del archivo
	$ext = explode(".", $file);
	$ext = strtolower($ext[count($ext) - 1]);
	if ($ext == "jpeg") $ext = "jpg";
	 
	// Dependiendo de la extensión llamamos a distintas funciones
	switch ($ext) {
			case "jpg":
					$img = imagecreatefromjpeg($file);
			break;
			case "png":
					$img = imagecreatefrompng($file);
			break;
			case "gif":
					$img = imagecreatefromgif($file);
			break;
	}
	// Creamos la miniatura
	$thumb = imagecreatetruecolor($newwidth, $newheight);
	// La redimensionamos
	imagecopyresampled($thumb, $img, 0, 0, 0, 0, $newwidth, $newheight, $file_info[0], $file_info[1]);
	// La mostramos como jpg
	//header("Content-type: image/jpeg");
	imagejpeg($img, dirname($file)."/th.jpg");	
}
//crearMiniatura("C:/imagenes_hce_dev/2017/07/12/205/711_pterigio_od_6.jpg", 5);
function crearMiniatura2($archivo, $nuevo_ancho, $nuevo_alto=60){
	
	// Obtenemos la relación de aspecto
	list($ancho, $alto) = getimagesize($archivo);
	//$file_info = getimagesize($archivo);
	//$porcentaje = $file_info[0] / $file_info[1]; //0.5;	
	//echo "<br>file_info = ".$file_info[0]." / ".$file_info[1]; 
	$porcentaje = $ancho / $alto; 
echo "<br>ancho/alto/porcentaje = ".$ancho." / ".$alto." / ".$porcentaje; 
	 
	// Calculamos las nuevas dimensiones
	//$newwidth = $width;
	$nuevo_alto = round($nuevo_ancho / $porcentaje);
echo "<br> nuevo_ancho/nuevo_alto = ".$nuevo_ancho." / ".$nuevo_alto; 

	// Dependiendo de la extensión llamamos a distintas funciones para crear la nueva imagen	
	$ext = explode(".", $archivo);
	$ext = strtolower($ext[count($ext) - 1]);
	if ($ext == "jpeg") $ext = "jpg"; 
	
	switch ($ext) {
			case "jpg": $imagen = imagecreatefromjpeg($archivo); break;
			case "png": $imagen = imagecreatefrompng($archivo); break;
			case "gif": $imagen = imagecreatefromgif($archivo); break;
	}
	
	// Redimensionar
	$imagen_p = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);	
	imagecopyresampled($imagen_p, $imagen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);

	// Imprimir
	//$ruta_destino=dirname($archivo)." / thumbnail_".basename($archivo);
	$ruta_server="C:/imagenes_hce_dev"; 
	$ruta_destino="../tmp/thumbnails/thumb7".str_replace( "/" , "_" , str_replace($ruta_server, "", dirname($archivo)))."_".basename($archivo);
	echo "<br>".$ruta_destino;
	imagejpeg($imagen_p, 'C:/imagenes_hce_dev/2017/07/12/205/th63.jpg');
	imagejpeg($imagen_p, $ruta_destino); 	
}
crearMiniatura2('C:/imagenes_hce_dev/2017/07/12/205/711_pterigio_od_6.jpg', 60);
/**/	
	
	$id_hc_consulta=711;
	$id_admision=498;	
	
	$dbConsultasPterigio = new dbConsultasPterigio();	
	$dbListas = new DbListas();		
	$combo = new Combo_Box();	
	$utilidades = new Utilidades();	
	
	try {
		$var=6;
		$cero=0;
		//$var=$var/$cero;
		echo "<br>*****".$var."*****<br>"; 
		
		//$var=funcionInexistente($var);
		
		$errortry = 'Always throw this error';
		//throw new Exception($e);		
	}
	
	
	catch (Exception $e) {
		echo "<br>***** CATCHED! x ".$e->getMessage()."*****<br>";
		//20170621-ZJ: Log para seguimiento a error reportado por Paula(COTA) ("error -10"): 
		$sql = "INSERT INTO log_errores_reportados (fecha_log, id_usuario_crea, script_ln, ssql, variables) 
			VALUES (NOW(), 1, '".basename( __FILE__ )."-ln26', '".$e->getMessage()."', 'ss')";
		$dbConsultasPterigio->ejecutarSentencia($sql, array());
	}	
	
	//Lista para campos Sí/No 
	$lista_si_no = array(); 
	$lista_si_no[0]["id"] = "1"; 
	$lista_si_no[0]["valor"] = "S&iacute;"; 
	$lista_si_no[1]["id"] = "0"; 
	$lista_si_no[1]["valor"] = "No"; 	

	// Inicializar variables del formulario 
	$pterigio_obj = $dbConsultasPterigio->getConsultaPterigio($id_hc_consulta); 	
	
	//$arr_json='{"email":"mail@yahoo.es","password":"psw"}';
	
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
		
	<link href="../funciones/fine-uploader/fine-uploader-gallery.css" rel="stylesheet" type="text/css"/>
    
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
	
	<script src="../funciones/fine-uploader/fine-uploader.min.js"></script>
	<!--<script type="text/template" id="qq-template">-->
	<?php
		//zojo:$variable_archivo_ppal="vengo del archivo q me hace el requiere_once";
		//zojo:require_once("../src/fine-uploader/templates/gallery.php"); //ok!
		require_once("../funciones/fine-uploader/templates/gallery.html"); 			
	?> 
<?php 
			$directorio="../tmp/uploads/1/otra_cosa/otr";
			echo "dir = $directorio";
			if (!is_dir(dirname($directorio))){
				if (mkdir(dirname($directorio), 0777, true))
				{
					echo " ==> creé!!";
				} else {
					echo " ==> intenté pero no creé!!";
				}
			}	else{
				echo " ==> no creé!!";
			}
?> 	

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
			<td valign="top">
				<div id="uploader_pte_od"></div>
				<!--<div id="pdfButton_od" role="button" class="myCustomCssClassForStyling">Select a PDF</div>-->
				<!--<div id="foldersButton" role="button">Select Folders</div>-->
				<script>
					var uploader = new qq.FineUploader({
						
						element: document.getElementById("uploader_pte_od"), 
						
						debug: true, 
						//autoUpload: false, //uploader.uploadStoredFiles()
						
						request: {							
							endpoint: 'extension_pterigio_ajax_FineUp.php', 
							method: 'POST', 
							//forceMultipart: true; defaults to true 
							inputName: "pte_foto_od",  //ok!  // "pte_foto_od[]" genera err! (xq siempre envía por archivo)							
							params: {
								ojo: 'OD',
								hc: '715'								
							}, //or via uploader.setParams({param1: "foo", param2: "bar"});
							paramsInBody: true
							//params: obtener_parametros_consulta_pterigio()"oi"; 
						}, 
						deleteFile: {
							enabled: true, // defaults to false
							endpoint: 'extension_pterigio_ajax_FineUp.php', 
							method: 'POST' //vs 'DELETE'
							//params: {} object
						}, 
						//chunking{}
						//form()
						messages: {
							emptyError: "vacío???", 
							onLeave: "Los archivos se están cargando, si abandona la página se cancelará el cargue.", 
							typeError: "Extension no válida. Extensiones permitidas: {extensions}.", 
							sizeError: "{file} es muy grande, el tamaño máximo es {sizeLimit}", 
							retryFailTooManyItemsError: "Reintento fallido - Ha alcanzado el límite de archivos", 							
							tooManyItemsError: "Too many items ({netItems}) would be uploaded. Item limit is {itemLimit}."
						}, 
						
						/*retry: {
						   enableAuto: true, // defaults to false
						   showButton: true
						}, */
						
						/*
								chunking: {
									enabled: true,
									concurrent: {
										enabled: true
									},
									success: {
										endpoint: "/vendor/fineuploader/php-traditional-server/endpoint.php?done"
									}
								},
								resume: {
									enabled: true
								},
						*/						
						
						validation: {							
							allowedExtensions: ["jpeg", "jpg", "tiff", "gif", "pdf", "xls", "xlsx"],
							//acceptFiles: "audio/*, video/*, image/*, MIME_type", 
							sizeLimit: 25000000 // 5 MiB
						},
						
					
						/*,			
						extraButtons: [
							{
								element: document.getElementById("pdfButton"),
								validation: {
									allowedExtensions: ["pdf"]
								}
							}
						]*/			
						
						/*failedUploadTextDisplay: {
							mode: 'custom',
							maxChars: 40,
							responseProperty: 'error',
							enableTooltip: true
						}	*/	

						callbacks: {
							onUpload: function(id, name) {
								console.log("enviando id="+id+"; name="+name);
							},
							onComplete: function(id, xhr, isError) {
								console.log("carga finalizada para id="+id);
							},
							onDelete: function(id) {
								console.log("borrando id="+id);
							},
							onDeleteComplete: function(id, xhr, isError) {
								console.log("borré id="+id);
							}							
						},
			
						/*thumbnails: {
							customResizer: !qq.ios() && function(resizeInfo) {
								return new Promise(function(resolve, reject) {
									pica.resizeCanvas(resizeInfo.sourceCanvas, resizeInfo.targetCanvas, {}, resolve)
								})
							}
						},*/											
					})								
				</script>			
			</td>
			<td><label>Foto Pterigio</label></td>
			<td valign="top">
				(Proceso teórico)
				<div id="uploader_pte_oi"></div>
				<script>
					var uploader2 = new qq.FineUploader({
						element: document.getElementById("uploader_pte_oi"), 
						
						debug: true, 
						//autoUpload: false,
						
						request: {							
							endpoint: 'extension_pterigio_ajax_FineUp0.php'
						}, 
						deleteFile: {
							enabled: true, // defaults to false							
							endpoint: 'extension_pterigio_ajax_FineUp0.php'							
						}						
					})			
					/*
					qq(document.getElementById("upload-button")).attach('click', function() {
						uploader.uploadStoredFiles();
						uploader2.uploadStoredFiles();
					});	*/
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