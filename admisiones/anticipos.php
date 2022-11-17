<?php
	session_start();
	
	require_once("../principal/ContenidoHtml.php");
	require_once("../db/DbVariables.php");
	
	$contenidoHtml = new ContenidoHtml();
	$variables = new Dbvariables();
	
	//variables
	$titulo = $variables->getVariable(1);
	
	$tipo_acceso_menu = $contenidoHtml->obtener_permisos_menu($_POST["hdd_numero_menu"]);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $titulo["valor_variable"]; ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
        <link href="../css/azul.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="../js/jquery.js"></script>
        <script type="text/javascript" src="../js/funciones.js"></script>
        <script type="text/javascript" src="../js/ajax.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="anticipos_v1.0.js"></script>
        <script type="text/javascript" src="../administracion/terceros_v1.3.js"></script>
        <script type="text/javascript" src="../js/validaFecha.js"></script>
    </head>
    <body>
        <?php
			$contenidoHtml->validar_seguridad(0);
			$contenidoHtml->cabecera_html();
		?>
        <div class="title-bar">
            <div class="wrapper">
            	<div class="breadcrumb">
                    <ul>
                        <li class="breadcrumb_on">Registrar Anticipos</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="contenedor_principal volumen">
            <div class="padding">
                <table>
                    <tr>
                        <td id="advertenciasg">
                            <div class="contenedor_exito" id="contenedor_exito"></div>
                            <div class="contenedor_error" id="contenedor_error"></div>
                        </td>
                    </tr>
                </table>
                <div>
                    <form id="frm_buscar_anticipos" name="frm_buscar_anticipos">
                        <table id="buscar">
                            <tr>
                                <td>
                                    <input type="text" id="txt_paciente" name="txt_paciente" placeholder="Buscar por nombre, n&uacute;mero de documento o n&uacute;mero de recibo" style="width:500px;"/>
                                </td>
                                <td>
                                    <input class="btnPrincipal peq" type="submit" value="Consultar" id="btn_consultar" name="btn_consultar" onclick="validar_buscar_anticipos();" />
                                </td>
                                <td>
                                	<?php
                                    	if ($tipo_acceso_menu == "2") {
									?>
                                    <input class="btnPrincipal peq" type="button" value="Crear nuevo anticipo" id="btn_nuevo_anticipo" name="btn_nuevo_anticipo" onclick="mostrar_crear_anticipo();" />
                                    <?php
										}
									?>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <div id="d_contenedor_ppal" style="min-height: 30px;"></div>
                    <div id="d_resultado" style="display:none;"></div>
                    <div id="d_imprimir_recibo" style="display:none;"></div>
                </div>
            </div>
        </div>
        <?php
        	$contenidoHtml->footer();
        ?>
    </body>
</html>
