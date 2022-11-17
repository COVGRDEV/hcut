function foco(id_elemento) {
	document.getElementById(id_elemento).focus();
}

function validar_array(array, id) {
	var text = $(id).val();
	var ind_existe = 0;//No existe
	for (var i = 0; i < array.length; i++) {
		if (text == array[i]) {
			ind_existe = 1;//Si Existe
			break;
		}
	}
	if (text == "") {
		ind_existe = 1;//Si Existe
	}
	if (ind_existe == 0) {
		alert("Valor incorrecto");
		document.getElementById(id.id).value = "";
		input = id.id;
		setTimeout("document.getElementById(input).focus()", 75);
	}
}

function formato_hc(event, id) {
	var isIE = document.all ? true : false;
	var key = (isIE) ? window.event.keyCode : event.which;
	var obj = (isIE) ? window.event.srcElement : event.target;
	var isNum = (key > 42 && key < 47 || key > 47 && key < 58) ? true : false;
	if (key != 0 && key != 8) {
		if (isIE) {
			window.event.keyCode = (!isNum) ? 0 : key;
		} else if (!isNum) {
			event.preventDefault();
		}
	}
	
	var text = $(id).val();
	/*Para permitir solo coma*/
	var num = text.replace(".", ",");
	/*Para permitir solo una coma*/
	var textos = num.split(",");
	if (textos.length >= 3) {
		if (textos[0] == "") {
			num = textos[0] + textos[1] + textos[2];
		} else {
			num = textos[0] + "," + textos[1] + textos[2];
		}
	}
	/*Para no pemitir como al incio*/
	if (textos.length >= 2) {
		if (textos[0] == "") {
			num = textos[0] + textos[1];
		}
	}
	
	//Para que el signa + se coloque en la posicion correcta
	var signo_mas = num.split("+");
	if (signo_mas.length >= 2) {
		var num = num.replace("-", "");
		
		if (signo_mas[0] != "") {
			num = signo_mas[0] + signo_mas[1];
		}
	}
	if (signo_mas.length >= 3) {
		if (signo_mas[0] == "") {
			num = signo_mas[0] + "+" + signo_mas[1] + signo_mas[2];
		}
	}
	//Para que el signo - se coloque en la posicion correcta
	var signo_men = num.split("-");
	if (signo_men.length >= 2) {
		var num = num.replace("+", "");
		
		if (signo_men[0] != "") {
			num = signo_men[0] + signo_men[1];
		}
	}
	if (signo_men.length >= 3) {
		if (signo_men[0] == "") {
			num = signo_men[0] + "-" + signo_men[1] + signo_men[2];
		}
	}
	
	$(id).val(num);
	return (isNum || key == 8 || key == 0);
}

function validar_buscar_hc() {
	var result = 0;
	$("#txt_paciente_hc").removeClass("borde_error");
	if ($("#txt_paciente_hc").val() == "") {
		$("#txt_paciente_hc").addClass("borde_error");
		result = 1;
	}
	return result;
}

function validar_buscar_personas_hc() {
	$("#frm_historia_clinica").validate({
		rules: {
			txt_paciente_hc: {
				required: true,
			},
		},
		submitHandler: function() {
			var params = "opcion=1&txt_paciente_hc=" + $("#txt_paciente_hc").val();
			
			llamarAjax("importar_hc_ajax.php", params, "contenedor_paciente_hc", "");
			return false;
		},
	});
}

function cargar_formulario_crear_hc(id_paciente) {
	var params = "opcion=2&id_paciente=" + id_paciente;
	
	llamarAjax("importar_hc_ajax.php", params, "contenedor_paciente_hc", "");
}

function marcar_checkbox(id) {
	$("input:checkbox").attr("checked", false);
	$("input[type=checkbox]").attr("checked", false);
	$("#" + id).prop("checked", "checked");
}

function filtrar_archivos() {
	$("input:checkbox").attr("checked", false);
	$("input[type=checkbox]").attr("checked", false);
	
	var texto = trim($("#txt_archivo_hc").val()).toUpperCase();
	var cant_registros = parseInt($("#hdd_cant_registros").val(), 0);
	for (var i = 0; i <= cant_registros; i++) {
		if ($("#hdd_con_datos_" + i).val() == "1") {
			var ind_hallado_aux = false;
			if (texto != "") {
				var nombre_completo_aux = $("#td_nombre_"+i).html().toUpperCase();
				if (nombre_completo_aux.indexOf(texto) >= 0) {
					ind_hallado_aux = true;
				}
			} else {
				ind_hallado_aux = true;
			}
			
			if (ind_hallado_aux) {
				$("#tr_archivos_"+i).css("display", "table-row");
			} else {
				$("#tr_archivos_"+i).css("display", "none");
			}
		}
	}
}

function crear_hc() {
	$("#btn_crear_hc").attr("disabled", "disabled");
	var ind_validar = validar_crear_hc();
	switch (ind_validar) {
		case 0:
			editar_crear_hc_fisica();
			break;
		case 1:
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Los campos marcados en rojo son obligatorios.");
			$("#btn_crear_hc").removeAttr("disabled");
			window.scrollTo(0, 0);
			break;
		case 2:
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Debe seleccionar al menos un archivo de Historia Cl&iacute;nica.");
			$("#btn_crear_hc").removeAttr("disabled");
			window.scrollTo(0, 0);
			break;
		case 3:
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Los campos marcados en rojo son obligatorios.<br />Debe seleccionar al menos un archivo de Historia Cl&iacute;nica.");
			$("#btn_crear_hc").removeAttr("disabled");
			window.scrollTo(0, 0);
			break;
		case 4:
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("El archivo seleccionado debe ser un PDF.");
			$("#btn_crear_hc").removeAttr("disabled");
			window.scrollTo(0, 0);
			break;
		case 5:
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Los campos marcados en rojo son obligatorios.<br />El archivo seleccionado debe ser un PDF.");
			$("#btn_crear_hc").removeAttr("disabled");
			window.scrollTo(0, 0);
			break;
		default:
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Se ha presentado un error con c&oacute;digo " + ind_validar + ".");
			$("#btn_crear_hc").removeAttr("disabled");
			window.scrollTo(0, 0);
			break;
	}
}

function validar_crear_hc() {
	var result = 0;
	
	$("#cmb_tipo_id").removeClass("borde_error");
	$("#txt_id").removeClass("borde_error");
	$("#cmb_sexo").removeClass("borde_error");
	$("#txt_nombre").removeClass("borde_error");
	$("#txt_apellido").removeClass("borde_error");
	$("#fil_hc_antigua").removeClass("borde_error");
	
	if ($("#cmb_tipo_id").val() == "") {
		$("#cmb_tipo_id").addClass("borde_error");
		result = 1;
	}
	if ($("#txt_id").val() == "") {
		$("#txt_id").addClass("borde_error");
		result = 1;
	}
	if ($("#cmb_sexo").val() == "") {
		$("#cmb_sexo").addClass("borde_error");
		result = 1;
	}
	if ($("#txt_nombre").val() == "") {
		$("#txt_nombre").addClass("borde_error");
		result = 1;
	}
	if ($("#txt_apellido").val() == "") {
		$("#txt_apellido").addClass("borde_error");
		result = 1;
	}
	
	var cant_registros = 0;
	if ($("#hdd_cant_registros").length) {
		cant_registros = parseInt($("#hdd_cant_registros").val(), 10);
	}
	var ind_cheked = 0;
	for (var i = 0; i < cant_registros; i++) {
		if ($("#chk_archivo_hc_" + i).is(":checked")) {
			ind_cheked = 1;
			break;
		}
	}
	if (ind_cheked == 0 && ($("#fil_hc_antigua").val() == "" || !$("#fil_hc_antigua").length)) {
		result += 2;
	} else {
		var ruta_archivo_aux = $("#fil_hc_antigua").val();
		if (ruta_archivo_aux != "") {
			var extension = obtener_extension_archivo(ruta_archivo_aux);
			if (extension != "pdf") {
				$("#fil_hc_antigua").addClass("borde_error");
				result += 4;
			}
		}
	}
	
	return result;
}

function editar_crear_hc_fisica() {
	var cant_registros = parseInt($("#hdd_cant_registros").val(), 10);
	var nombre_archivo = "";
	for (var i = 0; i < cant_registros; i++) {
		if ($("#chk_archivo_hc_" + i).is(":checked")){
			nombre_archivo = str_encode($("#chk_archivo_hc_" + i).val());
			break;
		}
	}
	
	var params = "opcion=3&id_paciente=" + $("#hdd_id_paciente").val() +
				 "&id_tipo_documento=" + str_encode($("#cmb_tipo_id").val()) +
				 "&numero_documento=" + str_encode($("#txt_id").val()) +
				 "&sexo=" + str_encode($("#cmb_sexo").val()) +
				 "&nombre_1=" + str_encode($("#txt_nombre").val()) +
				 "&nombre_2=" + str_encode($("#txt_nombre2").val()) +
				 "&apellido_1=" + str_encode($("#txt_apellido").val()) +
				 "&apellido_2=" + str_encode($("#txt_apellido2").val()) +
				 "&nombre_archivo=" + nombre_archivo;
	
	llamarAjaxUploadFiles("importar_hc_ajax.php", params, "div_importar_historia_clinica", "validar_exito_importar()", "d_barra_progreso", "fil_hc_antigua");
}

function validar_exito_importar() {
	var hdd_exito = $("#hdd_exito").val();
	var hdd_url_menu = $("#hdd_url_menu").val();
	
	$(".frm_historia_clinica").css("display", "none");
	if (hdd_exito > 0) {
		$("#contenedor_exito").css("display", "block");
		$("#contenedor_exito").html("Datos guardados correctamente");
		setTimeout("enviar_credencial('" + hdd_url_menu + "')", 3000);
	} else if (hdd_exito == "-2") {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error al crear el paciente, por favor verifique si el paciente ya fue creado en atenci&oacute;n");
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error al guardar usuarios");
	}
	
	$("#btn_crear_hc").removeAttr("disabled");
	window.scrollTo(0, 0);
}

function buscar_historias_ant() {
	$("#contenedor_error").css("display", "none");
	$("#txt_archivo_hc").removeClass("borde_error");
	
	if ($("#txt_archivo_hc").val() != "") {
		var params = "opcion=4&txt_archivo_hc=" + str_encode($("#txt_archivo_hc").val());
		
		llamarAjax("importar_hc_ajax.php", params, "d_buscar_hc_ant", "");
	} else {
		$("#txt_archivo_hc").addClass("borde_error");
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Los campos marcados en rojo son obligatorios.");
	}
}
