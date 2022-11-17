<?php
	$opcion = $_POST["opcion"];
	
	switch ($opcion) {
		case "1"://imprime el buscar usuarios
	?>
    <div class="encabezado">
        <h3>Buscar Paciente</h3>
    </div>
    <div class='contenedor_error' id='contenedor_error'></div>
    <form id="frmInternoBuscarusuario">
        <table border="0">
            <tr>
                <td style="width:500px;">
                    <input type="text" id="txt_identificacion_interno" name="txt_identificacion_interno" onkeypress="return leer_codigo_cedula(event, 1);" placeholder="Nombre o n&uacute;mero de documento" />
                </td>
                <td>
                    <input type="submit" value="Buscar" class="btnPrincipal" onclick="buscar_paciente();" />
                </td>
            </tr>
        </table>
    </form>
    <div id="d_datagrid_resultado"></div>
    <?php
			break;
	}
?>
