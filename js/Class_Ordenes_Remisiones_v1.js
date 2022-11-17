$("#txtParametroMedicamentos").keypress(function (event) {
	if (event.which == 13) {
		alert("Enter");
	}
});

/*------------- Remisiones ---------------*/
function mostrar_remision(id) {
	id = parseInt(id, 10);
	for (var i = 0; i < 10; i++) {
		if (id == i) {
			$("#tabla_rem_" + i).show();
		} else if ($("#tabla_rem_" + i).is(":visible")) {
			$("#tabla_rem_" + i).hide();
		}
	}
}

function agregar_remision() {
	var cant_remisiones = parseInt($("#cant_remisiones").val(), 10);

	if (cant_remisiones < 10) {
		var opt_aux = new Option(cant_remisiones + 1, cant_remisiones);
		$(opt_aux).html(cant_remisiones + 1);

		$("#cmb_num_remision").append(opt_aux);
		$("#cmb_num_remision").val(cant_remisiones);

		$("#cant_remisiones").val(cant_remisiones + 1);
		mostrar_remision(cant_remisiones);
	} else {
		alert("Se ha alcanzado el n\xfamero m\xe1ximo de remisiones permitidas.");
	}
}

function ocultar_remision(id) {
	$("#d_detalle_formula_" + id).hide();
}

function restar_remision() {
	var num_remision = parseInt($("#cmb_num_remision").val(), 10);

	/*Verifica sí la remisión ya existe en base de datos*/
	var idRemision = $('#hdd_idRemision_' + num_remision).val();

	if (idRemision > 0) {/*Pide confirmación para eliminar la remisión*/
		swal({//Ventana temporal que muestra el progreso del ajax...
			title: '¿Realmente desea eliminar la remisión?',
			type: 'question',
			text: '',
			showCancelButton: true,
			confirmButtonText: 'Sí, eliminar!',
			cancelButtonText: 'No, Cancelar!'
		}).then((result) => {
			if (result.value) {//Imprimir reporte en PDF
				var params = "opcion=1" +
						"&tipoRemision=" + "" +
						"&observacion=" + "" +
						"&idHc=" + "" +
						"&idRemision=" + idRemision +
						"&indEstado=" + 0;
				llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "resultadoRemision", "verificarEliminarRemision(" + num_remision + ");");
				//llamarAjax("../funciones/Class_Atencion_Remision_ajax.php", params, "resultadoRemision", "");
			}
		});

	} else {//Elimina el objeto del DOOM
		$("#cmb_num_remision option[value='" + num_remision + "']").remove();
		mostrar_remision($("#cmb_num_remision").val());
	}
}

function verificarEliminarRemision(num_remision) {
	var resultado = "";
	resultado = $("#hdd_resultadoRemision").val();

	switch (resultado) {
		case "-1":
			alert_basico('Error al eliminar la remisión', '', 'error');
			break;
		default://Genera el reporte en PDF
			$("#cmb_num_remision option[value='" + num_remision + "']").remove();
			mostrar_remision($("#cmb_num_remision").val());
			break;
	}
}

function guardar_remision(secuencia) {
	var tipoRemision = $("#cmb_tipo_remision_" + secuencia).val();
	/*Valida campos vacios*/
	if (tipoRemision.length > 0) {
		/*Guarda o modifica la remisión*/
		var observacion = str_encode(eval("CKEDITOR.instances.tabla_rem_desc_" + secuencia + ".getData()"));
		var idRemision = $("#hdd_idRemision_" + secuencia).val();
		var idHc = $("#hdd_id_hc_consulta").val();

		var params = "opcion=1" +
					 "&tipoRemision=" + tipoRemision +
					 "&observacion=" + observacion +
					 "&idRemision=" + idRemision +
					 "&idHc=" + idHc +
					 "&indEstado=1";
		
		llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "resultadoRemision", "verificarGuardarRemision(" + secuencia + ");");
	} else {
		alert_basico('Seleccione el tipo de remisión', '', 'error');
	}
}


function verificarGuardarRemision(secuencia) {
	var resultado = "";
	resultado = $("#hdd_resultadoRemision").val();
	switch (resultado) {
		case "-1":
			alert_basico('Error al guardar la remisión', '', 'error');
			break;
		default://Genera el reporte en PDF
			$("#hdd_idRemision_" + secuencia).val(resultado);
			var idPaciente = $('#hdd_id_paciente').val();
			var idHc = $('#hdd_id_hc_consulta').val();
			var params = "opcion=1" +
						 "&idPaciente=" + idPaciente +
						 "&idHc=" + idHc +
						 "&idRemision=" + resultado;
			
			llamarAjax("../historia_clinica/reporte_ordenesRemisiones_ajax.php", params, "d_impresion_remision", "imprimir_pdf_remision();");
			break;
	}
}

function imprimir_remision(secuencia) {
	var idRemision = $('#hdd_idRemision_' + secuencia).val();
	var idPaciente = $('#hdd_id_paciente').val();
	var idHc = $('#hdd_id_hc_consulta').val();
	var params = "opcion=1" +
			"&idPaciente=" + idPaciente +
			"&idHc=" + idHc +
			"&idRemision=" + idRemision;
	llamarAjax("../historia_clinica/reporte_ordenesRemisiones_ajax.php", params, "resultadoRemision", "imprimir_pdf_remision();");
}

function imprimir_pdf_remision() {
	var ruta = $("#hdd_ruta_remision_pdf").val();
	window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=remision.pdf", "_blank");
}
/*------------- Remisiones ---------------*/


/*------- Formulas de medicamentos -------*/
function agregar_formula_medicamentos() {
	var cant_medicamentos = parseInt($("#cant_medicamentos").val(), 10);

	if (cant_medicamentos < 10) {
		var opt_aux = new Option(cant_medicamentos + 1, cant_medicamentos);
		$(opt_aux).html(cant_medicamentos + 1);

		$("#cmb_num_med").append(opt_aux);
		$("#cmb_num_med").val(cant_medicamentos);

		$("#cant_medicamentos").val(cant_medicamentos + 1);
		mostrar_medicamento(cant_medicamentos);
	} else {
		alert_basico('Se ha alcanzado el n\xfamero m\xe1ximo de remisiones permitidas.', '', 'error');
	}
}

function mostrar_medicamento(id) {
	id = parseInt(id, 10);
	for (var i = 0; i < 10; i++) {
		if (id == i) {
			$("#tabla_med_" + i).show();
		} else if ($("#tabla_med_" + i).is(":visible")) {
			$("#tabla_med_" + i).hide();
		}
	}
}

function btnBuscarMedicamentos(rastro) {
	mostrar_formulario_flotante(1);
	var params = "opcion=2" +
			"&rastro=" + rastro;
	llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "d_interno", "");
}

function buscarMedicamentos() {
	var parametro = $("#txtParametroMedicamentos").val();
	var params = "opcion=3" +
			"&parametro=" + parametro;
	llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "resultadoTblMedicamentos", "");
}

function comfirmAgregarMedicamento(codMed) {

	swal({//Ventana temporal que muestra el progreso del ajax...
		title: '¿Realmente desea agregar el medicamento?',
		type: 'question',
		text: '',
		showCancelButton: true,
		confirmButtonText: 'Sí, agregar!',
		cancelButtonText: 'No, Cancelar!'
	}).then((result) => {
		if (result.value) {//Imprimir reporte en PDF
			var rastro = $("#hdd_rastro").val();
			var nombreGenerico = $("#hdd_nomGenMed_" + codMed).val();
			var nombreComercial = $("#hdd_nomComMed_" + codMed).val();
			var presentacion = $("#hdd_nomPresMed_" + codMed).val();

			$("#hdd_codMed_" + rastro).val(codMed);
			$("#codMed_" + rastro).val(codMed);
			$("#nomGen_" + rastro).val(nombreGenerico);
			$("#nomCom_" + rastro).val(nombreComercial);
			$("#presMed_" + rastro).val(presentacion);

			cerrar_div_centro();
		}
	});
}

function formular_medicamento(secuencia) {

	var idFormulacionMedicamentos = $("#hdd_id_formulacion_medicamentos").val();
	var codMed = $("#hdd_codMed_" + secuencia).val();
	var cant = $("#cantMed_" + secuencia).val();
	var tiempo = $("#tiempoMed_" + secuencia).val();
	var frecAdmMed = str_encode(eval("CKEDITOR.instances.frecAdmMed_" + secuencia + ".getData()"));
	var idFormMedDet = $("#hdd_idFormMedDet_" + secuencia).val();
	var tipoFormulacion = $("#hdd_tipoFormulacion_ordenesRemisiones").val();
	var idHc = $('#hdd_idHC_ordenesRemisiones').val();
	var idPaciente = $('#hdd_idPaciente_ordenesRemisiones').val();

	var medicoRemitente = "";
	var fechaMologacion = "";
	var observacion = "";

	if (frecAdmMed.length > 0 && cant.length > 0 && codMed.length > 0) {
		if (parseInt(cant, 10) && parseInt(tiempo, 10)) {//Validación de números enteros para el campo Cantidad y tiempo del tratamiento
			cant = parseInt(cant, 10);
			tiempo = parseInt(tiempo, 10);
			swal({//Ventana temporal que muestra el progreso del ajax...
				title: "¿Realmente desea formular el medicamento?",
				type: 'question',
				text: '',
				showCancelButton: true,
				confirmButtonText: 'Sí, formular!',
				cancelButtonText: 'No,cancelar!'
			}).then((result) => {
				if (result.value) {//Evento aceptar
					var params = "opcion=4" +
							"&idFormulacionMedicamentos=" + idFormulacionMedicamentos +
							"&codMed=" + codMed +
							"&cant=" + cant +
							"&frecAdmMed=" + frecAdmMed +
							"&idFormMedDet=" + idFormMedDet +
							"&tiempo=" + tiempo +
							"&idHc=" + idHc +
							"&idPaciente=" + idPaciente +
							"&tipoFormulacion=" + tipoFormulacion +
							"&medicoRemitente=" + medicoRemitente +
							"&fechaMologacion=" + fechaMologacion +
							"&observacion=" + observacion;

					llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "rta_formulacion_medicamentos", "verificarFormularMedicamento(" + secuencia + ");");
				}
			});
		} else {
			alert_basico('Sólo números enteros en los campos Cantidad y tiempo del tratamiento', '', 'error');
		}
	} else {
		alert_basico('Debe seleccionar un medicamento, especificar el tiempo y la posología', '', 'error');
	}
}


function homologar_formular_medicamento(secuencia) {

	var idFormulacionMedicamentos = $("#hdd_id_formulacion_medicamentos").val();
	var codMed = $("#hdd_codMed_" + secuencia).val();
	var cant = $("#cantMed_" + secuencia).val();
	var tiempo = $("#tiempoMed_" + secuencia).val();
	var frecAdmMed = str_encode(eval("CKEDITOR.instances.frecAdmMed_" + secuencia + ".getData()"));
	var idFormMedDet = $("#hdd_idFormMedDet_" + secuencia).val();
	var tipoFormulacion = $("#hdd_tipoFormulacion_ordenesRemisiones").val();
	var idHc = $('#hdd_idHC_ordenesRemisiones').val();
	var idPaciente = $('#hdd_idPaciente_ordenesRemisiones').val();
	var medicoRemitente = tipoFormulacion == 2 ? $("#medicoRemitenteHomologacion").val() : "";
	var fechaMologacion = tipoFormulacion == 2 ? $("#fechaFormulacionHomologacion").val() : "";
	var observacion = str_encode($("#observacionHomologacion").val());
	
	var cmb_convenio = $("#cmb_convenio").val();
	var rango = $("#cmb_rango").val();
	var cmb_tipoCotizante = $("#cmb_tipoCotizante").val();
	var cmb_cod_plan = $("#cmb_cod_plan").val();
	
	if (frecAdmMed.length > 0 && cant.length > 0 && codMed.length > 0 && medicoRemitente.length > 0
			&& fechaMologacion.length > 0 && cmb_convenio.length > 0 && rango.length > 0
			&& cmb_tipoCotizante.length > 0 && cmb_cod_plan.length > 0) {
		if (parseInt(cant, 10) && parseInt(tiempo, 10)) {//Validación de números enteros para el campo Cantidad y tiempo del tratamiento
			$("#cke_frecAdmMed_" + secuencia).removeClass("bordeAdmision");
			$("#cantMed_" + secuencia).removeClass("bordeAdmision");
			$("#hdd_codMed_" + secuencia).removeClass("bordeAdmision");
			$("#medicoRemitenteHomologacion").removeClass("bordeAdmision");
			$("#fechaFormulacionHomologacion").removeClass("bordeAdmision");
			$("#observacionHomologacion").removeClass("bordeAdmision");
			$("#cmb_convenio").removeClass("bordeAdmision");
			$("#cmb_rango").removeClass("bordeAdmision");
			$("#cmb_tipoCotizante").removeClass("bordeAdmision");
			$("#cmb_cod_plan").removeClass("bordeAdmision");
			if (observacion.length > 0) {//Validación de observaciones
				cant = parseInt(cant, 10);
				tiempo = parseInt(tiempo, 10);

				//Validacion de estado del convenio
				var id_convenio_db = $("#hdd_id_convenio_pc").val();
				var id_plan_db = $("#hdd_id_plan_pc").val();
				var estado_convenio_db = $("#hdd_status_convenio").val();
				var bandera_guardar = false;

				if (estado_convenio_db == 1) {//Sí el estado es activo
					bandera_guardar = true;
				} else {
					if (id_convenio_db == cmb_convenio && id_plan_db == cmb_cod_plan) {
						alert_basico("", "El paciente se encuentra inactivo para el convenio y el plan seleccionados", "error");
					} else {
						//cmb_estatus_seguro = 1;//Asigna estado de activo
						bandera_guardar = true;
					}
				}
				
				if (bandera_guardar) {
					swal({//Ventana temporal que muestra el progreso del ajax...
						title: "¿Realmente desea homologar la formulación de medicamentos?",
						type: 'question',
						text: '',
						showCancelButton: true,
						confirmButtonText: 'Sí',
						cancelButtonText: 'No'
					}).then((result) => {
						if (result.value) {//
							var params = "opcion=4&idFormulacionMedicamentos=" + idFormulacionMedicamentos +
										 "&codMed=" + codMed +
										 "&cant=" + cant +
										 "&frecAdmMed=" + frecAdmMed +
										 "&idFormMedDet=" + idFormMedDet +
										 "&tiempo=" + tiempo +
										 "&idHc=" + idHc +
										 "&idPaciente=" + idPaciente +
										 "&tipoFormulacion=" + tipoFormulacion +
										 "&medicoRemitente=" + medicoRemitente +
										 "&fechaMologacion=" + fechaMologacion +
										 "&observacion=" + observacion +
										 "&rango=" + rango +
										 "&tipoCotizante=" + cmb_tipoCotizante +
										 "&plan=" + cmb_cod_plan +
										 "&convenio=" + cmb_convenio;
							
							console.log(params);
							llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "rta_formulacion_medicamentos", "verificarFormularMedicamento(" + secuencia + ");");
						}
					});
				}
			} else {
				alert_basico('Debe agregar una observación de la homologaci&oacute;n', '', 'error');
				$("#observacionHomologacion").addClass("bordeAdmision");
			}
		} else {
			alert_basico('Sólo números enteros en los campos Cantidad y tiempo del tratamiento', '', 'error');
		}
	} else {
		alert_basico("Los campos en color rojo son obligatorios", "", "error");
		$("#cke_frecAdmMed_" + secuencia).addClass("bordeAdmision");
		$("#cantMed_" + secuencia).addClass("bordeAdmision");
		$("#hdd_codMed_" + secuencia).addClass("bordeAdmision");
		$("#medicoRemitenteHomologacion").addClass("bordeAdmision");
		$("#fechaFormulacionHomologacion").addClass("bordeAdmision");
		$("#observacionHomologacion").addClass("bordeAdmision");
		$("#cmb_convenio").addClass("bordeAdmision");
		$("#cmb_rango").addClass("bordeAdmision");
		$("#cmb_tipoCotizante").addClass("bordeAdmision");
		$("#cmb_cod_plan").addClass("bordeAdmision");
	}
}

function verificarFormularMedicamento(secuencia) {
	var resultados = $("#hdd_rta_formulacion_medicamentos").val();
	var resultado = resultados.split(';');

	if (parseInt(resultado[0], 10) > 0 && parseInt(resultado[1], 10)) {
		alert_basico('El medicamento ha sido formulado', '', 'success');
		var idFormulacionMed = parseInt(resultado[1], 10);
		var idFormulacionMedDet = parseInt(resultado[0], 10);

		$("#hdd_id_formulacion_medicamentos").val(idFormulacionMed);/*Asigna el ID de formulas_medicamentos*/
		$("#hdd_idFormMedDet_" + secuencia).val(idFormulacionMedDet);/*Asigna el ID de formulas_medicamentos*/

		$("#codMed_" + secuencia).css({"background-color": "#CEFDAF"});

		if ($("#tblImprimirOrden").is(":hidden")) {//Sí el botón de imprimir reporte de la formulación se encuentra oculto
			$("#tblImprimirOrden").css({"display": "inline-table"});
		}

		let idHc = $("#hdd_idHC_ordenesRemisiones").val();//Muestra el código de la formulación, esto para formulaciones homologadas
		if (idHc.length >= 0) {
			$("#divNumeroFormulacion h5 span").html(idFormulacionMed);
			if ($("#divNumeroFormulacion").is(":hidden")) {//Sí el botón de imprimir reporte de la formulación se encuentra oculto
				$("#divNumeroFormulacion").css({"display": "inline-table"});
			}
		}

	} else {
		switch (parseInt(resultado[0], 10)) {
			case - 1:
				alert_basico(resultado[0], 'Error interno al tratar de formular el medicamento', 'error');
				break;
			case - 6:
				alert_basico(resultado[0], 'Error, el medicamento que intenta formular ya ha sido formulado con anterioridad para esta H.C.', 'error');
				break;
			case - 5:
				alert_basico(resultado[0], 'Error, No puede formular el medicamento porque la formulación de medicamentos ya se encuentra en proceso de despacho', 'error');
				break;
			default:
				alert_basico(resultado[0], '', 'error');
				break;
		}
	}
}

function eliminarFormularMedicamentos() {
	var num_medicamento = parseInt($("#cmb_num_med").val(), 10);
	var formulaMedicamento = $("#hdd_id_formulacion_medicamentos").val();
	var formulaMedicamentoDet = $("#hdd_idFormMedDet_" + num_medicamento).val();

	if (formulaMedicamentoDet.length > 0 && formulaMedicamento.length > 0) {/*Borra DOOM y DB*/
		swal({//Ventana temporal que muestra el progreso del ajax...
			title: '¿Realmente desea eliminar el medicamento?',
			type: 'question',
			text: '',
			showCancelButton: true,
			confirmButtonText: 'Sí, eliminar!',
			cancelButtonText: 'No, cancelar!'
		}).then((result) => {
			if (result.value) {//
				var params = "opcion=5" +
						"&formulaMedicamento=" + formulaMedicamento +
						"&formulaMedicamentoDet=" + formulaMedicamentoDet;

				llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "rta_formulacion_medicamentos", "verificarEliminarFormularMedicamentos(" + num_medicamento + ")");
			}
		});

	} else if (formulaMedicamentoDet.length == 0) {/*Únicamente borra el objeto del DOOM*/
		$("#cmb_num_med option[value='" + num_medicamento + "']").remove();
		mostrar_medicamento($("#cmb_num_med").val());
	} else {/*Error*/
		alert_basico('Hubo un error al intentar eliminar el medicamento', '', 'error');
	}
}

function verificarEliminarFormularMedicamentos(secuencia) {
	var resultado = $("#hdd_rta_eliminar_formulacion_medicamentos").val();

	switch (parseInt(resultado, 10)) {
		case - 2:
			alert_basico('-2', 'Error, el medicamento no existe', 'error');
			break;
		case - 1:
			alert_basico('-1', 'Error interno al intentar eliminar el medicamento', 'error');
			break;
		case - 3:
			alert_basico('-3', 'Error, la formulación ya se encuentra en proceso de despacho', 'error');
			break;
		default:/*Éxito*/
			alert_basico('El medicamento ha sido eliminado de la formulación', '', 'success');

			if (resultado == -100) {
				$("#hdd_id_formulacion_medicamentos").val("");//Reset al HDD que almacena el ID de la formulación
				if ($("#tblImprimirOrden").is(":visible")) {//Sí el botón de imprimir reporte de la formulación se encuentra oculto
					$("#tblImprimirOrden").css({"display": "none"});
				}
			}

			$("#cmb_num_med option[value='" + secuencia + "']").remove();
			mostrar_medicamento($("#cmb_num_med").val());
			break;
	}
}

function imprimir_orden_medicamentos() {
	var idFormulaMedicamento = $('#hdd_id_formulacion_medicamentos').val();
	var params = "opcion=2" +
			"&idFormulaMedicamento=" + idFormulaMedicamento;
	llamarAjax("../historia_clinica/reporte_ordenesRemisiones_ajax.php", params, "resultadoRemision", "generar_pdf_orden_medicamentos();");
}

function generar_pdf_orden_medicamentos() {
	var ruta = $("#hdd_ruta_ordenMericamentos_pdf").val();
	window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=ordenMeicamentos.pdf", "_blank");
}


/* ------------------------- Órdenes médicas --------------------------*/
/* ------------------------- Órdenes médicas --------------------------*/
/* ------------------------- Órdenes médicas --------------------------*/
/* ------------------------- Órdenes médicas --------------------------*/
/* ------------------------- Órdenes médicas --------------------------*/

function btnBuscarDiagnosticosOrdenesMedicas(rastro) {
	mostrar_formulario_flotante(1);
	var params = "opcion=13" +
			"&rastro=" + rastro;
	llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "d_interno", "");
}

function buscarDiagnosticoOrdenesMedicas() {
	let parametro = $("#txtParametro").val();

	var params = "opcion=14" +
			"&parametro=" + parametro;
	llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "resultadoTbl", "");
}

function detallesPaquete(idPaquete, msgTipo, nombrePaquete) {
	swal({//Ventana temporal que muestra el progreso del ajax...
		title: '¿Desea agregar el paquete?',
		showCancelButton: true,
		html: '<div id="tmpResultado"></div>',
		confirmButtonText: 'Sí, agregar!',
		cancelButtonText: 'No, cancelar!',
		onOpen: () => {
			var params = "opcion=8" +
					"&idPaquete=" + idPaquete +
					"&msgTipo=" + msgTipo +
					"&nombrePaquete=" + nombrePaquete;

			llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "tmpResultado", "");
		}
	}).then((result) => {
		if (result.value) {//Imprimir reporte en PDF

			let IdPaq = $("#hddDetIdPaq").val();
			let TipoPaq = $("#hddDetTipoPaq").val();
			let DescPaq = $("#hddDetNomPaq").val();
			let tipoProc = 1;//Tipo Paquete
			let rastro = $("#hdd_rastro").val();

			$("#tipoProc_" + rastro).val(TipoPaq);
			$("#nomProc_" + rastro).val(DescPaq);
			$("#codProc_" + rastro).val(IdPaq);
			$("#hdd_tipoProc_" + rastro).val(tipoProc);

			//Muestra el btn Ver procedimientos
			$("#btn_ver_proc_paquetes_" + rastro).css({"display": "inline-block"});

			cerrar_div_centro();
		}
	});
	;
}

function detallesDiagnóstico(codigo, nombre) {
	swal({//Ventana temporal que muestra el progreso del ajax...
		title: '¿Desea agregar el diagnóstico?',
		showCancelButton: true,
		html: '<div id="tmpResultado"></div>',
		confirmButtonText: 'Sí, agregar!',
		cancelButtonText: 'No, cancelar!',
		onOpen: () => {
			var params = "opcion=15" +
					"&codigo=" + codigo +
					"&nombre=" + nombre;

			llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "tmpResultado", "");
		}
	}).then((result) => {
		if (result.value) {//
			let rastro = $("#hdd_rastro").val();
			let codigo = $("#hddDetCodDiag").val();
			let nombre = $("#hddDetNomDiag").val();

			$("#codDiag_" + rastro).val(codigo);
			$("#nomDiag_" + rastro).val(codigo + " - " + nombre);

			cerrar_div_centro();
		}
	});
	;
}

function detallesProcedimiento(idProcedimiento, descripcion) {
	swal({//Ventana temporal que muestra el progreso del ajax...
		title: '¿Desea agregar el procedimiento?',
		showCancelButton: true,
		html: '<div id="tmpResultado"></div>',
		confirmButtonText: 'Sí, agregar!',
		cancelButtonText: 'No, cancelar!',
		onOpen: () => {
			var params = "opcion=9" +
					"&idProcedimiento=" + idProcedimiento +
					"&descripcion=" + descripcion;

			llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "tmpResultado", "");
		}
	}).then((result) => {
		if (result.value) {//Imprimir reporte en PDF

			let IdProcedimiento = $("#hddDetIdProcedimiento").val();
			let TipoProcedimiento = $("#hddDetTipoProcedimiento").val();
			let DescProcedimiento = $("#hddDetDescProcedimiento").val();
			let tipoProc = 2;//Tipo Procedimiento
			let rastro = $("#hdd_rastro").val();

			$("#tipoProc_" + rastro).val(TipoProcedimiento);
			$("#nomProc_" + rastro).val(DescProcedimiento);
			$("#codProc_" + rastro).val(IdProcedimiento);
			$("#hdd_tipoProc_" + rastro).val(tipoProc);

			cerrar_div_centro();
		}
	});
}

function verProcedimientosPaquete(secuencia) {
	swal({//Ventana temporal que muestra el progreso del ajax...
		title: 'Detalle del paquete',
		//showCancelButton: true,
		html: '<div id="tmpResultado"></div>',
		//confirmButtonText: 'Sí, agregar!',
		//cancelButtonText: 'No, cancelar!',
		onOpen: () => {
			var idPaquete = $("#hddCodProcOrdenMedica_" + secuencia).val();
			var params = "opcion=10" +
					"&idPaquete=" + idPaquete
			llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "tmpResultado", "");
		}
	});
}

function ordenar_procedimiento(secuencia) {
	var ciex = "";
	var descripcion = $("#descProcOrdenMedica_" + secuencia).val();
	var idOrdenMedica = $("#hdd_id_orden_medica").val();
	var codProcedimiento = $("#hddCodProcOrdenMedica_" + secuencia).val();
	var tipoProducto = $("#hddTipoProdOrdenMedica_" + secuencia).val();
	var idHc = $("#hdd_idHC_ordenMedica").val();
	var idPaciente = $("#hdd_idPaciente_ordenMedica").val();
	var idOrdenMedDet = $("#hdd_idOrdenMedDet_" + secuencia).val();
	var datosClinicos = $("#datosClinicosOrdenMedica_" + secuencia).val();
	var ojo = $("#cmb_ojoOrdenMedica_" + secuencia).val();
	var tipoOrdenMedica = $("#hdd_tipoOrdenMedica").val();

	if (codProcedimiento.length > 0 && ojo.length > 0) {
		$("#codProcOrdenMedica_" + secuencia).removeClass("bordeAdmision");
		$("#cmb_ojoOrdenMedica_" + secuencia).removeClass("bordeAdmision");
		swal({//Alert de confirmación
			title: '¿Realmente desea ordenar el procedimiento/paquete?',
			type: 'question',
			showCancelButton: true,
			confirmButtonText: 'Sí, ordenar!',
			cancelButtonText: 'No, cancelar!'
		}).then((resultado) => {
			if (resultado.value) {//Evento aceptar
				swal({//Ventana temporal que muestra el progreso del ajax...
					title: '...Espere un momento...',
					type: 'question',
					showConfirmButton: false,
					onOpen: () => {
						var params = "opcion=11" +
								"&ciex=" + ciex +
								"&descripcion=" + descripcion +
								"&idOrdenMedica=" + idOrdenMedica +
								"&codProcedimiento=" + codProcedimiento +
								"&idHc=" + idHc +
								"&idPaciente=" + idPaciente +
								"&idOrdenMedDet=" + idOrdenMedDet +
								"&datosClinicos=" + datosClinicos +
								"&ojo=" + ojo +
								"&medicoRemitente=" + "" +
								"&fechaHomologacion=" + "" +
								"&observacion=" + "" +
								"&tipoOrdenMedica=" + tipoOrdenMedica +
								"&tipoProducto=" + tipoProducto +
								"&rango=" + "" +
								"&tipoCotizante=" + "" +
								"&plan=" + "" +
								"&convenio=" + "";

						llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "d_ordenar_procedimiento", "validarOrdenarProcedimiento(" + secuencia + ")");
					}
				});
			}
		});
	} else {
		$("#codProcOrdenMedica_" + secuencia).addClass("bordeAdmision");
		$("#cmb_ojoOrdenMedica_" + secuencia).addClass("bordeAdmision");
		alert_basico('Debe seleccionar un procedimiento y especificar el ojo', '', 'error');
	}
}


function ordenar_procedimiento_homologado(secuencia) {
	var ciex = $("#hddCodCiexOrdenMedica_" + secuencia).val();
	var descripcion = $("#descProcOrdenMedica_" + secuencia).val();
	var idOrdenMedica = $("#hdd_id_orden_medica").val();
	var codProcedimiento = $("#hddCodProcOrdenMedica_" + secuencia).val();
	var idHc = $("#hdd_idHC_ordenMedica").val();
	var idPaciente = $("#hdd_idPaciente_ordenMedica").val();
	var idOrdenMedDet = $("#hdd_idOrdenMedDet_" + secuencia).val();
	var datosClinicos = $("#datosClinicosOrdenMedica_" + secuencia).val();
	var ojo = $("#cmb_ojoOrdenMedica_" + secuencia).val();
	var medicoRemitente = $("#medicoRemitenteOrdenMedica").val();
	var fechaHomologacion = $("#fechaHomologacionOrdenMedica").val();
	var observacion = $("#observacionOrdenMedica").val();
	var tipoOrdenMedica = $("#hdd_tipoOrdenMedica").val();
	var tipoProducto = $("#hddTipoProdOrdenMedica_" + secuencia).val();

	var cmb_convenio = $("#cmb_convenio").val();
	var rango = $("#cmb_rango").val();
	var cmb_tipoCotizante = $("#cmb_tipoCotizante").val();
	var cmb_cod_plan = $("#cmb_cod_plan").val();

	if (medicoRemitente.length > 0 && fechaHomologacion.length > 0 && cmb_convenio.length > 0 && rango.length > 0 && cmb_tipoCotizante.length > 0 && cmb_cod_plan.length > 0) {
		$("#medicoRemitenteOrdenMedica").removeClass("bordeAdmision");
		$("#fechaHomologacionOrdenMedica").removeClass("bordeAdmision");
		$("#cmb_convenio").removeClass("bordeAdmision");
		$("#cmb_rango").removeClass("bordeAdmision");
		$("#cmb_tipoCotizante").removeClass("bordeAdmision");
		$("#cmb_cod_plan").removeClass("bordeAdmision");
		if (ciex.length > 0 && codProcedimiento.length > 0 && ojo.length > 0) {
			$("#codCiexOrdenMedica_" + secuencia).removeClass("bordeAdmision");
			$("#codProcOrdenMedica_" + secuencia).removeClass("bordeAdmision");
			$("#cmb_ojoOrdenMedica_" + secuencia).removeClass("bordeAdmision");

			//Validacion de estado del convenio
			var id_convenio_db = $("#hdd_id_convenio_pc").val();
			var id_plan_db = $("#hdd_id_plan_pc").val();
			var estado_convenio_db = $("#hdd_status_convenio").val();
			var bandera_guardar = false;

			if (estado_convenio_db == 1) {//Sí el estado es activo
				bandera_guardar = true;
			} else {
				if (id_convenio_db == cmb_convenio && id_plan_db == cmb_cod_plan) {
					alert_basico("", "El paciente se encuentra inactivo para el convenio y el plan seleccionados", "error");
				} else {
					//cmb_estatus_seguro = 1;//Asigna estado de activo
					bandera_guardar = true;
				}
			}

			if (bandera_guardar) {
				swal({//Alert de confirmación
					title: '¿Realmente desea realizar la &oacute;rdenar de homologaci&oacute;n del procedimiento?',
					type: 'question',
					showCancelButton: true,
					confirmButtonText: 'Sí, ordenar!',
					cancelButtonText: 'No, cancelar!'
				}).then((resultado) => {
					if (resultado.value) {//Evento aceptar
						swal({//Ventana temporal que muestra el progreso del ajax...
							title: '...Espere un momento...',
							type: 'question',
							showConfirmButton: false,
							html: '<div id="tmpResultado"></div>',
							onOpen: () => {
								var params = "opcion=11" +
										"&ciex=" + ciex +
										"&descripcion=" + descripcion +
										"&idOrdenMedica=" + idOrdenMedica +
										"&codProcedimiento=" + codProcedimiento +
										"&idHc=" + idHc +
										"&idPaciente=" + idPaciente +
										"&idOrdenMedDet=" + idOrdenMedDet +
										"&datosClinicos=" + datosClinicos +
										"&ojo=" + ojo +
										"&medicoRemitente=" + medicoRemitente +
										"&fechaHomologacion=" + fechaHomologacion +
										"&observacion=" + observacion +
										"&tipoOrdenMedica=" + tipoOrdenMedica +
										"&tipoProducto=" + tipoProducto +
										"&rango=" + rango +
										"&tipoCotizante=" + cmb_tipoCotizante +
										"&plan=" + cmb_cod_plan +
										"&convenio=" + cmb_convenio;

								llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "tmpResultado", "validarOrdenarProcedimiento(" + secuencia + ")");
							}
						});
					}
				});
			}
		} else {
			$("#codCiexOrdenMedica_" + secuencia).addClass("bordeAdmision");
			$("#codProcOrdenMedica_" + secuencia).addClass("bordeAdmision");
			$("#cmb_ojoOrdenMedica_" + secuencia).addClass("bordeAdmision");
			alert_basico('Debe seleccionar un procedimiento, especificar el diagnóstico y el ojo', '', 'error');
		}
	} else {
		$("#medicoRemitenteOrdenMedica").addClass("bordeAdmision");
		$("#fechaHomologacionOrdenMedica").addClass("bordeAdmision");
		$("#cmb_convenio").addClass("bordeAdmision");
		$("#cmb_rango").addClass("bordeAdmision");
		$("#cmb_tipoCotizante").addClass("bordeAdmision");
		$("#cmb_cod_plan").addClass("bordeAdmision");
		alert_basico("Los campos en color rojo son obligatorios", "", "error");
	}
}


function validarOrdenarProcedimiento(secuencia) {
	var resultados = $("#hdd_rta_ordenar_procedimiento").val();

	var resultado = resultados.split(';');

	if (parseInt(resultado[0], 10) > 0 && parseInt(resultado[1], 10)) {
		alert_basico('El procedimiento/paquete ha sido ordenado', '', 'success');

		var idOrdenMed = parseInt(resultado[1], 10);
		var idOrdenMedDet = parseInt(resultado[0], 10);

		$("#divIdOrdenMedica").css({"display": "initial"});
		$("#spanIdOrdenM").text(idOrdenMed);

		$("#hdd_id_orden_medica").val(idOrdenMed);/*Asigna el ID de formulas_medicamentos*/
		$("#hdd_idOrdenMedDet_" + secuencia).val(idOrdenMedDet);/*Asigna el ID de formulas_medicamentos*/

		$("#codProcOrdenMedica_" + secuencia).css({"background-color": "#CEFDAF"});

		$("#btn_ordenar_medicamento_" + secuencia).css({"display": "none"});
		$("#btn_buscar_procedimientos_" + secuencia).css({"display": "none"});

		if ($("#tblImprimirOrdenMedicamentos").is(":hidden")) {//Si el botón imprimir está oculto
			$("#tblImprimirOrdenMedicamentos").css({"display": "inline-table"});
		}

	} else {
		switch (parseInt(resultado[0], 10)) {
			case - 2:
				alert_basico(resultado[0], 'Error, la orden ya se encuentra en proceso de autorización', 'error');
				break;
			case - 3:
				alert_basico(resultado[0], 'Error, el procedimiento/paquete ya se encuentra asignado a la misma orden médica', 'error');
				break;
			default:
				alert_basico(resultado[0], 'Error interno al tratar de ordenar el procedimiento', 'error');
				break;
		}
	}
}

function agregar_proc() {
	var cant_proc = parseInt($("#cant_proc").val(), 10);

	if (cant_proc < 10) {
		var opt_aux = new Option(cant_proc + 1, cant_proc);
		$(opt_aux).html(cant_proc + 1);

		$("#cmb_num_proc").append(opt_aux);
		$("#cmb_num_proc").val(cant_proc);

		$("#cant_proc").val(cant_proc + 1);
		mostrar_proc(cant_proc);
	} else {
		alert("Se ha alcanzado el n\xfamero m\xe1ximo de procedimientos permitidos.");
	}
}


function eliminarOrdenMedica() {
	var num_proc = parseInt($("#cmb_num_proc").val(), 10);
	var idOrdenMedica = $("#hdd_id_orden_medica").val();
	var idOrdenMedDet = $("#hdd_idOrdenMedDet_" + num_proc).val();

	if (idOrdenMedDet.length > 0 && idOrdenMedica.length > 0) {/*Borra DOOM y DB*/
		swal({
			title: '¿Realmente desea eliminar el procedimiento/paquete?',
			type: 'question',
			text: 'Se requiere una nota aclaratoria',
			showCloseButton: true,
			showCancelButton: true,
			confirmButtonText: 'Sí, eliminar',
			cancelButtonText: 'cancelar!',
			input: 'textarea',
			inputValidator: (value) => {
				return !value && 'Debe asignar una nota aclaratoria!'
			}
		}).then((resultado) => {
			if (resultado.value) {//Evento aceptar
				swal({//Ventana temporal que muestra el progreso del ajax...
					title: '...Espere un momento...',
					type: 'question',
					allowOutsideClick: false,
					showConfirmButton: false,
					html: '<div id="tmpResultado"></div>',
					onOpen: () => {

						var params = "opcion=12" +
								"&idOrdenMedica=" + idOrdenMedica +
								"&idOrdenMedDet=" + idOrdenMedDet +
								"&notaAclaratoria=" + JSON.stringify(resultado.value);
						llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "tmpResultado", "verificarEliminarOrdenMedica(" + num_proc + ")");
						//llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "tmpResultado", "");
					}
				});
			}
		});

	} else if (idOrdenMedDet.length == 0) {/*Únicamente borra el objeto del DOOM*/
		$("#cmb_num_proc option[value='" + num_proc + "']").remove();
		mostrar_proc($("#cmb_num_proc").val());
	} else {/*Error*/
		alert_basico('Hubo un error al intentar eliminar el medicamento', '', 'error');
	}
}

function verificarEliminarOrdenMedica(secuencia) {
	var resultado = $("#hdd_rta_eliminar_procedimiento_ordenMedica").val();

	switch (parseInt(resultado, 10)) {
		case - 5:
			alert_basico(resultado, 'Error, el procedimiento/paquete no existe', 'error');
			break;
		case - 1:
			alert_basico(resultado, 'Error interno al intentar eliminar el procedimiento/paquete', 'error');
			break;
		case - 6:
			alert_basico(resultado, 'Error, la orden médica ya se encuentra en proceso de autorizaciones', 'error');
			break;
		default:/*Éxito*/
			if (resultado == -100) {
				alert_basico('El procedimiento/paquete ha sido eliminado junto con la &oacute;rden m&eacute;dica', '', 'success');
				$("#hdd_id_orden_medica").val("");//
				if ($("#btnImprimirOrdenMedica").is(":visible")) {//Sí el botón de imprimir reporte de la formulación se encuentra oculto
					$("#tblImprimirOrdenMedicamentos").css({"display": "none"});
				}
				$("#divIdOrdenMedica").css({"display": "none"});
				$("#spanIdOrdenM").html("");

			} else {
				alert_basico('El procedimiento/paquete ha sido eliminado de la orden m&eacute;dica', '', 'success');
			}

			$("#cmb_num_proc option[value='" + secuencia + "']").remove();
			mostrar_proc($("#cmb_num_proc").val());
			break;
	}
}

function mostrar_proc(id) {
	id = parseInt(id, 10);
	for (var i = 0; i < 10; i++) {
		if (id == i) {
			$("#tabla_proc_" + i).show();
		} else if ($("#tabla_proc_" + i).is(":visible")) {
			$("#tabla_proc_" + i).hide();
		}
	}
}

function imprimir_orden_medica() {
	var idOrdenMedica = $('#hdd_id_orden_medica').val();
	var params = "opcion=3" +
			"&idOrdenMedica=" + idOrdenMedica;
	llamarAjax("../historia_clinica/reporte_ordenesRemisiones_ajax.php", params, "resultadoOrdenMedica", "generar_pdf_orden_medica();");
}

function generar_pdf_orden_medica() {
	var ruta = $("#hdd_ruta_ordenMedica_pdf").val();
	window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=ordenMedica.pdf", "_blank");
}

function seleccionar_convenio(idConvenio) {
	var params = "opcion=16" +
			"&id_convenio=" + idConvenio;
	llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "d_plan", "");
}







function btnVentanaBuscarProcedimientoOrdenMedica(secuencia) {
	swal({//Ventana temporal que muestra el progreso del ajax...
		title: "Buscar procedimientos y paquetes",
		showCancelButton: true,
		html: '<div id="tmpResultado"></div>',
		showConfirmButton: false,
		confirmButtonText: "",
		cancelButtonText: "Cancelar",
		onOpen: () => {
			var params = "opcion=7" +
					"&secuencia=" + secuencia;
			llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "tmpResultado", "");
		}
	}).then((result) => {
		if (result.value) {//Imprimir reporte en PDF
		}
	});
}

function buscarProcedimientosOrdenMedica(secuencia) {
	var params = "opcion=17&parametro=" + str_encode($("#txtParametro").val()) + "&secuencia=" + secuencia;
	llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "resultadoTbl", "");
}

function agregarProcedimientoOrdenMedica(id, nombre, tipoProducto, secuencia) {
	swal.close();
	$("#codProcOrdenMedica_" + secuencia).val(id);
	$("#hddCodProcOrdenMedica_" + secuencia).val(id);
	$("#nomProcOrdenMedica_" + secuencia).html(nombre);
	$("#hddTipoProdOrdenMedica_" + secuencia).val(tipoProducto);
	muestra_btn_ver_procedimientos(tipoProducto, secuencia);
}

function muestra_btn_ver_procedimientos(tipo, secuencia) {
	if (tipo == 1) {//Si es un paquete
		$("#btn_ver_proc_paquetes_" + secuencia).css({"display": "inline"});
	} else {
		$("#btn_ver_proc_paquetes_" + secuencia).css({"display": "none"});
	}
}

function agregarDiagnosticoOrdenMedica(id, nombre, secuencia) {
	swal.close();
	$("#hddCodCiexOrdenMedica_" + secuencia).val(id);
	$("#codCiexOrdenMedica_" + secuencia).val(id);
	$("#nomCiexOrdenMedica_" + secuencia).html(nombre);
}

function btnVentanaBuscarDiagnosticoOrdenMedica(secuencia) {
	swal({//Ventana temporal que muestra el progreso del ajax...
		title: "Buscar diagnósticos",
		showCancelButton: true,
		html: '<div id="tmpResultado"></div>',
		showConfirmButton: false,
		confirmButtonText: "",
		cancelButtonText: "Cancelar",
		onOpen: () => {
			var params = "opcion=18" +
					"&secuencia=" + secuencia;
			llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "tmpResultado", "");
		}
	}).then((result) => {
		if (result.value) {//Imprimir reporte en PDF		   
		}
	});
}

function buscarDiagnosticoOrdenMedica(secuencia) {
	var params = "opcion=19&parametro=" + str_encode($("#txtParametro").val()) + "&secuencia=" + secuencia;
	llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "resultadoTblDiagnostico", "");
}

function btnBuscarProcedimientosPaquetesOrdenMedica(rastro) {
	mostrar_formulario_flotante(1);
	var params = "opcion=6" +
			"&rastro=" + rastro;
	llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "d_interno", "");
}

function buscarProductosOrdenesMedicas() {
	let parametro = $("#txtParametro").val();
	let paquete = $("#checkPaquete").is(':checked');
	let procedimientos = $("#checkProcedimientos").is(':checked');

	if (!paquete && !procedimientos) {
		alert_basico('Campos vacios', 'Error, Debe seleccionar el tipo de producto por buscar', 'error');
	} else {
		paquete = paquete ? 1 : 0;
		procedimientos = procedimientos ? 1 : 0;
		var params = "opcion=7" +
				"&parametro=" + parametro +
				"&paquete=" + paquete +
				"&procedimientos=" + procedimientos;
		llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "resultadoTbl", "");
	}
}

function buscar_procedimiento_codigo_ordenMedica(cod_servicio, secuencia) {
	var params = "opcion=20&cod_servicio=" + str_encode(cod_servicio) + "&secuencia=" + secuencia;
	llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "d_buscar_procedimiento_ciex_codigo", "continuar_buscar_procedimiento_codigo_ordenMedica();");
}

function continuar_buscar_procedimiento_codigo_ordenMedica() {
	var tipo_servicio = $("#hdd_tipo_servicio_b_aut").val();
	var nombre_servicio = $("#hdd_nombre_servicio_b_aut").val();
	var secuencia = $("#hdd_secuencia_b_aut").val();

	if (tipo_servicio > 0) {
		$("#hddCodProcOrdenMedica_" + secuencia).val($("#codProcOrdenMedica_" + secuencia).val());
		$("#nomProcOrdenMedica_" + secuencia).removeClass("texto-rojo");
		$("#nomProcOrdenMedica_" + secuencia).html(nombre_servicio);
		$("#hddTipoProdOrdenMedica_" + secuencia).val(tipo_servicio);
		muestra_btn_ver_procedimientos(tipo_servicio, secuencia);
	} else {
		$("#hddCodProcOrdenMedica_" + secuencia).val("");
		$("#nomProcOrdenMedica_" + secuencia).addClass("texto-rojo");
		$("#nomProcOrdenMedica_" + secuencia).html(nombre_servicio);
		$("#hddTipoProdOrdenMedica_" + secuencia).val("");
	}
}

function buscar_diagnostico_codigo_ordenMedica(cod_ciex, secuencia) {
	var params = "opcion=21&cod_ciex=" + str_encode(cod_ciex) + "&secuencia=" + secuencia;
	llamarAjax("../funciones/Class_Ordenes_Remisiones_ajax.php", params, "d_buscar_procedimiento_ciex_codigo", "continuar_buscar_diagnostico_codigo_ordenMedica();");
}

function continuar_buscar_diagnostico_codigo_ordenMedica() {
	var ind_hallado = $("#hdd_hallado_ciex_b_aut").val();
	var nombre_ciex = $("#hdd_nombre_ciex_b_aut").val();
	var secuencia = $("#hdd_secuencia_b_aut").val();

	if (ind_hallado == 1) {
		$("#hddCodCiexOrdenMedica_" + secuencia).val($("#codCiexOrdenMedica_" + secuencia).val());
		$("#nomCiexOrdenMedica_" + secuencia).removeClass("texto-rojo");
		$("#nomCiexOrdenMedica_" + secuencia).html(nombre_ciex);
	} else {
		$("#hddCodCiexOrdenMedica_" + secuencia).val("");
		$("#nomCiexOrdenMedica_" + secuencia).addClass("texto-rojo");
		$("#nomCiexOrdenMedica_" + secuencia).html(nombre_ciex);
	}
}