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

function validar_cargar_archivo_anexo() {
	if ($("#fil_arch_anexo").val() == "") {
		alert("Debe seleccionar por lo menos un archivo");
		$("#fil_arch_anexo").focus();
		return false;
	}
	if ($("#txt_nombre_alt_tipo_reg").val() == "") {
		alert("Debe digitar el nombre del tipo de anexo");
		$("#txt_nombre_alt_tipo_reg").focus();
		return false;
	}
	
	return true;
}

function cargar_archivo_anexo() {
	$("#btn_cargar_anexo").attr("disabled", "disabled");
	if (validar_cargar_archivo_anexo()) {
		var ruta_arch_adjunto = $("#fil_arch_anexo").val();
		
		var ind_habeas_data = "";
		if($('#ind_habeas_1').prop('checked')){
			ind_habeas_data = $('#ind_habeas_1').val();	
		}else if(!$('#ind_habeas_1').prop('checked') && !$('#ind_habeas_0').prop('checked')){
			ind_habeas_data =  $('#ind_habeas_db').val();
		}
	
		$("#hdd_paciente_adjunto").val($("#hdd_id_paciente").val());
		$("#hdd_nombre_alt_tipo_reg").val(str_encode($("#txt_nombre_alt_tipo_reg").val()));
		$("#hdd_observaciones_hc").val(str_encode($("#txt_observaciones_hc").val()));
		
		var params = "opcion=3&id_paciente=" + $("#hdd_id_paciente").val() +
					 "&nombre_alt_tipo_reg=" + str_encode($("#txt_nombre_alt_tipo_reg").val()) +
					 "&observaciones_hc=" + str_encode($("#txt_observaciones_hc").val()) +
					 "&ind_habeas_data=" + ind_habeas_data;
		
		llamarAjaxUploadFiles("importar_anexos_ajax.php", params, "d_guardar_arch_adjunto", "continuar_cargar_archivo_anexo();", "d_barra_progreso_adj", "fil_arch_adjunto");
	} else {
		$("#btn_cargar_anexo").removeAttr("disabled");
	}
}

function continuar_cargar_archivo_anexo() {
	var id_hc = $("#hdd_id_hc_adjunto").val();
	$("#btn_cargar_anexo").removeAttr("disabled");
	if (id_hc > 0) {
		alert("Archivos agregados con \xe9xito a la historia cl\xednica");
		$("#d_paciente_hc").html("");
	} else if (id_hc == -1) {
		alert("Error al crear el registro de historia cl\xednica");
	} else {
		alert("Error interno al tratar de crear el registro de historia cl\xednica");
	}
}


function validar_check(opcion){
	switch(opcion){
		case 0:
			 if(!$('#ind_habeas_1').attr('disabled')){
				 $("#ind_habeas_1" ).attr( "disabled", true );
			 }else{
				  $( "#ind_habeas_1" ).attr( "disabled", false );
			 } 
		break;
		case 1:
			 if(!$('#ind_habeas_0').attr('disabled')){
				 $( "#ind_habeas_0" ).attr( "disabled", true );
			 }else{
				  $( "#ind_habeas_0" ).attr( "disabled", false );
			 }
		break;
	}
}

