function mostrar_lista_tabla() {
	var lista_tabla = $("#hdd_cant_examenes_op").val();
	
	for (i = 1; i <= lista_tabla; i++) {
		$("#tabla_diag_" + i).css("display", "block");
	}
}

function restar_tabla_examen() {
	var cant_aux = parseInt($('#hdd_cant_examenes_op').val(), 10);
	
	if (cant_aux > 1) {
		$("#tr_examen_" + (cant_aux - 1)).css("display", "none");
		$("#cmb_examen_" + (cant_aux - 1)).val("");
		$("#d_archivo_examen_" + (cant_aux - 1)).html("");
		$("#txt_observaciones_examen_" + (cant_aux - 1)).val("");
		$("#hdd_cant_examenes_op").val(cant_aux - 1);
	}
}

function agregar_tabla_examen() {
	var cant_aux = parseInt($('#hdd_cant_examenes_op').val(), 10);
	
	if (cant_aux < 20) {
		$("#tr_examen_" + (cant_aux)).css("display", "");
		$("#hdd_cant_examenes_op").val(cant_aux + 1);
	}
} 

function obtener_extension_archivo(nombre_archivo) {
	var extension = nombre_archivo.substring(nombre_archivo.lastIndexOf(".") + 1).toLowerCase();
	
	return extension;
}

function obtener_ruta_valida(indice) {
	var ruta_arch_examen = $("#fil_arch_examen_" + indice).val();
	var extension = obtener_extension_archivo(ruta_arch_examen);
	if (extension != "jpg" && extension != "png" && extension != "bmp" && extension != "gif" && extension != "pdf") {
		ruta_arch_examen = "";
	}
	
	return ruta_arch_examen;
}

function limpiar_bordes_examenes() {
	var cant_aux = parseInt($('#hdd_cant_examenes_op').val(), 10);
	
	for (var i = 0; i < cant_aux; i++) {
		$("#cmb_examen_" + i).css({"border": "1px solid rgba(0,0,0,.2)"});
		$("#cmb_ojo_examen_" + i).css({"border": "1px solid rgba(0,0,0,.2)"});
		$("#fil_arch_examen_" + i).css({"border": "1px solid rgba(0,0,0,.2)"});
	}
}

function validar_archivos_examenes() {
	var resultado = true;
	var cant_aux = parseInt($('#hdd_cant_examenes_op').val(), 10);
	
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

function validar_examenes() {
	limpiar_bordes_examenes();
	
	var resultado = true;
	var cant_aux = parseInt($('#hdd_cant_examenes_op').val(), 10);
	
	for (var i = 0; i < cant_aux; i++) {
		if ($("#cmb_examen_" + i).val() == "") {
			$("#cmb_examen_" + i).css({"border": "2px solid #FF002A"});
			resultado = false;
		}
		if ($("#cmb_ojo_examen_" + i).val() == "") {
			$("#cmb_ojo_examen_" + i).css({"border": "2px solid #FF002A"});
			resultado = false;
		}
		if ($("#fil_arch_examen_" + i).val() == "" && $("#hdd_ruta_arch_examen_" + i).val() == ""){
			$("#fil_arch_examen_" + i).css({"border": "2px solid #FF002A"});
			resultado = false;
		}
	}
	
	if (resultado) {
		resultado = validar_archivos_examenes();
	}
	
	return resultado;
}

function subir_archivos(ind_actualiza) {
	var cant_aux = parseInt($('#hdd_cant_examenes_op').val(), 10);
	
	for (var i = 0; i < cant_aux; i++) {
		var ruta_arch_examen = obtener_ruta_valida(i);
		if (ruta_arch_examen != "") {
			$("#frm_arch_examen_" + i).submit();
			
			if (ind_actualiza) {
				document.getElementById("d_archivo_examen_" + i).innerHTML = "";
				cargar_archivo(i, 3000);
			}
		}
	}
}

function cargar_archivo(indice, retardo) {
	var params = "opcion=2&id_hc=" + $("#hdd_id_hc_consulta_" + indice).val() +
				 "&id_examen=" + $("#cmb_examen_" + indice).val() +
				 "&id_ojo=" + $("#cmb_ojo_examen_" + indice).val();
	
	if (retardo > 0) {
		setTimeout(function() {llamarAjax("../funciones/Class_Examenes_Op_ajax.php", params, "d_archivo_examen_" + indice, "");}, retardo);
	} else {
		llamarAjax("../funciones/Class_Examenes_Op_ajax.php", params, "d_archivo_examen_" + indice, "");
	}
}
