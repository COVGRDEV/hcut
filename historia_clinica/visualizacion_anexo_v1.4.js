function imprimir_anexo_hc() {
	$("#btn_imprimir").attr("disabled", "disabled");
	$("#d_img_espera_anexo").css("display", "block");
	
	var params = "id_hc=" + $("#hdd_id_hc").val();
	
	llamarAjax("../historia_clinica/impresion_historia_clinica.php", params, "d_impresion_hc", "continuar_imprimir_anexo_hc();");
}

function continuar_imprimir_anexo_hc() {
	var ruta = $("#hdd_ruta_arch_hc_pdf").val();
	window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=anexo.pdf", "_blank");
	
	$("#d_img_espera_anexo").css("display", "none");
	$("#btn_imprimir").removeAttr("disabled");
}

function mostrar_archivo_pantalla_completa(indice) {
	//Se lleva la imagen a su tamaño y posición originales
	$("#img_drag_" + indice).css("top", "0px");
	$("#img_drag_" + indice).css("left", "0px");
	$("#hdd_zoom_act_" + indice).val(-1);
	manejar_zoom_imagen("", indice);
	
	$("#d_archivo_completo_" + indice).css("display", "block");
}

function ocultar_archivo_pantalla_completa(indice) {
	$("#d_archivo_completo_" + indice).css("display", "none");
}

//Función para obtener la posición del mouse
var g_mouse_pos = {x: -1, y: -1};
jQuery(function($) {
    $(document).mousemove(function(event) {
        g_mouse_pos.x = event.pageX;
        g_mouse_pos.y = event.pageY;
    });
});

function manejar_zoom_imagen(direccion, indice) {
	var arr_zoom = [12.5, 25, 33.33, 50, 66.67, 75, 100, 125, 150, 200, 300, 400, 600, 800];
	var zoom_min = -6;
	var zoom_max = 7;
	
	var zoom_act = parseInt($("#hdd_zoom_act_" + indice).val(), 10);
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
		var ancho_aux = Math.round(parseInt($("#hdd_ancho_act_" + indice).val(), 10) * val_zoom_aux / 100);
		var alto_aux = Math.round(parseInt($("#hdd_alto_act_" + indice).val(), 10) * val_zoom_aux / 100);
		
		$("#img_drag_" + indice).css("width", ancho_aux + "px");
		$("#img_drag_" + indice).css("height", alto_aux + "px");
		
		$("#d_label_zoom_" + indice).html("<b>Zoom: " + val_zoom_aux + "%</b>");
		
		$("#hdd_zoom_act_" + indice).val(zoom_act);
		
		//Posición
		if (direccion == "U" || direccion == "D") {
			var left_aux = $("#img_drag_" + indice).css("left");
			left_aux = parseInt(left_aux.substring(0, left_aux.length - 2), 10);
			left_aux = Math.round((g_mouse_pos.x - left_aux) * val_zoom_aux / val_zoom_ini) * -1 + g_mouse_pos.x;
			$("#img_drag_" + indice).css("left", left_aux + "px");
			
			var top_aux = $("#img_drag_" + indice).css("top");
			top_aux = parseInt(top_aux.substring(0, top_aux.length - 2), 10);
			top_aux = Math.round((g_mouse_pos.y - top_aux) * val_zoom_aux / val_zoom_ini) * -1 + g_mouse_pos.y;
			$("#img_drag_" + indice).css("top", top_aux + "px");
		}
	}
	
	return false;
}

function mostrar_archivo_anexo(indice) {
	var cant_archivos_anexos = parseInt($("#hdd_cant_archivos_anexos").val(), 10);
	
	for (var j = 0; j < cant_archivos_anexos; j++) {
		$("#d_archivo_muestra_" + j).css("display", "none");
		$("#tr_zoom_in_" + j).css("display", "none");
		$("#sp_archivo_adjunto_" + j).removeClass("active");
	}
	$("#d_archivo_muestra_" + indice).css("display", "block");
	$("#tr_zoom_in_" + indice).css("display", "table-row");
	$("#sp_archivo_adjunto_" + indice).addClass("active");
	$("#hdd_archivo_visible").val(indice);
}
