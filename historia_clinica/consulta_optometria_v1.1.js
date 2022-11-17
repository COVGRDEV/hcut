/***********************************************/
/*Configuración del editor de texto enriquecido*/
/***********************************************/

$(document).ready(function () {

    $("#cmb_examinado_antes").trigger("change");

    //$("#rx_gafas").trigger("click"); //ojo: lo chequea!
    config_seccion_correccion_optica("gafas");
    config_seccion_correccion_optica("ldc");
    config_seccion_correccion_optica("abv");
    config_seccion_correccion_optica("cxr");
});

if (CKEDITOR.env.ie && CKEDITOR.env.version < 9) {
    CKEDITOR.tools.enableHtml5Elements(document);
}

CKEDITOR.config.height = 70;
CKEDITOR.config.width = "auto";

var initCKEditorOptom = (function (id_obj) {
    var wysiwygareaAvailable = isWysiwygareaAvailable(),
            isBBCodeBuiltIn = !!CKEDITOR.plugins.get("bbcode");

    return function (id_obj) {
        var editorElement = CKEDITOR.document.getById(id_obj);

        //Dependiendo de la disponibilidad del plugin wysiwygare se incia el editor clásico o el editor en línea.
        if (wysiwygareaAvailable) {
            CKEDITOR.replace(id_obj);
        } else {
            editorElement.setAttribute("contenteditable", "true");
            CKEDITOR.inline(id_obj);
        }
    };

    function isWysiwygareaAvailable() {
        if (CKEDITOR.revision == ("%RE" + "V%")) {
            return true;
        }

        return !!CKEDITOR.plugins.get("wysiwygarea");
    }
})();

/***********************************************/
/***********************************************/
/***********************************************/

function config_autocomplete(cod_grupo) {
    switch (cod_grupo) {
        case 4: //frm_gafas
            //Para lensometria y prisma 		
            $("[id^='lenso_esfera_o']").autocomplete({source: Tags_esfera});
            $("[id^='lenso_cilindro_o']").autocomplete({source: Tags_cilindro});
            $("[id^='lenso_adicion_o']").autocomplete({source: Tags_adicion});
            $("[id^='prisma_potencia_o']").autocomplete({source: Tags_prisma_potencia});
            break;
    }
}

var arr_textarea_ids = [];
function ajustar_textareas() {
    for (i = 0; i < arr_textarea_ids.length; i++) {
        $("#" + arr_textarea_ids[i]).trigger("input");
    }
}

var g_querato_np = "NP/NA";

function mostrar_formulario_flotante(tipo) {
    if (tipo == 1) { //mostrar
        $("#fondo_negro").css("display", "block");
        $("#d_centro").slideDown(400).css("display", "block");
    } else if (tipo == 0) { //Ocultar
        $("#fondo_negro").css("display", "none");
        $("#d_centro").slideDown(400).css("display", "none");
    }
}


function mostrar_div(id_div) {
    $("#" + id_div).css("display", "block");
}


function reducir_formulario_flotante(ancho, alto) { //480, 390
    $(".div_centro").width(ancho);
    $(".div_centro").height(alto);
    $(".div_centro").css("top", "20%");
    $(".div_interno").width(ancho - 15);
    $(".div_interno").height(alto - 35);
}

function cambiar_mas(text) {
    var resultado = text.replace("+", "|mas");
    return resultado;
}

function calcular_diferencia(val1, val2, eje1, eje2, resul) {
    if ($("#" + val1).val() == g_querato_np) {
        $("#" + eje1).val("");
        $("#" + eje1).attr("disabled", "disabled");
        $("#" + resul).val(g_querato_np);
    } else if ($("#" + val2).val() == g_querato_np) {
        $("#" + eje2).val("");
        $("#" + eje2).attr("disabled", "disabled");
        $("#" + resul).val(g_querato_np);
    } else {
        $("#" + eje1).removeAttr("disabled");
        $("#" + eje2).removeAttr("disabled");

        var numero1 = $("#" + val1).val().replace(",", ".");
        var numero2 = $("#" + val2).val().replace(",", ".");
        var valor1 = parseFloat(numero1);
        var valor2 = parseFloat(numero2);
        if ($("#" + val1).val() != "" && $("#" + val2).val() != "") {
            var resultado = valor1 - valor2;
            var num_resul = parseFloat(resultado);
            if (!isNaN(num_resul)) {
                $("#" + resul).val(num_resul);
                var text_resul = $("#" + resul).val();
                text_resul = text_resul.replace(".", ",");
                $("#" + resul).val(text_resul);
            }
        }

        if ($("#" + val1).val() == "" || $("#" + val2).val() == "") {
            $("#" + resul).val("");
        }
    }
}

function mostrar_formulario(tipo) {
    if (tipo == 1) {//mostrar
        $(".formulario").slideDown(600).css("display", "block")
    } else if (tipo == 0) {//Ocultar
        $(".formulario").slideUp(600).css("display", "none")
    }
}

function llamar_crear_usuarios() {
    var params = "opcion=1";
    llamarAjax("consulta_optometria_ajax.php", params, "principal_optometria", "mostrar_formulario(1)");
}


/**
 * Validar los campos de optometria
 */
function validar_optometria() {
    var result = 0;

    $("#cke_txt_anamnesis").removeClass("borde_error");

    $("#avsc_lejos_od").removeClass("borde_error");
    $("#avsc_cerca_od").removeClass("borde_error");
    $("#avsc_lejos_oi").removeClass("borde_error");
    $("#avsc_cerca_oi").removeClass("borde_error");

    var canti_gafas = parseInt($("#hdd_canti_gafas").val(), 10);
    for (var i = 1; i <= canti_gafas; i++) {
        $("#lenso_esfera_od_" + i).removeClass("borde_error");
        $("#lenso_cilindro_od_" + i).removeClass("borde_error");
        $("#lenso_eje_od_" + i).removeClass("borde_error");
        $("#lenso_lejos_od_" + i).removeClass("borde_error");
        $("#lenso_cerca_od_" + i).removeClass("borde_error");
        $("#lenso_esfera_oi_" + i).removeClass("borde_error");
        $("#lenso_cilindro_oi_" + i).removeClass("borde_error");
        $("#lenso_eje_oi_" + i).removeClass("borde_error");
        $("#lenso_lejos_oi_" + i).removeClass("borde_error");
        $("#lenso_cerca_oi_" + i).removeClass("borde_error");
    }

    $("#querato_k1_od").removeClass("borde_error");
    $("#querato_ejek1_od").removeClass("borde_error");
    $("#querato_dif_od").removeClass("borde_error");
    $("#querato_k1_oi").removeClass("borde_error");
    $("#querato_ejek1_oi").removeClass("borde_error");
    $("#querato_dif_oi").removeClass("borde_error");

    $("#subjetivo_esfera_od").removeClass("borde_error");
    $("#subjetivo_lejos_od").removeClass("borde_error");
    $("#subjetivo_cerca_od").removeClass("borde_error");
    $("#subjetivo_esfera_oi").removeClass("borde_error");
    $("#subjetivo_lejos_oi").removeClass("borde_error");
    $("#subjetivo_cerca_oi").removeClass("borde_error");

    $("#refrafinal_esfera_od").removeClass("borde_error");
    $("#refrafinal_esfera_oi").removeClass("borde_error");

    $("#txt_nombre_usuario_alt").removeClass("borde_error");

    $("#txt_tipo_lente").removeClass("borde_error");

    if (CKEDITOR.instances.txt_anamnesis.getData() == "") {
        $("#cke_txt_anamnesis").addClass("borde_error");
        result = 1;
    }

    if ($("#avsc_lejos_od").val() == "" && $("#avsc_lejos_od").attr("disabled") != "disabled") {
        $("#avsc_lejos_od").addClass("borde_error");
        result = 1;
    }
    if ($("#avsc_cerca_od").val() == "" && $("#avsc_cerca_od").attr("disabled") != "disabled") {
        $("#avsc_cerca_od").addClass("borde_error");
        result = 1;
    }
    if ($("#avsc_lejos_oi").val() == "" && $("#avsc_lejos_oi").attr("disabled") != "disabled") {
        $("#avsc_lejos_oi").addClass("borde_error");
        result = 1;
    }
    if ($("#avsc_cerca_oi").val() == "" && $("#avsc_cerca_oi").attr("disabled") != "disabled") {
        $("#avsc_cerca_oi").addClass("borde_error");
        result = 1;
    }

    //Lensometría
    if ($("#cmb_examinado_antes").val() == "1" && $("#cmb_usa_correccion").val() == "1" && $("#rx_gafas").is(":checked")) {
        for (var i = 1; i <= canti_gafas; i++) {
            if (!$("#chk_presentes_gafas_" + i).is(":checked")) {
                if ($("#lenso_esfera_od_" + i).val() == "") {
                    $("#lenso_esfera_od_" + i).addClass("borde_error");
                    result = 1;
                }
                if ($("#lenso_eje_od_" + i).val() == "") {
                    $("#lenso_eje_od_" + i).addClass("borde_error");
                    result = 1;
                }
                if ($("#lenso_lejos_od_" + i).val() == "") {
                    $("#lenso_lejos_od_" + i).addClass("borde_error");
                    result = 1;
                }
                if ($("#lenso_cerca_od_" + i).val() == "") {
                    $("#lenso_cerca_od_" + i).addClass("borde_error");
                    result = 1;
                }

                if ($("#lenso_esfera_oi_" + i).val() == "") {
                    $("#lenso_esfera_oi_" + i).addClass("borde_error");
                    result = 1;
                }
                if ($("#lenso_eje_oi_" + i).val() == "") {
                    $("#lenso_eje_oi_" + i).addClass("borde_error");
                    result = 1;
                }
                if ($("#lenso_lejos_oi_" + i).val() == "") {
                    $("#lenso_lejos_oi_" + i).addClass("borde_error");
                    result = 1;
                }
                if ($("#lenso_cerca_oi_" + i).val() == "") {
                    $("#lenso_cerca_oi_" + i).addClass("borde_error");
                    result = 1;
                }
            }
        }
    }

    if ($("#querato_k1_od").val() == "" && $("#querato_k1_od").attr("disabled") != "disabled") {
        $("#querato_k1_od").addClass("borde_error");
        result = 1;
    }
    if ($("#querato_ejek1_od").val() == "" && $("#querato_ejek1_od").attr("disabled") != "disabled" && $("#querato_k1_od").val() != g_querato_np) {
        $("#querato_ejek1_od").addClass("borde_error");
        result = 1;
    }
    if ($("#querato_dif_od").val() == "" && $("#querato_dif_od").attr("disabled") != "disabled") {
        $("#querato_dif_od").addClass("borde_error");
        result = 1;
    }
    if ($("#querato_k1_oi").val() == "" && $("#querato_k1_oi").attr("disabled") != "disabled") {
        $("#querato_k1_oi").addClass("borde_error");
        result = 1;
    }
    if ($("#querato_ejek1_oi").val() == "" && $("#querato_ejek1_oi").attr("disabled") != "disabled" && $("#querato_k1_oi").val() != g_querato_np) {
        $("#querato_ejek1_oi").addClass("borde_error");
        result = 1;
    }
    if ($("#querato_dif_oi").val() == "" && $("#querato_dif_oi").attr("disabled") != "disabled") {
        $("#querato_dif_oi").addClass("borde_error");
        result = 1;
    }

    if ($("#subjetivo_esfera_od").val() == "" && $("#subjetivo_esfera_od").attr("disabled") != "disabled") {
        $("#subjetivo_esfera_od").addClass("borde_error");
        result = 1;
    }
    if ($("#subjetivo_lejos_od").val() == "" && $("#subjetivo_lejos_od").attr("disabled") != "disabled") {
        $("#subjetivo_lejos_od").addClass("borde_error");
        result = 1;
    }
    if ($("#subjetivo_cerca_od").val() == "" && $("#subjetivo_cerca_od").attr("disabled") != "disabled") {
        $("#subjetivo_cerca_od").addClass("borde_error");
        result = 1;
    }
    if ($("#subjetivo_esfera_oi").val() == "" && $("#subjetivo_esfera_oi").attr("disabled") != "disabled") {
        $("#subjetivo_esfera_oi").addClass("borde_error");
        result = 1;
    }
    if ($("#subjetivo_lejos_oi").val() == "" && $("#subjetivo_lejos_oi").attr("disabled") != "disabled") {
        $("#subjetivo_lejos_oi").addClass("borde_error");
        result = 1;
    }
    if ($("#subjetivo_cerca_oi").val() == "" && $("#subjetivo_cerca_oi").attr("disabled") != "disabled") {
        $("#subjetivo_cerca_oi").addClass("borde_error");
        result = 1;
    }

    if ($("#refrafinal_esfera_od").val() == "" && $("#refrafinal_esfera_od").attr("disabled") != "disabled") {
        $("#refrafinal_esfera_od").addClass("borde_error");
        result = 1;
    }
    if ($("#refrafinal_esfera_oi").val() == "" && $("#refrafinal_esfera_oi").attr("disabled") != "disabled") {
        $("#refrafinal_esfera_oi").addClass("borde_error");
        result = 1;
    }

    if ($("#hdd_usuario_anonimo").val() == "1" && $("#txt_nombre_usuario_alt").val() == "") {
        $("#txt_nombre_usuario_alt").addClass("borde_error");
        result = 1;
    }

    if ($("#txt_tipo_lente").val() == "") {
        $("#txt_tipo_lente").addClass("borde_error");
        result = 1;
    }
	
	if ($("#cmb_tipo_lente").val() == "" && $("#cmb_tipo_lente").attr("disabled") != "disabled") {
		$("#cmb_tipo_lente").addClass("borde_error");
		result = 1;
	}
	
	if ($("#cmb_tipo_filtro").val() == "" && $("#cmb_tipo_filtro").attr("disabled") != "disabled") {
		$("#cmb_tipo_filtro").addClass("borde_error");
		result = 1;
	}
	
	if ($("#cmb_tiempo_vigencia").val() == "" && $("#cmb_tiempo_vigencia").attr("disabled") != "disabled") {
		$("#cmb_tiempo_vigencia").addClass("borde_error");
		result = 1;
	}	
	
	if ($("cmb_timepo_periodo").val() == "" && $("#cmb_timepo_periodo").attr("disabled") != "disabled") {
		$("cmb_timepo_periodo").addClass("borde_error");
		result = 1;
	}
	if ($("txt_distancia_pupilar").val() ==""){
		$("txt_distancia_pupilar").addClass("borde_error");
		result = 1;
	}
	if ($("txt_cantidad").val() ==""){
		$("txt_cantidad").addClass("borde_error");
		result = 1;
	}

    //Validación de diagnósticos
    var result_ciex = validar_diagnosticos_hc(0);
    if (result_ciex < 0) {
        result = result_ciex;
    }

    return result;

}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta SIN VALIDAR LOS CAMPOS
 */
function validar_crear_optometria(tipo, ind_imprimir) {
    switch (tipo) {
        case 1: //Finalizar consulta
        case 3: //Guardar desde la historia
        case 4: //Finalizar consulta desde traslado
            $("#btn_imprimir").attr("disabled", "disabled");
            $("#btn_crear").attr("disabled", "disabled");
            $("#btn_finalizar").attr("disabled", "disabled");

            $("#contenedor_error").css("display", "none");
            var cmb_validar_consulta = $("#cmb_validar_consulta").val();
            if (cmb_validar_consulta == "1") {
                var resultado = validar_optometria();
                if (resultado == 0) {
                    editar_consulta_optometria(tipo, ind_imprimir);
                    return false;
                } else {
                    $("#contenedor_error").css("display", "block");
                    if (resultado == -2) {
                        $("#contenedor_error").html("Error - existen diagn&oacute;sticos duplicados");
                    } else {
                        $("#contenedor_error").html("Los campos marcados en rojo son obligatorios");
                    }
                    window.scroll(0, 0);
                    $("#btn_imprimir").removeAttr("disabled");
                    $("#btn_crear").removeAttr("disabled");
                    $("#btn_finalizar").removeAttr("disabled");
                    return false;
                }
            } else {
                //Se validan duplicados de diagnósticos
                if (validar_duplicados_diagnosticos_hc() != -2) {
                    editar_consulta_optometria(tipo, ind_imprimir);
                } else {
                    $("#contenedor_error").css("display", "block");
                    $("#contenedor_error").html("Error - existen diagn&oacute;sticos duplicados");
                    window.scroll(0, 0);
                    $("#btn_imprimir").removeAttr("disabled");
                    $("#btn_crear").removeAttr("disabled");
                    $("#btn_finalizar").removeAttr("disabled");
                }
            }
            break;

        case 2: //Guardar cambios
            //Se validan duplicados de diagnósticos
            if (validar_duplicados_diagnosticos_hc() != -2) {
                editar_consulta_optometria(tipo, ind_imprimir);
            } else {
                $("#contenedor_error").css("display", "block");
                $("#contenedor_error").html("Error - existen diagn&oacute;sticos duplicados");
                window.scroll(0, 0);
            }
            break;
    }
}

function imprimir_optometria() {
    var params = "id_hc=" + $("#hdd_id_hc_consulta").val();

    llamarAjax("../historia_clinica/impresion_historia_clinica.php", params, "d_impresion_hc", "continuar_imprimir_optometria();");
}

function continuar_imprimir_optometria() {
    var ruta = $("#hdd_ruta_arch_hc_pdf").val();
    window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=consulta_optometria.pdf", "_blank");
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta 
 */

function editar_consulta_optometria(tipo, ind_imprimir) {
    $("#btn_imprimir").attr("disabled", "disabled");
    $("#btn_crear").attr("disabled", "disabled");
    $("#btn_finalizar").attr("disabled", "disabled");

    var hdd_id_hc_consulta = $("#hdd_id_hc_consulta").val();
    var hdd_id_admision = $("#hdd_id_admision").val();
    var txt_anamnesis = str_encode(CKEDITOR.instances.txt_anamnesis.getData());

    var avsc_lejos_od = $("#avsc_lejos_od").val();
    var avsc_media_od = $("#avsc_media_od").val();
    var avsc_cerca_od = $("#avsc_cerca_od").val();
    var avsc_lejos_oi = $("#avsc_lejos_oi").val();
    var avsc_media_oi = $("#avsc_media_oi").val();
    var avsc_cerca_oi = $("#avsc_cerca_oi").val();

    var querato_k1_od = $("#querato_k1_od").val();
    var querato_ejek1_od = $("#querato_ejek1_od").val();
    var querato_dif_od = $("#querato_dif_od").val();
    var querato_k1_oi = $("#querato_k1_oi").val();
    var querato_ejek1_oi = $("#querato_ejek1_oi").val();
    var querato_dif_oi = $("#querato_dif_oi").val();

    var refraobj_esfera_od = cambiar_mas($("#refraobj_esfera_od").val());
    var refraobj_cilindro_od = $("#refraobj_cilindro_od").val();
    var refraobj_eje_od = $("#refraobj_eje_od").val();
    var refraobj_lejos_od = $("#refraobj_lejos_od").val();
    var refraobj_esfera_oi = cambiar_mas($("#refraobj_esfera_oi").val());
    var refraobj_cilindro_oi = $("#refraobj_cilindro_oi").val();
    var refraobj_eje_oi = $("#refraobj_eje_oi").val();
    var refraobj_lejos_oi = $("#refraobj_lejos_oi").val();

    var subjetivo_esfera_od = cambiar_mas($("#subjetivo_esfera_od").val());
    var subjetivo_cilindro_od = $("#subjetivo_cilindro_od").val();
    var subjetivo_eje_od = $("#subjetivo_eje_od").val();
    var subjetivo_lejos_od = $("#subjetivo_lejos_od").val();
    var subjetivo_media_od = $("#subjetivo_media_od").val();
    var subjetivo_ph_od = $("#subjetivo_ph_od").val();
    var subjetivo_adicion_od = $("#subjetivo_adicion_od").val();
    var subjetivo_cerca_od = $("#subjetivo_cerca_od").val();
    var subjetivo_esfera_oi = cambiar_mas($("#subjetivo_esfera_oi").val());
    var subjetivo_cilindro_oi = $("#subjetivo_cilindro_oi").val();
    var subjetivo_eje_oi = $("#subjetivo_eje_oi").val();
    var subjetivo_lejos_oi = $("#subjetivo_lejos_oi").val();
    var subjetivo_media_oi = $("#subjetivo_media_oi").val();
    var subjetivo_ph_oi = $("#subjetivo_ph_oi").val();
    var subjetivo_adicion_oi = $("#subjetivo_adicion_oi").val();
    var subjetivo_cerca_oi = $("#subjetivo_cerca_oi").val();

    var cicloplejio_esfera_od = cambiar_mas($("#cicloplejio_esfera_od").val());
    var cicloplejio_cilindro_od = $("#cicloplejio_cilindro_od").val();
    var cicloplejio_eje_od = $("#cicloplejio_eje_od").val();
    var cicloplejio_lejos_od = $("#cicloplejio_lejos_od").val();
    var cicloplejio_esfera_oi = cambiar_mas($("#cicloplejio_esfera_oi").val());
    var cicloplejio_cilindro_oi = $("#cicloplejio_cilindro_oi").val();
    var cicloplejio_eje_oi = $("#cicloplejio_eje_oi").val();
    var cicloplejio_lejos_oi = $("#cicloplejio_lejos_oi").val();

    var refrafinal_esfera_od = cambiar_mas($("#refrafinal_esfera_od").val());
    var refrafinal_cilindro_od = $("#refrafinal_cilindro_od").val();
    var refrafinal_eje_od = $("#refrafinal_eje_od").val();
    var refrafinal_adicion_od = $("#refrafinal_adicion_od").val();
    var refrafinal_esfera_oi = cambiar_mas($("#refrafinal_esfera_oi").val());
    var refrafinal_cilindro_oi = $("#refrafinal_cilindro_oi").val();
    var refrafinal_eje_oi = $("#refrafinal_eje_oi").val();
    var refrafinal_adicion_oi = $("#refrafinal_adicion_oi").val();

    //var presion_intraocular_od = $("#presion_intraocular_od").val();
    //var presion_intraocular_oi = $("#presion_intraocular_oi").val();

    var diagnostico_optometria = str_encode(CKEDITOR.instances.txt_diagnostico_optometria.getData());
    var txt_observaciones_subjetivo = str_encode(CKEDITOR.instances.txt_observaciones_subjetivo.getData());
    var txt_observaciones_optometria = str_encode(CKEDITOR.instances.txt_observaciones_optometria.getData());
    var txt_observaciones_rxfinal = str_encode(CKEDITOR.instances.txt_observaciones_rxfinal.getData());

    var cmb_validar_consulta = str_encode($("#cmb_validar_consulta").val());

    var nombre_usuario_alt = str_encode($("#txt_nombre_usuario_alt").val());
    var tipo_lente = str_encode($("#txt_tipo_lente").val());
	
	var tipos_letnes_slct = $("#cmb_tipo_lente").val();
	var tipo_filtro_slct = $("#cmb_tipo_filtro").val();
	var tiempo_vigencia_slct = $("#cmb_tiempo_vigencia").val();
	var tiempo_periodo = str_encode($("#cmb_timepo_periodo").val());
	var distancia_pupilar = str_encode($("#txt_distancia_pupilar").val());
	var form_cantidad = str_encode($("#txt_cantidad").val());

    var cmb_examinado_antes = str_encode($("#cmb_examinado_antes").val());
    if ($("#rx_gafas").is(":checked") && $("#cmb_usa_correccion").val() == "1") {
        var rx_gafas = 1;
    } else {
        var rx_gafas = 0;
    }
    if ($("#rx_ldc").is(":checked") && $("#cmb_usa_correccion").val() == "1") {
        var rx_ldc = 1;
    } else {
        var rx_ldc = 0;
    }
    if ($("#rx_abv").is(":checked") && $("#cmb_usa_correccion").val() == "1") {
        var rx_abv = 1;
    } else {
        var rx_abv = 0;
    }
    if ($("#rx_cxr").is(":checked") && $("#cmb_usa_correccion").val() == "1") {
        var rx_cxr = 1;
    } else {
        var rx_cxr = 0;
    }

    //var cmb_paciente_dilatado = str_encode($("#cmb_paciente_dilatado").val());

    var observaciones_optometricas_finales = "";

    var cmb_dominancia_ocular = str_encode($("#cmb_dominancia_ocular").val());

    //Para Diagnosticos
    var cant_ciex = $("#lista_tabla").val()
    var params = "opcion=1&cant_ciex=" + cant_ciex;
    for (i = 1; i <= cant_ciex; i++) {
        var cod_ciex = $("#hdd_ciex_diagnostico_" + i).val();
        var val_ojos = $("#valor_ojos_" + i).val();
        if (cod_ciex != "") {
            params += "&cod_ciex_" + i + "=" + cod_ciex + "&val_ojos_" + i + "=" + val_ojos;
        }
    }

    //Para antecedentes
    params += obtener_parametros_antecedentes();

    //Para detalles de gafas
    var canti;
    if ($("#rx_gafas").is(":checked") && $("#cmb_usa_correccion").val() == "1") {
        canti = $("#hdd_canti_gafas").val();
    } else {
        canti = 0;
    }
    params += "&hdd_canti_gafas=" + canti;
    for (i = 1; i <= canti; i++) {

        cmb_tipo_gafas = $("#cmb_tipo_gafas_" + i).val();
        if ($("#chk_presentes_gafas_" + i).is(":checked")) {
            var ind_presentes_gafas = 0;
        } else {
            var ind_presentes_gafas = 1;
        }

        if (cmb_tipo_gafas > 0 || ind_presentes_gafas >= 0) {

            // frm gafas: 
            params += "&ind_presentes_gafas_" + i + "=" + ind_presentes_gafas +
                    "&cmb_tipo_gafas_" + i + "=" + cmb_tipo_gafas +
                    "&cmb_tipo_gafas_det_" + i + "=" + $("#cmb_tipo_gafas_det_" + i).val() +
                    "&otro_tipo_gafas_" + i + "=" + $("#otro_tipo_gafas_" + i).val() +
                    "&cmb_filtro_" + i + "=" + $("#cmb_filtro_" + i).val() +
                    "&txt_tiempo_gafas_" + i + "=" + $("#txt_tiempo_gafas_" + i).val() +
                    "&cmb_tiempo_gafas_" + i + "=" + $("#cmb_tiempo_gafas_" + i).val() +
                    "&cmb_grado_satisfaccion_gafas_" + i + "=" + $("#cmb_grado_satisfaccion_gafas_" + i).val();


            // frm lensometría-prisma: 

            lenso_esfera_od = cambiar_mas($("#lenso_esfera_od_" + i).val());
            lenso_cilindro_od = $("#lenso_cilindro_od_" + i).val();
            lenso_eje_od = $("#lenso_eje_od_" + i).val();
            lenso_adicion_od = $("#lenso_adicion_od_" + i).val();
            lenso_lejos_od = $("#lenso_lejos_od_" + i).val();
            lenso_media_od = $("#lenso_media_od_" + i).val();
            lenso_cerca_od = $("#lenso_cerca_od_" + i).val();
            lenso_esfera_oi = cambiar_mas($("#lenso_esfera_oi_" + i).val());
            lenso_cilindro_oi = $("#lenso_cilindro_oi_" + i).val();
            lenso_eje_oi = $("#lenso_eje_oi_" + i).val();
            lenso_adicion_oi = $("#lenso_adicion_oi_" + i).val();
            lenso_lejos_oi = $("#lenso_lejos_oi_" + i).val();
            lenso_media_oi = $("#lenso_media_oi_" + i).val();
            lenso_cerca_oi = $("#lenso_cerca_oi_" + i).val();

            prisma_tipo_od = $("#cmb_prisma_tipo_od_" + i).val();
            prisma_potencia_od = $("#prisma_potencia_od_" + i).val();
            prisma_base_od = $("#cmb_prisma_base_od_" + i).val();
            prisma_base_otra_od = $("#prisma_base_otra_od_" + i).val();
            prisma_tipo_oi = $("#cmb_prisma_tipo_oi_" + i).val();
            prisma_potencia_oi = $("#prisma_potencia_oi_" + i).val();
            prisma_base_oi = $("#cmb_prisma_base_oi_" + i).val();
            prisma_base_otra_oi = $("#prisma_base_otra_oi_" + i).val();

            params += "&lenso_esfera_od_" + i + "=" + lenso_esfera_od +
                    "&lenso_cilindro_od_" + i + "=" + lenso_cilindro_od +
                    "&lenso_eje_od_" + i + "=" + lenso_eje_od +
                    "&lenso_adicion_od_" + i + "=" + lenso_adicion_od +
                    "&lenso_lejos_od_" + i + "=" + lenso_lejos_od +
                    "&lenso_media_od_" + i + "=" + lenso_media_od +
                    "&lenso_cerca_od_" + i + "=" + lenso_cerca_od +
                    "&lenso_esfera_oi_" + i + "=" + lenso_esfera_oi +
                    "&lenso_cilindro_oi_" + i + "=" + lenso_cilindro_oi +
                    "&lenso_eje_oi_" + i + "=" + lenso_eje_oi +
                    "&lenso_adicion_oi_" + i + "=" + lenso_adicion_oi +
                    "&lenso_lejos_oi_" + i + "=" + lenso_lejos_oi +
                    "&lenso_media_oi_" + i + "=" + lenso_media_oi +
                    "&lenso_cerca_oi_" + i + "=" + lenso_cerca_oi +
                    "&cmb_prisma_tipo_od_" + i + "=" + prisma_tipo_od +
                    "&prisma_potencia_od_" + i + "=" + prisma_potencia_od +
                    "&cmb_prisma_base_od_" + i + "=" + prisma_base_od +
                    "&cmb_prisma_tipo_oi_" + i + "=" + prisma_tipo_oi +
                    "&prisma_potencia_oi_" + i + "=" + prisma_potencia_oi +
                    "&cmb_prisma_base_oi_" + i + "=" + prisma_base_oi;
        }
    }

    //Para detalles de ldc			
    if ($("#rx_ldc").is(":checked") && $("#cmb_usa_correccion").val() == "1") {
        canti = $("#hdd_canti_ldc").val();
    } else {
        canti = 0;
    }
    params += "&hdd_canti_ldc=" + canti;
    for (i = 1; i <= canti; i++) {

        cmb_tipo_ldc = $("#cmb_tipo_ldc_" + i).val();
        if ($("#chk_presentes_ldc_" + i).is(":checked")) {
            var ind_presentes_ldc = 0;
        } else {
            var ind_presentes_ldc = 1;
        }

        if (cmb_tipo_ldc > 0 || ind_presentes_ldc >= 0) {
            params += "&ind_presentes_ldc_" + i + "=" + ind_presentes_ldc +
                    "&tipo_ldc_" + i + "=" + cmb_tipo_ldc +
                    "&tipo_ldc_det_" + i + "=" + $("#cmb_tipo_ldc_det_" + i).val() +
                    "&tipo_ldc_otro_" + i + "=" + $("#otro_tipo_ldc_" + i).val() +
                    "&disenio_ldc_" + i + "=" + $("#cmb_disenioldc_" + i).val() +
                    "&tipoduracion_ldc_" + i + "=" + $("#cmb_tipoduracion_ldc_" + i).val() +
                    "&tipoduracion_det_ldc_" + i + "=" + $("#cmb_tipoduracion_det_ldc_" + i).val() +
                    "&ojo_ldc_" + i + "=" + $("#cmb_ojos_ldc_" + i).val() +
                    "&tiempo_uso_ldc_" + i + "=" + $("#txt_tiempo_uso_ldc_" + i).val() +
                    "&und_tiempo_uso_ldc_" + i + "=" + $("#cmb_tiempo_uso_ldc_" + i).val() +
                    "&tiempo_nouso_ldc_" + i + "=" + $("#txt_tiempo_nouso_ldc_" + i).val() +
                    "&und_tiempo_nouso_ldc_" + i + "=" + $("#cmb_tiempo_nouso_ldc_" + i).val() +
                    "&modalidad_uso_ldc_" + i + "=" + $("#cmb_modalidad_uso_ldc_" + i).val() +
                    "&tiempo_reemplazo_ldc_" + i + "=" + $("#cmb_tiempo_reemplazo_ldc_" + i).val() +
                    "&grado_satisfacc_ldc_" + i + "=" + $("#cmb_grado_satisfaccion_ldc_" + i).val();
        }
    }

    //Para detalles de abv 	
    if ($("#rx_abv").is(":checked") && $("#cmb_usa_correccion").val() == "1") {
        canti = $("#hdd_canti_abv").val();
    } else {
        canti = 0;
    }
    params += "&hdd_canti_abv=" + canti;
    for (i = 1; i <= canti; i++) {

        cmb_tipo_abv = $("#cmb_tipo_abv_" + i).val();
        if ($("#chk_presentes_abv_" + i).is(":checked")) {
            var ind_presentes_abv = 0;
        } else {
            var ind_presentes_abv = 1;
        }

        if (cmb_tipo_abv > 0 || ind_presentes_abv >= 0) {
            params += "&ind_presentes_abv_" + i + "=" + ind_presentes_abv +
                    "&cmb_tipo_abv_" + i + "=" + cmb_tipo_abv +
                    "&cmb_tipo_abv_det_" + i + "=" + $("#cmb_tipo_abv_det_" + i).val() +
                    "&tipo_abv_otro_" + i + "=" + $("#otro_tipo_abv_" + i).val() +
                    "&cmb_grado_satisfaccion_abv_" + i + "=" + $("#cmb_grado_satisfaccion_abv_" + i).val();
        }
    }

    //Para detalles de cxr
    if ($("#rx_cxr").is(":checked") && $("#cmb_usa_correccion").val() == "1") {
        canti = $("#hdd_canti_cxr").val();
    } else {
        canti = 0;
    }
    params += "&hdd_canti_cxr=" + canti;
    for (i = 1; i <= canti; i++) {

        tipo_cxr = parseInt($("#cmb_tipo_cxr_" + i).val());
        if ($("#chk_ajuste_" + i).is(":checked")) {
            var ind_ajuste = 1;
        } else {
            var ind_ajuste = 0;
        }

        if (tipo_cxr > 0) {
            params += "&tipo_cxr_" + i + "=" + tipo_cxr +
                    "&tipo_cxr_det_" + i + "=" + $("#cmb_tipo_cxr_det_" + i).val() +
                    "&tipo_cxr_otro_" + i + "=" + $("#otro_tipo_cxr_" + i).val() +
                    "&ind_ajuste_" + i + "=" + ind_ajuste +
                    "&ojo_" + i + "=" + $("#cmb_ojos_cxr_" + i).val() +
                    "&tiempo_uso_" + i + "=" + $("#txt_tiempo_cxr_" + i).val() +
                    "&und_tiempo_uso_" + i + "=" + $("#cmb_tiempo_cxr_" + i).val() +
                    "&entidad_" + i + "=" + $("#cmb_entidad_cxr_" + i).val() +
                    "&id_usuario_cirujano_" + i + "=" + $("#cmb_cirujano_" + i).val() +
                    "&cirujano_otro_" + i + "=" + $("#txt_otro_cirujano_" + i).val() +
                    "&grado_satisfacc_" + i + "=" + $("#cmb_grado_satisfaccion_cxr_" + i).val();
        }
    }

    //console.log("parametros RX en uso: "+params);	 

    params += "&hdd_id_hc_consulta=" + hdd_id_hc_consulta +
            "&hdd_id_admision=" + hdd_id_admision +
            "&txt_anamnesis=" + txt_anamnesis +
            "&avsc_lejos_od=" + avsc_lejos_od +
            "&avsc_media_od=" + avsc_media_od +
            "&avsc_cerca_od=" + avsc_cerca_od +
            "&avsc_lejos_oi=" + avsc_lejos_oi +
            "&avsc_media_oi=" + avsc_media_oi +
            "&avsc_cerca_oi=" + avsc_cerca_oi +
            "&querato_k1_od=" + querato_k1_od +
            "&querato_ejek1_od=" + querato_ejek1_od +
            "&querato_dif_od=" + querato_dif_od +
            "&querato_k1_oi=" + querato_k1_oi +
            "&querato_ejek1_oi=" + querato_ejek1_oi +
            "&querato_dif_oi=" + querato_dif_oi +
            "&refraobj_esfera_od=" + refraobj_esfera_od +
            "&refraobj_cilindro_od=" + refraobj_cilindro_od +
            "&refraobj_eje_od=" + refraobj_eje_od +
            "&refraobj_lejos_od=" + refraobj_lejos_od +
            "&refraobj_esfera_oi=" + refraobj_esfera_oi +
            "&refraobj_cilindro_oi=" + refraobj_cilindro_oi +
            "&refraobj_eje_oi=" + refraobj_eje_oi +
            "&refraobj_lejos_oi=" + refraobj_lejos_oi +
            "&subjetivo_esfera_od=" + subjetivo_esfera_od +
            "&subjetivo_cilindro_od=" + subjetivo_cilindro_od +
            "&subjetivo_eje_od=" + subjetivo_eje_od +
            "&subjetivo_lejos_od=" + subjetivo_lejos_od +
            "&subjetivo_media_od=" + subjetivo_media_od +
            "&subjetivo_ph_od=" + subjetivo_ph_od +
            "&subjetivo_adicion_od=" + subjetivo_adicion_od +
            "&subjetivo_cerca_od=" + subjetivo_cerca_od +
            "&subjetivo_esfera_oi=" + subjetivo_esfera_oi +
            "&subjetivo_cilindro_oi=" + subjetivo_cilindro_oi +
            "&subjetivo_eje_oi=" + subjetivo_eje_oi +
            "&subjetivo_lejos_oi=" + subjetivo_lejos_oi +
            "&subjetivo_media_oi=" + subjetivo_media_oi +
            "&subjetivo_ph_oi=" + subjetivo_ph_oi +
            "&subjetivo_adicion_oi=" + subjetivo_adicion_oi +
            "&subjetivo_cerca_oi=" + subjetivo_cerca_oi +
            "&cicloplejio_esfera_od=" + cicloplejio_esfera_od +
            "&cicloplejio_cilindro_od=" + cicloplejio_cilindro_od +
            "&cicloplejio_eje_od=" + cicloplejio_eje_od +
            "&cicloplejio_lejos_od=" + cicloplejio_lejos_od +
            "&cicloplejio_esfera_oi=" + cicloplejio_esfera_oi +
            "&cicloplejio_cilindro_oi=" + cicloplejio_cilindro_oi +
            "&cicloplejio_eje_oi=" + cicloplejio_eje_oi +
            "&cicloplejio_lejos_oi=" + cicloplejio_lejos_oi +
            "&refrafinal_esfera_od=" + refrafinal_esfera_od +
            "&refrafinal_cilindro_od=" + refrafinal_cilindro_od +
            "&refrafinal_eje_od=" + refrafinal_eje_od +
            "&refrafinal_adicion_od=" + refrafinal_adicion_od +
            "&refrafinal_esfera_oi=" + refrafinal_esfera_oi +
            "&refrafinal_cilindro_oi=" + refrafinal_cilindro_oi +
            "&refrafinal_eje_oi=" + refrafinal_eje_oi +
            "&refrafinal_adicion_oi=" + refrafinal_adicion_oi +
            "&tipo_guardar=" + tipo +
            //"&presion_intraocular_od=" + presion_intraocular_od +
            //"&presion_intraocular_oi=" + presion_intraocular_oi +
            "&diagnostico_optometria=" + diagnostico_optometria +
            "&txt_observaciones_subjetivo=" + txt_observaciones_subjetivo +
            "&txt_observaciones_optometria=" + txt_observaciones_optometria +
            "&txt_observaciones_rxfinal=" + txt_observaciones_rxfinal +
            "&cmb_validar_consulta=" + cmb_validar_consulta +
            "&cmb_examinado_antes=" + cmb_examinado_antes +
            "&rx_gafas=" + rx_gafas +
            "&rx_ldc=" + rx_ldc +
            "&rx_abv=" + rx_abv +
            "&rx_cxr=" + rx_cxr +
            //"&cmb_paciente_dilatado=" + cmb_paciente_dilatado +
            "&cmb_dominancia_ocular=" + cmb_dominancia_ocular +
            "&nombre_usuario_alt=" + nombre_usuario_alt +
            "&observaciones_optometricas_finales=" + observaciones_optometricas_finales +
            "&observaciones_admision=" + str_encode($("#txt_observaciones_admision").val()) +
            "&tipo_lente=" + tipo_lente +
			
			"&tipos_letnes_slct=" + tipos_letnes_slct +
			"&tipo_filtro_slct=" + tipo_filtro_slct +
			"&tiempo_vigencia_slct=" + tiempo_vigencia_slct +
			"&tiempo_periodo=" + tiempo_periodo +
			"&distancia_pupilar=" + distancia_pupilar +
			"&form_cantidad=" + form_cantidad +
            "&cadena_colores=" + obtener_cadena_colores();
	//alert(params);
    llamarAjax("consulta_optometria_ajax.php", params, "guardar_optometria", "validar_exito(" + ind_imprimir + ")");
}

function validar_exito(ind_imprimir) {
    var hdd_exito = $("#hdd_exito").val();
    var hdd_url_menu = $("#hdd_url_menu").val();
    var hdd_tipo_guardar = $("#hdd_tipo_guardar").val();

    if (hdd_tipo_guardar == 1) { //Cierra el formulario
        if (hdd_exito > 0) {
            $(".formulario").css("display", "none");
            $("#contenedor_exito").css("display", "block");
            $("#contenedor_exito").html("Datos guardados correctamente");
            setTimeout("enviar_credencial('" + hdd_url_menu + "')", 3000);
        } else {
            $("#contenedor_error").css("display", "block");
            $("#contenedor_error").html("Error al guardar la consulta de optometr&iacute;a");
        }
    } else { //Permanece en el formulario
        if (hdd_exito > 0) {
            $("#contenedor_exito").css("display", "block");
            $("#contenedor_exito").html("Datos guardados correctamente");
            setTimeout('$("#contenedor_exito").css("display", "none")', 3000);

            if (ind_imprimir == 1) {
                imprimir_optometria();
            }

            // Reset los formularios cargados por ajax: 
            config_seccion_correccion_optica("gafas", 1);
            config_seccion_correccion_optica("ldc", 1);
            config_seccion_correccion_optica("abv", 1);
            config_seccion_correccion_optica("cxr", 1);
        } else {
            $("#contenedor_error").css("display", "block");
            $("#contenedor_error").html("Error al guardar la consulta de optometr&iacute;a");
        }
    }
    window.scrollTo(0, 0);
    $("#btn_imprimir").removeAttr("disabled");
    $("#btn_crear").removeAttr("disabled");
    $("#btn_finalizar").removeAttr("disabled");
}

/**
 * 
 * @param {Object} ojo
 * ojo=OD
 * ojo=OI
 */
function copiar_objetivo(ojo) {
    if (ojo == "OD") {
        var refraobj_esfera_od = $("#refraobj_esfera_od").val();
        var refraobj_cilindro_od = $("#refraobj_cilindro_od").val();
        var refraobj_eje_od = $("#refraobj_eje_od").val();
        var refraobj_lejos_od = $("#refraobj_lejos_od").val();

        $("#subjetivo_esfera_od").val(refraobj_esfera_od);
        $("#subjetivo_cilindro_od").val(refraobj_cilindro_od);
        $("#subjetivo_eje_od").val(refraobj_eje_od);
        $("#subjetivo_lejos_od").val(refraobj_lejos_od);
    } else if (ojo == "OI") {
        var refraobj_esfera_oi = $("#refraobj_esfera_oi").val();
        var refraobj_cilindro_oi = $("#refraobj_cilindro_oi").val();
        var refraobj_eje_oi = $("#refraobj_eje_oi").val();
        var refraobj_lejos_oi = $("#refraobj_lejos_oi").val();

        $("#subjetivo_esfera_oi").val(refraobj_esfera_oi);
        $("#subjetivo_cilindro_oi").val(refraobj_cilindro_oi);
        $("#subjetivo_eje_oi").val(refraobj_eje_oi);
        $("#subjetivo_lejos_oi").val(refraobj_lejos_oi);
    }
}

/**
 * 
 * @param {Object} ojo
 * ojo=OD
 * ojo=OI
 */
function copiar_subjetivo(ojo) {
    if (ojo == "OD") {
        var subjetivo_esfera_od = $("#subjetivo_esfera_od").val();
        var subjetivo_cilindro_od = $("#subjetivo_cilindro_od").val();
        var subjetivo_eje_od = $("#subjetivo_eje_od").val();
        var subjetivo_adicion_od = $("#subjetivo_adicion_od").val();

        $("#refrafinal_esfera_od").val(subjetivo_esfera_od);
        $("#refrafinal_cilindro_od").val(subjetivo_cilindro_od);
        $("#refrafinal_eje_od").val(subjetivo_eje_od);
        $("#refrafinal_adicion_od").val(subjetivo_adicion_od);
    } else if (ojo == "OI") {
        var subjetivo_esfera_oi = $("#subjetivo_esfera_oi").val();
        var subjetivo_cilindro_oi = $("#subjetivo_cilindro_oi").val();
        var subjetivo_eje_oi = $("#subjetivo_eje_oi").val();
        var subjetivo_adicion_oi = $("#subjetivo_adicion_oi").val();

        $("#refrafinal_esfera_oi").val(subjetivo_esfera_oi);
        $("#refrafinal_cilindro_oi").val(subjetivo_cilindro_oi);
        $("#refrafinal_eje_oi").val(subjetivo_eje_oi);
        $("#refrafinal_adicion_oi").val(subjetivo_adicion_oi);
    }
}

function calculo_ejes(eje1, eje2) {
    var val_eje1 = $("#" + eje1).val();
    var val_eje2 = $("#" + eje2).val();
    var val_eje_resul = (parseInt(val_eje1, 10) + 90) % 180;
    if (!isNaN(val_eje_resul)) {
        $("#" + eje2).val(val_eje_resul);
    }
}

function generar_formula_gafas(observaciones, fecha_hc, nombre_paciente, nombre_profesional, esfera_od, cilindro_od, eje_od, adicion_od, esfera_oi, cilindro_oi, eje_oi, adicion_oi, id_admision, id_hc_consulta) {
    var esfera_od = $("#" + esfera_od).val();
    var cilindro_od = $("#" + cilindro_od).val();
    var eje_od = $("#" + eje_od).val();
    var adicion_od = $("#" + adicion_od).val();

    var esfera_oi = $("#" + esfera_oi).val();
    var cilindro_oi = $("#" + cilindro_oi).val();
    var eje_oi = $("#" + eje_oi).val();
    var adicion_oi = $("#" + adicion_oi).val();

    var observaciones = eval("CKEDITOR.instances." + observaciones + ".getData();");
    var tipo_lente = $("#txt_tipo_lente").val();
	
	var hdd_id_hc_consulta = str_encode($("#hdd_id_hc_consulta").val());
	var tipos_letnes_slct = $("#cmb_tipo_lente").val();
	var tipo_filtro_slct = $("#cmb_tipo_filtro").val();
	var tiempo_vigencia_slct = $("#cmb_tiempo_vigencia").val();
	var tiempo_periodo = str_encode($("#cmb_timepo_periodo").val());
	var distancia_pupilar = str_encode($("#txt_distancia_pupilar").val());
	var form_cantidad = str_encode($("#txt_cantidad").val());
	var tipo_impresion = 1;
    var params = "opcion=1&id_admision=" + id_admision +
			"&hdd_id_hc_consulta=" + str_encode(hdd_id_hc_consulta) +
            "&hdd_nombre_paciente=" + str_encode(nombre_paciente) +
            "&hdd_fecha_admision=" + str_encode(fecha_hc) +
            "&esfera_od=" + str_encode(esfera_od) +
            "&cilindro_od=" + str_encode(cilindro_od) +
            "&eje_od=" + str_encode(eje_od) +
            "&adicion_od=" + str_encode(adicion_od) +
            "&esfera_oi=" + str_encode(esfera_oi) +
            "&cilindro_oi=" + str_encode(cilindro_oi) +
            "&eje_oi=" + str_encode(eje_oi) +
            "&adicion_oi=" + str_encode(adicion_oi) +
            "&observacion=" + str_encode(observaciones) +
            "&hdd_nombre_profesional_optometra=" + str_encode(nombre_profesional) +
            "&tipo_lente=" + str_encode(tipo_lente)+
			"&tipos_letnes_slct=" + str_encode(tipos_letnes_slct)+
			"&tipo_filtro_slct=" + str_encode(tipo_filtro_slct)+
			"&tiempo_vigencia_slct=" + str_encode(tiempo_vigencia_slct)+
			"&tiempo_periodo=" + str_encode(tiempo_periodo)+
			"&distancia_pupilar=" + str_encode(distancia_pupilar)+
			"&form_cantidad=" + str_encode(form_cantidad) + 
			"&tipo_impresion=" +  tipo_impresion;
	
	//alert(params);
    llamarAjax("../historia_clinica/formula_gafas_ajax.php", params, "imprimir_formula", "imprimir_formula_gafas_cont();");
}

function imprimir_formula_gafas_cont() {
    var ruta = $("#hdd_ruta_formula_gafas_pdf").val();
    window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=formula_gafas.pdf", "_blank");
}

function imprSelec(muestra) {
    var ficha = document.getElementById(muestra);
    var ventimp = window.open(" ", "popimpr");
    ventimp.document.write(ficha.innerHTML);
    ventimp.document.close();
    ventimp.print();
}

function enviar_a_estados() {
    validar_crear_optometria(2, 0);

    var params = "opcion=3&id_hc=" + $("#hdd_id_hc_consulta").val() +
            "&id_admision=" + $("#hdd_id_admision").val();

    llamarAjax("consulta_optometria_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1);");
}

function seleccionar_ojo_op(id_ojo) {
    switch (id_ojo) {
        case "79":
            $("#avsc_lejos_od").removeAttr("disabled");
            $("#avsc_media_od").removeAttr("disabled");
            $("#avsc_cerca_od").removeAttr("disabled");
            $("#avsc_lejos_oi").val("");
            $("#avsc_media_oi").val("");
            $("#avsc_cerca_oi").val("");
            $("#avsc_lejos_oi").attr("disabled", "disabled");
            $("#avsc_media_oi").attr("disabled", "disabled");
            $("#avsc_cerca_oi").attr("disabled", "disabled");

            $("#lenso_esfera_od").removeAttr("disabled");
            $("#lenso_cilindro_od").removeAttr("disabled");
            $("#lenso_eje_od").removeAttr("disabled");
            $("#lenso_lejos_od").removeAttr("disabled");
            $("#lenso_adicion_od").removeAttr("disabled");
            $("#lenso_cerca_od").removeAttr("disabled");
            $("#lenso_esfera_oi").val("");
            $("#lenso_cilindro_oi").val("");
            $("#lenso_eje_oi").val("");
            $("#lenso_lejos_oi").val("");
            $("#lenso_adicion_oi").val("");
            $("#lenso_cerca_oi").val("");
            $("#lenso_esfera_oi").attr("disabled", "disabled");
            $("#lenso_cilindro_oi").attr("disabled", "disabled");
            $("#lenso_eje_oi").attr("disabled", "disabled");
            $("#lenso_lejos_oi").attr("disabled", "disabled");
            $("#lenso_adicion_oi").attr("disabled", "disabled");
            $("#lenso_cerca_oi").attr("disabled", "disabled");

            $("#querato_k1_od").removeAttr("disabled");
            $("#querato_ejek1_od").removeAttr("disabled");
            $("#querato_dif_od").removeAttr("disabled");
            $("#querato_k1_oi").val("");
            $("#querato_ejek1_oi").val("");
            $("#querato_dif_oi").val("");
            $("#querato_k1_oi").attr("disabled", "disabled");
            $("#querato_ejek1_oi").attr("disabled", "disabled");
            $("#querato_dif_oi").attr("disabled", "disabled");

            $("#refraobj_esfera_od").removeAttr("disabled");
            $("#refraobj_cilindro_od").removeAttr("disabled");
            $("#refraobj_eje_od").removeAttr("disabled");
            $("#refraobj_lejos_od").removeAttr("disabled");
            $("#refraobj_esfera_oi").val("");
            $("#refraobj_cilindro_oi").val("");
            $("#refraobj_eje_oi").val("");
            $("#refraobj_lejos_oi").val("");
            $("#refraobj_esfera_oi").attr("disabled", "disabled");
            $("#refraobj_cilindro_oi").attr("disabled", "disabled");
            $("#refraobj_eje_oi").attr("disabled", "disabled");
            $("#refraobj_lejos_oi").attr("disabled", "disabled");

            $("#subjetivo_esfera_od").removeAttr("disabled");
            $("#subjetivo_cilindro_od").removeAttr("disabled");
            $("#subjetivo_eje_od").removeAttr("disabled");
            $("#subjetivo_lejos_od").removeAttr("disabled");
            $("#subjetivo_media_od").removeAttr("disabled");
            $("#subjetivo_ph_od").removeAttr("disabled");
            $("#subjetivo_adicion_od").removeAttr("disabled");
            $("#subjetivo_cerca_od").removeAttr("disabled");
            $("#subjetivo_esfera_oi").val("");
            $("#subjetivo_cilindro_oi").val("");
            $("#subjetivo_eje_oi").val("");
            $("#subjetivo_lejos_oi").val("");
            $("#subjetivo_media_oi").val("");
            $("#subjetivo_ph_oi").val("");
            $("#subjetivo_adicion_oi").val("");
            $("#subjetivo_cerca_oi").val("");
            $("#subjetivo_esfera_oi").attr("disabled", "disabled");
            $("#subjetivo_cilindro_oi").attr("disabled", "disabled");
            $("#subjetivo_eje_oi").attr("disabled", "disabled");
            $("#subjetivo_lejos_oi").attr("disabled", "disabled");
            $("#subjetivo_media_oi").attr("disabled", "disabled");
            $("#subjetivo_ph_oi").attr("disabled", "disabled");
            $("#subjetivo_adicion_oi").attr("disabled", "disabled");
            $("#subjetivo_cerca_oi").attr("disabled", "disabled");

            $("#cicloplejio_esfera_od").removeAttr("disabled");
            $("#cicloplejio_cilindro_od").removeAttr("disabled");
            $("#cicloplejio_eje_od").removeAttr("disabled");
            $("#cicloplejio_lejos_od").removeAttr("disabled");
            $("#cicloplejio_esfera_oi").val("");
            $("#cicloplejio_cilindro_oi").val("");
            $("#cicloplejio_eje_oi").val("");
            $("#cicloplejio_lejos_oi").val("");
            $("#cicloplejio_esfera_oi").attr("disabled", "disabled");
            $("#cicloplejio_cilindro_oi").attr("disabled", "disabled");
            $("#cicloplejio_eje_oi").attr("disabled", "disabled");
            $("#cicloplejio_lejos_oi").attr("disabled", "disabled");

            $("#refrafinal_esfera_od").removeAttr("disabled");
            $("#refrafinal_cilindro_od").removeAttr("disabled");
            $("#refrafinal_eje_od").removeAttr("disabled");
            $("#refrafinal_adicion_od").removeAttr("disabled");
            $("#refrafinal_esfera_oi").val("");
            $("#refrafinal_cilindro_oi").val("");
            $("#refrafinal_eje_oi").val("");
            $("#refrafinal_adicion_oi").val("");
            $("#refrafinal_esfera_oi").attr("disabled", "disabled");
            $("#refrafinal_cilindro_oi").attr("disabled", "disabled");
            $("#refrafinal_eje_oi").attr("disabled", "disabled");
            $("#refrafinal_adicion_oi").attr("disabled", "disabled");

            //$("#presion_intraocular_od").removeAttr("disabled");
            //$("#presion_intraocular_oi").val("");
            //$("#presion_intraocular_oi").attr("disabled", "disabled");
            break;

        case "80":
            $("#avsc_lejos_od").val("");
            $("#avsc_media_od").val("");
            $("#avsc_cerca_od").val("");
            $("#avsc_lejos_od").attr("disabled", "disabled");
            $("#avsc_media_od").attr("disabled", "disabled");
            $("#avsc_cerca_od").attr("disabled", "disabled");
            $("#avsc_lejos_oi").removeAttr("disabled");
            $("#avsc_media_oi").removeAttr("disabled");
            $("#avsc_cerca_oi").removeAttr("disabled");

            $("#lenso_esfera_od").val("");
            $("#lenso_cilindro_od").val("");
            $("#lenso_eje_od").val("");
            $("#lenso_lejos_od").val("");
            $("#lenso_adicion_od").val("");
            $("#lenso_cerca_od").val("");
            $("#lenso_esfera_od").attr("disabled", "disabled");
            $("#lenso_cilindro_od").attr("disabled", "disabled");
            $("#lenso_eje_od").attr("disabled", "disabled");
            $("#lenso_lejos_od").attr("disabled", "disabled");
            $("#lenso_adicion_od").attr("disabled", "disabled");
            $("#lenso_cerca_od").attr("disabled", "disabled");
            $("#lenso_esfera_oi").removeAttr("disabled");
            $("#lenso_cilindro_oi").removeAttr("disabled");
            $("#lenso_eje_oi").removeAttr("disabled");
            $("#lenso_lejos_oi").removeAttr("disabled");
            $("#lenso_adicion_oi").removeAttr("disabled");
            $("#lenso_cerca_oi").removeAttr("disabled");

            $("#querato_k1_od").val("");
            $("#querato_ejek1_od").val("");
            $("#querato_dif_od").val("");
            $("#querato_k1_od").attr("disabled", "disabled");
            $("#querato_ejek1_od").attr("disabled", "disabled");
            $("#querato_dif_od").attr("disabled", "disabled");
            $("#querato_k1_oi").removeAttr("disabled");
            $("#querato_ejek1_oi").removeAttr("disabled");
            $("#querato_dif_oi").removeAttr("disabled");

            $("#refraobj_esfera_od").val("");
            $("#refraobj_cilindro_od").val("");
            $("#refraobj_eje_od").val("");
            $("#refraobj_lejos_od").val("");
            $("#refraobj_esfera_od").attr("disabled", "disabled");
            $("#refraobj_cilindro_od").attr("disabled", "disabled");
            $("#refraobj_eje_od").attr("disabled", "disabled");
            $("#refraobj_lejos_od").attr("disabled", "disabled");
            $("#refraobj_esfera_oi").removeAttr("disabled");
            $("#refraobj_cilindro_oi").removeAttr("disabled");
            $("#refraobj_eje_oi").removeAttr("disabled");
            $("#refraobj_lejos_oi").removeAttr("disabled");

            $("#subjetivo_esfera_od").val("");
            $("#subjetivo_cilindro_od").val("");
            $("#subjetivo_eje_od").val("");
            $("#subjetivo_lejos_od").val("");
            $("#subjetivo_media_od").val("");
            $("#subjetivo_ph_od").val("");
            $("#subjetivo_adicion_od").val("");
            $("#subjetivo_cerca_od").val("");
            $("#subjetivo_esfera_od").attr("disabled", "disabled");
            $("#subjetivo_cilindro_od").attr("disabled", "disabled");
            $("#subjetivo_eje_od").attr("disabled", "disabled");
            $("#subjetivo_lejos_od").attr("disabled", "disabled");
            $("#subjetivo_media_od").attr("disabled", "disabled");
            $("#subjetivo_ph_od").attr("disabled", "disabled");
            $("#subjetivo_adicion_od").attr("disabled", "disabled");
            $("#subjetivo_cerca_od").attr("disabled", "disabled");
            $("#subjetivo_esfera_oi").removeAttr("disabled");
            $("#subjetivo_cilindro_oi").removeAttr("disabled");
            $("#subjetivo_eje_oi").removeAttr("disabled");
            $("#subjetivo_lejos_oi").removeAttr("disabled");
            $("#subjetivo_media_oi").removeAttr("disabled");
            $("#subjetivo_ph_oi").removeAttr("disabled");
            $("#subjetivo_adicion_oi").removeAttr("disabled");
            $("#subjetivo_cerca_oi").removeAttr("disabled");

            $("#cicloplejio_esfera_od").val("");
            $("#cicloplejio_cilindro_od").val("");
            $("#cicloplejio_eje_od").val("");
            $("#cicloplejio_lejos_od").val("");
            $("#cicloplejio_esfera_od").attr("disabled", "disabled");
            $("#cicloplejio_cilindro_od").attr("disabled", "disabled");
            $("#cicloplejio_eje_od").attr("disabled", "disabled");
            $("#cicloplejio_lejos_od").attr("disabled", "disabled");
            $("#cicloplejio_esfera_oi").removeAttr("disabled");
            $("#cicloplejio_cilindro_oi").removeAttr("disabled");
            $("#cicloplejio_eje_oi").removeAttr("disabled");
            $("#cicloplejio_lejos_oi").removeAttr("disabled");

            $("#refrafinal_esfera_od").val("");
            $("#refrafinal_cilindro_od").val("");
            $("#refrafinal_eje_od").val("");
            $("#refrafinal_adicion_od").val("");
            $("#refrafinal_esfera_od").attr("disabled", "disabled");
            $("#refrafinal_cilindro_od").attr("disabled", "disabled");
            $("#refrafinal_eje_od").attr("disabled", "disabled");
            $("#refrafinal_adicion_od").attr("disabled", "disabled");
            $("#refrafinal_esfera_oi").removeAttr("disabled");
            $("#refrafinal_cilindro_oi").removeAttr("disabled");
            $("#refrafinal_eje_oi").removeAttr("disabled");
            $("#refrafinal_adicion_oi").removeAttr("disabled");

            //$("#presion_intraocular_od").val("");
            //$("#presion_intraocular_od").attr("disabled", "disabled");
            //$("#presion_intraocular_oi").removeAttr("disabled");
            break;

        case "81":
            $("#avsc_lejos_od").removeAttr("disabled");
            $("#avsc_media_od").removeAttr("disabled");
            $("#avsc_cerca_od").removeAttr("disabled");
            $("#avsc_lejos_oi").removeAttr("disabled");
            $("#avsc_media_oi").removeAttr("disabled");
            $("#avsc_cerca_oi").removeAttr("disabled");

            $("#lenso_esfera_od").removeAttr("disabled");
            $("#lenso_cilindro_od").removeAttr("disabled");
            $("#lenso_eje_od").removeAttr("disabled");
            $("#lenso_lejos_od").removeAttr("disabled");
            $("#lenso_adicion_od").removeAttr("disabled");
            $("#lenso_cerca_od").removeAttr("disabled");
            $("#lenso_esfera_oi").removeAttr("disabled");
            $("#lenso_cilindro_oi").removeAttr("disabled");
            $("#lenso_eje_oi").removeAttr("disabled");
            $("#lenso_lejos_oi").removeAttr("disabled");
            $("#lenso_adicion_oi").removeAttr("disabled");
            $("#lenso_cerca_oi").removeAttr("disabled");

            $("#querato_k1_od").removeAttr("disabled");
            $("#querato_ejek1_od").removeAttr("disabled");
            $("#querato_dif_od").removeAttr("disabled");
            $("#querato_k1_oi").removeAttr("disabled");
            $("#querato_ejek1_oi").removeAttr("disabled");
            $("#querato_dif_oi").removeAttr("disabled");

            $("#refraobj_esfera_od").removeAttr("disabled");
            $("#refraobj_cilindro_od").removeAttr("disabled");
            $("#refraobj_eje_od").removeAttr("disabled");
            $("#refraobj_lejos_od").removeAttr("disabled");
            $("#refraobj_esfera_oi").removeAttr("disabled");
            $("#refraobj_cilindro_oi").removeAttr("disabled");
            $("#refraobj_eje_oi").removeAttr("disabled");
            $("#refraobj_lejos_oi").removeAttr("disabled");

            $("#subjetivo_esfera_od").removeAttr("disabled");
            $("#subjetivo_cilindro_od").removeAttr("disabled");
            $("#subjetivo_eje_od").removeAttr("disabled");
            $("#subjetivo_lejos_od").removeAttr("disabled");
            $("#subjetivo_media_od").removeAttr("disabled");
            $("#subjetivo_ph_od").removeAttr("disabled");
            $("#subjetivo_adicion_od").removeAttr("disabled");
            $("#subjetivo_cerca_od").removeAttr("disabled");
            $("#subjetivo_esfera_oi").removeAttr("disabled");
            $("#subjetivo_cilindro_oi").removeAttr("disabled");
            $("#subjetivo_eje_oi").removeAttr("disabled");
            $("#subjetivo_lejos_oi").removeAttr("disabled");
            $("#subjetivo_media_oi").removeAttr("disabled");
            $("#subjetivo_ph_oi").removeAttr("disabled");
            $("#subjetivo_adicion_oi").removeAttr("disabled");
            $("#subjetivo_cerca_oi").removeAttr("disabled");

            $("#cicloplejio_esfera_od").removeAttr("disabled");
            $("#cicloplejio_cilindro_od").removeAttr("disabled");
            $("#cicloplejio_eje_od").removeAttr("disabled");
            $("#cicloplejio_lejos_od").removeAttr("disabled");
            $("#cicloplejio_esfera_oi").removeAttr("disabled");
            $("#cicloplejio_cilindro_oi").removeAttr("disabled");
            $("#cicloplejio_eje_oi").removeAttr("disabled");
            $("#cicloplejio_lejos_oi").removeAttr("disabled");

            $("#refrafinal_esfera_od").removeAttr("disabled");
            $("#refrafinal_cilindro_od").removeAttr("disabled");
            $("#refrafinal_eje_od").removeAttr("disabled");
            $("#refrafinal_adicion_od").removeAttr("disabled");
            $("#refrafinal_esfera_oi").removeAttr("disabled");
            $("#refrafinal_cilindro_oi").removeAttr("disabled");
            $("#refrafinal_eje_oi").removeAttr("disabled");
            $("#refrafinal_adicion_oi").removeAttr("disabled");

            //$("#presion_intraocular_od").removeAttr("disabled");
            //$("#presion_intraocular_oi").removeAttr("disabled");
            break;
    }
}

function obj_dependientes(opcion) {
    //console.log("en procesar_obj_dependientes "+opcion);		

    switch (opcion) {
        case 1: // obj disparador: ha sido examinado antes?
            if ($("#cmb_examinado_antes").val() == "1") {
                $("#zona_uso_co").show(500);
            } else {
                $("#zona_uso_co").hide(500);
                $("#cmb_usa_correccion").val(""); //para controlar los params del post sólo desde Usa Corrección en adelante				
            }
            $("#cmb_usa_correccion").trigger("change");
            break;

        case 2: // objeto disparador: usa corrección óptica?
            //if ($("#cmb_usa_correccion").val()=="1" && $("#cmb_usa_correccion").is(":visible")==true) {
            if ($("#cmb_usa_correccion").val() == "1" && $("#cmb_examinado_antes").val() == "1") {
                $("#zona_tipo_co").show(500);
                $("#zona_frms_tipo_co").show(500);
                if ($("#rx_gafas").is(":checked")) {
                    $("#seccion_lensometria").show(500);
                }
            } else {
                $("#zona_tipo_co").hide(500);
                $("#zona_frms_tipo_co").hide(500);
                $("#seccion_lensometria").hide(500);
            }
            break;
    }
}

function config_seccion_correccion_optica(sufijo_obj, guardado) {
    if (guardado == 1) { //se guardó el formulario 
        if ($("#cmb_usa_correccion").val() != "1" || $("#rx_" + sufijo_obj).is(":checked") == false) {
            $("#rx_" + sufijo_obj).attr("checked", false);
            $("#div_" + sufijo_obj).empty();
            $("#hdd_canti_" + sufijo_obj).val("0");

            if (sufijo_obj == "gafas") {
                $("#seccion_lensometria").empty();
            }

            obj_dependientes(2);
        }
    }

    if (parseInt($("#hdd_canti_" + sufijo_obj).val()) == 0) {
        $("#btn_add_" + sufijo_obj).trigger("click");
    }

    if ($("#rx_" + sufijo_obj).is(":checked")) {
        $("#seccion_" + sufijo_obj).show("slow");
        if (sufijo_obj == "gafas") {
            $("#seccion_lensometria").show("slow");
        }
    } else {
        $("#seccion_" + sufijo_obj).hide("slow");
        if (sufijo_obj == "gafas") {
            $("#seccion_lensometria").hide("slow");
        }
    }
}

function agregar_frm(sufijo_obj) {
    var cod_objeto = 0;
    var div_destino;
    var contador_frm_objeto = parseInt($("#hdd_canti_" + sufijo_obj).val());

    switch (sufijo_obj) {
        case "gafas":
            // Controlar límite de formularios (por capacidad del campo colorPick programado para Lensometría):
            if (contador_frm_objeto == 5) {
                return;
            }
            cod_objeto = 4;
            div_destino = "div_ajax_gafas";
            break;

        case "ldc":
            cod_objeto = 5;
            div_destino = "div_ajax_ldc";
            break;

        case "abv":
            cod_objeto = 6;
            div_destino = "div_ajax_abv";
            break;

        case "cxr":
            cod_objeto = 7;
            div_destino = "div_ajax_cxr";
            break;
    }

    $("#hdd_canti_" + sufijo_obj).val(contador_frm_objeto + 1);
    contador_frm_objeto = parseInt($("#hdd_canti_" + sufijo_obj).val());

    var params = "opcion=" + cod_objeto + "&nfrm=" + contador_frm_objeto;
    llamarAjax("consulta_optometria_ajax.php", params, div_destino, "fin_agregar_frm(" + contador_frm_objeto + ", " + cod_objeto + ")");
}

function fin_agregar_frm(fila, cod_objeto) {
    var vector;

    switch (cod_objeto) {
        case 4:
            div_origen = "div_ajax_gafas";
            div_destino = "div_gafas";
            break;
        case 5:
            div_origen = "div_ajax_ldc";
            div_destino = "div_ldc";
            break;
        case 6:
            div_origen = "div_ajax_abv";
            div_destino = "div_abv";
            break;
        case 7:
            div_origen = "div_ajax_cxr";
            div_destino = "div_cxr";
            break;
    }

    //Separar código de los frm recibidos: 
    vector = $("#" + div_origen).html().split("|*;|separador|*;|");

    //Eliminar la duplicidad de objetos generada al copiar el código HTML en el contenedor final: 
    $("#" + div_origen).empty();

    //Ubicar en la página los frm recibidos: 
    $("#" + div_destino).append(vector[0]);

    if (div_destino == "div_gafas") {
        //frm lensometría: 
        $("#seccion_lensometria").append(vector[1]);
        config_autocomplete(cod_objeto);
    }
}

function quitar_frm(sufijo_obj) {
    contador_frm_objeto = parseInt($("#hdd_canti_" + sufijo_obj).val());

    // Mantener siempre visible almenos un frm: 
    if (contador_frm_objeto == 1) {
        return;
    }

    $(".frm_" + sufijo_obj + "_" + contador_frm_objeto).remove();
    $("#hdd_canti_" + sufijo_obj).val(parseInt($("#hdd_canti_" + sufijo_obj).val()) - 1);
}

function efecto_capas(sufijo_obj) {
    var obj = $("#div_" + sufijo_obj);

    if (obj.is(":visible")) {
        obj.hide("slow");
        $("#img_ver_" + sufijo_obj).hide();
        $("#img_ocultar_" + sufijo_obj).show();
        $("#div_ver_" + sufijo_obj).css("background-image", 'url("../imagenes/ver_abajo.png")');
    } else {
        obj.show("slow");
        $("#img_ver_" + sufijo_obj).show();
        $("#img_ocultar_" + sufijo_obj).hide();
        $("#div_ver_" + sufijo_obj).css("background-image", 'url("../imagenes/ver_derecha.png")');
    }
}

function capas_prisma(fila) {
    efecto_capas("pris_od" + fila);
    efecto_capas("pris_oi" + fila);
}

function cmb_dependientes(tipo_combo, fila) {
    var valor_padre, valor_detalle, params;

    switch (tipo_combo) {
        case 1: //cmb_tipo_gafas_[i]
            valor_padre = $("#cmb_tipo_gafas_" + fila).val();
            params = "opcion=8&f=" + fila + "&vp=" + valor_padre;
            llamarAjax("consulta_optometria_ajax.php", params, "d_cmb_tgafas_" + fila, "", "");

            if (valor_padre == 218) { //otro				
                $("#otro_tipo_gafas_" + fila).attr("disabled", false);
            } else {
                $("#otro_tipo_gafas_" + fila).val("");
                $("#otro_tipo_gafas_" + fila).attr("disabled", true);
            }
            break;

        case 11: //cmb_tipo_gafas_det_[i]						
            valor_detalle = $("#cmb_tipo_gafas_det_" + fila).val();

            if (valor_detalle == 217) { //otro 
                $("#otro_tipo_gafas_" + fila).attr("disabled", false);
            } else {
                $("#otro_tipo_gafas_" + fila).val("");
                $("#otro_tipo_gafas_" + fila).attr("disabled", true);
            }
            break;

        case 2: //cmb_tipo_ldc_[i]
            valor_padre = $("#cmb_tipo_ldc_" + fila).val();
            params = "opcion=9&f=" + fila + "&vp=" + valor_padre;
            llamarAjax("consulta_optometria_ajax.php", params, "d_cmb_tldc_" + fila, "", "");

            if (valor_padre == 382) { //otro				
                $("#otro_tipo_ldc_" + fila).attr("disabled", false);
            } else {
                $("#otro_tipo_ldc_" + fila).val("");
                $("#otro_tipo_ldc_" + fila).attr("disabled", true);
            }
            break;

        case 3: //cmb_tipo_abv_[i]
            valor_padre = $("#cmb_tipo_abv_" + fila).val();
            params = "opcion=10&f=" + fila + "&vp=" + valor_padre;
            llamarAjax("consulta_optometria_ajax.php", params, "d_cmb_tabv_" + fila, "", "");

            if (valor_padre == 341) { //otro				
                $("#otro_tipo_abv_" + fila).attr("disabled", false);
            } else {
                $("#otro_tipo_abv_" + fila).val("");
                $("#otro_tipo_abv_" + fila).attr("disabled", true);
            }
            break;

        case 33: //cmb_tipo_abv_det_[i]			
            valor_detalle = $("#cmb_tipo_abv_det_" + fila).val();

            if (valor_detalle == 338) { //otro 
                $("#otro_tipo_abv_" + fila).attr("disabled", false);
            } else {
                $("#otro_tipo_abv_" + fila).val("");
                $("#otro_tipo_abv_" + fila).attr("disabled", true);
            }
            break;

        case 4: //cmb_tipo_cxr_[i]
            valor_padre = $("#cmb_tipo_cxr_" + fila).val();
            params = "opcion=11&f=" + fila + "&vp=" + valor_padre;
            llamarAjax("consulta_optometria_ajax.php", params, "d_cmb_tcxr_" + fila, "", "");

            if (valor_padre == 359) { //otro				
                $("#otro_tipo_cxr_" + fila).attr("disabled", false);
            } else {
                $("#otro_tipo_cxr_" + fila).val("");
                $("#otro_tipo_cxr_" + fila).attr("disabled", true);
            }
            break;

        case 44: //cmb_tipo_cxr_det_[i]			
            valor_detalle = $("#cmb_tipo_cxr_det_" + fila).val();

            if (valor_detalle == 347 || valor_detalle == 357) { //otro 
                $("#otro_tipo_cxr_" + fila).attr("disabled", false);
            } else {
                $("#otro_tipo_cxr_" + fila).val("");
                $("#otro_tipo_cxr_" + fila).attr("disabled", true);
            }
            break;

        case 5: //cmb_tipodura_ldc_[i]			
            valor_padre = $("#cmb_tipoduracion_ldc_" + fila).val();
            params = "opcion=12&f=" + fila + "&vp=" + valor_padre;
            llamarAjax("consulta_optometria_ajax.php", params, "d_cmb_duracion_det_" + fila, "", "");
            break;
    }
}

function config_dr(tipo_obj, fila) {
    switch (tipo_obj) {
        case 1: //cmb_entidad[i] 
            if ($("#cmb_entidad_cxr_" + fila).val() == 389) { // FOSCAL
                $("#cmb_cirujano_" + fila).attr("disabled", false);
                $("#txt_otro_cirujano_" + fila).attr("disabled", false);
            } else {
                $("#cmb_cirujano_" + fila).val(" ");
                $("#cmb_cirujano_" + fila).attr("disabled", true);
                $("#txt_otro_cirujano_" + fila).attr("disabled", false);
            }
            break;

        case 2: //cmb_doctor[i]
            if ($("#cmb_cirujano_" + fila).val() == 0) { //Otro
                $("#txt_otro_cirujano_" + fila).attr("disabled", false);
            } else {
                $("#txt_otro_cirujano_" + fila).val("");
                $("#txt_otro_cirujano_" + fila).attr("disabled", true);
            }
            break;
    }
}
