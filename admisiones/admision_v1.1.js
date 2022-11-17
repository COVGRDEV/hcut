$(document).ready(function() {
    verifica();

    $("#modal").click(function() {
        mostrar_formulario_flotante_adm(1);
    })
});

function mostrar_formulario_flotante_adm(tipo) {
    if (tipo == 1) {//mostrar
        $("#fondo_negro").css("display", "block");
        $("#d_centro").slideDown(400).css("display", "block");

        //Envia por ajax la peticion para construir el formulario de buscar pacientes
        var params = "opcion=1";
        llamarAjax("../funciones/BuscarUsuario.php", params, "d_interno", "formularioFlotanteFocus();");
    } else if (tipo == 0) {//Ocultar
        $("#a_cierre_panel2").remove();//Elimina el el icono cerrar ventana agregado en las lineas de arriba
        $("#d_centro").css("display", "none");
        $("#fondo_negro").css("display", "none");

        $("#advertenciasg").prepend('<div class="contenedor_error" id="contenedor_error"></div>');//Agreba el div de error al conteendor
    }
}

function formularioFlotanteFocus() {//Funcion que asegura el puntero del mouse en el input del formulario flotante
    $("#txt_identificacion_interno").select();
}

function buscar_paciente() {
    $("#frmInternoBuscarusuario").validate({
        debug: false,
        rules: {
            txt_identificacion_interno: {
                required: true,
            },
        },
        submitHandler: function(form) {
            buscar_paciente_cont();

            return false;
        }
    });
}

function buscar_paciente_cont() {
    var txtidentificacioninterno = $("#txt_identificacion_interno").val();

    //Envia por ajax la peticion para construir el formulario de buscar pacientes
    var params = "opcion=4&txtidentificacioninterno=" + txtidentificacioninterno;
    llamarAjax("admision_ajax.php", params, "d_datagrid_resultado", "");
}

function seleccionUsuario(id) {//Evento clic en el formulario flotante: Buscar Usuario
    var nombre_completo_aux = obtener_nombre_completo($("#hdd_nombre_1_" + id).val(), $("#hdd_nombre_2_" + id).val(), $("#hdd_apellido_1_" + id).val(), $("#hdd_apellido_2_" + id).val());
	
    if (confirm("\u00BFConfirma que desea seleccionar el paciente " + nombre_completo_aux + "?")) {
		//carga los valores en el formulario: Datos del paciente
        $("#hdd_id_paciente").val($("#hdd_id_paciente_" + id).val());
        $("#txt_id").val($("#hdd_numero_documento_" + id).val());
        $("#cmb_tipo_id").val($("#hdd_id_tipo_documento_" + id).val());
        $("#cmb_sexo").val($("#hdd_sexo_" + id).val());
        $("#txt_nombre").val($("#hdd_nombre_1_" + id).val());
        $("#txt_nombre2").val($("#hdd_nombre_2_" + id).val());
        $("#txt_apellido").val($("#hdd_apellido_1_" + id).val());
        $("#txt_apellido2").val($("#hdd_apellido_2_" + id).val());
        $("#txt_fecha_nacimiento").val($("#hdd_fecha_nacimiento_aux_" + id).val());

        $("#cmb_id_pais_nac").val($("#hdd_id_pais_nac_" + id).val());
        if ($("#hdd_id_pais_nac_" + id).val() == "1") {
            //Si el pais seleccionado es: Colombia
            $("#td_departamento_nac_label").css("display", "table-cell");
            $("#td_municipio_nac_label").css("display", "table-cell");
            $("#td_departamento_nac").css("display", "table-cell");
            $("#td_municipio_nac").css("display", "table-cell");
            $("#td_departamento_nac_n_label").css("display", "none");
            $("#td_municipio_nac_n_label").css("display", "none");
            $("#td_departamento_nac_n").css("display", "none");
            $("#td_municipio_nac_n").css("display", "none");
        } else {
            $("#td_departamento_nac_label").css("display", "none");
            $("#td_municipio_nac_label").css("display", "none");
            $("#td_departamento_nac").css("display", "none");
            $("#td_municipio_nac").css("display", "none");
            $("#td_departamento_nac_n_label").css("display", "table-cell");
            $("#td_municipio_nac_n_label").css("display", "table-cell");
            $("#td_departamento_nac_n").css("display", "table-cell");
            $("#td_municipio_nac_n").css("display", "table-cell");
        }

        seleccionar_pais($("#hdd_id_pais_nac_" + id).val(), "_nac");
        $("#cmb_departamento_nac").val($("#hdd_cod_dep_nac_" + id).val());
        seleccionar_departamento($("#hdd_cod_dep_nac_" + id).val(), $("#hdd_cod_mun_nac_" + id).val(), "_nac");
        $("#txt_nombre_dep_nac").val($("#hdd_nom_dep_nac_" + id).val());
        $("#txt_nombre_mun_nac").val($("#hdd_nom_mun_nac_" + id).val());

        $("#cmb_tipo_sangre").val($("#hdd_tipo_sangre_" + id).val());
        $("#cmb_factor_rh").val($("#hdd_factor_rh_" + id).val());
        $("#txt_telefono").val($("#hdd_telefono_1_" + id).val());
        $("#txt_telefono2").val($("#hdd_telefono_2_" + id).val());
        $("#txt_email").val($("#hdd_email_" + id).val());

        $("#cmb_id_pais").val($("#hdd_id_pais_" + id).val());
        if ($("#hdd_id_pais_" + id).val() == "1") {
            //Si el pais seleccionado es: Colombia
            $("#td_departamento_label").css("display", "table-cell");
            $("#td_municipio_label").css("display", "table-cell");
            $("#td_departamento").css("display", "table-cell");
            $("#td_municipio").css("display", "table-cell");
            $("#td_departamento_n_label").css("display", "none");
            $("#td_municipio_n_label").css("display", "none");
            $("#td_departamento_n").css("display", "none");
            $("#td_municipio_n").css("display", "none");
        } else {
            $("#td_departamento_label").css("display", "none");
            $("#td_municipio_label").css("display", "none");
            $("#td_departamento").css("display", "none");
            $("#td_municipio").css("display", "none");
            $("#td_departamento_n_label").css("display", "table-cell");
            $("#td_municipio_n_label").css("display", "table-cell");
            $("#td_departamento_n").css("display", "table-cell");
            $("#td_municipio_n").css("display", "table-cell");
        }

        seleccionar_pais($("#hdd_id_pais_" + id).val(), "");
        $("#cmb_departamento").val($("#hdd_cod_dep_" + id).val());
        seleccionar_departamento($("#hdd_cod_dep_" + id).val(), $("#hdd_cod_mun_" + id).val(), "");
        $("#txt_nombre_dep").val($("#hdd_nom_dep_" + id).val());
        $("#txt_nombre_mun").val($("#hdd_nom_mun_" + id).val());

        $("#txt_direccion").val($("#hdd_direccion_" + id).val());
        $("#cmb_zona").val($("#hdd_id_zona_" + id).val());
        $("#txt_profesion").val($("#hdd_profesion_" + id).val());
        $("#cmb_etnia").val($("#hdd_id_etnia_" + id).val());
        $("#cmb_desplazado").val($("#hdd_ind_desplazado_" + id).val());
        //Datos de la admsion
        $("#cmb_numerohijos").val($("#hdd_numero_hijos_" + id).val());
        $("#cmb_numerohijas").val($("#hdd_numero_hijas_" + id).val());
        $("#cmb_numerohermanos").val($("#hdd_numero_hermanos_" + id).val());
        $("#cmb_numerohermanas").val($("#hdd_numero_hermanas_" + id).val());
        $("#txt_presionarterial").val($("#hdd_presion_arterial_" + id).val());
        $("#txt_pulso").val($("#hdd_pulso_" + id).val());
        $("#txt_acompanante").val($("#hdd_nombre_acompa_" + id).val());
        $("#txt_mconsulta").val($("#hdd_motivo_consulta_" + id).val());
        $("#cmb_convenio").val($("#hdd_id_convenio_" + id).val());
        mostrar_formulario_flotante_adm(0);
        $("#hdd_paciente_existe").val(2); //Agrega el valor 2 al hidden para que este actualice el registro
        $("#btnBuscarusuario").css("display", "none");

        seleccionar_convenio($("#hdd_id_convenio_" + id).val(), 1);

        $("#hdd_numero_documento_ori").val($("#hdd_numero_documento_" + id).val());
        $("#hdd_nombre_completo_ori").val(nombre_completo_aux);

        //Se carga de nuevo el componente de historia clínica
        cargar_componente_hc_adm(id);
    }
}

function guardar_admision() {
    $("#frmAdmision").validate({
        debug: false,
        rules: {
            cmb_tipo_id: {
                required: true,
            },
            txt_id: {
                required: true,
            },
            txt_nombre: {
                required: true,
            },
            txt_apellido: {
                required: true,
            },
            cmb_plan: {
                required: true,
            },
            cmb_id_pais: {
                required: true,
            },
            cmb_sexo: {
                required: true,
            },
            txt_direccion: {
            	required: true,
            	minlength: 3,
            },
            txt_telefono: {
            	required: true,
            	number: true,
            	minlength: 7,
            },
            cmb_id_pais: {
            	required: true,
            },
            cmb_lugar_cita: {
            	required: true,
            },
			txt_mconsulta: {
            	required: true,
            	minlength: 3,
            	maxlength: 500,
            },
			txt_email: {
				required: true,
				minlength: 6
            },
			cmb_tipo_coti_paciente: {
            	required: true,
            },
			cmb_rango_paciente: {
            	required: true,
            }
        },
        submitHandler: function (form) {
            $("#btnNuevo").attr("disabled", "disabled");
			
            //var cmb_departamento_nac = "";
            //var cmb_municipio_nac = "";
            //var txt_nombre_dep_nac = "";
            //var txt_nombre_mun_nac = "";
            var cmb_departamento = "";
            var cmb_municipio = "";
            var txt_nombre_dep = "";
            var txt_nombre_mun = "";
            //var cmb_desplazado = "";
            //var cmb_etnia = "";
            var cmd_profesionalAtiende = "";
            //var txt_presionarterial = "";
            //var txt_pulso = "";
            var txt_nombre_cirugia = "";
            var txt_fecha_cirugia = "";
            var cmb_ojo = "";
            var cmb_num_cirugia = "";
            var txt_nombre_med_orden = "";
            var cmb_cant_examenes = "";
            var txt_num_carnet = "";         
			
            if ($("#cmb_departamento").is(":visible")) {
                cmb_departamento = $("#cmb_departamento").val().length > 0 ? $("#cmb_departamento").val() : "-1";//-1 el objeto esta visible
            } else {
                cmb_departamento = "";//-2 El objeto no esta visible
            }
			
            if ($("#cmb_municipio").is(":visible")) {
                cmb_municipio = $("#cmb_municipio").val().length > 0 ? $("#cmb_municipio").val() : "-1";//-1 el objeto esta visible
            } else {
                cmb_municipio = "";//-2 El objeto no esta visible
            }
			
            /*if ($("#cmb_departamento_nac").is(":visible")) {
				cmb_departamento_nac = $("#cmb_departamento_nac").val().length > 0 ? $("#cmb_departamento_nac").val() : "-1";//-1 el objeto esta visible
			} else {
				cmb_departamento_nac = "";//-2 El objeto no esta visible
			}
			
			if ($("#cmb_municipio_nac").is(":visible")) {
				cmb_municipio_nac = $("#cmb_municipio_nac").val().length > 0 ? $("#cmb_municipio_nac").val() : "-1";//-1 el objeto esta visible
			} else {
				cmb_municipio_nac = "";//-2 El objeto no esta visible
			}
			
			if ($("#txt_nombre_dep_nac").is(":visible")) {
				txt_nombre_dep_nac = $("#txt_nombre_dep_nac").val().length > 0 ? $("#txt_nombre_dep_nac").val() : "-1";//-1 el objeto esta visible
			} else {
				txt_nombre_dep_nac = "";//-2 El objeto no esta visible
			}
			
			if ($("#txt_nombre_mun_nac").is(":visible")) {
				txt_nombre_mun_nac = $("#txt_nombre_mun_nac").val().length > 0 ? $("#txt_nombre_mun_nac").val() : "-1";//-1 el objeto esta visible
			} else {
				txt_nombre_mun_nac = "";//-2 El objeto no esta visible
			}*/
			
			if ($("#txt_nombre_dep").is(":visible")) {
				txt_nombre_dep = $("#txt_nombre_dep").val().length > 0 ? $("#txt_nombre_dep").val() : "-1";//-1 el objeto esta visible
			} else {
				txt_nombre_dep = "";//-2 El objeto no esta visible
			}
			
			if ($("#txt_nombre_mun").is(":visible")) {
				txt_nombre_mun = $("#txt_nombre_mun").val().length > 0 ? $("#txt_nombre_mun").val() : "-1";//-1 el objeto esta visible
			} else {
				txt_nombre_mun = "";//-2 El objeto no esta visible
			}
			
			/*if ($("#cmb_desplazado").is(":visible")) {
				cmb_desplazado = $("#cmb_desplazado").val().length > 0 ? $("#cmb_desplazado").val() : "-1";//-1 el objeto esta visible
			} else {
				cmb_desplazado = "";//-2 El objeto no esta visible
			}
			
			if ($("#cmb_etnia").is(":visible")) {
				cmb_etnia = $("#cmb_etnia").val().length > 0 ? $("#cmb_etnia").val() : "-1";//-1 el objeto esta visible
			} else {
				cmb_etnia = "";//-2 El objeto no esta visible
			}
			
			if ($("#td_presion_arterial").is(":visible")) {
				txt_presionarterial = $("#txt_presionarterial").val().length > 0 ? $("#txt_presionarterial").val() : "-1";
			} else {
				txt_presionarterial = "";
			}
			
			if ($("#td_pulso").is(":visible")) {
				txt_pulso = $("#txt_pulso").val().length > 0 ? $("#txt_pulso").val() : "-1";
			} else {
				txt_pulso = "";
			}*/
			
			if ($("#tr_cirugia").is(":visible")) {
				txt_nombre_cirugia = $("#txt_nombre_cirugia").val().length > 0 ? $("#txt_nombre_cirugia").val() : "-1";
				txt_fecha_cirugia = $("#txt_fecha_cirugia").val().length > 0 ? $("#txt_fecha_cirugia").val() : "-1";
				cmb_ojo = $("#cmb_ojo").val().length > 0 ? $("#cmb_ojo").val() : "-1";
				cmb_num_cirugia = $("#cmb_num_cirugia").val().length > 0 ? $("#cmb_num_cirugia").val() : "-1";
			} else {
				txt_nombre_cirugia = "";
				txt_fecha_cirugia = "";
				cmb_ojo = "";
				cmb_num_cirugia = "";
			}
				 
			if ($("#tr_nombre_med_orden").is(":visible")) {
				txt_nombre_med_orden = $("#txt_nombre_med_orden").val().length > 0 ? $("#txt_nombre_med_orden").val() : "-1";
			} else {
				txt_nombre_med_orden = "";
			}
			
			//Se valida la selección de exámenes
			var ind_examenes_compl = "";
			if ($("#tr_examenes").is(":visible")) {
				cmb_cant_examenes = $("#cmb_cant_examenes").val().length > 0 ? $("#cmb_cant_examenes").val() : "-1";
				for (var i = 0; i < parseInt(cmb_cant_examenes, 10); i++) {
					if ($("#cmb_examen_" + i).val() == "") {
						ind_examenes_compl = "-1";
						break;
					}
					if ($("#cmb_ojo_examen_" + i).val() == "") {
						ind_examenes_compl = "-1";
						break;
					}
				}
			} else {
				cmb_cant_examenes = "";
			}
			
            if ($("#cmd_profesionalAtiende").is(":visible")) {
                cmd_profesionalAtiende = $("#cmd_profesionalAtiende").val().length > 0 ? $("#cmd_profesionalAtiende").val() : "-1";//-1 el objeto esta visible
            } else {
                cmd_profesionalAtiende = "";//-2 El objeto no esta visible
            }
			
			if ($("#hdd_num_carnet_obl").val() == "1") {
				txt_num_carnet = $("#txt_num_carnet").val().length > 0 ? $("#txt_num_carnet").val() : "-1";
			} else {
				txt_num_carnet = $("#txt_num_carnet").val().length > 0 ? $("#txt_num_carnet").val() : "";
			}
			
            //Se valida la selección de profesionales
            var ind_profesionales_sel = "";
            var cant_tipo_cita_det = parseInt($("#hdd_cant_tipo_cita_det").val(), 10);
            for (var i = 0; i < cant_tipo_cita_det; i++) {
                if ($("#cmb_usuario_cita_det_" + i).val() == "") {
                    ind_profesionales_sel = "-1";
                    $("#cmb_usuario_cita_det_" + i).addClass("bordeAdmision");
                } else {
                    $("#cmb_usuario_cita_det_" + i).removeClass("bordeAdmision");
                }
            }
			
            var bol_email = validar_email($("#txt_email").val());
			
            if (/*cmb_departamento_nac === "-1" || cmb_municipio_nac === "-1" || txt_nombre_dep_nac === "-1" || txt_nombre_mun_nac === "-1"
					|| */cmb_departamento === "-1" || cmb_municipio === "-1" || txt_nombre_dep === "-1" || txt_nombre_mun === "-1"
					/*|| cmb_desplazado === "-1" || cmb_etnia === "-1" */|| cmd_profesionalAtiende === "-1" || txt_nombre_cirugia === "-1"
					|| txt_fecha_cirugia === "-1" || cmb_ojo === "-1" || cmb_num_cirugia === "-1" || txt_nombre_med_orden === "-1"
					|| cmb_cant_examenes === "-1" || ind_examenes_compl == "-1" || ind_profesionales_sel == "-1" || txt_num_carnet === "-1" || !bol_email) {
			    //Asigna las propiedades css
                //cmb_departamento_nac === "-1" ? $("#cmb_departamento_nac").addClass("bordeAdmision") : $("#cmb_departamento_nac").removeClass("bordeAdmision");
                //cmb_municipio_nac === "-1" ? $("#cmb_municipio_nac").addClass("bordeAdmision") : $("#cmb_municipio_nac").removeClass("bordeAdmision");
                //txt_nombre_dep_nac === "-1" ? $("#txt_nombre_dep_nac").addClass("bordeAdmision") : $("#txt_nombre_dep_nac").removeClass("bordeAdmision");
                //txt_nombre_mun_nac === "-1" ? $("#txt_nombre_mun_nac").addClass("bordeAdmision") : $("#txt_nombre_mun_nac").removeClass("bordeAdmision");
                cmb_departamento === "-1" ? $("#cmb_departamento").addClass("bordeAdmision") : $("#cmb_departamento").removeClass("bordeAdmision");
                cmb_municipio === "-1" ? $("#cmb_municipio").addClass("bordeAdmision") : $("#cmb_municipio").removeClass("bordeAdmision");
                txt_nombre_dep === "-1" ? $("#txt_nombre_dep").addClass("bordeAdmision") : $("#txt_nombre_dep").removeClass("bordeAdmision");
                txt_nombre_mun === "-1" ? $("#txt_nombre_mun").addClass("bordeAdmision") : $("#txt_nombre_mun").removeClass("bordeAdmision");
                //cmb_desplazado === "-1" ? $("#cmb_desplazado").addClass("bordeAdmision") : $("#cmb_desplazado").removeClass("bordeAdmision");
                //cmb_etnia === "-1" ? $("#cmb_etnia").addClass("bordeAdmision") : $("#cmb_etnia").removeClass("bordeAdmision");
                cmd_profesionalAtiende === "-1" ? $("#cmd_profesionalAtiende").addClass("bordeAdmision") : $("#cmd_profesionalAtiende").removeClass("bordeAdmision");
                txt_nombre_cirugia === "-1" ? $("#txt_nombre_cirugia").addClass("bordeAdmision") : $("#txt_nombre_cirugia").removeClass("bordeAdmision");
                txt_fecha_cirugia === "-1" ? $("#txt_fecha_cirugia").addClass("bordeAdmision") : $("#txt_fecha_cirugia").removeClass("bordeAdmision");
                cmb_ojo === "-1" ? $("#cmb_ojo").addClass("bordeAdmision") : $("#cmb_ojo").removeClass("bordeAdmision");
                cmb_num_cirugia === "-1" ? $("#cmb_num_cirugia").addClass("bordeAdmision") : $("#cmb_num_cirugia").removeClass("bordeAdmision");
				txt_nombre_med_orden === "-1" ? $("#txt_nombre_med_orden").addClass("bordeAdmision") : $("#txt_nombre_med_orden").removeClass("bordeAdmision");
                cmb_cant_examenes === "-1" ? $("#cmb_cant_examenes").addClass("bordeAdmision") : $("#cmb_cant_examenes").removeClass("bordeAdmision");
				txt_num_carnet === "-1" ? $("#txt_num_carnet").addClass("bordeAdmision") : $("#txt_num_carnet").removeClass("bordeAdmision");
				
				if (cmb_cant_examenes != "" && cmb_cant_examenes != "") {
					for (var i = 0; i < parseInt(cmb_cant_examenes, 10); i++) {
						if ($("#cmb_examen_" + i).val() == "") {
							$("#cmb_examen_" + i).addClass("bordeAdmision");
						} else {
							$("#cmb_examen_" + i).removeClass("bordeAdmision");
						}
						if ($("#cmb_ojo_examen_" + i).val() == "") {
							$("#cmb_ojo_examen_" + i).addClass("bordeAdmision");
						} else {
							$("#cmb_ojo_examen_" + i).removeClass("bordeAdmision");
						}
					}
				}
				
				if (!bol_email) {
					$("#txt_email").addClass("bordeAdmision");
				} else {
					$("#txt_email").removeClass("bordeAdmision");
				}
				
				$("#contenedor_error").css("display", "block");
				$("#contenedor_error").html("Los campos marcados en rojo son obligatorios");
				$("#btnNuevo").removeAttr("disabled");
				window.scrollTo(0, 0);
            } else {
				//Remueve La clase bordeAdmision en caso de que esta este agregada
				//$("#cmb_municipio_nac").removeClass("bordeAdmision");
				//$("#cmb_departamento_nac").removeClass("bordeAdmision");
				//$("#txt_nombre_dep_nac").removeClass("bordeAdmision");
				//$("#txt_nombre_mun_nac").removeClass("bordeAdmision");
				$("#cmb_municipio").removeClass("bordeAdmision");
				$("#cmb_departamento").removeClass("bordeAdmision");
				$("#txt_nombre_dep").removeClass("bordeAdmision");
				$("#txt_nombre_mun").removeClass("bordeAdmision");
				//$("#cmb_desplazado").removeClass("bordeAdmision");
				$("#cmd_profesionalAtiende").removeClass("bordeAdmision");
				$("#txt_nombre_cirugia").removeClass("bordeAdmision");
				$("#txt_fecha_cirugia").removeClass("bordeAdmision");
				$("#cmb_ojo").removeClass("bordeAdmision");
				$("#cmb_num_cirugia").removeClass("bordeAdmision");
				$("#txt_nombre_med_orden").removeClass("bordeAdmision");
				$("#cmb_cant_examenes").removeClass("bordeAdmision");
				for (var i = 0; i < 10; i++) {
					$("#cmb_examen_" + i).removeClass("bordeAdmision");
					$("#cmb_ojo_examen_" + i).removeClass("bordeAdmision");
				}
				$("#txt_num_carnet").removeClass("bordeAdmision");
				$("#txt_email").removeClass("bordeAdmision");
				
				var myArray = [];
				var filas = parseInt($("#hdd_ult_index_precio").val(), 10);
				
				//Se verifica si hay por lo menos un precio que cobrar
                var ind_continuar = true;
                if (isObject(document.getElementById("tr_precio_no_hallado")) || filas == 0) {
                    $("#contenedor_error").css("display", "block");
                    $("#contenedor_error").html("Debe seleccionar por lo menos un producto a cobrar");
                    ind_continuar = false;
                } else {
                    //Se verifica que se hayan seleccionado todos los convenios y los planes
                    for (i = 1; i <= filas; i++) {
                        if (isObject(document.getElementById("tr_precios_" + i))) {
                            if ($("#cmb_convenio_pago_" + i).val() == "") {
                                $("#contenedor_error").css("display", "block");
                                $("#contenedor_error").html("Los campos marcados en rojo son obligatorios.");
                                ind_continuar = false;
                                $("#cmb_convenio_pago_" + i).addClass("bordeAdmision");
                            } else {
                                $("#cmb_convenio_pago_" + i).removeClass("bordeAdmision");
                            }
                            if ($("#cmb_plan_pago_" + i).val() == "") {
                                $("#contenedor_error").css("display", "block");
                                $("#contenedor_error").html("Los campos marcados en rojo son obligatorios.");
                                ind_continuar = false;
                                $("#cmb_plan_pago_" + i).addClass("bordeAdmision");
                            } else {
                                $("#cmb_plan_pago_" + i).removeClass("bordeAdmision");
                            }

                            if ($("#hdd_ind_num_aut_" + i).val() == "1" && $("#hdd_ind_num_aut_obl_" + i).val() == "1" && $("#txt_num_autorizacion_" + i).val().length < 4
                                    && $("#cmb_medio_pago").val() != $("#hdd_medio_pago_np").val()) {
                                $("#contenedor_error").css("display", "block");
                                $("#contenedor_error").html("Los campos marcados en rojo son obligatorios.");
                                ind_continuar = false;
                                $("#txt_num_autorizacion_" + i).addClass("bordeAdmision");
                            } else {
                                $("#txt_num_autorizacion_" + i).removeClass("bordeAdmision");
                            }
                        }
                    }

                    if (ind_continuar) {
                        //Se verifica que no haya cantidades de cero
                        for (i = 1; i <= filas; i++) {
                            if (isObject(document.getElementById("tr_precios_" + i))) {
                                var cantidad_aux = trim($("#txt_cant_precio_" + i).val())
                                if (cantidad_aux != "") {
                                    cantidad_aux = parseInt(cantidad_aux, 10);
                                } else {
                                    cantidad_aux = 0;
                                }
                                if (cantidad_aux <= 0) {
                                    $("#contenedor_error").css("display", "block");
                                    $("#contenedor_error").html("Todas las cantidades de los procuctos deben ser mayores a cero (0).");
                                    ind_continuar = false;
                                    break;
                                }
                            }
                        }

                        if (ind_continuar) {
                            //Se verifica que no haya valores vacíos
                            var total_aux = 0;
                            for (i = 1; i <= filas; i++) {
                                if (isObject(document.getElementById("tr_precios_" + i))) {
                                    if (trim($("#txt_valor_pago_" + i).val()) == "" || trim($("#txt_valor_cuota_" + i).val()) == "") {
                                        $("#contenedor_error").css("display", "block");
                                        $("#contenedor_error").html("Los valores unitarios y de cuota moderadora de los productos no deben ser vac&iacute;os.");
                                        ind_continuar = false;
                                        break;
                                    }
                                    total_aux += (parseInt($("#txt_valor_pago_" + i).val(), 10) + parseInt($("#txt_valor_cuota_" + i).val(), 10)) * parseInt($("#txt_cant_precio_" + i).val(), 10);
                                }
                            }

                            if (ind_continuar) {
                                //Si el total es cero, em medio de pago debe ser NP
                                if (total_aux == 0 && ($("#cmb_medio_pago").val() != $("#hdd_medio_pago_np").val())) {
                                    $("#contenedor_error").css("display", "block");
                                    $("#contenedor_error").html("El valor total de los servicios debe ser mayor a cero (0) o el medio de pago debe ser NP");
                                    ind_continuar = false;
                                }
                            }
                        }
                    }
                }
				
                if (ind_continuar) {
                    if ($("#hdd_numero_documento_ori").val() != "" && $("#txt_id").val() != $("#hdd_numero_documento_ori").val()) {
                        $("#fondo_negro").css("display", "block");
                        $("#a_cierre_panel").css("display", "none");
                        $("#d_centro").slideDown(400).css("display", "block");
						
                        var nombre_completo_aux = obtener_nombre_completo($("#txt_nombre").val(), $("#txt_nombre2").val(), $("#txt_apellido").val(), $("#txt_apellido2").val());

                        var codigohtml =
							'<div class="encabezado">' +
								'<h3>Cambio de identificaci&oacute;n del paciente</h3>' +
							'</div>' +
							'<br />' +
							'<table style="width:100%; font-size:10pt;">' +
								'<tr>' +
									'<td align="right" style="width:25%;">No. documento anterior:</td>' +
									'<td align="left" style="width:25%;"><b>' + $("#hdd_numero_documento_ori").val() + '</b></td>' +
									'<td align="right" style="width:25%;">No. documento actual:</td>' +
									'<td align="left" style="width:25%;"><b>' + $("#txt_id").val() + '</b></td>' +
								'</tr>' +
								'<tr>' +
									'<td align="right">Nombre anterior:</td>' +
									'<td align="left"><b>' + $("#hdd_nombre_completo_ori").val() + '</b></td>' +
									'<td align="right">Nombre actual:</td>' +
									'<td align="left"><b>' + nombre_completo_aux + '</b></td>' +
								'</tr>' +
								'<tr>' +
									'<td align="center" colspan="4">' +
										'<h5>' +
											'Los datos de identificaci&oacute;n han cambiado, &iquest;confirma que se trata del mismo paciente?' +
										'</h5>' +
									'</td>' +
								'</tr>' +
								'<tr>' +
									'<td align="center" colspan="4">' +
										'<input type="button" class="btnPrincipal" value="Aceptar" onclick="ajustar_cambiar_paciente();" />&nbsp;&nbsp;' +
										'<input type="button" class="btnSecundario" value="Cancelar" onclick="cancelar_guardar_admision();" />' +
									'</td>' +
								'</tr>' +
							'</table>';
                        $(".div_interno").html(codigohtml);
						
                        posicionarDivFlotante("d_centro");
                    } else {
                        continuar_guardar_admision();
                    }
                } else {
                    $("#btnNuevo").removeAttr("disabled");
                    window.scrollTo(0, 0);
                }
            }
			
            return false;
        }
    });
}

function cancelar_guardar_admision() {
    $("#d_centro").css("display", "none");
    $("#fondo_negro").css("display", "none");
    $("#btnNuevo").removeAttr("disabled");
    $("#a_cierre_panel").css("display", "block");
}

function ajustar_cambiar_paciente() {
    if ($("#hdd_id_paciente").val() != "" && $("#hdd_id_paciente").val() != "0") {
        arr_aux = $("#hdd_id_consulta").val().split("-");
        $("#hdd_id_consulta").val(arr_aux[0] + "-" + $("#hdd_id_paciente").val());
        $("#hdd_paciente_existe").val(1);
    }
    continuar_guardar_admision();
}

function continuar_guardar_admision() {
	$("#d_centro").css("display", "none");
	$("#fondo_negro").css("display", "none");
	$("#a_cierre_panel").css("display", "block");
	
	var cont_aux = 0;
	var filas = parseInt($("#hdd_ult_index_precio").val(), 10);
	var params = "opcion=11&id_medio_pago=";
	for (i = 1; i <= filas; i++) {
		if (isObject(document.getElementById("tr_precios_" + i))) {
			params += "&idListaPrecios_" + cont_aux + "=" + $("#hdd_id_precio_" + i).val() +
					  "&id_convenio_pago_" + cont_aux + "=" + $("#cmb_convenio_pago_" + i).val() +
					  "&id_plan_pago_" + cont_aux + "=" + $("#cmb_plan_pago_" + i).val() +
					  "&num_autorizacion_" + cont_aux + "=" + str_encode($("#txt_num_autorizacion_" + i).val()) +
					  "&cantidad_" + cont_aux + "=" + $("#txt_cant_precio_" + i).val() +
					  "&tipoPrecio_" + cont_aux + "=" + trim($("#hdd_tipo_precio_" + i).val()) +
					  "&cod_servicio_" + cont_aux + "=" + $("#hdd_cod_servicio_" + i).val() +
					  "&tipoBilateral_" + cont_aux + "=" + trim($("#hdd_tipo_bilateral_" + i).val()) +
					  "&valor_" + cont_aux + "=" + $("#txt_valor_pago_" + i).val() +
					  "&valor_cuota_" + cont_aux + "=" + $("#txt_valor_cuota_" + i).val() +
					  "&indTipoPago_" + cont_aux + "=" + $("#hdd_tipo_pago_" + i).val();
			
			cont_aux++;
		}
	}
	params += "&cant_precios=" + cont_aux;
	
	llamarAjax("admision_ajax.php", params, "d_guardar_precios", "continuar_guardar_admision_2();");
}

function continuar_guardar_admision_2() {
    var tmp_pagos_detalle = ($("#hdd_tmp_pagos_detalle").val() != "" ? parseInt($("#hdd_tmp_pagos_detalle").val(), 10) : 0);

    if (tmp_pagos_detalle <= 0) {
        $("#contenedor_error").css("display", "block");
        $("#contenedor_error").html("Error interno al guardar el detalle del pago");
        $("#btnNuevo").removeAttr("disabled");
        window.scrollTo(0, 0);
    } else {
        /*var cmb_municipio_nac = "";
        var cmb_departamento_nac = "";
        var txt_nombre_dep_nac = "";
        var txt_nombre_mun_nac = "";*/
        var cmb_municipio = "";
        var cmb_departamento = "";
        var txt_nombre_dep = "";
        var txt_nombre_mun = "";
        /*var cmbdesplazado = "";
        var cmbetnia = "";*/
        var cmbprofesionalAtiende = "";
        var txt_num_carnet = "";
        /*var txt_presionarterial = "";
        var txt_pulso = "";*/
        var txt_nombre_cirugia = "";
        var txt_fecha_cirugia = "";
        var cmb_ojo = "";
        var cmb_num_cirugia = "";
        var txt_nombre_med_orden = "";
        var cmb_cant_examenes = "";
        
        /*if ($("#cmb_municipio_nac").is(":visible")) {
            cmb_municipio_nac = $("#cmb_municipio_nac").val().length > 0 ? $("#cmb_municipio_nac").val() : "-1";//-1 el objeto esta visible
        } else {
            cmb_municipio_nac = "";//-2 El objeto no esta visible
        }

        if ($("#cmb_departamento_nac").is(":visible")) {
            cmb_departamento_nac = $("#cmb_departamento_nac").val().length > 0 ? $("#cmb_departamento_nac").val() : "-1";//-1 el objeto esta visible
        } else {
            cmb_departamento_nac = "";//-2 El objeto no esta visible
        }

        if ($("#txt_nombre_dep_nac").is(":visible")) {
            txt_nombre_dep_nac = $("#txt_nombre_dep_nac").val().length > 0 ? $("#txt_nombre_dep_nac").val() : "-1";//-1 el objeto esta visible
        } else {
            txt_nombre_dep_nac = "";//-2 El objeto no esta visible
        }

        if ($("#txt_nombre_mun_nac").is(":visible")) {
            txt_nombre_mun_nac = $("#txt_nombre_mun_nac").val().length > 0 ? $("#txt_nombre_mun_nac").val() : "-1";//-1 el objeto esta visible
        } else {
            txt_nombre_mun_nac = "";//-2 El objeto no esta visible
        }*/

        if ($("#cmb_municipio").is(":visible")) {
            cmb_municipio = $("#cmb_municipio").val().length > 0 ? $("#cmb_municipio").val() : "-1";//-1 el objeto esta visible
        } else {
            cmb_municipio = "";//-2 El objeto no esta visible
        }

        if ($("#cmb_departamento").is(":visible")) {
            cmb_departamento = $("#cmb_departamento").val().length > 0 ? $("#cmb_departamento").val() : "-1";//-1 el objeto esta visible
        } else {
            cmb_departamento = "";//-2 El objeto no esta visible
        }

        if ($("#txt_nombre_dep").is(":visible")) {
            txt_nombre_dep = $("#txt_nombre_dep").val().length > 0 ? $("#txt_nombre_dep").val() : "-1";//-1 el objeto esta visible
        } else {
            txt_nombre_dep = "";//-2 El objeto no esta visible
        }

        if ($("#txt_nombre_mun").is(":visible")) {
            txt_nombre_mun = $("#txt_nombre_mun").val().length > 0 ? $("#txt_nombre_mun").val() : "-1";//-1 el objeto esta visible
        } else {
            txt_nombre_mun = "";//-2 El objeto no esta visible
        }

        /*if ($("#cmb_desplazado").is(":visible")) {
            cmbdesplazado = $("#cmb_desplazado").val().length > 0 ? $("#cmb_desplazado").val() : "-1";//-1 el objeto esta visible
        } else {
            cmbdesplazado = "";//-2 El objeto no esta visible
        }

        if ($("#cmb_etnia").is(":visible")) {
            cmbetnia = $("#cmb_etnia").val().length > 0 ? $("#cmb_etnia").val() : "-1";//-1 el objeto esta visible
        } else {
            cmbetnia = "";//-2 El objeto no esta visible
        }*/

        if ($("#cmd_profesionalAtiende").is(":visible")) {
            cmbprofesionalAtiende = $("#cmd_profesionalAtiende").val().length > 0 ? $("#cmd_profesionalAtiende").val() : "-1";//-1 el objeto esta visible
        } else {
            cmbprofesionalAtiende = "";//-2 El objeto no esta visible
        }

        /*if ($("#td_presion_arterial").is(":visible")) {
            txt_presionarterial = $("#txt_presionarterial").val().length > 0 ? $("#txt_presionarterial").val() : "";
        } else {
            txt_presionarterial = "";
        }

        if ($("#td_pulso").is(":visible")) {
            txt_pulso = $("#txt_pulso").val().length > 0 ? $("#txt_pulso").val() : "";
        } else {
            txt_pulso = "";
        }*/

        if ($("#tr_cirugia").is(":visible")) {
            txt_nombre_cirugia = $("#txt_nombre_cirugia").val().length > 0 ? $("#txt_nombre_cirugia").val() : "-1";
            txt_fecha_cirugia = $("#txt_fecha_cirugia").val().length > 0 ? $("#txt_fecha_cirugia").val() : "-1";
            cmb_ojo = $("#cmb_ojo").val().length > 0 ? $("#cmb_ojo").val() : "-1";
            cmb_num_cirugia = $("#cmb_num_cirugia").val().length > 0 ? $("#cmb_num_cirugia").val() : "-1";
        } else {
            txt_nombre_cirugia = "";
            txt_fecha_cirugia = "";
            cmb_ojo = "";
            cmb_num_cirugia = "";
        }

        if ($("#tr_nombre_med_orden").is(":visible")) {
            txt_nombre_med_orden = $("#txt_nombre_med_orden").val().length > 0 ? $("#txt_nombre_med_orden").val() : "-1";
        } else {
            txt_nombre_med_orden = "";
        }

        if ($("#tr_examenes").is(":visible")) {
            cmb_cant_examenes = $("#cmb_cant_examenes").val().length > 0 ? $("#cmb_cant_examenes").val() : "-1";
        } else {
            cmb_cant_examenes = "";
        }

        if ($("#hdd_num_carnet_obl").val() == "1") {
            txt_num_carnet = $("#txt_num_carnet").val().length > 0 ? $("#txt_num_carnet").val() : "-1";
        } else {
            txt_num_carnet = $("#txt_num_carnet").val().length > 0 ? $("#txt_num_carnet").val() : "";
        }

        var hddidconsulta = $("#hdd_id_consulta").val();
        var hddidconsultaaux = hddidconsulta.split("-");

        var hddpacienteexiste2 = $("#hdd_paciente_existe").val();
        var hddpacienteexisteaux = hddpacienteexiste2.split(":");
        var cant_tipo_cita_det = parseInt($("#hdd_cant_tipo_cita_det").val(), 10);

        var hdd_id_admision_paciente = parseInt($("#hdd_id_admision_paciente").val(), 10);
        var hdd_estado_consulta = parseInt($("#hdd_estado_consulta").val(), 10);
		var tipo_coti_paciente = $("#cmb_tipo_coti_paciente").val();
		var rango_paciente = $("#cmb_rango_paciente").val();
        
		var params = "opcion=3&txtnombre1=" + str_encode($("#txt_nombre").val()) +
					 "&txtnombre2=" + str_encode($("#txt_nombre2").val()) +
					 "&txtapellido1=" + str_encode($("#txt_apellido").val()) +
					 "&txtapellido2=" + str_encode($("#txt_apellido2").val()) +
					 "&cmbtipoid=" + $("#cmb_tipo_id").val() +
					 "&txtid=" + $("#txt_id").val() +
					 "&txttelefono=" + str_encode($("#txt_telefono").val()) +
					 "&txtfechanacimientoaux=" + $("#txt_fecha_nacimiento").val() +
					 "&cmb_id_pais_nac=" + //$("#cmb_id_pais_nac").val() +
					 "&cmb_departamento_nac=" + //cmb_departamento_nac +
					 "&cmb_municipio_nac=" + //cmb_municipio_nac +
					 "&txt_nombre_dep_nac=" + //str_encode(txt_nombre_dep_nac) +
					 "&txt_nombre_mun_nac=" + //str_encode(txt_nombre_mun_nac) +
					 "&cmb_id_pais=" + $("#cmb_id_pais").val() +
					 "&cmb_departamento=" + cmb_departamento +
					 "&cmb_municipio=" + cmb_municipio +
					 "&txt_nombre_dep=" + str_encode(txt_nombre_dep) +
					 "&txt_nombre_mun=" + str_encode(txt_nombre_mun) +
					 "&txtdireccion=" + str_encode($("#txt_direccion").val()) +
					 "&cmbsexo=" + $("#cmb_sexo").val() +
					 "&cmbdesplazado=" + //cmbdesplazado +
					 "&cmbetnia=" + //cmbetnia +
					 "&cmbzona=" + //$("#cmb_zona").val() +
					 "&hddidconsultaaux=" + hddidconsultaaux[1] +
					 "&txttelefono2=" + //str_encode($("#txt_telefono2").val()) +
					 "&txtemail=" + str_encode($("#txt_email").val()) +
					 "&id_cita=" + hddidconsultaaux[0] +
					 "&id_tipo_cita=" + $("#cmb_tipo_cita").val() +
					 "&hddpacienteexisteaux=" + hddpacienteexisteaux[0] +
					 "&cmbtiposangre=" + //$("#cmb_tipo_sangre").val() +
					 "&txtpresionarterial=" + //txt_presionarterial +
					 "&txtpulso=" + //txt_pulso +
					 "&txt_nombre_cirugia=" + str_encode(txt_nombre_cirugia) +
					 "&txt_fecha_cirugia=" + txt_fecha_cirugia +
					 "&cmb_ojo=" + cmb_ojo +
					 "&cmb_num_cirugia=" + cmb_num_cirugia +
					 "&txt_nombre_med_orden=" + str_encode(txt_nombre_med_orden) +
					 "&cmb_cant_examenes=" + cmb_cant_examenes +
					 "&cmbnumerohijos=" + //$("#cmb_numerohijos").val() +
					 "&cmbnumerohijas=" + //$("#cmb_numerohijas").val() +
					 "&cmbnumerohermanos=" + //$("#cmb_numerohermanos").val() +
					 "&cmbnumerohermanas=" + //$("#cmb_numerohermanas").val() +
					 "&txtacompanante=" + //str_encode($("#txt_acompanante").val()) +
					 "&txtplan=" + $("#cmb_plan").val() +
					 "&factorrh=" + //$("#cmb_factor_rh").val() +
					 "&cmb_lugar_cita=" + $("#cmb_lugar_cita").val() +
					 "&cmbconvenio=" + $("#cmb_convenio").val() +
					 "&txtprofesion=" + //str_encode($("#txt_profesion").val()) +
					 "&txtmconsulta=" + str_encode($("#txt_mconsulta").val()) +
					 "&txt_observaciones_admision=" + str_encode($("#txt_observaciones_admision").val()) +
					 "&cmb_medio_pago=" + //$("#cmb_medio_pago").val() +
					 "&cmbprofesionalAtiende=" + cmbprofesionalAtiende +
					 "&post=" + $("#hdd_post").val() +
					 "&cant_tipo_cita_det=" + cant_tipo_cita_det +
					 "&hdd_id_admision_paciente=" + hdd_id_admision_paciente +
					 "&hdd_estado_consulta=" + hdd_estado_consulta +
					 "&cmb_estado_civil=" + //$("#cmb_estado_civil").val() +
					 "&txt_num_carnet=" + txt_num_carnet +
					 "&tipo_coti_paciente=" + tipo_coti_paciente +
					 "&rango_paciente=" + rango_paciente;
		
		if (cmb_cant_examenes != "" && cmb_cant_examenes != "") {
			for (var i = 0; i < parseInt(cmb_cant_examenes, 10); i++) {
				params += "&id_examen_" + i + "=" + $("#cmb_examen_" + i).val() +
						  "&id_ojo_examen_" + i + "=" + $("#cmb_ojo_examen_" + i).val();
			}
		}
		
		for (var i = 0; i < cant_tipo_cita_det; i++) {
			params += "&id_tipo_reg_cita_det_" + i + "=" + $("#hdd_tipo_reg_cita_det_" + i).val() +
					  "&id_estado_atencion_cita_det_" + i + "=" + $("#hdd_estado_atencion_cita_det_" + i).val() +
					  "&id_usuario_cita_det_" + i + "=" + $("#cmb_usuario_cita_det_" + i).val();
		}
		
		llamarAjax("admision_ajax.php", params, "sqle", "verificar_guardar_admision();");
	}
}

function verificar_guardar_admision() {
    //Aqui debe verificar que el procedimiento almacenado se ejecuto con exito.
    //Redirige a la página: estado_atencion.php
    var resultado_aux = ($("#hdd_resultado_crear").val() != "" ? parseInt($("#hdd_resultado_crear").val(), 10) : -100);

    if (resultado_aux > 0) {
        $("#contenedor_error").css("display", "none");
        $("#contenedor_exito").css("display", "block");
        $("#contenedor_exito").html("Admisi&oacute;n registrada con &eacute;xito");
        $("#hdd_id_admision_paciente").val(resultado_aux);
        //imprimir_recibo_pago();
		//setTimeout(function() { enviar_credencial("../admisiones/estado_atencion.php", 13); }, 1000);
		setTimeout(function() { enviar_formulario_registrar_pago(resultado_aux, 0); }, 1000);
    } else if (resultado_aux == 0) {
        $("#contenedor_error").css("display", "block");
        $("#contenedor_error").html("Error - No se encontraron profesionales disponibles para continuar con la atenci&oacute;n");
        $("#btnNuevo").removeAttr("disabled");
        window.scrollTo(0, 0);
    } else if (resultado_aux == -11) {
        $("#contenedor_error").css("display", "block");
        $("#contenedor_error").html("Error - El n&uacute;mero de documento solamente debe contener caracteres num&eacute;ricos");
        $("#btnNuevo").removeAttr("disabled");
        window.scrollTo(0, 0);
    } else if (resultado_aux == -12) {
        $("#contenedor_error").css("display", "block");
        $("#contenedor_error").html("Error - El n&uacute;mero de documento tiene una longitud incorrecta");
        $("#btnNuevo").removeAttr("disabled");
        window.scrollTo(0, 0);
    } else if (resultado_aux == -13) {
        $("#contenedor_error").css("display", "block");
        $("#contenedor_error").html("Error - Fecha de nacimiento incorrecta");
        $("#btnNuevo").removeAttr("disabled");
        window.scrollTo(0, 0);
    } else if (resultado_aux == -14) {
        $("#contenedor_error").css("display", "block");
        $("#contenedor_error").html("Error - La fecha de nacimiento no concuerda con el tipo de documento");
        $("#btnNuevo").removeAttr("disabled");
        window.scrollTo(0, 0);
    } else {
        $("#contenedor_error").css("display", "block");
        $("#contenedor_error").html("Error interno al tratar de registrar la admisi&oacute;n (C&oacute;digo de error: " + resultado_aux + ")");
        $("#btnNuevo").removeAttr("disabled");
        window.scrollTo(0, 0);
    }
}

function verificar_post_usuario() {//Verifica si algun dato fue enviado por post
    var hdd_id_post = $("#hdd_id_post").val();
    var hdd_id_consulta = $("#hdd_id_consulta").val();
    if (hdd_id_post == 1) {
        var params = "opcion=1&hdd_id_consulta=" + trim(hdd_id_consulta);
        llamarAjax("admision_ajax.php", params, "hdd_paciente_existe", "verifica();");

        //Muestra el enlace para regresar a la ventana de estado_atencion.php
        $(".breadcrumb ul").append("<li><a href='#' onclick='enviar_credencial(\"../admisiones/estado_atencion.php\", 13);'>Estado atencion</a></li><li class='breadcrumb_on'>Admision</li>");
    } else {
        //Solo muestra la ventana actual en la navegacion
        $(".breadcrumb ul").append("<li class='breadcrumb_on'>Admision</li>");
    }
}

function verifica() { //Verifica si el usuario Existe
    var pacienteExiste = $("#hdd_paciente_existe").val().split(":");
    var post = $("#hdd_post").val();
    var txt_id = $("#txt_id").val();
    var txt_nombre = $("#txt_nombre").val() + " " + $("#txt_nombre2").val() + " " + $("#txt_apellido").val() + " " + $("#txt_apellido2").val();

    if (pacienteExiste[0] == 1) {
        $("#contenedor_exito").css("display", "block");
        $("#contenedor_exito").html('Existe un paciente con n&uacute;mero de documento <span style="font-weight: bold;">' + txt_id + '</span> y nombre <span style="font-weight: bold;">' + txt_nombre + "</span>.<br />Por favor verifique que se trata de la misma persona y actualice los datos estad&iacute;sticos del paciente.");
        $("#btnBuscarusuario").css("display", "none");
    } else if (pacienteExiste[0] == 0) { //contenedor_advertencia
        if ($("#hdd_id_post").val() != "") {
            $("#contenedor_advertencia").css("display", "block");
            $("#contenedor_advertencia").html("El paciente no existe, verifique los datos antes de guardar");
        }
    } else if (pacienteExiste[0] == 2) { //contenedor_advertencia
        $("#d_admision").css("display", "none");
        $("#contenedor_confirmacion").css("display", "block");
        $("#contenedor_confirmacion").html(
			'<h5>Se encontr&oacute; un paciente con ' + pacienteExiste[3] + ' n&uacute;mero ' + pacienteExiste[2] + ' y nombre ' + pacienteExiste[1] + '</h5>' +
			'<h5> &iquest;Desea cargar los datos de este paciente? </h5>' +
			'<div id="confirmacion">' +
				'<input type="button" value="Aceptar" class="btnPrincipal peq" onClick="confirmacion(1);" />&nbsp;&nbsp;&nbsp;&nbsp;' +
				'<input type="button" value="Cancelar" class="btnPrincipal peq" onClick="confirmacion(2);" />' +
			'</div>' +
			'<div class="clear_both"></div>');
    }
	
    if ($("#cmb_id_pais").val() == "1") {
        $("#tr_departamento").css("display", "table-row");
        $("#tr_municipios").css("display", "table-row");
    }

    if ($("#hdd_id_post").val() == 1) {
        //Muestra el enlace para regresar a la ventana  de estado_atencion.php
        $(".breadcrumb ul li").remove();
        $(".breadcrumb ul").append("<li><a href='#' onclick='enviar_credencial(\"../admisiones/estado_atencion.php\", 13);'>Estados de atenci\u00F3n</a></li><li class='breadcrumb_on'>Admisiones</li>");
    }

    if ($("#hdd_paciente_existe").val() == 3) {//Verifica si hdd_paciente_existe esta con valor de 3 = el paciente no existe
        $("#contenedor_advertencia").css("display", "block");
        $("#contenedor_advertencia").html("No se hall&oacute; historia cl&iacute;nica, pregunte al paciente si es la primera vez que asiste al consultorio.");
    }

    if (post == 0) {//Si no recibe datos por Post
        $("#txt_id").blur(function() {//Crea el evento blur para para caja de texto del número de documento
            var eventoPistola = $("#hdd_evento_pistola").val();

            if (eventoPistola == 0) {
                var ndocumento = $("#txt_id").val();
                var tipodocumento = $("#cmb_tipo_id").val();
                if (ndocumento.length >= 1 && tipodocumento.length >= 1) {
                    var params = "opcion=7&ndocumento=" + ndocumento + "&tipodocumento=" + tipodocumento;
                    llamarAjax("admision_ajax.php", params, "hdd_post_existe", "postVerificaDatos();");
                }
            }
        })
    }
}

function verificaEventoPistola() {
    var ndocumento = $("#txt_id").val();
    var tipodocumento = $("#cmb_tipo_id").val();
    if (ndocumento.length >= 1 && tipodocumento.length >= 1) {
        var params = "opcion=7&ndocumento=" + ndocumento + "&tipodocumento=" + tipodocumento;
        llamarAjax("admision_ajax.php", params, "hdd_post_existe", "postVerificaDatos();");
    }
}

function postVerificaDatos() {//Funcion que verifica si hay datos en la funcion ajax de la funcion verifica() js
    var eventoPistola = $("#hdd_evento_pistola").val();

    if ($("#hdd_datos_paciente").val() != "") {
        var resultado = $("#hdd_datos_paciente").val().split(":");
        $("#fondo_negro").css("display", "block");
        $("#d_centro").slideDown(400).css("display", "block");

        var codigohtml =
			'<div class="encabezado">' +
				'<h3>Existe un registro con la informaci&oacute;n</h3>' +
			'</div>' +
			'<table style="width:100%;">' +
				'<tr>' +
					'<td colspan="2" style="text-align:center; color: #33338C;"></td>' +
				'</tr>' +
				'<tr>' +
					'<td style="text-align:right;">Tipo de documento:</td>' +
					'<td style="text-align:left;font-weight: 700;">' + resultado[5] + '</td>' +
				'</tr>' +
				'<tr>' +
					'<td style="text-align:right;">N&uacute;mero de documento:</td>' +
					'<td style="text-align:left;font-weight: 700;">' + resultado[6] + '</td>' +
				'</tr>' +
				'<tr>' +
					'<td style="text-align:right;">Nombre(s):</td>' +
					'<td style="text-align:left;font-weight: 700;">' + resultado[7] + '</td>' +
				'</tr>' +
				'<tr>' +
					'<td style="text-align:right;">Apellido(s):</td>' +
					'<td style="text-align:left;font-weight: 700;">' + resultado[8] + '</td>' +
				'</tr>' +
				'<tr>' +
					'<td colspan="2">' +
						'<p style="color: #FF552A;font-size: 10pt;font-weight: 700;">' +
							'\u00BFDesea seleccionar el registro e importar los datos?' +
						'</p>' +
					'</td>' +
				'</tr>' +
				'<tr>' +
					'<td colspan="2" style="text-align:center;">' +
						'<br/><br/>' +
						'<input type="button" class="btnPrincipal peq" value="Aceptar" onclick="opcionesFormularioflotante(1);" />&nbsp;&nbsp;' +
						'<input type="button" class="btnSecundario peq" value="Cancelar" onclick="opcionesFormularioflotante(0);" />' +
					'</td>' +
				'</tr>' +
			'</table>';
        $(".div_interno").html(codigohtml);

        posicionarDivFlotante("d_centro");
    } else {
        opcionesFormularioflotante(0);
    }
}

function cargar_dep_muni(sufijo) {
    $("#cmb_departamento" + sufijo).val($("#hdd_cod_dep_b").val());
    seleccionar_departamento($("#hdd_cod_dep_b").val(), $("#hdd_cod_mun_dane_b").val(), sufijo);
}

function marcar_tipo_sangre(tipo_sangre) {
    var selectobject = document.getElementById("cmb_tipo_sangre")
    var valor_sel = "";
    for (var i = 0; i < selectobject.length; i++) {
        if (selectobject.options[i].text == tipo_sangre) {
            valor_sel = selectobject.options[i].value;
            break;
        }
    }

    if (valor_sel != "") {
        selectobject.value = valor_sel;
    }
}

function marcar_factor_rh(factor_rh) {
    var selectobject = document.getElementById("cmb_factor_rh")
    var valor_sel = "";
    for (var i = 0; i < selectobject.length; i++) {
        if (selectobject.options[i].text == factor_rh) {
            valor_sel = selectobject.options[i].value;
            break;
        }
    }

    if (valor_sel != "") {
        selectobject.value = valor_sel;
    }
}

function opcionesFormularioflotante(n1) {
    if (n1 == 1) {
        var resultado = $("#hdd_datos_paciente").val().split(":");
        var datosPacienteCedula = $("#hdd_evento_pistola").val().split(",");
        if (datosPacienteCedula[0] != "0") {
            $("#txt_nombre").val(trim(datosPacienteCedula[3]));
            $("#txt_nombre2").val(trim(datosPacienteCedula[4]));
            $("#txt_apellido").val(trim(datosPacienteCedula[1]));
            $("#txt_apellido2").val(trim(datosPacienteCedula[2]));

            if (datosPacienteCedula[5] == "M") {
                $("#cmb_sexo").val(2);
            } else if (datosPacienteCedula[5] == "F") {
                $("#cmb_sexo").val(1);
            }

            var anio = datosPacienteCedula[6].substring(0, 4);
            var mes = datosPacienteCedula[6].substring(4, 6);
            var dia = datosPacienteCedula[6].substring(6, 8);
            $("#txt_fecha_nacimiento").val(dia + "/" + mes + "/" + anio);

            var tipoSangre = trim(datosPacienteCedula[8]);

            var tipo_sangre_aux = "";
            var factor_rh_aux = "";
            if (tipoSangre.length == 2) {
                tipo_sangre_aux = tipoSangre.substring(0, 1);
                factor_rh_aux = tipoSangre.substring(1, 2);
            } else if (tipoSangre.length == 3) {
                tipo_sangre_aux = tipoSangre.substring(0, 2);
                factor_rh_aux = tipoSangre.substring(2, 3);
            }
            marcar_tipo_sangre(tipo_sangre_aux);
            marcar_factor_rh(factor_rh_aux);

            $("#txt_telefono").val(resultado[1]);
            $("#txt_telefono2").val(resultado[2]);
            $("#txt_email").val(resultado[3]);
            $("#cmb_id_pais").val(resultado[12]);
            seleccionar_pais(resultado[12], "");
            $("#cmb_departamento").val(resultado[13]);
            seleccionar_departamento(resultado[13], resultado[14], "");
            $("#txt_nombre_dep").val(resultado[15]);
            $("#txt_nombre_mun").val(resultado[16]);
            $("#txt_direccion").val(resultado[18]);
            $("#txt_profesion").val(resultado[19]);
            $("#cmb_zona").val(resultado[20]);
            $("#cmb_desplazado").val(resultado[21]);
            $("#cmb_etnia").val(resultado[22]);
            $("#cmb_numerohijos").val(resultado[27]);
            $("#cmb_numerohijas").val(resultado[36]);
            $("#cmb_numerohermanos").val(resultado[28]);
            $("#cmb_numerohermanas").val(resultado[37]);
            $("#cmb_estado_civil").val(trim(resultado[29]));
            $("#cmb_id_pais_nac").val(resultado[30]);
            seleccionar_pais(resultado[30], "_nac");
            $("#cmb_departamento_nac").val(resultado[31]);
            seleccionar_departamento(resultado[31], resultado[32], "_nac");
            $("#cmb_municipio_nac").val(resultado[32]);
            $("#txt_nombre_dep_nac").val(resultado[33]);
            $("#txt_nombre_mun_nac").val(resultado[34]);
        } else {
            //Asigna los valores a los campos
            $("#txt_fecha_nacimiento").val(resultado[17]);
            $("#txt_nombre").val(trim(resultado[17]));
            $("#cmb_tipo_sangre").val(resultado[9]);
            $("#cmb_factor_rh").val(resultado[10]);
            $("#txt_telefono").val(resultado[1]);
            $("#txt_telefono2").val(resultado[2]);
            $("#txt_email").val(resultado[3]);
            $("#cmb_sexo").val(resultado[11]);
            $("#cmb_id_pais").val(resultado[12]);
            seleccionar_pais(resultado[12], "");
            $("#cmb_departamento").val(resultado[13]);
            seleccionar_departamento(resultado[13], resultado[14], "");
            $("#txt_nombre_dep").val(resultado[15]);
            $("#txt_nombre_mun").val(resultado[16]);
            $("#txt_direccion").val(resultado[18]);
            $("#txt_profesion").val(resultado[19]);
            $("#cmb_zona").val(resultado[20]);
            $("#cmb_desplazado").val(resultado[21]);
            $("#cmb_etnia").val(resultado[22]);
            $("#txt_nombre").val(trim(resultado[23]));
            $("#txt_nombre2").val(trim(resultado[24]));
            $("#txt_apellido").val(trim(resultado[25]));
            $("#txt_apellido2").val(trim(resultado[26]));
            $("#cmb_numerohijos").val(trim(resultado[27]));
            $("#cmb_numerohijas").val(trim(resultado[36]));
            $("#cmb_numerohermanos").val(trim(resultado[28]));
            $("#cmb_numerohermanas").val(trim(resultado[37]));
            $("#cmb_estado_civil").val(trim(resultado[29]));
            $("#cmb_id_pais_nac").val(resultado[30]);
            seleccionar_pais(resultado[30], "_nac");
            $("#cmb_departamento_nac").val(resultado[31]);
            seleccionar_departamento(resultado[31], resultado[32], "_nac");
            $("#txt_nombre_dep_nac").val(resultado[33]);
            $("#txt_nombre_mun_nac").val(resultado[34]);
        }
        $("#hdd_id_paciente").val(resultado[35]);

        //Se borra el componente de historia clínica actual
        $("#caja_flotante").remove();

        //Se carga de nuevo el componente de historia clínica
        cargar_componente_hc_adm(resultado[35]);

        //Cambia el estado de hdd_paciente_existe a = 2 (el paciente existe)
        $("#hdd_paciente_existe").val("2");

        //Se actualizan los datos originales del paciente
        if ($("#hdd_numero_documento_ori").val() == "") {
            $("#hdd_numero_documento_ori").val($("#txt_id").val());
            $("#hdd_nombre_completo_ori").val(obtener_nombre_completo($("#txt_nombre").val(), $("#txt_nombre2").val(), $("#txt_apellido").val(), $("#txt_apellido2").val()));
        }

        //Oculta el div flotante
        $("#d_centro").css("display", "none");
        $("#fondo_negro").css("display", "none");
    } else if (n1 == 0) {
        //Oculta el div flotante
        $("#d_centro").css("display", "none");
        $("#fondo_negro").css("display", "none");

        var datosPacienteCedula = $("#hdd_evento_pistola").val().split(",");
        if (datosPacienteCedula[0] != "0") {
            $("#txt_nombre").val(trim(datosPacienteCedula[3]));
            $("#txt_nombre2").val(trim(datosPacienteCedula[4]));
            $("#txt_apellido").val(trim(datosPacienteCedula[1]));
            $("#txt_apellido2").val(trim(datosPacienteCedula[2]));

            if (datosPacienteCedula[5] == "M") {
                $("#cmb_sexo").val(2);
            } else if (datosPacienteCedula[5] == "F") {
                $("#cmb_sexo").val(1);
            }

            var anio = datosPacienteCedula[6].substring(0, 4);
            var mes = datosPacienteCedula[6].substring(4, 6);
            var dia = datosPacienteCedula[6].substring(6, 8);
            $("#txt_fecha_nacimiento").val(dia + "/" + mes + "/" + anio);

            var params = "opcion=13&cod_mun_reg=" + datosPacienteCedula[7];

            llamarAjax("admision_ajax.php", params, "d_municipio_reg", "cargar_dep_muni(\"_nac\");");

            var tipoSangre = trim(datosPacienteCedula[8]);

            var tipo_sangre_aux = "";
            var factor_rh_aux = "";
            if (tipoSangre.length == 2) {
                tipo_sangre_aux = tipoSangre.substring(0, 1);
                factor_rh_aux = tipoSangre.substring(1, 2);
            } else if (tipoSangre.length == 3) {
                tipo_sangre_aux = tipoSangre.substring(0, 2);
                factor_rh_aux = tipoSangre.substring(2, 3);
            }
            marcar_tipo_sangre(tipo_sangre_aux);
            marcar_factor_rh(factor_rh_aux);
        }
    }
}

function seleccionar_departamento(cod_dep, cod_mun_dane, sufijo) {
	var params = "opcion=2&cod_dep=" + cod_dep +
				 "&cod_mun_dane=" + cod_mun_dane +
				 "&sufijo=" + sufijo;
	
	llamarAjax("admision_ajax.php", params, "d_municipios" + sufijo, "");
}

function seleccionar_pais(id_pais, sufijo) {
    if (id_pais == "1") {
        $("#td_departamento" + sufijo + "_label").css("display", "table-cell");
        $("#td_municipio" + sufijo + "_label").css("display", "table-cell");
        $("#td_departamento" + sufijo).css("display", "table-cell");
        $("#td_municipio" + sufijo).css("display", "table-cell");
        $("#td_departamento" + sufijo + "_n_label").css("display", "none");
        $("#td_municipio" + sufijo + "_n_label").css("display", "none");
        $("#td_departamento" + sufijo + "_n").css("display", "none");
        $("#td_municipio" + sufijo + "_n").css("display", "none");
    } else {
        $("#td_departamento" + sufijo + "_label").css("display", "none");
        $("#td_municipio" + sufijo + "_label").css("display", "none");
        $("#td_departamento" + sufijo).css("display", "none");
        $("#td_municipio" + sufijo).css("display", "none");
        $("#td_departamento" + sufijo + "_n_label").css("display", "table-cell");
        $("#td_municipio" + sufijo + "_n_label").css("display", "table-cell");
        $("#td_departamento" + sufijo + "_n").css("display", "table-cell");
        $("#td_municipio" + sufijo + "_n").css("display", "table-cell");
    }
}

function confirmacion(n1) {//Esta funcion procesa el div de confirmacion
    if (n1 == 1) { //Si acepta la peticion
        $("#contenedor_confirmacion").css("display", "none");
        $("#d_admision").css("display", "block");
        $("#btnBuscarusuario").css("display", "none");//Oculta el boton: buscar usuarios
    } else if (n1 == 2) { //Si cancela la peticion         
        $("#contenedor_confirmacion").css("display", "none");
        $("#d_admision").css("display", "block");

        $("#hdd_paciente_existe").val("3");

        var hddpacientecancelar = $("#hdd_paciente_cancelar").val().split(":");

        //Limpia el formulario
        $("#cmb_sexo").val("");
        $("#txt_nombre").val(hddpacientecancelar[0]);
        $("#txt_nombre2").val(hddpacientecancelar[1]);
        $("#txt_apellido").val(hddpacientecancelar[2]);
        $("#txt_apellido2").val(hddpacientecancelar[3]);
        $("#txt_fecha_nacimiento").val("");
        $("#txt_telefono").val("");
        $("#cmb_id_pais").val("");
        $("#cmb_departamento").val("");
        $("#cmb_municipio").val("");
        $("#txt_nombre_dep").val("");
        $("#txt_nombre_mun").val("");
        $("#txt_direccion").val("");
        $("#cmb_zona").val("");
        $("#cmb_desplazado").val("");
        $("#cmb_etnia").val("");
        $("#btnBuscarusuario").css("display", "none");//Oculta el boton: buscar usuarios
    }
}

function seleccionar_convenio(id_plan, modificar_valores) {
	var id_convenio = $("#cmb_convenio").val();
	var id_paciente = $("#hdd_id_paciente").val();
	var params = "opcion=5&id_convenio=" + id_convenio +
				 "&id_plan=" + id_plan +
				 "&id_paciente=" + id_paciente +
				 "&modificar_valores=" + modificar_valores;
	
	llamarAjax("admision_ajax.php", params, "d_plan", "borrarPrecios(); actualizar_status_convenio_paciente();");
	
	//Se verifica si el convenio es de ecopetrol
	var ind_eco = "0";
	var ind_num_carnet = "0";
	var ind_num_carnet_obl = "0";
	if (isObject(document.getElementById("hdd_eco_convenio_" + id_convenio))) {
		ind_eco = $("#hdd_eco_convenio_" + id_convenio).val();
		ind_num_carnet = $("#hdd_ind_num_carnet_" + id_convenio).val();
		ind_num_carnet_obl = $("#hdd_ind_num_carnet_obl_" + id_convenio).val();
	}
	
	if (ind_eco == "1") {
		$("#tr_nombre_med_orden_label").css("display", "table-row");
		$("#tr_nombre_med_orden").css("display", "table-row");
	} else {
		$("#tr_nombre_med_orden_label").css("display", "none");
		$("#tr_nombre_med_orden").css("display", "none");
		$("#txt_nombre_med_orden").val("");
	}
	
	if (ind_num_carnet == "1") {
		$("#td_num_carnet_l").css("display", "table-cell");
		$("#td_num_carnet").css("display", "table-cell");
		$("#hdd_num_carnet_obl").val(ind_num_carnet_obl);
		
		if ($("#txt_num_carnet").val() == "") {
			params = "opcion=18&id_convenio=" + id_convenio +
					 "&id_paciente=" + id_paciente;
			
			llamarAjax("admision_ajax.php", params, "d_num_carnet_busq", "actualizar_num_carnet();");
		}
	} else {
		$("#td_num_carnet_l").css("display", "none");
		$("#td_num_carnet").css("display", "none");
		$("#hdd_num_carnet_obl").val("0");
		$("#txt_num_carnet").val("");
	}
}

function actualizar_num_carnet() {
    var num_carnet_aux = $("#hdd_num_carnet_busq").val();
    if (num_carnet_aux != "") {
        $("#txt_num_carnet").val(num_carnet_aux);
    }
}

function settipocita(modificar_valores, id_tipo_cita_base) {//Obtiene el Tipo de cita
    var params = "opcion=6&id_tipo_cita_base=" + id_tipo_cita_base +
            	 "&modificar_valores=" + modificar_valores;

    if ($("#cmd_profesionalAtiende").length) {
        params += "&idprofesional=" + $("#cmd_profesionalAtiende").val();
    } else {
        params += "&idprofesional=" + $("#hdd_usuario_prof").val();
    }

    llamarAjax("admision_ajax.php", params, "cmbTipoCita", "");
}

function cargar_precios(modificar, ind_inicial) {
    var tipoCita = $("#cmb_tipo_cita").val();
	var id_paciente = $("#hdd_id_paciente").val();
    var id_convenio = $("#cmb_convenio").val();
    var id_plan = $("#cmb_plan").val();
    var medio_pago = $("#cmb_medio_pago").val();
    var id_admision_paciente = $("#hdd_id_admision_paciente").val();
    var nombreConvenio = $("#cmb_convenio option:selected").text();
    var exento = $("#hdd_exento").val();
	var tipo_coti_paciente = $("#cmb_tipo_coti_paciente").val();
    var rango_paciente = $("#cmb_rango_paciente").val();
	
    if (tipoCita.length > 0) {
        if (id_plan.length > 0) {
            var tipoCita = $("#cmb_tipo_cita").val();
            var params = "opcion=8&tipoCita=" + tipoCita +
						 "&id_paciente=" + id_paciente +
						 "&id_convenio=" + id_convenio +
						 "&id_plan=" + id_plan +
						 "&id_admision_paciente=" + id_admision_paciente +
						 "&modificar_precios=" + modificar +
						 "&medio_pago=" + medio_pago +
						 "&ind_inicial=" + ind_inicial +
						 "&nombreConvenio=" + nombreConvenio +
						 "&exento=" + exento +
						 "&tipo_coti_paciente=" + tipo_coti_paciente +
						 "&rango_paciente=" + rango_paciente;
			
            if ($("#tr_examenes").is(":visible")) {
                var cant_examenes = $("#cmb_cant_examenes").val();
                if (cant_examenes != "") {
                    cant_examenes = parseInt(cant_examenes, 10);
                } else {
                    cant_examenes = 0;
                }
                params += "&cant_examenes=" + cant_examenes;
                for (var i = 0; i < cant_examenes; i++) {
                    params += "&id_examen_" + i + "=" + $("#cmb_examen_" + i).val() +
							  "&id_ojo_" + i + "=" + $("#cmb_ojo_examen_" + i).val();
                }
            } else {
                params += "&cant_examenes=0";
            }
			
            llamarAjax("admision_ajax.php", params, "precios", "");
            $("#precios").css("border", "none");
        }
    } else {
        alert("Debe seleccionar el tipo de Cita");
        $("#cmb_plan").val("");
    }

    if (id_plan.length <= 0) {
        borrarPrecios();
    }
}

function borrarPrecios() {
    $("#tablaPrecios").remove();
    $("#resumenLiquidacion").remove();
}

function preciosFlotantes() {
    var params = "opcion=9&flotante=" + 1;
    llamarAjax("admision_ajax.php", params, "d_interno", "");

    $("#fondo_negro").css("display", "block");
    $("#d_centro").slideDown(400).css("display", "block");
}

function buscar_precios() {
    var params = "opcion=10&id_plan=" + $("#cmb_plan").val() +
				 "&parametro=" + $("#txt_identificacion_interno").val();
	
    llamarAjax("admision_ajax.php", params, "rPrecios", "cargarPreciosModal();");
}

function validarFormularioPrecios() {
    $("#frmBuscarPrecios").validate({
        debug: false,
        rules: {
            txt_identificacion_interno: {
                required: true,
            },
        },
        submitHandler: function(form) {
            buscar_precios();

            return false;
        }
    });
}

//Funcion carga los precios en la ventana flotante
function cargarPreciosModal() {
    var myArray = [];
    var filas = $("#tablaPrecios tr").length;

    for (i = 1; i <= (filas - 1); i++) {
        myArray.push($("#tablaPrecios tr:eq(" + i + ") td:eq(0) input").attr("id"));
    }

    //Agrega los cheked en la ventana flotante de precios
    for (i = 0; i <= myArray.length; i++) {
        $("#b" + myArray[i] + "").remove();
    }

    //verifica si no hay regisros
    var filas = $("#tablaPreciosModal tr").length;

    if (filas == 1) {
        $("#tablaPreciosModal").append('<tr><td colspan="3">No hay registros</td></tr>');
        $("#btnModalAgregar").remove();
    }
}

//Elimina el precio seleccionado
function borrarPrecio(id_tr) {
    //Elimina la fila: no hay resultados en la tabla con id: tablaPrecios
    $("#" + id_tr).remove();
    var cont_aux = parseInt($("#hdd_ult_index_precio").val(), 10);
    var ult_index_precio = 0;
    for (var i = cont_aux; i >= 1; i--) {
        if (isObject(document.getElementById("tr_precios_" + i))) {
            ult_index_precio = i;
            break;
        }
    }
    $("#hdd_ult_index_precio").val(ult_index_precio);

    //El número de filas de la tabla con id: tablaPrecios
    var filas = $("#tablaPrecios tr").length;

    //Agrega el mensaje: no hay resultados en la tabla con id: tablaPrecios
    if (filas <= 1) {
        $("#tablaPrecios").append("<tr id=\"tr_precio_no_hallado\"><td colspan=\"6\">No hay resultados</td></tr>");
    } else {
		//Se recalculan los valores de cirugías
		recalcular_precios_cx_cop_cm();
	}
}

function agregarPrecios(ind_modificar_pagos) {
	var myArray = [];
	//Cuantas filas hay en el listado
	var filas = $("#tablaPreciosModal tr").length;
	
	$("#tablaPrecios #tr_precio_no_hallado").remove();
	
	var disabled_aux = "";
	if (ind_modificar_pagos != 1) {
		disabled_aux = "disabled";
	}
	
	var cont_aux = parseInt($("#hdd_ult_index_precio").val(), 10);
	for (i = 1; i < filas; i++) {
		if ($("#hdd_sel_proc_b_" + i).is(":checked")) {
			if ($("#hdd_tipo_precio_b_" + i).val() != "Q") {
				cont_aux++;
				
				var ind_tipo_pago = $("#hdd_tipo_pago_b_" + i).val();
				var disabled_cuota_aux = "";
				if (ind_tipo_pago != "1" || ind_modificar_pagos != 1) {
					disabled_cuota_aux = "disabled";
				}
				
				//imprime las filas en la tabla tablaPrecios
				$("#tablaPrecios").append(
					'<tr id="tr_precios_' + cont_aux + '">\n' +
						'<td align="left" class="td_reducido">\n' +
							'<input type="hidden" name="hdd_id_precio_' + cont_aux + '" id="hdd_id_precio_' + cont_aux + '" value="' + $("#hdd_id_precio_b_" + i).val() + '" />\n' +
							'<input type="hidden" name="hdd_tipo_precio_' + cont_aux + '" id="hdd_tipo_precio_' + cont_aux + '" value="' + $("#hdd_tipo_precio_b_" + i).val() + '" />\n' +
							'<input type="hidden" name="hdd_cod_servicio_' + cont_aux + '" id="hdd_cod_servicio_' + cont_aux + '" value="' + $("#hdd_cod_servicio_b_" + i).val() + '" />\n' +
							'<input type="hidden" name="hdd_tipo_bilateral_' + cont_aux + '" id="hdd_tipo_bilateral_' + cont_aux + '" value="' + $("#hdd_tipo_bilateral_b_" + i).val() + '" />\n' +
							$("#hdd_nombre_proc_b_" + i).val() + ' (' + $("#hdd_cod_servicio_b_" + i).val() + ')' + '\n' +
						'</td>\n' +
						'<td align="left" class="td_reducido">' +
							'<div id="d_convenio_pago_' + cont_aux + '" style="width:100%;"></div>' +
							'<div id="d_plan_pago_' + cont_aux + '" style="width:100%;"></div>' +
							'<div id="d_plan_pago_sel_' + cont_aux + '" style="display:none;"></div>' +
						'</td>' +
						'<td align="center">' +
							'<input type="text" name="txt_num_autorizacion_' + cont_aux + '" id="txt_num_autorizacion_' + cont_aux + '" value="" class="no-margin" maxlength="20" onblur="trim_cadena(this);" />' +
						'</td>' +
						'<td align="center">' + $("#hdd_nombre_bilateral_b_" + i).val() + '</td>' +
						'<td class="td_reducido">\n' +
							'<input type="text" name="txt_cant_precio_' + cont_aux + '" id="txt_cant_precio_' + cont_aux + '" value="1" class="texto_centrado no-margin" maxlength="3" onkeypress="return solo_numeros(event, false);" onchange="recalcular_totales_liquidacion();" />\n' +
						'</td>\n' +
						'<td class="td_reducido">\n' +
							'<input type="hidden" name="hdd_tipo_pago_' + cont_aux + '" id="hdd_tipo_pago_' + cont_aux + '" value="' + $("#hdd_ind_tipo_pago_b_" + i).val() + '" />\n' +
							'<input type="text" name="txt_valor_pago_' + cont_aux + '" id="txt_valor_pago_' + cont_aux + '" value="' + $("#hdd_precio_b_" + i).val() + '" class="texto_centrado no-margin" maxlength="9" onkeypress="return solo_numeros(event, false);" onchange="recalcular_totales_liquidacion();" ' + disabled_aux + ' />\n' +
						'</td>\n' +
						'<td>\n' +
							'<input type="text" name="txt_valor_cuota_' + cont_aux + '" id="txt_valor_cuota_' + cont_aux + '" value="' + $("#hdd_precio_cuota_b_" + i).val() + '" class="texto_centrado no-margin" maxlength="9" onkeypress="return solo_numeros(event, false);" onchange="recalcular_totales_liquidacion();" ' + disabled_cuota_aux + ' />\n' +
						'</td>\n' +
						'<td style="width: 40px;">\n' +
							'<div class="Error-icon" onclick="borrarPrecio(\'tr_precios_' + cont_aux + '\');"></div>\n' +
						'</td>\n' +
					'</tr>'
				);
				
				mostrar_convenio_pago(cont_aux);
			} else {
				//Paquetes
				var cantidad_b_det = parseInt($("#hdd_cantidad_b_det_" + i).val(), 10)
				for (j = 0; j < cantidad_b_det; j++) {
					cont_aux++;
					
					var ind_tipo_pago = $("#hdd_ind_tipo_pago_b_det_" + i + "_" + j).val();
					var disabled_cuota_aux = "";
					if (ind_tipo_pago != "1" || ind_modificar_pagos != 1) {
						disabled_cuota_aux = "disabled";
					}
					
					//imprime las filas en la tabla tablaPrecios
					$("#tablaPrecios").append(
						'<tr id="tr_precios_' + cont_aux + '">\n' +
							'<td align="left" class="td_reducido">\n' +
								'<input type="hidden" name="hdd_id_precio_' + cont_aux + '" id="hdd_id_precio_' + cont_aux + '" value="' + $("#hdd_id_precio_b_det_" + i + "_" + j).val() + '" />\n' +
								'<input type="hidden" name="hdd_tipo_precio_' + cont_aux + '" id="hdd_tipo_precio_' + cont_aux + '" value="' + $("#hdd_tipo_precio_b_det_" + i + "_" + j).val() + '" />\n' +
								'<input type="hidden" name="hdd_cod_servicio_' + cont_aux + '" id="hdd_cod_servicio_' + cont_aux + '" value="' + $("#hdd_cod_servicio_b_det_" + i + "_" + j).val() + '" />\n' +
								'<input type="hidden" name="hdd_tipo_bilateral_' + cont_aux + '" id="hdd_tipo_bilateral_' + cont_aux + '" value="' + $("#hdd_tipo_bilateral_b_det_" + i + "_" + j).val() + '" />\n' +
								$("#hdd_nombre_servicio_b_det_" + i + "_" + j).val() + ' (' + $("#hdd_cod_servicio_b_det_" + i + "_" + j).val() + ')' + '\n' +
							'</td>\n' +
							'<td align="left" class="td_reducido">' +
								'<div id="d_convenio_pago_' + cont_aux + '" style="width:100%;"></div>' +
								'<div id="d_plan_pago_' + cont_aux + '" style="width:100%;"></div>' +
								'<div id="d_plan_pago_sel_' + cont_aux + '" style="display:none;"></div>' +
							'</td>' +
							'<td align="center">' +
								'<input type="text" name="txt_num_autorizacion_' + cont_aux + '" id="txt_num_autorizacion_' + cont_aux + '" value="" class="no-margin" maxlength="20" onblur="trim_cadena(this);" />' +
							'</td>' +
							'<td align="center">' + $("#hdd_nombre_bilateral_b_det_" + i + "_" + j).val() + '</td>' +
							'<td class="td_reducido">\n' +
								'<input type="text" name="txt_cant_precio_' + cont_aux + '" id="txt_cant_precio_' + cont_aux + '" value="1" class="texto_centrado no-margin" maxlength="3" onkeypress="return solo_numeros(event, false);" onchange="recalcular_totales_liquidacion();" />\n' +
							'</td>\n' +
							'<td class="td_reducido">\n' +
								'<input type="hidden" name="hdd_tipo_pago_' + cont_aux + '" id="hdd_tipo_pago_' + cont_aux + '" value="' + $("#hdd_ind_tipo_pago_b_det_" + i + "_" + j).val() + '" />\n' +
								'<input type="text" name="txt_valor_pago_' + cont_aux + '" id="txt_valor_pago_' + cont_aux + '" value="' + $("#hdd_valor_b_det_" + i + "_" + j).val() + '" class="texto_centrado no-margin" maxlength="9" onkeypress="return solo_numeros(event, false);" onchange="recalcular_totales_liquidacion();" ' + disabled_aux + ' />\n' +
							'</td>\n' +
							'<td>\n' +
								'<input type="text" name="txt_valor_cuota_' + cont_aux + '" id="txt_valor_cuota_' + cont_aux + '" value="' + $("#hdd_valor_cuota_b_det_" + i + "_" + j).val() + '" class="texto_centrado no-margin" maxlength="9" onkeypress="return solo_numeros(event, false);" onchange="recalcular_totales_liquidacion();" ' + disabled_cuota_aux + ' />\n' +
							'</td>\n' +
							'<td style="width: 40px;">\n' +
								'<div class="Error-icon" onclick="borrarPrecio(\'tr_precios_' + cont_aux + '\');"></div>\n' +
							'</td>\n' +
						'</tr>'
					);
					
					mostrar_convenio_pago(cont_aux);
				}
			}
		}
	}
	$("#hdd_ult_index_precio").val(cont_aux);
	
	mostrar_formulario_flotante_adm(0);
}

//Asigna valor 0 al combo box: cmb_plan
function resetCmbPlan() {
    $("#cmb_plan").val("");
}

function seleccionar_tipo_cita(id_tipo_cita, modificar_valores) {
    borrarPrecios();
    resetCmbPlan();

    var ind_preqx = "0";
    var ind_signos_vitales = "0";
    var ind_examenes = "0";
    if (id_tipo_cita != "") {
        ind_preqx = $("#hdd_ind_preqx_" + id_tipo_cita).val();
        ind_signos_vitales = $("#hdd_ind_signos_vitales_" + id_tipo_cita).val();
        ind_examenes = $("#hdd_ind_examenes_" + id_tipo_cita).val();
    }

    if (ind_signos_vitales == "1") {
        $("#td_presion_arterial_label").css("display", "");
        $("#td_presion_arterial").css("display", "");
        $("#td_pulso_label").css("display", "");
        $("#td_pulso").css("display", "");
    } else {
        $("#td_presion_arterial_label").css("display", "none");
        $("#td_presion_arterial").css("display", "none");
        $("#td_pulso_label").css("display", "none");
        $("#td_pulso").css("display", "none");
    }

    if (ind_preqx == "1") {
        $("#tr_cirugia_label").css("display", "");
        $("#tr_cirugia").css("display", "");
    } else {
        $("#tr_cirugia_label").css("display", "none");
        $("#tr_cirugia").css("display", "none");
    }

    if (ind_examenes == "1") {
        $("#tr_examenes").css("display", "");
        $("#tr_examenes2").css("display", "");
    } else {
        $("#tr_examenes").css("display", "none");
        $("#tr_examenes2").css("display", "none");
    }

    cargar_usuarios_tipo_cita();
}

function cargar_usuarios_tipo_cita() {
    var params = "opcion=12&id_tipo_cita=" + $("#cmb_tipo_cita").val();

    if (isObject(document.getElementById("hdd_usuario_prof"))) {
        params += "&id_usuario_prof=" + $("#hdd_usuario_prof").val();
    } else {
        params += "&id_usuario_prof=" + $("#cmd_profesionalAtiende").val();
    }

    llamarAjax("admision_ajax.php", params, "d_usuarios_cita", "");
}

function cargar_usuarios_tipo_cita_admision(id_admision, tipo_cita, id_profesional, estado_consulta) {
    var params = "opcion=12&id_tipo_cita=" + tipo_cita +
				 "&id_usuario_prof=" + id_profesional +
				 "&id_admision=" + id_admision +
				 "&estado_consulta=" + estado_consulta;
	
    llamarAjax("admision_ajax.php", params, "d_usuarios_cita", "");
}

function mostrar_examenes(cantidad) {
    if (cantidad == "") {
        cantidad = 0;
    } else {
        cantidad = parseInt(cantidad, 10);
    }

    for (var i = 0; i < cantidad; i++) {
        $("#tr_examen_" + i).css("display", "");
    }
    for (var i = cantidad; i < 10; i++) {
        $("#tr_examen_" + i).css("display", "none");
    }

    if (cantidad == 0) {
        $("#tr_examen_vacio").css("display", "");
    } else {
        $("#tr_examen_vacio").css("display", "none");
    }
}

function imprimir_recibo_pago() {
    var params = "opcion=1&id_pago=&id_admision=" + $("#hdd_id_admision_paciente").val();

    llamarAjax("recibo_pago_ajax.php", params, "d_imprimir_recibo", "mostrar_recibo_pago();");
}

function mostrar_recibo_pago() {
    var cont_aux = 0;
    while ($("#hdd_ruta_arch_pdf_" + cont_aux).length) {
        var ruta = $("#hdd_ruta_arch_pdf_" + cont_aux).val();
        window.open("../funciones/abrir_pdf.php?ruta=" + ruta + "&nombre_arch=recibo_pago_" + (cont_aux + 1) + ".pdf", "_blank");

        cont_aux++;
    }
}

function cargar_componente_hc_adm(id_paciente) {
    var params = "opcion=14&id_paciente=" + id_paciente;

    $("#d_cargar_hc_adm").html("");
    llamarAjax("admision_ajax.php", params, "d_cargar_hc_adm", "");
}

function seleccionar_medio_pago(id_medio_pago) {
    if (id_medio_pago == $("#hdd_medio_pago_np").val()) {
        var ult_index_precio = parseInt($("#hdd_ult_index_precio").val(), 10);
        for (var i = 1; i <= ult_index_precio; i++) {
            $("#txt_valor_pago_" + i).val(0);
            $("#txt_valor_cuota_" + i).val(0);
        }
    }
}

function mostrar_convenio_pago(indice) {
    var id_convenio = $("#cmb_convenio").val();
    var id_plan = $("#cmb_plan").val();
	
    var params = "opcion=15&indice=" + indice +
				 "&id_convenio=" + id_convenio;
	
    llamarAjax("admision_ajax.php", params, "d_convenio_pago_" + indice, "seleccionar_convenio_pago(" + indice + ", \"" + id_convenio + "\", \"" + id_plan + "\");");
}

function seleccionar_convenio_pago(indice, id_convenio, id_plan) {
    var params = "opcion=16&indice=" + indice +
				 "&id_convenio=" + id_convenio +
				 "&id_plan=" + id_plan;

    llamarAjax("admision_ajax.php", params, "d_plan_pago_" + indice, "actualizar_precio_det(" + indice + ", \"" + id_plan + "\"); actualizar_ind_num_aut(" + indice + ");");
}

function actualizar_precio_det(indice, id_plan) {
    var params = "opcion=17&indice=" + indice +
				 "&id_plan=" + id_plan +
				 "&cod_servicio=" + $("#hdd_cod_servicio_" + indice).val() +
				 "&tipo_precio=" + $("#hdd_tipo_precio_" + indice).val() +
				 "&tipo_bilateral=" + $("#hdd_tipo_bilateral_" + indice).val();
	
    llamarAjax("admision_ajax.php", params, "d_plan_pago_sel_" + indice, "finalizar_actualizar_precio_det(" + indice + ")");
}

function actualizar_ind_num_aut(indice) {
    document.getElementById("hdd_ind_num_aut_" + indice).value = document.getElementById("hdd_ind_num_aut_aux_" + indice).value;
    document.getElementById("hdd_ind_num_aut_obl_" + indice).value = document.getElementById("hdd_ind_num_aut_obl_aux_" + indice).value;

    if (document.getElementById("hdd_ind_num_aut_" + indice).value == "1") {
        document.getElementById("txt_num_autorizacion_" + indice).disabled = false;
    } else {
        document.getElementById("txt_num_autorizacion_" + indice).value = "";
        document.getElementById("txt_num_autorizacion_" + indice).disabled = true;
    }
}

function finalizar_actualizar_precio_det(indice) {
	var id_precio = $("#hdd_id_precio_detalle_" + indice).val();
	var valor = $("#hdd_valor_detalle_" + indice).val();
	var valor_cuota = $("#hdd_valor_cuota_detalle_" + indice).val();
	var ind_tipo_pago = $("#hdd_tipo_pago_detalle_" + indice).val();
	
	$("#hdd_id_precio_" + indice).val(id_precio);
	$("#txt_valor_pago_" + indice).val(valor);
	$("#txt_valor_cuota_" + indice).val(valor_cuota);
	$("#hdd_tipo_pago_" + indice).val(ind_tipo_pago);
	
	/*if (ind_tipo_pago == "1") {
		$("#txt_valor_cuota_" + indice).removeAttr("disabled");
	} else {
		$("#txt_valor_cuota_" + indice).attr("disabled", "disabled");
	}*/
	
	recalcular_precios_cx_cop_cm();
}

function cancelar_registro_admision() {
    enviar_credencial("../admisiones/estado_atencion.php", 13);
}

function cargar_usuarios_profesionales(modificar_valores) {
    if ($("#d_usuario_prof").length) {
        var params = "opcion=19&id_usuario_prof_base=" + $("#cmd_profesionalAtiende").val() +
                	 "&modificar_valores=" + modificar_valores;

        llamarAjax("admision_ajax.php", params, "d_usuario_prof", "");
    }
}

function actualizar_citas_profesionales(modificar_valores) {
    cargar_usuarios_profesionales(modificar_valores);
    cargar_usuarios_tipo_cita();
    settipocita(modificar_valores, $("#cmb_tipo_cita").val());
}

function calcular_cuota_mod() {
	var num_precios = parseInt($("#hdd_ult_index_precio").val(), 10);
	var valor_cuota = 0;
	for (i = 1; i <= num_precios; i++) {
		if (isObject(document.getElementById("tr_precios_" + i))) {
			valor_cuota += parseInt($("#txt_valor_cuota_" + i).val());
		}
	}
	$("#sp_cuota_mod").html("$" + (isNaN(valor_cuota) ? 0 : valor_cuota));
}

function recalcular_precios_cx_cop_cm() {
	var id_paciente = $("#hdd_id_paciente").val();
	var id_plan = $("#cmb_plan").val();
	var tipo_coti_paciente = $("#cmb_tipo_coti_paciente").val();
	var rango_paciente = $("#cmb_rango_paciente").val();
	var params = "opcion=1&id_paciente=" + + id_paciente +
				 "&id_plan=" + id_plan +
				 "&tipo_coti_paciente=" + tipo_coti_paciente +
				 "&rango_paciente=" + rango_paciente;
	
	var cant_registros = parseInt($("#hdd_ult_index_precio").val(), 10);
	cont_aux = 0;
	for (var i = 1; i <= cant_registros; i++) {
		if (isObject(document.getElementById("tr_precios_" + i))) {
			params += "&orden_" + cont_aux + "=" + i +
					  "&tipo_precio_" + cont_aux + "=" + $("#hdd_tipo_precio_" + i).val() +
					  "&cod_producto_" + cont_aux + "=" + $("#hdd_cod_servicio_" + i).val() +
					  "&tipo_bilateral_" + cont_aux + "=" + $("#hdd_tipo_bilateral_" + i).val() +
					  "&id_plan_" + cont_aux + "=" + $("#cmb_plan_pago_" + i).val() +
					  "&valor_" + cont_aux + "=" + str_encode($("#txt_valor_pago_" + i).val()) +
					  "&valor_cuota_" + cont_aux + "=" + str_encode($("#txt_valor_cuota_" + i).val()) +
					  "&cantidad_" + cont_aux + "=" + str_encode($("#txt_cant_precio_" + i).val());
			
			cont_aux++;
		}
	}
	params += "&cant_registros=" + cont_aux;
	
	llamarAjax("../funciones/LiquidadorPrecios_ajax.php", params, "d_liq_cx_cop_cm", "continuar_recalcular_precios_cx_cop_cm();");
}

function continuar_recalcular_precios_cx_cop_cm() {
	//Cirugías
	var cantidad_aux = parseInt($("#hdd_cantidad_cal_cx").val(), 10);
	for (var i = 0; i < cantidad_aux; i++) {
		var orden = $("#hdd_orden_cal_cx_" + i).val();
		var valor = Math.round($("#hdd_valor_cal_cx_" + i).val());
		
		$("#txt_valor_pago_" + orden).val(valor);
	}
	
	//Copagos y cuotas moderadoras
	cantidad_aux = parseInt($("#hdd_cantidad_cop_cm").val(), 10);
	for (var i = 0; i < cantidad_aux; i++) {
		var orden = $("#hdd_orden_cop_cm_" + i).val();
		var valor = Math.round($("#hdd_valor_cop_cm_" + i).val());
		
		$("#txt_valor_cuota_" + orden).val(valor);
	}
	
	//Se recalculan los totales
	recalcular_totales_liquidacion();
}

function enviar_formulario_registrar_pago(id_admision, id_pago) {
    $("#frm_credencial").append('<input type="hidden" id="hdd_idAdmision" name="hdd_idAdmision" value="' + id_admision + '"  />');
    $("#frm_credencial").append('<input type="hidden" id="hdd_idPago" name="hdd_idPago" value="' + id_pago + '"  />');
    $("#frm_credencial").append('<input type="hidden" id="hdd_numero_menu" name="hdd_numero_menu" value="18"  />');
    enviar_credencial("../admisiones/pagos.php", $("#hdd_menu").val());
}

function actualizar_status_convenio_paciente() {
	$("#sp_status_convenio").html($("#hdd_nombre_status_convenio_paciente_b").val());
}

function recalcular_totales_liquidacion() {
	var cant_registros = parseInt($("#hdd_ult_index_precio").val(), 10);
	var total = 0;
	var total_cuota = 0;
	for (var i = 1; i <= cant_registros; i++) {
		if (isObject(document.getElementById("tr_precios_" + i))) {
			total += (parseInt($("#txt_valor_pago_" + i).val(), 10) * parseInt($("#txt_cant_precio_" + i).val(), 10));
			total_cuota += parseInt($("#txt_valor_cuota_" + i).val(), 10);
		}
	}
	
	$("#sp_valor_total").html("$" + total.formatoNumerico());
	$("#sp_cuota_mod").html("$" + total_cuota.formatoNumerico());
}
