function buscar_pacientes_hc() {
    $("#frm_historia_clinica").validate({
        rules: {
            txt_paciente_hc: {
                required: true,
            },
        },
        submitHandler: function() {
            var params = "opcion=1&parametro=" + str_encode($("#txt_paciente_hc").val());
			
            llamarAjax("importar_anexos_ajax.php", params, "d_paciente_hc", "");
			
            return false;
        },
    });
}

function mostrar_form_importar(id_paciente){
	var params = "opcion=2&id_paciente=" + id_paciente;
	
	llamarAjax("importar_anexos_ajax.php", params, "d_paciente_hc", "");
}

function cargar_archivo_anexo() {
	var ruta_arch_adjunto = $("#fil_arch_anexo").val();
	if (ruta_arch_adjunto != "") {
		var extension = obtener_extension_archivo(ruta_arch_adjunto);
		if (extension != "jpg" && extension != "png" && extension != "bmp" && extension != "gif" && extension != "pdf") {
			alert("El archivo a cargar debe ser una imagen o un archivo pdf");
			return;
		}
		
		if ($("#txt_nombre_alt_tipo_reg").val() == "") {
			alert("Debe digitar el nombre del tipo de anexo");
			$("#txt_nombre_alt_tipo_reg").focus();
		}
		
		$("#hdd_paciente_adjunto").val($("#hdd_id_paciente").val());
		$("#hdd_nombre_alt_tipo_reg").val(str_encode($("#txt_nombre_alt_tipo_reg").val()));
		$("#hdd_observaciones_hc").val(str_encode($("#txt_observaciones_hc").val()));
		
		$("#frm_arch_anexo").submit();
	} else {
		alert("Debe seleccionar un archivo");
		return;
	}
}
