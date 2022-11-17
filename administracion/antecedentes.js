function mostrar_formulario(tipo) {
    if (tipo == 1) {//mostrar
        $('.formulario').slideDown(600).css('display', 'block')
    }
    else if (tipo == 0) {//Ocultar
        $('.formulario').slideUp(600).css('display', 'none')
    }
}

function cargar_antecedentes() {
	 var params = 'opcion=1';
    llamarAjax("antecedentes_ajax.php", params, "principal_antecedentes", "mostrar_formulario(1)");
}

function cargar_formulario_editar(id_antecedente, tipo_antecedente) {
	var params = 'opcion=2&id_antecedente=' + id_antecedente +
				 '&tipo_antecedente=' + tipo_antecedente;
	llamarAjax("antecedentes_ajax.php", params, "principal_antecedentes", "mostrar_formulario(1)");
}


function mostrar_formulario_flotante(tipo) {
	if(tipo==1){//mostrar
		$('#fondo_negro').css('display', 'block');
		$('#d_centro').slideDown(400).css('display', 'block');
	}
	else if(tipo==0){//Ocultar
		$('#fondo_negro').css('display', 'none');
		$('#d_centro').slideDown(400).css('display', 'none');
	}
}

function reducir_formulario_flotante(ancho, alto) {
	$('.div_centro').width(ancho);
	$('.div_centro').css('top', '20%');
	$('.div_interno').width(ancho/*-15*/);
}

function validar_editar_antecedente() {
	$("#frm_antecedentes").validate({
        rules: {
            txt_nombre_antecedente: {
                required: true,
            }
        },
        submitHandler: function() {
			mostrar_formulario_flotante(1);
            reducir_formulario_flotante(400, 250);
			posicionarDivFlotante('d_centro');
            confirmar_guardar();
        },
    });
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
					'<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" class="btnPrincipal" value="Aceptar" onclick="editar_antecedente();"/>\n' +
					'<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" class="btnSecundario" value="Cancelar" onclick="cerrar_div_centro();"/>' +
				'</th>' +
			'</tr>' +
		'</table>');
}

function editar_antecedente() {
	var id_antecedente = $('#hdd_id_antecedente').val();
	var tipo_antecedente = $('#hdd_tipo_antecedente').val();
    
    var params = "opcion=3&id_antecedente=" + id_antecedente +
				 "&tipo_antecedente=" + tipo_antecedente +
	             "&nombre_antecedente=" + $("#txt_nombre_antecedente").val();
	if (document.getElementById("chk_inicial").checked) {
		params += "&ind_inicial=1";
	} else {
		params += "&ind_inicial=0";
	}
	if (document.getElementById("chk_activo").checked) {
		params += "&ind_activo=1";
	} else {
		params += "&ind_activo=0";
	}
	
	llamarAjax("antecedentes_ajax.php", params, "principal_antecedentes", "cerrar_div_centro(); validar_exito();");
}

function validar_exito() {
	var hdd_exito = $('#hdd_exito').val();
	if (hdd_exito > 0) {
		$("#contenedor_exito").css("display", "block");
		$('#contenedor_exito').html('Datos guardados correctamente');
		setTimeout("$('#contenedor_exito').slideUp(200).css('display', 'none')", 5000);
		setTimeout(cargar_antecedentes(), 5000);
	} else {
		$("#contenedor_error").css("display", "block");
		$('#contenedor_error').html('Error al guardar el antecedente');
		setTimeout("$('#contenedor_error').slideUp(200).css('display', 'none')", 5000);
		setTimeout(cargar_antecedentes(), 5000);
	}
}

function cargar_formulario_crear() {
    var params = "opcion=4";
	
	llamarAjax("antecedentes_ajax.php", params, "principal_antecedentes", "mostrar_formulario(1);");
}

function validar_crear_antecedente() {
	$("#frm_antecedentes").validate({
        rules: {
            txt_nombre_antecedente: {
                required: true,
            }
        },
        submitHandler: function() {
			if ($("#cmb_tipo_antecedente").val() == "-1") {
                $("#contenedor_error").css("display", "block");
                $('#contenedor_error').html('Debe seleccionar el tipo de antecedente');
                return false;
			}
			mostrar_formulario_flotante(1);
            reducir_formulario_flotante(400, 250);
			posicionarDivFlotante('d_centro');
            confirmar_crear();
        },
    });
}

function confirmar_crear() {
	$('#d_interno').html(
		'<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
			'<tr>' +
				'<th align="center">' +
					'<h4>&iquest;Est&aacute; seguro de crear esta informaci&oacute;n?</h4>' +
				'</th>' +
			'</tr>' +
			'<tr>' +
				'<th align="center">' +
					'<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" class="btnPrincipal" value="Aceptar" onclick="crear_antecedente();"/>\n' +
					'<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" class="btnSecundario" value="Cancelar" onclick="cerrar_div_centro();"/>' +
				'</th>' +
			'</tr>' +
		'</table>');
}

function crear_antecedente() {
    var params = "opcion=5&tipo_antecedente=" + $("#cmb_tipo_antecedente").val() +
	             "&nombre_antecedente=" + $("#txt_nombre_antecedente").val();
	if (document.getElementById("chk_inicial").checked) {
		params += "&ind_inicial=1";
	} else {
		params += "&ind_inicial=0";
	}
	if (document.getElementById("chk_activo").checked) {
		params += "&ind_activo=1";
	} else {
		params += "&ind_activo=0";
	}
	
	llamarAjax("antecedentes_ajax.php", params, "principal_antecedentes", "cerrar_div_centro(); validar_exito();");
}
