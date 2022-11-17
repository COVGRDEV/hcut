/***********************************************/
/*Configuración del editor de texto enriquecido*/
/***********************************************/

if (CKEDITOR.env.ie && CKEDITOR.env.version < 9) {
	CKEDITOR.tools.enableHtml5Elements(document);
}

CKEDITOR.config.width = "auto";
CKEDITOR.config.height = 55;

var initCKEditorEvolucion = (function(id_obj) {
	var wysiwygareaAvailable = isWysiwygareaAvailable(),
		isBBCodeBuiltIn = !!CKEDITOR.plugins.get("bbcode");
	
	return function(id_obj) {
		var editorElement = CKEDITOR.document.getById(id_obj);
		
		//Dependiendo de la disponibilidad del plugin wysiwygare se incia el editor clásico o el editor en línea.
		if (wysiwygareaAvailable) {
			CKEDITOR.replace(id_obj);
		} else {
			editorElement.setAttribute("contenteditable", "true");
			CKEDITOR.inline(id_obj);
		}
	};
	
	function isWysiwygareaAvailable() {
		if (CKEDITOR.revision == ("%RE" + "V%")) {
			return true;
		}
		
		return !!CKEDITOR.plugins.get("wysiwygarea");
	}
} )();

var initCKEditorTratEvol = (function() {
	var wysiwygareaAvailable = isWysiwygareaAvailable(),
		isBBCodeBuiltIn = !!CKEDITOR.plugins.get("bbcode");
	
	return function() {
		var editorElement = CKEDITOR.document.getById("txt_tratamiento_evolucion");
		
		//Dependiendo de la disponibilidad del plugin wysiwygare se incia el editor clásico o el editor en línea.
		if (wysiwygareaAvailable) {
			CKEDITOR.replace("txt_tratamiento_evolucion");
		} else {
			editorElement.setAttribute("contenteditable", "true");
			CKEDITOR.inline("txt_tratamiento_evolucion");
		}
	};
	
	function isWysiwygareaAvailable() {
		if (CKEDITOR.revision == ("%RE" + "V%")) {
			return true;
		}
		
		return !!CKEDITOR.plugins.get("wysiwygarea");
	}
} )();

/***********************************************/
/***********************************************/
/***********************************************/

//var arr_textarea_ids = [];
function ajustar_textareas() {
	/*for (i = 0; i < arr_textarea_ids.length; i++) {
		$("#" + arr_textarea_ids[i]).trigger("input");
	}*/
	
	for (var i in CKEDITOR.instances) {
		(function(i){
			CKEDITOR.instances[i].setData(CKEDITOR.instances[i].getData());
		})(i);
	}
}

function ocultar_panels_evolucion() {
	$("#panel2-1").removeClass("active");
	$("#panel2-3").removeClass("active");
	$("#panel2-4").removeClass("active");
	$("#panel2-5").removeClass("active");
	$("#panel2-6").removeClass("active");
	$("#panel2-7").removeClass("active");
}

function mostrar_formulario_flotante(tipo){
	if(tipo==1){//mostrar
		$("#fondo_negro").css("display", "block");
		$("#d_centro").slideDown(400).css("display", "block");
	}
	else if(tipo==0){//Ocultar
		$("#fondo_negro").css("display", "none");
		$("#d_centro").slideDown(400).css("display", "none");
	}
}

function reducir_formulario_flotante(ancho, alto){ //480, 390
	$(".div_centro").width(ancho);
	$(".div_centro").height(alto);
	$(".div_centro").css("top", "20%");
	$(".div_interno").width(ancho - 15);
	$(".div_interno").height(alto - 35);
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
	if (text == "") {
		ind_existe = 1;//Si Existe
	}
	if (ind_existe == 0) {
		alert("Valor incorrecto");
		document.getElementById(id.id).value = "";
		input = id.id;
		setTimeout("document.getElementById(input).focus()", 75); 
	}
}

function mostrar_formulario(tipo) {
    if (tipo == 1) {//mostrar
        $(".formulario").slideDown(600).css("display", "block");
    }
    else if (tipo == 0) {//Ocultar
        $(".formulario").slideUp(600).css("display", "none");
    }
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta SIN VALIDAR LOS CAMPOS
 */
function guardar_evolucion(tipo, ind_imprimir) {
	switch (tipo) {
		case 1: //Finalizar consulta
		case 3: //Guardar desde la historia
		case 4: //Finalizar consulta desde traslado
			$("#btn_imprimir").attr("disabled", "disabled");
			$("#btn_crear").attr("disabled", "disabled");
			$("#btn_finalizar").attr("disabled", "disabled");
			
			$("#contenedor_error").css("display", "none");
			var resultado = validar_evolucion();
			if (resultado == 0) {
				editar_consulta_evolucion(tipo, ind_imprimir);
			} else {
				$("#contenedor_error").css("display", "block");
				if (resultado == -2) {
					$("#contenedor_error").html("Error - existen diagn&oacute;sticos duplicados");
				} else if (resultado == -3) {
					$("#contenedor_error").html("Error - existen procedimientos duplicados");
				} else {
					$("#contenedor_error").html("Los campos marcados en rojo son obligatorios");
				}
				window.scroll(0, 0);
				$("#btn_imprimir").removeAttr("disabled");
				$("#btn_crear").removeAttr("disabled");
				$("#btn_finalizar").removeAttr("disabled");
			}
			break;
		case 2: //Guardar cambios
			//Se validan duplicados de diagnósticos
			if (validar_duplicados_diagnosticos_hc() != -2) {
				if (validar_hc_procedimientos_solic() != -3) {
					editar_consulta_evolucion(tipo, ind_imprimir);
				} else {
					$("#contenedor_error").css("display", "block");
					$("#contenedor_error").html("Error - existen procedimientos duplicados");
					window.scroll(0, 0);
				}
			} else {
				$("#contenedor_error").css("display", "block");
				$("#contenedor_error").html("Error - existen diagn&oacute;sticos duplicados");
				window.scroll(0, 0);
			}
			break;
	}
}

function imprimir_evolucion() {
	var params = "id_hc=" + $("#hdd_id_hc_consulta").val();
	
	llamarAjax("../historia_clinica/impresion_historia_clinica.php", params, "d_impresion_hc", "continuar_imprimir_evolucion()");
}

function continuar_imprimir_evolucion() {
	var ruta = $("#hdd_ruta_arch_hc_pdf").val();
	window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=consulta_evolucion.pdf", "_blank");
}

function validar_evolucion() {
	var resultado = 0;
	var panel_1 = 0;
	var panel_2 = 0;
	var panel_3 = 0;
	var panel_4 = 0;
	var panel_5 = 0;
	var panel_6 = 0;
	var panel_7 = 0;
	
	$("#panel_oft_1").removeClass("borde_error_panel");
	$("#panel_oft_1 a").css({"color": "#5B5B5B"});
	$("#panel_oft_2").removeClass("borde_error_panel");
	$("#panel_oft_2 a").css({"color": "#5B5B5B"});
	$("#panel_oft_3").removeClass("borde_error_panel");
	$("#panel_oft_3 a").css({"color": "#5B5B5B"});
	$("#panel_oft_4").removeClass("borde_error_panel");
	$("#panel_oft_4 a").css({"color": "#5B5B5B"});
	$("#panel_oft_5").removeClass("borde_error_panel");
	$("#panel_oft_5 a").css({"color": "#5B5B5B"});
	$("#panel_oft_6").removeClass("borde_error_panel");
	$("#panel_oft_6 a").css({"color": "#5B5B5B"});
	$("#panel_oft_7").removeClass("borde_error_panel");
	$("#panel_oft_7 a").css({"color": "#5B5B5B"});
	
    $("#cke_txt_evolucion").removeClass("borde_error");
	$("#txt_nombre_usuario_alt").removeClass("borde_error");
	$("#cmb_formula_gafas").removeClass("borde_error");
	
	if (CKEDITOR.instances.txt_evolucion.getData() == "") {
		$("#cke_txt_evolucion").addClass("borde_error");
		resultado = -1;
		panel_2 = 1;
	}
	
	if ($("#cmb_formula_gafas").val() == "" && $("#hdd_ind_optometria").val() == "1") {
		$("#cmb_formula_gafas").addClass("borde_error");
		resultado = -1;
		panel_2 = 1;
	}
	
	var num_diag_oblig = parseInt($("#hdd_num_diag_oblig").val(), 10);
	for (var i = 1; i <= num_diag_oblig; i++) {
		if ($("#hdd_ciex_diagnostico_" + i).val() == "") {
			$("#ciex_diagnostico_" + i).addClass("borde_error");
			resultado = -1;
			panel_2 = 1;
		}
		if ($("#valor_ojos_" + i).val() == "") {
			$("#valor_ojos_" + i).addClass("borde_error");
			resultado = -1;
			panel_2 = 1;
		}
	}
	
	//Formulación de medicamentos
	if (!validar_formulacion_fm()) {
		resultado = -1;
		panel_2 = 1;
	}
	
	if ($("#hdd_usuario_anonimo").val() == "1" && $("#txt_nombre_usuario_alt").val() == ""){
		$("#txt_nombre_usuario_alt").addClass("borde_error");
		resultado = -1;
		panel_2 = 1;
	}
	
	//Validación de diagnósticos
	var result_ciex = validar_diagnosticos_hc(1);
	if (result_ciex < 0) {
		resultado = result_ciex;
		panel_2 = 1;
	} else {
		//Validación de procedimientos solicitados
		var result_cups_solic = validar_hc_procedimientos_solic();
		if (result_cups_solic < 0) {
			resultado = result_cups_solic;
			panel_2 = 1;
		}
	}
	
	//Validación de registros adicionales
	var tipo_reg_adicional = $("#hdd_tipo_reg_adicional").val();
	
	switch (tipo_reg_adicional) {
		case "2": //Retina
			var result_aux = validar_consulta_oftalmologia_retina();
			if (result_aux < 0) {
				resultado = -1;
				panel_4 = 1;
			}
			break;
		case "3": //Oculoplastia
			var result_aux = validar_consulta_oculoplastia();
			if (result_aux < 0) {
				resultado = -1;
				panel_5 = 1;
			}
			break;
		case "4": //Pterigio
			var result_aux = validar_consulta_pterigio();
			if (result_aux < 0) {
				resultado = -1;
				panel_6 = 1;
			}
			break;			
		case "5": //NESO
			var result_aux = validar_consulta_neso(); 
			if (result_aux < 0) {
				resultado = -1;
				panel_7 = 1;
			}
			break;				
	}
	
	if (panel_1 == 1) {
	   $("#panel_oft_1").addClass("borde_error_panel");
	   $("#panel_oft_1 a").css({"color": "#FF002A"});
	}
	if (panel_2 == 1) {
	   $("#panel_oft_2").addClass("borde_error_panel");
	   $("#panel_oft_2 a").css({"color": "#FF002A"});
	}
	if (panel_3 == 1) {
	   $("#panel_oft_3").addClass("borde_error_panel");
	   $("#panel_oft_3 a").css({"color": "#FF002A"});
	}
	if (panel_4 == 1) {
	   $("#panel_oft_4").addClass("borde_error_panel");
	   $("#panel_oft_4 a").css({"color": "#FF002A"});
	}
	if (panel_5 == 1) {
	   $("#panel_oft_5").addClass("borde_error_panel");
	   $("#panel_oft_5 a").css({"color": "#FF002A"});
	}
	if (panel_6 == 1) {
	   $("#panel_oft_6").addClass("borde_error_panel");
	   $("#panel_oft_6 a").css({"color": "#FF002A"});
	} 	
	if (panel_7 == 1) {
	   $("#panel_oft_7").addClass("borde_error_panel");
	   $("#panel_oft_7 a").css({"color": "#FF002A"});
	}	
	
	return resultado;
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta 
 */
function editar_consulta_evolucion(tipo, ind_imprimir) {
	$("#btn_imprimir").attr("disabled", "disabled");
	$("#btn_crear").attr("disabled", "disabled");
	$("#btn_finalizar").attr("disabled", "disabled");
	
	var id_hc = $("#hdd_id_hc_consulta").val();
	var id_admision = $("#hdd_id_admision").val();
	var texto_evolucion = str_encode(CKEDITOR.instances.txt_evolucion.getData());
	var diagnostico_evolucion = str_encode(CKEDITOR.instances.txt_diagnostico_evolucion.getData());
	var solicitud_examenes_evolucion = str_encode(CKEDITOR.instances.txt_solicitud_examenes_evolucion.getData());
	var tratamiento_evolucion = str_encode(CKEDITOR.instances.txt_tratamiento_evolucion.getData());
	var medicamentos_evolucion = str_encode($("#medicamentos_evolucion").val());
	var nombre_usuario_alt = str_encode($("#txt_nombre_usuario_alt").val());
	var ind_formula_gafas = $("#cmb_formula_gafas").val();
	var observaciones_tonometria = str_encode(CKEDITOR.instances.txt_observaciones_tonometria.getData());
	
	var params = "opcion=1&id_hc=" + id_hc +
				 "&id_admision=" + id_admision +
	             "&texto_evolucion=" + texto_evolucion +
				 "&tipo_guardar=" + tipo +
				 "&diagnostico_evolucion=" +diagnostico_evolucion +
				 "&solicitud_examenes_evolucion=" + solicitud_examenes_evolucion +
				 "&tratamiento_evolucion=" + tratamiento_evolucion +
				 "&medicamentos_evolucion=" + medicamentos_evolucion +
				 "&nombre_usuario_alt=" + nombre_usuario_alt +
				 "&ind_formula_gafas=" + ind_formula_gafas +
				 "&observaciones_tonometria=" + observaciones_tonometria;
	
	//Para Diagnosticos
	var cant_ciex = $("#lista_tabla").val()
	params += "&cant_ciex=" + cant_ciex;
	for (i = 1; i <= cant_ciex; i++) {
		var cod_ciex = $("#hdd_ciex_diagnostico_" + i).val();
		var val_ojos = $("#valor_ojos_" + i).val();
		if (cod_ciex != "") {
			params += "&cod_ciex_" + i + "=" + cod_ciex +
					  "&val_ojos_" + i + "=" + val_ojos;
		}
	}
	
    //Para antecedentes
    params += obtener_parametros_antecedentes();
	
	//Tonometria
	params += obtener_parametros_tonometria();
	
	//Solicitud de procedimientos
	params += obtener_parametros_proc_solic();
	
	//Formulación de medicamentos
	params += obtener_parametros_formulacion_fm();
	
	//Registros adicionales
	var tipo_reg_adicional = $("#hdd_tipo_reg_adicional").val();
	params += "&tipo_reg_adicional=" + tipo_reg_adicional;
	switch (tipo_reg_adicional) {
		case "2": //Retina
			params += obtener_parametros_consulta_oftalmologia_retina();
			break;
		case "3": //Oculoplastia
			params += obtener_parametros_consulta_oculoplastia();
			break;
		case "4": //Pterigio
			params += obtener_parametros_consulta_pterigio();
			break;			
		case "5": //NESO
			params += obtener_parametros_consulta_neso();
			break;			
	}
	
	llamarAjax("evolucion_ajax.php", params, "d_guardar_evolucion", "validar_exito(" + ind_imprimir + ")");
}

function validar_exito(ind_imprimir) {
	var hdd_exito = $("#hdd_exito").val();
	var hdd_url_menu = $("#hdd_url_menu").val();
	var hdd_tipo_guardar = $("#hdd_tipo_guardar").val();
	var hdd_exito_formulacion_fm = $("#hdd_exito_formulacion_fm").val();
	var hdd_exito_hc_procedimientos_solic = $("#hdd_exito_hc_procedimientos_solic").val();
	
	if (hdd_tipo_guardar == 1) { //Cierra el formulario
		if (hdd_exito > 0 && hdd_exito_formulacion_fm > 0 && hdd_exito_hc_procedimientos_solic > 0) {
			$("#frm_consulta_evolucion").css("display", "none");
			$("#contenedor_exito").css("display", "block");
			$("#contenedor_exito").html("Datos guardados correctamente");
			setTimeout("enviar_credencial('" + hdd_url_menu + "')", 3000);
		} else if (hdd_exito <= 0) {
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Error al guardar la evoluci&oacute;n");
		} else if (hdd_exito_formulacion_fm <= 0){
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Error al guardar la formulaci&oacute;n de medicamentos");
		} else {
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Error al guardar la solicitud de procedimientos y ex&aacute;menes");
		}
	} else if (hdd_tipo_guardar == 2) { //Permanece en el formulario
		if (hdd_exito > 0 && hdd_exito_formulacion_fm > 0 && hdd_exito_hc_procedimientos_solic > 0) {
			$("#contenedor_exito").css("display", "block");
			$("#contenedor_exito").html("Datos guardados correctamente");
			setTimeout('$("#contenedor_exito").css("display", "none")', 3000);
			
			if (ind_imprimir == 1) {
				imprimir_evolucion();
			}
		} else if (hdd_exito <= 0) {
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Error al guardar la evoluci&oacute;n");
		} else if (hdd_exito_formulacion_fm <= 0){
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Error al guardar la formulaci&oacute;n de medicamentos");
		} else {
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Error al guardar la solicitud de procedimientos y ex&aacute;menes");
		}
		reset_uploaders(); 
	} else if (hdd_tipo_guardar == 3) { //Permanece en el formulario
		if (hdd_exito > 0 && hdd_exito_formulacion_fm > 0 && hdd_exito_hc_procedimientos_solic > 0) {
			$("#contenedor_exito").css("display", "block");
			$("#contenedor_exito").html("Datos guardados correctamente");
			setTimeout('$("#contenedor_exito").css("display", "none")', 3000);
			
			if (ind_imprimir == 1) {
				imprimir_evolucion();
			}
		} else if (hdd_exito <= 0) {
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Error al guardar la evoluci&oacute;n");
		} else if (hdd_exito_formulacion_fm <= 0){
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Error al guardar la formulaci&oacute;n de medicamentos");
		} else {
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Error al guardar la solicitud de procedimientos y ex&aacute;menes");
		}
		reset_uploaders(); 
	}
	window.scrollTo(0, 0);
	$("#btn_imprimir").removeAttr("disabled");
	$("#btn_crear").removeAttr("disabled");
	$("#btn_finalizar").removeAttr("disabled");
}

function enviar_a_estados() {
	guardar_evolucion(2, 0);
	
	var params = "opcion=2&id_hc=" + $("#hdd_id_hc_consulta").val() +
				 "&id_admision=" + $("#hdd_id_admision").val();
	
	llamarAjax("evolucion_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1);");
}

function ajustar_div_optometria() {
	$("#div_consulta_optometria").height($("#HcFrame").contents().height() + 100);
}
