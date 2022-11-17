
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
			
			llamarAjax("acceso_resultados_ajax.php", params, "contenedor_paciente_hc", "");
			
			return false;
		},
	});
}



function ver_registros_hc(id_persona, nombre_persona, documento_persona, tipo_documento, telefonos, fecha_nacimiento, edad_paciente, clave_verificacion) {
	var params = 'opcion=2' +
				 '&id_persona=' + id_persona +
				 '&nombre_persona=' + nombre_persona +
				 '&documento_persona=' + documento_persona +
				 '&tipo_documento=' + tipo_documento +
				 '&telefonos=' + telefonos +
				 '&fecha_nacimiento=' + fecha_nacimiento +
				 '&edad_paciente=' + edad_paciente +
				 '&clave_verificacion=' + clave_verificacion;
	
	llamarAjax("acceso_resultados_ajax.php", params, "contenedor_paciente_hc", "");
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
	
	llamarAjax("acceso_resultados_ajax.php", params, "contenedor_paciente_hc", "");
	
}


//Metodo para habilitar paciente
function habilitar_paciente(id_paciente, clave_verificacion, nombre_persona, documento_persona){
	
	if(clave_verificacion == ''){
		
	}
	else{
		
	}
	
	
	var params = "opcion=3&id_paciente=" + id_paciente +
				 "&clave_verificacion=" + clave_verificacion +
				 "&nombre_persona=" + nombre_persona +
				 "&documento_persona=" + documento_persona;
				 
	llamarAjax("acceso_resultados_ajax.php", params, "contenedor_paciente_hc", "validar_exito()");			 
	
	
}


 


function validar_exito() {
    
    var resultado = parseInt($("#hdd_exito").val(), 10);
    var ruta = $("#hdd_ruta_arch_pdf").val();
    
    if (resultado > 0) {
		$("#contenedor_exito").css("display", "block");
		$("#contenedor_exito").html("Se genero correctamente");
		//$("#contenedor_paciente_hc").html("");
		//setTimeout(function () { $("#contenedor_exito").css("display", "none"); }, 2000);
		//window.scroll(0, 0);
		window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=formula.pdf", "_blank");
	} else if (resultado == -1) {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error interno");
		window.scroll(0, 0);
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error interno");
		window.scroll(0, 0);
	}
    
}

function imprimir_registro_resultados(id_hc) {
	var params = "id_hc=" + id_hc;
	llamarAjax("../historia_clinica/impresion_historia_clinica.php", params, "d_impresion_hc", "continuar_imprimir_registro_hc();");
}




