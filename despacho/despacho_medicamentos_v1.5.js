if (CKEDITOR.env.ie && CKEDITOR.env.version < 9) {
	CKEDITOR.tools.enableHtml5Elements(document);
}

CKEDITOR.config.height = 70;
CKEDITOR.config.width = "auto";

var initCKEditorOptom = (function (id_obj) {
	var wysiwygareaAvailable = isWysiwygareaAvailable(),
			isBBCodeBuiltIn = !!CKEDITOR.plugins.get("bbcode");
	
	return function (id_obj) {
		var editorElement = CKEDITOR.document.getById(id_obj);
		
		//Dependiendo de la disponibilidad del plugin wysiwygare se incia el editor clásico o el editor en línea.
		if (wysiwygareaAvailable) {
			CKEDITOR.replace(id_obj);
		} else {
			editorElement.setAttribute("contenteditable", "true");
			CKEDITOR.inline(id_obj);
		}
	};
	
	function isWysiwygareaAvailable() {
		if (CKEDITOR.revision == ("%RE" + "V%")) {
			return true;
		}
		
		return !!CKEDITOR.plugins.get("wysiwygarea");
	}
})();

function validarBuscarFormulas() {
	$("#frm_formulas").validate({
		rules: {
			txt_paciente: {
				required: true,
			},
		},
		submitHandler: function () {
			var txt_paciente = $("#txt_paciente").val();
			var params = "opcion=1" +
						 "&txt_paciente=" + txt_paciente +
						 "&tipoDeAccion=" + 1;
			
			llamarAjax("despacho_medicamentos_ajax.php", params, "contenedor_medicamentos", "");
			
			return false;
		},
	});
}

function validarBuscarPacientes() {
	var txt_paciente = $("#txt_paciente").val();
	if (txt_paciente.length == 0) {
		alert_basico("Error", "Ingrese el número de documento o el nombre del paciente", "error");
	} else {
		var params = "opcion=1" +
					 "&txt_paciente=" + txt_paciente +
					 "&tipoDeAccion=" + 2;
		
		llamarAjax("despacho_medicamentos_ajax.php", params, "contenedor_medicamentos", "");
	}
}

function seleccionar_formulacion(idFormulacion) {
	var params = "opcion=2&idFormulacion=" + idFormulacion;
	
	llamarAjax("despacho_medicamentos_ajax.php", params, "divFormulas", "");
}

function btnRegresar() {
	var docPaciente = $("#hddIdPaciente").val();
	var params = "opcion=1" +
				 "&txt_paciente=" + docPaciente +
				 "&tipoDeAccion=" + 1;
	
	llamarAjax("despacho_medicamentos_ajax.php", params, "contenedor_medicamentos", "");
}

function validarDespacho() {
	var cantidadMedicamentosDespacharTbl = parseInt($("#hddCantMedicamentosDespacho").val(), 10);
	var params = "opcion=3";
	var cantidadMedicamentosDespacho = 0;
	
	/*Validación de los campos*/
	var continuar = false;
	
	var idFormulaMedicamento = $("#hddIdFormulaMedicamento").val();
	var cantidadTotalPorDespachar = 0;
	
	for (var i = 1; i <= cantidadMedicamentosDespacharTbl; i++) {
		var tmpCantidad = $("#txtCantidad" + i).val();
		var tmpLote = $("#txtLote" + i).val();
		var tmpIdFormulaMedicamentoDet = $("#hddIdFormulaMedicamentoDet" + i).val();
		var tmpUnidadMedida = $("#hddUnidadMedida" + i).val();
		var tmpCodigoSiesa = $("#hddCodigoSiesa" + i).val();
		if (tmpCantidad.length >= 1 || tmpLote.length >= 1) {//Sí se ingresaron valores en la fila
			if (tmpCantidad.length >= 1 && tmpLote.length >= 1) {//Si ambos input tiene valores
				if (parseInt(tmpCantidad, 10)) {//Valida números enteros en los campos de cantidades					
					cantidadMedicamentosDespacho++;
					continuar = true;
					params += "&cantidad" + cantidadMedicamentosDespacho + "=" + tmpCantidad +
							  "&lote" + cantidadMedicamentosDespacho + "=" + tmpLote +
							  "&idFormulaMedicamentoDet" + cantidadMedicamentosDespacho + "=" + tmpIdFormulaMedicamentoDet +
							  "&unidadMedida" + cantidadMedicamentosDespacho + "=" + tmpUnidadMedida +
							  "&codigoSiesa" + cantidadMedicamentosDespacho + "=" + tmpCodigoSiesa;
					
					cantidadTotalPorDespachar += parseInt(tmpCantidad, 10);
				}
			}
		}
	}
	
	var observaciones = $("#txt_observaciones_despacho").val();
	var cmb_lugar_despacho = $("#cmb_lugar_despacho").val();
	if (observaciones.length < 5 || cmb_lugar_despacho.length <= 0) {//Valida observaciones y lugar de despacho
		continuar = false;
	}
	
	if (continuar) {
		var cantidadMaximaPorDespachar = $("#hddCantidadMaximaDespacho").val();
		var backdrop = "";
		var type = "question";
		var text = "";
		var html = "";
		var title = "¿Desea despachar los medicamentos?";
		var ultimoDespacho = parseInt($("#hddUltimoDespacho").val(), 10);
		var mensajeUltimoDespacho = "";
		if (ultimoDespacho == 0) {
			mensajeUltimoDespacho = "<h4 style='color:#E11111'>El último despacho sucedió hace menos de 1 mes</h4>";
			backdrop = "rgba(225,17,17,0.4)";
		}
		
		
		swal({//Alert de confirmación
			title: title,
			type: type,
			text: text,
			html: html,
			showCancelButton: true,
			confirmButtonText: "Sí",
			cancelButtonText: "No",
			backdrop: backdrop
		}).then((resultado) => {
			if (resultado.value) {//Evento aceptar				
						swal({//Ventana temporal que muestra el progreso del ajax...
							title: "Espere un momento",
							imageUrl: "../imagenes/horizontal_bar.gif",
							allowOutsideClick: false,
							showConfirmButton: false,
							showCloseButton: false,
							onOpen: () => {
								//var observacion = ""; //JSON.stringify(resultado.value);
								var idPaciente = $("#hdd_idPaciente").val();
								params += "&totalMedicamentos=" + cantidadMedicamentosDespacho +
										  "&idFormulacion=" + idFormulaMedicamento +
										  "&observacion=" + observaciones +
										  "&idPaciente=" + idPaciente +
										  "&lugar_despacho=" + cmb_lugar_despacho;
								
								llamarAjax("despacho_medicamentos_ajax.php", params, "d_resultado_despacho", "validarGuardarDespacho();");
							},
							backdrop: backdrop
						});
			}
		});
	} else {
		alert_basico("Error", "Hay campos vacios o las cantidades por despachar no son correctas", "error");
	}
}

function validarGuardarDespacho() {
	var resultado = parseInt($("#hddResultadoDespacho").val(), 10);
	
	switch (true) {
		case resultado > 0:
			swal({//Ventana temporal que muestra el progreso del ajax...
				title: "El despacho ha sido realizado",
				type: "success",
				text: "¿Desea imprimir el reporte de salida de inventario?",
				showCancelButton: true,
				confirmButtonText: "Sí",
				cancelButtonText: "No",
				onOpen: () => {
					btnRegresar();
				}
			}).then((result) => {
				if (result.value) {//Imprimir reporte en PDF
					generar_reporte_despacho_medicamentos(resultado);
				}
			});
			break;
		default:
			var mensajeTipoError = "";
			var tipoError = 0;
			switch (resultado) {/*Errores del -1 al -4 pertenecen a bases de datos*/
				case - 1:
					tipoError = resultado;
					mensajeTipoError = "Error interno en el procedimiento de almacenado";
					break;
				case - 2:
					tipoError = resultado;
					mensajeTipoError = "Error, no hay cantidades pendientes por despachar para el medicamento";
					break;
				case - 3:
					tipoError = resultado;
					mensajeTipoError = "Error, la cantidad ingresada por depachar es mayor a la cantidad pendiente por despachar";
					break;
				case - 4:
					tipoError = resultado;
					mensajeTipoError = "Error, el medicamento no existe en la formulación";
					break;
				case - 5:
					tipoError = resultado;
					mensajeTipoError = "Error interno en PHP";
					break;
				case - 6://Error en la integración
					tipoError = resultado;
					var resultadoIntegracion = $("#hddResultadoIntegracionDespacho").val();
					mensajeTipoError = resultadoIntegracion;
					break;
				case - 7://Integracion exitosa pero Error en HCUT
					tipoError = resultado;
					mensajeTipoError = "Error, Descarga exitosa en SIESA pero, error interno en HCUT en el paso número 2 del pa_despachar_medicamentos";
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

function generar_reporte_despacho_medicamentos(idDespacho) {
	var params = "opcion=4" +
			"&idDespachoMedicamento=" + idDespacho;
	
	llamarAjax("despacho_medicamentos_ajax.php", params, "div_ruta_reporte_pdf", "imprimir_reporte_despacho_medicamentos();");
}

function imprimir_reporte_despacho_medicamentos() {
	var ruta = $("#hdd_ruta_reporte_pdf").val();
	window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=ordenMeicamentos.pdf", "_blank");
}

function buscar_diagnosticos_ciex(num) {
	
	var cod_ciex = $("#ciex_diagnostico_" + num).val();
	if (cod_ciex != "") {
		var params = "opcion=2&cod_ciex=" + cod_ciex + "&num=" + num;
		
		llamarAjax("../funciones/Class_Diagnosticos_ajax.php", params, "texto_diagnostico_" + num, "validar_ciex(" + num + ");");
	} else {
		$("#texto_diagnostico_" + num).html("");
		$("#hdd_ciex_diagnostico_" + num).val("");
		$("#valor_ojos_" + num).val("");
	}
}

function validar_ciex(num) {
	var texto_ciex = $("#hdd_texto_ciex_" + num).val();
	var codigo_ciex = $("#hdd_codigo_ciex_" + num).val();
	
	if (codigo_ciex == "") {
		$("#texto_diagnostico_" + num).html(texto_ciex);
		$("#texto_diagnostico_" + num).css({"color":"#FE6B6B"});
		$("#hdd_ciex_diagnostico_" + num).val("");
	} else {
		$("#texto_diagnostico_" + num).html(texto_ciex);
		$("#texto_diagnostico_" + num).css({"color":"#5B5B5B"});
		$("#ciex_diagnostico_" + num).val(codigo_ciex);
		$("#hdd_ciex_diagnostico_" + num).val(codigo_ciex);
	}
}

function ver_formulaciones_paciente(idPaciente, nombre, documento, tipoDocumento, telefonos, fechaNacimiento, edad, convenio, plan, estadoConvenio, tipoAccion) {
	var params = "opcion=5" +
				 "&idPaciente=" + idPaciente +
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
	
	llamarAjax("despacho_medicamentos_ajax.php", params, "contenedor_medicamentos", "");
}

function eliminar_medicamento(indice){
	
	var id_paciente = $("#hddIdPaciente").val();
	var id_lugar_formula_medica =  $("#hddIdLugarFormulaMedica" + indice).val();
	var formula_medicamento = $("#hddIdFormulaMedicamento").val();
	var formula_medicamento_det = $("#hddIdFormulaMedicamentoDet" + indice).val();
	var cod_siesa = $("#hddCodigoSiesa" + indice).val();
	var cantidad_pendiente = $("#hddCantPendiente" + indice).val();
	var cantidad_orden = $("#hddCantOrden" + indice).val(); 
	var cantidadMedicamentosDespacharTbl = parseInt($("#hddCantMedicamentosDespacho").val(), 10);
	var codigo_despacho = $("#txtNumDespacho").val().trim();
	
	if(codigo_despacho != ""){
		swal({//Ventana temporal que muestra el progreso del ajax...
							title: "Espere un momento",
							imageUrl: "../imagenes/horizontal_bar.gif",
							allowOutsideClick: false,
							showConfirmButton: false,
							showCloseButton: false,
							onOpen: () => {
							
							var params = "opcion=6"+
							 "&idPaciente=" 				+ id_paciente + 
							 "&idMedicamento_det=" 			+ formula_medicamento_det +
							 "&lugarFormula="   			+ id_lugar_formula_medica +
							 "&cantidad=" 					+ cantidad_orden +
							 "&cantidadPendiente=" 			+ cantidad_pendiente +
							 "&codSiesa=" 					+ cod_siesa +
							 "&cantidadMedicamentosTbl=" 	+ cantidadMedicamentosDespacharTbl +
							 "&codDespacho=" 				+ codigo_despacho +
							 "&idFormula=" 					+ formula_medicamento;
							 
							llamarAjax("despacho_medicamentos_ajax.php", params, "d_resultado_anulacion", "validar_eliminar_medicamento();");
							
							},
							
						});
	}else{
		alert("Debe ingresar el codigo del despacho que desea cancelar")
	}
	
				
}

function validar_eliminar_medicamento(){
	
	var rta = $("#hdd_rta_anulacion_medicamento").val();
	var resultado = $("#hdd_result_siesa_anulacion_medicamento").val();
	
	var id_paciente = $("#hdd_id_paciente").val();
	
	
	if(rta > 0){
			swal({
				title: "",
				type: "success",
				text: "Medicamento anulado",
				showConfirmButton: false,
				showCloseButton: true,
			});
			
			var params = "opcion=1" +
				 "&txt_paciente=" + id_paciente +
				 "&tipoDeAccion=" + 1;	
				 	 
			llamarAjax("despacho_medicamentos_ajax.php", params, "contenedor_medicamentos", "");
				
	}else{
	
		swal({
				title: rta,
				type: "error",
				text: resultado,
				showConfirmButton: false,
				showCloseButton: true,
		});	
	}
			
}


function imprimir_orden_medicamentos_despacho() {
	var idFormulaMedicamento = $("#hddIdFormulaMedicamento").val();
	var params = "opcion=2" +
			"&idFormulaMedicamento=" + idFormulaMedicamento;
	
	llamarAjax("../historia_clinica/reporte_ordenesRemisiones_ajax.php", params, "div_ruta_reporte_pdf", "generar_pdf_orden_medicamentos();");
}

function generar_pdf_orden_medicamentos() {
	var ruta = $("#hdd_ruta_ordenMericamentos_pdf").val();
	window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=ordenMeicamentos.pdf", "_blank");
}

function nuevo_paciente_btn() {
	var params = "opcion=10" +
				 "&ind_overflow=1" +
				 "&ind_ventana_flotante=1";
	
	llamarAjax("../admisiones/pacientes_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1);");
	
	//Oculta el div "contenedor_medicamentos"
	$("#contenedor_medicamentos").html("");
}
