// JavaScript Document

function ver_contactos_antecedentes_med() {
	var cant_antecedentes = parseInt($("#hdd_cant_antecedentes").val(), 10);
	var cont_aux = 0;
	var params = "opcion=1";
	for (var i = 0; i < cant_antecedentes; i++) {
		if ($("#chk_ant_med_" + i).is(":checked")) {
			params += "&id_antecedente_med_" + cont_aux + "=" + $("#hdd_ant_med_" + i).val();
			cont_aux++;
		}
	}
	
	if (cont_aux > 0) {
		params += "&cant_antecedentes=" + cont_aux;
		
		$("#d_interno_contactos_antecedentes").html("(Sin contactos)");
		llamarAjax("antecedentes_ajax.php", params, "d_interno_contactos_antecedentes", "continuar_ver_contactos_antecedentes_med();");
	} else {
		$("#d_interno_contactos_antecedentes").html("(Sin contactos)");
		$("#d_contactos_antecedentes").hide(200);
	}
}

function continuar_ver_contactos_antecedentes_med() {
	var cant_contactos_antecedentes_med = parseInt($("#hdd_cant_contactos_antecedentes_med").val(), 10);
	
	if (cant_contactos_antecedentes_med > 0) {
		$("#d_contactos_antecedentes").show(200);
	} else {
		$("#d_interno_contactos_antecedentes").html("(Sin contactos)");
		$("#d_contactos_antecedentes").hide(200);
	}
}

function abrir_cerrar_contactos_antecedentes_med() {
	if ($("#d_contactos_antecedentes").is(":visible")) {
	$("#d_contactos_antecedentes").hide(200);
	} else {
		$("#d_contactos_antecedentes").show(200);
	}
}

function obtener_parametros_antecedentes() {
	var cant_antecedentes = parseInt($("#hdd_cant_antecedentes").val(), 10);
	var params = "&cant_antecedentes=" + cant_antecedentes;
	
	for (i = 0; i < cant_antecedentes; i++) {
		params += "&id_antecedentes_medicos_" + i + "=" + $("#hdd_antecedente_medico_" + i).val() +
				  "&texto_antecedente_" + i + "=" + eval("str_encode(CKEDITOR.instances.txt_texto_antecedente_" + i + ".getData())");
	}
	return params;
}
