function mostrar_formulario_flotante(tipo) {
    if (tipo == 1) { //mostrar
        $("#fondo_negro").css("display", "block");
        $("#d_centro").slideDown(400).css("display", "block");
    } else if (tipo == 0) { //Ocultar
        $("#fondo_negro").css("display", "none");
        $("#d_centro").slideDown(400).css("display", "none");
    }
}

function reducir_formulario_flotante(ancho, alto) {
    $(".div_centro").width(ancho);
    $(".div_centro").css("top", "20%");
    $(".div_interno").width(ancho/*-15*/);
}

function mostrar_formulario(tipo) {
    if (tipo == 1) { //mostrar
        $(".formulario").slideDown(600).css("display", "block");
    } else if (tipo == 0) { //Ocultar
        $(".formulario").slideUp(600).css("display", "none");
    }
}

function ver_todos_usuarios() {
    $("#txt_busca_usuario").val("");
    
    var params = "opcion=1&txt_busca_usuario=" + $("#txt_busca_usuario").val();
	
    llamarAjax("usuarios_ajax.php", params, "principal_usuarios", "mostrar_formulario(1)");
}

function buscar_usuarios() {
    $("#listado_usuarios").validate({
        rules: {
            txt_busca_usuario: {
                required: true,
            }
        },
        submitHandler: function() {
            var txt_busca_usuario = $("#txt_busca_usuario").val();
            var params = "opcion=1&txt_busca_usuario=" + txt_busca_usuario;
            llamarAjax("usuarios_ajax.php", params, "principal_usuarios", "mostrar_formulario(1)");
            return false;
        },
    });
}

function llamar_crear_usuarios() {
    var params = "opcion=2&txt_busca_usuario=" + txt_busca_usuario;
	
    llamarAjax("usuarios_ajax.php", params, "principal_usuarios", "mostrar_formulario(1)");
}

function validar_usuario_existente(nombre) {
    nombre = $(nombre).val();
    var params = "opcion=3&nombre_usuario=" + nombre;
	
    llamarAjax("usuarios_ajax.php", params, "div_usuario_existe", "");
}

function validar_documento_existente(documento, tipo, id_usuario) {
    documento = $(documento).val();
    if (tipo == 1) {
        var params = "opcion=5&documento_usuario=" + documento + "&tipo=" + tipo + "&id_usuario=" + id_usuario;
		
        llamarAjax("usuarios_ajax.php", params, "div_documento_existe", "");
    } else if (tipo == 2) {
        var params = "opcion=5&documento_usuario=" + documento + "&tipo=" + tipo + "&id_usuario=" + id_usuario;
		
        llamarAjax("usuarios_ajax.php", params, "div_documento_existe", "");
    }
}

jQuery.validator.addMethod(
	"usuarioexistente",
	function(elementValue, element, param) {
		var validar_existe_usuario = $("#hdd_usuario_existe").val();
		if (validar_existe_usuario == "true") {
			return true;
		} else {
			return false;
		}
	},
	"El usuario ya esxite"
);

jQuery.validator.addMethod(
	"documentoexistente",
	function(elementValue, element, param) {
		var validar_existe_documento = $("#hdd_documento_existe").val();
		if (validar_existe_documento == "true") {
			return true;
		} else {
			return false;
		}
	},
	"El Documento ya esxite"
);

function validar_crear_usuarios() {
	var result = 0;
	$("#contenedor_error").css("display", "none");
	
	$("#txt_nombre_usuario").removeClass("borde_error");
	$("#txt_apellido_usuario").removeClass("borde_error");
	$("#cmb_tipo_documento").removeClass("borde_error");
	$("#txt_numero_documento").removeClass("borde_error");
	$("#txt_usuario").removeClass("borde_error");
	$("#txt_clave").removeClass("borde_error");
	
	if (trim($("#txt_nombre_usuario").val()) == "") {
		$("#txt_nombre_usuario").addClass("borde_error");
		result = 1;
	}
	if (trim($("#txt_apellido_usuario").val()) == "") {
		$("#txt_apellido_usuario").addClass("borde_error");
		result = 1;
	}
	if ($("#cmb_tipo_documento").val() == "") {
		$("#cmb_tipo_documento").addClass("borde_error");
		result = 1;
	}
	if (trim($("#txt_numero_documento").val()) == "") {
		$("#txt_numero_documento").addClass("borde_error");
		result = 1;
	}
	if (trim($("#txt_usuario").val()) == "") {
		$("#txt_usuario").addClass("borde_error");
		result = 1;
	}
	if (trim($("#txt_clave").val()) == "") {
		$("#txt_clave").addClass("borde_error");
		result = 1;
	}
	
	if (result == 0) {
		var categorias = new Array();
		$("input[name='check_pefiles']:checked").each(function() {
			categorias.push($(this).val());
		});
		
		if (categorias == "") {
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Debe seleccionar al menos un perfil.");
			window.scroll(0, 0);
		} else {
			crear_usuarios();
		}
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Los campos marcados en rojo son obligatorios.");
		window.scroll(0, 0);
	}
}

function validar_editar_usuarios() {
	var result = 0;
	$("#contenedor_error").css("display", "none");
	
	$("#txt_nombre_usuario").removeClass("borde_error");
	$("#txt_apellido_usuario").removeClass("borde_error");
	$("#cmb_tipo_documento").removeClass("borde_error");
	$("#txt_numero_documento").removeClass("borde_error");
	$("#txt_usuario").removeClass("borde_error");
	$("#txt_clave").removeClass("borde_error");
	
	if (trim($("#txt_nombre_usuario").val()) == "") {
		$("#txt_nombre_usuario").addClass("borde_error");
		result = 1;
	}
	if (trim($("#txt_apellido_usuario").val()) == "") {
		$("#txt_apellido_usuario").addClass("borde_error");
		result = 1;
	}
	if ($("#cmb_tipo_documento").val() == "") {
		$("#cmb_tipo_documento").addClass("borde_error");
		result = 1;
	}
	if (trim($("#txt_numero_documento").val()) == "") {
		$("#txt_numero_documento").addClass("borde_error");
		result = 1;
	}
	
	if (result == 0) {
		var categorias_editar = new Array();
		$("input[name='check_pefiles']:checked").each(function() {
			categorias_editar.push($(this).val());
		});
		
		if (categorias_editar == "") {
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Debe seleccionar al menos un perfil.");
			window.scroll(0, 0);
		} else {
			mostrar_formulario_flotante(1);
			reducir_formulario_flotante(400, 250);
			posicionarDivFlotante("d_centro");
			confirmar_guardar();
		}
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Los campos marcados en rojo son obligatorios.");
		window.scroll(0, 0);
	}
}

/*
 * Tipo
 * 1=crear
 * 2=editar
 */
function validar_exito() {
    var hdd_exito = $("#hdd_exito").val();
    $(".formulario").css("display", "none")
    if (hdd_exito > 0) {
        $("#contenedor_exito").css("display", "block");
        $("#contenedor_exito").html("Datos guardados correctamente");
		window.scroll(0, 0);
        setTimeout(
			function () {
				$("#contenedor_exito").slideUp(200).css("display", "none");
			}, 2000);
		volver_inicio();
    } else {
        $("#contenedor_error").css("display", "block");
        $("#contenedor_error").html("Error al guardar usuarios");
		window.scroll(0, 0);
		setTimeout('$("#contenedor_error").slideUp(200).css("display", "none")', 3000);
    }
}

function volver_inicio() {
    //Si hay algo en la casilla de buscar usuarios busca por esta opcion
    if ($("#txt_busca_usuario").val() != "") {
        $("#listado_usuarios").submit();
    } else {//De lo contrario muestra todos los usuarios
        ver_todos_usuarios();
    }
}

function crear_usuarios() {
    var perfiles_usuario = new Array();
    $("input[name='check_pefiles']:checked").each(function() {
		perfiles_usuario.push($(this).val());
    });
	
	$("#opcion").val(4);
	$("#hdd_nombre_usuario").val($("#txt_nombre_usuario").val());
	$("#hdd_apellido_usuario").val($("#txt_apellido_usuario").val());
	$("#hdd_tipo_documento").val($("#cmb_tipo_documento").val());
	$("#hdd_numero_documento").val($("#txt_numero_documento").val());
	$("#hdd_id_usuario_firma").val($("#cmb_usuario_firma").val());
	$("#hdd_tipo_num_reg").val($("#cmb_tipo_num_reg").val());
	$("#hdd_num_reg_medico").val($("#txt_num_reg_medico").val());
	$("#hdd_ind_autoriza").val($("#check_autoriza").is(":checked") ? 1 : 0);
	$("#hdd_login_usuario").val($("#txt_usuario").val());
	$("#hdd_clave_usuario").val($("#txt_clave").val());
	$("#hdd_ind_anonimo").val($("#check_anonimo").is(":checked") ? 1 : 0);
	$("#hdd_lista_perfiles").val(perfiles_usuario);
	
	$("#frm_registro_usuarios").submit();
}

function editar_usuarios() {
    var perfiles_usuario = new Array();
    $("input[name='check_pefiles']:checked").each(function() {
		perfiles_usuario.push($(this).val());
    });
	
	$("#opcion").val(6);
	$("#hdd_id_usuario_edit").val($("#hdd_id_usuario").val());
	$("#hdd_nombre_usuario").val($("#txt_nombre_usuario").val());
	$("#hdd_apellido_usuario").val($("#txt_apellido_usuario").val());
	$("#hdd_tipo_documento").val($("#cmb_tipo_documento").val());
	$("#hdd_numero_documento").val($("#txt_numero_documento").val());
	$("#hdd_id_usuario_firma").val($("#cmb_usuario_firma").val());
	$("#hdd_tipo_num_reg").val($("#cmb_tipo_num_reg").val());
	$("#hdd_num_reg_medico").val($("#txt_num_reg_medico").val());
	$("#hdd_ind_autoriza").val($("#check_autoriza").is(":checked") ? 1 : 0);
	$("#hdd_ind_anonimo").val($("#check_anonimo").is(":checked") ? 1 : 0);
	$("#hdd_ind_estado").val($("#check_estado").is(":checked") ? 1 : 0);
	$("#hdd_lista_perfiles").val(perfiles_usuario);
	
	$("#frm_registro_usuarios").submit();
}

function seleccionar_usuarios(id_usuario) {
	var params = "opcion=2&id_usuario=" + id_usuario;
	
	llamarAjax("usuarios_ajax.php", params, "principal_usuarios", "mostrar_formulario(1);");
}

function confirmar_guardar() {
	$("#d_interno").html(
		'<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
			'<tr>' +
				'<th align="center">' +
					'<h4>&iquest;Est&aacute; seguro de guardar esta informaci&oacute;n?</h4>' +
				'</th>' +
			'</tr>' +
			'<tr>' +
				'<th align="center">' +
					'<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" class="btnPrincipal" value="Aceptar" onclick="editar_usuarios();"/>\n' +
					'<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" class="btnPrincipal" value="Cancelar" onclick="cerrar_div_centro();"/> ' +
				'</th>' +
			'</tr>' +
		'</table>');
}

//Resetea la contraseña
function resetear_pass(usuario) {
    if (confirm("La contrase\u00f1a del usuario: " + usuario + " sera reemplazada por: " + usuario + " ¿Realmente desea realizar la acci\u00f3n?")) {
        var id_usuario = $("#hdd_id_usuario").val();
        var params = "opcion=7&id_usuario=" + id_usuario;
        llamarAjax("usuarios_ajax.php", params, "rtaResetearPass", "postResetearPass();");
    }
}

//Funcion que verifica que la funcion: resetear_pass, se ejecute sin problemas
function postResetearPass() {
    var rtaResetearPass = $("#rtaResetearPass").text();
    if (rtaResetearPass == "1") {
        $("#contenedor_exito").css({"display": "block"});
        $("#contenedor_exito").html("La contrase\u00f1a ha sido reseteada");
    } else {
        $("#contenedor_error").css({"display": "block"});
        $("#contenedor_error").html("Error al intentar resetear la contrase\u00f1a. Vuelve a intentarlo");
    }
}
