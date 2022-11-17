/***********************************************/
/*Configuración del editor de texto enriquecido*/
/***********************************************/

if (CKEDITOR.env.ie && CKEDITOR.env.version < 9) {
	CKEDITOR.tools.enableHtml5Elements(document);
}

CKEDITOR.config.width = "auto";

var initCKEditorNotas = (function(id_obj) {
	var wysiwygareaAvailable = isWysiwygareaAvailable(),
		isBBCodeBuiltIn = !!CKEDITOR.plugins.get("bbcode");
	
	return function(id_obj) {
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
} )();

/***********************************************/
/***********************************************/
/***********************************************/

function buscar_admisiones() {
    $("#frm_buscar_notas").validate({
        rules: {
            txt_parametro: {
                required: true,
            },
        },
        submitHandler: function() {
            var params = "opcion=1&parametro=" + str_encode($("#txt_parametro").val()) +
						 "&fecha_admision=" + $("#txt_fecha_admision_b").val();
			
            llamarAjax("notas_no_asistenciales_ajax.php", params, "d_resultados_b", "");
            return false;
        },
    });
}

//Ventana modificar plantilla
function abrir_editar_nota(id_nota, id_admision) {
    var params = "opcion=2&id_nota=" + id_nota + "&id_admision=" + id_admision;
	
    llamarAjax("notas_no_asistenciales_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1); asigna_formato_textarea();");
}

function asigna_formato_textarea() {
   $(".div_centro").css({"text-align":"justify"});
   $(".div_interno").css("text-align","justify");
   $("body").css("text-align","justify");
}

//Procesa la impresion
function imprimir() {
	var tipo = $("#cmb_tipoImpresion").val();
    var idFormula = $("#hddidFormula").val();
    var text_despacho = CKEDITOR.instances.text_formula.getData();
    
	result = 0;
	$("#cmb_tipoImpresion").removeClass("borde_error");
	$("#d_contenedor_error_2").css("display", "none");
	
	if (tipo == "0") {
		$("#cmb_tipoImpresion").addClass("borde_error");
		result = 1;
	}
	
	if (result == 0) {
		var params = "opcion=6&idFormula=" + idFormula + "&tipoImpresion=" + tipo + "&text_despacho=" + text_despacho;
		llamarAjax("notas_no_asistenciales_ajax.php", params, "rtaImpresion", "imprSelec();");  
	} else {
		$("#d_contenedor_error_2").css("display", "block");
		$("#d_contenedor_error_2").html("Los campos marcados en rojo son obligatorios");
		return false;
	}
}

function imprSelec(){
	var ficha = document.getElementById("campo_imprimir");
	var ventimp = window.open(" ", "popimpr");
	ventimp.document.write(ficha.innerHTML);
	ventimp.document.close();
	ventimp.print();
	ventimp.close();
}

function validar_guardar_nota(id_tipo) {
	var resultado = true;
	var cant_notas = parseInt($("#hdd_cant_notas").val(), 10);
	for (var i = 0; i < cant_notas; i++) {
		$("#cke_text_nota_" + i).removeClass("bordeAdmision");
		$("#txt_fecha_nota_" + i).removeClass("bordeAdmision");
	}
	
	if (id_tipo == "1" || id_tipo == "2") {
		var texto_aux = "";
		for (var i = 0; i < cant_notas; i++) {
			if ($("#hdd_act_nota_" + i).val() == "1") {
				eval("texto_aux = trim(CKEDITOR.instances.text_nota_" + i + ".getData())");
				if (texto_aux == "") {
					$("#cke_text_nota_" + i).addClass("bordeAdmision");
					resultado = false;
				}
				
				if ($("#txt_fecha_nota_" + i).val() == "") {
					$("#txt_fecha_nota_" + i).addClass("bordeAdmision");
					resultado = false;
				}
			}
		}
	}
	
	return resultado;
}

function guardar_nota(id_tipo) {
	if (validar_guardar_nota(id_tipo)) {
		$("#btn_imprimir").attr("disabled", "disabled");
		$("#btn_guardar").attr("disabled", "disabled");
		
		var result = 0;
		var cant_notas = parseInt($("#hdd_cant_notas").val(), 10);
		$("#d_contenedor_error_2").css("display", "none");
		
		var params = "opcion=3&id_admision=" + $("#hdd_id_admision").val() +
					 "&id_paciente=" + $("#hdd_id_paciente").val() +
					 "&id_nota=" + $("#hdd_id_nota").val() +
					 "&id_tipo=" + id_tipo +
					 "&num_nota=" + parseInt($("#cmb_num_nota").val(), 10);
		
		var cont_aux = 0;
		for (var i = 0; i < cant_notas; i++) {
			if ($("#hdd_act_nota_" + i).val() == "1") {
				params += "&texto_nota_" + cont_aux + "=" + str_encode(eval("CKEDITOR.instances.text_nota_" + i + ".getData()")) +
						  "&fecha_nota_" + cont_aux + "=" + $("#txt_fecha_nota_" + i).val();
				
				cont_aux++;
			}
		}
		params += "&cant_notas=" + cont_aux;
		
		llamarAjax("notas_no_asistenciales_ajax.php", params, "d_resultado_guardar", "validar_exito(" + id_tipo + ")");
	} else {
		$("#d_contenedor_error_2").css("display", "block");
		$("#d_contenedor_error_2").html("Los campos marcados en rojo son obligatorios");
		window.scrollTo(0, 0);
	}
}

function validar_exito(id_tipo) {
	var hdd_exito = parseInt($("#hdd_resultado_nota").val(), 10);
	
	if (hdd_exito > 0) {
		if (id_tipo == 1) { //Solo guardar
			cerrar_div_centro();
			$("#contenedor_exito").css("display", "block");
			$("#contenedor_exito").html("Datos guardados correctamente");
			window.scrollTo(0, 0);
			setTimeout('$("#contenedor_exito").css("display", "none");', 2000);
		} else if (id_tipo == 2) { //Imprimir y guardar
			$("#d_contenedor_exito_2").css("display", "block");
			$("#d_contenedor_exito_2").html("Datos guardados correctamente");
			setTimeout('$("#d_contenedor_exito_2").css("display", "none");', 2000);
			
			var ruta = $("#hdd_ruta_arch_pdf").val();
			window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=nota_no_asistencial.pdf", "_blank");
		} else if (id_tipo == 3) { //Solo Imprimir
			var ruta = $("#hdd_ruta_arch_pdf").val();
			window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=nota_no_asistencial.pdf", "_blank");
		}
	} else if (hdd_exito == -3) {
		$("#d_contenedor_error_2").css("display", "block");
		$("#d_contenedor_error_2").html("Error al registrar el detalle de las notas no asistenciales");
		window.scrollTo(0, 0);
	} else if (hdd_exito == -1) {
		$("#d_contenedor_error_2").css("display", "block");
		$("#d_contenedor_error_2").html("Error interno al guardar las notas no asistenciales");
		window.scrollTo(0, 0);
	} else {
		$("#d_contenedor_error_2").css("display", "block");
		$("#d_contenedor_error_2").html("Error al guardar las notas no asistenciales");
		window.scrollTo(0, 0);
	}
	
	$("#btn_imprimir").removeAttr("disabled");
	$("#btn_guardar").removeAttr("disabled");
}

function mostrar_nota(id) {
	id = parseInt(id, 10);
	for (var i = 0; i < 20; i++) {
		if (id == i) {
			$("#d_detalle_nota_" + i).show();
		} else if ($("#d_detalle_nota_" + i).is(":visible")) {
			$("#d_detalle_nota_" + i).hide();
		}
	}
	
	ajustar_textareas();
}

function ocultar_nota(id) {
	$("#d_detalle_nota_" + id).hide();
}

function agregar_nota() {
	var cant_notas = parseInt($("#hdd_cant_notas").val(), 10);
	if (cant_notas < 20) {
		$("#hdd_act_nota_" + cant_notas).val(1);
		var opt_aux = new Option(cant_notas + 1, cant_notas);
		$(opt_aux).html(cant_notas + 1);
		$("#cmb_num_nota").append(opt_aux);
		
		$("#hdd_cant_notas").val(cant_notas + 1);
		$("#cmb_num_nota").val(cant_notas);
		mostrar_nota(cant_notas);
	} else {
		alert("Se ha alcanzado el n\xfamero m\xe1ximo de notas permitidas.");
	}
}

function restar_nota() {
	var num_nota = parseInt($("#cmb_num_nota").val(), 10);
	
	$("#hdd_act_nota_" + num_nota).val(0);
	ocultar_nota(num_nota);
	$("#cmb_num_nota option[value='" + num_nota + "']").remove();
	
	mostrar_nota($("#cmb_num_nota").val());
}

function ajustar_textareas() {
	for (var i in CKEDITOR.instances) {
		(function(i){
			CKEDITOR.instances[i].setData(CKEDITOR.instances[i].getData());
		})(i);
	}
}
