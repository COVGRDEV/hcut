
function valida_hora_campo(valor) {
  var tiempo = valor.split(":");
  if(tiempo.length != 3){
  	alert('Error en el formato de la Hora');
  	$('#txt_hora_encuesta').val('');
  }
  var hora = tiempo[0];
  var minutos = tiempo[1];
  var jornada = tiempo[2];
}



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
			
			llamarAjax("atencion_postqx_ajax.php", params, "contenedor_paciente_hc", "");
			
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
	
	llamarAjax("atencion_postqx_ajax.php", params, "contenedor_paciente_hc", "");
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


function vincular_paciente(id_paciente, nombre_paciente, id_seguimiento){
	
	
	var params = 'opcion=3' +
				 '&id_paciente=' + id_paciente +
				 '&nombre_paciente=' + nombre_paciente +
				 '&id_seguimiento=' + id_seguimiento;
	
	llamarAjax("atencion_postqx_ajax.php", params, "contenedor_paciente_hc", "");
	
}



function validar_vincular_form() {
    var result = 0;
    
    $('#txt_hora_encuesta').removeClass("borde_error");
    $('#txt_fecha_primer').removeClass("borde_error");
    $('#txt_fecha_segundo').removeClass("borde_error");
    $('#cmb_ojos').removeClass("borde_error");
    $('#cmb_sino_voz').removeClass("borde_error");
    $('#cmb_sino_excluye').removeClass("borde_error");
    
    var primera_fecha = $('#txt_fecha_primer').val().split("/");
	var segunda_fecha = $('#txt_fecha_segundo').val().split("/");
	
	var dia_primero = primera_fecha[0];
	var mes_primero = primera_fecha[1];
	var ano_primero = primera_fecha[2];
	
	var dia_segundo = segunda_fecha[0];
	var mes_segundo = segunda_fecha[1];
	var ano_segundo = segunda_fecha[2];
	
	fecha_primera=new Date(ano_primero,mes_primero,dia_primero);
	fecha_segunda=new Date(ano_segundo,mes_segundo,dia_segundo);
	
	
	
	if ($('#txt_hora_encuesta').val() == '') {
        $('#txt_hora_encuesta').addClass("borde_error");
        result = 1;
    }
	
	if ($('#txt_fecha_primer').val() == '') {
        $('#txt_fecha_primer').addClass("borde_error");
        result = 1;
    }
    if (fecha_primera >= fecha_segunda) {
        $('#txt_fecha_segundo').addClass("borde_error");
        $('#txt_fecha_primer').addClass("borde_error");
        result = 2;
    }
    
    if ($('#txt_fecha_primer').val() == '') {
        $('#txt_fecha_primer').addClass("borde_error");
        result = 1;
    }
    
    if ($('#cmb_ojos').val() == 0) {
        $('#cmb_ojos').addClass("borde_error");
        result = 1;
    }
    if ($('#cmb_sino_voz').val() == 0) {
        $('#cmb_sino_voz').addClass("borde_error");
        result = 1;
    }
    if ($('#cmb_sino_excluye').val() == 0) {
        $('#cmb_sino_excluye').addClass("borde_error");
        result = 1;
    }
    
    return result;
}


 /*
  * 1=Guardar
  * 2=Actualizar 
  */
function validar_vincular_paciente(tipo) {
		
	$("#contenedor_error").css("display", "none");
	if(validar_vincular_form() == 0) {
		guardar_vincular_paciente(tipo);
		return false;
	} else if(validar_vincular_form() == 1) {
		$("#contenedor_error").css("display", "block");
		$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
		window.scroll(0, 0);
		return false;
	} else if(validar_vincular_form() == 2) {
		$("#contenedor_error").css("display", "block");
		$('#contenedor_error').html('La primera fecha debe ser menor a la segunda fecha');
		window.scroll(0, 0);
		return false;
	}
	
}

/*
  * 1=Guardar
  * 2=Actualizar 
  */
function guardar_vincular_paciente(tipo){
	
	var tiempo = $('#txt_hora_encuesta').val().split(":");
	var hora = tiempo[0];
	var minutos = tiempo[1];
	var jornada = tiempo[2];
	if(jornada == 'pm'){
		hora = parseInt(hora) + 12;
	}
	
	
	caracteres_hora = hora.length;
	
	if(caracteres_hora<2) {
	    hora='0'+hora
	} 
	var valor_hora = hora+':'+minutos+':00'; 
	
	
	var hdd_id_paciente = $('#hdd_id_paciente').val();
	var hdd_id_seguimiento = $('#hdd_id_seguimiento').val();
	var txt_fecha_primer = $('#txt_fecha_primer').val()+' '+valor_hora;
	var txt_fecha_segundo = $('#txt_fecha_segundo').val()+' '+valor_hora;
	var txt_fecha_tercer = $('#txt_fecha_tercer').val();
	var cmb_ojos = $('#cmb_ojos').val();
	var cmb_sino_voz = $('#cmb_sino_voz').val();
	var cmb_sino_excluye = $('#cmb_sino_excluye').val();
	
	
	var params = 'opcion=4' + 
				 '&hdd_id_paciente=' + hdd_id_paciente +
				 '&hdd_id_seguimiento=' + hdd_id_seguimiento +
				 '&txt_fecha_primer=' + txt_fecha_primer +
				 '&txt_fecha_segundo=' + txt_fecha_segundo +
				 '&txt_fecha_tercer=' + txt_fecha_tercer +
				 '&cmb_ojos=' + cmb_ojos +
				 '&cmb_sino_voz=' + cmb_sino_voz +
				 '&cmb_sino_excluye=' + cmb_sino_excluye;
				 
	llamarAjax("atencion_postqx_ajax.php", params, "guardar_vincular_paciente", "validar_exito()");
				 
}



function validar_exito() {
    
    var resultado = parseInt($("#hdd_exito").val(), 10);
    if (resultado > 0) {
		$("#contenedor_exito").css("display", "block");
		$("#contenedor_exito").html("Seguimiento guardado con &eacute;xito");
		$("#contenedor_paciente_hc").html("");
		setTimeout(function () { $("#contenedor_exito").css("display", "none"); }, 2000);
		window.scroll(0, 0);
	} else if (resultado == -1) {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error interno al guardar los datos del Seguimiento");
		window.scroll(0, 0);
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error al guardar los datos del Seguimiento");
		window.scroll(0, 0);
	}
    
}


function mostrar_foto(src_foto){
	
	//<img src="<?php echo $valor_imagen; ?>" height="100" width="100">
	$("#d_interno").html("");
	mostrar_formulario_flotante(1);
	jQuery("<img />", {
			id: "photo",
			height: "100%",
			width: "100%",
			src: src_foto
	}).appendTo("#d_interno");
	
	
	
	
	//d_interno
	
	
	
}




