function mostrar_lista_tabla() {
	var lista_tabla = $("#lista_tabla").val();
	
	for (i = 1; i <= lista_tabla; i++) {
		$("#tabla_diag_" + i).css("display", "block");
	}
}

function restar_tabla_daig() {
	var lista_tabla = $("#lista_tabla").val();
	
	if (lista_tabla > 4) {
		$("#tabla_diag_" + lista_tabla).css("display", "none");
		$("#ciex_diagnostico_" + lista_tabla).val("");
		$("#texto_diagnostico_" + lista_tabla).html("");
		$("#valor_ojos_" + lista_tabla).val("");
		var total = parseInt(lista_tabla, 10) - 1;
		$("#lista_tabla").val(total);
	}
}

function agregar_tabla_daig() {
	var lista_tabla = $("#lista_tabla").val();
	var total = parseInt(lista_tabla, 10) + 1;
	if (total <= 10) {
		$("#tabla_diag_" + total).css("display", "block");
		$("#lista_tabla").val(total);
	}
}

function abrir_buscar_diagnostico(num) {
	mostrar_formulario_flotante(1);
	reducir_formulario_flotante(800, "auto");
	posicionarDivFlotante("d_centro");
	
	$("#d_interno").html(
		'<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
			'<tr>' +
				'<td align="center" colspan="2"><h4>Buscar Diagn&oacute;sticos</h4></td>' +
			'</tr>' +
			'<tr>' +
				'<td align="center" style="width:90%">' +
					'<input type="text" name="txt_busca_diagnostico" id="txt_busca_diagnostico" placeholder="Buscar diagn&oacute;stico" onkeypress="return validar_enter_diag();">' +
				'</td>' +
				'<td align="center" style="width:10%">' +
					'<input type="hidden" id="hdd_diagnostico_num" value="' + num + '" />' +
					'<input type="button" id="btn_buscar_diagnostico" nombre="btn_buscar_diagnostico" value="Buscar" class="btnPrincipal peq" onclick="buscar_diagnosticos(' + num + ');"/> ' +
				'</td>' +
			'</tr>' +
		'</table>' +
		'<div id="d_buscar_diagnosticos"></div>');
	$("#txt_busca_diagnostico").focus();
}

function buscar_diagnosticos(num) {
	var txt_busca_diagnostico = $("#txt_busca_diagnostico").val();
	var params = "opcion=1&num=" + num + "&txt_busca_diagnostico=" + txt_busca_diagnostico;
	
	llamarAjax("../funciones/Class_Diagnosticos_ajax.php", params, "d_buscar_diagnosticos", "");
}

function seleccionar_diagnostico_ciex(cod_ciex, nombre, num) {
	$("#ciex_diagnostico_" + num).val(cod_ciex);
	$("#hdd_ciex_diagnostico_" + num).val(cod_ciex);
	$("#texto_diagnostico_" + num).html(nombre);
	$("#texto_diagnostico_" + num).css({"color":"#5B5B5B"});
	
	mostrar_formulario_flotante(0);
}

function buscar_diagnosticos_ciex(num) {
	var cod_ciex = $("#ciex_diagnostico_" + num).val();
	if (cod_ciex != "") {
		var params = "opcion=2&cod_ciex=" + cod_ciex + "&num=" + num;
		
		llamarAjax("../funciones/Class_Diagnosticos_ajax.php", params, "texto_diagnostico_" + num, "validar_ciex(" + num + ");");
	} else {
		$("#texto_diagnostico_" + num).html("");
		$("#hdd_ciex_diagnostico_" + num).val("");
		$("#valor_ojos_" + num).val("");
	}
}

function validar_ciex(num) {
	var texto_ciex = $("#hdd_texto_ciex_" + num).val();
	var codigo_ciex = $("#hdd_codigo_ciex_" + num).val();
	
	if (codigo_ciex == "") {
		$("#texto_diagnostico_" + num).html(texto_ciex);
		$("#texto_diagnostico_" + num).css({"color":"#FE6B6B"});
		$("#hdd_ciex_diagnostico_" + num).val("");
	} else {
		$("#texto_diagnostico_" + num).html(texto_ciex);
		$("#texto_diagnostico_" + num).css({"color":"#5B5B5B"});
		$("#ciex_diagnostico_" + num).val(codigo_ciex);
		$("#hdd_ciex_diagnostico_" + num).val(codigo_ciex);
	}
}

function validar_enter_diag() {
	var isIE = document.all ? true : false;
	var key = (isIE) ? window.event.keyCode : event.which;
	
	var bol_enter = false;
	if (key == 13) {
		bol_enter = true;
		$("#btn_buscar_diagnostico").focus();
		buscar_diagnosticos($("#hdd_diagnostico_num").val());
	}
	return !bol_enter;
}

function validar_diagnosticos_hc(ind_obligatorio) {
	var ind_error = 0;
	
	var ind_error_dupl = validar_duplicados_diagnosticos_hc();
	
	if (ind_obligatorio == 1) {
		if ($("#hdd_ciex_diagnostico_1").val() == "") {
			$("#ciex_diagnostico_1").addClass("borde_error");
			ind_error = -1;
		}
		if ($("#valor_ojos_1").val() == "") {
			$("#valor_ojos_1").addClass("borde_error");
			ind_error = -1;
		}
	}
	var cant_ciex = parseInt($("#lista_tabla").val(), 10);
	for (i = 1; i <= cant_ciex; i++) {
		var cod_ciex = $("#hdd_ciex_diagnostico_" + i).val();
	 	var val_ojos = $("#valor_ojos_" + i).val();
	 	if (cod_ciex != "" && val_ojos == "") {
			$("#valor_ojos_" + i).addClass("borde_error");
			ind_error = -1;
		}
	}
	
	return (ind_error_dupl == -2 ? ind_error_dupl : ind_error);
}

function validar_duplicados_diagnosticos_hc() {
	//Pintar normal
	for (i = 1; i <= 10; i++) {
		$("#ciex_diagnostico_" + i).removeClass("borde_error");
	 	 $("#valor_ojos_" + i).removeClass("borde_error");
	}
	
	var ind_error = 0;
	var cant_ciex = parseInt($("#lista_tabla").val(), 10);
	for (var i = 1; i < cant_ciex; i++) {
		for (var j = i + 1; j <= cant_ciex; j++) {
			if ($("#hdd_ciex_diagnostico_" + i).val() != "" && $("#hdd_ciex_diagnostico_" + i).val() == $("#hdd_ciex_diagnostico_" + j).val()) {
				$("#ciex_diagnostico_" + i).addClass("borde_error");
				$("#ciex_diagnostico_" + j).addClass("borde_error");
				ind_error = -2;
			}
		}
	}
	
	return ind_error;
}
