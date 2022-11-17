// JavaScript Document

function cambiar_pais_enc_hc(id_pais, sufijo) {
	if (id_pais == "1") {
		$("#d_cod_dep_" + sufijo + "_enc_hc").css("display", "block");
		$("#d_nom_dep_" + sufijo + "_enc_hc").css("display", "none");
		$("#d_cod_mun_" + sufijo + "_enc_hc").css("display", "block");
		$("#d_nom_mun_" + sufijo + "_enc_hc").css("display", "none");
	} else {
		$("#d_cod_dep_" + sufijo + "_enc_hc").css("display", "none");
		$("#d_nom_dep_" + sufijo + "_enc_hc").css("display", "block");
		$("#d_cod_mun_" + sufijo + "_enc_hc").css("display", "none");
		$("#d_nom_mun_" + sufijo + "_enc_hc").css("display", "block");
	}
}

function cambiar_departamento_enc_hc(cod_dep, sufijo) {
	var params = "opcion=1&cod_dep=" + cod_dep +
				 "&sufijo=" + sufijo;
	
	llamarAjax("FuncionesHistoriaClinica_ajax.php", params, "d_mun_" + sufijo + "_enc_hc", "");
}

function iniciar_edicion_datos_enc_hc() {
	$("#tbl_encabezado_hc_1").css("display", "none");
	$("#tbl_encabezado_hc_2").css("display", "table");
	$("#tbl_encabezado_hc_3").css("display", "none");
	$("#tbl_encabezado_hc_4").css("display", "table");
}

function cancelar_edicion_datos_enc_hc() {
	$("#tbl_encabezado_hc_1").css("display", "table");
	$("#tbl_encabezado_hc_2").css("display", "none");
	$("#tbl_encabezado_hc_3").css("display", "table");
	$("#tbl_encabezado_hc_4").css("display", "none");
}

function validar_guardar_datos_enc_hc() {
	var resultado = true;
	
	$("#d_contenedor_error_enc_hc").css("display", "none");
	
	$("#txt_nombre_1_enc_hc").removeClass("borde_error");
	$("#txt_apellido_1_enc_hc").removeClass("borde_error");
	$("#cmb_estado_civil_enc_hc").removeClass("borde_error");
	$("#txt_profesion_enc_hc").removeClass("borde_error");
	$("#cmb_pais_nac_enc_hc").removeClass("borde_error");
	$("#cmb_dep_nac_enc_hc").removeClass("borde_error");
	$("#cmb_mun_nac_enc_hc").removeClass("borde_error");
	$("#txt_nom_dep_nac_enc_hc").removeClass("borde_error");
	$("#txt_nom_mun_nac_enc_hc").removeClass("borde_error");
	$("#txt_fecha_nacimiento_enc_hc").removeClass("borde_error");
	$("#cmb_sexo_enc_hc").removeClass("borde_error");
	$("#txt_direccion_enc_hc").removeClass("borde_error");
	$("#cmb_pais_res_enc_hc").removeClass("borde_error");
	$("#cmb_dep_res_enc_hc").removeClass("borde_error");
	$("#cmb_mun_res_enc_hc").removeClass("borde_error");
	$("#txt_nom_dep_res_enc_hc").removeClass("borde_error");
	$("#txt_nom_mun_res_enc_hc").removeClass("borde_error");
	$("#txt_telefono_1_enc_hc").removeClass("borde_error");
	$("#txt_email_enc_hc").removeClass("borde_error");
	$("#txt_motivo_consulta_enc_hc").removeClass("borde_error");
	
	if ($('#txt_nombre_1_enc_hc').val() == "") {
		$("#txt_nombre_1_enc_hc").addClass("borde_error");
		resultado = false;
	}
	if ($('#txt_apellido_1_enc_hc').val() == "") {
		$("#txt_apellido_1_enc_hc").addClass("borde_error");
		resultado = false;
	}
	if ($('#cmb_estado_civil_enc_hc').val() == "") {
		$("#cmb_estado_civil_enc_hc").addClass("borde_error");
		resultado = false;
	}
	if ($('#txt_profesion_enc_hc').val() == "") {
		$("#txt_profesion_enc_hc").addClass("borde_error");
		resultado = false;
	}
	if ($('#cmb_pais_nac_enc_hc').val() == "") {
		$("#cmb_pais_nac_enc_hc").addClass("borde_error");
		resultado = false;
	}
	if ($('#cmb_pais_nac_enc_hc').val() == "1") {
		if ($('#cmb_dep_nac_enc_hc').val() == "") {
			$("#cmb_dep_nac_enc_hc").addClass("borde_error");
			resultado = false;
		}
		if ($('#cmb_mun_nac_enc_hc').val() == "") {
			$("#cmb_mun_nac_enc_hc").addClass("borde_error");
			resultado = false;
		}
	} else {
		if ($('#txt_nom_dep_nac_enc_hc').val() == "") {
			$("#txt_nom_dep_nac_enc_hc").addClass("borde_error");
			resultado = false;
		}
		if ($('#txt_nom_mun_nac_enc_hc').val() == "") {
			$("#txt_nom_mun_nac_enc_hc").addClass("borde_error");
			resultado = false;
		}
	}
	if ($('#txt_fecha_nacimiento_enc_hc').val() == "") {
		$("#txt_fecha_nacimiento_enc_hc").addClass("borde_error");
		resultado = false;
	}
	if ($('#cmb_sexo_enc_hc').val() == "") {
		$("#cmb_sexo_enc_hc").addClass("borde_error");
		resultado = false;
	}
	if ($('#txt_direccion_enc_hc').val() == "") {
		$("#txt_direccion_enc_hc").addClass("borde_error");
		resultado = false;
	}
	if ($('#cmb_pais_res_enc_hc').val() == "") {
		$("#cmb_pais_res_enc_hc").addClass("borde_error");
		resultado = false;
	}
	if ($('#cmb_pais_res_enc_hc').val() == "1") {
		if ($('#cmb_dep_res_enc_hc').val() == "") {
			$("#cmb_dep_res_enc_hc").addClass("borde_error");
			resultado = false;
		}
		if ($('#cmb_mun_res_enc_hc').val() == "") {
			$("#cmb_mun_res_enc_hc").addClass("borde_error");
			resultado = false;
		}
	} else {
		if ($('#txt_nom_dep_res_enc_hc').val() == "") {
			$("#txt_nom_dep_res_enc_hc").addClass("borde_error");
			resultado = false;
		}
		if ($('#txt_nom_mun_res_enc_hc').val() == "") {
			$("#txt_nom_mun_res_enc_hc").addClass("borde_error");
			resultado = false;
		}
	}
	if ($('#txt_telefono_1_enc_hc').val() == "") {
		$("#txt_telefono_1_enc_hc").addClass("borde_error");
		resultado = false;
	}
	if ($('#txt_email_enc_hc').val() == "") {
		$("#txt_email_enc_hc").addClass("borde_error");
		resultado = false;
	}
	if ($('#txt_motivo_consulta_enc_hc').val() == "") {
		$("#txt_motivo_consulta_enc_hc").addClass("borde_error");
		resultado = false;
	}
	
	return resultado;
}

function guardar_datos_enc_hc() {
	if (validar_guardar_datos_enc_hc()) {
		var params = "opcion=2&id_admision=" + $("#hdd_id_admision_enc_hc").val() +
					 "&nombre_1=" + str_encode($("#txt_nombre_1_enc_hc").val()) +
					 "&nombre_2=" + str_encode($("#txt_nombre_2_enc_hc").val()) +
					 "&apellido_1=" + str_encode($("#txt_apellido_1_enc_hc").val()) +
					 "&apellido_2=" + str_encode($("#txt_apellido_2_enc_hc").val()) +
					 "&nombre_acompa=" + str_encode($("#txt_nombre_acompa_enc_hc").val()) +
					 "&id_estado_civil=" + $("#cmb_estado_civil_enc_hc").val() +
					 "&profesion=" + str_encode($("#txt_profesion_enc_hc").val()) +
					 "&id_pais_nac=" + $("#cmb_pais_nac_enc_hc").val() +
					 "&cod_dep_nac=" + $("#cmb_dep_nac_enc_hc").val() +
					 "&cod_mun_nac=" + $("#cmb_mun_nac_enc_hc").val() +
					 "&nom_dep_nac=" + str_encode($("#txt_nom_dep_nac_enc_hc").val()) +
					 "&nom_mun_nac=" + str_encode($("#txt_nom_mun_nac_enc_hc").val()) +
					 "&fecha_nacimiento=" + $("#txt_fecha_nacimiento_enc_hc").val() +
					 "&sexo=" + $("#cmb_sexo_enc_hc").val() +
					 "&numero_hijos=" + $("#cmb_numero_hijos_enc_hc").val() +
					 "&numero_hijas=" + $("#cmb_numero_hijas_enc_hc").val() +
					 "&numero_hermanos=" + $("#cmb_numero_hermanos_enc_hc").val() +
					 "&numero_hermanas=" + $("#cmb_numero_hermanas_enc_hc").val() +
					 "&direccion=" + str_encode($("#txt_direccion_enc_hc").val()) +
					 "&id_pais_res=" + $("#cmb_pais_res_enc_hc").val() +
					 "&cod_dep_res=" + $("#cmb_dep_res_enc_hc").val() +
					 "&cod_mun_res=" + $("#cmb_mun_res_enc_hc").val() +
					 "&nom_dep_res=" + str_encode($("#txt_nom_dep_res_enc_hc").val()) +
					 "&nom_mun_res=" + str_encode($("#txt_nom_mun_res_enc_hc").val()) +
					 "&telefono_1=" + $("#txt_telefono_1_enc_hc").val() +
					 "&telefono_2=" + $("#txt_telefono_2_enc_hc").val() +
					 "&email=" + $("#txt_email_enc_hc").val() +
					 "&observ_paciente=" + str_encode($("#txt_observ_paciente_enc_hc").val()) +
					 "&presion_arterial=" + $("#txt_presion_arterial_enc_hc").val() +
					 "&pulso=" + $("#txt_pulso_enc_hc").val() +
					 "&observaciones_admision=" + str_encode($("#txt_observaciones_admision_enc_hc").val()) +
					 "&motivo_consulta=" + str_encode($("#txt_motivo_consulta_enc_hc").val()) +
					 "&cadena_colores_adm=" + obtener_cadena_colores("adm") +
					 "&cadena_colores_pac=" + obtener_cadena_colores("pac");
		
		llamarAjax("FuncionesHistoriaClinica_ajax.php", params, "d_guardar_enc_hc", "finalizar_guardar_datos_enc_hc();");
	} else {
		$("#d_contenedor_error_enc_hc").html("Los campos marcados en rojo son obligatorios.");
		$("#d_contenedor_error_enc_hc").css("display", "block");
	}
}

function finalizar_guardar_datos_enc_hc() {
	try {
		var resultado = parseInt($("#hdd_resultado_enc_hc").val(), 10);
		
		if (resultado > 0) {
			$("#d_contenedor_exito_enc_hc").html("Datos actualizados con &eacute;xito.");
			$("#d_contenedor_exito_enc_hc").css("display", "block");
			
			var params = "opcion=3&id_paciente=" + $("#hdd_id_paciente_enc_hc").val() +
						 "&id_admision=" + $("#hdd_id_admision_enc_hc").val() +
						 "&id_hc=" + $("#hdd_id_hc_enc_hc").val() +
						 "&ind_editar=" + $("#hdd_ind_editar_enc_hc").val();
			
			llamarAjax("FuncionesHistoriaClinica_ajax.php", params, "encabezado_hc_principal", "");
			
			setTimeout(function() { $("#d_contenedor_exito_enc_hc").css("display", "none"); }, 2000);
		} else if (resultado == -1) {
			$("#d_contenedor_error_enc_hc").html("Error interno al tratar de guardar los datos del paciente.");
			$("#d_contenedor_error_enc_hc").css("display", "block");
		} else {
			$("#d_contenedor_error_enc_hc").html("Error al tratar de guardar los datos del paciente.");
			$("#d_contenedor_error_enc_hc").css("display", "block");
		}
	} catch (e) {
		$("#d_contenedor_error_enc_hc").html("Error de JavaScript al tratar de guardar los datos del paciente.");
		$("#d_contenedor_error_enc_hc").css("display", "block");
	}
}
