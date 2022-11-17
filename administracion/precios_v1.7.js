$(document).ready(function () {
    //Carga el listado de convenios
    muestra_convenios();
});

function muestra_convenios() {
    $("#contenedor_error").css("display", "none"); //Oculta el mensaje de error en caso de estar visible

    //Limpia el input
    $("#txtParametro").val("");

    var params = "opcion=1&parametro=-5";
    llamarAjax("precios_ajax.php", params, "principal_convenios", "");
}

function seleccionar_convenio(idConvenio, idEcopetrol) {
    var params = "opcion=2&idConvenio=" + idConvenio + "&idEcopetrol=" + idEcopetrol;
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
        submitHandler: function () {
            var parametro = $("#txtParametro").val();
            var params = "opcion=1&parametro=" + parametro;
            llamarAjax("precios_ajax.php", params, "principal_convenios", "");

            return false;
        },
    });
}

//Limpia el combo box: 
function resetServicio() {
    $("#cmb_tipo_servicio").val("0");
    $("#resultado").html(""); //Elimina el contenido del vid con id: resultado    
}

function listasPrecios() {
    var parametro = $("#cmb_tipo_servicio").val();
    var idPlan = $("#cmb_plan").val();
    var accion = "1"; //Busca el total de los precios
    var txtBuscar = "";
    if (isObject(document.getElementById("txtParametroPrecios"))) {
        txtBuscar = $("#txtParametroPrecios").val();
    }

    if (idPlan == "") {
        alert("Seleccione el Plan");
    } else {
        var params = "opcion=3&parametro=" + parametro + "&idPlan=" + idPlan + "&accion=" + accion + "&txtBuscar=" + txtBuscar;
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
        submitHandler: function () {
            var parametro = $("#cmb_tipo_servicio").val();
            var idPlan = $("#cmb_plan").val();
            var accion = "2"; //Busca el precio segun el texto ingresado en la caja de texto
            var txtBuscar = $("#txtParametroPrecios").val();

            if (idPlan == "") {
                alert("Seleccione el Plan");
            } else {
                var params = "opcion=3&parametro=" + parametro + "&idPlan=" + idPlan + "&accion=" + accion + "&txtBuscar=" + txtBuscar;
                llamarAjax("precios_ajax.php", params, "resultado", "");
            }

            return false;
        },
    });
}

/**
 * Ver el historial de los precios de cada procedimiento  
 * @param {Object} tipo
 * @param {Object} cod_procedimiento
 * @param {Object} tipo_bilateral
 */
function ver_historia_precios(tipo, cod_procedimiento, tipo_bilateral) {

    $("#contenedor_error").remove(); //Elimina el div de error que esta detras del elemento flotante
    $("#fondo_negro_agregar_servicios").css("display", "block");
    $("#d_centro_agregar_servicios").slideDown(400).css("display", "block");

    //Asigna el alto por defecto a la página
    $("#d_interno_agregar_servicios").css("height", "470");

    var cmb_plan = $("#cmb_plan").val();
    var cmb_tipo_servicio = $("#cmb_tipo_servicio").val();
    var nombre_convenio = $("#hdd_nombre_convenio").val();

    var params = "opcion=8&cod_procedimiento=" + cod_procedimiento + "&id_plan=" + cmb_plan + "&tipo_servicio=" + cmb_tipo_servicio +
            "&nombre_convenio=" + nombre_convenio + "&tipo_bilateral=" + tipo_bilateral;
    llamarAjax("precios_ajax.php", params, "d_interno_agregar_servicios", "");
}

function abrir_agregar_precios(cod_procedimiento, tipo_bilateral) {
    $("#contenedor_error").remove(); //Elimina el div de error que esta detras del elemento flotante
    $("#fondo_negro_agregar_servicios").css("display", "block");
    $("#d_centro_agregar_servicios").slideDown(400).css("display", "block");

    //Asigna el alto por defecto a la página
    $("#d_interno_agregar_servicios").css("height", "470");

    //Envia por ajax la peticion para construir el formulario flotante
    var tipo_servicio = $("#cmb_tipo_servicio").val();
    var params = "opcion=4&tipoServicio=" + tipo_servicio + "&idPrecio=" + idPrecio;
    //llamarAjax("precios_ajax.php", params, "d_interno_agregar_servicios", "");
}

/**
 * Muestra la ventana flotante de Agregar Precio
 * La variable tipo se usa para identificar si se va a abrir la ventana o si se va a cerrar
 * @param {Object} tipo
 * 1=abrir ventana
 * 0=cerrar ventana 
 * Tambien se usa para identificar si se va a crear o a modificar un precio
 * @param {Object} tipo
 * 1=Modificar
 * 2=Crear
 * @param {Object} tipo
 * @param {Object} idPrecio
 */
function ventanaAgregarPrecio(tipo, idPrecio, iss) {
    if (tipo == 1 || tipo == 2) {//mostrar
        $("#contenedor_error").remove(); //Elimina el div de error que esta detras del elemento flotante
        $("#fondo_negro_agregar_servicios").css("display", "block");
        $("#d_centro_agregar_servicios").slideDown(400).css("display", "block");
		
        //Asigna el alto por defecto a la página
        $("#d_interno_agregar_servicios").css("height", "470");
		
        //Envia por ajax la peticion para construir el formulario flotante
		
        var tipo_servicio = $("#cmb_tipo_servicio").val();
		
        if (iss) {
            var idPlan = $("#cmb_plan").val();
            var params = "opcion=10&tipoServicio=" + tipo_servicio + "&idPrecio=" + idPrecio + "&tipo_accion=" + tipo + '&idPlan=' + idPlan;
			
            llamarAjax("precios_ajax.php", params, "d_interno_agregar_servicios", "");
        } else {
            var params = "opcion=4&tipoServicio=" + tipo_servicio + "&idPrecio=" + idPrecio + "&tipo_accion=" + tipo;
			
            llamarAjax("precios_ajax.php", params, "d_interno_agregar_servicios", "");
        }
    } else if (tipo == 0) {//Ocultar
        $("#d_centro_agregar_servicios").css("display", "none");
        $("#fondo_negro_agregar_servicios").css("display", "none");
		
        //Agrega de nuevo el campo error al formulario
        $("#advertenciasg").prepend('<div class="contenedor_error" id="contenedor_error"></div>');//Agreba el div de error al conteendor
	}
}

//Muestra la ventana flotante de Servicios
function ventanaServicios(tipo) {
    if (tipo == 1) {//mostrar
        $("#contenedor_error").remove(); //Elimina el div de error que esta detras del elemento flotante
        $("#fondo_negro_servicios").css("display", "block");
        $("#d_centro_servicios").slideDown(400).css("display", "block");

        //Asigna el alto por defecto a la página
        $("#d_interno_servicios").css({"min-height": "470px"});

        //Envia por ajax la peticion para construir el formulario flotante
        var tipo_servicio = $("#idServicio").val();
        var params = "opcion=5&tipoServicio=" + tipo_servicio;
        llamarAjax("precios_ajax.php", params, "d_interno_servicios", "");
    } else if (tipo == 0) {//Ocultar
        $("#d_centro_servicios").css("display", "none");
        $("#fondo_negro_servicios").css("display", "none");

        //Agrega de nuevo el campo error al formulario
        $("#advertenciasAgregarPrecio").prepend('<div class="contenedor_error" id="contenedor_error"></div>');//Agreba el div de error al conteendor
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
        submitHandler: function () {
            var codigoServicioSeleccionado = $("#hdd_idServicio").val();
            var idPrecio = $("#idPrecio").val();
			
            if (codigoServicioSeleccionado == "0" && idPrecio == "0") {
                alert("Seleccione el Servicio");
            } else if (codigoServicioSeleccionado != "") {
                //Validacion de Fechas seleccionadas
                var fechaInicial_aux = $("#fechaInicial").val();
                var fechaFinal_aux = $("#fechaFinal").val();
                var fechaFinal2_aux = "";
				
                if (fechaFinal_aux != "") {
                    fechaFinal_aux = fechaFinal_aux.split("/");
                    fechaFinal2_aux = fechaFinal_aux[2] + "-" + fechaFinal_aux[1] + "-" + fechaFinal_aux[0];
                } else {
                    fechaFinal_aux = fechaFinal_aux.split("/");
                }
				
                fechaInicial_aux = fechaInicial_aux.split("/");
				
                //Guarda la fechas en un objeto de tipo date
                var fechaInicial = new Date();
                var fechaFinal = new Date();
				
                fechaInicial.setFullYear(fechaInicial_aux[2], fechaInicial_aux[1], fechaInicial_aux[0]);
                fechaFinal.setFullYear(fechaFinal_aux[2], fechaFinal_aux[1], fechaFinal_aux[0]);
				
                //Si fecha Inicial es Mayor a Fecha Final
                if (fechaInicial > fechaFinal) {
                    alert("Fecha inicial no debe ser mayor a fecha final");
                } else {
                    var plan = $("#cmb_plan").val();
                    var tipoServicio = $("#idServicio").val();
                    var tipoBilateral = $("#cmb_tipo_bilateral").val();
                    var txtValorTotal = $("#txtValorTotal").val();
                    var txtValorCuota = $("#txtValorCuota").val();
					
                    var params = "opcion=7&codigoServicioSeleccionado=" + codigoServicioSeleccionado +
                            "&idPrecio=" + idPrecio +
                            "&fechaInicial=" + fechaInicial_aux[2] + "-" + fechaInicial_aux[1] + "-" + fechaInicial_aux[0] +
                            "&fechaFinal=" + fechaFinal2_aux +
                            "&tipoServicio=" + tipoServicio +
                            "&plan=" + plan +
                            "&tipoBilateral=" + tipoBilateral +
                            "&txtValorTotal=" + txtValorTotal +
                            "&txtValorCuota=" + txtValorCuota;
					
					llamarAjax("precios_ajax.php", params, "d_agregar_precio", "verificaGuardarPrecio();");
                }
            }
            return false;
        },
    });
}

/**
 *Para crear los precios desde la lista 
 */
function crear_precios() {
	if (validar_crear_precios() == 0) {
	    var codigoServicioSeleccionado = $("#hdd_idServicio").val();
        var idPrecio = $("#idPrecio").val();
        var plan = $("#cmb_plan").val();
        var tipoServicio = $("#idServicio").val();
        var tipoBilateral = $("#cmb_tipo_bilateral").val();
        var txtValorTotal = $("#txtValorTotal").val();
        var txtValorCuota = $("#txtValorCuota").val();
		
	    var var_fecha_final_anterior = $("#fecha_final_anterior").val();
        var fecha_final_anterior = $("#fecha_final_anterior").val().split("/");
        fecha_final_anterior = new Date(fecha_final_anterior[2], fecha_final_anterior[1], fecha_final_anterior[0]);
		
	    var var_fecha_inicial = $("#fechaInicial").val();
        var fecha_inicial = $("#fechaInicial").val().split("/");
        fecha_inicial = new Date(fecha_inicial[2], fecha_inicial[1], fecha_inicial[0]);
		
	    var var_fecha_final = $("#fechaFinal").val();
        var fecha_final = $("#fechaFinal").val().split("/");
        fecha_final = new Date(fecha_final[2], fecha_final[1], fecha_final[0]);
		
	    var var_fecha_hoy = $("#fecha_hoy").val();
        var fecha_hoy = $("#fecha_hoy").val().split("/");
        fecha_hoy = new Date(fecha_hoy[2], fecha_hoy[1], fecha_hoy[0]);
		
	    //Si fecha Inicial es Mayor a Fecha Final
        if (Date.parse(fecha_inicial) > Date.parse(fecha_final)) {
            alert("Fecha inicial no debe ser mayor a fecha final");
            return false;
        }
		//Si fecha inicial se cruza con el rango dela fecha del precio anterior
        if (Date.parse(fecha_inicial) < Date.parse(fecha_final_anterior)) {
            alert("La fecha inicial se cruza con el rango de fechas del precio anterior");
            return false;
        }
		
	    var params = "opcion=9&codigoServicioSeleccionado=" + codigoServicioSeleccionado +
					 "&idPrecio=" + idPrecio +
					 "&fechaInicial=" + var_fecha_inicial +
					 "&fechaFinal=" + var_fecha_final +
					 "&fechaFinalAnterior=" + var_fecha_final_anterior +
					 "&tipoServicio=" + tipoServicio +
					 "&plan=" + plan +
					 "&tipoBilateral=" + tipoBilateral +
					 "&txtValorTotal=" + txtValorTotal +
					 "&txtValorCuota=" + txtValorCuota;
		
		llamarAjax("precios_ajax.php", params, "advertenciasAgregarPrecio", "validar_exito_guardar();");
    } else {
        $("#contenedor_error").css("display", "block");
        $("#contenedor_error").html("Los campos marcados en rojo son obligatorios");
        return false;
    }
}

function validar_exito_guardar() {
    var hdd_exito = parseInt($("#hdd_exito").val(), 10);

    if (hdd_exito > 0) {
        $("#contenedor_exito_gurdar").css("display", "block");
        $("#contenedor_exito_gurdar").html("Datos guardados correctamente.");
        setTimeout('$("#fondo_negro_agregar_servicios").css("display", "none"); $("#d_centro_agregar_servicios").css("display", "none");', 1000);
        listasPrecios();
    } else if (hdd_exito == -1) {
        $("#contenedor_error_gurdar").css("display", "block");
        $("#contenedor_error_gurdar").html("Error interno al guardar los precios.");
    } else if (hdd_exito == -3) {
        $("#contenedor_error_gurdar").css("display", "block");
        $("#contenedor_error_gurdar").html("Error - Se encontr&oacute; un conflicto entre las fechas de los precios, es posible que exista un precio cuya fecha inicial corresponda a la fecha inicial del precio que se trata de ingresar.");
    } else {
        $("#contenedor_error_gurdar").css("display", "block");
        $("#contenedor_error_gurdar").html("Error al guardar los precios.");
    }
}

function validar_crear_precios() {
    var result = 0;
    $("#fechaInicial").removeClass("borde_error");
    $("#txtValorTotal").removeClass("borde_error");

    if ($("#fechaInicial").val() == "") {
        $("#fechaInicial").addClass("borde_error");
        result = 1;
    }
    if ($("#txtValorTotal").val() == "") {
        $("#txtValorTotal").addClass("borde_error");
        result = 1;
    }

    return result;
}

function verificaGuardarPrecio() {
    var resultado = $("#hdd_resul_agregar_precio").val();

    if (resultado == "-2") {
        $("#contenedor_error").css("display", "block");
        $("#contenedor_error").html("Error al guardar. Revise que la fecha del registro no este en conflicto con un registro ya creado");
    } else {
        ventanaAgregarPrecio(0, 0, false);
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
        submitHandler: function () {
            var parametro = $("#txtParametroServicio").val();
            var tipoServicio = $("#hdd_tipoServicio").val();
            var indEcopetrol = $("#hdd_ecopetrol").val(); //variable que trae del formulario principal
            var idPlan = $("#cmb_plan").val();

            var params = "opcion=6&parametro=" + parametro + "&tipoServicio=" + tipoServicio + "&indEcopetrol=" + indEcopetrol + "&idPlan=" + idPlan;
            llamarAjax("precios_ajax.php", params, "servicios", "");
            return false;
        },
    });
}

//Funcion que agrega el servicio seleccionado a la ventana flotante Agregar Precio
function seleccionar_servicio(idServicio, nombreServicio) {
    var codigoServicio = idServicio;

    //Asigna los valores 
    $("#txtNombreServicio").html(nombreServicio);
    $("#hdd_idServicio").val(codigoServicio);

    //Cierra la ventana flotante
    ventanaServicios(0);
}

function importarISS() {
    var txt;
    var r = confirm("¿Realmente desea realizar la impotación del manual ISS 2001?");
    if (r == true) {
        txt = "Sí";
    } else {
        txt = "No";
    }

    if (txt == "Sí") {
        var plan = $("#cmb_plan").val();


        var params = "opcion=11" +
                "&plan=" + plan;
        llamarAjax("precios_ajax.php", params, "divResultadoISS", "validarImportarISS2001()");
    }
}

function validarImportarISS2001() {
    var hdd_exito = parseInt($("#hdd_importarISS2001").val(), 10);
    if (hdd_exito > 0) {
        $("#contenedor_exito_gurdar").css("display", "block");
        $("#contenedor_exito_gurdar").html("La importación se ha ralizado de forma exitosa");
        setTimeout('$("#fondo_negro_agregar_servicios").css("display", "none"); $("#d_centro_agregar_servicios").css("display", "none");', 3000);
        listasPrecios();
    } else if (hdd_exito == -1) {
        $("#contenedor_error_gurdar").css("display", "block");
        $("#contenedor_error_gurdar").html("Error interno al realizar la importación ISS 2001.");
    }
}

function muestraImportarCsv() {
	var tipo_servicio = $("#cmb_tipo_servicio").val();
    var params = "opcion=12&tipo_servicio=" + tipo_servicio;
    llamarAjax("precios_ajax.php", params, "divImportarCsv", "");
}

function validarFrmPreciosCSV() {
    $("#frm_preciosCSV").validate({
        rules: {
            fileCSV: {
                required: true,
            }
        },
        submitHandler: function () {
            var params = "opcion=13";
            llamarAjaxUploadFiles("precios_ajax.php", params, "resultadoImportarCSV", "validaSubirArchivo()", "d_barra_progreso_adj", "fileCSV");
        },
    });
}

function validaSubirArchivo() {
    var file = $('#hddArchivo').val();
    var idPlan = $('#cmb_plan').val();
    var tipoServicio = $('#cmb_tipo_servicio').val();
	
	var porcentaje = $('#txt_porcentaje').val();
	var precioUvr =  $('#txt_precio_uvr').val();
	var check_porc = $('#chk_porcentaje_xsx').attr('checked');
	
	if(!check_porc && porcentaje == ""){
		
		swal({
			  title: "El porcentaje no puede ir vacío",
			  text: "",
			  type: "warning",
			  closeOnConfirm: false
		});
		
	}else if (file != -1) {
        var params = "opcion=14&file=" + file +
                "&idPlan=" + idPlan +
                "&tipoServicio=" + tipoServicio +
				 "&porcentaje=" + porcentaje +
				"&check_porc=" + check_porc +
				"&precioUvr=" + precioUvr;
        llamarAjax("precios_ajax.php", params, "resultadoImportarCSV", "validaImportarCSV()");
    } else {
        swal({
			  title: "Error",
			  text: "Error al subir el archivo",
			  type: "error",
			  closeOnConfirm: false
		});
    }
}

function validaImportarCSV() {
    var rta = $('#hddResultadoImportacionCSV').val();

    switch (rta) {
        case "-1":
           swal({
				  title: "Error",
				  text: "Error en la importación",
				  type: "error",
				  closeOnConfirm: false
			});
            break;
        case "-3":
           swal({
				  title: "Error",
				  text: "Error al leer el archivo",
				  type: "error",
				  closeOnConfirm: false
			});
            break;
        case "-4":
           swal({
				  title: "Error",
				  text: "El archivo está vacio",
				  type: "error",
				  closeOnConfirm: false
			});
		
            break;
        case "-5":
           swal({
				  title: "Error",
				  text: "Error de versión del archivo",
				  type: "error",
				  closeOnConfirm: false
			});
            break;        
        case "1":/*Realiza la actualizacion o creacion de pacientes*/
            var file = $('#hddRutaFile').val();
            var params = "opcion=16" +
                    "&file=" + file;
            llamarAjax("precios_ajax.php", params, "divImportarCsv", "alert(\"La importación de precios ha sido exitosa\");listasPrecios()");
            break;
        default:
            alert(rta);
            break;    
    }
}

function generarArchivoExcel(){
	
	 var id_plan = $('#cmb_plan').val();
	 var tipo_servicio = $('#cmb_tipo_servicio').val();
	 var params ="opcion=17" +
	 			 "&id_plan=" + id_plan +
				 "&tipo_servicio=" + tipo_servicio;
	 llamarAjax("precios_ajax.php", params, "resultadoImportarCSV", "");
	
}
function mostrarAlertEstado(checkbox){
	
	 if(checkbox.checked == true){
			
		swal({
            title: "El porcentaje será tomado del archivo .CSV",
            type: "warning",
            showCancelButton: false,
			showConfirmButton: true,
            confirmButtonText: "Aceptar",
        }); 
		}
}