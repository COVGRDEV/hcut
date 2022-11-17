// JavaScript Document

var g_arr_nombres_componentes = [];
var g_arr_sufijos_ojos = [];
var g_arr_componentes_rec = [];

window.onscroll = function(){
	ocultar_listas_rec_oft();
};

window.onkeyup = function(event){
	//Tecla escape
	if (event.keyCode == 27) {
		ocultar_listas_rec_oft();
	}
};

function mostrar_lista_rec_oft(objeto, evento, id_componente_rec, id_componente, id_hidden, id_div, id_img) {
	if ($("#" + id_componente_rec).is(":visible")) {
		ocultar_lista_rec_oft(id_componente_rec);
	} else {
		//Se ocultan todos los componentes visibles
		ocultar_listas_rec_oft();
		
		//Se ocultan los detalles de la lista seleccionada
		ocultar_detalles_lista_rec(id_hidden, id_div, id_img, "");
		
		var rect = objeto.getBoundingClientRect();
		var body_rect = document.body.getBoundingClientRect();
		
		$("#" + id_componente_rec).css("display", "block");
		
		//Se verifica si hay espacio horizontal
		var ind_right = false;
		if ((body_rect.right - rect.right) > ($("#" + id_componente_rec).outerWidth() + 5)) {
			$("#" + id_componente_rec).css("right", "auto");
			$("#" + id_componente_rec).css("left", rect.left + "px");
			ind_right = true;
		} else {
			$("#" + id_componente_rec).css("left", "auto");
			$("#" + id_componente_rec).css("right", (body_rect.right - rect.right) + "px");
		}
		
		//Se verifica si hay espacio vertical
		if ((body_rect.bottom - body_rect.top - rect.bottom) > ($("#" + id_componente_rec).outerHeight() + 50)) {
			$("#" + id_componente_rec).css("bottom", "auto");
			$("#" + id_componente_rec).css("top", rect.bottom + "px");
		} else if (rect.top > ($("#" + id_componente_rec).outerHeight() + 10)) {
			$("#" + id_componente_rec).css("top", "auto");
			$("#" + id_componente_rec).css("bottom", (body_rect.bottom - body_rect.top - rect.top) + "px");
		} else {
			//Se ubica centrado
			if (ind_right) {
				$("#" + id_componente_rec).css("left", rect.right + "px");
			} else {
				$("#" + id_componente_rec).css("right", (body_rect.right - rect.left) + "px");
			}
			
			$("#" + id_componente_rec).css("bottom", "auto");
			$("#" + id_componente_rec).css("top", ((body_rect.bottom - body_rect.top - $("#" + id_componente_rec).outerHeight()) / 2) + "px");
		}
	}
}

function ocultar_lista_rec_oft(id_componente_rec) {
	$("#" + id_componente_rec).css("display", "none");
}

function ocultar_listas_rec_oft() {
	for (var i = 0; i < g_arr_componentes_rec.length; i++) {
		$("#" + g_arr_componentes_rec[i]).css("display", "none");
	}
}

function mostrar_detalle_lista_rec(id_hidden, id_div, id_img, sufijo, sufijo2) {
	if (!$("#" + id_div + "_cont" + sufijo + sufijo2).is(":visible")) {
		//Se cierran todos los contenedores de detalle abiertos
		ocultar_detalles_lista_rec(id_hidden, id_div, id_img, sufijo);
		
		$("#" + id_div + "_cont" + sufijo + sufijo2).css("display", "inline-block");
	} else {
		$("#" + id_div + "_cont" + sufijo + sufijo2).css("display", "none");
	}
}

function ocultar_detalles_lista_rec(id_hidden, id_div, id_img, sufijo) {
	var cant_aux = parseInt($("#" + id_hidden + sufijo + "_cant").val(), 10);
	for (var i = 0; i < cant_aux; i++) {
		$("#" + id_div + "_cont" + sufijo + "_" + i).css("display", "none");
		
		if ($("#" + id_div + "_cont" + sufijo + "_" + i).length) {
			ocultar_detalles_lista_rec(id_hidden, id_div, id_img, sufijo + "_" + i);
		}
	}
}

function cambiar_check_rec_oft(nombre_componente, sufijo_ojo, sufijo, sufijo2) {
	var id_check = "chk_" + nombre_componente + sufijo_ojo + sufijo + sufijo2;
	$("#" + id_check).prop("checked", !$("#" + id_check).is(":checked"));
	
	marcar_check_rec_oft(nombre_componente, sufijo_ojo, sufijo, sufijo2);
}

function marcar_check_rec_oft(nombre_componente, sufijo_ojo, sufijo, sufijo2) {
	//Se cierran todos los contenedores de detalle abiertos
	ocultar_detalles_lista_rec("hdd_" + nombre_componente + sufijo_ojo, "d_" + nombre_componente + sufijo_ojo, "img_" + nombre_componente + sufijo_ojo, sufijo);
	
	var tipo_componente = parseInt($("#hdd_" + nombre_componente + sufijo_ojo + "_tipo").val(), 10);
	var id_componente = "";
	if (tipo_componente != 3) {
		id_componente = "txt_" + nombre_componente + sufijo_ojo;
	} else {
		id_componente = "hdd_" + nombre_componente + "_valor" + sufijo_ojo;
	}
	
	var texto_base = $("#" + id_componente).val();
	var texto = obtener_texto_check_oft_rec(nombre_componente, sufijo_ojo, sufijo, sufijo2);
	
	var id_check = "chk_" + nombre_componente + sufijo_ojo + sufijo + sufijo2;
	
	if ($("#" + id_check).is(":checked")) {
		//Se agrega el texto
		var texto_default = $("#hdd_" + nombre_componente + sufijo_ojo + "_default").val();
		if (texto == texto_default || texto_base == texto_default) {
			limpiar_componente_rec_oft(nombre_componente, sufijo_ojo, "", (texto == texto_default));
			$("#chk_" + nombre_componente + sufijo_ojo + sufijo + sufijo2).prop("checked", true);
			texto_base = "";
		}
		if (texto_base != "") {
			texto_base += ", ";
		}
		
		texto_base += texto;
		
		var id_texto_valor = "txt_" + nombre_componente + sufijo_ojo + "_valor" + sufijo + sufijo2;
		var id_hdd_unidad = "hdd_" + nombre_componente + sufijo_ojo + "_unidad" + sufijo + sufijo2;
		if ($("#" + id_texto_valor).length && $("#" + id_texto_valor).val() != "") {
			texto_base += ": " + $("#" + id_texto_valor).val();
			
			if ($("#" + id_hdd_unidad).val() != "") {
				texto_base += " " + $("#" + id_hdd_unidad).val();
			}
		}
	} else {
		//Se quita el texto
		if (texto_base == texto) {
			texto_base = "";
		} else {
			var pos_aux = texto_base.indexOf(texto + ", ");
			if (pos_aux != -1) {
				texto_base = texto_base.substring(0, pos_aux) + texto_base.substring((pos_aux + texto.length + 2), 10000);
			} else {
				pos_aux = texto_base.indexOf(", " + texto);
				if (pos_aux != -1) {
					var pos_aux2 = texto_base.indexOf(", ", pos_aux + 1);
					if (pos_aux2 != -1) {
						texto_base = texto_base.substring(0, pos_aux) + ", " + texto_base.substring(pos_aux2 + 2, 10000);
					} else {
						texto_base = texto_base.substring(0, pos_aux);
					}
				} else {
					pos_aux = texto_base.indexOf(texto + ": ");
					if (pos_aux != -1) {
						var pos_aux2 = texto_base.substring(pos_aux + texto.length, 10000).indexOf(", ");
						if (pos_aux2 != -1) {
							texto_base = (pos_aux > 0 ? texto_base.substring(0, pos_aux) : "") + texto_base.substring(pos_aux + texto.length + pos_aux2 + 2, 10000);
						} else {
							texto_base = "";
						}
					}
				}
			}
		}
	}
	
	$("#" + id_componente).val(texto_base);
	
	//Ajustar textarea
	$("#" + id_componente).trigger("input");
}

function limpiar_componente_rec_oft(nombre_componente, sufijo_ojo, sufijo, ind_default) {
	if (sufijo == "") {
		var tipo_componente = parseInt($("#hdd_" + nombre_componente + sufijo_ojo + "_tipo").val(), 10);
		var id_componente = "";
		if (tipo_componente != 3) {
			id_componente = "txt_" + nombre_componente + sufijo_ojo;
		} else {
			id_componente = "hdd_" + nombre_componente + "_valor" + sufijo_ojo;
		}
		$("#" + id_componente).val("");
	}
	if ($("#hdd_" + nombre_componente + sufijo_ojo + sufijo + "_cant").length) {
		var cantidad_aux = parseInt($("#hdd_" + nombre_componente + sufijo_ojo + sufijo + "_cant").val(), 10);
		for (var i = 0; i < cantidad_aux; i++) {
			$("#chk_" + nombre_componente + sufijo_ojo + sufijo + "_" + i).prop("checked", false);
			if (ind_default) {
				$("#txt_" + nombre_componente + sufijo_ojo + "_valor" + sufijo + "_" + i).val("");
			}
			limpiar_componente_rec_oft(nombre_componente, sufijo_ojo, sufijo + "_" + i, ind_default);
		}
	}
}

function copiar_campo_rec_oft(nombre_componente, sufijo_ojo, sufijo_ojo_alt) {
	ocultar_listas_rec_oft();
	
	if (sufijo_ojo != sufijo_ojo_alt) {
		copiar_checks_rec_oft(nombre_componente, sufijo_ojo, sufijo_ojo_alt, "");
		
		var tipo_componente = parseInt($("#hdd_" + nombre_componente + sufijo_ojo + "_tipo").val(), 10);
		var id_componente = "";
		var id_componente_alt = "";
		if (tipo_componente != 3) {
			id_componente = "txt_" + nombre_componente + sufijo_ojo;
			id_componente_alt = "txt_" + nombre_componente + sufijo_ojo_alt;
		} else {
			id_componente = "hdd_" + nombre_componente + "_valor" + sufijo_ojo;
			id_componente_alt = "hdd_" + nombre_componente + "_valor" + sufijo_ojo_alt;
		}
		
		$("#" + id_componente).val($("#" + id_componente_alt).val());
	}
}

function copiar_checks_rec_oft(nombre_componente, sufijo_ojo, sufijo_ojo_alt, sufijo) {
	if ($("#hdd_" + nombre_componente + sufijo_ojo + sufijo + "_cant").length) {
		var cantidad_aux = parseInt($("#hdd_" + nombre_componente + sufijo_ojo + sufijo + "_cant").val(), 10);
		for (var i = 0; i < cantidad_aux; i++) {
			$("#chk_" + nombre_componente + sufijo_ojo + sufijo + "_" + i).prop("checked", $("#chk_" + nombre_componente + sufijo_ojo_alt + sufijo + "_" + i).is(":checked"));
			$("#txt_" + nombre_componente + sufijo_ojo + "_valor" + sufijo + "_" + i).val($("#txt_" + nombre_componente + sufijo_ojo_alt + "_valor" + sufijo + "_" + i).val());
			
			copiar_checks_rec_oft(nombre_componente, sufijo_ojo, sufijo_ojo_alt, sufijo + "_" + i);
		}
	}
}

function marcar_inicio_checks_rec_oft() {
	for (var i = 0; i < g_arr_nombres_componentes.length; i++) {
		var nombre_componente = g_arr_nombres_componentes[i];
		var sufijo_ojo = g_arr_sufijos_ojos[i];
		
		var tipo_componente = parseInt($("#hdd_" + nombre_componente + sufijo_ojo + "_tipo").val(), 10);
		var id_componente = "";
		if (tipo_componente != 3) {
			id_componente = "txt_" + nombre_componente + sufijo_ojo;
		} else {
			id_componente = "hdd_" + nombre_componente + "_valor" + sufijo_ojo;
		}
		
		var texto_base = $("#" + id_componente).val();
		if (texto_base != "") {
			marcar_checks_rec_oft_contenido(nombre_componente, sufijo_ojo, "");
		}
		
		$("#" + id_componente).trigger("input");
	}
}

function marcar_checks_rec_oft_contenido(nombre_componente, sufijo_ojo, sufijo) {
	if ($("#hdd_" + nombre_componente + sufijo_ojo + sufijo + "_cant").length) {
		var tipo_componente = parseInt($("#hdd_" + nombre_componente + sufijo_ojo + "_tipo").val(), 10);
		var id_componente = "";
		if (tipo_componente != 3) {
			id_componente = "txt_" + nombre_componente + sufijo_ojo;
		} else {
			id_componente = "hdd_" + nombre_componente + "_valor" + sufijo_ojo;
		}
		
		var texto_base = $("#" + id_componente).val();
		var cantidad_aux = parseInt($("#hdd_" + nombre_componente + sufijo_ojo + sufijo + "_cant").val(), 10);
		for (var i = 0; i < cantidad_aux; i++) {
			if ($("#hdd_" + nombre_componente + sufijo_ojo + "_texto" + sufijo + "_" + i).length) {
				var texto = $("#hdd_" + nombre_componente + sufijo_ojo + "_texto" + sufijo + "_" + i).val();
				
				var ind_hallado = false;
				if (texto == texto_base) {
					ind_hallado = true;
				} else {
					var pos_aux = texto_base.indexOf(texto + ", ");
					if (pos_aux >= 0) {
						ind_hallado = true;
					} else {
						pos_aux = texto_base.indexOf(", " + texto);
						if (pos_aux >= 0) {
							ind_hallado = true;
						}
					}
				}
				
				if (ind_hallado) {
					$("#chk_" + nombre_componente + sufijo_ojo + sufijo + "_" + i).prop("checked", true);
				}
			} else {
				marcar_checks_rec_oft_contenido(nombre_componente, sufijo_ojo, sufijo + "_" + i);
			}
		}
	}
}

function cambiar_texto_rec_oft(nombre_componente, sufijo_ojo, sufijo, sufijo2) {
	var id_check = "chk_" + nombre_componente + sufijo_ojo + sufijo + sufijo2;
	var id_texto_valor = "txt_" + nombre_componente + sufijo_ojo + "_valor" + sufijo + sufijo2;
	var id_hdd_unidad = "hdd_" + nombre_componente + sufijo_ojo + "_unidad" + sufijo + sufijo2;
	
	if ($("#" + id_check).is(":checked")) {
		var texto_inic = obtener_texto_check_oft_rec(nombre_componente, sufijo_ojo, sufijo, sufijo2);
		var texto = texto_inic;
		if ($("#" + id_texto_valor).val() != "") {
			texto += ": " + $("#" + id_texto_valor).val();
			
			if ($("#" + id_hdd_unidad).val() != "") {
				texto += " " + $("#" + id_hdd_unidad).val();
			}
		}
		
		var tipo_componente = parseInt($("#hdd_" + nombre_componente + sufijo_ojo + "_tipo").val(), 10);
		var id_componente = "";
		if (tipo_componente != 3) {
			id_componente = "txt_" + nombre_componente + sufijo_ojo;
		} else {
			id_componente = "hdd_" + nombre_componente + "_valor" + sufijo_ojo;
		}
		
		var texto_base = $("#" + id_componente).val();
		
		var pos_aux = texto_base.indexOf(texto_inic + ", ");
		if (pos_aux != -1) {
			texto_base = texto_base.substring(0, pos_aux) + texto + ", " + texto_base.substring((pos_aux + texto_inic.length + 2), 10000);
		} else {
			pos_aux = texto_base.indexOf(", " + texto_inic);
			if (pos_aux != -1) {
				var pos_aux2 = texto_base.indexOf(", ", pos_aux + 1);
				if (pos_aux2 != -1) {
					texto_base = texto_base.substring(0, pos_aux) + ", " + texto + ", " + texto_base.substring(pos_aux2 + 2, 10000);
				} else {
					texto_base = texto_base.substring(0, pos_aux) + ", " + texto;
				}
			} else {
				pos_aux = texto_base.indexOf(texto_inic + ": ");
				if (pos_aux != -1) {
					var pos_aux2 = texto_base.substring(pos_aux + texto_inic.length, 10000).indexOf(", ");
					if (pos_aux2 != -1) {
						texto_base = (pos_aux > 0 ? texto_base.substring(0, pos_aux) : "") + texto + ", " + texto_base.substring(pos_aux + texto_inic.length + pos_aux2 + 2, 10000);
					} else {
						texto_base = texto;
					}
				} else {
					texto_base = texto;
				}
			}
		}
		
		$("#" + id_componente).val(texto_base);
	}
}

function obtener_texto_check_oft_rec(nombre_componente, sufijo_ojo, sufijo, sufijo2) {
	var id_hidden_base = "hdd_" + nombre_componente + sufijo_ojo;
	
	//Se arma la cadena de la selección
	var sufijo_compl = sufijo + sufijo2;
	var texto = $("#" + id_hidden_base + "_texto" + sufijo_compl).val();
	if ($("#" + id_hidden_base + "_excluye" + sufijo_compl).val() != "1") {
		var pos_aux = sufijo_compl.length;
		do {
			pos_aux = sufijo_compl.lastIndexOf("_", pos_aux - 1);
			if (pos_aux > 0) {
				texto = $("#" + id_hidden_base + "_texto" + sufijo_compl.substring(0, pos_aux)).val() + " " + texto;
				if ($("#" + id_hidden_base + "_excluye" + sufijo_compl.substring(0, pos_aux)).val() == "1") {
					break;
				}
			}
		} while (pos_aux > 0);
	}
	
	return texto;
}

function marcar_sano_rec_oft(nombre_componente, sufijo_ojo, sufijo) {
	var id_hidden_default = "hdd_" + nombre_componente + sufijo_ojo + "_default";
	var default_op = $("#" + id_hidden_default).val();
	
	var cantidad_aux = parseInt($("#hdd_" + nombre_componente + sufijo_ojo + sufijo + "_cant").val(), 10);
	for (var i = 0; i < cantidad_aux; i++) {
		if ($("#chk_" + nombre_componente + sufijo_ojo + sufijo + "_" + i).length) {
			var texto = obtener_texto_check_oft_rec(nombre_componente, sufijo_ojo, sufijo, "_" + i)
			
			var id_check = "chk_" + nombre_componente + sufijo_ojo + sufijo + "_" + i;
			var pos_aux = (", " + default_op + ", ").indexOf(", " + texto + ", ");
			
			$("#" + id_check).prop("checked", (pos_aux != -1));
		} else {
			marcar_sano_rec_oft(nombre_componente, sufijo_ojo, sufijo + "_" + i);
		}
	}
	
	if (sufijo == "") {
		var tipo_componente = parseInt($("#hdd_" + nombre_componente + sufijo_ojo + "_tipo").val(), 10);
		var id_componente = "";
		if (tipo_componente != 3) {
			id_componente = "txt_" + nombre_componente + sufijo_ojo;
		} else {
			id_componente = "hdd_" + nombre_componente + "_valor" + sufijo_ojo;
		}
		
		$("#" + id_componente).val(default_op);
	}
}
