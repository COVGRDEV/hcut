function validarBuscarPersonasHc() {
    $("#frmRegistroCx").validate({
        rules: {
            txt_paciente_hc: {
                required: true,
            },
        },
        submitHandler: function() {

            $("#contenedor_error").css("display", "none");
            var txt_paciente_hc = $('#txt_paciente_hc').val();
            var params = "opcion=1&txt_paciente_hc=" + txt_paciente_hc;

            llamarAjax("registro_cx_ajax.php", params, "contenedor_paciente_hc", "");

            return false;
        },
    });
}

function validar_buscar_hc() {
    var result = 0;
    $('#txt_paciente_hc').removeClass("borde_error");
    if ($('#txt_paciente_hc').val() == '') {
        $('#txt_paciente_hc').addClass("borde_error");
        result = 1;
    }
    return result;
}

function mostrar_formulario_cx(id_admision, id_hc, pagina_menu, credencial, id_menu, ind_complemento) {
    mostrar_formulario_flotante(1);
    $("#d_interno").empty();
    posicionarDivFlotante('d_centro');
    reducir_formulario_flotante('1050', '700');
    $('#d_centro').height(700);
    $('#d_interno').height(700 - 35);
	
    var HcFrame = document.createElement("iframe");
    HcFrame.id = "HcFrame";
    ruta = pagina_menu + "?hdd_id_admision=" + id_admision +
            "&hdd_id_hc=" + id_hc +
            "&credencial=" + credencial +
            "&hdd_numero_menu=" + id_menu +
            "&tipo_entrada=2" +
            "&ind_complemento=" + ind_complemento;
	
    HcFrame.setAttribute("src", ruta);
    HcFrame.style.height = '100%';
    HcFrame.style.width = '1050px';
    var control = document.getElementById("HcFrame")
    $("#d_interno").append(HcFrame);
}

function ver_registros_hc(id_persona, nombre_persona, documento_persona, tipo_documento, telefonos, fecha_nacimiento, edad_paciente) {
    var params = "opcion=2&id_persona=" + id_persona +
            "&nombre_persona=" + nombre_persona +
            "&documento_persona=" + documento_persona +
            "&tipo_documento=" + tipo_documento +
            "&telefonos=" + telefonos +
            "&fecha_nacimiento=" + fecha_nacimiento +
            "&edad_paciente=" + edad_paciente;
	
    llamarAjax("registro_cx_ajax.php", params, "contenedor_paciente_hc", "");
}

function imprimir_hoja_stickers(id_admision, id_hc) {
    var params = "opcion=1&id_admision=" + id_admision +
	             "&id_hc=" + id_hc;
	
    llamarAjax("hoja_stickers_ajax.php", params, "d_impresion_stickers", "imprSelec(\"d_campo_imprimir\");");
}

function mostrar_cargar_stickers(id_hc) {
	$('#fondo_negro').css('display', 'block');
	$('#d_centro_stickers').slideDown(400).css('display', 'block');
	$('#d_centro_stickers').css('width', 600);
	$('#d_centro_stickers').css('height', '250px');
    $('#d_centro_stickers').css('top', '20%');
    $('#d_interno_stickers').css('width', 585);
	posicionarDivFlotante("d_centro_stickers");
    $('#d_interno_stickers').css('height', 'auto');
	
	$("#hdd_id_hc_stickers").val(id_hc);
}

function cargar_hoja_stickers() {
	var ruta_arch_cx = $("#fil_hoja_stickers").val();
	if (ruta_arch_cx != "") {
		var extension = obtener_extension_archivo(ruta_arch_cx);
		if (extension != "jpg" && extension != "png" && extension != "bmp" && extension != "gif" && extension != "pdf") {
			alert("El archivo a cargar debe ser una imagen o un archivo pdf");
			return;
		}
		
		$("#frm_arch_stickers").submit();
		cerrar_div_centro_stickers();
		alert("Archivo cargado con \xe9xito");
	} else {
		alert("Debe seleccionar un archivo");
		return;
	}
}

function obtener_extension_archivo(nombre_archivo) {
	var extension = nombre_archivo.substring(nombre_archivo.lastIndexOf(".") + 1).toLowerCase();
	
	return extension;
}

function cerrar_div_centro_stickers() {
	$('#fondo_negro').css('display', 'none');
	$('#d_centro_stickers').slideDown(400).css('display', 'none');
}
