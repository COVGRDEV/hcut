
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


    var programada = $('#e0').is(':checked') ? $('#e0').val() : 0;
    var cancelada = $('#e1').is(':checked') ? $('#e1').val() : 0;
    var atencion = $('#e2').is(':checked') ? $('#e2').val() : 0;
    var atendida = $('#e3').is(':checked') ? $('#e3').val() : 0;
    var noAsistio = $('#e4').is(':checked') ? $('#e4').val() : 0;
    var noFinalizada = $('#e5').is(':checked') ? $('#e5').val() : 0;
    var sePresento = $('#e6').is(':checked') ? $('#e6').val() : 0;

    if (profesional == '' && lugar == '' && fechaInicial == '' && fechaFinal == '' && cita == '' && tipoCita == '' && hora == '') {//Si todos los campos estan vacios
        $('#contenedor_error').html('Debe seleccionar un tipo de busqueda');//Mensaje de error
        $('#contenedor_error').css({'display': 'block'});//Muestra el mensaje de error

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

        var params = 'opcion=1&profesional=' + profesional + '&lugar=' + lugar + '&fechaInicial=' + fechaInicial_aux + '&fechaFinal=' + fechaFinal_aux + '&cita=' + cita + '&tipoCita=' + tipoCita + '&hora=' + hora + '&programada=' + programada + '&cancelada=' + cancelada + '&atencion=' + atencion + '&atendida=' + atendida + '&noAsistio=' + noAsistio + '&noFinalizada=' + noFinalizada + '&sePresento=' +sePresento;
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
    var estado = $('#cmb_estado_cita').val();
    var fechaInicial = $('#fechaInicial').val();
    var fechaFinal = $('#fechaFinal').val();
    var cita = $('#cita').val();
    var tipoCita = $('#cmb_tipo_cita').val();
    var hora = str_encode($('#cmb_hora').val());
    
    
    
    var programada = $('#e0').is(':checked') ? $('#e0').val() : 0;
    var cancelada = $('#e1').is(':checked') ? $('#e1').val() : 0;
    var atencion = $('#e2').is(':checked') ? $('#e2').val() : 0;
    var atendida = $('#e3').is(':checked') ? $('#e3').val() : 0;
    var noAsistio = $('#e4').is(':checked') ? $('#e4').val() : 0;
    var noFinalizada = $('#e5').is(':checked') ? $('#e5').val() : 0;
    var sePresento = $('#e6').is(':checked') ? $('#e6').val() : 0;
    

    if (profesional == '' && lugar == '' && estado == '' && fechaInicial == '' && fechaFinal == '' && cita == '' && tipoCita == '' && hora == '') {//Si todos los campos estan vacios
        $('#contenedor_error').html('Debe seleccionar un tipo de busqueda');//Mensaje de error
        $('#contenedor_error').css({'display': 'block'});//Muestra el mensaje de error

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


        var params = 'opcion=3&profesional=' + profesional + '&lugar=' + lugar + '&estado=' + estado + '&fechaInicial=' + fechaInicial_aux + '&fechaFinal=' + fechaFinal_aux + '&cita=' + cita + '&profesionalNombre=' + $('#cmb_profesional option:selected').text() + '&lugarNombre=' + $('#cmb_lugar option:selected').text() + '&estadoNombre=' + $('#cmb_estado_cita option:selected').text() + '&tipoCita=' + tipoCita + '&hora=' + hora + '&programada=' + programada + '&cancelada=' + cancelada + '&atencion=' + atencion + '&atendida=' + atendida + '&noAsistio=' + noAsistio + '&noFinalizada=' + noFinalizada + '&sePresento=' +sePresento;
        llamarAjax("reporte_citas_ajax.php", params, "d_generar_pdf", "descargar_pdf();");
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