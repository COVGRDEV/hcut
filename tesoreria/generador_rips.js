function seleccionar_convenio(id_convenio) {
	var params = 'opcion=1&id_convenio=' + id_convenio;
	
	llamarAjax("generador_rips_ajax.php", params, "d_planes", "cargar_rips_disponibles();");
}

function seleccionar_plan(id_plan) {
	cargar_rips_disponibles();
}

function generar_rips() {
	if (validar_generar_rips()) {
		var params = "opcion=2&id_convenio=" + $("#cmb_convenio").val() +
					 "&id_plan=" + $("#cmb_plan").val() +
					 "&fecha_inicial=" + $("#txt_fecha_inicial").val() +
					 "&fecha_final=" + $("#txt_fecha_final").val() +
					 "&num_factura=" + str_encode($("#txt_num_factura").val()) +
					 "&ind_ct=" + ($("#chk_ct").is(":checked") ? 1 : 0) +
					 "&ind_af=" + ($("#chk_af").is(":checked") ? 1 : 0) +
					 "&ind_us=" + ($("#chk_us").is(":checked") ? 1 : 0) +
					 "&ind_ad=" + ($("#chk_ad").is(":checked") ? 1 : 0) +
					 "&ind_ac=" + ($("#chk_ac").is(":checked") ? 1 : 0) +
					 "&ind_ap=" + ($("#chk_ap").is(":checked") ? 1 : 0) +
					 "&ind_ah=" + ($("#chk_ah").is(":checked") ? 1 : 0) +
					 "&ind_au=" + ($("#chk_au").is(":checked") ? 1 : 0) +
					 "&ind_an=" + ($("#chk_an").is(":checked") ? 1 : 0) +
					 "&ind_am=" + ($("#chk_am").is(":checked") ? 1 : 0) +
					 "&ind_at=" + ($("#chk_at").is(":checked") ? 1 : 0);
		
		$("#btn_generar_rips").attr("disabled", "disabled");
		$("#d_generando_rips").show();
		setTimeout(function() {llamarAjax("generador_rips_ajax.php", params, "d_generar_rips", "terminar_generar_rips();");}, 1000);
	} else {
		$("#contenedor_error").css("display", "block");
		$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
	}
}

function validar_generar_rips() {
	var resultado = true;
	
	$("#contenedor_error").css("display", "none");
	$("#cmb_convenio").removeClass("bordeAdmision");
	$("#txt_fecha_inicial").removeClass("bordeAdmision");
	$("#txt_fecha_final").removeClass("bordeAdmision");
	$("#txt_num_factura").removeClass("bordeAdmision");
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
	if ($("#txt_num_factura").val() == "") {
		$("#txt_num_factura").addClass("bordeAdmision");
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

function terminar_generar_rips() {
	$("#btn_generar_rips").removeAttr("disabled");
	$("#d_generando_rips").hide();
	cargar_rips_disponibles();
}

function cargar_rips_disponibles() {
	var params = "opcion=3&id_convenio=" + $("#cmb_convenio").val() +
				 "&id_plan=" + $("#cmb_plan").val();
	
	llamarAjax("generador_rips_ajax.php", params, "d_descargar_rips", "");
}