//<![CDATA[ 
$(function() {
    $(".paginated", "table").each(function(i) {
        $(this).text(i + 1);
    });
	
    $("table.paginated").each(function() {
        var currentPage = 0;
        var numPerPage = 10;
        var $table = $(this);
        $table.bind("repaginate", function() {
            $table.find("tbody tr").hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
        });
        $table.trigger("repaginate");
        var numRows = $table.find("tbody tr").length;
        var numPages = Math.ceil(numRows / numPerPage);
        var $pager = $('<div class="pager"></div>');
        for (var page = 0; page < numPages; page++) {
            $('<span class="page-number"></span>').text(page + 1).bind("click", {
                newPage: page
            }, function(event) {
                currentPage = event.data["newPage"];
                $table.trigger("repaginate");
                $(this).addClass("active").siblings().removeClass("active");
            }).appendTo($pager).addClass("clickable");
        }
        $pager.insertBefore($table).find("span.page-number:first").addClass("active");
    });
});
//]]>

//Funcion clic de la tabla disponibilidad especialistas -> primera página
function ver_disponibilidad(id, profesional, mes, ano) {
	$("#contenedor_error").css("display", "none");
    var idusuario = id;
	if (mes === undefined) {
		mes = "";
	}
	if (ano === undefined) {
		ano = "";
	}
    var params = "opcion=1&idusuario=" + idusuario +
				 "&profesional=" + profesional +
				 "&mes=" + mes +
				 "&ano=" + ano;
	
    llamarAjax("disponibilidad_ajax.php", params, "d_disponibilidad", "");
}

function calendario(mes, anio) {
    var hdd_id_usuario = $("#hdd_id_usuario").val();
    var params = "opcion=2&mes=" + mes +
				 "&anio=" + anio +
				 "&hdd_id_usuario=" + hdd_id_usuario;
	
    llamarAjax("disponibilidad_ajax.php", params, "agenda", "");
}

//Funcion de evento onChange del combo box en el calendario Disponibilidad de especialistas
function guardar_disponibilidad(anio, mes, dia) {
    var opCombo = $("#cmbDisponibilidad" + dia).val();
	var lugar_disp = $("#cmb_lugar_disp_" + dia).val();
    var hdd_id_usuario = $("#hdd_id_usuario").val();
    var params = "opcion=3&opCombo=" + opCombo +
				 "&lugar_disp=" + lugar_disp +
				 "&anio=" + anio +
				 "&mes=" + mes +
				 "&dia=" + dia +
				 "&hdd_id_usuario=" + hdd_id_usuario;
	
    llamarAjax("disponibilidad_ajax.php", params, "hdd_pa_rta", "guardar_disponibilidad_accion(" + anio + "," + mes + "," + dia + "," + opCombo + ");");
}

//Funcion que lee el valor impreso en el hidden hdd_pa_rta y con base en eso hace la funcion
function guardar_disponibilidad_accion(anio, mes, dia, opCombo) {
    //Almacena el valor del hidden
    var rta2 = parseInt($("#rta_crearDisponibilidadProf").val(), 10);
	
	if (rta2 > 0) {
		$("#hdd_tipo_disponibilidad_" + dia).val($("#cmbDisponibilidad" + dia).val());
		
		//Se ajusta el color del día
		switch (opCombo) {
			case 13: //No disponible
				$("#d_dia_" + dia).attr("class", "no_disponible");
                                       $("#btn_asignar_parcial_" + dia).remove();
                                       $("#cmbDisponibilidad" + dia).css("width", "117px");
				break;
			case 11: //Completa
				$("#d_dia_" + dia).attr("class", "completa");
                                       $("#btn_asignar_parcial_" + dia).remove();
                                       $("#cmbDisponibilidad" + dia).css("width", "117px");
				break;
			case 12: //Parcial
				$("#d_dia_" + dia).attr("class", "parcial");
				configurar_disponibilidad(anio, mes, dia, $("#cmb_lugar_disp_" + dia).val());
				break;
		}
	} else if (rta2 == -3) {
		alert("Hay citas asignadas a la fecha seleccionada, por favor revise la secci\xf3n de citas");
		$("#cmbDisponibilidad" + dia).val($("#hdd_tipo_disponibilidad_" + dia).val());
	} else {
		alert("Error interno al realizar el cambio (" + rta2 + ")");
		$("#cmbDisponibilidad" + dia).val($("#hdd_tipo_disponibilidad_" + dia).val());
	}
}

//Funcion clic de la tabla disponibilidad especialistas -> primera página
function configurar_disponibilidad(anio, mes, dia, id_lugar_disp) {
    var id_usuario_prof = $("#hdd_id_usuario").val();
    var profesional = $("#profesional").html();
    
    var name_mes = $(".encabezado table tr td:eq(1) h2").html();
    var id_disponibilidad = $("#hdd_id_disponibilidad_" + dia).val();
	
    var params = "opcion=4&id_usuario_prof=" + id_usuario_prof +
				 "&id_disponibilidad=" + id_disponibilidad +
				 "&anio=" + anio +
				 "&mes=" + mes +
				 "&dia=" + dia +
				 "&profesional=" + profesional +
				 "&name_mes=" + name_mes +
				 "&id_lugar_disp=" + id_lugar_disp;
	
    llamarAjax("disponibilidad_ajax.php", params, "d_disponibilidad", "");
}

function validar_guardar_disp_det() {
	var resultado = 1;
	var mensaje_error = "Los campos marcados en rojo son obligatorios.";
	$("#contenedor_error").css("display", "none");
	
	var cant_disp_prof_det = parseInt($("#hdd_cant_disp_prof_det").val(), 10);
	for (var i = 0; i < cant_disp_prof_det; i++) {
		$("#cmb_hinicio_" + i).removeClass("borde_error");
		$("#cmb_hfinal_" + i).removeClass("borde_error");
		$("#cmb_lugar_disp_parc_" + i).removeClass("borde_error");
		$("#img_filtros_" + i).removeClass("borde_error");
	}
	
	var bol_seleccion = false;
	for (var i = 0; i < cant_disp_prof_det; i++) {
		if ($("#cmb_hinicio_" + i).val() != "-1" && $("#cmb_hfinal_" + i).val() != "-1" && $("#cmb_lugar_disp_parc_" + i).val() != "-1") {
			bol_seleccion = true;
			break;
		}
	}
	
	if (!bol_seleccion) {
		if ($("#cmb_hinicio_0").val() == "-1") {
			$("#cmb_hinicio_0").addClass("borde_error");
		}
		if ($("#cmb_hfinal_0").val() == "-1") {
			$("#cmb_hfinal_0").addClass("borde_error");
		}
		if ($("#cmb_lugar_disp_parc_0").val() == "-1") {
			$("#cmb_lugar_disp_parc_0").addClass("borde_error");
		}
		resultado = 0;
	}
	
	for (var i = 0; i < cant_disp_prof_det; i++) {
		if ($("#cmb_hinicio_" + i).val() != "-1") {
			if ($("#cmb_hfinal_" + i).val() == "-1") {
				$("#cmb_hfinal_" + i).addClass("borde_error");
				resultado = 0;
			}
			if ($("#cmb_lugar_disp_parc_" + i).val() == "-1") {
				$("#cmb_lugar_disp_parc_" + i).addClass("borde_error");
				resultado = 0;
			}
		}
	}
	
	if (resultado == 1) {
		mensaje_error = "Las horas finales deben ser mayores a las horas iniciales.";
		for (var i = 0; i < cant_disp_prof_det; i++) {
			if ($("#cmb_hinicio_" + i).val() != "-1") {
				var cmb_hinicio = $("#cmb_hinicio_" + i).val();
				var cmb_hfinal = $("#cmb_hfinal_" + i).val();
				
				if (cmb_hinicio >= cmb_hfinal) {
					$("#cmb_hinicio_" + i).addClass("borde_error");
					$("#cmb_hfinal_" + i).addClass("borde_error");
					resultado = 0;
				}
			}
		}
	}
	
	if (resultado == 1) {
		mensaje_error = "Existen conflictos entre las horas seleccionadas.";
		for (var i = 0; i < (cant_disp_prof_det - 1); i++) {
			if ($("#cmb_hinicio_" + i).val() != "-1") {
				var hora_ini = $("#cmb_hinicio_" + i).val();
				var hora_final = $("#cmb_hfinal_" + i).val();
				
				for (var j = i + 1; j < cant_disp_prof_det; j++) {
					if ($("#cmb_hinicio_" + j).val() != "-1") {
						var hora_ini_2 = $("#cmb_hinicio_" + j).val();
						var hora_final_2 = $("#cmb_hfinal_" + j).val();
						
						if (hora_ini >= hora_ini_2 && hora_ini < hora_final_2) {
							$("#cmb_hinicio_" + i).addClass("borde_error");
							$("#cmb_hinicio_" + j).addClass("borde_error");
							$("#cmb_hfinal_" + j).addClass("borde_error");
							resultado = 0;
						}
						if (hora_final > hora_ini_2 && hora_final <= hora_final_2) {
							$("#cmb_hinicio_" + j).addClass("borde_error");
							$("#cmb_hfinal_" + i).addClass("borde_error");
							$("#cmb_hfinal_" + j).addClass("borde_error");
							resultado = 0;
						}
					}
				}
			}
		}
	}
	
	if (resultado == 0) {
   	    $("#contenedor_error").html(mensaje_error);
	}
	
	return resultado;
}

//funcion que Guarda los valores en el 3 formulario llamado: disponibilidad especialistas
function guardar_disponibilidad_prof_det() {
	if (validar_guardar_disp_det() == 1) {
		var cant_disp_prof_det = parseInt($("#hdd_cant_disp_prof_det").val(), 10);
		
		var params = "opcion=5&id_usuario_prof=" + $("#hdd_id_usuario").val() +
					 "&fecha_cal=" + $("#hdd_fecha_cal").val();
		
		var cont_aux = 0;
		for (var i = 0; i < cant_disp_prof_det; i++) {
			if ($("#cmb_hinicio_" + i).val() != "-1") {
				params += "&hora_ini_" + cont_aux + "=" + $("#cmb_hinicio_" + i).val() +
						  "&hora_fin_" + cont_aux + "=" + $("#cmb_hfinal_" + i).val() +
						  "&id_lugar_disp_" + cont_aux + "=" + $("#cmb_lugar_disp_parc_" + i).val() +
						  "&cadena_convenios_" + cont_aux + "=" + $("#hdd_cadena_convenios_" + i).val() +
						  "&cadena_perfiles_" + cont_aux + "=" + $("#hdd_cadena_perfiles_" + i).val() +
						  "&cadena_tipos_citas_" + cont_aux + "=" + $("#hdd_cadena_tipos_citas_" + i).val();
				
				cont_aux++;
			}
		}
		params += "&cant_disp_prof_det=" + cont_aux;
		
		llamarAjax("disponibilidad_ajax.php", params, "d_cmb", "guardar_disponibilidad_prof_det_rta();");
	} else {
        $("#contenedor_error").css("display", "block");
		window.scrollTo(0, 0);
	}
}

//Funcion que hace el proceso despues de la funcion guardar_disponibilidad_prof_det()
function guardar_disponibilidad_prof_det_rta() {
	var resultado = parseInt($("#hdd_pa_rta").val(), 10);
	var id = $("#hdd_id_usuario").val();
	var mes_aux = $("#hdd_mes_sel").val();
	var ano_aux = $("#hdd_ano_sel").val();
	
	if (resultado > 0) {
		display_contenedor_exito(1, "El registro se ha guardado de forma exitosa");
		display_contenedor_exito(0, "");
		ver_disponibilidad(id, "", mes_aux, ano_aux);
	} else if (rta == -3) {
		alert("Hay citas asignadas a la fecha seleccionada, por favor revise la secci\xf3n de citas");
		ver_disponibilidad(id, "", mes_aux, ano_aux);
	} else {
		alert("Error interno al tratar de guardar la disponibilidad");
		ver_disponibilidad(id, "", mes_aux, ano_aux);
	}
}

//Funcion de mostrar el div contenedor_exito
function display_contenedor_exito(parametro, mensaje) {
    if (parametro == 0) {
        setTimeout('$("#contenedor_exito").slideUp(200).css("display", "none")', 4000);
    } else if (parametro == 1) {
        $("#contenedor_exito").slideDown(200).css("display", "block");
        $("#contenedor_exito").html(mensaje);
    }
}

//Funcion de mostrar el div contenedor_error
function display_contenedor_error(parametro, mensaje) {
    if (parametro == 0) {
        setTimeout('$("#contenedor_error").slideUp(200).css("display", "none")', 4000);
    } else if (parametro == 1) {
        $("#contenedor_error").slideDown(200).css("display", "block");
        $("#contenedor_error").html(mensaje);
    }
}

//Esta funcion se pocesa en el boton regresar.
function regresar_disponibilidad() {
    var params = "opcion=6";
	
    llamarAjax("disponibilidad_ajax.php", params, "d_disponibilidad", "");
}

function chocorramo() {
    $("#cmb_hinicio").val("09:30");
}

function validar_aplicar_disponibilidad_mes() {
	mostrar_formulario_flotante(1);
	reducir_formulario_flotante(400, 250);
	posicionarDivFlotante("d_centro");
	confirmar_aplicar_disponibilidad_mes();
}

function confirmar_aplicar_disponibilidad_mes(){
	$("#d_interno").html(
		'<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
			'<tr>' +
				'<th align="center">' +
					'<h4>&iquest;Est&aacute; seguro de guardar esta informaci&oacute;n?</h4>' +
				'</th>' +
			'</tr>' +
			'<tr>' +
				'<th align="center">' +
					'<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" class="btnPrincipal" value="Aceptar" onclick="aplicar_disponibilidad_mes();"/>\n' +
					'<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" class="btnPrincipal" value="Cancelar" onclick="cerrar_div_centro();"/> ' +
				'</th>' +
			'</tr>' +
		'</table>');
}

function aplicar_disponibilidad_mes() {
	var params = "opcion=7&id_usuario_prof=" + $("#hdd_id_usuario").val() +
				 "&id_tipo_disponibilidad=" + $("#cmb_disponibilidad_mes").val() +
				 "&id_lugar_disp=" + $("#cmb_lugar_disp_mes").val() +
				 "&mes=" + $("#hdd_mes_sel").val() +
				 "&ano=" + $("#hdd_ano_sel").val();
	
	llamarAjax("disponibilidad_ajax.php", params, "d_guardar_disponibilidad", "terminar_aplicar_disponibilidad_mes()");
}

function terminar_aplicar_disponibilidad_mes() {
    var rta = $("#hdd_resultado_mes").val();
    var id = $("#hdd_id_usuario").val();
    var mes_aux = $("#hdd_mes_sel").val();
	var ano_aux = $("#hdd_ano_sel").val();
	cerrar_div_centro();
	
	if (rta > 0) {
		display_contenedor_exito(1, "Disponibilidades del mes actualizadas con &eacute;xito");
		display_contenedor_exito(0, "");
		ver_disponibilidad(id, "", mes_aux, ano_aux);
	} else {
		display_contenedor_error(1, "Error interno al registrar las disponibilidades");
		display_contenedor_error(0, "");
	}
}

function validar_duplicar_disp_ult_semana() {
	mostrar_formulario_flotante(1);
	reducir_formulario_flotante(400, 250);
	posicionarDivFlotante("d_centro");
	confirmar_duplicar_disp_ult_semana();
}

function confirmar_duplicar_disp_ult_semana(){
	$("#d_interno").html(
		'<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
			'<tr>' +
				'<th align="center">' +
					'<h4>&iquest;Est&aacute; seguro de guardar esta informaci&oacute;n?</h4>' +
				'</th>' +
			'</tr>' +
			'<tr>' +
				'<th align="center">' +
					'<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" class="btnPrincipal" value="Aceptar" onclick="duplicar_disp_ult_semana();"/>\n' +
					'<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" class="btnPrincipal" value="Cancelar" onclick="cerrar_div_centro();"/> ' +
				'</th>' +
			'</tr>' +
		'</table>');
}

function duplicar_disp_ult_semana() {
	var params = "opcion=8&id_usuario_prof=" + $("#hdd_id_usuario").val() +
				 "&mes=" + $("#hdd_mes_sel").val() +
				 "&ano=" + $("#hdd_ano_sel").val();
	
	llamarAjax("disponibilidad_ajax.php", params, "d_guardar_disponibilidad", "terminar_duplicar_disp_ult_semana()");
}

function terminar_duplicar_disp_ult_semana() {
    var rta = $("#hdd_resultado_semana").val();
    var id = $("#hdd_id_usuario").val();
    var mes_aux = $("#hdd_mes_sel").val();
	var ano_aux = $("#hdd_ano_sel").val();
	cerrar_div_centro();
	
	if (rta > 0) {
		display_contenedor_exito(1, "Disponibilidades de la &uacute;ltima semana duplicada con &eacute;xito");
		display_contenedor_exito(0, "");
		ver_disponibilidad(id, "", mes_aux, ano_aux);
	} else if (rta == 0) {
		display_contenedor_error(1, "No hay d&iacute;as por configurar en el mes o no hay semanas para duplicar");
		display_contenedor_error(0, "");
	} else {
		display_contenedor_error(1, "Error interno al registrar las disponibilidades");
		display_contenedor_error(0, "");
	}
}

function agregar_disponibilidad_parcial() {
	var cant_disp_prof_det = parseInt($("#hdd_cant_disp_prof_det").val(), 10);
	
	if (cant_disp_prof_det < 20) {
		$("#tr_disp_" + cant_disp_prof_det).css("display", "table-row");
		cant_disp_prof_det++;
		$("#hdd_cant_disp_prof_det").val(cant_disp_prof_det)
	} else {
		alert("No se pueden agregar m\xe1s disponibilidades parciales.");
	}
}

function restar_disponibilidad_parcial() {
	var cant_disp_prof_det = parseInt($("#hdd_cant_disp_prof_det").val(), 10);
	cant_disp_prof_det--;
	
	if (cant_disp_prof_det >= 0) {
		$("#hdd_cadena_convenios_" + cant_disp_prof_det).val("");
		$("#hdd_cadena_perfiles_" + cant_disp_prof_det).val("");
		$("#hdd_cadena_tipos_citas_" + cant_disp_prof_det).val("");
		$("#cmb_hinicio_" + cant_disp_prof_det).val("");
		$("#cmb_hfinal_" + cant_disp_prof_det).val("");
		$("#cmb_lugar_disp_parc_" + cant_disp_prof_det).val("");
		
		if (cant_disp_prof_det > 0) {
			$("#tr_disp_" + cant_disp_prof_det).css("display", "none");
			$("#hdd_cant_disp_prof_det").val(cant_disp_prof_det)
		}
	}
}

function ver_restricciones(indice, id_disponibilidad_det) {
	$("#d_centro").css("width", "900px");
	$("#d_interno").css("width", "100%");
	mostrar_formulario_flotante(1);
	
	var params = "opcion=9&indice=" + indice +
				 "&id_usuario_prof=" + $("#hdd_id_usuario").val() +
				 "&id_disponibilidad_det=" + id_disponibilidad_det +
				 "&cadena_convenios=" + $("#hdd_cadena_convenios_" + indice).val() +
				 "&cadena_perfiles=" + $("#hdd_cadena_perfiles_" + indice).val() +
				 "&cadena_tipos_citas=" + $("#hdd_cadena_tipos_citas_" + indice).val();
	
	llamarAjax("disponibilidad_ajax.php", params, "d_interno", "");
}

function seleccionar_fitro_disponibilidad(prefijo, indice) {
	var cant_filtros = parseInt($("#hdd_cant_" + prefijo + "_filtros").val(), 10);
	if (indice == "todos") {
		var ind_checked = $("#chk_" + prefijo + "_todos").is(":checked");
		for (var i = 0; i < cant_filtros; i++) {
			$("#chk_" + prefijo + "_" + i).prop("checked", ind_checked);
		}
	} else {
		var ind_checked = true;
		for (var i = 0; i < cant_filtros; i++) {
			if (!$("#chk_" + prefijo + "_" + i).is(":checked")) {
				ind_checked = false;
				break;
			}
		}
		$("#chk_" + prefijo + "_todos").prop("checked", ind_checked);
	}
}

function aplicar_restricciones_disp(indice) {
	if ($("#chk_convenio_todos").is(":checked")) {
		$("#hdd_cadena_convenios_" + indice).val("");
	} else {
		var cant_convenio_filtros = parseInt($("#hdd_cant_convenio_filtros").val(), 10);
		var cadena_convenios = "";
		for (var i = 0; i < cant_convenio_filtros; i++) {
			if ($("#chk_convenio_" + i).is(":checked")) {
				if (cadena_convenios != "") {
					cadena_convenios += ",";
				}
				cadena_convenios += $("#hdd_convenio_" + i).val();
			}
		}
		$("#hdd_cadena_convenios_" + indice).val(cadena_convenios);
	}
	
	if ($("#chk_perfil_todos").is(":checked")) {
		$("#hdd_cadena_perfiles_" + indice).val("");
	} else {
		var cant_perfil_filtros = parseInt($("#hdd_cant_perfil_filtros").val(), 10);
		var cadena_perfiles = "";
		for (var i = 0; i < cant_perfil_filtros; i++) {
			if ($("#chk_perfil_" + i).is(":checked")) {
				if (cadena_perfiles != "") {
					cadena_perfiles += ",";
				}
				cadena_perfiles += $("#hdd_perfil_" + i).val();
			}
		}
		$("#hdd_cadena_perfiles_" + indice).val(cadena_perfiles);
	}
	
	if ($("#chk_tipo_cita_todos").is(":checked")) {
		$("#hdd_cadena_tipos_citas_" + indice).val("");
	} else {
		var cant_tipo_cita_filtros = parseInt($("#hdd_cant_tipo_cita_filtros").val(), 10);
		var cadena_tipos_citas = "";
		for (var i = 0; i < cant_tipo_cita_filtros; i++) {
			if ($("#chk_tipo_cita_" + i).is(":checked")) {
				if (cadena_tipos_citas != "") {
					cadena_tipos_citas += ",";
				}
				cadena_tipos_citas += $("#hdd_tipo_cita_" + i).val();
			}
		}
		$("#hdd_cadena_tipos_citas_" + indice).val(cadena_tipos_citas);
	}
	
	cerrar_div_centro();
}
