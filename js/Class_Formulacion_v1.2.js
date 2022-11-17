function agregar_medicamento_fm() {
	var cant_formulaciones = parseInt($("#hdd_cant_formulaciones").val(), 10);
	
	if (cant_formulaciones <= 20) {
		$("#tr_formulacion_fm_" + cant_formulaciones).css("display", "table-row");
		$("#tr_formulacion_fm2_" + cant_formulaciones).css("display", "table-row");
		$("#tr_formulacion_fm3_" + cant_formulaciones).css("display", "table-row");
		
		$("#hdd_cant_formulaciones").val(cant_formulaciones + 1);
	}
}

function restar_medicamento_fm() {
	var cant_formulaciones = parseInt($("#hdd_cant_formulaciones").val(), 10) - 1;
	
	$("#hdd_cod_medicamento_fm_" + cant_formulaciones).val("");
	$("#hdd_id_tipo_medicamento_fm_" + cant_formulaciones).val("");
	$("#txt_nombre_medicamento_fm_" + cant_formulaciones).val("");
	$("#txt_presentacion_fm_" + cant_formulaciones).val("");
	$("#txt_dosificacion_fm_" + cant_formulaciones).val("");
	$("#txt_unidades_fm_" + cant_formulaciones).val("");
	$("#txt_duracion_fm_" + cant_formulaciones).val("");
	
	if (cant_formulaciones > 0) {
		$("#tr_formulacion_fm_" + cant_formulaciones).css("display", "none");
		$("#tr_formulacion_fm2_" + cant_formulaciones).css("display", "none");
		$("#tr_formulacion_fm3_" + cant_formulaciones).css("display", "none");
		
		$("#hdd_cant_formulaciones").val(cant_formulaciones);
	}
}

function procesar_seleccion_fm(indice) {
	var texto_base = $("#txt_nombre_medicamento_fm_" + indice).val();
	var pos_ini = texto_base.indexOf("[#");
	var pos_fin = texto_base.indexOf("#]");
	
	if (pos_ini >= 0 && pos_fin >= 0) {
		var nombre_medicamento_aux = trim(texto_base.substring(0, pos_ini - 1));
		var cadena_aux = texto_base.substring(pos_ini + 2, pos_fin);
		var arr_valores_aux = cadena_aux.split("#");
		
		if (arr_valores_aux.length == 3) {
			$("#txt_nombre_medicamento_fm_" + indice).val(nombre_medicamento_aux);
			$("#txt_presentacion_fm_" + indice).val(trim(arr_valores_aux[0]));
			$("#hdd_cod_medicamento_fm_" + indice).val(arr_valores_aux[1]);
			$("#hdd_cod_tipo_medicamento_fm_" + indice).val(arr_valores_aux[2]);
		}
	}
}

function validar_formulacion_fm() {
	var resultado = true;
	
	var cant_formulaciones = parseInt($("#hdd_cant_formulaciones").val(), 10);
	for (i = 0; i < cant_formulaciones; i++) {
		$("#txt_nombre_medicamento_fm_" + i).removeClass("bordeAdmision");
		$("#txt_cantidad_fm_" + i).removeClass("bordeAdmision");
	 	if (trim($("#txt_nombre_medicamento_fm_" + i).val()) == "" && ($("#txt_presentacion_fm_" + i).val() != "" || $("#txt_cantidad_fm_" + i).val() != "" ||
				$("#txt_dosificacion_fm_" + i).val() != "" || $("#txt_unidades_fm_" + i).val() != "" || $("#txt_duracion_fm_" + i).val() != "")) {
			$("#txt_nombre_medicamento_fm_" + i).addClass("bordeAdmision");
			resultado = false;
		}
	 	if (trim($("#txt_nombre_medicamento_fm_" + i).val()) != "" && $("#txt_cantidad_fm_" + i).val() == "") {
			$("#txt_cantidad_fm_" + i).addClass("bordeAdmision");
			resultado = false;
		}
	}
	
	return resultado;
}

function obtener_parametros_formulacion_fm() {
	var params = "";
	var cont_aux = 0;
	var cant_formulaciones = parseInt($("#hdd_cant_formulaciones").val(), 10);
	for (i = 0; i < cant_formulaciones; i++) {
		if (trim($("#txt_nombre_medicamento_fm_" + i).val()) != "") {
			params += "&nombre_medicamento_fm_" + cont_aux + "=" + str_encode($("#txt_nombre_medicamento_fm_" + i).val()) +
					  "&cod_medicamento_fm_" + cont_aux + "=" + $("#hdd_cod_medicamento_fm_" + i).val() +
					  "&cod_tipo_medicamento_fm_" + cont_aux + "=" + $("#hdd_cod_tipo_medicamento_fm_" + i).val() +
					  "&presentacion_fm_" + cont_aux + "=" + str_encode($("#txt_presentacion_fm_" + i).val()) +
					  "&cantidad_fm_" + cont_aux + "=" + $("#txt_cantidad_fm_" + i).val() +
					  "&dosificacion_fm_" + cont_aux + "=" + str_encode($("#txt_dosificacion_fm_" + i).val()) +
					  "&unidades_fm_" + cont_aux + "=" + str_encode($("#txt_unidades_fm_" + i).val()) +
					  "&duracion_fm_" + cont_aux + "=" + str_encode($("#txt_duracion_fm_" + i).val());
			
			cont_aux++;
		}
	}
	params += "&cant_formulaciones_fm=" + cont_aux;
	
	return params;
}

function copiar_formulacion_anterior_fm(id_hc) {
	var params = "opcion=1&id_hc=" + id_hc;
	
	llamarAjax("../funciones/Class_Formulacion_ajax.php", params, "d_formulacion_anterior_fm", "continuar_copiar_formulacion_anterior_fm();");
}

function continuar_copiar_formulacion_anterior_fm() {
	var cant_ant = parseInt($("#hdd_cant_ant_fm").val(), 10);
	$("#hdd_cant_formulaciones").val(cant_ant);
	
	for (var i = 0; i < 20; i++) {
		if (i < cant_ant) {
			$("#tr_formulacion_fm_" + i).css("display", "table-row");
			$("#tr_formulacion_fm2_" + i).css("display", "table-row");
			$("#tr_formulacion_fm3_" + i).css("display", "table-row");
			
			$("#hdd_cod_medicamento_fm_" + i).val($("#hdd_cod_medicamento_ant_fm_" + i).val());
			$("#hdd_cod_tipo_medicamento_fm_" + i).val($("#hdd_cod_tipo_medicamento_ant_fm_" + i).val());
			$("#txt_nombre_medicamento_fm_" + i).val($("#hdd_nombre_medicamento_ant_fm_" + i).val());
			$("#txt_presentacion_fm_" + i).val($("#hdd_presentacion_ant_fm_" + i).val());
			$("#txt_cantidad_fm_" + i).val($("#hdd_cantidad_ant_fm_" + i).val());
			$("#txt_dosificacion_fm_" + i).val($("#hdd_dosificacion_ant_fm_" + i).val());
			$("#txt_unidades_fm_" + i).val($("#hdd_unidades_ant_fm_" + i).val());
			$("#txt_duracion_fm_" + i).val($("#hdd_duracion_ant_fm_" + i).val());
		} else {
			if (i > 0) {
				$("#tr_formulacion_fm_" + i).css("display", "none");
				$("#tr_formulacion_fm2_" + i).css("display", "none");
				$("#tr_formulacion_fm3_" + i).css("display", "none");
			}
			
			$("#hdd_cod_medicamento_fm_" + i).val("");
			$("#hdd_id_tipo_medicamento_fm_" + i).val("");
			$("#txt_nombre_medicamento_fm_" + i).val("");
			$("#txt_presentacion_fm_" + i).val("");
			$("#txt_dosificacion_fm_" + i).val("");
			$("#txt_unidades_fm_" + i).val("");
			$("#txt_duracion_fm_" + i).val("");
		}
	}
}
