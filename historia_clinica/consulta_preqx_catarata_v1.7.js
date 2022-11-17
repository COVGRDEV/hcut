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

function mostrar_formulario_flotante(tipo){
	if(tipo==1){//mostrar
		$('#fondo_negro').css('display', 'block');
		$('#d_centro').slideDown(400).css('display', 'block');
	}
	else if(tipo==0){//Ocultar
		$('#fondo_negro').css('display', 'none');
		$('#d_centro').slideDown(400).css('display', 'none');
	}
}

function reducir_formulario_flotante(ancho, alto){ //480, 390
	$('.div_centro').width(ancho);
	$('.div_centro').height(alto);
	$('.div_centro').css('top', '20%');
	$('.div_interno').width(ancho-15);
	$('.div_interno').height(alto-35);
}

function validar_array(array, id) {
	var text = $(id).val();
	var ind_existe = 0;//No existe
	for (var i = 0; i < array.length; i++) {
		if (text == array[i]) {
			ind_existe = 1;//Si Existe
			break;  
		}
	}
	if (text == '') {
		ind_existe = 1;//Si Existe
	}
	if (ind_existe == 0) {
		alert('Valor incorrecto');
		document.getElementById(id.id).value="";
		input = id.id;
		setTimeout('document.getElementById(input).focus()',75); 
	}
}

function validar_array_locs3(obj_val_locs3) {
	var id_locs3 = $("#cmb_locs3").val();
	switch (id_locs3) {
		case "111": //No
			validar_array(array_locs3_no, obj_val_locs3);
			break;
			
		case "112": //Nc
			validar_array(array_locs3_nc, obj_val_locs3);
			break;
			
		case "113": //C
			validar_array(array_locs3_c, obj_val_locs3);
			break;
			
		case "114": //SCP
			validar_array(array_locs3_scp, obj_val_locs3);
			break;
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
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta SIN VALIDAR LOS CAMPOS
 */
function guardar_consulta(tipo, ind_imprimir) {
	switch (tipo) {
		case 1: //Finalizar consulta
		case 3: //Guardar desde historia
		case 4: //Finalizar consulta desde traslado
			$("#contenedor_error").css("display", "none");
			if (validar_consulta()) {
				editar_consulta(tipo, ind_imprimir);
			} else {
				$("#contenedor_error").css("display", "block");
				$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
				window.scroll(0, 0);
			}
			break;
		case 2: //Guardar cambios
			editar_consulta(tipo, ind_imprimir);
			break;
	}
}

function imprimir_consulta() {
	var params = "id_hc=" + $("#hdd_id_hc_consulta").val();
	
	llamarAjax("../historia_clinica/impresion_historia_clinica.php", params, "d_impresion_hc", "continuar_imprimir_consulta();");
}

function continuar_imprimir_consulta() {
	var ruta = $("#hdd_ruta_arch_hc_pdf").val();
	window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=consulta_preqx_catarata.pdf", "_blank");
}

function validar_consulta() {
	var resultado = true;
	var panel_1 = 0;
	var panel_2 = 0;
	var panel_3 = 0;
	var panel_4 = 0;
	var panel_5 = 0;
	
	$("#panel_cata_1").removeClass("borde_error_panel");
	$("#panel_cata_1 a").css({"color": "#5B5B5B"});
	$("#panel_cata_2").removeClass("borde_error_panel");
	$("#panel_cata_2 a").css({"color": "#5B5B5B"});
	$("#panel_cata_3").removeClass("borde_error_panel");
	$("#panel_cata_3 a").css({"color": "#5B5B5B"});
	$("#panel_cata_4").removeClass("borde_error_panel");
	$("#panel_cata_4 a").css({"color": "#5B5B5B"});
	$("#panel_cata_5").removeClass("borde_error_panel");
	$("#panel_cata_5 a").css({"color": "#5B5B5B"});
	
	
	$("#cmb_locs3").removeClass("bordeAdmision");
	$("#txt_val_locs3").removeClass("bordeAdmision");
	$("#txt_val_rec_endotelial").removeClass("bordeAdmision");
	$("#txt_val_paquimetria").removeClass("bordeAdmision");
	$("#cmb_plegables").removeClass("bordeAdmision");
	$("#cmb_rigido").removeClass("bordeAdmision");
	$("#cmb_especiales").removeClass("bordeAdmision");
	$("#cke_txt_evolucion").removeClass("bordeAdmision");
	$("#cmb_anestesia").removeClass("bordeAdmision");
	$("#txt_q_val_biometria_od").removeClass("bordeAdmision");
	$("#txt_q_eje_biometria_od").removeClass("bordeAdmision");
	$("#txt_q_val_iol_master_od").removeClass("bordeAdmision");
	$("#txt_q_eje_iol_master_od").removeClass("bordeAdmision");
	$("#txt_q_val_topografia_od").removeClass("bordeAdmision");
	$("#txt_q_eje_topografia_od").removeClass("bordeAdmision");
	$("#txt_q_val_definitiva_od").removeClass("bordeAdmision");
	$("#txt_q_eje_definitiva_od").removeClass("bordeAdmision");
	$("#txt_q_val_biometria_oi").removeClass("bordeAdmision");
	$("#txt_q_eje_biometria_oi").removeClass("bordeAdmision");
	$("#txt_q_val_iol_master_oi").removeClass("bordeAdmision");
	$("#txt_q_eje_iol_master_oi").removeClass("bordeAdmision");
	$("#txt_q_val_topografia_oi").removeClass("bordeAdmision");
	$("#txt_q_eje_topografia_oi").removeClass("bordeAdmision");
	$("#txt_q_val_definitiva_oi").removeClass("bordeAdmision");
	$("#txt_q_eje_definitiva_oi").removeClass("bordeAdmision");
	$("#hdd_img_queratometria_od").removeClass("bordeAdmision");
	$("#hdd_img_queratometria_oi").removeClass("bordeAdmision");
	$("#cmb_incision_arq").removeClass("bordeAdmision");
	$("#txt_val_incision_arq").removeClass("bordeAdmision");
	$("#cke_txt_observaciones_preqx").removeClass("bordeAdmision");
	$("#txt_nombre_usuario_alt").removeClass("bordeAdmision");
	
	$("#ciex_diagnostico_1").removeClass("bordeAdmision");
	$("#valor_ojos_1").removeClass("bordeAdmision");
	var cant_ciex = $('#lista_tabla').val()
	for (i = 1; i <= cant_ciex; i++) {
	 	 $("#valor_ojos_" + i).removeClass("bordeAdmision");
	}
	
	if ($("#hdd_ciex_diagnostico_1").val() == "") {
		$("#ciex_diagnostico_1").addClass("bordeAdmision");
		resultado = false;
		panel_5=1;
	}
	if ($("#valor_ojos_1").val() == "") {
		$("#valor_ojos_1").addClass("bordeAdmision");
		resultado = false;
		panel_5=1;
	}
	for (i = 1; i <= cant_ciex; i++) {
		var cod_ciex = $("#hdd_ciex_diagnostico_"+i).val();
		var val_ojos = $("#valor_ojos_"+i).val();
		if (cod_ciex != '' && val_ojos == '') {
			$("#valor_ojos_" + i).addClass("bordeAdmision");
		 	resultado = false;
		 	panel_5=1;
		}
	}
	
	if (CKEDITOR.instances.txt_evolucion.getData() == "") {
		$("#cke_txt_evolucion").addClass("bordeAdmision");
		resultado = false;
		panel_2=1;
	}
    if ($("#cmb_anestesia").val() == ""){
		$("#cmb_anestesia").addClass("bordeAdmision");
		resultado = false;
		panel_2=1;
	}
	
	if (bol_od) {
	    if ($("#txt_q_val_biometria_od").val() == ""){
			$("#txt_q_val_biometria_od").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_eje_biometria_od").val() == ""){
			$("#txt_q_eje_biometria_od").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_val_iol_master_od").val() == ""){
			$("#txt_q_val_iol_master_od").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_eje_iol_master_od").val() == ""){
			$("#txt_q_eje_iol_master_od").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_val_topografia_od").val() == ""){
			$("#txt_q_val_topografia_od").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_eje_topografia_od").val() == ""){
			$("#txt_q_eje_topografia_od").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_val_definitiva_od").val() == ""){
			$("#txt_q_val_definitiva_od").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_eje_definitiva_od").val() == ""){
			$("#txt_q_eje_definitiva_od").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	}
	if (bol_oi) {
	    if ($("#txt_q_val_biometria_oi").val() == ""){
			$("#txt_q_val_biometria_oi").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_eje_biometria_oi").val() == ""){
			$("#txt_q_eje_biometria_oi").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_val_iol_master_oi").val() == ""){
			$("#txt_q_val_iol_master_oi").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_eje_iol_master_oi").val() == ""){
			$("#txt_q_eje_iol_master_oi").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_val_topografia_oi").val() == ""){
			$("#txt_q_val_topografia_oi").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_eje_topografia_oi").val() == ""){
			$("#txt_q_eje_topografia_oi").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_val_definitiva_oi").val() == ""){
			$("#txt_q_val_definitiva_oi").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_eje_definitiva_oi").val() == ""){
			$("#txt_q_eje_definitiva_oi").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	}
	
    if ($("#cmb_incision_arq").val() == ""){
		$("#cmb_incision_arq").addClass("bordeAdmision");
		resultado = false;
		panel_3=1;
	}
    if ($("#cmb_incision_arq").val() == "1" && $("#txt_val_incision_arq").val() == ""){
		$("#txt_val_incision_arq").addClass("bordeAdmision");
		resultado = false;
		panel_3=1;
	}
	
	if ($('#hdd_usuario_anonimo').val() == "1" && $("#txt_nombre_usuario_alt").val() == ""){
		$("#txt_nombre_usuario_alt").addClass("bordeAdmision");
		resultado = false;
	}
	
	if(panel_1 == 1){
	   $("#panel_cata_1").addClass("borde_error_panel");
	   $("#panel_cata_1 a").css({"color": "#FF002A"});
	}
	if(panel_2 == 1){
	   $("#panel_cata_2").addClass("borde_error_panel");
	   $("#panel_cata_2 a").css({"color": "#FF002A"});
	}
	if(panel_3 == 1){
	   $("#panel_cata_3").addClass("borde_error_panel");
	   $("#panel_cata_3 a").css({"color": "#FF002A"});
	}
	if(panel_4 == 1){
	   $("#panel_cata_4").addClass("borde_error_panel");
	   $("#panel_cata_4 a").css({"color": "#FF002A"});
	}
	if(panel_5 == 1){
	   $("#panel_cata_5").addClass("borde_error_panel");
	   $("#panel_cata_5 a").css({"color": "#FF002A"});
	}
	
	return resultado;
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta 
 */
function editar_consulta(tipo, ind_imprimir) {
	//Se guardan las imágenes
	guardar_imagenes();
	
	var params = "opcion=1&id_hc=" + $("#hdd_id_hc_consulta").val() +
				 "&id_admision=" + $("#hdd_id_admision").val() +
				 "&id_paciente=" + $("#hdd_id_paciente").val() +
				 "&id_locs3=" + $("#cmb_locs3").val() +
				 "&val_locs3=" + $("#txt_val_locs3").val() +
				 "&val_rec_endotelial=" + $("#txt_val_rec_endotelial").val() +
				 "&val_paquimetria=" + $("#txt_val_paquimetria").val() +
				 "&id_plegables=" + $("#cmb_plegables").val() +
				 "&id_rigido=" + $("#cmb_rigido").val() +
				 "&id_especiales=" + $("#cmb_especiales").val() +
				 "&texto_evolucion=" + str_encode(CKEDITOR.instances.txt_evolucion.getData()) +
				 "&id_anestesia=" + $("#cmb_anestesia").val() +
				 "&querato_val_biometria_od=" + $("#txt_q_val_biometria_od").val() +
				 "&querato_eje_biometria_od=" + $("#txt_q_eje_biometria_od").val() +
				 "&querato_val_iol_master_od=" + $("#txt_q_val_iol_master_od").val() +
				 "&querato_eje_iol_master_od=" + $("#txt_q_eje_iol_master_od").val() +
				 "&querato_val_topografia_od=" + $("#txt_q_val_topografia_od").val() +
				 "&querato_eje_topografia_od=" + $("#txt_q_eje_topografia_od").val() +
				 "&querato_val_definitiva_od=" + $("#txt_q_val_definitiva_od").val() +
				 "&querato_eje_definitiva_od=" + $("#txt_q_eje_definitiva_od").val() +
				 "&querato_val_biometria_oi=" + $("#txt_q_val_biometria_oi").val() +
				 "&querato_eje_biometria_oi=" + $("#txt_q_eje_biometria_oi").val() +
				 "&querato_val_iol_master_oi=" + $("#txt_q_val_iol_master_oi").val() +
				 "&querato_eje_iol_master_oi=" + $("#txt_q_eje_iol_master_oi").val() +
				 "&querato_val_topografia_oi=" + $("#txt_q_val_topografia_oi").val() +
				 "&querato_eje_topografia_oi=" + $("#txt_q_eje_topografia_oi").val() +
				 "&querato_val_definitiva_oi=" + $("#txt_q_val_definitiva_oi").val() +
				 "&querato_eje_definitiva_oi=" + $("#txt_q_eje_definitiva_oi").val() +
				 "&img_queratometria_od=" + $("#hdd_img_queratometria_od").val() +
				 "&img_queratometria_oi=" + $("#hdd_img_queratometria_oi").val() +
				 "&ind_incision_arq=" + $("#cmb_incision_arq").val() +
				 "&val_incision_arq=" + $("#txt_val_incision_arq").val() +
				 "&observaciones_preqx=" + str_encode(CKEDITOR.instances.txt_observaciones_preqx.getData()) +
				 "&diagnostico_preqx_catarata=" + str_encode(CKEDITOR.instances.diagnostico_preqx_catarata.getData()) +
				 "&solicitud_examenes_preqx_catarata=" + str_encode(CKEDITOR.instances.solicitud_examenes_preqx_catarata.getData()) +
				 "&tratamiento_preqx_catarata=" + str_encode(CKEDITOR.instances.tratamiento_preqx_catarata.getData()) +
				 "&medicamentos_preqx_catarata=" + str_encode($("#medicamentos_preqx_catarata").val()) +
				 "&nombre_usuario_alt=" + str_encode($('#txt_nombre_usuario_alt').val()) +
				 "&tipo_guardar=" + tipo;
	
	//Para Diagnosticos
	var cant_ciex = $('#lista_tabla').val()
	params += '&cant_ciex=' + cant_ciex;
	for (i = 1; i <= cant_ciex; i++) {
		var cod_ciex = $("#hdd_ciex_diagnostico_" + i).val();
		var val_ojos = $("#valor_ojos_" + i).val();
		if (cod_ciex != '') {
			params += '&cod_ciex_' + i + '=' + cod_ciex +
					  '&val_ojos_' + i + '=' + val_ojos;
		}
	}
	
	llamarAjax("consulta_preqx_catarata_ajax.php", params, "d_guardar_consulta", "validar_exito(" + ind_imprimir + ");");
}

function validar_exito(ind_imprimir) {
	var hdd_exito = $('#hdd_exito').val();
	var hdd_url_menu = $('#hdd_url_menu').val();
	var hdd_tipo_guardar = $('#hdd_tipo_guardar').val();
	
	if (hdd_tipo_guardar == 1) { //Cierra el formulario
		if (hdd_exito > 0) {
			$('#frm_consulta').css('display', 'none');
			$("#contenedor_exito").css("display", "block");
			$('#contenedor_exito').html('Datos guardados correctamente');
			setTimeout("enviar_credencial('" + hdd_url_menu + "')", 3000);
		} else {
			$("#contenedor_error").css("display", "block");
			$('#contenedor_error').html('Error al guardar la evoluci&oacute;n');
		}
	} else if (hdd_tipo_guardar == 2) { //Permanece en el formulario
		if (hdd_exito > 0) {
			$("#contenedor_exito").css("display", "block");
			$('#contenedor_exito').html('Datos guardados correctamente');
			setTimeout('$("#contenedor_exito").css("display", "none")', 3000);
			
			if (ind_imprimir == 1) {
				setTimeout("imprimir_consulta()", 1000);
			}
		} else {
			$("#contenedor_error").css("display", "block");
			$('#contenedor_error').html('Error al guardar la evoluci&oacute;n');
		}
	} else if (hdd_tipo_guardar == 3) { //Permanece en el formulario
		if (hdd_exito > 0) {
			$("#contenedor_exito").css("display", "block");
			$('#contenedor_exito').html('Datos guardados correctamente');
			setTimeout('$("#contenedor_exito").css("display", "none")', 3000);
			
			if (ind_imprimir == 1) {
				setTimeout("imprimir_consulta()", 1000);
			}
		} else {
			$("#contenedor_error").css("display", "block");
			$('#contenedor_error').html('Error al guardar la evoluci&oacute;n');
		}
	}
	window.scrollTo(0, 0);
}

function guardar_imagenes() {
	document.getElementById("ifr_img_queratometria_od").contentWindow.guardar_imagen();
	document.getElementById("ifr_img_queratometria_oi").contentWindow.guardar_imagen();
}

function cambiar_lista_locs3(id_locs3) {
	$("#txt_val_locs3").val("");
	$(function() {
		switch (id_locs3) {
			case "111": //No
				$("#txt_val_locs3").autocomplete({ source: array_locs3_no });
				break;
				
			case "112": //Nc
				$("#txt_val_locs3").autocomplete({ source: array_locs3_nc });
				break;
				
			case "113": //C
				$("#txt_val_locs3").autocomplete({ source: array_locs3_c });
				break;
				
			case "114": //SCP
				$("#txt_val_locs3").autocomplete({ source: array_locs3_scp });
				break;
		}
	});
}

function seleccionar_incision_arq(ind_incision_arq) {
	switch (ind_incision_arq) {
		case "1":
			$("#txt_val_incision_arq").attr("disabled", false);
			if (trim($("#txt_val_paquimetria").val()) != "") {
				var val_incision_arq_aux = Math.round(parseInt($("#txt_val_paquimetria").val(), 10) * 0.8);
				$("#txt_val_incision_arq").val(val_incision_arq_aux);
			}
			break;
			
		case "0":
			$("#txt_val_incision_arq").attr("disabled", true);
			$("#txt_val_incision_arq").val("");
			break;
	}
}

function enviar_a_estados() {
	guardar_consulta(2, 0);
	
	var params = "opcion=2&id_hc=" + $("#hdd_id_hc_consulta").val() +
				 "&id_admision=" + $("#hdd_id_admision").val();
	
	llamarAjax("consulta_preqx_catarata_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1);");
}
