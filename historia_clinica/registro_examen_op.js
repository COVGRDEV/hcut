function mostrar_formulario_flotante(tipo){
	if(tipo==1){//mostrar
		$('#fondo_negro').css('display', 'block');
		$('#d_centro').slideDown(400).css('display', 'block');
	}
	else if(tipo==0){//Ocultar
		$('#fondo_negro').css('display', 'none');
		$('#d_centro').slideDown(400).css('display', 'none');
	}
}

function reducir_formulario_flotante(ancho, alto){ //480, 390
	$('.div_centro').width(ancho);
	$('.div_centro').height(alto);
	$('.div_centro').css('top', '20%');
	$('.div_interno').width(ancho-15);
	$('.div_interno').height(alto-35);
}

function validar_array(array, id){
	var text = $(id).val();
	var ind_existe = 0;//No existe
	for(var i=0;i<array.length;i++){
		if(text == array[i]){
			ind_existe = 1;//Si Existe
			break;  
		}
	}
	if(text == ''){
		ind_existe = 1;//Si Existe
	}
	if(ind_existe == 0){
		alert('Valor incorrecto');
		document.getElementById(id.id).value="";
		input = id.id;
		setTimeout('document.getElementById(input).focus()',75); 
	}
}

function mostrar_formulario(tipo) {
    if (tipo == 1) {//mostrar
        $('.formulario').slideDown(600).css('display', 'block')
    }
    else if (tipo == 0) {//Ocultar
        $('.formulario').slideUp(600).css('display', 'none')
    }
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta SIN VALIDAR LOS CAMPOS
 */
function guardar_examen(tipo) {
	
	switch (tipo) 
	{ 
		case 1:
			$("#contenedor_error").css("display", "none");
			if (validar_examenes()) {
				editar_examen_optometria(tipo);
			} else {
				$("#contenedor_error").css("display", "block");
				$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
				window.scrollTo(0,0);
			}
		break;
		case 2:
			limpiar_bordes_examenes();
			if (validar_archivos_examenes()) {
				editar_examen_optometria(tipo);
			}
		break;
		case 3:
			$("#contenedor_error").css("display", "none");
			if (validar_examenes()) {
				editar_examen_optometria(tipo);
			} else {
				$("#contenedor_error").css("display", "block");
				$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
				window.scrollTo(0,0);
			}
		break;
		alert('Error tipo de guardado');
	}
	
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta 
 */
function editar_examen_optometria(tipo){
	var cant_aux = parseInt($('#hdd_cant_examenes_op').val(), 10);
	
	var params = "opcion=1&id_hc=" + $("#hdd_id_hc_consulta").val() +
				 "&id_admision=" + $("#hdd_id_admision").val() +
				 "&tipo_guardar=" + tipo +
				 "&cant_examenes=" + cant_aux;
	
	for (var i = 0; i < cant_aux; i++) {
		var ruta_arch_examen = obtener_ruta_valida(i);
		params += "&id_examen_" + i + "=" + $("#cmb_examen_" + i).val() +
				  "&id_ojo_examen_" + i + "=" + $("#cmb_ojo_examen_" + i).val() +
				  "&ruta_arch_examen_" + i + "=" + str_encode(ruta_arch_examen) +
				  "&observaciones_examen_" + i + "=" + str_encode($("#txt_observaciones_examen_" + i).val());
	}
	
	llamarAjax("registro_examen_op_ajax.php", params, "d_guardar_examen", "validar_exito()");
}

function validar_exito() {
	var hdd_exito = $('#hdd_exito').val();
	var hdd_url_menu = $('#hdd_url_menu').val();
	var hdd_tipo_guardar = $('#hdd_tipo_guardar').val();
	
	if (hdd_tipo_guardar == 1) { //Cierra el formulario
		if (hdd_exito > 0) {
			$("#contenedor_exito").css("display", "block");
			$('#contenedor_exito').html('Datos guardados correctamente');
			subir_archivos(false);
			setTimeout("enviar_credencial('" + hdd_url_menu + "')", 1000);
		} else {
			$("#contenedor_error").css("display", "block");
			$('#contenedor_error').html('Error al guardar el examen');
		}
	} else if (hdd_tipo_guardar == 2) { //Permanece en el formulario
		if (hdd_exito > 0) {
			$("#contenedor_exito").css("display", "block");
			$('#contenedor_exito').html('Datos guardados correctamente');
			subir_archivos(true);
			setTimeout('$("#contenedor_exito").css("display", "none")', 1000);
		} else {
			$("#contenedor_error").css("display", "block");
			$('#contenedor_error').html('Error al guardar el examen');
		}
	} else if (hdd_tipo_guardar == 3) { //Permanece en el formulario
		if (hdd_exito > 0) {
			$("#contenedor_exito").css("display", "block");
			$('#contenedor_exito').html('Datos guardados correctamente');
			subir_archivos(true);
			setTimeout('$("#contenedor_exito").css("display", "none")', 1000);
		} else {
			$("#contenedor_error").css("display", "block");
			$('#contenedor_error').html('Error al guardar el examen');
		}
	}
}
