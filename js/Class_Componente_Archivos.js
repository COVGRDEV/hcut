// JavaScript Document

var uploader_default_messages = {
	emptyError: "vacío???", 
	onLeave: "Los archivos se están cargando, si abandona la página se cancelará el cargue.", 
	typeError: "Extension no válida. Extensiones permitidas: {extensions}.", 
	sizeError: "{file} es muy grande, el tamaño máximo es {sizeLimit}", 
	retryFailTooManyItemsError: "Reintento fallido - Ha alcanzado el límite de archivos", 							
	tooManyItemsError: "Hay muchos archivos para cargar ({netItems}). El límite de archivos es {itemLimit}." 
}

function reset_uploaders() {
	var id; 
	$("[id*='uploader_']").each(function(){
	   /*$(this).prop("id"); 
	   obj=eval($(this).prop("id"));
	   //console.log(id);
	   obj.reset(); */
	   console.log(" --> reseted!"); 
	});		
	if (window.uploader_pte_foto_od) { uploader_pte_foto_od.reset(); }
	if (window.uploader_pte_foto_oi) { uploader_pte_foto_oi.reset(); }
}

function ver_archivos(id_reg_archivos, modulo, id_modulo, credencial, id_menu) {
	mostrar_archivos_div(id_reg_archivos, modulo, id_modulo, "visor_archivos.php", credencial, id_menu);
}

function mostrar_archivos_div(id_reg_archivos, modulo, id_modulo, pagina_consulta, credencial, menu) {
	console.log("MOSTRAR_ARCHIVOS_DIV: "+id_reg_archivos+" / "+modulo+" / "+id_modulo+" / "+credencial+" / "+menu);
	mostrar_formulario_flotante_extend(1);
	$("#d_interno_extend").empty();
	$('#d_interno_extend').height("99%");
	
	var HcFrame = document.createElement("iframe");
	HcFrame.id = "HcFrame";    
	ruta = pagina_consulta + '?hdd_id_reg_archivos=' + id_reg_archivos + 
			'&hdd_modulo=' + modulo + 
			'&hdd_id_modulo=' + id_modulo + 
			'&credencial=' + credencial +
			'&hdd_numero_menu=' + menu + '&tipo_entrada=1';
	
	HcFrame.setAttribute("src", ruta); 
	HcFrame.style.height='100%';
	HcFrame.style.width='99%';
	var control = document.getElementById("HcFrame")
	$("#d_interno_extend").append(HcFrame);
}