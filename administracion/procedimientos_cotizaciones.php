<?php
	session_start();
	/*
	  Pagina listado de usuarios, muestra los usuarios existentes, para modificar o crear uno nuevo
	  Autor: Helio Ruber LÃ³pez - 16/09/2013
	 */
	
	require_once("../db/DbVariables.php");
	require_once("../db/DbListas.php");
	require_once("../funciones/Utilidades.php");
	require_once("../principal/ContenidoHtml.php");
	
	$dbVariables = new Dbvariables();
	$utilidades = new Utilidades();
	$contenido = new ContenidoHtml();
	$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);
	
	//variables
	$titulo = $dbVariables->getVariable(1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $titulo['valor_variable']; ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
        <link href="../css/azul.css" rel="stylesheet" type="text/css" />
        <script type='text/javascript' src='../js/jquery.js'></script>
        <script type='text/javascript' src='../js/jquery.validate.js'></script>
        <script type='text/javascript' src='../js/jquery.validate.add.js'></script>
        <script type='text/javascript' src='../js/ajax.js'></script>
        <script type='text/javascript' src='../js/funciones.js'></script>
        <script type='text/javascript' src='procedimientos_cotizaciones.js'></script>
    </head>
    <body onload="ver_todos_procedimientos();">
        <?php
			$contenido->validar_seguridad(0);
			$contenido->cabecera_html();
        ?>
        <div class="title-bar">
            <div class="wrapper">
                <div class="breadcrumb">
                <ul>
                    <li class="breadcrumb_on">Administraci&oacute;n de procedimientos para cotizaciones</li>
                </ul>
            </div>
            </div>
        </div>
        <div class="contenedor_principal volumen">
            <div class="padding">
                <form id="frm_listado_procedimientos" name="frm_listado_procedimientos">
                    <table border="0" cellpadding="0" cellspacing="0" align="center" style="width:73%;">
                        <tr valign="middle">
                            <td align="center" colspan="3">
                                <div class='contenedor_error' id='contenedor_error'></div>
                                <div class='contenedor_exito' id='contenedor_exito'></div>
                            </td>
                        </tr>
                        <tr valign="middle">
                            <td style="width:15%;">
                                <label class="left inline">Procedimiento</label>
                            </td>
                            <td align="right" style="width:42%;">
                                <input type='text' id="txt_buscar_procedimiento" name="txt_buscar_procedimiento"  class="input required" onblur="trim_cadena(this);"  style="width:100%;" />
                            </td>
                            <td align="right" style="width:43%;">
                                <input type="submit" id="btn_buscar_procedimiento" nombre="btn_buscar_procedimiento" value="Buscar" class="btnSecundario peq" onclick="buscar_procedimientos();"/>
                                    <input type="button" id="btn_ver_todos" nombre="btn_ver_todos" value="Ver todos" class="btnSecundario peq" onclick="ver_todos_procedimientos();" />
                                    <?php
                                        if ($tipo_acceso_menu == 2) {
                                    ?>
                                    <input type="button" id="btn_crear_procedimiento" nombre="btn_crear_procedimiento" value="Crear nuevo procedimiento" class="btnPrincipal peq" onclick="llamar_crear_procedimiento();"/>
                                    <?php
                                        }
                                    ?>
                            </td>
                        </tr>	
                    </table>
                    <br />
                </form>
            </div>
            <div class="padding">
                <div id="d_principal_procedimientos"></div>
            </div>
        </div>
        <div id="d_guardar_procedimientos" style="display:none;"></div>
        <?php
        $contenido->footer();
        ?>  
    </body>
</html>
