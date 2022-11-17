/***********************************************/
/*Configuración del editor de texto enriquecido*/
/***********************************************/

if (CKEDITOR.env.ie && CKEDITOR.env.version < 9) {
	CKEDITOR.tools.enableHtml5Elements(document);
}

CKEDITOR.config.width = 'auto';

var initCKEditorObservacionPredef = (function(id_obj) {
	var wysiwygareaAvailable = isWysiwygareaAvailable(),
		isBBCodeBuiltIn = !!CKEDITOR.plugins.get('bbcode');
	
	return function(id_obj) {
		var editorElement = CKEDITOR.document.getById(id_obj);
		
		//Dependiendo de la disponibilidad del plugin wysiwygare se incia el editor clásico o el editor en línea.
		if (wysiwygareaAvailable) {
			CKEDITOR.replace(id_obj);
		} else {
			editorElement.setAttribute('contenteditable', 'true');
			CKEDITOR.inline(id_obj);
		}
	};
	
	function isWysiwygareaAvailable() {
		if (CKEDITOR.revision == ('%RE' + 'V%')) {
			return true;
		}
		
		return !!CKEDITOR.plugins.get('wysiwygarea');
	}
} )();

/***********************************************/
/***********************************************/
/***********************************************/

function buscar_medicamento() {
	$("#frm_buscar_medicamento").validate({
		rules: {
			txt_parametro: {
				required: true,
			},
		},
		submitHandler: function () {
			var params = "opcion=1&parametro=" + str_encode($("#txt_parametro").val());
			
			llamarAjax("medicamentos_ajax.php", params, "d_principal_medicamentos", "");
			return false;
		},
	});
}

//Funcion que muestra la ventana para editar procedimientos
function seleccionar_medicamento(cod_medicamento) {
	var params = "opcion=2&tipo=0&cod_medicamento=" + cod_medicamento;
	
	llamarAjax("medicamentos_ajax.php", params, "d_interno_adic", "mostrar_formulario_flotante_adic(1);");
}


//Funcion que muestra la ventana para nuevo procedimiento
function nuevo_medicamento() {
	var params = "opcion=2&tipo=1&cod_medicamento=";
	
	llamarAjax("medicamentos_ajax.php", params, "d_interno_adic", "mostrar_formulario_flotante_adic(1);");
}



function crear_medicamento() {
	
	
	$("#frm_nuevo_medicamento").validate({
		rules: {
		
			txt_cod_medicamento_siesa: {
				required: true,
			},
			txt_nombre_generico: {
				required: true,
			},
			txt_nombre_comercial: {
				required: true,
			},
			
			txt_presentacion: {
				required: true,
			},
			txt_unidad_medida: {
				required: true,
			}
		},
		submitHandler: function () {
			var params = "opcion=3&tipo=1&cod_medicamento=1" +
						 "&cod_medicamento_siesa=" + str_encode($("#txt_cod_medicamento_siesa").val()) +
						 "&nombre_generico=" + str_encode($("#txt_nombre_generico").val()) +
						 "&nombre_comercial=" + str_encode($("#txt_nombre_comercial").val()) +
						 "&presentacion=" + str_encode($("#txt_presentacion").val()) +
						 "&unidad_medida=" + str_encode($("#txt_unidad_medida").val()) +
						 "&ind_activo=" + ($("#chk_activo").is(":checked") ? 1 : 0);
						 
			llamarAjax("medicamentos_ajax.php", params, "d_resultado_medicamento", "verificar_crear_editar()")
			return false;
		},
	});
}

function editar_medicamento() {
	$("#frm_nuevo_medicamento").validate({
		rules: {
			txt_cod_medicamento_siesa: {
				required: true,
			},
			txt_nombre_generico: {
				required: true,
			},
			txt_nombre_comercial: {
				required: true,
			},
			
			txt_presentacion: {
				required: true,
			},
			txt_unidad_medida: {
				required: true,
			}
		},
		submitHandler: function () {
			var params = "opcion=3&tipo=0&cod_medicamento=" + $("#txt_cod_medicamento").val() +
						 "&cod_medicamento_siesa=" + str_encode($("#txt_cod_medicamento_siesa").val()) +
						 "&nombre_generico=" + str_encode($("#txt_nombre_generico").val()) +
						 "&nombre_comercial=" + str_encode($("#txt_nombre_comercial").val()) +
						 "&presentacion=" + str_encode($("#txt_presentacion").val()) +
						 "&unidad_medida=" + str_encode($("#txt_unidad_medida").val()) +
						 "&ind_activo=" + ($("#chk_activo").is(":checked") ? 1 : 0);
			
			llamarAjax("medicamentos_ajax.php", params, "d_resultado_medicamento", "verificar_crear_editar()")
			return false;
		},
	});
}

function verificar_crear_editar() {
	var resultado = parseInt($("#hdd_resul_medicamento").val(), 10);
	
	if (resultado > 0) {
		$("#contenedor_exito").css("display", "block");
		$("#contenedor_exito").html("Medicamento guardado con &eacute;xito");
		
		if ($("#txt_parametro").val() != "") {
			$("#frm_buscar_medicamento").submit();
		}
		
		setTimeout(function () {
			$("#contenedor_exito").css("display", "none");
		}, 3000);
	} else if (resultado = -3) {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("El c&oacute;digo del m&eacute;dicamento ya ha sido registrado");
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error al guardar el medicamento");
	}
	
	cerrar_div_centro_adic();
}

/*function cargar_actualizacion() {
	var params = "opcion=4";
	
	llamarAjax("procedimientos_ajax.php", params, "d_interno_adic", "mostrar_formulario_flotante_adic(1);");
}

function validar_procesar_archivo() {
	$("#contenedor_error").css("display", "none");
	
	if ($("#fil_actualizacion").val() == "") {
		alert("Debe seleccionar por lo menos un archivo");
		$("#fil_actualizacion").focus();
		return false;
	}
	
	return true;
}

function procesar_archivo() {
	$("#btn_procesar_arch").attr("disabled", "disabled");
	if (validar_procesar_archivo()) {
		var params = "opcion=5&ind_inhabilitar=" + ($("#chk_inhabilitar").is(":checked") ? 1 : 0);
		
		llamarAjaxUploadFiles("procedimientos_ajax.php", params, "d_resultado_procedimiento", "continuar_procesar_archivo();", "d_barra_progreso_adj", "fil_actualizacion");
	} else {
		$("#btn_procesar_arch").removeAttr("disabled");
	}
}

function continuar_procesar_archivo() {
	var resultado = $("#hdd_resul_actualizar").val();
	if (resultado > 0) {
		$("#contenedor_exito").css("display", "block");
		$("#contenedor_exito").html("Procedimientos actualizados con &eacute;xito");
		
		setTimeout(function () {
			$("#contenedor_exito").css("display", "none");
		}, 3000);
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error interno al tratar de actualizar los procedimientos");
	}
	
	$("#btn_procesar_arch").removeAttr("disabled");
	cerrar_div_centro_adic();
}

function seleccionar_especialidad(id_especialidad) {
	var params = "opcion=6&id_especialidad=" + id_especialidad;
	
	llamarAjax("procedimientos_ajax.php", params, "d_via_especialidad", "");
}
*/