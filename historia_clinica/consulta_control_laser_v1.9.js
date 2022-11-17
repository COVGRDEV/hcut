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

var g_txt_np_na = "NP/NA";
var g_cmb_np_na = "69";

/**
 * Validar los campos de control laser
 */
function validar_control_laser() {
	var result = 0;
	var panel_1 = 0;
	var panel_2 = 0;
	var panel_3 = 0;
	var panel_4 = 0;
	
	$("#cke_anamnesis").removeClass("borde_error");
	$("#avsc_lejos_od").removeClass("borde_error");
	$("#avsc_cerca_od").removeClass("borde_error");
	$("#avsc_lejos_oi").removeClass("borde_error");
	$("#avsc_cerca_oi").removeClass("borde_error");
	$("#querato_cilindro_od").removeClass("borde_error");
	$("#querato_eje_od").removeClass("borde_error");
	$("#querato_mplano_od").removeClass("borde_error");
	$("#querato_cilindro_oi").removeClass("borde_error");
	$("#querato_eje_oi").removeClass("borde_error");
	$("#querato_mplano_oi").removeClass("borde_error");
	$("#avc_esfera_od").removeClass("borde_error");
	$("#avc_cilindro_od").removeClass("borde_error");
	$("#avc_eje_od").removeClass("borde_error");
	$("#avcc_lejos_od").removeClass("borde_error");
	$("#avcc_cerca_od").removeClass("borde_error");
	$("#avc_esfera_oi").removeClass("borde_error");
	$("#avc_cilindro_oi").removeClass("borde_error");
	$("#avc_eje_oi").removeClass("borde_error");
	$("#avcc_lejos_oi").removeClass("borde_error");
	$("#avcc_cerca_oi").removeClass("borde_error");
	$("#cke_diagnostico_control_laser").removeClass("borde_error");
	
	if (CKEDITOR.instances.anamnesis.getData() == "") {
		$("#cke_anamnesis").addClass("borde_error");
		result = 1;
		panel_1 = 1;
	}
	
	if ($("#avsc_lejos_od").val() == ""){ $("#avsc_lejos_od").addClass("borde_error"); result = 1; panel_1 = 1; }
	if ($("#avsc_cerca_od").val() == ""){ $("#avsc_cerca_od").addClass("borde_error"); result = 1; panel_1 = 1; }
	if ($("#avsc_lejos_oi").val() == ""){ $("#avsc_lejos_oi").addClass("borde_error"); result = 1; panel_1 = 1; }
	if ($("#avsc_cerca_oi").val() == ""){ $("#avsc_cerca_oi").addClass("borde_error"); result = 1; panel_1 = 1; }
	if ($("#querato_cilindro_od").val() == ""){ $("#querato_cilindro_od").addClass("borde_error"); result = 1; panel_2 = 1; }
	if ($("#querato_eje_od").val() == ""){ $("#querato_eje_od").addClass("borde_error"); result = 1; panel_2 = 1; }
	if ($("#querato_mplano_od").val() == ""){ $("#querato_mplano_od").addClass("borde_error"); result = 1; panel_2 = 1; }
	if ($("#querato_cilindro_oi").val() == ""){ $("#querato_cilindro_oi").addClass("borde_error"); result = 1; panel_2 = 1; }
	if ($("#querato_eje_oi").val() == ""){ $("#querato_eje_oi").addClass("borde_error"); result = 1; panel_2 = 1; }
	if ($("#querato_mplano_oi").val() == ""){ $("#querato_mplano_oi").addClass("borde_error"); result = 1; panel_2 = 1; }
	if ($("#avc_esfera_od").val() == ""){ $("#avc_esfera_od").addClass("borde_error"); result = 1; panel_3 = 1; }
	if ($("#avc_cilindro_od").val() == ""){ $("#avc_cilindro_od").addClass("borde_error"); result = 1; panel_3 = 1; }
	if ($("#avc_eje_od").val() == ""){ $("#avc_eje_od").addClass("borde_error"); result = 1; panel_3 = 1; }
	if ($("#avcc_lejos_od").val() == ""){ $("#avcc_lejos_od").addClass("borde_error"); result = 1; panel_3 = 1; }
	if ($("#avcc_cerca_od").val() == ""){ $("#avcc_cerca_od").addClass("borde_error"); result = 1; panel_3 = 1; }
	if ($("#avc_esfera_oi").val() == ""){ $("#avc_esfera_oi").addClass("borde_error"); result = 1; panel_3 = 1; }
	if ($("#avc_cilindro_oi").val() == ""){ $("#avc_cilindro_oi").addClass("borde_error"); result = 1; panel_3 = 1; }
	if ($("#avc_eje_oi").val() == ""){ $("#avc_eje_oi").addClass("borde_error"); result = 1; panel_3 = 1; }
	if ($("#avcc_lejos_oi").val() == ""){ $("#avcc_lejos_oi").addClass("borde_error"); result = 1; panel_3 = 1; }
	if ($("#avcc_cerca_oi").val() == ""){ $("#avcc_cerca_oi").addClass("borde_error"); result = 1; panel_3 = 1; }
	
	//Validación de diagnósticos
	var result_ciex = validar_diagnosticos_hc(0);
	if (result_ciex < 0) {
		result = result_ciex;
		panel_4 = 1;
	}
	
	return result;
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta SIN VALIDAR LOS CAMPOS
 */
function crear_control_laser(tipo, ind_imprimir) {
	switch (tipo) {
		case 1: //Finalizar consulta
		case 3: //Guardar desde historia
		case 4: //Finalizar consulta desde traslado
			$("#contenedor_error").css("display", "none");
			var resultado = validar_control_laser();
			if (resultado == 0) {
				editar_consulta_control_laser(tipo, ind_imprimir);
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
				editar_consulta_control_laser(tipo, ind_imprimir);
			} else {
				$("#contenedor_error").css("display", "block");
				$("#contenedor_error").html("Error - existen diagn&oacute;sticos duplicados");
				window.scroll(0, 0);
			}
			break;
	}
}

function imprimir_control_laser() {
	var params = "id_hc=" + $("#hdd_id_hc_consulta").val();
	
	llamarAjax("../historia_clinica/impresion_historia_clinica.php", params, "d_impresion_hc", "continuar_imprimir_control_laser();");
}

function continuar_imprimir_control_laser() {
	var ruta = $("#hdd_ruta_arch_hc_pdf").val();
	window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=consulta_control_laser_optometria.pdf", "_blank");
}


/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta 
 */
function editar_consulta_control_laser(tipo, ind_imprimir){
	var hdd_id_hc_consulta = $("#hdd_id_hc_consulta").val();
	var hdd_id_admision = $("#hdd_id_admision").val();
	var anamnesis = CKEDITOR.instances.anamnesis.getData();
	var avsc_lejos_od = $("#avsc_lejos_od").val();
	var avsc_cerca_od = $("#avsc_cerca_od").val();
	var avsc_lejos_oi = $("#avsc_lejos_oi").val();
	var avsc_cerca_oi = $("#avsc_cerca_oi").val();
	var querato_cilindro_od = $("#querato_cilindro_od").val();
	var querato_eje_od = $("#querato_eje_od").val();
	var querato_mplano_od = $("#querato_mplano_od").val();
	var querato_cilindro_oi = $("#querato_cilindro_oi").val();
	var querato_eje_oi = $("#querato_eje_oi").val();
	var querato_mplano_oi = $("#querato_mplano_oi").val();
	var avc_esfera_od = $("#avc_esfera_od").val();
	var avc_cilindro_od = $("#avc_cilindro_od").val();
	var avc_eje_od = $("#avc_eje_od").val();
	var avcc_lejos_od = $("#avcc_lejos_od").val();
	var avcc_adicion_od = $("#avcc_adicion_od").val();
	var avcc_cerca_od = $("#avcc_cerca_od").val();
	var avc_esfera_oi = $("#avc_esfera_oi").val();
	var avc_cilindro_oi = $("#avc_cilindro_oi").val();
	var avc_eje_oi = $("#avc_eje_oi").val();
	var avcc_lejos_oi = $("#avcc_lejos_oi").val();
	var avcc_adicion_oi = $("#avcc_adicion_oi").val();
	var avcc_cerca_oi = $("#avcc_cerca_oi").val();
	var diagnostico_control_laser = CKEDITOR.instances.diagnostico_control_laser.getData();
	var txt_observaciones_avc = CKEDITOR.instances.txt_observaciones_avc.getData();

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
	
	params += "&hdd_id_hc_consulta="+ hdd_id_hc_consulta +
			  "&hdd_id_admision=" + hdd_id_admision +
			  "&anamnesis=" + str_encode(anamnesis) +
			  "&avsc_lejos_od=" + str_encode(avsc_lejos_od) +
			  "&avsc_cerca_od=" + str_encode(avsc_cerca_od) +
			  "&avsc_lejos_oi=" + str_encode(avsc_lejos_oi) +
			  "&avsc_cerca_oi=" + str_encode(avsc_cerca_oi) +
			  "&querato_cilindro_od=" + str_encode(querato_cilindro_od) +
			  "&querato_eje_od=" + str_encode(querato_eje_od) +
			  "&querato_mplano_od=" + str_encode(querato_mplano_od) +
			  "&querato_cilindro_oi=" + str_encode(querato_cilindro_oi) +
			  "&querato_eje_oi=" + str_encode(querato_eje_oi) +
			  "&querato_mplano_oi=" + str_encode(querato_mplano_oi) +
			  "&avc_esfera_od=" + str_encode(avc_esfera_od) +
			  "&avc_cilindro_od=" + str_encode(avc_cilindro_od) +
			  "&avc_eje_od=" + str_encode(avc_eje_od) +
			  "&avcc_lejos_od=" + str_encode(avcc_lejos_od) +
			  "&avcc_adicion_od=" + str_encode(avcc_adicion_od) +
			  "&avcc_cerca_od=" + str_encode(avcc_cerca_od) +
			  "&avc_esfera_oi=" + str_encode(avc_esfera_oi) +
			  "&avc_cilindro_oi=" + str_encode(avc_cilindro_oi) +
			  "&avc_eje_oi=" + str_encode(avc_eje_oi) +
			  "&avcc_lejos_oi=" + str_encode(avcc_lejos_oi) +
			  "&avcc_adicion_oi=" + str_encode(avcc_adicion_oi) +
			  "&avcc_cerca_oi=" + str_encode(avcc_cerca_oi) +
			  "&diagnostico_control_laser=" + str_encode(diagnostico_control_laser) +
			  "&observaciones_avc=" + str_encode(txt_observaciones_avc) +
			  "&tipo_guardar=" + tipo;
	
	llamarAjax("consulta_control_laser_ajax.php", params, "guardar_control_laser", "validar_exito(" + ind_imprimir + ")");
}

function validar_exito(ind_imprimir) {
    var hdd_exito = $("#hdd_exito").val();
    var hdd_url_menu = $("#hdd_url_menu").val();
    var hdd_tipo_guardar = $("#hdd_tipo_guardar").val();
    
    if(hdd_tipo_guardar==1){//Cierra el formulario
    	$(".formulario").css("display", "none");
	    if (hdd_exito > 0) {
	        $("#contenedor_exito").css("display", "block");
	        $("#contenedor_exito").html("Datos guardados correctamente");
	        setTimeout("enviar_credencial('" + hdd_url_menu + "')", 3000);
	    } else {
	        $("#contenedor_error").css("display", "block");
	        $("#contenedor_error").html("Error al guardar usuarios");
	        setTimeout("enviar_credencial('" + hdd_url_menu + "')", 3000);
	    }
    } else if(hdd_tipo_guardar==2 || hdd_tipo_guardar==3){//Permanece en el formulario
    	if (hdd_exito > 0) {
	        $("#contenedor_exito").css("display", "block");
	        $("#contenedor_exito").html("Datos guardados correctamente");
	        setTimeout('$("#contenedor_exito").css("display", "none")', 3000);
			
			if (ind_imprimir == 1) {
				imprimir_control_laser();
			}
	    } else {
	        $("#contenedor_error").css("display", "block");
	        $("#contenedor_error").html("Error al guardar usuarios");
	        setTimeout('$("#contenedor_error").css("display", "none")', 3000);
	    }
    }
	window.scrollTo(0, 0);
}

function validar_np_na(campo_1, campo_2, campo_3, campo_4, combo_1, combo_2) {
	if ($("#" + campo_1).val() == g_txt_np_na) {
		if (campo_2 != "") {
			$("#" + campo_2).val(g_txt_np_na);
		}
		if (campo_3 != "") {
			$("#" + campo_3).val(g_txt_np_na);
		}
		if (campo_4 != "") {
			$("#" + campo_4).val(g_txt_np_na);
		}
		if (combo_1 != "") {
			$("#" + combo_1).val(g_cmb_np_na);
		}
		if (combo_2 != "") {
			$("#" + combo_2).val(g_cmb_np_na);
		}
	} else if ($("#" + combo_1).val() == g_cmb_np_na) {
		if (combo_2 != "") {
			$("#" + combo_2).val(g_cmb_np_na);
		}
	}
}

function enviar_a_estados() {
	crear_control_laser(2, 0);
	
	var params = "opcion=2&id_hc=" + $("#hdd_id_hc_consulta").val() +
				 "&id_admision=" + $("#hdd_id_admision").val();
	
	llamarAjax("consulta_control_laser_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1);");
}

function generar_formula_gafas(observaciones, fecha_hc, nombre_paciente, nombre_profesional, esfera_od, cilindro_od, eje_od, adicion_od, esfera_oi, cilindro_oi, eje_oi, adicion_oi, id_admision) {
	var esfera_od = $("#" + esfera_od).val();
	var cilindro_od = $("#" + cilindro_od).val();
	var eje_od = $("#" + eje_od).val();
	var adicion_od = $("#" + adicion_od).val();
	
	var esfera_oi = $("#" + esfera_oi).val();
	var cilindro_oi = $("#" + cilindro_oi).val();
	var eje_oi = $("#" + eje_oi).val();
	var adicion_oi = $("#" + adicion_oi).val();
	
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
	
	llamarAjax("../historia_clinica/formula_gafas_ajax.php", params, "d_imprimir_formula", "imprimir_formula_gafas_cont();");
}


function imprimir_formula_gafas_cont() {
	var ruta = $("#hdd_ruta_formula_gafas_pdf").val();
	window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=formula_gafas.pdf", "_blank");
}
