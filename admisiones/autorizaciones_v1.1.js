function seleccionar_ordenMedica(idOrdenMedica) {
	var params = "opcion=2&idOrdenMedica=" + idOrdenMedica;
	
	llamarAjax("autorizaciones_ajax.php", params, "divOrdenesMedicas", "");
}

function imprimir_orden_medica_autorizaciones() {
	var idOrdenMedica = $("#hddIdOrdenMedica").val();
	var params = "opcion=3&idOrdenMedica=" + idOrdenMedica;
	
	llamarAjax("../historia_clinica/reporte_ordenesRemisiones_ajax.php", params, "div_ruta_reporte_pdf", "generar_pdf_orden_medica();");
}

function generar_pdf_orden_medica() {
	var ruta = $("#hdd_ruta_ordenMericamentos_pdf").val();
	window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=ordenMeicamentos.pdf", "_blank");
}

function btnBuscarProcedimientosPaquetes(rastro) {
	mostrar_formulario_flotante(1);
	var params = "opcion=6&rastro=" + rastro;
	
	llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "divTmpOrdenMedicaDet", "");
}

function seleccionar_orden_medica_det(id) {
	mostrar_formulario_flotante(1);
	var params = "opcion=3&idOrdenMedicaDet=" + id;
	
	llamarAjax("autorizaciones_ajax.php", params, "d_interno", "");
}

function seleccionar_autorizacion(id) {
	mostrar_formulario_flotante(1);
	var params = "opcion=7&idAutorizacion=" + id;
	
	llamarAjax("autorizaciones_ajax.php", params, "d_interno", "");
}

function seleccionar_proveedores(id) {
	var textoTipo = $("#hddNombreTipoProcedimiento").val();
	var nombreProcedimiento = $("#hddNombreProcedimiento").val();

	var params = "opcion=4&idOrdenMedicaDet=" + id +
				 "&textoTipo=" + textoTipo +
				 "&nombreProcedimiento=" + nombreProcedimiento;
	
	llamarAjax("autorizaciones_ajax.php", params, "d_interno", "");
}

function cargarDatosProveedor() {
	var idProveedor = $("#cmbProveedor").val();

	var params = "opcion=5&idProveedor=" + idProveedor;
	
	llamarAjax("autorizaciones_ajax.php", params, "divSeleccionarProveedor", "");
}

function validarAutorizar() {
	var resultado = parseInt($("#hddResultadoAutorizar").val(), 10);
	
	switch (true) {
		case resultado > 0:
			swal({//Ventana temporal que muestra el progreso del ajax...
				title: "La autorización ha sido creada",
				type: "success",
				text: "¿Desea imprimir el reporte de la autorización?",
				showCancelButton: true,
				confirmButtonText: "Sí, imprimir",
				cancelButtonText: "No, continuar",
				onOpen: () => {
					var idOrdenMedica = $("#hddIdOrdenMedica").val();
					cerrar_div_centro();
					seleccionar_ordenMedica(idOrdenMedica);
				}
			}).then((result) => {
				if (result.value) {//Imprimir reporte en PDF
					imprimir_autorizacion(resultado);
				}
			});
			break;
		default:
			var mensajeTipoError = "";
			var tipoError = 0;
			switch (resultado) {
				case - 1:
					tipoError = resultado;
					mensajeTipoError = "Error interno en el procedimiento de almacenado";
					break;
				case - 2:
					tipoError = resultado;
					mensajeTipoError = "Error, la órden médica se encuentra inactiva";
					break;
				case - 3:
					tipoError = resultado;
					mensajeTipoError = "Error, El procedimiento ya ha sido autorizado";
					break;
				case - 4:
					tipoError = resultado;
					mensajeTipoError = "Error, la fecha de vencimiento es menor a la fecha actual";
					break;
			}
			
			swal({
				title: tipoError,
				type: "error",
				text: mensajeTipoError,
				showConfirmButton: false,
				showCloseButton: true,
			});
			break;
	}
}

function imprimir_autorizacion(id = null) {
	var idAutorizacion = (id == null ? $("#hdd_idautorizacion").val() : id);
	var params = "opcion=8&idAutorizacion=" + idAutorizacion;
	
	llamarAjax("autorizaciones_ajax.php", params, "div_ruta_reporte_pdf", "generar_pdf_autorizacion();");
}

function generar_pdf_autorizacion() {
	var ruta = $("#hdd_ruta_autorizacion_pdf").val();
	window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=ordenMeicamentos.pdf", "_blank");
}

function autorizar_procedimientos_adicionales() {
	mostrar_formulario_flotante(1);
	var params = "opcion=9";
	
	llamarAjax("autorizaciones_ajax.php", params, "d_interno", "");
}

function autorizar_procedimiento_adicional() {
	var idProcedimiento = $("#hddCodProcModal").val();
	var ciex = $("#hddCodCiexModal").val();
	var ojo = $("#cmb_ojo").val();
	var codProveedor = $("#codProvModal").val();

	if (idProcedimiento.length > 0 && ciex.length > 0 && ojo.length > 0 && codProveedor.length > 0) {
		swal({
			title: "¿Realmente desea autorizar el procedimiento?",
			type: "question",
			text: "Se requiere una observación",
			showCloseButton: true,
			showCancelButton: true,
			confirmButtonText: "Aceptar",
			cancelButtonText: "Cancelar",
			input: "textarea",
			inputValidator: (value) => {
				return !value && "Debe asignar una observación"
			}
		}).then((resultado) => {
			if (resultado.value) {//Evento aceptar
				swal({//Ventana temporal que muestra el progreso del ajax...
					title: "...Espere un momento...",
					type: "question",
					allowOutsideClick: false,
					showConfirmButton: false,
					html: '<div id="tmpResultado"></div>',
					onOpen: () => {
						var observacion = JSON.stringify(resultado.value);
						var idOrdenMedica = $("#hddIdOrdenMedica").val();
						var tipoAuto = 2;//2= Autorización basada en procedimiento o paquete; agregado por el usuario autorizador
						var tipoProducto = $("#hddTipoProductos").val();
						var params = "opcion=15&tipoAuto=" + tipoAuto +
									 "&idOrdenMedica=" + idOrdenMedica +
									 "&observacion=" + observacion +
									 "&idProveedor=" + codProveedor +
									 "&procedimiento=" + idProcedimiento +
									 "&ciex=" + ciex +
									 "&ojo=" + ojo +
									 "&tipoProducto=" + tipoProducto;
						
						llamarAjax("autorizaciones_ajax.php", params, "d_adicionales_aut", "validarAutorizarAdicional();");
					}
				});
			}
		});
	} else {
		alert_basico("Debe seleccionar un procedimiento o paquete, un diagnóstico, el ojo y el proveedor", "", "error");
	}
}

function validarAutorizarAdicional() {
	var resultado = parseInt($("#hddResultadoAutorizar").val(), 10);

	switch (true) {
		case resultado > 0:
			swal({//Ventana temporal que muestra el progreso del ajax...
				title: "La autorización ha sido creada",
				type: "success",
				text: "¿Desea imprimir el reporte de la autorización?",
				showCancelButton: true,
				confirmButtonText: "Sí, imprimir",
				cancelButtonText: "No, continuar",
				onOpen: () => {
					var idOrdenMedica = $("#hddIdOrdenMedica").val();
					cerrar_div_centro();
					seleccionar_ordenMedica(idOrdenMedica);
				}
			}).then((result) => {
				if (result.value) {//Imprimir reporte en PDF
					imprimir_autorizacion(resultado);
				}
			});
			break;
		default:
			var mensajeTipoError = "";
			var tipoError = 0;
			switch (resultado) {
				case - 1:
					tipoError = resultado;
					mensajeTipoError = "Error interno en el procedimiento de almacenado";
					break;
				case - 2:
					tipoError = resultado;
					mensajeTipoError = "Error, la órden médica se encuentra inactiva";
					break;
				case - 3:
					tipoError = resultado;
					mensajeTipoError = "Error, El procedimiento ya ha sido autorizado";
					break;
				case - 4:
					tipoError = resultado;
					mensajeTipoError = "Error, la fecha de vencimiento es menor a la fecha actual";
					break;
			}
			
			swal({
				title: tipoError,
				type: "error",
				text: mensajeTipoError,
				showConfirmButton: false,
				showCloseButton: true,
			});
			break;
	}
}


function autorizar_procedimiento() {
	var idProveedor = $("#codProvModal").val();
	
	if (idProveedor != "") {
		swal({
			title: "¿Realmente desea autorizar el procedimiento/paquete?",
			type: "question",
			text: "Se requiere una observación",
			showCloseButton: true,
			showCancelButton: true,
			confirmButtonText: "Aceptar",
			cancelButtonText: "Cancelar",
			input: "textarea",
			inputValidator: (value) => {
				return !value && "Debe asignar una observción"
			}
		}).then((resultado) => {
			if (resultado.value) {//Evento aceptar
				swal({//Ventana temporal que muestra el progreso del ajax...
					title: "...Espere un momento...",
					type: "question",
					allowOutsideClick: false,
					showConfirmButton: false,
					html: '<div id="tmpResultado"></div>',
					onOpen: () => {
						var tipoAuto = 1;
						var idOrdenMedicaDet = $("#hddIdOrdenMedicaDet").val();
						var idOrdenMedica = $("#hddIdOrdenMedica").val();
						var observacion = JSON.stringify(resultado.value);

						var params = "opcion=6&tipoAuto=" + tipoAuto +
									 "&idOrdenMedicaDet=" + idOrdenMedicaDet +
									 "&idOrdenMedica=" + idOrdenMedica +
									 "&observacion=" + observacion +
									 "&idProveedor=" + idProveedor;
						
						llamarAjax("autorizaciones_ajax.php", params, "tmpResultado", "validarAutorizar();");
					}
				});
			}
		});
	} else if (idProveedor <= 0) {
		alert_basico("Debe seleccionar un proveedor", "", "error");
	}
}

function ver_ordenes_paciente(idPaciente, nombre, documento, tipoDocumento, telefonos, fechaNacimiento, edad, convenio, plan, estadoConvenio, tipoAccion) {
	var params = "opcion=16&idPaciente=" + idPaciente +
				 "&nombre=" + nombre +
				 "&documento=" + documento +
				 "&telefonos=" + telefonos +
				 "&fechaNacimiento=" + fechaNacimiento +
				 "&edad=" + edad +
				 "&nombre_convenio=" + convenio +
                                 "&nombre_plan=" + plan +
				 "&estadoConvenio=" + estadoConvenio +
				 "&tipoAccion=" + tipoAccion +
				 "&tipoDocumento=" + tipoDocumento;
	
	llamarAjax("autorizaciones_ajax.php", params, "contenedor_ordenes", "");
}

function validarBuscarPacientes() {
	var txt_paciente = $("#txt_paciente").val();
	if (txt_paciente.length == 0) {
		alert_basico("Error", "Ingrese el número de documento o el nombre del paciente", "error");
	} else {
		var params = "opcion=1&txt_paciente=" + txt_paciente +
					 "&tipoDeAccion=" + 2;
		
		llamarAjax("autorizaciones_ajax.php", params, "contenedor_ordenes", "");
	}
}

function validarBuscarOrdenes() {
	$("#frm_formulas").validate({
		rules: {
			txt_paciente: {
				required: true,
			},
		},
		submitHandler: function () {
			var txt_paciente = $("#txt_paciente").val();
			var params = "opcion=1&txt_paciente=" + txt_paciente +
						 "&tipoDeAccion=" + 1;
			
			llamarAjax("autorizaciones_ajax.php", params, "contenedor_ordenes", "");

			return false;
		},
	});
}

function btnRegresar() {
	var docPaciente = $("#hddIdPaciente").val();
	var params = "opcion=1&txt_paciente=" + docPaciente +
				 "&tipoDeAccion=" + 1;
	
	llamarAjax("autorizaciones_ajax.php", params, "contenedor_ordenes", "");
}


function desautorizar() {
	swal({
		title: "¿Realmente desea cancelar la autorización?",
		type: "question",
		text: "Se requiere una observación",
		showCloseButton: true,
		showCancelButton: true,
		confirmButtonText: "Sí",
		cancelButtonText: "No",
		input: "textarea",
		inputValidator: (value) => {
			return !value && "Debe asignar una observación"
		}
	}).then((resultado) => {
		if (resultado.value) {//Evento aceptar
			swal({//Ventana temporal que muestra el progreso del ajax...
				title: "...Espere un momento...",
				type: "question",
				allowOutsideClick: true,
				showConfirmButton: false,
				html: '<div id="tmpResultado"></div>',
				onOpen: () => {
					var observacion = JSON.stringify(resultado.value);
					var idAutorizacion = $("#hdd_idautorizacion").val();
					var params = "opcion=17&idAutorizacion=" + idAutorizacion +
								 "&observacion=" + observacion;
					
					llamarAjax("autorizaciones_ajax.php", params, "tmpResultado", "validarDesautorizar();");					
				}
			});
		}
	});
}

function validarDesautorizar() {
	var resultado = parseInt($("#hddResultadoDesautorizar").val(), 10);

	switch (true) {
		case resultado > 0:
			swal({//Ventana temporal que muestra el progreso del ajax...
				title: "La autorización ha sido cancelada",
				type: "success",
				text: "",
				showCancelButton: false,
				confirmButtonText: "Aceptar",
				cancelButtonText: "",
				onOpen: () => {
					var idOrdenMedica = $("#hddIdOrdenMedica").val();
					cerrar_div_centro();
					seleccionar_ordenMedica(idOrdenMedica);
				}
			});
			break;
		default:
			var mensajeTipoError = "";
			var tipoError = 0;
			switch (resultado) {
				case - 1:
					tipoError = resultado;
					mensajeTipoError = "Error interno en el procedimiento de almacenado";
					break;
				case - 5:
					tipoError = resultado;
					mensajeTipoError = "La autorización no se puede cancelar ";
					break;
			}
			swal({
				title: tipoError,
				type: "error",
				text: mensajeTipoError,
				showConfirmButton: false,
				showCloseButton: true,
			});
			break;
	}
}

function seleccionar_convenio(idConvenio, contador) {
	var id_convenio = $("#" + idConvenio).val();
	var params = "opcion=19&id_convenio=" + id_convenio +
				 "&contador=" + contador;
	
	llamarAjax("autorizaciones_ajax.php", params, "cmb_plan_" + contador, "");
}

function btnVentanaBuscarProcedimiento() {
	swal({//Ventana temporal que muestra el progreso del ajax...
		title: "Buscar procedimientos y paquetes",
		showCancelButton: true,
		html: '<div id="tmpResultado"></div>',
		showConfirmButton: false,
		confirmButtonText: "",
		cancelButtonText: "Cancelar",
		onOpen: () => {
			var params = "opcion=10";
			
			llamarAjax("autorizaciones_ajax.php", params, "tmpResultado", "");
		}
	}).then((result) => {
		if (result.value) {//Imprimir reporte en PDF
		}
	});
}

function buscarProcedimientos() {
	var params = "opcion=11&parametro=" + str_encode($("#txtParametro").val());
	
	llamarAjax("autorizaciones_ajax.php", params, "resultadoTbl", "");
}

function agregarProcedimientoAutorizaciones(id, nombre, tipoProducto) {
	swal.close();
	$("#codProcModal").val(id);
	$("#hddCodProcModal").val(id);
	$("#nomProcModal").html(nombre);
	$("#hddTipoProductos").val(tipoProducto);
}

function buscar_procedimiento_codigo(cod_servicio) {
	var params = "opcion=21&cod_servicio=" + str_encode(cod_servicio);
	
	llamarAjax("autorizaciones_ajax.php", params, "d_adicionales_aut", "continuar_buscar_procedimiento_codigo();");
}

function continuar_buscar_procedimiento_codigo() {
	var tipo_servicio = $("#hdd_tipo_servicio_b_aut").val();
	var nombre_servicio = $("#hdd_nombre_servicio_b_aut").val();
	
	if (tipo_servicio > 0) {
		$("#hddCodProcModal").val($("#codProcModal").val());
		$("#nomProcModal").removeClass("texto-rojo");
		$("#nomProcModal").html(nombre_servicio);
		$("#hddTipoProductos").val(tipo_servicio);
	} else {
		$("#hddCodProcModal").val("");
		$("#nomProcModal").addClass("texto-rojo");
		$("#nomProcModal").html(nombre_servicio);
		$("#hddTipoProductos").val("");
	}
}

function btnVentanaBuscarDiagnostico() {
	swal({//Ventana temporal que muestra el progreso del ajax...
		title: "Buscar diagnósticos",
		showCancelButton: true,
		html: '<div id="tmpResultado"></div>',
		showConfirmButton: false,
		confirmButtonText: "",
		cancelButtonText: "Cancelar",
		onOpen: () => {
			var params = "opcion=12";
			
			llamarAjax("autorizaciones_ajax.php", params, "tmpResultado", "");
		}
	}).then((result) => {
		if (result.value) {//Imprimir reporte en PDF		   
		}
	});
}

function buscarDiagnostico() {
	var params = "opcion=13&parametro=" + str_encode($("#txtParametro").val());
	
	llamarAjax("autorizaciones_ajax.php", params, "resultadoTblDiagnostico", "");
}

function agregarDiagnosticoAutorizaciones(id, nombre) {
	swal.close();
	$("#hddCodCiexModal").val(id);
	$("#codCiexModal").val(id);
	$("#nomCiexModal").html(nombre);
}

function buscar_diagnostico_codigo(cod_ciex) {
	var params = "opcion=22&cod_ciex=" + str_encode(cod_ciex);
	
	llamarAjax("autorizaciones_ajax.php", params, "d_adicionales_aut", "continuar_buscar_diagnostico_codigo();");
}

function continuar_buscar_diagnostico_codigo() {
	var ind_hallado = $("#hdd_hallado_ciex_b_aut").val();
	var nombre_ciex = $("#hdd_nombre_ciex_b_aut").val();
	
	if (ind_hallado == 1) {
		$("#hddCodCiexModal").val($("#codCiexModal").val());
		$("#nomCiexModal").removeClass("texto-rojo");
		$("#nomCiexModal").html(nombre_ciex);
	} else {
		$("#hddCodCiexModal").val("");
		$("#nomCiexModal").addClass("texto-rojo");
		$("#nomCiexModal").html(nombre_ciex);
	}
}

function btnVentanaBuscarProveedor() {
	swal({//Ventana temporal que muestra el progreso del ajax...
		title: "Buscar proveedores",
		showCancelButton: true,
		html: '<div id="tmpResultado"></div>',
		showConfirmButton: false,
		confirmButtonText: "",
		cancelButtonText: "Cancelar",
		onOpen: () => {
			var params = "opcion=14";
			
			llamarAjax("autorizaciones_ajax.php", params, "tmpResultado", "");
		}
	}).then((result) => {
		if (result.value) {//Imprimir reporte en PDF		   
		}
	});
}

function buscarProveedor() {
	var params = "opcion=20&parametro=" + str_encode($("#txtParametro").val());
	
	llamarAjax("autorizaciones_ajax.php", params, "resultadoTblProveedor", "");
}

function agregarProveedorAutorizaciones(id_proveedor, numero_documento, nombre_proveedor) {
	swal.close();
	$("#codProvModal").val(id_proveedor);
	$("#nomProvModal").html(nombre_proveedor);
}

function liquidar_autorizacion() {
	$("#btn_liquidar_autorizacion").attr("disabled", "disabled");
	var banderaVisibleNoChecked = true;
	var banderaConvenioPlan = false;
	var id_auto_aux = 0;
	var filas = $("#tablaAutorizaciones  tr").length;
	//Valida visibilidad del input y si está checked
	for (var i = 1; i < filas; i++) {
		if ($("#auto_" + i).is(":visible")) {
			if ($("#auto_" + i).prop("checked")) {
				banderaVisibleNoChecked = false;
				var convenio = $("#cmb_convenio_" + i).val();
				var plan = $("#cmb_plan_" + i).val();
				//Valida la selección del convenio y del plan
				if (plan == "" || convenio == "") {
					banderaConvenioPlan = true;
					id_auto_aux = $("#auto_" + i).val();
					break;
				}
			}
		}
	}
	
	if (banderaVisibleNoChecked) {
		alert_basico("Error", "Debe seleccionar al menos una autorización", "error");
		$("#btn_liquidar_autorizacion").removeAttr("disabled");
	} else if (banderaConvenioPlan) {
		alert_basico("Error", "Seleccione el convenio y el plan para la autorización #" + id_auto_aux, "error");
		$("#btn_liquidar_autorizacion").removeAttr("disabled");
	} else {
		swal({
			title: "¿Realmente desea realizar la liquidación?",
			type: "question",
			showCloseButton: true,
			showCancelButton: true,
			confirmButtonText: "Aceptar",
			cancelButtonText: "Cancelar"
		}).then((resultado) => {
			if (resultado.value) {
				var observacion = JSON.stringify(resultado.value);
				var params = "opcion=18&id_paciente=" + $("#hdd_paciente").val() +
							 "&id_profesional=" + $("#hdd_idProfesional").val();
				
				//Recorre la tabla y recoge el id de los checkbox seleccionados
				var cont_aux = 0;
				for (var i = 1; i < filas; i++) {
					var banderaVisible = $("#auto_" + i).is(":visible");
					if (banderaVisible) {
						var banderaChecked = $("#auto_" + i).prop("checked");
						if (banderaChecked) {
							params += "&id_autorizacion_" + cont_aux + "=" + $("#auto_" + i).val() +
									  "&id_convenio_autorizacion_" + cont_aux + "=" + $("#cmb_convenio_" + i).val() +
									  "&id_plan_autorizacion_" + cont_aux + "=" + $("#cmb_plan_" + i).val();
							
							cont_aux++;
						}
					}
				}
				params += "&cant_autorizaciones=" + cont_aux;
				
				llamarAjax("autorizaciones_ajax.php", params, "d_autorizar", "finalizar_liquidar_autorizacion();");
			} else {
				$("#btn_liquidar_autorizacion").removeAttr("disabled");
			}
		});
	}
}

function finalizar_liquidar_autorizacion() {
	var resultado = $("#hdd_resultado_pago_aut").val();
	window.scrollTo(0, 0);
	if (resultado > 0) {
		$("#contenedor_error").css("display", "none");
		$("#contenedor_exito").css("display", "block");
		$("#contenedor_exito").html("Liquidaci&oacute;n de autorizaci&oacute;n registrada con &eacute;xito");
		setTimeout(function() { enviar_formulario_registrar_pago(0, resultado); }, 1000);
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error al registrar la liquidaci&oacute;n de las autorizaciones");
		$("#btn_liquidar_autorizacion").removeAttr("disabled");
	}
}

function enviar_formulario_registrar_pago(id_admision, id_pago) {
    $("#frm_credencial").append('<input type="hidden" id="hdd_idAdmision" name="hdd_idAdmision" value="' + id_admision + '"  />');
    $("#frm_credencial").append('<input type="hidden" id="hdd_idPago" name="hdd_idPago" value="' + id_pago + '"  />');
    $("#frm_credencial").append('<input type="hidden" id="hdd_numero_menu" name="hdd_numero_menu" value="18"  />');
    enviar_credencial("../admisiones/pagos.php", $("#hdd_menu").val());
}

function nuevo_paciente_btn() {
    var params = "opcion=10"+
            "&ind_overflow=1"+
            "&ind_ventana_flotante=1";
    llamarAjax("../admisiones/pacientes_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1);");
    
    //Oculta el div "contenedor_medicamentos"
    $("#contenedor_ordenes").html("");
}