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
	$("#btn_imprimir").attr("disabled", "disabled");
	$("#btn_imprimir_cotiz").attr("disabled", "disabled");
	$("#btn_guardar").attr("disabled", "disabled");
	$("#btn_finalizar").attr("disabled", "disabled");
	
	var result = 0;
	var cant_formulas = parseInt($("#hdd_cant_formulas").val(), 10);
	var num_formula = parseInt($("#cmb_num_formula").val(), 10);
	var cant_cotizaciones = parseInt($("#hdd_cant_cotizaciones").val(), 10);
	var num_cotizacion = parseInt($("#cmb_num_cotizacion").val(), 10);
	$("#contenedor_error").css("display", "none");
	
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
	
	window.alert(str_encode(eval("CKEDITOR.instances.text_despacho_" + 0 + ".getData()")));
	for (var i = 0; i < cant_formulas; i++) {
		 // window.alert(str_encode(eval("CKEDITOR.instances.text_despacho_" + i + ".getData()")));
		if ($("#hdd_act_formula_" + i).val() == "1") {
			params += "&remitido_" + cont_aux + "=" + str_encode($("#txt_remitido_" + i).val()) +
					  "&num_carnet_" + cont_aux + "=" + str_encode($("#txt_num_carnet_" + i).val()) +
					  "&fecha_det_" + cont_aux + "=" + str_encode($("#txt_fecha_det_" + i).val()) +
					  "&formula_medica_" + cont_aux + "=" + str_encode(eval("CKEDITOR.instances.text_despacho_" + i + ".getData()"));
					  
					
			
			cont_aux++;
		}
	}
	params += "&cant_formulas=" + cont_aux;
	
	cont_aux = 0;
	for (var i = 0; i < cant_cotizaciones; i++) {
		if ($("#hdd_act_cotizacion_" + i).val() == "1") {
			if ($("#cmb_proc_cotiz_" + i).val() != "" && $("#txt_valor_cotiz_" + i).val() != "") {
				params += "&id_proc_cotiz_" + cont_aux + "=" + $("#cmb_proc_cotiz_" + i).val() +
						  "&valor_cotiz_" + cont_aux + "=" + $("#txt_valor_cotiz_" + i).val() +
						  "&observaciones_cotiz_" + cont_aux + "=" + str_encode(eval("CKEDITOR.instances.txt_observaciones_cotiz_" + i + ".getData()"));
				
				cont_aux++;
			}
		}
	}
	params += "&cant_cotizaciones=" + cont_aux;
	
	$("#guardar_despacho").css("display", "none");
	llamarAjax("despacho_ajax.php", params, "guardar_despacho", "validar_exito(" + id_tipo + ", " + tipo_impresion + ")");
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
			
			var ruta = $("#hdd_ruta_arch_pdf").val();
			window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=formula.pdf", "_blank");
		} else if (id_tipo == 3) { //Solo Guarda
			$("#contenedor_exito").css("display", "block");
			$('#contenedor_exito').html('Datos guardados correctamente');
			$("#guardar_despacho").css("display", "block");
			window.scrollTo(0, 0);
			setTimeout('$("#contenedor_exito").css("display", "none");', 2000);
		} else if (id_tipo == 4) { //Solo Imprime
			var ruta = $("#hdd_ruta_arch_pdf").val();
			window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=formula.pdf", "_blank");
		} else if (id_tipo == 5) { //Imprimir cotización
			$("#contenedor_exito").css("display", "block");
			$('#contenedor_exito').html('Datos guardados correctamente');
			
			var ruta = $("#hdd_ruta_cotiz_pdf").val();
			window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=cotizacion.pdf", "_blank");
		} else if (id_tipo == 6) { //Solo Imprime cotización
			var ruta = $("#hdd_ruta_cotiz_pdf").val();
			window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=cotizacion.pdf", "_blank");
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
	
	$("#btn_imprimir").removeAttr("disabled");
	$("#btn_imprimir_cotiz").removeAttr("disabled");
	$("#btn_guardar").removeAttr("disabled");
	$("#btn_finalizar").removeAttr("disabled");
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


function convertDateFormat(string) {
  var info = string.split('/');
  return info[2] + '-' + info[1] + '-' + info[0];
}
	
	
function calcular_diff_fechas(){
	var fecha_inicial 	= $('#fechaInicial').val();
	var fecha_inicial	= moment(convertDateFormat(fecha_inicial));
	var fecha_final 	= $('#fechaFinal').val();
	var fecha_final		= moment(convertDateFormat(fecha_final));
	var dias 			= fecha_final.diff(fecha_inicial, 'days');

	if(dias<0){
				 swal({
				title: "Fechas incorrectas",
				type: "warning",
				text: "La fecha final es menor a la fecha inicial",
				showConfirmButton: false,
				showCloseButton: true,
			});
	}
	
	dias = dias + 1;
	if(dias > 0){
		document.getElementById("hdd_diferencia_dias").innerHTML = dias + " días";	
	}else{
		document.getElementById("hdd_diferencia_dias").innerHTML = 0 + " días";	
	}
}

function guardar_incapacidad(){
	
	if($('#hdd_rta_hc').val() == 1){
	
			var id_paciente = $('#hdd_id_paciente').val();
			var id_convenio = $('#hdd_id_convenio').val();
			var id_plan = $('#hdd_id_plan').val();
			var id_lugar_cita = $('#hdd_id_lugar_cita').val();
	}else{	
		
			var id_paciente = $('#hdd_id_paciente_hc').val();
			var id_convenio = $('#hdd_id_convenio_hc').val();
			var id_plan = $('#hdd_id_plan_hc').val();
			var id_lugar_cita = $('#hdd_id_lugar_cita_hc').val();		
			
	}	 	
			var id_admision = $('#hdd_id_admision').val();	
			var id_profesional = $('#hdd_id_profesional').val();
			var id_usuario = $('#hdd_id_usuario').val();
			var tipo_atencion = $('#cmb_tipo_atencion_hc').val();
			var origen_incapacidad = $('#cmb_origen_incapacidad_hc').val();
			var prorroga = $('#cmb_prorroga_hc').val();
			var fecha_inicial = $('#fechaInicial').val();
			var fecha_final = $('#fechaFinal').val();
			var observaciones = str_encode(eval("CKEDITOR.instances.txt_observaciones_adicionales.getData()"));
			var cod_ciex = $('#hdd_ciex_diagnostico_1').val();
			var cod_ciex_rel = $('#hdd_ciex_diagnostico_2').val();			
			var id_hc = $('#hdd_id_hc').val();
		

	//Se validan que los campos importantes no vayan vacíos, dentro del formularios
	 if (fecha_inicial == '' || fecha_final == '' || origen_incapacidad == '' || prorroga == '' || tipo_atencion == '') {
    		
			$('#fechaInicial').addClass('error');
			$('#fechaFinal').addClass('error');
			$('#cmb_prorroga_0').addClass('error');
			$('#cmb_tipo_atencion_0').addClass('error');
			$('#cmb_origen_incapacidad_0').addClass('error');
			
		 swal({
				title: "Campos vacíos",
				type: "error",
				text: "Por favor, diligencie el formulario con los datos requeridos",
				showConfirmButton: false,
				showCloseButton: true,
			});

    } else {
        fecha_inicial = fecha_inicial.split('/');
        fecha_inicial = fecha_inicial[2] + '-' + fecha_inicial[1] + '-' + fecha_inicial[0];
        fecha_final = fecha_final.split('/');
        fecha_final = fecha_final[2] + '-' + fecha_final[1] + '-' + fecha_final[0];
		
		var params = "opcion=2&id_paciente=" + id_paciente +
					 "&id_profesional=" + id_profesional +
					 "&id_usuario=" + id_usuario +
					 "&tipo_atencion=" + tipo_atencion +
					 "&origen_incapacidad=" + origen_incapacidad +
					 "&prorroga=" + prorroga +
					 "&fecha_inicial=" + fecha_inicial +
					 "&fecha_final=" + fecha_final +
					 "&observaciones=" + observaciones +
					 "&id_convenio=" + id_convenio + 
					 "&id_plan=" + id_plan +
					 "&id_lugar_cita=" + id_lugar_cita +
					 "&id_admision=" + id_admision +
					 "&cod_ciex=" + cod_ciex +
					 "&cod_ciex_rel=" + cod_ciex_rel +
					 "&id_hc=" + id_hc +
					 "&observaciones=" + observaciones;
				 
		llamarAjax("despacho_ajax.php", params, "guardar_incapacidad", "imprimir_incapacidad();");
			
    } 
}

function imprimir_incapacidad(){
	
	var rta_guardar = $('#hdd_rta_guardar_incapacidad').val();
	
	if(rta_guardar > 0){
		
		var id_paciente = $('#hdd_id_paciente').val();
		
		var fecha_inicial = $('#fechaInicial').val();
		var fecha_final = $('#fechaFinal').val();	
		var id_incapacidad = rta_guardar;
	
		var fecha_inicial_diff	= moment(convertDateFormat(fecha_inicial));
		var fecha_final_diff	= moment(convertDateFormat(fecha_final));
		var dias 				= fecha_final_diff.diff(fecha_inicial_diff, 'days');	
		(dias = dias + 1);
		if(dias<0){
			dias = 0;
		}
		
		var params = 	"opcion=3&id_incapacidad="	+	id_incapacidad		+
						"&dias="             		+   dias;
		
		llamarAjax("despacho_ajax.php", params, "guardar_incapacidad","imprimir_incapacidad_cont();");
		
	}else{
		
		 swal({
				title: "Error",
				type: "error",
				text: "Error al guardar la admision",
				showConfirmButton: false,
				showCloseButton: true,
			});
	}
}

function imprimir_incapacidad_cont(){
	var ruta = $("#hdd_ruta_incapacidad").val();
	window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=incapacidad.pdf", "_blank");

}


function imprimir_formula_gafas(hdd_id_hc_consulta, tipo_impresion, esfera_od, cilindro_od, eje_od, adicion_od, esfera_oi, cilindro_oi, eje_oi, adicion_oi, observacion, id_admision, tipo_lente) {
	var hdd_nombre_paciente = $('#hdd_nombre_paciente').val();
	//var hdd_impresion_tipo = str_encode($("hdd_impresion_tipo").val());
	var hdd_id_hc_consulta =  str_encode($("#hdd_id_hc_consulta").val()); 
	var hdd_fecha_admision = $('#hdd_fecha_admision').val();
	var hdd_nombre_profesional_optometra = $('#hdd_nombre_profesional_optometra').val();
		
	var params = "opcion=1&id_admision=" + id_admision +
				 "&hdd_id_hc_consulta=" + hdd_id_hc_consulta +
				 "&hdd_nombre_paciente=" + str_encode(hdd_nombre_paciente) +
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
				 "&tipo_lente=" + str_encode(tipo_lente) +
				 "&tipo_impresion=" + tipo_impresion+
				 "&hdd_nombre_profesional_optometra=" + hdd_nombre_profesional_optometra;
	
	//alert(params);
	llamarAjax("../historia_clinica/formula_gafas_ajax.php", params, "imprimir_formula", "imprimir_formula_gafas_cont();");
}

function imprimir_formula_gafas_cont() {
	var ruta = $("#hdd_ruta_formula_gafas_pdf").val();
	window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=formula_gafas.pdf", "_blank");
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
	mostrar_remitido();
	ajustar_textareas();
}

function ocultar_formula(id) {
	$("#d_detalle_formula_" + id).hide();
}

function agregar_formula() {
	var cant_formulas = parseInt($("#hdd_cant_formulas").val(), 10);
	if (cant_formulas < 20) {
		$("#hdd_act_formula_" + cant_formulas).val(1);
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
	var num_formula = parseInt($("#cmb_num_formula").val(), 10);
	
	$("#hdd_act_formula_" + num_formula).val(0);
	ocultar_formula(num_formula);
	$("#cmb_num_formula option[value='" + num_formula + "']").remove();
	
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
	
	ajustar_textareas();
}

function ocultar_cotizacion(id) {
	$("#d_detalle_cotizacion_" + id).hide();
}

function agregar_cotizacion() {
	var cant_cotizaciones = parseInt($("#hdd_cant_cotizaciones").val(), 10);
	if (cant_cotizaciones < 20) {
		$("#hdd_act_cotizacion_" + cant_cotizaciones).val(1);
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
	var num_cotizacion = parseInt($("#cmb_num_cotizacion").val(), 10);
	
	$("#hdd_act_cotizacion_" + num_cotizacion).val(0);
	ocultar_cotizacion(num_cotizacion);
	$("#cmb_num_cotizacion option[value='" + num_cotizacion + "']").remove();
	
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

function ajustar_textareas() {
	for (var i in CKEDITOR.instances) {
		(function(i){
			CKEDITOR.instances[i].setData(CKEDITOR.instances[i].getData());
		})(i);
	}
}
