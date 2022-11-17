// JavaScript Document

function cambiar_espera_dilatacion(id_admision) {
	var params = "opcion=1&id_admision=" + id_admision +
				 "&ind_dilatado=" + ($("#chk_dilatado").is(":checked") ? 1 : 0);
	
	llamarAjax("../funciones/Class_Espera_Dilatacion_ajax.php", params, "d_espera_dilatacion", "");
}
