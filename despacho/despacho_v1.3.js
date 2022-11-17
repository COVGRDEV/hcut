function cargar_formula(texto_formula) {
	var indice = $("#cmb_num_formula").val();
	
	//Tomar el texto del editor
	var editor = nicEditors.findEditor("text_despacho_" + indice);
	editor.saveContent();
	var text_despacho = $("#text_despacho_" + indice).val();
	
	//Se a grega el texto de la fórmula
	var texto_descripcion_formula = text_despacho + '<br />' + texto_formula;
	
	//Se cargar a la caja de texto
	editor.setContent(texto_descripcion_formula);
	editor.saveContent();
}

function cargar_formula_id(indice) {
	var cod_formula = $("#texto_cod_formula_" + indice).val();
	var texto_formula = $("#hdd_formula_" + cod_formula).val();
	
	if (texto_formula != undefined) {
		//Tomar el texto del editor
		var editor = nicEditors.findEditor("text_despacho_" + indice);
		editor.saveContent();
		var text_despacho = $("#text_despacho_" + indice).val();
		
		//Se a grega el texto de la fórmula
		var texto_descripcion_formula = text_despacho + '<br />' + texto_formula;
		
		//Se cargar a la caja de texto
		editor.setContent(texto_descripcion_formula);
		editor.saveContent();
	}
}

function guardar_despacho(id_tipo) {
	var result = 0;
	var cant_formulas = parseInt($("#hdd_cant_formulas").val(), 10);
	var num_formula = parseInt($("#cmb_num_formula").val(), 10);
	$("#contenedor_error").css("display", "none");
	
	if (result == 0) {
		//var txt_formula_medica = $('#txt_formula_medica').val();
		var hdd_id_paciente = $('#hdd_id_paciente').val();
		var hdd_id_admision = $('#hdd_id_admision').val();
		var hdd_nombre_paciente = $('#hdd_nombre_paciente').val();
		var hdd_fecha_admision = $('#hdd_fecha_admision').val();
		var hdd_documento_paciente = $('#hdd_documento_paciente').val();
		var hdd_nombre_profesional = $('#hdd_nombre_profesional').val();
		
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
					 "&nombre_profesional=" + hdd_nombre_profesional +
					 "&tipo_impresion=" + tipo_impresion +
					 "&id_tipo=" + id_tipo +
					 "&num_formula=" + num_formula +
					 "&cant_formulas=" + cant_formulas;
		
		for (var i = 0; i < cant_formulas; i++) {
			nicEditors.findEditor('text_despacho_' + i).saveContent();
			var text_despacho = $('#text_despacho_' + i).val();
			params += "&remitido_" + i + "=" + str_encode($("#txt_remitido_" + i).val());
			params += "&num_carnet_" + i + "=" + str_encode($("#txt_num_carnet_" + i).val());
			params += "&formula_medica_" + i + "=" + str_encode(text_despacho);
		}
		
		$("#guardar_despacho").css("display", "none");
		llamarAjax("despacho_ajax.php", params, "guardar_despacho", "validar_exito(" + id_tipo + ")");
	} else {
		$("#contenedor_error").css("display", "block");
    	$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
		return false;
	}
}

function validar_exito(id_tipo) {
	var hdd_exito = $('#hdd_exito').val();
	var hdd_url_menu = $('#hdd_url_menu').val();
	
	if (hdd_exito > 0) {
		if (id_tipo == 2) { //Imprimir
			$("#contenedor_exito").css("display", "block");
			$('#contenedor_exito').html('Datos guardados correctamente');
			imprSelec('campo_imprimir');
		} else if (id_tipo == 1) { //Guarda y finaliza No imprimir
			$("#contenedor_exito").css("display", "block");
			$('#contenedor_exito').html('Datos guardados correctamente');
			setTimeout("enviar_credencial('" + hdd_url_menu + "')", 2000);
		} else if (id_tipo == 3) { //Solo Guarda
			$("#contenedor_exito").css("display", "block");
			$('#contenedor_exito').html('Datos guardados correctamente');
			$("#guardar_despacho").css("display", "block");
			window.scrollTo(0, 0);
			setTimeout('$("#contenedor_exito").css("display", "none");', 2000);
		} else if (id_tipo == 4) { //Solo Imprime
			imprSelec('campo_imprimir');
		}
	} else {
		$("#contenedor_error").css("display", "block");
		$('#contenedor_error').html('Error al guardar usuarios');
		setTimeout("enviar_credencial('" + hdd_url_menu + "')", 2000);
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
	
}
