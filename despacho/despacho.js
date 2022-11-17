function cargar_formula(texto_formula) {
	//Tomar el texto del editor
	var text_despacho = $("div.nicEdit-main").html();
	var texto_descripcion_formula = text_despacho+'<br />'+texto_formula;
	//cargar al editor
	$("div.nicEdit-main").html(texto_descripcion_formula);
	//Cargar a la caja de texto
	$('#text_despacho').val(texto_descripcion_formula);
}


function cargar_formula_id(){
	
	var cod_formula = $('#texto_cod_formula').val();
	var texto_formula = $('#hdd_formula_'+cod_formula).val();
	
	if(texto_formula !=undefined){	
		//Tomar el texto del editor
		var text_despacho = $("div.nicEdit-main").html();
		var texto_descripcion_formula = text_despacho+'<br />'+texto_formula;
		//cargar al editor
		$("div.nicEdit-main").html(texto_descripcion_formula);
		//Cargar a la caja de texto
		$('#text_despacho').val(texto_descripcion_formula);
	}
}



function guardar_despacho(id_tipo){
	
	var result = 0;
	$('#text_despacho').removeClass("borde_error");
	$("#contenedor_error").css("display", "none");
	var text_despacho_1 = $('#text_despacho').val();
	var text_despacho_2 = $("div.nicEdit-main").html();
	if(text_despacho_2=='<br>'){$("div.nicEdit-main").addClass("borde_error");result=1;}
	if(result == 0){
		//var text_despacho = $('#text_despacho').val();
		var text_despacho = str_encode($("div.nicEdit-main").html());
		var hdd_id_paciente = $('#hdd_id_paciente').val();
		var hdd_id_admision = $('#hdd_id_admision').val();
		var hdd_nombre_paciente = $('#hdd_nombre_paciente').val();
        var hdd_fecha_admision = $('#hdd_fecha_admision').val();
        var hdd_documento_paciente = $('#hdd_documento_paciente').val();
        var hdd_nombre_profesional = $('#hdd_nombre_profesional').val();
        var text_remitido = str_encode($('#text_remitido').val());
        
        if($("#tipo_impresion").is(':checked')) {  
            var tipo_impresion = 2;  //Ecopetrol
        } else {  
            var tipo_impresion = 1; //Consultorio
        }  
	    var params='opcion=1' +
				   '&text_despacho='+ text_despacho +
				   '&hdd_id_paciente='+ hdd_id_paciente +
				   '&hdd_id_admision='+ hdd_id_admision +
				   '&hdd_nombre_paciente='+ hdd_nombre_paciente +
				   '&hdd_fecha_admision='+ hdd_fecha_admision +
				   '&hdd_documento_paciente='+ hdd_documento_paciente +
				   '&tipo_impresion='+ tipo_impresion +
				   '&hdd_nombre_profesional='+ hdd_nombre_profesional +
				   '&text_remitido='+ text_remitido +
				   '&id_tipo='+ id_tipo;
				   
		llamarAjax("despacho_ajax.php", params, "guardar_despacho", "validar_exito("+id_tipo+")");   
	}
	else{
		$("#contenedor_error").css("display", "block");
    	$('#contenedor_error').html('Los campos marcados en rojo son obligatorios');
		return false;
	}
}

function validar_exito(id_tipo) {

    var hdd_exito = $('#hdd_exito').val();
    var hdd_url_menu = $('#hdd_url_menu').val();
    
    if (hdd_exito > 0) {
    	
    	if(id_tipo == 2){//Imprimir
    		$("#contenedor_exito").css("display", "block");                
		    $('#contenedor_exito').html('Datos guardados correctamente');
		    imprSelec('campo_imprimir');	
	    }
	    else if(id_tipo == 1){//Guarda y finaliza No imprimir
	    	$("#contenedor_exito").css("display", "block");                
		    $('#contenedor_exito').html('Datos guardados correctamente');  
		    setTimeout("enviar_credencial('"+hdd_url_menu+"')", 3000);
		    
	    }
	    else if(id_tipo == 3){//Solo Guarda
	    	$("#contenedor_exito").css("display", "block");                
		    $('#contenedor_exito').html('Datos guardados correctamente');
		    setTimeout('$("#contenedor_exito").css("display", "block");', 3000);
	    }
	    else if(id_tipo == 4){//Solo Imprime
	    	imprSelec('campo_imprimir');
	    }	       
	}                                                                  
	else {                                                             
	    $("#contenedor_error").css("display", "block");                
	    $('#contenedor_error').html('Error al guardar usuarios');      
	    setTimeout("enviar_credencial('"+hdd_url_menu+"')", 3000);     
	}                                                                  }


function imprSelec(muestra){
	
	var ficha=document.getElementById(muestra);
	var ventimp=window.open(' ','popimpr');
	ventimp.document.write(ficha.innerHTML);
	ventimp.document.close();
	ventimp.print();
	ventimp.close();
}


function mostrar_remitido(){
	if($("#tipo_impresion").is(':checked')) {  
		$("#div_remitido").css("display", "block");
    } else {  
        $("#div_remitido").css("display", "none");
    }
} 


function seleccionar_formula(){
	var tipo_formula_gafas = $('#tipo_formula_gafas').val();
	if(tipo_formula_gafas == 1){//Subjetivo
		$("#tabla_subjetivo").css("display", "");
		$("#tabla_refrafinal").css("display", "none");
		
	}
	if(tipo_formula_gafas == 2){//Refracci&oacute;n final
		$("#tabla_subjetivo").css("display", "none");
		$("#tabla_refrafinal").css("display", "");
	}
	if(tipo_formula_gafas == ''){//Subjetivo
		$("#tabla_subjetivo").css("display", "none");
		$("#tabla_refrafinal").css("display", "none");
		
	}
	
}


function imprimir_formula_gafas(esfera_od, cilindro_od, eje_od, adicion_od, esfera_oi, cilindro_oi, eje_oi, adicion_oi, observacion){
	
	var hdd_nombre_paciente = $('#hdd_nombre_paciente').val();
    var hdd_fecha_admision = $('#hdd_fecha_admision').val();
    
    var params='opcion=2' +
			   '&hdd_nombre_paciente='+ hdd_nombre_paciente +
			   '&hdd_fecha_admision='+ hdd_fecha_admision +
			   '&esfera_od='+ esfera_od +
			   '&cilindro_od='+ cilindro_od +
			   '&eje_od='+ eje_od +
			   '&adicion_od='+ adicion_od +
			   '&esfera_oi='+ esfera_oi +
			   '&cilindro_oi='+ cilindro_oi +
			   '&eje_oi='+ eje_oi +
			   '&adicion_oi='+ adicion_oi +
			   '&observacion='+ observacion;
	llamarAjax("despacho_ajax.php", params, "imprimir_formula", "imprSelec(\"campo_imprimir_formula\")");  
    
	
}

;