function validar_buscar_hc() {
    var result = 0;
    $("#txt_paciente_hc").removeClass("borde_error");
    if ($("#txt_paciente_hc").val() == "") {
        $("#txt_paciente_hc").addClass("borde_error");
        result = 1;
    }
    return result;
}

function validar_buscar_paciente() {
    $("#frm_formulas").validate({
        rules: {
            txt_paciente_hc: {
                required: true,
            },
        },
        submitHandler: function() {
            var params = "opcion=1&parametro=" + str_encode($("#txt_paciente_hc").val());
			
            llamarAjax("mover_admisiones_ajax.php", params, "d_contenedor_admision", "");
			
            return false;
        },
    });
}

function cargar_admision_cita(id_admision, id_cita) {
    var params = "opcion=2&id_admision=" + id_admision +
				 "&id_cita=" + id_cita;
	
    llamarAjax("mover_admisiones_ajax.php", params, "d_contenedor_admision", "");
}

function mover_atencion(tipo_cambio) {
	$("#btn_mover_atencion").attr("disabled", "disabled");
	$("#contenedor_exito").css("display", "none");
	$("#contenedor_error").css("display", "none");
	$("#cmb_estado_atencion").removeClass("borde_error");
	if ($("#cmb_estado_atencion").val() == "") {
		$("#cmb_estado_atencion").addClass("borde_error");
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Los campos marcados en rojo son obligatorios");
		$("#btn_mover_atencion").removeAttr("disabled");
	} else {
		mostrar_formulario_flotante(1);
		$("#d_interno").html(
			'<table class="datagrid" border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
				'<tr class="headegrid">' +
					'<th align="center" class="msg_alerta" style="border: 1px solid #fff;">' +
						'<h4>&iquest;Est&aacute; seguro que desea cambiar de estado la admisi&oacute;n seleccionada?</h4>' +
					'</th>' +
				'</tr>' +
				'<tr>' +
					'<th align="center" style="width:5%;border: 1px solid #fff;">' +
						'<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" value="Aceptar" class="btnPrincipal" onclick="continuar_mover_atencion(\'' + tipo_cambio + '\');"/>' +
						'&nbsp;&nbsp;' +
						'<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" value="Cancelar" class="btnSecundario" onclick="cerrar_div_centro();"/>' +
					'</th>' +
				'</tr>' +
			'</table>');
		posicionarDivFlotante("d_centro");
	}
}

function continuar_mover_atencion(tipo_cambio) {
	var params = "opcion=3&id_admision=" + $("#hdd_id_admision").val() +
				 "&id_cita=" + $("#hdd_id_cita").val() +
				 "&id_estado_atencion=" + $("#cmb_estado_atencion").val() +
				 "&tipo_cambio=" + tipo_cambio;
	
	cerrar_div_centro();
	llamarAjax("mover_admisiones_ajax.php", params, "d_mover_admision", "finalizar_mover_atencion();");
}

function finalizar_mover_atencion() {
	var resultado = parseInt($("#hdd_resul_cambio_estado").val(), 10);
	
	if (resultado > 0) {
		$("#contenedor_exito").css("display", "block");
		$("#contenedor_exito").html("Cambio de estado registrado con &eacute;xito. Si se cancel&oacute; la atenci&oacute;n es recomendable cancelar tambi&eacute;n el pago asociado.");
		$("#d_contenedor_admision").html("");
	} else if (resultado == -1) {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error interno al registrar el cambio de estado");
		$("#btn_mover_atencion").removeAttr("disabled");
	} else {
		$("#contenedor_error").css("display", "block");
		$("#contenedor_error").html("Error al registrar el cambio de estado");
		$("#btn_mover_atencion").removeAttr("disabled");
	}
}
