function buscar_paciente(indice) {
	$("#contenedor_error").css("display", "none");
	$("#txt_paciente_hc_1").removeClass("borde_error");
	$("#txt_paciente_hc_2").removeClass("borde_error");
	
	if ($("#txt_paciente_hc_" + indice).val() != "") {
		var params = "opcion=1&indice=" + indice +
					 "&texto_busqueda=" + str_encode($("#txt_paciente_hc_" + indice).val());
		
		llamarAjax("unificar_historia_clinica_ajax.php", params, "d_contenedor_paciente_hc_" + indice, "");
	} else {
		$("#txt_paciente_hc_" + indice).addClass("borde_error");
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Los campos marcados en rojo son obligatorios");
		window.scroll(0, 0);
	}
}

function ver_registros_hc(id_paciente, indice) {
	var params = "opcion=2&indice=" + indice +
				 "&id_paciente=" + id_paciente;
	
	llamarAjax("unificar_historia_clinica_ajax.php", params, "d_contenedor_paciente_hc_" + indice, "");
}

function confirmar_unificar_hc(indice) {
	$("#fondo_negro").css("display", "block");
	$("#d_centro").slideDown(400).css("display", "block");
	
	$("#d_interno").html(
		'<table class="datagrid" border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
			'<tr class="headegrid">' +
				'<th align="center" class="msg_alerta" style="border: 1px solid #fff;">' +
					'<h4>&iquest;Est&aacute; seguro que desea unificar las historias cl&iacute;nicas?</h4>' +
				'</th>' +
			'</tr>' +
			'<tr>' +
				'<th align="center" style="width:5%;border: 1px solid #fff;">' +
					'<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" value="Aceptar" class="btnPrincipal" onclick="unificar_hc(' + indice + ');"/>' +
					'&nbsp;&nbsp;' +
					'<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" value="Cancelar" class="btnSecundario" onclick="cerrar_div_centro();"/>' +
				'</th>' +
			'</tr>' +
		'</table>');
	posicionarDivFlotante("d_centro");
}

function unificar_hc(indice) {
	cerrar_div_centro();
	$("#contenedor_error").css("display", "none");
	if ($("#hdd_id_paciente_1").length > 0 && $("#hdd_id_paciente_2").length > 0) {
		var id_paciente_1 = $("#hdd_id_paciente_1").val();
		var id_paciente_2 = $("#hdd_id_paciente_2").val();
		if (id_paciente_1 != id_paciente_2) {
			if (validar_unificar_hc(indice)) {
				var indice_2 = (indice == 1 ? 2 : 1);
				var params = "opcion=3&id_paciente=" + $("#hdd_id_paciente_" + indice).val() +
							 "&id_paciente_2=" + $("#hdd_id_paciente_" + indice_2).val() +
							 "&id_tipo_documento=" + $("#cmb_tipo_documento_" + indice).val() +
							 "&numero_documento=" + $("#txt_numero_documento_" + indice).val() +
							 "&nombre_1=" + str_encode($("#txt_nombre_1_" + indice).val()) +
							 "&nombre_2=" + str_encode($("#txt_nombre_2_" + indice).val()) +
							 "&apellido_1=" + str_encode($("#txt_apellido_1_" + indice).val()) +
							 "&apellido_2=" + str_encode($("#txt_apellido_2_" + indice).val()) +
							 "&sexo=" + $("#cmb_sexo_" + indice).val() +
							 "&fecha_nacimiento=" + $("#txt_fecha_nacimiento_" + indice).val() +
							 "&telefono_1=" + $("#txt_telefono_1_" + indice).val() +
							 "&telefono_2=" + $("#txt_telefono_2_" + indice).val();
				
				$("#d_unificar_hc_1").css("display", "none");
				$("#d_unificar_hc_2").css("display", "none");
				$("#d_esperar_unificar_hc_" + indice).css("display", "block");
				llamarAjax("unificar_historia_clinica_ajax.php", params, "d_guardar_unificacion", "finalizar_unificar_hc(" + indice + ");");
			} else {
				$("#contenedor_error").css("display", "block");
				$("#contenedor_error").html("Los campos marcados en rojo son obligatorios");
				window.scroll(0, 0);
			}
		} else {
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Se seleccion&oacute; el mismo paciente dos veces, no se pudo realizar el proceso de unificaci&oacute;n de historias cl&iacute;nicas");
			window.scroll(0, 0);
		}
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Debe seleccionar dos pacientes para realizar la unificaci&oacute;n de historias cl&iacute;nicas");
		window.scroll(0, 0);
	}
}

function validar_unificar_hc(indice) {
	var resultado = true;
	$("#contenedor_error").css("display", "none");
	$("#cmb_tipo_documento_" + indice).removeClass("borde_error");
	$("#txt_numero_documento_" + indice).removeClass("borde_error");
	$("#txt_nombre_1_" + indice).removeClass("borde_error");
	$("#txt_apellido_1_" + indice).removeClass("borde_error");
	$("#cmb_sexo_" + indice).removeClass("borde_error");
	$("#txt_fecha_nacimiento_" + indice).removeClass("borde_error");
	$("#txt_telefono_1_" + indice).removeClass("borde_error");
	
	if ($("#cmb_tipo_documento_" + indice).val() == "") {
		$("#cmb_tipo_documento_" + indice).addClass("borde_error");
		resultado = false;
	}
	if ($("#txt_numero_documento_" + indice).val() == "") {
		$("#txt_numero_documento_" + indice).addClass("borde_error");
		resultado = false;
	}
	if ($("#txt_nombre_1_" + indice).val() == "") {
		$("#txt_nombre_1_" + indice).addClass("borde_error");
		resultado = false;
	}
	if ($("#txt_apellido_1_" + indice).val() == "") {
		$("#txt_apellido_1_" + indice).addClass("borde_error");
		resultado = false;
	}
	if ($("#cmb_sexo_" + indice).val() == "") {
		$("#cmb_sexo_" + indice).addClass("borde_error");
		resultado = false;
	}
	if ($("#txt_fecha_nacimiento_" + indice).val() == "") {
		$("#txt_fecha_nacimiento_" + indice).addClass("borde_error");
		resultado = false;
	}
	if ($("#txt_telefono_1_" + indice).val() == "") {
		$("#txt_telefono_1_" + indice).addClass("borde_error");
		resultado = false;
	}
	
	return resultado;
}

function finalizar_unificar_hc(indice) {
	$("#d_unificar_hc_1").css("display", "block");
	$("#d_unificar_hc_2").css("display", "block");
	$("#d_esperar_unificar_hc_1").css("display", "none");
	$("#d_esperar_unificar_hc_2").css("display", "none");
	
	var resultado = parseInt($("#hdd_resultado_unificar_hc").val(), 10);
	if (resultado > 0) {
		$("#contenedor_exito").css("display", "block");
		$("#contenedor_exito").html("Historias cl&iacute;nicas unificadas con &eacute;xito");
		$("#d_contenedor_paciente_hc_1").html("");
		$("#d_contenedor_paciente_hc_2").html("");
		setTimeout(function () { $("#contenedor_exito").css("display", "none"); }, 2000);
		window.scroll(0, 0);
	} else if (resultado == -1) {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error interno al unificar las historias cl&iacute;nicas");
		window.scroll(0, 0);
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error al unificar las historias cl&iacute;nicas");
		window.scroll(0, 0);
	}
}
