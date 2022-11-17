function imprimir_anexo_hc() {
	var params = "id_hc=" + $("#hdd_id_hc").val();
	
	llamarAjax("../historia_clinica/impresion_historia_clinica.php", params, "d_impresion_hc", "continuar_imprimir_anexo_hc();");
}

function continuar_imprimir_anexo_hc() {
	var ruta = $("#hdd_ruta_arch_hc_pdf").val();
	window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=anexo.pdf", "_blank");
}

function mostrar_archivo_pantalla_completa() {
	//Se lleva la imagen a su tamaño y posición originales
	$("#img_drag").css("top", "0px");
	$("#img_drag").css("left", "0px");
	$("#hdd_zoom_act").val(-1);
	manejar_zoom_imagen("");
	
	$("#d_archivo_completo").css("display", "block");
}

function ocultar_archivo_pantalla_completa() {
	$("#d_archivo_completo").css("display", "none");
}

//Función para obtener la posición del mouse
var g_mouse_pos = {x: -1, y: -1};
jQuery(function($) {
    $(document).mousemove(function(event) {
        g_mouse_pos.x = event.pageX;
        g_mouse_pos.y = event.pageY;
    });
});

function manejar_zoom_imagen(direccion) {
	var arr_zoom = [12.5, 25, 33.33, 50, 66.67, 75, 100, 125, 150, 200, 300, 400, 600, 800];
	var zoom_min = -6;
	var zoom_max = 7;
	
	var zoom_act = parseInt($("#hdd_zoom_act").val(), 10);
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
		var ancho_aux = Math.round(parseInt($("#hdd_ancho_act").val(), 10) * val_zoom_aux / 100);
		var alto_aux = Math.round(parseInt($("#hdd_alto_act").val(), 10) * val_zoom_aux / 100);
		
		$("#img_drag").css("width", ancho_aux + "px");
		$("#img_drag").css("height", alto_aux + "px");
		
		$("#d_label_zoom").html("<b>Zoom: " + val_zoom_aux + "%</b>");
		
		$("#hdd_zoom_act").val(zoom_act);
		
		//Posición
		if (direccion == "U" || direccion == "D") {
			var left_aux = $("#img_drag").css("left");
			left_aux = parseInt(left_aux.substring(0, left_aux.length - 2), 10);
			left_aux = Math.round((g_mouse_pos.x - left_aux) * val_zoom_aux / val_zoom_ini) * -1 + g_mouse_pos.x;
			$("#img_drag").css("left", left_aux + "px");
			
			var top_aux = $("#img_drag").css("top");
			top_aux = parseInt(top_aux.substring(0, top_aux.length - 2), 10);
			top_aux = Math.round((g_mouse_pos.y - top_aux) * val_zoom_aux / val_zoom_ini) * -1 + g_mouse_pos.y;
			$("#img_drag").css("top", top_aux + "px");
		}
	}
	
	return false;
}
