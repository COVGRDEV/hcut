var g_id_proc_ac = 0;
var g_id_proc_ap = 0;
var g_id_proc_am = 0;
var g_id_proc_at = 0;
var g_id_proc_us = 0;
var g_id_proc_af = 0;
var g_id_proc_ad = 0;

function seleccionar_convenio(id_convenio, sigla, indice) {
	var params = "opcion=1&id_convenio=" + id_convenio +
				 "&sigla=" + sigla +
				 "&indice=" + indice;
	
	var destino_aux = "d_planes";
	var funcion_despues = "";
	if (sigla != "" || indice != "") {
		if (sigla != "") {
			destino_aux += "_" + sigla;
		}
		if (indice != "") {
			destino_aux += "_" + indice;
		}
	} else {
		funcion_despues = "cargar_rips_disponibles();";
	}
	llamarAjax("generador_rips_ajax.php", params, destino_aux, funcion_despues);
}

function seleccionar_plan(id_plan) {
	cargar_rips_disponibles();
}

function confirmar_cargar_datos_rips() {
	var ind_rips_existentes = ($("#chk_rips_existentes").is(":checked") ? 1 : 0);
	
	if (ind_rips_existentes == 1) {
		$("#fondo_negro").css("display", "block");
		$("#d_centro").slideDown(400).css("display", "block");
		
		$("#d_interno").html(
			'<table class="datagrid" border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
				'<tr class="headegrid">' +
					'<th align="center" class="msg_alerta" style="border: 1px solid #fff;">' +
						'<h4>&iquest;Est&aacute; seguro que desea generar nuevamente los RIPS existentes?<br />Esto har&aacute; que se pierdan los cambios que haya realizado anteriormente.</h4>' +
					'</th>' +
				'</tr>' +
				'<tr>' +
					'<th align="center" style="width:5%;border: 1px solid #fff;">' +
						'<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" value="Aceptar" class="btnPrincipal" onclick="cargar_datos_rips();"/>' +
						'&nbsp;&nbsp;' +
						'<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" value="Cancelar" class="btnPrincipal" onclick="cerrar_div_centro();"/>' +
					'</th>' +
				'</tr>' +
			'</table>');
		posicionarDivFlotante("d_centro");
	} else {
		cargar_datos_rips();
	}
}

function cargar_datos_rips() {
	cerrar_div_centro();
	if (validar_cargar_datos_rips()) {
		var params = "opcion=2&id_convenio=" + $("#cmb_convenio").val() +
					 "&id_plan=" + $("#cmb_plan").val() +
					 "&fecha_inicial=" + $("#txt_fecha_inicial").val() +
					 "&fecha_final=" + $("#txt_fecha_final").val() +
					 "&tipo_factura=" + $("#cmb_tipo_factura").val() +
					 "&id_prestador=" + $("#cmb_prestador").val() +
					 "&ind_rips_existentes=" + ($("#chk_rips_existentes").is(":checked") ? 1 : 0) +
					 "&ind_ac=" + ($("#chk_ac").is(":checked") ? 1 : 0) +
					 "&ind_ap=" + ($("#chk_ap").is(":checked") ? 1 : 0) +
					 "&ind_am=" + ($("#chk_am").is(":checked") ? 1 : 0) +
					 "&ind_at=" + ($("#chk_at").is(":checked") ? 1 : 0) +
					 "&ind_us=" + ($("#chk_us").is(":checked") ? 1 : 0) +
					 "&ind_af=" + ($("#chk_af").is(":checked") ? 1 : 0) +
					 "&ind_ad=" + ($("#chk_ad").is(":checked") ? 1 : 0) +
					 "&ind_ct=" + ($("#chk_ct").is(":checked") ? 1 : 0) +
					 "&ind_sin_np=" + ($("#chk_sin_np").is(":checked") ? 1 : 0);
		
		$("#btn_cargar_datos").attr("disabled", "disabled");
		$("#d_cargar_datos_rips").html("");
		llamarAjax("generador_rips_ajax.php", params, "d_cargar_datos_rips", "terminar_cargar_datos_rips();");
	} else {
		$("#contenedor_error").css("display", "block");
		$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
	}
}

function validar_cargar_datos_rips() {
	var resultado = true;
	
	$("#contenedor_error").css("display", "none");
	$("#cmb_convenio").removeClass("bordeAdmision");
	$("#txt_fecha_inicial").removeClass("bordeAdmision");
	$("#txt_fecha_final").removeClass("bordeAdmision");
	$("#cmb_tipo_factura").removeClass("bordeAdmision");
	$("#td_rips").removeClass("bordeAdmision");
	
	if ($("#cmb_convenio").val() == "") {
		$("#cmb_convenio").addClass("bordeAdmision");
		resultado = false;
	}
	if ($("#txt_fecha_inicial").val() == "") {
		$("#txt_fecha_inicial").addClass("bordeAdmision");
		resultado = false;
	}
	if ($("#txt_fecha_final").val() == "") {
		$("#txt_fecha_final").addClass("bordeAdmision");
		resultado = false;
	}
	if ($("#cmb_tipo_factura").val() == "") {
		$("#cmb_tipo_factura").addClass("bordeAdmision");
		resultado = false;
	}
	if (!$("#chk_ct").is(":checked") && !$("#chk_af").is(":checked") && !$("#chk_us").is(":checked") && !$("#chk_ad").is(":checked")
			&& !$("#chk_ac").is(":checked") && !$("#chk_ap").is(":checked") && !$("#chk_ah").is(":checked") && !$("#chk_au").is(":checked")
			&& !$("#chk_an").is(":checked") && !$("#chk_am").is(":checked") && !$("#chk_at").is(":checked")) {
		$("#td_rips").addClass("bordeAdmision");
		resultado = false;
	}
	
	return resultado;
}

function terminar_cargar_datos_rips() {
	$("#btn_cargar_datos").removeAttr("disabled");
	//cargar_rips_disponibles();
}

function cargar_rips_disponibles() {
	var params = "opcion=3&id_convenio=" + $("#cmb_convenio").val() +
				 "&id_plan=" + $("#cmb_plan").val();
	
	llamarAjax("generador_rips_ajax.php", params, "d_descargar_rips", "");
}

function cargar_nombre_procedimiento(cod_procedimiento, td_nombre_procedimiento) {
	var params = "opcion=5&cod_procedimiento=" + cod_procedimiento;
	
	llamarAjax("generador_rips_ajax.php", params, td_nombre_procedimiento, "");
}

function cargar_nombre_ciex(cod_ciex, td_nombre_ciex) {
	var params = "opcion=6&cod_ciex=" + cod_ciex;
	
	llamarAjax("generador_rips_ajax.php", params, td_nombre_ciex, "");
}

function agregar_numero_factura(num_factura) {
	//Consultas
	var cont_aux = 0;
	while ($("#txt_num_factura_ac_" + cont_aux).length > 0) {
		$("#txt_num_factura_ac_" + cont_aux).val(num_factura);
		
		cont_aux++;
	}
	
	//Procedimientos
	var cont_aux = 0;
	while ($("#txt_num_factura_ap_" + cont_aux).length > 0) {
		$("#txt_num_factura_ap_" + cont_aux).val(num_factura);
		
		cont_aux++;
	}
	
	//Medicamentos
	var cont_aux = 0;
	while ($("#txt_num_factura_am_" + cont_aux).length > 0) {
		$("#txt_num_factura_am_" + cont_aux).val(num_factura);
		
		cont_aux++;
	}
	
	//Otros servicios
	var cont_aux = 0;
	while ($("#txt_num_factura_at_" + cont_aux).length > 0) {
		$("#txt_num_factura_at_" + cont_aux).val(num_factura);
		
		cont_aux++;
	}
	
	//Facturas
	var cont_aux = 0;
	while ($("#txt_num_factura_af_" + cont_aux).length > 0) {
		$("#txt_num_factura_af_" + cont_aux).val(num_factura);
		
		cont_aux++;
	}
}

function guardar_ac(ind_mostrar) {
	var ind_ac = parseInt($("#hdd_ac").val(), 10);
	if (ind_ac == 1) {
		if (validar_guardar_ac()) {
			var params = "opcion=7&id_convenio=" + $("#hdd_id_convenio").val() +
						 "&id_plan=" + $("#hdd_id_plan").val() +
						 "&fecha_inicial=" + $("#hdd_fecha_ini").val() +
						 "&fecha_final=" + $("#hdd_fecha_fin").val() +
						 "&id_prestador=" + $("#hdd_prestador").val();
			
			var cant_registros_ac = parseInt($("#hdd_cant_registros_ac").val(), 10);
			for (var i = 0; i < cant_registros_ac; i++) {
				params += "&id_rips_ac_" + i + "=" + $("#hdd_id_rips_ac_" + i).val() +
						  "&id_admision_ac_" + i + "=" + $("#hdd_id_admision_ac_" + i).val() +
						  "&id_detalle_precio_ac_" + i + "=" + $("#hdd_id_detalle_precio_ac_" + i).val() +
						  "&id_paciente_ac_" + i + "=" + $("#hdd_id_paciente_ac_" + i).val() +
						  "&id_hc_ac_" + i + "=" + $("#hdd_id_hc_ac_" + i).val() +
						  "&ind_revisado_ac_" + i + "=" + ($("#chk_revisado_ac_" + i).is(":checked") ? 1 : 0) +
						  "&ind_borrado_ac_" + i + "=" + ($("#chk_borrado_ac_" + i).is(":checked") ? 1 : 0) +
						  "&num_factura_ac_" + i + "=" + str_encode($("#txt_num_factura_ac_" + i).val()) +
						  "&id_convenio_ac_" + i + "=" + $("#cmb_convenio_ac_" + i).val() +
						  "&id_plan_ac_" + i + "=" + $("#cmb_plan_ac_" + i).val() +
						  "&tipo_documento_ac_" + i + "=" + $("#cmb_tipo_documento_ac_" + i).val() +
						  "&numero_documento_ac_" + i + "=" + str_encode($("#txt_numero_documento_ac_" + i).val()) +
						  "&fecha_consulta_ac_" + i + "=" + $("#txt_fecha_consulta_ac_" + i).val() +
						  "&num_autorizacion_ac_" + i + "=" + str_encode($("#txt_num_autorizacion_ac_" + i).val()) +
						  "&cod_procedimiento_ac_" + i + "=" + $("#txt_cod_procedimiento_ac_" + i).val() +
						  "&fin_consulta_ac_" + i + "=" + $("#cmb_fin_consulta_ac_" + i).val() +
						  "&causa_ext_ac_" + i + "=" + $("#cmb_causa_ext_ac_" + i).val() +
						  "&cod_ciex_ac_" + i + "_0=" + $("#txt_cod_ciex_ac_" + i + "_0").val() +
						  "&cod_ciex_ac_" + i + "_1=" + $("#txt_cod_ciex_ac_" + i + "_1").val() +
						  "&cod_ciex_ac_" + i + "_2=" + $("#txt_cod_ciex_ac_" + i + "_2").val() +
						  "&cod_ciex_ac_" + i + "_3=" + $("#txt_cod_ciex_ac_" + i + "_3").val() +
						  "&tipo_diag_prin_ac_" + i + "=" + $("#cmb_tipo_diag_prin_ac_" + i).val() +
						  "&valor_consulta_ac_" + i + "=" + $("#txt_valor_consulta_ac_" + i).val() +
						  "&valor_cuota_ac_" + i + "=" + $("#txt_valor_cuota_ac_" + i).val() +
						  "&observaciones_ac_" + i + "=" + str_encode($("#txt_observaciones_ac_" + i).val());
			}
			params += "&cant_registros_ac=" + cant_registros_ac;
			
			llamarAjax("generador_rips_ajax.php", params, "d_guardar_ac", "finalizar_guardar_rips(\"ac\", " + ind_mostrar + ");");
		} else {
			$("#contenedor_error").css("display", "block");
			$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
		}
	}
}

function validar_guardar_ac() {
	var cant_registros_ac = parseInt($("#hdd_cant_registros_ac").val(), 10);
	$("#contenedor_error").css("display", "none");
	var resultado = true;
	
	for (var i = 0; i < cant_registros_ac; i++) {
		if ($("#hdd_con_datos_ac_" + i).val() == "1") {
			$("#cmb_convenio_ac_" + i).removeClass("bordeAdmision");
			$("#cmb_plan_ac_" + i).removeClass("bordeAdmision");
			$("#txt_fecha_consulta_ac_" + i).removeClass("bordeAdmision");
			
			if ($("#cmb_convenio_ac_" + i).val() == "") {
				$("#cmb_convenio_ac_" + i).addClass("bordeAdmision");
				resultado = false;
			}
			if ($("#cmb_plan_ac_" + i).val() == "") {
				$("#cmb_plan_ac_" + i).addClass("bordeAdmision");
				resultado = false;
			}
			if ($("#txt_fecha_consulta_ac_" + i).val() == "") {
				$("#txt_fecha_consulta_ac_" + i).addClass("bordeAdmision");
				resultado = false;
			}
		}
	}
	
	return resultado;
}

function guardar_ap(ind_mostrar) {
	var ind_ap = parseInt($("#hdd_ap").val(), 10);
	if (ind_ap == 1) {
		if (validar_guardar_ap()) {
			var params = "opcion=8&id_convenio=" + $("#hdd_id_convenio").val() +
						 "&id_plan=" + $("#hdd_id_plan").val() +
						 "&fecha_inicial=" + $("#hdd_fecha_ini").val() +
						 "&fecha_final=" + $("#hdd_fecha_fin").val() +
						 "&id_prestador=" + $("#hdd_prestador").val();
			
			var cant_registros_ap = parseInt($("#hdd_cant_registros_ap").val(), 10);
			for (var i = 0; i < cant_registros_ap; i++) {
				params += "&id_rips_ap_" + i + "=" + $("#hdd_id_rips_ap_" + i).val() +
						  "&id_admision_ap_" + i + "=" + $("#hdd_id_admision_ap_" + i).val() +
						  "&id_detalle_precio_ap_" + i + "=" + $("#hdd_id_detalle_precio_ap_" + i).val() +
						  "&id_paciente_ap_" + i + "=" + $("#hdd_id_paciente_ap_" + i).val() +
						  "&id_hc_ap_" + i + "=" + $("#hdd_id_hc_ap_" + i).val() +
						  "&ind_revisado_ap_" + i + "=" + ($("#chk_revisado_ap_" + i).is(":checked") ? 1 : 0) +
						  "&ind_borrado_ap_" + i + "=" + ($("#chk_borrado_ap_" + i).is(":checked") ? 1 : 0) +
						  "&num_factura_ap_" + i + "=" + str_encode($("#txt_num_factura_ap_" + i).val()) +
						  "&id_convenio_ap_" + i + "=" + $("#cmb_convenio_ap_" + i).val() +
						  "&id_plan_ap_" + i + "=" + $("#cmb_plan_ap_" + i).val() +
						  "&tipo_documento_ap_" + i + "=" + $("#cmb_tipo_documento_ap_" + i).val() +
						  "&numero_documento_ap_" + i + "=" + str_encode($("#txt_numero_documento_ap_" + i).val()) +
						  "&fecha_pro_ap_" + i + "=" + $("#txt_fecha_pro_ap_" + i).val() +
						  "&num_autorizacion_ap_" + i + "=" + str_encode($("#txt_num_autorizacion_ap_" + i).val()) +
						  "&cod_procedimiento_ap_" + i + "=" + $("#txt_cod_procedimiento_ap_" + i).val() +
						  "&amb_rea_ap_" + i + "=" + $("#cmb_amb_rea_ap_" + i).val() +
						  "&fin_pro_ap_" + i + "=" + $("#cmb_fin_pro_ap_" + i).val() +
						  "&per_ati_ap_" + i + "=" + $("#cmb_per_ati_ap_" + i).val() +
						  "&cod_ciex_prin_ap_" + i + "=" + $("#txt_cod_ciex_prin_ap_" + i).val() +
						  "&cod_ciex_rel_ap_" + i + "=" + $("#txt_cod_ciex_rel_ap_" + i).val() +
						  "&cod_ciex_com_ap_" + i + "=" + $("#txt_cod_ciex_com_ap_" + i).val() +
						  "&for_rea_ap_" + i + "=" + $("#cmb_for_rea_ap_" + i).val() +
						  "&valor_pro_ap_" + i + "=" + $("#txt_valor_pro_ap_" + i).val() +
						  "&valor_copago_ap_" + i + "=" + $("#txt_valor_copago_ap_" + i).val() +
						  "&observaciones_ap_" + i + "=" + str_encode($("#txt_observaciones_ap_" + i).val());
			}
			params += "&cant_registros_ap=" + cant_registros_ap;
			
			llamarAjax("generador_rips_ajax.php", params, "d_guardar_ap", "finalizar_guardar_rips(\"ap\", " + ind_mostrar + ");");
		} else {
			$("#contenedor_error").css("display", "block");
			$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
		}
	}
}

function validar_guardar_ap() {
	var cant_registros_ap = parseInt($("#hdd_cant_registros_ap").val(), 10);
	$("#contenedor_error").css("display", "none");
	var resultado = true;
	
	for (var i = 0; i < cant_registros_ap; i++) {
		if ($("#hdd_con_datos_ap_" + i).val() == "1") {
			$("#cmb_convenio_ap_" + i).removeClass("bordeAdmision");
			$("#cmb_plan_ap_" + i).removeClass("bordeAdmision");
			$("#txt_fecha_pro_ap_" + i).removeClass("bordeAdmision");
			
			if ($("#cmb_convenio_ap_" + i).val() == "") {
				$("#cmb_convenio_ap_" + i).addClass("bordeAdmision");
				resultado = false;
			}
			if ($("#cmb_plan_ap_" + i).val() == "") {
				$("#cmb_plan_ap_" + i).addClass("bordeAdmision");
				resultado = false;
			}
			if ($("#txt_fecha_pro_ap_" + i).val() == "") {
				$("#txt_fecha_pro_ap_" + i).addClass("bordeAdmision");
				resultado = false;
			}
		}
	}
	
	return resultado;
}

function guardar_am(ind_mostrar) {
	var ind_am = parseInt($("#hdd_am").val(), 10);
	if (ind_am == 1) {
		if (validar_guardar_am()) {
			var params = "opcion=9&id_convenio=" + $("#hdd_id_convenio").val() +
						 "&id_plan=" + $("#hdd_id_plan").val() +
						 "&fecha_inicial=" + $("#hdd_fecha_ini").val() +
						 "&fecha_final=" + $("#hdd_fecha_fin").val() +
						 "&id_prestador=" + $("#hdd_prestador").val();
			
			var cant_registros_am = parseInt($("#hdd_cant_registros_am").val(), 10);
			for (var i = 0; i < cant_registros_am; i++) {
				params += "&id_rips_am_" + i + "=" + $("#hdd_id_rips_am_" + i).val() +
						  "&id_admision_am_" + i + "=" + $("#hdd_id_admision_am_" + i).val() +
						  "&id_detalle_precio_am_" + i + "=" + $("#hdd_id_detalle_precio_am_" + i).val() +
						  "&id_paciente_am_" + i + "=" + $("#hdd_id_paciente_am_" + i).val() +
						  "&ind_revisado_am_" + i + "=" + ($("#chk_revisado_am_" + i).is(":checked") ? 1 : 0) +
						  "&ind_borrado_am_" + i + "=" + ($("#chk_borrado_am_" + i).is(":checked") ? 1 : 0) +
						  "&num_factura_am_" + i + "=" + str_encode($("#txt_num_factura_am_" + i).val()) +
						  "&id_convenio_am_" + i + "=" + $("#cmb_convenio_am_" + i).val() +
						  "&id_plan_am_" + i + "=" + $("#cmb_plan_am_" + i).val() +
						  "&tipo_documento_am_" + i + "=" + $("#cmb_tipo_documento_am_" + i).val() +
						  "&numero_documento_am_" + i + "=" + str_encode($("#txt_numero_documento_am_" + i).val()) +
						  "&fecha_medicamento_am_" + i + "=" + $("#txt_fecha_medicamento_am_" + i).val() +
						  "&num_autorizacion_am_" + i + "=" + str_encode($("#txt_num_autorizacion_am_" + i).val()) +
						  "&cod_medicamento_am_" + i + "=" + $("#txt_cod_medicamento_am_" + i).val() +
						  "&tipo_medicamento_am_" + i + "=" + $("#cmb_tipo_medicamento_am_" + i).val() +
						  "&nombre_generico_am_" + i + "=" + str_encode($("#txt_nombre_generico_am_" + i).val()) +
						  "&forma_farma_am_" + i + "=" + str_encode($("#txt_forma_farma_am_" + i).val()) +
						  "&concentracion_am_" + i + "=" + str_encode($("#txt_concentracion_am_" + i).val()) +
						  "&unidad_medida_am_" + i + "=" + str_encode($("#txt_unidad_medida_am_" + i).val()) +
						  "&cantidad_am_" + i + "=" + $("#txt_cantidad_am_" + i).val() +
						  "&valor_medicamento_am_" + i + "=" + $("#txt_valor_medicamento_am_" + i).val() +
						  "&observaciones_am_" + i + "=" + str_encode($("#txt_observaciones_am_" + i).val());
			}
			params += "&cant_registros_am=" + cant_registros_am;
			
			llamarAjax("generador_rips_ajax.php", params, "d_guardar_am", "finalizar_guardar_rips(\"am\", " + ind_mostrar + ");");
		} else {
			$("#contenedor_error").css("display", "block");
			$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
		}
	}
}

function validar_guardar_am() {
	var cant_registros_am = parseInt($("#hdd_cant_registros_am").val(), 10);
	$("#contenedor_error").css("display", "none");
	var resultado = true;
	
	for (var i = 0; i < cant_registros_am; i++) {
		if ($("#hdd_con_datos_am_" + i).val() == "1") {
			$("#cmb_convenio_am_" + i).removeClass("bordeAdmision");
			$("#cmb_plan_am_" + i).removeClass("bordeAdmision");
			$("#txt_fecha_medicamento_am_" + i).removeClass("bordeAdmision");
			
			if ($("#cmb_convenio_am_" + i).val() == "") {
				$("#cmb_convenio_am_" + i).addClass("bordeAdmision");
				resultado = false;
			}
			if ($("#cmb_plan_am_" + i).val() == "") {
				$("#cmb_plan_am_" + i).addClass("bordeAdmision");
				resultado = false;
			}
			if ($("#txt_fecha_medicamento_am_" + i).val() == "") {
				$("#txt_fecha_medicamento_am_" + i).addClass("bordeAdmision");
				resultado = false;
			}
		}
	}
	
	return resultado;
}

function guardar_at(ind_mostrar) {
	var ind_at = parseInt($("#hdd_at").val(), 10);
	if (ind_at == 1) {
		if (validar_guardar_at()) {
			var params = "opcion=10&id_convenio=" + $("#hdd_id_convenio").val() +
						 "&id_plan=" + $("#hdd_id_plan").val() +
						 "&fecha_inicial=" + $("#hdd_fecha_ini").val() +
						 "&fecha_final=" + $("#hdd_fecha_fin").val() +
						 "&id_prestador=" + $("#hdd_prestador").val();
			
			var cant_registros_at = parseInt($("#hdd_cant_registros_at").val(), 10);
			for (var i = 0; i < cant_registros_at; i++) {
				params += "&id_rips_at_" + i + "=" + $("#hdd_id_rips_at_" + i).val() +
						  "&id_admision_at_" + i + "=" + $("#hdd_id_admision_at_" + i).val() +
						  "&id_detalle_precio_at_" + i + "=" + $("#hdd_id_detalle_precio_at_" + i).val() +
						  "&id_paciente_at_" + i + "=" + $("#hdd_id_paciente_at_" + i).val() +
						  "&ind_revisado_at_" + i + "=" + ($("#chk_revisado_at_" + i).is(":checked") ? 1 : 0) +
						  "&ind_borrado_at_" + i + "=" + ($("#chk_borrado_at_" + i).is(":checked") ? 1 : 0) +
						  "&num_factura_at_" + i + "=" + str_encode($("#txt_num_factura_at_" + i).val()) +
						  "&id_convenio_at_" + i + "=" + $("#cmb_convenio_at_" + i).val() +
						  "&id_plan_at_" + i + "=" + $("#cmb_plan_at_" + i).val() +
						  "&tipo_documento_at_" + i + "=" + $("#cmb_tipo_documento_at_" + i).val() +
						  "&numero_documento_at_" + i + "=" + str_encode($("#txt_numero_documento_at_" + i).val()) +
						  "&fecha_insumo_at_" + i + "=" + $("#txt_fecha_insumo_at_" + i).val() +
						  "&num_autorizacion_at_" + i + "=" + str_encode($("#txt_num_autorizacion_at_" + i).val()) +
						  "&tipo_insumo_at_" + i + "=" + $("#cmb_tipo_insumo_at_" + i).val() +
						  "&cod_insumo_at_" + i + "=" + $("#txt_cod_insumo_at_" + i).val() +
						  "&nombre_insumo_at_" + i + "=" + str_encode($("#txt_nombre_insumo_at_" + i).val()) +
						  "&cantidad_at_" + i + "=" + $("#txt_cantidad_at_" + i).val() +
						  "&valor_insumo_at_" + i + "=" + $("#txt_valor_insumo_at_" + i).val() +
						  "&observaciones_at_" + i + "=" + str_encode($("#txt_observaciones_at_" + i).val());
			}
			params += "&cant_registros_at=" + cant_registros_at;
			
			llamarAjax("generador_rips_ajax.php", params, "d_guardar_at", "finalizar_guardar_rips(\"at\", " + ind_mostrar + ");");
		} else {
			$("#contenedor_error").css("display", "block");
			$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
		}
	}
}

function validar_guardar_at() {
	var cant_registros_at = parseInt($("#hdd_cant_registros_at").val(), 10);
	$("#contenedor_error").css("display", "none");
	var resultado = true;
	
	for (var i = 0; i < cant_registros_at; i++) {
		if ($("#hdd_con_datos_at_" + i).val() == "1") {
			$("#cmb_convenio_at_" + i).removeClass("bordeAdmision");
			$("#cmb_plan_at_" + i).removeClass("bordeAdmision");
			$("#txt_fecha_insumo_at_" + i).removeClass("bordeAdmision");
			
			if ($("#cmb_convenio_at_" + i).val() == "") {
				$("#cmb_convenio_at_" + i).addClass("bordeAdmision");
				resultado = false;
			}
			if ($("#cmb_plan_at_" + i).val() == "") {
				$("#cmb_plan_at_" + i).addClass("bordeAdmision");
				resultado = false;
			}
			if ($("#txt_fecha_insumo_at_" + i).val() == "") {
				$("#txt_fecha_insumo_at_" + i).addClass("bordeAdmision");
				resultado = false;
			}
		}
	}
	
	return resultado;
}

function guardar_us(ind_mostrar) {
	var ind_us = parseInt($("#hdd_us").val(), 10);
	if (ind_us == 1) {
		var params = "opcion=12&id_rips=" + $("#hdd_id_rips").val() +
					 "&id_convenio=" + $("#hdd_id_convenio").val() +
					 "&id_plan=" + $("#hdd_id_plan").val() +
					 "&fecha_ini=" + $("#hdd_fecha_ini").val() +
					 "&fecha_fin=" + $("#hdd_fecha_fin").val() +
					 "&tipo_factura=" + $("#hdd_tipo_factura").val() +
					 "&num_factura=" + str_encode($("#txt_num_factura").val());
		
		var cant_registros_us = parseInt($("#hdd_cant_registros_us").val(), 10);
		for (var i = 0; i < cant_registros_us; i++) {
			params += "&id_rips_us_" + i + "=" + $("#hdd_id_rips_us_" + i).val() +
					  "&id_paciente_us_" + i + "=" + $("#hdd_id_paciente_us_" + i).val() +
					  "&ind_revisado_us_" + i + "=" + ($("#chk_revisado_us_" + i).is(":checked") ? 1 : 0) +
					  "&ind_borrado_us_" + i + "=" + ($("#chk_borrado_us_" + i).is(":checked") ? 1 : 0) +
					  "&tipo_documento_us_" + i + "=" + $("#cmb_tipo_documento_us_" + i).val() +
					  "&numero_documento_us_" + i + "=" + str_encode($("#txt_numero_documento_us_" + i).val()) +
					  "&nombre_1_us_" + i + "=" + str_encode($("#txt_nombre_1_us_" + i).val()) +
					  "&nombre_2_us_" + i + "=" + str_encode($("#txt_nombre_2_us_" + i).val()) +
					  "&apellido_1_us_" + i + "=" + str_encode($("#txt_apellido_1_us_" + i).val()) +
					  "&apellido_2_us_" + i + "=" + str_encode($("#txt_apellido_2_us_" + i).val()) +
					  "&tipo_usuario_us_" + i + "=" + $("#cmb_tipo_usuario_us_" + i).val() +
					  "&edad_us_" + i + "=" + $("#txt_edad_us_" + i).val() +
					  "&unidad_edad_us_" + i + "=" + $("#cmb_unidad_edad_us_" + i).val() +
					  "&sexo_us_" + i + "=" + $("#cmb_sexo_us_" + i).val() +
					  "&cod_dep_us_" + i + "=" + $("#cmb_cod_dep_us_" + i).val() +
					  "&cod_mun_us_" + i + "=" + $("#cmb_cod_mun_us_" + i).val() +
					  "&zona_us_" + i + "=" + $("#cmb_zona_us_" + i).val() +
					  "&observaciones_us_" + i + "=" + str_encode($("#txt_observaciones_us_" + i).val());
		}
		params += "&cant_registros_us=" + cant_registros_us;
		
		llamarAjax("generador_rips_ajax.php", params, "d_guardar_us", "finalizar_guardar_rips(\"us\", " + ind_mostrar + ");");
	}
}

function guardar_af(ind_mostrar) {
	var ind_af = parseInt($("#hdd_af").val(), 10);
	if (ind_af == 1) {
		var params = "opcion=13&id_rips=" + $("#hdd_id_rips").val() +
					 "&id_convenio=" + $("#hdd_id_convenio").val() +
					 "&id_plan=" + $("#hdd_id_plan").val() +
					 "&fecha_ini=" + $("#hdd_fecha_ini").val() +
					 "&fecha_fin=" + $("#hdd_fecha_fin").val() +
					 "&tipo_factura=" + $("#hdd_tipo_factura").val() +
					 "&num_factura=" + str_encode($("#txt_num_factura").val()) +
					 "&id_prestador=" + $("#hdd_prestador").val();
		
		var cant_registros_af = parseInt($("#hdd_cant_registros_af").val(), 10);
		var cont_aux = 0;
		for (var i = 0; i < cant_registros_af; i++) {
			if ($("#tr_reg_af_" + i).css("display") != "none") {
				params += "&num_factura_af_" + cont_aux + "=" + $("#txt_num_factura_af_" + i).val() +
						  "&fecha_factura_af_" + cont_aux + "=" + $("#txt_fecha_factura_af_" + i).val() +
						  "&fecha_ini_af_" + cont_aux + "=" + $("#txt_fecha_ini_af_" + i).val() +
						  "&fecha_fin_af_" + cont_aux + "=" + $("#txt_fecha_fin_af_" + i).val() +
						  "&num_contrato_af_" + cont_aux + "=" + str_encode($("#txt_num_contrato_af_" + i).val()) +
						  "&plan_benef_af_" + cont_aux + "=" + str_encode($("#txt_plan_benef_af_" + i).val()) +
						  "&num_poliza_af_" + cont_aux + "=" + str_encode($("#txt_num_poliza_af_" + i).val()) +
						  "&valor_copago_af_" + cont_aux + "=" + $("#txt_valor_copago_af_" + i).val() +
						  "&valor_comision_af_" + cont_aux + "=" + $("#txt_valor_comision_af_" + i).val() +
						  "&valor_descuento_af_" + cont_aux + "=" + $("#txt_valor_descuento_af_" + i).val() +
						  "&valor_neto_af_" + cont_aux + "=" + $("#txt_valor_neto_af_" + i).val();
				cont_aux++;
			}
		}
		params += "&cant_registros_af=" + cont_aux;
		
		llamarAjax("generador_rips_ajax.php", params, "d_guardar_af", "finalizar_guardar_rips(\"af\", " + ind_mostrar + ");");
	}
}

function guardar_ad(ind_mostrar) {
	var ind_ad = parseInt($("#hdd_ad").val(), 10);
	if (ind_ad == 1) {
		var params = "opcion=14&id_rips=" + $("#hdd_id_rips").val() +
					 "&id_convenio=" + $("#hdd_id_convenio").val() +
					 "&id_plan=" + $("#hdd_id_plan").val() +
					 "&fecha_ini=" + $("#hdd_fecha_ini").val() +
					 "&fecha_fin=" + $("#hdd_fecha_fin").val() +
					 "&tipo_factura=" + $("#hdd_tipo_factura").val() +
					 "&num_factura=" + str_encode($("#txt_num_factura").val()) +
					 "&id_prestador=" + $("#hdd_prestador").val();
		
		var cant_registros_ad = parseInt($("#hdd_cant_registros_ad").val(), 10);
		var cont_aux = 0;
		for (var i = 0; i < cant_registros_ad; i++) {
			if ($("#tr_reg_ad_" + i).css("display") != "none") {
				params += "&num_factura_ad_" + cont_aux + "=" + $("#txt_num_factura_ad_" + i).val() +
						  "&cod_concepto_ad_" + cont_aux + "=" + $("#cmb_cod_concepto_ad_" + i).val() +
						  "&cantidad_ad_" + cont_aux + "=" + $("#txt_cantidad_ad_" + i).val() +
						  "&valor_unitario_ad_" + cont_aux + "=" + $("#txt_valor_unitario_ad_" + i).val();
				cont_aux++;
			}
		}
		params += "&cant_registros_ad=" + cont_aux;
		
		llamarAjax("generador_rips_ajax.php", params, "d_guardar_ad", "finalizar_guardar_rips(\"ad\", " + ind_mostrar + ");");
	}
}

function finalizar_guardar_rips(sigla, ind_mostrar) {
	switch (sigla) {
		case "ac":
		case "ap":
		case "am":
		case "at":
			var ok_carga = parseInt($("#hdd_" + sigla + "_ok_carga").val(), 10);
			if (ok_carga == 1) {
				var borrar_resul = parseInt($("#hdd_borrar_" + sigla + "_resul").val(), 10);
				if (borrar_resul > 0) {
					var cant_registros = parseInt($("#hdd_cant_registros_" + sigla + "_resul").val(), 10);
					
					var bol_error = false;
					var indice_error = 0;
					for (var i = 0; i < cant_registros; i++) {
						if ($("#hdd_" + sigla + "_resul_" + i).length <= 0 || parseInt($("#hdd_" + sigla + "_resul_" + i).val(), 10) <= 0) {
							bol_error = true;
							indice_error = i + 1;
							break;
						} else if ($("#hdd_id_rips_" + sigla + "_" + i).val() == "0" || $("#hdd_id_rips_" + sigla + "_" + i).val() == "") {
							$("#hdd_id_rips_" + sigla + "_" + i).val($("#hdd_" + sigla + "_resul_" + i).val());
						}
					}
					
					if (!bol_error) {
						if (ind_mostrar == 1) {
							$("#contenedor_exito").css("display", "block");
							$("#contenedor_exito").html("Datos guardados correctamente");
							setTimeout("$('#contenedor_exito').slideUp(200).css('display', 'none')", 2000);
						}
					} else {
						$("#contenedor_error").css("display", "block");
						$("#contenedor_error").html("Error al tratar de crear el registro n\xfamero " + indice_error);
						setTimeout("$('#contenedor_error').slideUp(200).css('display', 'none')", 10000);
					}
				} else {
					$("#contenedor_error").css("display", "block");
					$("#contenedor_error").html("Error al tratar de borrar los registros no utilizados");
					setTimeout("$('#contenedor_error').slideUp(200).css('display', 'none')", 10000);
				}
			} else {
				$("#contenedor_error").css("display", "block");
				$("#contenedor_error").html("Error - La cantidad de datos enviados supera los l&iacute;mites aceptados por el servidor, por favor seleccione un rango de fechas menor.");
				setTimeout("$('#contenedor_error').slideUp(200).css('display', 'none')", 10000);
			}
			break;
			
		case "us":
			var ok_carga = parseInt($("#hdd_" + sigla + "_ok_carga").val(), 10);
			if (ok_carga == 1) {
				var id_rips_resul = parseInt($("#hdd_id_rips_resul_" + sigla).val(), 10);
				
				if (id_rips_resul > 0) {
					$("#hdd_id_rips").val(id_rips_resul);
					
					var cant_registros = parseInt($("#hdd_cant_registros_" + sigla + "_resul").val(), 10);
					
					var bol_error = false;
					var indice_error = 0;
					for (var i = 0; i < cant_registros; i++) {
						if ($("#hdd_" + sigla + "_resul_" + i).length <= 0 || parseInt($("#hdd_" + sigla + "_resul_" + i).val(), 10) <= 0) {
							bol_error = true;
							indice_error = i + 1;
							break;
						} else if ($("#hdd_id_rips_" + sigla + "_" + i).val() == "0" || $("#hdd_id_rips_" + sigla + "_" + i).val() == "") {
							$("#hdd_id_rips_" + sigla + "_" + i).val($("#hdd_" + sigla + "_resul_" + i).val());
						}
					}
					
					if (!bol_error) {
						if (ind_mostrar == 1) {
							$("#contenedor_exito").css("display", "block");
							$("#contenedor_exito").html("Datos guardados correctamente");
							setTimeout("$('#contenedor_exito').slideUp(200).css('display', 'none')", 2000);
						}
					} else {
						$("#contenedor_error").css("display", "block");
						$("#contenedor_error").html("Error al tratar de crear el registro n\xfamero " + indice_error);
						setTimeout("$('#contenedor_error').slideUp(200).css('display', 'none')", 10000);
					}
				} else {
					$("#contenedor_error").css("display", "block");
					$("#contenedor_error").html("Error al tratar de crear el registro de RIPS");
					setTimeout("$('#contenedor_error').slideUp(200).css('display', 'none')", 10000);
				}
			} else {
				$("#contenedor_error").css("display", "block");
				$("#contenedor_error").html("Error - La cantidad de datos enviados supera los l&iacute;mites aceptados por el servidor, por favor seleccione un rango de fechas menor.");
				setTimeout("$('#contenedor_error').slideUp(200).css('display', 'none')", 10000);
			}
			break;
		case "af":
		case "ad":
			var ok_carga = parseInt($("#hdd_" + sigla + "_ok_carga").val(), 10);
			if (ok_carga == 1) {
				var id_rips_resul = parseInt($("#hdd_id_rips_resul_" + sigla).val(), 10);
				
				if (id_rips_resul > 0) {
					$("#hdd_id_rips").val(id_rips_resul);
					
					var cant_registros = parseInt($("#hdd_cant_registros_" + sigla + "_resul").val(), 10);
					
					var bol_error = false;
					var indice_error = 0;
					for (var i = 0; i < cant_registros; i++) {
						if ($("#hdd_" + sigla + "_resul_" + i).length <= 0 || parseInt($("#hdd_" + sigla + "_resul_" + i).val(), 10) <= 0) {
							bol_error = true;
							indice_error = i + 1;
							break;
						}
					}
					
					if (!bol_error) {
						if (ind_mostrar == 1) {
							$("#contenedor_exito").css("display", "block");
							$("#contenedor_exito").html("Datos guardados correctamente");
							setTimeout("$('#contenedor_exito').slideUp(200).css('display', 'none')", 2000);
						}
					} else {
						$("#contenedor_error").css("display", "block");
						$("#contenedor_error").html("Error al tratar de crear el registro n\xfamero " + indice_error);
						setTimeout("$('#contenedor_error').slideUp(200).css('display', 'none')", 10000);
					}
				} else {
					$("#contenedor_error").css("display", "block");
					$("#contenedor_error").html("Error al tratar de crear el registro de RIPS");
					setTimeout("$('#contenedor_error').slideUp(200).css('display', 'none')", 10000);
				}
			} else {
				$("#contenedor_error").css("display", "block");
				$("#contenedor_error").html("Error - La cantidad de datos enviados supera los l&iacute;mites aceptados por el servidor, por favor seleccione un rango de fechas menor.");
				setTimeout("$('#contenedor_error').slideUp(200).css('display', 'none')", 10000);
			}
			break;
	}
}

function agregar_registro_ac() {
	var cant_registros = parseInt($("#hdd_cant_registros_ac").val(), 0);
	
	if (isObject(document.getElementById("tr_reg_ac_" + cant_registros))) {
		$("#tr_reg_ac_" + cant_registros).css("display", "table-row");
		$("#hdd_con_datos_ac_" + cant_registros).val(1);
		cant_registros++;
		$("#hdd_cant_registros_ac").val(cant_registros);
	} else {
		alert("L\xedmite de registros alcanzado");
	}
}

function calcular_neto_ac(indice) {
	var valor_consulta = 0.0;
	try {
		valor_consulta = parseFloat($("#txt_valor_consulta_ac_" + indice).val());
		if (isNaN(valor_consulta)) {
			valor_consulta = 0.0;
		}
	} catch (e) {
		valor_consulta = 0.0;
	}
	
	var valor_cuota = 0.0;
	try {
		valor_cuota = parseFloat($("#txt_valor_cuota_ac_" + indice).val());
		if (isNaN(valor_cuota)) {
			valor_cuota = 0.0;
		}
	} catch (e) {
		valor_cuota = 0.0;
	}
	
	var valor_neto = valor_consulta - valor_cuota;
	$("#txt_valor_neto_ac_" + indice).val(valor_neto);
}

function agregar_registro_ap() {
	var cant_registros = parseInt($("#hdd_cant_registros_ap").val(), 0);
	
	if (isObject(document.getElementById("tr_reg_ap_" + cant_registros))) {
		$("#tr_reg_ap_" + cant_registros).css("display", "table-row");
		$("#hdd_con_datos_ap_" + cant_registros).val(1);
		cant_registros++;
		$("#hdd_cant_registros_ap").val(cant_registros);
	} else {
		alert("L\xedmite de registros alcanzado");
	}
}

function agregar_registro_am() {
	var cant_registros = parseInt($("#hdd_cant_registros_am").val(), 0);
	
	if (isObject(document.getElementById("tr_reg_am_" + cant_registros))) {
		$("#tr_reg_am_" + cant_registros).css("display", "table-row");
		$("#hdd_con_datos_am_" + cant_registros).val(1);
		cant_registros++;
		$("#hdd_cant_registros_am").val(cant_registros);
	} else {
		alert("L\xedmite de registros alcanzado");
	}
}

function calcular_total_am(indice) {
	var cantidad = 0.0;
	try {
		cantidad = parseFloat($("#txt_cantidad_am_" + indice).val());
		if (isNaN(cantidad)) {
			cantidad = 0.0;
		}
	} catch (e) {
		cantidad = 0.0;
	}
	
	var valor_medicamento = 0.0;
	try {
		valor_medicamento = parseFloat($("#txt_valor_medicamento_am_" + indice).val());
		if (isNaN(valor_medicamento)) {
			valor_medicamento = 0.0;
		}
	} catch (e) {
		valor_medicamento = 0.0;
	}
	
	var valor_total_med = cantidad * valor_medicamento;
	$("#txt_valor_total_med_am_" + indice).val(valor_total_med);
}

function agregar_registro_at() {
	var cant_registros = parseInt($("#hdd_cant_registros_at").val(), 0);
	
	if (isObject(document.getElementById("tr_reg_at_" + cant_registros))) {
		$("#tr_reg_at_" + cant_registros).css("display", "table-row");
		$("#hdd_con_datos_at_" + cant_registros).val(1);
		cant_registros++;
		$("#hdd_cant_registros_at").val(cant_registros);
	} else {
		alert("L\xedmite de registros alcanzado");
	}
}

function calcular_total_at(indice) {
	var cantidad = 0.0;
	try {
		cantidad = parseFloat($("#txt_cantidad_at_" + indice).val());
		if (isNaN(cantidad)) {
			cantidad = 0.0;
		}
	} catch (e) {
		cantidad = 0.0;
	}
	
	var valor_insumo = 0.0;
	try {
		valor_insumo = parseFloat($("#txt_valor_insumo_at_" + indice).val());
		if (isNaN(valor_insumo)) {
			valor_insumo = 0.0;
		}
	} catch (e) {
		valor_insumo = 0.0;
	}
	
	var valor_total_serv = cantidad * valor_insumo;
	$("#txt_valor_total_serv_at_" + indice).val(valor_total_serv);
}

function agregar_registro_us() {
	var cant_registros = parseInt($("#hdd_cant_registros_us").val(), 0);
	
	if (isObject(document.getElementById("tr_reg_us_" + cant_registros))) {
		$("#tr_reg_us_" + cant_registros).css("display", "table-row");
		$("#hdd_con_datos_us_" + cant_registros).val(1);
		cant_registros++;
		$("#hdd_cant_registros_us").val(cant_registros);
	} else {
		alert("L\xedmite de registros alcanzado");
	}
}

function seleccionar_departamento_us(cod_dep, indice) {
	var params = "opcion=11&cod_dep=" + cod_dep + "&indice=" + indice;
	
	llamarAjax("generador_rips_ajax.php", params, "d_municipios_us_" + indice, "");
}

function recalcular_af() {
	//Se totalizan los valores
	var mapa_valores = {};
	var mapa_copagos = {};
	
	if ($("#hdd_ac").val() == "1") {
		var cant_registros_ac = parseInt($("#hdd_cant_registros_ac").val(), 0);
		for (var i = 0; i < cant_registros_ac; i++) {
			if ($("#hdd_con_datos_ac_" + i).val() == "1" && !$("#chk_borrado_ac_" + i).is(":checked")) {
				var num_factura_aux = $("#txt_num_factura_ac_" + i).val();
				
				if (num_factura_aux in mapa_valores) {
					var valor_aux = mapa_valores[num_factura_aux] + parseFloat("0" + $("#txt_valor_neto_ac_" + i).val());
					mapa_valores[num_factura_aux] = valor_aux;
				} else {
					mapa_valores[num_factura_aux] = parseFloat("0" + $("#txt_valor_neto_ac_" + i).val());
					mapa_copagos[num_factura_aux] = 0.0;
				}
			}
		}
	}
	
	if ($("#hdd_ap").val() == "1") {
		var cant_registros_ap = parseInt($("#hdd_cant_registros_ap").val(), 0);
		for (var i = 0; i < cant_registros_ap; i++) {
			if ($("#hdd_con_datos_ap_" + i).val() == "1" && !$("#chk_borrado_ap_" + i).is(":checked")) {
				var num_factura_aux = $("#txt_num_factura_ap_" + i).val();
				
				if (num_factura_aux in mapa_valores) {
					var valor_aux = mapa_valores[num_factura_aux] + parseFloat("0" + $("#txt_valor_pro_ap_" + i).val());
					mapa_valores[num_factura_aux] = valor_aux;
					var valor_copago_aux = mapa_copagos[num_factura_aux] + parseFloat("0" + $("#txt_valor_copago_ap_" + i).val());
					mapa_copagos[num_factura_aux] = valor_copago_aux;
				} else {
					mapa_valores[num_factura_aux] = parseFloat("0" + $("#txt_valor_pro_ap_" + i).val());
					mapa_copagos[num_factura_aux] = parseFloat("0" + $("#txt_valor_copago_ap_" + i).val());
				}
			}
		}
	}
	
	if ($("#hdd_am").val() == "1") {
		var cant_registros_am = parseInt($("#hdd_cant_registros_am").val(), 0);
		for (var i = 0; i < cant_registros_am; i++) {
			if ($("#hdd_con_datos_am_" + i).val() == "1" && !$("#chk_borrado_am_" + i).is(":checked")) {
				var num_factura_aux = $("#txt_num_factura_am_" + i).val();
				
				if (num_factura_aux in mapa_valores) {
					var valor_aux = mapa_valores[num_factura_aux] + parseFloat("0" + $("#txt_valor_total_med_am_" + i).val());
					mapa_valores[num_factura_aux] = valor_aux;
				} else {
					mapa_valores[num_factura_aux] = parseFloat("0" + $("#txt_valor_total_med_am_" + i).val());
					mapa_copagos[num_factura_aux] = 0.0;
				}
			}
		}
	}
	
	if ($("#hdd_at").val() == "1") {
		var cant_registros_at = parseInt($("#hdd_cant_registros_at").val(), 0);
		for (var i = 0; i < cant_registros_at; i++) {
			if ($("#hdd_con_datos_at_" + i).val() == "1" && !$("#chk_borrado_at_" + i).is(":checked")) {
				var num_factura_aux = $("#txt_num_factura_at_" + i).val();
				
				if (num_factura_aux in mapa_valores) {
					var valor_aux = mapa_valores[num_factura_aux] + parseFloat("0" + $("#txt_valor_total_serv_at_" + i).val());
					mapa_valores[num_factura_aux] = valor_aux;
				} else {
					mapa_valores[num_factura_aux] = parseFloat("0" + $("#txt_valor_total_serv_at_" + i).val());
					mapa_copagos[num_factura_aux] = 0.0;
				}
			}
		}
	}
	
	//Se ocultan las facturas que ya no aparecen en los productos
	var cant_registros_af = parseInt($("#hdd_cant_registros_af").val(), 10);
	for (var i = 0; i < cant_registros_af; i++) {
		if ($("#tr_reg_af_" + i).css("display") != "none") {
			var num_factura_aux = $("#txt_num_factura_af_" + i).val();
			if (!(num_factura_aux in mapa_valores)) {
				$("#tr_reg_af_" + i).css("display", "none");
			}
		}
	}
	
	//Se agregan los valores a las facturas
	for (var num_factura in mapa_valores) {
		//Se busca la factura en la tabla
		var bol_hallado = false;
		var cant_registros_af = parseInt($("#hdd_cant_registros_af").val(), 10);
		for (var i = 0; i < cant_registros_af; i++) {
			if ($("#txt_num_factura_af_" + i).val() == num_factura) {
				//Registro hallado
				bol_hallado = true;
				$("#txt_valor_copago_af_" + i).val(mapa_copagos[num_factura]);
				$("#txt_valor_comision_af_" + i).val(0);
				$("#txt_valor_descuento_af_" + i).val(0);
				$("#txt_valor_neto_af_" + i).val(mapa_valores[num_factura] - mapa_copagos[num_factura]);
				$("#tr_reg_af_" + i).css("display", "table-row");
				break;
			}
		}
		
		if (!bol_hallado) {
			if ($("#txt_num_factura_af_" + cant_registros_af).length > 0) {
				$("#txt_num_factura_af_" + cant_registros_af).val(num_factura);
				$("#txt_valor_copago_af_" + cant_registros_af).val(mapa_copagos[num_factura]);
				$("#txt_valor_comision_af_" + cant_registros_af).val(0);
				$("#txt_valor_descuento_af_" + cant_registros_af).val(0);
				$("#txt_valor_neto_af_" + cant_registros_af).val(mapa_valores[num_factura] - mapa_copagos[num_factura]);
				$("#tr_reg_af_" + cant_registros_af).css("display", "table-row");
				
				cant_registros_af++;
				$("#hdd_cant_registros_af").val(cant_registros_af)
			}
		}
	}
	
	//Se renumeran las facturas
	var cant_registros_af = parseInt($("#hdd_cant_registros_af").val(), 10);
	var cont_aux = 1;
	for (var i = 0; i < cant_registros_af; i++) {
		if ($("#tr_reg_af_" + i).css("display") != "none") {
			$("#td_num_fila_af_" + i).html(cont_aux)
			
			cont_aux++;
		}
	}
}

function calcular_total_ad(indice) {
	var cantidad = 0.0;
	try {
		cantidad = parseFloat($("#txt_cantidad_ad_" + indice).val());
		if (isNaN(cantidad)) {
			cantidad = 0.0;
		}
	} catch (e) {
		cantidad = 0.0;
	}
	
	var valor_unitario = 0.0;
	try {
		valor_unitario = parseFloat($("#txt_valor_unitario_ad_" + indice).val());
		if (isNaN(valor_unitario)) {
			valor_unitario = 0.0;
		}
	} catch (e) {
		valor_unitario = 0.0;
	}
	
	var valor_total = cantidad * valor_unitario;
	$("#txt_valor_total_ad_" + indice).val(valor_total);
}

function agregar_valor_mapa_ad(mapa, num_factura, tipo_concepto, valor, cantidad) {
	if (num_factura in mapa) {
		var mapa_conceptos = mapa[num_factura];
		if (tipo_concepto in mapa_conceptos) {
			var mapa_valores = mapa_conceptos[tipo_concepto];
			if (valor in mapa_valores) {
				mapa_valores[valor] = mapa_valores[valor] + cantidad;
			} else {
				mapa_valores[valor] = cantidad;
			}
			mapa_conceptos[tipo_concepto] = mapa_valores;
			mapa[num_factura] = mapa_conceptos;
		} else {
			var mapa_valores = {};
			mapa_valores[valor] = cantidad;
			mapa_conceptos[tipo_concepto] = mapa_valores;
			mapa[num_factura] = mapa_conceptos;
		}
	} else {
		var mapa_valores = {};
		mapa_valores[valor] = cantidad;
		var mapa_conceptos = {};
		mapa_conceptos[tipo_concepto] = mapa_valores;
		mapa[num_factura] = mapa_conceptos;
	}
	
	return mapa;
}

function recalcular_ad() {
	//Se totalizan los valores
	var mapa_valores = {};
	
	if ($("#hdd_ac").val() == "1") {
		var cant_registros_ac = parseInt($("#hdd_cant_registros_ac").val(), 0);
		for (var i = 0; i < cant_registros_ac; i++) {
			if ($("#hdd_con_datos_ac_" + i).val() == "1") {
				var num_factura_aux = $("#txt_num_factura_ac_" + i).val();
				var tipo_concepto_aux = "01";
				var valor_aux = "" + parseFloat("0" + $("#txt_valor_consulta_ac_" + i).val());
				var cantidad_aux = 1;
				
				mapa_valores = agregar_valor_mapa_ad(mapa_valores, num_factura_aux, tipo_concepto_aux, valor_aux, cantidad_aux);
			}
		}
	}
	
	if ($("#hdd_ap").val() == "1") {
		var cant_registros_ap = parseInt($("#hdd_cant_registros_ap").val(), 0);
		for (var i = 0; i < cant_registros_ap; i++) {
			if ($("#hdd_con_datos_ap_" + i).val() == "1") {
				var num_factura_aux = $("#txt_num_factura_ap_" + i).val();
				var tipo_concepto_aux = "";
				switch ($("#cmb_fin_pro_ap_" + i).val()) {
					case "1": //Diagnóstico
						tipo_concepto_aux += "02";
						break;
					case "2": //Terapéutico
						if ($("#cmb_for_rea_ap_" + i).val() == "") {
							tipo_concepto_aux += "03";
						} else {
							tipo_concepto_aux += "04";
						}
						break;
					case "": //Vacío
						tipo_concepto_aux += "00";
						break;
					default: //PYP
						tipo_concepto_aux += "05";
						break;
				}
				var valor_aux = "" + parseFloat("0" + $("#txt_valor_pro_ap_" + i).val());
				var cantidad_aux = 1;
				
				mapa_valores = agregar_valor_mapa_ad(mapa_valores, num_factura_aux, tipo_concepto_aux, valor_aux, cantidad_aux);
			}
		}
	}
	
	if ($("#hdd_am").val() == "1") {
		var cant_registros_am = parseInt($("#hdd_cant_registros_am").val(), 0);
		for (var i = 0; i < cant_registros_am; i++) {
			if ($("#hdd_con_datos_am_" + i).val() == "1") {
				var num_factura_aux = $("#txt_num_factura_am_" + i).val();
				var tipo_concepto_aux = "";
				switch ($("#cmb_tipo_medicamento_am_" + i).val()) {
					case "1": //POS
						tipo_concepto_aux += "12";
						break;
					case "2": //No POS
						tipo_concepto_aux += "13";
						break;
					default:
						tipo_concepto_aux += "00";
						break;
				}
				var valor_aux = "" + parseFloat("0" + $("#txt_valor_medicamento_am_" + i).val());
				var cantidad_aux = parseInt("0" + $("#txt_cantidad_am_" + i).val(), 10);
				
				mapa_valores = agregar_valor_mapa_ad(mapa_valores, num_factura_aux, tipo_concepto_aux, valor_aux, cantidad_aux);
			}
		}
	}
	
	if ($("#hdd_at").val() == "1") {
		var cant_registros_at = parseInt($("#hdd_cant_registros_at").val(), 0);
		for (var i = 0; i < cant_registros_at; i++) {
			if ($("#hdd_con_datos_at_" + i).val() == "1") {
				var num_factura_aux = $("#txt_num_factura_at_" + i).val();
				var tipo_concepto_aux = "";
				switch ($("#cmb_tipo_insumo_at_" + i).val()) {
					case "1": //Materiales e insumos
						tipo_concepto_aux += "09";
						break;
					case "2": //Traslados
						tipo_concepto_aux += "14";
						break;
					case "3": //Estancias
						tipo_concepto_aux += "06";
						break;
					case "4": //Honorarios
						tipo_concepto_aux += "07";
						break;
					default:
						tipo_concepto_aux += "00";
						break;
				}
				var valor_aux = "" + parseFloat("0" + $("#txt_valor_insumo_at_" + i).val());
				var cantidad_aux = parseInt("0" + $("#txt_cantidad_at_" + i).val(), 10);
				
				mapa_valores = agregar_valor_mapa_ad(mapa_valores, num_factura_aux, tipo_concepto_aux, valor_aux, cantidad_aux);
			}
		}
	}
	
	//Se ocultan todos los registros de descripción
	var cant_registros_ad = parseInt($("#hdd_cant_registros_ad").val(), 10);
	for (var i = 0; i < cant_registros_ad; i++) {
		$("#tr_reg_ad_" + i).css("display", "none");
	}
	$("#hdd_cant_registros_ad").val(0);
	
	//Se agregan los valores a las facturas
	var cont_aux = 0;
	for (var num_factura_aux in mapa_valores) {
		var mapa_conceptos_aux = mapa_valores[num_factura_aux];
		for (var tipo_concepto_aux in mapa_conceptos_aux) {
			var mapa_valores_aux = mapa_conceptos_aux[tipo_concepto_aux];
			for (var valor_aux in mapa_valores_aux) {
				$("#txt_num_factura_ad_" + cont_aux).val(num_factura_aux);
				$("#cmb_cod_concepto_ad_" + cont_aux).val(tipo_concepto_aux);
				$("#txt_valor_unitario_ad_" + cont_aux).val(valor_aux);
				$("#txt_cantidad_ad_" + cont_aux).val(mapa_valores_aux[valor_aux]);
				calcular_total_ad(cont_aux);
				$("#tr_reg_ad_" + cont_aux).css("display", "table-row");
				
				cont_aux++;
			}
		}
	}
	
	$("#hdd_cant_registros_ad").val(cont_aux);
}

function recalcular_ct() {
	//Se cuentan los registros
	var mapa_valores = {};
	
	if ($("#hdd_ac").val() == "1") {
		var cant_registros_ac = parseInt($("#hdd_cant_registros_ac").val(), 0);
		var cont_ac = 0;
		for (var i = 0; i < cant_registros_ac; i++) {
			if ($("#hdd_con_datos_ac_" + i).val() == "1" && !$("#chk_borrado_ac_" + i).is(":checked")) {
				cont_ac++;
			}
		}
		
		mapa_valores["AC"] = cont_ac;
	}
	
	if ($("#hdd_ap").val() == "1") {
		var cant_registros_ap = parseInt($("#hdd_cant_registros_ap").val(), 0);
		var cont_ap = 0;
		for (var i = 0; i < cant_registros_ap; i++) {
			if ($("#hdd_con_datos_ap_" + i).val() == "1" && !$("#chk_borrado_ap_" + i).is(":checked")) {
				cont_ap++;
			}
		}
		
		mapa_valores["AP"] = cont_ap;
	}
	
	if ($("#hdd_am").val() == "1") {
		var cant_registros_am = parseInt($("#hdd_cant_registros_am").val(), 0);
		var cont_am = 0;
		for (var i = 0; i < cant_registros_am; i++) {
			if ($("#hdd_con_datos_am_" + i).val() == "1" && !$("#chk_borrado_am_" + i).is(":checked")) {
				cont_am++;
			}
		}
		
		mapa_valores["AM"] = cont_am;
	}
	
	if ($("#hdd_at").val() == "1") {
		var cant_registros_at = parseInt($("#hdd_cant_registros_at").val(), 0);
		var cont_at = 0;
		for (var i = 0; i < cant_registros_at; i++) {
			if ($("#hdd_con_datos_at_" + i).val() == "1" && !$("#chk_borrado_at_" + i).is(":checked")) {
				cont_at++;
			}
		}
		
		mapa_valores["AT"] = cont_at;
	}
	
	if ($("#hdd_us").val() == "1") {
		var cant_registros_us = parseInt($("#hdd_cant_registros_us").val(), 0);
		var cont_us = 0;
		for (var i = 0; i < cant_registros_us; i++) {
			if ($("#hdd_con_datos_us_" + i).val() == "1" && !$("#chk_borrado_us_" + i).is(":checked")) {
				cont_us++;
			}
		}
		
		mapa_valores["US"] = cont_us;
	}
	
	if ($("#hdd_af").val() == "1") {
		var cant_registros_af = parseInt($("#hdd_cant_registros_af").val(), 0);
		var cont_af = 0;
		for (var i = 0; i < cant_registros_af; i++) {
			if ($("#tr_reg_af_" + i).css("display") != "none") {
				cont_af++;
			}
		}
		
		mapa_valores["AF"] = cont_af;
	}
	
	if ($("#hdd_ad").val() == "1") {
		var cant_registros_ad = parseInt($("#hdd_cant_registros_ad").val(), 0);
		var cont_ad = 0;
		for (var i = 0; i < cant_registros_ad; i++) {
			if ($("#tr_reg_ad_" + i).css("display") != "none") {
				cont_ad++;
			}
		}
		
		mapa_valores["AD"] = cont_ad;
	}
	
	//Se ocultan las filas
	var cant_registros_ct = parseInt($("#hdd_cant_registros_ct").val(), 10);
	for (var i = 0; i < cant_registros_ct; i++) {
		$("#tr_reg_ct_" + i).css("display", "none");
	}
	
	//Se agregan los valores de control
	cont_aux = 0;
	for (var cod_archivo in mapa_valores) {
		$("#td_cod_archivo_ct_" + cont_aux).html(cod_archivo);
		$("#td_cantidad_ct_" + cont_aux).html(mapa_valores[cod_archivo]);
		$("#tr_reg_ct_" + cont_aux).css("display", "table-row");
		
		cont_aux++;
	}
}

function generar_rips() {
	if (validar_generar_rips()) {
		var tiempo_espera = 100;
		if ($("#hdd_ac").val()) {
			setTimeout(function() { guardar_ac(0); }, tiempo_espera);
			tiempo_espera += 100;
		}
		if ($("#hdd_ap").val()) {
			setTimeout(function() { guardar_ap(0); }, tiempo_espera);
			tiempo_espera += 100;
		}
		if ($("#hdd_am").val()) {
			setTimeout(function() { guardar_am(0); }, tiempo_espera);
			tiempo_espera += 100;
		}
		if ($("#hdd_at").val()) {
			setTimeout(function() { guardar_at(0); }, tiempo_espera);
			tiempo_espera += 100;
		}
		if ($("#hdd_us").val()) {
			setTimeout(function() { guardar_us(0); }, tiempo_espera);
			tiempo_espera += 100;
		}
		if ($("#hdd_af").val()) {
			setTimeout(function() { guardar_af(0); }, tiempo_espera);
			tiempo_espera += 100;
		}
		if ($("#hdd_ad").val()) {
			setTimeout(function() { guardar_ad(0); }, tiempo_espera);
			tiempo_espera += 100;
		}
		
		$("#btn_generar_rips").attr("disabled", "disabled");
		$("#d_generando_rips").show();
		setTimeout(function() { continuar_generar_rips(2); }, 3000);
	} else {
		$("#contenedor_error").css("display", "block");
		$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
	}
}

function confirmar_generar_rips_directo() {
	$("#fondo_negro").css("display", "block");
	$("#d_centro").slideDown(400).css("display", "block");
	
	$("#d_interno").html(
		'<table class="datagrid" border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
			'<tr class="headegrid">' +
				'<th align="center" class="msg_alerta" style="border: 1px solid #fff;">' +
					'<h4>Los datos de las facturas ser&aacute;n recalculados.<br />&iquest;Est&aacute; seguro que desea generar directamente los archivos planos<br />sin cargarlos en pantalla?</h4>' +
				'</th>' +
			'</tr>' +
			'<tr>' +
				'<th align="center" style="width:5%;border: 1px solid #fff;">' +
					'<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" value="Aceptar" class="btnPrincipal" onclick="generar_rips_directo();"/>' +
					'&nbsp;&nbsp;' +
					'<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" value="Cancelar" class="btnPrincipal" onclick="cerrar_div_centro();"/>' +
				'</th>' +
			'</tr>' +
		'</table>');
	posicionarDivFlotante("d_centro");
}

function generar_rips_directo() {
	$("#btn_generar_rips").attr("disabled", "disabled");
	cerrar_div_centro();
	
	if (validar_cargar_datos_rips()) {
		$("#d_cargar_datos_rips").html("");
		continuar_generar_rips(1);
	} else {
		$("#contenedor_error").css("display", "block");
		$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
		$("#btn_generar_rips").removeAttr("disabled");
	}
}

function continuar_generar_rips(tipo) {
	var params = "opcion=15";
	switch (tipo) {
		case 1: //Directo
			params += "&id_rips=0&id_convenio=" + $("#cmb_convenio").val() +
					  "&id_plan=" + $("#cmb_plan").val() +
					  "&fecha_ini=" + $("#txt_fecha_inicial").val() +
					  "&fecha_fin=" + $("#txt_fecha_final").val() +
					  "&ind_ac=" + ($("#chk_ac").is(":checked") ? 1 : 0) +
					  "&ind_ap=" + ($("#chk_ap").is(":checked") ? 1 : 0) +
					  "&ind_am=" + ($("#chk_am").is(":checked") ? 1 : 0) +
					  "&ind_at=" + ($("#chk_at").is(":checked") ? 1 : 0) +
					  "&ind_us=" + ($("#chk_us").is(":checked") ? 1 : 0) +
					  "&ind_af=" + ($("#chk_af").is(":checked") ? 1 : 0) +
					  "&ind_ad=" + ($("#chk_ad").is(":checked") ? 1 : 0) +
					  "&ind_ct=" + ($("#chk_ct").is(":checked") ? 1 : 0) +
					  "&id_prestador=" + $("#cmb_prestador").val() +
					  "&ind_sin_np=" + ($("#chk_sin_np").is(":checked") ? 1 : 0);
			break;
			
		case 2: //Con carga previa
			params += "&id_rips=" + $("#hdd_id_rips").val() +
					  "&ind_ac=" + $("#hdd_ac").val() +
					  "&ind_ap=" + $("#hdd_ap").val() +
					  "&ind_am=" + $("#hdd_am").val() +
					  "&ind_at=" + $("#hdd_at").val() +
					  "&ind_us=" + $("#hdd_us").val() +
					  "&ind_af=" + $("#hdd_af").val() +
					  "&ind_ad=" + $("#hdd_ad").val() +
					  "&ind_ct=" + $("#hdd_ct").val() +
					  "&id_prestador=" + $("#hdd_prestador").val() +
					  "&ind_sin_np=" + ($("#hdd_sin_np").is(":checked") ? 1 : 0);
			break;
	}
	
	llamarAjax("generador_rips_ajax.php", params, "d_generar_rips", "terminar_generar_rips();");
}

function validar_generar_rips() {
	var resultado = true;
	
	$("#contenedor_error").css("display", "none");
	$("#txt_num_factura").removeClass("bordeAdmision");
	
	var tipo_factura = parseInt($("#hdd_tipo_factura").val(), 10);
	if (tipo_factura == 1) {
		if (trim($("#txt_num_factura").val()) == "") {
			$("#txt_num_factura").addClass("bordeAdmision");
			resultado = false;
		}
	}
	
	if ($("#hdd_ac").val() == "1") {
		var cant_registros_ac = parseInt($("#hdd_cant_registros_ac").val(), 0);
		for (var i = 0; i < cant_registros_ac; i++) {
			if ($("#hdd_con_datos_ac_" + i).val() == "1") {
				$("#txt_num_factura_ac_" + i).removeClass("bordeAdmision");
				if (trim($("#txt_num_factura_ac_" + i).val()) == "") {
					$("#txt_num_factura_ac_" + i).addClass("bordeAdmision");
					resultado = false;
				}
			}
		}
	}
	
	if ($("#hdd_ap").val() == "1") {
		var cant_registros_ap = parseInt($("#hdd_cant_registros_ap").val(), 0);
		for (var i = 0; i < cant_registros_ap; i++) {
			if ($("#hdd_con_datos_ap_" + i).val() == "1") {
				$("#txt_num_factura_ap_" + i).removeClass("bordeAdmision");
				if (trim($("#txt_num_factura_ap_" + i).val()) == "") {
					$("#txt_num_factura_ap_" + i).addClass("bordeAdmision");
					resultado = false;
				}
			}
		}
	}
	
	if ($("#hdd_am").val() == "1") {
		var cant_registros_am = parseInt($("#hdd_cant_registros_am").val(), 0);
		for (var i = 0; i < cant_registros_am; i++) {
			if ($("#hdd_con_datos_am_" + i).val() == "1") {
				$("#txt_num_factura_am_" + i).removeClass("bordeAdmision");
				if (trim($("#txt_num_factura_am_" + i).val()) == "") {
					$("#txt_num_factura_am_" + i).addClass("bordeAdmision");
					resultado = false;
				}
			}
		}
	}
	
	if ($("#hdd_at").val() == "1") {
		var cant_registros_at = parseInt($("#hdd_cant_registros_at").val(), 0);
		for (var i = 0; i < cant_registros_at; i++) {
			if ($("#hdd_con_datos_at_" + i).val() == "1") {
				$("#txt_num_factura_at_" + i).removeClass("bordeAdmision");
				if (trim($("#txt_num_factura_at_" + i).val()) == "") {
					$("#txt_num_factura_at_" + i).addClass("bordeAdmision");
					resultado = false;
				}
			}
		}
	}
	
	if ($("#hdd_af").val() == "1") {
		var cant_registros_af = parseInt($("#hdd_cant_registros_af").val(), 0);
		for (var i = 0; i < cant_registros_af; i++) {
			if ($("#tr_reg_af_" + i).css("display") != "none") {
				$("#txt_num_factura_af_" + i).removeClass("bordeAdmision");
				if (trim($("#txt_num_factura_af_" + i).val()) == "") {
					$("#txt_num_factura_af_" + i).addClass("bordeAdmision");
					resultado = false;
				}
			}
		}
	}
	
	if ($("#hdd_ad").val() == "1") {
		var cant_registros_ad = parseInt($("#hdd_cant_registros_ad").val(), 0);
		for (var i = 0; i < cant_registros_ad; i++) {
			if ($("#tr_reg_ad_" + i).css("display") != "none") {
				$("#txt_num_factura_ad_" + i).removeClass("bordeAdmision");
				if (trim($("#txt_num_factura_ad_" + i).val()) == "") {
					$("#txt_num_factura_ad_" + i).addClass("bordeAdmision");
					resultado = false;
				}
			}
		}
	}
	
	return resultado;
}

function terminar_generar_rips() {
	$("#btn_generar_rips").removeAttr("disabled");
	$("#d_generando_rips").hide();
	$("#btn_generar_rips").removeAttr("disabled");
	alert("RIPS generados con \xe9xito.");
	cargar_rips_disponibles();
}

function filtrar_ac() {
	var texto = trim($("#txt_buscar_ac").val()).toUpperCase();
	var num_factura = trim($("#txt_buscar_factura_ac").val()).toUpperCase();
	var cod_cups = trim($("#txt_buscar_cups_ac").val()).toUpperCase();
	var valor_concepto = trim($("#txt_buscar_valor_ac").val()).toUpperCase();
	var valor_concepto_num = 0;
	if (valor_concepto != "") {
		valor_concepto_num = parseFloat(valor_concepto);
	}
	var ind_revisado = parseInt($("#cmb_revisado_ac").val(), 10);
	var ind_borrado = parseInt($("#cmb_borrado_ac").val(), 10);
	var cant_registros = parseInt($("#hdd_cant_registros_ac").val(), 0);
	for (var i = 0; i < cant_registros; i++) {
		if ($("#hdd_con_datos_ac_" + i).val() == "1") {
			var ind_hallado_aux = false;
			if (texto != "") {
				var numero_documento_aux = $("#txt_numero_documento_ac_" + i).val().toUpperCase();
				var nombre_completo_aux = $("#td_nombre_completo_ac_" + i).html().toUpperCase();
				if (numero_documento_aux.indexOf(texto) >= 0 || nombre_completo_aux.indexOf(texto) >= 0) {
					ind_hallado_aux = true;
				}
			} else {
				ind_hallado_aux = true;
			}
			
			if (ind_hallado_aux && num_factura != "") {
				var num_factura_aux = $("#txt_num_factura_ac_" + i).val().toUpperCase();
				if (num_factura_aux.indexOf(num_factura) < 0) {
					ind_hallado_aux = false;
				}
			}
			
			if (ind_hallado_aux && cod_cups != "") {
				var cod_cups_aux = $("#txt_cod_procedimiento_ac_" + i).val().toUpperCase();
				if (cod_cups_aux.indexOf(cod_cups) < 0) {
					ind_hallado_aux = false;
				}
			}
			
			if (ind_hallado_aux && valor_concepto != "") {
				var valor_consulta_aux = parseFloat($("#txt_valor_consulta_ac_" + i).val());
				var valor_cuota_aux = parseFloat($("#txt_valor_cuota_ac_" + i).val());
				var valor_neto_aux = parseFloat($("#txt_valor_neto_ac_" + i).val());
				if (valor_consulta_aux != valor_concepto_num && valor_cuota_aux != valor_concepto_num && valor_neto_aux != valor_concepto_num) {
					ind_hallado_aux = false;
				}
			}
			
			if (ind_hallado_aux) {
				switch (ind_revisado) {
					case 1:
						if (!$("#chk_revisado_ac_" + i).is(":checked")) {
							ind_hallado_aux = false;
						}
						break;
					case 0:
						if ($("#chk_revisado_ac_" + i).is(":checked")) {
							ind_hallado_aux = false;
						}
						break;
				}
			}
			
			if (ind_hallado_aux) {
				switch (ind_borrado) {
					case 1:
						if (!$("#chk_borrado_ac_" + i).is(":checked")) {
							ind_hallado_aux = false;
						}
						break;
					case 0:
						if ($("#chk_borrado_ac_" + i).is(":checked")) {
							ind_hallado_aux = false;
						}
						break;
				}
			}
			
			if (ind_hallado_aux) {
				$("#tr_reg_ac_" + i).css("display", "table-row");
			} else {
				$("#tr_reg_ac_" + i).css("display", "none");
			}
		}
	}
	
	contar_registros("ac");
}

function filtrar_ap() {
	var texto = trim($("#txt_buscar_ap").val()).toUpperCase();
	var num_factura = trim($("#txt_buscar_factura_ap").val()).toUpperCase();
	var cod_cups = trim($("#txt_buscar_cups_ap").val()).toUpperCase();
	var valor_concepto = trim($("#txt_buscar_valor_ap").val()).toUpperCase();
	var valor_concepto_num = 0;
	if (valor_concepto != "") {
		valor_concepto_num = parseFloat(valor_concepto);
	}
	var ind_revisado = parseInt($("#cmb_revisado_ap").val(), 10);
	var ind_borrado = parseInt($("#cmb_borrado_ap").val(), 10);
	var cant_registros = parseInt($("#hdd_cant_registros_ap").val(), 0);
	for (var i = 0; i < cant_registros; i++) {
		if ($("#hdd_con_datos_ap_" + i).val() == "1") {
			var ind_hallado_aux = false;
			if (texto != "") {
				var numero_documento_aux = $("#txt_numero_documento_ap_" + i).val().toUpperCase();
				var nombre_completo_aux = $("#td_nombre_completo_ap_" + i).html().toUpperCase();
				if (numero_documento_aux.indexOf(texto) >= 0 || nombre_completo_aux.indexOf(texto) >= 0) {
					ind_hallado_aux = true;
				}
			} else {
				ind_hallado_aux = true;
			}
			
			if (ind_hallado_aux && num_factura != "") {
				var num_factura_aux = $("#txt_num_factura_ap_" + i).val().toUpperCase();
				if (num_factura_aux.indexOf(num_factura) < 0) {
					ind_hallado_aux = false;
				}
			}
			
			if (ind_hallado_aux && cod_cups != "") {
				var cod_cups_aux = $("#txt_cod_procedimiento_ap_" + i).val().toUpperCase();
				if (cod_cups_aux.indexOf(cod_cups) < 0) {
					ind_hallado_aux = false;
				}
			}
			
			if (ind_hallado_aux && valor_concepto != "") {
				var valor_pro_aux = parseFloat($("#txt_valor_pro_ap_" + i).val());
				var valor_copago_aux = parseFloat($("#txt_valor_copago_ap_" + i).val());
				if (valor_pro_aux != valor_concepto_num && valor_copago_aux != valor_concepto_num) {
					ind_hallado_aux = false;
				}
			}
			
			if (ind_hallado_aux) {
				switch (ind_revisado) {
					case 1:
						if (!$("#chk_revisado_ap_" + i).is(":checked")) {
							ind_hallado_aux = false;
						}
						break;
					case 0:
						if ($("#chk_revisado_ap_" + i).is(":checked")) {
							ind_hallado_aux = false;
						}
						break;
				}
			}
			
			if (ind_hallado_aux) {
				switch (ind_borrado) {
					case 1:
						if (!$("#chk_borrado_ap_" + i).is(":checked")) {
							ind_hallado_aux = false;
						}
						break;
					case 0:
						if ($("#chk_borrado_ap_" + i).is(":checked")) {
							ind_hallado_aux = false;
						}
						break;
				}
			}
			
			if (ind_hallado_aux) {
				$("#tr_reg_ap_" + i).css("display", "table-row");
			} else {
				$("#tr_reg_ap_" + i).css("display", "none");
			}
		}
	}
	
	contar_registros("ap");
}

function filtrar_am() {
	var texto = trim($("#txt_buscar_am").val()).toUpperCase();
	var num_factura = trim($("#txt_buscar_factura_am").val()).toUpperCase();
	var cod_medicamento = trim($("#txt_buscar_medicamento_am").val()).toUpperCase();
	var valor_concepto = trim($("#txt_buscar_valor_am").val()).toUpperCase();
	var valor_concepto_num = 0;
	if (valor_concepto != "") {
		valor_concepto_num = parseFloat(valor_concepto);
	}
	var ind_revisado = parseInt($("#cmb_revisado_am").val(), 10);
	var ind_borrado = parseInt($("#cmb_borrado_am").val(), 10);
	var cant_registros = parseInt($("#hdd_cant_registros_am").val(), 0);
	for (var i = 0; i < cant_registros; i++) {
		if ($("#hdd_con_datos_am_" + i).val() == "1") {
			var ind_hallado_aux = false;
			if (texto != "") {
				var numero_documento_aux = $("#txt_numero_documento_am_" + i).val().toUpperCase();
				var nombre_completo_aux = $("#td_nombre_completo_am_" + i).html().toUpperCase();
				if (numero_documento_aux.indexOf(texto) >= 0 || nombre_completo_aux.indexOf(texto) >= 0) {
					ind_hallado_aux = true;
				}
			} else {
				ind_hallado_aux = true;
			}
			
			if (ind_hallado_aux && num_factura != "") {
				var num_factura_aux = $("#txt_num_factura_am_" + i).val().toUpperCase();
				if (num_factura_aux.indexOf(num_factura) < 0) {
					ind_hallado_aux = false;
				}
			}
			
			if (ind_hallado_aux && cod_medicamento != "") {
				var cod_medicamento_aux = $("#txt_cod_medicamento_am_" + i).val().toUpperCase();
				if (cod_medicamento_aux.indexOf(cod_medicamento) < 0) {
					ind_hallado_aux = false;
				}
			}
			
			if (ind_hallado_aux && valor_concepto != "") {
				var valor_medicamento_aux = parseFloat($("#txt_valor_medicamento_am_" + i).val());
				var valor_total_med_aux = parseFloat($("#txt_valor_total_med_am_" + i).val());
				if (valor_medicamento_aux != valor_concepto_num && valor_total_med_aux != valor_concepto_num) {
					ind_hallado_aux = false;
				}
			}
			
			if (ind_hallado_aux) {
				switch (ind_revisado) {
					case 1:
						if (!$("#chk_revisado_am_" + i).is(":checked")) {
							ind_hallado_aux = false;
						}
						break;
					case 0:
						if ($("#chk_revisado_am_" + i).is(":checked")) {
							ind_hallado_aux = false;
						}
						break;
				}
			}
			
			if (ind_hallado_aux) {
				switch (ind_borrado) {
					case 1:
						if (!$("#chk_borrado_am_" + i).is(":checked")) {
							ind_hallado_aux = false;
						}
						break;
					case 0:
						if ($("#chk_borrado_am_" + i).is(":checked")) {
							ind_hallado_aux = false;
						}
						break;
				}
			}
			
			if (ind_hallado_aux) {
				$("#tr_reg_am_" + i).css("display", "table-row");
			} else {
				$("#tr_reg_am_" + i).css("display", "none");
			}
		}
	}
	
	contar_registros("am");
}

function filtrar_at() {
	var texto = trim($("#txt_buscar_at").val()).toUpperCase();
	var num_factura = trim($("#txt_buscar_factura_at").val()).toUpperCase();
	var cod_insumo = trim($("#txt_buscar_servicio_at").val()).toUpperCase();
	var valor_concepto = trim($("#txt_buscar_valor_at").val()).toUpperCase();
	var valor_concepto_num = 0;
	if (valor_concepto != "") {
		valor_concepto_num = parseFloat(valor_concepto);
	}
	var ind_revisado = parseInt($("#cmb_revisado_at").val(), 10);
	var ind_borrado = parseInt($("#cmb_borrado_at").val(), 10);
	var cant_registros = parseInt($("#hdd_cant_registros_at").val(), 0);
	for (var i = 0; i < cant_registros; i++) {
		if ($("#hdd_con_datos_at_" + i).val() == "1") {
			var ind_hallado_aux = false;
			if (texto != "") {
				var numero_documento_aux = $("#txt_numero_documento_at_" + i).val().toUpperCase();
				var nombre_completo_aux = $("#td_nombre_completo_at_" + i).html().toUpperCase();
				if (numero_documento_aux.indexOf(texto) >= 0 || nombre_completo_aux.indexOf(texto) >= 0) {
					ind_hallado_aux = true;
				}
			} else {
				ind_hallado_aux = true;
			}
			
			if (ind_hallado_aux && num_factura != "") {
				var num_factura_aux = $("#txt_num_factura_at_" + i).val().toUpperCase();
				if (num_factura_aux.indexOf(num_factura) < 0) {
					ind_hallado_aux = false;
				}
			}
			
			if (ind_hallado_aux && cod_insumo != "") {
				var cod_insumo_aux = $("#txt_cod_insumo_at_" + i).val().toUpperCase();
				if (cod_insumo_aux.indexOf(cod_insumo) < 0) {
					ind_hallado_aux = false;
				}
			}
			
			if (ind_hallado_aux && valor_concepto != "") {
				var valor_insumo_aux = parseFloat($("#txt_valor_insumo_at_" + i).val());
				var valor_total_serv_aux = parseFloat($("#txt_valor_total_serv_at_" + i).val());
				if (valor_insumo_aux != valor_concepto_num && valor_total_serv_aux != valor_concepto_num) {
					ind_hallado_aux = false;
				}
			}
			
			if (ind_hallado_aux) {
				switch (ind_revisado) {
					case 1:
						if (!$("#chk_revisado_at_" + i).is(":checked")) {
							ind_hallado_aux = false;
						}
						break;
					case 0:
						if ($("#chk_revisado_at_" + i).is(":checked")) {
							ind_hallado_aux = false;
						}
						break;
				}
			}
			
			if (ind_hallado_aux) {
				switch (ind_borrado) {
					case 1:
						if (!$("#chk_borrado_at_" + i).is(":checked")) {
							ind_hallado_aux = false;
						}
						break;
					case 0:
						if ($("#chk_borrado_at_" + i).is(":checked")) {
							ind_hallado_aux = false;
						}
						break;
				}
			}
			
			if (ind_hallado_aux) {
				$("#tr_reg_at_" + i).css("display", "table-row");
			} else {
				$("#tr_reg_at_" + i).css("display", "none");
			}
		}
	}
	
	contar_registros("at");
}

function filtrar_us() {
	var texto = trim($("#txt_buscar_us").val()).toUpperCase();
	var ind_revisado = parseInt($("#cmb_revisado_us").val(), 10);
	var ind_borrado = parseInt($("#cmb_borrado_us").val(), 10);
	var cant_registros = parseInt($("#hdd_cant_registros_us").val(), 0);
	for (var i = 0; i < cant_registros; i++) {
		if ($("#hdd_con_datos_us_" + i).val() == "1") {
			var arr_partes = texto.split(" ");
			var ind_hallado_aux = true;
			for (var j = 0; j < arr_partes.length; j++) {
				var parte_aux = trim(arr_partes[j]);
				if (parte_aux != "") {
					var numero_documento_aux = $("#txt_numero_documento_us_" + i).val().toUpperCase();
					var apellido_1_aux = $("#txt_apellido_1_us_" + i).val().toUpperCase();
					var apellido_2_aux = $("#txt_apellido_2_us_" + i).val().toUpperCase();
					var nombre_1_aux = $("#txt_nombre_1_us_" + i).val().toUpperCase();
					var nombre_2_aux = $("#txt_nombre_2_us_" + i).val().toUpperCase();
					if (numero_documento_aux.indexOf(parte_aux) < 0 && apellido_1_aux.indexOf(parte_aux) < 0 && apellido_2_aux.indexOf(parte_aux) < 0
							&& nombre_1_aux.indexOf(parte_aux) < 0 && nombre_2_aux.indexOf(parte_aux) < 0) {
						ind_hallado_aux = false;
						break;
					}
				}
			}
			
			if (ind_hallado_aux) {
				switch (ind_revisado) {
					case 1:
						if (!$("#chk_revisado_us_" + i).is(":checked")) {
							ind_hallado_aux = false;
						}
						break;
					case 0:
						if ($("#chk_revisado_us_" + i).is(":checked")) {
							ind_hallado_aux = false;
						}
						break;
				}
			}
			
			if (ind_hallado_aux) {
				switch (ind_borrado) {
					case 1:
						if (!$("#chk_borrado_us_" + i).is(":checked")) {
							ind_hallado_aux = false;
						}
						break;
					case 0:
						if ($("#chk_borrado_us_" + i).is(":checked")) {
							ind_hallado_aux = false;
						}
						break;
				}
			}
			
			if (ind_hallado_aux) {
				$("#tr_reg_us_" + i).css("display", "table-row");
			} else {
				$("#tr_reg_us_" + i).css("display", "none");
			}
		}
	}
	
	contar_registros("us");
}

function cargar_rips_ac(ind_rips_existentes) {
	var params = "&opcion=16&id_convenio=" + $("#hdd_id_convenio").val() +
				 "&id_plan=" + $("#hdd_id_plan").val() +
				 "&fecha_inicial=" + $("#hdd_fecha_ini").val() +
				 "&fecha_final=" + $("#hdd_fecha_fin").val() +
				 "&tipo_factura=" + $("#hdd_tipo_factura").val() +
				 "&num_factura=" + str_encode($("#txt_num_factura").val()) +
				 "&ind_rips_existentes=" + ind_rips_existentes +
				 "&ind_sin_np=" + $("#hdd_sin_np").val();
	
	llamarAjax("generador_rips_ajax.php", params, "d_registros_ac", "");
}

function cargar_rips_ap(ind_rips_existentes) {
	var params = "&opcion=17&id_convenio=" + $("#hdd_id_convenio").val() +
				 "&id_plan=" + $("#hdd_id_plan").val() +
				 "&fecha_inicial=" + $("#hdd_fecha_ini").val() +
				 "&fecha_final=" + $("#hdd_fecha_fin").val() +
				 "&tipo_factura=" + $("#hdd_tipo_factura").val() +
				 "&num_factura=" + str_encode($("#txt_num_factura").val()) +
				 "&ind_rips_existentes=" + ind_rips_existentes +
				 "&ind_sin_np=" + $("#hdd_sin_np").val();
	
	llamarAjax("generador_rips_ajax.php", params, "d_registros_ap", "");
}

function cargar_rips_am(ind_rips_existentes) {
	var params = "&opcion=18&id_convenio=" + $("#hdd_id_convenio").val() +
				 "&id_plan=" + $("#hdd_id_plan").val() +
				 "&fecha_inicial=" + $("#hdd_fecha_ini").val() +
				 "&fecha_final=" + $("#hdd_fecha_fin").val() +
				 "&tipo_factura=" + $("#hdd_tipo_factura").val() +
				 "&num_factura=" + str_encode($("#txt_num_factura").val()) +
				 "&ind_rips_existentes=" + ind_rips_existentes +
				 "&ind_sin_np=" + $("#hdd_sin_np").val();
	
	llamarAjax("generador_rips_ajax.php", params, "d_registros_am", "");
}

function cargar_rips_at(ind_rips_existentes) {
	var params = "&opcion=19&id_convenio=" + $("#hdd_id_convenio").val() +
				 "&id_plan=" + $("#hdd_id_plan").val() +
				 "&fecha_inicial=" + $("#hdd_fecha_ini").val() +
				 "&fecha_final=" + $("#hdd_fecha_fin").val() +
				 "&tipo_factura=" + $("#hdd_tipo_factura").val() +
				 "&num_factura=" + str_encode($("#txt_num_factura").val()) +
				 "&ind_rips_existentes=" + ind_rips_existentes +
				 "&ind_sin_np=" + $("#hdd_sin_np").val();
	
	llamarAjax("generador_rips_ajax.php", params, "d_registros_at", "");
}

function cargar_rips_us(ind_rips_existentes, ind_refrescar) {
	var params = "&opcion=20&id_convenio=" + $("#hdd_id_convenio").val() +
				 "&id_plan=" + $("#hdd_id_plan").val() +
				 "&fecha_inicial=" + $("#hdd_fecha_ini").val() +
				 "&fecha_final=" + $("#hdd_fecha_fin").val() +
				 "&ind_rips_existentes=" + ind_rips_existentes +
				 "&ind_sin_np=" + $("#hdd_sin_np").val();
	if (ind_refrescar != 1) {
		params += "&id_rips=" + $("#hdd_id_rips").val();
	} else {
		params += "&id_rips=0";
	}
	
	llamarAjax("generador_rips_ajax.php", params, "d_registros_us", "");
}

function contar_registros(sigla) {
	var cant_registros = parseInt($("#hdd_cant_registros_" + sigla).val(), 10);
	var cont_aux = 0;
	for (var i = 0; i < cant_registros; i++) {
		if ($("#tr_reg_" + sigla + "_" + i).css("display") != "none") {
			cont_aux++;
		}
	}
	
	$("#span_num_reg_" + sigla).text(cont_aux);
	$("#span_cant_" + sigla).text(cont_aux);
}

function desplazar_tabla_horizontal(sigla) {
	$("#d_tbl_encabezado_" + sigla).scrollLeft($("#d_tbl_cuerpo_" + sigla).scrollLeft());
}
