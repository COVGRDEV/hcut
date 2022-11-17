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
    var isNum = (key > 42 && key < 47 || key > 47 && key < 58) ? true : false;
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

    //var num = formato_numero(text, 2, ',', '.')
    $(id).val(num);
    return (isNum || key == 8 || key == 0);
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
            llamarAjax("importar_hc_ajax.php", params, "contenedor_paciente_hc", "");

            return false;
        },
    });
   
}



function form_crear_hc(id_persona){
	var params = 'opcion=2&id_persona='+id_persona;
    llamarAjax("importar_hc_ajax.php", params, "contenedor_paciente_hc", "");
}

function marcar_checkbox(id){
	$("input:checkbox").attr('checked', false);
	$("input[type=checkbox]").attr('checked', false);
	$('#'+id).prop("checked", "checked");
}


function filtrar_archivos(){
	
	$("input:checkbox").attr('checked', false);
	$("input[type=checkbox]").attr('checked', false);
	
	var texto = trim($("#txt_archivo_hc").val()).toUpperCase();
	var cant_registros = parseInt($("#hdd_cant_registros").val(), 0);
	for (var i = 0; i < cant_registros+1; i++) {
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

function crear_hc(){
	var ind_validar = validar_crear_hc();
	if(ind_validar == 0){
		editar_crear_hc_fisica();
		return false;
	} else if(ind_validar == 1){
		$("#contenedor_error").css("display", "block");
    	$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
		return false;
	} else if(ind_validar == 2){
		$("#contenedor_error").css("display", "block");
    	$('#contenedor_error').html('Los campos marcados en rojo son obligatorios <br /> Debe seleccionar al menos un archivo de Historia Cl&iacute;nica');
		return false;
	}
}

function validar_crear_hc() {
	var result = 0;
	
	$("#cmb_tipo_id").removeClass("borde_error");
	$("#txt_id").removeClass("borde_error");
	$("#txt_nombre").removeClass("borde_error");
	$("#txt_apellido").removeClass("borde_error");
	
	if($('#cmb_tipo_id').val()==''){ $("#cmb_tipo_id").addClass("borde_error"); result=1;}
	if($('#txt_id').val()==''){ $("#txt_id").addClass("borde_error"); result=1;}
	if($('#txt_nombre').val()==''){ $("#txt_nombre").addClass("borde_error"); result=1;}
	if($('#txt_apellido').val()==''){ $("#txt_apellido").addClass("borde_error"); result=1;}
	
	var cant_registros = parseInt($("#hdd_cant_registros").val(), 0);
	var ind_cheked = 0;
	for (var i = 1; i < cant_registros+1; i++) {
		if ($('#archivo_hc_'+i).is(':checked')){
			ind_cheked = 1;
			break;
		}
	}
	if(ind_cheked == 0){
		result=2;
	}
	return result;
}

function editar_crear_hc_fisica() {
	var hdd_id_paciente = $('#hdd_id_paciente').val();
	var cmb_tipo_id = str_encode($('#cmb_tipo_id').val());
	var txt_id = str_encode($('#txt_id').val());
	var txt_nombre = str_encode($('#txt_nombre').val());
	var txt_nombre2 = str_encode($('#txt_nombre2').val());
	var txt_apellido = str_encode($('#txt_apellido').val());
	var txt_apellido2 = str_encode($('#txt_apellido2').val());
	
	var cant_registros = parseInt($("#hdd_cant_registros").val(), 0);
	for (var i = 1; i < cant_registros+1; i++) {
		if ($('#archivo_hc_'+i).is(':checked')){
			var nombre_archivo = str_encode($('#archivo_hc_'+i).val());
			break;
		}
	}
	
	var params = 'opcion=3&id_persona='+hdd_id_paciente+'&tipo_identificacion='+cmb_tipo_id+'&identificacion='+txt_id+
				 '&txt_nombre='+txt_nombre+'&txt_nombre2='+txt_nombre2+'&txt_apellido='+txt_apellido+'&txt_apellido2='+txt_apellido2+'&nombre_archivo='+nombre_archivo;
    llamarAjax("importar_hc_ajax.php", params, "div_importar_historia_clinica", "validar_exito_importar()");
}

function validar_exito_importar(){
	var hdd_exito = $('#hdd_exito').val();
    var hdd_url_menu = $('#hdd_url_menu').val();
    
	$('.frm_historia_clinica').css('display', 'none');
    if (hdd_exito > 0) {
        $("#contenedor_exito").css("display", "block");
        $('#contenedor_exito').html('Datos guardados correctamente');
        setTimeout("enviar_credencial('" + hdd_url_menu + "')", 3000);
    } else if (hdd_exito == "-2") {
        $("#contenedor_error").css("display", "block");
        $('#contenedor_error').html('Error al crear el paciente, por favor verifique si el paciente ya fue creado en atenci&oacute;n');
	} else {
        $("#contenedor_error").css("display", "block");
        $('#contenedor_error').html('Error al guardar usuarios');
    }
    
	window.scrollTo(0, 0);
}
