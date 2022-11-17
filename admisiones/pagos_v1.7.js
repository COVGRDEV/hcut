$(document).ready(function() {
	verifica();
});

//Verifica si hay datos enviados por post
function verifica() {
	var post = $("#post").val();
	var post_pago = $("#post_pago").val();

	if (post != 0 || post_pago != 0) {
		//Elimina el formulario de busqueda
		$("#buscar").remove();

		//Ejecuta la accion ajax que carga la información
		buscarPagos(1);
	}
}

//Funcion que valida el formulario de buscar usuario por nombre no numero de documento en la pagina principal pagos.php
function validarBuscarPagos() {
	$("#frmBuscarPagos").validate({
		debug: false,
		rules: {
			txt_paciente: {
				required: true,
			},
		},
		submitHandler: function(form) {
			buscarPagos(0);
			return false;
		}
	});
}

function buscarPagos(accion) {
	$("#contenedor").css("display", "block");
	var parametro = "";
	if (accion != 1) {
		parametro = str_encode($("#txt_paciente").val());
	}
	
	var params = "opcion=1&id_admision=" + str_encode($("#post").val()) +
				 "&parametro=" + parametro + "&ind_crear=0" +
				 "&id_pago=" + $("#post_pago").val();
	
	llamarAjax("pagos_ajax.php", params, "contenedor", "desabilitaComboBancos();");
}

//Esta funcion desabilita los combo box de los bancos
function desabilitaComboBancos() {
	$("#cmb_banco1").attr("disabled", "disabled");
	$("#cmb_banco21").attr("disabled", "disabled");
	$("#cmb_banco31").attr("disabled", "disabled");
}

//Esta función muestra la advertencia para registra el pago
//Acciones: 1: Crear - 2: Crear y registrar - 3: Solo modificar - 4: Registrar - 5: Editar registro existente
function validar_registrar_pago(id, tipo_accion) {
	$("#btnRegistrarpagos").attr("disabled", "disabled");
	$("#btnCrearPagos").attr("disabled", "disabled");
	$("#btnCrearRegPagos").attr("disabled", "disabled");

	var idElemento = id;
	var hdd_pagar = $("#hdd_pagar" + id).val();
	var hdd_cuota_pagar = $("#hdd_cuotaPagar").val();
	var hdd_pagar_aux = $("#hdd_pagar" + id + "_aux").val();
	var ind_tipo_pago = $("#hdd_idTipoPago").val();
	var ind_desc_cc = $("#hdd_descCC").val();
	
	$("#contenedor_error").css("display", "none");
	$("#cmb_lugar_cita" + id).removeClass("borde_error");
	$("#cmb_usuario_prof" + id).removeClass("borde_error");
	$("#cmb_convenio" + id).removeClass("borde_error");
	$("#cmb_plan" + id).removeClass("borde_error");
	$("#txt_factura").removeClass("borde_error");
	$("#txt_pedido").removeClass("borde_error");
	//$("#factura" + id).removeClass("borde_error");
	//$("#cmb_entidad" + id).removeClass("borde_error");
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

	var filas_aut = parseInt($("#hdd_cant_productos").val(), 10);
	for (i = 0; i < filas_aut; i++) {
		if (isObject(document.getElementById("hdd_tipo_precio_" + i))) {
			$("#txt_num_autorizacion_" + i).removeClass("borde_error")
		}
	}

	var cont_medios_pago = parseInt($("#hdd_cont_medios_pago").val(), 10);
	var cont_medios_pago_sel = 0;
	for (var i = 0; i < cont_medios_pago; i++) {
		$("#cmb_tipo_pago" + id + "_" + i).removeClass("borde_error");
		$("#tipoPago" + id + "_" + i).removeClass("borde_error");
		$("#cmb_banco" + id + "_" + i).removeClass("borde_error");
		$("#txt_num_cheque_" + i).removeClass("borde_error");
		$("#txt_num_cuenta_" + i).removeClass("borde_error");
		$("#txt_num_autoriza_" + i).removeClass("borde_error");
		$("#txt_ano_vence_" + i).removeClass("borde_error");
		$("#txt_mes_vence_" + i).removeClass("borde_error");
		$("#txt_referencia_" + i).removeClass("borde_error");
		$("#txt_fecha_consigna_" + i).removeClass("borde_error");
		$("#cmb_franquicia_tc_" + i).removeClass("borde_error");
		$("#cmb_usuario_autoriza" + id + "_" + i).removeClass("borde_error");
		if ($("#cmb_tipo_pago" + id + "_" + i).val() != "") {
			cont_medios_pago_sel++;
		}
		
		if ($("#hdd_cant_anticipos_" + i).length) {
			for (var j = 0; j < parseInt($("#hdd_cant_anticipos_" + i).val(), 10); j++) {
				$("#txt_valor_anticipo_" + i + "_" + j).removeClass("borde_error");
			}
		}
	}
    var num_filas_aux = 0;
	var num_filas = $("#tablaPrecios" + id + " tr").length;
     num_filas_aux = (num_filas-1);
		if (num_filas <= 1) {
		alert("Debe seleccionar por lo menos un producto");
		$("#btnRegistrarpagos").removeAttr("disabled");
		$("#btnCrearPagos").removeAttr("disabled");
		$("#btnCrearRegPagos").removeAttr("disabled");
		return;
	}
	if (tipo_accion != 1 && tipo_accion != 3 && parseInt(hdd_pagar_aux, 10) != parseInt(hdd_pagar, 10)) {
		if (ind_tipo_pago != "1" || parseFloat(hdd_cuota_pagar) <= parseFloat(hdd_pagar) || hdd_pagar_aux != hdd_cuota_pagar) {
			alert("Error. El valor ingresado en los tipos de pago debe ser igual al valor total a pagar.");
				//window.alert(num_filas_aux);
			$("#btnRegistrarpagos").removeAttr("disabled");
			$("#btnCrearPagos").removeAttr("disabled");
			$("#btnCrearRegPagos").removeAttr("disabled");
			return;
		}
	}
	if (tipo_accion != 1 && tipo_accion != 3 && cont_medios_pago_sel <= 0) {
		alert("Error. Debe seleccionar por lo menos un medio de pago.");
		$("#btnRegistrarpagos").removeAttr("disabled");
		$("#btnCrearPagos").removeAttr("disabled");
		$("#btnCrearRegPagos").removeAttr("disabled");
		return;
	}
	
		num_products_table = (num_filas-1);
		var num_productos_table_database = parseInt($("#hdd_cant_productos_aux").val(), 10);
		var estado_pago = parseInt($("#hdd_estado_pago").val(), 10);
	if(num_productos_table_database != num_products_table && estado_pago == 2){
		if(num_productos_table_database>num_products_table){
			alert("Error. El número de productos de este pago es menor a el registrado en SIESA, debe agregar " +(num_productos_table_database-num_products_table) + " productos");
			}else if (num_productos_table_database<num_products_table){
			alert("Error. El número de productos de este pago es mayor a el registrado en SIESA, debe eliminar " +(num_products_table-num_productos_table_database) + " productos");
			}
		$("#btnRegistrarpagos").removeAttr("disabled");
		$("#btnCrearPagos").removeAttr("disabled");
		$("#btnCrearRegPagos").removeAttr("disabled");
		return;
	}
	
	var ind_error = false;
	if ($("#cmb_lugar_cita" + id).val() == "") {
		$("#cmb_lugar_cita" + id).addClass("borde_error")
		ind_error = true;
	}
	if ($("#cmb_usuario_prof" + id).val() == "") {
		$("#cmb_usuario_prof" + id).addClass("borde_error")
		ind_error = true;
	}
	if ($("#cmb_convenio" + id).val() == "") {
		$("#cmb_convenio" + id).addClass("borde_error")
		ind_error = true;
	}
	if ($("#cmb_plan" + id).val() == "") {
		$("#cmb_plan" + id).addClass("borde_error")
		ind_error = true;
	}

	var ind_num_aut_aux = $("#hdd_ind_num_aut").val();
	var ind_num_aut_obl_aux = $("#hdd_ind_num_aut_obl").val();
	if (ind_num_aut_aux == "1" && ind_num_aut_obl_aux == "1" && parseInt(hdd_pagar, 10) != 0) {
		for (i = 0; i < filas_aut; i++) {
			if (isObject(document.getElementById("hdd_tipo_precio_" + i))) {
				if ($("#txt_num_autorizacion_" + i).val().length < 5) {
					$("#txt_num_autorizacion_" + i).addClass("borde_error")
					ind_error = true;
				}
			}
		}
	}
	if (tipo_accion != 1 && tipo_accion != 3) {
		/*if ($("#cmb_entidad" + id).val() == "") {
			$("#cmb_entidad" + id).addClass("borde_error")
			ind_error = true;
		}
		
		if ($("#factura" + id).val() == "" && $("#hdd_fact_obl").val() == "1" && $("#hdd_convenios_np").val().indexOf(";" + $("#cmb_convenio" + id).val() + ";") == -1 && hdd_pagar != "0") {
			$("#factura" + id).addClass("borde_error")
			ind_error = true;
		}*/
		
		for (var i = 0; i < cont_medios_pago; i++) {
			var id_tipo_pago_aux = $("#cmb_tipo_pago" + id + "_" + i).val();
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
				
				if (ind_banco_aux == "1" && $("#cmb_banco" + id + "_" + i).val() == "") {
					$("#cmb_banco" + id + "_" + i).addClass("borde_error")
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
				if (ind_usuario_aut_aux == "1" && $("#cmb_usuario_autoriza" + id + "_" + i).val() == "") {
					$("#cmb_usuario_autoriza" + id + "_" + i).addClass("borde_error")
					ind_error = true;
				}
				if ($("#tipoPago" + id + "_" + i).val() == "" ||
						(parseInt($("#tipoPago" + id + "_" + i).val(), 10) == 0 && $("#cmb_tipo_pago" + id + "_" + i).val() != "99")) {
					$("#tipoPago" + id + "_" + i).addClass("borde_error")
					ind_error = true;
				}
				if ($("#cmb_tipo_pago" + id + "_" + i).val() == "99" && $("#tipoPago" + id + "_" + i).val() != "0") {
					alert("Error. El valor de no pago debe ser cero (0).");
					$("#btnRegistrarpagos").removeAttr("disabled");
					$("#btnCrearPagos").removeAttr("disabled");
					$("#btnCrearRegPagos").removeAttr("disabled");
					return;
				}
				
				//Anticipos
				if ($("#hdd_cant_anticipos_" + i).length) {
					for (var j = 0; j < parseInt($("#hdd_cant_anticipos_" + i).val(), 10); j++) {
						if ($("#chk_anticipo_" + i + "_" + j).is(":checked")) {
							var valor_aux = parseInt("0" + $("#txt_valor_anticipo_" + i + "_" + j).val(), 10);
							var saldo_aux = parseInt("0" + $("#hdd_saldo_anticipo_" + i + "_" + j).val(), 10);
							if (valor_aux == 0 || valor_aux > saldo_aux) {
								$("#txt_valor_anticipo_" + i + "_" + j).addClass("borde_error");
								ind_error = true;
							}
						}
					}
				}
			} else if ($("#tipoPago" + id + "_" + i).val() != "" && parseInt($("#tipoPago" + id + "_" + i).val(), 10) > 0) {
				$("#cmb_tipo_pago" + id + "_" + i).addClass("borde_error")
				ind_error = true;
			}
		}
	}

	var ind_tercero_obl = parseInt($("#hdd_ind_tercero_obl").val(), 10);
	if ($("#hdd_id_tercero").val() == "" && ind_tercero_obl == "1" && hdd_pagar != "0") {
		$("#d_buscar_tercero").addClass("borde_error")
		ind_error = true;
	}
	
	if (tipo_accion != 3 && tipo_accion != 4 && tipo_accion != 5) {
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
	}

	if (!ind_error) {
		mostrar_formulario_flotante(1);
		$("#d_interno").html(
			'<table class="datagrid" border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
				'<tr class="headegrid">' +
					'<th align="center" class="msg_alerta" style="border: 1px solid #fff;">' +
						'<h3>&iquest;Est&aacute; seguro que desea registrar el pago?</h3>' +
					'</th>' +
				'</tr>' +
				'<tr>' +
					'<th id="th_registrar_pago" align="center" style="width:5%;border: 1px solid #fff;">' +
						'<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" value="Aceptar" class="btnPrincipal" onclick="RegistrarPago(' + idElemento + ', ' + tipo_accion + ');"/>' +
						'&nbsp;&nbsp;' +
						'<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" value="Cancelar" class="btnSecundario" onclick="cerrar_div_centro();"/>' +
					'</th>' +
				'</tr>' +
			'</table>');
		posicionarDivFlotante("d_centro");

		$("#btnRegistrarpagos").removeAttr("disabled");
		$("#btnCrearPagos").removeAttr("disabled");
		$("#btnCrearRegPagos").removeAttr("disabled");
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Los campos marcados en rojo son obligatorios");
		window.scrollTo(0, 0);

		$("#btnRegistrarpagos").removeAttr("disabled");
		$("#btnCrearPagos").removeAttr("disabled");
		$("#btnCrearRegPagos").removeAttr("disabled");
	}
}
var banderaClick=false;

function RegistrarPago(idContador, tipo_accion) {
	if (!banderaClick){ //Validar de que solo se pueda hacer un click
		banderaClick=true;
		$("#btn_cancelar_si").attr("disabled", "disabled");
		$("#th_registrar_pago").html('Realizando comunicaci&oacute;n externa...&nbsp;<img src="../imagenes/ajax-loader.gif" /><br /><br />');
		var params = "opcion=2&id_pago=" + $("#hdd_id_pago").val() +
					 "&tipo_accion=" + tipo_accion +
					 "&idAdmision=" + $("#hdd_admision").val() +
					 "&idPaciente=" + $("#hdd_idPaciente").val() +
					 "&idLugarCita=" + $("#cmb_lugar_cita" + idContador).val() +
					 "&idUsuarioProf=" + $("#cmb_usuario_prof" + idContador).val() +
					 "&idConvenio=" + $("#cmb_convenio" + idContador).val() +
					 "&idPlan=" + $("#hdd_idPlan").val() +
					 "&num_factura=" + str_encode($("#txt_factura").length ? $("#txt_factura").val() : "") +
					 "&num_pedido=" + str_encode($("#txt_pedido").length ? $("#txt_pedido").val() : "") +
					 //"&factura=" + str_encode($("#factura" + idContador).val()) +
					 //"&cmbEntidad=" + $("#cmb_entidad" + idContador).val() +
					 "&observaciones_pago=" + str_encode($("#txt_observaciones_pago").val()) +
					 "&idTerceroPago=" + $("#hdd_id_tercero").val();
		
		var cont_medios_pago = parseInt($("#hdd_cont_medios_pago").val(), 10);
		for (var i = 0; i < cont_medios_pago; i++) {
			var ind_cod_seguridad_aux = $("#hdd_tipo_pago_ind_cod_seguridad_" + $("#cmb_tipo_pago" + idContador + "_" + i).val()).val();
			params += "&tipoPago" + i + "=" + $("#cmb_tipo_pago" + idContador + "_" + i).val() +
					  "&bancoPago" + i + "=" + $("#cmb_banco" + idContador + "_" + i).val() +
					  "&numCheque" + i + "=" + $("#txt_num_cheque_" + i).val() +
					  "&numCuenta" + i + "=" + $("#txt_num_cuenta_" + i).val() +
					  "&codSeguridad" + i + "=" + (ind_cod_seguridad_aux == "1" ? "999" : "") +
					  "&numAutoriza" + i + "=" + $("#txt_num_autoriza_" + i).val() +
					  "&anoVence" + i + "=" + $("#txt_ano_vence_" + i).val() +
					  "&mesVence" + i + "=" + $("#txt_mes_vence_" + i).val() +
					  "&referencia" + i + "=" + $("#txt_referencia_" + i).val() +
					  "&fechaConsigna" + i + "=" + $("#txt_fecha_consigna_" + i).val() +
					  "&idFranquiciaTC" + i + "=" + $("#cmb_franquicia_tc_" + i).val() +
					  "&valorPago" + i + "=" + $("#tipoPago" + idContador + "_" + i).val() +
					  "&IdUsuarioAutoriza" + i + "=" + $("#cmb_usuario_autoriza" + idContador + "_" + i).val();
			
			var cont_anticipos_aux = 0;
			if ($("#hdd_cant_anticipos_" + i).length) {
				for (var j = 0; j < $("#hdd_cant_anticipos_" + i).val(); j++) {
					if ($("#chk_anticipo_" + i + "_" + j).is(":checked")) {
						params += "&id_anticipo_" + i + "_" + cont_anticipos_aux + "=" + $("#hdd_id_anticipo_" + i + "_" + j).val() +
								  "&valor_anticipo_" + i + "_" + cont_anticipos_aux + "=" + $("#txt_valor_anticipo_" + i + "_" + j).val();
						
						cont_anticipos_aux++;
					}
				}
			}
			params += "&cant_anticipos_" + i + "=" + cont_anticipos_aux;
		}
		params += "&cant_medios_pago=" + cont_medios_pago;
		
		var cont_productos = 0;
		var filas = parseInt($("#hdd_cant_productos").val(), 10);
		for (i = 0; i < filas; i++) {
			if (isObject(document.getElementById("hdd_tipo_precio_" + i))) {
				params += "&id_detalle_precio_" + cont_productos + "=" + $("#hdd_detalle_precio_" + i).val() +
						  "&cod_servicio_" + cont_productos + "=" + $("#hdd_cod_servicio_" + i).val() +
						  "&tipo_precio_" + cont_productos + "=" + $("#hdd_tipo_precio_" + i).val() +
						  "&tipo_bilateral_" + cont_productos + "=" + $("#hdd_tipo_bilateral_" + i).val() +
						  "&num_autorizacion_" + cont_productos + "=" + str_encode($("#txt_num_autorizacion_" + i).val()) +
						  "&cantidad_" + cont_productos + "=" + $("#txt_cantidad_" + i).val() +
						  "&valor_" + cont_productos + "=" + $("#txt_valor_" + i).val() +
						  "&valor_cuota_" + cont_productos + "=" + $("#txt_valor_cuota_" + i).val();

				cont_productos++;
			}
		}
		params += "&cant_productos=" + cont_productos;

		if (tipo_accion == 1 || tipo_accion == 2) {
			params += "&id_tipo_documento=" + $("#cmb_tipo_documento").val() +
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
		}
		
		if (cont_productos > 0) {
			llamarAjax("pagos_ajax.php", params, "d_resultado", "verificaRegistrarPago(" + tipo_accion + ");");
		} else {
			alert("Error interno al tratar de registrar los productos, por favor contacte al \xe1rea de sistemas.");
			$("#btn_cancelar_si").removeAttr("disabled");
		}
	}
}

//Funcion que verifica el guardado de Registrar pago
function verificaRegistrarPago(tipo_accion) {
	banderaClick=false;
	var resultado_aux = $("#hdd_id_paciente_resul").val();
	
	if (resultado_aux > 0) {
		resultado_aux = $("#hdd_id_pago_resul").val();

		if (resultado_aux > 0) {
			$("#hdd_id_pago").val(resultado_aux);
			mostrar_formulario_flotante(0);
			$("#contenedor_error").css("display", "none");
			$("#contenedor_exito").css("display", "block");
			if (tipo_accion == 1) {
				$("#contenedor_exito").html("Pago creado con &eacute;xito");
			} else if (tipo_accion == 3) {
				$("#contenedor_exito").html("Pago modificado con &eacute;xito");
			} else {
				$("#contenedor_exito").html("Pago registrado con &eacute;xito");
			}
			$("#contenedor").css("display", "none");
			$("#txt_paciente").val("");
			if ($("#hdd_num_factura_siesa").length) {
				$("#lb_num_factura").html($("#hdd_num_factura_siesa").val());
			}
			if ($("#hdd_num_pedido_siesa").length) {
				$("#lb_num_pedido").html($("#hdd_num_pedido_siesa").val());
			}
			/*if ($("#hdd_factura_resul").val() != "") {
				$("#factura1").val($("#hdd_factura_resul").val());
			}*/
			
			//Se abre el archivo del recibo de pago
			if (tipo_accion == 1) {
				imprimir_recibo();
			}

			//Direcciona a estado atencion
			if ($("#post").val() != "0" || $("#post_pago").val() != "0") {
				setTimeout(function() {
					enviar_credencial("../admisiones/estado_atencion.php");
				}, 1000);
			} else {
				setTimeout(function() {
					$("#contenedor_exito").css("display", "none");
				}, 2000);
			}
		} else if (resultado_aux == "0") {
			mostrar_formulario_flotante(0);
			$("#contenedor_error").css("display", "block");
			$("#contenedor_error").html("Error - No se encontraros profesionales disponibles para continuar con la atenci\xf3n");
		} else {
			mostrar_formulario_flotante(0);
			
			var bol_error_prev = false;
			var mensaje_error_prev = "Error -";
			if ($("#hdd_resul_pedido_ant_siesa").length && $("#hdd_resul_pedido_ant_siesa").val() != "1") {
				bol_error_prev = true;
				mensaje_error_prev += " Se encontr\xf3 un pedido activo con n\xfamero <b>" + $("#hdd_num_pedido_ant_siesa").val() + "</b> asociado al pago.";
			}
			if ($("#hdd_resul_factura_ant_siesa").length && $("#hdd_resul_factura_ant_siesa").val() != "1") {
				bol_error_prev = true;
				mensaje_error_prev += " Se encontr\xf3 una factura activa con n\xfamero <b>" + $("#hdd_num_factura_ant_siesa").val() + "</b> asociada al pago.";
			}
			
			if (bol_error_prev) {
				$("#contenedor_error").html(mensaje_error_prev);
			} else {
				if ($("#hdd_resul_tercero_siesa").length && $("#hdd_resul_tercero_siesa").val() != "1") {
					$("#contenedor_error").html("Error en el registro externo del tercero del pago:<br />" + $("#hdd_mensaje_tercero_siesa").val());
				} else {
					if ($("#hdd_resul_tercero_entidad_siesa").length && $("#hdd_resul_tercero_entidad_siesa").val() != "1") {
						$("#contenedor_error").html("Error al verificar la existencia externa de la entidad como tercero");
					} else {
						if ($("#hdd_resul_factura_siesa").length && $("#hdd_resul_factura_siesa").val() != "1") {
							$("#contenedor_error").html("Error en el registro externo de la factura asociada al pago:<br />" + $("#hdd_mensaje_factura_siesa").val());
						} else {
							if ($("#hdd_resul_pedido_siesa").length && $("#hdd_resul_pedido_siesa").val() != "1") {
								$("#contenedor_error").html("Error en el registro externo del pedido asociado al pago:<br />" + $("#hdd_mensaje_pedido_siesa").val());
							} else {
								$("#contenedor_error").html("Error interno al tratar de registrar el pago");
							}
						}
					}
				}
			}
			$("#contenedor_error").css("display", "block");
		}
	} else if (resultado_aux == "-2") {
		mostrar_formulario_flotante(0);
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error - El n\xfamero de documento ya se encuentra asignado a otro paciente");
	} else if (resultado_aux == "-3" || resultado_aux == "-4" || resultado_aux == "-5") {
		mostrar_formulario_flotante(0);
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error interno al tratar de registrar los datos de detalle del pago (" + resultado_aux + ")");
	/*} else if (resultado_aux == "-6") {
		mostrar_formulario_flotante(0);
		$("#factura1").addClass("borde_error")
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error - El formato de n&uacute;mero de factura no es v&aacute;lido");*/
	} else if (resultado_aux == "-7") {
		mostrar_formulario_flotante(0);
		$("#factura1").addClass("borde_error")
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error - Los valores de anticipos presentan inconsistencias");
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
function tipoPago(idcontador, indice, id_pago, estado_pago) {
	if ($("#cmb_tipo_pago" + idcontador + "_" + indice).val() != "") { //Si el valor es seleccionado
		var id_tipo = $("#cmb_tipo_pago" + idcontador + "_" + indice).val();
		
		//Verifica que el valor no ha sido seleccionado por otro combo box
		var cont_medios_pago = parseInt($("#hdd_cont_medios_pago").val(), 10);
		var ind_repetido_aux = false;
		for (var i = 0; i < cont_medios_pago; i++) {
			if (i != indice && $("#cmb_tipo_pago" + idcontador + "_" + i).val() == id_tipo) {
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
				if (i != indice && $("#cmb_tipo_pago" + idcontador + "_" + i).val() == id_tipo_rel) {
					ind_tipo_rel = true;
					break;
				}
			}
		}
		
		if (!ind_repetido_aux && ind_tipo_rel) {
			if (id_tipo == "99") { //No pago
				$("#tipoPago" + idcontador + "_" + indice).val(0);
				$("#tipoPago" + idcontador + "_" + indice).attr("disabled", "disabled");
			} else {
				$("#tipoPago" + idcontador + "_" + indice).removeAttr("disabled");
			}
			procesarTipoPago(idcontador, indice); //Hace la sumatoria de nuevo
			
			//Se verifica si se trata de un tipo de pago negativo
			var ind_negativo = parseInt($("#hdd_tipo_pago_negativo_" + id_tipo).val(), 10);
			if (ind_negativo) {
				$("#tipoPago" + idcontador + "_" + indice).addClass("texto-rojo");
			} else {
				$("#tipoPago" + idcontador + "_" + indice).removeClass("texto-rojo");
			}
			
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
			var id_tipo_concepto = $("#hdd_tipo_pago_id_tipo_concepto_" + id_tipo).val();
			
			if (ind_banco == "1") {
				$("#d_banco_mp_" + indice).show();
			} else {
				$("#cmb_banco" + idcontador + "_" + indice).val("");
				$("#d_banco_mp_" + indice).hide();
			}
			if (ind_usuario_aut == "1") {
				$("#cmb_usuario_autoriza" + idcontador + "_" + indice).removeAttr("disabled");
			} else {
				$("#cmb_usuario_autoriza" + idcontador + "_" + indice).attr("disabled", true);
				$("#cmb_usuario_autoriza" + idcontador + "_" + indice).val("");
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
			if (id_tipo_concepto == "479") {
				$("#tipoPago" + idcontador + "_" + indice).attr("disabled", "disabled");
				
				//Se cargan los anticipos con saldo para el paciente y tercero
				var params = "opcion=14&indice=" + indice +
							 "&id_paciente=" + $("#hdd_idPaciente").val() +
							 "&id_tercero=" + $("#hdd_id_tercero").val() +
							 "&id_pago=" + id_pago +
							 "&estado_pago=" + estado_pago;
				
				llamarAjax("pagos_ajax.php", params, "d_anticipos_mp_" + indice, "");
				
				$("#d_anticipos_mp_" + indice).show();
				
				if (estado_pago == "1") {
					//pago pendiente, se marca el valor como cero
					$("#tipoPago" + idcontador + "_" + indice).val(0);
				}
			} else {
				$("#d_anticipos_mp_" + indice).html("");
				$("#d_anticipos_mp_" + indice).hide();
			}
		} else if (ind_repetido_aux) {
			alert("Error. El tipo de pago ya ha sido seleccionado.");
			$("#cmb_tipo_pago" + idcontador + "_" + indice).val("");
		} else {
			alert("Error. Este tipo de pago no puede ser seleccionado.");
			$("#cmb_tipo_pago" + idcontador + "_" + indice).val("");
		}
	} else {
		$("#cmb_banco" + idcontador + "_" + indice).val("");
		$("#cmb_banco" + idcontador + "_" + indice).attr("disabled", "disabled");
		$("#tipoPago" + idcontador + "_" + indice).val("0");
		$("#tipoPago" + idcontador + "_" + indice).attr("disabled", "disabled");
		procesarTipoPago(idcontador, indice);
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

function seleccionarBanco(idValor) {
	$("#" + idValor).attr("disabled", false);//Habilita el input
}

//Funcion que valida el formulario de tipos de pago
function procesarTipoPago(idcontador, indice) {
	var valorPagar = parseInt($("#hdd_pagar" + idcontador).val(), 10);

	calcular_total_medios(true);
}

function calcular_total_medios(ind_actualizar) {
	var idcontador = 1;
	var cont_medios_pago = parseInt($("#hdd_cont_medios_pago").val(), 10);
	var total_aux = 0;
	for (var i = 0; i < cont_medios_pago; i++) {
		var id_tipo = $("#cmb_tipo_pago" + idcontador + "_" + i).val();
		if ($("#hdd_tipo_pago_negativo_" + id_tipo).val() == "1") {
			total_aux -= parseInt($("#tipoPago" + idcontador + "_" + i).val(), 10);
		} else {
			total_aux += parseInt($("#tipoPago" + idcontador + "_" + i).val(), 10);
		}
	}

	if (ind_actualizar) {
		$("#tipoPagoTotalPagar").text(total_aux.formatoNumerico());
		$("#hdd_pagar" + idcontador + "_aux").val(total_aux);
	}

	return total_aux;
}

function mostrar_agregar_servicio(idcontador) {
	var params = "opcion=4&idcontador=" + idcontador;
	llamarAjax("pagos_ajax.php", params, "d_interno_servicios" + idcontador + "", "muestraFormularioFlotante(1, " + idcontador + ");");
}

function muestraFormularioFlotante(tipo, idcontador) {
	if (tipo == 1) { //mostrar
		$("#fondo_negro_servicios" + idcontador).css("display", "block");
		$("#d_centro_servicios" + idcontador).slideDown(400).css("display", "block");

		posicionarDivFlotante("d_centro_servicios" + idcontador);
	} else if (tipo == 0) { //Ocultar
		$("#fondo_negro_servicios" + idcontador).css("display", "none");
		$("#d_centro_servicios" + idcontador).slideDown(400).css("display", "none");
	}
}

//Muestra la tabla con los procedimientos para el plan
function buscarProcedimientos(tipoBusqueda, idContador) {
	if (tipoBusqueda == "1") {
		validarFormularioProcedimientos(idContador);
	} else if (tipoBusqueda == "0") {
		var idPlan = $("#a" + idContador + " #hdd_idPlan").val();
		var tipoPago = $("#a" + idContador + " #hdd_idTipoPago").val();
		var parametroPrecios = "";
		$("#txtParametroPrecios").val("");//Limpia la caja de texto

		var params = "opcion=5&idPlan=" + idPlan + "&parametroPrecios=" + parametroPrecios + "&tipoPago=" + tipoPago + "&idContador=" + idContador;
		llamarAjax("pagos_ajax.php", params, "resultadoProcedimientos", "");
	}
}

function validarFormularioProcedimientos(idContador) {
	$("#frmListadoPrecios").validate({
		rules: {
			txtParametroPrecios: {
				required: true,
			},
		},
		submitHandler: function() {

			var idPlan = $("#a" + idContador + " #hdd_idPlan").val();
			var tipoPago = $("#a" + idContador + " #hdd_idTipoPago").val();
			var parametroPrecios = str_encode($("#txtParametroPrecios").val());

			var params = "opcion=5&idPlan=" + idPlan + "&parametroPrecios=" + parametroPrecios + "&tipoPago=" + tipoPago + "&idContador=" + idContador;
			llamarAjax("pagos_ajax.php", params, "resultadoProcedimientos", "");

			return false;
		},
	});
}

function agregar_servicio(codigo, tipo, nombre, precio, precioCuota, tipoPrecio, tipoBilateral, nombreBilateral, ind_tipo_pago, idContador, indice_b) {
	//se obtiene el indice del nuevo registro
	var indice = parseInt($("#hdd_cant_productos").val(), 10);
	
	var disabled_aux = "";
	if (ind_tipo_pago != "1") {
		disabled_aux = 'disabled="disabled"';
	}
	
	var ind_num_aut = $("#hdd_ind_num_aut").val();
	var disabled_num_aut = "";
	if (ind_num_aut != "1") {
		disabled_num_aut = 'disabled="disabled"';
	}
	
	if (tipoPrecio != "Q") {
		$("#tablaPrecios" + idContador).append(
			'<tr id="tr_producto_' + indice + '">' +
				'<td align="center">' +
					'<input type="hidden" name="hdd_detalle_precio_' + indice + '" id="hdd_detalle_precio_' + indice + '" value="0" />' +
					'<input type="hidden" name="hdd_tipo_precio_' + indice + '" id="hdd_tipo_precio_' + indice + '" value="' + tipoPrecio + '" />' +
					'<input type="hidden" name="hdd_cod_servicio_' + indice + '" id="hdd_cod_servicio_' + indice + '" value="' + codigo + '" />' +
					'<input type="hidden" name="hdd_tipo_bilateral_' + indice + '" id="hdd_tipo_bilateral_' + indice + '" value="' + tipoBilateral + '" />' +
					codigo +
				'</td>' +
				'<td align="left">' +
					nombre +
				'</td>' +
				'<td align="center">' +
					nombreBilateral +
				'</td>' +
				'<td align="left">' +
					'<input type="text" name="txt_num_autorizacion_' + indice + '" id="txt_num_autorizacion_' + indice + '" value="" maxlength="20" onblur="trim_cadena(this);" class="no-margin" style="font-size:12pt;" ' + disabled_num_aut + ' />' +
				'</td>' +
				'<td>' +
					'<input type="text" style="text-align:center; margin:0; font-size:12pt;" id="txt_cantidad_' + indice + '" name="txt_cantidad_' + indice + '" value="1" onkeypress="solo_numeros(event, false);" onblur="validar_cantidades(' + indice + ', 2); registrar_cambios_totales();" />' +
				'</td>' +
				'<td>' +
					'<input type="text" style="text-align:right; margin:0; font-size:12pt;" id="txt_valor_' + indice + '" name="txt_valor_' + indice + '" value="' + precio + '" onkeypress="solo_numeros(event, false);" onblur="validar_cantidades(' + indice + ', 1); registrar_cambios_totales();" />' +
				'</td>' +
				'<td id="td_valor_total_' + indice + '">' +
					precio +
				'</td>' +
				'<td>' +
					'<input type="text" style="text-align:right; margin:0; font-size:12pt;" id="txt_valor_cuota_' + indice + '" name="txt_valor_cuota_' + indice + '" value="' + precioCuota + '" onkeypress="solo_numeros(event, false);" onBlur="registrar_cambios_totales();" ' + disabled_aux + ' />' +
				'</td>' +
				'<td>' +
					'<img src="../imagenes/icon-error.png" onclick="eliminar_producto(' + indice + ');" />' +
				'</td>' +
			'</tr>'
		);
		
		indice++;
	} else {
		//Paquete
		var cantidad_b_det = parseInt($("#hdd_cantidad_b_det_" + indice_b).val(), 10);
		
		for (j = 0; j < cantidad_b_det; j++) {
			$("#tablaPrecios" + idContador).append(
				'<tr id="tr_producto_' + indice + '">' +
					'<td align="center">' +
						'<input type="hidden" name="hdd_detalle_precio_' + indice + '" id="hdd_detalle_precio_' + indice + '" value="0" />' +
						'<input type="hidden" name="hdd_tipo_precio_' + indice + '" id="hdd_tipo_precio_' + indice + '" value="' + $("#hdd_tipo_precio_b_det_" + indice_b + "_" + j).val() + '" />' +
						'<input type="hidden" name="hdd_cod_servicio_' + indice + '" id="hdd_cod_servicio_' + indice + '" value="' + $("#hdd_cod_servicio_b_det_" + indice_b + "_" + j).val() + '" />' +
						'<input type="hidden" name="hdd_tipo_bilateral_' + indice + '" id="hdd_tipo_bilateral_' + indice + '" value="' + $("#hdd_tipo_bilateral_b_det_" + indice_b + "_" + j).val() + '" />' +
						$("#hdd_cod_servicio_b_det_" + indice_b + "_" + j).val() +
					'</td>' +
					'<td align="left">' +
						$("#hdd_nombre_servicio_b_det_" + indice_b + "_" + j).val() +
					'</td>' +
					'<td align="center">' +
						$("#hdd_nombre_bilateral_b_det_" + indice_b + "_" + j).val() +
					'</td>' +
					'<td align="left">' +
						'<input type="text" name="txt_num_autorizacion_' + indice + '" id="txt_num_autorizacion_' + indice + '" value="" maxlength="20" onblur="trim_cadena(this);" class="no-margin" style="font-size:12pt;" ' + disabled_num_aut + ' />' +
					'</td>' +
					'<td>' +
						'<input type="text" style="text-align:center; margin:0; font-size:12pt;" id="txt_cantidad_' + indice + '" name="txt_cantidad_' + indice + '" value="1" onkeypress="solo_numeros(event, false);" onblur="validar_cantidades(' + indice + ', 2); registrar_cambios_totales();" />' +
					'</td>' +
					'<td>' +
						'<input type="text" style="text-align:right; margin:0; font-size:12pt;" id="txt_valor_' + indice + '" name="txt_valor_' + indice + '" value="' + $("#hdd_valor_b_det_" + indice_b + "_" + j).val() + '" onkeypress="solo_numeros(event, false);" onblur="validar_cantidades(' + indice + ', 1); registrar_cambios_totales();" />' +
					'</td>' +
					'<td id="td_valor_total_' + indice + '">' +
						$("#hdd_valor_b_det_" + indice_b + "_" + j).val() +
					'</td>' +
					'<td>' +
						'<input type="text" style="text-align:right; margin:0; font-size:12pt;" id="txt_valor_cuota_' + indice + '" name="txt_valor_cuota_' + indice + '" value="' + $("#hdd_valor_cuota_b_det_" + indice_b + "_" + j).val() + '" onkeypress="solo_numeros(event, false);" onBlur="registrar_cambios_totales();" ' + disabled_aux + ' />' +
					'</td>' +
					'<td>' +
						'<img src="../imagenes/icon-error.png" onclick="eliminar_producto(' + indice + ');" />' +
					'</td>' +
				'</tr>'
			);
			
			indice++;
		}
	}
	
	//Se cierra el div
	muestraFormularioFlotante(0, 1);
	
	$("#hdd_cant_productos").val(indice);
	
	//Se calcula el valor total
	//calcular_total_productos();
	recalcular_precios_cx_cop_cm();
}

//Funcion que calcula la sumatoria de los procedimientos agregados
function validar_cantidades(indice, tipo) {
	var valor_aux = 0
	var cantidad_aux = 0
	try {
		valor_aux = parseInt($("#txt_valor_" + indice).val(), 10);
	} catch (err) {
		valor_aux = 0;
	}
	try {
		cantidad_aux = parseInt($("#txt_cantidad_" + indice).val(), 10);
	} catch (err) {
		cantidad_aux = 0;
	}

	if (tipo == 2 && cantidad_aux == 0) { //Si el evento se ejecuta en el input de cantidad
		alert("La cantidad debe ser mayor a cero (0)");
		cantidad_aux = 1;
		$("#txt_cantidad_" + indice).val(cantidad_aux);
	}

	//Se calcula el valor total del registro
	var total_aux = valor_aux * cantidad_aux;
	$("#td_valor_total_" + indice).html("$" + total_aux);

	//Se calcula el valor total
	calcular_total_productos();
}

//Funcion que eliminar el procedimiento
function eliminar_producto(indice) {
	$("#tr_producto_" + indice).remove();
	
	recalcular_precios_cx_cop_cm();
}

function calcular_total_productos() {
	var cant_productos = parseInt($("#hdd_cant_productos").val(), 10);
	var total_aux = 0;
	var total_cuota_aux = 0;
	for (var i = 0; i < cant_productos; i++) {
		if (isObject(document.getElementById("hdd_tipo_precio_" + i))) {
			var valor_aux = 0
			var valor_cuota_aux = 0
			var cantidad_aux = 0
			try {
				valor_aux = parseInt($("#txt_valor_" + i).val(), 10);
			} catch (err) {
				valor_aux = 0;
			}
			try {
				valor_cuota_aux = parseInt($("#txt_valor_cuota_" + i).val(), 10);
			} catch (err) {
				valor_cuota_aux = 0;
			}
			try {
				cantidad_aux = parseInt($("#txt_cantidad_" + i).val(), 10);
			} catch (err) {
				cantidad_aux = 0;
			}

			total_aux += (valor_aux * cantidad_aux);
			total_cuota_aux += valor_cuota_aux;
		}
	}
	
	$("#sp_total_pagar").html(total_aux.formatoNumerico());
	$("#sp_total_cuota").html(total_cuota_aux.formatoNumerico());
	$("#sp_total_diferencia").html((total_aux - total_cuota_aux).formatoNumerico());
	$("#hdd_pagar1").val(total_aux);
	$("#hdd_cuotaPagar").val(total_cuota_aux);
}

//Funcion que hace todo el proceso cuando se cambia de plan
function cambiarPlan(identificador, indice) {
	var id_plan = $("#" + identificador + "").val();
	
	if (id_plan != "") {
		$("#a" + indice + " #hdd_idPlan").val(id_plan); //Asigna rl valor del nuevo plan seleccionado
		var num_filas = $("#tablaPrecios" + indice + " tr").length;
		
		var params = "opcion=7&id_plan=" + id_plan +
					 "&fecha_pago=" + $("#hdd_fecha_pago").val() +
					 "&cant_productos=" + (num_filas - 1);
		
		//Se agregan los datos de los productos
		for (var i = 1; i < num_filas; i++) {
			//Se obtiene el id del tr
			var tr_id = $.trim($("#tablaPrecios" + indice + " tr:eq(" + i + ")").attr("id"));
			var arr_aux = tr_id.split("_");
			var indice_aux = arr_aux[2];
			params += "&cod_producto_" + i + "=" + $("#hdd_cod_servicio_" + indice_aux).val() +
					  "&tipo_precio_" + i + "=" + $("#hdd_tipo_precio_" + indice_aux).val() +
					  "&tipo_bilateral_" + i + "=" + $("#hdd_tipo_bilateral_" + indice_aux).val();
		}
		
		llamarAjax("pagos_ajax.php", params, "d_cambiar_plan", "postCambiarPlan(" + indice + ");");
	}
}

//Funcion que se ejecuta despues de cambiar el plan
function postCambiarPlan(indice) {
	var id_tipo_pago_val = $("#hdd_tipo_pago_val").val()
	var desc_cc_val = $("#hdd_desc_cc_val").val()
	$("#hdd_idTipoPago").val(id_tipo_pago_val);
	$("#hdd_descCC").val(desc_cc_val);
	
	var cadena_precios = $("#hdd_cadena_precios_val").val();
	var arr_precios = cadena_precios.split("|");
	var resultado_aux = "";
	
	//Se recorre la tabla actual y la compara con los resultados
	var num_filas = $("#tablaPrecios" + indice + " tr").length;
	var valor_total = 0;
	var valor_cuota = 0;
	for (i = 1; i < num_filas; i++) {
		var tr_id = $.trim($("#tablaPrecios" + indice + " tr:eq(" + i + ")").attr("id"));
		var arr_aux = tr_id.split("_");
		var indice_aux = arr_aux[2];
		
		var cod_servicio_aux = $("#hdd_cod_servicio_" + indice_aux).val();
		var tipo_precio_aux = $("#hdd_tipo_precio_" + indice_aux).val();
		var tipo_bilateral_aux = $("#hdd_tipo_bilateral_" + indice_aux).val();
		
		var ind_hallado_aux = false;
		for (j = 0; j < arr_precios.length; j++) {
			var arr_resul_aux = arr_precios[j].split(":");
			
			if (cod_servicio_aux == arr_resul_aux[0] && tipo_precio_aux == arr_resul_aux[1] && tipo_bilateral_aux == arr_resul_aux[2]) {
				$("#txt_valor_" + indice_aux).val(arr_resul_aux[3]);
				$("#td_valor_total_" + indice_aux).html(parseInt(arr_resul_aux[3], 10) * parseInt("0" + $("#txt_cantidad_" + indice_aux).val(), 10));
				ind_hallado_aux = true;
				break;
			}
		}
		
		if (!ind_hallado_aux) {
			$("#txt_valor_" + indice_aux).val("0");
			$("#td_valor_total_" + indice_aux).html("0");
		}
		
		//Se habilita o inhabilita la celda de cuota moderadora
		if (id_tipo_pago_val == "1") {
			$("#txt_valor_cuota_" + indice_aux).removeAttr("disabled");
		} else {
			$("#txt_valor_cuota_" + indice_aux).attr("disabled", "disabled");
			$("#txt_valor_cuota_" + indice_aux).val("0");
		}
	}
	
	recalcular_precios_cx_cop_cm();
}

function seleccionar_paciente(id_pago) {
	var params = "opcion=1&id_admision=0" +
				 "&parametro=&ind_crear=0" +
				 "&id_pago=" + id_pago;
	
	llamarAjax("pagos_ajax.php", params, "contenedor", "");
}

function cambiarConvenio(idContador) {
	var idConvenio = $("#cmb_convenio" + idContador).val();
	var params = "opcion=8&idConvenio=" + str_encode(idConvenio) + "&idContador=" + idContador;

	llamarAjax("pagos_ajax.php", params, "comboPlan", "actualizar_ind_num_aut();");
}

function actualizar_ind_num_aut() {
	$("#hdd_ind_num_aut").val($("#hdd_ind_num_aut_aux").val());
	$("#hdd_ind_num_aut_obl").val($("#hdd_ind_num_aut_obl_aux").val());

	var cant_productos = parseInt($("#hdd_cant_productos").val(), 10);
	if ($("#hdd_ind_num_aut").val() == "1") {
		for (var i = 0; i < cant_productos; i++) {
			$("#txt_num_autorizacion_" + i).removeAttr("disabled");
		}
	} else {
		for (var i = 0; i < cant_productos; i++) {
			$("#txt_num_autorizacion_" + i).val("");
			$("#txt_num_autorizacion_" + i).attr("disabled", "disabled");
		}
	}
}

function mostrar_crear_pago() {
	var params = "opcion=1&id_admision=0&parametro=&ind_crear=1&id_pago=0";

	llamarAjax("pagos_ajax.php", params, "contenedor", "");
	$("#contenedor").css("display", "block");
}

function seleccionar_pais(id_pais) {
	if (id_pais == "1") {
		$("#d_departamento").css("display", "block");
		$("#d_municipios").css("display", "block");
		$("#d_nombre_dep").css("display", "none");
		$("#d_nombre_mun").css("display", "none");
	} else {
		$("#d_departamento").css("display", "none");
		$("#d_municipios").css("display", "none");
		$("#d_nombre_dep").css("display", "block");
		$("#d_nombre_mun").css("display", "block");
	}
}

function seleccionar_departamento(cod_dep, cod_mun) {
	var params = "opcion=9&cod_dep=" + cod_dep + "&cod_mun=" + cod_mun;

	llamarAjax("pagos_ajax.php", params, "d_municipios", "");
}

function verificar_paciente(numero_documento) {
	var params = "opcion=10&numero_documento=" + numero_documento +
				 "&id_tipo_documento=" + $("#cmb_tipo_documento").val();
	
	llamarAjax("pagos_ajax.php", params, "d_val_paciente", "preguntar_cargar_paciente(\"" + numero_documento + "\");");
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
	$("#hdd_idPaciente").val($("#hdd_id_paciente_b").val());
	$("#hdd_edadPaciente").val($("#hdd_edad_b").val());
	$("#sp_edad").html($("#hdd_edad_b").val() + " a&ntilde;os");
	seleccionar_pais($("#hdd_id_pais_b").val());
	seleccionar_departamento($("#hdd_cod_dep_b").val(), $("#hdd_cod_mun_b").val());
	validar_mostrar_datos_tercero(1);

	cerrar_div_centro();
}

function imprimir_recibo() {
	var params = "opcion=1&id_pago=" + $("#hdd_id_pago").val() + "&id_admision=0";

	llamarAjax("recibo_pago_ajax.php", params, "d_imprimir_recibo", "mostrar_recibo();");
}

function mostrar_recibo() {
	if ($("#hdd_ruta_arch_pdf_0").length) {
		var ruta = $("#hdd_ruta_arch_pdf_0").val();
		window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=recibo_pago.pdf", "_blank");
	}
}

function borrar_pago() {
	var ind_continuar = true;
	$("#txt_observaciones_pago").removeClass("borde_error");
	$("#cmb_causales_devolucion").removeClass("borde_error");
	
	if (trim($("#txt_observaciones_pago").val()) == "") {
		$("#txt_observaciones_pago").addClass("borde_error");
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
						'<h3>&iquest;Est&aacute; seguro de querer anular el pago?</h3>' +
					'</th>' +
				'</tr>' +
				'<tr>' +
					'<th id="th_anualar_pago" align="center" style="width:5%;border: 1px solid #fff;">' +
						'<input type="button" id="btn_cancelar_si_b" nombre="btn_cancelar_si_b" value="Aceptar" class="btnPrincipal" onclick="continuar_borrar_pago();"/>' +
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

function continuar_borrar_pago() {
	$("#btn_cancelar_si_b").attr("disabled", "disabled");
	$("#th_anualar_pago").html('Realizando comunicaci&oacute;n externa...&nbsp;<img src="../imagenes/ajax-loader.gif" /><br /><br />');
	
	var params = "opcion=11&id_pago=" + $("#hdd_id_pago").val() +
				 "&observaciones_pago=" + str_encode($("#txt_observaciones_pago").val()) +
				 "&id_causal_borra=" + $("#cmb_causales_devolucion").val();
	
	llamarAjax("pagos_ajax.php", params, "d_resultado", "validar_borrar_pago();");
}

function validar_borrar_pago() {
	var resultado_aux = parseInt($("#hdd_resul_borrar").val(), 10);

	if (resultado_aux > 0) {
		var texto_adic_aux;
		if ($("#hdd_num_pedido_nota_credito_siesa").length && $("#hdd_num_pedido_nota_credito_siesa").val() != "") {
			texto_adic_aux = "<br /><b>Por favor anule manualmente el pedido relacionado n&uacute;mero " + $("#hdd_num_pedido_nota_credito_siesa").val() + ".</b>";
		} else {
			texto_adic_aux = "";
		}
		
		mostrar_formulario_flotante(0);
		$("#contenedor_error").css("display", "none");
		$("#contenedor_exito").css("display", "block");
		$("#contenedor_exito").html("El pago ha sido anulado." + texto_adic_aux);
		$("#contenedor").css("display", "none");
		$("#txt_paciente").val("");
		
		//setTimeout(function() { $("#contenedor_exito").css("display", "none"); }, 2000);
	} else {
		mostrar_formulario_flotante(0);
		if ($("#hdd_resul_tercero_siesa").length && $("#hdd_resul_tercero_siesa").val() != "1") {
			$("#contenedor_error").html("Error en el registro externo del tercero del pago:<br />" + $("#hdd_mensaje_tercero_siesa").val());
		} else {
			if ($("#hdd_resul_tercero_entidad_siesa").length && $("#hdd_resul_tercero_entidad_siesa").val() != "1") {
				$("#contenedor_error").html("Error al verificar la existencia externa de la entidad como tercero");
			} else {
				if ($("#hdd_resul_nota_credito_siesa").length && $("#hdd_resul_nota_credito_siesa").val() != "1") {
					$("#contenedor_error").html("Error en el registro externo de la nota cr&eacute;dito asociada al pago:<br />" + $("#hdd_mensaje_nota_credito_siesa").val());
				} else {
					$("#contenedor_error").html("Error interno al tratar de registrar la anulaci&oacute;n");
				}
			}
		}
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
	var idContador = 1;
	var cont_medios_pago = parseInt($("#hdd_cont_medios_pago").val(), 10);

	if (cont_medios_pago > 0) {
		cont_medios_pago--;
		$("#tr_medio_pago_" + cont_medios_pago).css("display", "none");
		$("#cmb_tipo_pago" + idContador + "_" + cont_medios_pago).val("");
		$("#cmb_banco" + idContador + "_" + cont_medios_pago).val("");
		$("#tipoPago" + idContador + "_" + cont_medios_pago).val("0");
		$("#tipoPago" + idContador + "_" + cont_medios_pago).attr("disabled", "disabled");
		$("#hdd_cont_medios_pago").val(cont_medios_pago);
	}

	calcular_total_medios(true);
}

function registrar_cambios_totales() {
	var idContador = 1;
	var cont_medios_pago = parseInt($("#hdd_cont_medios_pago").val(), 10);
	var cont_conceptos = 0;
	var indice_boleta = -1;
	var indice_concepto = -1;
	for (var i = 0; i < cont_medios_pago; i++) {
		if ($("#cmb_tipo_pago" + idContador + "_" + i).val() == "0") {
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
			$("#tipoPago" + idContador + "_" + indice_boleta).val(total_aux - total_cuota_aux);
			if (cont_conceptos == 1) {
				$("#tipoPago" + idContador + "_" + indice_concepto).val(total_cuota_aux);
			}
			procesarTipoPago(idContador, indice_boleta);
		} else if ((total_aux - total_cuota_aux) != 0) {
			//Primer concepto libre
			var ind_boleta_asig = false;
			var ind_total_asig = false;
			for (var i = 0; i < cant_productos; i++) {
				if (!ind_boleta_asig && $("#cmb_tipo_pago" + idContador + "_" + i).val() == "") {
					$("#cmb_tipo_pago" + idContador + "_" + i).val("0");
					$("#tipoPago" + idContador + "_" + i).val(total_aux - total_cuota_aux);
					ind_boleta_asig = true;
				} else if (!ind_total_asig) {
					$("#tipoPago" + idContador + "_" + i).val(total_cuota_aux);
					ind_total_asig = true;
				}
			}
			
			if (!ind_total_asig && total_cuota_aux > 0) {
				//Se agrega un nuevo concepto para registrar el valor de cuota
				agregar_medio_pago();
				$("#tipoPago" + idContador + "_" + cont_medios_pago).val(total_cuota_aux);
			}
		} else if (cont_conceptos == 1) {
			$("#tipoPago" + idContador + "_" + indice_concepto).val(total_aux);
			procesarTipoPago(idContador, indice_concepto);
		}
		
		//Se actualizan los totales y diferencia
		$("#sp_total_pagar").html(total_aux.formatoNumerico());
		$("#sp_total_cuota").html(total_cuota_aux.formatoNumerico());
		$("#sp_total_diferencia").html((total_aux - total_cuota_aux).formatoNumerico());
		$("#hdd_pagar1").val(total_aux);
		$("#hdd_cuotaPagar").val(total_cuota_aux);
	}
}

function mostrar_buscar_tercero(idcontador) {
	var params = "opcion=12&idcontador=" + idcontador;
	llamarAjax("pagos_ajax.php", params, "d_interno_servicios" + idcontador + "", "muestraFormularioFlotante(1, " + idcontador + ");");
}

function buscar_terceros_pago(id_contador) {
	$("#frmListadoTerceros").validate({
		rules: {
			txtParametroTerceros: {
				required: true,
			},
		},
		submitHandler: function() {
			var parametro_terceros = str_encode($("#txtParametroTerceros").val());

			var params = "opcion=13&parametro_terceros=" + parametro_terceros + "&id_contador=" + id_contador;

			llamarAjax("pagos_ajax.php", params, "d_resultado_b_terceros", "");

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

function cargar_formulario_nuevo_tercero(id_contador) {
	var params = "opcion=2&tipo=1&id_tercero=0" +
				 "&id_contador=" + id_contador;
	
	llamarAjax("../administracion/terceros_ajax.php", params, "d_interno_servicios" + id_contador, "muestraFormularioFlotante(1, " + id_contador + ");");
}

function crear_tercero_pago(id_contador, indice) {
	$("#btn_guardar_tercero").attr("disabled", "disabled");
	$("#d_contenedor_error_2").css("display", "none");
	if (validar_tercero()) {
		
		if($("#tr_obligaciones_terceros").is(":visible")){
			var obligaciones = $("#cmb_obligaciones_terceros").val();
			var det_tributarios = $("#cmb_detalles_tributarios_t").val();
			var ind_iva = ($("#chk_ind_iva").is(":checked") ? 1 : 0);
		}else{
			var obligaciones = $("#hdd_obligaciones_terceros").val();
			var det_tributarios = $("#hdd_detalles_tributarios").val();
			var ind_iva = "";
		}
		var params = "opcion=3&tipo=1" +
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
					 "&id_contador=" + id_contador +
					 "&indice=" + indice +
					 "&obligaciones=" + obligaciones  +
					 "&det_tributarios=" + det_tributarios +
					 "&ind_iva=" + ind_iva;
		
		llamarAjax("../administracion/terceros_ajax.php", params, "d_resultado_tercero_t", "verificar_guardar_tercero_pago(" + id_contador + ", " + indice + ");");
	} else {
		$("#d_contenedor_error_2").css("display", "block");
		$("#d_contenedor_error_2").html("Los campos marcados en rojo son obligatorios");
		window.scroll(0, 0);
		$("#btn_guardar_tercero").removeAttr("disabled");
	}
}

function verificar_guardar_tercero_pago(id_contador, indice) {
	var resultado = parseInt($("#hdd_guardar_tercero").val(), 10);

	if (resultado > 0) {
		$("#d_contenedor_exito_2").css("display", "block");
		$("#d_contenedor_exito_2").html("Registro guardado con &eacute;xito");

		$("#hdd_id_tercero").val(resultado);
		$("#d_nombre_tercero").html($("#hdd_nombre_tercero_crea" + id_contador + "_" + indice).val());

		setTimeout(function() {
			$("#d_contenedor_exito_2").css("display", "none");
			$("#d_interno_servicios").html("");
			muestraFormularioFlotante(0, id_contador);
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

function validar_mostrar_datos_tercero(id_contador) {
	var ind_mostrar = true;
	var cont_medios_pago = parseInt($("#hdd_cont_medios_pago").val(), 10);
	var mensaje_aux = "";
	var ind_tercero_obl = 0;
	for (var i = 0; i < cont_medios_pago; i++) {
		var id_tipo_aux = $("#cmb_tipo_pago" + id_contador + "_" + i).val();
		if ($("#hdd_tipo_pago_tercero_" + id_tipo_aux).val() == "1") {
			ind_mostrar = true;
			mensaje_aux = "Uno de los tipos de pago seleccionados requiere la asignaci&oacute;n de un tercero.";
			ind_tercero_obl = 1;
			break;
		}
	}

	//Edad
	if ($("#hdd_edadPaciente").val() != "") {
		var edad_aux = parseInt($("#hdd_edadPaciente").val(), 10);
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
	var id_paciente = $("#hdd_idPaciente").val();
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

function seleccionar_anticipo(indice, consec) {
	if ($("#chk_anticipo_" + indice + "_" + consec).is(":checked")) {
		$("#txt_valor_anticipo_" + indice + "_" + consec).val($("#hdd_saldo_anticipo_" + indice + "_" + consec).val());
		$("#txt_valor_anticipo_" + indice + "_" + consec).removeAttr("disabled");
	} else {
		$("#txt_valor_anticipo_" + indice + "_" + consec).val("0");
		$("#txt_valor_anticipo_" + indice + "_" + consec).attr("disabled", "disabled");
	}
	totalizar_anticipos(indice);
}

function totalizar_anticipos(indice) {
	var cant_anticipos = parseInt($("#hdd_cant_anticipos_" + indice).val(), 10);
	var total_anticipos = 0;
	for (var i = 0; i < cant_anticipos; i++) {
		if ($("#chk_anticipo_" + indice + "_" + i).is(":checked")) {
			total_anticipos += parseInt("0" + $("#txt_valor_anticipo_" + indice + "_" + i).val(), 10);
		}
	}
	$("#tipoPago1_" + indice).val(total_anticipos);
	
	procesarTipoPago(1, indice);
}

function imprimir_factura() {
	var params = "opcion=3&id_pago=" + $("#hdd_id_pago").val();

	llamarAjax("recibo_pago_ajax.php", params, "d_imprimir_recibo", "mostrar_factura();");
}

function mostrar_factura() {
	if ($("#hdd_ruta_arch_pdf_fact").length) {
		var ruta = $("#hdd_ruta_arch_pdf_fact").val();
		window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=factura.pdf", "_blank");
	}
}

function consultar_pago_siesa(id_pago, id_lugar_cita) {
	$("btnConsultarSiesa").attr("disabled", "disabled");
	  
	var params = "opcion=15&id_pago=" + id_pago +
				 "&id_lugar_cita=" + id_lugar_cita;
	
	$("#d_tipo_pago_siesa").html("<img src='../imagenes/ajax-loader.gif\'>");
	llamarAjax("pagos_ajax.php", params, "d_tipo_pago_siesa", "finalizar_consultar_pago_siesa()");
}

function finalizar_consultar_pago_siesa() {
	$("btnConsultarSiesa").removeAttr("disabled");
}
