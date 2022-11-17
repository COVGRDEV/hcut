$(document).ready(function() {
    //Carga el listado de convenios
    muestra_formulas_medicas();
});

//Carga los datos de la tabla: datos_entidad
function muestra_formulas_medicas() {
    var params = 'opcion=1';
	
    llamarAjax("plantillas_formulas_ajax.php", params, "principaltk", "");
}

//Funcion que busca las f´rrmular médicas
function buscarFormulas() {
    $("#frmBuscarPlantilla").validate({
        rules: {
            txtParametro: {
                required: true,
            },
        },
        submitHandler: function() {
            var parametro = $('#txtParametro').val();
            var params = 'opcion=2&parametro=' + parametro;
            llamarAjax("plantillas_formulas_ajax.php", params, "principaltk", "");
            return false;
        },
    });
}

//Ventana modificar plantilla
function ventanaModificar(idFormula) {
    var params = 'opcion=3&idFormula=' + idFormula;
	
    llamarAjax("plantillas_formulas_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1);asigna_formato_textarea();");
}

function asigna_formato_textarea() {
   new nicEditor().panelInstance('text_formula');
   
   $(".div_centro").css({'text-align':'justify'});
   $(".div_interno").css("text-align","justify");
   $("body").css("text-align","justify");
}

//Procesa la impresion
function imprimir() {
	var tipo = $('#cmb_tipoImpresion').val();
    var idFormula = $('#hddidFormula').val();
    var text_despacho = $("div.nicEdit-main").html();
    
	result = 0;
	$('#cmb_tipoImpresion').removeClass("borde_error");
	$("#contenedor_error").css("display", "none");
	
	if (tipo == '0') {
		$('#cmb_tipoImpresion').addClass("borde_error");
		result = 1;
	}
	
	if (result == 0) {
		var params = 'opcion=6&idFormula=' +idFormula+'&tipoImpresion=' +tipo+ '&text_despacho=' + text_despacho;
    	llamarAjax("plantillas_formulas_ajax.php", params, "rtaImpresion", "imprSelec();");  
	} else {
		$("#contenedor_error").css("display", "block");
    	$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
		return false;
	}
}

function imprSelec(){
	var ficha=document.getElementById('campo_imprimir');
	var ventimp=window.open(' ','popimpr');
	ventimp.document.write(ficha.innerHTML);
	ventimp.document.close();
	ventimp.print();
	ventimp.close();
}

//Funcion para guardar la plantilla de la formula
function guardarPlantilla(accion) {
    var result = 0;
    var text_formula = $("div.nicEdit-main").html();
    var idCodigo = $('#hddidFormula').val();
	
    if (text_formula == '<br>') {
        $("div.nicEdit-main").addClass("borde_error");
        result = 1;
    }
	
    if (result == 0) {//Guarda
        var tipo_impresion;
        if ($("#indEstado").is(':checked')) {
            var indEstadoAux = 1;  
        } else {
            var indEstadoAux = 0; 
        }
		
		var titulo_formula = str_encode($("#txt_titulo_formula").val());
        var text_formula2 = str_encode($("div.nicEdit-main").html());
        
        var params = "opcion=4&indEstado=" + indEstadoAux +
					 "&titulo_formula=" + titulo_formula +
					 "&text_formula=" + text_formula2 +
					 "&accion=" + accion +
					 "&idFormula=" + idCodigo;
		
        llamarAjax("plantillas_formulas_ajax.php", params, "hddResultado", "postGuardarPlantilla();");
    } else {
        $("#contenedor_error").css("display", "block");
        $('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
        return false;
    }
}

//Funcion que verifica que se haya guardado el registro
function postGuardarPlantilla() {
    var resultado = $('#hddResultado').text();
	
    if (resultado == '1') {
        $('#contenedor_exito').css("display", "block");
        $('#contenedor_exito').html('El registro ha sido guardado.');
        mostrar_formulario_flotante(0);
        muestra_formulas_medicas();
    } else if (resultado == '-1') {
        $('#contenedor_error').css("display", "block");
        $('#contenedor_error').html('Error al guardar el registro. Intente de nuevo');
    }
}

//Muestra la ventana de crear nueva plantilla
function ventanaNuevo() {
    var params = 'opcion=5';
	
    llamarAjax("plantillas_formulas_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1);asigna_formato_textarea();");
}
