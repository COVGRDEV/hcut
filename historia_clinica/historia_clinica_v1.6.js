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
    if (text == '') {
        ind_existe = 1;//Si Existe
    }
    if (ind_existe == 0) {
        alert('Valor incorrecto');
        document.getElementById(id.id).value = "";
        input = id.id;
        setTimeout('document.getElementById(input).focus()', 75);
    }
}

function formato_hc(event, id) {
    var isIE = document.all ? true : false;
    var key = (isIE) ? window.event.keyCode : event.which;
    var obj = (isIE) ? window.event.srcElement : event.target;
    var isNum = true;
    if (key != 0 && key != 8) {
        if (isIE) {
            window.event.keyCode = (!isNum) ? 0 : key;
        } else if (!isNum) {
            event.preventDefault();
        }
    }
    
    var text = $(id).val();
    /*Para permitir solo coma*/
    var num = text.replace('.', ",");
    /*Para permitir solo una coma*/
    var textos = num.split(",");
    if (textos.length >= 3) {
        if (textos[0] == '') {
            num = textos[0] + textos[1] + textos[2];
        }
        else {
            num = textos[0] + ',' + textos[1] + textos[2];
        }
    }
    /*Para no pemitir como al incio*/
    if (textos.length >= 2) {
        if (textos[0] == '') {
            num = textos[0] + textos[1];
        }
    }

    //Para que el signa + se coloque en la posicion correcta
    var signo_mas = num.split("+");
    if (signo_mas.length >= 2) {

        var num = num.replace('-', "");

        if (signo_mas[0] != '') {
            num = signo_mas[0] + signo_mas[1];
        }
    }
    if (signo_mas.length >= 3) {
        if (signo_mas[0] == '') {
            num = signo_mas[0] + '+' + signo_mas[1] + signo_mas[2];
        }
    }
    //Para que el signo - se coloque en la posicion correcta
    var signo_men = num.split("-");
    if (signo_men.length >= 2) {

        var num = num.replace('+', "");

        if (signo_men[0] != '') {
            num = signo_men[0] + signo_men[1];
        }
    }
    if (signo_men.length >= 3) {
        if (signo_men[0] == '') {
            num = signo_men[0] + '-' + signo_men[1] + signo_men[2];
        }
    }
	
    $(id).val(num);
    
    return (isNum);
}

function validar_buscar_hc() {
    var result = 0;
    $('#txt_paciente_hc').removeClass("borde_error");
    if ($('#txt_paciente_hc').val() == '') {
        $('#txt_paciente_hc').addClass("borde_error");
        result = 1;
    }
    return result;
}

function validarBuscarPersonasHc() {
	$("#frm_historia_clinica").validate({
		rules: {
			txt_paciente_hc: {
				required: true,
			},
		},
		submitHandler: function() {
			var txt_paciente_hc = $('#txt_paciente_hc').val();
			var params = 'opcion=1' +
						 '&txt_paciente_hc=' + txt_paciente_hc;
			
			llamarAjax("historia_clinica_ajax.php", params, "contenedor_paciente_hc", "");
			
			return false;
		},
	});
}

function ver_registros_hc(id_persona, nombre_persona, documento_persona, tipo_documento, telefonos, fecha_nacimiento, edad_paciente) {
	var params = 'opcion=2' +
				 '&id_persona=' + id_persona +
				 '&nombre_persona=' + nombre_persona +
				 '&documento_persona=' + documento_persona +
				 '&tipo_documento=' + tipo_documento +
				 '&telefonos=' + telefonos +
				 '&fecha_nacimiento=' + fecha_nacimiento +
				 '&edad_paciente=' + edad_paciente;
	
	llamarAjax("historia_clinica_ajax.php", params, "contenedor_paciente_hc", "");
}

function mostrar_observaciones(id_div){
	if ($('#' + id_div).css('display') == 'block') {
		$('#' + id_div).slideUp(400).css('display', 'none');
		$('#' + id_div + '_ver').css('background-image', 'url("../imagenes/ver_derecha.png")');
	} else {
		$('#' + id_div).slideDown(400).css('display', 'block');
		$('#' + id_div + '_ver').css('background-image', 'url("../imagenes/ver_abajo.png")');
	}
}

function confirmar_borrar_registro_hc(id_hc) {
	$("#d_interno").html = "";
	$("#fondo_negro").css("display", "block");
	$("#d_centro").slideDown(400).css("display", "block");
	
	var params = "opcion=3&id_hc=" + id_hc;
	
	llamarAjax("historia_clinica_ajax.php", params, "d_interno", "");
}

function borrar_registro_hc(id_hc) {
	$("#txt_observaciones_hc_borrar").removeClass("borde_error");
	
	if (trim($("#txt_observaciones_hc_borrar").val()) != "") {
		var params = "opcion=4&id_hc=" + id_hc +
					 "&observaciones_hc=" + str_encode($("#txt_observaciones_hc_borrar").val());
		
		llamarAjax("historia_clinica_ajax.php", params, "d_guardar_hc", "finalizar_borrar_registro_hc();");
	} else {
		if ($("#txt_observaciones_hc_borrar").val() == "") {
			$("#txt_observaciones_hc_borrar").addClass("borde_error");
			$("#d_contenedor_error_borrar").css("display", "block");
			$("#d_contenedor_error_borrar").html("Los campos marcados en rojo son obligatorios");
		}
	}
}

function finalizar_borrar_registro_hc() {
	var resultado = parseInt($("#hdd_resultado_hc").val(), 10);
	
	if (resultado > 0) {
        $("#contenedor_exito").css("display", "block");
        $("#contenedor_exito").html('Registro borrado correctamente');
        setTimeout('$("#contenedor_exito").css("display", "none")', 2000);
		$("#frm_historia_clinica").submit();
		cerrar_div_centro();
	} else if (resultado == -1) {
        $("#d_contenedor_error_borrar").css("display", "block");
        $("#d_contenedor_error_borrar").html("Error al tratar de borrar el registro");
	} else if (resultado == -2) {
        $("#d_contenedor_error_borrar").css("display", "block");
        $("#d_contenedor_error_borrar").html("Error interno al tratar de borrar el registro");
	} else if (resultado == -3) {
        $("#d_contenedor_error_borrar").css("display", "block");
        $("#d_contenedor_error_borrar").html("No est&aacute; permitido borrar el tipo de registro seleccionado");
	}
}

function mostrar_hc_completa(id_paciente, credencial, menu) {
	mostrar_formulario_flotante_extend(1);
	$("#d_interno_extend").empty();
	$('#d_interno_extend').height("99%");
	
	var HcFrame = document.createElement("iframe");
	HcFrame.id = "HcFrame";    
	ruta = "../historia_clinica/historia_clinica_ajax.php?opcion=5" +
		   "&id_paciente=" + id_paciente +
		   "&credencial=" + credencial +
		   "&hdd_numero_menu=" + menu;
	
	HcFrame.setAttribute("src", ruta); 
	HcFrame.style.height='100%';
	HcFrame.style.width='99%';
	var control = document.getElementById("HcFrame")
	$("#d_interno_extend").append(HcFrame);
}

function imprimir_registro_hc(id_hc) {
	var params = "id_hc=" + id_hc;
	
	llamarAjax("impresion_historia_clinica.php", params, "d_impresion_hc", "continuar_imprimir_registro_hc();");
}


function continuar_imprimir_registro_hc() {
	var ruta = $("#hdd_ruta_arch_hc_pdf").val();
	window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=registro_hc.pdf", "_blank");
}

function imprimir_hc_completa(id_paciente) {
	$("#d_btn_impr_completa_1").css("display", "none");
	$("#d_btn_impr_completa_2").css("display", "block");
	
	var params = "id_paciente=" + id_paciente;
	
	var cant_tipos_reg_filtros = parseInt($("#hdd_cant_tipos_reg_filtros").val(), 10);
	var ind_tipo_reg_todos = $("#chk_tipo_reg_todos").is(":checked") ? 1 : 0;
	var cont_aux = 0;
	for (var i = 0; i < cant_tipos_reg_filtros; i++) {
		if ($("#chk_tipo_reg_" + i).is(":checked")) {
			params += "&id_tipo_reg_" + cont_aux + "=" + $("#hdd_tipo_reg_" + i).val();
			cont_aux++;
		}
	}
	params += "&ind_tipo_reg_todos=" + ind_tipo_reg_todos +
			  "&cant_tipos_reg_filtros=" + cont_aux;
	
	var cant_usuarios_prof_filtros = parseInt($("#hdd_cant_usuarios_prof_filtros").val(), 10);
	var ind_usuario_prof_todos = $("#chk_usuario_prof_todos").is(":checked") ? 1 : 0;
	cont_aux = 0;
	for (var i = 0; i < cant_usuarios_prof_filtros; i++) {
		if ($("#chk_usuario_prof_" + i).is(":checked")) {
			params += "&id_usuario_prof_" + cont_aux + "=" + $("#hdd_usuario_prof_" + i).val();
			cont_aux++;
		}
	}
	params += "&ind_usuario_prof_todos=" + ind_usuario_prof_todos +
			  "&cant_usuarios_prof_filtros=" + cont_aux;
	
	llamarAjax("impresion_historia_clinica.php", params, "d_impresion_hc", "continuar_imprimir_hc_completa();");
}

function continuar_imprimir_hc_completa() {
	var ruta = $("#hdd_ruta_arch_hc_pdf").val();
	window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=historia_clinica.pdf", "_blank");
	
	$("#d_btn_impr_completa_1").css("display", "block");
	$("#d_btn_impr_completa_2").css("display", "none");
}

function ver_imagenes_hc(id_paciente, credencial, id_menu) {
	mostrar_consultas_div(id_paciente, "", 0, "ver_imagenes_hc.php", 0, credencial, id_menu);
}

function abrir_cerrar_filtros_hc() {
	if ($("#d_filtros_hc").is(":visible")) {
		$("#d_filtros_hc").hide(200);
	} else {
		$("#d_filtros_hc").show(200);
	}
}

function seleccionar_fitro_tipo_reg(indice) {
	var cant_tipos_reg_filtros = parseInt($("#hdd_cant_tipos_reg_filtros").val(), 10);
	if (indice == "todos") {
		var ind_checked = $("#chk_tipo_reg_todos").is(":checked");
		for (var i = 0; i < cant_tipos_reg_filtros; i++) {
			$("#chk_tipo_reg_" + i).prop("checked", ind_checked);
		}
	} else {
		var ind_checked = true;
		for (var i = 0; i < cant_tipos_reg_filtros; i++) {
			if (!$("#chk_tipo_reg_" + i).is(":checked")) {
				ind_checked = false;
				break;
			}
		}
		$("#chk_tipo_reg_todos").prop("checked", ind_checked);
	}
	
	aplicar_filtros_hc();
}

function seleccionar_fitro_usuario_prof(indice) {
	var cant_usuarios_prof_filtros = parseInt($("#hdd_cant_usuarios_prof_filtros").val(), 10);
	if (indice == "todos") {
		var ind_checked = $("#chk_usuario_prof_todos").is(":checked");
		for (var i = 0; i < cant_usuarios_prof_filtros; i++) {
			$("#chk_usuario_prof_" + i).prop("checked", ind_checked);
		}
	} else {
		var ind_checked = true;
		for (var i = 0; i < cant_usuarios_prof_filtros; i++) {
			if (!$("#chk_usuario_prof_" + i).is(":checked")) {
				ind_checked = false;
				break;
			}
		}
		$("#chk_usuario_prof_todos").prop("checked", ind_checked);
	}
	
	aplicar_filtros_hc();
}

function aplicar_filtros_hc() {
	var cant_registros_hc = parseInt($("#hdd_cant_registros_hc").val(), 10);
	if ($("#chk_tipo_reg_todos").is(":checked") && $("#chk_usuario_prof_todos").is(":checked")) {
		//Todos visibles
		for (var i = 0; i < cant_registros_hc; i++) {
			$("#tr_registro_hc_" + i).css("display", "table-row");
		}
	} else {
		var cant_tipos_reg_filtros = parseInt($("#hdd_cant_tipos_reg_filtros").val(), 10);
		var cadena_tipos_reg = "#";
		for (var i = 0; i < cant_tipos_reg_filtros; i++) {
			if ($("#chk_tipo_reg_" + i).is(":checked")) {
				cadena_tipos_reg += $("#hdd_tipo_reg_" + i).val() + "#";
			}
		}
		var cant_usuarios_prof_filtros = parseInt($("#hdd_cant_usuarios_prof_filtros").val(), 10);
		var cadena_usuarios_prof = "#";
		for (var i = 0; i < cant_usuarios_prof_filtros; i++) {
			if ($("#chk_usuario_prof_" + i).is(":checked")) {
				cadena_usuarios_prof += $("#hdd_usuario_prof_" + i).val() + "#";
			}
		}
		
		var ind_tipo_reg_todos = $("#chk_tipo_reg_todos").is(":checked");
		var ind_usuario_prof_todos = $("#chk_usuario_prof_todos").is(":checked");
		for (var i = 0; i < cant_registros_hc; i++) {
			var id_tipo_reg_aux = $("#hdd_tipo_reg_hc_" + i).val();
			var id_usuario_prof_aux = $("#hdd_usuario_prof_hc_" + i).val();
			
			if ((ind_tipo_reg_todos || cadena_tipos_reg.indexOf("#" + id_tipo_reg_aux + "#") >= 0) &&
					(ind_usuario_prof_todos || cadena_usuarios_prof.indexOf("#" + id_usuario_prof_aux + "#") >= 0)) {
				$("#tr_registro_hc_" + i).css("display", "table-row");
			} else {
				$("#tr_registro_hc_" + i).css("display", "none");
			}
		}
	}
}
