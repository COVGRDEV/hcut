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
    $("#d_centro").css({"top": "50px;"});
	
    $("#fondo_negro").css("display", "block");
    $("#d_centro").slideDown(400).css("display", "block");
	
    var params = "opcion=1";
	
    llamarAjax("procedimiento_qx_ajax.php", params, "d_interno", "");
}

function validar_buscar_procedimientos() {
	$("#frmBuscarProcedimiento").validate({
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
	
	llamarAjax("procedimiento_qx_ajax.php", params, "d_procedimientos_b", "");
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
			$("#contenedor_error").css("display", "none");
			var resultado = validar_cirugia();
			if (resultado == 1) {
				editar_cirugia(tipo, ind_imprimir);
			} else {
				$("#contenedor_error").css("display", "block");
				if (resultado == -2) {
					$("#contenedor_error").html("Error - existen diagn&oacute;sticos duplicados");
				} else {
					$("#contenedor_error").html("Los campos marcados en rojo son obligatorios");
				}
				window.scrollTo(0,0);
			}
			break;
		case 2:
			//Se validan duplicados de diagnósticos
			if (validar_duplicados_diagnosticos_hc() != -2) {
				if (validar_archivo_cx() == 1) {
					editar_cirugia(tipo, ind_imprimir);
				}
			} else {
				$("#contenedor_error").css("display", "block");
				$("#contenedor_error").html("Error - existen diagn&oacute;sticos duplicados");
				window.scroll(0, 0);
			}
			break;
	}
}

function imprimir_cirugia() {
	var params = "id_hc=" + $("#hdd_id_hc").val();
	
	llamarAjax("../historia_clinica/impresion_historia_clinica.php", params, "d_impresion_hc", "continuar_imprimir_cirugia();");
}

function continuar_imprimir_cirugia() {
	var ruta = $("#hdd_ruta_arch_hc_pdf").val();
	window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=cirugia.pdf", "_blank");
}

function limpiar_bordes() {
    $("#txt_fecha_cx").removeClass("borde_error");
    $("#cmb_convenio").removeClass("borde_error");
    $("#cmb_amb_rea").removeClass("borde_error");
    $("#cmb_fin_pro").removeClass("borde_error");
    $("#cmb_usuario_prof").removeClass("borde_error");
    $("#cmb_reoperacion").removeClass("borde_error");
    $("#cmb_reop_ent").removeClass("borde_error");
	$("#fil_arch_cx").removeClass("borde_error");
	
	var cant_procedimientos = parseInt($("#hdd_cant_procedimientos").val(), 10);
	for (var i = 0; i < cant_procedimientos; i++) {
	    $("#cmb_ojo_" + i).removeClass("borde_error");
    	$("#cmb_via_" + i).removeClass("borde_error");
	}
}

function validar_cirugia() {
	var resultado = 1;
	var panel_1 = 0;
	var panel_2 = 0;
	var panel_3 = 0;
	
	$("#panel_proce_1").removeClass("borde_error_panel");
	$("#panel_proce_1 a").css({"color": "#5B5B5B"});
	$("#panel_proce_2").removeClass("borde_error_panel");
	$("#panel_proce_2 a").css({"color": "#5B5B5B"});
	$("#panel_proce_3").removeClass("borde_error_panel");
	$("#panel_proce_3 a").css({"color": "#5B5B5B"});
	
	
    if ($("#txt_fecha_cx").val() == "") {
		$("#txt_fecha_cx").addClass("borde_error");
		resultado = -1;
		panel_1 = 1;
	}
    if ($("#cmb_convenio").val() == "") {
		$("#cmb_convenio").addClass("borde_error");
		resultado = -1;
		panel_1 = 1;
	}
    if ($("#cmb_amb_rea").val() == "") {
		$("#cmb_amb_rea").addClass("borde_error");
		resultado = -1;
		panel_1 = 1;
	}
    if ($("#cmb_fin_pro").val() == "") {
		$("#cmb_fin_pro").addClass("borde_error");
		resultado = -1;
		panel_1 = 1;
	}
    if ($("#cmb_usuario_prof").val() == "") {
		$("#cmb_usuario_prof").addClass("borde_error");
		resultado = -1;
		panel_1 = 1;
	}
    if ($("#cmb_reoperacion").val() == "") {
		$("#cmb_reoperacion").addClass("borde_error");
		resultado = -1;
		panel_1 = 1;
	} else if ($("#cmb_reoperacion").val() == "1") {
		if ($("#cmb_reop_ent").val() == "") {
			$("#cmb_reop_ent").addClass("borde_error");
			resultado = -1;
			panel_1 = 1;
		}
	}
	
	var cant_procedimientos = parseInt($("#hdd_cant_procedimientos").val(), 10);
	for (var i = 0; i < cant_procedimientos; i++) {
	    if ($("#tr_proc_cx_" + i).is(":visible") && $("#hdd_cod_procedimiento_" + i).val() != "") {
			if ($("#cmb_ojo_" + i).val() == "") {
				$("#cmb_ojo_" + i).addClass("borde_error");
				resultado = -1;
				panel_1 = 1;
			}
			if ($("#cmb_via_" + i).val() == "") {
				$("#cmb_via_" + i).addClass("borde_error");
				resultado = -1;
				panel_1 = 1;
			}
		}
	}
	
	//Validación de diagnósticos
	var result_ciex = validar_diagnosticos_hc(0);
	if (result_ciex < 0) {
		resultado = result_ciex;
		panel_3 = 1;
	}
	
	if (resultado == 1) {
		resultado = validar_archivo_cx();
	}
	
	if (panel_1 == 1){
	   $("#panel_proce_1").addClass("borde_error_panel");
	   $("#panel_proce_1 a").css({"color": "#FF002A"});
	}
	if (panel_2 == 1){
	   $("#panel_proce_2").addClass("borde_error_panel");
	   $("#panel_proce_2 a").css({"color": "#FF002A"});
	}
	if (panel_3 == 1){
	   $("#panel_proce_3").addClass("borde_error_panel");
	   $("#panel_proce_3 a").css({"color": "#FF002A"});
	}
	
	return resultado;
}

function validar_archivo_cx() {
	var resultado = 1;
	var ruta_arch_cx = $("#fil_arch_cx").val();
	if (ruta_arch_cx != "") {
		var extension = obtener_extension_archivo(ruta_arch_cx);
		if (extension != "jpg" && extension != "png" && extension != "bmp" && extension != "gif" && extension != "pdf") {
			$("#fil_arch_cx").addClass("borde_error");
			resultado = -1;
		}
	}
	
	if (resultado < 0) {
		alert("Los archivos a cargar deben ser im\xe1genes o archivos pdf");
	}
	
	return resultado;
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta 
 */
function editar_cirugia(tipo, ind_imprimir){
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
				 "&arch_cx=" + $("#fil_arch_cx").val() +
				 "&arch_stickers=" + $("#fil_hoja_stickers").val();
	
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
	
	llamarAjax("procedimiento_qx_ajax.php", params, "d_guardar_cx", "validar_exito(" + tipo + ", " + ind_imprimir + ");");
}

function validar_exito(tipo, ind_imprimir) {
	var resul_guardar = $("#hdd_resul_guardar").val();
	
	if (resul_guardar > 0) {
		$("#contenedor_exito").css("display", "block");
		$("#contenedor_exito").html("Datos guardados correctamente");
		$("#hdd_id_hc").val(resul_guardar);
		$("#hdd_id_hc_cx").val(resul_guardar);
		$("#hdd_id_hc_stickers").val(resul_guardar);
		subir_archivo_cx(true);
		subir_archivo_stickers(true);
		switch (tipo) {
			case 1:
				setTimeout("parent.cerrar_div_centro();", 1000);
				break;
			case 2:
			case 3:
				setTimeout('$("#contenedor_exito").css("display", "none")', 2000);
				break;
		}
		
		if (ind_imprimir == 1) {
			imprimir_cirugia();
		}
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error interno al guardar la cirug&iacute;a");
	}
	window.scrollTo(0,0);
}

function obtener_ruta_valida(id_archivo) {
	var ruta_arch_examen = $("#" + id_archivo).val();
	var extension = obtener_extension_archivo(ruta_arch_examen);
	if (extension != "jpg" && extension != "png" && extension != "bmp" && extension != "gif" && extension != "pdf") {
		ruta_arch_examen = "";
	}
	
	return ruta_arch_examen;
}

function subir_archivo_cx(ind_actualizar) {
	var ruta_arch_examen = obtener_ruta_valida("fil_arch_cx");
	if (ruta_arch_examen != "") {
		$("#frm_arch_cx").submit();
		
		if (ind_actualizar) {
			document.getElementById("d_archivo_cx").innerHTML = "";
			cargar_archivo(3000, 2);
		}
	}
}

function subir_archivo_stickers(ind_actualizar) {
	var ruta_arch_examen = obtener_ruta_valida("fil_hoja_stickers");
	if (ruta_arch_examen != "") {
		$("#frm_arch_stickers").submit();
		
		if (ind_actualizar) {
			document.getElementById("d_archivo_stickers").innerHTML = "";
			cargar_archivo(3000, 1);
		}
	}
}

/*
Tipo de archivo
1: Hoja de stickers
2: Formato de SAP
*/
function cargar_archivo(retardo, tipo_arch) {
	switch (tipo_arch) {
		case 1:
			var params = "opcion=7&id_hc=" + $("#hdd_id_hc").val();
			
			if (retardo > 0) {
				setTimeout(function() {llamarAjax("procedimiento_qx_ajax.php", params, "d_archivo_stickers", "");}, retardo);
			} else {
				llamarAjax("procedimiento_qx_ajax.php", params, "d_archivo_stickers", "");
			}
			break;
			
		case 2:
			var params = "opcion=5&id_hc=" + $("#hdd_id_hc").val();
			
			if (retardo > 0) {
				setTimeout(function() {llamarAjax("procedimiento_qx_ajax.php", params, "d_archivo_cx", "");}, retardo);
			} else {
				llamarAjax("procedimiento_qx_ajax.php", params, "d_archivo_cx", "");
			}
			break;
	}
}
