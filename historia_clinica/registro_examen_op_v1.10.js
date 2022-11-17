function mostrar_formulario_flotante(tipo) {
	if (tipo == 1) { //mostrar
		$("#fondo_negro").css("display", "block");
		$("#d_centro").slideDown(400).css("display", "block");
	} else if (tipo == 0) { //Ocultar
		$("#fondo_negro").css("display", "none");
		$("#d_centro").slideDown(400).css("display", "none");
	}
}

function reducir_formulario_flotante(ancho, alto){ //480, 390
	$(".div_centro").width(ancho);
	$(".div_centro").height(alto);
	$(".div_centro").css("top", "20%");
	$(".div_interno").width(ancho-15);
	$(".div_interno").height(alto-35);
}

function validar_array(array, id) {
	var text = $(id).val();
	var ind_existe = 0;//No existe
	for (var i = 0; i < array.length; i++) {
		if(text == array[i]){
			ind_existe = 1;//Si Existe
			break;  
		}
	}
	if (text == "") {
		ind_existe = 1;//Si Existe
	}
	if (ind_existe == 0) {
		alert("Valor incorrecto");
		document.getElementById(id.id).value="";
		input = id.id;
		setTimeout("document.getElementById(input).focus()", 75); 
	}
}

function mostrar_formulario(tipo) {
    if (tipo == 1) {//mostrar
        $(".formulario").slideDown(600).css("display", "block")
    }
    else if (tipo == 0) {//Ocultar
        $(".formulario").slideUp(600).css("display", "none")
    }
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta SIN VALIDAR LOS CAMPOS
 */
function guardar_examen(tipo, ind_imprimir) {
	limpiar_bordes_examenes();
	switch (tipo) { 
		case 1: //Finalizar consulta
		case 3: //Guardar desde historia
			$("#contenedor_error").css("display", "none");
			var resul_validar = validar_examen_op(tipo);
			if (resul_validar == 1) {
				editar_examen_optometria(tipo, ind_imprimir);
			} else if (resul_validar == -1) {
				$("#contenedor_error").css("display", "block");
				$("#contenedor_error").html("Los archivos adjuntos deben guardarse antes de finalizar los ex&aacute;menes, por favor oprima el bot&oacute;n 'Guardar cambios'");
				window.scrollTo(0, 0);
			} else if (resul_validar == -2) {
				$("#contenedor_error").css("display", "block");
				$("#contenedor_error").html("Error - existen diagn&oacute;sticos duplicados");
				window.scrollTo(0, 0);
			} else {
				$("#contenedor_error").css("display", "block");
				$("#contenedor_error").html("Los campos marcados en rojo son obligatorios");
				window.scrollTo(0, 0);
			}
			break;
			
		case 2: //Guardar cambios
			//Se validan duplicados de diagnósticos
			if (validar_duplicados_diagnosticos_hc() != -2) {
				editar_examen_optometria(tipo, ind_imprimir);
			} else {
				$("#contenedor_error").css("display", "block");
				$("#contenedor_error").html("Error - existen diagn&oacute;sticos duplicados");
				window.scroll(0, 0);
			}
			break;
	}
}

function validar_examen_op(tipo) {
	var resultado = 1;
	panel_1 = 0;
	panel_2 = 0;
	
	$("#panel_oft_1").removeClass("borde_error_panel");
	$("#panel_oft_1 a").css({"color": "#5B5B5B"});
	$("#panel_oft_2").removeClass("borde_error_panel");
	$("#panel_oft_2 a").css({"color": "#5B5B5B"});
	
	$("#txt_nombre_usuario_alt").removeClass("borde_error");
	
	if ($("#hdd_usuario_anonimo").val() == "1" && $("#txt_nombre_usuario_alt").val() == ""){
		$("#txt_nombre_usuario_alt").addClass("borde_error");
		resultado = 0;
	}
	
	//Validación de diagnósticos
	var result_ciex = validar_diagnosticos_hc(0);
	if (result_ciex < 0) {
		if (result_ciex != -2) {
			result_ciex = -3;
		}
		resultado = result_ciex;
		
		panel_2 = 1;
	}
	
	var resul_examenes = validar_examenes(tipo);
	if (resul_examenes <= 0) {
		panel_1 = 1;
	   
		resultado = resul_examenes;
	}
	
	if (panel_1 == 1) {
	   $("#panel_oft_1").addClass("borde_error_panel");
	   $("#panel_oft_1 a").css({"color": "#FF002A"});
	}
	if (panel_2 == 1) {
	   $("#panel_oft_2").addClass("borde_error_panel");
	   $("#panel_oft_2 a").css({"color": "#FF002A"});
	}
	
	return resultado;
}

function imprimir_examen() {
	$("#btn_imprimir").attr("disabled", "disabled");
	$("#d_img_espera_examen").css("display", "block");
	
	var params = "id_hc=" + $("#hdd_id_hc_examen").val();
	
	llamarAjax("../historia_clinica/impresion_historia_clinica.php", params, "d_impresion_hc", "continuar_imprimir_examen();");
}

function continuar_imprimir_examen() {
	var ruta = $("#hdd_ruta_arch_hc_pdf").val();
	window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=examenes.pdf", "_blank");
	
	$("#d_img_espera_examen").css("display", "none");
	$("#btn_imprimir").removeAttr("disabled");
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta 
 */
function editar_examen_optometria(tipo, ind_imprimir) {
	var cant_aux = parseInt($("#hdd_cant_examenes_op").val(), 10);
	
	var params = "opcion=1&id_hc=" + $("#hdd_id_hc_consulta").val() +
				 "&id_admision=" + $("#hdd_id_admision").val() +
				 "&nombre_usuario_alt=" + str_encode($("#txt_nombre_usuario_alt").val()) +
				 "&tipo_guardar=" + tipo +
				 "&cant_examenes=" + cant_aux;
	
	for (var i = 0; i < cant_aux; i++) {
		params += "&id_examen_" + i + "=" + $("#cmb_examen_" + i).val() +
				  "&id_ojo_examen_" + i + "=" + $("#cmb_ojo_examen_" + i).val() +
				  "&observaciones_examen_" + i + "=" + str_encode(eval("CKEDITOR.instances.txt_observaciones_examen_" + i + ".getData()")) +
				  "&id_examen_hc_" + i + "=" + $("#hdd_id_examen_hc_" + i).val() +
				  "&pu_od_" + i + "=" + $("#txt_pu_od_" + i).val() +
				  "&pu_oi_" + i + "=" + $("#txt_pu_oi_" + i).val();
		
		params += obtener_params_archivos_examen(i);
	}
	
	//Para Diagnosticos
	var cant_ciex = $("#lista_tabla").val();
	params += "&cant_ciex=" + cant_ciex;
	for (i = 1; i <= cant_ciex; i++) {
	 	 var cod_ciex = $("#hdd_ciex_diagnostico_"+i).val();
	 	 var val_ojos = $("#valor_ojos_"+i).val();
	 	 if (cod_ciex != "") {
	 	 	params += "&cod_ciex_" + i + "=" + cod_ciex + "&val_ojos_" + i + "=" + val_ojos;
	 	 }
	}
	
	llamarAjax("registro_examen_op_ajax.php", params, "d_guardar_examen", "validar_exito(" + ind_imprimir + ");");
}

function validar_exito(ind_imprimir) {
	var hdd_exito = $("#hdd_exito").val();
	var hdd_url_menu = $("#hdd_url_menu").val();
	var hdd_tipo_guardar = $("#hdd_tipo_guardar").val();
	
	if (hdd_tipo_guardar == 1) { //Cierra el formulario
		if (hdd_exito > 0) {
			$("#contenedor_exito").css("display", "block");
			$("#contenedor_exito").html("Datos guardados correctamente");
			subir_archivos(false);
			setTimeout("enviar_credencial('" + hdd_url_menu + "')", 5000);
		} else {
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Error al guardar el examen");
		}
	} else if (hdd_tipo_guardar == 2) { //Permanece en el formulario
		if (hdd_exito > 0) {
			$("#contenedor_exito").css("display", "block");
			$("#contenedor_exito").html("Datos guardados correctamente");
			subir_archivos(true);
			setTimeout('$("#contenedor_exito").css("display", "none")', 3000);
			
			if (ind_imprimir == 1) {
				imprimir_examen();
			}
		} else {
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Error al guardar el examen");
		}
	} else if (hdd_tipo_guardar == 3) { //Permanece en el formulario
		if (hdd_exito > 0) {
			$("#contenedor_exito").css("display", "block");
			$("#contenedor_exito").html("Datos guardados correctamente");
			subir_archivos(true);
			setTimeout('$("#contenedor_exito").css("display", "none")', 3000);
			
			if (ind_imprimir == 1) {
				imprimir_examen();
			}
		} else {
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Error al guardar el examen");
		}
	}
	window.scrollTo(0, 0);
}
