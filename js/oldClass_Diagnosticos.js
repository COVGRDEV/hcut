function mostrar_lista_tabla() {
	var lista_tabla = $('#lista_tabla').val();
	
	for (i = 1; i <= lista_tabla; i++) {
		$("#tabla_diag_" + i).css("display", "block");
	}
}

function restar_tabla_daig() {
	var lista_tabla = $('#lista_tabla').val();
	
	if (lista_tabla > 4) {
		$("#tabla_diag_" + lista_tabla).css("display", "none");
		$('#ciex_diagnostico_' + lista_tabla).val('');
		$('#texto_diagnostico_' + lista_tabla).html('');
		$('#valor_ojos_' + lista_tabla).val('');
		var total = parseInt(lista_tabla, 10) - 1;
		$('#lista_tabla').val(total);
	}
}

function agregar_tabla_daig() {
	var lista_tabla = $('#lista_tabla').val();
	var total = parseInt(lista_tabla, 10) + 1;
	if (total <= 10) {
		$("#tabla_diag_" + total).css("display", "block");
		$('#lista_tabla').val(total);
	}
}

function abrir_buscar_diagnostico(num) {
	mostrar_formulario_flotante(1);
	posicionarDivFlotante('d_centro');
	reducir_formulario_flotante(800, 'auto');
	$('#d_interno').html(
		'<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%"> ' +
	    	'<tr>' +
	    		'<td align="center" colspan="2"><h4>Buscar Diagn&oacute;sticos</h4></td>' +
			'</tr>' +
			'<tr>' +
				'<td align="center" style="width:90%">' +
					'<input type="text" name="txt_busca_diagnostico" id="txt_busca_diagnostico" placeholder="Buscar diagn&oacute;stico">' +
				'</td>' +
				'<td align="center" style="width:10%">' +
					'<input type="button" id="btn_buscar_diagnostico" nombre="btn_buscar_diagnostico" value="Buscar" class="btnPrincipal peq" onclick="buscar_diagnosticos('+num+');"/> ' +                          
				'</td>' +
			'</tr>' +
		'</table>' +
		'<div id="d_buscar_diagnosticos"></div>');
}

function buscar_diagnosticos(num) {
	var txt_busca_diagnostico = $('#txt_busca_diagnostico').val();
	var params = 'opcion=1&num=' + num +
				 '&txt_busca_diagnostico=' + txt_busca_diagnostico;
	llamarAjax("../funciones/Class_Diagnosticos_ajax.php", params, "d_buscar_diagnosticos", "");
}

function seleccionar_diagnostico_ciex(cod_ciex, nombre, num) {
	$('#ciex_diagnostico_' + num).val(cod_ciex);
	$('#hdd_ciex_diagnostico_' + num).val(cod_ciex);
	$('#texto_diagnostico_' + num).html(nombre);
	$('#texto_diagnostico_' + num).css({'color':'#5B5B5B'});
	mostrar_formulario_flotante(0);
}

function buscar_diagnosticos_ciex(num) {
	var cod_ciex = $('#ciex_diagnostico_' + num).val();
	if (cod_ciex != '') {
		var params = 'opcion=2&cod_ciex=' + cod_ciex + '&num=' + num;
		llamarAjax("../funciones/Class_Diagnosticos_ajax.php", params, "texto_diagnostico_" + num, "validar_ciex(" + num + ");");
	} else {
		$('#texto_diagnostico_' + num).html('');
		$('#hdd_ciex_diagnostico_' + num).val('');
		$('#valor_ojos_' + num).val('');
	}
}

function validar_ciex(num) {
	var texto_ciex = $('#hdd_texto_ciex_' + num).val();
	var codigo_ciex = $('#hdd_codigo_ciex_' + num).val();
	
	if (codigo_ciex == '') {
		$('#txt_busca_diagnostico_' + num).val(texto_ciex);
		$('#txt_busca_diagnostico_' + num).css({'color':'#FE6B6B'});
		$('#hdd_ciex_diagnostico_' + num).val('');
		$('#ciex_diagnostico_' + num).val('');
	} else {
		$('#txt_busca_diagnostico_' + num).val(texto_ciex);
		$('#txt_busca_diagnostico_' + num).css({'color':'#5B5B5B'});
		$('#ciex_diagnostico_' + num).val(codigo_ciex);
		$('#hdd_ciex_diagnostico_' + num).val(codigo_ciex);
	}
}

function cargar_diagnosticos(campo_text, num) {
	var texto_ciex = $('#' + campo_text).val();
	
	var cod = -1;
	var elem = texto_ciex.split('|');
	texto = elem[0].trim();
	if (elem.length > 1) {
		cod = elem[1].trim();	
	}
	
	var ciex_hdd = $('#hdd_ciex_diagnostico_' + num).val();
	if (ciex_hdd == '' || (cod != -1 && cod != ciex_hdd)) {
		var params = 'opcion=2&cod_ciex=' + cod + '&num=' + num;
		llamarAjax("../funciones/Class_Diagnosticos_ajax.php", params, "texto_diagnostico_" + num, "validar_ciex(" + num + ");");
	}
}
