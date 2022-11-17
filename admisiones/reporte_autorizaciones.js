//Funcion para generar el reporte general
/*function reporteGeneral() {
    var fechaInicial = $('#fechaInicial').val();
    var fechaFinal = $('#fechaFinal').val();
    var id_convenio = $('#cmbConvenio').val();
    var id_plan = $('#cmbPlan').val();
	var id_lugar_cita = $('#cmbSede').val(); 
	
    if (fechaInicial == '' || fechaFinal == '') {
        alert('Error!. Debe seleccionar fecha inicial y fecha final');
	} else if (id_convenio == "") {
		alert('Error!. Debe seleccionar un convenio');
	} else {
        var fechaInicial2;
        var fechaFinal2;
        fechaInicial2 = fechaInicial.split('/');
        fechaInicial2 = fechaInicial2[2] + '-' + fechaInicial2[1] + '-' + fechaInicial2[0];
        fechaFinal2 = fechaFinal.split('/');
        fechaFinal2 = fechaFinal2[2] + '-' + fechaFinal2[1] + '-' + fechaFinal2[0];
		
        var params = 'opcion=1&fecha_inicial=' + str_encode(fechaInicial2) +
					 '&fecha_final=' + str_encode(fechaFinal2) +
					 '&id_convenio=' + id_convenio +
					 '&id_plan=' + id_plan +
					 '&id_lugar_cita' + id_lugar_cita;
		
        llamarAjax("reporte_oportunidad_atencion_ajax.php", params, "reporte", "descargar_pdf();");
    }
}*/

//Reporte General en Excel
function reporteGeneralExcel() {
    var fechaInicial = $('#fechaInicial').val();
    var fechaFinal = $('#fechaFinal').val();
    var id_convenio = $('#cmbConvenio').val();
    var id_plan = $('#cmbPlan').val();
	var id_lugar_cita = $('#cmbSede').val(); 
	
    if (fechaInicial == '' || fechaFinal == '') {
        alert('Debe seleccionar fecha inicial y fecha final');
	} else {
        var fechaInicial2;
        var fechaFinal2;
        fechaInicial2 = fechaInicial.split('/');
        fechaInicial2 = fechaInicial2[2] + '-' + fechaInicial2[1] + '-' + fechaInicial2[0];
        fechaFinal2 = fechaFinal.split('/');
        fechaFinal2 = fechaFinal2[2] + '-' + fechaFinal2[1] + '-' + fechaFinal2[0];
		
        $('#hddfechaInicial').val(fechaInicial2);
        $('#hddfechaFinal').val(fechaFinal2);
        $('#hddconvenio').val(id_convenio);
        $('#hddplan').val(id_plan);
    
         	
        if (isObject(document.getElementById("frm_excel_general"))) {
            document.getElementById("frm_excel_general").submit();
        } else {
            alert("Debe realizar una b\xfasqueda.");
        }
    }
}
/*
function generar_reporte_auditoria_excel() {
    var fecha_ini = $("#txt_fecha_ini_aud").val();
    var fecha_fin = $("#txt_fecha_fin_aud").val();
    var id_convenio = $("#cmb_convenio_aud").val();
	
    if (fecha_ini == "" || fecha_fin == "") {
        alert('Debe seleccionar fecha inicial y fecha final');
    } else {
		var fecha_ini2 = fecha_ini.split("/");
		fecha_ini2 = fecha_ini2[2] + "-" + fecha_ini2[1] + "-" + fecha_ini2[0];
		var fecha_fin2 = fecha_fin.split("/");
		fecha_fin2 = fecha_fin2[2] + "-" + fecha_fin2[1] + "-" + fecha_fin2[0];
		
		$("#hdd_fecha_ini_aud").val(fecha_ini2);
		$("#hdd_fecha_fin_aud").val(fecha_fin2);
		$("#hdd_id_convenio_aud").val(id_convenio);
		
		if (isObject(document.getElementById("frm_excel_aud"))) {
			document.getElementById("frm_excel_aud").submit();
		} else {
			alert("Debe realizar una b\xfasqueda.");
		}
	}
}

//Muestra la ventana flotante de Servicios
function ventanaPacientes(tipo) {
    if (tipo == 1) {//mostrar
        $('#fondo_negro_pacientes').css('display', 'block');
        $('#d_centro_pacientes').slideDown(400).css('display', 'block');
        //Asigna el alto por defecto a la página
        $('#d_interno_pacientes').css({'min-height': '470px'});
        //Envia por ajax la peticion para construir el formulario flotante
        //var tipo_servicio = $('#idServicio').val();
        var hddPacientes = $('#hdd_pacientes').val();
        var params = 'opcion=3&hddPacientes=' + hddPacientes;
        llamarAjax("reporte_oportunidad_atencion_ajax.php", params, "d_interno_pacientes", "");
    } else if (tipo == 0) {//Ocultar
        $("#d_centro_pacientes").css("display", "none");
        $("#fondo_negro_pacientes").css("display", "none");
    }
}



//Funcion que genera el excel de la ventana flotante para Reporte estadístico por paciente
function generar_excel2(idPaciente) {
    var fechaInicial2;
    var fechaFinal2;
    var fechaInicial = $('#fechaInicial2').val();
    var fechaFinal = $('#fechaFinal2').val();
    fechaInicial2 = fechaInicial.split('/');
    fechaInicial2 = fechaInicial2[2] + '-' + fechaInicial2[1] + '-' + fechaInicial2[0];
    fechaFinal2 = fechaFinal.split('/');
    fechaFinal2 = fechaFinal2[2] + '-' + fechaFinal2[1] + '-' + fechaFinal2[0];
    $('#fechaInicial22').val(fechaInicial2);
    $('#fechaFinal22').val(fechaFinal2);
    $('#txtIdPaciente2').val(idPaciente);
    if (isObject(document.getElementById("frm_excel_paciente"))) {
        document.getElementById("frm_excel_paciente").submit();
    } else {
        alert("Debe realizar una b\xfasqueda.");
    }
}

function mostrar_formulario_conceptos(tipo) {
    if (tipo == 1) { //mostrar
        $('#fondo_negro_conceptos').css('display', 'block');
        $('#d_centro_conceptos').slideDown(400).css('display', 'block');

    } else if (tipo == 0) { //Ocultar
        $('#fondo_negro_conceptos').css('display', 'none');
        $('#d_centro_conceptos').slideDown(400).css('display', 'none');
    }
}


*/
function seleccionar_convenio(id_convenio) {
    var params = "opcion=1&id_convenio=" + id_convenio;
	
    llamarAjax("reporte_autorizaciones_ajax.php", params, "d_plan", "");
}
