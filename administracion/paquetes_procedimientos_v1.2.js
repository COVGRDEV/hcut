//Funcion que busca el servicio
function buscar_paquetes() {
    $("#frmPaquetes").validate({
        rules: {
            txtParametro: {
                required: true,
            }
        },
        submitHandler: function () {
            var params = "opcion=5&proceso=2&parametro=" + $("#txtParametro").val();
			
            llamarAjax("paquetes_procedimientos_ajax.php", params, "divPaquetes", "");

            return false;
        },
    });
}

function obtener_paquetes() {
    var params = "opcion=5&proceso=1";
	
    llamarAjax("paquetes_procedimientos_ajax.php", params, "divPaquetes", "");
}

function obtener_paquete(id) {
    var params = "opcion=1&funcion=2&idPaquete=" + id;
	
    llamarAjax("paquetes_procedimientos_ajax.php", params, "divPaquetes", "");
}

function iniciar_nuevo_paquete() {
    var params = "opcion=1&funcion=1";
	
    llamarAjax("paquetes_procedimientos_ajax.php", params, "divPaquetes", "");
}

function mostrar_agregar_producto() {
    var cant_productos = parseInt($("#hdd_cant_productos").val(), 10);

    if (cant_productos < 20) {
        $("#tr_productos_" + cant_productos).css("display", "table-row");
		 $("#hdd_id_paquete_p_det_"+cant_productos).val("");
        $("#hdd_cant_productos").val(cant_productos + 1);
    } else {
        alert_basico("Error", "Se ha alcanzado el número máximo de procedimientos permitidos por paquete.", "error");
    }
}

function ocultar_agregar_producto() {
    var cant_productos = parseInt($("#hdd_cant_productos").val(), 10);
	if (cant_productos > 0) {
		cant_productos--;
		$("#cmb_tipo_" + cant_productos).val("");
		$("#hdd_cod_servicio_" + cant_productos).val("");
		$("#txt_cod_servicio_" + cant_productos).val("");
		$("#sp_nombre_servicio_" + cant_productos).html("");
        $("#tr_productos_" + cant_productos).css("display", "none");
        $("#hdd_cant_productos").val(cant_productos);
	}
}

function mostrar_buscar_procedimientos(rastro) {
    mostrar_formulario_flotante(1);
    var params = "opcion=2&rastro=" + rastro;
	
    llamarAjax("paquetes_procedimientos_ajax.php", params, "d_interno", "");
}

function buscar_procedimientos() {
    var parametro = $("#txtParametroProcedimineto").val();
	
    if (parametro == "") {
        alert_basico("", "Agregue el código o nombre del procedimiento", "warning");
    } else {
        var params = "opcion=3&parametro=" + parametro;
		
        llamarAjax("paquetes_procedimientos_ajax.php", params, "resultadoTblProcedimientos", "");
    }
}

function comfirmar_agregar_procedimiento(cod_producto, tipo_producto) {
	var rastro = $("#hdd_rastro").val();
	var nombre_producto = $("#hdd_nomProc_" + cod_producto).val();
	
	//Comprueba repetidos
	var continuar = true;
	for (var i = 0; i < 20; i++) {
		if ($("#hdd_cod_servicio_" + i).val().length > 0) {
			if ($("#cmb_tipo_" + i).val() == tipo_producto && $("#hdd_cod_servicio_" + i).val() == cod_producto) {
				continuar = false;
				break;
			}
		}
	}
	
	if (continuar) {
		continuar_comfirmar_agregar_procedimiento(rastro, cod_producto, tipo_producto, nombre_producto);
	} else {
		swal({
			title: resultado,
			type: "error",
			text: "Error - procedimiento duplicado",
			showConfirmButton: false,
			showCloseButton: true,
		});
	}
}

function continuar_comfirmar_agregar_procedimiento(rastro, cod_producto, tipo_producto, nombre_producto) {
    $("#cmb_tipo_" + rastro).val(tipo_producto);
    $("#hdd_cod_servicio_" + rastro).val(cod_producto);
    $("#txt_cod_servicio_" + rastro).val(cod_producto);
    $("#sp_nombre_servicio_" + rastro).html(nombre_producto);

    cerrar_div_centro();
}

function crear_paquete() {
	var cant_productos = parseInt($("#hdd_cant_productos").val(), 10);
    var nom_paquete = $("#txt_nom_paquete").val();
	var id_paquete = $("#hdd_id_paquete_procedimientos").val();
    var procedimientos = "";
	
    //Procesa procedimientos
    var cont_aux = 0;
	var params = "";
	
    for (var i = 0; i < cant_productos; i++) {
		console.log($("#hdd_cod_servicio_" + i).val())
        if ($("#hdd_cod_servicio_" + i).val().length > 0) {
			params += "&tipo_producto_" + cont_aux + "=" + $("#cmb_tipo_" + i).val() +
					  "&cod_producto_" + cont_aux + "=" + $("#hdd_cod_servicio_" + i).val() +
					  "&tipo_valor" + cont_aux + "=" + $("#cmb_tipo_valor_" + i).val() +
					  "&valor" + cont_aux + "=" + $("#txt_valor_servicio_" + i).val() +
					  "&id_paquete" + cont_aux + "=" + id_paquete;
            cont_aux ++;
			
			console.log(params)
        	
		}
    }
		
    if (nom_paquete.length > 0 && cont_aux  > 0) {
        swal({//Alert de confirmación
            title: id_paquete == "" ? "¿Realmente desea crear el paquete?" : "¿Realmente desea actualizar este paquete?",
            type: "question",
            showCancelButton: true,
            confirmButtonText: "Sí, crear",
            cancelButtonText: "No, cancelar"
        }).then((resultado) => {
            if (resultado.value) {//Evento aceptar
                swal({//Ventana temporal que muestra el progreso del ajax...
                    title: "...Espere un momento...",
                    type: "question",
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    html: '<div id="tmpResultado"></div>',
                    onOpen: () => {
                        var ind_auto_honorarios_medicos = $("#checkAutoHonorariosMed").is(":checked") ? 1 : 0;
                        var ind_auto_anestesia = $("#checkAutoAnestesiologo").is(":checked") ? 1 : 0;
                        var ind_auto_ayudantia = $("#checkAutoAyudantia").is(":checked") ? 1 : 0;
                        var ind_auto_derechos_sala = $("#checkAutoDerechosSala").is(":checked") ? 1 : 0;
						var ind_auto_insumos_adicionales = $("#checkAutoInsusmoAdicionales").is(":checked") ? 1 : 0;
						var valor_insumos_adicionales = $("#valorInsumosAdicionales").val().trim();

                        params = "opcion=4&nom_paquete=" + nom_paquete +
								 "&ind_auto_honorarios_medicos=" + ind_auto_honorarios_medicos +
								 "&ind_auto_anestesia=" + ind_auto_anestesia +
								 "&ind_auto_ayudantia=" + ind_auto_ayudantia +
								 "&ind_auto_derechos_sala=" + ind_auto_derechos_sala +
								 "&ind_auto_insumos_adicionales=" + ind_auto_insumos_adicionales +
								 "&valor_insumos=" + valor_insumos_adicionales +
								 "&cantidad_det=" + cont_aux +
								 "&id_paquete=" + id_paquete +
								 params;
						
						llamarAjax("paquetes_procedimientos_ajax.php", params, "d_acciones_paquete", "validar_guardar()");
                    }
                });
            }
        });
    } else {
        alert_basico("Debe agregar el nombre del paquete y al menos un producto", "", "error");
    }
}

function validar_guardar() {
	var resultado = parseInt($("#hdd_resultado_crear").val(), 10);
	var id_paquete = $("#hdd_id_paquete_procedimientos").val();
	
	if (resultado > 0) {
		swal({//Ventana temporal que muestra el progreso del ajax...
			title: id_paquete == "" ? "El paquete ha sido creado" : "El paquete se ha actualizado",
			type: "success",
			html: '<span>C&oacute;digo del paquete: </span><span style="color: #399000;font-weight: bold;">#' + resultado + "</span>",
			showCancelButton: false,
			onOpen: () => {
				obtener_paquetes();
			}
		});
	} else {
		
		swal({
			title: "Error",
			type: "error",
			text: "Error interno en la creación del paquete (" + resultado + ")",
			showConfirmButton: false,
			showCloseButton: true,
		});
	}
}

function inhabilitar_paquete() {
    swal({
        title: "¿Realmente desea inhabilitar el paquete?",
        type: "question",
        text: "Se requiere una nota aclaratoria",
        showCloseButton: true,
        showCancelButton: true,
        confirmButtonText: "Inhabilitar",
        cancelButtonText: "Cancelar",
        input: "textarea",
        inputValidator: (value) => {
            return !value && "Debe asignar una nota aclaratoria"
        }
    }).then((resultado) => {
        if (resultado.value) {//Evento aceptar
            swal({//Ventana temporal que muestra el progreso del ajax...
                title: "...Espere un momento...",
                type: "question",
                allowOutsideClick: false,
                showConfirmButton: false,
                html: '<div id="tmpResultado"></div>',
                onOpen: () => {
                    let idPaquete = $("#hdd_id_paquete_procedimientos").val();

					var params = "opcion=6&idPaquete=" + idPaquete +
								 "&observacion=" + JSON.stringify(resultado.value);
					
                    llamarAjax("paquetes_procedimientos_ajax.php", params, "d_acciones_paquete", "validar_inhabilitar_paquete()");
                }
            });
        }
    });
    
}

function validar_inhabilitar_paquete() {
	var resultado = parseInt($("#hdd_resultado_inhabilitar").val(), 10);
	
	if (resultado > 0) {
		swal({//Ventana temporal que muestra el progreso del ajax...
			title: "El paquete ha sido inhabilitado",
			type: "success",
			showCancelButton: false,
			onOpen: () => {
				obtener_paquetes();
			}
		});
	} else {
		let mensajeTipoError = "";
		let tipoError = 0;
		switch (resultado) {
			case - 1:
				tipoError = resultado;
				mensajeTipoError = "Error interno en el procedimiento de almacenado";
				break;
		}
		
		swal({
			title: tipoError,
			type: "error",
			text: mensajeTipoError,
			showConfirmButton: false,
			showCloseButton: true,
		});
	}
}

function cambiar_tipo_producto(indice) {
	$("#hdd_cod_servicio_" + indice).val("");
	$("#txt_cod_servicio_" + indice).val("");
	$("#sp_nombre_servicio_" + indice).html("");
}

function buscar_producto_codigo(cod_producto, indice) {
	if ($("#cmb_tipo_" + indice).val() != "") {
		var params = "opcion=7&tipo_producto=" + $("#cmb_tipo_" + indice).val() +
					 "&cod_producto=" + cod_producto;
		
		llamarAjax("paquetes_procedimientos_ajax.php", params, "d_acciones_paquete", "continuar_buscar_producto_codigo(" + indice + ");");
	}
}

function continuar_buscar_producto_codigo(indice) {
	var cod_producto_b = $("#hdd_cod_producto_b").val();
	var nombre_producto_b = $("#hdd_nombre_producto_b").val();
	
	if (cod_producto_b != "") {
		$("#hdd_cod_servicio_" + indice).val(cod_producto_b);
		$("#sp_nombre_servicio_" + indice).removeClass("texto-rojo");
		$("#sp_nombre_servicio_" + indice).html(nombre_producto_b);
	} else {
		$("#hdd_cod_servicio_" + indice).val("");
		$("#sp_nombre_servicio_" + indice).addClass("texto-rojo");
		$("#sp_nombre_servicio_" + indice).html(nombre_producto_b);
	}
}

function mostrarTxtInsumosAdicionales(checkbox){
	
	if(checkbox.checked == true){
        $("#valorInsumosAdicionales").css("display", "");
    }else{
        $("#valorInsumosAdicionales").css("display", "none");
   }

}