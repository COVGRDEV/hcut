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
    //var isNum = (key > 42 && key < 47 || key > 47 && key < 58) ? true : false;
    //var dotOK = (key == 44 && decReq && (obj.value.indexOf(",") < 0 || obj.value.length == 0)) ? true : false;
    if (key != 0 && key != 8) {
        if (isIE) {
            //window.event.keyCode = (!isNum && !dotOK) ? 0 : key;
            window.event.keyCode = (!isNum) ? 0 : key;
            //} else if (!isNum && !dotOK) {
        } else if (!isNum) {
            event.preventDefault();
        }
    }
    //return (isNum || dotOK || key == 8 || key == 0);
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
    //return (isNum || key == 8 || key == 0);
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
