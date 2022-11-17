// JavaScript Document

function mostrar_detalle_imagen_hc(id_examen) {
	var ind_titulo_imagen_hc = parseInt($("#hdd_titulo_imagen_hc_" + id_examen).val(), 10);
	
	if (ind_titulo_imagen_hc == 0) {
		//Mostrar detalle
		$("#img_titulo_imagen_hc_down_" + id_examen).css("display", "none");
		$("#img_titulo_imagen_hc_up_" + id_examen).css("display", "inline-block");
		$("#hdd_titulo_imagen_hc_" + id_examen).val(1);
		$("#d_lista_imagen_hc_" + id_examen).show(200);
	} else {
		//Ocultar detalle
		$("#img_titulo_imagen_hc_down_" + id_examen).css("display", "inline-block");
		$("#img_titulo_imagen_hc_up_" + id_examen).css("display", "none");
		$("#hdd_titulo_imagen_hc_" + id_examen).val(0);
		$("#d_lista_imagen_hc_" + id_examen).hide(200);
	}
}

function cargar_archivos_imagen_hc(id_paciente, retardo) {
	var params = "opcion=1&id_paciente=" + id_paciente;
	
	if (retardo > 0) {
		setTimeout(function() {llamarAjax("ver_imagenes_hc_ajax.php", params, "d_ver_imagenes_hc", "");}, retardo);
	} else {
		llamarAjax("ver_imagenes_hc_ajax.php", params, "d_ver_imagenes_hc", "");
	}
}

function mostrar_archivo_imagen_hc(id_examen_hc_det) {
	var cant_archivos_imagen_hc = parseInt($("#hdd_cant_archivos_imagen_hc").val(), 10);
	
	for (var i = 0; i < cant_archivos_imagen_hc; i++) {
		var id_examen_hc_det_aux = $("#hdd_id_examen_hc_det_" + i).val();
		if (id_examen_hc_det_aux == id_examen_hc_det) {
			$("#d_archivo_imagen_hc_" + id_examen_hc_det_aux).css("display", "block");
		} else {
			$("#d_archivo_imagen_hc_" + id_examen_hc_det_aux).css("display", "none");
		}
	}
}

function mostrar_archivo_imagen_hc_indice(indice) {
	mostrar_archivo_imagen_hc($("#hdd_id_examen_hc_det_" + indice).val());
}

//Función para obtener la posición del mouse
var g_mouse_pos = {x: -1, y: -1};
jQuery(function($) {
    $(document).mousemove(function(event) {
        g_mouse_pos.x = event.pageX;
        g_mouse_pos.y = event.pageY;
    });
});

function manejar_zoom_imagen(direccion, id) {
	var arr_zoom = [12.5, 25, 33.33, 50, 66.67, 75, 100, 125, 150, 200, 300, 400, 600, 800];
	var zoom_min = -6;
	var zoom_max = 7;
	
	var zoom_act = parseInt($("#hdd_zoom_act_" + id).val(), 10);
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
		var ancho_aux = Math.round(parseInt($("#hdd_ancho_act_" + id).val(), 10) * val_zoom_aux / 100);
		var alto_aux = Math.round(parseInt($("#hdd_alto_act_" + id).val(), 10) * val_zoom_aux / 100);
		
		$("#img_drag_" + id).css("width", ancho_aux + "px");
		$("#img_drag_" + id).css("height", alto_aux + "px");
		
		$("#d_label_zoom_" + id).html("<b>Zoom: " + val_zoom_aux + "%</b>");
		
		$("#hdd_zoom_act_" + id).val(zoom_act);
		
		//Posición
		if (direccion == "U" || direccion == "D") {
			var left_aux = $("#img_drag_" + id).css("left");
			left_aux = parseInt(left_aux.substring(0, left_aux.length - 2), 10);
			left_aux = Math.round((g_mouse_pos.x - left_aux - 325) * val_zoom_aux / val_zoom_ini) * -1 + g_mouse_pos.x - 325;
			$("#img_drag_" + id).css("left", left_aux + "px");
			
			var top_aux = $("#img_drag_" + id).css("top");
			top_aux = parseInt(top_aux.substring(0, top_aux.length - 2), 10);
			top_aux = Math.round((g_mouse_pos.y - top_aux - 125) * val_zoom_aux / val_zoom_ini) * -1 + g_mouse_pos.y - 125;
			$("#img_drag_" + id).css("top", top_aux + "px");
		}
	}
	
	return false;
}

function imprimir_imagenes_hc(id_paciente) {
	$("#d_btn_impr_img_hc_1").css("display", "none");
	$("#d_btn_impr_img_hc_2").css("display", "block");
	
	var params = "id_paciente=" + id_paciente + "&ind_imagenes_hc=1";
	
	llamarAjax("../historia_clinica/impresion_historia_clinica.php", params, "d_impresion_img_hc", "continuar_imprimir_imagenes_hc();");
}

function continuar_imprimir_imagenes_hc() {
	var ruta = $("#hdd_ruta_arch_hc_pdf").val();
	window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=imagenes_hc.pdf", "_blank");
	
	$("#d_btn_impr_img_hc_1").css("display", "block");
	$("#d_btn_impr_img_hc_2").css("display", "none");
}
