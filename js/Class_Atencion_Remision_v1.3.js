var g_cant_max_examenes_rem = 10;

function seleccionar_dest_remision(valor, id_admision) {
    if (valor != "-1") {
        var id_tipo_cita_origen = $("#hdd_id_tipo_cita_origen").val();
        var arr_aux = valor.split("-");
        var id_tipo_cita = arr_aux[0];
        var id_tipo_reg = arr_aux[1];
        var ind_examenes = arr_aux[2];

        $("#tr_usuario_atencion_remision").css("display", "none");
        if (ind_examenes == "1" && id_tipo_cita_origen != id_tipo_cita) {
            $("#tr_cant_examenes_remision").css("display", "table-row");
            $("#tr_examenes_remision").css("display", "table-row");
            $("#tr_lugar_remision").css("display", "table-row");
            $("#cmb_lugar_remision").val("");
        } else if (id_tipo_cita_origen != id_tipo_cita) {
            $("#tr_cant_examenes_remision").css("display", "none");
            $("#tr_examenes_remision").css("display", "none");
            $("#tr_lugar_remision").css("display", "table-row");
            $("#cmb_lugar_remision").val("");
        } else {
            $("#tr_cant_examenes_remision").css("display", "none");
            $("#tr_examenes_remision").css("display", "none");
            $("#tr_lugar_remision").css("display", "none");
            $("#cmb_lugar_remision").val($("#hdd_id_lugar_cita_origen").val());

            var params = "opcion=2&id_admision=" + id_admision +
                    "&id_tipo_reg=" + id_tipo_reg +
                    "&id_lugar_cita=" + $("#hdd_id_lugar_cita_origen").val();

            llamarAjax("../funciones/Class_Atencion_Remision_ajax.php", params, "d_usuario_atencion_remision", "continuar_seleccionar_dest_remision();");
        }
    } else {
        $("#tr_cant_examenes_remision").css("display", "none");
        $("#tr_examenes_remision").css("display", "none");
    }
}

function continuar_seleccionar_dest_remision() {
    if ($("#hdd_mostrar_usuarios_rem").val() == "1") {
        $("#tr_usuario_atencion_remision").css("display", "table-row");
    }
}

function mostrar_examenes_remision(cantidad) {
    cantidad = parseInt(cantidad, 10);
    for (var i = 1; i <= cantidad; i++) {
        $("#tr_examen_remision_" + i).css("display", "table-row");
    }
    for (var i = cantidad + 1; i <= g_cant_max_examenes_rem; i++) {
        $("#tr_examen_remision_" + i).css("display", "none");
    }
    if (cantidad > 0) {
        $("#tr_examen_remision_vacio").css("display", "none");
    } else {
        $("#tr_examen_remision_vacio").css("display", "table-row");
    }
}

function guardar_atencion_remision(id_hc, id_admision, funcion_finalizar, obj_exito) {
    console.log("Hola");
    $("#btn_enviar_remision").attr("disabled", "disabled");
    $("#btn_finalizar_enviar_remision").attr("disabled", "disabled");
    if (validar_guardar_atencion_remision()) {
        $("#tr_examenes_remision_guardando").css("display", "table-row");
        if (funcion_finalizar != "") {
            eval(funcion_finalizar);
            var bol_finalizado = false;
            var cont_intentos = 0;
            var id_intervalo_aux = setInterval(
                    function () {
                        if ($("#" + obj_exito).length) {
                            var exito_aux = parseInt($("#" + obj_exito).val(), 10);
                            if (exito_aux > 0) {
                                continuar_guardar_atencion_remision(id_hc, id_admision);
                            } else {
                                $("#d_contenedor_error_rem").html("Error al tratar de finalizar la consulta, revise que todos los campos obligatorios est&eacute;n diligenciados.");
                                $("#d_contenedor_error_rem").css("display", "block");
                                $("#tr_examenes_remision_guardando").css("display", "none");
                                $("#btn_enviar_remision").removeAttr("disabled");
                                $("#btn_finalizar_enviar_remision").removeAttr("disabled");
                            }
                            bol_finalizado = true;
                            clearInterval(id_intervalo_aux);
                        }

                        cont_intentos++;

                        if (cont_intentos >= 10) {
                            clearInterval(id_intervalo_aux);
                            if (!bol_finalizado) {
                                $("#d_contenedor_error_rem").html("Error interno al tratar de finalizar la consulta");
                                $("#d_contenedor_error_rem").css("display", "block");
                                $("#btn_enviar_remision").removeAttr("disabled");
                                $("#btn_finalizar_enviar_remision").removeAttr("disabled");
                            }
                            $("#tr_examenes_remision_guardando").css("display", "none");
                        }
                    }, 1000);
        } else {
            continuar_guardar_atencion_remision(id_hc, id_admision);
        }
    } else {
        $("#btn_enviar_remision").removeAttr("disabled");
        $("#btn_finalizar_enviar_remision").removeAttr("disabled");
    }
}

function continuar_guardar_atencion_remision(id_hc, id_admision) {
    var arr_aux = $("#cmb_tipo_cita_rem").val().split("-");

    var params = "opcion=1&id_hc=" + id_hc +
            "&id_admision=" + id_admision +
            "&id_tipo_cita=" + $("#hdd_id_tipo_cita_origen").val() +
            "&id_tipo_reg=" + $("#hdd_id_tipo_reg_origen").val() +
            "&id_tipo_cita_rem=" + arr_aux[0] +
            "&id_tipo_reg_rem=" + arr_aux[1] +
            "&id_usuario_rem=" + $("#cmb_usuario_rem").val() +
            "&id_lugar_rem=" + $("#cmb_lugar_remision").val() +
            "&observaciones_remision=" + str_encode($("#txt_observaciones_remision").val());

    if ($("#tr_cant_examenes_remision").is(":visible")) {
        var cant_examenes = parseInt($("#cmb_cant_examenes_remision").val(), 10);
        params += "&cant_examenes=" + cant_examenes;

        for (var i = 1; i <= cant_examenes; i++) {
            params += "&id_examen_" + (i - 1) + "=" + $("#cmb_examen_remision_" + i).val() +
                    "&id_ojo_" + (i - 1) + "=" + $("#cmb_ojo_examen_remision_" + i).val();
        }
    } else {
        params += "&cant_examenes=0";
    }

    llamarAjax("../funciones/Class_Atencion_Remision_ajax.php", params, "d_guardar_atencion_remision", "finalizar_guardar_atencion_remision();");
}

function validar_guardar_atencion_remision() {
    var resultado = true;
    $("#d_contenedor_error_rem").css("display", "none");
    $("#d_contenedor_exito_rem").css("display", "none");

    $("#cmb_tipo_cita_rem").removeClass("bordeAdmision");
    $("#cmb_cant_examenes_remision").removeClass("bordeAdmision");
    $("#cmb_usuario_rem").removeClass("bordeAdmision");
    $("#cmb_lugar_remision").removeClass("bordeAdmision");
    for (var i = 1; i <= g_cant_max_examenes_rem; i++) {
        $("#cmb_examen_remision_" + i).removeClass("bordeAdmision");
        $("#cmb_ojo_examen_remision_" + i).removeClass("bordeAdmision");
    }

    if ($("#cmb_tipo_cita_rem").val() == "-1" || $("#cmb_tipo_cita_rem").val() == "") {
        $("#cmb_tipo_cita_rem").addClass("bordeAdmision");
        resultado = false;
    }
    if ($("#tr_usuario_atencion_remision").is(":visible")) {
        if ($("#cmb_usuario_rem").val() == "-1" || $("#cmb_usuario_rem").val() == "") {
            $("#cmb_usuario_rem").addClass("bordeAdmision");
            resultado = false;
        }
    }
    if ($("#tr_lugar_remision").is(":visible")) {
        if ($("#cmb_lugar_remision").val() == "-1" || $("#cmb_lugar_remision").val() == "") {
            $("#cmb_lugar_remision").addClass("bordeAdmision");
            resultado = false;
        }
    }
    if ($("#tr_cant_examenes_remision").is(":visible")) {
        if ($("#cmb_cant_examenes_remision").val() == "0" || $("#cmb_cant_examenes_remision").val() == "") {
            $("#cmb_cant_examenes_remision").addClass("bordeAdmision");
            resultado = false;
        }

        var cantidad_aux = parseInt($("#cmb_cant_examenes_remision").val(), 10);
        for (var i = 1; i <= cantidad_aux; i++) {
            if ($("#cmb_examen_remision_" + i).val() == "-1" || $("#cmb_examen_remision_" + i).val() == "") {
                $("#cmb_examen_remision_" + i).addClass("bordeAdmision");
                resultado = false;
            }
            if ($("#cmb_ojo_examen_remision_" + i).val() == "-1" || $("#cmb_ojo_examen_remision_" + i).val() == "") {
                $("#cmb_ojo_examen_remision_" + i).addClass("bordeAdmision");
                resultado = false;
            }
        }
    }

    if (!resultado) {
        $("#d_contenedor_error_rem").html("Los campos marcados en rojo son obligatorios");
        $("#d_contenedor_error_rem").css("display", "block");
    }

    return resultado;
}

function finalizar_guardar_atencion_remision() {
    $("#tr_examenes_remision_guardando").css("display", "none");

    var resultado_aux = 0;
    if ($("#hdd_resultado_admision_remision").length) {
        resultado_aux = parseInt($("#hdd_resultado_admision_remision").val(), 10);
    }

    if (resultado_aux > 0) {
        $("#d_contenedor_exito_rem").html("Movimiento de paciente registrado con exito.");
        $("#d_contenedor_exito_rem").css("display", "block");
        var url_menu = $("#hdd_url_menu_admision_remision").val();
        setTimeout("enviar_credencial('" + url_menu + "')", 2000);
    } else if (resultado_aux == -3) {
        $("#d_contenedor_error_rem").html("Error - No se encontr&oacute; profesional disponible para la atenci&oacute;n.");
        $("#d_contenedor_error_rem").css("display", "block");
        $("#btn_enviar_remision").removeAttr("disabled");
        $("#btn_finalizar_enviar_remision").removeAttr("disabled");
    } else {
        $("#d_contenedor_error_rem").html("Error al registrar el movimiento del paciente.");
        $("#d_contenedor_error_rem").css("display", "block");
        $("#btn_enviar_remision").removeAttr("disabled");
        $("#btn_finalizar_enviar_remision").removeAttr("disabled");
    }
}

