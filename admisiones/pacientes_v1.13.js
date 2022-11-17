function buscar_paciente() {
    $("#contenedor_error").css("display", "none");
    $("#txt_paciente_hc").removeClass("borde_error");

    if ($("#txt_paciente_hc").val() != "") {
        var params = "opcion=1&texto_busqueda=" + str_encode($("#txt_paciente_hc").val());

        llamarAjax("pacientes_ajax.php", params, "d_contenedor_paciente_hc", "");
    } else {
        $("#txt_paciente_hc").addClass("borde_error");
        $("#contenedor_error").css("display", "block");
        $("#contenedor_error").html("Los campos marcados en rojo son obligatorios");
        window.scroll(0, 0);
    }
}

function ver_datos_paciente(id_paciente) {
    var params = "opcion=2&id_paciente=" + id_paciente;

    llamarAjax("pacientes_ajax.php", params, "d_contenedor_paciente_hc", "");
}


function validar_paciente(tipo) {
    $("#frm_paciente").validate({
        rules: {
            cmb_tipo_documento: {
                required: true,
            },
            txt_numero_documento: {
                required: true,
                alphanumeric: true
            },
            txt_nombre_1: {
                required: true,
            },
            txt_apellido_1: {
                required: true,
            },
            cmb_sexo: {
                required: true,
            },
            txt_fecha_nacimiento: {
                required: true,
            },
            cmb_pais_res: {
                required: true,
            },
            cmb_cod_dep_res: {
                required: true,
            },
            cmb_cod_mun_res: {
                required: true,
            },
            cmb_convenio: {
                required: true,
            },
            cmb_plan: {
                required: true,
            },
            cmb_estatus_convenio: {
                required: true,
            },
            cmb_exento_convenio: {
                required: true,
            },
            cmb_rango: {
                required: true,
            },
            cmb_tipoUsuario: {
                required: true,
            },
            txt_email: {
                required: true,
            }
        },
        messages: {
            txt_numero_documento: {
                alphanumeric: "Solo se permite numeros y letras"
            },
            txt_numero_telefono: {
                alphanumeric: "Solo se permite numeros y letras"
            }

        },
        invalidHandler: function (form, validator) {
            alert_basico("", "Los campos en color rojo son obligatorios", "error");
        },
        submitHandler: function () {
            //Valida email
            var email_aux = validar_email($("#txt_email").val());
            //Valida campos condicionales requeridos
            var bandera_continuar = false;
            if ($("#txt_nom_dep_res").is(":visible") && $("#txt_nom_mun_res").is(":visible")) {

                if ($("#txt_nom_dep_res").val().length > 0 && $("#txt_nom_mun_res").val().length > 0) {
                    bandera_continuar = true;
                } else {
                    $("#txt_nom_dep_res").addClass("bordeAdmision")
                    $("#txt_nom_mun_res").addClass("bordeAdmision")
                    alert_basico("", "Los campos en color rojo son obligatorios", "error");
                }
            } else {
                bandera_continuar = true;
            }

            if (bandera_continuar) {
                if (email_aux) {
                    swal({//Alert de confirmación
                        title: "&iquest;Est&aacute; seguro que desea registrar los datos del paciente?",
                        type: "question",
                        text: "",
                        html: "",
                        showCancelButton: true,
                        confirmButtonText: 'S&iacute!',
                        cancelButtonText: 'No!',
                        backdrop: ""
                    }).then((resultado) => {
                        if (resultado.value) {//Evento aceptar         
                            guardar_paciente(tipo);
                        }
                    });
                } else {
                    alert_basico("", 'Si el paciente no tiene email debe escribir "No tiene"', "error");
                }
            }
        },
    });
}

function guardar_paciente(tipo) {
    //Valida mensajes de error
    //cerrar_div_centro();
    $("#contenedor_error").css("display", "none");

    var cod_dep = "";
    var cod_mun = "";
    var nom_dep_res = "";
    var nom_mun_res = "";

    //valida campos campos condicionales
    if ($("#cmb_cod_dep_res").is(":visible")) {
        cod_dep = $("#cmb_cod_dep_res").val();
    }

    if ($("#cmb_cod_mun_res").is(":visible")) {
        cod_mun = $("#cmb_cod_mun_res").val();
    }

    if ($("#txt_nom_dep_res").is(":visible")) {
        nom_dep_res = $("#txt_nom_dep_res").val();
    }

    if ($("#txt_nom_mun_res").is(":visible")) {
        nom_mun_res = $("#txt_nom_mun_res").val();
    }

    var id_paciente = $("#hdd_id_paciente").val();
    var params = "opcion=3&id_paciente=" + id_paciente +
            "&id_tipo_documento=" + $("#cmb_tipo_documento").val() +
            "&numero_documento=" + $("#txt_numero_documento").val() +
            "&nombre_1=" + str_encode($("#txt_nombre_1").val()) +
            "&nombre_2=" + str_encode($("#txt_nombre_2").val()) +
            "&apellido_1=" + str_encode($("#txt_apellido_1").val()) +
            "&apellido_2=" + str_encode($("#txt_apellido_2").val()) +
            "&sexo=" + $("#cmb_sexo").val() +
            "&fecha_nacimiento=" + $("#txt_fecha_nacimiento").val() +
            "&id_pais=" + $("#cmb_pais_res").val() +
            "&cod_dep=" + cod_dep +
            "&cod_mun=" + cod_mun +
            "&direccion=" + str_encode($("#txt_direccion").val()) +
            "&email=" + str_encode($("#txt_email").val()) +
            "&telefono_1=" + str_encode($("#txt_telefono_1").val()) +
            "&observ_paciente=" + str_encode($("#txt_observ_paciente").val()) +
            "&convenio=" + $("#cmb_convenio").val() +
            "&estado_convenio=" + $("#cmb_estatus_convenio").val() +
            "&exento=" + $("#cmb_exento_convenio").val() +
            "&rango=" + $("#cmb_rango").val() +
            "&tipoUsuario=" + $("#cmb_tipoUsuario").val() +
            "&nom_dep_res=" + nom_dep_res +
            "&nom_mun_res=" + nom_mun_res +
            "&tipo_accion=" + tipo +
            "&id_plan=" + $("#cmb_plan").val();

    $("#d_btn_guardar_paciente").css("display", "none");
    $("#d_esperar_guardar_paciente").css("display", "block");

    llamarAjax("../admisiones/pacientes_ajax.php", params, "d_guardar_paciente", "finalizar_guardar_paciente();");


}

function finalizar_guardar_paciente() {
    $("#d_btn_guardar_paciente").css("display", "block");
    $("#d_esperar_unificar_hc").css("display", "none");

    var resultado = parseInt($("#hdd_resultado_guardar_paciente").val(), 10);
	if(resultado>0){
		 var ind_ventana_flotante = $("#hdd_ind_ventana_flotante").val();
            $("#contenedor_exito").css("display", "block");
            $("#contenedor_exito").html("Paciente guardado con &eacute;xito");
            alert_basico("", "Paciente guardado con éxito", "success", true);
            if (ind_ventana_flotante == 0) {//Proceso de post-guardado desde admisiones/pacientes
                $("#d_contenedor_paciente_hc").html("");
                setTimeout(function () {
                    $("#contenedor_exito").css("display", "none");
                }, 2000);
                window.scroll(0, 0);
            } else {//Proceso de post-guardado desde admisiones/pacientes
                mostrar_formulario_flotante(0);
            }
	}else if(resultado == -1){
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error interno al guardar los datos del paciente");
		alert_basico("-1", 'Error interno al guardar los datos del paciente', "error");
		window.scroll(0, 0);
	}else if(resultado == -3){
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Ya existe un paciente con el mismo número de documento");
		alert_basico("-3", 'Ya existe un paciente con el mismo número de documento', "error");
		window.scroll(0, 0);
	}else{
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error interno al guardar los datos del paciente");
		alert_basico("", 'Error interno al guardar los datos del paciente', "error");
		window.scroll(0, 0);
	}
      
}

function seleccionar_pais(id_pais, sufijo) {
    if (id_pais == "1") {
        $("#td_dep_col_" + sufijo).css("display", "table-cell");
        $("#td_mun_col_" + sufijo).css("display", "table-cell");
        $("#td_dep_col_val_" + sufijo).css("display", "table-cell");
        $("#td_mun_col_val_" + sufijo).css("display", "table-cell");
        $("#td_dep_otro_" + sufijo).css("display", "none");
        $("#td_mun_otro_" + sufijo).css("display", "none");
        $("#td_dep_otro_val_" + sufijo).css("display", "none");
        $("#td_mun_otro_val_" + sufijo).css("display", "none");
    } else {
        $("#td_dep_col_" + sufijo).css("display", "none");
        $("#td_mun_col_" + sufijo).css("display", "none");
        $("#td_dep_col_val_" + sufijo).css("display", "none");
        $("#td_mun_col_val_" + sufijo).css("display", "none");
        $("#td_dep_otro_" + sufijo).css("display", "table-cell");
        $("#td_mun_otro_" + sufijo).css("display", "table-cell");
        $("#td_dep_otro_val_" + sufijo).css("display", "table-cell");
        $("#td_mun_otro_val_" + sufijo).css("display", "table-cell");
    }
}

function seleccionar_departamento(cod_dep, sufijo) {
    var params = "opcion=4&cod_dep=" + cod_dep +
            "&sufijo=" + sufijo;

    llamarAjax("../admisiones/pacientes_ajax.php", params, "d_municipio_" + sufijo, "");
}

function muestraImportarPacientes() {
    var params = "opcion=5";
    llamarAjax("pacientes_ajax.php", params, "d_contenedor_paciente_hc", "");
}

function importarPacientes() {
    var file = $("#file").val();
    alert(file);
}

function validarImportarPacientes() {
    var ind_importar = 1;
    $("#contenedor_error").css("display", "none");
    $("#fileISS").removeClass("borde_error");
    $("#cmb_convenio").removeClass("borde_error");
    $("#cmb_plan").removeClass("borde_error");

    if ($("#fileISS").val() == "") {
        ind_importar = 0;
        $("#fileISS").addClass("borde_error");
    }
    if ($("#cmb_convenio").val() == "") {
        ind_importar = 0;
        $("#cmb_convenio").addClass("borde_error");
    }

    if ($("#cmb_plan").val() == "") {
        ind_importar = 0;
        $("#cmb_plan").addClass("borde_error");
    }

    if (ind_importar == 1) {
        var params = "opcion=6" +
                "&idConvenio=" + $('#cmb_convenio').val() +
                "&idPlan=" + $('#cmb_plan').val();

        swal({//Ventana temporal que muestra el progreso del ajax...
            title: 'Espere un momento',
            allowOutsideClick: false,
            showConfirmButton: false,
            showCloseButton: true,
            html: '<div id=""><img src="../imagenes/ajax-loader.gif" ></div>',
            onOpen: () => {
                llamarAjaxUploadFiles("pacientes_ajax.php", params, "resultadoImportarISS", "validarImportacionPacientes()", "d_barra_progreso_adj", "fileISS");
            }
        });
    } else {
        $("#contenedor_error").html("Los campos marcados en rojo son obligatorios.");
        $("#contenedor_error").css("display", "block");
    }
}

function validarImportacionPacientes() {
    var rta = $('#hddResultadoValidaArchivo').val();

    switch (rta) {
        case "-1":
            alert_basico('Error', 'Error al cargar el archivo', 'error');
            break;
        case "-3":
            alert_basico('Error', 'Error al leer el archivo', 'error');
            break;
        case "-4":
            alert_basico('Error', 'Error de versión del archivo', 'error');
            break;
        case "1":/*Realiza la actualizacion o creacion de pacientes*/
            var rta = $('#hddResultadoImportarPacientes').val();

            switch (rta) {
                case "-1":
                    alert_basico('Error', 'Error al realizar la importación', 'error');
                    break;
                case "1":/*Realiza la actualizacion o creacion de pacientes*/
                    alert_basico('Los pacientes ha sido cargados!', '', 'success');
                    $("#cmb_convenio").val("");
                    $("#cmb_plan").val("");
                    $("#fileISS").val(null);
                    break;
            }
            break;
    }
}

function getPlanes(idConvenio) {
    var idConvenio = idConvenio;
    var params = "opcion=8&idConvenio=" + idConvenio;
    llamarAjax("../admisiones/pacientes_ajax.php", params, "div_plan", "");
}

function nuevo_paciente() {
    var params = "opcion=10";
    llamarAjax("pacientes_ajax.php", params, "d_contenedor_paciente_hc", "");
}