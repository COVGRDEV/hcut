
/***********************************************/
/***********************************************/
/***********************************************/

$(document).ready( function() { 
	config_reproducido("OI"); 
	config_reproducido("OD"); 
});

if (CKEDITOR.env.ie && CKEDITOR.env.version < 9) {
	CKEDITOR.tools.enableHtml5Elements(document);
}

CKEDITOR.config.height = 70;
CKEDITOR.config.width = "auto";

var initCKEditorPte = (function(id_obj) {
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

function validar_ojo(ojo){
	var resultado=0; 
	
	ojo=ojo.toLowerCase();
	
	$("#pte_grado_" + ojo).removeClass("borde_error"); 
	$("#pte_reproducido_" + ojo).removeClass("borde_error"); 
	$("#pte_conjuntiva_sup_" + ojo).removeClass("borde_error"); 
	$("#pte_astigmatismo_" + ojo).removeClass("borde_error"); 		
		
	if ($("#pte_grado_" + ojo).val()=="") {
		
		//Ningún campo dependiente debe estar digitado
		
		if ($("#pte_reproducido_" + ojo).val() != "" 
			/*|| $("#pte_conjuntiva_sup_" + ojo).val() != ""*/ 
			|| $("#pte_astigmatismo_" + ojo).val() != ""  
		) { 
			$("#pte_grado_" + ojo).addClass("borde_error"); 
			resultado = -1; 
		}
	} else {
		
		//Validar campos dependientes: 
		
		if ($("#pte_reproducido_" + ojo).val() == "") {
			$("#pte_reproducido_" + ojo).addClass("borde_error"); 
			resultado = -1; 
		} else if ( $("#pte_reproducido_" + ojo).val() == "1" && $("#pte_conjuntiva_sup_" + ojo).val() == "" ) {
				$("#pte_conjuntiva_sup_" + ojo).addClass("borde_error"); 
				resultado = -1; 
		}
		
		if ($("#pte_astigmatismo_" + ojo).val() == "") {
			$("#pte_astigmatismo_" + ojo).addClass("borde_error"); 
			resultado = -1; 
		} 	
	}
	
	return resultado; 
}

function validar_consulta_pterigio() {
	var resultado = 0, resultado1 = 0, resultado2 = 0;
	var ojo;

	//$("#pte_grado_od" + ojo).removeClass("borde_error"); 
	//$("#pte_grado_oi" + ojo).removeClass("borde_error"); 
	$("#label_grado").removeClass("borde_error");
	$("#label_grado").css({"color": "#5B5B5B"}); 
	
	// Seleccionar mínimo un ojo con Pterigio 
	if ( $("#pte_grado_od").val()=="" && $("#pte_grado_oi").val()=="" ) {
		//$("#pte_grado_od" + ojo).addClass("borde_error"); 
		//$("#pte_grado_oi" + ojo).addClass("borde_error"); 
		$("#label_grado").addClass("borde_error");
		$("#label_grado").css({"color": "#FF002A"});

		resultado=-1;
	}	
	
	resultado1=validar_ojo("OD");	
	resultado2=validar_ojo("OI");
	
	if ( resultado1<0 || resultado2<0 ) {
		resultado=-1;
	} 
	
	return resultado; 
}

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
/* 
function agregar_ckeditor_pterigio() { 
	initCKEditorOculoplastia("txt_observ_XXXXXX"); 
} 
*/

function config_grado(sufijo_ojo) { 
	sufijo_ojo=sufijo_ojo.toLowerCase(); 
	if ($("#pte_grado_"+sufijo_ojo).val()=="") { 
		$("#pte_reproducido_"+sufijo_ojo).val(""); 
		$("#pte_conjuntiva_sup_"+sufijo_ojo).val(""); 
		$("#pte_astigmatismo_"+sufijo_ojo).val(""); 
	}  
} 

function config_reproducido(sufijo_ojo) { 
	sufijo_ojo=sufijo_ojo.toLowerCase(); 	
	if ($("#pte_reproducido_"+sufijo_ojo).val()==1) { 
		$("#pte_conjuntiva_sup_"+sufijo_ojo).attr("disabled", false); 
	} else { 
		$("#pte_conjuntiva_sup_"+sufijo_ojo).val(""); 
		$("#pte_conjuntiva_sup_"+sufijo_ojo).attr("disabled", true); 
	} 
} 

function copiar_campo_pterigio(id_campo_origen, id_campo_destino) {
	var campo_origen = $("#" + id_campo_origen).val();
	$("#" + id_campo_destino).val(campo_origen);
}
