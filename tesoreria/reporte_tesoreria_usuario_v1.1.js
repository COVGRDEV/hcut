//Funcion para generar el reporte general
function reporteGeneral() {
    var fecha = $("#txt_fecha").val();
    var id_convenio = $("#cmb_convenio").val();

    if (fecha == "") {
        alert("Error - Debe seleccionar una fecha");
    } else {
        var fecha2;
        fecha2 = fecha.split("/");
        fecha2 = fecha2[2] + "-" + fecha2[1] + "-" + fecha2[0];
		
        var params = "opcion=1&fecha=" + str_encode(fecha2) +
					 "&id_convenio=" + id_convenio;
		
        llamarAjax("reporte_tesoreria_usuario_ajax.php", params, "d_reporte", "descargar_pdf();");
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
