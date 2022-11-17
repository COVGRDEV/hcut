function seleccionar_reoperacion(ind_reoperacion) {
	if (ind_reoperacion == "1") {
		$("#cmb_reop_ent").prop("disabled", false);
		$("#txt_fecha_cx_ant").prop("disabled", false);
	} else {
		$("#cmb_reop_ent").prop("disabled", true);
		$("#cmb_reop_ent").val("");
		$("#txt_fecha_cx_ant").prop("disabled", true);
		$("#txt_fecha_cx_ant").val("");
	}
}

function mostrar_agregar_procedimiento() {
    //Posicionar el div flotante en la pantalla
    $('#d_centro').css({'top': "50px;"});
	
    $('#fondo_negro').css('display', 'block');
    $('#d_centro').slideDown(400).css('display', 'block');
	
    var params = "opcion=1";
	
    llamarAjax("procedimiento_qx_laser_ajax.php", params, "d_interno", "");
}

function validar_buscar_procedimientos() {
	$('#frmBuscarProcedimiento').validate({
		debug: false,
		rules: {
			txt_identificacion_interno: {
				required: true,
			},
		},
		submitHandler: function(form) {
			buscar_procedimientos();
			
			return false;
		}
	});
}

function buscar_procedimientos() {
	var params = "opcion=2&parametro=" + str_encode($("#txt_identificacion_interno").val());
	
	llamarAjax("procedimiento_qx_laser_ajax.php", params, "d_procedimientos_b", "");
}

function seleccionar_procedimiento(indice) {
	var cant_procedimientos = parseInt($("#hdd_cant_procedimientos").val(), 10);
	
	if (cant_procedimientos <= 50) {
		$("#td_cod_procedimiento_" + cant_procedimientos).html($("#hdd_cod_procedimiento_b_" + indice).val());
		$("#td_nombre_procedimiento_" + cant_procedimientos).html($("#hdd_nombre_procedimiento_b_" + indice).val());
		$("#hdd_cod_procedimiento_" + cant_procedimientos).val($("#hdd_cod_procedimiento_b_" + indice).val());
		$("#tr_proc_cx_" + cant_procedimientos).css("display", "")
		
		cant_procedimientos++;
		$("#hdd_cant_procedimientos").val(cant_procedimientos);
	} else {
		alert("L\xedmite de procedimientos quir\xfargicos alcanzado");
	}
	
	cerrar_div_centro();
}

function quitar_procedimiento(indice) {
	$("#td_cod_procedimiento_" + indice).html("");
	$("#td_nombre_procedimiento_" + indice).html("");
	$("#cmb_ojo_" + indice).val("");
	$("#cmb_via_" + indice).val("");
	$("#hdd_cod_procedimiento_" + indice).val("");
	$("#tr_proc_cx_" + indice).css("display", "none")
	
	var cant_procedimientos = parseInt($("#hdd_cant_procedimientos").val(), 10);
	if (indice + 1 == cant_procedimientos) {
		cant_procedimientos--;
		$("#hdd_cant_procedimientos").val(cant_procedimientos);
	}
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la cirugía
 * 2=Guardar y NO cambiar el estado de la cirugía, SIN VALIDAR LOS CAMPOS
 */
function guardar_cirugia(tipo, ind_imprimir) {
	$("#contenedor_error").css("display", "none");
	limpiar_bordes();
	switch (tipo) {
		case 1:
		case 3:
			if (validar_cirugia()) {
				editar_cirugia(tipo);
			} else {
				$("#contenedor_error").css("display", "block");
				$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
				window.scrollTo(0, 0);
			}
			break;
		case 2:
			editar_cirugia(tipo);
			break;
	}
	
	if (ind_imprimir == 1) {
		setTimeout("imprimir_cirugia()", 1000);
	}
}

function imprimir_cirugia() {
	var params = "id_hc=" + $("#hdd_id_hc").val();
	
	llamarAjax("../historia_clinica/impresion_historia_clinica.php", params, "d_impresion_hc", "imprSelec(\"d_impresion_hc\")");
}

/**
 *Guardar y validar los datos de la evaluacion 
 */
function guardar_evaluacion_qx(){
	var result = 0;
	$('#txt_anotaciones_ev').removeClass("borde_error");
	if ($('#txt_anotaciones_ev').val()=='') {
		$('#txt_anotaciones_ev').addClass("borde_error");
		result = 1;
	}
	$("#contenedor_error").css("display", "none");
	if (result == 1) {
		$("#contenedor_error").css("display", "block");
		$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
		window.scrollTo(0, 0);
	} else if (result == 0) {
		var params = "opcion=4&id_hc=" + $("#hdd_id_hc").val() +
					 "&id_admision=" + $("#hdd_id_admision").val() +
					 "&txt_anotaciones_ev=" + str_encode($("#txt_anotaciones_ev").val());
		
		llamarAjax("procedimiento_qx_laser_ajax.php", params, "d_guardar_cx", "validar_exito(1);");
	}
}

function limpiar_bordes() {
    $("#txt_fecha_cx").css({"border": "1px solid rgba(0,0,0,.2)"});
    $("#cmb_convenio").css({"border": "1px solid rgba(0,0,0,.2)"});
    $("#cmb_amb_rea").css({"border": "1px solid rgba(0,0,0,.2)"});
    $("#cmb_fin_pro").css({"border": "1px solid rgba(0,0,0,.2)"});
    $("#cmb_usuario_prof").css({"border": "1px solid rgba(0,0,0,.2)"});
    $("#cmb_reoperacion").css({"border": "1px solid rgba(0,0,0,.2)"});
    $("#cmb_reop_ent").css({"border": "1px solid rgba(0,0,0,.2)"});
	
    $("#cmb_tipo_laser").css({"border": "1px solid rgba(0,0,0,.2)"});
    $("#cmb_ojo").css({"border": "1px solid rgba(0,0,0,.2)"});
	
	$("#cmb_tecnica_od").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_microquerato_od").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_num_placas_od").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_tiempo_vacio_od").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_uso_cuchilla_od").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_bisagra_od").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_tiempo_qx_od").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_tipo_od").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_esfera_od").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_cilindro_od").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_eje_od").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_zona_optica_od").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_ablacion_od").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_esp_corneal_base_od").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_humedad_od").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_temperatura_od").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_wtw_od").css({"border": "1px solid rgba(0,0,0,.2)"});
	
	$("#cmb_tecnica_oi").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_microquerato_oi").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_num_placas_oi").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_tiempo_vacio_oi").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_uso_cuchilla_oi").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_bisagra_oi").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_tiempo_qx_oi").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_tipo_oi").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_esfera_oi").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_cilindro_oi").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_eje_oi").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_zona_optica_oi").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_ablacion_oi").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_esp_corneal_base_oi").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_humedad_oi").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_temperatura_oi").css({"border": "1px solid rgba(0,0,0,.2)"});
	$("#txt_wtw_oi").css({"border": "1px solid rgba(0,0,0,.2)"});
	
	var cant_procedimientos = parseInt($("#hdd_cant_procedimientos").val(), 10);
	for (var i = 0; i < cant_procedimientos; i++) {
	    $("#cmb_ojo_" + i).css({"border": "1px solid rgba(0,0,0,.2)"});
    	$("#cmb_via_" + i).css({"border": "1px solid rgba(0,0,0,.2)"});
	}
}

function validar_cirugia() {
	var resultado = true;
	
	var panel_1 = 0;
	var panel_2 = 0;
	var panel_3 = 0;
	
	$("#proce_laser_1").removeClass("borde_error_panel");
	$("#proce_laser_1 a").css({"color": "#5B5B5B"});
	$("#proce_laser_2").removeClass("borde_error_panel");
	$("#proce_laser_2 a").css({"color": "#5B5B5B"});
	$("#proce_laser_3").removeClass("borde_error_panel");
	$("#proce_laser_3 a").css({"color": "#5B5B5B"});
	
    if ($("#txt_fecha_cx").val() == "") {
		$("#txt_fecha_cx").css({"border": "2px solid #FF002A"});
		resultado = false;
		panel_1=1;
	}
    if ($("#cmb_convenio").val() == "") {
		$("#cmb_convenio").css({"border": "2px solid #FF002A"});
		resultado = false;
		panel_1=1;
	}
    if ($("#cmb_amb_rea").val() == "") {
		$("#cmb_amb_rea").css({"border": "2px solid #FF002A"});
		resultado = false;
		panel_1=1;
	}
    if ($("#cmb_fin_pro").val() == "") {
		$("#cmb_fin_pro").css({"border": "2px solid #FF002A"});
		resultado = false;
		panel_1=1;
	}
    if ($("#cmb_usuario_prof").val() == "") {
		$("#cmb_usuario_prof").css({"border": "2px solid #FF002A"});
		resultado = false;
		panel_1=1;
	}
    if ($("#cmb_reoperacion").val() == "") {
		$("#cmb_reoperacion").css({"border": "2px solid #FF002A"});
		resultado = false;
		panel_1=1;
	} else if ($("#cmb_reoperacion").val() == "1") {
		if ($("#cmb_reop_ent").val() == "") {
			$("#cmb_reop_ent").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_1=1;
		}
	}
	
    if ($("#cmb_tipo_laser").val() == "") {
		$("#cmb_tipo_laser").css({"border": "2px solid #FF002A"});
		resultado = false;
		panel_2=1;
	}
    if ($("#cmb_ojo").val() == "") {
		$("#cmb_ojo").css({"border": "2px solid #FF002A"});
		resultado = false;
		panel_2=1;
	}
	
	if ($("#cmb_ojo").val() == "79" || $("#cmb_ojo").val() == "81") {
		if ($("#cmb_tecnica_od").val() == "") {
			$("#cmb_tecnica_od").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_microquerato_od").val() == "") {
			$("#txt_microquerato_od").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_num_placas_od").val() == "") {
			$("#txt_num_placas_od").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_tiempo_vacio_od").val() == "") {
			$("#txt_tiempo_vacio_od").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_uso_cuchilla_od").val() == "") {
			$("#txt_uso_cuchilla_od").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_bisagra_od").val() == "") {
			$("#txt_bisagra_od").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_tiempo_qx_od").val() == "") {
			$("#txt_tiempo_qx_od").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_tipo_od").val() == "") {
			$("#txt_tipo_od").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_esfera_od").val() == "") {
			$("#txt_esfera_od").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_cilindro_od").val() == "") {
			$("#txt_cilindro_od").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_eje_od").val() == "") {
			$("#txt_eje_od").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_zona_optica_od").val() == "") {
			$("#txt_zona_optica_od").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_ablacion_od").val() == "") {
			$("#txt_ablacion_od").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_esp_corneal_base_od").val() == "") {
			$("#txt_esp_corneal_base_od").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_humedad_od").val() == "") {
			$("#txt_humedad_od").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_temperatura_od").val() == "") {
			$("#txt_temperatura_od").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_wtw_od").val() == "") {
			$("#txt_wtw_od").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
	}
	
	if ($("#cmb_ojo").val() == "80" || $("#cmb_ojo").val() == "81") {
		if ($("#cmb_tecnica_oi").val() == "") {
			$("#cmb_tecnica_oi").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_microquerato_oi").val() == "") {
			$("#txt_microquerato_oi").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_num_placas_oi").val() == "") {
			$("#txt_num_placas_oi").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_tiempo_vacio_oi").val() == "") {
			$("#txt_tiempo_vacio_oi").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_uso_cuchilla_oi").val() == "") {
			$("#txt_uso_cuchilla_oi").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_bisagra_oi").val() == "") {
			$("#txt_bisagra_oi").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_tiempo_qx_oi").val() == "") {
			$("#txt_tiempo_qx_oi").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_tipo_oi").val() == "") {
			$("#txt_tipo_oi").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_esfera_oi").val() == "") {
			$("#txt_esfera_oi").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_cilindro_oi").val() == "") {
			$("#txt_cilindro_oi").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_eje_oi").val() == "") {
			$("#txt_eje_oi").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_zona_optica_oi").val() == "") {
			$("#txt_zona_optica_oi").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_ablacion_oi").val() == "") {
			$("#txt_ablacion_oi").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_esp_corneal_base_oi").val() == "") {
			$("#txt_esp_corneal_base_oi").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_humedad_oi").val() == "") {
			$("#txt_humedad_oi").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_temperatura_oi").val() == "") {
			$("#txt_temperatura_oi").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
		if ($("#txt_wtw_oi").val() == "") {
			$("#txt_wtw_oi").css({"border": "2px solid #FF002A"});
			resultado = false;
			panel_2=1;
		}
	}
	
	var cant_procedimientos = parseInt($("#hdd_cant_procedimientos").val(), 10);
	for (var i = 0; i < cant_procedimientos; i++) {
	    if ($("#tr_proc_cx_" + i).is(":visible") && $("#hdd_cod_procedimiento_" + i).val() != "") {
			if ($("#cmb_ojo_" + i).val() == "") {
				$("#cmb_ojo_" + i).css({"border": "2px solid #FF002A"});
				resultado = false;
				panel_1=1;
			}
			if ($("#cmb_via_" + i).val() == "") {
				$("#cmb_via_" + i).css({"border": "2px solid #FF002A"});
				resultado = false;
				panel_1=1;
			}
		}
	}
	
	var cant_ciex = parseInt($("#lista_tabla").val(), 10);
	for (i = 1; i <= cant_ciex; i++) {
		var cod_ciex = $("#hdd_ciex_diagnostico_" + i).val();
		var val_ojos = $("#valor_ojos_" + i).val();
		if (cod_ciex != "" && val_ojos == "") {
			$("#valor_ojos_" + i).css({"border": "2px solid #FF002A"});
		 	resultado = false;
		 	panel_3=1;
		}
	}
	
	if (panel_1 == 1) {
	   $("#proce_laser_1").addClass("borde_error_panel");
	   $("#proce_laser_1 a").css({"color": "#FF002A"});
	}
	if (panel_2 == 1) {
	   $("#proce_laser_2").addClass("borde_error_panel");
	   $("#proce_laser_2 a").css({"color": "#FF002A"});
	}
	if (panel_3 == 1) {
	   $("#proce_laser_3").addClass("borde_error_panel");
	   $("#proce_laser_3 a").css({"color": "#FF002A"});
	}
	
	return resultado;
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta 
 */
function editar_cirugia(tipo){
	var id_admision = $("#hdd_id_admision").val();
	
	var params = "opcion=3&id_hc=" + $("#hdd_id_hc").val() +
				 "&id_admision=" + $("#hdd_id_admision").val() +
				 "&tipo_guardar=" + tipo +
				 "&fecha_cx=" + $("#txt_fecha_cx").val() +
				 "&id_convenio=" + $("#cmb_convenio").val() +
				 "&id_amb_rea=" + $("#cmb_amb_rea").val() +
				 "&id_fin_pro=" + $("#cmb_fin_pro").val() +
				 "&id_usuario_prof=" + $("#cmb_usuario_prof").val() +
				 "&ind_reoperacion=" + $("#cmb_reoperacion").val() +
				 "&ind_reop_ent=" + $("#cmb_reop_ent").val() +
				 "&fecha_cx_ant=" + $("#txt_fecha_cx_ant").val() +
				 "&observaciones_cx=" + str_encode($("#txt_observaciones_cx").val()) +
				 "&id_tipo_laser=" + $("#cmb_tipo_laser").val() +
				 "&id_ojo=" + $("#cmb_ojo").val() +
				 "&num_turno=" + $("#txt_num_turno").val() +
				 "&id_tecnica_od=" + $("#cmb_tecnica_od").val() +
				 "&microquerato_od=" + str_encode($("#txt_microquerato_od").val()) + 
				 "&num_placas_od=" + $("#txt_num_placas_od").val() +
				 "&tiempo_vacio_od=" + $("#txt_tiempo_vacio_od").val() +
				 "&uso_cuchilla_od=" + $("#txt_uso_cuchilla_od").val() +
				 "&bisagra_od=" + str_encode($("#txt_bisagra_od").val()) +
				 "&tiempo_qx_od=" + $("#txt_tiempo_qx_od").val() +
				 "&tipo_od=" + str_encode($("#txt_tipo_od").val()) +
				 "&esfera_od=" + str_encode($("#txt_esfera_od").val()) +
				 "&cilindro_od=" + str_encode($("#txt_cilindro_od").val()) +
				 "&eje_od=" + $("#txt_eje_od").val() +
				 "&zona_optica_od=" + $("#txt_zona_optica_od").val() +
				 "&ablacion_od=" + $("#txt_ablacion_od").val() +
				 "&esp_corneal_base_od=" + $("#txt_esp_corneal_base_od").val() +
				 "&humedad_od=" + $("#txt_humedad_od").val() +
				 "&temperatura_od=" + $("#txt_temperatura_od").val() +
				 "&wtw_od=" + $("#txt_wtw_od").val() +
				 "&id_tecnica_oi=" + $("#cmb_tecnica_oi").val() +
				 "&microquerato_oi=" + str_encode($("#txt_microquerato_oi").val()) + 
				 "&num_placas_oi=" + $("#txt_num_placas_oi").val() +
				 "&tiempo_vacio_oi=" + $("#txt_tiempo_vacio_oi").val() +
				 "&uso_cuchilla_oi=" + $("#txt_uso_cuchilla_oi").val() +
				 "&bisagra_oi=" + str_encode($("#txt_bisagra_oi").val()) +
				 "&tiempo_qx_oi=" + $("#txt_tiempo_qx_oi").val() +
				 "&tipo_oi=" + str_encode($("#txt_tipo_oi").val()) +
				 "&esfera_oi=" + str_encode($("#txt_esfera_oi").val()) +
				 "&cilindro_oi=" + str_encode($("#txt_cilindro_oi").val()) +
				 "&eje_oi=" + $("#txt_eje_oi").val() +
				 "&zona_optica_oi=" + $("#txt_zona_optica_oi").val() +
				 "&ablacion_oi=" + $("#txt_ablacion_oi").val() +
				 "&esp_corneal_base_oi=" + $("#txt_esp_corneal_base_oi").val() +
				 "&humedad_oi=" + $("#txt_humedad_oi").val() +
				 "&temperatura_oi=" + $("#txt_temperatura_oi").val() +
				 "&wtw_oi=" + $("#txt_wtw_oi").val();
	
	var cant_procedimientos = parseInt($("#hdd_cant_procedimientos").val(), 10);
	params += "&cant_procedimientos=" + cant_procedimientos;
	for (var i = 0; i < cant_procedimientos; i++) {
		if (document.getElementById("tr_proc_cx_" + i).style.display != "none" && $("#hdd_cod_procedimiento_" + i).val() != "") {
			params += "&cod_procedimiento_" + i + "=" + $("#hdd_cod_procedimiento_" + i).val() +
					  "&id_ojo_" + i + "=" + $("#cmb_ojo_" + i).val() +
					  "&via_" + i + "=" + $("#cmb_via_" + i).val();
		}
	}
	
	//Para Diagnosticos
	var cant_ciex = parseInt($("#lista_tabla").val(), 10);
	params += "&cant_ciex=" + cant_ciex;
	for (var i = 1; i <= cant_ciex; i++) {
		var cod_ciex = $("#hdd_ciex_diagnostico_" + i).val();
		var val_ojos = $("#valor_ojos_" + i).val();
		if (cod_ciex != "") {
			params += "&cod_ciex_" + i + "=" + cod_ciex +
					  "&val_ojos_" + i + "=" + val_ojos;
		}
	}
	
	llamarAjax("procedimiento_qx_laser_ajax.php", params, "d_guardar_cx", "validar_exito(" + tipo + ");");
}

function validar_exito(tipo) {
	var resul_guardar = $('#hdd_resul_guardar').val();
	
	if (resul_guardar > 0) {
		$("#contenedor_exito").css("display", "block");
		$("#contenedor_exito").html("Datos guardados correctamente");
		$("#hdd_id_hc").val(resul_guardar);
		switch (tipo) {
			case 1:
				setTimeout("parent.cerrar_div_centro();", 1000);
				break;
			case 2:
			case 3:
				setTimeout('$("#contenedor_exito").css("display", "none")', 2000);
				break;
		}
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error interno al guardar la cirug&iacute;a");
	}
	window.scrollTo(0, 0);
}
/**
 * ind_complemento:
 * 1 = Entra para ingresar complemento
 * 0 = Entra y NO ingresa Complemento
 */
function seleccionar_ojo(id_ojo, ind_complemento) {
	
 if(ind_complemento==0){
	switch (id_ojo) {
		case "79": //OD
			$("#cmb_tecnica_od").prop("disabled", false);
			$("#txt_microquerato_od").prop("disabled", false);
			$("#txt_num_placas_od").prop("disabled", false);
			$("#txt_tiempo_vacio_od").prop("disabled", false);
			$("#txt_uso_cuchilla_od").prop("disabled", false);
			$("#txt_bisagra_od").prop("disabled", false);
			$("#txt_tiempo_qx_od").prop("disabled", false);
			$("#txt_tipo_od").prop("disabled", false);
			$("#txt_esfera_od").prop("disabled", false);
			$("#txt_cilindro_od").prop("disabled", false);
			$("#txt_eje_od").prop("disabled", false);
			$("#txt_zona_optica_od").prop("disabled", false);
			$("#txt_ablacion_od").prop("disabled", false);
			$("#txt_esp_corneal_base_od").prop("disabled", false);
			$("#txt_humedad_od").prop("disabled", false);
			$("#txt_temperatura_od").prop("disabled", false);
			$("#txt_wtw_od").prop("disabled", false);
			
			$("#cmb_tecnica_oi").prop("disabled", true);
			$("#cmb_tecnica_oi").val("");
			$("#txt_microquerato_oi").prop("disabled", true);
			$("#txt_microquerato_oi").val("");
			$("#txt_num_placas_oi").prop("disabled", true);
			$("#txt_num_placas_oi").val("");
			$("#txt_tiempo_vacio_oi").prop("disabled", true);
			$("#txt_tiempo_vacio_oi").val("");
			$("#txt_uso_cuchilla_oi").prop("disabled", true);
			$("#txt_uso_cuchilla_oi").val("");
			$("#txt_bisagra_oi").prop("disabled", true);
			$("#txt_bisagra_oi").val("");
			$("#txt_tiempo_qx_oi").prop("disabled", true);
			$("#txt_tiempo_qx_oi").val("");
			$("#txt_tipo_oi").prop("disabled", true);
			$("#txt_tipo_oi").val("");
			$("#txt_esfera_oi").prop("disabled", true);
			$("#txt_esfera_oi").val("");
			$("#txt_cilindro_oi").prop("disabled", true);
			$("#txt_cilindro_oi").val("");
			$("#txt_eje_oi").prop("disabled", true);
			$("#txt_eje_oi").val("");
			$("#txt_zona_optica_oi").prop("disabled", true);
			$("#txt_zona_optica_oi").val("");
			$("#txt_ablacion_oi").prop("disabled", true);
			$("#txt_ablacion_oi").val("");
			$("#txt_esp_corneal_base_oi").prop("disabled", true);
			$("#txt_esp_corneal_base_oi").val("");
			$("#txt_humedad_oi").prop("disabled", true);
			$("#txt_humedad_oi").val("");
			$("#txt_temperatura_oi").prop("disabled", true);
			$("#txt_temperatura_oi").val("");
			$("#txt_wtw_oi").prop("disabled", true);
			$("#txt_wtw_oi").val("");
			break;
			
		case "80": //OI
			$("#cmb_tecnica_od").prop("disabled", true);
			$("#cmb_tecnica_od").val("");
			$("#txt_microquerato_od").prop("disabled", true);
			$("#txt_microquerato_od").val("");
			$("#txt_num_placas_od").prop("disabled", true);
			$("#txt_num_placas_od").val("");
			$("#txt_tiempo_vacio_od").prop("disabled", true);
			$("#txt_tiempo_vacio_od").val("");
			$("#txt_uso_cuchilla_od").prop("disabled", true);
			$("#txt_uso_cuchilla_od").val("");
			$("#txt_bisagra_od").prop("disabled", true);
			$("#txt_bisagra_od").val("");
			$("#txt_tiempo_qx_od").prop("disabled", true);
			$("#txt_tiempo_qx_od").val("");
			$("#txt_tipo_od").prop("disabled", true);
			$("#txt_tipo_od").val("");
			$("#txt_esfera_od").prop("disabled", true);
			$("#txt_esfera_od").val("");
			$("#txt_cilindro_od").prop("disabled", true);
			$("#txt_cilindro_od").val("");
			$("#txt_eje_od").prop("disabled", true);
			$("#txt_eje_od").val("");
			$("#txt_zona_optica_od").prop("disabled", true);
			$("#txt_zona_optica_od").val("");
			$("#txt_ablacion_od").prop("disabled", true);
			$("#txt_ablacion_od").val("");
			$("#txt_esp_corneal_base_od").prop("disabled", true);
			$("#txt_esp_corneal_base_od").val("");
			$("#txt_humedad_od").prop("disabled", true);
			$("#txt_humedad_od").val("");
			$("#txt_temperatura_od").prop("disabled", true);
			$("#txt_temperatura_od").val("");
			$("#txt_wtw_od").prop("disabled", true);
			$("#txt_wtw_od").val("");
			
			$("#cmb_tecnica_oi").prop("disabled", false);
			$("#txt_microquerato_oi").prop("disabled", false);
			$("#txt_num_placas_oi").prop("disabled", false);
			$("#txt_tiempo_vacio_oi").prop("disabled", false);
			$("#txt_uso_cuchilla_oi").prop("disabled", false);
			$("#txt_bisagra_oi").prop("disabled", false);
			$("#txt_tiempo_qx_oi").prop("disabled", false);
			$("#txt_tipo_oi").prop("disabled", false);
			$("#txt_esfera_oi").prop("disabled", false);
			$("#txt_cilindro_oi").prop("disabled", false);
			$("#txt_eje_oi").prop("disabled", false);
			$("#txt_zona_optica_oi").prop("disabled", false);
			$("#txt_ablacion_oi").prop("disabled", false);
			$("#txt_esp_corneal_base_oi").prop("disabled", false);
			$("#txt_humedad_oi").prop("disabled", false);
			$("#txt_temperatura_oi").prop("disabled", false);
			$("#txt_wtw_oi").prop("disabled", false);
			break;
			
		case "81": //AO
			$("#cmb_tecnica_od").prop("disabled", false);
			$("#txt_microquerato_od").prop("disabled", false);
			$("#txt_num_placas_od").prop("disabled", false);
			$("#txt_tiempo_vacio_od").prop("disabled", false);
			$("#txt_uso_cuchilla_od").prop("disabled", false);
			$("#txt_bisagra_od").prop("disabled", false);
			$("#txt_tiempo_qx_od").prop("disabled", false);
			$("#txt_tipo_od").prop("disabled", false);
			$("#txt_esfera_od").prop("disabled", false);
			$("#txt_cilindro_od").prop("disabled", false);
			$("#txt_eje_od").prop("disabled", false);
			$("#txt_zona_optica_od").prop("disabled", false);
			$("#txt_ablacion_od").prop("disabled", false);
			$("#txt_esp_corneal_base_od").prop("disabled", false);
			$("#txt_humedad_od").prop("disabled", false);
			$("#txt_temperatura_od").prop("disabled", false);
			$("#txt_wtw_od").prop("disabled", false);
			
			$("#cmb_tecnica_oi").prop("disabled", false);
			$("#txt_microquerato_oi").prop("disabled", false);
			$("#txt_num_placas_oi").prop("disabled", false);
			$("#txt_tiempo_vacio_oi").prop("disabled", false);
			$("#txt_uso_cuchilla_oi").prop("disabled", false);
			$("#txt_bisagra_oi").prop("disabled", false);
			$("#txt_tiempo_qx_oi").prop("disabled", false);
			$("#txt_tipo_oi").prop("disabled", false);
			$("#txt_esfera_oi").prop("disabled", false);
			$("#txt_cilindro_oi").prop("disabled", false);
			$("#txt_eje_oi").prop("disabled", false);
			$("#txt_zona_optica_oi").prop("disabled", false);
			$("#txt_ablacion_oi").prop("disabled", false);
			$("#txt_esp_corneal_base_oi").prop("disabled", false);
			$("#txt_humedad_oi").prop("disabled", false);
			$("#txt_temperatura_oi").prop("disabled", false);
			$("#txt_wtw_oi").prop("disabled", false);
			break;
	}
  }
}
