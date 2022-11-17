function validarConsultar() {
	$("#frm_remisiones").validate({
		rules: {
			txt_paciente_hc: {
				required: true,
			},
		},
		submitHandler: function () {
			var txt_paciente_hc = $("#txt_paciente_hc").val();
			var params = "opcion=1&txt_paciente_hc=" + txt_paciente_hc;
			
			llamarAjax("remisiones_ajax.php", params, "contenedor_paciente_hc", "");
			
			return false;
		},
	});
}

function ver_remisiones_paciente(id_persona, nombre_persona, documento_persona, tipo_documento, telefonos, fecha_nacimiento, edad_paciente, nombreConvenio, estadoConvenio) {
	var params = "opcion=2&id_persona=" + id_persona +
				 "&nombre_persona=" + nombre_persona +
				 "&documento_persona=" + documento_persona +
				 "&tipo_documento=" + tipo_documento +
				 "&telefonos=" + telefonos +
				 "&fecha_nacimiento=" + fecha_nacimiento +
				 "&edad_paciente=" + edad_paciente +
				 "&nombreConvenio=" + nombreConvenio +
				 "&estadoConvenio=" + estadoConvenio;
	
	llamarAjax("remisiones_ajax.php", params, "contenedor_paciente_hc", "");
}

function asignarCita(idPaciente) {
	$("#frm_credencial").append('<input type="hidden" name="hdd_id_paciente" id="hdd_id_paciente" value="'+idPaciente+'" />');
	
	enviar_credencial("../citas/asignar_citas.php", 9);
}
