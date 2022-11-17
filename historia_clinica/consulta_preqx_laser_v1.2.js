
/**
 * Validar los campos de control prequirurgico laser
 */
function validar_preqx_laser(){
	
	var result = 0;
	var panel_1 = 0;
	var panel_2 = 0;
	var panel_3 = 0;
	var panel_4 = 0;
	
	$("#panel_laser_1").removeClass("borde_error_panel");
	$("#panel_laser_1 a").css({"color": "#5B5B5B"});
	$("#panel_laser_2").removeClass("borde_error_panel");
	$("#panel_laser_2 a").css({"color": "#5B5B5B"});
	$("#panel_laser_3").removeClass("borde_error_panel");
	$("#panel_laser_3 a").css({"color": "#5B5B5B"});
	$("#panel_laser_4").removeClass("borde_error_panel");
	$("#panel_laser_4 a").css({"color": "#5B5B5B"});
		
	$("#querato_cilindro_od").removeClass("borde_error");
	$("#querato_eje_od").removeClass("borde_error");
	$("#querato_kplano_od").removeClass("borde_error");
	$("#refraccion_esfera_od").removeClass("borde_error");
	$("#refraccion_cilindro_od").removeClass("borde_error");
	$("#refraccion_eje_od").removeClass("borde_error");
	$("#refraccion_lejos_od").removeClass("borde_error");
	$("#refraccion_cerca_od").removeClass("borde_error");
	$("#refractivo_deseado_od").removeClass("borde_error");
	$("#refractivo_esfera_od").removeClass("borde_error");
	$("#refractivo_cilindro_od").removeClass("borde_error");
	$("#refractivo_eje_od").removeClass("borde_error");
	$("#nomograma_esfera_od").removeClass("borde_error");
	$("#nomograma_cilindro_od").removeClass("borde_error");
	$("#nomograma_eje_od").removeClass("borde_error");
	$("#paquimetria_central_od").removeClass("borde_error");
	$("#paquimetria_periferica_od").removeClass("borde_error");
	
	$("#querato_cilindro_oi").removeClass("borde_error");
	$("#querato_eje_oi").removeClass("borde_error");
	$("#querato_kplano_oi").removeClass("borde_error");	
	$("#refraccion_esfera_oi").removeClass("borde_error");
	$("#refraccion_cilindro_oi").removeClass("borde_error");
	$("#refraccion_eje_oi").removeClass("borde_error");
	$("#refraccion_lejos_oi").removeClass("borde_error");
	$("#refraccion_cerca_oi").removeClass("borde_error");	
	$("#refractivo_deseado_oi").removeClass("borde_error");
	$("#refractivo_esfera_oi").removeClass("borde_error");
	$("#refractivo_cilindro_oi").removeClass("borde_error");
	$("#refractivo_eje_oi").removeClass("borde_error");
	$("#nomograma_esfera_oi").removeClass("borde_error");
	$("#nomograma_cilindro_oi").removeClass("borde_error");
	$("#nomograma_eje_oi").removeClass("borde_error");
	$("#paquimetria_central_oi").removeClass("borde_error");
	$("#paquimetria_periferica_oi").removeClass("borde_error");
	
	
	$("#nomograma_equipo").removeClass("borde_error");
	$("#patologia_ocular_valor").removeClass("borde_error");
	$("#patologia_ocular_descripcion").removeClass("borde_error");
	$("#cirugia_ocular_valor").removeClass("borde_error");
	$("#cirugia_ocular_descripcion").removeClass("borde_error");	
	$("#diagnostico_preqx_laser").removeClass("borde_error");

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
	//**
	if (bol_od) {
		if($('#querato_cilindro_od').val()==''){ $("#querato_cilindro_od").addClass("borde_error"); result=1; panel_1=1;}
		if($('#querato_eje_od').val()==''){ $("#querato_eje_od").addClass("borde_error"); result=1; panel_1=1;}
		if($('#querato_kplano_od').val()==''){ $("#querato_kplano_od").addClass("borde_error"); result=1; panel_1=1;}
		if($('#refraccion_esfera_od').val()==''){ $("#refraccion_esfera_od").addClass("borde_error"); result=1; panel_1=1;}
		if($('#refraccion_cilindro_od').val()==''){ $("#refraccion_cilindro_od").addClass("borde_error"); result=1; panel_1=1;}
		if($('#refraccion_eje_od').val()==''){ $("#refraccion_eje_od").addClass("borde_error"); result=1; panel_1=1;}
		if($('#refraccion_lejos_od').val()==''){ $("#refraccion_lejos_od").addClass("borde_error"); result=1; panel_1=1;}
		if($('#refraccion_cerca_od').val()==''){ $("#refraccion_cerca_od").addClass("borde_error"); result=1; panel_1=1;}
		if($('#refractivo_deseado_od').val()==''){ $("#refractivo_deseado_od").addClass("borde_error"); result=1; panel_2=1;}
		if($('#refractivo_esfera_od').val()==''){ $("#refractivo_esfera_od").addClass("borde_error"); result=1; panel_2=1;}
		if($('#refractivo_cilindro_od').val()==''){ $("#refractivo_cilindro_od").addClass("borde_error"); result=1; panel_2=1;}
		if($('#refractivo_eje_od').val()==''){ $("#refractivo_eje_od").addClass("borde_error"); result=1; panel_2=1;}
		if($('#nomograma_esfera_od').val()==''){ $("#nomograma_esfera_od").addClass("borde_error"); result=1; panel_3=1;}
		if($('#nomograma_cilindro_od').val()==''){ $("#nomograma_cilindro_od").addClass("borde_error"); result=1; panel_3=1;}
		if($('#nomograma_eje_od').val()==''){ $("#nomograma_eje_od").addClass("borde_error"); result=1; panel_3=1;}
		if($('#paquimetria_central_od').val()==''){ $("#paquimetria_central_od").addClass("borde_error"); result=1; panel_3=1;}
		if($('#paquimetria_periferica_od').val()==''){ $("#paquimetria_periferica_od").addClass("borde_error"); result=1; panel_3=1;}
	}
	
	if (bol_oi) {
		if($('#querato_cilindro_oi').val()==''){ $("#querato_cilindro_oi").addClass("borde_error"); result=1; panel_1=1;}
		if($('#querato_eje_oi').val()==''){ $("#querato_eje_oi").addClass("borde_error"); result=1; panel_1=1;}
		if($('#querato_kplano_oi').val()==''){ $("#querato_kplano_oi").addClass("borde_error"); result=1; panel_1=1;}	
		if($('#refraccion_esfera_oi').val()==''){ $("#refraccion_esfera_oi").addClass("borde_error"); result=1; panel_1=1;}
		if($('#refraccion_cilindro_oi').val()==''){ $("#refraccion_cilindro_oi").addClass("borde_error"); result=1; panel_1=1;}
		if($('#refraccion_eje_oi').val()==''){ $("#refraccion_eje_oi").addClass("borde_error"); result=1; panel_1=1;}
		if($('#refraccion_lejos_oi').val()==''){ $("#refraccion_lejos_oi").addClass("borde_error"); result=1; panel_1=1;}
		if($('#refraccion_cerca_oi').val()==''){ $("#refraccion_cerca_oi").addClass("borde_error"); result=1; panel_1=1;} 	
		if($('#refractivo_deseado_oi').val()==''){ $("#refractivo_deseado_oi").addClass("borde_error"); result=1; panel_2=1;}
		if($('#refractivo_esfera_oi').val()==''){ $("#refractivo_esfera_oi").addClass("borde_error"); result=1; panel_2=1;}
		if($('#refractivo_cilindro_oi').val()==''){ $("#refractivo_cilindro_oi").addClass("borde_error"); result=1; panel_2=1;}
		if($('#refractivo_eje_oi').val()==''){ $("#refractivo_eje_oi").addClass("borde_error"); result=1; panel_2=1;}
		if($('#nomograma_esfera_oi').val()==''){ $("#nomograma_esfera_oi").addClass("borde_error"); result=1; panel_3=1;}
		if($('#nomograma_cilindro_oi').val()==''){ $("#nomograma_cilindro_oi").addClass("borde_error"); result=1; panel_3=1;}
		if($('#nomograma_eje_oi').val()==''){ $("#nomograma_eje_oi").addClass("borde_error"); result=1; panel_3=1;}
		if($('#paquimetria_central_oi').val()==''){ $("#paquimetria_central_oi").addClass("borde_error"); result=1; panel_3=1;}
		if($('#paquimetria_periferica_oi').val()==''){ $("#paquimetria_periferica_oi").addClass("borde_error"); result=1; panel_3=1;}
	}	
	
	
	if($('#nomograma_equipo').val()==''){ $("#nomograma_equipo").addClass("borde_error"); result=1; panel_3=1;}
	if($('#patologia_ocular_valor').val()==''){
		 $("#patologia_ocular_valor").addClass("borde_error"); 
		 result=1;
		 panel_3=1;
	}
	else{
		if($('#patologia_ocular_valor').val()=='45'){
			if($('#patologia_ocular_descripcion').val()==''){ $("#patologia_ocular_descripcion").addClass("borde_error"); result=1; panel_3=1;}	
		}
	}
	
	if($('#cirugia_ocular_valor').val()==''){ 
		$("#cirugia_ocular_valor").addClass("borde_error"); 
		result=1;
		panel_3=1;
	}
	else{
		if($('#cirugia_ocular_valor').val()=='45'){
			if($('#cirugia_ocular_descripcion').val()==''){ $("#cirugia_ocular_descripcion").addClass("borde_error"); result=1; panel_3=1;}
		}
	}
	//if($('#diagnostico_preqx_laser').val()==''){ $("#diagnostico_preqx_laser").addClass("borde_error"); result=1;}
	if(panel_1 == 1){
	   $("#panel_laser_1").addClass("borde_error_panel");
	   $("#panel_laser_1 a").css({"color": "#FF002A"});
	}
	if(panel_2 == 1){
	   $("#panel_laser_2").addClass("borde_error_panel");
	   $("#panel_laser_2 a").css({"color": "#FF002A"});
	}
	if(panel_3 == 1){
	   $("#panel_laser_3").addClass("borde_error_panel");
	   $("#panel_laser_3 a").css({"color": "#FF002A"});
	}
	if(panel_4 == 1){
	   $("#panel_laser_4").addClass("borde_error_panel");
	   $("#panel_laser_4 a").css({"color": "#FF002A"});
	}
	
	return result;
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta SIN VALIDAR LOS CAMPOS
 * 3=Guardar y NO cambiar el estado de la consulta CON VALIDACION DE LOS CAMPOS
 */
function crear_preqx_laser(tipo, ind_imprimir) {
	switch (tipo) {
		case 1: //Finalizar consulta
		case 3: //Guardar desde historia
			$("#frm_consulta_preqx_laser").validate({
				submitHandler: function() {
					$("#contenedor_error").css("display", "none");
					/*if (validar_preqx_laser() == 0) {
						editar_consulta_preqx_laser(tipo);
						return false;
					} else {
						$("#contenedor_error").css("display", "block");
						$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
						return false;
					}*/
					editar_consulta_preqx_laser(tipo);
				},
			});
			break;
		case 2: //Guardar cambios
			editar_consulta_preqx_laser(tipo);
			break;
	}
	
	if (ind_imprimir == 1) {
		setTimeout("imprimir_preqx_laser()", 1000);
	}
}

function imprimir_preqx_laser() {
	var params = "id_hc=" + $("#hdd_id_hc_consulta").val();
	
	llamarAjax("../historia_clinica/impresion_historia_clinica.php", params, "d_impresion_hc", "imprSelec(\"d_impresion_hc\")");
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta 
 * 3=Guardar y NO cambiar el estado de la consulta CON VALIDACION DE LOS CAMPOS
 */
function editar_consulta_preqx_laser(tipo) {
	var hdd_id_hc_consulta = $('#hdd_id_hc_consulta').val();
	var hdd_id_admision = $('#hdd_id_admision').val();
	var querato_cilindro_od = $('#querato_cilindro_od').val();
	var querato_eje_od = $('#querato_eje_od').val();
	var querato_kplano_od = $('#querato_kplano_od').val();
	var querato_cilindro_oi = $('#querato_cilindro_oi').val();
	var querato_eje_oi = $('#querato_eje_oi').val();
	var querato_kplano_oi = $('#querato_kplano_oi').val();
	var refraccion_esfera_od = $('#refraccion_esfera_od').val();
	var refraccion_cilindro_od = $('#refraccion_cilindro_od').val();
	var refraccion_eje_od = $('#refraccion_eje_od').val();
	var refraccion_lejos_od = $('#refraccion_lejos_od').val();
	var refraccion_cerca_od = $('#refraccion_cerca_od').val();
	var refraccion_esfera_oi = $('#refraccion_esfera_oi').val();
	var refraccion_cilindro_oi = $('#refraccion_cilindro_oi').val();
	var refraccion_eje_oi = $('#refraccion_eje_oi').val();
	var refraccion_lejos_oi = $('#refraccion_lejos_oi').val();
	var refraccion_cerca_oi = $('#refraccion_cerca_oi').val();
	var cicloplejio_esfera_od = $('#cicloplejio_esfera_od').val();
	var cicloplejio_cilindro_od = $('#cicloplejio_cilindro_od').val();
	var cicloplejio_eje_od = $('#cicloplejio_eje_od').val();
	var cicloplejio_avcc_lejos_od = $('#cicloplejio_avcc_lejos_od').val();
	var cicloplejio_esfera_oi = $('#cicloplejio_esfera_oi').val();
	var cicloplejio_cilindro_oi = $('#cicloplejio_cilindro_oi').val();
	var cicloplejio_eje_oi = $('#cicloplejio_eje_oi').val();
	var cicloplejio_avcc_lejos_oi = $('#cicloplejio_avcc_lejos_oi').val();
	var refractivo_deseado_od = $('#refractivo_deseado_od').val();
	var refractivo_esfera_od = $('#refractivo_esfera_od').val();
	var refractivo_cilindro_od = $('#refractivo_cilindro_od').val();
	var refractivo_eje_od = $('#refractivo_eje_od').val();
	var refractivo_deseado_oi = $('#refractivo_deseado_oi').val();
	var refractivo_esfera_oi = $('#refractivo_esfera_oi').val();
	var refractivo_cilindro_oi = $('#refractivo_cilindro_oi').val();
	var refractivo_eje_oi = $('#refractivo_eje_oi').val();
	var nomograma_equipo = $('#nomograma_equipo').val();
	var nomograma_esfera_od = $('#nomograma_esfera_od').val();
	var nomograma_cilindro_od = $('#nomograma_cilindro_od').val();
	var nomograma_eje_od = $('#nomograma_eje_od').val();
	var nomograma_esfera_oi = $('#nomograma_esfera_oi').val();
	var nomograma_cilindro_oi = $('#nomograma_cilindro_oi').val();
	var nomograma_eje_oi = $('#nomograma_eje_oi').val();
	var patologia_ocular_valor = $('#patologia_ocular_valor').val();
	var patologia_ocular_descripcion = $('#patologia_ocular_descripcion').val();
	var cirugia_ocular_valor = $('#cirugia_ocular_valor').val();
	var cirugia_ocular_descripcion = $('#cirugia_ocular_descripcion').val();
	var paquimetria_central_od = $('#paquimetria_central_od').val();
	var paquimetria_central_oi = $('#paquimetria_central_oi').val();
	var diagnostico_preqx_laser = str_encode($('#diagnostico_preqx_laser').val());
	var paquimetria_periferica_od = $('#paquimetria_periferica_od').val();
	var paquimetria_periferica_oi = $('#paquimetria_periferica_oi').val();
	
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
	  
	  params=params+'&hdd_id_hc_consulta='+ hdd_id_hc_consulta +
				    '&hdd_id_admision=' + hdd_id_admision +
				    '&querato_cilindro_od=' + str_encode(querato_cilindro_od) +
					'&querato_eje_od=' + str_encode(querato_eje_od) +
					'&querato_kplano_od=' + str_encode(querato_kplano_od) +
					'&querato_cilindro_oi=' + str_encode(querato_cilindro_oi) +
					'&querato_eje_oi=' + str_encode(querato_eje_oi) +
					'&querato_kplano_oi=' + str_encode(querato_kplano_oi) +
					'&refraccion_esfera_od=' + str_encode(refraccion_esfera_od) +
					'&refraccion_cilindro_od=' + str_encode(refraccion_cilindro_od) +
					'&refraccion_eje_od=' + str_encode(refraccion_eje_od) +
					'&refraccion_lejos_od=' + str_encode(refraccion_lejos_od) +
					'&refraccion_cerca_od=' + str_encode(refraccion_cerca_od) +
					'&refraccion_esfera_oi=' + str_encode(refraccion_esfera_oi) +
					'&refraccion_cilindro_oi=' + str_encode(refraccion_cilindro_oi) +
					'&refraccion_eje_oi=' + str_encode(refraccion_eje_oi) +
					'&refraccion_lejos_oi=' + str_encode(refraccion_lejos_oi) +
					'&refraccion_cerca_oi=' + str_encode(refraccion_cerca_oi) +
					'&cicloplejio_esfera_od=' + str_encode(cicloplejio_esfera_od) +
					'&cicloplejio_cilindro_od=' + str_encode(cicloplejio_cilindro_od) +
					'&cicloplejio_eje_od=' + cicloplejio_eje_od +
					'&cicloplejio_avcc_lejos_od=' + cicloplejio_avcc_lejos_od +
					'&cicloplejio_esfera_oi=' + str_encode(cicloplejio_esfera_oi) +
					'&cicloplejio_cilindro_oi=' + str_encode(cicloplejio_cilindro_oi) +
					'&cicloplejio_eje_oi=' + cicloplejio_eje_oi +
					'&cicloplejio_avcc_lejos_oi=' + cicloplejio_avcc_lejos_oi +
					'&refractivo_deseado_od=' + str_encode(refractivo_deseado_od) +
					'&refractivo_esfera_od=' + str_encode(refractivo_esfera_od) +
					'&refractivo_cilindro_od=' + str_encode(refractivo_cilindro_od) +
					'&refractivo_eje_od=' + refractivo_eje_od +
					'&refractivo_deseado_oi=' + str_encode(refractivo_deseado_oi) +
					'&refractivo_esfera_oi=' + str_encode(refractivo_esfera_oi) +
					'&refractivo_cilindro_oi=' + str_encode(refractivo_cilindro_oi) +
					'&refractivo_eje_oi=' + refractivo_eje_oi +
					'&nomograma_equipo=' + nomograma_equipo +
					'&nomograma_esfera_od=' + str_encode(nomograma_esfera_od) +
					'&nomograma_cilindro_od=' + str_encode(nomograma_cilindro_od) +
					'&nomograma_eje_od=' + nomograma_eje_od +
					'&nomograma_esfera_oi=' + str_encode(nomograma_esfera_oi) +
					'&nomograma_cilindro_oi=' + str_encode(nomograma_cilindro_oi) +
					'&nomograma_eje_oi=' + nomograma_eje_oi +
					'&patologia_ocular_valor=' + patologia_ocular_valor +
					'&patologia_ocular_descripcion=' + str_encode(patologia_ocular_descripcion) +
					'&cirugia_ocular_valor=' + cirugia_ocular_valor +
					'&cirugia_ocular_descripcion=' + str_encode(cirugia_ocular_descripcion) +
					'&paquimetria_central_od=' + str_encode(paquimetria_central_od) +
					'&paquimetria_central_oi=' + str_encode(paquimetria_central_oi) +
					'&diagnostico_preqx_laser=' + str_encode(diagnostico_preqx_laser) +
					'&paquimetria_periferica_od=' + str_encode(paquimetria_periferica_od) +
					'&paquimetria_periferica_oi=' + str_encode(paquimetria_periferica_oi) +
					'&tipo_guardar=' + tipo;
	llamarAjax("consulta_preqx_laser_ajax.php", params, "guardar_preqx_laser", "validar_exito()");
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

/**
 * Validar los campos de complemento de control prequirurgico laser
 */
function validar_complemento_preqx_laser(){
	
	var result = 0;
	
	$("#refraccion_complemento_esfera_od").removeClass("borde_error");
	$("#refraccion_complemento_cilindro_od").removeClass("borde_error");
	$("#refraccion_complemento_eje_od").removeClass("borde_error");
	$("#refraccion_complemento_lejos_od").removeClass("borde_error");
	$("#refraccion_complemento_cerca_od").removeClass("borde_error");
		
	$("#refraccion_complemento_esfera_oi").removeClass("borde_error");
	$("#refraccion_complemento_cilindro_oi").removeClass("borde_error");
	$("#refraccion_complemento_eje_oi").removeClass("borde_error");
	$("#refraccion_complemento_lejos_oi").removeClass("borde_error");
	$("#refraccion_complemento_cerca_oi").removeClass("borde_error");	

	if (bol_od) {
		if($('#refraccion_complemento_esfera_od').val()==''){ $("#refraccion_complemento_esfera_od").addClass("borde_error"); result=1;}
		if($('#refraccion_complemento_cilindro_od').val()==''){ $("#refraccion_complemento_cilindro_od").addClass("borde_error"); result=1;}
		if($('#refraccion_complemento_eje_od').val()==''){ $("#refraccion_complemento_eje_od").addClass("borde_error"); result=1;}
		if($('#refraccion_complemento_lejos_od').val()==''){ $("#refraccion_complemento_lejos_od").addClass("borde_error"); result=1;}
		if($('#refraccion_complemento_cerca_od').val()==''){ $("#refraccion_complemento_cerca_od").addClass("borde_error"); result=1;}
	}
	
	if (bol_oi) {
		if($('#refraccion_complemento_esfera_oi').val()==''){ $("#refraccion_complemento_esfera_oi").addClass("borde_error"); result=1;}
		if($('#refraccion_complemento_cilindro_oi').val()==''){ $("#refraccion_complemento_cilindro_oi").addClass("borde_error"); result=1;}
		if($('#refraccion_complemento_eje_oi').val()==''){ $("#refraccion_complemento_eje_oi").addClass("borde_error"); result=1;}
		if($('#refraccion_complemento_lejos_oi').val()==''){ $("#refraccion_complemento_lejos_oi").addClass("borde_error"); result=1;}
		if($('#refraccion_complemento_cerca_oi').val()==''){ $("#refraccion_complemento_cerca_oi").addClass("borde_error"); result=1;}	
	}
	return result;
}



/**
 *crear o editar las datos complementarios de la consulta preqx laser 
 */
function crear_editar_complemento_preqx_laser() {
	
	$("#contenedor_error").css("display", "none");
	
	if (validar_complemento_preqx_laser() == 0){
		
		var hdd_id_hc_consulta = $('#hdd_id_hc_consulta').val();		
		var refraccion_esfera_od = $('#refraccion_complemento_esfera_od').val();
		var refraccion_cilindro_od = $('#refraccion_complemento_cilindro_od').val();
		var refraccion_eje_od = $('#refraccion_complemento_eje_od').val();
		var refraccion_lejos_od = $('#refraccion_complemento_lejos_od').val();
		var refraccion_cerca_od = $('#refraccion_complemento_cerca_od').val();
		var refraccion_esfera_oi = $('#refraccion_complemento_esfera_oi').val();
		var refraccion_cilindro_oi = $('#refraccion_complemento_cilindro_oi').val();
		var refraccion_eje_oi = $('#refraccion_complemento_eje_oi').val();
		var refraccion_lejos_oi = $('#refraccion_complemento_lejos_oi').val();
		var refraccion_cerca_oi = $('#refraccion_complemento_cerca_oi').val();
		
		var params='opcion=2'+
		            '&hdd_id_hc_consulta='+ hdd_id_hc_consulta +
					'&refraccion_esfera_od=' + str_encode(refraccion_esfera_od) +
					'&refraccion_cilindro_od=' + str_encode(refraccion_cilindro_od) +
					'&refraccion_eje_od=' + str_encode(refraccion_eje_od) +
					'&refraccion_lejos_od=' + str_encode(refraccion_lejos_od) +
					'&refraccion_cerca_od=' + str_encode(refraccion_cerca_od) +
					'&refraccion_esfera_oi=' + str_encode(refraccion_esfera_oi) +
					'&refraccion_cilindro_oi=' + str_encode(refraccion_cilindro_oi) +
					'&refraccion_eje_oi=' + str_encode(refraccion_eje_oi) +
					'&refraccion_lejos_oi=' + str_encode(refraccion_lejos_oi) +
					'&refraccion_cerca_oi=' + str_encode(refraccion_cerca_oi);
		llamarAjax("consulta_preqx_laser_ajax.php", params, "guardar_complemento_preqx_laser", "validar_exito_complemento()");
		
		
	}
	else{
		$("#contenedor_error_complemento").css("display", "block");
		$('#contenedor_error_complemento').html('Los campos marcados en rojo son obligatorios');
		return false;
	}
	
}


/**
 *Validar el exito de la trasaccion de los datos complementarios 
 */
function validar_exito_complemento(){
	var hdd_exito = $('#hdd_exito_complemento').val();
    
    if (hdd_exito > 0) {
        $("#contenedor_exito_complemento").css("display", "block");
        $('#contenedor_exito_complemento').html('Datos guardados correctamente');
        setTimeout('$("#contenedor_exito_complemento").css("display", "none")', 3000);
    }
    else {
        $("#contenedor_error_complemento").css("display", "block");
        $('#contenedor_error_complemento').html('Error al guardar usuarios');
        setTimeout('$("#contenedor_error_complemento").css("display", "none")', 3000);
    }
	
}



