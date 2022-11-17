<?php
	function obtener_listado_contactos() {
		//Div flotante de contactos médicos relacionados
?>
<div id="d_contactos_antecedentes" class="div_panel_der" style="display:none;">
	<div class="a_cierre_panel" onclick="abrir_cerrar_contactos_antecedentes_med();"></div>
    <div id="d_height">
        <div id="d_header">
            <div><b>Contactos</b></div>
        </div>
        <div id="d_interno_contactos_antecedentes">(Sin contactos)</div>
    </div>
</div>
<script type="text/javascript" id="ajax">
	ver_contactos_antecedentes_med();
</script>
<?php
	}
?>
