function buscar_pacientes() {
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
			
            llamarAjax("registro_evolucion_ajax.php", params, "contenedor_paciente_hc", "");
			
            return false;
        },
    });
}

function mostrar_formulario_evolucion(id_paciente, id_menu, pagina_menu, id_tipo_reg, credencial) {
    mostrar_formulario_flotante(1);
    $("#d_interno").empty();
    posicionarDivFlotante('d_centro');
    reducir_formulario_flotante('1050', '700');
    $('#d_centro').height(700);
    $('#d_interno').height(700 - 35);
	
    var HcFrame = document.createElement("iframe");
    HcFrame.id = "HcFrame";
    ruta = pagina_menu + "?hdd_id_paciente=" + id_paciente +
            "&hdd_numero_menu=" + id_menu +
			"&id_tipo_reg=" + id_tipo_reg +
            "&credencial=" + credencial +
            "&tipo_entrada=2";
	
    HcFrame.setAttribute("src", ruta);
    HcFrame.style.height = '100%';
    HcFrame.style.width = '1050px';
    var control = document.getElementById("HcFrame")
    $("#d_interno").append(HcFrame);
}
