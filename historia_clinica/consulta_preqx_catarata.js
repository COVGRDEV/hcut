//Imagen Queratoemtria OD
var imagen_queratometria_od = imagen_queratometria_od || {};
imagen_queratometria_od.GuardandoPNGs = (function() {
  var mousePressed = false;
  var lastX, lastY;
  var ctx;
  function init_queratometria_od() {  
    // init canvas
    var canvas_queratometria_od = document.getElementById('canvas_queratometria_od');
    ctx = canvas_queratometria_od.getContext('2d');
    resetCanvasQueratometria_od();	

    // button events
    document.getElementById('btn_save_queratometria_od').onmouseup = sendToServerQueratometria_od;
    document.getElementById('btn_clear_queratometria_od').onmouseup = resetCanvasQueratometria_od;

    // canvas events
    canvas_queratometria_od.onmousedown = function(e) {
      draw(e.layerX, e.layerY);
      mousePressed = true;
    };

    canvas_queratometria_od.onmousemove = function(e) {
      if (mousePressed) {
        draw(e.layerX, e.layerY);
      }
    };

    canvas_queratometria_od.onmouseup = function(e) {
      mousePressed = false;
    };
    
    canvas_queratometria_od.onmouseleave = function(e) {
      mousePressed = false;
    };
  }

  function draw(x, y) {
    if (mousePressed) {
      ctx.beginPath();
      ctx.strokeStyle = document.getElementById('color_queratometria_od').value;
      if(document.getElementById('color_queratometria_od').value == '#ffffff' || document.getElementById('color_queratometria_od').value == '#FFFFFF' ){
      	ctx.lineWidth = 10;   	
      }
      else{
      	ctx.lineWidth = 2;	
      }
      ctx.lineJoin = 'round';
      ctx.moveTo(lastX, lastY);
      ctx.lineTo(x, y);
      ctx.closePath();
      ctx.stroke();
    }
    lastX = x; lastY = y;
  }

  function sendToServerQueratometria_od() {
  	var canvas_queratometria_od = document.getElementById('canvas_queratometria_od');
  	var id_hc = document.getElementById('hdd_id_hc_consulta').value;
  	var id_paciente = document.getElementById('hdd_id_paciente').value;
  	
  	var img = new Image();
    //indico la URL de la imagen
	img.src = '../imagenes/queratometria_img.png';
	//defino el evento onload del objeto imagen
	//incluyo la imagen en el canvas
	ctx.drawImage(img, 1, 1);
  	
  	var data = 'queratometriaod|'+id_paciente+'|'+id_hc+'|'+canvas_queratometria_od.toDataURL('../imagenes/hc_temporal');
  	
  	var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
      // request complete
      if (xhr.readyState == 4) {
	    alert('Imagen guardada correctamente');
	    document.getElementById('hdd_img_queratometria_od').value = xhr.responseText;
      }
    }
    xhr.open('POST','../historia_clinica/guardar_img_oftalmologia.php',true);
    xhr.setRequestHeader('Content-Type', 'application/upload');
    xhr.send(data);
  }
  
  function resetCanvasQueratometria_od() {
  	var canvas_queratometria_od = document.getElementById('canvas_queratometria_od');
  	ctx = canvas_queratometria_od.getContext('2d');
    // just repaint canvas white
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, canvas_queratometria_od.width, canvas_queratometria_od.height);
	
	var img = new Image();
    //indico la URL de la imagen
    var img_queratometria_od_bd = document.getElementById('hdd_img_queratometria_od').value;
    
    if(img_queratometria_od_bd == ''){
    	img.src = '../imagenes/queratometria_img.png';	
    }
    else{
    	img.src = img_queratometria_od_bd;
    }
    //defino el evento onload del objeto imagen
	//incluyo la imagen en el canvas
	ctx.drawImage(img, 1, 1);
	
  }
  return {
    'init_queratometria_od': init_queratometria_od,
    'resetCanvasQueratometria_od': resetCanvasQueratometria_od
  };
});








//Imagen Queratoemtria OI
var imagen_queratometria_oi = imagen_queratometria_oi || {};
imagen_queratometria_oi.GuardandoPNGs = (function() {
  var mousePressed = false;
  var lastX, lastY;
  var ctx;
  function init_queratometria_oi() {  
    // init canvas
    var canvas_queratometria_oi = document.getElementById('canvas_queratometria_oi');
    ctx = canvas_queratometria_oi.getContext('2d');
    resetCanvasQueratometria_oi();	

    // button events
    document.getElementById('btn_save_queratometria_oi').onmouseup = sendToServerQueratometria_oi;
    document.getElementById('btn_clear_queratometria_oi').onmouseup = resetCanvasQueratometria_oi;

    // canvas events
    canvas_queratometria_oi.onmousedown = function(e) {
      draw(e.layerX, e.layerY);
      mousePressed = true;
    };

    canvas_queratometria_oi.onmousemove = function(e) {
      if (mousePressed) {
        draw(e.layerX, e.layerY);
      }
    };
    
    canvas_queratometria_oi.onmouseup = function(e) {
      mousePressed = false;
    };
    
    canvas_queratometria_oi.onmouseleave = function(e) {
      mousePressed = false;
    };
  }

  function draw(x, y) {
    if (mousePressed) {
      ctx.beginPath();
      ctx.strokeStyle = document.getElementById('color_queratometria_oi').value;
      if(document.getElementById('color_queratometria_oi').value == '#ffffff' || document.getElementById('color_queratometria_oi').value == '#FFFFFF' ){
      	ctx.lineWidth = 10;   	
      }
      else{
      	ctx.lineWidth = 2;	
      }
      ctx.lineJoin = 'round';
      ctx.moveTo(lastX, lastY);
      ctx.lineTo(x, y);
      ctx.closePath();
      ctx.stroke();
    }
    lastX = x; lastY = y;
  }

  function sendToServerQueratometria_oi() {
  	var canvas_queratometria_oi = document.getElementById('canvas_queratometria_oi');
  	var id_hc = document.getElementById('hdd_id_hc_consulta').value;
  	var id_paciente = document.getElementById('hdd_id_paciente').value;
  	
  	var img = new Image();
    //indico la URL de la imagen
	img.src = '../imagenes/queratometria_img.png';
	//defino el evento onload del objeto imagen
	//incluyo la imagen en el canvas
	ctx.drawImage(img, 1, 1);
  	
  	var data = 'queratometriaoi|'+id_paciente+'|'+id_hc+'|'+canvas_queratometria_oi.toDataURL('../imagenes/hc_temporal');
  	
  	var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
      // request complete
      if (xhr.readyState == 4) {
	    alert('Imagen guardada correctamente');
	    document.getElementById('hdd_img_queratometria_oi').value = xhr.responseText;
      }
    }
    xhr.open('POST','../historia_clinica/guardar_img_oftalmologia.php',true);
    xhr.setRequestHeader('Content-Type', 'application/upload');
    xhr.send(data);
  }
  
  function resetCanvasQueratometria_oi() {
  	var canvas_queratometria_oi = document.getElementById('canvas_queratometria_oi');
  	ctx = canvas_queratometria_oi.getContext('2d');
    // just repaint canvas white
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, canvas_queratometria_oi.width, canvas_queratometria_oi.height);
	
	var img = new Image();
    //indico la URL de la imagen
    var img_queratometria_oi_bd = document.getElementById('hdd_img_queratometria_oi').value;
    
    if(img_queratometria_oi_bd == ''){
    	img.src = '../imagenes/queratometria_img.png';	
    }
    else{
    	img.src = img_queratometria_oi_bd;
    }
    //defino el evento onload del objeto imagen
	//incluyo la imagen en el canvas
	ctx.drawImage(img, 1, 1);
	
  }
  return {
    'init_queratometria_oi': init_queratometria_oi,
    'resetCanvasQueratometria_oi': resetCanvasQueratometria_oi
  };
});


window.onload = function() {
	if(bol_od == true){
		new imagen_queratometria_od.GuardandoPNGs().init_queratometria_od();	
	}
	
	if(bol_oi == true){
		new imagen_queratometria_oi.GuardandoPNGs().init_queratometria_oi();
	}
};


function borrar_img_queratometria_od(){
	document.getElementById('color_queratometria_od').value = '#FFFFFF';
}

function borrar_img_queratometria_oi(){
	document.getElementById('color_queratometria_oi').value = '#FFFFFF';
}





function mostrar_formulario_flotante(tipo){
	if(tipo==1){//mostrar
		$('#fondo_negro').css('display', 'block');
		$('#d_centro').slideDown(400).css('display', 'block');
	}
	else if(tipo==0){//Ocultar
		$('#fondo_negro').css('display', 'none');
		$('#d_centro').slideDown(400).css('display', 'none');
	}
}

function reducir_formulario_flotante(ancho, alto){ //480, 390
	$('.div_centro').width(ancho);
	$('.div_centro').height(alto);
	$('.div_centro').css('top', '20%');
	$('.div_interno').width(ancho-15);
	$('.div_interno').height(alto-35);
}

function validar_array(array, id) {
	var text = $(id).val();
	var ind_existe = 0;//No existe
	for (var i = 0; i < array.length; i++) {
		if (text == array[i]) {
			ind_existe = 1;//Si Existe
			break;  
		}
	}
	if (text == '') {
		ind_existe = 1;//Si Existe
	}
	if (ind_existe == 0) {
		alert('Valor incorrecto');
		document.getElementById(id.id).value="";
		input = id.id;
		setTimeout('document.getElementById(input).focus()',75); 
	}
}

function validar_array_locs3(obj_val_locs3) {
	var id_locs3 = $("#cmb_locs3").val();
	switch (id_locs3) {
		case "111": //No
			validar_array(array_locs3_no, obj_val_locs3);
			break;
			
		case "112": //Nc
			validar_array(array_locs3_nc, obj_val_locs3);
			break;
			
		case "113": //C
			validar_array(array_locs3_c, obj_val_locs3);
			break;
			
		case "114": //SCP
			validar_array(array_locs3_scp, obj_val_locs3);
			break;
	}
}

function mostrar_formulario(tipo) {
    if (tipo == 1) {//mostrar
        $('.formulario').slideDown(600).css('display', 'block')
    }
    else if (tipo == 0) {//Ocultar
        $('.formulario').slideUp(600).css('display', 'none')
    }
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta SIN VALIDAR LOS CAMPOS
 */
function guardar_consulta(tipo) {
	
	
	switch (tipo) 
	{ 
		case 1:
			$("#frm_consulta").validate({
				submitHandler: function() {
					$("#contenedor_error").css("display", "none");
					if (validar_consulta()) {
						editar_consulta(tipo);
					} else {
						$("#contenedor_error").css("display", "block");
						$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
					}
					return false;
	        	},
		    });
		break;
		case 2:
			editar_consulta(tipo);
		break;
		case 3:
			$("#frm_consulta").validate({
				submitHandler: function() {
					$("#contenedor_error").css("display", "none");
					if (validar_consulta()) {
						editar_consulta(tipo);
					} else {
						$("#contenedor_error").css("display", "block");
						$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
					}
					return false;
	        	},
		    });
		break;
		alert('Error tipo de guardado');
	}	
	
}

function validar_consulta() {
	var resultado = true;
	var panel_1 = 0;
	var panel_2 = 0;
	var panel_3 = 0;
	var panel_4 = 0;
	var panel_5 = 0;
	
	$("#panel_cata_1").removeClass("borde_error_panel");
	$("#panel_cata_1 a").css({"color": "#5B5B5B"});
	$("#panel_cata_2").removeClass("borde_error_panel");
	$("#panel_cata_2 a").css({"color": "#5B5B5B"});
	$("#panel_cata_3").removeClass("borde_error_panel");
	$("#panel_cata_3 a").css({"color": "#5B5B5B"});
	$("#panel_cata_4").removeClass("borde_error_panel");
	$("#panel_cata_4 a").css({"color": "#5B5B5B"});
	$("#panel_cata_5").removeClass("borde_error_panel");
	$("#panel_cata_5 a").css({"color": "#5B5B5B"});
	
	
	$("#cmb_locs3").removeClass("bordeAdmision");
	$("#txt_val_locs3").removeClass("bordeAdmision");
	$("#txt_val_rec_endotelial").removeClass("bordeAdmision");
	$("#txt_val_paquimetria").removeClass("bordeAdmision");
	$("#cmb_plegables").removeClass("bordeAdmision");
	$("#cmb_rigido").removeClass("bordeAdmision");
	$("#cmb_especiales").removeClass("bordeAdmision");
	$("#txt_evolucion").removeClass("bordeAdmision");
	$("#cmb_anestesia").removeClass("bordeAdmision");
	$("#txt_q_val_biometria_od").removeClass("bordeAdmision");
	$("#txt_q_eje_biometria_od").removeClass("bordeAdmision");
	$("#txt_q_val_iol_master_od").removeClass("bordeAdmision");
	$("#txt_q_eje_iol_master_od").removeClass("bordeAdmision");
	$("#txt_q_val_topografia_od").removeClass("bordeAdmision");
	$("#txt_q_eje_topografia_od").removeClass("bordeAdmision");
	$("#txt_q_val_definitiva_od").removeClass("bordeAdmision");
	$("#txt_q_eje_definitiva_od").removeClass("bordeAdmision");
	$("#txt_q_val_biometria_oi").removeClass("bordeAdmision");
	$("#txt_q_eje_biometria_oi").removeClass("bordeAdmision");
	$("#txt_q_val_iol_master_oi").removeClass("bordeAdmision");
	$("#txt_q_eje_iol_master_oi").removeClass("bordeAdmision");
	$("#txt_q_val_topografia_oi").removeClass("bordeAdmision");
	$("#txt_q_eje_topografia_oi").removeClass("bordeAdmision");
	$("#txt_q_val_definitiva_oi").removeClass("bordeAdmision");
	$("#txt_q_eje_definitiva_oi").removeClass("bordeAdmision");
	$("#hdd_img_queratometria_od").removeClass("bordeAdmision");
	$("#hdd_img_queratometria_oi").removeClass("bordeAdmision");
	$("#cmb_incision_arq").removeClass("bordeAdmision");
	$("#txt_val_incision_arq").removeClass("bordeAdmision");
	$("#txt_observaciones_preqx").removeClass("bordeAdmision");
	
	$("#ciex_diagnostico_1").removeClass("bordeAdmision");
	$("#valor_ojos_1").removeClass("bordeAdmision");
	var cant_ciex = $('#lista_tabla').val()
	for (i = 1; i <= cant_ciex; i++) {
	 	 $("#valor_ojos_" + i).removeClass("bordeAdmision");
	}
	
	if ($("#hdd_ciex_diagnostico_1").val() == "") {
		$("#ciex_diagnostico_1").addClass("bordeAdmision");
		resultado = false;
		panel_5=1;
	}
	if ($("#valor_ojos_1").val() == "") {
		$("#valor_ojos_1").addClass("bordeAdmision");
		resultado = false;
		panel_5=1;
	}
	for (i = 1; i <= cant_ciex; i++) {
		var cod_ciex = $("#hdd_ciex_diagnostico_"+i).val();
		var val_ojos = $("#valor_ojos_"+i).val();
		if (cod_ciex != '' && val_ojos == '') {
			$("#valor_ojos_" + i).addClass("bordeAdmision");
		 	resultado = false;
		 	panel_5=1;
		}
	}
	
    if ($("#cmb_locs3").val() == ""){
		$("#cmb_locs3").addClass("bordeAdmision");
		resultado = false;
		panel_1=1;
	}
    if ($("#txt_val_locs3").val() == ""){
		$("#txt_val_locs3").addClass("bordeAdmision");
		resultado = false;
		panel_1=1;
	}
    if ($("#txt_val_rec_endotelial").val() == ""){
		$("#txt_val_rec_endotelial").addClass("bordeAdmision");
		resultado = false;
		panel_1=1;
	}
    if ($("#txt_val_paquimetria").val() == ""){
		$("#txt_val_paquimetria").addClass("bordeAdmision");
		resultado = false;
		panel_1=1;
	}
    if ($("#cmb_plegables").val() == "" && $("#cmb_rigido").val() == "" && $("#cmb_especiales").val() == ""){
		$("#cmb_plegables").addClass("bordeAdmision");
		$("#cmb_rigido").addClass("bordeAdmision");
		$("#cmb_especiales").addClass("bordeAdmision");
		resultado = false;
		panel_1=1;
	}
    if ($("#txt_evolucion").val() == ""){
		$("#txt_evolucion").addClass("bordeAdmision");
		resultado = false;
		panel_2=1;
	}
    if ($("#cmb_anestesia").val() == ""){
		$("#cmb_anestesia").addClass("bordeAdmision");
		resultado = false;
		panel_2=1;
	}
	
	if (bol_od) {
	    if ($("#txt_q_val_biometria_od").val() == ""){
			$("#txt_q_val_biometria_od").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_eje_biometria_od").val() == ""){
			$("#txt_q_eje_biometria_od").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_val_iol_master_od").val() == ""){
			$("#txt_q_val_iol_master_od").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_eje_iol_master_od").val() == ""){
			$("#txt_q_eje_iol_master_od").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_val_topografia_od").val() == ""){
			$("#txt_q_val_topografia_od").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_eje_topografia_od").val() == ""){
			$("#txt_q_eje_topografia_od").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_val_definitiva_od").val() == ""){
			$("#txt_q_val_definitiva_od").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_eje_definitiva_od").val() == ""){
			$("#txt_q_eje_definitiva_od").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	}
	if (bol_oi) {
	    if ($("#txt_q_val_biometria_oi").val() == ""){
			$("#txt_q_val_biometria_oi").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_eje_biometria_oi").val() == ""){
			$("#txt_q_eje_biometria_oi").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_val_iol_master_oi").val() == ""){
			$("#txt_q_val_iol_master_oi").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_eje_iol_master_oi").val() == ""){
			$("#txt_q_eje_iol_master_oi").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_val_topografia_oi").val() == ""){
			$("#txt_q_val_topografia_oi").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_eje_topografia_oi").val() == ""){
			$("#txt_q_eje_topografia_oi").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_val_definitiva_oi").val() == ""){
			$("#txt_q_val_definitiva_oi").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	    if ($("#txt_q_eje_definitiva_oi").val() == ""){
			$("#txt_q_eje_definitiva_oi").addClass("bordeAdmision");
			resultado = false;
			panel_3=1;
		}
	}
	
    if ($("#cmb_incision_arq").val() == ""){
		$("#cmb_incision_arq").addClass("bordeAdmision");
		resultado = false;
		panel_3=1;
	}
    if ($("#cmb_incision_arq").val() == "1" && $("#txt_val_incision_arq").val() == ""){
		$("#txt_val_incision_arq").addClass("bordeAdmision");
		resultado = false;
		panel_3=1;
	}
	
	if(panel_1 == 1){
	   $("#panel_cata_1").addClass("borde_error_panel");
	   $("#panel_cata_1 a").css({"color": "#FF002A"});
	}
	if(panel_2 == 1){
	   $("#panel_cata_2").addClass("borde_error_panel");
	   $("#panel_cata_2 a").css({"color": "#FF002A"});
	}
	if(panel_3 == 1){
	   $("#panel_cata_3").addClass("borde_error_panel");
	   $("#panel_cata_3 a").css({"color": "#FF002A"});
	}
	if(panel_4 == 1){
	   $("#panel_cata_4").addClass("borde_error_panel");
	   $("#panel_cata_4 a").css({"color": "#FF002A"});
	}
	if(panel_5 == 1){
	   $("#panel_cata_5").addClass("borde_error_panel");
	   $("#panel_cata_5 a").css({"color": "#FF002A"});
	}
	
	return resultado;
}

/**
 *tipo:
 * 1=Guardar y cambiar de estado la consulta
 * 2=Guardar y NO cambiar el estado de la consulta 
 */
function editar_consulta(tipo){
	var params = "opcion=1&id_hc=" + $("#hdd_id_hc_consulta").val() +
				 "&id_admision=" + $("#hdd_id_admision").val() +
				 "&id_locs3=" + $("#cmb_locs3").val() +
				 "&val_locs3=" + $("#txt_val_locs3").val() +
				 "&val_rec_endotelial=" + $("#txt_val_rec_endotelial").val() +
				 "&val_paquimetria=" + $("#txt_val_paquimetria").val() +
				 "&id_plegables=" + $("#cmb_plegables").val() +
				 "&id_rigido=" + $("#cmb_rigido").val() +
				 "&id_especiales=" + $("#cmb_especiales").val() +
				 "&texto_evolucion=" + str_encode($("#txt_evolucion").val()) +
				 "&id_anestesia=" + $("#cmb_anestesia").val() +
				 "&querato_val_biometria_od=" + $("#txt_q_val_biometria_od").val() +
				 "&querato_eje_biometria_od=" + $("#txt_q_eje_biometria_od").val() +
				 "&querato_val_iol_master_od=" + $("#txt_q_val_iol_master_od").val() +
				 "&querato_eje_iol_master_od=" + $("#txt_q_eje_iol_master_od").val() +
				 "&querato_val_topografia_od=" + $("#txt_q_val_topografia_od").val() +
				 "&querato_eje_topografia_od=" + $("#txt_q_eje_topografia_od").val() +
				 "&querato_val_definitiva_od=" + $("#txt_q_val_definitiva_od").val() +
				 "&querato_eje_definitiva_od=" + $("#txt_q_eje_definitiva_od").val() +
				 "&querato_val_biometria_oi=" + $("#txt_q_val_biometria_oi").val() +
				 "&querato_eje_biometria_oi=" + $("#txt_q_eje_biometria_oi").val() +
				 "&querato_val_iol_master_oi=" + $("#txt_q_val_iol_master_oi").val() +
				 "&querato_eje_iol_master_oi=" + $("#txt_q_eje_iol_master_oi").val() +
				 "&querato_val_topografia_oi=" + $("#txt_q_val_topografia_oi").val() +
				 "&querato_eje_topografia_oi=" + $("#txt_q_eje_topografia_oi").val() +
				 "&querato_val_definitiva_oi=" + $("#txt_q_val_definitiva_oi").val() +
				 "&querato_eje_definitiva_oi=" + $("#txt_q_eje_definitiva_oi").val() +
				 "&img_queratometria_od=" + $("#hdd_img_queratometria_od").val() +
				 "&img_queratometria_oi=" + $("#hdd_img_queratometria_oi").val() +
				 "&ind_incision_arq=" + $("#cmb_incision_arq").val() +
				 "&val_incision_arq=" + $("#txt_val_incision_arq").val() +
				 "&observaciones_preqx=" + str_encode($("#txt_observaciones_preqx").val()) +
				 "&diagnostico_preqx_catarata=" + str_encode($("#diagnostico_preqx_catarata").val()) +
				 "&solicitud_examenes_preqx_catarata=" + str_encode($("#solicitud_examenes_preqx_catarata").val()) +
				 "&tratamiento_preqx_catarata=" + str_encode($("#tratamiento_preqx_catarata").val()) +
				 "&medicamentos_preqx_catarata=" + str_encode($("#medicamentos_preqx_catarata").val()) +
				 "&tipo_guardar=" + tipo;
	
	//Para Diagnosticos
	var cant_ciex = $('#lista_tabla').val()
	params += '&cant_ciex=' + cant_ciex;
	for (i = 1; i <= cant_ciex; i++) {
		var cod_ciex = $("#hdd_ciex_diagnostico_" + i).val();
		var val_ojos = $("#valor_ojos_" + i).val();
		if (cod_ciex != '') {
			params += '&cod_ciex_' + i + '=' + cod_ciex +
					  '&val_ojos_' + i + '=' + val_ojos;
		}
	}
	
	llamarAjax("consulta_preqx_catarata_ajax.php", params, "d_guardar_consulta", "validar_exito()");
}

function validar_exito() {
	var hdd_exito = $('#hdd_exito').val();
	var hdd_url_menu = $('#hdd_url_menu').val();
	var hdd_tipo_guardar = $('#hdd_tipo_guardar').val();
	
	if (hdd_tipo_guardar == 1) { //Cierra el formulario
		if (hdd_exito > 0) {
			$('#frm_consulta').css('display', 'none');
			$("#contenedor_exito").css("display", "block");
			$('#contenedor_exito').html('Datos guardados correctamente');
			setTimeout("enviar_credencial('" + hdd_url_menu + "')", 3000);
		} else {
			$("#contenedor_error").css("display", "block");
			$('#contenedor_error').html('Error al guardar la evoluci&oacute;n');
		}
	} else if (hdd_tipo_guardar == 2) { //Permanece en el formulario
		if (hdd_exito > 0) {
			$("#contenedor_exito").css("display", "block");
			$('#contenedor_exito').html('Datos guardados correctamente');
			setTimeout('$("#contenedor_exito").css("display", "none")', 3000);
		} else {
			$("#contenedor_error").css("display", "block");
			$('#contenedor_error').html('Error al guardar la evoluci&oacute;n');
		}
	} else if (hdd_tipo_guardar == 3) { //Permanece en el formulario
		if (hdd_exito > 0) {
			$("#contenedor_exito").css("display", "block");
			$('#contenedor_exito').html('Datos guardados correctamente');
			setTimeout('$("#contenedor_exito").css("display", "none")', 3000);
		} else {
			$("#contenedor_error").css("display", "block");
			$('#contenedor_error').html('Error al guardar la evoluci&oacute;n');
		}
	}
	
}

function cambiar_lista_locs3(id_locs3) {
	$("#txt_val_locs3").val("");
	$(function() {
		switch (id_locs3) {
			case "111": //No
				$("#txt_val_locs3").autocomplete({ source: array_locs3_no });
				break;
				
			case "112": //Nc
				$("#txt_val_locs3").autocomplete({ source: array_locs3_nc });
				break;
				
			case "113": //C
				$("#txt_val_locs3").autocomplete({ source: array_locs3_c });
				break;
				
			case "114": //SCP
				$("#txt_val_locs3").autocomplete({ source: array_locs3_scp });
				break;
		}
	});
}

function seleccionar_incision_arq(ind_incision_arq) {
	switch (ind_incision_arq) {
		case "1":
			$("#txt_val_incision_arq").attr("disabled", false);
			if (trim($("#txt_val_paquimetria").val()) != "") {
				var val_incision_arq_aux = Math.round(parseInt($("#txt_val_paquimetria").val(), 10) * 0.8);
				$("#txt_val_incision_arq").val(val_incision_arq_aux);
			}
			break;
			
		case "0":
			$("#txt_val_incision_arq").attr("disabled", true);
			$("#txt_val_incision_arq").val("");
			break;
	}
}
