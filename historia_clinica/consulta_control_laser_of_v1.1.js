/**
 * Validar los campos de control laser
 */
function validar_control_laser_of(){
	var result = 0;
	
	var panel_1 = 0;
	var panel_2 = 0;
	
	$("#panel_control_oft_1").removeClass("borde_error_panel");
	$("#panel_control_oft_1 a").css({"color": "#5B5B5B"});
	$("#panel_control_oft_2").removeClass("borde_error_panel");
	$("#panel_control_oft_2 a").css({"color": "#5B5B5B"});
	
	$("#presion_intraocular_aplanatica_od").removeClass("borde_error");
	$("#presion_intraocular_aplanatica_oi").removeClass("borde_error");
	$("#hallazgos_control_laser").removeClass("borde_error");
	$("#diagnostico_control_laser_of").removeClass("borde_error");	
	
	//**Para diagnosticos pintar normal
	$('#ciex_diagnostico_1').removeClass("borde_error");
	$('#valor_ojos_1').removeClass("borde_error");
	var cant_ciex = $('#lista_tabla').val()
	for (i=1;i<=cant_ciex;i++) {
	 	 var cod_ciex = $("#hdd_ciex_diagnostico_"+i).val();
	 	 var val_ojos = $("#valor_ojos_"+i).val();
	 	 $("#valor_ojos_"+i).removeClass("borde_error");
	}
	//**
	  
	//**Para diagnosticos pintar error
	if($('#hdd_ciex_diagnostico_1').val()==''){
		$("#ciex_diagnostico_1").addClass("borde_error");
		result=1;
		panel_2=1;
	}
	if($('#valor_ojos_1').val()==''){
		$("#valor_ojos_1").addClass("borde_error");
		result=1;
		panel_2=1;
	}
	var cant_ciex = $('#lista_tabla').val()
	for (i=1;i<=cant_ciex;i++) {
	 	 var cod_ciex = $("#hdd_ciex_diagnostico_"+i).val();
	 	 var val_ojos = $("#valor_ojos_"+i).val();
	 	 if(cod_ciex!='' && val_ojos==''){
	 	 	$("#valor_ojos_"+i).addClass("borde_error");
	 	 	result=1;
	 	 	panel_2=1;
	 	 }
	}
	//**
	
	if($('#presion_intraocular_aplanatica_od').val()==''){ $("#presion_intraocular_aplanatica_od").addClass("borde_error"); result=1; panel_1=1;}
	if($('#presion_intraocular_aplanatica_oi').val()==''){ $("#presion_intraocular_aplanatica_oi").addClass("borde_error"); result=1; panel_1=1;}
	if($('#hallazgos_control_laser').val()==''){ $("#hallazgos_control_laser").addClass("borde_error"); result=1; panel_1=1;}
	//if($('#diagnostico_control_laser_of').val()==''){ $("#diagnostico_control_laser_of").addClass("borde_error"); result=1; panel_2=1;}
	
	return result;
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta SIN VALIDAR LOS CAMPOS
 */
function crear_control_laser_of(tipo, ind_imprimir) {
	switch (tipo) {
		case 1: //Finalizar consulta
		case 3: //Guardar desde la historia
			$("#frm_consulta_control_laser_of").validate({
				submitHandler: function() {
					$("#contenedor_error").css("display", "none");
					if (validar_control_laser_of() == 0) {
						editar_consulta_control_laser_of(tipo);
						return false;
					} else {
						$("#contenedor_error").css("display", "block");
						$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
						return false;
					}
				},
			});
			break;
		case 2: //Guardar cambios
			editar_consulta_control_laser_of(tipo);
			break;
	}
	
	if (ind_imprimir == 1) {
		setTimeout("imprimir_optometria()", 1000);
	}
}

function imprimir_optometria() {
	var params = "id_hc=" + $("#hdd_id_hc_consulta").val();
	
	llamarAjax("../historia_clinica/impresion_historia_clinica.php", params, "d_impresion_hc", "imprSelec(\"d_impresion_hc\")");
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta 
 */
function editar_consulta_control_laser_of(tipo){
	var hdd_id_hc_consulta = $('#hdd_id_hc_consulta').val();
	var hdd_id_admision = $('#hdd_id_admision').val();
	var	presion_intraocular_aplanatica_od = $('#presion_intraocular_aplanatica_od').val();
	var	presion_intraocular_aplanatica_oi = $('#presion_intraocular_aplanatica_oi').val();
	var	hallazgos_control_laser = $('#hallazgos_control_laser').val();
	var	diagnostico_control_laser_of = $('#diagnostico_control_laser_of').val();
	
	var	solicitud_examenes_control_laser = $('#solicitud_examenes_control_laser').val();
	var	tratamiento_control_laser = $('#tratamiento_control_laser').val();
	var	medicamentos_control_laser = $('#medicamentos_control_laser').val();
	
	var params='opcion=1';
	
	//Para Diagnosticos
	var cant_ciex = $('#lista_tabla').val()
	params=params+'&cant_ciex=' + cant_ciex;
	for (i=1;i<=cant_ciex;i++) {
	 	 var cod_ciex = $("#hdd_ciex_diagnostico_"+i).val();
	 	 var val_ojos = $("#valor_ojos_"+i).val();
	 	 if(cod_ciex!=''){
	 	 	params=params+'&cod_ciex_'+i+'='+cod_ciex+'&val_ojos_'+i+'='+val_ojos;
	 	 }
	}
	
	params = params+'&hdd_id_hc_consulta='+ hdd_id_hc_consulta +
				    '&hdd_id_admision=' + hdd_id_admision +
				    '&presion_intraocular_aplanatica_od=' + str_encode(presion_intraocular_aplanatica_od) +
					'&presion_intraocular_aplanatica_oi=' + str_encode(presion_intraocular_aplanatica_oi) +
					'&hallazgos_control_laser=' + str_encode(hallazgos_control_laser) +
					'&diagnostico_control_laser_of=' + str_encode(diagnostico_control_laser_of) +
				    '&tipo_guardar=' + tipo +
				    '&solicitud_examenes_control_laser=' + str_encode(solicitud_examenes_control_laser) +
				    '&tratamiento_control_laser=' + str_encode(tratamiento_control_laser) +
				    '&medicamentos_control_laser=' + str_encode(medicamentos_control_laser);
	
	llamarAjax("consulta_control_laser_ajax_of.php", params, "guardar_control_laser_of", "validar_exito()");
}

function validar_exito() {
    var hdd_exito = $('#hdd_exito').val();
    var hdd_url_menu = $('#hdd_url_menu').val();
    var hdd_tipo_guardar = $('#hdd_tipo_guardar').val();
    
    if(hdd_tipo_guardar==1){//Cierra el formulario
    	$('.formulario').css('display', 'none');
	    if (hdd_exito > 0) {
	        $("#contenedor_exito").css("display", "block");
	        $('#contenedor_exito').html('Datos guardados correctamente');
	        setTimeout("enviar_credencial('"+hdd_url_menu+"')", 3000);
	    }
	    else {
	        $("#contenedor_error").css("display", "block");
	        $('#contenedor_error').html('Error al guardar usuarios');
	        setTimeout("enviar_credencial('"+hdd_url_menu+"')", 3000);
	    }
    }
    else if(hdd_tipo_guardar==2 || hdd_tipo_guardar==3){//Permanece en el formulario
    	if (hdd_exito > 0) {
	        $("#contenedor_exito").css("display", "block");
	        $('#contenedor_exito').html('Datos guardados correctamente');
	        setTimeout('$("#contenedor_exito").css("display", "none")', 3000);
	    }
	    else {
	        $("#contenedor_error").css("display", "block");
	        $('#contenedor_error').html('Error al guardar usuarios');
	        setTimeout('$("#contenedor_error").css("display", "none")', 3000);
	    }
    }
	window.scrollTo(0, 0);
}
