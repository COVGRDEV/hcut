//Funcion para generar el reporte general
function reporteGeneral() {
    var fechaInicial = $('#fechaInicial').val();
    var fechaFinal = $('#fechaFinal').val();
    var plan = $('#cmbConvenio').val();

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
					 '&id_plan=' + plan;
		
        llamarAjax("reporte_tesoreria_usuario_ajax.php", params, "reporte", "descargar_pdf();");
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
