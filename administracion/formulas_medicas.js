function mostrar_formulario_flotante(tipo) {
    if (tipo == 1) { //mostrar
        $('#fondo_negro').css('display', 'block');
        $('#d_centro').slideDown(400).css('display', 'block');
    } else if (tipo == 0) { //Ocultar
        $('#fondo_negro').css('display', 'none');
        $('#d_centro').slideDown(400).css('display', 'none');
    }
}

function reducir_formulario_flotante(ancho, alto) { //480, 390
    $('.div_centro').width(ancho);
    //$('.div_centro').height(alto);
    $('.div_centro').css('top', '20%');
    $('.div_interno').width(ancho/*-15*/);
    //$('.div_interno').height(alto-35);
}

function mostrar_formulario(tipo) {
    if (tipo == 1) {//mostrar
        $('.formulario').slideDown(600).css('display', 'block')
    }
    else if (tipo == 0) {//Ocultar
        $('.formulario').slideUp(600).css('display', 'none')
    }
}

function cargar_formulas(txt_buscar_formula) {
	var params = "opcion=1&txt_buscar_formula=" + str_encode(txt_buscar_formula);
	
    llamarAjax("formulas_medicas_ajax.php", params, "principal_formulas", "mostrar_formulario(1)");
}

function buscar_formulas() {
    $("#frm_listado_formulas").validate({
        rules: {
            txt_buscar_formula: {
                required: true,
            }
        },
        submitHandler: function() {
			cargar_formulas($("#txt_buscar_formula").val());
			
            return false;
        },
    });
}

function llamar_crear_formula() {
    var params = "opcion=2";
	
    llamarAjax("formulas_medicas_ajax.php", params, "principal_formulas", "mostrar_formulario(1)");
}

function seleccionar_formula(indice) {
	var params = "opcion=2&id_formula_med=" + $("#hdd_id_formula_med_" + indice).val();
	
    llamarAjax("formulas_medicas_ajax.php", params, "principal_formulas", "mostrar_formulario(1);");
}

function seleccionar_convenio(id_convenio) {
	var params = "opcion=3&id_convenio=" + id_convenio;
	
    llamarAjax("formulas_medicas_ajax.php", params, "d_planes", "");
}

function agregar_diagnostico() {
	var cont_cx = parseInt($("#hdd_cont_cx").val(), 10);
	
	if (cont_cx < 50) {
		$("#tr_cx_" + cont_cx).css("display", "table-row");
		
		$("#hdd_cont_cx").val(cont_cx + 1);
	} else {
		alert("Se ha alcanzado el l\xedmite de diagn\xf3sticos");
	}
}

function borrar_diagnostico(indice) {
	$("#hdd_cod_ciex_" + indice).val("");
	$("#td_cx_" + indice).html("");
	$("#tr_cx_" + indice).css("display", "none");
}

function mostrar_buscar_diagnostico(indice) {
	mostrar_formulario_flotante(1);
	posicionarDivFlotante("d_centro");
	
	$("#d_interno").html(
		'<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
			'<tr>' +
				'<td align="center" colspan="2"><h4>Buscar Diagn&oacute;sticos</h4></td>' +
			'</tr>' +
			'<tr>' +
				'<td align="center" style="width:90%">' +
					'<input type="text" name="txt_busca_diagnostico" id="txt_busca_diagnostico" placeholder="Buscar diagn&oacute;stico">' +
				'</td>' +
				'<td align="center" style="width:10%">' +
					'<input type="button" id="btn_buscar_diagnostico" nombre="btn_buscar_diagnostico" value="Buscar" class="btnPrincipal peq" onclick="buscar_diagnostico(' + indice + ');"/> ' +
				'</td>' +
			'</tr>' +
		'</table>' +
		'<div id="d_buscar_diagnosticos"></div>');
}

function buscar_diagnostico(indice) {
	var params = "opcion=4&indice=" + indice + "&txt_busca_diagnostico=" + str_encode($("#txt_busca_diagnostico").val());
	
	llamarAjax("formulas_medicas_ajax.php", params, "d_buscar_diagnosticos", "");
}

function seleccionar_diagnostico_ciex(cod_ciex, nombre, indice) {
	$("#hdd_cod_ciex_" + indice).val(cod_ciex);
	$("#td_cx_" + indice).html(cod_ciex + " - " + nombre);
	
	mostrar_formulario_flotante(0);
}

function agregar_ftexto() {
	var cont_ftexto = parseInt($("#hdd_cont_ftexto").val(), 10);
	
	if (cont_ftexto < 50) {
		$("#tr_ftexto_" + cont_ftexto).css("display", "table-row");
		
		$("#hdd_cont_ftexto").val(cont_ftexto + 1);
	} else {
		alert("Se ha alcanzado el l\xedmite de detalle");
	}
}

function borrar_ftexto(indice) {
	$("#txt_desc_texto_" + indice).val("");
	$("#tr_ftexto_" + indice).css("display", "none");
}

function validar_crear_formula() {
	var resultado = true;
	
	$("#contenedor_error").css("display", "none");
	
	var cont_cx = parseInt($("#hdd_cont_cx").val(), 10);
	var visibles_aux = false;
	for (var i = 0; i < cont_cx; i++) {
		$("#td_cx_" + i).removeClass("bordeAdmision");
		if ($("#tr_cx_" + i).is(":visible")) {
			visibles_aux = true;
		}
	}
	if (!visibles_aux) {
		agregar_diagnostico();
		cont_cx++;
	}
	
	var cont_ftexto = parseInt($("#hdd_cont_ftexto").val(), 10);
	visibles_aux = false;
	for (var i = 0; i < cont_ftexto; i++) {
		$("#txt_desc_texto_" + i).removeClass("bordeAdmision");
		if ($("#tr_ftexto_" + i).is(":visible")) {
			visibles_aux = true;
		}
	}
	if (!visibles_aux) {
		agregar_ftexto();
		cont_ftexto++;
	}
	
	for (var i = 0; i < cont_cx; i++) {
		if ($("#tr_cx_" + i).is(":visible") && $("#hdd_cod_ciex_" + i).val() == "") {
			$("#td_cx_" + i).addClass("bordeAdmision");
			resultado = false;
		}
	}
	
	for (var i = 0; i < cont_ftexto; i++) {
		if ($("#tr_ftexto_" + i).is(":visible") && $("#txt_desc_texto_" + i).val() == "") {
			$("#txt_desc_texto_" + i).addClass("bordeAdmision");
			resultado = false;
		}
	}
	
	return resultado;
}

function crear_formula() {
	if (validar_crear_formula()) {
		var params = "opcion=5&id_convenio=" + $("#cmb_convenio").val() +
					 "&id_plan=" + $("#cmb_plan").val();
		
		var cont_aux = 0;
		var cont_cx = parseInt($("#hdd_cont_cx").val(), 10);
		for (var i = 0; i < cont_cx; i++) {
			if ($("#tr_cx_" + i).is(":visible")) {
				params += "&cod_ciex_" + cont_aux + "=" + $("#hdd_cod_ciex_" + i).val();
				cont_aux++;
			}
		}
		params += "&cant_cx=" + cont_aux;
		
		cont_aux = 0;
		var cont_ftexto = parseInt($("#hdd_cont_ftexto").val(), 10);
		for (var i = 0; i < cont_ftexto; i++) {
			if ($("#tr_ftexto_" + i).is(":visible")) {
				params += "&desc_texto_" + cont_aux + "=" + str_encode($("#txt_desc_texto_" + i).val());
				cont_aux++;
			}
		}
		params += "&cant_ftexto=" + cont_aux;
		
		llamarAjax("formulas_medicas_ajax.php", params, "d_guardar_formulas", "terminar_crear_formula();");
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Los campos marcados en rojo son obligatorios");
	}
}

function terminar_crear_formula() {
	var resultado = $("#hdd_resul_guardar_formula").val();
	if (resultado > 0) {
		$("#contenedor_exito").css("display", "block");
		$("#contenedor_exito").html("F&oacute;rmula m&eacute;dica guardada con &eacute;xito");
		setTimeout('$("#contenedor_exito").css("display", "none")', 3000);
		
		cargar_formulas($("#txt_buscar_formula").val());
	} else if (resultado == "-3") {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error - La f&oacute;rmula m&eacute;dica ya existe");
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error interno al tratar de guardar la f&oacute;rmula m&eacute;dica");
	}
}

function editar_formula() {
	if (validar_crear_formula()) {
		var params = "opcion=6&id_formula_med=" + $("#hdd_id_formula_med").val() +
					 "&id_convenio=" + $("#cmb_convenio").val() +
					 "&id_plan=" + $("#cmb_plan").val();
		if ($("#chk_activo").is(":checked")) {
			params += "&ind_activo=1";
		} else {
			params += "&ind_activo=0";
		}
		
		var cont_aux = 0;
		var cont_cx = parseInt($("#hdd_cont_cx").val(), 10);
		for (var i = 0; i < cont_cx; i++) {
			if ($("#tr_cx_" + i).is(":visible")) {
				params += "&cod_ciex_" + cont_aux + "=" + $("#hdd_cod_ciex_" + i).val();
				cont_aux++;
			}
		}
		params += "&cant_cx=" + cont_aux;
		
		cont_aux = 0;
		var cont_ftexto = parseInt($("#hdd_cont_ftexto").val(), 10);
		for (var i = 0; i < cont_ftexto; i++) {
			if ($("#tr_ftexto_" + i).is(":visible")) {
				params += "&desc_texto_" + cont_aux + "=" + str_encode($("#txt_desc_texto_" + i).val());
				cont_aux++;
			}
		}
		params += "&cant_ftexto=" + cont_aux;
		
		llamarAjax("formulas_medicas_ajax.php", params, "d_guardar_formulas", "terminar_crear_formula();");
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Los campos marcados en rojo son obligatorios");
	}
}

/*
 * Tipo
 * 1=crear
 * 2=editar
 */
function validar_exito() {

    var hdd_exito = $('#hdd_exito').val();
    $('.formulario').css('display', 'none')
    if (hdd_exito > 0) {
        $("#contenedor_exito").css("display", "block");
        $('#contenedor_exito').html('Datos guardados correctamente');
        setTimeout("$('#contenedor_exito').slideUp(200).css('display', 'none')", 5000);
    }
    else {
        $("#contenedor_error").css("display", "block");
        $('#contenedor_error').html('Error al guardar usuarios');
        setTimeout("$('#contenedor_error').slideUp(200).css('display', 'none')", 5000);
    }
}

function volver_inicio() {
	cargar_formulas($("#txt_buscar_formula").val());
}

function crear_usuarios() {
    var txt_nombre_usuario = $('#txt_nombre_usuario').val();
    var txt_apellido_usuario = $('#txt_apellido_usuario').val();
    var cmb_tipo_documento = $('#cmb_tipo_documento').val();
    var txt_numero_documento = $('#txt_numero_documento').val();
    var txt_usuario = $('#txt_usuario').val();
    var txt_clave = $('#txt_clave').val();
    var perfiles_usuario = new Array();
    $("input[name='check_pefiles']:checked").each(function() {
        perfiles_usuario.push($(this).val());
    });
    var params = 'opcion=4&txt_nombre_usuario=' + txt_nombre_usuario +
            '&txt_apellido_usuario=' + txt_apellido_usuario +
            '&cmb_tipo_documento=' + cmb_tipo_documento +
            '&txt_numero_documento=' + txt_numero_documento +
            '&txt_usuario=' + txt_usuario +
            '&txt_clave=' + txt_clave +
            '&array_perfiles=' + perfiles_usuario;
    llamarAjax("formulas_medicas_ajax.php", params, "principal_usuarios", "validar_exito(); volver_inicio()");
}

function editar_usuarios() {
    var txt_nombre_usuario = $('#txt_nombre_usuario').val();
    var txt_apellido_usuario = $('#txt_apellido_usuario').val();
    var cmb_tipo_documento = $('#cmb_tipo_documento').val();
    var txt_numero_documento = $('#txt_numero_documento').val();
    var hdd_id_usuario = $('#hdd_id_usuario').val();
    var check_estado = $('#check_estado').is(':checked') ? 1 : 0;
    var perfiles_usuario = new Array();
    $("input[name='check_pefiles']:checked").each(function() {
        perfiles_usuario.push($(this).val());
    });

    var params = 'opcion=6&txt_nombre_usuario=' + txt_nombre_usuario +
            '&txt_apellido_usuario=' + txt_apellido_usuario +
            '&cmb_tipo_documento=' + cmb_tipo_documento +
            '&txt_numero_documento=' + txt_numero_documento +
            '&array_perfiles=' + perfiles_usuario +
            '&hdd_id_usuario=' + hdd_id_usuario +
            '&check_estado=' + check_estado;
    llamarAjax("formulas_medicas_ajax.php", params, "principal_usuarios", "cerrar_div_centro(); validar_exito(); volver_inicio()");
}

function confirmar_guardar() {
    $('#d_interno').html(
            '<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
            '<tr>' +
            '<th align="center">' +
            '<h4>&iquest;Est&aacute; seguro de guardar esta informaci&oacute;n?</h4>' +
            '</th>' +
            '</tr>' +
            '<tr>' +
            '<th align="center">' +
            '<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" class="btnPrincipal" value="Aceptar" onclick="editar_usuarios();"/>\n' +
            '<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" class="btnSecundario" value="Cancelar" onclick="cerrar_div_centro();"/> ' +
            '</th>' +
            '</tr>' +
            '</table>');
}
