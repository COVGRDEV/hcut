// JavaScript Document

function cambiar_color_pick(indice) {
	var color_act = parseInt($("#hdd_cp_color" + indice).val(), 10);
	var color_new = (color_act + 1) % 4;
	
	$("#hdd_cp_color" + indice).val(color_new);
	$("#d_cp" + indice).removeClass("color_pick_" + color_act);
	$("#d_cp" + indice).addClass("color_pick_" + color_new);
	
	$("#" + $("#hdd_cp_componente" + indice).val()).removeClass("componente_color_pick_" + color_act);
	$("#" + $("#hdd_cp_componente" + indice).val()).addClass("componente_color_pick_" + color_new);
}

function obtener_cadena_colores(sufijo) {
	if (typeof sufijo === "undefined") {
		sufijo = "";
	} else {
		sufijo = "_" + sufijo;
	}
	
	var cant_color_pick = parseInt($("#hdd_cant_color_pick" + sufijo).val(), 10);
	var cadena_colores = "";
	
	for (var i = 0; i < cant_color_pick; i++) {
		if ($("#hdd_cp_color" + sufijo + "_" + i).length) {
			cadena_colores += $("#hdd_cp_color" + sufijo + "_" + i).val();
		} else {
			cadena_colores += "0";
		}
	}
	
	return cadena_colores;
}
