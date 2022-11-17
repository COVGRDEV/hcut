function validar_busqueda() {
    //Validación
    var fechaInicial = str_encode($('#fechaInicial').val());
    var fechaFinal = str_encode($('#fechaFinal').val());
    var cant_estados = $('#hdd_cant_estados_citas').val();

    var pasaporte = false;
    if (fechaInicial != "" && fechaFinal != "") {
        for (var a = 1; a <= cant_estados; a++) {
            if (!pasaporte) {
                var cheked = $('#tp' + a).is(':checked') ? 1 : 0;
                if (cheked == 1) {
                    pasaporte = true;
                    break;
                }
            }
        }
    }

    return pasaporte;
}

//Funcion del boton: Buscar
function buscar() {
    if (validar_busqueda()) {
        var profesional = str_encode($('#cmb_profesional').val());
        var lugar = str_encode($('#cmb_lugar').val());
        var fechaInicial = str_encode($('#fechaInicial').val());
        var fechaFinal = str_encode($('#fechaFinal').val());
        var cita = str_encode($('#cita').val());
        var tipoCita = str_encode($('#cmb_tipo_cita').val());
        var hora = str_encode($('#cmb_hora').val());
        var estado = $('#hdd_cant_estados_citas').val();
        var convenio = $('#cmb_convenio').val();

        //Vuelve a ajecutar el mimos código de la linea 41
        $('#contenedor_error').css({'display': 'none'});//Oculta el mensaje de error en caso de estar visible

        var fechaInicial_aux = '';
        var fechaFinal_aux = '';

        if (fechaInicial != '') {//Convierte la fecha a formato SQL
            var inicial_aux = fechaInicial.split('/');
            fechaInicial_aux = inicial_aux[2] + '-' + inicial_aux[1] + '-' + inicial_aux[0];
        }
        if (fechaFinal != '') {//Convierte la fecha a formato SQL
            var final_aux = fechaFinal.split('/');
            fechaFinal_aux = final_aux[2] + '-' + final_aux[1] + '-' + final_aux[0];
        }

        var params = 'opcion=1&profesional=' + profesional +
                '&lugar=' + lugar +
                '&fechaInicial=' + fechaInicial_aux +
                '&fechaFinal=' + fechaFinal_aux +
                '&cita=' + cita +
                '&tipoCita=' + tipoCita +
                '&hora=' + hora +
                '&contadorTp=' + estado +
                '&convenio=' + convenio;

        //Enviar los checkbox del estado de cita
        for (a = 1; a <= estado; a++) {
            var cheked = $('#tp' + a).is(':checked') ? $('#tp' + a).val() : 0;
            params += '&tp' + a + '=' + cheked;
        }

        llamarAjax("reporte_citas_ajax.php", params, "reporte_citas", "validar_fechas_busqueda();");
	}else {
        $('#contenedor_error').html('Debe seleccionar las fechas inicial y final y por lo menos un estado de la cita');//Mensaje de error
        $('#contenedor_error').css({'display': 'block'});//Muestra el mensaje de error
    }
}

function validar_fechas_busqueda() {
	if($("#hdd_error_dias_b").length){
		$('#contenedor_error').html('Existe más de un mes entre las fechas seleccionadas');
		$('#contenedor_error').css({'display': 'block'});
	}
}

//Funcion que muestra el detalle de la cita
function detalleCita(idCita) {
    var params = 'opcion=2&idCita=' + idCita;
    llamarAjax("reporte_citas_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1);");
}

function generarPDF() {
    if (validar_busqueda()) {
        var profesional = $('#cmb_profesional').val();
        var lugar = $('#cmb_lugar').val();
        var fechaInicial = $('#fechaInicial').val();
        var fechaFinal = $('#fechaFinal').val();
        var cita = $('#cita').val();
        var tipoCita = $('#cmb_tipo_cita').val();
        var hora = str_encode($('#cmb_hora').val());
        var estado = $('#hdd_cant_estados_citas').val();
        var convenio = $('#cmb_convenio').val();

        $('#contenedor_error').css({'display': 'none'});//Oculta el mensaje de error en caso de estar visible

        var fechaInicial_aux = '';
        var fechaFinal_aux = '';

        if (fechaInicial != '') {//Convierte la fecha a formato SQL
            var inicial_aux = fechaInicial.split('/');
            fechaInicial_aux = inicial_aux[2] + '-' + inicial_aux[1] + '-' + inicial_aux[0];
        }
        if (fechaFinal != '') {//Convierte la fecha a formato SQL
            var final_aux = fechaFinal.split('/');
            fechaFinal_aux = final_aux[2] + '-' + final_aux[1] + '-' + final_aux[0];
        }

        var params = 'opcion=3&profesional=' + profesional +
                '&lugar=' + lugar +
                '&fechaInicial=' + fechaInicial_aux +
                '&fechaFinal=' + fechaFinal_aux +
                '&cita=' + cita +
                '&profesionalNombre=' + $('#cmb_profesional option:selected').text() +
                '&lugarNombre=' + $('#cmb_lugar option:selected').text() +
                '&estadoNombre=' + $('#cmb_estado_cita option:selected').text() +
                '&tipoCita=' + tipoCita +
                '&hora=' + hora +
                '&contadorTp=' + estado +
                '&convenio=' + convenio;

        for (a = 1; a <= estado; a++) {
            var cheked = $('#tp' + a).is(':checked') ? $('#tp' + a).val() : 0;
            params += '&tp' + a + '=' + cheked;
        }

        llamarAjax("reporte_citas_ajax.php", params, "d_generar_pdf", "descargar_pdf();");//descargar_pdf();
    } else {
        $('#contenedor_error').html('Debe seleccionar las fechas inicial y final y por lo menos un estado de la cita');//Mensaje de error
        $('#contenedor_error').css({'display': 'block'});//Muestra el mensaje de error
    }
}

function descargar_pdf() {
    if (isObject(document.getElementById("hdd_archivo_pdf"))) {
        var nombreArchivo = document.getElementById("hdd_archivo_pdf").value;

        var ventanaAux = window.open("../funciones/pdf/descargar_archivo.php?nombre_archivo=" + escape(nombreArchivo), '_blank');
        ventanaAux.focus();
    }else if($("#hdd_error_dias").length){
		alert("Error - BUSQUEDA SUPERIOR A UN MES");
					
	}else {
        alert("Archivo no disponible");
    }
}

//Genera el reporte para la cita
function generarPDFCita() {
    //Validación
    var idCita = $('#hdd_idCita').val();

    var params = 'opcion=4&idCita=' + idCita;
    llamarAjax("reporte_citas_ajax.php", params, "d_generar_pdf2", "descargar_pdf2();");
}

function descargar_pdf2() {
    if (isObject(document.getElementById("hdd_archivo_pdf2"))) {
        var nombreArchivo = document.getElementById("hdd_archivo_pdf2").value;

        //var ventanaAux = window.open("../funciones/pdf/descargar_archivo.php?nombre_archivo=" + escape(nombreArchivo), '_blank');
        //ventanaAux.focus();
        
        window.open("../funciones/abrir_pdf.php?ruta=" + escape(nombreArchivo) + "&nombre_arch=remision.pdf", "_blank");
    } else {
        alert("Archivo no disponible");
    }
}

function exportar_a_excel() {
    if (validar_busqueda()) {
        var profesional = $('#cmb_profesional').val();
        var lugar = $('#cmb_lugar').val();
        var estado = $('#cmb_estado_cita').val();
        var fechaInicial = $('#fechaInicial').val();
        var fechaFinal = $('#fechaFinal').val();
        var cita = $('#cita').val();
        var tipoCita = $('#cmb_tipo_cita').val();
        var hora = str_encode($('#cmb_hora').val());
        var estado = $('#hdd_cant_estados_citas').val();
        var convenio = $('#cmb_convenio').val();

        $('#contenedor_error').css({'display': 'none'});//Oculta el mensaje de error en caso de estar visible

        var fechaInicial_aux = '';
        var fechaFinal_aux = '';

        if (fechaInicial != '') {//Convierte la fecha a formato SQL
            var inicial_aux = fechaInicial.split('/');
            fechaInicial_aux = inicial_aux[2] + '-' + inicial_aux[1] + '-' + inicial_aux[0];
        }
        if (fechaFinal != '') {//Convierte la fecha a formato SQL
            var final_aux = fechaFinal.split('/');
            fechaFinal_aux = final_aux[2] + '-' + final_aux[1] + '-' + final_aux[0];
        }

        $('#hddProfesional').val(profesional);
        $('#hddLugar').val(lugar);
        $('#hddFechaInicial').val(fechaInicial_aux);
        $('#hddFechaFinal').val(fechaFinal_aux);
        $('#hddCita').val(cita);
        $('#hddTipoCita').val(tipoCita);
        $('#hddHora').val(hora);
        $('#contadorTp').val(estado);
        $('#convenio').val(convenio);

        //Agrega los valores a los checkbox de estado de la cita
        for (a = 1; a <= estado; a++) {
            var cheked = $('#tp' + a).is(':checked') ? $('#tp' + a).val() : 0;
            $('#frm_excel').append('<input type="hidden" id="tp' + a + '" name="tp' + a + '" value="' + cheked + '" />');
        }

        if (isObject(document.getElementById("frm_excel"))) {
            document.getElementById("frm_excel").submit();
        } else {
            alert("Debe realizar una b\xfasqueda.");
        }
    } else {
        $('#contenedor_error').html('Debe seleccionar las fechas inicial y final y por lo menos un estado de la cita');//Mensaje de error
        $('#contenedor_error').css({'display': 'block'});//Muestra el mensaje de error
    }
}

function seleccionar_todos_estados() {
    var cant_estados_citas = parseInt($('#hdd_cant_estados_citas').val(), 10);
    var ind_todos = $("#tp_todos").is(':checked') ? true : false;
    for (a = 1; a <= cant_estados_citas; a++) {
        $("#tp" + a).attr("checked", ind_todos);
    }
}

function generar_plano() {
    if (validar_busqueda()) {
        var profesional = $('#cmb_profesional').val();
        var lugar = $('#cmb_lugar').val();
        var fechaInicial = $('#fechaInicial').val();
        var fechaFinal = $('#fechaFinal').val();
        var cita = $('#cita').val();
        var tipoCita = $('#cmb_tipo_cita').val();
        var hora = str_encode($('#cmb_hora').val());
        var estado = $('#hdd_cant_estados_citas').val();
        var convenio = $('#cmb_convenio').val();

        var fechaInicial_aux = '';
        var fechaFinal_aux = '';

        if (fechaInicial != '') {//Convierte la fecha a formato SQL
            var inicial_aux = fechaInicial.split('/');
            fechaInicial_aux = inicial_aux[2] + '-' + inicial_aux[1] + '-' + inicial_aux[0];
        }
        if (fechaFinal != '') {//Convierte la fecha a formato SQL
            var final_aux = fechaFinal.split('/');
            fechaFinal_aux = final_aux[2] + '-' + final_aux[1] + '-' + final_aux[0];
        }

        $('#contenedor_error').css({'display': 'none'});//Oculta el mensaje de error en caso de estar visible

        var params = 'opcion=5&profesional=' + profesional +
                '&lugar=' + lugar +
                '&fechaInicial=' + fechaInicial_aux +
                '&fechaFinal=' + fechaFinal_aux +
                '&cita=' + cita +
                '&profesionalNombre=' + $('#cmb_profesional option:selected').text() +
                '&lugarNombre=' + $('#cmb_lugar option:selected').text() +
                '&estadoNombre=' + $('#cmb_estado_cita option:selected').text() +
                '&tipoCita=' + tipoCita +
                '&hora=' + hora +
                '&contadorTp=' + estado +
                '&convenio=' + convenio;

        for (a = 1; a <= estado; a++) {
            var cheked = $('#tp' + a).is(':checked') ? $('#tp' + a).val() : 0;
            params += '&tp' + a + '=' + cheked;
        }

        llamarAjax("reporte_citas_ajax.php", params, "d_generar_plano", "descargar_plano();");//descargar_pdf();
    } else {
        $('#contenedor_error').html('Debe seleccionar las fechas inicial y final y por lo menos un estado de la cita');//Mensaje de error
        $('#contenedor_error').css({'display': 'block'});//Muestra el mensaje de error
    }
}

function descargar_plano() {
    if (isObject(document.getElementById("hdd_nombre_plano_zip"))) {
        window.open($("#hdd_nombre_plano_zip").val(), "_blank");
    } else {
        alert("Error al generar el archivo plano");
    }
}
