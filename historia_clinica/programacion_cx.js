function buscar_paciente_prog() {
	$("#contenedor_error").css("display", "none");
    $("#txt_paciente_hc").removeClass("borde_error");
	if ($("#txt_paciente_hc").val() != "") {
		buscar_paciente_prog2($("#txt_paciente_hc").val());
	} else {
        $("#txt_paciente_hc").addClass("borde_error");
		$("#contenedor_error").html("Los campos marcados en rojo son obligatorios.");
		$("#contenedor_error").css("display", "block");
	}
}

function buscar_paciente_prog2(parametro) {
	var params = "opcion=1&paciente_hc=" + parametro;
	
	llamarAjax("programacion_cx_ajax.php", params, "d_contenedor_programacion_cx", "");
}

function buscar_paciente_prog_id(id_paciente, id_prog_cx) {
	var params = "opcion=1&id_paciente=" + id_paciente;
	
	var funcion_aux = "";
	if (id_prog_cx != "") {
		funcion_aux = "mostrar_editar_prog_cx(" + id_prog_cx + ");";
	}
	llamarAjax("programacion_cx_ajax.php", params, "d_contenedor_programacion_cx", funcion_aux);
}

function mostrar_editar_prog_cx(id_prog_cx) {
	var params = "opcion=9&id_prog_cx=" + id_prog_cx;
	
	$("#d_lista_prog_cx_paciente").css("display", "none");
	$("#d_programacion_cx_paciente").html("");
	$("#d_programacion_cx_paciente").css("display", "block");
	llamarAjax("programacion_cx_ajax.php", params, "d_programacion_cx_paciente", "");
}

function mostrar_crear_prog_cx() {
    var params = "opcion=3";
	
    llamarAjax("programacion_cx_ajax.php", params, "d_contenedor_programacion_cx", "");
}

function ver_registro_prog_cx_paciente(id_paciente) {
    var params = "opcion=2&id_paciente=" + id_paciente;
	
    llamarAjax("programacion_cx_ajax.php", params, "d_contenedor_programacion_cx", "");
}

function verificar_paciente(numero_documento) {
	if (numero_documento != "") {
		var params = "opcion=4&numero_documento=" + numero_documento;
		
		llamarAjax("programacion_cx_ajax.php", params, "d_val_paciente", "continuar_verificar_paciente();");
	}
}

function continuar_verificar_paciente() {
	var id_paciente = $("#hdd_id_paciente_b").val();
	
	if (id_paciente != "" && id_paciente != "0") {
        $("#fondo_negro").css("display", "block");
        $("#d_centro").slideDown(400).css("display", "block");
		
		var codigohtml =
			'<div class="encabezado">' +
				'<h3>Existe un paciente con la informaci&oacute;n</h3>' +
			'</div>' +
			'<table style="width:100%;">' +
				'<tr>' +
					'<td colspan="2" style="text-align:center; color: #33338C;"></td>' +
				'</tr>' +
				'<tr>' +
					'<td style="text-align:right;">Tipo de documento:</td>' +
					'<td style="text-align:left;font-weight: 700;">' + $("#hdd_tipo_documento_b").val() + '</td>' +
				'</tr>' +
				'<tr>' +
					'<td style="text-align:right;">N&uacute;mero de documento:</td>' +
					'<td style="text-align:left;font-weight: 700;">' + $("#hdd_numero_documento_b").val() + '</td>' +
				'</tr>' +
				'<tr>' +
					'<td style="text-align:right;">Nombre(s):</td>' +
					'<td style="text-align:left;font-weight: 700;">' + $("#hdd_nombre_1_b").val() + " " + $("#hdd_nombre_2_b").val() + '</td>' +
				'</tr>' +
				'<tr>' +
					'<td style="text-align:right;">Apellido(s):</td>' +
					'<td style="text-align:left;font-weight: 700;">' + $("#hdd_apellido_1_b").val() + " " + $("#hdd_apellido_2_b").val() + '</td>' +
				'</tr>' +
				'<tr>' +
					'<td colspan="2">' +
						'<p style="color: #FF552A;font-size: 10pt;font-weight: 700;">' +
							'\u00BFDesea seleccionar el registro e importar los datos?' +
						'</p>' +
					'</td>' +
				'</tr>' +
				'<tr>' +
					'<td colspan="2" style="text-align:center;">' +
						'<br/><br/>' +
						'<input type="button" class="btnPrincipal peq" value="Aceptar" onclick="finalizar_verificar_paciente(1);" />&nbsp;&nbsp;' +
						'<input type="button" class="btnSecundario peq" value="Cancelar" onclick="finalizar_verificar_paciente(0);" />' +
					'</td>' +
				'</tr>' +
			'</table>';
        $(".div_interno").html(codigohtml);
		
        posicionarDivFlotante("d_centro");
    } else {
		finalizar_verificar_paciente(0);
    }
}

function finalizar_verificar_paciente(ind_resultado) {
	if (ind_resultado == 1) {
		//Se cargan los datos del paciente
		$("#hdd_id_paciente").val($("#hdd_id_paciente_b").val());
        $("#cmb_tipo_documento").val($("#hdd_id_tipo_documento_b").val());
        $("#txt_nombre_1").val($("#hdd_nombre_1_b").val());
        $("#txt_nombre_2").val($("#hdd_nombre_2_b").val());
        $("#txt_apellido_1").val($("#hdd_apellido_1_b").val());
        $("#txt_apellido_2").val($("#hdd_apellido_2_b").val());
        $("#cmb_sexo").val($("#hdd_sexo_b").val());
        $("#txt_fecha_nacimiento").val($("#hdd_fecha_nacimiento_b").val());
        $("#cmb_tipo_sangre").val($("#hdd_tipo_sangre_b").val());
        $("#cmb_factor_rh").val($("#hdd_factor_rh_b").val());
        $("#cmb_pais_nac").val($("#hdd_id_pais_nac_b").val());
        $("#cmb_cod_dep_nac").val($("#hdd_cod_dep_nac_b").val());
        $("#cmb_cod_mun_nac").val($("#hdd_cod_mun_nac_b").val());
        $("#txt_nom_dep_nac").val($("#hdd_nom_dep_nac_b").val());
        $("#txt_nom_mun_nac").val($("#hdd_nom_mun_nac_b").val());
        $("#cmb_pais_res").val($("#hdd_id_pais_b").val());
        $("#cmb_cod_dep_res").val($("#hdd_cod_dep_b").val());
        $("#cmb_cod_mun_res").val($("#hdd_cod_mun_b").val());
        $("#txt_nom_dep_res").val($("#hdd_nom_dep_b").val());
        $("#txt_nom_mun_res").val($("#hdd_nom_mun_b").val());
        $("#cmb_zona").val($("#hdd_id_zona_b").val());
        $("#txt_direccion").val($("#hdd_direccion_b").val());
        $("#txt_email").val($("#hdd_email_b").val());
        $("#txt_telefono_1").val($("#hdd_telefono_1_b").val());
        $("#txt_telefono_2").val($("#hdd_telefono_2_b").val());
        $("#txt_profesion").val($("#hdd_profesion_b").val());
        $("#cmb_estado_civil").val($("#hdd_id_estado_civil_b").val());
        $("#cmb_desplazado").val($("#hdd_ind_desplazado_b").val());
        $("#cmb_etnia").val($("#hdd_id_etnia_b").val());
        $("#cmb_convenio").val($("#hdd_id_convenio_b").val());
	}
	$("#d_centro").css("display", "none");
	$("#fondo_negro").css("display", "none");
}

function limpiar_id_paciente() {
	$("#hdd_id_paciente").val("");
}

function seleccionar_pais(id_pais, sufijo) {
	if (id_pais == "1") {
		$("#td_dep_col_" + sufijo).css("display", "table-cell");
		$("#td_mun_col_" + sufijo).css("display", "table-cell");
		$("#td_dep_col_val_" + sufijo).css("display", "table-cell");
		$("#td_mun_col_val_" + sufijo).css("display", "table-cell");
		$("#td_dep_otro_" + sufijo).css("display", "none");
		$("#td_mun_otro_" + sufijo).css("display", "none");
		$("#td_dep_otro_val_" + sufijo).css("display", "none");
		$("#td_mun_otro_val_" + sufijo).css("display", "none");
	} else {
		$("#td_dep_col_" + sufijo).css("display", "none");
		$("#td_mun_col_" + sufijo).css("display", "none");
		$("#td_dep_col_val_" + sufijo).css("display", "none");
		$("#td_mun_col_val_" + sufijo).css("display", "none");
		$("#td_dep_otro_" + sufijo).css("display", "table-cell");
		$("#td_mun_otro_" + sufijo).css("display", "table-cell");
		$("#td_dep_otro_val_" + sufijo).css("display", "table-cell");
		$("#td_mun_otro_val_" + sufijo).css("display", "table-cell");
	}
}

function seleccionar_departamento(cod_dep, sufijo) {
	var params = "opcion=5&cod_dep=" + cod_dep +
				 "&sufijo=" + sufijo;
	
	llamarAjax("programacion_cx_ajax.php", params, "d_municipio_" + sufijo, "");
}

function agregar_elemento() {
    var params = "opcion=6&flotante=" + 1;
    llamarAjax("programacion_cx_ajax.php", params, "d_interno", "$(\"#txt_elemento_b\").focus();");
	
	$("#fondo_negro").css("display", "block");
	$("#d_centro").slideDown(400).css("display", "block");
}

function buscar_elemento() {
	$("#txt_elemento_b").removeClass("borde_error");
	if ($("#txt_elemento_b").val() != "") {
		var params = "opcion=7&parametro=" + str_encode($("#txt_elemento_b").val());
		
		llamarAjax("programacion_cx_ajax.php", params, "d_elementos_b", "");
	} else {
		$("#txt_elemento_b").addClass("borde_error");
		$("#contenedor_error").html("Los campos marcados en rojo son obligatorios.");
		$("#contenedor_error").css("display", "block");
	}
}

function seleccionar_producto_b(tipo, codigo, nombre, id_tipo_insumo) {
	var indice = parseInt($("#hdd_cant_productos").val(), 10);
	
	$("#tr_producto_" + indice).css("display", "table-row");
	$("#hdd_tipo_producto_" + indice).val(tipo);
	$("#hdd_cod_producto_" + indice).val(codigo);
	$("#sp_producto_" + indice).html(codigo + " - " + nombre);
	$("#cmb_tipo_bilateral_" + indice).val(-1);
	$("#txt_cantidad_producto_" + indice).val(1);
	
	borrar_filas_tabla_det_val(indice);
	if (id_tipo_insumo == "3") {
		$("#tr_producto_val_" + indice).css("display", "table-row");
		agregar_filas_tabla_det_val(indice, 1);
	}
	
	$("#hdd_cant_productos").val(indice + 1);
	cerrar_div_centro();
}

function borrar_elemento(indice) {
	$("#tr_producto_" + indice).css("display", "none");
	$("#tr_producto_val_" + indice).css("display", "none");
	$("#hdd_tipo_producto_" + indice).val("");
	$("#hdd_cod_producto_" + indice).val("");
	$("#sp_producto_" + indice).html("");
	borrar_filas_tabla_det_val(indice);
	
	var cant_productos = parseInt($("#hdd_cant_productos").val(), 10);
	if (indice + 1 == cant_productos) {
		$("#hdd_cant_productos").val(cant_productos - 1);
	}
}

function borrar_filas_tabla_det_val(indice) {
	var cont = 0;
	while ($("#tr_det_val_" + indice + "_" + cont).length) {
		$("#tr_det_val_" + indice + "_" + cont).remove();
		cont++;
	}
}

function agregar_filas_tabla_det_val(indice, cantidad) {
	if (!$("#tr_producto_val_" + indice).is(":visible")) {
		return;
	}
	
	cantidad = parseInt(cantidad, 10);
	for (var i = 0; i < cantidad; i++) {
		if ($("#tr_det_val_" + indice + "_" + i).length == false) {
			$("#tbl_productos_val_" + indice).append(
				'<tr id="tr_det_val_' + indice + '_' + i + '">' +
					'<td align="center">' + (i + 1) + '</td>' +
					'<td align="right">&nbsp;Tipo:&nbsp;</td>' +
					'<td align="center">' +
						'<input type="text" id="txt_tipo_lente_' + indice + '_' + i + '" name="txt_tipo_lente_' + indice + '_' + i + '" value="" maxlength="50" class="no-margin" style="width:100%;" onblur="trim(this.value);" />' +
					'</td>' +
					'<td align="right">&nbsp;Serial:&nbsp;</td>' +
					'<td align="center">' +
						'<input type="text" id="txt_serial_lente_' + indice + '_' + i + '" name="txt_serial_lente_' + indice + '_' + i + '" value="" maxlength="50" class="no-margin" style="width:100%;" onblur="trim(this.value);" />' +
					'</td>' +
					'<td align="right">&nbsp;Poder:&nbsp;</td>' +
					'<td align="center">' +
						'<input type="text" id="txt_poder_lente_' + indice + '_' + i + '" name="txt_poder_lente_' + indice + '_' + i + '" value="" maxlength="10" class="no-margin" style="width:70px;" onblur="trim(this.value);" />' +
					'</td>' +
				'</tr>'
			);
			$(function() {
				$("#txt_poder_lente_" + indice + "_" + i).autocomplete({ source: Tags_poder_lente });
			});
		}
	}
	var cont = cantidad;
	while ($("#tr_det_val_" + indice + "_" + cont).length) {
		$("#tr_det_val_" + indice + "_" + cont).remove();
		cont++;
	}
}

function crear_prog_cx() {
	$("#btn_crear_prog_cx").prop("disabled", true);
	var resul_val = validar_crear_prog_cx();
	if (resul_val == 1) {
		var params = "opcion=8&id_paciente=" + $("#hdd_id_paciente").val() +
					 "&id_tipo_documento=" + $("#cmb_tipo_documento").val() +
					 "&numero_documento=" + str_encode($("#txt_numero_documento").val()) +
					 "&nombre_1=" + str_encode($("#txt_nombre_1").val()) +
					 "&nombre_2=" + str_encode($("#txt_nombre_2").val()) +
					 "&apellido_1=" + str_encode($("#txt_apellido_1").val()) +
					 "&apellido_2=" + str_encode($("#txt_apellido_2").val()) +
					 "&sexo=" + $("#cmb_sexo").val() +
					 "&fecha_nacimiento=" + $("#txt_fecha_nacimiento").val() +
					 "&tipo_sangre=" + $("#cmb_tipo_sangre").val() +
					 "&factor_rh=" + $("#cmb_factor_rh").val() +
					 "&id_pais_nac=" + $("#cmb_pais_nac").val() +
					 "&cod_dep_nac=" + $("#cmb_cod_dep_nac").val() +
					 "&cod_mun_nac=" + $("#cmb_cod_mun_nac").val() +
					 "&nom_dep_nac=" + str_encode($("#txt_nom_dep_nac").val()) +
					 "&nom_mun_nac=" + str_encode($("#txt_nom_mun_nac").val()) +
					 "&id_pais_res=" + $("#cmb_pais_res").val() +
					 "&cod_dep_res=" + $("#cmb_cod_dep_res").val() +
					 "&cod_mun_res=" + $("#cmb_cod_mun_res").val() +
					 "&nom_dep_res=" + str_encode($("#txt_nom_dep_res").val()) +
					 "&nom_mun_res=" + str_encode($("#txt_nom_mun_res").val()) +
					 "&id_zona=" + $("#cmb_zona").val() +
					 "&direccion=" + str_encode($("#txt_direccion").val()) +
					 "&email=" + str_encode($("#txt_email").val()) +
					 "&telefono_1=" + str_encode($("#txt_telefono_1").val()) +
					 "&telefono_2=" + str_encode($("#txt_telefono_2").val()) +
					 "&profesion=" + str_encode($("#txt_profesion").val()) +
					 "&id_estado_civil=" + $("#cmb_estado_civil").val() +
					 "&ind_desplazado=" + $("#cmb_desplazado").val() +
					 "&id_etnia=" + $("#cmb_etnia").val() +
					 "&id_usuario_prof=" + $("#cmb_usuario_prof").val() +
					 "&fecha_prog=" + $("#txt_fecha_prog").val() +
					 "&hora_prog=" + $("#txt_hora_prog").val() +
					 "&id_convenio=" + $("#cmb_convenio").val();
		
		var cant_productos = parseInt($("#hdd_cant_productos").val(), 10);
		var cont_aux = 0;
		for (var i = 0; i < cant_productos; i++) {
			if ($("#tr_producto_" + i).is(":visible")) {
				params += "&tipo_elemento_" + cont_aux + "=" + $("#hdd_tipo_producto_" + i).val() +
						  "&cod_elemento_" + cont_aux + "=" + $("#hdd_cod_producto_" + i).val() +
						  "&tipo_bilateral_" + cont_aux + "=" + $("#cmb_tipo_bilateral_" + i).val() +
						  "&cantidad_" + cont_aux + "=" + $("#txt_cantidad_producto_" + i).val();
				
				var cont2_aux = 0;
				while ($("#tr_det_val_" + i + "_" + cont2_aux).length) {
					params += "&tipo_lente_" + cont_aux + "_" + cont2_aux + "=" + trim($("#txt_tipo_lente_" + i + "_" + cont2_aux).val()) +
							  "&serial_lente_" + cont_aux + "_" + cont2_aux + "=" + trim($("#txt_serial_lente_" + i + "_" + cont2_aux).val()) +
							  "&poder_lente_" + cont_aux + "_" + cont2_aux + "=" + trim($("#txt_poder_lente_" + i + "_" + cont2_aux).val());
					
					cont2_aux++;
				}
				params += "&cant_det_val_" + cont_aux + "=" + cont2_aux;
				
				cont_aux++;
			}
		}
		params += "&cant_det=" + cont_aux;
		
		llamarAjax("programacion_cx_ajax.php", params, "d_crear_prog_cx", "finalizar_crear_prog_cx();");
	} else if (resul_val == -1) {
		$("#contenedor_error").html("Debe seleccionar al menos un producto o servicio.");
		$("#contenedor_error").css("display", "block");
		$("#btn_crear_prog_cx").prop("disabled", false);
		window.scrollTo(0, 0);
	} else {
		$("#contenedor_error").html("Los campos marcados en rojo son obligatorios.");
		$("#contenedor_error").css("display", "block");
		$("#btn_crear_prog_cx").prop("disabled", false);
		window.scrollTo(0, 0);
	}
}

function finalizar_crear_prog_cx() {
	var result_paciente_prog_cx = parseInt($("#hdd_result_paciente_prog_cx").val(), 10);
	var result_crear_prog_cx = parseInt($("#hdd_result_crear_prog_cx").val(), 10);
	$("#btn_crear_prog_cx").prop("disabled", false);
	$("#contenedor_error").css("display", "none");
	
	if (result_paciente_prog_cx > 0 && result_crear_prog_cx > 0) {
		buscar_paciente_prog2($("#txt_numero_documento").val());
		$("#contenedor_exito").html("Programaci&oacute;n de cirug&iacute;a registrada con &eacute;xito.");
		$("#contenedor_exito").css("display", "block");
		setTimeout(function() { $("#contenedor_exito").css("display", "none"); }, 3000);
	} else if (result_paciente_prog_cx <= 0) {
		$("#contenedor_error").html("Error al registrar los datos del paciente.");
		$("#contenedor_error").css("display", "block");
	} else {
		$("#contenedor_error").html("Error al crear el registro de programaci&oacute;n de cirug&iacute;a con c&oacute;digo (" + result_crear_prog_cx + ").");
		$("#contenedor_error").css("display", "block");
	}
	window.scrollTo(0, 0);
}

function validar_crear_prog_cx() {
	$("#contenedor_error").css("display", "none");
	var resultado = 1;
	
	$("#cmb_tipo_documento").removeClass("borde_error");
	$("#txt_numero_documento").removeClass("borde_error");
	$("#txt_nombre_1").removeClass("borde_error");
	$("#txt_apellido_1").removeClass("borde_error");
	$("#cmb_sexo").removeClass("borde_error");
	$("#txt_fecha_nacimiento").removeClass("borde_error");
	$("#cmb_tipo_sangre").removeClass("borde_error");
	$("#cmb_factor_rh").removeClass("borde_error");
	$("#cmb_pais_nac").removeClass("borde_error");
	$("#cmb_cod_dep_nac").removeClass("borde_error");
	$("#cmb_cod_mun_nac").removeClass("borde_error");
	$("#txt_nom_dep_nac").removeClass("borde_error");
	$("#txt_nom_mun_nac").removeClass("borde_error");
	$("#cmb_pais_res").removeClass("borde_error");
	$("#cmb_cod_dep_res").removeClass("borde_error");
	$("#cmb_cod_mun_res").removeClass("borde_error");
	$("#txt_nom_dep_res").removeClass("borde_error");
	$("#txt_nom_mun_res").removeClass("borde_error");
	$("#cmb_zona").removeClass("borde_error");
	$("#txt_direccion").removeClass("borde_error");
	$("#txt_email").removeClass("borde_error");
	$("#txt_telefono_1").removeClass("borde_error");
	$("#txt_profesion").removeClass("borde_error");
	$("#cmb_estado_civil").removeClass("borde_error");
	$("#cmb_desplazado").removeClass("borde_error");
	$("#cmb_etnia").removeClass("borde_error");
	
	$("#cmb_usuario_prof").removeClass("borde_error");
	$("#txt_fecha_prog").removeClass("borde_error");
	$("#cmb_convenio").removeClass("borde_error");
	
	var cant_aux = parseInt($("#hdd_cant_productos").val(), 10);
	for (var i = 0; i < cant_aux; i++) {
		$("#cmb_tipo_bilateral_" + i).removeClass("borde_error");
		$("#txt_cantidad_producto_" + i).removeClass("borde_error");
	}
	
	if (trim($("#cmb_tipo_documento").val()) == "") {
		$("#cmb_tipo_documento").addClass("borde_error");
		resultado = 0;
	}
	if (trim($("#txt_numero_documento").val()) == "") {
		$("#txt_numero_documento").addClass("borde_error");
		resultado = 0;
	}
	if (trim($("#txt_nombre_1").val()) == "") {
		$("#txt_nombre_1").addClass("borde_error");
		resultado = 0;
	}
	if (trim($("#txt_apellido_1").val()) == "") {
		$("#txt_apellido_1").addClass("borde_error");
		resultado = 0;
	}
	if (trim($("#cmb_sexo").val()) == "") {
		$("#cmb_sexo").addClass("borde_error");
		resultado = 0;
	}
	if (trim($("#txt_fecha_nacimiento").val()) == "") {
		$("#txt_fecha_nacimiento").addClass("borde_error");
		resultado = 0;
	}
	if (trim($("#cmb_tipo_sangre").val()) == "") {
		$("#cmb_tipo_sangre").addClass("borde_error");
		resultado = 0;
	}
	if (trim($("#cmb_factor_rh").val()) == "") {
		$("#cmb_factor_rh").addClass("borde_error");
		resultado = 0;
	}
	if (trim($("#cmb_pais_nac").val()) == "") {
		$("#cmb_pais_nac").addClass("borde_error");
		resultado = 0;
	} else if (trim($("#cmb_pais_nac").val()) == "1") {
		if (trim($("#cmb_cod_dep_nac").val()) == "") {
			$("#cmb_cod_dep_nac").addClass("borde_error");
			resultado = 0;
		}
		if (trim($("#cmb_cod_mun_nac").val()) == "") {
			$("#cmb_cod_mun_nac").addClass("borde_error");
			resultado = 0;
		}
	} else {
		if (trim($("#txt_nom_dep_nac").val()) == "") {
			$("#txt_nom_dep_nac").addClass("borde_error");
			resultado = 0;
		}
		if (trim($("#txt_nom_mun_nac").val()) == "") {
			$("#txt_nom_mun_nac").addClass("borde_error");
			resultado = 0;
		}
	}
	if (trim($("#cmb_pais_res").val()) == "") {
		$("#cmb_pais_res").addClass("borde_error");
		resultado = 0;
	} else if (trim($("#cmb_pais_res").val()) == "1") {
		if (trim($("#cmb_cod_dep_res").val()) == "") {
			$("#cmb_cod_dep_res").addClass("borde_error");
			resultado = 0;
		}
		if (trim($("#cmb_cod_mun_res").val()) == "") {
			$("#cmb_cod_mun_res").addClass("borde_error");
			resultado = 0;
		}
	} else {
		if (trim($("#txt_nom_dep_res").val()) == "") {
			$("#txt_nom_dep_res").addClass("borde_error");
			resultado = 0;
		}
		if (trim($("#txt_nom_mun_res").val()) == "") {
			$("#txt_nom_mun_res").addClass("borde_error");
			resultado = 0;
		}
	}
	if (trim($("#cmb_zona").val()) == "") {
		$("#cmb_zona").addClass("borde_error");
		resultado = 0;
	}
	if (trim($("#txt_direccion").val()) == "") {
		$("#txt_direccion").addClass("borde_error");
		resultado = 0;
	}
	if (trim($("#txt_email").val()) == "" || !validar_email($("#txt_email").val())) {
		$("#txt_email").addClass("borde_error");
		resultado = 0;
	}
	if (trim($("#txt_telefono_1").val()) == "") {
		$("#txt_telefono_1").addClass("borde_error");
		resultado = 0;
	}
	if (trim($("#txt_profesion").val()) == "") {
		$("#txt_profesion").addClass("borde_error");
		resultado = 0;
	}
	if (trim($("#cmb_estado_civil").val()) == "") {
		$("#cmb_estado_civil").addClass("borde_error");
		resultado = 0;
	}
	if (trim($("#cmb_desplazado").val()) == "") {
		$("#cmb_desplazado").addClass("borde_error");
		resultado = 0;
	}
	if (trim($("#cmb_etnia").val()) == "") {
		$("#cmb_etnia").addClass("borde_error");
		resultado = 0;
	}
	
	if (trim($("#cmb_usuario_prof").val()) == "") {
		$("#cmb_usuario_prof").addClass("borde_error");
		resultado = 0;
	}
	if (trim($("#txt_fecha_prog").val()) == "") {
		$("#txt_fecha_prog").addClass("borde_error");
		resultado = 0;
	}
	if (trim($("#cmb_convenio").val()) == "") {
		$("#cmb_convenio").addClass("borde_error");
		resultado = 0;
	}
	
	if (resultado == 1) {
		var cant_productos = 0;
		for (var i = 0; i < cant_aux; i++) {
			if ($("#tr_producto_" + i).is(":visible")) {
				cant_productos++;
				if (trim($("#cmb_tipo_bilateral_" + i).val()) == "") {
					$("#cmb_tipo_bilateral_" + i).addClass("borde_error");
					resultado = 0;
				}
				if (trim($("#txt_cantidad_producto_" + i).val()) == "") {
					$("#txt_cantidad_producto_" + i).addClass("borde_error");
					resultado = 0;
				}
			}
		}
		
		if (cant_productos == 0) {
			resultado = -1;
		}
	}
	
	return resultado;
}

function ocultar_prog_cx() {
	$("#d_lista_prog_cx_paciente").css("display", "block");
	$("#d_programacion_cx_paciente").css("display", "none");
	$("#d_programacion_cx_paciente").html("");
}

function seleccionar_estado_prog(id_estado_prog) {
	if (id_estado_prog != "435") {
		$("#tr_motivo_cancela").css("display", "none");
		$("#cmb_motivo_cancela").val("");
	} else {
		$("#tr_motivo_cancela").css("display", "table-row");
	}
}

function editar_prog_cx() {
	$("#btn_editar_prog_cx").prop("disabled", true);
	var resul_val = validar_editar_prog_cx();
	if (resul_val == 1) {
		var params = "opcion=10&id_prog_cx=" + $("#hdd_id_prog_cx").val() +
					 "&id_paciente=" + $("#hdd_id_paciente").val() +
					 "&id_usuario_prof=" + $("#cmb_usuario_prof").val() +
					 "&fecha_prog=" + $("#txt_fecha_prog").val() +
					 "&hora_prog=" + $("#txt_hora_prog").val() +
					 "&id_convenio=" + $("#cmb_convenio").val() +
					 "&id_estado_prog=" + $("#cmb_estado_prog").val() +
					 "&id_motivo_cancela=" + $("#cmb_motivo_cancela").val();
		
		var cant_productos = parseInt($("#hdd_cant_productos").val(), 10);
		var cont_aux = 0;
		for (var i = 0; i < cant_productos; i++) {
			if ($("#tr_producto_" + i).is(":visible")) {
				params += "&tipo_elemento_" + cont_aux + "=" + $("#hdd_tipo_producto_" + i).val() +
						  "&cod_elemento_" + cont_aux + "=" + $("#hdd_cod_producto_" + i).val() +
						  "&tipo_bilateral_" + cont_aux + "=" + $("#cmb_tipo_bilateral_" + i).val() +
						  "&cantidad_" + cont_aux + "=" + $("#txt_cantidad_producto_" + i).val();
				
				var cont2_aux = 0;
				while ($("#tr_det_val_" + i + "_" + cont2_aux).length) {
					params += "&tipo_lente_" + cont_aux + "_" + cont2_aux + "=" + trim($("#txt_tipo_lente_" + i + "_" + cont2_aux).val()) +
							  "&serial_lente_" + cont_aux + "_" + cont2_aux + "=" + trim($("#txt_serial_lente_" + i + "_" + cont2_aux).val()) +
							  "&poder_lente_" + cont_aux + "_" + cont2_aux + "=" + trim($("#txt_poder_lente_" + i + "_" + cont2_aux).val());
					
					cont2_aux++;
				}
				params += "&cant_det_val_" + cont_aux + "=" + cont2_aux;
				
				cont_aux++;
			}
		}
		params += "&cant_det=" + cont_aux;
		
		llamarAjax("programacion_cx_ajax.php", params, "d_editar_prog_cx", "finalizar_editar_prog_cx();");
	} else if (resul_val == -1) {
		$("#contenedor_error").html("Debe seleccionar al menos un producto o servicio.");
		$("#contenedor_error").css("display", "block");
		$("#btn_editar_prog_cx").prop("disabled", false);
		window.scrollTo(0, 0);
	} else {
		$("#contenedor_error").html("Los campos marcados en rojo son obligatorios.");
		$("#contenedor_error").css("display", "block");
		$("#btn_editar_prog_cx").prop("disabled", false);
		window.scrollTo(0, 0);
	}
}

function finalizar_editar_prog_cx() {
	var result_editar_prog_cx = parseInt($("#hdd_result_editar_prog_cx").val(), 10);
	$("#btn_editar_prog_cx").prop("disabled", false);
	$("#contenedor_error").css("display", "none");
	
	if (result_editar_prog_cx > 0) {
		$("#contenedor_exito").html("Programaci&oacute;n de cirug&iacute;a guardada con &eacute;xito.");
		$("#contenedor_exito").css("display", "block");
		setTimeout(function() { $("#contenedor_exito").css("display", "none"); }, 3000);
		
		var id_estado_prog = $("#hdd_result_id_estado_prog").val();
		if (id_estado_prog != "") {
			$("#hdd_id_estado_prog").val(id_estado_prog);
			$("#sp_estado_prog").html($("#hdd_result_nombre_estado_prog").val());
			
			$("#cmb_estado_prog").val("");
			switch (id_estado_prog) {
				case "433": //Programada
					$("#cmb_estado_prog option[value='433']").remove();
					break;
				case "434": //Realizada
				case "435": //Cancelada
					$("#cmb_estado_prog").attr("disabled", "disabled");
					break;
			}
		}
	} else {
		$("#contenedor_error").html("Error al editar el registro de programaci&oacute;n de cirug&iacute;a con c&oacute;digo (" + result_editar_prog_cx + ").");
		$("#contenedor_error").css("display", "block");
	}
	window.scrollTo(0, 0);
}

function validar_editar_prog_cx() {
	$("#contenedor_error").css("display", "none");
	var resultado = 1;
	
	$("#cmb_usuario_prof").removeClass("borde_error");
	$("#txt_fecha_prog").removeClass("borde_error");
	$("#txt_hora_prog").removeClass("borde_error");
	$("#cmb_convenio").removeClass("borde_error");
	$("#cmb_motivo_cancela").removeClass("borde_error");
	
	var cant_aux = parseInt($("#hdd_cant_productos").val(), 10);
	for (var i = 0; i < cant_aux; i++) {
		$("#cmb_tipo_bilateral_" + i).removeClass("borde_error");
		$("#txt_cantidad_producto_" + i).removeClass("borde_error");
		
		if ($("#tr_producto_val_" + i).is(":visible")) {
			var j = 0;
			while ($("#tr_det_val_" + i + "_" + j).length) {
				$("#txt_tipo_lente_" + i + "_" + j).removeClass("borde_error");
				$("#txt_serial_lente_" + i + "_" + j).removeClass("borde_error");
				$("#txt_poder_lente_" + i + "_" + j).removeClass("borde_error");
				j++;
			}
		}
	}
	
	if (trim($("#cmb_usuario_prof").val()) == "") {
		$("#cmb_usuario_prof").addClass("borde_error");
		resultado = 0;
	}
	if (trim($("#txt_fecha_prog").val()) == "") {
		$("#txt_fecha_prog").addClass("borde_error");
		resultado = 0;
	}
	if (trim($("#txt_hora_prog").val()) == "" && ($("#cmb_estado_prog").val() == "433" || $("#cmb_estado_prog").val() == "434")) {
		$("#txt_hora_prog").addClass("borde_error");
		resultado = 0;
	}
	if (trim($("#cmb_convenio").val()) == "") {
		$("#cmb_convenio").addClass("borde_error");
		resultado = 0;
	}
	if ($("#tr_motivo_cancela").is(":visible") && trim($("#cmb_motivo_cancela").val()) == "") {
		$("#cmb_motivo_cancela").addClass("borde_error");
		resultado = 0;
	}
	
	if (resultado == 1) {
		var cant_productos = 0;
		for (var i = 0; i < cant_aux; i++) {
			if ($("#tr_producto_" + i).is(":visible")) {
				cant_productos++;
				if (trim($("#cmb_tipo_bilateral_" + i).val()) == "") {
					$("#cmb_tipo_bilateral_" + i).addClass("borde_error");
					resultado = 0;
				}
				if (trim($("#txt_cantidad_producto_" + i).val()) == "") {
					$("#txt_cantidad_producto_" + i).addClass("borde_error");
					resultado = 0;
				}
				
				if ($("#tr_producto_val_" + i).is(":visible")) {
					var j = 0;
					while ($("#tr_det_val_" + i + "_" + j).length) {
						if (trim($("#txt_tipo_lente_" + i + "_" + j).val()) == "") {
							$("#txt_tipo_lente_" + i + "_" + j).addClass("borde_error");
							resultado = 0;
						}
						if (trim($("#txt_serial_lente_" + i + "_" + j).val()) == "") {
							$("#txt_serial_lente_" + i + "_" + j).addClass("borde_error");
							resultado = 0;
						}
						if (trim($("#txt_poder_lente_" + i + "_" + j).val()) == "") {
							$("#txt_poder_lente_" + i + "_" + j).addClass("borde_error");
							resultado = 0;
						}
						j++;
					}
				}
			}
		}
		
		if (cant_productos == 0) {
			resultado = -1;
		}
	}
	
	return resultado;
}

function programar_cita_preqx() {
    $("#frm_credencial").append('<input type="hidden" id="hdd_id_prog_cx" name="hdd_id_prog_cx" value="' + $("#hdd_id_prog_cx").val() + '" />');
    $("#frm_credencial").append('<input type="hidden" id="hdd_id_paciente" name="hdd_id_paciente" value="' + $("#hdd_id_paciente").val() + '"  />');
    $("#frm_credencial").append('<input type="hidden" id="hdd_id_usuario_prof" name="hdd_id_usuario_prof" value="' + $("#cmb_usuario_prof").val() + '" />');
	
    enviar_credencial("../citas/asignar_citas.php", $("#hdd_menu").val());
}
