function cargar_formulas_medicas(txt_id) {
	var params = "&txt_id=" + str_encode(txt_id) +
				 "&id_convenio=" + $("#hdd_id_convenio").val() +
				 "&id_plan=" + $("#hdd_id_plan").val();
	
	var cant_ciex = 0;
	if (isObject(document.getElementById("lista_tabla"))) {
		var cant_aux = parseInt($("#lista_tabla").val(), 10);
		for (var i = 1; i <= cant_aux; i++) {
			if ($("#hdd_ciex_diagnostico_" + i).val() != "") {
				params += "&cod_ciex_" + cant_ciex + "=" + $("#hdd_ciex_diagnostico_" + i).val();
				
				cant_ciex++;
			}
		}
	}
	params += "&cant_ciex=" + cant_ciex;
	
	if (cant_ciex > 0) {
		$("#d_interno").html("");
		llamarAjax("../funciones/Class_Formulas_Medicas.php", params, "d_interno", "");
		
		mostrar_formulario_flotante(1);
		reducir_formulario_flotante(800, "auto");
		posicionarDivFlotante("d_centro");
	} else {
		alert("Debe seleccionar un diagn\xf3stico");
	}
}

function cambiar_check_texto(indice_1, indice_2) {
	if (isObject(document.getElementById("chk_todos_" + indice_1))) {
		var check_aux = $("#chk_texto_" + indice_1 + "_" + indice_2);
		
		if (check_aux.is(":checked")) {
			var cant_textos = parseInt($("#hdd_cant_textos_" + indice_1).val(), 10);
			
			var bol_completos = true;
			for (var j = 0; j < cant_textos; j++) {
				if (!($("#chk_texto_" + indice_1 + "_" + j).is(":checked"))) {
					bol_completos = false;
					break;
				}
			}
			if (bol_completos) {
				$("#chk_todos_" + indice_1).prop("checked", true);
			}
		} else {
			$("#chk_todos_" + indice_1).prop("checked", false);
		}
	}
}

function cambiar_check_todos(indice) {
	var bol_check = $("#chk_todos_" + indice).is(":checked");
	var cant_textos = parseInt($("#hdd_cant_textos_" + indice).val(), 10);
	for (var j = 0; j < cant_textos; j++) {
		$("#chk_texto_" + indice + "_" + j).prop("checked", bol_check);
	}
}

function agregar_formulas_medicas(txt_id) {
	var texto_aux = "";
	var cant_formulas_prefed = parseInt($("#hdd_cant_formulas_prefed").val(), 10);
	for (var i = 0; i < cant_formulas_prefed; i++) {
		var cant_textos = parseInt($("#hdd_cant_textos_" + i).val(), 10);
		for (var j = 0; j < cant_textos; j++) {
			if ($("#chk_texto_" + i + "_" + j).is(":checked")) {
				if (texto_aux != "") {
					texto_aux += String.fromCharCode(13);
				}
				texto_aux += $("#hdd_texto_" + i + "_" + j).val();
			}
		}
	}
	
	var texto_ori = $("#" + txt_id).val();
	if (texto_ori != "") {
		texto_aux = texto_ori + String.fromCharCode(13) + texto_aux;
	}
	
	$("#" + txt_id).val(texto_aux);
	cerrar_div_centro();
}
