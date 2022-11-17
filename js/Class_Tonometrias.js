// JavaScript Document

function mostrar_tonometria() {
	var lista_tabla = $("#cant_tonometria").val();
	
	for (i = 1; i <= lista_tabla; i++) {
		$("#tabla_tono_" + i).css("display", "table-row");
	}
}

function restar_tabla_tono() {
	var lista_tabla = $("#cant_tonometria").val();
	
	if (lista_tabla > 1) {
		$("#tabla_tono_" + lista_tabla).css("display", "none");
		$("#tonometria_valor_od_" + lista_tabla).val("");
		$("#tonometria_dilatado_od_" + lista_tabla).val("");
		$("#tonometria_valor_oi_" + lista_tabla).val("");
		$("#tonometria_dilatado_oi_" + lista_tabla).val("");
		var total = parseInt(lista_tabla, 10) - 1;
		$("#cant_tonometria").val(total);
	}
}

function agregar_tabla_tono() {
	var lista_tabla = $("#cant_tonometria").val();
	var total = parseInt(lista_tabla, 10) + 1;
	if (total <= 10) {
		$("#tabla_tono_" + total).css("display", "table-row");
		$("#cant_tonometria").val(total);
	}
}

function ver_tonometrias_anteriores(ind_ver) {
	if (ind_ver) {
		$("#d_tonometrias_ant").show(200);
		$("#img_mostrar_tono_ant").hide();
		$("#img_ocultar_tono_ant").show();
	} else {
		$("#d_tonometrias_ant").hide(200);
		$("#img_ocultar_tono_ant").hide();
		$("#img_mostrar_tono_ant").show();
	}
}

function obtener_parametros_tonometria() {
	var cant_tono = $("#cant_tonometria").val();
	var params = "";
	var h = 0;
	for (var i = 1; i <= cant_tono; i++) {
	 	if ($("#tonometria_valor_od_" + i).val() != "" || $("#tonometria_dilatado_od_" + i).val() != "" ||
				$("#tonometria_valor_oi_" + i).val() != "" || $("#tonometria_dilatado_oi_" + i).val() != "") {
	 	 	params += "&tonometria_valor_od_" + i + "=" + $("#tonometria_valor_od_" + i).val() + 
	 	 			  "&tonometria_dilatado_od_" + i + "=" + $("#tonometria_dilatado_od_" + i).val() +
	 	 			  "&tonometria_valor_oi_" + i + "=" + $("#tonometria_valor_oi_" + i).val() +
	 	 			  "&tonometria_dilatado_oi_" + i + "=" + $("#tonometria_dilatado_oi_" + i).val() +
	 	 			  "&tonometria_fecha_" + i + "=" + $("#tonometria_fecha_" + i).val() + 
	 	 			  "&tonometria_hora_" + i + "=" + $("#tonometria_hora_" + i).val(); 
	 	 	h++;
	 	}
	}
	params += "&cant_tono=" + h;
	
	return params;
}
