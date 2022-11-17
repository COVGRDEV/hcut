//Funcion para generar el reporte general
function consultar_integracion() {
	var bol_continuar = true;
	$("#contenedor_error").css("display", "none");
	
	$("#txt_fecha_ini").removeClass("borde_error");
	$("#txt_fecha_fin").removeClass("borde_error");
	$("#cmb_entidad").removeClass("borde_error");
	
	var fecha_ini = $("#txt_fecha_ini").val();
	var fecha_fin = $("#txt_fecha_fin").val();
	var id_prestador = $("#cmb_entidad").val();
	
	if (fecha_ini == "") {
		$("#txt_fecha_ini").addClass("borde_error");
		bol_continuar = false;
	}
	if (fecha_fin == "") {
		$("#txt_fecha_fin").addClass("borde_error");
		bol_continuar = false;
	}
	if (id_prestador == "") {
		$("#cmb_entidad").addClass("borde_error");
		bol_continuar = false;
	}
	
	if (bol_continuar) {
		var params = "opcion=1&fecha_ini=" + str_encode(fecha_ini) +
					 "&fecha_fin=" + str_encode(fecha_fin) +
					 "&id_prestador=" + id_prestador;
		
		llamarAjax("reporte_integracion_ajax.php", params, "d_reporte", "");
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Los campos marcados en rojo son obligatorios.");
		window.scroll(0, 0);
	}
}
