//Reporte General en Excel
function generar_reporte_excel() {
    if ($("#txt_fecha_ini").val() == "" || $("#txt_fecha_fin").val() == "") {
        alert("Debe seleccionar la fecha inicial y la fecha final");
    } else {
		$("#hdd_fecha_ini").val($("#txt_fecha_ini").val());
		$("#hdd_fecha_fin").val($("#txt_fecha_fin").val());
		$("#hdd_fecha_fin").val($("#txt_fecha_fin").val());
		$("#hdd_tipo_fecha").val($("input[name=rad_tipo_fecha]:checked", "#frm_tipos_fecha").val());
		
		var cant_estados_prog = parseInt($("#hdd_cant_estados_prog").val(), 10);
		for (var i = 0; i < cant_estados_prog; i++) {
			$("#hdd_sel_estado_prog_" + i).val($("#chk_estado_prog_" + i).is(":checked") ? 1 : 0);
		}
		
		document.getElementById("frm_excel_general").submit();
    }
}

function abrir_buscar_concepto() {
	$("#d_interno_conceptos").html("");
	
    var params = "opcion=1";
	
    llamarAjax("reporte_programacion_cx_ajax.php", params, "d_interno_conceptos", "");
	mostrar_formulario_conceptos(1);
}

function limpiar_concepto() {
	$("#hdd_cod_concepto").val("");
	$("#hdd_tipo_concepto").val("");
	$("#txt_concepto").val("");
}

function mostrar_formulario_conceptos(tipo) {
    if (tipo == 1) { //mostrar
        $("#fondo_negro_conceptos").css("display", "block");
        $("#d_centro_conceptos").slideDown(400).css("display", "block");

    } else if (tipo == 0) { //Ocultar
        $("#fondo_negro_conceptos").css("display", "none");
        $("#d_centro_conceptos").slideDown(400).css("display", "none");
    }
}

function buscar_conceptos() {
	if (trim($("#txt_concepto_b").val()) == "") {
		alert("Debe indicar el c\xf3digo o nombre a buscar");
		$("#txt_concepto_b").focus();
		return;
	}
	
    var params = "opcion=2&texto_b=" + str_encode($("#txt_concepto_b").val());
	
    llamarAjax("reporte_programacion_cx_ajax.php", params, "d_buscar_conceptos", "");
}

function seleccionar_concepto(tipo_concepto, cod_concepto, nombre_concepto) {
	$("#hdd_tipo_concepto").val(tipo_concepto);
	$("#hdd_cod_concepto").val(cod_concepto);
	$("#txt_concepto").val(nombre_concepto);
	
	mostrar_formulario_conceptos(0);
}

function seleccional_check_estado(indice) {
	var cant_estados_prog = parseInt($("#hdd_cant_estados_prog").val(), 10);
	
	if (indice == "t") {
		for (var i = 0; i < cant_estados_prog; i++) {
			$("#chk_estado_prog_" + i).prop("checked", $("#chk_estado_prog_todos").is(":checked"));
		}
	} else {
		if ($("#chk_estado_prog_" + indice).is(":checked")) {
			var check_todos = true;
			for (var i = 0; i < cant_estados_prog; i++) {
				if (!$("#chk_estado_prog_" + i).is(":checked")) {
					check_todos = false;
					break;
				}
			}
			$("#chk_estado_prog_todos").prop("checked", check_todos);
		} else {
			$("#chk_estado_prog_todos").prop("checked", false);
		}
	}
}
