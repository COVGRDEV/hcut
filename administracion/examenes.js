$(document).ready(function () {
    //Carga el listado de convenios
    muestra_examenes();
});


function muestra_examenes() {
    $('#contenedor_error').css('display', 'none');//Oculta el mensaje de error en caso de estar visible

    //Limpia el input
    $('#txtParametro').val('');

    var params = 'opcion=1&parametro=0';
    llamarAjax("examenes_ajax.php", params, "principal_examenes", "");
}


//Funcion que busca un convenio especifico
function buscarExamen() {
    $("#frmBuscarExamen").validate({
        rules: {
            txtParametro: {
                required: true,
            },
        },
        submitHandler: function () {
            var parametro = $('#txtParametro').val();
            var params = 'opcion=1&parametro=' + parametro;
            llamarAjax("examenes_ajax.php", params, "principal_examenes", "");
            return false;
        },
    });
}

//Funcion que muestra la ventana para nuevo examen
function formNuevoExamen() {//d_interno_adic | d_interno
    var params = 'opcion=2&tipo=1&codExamen=0';
    llamarAjax("examenes_ajax.php", params, "d_interno_adic", "mostrar_formulario_flotante_adic(1);");
}

//Funcion que muestra la ventana para editar examen
function seleccionar_examen(codExamen) {
    var params = 'opcion=2&tipo=0&codExamen=' + codExamen;
    llamarAjax("examenes_ajax.php", params, "d_interno_adic", "mostrar_formulario_flotante_adic(1);");
}

//Funcion que muestra la ventana para nuevo examen
function formSeleccionarProcedimiento() {//d_centro_adic | d_interno
    var params = 'opcion=3&parametro=0';
    llamarAjax("examenes_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1);");
}


//Funcion que busca un convenio especifico
function buscarProcedimiento() {
    $("#frmBuscarProcedimiento").validate({
        rules: {
            txtProcedimiento: {
                required: true,
            },
        },
        submitHandler: function () {
            var parametro = $('#txtProcedimiento').val();
            var params = 'opcion=3&parametro=' + parametro;
            llamarAjax("examenes_ajax.php", params, "d_interno", "");
            return false;
        },
    });
}

//Funcion que agrega el proceso seleccionado al examen
function seleccionar_proceso(codProceso, nomProceso) {
    $('#txtCodProcedimiento').val(codProceso);
    $('#txtNombreProcedimiento').val(nomProceso);
    cerrar_div_centro();
}

/*Guarda el examen*/
function guardaExamen() {
    $("#frmNuevoExamen").validate({
        rules: {
            txtNombreProcedimiento: {
                required: true,
            },
            txtNombre: {
                required: true,
            },
        },
        submitHandler: function () {
            var txtCodProcedimiento = $('#txtCodProcedimiento').val();
            var txtNombre = $('#txtNombre').val();
            var indActivo = $('#indActivo').is(':checked') ? 1 : 0;
            var params = 'opcion=4&tipo=1&txtCodProcedimiento=' + txtCodProcedimiento + '&txtNombre=' + txtNombre + '&indActivo=' + indActivo+ "&codExamen=0";
            llamarAjax("examenes_ajax.php", params, "hdd_resultado_examen", "verificaGuardarEditar();");


            return false;
        },
    });
}



/*Guarda el examen*/
function editaExamen() {
    $("#frmNuevoExamen").validate({
        rules: {
            txtNombreProcedimiento: {
                required: true,
            },
            txtNombre: {
                required: true,
            },
        },
        submitHandler: function () {
            var codExamen = $('#hdd_cod_examen').val();
            var txtCodProcedimiento = $('#txtCodProcedimiento').val();
            var txtNombre = $('#txtNombre').val();
            var indActivo = $('#indActivo').is(':checked') ? 1 : 0;
            var params = 'opcion=4&tipo=0&txtCodProcedimiento=' + txtCodProcedimiento + '&txtNombre=' + txtNombre + '&indActivo=' + indActivo + "&codExamen=" + codExamen;
            llamarAjax("examenes_ajax.php", params, "hdd_resultado_examen", "verificaGuardarEditar();");


            return false;
        },
    });
}


function verificaGuardarEditar() {
    var resultado = $('#hdd_resultado_examen').text();

    if (resultado == '1') {
        $('#contenedor_exito').css('display', 'block');
        $('#contenedor_exito').html('El registro ha sido guardado');

        cerrar_div_centro_adic();
        muestra_examenes();

        setTimeout(function () {
            $('#contenedor_exito').css('display', 'none');

        }, 5000);
    }
    else if (resultado == '-1') {
        $('#contenedor_error').css('display', 'block');
        $('#contenedor_error').html('Se ha producido un error. vuelva a intentarlo');
    }
}
