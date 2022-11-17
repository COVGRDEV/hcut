function mostrar_procedimientos_solic() {
	var cant_procedimientos = $("#hdd_cant_procedimientos").val();
	
	for (i = 0; i < cant_procedimientos; i++) {
		$("#tbl_proc_solic_" + i).css("display", "block");
	}
}

function restar_procedimientos_solic() {
	var cant_procedimientos = parseInt($("#hdd_cant_procedimientos").val(), 10);
	
	if (cant_procedimientos > 0) {
		cant_procedimientos--;
		$("#txt_cod_procedimiento_" + cant_procedimientos).val("");
		$("#hdd_cod_procedimiento_" + cant_procedimientos).val("");
		$("#d_nombre_procedimiento_" + cant_procedimientos).html("");
		$("#cmb_ojo_" + cant_procedimientos).val("");
		if (cant_procedimientos > 0) {
			$("#hdd_cant_procedimientos").val(cant_procedimientos);
			$("#tbl_proc_solic_" + cant_procedimientos).css("display", "none");
		}
	}
}

function agregar_procedimientos_solic() {
	var cant_procedimientos = parseInt($("#hdd_cant_procedimientos").val(), 10);
	if (cant_procedimientos < 20) {
		$("#tbl_proc_solic_" + cant_procedimientos).css("display", "block");
		$("#hdd_cant_procedimientos").val(cant_procedimientos + 1);
	}
}

function abrir_buscar_cups_solic(indice) {
	mostrar_formulario_flotante(1);
	reducir_formulario_flotante(800, "auto");
	posicionarDivFlotante("d_centro");
	
	$("#d_interno").html(
		'<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
			'<tr>' +
				'<td align="center" colspan="2"><h4>Buscar Procedimientos/Ex&aacute;menes</h4></td>' +
			'</tr>' +
			'<tr>' +
				'<td align="center" style="width:90%">' +
					'<input type="text" name="txt_buscar_cups" id="txt_buscar_cups" placeholder="Buscar procedimiento o examen" onkeypress="return validar_enter_cups_solic();">' +
				'</td>' +
				'<td align="center" style="width:10%">' +
					'<input type="hidden" id="hdd_cups_solic_num" value="' + indice + '" />' +
					'<input type="button" id="btn_buscar_cups_solic" nombre="btn_buscar_cups_solic" value="Buscar" class="btnPrincipal peq" onclick="buscar_cups_solic(' + indice + ');"/> ' +
				'</td>' +
			'</tr>' +
		'</table>' +
		'<div id="d_buscar_cups_solic"></div>');
	$("#txt_buscar_cups").focus();
}

function buscar_cups_solic(indice) {
	var params = "opcion=1&texto_busc=" + str_encode($("#txt_buscar_cups").val()) + "&indice=" + indice;
	
	llamarAjax("../funciones/Class_Solic_Procs_ajax.php", params, "d_buscar_cups_solic", "");
}

function seleccionar_cups_solic(cod_procedimiento, nombre_procedimiento, indice) {
	$("#txt_cod_procedimiento_" + indice).val(cod_procedimiento);
	$("#hdd_cod_procedimiento_" + indice).val(cod_procedimiento);
	$("#d_nombre_procedimiento_" + indice).html(nombre_procedimiento);
	$("#d_nombre_procedimiento_" + indice).css({"color":"#5B5B5B"});
	
	mostrar_formulario_flotante(0);
}

function buscar_cod_cups_solic(indice) {
	var cod_procedimiento = $("#txt_cod_procedimiento_" + indice).val();
	if (cod_procedimiento != "") {
		var params = "opcion=2&cod_procedimiento=" + cod_procedimiento + "&indice=" + indice;
		
		llamarAjax("../funciones/Class_Solic_Procs_ajax.php", params, "d_nombre_procedimiento_" + indice, "validar_cod_cups_solic(" + indice + ");");
	} else {
		$("#hdd_cod_procedimiento_" + indice).val("");
		$("#d_nombre_procedimiento_" + indice).html("");
		$("#cmb_ojo_" + indice).val("");
	}
}

function validar_cod_cups_solic(indice) {
	var cod_procedimiento = $("#hdd_cod_procedimiento_b_" + indice).val();
	var nombre_procedimiento = $("#hdd_nombre_procedimiento_b_" + indice).val();
	
	if (cod_procedimiento == "") {
		$("#d_nombre_procedimiento_" + indice).css({"color":"#FE6B6B"});
	} else {
		$("#d_nombre_procedimiento_" + indice).css({"color":"#5B5B5B"});
	}
	$("#txt_cod_procedimiento_" + indice).val(cod_procedimiento);
	$("#hdd_cod_procedimiento_" + indice).val(cod_procedimiento);
	$("#d_nombre_procedimiento_" + indice).html(nombre_procedimiento);
}

function validar_enter_cups_solic() {
	var isIE = document.all ? true : false;
	var key = (isIE) ? window.event.keyCode : event.which;
	
	var bol_enter = false;
	if (key == 13) {
		bol_enter = true;
		$("#btn_buscar_cups_solic").focus();
		buscar_cups_solic($("#hdd_cups_solic_num").val());
	}
	return !bol_enter;
}

function validar_hc_procedimientos_solic() {
	//Quitar error
	for (i = 0; i < 20; i++) {
		$("#txt_cod_procedimiento_" + i).removeClass("borde_error");
	}
	
	var ind_error_dupl = validar_duplicados_proc_solic();
	
	return ind_error_dupl;
}

function validar_duplicados_proc_solic() {
	var ind_error = 0;
	var cant_procedimientos = parseInt($("#hdd_cant_procedimientos").val(), 10);
	for (var i = 0; i < cant_procedimientos - 1; i++) {
		for (var j = i + 1; j < cant_procedimientos; j++) {
			if ($("#hdd_cod_procedimiento_" + i).val() != "" && $("#hdd_cod_procedimiento_" + i).val() == $("#hdd_cod_procedimiento_" + j).val()) {
				$("#txt_cod_procedimiento_" + i).addClass("borde_error");
				$("#txt_cod_procedimiento_" + j).addClass("borde_error");
				ind_error = -3;
			}
		}
	}
	
	return ind_error;
}

function obtener_parametros_proc_solic() {
	var params = "";
	var cont_aux = 0;
	var cant_procedimientos = parseInt($("#hdd_cant_procedimientos").val(), 10);
	for (i = 0; i < cant_procedimientos; i++) {
		if ($("#hdd_cod_procedimiento_" + i).val() != "") {
			params += "&cod_procedimiento_solic_" + cont_aux + "=" + str_encode($("#hdd_cod_procedimiento_" + i).val()) +
					  "&id_ojo_proc_solic_" + cont_aux + "=" + $("#cmb_ojo_" + i).val();
			
			cont_aux++;
		}
	}
	params += "&cant_procedimientos_solic=" + cont_aux;
	
	return params;
}
