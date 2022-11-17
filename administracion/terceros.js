//Funcion que busca un convenio especifico
function buscar_terceros() {
    $("#frm_buscar_terceros").validate({
        rules: {
            txt_parametro: {
                required: true
            },
        },
        submitHandler: function () {
            var params = "opcion=1&parametro=" + str_encode($("#txt_parametro").val());
			
            llamarAjax("terceros_ajax.php", params, "d_principal_terceros", "");
            return false;
        },
    });
}

//Funcion que muestra la ventana para nuevo examen
function abrir_nuevo_tercero() {//d_interno_adic | d_interno
    var params = "opcion=2&tipo=1&id_tercero=0";
    llamarAjax("terceros_ajax.php", params, "d_interno_adic", "mostrar_formulario_flotante_adic(1);");
}

//Funcion que muestra la ventana para editar terceros
function seleccionar_tercero(id_tercero) {
    var params = "opcion=2&tipo=0&id_tercero=" + id_tercero;
    llamarAjax("terceros_ajax.php", params, "d_interno_adic", "mostrar_formulario_flotante_adic(1);");
}

function crear_tercero() {
	$("#btn_guardar_tercero").attr("disabled", "disabled");
	$("#d_contenedor_error_2").css("display", "none");
	if (validar_tercero()) {
		var params = "opcion=3&tipo=1" +
					 "&id_tipo_documento=" + $("#cmb_tipo_documento").val() +
					 "&numero_documento=" + $("#txt_numero_documento").val() +
					 "&numero_verificacion=" + $("#txt_numero_verificacion").val() +
					 "&nombre_tercero=" + str_encode($("#txt_nombre_tercero").val()) +
					 "&ind_activo=" + ($("#chk_activo").is(":checked") ? 1 : 0);
		
		llamarAjax("terceros_ajax.php", params, "d_resultado_tercero", "verificar_guardar_tercero();");
	} else {
		$("#d_contenedor_error_2").css("display", "block");
		$("#d_contenedor_error_2").html("Los campos marcados en rojo son obligatorios");
		window.scroll(0, 0);
		$("#btn_guardar_tercero").removeAttr("disabled");
	}
}

function editar_tercero() {
	$("#btn_guardar_tercero").attr("disabled", "disabled");
	$("#d_contenedor_error_2").css("display", "none");
	if (validar_tercero()) {
		var params = "opcion=3&tipo=0" +
					 "&id_tercero=" + $("#hdd_id_tercero").val() +
					 "&id_tipo_documento=" + $("#cmb_tipo_documento").val() +
					 "&numero_documento=" + $("#txt_numero_documento").val() +
					 "&numero_verificacion=" + $("#txt_numero_verificacion").val() +
					 "&nombre_tercero=" + str_encode($("#txt_nombre_tercero").val()) +
					 "&ind_activo=" + ($("#chk_activo").is(":checked") ? 1 : 0);
		
		llamarAjax("terceros_ajax.php", params, "d_resultado_tercero", "verificar_guardar_tercero();");
	} else {
		$("#d_contenedor_error_2").css("display", "block");
		$("#d_contenedor_error_2").html("Los campos marcados en rojo son obligatorios");
		window.scroll(0, 0);
		$("#btn_guardar_tercero").removeAttr("disabled");
	}
}

function validar_tercero() {
	var resultado = true;
	$("#cmb_tipo_documento").removeClass("borde_error");
	$("#txt_numero_documento").removeClass("borde_error");
	$("#txt_numero_verificacion").removeClass("borde_error");
	$("#txt_nombre_tercero").removeClass("borde_error");
	
	if ($("#cmb_tipo_documento").val() == "") {
		$("#cmb_tipo_documento").addClass("borde_error");
		resultado = false;
	}
	if ($("#txt_numero_documento").val().length < 5) {
		$("#txt_numero_documento").addClass("borde_error");
		resultado = false;
	}
	if ($("#txt_numero_verificacion").val() == "" && $("#cmb_tipo_documento").val() == "146") {
		$("#txt_numero_verificacion").addClass("borde_error");
		resultado = false;
	}
	if ($("#txt_nombre_tercero").val() == "") {
		$("#txt_nombre_tercero").addClass("borde_error");
		resultado = false;
	}
	
	return resultado;
}

function verificar_guardar_tercero() {
	$("#btn_guardar_tercero").removeAttr("disabled");
    var resultado = parseInt($("#hdd_guardar_tercero").val(), 10);
	
    if (resultado > 0) {
        $("#d_contenedor_exito").css("display", "block");
        $("#d_contenedor_exito").html("Registro guardado con &eacute;xito");
		
		$("#d_principal_terceros").html("");
        cerrar_div_centro_adic();
		
        setTimeout(function () {
            $("#d_contenedor_exito").css("display", "none");
        }, 3000);
    } else if (resultado == -2) {
        $("#d_contenedor_error_2").css("display", "block");
        $("#d_contenedor_error_2").html("Error interno al tratar de guardar el tercero");
    } else if (resultado == -3) {
        $("#d_contenedor_error_2").css("display", "block");
        $("#d_contenedor_error_2").html("Error - N&uacute;mero de verificaci&oacute;n no v&aacute;lido");
		$("#txt_numero_verificacion").addClass("borde_error");
    } else if (resultado == -4) {
        $("#d_contenedor_error_2").css("display", "block");
        $("#d_contenedor_error_2").html("Error - El n&uacute;mero de documento ya se encuentra registrado");
		$("#txt_numero_documento").addClass("borde_error");
    } else {
        $("#d_contenedor_error_2").css("display", "block");
        $("#d_contenedor_error_2").html("Error al tratar de guardar el tercero");
    }
}
