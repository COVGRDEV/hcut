//Funcion que busca un convenio especifico
function buscar_lista_espera() {
	if (validar_buscar_lista_espera()) {
		var params = "opcion=1&parametro=" + str_encode($("#txt_parametro").val()) +
					 "&id_tipo_lista=" + $("#cmb_tipo_lista").val();
		
		llamarAjax("listas_espera_ajax.php", params, "principal_espera", "");
	}
}

function validar_buscar_lista_espera() {
	var resultado = true;
	$("#contenedor_error").css("display", "none");
	$("#txt_parametro").removeClass("bordeAdmision");
	$("#cmb_tipo_lista").removeClass("bordeAdmision");
	
    if ($("#txt_parametro").val() == "" && $("#cmb_tipo_lista").val() == "") {
		$("#txt_parametro").addClass("bordeAdmision");
		$("#cmb_tipo_lista").addClass("bordeAdmision");
		resultado = false;
	}
	
	if (!resultado) {
		$('#contenedor_error').html("Debe seleccionar por lo menos un valor");
		$("#contenedor_error").css("display", "block");
	}
	
	return resultado;
}

function agregar_nueva_espera() {
    var params = "opcion=2&accion=1&id_reg_lista=";
	
    llamarAjax("listas_espera_ajax.php", params, "principal_espera", "");
}

function seleccionar_espera(id_reg_lista) {
    var params = "opcion=2&accion=2&id_reg_lista=" + id_reg_lista;
	
    llamarAjax("listas_espera_ajax.php", params, "principal_espera", "");
}

function limpiar_bordes() {
    $("#txt_nombre_1").css({"border": "1px solid rgba(0,0,0,.2)"});
    $("#txt_apellido_1").css({"border": "1px solid rgba(0,0,0,.2)"});
    $("#cmb_tipo_documento").css({"border": "1px solid rgba(0,0,0,.2)"});
    $("#txt_numero_documento").css({"border": "1px solid rgba(0,0,0,.2)"});
    $("#txt_fecha_lista").css({"border": "1px solid rgba(0,0,0,.2)"});
    $("#cmb_tipo_cirugia").css({"border": "1px solid rgba(0,0,0,.2)"});
    $("#txt_telefono_contacto").css({"border": "1px solid rgba(0,0,0,.2)"});
}

function validar_lista_espera() {
	var resultado = true;
	limpiar_bordes();
	$("#contenedor_error").css("display", "none");
	
    if ($("#cmb_tipo_documento").val() == "") {
		$("#cmb_tipo_documento").css({"border": "2px solid #FF002A"});
		resultado = false;
	}
    if ($("#txt_numero_documento").val() == "") {
		$("#txt_numero_documento").css({"border": "2px solid #FF002A"});
		resultado = false;
	}
    if ($("#txt_nombre_1").val() == "") {
		$("#txt_nombre_1").css({"border": "2px solid #FF002A"});
		resultado = false;
	}
    if ($("#txt_apellido_1").val() == "") {
		$("#txt_apellido_1").css({"border": "2px solid #FF002A"});
		resultado = false;
	}
    if ($("#txt_fecha_lista").val() == "") {
		$("#txt_fecha_lista").css({"border": "2px solid #FF002A"});
		resultado = false;
	}
    if ($("#cmb_tipo_cirugia").val() == "") {
		$("#cmb_tipo_cirugia").css({"border": "2px solid #FF002A"});
		resultado = false;
	}
    if ($("#txt_telefono_contacto").val() == "") {
		$("#txt_telefono_contacto").css({"border": "2px solid #FF002A"});
		resultado = false;
	}
	
	if (!resultado) {
		$("#contenedor_error").css("display", "block");
		$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
		window.scrollTo(0, 0);
	}
	
	return resultado;
}

function modificar_lista_espera() {
	if (validar_lista_espera()) {
		var params = "opcion=3&id_reg_lista=" + $("#hdd_reg_lista").val() +
					 "&id_paciente=" + $("#hdd_paciente").val() +
					 "&id_tipo_documento=" + $("#cmb_tipo_documento").val() +
					 "&numero_documento=" + str_encode($("#txt_numero_documento").val()) +
					 "&nombre_1=" + str_encode($("#txt_nombre_1").val()) +
					 "&nombre_2=" + str_encode($("#txt_nombre_2").val()) +
					 "&apellido_1=" + str_encode($("#txt_apellido_1").val()) +
					 "&apellido_2=" + str_encode($("#txt_apellido_2").val()) +
					 "&fecha_lista=" + $("#txt_fecha_lista").val() +
					 "&id_tipo_cirugia=" + $("#cmb_tipo_cirugia").val() +
					 "&telefono_contacto=" + str_encode($("#txt_telefono_contacto").val());
		
		llamarAjax("listas_espera_ajax.php", params, "d_guardar_lista", "verificar_guardar_lista_espera();");
	}
}

function crear_lista_espera() {
	if (validar_lista_espera()) {
		var params = "opcion=4&id_paciente=" + $("#hdd_paciente").val() +
					 "&id_tipo_documento=" + $("#cmb_tipo_documento").val() +
					 "&numero_documento=" + str_encode($("#txt_numero_documento").val()) +
					 "&nombre_1=" + str_encode($("#txt_nombre_1").val()) +
					 "&nombre_2=" + str_encode($("#txt_nombre_2").val()) +
					 "&apellido_1=" + str_encode($("#txt_apellido_1").val()) +
					 "&apellido_2=" + str_encode($("#txt_apellido_2").val()) +
					 "&fecha_lista=" + $("#txt_fecha_lista").val() +
					 "&id_tipo_cirugia=" + $("#cmb_tipo_cirugia").val() +
					 "&telefono_contacto=" + str_encode($("#txt_telefono_contacto").val());
		
		llamarAjax("listas_espera_ajax.php", params, "d_guardar_lista", "verificar_guardar_lista_espera();");
	}
}

function verificar_guardar_lista_espera() {
	var resultado = $("#hdd_resul_guardar_lista").val();
	
	if (resultado > 0) {
		$("#contenedor_exito").css("display", "block");
		$("#contenedor_exito").html("Registro guardado con &eacute;xito");
		
        setTimeout(function() { $("#contenedor_exito").css("display", "none"); buscar_lista_espera(); }, 2000);
	} else if (resultado == "-1") {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error al guardar el registro");
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error interno al guardar el registro");
	}
}

function buscar_paciente() {
	var id_tipo_documento = $("#cmb_tipo_documento").val();
	var numero_documento = $("#txt_numero_documento").val();
	
	if (id_tipo_documento != "" && numero_documento != "") {
		var params = "opcion=5&id_tipo_documento=" + id_tipo_documento +
					 "&numero_documento=" + numero_documento;
		
		llamarAjax("listas_espera_ajax.php", params, "d_interno", "verificar_buscar_paciente();");
	}
}

function verificar_buscar_paciente() {
	if ($("#hdd_paciente_b").val() != "") {
		$("#fondo_negro").css("display", "block");
		$("#d_centro").slideDown(400).css("display", "block");
	}
}

function cargar_datos_paciente() {
	$("#hdd_paciente").val($("#hdd_paciente_b").val());
	$("#cmb_tipo_documento").val($("#hdd_tipo_documento_b").val());
	$("#txt_nombre_1").val($("#hdd_nombre_1_b").val());
	$("#txt_nombre_2").val($("#hdd_nombre_2_b").val());
	$("#txt_apellido_1").val($("#hdd_apellido_1_b").val());
	$("#txt_apellido_2").val($("#hdd_apellido_2_b").val());
	$("#txt_telefono_contacto").val($("#hdd_telefono_contacto_b").val());
	
	cerrar_div_centro();
}

function borrar_lista_espera(id_reg_lista) {
	if (confirm("\xbfEst\xe1 seguro de querer borrar el registro?")) {
		var params = "opcion=6&id_reg_lista=" + id_reg_lista;
		
		llamarAjax("listas_espera_ajax.php", params, "d_guardar_lista", "verificar_borrar_lista_espera();");
	}
}

function verificar_borrar_lista_espera() {
	var resultado = $("#hdd_resul_borrar_lista").val();
	
	if (resultado > 0) {
		$("#contenedor_exito").css("display", "block");
		$("#contenedor_exito").html("Registro borrado con &eacute;xito");
		
        setTimeout(function() { $("#contenedor_exito").css("display", "none"); buscar_lista_espera(); }, 2000);
	} else if (resultado == "-1") {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error al borrar el registro");
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error interno al borrar el registro");
	}
}

function exportar_excel_lista() {
	var parametro = $("#txt_parametro").val();
	var id_tipo_lista = $("#cmb_tipo_lista").val();
	
	if (parametro != "" || id_tipo_lista != "") {
		$("#hdd_parametro_e").val(parametro);
		$("#hdd_tipo_lista_e").val(id_tipo_lista);
		
		//Enviar los datos del formulario
		if (isObject(document.getElementById("frm_reporte_espera"))) {
			document.getElementById("frm_reporte_espera").submit();
		}
	}
}
