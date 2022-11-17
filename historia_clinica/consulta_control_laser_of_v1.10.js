/***********************************************/
/*Configuración del editor de texto enriquecido*/
/***********************************************/

if (CKEDITOR.env.ie && CKEDITOR.env.version < 9) {
	CKEDITOR.tools.enableHtml5Elements(document);
}

CKEDITOR.config.width = "auto";
CKEDITOR.config.height = 55;

var initCKEditorControl = (function(id_obj) {
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

/***********************************************/
/***********************************************/
/***********************************************/

function ajustar_textareas() {
	for (var i in CKEDITOR.instances) {
		(function(i){
			CKEDITOR.instances[i].setData(CKEDITOR.instances[i].getData());
		})(i);
	}
}

/**
 * Validar los campos de control laser
 */
function validar_control_laser_of(){
	var result = 0;
	
	var panel_1 = 0;
	var panel_2 = 0;
	
	$("#panel_control_oft_1").removeClass("borde_error_panel");
	$("#panel_control_oft_1 a").css({"color": "#5B5B5B"});
	$("#panel_control_oft_2").removeClass("borde_error_panel");
	$("#panel_control_oft_2 a").css({"color": "#5B5B5B"});
	
	$("#presion_intraocular_aplanatica_od").removeClass("borde_error");
	$("#presion_intraocular_aplanatica_oi").removeClass("borde_error");
	$("#cke_hallazgos_control_laser").removeClass("borde_error");
	$("#cke_diagnostico_control_laser_of").removeClass("borde_error");
	$("#txt_nombre_usuario_alt").removeClass("borde_error");
	
	$("#cmb_formula_gafas").removeClass("borde_error");
	
	if ($("#presion_intraocular_aplanatica_od").val() == "") {
		$("#presion_intraocular_aplanatica_od").addClass("borde_error");
		result = 1;
		panel_1 = 1;
	}
	if ($("#presion_intraocular_aplanatica_oi").val() == "") {
		$("#presion_intraocular_aplanatica_oi").addClass("borde_error");
		result = 1;
		panel_1 = 1;
	}
	if (CKEDITOR.instances.hallazgos_control_laser.getData() == "") {
		$("#cke_hallazgos_control_laser").addClass("borde_error");
		result = 1;
		panel_1 = 1;
	}
	
	if ($("#cmb_formula_gafas").val() == "" && $("#hdd_ind_optometria").val() == "1") {
		$("#cmb_formula_gafas").addClass("borde_error");
		result = 1;
		panel_2 = 1;
	}
	
	if ($("#hdd_usuario_anonimo").val() == "1" && $("#txt_nombre_usuario_alt").val() == ""){
		$("#txt_nombre_usuario_alt").addClass("borde_error");
		result = 1;
	}
	
	//Formulación de medicamentos
	if (!validar_formulacion_fm()) {
		result = 1;
		panel_2 = 1;
	}
	
	//Validación de diagnósticos
	var result_ciex = validar_diagnosticos_hc(1);
	if (result_ciex < 0) {
		result = result_ciex;
		panel_2 = 1;
	}
	
	return result;
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta SIN VALIDAR LOS CAMPOS
 */
function crear_control_laser_of(tipo, ind_imprimir) {
	switch (tipo) {
		case 1: //Finalizar consulta
		case 3: //Guardar desde la historia
		case 4: //Finalizar preconsulta
		case 5: //Finalizar consulta desde traslado
		case 6: //Finalizar preconsulta desde traslado
			$("#contenedor_error").css("display", "none");
			var resultado = validar_control_laser_of();
			if (resultado == 0) {
				editar_consulta_control_laser_of(tipo, ind_imprimir);
			} else {
				$("#contenedor_error").css("display", "block");
				if (resultado == -2) {
					$("#contenedor_error").html("Error - existen diagn&oacute;sticos duplicados");
				} else {
					$("#contenedor_error").html("Los campos marcados en rojo son obligatorios");
				}
				window.scroll(0, 0);
			}
			break;
			
		case 2: //Guardar cambios
			//Se validan duplicados de diagnósticos
			if (validar_duplicados_diagnosticos_hc() != -2) {
				editar_consulta_control_laser_of(tipo, ind_imprimir);
			} else {
				$("#contenedor_error").css("display", "block");
				$("#contenedor_error").html("Error - existen diagn&oacute;sticos duplicados");
				window.scroll(0, 0);
			}
			break;
	}
}

function imprimir_control_laser_of() {
	var params = "id_hc=" + $("#hdd_id_hc_consulta").val();
	
	llamarAjax("../historia_clinica/impresion_historia_clinica.php", params, "d_impresion_hc", "continuar_imprimir_control_laser_of();");
}

function continuar_imprimir_control_laser_of() {
	var ruta = $("#hdd_ruta_arch_hc_pdf").val();
	window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=consulta_control_laser_oftalmologia.pdf", "_blank");
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta 
 */
function editar_consulta_control_laser_of(tipo, ind_imprimir){
	var hdd_id_hc_consulta = $("#hdd_id_hc_consulta").val();
	var hdd_id_admision = $("#hdd_id_admision").val();
	var	presion_intraocular_aplanatica_od = $("#presion_intraocular_aplanatica_od").val();
	var	presion_intraocular_aplanatica_oi = $("#presion_intraocular_aplanatica_oi").val();
	var hallazgos_control_laser = CKEDITOR.instances.hallazgos_control_laser.getData();
	var	diagnostico_control_laser_of = CKEDITOR.instances.diagnostico_control_laser_of.getData();
	
	var	solicitud_examenes_control_laser = CKEDITOR.instances.solicitud_examenes_control_laser.getData();
	var	tratamiento_control_laser = CKEDITOR.instances.tratamiento_control_laser.getData();
	var	medicamentos_control_laser = $("#medicamentos_control_laser").val();
	var nombre_usuario_alt = $("#txt_nombre_usuario_alt").val();
	var ind_formula_gafas = $("#cmb_formula_gafas").val();
	
	var params = "opcion=1";
	
	//Para Diagnosticos
	var cant_ciex = $("#lista_tabla").val()
	params += "&cant_ciex=" + cant_ciex;
	for (i = 1; i <= cant_ciex; i++) {
	 	 var cod_ciex = $("#hdd_ciex_diagnostico_"+i).val();
	 	 var val_ojos = $("#valor_ojos_"+i).val();
	 	 if (cod_ciex != "") {
	 	 	params += "&cod_ciex_" + i + "=" + cod_ciex + "&val_ojos_" + i + "=" + val_ojos;
	 	 }
	}
	
	params += "&hdd_id_hc_consulta=" + hdd_id_hc_consulta +
			  "&hdd_id_admision=" + hdd_id_admision +
			  "&presion_intraocular_aplanatica_od=" + str_encode(presion_intraocular_aplanatica_od) +
			  "&presion_intraocular_aplanatica_oi=" + str_encode(presion_intraocular_aplanatica_oi) +
			  "&hallazgos_control_laser=" + str_encode(hallazgos_control_laser) +
			  "&diagnostico_control_laser_of=" + str_encode(diagnostico_control_laser_of) +
			  "&tipo_guardar=" + tipo +
			  "&solicitud_examenes_control_laser=" + str_encode(solicitud_examenes_control_laser) +
			  "&tratamiento_control_laser=" + str_encode(tratamiento_control_laser) +
			  "&medicamentos_control_laser=" + str_encode(medicamentos_control_laser) +
			  "&nombre_usuario_alt=" + str_encode(nombre_usuario_alt) +
			  "&ind_formula_gafas=" + ind_formula_gafas;
	
	//Formulación de medicamentos
	params += obtener_parametros_formulacion_fm();
	
	llamarAjax("consulta_control_laser_ajax_of.php", params, "guardar_control_laser_of", "validar_exito(" + ind_imprimir + ")");
}

function validar_exito(ind_imprimir) {
    var hdd_exito = $("#hdd_exito").val();
    var hdd_url_menu = $("#hdd_url_menu").val();
    var hdd_tipo_guardar = $("#hdd_tipo_guardar").val();
    var hdd_exito_formulacion_fm = $("#hdd_exito_formulacion_fm").val();
	
    if(hdd_tipo_guardar == 1 || hdd_tipo_guardar == 4) { //Cierra el formulario
    	$(".formulario").css("display", "none");
	    if (hdd_exito > 0 && hdd_exito_formulacion_fm > 0) {
	        $("#contenedor_exito").css("display", "block");
	        $("#contenedor_exito").html("Datos guardados correctamente");
	        setTimeout("enviar_credencial('" + hdd_url_menu + "')", 3000);
	    } else if (hdd_exito <= 0) {
	        $("#contenedor_error").css("display", "block");
	        $("#contenedor_error").html("Error al guardar la consulta");
		} else {
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Error al guardar la formulaci&oacute;n de medicamentos");
	    }
    } else if(hdd_tipo_guardar == 2 || hdd_tipo_guardar == 3) { //Permanece en el formulario
    	if (hdd_exito > 0 && hdd_exito_formulacion_fm > 0) {
	        $("#contenedor_exito").css("display", "block");
	        $("#contenedor_exito").html("Datos guardados correctamente");
	        setTimeout('$("#contenedor_exito").css("display", "none")', 3000);
			
			if (ind_imprimir == 1) {
				imprimir_control_laser_of();
			}
	    } else if (hdd_exito <= 0) {
	        $("#contenedor_error").css("display", "block");
	        $("#contenedor_error").html("Error al guardar la consulta");
		} else {
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Error al guardar la formulaci&oacute;n de medicamentos");
	    }
    }
	window.scrollTo(0, 0);
}

function enviar_a_estados() {
	crear_control_laser_of(2, 0);
	
	var params = "opcion=2&id_hc=" + $("#hdd_id_hc_consulta").val() +
				 "&id_admision=" + $("#hdd_id_admision").val() +
				 "&ind_preconsulta=" + $("#hdd_ind_preconsulta").val();
	
	llamarAjax("consulta_control_laser_ajax_of.php", params, "d_interno", "mostrar_formulario_flotante(1);");
}

function ajustar_div_optometria() {
	$("#div_consulta_optometria").height($("#HcFrame").contents().height() + 100);
}
