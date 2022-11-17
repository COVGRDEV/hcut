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

function buscar_procedimiento() {
	$("#frm_buscar_procedimiento").validate({
		rules: {
			txt_parametro: {
				required: true,
			},
		},
		submitHandler: function () {
			var params = "opcion=1&parametro=" + str_encode($("#txt_parametro").val());
			
			llamarAjax("procedimientos_ajax.php", params, "d_principal_procedimientos", "");
			return false;
		},
	});
}

//Funcion que muestra la ventana para nuevo procedimiento
function nuevo_procedimiento() {
	var params = "opcion=2&tipo=1&cod_procedimiento=";
	
	llamarAjax("procedimientos_ajax.php", params, "d_interno_adic", "mostrar_formulario_flotante_adic(1);");
}

//Funcion que muestra la ventana para editar procedimientos
function seleccionar_procedimiento(cod_procedimiento) {
	var params = "opcion=2&tipo=0&cod_procedimiento=" + cod_procedimiento;
	
	llamarAjax("procedimientos_ajax.php", params, "d_interno_adic", "mostrar_formulario_flotante_adic(1);");
}

function crear_procedimiento() {
	$("#frm_nuevo_procedimiento").validate({
		rules: {
			txt_cod_procedimiento: {
				required: true,
			},
			txt_nombre_procedimiento: {
				required: true,
			},
		},
		submitHandler: function () {
			var params = "opcion=3&tipo=1&cod_procedimiento=" + $("#txt_cod_procedimiento").val() +
						 "&nombre_procedimiento=" + str_encode($("#txt_nombre_procedimiento").val()) +
						 "&id_especialidad=" + str_encode($("#cmb_especialidad").val()) +
						 "&id_via=" + str_encode($("#cmb_via").val()) +
						 "&ind_proc_qx=" + ($("#chk_proc_qx").is(":checked") ? 1 : 0) +
						 "&ind_activo=" + ($("#chk_activo").is(":checked") ? 1 : 0);
			
			llamarAjax("procedimientos_ajax.php", params, "d_resultado_procedimiento", "verificar_crear_editar();");
			return false;
		},
	});
}

function editar_procedimiento() {
	$("#frm_nuevo_procedimiento").validate({
		rules: {
			txt_nombre_procedimiento: {
				required: true,
			}
		},
		submitHandler: function () {
			var params = "opcion=3&tipo=0&cod_procedimiento=" + $("#txt_cod_procedimiento").val() +
						 "&nombre_procedimiento=" + str_encode($("#txt_nombre_procedimiento").val()) +
						 "&id_especialidad=" + str_encode($("#cmb_especialidad").val()) +
						 "&id_via=" + str_encode($("#cmb_via").val()) +
						 "&ind_proc_qx=" + ($("#chk_proc_qx").is(":checked") ? 1 : 0) +
						 "&ind_activo=" + ($("#chk_activo").is(":checked") ? 1 : 0);
			
			llamarAjax("procedimientos_ajax.php", params, "d_resultado_procedimiento", "verificar_crear_editar();");
			return false;
		},
	});
}

function verificar_crear_editar() {
	var resultado = parseInt($("#hdd_resul_procedimiento").val(), 10);
	
	if (resultado > 0) {
		$("#contenedor_exito").css("display", "block");
		$("#contenedor_exito").html("Procedimiento guardado con &eacute;xito");
		
		if ($("#txt_parametro").val() != "") {
			$("#frm_buscar_procedimiento").submit();
		}
		
		setTimeout(function () {
			$("#contenedor_exito").css("display", "none");
		}, 3000);
	} else if (resultado = -3) {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("El c&oacute;digo del procedimiento ya ha sido registrado");
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error al guardar el procedimiento");
	}
	
	cerrar_div_centro_adic();
}

function cargar_actualizacion() {
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
