// JavaScript Document

function seleccionar_intravitreas_retina(ind_intravitreas) {
	if (ind_intravitreas == "1") {
		$("#txt_cant_intr_od").attr("disabled", false);
		$("#txt_cant_intr_oi").attr("disabled", false);
	} else {
		$("#txt_cant_intr_od").val("");
		$("#txt_cant_intr_od").attr("disabled", true);
		$("#txt_cant_intr_oi").val("");
		$("#txt_cant_intr_oi").attr("disabled", true);
	}
}

function seleccionar_cx_retina(ind_cx_retina) {
	console.log("#" + ind_cx_retina + "#");
	if (ind_cx_retina == "1") {
		$("#d_agregar_quitar_cx_ret").css("display", "block");
		$("#txt_texto_cx_0").attr("disabled", false);
		$("#txt_fecha_cx_0").attr("disabled", false);
	} else {
		$("#d_agregar_quitar_cx_ret").css("display", "none");
		$("#txt_texto_cx_0").attr("disabled", true);
		$("#txt_fecha_cx_0").attr("disabled", true);
		$("#hdd_cant_retina_cx").val("1");
		for (var i = 0; i < 20; i++) {
			if (i > 0) {
				$("#tr_retina_cx_" + i).css("display", "none");
			}
			$("#txt_texto_cx_" + i).val("");
			$("#txt_fecha_cx_" + i).val("");
		}
	}
}

function agregar_cx_retina() {
	var cant_retina_cx = parseInt($("#hdd_cant_retina_cx").val(), 10);
	
	if (cant_retina_cx < 20) {
		$("#tr_retina_cx_" + cant_retina_cx).css("display", "table-row");
		cant_retina_cx++;
		$("#hdd_cant_retina_cx").val(cant_retina_cx);
	} else {
		alert("Se ha alcanzado el n\xfamero m\xe1ximo de registros.");
	}
}

function restar_cx_retina() {
	var cant_retina_cx = parseInt($("#hdd_cant_retina_cx").val(), 10);
	
	if (cant_retina_cx > 0) {
		cant_retina_cx--;
		$("#txt_texto_cx_" + cant_retina_cx).val("");
		$("#txt_fecha_cx_" + cant_retina_cx).val("");
		if (cant_retina_cx > 0) {
			$("#tr_retina_cx_" + cant_retina_cx).css("display", "none");
			$("#hdd_cant_retina_cx").val(cant_retina_cx);
		} else {
			$("#hdd_cant_retina_cx").val(1);
		}
	}
}

function validar_consulta_oftalmologia_retina() {
	var resultado = 0;
	var cant_retina_cx = parseInt($("#hdd_cant_retina_cx").val(), 10);
	
	var cont_cx = 0;
	for (var i = 0; i < cant_retina_cx; i++) {
		$("#txt_texto_cx_" + i).removeClass("borde_error");
		if (trim($("#txt_texto_cx_" + i).val()) == "" && $("#txt_fecha_cx_" + i).val() != "") {
			$("#txt_texto_cx_" + i).addClass("borde_error");
			resultado = -1;
		}
		if (trim($("#txt_texto_cx_" + i).val()) != "") {
			cont_cx++;
		}
	}
	
	if ($("#cmb_cx_retina").val() == "1" && cont_cx == 0) {
		$("#txt_texto_cx_0").addClass("borde_error");
		resultado = -1;
	}
	
	return resultado;
}

function obtener_parametros_consulta_oftalmologia_retina() {
	var params = "&ind_laser_ret=" + $("#cmb_laser_ret").val() + 
				 "&ind_intravitreas_ret=" + $("#cmb_intravitreas").val() +
				 "&cant_intr_od_ret=" + $("#txt_cant_intr_od").val() +
				 "&cant_intr_oi_ret=" + $("#txt_cant_intr_oi").val() +
				 "&ind_cx_retina=" + $("#cmb_cx_retina").val();
	
	var cant_retina_cx = parseInt($("#hdd_cant_retina_cx").val(), 10);
	
	var cont_aux = 0;
	for (var i = 0; i < cant_retina_cx; i++) {
		if (trim($("#txt_texto_cx_" + i).val()) != "") {
			params += "&texto_cx_ret_" + cont_aux + "=" + str_encode($("#txt_texto_cx_" + i).val()) + 
					  "&fecha_cx_ret_" + cont_aux + "=" + $("#txt_fecha_cx_" + i).val();
			
			cont_aux++;
		}
	}
	params += "&cant_retina_cx=" + cont_aux;
	
	return params;
}
