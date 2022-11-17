//Funcion que genera el informe
function generarInforme() {
	var fechaInicial = $("#fechaInicial").val();
	var fechaFinal = $("#fechaFinal").val();
	var convenio = $("#cmb_convenio").val();
	
	if (fechaInicial == "" || fechaFinal == "") {
		alert("Debe ingresar la fecha inicial y la fecha final");
	} else {
		var fechaInicial_aux = fechaInicial.split("/");
		var fechaFinal_aux = fechaFinal.split("/");
		
		$("#fechaInicial2").val(fechaInicial_aux[2] + "-" + fechaInicial_aux[1] + "-" + fechaInicial_aux[0]);
		$("#fechaFinal2").val(fechaFinal_aux[2] + "-" + fechaFinal_aux[1] + "-" + fechaFinal_aux[0]);
		
		if (convenio == "") {
			convenio = 0;
		}
		
		$("#convenio2").val(convenio);
		
		//Enviar los datos del formulario
		if (isObject(document.getElementById("frmReporteOportunidad2"))) {
			document.getElementById("frmReporteOportunidad2").submit();
		} else {
			alert("Debe realizar una b\xfasqueda.");
		}
	}
}

function generar_reporte_colpatria() {
	var fecha_ini = $("#txt_fecha_ini_colpatria").val();
	var fecha_fin = $("#txt_fecha_fin_colpatria").val();
	var id_convenio = $("#cmb_convenio_colpatria").val();
	
	if (fecha_ini == "" || fecha_fin == "") {
		alert("Debe ingresar la fecha inicial y la fecha final");
		return;
	}
	if (id_convenio == "") {
		alert("Debe seleccionar un convenio");
		return;
	}
	
	var params = "opcion=1&fecha_ini=" + fecha_ini +
				 "&fecha_fin=" + fecha_fin +
				 "&id_convenio=" + id_convenio;
	
	llamarAjax("reporte_oportunidad_ajax.php", params, "d_reporte", "descargar_pdf();");
}

function generar_reporte_colmedica() {
	var fecha_ini = $("#txt_fecha_ini_colmedica").val();
	var fecha_fin = $("#txt_fecha_fin_colmedica").val();
	var id_convenio = $("#cmb_convenio_colmedica").val();
	
	if (fecha_ini == "" || fecha_fin == "") {
		alert("Debe ingresar la fecha inicial y la fecha final");
		return;
	}
	if (id_convenio == "") {
		alert("Debe seleccionar un convenio");
		return;
	}
	
	var params = "opcion=2&fecha_ini=" + fecha_ini +
				 "&fecha_fin=" + fecha_fin +
				 "&id_convenio=" + id_convenio;
	
	llamarAjax("reporte_oportunidad_ajax.php", params, "d_reporte", "descargar_pdf();");
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
