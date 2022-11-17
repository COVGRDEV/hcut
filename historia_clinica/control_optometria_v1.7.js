/***********************************************/
/*Configuración del editor de texto enriquecido*/
/***********************************************/

if (CKEDITOR.env.ie && CKEDITOR.env.version < 9) {
	CKEDITOR.tools.enableHtml5Elements(document);
}

CKEDITOR.config.height = 70;
CKEDITOR.config.width = 'auto';

var initCKEditorAnamnesis = (function() {
	var wysiwygareaAvailable = isWysiwygareaAvailable(),
		isBBCodeBuiltIn = !!CKEDITOR.plugins.get('bbcode');
	
	return function() {
		var editorElement = CKEDITOR.document.getById('txt_anamnesis');
		
		//Dependiendo de la disponibilidad del plugin wysiwygare se incia el editor clásico o el editor en línea.
		if (wysiwygareaAvailable) {
			CKEDITOR.replace('txt_anamnesis');
		} else {
			editorElement.setAttribute('contenteditable', 'true');
			CKEDITOR.inline('txt_anamnesis');
		}
	};
	
	function isWysiwygareaAvailable() {
		if (CKEDITOR.revision == ('%RE' + 'V%')) {
			return true;
		}
		
		return !!CKEDITOR.plugins.get('wysiwygarea');
	}
} )();

var initCKEditorSubjetivo = (function() {
	var wysiwygareaAvailable = isWysiwygareaAvailable(),
		isBBCodeBuiltIn = !!CKEDITOR.plugins.get('bbcode');
	
	return function() {
		var editorElement = CKEDITOR.document.getById('txt_observaciones_subjetivo');
		
		//Dependiendo de la disponibilidad del plugin wysiwygare se incia el editor clásico o el editor en línea.
		if (wysiwygareaAvailable) {
			CKEDITOR.replace('txt_observaciones_subjetivo');
		} else {
			editorElement.setAttribute('contenteditable', 'true');
			CKEDITOR.inline('txt_observaciones_subjetivo');
		}
	};
	
	function isWysiwygareaAvailable() {
		if (CKEDITOR.revision == ('%RE' + 'V%')) {
			return true;
		}
		
		return !!CKEDITOR.plugins.get('wysiwygarea');
	}
} )();

var initCKEditorDiagOpt = (function() {
	var wysiwygareaAvailable = isWysiwygareaAvailable(),
		isBBCodeBuiltIn = !!CKEDITOR.plugins.get('bbcode');
	
	return function() {
		var editorElement = CKEDITOR.document.getById('txt_diagnostico_optometria');
		
		//Dependiendo de la disponibilidad del plugin wysiwygare se incia el editor clásico o el editor en línea.
		if (wysiwygareaAvailable) {
			CKEDITOR.replace('txt_diagnostico_optometria');
		} else {
			editorElement.setAttribute('contenteditable', 'true');
			CKEDITOR.inline('txt_diagnostico_optometria');
		}
	};
	
	function isWysiwygareaAvailable() {
		if (CKEDITOR.revision == ('%RE' + 'V%')) {
			return true;
		}
		
		return !!CKEDITOR.plugins.get('wysiwygarea');
	}
} )();

/***********************************************/
/***********************************************/
/***********************************************/

var g_querato_np = "NP/NA";

function mostrar_formulario_flotante(tipo){
	if (tipo == 1) { //mostrar
		$('#fondo_negro').css('display', 'block');
		$('#d_centro').slideDown(400).css('display', 'block');
	} else if (tipo == 0) { //Ocultar
		$('#fondo_negro').css('display', 'none');
		$('#d_centro').slideDown(400).css('display', 'none');
	}
}

function reducir_formulario_flotante(ancho, alto) {
	$('.div_centro').width(ancho);
	$('.div_centro').height(alto);
	$('.div_centro').css('top', '20%');
	$('.div_interno').width(ancho - 15);
	$('.div_interno').height(alto - 35);
}

function cambiar_mas(text){
	var resultado = text.replace("+","|mas");
	return resultado; 
}

function calcular_diferencia(val1, val2, eje1, eje2, resul) {
	if ($('#' + val1).val() != g_querato_np) {
		var numero1 = $('#' + val1).val().replace(',', ".");
		var numero2 = $('#' + val2).val().replace(',', ".");
		var valor1 = parseFloat(numero1);
		var valor2 = parseFloat(numero2);
		if($('#' + val1).val() != '' && $('#' + val2).val() != ''){
			var resultado = valor1 - valor2;
			var num_resul = parseFloat(resultado);
			if (!isNaN(num_resul)) {
				$('#' + resul).val(num_resul);
				var text_resul = $('#' + resul).val();
				text_resul = text_resul.replace('.', ",");
				$('#' + resul).val(text_resul);
			}
		}
		
		if ($('#' + val1).val() == '' || $('#' + val2).val() == '') {
			$('#' + resul).val('');
		}
	} else {
		$('#' + val2).val(g_querato_np);
		$('#' + eje1).val(g_querato_np);
		$('#' + eje2).val(g_querato_np);
		$('#' + resul).val(g_querato_np);
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
 * Validar los campos de optometria
 */
function validar_control_optometria() {
	var result = 0;
	
	$("#cke_txt_anamnesis").removeClass("borde_error");
	
	$("#avsc_lejos_od").removeClass("borde_error");
	$("#avsc_cerca_od").removeClass("borde_error");
	$("#avsc_lejos_oi").removeClass("borde_error");
	$("#avsc_cerca_oi").removeClass("borde_error");
	
	$("#querato_k1_od").removeClass("borde_error");
	$("#querato_ejek1_od").removeClass("borde_error");
	$("#querato_dif_od").removeClass("borde_error");
	$("#querato_k1_oi").removeClass("borde_error");
	$("#querato_ejek1_oi").removeClass("borde_error");
	$("#querato_dif_oi").removeClass("borde_error");
	
	$("#subjetivo_esfera_od").removeClass("borde_error");
	$("#subjetivo_lejos_od").removeClass("borde_error");
	$("#subjetivo_cerca_od").removeClass("borde_error");
	$("#subjetivo_esfera_oi").removeClass("borde_error");
	$("#subjetivo_lejos_oi").removeClass("borde_error");
	$("#subjetivo_cerca_oi").removeClass("borde_error");
	
	//Para diagnosticos pintar normal
	$("#ciex_diagnostico_1").removeClass("borde_error");
	$("#valor_ojos_1").removeClass("borde_error");
	var cant_ciex = $('#lista_tabla').val()
	for (i = 1; i <= cant_ciex; i++) {
	 	 var cod_ciex = $("#hdd_ciex_diagnostico_" + i).val();
	 	 var val_ojos = $("#valor_ojos_" + i).val();
	 	 $("#valor_ojos_" + i).removeClass("borde_error");
	}
	
	if ($("#cmb_validar_consulta").val() != "2") {
		//Para diagnosticos pintar error
		if ($('#hdd_ciex_diagnostico_1').val() == '') {
			$("#ciex_diagnostico_1").addClass("borde_error");
			result = 1;
		}
		if ($('#valor_ojos_1').val() == '') {
			$("#valor_ojos_1").addClass("borde_error");
			result = 1;
		}
		var cant_ciex = $('#lista_tabla').val()
		for (i = 1; i <= cant_ciex; i++) {
			 var cod_ciex = $("#hdd_ciex_diagnostico_" + i).val();
			 var val_ojos = $("#valor_ojos_" + i).val();
			 if (cod_ciex != '' && val_ojos == '') {
				$("#valor_ojos_" + i).addClass("borde_error");
				result = 1;
			 }
		}
		
		if (CKEDITOR.instances.txt_anamnesis.getData() == "") {
			$("#cke_txt_anamnesis").addClass("borde_error");
			result = 1;
		}
		if ($('#avsc_lejos_od').val() == '' && $('#avsc_lejos_od').attr("disabled") != "disabled") {
			$("#avsc_lejos_od").addClass("borde_error");
			result = 1;
		}
		if ($('#avsc_cerca_od').val() == '' && $('#avsc_cerca_od').attr("disabled") != "disabled") {
			$("#avsc_cerca_od").addClass("borde_error");
			result = 1;
		}
		if ($('#avsc_lejos_oi').val() == '' && $('#avsc_lejos_oi').attr("disabled") != "disabled") {
			$("#avsc_lejos_oi").addClass("borde_error");
			result = 1;
		}
		if ($('#avsc_cerca_oi').val() == '' && $('#avsc_cerca_oi').attr("disabled") != "disabled") {
			$("#avsc_cerca_oi").addClass("borde_error");
			result = 1;
		}
		
		if ($('#querato_k1_od').val() == '' && $('#querato_k1_od').attr("disabled") != "disabled") {
			$("#querato_k1_od").addClass("borde_error");
			result = 1;
		}
		if ($('#querato_ejek1_od').val() == '' && $('#querato_ejek1_od').attr("disabled") != "disabled") {
			$("#querato_ejek1_od").addClass("borde_error");
			result = 1;
		}
		if ($('#querato_dif_od').val() == '' && $('#querato_dif_od').attr("disabled") != "disabled") {
			$("#querato_dif_od").addClass("borde_error");
			result = 1;
		}
		if ($('#querato_k1_oi').val() == '' && $('#querato_k1_oi').attr("disabled") != "disabled") {
			$("#querato_k1_oi").addClass("borde_error");
			result = 1;
		}
		if ($('#querato_ejek1_oi').val() == '' && $('#querato_ejek1_oi').attr("disabled") != "disabled") {
			$("#querato_ejek1_oi").addClass("borde_error");
			result = 1;
		}
		if ($('#querato_dif_oi').val() == '' && $('#querato_dif_oi').attr("disabled") != "disabled") {
			$("#querato_dif_oi").addClass("borde_error");
			result = 1;
		}
		
		if ($('#subjetivo_esfera_od').val() == '' && $('#subjetivo_esfera_od').attr("disabled") != "disabled") {
			$("#subjetivo_esfera_od").addClass("borde_error");
			result = 1;
		}
		if ($('#subjetivo_lejos_od').val() == '' && $('#subjetivo_lejos_od').attr("disabled") != "disabled") {
			$("#subjetivo_lejos_od").addClass("borde_error");
			result = 1;
		}
		if ($('#subjetivo_cerca_od').val() == '' && $('#subjetivo_cerca_od').attr("disabled") != "disabled") {
			$("#subjetivo_cerca_od").addClass("borde_error");
			result = 1;
		}
		if ($('#subjetivo_esfera_oi').val() == '' && $('#subjetivo_esfera_oi').attr("disabled") != "disabled") {
			$("#subjetivo_esfera_oi").addClass("borde_error");
			result = 1;
		}
		if ($('#subjetivo_lejos_oi').val() == '' && $('#subjetivo_lejos_oi').attr("disabled") != "disabled") {
			$("#subjetivo_lejos_oi").addClass("borde_error");
			result = 1;
		}
		if ($('#subjetivo_cerca_oi').val() == '' && $('#subjetivo_cerca_oi').attr("disabled") != "disabled") {
			$("#subjetivo_cerca_oi").addClass("borde_error");
			result = 1;
		}
	}
	
	return result;
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta SIN VALIDAR LOS CAMPOS
 */
function validar_crear_control_optometria(tipo, ind_imprimir) {
	switch (tipo) {
		case 1: //Finalizar consulta
		case 3: //Guardar desde la historia
		case 4: //Finalizar consulta desde traslado
			$("#btn_imprimir").attr("disabled", "disabled");
			$("#btn_crear").attr("disabled", "disabled");
			$("#btn_finalizar").attr("disabled", "disabled");
			
			$("#contenedor_error").css("display", "none");
			if (validar_control_optometria() == 0) {
				editar_consulta_optometria(tipo, ind_imprimir);
				return false;
			} else {
				$("#contenedor_error").css("display", "block");
				$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
				window.scroll(0, 0);
				$("#btn_imprimir").removeAttr("disabled");
				$("#btn_crear").removeAttr("disabled");
				$("#btn_finalizar").removeAttr("disabled");
				return false;
			}
			break;
			
		case 2: //Guardar cambios
			editar_consulta_optometria(tipo, ind_imprimir);
			break;
	}
}

function imprimir_control_optometria() {
	var params = "id_hc=" + $("#hdd_id_hc_consulta").val();
	
	llamarAjax("../historia_clinica/impresion_historia_clinica.php", params, "d_impresion_hc", "continuar_imprimir_control_optometria();");
}

function continuar_imprimir_control_optometria() {
	var ruta = $("#hdd_ruta_arch_hc_pdf").val();
	window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=control_optometria.pdf", "_blank");
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta 
 */
function editar_consulta_optometria(tipo, ind_imprimir) {
	$("#btn_imprimir").attr("disabled", "disabled");
	$("#btn_crear").attr("disabled", "disabled");
	$("#btn_finalizar").attr("disabled", "disabled");
	
	var id_hc = $('#hdd_id_hc_consulta').val();
	var id_admision = $('#hdd_id_admision').val();
	
	var validar_completa = $('#cmb_validar_consulta').val();
	var anamnesis = str_encode(CKEDITOR.instances.txt_anamnesis.getData());
	var id_ojo = $('#cmb_ojo_control').val();
	
	var avsc_lejos_od = $('#avsc_lejos_od').val();
	var avsc_ph_od = $('#avsc_ph_od').val();
	var avsc_cerca_od = $('#avsc_cerca_od').val();
	var avsc_lejos_oi = $('#avsc_lejos_oi').val();
	var avsc_ph_oi = $('#avsc_ph_oi').val();
	var avsc_cerca_oi = $('#avsc_cerca_oi').val();
	
	var lenso_esfera_od = str_encode($('#lenso_esfera_od').val());
	var lenso_cilindro_od = str_encode($('#lenso_cilindro_od').val());
	var lenso_eje_od = str_encode($('#lenso_eje_od').val());
	var lenso_lejos_od = str_encode($('#lenso_lejos_od').val());
	var lenso_ph_od = str_encode($('#lenso_ph_od').val());
	var lenso_adicion_od = str_encode($('#lenso_adicion_od').val());
	var lenso_cerca_od = str_encode($('#lenso_cerca_od').val());
	var lenso_esfera_oi = str_encode($('#lenso_esfera_oi').val());
	var lenso_cilindro_oi = str_encode($('#lenso_cilindro_oi').val());
	var lenso_eje_oi = str_encode($('#lenso_eje_oi').val());
	var lenso_lejos_oi = str_encode($('#lenso_lejos_oi').val());
	var lenso_ph_oi = str_encode($('#lenso_ph_oi').val());
	var lenso_adicion_oi = str_encode($('#lenso_adicion_oi').val());
	var lenso_cerca_oi = str_encode($('#lenso_cerca_oi').val());
	
	var querato_k1_od = str_encode($('#querato_k1_od').val());
	var querato_ejek1_od = str_encode($('#querato_ejek1_od').val());
	var querato_dif_od = str_encode($('#querato_dif_od').val());
	var querato_k1_oi = str_encode($('#querato_k1_oi').val());
	var querato_ejek1_oi = str_encode($('#querato_ejek1_oi').val());
	var querato_dif_oi = str_encode($('#querato_dif_oi').val());
	
	var cicloplejio_esfera_od = str_encode($('#cicloplejio_esfera_od').val());
	var cicloplejio_cilindro_od = str_encode($('#cicloplejio_cilindro_od').val());
	var cicloplejio_eje_od = str_encode($('#cicloplejio_eje_od').val());
	var cicloplejio_lejos_od = str_encode($('#cicloplejio_lejos_od').val());
	var cicloplejio_ph_od = str_encode($('#cicloplejio_ph_od').val());
	var cicloplejio_adicion_od = str_encode($('#cicloplejio_adicion_od').val());
	var cicloplejio_esfera_oi = str_encode($('#cicloplejio_esfera_oi').val());
	var cicloplejio_cilindro_oi = str_encode($('#cicloplejio_cilindro_oi').val());
	var cicloplejio_eje_oi = str_encode($('#cicloplejio_eje_oi').val());
	var cicloplejio_lejos_oi = str_encode($('#cicloplejio_lejos_oi').val());
	var cicloplejio_ph_oi = str_encode($('#cicloplejio_ph_oi').val());
	var cicloplejio_adicion_oi = str_encode($('#cicloplejio_adicion_oi').val());
	
	var subjetivo_esfera_od = str_encode($('#subjetivo_esfera_od').val());
	var subjetivo_cilindro_od = str_encode($('#subjetivo_cilindro_od').val());
	var subjetivo_eje_od = $('#subjetivo_eje_od').val();
	var subjetivo_lejos_od = $('#subjetivo_lejos_od').val();
	var subjetivo_ph_od = $('#subjetivo_ph_od').val();
	var subjetivo_adicion_od = str_encode($('#subjetivo_adicion_od').val());
	var subjetivo_cerca_od = $('#subjetivo_cerca_od').val();
	var subjetivo_esfera_oi = str_encode($('#subjetivo_esfera_oi').val());
	var subjetivo_cilindro_oi = str_encode($('#subjetivo_cilindro_oi').val());
	var subjetivo_eje_oi = $('#subjetivo_eje_oi').val();
	var subjetivo_lejos_oi = $('#subjetivo_lejos_oi').val();
	var subjetivo_ph_oi = $('#subjetivo_ph_oi').val();
	var subjetivo_adicion_oi = str_encode($('#subjetivo_adicion_oi').val());
	var subjetivo_cerca_oi = $('#subjetivo_cerca_oi').val();
	
	var observaciones_subjetivo = str_encode(CKEDITOR.instances.txt_observaciones_subjetivo.getData());
	var diagnostico_optometria = str_encode(CKEDITOR.instances.txt_diagnostico_optometria.getData());
	
	var params = 'opcion=1';
	
	//Para Diagnosticos
	var cant_ciex = $('#lista_tabla').val();
	params += '&cant_ciex=' + cant_ciex;
	for (i = 1; i <= cant_ciex; i++) {
	 	 var cod_ciex = $("#hdd_ciex_diagnostico_" + i).val();
	 	 var val_ojos = $("#valor_ojos_" + i).val();
	 	 if (cod_ciex != '') {
	 	 	params += '&cod_ciex_' + i + '=' + cod_ciex + '&val_ojos_' + i + '=' + val_ojos;
	 	 }
	}
	
	params += '&id_hc='+ id_hc +
			  '&id_admision=' + id_admision +
			  '&validar_completa=' + validar_completa +
			  '&anamnesis=' + anamnesis +
			  '&id_ojo=' + id_ojo +
			  '&avsc_lejos_od=' + avsc_lejos_od +
			  '&avsc_ph_od=' + avsc_ph_od +
			  '&avsc_cerca_od=' + avsc_cerca_od +
			  '&avsc_lejos_oi=' + avsc_lejos_oi +
			  '&avsc_ph_oi=' + avsc_ph_oi +
			  '&avsc_cerca_oi=' + avsc_cerca_oi +
			  '&lenso_esfera_od=' + lenso_esfera_od +
			  '&lenso_cilindro_od=' + lenso_cilindro_od +
			  '&lenso_eje_od=' + lenso_eje_od +
			  '&lenso_lejos_od=' + lenso_lejos_od +
			  '&lenso_ph_od=' + lenso_ph_od +
			  '&lenso_adicion_od=' + lenso_adicion_od +
			  '&lenso_cerca_od=' + lenso_cerca_od +
			  '&lenso_esfera_oi=' + lenso_esfera_oi +
			  '&lenso_cilindro_oi=' + lenso_cilindro_oi +
			  '&lenso_eje_oi=' + lenso_eje_oi +
			  '&lenso_lejos_oi=' + lenso_lejos_oi +
			  '&lenso_ph_oi=' + lenso_ph_oi +
			  '&lenso_adicion_oi=' + lenso_adicion_oi +
			  '&lenso_cerca_oi=' + lenso_cerca_oi +
			  '&querato_k1_od=' + querato_k1_od +
			  '&querato_ejek1_od=' + querato_ejek1_od +
			  '&querato_dif_od=' + querato_dif_od +
			  '&querato_k1_oi=' + querato_k1_oi +
			  '&querato_ejek1_oi=' + querato_ejek1_oi +
			  '&querato_dif_oi=' + querato_dif_oi +
			  '&cicloplejio_esfera_od=' + cicloplejio_esfera_od +
			  '&cicloplejio_cilindro_od=' + cicloplejio_cilindro_od +
			  '&cicloplejio_eje_od=' + cicloplejio_eje_od +
			  '&cicloplejio_lejos_od=' + cicloplejio_lejos_od +
			  '&cicloplejio_ph_od=' + cicloplejio_ph_od +
			  '&cicloplejio_adicion_od=' + cicloplejio_adicion_od +
			  '&cicloplejio_esfera_oi=' + cicloplejio_esfera_oi +
			  '&cicloplejio_cilindro_oi=' + cicloplejio_cilindro_oi +
			  '&cicloplejio_eje_oi=' + cicloplejio_eje_oi +
			  '&cicloplejio_lejos_oi=' + cicloplejio_lejos_oi +
			  '&cicloplejio_ph_oi=' + cicloplejio_ph_oi +
			  '&cicloplejio_adicion_oi=' + cicloplejio_adicion_oi +
			  '&subjetivo_esfera_od=' + subjetivo_esfera_od +
			  '&subjetivo_cilindro_od=' + subjetivo_cilindro_od +
			  '&subjetivo_eje_od=' + subjetivo_eje_od +
			  '&subjetivo_lejos_od=' + subjetivo_lejos_od +
			  '&subjetivo_ph_od=' + subjetivo_ph_od +
			  '&subjetivo_adicion_od=' + subjetivo_adicion_od +
			  '&subjetivo_cerca_od=' + subjetivo_cerca_od +
			  '&subjetivo_esfera_oi=' + subjetivo_esfera_oi +
			  '&subjetivo_cilindro_oi=' + subjetivo_cilindro_oi +
			  '&subjetivo_eje_oi=' + subjetivo_eje_oi +
			  '&subjetivo_lejos_oi=' + subjetivo_lejos_oi +
			  '&subjetivo_ph_oi=' + subjetivo_ph_oi +
			  '&subjetivo_adicion_oi=' + subjetivo_adicion_oi +
			  '&subjetivo_cerca_oi=' + subjetivo_cerca_oi +
			  '&observaciones_subjetivo=' + observaciones_subjetivo +
			  '&diagnostico_optometria=' + diagnostico_optometria +
			  '&tipo_guardar=' + tipo +
			  '&cadena_colores=' + obtener_cadena_colores();
	
	llamarAjax("control_optometria_ajax.php", params, "guardar_optometria", "validar_exito(" + ind_imprimir + ")");
}

function validar_exito(ind_imprimir) {
	var hdd_exito = $('#hdd_exito').val();
	var hdd_url_menu = $('#hdd_url_menu').val();
	var hdd_tipo_guardar = $('#hdd_tipo_guardar').val();
	
	if (hdd_tipo_guardar == 1) { //Cierra el formulario
		if (hdd_exito > 0) {
			$('.formulario').css('display', 'none');
			$("#contenedor_exito").css("display", "block");
			$('#contenedor_exito').html('Datos guardados correctamente');
			setTimeout("enviar_credencial('" + hdd_url_menu + "')", 3000);
		} else {
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Error al guardar la consulta de control de optometr&iacute;a");
		}
	} else { //Permanece en el formulario
		if (hdd_exito > 0) {
			$("#contenedor_exito").css("display", "block");
			$('#contenedor_exito').html('Datos guardados correctamente');
			setTimeout('$("#contenedor_exito").css("display", "none")', 3000);
			
			if (ind_imprimir == 1) {
				imprimir_control_optometria();
			}
		} else {
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Error al guardar la consulta de control de optometr&iacute;a");
		}
	}
	window.scrollTo(0, 0);
	$("#btn_imprimir").removeAttr("disabled");
	$("#btn_crear").removeAttr("disabled");
	$("#btn_finalizar").removeAttr("disabled");
}

function calculo_ejes(eje1, eje2) {
	var val_eje1 = $('#' + eje1).val();
	var val_eje2 = $('#' + eje2).val();
	var val_eje_resul = (parseInt(val_eje1, 10) + 90) % 180;
	if (!isNaN(val_eje_resul)) {
		$('#' + eje2).val(val_eje_resul);
	}
}

function generar_formula_gafas(observaciones, fecha_hc, nombre_paciente, nombre_profesional, esfera_od, cilindro_od, eje_od, adicion_od, esfera_oi, cilindro_oi, eje_oi, adicion_oi, id_admision) {
	var esfera_od = $('#'+esfera_od).val();
	var cilindro_od = $('#'+cilindro_od).val();
	var eje_od = $('#'+eje_od).val();
	var adicion_od = $('#'+adicion_od).val();
	
	var esfera_oi = $('#'+esfera_oi).val();
	var cilindro_oi = $('#'+cilindro_oi).val();
	var eje_oi = $('#'+eje_oi).val();
	var adicion_oi = $('#'+adicion_oi).val();
	
	var observaciones = eval("CKEDITOR.instances." + observaciones + ".getData()");
	
	var params = "opcion=1&id_admision=" + id_admision +
				 "&hdd_nombre_paciente=" + str_encode(nombre_paciente) +
				 "&hdd_fecha_admision=" + str_encode(fecha_hc) +
				 "&esfera_od=" + str_encode(esfera_od) +
				 "&cilindro_od=" + str_encode(cilindro_od) +
				 "&eje_od=" + str_encode(eje_od) +
				 "&adicion_od=" + str_encode(adicion_od) +
				 "&esfera_oi=" + str_encode(esfera_oi) +
				 "&cilindro_oi=" + str_encode(cilindro_oi) +
				 "&eje_oi=" + str_encode(eje_oi) +
				 "&adicion_oi=" + str_encode(adicion_oi) +
				 "&observacion=" + str_encode(observaciones) +
				 "&hdd_nombre_profesional_optometra=" + str_encode(nombre_profesional);
	
	llamarAjax("../historia_clinica/formula_gafas_ajax.php", params, "imprimir_formula", "imprimir_formula_gafas_cont();");
}

function imprimir_formula_gafas_cont() {
	var ruta = $("#hdd_ruta_formula_gafas_pdf").val();
	window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=formula_gafas.pdf", "_blank");
}

function imprSelec(muestra) {
	var ficha = document.getElementById(muestra);
	var ventimp = window.open(' ', 'popimpr');
	ventimp.document.write(ficha.innerHTML);
	ventimp.document.close();
	ventimp.print();
}

function enviar_a_estados() {
	validar_crear_control_optometria(2, 0);
	
	var params = "opcion=3&id_hc=" + $("#hdd_id_hc_consulta").val() +
				 "&id_admision=" + $("#hdd_id_admision").val();
	
	llamarAjax("control_optometria_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1);");
}

function seleccionar_ojo_co(id_ojo) {
	switch (id_ojo) {
		case "79":
			$("#avsc_lejos_od").removeAttr("disabled");
			$("#avsc_ph_od").removeAttr("disabled");
			$("#avsc_cerca_od").removeAttr("disabled");
			$("#avsc_lejos_oi").val("");
			$("#avsc_ph_oi").val("");
			$("#avsc_cerca_oi").val("");
			$("#avsc_lejos_oi").attr("disabled", "disabled");
			$("#avsc_ph_oi").attr("disabled", "disabled");
			$("#avsc_cerca_oi").attr("disabled", "disabled");
			
			$("#lenso_esfera_od").removeAttr("disabled");
			$("#lenso_cilindro_od").removeAttr("disabled");
			$("#lenso_eje_od").removeAttr("disabled");
			$("#lenso_lejos_od").removeAttr("disabled");
			$("#lenso_ph_od").removeAttr("disabled");
			$("#lenso_adicion_od").removeAttr("disabled");
			$("#lenso_cerca_od").removeAttr("disabled");
			$("#lenso_esfera_oi").val("");
			$("#lenso_cilindro_oi").val("");
			$("#lenso_eje_oi").val("");
			$("#lenso_lejos_oi").val("");
			$("#lenso_ph_oi").val("");
			$("#lenso_adicion_oi").val("");
			$("#lenso_cerca_oi").val("");
			$("#lenso_esfera_oi").attr("disabled", "disabled");
			$("#lenso_cilindro_oi").attr("disabled", "disabled");
			$("#lenso_eje_oi").attr("disabled", "disabled");
			$("#lenso_lejos_oi").attr("disabled", "disabled");
			$("#lenso_ph_oi").attr("disabled", "disabled");
			$("#lenso_adicion_oi").attr("disabled", "disabled");
			$("#lenso_cerca_oi").attr("disabled", "disabled");
			
			$("#querato_k1_od").removeAttr("disabled");
			$("#querato_ejek1_od").removeAttr("disabled");
			$("#querato_dif_od").removeAttr("disabled");
			$("#querato_k1_oi").val("");
			$("#querato_ejek1_oi").val("");
			$("#querato_dif_oi").val("");
			$("#querato_k1_oi").attr("disabled", "disabled");
			$("#querato_ejek1_oi").attr("disabled", "disabled");
			$("#querato_dif_oi").attr("disabled", "disabled");
			
			$("#cicloplejio_esfera_od").removeAttr("disabled");
			$("#cicloplejio_cilindro_od").removeAttr("disabled");
			$("#cicloplejio_eje_od").removeAttr("disabled");
			$("#cicloplejio_lejos_od").removeAttr("disabled");
			$("#cicloplejio_ph_od").removeAttr("disabled");
			$("#cicloplejio_adicion_od").removeAttr("disabled");
			$("#cicloplejio_esfera_oi").val("");
			$("#cicloplejio_cilindro_oi").val("");
			$("#cicloplejio_eje_oi").val("");
			$("#cicloplejio_lejos_oi").val("");
			$("#cicloplejio_ph_oi").val("");
			$("#cicloplejio_adicion_oi").val("");
			$("#cicloplejio_esfera_oi").attr("disabled", "disabled");
			$("#cicloplejio_cilindro_oi").attr("disabled", "disabled");
			$("#cicloplejio_eje_oi").attr("disabled", "disabled");
			$("#cicloplejio_lejos_oi").attr("disabled", "disabled");
			$("#cicloplejio_ph_oi").attr("disabled", "disabled");
			$("#cicloplejio_adicion_oi").attr("disabled", "disabled");
			
			$("#subjetivo_esfera_od").removeAttr("disabled");
			$("#subjetivo_cilindro_od").removeAttr("disabled");
			$("#subjetivo_eje_od").removeAttr("disabled");
			$("#subjetivo_lejos_od").removeAttr("disabled");
			$("#subjetivo_ph_od").removeAttr("disabled");
			$("#subjetivo_adicion_od").removeAttr("disabled");
			$("#subjetivo_cerca_od").removeAttr("disabled");
			$("#subjetivo_esfera_oi").val("");
			$("#subjetivo_cilindro_oi").val("");
			$("#subjetivo_eje_oi").val("");
			$("#subjetivo_lejos_oi").val("");
			$("#subjetivo_ph_oi").val("");
			$("#subjetivo_adicion_oi").val("");
			$("#subjetivo_cerca_oi").val("");
			$("#subjetivo_esfera_oi").attr("disabled", "disabled");
			$("#subjetivo_cilindro_oi").attr("disabled", "disabled");
			$("#subjetivo_eje_oi").attr("disabled", "disabled");
			$("#subjetivo_lejos_oi").attr("disabled", "disabled");
			$("#subjetivo_ph_oi").attr("disabled", "disabled");
			$("#subjetivo_adicion_oi").attr("disabled", "disabled");
			$("#subjetivo_cerca_oi").attr("disabled", "disabled");
			break;
			
		case "80":
			$("#avsc_lejos_od").val("");
			$("#avsc_ph_od").val("");
			$("#avsc_cerca_od").val("");
			$("#avsc_lejos_od").attr("disabled", "disabled");
			$("#avsc_ph_od").attr("disabled", "disabled");
			$("#avsc_cerca_od").attr("disabled", "disabled");
			$("#avsc_lejos_oi").removeAttr("disabled");
			$("#avsc_ph_oi").removeAttr("disabled");
			$("#avsc_cerca_oi").removeAttr("disabled");
			
			$("#lenso_esfera_od").val("");
			$("#lenso_cilindro_od").val("");
			$("#lenso_eje_od").val("");
			$("#lenso_lejos_od").val("");
			$("#lenso_ph_od").val("");
			$("#lenso_adicion_od").val("");
			$("#lenso_cerca_od").val("");
			$("#lenso_esfera_od").attr("disabled", "disabled");
			$("#lenso_cilindro_od").attr("disabled", "disabled");
			$("#lenso_eje_od").attr("disabled", "disabled");
			$("#lenso_lejos_od").attr("disabled", "disabled");
			$("#lenso_ph_od").attr("disabled", "disabled");
			$("#lenso_adicion_od").attr("disabled", "disabled");
			$("#lenso_cerca_od").attr("disabled", "disabled");
			$("#lenso_esfera_oi").removeAttr("disabled");
			$("#lenso_cilindro_oi").removeAttr("disabled");
			$("#lenso_eje_oi").removeAttr("disabled");
			$("#lenso_lejos_oi").removeAttr("disabled");
			$("#lenso_ph_oi").removeAttr("disabled");
			$("#lenso_adicion_oi").removeAttr("disabled");
			$("#lenso_cerca_oi").removeAttr("disabled");
			
			$("#querato_k1_od").val("");
			$("#querato_ejek1_od").val("");
			$("#querato_dif_od").val("");
			$("#querato_k1_od").attr("disabled", "disabled");
			$("#querato_ejek1_od").attr("disabled", "disabled");
			$("#querato_dif_od").attr("disabled", "disabled");
			$("#querato_k1_oi").removeAttr("disabled");
			$("#querato_ejek1_oi").removeAttr("disabled");
			$("#querato_dif_oi").removeAttr("disabled");
			
			$("#cicloplejio_esfera_od").val("");
			$("#cicloplejio_cilindro_od").val("");
			$("#cicloplejio_eje_od").val("");
			$("#cicloplejio_lejos_od").val("");
			$("#cicloplejio_ph_od").val("");
			$("#cicloplejio_adicion_od").val("");
			$("#cicloplejio_esfera_od").attr("disabled", "disabled");
			$("#cicloplejio_cilindro_od").attr("disabled", "disabled");
			$("#cicloplejio_eje_od").attr("disabled", "disabled");
			$("#cicloplejio_lejos_od").attr("disabled", "disabled");
			$("#cicloplejio_ph_od").attr("disabled", "disabled");
			$("#cicloplejio_adicion_od").attr("disabled", "disabled");
			$("#cicloplejio_esfera_oi").removeAttr("disabled");
			$("#cicloplejio_cilindro_oi").removeAttr("disabled");
			$("#cicloplejio_eje_oi").removeAttr("disabled");
			$("#cicloplejio_lejos_oi").removeAttr("disabled");
			$("#cicloplejio_ph_oi").removeAttr("disabled");
			$("#cicloplejio_adicion_oi").removeAttr("disabled");
			
			$("#subjetivo_esfera_od").val("");
			$("#subjetivo_cilindro_od").val("");
			$("#subjetivo_eje_od").val("");
			$("#subjetivo_lejos_od").val("");
			$("#subjetivo_ph_od").val("");
			$("#subjetivo_adicion_od").val("");
			$("#subjetivo_cerca_od").val("");
			$("#subjetivo_esfera_od").attr("disabled", "disabled");
			$("#subjetivo_cilindro_od").attr("disabled", "disabled");
			$("#subjetivo_eje_od").attr("disabled", "disabled");
			$("#subjetivo_lejos_od").attr("disabled", "disabled");
			$("#subjetivo_ph_od").attr("disabled", "disabled");
			$("#subjetivo_adicion_od").attr("disabled", "disabled");
			$("#subjetivo_cerca_od").attr("disabled", "disabled");
			$("#subjetivo_esfera_oi").removeAttr("disabled");
			$("#subjetivo_cilindro_oi").removeAttr("disabled");
			$("#subjetivo_eje_oi").removeAttr("disabled");
			$("#subjetivo_lejos_oi").removeAttr("disabled");
			$("#subjetivo_ph_oi").removeAttr("disabled");
			$("#subjetivo_adicion_oi").removeAttr("disabled");
			$("#subjetivo_cerca_oi").removeAttr("disabled");
			break;
			
		case "81":
			$("#avsc_lejos_od").removeAttr("disabled");
			$("#avsc_ph_od").removeAttr("disabled");
			$("#avsc_cerca_od").removeAttr("disabled");
			$("#avsc_lejos_oi").removeAttr("disabled");
			$("#avsc_ph_oi").removeAttr("disabled");
			$("#avsc_cerca_oi").removeAttr("disabled");
			
			$("#lenso_esfera_od").removeAttr("disabled");
			$("#lenso_cilindro_od").removeAttr("disabled");
			$("#lenso_eje_od").removeAttr("disabled");
			$("#lenso_lejos_od").removeAttr("disabled");
			$("#lenso_ph_od").removeAttr("disabled");
			$("#lenso_adicion_od").removeAttr("disabled");
			$("#lenso_cerca_od").removeAttr("disabled");
			$("#lenso_esfera_oi").removeAttr("disabled");
			$("#lenso_cilindro_oi").removeAttr("disabled");
			$("#lenso_eje_oi").removeAttr("disabled");
			$("#lenso_lejos_oi").removeAttr("disabled");
			$("#lenso_ph_oi").removeAttr("disabled");
			$("#lenso_adicion_oi").removeAttr("disabled");
			$("#lenso_cerca_oi").removeAttr("disabled");
			
			$("#querato_k1_od").removeAttr("disabled");
			$("#querato_ejek1_od").removeAttr("disabled");
			$("#querato_dif_od").removeAttr("disabled");
			$("#querato_k1_oi").removeAttr("disabled");
			$("#querato_ejek1_oi").removeAttr("disabled");
			$("#querato_dif_oi").removeAttr("disabled");
			
			$("#cicloplejio_esfera_od").removeAttr("disabled");
			$("#cicloplejio_cilindro_od").removeAttr("disabled");
			$("#cicloplejio_eje_od").removeAttr("disabled");
			$("#cicloplejio_lejos_od").removeAttr("disabled");
			$("#cicloplejio_ph_od").removeAttr("disabled");
			$("#cicloplejio_adicion_od").removeAttr("disabled");
			$("#cicloplejio_esfera_oi").removeAttr("disabled");
			$("#cicloplejio_cilindro_oi").removeAttr("disabled");
			$("#cicloplejio_eje_oi").removeAttr("disabled");
			$("#cicloplejio_lejos_oi").removeAttr("disabled");
			$("#cicloplejio_ph_oi").removeAttr("disabled");
			$("#cicloplejio_adicion_oi").removeAttr("disabled");
			
			$("#subjetivo_esfera_od").removeAttr("disabled");
			$("#subjetivo_cilindro_od").removeAttr("disabled");
			$("#subjetivo_eje_od").removeAttr("disabled");
			$("#subjetivo_lejos_od").removeAttr("disabled");
			$("#subjetivo_ph_od").removeAttr("disabled");
			$("#subjetivo_adicion_od").removeAttr("disabled");
			$("#subjetivo_cerca_od").removeAttr("disabled");
			$("#subjetivo_esfera_oi").removeAttr("disabled");
			$("#subjetivo_cilindro_oi").removeAttr("disabled");
			$("#subjetivo_eje_oi").removeAttr("disabled");
			$("#subjetivo_lejos_oi").removeAttr("disabled");
			$("#subjetivo_ph_oi").removeAttr("disabled");
			$("#subjetivo_adicion_oi").removeAttr("disabled");
			$("#subjetivo_cerca_oi").removeAttr("disabled");
			break;
	}
}
