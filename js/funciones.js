// JavaScript Document

posicionar_encabezado_hc();
$(window).scroll(function () {
    posicionar_encabezado_hc();
});

function posicionar_encabezado_hc() {
    var altura_del_header = $('.title-bar').outerHeight(true) + $('.topbar').outerHeight(true);
    var altura_del_div = $('#encabezado_hc_principal').outerHeight(true);

    try {
        if ($(window).scrollTop() >= altura_del_header) {
            var position_left = ($(window).width() - 1017) / 2;
            $('#encabezado_hc_principal').addClass('fixed_hc');
            $('#encabezado_hc_principal').css('left', (position_left) + 'px');
            $('#id_contenedor_principal').css('margin-top', (altura_del_div) + 'px');
        } else {
            $('#encabezado_hc_principal').removeClass('fixed_hc');
            $('#encabezado_hc_principal').css('left', '0');
            $('#id_contenedor_principal').css('margin-top', '0');
        }
    } catch (e) {
        $('#encabezado_hc_principal').removeClass('fixed_hc');
        $('#encabezado_hc_principal').css('left', '0');
        $('#id_contenedor_principal').css('margin-top', '0');
    }
}

function isObject(v) {
    return (v != null && typeof (v) == 'object');
}

function solo_numeros(event, decReq) {
    var isIE = document.all ? true : false;
    var key = (isIE) ? window.event.keyCode : event.which;
    var obj = (isIE) ? window.event.srcElement : event.target;
    var isNum = (key > 47 && key < 58) ? true : false;
    var dotOK = (key == 46 && decReq && (obj.value.indexOf(".") < 0 || obj.value.length == 0)) ? true : false;
    if (key != 0 && key != 8) {
        if (isIE) {
            window.event.keyCode = (!isNum && !dotOK) ? 0 : key;
        } else if (!isNum && !dotOK) {
            event.preventDefault();
        }
    }
    return (isNum || dotOK || key == 8 || key == 0);
}

function validarNro(e) {
    var key;
    if (window.event) // IE
    {
        key = e.keyCode;
        if (key == 8) {
            e.keyCode = 9;
            return (e.keyCode);
        }
    } else if (e.which) // Netscape/Firefox/Opera
    {
        key = e.which;
        if (key == 8) {
            e.keyCode = 9;
            return (e.keyCode);
        }
    }
    if (key < 48 || key > 57)
    {
        return false;
    }
    return true;
}

function solo_alfanumericos(event) {
    var isIE = document.all ? true : false;
    var key = (isIE) ? window.event.keyCode : event.which;
    var obj = (isIE) ? window.event.srcElement : event.target;
    var isAlfanum = ((key > 47 && key < 58) || (key > 64 && key < 91) || (key > 96 && key < 123)) ? true : false;
    if (key != 0 && key != 8) {
        if (isIE) {
            window.event.keyCode = (!isAlfanum) ? 0 : key;
        } else if (!isAlfanum) {
            event.preventDefault();
        }
    }
    return (isAlfanum || key == 8 || key == 0);
}

function solo_caracteres(event, cadena_caracteres) {
    var isIE = document.all ? true : false;
    var key = (isIE) ? window.event.keyCode : event.which;
    var obj = (isIE) ? window.event.srcElement : event.target;

    var bol_valido = false;
    for (var i = 0; i < cadena_caracteres.length; i++) {
        if (key == cadena_caracteres.charCodeAt(i)) {
            bol_valido = true;
            break;
        }
    }

    if (key != 0 && key != 8) {
        if (isIE) {
            window.event.keyCode = (!bol_valido) ? 0 : key;
        } else if (!bol_valido) {
            event.preventDefault();
        }
    }
    return (bol_valido || key == 8 || key == 0);
}

function trim(str) {
    return str.replace(/^\s*|\s*$/g, "");
}

function ir_al_inicio() {
    window.open('../index.php', '_parent');
}

function convertir_a_mayusculas(cajaTxt) {
    cajaTxt.value = cajaTxt.value.toUpperCase();
}

function convertir_a_minusculas(cajaTxt) {
    cajaTxt.value = cajaTxt.value.toLowerCase();
}

function convert_mayusc_inicial(caja_texto) {
    var texto_aux = caja_texto.value;
    var texto_rta = "";

    var ind_mayusc = true;
    for (var i = 0; i < texto_aux.length; i++) {
        var char_aux = texto_aux.charAt(i);
        if (ind_mayusc) {
            char_aux = char_aux.toUpperCase();
        } else {
            char_aux = char_aux.toLowerCase();
        }

        texto_rta += char_aux;

        if (char_aux == " " || char_aux == "." || char_aux == "(") {
            ind_mayusc = true;
        } else {
            ind_mayusc = false;
        }
    }

    caja_texto.value = texto_rta;
}

//quitar espacios de una cadena de caracteres 
function quitar_espacios(id_texto) {
    cadena_caracteres = $(id_texto).val();
    var array_cadena = cadena_caracteres.split(" ");
    i = array_cadena.length;
    while (i > 1) {
        cadena_caracteres = cadena_caracteres.replace(" ", "");
        array_cadena = cadena_caracteres.split(" ");
        i = array_cadena.length;
    }
    $(id_texto).val(cadena_caracteres);
}

var g_fecha_act = "";
function obtener_fecha_actual() {
    return g_fecha_act;
}

//Convertir una cadena a mayuscula
function convertirAMayusculas(cajaTxt) {
    $(cajaTxt).val($(cajaTxt).val().toUpperCase());
}

//Convertir una cadena a minuscula
function convertirAMinusculas(cajaTxt) {
    $(cajaTxt).val($(cajaTxt).val().toLowerCase());
}

/******Combinacion para hacer cambio de colores en las tablas******/
//Resaltar fila de una tabla
function resaltar_fila(fila) {
    fila.style.backgroundColor = "#E5E5E5";
}

//Opacar fila de una tabla
function opacar_fila(fila) {
    fila.style.backgroundColor = "#FFF";
}

//unión de ambas funciones ltrim y rtrim
//function trim(id_texto, chars) {
function trim_cadena(id_texto) {
    var str = $(id_texto).val();
    var chars = '';
    var resul = ltrim(rtrim(str, chars), chars);
    $(id_texto).val(resul);
}

//ltrim quita los espacios o caracteres indicados al inicio de la cadena
function ltrim(str, chars) {
    chars = chars || "\\s";
    return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}

//rtrim quita los espacios o caracteres indicados al final de la cadena 
function rtrim(str, chars) {
    chars = chars || "\\s";
    return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}

//Funcion para cerrar el div del centro de ajax
function cerrar_div_centro() {
    $('#fondo_negro').css('display', 'none');
    $('#d_centro').slideDown(400).css('display', 'none');
}

function cerrar_div_centro_adic() {
    $('#fondo_negro_adic').css('display', 'none');
    $('#d_centro_adic').slideDown(400).css('display', 'none');
}

function cerrar_div_centro_extend() {
    $('#fondo_negro_extend').css('display', 'none');
    $('#d_centro_extend').slideDown(400).css('display', 'none');
}

//Funciones del Index
$(document).ready(function () {
    $('#principal_header_div2').hover(function () {
        //animacion icono de usuario
        $('.img2').animate({'background-position-y': '-32'}, 200, 'linear');

        //muestra el div escondido
        $('#principal_header_div3').slideDown(200).css('display', 'block')
    }, function () {
        //animacion icono de usuario
        $('.img2').animate({'background-position-y': '0'}, 200, 'linear');

        //muestra el div escondido
        $('#principal_header_div3').slideUp(200).css('display', 'none')
    })

    /*$('#cerrar_sesion').click(function() {
     confirmar()
     })*/
});
// End Funciones del index

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function enviar_credencial(action, id_menu) {
    $("#frm_credencial").attr("action", action);
    $("#hdd_numero_menu").val(id_menu);
    document.getElementById("frm_credencial").submit();
}

function confirmar() {
    //var r = confirm("\xbfDesea cerrar la sesion?")
    //if (r == true) {
    //cerrar_sesion_ajax();
    url = "../principal/cerrar_sesion.php";
    $(location).attr('href', url);
    //} else {

    //}
}

function confirmar_cerrar_resultados() {
    //var r = confirm("\xbfDesea cerrar la sesion?")
    //if (r == true) {
    //cerrar_sesion_ajax();
    url = "../principal/cerrar_sesion_resultados.php";
    $(location).attr('href', url);
    //} else {

    //}
}

function cerrar_sesion_ajax() {
    $.ajax({
        url: "../principal/cerrar_sesion.php",
        type: "POST",
        data: {id: 1},
        dataType: "html"
    });
    url = "../index.php";
    $(location).attr('href', url);

}

function salir() {
    window.open('../index.php', '_parent');
}

/**
 * Función que procesa la lectura de códigos de barras de las cédulas
 * Tipos:
 * 1 - Solo número de cédula (Buscar paciente)
 * 2 - Datos completos (Admisiones)
 */
var bol_modo_lectura_cod = false;
var texto_lectura_cod = "";
var n1;
function leer_codigo_cedula(event, tipo) {
    var isIE = document.all ? true : false;
    var key = (isIE) ? window.event.keyCode : event.which;
    var obj = (isIE) ? window.event.srcElement : event.target;
    var ind_retorno = true;

    if (key == 58) {
        if (bol_modo_lectura_cod) {
            //Si se encuentra en modo lectura, se finaliza
            bol_modo_lectura_cod = false;
            var arr_datos = texto_lectura_cod.split("|");

            //Se asignan los valores leidos
            switch (tipo) {
                case 1: //Solo número de cédula (Buscar paciente)
                    var cedula_aux = trim(arr_datos[0]);
                    if (cedula_aux.length > 0) {
                        $("#txt_identificacion_interno").val(parseInt(cedula_aux, 10));
                        buscar_paciente_cont();
                    }
                    break;
                case 2: //Datos completos (Admisiones)
                    var cedula_aux = trim(arr_datos[0]);
                    if (cedula_aux.length > 0) {
                        $("#txt_id").val(parseInt(cedula_aux, 10));
                        $('#hdd_evento_pistola').val(arr_datos);
                        verificaEventoPistola();
                    }
                    break;
                default:
                    alert("Modo no soportado.");
                    break;
            }
            //alert(arr_datos[0] + "-" + texto_lectura_cod);
        } else {
            //Se inicia el modo lectura
            bol_modo_lectura_cod = true;
            texto_lectura_cod = "";
        }

        if (isIE) {
            window.event.keyCode = 0;
        } else {
            event.preventDefault();
        }
        ind_retorno = false;
    } else {
        if (bol_modo_lectura_cod) {
            //Se almacenan los caracteres
            if (key != 59) {
                texto_lectura_cod += String.fromCharCode(key);
            } else {
                texto_lectura_cod += "|";
            }

            if (key != 0 && key != 8) {
                if (isIE) {
                    window.event.keyCode = 0;
                } else {
                    event.preventDefault();
                }
            }
            ind_retorno = false;
        } else {
            //No está en modo lectura, se procesan los caracteres de forma normal
            ind_retorno = true;
        }
    }

    return ind_retorno;
}

function mostrar_formulario_flotante(tipo) {
    if (tipo == 1) { //mostrar
        $('#fondo_negro').css('display', 'block');
        $('#d_centro').slideDown(400).css('display', 'block');
    } else if (tipo == 0) { //Ocultar
        $('#fondo_negro').css('display', 'none');
        $('#d_centro').slideDown(400).css('display', 'none');
    }
}

function reducir_formulario_flotante(ancho, alto) {
    $('#d_centro').css("width", ancho + "px");
    $('#d_centro').css("height", alto + "px");
    //$('#d_centro').css('top', '20%');
    $('#d_interno').css("width", (ancho - 15) + "px");
    $('#d_interno').css("height", (alto - 35) + "px");
    $('#d_interno').css('height', 'auto');
}

function mostrar_formulario_flotante_adic(tipo) {
    if (tipo == 1) { //mostrar
        $('#fondo_negro_adic').css('display', 'block');
        $('#d_centro_adic').slideDown(400).css('display', 'block');
    } else if (tipo == 0) { //Ocultar
        $('#fondo_negro_adic').css('display', 'none');
        $('#d_centro_adic').slideDown(400).css('display', 'none');
    }
}

function mostrar_formulario_flotante_extend(tipo) {
    if (tipo == 1) { //mostrar
        $('#fondo_negro_extend').css('display', 'block');
        $('#d_centro_extend').slideDown(400).css('display', 'block');
    } else if (tipo == 0) { //Ocultar
        $('#fondo_negro_extend').css('display', 'none');
        $('#d_centro_extend').slideDown(400).css('display', 'none');
    }
}

//Funcion que centra el div flotante respecto a la pantalla
function posicionarDivFlotante(id) {
    //Centrar el div flotante en la pantalla
    //$("#" + id).css({'top': Math.max(0, (($(window).height() - $("#" + id).outerHeight()) / 2) + $(window).scrollTop()) + "px"});
    //$("#" + id).css({'top': Math.max(0, 100 + $(window).scrollTop()) + "px"});
    $("#" + id).css({'top': "10%"});
}

//Funion que agrega las unidades de mil a un valor númerico
function number_format(number, decimals, dec_point, thousands_sep) {
    // Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

function str_encode(texto) {
    var texto = texto.replace(/\+/g, "|PLUS|");
    var texto = texto.replace(/&/g, "|AMP|");
    var texto = texto.replace(/'/g, "");
    var texto_rta = "";
    for (var i = 0; i < texto.length; i++) {
        if (texto.charCodeAt(i) != 10) {
            texto_rta += texto.charAt(i);
        } else {
            texto_rta += "|ENTER|";
        }
    }
    return texto_rta;
}

function str_decode(texto) {
    var texto = texto.replace(/\|PLUS\|/g, "\+");
    var texto = texto.replace(/\|AMP\|/g, "&");
    var texto = texto.replace(/\|ENTER\|/g, "\n");

    return texto;
}

function ver_hc_panel() {
    $('#caja_flotante').css('width', '380px');
    $('#ocultar_hc_panel').css('display', 'block');
    $('#ver_hc_panel').css('display', 'none');
    $('#detalle_hc').slideDown(400).css('display', 'block');
}

function ocultar_hc_panel() {
    $('#caja_flotante').css('width', '100px');
    $('#ocultar_hc_panel').css('display', 'none');
    $('#ver_hc_panel').css('display', 'block');
    $('#detalle_hc').slideDown(400).css('display', 'none');
}

function mostrar_consultas_div(id_paciente, nombre_paciente, id_admision, pagina_consulta, id_hc, credencial, menu) {
    mostrar_formulario_flotante_extend(1);
    $("#d_interno_extend").empty();
    $('#d_interno_extend').height("99%");

    var HcFrame = document.createElement("iframe");
    HcFrame.id = "HcFrame";
    ruta = pagina_consulta + '?hdd_id_paciente=' + id_paciente +
            '&hdd_nombre_paciente=' + nombre_paciente +
            '&hdd_id_admision=' + id_admision +
            '&hdd_id_hc=' + id_hc +
            '&credencial=' + credencial +
            '&hdd_numero_menu=' + menu + '&tipo_entrada=1';

    HcFrame.setAttribute("src", ruta);
    HcFrame.style.height = '100%';
    HcFrame.style.width = '99%';
    var control = document.getElementById("HcFrame")
    $("#d_interno_extend").append(HcFrame);
}

function mostrar_consulta_iframe(id_paciente, nombre_paciente, id_admision, pagina_consulta, id_hc, credencial, menu, id_div) {
    var HcFrame = document.createElement("iframe");
    HcFrame.id = "HcFrame";
    ruta = pagina_consulta + '?hdd_id_paciente=' + id_paciente +
            '&hdd_nombre_paciente=' + nombre_paciente +
            '&hdd_id_admision=' + id_admision +
            '&hdd_id_hc=' + id_hc +
            '&credencial=' + credencial +
            '&hdd_numero_menu=' + menu + '&tipo_entrada=2';
    HcFrame.setAttribute("src", ruta);
    HcFrame.setAttribute("scrolling", 'no');
    HcFrame.style.height = '100%';
    HcFrame.style.width = '100%';
    HcFrame.style.border = '1px solid #CCC';
    HcFrame.style.margin = '0 px';

    var control = document.getElementById("HcFrame")
    $("#div_consulta_optometria").append(HcFrame);
}



function imprSelec(muestra) {
    var ficha = document.getElementById(muestra);
    var ventimp = window.open(' ', 'popimpr');
    ventimp.document.write(ficha.innerHTML);
    ventimp.document.close();
    ventimp.print();
    ventimp.close();
}

function imprimir_reg_hc(id_hc) {
    var params = "id_hc=" + id_hc;

    llamarAjax("../historia_clinica/impresion_historia_clinica.php", params, "d_impresion_hc", "continuar_imprimir_reg_hc();");
}

function continuar_imprimir_reg_hc() {
    var ruta = $("#hdd_ruta_arch_hc_pdf").val();
    window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=registro_hc.pdf", "_blank");
}

function obtener_extension_archivo(nombre_archivo) {
    var extension = nombre_archivo.substring(nombre_archivo.lastIndexOf(".") + 1).toLowerCase();

    return extension;
}

/*
 Modos: H: HTML - J: JavaScript
 */
function remplazar_acentos(texto, modo) {
    switch (modo) {
        case "H":
        case "h":
            texto = remplazar_acentos_int(texto, "á", "&aacute;");
            texto = remplazar_acentos_int(texto, "é", "&eacute;");
            texto = remplazar_acentos_int(texto, "í", "&iacute;");
            texto = remplazar_acentos_int(texto, "ó", "&oacute;");
            texto = remplazar_acentos_int(texto, "ú", "&uacute;");
            texto = remplazar_acentos_int(texto, "ü", "&uuml;");
            texto = remplazar_acentos_int(texto, "ñ", "&ntilde;");
            texto = remplazar_acentos_int(texto, "Á", "&Aacute;");
            texto = remplazar_acentos_int(texto, "É", "&Eacute;");
            texto = remplazar_acentos_int(texto, "Í", "&Iacute;");
            texto = remplazar_acentos_int(texto, "Ó", "&Oacute;");
            texto = remplazar_acentos_int(texto, "Ú", "&Uacute;");
            texto = remplazar_acentos_int(texto, "Ü", "&Uuml;");
            texto = remplazar_acentos_int(texto, "Ñ", "&Ntilde;");
            break;

        case "J":
        case "j":
            texto = remplazar_acentos_int(texto, "á", "\\xe1");
            texto = remplazar_acentos_int(texto, "é", "\\xe9");
            texto = remplazar_acentos_int(texto, "í", "\\xed");
            texto = remplazar_acentos_int(texto, "ó", "\\xf3");
            texto = remplazar_acentos_int(texto, "ú", "\\xfa");
            texto = remplazar_acentos_int(texto, "ü", "\\xfc");
            texto = remplazar_acentos_int(texto, "ñ", "\\xf1");
            texto = remplazar_acentos_int(texto, "Á", "\\xc1");
            texto = remplazar_acentos_int(texto, "É", "\\xc9");
            texto = remplazar_acentos_int(texto, "Í", "\\xcd");
            texto = remplazar_acentos_int(texto, "Ó", "\\xd3");
            texto = remplazar_acentos_int(texto, "Ú", "\\xda");
            texto = remplazar_acentos_int(texto, "Ü", "\\xdc");
            texto = remplazar_acentos_int(texto, "Ñ", "\\xd1");
            break;
    }

    return texto;
}

function remplazar_acentos_int(texto, base, remplazar) {
    var pos_aux = texto.indexOf(base);
    while (pos_aux != -1) {
        texto = texto.substring(0, pos_aux) + remplazar + texto.substring(pos_aux + 1);
        pos_aux = texto.indexOf(base);
    }

    return texto;
}

function validar_email(valor) {
    var filter = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if (filter.test(valor)/* || valor.toLowerCase() == "no tiene"*/) {
        return true;
    } else {
        return false;
    }
}

function imprimir_hc_completa_ext(id_paciente) {
    $("#d_btn_impr_completa_1").css("display", "none");
    $("#d_btn_impr_completa_2").css("display", "block");

    var params = "id_paciente=" + id_paciente +
            obtener_parametros_filtros_ext();

    llamarAjax("../historia_clinica/impresion_historia_clinica.php", params, "d_impresion_hc", "continuar_imprimir_hc_completa_ext();");
}

function continuar_imprimir_hc_completa_ext() {
    var ruta = $("#hdd_ruta_arch_hc_pdf").val();
    window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=historia_clinica.pdf", "_blank");

    $("#d_btn_impr_completa_1").css("display", "block");
    $("#d_btn_impr_completa_2").css("display", "none");
}

function imprimir_hc_resumen_ext(id_paciente) {
    $("#d_btn_impr_completa_1").css("display", "none");
    $("#d_btn_impr_completa_2").css("display", "block");

    var params = "id_paciente=" + id_paciente +
            obtener_parametros_filtros_ext();

    llamarAjax("../historia_clinica/impresion_historia_clinica_resumen.php", params, "d_impresion_hc", "continuar_imprimir_hc_resumen_ext();");
}

function continuar_imprimir_hc_resumen_ext() {
    var ruta = $("#hdd_ruta_arch_hc_pdf").val();
    window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=historia_clinica_resumen.pdf", "_blank");

    $("#d_btn_impr_completa_1").css("display", "block");
    $("#d_btn_impr_completa_2").css("display", "none");
}

function obtener_parametros_filtros_ext() {
    var params = "";
    var cant_tipos_reg_filtros = parseInt($("#hdd_cant_tipos_reg_filtros").val(), 10);
    var ind_tipo_reg_todos = $("#chk_tipo_reg_todos").is(":checked") ? 1 : 0;
    var cont_aux = 0;
    for (var i = 0; i < cant_tipos_reg_filtros; i++) {
        if ($("#chk_tipo_reg_" + i).is(":checked")) {
            params += "&id_tipo_reg_" + cont_aux + "=" + $("#hdd_tipo_reg_" + i).val();
            cont_aux++;
        }
    }
    params += "&ind_tipo_reg_todos=" + ind_tipo_reg_todos +
            "&cant_tipos_reg_filtros=" + cont_aux;

    var cant_usuarios_prof_filtros = parseInt($("#hdd_cant_usuarios_prof_filtros").val(), 10);
    var ind_usuario_prof_todos = $("#chk_usuario_prof_todos").is(":checked") ? 1 : 0;
    cont_aux = 0;
    for (var i = 0; i < cant_usuarios_prof_filtros; i++) {
        if ($("#chk_usuario_prof_" + i).is(":checked")) {
            params += "&id_usuario_prof_" + cont_aux + "=" + $("#hdd_usuario_prof_" + i).val();
            cont_aux++;
        }
    }
    params += "&ind_usuario_prof_todos=" + ind_usuario_prof_todos +
            "&cant_usuarios_prof_filtros=" + cont_aux;

    return params;
}

function obtener_nombre_completo(nombre1, nombre2, apellido1, apellido2) {
    var nombre_aux = nombre1;
    if (trim(nombre2) != "") {
        nombre_aux += " " + nombre2;
    }
    nombre_aux += " " + apellido1;
    if (trim(apellido2) != "") {
        nombre_aux += " " + apellido2;
    }

    return nombre_aux;
}

function abrir_cerrar_filtros_hc_ext() {
    if ($("#d_filtros_hc").is(":visible")) {
        $("#d_filtros_hc").hide(200);
    } else {
        $("#d_filtros_hc").show(200);
    }
}

function seleccionar_fitro_tipo_reg_ext(indice) {
    var cant_tipos_reg_filtros = parseInt($("#hdd_cant_tipos_reg_filtros").val(), 10);
    if (indice == "todos") {
        var ind_checked = $("#chk_tipo_reg_todos").is(":checked");
        for (var i = 0; i < cant_tipos_reg_filtros; i++) {
            $("#chk_tipo_reg_" + i).prop("checked", ind_checked);
        }
    } else {
        var ind_checked = true;
        for (var i = 0; i < cant_tipos_reg_filtros; i++) {
            if (!$("#chk_tipo_reg_" + i).is(":checked")) {
                ind_checked = false;
                break;
            }
        }
        $("#chk_tipo_reg_todos").prop("checked", ind_checked);
    }

    aplicar_filtros_hc_ext();
}

function seleccionar_fitro_usuario_prof_ext(indice) {
    var cant_usuarios_prof_filtros = parseInt($("#hdd_cant_usuarios_prof_filtros").val(), 10);
    if (indice == "todos") {
        var ind_checked = $("#chk_usuario_prof_todos").is(":checked");
        for (var i = 0; i < cant_usuarios_prof_filtros; i++) {
            $("#chk_usuario_prof_" + i).prop("checked", ind_checked);
        }
    } else {
        var ind_checked = true;
        for (var i = 0; i < cant_usuarios_prof_filtros; i++) {
            if (!$("#chk_usuario_prof_" + i).is(":checked")) {
                ind_checked = false;
                break;
            }
        }
        $("#chk_usuario_prof_todos").prop("checked", ind_checked);
    }

    aplicar_filtros_hc_ext();
}

function aplicar_filtros_hc_ext() {
    var cant_registros_hc = parseInt($("#hdd_cant_registros_hc").val(), 10);
    if ($("#chk_tipo_reg_todos").is(":checked") && $("#chk_usuario_prof_todos").is(":checked")) {
        //Todos visibles
        for (var i = 0; i < cant_registros_hc; i++) {
            $("#tr_registro_hc_" + i).css("display", "table-row");
        }
    } else {
        var cant_tipos_reg_filtros = parseInt($("#hdd_cant_tipos_reg_filtros").val(), 10);
        var cadena_tipos_reg = "#";
        for (var i = 0; i < cant_tipos_reg_filtros; i++) {
            if ($("#chk_tipo_reg_" + i).is(":checked")) {
                cadena_tipos_reg += $("#hdd_tipo_reg_" + i).val() + "#";
            }
        }
        var cant_usuarios_prof_filtros = parseInt($("#hdd_cant_usuarios_prof_filtros").val(), 10);
        var cadena_usuarios_prof = "#";
        for (var i = 0; i < cant_usuarios_prof_filtros; i++) {
            if ($("#chk_usuario_prof_" + i).is(":checked")) {
                cadena_usuarios_prof += $("#hdd_usuario_prof_" + i).val() + "#";
            }
        }

        var ind_tipo_reg_todos = $("#chk_tipo_reg_todos").is(":checked");
        var ind_usuario_prof_todos = $("#chk_usuario_prof_todos").is(":checked");
        for (var i = 0; i < cant_registros_hc; i++) {
            var id_tipo_reg_aux = $("#hdd_tipo_reg_hc_" + i).val();
            var id_usuario_prof_aux = $("#hdd_usuario_prof_hc_" + i).val();

            if ((ind_tipo_reg_todos || cadena_tipos_reg.indexOf("#" + id_tipo_reg_aux + "#") >= 0) &&
                    (ind_usuario_prof_todos || cadena_usuarios_prof.indexOf("#" + id_usuario_prof_aux + "#") >= 0)) {
                $("#tr_registro_hc_" + i).css("display", "table-row");
            } else {
                $("#tr_registro_hc_" + i).css("display", "none");
            }
        }
    }
}

/**
 * Función para formato de números
 * n: longitud decimal
 * x: longitud de las partes a separar (3 para separadores de mil)
 * s: delimitador de secciones (miles)
 * c: delimitador decimal
 */
Number.prototype.formatoNumerico = function(n, x, s, c) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = this.toFixed(Math.max(0, ~~n));

    return (c ? num.replace(',', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || '.'));
};

/*Documentación:*/
/* https://sweetalert2.github.io */
function alert_basico(titulo, texto, typo, cerrado_automatico=false) {
    swal({
        title: titulo,
        text: texto,
        type: typo
    });
    
    if(cerrado_automatico){
        setTimeout(function () {
            swal.close();
        }, 2000);
    }
}
