function validarBuscarPersonasHc() {


    $("#frmRegistroCx").validate({
        rules: {
            txt_paciente_hc: {
                required: true,
            },
        },
        submitHandler: function() {

            $("#contenedor_error").css("display", "none");
            var txt_paciente_hc = $('#txt_paciente_hc').val();
            var params = "opcion=1&txt_paciente_hc=" + txt_paciente_hc;

            llamarAjax("registro_cx_ajax.php", params, "contenedor_paciente_hc", "");

            return false;
        },
    });


    /*
     if (validar_buscar_hc() == 0) {
     $("#contenedor_error").css("display", "none");
     var txt_paciente_hc = $('#txt_paciente_hc').val();
     var params = "opcion=1&txt_paciente_hc=" + txt_paciente_hc;
     
     llamarAjax("registro_cx_ajax.php", params, "contenedor_paciente_hc", "");
     } else {
     $("#contenedor_error").css("display", "block");
     $('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
     return false;
     }
     */

}

function validar_buscar_hc() {
    var result = 0;
    $('#txt_paciente_hc').removeClass("borde_error");
    if ($('#txt_paciente_hc').val() == '') {
        $('#txt_paciente_hc').addClass("borde_error");
        result = 1;
    }
    return result;
}

function mostrar_formulario_cx(id_admision, id_hc, pagina_menu, credencial, id_menu, ind_complemento) {
    mostrar_formulario_flotante(1);
    $("#d_interno").empty();
    posicionarDivFlotante('d_centro');
    reducir_formulario_flotante('1050', '700');
    $('.div_centro').height(700);
    $('.div_interno').height(700 - 35);

    var HcFrame = document.createElement("iframe");
    HcFrame.id = "HcFrame";
    ruta = pagina_menu + "?hdd_id_admision=" + id_admision +
            "&hdd_id_hc=" + id_hc +
            "&credencial=" + credencial +
            "&hdd_numero_menu=" + id_menu +
            "&tipo_entrada=2" +
            "&ind_complemento=" + ind_complemento;

    HcFrame.setAttribute("src", ruta);
    HcFrame.style.height = '100%';
    HcFrame.style.width = '1050px';
    var control = document.getElementById("HcFrame")
    $("#d_interno").append(HcFrame);
}

function ver_registros_hc(id_persona, nombre_persona, documento_persona, tipo_documento, telefonos, fecha_nacimiento, edad_paciente) {
    var params = "opcion=2&id_persona=" + id_persona +
            "&nombre_persona=" + nombre_persona +
            "&documento_persona=" + documento_persona +
            "&tipo_documento=" + tipo_documento +
            "&telefonos=" + telefonos +
            "&fecha_nacimiento=" + fecha_nacimiento +
            "&edad_paciente=" + edad_paciente;

    llamarAjax("registro_cx_ajax.php", params, "contenedor_paciente_hc", "");
}
