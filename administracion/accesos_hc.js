function abrir_buscar_paciente() {
	$("#d_interno_pacientes").html("");
	
    var params = "opcion=1";
	
    llamarAjax("accesos_hc_ajax.php", params, "d_interno_pacientes", "");
	mostrar_formulario_pacientes(1);
}

function limpiar_paciente() {
	$("#hdd_paciente").val("");
	$("#txt_paciente").val("");
}

function mostrar_formulario_pacientes(tipo) {
    if (tipo == 1) { //mostrar
        $('#fondo_negro_pacientes').css('display', 'block');
        $('#d_centro_pacientes').slideDown(400).css('display', 'block');

    } else if (tipo == 0) { //Ocultar
        $('#fondo_negro_pacientes').css('display', 'none');
        $('#d_centro_pacientes').slideDown(400).css('display', 'none');
    }
}

function buscar_pacientes() {
	if (trim($("#txp_paciente_b").val()) == "") {
		alert("Debe indicar el documento o nombre a buscar");
		$("#txp_paciente_b").focus();
		return;
	}
	
    var params = "opcion=2&texto_b=" + str_encode($("#txp_paciente_b").val());
	
    llamarAjax("accesos_hc_ajax.php", params, "d_buscar_pacientes", "");
}

function seleccionar_paciente(id_paciente, nombre_completo) {
	$("#hdd_paciente").val(id_paciente);
	$("#txt_paciente").val(nombre_completo);
	
	mostrar_formulario_pacientes(0);
}

function buscar_accesos() {
	if ($("#cmb_usuario").val() == "" && $("#hdd_paciente").val() == "" && $("#txt_fecha_inicial").val() == "" && $("#txt_fecha_final").val() == "") {
		alert("Debe seleccionar un par\xe1metro de b\xfasqueda");
		return;
	}
	if ($("#txt_fecha_inicial").val() == "" && $("#txt_fecha_final").val() != "") {
		alert("La fecha inicial no debe ser vac\xeda");
		return;
	}
	if ($("#txt_fecha_inicial").val() != "" && $("#txt_fecha_final").val() == "") {
		alert("La fecha final no debe ser vac\xeda");
		return;
	}
	
	var params = "opcion=3&id_usuario=" + $("#cmb_usuario").val() +
				 "&id_paciente=" + $("#hdd_paciente").val() +
				 "&fecha_inicial=" + $("#txt_fecha_inicial").val() +
				 "&fecha_final=" + $("#txt_fecha_final").val();
	
    llamarAjax("accesos_hc_ajax.php", params, "d_accesos_hc", "");
}

function exportar_a_excel() {
	if (isObject(document.getElementById("frm_excel"))) {
		document.getElementById("frm_excel").submit();
	} else {
		alert("Debe realizar una b\xfasqueda.");
	}
}
