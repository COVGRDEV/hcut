//<![CDATA[ 
$(function() {
    $('.paginated', 'table').each(function(i) {
        $(this).text(i + 1);
    });
	
    $('table.paginated').each(function() {
        var currentPage = 0;
        var numPerPage = 10;
        var $table = $(this);
        $table.bind('repaginate', function() {
            $table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
        });
        $table.trigger('repaginate');
        var numRows = $table.find('tbody tr').length;
        var numPages = Math.ceil(numRows / numPerPage);
        var $pager = $('<div class="pager"></div>');
        for (var page = 0; page < numPages; page++) {
            $('<span class="page-number"></span>').text(page + 1).bind('click', {
                newPage: page
            }, function(event) {
                currentPage = event.data['newPage'];
                $table.trigger('repaginate');
                $(this).addClass('active').siblings().removeClass('active');
            }).appendTo($pager).addClass('clickable');
        }
        $pager.insertBefore($table).find('span.page-number:first').addClass('active');
    });
});
//]]>

//Funcion clic de la tabla disponibilidad especialistas -> primera página
function disponibilidad(id, profesional, mes, ano) {
    var idusuario = id;
	if (mes === undefined) {
		mes = "";
	}
	if (ano === undefined) {
		ano = "";
	}
    var params = 'opcion=1&idusuario=' + idusuario +
				 '&profesional=' + profesional +
				 '&mes=' + mes + '&ano=' + ano;
	
    llamarAjax("disponibilidad_ajax.php", params, "d_disponibilidad", "");
}

function calendario(mes, anio) {
    var hdd_id_usuario = $('#hdd_id_usuario').val();
    var params = 'opcion=2&mes=' + mes +
            '&anio=' + anio + '&hdd_id_usuario=' + hdd_id_usuario;
    llamarAjax("disponibilidad_ajax.php", params, "agenda", "");
}

//Funcion de evento onChange del combo box en el calendario Disponibilidad de especialistas
function guardar_disponibilidad(anio, mes, dia) {
    var opCombo = $('#cmbDisponibilidad' + dia + '').val();
	var lugar_disp = $('#cmb_lugar_disp_' + dia + '').val();
    var hdd_id_usuario = $('#hdd_id_usuario').val();
    var params = 'opcion=3&opCombo=' + opCombo + '&lugar_disp=' + lugar_disp + '&anio=' + anio + '&mes=' + mes + '&dia=' + dia + '&hdd_id_usuario=' + hdd_id_usuario ;
    llamarAjax("disponibilidad_ajax.php", params, "hdd_pa_rta", "guardar_disponibilidad_accion(" + anio + "," + mes + "," + dia + "," + opCombo + ");");
}

//Funcion que lee el valor impreso en el hidden hdd_pa_rta y con base en eso hace la funcion
function guardar_disponibilidad_accion(anio, mes, dia, opCombo) {
    //Almacena el valor del hidden
    var rta2 = parseInt($('#rta_crearDisponibilidadProf').val(), 10);
	
	switch (rta2) {
		case 1: //si Hay registros
			$('#hdd_tipo_disponibilidad_' + dia).val($('#cmbDisponibilidad' + dia).val());
			
			//Se ajusta el color del día
			switch (opCombo) {
				case 13: //No disponible
					$('#d_dia_' + dia + '').attr("class", "no_disponible");
                                        $('#btn_asignar_parcial_' + dia).remove();
                                        $('#cmbDisponibilidad' + dia).css('width','117px');
					break;
				case 11: //Completa
					$('#d_dia_' + dia + '').attr("class", "completa");
                                        $('#btn_asignar_parcial_' + dia).remove();
                                        $('#cmbDisponibilidad' + dia).css('width','117px');
					break;
				case 12: //Parcial
					$('#d_dia_' + dia + '').attr("class", "parcial");
					configurar_disponibilidad(anio, mes, dia, $("#cmb_lugar_disp_" + dia).val());
					break;
			}
			break;
		case -3:
			alert('Hay citas asignadas a la fecha seleccionada, por favor revise la secci\xf3n de citas');
			$('#cmbDisponibilidad' + dia).val($('#hdd_tipo_disponibilidad_' + dia).val());
			break;
		default: //Errores
			alert('Error interno al realizar el cambio (' + rta2 + ')');
			$('#cmbDisponibilidad' + dia).val($('#hdd_tipo_disponibilidad_' + dia).val());
			break;
                        
	}
}

//Funcion clic de la tabla disponibilidad especialistas -> primera página
function configurar_disponibilidad(anio, mes, dia, id_lugar_disp) {
    var idusuario = $("#hdd_id_usuario").val();
    var profesional = $("#profesional").html();
    
    var name_mes = $(".encabezado table tr td:eq(1) h2").html();
    var id_disponibilidad = $("#hdd_id_disponibilidad_" + dia).val();
	
    var params = 'opcion=4&idusuario=' + idusuario +
				 '&id_disponibilidad=' + id_disponibilidad +
				 '&anio=' + anio +
				 '&mes=' + mes +
				 '&dia=' + dia +
				 '&profesional=' + profesional +
				 '&name_mes=' + name_mes +
				 '&id_lugar_disp=' + id_lugar_disp;
    llamarAjax("disponibilidad_ajax.php", params, "d_disponibilidad", "");
}

function validar_guardar_disp_det() {
	if ($("#cmb_hinicio option:selected").val() == "-1" && $("#cmb_hfinal option:selected").val() == "-1"
			&& $("#cmb_hinicio2 option:selected").val() == "-1" && $("#cmb_hfinal2 option:selected").val() == "-1") {
		alert("Debe seleccionar un rango de horas");
		return;
	}
	
	if ($("#cmb_hinicio option:selected").val() == "-1" && $("#cmb_hfinal option:selected").val() != "-1") {
		alert("La hora de inicio no debe ser vac\xeda");
		return;
	}
	
	if ($("#cmb_hinicio option:selected").val() != "-1" && $("#cmb_hfinal option:selected").val() == "-1") {
		alert("La hora final no debe ser vac\xeda");
		return;
	}
	
	if ($("#cmb_hinicio option:selected").val() != "-1" && $("#cmb_lugar_disp_parc option:selected").val() == "-1") {
		alert("El lugar no debe ser vac\xedo");
		return;
	}
	
	if ($("#cmb_hinicio2 option:selected").val() == "-1" && $("#cmb_hfinal2 option:selected").val() != "-1") {
		alert("La segunda hora de inicio no debe ser vac\xeda");
		return;
	}
	
	if ($("#cmb_hinicio2 option:selected").val() != "-1" && $("#cmb_hfinal2 option:selected").val() == "-1") {
		alert("La segunda hora final no debe ser vac\xeda");
		return;
	}
	
	if ($("#cmb_hinicio2 option:selected").val() != "-1" && $("#cmb_lugar_disp_parc2 option:selected").val() == "-1") {
		alert("El segundo lugar no debe ser vac\xedo");
		return;
	}
	
	if ($("#cmb_hinicio option:selected").val() != "-1" && $("#cmb_hfinal option:selected").val() != "-1") {
		var cmb_hinicio = $('#cmb_hinicio option:selected').val();
		var cmb_hfinal = $('#cmb_hfinal option:selected').val();
		
		//Si Hora de inicio con los minutos son igual a Hora Final con los minutos
		if (cmb_hinicio == cmb_hfinal) {
			alert('La hora inicial no puede ser igual a la hora final');
			return;
		}
		
		//Hora de inicio es mayor a Hora Final 
		if (cmb_hinicio > cmb_hfinal) {
			alert('La hora inicial no puede ser mayor a la hora final');
			return;
		}
	}
	
	if ($("#cmb_hinicio2 option:selected").val() != "-1" && $("#cmb_hfinal2 option:selected").val() != "-1") {
		var cmb_hinicio = $('#cmb_hinicio2 option:selected').html();
		var cmb_hfinal = $('#cmb_hfinal2 option:selected').html();
		
		//Si Hora de inicio con los minutos son igual a Hora Final con los minutos
		if (cmb_hinicio == cmb_hfinal) {
			alert('La hora inicial no puede ser igual a la hora final');
			return;
		}
		
		//Hora de inicio es mayor a Hora Final 
		if (cmb_hinicio > cmb_hfinal) {
			alert('La hora inicial no puede ser mayor a la hora final');
			return;
		}
	}
	
	guardar_disponibilidad_prof_det();
}

//funcion que Guarda los valores en el 3 formulario llamado: disponibilidad especialistas
function guardar_disponibilidad_prof_det() {
	var fechaInicio = $('#cmb_hinicio option:selected').val();
	var fechaFinal = $('#cmb_hfinal option:selected').val();
	var lugarDisp = $('#cmb_lugar_disp_parc option:selected').val();
	var fechaInicio2 = $('#cmb_hinicio2 option:selected').val();
	var fechaFinal2 = $('#cmb_hfinal2 option:selected').val();
	var fecha_cal = $('#hdd_fecha_cal').val();
	var hdd_id_usuario = $('#hdd_id_usuario').val();
	var lugarDisp2 = $('#cmb_lugar_disp_parc2 option:selected').val();
	
	var params = 'opcion=5&fechaInicio=' + fechaInicio +
				 '&fechaFinal= ' + fechaFinal +
				 '&lugarDisp=' + lugarDisp +
				 '&fechaInicio2= ' + fechaInicio2 +
				 '&fechaFinal2= ' + fechaFinal2 +
				 '&lugarDisp2=' + lugarDisp2 +
				 '&fecha_cal=' + fecha_cal +
				 '&hdd_id_usuario=' + hdd_id_usuario;
	
	llamarAjax("disponibilidad_ajax.php", params, "d_cmb", "guardar_disponibilidad_prof_det_rta();");
}

//Funcion que hace el proceso despues de la funcion guardar_disponibilidad_prof_det()
function guardar_disponibilidad_prof_det_rta() {
	var rta = $('#hdd_pa_rta').val();
	var id = $('#hdd_id_usuario').val();
	var mes_aux = $('#hdd_mes_sel').val();
	var ano_aux = $('#hdd_ano_sel').val();
	
	if (rta == '-3') {
		alert('Hay citas asignadas a la fecha seleccionada, por favor revise la secci\xf3n de citas');
		disponibilidad(id, "", mes_aux, ano_aux);
	} else if (rta == '1') {
		display_contenedor_exito(1, 'El registro se ha guardado de forma exitosa');
		display_contenedor_exito(0, "");
		disponibilidad(id, "", mes_aux, ano_aux);
	}
}

//Funcion de mostrar el div contenedor_exito
function display_contenedor_exito(parametro, mensaje) {
    if (parametro == 0) {
        setTimeout("$('#contenedor_exito').slideUp(200).css('display', 'none')", 4000);
    } else if (parametro == 1) {
        $("#contenedor_exito").slideDown(200).css("display", "block");
        $("#contenedor_exito").html(mensaje);
    }
}

//Funcion de mostrar el div contenedor_error
function display_contenedor_error(parametro, mensaje) {
    if (parametro == 0) {
        setTimeout("$('#contenedor_error').slideUp(200).css('display', 'none')", 4000);
    } else if (parametro == 1) {
        $("#contenedor_error").slideDown(200).css("display", "block");
        $("#contenedor_error").html(mensaje);
    }
}

//Esta funcion se pocesa en el boton regresar.
function regresar_disponibilidad() {
    var params = 'opcion=6';
    llamarAjax("disponibilidad_ajax.php", params, "d_disponibilidad", "");
}

function chocorramo() {
    $('#cmb_hinicio').val('09:30');
}

function validar_aplicar_disponibilidad_mes() {
	mostrar_formulario_flotante(1);
	reducir_formulario_flotante(400, 250);
	posicionarDivFlotante('d_centro');
	confirmar_aplicar_disponibilidad_mes();
}

function confirmar_aplicar_disponibilidad_mes(){
	$('#d_interno').html(
		'<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
			'<tr>' +
				'<th align="center">' +
					'<h4>&iquest;Est&aacute; seguro de guardar esta informaci&oacute;n?</h4>' +
				'</th>' +
			'</tr>' +
			'<tr>' +
				'<th align="center">' +
					'<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" class="btnPrincipal" value="Aceptar" onclick="aplicar_disponibilidad_mes();"/>\n' +
					'<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" class="btnSecundario" value="Cancelar" onclick="cerrar_div_centro();"/> ' +
				'</th>' +
			'</tr>' +
		'</table>');
}

function aplicar_disponibilidad_mes() {
	var params = "opcion=7&id_usuario_prof=" + $('#hdd_id_usuario').val() +
				 "&id_tipo_disponibilidad=" + $('#cmb_disponibilidad_mes').val() +
				 "&id_lugar_disp=" + $('#cmb_lugar_disp_mes').val() +
				 "&mes=" + $('#hdd_mes_sel').val() +
				 "&ano=" + $('#hdd_ano_sel').val();
	
	llamarAjax("disponibilidad_ajax.php", params, "d_guardar_disponibilidad", "terminar_aplicar_disponibilidad_mes()");
}

function terminar_aplicar_disponibilidad_mes() {
    var rta = $('#hdd_resultado_mes').val();
    var id = $('#hdd_id_usuario').val();
    var mes_aux = $('#hdd_mes_sel').val();
	var ano_aux = $('#hdd_ano_sel').val();
	cerrar_div_centro();
	
	if (rta > 0) {
		display_contenedor_exito(1, "Disponibilidades del mes actualizadas con &eacute;xito");
		display_contenedor_exito(0, "");
		disponibilidad(id, "", mes_aux, ano_aux);
	} else {
		display_contenedor_error(1, "Error interno al registrar las disponibilidades");
		display_contenedor_error(0, "");
	}
}

function validar_duplicar_disp_ult_semana() {
	mostrar_formulario_flotante(1);
	reducir_formulario_flotante(400, 250);
	posicionarDivFlotante('d_centro');
	confirmar_duplicar_disp_ult_semana();
}

function confirmar_duplicar_disp_ult_semana(){
	$('#d_interno').html(
		'<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
			'<tr>' +
				'<th align="center">' +
					'<h4>&iquest;Est&aacute; seguro de guardar esta informaci&oacute;n?</h4>' +
				'</th>' +
			'</tr>' +
			'<tr>' +
				'<th align="center">' +
					'<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" class="btnPrincipal" value="Aceptar" onclick="duplicar_disp_ult_semana();"/>\n' +
					'<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" class="btnSecundario" value="Cancelar" onclick="cerrar_div_centro();"/> ' +
				'</th>' +
			'</tr>' +
		'</table>');
}

function duplicar_disp_ult_semana() {
	var params = "opcion=8&id_usuario_prof=" + $('#hdd_id_usuario').val() +
				 "&mes=" + $('#hdd_mes_sel').val() +
				 "&ano=" + $('#hdd_ano_sel').val();
	
	llamarAjax("disponibilidad_ajax.php", params, "d_guardar_disponibilidad", "terminar_duplicar_disp_ult_semana()");
}

function terminar_duplicar_disp_ult_semana() {
    var rta = $('#hdd_resultado_semana').val();
    var id = $('#hdd_id_usuario').val();
    var mes_aux = $('#hdd_mes_sel').val();
	var ano_aux = $('#hdd_ano_sel').val();
	cerrar_div_centro();
	
	if (rta > 0) {
		display_contenedor_exito(1, "Disponibilidades de la &uacute;ltima semana duplicada con &eacute;xito");
		display_contenedor_exito(0, "");
		disponibilidad(id, "", mes_aux, ano_aux);
	} else if (rta == 0) {
		display_contenedor_error(1, "No hay d&iacute;as por configurar en el mes o no hay semanas para duplicar");
		display_contenedor_error(0, "");
	} else {
		display_contenedor_error(1, "Error interno al registrar las disponibilidades");
		display_contenedor_error(0, "");
	}
}
