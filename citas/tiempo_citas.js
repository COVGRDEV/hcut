
//Funcion evento clic en el boton Nuevo
function btnNuevo() {
    //Oculta el datagrid
    display_d_datagrid(0);
    display_formulario(1);
    display_update_d_datagrid(0);
}

//Guarda el registro del formulario: editar registro
function update_datagrid_form_editar() {

    $('#frmUpdatedatagrid').validate({
        debug: false,
        rules: {
            cmbTiempo: {
                required: true,
            },
        },
        submitHandler: function(form) {
            //Procedimiento de actualizar registro
            update_datagrid_form_editar_ajax();

            return false;
        }
    });
}

//Selecciona la informacion del tr del datagrid
function update_datagrid(e) {
    var idtiponoticia = $("#" + e + " td p:eq(0)").text();
    var idusuario = $("#" + e + " td p:eq(1)").text();
    var params = 'opcion=3&idtiponoticia=' + idtiponoticia + "&idusuario=" + idusuario;
    llamarAjax("tiempo_citas_ajax.php", params, "update_d_datagrid", "display_d_datagrid(0);display_update_d_datagrid(1)");

}

//Funcion de mostrar el div update_d_datagrid
function display_update_d_datagrid(parametro) {
    if (parametro == 0) {
        //despliega el div
        $("#update_d_datagrid").slideUp(200).css("display", "none");
    }
    else if (parametro == 1) {
        //despliega el div
        $("#update_d_datagrid").slideDown(200).css("display", "block");
    }
}


//Funcion de mostrar el div d_datagrid
function display_d_datagrid(parametro) {
    if (parametro == 0) {
        //despliega el div
        $("#d_datagrid").slideUp(200).css("display", "none");
    }
    else if (parametro == 1) {
        //despliega el div
        $("#d_datagrid").slideDown(200).css("display", "block");
    }
}

//Funcion de mostrar el div formulario
function display_formulario(parametro) {
    if (parametro == 0) {
        //despliega el div
        $("#formulario").slideUp(200).css("display", "none");
    }
    else if (parametro == 1) {
        //despliega el div
        $("#formulario").slideDown(200).css("display", "block");
        
        //Limpia el formulario
        $("#cmbTiempo").val('');
        $("#cmb_usuarios2").val('');
        $("#cmb_tiposcitas2").val('');
        
    }
}

//Funcion de mostrar el div contenedor_exito
function display_contenedor_exito(parametro) {
    if (parametro == 0) {
        setTimeout("$('#contenedor_exito').slideUp(200).css('display', 'none')", 4000);
    }
    else if (parametro == 1) {
        $("#contenedor_exito").slideDown(200).css("display", "block");
        $("#contenedor_exito").html('El registro se ha guardado de forma exitosa');
    }

}


//Funcion de mostrar el div contenedor_exito
function display_contenedor_error(parametro, mensaje) {
    if (parametro == 0) {
        setTimeout("$('#contenedor_error').slideUp(200).css('display', 'none')", 4000);
    }
    else if (parametro == 1) {
        $("#contenedor_error").slideDown(200).css("display", "block");
        $("#contenedor_error").html(mensaje);
    }

}

function usuarios_perfil() {
    var perfil = $('#cmb_perfil').val();
    var usuario = $('#cmb_usuarios').val();
    var tipocitas = $('#cmb_tiposcitas').val();
    var params = 'opcion=1&perfil=' + perfil + "&usuario=" + usuario + "&tipocitas=" + tipocitas;
    llamarAjax("tiempo_citas_ajax.php", params, "d_cmb_usuarios", "", "");
}

//despliega la tabla con el resultado
function datagrid() {
    var perfil = $('#cmb_perfil').val();
    var usuario = $('#cmb_usuarios').val();
    var tipocitas = $('#cmb_tiposcitas').val();
    var params = 'opcion=2&perfil=' + perfil + "&usuario=" + usuario + "&tipocitas=" + tipocitas;
    llamarAjax("tiempo_citas_ajax.php", params, "d_datagrid", "display_formulario(0);display_d_datagrid(1); display_update_d_datagrid(0)");
}

//Funcion actualiza el registro seleccionado del datagrid
function update_datagrid_form_editar_ajax() {
    var idtipocita = $("#txtIdtipocita").val();
    var txtIdusuario = $('#txtIdusuario').val();
    var cmbTiempo = $('#cmbTiempo').val();
    var indactivo = $('#indactivo').is(':checked') ? 1 : 0;

    var params = 'opcion=4&idtipocita=' + idtipocita + "&txtIdusuario=" + txtIdusuario + "&cmbTiempo=" + cmbTiempo + "&indactivo=" + indactivo;
    llamarAjax("tiempo_citas_ajax.php", params, "contenedor_exito", "display_contenedor_exito(1);display_contenedor_exito(0);display_update_d_datagrid(0);datagrid()");
}



function nuevo_tiempo_citas() {
    $('#frmNuevotiempocitas').validate({
        debug: false,
        rules: {
            cmb_usuarios2: {
                required: true,
            },
            cmb_tiposcitas2: {
                required: true,
            },
            cmbTiempo: {
                required: true,
            },
        },
        submitHandler: function(form) {

            var txtIdusuario = $("#cmb_usuarios2").val();
            var idtipocita = $('#cmb_tiposcitas2').val();
            var cmbTiempo = $('#cmbTiempo').val();
            var params = 'opcion=5&txtIdusuario=' + txtIdusuario + "&idtipocita=" + idtipocita + "&cmbTiempo=" + cmbTiempo;
            llamarAjax("tiempo_citas_ajax.php", params, "d_guardar", "despues_nuevo_tiempo_citas()");
			
            return false;
        }

    });
}


function despues_nuevo_tiempo_citas() {
    var rta = $('#hdd_rta_save').val();
    switch (rta) {
        case "1":
            display_contenedor_exito(1);
            display_contenedor_exito(0);
            display_formulario(0);
            break;
        case "-1":
            display_contenedor_error(1, 'Error interno!');
            break;
        case "-2":
            display_contenedor_error(1, 'El registro ya existe!');
            break;
    }
}









