$(document).ready(function() {
    //Carga el listado de convenios
    muestra_convenios();
});

function muestra_convenios() {
    $('#contenedor_error').css('display', 'none');//Oculta el mensaje de error en caso de estar visible

    //Limpia el input
    $('#txtParametro').val('');

    var params = 'opcion=1&parametro=-5';
    llamarAjax("precios_ajax.php", params, "principal_convenios", "");
}

function seleccionar_convenio(idConvenio, idEcopetrol) {
    var params = 'opcion=2&idConvenio=' + idConvenio + '&idEcopetrol=' + idEcopetrol;
    llamarAjax("precios_ajax.php", params, "principal_convenios", "");
}

//Funcion que busca un convenio especifico
function buscarConvenio() {
    $("#frmBuscarConvenio").validate({
        rules: {
            txtParametro: {
                required: true,
            },
        },
        submitHandler: function() {
            var parametro = $('#txtParametro').val();
            var params = 'opcion=1&parametro=' + parametro;
            llamarAjax("precios_ajax.php", params, "principal_convenios", "");

            return false;
        },
    });
}

//Limpia el combo box: 
function resetServicio() {
    $('#cmb_tipo_servicio').val('0');
    $('#resultado').html('');//Elimina el contenido del vid con id: resultado    
}

function listasPrecios() {
    var parametro = $('#cmb_tipo_servicio').val();
    var idPlan = $('#cmb_plan').val();
    var accion = '1';//Busca el total de los precios
    var txtBuscar = $('#txtParametroPrecios').val();

    if (idPlan == '') {
        alert('Seleccione el Plan');
    } else {
        var params = 'opcion=3&parametro=' + parametro + '&idPlan=' + idPlan + '&accion=' + accion + '&txtBuscar=' + txtBuscar;
        llamarAjax("precios_ajax.php", params, "resultado", "");
    }
}

//Funcion que busca un convenio especifico
function buscarPrecio() {
    $("#frmListadoPrecios").validate({
        rules: {
            txtParametroPrecios: {
                required: true,
            },
        },
        submitHandler: function() {
            var parametro = $('#cmb_tipo_servicio').val();
            var idPlan = $('#cmb_plan').val();
            var accion = '2';//Busca el precio segun el texto ingresado en la caja de texto
            var txtBuscar = $('#txtParametroPrecios').val();

            if (idPlan == '') {
                alert('Seleccione el Plan');
            } else {
                var params = 'opcion=3&parametro=' + parametro + '&idPlan=' + idPlan + '&accion=' + accion + '&txtBuscar=' + txtBuscar;
                llamarAjax("precios_ajax.php", params, "resultado", "");
            }

            return false;
        },
    });
}

//Muestra la ventana flotante de Agregar Precio
function ventanaAgregarPrecio(tipo, idPrecio) {
    if (tipo == 1) {//mostrar
        $('#contenedor_error').remove();//Elimina el div de error que esta detras del elemento flotante
        $('#fondo_negro_agregar_servicios').css('display', 'block');
        $('#d_centro_agregar_servicios').slideDown(400).css('display', 'block');

        //Asigna el alto por defecto a la página
        $('#d_interno_agregar_servicios').css('height', '470');

        //Envia por ajax la peticion para construir el formulario flotante
        var tipo_servicio = $('#cmb_tipo_servicio').val();
        var params = 'opcion=4&tipoServicio=' + tipo_servicio + '&idPrecio=' + idPrecio;
        llamarAjax("precios_ajax.php", params, "d_interno_agregar_servicios", "");
    } else if (tipo == 0) {//Ocultar
        $("#d_centro_agregar_servicios").css("display", "none");
        $("#fondo_negro_agregar_servicios").css("display", "none");

        //Agrega de nuevo el campo error al formulario
        $('#advertenciasg').prepend('<div class="contenedor_error" id="contenedor_error"></div>');//Agreba el div de error al conteendor
    }
}

//Muestra la ventana flotante de Servicios
function ventanaServicios(tipo) {
    if (tipo == 1) {//mostrar
        $('#contenedor_error').remove();//Elimina el div de error que esta detras del elemento flotante
        $('#fondo_negro_servicios').css('display', 'block');
        $('#d_centro_servicios').slideDown(400).css('display', 'block');

        //Asigna el alto por defecto a la página
        $('#d_interno_servicios').css({'min-height': '470px'});

        //Envia por ajax la peticion para construir el formulario flotante
        var tipo_servicio = $('#idServicio').val();
        var params = 'opcion=5&tipoServicio=' + tipo_servicio;
        llamarAjax("precios_ajax.php", params, "d_interno_servicios", "");
    } else if (tipo == 0) {//Ocultar
        $("#d_centro_servicios").css("display", "none");
        $("#fondo_negro_servicios").css("display", "none");

        //Agrega de nuevo el campo error al formulario
        $('#advertenciasAgregarPrecio').prepend('<div class="contenedor_error" id="contenedor_error"></div>');//Agreba el div de error al conteendor
    }
}

//Funcion que busca un convenio especifico
function AgregarPrecio() {
    $("#frmAgregarPrecio").validate({
        rules: {
            fechaInicial: {
                required: true,
            },
            txtValorTotal: {
                required: true,
                number: true
            },
            txtValorCuota: {
                required: true,
                number: true
            },
        },
        submitHandler: function() {
            var codigoServicioSeleccionado = $('#hdd_idServicio').val();
            var idPrecio = $('#idPrecio').val();
			
            if (codigoServicioSeleccionado == '0' && idPrecio == '0') {
                alert('Seleccione el Servicio');
            } else if (codigoServicioSeleccionado != '') {
                //Validacion de Fechas seleccionadas
                var fechaInicial_aux = $('#fechaInicial').val();
                var fechaFinal_aux = $('#fechaFinal').val();
                var fechaFinal2_aux = '';
				
				if (fechaFinal_aux != '') {
                    fechaFinal_aux = fechaFinal_aux.split('/');
                    fechaFinal2_aux = fechaFinal_aux[2] + '-' + fechaFinal_aux[1] + '-' + fechaFinal_aux[0];
                } else {
                    fechaFinal_aux = fechaFinal_aux.split('/');
                }
				
                fechaInicial_aux = fechaInicial_aux.split('/');
				
                //Guarda la fechas en un objeto de tipo date
                var fechaInicial = new Date();
                var fechaFinal = new Date();

                fechaInicial.setFullYear(fechaInicial_aux[2], fechaInicial_aux[1], fechaInicial_aux[0]);
                fechaFinal.setFullYear(fechaFinal_aux[2], fechaFinal_aux[1], fechaFinal_aux[0]);

                //Si fecha Inicial es Mayor a Fecha Final
                if (fechaInicial > fechaFinal) {
                    alert('Fecha inicial no debe ser mayor a fecha final');
                } else {
                    var plan = $('#cmb_plan').val();
                    var tipoServicio = $('#idServicio').val();
					var tipoBilateral = $('#cmb_tipo_bilateral').val();
                    var txtValorTotal = $('#txtValorTotal').val();
                    var txtValorCuota = $('#txtValorCuota').val();
					
                    var params = 'opcion=7&codigoServicioSeleccionado=' + codigoServicioSeleccionado +
								 '&idPrecio=' + idPrecio +
								 '&fechaInicial=' + fechaInicial_aux[2] + '-' + fechaInicial_aux[1] + '-' + fechaInicial_aux[0] +
								 '&fechaFinal=' + fechaFinal2_aux +
								 '&tipoServicio=' + tipoServicio +
								 '&plan=' + plan +
								 '&tipoBilateral=' + tipoBilateral +
								 '&txtValorTotal=' + txtValorTotal +
								 '&txtValorCuota=' + txtValorCuota;
					
                    llamarAjax("precios_ajax.php", params, "rtaAgregarPrecio", "verificaGuardarPrecio();");
                }
            }
            return false;
        },
    });
}

function verificaGuardarPrecio() {
    var resultado = $('#rtaAgregarPrecio').text();

    if (resultado == '-2') {
        $('#contenedor_error').css('display', 'block');
        $('#contenedor_error').html('Error al guardar. Revise que la fecha del registro no este en conflicto con un registro ya creado');
    } else {
        ventanaAgregarPrecio(0, 0);
        listasPrecios();
    }
}

//Funcion que busca el servicio
function buscarServicio() {
    $("#frmBuscarServicioFlotante").validate({
        rules: {
            txtParametroServicio: {
                required: true,
            }
        },
        submitHandler: function() {
            var parametro = $('#txtParametroServicio').val();
            var tipoServicio = $('#hdd_tipoServicio').val();
            var indEcopetrol = $('#hdd_ecopetrol').val();//variable que trae del formulario principal

            var params = 'opcion=6&parametro=' + parametro + '&tipoServicio=' + tipoServicio + '&indEcopetrol=' + indEcopetrol;
            llamarAjax("precios_ajax.php", params, "servicios", "");
            return false;
        },
    });
}

//Funcion que agrega el servicio seleccionado a la ventana flotante Agregar Precio
function seleccionar_servicio(idServicio, nombreServicio) {
    var codigoServicio = idServicio;

    //Asigna los valores 
    $('#txtNombreServicio').html(nombreServicio);
    $('#hdd_idServicio').val(codigoServicio);

    //Cierra la ventana flotante
    ventanaServicios(0);
}
