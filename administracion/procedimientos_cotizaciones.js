function ver_todos_procedimientos() {
    $('#txt_buscar_procedimiento').val("");
    
    var params = "opcion=1&txt_buscar_procedimiento=" + $("#txt_buscar_procedimiento").val();
	
    llamarAjax("procedimientos_cotizaciones_ajax.php", params, "d_principal_procedimientos", "mostrar_formulario(1)");
}

function buscar_procedimientos() {
    $("#frm_listado_procedimientos").validate({
        rules: {
            txt_buscar_procedimiento: {
                required: true,
            }
        },
        submitHandler: function() {
            var params = "opcion=1&txt_buscar_procedimiento=" + $("#txt_buscar_procedimiento").val();
			
            llamarAjax("procedimientos_cotizaciones_ajax.php", params, "d_principal_procedimientos", "mostrar_formulario(1)");
            return false;
        },
    });
}

function mostrar_formulario(tipo) {
    if (tipo == 1) { //mostrar
        $('.formulario').slideDown(600).css('display', 'block')
    } else if (tipo == 0) { //Ocultar
        $('.formulario').slideUp(600).css('display', 'none')
    }
}

function llamar_crear_procedimiento() {
    var params = 'opcion=2';
	
    llamarAjax("procedimientos_cotizaciones_ajax.php", params, "d_principal_procedimientos", "mostrar_formulario(1)");
}

function seleccionar_procedimiento(id_proc_cotiz) {
    var params = "opcion=2&id_proc_cotiz=" + id_proc_cotiz;
	
    llamarAjax("procedimientos_cotizaciones_ajax.php", params, "d_principal_procedimientos", "mostrar_formulario(1);");
}

function validar_editar_procedimiento() {
	var result = 0;
	$("#contenedor_error").css("display", "none");
	
	$("#txt_nombre_proc_cotiz").removeClass("borde_error");
	
	if (trim($('#txt_nombre_proc_cotiz').val()) == '') {
		$("#txt_nombre_proc_cotiz").addClass("borde_error");
		result = 1;
	}
	
	if (result == 0) {
		mostrar_formulario_flotante(1);
		reducir_formulario_flotante(400, 250);
		posicionarDivFlotante('d_centro');
		confirmar_guardar();
	} else {
		$("#contenedor_error").css("display", "block");
		$('#contenedor_error').html('Los campos marcados en rojo son obligatorios.');
		window.scroll(0, 0);
	}
}

function validar_crear_procedimiento() {
	var result = 0;
	$("#contenedor_error").css("display", "none");
	
	$("#txt_nombre_proc_cotiz").removeClass("borde_error");
	
	if (trim($('#txt_nombre_proc_cotiz').val()) == '') {
		$("#txt_nombre_proc_cotiz").addClass("borde_error");
		result = 1;
	}
	
	if (result == 0) {
		crear_procedimiento();
	} else {
		$("#contenedor_error").css("display", "block");
		$('#contenedor_error').html('Los campos marcados en rojo son obligatorios.');
		window.scroll(0, 0);
	}
}

function volver_inicio() {
    if ($('#txt_buscar_procedimiento').val() != '') {
        $("#frm_listado_procedimientos").submit();
    } else {
        ver_todos_procedimientos();
    }
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
					'<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" class="btnPrincipal" value="Aceptar" onclick="editar_procedimiento();"/>\n' +
					'<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" class="btnSecundario" value="Cancelar" onclick="cerrar_div_centro();"/> ' +
				'</th>' +
			'</tr>' +
		'</table>');
}

function editar_procedimiento() {
	cerrar_div_centro();
	
	var params = "opcion=3&id_proc_cotiz=" + $("#hdd_id_proc_cotiz").val() +
				 "&nombre_proc_cotiz=" + str_encode($("#txt_nombre_proc_cotiz").val()) +
				 "&ind_activo=" + ($('#chk_activo').is(':checked') ? 1 : 0);
	
	llamarAjax("procedimientos_cotizaciones_ajax.php", params, "d_guardar_procedimientos", "validar_exito();");
}

function crear_procedimiento() {
	var params = "opcion=4&nombre_proc_cotiz=" + str_encode($("#txt_nombre_proc_cotiz").val());
	
	llamarAjax("procedimientos_cotizaciones_ajax.php", params, "d_guardar_procedimientos", "validar_exito();");
}

function validar_exito() {
	var hdd_exito = $("#hdd_exito").val();
	if (hdd_exito > 0) {
		$("#contenedor_exito").css("display", "block");
		$('#contenedor_exito').html('Datos guardados correctamente');
		window.scroll(0, 0);
		setTimeout(
			function () {
				$('#contenedor_exito').slideUp(200).css('display', 'none');
			}, 2000);
		volver_inicio();
	} else {
		$("#contenedor_error").css("display", "block");
		$('#contenedor_error').html('Error al guardar el registro');
		window.scroll(0, 0);
	}
}
