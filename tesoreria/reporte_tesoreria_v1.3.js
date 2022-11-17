//Funcion para generar el reporte general
function reporteGeneral() {
    var fechaInicial = $('#fechaInicial').val();
    var fechaFinal = $('#fechaFinal').val();
    var plan = $('#cmbConvenio').val();
    var cod_insumo = $('#hdd_cups').val();
    var tipo_precio = $('#hdd_tipo_precio').val();
    var usuario_adm = $('#cmbUsuarioAdm').val();
    var usuario = $('#cmbUsuario').val();

    if (fechaInicial == '' || fechaFinal == '') {
        alert('Error!. Debe seleccionar fecha inicial y fecha final');
    } else {
        var fechaInicial2;
        var fechaFinal2;
        fechaInicial2 = fechaInicial.split('/');
        fechaInicial2 = fechaInicial2[2] + '-' + fechaInicial2[1] + '-' + fechaInicial2[0];
        fechaFinal2 = fechaFinal.split('/');
        fechaFinal2 = fechaFinal2[2] + '-' + fechaFinal2[1] + '-' + fechaFinal2[0];
		
        var params = 'opcion=1&fecha_inicial=' + str_encode(fechaInicial2) +
					 '&fecha_final=' + str_encode(fechaFinal2) +
					 '&id_plan=' + plan +
					 '&cod_insumo=' + cod_insumo +
					 '&tipo_precio=' + tipo_precio +
					 '&id_usuario_adm=' + usuario_adm +
					 '&id_usuario=' + usuario;
		
        llamarAjax("reporte_tesoreria_ajax.php", params, "reporte", "descargar_pdf();");
    }
}

//Reporte General en Excel
function reporteGeneralExcel() {
    var fechaInicial = $('#fechaInicial').val();
    var fechaFinal = $('#fechaFinal').val();
    var id_convenio = $('#cmbConvenio').val();
    var cod_insumo = $('#hdd_cups').val();
    var tipo_precio = $('#hdd_tipo_precio').val();
    var usuario_adm = $('#cmbUsuarioAdm').val();
    var usuario = $('#cmbUsuario').val();
	
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
        $('#hddcodinsumo').val(cod_insumo);
        $('#hddtipoprecio').val(tipo_precio);
        $('#hddusuarioadm').val(usuario_adm);
        $('#hddusuario').val(usuario);
		
        if (isObject(document.getElementById("frm_excel_general"))) {
            document.getElementById("frm_excel_general").submit();
        } else {
            alert("Debe realizar una b\xfasqueda.");
        }
    }
}

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

//Funcion que descarga reporte gerenal de tesoreria
function descargar_pdf() {
    if (isObject(document.getElementById("hdd_archivo_pdf"))) {
        var nombreArchivo = document.getElementById("hdd_archivo_pdf").value;
        var ventanaAux = window.open("../funciones/pdf/descargar_archivo.php?nombre_archivo=" + escape(nombreArchivo), '_blank');
        ventanaAux.focus();
    } else {
        alert("Archivo no disponible");
    }
}


//Funcion que descarga reporte estadistico
function descargar_pdf2() {
    if (isObject(document.getElementById("hdd_archivo_pdf2"))) {
        var nombreArchivo = document.getElementById("hdd_archivo_pdf2").value;
        if (nombreArchivo == '-2') {
            ventanaPacientes(1);
        } else {
            var ventanaAux = window.open("../funciones/pdf/descargar_archivo.php?nombre_archivo=" + escape(nombreArchivo), '_blank');
            ventanaAux.focus();
        }

    } else {
        alert("Archivo no disponible");
    }
}


//Funcion para generar el reporte general
function reporteEstadisticopaciente() {
    var fechaInicial = $('#fechaInicial2').val();
    var fechaFinal = $('#fechaFinal2').val();
    var txtIdPaciente = $('#txtIdPaciente').val();
    if (txtIdPaciente == '') {
        alert('Error!. Debe ingresar el nombre o identificaci\xf3n del paciente');
    } else {
		//Validacion de fechas
        if (fechaInicial != '' && fechaFinal == '') {
            alert('Error!. Debe seleccionar la fecha final');
        } else {
			//Validacion de fechas
            if (fechaFinal != '' && fechaInicial == '') {
                alert('Error!. Debe seleccionar la fecha inicial');
            } else {

                var fechaInicial2;
                var fechaFinal2;
                fechaInicial2 = fechaInicial.split('/');
                fechaInicial2 = fechaInicial2[2] + '-' + fechaInicial2[1] + '-' + fechaInicial2[0];
                fechaFinal2 = fechaFinal.split('/');
                fechaFinal2 = fechaFinal2[2] + '-' + fechaFinal2[1] + '-' + fechaFinal2[0];
                var params = 'opcion=2&fechaInicial=' + str_encode(fechaInicial2) + '&fechaFinal=' + str_encode(fechaFinal2) + '&txtPaciente=' + str_encode(txtIdPaciente);
                llamarAjax("reporte_tesoreria_ajax.php", params, "reporteEstadistico", "descargar_pdf2();"); //descargar_pdf();
            }
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
        llamarAjax("reporte_tesoreria_ajax.php", params, "d_interno_pacientes", "");
    } else if (tipo == 0) {//Ocultar
        $("#d_centro_pacientes").css("display", "none");
        $("#fondo_negro_pacientes").css("display", "none");
    }
}

//Funcion que se ejecuta en la ventana flotante para generar el reporte estadistico por paciente
function reporteEstadisticopaciente2(idPaciente) {
    var fechaInicial = $('#fechaInicial2').val();
    var fechaFinal = $('#fechaFinal2').val();
    var fechaInicial2;
    var fechaFinal2;
    fechaInicial2 = fechaInicial.split('/');
    fechaInicial2 = fechaInicial2[2] + '-' + fechaInicial2[1] + '-' + fechaInicial2[0];
    fechaFinal2 = fechaFinal.split('/');
    fechaFinal2 = fechaFinal2[2] + '-' + fechaFinal2[1] + '-' + fechaFinal2[0];
	
    var params = 'opcion=2&fechaInicial=' + str_encode(fechaInicial2) + '&fechaFinal=' + str_encode(fechaFinal2) + '&txtPaciente=' + str_encode(idPaciente);
	
    llamarAjax("reporte_tesoreria_ajax.php", params, "reporteEstadistico", "descargar_pdf2();");
}

//Genera el reporte en excel para un paciente
function reporteEstadisticopacienteExcel() {
    var txtIdPaciente = $('#txtIdPaciente').val();
    if (txtIdPaciente == '') {
		alert('Error!. Debe ingresar el nombre o identificaci\xf3n del paciente');
	} else {
		//Validacion de fechas
        if (fechaInicial != '' && fechaFinal == '') {
            alert('Error!. Debe seleccionar la fecha final');
        } else {
			//Validacion de fechas
            if (fechaFinal != '' && fechaInicial == '') {
                alert('Error!. Debe seleccionar la fecha inicial');
            } else {
                var params = 'opcion=4&txtPaciente=' + str_encode(txtIdPaciente);
                llamarAjax("reporte_tesoreria_ajax.php", params, "rtaExcel", "generar_excel();");
            }
        }
    }
}

//Funcion que descarga reporte estadistico
function generar_excel() {
    var fechaInicial = $('#fechaInicial2').val();
    var fechaFinal = $('#fechaFinal2').val();
    var txtIdPaciente = $('#hdd_paciente_hallado').val();
    var resultado = $('#hdd_archivo_excel2').val();
	
    if (resultado == '1') {
        //Genera el excel
        var fechaInicial2;
        var fechaFinal2;
        fechaInicial2 = fechaInicial.split('/');
        fechaInicial2 = fechaInicial2[2] + '-' + fechaInicial2[1] + '-' + fechaInicial2[0];
        fechaFinal2 = fechaFinal.split('/');
        fechaFinal2 = fechaFinal2[2] + '-' + fechaFinal2[1] + '-' + fechaFinal2[0];
        $('#fechaInicial22').val(fechaInicial2);
        $('#fechaFinal22').val(fechaFinal2);
        $('#txtIdPaciente2').val(txtIdPaciente);
        if (isObject(document.getElementById("frm_excel_paciente"))) {
            document.getElementById("frm_excel_paciente").submit();
        } else {
            alert("Debe realizar una b\xfasqueda.");
        }
    } else if (resultado > '1') {
        //Muestra ventana emergente donde lista los usuarios encontrados
        ventanaPacientesExcel(1);
    }
    else if (resultado == '0') {
        alert('No hay resultados');
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

//Muestra la ventana flotante de Servicios
function ventanaPacientesExcel(tipo) {
    if (tipo == 1) {//mostrar
        $('#fondo_negro_pacientes').css('display', 'block');
        $('#d_centro_pacientes').slideDown(400).css('display', 'block');
		
        //Asigna el alto por defecto a la página
        $('#d_interno_pacientes').css({'min-height': '470px'});
		
        //Envia por ajax la peticion para construir el formulario flotante
        var hddPacientes = $('#hdd_pacientes_excel').val();
        var params = 'opcion=5&hddPacientes=' + hddPacientes;
		
        llamarAjax("reporte_tesoreria_ajax.php", params, "d_interno_pacientes", "");
    } else if (tipo == 0) {//Ocultar
        $("#d_centro_pacientes").css("display", "none");
        $("#fondo_negro_pacientes").css("display", "none");
    }
}

function abrir_buscar_concepto() {
	$("#d_interno_conceptos").html("");
	
    var params = "opcion=6";
	
    llamarAjax("reporte_tesoreria_ajax.php", params, "d_interno_conceptos", "");
	mostrar_formulario_conceptos(1);
}

function limpiar_concepto() {
	$("#hdd_cups").val("");
	$("#hdd_tipo_precio").val("");
	$("#txt_cups").val("");
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

function buscar_procedimientos() {
	if (trim($("#txp_procedimiento_b").val()) == "") {
		alert("Debe indicar el c\xf3digo o nombre a buscar");
		$("#txp_procedimiento_b").focus();
		return;
	}
	
    var params = "opcion=7&texto_b=" + str_encode($("#txp_procedimiento_b").val());
	
    llamarAjax("reporte_tesoreria_ajax.php", params, "d_buscar_procedimientos", "");
}

function seleccionar_procedimiento(cod_insumo, nombre_insumo, tipo_precio) {
	$("#hdd_cups").val(cod_insumo);
	$("#txt_cups").val(nombre_insumo);
	$("#hdd_tipo_precio").val(tipo_precio);
	
	mostrar_formulario_conceptos(0);
}
