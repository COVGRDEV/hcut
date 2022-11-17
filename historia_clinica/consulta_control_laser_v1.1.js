/**
 * Validar los campos de control laser
 */
function validar_control_laser(){
	var result = 0;
	var panel_1 = 0;
	var panel_2 = 0;
	var panel_3 = 0;
	var panel_4 = 0;
	
	$("#panel_control_opt_1").removeClass("borde_error_panel");
	$("#panel_control_opt_1 a").css({"color": "#5B5B5B"});
	$("#panel_control_opt_2").removeClass("borde_error_panel");
	$("#panel_control_opt_2 a").css({"color": "#5B5B5B"});
	$("#panel_control_opt_3").removeClass("borde_error_panel");
	$("#panel_control_opt_3 a").css({"color": "#5B5B5B"});
	$("#panel_control_opt_4").removeClass("borde_error_panel");
	$("#panel_control_opt_4 a").css({"color": "#5B5B5B"});
	
	
	$("#anamnesis").removeClass("borde_error");
	$("#avsc_lejos_od").removeClass("borde_error");
	$("#avsc_cerca_od").removeClass("borde_error");
	$("#avsc_lejos_oi").removeClass("borde_error");
	$("#avsc_cerca_oi").removeClass("borde_error");
	$("#querato_cilindro_od").removeClass("borde_error");
	$("#querato_eje_od").removeClass("borde_error");
	$("#querato_mplano_od").removeClass("borde_error");
	$("#querato_cilindro_oi").removeClass("borde_error");
	$("#querato_eje_oi").removeClass("borde_error");
	$("#querato_mplano_oi").removeClass("borde_error");
	$("#avc_esfera_od").removeClass("borde_error");
	$("#avc_cilindro_od").removeClass("borde_error");
	$("#avc_eje_od").removeClass("borde_error");
	$("#avcc_lejos_od").removeClass("borde_error");
	$("#avcc_adicion_od").removeClass("borde_error");
	$("#avcc_cerca_od").removeClass("borde_error");
	$("#avc_esfera_oi").removeClass("borde_error");
	$("#avc_cilindro_oi").removeClass("borde_error");
	$("#avc_eje_oi").removeClass("borde_error");
	$("#avcc_lejos_oi").removeClass("borde_error");
	$("#avcc_adicion_oi").removeClass("borde_error");
	$("#avcc_cerca_oi").removeClass("borde_error");
	$("#diagnostico_control_laser").removeClass("borde_error");
	
	//**Para diagnosticos pintar normal
	$('#ciex_diagnostico_1').removeClass("borde_error");
	$('#valor_ojos_1').removeClass("borde_error");
	var cant_ciex = $('#lista_tabla').val()
	for (i=1;i<=cant_ciex;i++) {
	 	 var cod_ciex = $("#hdd_ciex_diagnostico_"+i).val();
	 	 var val_ojos = $("#valor_ojos_"+i).val();
	 	 $("#valor_ojos_"+i).removeClass("borde_error");
	}
	
	//**Para diagnosticos pintar error
	if($('#hdd_ciex_diagnostico_1').val()==''){
		$("#ciex_diagnostico_1").addClass("borde_error");
		result=1;
		panel_4=1;
	}
	if($('#valor_ojos_1').val()==''){
		$("#valor_ojos_1").addClass("borde_error");
		result=1;
		panel_4=1;
	}
	var cant_ciex = $('#lista_tabla').val()
	for (i=1;i<=cant_ciex;i++) {
	 	 var cod_ciex = $("#hdd_ciex_diagnostico_"+i).val();
	 	 var val_ojos = $("#valor_ojos_"+i).val();
	 	 if(cod_ciex!='' && val_ojos==''){
	 	 	$("#valor_ojos_"+i).addClass("borde_error");
	 	 	result=1;
	 	 	panel_4=1;
	 	 }
	}
	
	if($('#anamnesis').val()==''){ $("#anamnesis").addClass("borde_error"); result=1; panel_1=1;}
	if($('#avsc_lejos_od').val()==''){ $("#avsc_lejos_od").addClass("borde_error"); result=1; panel_1=1;}
	if($('#avsc_cerca_od').val()==''){ $("#avsc_cerca_od").addClass("borde_error"); result=1; panel_1=1;}
	if($('#avsc_lejos_oi').val()==''){ $("#avsc_lejos_oi").addClass("borde_error"); result=1; panel_1=1;}
	if($('#avsc_cerca_oi').val()==''){ $("#avsc_cerca_oi").addClass("borde_error"); result=1; panel_1=1;}
	if($('#querato_cilindro_od').val()==''){ $("#querato_cilindro_od").addClass("borde_error"); result=1; panel_2=1;}
	if($('#querato_eje_od').val()==''){ $("#querato_eje_od").addClass("borde_error"); result=1; panel_2=1;}
	if($('#querato_mplano_od').val()==''){ $("#querato_mplano_od").addClass("borde_error"); result=1; panel_2=1;}
	if($('#querato_cilindro_oi').val()==''){ $("#querato_cilindro_oi").addClass("borde_error"); result=1; panel_2=1;}
	if($('#querato_eje_oi').val()==''){ $("#querato_eje_oi").addClass("borde_error"); result=1; panel_2=1;}
	if($('#querato_mplano_oi').val()==''){ $("#querato_mplano_oi").addClass("borde_error"); result=1; panel_2=1;}
	if($('#avc_esfera_od').val()==''){ $("#avc_esfera_od").addClass("borde_error"); result=1; panel_3=1;}
	if($('#avc_cilindro_od').val()==''){ $("#avc_cilindro_od").addClass("borde_error"); result=1; panel_3=1;}
	if($('#avc_eje_od').val()==''){ $("#avc_eje_od").addClass("borde_error"); result=1; panel_3=1;}
	if($('#avcc_lejos_od').val()==''){ $("#avcc_lejos_od").addClass("borde_error"); result=1; panel_3=1;}
	if($('#avcc_adicion_od').val()==''){ $("#avcc_adicion_od").addClass("borde_error"); result=1; panel_3=1;}
	if($('#avcc_cerca_od').val()==''){ $("#avcc_cerca_od").addClass("borde_error"); result=1; panel_3=1;}
	if($('#avc_esfera_oi').val()==''){ $("#avc_esfera_oi").addClass("borde_error"); result=1; panel_3=1;}
	if($('#avc_cilindro_oi').val()==''){ $("#avc_cilindro_oi").addClass("borde_error"); result=1; panel_3=1;}
	if($('#avc_eje_oi').val()==''){ $("#avc_eje_oi").addClass("borde_error"); result=1; panel_3=1;}
	if($('#avcc_lejos_oi').val()==''){ $("#avcc_lejos_oi").addClass("borde_error"); result=1; panel_3=1;}
	if($('#avcc_adicion_oi').val()==''){ $("#avcc_adicion_oi").addClass("borde_error"); result=1; panel_3=1;}
	if($('#avcc_cerca_oi').val()==''){ $("#avcc_cerca_oi").addClass("borde_error"); result=1; panel_3=1;}
	//if($('#diagnostico_control_laser').val()==''){ $("#diagnostico_control_laser").addClass("borde_error"); result=1;}
	
	if(panel_1 == 1){
	   $("#panel_control_opt_1").addClass("borde_error_panel");
	   $("#panel_control_opt_1 a").css({"color": "#FF002A"});
	}
	if(panel_2 == 1){
	   $("#panel_control_opt_2").addClass("borde_error_panel");
	   $("#panel_control_opt_2 a").css({"color": "#FF002A"});
	}
	if(panel_3 == 1){
	   $("#panel_control_opt_3").addClass("borde_error_panel");
	   $("#panel_control_opt_3 a").css({"color": "#FF002A"});
	}
	if(panel_4 == 1){
	   $("#panel_control_opt_4").addClass("borde_error_panel");
	   $("#panel_control_opt_4 a").css({"color": "#FF002A"});
	}

	
	return result;
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta SIN VALIDAR LOS CAMPOS
 */
function crear_control_laser(tipo, ind_imprimir) {
	switch (tipo) {
		case 1: //Finalizar consulta
		case 3: //Guardar desde historia
			$("#frm_consulta_control_laser").validate({
				submitHandler: function() {
					$("#contenedor_error").css("display", "none");
					if (validar_control_laser() == 0) {
						editar_consulta_control_laser(tipo);
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
			editar_consulta_control_laser(tipo);
			break;
	}
	
	if (ind_imprimir == 1) {
		setTimeout("imprimir_control_laser()", 1000);
	}
}

function imprimir_control_laser() {
	var params = "id_hc=" + $("#hdd_id_hc_consulta").val();
	
	llamarAjax("../historia_clinica/impresion_historia_clinica.php", params, "d_impresion_hc", "imprSelec(\"d_impresion_hc\")");
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta 
 */
function editar_consulta_control_laser(tipo){
	var hdd_id_hc_consulta = $('#hdd_id_hc_consulta').val();
	var hdd_id_admision = $('#hdd_id_admision').val();
	var anamnesis = $('#anamnesis').val();
	var avsc_lejos_od = $('#avsc_lejos_od').val();
	var avsc_cerca_od = $('#avsc_cerca_od').val();
	var avsc_lejos_oi = $('#avsc_lejos_oi').val();
	var avsc_cerca_oi = $('#avsc_cerca_oi').val();
	var querato_cilindro_od = $('#querato_cilindro_od').val();
	var querato_eje_od = $('#querato_eje_od').val();
	var querato_mplano_od = $('#querato_mplano_od').val();
	var querato_cilindro_oi = $('#querato_cilindro_oi').val();
	var querato_eje_oi = $('#querato_eje_oi').val();
	var querato_mplano_oi = $('#querato_mplano_oi').val();
	var avc_esfera_od = $('#avc_esfera_od').val();
	var avc_cilindro_od = $('#avc_cilindro_od').val();
	var avc_eje_od = $('#avc_eje_od').val();
	var avcc_lejos_od = $('#avcc_lejos_od').val();
	var avcc_adicion_od = $('#avcc_adicion_od').val();
	var avcc_cerca_od = $('#avcc_cerca_od').val();
	var avc_esfera_oi = $('#avc_esfera_oi').val();
	var avc_cilindro_oi = $('#avc_cilindro_oi').val();
	var avc_eje_oi = $('#avc_eje_oi').val();
	var avcc_lejos_oi = $('#avcc_lejos_oi').val();
	var avcc_adicion_oi = $('#avcc_adicion_oi').val();
	var avcc_cerca_oi = $('#avcc_cerca_oi').val();
	var diagnostico_control_laser = str_encode($('#diagnostico_control_laser').val());

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
				    '&anamnesis=' + str_encode(anamnesis) +
					'&avsc_lejos_od=' + str_encode(avsc_lejos_od) +
					'&avsc_cerca_od=' + str_encode(avsc_cerca_od) +
					'&avsc_lejos_oi=' + str_encode(avsc_lejos_oi) +
					'&avsc_cerca_oi=' + str_encode(avsc_cerca_oi) +
					'&querato_cilindro_od=' + str_encode(querato_cilindro_od) +
					'&querato_eje_od=' + str_encode(querato_eje_od) +
					'&querato_mplano_od=' + str_encode(querato_mplano_od) +
					'&querato_cilindro_oi=' + str_encode(querato_cilindro_oi) +
					'&querato_eje_oi=' + str_encode(querato_eje_oi) +
					'&querato_mplano_oi=' + str_encode(querato_mplano_oi) +
					'&avc_esfera_od=' + str_encode(avc_esfera_od) +
					'&avc_cilindro_od=' + str_encode(avc_cilindro_od) +
					'&avc_eje_od=' + str_encode(avc_eje_od) +
					'&avcc_lejos_od=' + str_encode(avcc_lejos_od) +
					'&avcc_adicion_od=' + str_encode(avcc_adicion_od) +
					'&avcc_cerca_od=' + str_encode(avcc_cerca_od) +
					'&avc_esfera_oi=' + str_encode(avc_esfera_oi) +
					'&avc_cilindro_oi=' + str_encode(avc_cilindro_oi) +
					'&avc_eje_oi=' + str_encode(avc_eje_oi) +
					'&avcc_lejos_oi=' + str_encode(avcc_lejos_oi) +
					'&avcc_adicion_oi=' + str_encode(avcc_adicion_oi) +
					'&avcc_cerca_oi=' + str_encode(avcc_cerca_oi) +
					'&diagnostico_control_laser=' + str_encode(diagnostico_control_laser) +
				    '&tipo_guardar=' + tipo;
	llamarAjax("consulta_control_laser_ajax.php", params, "guardar_control_laser", "validar_exito()");
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
