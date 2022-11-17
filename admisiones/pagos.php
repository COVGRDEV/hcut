<?php
	session_start();
	
	require_once("../principal/ContenidoHtml.php");
	require_once("../db/DbVariables.php");
	
	$contenidoHtml = new ContenidoHtml();
	$variables = new Dbvariables();
	
	//variables
	$titulo = $variables->getVariable(1);
	
	//variable que recibe por post
	$post = isset($_POST["hdd_idAdmision"]) ? $_POST["hdd_idAdmision"] : 0;
	$postPago = isset($_POST["hdd_idPago"]) ? $_POST["hdd_idPago"] : 0;
	$ind_pago_auto = isset($_POST["hdd_ind_pago_auto"]) ? $_POST["hdd_ind_pago_auto"] : 0;
	
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
        <script type="text/javascript" src="../js/sweetalert2.all.min.js"></script>  
		<script type="text/javascript" src="pagos_v1.8.js"></script>
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
                        <li class="breadcrumb_on">Registrar Pago</li>
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
                    <form id="frmBuscarPagos" name="frmBuscarPagos">
                        <table id="buscar">
                            <tr>
                                <td>
                                    <input type="text" id="txt_paciente" name="txt_paciente" placeholder="Buscar por nombre, n&uacute;mero de documento o n&uacute;mero de recibo" style="width:500px;"/>
                                </td>
                                <td>
                                    <input class="btnPrincipal peq" type="submit" value="Consultar" id="btn_consultar" name="btn_consultar" onclick="validarBuscarPagos();" />
                                </td>
                                <td>
                                	<?php
                                    	if ($tipo_acceso_menu == "2") {
									?>
                                    <input class="btnPrincipal peq" type="button" value="Crear nuevo pago" id="btn_crear_pago" name="btn_crear_pago" onclick="mostrar_crear_pago();" />
                                    <?php
										}
									?>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <div id="contenedor" style="min-height: 30px;"></div>
                    <div id="d_resultado" style="display:none;"></div>
                    <div id="d_imprimir_recibo" style="display:none;"></div>
                    <input type="hidden" id="post" name="post" value="<?php echo($post); ?>" />
                    <input type="hidden" id="post_pago" name="post_pago" value="<?php echo($postPago); ?>" />
					<input type="hidden" id="hdd_ind_pago_auto" name="hdd_ind_pago_auto" value="<?php echo($ind_pago_auto); ?>" />
                    <input type="hidden" id="hdd_ind_banco" name="hdd_ind_banco"></input>
                    <div id="d_ind_banco_tercero" style="display:none;"></div>
                </div>
            </div>
        </div>
        <?php
        	$contenidoHtml->footer();
        ?>
    </body>
</html>
