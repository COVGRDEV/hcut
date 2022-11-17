
/***********************************************/
/***********************************************/
/***********************************************/

/*$(document).ready( function() { 
	config_reproducido("OI"); 
	config_reproducido("OD"); 
});*/

if (CKEDITOR.env.ie && CKEDITOR.env.version < 9) {
	CKEDITOR.tools.enableHtml5Elements(document);
}

CKEDITOR.config.height = 70;
CKEDITOR.config.width = "auto";

var initCKEditorNeso = (function(id_obj) {
	var wysiwygareaAvailable = isWysiwygareaAvailable(),
		isBBCodeBuiltIn = !!CKEDITOR.plugins.get("bbcode");
	
	return function(id_obj) {
		var editorElement = CKEDITOR.document.getById(id_obj);
		
		//Dependiendo de la disponibilidad del plugin wysiwygare se incia el editor clásico o el editor en línea.
		if (wysiwygareaAvailable) {
			CKEDITOR.replace(id_obj);
		} else {
			editorElement.setAttribute("contenteditable", "true");
			CKEDITOR.inline(id_obj);
		}
	};
	
	function isWysiwygareaAvailable() {
		if (CKEDITOR.revision == ("%RE" + "V%")) {
			return true;
		}
		
		return !!CKEDITOR.plugins.get("wysiwygarea");
	}
} )();

/* Retorna la coordenada X de un huso horario */
function px(huso){
	switch (huso) {				
		case 1: return 190; break; 
		case 2: return 232; break; 
		case 3: return 247; break; 
		case 4: return 232; break; 
		case 5: return 190; break; 
		case 6: return 135; break; 
		case 7: return 78; break; 
		case 8: return 36; break; 
		case 9: return 20; break; 
		case 10: return 36; break; 
		case 11: return 78; break; 
		case 12: return 135; break; 
	} 
}

/* Retorna la coordenada Y de un huso horario */
function py(huso){ 
	switch (huso) { 				
		case 1: return 36; break; 
		case 2: return 78; break; 
		case 3: return 135; break; 
		case 4: return 190; break; 
		case 5: return 232; break; 
		case 6: return 247; break; 
		case 7: return 232; break; 
		case 8: return 190; break; 
		case 9: return 135; break; 
		case 10: return 78; break; 
		case 11: return 36; break; 
		case 12: return 20; break; 
	} 
}	

/* Accede al CANVAS en el iframe de cada objeto de dibujo (pizarra) */
function getContextoCanvas(sufijo_ojo){	
	doc_iframe=window.frames["ifr_neso_img_"+sufijo_ojo].contentWindow.document;
	obj_canvas=doc_iframe.getElementsByTagName("canvas")[1]; //vector {canvas.wPaint-canvas-bg, canvas.wPaint-canvas, canvas.wPaint-canvas-temp}
	context=obj_canvas.getContext('2d'); 
	return context; 
}

function xH(id_huso){
	if (id_huso>12) { id_huso=id_huso-12; }
	switch (id_huso) {				
		case 1: x = 190; break; 
		case 2: x = 232; break; 
		case 3: x = 247; break; 
		case 4: x = 232; break; 
		case 5: x = 190; break; 
		case 6: x = 135; break; 
		case 7: x = 78; break; 
		case 8: x = 36; break; 
		case 9: x = 20; break; 
		case 10: x = 36; break; 
		case 11: x = 78; break; 
		case 12: x = 135; break; 		
	} 
	x = x + 55; //Se agrega el desplazamiento por ubicación del .png en el iframe-canvas
	return x;
}

function yH(id_huso){ 
	if (id_huso>12) { id_huso=id_huso-12; }
	switch (id_huso) { 				
		case 1: y = 36; break; 
		case 2: y = 78; break; 
		case 3: y = 135; break; 
		case 4: y = 190; break; 
		case 5: y = 232; break; 
		case 6: y = 247; break; 
		case 7: y = 232; break; 
		case 8: y = 190; break; 
		case 9: y = 135; break; 
		case 10: y = 78; break; 
		case 11: y = 36; break; 
		case 12: y = 20; break; 
	} 
	y= y + 0; //Se agrega el desplazamiento por ubicación del .png en el iframe-canvas
	return y;
}	

function gH(id_huso){ 
	if (id_huso>12) { id_huso=id_huso-12; }
	switch (id_huso) { 				
		case 1: grados = 300; break; 
		case 2: grados = 330; break; 
		case 3: grados = 0; break; 
		case 4: grados = 30; break; 
		case 5: grados = 60; break; 
		case 6: grados = 90; break; 
		case 7: grados = 120; break; 
		case 8: grados = 150; break; 
		case 9: grados = 180; break; 
		case 10: grados = 210; break; 
		case 11: grados = 240; break; 
		case 12: grados = 270; break; 
	} 
	return grados;
}


/* Dibuja cada lesión NESO descrita en "Husos horarios comprometidos" */
function dibujarNeso(idojo){
	
	if (idojo==79) { sufijo_ojo='od'; } 
	if (idojo==80) { sufijo_ojo='oi'; } 
	
console.log("INI dibujandoNeso"); 
	// Constantes
	var mmcornea=12; //Longitud promedio de una córnea; milímetros
	var xcentro=190; //Coordenada X del canvas, correspondiente al centro de la córnea 
	var ycentro=135; //Coordenada Y del canvas, correspondiente al centro de la córnea 
	
	var canti_lesiones; 
	var huso_ini, canti_husos, mmcornea; 
	var huso_fin, huso_medio;	
	
	// Pintar el NESO de cada lesión: 
	canti_lesiones=$("[id^='neso_hdd_idojo_"+sufijo_ojo+"_']").length;
	for (var i=1; i<=canti_lesiones; i++) {
console.log("neso "+i); 
		
		sufijo=sufijo_ojo+"_"+i; 
		
		// Variables de entrada 
		id_ojo=$("#neso_hdd_idojo_"+sufijo).val(); //79:OD; 80:OI  
		canti_husos=parseInt($("#neso_nhusos_"+sufijo).val()); 
		huso_ini=parseInt($("#neso_husoini_"+sufijo).val()); 
		mm_neso=parseInt($("#neso_mmcornea_"+sufijo).val());  
		console.log("entradas: (id_ojo="+id_ojo+", canti_husos="+canti_husos+", huso_ini="+huso_ini+", mm_neso="+mm_neso); 
/*
		huso_ini=5; 
		canti_husos=3; 
		mm_neso=5; 
		*/		
/*		if ( (sufijo_ojo=="od" && id_ojo!=79) || (sufijo_ojo=="oi" && id_ojo!=80) ) { 
			console.log("obviado xq no es del lote en modificación"); 
			continue; 
		}*/	
	
		// Solo se pintan las lesiones que tienen datos completos: 
		//isNaN(parseInt($("#neso_dosis").val()
		//if (isNaN(parseInt(canti_husos)) && isNaN(parseInt(huso_ini)) && isNaN(parseInt(mm_neso))) { 
		if (isNaN(canti_husos) || isNaN(huso_ini) || isNaN(mm_neso)) { 
			console.log("obviado x datos incompletos"); 
			continue; 
		}
		//PENDIENTE: marcar como error los campos vacíos; manejar onChange la validación de requeridos si se digitó alguno 
		
		contexto=getContextoCanvas(sufijo_ojo); 		
		//contexto.drawImage(Imagen_husos, 0, 0); 		
		//contexto.save();
		
		contexto.lineWidth = 1; 

		xini=xH(huso_ini);
		yini=yH(huso_ini);
		
		huso_fin=huso_ini+canti_husos;
		
		radio0_xH=113; //radio original, en píxeles
		radio0_mm=mmcornea/2; //300; //radio original, en milímetros, escalado a longitud aproximada de la córnea
		radio_huso = radio0_xH - (mm_neso * ( radio0_xH / radio0_mm )); 
		
		//var radianes = (Math.PI / 180) * grados;
		if (mm_neso<=radio0_mm) {
			anguloIni = (Math.PI / 180) * gH(huso_ini); 
			anguloFin = (Math.PI / 180) * gH(huso_fin);	
			sentido=false;			
		} else {	
			radio_huso = radio0_mm - radio_huso;
			anguloIni = (Math.PI / 180) * gH(huso_fin) + Math.PI; 
			anguloFin = (Math.PI / 180) * gH(huso_ini) + Math.PI; 					
			sentido=true;
		}
		console.log("gH(huso_ini), gH(huso_fin) = "+gH(huso_ini)+", "+gH(huso_fin)+"; radio_huso = "+radio_huso+"; radio_huso FINAL = "+radio_huso);		
		
		context.beginPath(); 
		//contexto.strokeStyle="red";
		context.arc(xcentro, ycentro, radio_huso, anguloIni, anguloFin, sentido); 
		//context.arc(xcentro, ycentro, radio_huso, anguloFin, anguloIni, !sentido); 
		contexto.lineTo(xH(huso_fin), yH(huso_fin));
		contexto.stroke();		 
		//context.beginPath(); 
		context.arc(xcentro, ycentro, radio_huso, anguloFin, anguloIni, !sentido); 
		//context.arc(xcentro, ycentro, radio_huso, anguloIni, anguloFin, sentido); 
		contexto.lineTo(xH(huso_ini), yH(huso_ini));
		contexto.stroke(); 
	} 	
}	

function guardar_imagenes_neso() {
console.log("INI guardar imgs neso");
	document.getElementById("ifr_neso_img_od").contentWindow.guardar_imagen();
	document.getElementById("ifr_neso_img_oi").contentWindow.guardar_imagen();
console.log("FIN guardar imgs neso");	
}

function validar_ojo_neso(ojo){
console.log("validar_ojo");		
	var resultado=0; 
	var boo_husos=false;
	
	ojo=ojo.toLowerCase();

	$("#table_husos_" + ojo).removeClass("borde_error"); 
	//$("#table_husos_" + ojo).css({"color": "#5B5B5B"});	
	$("#neso_mm_cornea_" + ojo).removeClass("borde_error"); 
	$("#neso_mm_cornea_").css({"color": "#5B5B5B"});
	$("#neso_ind_recidivante_" + ojo).removeClass("borde_error"); 
	$("#neso_ind_recidivante_").css({"color": "#5B5B5B"});	
	
	for (i=1; i<=12; i++) { 
		if($("#neso_huso" + i + "_" + ojo).is(":checked")) { boo_husos=true; break; } 
	} 	
	
	if (boo_husos===false) { 
		
		resultado = -2; 
		
		//Ningún campo dependiente debe estar digitado 
		
		if (/*!isNaN(parseInt($("#neso_mm_cornea_").val()))*/$("#neso_mm_cornea_" + ojo).val() != "" 
			|| $("#neso_ind_recidivante_" + ojo).val() != ""  
		) {
			$("#table_husos_" + ojo).addClass("borde_error"); 
			//$("#table_husos_" + ojo).css({"color": "#FF002A"}); 			
			resultado = -1; 
		}
	} else { 

		//Validar campos dependientes: 
		
		if ($("#neso_mm_cornea_" + ojo).val() == "") { 
			$("#neso_mm_cornea_" + ojo).addClass("borde_error"); 
			$("#neso_mm_cornea_").css({"color": "#5B5B5B"}); 
			resultado = -1; 
		}
		if ($("#neso_ind_recidivante_" + ojo).val() == "") { 
			$("#neso_ind_recidivante_" + ojo).addClass("borde_error"); 
			$("#neso_ind_recidivante_").css({"color": "#5B5B5B"}); 
			resultado = -1; 
		} 	
	}
	
	return resultado; 
}

function validar_consulta_neso() {
	var resultado = 0, resultado1 = 0, resultado2 = 0;
	var ojo;
	
	$("#label_husos").removeClass("borde_error");
	$("#label_husos").css({"color": "#5B5B5B"}); 
	$("#neso_ind_interferon").removeClass("borde_error"); 
	$("#neso_ind_interferon").css({"color": "#5B5B5B"}); 
	$("#neso_dosis").removeClass("borde_error"); 
	$("#neso_dosis").css({"color": "#5B5B5B"});		

	if ($("#neso_ind_interferon").val()=="") {
		$("#neso_ind_interferon").addClass("borde_error"); 
		$("#neso_ind_interferon").css({"color": "#5B5B5B"}); 		
		resultado=-1;
	}

	// Si usa interferón, pedir Número de dosis 
	if ($("#neso_ind_interferon").val()=="1" && isNaN(parseInt($("#neso_dosis").val()))) { 
		$("#neso_dosis").addClass("borde_error"); 
		$("#neso_dosis").css({"color": "#5B5B5B"}); 	
		resultado=-1;
	}	

	// Seleccionar mínimo un ojo con NESO
	resultado1 = validar_ojo_neso("OD");	
	resultado2 = validar_ojo_neso("OI");	
	
	if ( resultado1<0 || resultado2<0 ) {
		resultado=-1;		
	} 
	if ( resultado1==-2 && resultado2==-2 ) {
		$("#label_husos").addClass("borde_error");
		$("#label_husos").css({"color": "#FF002A"});
	}	
	
	return resultado; 
}

function obtener_parametros_consulta_neso() {
	
	//Se guardan las imágenes
	guardar_imagenes_neso();
	
	var neso_observaciones = str_encode(CKEDITOR.instances.neso_observaciones.getData());
	 
	var params = "&neso_ind_interferon_od=" + $("#neso_ind_interferon_od").val() + 
				"&neso_dosis_od=" + $("#neso_dosis_od").val() + 	
				"&neso_img_od=" + str_encode($("#neso_img_od").val()) + 
				"&neso_ind_recidivante_od=" + $("#neso_ind_recidivante_od").val() + 
				"&neso_ind_interferon_oi=" + $("#neso_ind_interferon_oi").val() + 
				"&neso_dosis_oi=" + $("#neso_dosis_oi").val() + 	
				"&neso_img_oi=" + str_encode($("#neso_img_oi").val()) + 
				"&neso_ind_recidivante_oi=" + $("#neso_ind_recidivante_oi").val() +  
				"&neso_observaciones=" + neso_observaciones +
				"&neso_id_paciente=" + $("#hdd_neso_id_paciente").val();;  
				
	
	//Para lesiones NESO
	
	var array_lesiones_idojo = new Array();
	var array_lesiones_nhusos = new Array();
	var array_lesiones_husoini = new Array();
	var array_lesiones_mmcornea = new Array();			
	var array_tipos_ojos = ["od", "oi"];	

	canti = array_tipos_ojos.length;
	for (k = 0; k < canti; k++) {
		
		canti_lesiones=$("[id^='neso_hdd_idojo_"+array_tipos_ojos[k]+"']").length;
		for (i = 1; i <= canti_lesiones; i++) { 
			sufijo=array_tipos_ojos[k]+"_"+i; 
			id_ojo=$("#neso_hdd_idojo_"+sufijo).val(); //79:OD; 80:OI  			
			canti_husos=$("#neso_nhusos_"+sufijo).val(); 
			huso_ini=$("#neso_husoini_"+sufijo).val();
			mmcornea=$("#neso_mmcornea_"+sufijo).val();  
			
			fila_valida=true; 
			if (canti_husos == "" || huso_ini=="" || mmcornea=="") { fila_valida=false; }
			if (fila_valida==true) { 
				array_lesiones_idojo.push(id_ojo);
				array_lesiones_nhusos.push(canti_husos);
				array_lesiones_husoini.push(huso_ini);
				array_lesiones_mmcornea.push(mmcornea);			
			} 		
		}
	}
	
	params += '&array_lesiones_idojo=' + array_lesiones_idojo + 
			  '&array_lesiones_nhusos=' + array_lesiones_nhusos + 
			  '&array_lesiones_husoini=' + array_lesiones_husoini + 	
			  '&array_lesiones_mmcornea=' + array_lesiones_mmcornea;		
	
	console.log(params); 	
	return params; 
}
/*
function config_grado_neso(sufijo_ojo) { 
	sufijo_ojo=sufijo_ojo.toLowerCase(); 
	if ($("#pte_grado_"+sufijo_ojo).val()=="") { 
		$("#pte_reproducido_"+sufijo_ojo).val(""); 
		$("#pte_conjuntiva_sup_"+sufijo_ojo).val(""); 
		$("#pte_astigmatismo_"+sufijo_ojo).val(""); 
	}  
} 

function config_reproducido_neso(sufijo_ojo) { 
	sufijo_ojo=sufijo_ojo.toLowerCase(); 	
	if ($("#pte_reproducido_"+sufijo_ojo).val()==1) { 
		$("#pte_conjuntiva_sup_"+sufijo_ojo).attr("disabled", false); 
	} else { 
		$("#pte_conjuntiva_sup_"+sufijo_ojo).val(""); 
		$("#pte_conjuntiva_sup_"+sufijo_ojo).attr("disabled", true); 
	} 
} 
*/