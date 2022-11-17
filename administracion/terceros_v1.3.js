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
		
		if($("#tr_obligaciones_terceros").is(":visible")){
			var obligaciones = $("#cmb_obligaciones_terceros").val();
			var det_tributarios = $("#cmb_detalles_tributarios_t").val();
			var ind_iva = ($("#chk_ind_iva").is(":checked") ? 1 : 0);
		}else{
			var obligaciones = $("#hdd_obligaciones_terceros").val();
			var det_tributarios = $("#hdd_detalles_tributarios").val();
			var ind_iva = "";
		}
		
		var params = "opcion=3&tipo=1" +
					 "&id_tipo_documento=" + $("#cmb_tipo_documento_t").val() +
					 "&numero_documento=" + $("#txt_numero_documento_t").val() +
					 "&numero_verificacion=" + $("#txt_numero_verificacion_t").val() +
					 "&nombre_tercero=" + str_encode($("#txt_nombre_tercero_t").val()) +
					 "&nombre_1=" + str_encode($("#txt_nombre_1_t").val()) +
					 "&nombre_2=" + str_encode($("#txt_nombre_2_t").val()) +
					 "&apellido_1=" + str_encode($("#txt_apellido_1_t").val()) +
					 "&apellido_2=" + str_encode($("#txt_apellido_2_t").val()) +
					 "&email=" + str_encode($("#txt_email_tercero_t").val()) +
					 "&id_paciente=" + $("#hdd_id_paciente_t").val() +
					 "&ind_activo=" + ($("#chk_activo_t").is(":checked") ? 1 : 0) +
					 "&obligaciones=" + obligaciones +
					 "&det_tributarios=" + det_tributarios +
					 "&ind_iva=" + ind_iva;
		
		llamarAjax("terceros_ajax.php", params, "d_resultado_tercero_t", "verificar_guardar_tercero();");
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
		
		if($("#tr_obligaciones_terceros").is(":visible")){
			var obligaciones = $("#cmb_obligaciones_terceros").val();
			var det_tributarios = $("#cmb_detalles_tributarios_t").val();
			var ind_iva = ($("#chk_ind_iva").is(":checked") ? 1 : 0);
		}else{
			var obligaciones = $("#hdd_obligaciones_terceros").val();
			var det_tributarios = $("#hdd_detalles_tributarios").val();
			var ind_iva = "";
		}
		
		var params = "opcion=3&tipo=0" +
					 "&id_tercero=" + $("#hdd_id_tercero_t").val() +
					 "&id_tipo_documento=" + $("#cmb_tipo_documento_t").val() +
					 "&numero_documento=" + $("#txt_numero_documento_t").val() +
					 "&numero_verificacion=" + $("#txt_numero_verificacion_t").val() +
					 "&nombre_tercero=" + str_encode($("#txt_nombre_tercero_t").val()) +
					 "&nombre_1=" + str_encode($("#txt_nombre_1_t").val()) +
					 "&nombre_2=" + str_encode($("#txt_nombre_2_t").val()) +
					 "&apellido_1=" + str_encode($("#txt_apellido_1_t").val()) +
					 "&apellido_2=" + str_encode($("#txt_apellido_2_t").val()) +
					 "&email=" + str_encode($("#txt_email_tercero_t").val()) +
					 "&id_paciente=" + $("#hdd_id_paciente_t").val() +
					 "&ind_activo=" + ($("#chk_activo_t").is(":checked") ? 1 : 0) +
					 "&obligaciones=" + obligaciones +
					 "&det_tributarios=" +det_tributarios +
					 "&ind_iva=" + ind_iva;
		
		llamarAjax("terceros_ajax.php", params, "d_resultado_tercero_t", "verificar_guardar_tercero();");
	} else {
		$("#d_contenedor_error_2").css("display", "block");
		$("#d_contenedor_error_2").html("Los campos marcados en rojo son obligatorios");
		window.scroll(0, 0);
		$("#btn_guardar_tercero").removeAttr("disabled");
	}
}

function validar_tercero() {
	var resultado = true;
	$("#cmb_tipo_documento_t").removeClass("borde_error");
	$("#txt_numero_documento_t").removeClass("borde_error");
	$("#txt_numero_verificacion_t").removeClass("borde_error");
	$("#txt_nombre_tercero_t").removeClass("borde_error");
	$("#txt_nombre_1_t").removeClass("borde_error");
	$("#txt_apellido_1_t").removeClass("borde_error");
	$("#txt_email_tercero_t").removeClass("borde_error");
	
	if ($("#cmb_tipo_documento_t").val() == "") {
		$("#cmb_tipo_documento_t").addClass("borde_error");
		resultado = false;
	}
	if ($("#txt_numero_documento_t").val().length < 5) {
		$("#txt_numero_documento_t").addClass("borde_error");
		resultado = false;
	}
	if ($("#txt_numero_verificacion_t").val() == "" && $("#cmb_tipo_documento_t").val() == "146") {
		$("#txt_numero_verificacion_t").addClass("borde_error");
		resultado = false;
	}
	if ($("#tr_razon_social").is(":visible")) {
		if ($("#txt_nombre_tercero_t").val() == "") {
			$("#txt_nombre_tercero_t").addClass("borde_error");
			resultado = false;
		}
	} else {
		if ($("#txt_nombre_1_t").val() == "") {
			$("#txt_nombre_1_t").addClass("borde_error");
			resultado = false;
		}
		if ($("#txt_apellido_1_t").val() == "") {
			$("#txt_apellido_1_t").addClass("borde_error");
			resultado = false;
		}
	}
	if ($("#txt_email_tercero_t").val() == "" || !validar_email($("#txt_email_tercero_t").val())) {
		$("#txt_email_tercero_t").addClass("borde_error");
		resultado = false;
	}
	
	if(!$("#tr_obligaciones_terceros").is(":visible")){
		$("#cmb_obligaciones_terceros").val("");
		$("#cmb_detalles_tributarios_t").val("");
		
	}
	
	if($("#tr_obligaciones_terceros").is(":visible")){
		if($("#cmb_obligaciones_terceros").val() == "" || $("#cmb_detalles_tributarios_t").val() == ""){
			$("#cmb_obligaciones_terceros").addClass("borde_error");
			$("#cmb_detalles_tributarios_t").addClass("borde_error");
			resultado = false;	
		}
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
		$("#txt_numero_verificacion_t").addClass("borde_error");
    } else if (resultado == -4) {
        $("#d_contenedor_error_2").css("display", "block");
        $("#d_contenedor_error_2").html("Error - El n&uacute;mero de documento ya se encuentra registrado");
		$("#txt_numero_documento_t").addClass("borde_error");
    } else {
        $("#d_contenedor_error_2").css("display", "block");
        $("#d_contenedor_error_2").html("Error al tratar de guardar el tercero");
    }
}

function mostrar_buscar_paciente() {
	var params = "opcion=4";
	
	llamarAjax("terceros_ajax.php", params, "d_interno", "continuar_mostrar_buscar_paciente();");
}

function continuar_mostrar_buscar_paciente() {
	var numero_documento = $("#txt_numero_documento_t").val();
	if (numero_documento != "") {
		$("#txtParametroPacientes").val(numero_documento);
		continuar_buscar_pacientes_terceros();
	}
	muestraFormularioFlotanteTerceros(1);
}

function buscar_pacientes_terceros() {
    $("#frmListadoPacientes").validate({
        rules: {
            txtParametroPacientes: {
                required: true,
            },
        },
        submitHandler: function() {
            continuar_buscar_pacientes_terceros();

            return false;
        },
    });
}

function continuar_buscar_pacientes_terceros() {
	var parametro_pacientes = str_encode($("#txtParametroPacientes").val());
	
	var params = "opcion=5&parametro_pacientes=" + parametro_pacientes;
	llamarAjax("terceros_ajax.php", params, "d_resultado_b_pacientes", "");
}

function agregar_paciente(id_paciente, nombre_paciente) {
	$("#hdd_id_paciente_t").val(id_paciente);
	$("#d_nombre_paciente").html(nombre_paciente);
	
	//Se cierra el div
	muestraFormularioFlotanteTerceros(0);
}

function borrar_paciente() {
	$("#hdd_id_paciente_t").val("");
	$("#d_nombre_paciente").html("");
}

function muestraFormularioFlotanteTerceros(tipo) {
    if (tipo == 1) { //mostrar
        $("#fondo_negro").css("display", "block");
        $("#d_centro").slideDown(400).css("display", "block");
		
		posicionarDivFlotante("d_centro_servicios");
    } else if (tipo == 0) { //Ocultar
        $("#fondo_negro").css("display", "none");
        $("#d_centro").slideDown(400).css("display", "none");
    }
}

function seleccionar_tipo_doc(id_tipo_doc) {
	if (id_tipo_doc == "146") {
		$("#tr_razon_social").css("display", "table-row");
		$("#tr_nombre_completo").css("display", "none");
		$("#tr_obligaciones_terceros").css("display", "table-row");
		$("#txt_nombre_1_t").val("");
		$("#txt_nombre_2_t").val("");
		$("#txt_apellido_1_t").val("");
		$("#txt_apellido_2_t").val("");
		
	} else {
		$("#tr_razon_social").css("display", "none");
		$("#txt_nombre_tercero_t").val("");
		$("#tr_nombre_completo").css("display", "table-row");
		$("#tr_obligaciones_terceros").css("display", "none");
	}
}
