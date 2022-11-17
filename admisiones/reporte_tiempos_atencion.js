//Reporte General en Excel
function reporteGeneralExcel() {
    var fechaInicial = $('#fechaInicial').val();
    var fechaFinal = $('#fechaFinal').val();
    var id_convenio = $('#cmbConvenio').val();
    var id_plan = $('#cmbPlan').val();
	
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
function seleccionar_convenio(id_convenio) {

    var params = "opcion=10&id_convenio=" + id_convenio;
	
    llamarAjax("reporte_tiempos_atencion_ajax.php", params, "d_plan", "");
}