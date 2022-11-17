$(document).ready(function() {
    //Carga el listado de convenios
    muestra_convenios();
});


function muestra_convenios() {
    $('#contenedor_error').css('display', 'none');//Oculta el mensaje de error en caso de estar visible
    $('#contenedor_exito').css('display', 'none');//Oculta el mensaje de exito en caso de estar visible

    //Limpia el input
    $('#txtParametro').val('');

    var params = 'opcion=1&parametro=-1';
    llamarAjax("tipos_citas_ajax.php", params, "reporte_citas", "");
}



//Funcion que busca un convenio especifico
function buscarTipoCita() {
    $("#frmBuscarTipoCita").validate({
        rules: {
            txtParametro: {
                required: true,
            },
        },
        submitHandler: function() {

            $('#contenedor_error').css('display', 'none');//Oculta el mensaje de error en caso de estar visible
            $('#contenedor_exito').css('display', 'none');//Oculta el mensaje de exito en caso de estar visible

            var parametro = $('#txtParametro').val();
            var params = 'opcion=1&parametro=' + str_encode(parametro);
            llamarAjax("tipos_citas_ajax.php", params, "reporte_citas", "");

            return false;
        },
    });
}

//Imprime el formulario para un nuevo tipo de cita
function nuevoTipoCita() {
    var params = 'opcion=2';
    llamarAjax("tipos_citas_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1);postNuevoTipoCita();");
}


function postNuevoTipoCita(){
    $('#cmb_tipos_registros_hc').attr('disabled', 'disabled');
}

//Funcion que guarda un nuevo tipo de cita
function guardarNuevoTipoCita() {
    //Validacion del formulario
    var parametro = $('#txtNombre').val()
    if (parametro == '') {
        alert('Error! Debe agregar el nombre del tipo de procedimiento');
    } else if (parametro != '') {

        var parametro = str_encode($('#txtNombre').val());
        var indPreq = $('#indPreq').is(':checked') ? 1 : 0;
        var indVitales = $('#indVitales').is(':checked') ? 1 : 0;
        var indActivo = $('#indActivo').is(':checked') ? 1 : 0;
        var indExamenes = $('#indExamenes').is(':checked') ? 1 : 0;
        var tiposRegistrosHc = '';
        var pasaporte = false;

        if ($('#indPreq').is(':checked')) {
            tiposRegistrosHc = $('#cmb_tipos_registros_hc').val();

            if (tiposRegistrosHc != '') {
                pasaporte = true;
            }
        } else {
            tiposRegistrosHc = '';
            pasaporte = true;
        }

        if (pasaporte) {
            var params = 'opcion=3&parametro=' + parametro + '&indPreq=' + indPreq + '&indVitales=' + indVitales + '&indActivo=' + indActivo + '&accion=0&idTipoCita=&indExamenes=' + indExamenes + '&tiposRegistrosHc=' + tiposRegistrosHc;
            llamarAjax("tipos_citas_ajax.php", params, "hdd_resultado", "postGuardaNuevoTipoCita();");
            
        } else {
            alert('Debe seleccionar el Tipo de registro prequirurgico');
        }
    }
}



//Funcion que guarda un nuevo tipo de cita
function modificaTipoCita() {
    //Validacion del formulario
    var parametro = $('#txtNombre').val()
    if (parametro == '') {
        alert('Error! Debe agregar el nombre del tipo de procedimiento');
    } else if (parametro != '') {

        var parametro = str_encode($('#txtNombre').val());
        var indPreq = $('#indPreq').is(':checked') ? 1 : 0;
        var indVitales = $('#indVitales').is(':checked') ? 1 : 0;
        var indActivo = $('#indActivo').is(':checked') ? 1 : 0;
        var idTipoCita = $('#hdd_idTipoCita').val();
        var indExamenes = $('#indExamenes').is(':checked') ? 1 : 0;
        var tiposRegistrosHc = '';
        var pasaporte = false;

        if ($('#indPreq').is(':checked')) {
            tiposRegistrosHc = $('#cmb_tipos_registros_hc').val();

            if (tiposRegistrosHc != '') {
                pasaporte = true;
            }
        } else {
            tiposRegistrosHc = '';
            pasaporte = true;
        }

        if (pasaporte) {
            var params = 'opcion=3&parametro=' + parametro + '&indPreq=' + indPreq + '&indVitales=' + indVitales + '&indActivo=' + indActivo + '&accion=1&idTipoCita=' + idTipoCita + '&indExamenes=' + indExamenes + '&tiposRegistrosHc=' + tiposRegistrosHc;
            llamarAjax("tipos_citas_ajax.php", params, "hdd_resultado", "postModificaTipoCita();");
        } else {
            alert('Debe seleccionar el Tipo de registro prequirurgico');
        }

    }
}


//Funcion que verifica si se ha guardado el registro 
function postGuardaNuevoTipoCita() {
    var resultado = $('#hdd_resultado').text();

    if (resultado == '1') {//cierra la ventana flotante
        mostrar_formulario_flotante(0);
        muestra_convenios();
        $('#contenedor_exito').css({'display': 'block'});
        $('#contenedor_exito').html('El registro ha sido guardado.');
    } else if (resultado == '1') {
        alert('Error al guardar!. Intentelo de nuevo');
    }
}


//Funcion que verifica si se ha guardado el registro 
function postModificaTipoCita() {
    var resultado = $('#hdd_resultado').text();

    if (resultado == '1') {//cierra la ventana flotante
        $('#contenedor_exito').css({'display': 'block'});
        $('#contenedor_exito').html('El registro ha sido guardado.');
    } else if (resultado == '1') {
        alert('Error al guardar!. Intentelo de nuevo');
    }
}

function tiposCitaDetalle(idTipoCita, idTipoRegistro) {
    var params = 'opcion=5&idTipoCita=' + idTipoCita + '&idTipoRegistro=' + idTipoRegistro;
    llamarAjax("tipos_citas_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1);verificaIconoBorrarProcedimiento();");
}


//Funcion que verirfica si muestra el icono para Borrar el procedimiento
function verificaIconoBorrarProcedimiento() {
    var procedimiento = $('#hdd_Procedimiento').val();
    if (procedimiento == '') {
        ocultaBtnEliminarProcedimiento();
    }
}

function tiposCitaDetalleNuevo(idTipoCita) {
    var params = 'opcion=11&idTipoCita=' + idTipoCita;
    llamarAjax("tipos_citas_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1);");
}

//Muestra la venata flotante para seleccionar un procedimiento
function seleccionarProcedimiento(tipo) {
    if (tipo == 1) {//mostrar
        $('#fondo_negro_procedimientos').css('display', 'block');
        $('#d_centro_procedimientos').slideDown(400).css({'display': 'block', 'z-index': '10'});

        //Asigna el alto por defecto a la página
        $('#d_interno_procedimientos').css({'min-height': '470px'});

        //Envia por ajax la peticion para construir el formulario flotante
        var params = 'opcion=6';
        llamarAjax("tipos_citas_ajax.php", params, "d_interno_procedimientos", "");
    }
    else if (tipo == 0) {//Ocultar
        $("#d_centro_procedimientos").css("display", "none");
        $("#fondo_negro_procedimientos").css("display", "none");

    }
}

//Funcion que muestra el listado de procedimientos
function buscarProcedimiento() {
    $("#frmBuscarProcedimiento").validate({
        rules: {
            txtParametro: {
                required: true,
            },
        },
        submitHandler: function() {
            var parametro = $('#txtProcedimiento').val();
            var params = 'opcion=7&parametro=' + str_encode(parametro);
            llamarAjax("tipos_citas_ajax.php", params, "procedimientos", "muestraBtnEliminarProcedimiento();");

            return false;
        },
    });
}



function ocultaBtnEliminarProcedimiento() {
    $('#btnEliminarProcedimiento').css({'display': 'none'});
}

function muestraBtnEliminarProcedimiento() {
    $('#btnEliminarProcedimiento').css({'display': 'block'});
}

//Muestra la venata flotante para seleccionar un procedimiento
function seleccionarEstadoAtencion(tipo) {
    if (tipo == 1) {//mostrar
        $('#fondo_negro_procedimientos').css('display', 'block');
        $('#d_centro_procedimientos').slideDown(400).css({'display': 'block', 'z-index': '10'});

        //Asigna el alto por defecto a la página
        $('#d_interno_procedimientos').css({'min-height': '470px'});

        //Envia por ajax la peticion para construir el formulario flotante
        var params = 'opcion=8';
        llamarAjax("tipos_citas_ajax.php", params, "d_interno_procedimientos", "");
    }
    else if (tipo == 0) {//Ocultar
        $("#d_centro_procedimientos").css("display", "none");
        $("#fondo_negro_procedimientos").css("display", "none");

    }
}

//Muestra la venata flotante para seleccionar un procedimiento
function seleccionarTipoRegistro(tipo) {
    if (tipo == 1) {//mostrar
        $('#fondo_negro_procedimientos').css('display', 'block');
        $('#d_centro_procedimientos').slideDown(400).css({'display': 'block', 'z-index': '10'});

        //Asigna el alto por defecto a la página
        $('#d_interno_procedimientos').css({'min-height': '470px'});

        //Envia por ajax la peticion para construir el formulario flotante
        var params = 'opcion=9';
        llamarAjax("tipos_citas_ajax.php", params, "d_interno_procedimientos", "");
    }
    else if (tipo == 0) {//Ocultar
        $("#d_centro_procedimientos").css("display", "none");
        $("#fondo_negro_procedimientos").css("display", "none");
    }
}

//Funcion que guarda el tipo de cita detalle 
function guardarTipoCitaDetalle(idCita, idDetalle) {

    var orden = $('#txtOrden').val();
    var estadoAtencion = $('#hdd_EstadoAtencion').val();
    var procedimiento = $('#hdd_Procedimiento').val();
    var cmb_usuarioAlt = $('#cmb_usuarioAlt').val();

    //Validacion
    if (orden == '') {
        alert('Debe ingresar un valor en el campo: Orden');
    } else if (orden == '0') {//No permite inrgesar el numero 0 en el campo orden
        alert('El valor 0 no es permitido en el campo: Orden');
    } else if (cmb_usuarioAlt == '') {
        alert('Error!. Seleccione el campo: Atiende otro usuario profesional.');
    } else {
        //Recorre la tabla de Tipos de cita detalle.
        var filas = $('#tablaTiposCitaDetalles tr').length;
        var pasaporte = false;//variable que define si se puede o no guardar el objeto

        //Verifica si es posible agregar el elemento seleccionado
        for (i = 2; i <= (filas - 1); i++) {//Inicia en 2 la variable i porque es necesario quitar los 2 tr de informacion del principio de la tabla
            //Valida si puede guardar o no el numero de orden con base en los que ya estan guardados
            if ($('#hdd_TipoRegistro').val() == $('#tablaTiposCitaDetalles tr:eq(' + i + ')').attr('id')) {
                //Valida que el Orden se pueda agregar
                if ($('#txtOrden').val() == $.trim($('#tablaTiposCitaDetalles tr:eq(' + i + ') #orden').html())) {
                    pasaporte = true;
                    break;
                } else {
                    pasaporte = true;
                }
            } else if ($('#hdd_TipoRegistro').val() != $('#tablaTiposCitaDetalles tr:eq(' + i + ')').attr('id')) {
                //Valida que el Orden se pueda agregar
                if ($('#txtOrden').val() == $.trim($('#tablaTiposCitaDetalles tr:eq(' + i + ') #orden').html())) {
                    alert('Debe seleccionar otro numero de orden');
                    pasaporte = false;
                    break;
                } else {
                    pasaporte = true;
                }
            }
        }

        //Verifica si puede guardar
        if (pasaporte) {
            var params = 'opcion=10&idCita=' + idCita + '&idDetalle=' + idDetalle + '&accion=' + 3 + '&estadoAtencion=' + estadoAtencion + '&procedimiento=' + procedimiento + '&orden=' + str_encode(orden) + '&usuarioAlt=' + cmb_usuarioAlt;
            llamarAjax("tipos_citas_ajax.php", params, "hdd_resultado_agregar", "postEliminarTipoCitaDetalle(" + idCita + ")");//postEliminarTipoCitaDetalle(" + idCita + ")


        }
    }
}

//Funcion que guarda el tipo de cita detalle 
function nuevoTipoCitaDetalle(idCita) {
    var orden = $('#txtOrden').val();
    var idDetalle = $('#hdd_TipoRegistro').val();
    var estadoAtencion = $('#hdd_EstadoAtencion').val();
    var procedimiento = $('#hdd_Procedimiento').val();
    var cmb_usuarioAlt = $('#cmb_usuarioAlt').val();

    //Validacion
    if (orden == '') {
        alert('Debe ingresar un valor en el campo: Orden');
    } else if (orden == '0') {//No permite inrgesar el numero 0 en el campo orden
        alert('El valor 0 no es permitido en el campo: Orden');
    } else if (estadoAtencion == '') {
        alert('Debe seleccionar el Estado de atencion');
    } else if (idDetalle == '') {
        alert('Debe seleccionar el Tipo de registro');
    } else if (cmb_usuarioAlt == '') {
        alert('Error!. Seleccione el campo: Atiende otro usuario profesional.');
    } else {
        //Recorre la tabla de Tipos de cita detalle.
        var filas = $('#tablaTiposCitaDetalles tr').length;
        var pasaporte = false;//variable que define si se puede o no guardar el objeto

        //Verifica si es posible agregar el elemento seleccionado
        for (i = 2; i <= (filas - 1); i++) {//Inicia en 2 la variable i porque es necesario quitar los 2 tr de informacion del principio de la tabla
            //Valida si puede guardar o no el numero de orden con base en los que ya estan guardados
            if ($('#hdd_TipoRegistro').val() == $('#tablaTiposCitaDetalles tr:eq(' + i + ')').attr('id')) {
                alert('Error! El tipo de registro ya existe');
                break;
            } else if ($('#hdd_TipoRegistro').val() != $('#tablaTiposCitaDetalles tr:eq(' + i + ')').attr('id')) {
                //Valida que el Orden se pueda agregar
                if ($('#txtOrden').val() == $.trim($('#tablaTiposCitaDetalles tr:eq(' + i + ') #orden').html())) {
                    alert('Debe seleccionar otro numero de orden');
                    pasaporte = false;
                    break;
                } else {
                    pasaporte = true;
                }
            }
        }

        //Verifica si puede guardar
        if (pasaporte) {
            var params = 'opcion=10&idCita=' + idCita + '&idDetalle=' + idDetalle + '&accion=' + 1 + '&estadoAtencion=' + estadoAtencion + '&procedimiento=' + procedimiento + '&orden=' + str_encode(orden) + '&usuarioAlt=' + cmb_usuarioAlt;
            llamarAjax("tipos_citas_ajax.php", params, "hdd_resultado_agregar", "postEliminarTipoCitaDetalle(" + idCita + ")");//postEliminarTipoCitaDetalle(" + idCita + ")
        }
    }

}

//Agrega un tipo de registro HC
function agregarTipoRegistroHC(id, nombre) {
    //Agrega los valores
    $('#txtTipoRegistro').text(nombre);
    $('#hdd_TipoRegistro').val(id);
    seleccionarProcedimiento(0);//Cierra el div flotante
}

//Advertencia para elimianr objeto
function eliminarTipoCitaDetalle(idCita, idDetalle) {
    if (confirm('Desea eliminar el elemento?')) {
        var params = 'opcion=10&idCita=' + idCita + '&idDetalle=' + idDetalle + '&accion=' + 2 + '&estadoAtencion=&procedimiento=&orden=&usuarioAlt=';
        llamarAjax("tipos_citas_ajax.php", params, "hdd_resultado_agregar", "postEliminarTipoCitaDetalle(" + idCita + ")");
    } else {

    }
}

//Funcion que verifica si el objeto ha sido eliminado de forma exitosa
function postEliminarTipoCitaDetalle(idCita) {
    if ($('#hdd_resultado_agregar').text() == '1') {
        cerrar_div_centro();//Oculta el div flotante
        muestraTipoCita(idCita);
    } else if ($('#hdd_resultado_agregar').text() == '-1') {
        alert('Error interno del servidor. Intentar de nuevo');
    }

}

function muestraTipoCita(id) {

    $('#contenedor_error').css('display', 'none');//Oculta el mensaje de error en caso de estar visible
    $('#contenedor_exito').css('display', 'none');//Oculta el mensaje de exito en caso de estar visible

    var params = 'opcion=4&idTipoCita=' + id;
    llamarAjax("tipos_citas_ajax.php", params, "reporte_citas", "postMuestraTipoCita();");
}

function postMuestraTipoCita(){
    
    if ($('#indPreq').is(':checked')) {
        //Muestra el combo box
        $('#cmb_tipos_registros_hc').attr('disabled', false); 
    } else {
        //oculta el combo box       
        $('#cmb_tipos_registros_hc').attr('disabled', 'disabled'); 
    }
    
}

//Agrega los Estados de Atencion
function agregarEstadosAtencion(idEstadoAtencion, nombreEstado) {
    //Agrega los valores
    $('#txtEstadoAtencion').text(nombreEstado);
    $('#hdd_EstadoAtencion').val(idEstadoAtencion);
    seleccionarProcedimiento(0);//Cierra el div flotante
}

//Agrega los Procedimientos
function agregarProcedimiento(idProcedimiento, nombreProcedimiento) {
    //Agrega los valores
    $('#txtProcedimientos').text(nombreProcedimiento);
    $('#hdd_Procedimiento').val(idProcedimiento);
    seleccionarProcedimiento(0);//Cierra el div flotante
}

//Funcion del boton eliminar para el formulario flotante: Detalle cita
function eliminarProcedimiento() {
    if (confirm('Desea eliminar el procedimiento?')) {
        $('#txtProcedimientos').text('');
        $('#hdd_Procedimiento').val('');

        ocultaBtnEliminarProcedimiento();

    }
}

function indicadorPre() {
    //comprueba si el checkbox fue seleccionado
    if ($('#indPreq').is(':checked')) {
        //Muestra el combo box
        $('#cmb_tipos_registros_hc').attr('disabled', false); 
    } else {
        //oculta el combo box       
        $('#cmb_tipos_registros_hc').attr('disabled', 'disabled'); 
    }
}


