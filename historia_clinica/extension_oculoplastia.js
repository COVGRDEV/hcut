// JavaScript Document

/***********************************************/
/*Configuración del editor de texto enriquecido*/
/***********************************************/

if (CKEDITOR.env.ie && CKEDITOR.env.version < 9) {
	CKEDITOR.tools.enableHtml5Elements(document);
}

CKEDITOR.config.width = 'auto';
CKEDITOR.config.height = 55;

var initCKEditorOculoplastia = (function(id_obj) {
	var wysiwygareaAvailable = isWysiwygareaAvailable(),
		isBBCodeBuiltIn = !!CKEDITOR.plugins.get('bbcode');
	
	return function(id_obj) {
		var editorElement = CKEDITOR.document.getById(id_obj);
		
		//Dependiendo de la disponibilidad del plugin wysiwygare se incia el editor clásico o el editor en línea.
		if (wysiwygareaAvailable) {
			CKEDITOR.replace(id_obj);
		} else {
			editorElement.setAttribute('contenteditable', 'true');
			CKEDITOR.inline(id_obj);
		}
	};
	
	function isWysiwygareaAvailable() {
		if (CKEDITOR.revision == ('%RE' + 'V%')) {
			return true;
		}
		
		return !!CKEDITOR.plugins.get('wysiwygarea');
	}
} )();

/***********************************************/
/***********************************************/
/***********************************************/

function validar_consulta_oculoplastia() {
	var resultado = 0;
	var cant_oculoplastia_antec = parseInt($("#hdd_cant_oculoplastia_antec").val(), 10);
	
	for (var i = 0; i < cant_oculoplastia_antec; i++) {
		$("#txt_texto_antec_ocp_" + i).removeClass("borde_error");
		if (trim($("#txt_texto_antec_ocp_" + i).val()) == "" && $("#txt_fecha_antec_ocp_" + i).val() != "") {
			$("#txt_texto_antec_ocp_" + i).addClass("borde_error");
			resultado = -1;
		}
	}
	
	return resultado;
}

function obtener_parametros_consulta_oculoplastia() {
	var params = "&exoftalmometria_od=" + str_encode($("#txt_exoftalmometria_od").val()) +
				 "&exoftalmometria_base=" + str_encode($("#txt_exoftalmometria_base").val()) +
				 "&exoftalmometria_oi=" + str_encode($("#txt_exoftalmometria_oi").val()) +
				 "&observ_orbita=" + str_encode(CKEDITOR.instances.txt_observ_orbita.getData()) +
				 "&observ_cejas=" + str_encode(CKEDITOR.instances.txt_observ_cejas.getData()) +
				 "&fme_od=" + str_encode($("#txt_fme_od").val()) +
				 "&fme_oi=" + str_encode($("#txt_fme_oi").val()) +
				 "&dmr_od=" + str_encode($("#txt_dmr_od").val()) +
				 "&dmr_oi=" + str_encode($("#txt_dmr_oi").val()) +
				 "&fen_od=" + str_encode($("#txt_fen_od").val()) +
				 "&fen_oi=" + str_encode($("#txt_fen_oi").val()) +
				 "&observ_parpados=" + str_encode(CKEDITOR.instances.txt_observ_parpados.getData()) +
				 "&observ_pestanas=" + str_encode(CKEDITOR.instances.txt_observ_pestanas.getData()) +
				 "&gm_expresibilidad_od=" + str_encode($("#txt_gm_expresibilidad_od").val()) +
				 "&gm_expresibilidad_oi=" + str_encode($("#txt_gm_expresibilidad_oi").val()) +
				 "&gm_calidad_expr_od=" + str_encode($("#txt_gm_calidad_expr_od").val()) +
				 "&gm_calidad_expr_oi=" + str_encode($("#txt_gm_calidad_expr_oi").val()) +
				 "&observ_glandulas_meib=" + str_encode(CKEDITOR.instances.txt_observ_glandulas_meib.getData()) +
				 "&prueba_irrigacion_od=" + str_encode($("#txt_prueba_irrigacion_od").val()) +
				 "&prueba_irrigacion_oi=" + str_encode($("#txt_prueba_irrigacion_oi").val()) +
				 "&observ_via_lagrimal=" + str_encode(CKEDITOR.instances.txt_observ_via_lagrimal.getData());
	
	var cant_oculoplastia_antec = parseInt($("#hdd_cant_oculoplastia_antec").val(), 10);
	params += "&cant_oculoplastia_antec=" + cant_oculoplastia_antec;
	for (var i = 0; i < cant_oculoplastia_antec; i++) {
		params += "&id_antec_ocp_" + i + "=" + $("#hdd_id_antec_ocp_" + i).val() +
				  "&texto_antec_ocp_" + i + "=" + str_encode($("#txt_texto_antec_ocp_" + i).val()) + 
				  "&fecha_antec_ocp_" + i + "=" + $("#txt_fecha_antec_ocp_" + i).val();
	}
	
	var cant_oculoplastia_compl = parseInt($("#hdd_cant_oculoplastia_compl").val(), 10);
	params += "&cant_oculoplastia_compl=" + cant_oculoplastia_compl;
	for (var i = 0; i < cant_oculoplastia_compl; i++) {
		params += "&id_compl_ocp_" + i + "=" + $("#hdd_id_compl_ocp_" + i).val() +
				  "&ind_compl_ocp_" + i + "=" + ($("#chk_compl_ocp_" + i).is(":checked") ? 1 : 0);
	}
	
	return params;
}

function agregar_ckeditor_oculoplastia() {
	initCKEditorOculoplastia("txt_observ_orbita");
	initCKEditorOculoplastia("txt_observ_cejas");
	initCKEditorOculoplastia("txt_observ_parpados");
	initCKEditorOculoplastia("txt_observ_pestanas");
	initCKEditorOculoplastia("txt_observ_glandulas_meib");
	initCKEditorOculoplastia("txt_observ_via_lagrimal");
}

function copiar_campo_oculoplastia(id_campo_origen, id_campo_destino) {
	var campo_origen = $("#" + id_campo_origen).val();
	$("#" + id_campo_destino).val(campo_origen);
}
