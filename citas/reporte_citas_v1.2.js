
//Funcion del boton: Buscar
function buscar() {
    //Validación
    var profesional = str_encode($('#cmb_profesional').val());
    var lugar = str_encode($('#cmb_lugar').val());
    //var estado = str_encode($('#cmb_estado_cita').val());
    var fechaInicial = str_encode($('#fechaInicial').val());
    var fechaFinal = str_encode($('#fechaFinal').val());
    var cita = str_encode($('#cita').val());
    var tipoCita = str_encode($('#cmb_tipo_cita').val());
    var hora = str_encode($('#cmb_hora').val());
    var estado = $('#hddTiposCita').val();
    var convenio = $('#cmb_convenio').val();


    if (profesional == '' && lugar == '' && fechaInicial == '' && fechaFinal == '' && cita == '' && tipoCita == '' && hora == '' && convenio == '') {//Si todos los campos estan vacios

        //Segunda validacion para los checkbox de esstado de la cita generados por codigo
        var pasaporte = false;
        for (a = 1; a <= estado; a++) {
            if (!pasaporte) {
                var cheked = $('#tp' + a).is(':checked') ? 1 : 0;
                if (cheked == 1) {
                    pasaporte = true;
                }
            }
        }

        if (pasaporte) {

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

            llamarAjax("reporte_citas_ajax.php", params, "reporte_citas", "");
        } else {
            $('#contenedor_error').html('Debe seleccionar un tipo de busqueda');//Mensaje de error
            $('#contenedor_error').css({'display': 'block'});//Muestra el mensaje de error
        }



    } else {
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

        llamarAjax("reporte_citas_ajax.php", params, "reporte_citas", "");
    }
}


//Funcion que muestra el detalle de la cita
function detalleCita(idCita) {

    var params = 'opcion=2&idCita=' + idCita;
    llamarAjax("reporte_citas_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1);");
}



function generarPDF() {

    //Validación
    var profesional = $('#cmb_profesional').val();
    var lugar = $('#cmb_lugar').val();
    //var estado = $('#cmb_estado_cita').val();
    var fechaInicial = $('#fechaInicial').val();
    var fechaFinal = $('#fechaFinal').val();
    var cita = $('#cita').val();
    var tipoCita = $('#cmb_tipo_cita').val();
    var hora = str_encode($('#cmb_hora').val());
    var estado = $('#hddTiposCita').val();
    var convenio = $('#cmb_convenio').val();

    if (profesional == '' && lugar == '' && fechaInicial == '' && fechaFinal == '' && cita == '' && tipoCita == '' && hora == '' && convenio == '') {//Si todos los campos estan vacios

        //Segunda validacion para los checkbox de esstado de la cita generados por codigo
        var pasaporte = false;
        for (a = 1; a <= estado; a++) {
            if (!pasaporte) {
                var cheked = $('#tp' + a).is(':checked') ? 1 : 0;
                if (cheked == 1) {
                    pasaporte = true;
                }
            }
        }

        if (pasaporte) {
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

            //Enviar los checkbox del estado de cita
            for (a = 1; a <= estado; a++) {
                var cheked = $('#tp' + a).is(':checked') ? $('#tp' + a).val() : 0;
                params += '&tp' + a + '=' + cheked;
            }
            
            llamarAjax("reporte_citas_ajax.php", params, "d_generar_pdf", "descargar_pdf();");//descargar_pdf();
        } else {
            $('#contenedor_error').html('Debe seleccionar un tipo de busqueda');//Mensaje de error
            $('#contenedor_error').css({'display': 'block'});//Muestra el mensaje de error
        }


    } else {
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

        /*
         var params = 'opcion=1&profesional=' + profesional +
         '&lugar=' + lugar +
         '&fechaInicial=' + fechaInicial_aux +
         '&fechaFinal=' + fechaFinal_aux +
         '&cita=' + cita +
         '&tipoCita=' + tipoCita +
         '&hora=' + hora +
         '&contadorTp=' + estado +
         '&convenio=' + convenio;*/

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

        //Enviar los checkbox del estado de cita
        for (a = 1; a <= estado; a++) {
            var cheked = $('#tp' + a).is(':checked') ? $('#tp' + a).val() : 0;
            params += '&tp' + a + '=' + cheked;
        }

        llamarAjax("reporte_citas_ajax.php", params, "d_generar_pdf", "descargar_pdf();");//descargar_pdf();
    }

}


function descargar_pdf() {
    if (isObject(document.getElementById("hdd_archivo_pdf"))) {
        var nombreArchivo = document.getElementById("hdd_archivo_pdf").value;

        var ventanaAux = window.open("../funciones/pdf/descargar_archivo.php?nombre_archivo=" + escape(nombreArchivo), '_blank');
        ventanaAux.focus();
    } else {
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

        var ventanaAux = window.open("../funciones/pdf/descargar_archivo.php?nombre_archivo=" + escape(nombreArchivo), '_blank');
        ventanaAux.focus();
    } else {
        alert("Archivo no disponible");
    }
}



function exportar_a_excel() {

    //Validación
    var profesional = $('#cmb_profesional').val();
    var lugar = $('#cmb_lugar').val();
    var estado = $('#cmb_estado_cita').val();
    var fechaInicial = $('#fechaInicial').val();
    var fechaFinal = $('#fechaFinal').val();
    var cita = $('#cita').val();
    var tipoCita = $('#cmb_tipo_cita').val();
    var hora = str_encode($('#cmb_hora').val());
    var estado = $('#hddTiposCita').val();
    var convenio = $('#cmb_convenio').val();

    if (profesional == '' && lugar == '' && fechaInicial == '' && fechaFinal == '' && cita == '' && tipoCita == '' && hora == '' && convenio == '') {//Si todos los campos estan vacios

        //Segunda validacion para los checkbox de esstado de la cita generados por codigo
        var pasaporte = false;
        for (a = 1; a <= estado; a++) {
            if (!pasaporte) {
                var cheked = $('#tp' + a).is(':checked') ? 1 : 0;
                if (cheked == 1) {
                    pasaporte = true;
                }
            }
        }

        if (pasaporte) {

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
            $('#contenedor_error').html('Debe seleccionar un tipo de busqueda');//Mensaje de error
            $('#contenedor_error').css({'display': 'block'});//Muestra el mensaje de error
        }

    } else {
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

    }



}
