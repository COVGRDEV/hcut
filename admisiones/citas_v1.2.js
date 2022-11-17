$(document).ready(function() {
    inicio();
    setInterval('inicio()', 10000);//Ejecuta la peticion ajax cada 30 segundos
});

function inicio() {

    if ($('#txtParametro').val() == '') {//Si el input de busqueda esta vacio
        var params = 'opcion=1';
        llamarAjax("citas_ajax.php", params, "contenedor", "");//Al finalizar el llamado ajax ejecuta la funcion contenedor() que asigna la propiedad css: alto -> auto. Para que los 16px asignados al inicio se eliminen
    }
    if ($('#txtParametro').val() != '') {
        var parametro = $('#txtParametro').val();
        var params = 'opcion=4&parametro=' + str_encode(parametro);
        llamarAjax("citas_ajax.php", params, "contenedor", "");
    }
}

function verTodos() {

    $('#txtParametro').val('');


    var params = 'opcion=1';
    llamarAjax("citas_ajax.php", params, "contenedor", "");//Al finalizar el llamado ajax ejecuta la funcion contenedor() que asigna la propiedad css: alto -> auto. Para que los 16px asignados al inicio se eliminen

}


/*Despliega el formulario flotante*/
function detalle_cita(id) {

    var idCita = id;
    var params = 'opcion=2&idCita=' + idCita;
    llamarAjax("citas_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1);");
}

function cambiarEstado(idEstado) {
    var idCita = $('#idCita2').val();
    var params = 'opcion=3&idCita=' + idCita + '&idEstado=' + idEstado;
    llamarAjax("citas_ajax.php", params, "d_loader", "postCambiarEstado()");
}

//Funcion que cierra el div flotante y 
function postCambiarEstado() {
    var resultado = $('#d_loader').html();
    if (resultado == '1') {

        $('#dContenedorInterno').html('<div class="citasAsignarEstado"></div><h5>El registro ha sido guardado con exito.</h5>');

        setTimeout(function() {
            $('#dContenedorInterno').remove();
            inicio();
            mostrar_formulario_flotante(0);
        }, 300);
    } else {
        $('#d_loader').css('width', '100%');
        $('#d_loader').html('<p style="font-size: 10pt;color: #FF0000;">Hubo un error, vuelve a intentarlo.</p>');
    }
}


function buscarCitas() {
    $("#frmBuscarCita").validate({
        rules: {
            txtParametro: {
                required: true,
            },
        },
        submitHandler: function() {

            var parametro = $('#txtParametro').val();
            var params = 'opcion=4&parametro=' + str_encode(parametro);
            llamarAjax("citas_ajax.php", params, "contenedor", "");
            return false;
        },
    });
}