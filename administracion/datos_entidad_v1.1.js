$(document).ready(function() {
    //Carga el listado de convenios
    mostrar_datos_entidad();
});

//Carga los datos de la tabla: datos_entidad
function mostrar_datos_entidad() {
	var params = 'opcion=1';
	llamarAjax("datos_entidad_ajax.php", params, "d_principal", "");
}

//Muestra la venatan flotante de agregar Planes
function mostrar_ventana_modificar(tipo, id_prestador) {
	var params = "opcion=2&id_prestador=" + id_prestador + "&tipo=" + tipo;
	
	llamarAjax("datos_entidad_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1);");
}

function modificar_datos_entidad() {
	if (validar_datos_entidad()) {
		var params = "opcion=3&id_prestador=" + $("#hdd_id_prestador").val() +
					 "&cod_prestador=" + str_encode($("#txt_cod_prestador").val()) +
					 "&nombre_prestador=" + str_encode($("#txt_nombre_prestador").val()) +
					 "&sigla_prestador=" + str_encode($("#txt_sigla_prestador").val()) +
					 "&id_tipo_documento=" + $("#cmb_tipo_documento").val() +
					 "&numero_documento=" + str_encode($("#txt_numero_documento").val());
		
		llamarAjax("datos_entidad_ajax.php", params, "d_guardar_datos_entidad", "finalizar_modificar_datos_entidad();");
	} else {
		$("#contenedor_error2").css("display", "block");
		$('#contenedor_error2').html('Los campos marcados en rojo son obligatorios');
	}
}

function validar_datos_entidad() {
	var resultado = true;
	
	$("#contenedor_error2").css("display", "none");
	$("#txt_cod_prestador").removeClass("bordeAdmision");
	$("#txt_nombre_prestador").removeClass("bordeAdmision");
	$("#txt_sigla_prestador").removeClass("bordeAdmision");
	$("#cmb_tipo_documento").removeClass("bordeAdmision");
	$("#txt_numero_documento").removeClass("bordeAdmision");
	
	if ($("#txt_cod_prestador").val() == "") {
		$("#txt_cod_prestador").addClass("bordeAdmision");
		resultado = false;
	}
	if ($("#txt_nombre_prestador").val() == "") {
		$("#txt_nombre_prestador").addClass("bordeAdmision");
		resultado = false;
	}
	if ($("#txt_sigla_prestador").val() == "") {
		$("#txt_sigla_prestador").addClass("bordeAdmision");
		resultado = false;
	}
	if ($("#cmb_tipo_documento").val() == "") {
		$("#cmb_tipo_documento").addClass("bordeAdmision");
		resultado = false;
	}
	if ($("#txt_numero_documento").val() == "") {
		$("#txt_numero_documento").addClass("bordeAdmision");
		resultado = false;
	}
	
	return resultado;
}

function finalizar_modificar_datos_entidad() {
	var resultado = parseInt($('#hdd_resul_guardar').val(), 10);
	
	if (resultado > 0) {
		mostrar_formulario_flotante(0);
		mostrar_datos_entidad();
		$('#contenedor_exito').css('display', 'block');
		$('#contenedor_exito').html('Registro guardado con \xe9xito');
	} else if (resultado == '-1') {
		$('#contenedor_error').css('display', 'block');
		$('#contenedor_error').html('Error al guardar el registro');
	}
}

function iniciar_crear_datos_entidad() {
	mostrar_ventana_modificar(1, 0);
}
