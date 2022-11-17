function imprimir_anexo_hc() {
	var params = "id_hc=" + $("#hdd_id_hc").val();
	
	llamarAjax("../historia_clinica/impresion_historia_clinica.php", params, "d_impresion_hc", "imprSelec(\"d_impresion_hc\")");
}
