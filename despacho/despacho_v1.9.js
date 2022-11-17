/***********************************************/
/*Configuración del editor de texto enriquecido*/
/***********************************************/

if (CKEDITOR.env.ie && CKEDITOR.env.version < 9) {
	CKEDITOR.tools.enableHtml5Elements(document);
}

CKEDITOR.config.width = 'auto';

var initCKEditorDespacho = (function(id_obj) {
	var wysiwygareaAvailable = isWysiwygareaAvailable(),
		isBBCodeBuiltIn = !!CKEDITOR.plugins.get('bbcode');
	
	return function(id_obj) {
		var editorElement = CKEDITOR.document.getById(id_obj);
		
		//Dependiendo de la disponibilidad del plugin wysiwygare se incia el editor clásico o el editor en línea.
		if (wysiwygareaAvailable) {
			CKEDITOR.replace(id_obj);
		} else {
			editorElement.setAttribute('contenteditable', 'true');
			CKEDITOR.inline(id_obj);
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

function cargar_formula(cod_formula) {
	var texto_formula = $("#hdd_formula_" + cod_formula).val();
	var indice = $("#cmb_num_formula").val();
	
	//Tomar el texto del editor
	var text_despacho = eval("CKEDITOR.instances.text_despacho_" + indice + ".getData()");
	
	//Se a grega el texto de la fórmula
	var texto_descripcion_formula = text_despacho + str_decode(texto_formula);
	
	//Se cargar a la caja de texto
	eval("CKEDITOR.instances.text_despacho_" + indice + ".setData(texto_descripcion_formula)");
}

function cargar_formula_id(indice) {
	var cod_formula = $("#texto_cod_formula_" + indice).val();
	var texto_formula = $("#hdd_formula_" + cod_formula).val();
	
	if (texto_formula != undefined) {
		//Tomar el texto del editor
		var text_despacho = eval("CKEDITOR.instances.text_despacho_" + indice + ".getData()");
		
		//Se a grega el texto de la fórmula
		var texto_descripcion_formula = text_despacho + texto_formula;
		
		//Se cargar a la caja de texto
		eval("CKEDITOR.instances.text_despacho_" + indice + ".setData(texto_descripcion_formula)");
	}
}

function guardar_despacho(id_tipo) {
	var result = 0;
	var cant_formulas = parseInt($("#hdd_cant_formulas").val(), 10);
	var num_formula = parseInt($("#cmb_num_formula").val(), 10);
	var cant_cotizaciones = parseInt($("#hdd_cant_cotizaciones").val(), 10);
	var num_cotizacion = parseInt($("#cmb_num_cotizacion").val(), 10);
	$("#contenedor_error").css("display", "none");
	
	if (result == 0) {
		//var txt_formula_medica = $('#txt_formula_medica').val();
		var hdd_id_paciente = $('#hdd_id_paciente').val();
		var hdd_id_admision = $('#hdd_id_admision').val();
		var hdd_nombre_paciente = $('#hdd_nombre_paciente').val();
		var hdd_fecha_admision = $('#hdd_fecha_admision').val();
		var hdd_documento_paciente = $('#hdd_documento_paciente').val();
		var hdd_id_profesional = $('#hdd_id_profesional').val();
		var hdd_nombre_profesional = $('#hdd_nombre_profesional').val();
		var fecha_actual_impr = $("#chk_fecha_actual_impr").is(":checked") ? 1 : 0;
		
		if ($("#tipo_impresion").is(':checked')) {
			var tipo_impresion = 2; //Ecopetrol
		} else {  
			var tipo_impresion = 1; //Consultorio
		}
		
		var params = "opcion=1&id_paciente=" + hdd_id_paciente +
					 "&id_admision=" + hdd_id_admision +
					 "&nombre_paciente=" + hdd_nombre_paciente +
					 "&fecha_admision=" + hdd_fecha_admision +
					 "&documento_paciente=" + hdd_documento_paciente +
					 "&id_usuario_prof=" + hdd_id_profesional +
					 "&nombre_profesional=" + hdd_nombre_profesional +
					 "&tipo_impresion=" + tipo_impresion +
					 "&fecha_actual_impr=" + fecha_actual_impr +
					 "&id_tipo=" + id_tipo +
					 "&num_formula=" + num_formula +
					 "&num_cotizacion=" + num_cotizacion;
		
		var cont_aux = 0;
		for (var i = 0; i < cant_formulas; i++) {
			var bol_existe_aux = false;
			$("#cmb_num_formula option").each(function(){
				if (this.value == ("" + i)) {
					bol_existe_aux = true;
					return false;
				}
			});
			
			if (bol_existe_aux) {
				params += "&remitido_" + cont_aux + "=" + str_encode($("#txt_remitido_" + i).val()) +
						  "&num_carnet_" + cont_aux + "=" + str_encode($("#txt_num_carnet_" + i).val()) +
						  "&formula_medica_" + cont_aux + "=" + str_encode(eval("CKEDITOR.instances.text_despacho_" + i + ".getData()"));
				
				cont_aux++;
			}
		}
		params += "&cant_formulas=" + cont_aux;
		
		cont_aux = 0;
		for (var i = 0; i < cant_cotizaciones; i++) {
			var bol_existe_aux = false;
			$("#cmb_num_cotizacion option").each(function(){
				if (this.value == ("" + i)) {
					bol_existe_aux = true;
					return false;
				}
			});
			
			if (bol_existe_aux) {
				if ($("#cmb_proc_cotiz_" + i).val() != "" && $("#txt_valor_cotiz_" + i).val() != "") {
					params += "&id_proc_cotiz_" + cont_aux + "=" + $("#cmb_proc_cotiz_" + i).val() +
							  "&valor_cotiz_" + cont_aux + "=" + $("#txt_valor_cotiz_" + i).val() +
							  "&observaciones_cotiz_" + cont_aux + "=" + str_encode($('#txt_observaciones_cotiz_' + i).val());
					
					cont_aux++;
				}
			}
		}
		params += "&cant_cotizaciones=" + cont_aux;
		
		$("#guardar_despacho").css("display", "none");
		llamarAjax("despacho_ajax.php", params, "guardar_despacho", "validar_exito(" + id_tipo + ", " + tipo_impresion + ")");
	} else {
		$("#contenedor_error").css("display", "block");
    	$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
		return false;
	}
}

function validar_exito(id_tipo, tipo_impresion) {
	var hdd_exito = parseInt($('#hdd_exito').val(), 10);
	var hdd_url_menu = $('#hdd_url_menu').val();
	
	if (hdd_exito > 0) {
		if (id_tipo == 1) { //Guarda y finaliza No imprimir
			$("#contenedor_exito").css("display", "block");
			$('#contenedor_exito').html('Datos guardados correctamente');
			setTimeout("enviar_credencial('" + hdd_url_menu + "')", 2000);
		} else if (id_tipo == 2) { //Imprimir
			$("#contenedor_exito").css("display", "block");
			$('#contenedor_exito').html('Datos guardados correctamente');
			switch (tipo_impresion) {
				case 1:
					imprSelec('campo_imprimir');
					break;
				case 2:
					var ruta = $("#hdd_ruta_arch_pdf").val();
					window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=formula.pdf", "_blank");
					break;
			}
		} else if (id_tipo == 3) { //Solo Guarda
			$("#contenedor_exito").css("display", "block");
			$('#contenedor_exito').html('Datos guardados correctamente');
			$("#guardar_despacho").css("display", "block");
			window.scrollTo(0, 0);
			setTimeout('$("#contenedor_exito").css("display", "none");', 2000);
		} else if (id_tipo == 4) { //Solo Imprime
			switch (tipo_impresion) {
				case 1:
					imprSelec('campo_imprimir');
					break;
				case 2:
					var ruta = $("#hdd_ruta_arch_pdf").val();
					window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=formula.pdf", "_blank");
					break;
			}
		} else if (id_tipo == 5) { //Imprimir cotización
			$("#contenedor_exito").css("display", "block");
			$('#contenedor_exito').html('Datos guardados correctamente');
			imprSelec('campo_imprimir_cotiz');
		} else if (id_tipo == 6) { //Solo Imprime cotización
			imprSelec('campo_imprimir_cotiz');
		}
	} else if (hdd_exito == -3) {
		$("#contenedor_error").css("display", "block");
		$('#contenedor_error').html('Error al registrar las f&oacute;rmulas de despacho');
		window.scrollTo(0, 0);
	} else if (hdd_exito == -4) {
		$("#contenedor_error").css("display", "block");
		$('#contenedor_error').html('Error al registrar las cotizaciones de despacho');
		window.scrollTo(0, 0);
	} else {
		$("#contenedor_error").css("display", "block");
		$('#contenedor_error').html('Error al guardar el registro de despacho');
		window.scrollTo(0, 0);
	}
}

function imprSelec(muestra) {
	var ficha = document.getElementById(muestra);
	var ventimp = window.open(' ', 'popimpr');
	ventimp.document.write(ficha.innerHTML);
	ventimp.document.close();
	ventimp.print();
	ventimp.close();
}

function mostrar_remitido() {
	var cant_formulas = parseInt($("#hdd_cant_formulas").val(), 10);
	if ($("#tipo_impresion").is(':checked')) {
		for (var i = 0; i < cant_formulas; i++) {
			$("#d_remitido_" + i).css("display", "block");
		}
	} else {
		for (var i = 0; i < cant_formulas; i++) {
			$("#d_remitido_" + i).css("display", "none");
		}
	}
}

function seleccionar_formula() {
	var tipo_formula_gafas = $('#tipo_formula_gafas').val();
	if (tipo_formula_gafas == 1) { //Subjetivo
		$("#tabla_subjetivo").css("display", "");
		$("#tabla_refrafinal").css("display", "none");
	}
	if (tipo_formula_gafas == 2) { //Refracci&oacute;n final
		$("#tabla_subjetivo").css("display", "none");
		$("#tabla_refrafinal").css("display", "");
	}
	if (tipo_formula_gafas == '') { //Subjetivo
		$("#tabla_subjetivo").css("display", "none");
		$("#tabla_refrafinal").css("display", "none");
	}
}

function imprimir_formula_gafas(esfera_od, cilindro_od, eje_od, adicion_od, esfera_oi, cilindro_oi, eje_oi, adicion_oi, observacion) {
	var hdd_nombre_paciente = $('#hdd_nombre_paciente').val();
	var hdd_fecha_admision = $('#hdd_fecha_admision').val();
	var hdd_nombre_profesional_optometra = $('#hdd_nombre_profesional_optometra').val();
	
	var params = "opcion=2&hdd_nombre_paciente=" + str_encode(hdd_nombre_paciente) +
				 "&hdd_fecha_admision=" + str_encode(hdd_fecha_admision) +
				 "&esfera_od=" + str_encode(esfera_od) +
				 "&cilindro_od=" + str_encode(cilindro_od) +
				 "&eje_od=" + str_encode(eje_od) +
				 "&adicion_od=" + str_encode(adicion_od) +
				 "&esfera_oi=" + str_encode(esfera_oi) +
				 "&cilindro_oi=" + str_encode(cilindro_oi) +
				 "&eje_oi=" + str_encode(eje_oi) +
				 "&adicion_oi=" + str_encode(adicion_oi) +
				 "&observacion=" + str_encode(observacion) +
				 "&hdd_nombre_profesional_optometra=" + hdd_nombre_profesional_optometra;
	
	llamarAjax("despacho_ajax.php", params, "imprimir_formula", "imprSelec(\"campo_imprimir_formula\")");
}

function mostrar_formula(id) {
	id = parseInt(id, 10);
	for (var i = 0; i < 20; i++) {
		if (id == i) {
			$("#d_detalle_formula_" + i).show();
		} else if ($("#d_detalle_formula_" + i).is(":visible")) {
			$("#d_detalle_formula_" + i).hide();
		}
	}
}

function ocultar_formula(id) {
	$("#d_detalle_formula_" + id).hide();
}

function agregar_formula() {
	var cant_formulas = parseInt($("#hdd_cant_formulas").val(), 10);
	if (cant_formulas < 20) {
		var opt_aux = new Option(cant_formulas + 1, cant_formulas);
		$(opt_aux).html(cant_formulas + 1);
		$("#cmb_num_formula").append(opt_aux);
		
		$("#hdd_cant_formulas").val(cant_formulas + 1);
		$("#cmb_num_formula").val(cant_formulas);
		mostrar_formula(cant_formulas);
	} else {
		alert("Se ha alcanzado el n\xfamero m\xe1ximo de f\xf3rmulas permitidas.");
	}
}

function restar_formula() {
	ocultar_formula($("#cmb_num_formula").val());
	$("#cmb_num_formula option[value='" + $("#cmb_num_formula").val() + "']").remove();
	
	mostrar_formula($("#cmb_num_formula").val());
}

function cancelar_registro_despacho() {
	enviar_credencial("../admisiones/estado_atencion.php");
}

function mostrar_cotizacion(id) {
	id = parseInt(id, 10);
	for (var i = 0; i < 20; i++) {
		if (id == i) {
			$("#d_detalle_cotizacion_" + i).show();
		} else if ($("#d_detalle_cotizacion_" + i).is(":visible")) {
			$("#d_detalle_cotizacion_" + i).hide();
		}
	}
}

function ocultar_cotizacion(id) {
	$("#d_detalle_cotizacion_" + id).hide();
}

function agregar_cotizacion() {
	var cant_cotizaciones = parseInt($("#hdd_cant_cotizaciones").val(), 10);
	if (cant_cotizaciones < 20) {
		var opt_aux = new Option(cant_cotizaciones + 1, cant_cotizaciones);
		$(opt_aux).html(cant_cotizaciones + 1);
		$("#cmb_num_cotizacion").append(opt_aux);
		
		$("#hdd_cant_cotizaciones").val(cant_cotizaciones + 1);
		$("#cmb_num_cotizacion").val(cant_cotizaciones);
		mostrar_cotizacion(cant_cotizaciones);
	} else {
		alert("Se ha alcanzado el n\xfamero m\xe1ximo de cotizaciones permitidas.");
	}
}

function restar_cotizacion() {
	ocultar_cotizacion($("#cmb_num_cotizacion").val());
	$("#cmb_num_cotizacion option[value='" + $("#cmb_num_cotizacion").val() + "']").remove();
	
	mostrar_cotizacion($("#cmb_num_cotizacion").val());
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
