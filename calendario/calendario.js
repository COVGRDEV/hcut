var mes_seleccionado = "";
var anio_seleccionado = "";
function calendario(mes, anio) {
	mes_seleccionado = mes;
	anio_seleccionado = anio;
    
    var params = 'opcion=1&mes=' + mes + '&anio=' + anio;
	
    llamarAjax("calendario_ajax.php", params, "d_calendario", "");
	document.getElementById("d_calendario").style.display = "block";
}

function calendario_base() {
	calendario(mes_seleccionado, anio_seleccionado);
}

function modificar_calendario(fecha, ind_laboral) {
    var params = 'opcion=2&fecha=' + fecha + '&ind_laboral=' + ind_laboral;
	
	llamarAjax("calendario_ajax.php", params, "d_interno", "confirmar_modificar_calendario()");
}

function confirmar_modificar_calendario() {
	var ind_resultado = -1;
	if (isObject(document.getElementById("hdd_modificar_calendario"))) {
		ind_resultado = parseInt($('#hdd_modificar_calendario').val(), 10);
	}
	
    if (ind_resultado > 0) {
        $("#contenedor_exito").css("display", "block");
        $('#contenedor_exito').html('Datos guardados correctamente');
    } else {
        $("#contenedor_error").css("display", "block");
        $('#contenedor_error').html('Error al guardar el d&iacute;a');
    }
	mostrar_formulario_flotante(1);
	setTimeout("mostrar_formulario_flotante(0)", 1000);
	
	calendario_base();
}
