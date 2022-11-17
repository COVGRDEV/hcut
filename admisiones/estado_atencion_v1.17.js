/* 
 * Archivo .js para la pagina estado.php
 * Juan Pablo Gomez Quiroga.
 */

$(document).ready(function () {
    var id_usuario_sel = $("#cmb_lista_usuarios option:selected").val();
    var id_lugar_cita = $("#cmb_lugar_cita option:selected").val();
    var params = "opcion=1&id_usuario_sel=" + id_usuario_sel + "&id_lugar_cita=" + id_lugar_cita;
    llamarAjax("estado_atencion_ajax.php", params, "contenedor", "");
});

var g_id_intervalo_estados = 0;
var g_cont_intervalos = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
function actualizar_reloj(hora, minutos, segundos, hora2, minutos2, segundos2, indice, id_estado_atencion) {
    if (segundos == 60) {
        segundos = 0;
        minutos++;
        if (minutos == 60) {
            minutos = 0;
            hora++;
        }
    }
    if (segundos <= 9) {
        segundos = "0" + segundos;
    }
    if (minutos <= 9) {
        minutos = "0" + parseInt(minutos);
    }

    if (hora2 != "") {
        if (segundos2 == 60) {
            segundos2 = 0;
            minutos2++;
            if (minutos2 == 60) {
                minutos2 = 0;
                hora2++;
            }
        }
        if (segundos2 <= 9) {
            segundos2 = "0" + segundos2;
        }
        if (minutos2 <= 9) {
            minutos2 = "0" + parseInt(minutos2);
        }
    }

    var texto_aux = hora + ":" + minutos;
    if (hora2 != "") {
        texto_aux += "<br />(" + hora2 + ":" + minutos2 + ")";
    }

    $("#sp_" + id_estado_atencion + "_" + indice).html(texto_aux);
    var color_aux = "#FF5500";
    if (hora == 0 && minutos < 15) {
        color_aux = "#777777";
    }
    $("#sp_" + id_estado_atencion + "_" + indice).css("color", color_aux);
    segundos++;
}

function limpiar_intervalo_estados() {
    clearInterval(g_id_intervalo_estados);
}

//Funcion que recarga el div contenedor
function recargar_estadosatencion() {
    limpiar_intervalo_estados();
    var id_usuario_sel = $("#cmb_lista_usuarios option:selected").val();
    var id_lugar_cita = $("#cmb_lugar_cita option:selected").val();
    var params = "opcion=1&id_usuario_sel=" + id_usuario_sel + "&id_lugar_cita=" + id_lugar_cita;
    llamarAjax("estado_atencion_ajax.php", params, "contenedor", "");
}

//Llama al modulo de admisiones
function modulo_admision(id_consulta, url, id_lugar_cita, nombre_lugar_cita) {
    var id_lugar_usuario = parseInt($("#hdd_lugar_usuario").val(), 10);

    if (id_lugar_cita == id_lugar_usuario) {
        continuar_modulo_admision(id_consulta, url);
    } else {
        $("#d_interno_adic").html(
                '<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
                '<tr>' +
                '<th align="center">' +
                '<h4>&iquest;El lugar de la cita es <span style="color:#B00;">' + nombre_lugar_cita + '</span> &iquest;Desea continuar?</h4>' +
                '</th>' +
                '</tr>' +
                '<tr>' +
                '<th align="center">' +
                '<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" class="btnPrincipal" value="Aceptar" onclick="continuar_modulo_admision(\'' + id_consulta + '\', \'' + url + '\');"/>\n' +
                '<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" class="btnSecundario" value="Cancelar" onclick="cerrar_div_centro_adic();"/> ' +
                '</th>' +
                '</tr>' +
                '</table>');

        $("#fondo_negro_adic").css("z-index", 14);
        $("#fondo_negro_adic").css("display", "block");
        $("#d_centro_adic").css("z-index", 15);
        $("#d_centro_adic").slideDown(500).css("display", "block");
    }
}

function continuar_modulo_admision(id_consulta, url) {
    $("#frm_credencial").append('<input type="hidden" id="hdd_id_consulta" name="hdd_id_consulta" />');
    $("#frm_credencial").append('<input type="hidden" id="hdd_id_post" name="hdd_id_post" value="1" />');
    $("#frm_credencial").append('<input type="hidden" id="hdd_paciente" name="hdd_paciente" />');

    $("#hdd_id_consulta").val(id_consulta);

    enviar_credencial(url, $("#hdd_menu").val());
}

function paciente_axiste() {
    //Si el paciente ya existe o no existe en la tabla pacientes muestra la notificacion
    var hdd_paciente = $("#hdd_paciente").val();
    if (hdd_paciente == 1) {
        $("#contenedor_exito").css("display", "block");
        $("#contenedor_exito").html('Existe un paciente con el numero de documento: <span style="font-weight: bold;">' + $("#hdd_numero_documento").val() + '</span> y nombre: <span style="font-weight: bold;">' + $("#hdd_nombre_paciente").val() + "</span>");
    }

}

function buscar_estadoatencion(opcion) {
    if (opcion == 1) {
        $("#frmbuscarPaciente").validate({
            debug: false,
            rules: {
                txt_buscarpaciente: {
                    required: true,
                },
            },
            submitHandler: function (form) {
                //Muestra el formulario
                mostrar_formulario_flotante(1);

                var parametro = $("#txt_buscarpaciente").val();
                var params = "opcion=3&parametro=" + parametro;
                $("#contenedor_interno2").remove();

                //Limpia el div
                $("#d_interno").css("width", "100%");
                $("#d_interno").css("height", "auto");
                $("#d_centro").css("width", "980px");
                $("#d_centro").css("height", "auto");

                $("#d_interno").empty();

                llamarAjax("estado_atencion_ajax.php", params, "d_interno", "");

                return false;
            }

        });
    } else if (opcion == 0) {
        limpiar_intervalo_estados();
        var id_usuario_sel = $("#cmb_lista_usuarios option:selected").val();
        var id_lugar_cita = $("#cmb_lugar_cita option:selected").val();
        var params = "opcion=1&id_usuario_sel=" + id_usuario_sel + "&id_lugar_cita=" + id_lugar_cita;
        llamarAjax("estado_atencion_ajax.php", params, "contenedor", "");
    }
}

function validar_destino(id_admision, id_paciente, id_estado_atencion, nombre_paciente, pag_destino, id_lugar_cita, nombre_lugar_cita, id_pago) {
    var id_lugar_usuario = parseInt($("#hdd_lugar_usuario").val(), 10);

    if (id_lugar_cita == id_lugar_usuario) {
        continuar_validar_destino(id_admision, id_paciente, id_estado_atencion, nombre_paciente, pag_destino, id_pago);
    } else {
        $("#d_interno_adic").html(
                '<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
                '<tr>' +
                '<th align="center">' +
                '<h4>&iquest;El lugar de la cita es <span style="color:#B00;">' + nombre_lugar_cita + '</span> &iquest;Desea continuar?</h4>' +
                '</th>' +
                '</tr>' +
                '<tr>' +
                '<th align="center">' +
                '<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" class="btnPrincipal" value="Aceptar" onclick="continuar_validar_destino(' + id_admision + ', ' + id_paciente + ', ' + id_estado_atencion + ', \'' + nombre_paciente + '\', \'' + pag_destino + '\', ' + id_pago + ');"/>\n' +
                '<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" class="btnSecundario" value="Cancelar" onclick="cerrar_div_centro_adic();"/> ' +
                '</th>' +
                '</tr>' +
                '</table>');

        $("#fondo_negro_adic").css("z-index", 14);
        $("#fondo_negro_adic").css("display", "block");
        $("#d_centro_adic").css("z-index", 15);
        $("#d_centro_adic").slideDown(500).css("display", "block");
    }
}

function continuar_validar_destino(id_admision, id_paciente, id_estado_atencion, nombre_paciente, pag_destino, id_pago) {
    //Despliega el formulario flotante
    if ($("#d_centro_adic").is(":visible")) {
        cerrar_div_centro_adic();
    }
    switch (id_estado_atencion) {
        case 2: //Registrar pagos
            enviar_formulario_registrar_pago(id_admision, pag_destino, id_pago);
            break;
        case 3: //Iniciar consulta de optometría
            mostrar_formulario_flotante(1);
            reducir_formulario_flotante(400, 300);
            posicionarDivFlotante("d_centro");
            $("#d_interno").html('<table class="datagrid" border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%"> ' +
                    '<tr class="headegrid"><th align="center" class="msg_alerta" style="border: 1px solid #fff;"><h4>&iquest;Est&aacute; seguro de iniciar la consulta de optometr&iacute;a para el paciente: <br /> ' + nombre_paciente +
                    '?</h4></th></tr>' +
                    '<tr><th align="center" style="width:5%;border: 1px solid #fff;">' +
                    '<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" value="Aceptar" class="btnPrincipal" onclick="llamar_formulario_consulta(' + id_admision + ', ' + id_paciente + ', ' + id_estado_atencion + ', \'' + pag_destino + '\',  \'' + nombre_paciente + '\');"/>&nbsp;&nbsp;' +
                    '<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" value="Cancelar" class="btnSecundario" onclick="cerrar_div_centro();"/>' +
                    '</th></tr></table>');
            $("#d_interno").css("height", "auto");
            break;
        case 4: //Consulta de optometría
            llamar_formulario_consulta(id_admision, id_paciente, id_estado_atencion, pag_destino, nombre_paciente);
            break;
        case 13: //Iniciar consulta de exámenes
            mostrar_formulario_flotante(1);
            reducir_formulario_flotante(400, 300);
            posicionarDivFlotante("d_centro");
            $("#d_interno").html('<table class="datagrid" border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%"> ' +
                    '<tr class="headegrid"><th align="center" class="msg_alerta" style="border: 1px solid #fff;"><h4>&iquest;Est&aacute; seguro de iniciar los ex&aacute;menes para el paciente: <br /> ' + nombre_paciente +
                    '?</h4></th></tr>' +
                    '<tr><th align="center" style="width:5%;border: 1px solid #fff;">' +
                    '<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" value="Aceptar" class="btnPrincipal" onclick="llamar_formulario_consulta(' + id_admision + ', ' + id_paciente + ', ' + id_estado_atencion + ', \'' + pag_destino + '\',  \'' + nombre_paciente + '\');"/>&nbsp;&nbsp;' +
                    '<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" value="Cancelar" class="btnSecundario" onclick="cerrar_div_centro();"/>' +
                    '</th></tr></table>');
            $("#d_interno").css("height", "auto");
            break;
        case 14: //Consulta de exámenes
            llamar_formulario_consulta(id_admision, id_paciente, id_estado_atencion, pag_destino, nombre_paciente);
            break;
        case 11: //Iniciar preconsulta
            mostrar_formulario_flotante(1);
            reducir_formulario_flotante(400, 300);
            posicionarDivFlotante("d_centro");
            $("#d_interno").html('<table class="datagrid" border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
                    '<tr class="headegrid"><th align="center" class="msg_alerta" style="border: 1px solid #fff;"><h4>Iniciar consulta de oftalmolog&iacute;a para el paciente: <br />' + nombre_paciente +
                    '</h4></th></tr>' +
                    '<tr><th align="center" style="width:5%;border: 1px solid #fff;">' +
                    '<input type="button" id="btn_cancelar_si2" nombre="btn_cancelar_si2" value="Preconsulta" class="btnPrincipal" onclick="llamar_formulario_consulta(' + id_admision + ', ' + id_paciente + ', ' + id_estado_atencion + ', \'' + pag_destino + '?ind_preconsulta=1\',  \'' + nombre_paciente + '\');"/>&nbsp;&nbsp;' +
                    '<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" value="Consulta" class="btnPrincipal" onclick="llamar_formulario_consulta(' + id_admision + ', ' + id_paciente + ', ' + id_estado_atencion + ', \'' + pag_destino + '\',  \'' + nombre_paciente + '\');"/>&nbsp;&nbsp;' +
                    '<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" value="Cancelar" class="btnSecundario" onclick="cerrar_div_centro();"/>' +
                    '</th></tr></table>');
            $("#d_interno").css("height", "auto");
            break;
        case 12: //Preconsulta
            llamar_formulario_consulta(id_admision, id_paciente, id_estado_atencion, pag_destino + "?ind_preconsulta=1", nombre_paciente);
            break;
        case 5: //Iniciar consulta de oftalmología
            mostrar_formulario_flotante(1);
            reducir_formulario_flotante(400, 300);
            posicionarDivFlotante("d_centro");
            $("#d_interno").html('<table class="datagrid" border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
                    '<tr class="headegrid"><th align="center" class="msg_alerta" style="border: 1px solid #fff;"><h4>&iquest;Est&aacute; seguro de iniciar la consulta de oftalmolog&iacute;a para el paciente: <br />' + nombre_paciente +
                    '?</h4></th></tr>' +
                    '<tr><th align="center" style="width:5%;border: 1px solid #fff;">' +
                    '<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" value="Aceptar" class="btnPrincipal" onclick="llamar_formulario_consulta(' + id_admision + ', ' + id_paciente + ', ' + id_estado_atencion + ', \'' + pag_destino + '\',  \'' + nombre_paciente + '\');"/>&nbsp;&nbsp;' +
                    '<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" value="Cancelar" class="btnSecundario" onclick="cerrar_div_centro();"/>' +
                    '</th></tr></table>');
            $("#d_interno").css("height", "auto");
            break;
        case 6: //Consulta de oftalmología
            llamar_formulario_consulta(id_admision, id_paciente, id_estado_atencion, pag_destino, nombre_paciente);
            break;
        case 7: //Iniciar despacho
            mostrar_formulario_flotante(1);
            reducir_formulario_flotante(400, 300);
            posicionarDivFlotante("d_centro");
            $("#d_interno").html('<table class="datagrid" border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
                    '<tr class="headegrid"><th align="center" class="msg_alerta" style="border: 1px solid #fff;"><h4>Est&aacute; seguro de iniciar el proceso de despacho para el paciente: <br />' + nombre_paciente +
                    '?</h4></th></tr>' +
                    '<tr><th align="center" style="width:5%;border: 1px solid #fff;">' +
                    '<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" value="Aceptar" class="btnPrincipal" onclick="llamar_formulario_consulta(' + id_admision + ', ' + id_paciente + ', ' + id_estado_atencion + ', \'' + pag_destino + '\',  \'' + nombre_paciente + '\');"/>&nbsp;&nbsp;' +
                    '<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" value="Cancelar" class="btnSecundario" onclick="cerrar_div_centro();"/>' +
                    '</th></tr></table>');
            $("#d_interno").css("height", "auto");
            break;
        case 8: // Despacho
            llamar_formulario_consulta(id_admision, id_paciente, id_estado_atencion, pag_destino, nombre_paciente);
            break;
    }
}

function llamar_formulario_consulta(id_admision, id_paciente, id_estado_atencion, pag_destino, nombre_paciente) {
    var params = "opcion=7&id_admision=" + id_admision;

    llamarAjax("estado_atencion_ajax.php", params, "d_registro_lugar_adm", "enviar_formulario_consulta(" + id_admision + ", " + id_paciente + ", " + id_estado_atencion + ", \"" + pag_destino + "\", \"" + nombre_paciente + "\");");
}

function enviar_formulario_consulta(id_admision, id_paciente, id_estado_atencion, pag_destino, nombre_paciente) {
    $("#frm_credencial").append('<input type="hidden" id="hdd_id_admision" name="hdd_id_admision" value="' + id_admision + '" />');
    $("#frm_credencial").append('<input type="hidden" id="hdd_id_paciente" name="hdd_id_paciente" value="' + id_paciente + '"  />');
    $("#frm_credencial").append('<input type="hidden" id="hdd_id_estado_atencion" name="hdd_id_estado_atencion" value="' + id_estado_atencion + '" />');
    $("#frm_credencial").append('<input type="hidden" id="hdd_nombre_paciente" name="hdd_nombre_paciente" value="' + nombre_paciente + '" />');
    $("#frm_credencial").append('<input type="hidden" id="hdd_en_atencion" name="hdd_en_atencion" value="1" />');

    enviar_credencial(pag_destino, $("#hdd_menu").val());
}

function enviar_formulario_registrar_pago(id_admision, pag_destino, id_pago) {
    $("#frm_credencial").append('<input type="hidden" id="hdd_idAdmision" name="hdd_idAdmision" value="' + id_admision + '"  />');
    $("#frm_credencial").append('<input type="hidden" id="hdd_idPago" name="hdd_idPago" value="' + id_pago + '"  />');

    enviar_credencial(pag_destino, $("#hdd_menu").val());
}

function expandir_listado(estado, idtabla) {
    //Limpia el div
    $("#d_interno").css("width", "100%");
    $("#d_interno").css("height", "auto");
    $("#d_centro").css("width", "980px");
    $("#d_centro").css("height", "auto");

    $("#d_interno").empty();
    $("#a_cierre_panel2").remove();

    //Despliega el formulario flotante
    $("#fondo_negro").css("display", "block");
    $("#d_centro").slideDown(400).css("display", "block");

    //Le cambia el id a la tabla clonada
    var tabla = $("#" + idtabla).clone().attr("id", "tablaModal");
    var fecha = $("#fecha_actual").val();

    //Dibuja los elementos en el div con id: d_interno
    $("#d_interno").append('<div id="contenedor_interno2" style="max-height:500px; overflow:auto;"><div class="encabezado" id="encabezado_interno"><h3>' + estado + " - " + fecha + "</h3></div>");
    $("#contenedor_interno2").append("</div>");
    $("#contenedor_interno2").append(tabla);

    //Asigna la propiedad de css: table-cell a las filas de la tabla con id:tabla2
    var filas = $("#tablaModal tr").length;

    for (var i = 0; i <= (filas - 1); i++) {
        $("#tablaModal tr:eq(" + i + ") td:eq(1)").css("display", "table-cell");
        $("#tablaModal tr:eq(" + i + ") td:eq(2)").css("display", "table-cell");
        $("#tablaModal tr:eq(" + i + ") td:eq(3)").css("display", "table-cell");
    }

    $("#tablaModal").addClass("modal_table");
    $("#tablaModal thead").css("display", "table-row-group");
    $("#d_centro").css("height", "auto");
}

//Esta funcion de ejecuta en el icono de cerrar que tiene el id:a_cierre_panel2 de formulario flotante de la funcion: expandir_listado
function expandir_listado_borrar() {
    $("#contenedor_interno2").remove();
    //Elimina el el icono cerrar ventana agregado en las lineas de arriba
    $("#a_cierre_panel2").remove();
    $("#d_centro").css("display", "none");
    $("#fondo_negro").css("display", "none");
}

function creaListado(id) {
    var filas = $("#" + idtabla + " tr").length;

    for (i = 1; i <= (filas - 1); i++) {
        myArray.push($("#tablaPrecios tr:eq(" + i + ") td:eq(0) input").attr("id"));
    }
}

function confirmar_llegada_persona(id_cita, id_lugar_cita, nombre_lugar_cita, pagina_menu) {
    var id_lugar_usuario = parseInt($("#hdd_lugar_usuario").val(), 10);

    if (id_lugar_cita == id_lugar_usuario) {
        continuar_confirmar_llegada_persona(id_cita,pagina_menu);
    } else {
        $("#d_interno_adic").html(
			'<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
				'<tr>' +
					'<th align="center">' +
						'<h4>&iquest;El lugar de la cita es <span style="color:#B00;">' + nombre_lugar_cita + '</span> &iquest;Desea continuar?</h4>' +
					'</th>' +
				'</tr>' +
				'<tr>' +
					'<th align="center">' +
						'<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" class="btnPrincipal" value="Aceptar" onclick="continuar_confirmar_llegada_persona(' + id_cita + ',"'+pagina_menu+'");"/>\n' +
						'<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" class="btnSecundario" value="Cancelar" onclick="cerrar_div_centro_adic();"/> ' +
					'</th>' +
				'</tr>' +
			'</table>');
		
        $("#fondo_negro_adic").css("z-index", 14);
        $("#fondo_negro_adic").css("display", "block");
        $("#d_centro_adic").css("z-index", 15);
        $("#d_centro_adic").slideDown(500).css("display", "block");
    }
}

function continuar_confirmar_llegada_persona(id_cita,pagina_menu) {
    $("#d_centro_adic").css("height", "auto");
    $("#d_interno_adic").css("width", "100%");
    $("#d_interno_adic").css("height", "auto");

    $("#d_interno_adic").html("");
    var params = "opcion=6&id_cita=" + id_cita+"&paginaMenu="+pagina_menu;
	
    llamarAjax("estado_atencion_ajax.php", params, "d_interno_adic", "");

    //Despliega el formulario flotante
    if ($("#d_centro").is(":visible")) {
        cerrar_div_centro();
    }
    $("#fondo_negro_adic").css("display", "block");
    $("#d_centro_adic").slideDown(500).css("display", "block");
}

function marcar_llegada_persona(id_cita) {
    $("#fondo_negro_adic").css("display", "none");
    $("#d_centro_adic").css("display", "none");

    var params = "opcion=4&id_cita=" + id_cita;
    llamarAjax("estado_atencion_ajax.php", params, "d_interno", "recargar_estadosatencion();");
}

function cargar_estados_atencion() {
    id_usuario_sel = $("#cmb_lista_usuarios").val();
    id_lugar_cita = $("#cmb_lugar_cita").val();

    var params = "opcion=5&id_usuario_sel=" + id_usuario_sel +
            "&id_lugar_cita=" + id_lugar_cita;

    llamarAjax("estado_atencion_ajax.php", params, "d_estados_nv", "continuar_cargar_estados_atencion();");
}

function continuar_cargar_estados_atencion() {
    var cant_estados_nv = parseInt($("#hdd_cant_estados_nv").val(), 10);

    for (var i = 0; i < cant_estados_nv; i++) {
        var id_estado_atencion = $("#hdd_estado_nv_" + i).val();
        $("#d_estado_" + id_estado_atencion).html($("#d_estado_nv_" + id_estado_atencion).html());
        $("#d_estado_nv_" + id_estado_atencion).html("");
    }
}
