<?php
	session_start();
	/*
	  Pagina para la administración de terceros
	  Autor: Feisar Moreno - 28/11/2016
	 */
	
	require_once("../db/DbVariables.php");
	require_once("../funciones/Utilidades.php");
	require_once("../principal/ContenidoHtml.php");
	
	$dbVariables = new DbVariables();
	$utilidades = new Utilidades();
	$contenido_html = new ContenidoHtml();
	
	//variables
	$titulo = $dbVariables->getVariable(1);
	
	$tipo_acceso_menu = $contenido_html->obtener_permisos_menu($_POST["hdd_numero_menu"]);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $titulo["valor_variable"]; ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
        <link href="../css/azul.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="../js/jquery.js"></script>
        <script type="text/javascript" src="../js/jquery-ui.custom.js"></script>
        <script type="text/javascript" src="../js/jquery.cookie.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.add.js"></script>
        <script type="text/javascript" src="../js/ajax.js"></script>
        <script type="text/javascript" src="../js/funciones.js"></script>
        <script type="text/javascript" src="terceros_v1.3.js"></script>
        <link href="../src/skin-vista/ui.dynatree.css" rel="stylesheet" type="text/css" >
        <script src="../src/jquery.dynatree.js" type="text/javascript"></script>
    </head>
    <body>
        <?php
			$contenido_html->validar_seguridad(0);
			$contenido_html->cabecera_html();
        ?>
        <div class="title-bar">
            <div class="wrapper">
                <div class="breadcrumb">
                    <ul>
                        <li class="breadcrumb_on">Administraci&oacute;n de terceros</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="contenedor_principal volumen">
            <div class="padding">
                <form id="frm_buscar_terceros" name="frm_buscar_terceros"> 
                    <table style="width: 100%;">
                        <tr valign="middle">
                            <td align="center" colspan="3">
                                <div id="advertenciasg">
                                    <div class="contenedor_error" id="d_contenedor_error"></div>
                                    <div class="contenedor_exito" id="d_contenedor_exito"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:82%;">
                                <input type="text" id="txt_parametro" name="txt_parametro" placeholder="N&uacute;mero de documento o nombre del tercero" onblur="trim_cadena(this);" />
                            </td>
                            <td style="width:8%;">
                                <input type="submit" id="btn_buscar" nombre="btn_buscar" value="Buscar" class="btnPrincipal peq" onclick="buscar_terceros();"/>

                            </td>
                            <td style="width:10%;">
                            	<?php
                                	if ($tipo_acceso_menu == "2") {
								?>
                                <input type="button" id="btn_nuevo_tercero" nombre="btn_nuevo_tercero" value="Nuevo tercero" class="btnPrincipal peq" onclick="abrir_nuevo_tercero();"/>
                                <?php
									}
								?>
                            </td>
                        </tr>
                    </table>
                </form>
                <div id="d_principal_terceros"></div>
                <input type="hidden" id="hdd_resultado" name="hdd_resultado" />
            </div>
        </div>
        <?php
			$contenido_html->footer();
		?>  
    </body>
</html>