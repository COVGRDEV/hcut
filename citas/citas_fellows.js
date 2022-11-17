/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//<![CDATA[ 
$(function() {
    $('.paginated', 'table').each(function(i) {
        $(this).text(i + 1);
    });

    $('table.paginated').each(function() {
        var currentPage = 0;
        var numPerPage = 10;
        var $table = $(this);
        $table.bind('repaginate', function() {
            $table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
        });
        $table.trigger('repaginate');
        var numRows = $table.find('tbody tr').length;
        var numPages = Math.ceil(numRows / numPerPage);
        var $pager = $('<div class="pager"></div>');
        for (var page = 0; page < numPages; page++) {
            $('<span class="page-number"></span>').text(page + 1).bind('click', {
                newPage: page
            }, function(event) {
                currentPage = event.data['newPage'];
                $table.trigger('repaginate');
                $(this).addClass('active').siblings().removeClass('active');
            }).appendTo($pager).addClass('clickable');
        }
        $pager.insertBefore($table).find('span.page-number:first').addClass('active');
    });
});
//]]>

var mes_seleccionado = "";
var anio_seleccionado = "";
function calendario(mes, anio) {
	mes_seleccionado = mes;
	anio_seleccionado = anio;
    var id_fellow_g = $('#cmb_lista_usuarios').val();
    var params = 'opcion=1&mes=' + mes + '&anio=' + anio + '&id_fellow_g=' + id_fellow_g;
	
    llamarAjax("citas_fellows_ajax.php", params, "d_fellows", "");
	document.getElementById("d_fellows").style.display = "block";
}

function calendario_base() {
	calendario(mes_seleccionado, anio_seleccionado);
}

function mover_citas_fellow(fecha, id_usuario) {
	if (id_usuario != -1) {
	    var params = 'opcion=2&fecha=' + fecha + '&id_usuario=' + id_usuario + '&id_fellow_g=' + $("#cmb_lista_usuarios").val();
		
		llamarAjax("citas_fellows_ajax.php", params, "d_interno", "mostrar_confirmacion_mover()");
	}
}

function mostrar_confirmacion_mover() {
	mostrar_formulario_flotante(1);
	posicionarDivFlotante('d_centro');
}

function cambiar_citas(fecha, id_usuario, id_fellow_g) {
    var params = 'opcion=3&fecha=' + fecha + '&id_usuario=' + id_usuario + '&id_fellow_g=' + id_fellow_g;
	
	llamarAjax("citas_fellows_ajax.php", params, "d_mover_citas", "confirmar_cambiar_citas();");
}

function confirmar_cambiar_citas() {
	var ind_resultado = -1;
	if (isObject(document.getElementById("hdd_resultado_mover"))) {
		ind_resultado = parseInt($('#hdd_resultado_mover').val(), 10);
	}
	//alert("ind_resultado=" + ind_resultado);
	
    if (ind_resultado > 0) {
        $("#contenedor_exito").css("display", "block");
        $('#contenedor_exito').html('Datos guardados correctamente');
        setTimeout("$('#contenedor_exito').slideUp(200).css('display', 'none')", 2000);
    } else {
        $("#contenedor_error").css("display", "block");
        $('#contenedor_error').html('Error al guardar usuarios');
        setTimeout("$('#contenedor_error').slideUp(200).css('display', 'none')", 2000);
    }
	setTimeout("mostrar_formulario_flotante(0)", 2000);
	calendario_base();
}

function cancelar_cambio_citas(dia) {
	$("#cmb_usuario_fellow_" + dia).val("-1");
	mostrar_formulario_flotante(0);
}
