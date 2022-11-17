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

function ocultar_panels_dermatologia() {
	$("#panel2-2").removeClass("active");
	$("#panel2-3").removeClass("active");
}

function calcular_imc() {
	if ($("#txt_peso").val() != "" && $("#txt_talla").val() != "") {
		var imc = 10 * parseFloat($("#txt_peso").val()) / 
				  Math.pow(parseFloat($("#txt_talla").val()) / 100, 2);
		imc = Math.round(imc) / 10;
		$("#txt_imc").val(imc);
	} else {
		$("#txt_imc").val("");
	}
}

function calcular_fg() {
	var fg_total = parseInt("0" + $("#cmb_fg_labio_superior").val(), 10) +
				   parseInt("0" + $("#cmb_fg_mejilla").val(), 10) +
				   parseInt("0" + $("#cmb_fg_torax").val(), 10) +
				   parseInt("0" + $("#cmb_fg_espalda_superior").val(), 10) +
				   parseInt("0" + $("#cmb_fg_espalda_inferior").val(), 10) +
				   parseInt("0" + $("#cmb_fg_abdomen_superior").val(), 10) +
				   parseInt("0" + $("#cmb_fg_abdomen_inferior").val(), 10) +
				   parseInt("0" + $("#cmb_fg_brazo").val(), 10) +
				   parseInt("0" + $("#cmb_fg_muslo").val(), 10);
	
	$("#sp_fg_total").html(fg_total > 0 ? fg_total : "");
}

function validar_dermatologia(){
	var result = 0;
	
	var panel_1 = 0;
	var panel_2 = 0;
	var panel_3 = 0;
	
	$("#panel_derma_1").removeClass("borde_error_panel");
	$("#panel_derma_1 a").css({"color": "#5B5B5B"});
	$("#panel_derma_2").removeClass("borde_error_panel");
	$("#panel_derma_2 a").css({"color": "#5B5B5B"});
	$("#panel_derma_3").removeClass("borde_error_panel");
	$("#panel_derma_3 a").css({"color": "#5B5B5B"});
	
	$("#txt_peso").removeClass("borde_error");
	$("#txt_talla").removeClass("borde_error");
	
	if ($("#txt_peso").val() == "") {
		$("#txt_peso").addClass("borde_error");
		result = 1;
		panel_2 = 1;
	}
	if ($("#txt_talla").val() == "") {
		$("#txt_talla").addClass("borde_error");
		result = 1;
		panel_2 = 1;
	}
	
	//Formulación de medicamentos
	if (!validar_formulacion_fm()) {
		result = 1;
		panel_3 = 1;
	}
	
	//Validación de diagnósticos
	var result_ciex = validar_diagnosticos_hc_ojos(1, false);
	if (result_ciex < 0) {
		result = result_ciex;
		panel_3 = 1;
	} else {
		//Validación de procedimientos solicitados
		var result_cups_solic = validar_hc_procedimientos_solic();
		if (result_cups_solic < 0) {
			result = result_cups_solic;
			panel_3 = 1;
		}
	}
	
	if (panel_1 == 1) {
	   $("#panel_derma_1").addClass("borde_error_panel");
	   $("#panel_derma_1 a").css({"color": "#FF002A"});
	}
	if (panel_2 == 1) {
	   $("#panel_derma_2").addClass("borde_error_panel");
	   $("#panel_derma_2 a").css({"color": "#FF002A"});
	}
	if (panel_3 == 1) {
	   $("#panel_derma_3").addClass("borde_error_panel");
	   $("#panel_derma_3 a").css({"color": "#FF002A"});
	}
	
	return result;
}

function crear_dermatologia(tipo, ind_imprimir) {
	switch (tipo) {
		case 1: //Finalizar consulta
		case 3: //Guardar desde la historia
		case 4: //Finalizar consulta desde traslado
			$("#contenedor_error").css("display", "none");
			var resultado = validar_dermatologia();
			if (resultado == 0) {
				editar_consulta_dermatologia(tipo, ind_imprimir);
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
			}
			break;
			
		case 2: //Guardar cambios
			//Se validan duplicados de diagnósticos
			if (validar_duplicados_diagnosticos_hc() != -2) {
				if (validar_hc_procedimientos_solic() != -3) {
					editar_consulta_dermatologia(tipo, ind_imprimir);
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

function imprimir_dermatologia() {
	var params = "id_hc=" + $("#hdd_id_hc_consulta").val();
	
	llamarAjax("../historia_clinica/impresion_historia_clinica.php", params, "d_impresion_hc", "continuar_imprimir_dermatologia();");
}

function continuar_imprimir_dermatologia() {
	var ruta = $("#hdd_ruta_arch_hc_pdf").val();
	window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=consulta_dermatologia.pdf", "_blank");
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta 
 */
function editar_consulta_dermatologia(tipo, ind_imprimir){
	var params = "opcion=1" +
				 "&id_hc_consulta=" + $("#hdd_id_hc_consulta").val() +
				 "&id_admision=" + $("#hdd_id_admision").val() +
				 "&peso=" + $("#txt_peso").val() +
				 "&talla=" + $("#txt_talla").val() +
				 "&id_ludwig=" + $("#cmb_ludwig").val() +
				 "&fg_labio_superior=" + $("#cmb_fg_labio_superior").val() +
				 "&fg_mejilla=" + $("#cmb_fg_mejilla").val() +
				 "&fg_torax=" + $("#cmb_fg_torax").val() +
				 "&fg_espalda_superior=" + $("#cmb_fg_espalda_superior").val() +
				 "&fg_espalda_inferior=" + $("#cmb_fg_espalda_inferior").val() +
				 "&fg_abdomen_superior=" + $("#cmb_fg_abdomen_superior").val() +
				 "&fg_abdomen_inferior=" + $("#cmb_fg_abdomen_inferior").val() +
				 "&fg_brazo=" + $("#cmb_fg_brazo").val() +
				 "&fg_muslo=" + $("#cmb_fg_muslo").val() +
				 "&descripcion_cara=" + str_encode(CKEDITOR.instances.txt_descripcion_cara.getData()) +
				 "&descripcion_cuerpo=" + str_encode(CKEDITOR.instances.txt_descripcion_cuerpo.getData()) +
				 "&desc_antecedentes_medicos=" + str_encode(CKEDITOR.instances.txt_desc_antecedentes_medicos.getData()) +
				 "&diagnostico_dermat=" + str_encode(CKEDITOR.instances.txt_diagnostico_dermat.getData()) +
				 "&solicitud_examenes=" + str_encode(CKEDITOR.instances.txt_solicitud_examenes.getData()) +
				 "&tratamiento_dermat=" + str_encode(CKEDITOR.instances.txt_tratamiento_dermat.getData()) +
				 "&tipo_guardar=" + tipo;
	
	//Antecedentes medicos
	var cant_antecedentes = parseInt($("#hdd_cant_antecedentes").val(), 10);
	var array_antecedentes_medicos_ids = new Array();
	var array_antecedentes_medicos_val = new Array();
	for (var i = 0; i < cant_antecedentes; i++) {
		array_antecedentes_medicos_ids.push($("#hdd_ant_med_" + i).val());
		array_antecedentes_medicos_val.push($("#chk_ant_med_" + i).is(":checked"));
	}
	
	params += "&array_antecedentes_medicos_ids=" + array_antecedentes_medicos_ids +
			  "&array_antecedentes_medicos_val=" + array_antecedentes_medicos_val;
	
	//Para Diagnosticos
	var cant_ciex = $("#lista_tabla").val()
	params += "&cant_ciex=" + cant_ciex;
	for (i = 1; i <= cant_ciex; i++) {
		var cod_ciex = $("#hdd_ciex_diagnostico_" + i).val();
		if (cod_ciex != "") {
			params += "&cod_ciex_" + i + "=" + cod_ciex + "&val_ojos_" + i + "=";
		}
	}
	
	//Solicitud de procedimientos
	params += obtener_parametros_proc_solic();
	
	//Formulación de medicamentos
	params += obtener_parametros_formulacion_fm();
	
	llamarAjax("consulta_dermatologia_ajax.php", params, "guardar_dermatologia", "validar_exito(" + ind_imprimir + ")");
}

function validar_exito(ind_imprimir) {
    var hdd_exito = $("#hdd_exito").val();
    var hdd_url_menu = $("#hdd_url_menu").val();
    var hdd_tipo_guardar = $("#hdd_tipo_guardar").val();
    var hdd_exito_formulacion_fm = $("#hdd_exito_formulacion_fm").val();
	var hdd_exito_hc_procedimientos_solic = $("#hdd_exito_hc_procedimientos_solic").val();
	
    if(hdd_tipo_guardar == 1 || hdd_tipo_guardar == 4) { //Cierra el formulario
	    if (hdd_exito > 0 && hdd_exito_formulacion_fm > 0 && hdd_exito_hc_procedimientos_solic > 0) {
	        $("#contenedor_exito").css("display", "block");
	        $("#contenedor_exito").html("Datos guardados correctamente");
	        setTimeout("enviar_credencial('" + hdd_url_menu + "')", 3000);
	    } else if (hdd_exito <= 0) {
	        $("#contenedor_error").css("display", "block");
	        $("#contenedor_error").html("Error al guardar la consulta");
		} else if (hdd_exito_formulacion_fm <= 0){
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Error al guardar la formulaci&oacute;n de medicamentos");
		} else {
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Error al guardar la solicitud de procedimientos y ex&aacute;menes");
		}
    } else if(hdd_tipo_guardar == 2 || hdd_tipo_guardar == 3) { //Permanece en el formulario
    	if (hdd_exito > 0 && hdd_exito_formulacion_fm > 0 && hdd_exito_hc_procedimientos_solic > 0) {
	        $("#contenedor_exito").css("display", "block");
	        $("#contenedor_exito").html("Datos guardados correctamente");
	        setTimeout('$("#contenedor_exito").css("display", "none")', 3000);
			
			if (ind_imprimir == 1) {
				imprimir_dermatologia();
			}
	    } else if (hdd_exito <= 0) {
	        $("#contenedor_error").css("display", "block");
	        $("#contenedor_error").html("Error al guardar la consulta");
		} else if (hdd_exito_formulacion_fm <= 0){
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Error al guardar la formulaci&oacute;n de medicamentos");
		} else {
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Error al guardar la solicitud de procedimientos y ex&aacute;menes");
		}
    }
	window.scrollTo(0, 0);
}

function enviar_a_estados() {
	crear_dermatologia(2, 0);
	
	var params = "opcion=2&id_hc=" + $("#hdd_id_hc_consulta").val() +
				 "&id_admision=" + $("#hdd_id_admision").val() +
				 "&ind_preconsulta=" + $("#hdd_ind_preconsulta").val();
	
	llamarAjax("consulta_dermatologia_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1);");
}
