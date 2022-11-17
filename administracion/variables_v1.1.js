function mostrar_formulario(tipo) {
    if (tipo == 1) {//mostrar
        $('.formulario').slideDown(600).css('display', 'block')
    }
    else if (tipo == 0) {//Ocultar
        $('.formulario').slideUp(600).css('display', 'none')
    }
}

function cargar_variables() {
	 var params = 'opcion=1';
    llamarAjax("variables_ajax.php", params, "principal_variables", "mostrar_formulario(1)");
}

function cargar_formulario_editar(id_variable) {
	var params = 'opcion=2&id_variable=' + id_variable;
	llamarAjax("variables_ajax.php", params, "principal_variables", "mostrar_formulario(1)");
}


function mostrar_formulario_flotante(tipo) {
	if(tipo==1){//mostrar
		$('#fondo_negro').css('display', 'block');
		$('#d_centro').slideDown(400).css('display', 'block');
	}
	else if(tipo==0){//Ocultar
		$('#fondo_negro').css('display', 'none');
		$('#d_centro').slideDown(400).css('display', 'none');
	}
}

function reducir_formulario_flotante(ancho, alto) {
	$('.div_centro').width(ancho);
	$('.div_centro').css('top', '20%');
	$('.div_interno').width(ancho/*-15*/);
}

function seleccionar_tipo_variable(tipo_variable) {
	if (tipo_variable != "2") {
		document.getElementById("cmb_cantidad_variable").value = "1";
		document.getElementById("cmb_cantidad_variable").disabled = true;
		for (var i = 1; i < 20; i++) {
			document.getElementById("txt_valor_variable_" + i).value = "";
			document.getElementById("txt_valor_variable_" + i).style.display = "none";
		}
	} else {
		document.getElementById("cmb_cantidad_variable").disabled = false;
	}
}

function seleccionar_cantidad_variables(cantidad) {
	
	for (var i = 0; i < cantidad && i < 20; i++) {
		document.getElementById("txt_valor_variable_" + i).style.display = "inline";
	}
	for (var i = cantidad; i < 20; i++) {
		document.getElementById("txt_valor_variable_" + i).value = "";
		document.getElementById("txt_valor_variable_" + i).style.display = "none";
	}
	
	if(cantidad == 1){
		document.getElementById("txt_valor_variable_" + 0).style.width = "800px";
	}
	else{
		document.getElementById("txt_valor_variable_" + 0).style.width = "150px";
	}
	
	
}

function validar_editar_variable() {
	$("#frm_variables").validate({
        rules: {
            txt_nombre_variable: {
                required: true,
            },
            txt_descripcion_variable: {
                required: true,
            }
        },
        submitHandler: function() {
			if (document.getElementById("cmb_tipo_variable").value == "") {
				$("#contenedor_error").css("display", "block");
                $('#contenedor_error').html('Debe seleccionar el tipo de variable');
                return false;
			}
			if (document.getElementById("cmb_cantidad_variable").value == "") {
				$("#contenedor_error").css("display", "block");
                $('#contenedor_error').html('Debe seleccionar la cantidad de valores de la variable');
                return false;
			}
			
			/*for (var i = 0; i < 20; i++) {
				if (document.getElementById("txt_valor_variable_" + i).style.display != "none") {
					if (trim(document.getElementById("txt_valor_variable_" + i).value) == "") {
						$("#contenedor_error").css("display", "block");
    	    	        $('#contenedor_error').html('Debe ingresar el valor de variable n&uacute;mero ' + (i + 1));
                		return false;
					}
				}
			}*/
        	
			mostrar_formulario_flotante(1);
            reducir_formulario_flotante(400, 250);
			posicionarDivFlotante('d_centro');
            confirmar_guardar();
        },
    });
}

function confirmar_guardar() {
	$('#d_interno').html(
		'<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
			'<tr>' +
				'<th align="center">' +
					'<h4>&iquest;Est&aacute; seguro de guardar esta informaci&oacute;n?</h4>' +
				'</th>' +
			'</tr>' +
			'<tr>' +
				'<th align="center">' +
					'<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" class="btnPrincipal" value="Aceptar" onclick="editar_variable();"/>\n' +
					'<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" class="btnSecundario" value="Cancelar" onclick="cerrar_div_centro();"/>' +
				'</th>' +
			'</tr>' +
		'</table>');
}

function editar_variable() {
	var id_variable = $('#hdd_id_variable').val();
    
    var params = "opcion=3&id_variable=" + id_variable +
	             "&nombre_variable=" + $("#txt_nombre_variable").val() +
	             "&descripcion_variable=" + $("#txt_descripcion_variable").val() +
	             "&tipo_variable=" + $("#cmb_tipo_variable").val() +
	             "&cantidad_variable=" + $("#cmb_cantidad_variable").val();
	for (var i = 0; i < 20; i++) {
		params += "&valor_variable_" + i + "=" + $("#txt_valor_variable_" + i).val();
	}
	
	llamarAjax("variables_ajax.php", params, "principal_variables", "cerrar_div_centro(); validar_exito();");
}

function validar_exito() {
	var hdd_exito = $('#hdd_exito').val();
	if (hdd_exito > 0) {
		$("#contenedor_exito").css("display", "block");
		$('#contenedor_exito').html('Datos guardados correctamente');
		setTimeout("$('#contenedor_exito').slideUp(200).css('display', 'none')", 5000);
		setTimeout(cargar_variables(), 5000);
	} else {
		$("#contenedor_error").css("display", "block");
		$('#contenedor_error').html('Error al guardar la variable');
		setTimeout("$('#contenedor_error').slideUp(200).css('display', 'none')", 5000);
		setTimeout(cargar_variables(), 5000);
	}
}
