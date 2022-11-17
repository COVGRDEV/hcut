function buscar_cotizaciones() {
	if (validar_buscar_cotizaciones()) {
		var params = "opcion=1&txt_paciente=" + str_encode($("#txt_paciente_b").val()) +
					 "&id_proc_cotiz=" + $("#cmb_proc_cotiz_b").val() +
					 "&observaciones_cotiz=" + str_encode($("#txt_observaciones_cotiz_b").val()) +
					 "&fecha_ini=" + $("#txt_fecha_ini_b").val() +
					 "&fecha_fin=" + $("#txt_fecha_fin_b").val();
		
		llamarAjax("reporte_cotizaciones_ajax.php", params, "d_resul_cotizaciones", "");
	} else {
		$("#contenedor_error").css("display", "block");
	}
}

function validar_buscar_cotizaciones() {
    var result = true;
	
	$("#contenedor_error").css("display", "none");
	$("#txt_paciente_b").removeClass("borde_error");
	$("#cmb_proc_cotiz_b").removeClass("borde_error");
	$("#txt_observaciones_cotiz_b").removeClass("borde_error");
	$("#txt_fecha_ini_b").removeClass("borde_error");
	$("#txt_fecha_fin_b").removeClass("borde_error");
	
    if ($("#txt_paciente_b").val() == "" && $("#cmb_proc_cotiz_b").val() == "" && $("#txt_observaciones_cotiz_b").val() == "" && $("#txt_fecha_ini_b").val() == "" && $("#txt_fecha_fin_b").val() == "") {
        $("#txt_paciente_b").addClass("borde_error");
        $("#cmb_proc_cotiz_b").addClass("borde_error");
        $("#txt_observaciones_cotiz_b").addClass("borde_error");
        $("#txt_fecha_ini_b").addClass("borde_error");
        $("#txt_fecha_fin_b").addClass("borde_error");
		
		$("#contenedor_error").html("Debe seleccionar por lo menos un par&aacute;metro de b&uacute;squeda");
        result = false;
    } else if (($("#txt_fecha_ini_b").val() == "" && $("#txt_fecha_fin_b").val() != "") || ($("#txt_fecha_ini_b").val() != "" && $("#txt_fecha_fin_b").val() == "")) {
		if ($("#txt_fecha_ini_b").val() == "") {
			$("#txt_fecha_ini_b").addClass("borde_error");
		}
		if ($("#txt_fecha_fin_b").val() == "") {
			$("#txt_fecha_fin_b").addClass("borde_error");
		}
		
		$("#contenedor_error").html("Debe ingresar las dos fechas de b&uacute;squeda");
        result = false;
	}
    return result;
}

function ver_registros_hc(id_persona, nombre_persona, documento_persona, tipo_documento, telefonos, fecha_nacimiento, edad_paciente) {
	var params = 'opcion=2' +
				 '&id_persona=' + id_persona +
				 '&nombre_persona=' + nombre_persona +
				 '&documento_persona=' + documento_persona +
				 '&tipo_documento=' + tipo_documento +
				 '&telefonos=' + telefonos +
				 '&fecha_nacimiento=' + fecha_nacimiento +
				 '&edad_paciente=' + edad_paciente;
	
	llamarAjax("reporte_cotizaciones_ajax.php", params, "contenedor_paciente_hc", "");
}

function exportar_a_excel() {
	//Enviar los datos del formulario
	if (isObject(document.getElementById("frm_reporte_excel"))) {
		document.getElementById("frm_reporte_excel").submit();
	} else {
		alert("Debe realizar una b\xfasqueda.");
	}
}
