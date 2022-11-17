//Funcion que valida el formulario de buscar usuario por nombre no numero de documento en la pagina principal pagos.php
function validar_buscar_anticipos() {
	$("#frm_buscar_anticipos").validate({
		debug: false,
		rules: {
			txt_paciente: {
				required: true,
			},
		},
		submitHandler: function(form) {
			buscar_anticipos(0);
			return false;
		}
	});
}

function mostrar_crear_anticipo() {
	var params = "opcion=1&ind_crear=1";
	
	llamarAjax("anticipos_ajax.php", params, "d_contenedor_ppal", "");
	$("#d_contenedor_ppal").css("display", "block");
}

function buscar_anticipos(accion) {
	$("#d_contenedor_ppal").css("display", "block");
	var parametro = "";
	if (accion != 1) {
		parametro = str_encode($("#txt_paciente").val());
	}
	
	var params = "opcion=1&parametro=" + parametro + "&ind_crear=0";
	
	llamarAjax("anticipos_ajax.php", params, "d_contenedor_ppal", "");
}

//Esta función muestra la advertencia para registra el pago
function validar_registrar_anticipo() {
	$("#btn_crear_anticipo").attr("disabled", "disabled");
	
	var hdd_pagar = $("#hdd_pagar").val();
	
	$("#contenedor_error").css("display", "none");
	$("#cmb_lugar").removeClass("borde_error");
	$("#cmb_usuario_prof").removeClass("borde_error");
	$("#cmb_tipo_documento").removeClass("borde_error");
	$("#txt_numero_documento").removeClass("borde_error");
	$("#txt_nombre_1").removeClass("borde_error");
	$("#txt_apellido_1").removeClass("borde_error");
	$("#txt_direccion").removeClass("borde_error");
	$("#cmb_pais").removeClass("borde_error");
	$("#cmb_departamento").removeClass("borde_error");
	$("#txt_nom_dep").removeClass("borde_error");
	$("#cmb_municipio").removeClass("borde_error");
	$("#txt_nom_mun").removeClass("borde_error");
	$("#txt_telefono_1").removeClass("borde_error");
	$("#d_buscar_tercero").removeClass("borde_error");
	
	var cont_medios_pago = parseInt($("#hdd_cont_medios_pago").val(), 10);
	var cont_medios_pago_sel = 0;
	for (var i = 0; i < cont_medios_pago; i++) {
		$("#cmb_tipo_pago_" + i).removeClass("borde_error");
		$("#txt_valor_pago_" + i).removeClass("borde_error");
		$("#cmb_banco_" + i).removeClass("borde_error");
		$("#txt_num_cheque_" + i).removeClass("borde_error");
		$("#txt_num_cuenta_" + i).removeClass("borde_error");
		$("#txt_num_autoriza_" + i).removeClass("borde_error");
		$("#txt_ano_vence_" + i).removeClass("borde_error");
		$("#txt_mes_vence_" + i).removeClass("borde_error");
		$("#txt_referencia_" + i).removeClass("borde_error");
		$("#txt_fecha_consigna_" + i).removeClass("borde_error");
		$("#cmb_franquicia_tc_" + i).removeClass("borde_error");
		$("#cmb_usuario_autoriza_" + i).removeClass("borde_error");
		if ($("#cmb_tipo_pago_" + i).val() != "") {
			cont_medios_pago_sel++;
		}
	}
	
	if (cont_medios_pago_sel <= 0) {
		alert("Error. Debe seleccionar por lo menos un medio de pago.");
		$("#btn_crear_anticipo").removeAttr("disabled");
		return;
	}
	
	var ind_error = false;
	if ($("#cmb_lugar").val() == "") {
		$("#cmb_lugar").addClass("borde_error")
		ind_error = true;
	}
	if ($("#cmb_usuario_prof").val() == "") {
		$("#cmb_usuario_prof").addClass("borde_error")
		ind_error = true;
	}
	
	for (var i = 0; i < cont_medios_pago; i++) {
		var id_tipo_pago_aux = $("#cmb_tipo_pago_" + i).val();
		if (id_tipo_pago_aux != "") {
			var ind_banco_aux = $("#hdd_tipo_pago_banco_" + id_tipo_pago_aux).val();
			var ind_usuario_aut_aux = $("#hdd_tipo_pago_usuario_aut_" + id_tipo_pago_aux).val();
			var ind_cheque_aux = $("#hdd_tipo_pago_ind_cheque_" + id_tipo_pago_aux).val();
			var ind_cuenta_aux = $("#hdd_tipo_pago_ind_cuenta_" + id_tipo_pago_aux).val();
			var ind_cod_seguridad_aux = $("#hdd_tipo_pago_ind_cod_seguridad_" + id_tipo_pago_aux).val();
			var ind_num_autoriza_aux = $("#hdd_tipo_pago_ind_num_autoriza_" + id_tipo_pago_aux).val();
			var ind_fecha_vence_aux = $("#hdd_tipo_pago_ind_fecha_vence_" + id_tipo_pago_aux).val();
			var ind_referencia_aux = $("#hdd_tipo_pago_ind_referencia_" + id_tipo_pago_aux).val();
			var ind_fecha_consigna_aux = $("#hdd_tipo_pago_ind_fecha_consigna_" + id_tipo_pago_aux).val();
			var ind_franquicia_tc_aux = $("#hdd_tipo_pago_ind_franquicia_tc_" + id_tipo_pago_aux).val();
			
			if (ind_banco_aux == "1" && $("#cmb_banco_" + i).val() == "") {
				$("#cmb_banco_" + i).addClass("borde_error")
				ind_error = true;
			}
			if (ind_cheque_aux == "1" && $("#txt_num_cheque_" + i).val() == "") {
				$("#txt_num_cheque_" + i).addClass("borde_error")
				ind_error = true;
			}
			if (ind_cuenta_aux == "1" && $("#txt_num_cuenta_" + i).val() == "") {
				$("#txt_num_cuenta_" + i).addClass("borde_error")
				ind_error = true;
			}
			if (ind_num_autoriza_aux == "1" && $("#txt_num_autoriza_" + i).val() == "") {
				$("#txt_num_autoriza_" + i).addClass("borde_error")
				ind_error = true;
			}
			if (ind_fecha_vence_aux == "1" && $("#txt_ano_vence_" + i).val() == "") {
				$("#txt_ano_vence_" + i).addClass("borde_error")
				ind_error = true;
			}
			if (ind_fecha_vence_aux == "1" && $("#txt_mes_vence_" + i).val() == "") {
				$("#txt_mes_vence_" + i).addClass("borde_error")
				ind_error = true;
			}
			if (ind_referencia_aux == "1" && $("#txt_referencia_" + i).val() == "") {
				$("#txt_referencia_" + i).addClass("borde_error")
				ind_error = true;
			}
			if (ind_fecha_consigna_aux == "1" && $("#txt_fecha_consigna_" + i).val() == "") {
				$("#txt_fecha_consigna_" + i).addClass("borde_error")
				ind_error = true;
			}
			if (ind_franquicia_tc_aux == "1" && $("#cmb_franquicia_tc_" + i).val() == "") {
				$("#cmb_franquicia_tc_" + i).addClass("borde_error")
				ind_error = true;
			}
			if (ind_usuario_aut_aux == "1" && $("#cmb_usuario_autoriza_" + i).val() == "") {
				$("#cmb_usuario_autoriza_" + i).addClass("borde_error")
				ind_error = true;
			}
			if ($("#txt_valor_pago_" + i).val() == "" ||
					(parseInt($("#txt_valor_pago_" + i).val(), 10) == 0 && $("#cmb_tipo_pago_" + i).val() != "99")) {
				$("#txt_valor_pago_" + i).addClass("borde_error")
				ind_error = true;
			}
			if ($("#cmb_tipo_pago_" + i).val() == "99" && $("#txt_valor_pago_" + i).val() != "0") {
				alert("Error. El valor de no pago debe ser cero (0).");
				$("#btn_crear_anticipo").removeAttr("disabled");
				return;
			}
		} else if ($("#txt_valor_pago_" + i).val() != "" && parseInt($("#txt_valor_pago_" + i).val(), 10) > 0) {
			$("#cmb_tipo_pago_" + i).addClass("borde_error")
			ind_error = true;
		}
	}
	
	var ind_tercero_obl = parseInt($("#hdd_ind_tercero_obl").val(), 10);
	if ($("#hdd_id_tercero").val() == "" && ind_tercero_obl == "1" && hdd_pagar != "0") {
		$("#d_buscar_tercero").addClass("borde_error")
		ind_error = true;
	}
	
	//Datos del paciente
	if ($("#cmb_tipo_documento").val() == "") {
		$("#cmb_tipo_documento").addClass("borde_error")
		ind_error = true;
	}
	if (trim($("#txt_numero_documento").val()) == "") {
		$("#txt_numero_documento").addClass("borde_error")
		ind_error = true;
	}
	if (trim($("#txt_nombre_1").val()) == "") {
		$("#txt_nombre_1").addClass("borde_error")
		ind_error = true;
	}
	if (trim($("#txt_apellido_1").val()) == "") {
		$("#txt_apellido_1").addClass("borde_error")
		ind_error = true;
	}
	if (trim($("#txt_direccion").val()) == "") {
		$("#txt_direccion").addClass("borde_error")
		ind_error = true;
	}
	if ($("#cmb_pais").val() == "") {
		$("#cmb_pais").addClass("borde_error")
		ind_error = true;
	} else if ($("#cmb_pais").val() == "1") {
		if ($("#cmb_departamento").val() == "") {
			$("#cmb_departamento").addClass("borde_error")
			ind_error = true;
		}
		if ($("#cmb_municipio").val() == "") {
			$("#cmb_municipio").addClass("borde_error")
			ind_error = true;
		}
	} else {
		if (trim($("#txt_nom_dep").val()) == "") {
			$("#txt_nom_dep").addClass("borde_error")
			ind_error = true;
		}
		if (trim($("#txt_nom_mun").val()) == "") {
			$("#txt_nom_mun").addClass("borde_error")
			ind_error = true;
		}
	}
	if (trim($("#txt_telefono_1").val()) == "") {
		$("#txt_telefono_1").addClass("borde_error")
		ind_error = true;
	}
	
	if (!ind_error) {
		mostrar_formulario_flotante(1);
		$("#d_interno").html(
			'<table class="datagrid" border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
				'<tr class="headegrid">' +
					'<th align="center" class="msg_alerta" style="border: 1px solid #fff;">' +
						'<h3>&iquest;Est&aacute; seguro que desea registrar el anticipo?</h3>' +
					'</th>' +
				'</tr>' +
				'<tr>' +
					'<th id="th_registrar_pago" align="center" style="width:5%;border: 1px solid #fff;">' +
						'<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" value="Aceptar" class="btnPrincipal" onclick="registrar_anticipo();"/>' +
						'&nbsp;&nbsp;' +
						'<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" value="Cancelar" class="btnSecundario" onclick="cerrar_div_centro();"/>' +
					'</th>' +
				'</tr>' +
			'</table>');
		posicionarDivFlotante("d_centro");
		
		$("#btn_crear_anticipo").removeAttr("disabled");
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Los campos marcados en rojo son obligatorios");
		window.scrollTo(0, 0);
		
		$("#btn_crear_anticipo").removeAttr("disabled");
	}
}

function registrar_anticipo() {
	$("#btn_cancelar_si").attr("disabled", "disabled");
	$("#th_registrar_pago").html('Realizando comunicaci&oacute;n externa...&nbsp;<img src="../imagenes/ajax-loader.gif" /><br /><br />');
    var params = "opcion=2&id_paciente=" + $("#hdd_id_paciente").val() +
				 "&id_lugar=" + $("#cmb_lugar").val() +
				 "&id_usuario_prof=" + $("#cmb_usuario_prof").val() +
				 "&observaciones_anticipo=" + str_encode($("#txt_observaciones_anticipo").val()) +
				 "&id_tercero=" + $("#hdd_id_tercero").val() +
				 "&id_tipo_documento=" + $("#cmb_tipo_documento").val() +
				 "&numero_documento=" + str_encode($("#txt_numero_documento").val()) +
				 "&nombre_1=" + str_encode($("#txt_nombre_1").val()) +
				 "&nombre_2=" + str_encode($("#txt_nombre_2").val()) +
				 "&apellido_1=" + str_encode($("#txt_apellido_1").val()) +
				 "&apellido_2=" + str_encode($("#txt_apellido_2").val()) +
				 "&direccion=" + str_encode($("#txt_direccion").val()) +
				 "&id_pais=" + $("#cmb_pais").val() +
				 "&cod_dep=" + $("#cmb_departamento").val() +
				 "&nom_dep=" + str_encode($("#txt_nom_dep").val()) +
				 "&cod_mun=" + $("#cmb_municipio").val() +
				 "&nom_mun=" + str_encode($("#txt_nom_mun").val()) +
				 "&telefono_1=" + str_encode($("#txt_telefono_1").val()) +
				 "&telefono_2=" + str_encode($("#txt_telefono_2").val());
	
	var cont_medios_pago = parseInt($("#hdd_cont_medios_pago").val(), 10);
	for (var i = 0; i < cont_medios_pago; i++) {
		var ind_cod_seguridad_aux = $("#hdd_tipo_pago_ind_cod_seguridad_" + $("#cmb_tipo_pago_" + i).val()).val();
		params += "&tipo_pago_" + i + "=" + $("#cmb_tipo_pago_" + i).val() +
				  "&banco_pago_" + i + "=" + $("#cmb_banco_" + i).val() +
				  "&num_cheque_" + i + "=" + $("#txt_num_cheque_" + i).val() +
				  "&num_cuenta_" + i + "=" + $("#txt_num_cuenta_" + i).val() +
				  "&cod_seguridad_" + i + "=" + (ind_cod_seguridad_aux == "1" ? "999" : "") +
				  "&num_autoriza_" + i + "=" + $("#txt_num_autoriza_" + i).val() +
				  "&ano_vence_" + i + "=" + $("#txt_ano_vence_" + i).val() +
				  "&mes_vence_" + i + "=" + $("#txt_mes_vence_" + i).val() +
				  "&referencia_" + i + "=" + $("#txt_referencia_" + i).val() +
				  "&fecha_consigna_" + i + "=" + $("#txt_fecha_consigna_" + i).val() +
				  "&id_franquicia_tc_" + i + "=" + $("#cmb_franquicia_tc_" + i).val() +
				  "&valor_pago_" + i + "=" + $("#txt_valor_pago_" + i).val() +
				  "&id_usuario_autoriza_" + i + "=" + $("#cmb_usuario_autoriza_" + i).val();
	}
	params += "&cant_medios_pago=" + cont_medios_pago;
	
	llamarAjax("anticipos_ajax.php", params, "d_resultado", "verifica_registrar_anticipo();");
}

//Funcion que verifica el guardado del anticipo
function verifica_registrar_anticipo() {
	var resultado_aux = $("#hdd_id_paciente_resul").val();
	
	if (resultado_aux > 0) {
		resultado_aux = $("#hdd_id_anticipo_resul").val();
		
		if (resultado_aux > 0) {
			$("#hdd_id_anticipo").val(resultado_aux);
			mostrar_formulario_flotante(0);
			$("#contenedor_error").css("display", "none");
			$("#contenedor_exito").css("display", "block");
			$("#contenedor_exito").html("Anticipo registrado con &eacute;xito");
			$("#d_contenedor_ppal").css("display", "none");
			$("#txt_paciente").val("");
			if ($("#hdd_num_anticipo_siesa").length) {
				$("#lb_num_anticipo").html($("#hdd_num_anticipo_siesa").val());
			}
			var valor_pagar_aux = parseInt($("#hdd_pagar").val(), 10);
			$("#sp_saldo_anticipo").html("$" + valor_pagar_aux.formatoNumerico());
			
			//Se abre el archivo del recibo de pago
			imprimir_recibo_anticipo();
			
			setTimeout(function() {
				$("#contenedor_exito").css("display", "none");
			}, 2000);
		} else if (resultado_aux == "0") {
			mostrar_formulario_flotante(0);
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Error - No se encontraros profesionales disponibles para continuar con la atenci\xf3n");
		} else {
			mostrar_formulario_flotante(0);
			if ($("#hdd_resul_tercero_siesa").length && $("#hdd_resul_tercero_siesa").val() != "1") {
				$("#contenedor_error").html("Error en el registro externo del tercero del pago:<br />" + $("#hdd_mensaje_tercero_siesa").val());
			} else {
				if ($("#hdd_resul_anticipo_siesa").length && $("#hdd_resul_anticipo_siesa").val() != "1") {
					$("#contenedor_error").html("Error en el registro externo del anticipo:<br />" + $("#hdd_mensaje_anticipo_siesa").val());
				} else {
					$("#contenedor_error").html("Error interno al tratar de registrar el pago");
				}
			}
			$("#contenedor_error").css("display", "block");
		}
	} else if (resultado_aux == "-2") {
		mostrar_formulario_flotante(0);
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error - El n\xfamero de documento ya se encuentra asignado a otro paciente");
	} else {
		mostrar_formulario_flotante(0);
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error interno al tratar de registrar los datos del paciente");
	}
	$("#btn_cancelar_si").removeAttr("disabled");
	window.scrollTo(0, 0);
}

function mostrar_formulario_flotante(tipo) {
    if (tipo == 1) {//mostrar
        $("#fondo_negro").css("display", "block");
        $("#d_centro").slideDown(400).css("display", "block");
    } else if (tipo == 0) {//Ocultar
        $("#fondo_negro").css("display", "none");
        $("#d_centro").slideDown(400).css("display", "none");
    }
}

//Funcion que valida el formulario de tipos de pago
function validar_tipo_pago(indice) {
	if ($("#cmb_tipo_pago_" + indice).val() != "") { //Si el valor es seleccionado
		var id_tipo = $("#cmb_tipo_pago_" + indice).val();
		
		//Verifica que el valor no ha sido seleccionado por otro combo box
		var cont_medios_pago = parseInt($("#hdd_cont_medios_pago").val(), 10);
		var ind_repetido_aux = false;
		for (var i = 0; i < cont_medios_pago; i++) {
			if (i != indice && $("#cmb_tipo_pago_" + i).val() == id_tipo) {
				ind_repetido_aux = true;
				break;
			}
		}
		
		//Si se trata de una corrección verifica que el valor base haya sido seleccionado
		var ind_tipo_rel = true;
		var id_tipo_rel = $("#hdd_tipo_pago_rel_" + id_tipo).val();
		if (id_tipo_rel != "") {
			ind_tipo_rel = false;
			for (var i = 0; i < cont_medios_pago; i++) {
				if (i != indice && $("#cmb_tipo_pago_" + i).val() == id_tipo_rel) {
					ind_tipo_rel = true;
					break;
				}
			}
		}
		
		if (!ind_repetido_aux && ind_tipo_rel) {
			if (id_tipo == "99") { //No pago
				$("#txt_valor_pago_" + indice).val(0);
				$("#txt_valor_pago_" + indice).attr("disabled", "disabled");
			} else {
				$("#txt_valor_pago_" + indice).removeAttr("disabled");
			}
			
			procesar_tipo_pago(indice); //Hace la sumatoria de nuevo
			
			//Se verifica si se trata de un tipo de pago negativo
			var ind_negativo = parseInt($("#hdd_tipo_pago_negativo_" + id_tipo).val(), 10);
			if (ind_negativo) {
				$("#txt_valor_pago_" + indice).addClass("texto-rojo");
			} else {
				$("#txt_valor_pago_" + indice).removeClass("texto-rojo");
			}
			
			//Consulta los diferentes indicadores para el tipo de pago seleccionado
			var ind_banco = $("#hdd_tipo_pago_banco_" + id_tipo).val();
			var ind_usuario_aut = $("#hdd_tipo_pago_usuario_aut_" + id_tipo).val();
			var ind_cheque = $("#hdd_tipo_pago_ind_cheque_" + id_tipo).val();
            var ind_cuenta = $("#hdd_tipo_pago_ind_cuenta_" + id_tipo).val();
            var ind_cod_seguridad = $("#hdd_tipo_pago_ind_cod_seguridad_" + id_tipo).val();
            var ind_num_autoriza = $("#hdd_tipo_pago_ind_num_autoriza_" + id_tipo).val();
            var ind_fecha_vence = $("#hdd_tipo_pago_ind_fecha_vence_" + id_tipo).val();
            var ind_referencia = $("#hdd_tipo_pago_ind_referencia_" + id_tipo).val();
            var ind_fecha_consigna = $("#hdd_tipo_pago_ind_fecha_consigna_" + id_tipo).val();
			var ind_franquicia_tc = $("#hdd_tipo_pago_ind_franquicia_tc_" + id_tipo).val();
			
			if (ind_banco == "1") {
				$("#d_banco_mp_" + indice).show();
			} else {
				$("#cmb_banco_" + indice).val("");
				$("#d_banco_mp_" + indice).hide();
			}
			if (ind_usuario_aut == "1") {
				$("#cmb_usuario_autoriza_" + indice).removeAttr("disabled");
			} else {
				$("#cmb_usuario_autoriza_" + indice).attr("disabled", true);
				$("#cmb_usuario_autoriza_" + indice).val("");
			}
			if (ind_cheque == "1") {
				$("#d_cheque_mp_" + indice).show();
			} else {
				$("#txt_num_cheque_" + indice).val("");
				$("#d_cheque_mp_" + indice).hide();
			}
			if (ind_cuenta == "1") {
				$("#d_cuenta_mp_" + indice).show();
			} else {
				$("#txt_num_cuenta_" + indice).val("");
				$("#d_cuenta_mp_" + indice).hide();
			}
			if (ind_num_autoriza == "1") {
				$("#d_num_autoriza_mp_" + indice).show();
			} else {
				$("#txt_num_autoriza_" + indice).val("");
				$("#d_num_autoriza_mp_" + indice).hide();
			}
			if (ind_fecha_vence == "1") {
				$("#d_fecha_vence_mp_" + indice).show();
			} else {
				$("#txt_ano_vence_" + indice).val("");
				$("#txt_mes_vence_" + indice).val("");
				$("#d_fecha_vence_mp_" + indice).hide();
			}
			if (ind_referencia == "1") {
				$("#d_referencia_mp_" + indice).show();
			} else {
				$("#txt_referencia_" + indice).val("");
				$("#d_referencia_mp_" + indice).hide();
			}
			if (ind_fecha_consigna == "1") {
				$("#d_fecha_consigna_mp_" + indice).show();
			} else {
				$("#txt_fecha_consigna_" + indice).val("");
				$("#d_fecha_consigna_mp_" + indice).hide();
			}
			if (ind_franquicia_tc == "1") {
				$("#d_franquicia_tc_mp_" + indice).show();
			} else {
				$("#cmb_franquicia_tc_" + indice).val("");
				$("#d_franquicia_tc_mp_" + indice).hide();
			}
		} else if (ind_repetido_aux) {
			alert("Error. El tipo de pago ya ha sido seleccionado.");
			$("#cmb_tipo_pago_" + indice).val("");
		} else {
			alert("Error. Este tipo de pago no puede ser seleccionado.");
			$("#cmb_tipo_pago_" + indice).val("");
		}
	} else {
		$("#cmb_banco_" + indice).val("");
		$("#cmb_banco_" + indice).attr("disabled", "disabled");
		$("#txt_valor_pago_" + indice).val("0");
		$("#txt_valor_pago_" + indice).attr("disabled", "disabled");
		procesar_tipo_pago(indice);
	}
}

//Verifica si el tipo de pago seleccionado requiere un tercero
function verificar_indicador_tercero() {
    var ind_tercero = $("#hdd_ind_tercero_tp").val();
	var id_tercero_aux = $("#hdd_id_tercero").val();
	
	if (ind_tercero == "1" || id_tercero_aux != "") {
		$("#fs_datos_tercero").css("display", "block");
	} else {
		$("#fs_datos_tercero").css("display", "none");
		$("#hdd_id_tercero").val("");
		$("#d_nombre_tercero").html("");
	}
}

function seleccionar_banco(id_valor) {
    $("#" + id_valor).attr("disabled", false);//Habilita el input
}

//Funcion que valida el formulario de tipos de pago
function procesar_tipo_pago(indice) {
	calcular_total_medios(true);
}

function calcular_total_medios(ind_actualizar) {
	var cont_medios_pago = parseInt($("#hdd_cont_medios_pago").val(), 10);
	var total_aux = 0;
	for (var i = 0; i < cont_medios_pago; i++) {
		var id_tipo = $("#cmb_tipo_pago_" + i).val();
		if ($("#hdd_tipo_pago_negativo_" + id_tipo).val() == "1") {
			total_aux -= parseInt($("#txt_valor_pago_" + i).val(), 10);
		} else {
			total_aux += parseInt($("#txt_valor_pago_" + i).val(), 10);
		}
	}
	
	if (ind_actualizar) {
		$("#sp_total_pagar").text(total_aux.formatoNumerico());
		$("#hdd_pagar_aux").val(total_aux);
	}
	
	return total_aux;
}

function muestraFormularioFlotante(tipo) {
    if (tipo == 1) { //mostrar
        $("#fondo_negro_servicios").css("display", "block");
        $("#d_centro_servicios").slideDown(400).css("display", "block");
		
		posicionarDivFlotante("d_centro_servicios");
    } else if (tipo == 0) { //Ocultar
        $("#fondo_negro_servicios").css("display", "none");
        $("#d_centro_servicios").slideDown(400).css("display", "none");
    }
}

function seleccionar_anticipo(id_anticipo) {
	var params = "opcion=1&parametro=&ind_crear=0" +
				 "&id_anticipo=" + id_anticipo;
	
	llamarAjax("anticipos_ajax.php", params, "d_contenedor_ppal", "");
}

function seleccionar_pais(id_pais) {
	if (id_pais == "1") {
		$("#d_departamento").css("display", "block");
		$("#d_municipios").css("display", "block");
		$("#d_nombre_dep").css("display", "none");
		$("#d_nombre_mun").css("display", "none");
		$("#txt_nom_dep").val("");
		$("#txt_nom_mun").val("");
	} else {
		$("#d_departamento").css("display", "none");
		$("#d_municipios").css("display", "none");
		$("#d_nombre_dep").css("display", "block");
		$("#d_nombre_mun").css("display", "block");
		$("#cmb_departamento").val("");
		$("#cmb_municipio").val("");
	}
}

function seleccionar_departamento(cod_dep, cod_mun) {
	var params = "opcion=9&cod_dep=" + cod_dep + "&cod_mun=" + cod_mun;
	
	llamarAjax("anticipos_ajax.php", params, "d_municipios", "");
}

function verificar_paciente(numero_documento) {
	var params = "opcion=10&numero_documento=" + numero_documento +
				 "&id_tipo_documento=" + $("#cmb_tipo_documento").val();
	
	llamarAjax("anticipos_ajax.php", params, "d_val_paciente", "preguntar_cargar_paciente(\"" + numero_documento + "\");");
}

function preguntar_cargar_paciente(numero_documento) {
    if (isObject(document.getElementById("hdd_id_paciente_b"))) {
        $("#fondo_negro").css("display", "block");
        $("#d_centro").slideDown(400).css("display", "block");
		
		var codigohtml =
			'<div class="encabezado">' +
				'<h3>Existe un registro con la informaci&oacute;n</h3>' +
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
					'<td style="text-align:left;font-weight: 700;">' + numero_documento + '</td>' +
				'</tr>' +
				'<tr>' +
					'<td style="text-align:right;">Nombre(s):</td>' +
					'<td style="text-align:left;font-weight: 700;">' + $("#hdd_nombre_1_b").val() + ' ' + $("#hdd_nombre_2_b").val() + '</td>' +
				'</tr>' +
				'<tr>' +
					'<td style="text-align:right;">Apellido(s):</td>' +
					'<td style="text-align:left;font-weight: 700;">' + $("#hdd_apellido_1_b").val() + ' ' + $("#hdd_apellido_2_b").val() + '</td>' +
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
						'<input type="button" class="btnPrincipal peq" value="Aceptar" onclick="cargar_paciente();" />&nbsp;&nbsp;' +
						'<input type="button" class="btnSecundario peq" value="Cancelar" onclick="cerrar_div_centro();" />' +
					'</td>' +
				'</tr>' +
			'</table>';
		
        $(".div_interno").html(codigohtml);
		
        posicionarDivFlotante("d_centro");
    }
}

function cargar_paciente() {
	$("#txt_nombre_1").val($("#hdd_nombre_1_b").val());
	$("#txt_nombre_2").val($("#hdd_nombre_2_b").val());
	$("#txt_apellido_1").val($("#hdd_apellido_1_b").val());
	$("#txt_apellido_2").val($("#hdd_apellido_2_b").val());
	$("#txt_direccion").val($("#hdd_direccion_b").val());
	$("#txt_telefono_1").val($("#hdd_telefono_1_b").val());
	$("#txt_telefono_2").val($("#hdd_telefono_2_b").val());
	$("#cmb_pais").val($("#hdd_id_pais_b").val());
	$("#cmb_departamento").val($("#hdd_cod_dep_b").val());
	$("#txt_nom_dep").val($("#hdd_nom_dep_b").val());
	$("#txt_nom_mun").val($("#hdd_nom_mun_b").val());
	$("#hdd_id_paciente").val($("#hdd_id_paciente_b").val());
	$("#hdd_edad_paciente").val($("#hdd_edad_b").val());
	$("#sp_edad").html($("#hdd_edad_b").val() + " a&ntilde;os");
	seleccionar_pais($("#hdd_id_pais_b").val());
	seleccionar_departamento($("#hdd_cod_dep_b").val(), $("#hdd_cod_mun_b").val());
	validar_mostrar_datos_tercero(1);
	
	cerrar_div_centro();
}

function imprimir_recibo_anticipo() {
	var params = "opcion=2&id_anticipo=" + $("#hdd_id_anticipo").val();
	
	llamarAjax("recibo_pago_ajax.php", params, "d_imprimir_recibo", "mostrar_recibo_anticipo();");
}

function mostrar_recibo_anticipo() {
	if ($("#hdd_ruta_arch_pdf").length) {
		var ruta = $("#hdd_ruta_arch_pdf").val();
		window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=recibo_anticipo.pdf", "_blank");
	}
}

function anular_anticipo() {
	var ind_continuar = true;
	$("#txt_observaciones_anticipo").removeClass("borde_error");
	$("#cmb_causales_devolucion").removeClass("borde_error");
	
	if (trim($("#txt_observaciones_anticipo").val()) == "") {
		$("#txt_observaciones_anticipo").addClass("borde_error");
		ind_continuar = false;
	}
	if (trim($("#cmb_causales_devolucion").val()) == "") {
		$("#cmb_causales_devolucion").addClass("borde_error");
		ind_continuar = false;
	}
	
	if (ind_continuar) {
		mostrar_formulario_flotante(1);
		$("#d_interno").html(
			'<table class="datagrid" border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
				'<tr class="headegrid">' +
					'<th align="center" class="msg_alerta" style="border: 1px solid #fff;">' +
						'<h3>&iquest;Est&aacute; seguro de querer anular el anticipo?</h3>' +
					'</th>' +
				'</tr>' +
				'<tr>' +
					'<th id="th_anular_anticipo" align="center" style="width:5%;border: 1px solid #fff;">' +
						'<input type="button" id="btn_cancelar_si_b" nombre="btn_cancelar_si_b" value="Aceptar" class="btnPrincipal" onclick="continuar_anular_anticipo();"/>' +
						'&nbsp;&nbsp;' +
						'<input type="button" id="btn_cancelar_no_b" nombre="btn_cancelar_no_b" value="Cancelar" class="btnSecundario" onclick="cerrar_div_centro();"/>' +
					'</th>' +
				'</tr>' +
			'</table>');
		posicionarDivFlotante("d_centro");
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Los campos marcados en rojo son obligatorios");
		window.scrollTo(0, 0);
	}
}

function continuar_anular_anticipo() {
	$("#btn_cancelar_si_b").attr("disabled", "disabled");
	$("#th_anular_anticipo").html('Realizando comunicaci&oacute;n externa...&nbsp;<img src="../imagenes/ajax-loader.gif" /><br /><br />');
	
	var params = "opcion=11&id_anticipo=" + $("#hdd_id_anticipo").val() +
				 "&observaciones_anticipo=" + str_encode($("#txt_observaciones_anticipo").val()) +
				 "&id_causal_borra=" + $("#cmb_causales_devolucion").val();
	
	llamarAjax("anticipos_ajax.php", params, "d_resultado", "validar_anular_anticipo();");
}

function validar_anular_anticipo() {
	var resultado_aux = parseInt($("#hdd_resul_anular_anticipo").val(), 10);
	
	if (resultado_aux > 0) {
		var num_anticipo_aux = $("#lb_num_anticipo").html();
		var texto_adic_aux;
		if (num_anticipo_aux != "") {
			texto_adic_aux = "<br /><b>Por favor anule manualmente el anticipo relacionado n&uacute;mero " + num_anticipo_aux + ".</b>";
		} else {
			texto_adic_aux = "";
		}
		
		mostrar_formulario_flotante(0);
		$("#contenedor_error").css("display", "none");
		$("#contenedor_exito").css("display", "block");
		$("#contenedor_exito").html("El anticipo ha sido anulado." + texto_adic_aux);
		$("#d_contenedor_ppal").css("display", "none");
		$("#txt_paciente").val("");
	} else {
		mostrar_formulario_flotante(0);
		$("#contenedor_error").html("Error interno al tratar de registrar la anulaci&oacute;n");
		$("#contenedor_error").css("display", "block");
	}
	window.scrollTo(0, 0);
}

function agregar_medio_pago() {
	var cont_medios_pago = parseInt($("#hdd_cont_medios_pago").val(), 10);
	
	if (cont_medios_pago < 10) {
		$("#tr_medio_pago_" + cont_medios_pago).css("display", "table-row");
		cont_medios_pago++;
		$("#hdd_cont_medios_pago").val(cont_medios_pago);
	}
	
	calcular_total_medios(true);
}

function restar_medio_pago() {
	var cont_medios_pago = parseInt($("#hdd_cont_medios_pago").val(), 10);
	
	if (cont_medios_pago > 0) {
		cont_medios_pago--;
		$("#tr_medio_pago_" + cont_medios_pago).css("display", "none");
		$("#cmb_tipo_pago_" + cont_medios_pago).val("");
		$("#cmb_banco_" + cont_medios_pago).val("");
		$("#txt_valor_pago_" + cont_medios_pago).val("0");
		$("#txt_valor_pago_" + cont_medios_pago).attr("disabled", "disabled");
		$("#hdd_cont_medios_pago").val(cont_medios_pago);
	}
	
	calcular_total_medios(true);
}

function registrar_cambios_totales() {
	var cont_medios_pago = parseInt($("#hdd_cont_medios_pago").val(), 10);
	var cont_conceptos = 0;
	var indice_boleta = -1;
	var indice_concepto = -1;
	for (var i = 0; i < cont_medios_pago; i++) {
		if ($("#cmb_tipo_pago_" + i).val() == "0") {
			indice_boleta = i;
		} else {
			indice_concepto = i;
			cont_conceptos++;
		}
	}
	
	if (indice_boleta != -1 || cont_conceptos == 1) {
		var cant_productos = parseInt($("#hdd_cant_productos").val(), 10);
		var total_aux = 0;
		var total_cuota_aux = 0;
		for (var i = 0; i < cant_productos; i++) {
			if (isObject(document.getElementById("hdd_tipo_precio_" + i))) {
				var valor_aux = parseFloat("0" + $("#txt_valor_" + i).val());
				var valor_cuota_aux = parseFloat("0" + $("#txt_valor_cuota_" + i).val());
				var cantidad_aux = parseInt("0" + $("#txt_cantidad_" + i).val(), 10);
				
				total_aux += valor_aux * cantidad_aux;
				total_cuota_aux += valor_cuota_aux;
			}
		}
		
		if (indice_boleta != -1) {
			$("#txt_valor_pago_" + indice_boleta).val(total_aux - total_cuota_aux);
			if (cont_conceptos == 1) {
				$("#txt_valor_pago_" + indice_concepto).val(total_cuota_aux);
			}
			procesar_tipo_pago(indice_boleta);
		} else if ((total_aux - total_cuota_aux) != 0) {
			//Primer concepto libre
			var ind_boleta_asig = false;
			var ind_total_asig = false;
			for (var i = 0; i < cant_productos; i++) {
				if (!ind_boleta_asig && $("#cmb_tipo_pago_" + i).val() == "") {
					$("#cmb_tipo_pago_" + i).val("0");
					$("#txt_valor_pago_" + i).val(total_aux - total_cuota_aux);
					ind_boleta_asig = true;
				} else if (!ind_total_asig) {
					$("#txt_valor_pago_" + i).val(total_cuota_aux);
					ind_total_asig = true;
				}
			}
			
			if (!ind_total_asig && total_cuota_aux > 0) {
				//Se agrega un nuevo concepto para registrar el valor de cuota
				agregar_medio_pago();
				$("#txt_valor_pago_" + cont_medios_pago).val(total_cuota_aux);
			}
		} else if (cont_conceptos == 1) {
			$("#txt_valor_pago_" + indice_concepto).val(total_aux);
			procesar_tipo_pago(indice_concepto);
		}
		
		//Se actualizan los totales y diferencia
		$("#sp_total_pagar").html(total_aux.formatoNumerico());
		$("#sp_total_cuota").html(total_cuota_aux.formatoNumerico());
		$("#sp_total_diferencia").html((total_aux - total_cuota_aux).formatoNumerico());
		$("#hdd_pagar").val(total_aux);
	}
}

function mostrar_buscar_tercero() {
	var params = "opcion=12";
	
	llamarAjax("anticipos_ajax.php", params, "d_interno_servicios", "muestraFormularioFlotante(1);");
}

function buscar_terceros_pago() {
    $("#frm_listado_terceros").validate({
        rules: {
            txt_parametro_terceros: {
                required: true,
            },
        },
        submitHandler: function() {
            var params = "opcion=13&parametro_terceros=" + str_encode($("#txt_parametro_terceros").val());
			
			llamarAjax("anticipos_ajax.php", params, "d_resultado_b_terceros", "");

            return false;
        },
    });
}

function agregar_tercero(id_tercero, nombre_tercero) {
	$("#hdd_id_tercero").val(id_tercero);
	$("#d_nombre_tercero").html(nombre_tercero);
	
	//Se cierra el div
	muestraFormularioFlotante(0, 1);
}

function cargar_formulario_nuevo_tercero() {
	var params = "opcion=2&tipo=1&id_tercero=0&id_contador=1";
	
	llamarAjax("../administracion/terceros_ajax.php", params, "d_interno_servicios", "muestraFormularioFlotante(1);");
}

function crear_tercero_pago(id_contador, indice) {
	$("#btn_guardar_tercero").attr("disabled", "disabled");
	$("#d_contenedor_error_2").css("display", "none");
	if (validar_tercero()) {
		var params = "opcion=3&tipo=1&id_contador=1" +
					 "&id_tipo_documento=" + $("#cmb_tipo_documento_t").val() +
					 "&numero_documento=" + $("#txt_numero_documento_t").val() +
					 "&numero_verificacion=" + $("#txt_numero_verificacion_t").val() +
					 "&nombre_tercero=" + str_encode($("#txt_nombre_tercero_t").val()) +
					 "&nombre_1=" + str_encode($("#txt_nombre_1_t").val()) +
					 "&nombre_2=" + str_encode($("#txt_nombre_2_t").val()) +
					 "&apellido_1=" + str_encode($("#txt_apellido_1_t").val()) +
					 "&apellido_2=" + str_encode($("#txt_apellido_2_t").val()) +
					 "&email=" + str_encode($("#txt_email_tercero_t").val()) +
					 "&ind_activo=" + ($("#chk_activo_t").is(":checked") ? 1 : 0) +
					 "&indice=" + indice;
		
		llamarAjax("../administracion/terceros_ajax.php", params, "d_resultado_tercero_t", "verificar_guardar_tercero_pago(" + indice + ");");
	} else {
		$("#d_contenedor_error_2").css("display", "block");
		$("#d_contenedor_error_2").html("Los campos marcados en rojo son obligatorios");
		window.scroll(0, 0);
		$("#btn_guardar_tercero").removeAttr("disabled");
	}
}

function verificar_guardar_tercero_pago(indice) {
    var resultado = parseInt($("#hdd_guardar_tercero").val(), 10);
	
    if (resultado > 0) {
        $("#d_contenedor_exito_2").css("display", "block");
        $("#d_contenedor_exito_2").html("Registro guardado con &eacute;xito");
		
		$("#hdd_id_tercero").val(resultado);
		$("#d_nombre_tercero").html($("#hdd_nombre_tercero_crea1_" + indice).val());
		
        setTimeout(function() {
            $("#d_contenedor_exito_2").css("display", "none");
			$("#d_interno_servicios").html("");
	        muestraFormularioFlotante(0);
        }, 2000);
    } else if (resultado == -2) {
        $("#d_contenedor_error_2").css("display", "block");
        $("#d_contenedor_error_2").html("Error interno al tratar de guardar el tercero");
		$("#btn_guardar_tercero").removeAttr("disabled");
    } else if (resultado == -3) {
        $("#d_contenedor_error_2").css("display", "block");
        $("#d_contenedor_error_2").html("Error - N&uacute;mero de verificaci&oacute;n no v&aacute;lido");
		$("#txt_numero_verificacion").addClass("borde_error");
		$("#btn_guardar_tercero").removeAttr("disabled");
    } else if (resultado == -4) {
        $("#d_contenedor_error_2").css("display", "block");
        $("#d_contenedor_error_2").html("Error - El n&uacute;mero de documento ya se encuentra registrado");
		$("#txt_numero_documento_t").addClass("borde_error");
		$("#btn_guardar_tercero").removeAttr("disabled");
    } else {
        $("#d_contenedor_error_2").css("display", "block");
        $("#d_contenedor_error_2").html("Error al tratar de guardar el tercero");
		$("#btn_guardar_tercero").removeAttr("disabled");
    }
}

function borrar_tercero() {
	$("#hdd_id_tercero").val("");
	$("#d_nombre_tercero").html("");
}

function validar_mostrar_datos_tercero() {
	var ind_mostrar = true;
	var cont_medios_pago = parseInt($("#hdd_cont_medios_pago").val(), 10);
	var mensaje_aux = "";
	var ind_tercero_obl = 0;
	for (var i = 0; i < cont_medios_pago; i++) {
		var id_tipo_aux = $("#cmb_tipo_pago_" + i).val();
		if ($("#hdd_tipo_pago_tercero_" + id_tipo_aux).val() == "1") {
			ind_mostrar = true;
			mensaje_aux = "Uno de los tipos de pago seleccionados requiere la asignaci&oacute;n de un tercero.";
			ind_tercero_obl = 1;
			break;
		}
	}
	
	//Edad
	if ($("#hdd_edad_paciente").val() != "") {
		var edad_aux = parseInt($("#hdd_edad_paciente").val(), 10);
		if (edad_aux < 18) {
			ind_mostrar = true;
			mensaje_aux += (mensaje_aux != "" ? "<br />" : "");
			mensaje_aux += "El paciente es menor de edad, se debe asignar un tercero (este dato es obligatorio para menores de 16 a&ntilde;os).";
			if (edad_aux < 16) {
				ind_tercero_obl = 1;
			}
		}
	}
	
	mostrar_ocultar_datos_tercero(ind_mostrar, mensaje_aux, ind_tercero_obl);
}

function mostrar_ocultar_datos_tercero(ind_mostrar, mensaje, ind_tercero_obl) {
	if (ind_mostrar) {
		$("#fs_datos_tercero").css("display", "block");
		$("#lbl_texto_tercero").html(mensaje);
		$("#hdd_ind_tercero_obl").val(ind_tercero_obl);
	} else {
		$("#fs_datos_tercero").css("display", "none");
		$("#hdd_ind_tercero_obl").val(0);
	}
}

function recalcular_precios_cx_cop_cm() {
	var id_paciente = $("#hdd_id_paciente").val();
	var id_plan = $("#cmb_plan1").val();
	var params = "opcion=1&id_paciente=" + id_paciente +
				 "&id_plan=" + id_plan;
	
	var cant_registros = parseInt($("#hdd_cant_productos").val(), 10);
	cont_aux = 0;
	for (var i = 0; i < cant_registros; i++) {
		if (isObject(document.getElementById("tr_producto_" + i)) && $("#hdd_tipo_precio_" + i).val() == "P") {
			params += "&orden_" + cont_aux + "=" + i +
					  "&tipo_precio_" + cont_aux + "=" + $("#hdd_tipo_precio_" + i).val() +
					  "&cod_producto_" + cont_aux + "=" + $("#hdd_cod_servicio_" + i).val() +
					  "&tipo_bilateral_" + cont_aux + "=" + $("#hdd_tipo_bilateral_" + i).val() +
					  "&id_plan_" + cont_aux + "=" + id_plan +
					  "&valor_" + cont_aux + "=" + str_encode($("#txt_valor_" + i).val()) +
					  "&valor_cuota_" + cont_aux + "=" + str_encode($("#txt_valor_cuota_" + i).val()) +
					  "&cantidad_" + cont_aux + "=" + str_encode($("#txt_cantidad_" + i).val());
					  
			cont_aux++;
		}
	}
	params += "&cant_registros=" + cont_aux;
	
	llamarAjax("../funciones/LiquidadorPrecios_ajax.php", params, "d_liq_cx", "continuar_recalcular_precios_cx_cop_cm();");
}

function continuar_recalcular_precios_cx_cop_cm() {
	//Cirugías
	var cantidad_aux = parseInt($("#hdd_cantidad_cal_cx").val(), 10);
	for (var i = 0; i < cantidad_aux; i++) {
		var orden = $("#hdd_orden_cal_cx_" + i).val();
		var valor = Math.round($("#hdd_valor_cal_cx_" + i).val());
		
		$("#txt_valor_" + orden).val(valor);
		$("#td_valor_total_" + orden).html(valor * parseInt($("#txt_cantidad_" + orden).val(), 10));
	}
	
	//Copagos y cuotas moderadoras
	cantidad_aux = parseInt($("#hdd_cantidad_cop_cm").val(), 10);
	for (var i = 0; i < cantidad_aux; i++) {
		var orden = $("#hdd_orden_cop_cm_" + i).val();
		var valor = Math.round($("#hdd_valor_cop_cm_" + i).val());
		
		$("#txt_valor_cuota_" + orden).val(valor);
	}
	
	calcular_total_productos();
	registrar_cambios_totales();
}
