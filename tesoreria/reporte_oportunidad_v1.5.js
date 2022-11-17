//Funcion que genera el informe
function generarInforme() {
	var fechaInicial = $("#fechaInicial").val();
	var fechaFinal = $("#fechaFinal").val();
	var id_convenio = $("#cmb_convenio").val();
	var id_plan = $("#cmbPlan").val();
	
	if (fechaInicial == "" || fechaFinal == "") {
		alert("Debe ingresar la fecha inicial y la fecha final");
	} else {
		var fechaInicial_aux = fechaInicial.split("/");
		var fechaFinal_aux = fechaFinal.split("/");
		
		$("#fechaInicial2").val(fechaInicial_aux[2] + "-" + fechaInicial_aux[1] + "-" + fechaInicial_aux[0]);
		$("#fechaFinal2").val(fechaFinal_aux[2] + "-" + fechaFinal_aux[1] + "-" + fechaFinal_aux[0]);
		
		if (id_convenio == "") {
			alert("Debe seleccionar un convenio");
			return;
		}
		$("#convenio2").val(id_convenio);
		$("#plan").val(id_plan);
		
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

function generar_reporte_colpatria_excel() {
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
	
	$("#hdd_fecha_ini_colpatria_1").val(fecha_ini);
	$("#hdd_fecha_fin_colpatria_1").val(fecha_fin);
	$("#hdd_convenio_colpatria_1").val(id_convenio);
	
	//Enviar los datos del formulario
	if (isObject(document.getElementById("frmReporteColpatriaExcel"))) {
		document.getElementById("frmReporteColpatriaExcel").submit();
	} else {
		alert("Debe realizar una b\xfasqueda.");
	}
}

function generar_reporte_colpatria_excel_2() {
	var fecha_ini = $("#txt_fecha_ini_colpatria_2").val();
	var fecha_fin = $("#txt_fecha_fin_colpatria_2").val();
	var id_convenio = $("#cmb_convenio_colpatria_2").val();
	
	
	if (fecha_ini == "" || fecha_fin == "") {
		alert("Debe ingresar la fecha inicial y la fecha final");
		return;
	}
	if (id_convenio == "") {
		alert("Debe seleccionar un convenio");
		return;
	}
	
	$("#hdd_fecha_ini_colpatria_2").val(fecha_ini);
	$("#hdd_fecha_fin_colpatria_2").val(fecha_fin);
	
	$("#hdd_convenio_colpatria_2").val(id_convenio);
	
	//Enviar los datos del formulario
	if (isObject(document.getElementById("frmReporteColpatriaExcel2"))) {
		document.getElementById("frmReporteColpatriaExcel2").submit();
	} else {
		alert("Debe realizar una b\xfasqueda.");
	}
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

function generar_reporte_foscal() {
	var fechaInicial = $("#txt_fecha_ini_foscal").val();
	var fechaFinal = $("#txt_fecha_fin_foscal").val();
	var convenio = $("#cmb_convenio_foscal").val();
	
	
	if (fechaInicial == "" || fechaFinal == "") {
		alert("Debe ingresar la fecha inicial y la fecha final");
	} else {
		var fechaInicial_aux = fechaInicial.split("/");
		var fechaFinal_aux = fechaFinal.split("/");
		
		$("#hdd_fecha_ini_foscal").val(fechaInicial_aux[2] + "-" + fechaInicial_aux[1] + "-" + fechaInicial_aux[0]);
		$("#hdd_fecha_fin_foscal").val(fechaFinal_aux[2] + "-" + fechaFinal_aux[1] + "-" + fechaFinal_aux[0]);
		
		if (convenio == "") {
			convenio = 0;
		}
		
		$("#hdd_convenio_foscal").val(convenio);
		
		
		//Enviar los datos del formulario
		if (isObject(document.getElementById("frm_reporte_foscal"))) {
			document.getElementById("frm_reporte_foscal").submit();
		} else {
			alert("Debe realizar una b\xfasqueda.");
		}
	}
}

function descargar_pdf() {
    if (isObject(document.getElementById("hdd_archivo_pdf"))) {
        var nombreArchivo = document.getElementById("hdd_archivo_pdf").value;
        var ventanaAux = window.open("../funciones/pdf/descargar_archivo.php?nombre_archivo=" + escape(nombreArchivo), "_blank");
        ventanaAux.focus();
    } else {
        alert("Archivo no disponible");
    }
}

function seleccionar_convenio(id_convenio) {
    var params = "opcion=3&id_convenio=" + id_convenio;
	
    llamarAjax("reporte_oportunidad_ajax.php", params, "d_plan", "");
}


