/***********************************************/
/*Configuración del editor de texto enriquecido*/
/***********************************************/

if (CKEDITOR.env.ie && CKEDITOR.env.version < 9) {
	CKEDITOR.tools.enableHtml5Elements(document);
}

CKEDITOR.config.height = 95;
CKEDITOR.config.width = "auto";

var initCKEditorExamen = (function(indice) {
	var wysiwygareaAvailable = isWysiwygareaAvailable(),
		isBBCodeBuiltIn = !!CKEDITOR.plugins.get("bbcode");
	
	return function(indice) {
		var editorElement = CKEDITOR.document.getById("txt_observaciones_examen_" + indice);
		
		//Dependiendo de la disponibilidad del plugin wysiwygare se incia el editor clásico o el editor en línea.
		if (wysiwygareaAvailable) {
			CKEDITOR.replace("txt_observaciones_examen_" + indice);
		} else {
			editorElement.setAttribute("contenteditable", "true");
			CKEDITOR.inline("txt_observaciones_examen_" + indice);
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

function mostrar_lista_tabla() {
	var lista_tabla = $("#hdd_cant_examenes_op").val();
	
	for (i = 1; i <= lista_tabla; i++) {
		$("#tabla_diag_" + i).css("display", "block");
	}
}

function obtener_extension_archivo(nombre_archivo) {
	var extension = nombre_archivo.substring(nombre_archivo.lastIndexOf(".") + 1).toLowerCase();
	
	return extension;
}

function limpiar_bordes_examenes() {
	var cant_aux = parseInt($("#hdd_cant_examenes_op").val(), 10);
	
	for (var i = 0; i < cant_aux; i++) {
		$("#cmb_examen_" + i).css({"border": "1px solid rgba(0,0,0,.2)"});
		$("#cmb_ojo_examen_" + i).css({"border": "1px solid rgba(0,0,0,.2)"});
		$("#fil_arch_examen_" + i).css({"border": "1px solid rgba(0,0,0,.2)"});
	}
}

function validar_archivos_examenes() {
	var resultado = true;
	var cant_aux = parseInt($("#hdd_cant_examenes_op").val(), 10);
	
	for (var i = 0; i < cant_aux; i++) {
		//Se verifica si es un tipo de archivo válido (jpg, png, bmp, gif, pdf)
		var ruta_arch_examen = $("#fil_arch_examen_" + i).val();
		if (ruta_arch_examen != "") {
			var extension = obtener_extension_archivo(ruta_arch_examen);
			if (extension != "jpg" && extension != "png" && extension != "bmp" && extension != "gif" && extension != "pdf") {
				$("#fil_arch_examen_" + i).css({"border": "2px solid #FF002A"});
				resultado = false;
			}
		}
	}
	
	if (!resultado) {
		alert("Los archivos a cargar deben ser im\xe1genes o archivos pdf");
	}
	
	return resultado;
}

function validar_examenes(tipo) {
	limpiar_bordes_examenes();
	
	var resultado = 1;
	var cant_aux = parseInt($("#hdd_cant_examenes_op").val(), 10);
	
	for (var i = 0; i < cant_aux; i++) {
		if ($("#cmb_examen_" + i).val() == "") {
			$("#cmb_examen_" + i).css({"border": "2px solid #FF002A"});
			resultado = 0;
		}
		if ($("#cmb_ojo_examen_" + i).val() == "") {
			$("#cmb_ojo_examen_" + i).css({"border": "2px solid #FF002A"});
			resultado = 0;
		}
	}
	
	return resultado;
}

function subir_archivos(ind_actualizar) {
	var cant_aux = parseInt($("#hdd_cant_examenes_op").val(), 10);
	
	for (var i = 0; i < cant_aux; i++) {
		$("#hdd_id_examen_sel_" + i).val($("#cmb_examen_" + i).val());
		$("#hdd_id_ojo_sel_" + i).val($("#cmb_ojo_examen_" + i).val());
		$("#hdd_ind_actualizar_" + i).val(ind_actualizar ? 1 : 0);
		$("#frm_arch_examen_" + i).submit();
		
		if (ind_actualizar) {
			document.getElementById("d_archivo_examen_" + i).innerHTML = "";
			cargar_archivo(i, 3000);
			cargar_componentes_carga(i, 0);
		}
	}
}

function cargar_archivo(indice, retardo) {
	var params = "opcion=2&id_hc=" + $("#hdd_id_hc_examen").val() +
				 "&id_examen_hc=" + $("#hdd_id_examen_hc_" + indice).val() +
				 "&indice=" + indice;
	
	if (retardo > 0) {
		setTimeout(function() {llamarAjax("../funciones/Class_Examenes_Op_ajax.php", params, "d_archivo_examen_" + indice, "");}, retardo);
	} else {
		llamarAjax("../funciones/Class_Examenes_Op_ajax.php", params, "d_archivo_examen_" + indice, "");
	}
}

function agregar_tabla_examen() {
	var cant_aux = parseInt($("#hdd_cant_examenes_op").val(), 10);
	
	if (cant_aux < 20) {
		var opt_aux = new Option(cant_aux + 1, cant_aux);
		$(opt_aux).html(cant_aux + 1);
		$("#cmb_num_examen").append(opt_aux);
		$("#cmb_num_examen").val(cant_aux);
		
		mostrar_examen(cant_aux)
		$("#hdd_cant_examenes_op").val(cant_aux + 1);
	}
} 

function restar_tabla_examen() {
	var cant_aux = parseInt($("#hdd_cant_examenes_op").val(), 10);
	var num_examen = parseInt($("#cmb_num_examen").val(), 10);
	
	if (cant_aux > 1) {
		cant_aux--;
		$("#cmb_examen_" + (cant_aux)).val("");
		$("#cmb_ojo_examen_" + (cant_aux)).val("");
		$("#d_archivo_examen_" + (cant_aux)).html("");
		eval("CKEDITOR.instances.txt_observaciones_examen_" + cant_aux + '.setData("");');
		$("#hdd_id_examen_hc_" + (cant_aux)).val("");
		$("#hdd_cant_examenes_op").val(cant_aux);
		
		if (num_examen >= cant_aux) {
			mostrar_examen(cant_aux - 1);
			$("#cmb_num_examen").val(cant_aux - 1);
		}
		
		$("#cmb_num_examen option").filter("[value='" + cant_aux + "']").remove();
	} else {
		alert("No se puede borrar el \xfanico examen registrado");
	}
}

function mostrar_examen(num_examen) {
	num_examen = parseInt(num_examen, 10);
	for (var i = 0; i < 20; i++) {
		if (i != num_examen) {
			$("#tr_examen_" + (i)).css("display", "none");
		} else {
			$("#tr_examen_" + (i)).css("display", "table-row");
		}
	}
}

function agregar_archivos_examen(indice) {
	var cant_archivos = parseInt($("#hdd_cant_archivos_" + indice).val(), 10);
	if (cant_archivos < 10) {
		$("#tr_archivo_" + indice + "_" + cant_archivos).css("display", "table-row");
		cant_archivos++;
		$("#hdd_cant_archivos_" + indice).val(cant_archivos)
	}
}

function restar_archivos_examen(indice) {
	var cant_archivos = parseInt($("#hdd_cant_archivos_" + indice).val(), 10);
	cant_archivos--;
	if (cant_archivos >= 0) {
		$("#tr_archivo_" + indice + "_" + cant_archivos).css("display", "none");
		$("#hdd_cant_archivos_" + indice).val(cant_archivos)
	}
}

function obtener_params_archivos_examen(indice) {
	var cant_archivos = parseInt($("#hdd_cant_archivos_" + indice).val(), 10);
	
	var params = "&cant_archivos_" + indice + "=" + cant_archivos;
	for (var i = 0; i < cant_archivos; i++) {
		params += "&ruta_arch_examen_" + indice + "_" + i + "=" + $("#fil_arch_examen_" + indice + "_" + i).val();
	}
	
	return params;
}

function cargar_componentes_carga(indice, ind_mostrar) {
	var params = "opcion=3&indice=" + indice +
				 "&ind_mostrar=" + ind_mostrar;
	
	llamarAjax("../funciones/Class_Examenes_Op_ajax.php", params, "d_input_carga_examenes_" + indice, "");
}

function mostrar_archivo_examen(indice, cont) {
	var cant_archivos_examen = parseInt($("#hdd_cant_archivos_examen_" + indice).val(), 10);
	
	for (var j = 0; j < cant_archivos_examen; j++) {
		$("#d_archivo_muestra_" + indice + "_" + j).css("display", "none");
		$("#sp_archivo_examen_" + indice + "_" + j).removeClass("active");
	}
	$("#d_archivo_muestra_" + indice + "_" + cont).css("display", "block");
	$("#sp_archivo_examen_" + indice + "_" + cont).addClass("active");
	$("#hdd_archivo_visible_" + indice).val(cont);
}

function mostrar_archivo_pantalla_completa(indice) {
	var contador = $("#hdd_archivo_visible_" + indice).val();
	
	//Se lleva la imagen a su tamaño y posición originales
	$("#img_drag_" + indice + "_" + contador).css("top", "0px");
	$("#img_drag_" + indice + "_" + contador).css("left", "0px");
	$("#hdd_zoom_act_" + indice + "_" + contador).val(-1);
	manejar_zoom_imagen("", indice, contador);
	
	$("#d_archivo_completo_" + indice + "_" + contador).css("display", "block");
}

function ocultar_archivo_pantalla_completa(indice) {
	var contador = $("#hdd_archivo_visible_" + indice).val();
	$("#d_archivo_completo_" + indice + "_" + contador).css("display", "none");
}

function borrar_archivo_examen(indice) {
	mostrar_formulario_flotante(1);
	$("#d_interno").html(
		'<table class="datagrid" border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
			'<tr class="headegrid">' +
				'<th align="center" class="msg_alerta" style="border: 1px solid #fff;">' +
					'<h3>&iquest;Est&aacute; seguro que desea borrar el archivo?</h3>' +
				'</th>' +
			'</tr>' +
			'<tr>' +
				'<th align="center" style="width:5%;border: 1px solid #fff;">' +
					'<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" value="Aceptar" class="btnPrincipal" onclick="borrar_archivo_examen_cont(' + indice + ');"/>' +
					'&nbsp;&nbsp;' +
					'<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" value="Cancelar" class="btnSecundario" onclick="cerrar_div_centro();"/>' +
				'</th>' +
			'</tr>' +
		'</table>');
		posicionarDivFlotante("d_centro");
}

function borrar_archivo_examen_cont(indice) {
	var cant_archivos = parseInt($("#hdd_cant_archivos_examen_" + indice).val(), 10);
	var cont = -1;
	for (var j = 0; j < cant_archivos; j++) {
		if ($("#d_archivo_muestra_" + indice + "_" + j).is(":visible")) {
			cont = j;
			break;
		}
	}
	
	if (cont >= 0) {
		var params = "opcion=4&id_examen_hc_det=" + $("#hdd_id_examen_hc_det_" + indice + "_" + cont).val();
		
		llamarAjax("../funciones/Class_Examenes_Op_ajax.php", params, "d_borrar_archivo_examen", "validar_borrado_arch_examen(" + indice + ");");
	}
}

function validar_borrado_arch_examen(indice) {
	cerrar_div_centro();
	var resultado = parseInt($("#hdd_resul_borrar_arch_examen").val(), 10);
	
	if (resultado > 0) {
		$("#contenedor_exito").css("display", "block");
		$("#contenedor_exito").html("Archivo borrado correctamente");
		setTimeout('$("#contenedor_exito").css("display", "none")', 3000);
		cargar_archivo(indice, 0);
	} else if (resultado == -1)  {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error interno al tratar de borrar el archivo");
	} else  {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error al tratar de borrar el archivo");
	}
}

//Función para obtener la posición del mouse
var g_mouse_pos = {x: -1, y: -1};
jQuery(function($) {
    $(document).mousemove(function(event) {
        g_mouse_pos.x = event.pageX;
        g_mouse_pos.y = event.pageY;
    });
});

function manejar_zoom_imagen(direccion, indice, contador) {
	var arr_zoom = [12.5, 25, 33.33, 50, 66.67, 75, 100, 125, 150, 200, 300, 400, 600, 800];
	var zoom_min = -6;
	var zoom_max = 7;
	
	var zoom_act = parseInt($("#hdd_zoom_act_" + indice + "_" + contador).val(), 10);
	var val_zoom_ini = arr_zoom[zoom_act - zoom_min];
	switch (direccion) {
		case "U":
			zoom_act++;
			break;
		case "D":
			zoom_act--;
			break;
		default:
			zoom_act = 0;
			break;
	}
	if (zoom_act >= zoom_min && zoom_act <= zoom_max) {
		//Tamaño
		var val_zoom_aux = arr_zoom[zoom_act - zoom_min];
		var ancho_aux = Math.round(parseInt($("#hdd_ancho_act_" + indice + "_" + contador).val(), 10) * val_zoom_aux / 100);
		var alto_aux = Math.round(parseInt($("#hdd_alto_act_" + indice + "_" + contador).val(), 10) * val_zoom_aux / 100);
		
		$("#img_drag_" + indice + "_" + contador).css("width", ancho_aux + "px");
		$("#img_drag_" + indice + "_" + contador).css("height", alto_aux + "px");
		
		$("#d_label_zoom_" + indice + "_" + contador).html("<b>Zoom: " + val_zoom_aux + "%</b>");
		
		$("#hdd_zoom_act_" + indice + "_" + contador).val(zoom_act);
		
		//Posición
		if (direccion == "U" || direccion == "D") {
			var left_aux = $("#img_drag_" + indice + "_" + contador).css("left");
			left_aux = parseInt(left_aux.substring(0, left_aux.length - 2), 10);
			left_aux = Math.round((g_mouse_pos.x - left_aux) * val_zoom_aux / val_zoom_ini) * -1 + g_mouse_pos.x;
			$("#img_drag_" + indice + "_" + contador).css("left", left_aux + "px");
			
			var top_aux = $("#img_drag_" + indice + "_" + contador).css("top");
			top_aux = parseInt(top_aux.substring(0, top_aux.length - 2), 10);
			top_aux = Math.round((g_mouse_pos.y - top_aux) * val_zoom_aux / val_zoom_ini) * -1 + g_mouse_pos.y;
			$("#img_drag_" + indice + "_" + contador).css("top", top_aux + "px");
		}
	}
	
	return false;
}

function seleccionar_examen_op(id_examen, indice) {
	var params = "opcion=5&id_examen=" + id_examen + "&indice=" + indice;
	
	llamarAjax("../funciones/Class_Examenes_Op_ajax.php", params, "d_examen_compl_b_" + indice, "continuar_seleccionar_examen_op(" + indice + ");");
}

function continuar_seleccionar_examen_op(indice) {
	var id_examen_compl = $("#hdd_examen_compl_b_" + indice).val();
	var observacion_predef = $("#d_observacion_predef_b_" + indice).html();
	
	$("#tr_pu_" + indice).css("display", "none");
	$("#txt_pu_od_" + indice).val("");
	$("#txt_pu_oi_" + indice).val("");
	
	switch (id_examen_compl) {
		case "344": //Paquimetría ultrasónica
			$("#tr_pu_" + indice).css("display", "table-row");
			break;
	}
	
	var observaciones_examen = eval("CKEDITOR.instances.txt_observaciones_examen_" + indice + ".getData()");
	if (observaciones_examen == "" && observacion_predef != "") {
		eval("CKEDITOR.instances.txt_observaciones_examen_" + indice + ".setData(observacion_predef);");
	}
}

function obtener_nombres_comp_archivos() {
	var cant_examenes_op = parseInt($("#hdd_cant_examenes_op").val(), 10);
	
	var cadena_nombres = "";
	for (var i = 0; i < cant_examenes_op; i++) {
		var cant_archivos = parseInt($("#hdd_cant_archivos_" + i).val(), 10);
		for (var j = 0; j < cant_archivos; j++) {
			if ($("#fil_arch_examen_" + i + "_" + j).val() != "") {
				if (cadena_nombres != "") {
					cadena_nombres += ";";
				}
				cadena_nombres += "fil_arch_examen_" + i + "_" + j;
			}
		}
	}
	
	return cadena_nombres;
}

function refrescar_archivos() {
	var cant_aux = parseInt($("#hdd_cant_examenes_op").val(), 10);
	
	for (var i = 0; i < cant_aux; i++) {
		document.getElementById("d_archivo_examen_" + i).innerHTML = "";
		cargar_archivo(i, 0);
		cargar_componentes_carga(i, 0);
	}
}
