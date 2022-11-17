<?php
session_start();
/*
  Pagina listado de usuarios, muestra los usuarios existentes, para modificar o crear uno nuevo
  Autor: Helio Ruber LÃ³pez - 16/09/2013
 */

require_once("../db/DbVariables.php");
require_once("../db/DbUsuarios.php");
require_once("../db/DbListas.php");
require_once("../funciones/Utilidades.php");
require_once("../principal/ContenidoHtml.php");
$variables = new Dbvariables();
$usuarios = new DbUsuarios();
$utilidades = new Utilidades();
$contenido = new ContenidoHtml();
$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);
//variables
$titulo = $variables->getVariable(1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $titulo["valor_variable"]; ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
        <link href="../css/azul.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="../js/jquery.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.add.js"></script>
        <script type="text/javascript" src="../js/ajax.js"></script>
        <script type="text/javascript" src="../js/funciones.js"></script>
        <script type="text/javascript" src="usuarios_v1.6.js"></script>
    </head>
    <body onload="ver_todos_usuarios();">
        <?php
			$contenido->validar_seguridad(0);
			$contenido->cabecera_html();
        ?>
        
        <div class="title-bar">
            <div class="wrapper">
                <div class="breadcrumb">
                <ul>
                    <li class="breadcrumb_on">Administraci&oacute;n de usuarios</li>
                </ul>
            </div>
            </div>
        </div>
        
        <div class="contenedor_principal volumen">
            
            <div class="padding">
            <form id="listado_usuarios" name="listado_usuarios">
                <table border="0" cellpadding="0" cellspacing="0" align="center" style="width:73%;">
                    <tr valign="middle">
                        <td align="center" colspan="2">
                            <div class="contenedor_error" id="contenedor_error"></div>
                            <div class="contenedor_exito" id="contenedor_exito"></div>
                        </td>
                    </tr>
                    <tr valign="middle">
                        <td style="width:25%;">
                            <label class="left inline">Nombre de usuario</label>
                        </td>
                        <td align="right" style="width:25%;">
                            <input type="text" class="input required" id="txt_busca_usuario" name="txt_busca_usuario"  onblur="trim_cadena(this);"  style="width:300px;" />
                        </td>
                        <td align="right" style="width:75%;">
                            <input type="submit" id="btn_buscar_usuario" nombre="btn_buscar_usuario" value="Buscar" class="btnPrincipal peq" onclick="buscar_usuarios();"/>
                                <input type="button" id="btn_ver_todos" nombre="btn_ver_todos" value="Ver todos" class="btnPrincipal peq" onclick="ver_todos_usuarios();" />
                                <?php
                                	if ($tipo_acceso_menu == 2) {
								?>
                                <input type="button" id="btn_crear_usuario" nombre="btn_crear_usuario" value="Crear nuevo usuario" class="btnPrincipal peq" onclick="llamar_crear_usuarios();"/>
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
            <div id="principal_usuarios" ></div>
            </div>
        </div>
        <?php
        $contenido->footer();
        ?>  
    </body>
</html>
