/***********************************************/
/*Configuración del editor de texto enriquecido*/
/***********************************************/

if (CKEDITOR.env.ie && CKEDITOR.env.version < 9) {
    CKEDITOR.tools.enableHtml5Elements(document);
}

CKEDITOR.config.width = 'auto';

var initCKEditorCitas = (function (id_obj) {
    var wysiwygareaAvailable = isWysiwygareaAvailable(),
            isBBCodeBuiltIn = !!CKEDITOR.plugins.get('bbcode');

    return function (id_obj) {
        var editorElement = CKEDITOR.document.getById(id_obj);

        //Dependiendo de la disponibilidad del plugin wysiwygare se incia el editor clásico o el editor en línea.
        if (wysiwygareaAvailable) {
            CKEDITOR.replace(id_obj);
        } else {
            editorElement.setAttribute('contenteditable', 'true');
            CKEDITOR.inline(id_obj);
        }
    };

    function isWysiwygareaAvailable() {
        if (CKEDITOR.revision == ('%RE' + 'V%')) {
            return true;
        }

        return !!CKEDITOR.plugins.get('wysiwygarea');
    }
})();

/***********************************************/
/***********************************************/
/***********************************************/

function cargar_recomendacion(cod_recom) {
    var texto_recomendacion = $("#hdd_citaRecomendacion_" + cod_recom).val();
    //var indice = $("#cmb_num_formula").val();

    //Tomar el texto del editor
    var text_observacion = eval("CKEDITOR.instances.txt_observacion_cita.getData()");

    //Se a grega el texto de la fórmula
    var texto_descripcion_formula = text_observacion + str_decode(texto_recomendacion);

    //Se cargar a la caja de texto
    //eval("CKEDITOR.instances.text_despacho_" + indice + ".setData(texto_descripcion_formula)");
    eval("CKEDITOR.instances.txt_observacion_cita.setData(texto_descripcion_formula)");
}

var bol_evento_editar = true;
var ano_seleccionado = "";
var mes_seleccionado = "";

function calendario(mes, anio) {
    mes_seleccionado = mes;
    ano_seleccionado = anio;
    var usuario = $("#cmb_lista_usuarios").val();
    var params = "opcion=1&mes=" + mes +
            "&anio=" + anio +
            "&usuario=" + usuario +
            "&ind_reasignar=" + $("#hdd_reasignar").val() +
            "&id_cita_reasignar=" + $("#hdd_cita_reasignar").val();

    llamarAjax("asignar_citas_ajax.php", params, "agenda", "");
}

function mostrar_formulario_flotante_citas(tipo) {
    if (tipo == 1) { //mostrar
        $("#fondo_negro_citas").css("display", "block");
        $("#d_centro_cita").slideDown(400).css("display", "block");
    } else if (tipo == 0) { //Ocultar
        $("#fondo_negro_citas").css("display", "none");
        $("#d_centro_cita").slideDown(400).css("display", "none");
    }
}

function mostrar_formulario_flotante_citas2(tipo) {
    if (tipo == 1) { //mostrar
        $("#fondo_negro_citas2").css("display", "block");
        $("#d_centro_cita2").slideDown(400).css("display", "block");
    } else if (tipo == 0) { //Ocultar
        $("#fondo_negro_citas2").css("display", "none");
        $("#d_centro_cita2").slideDown(400).css("display", "none");
    }
}

function sin_atencion(fecha, id_usuario_prof) {

}

function disponible(fecha, id_usuario_prof) {
    var usuario = $("#cmb_lista_usuarios").val();
    var ind_reasignar = $("#hdd_reasignar").val();
    var id_cita_reasignar = $("#hdd_cita_reasignar").val();
    ano_seleccionado = fecha.substring(0, 4);
    mes_seleccionado = fecha.substring(5, 7);

    var params = "opcion=2&fecha=" + fecha + "&usuario=" + usuario +
            "&id_usuario_prof=" + id_usuario_prof +
            "&ind_reasignar=" + ind_reasignar +
            "&id_cita_reasignar=" + id_cita_reasignar;

    llamarAjax("asignar_citas_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1)");
    borrar_temporal_cita();
}

function no_disponible(fecha, id_usuario_prof) {
    disponible(fecha, id_usuario_prof);
}

function citas_por_cancelar(fecha, id_usuario_prof) {
    disponible(fecha, id_usuario_prof);
}

function abrir_crear_cita(fecha, hora_ini, hora_fin, id_usuario, id_lugar_disp) {
    bol_evento_editar = false;

    var params = "opcion=3&fecha=" + fecha +
            "&hora_ini=" + hora_ini +
            "&hora_fin=" + hora_fin +
            "&id_usuario=" + id_usuario +
            "&id_lugar_disp=" + id_lugar_disp +
            "&id_paciente_prog_cx=" + $("#hdd_id_paciente_prog_cx").val();

    llamarAjax("asignar_citas_ajax.php", params, "d_interno_citas", "mostrar_formulario_flotante_citas(1);remisionValidarIdPaciente();");

    setTimeout("bol_evento_editar = true;", 3000);
}

function remisionValidarIdPaciente() {
    var tipoDocumento = $("#cmb_tipo_documento").val();
    var numeroDocumento = $("#txt_numero_documento").val();
    var indicadorReasignar = $("#hdd_reasignar").val();

    if (tipoDocumento.length > 0 && numeroDocumento.length > 0 && indicadorReasignar == 0) {
        var lugar_cita = $("#hdd_lugar_disp").val();
        buscar_documento_paciente(numeroDocumento, lugar_cita);
    }
}

function abrir_editar_cita(id_cita, id_usuario, id_lugar_disp) {
    if (bol_evento_editar) {
        var params = "opcion=3" +
                "&id_cita=" + id_cita +
                "&id_usuario=" + id_usuario +
                "&id_lugar_disp=" + id_lugar_disp;

        llamarAjax("asignar_citas_ajax.php", params, "d_interno_citas", "mostrar_formulario_flotante_citas(1)");
    }
}

function cerrar_div_centro_citas(fecha) {
    $("#fondo_negro_citas").css("display", "none");
    $("#d_centro_cita").css("display", "none");
    borrar_temporal_cita();
}

function cerrar_div_centro_citas2() {
    $("#fondo_negro_citas2").css("display", "none");
    $("#d_centro_cita2").css("display", "none");
}

function refrescar_calendario() {
    calendario(mes_seleccionado, ano_seleccionado);
}

function validar_crear_cita(numero_menu) {
    $("#frm_citas").validate({
        rules: {
            cmb_tipo_documento: {
                required: true,
            },
            txt_numero_documento: {
                required: true,
                alphanumeric: true
            },
            txt_numero_telefono: {
                required: true,
            },
            cmb_convenio: {
                required: true,
            },
            cmb_lentes: {
                required: true,
            },
            cmb_tipo_cita: {
                required: true,
            },
            cmb_hora_cita: {
                required: true,
            },
            cmb_minuto_cita: {
                required: true,
            },
            cmb_sexo: {
                required: true,
            },
            txt_fecha_nacimiento: {
                required: true,
            },
            cmb_pais: {
                required: true,
            },
            cmb_estatus_convenio: {
                required: true,
            },
            cmb_departamento: {
                required: true,
            },
            cmb_municipio: {
                required: true,
            },
            txt_direccion: {
                required: true,
            },
            cmb_rango: {
                required: true,
            },
            cmb_tipoCotizante: {
                required: true,
            },
            cmb_cod_plan: {
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
        submitHandler: function () {
            crear_cita(numero_menu);
        },
    });
}

function crear_cita(numero_menu) {
    var txt_primer_nombre = str_encode($("#txt_primer_nombre").val());
    var txt_segundo_nombre = str_encode($("#txt_segundo_nombre").val());
    var txt_primer_apellido = str_encode($("#txt_primer_apellido").val());
    var txt_segundo_apellido = str_encode($("#txt_segundo_apellido").val());
    var cmb_tipo_documento = $("#cmb_tipo_documento").val();
    var txt_numero_documento = $("#txt_numero_documento").val();
    var txt_numero_telefono = str_encode($("#txt_numero_telefono").val());
    var cmb_tipo_cita = $("#cmb_tipo_cita").val();
    var cmb_horario_cita = $("#cmb_hora_cita").val() + ":" + $("#cmb_minuto_cita").val();
    var hdd_fecha_cita = $("#hdd_fecha_cita").val();
    var hdd_id_usuario = $("#hdd_id_usuario").val();
    var cmb_convenio = $("#cmb_convenio").val();
    var hdd_id_paciente = $("#hdd_id_paciente").val();
    var txt_fecha_nacimiento = $("#txt_fecha_nacimiento").val();
    var cmb_lentes = $("#cmb_lentes").val();
    var chk_no_pago = $("#chk_no_pago").is(":checked") ? 1 : 0;
    var txt_observacion_cita = str_encode(eval("CKEDITOR.instances.txt_observacion_cita.getData()"));
    var hdd_lugar_disp = $("#hdd_lugar_disp").val();
    var hdd_reasignar = $("#hdd_reasignar").val();
    var hdd_cita_reasignar = $("#hdd_cita_reasignar").val();
    var id_prog_cx = $("#hdd_id_prog_cx").val();
    var cmb_sexo = $("#cmb_sexo").val();
    var cmb_estatus_seguro = $("#cmb_estatus_convenio").val();
    var cmb_pais = $("#cmb_pais").val();
    var txt_direccion = $("#txt_direccion").val();
    /*var cmb_exento_convenio = $("#cmb_exento_convenio").val();*/
    var rango = $("#cmb_rango").val();
    var cmb_tipoCotizante = $("#cmb_tipoCotizante").val();
    var cmb_cod_plan = $("#cmb_cod_plan").val();
	
	var cmb_sede_alter = $("#cmb_sede_alter").val() != undefined ? $("#cmb_sede_alter").val() : "";
	console.log(cmb_sede_alter);
    //Valida la sección de país y municipios de residencia
    var cmb_cod_dep_res = "";
    var cmb_cod_mun_res = "";
    var txt_nom_dep_res = "";
    var txt_nom_mun_res = "";
    var bandera_guardar = false;
    if ($("#cmb_cod_dep_res").is(":visible") && $("#cmb_cod_mun_res").is(":visible")) {
        cmb_cod_dep_res = $("#cmb_cod_dep_res").val();
        cmb_cod_mun_res = $("#cmb_cod_mun_res").val();

        if (cmb_cod_dep_res.length > 0 && cmb_cod_mun_res.length > 0) {
            bandera_guardar = true;
        } else {
            $("#cmb_cod_dep_res").addClass("bordeAdmision")
            $("#cmb_cod_mun_res").addClass("bordeAdmision")
            alert_basico("", "Los campos en color rojo son obligatorios", "error");
        }
    } else if ($("#txt_nom_dep_res").is(":visible") && $("#txt_nom_mun_res").is(":visible")) {
        txt_nom_dep_res = $("#txt_nom_dep_res").val();
        txt_nom_mun_res = $("#txt_nom_mun_res").val();

        if (txt_nom_dep_res.length > 0 && txt_nom_mun_res.length > 0) {
            bandera_guardar = true;
        } else {
            $("#txt_nom_dep_res").addClass("bordeAdmision")
            $("#txt_nom_mun_res").addClass("bordeAdmision")
            alert_basico("", "Los campos en color rojo son obligatorios", "error");
        }
    }

    if (bandera_guardar) {
        bandera_guardar = false;
        //Valida estado del convenio y posibilidades para asignar la cita médica
        if (hdd_id_paciente.length <= 0) {//Si es un paciente nuevo
            bandera_guardar = true;
        } else {
            var id_convenio_db = $("#hdd_id_convenio_pc").val();
            var id_plan_db = $("#hdd_id_plan_pc").val();
            var estado_convenio_db = $("#hdd_status_convenio").val();
            if (estado_convenio_db == 1) {//Sí el estado es activo
                bandera_guardar = true;
            } else {

                if (id_convenio_db == cmb_convenio && id_plan_db == cmb_cod_plan) {
                    alert_basico("", "El paciente se encuentra inactivo para el convenio y el plan seleccionados", "error");
                } else {
                    cmb_estatus_seguro = 1;//Asigna estado de activo             
                    bandera_guardar = true;
                }
            }
        }
        if (bandera_guardar) {
            bandera_guardar = false;

            //Valida sí el usuario profesional tiene restricciones de convenios que debe atender únicamente
            var indConveniosAutorizados = $("#hdd_ind_convenios_autorizados").val();
            if (indConveniosAutorizados == 1) {
                var conveniosAutorizados = $("#hdd_convenios_autorizados").val().split(";");
                if (conveniosAutorizados.includes(cmb_convenio)) {
                    bandera_guardar = true;
                } else {
                    var tmpCmbConvenioTexto = $("#cmb_convenio option:selected").text();
                    alert_basico("", "El usuario profesional no puede atender pacientes con el convenio: " + tmpCmbConvenioTexto, "error");
                }
            } else {
                bandera_guardar = true;
            }

            if (bandera_guardar) {
                var params = "opcion=5&txt_primer_nombre=" + txt_primer_nombre +
                        "&txt_segundo_nombre=" + txt_segundo_nombre +
                        "&txt_primer_apellido=" + txt_primer_apellido +
                        "&txt_segundo_apellido=" + txt_segundo_apellido +
                        "&cmb_tipo_documento=" + cmb_tipo_documento +
                        "&txt_numero_documento=" + txt_numero_documento +
                        "&txt_numero_telefono=" + txt_numero_telefono +
                        "&cmb_tipo_cita=" + cmb_tipo_cita +
                        "&cmb_horario_cita=" + cmb_horario_cita +
                        "&hdd_fecha_cita=" + hdd_fecha_cita +
                        "&hdd_id_usuario=" + hdd_id_usuario +
                        "&txt_fecha_nacimiento=" + txt_fecha_nacimiento +
                        "&cmb_convenio=" + cmb_convenio +
                        "&cmb_lentes=" + cmb_lentes +
                        "&chk_no_pago=" + chk_no_pago +
                        "&txt_observacion_cita=" + txt_observacion_cita +
                        "&hdd_id_paciente=" + hdd_id_paciente +
                        "&hdd_lugar_disp=" + hdd_lugar_disp +
                        "&ind_reasignar=" + hdd_reasignar +
                        "&id_cita_reasignar=" + hdd_cita_reasignar +
                        "&id_prog_cx=" + id_prog_cx +
                        "&cmb_sexo=" + cmb_sexo +
                        "&cmb_pais=" + cmb_pais +
                        "&cmb_estatus_seguro=" + cmb_estatus_seguro +
                        "&cmb_cod_dep_res=" + cmb_cod_dep_res +
                        "&cmb_cod_mun_res=" + cmb_cod_mun_res +
                        "&txt_nom_dep_res=" + txt_nom_dep_res +
                        "&txt_nom_mun_res=" + txt_nom_mun_res +
                        "&txt_direccion=" + txt_direccion +
                        "&rango=" + rango +
                        "&cmb_tipoCotizante=" + cmb_tipoCotizante +
                        "&cmb_cod_plan=" + cmb_cod_plan + 
						"&cmb_sede_alter=" + cmb_sede_alter;

                if ($("#tr_espera_cx").is(":visible")) {
                    params += "&ind_quitar_lista_cx=" + ($("#chk_espera_si").is(":checked") ? 1 : 0) +
                            "&id_reg_lista=" + $("#hdd_reg_lista_b").val();
                } else {
                    params += "&ind_quitar_lista_cx=2&id_reg_lista=";
                }


                llamarAjax("asignar_citas_ajax.php", params, "d_guardar_cita", " validar_exito_cita(" + numero_menu + ", " + $("#hdd_id_usuario").val() + ") ");
                //llamarAjax("asignar_citas_ajax.php", params, "d_guardar_cita", "  ");
            }
        }
    }
}

//Genera el reporte para la cita
function generarPDFCita(idCita) {
    var params = 'opcion=4&idCita=' + idCita;
    llamarAjax("reporte_citas_ajax.php", params, "d_generar_pdf2", "descargar_pdf2();");
}

function descargar_pdf2() {
    if (isObject(document.getElementById("hdd_archivo_pdf2"))) {
        var nombreArchivo = document.getElementById("hdd_archivo_pdf2").value;

        window.open("../funciones/abrir_pdf.php?ruta=" + escape(nombreArchivo) + "&nombre_arch=remision.pdf", "_blank");
    } else {
        alert("Archivo no disponible");
    }
}

function validar_exito_cita(numero_menu, id_usuario_prof) {
    var hdd_exito = $("#hdd_exito").val();
    var hdd_fecha = $("#hdd_fecha").val();

    if (hdd_exito > 0) {
        $("#frm_citas").html("");
        $("#contenedor_exito").css("display", "block");
        $("#contenedor_exito").html("Datos guardados correctamente");
        setTimeout(function () {
            $("#contenedor_exito").slideUp(100).css("display", "none");
            mostrar_formulario_flotante_citas(0);
        }, 900);
        disponible(hdd_fecha, id_usuario_prof);
        refrescar_calendario();

        var ind_reasignar = $("#hdd_reasignar").val();
        if (ind_reasignar == "1") {
            setTimeout("cancelar_reasignar_cita(" + numero_menu + ")", 1000);
        }
        generarPDFCita(hdd_exito);
    } else {
        switch (hdd_exito) {
            case "-1":
                $("#contenedor_error").css("display", "block");
                $("#contenedor_error").html("Error interno al guardar los datos de la cita");
                setTimeout('$("#contenedor_error").slideUp(200).css("display", "none")', 4000);
                break;
            case "-2":
                $("#contenedor_error").css("display", "block");
                $("#contenedor_error").html("Error al guardar los datos de la cita");
                setTimeout('$("#contenedor_error").slideUp(200).css("display", "none")', 4000);
                break;
            case "-3":
                $("#contenedor_error").css("display", "block");
                $("#contenedor_error").html("El horario seleccionado ya no se encuentra disponible, por favor seleccione un nuevo horario");
                setTimeout('$("#contenedor_error").slideUp(200).css("display", "none")', 4000);
                cargar_horario(1, 0, 1);
                break;
        }
    }
}

function validar_editar_cita() {
    $("#frm_citas").validate({
        rules: {
            cmb_tipo_documento: {
                required: true,
            },
            txt_numero_documento: {
                required: true,
                alphanumeric: true
                        //documentoexistente: true
            },
            txt_numero_telefono: {
                required: true,
            },
            cmb_convenio: {
                required: true,
            },
            cmb_lentes: {
                required: true,
            },
            cmb_sexo: {
                required: true,
            },
            txt_fecha_nacimiento: {
                required: true,
            },
            cmb_pais: {
                required: true,
            },
            cmb_estatus_convenio: {
                required: true,
            },
            cmb_exento_convenio: {
                required: true,
            },
            txt_direccion: {
                required: true,
            },
            cmb_rango: {
                required: true,
            },
            cmb_tipoCotizante: {
                required: true,
            },
            cmb_cod_plan: {
                required: true,
            },
            txt_primer_nombre: {
                required: true,
            },
            txt_primer_apellido: {
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
        submitHandler: function () {
            editar_cita();
        },
    });
}

function editar_cita() {
    var txt_primer_nombre = str_encode($("#txt_primer_nombre").val());
    var txt_segundo_nombre = str_encode($("#txt_segundo_nombre").val());
    var txt_primer_apellido = str_encode($("#txt_primer_apellido").val());
    var txt_segundo_apellido = str_encode($("#txt_segundo_apellido").val());
    var cmb_tipo_documento = $("#cmb_tipo_documento").val();
    var txt_numero_documento = $("#txt_numero_documento").val();
    var txt_numero_telefono = str_encode($("#txt_numero_telefono").val());
    var cmb_convenio = $("#cmb_convenio").val();
    var cmb_lentes = $("#cmb_lentes").val();
    var chk_no_pago = $("#chk_no_pago").is(":checked") ? 1 : 0;

    //var txt_observacion_cita = str_encode($("#txt_observacion_cita").val());
    var txt_observacion_cita = str_encode(eval("CKEDITOR.instances.txt_observacion_cita.getData()"));

    var hdd_id_cita = $("#hdd_id_cita").val();
    var hdd_fecha_cita = $("#hdd_fecha_cita").val();
    var hdd_lugar_disp = $("#hdd_lugar_disp").val();
    var hdd_id_paciente = $("#hdd_id_paciente").val();

    var cmb_sexo = $("#cmb_sexo").val();
    var cmb_pais = $("#cmb_pais").val();
    var txt_fecha_nacimiento = $("#txt_fecha_nacimiento").val();

    var cmb_estatus_seguro = $("#cmb_estatus_convenio").val();
    var cmb_exento_convenio = $("#cmb_exento_convenio").val();
    var txt_direccion = $("#txt_direccion").val();
    var rango = $("#cmb_rango").val();
    var cmb_tipoCotizante = $("#cmb_tipoCotizante").val();
    var cmb_cod_plan = $("#cmb_cod_plan").val();

    //Valida
    var cmb_cod_dep_res = "";
    var cmb_cod_mun_res = "";
    var txt_nom_dep_res = "";
    var txt_nom_mun_res = "";
    var bandera_guardar = false;
    if ($("#cmb_cod_dep_res").is(":visible") && $("#cmb_cod_mun_res").is(":visible")) {
        cmb_cod_dep_res = $("#cmb_cod_dep_res").val();
        cmb_cod_mun_res = $("#cmb_cod_mun_res").val();

        if (cmb_cod_dep_res.length > 0 && cmb_cod_mun_res.length > 0) {
            bandera_guardar = true;
        } else {
            $("#cmb_cod_dep_res").addClass("bordeAdmision")
            $("#cmb_cod_mun_res").addClass("bordeAdmision")
            alert_basico("", "Los campos en color rojo son obligatorios", "error");
        }

    } else if ($("#txt_nom_dep_res").is(":visible") && $("#txt_nom_mun_res").is(":visible")) {
        txt_nom_dep_res = $("#txt_nom_dep_res").val();
        txt_nom_mun_res = $("#txt_nom_mun_res").val();

        if (txt_nom_dep_res.length > 0 && txt_nom_mun_res.length > 0) {
            bandera_guardar = true;
        } else {
            $("#txt_nom_dep_res").addClass("bordeAdmision")
            $("#txt_nom_mun_res").addClass("bordeAdmision")
            alert_basico("", "Los campos en color rojo son obligatorios", "error");
        }
    }

    if (bandera_guardar) {
        var params = "opcion=7&txt_primer_nombre=" + txt_primer_nombre +
                "&txt_segundo_nombre=" + txt_segundo_nombre +
                "&txt_primer_apellido=" + txt_primer_apellido +
                "&txt_segundo_apellido=" + txt_segundo_apellido +
                "&cmb_tipo_documento=" + cmb_tipo_documento +
                "&txt_numero_documento=" + txt_numero_documento +
                "&txt_numero_telefono=" + txt_numero_telefono +
                "&cmb_convenio=" + cmb_convenio +
                "&cmb_lentes=" + cmb_lentes +
                "&chk_no_pago=" + chk_no_pago +
                "&txt_observacion_cita=" + txt_observacion_cita +
                "&hdd_id_cita=" + hdd_id_cita +
                "&hdd_fecha_cita=" + hdd_fecha_cita +
                "&hdd_lugar_disp=" + hdd_lugar_disp +
                "&hdd_id_paciente=" + hdd_id_paciente +
                "&cmb_sexo=" + cmb_sexo +
                "&cmb_pais=" + cmb_pais +
                "&txt_fecha_nacimiento=" + txt_fecha_nacimiento +
                "&cmb_estatus_seguro=" + cmb_estatus_seguro +
                "&txt_direccion=" + txt_direccion +
                "&cmb_exento_convenio=" + cmb_exento_convenio +
                "&rango=" + rango +
                "&cmb_tipoCotizante=" + cmb_tipoCotizante +
                "&cmb_cod_dep_res=" + cmb_cod_dep_res +
                "&cmb_cod_mun_res=" + cmb_cod_mun_res +
                "&txt_nom_dep_res=" + txt_nom_dep_res +
                "&txt_nom_mun_res=" + txt_nom_mun_res +
                "&cmb_cod_plan=" + cmb_cod_plan;

        llamarAjax("asignar_citas_ajax.php", params, "d_guardar_cita", "validar_exito_cita(0, " + $("#hdd_id_usuario").val() + ")");
    }
}

/*
 Tipos de horario
 1 - horas
 2 - minutos
 */
function cargar_horario(tipo_horario, horas, ind_buscar_horario) {
    var tipo_cita = $("#cmb_tipo_cita").val();
    var usuario = $("#hdd_id_usuario").val();
    var hdd_fecha_cita = $("#hdd_fecha_cita").val();
    var hdd_hora_ini = $("#hdd_hora_ini").val();
    var hdd_hora_fin = $("#hdd_hora_fin").val();
    var params = "opcion=4&tipo_cita=" + tipo_cita +
            "&fecha_cita=" + hdd_fecha_cita +
            "&hora_ini=" + hdd_hora_ini +
            "&hora_fin=" + hdd_hora_fin +
            "&usuario=" + usuario +
            "&tipo_horario=" + tipo_horario +
            "&horas=" + horas +
            "&ind_buscar_horario=" + ind_buscar_horario;

    switch (tipo_horario) {
        case 1:
            llamarAjax("asignar_citas_ajax.php", params, "div_horas_cita", "");
            break;
        case 2:
            llamarAjax("asignar_citas_ajax.php", params, "div_minutos_cita", "");
            var hora_num = parseInt(horas, 10);
            var texto_aux = "AM";
            if (hora_num >= 12) {
                texto_aux = "PM";
            }
            document.getElementById("lb_ampm_cita").innerHTML = "&nbsp;" + texto_aux;
            verificar_hora_disponible("");
            break;
    }
}

function validar_documento_cita(documento, tipo, id_cita, fecha) {
    var params = "opcion=6&documento_cita=" + documento +
            "&tipo=" + tipo +
            "&id_cita=" + id_cita +
            "&fecha_cita=" + fecha;

    llamarAjax("asignar_citas_ajax.php", params, "div_documento_existe", "validar_documento()");
}

function validar_documento() {
    var validar_existe_documento = $("#hdd_documento_existe").val();

    if (validar_existe_documento == "1") {
        var nombre_existe = $("#hdd_nombre_existe").val();
        var fecha_existe = $("#hdd_fecha_existe").val();
        var hora_existe = $("#hdd_hora_existe").val();
        var mismo_dia_existe = $("#hdd_mismo_dia_existe").val();

        $("#contenedor_advertencia").css("display", "block");
        if (mismo_dia_existe == "1") {
            $("#contenedor_advertencia").html("La persona " + nombre_existe + " con el mismo n&uacute;mero de documento tiene cita el mismo d&iacute;a a las " + hora_existe + ".");
        } else {
            $("#contenedor_advertencia").html("La persona " + nombre_existe + " con el mismo n&uacute;mero de documento tiene cita el d&iacute;a " + fecha_existe + " a las " + hora_existe + ".");
        }
    } else {
        $("#contenedor_advertencia").css("display", "none");
        $("#contenedor_advertencia").html("");
    }
}

function buscar_documento_paciente(documento, id_lugar_cita) {
    $("#tr_espera_cx").css("display", "none");
    var tipo_documento = document.getElementById("cmb_tipo_documento").value;
    if (tipo_documento != "") {
        var hdd_fecha_cita = $("#hdd_fecha_cita").val();
        var hdd_hora_ini = $("#hdd_hora_ini").val();
        var hdd_hora_fin = $("#hdd_hora_fin").val();
        var hdd_id_usuario = $("#hdd_id_usuario").val();
        var params = "opcion=11&documento=" + documento +
                "&tipo_documento=" + tipo_documento +
                "&hdd_fecha_cita=" + hdd_fecha_cita +
                "&hdd_hora_ini=" + hdd_hora_ini +
                "&hdd_hora_fin=" + hdd_hora_fin +
                "&hdd_id_usuario=" + hdd_id_usuario +
                "&id_lugar_cita=" + id_lugar_cita;

        llamarAjax("asignar_citas_ajax.php", params, "d_interno_citas2", "validar_documento_persona()");
    }
}

function validar_documento_persona() {
    var hdd_documento_persona = $("#hdd_documento_persona").val();
    if (hdd_documento_persona == "true") {
        mostrar_formulario_flotante_citas2(1)
    } else {
        $('#trRecomendacion').css({'display': 'table-row'});
        $('#trEstadoInactivo').css({'display': 'none'});
        $('#trEstadoActivo').css({'display': 'none'});
    }
}

function cerrar_documento_persona() {
    mostrar_formulario_flotante_citas2(0);
}

function abrir_crear_cita_paciente(fecha, hora_ini, hora_fin, id_usuario, id_paciente, id_cita_ant, id_lugar_disp, ind_reasignar) {
    var params = "opcion=3&fecha=" + fecha +
            "&hora_ini=" + hora_ini +
            "&hora_fin=" + hora_fin +
            "&id_usuario=" + id_usuario +
            "&id_paciente=" + id_paciente +
            "&id_cita_ant=" + id_cita_ant +
            "&id_lugar_disp=" + id_lugar_disp +
            "&ind_reasignar=" + ind_reasignar;

    llamarAjax("asignar_citas_ajax.php", params, "d_interno_citas", "mostrar_formulario_flotante_citas2(0);mostrar_formulario_flotante_citas(1)");
}

function abrir_cancelar_cita() {
    $("#frm_citas").validate({
        rules: {

        },
        submitHandler: function () {
            var hdd_id_cita = $("#hdd_id_cita").val();
            var hdd_id_usuario = $("#hdd_id_usuario").val();
            var hdd_fecha_cita = $("#hdd_fecha_cita").val();
            var hdd_lugar_disp = $("#hdd_lugar_disp").val();

            var params = "opcion=8&hdd_id_cita=" + hdd_id_cita +
                    "&hdd_id_usuario=" + hdd_id_usuario +
                    "&hdd_fecha_cita=" + hdd_fecha_cita +
                    "&hdd_lugar_disp=" + hdd_lugar_disp;

            llamarAjax("asignar_citas_ajax.php", params, "frm_citas", "refrescar_calendario();");
        },
    });
}

function cancelar_cita(id_cita, fecha_cita, id_usuario_prof) {
    if ($("#cmb_tipo_cancela").val() == "") {
        alert("Debe seleccionar el tipo de cancelaci\xf3n");
        document.getElementById("cmb_tipo_cancela").focus();
        return;
    }
    if (trim($("#txt_observacion_cancela").val()).length < 5) {
        alert("Debe ingresar una observaci\xf3n v\xe1lida");
        document.getElementById("txt_observacion_cancela").focus();
        return;
    }

    var params = "opcion=9&id_cita=" + id_cita +
            "&fecha_cita=" + fecha_cita +
            "&id_tipo_cancela=" + $("#cmb_tipo_cancela").val() +
            "&observacion_cancela=" + str_encode($("#txt_observacion_cancela").val());
    llamarAjax("asignar_citas_ajax.php", params, "frm_citas", "validar_exito_cita(0, " + id_usuario_prof + ")");
}

function buscar_persona_cita() {
    var txt_busca_usuario = $("#txt_busca_usuario").val();
    var cmb_lista_usuarios = $("#cmb_lista_usuarios").val();

    if (txt_busca_usuario == "") {
        $("#msg_buscar").css("display", "block");
        $("#msg_buscar").html("Debe ingresar un valor para buscar");
    } else {
        $("#msg_buscar").css("display", "none");
        $("#msg_buscar").html("");

        var params = "opcion=10&txt_busca_usuario=" + txt_busca_usuario +
                "&cmb_lista_usuarios=" + cmb_lista_usuarios;

        llamarAjax("asignar_citas_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1)");
    }
}

function evento_enter() {
    $("#txt_busca_usuario").keydown(function (e) {
        if (e.keyCode == 13) {
            $("#frm_buscar_cita").validate({
                rules: {
                    txt_busca_usuario: {
                        required: true,
                    }
                },
                submitHandler: function () {
                    var txt_busca_usuario = $("#txt_busca_usuario").val();
                    var cmb_lista_usuarios = $("#cmb_lista_usuarios").val();

                    if (txt_busca_usuario == "") {
                        $("#msg_buscar").css("display", "block");
                        $("#msg_buscar").html("Debe ingresar un valor para buscar");
                    } else {
                        $("#msg_buscar").css("display", "none");
                        $("#msg_buscar").html("");

                        var params = "opcion=10&txt_busca_usuario=" + txt_busca_usuario +
                                "&cmb_lista_usuarios=" + cmb_lista_usuarios;

                        llamarAjax("asignar_citas_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1)");
                    }
                    return false;
                },
            });
        }
    });
}

/*
 * tipo= 1= mostrar ; 0= ocultar
 */
function mostrar_div_crear_cita(id_cita, tipo) {

    if (tipo == 1) {
        $("#crear_cita_" + id_cita).css("display", "block");
    } else if (tipo == 0) {
        $("#crear_cita_" + id_cita).css("display", "none");
    }
}

var cont_cal_actual = 0;
var cont_cal_total = 0;
function mostrar_cal_siguiente() {
    cont_cal_actual += 3;
    if (cont_cal_actual + 3 >= cont_cal_total) {
        cont_cal_actual = cont_cal_total - 3;
    }

    mostrar_cal_usuarios();
}

function mostrar_cal_anterior() {
    cont_cal_actual -= 3;
    if (cont_cal_actual < 0) {
        cont_cal_actual = 0;
    }

    mostrar_cal_usuarios();
}

function mostrar_cal_usuarios() {
    for (var i = 0; i < cont_cal_total; i++) {
        if (i >= cont_cal_actual && i <= cont_cal_actual + 2) {
            document.getElementById("d_profesional_" + i).style.display = "block";
        } else {
            document.getElementById("d_profesional_" + i).style.display = "none";
        }
    }

    if (cont_cal_actual > 0) {
        document.getElementById("a_btn_prev").style.display = "block";
    } else {
        document.getElementById("a_btn_prev").style.display = "none";
    }

    if (cont_cal_actual + 3 < cont_cal_total) {
        document.getElementById("a_btn_next").style.display = "block";
    } else {
        document.getElementById("a_btn_next").style.display = "none";
    }
}

function abrir_reasignar_cita(numero_menu) {
    $("#frm_credencial").append('<input type="hidden" id="hdd_reasignar" name="hdd_reasignar" />');
    $("#hdd_reasignar").val(1);
    $("#frm_credencial").append('<input type="hidden" id="hdd_cita_cancelar" name="hdd_cita_cancelar" />');
    $("#hdd_cita_cancelar").val($("#hdd_id_cita").val());

    enviar_credencial("../citas/asignar_citas.php", numero_menu);
}

function cancelar_reasignar_cita(numero_menu) {
    enviar_credencial("../citas/asignar_citas.php", numero_menu);
}

function validar_reasignar_cita(numero_menu) {
    $("#frm_citas").validate({
        rules: {
            //txt_numero_telefono: {
            //    required: true,
            //},
            //cmb_convenio: {
            //    required: true,
            //},
            //cmb_lentes: {
            //    required: true,
            //},
            cmb_tipo_cita: {
                required: true,
            },
            cmb_hora_cita: {
                required: true,
            },
            cmb_minuto_cita: {
                required: true,
            }
        },
        messages: {
            txt_numero_telefono: {
                alphanumeric: "Solo se permite numeros y letras"
            }
        },
        submitHandler: function () {
            crear_cita(numero_menu);
        },
    });
}

function seleccionar_convenio(id_convenio) {
    var params = "opcion=20&id_convenio=" + id_convenio;
    llamarAjax("asignar_citas_ajax.php", params, "d_plan", "");
}

function cargar_datos_paciente_asignar_plan() {
    $("#cmb_cod_plan").val($("#hdd_id_plan_pc").val());
}
function cargar_datos_paciente() {
    var id_paciente = $("#hdd_id_paciente_pc").val();
    $("#hdd_id_paciente").val(id_paciente);
    $("#cmb_tipo_documento").val($("#hdd_id_tipo_documento_pc").val());
    $("#txt_numero_documento").val($("#hdd_numero_documento_pc").val());
    $("#txt_primer_nombre").val($("#hdd_nombre1_pc").val());
    $("#txt_segundo_nombre").val($("#hdd_nombre2_pc").val());
    $("#txt_primer_apellido").val($("#hdd_apellido1_pc").val());
    $("#txt_segundo_apellido").val($("#hdd_apellido2_pc").val());
    $("#txt_numero_telefono").val($("#hdd_telefono_pc").val());

    $("#cmb_sexo").val($("#hdd_sexo").val());
    $("#txt_fecha_nacimiento").val($("#hdd_fecha_nacimiento").val());
    $("#cmb_pais").val($("#hdd_pais").val());

    var id_convenio = $("#hdd_id_convenio_pc").val();
    $("#cmb_convenio").val(id_convenio);
    $("#cmb_estatus_convenio").val($("#hdd_status_convenio").val());
    $("#cmb_exento_convenio").val($("#hdd_exento_convenio").val());
    $("#cmb_cod_plan").val($("#hdd_id_plan_pc").val());

    //$("#cmb_cod_plan").val($("#hdd_id_plan").val());
    $("#txt_direccion").val($("#hdd_direccion").val());
    $("#cmb_rango").val($("#hdd_rango").val());
    $("#cmb_tipoCotizante").val($("#hdd_tipoCotizante").val());


    var params = "opcion=20&id_convenio=" + id_convenio;
    llamarAjax("asignar_citas_ajax.php", params, "d_plan", "cargar_datos_paciente_asignar_plan()");


    if ($("#hdd_pais").val() == "1") {//Sí es Colombia
        $("#cmb_cod_dep_res").val($("#hdd_cod_dep").val());
        $("#cmb_cod_mun_res").val($("#hdd_cod_mun").val());
        $("#tr_dep_mun_otro_val_res").css({'display': 'none'});
        $("#tr_dep_mun_col_val_res").css({'display': 'content'});
    } else {
        $("#txt_nom_mun_res").val($("#hdd_nom_mun").val());
        $("#txt_nom_dep_res").val($("#hdd_nom_dep").val());
        $("#tr_dep_mun_col_val_res").css({'display': 'none'});
        $("#tr_dep_mun_otro_val_res").css({'display': 'content'});
    }

    var status = $("#hdd_status_convenio").val();
    if (status == 1) {
        $("#trEstadoActivo").css({'display': 'table-row'});
        $("#trEstadoInactivo").css({'display': 'none'});
        $("#spn_nombre_convenio_activo_aux").text($("#hdd_nom_convenio").val());
        $("#spn_nombre_plan_activo_aux").text($("#hdd_nom_plan").val());
    } else if (status == 2) {
        $("#trEstadoInactivo").css({'display': 'table-row'});
        $("#trEstadoActivo").css({'display': 'none'});
        $("#spn_nombre_convenio_inactivo_aux").html($("#hdd_nom_convenio").val());
        $("#spn_nombre_plan_inactivo_aux").html($("#hdd_nom_plan").val());
    }

    $("#trHistoricoPacientes").css({'display': 'table-row'});
   
    mostrar_formulario_flotante_citas2(0);
}


function verificar_hora_disponible(minuto_cita) {
    var params = "opcion=12&id_usuario_prof=" + $("#hdd_id_usuario").val() +
            "&fecha_cita=" + $("#hdd_fecha_cita").val() +
            "&hora_cita=" + $("#cmb_hora_cita").val() +
            "&minuto_cita=" + minuto_cita +
            "&id_tipo_cita=" + $("#cmb_tipo_cita").val();

    llamarAjax("asignar_citas_ajax.php", params, "d_guardar_cita", "terminar_verificar_hora_disponible();");
}

function terminar_verificar_hora_disponible() {
    var id_usuario_aux = parseInt($("#hdd_usuario_horario_cita").val(), 10);
    if (id_usuario_aux > 0) {
        $("#cmb_minuto_cita").val("");
        var nombre_usuario_aux = $("#hdd_nombre_usuario_horario_cita").val();
        var hora_ini_aux = $("#hdd_hora_ini_horario_cita").val();
        var hora_fin_aux = $("#hdd_hora_fin_horario_cita").val();
        alert("El usuario " + nombre_usuario_aux + " se encuentra programando una cita entre las\n" + hora_ini_aux + " y las " + hora_fin_aux + " por lo que el horario seleccionado no se encuentra disponible.\nPor favor seleccione un horario diferente.");
    }
}

function borrar_temporal_cita() {
    var params = "opcion=13";
    llamarAjax("asignar_citas_ajax.php", params, "", "");
}

function confirmar_cita() {
    if (confirm("\xbfEst\xe1 seguro de confirmar esta cita?")) {
		
       // var txt_observacion_cita = $("#txt_observacion_cita").val();
		var txt_observacion_cita = str_encode(eval("CKEDITOR.instances.txt_observacion_cita.getData()"));
		//alert(txt_observacion_cita);
        var params = "opcion=14&id_cita=" + $("#hdd_id_cita").val() +
                "&fecha_cita=" + $("#hdd_fecha_cita").val() +
                "&txt_observacion_cita=" + str_encode(txt_observacion_cita);

        llamarAjax("asignar_citas_ajax.php", params, "d_guardar_cita", "validar_exito_cita(0, " + $("#hdd_id_usuario").val() + ")");
    }
}

function mostrar_mesaje_citas() {
    var params = "opcion=15";
    llamarAjax("asignar_citas_ajax.php", params, "mensaje_agenda", "");
}

function validar_lista_espera() {
    $("#tr_espera_cx").css("display", "none");
    if ($("#hdd_id_paciente").val() != "" && $("#cmb_tipo_cita").val() != "") {
        var params = "opcion=16&id_paciente=" + $("#hdd_id_paciente").val() +
                "&id_tipo_cita=" + $("#cmb_tipo_cita").val();

        llamarAjax("asignar_citas_ajax.php", params, "d_guardar_cita", "continuar_validar_lista_espera();");
    }
}

function continuar_validar_lista_espera() {
    var id_reg_lista = parseInt($("#hdd_reg_lista_b").val(), 10);

    if (id_reg_lista > 0) {
        $("#d_espera_cx").html(
                "La persona seleccionada est&aacute; en lista de espera para cirug&iacute;a de " + $("#hdd_tipo_cirugia_b").val() +
                " desde el d&iacute;a " + $("#hdd_fecha_lista_b").val() +
                ".<br />&iquest;Desea quitarla de la lista de espera?");
        $("#chk_espera_si").attr("checked", true);
        $("#chk_espera_no").attr("checked", false);
        $("#tr_espera_cx").css("display", "table-row");
    }
}

function controlar_checks_espera(id_activado) {
    switch (id_activado) {
        case "si":
            $("#chk_espera_no").attr("checked", !$("#chk_espera_si").attr("checked"));
            break;
        case "no":
            $("#chk_espera_si").attr("checked", !$("#chk_espera_no").attr("checked"));
            break;
    }
}

function volver_programacion_cx(id_prog_cx, id_paciente, id_menu) {
    $("#frm_credencial").append('<input type="hidden" id="hdd_id_prog_cx_cita" name="hdd_id_prog_cx_cita" value="' + id_prog_cx + '" />');
    $("#frm_credencial").append('<input type="hidden" id="hdd_id_paciente_cita" name="hdd_id_paciente_cita" value="' + id_paciente + '"  />');

    enviar_credencial("../historia_clinica/programacion_cx.php", id_menu);
}


function seleccionar_pais(id_pais) {
    if (id_pais == "1") {
        /*
         $("#td_dep_col_" + sufijo).css("display", "table-cell");
         $("#td_mun_col_" + sufijo).css("display", "table-cell");
         */

        $("#tr_dep_mun_col_val_res").css("display", "contents");
        $("#tr_dep_mun_otro_val_res").css("display", "none");
    } else {
        $("#tr_dep_mun_col_val_res").css("display", "none");
        $("#tr_dep_mun_otro_val_res").css("display", "contents");
        /*
         $("#td_dep_col_" + sufijo).css("display", "none");
         $("#td_mun_col_" + sufijo).css("display", "none");
         $("#td_dep_col_val_" + sufijo).css("display", "none");
         $("#td_mun_col_val_" + sufijo).css("display", "none");
         $("#td_dep_otro_" + sufijo).css("display", "table-cell");
         $("#td_mun_otro_" + sufijo).css("display", "table-cell");
         $("#td_dep_otro_val_" + sufijo).css("display", "table-cell");
         $("#td_mun_otro_val_" + sufijo).css("display", "table-cell");
         */
    }
}

function seleccionar_departamento(cod_dep) {
    var params = "opcion=17&cod_dep=" + cod_dep;
    llamarAjax("asignar_citas_ajax.php", params, "d_municipio", "");
}

function historicoCitas() {
    swal({//Ventana temporal que muestra el progreso del ajax...
        title: 'Histórico de citas',
        allowOutsideClick: false,
        showConfirmButton: false,
        showCloseButton: true,
        html: '<div id="tmpResultadoDespacho"></div>',
        onOpen: () => {
            let idPaciente = $("#hdd_id_paciente").val();
            var params = 'opcion=18&idPaciente=' + idPaciente;
            llamarAjax("asignar_citas_ajax.php", params, "tmpResultadoDespacho", "");
        },
    });
}

//Genera el reporte para la cita
function generarPDFCita(idCita) {
    var params = 'opcion=4&idCita=' + idCita;
    llamarAjax("reporte_citas_ajax.php", params, "d_generar_pdf2", "descargar_pdf2();");
}

function descargar_pdf2() {
    if (isObject(document.getElementById("hdd_archivo_pdf2"))) {
        var nombreArchivo = document.getElementById("hdd_archivo_pdf2").value;

        //var ventanaAux = window.open("../funciones/pdf/descargar_archivo.php?nombre_archivo=" + escape(nombreArchivo), '_blank');
        //ventanaAux.focus();

        window.open("../funciones/abrir_pdf.php?ruta=" + escape(nombreArchivo) + "&nombre_arch=remision.pdf", "_blank");
    } else {
        alert("Archivo no disponible");
    }
}