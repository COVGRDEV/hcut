function buscar_paciente() {
	$("#contenedor_error").css("display", "none");
	$("#txt_paciente_hc").removeClass("borde_error");
	
	if ($("#txt_paciente_hc").val() != "") {
		var params = "opcion=1&texto_busqueda=" + str_encode($("#txt_paciente_hc").val());
		
		llamarAjax("pacientes_ajax.php", params, "d_contenedor_paciente_hc", "");
	} else {
		$("#txt_paciente_hc").addClass("borde_error");
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Los campos marcados en rojo son obligatorios");
		window.scroll(0, 0);
	}
}

function ver_datos_paciente(id_paciente) {
	var params = "opcion=2&id_paciente=" + id_paciente;
	
	llamarAjax("pacientes_ajax.php", params, "d_contenedor_paciente_hc", "");
}

function confirmar_guardar_paciente() {
	if (validar_paciente()) {
		$("#fondo_negro").css("display", "block");
		$("#d_centro").slideDown(400).css("display", "block");
		
		$("#d_interno").html(
			'<table class="datagrid" border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
				'<tr class="headegrid">' +
					'<th align="center" class="msg_alerta" style="border: 1px solid #fff;">' +
						'<h4>&iquest;Est&aacute; seguro que desea modificar los datos del paciente?</h4>' +
					'</th>' +
				'</tr>' +
				'<tr>' +
					'<th align="center" style="width:5%;border: 1px solid #fff;">' +
						'<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" value="Aceptar" class="btnPrincipal" onclick="guardar_paciente();"/>' +
						'&nbsp;&nbsp;' +
						'<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" value="Cancelar" class="btnSecundario" onclick="cerrar_div_centro();"/>' +
					'</th>' +
				'</tr>' +
			'</table>');
		posicionarDivFlotante("d_centro");
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Los campos marcados en rojo son obligatorios");
		window.scroll(0, 0);
	}
}

function guardar_paciente() {
	cerrar_div_centro();
	$("#contenedor_error").css("display", "none");
	
	var id_paciente = $("#hdd_id_paciente").val();
	var params = "opcion=3&id_paciente=" + $("#hdd_id_paciente").val() +
				 "&id_tipo_documento=" + $("#cmb_tipo_documento").val() +
				 "&numero_documento=" + $("#txt_numero_documento").val() +
				 "&nombre_1=" + str_encode($("#txt_nombre_1").val()) +
				 "&nombre_2=" + str_encode($("#txt_nombre_2").val()) +
				 "&apellido_1=" + str_encode($("#txt_apellido_1").val()) +
				 "&apellido_2=" + str_encode($("#txt_apellido_2").val()) +
				 "&sexo=" + $("#cmb_sexo").val() +
				 "&fecha_nacimiento=" + $("#txt_fecha_nacimiento").val() +
				 "&tipo_sangre=" + $("#cmb_tipo_sangre").val() +
				 "&factor_rh=" + $("#cmb_factor_rh").val() +
				 "&id_pais_nac=" + $("#cmb_pais_nac").val() +
				 "&cod_dep_nac=" + $("#cmb_cod_dep_nac").val() +
				 "&cod_mun_nac=" + $("#cmb_cod_mun_nac").val() +
				 "&nom_dep_nac=" + str_encode($("#txt_nom_dep_nac").val()) +
				 "&nom_mun_nac=" + str_encode($("#txt_nom_mun_nac").val()) +
				 "&id_pais=" + $("#cmb_pais_res").val() +
				 "&cod_dep=" + $("#cmb_cod_dep_res").val() +
				 "&cod_mun=" + $("#cmb_cod_mun_res").val() +
				 "&nom_dep=" + str_encode($("#txt_nom_dep_res").val()) +
				 "&nom_mun=" + str_encode($("#txt_nom_mun_res").val()) +
				 "&id_zona=" + $("#cmb_zona").val() +
				 "&direccion=" + str_encode($("#txt_direccion").val()) +
				 "&email=" + str_encode($("#txt_email").val()) +
				 "&telefono_1=" + str_encode($("#txt_telefono_1").val()) +
				 "&telefono_2=" + str_encode($("#txt_telefono_2").val()) +
				 "&profesion=" + str_encode($("#txt_profesion").val()) +
				 "&id_estado_civil=" + $("#cmb_estado_civil").val() +
				 "&ind_desplazado=" + $("#cmb_desplazado").val() +
				 "&id_etnia=" + $("#cmb_etnia").val();
	
	$("#d_btn_guardar_paciente").css("display", "none");
	$("#d_esperar_guardar_paciente").css("display", "block");
	llamarAjax("pacientes_ajax.php", params, "d_guardar_paciente", "finalizar_guardar_paciente();");
}

function validar_paciente() {
	var resultado = true;
	$("#contenedor_error").css("display", "none");
	$("#cmb_tipo_documento").removeClass("borde_error");
	$("#txt_numero_documento").removeClass("borde_error");
	$("#txt_nombre_1").removeClass("borde_error");
	$("#txt_apellido_1").removeClass("borde_error");
	$("#cmb_sexo").removeClass("borde_error");
	$("#txt_fecha_nacimiento").removeClass("borde_error");
	$("#cmb_tipo_sangre").removeClass("borde_error");
	$("#cmb_factor_rh").removeClass("borde_error");
	$("#cmb_pais_nac").removeClass("borde_error");
	$("#cmb_cod_dep_nac").removeClass("borde_error");
	$("#cmb_cod_mun_nac").removeClass("borde_error");
	$("#txt_nom_dep_nac").removeClass("borde_error");
	$("#txt_nom_mun_nac").removeClass("borde_error");
	$("#cmb_pais_res").removeClass("borde_error");
	$("#cmb_cod_dep_res").removeClass("borde_error");
	$("#cmb_cod_mun_res").removeClass("borde_error");
	$("#txt_nom_dep_res").removeClass("borde_error");
	$("#txt_nom_mun_res").removeClass("borde_error");
	$("#cmb_zona").removeClass("borde_error");
	$("#txt_direccion").removeClass("borde_error");
	$("#txt_telefono_1").removeClass("borde_error");
	$("#txt_profesion").removeClass("borde_error");
	$("#cmb_estado_civil").removeClass("borde_error");
	
	if ($("#cmb_tipo_documento").val() == "") {
		$("#cmb_tipo_documento").addClass("borde_error");
		resultado = false;
	}
	if ($("#txt_numero_documento").val() == "") {
		$("#txt_numero_documento").addClass("borde_error");
		resultado = false;
	}
	if ($("#txt_nombre_1").val() == "") {
		$("#txt_nombre_1").addClass("borde_error");
		resultado = false;
	}
	if ($("#txt_apellido_1").val() == "") {
		$("#txt_apellido_1").addClass("borde_error");
		resultado = false;
	}
	if ($("#cmb_sexo").val() == "") {
		$("#cmb_sexo").addClass("borde_error");
		resultado = false;
	}
	if ($("#txt_fecha_nacimiento").val() == "") {
		$("#txt_fecha_nacimiento").addClass("borde_error");
		resultado = false;
	}
	if ($("#cmb_tipo_sangre").val() == "") {
		$("#cmb_tipo_sangre").addClass("borde_error");
		resultado = false;
	}
	if ($("#cmb_factor_rh").val() == "") {
		$("#cmb_factor_rh").addClass("borde_error");
		resultado = false;
	}
	if ($("#cmb_pais_nac").val() == "") {
		$("#cmb_pais_nac").addClass("borde_error");
		resultado = false;
	} else if ($("#cmb_pais_nac").val() == "1") {
		if ($("#cmb_cod_dep_nac").val() == "") {
			$("#cmb_cod_dep_nac").addClass("borde_error");
			resultado = false;
		}
		if ($("#cmb_cod_mun_nac").val() == "") {
			$("#cmb_cod_mun_nac").addClass("borde_error");
			resultado = false;
		}
	} else {
		if ($("#txt_nom_dep_nac").val() == "") {
			$("#txt_nom_dep_nac").addClass("borde_error");
			resultado = false;
		}
		if ($("#txt_nom_mun_nac").val() == "") {
			$("#txt_nom_mun_nac").addClass("borde_error");
			resultado = false;
		}
	}
	if ($("#cmb_pais_res").val() == "") {
		$("#cmb_pais_res").addClass("borde_error");
		resultado = false;
	} else if ($("#cmb_pais_res").val() == "1") {
		if ($("#cmb_cod_dep_res").val() == "") {
			$("#cmb_cod_dep_res").addClass("borde_error");
			resultado = false;
		}
		if ($("#cmb_cod_mun_res").val() == "") {
			$("#cmb_cod_mun_res").addClass("borde_error");
			resultado = false;
		}
	} else {
		if ($("#txt_nom_dep_res").val() == "") {
			$("#txt_nom_dep_res").addClass("borde_error");
			resultado = false;
		}
		if ($("#txt_nom_mun_res").val() == "") {
			$("#txt_nom_mun_res").addClass("borde_error");
			resultado = false;
		}
	}
	if ($("#cmb_zona").val() == "") {
		$("#cmb_zona").addClass("borde_error");
		resultado = false;
	}
	if ($("#txt_direccion").val() == "") {
		$("#txt_direccion").addClass("borde_error");
		resultado = false;
	}
	if ($("#txt_telefono_1").val() == "") {
		$("#txt_telefono_1").addClass("borde_error");
		resultado = false;
	}
	if ($("#txt_profesion").val() == "") {
		$("#txt_profesion").addClass("borde_error");
		resultado = false;
	}
	if ($("#cmb_estado_civil").val() == "") {
		$("#cmb_estado_civil").addClass("borde_error");
		resultado = false;
	}
	
	return resultado;
}

function finalizar_guardar_paciente() {
	$("#d_btn_guardar_paciente").css("display", "block");
	$("#d_esperar_unificar_hc").css("display", "none");
	
	var resultado = parseInt($("#hdd_resultado_guardar_paciente").val(), 10);
	if (resultado > 0) {
		$("#contenedor_exito").css("display", "block");
		$("#contenedor_exito").html("Paciente guardado con &eacute;xito");
		$("#d_contenedor_paciente_hc").html("");
		setTimeout(function () { $("#contenedor_exito").css("display", "none"); }, 2000);
		window.scroll(0, 0);
	} else if (resultado == -1) {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error interno al guardar los datos del paciente");
		window.scroll(0, 0);
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error al guardar los datos del paciente");
		window.scroll(0, 0);
	}
}

function seleccionar_pais(id_pais, sufijo) {
	if (id_pais == "1") {
		$("#td_dep_col_" + sufijo).css("display", "table-cell");
		$("#td_mun_col_" + sufijo).css("display", "table-cell");
		$("#td_dep_col_val_" + sufijo).css("display", "table-cell");
		$("#td_mun_col_val_" + sufijo).css("display", "table-cell");
		$("#td_dep_otro_" + sufijo).css("display", "none");
		$("#td_mun_otro_" + sufijo).css("display", "none");
		$("#td_dep_otro_val_" + sufijo).css("display", "none");
		$("#td_mun_otro_val_" + sufijo).css("display", "none");
	} else {
		$("#td_dep_col_" + sufijo).css("display", "none");
		$("#td_mun_col_" + sufijo).css("display", "none");
		$("#td_dep_col_val_" + sufijo).css("display", "none");
		$("#td_mun_col_val_" + sufijo).css("display", "none");
		$("#td_dep_otro_" + sufijo).css("display", "table-cell");
		$("#td_mun_otro_" + sufijo).css("display", "table-cell");
		$("#td_dep_otro_val_" + sufijo).css("display", "table-cell");
		$("#td_mun_otro_val_" + sufijo).css("display", "table-cell");
	}
}

function seleccionar_departamento(cod_dep, sufijo) {
	var params = "opcion=4&cod_dep=" + cod_dep +
				 "&sufijo=" + sufijo;
	
	llamarAjax("pacientes_ajax.php", params, "d_municipio_" + sufijo, "");
}
