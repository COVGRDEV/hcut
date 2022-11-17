var g_max_reg_remision = 20;

$(document).ready(function() {
	//Carga el listado de tipos de citas
	mostrar_tipos_citas();
});

function mostrar_tipos_citas() {
	$("#contenedor_error").css("display", "none");//Oculta el mensaje de error en caso de estar visible
	$("#contenedor_exito").css("display", "none");//Oculta el mensaje de exito en caso de estar visible
	
	//Limpia el input
	$("#txtParametro").val("");
	
	var params = "opcion=1&parametro=-1";
	
	llamarAjax("tipos_citas_ajax.php", params, "reporte_citas", "");
}

//Funcion que busca un convenio especifico
function buscarTipoCita() {
	$("#frmBuscarTipoCita").validate({
		rules: {
			txtParametro: {
				required: true,
			},
		},
		submitHandler: function() {
			$("#contenedor_error").css("display", "none");//Oculta el mensaje de error en caso de estar visible
			$("#contenedor_exito").css("display", "none");//Oculta el mensaje de exito en caso de estar visible
			
			var parametro = $("#txtParametro").val();
			var params = "opcion=1&parametro=" + str_encode(parametro);
			llamarAjax("tipos_citas_ajax.php", params, "reporte_citas", "");
			
			return false;
		},
	});
}

//Imprime el formulario para un nuevo tipo de cita
function nuevoTipoCita() {
	var params = "opcion=2";
	llamarAjax("tipos_citas_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1);postNuevoTipoCita();");
}

function postNuevoTipoCita() {
	$("#cmb_tipos_registros_hc").attr("disabled", "disabled");
}

function validar_tipo_cita() {
	if ($("#txt_nombre_tipo_cita").val() == "") {
		alert("Debe ingresar el nombre del tipo de cita");
		return false;
	}
	
	if ($("#chk_preqx").is(":checked") && $("#cmb_tipo_reg_cx").val() == "") {
		alert("Debe seleccionar el tipo de registro prequir\xfargico");
		return false;
	}
	
	return true;
}

//Funcion que guarda un nuevo tipo de cita
function guardarNuevoTipoCita() {
	if (validar_tipo_cita()) {
		var nombre_tipo_cita = str_encode($("#txt_nombre_tipo_cita").val());
		var ind_activo = $("#chk_activo").is(":checked") ? 1 : 0;
		var ind_preconsulta = $("#chk_preconsulta").is(":checked") ? 1 : 0;
		var ind_examenes = $("#chk_examenes").is(":checked") ? 1 : 0;
		var ind_signos_vitales = $("#chk_signos_vitales").is(":checked") ? 1 : 0;
		var ind_preqx = $("#chk_preqx").is(":checked") ? 1 : 0;
		var ind_despacho = $("#chk_despacho").is(":checked") ? 1 : 0;

		var id_tipo_reg_cx = "";
		if ($("#chk_preqx").is(":checked")) {
			id_tipo_reg_cx = $("#cmb_tipo_reg_cx").val();
		}
		
		var params = "opcion=3&accion=0&id_tipo_cita=" +
					 "&nombre_tipo_cita=" + nombre_tipo_cita +
					 "&ind_activo=" + ind_activo +
					 "&ind_preconsulta=" + ind_preconsulta +
					 "&ind_examenes=" + ind_examenes +
					 "&ind_signos_vitales=" + ind_signos_vitales +
					 "&ind_preqx=" + ind_preqx +
					 "&ind_despacho=" + ind_despacho +
					 "&id_tipo_reg_cx=" + id_tipo_reg_cx;
		
		llamarAjax("tipos_citas_ajax.php", params, "d_guardar_tipo_cita", "validar_crear_modificar_tipo_cita();");
	}
}

//Funcion que guarda un nuevo tipo de cita
function modificaTipoCita() {
	if (validar_tipo_cita()) {
		var nombre_tipo_cita = str_encode($("#txt_nombre_tipo_cita").val());
		var ind_activo = $("#chk_activo").is(":checked") ? 1 : 0;
		var ind_preconsulta = $("#chk_preconsulta").is(":checked") ? 1 : 0;
		var ind_examenes = $("#chk_examenes").is(":checked") ? 1 : 0;
		var ind_signos_vitales = $("#chk_signos_vitales").is(":checked") ? 1 : 0;
		var ind_preqx = $("#chk_preqx").is(":checked") ? 1 : 0;
		var ind_despacho = $("#chk_despacho").is(":checked") ? 1 : 0;
		var id_tipo_reg_cx = "";
		
		if ($("#chk_preqx").is(":checked")) {
			id_tipo_reg_cx = $("#cmb_tipo_reg_cx").val();
		}
		
		var params = "opcion=3&accion=1&id_tipo_cita=" + $("#hdd_id_tipo_cita").val() +
					 "&nombre_tipo_cita=" + nombre_tipo_cita +
					 "&ind_activo=" + ind_activo +
					 "&ind_preconsulta=" + ind_preconsulta +
					 "&ind_examenes=" + ind_examenes +
					 "&ind_signos_vitales=" + ind_signos_vitales +
					 "&ind_preqx=" + ind_preqx +
					 "&ind_despacho=" + ind_despacho +
					 "&id_tipo_reg_cx=" + id_tipo_reg_cx;
		
		llamarAjax("tipos_citas_ajax.php", params, "d_guardar_tipo_cita", "validar_crear_modificar_tipo_cita();");
	}
}

//Funcion que verifica si se ha guardado el registro 
function validar_crear_modificar_tipo_cita() {
	var resultado_aux = parseInt($("#hdd_resultado_guardar").val(), 10);
	
	mostrar_formulario_flotante(0);
	if (resultado_aux > 0) {
		mostrar_tipos_citas();
		$("#contenedor_exito").css({"display": "block"});
		$("#contenedor_exito").html("Registro guardado con &eacute;xito");
	} else {
		$("#contenedor_error").css({"display": "block"});
		$("#contenedor_error").html("Error interno al guardar el registro");
	}
	window.scrollTo(0, 0);
}

function tiposCitaDetalle(idTipoCita, idTipoRegistro) {
	var params = "opcion=5&tipo=2&idTipoCita=" + idTipoCita +
				 "&idTipoRegistro=" + idTipoRegistro;
	
	llamarAjax("tipos_citas_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1); verificaIconoBorrarProcedimiento();");
}

//Funcion que verirfica si muestra el icono para Borrar el procedimiento
function verificaIconoBorrarProcedimiento() {
	var procedimiento = $("#hdd_Procedimiento").val();
	if (procedimiento == "") {
		ocultaBtnEliminarProcedimiento();
	}
}

function tiposCitaDetalleNuevo(idTipoCita) {
	var params = "opcion=5&tipo=1&idTipoCita=" + idTipoCita;
	
	llamarAjax("tipos_citas_ajax.php", params, "d_interno", "mostrar_formulario_flotante(1); verificaIconoBorrarProcedimiento();");
}

//Muestra la venata flotante para seleccionar un procedimiento
function seleccionarProcedimiento(tipo) {
	if (tipo == 1) {//mostrar
		$("#fondo_negro_procedimientos").css("display", "block");
		$("#d_centro_procedimientos").slideDown(400).css({"display": "block", "z-index": "10"});
		
		//Asigna el alto por defecto a la página
		$("#d_interno_procedimientos").css({"min-height": "470px"});
		
		//Envia por ajax la peticion para construir el formulario flotante
		var params = "opcion=6";
		llamarAjax("tipos_citas_ajax.php", params, "d_interno_procedimientos", "");
	} else if (tipo == 0) {//Ocultar
		$("#d_centro_procedimientos").css("display", "none");
		$("#fondo_negro_procedimientos").css("display", "none");
	}
}

//Funcion que muestra el listado de procedimientos
function buscarProcedimiento() {
	$("#frmBuscarProcedimiento").validate({
		rules: {
			txtParametro: {
				required: true,
			},
		},
		submitHandler: function() {
			var parametro = $("#txtProcedimiento").val();
			var params = "opcion=7&parametro=" + str_encode(parametro);
			llamarAjax("tipos_citas_ajax.php", params, "procedimientos", "muestraBtnEliminarProcedimiento();");
			
			return false;
		},
	});
}

function ocultaBtnEliminarProcedimiento() {
	$("#btnEliminarProcedimiento").css({"display": "none"});
}

function muestraBtnEliminarProcedimiento() {
	$("#btnEliminarProcedimiento").css({"display": "block"});
}

//Muestra la venata flotante para seleccionar un procedimiento
function seleccionarEstadoAtencion(tipo) {
	if (tipo == 1) {//mostrar
		$("#fondo_negro_procedimientos").css("display", "block");
		$("#d_centro_procedimientos").slideDown(400).css({"display": "block", "z-index": "10"});
		
		//Asigna el alto por defecto a la página
		$("#d_interno_procedimientos").css({"min-height": "470px"});
		
		//Envia por ajax la peticion para construir el formulario flotante
		var params = "opcion=8";
		llamarAjax("tipos_citas_ajax.php", params, "d_interno_procedimientos", "");
	} else if (tipo == 0) { //Ocultar
		$("#d_centro_procedimientos").css("display", "none");
		$("#fondo_negro_procedimientos").css("display", "none");
	}
}

//Muestra la venata flotante para seleccionar un procedimiento
function seleccionarTipoRegistro(tipo) {
	if (tipo == 1) {//mostrar
		$("#fondo_negro_procedimientos").css("display", "block");
		$("#d_centro_procedimientos").slideDown(400).css({"display": "block", "z-index": "10"});
		
		//Asigna el alto por defecto a la página
		$("#d_interno_procedimientos").css({"min-height": "470px"});
		
		//Envia por ajax la peticion para construir el formulario flotante
		var params = "opcion=9";
		llamarAjax("tipos_citas_ajax.php", params, "d_interno_procedimientos", "");
	} else if (tipo == 0) {//Ocultar
		$("#d_centro_procedimientos").css("display", "none");
		$("#fondo_negro_procedimientos").css("display", "none");
	}
}

//Funcion que guarda el tipo de cita detalle 
function guardarTipoCitaDetalle(id_tipo_cita, id_tipo_reg_ori) {
	if (validar_guardar_tipo_cita_detalle()) {
		var id_tipo_reg = $("#hdd_TipoRegistro").val();
		var orden = $("#txtOrden").val();
		var estadoAtencion = $("#hdd_EstadoAtencion").val();
		var procedimiento = $("#hdd_Procedimiento").val();
		var cmb_usuarioAlt = $("#cmb_usuarioAlt").val();
		var cmb_obligatorio = $("#cmb_obligatorio").val();
		
		//Recorre la tabla de Tipos de cita detalle.
		var filas = $("#tablaTiposCitaDetalles tr").length;
		var pasaporte = true; //variable que define si se puede o no guardar el objeto
		
		//Verifica si es posible agregar el elemento seleccionado
		for (i = 2; i <= (filas - 1); i++) {//Inicia en 2 la variable i porque es necesario quitar los 2 tr de informacion del principio de la tabla
			//Valida si puede guardar o no el numero de orden con base en los que ya estan guardados
			if ($("#hdd_TipoRegistroOri").val() == $("#tablaTiposCitaDetalles tr:eq(" + i + ")").attr("id")) {
				//Valida que el Orden se pueda agregar
				if ($("#txtOrden").val() == $.trim($("#tablaTiposCitaDetalles tr:eq(" + i + ") #orden").html())) {
					break;
				}
			} else {
				//Valida que el Orden se pueda agregar
				if ($("#txtOrden").val() == $.trim($("#tablaTiposCitaDetalles tr:eq(" + i + ") #orden").html())) {
					alert("Debe seleccionar otro numero de orden");
					pasaporte = false;
					break;
				}
			}
		}
		
		//Verifica si puede guardar
		if (pasaporte) {
			var params = "opcion=10&id_tipo_cita=" + id_tipo_cita +
						 "&id_tipo_reg_ori=" + id_tipo_reg_ori +
						 "&id_tipo_reg=" + id_tipo_reg +
						 "&accion=3&estadoAtencion=" + estadoAtencion +
						 "&procedimiento=" + procedimiento +
						 "&orden=" + str_encode(orden) +
						 "&usuarioAlt=" + cmb_usuarioAlt +
						 "&ind_obligatorio=" + cmb_obligatorio;
			
			var cant_reg_remision = parseInt($("#hdd_cant_reg_remision").val(), 10);
			var cont_aux = 0;
			for (var i = 0; i < cant_reg_remision; i++) {
				if ($("#tr_det_remision_" + i).is(":visible")) {
					params += "&id_tipo_cita_dest_" + cont_aux + "=" + $("#cmb_tipo_cita_dest_" + i).val() +
							  "&id_tipo_reg_hc_dest_" + cont_aux + "=" + $("#cmb_tipo_reg_hc_dest_" + i).val();
					
					cont_aux++;
				}
			}
			params += "&cant_reg_remision=" + cont_aux;
			
			llamarAjax("tipos_citas_ajax.php", params, "hdd_resultado_agregar", "postEliminarTipoCitaDetalle(" + id_tipo_cita + ")");
		}
	} else {
		$("#d_contenedor_error_rem").css("display", "block");
		$("#d_contenedor_error_rem").html("Los campos marcados en rojo son obligatorios");
		window.scroll(0, 0);
	}
}

function validar_guardar_tipo_cita_detalle() {
	var resultado = true;
	$("#d_contenedor_error_rem").css("display", "none");
	$("#txtTipoRegistro").removeClass("borde_error");
	$("#txtEstadoAtencion").removeClass("borde_error");
	$("#cmb_usuarioAlt").removeClass("borde_error");
	$("#cmb_obligatorio").removeClass("borde_error");
	$("#txtOrden").removeClass("borde_error");
	
	for (var i = 0; i < g_max_reg_remision; i++) {
		$("#cmb_tipo_cita_dest_" + i).removeClass("borde_error");
		$("#cmb_tipo_reg_hc_dest_" + i).removeClass("borde_error");
	}
	
	if ($("#hdd_TipoRegistro").val() == "") {
		$("#txtTipoRegistro").addClass("borde_error");
		resultado = false;
	}
	if ($("#hdd_EstadoAtencion").val() == "") {
		$("#txtEstadoAtencion").addClass("borde_error");
		resultado = false;
	}
	if ($("#cmb_usuarioAlt").val() == "") {
		$("#cmb_usuarioAlt").addClass("borde_error");
		resultado = false;
	}
	if ($("#cmb_obligatorio").val() == "") {
		$("#cmb_obligatorio").addClass("borde_error");
		resultado = false;
	}
	if ($("#txtOrden").val() == "") {
		$("#txtOrden").addClass("borde_error");
		resultado = false;
	}
	
	var cant_reg_remision = parseInt($("#hdd_cant_reg_remision").val(), 10);
	for (var i = 0; i < cant_reg_remision; i++) {
		if ($("#tr_det_remision_" + i).is(":visible")) {
			if ($("#cmb_tipo_cita_dest_" + i).val() == "") {
				$("#cmb_tipo_cita_dest_" + i).addClass("borde_error");
				resultado = false;
			}
			if ($("#cmb_tipo_reg_hc_dest_" + i).val() == "") {
				$("#cmb_tipo_reg_hc_dest_" + i).addClass("borde_error");
				resultado = false;
			}
		}
	}
	
	return resultado;
}

//Funcion que guarda el tipo de cita detalle 
function nuevoTipoCitaDetalle(id_tipo_cita) {
	if (validar_guardar_tipo_cita_detalle()) {
		var orden = $("#txtOrden").val();
		var id_tipo_reg = $("#hdd_TipoRegistro").val();
		var estadoAtencion = $("#hdd_EstadoAtencion").val();
		var procedimiento = $("#hdd_Procedimiento").val();
		var cmb_usuarioAlt = $("#cmb_usuarioAlt").val();
		var cmb_obligatorio = $("#cmb_obligatorio").val();
		
		//Recorre la tabla de Tipos de cita detalle.
		var filas = $("#tablaTiposCitaDetalles tr").length;
		var pasaporte = true; //variable que define si se puede o no guardar el objeto
		
		//Verifica si es posible agregar el elemento seleccionado
		for (i = 2; i <= (filas - 1); i++) {//Inicia en 2 la variable i porque es necesario quitar los 2 tr de informacion del principio de la tabla
			//Valida si puede guardar o no el numero de orden con base en los que ya estan guardados
			if ($("#hdd_TipoRegistro").val() == $("#tablaTiposCitaDetalles tr:eq(" + i + ")").attr("id")) {
				alert("Error! El tipo de registro ya existe");
				pasaporte = false;
				break;
			} else {
				//Valida que el Orden se pueda agregar
				if ($("#txtOrden").val() == $.trim($("#tablaTiposCitaDetalles tr:eq(" + i + ") #orden").html())) {
					alert("Debe seleccionar otro numero de orden");
					pasaporte = false;
					break;
				}
			}
		}
		
		//Verifica si puede guardar
		if (pasaporte) {
			var params = "opcion=10&id_tipo_cita=" + id_tipo_cita +
						 "&id_tipo_reg_ori=" +
						 "&id_tipo_reg=" + id_tipo_reg +
						 "&accion=1&estadoAtencion=" + estadoAtencion +
						 "&procedimiento=" + procedimiento +
						 "&orden=" + str_encode(orden) +
						 "&usuarioAlt=" + cmb_usuarioAlt +
						 "&ind_obligatorio=" + cmb_obligatorio;
			
			var cant_reg_remision = parseInt($("#hdd_cant_reg_remision").val(), 10);
			var cont_aux = 0;
			for (var i = 0; i < cant_reg_remision; i++) {
				if ($("#tr_det_remision_" + i).is(":visible")) {
					params += "&id_tipo_cita_dest_" + cont_aux + "=" + $("#cmb_tipo_cita_dest_" + i).val() +
							  "&id_tipo_reg_hc_dest_" + cont_aux + "=" + $("#cmb_tipo_reg_hc_dest_" + i).val();
					
					cont_aux++;
				}
			}
			params += "&cant_reg_remision=" + cont_aux;
			
			llamarAjax("tipos_citas_ajax.php", params, "hdd_resultado_agregar", "postEliminarTipoCitaDetalle(" + id_tipo_cita + ")");
		}
	} else {
		$("#d_contenedor_error_rem").css("display", "block");
		$("#d_contenedor_error_rem").html("Los campos marcados en rojo son obligatorios");
		window.scroll(0, 0);
	}
}

//Agrega un tipo de registro HC
function agregarTipoRegistroHC(id, nombre) {
	//Agrega los valores
	$("#txtTipoRegistro").text(nombre);
	$("#hdd_TipoRegistro").val(id);
	seleccionarProcedimiento(0);//Cierra el div flotante
}

//Advertencia para elimianr objeto
function eliminarTipoCitaDetalle(id_tipo_cita, id_tipo_reg_ori) {
	if (confirm("Desea eliminar el elemento?")) {
		var params = "opcion=10&id_tipo_cita=" + id_tipo_cita +
					 "&id_tipo_reg_ori=" + id_tipo_reg_ori +
					 "&accion=2&estadoAtencion=&procedimiento=&orden=&usuarioAlt=&ind_obligatorio=&cant_reg_remision=0";
		
		llamarAjax("tipos_citas_ajax.php", params, "hdd_resultado_agregar", "postEliminarTipoCitaDetalle(" + id_tipo_cita + ")");
	}
}

//Funcion que verifica si el objeto ha sido eliminado de forma exitosa
function postEliminarTipoCitaDetalle(id_tipo_cita) {
	if ($("#hdd_resultado_agregar").text() == "1") {
		cerrar_div_centro();//Oculta el div flotante
		muestraTipoCita(id_tipo_cita);
	} else if ($("#hdd_resultado_agregar").text() == "-1") {
		alert("Error interno del servidor. Intentar de nuevo");
	}
}

function muestraTipoCita(id) {
	$("#contenedor_error").css("display", "none");//Oculta el mensaje de error en caso de estar visible
	$("#contenedor_exito").css("display", "none");//Oculta el mensaje de exito en caso de estar visible
	
	var params = "opcion=4&idTipoCita=" + id;
	llamarAjax("tipos_citas_ajax.php", params, "reporte_citas", "postMuestraTipoCita();");
}

function postMuestraTipoCita() {
	if ($("#indPreq").is(":checked")) {
		//Muestra el combo box
		$("#cmb_tipos_registros_hc").attr("disabled", false); 
	} else {
		//oculta el combo box       
		$("#cmb_tipos_registros_hc").attr("disabled", "disabled"); 
	}
}

//Agrega los Estados de Atencion
function agregarEstadosAtencion(idEstadoAtencion, nombreEstado) {
	//Agrega los valores
	$("#txtEstadoAtencion").text(nombreEstado);
	$("#hdd_EstadoAtencion").val(idEstadoAtencion);
	seleccionarProcedimiento(0);//Cierra el div flotante
}

//Agrega los Procedimientos
function agregarProcedimiento(idProcedimiento, nombreProcedimiento) {
	//Agrega los valores
	$("#txtProcedimientos").text(nombreProcedimiento);
	$("#hdd_Procedimiento").val(idProcedimiento);
	seleccionarProcedimiento(0);//Cierra el div flotante
}

//Funcion del boton eliminar para el formulario flotante: Detalle cita
function eliminarProcedimiento() {
	if (confirm("Desea eliminar el procedimiento?")) {
		$("#txtProcedimientos").text("");
		$("#hdd_Procedimiento").val("");
		
		ocultaBtnEliminarProcedimiento();
	}
}

function seleccionar_preqx() {
	//comprueba si el checkbox fue seleccionado
	if ($("#chk_preqx").is(":checked")) {
		//Muestra el combo box
		$("#cmb_tipo_reg_cx").attr("disabled", false); 
	} else {
		//oculta el combo box       
		$("#cmb_tipo_reg_cx").attr("disabled", "disabled"); 
	}
}

function agregar_reg_remision() {
	var cant_reg_remision = parseInt($("#hdd_cant_reg_remision").val(), 10);
	if (cant_reg_remision < g_max_reg_remision) {
		$("#tr_det_remision_" + cant_reg_remision).css("display", "table-row");
		cant_reg_remision++;
		$("#hdd_cant_reg_remision").val(cant_reg_remision);
	}
}

function borrar_reg_remision(indice) {
	$("#tr_det_remision_" + indice).css("display", "none");
	$("#cmb_tipo_cita_dest_" + indice).val("");
	$("#cmb_tipo_reg_hc_dest_" + indice).val("");
}

function seleccionar_tipo_cita_dest(id_tipo_cita, indice) {
	var params = "opcion=11&id_tipo_cita=" + id_tipo_cita +
				 "&indice=" + indice;
	
	llamarAjax("tipos_citas_ajax.php", params, "d_tipo_reg_hc_dest_" + indice, "");
}
