/***********************************************/
/*Configuración del editor de texto enriquecido*/
/***********************************************/

if (CKEDITOR.env.ie && CKEDITOR.env.version < 9) {
	CKEDITOR.tools.enableHtml5Elements(document);
}

CKEDITOR.config.width = 'auto';
CKEDITOR.config.height = 55;

var initCKEditorPreQx = (function(id_obj) {
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


var arr_textarea_ids = [];
function ajustar_textareas() {
	for (i = 0; i < arr_textarea_ids.length; i++) {
		$("#" + arr_textarea_ids[i]).trigger("input");
	}
	
	for (var i in CKEDITOR.instances) {
		(function(i){
			CKEDITOR.instances[i].setData(CKEDITOR.instances[i].getData());
		})(i);
	}
}

/**
 * Validar los campos de control prequirurgico laser de oftalmologia
 */
function validar_preqx_laser_of() {
	var result = 0;
	var panel_1 = 0;
	var panel_2 = 0;
	var panel_3 = 0;
	
	$("#panel_laser_oft_1").removeClass("borde_error_panel");
	$("#panel_laser_oft_1 a").css({"color": "#5B5B5B"});
	$("#panel_laser_oft_2").removeClass("borde_error_panel");
	$("#panel_laser_oft_2 a").css({"color": "#5B5B5B"});
	$("#panel_laser_oft_3").removeClass("borde_error_panel");
	$("#panel_laser_oft_3 a").css({"color": "#5B5B5B"});
	
	$('#cke_preqx_laser_subjetivo').removeClass("borde_error");
	$('#cke_preqx_laser_biomiocroscopia').removeClass("borde_error");
	$('#presion_intraocular_od').removeClass("borde_error");
	$('#presion_intraocular_oi').removeClass("borde_error");
	$('#fondo_ojo_nervio_optico_od').removeClass("borde_error");
	$('#fondo_ojo_macula_od').removeClass("borde_error");
	$('#fondo_ojo_periferia_od').removeClass("borde_error");
	$('#fondo_ojo_vitreo_od').removeClass("borde_error");
	$('#fondo_ojo_nervio_optico_oi').removeClass("borde_error");
	$('#fondo_ojo_macula_oi').removeClass("borde_error");
	$('#fondo_ojo_periferia_oi').removeClass("borde_error");
	$('#fondo_ojo_vitreo_oi').removeClass("borde_error");
	$('#cke_preqx_laser_plan').removeClass("borde_error");
	$('#cke_diagnostico_preqx_laser_of').removeClass("borde_error");
	
	//Para diagnosticos pintar normal
	$('#ciex_diagnostico_1').removeClass("borde_error");
	$('#valor_ojos_1').removeClass("borde_error");
	
	var cant_ciex = $('#lista_tabla').val()
	for (i=1;i<=cant_ciex;i++) {
	 	 var cod_ciex = $("#hdd_ciex_diagnostico_"+i).val();
	 	 var val_ojos = $("#valor_ojos_"+i).val();
	 	 $("#valor_ojos_"+i).removeClass("borde_error");
	}
	  
	//Para diagnosticos pintar error
	if($('#hdd_ciex_diagnostico_1').val()==''){
		$("#ciex_diagnostico_1").addClass("borde_error");
		result=1;
		panel_3=1;
	}
	if($('#valor_ojos_1').val()==''){
		$("#valor_ojos_1").addClass("borde_error");
		result=1;
		panel_3=1;
	}
	var cant_ciex = $('#lista_tabla').val();
	for (i = 1; i <= cant_ciex; i++) {
	 	 var cod_ciex = $("#hdd_ciex_diagnostico_" + i).val();
	 	 var val_ojos = $("#valor_ojos_" + i).val();
	 	 if (cod_ciex != '' && val_ojos == '') {
	 	 	$("#valor_ojos_"+i).addClass("borde_error");
	 	 	result = 1;
	 	 	panel_3 = 1;
	 	 }
	}
	
	if (bol_od) {
		if($('#presion_intraocular_od').val()==''){$('#presion_intraocular_od').addClass("borde_error");result=1; panel_1=1;}
		if($('#fondo_ojo_nervio_optico_od').val()==''){$('#fondo_ojo_nervio_optico_od').addClass("borde_error"); result=1; panel_2=1;}
		if($('#fondo_ojo_macula_od').val()==''){$('#fondo_ojo_macula_od').addClass("borde_error"); result=1; panel_2=1;}
		if($('#fondo_ojo_periferia_od').val()==''){$('#fondo_ojo_periferia_od').addClass("borde_error"); result=1; panel_2=1;}
		if($('#fondo_ojo_vitreo_od').val()==''){$('#fondo_ojo_vitreo_od').addClass("borde_error"); result=1; panel_2=1;}
	}
	
	if (bol_oi) {
		if($('#presion_intraocular_oi').val()==''){$('#presion_intraocular_oi').addClass("borde_error"); result=1; panel_1=1;}
		if($('#fondo_ojo_nervio_optico_oi').val()==''){$('#fondo_ojo_nervio_optico_oi').addClass("borde_error"); result=1; panel_2=1;}
		if($('#fondo_ojo_macula_oi').val()==''){$('#fondo_ojo_macula_oi').addClass("borde_error"); result=1; panel_2=1;}
		if($('#fondo_ojo_periferia_oi').val()==''){$('#fondo_ojo_periferia_oi').addClass("borde_error"); result=1; panel_2=1;}
		if($('#fondo_ojo_vitreo_oi').val()==''){$('#fondo_ojo_vitreo_oi').addClass("borde_error"); result=1; panel_2=1;}
	}
	
	if (CKEDITOR.instances.preqx_laser_subjetivo.getData() == '') {
		$('#cke_preqx_laser_subjetivo').addClass("borde_error");
		result = 1;
		panel_1 = 1;
	}
	if (CKEDITOR.instances.preqx_laser_biomiocroscopia.getData() == '') {
		$('#cke_preqx_laser_biomiocroscopia').addClass("borde_error");
		result = 1;
		panel_1 = 1;
	}
	if (CKEDITOR.instances.preqx_laser_plan.getData() == '') {
		$('#cke_preqx_laser_plan').addClass("borde_error");
		result = 1; panel_2 = 1;
	}
	
	if (panel_1 == 1) {
	   $("#panel_laser_oft_1").addClass("borde_error_panel");
	   $("#panel_laser_oft_1 a").css({"color": "#FF002A"});
	}
	if (panel_2 == 1) {
	   $("#panel_laser_oft_2").addClass("borde_error_panel");
	   $("#panel_laser_oft_2 a").css({"color": "#FF002A"});
	}
	if (panel_3 == 1) {
	   $("#panel_laser_oft_3").addClass("borde_error_panel");
	   $("#panel_laser_oft_3 a").css({"color": "#FF002A"});
	}
	
	return result;
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta SIN VALIDAR LOS CAMPOS
 */
function crear_preqx_laser_of(tipo, ind_imprimir) {
	switch (tipo) {
		case 1: //Finalizar consulta
		case 3: //Guardar desde historia
		case 4: //Finalizar preconsulta
		case 5: //Finalizar consulta desde traslado
		case 6: //Finalizar preconsulta desde traslado
       		$("#contenedor_error").css("display", "none");
       		/*if (validar_preqx_laser_of() == 0) {
				editar_consulta_preqx_laser_of(tipo);
				return false;
			} else {
				$("#contenedor_error").css("display", "block");
				$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
				return false;
			}*/
			editar_consulta_preqx_laser_of(tipo);
			break;
		case 2: //Guardar cambios
			editar_consulta_preqx_laser_of(tipo);
			break;
	}
	
	if (ind_imprimir == 1) {
		setTimeout("imprimir_preqx_laser_of()", 1000);
	}
}

function imprimir_preqx_laser_of() {
	var params = "id_hc=" + $("#hdd_id_hc_consulta").val();
	
	llamarAjax("../historia_clinica/impresion_historia_clinica.php", params, "d_impresion_hc", "imprSelec(\"d_impresion_hc\")");
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta 
 */
function editar_consulta_preqx_laser_of(tipo) {
	var hdd_id_hc_consulta = $('#hdd_id_hc_consulta').val();
	var hdd_id_admision = $('#hdd_id_admision').val();
	var id_ojo = $("#cmb_ojo_laser").val();
	var preqx_laser_subjetivo = str_encode(CKEDITOR.instances.preqx_laser_subjetivo.getData());
	var preqx_laser_biomiocroscopia = str_encode(CKEDITOR.instances.preqx_laser_biomiocroscopia.getData());
	var presion_intraocular_od = $('#presion_intraocular_od').val();
	var presion_intraocular_oi = $('#presion_intraocular_oi').val();
	var fondo_ojo_nervio_optico_od = str_encode($('#fondo_ojo_nervio_optico_od').val());
	var fondo_ojo_macula_od = str_encode($('#fondo_ojo_macula_od').val());
	var fondo_ojo_periferia_od = str_encode($('#fondo_ojo_periferia_od').val());
	var fondo_ojo_vitreo_od = str_encode($('#fondo_ojo_vitreo_od').val());
	var fondo_ojo_nervio_optico_oi = str_encode($('#fondo_ojo_nervio_optico_oi').val());
	var fondo_ojo_macula_oi = str_encode($('#fondo_ojo_macula_oi').val());
	var fondo_ojo_periferia_oi = str_encode($('#fondo_ojo_periferia_oi').val());
	var fondo_ojo_vitreo_oi = str_encode($('#fondo_ojo_vitreo_oi').val());
	var preqx_laser_plan = str_encode(CKEDITOR.instances.preqx_laser_plan.getData());
	var diagnostico_preqx_laser_of = str_encode(CKEDITOR.instances.diagnostico_preqx_laser_of.getData());
	var solicitud_examenes_preqx_laser = str_encode(CKEDITOR.instances.solicitud_examenes_preqx_laser.getData());
	var tratamiento_preqx_laser = str_encode(CKEDITOR.instances.tratamiento_preqx_laser.getData());
	var medicamentos_preqx_laser = str_encode($('#medicamentos_preqx_laser').val());
	
	var params='opcion=1';
	
	//Para Diagnosticos
	var cant_ciex = $('#lista_tabla').val()
	params += '&cant_ciex=' + cant_ciex;
	for (i = 1; i <= cant_ciex; i++) {
	 	 var cod_ciex = $("#hdd_ciex_diagnostico_" + i).val();
	 	 var val_ojos = $("#valor_ojos_" + i).val();
	 	 if (cod_ciex != '') {
	 	 	params += '&cod_ciex_' + i + '=' + cod_ciex + '&val_ojos_' + i + '=' + val_ojos;
	 	 }
	}
	
	params += '&hdd_id_hc_consulta='+ hdd_id_hc_consulta +
			  '&hdd_id_admision=' + hdd_id_admision +
			  '&id_ojo=' + id_ojo +
			  '&preqx_laser_subjetivo='+ preqx_laser_subjetivo +
			  '&preqx_laser_biomiocroscopia='+ preqx_laser_biomiocroscopia +
			  '&presion_intraocular_od='+ presion_intraocular_od +
			  '&presion_intraocular_oi='+ presion_intraocular_oi +
			  '&fondo_ojo_nervio_optico_od='+ fondo_ojo_nervio_optico_od +
			  '&fondo_ojo_macula_od='+ fondo_ojo_macula_od +
			  '&fondo_ojo_periferia_od='+ fondo_ojo_periferia_od +
			  '&fondo_ojo_vitreo_od='+ fondo_ojo_vitreo_od +
			  '&fondo_ojo_nervio_optico_oi='+ fondo_ojo_nervio_optico_oi +
			  '&fondo_ojo_macula_oi='+ fondo_ojo_macula_oi +
			  '&fondo_ojo_periferia_oi='+ fondo_ojo_periferia_oi +
			  '&fondo_ojo_vitreo_oi='+ fondo_ojo_vitreo_oi +
			  '&preqx_laser_plan='+ preqx_laser_plan +
			  '&diagnostico_preqx_laser_of='+ diagnostico_preqx_laser_of +
			  '&tipo_guardar=' + tipo +
			  '&solicitud_examenes_preqx_laser=' + solicitud_examenes_preqx_laser +
			  '&tratamiento_preqx_laser=' + tratamiento_preqx_laser +
			  '&medicamentos_preqx_laser=' + medicamentos_preqx_laser;
	
	llamarAjax("consulta_preqx_laser_ajax_of.php", params, "guardar_preqx_laser", "validar_exito()");
}

function validar_exito() {
    var hdd_exito = $('#hdd_exito').val();
    var hdd_url_menu = $('#hdd_url_menu').val();
    var hdd_tipo_guardar = $('#hdd_tipo_guardar').val();
	
    if (hdd_tipo_guardar == 1 || hdd_tipo_guardar == 4) { //Cierra el formulario
    	$('.formulario').css('display', 'none');
	    if (hdd_exito > 0) {
	        $("#contenedor_exito").css("display", "block");
	        $('#contenedor_exito').html('Datos guardados correctamente');
	        setTimeout("enviar_credencial('"+hdd_url_menu+"')", 3000);
	    }
	    else {
	        $("#contenedor_error").css("display", "block");
	        $('#contenedor_error').html('Error al guardar usuarios');
	        setTimeout("enviar_credencial('"+hdd_url_menu+"')", 3000);
	    }
    } else if (hdd_tipo_guardar == 2 || hdd_tipo_guardar == 3) { //Permanece en el formulario
    	if (hdd_exito > 0) {
	        $("#contenedor_exito").css("display", "block");
	        $('#contenedor_exito').html('Datos guardados correctamente');
	        setTimeout('$("#contenedor_exito").css("display", "none")', 3000);
	    }
	    else {
	        $("#contenedor_error").css("display", "block");
	        $('#contenedor_error').html('Error al guardar usuarios');
	        setTimeout('$("#contenedor_error").css("display", "none")', 3000);
	    }
    }
	window.scrollTo(0, 0);
}

function enviar_a_estados() {
	crear_preqx_laser_of(2, 0);
	
	var params = "opcion=2&id_hc=" + $("#hdd_id_hc_consulta").val() +
				 "&id_admision=" + $("#hdd_id_admision").val() +
				 "&ind_preconsulta=" + $("#hdd_ind_preconsulta").val();
	
	llamarAjax("consulta_preqx_laser_ajax_of.php", params, "d_interno", "mostrar_formulario_flotante(1);");
}

function seleccionar_ojo_of(id_ojo) {
	switch (id_ojo) {
		case "79": //OD
			$("#presion_intraocular_od").removeAttr("disabled");
			$("#fondo_ojo_nervio_optico_od").removeAttr("disabled");
			$("#fondo_ojo_macula_od").removeAttr("disabled");
			$("#fondo_ojo_periferia_od").removeAttr("disabled");
			$("#fondo_ojo_vitreo_od").removeAttr("disabled");
			
			$("#presion_intraocular_oi").val("");
			$("#fondo_ojo_nervio_optico_oi").val("");
			$("#fondo_ojo_macula_oi").val("");
			$("#fondo_ojo_periferia_oi").val("");
			$("#fondo_ojo_vitreo_oi").val("");
			$("#presion_intraocular_oi").attr("disabled", "disabled");
			$("#fondo_ojo_nervio_optico_oi").attr("disabled", "disabled");
			$("#fondo_ojo_macula_oi").attr("disabled", "disabled");
			$("#fondo_ojo_periferia_oi").attr("disabled", "disabled");
			$("#fondo_ojo_vitreo_oi").attr("disabled", "disabled");
			break;
			
		case "80": //OI
			$("#presion_intraocular_od").val("");
			$("#fondo_ojo_nervio_optico_od").val("");
			$("#fondo_ojo_macula_od").val("");
			$("#fondo_ojo_periferia_od").val("");
			$("#fondo_ojo_vitreo_od").val("");
			$("#presion_intraocular_od").attr("disabled", "disabled");
			$("#fondo_ojo_nervio_optico_od").attr("disabled", "disabled");
			$("#fondo_ojo_macula_od").attr("disabled", "disabled");
			$("#fondo_ojo_periferia_od").attr("disabled", "disabled");
			$("#fondo_ojo_vitreo_od").attr("disabled", "disabled");
			
			$("#presion_intraocular_oi").removeAttr("disabled");
			$("#fondo_ojo_nervio_optico_oi").removeAttr("disabled");
			$("#fondo_ojo_macula_oi").removeAttr("disabled");
			$("#fondo_ojo_periferia_oi").removeAttr("disabled");
			$("#fondo_ojo_vitreo_oi").removeAttr("disabled");
			break;
			
		default:
			$("#presion_intraocular_od").removeAttr("disabled");
			$("#fondo_ojo_nervio_optico_od").removeAttr("disabled");
			$("#fondo_ojo_macula_od").removeAttr("disabled");
			$("#fondo_ojo_periferia_od").removeAttr("disabled");
			$("#fondo_ojo_vitreo_od").removeAttr("disabled");
			
			$("#presion_intraocular_oi").removeAttr("disabled");
			$("#fondo_ojo_nervio_optico_oi").removeAttr("disabled");
			$("#fondo_ojo_macula_oi").removeAttr("disabled");
			$("#fondo_ojo_periferia_oi").removeAttr("disabled");
			$("#fondo_ojo_vitreo_oi").removeAttr("disabled");
			break;
	}
}
