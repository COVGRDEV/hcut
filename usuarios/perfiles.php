<?php
session_start();
/*
  Pagina listado de perfiles, muestra los perfiles existentes, para modificar o crear uno nuevo
  Autor: Helio Ruber LÃ³pez - 16/09/2013
 */

require_once("../db/DbVariables.php");
require_once("../db/DbPerfiles.php");
require_once("../db/DbListas.php");
require_once("../funciones/Utilidades.php");
require_once("../principal/ContenidoHtml.php");
$variables = new Dbvariables();
$perfiles = new DbPerfiles();
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
        <script type="text/javascript" src="../js/jquery-ui.custom.js"></script>
		<script type="text/javascript" src="../js/jquery.cookie.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.add.js"></script>
        <script type="text/javascript" src="../js/ajax.js"></script>
        <script type="text/javascript" src="../js/funciones.js"></script>
        <script type="text/javascript" src="perfiles_v1.4.js"></script>
        <link href="../src/skin-vista/ui.dynatree.css" rel="stylesheet" type="text/css" >
		<script src="../src/jquery.dynatree.js" type="text/javascript"></script>
    </head>
    <body>
        <?php
			$contenido->validar_seguridad(0);
			$contenido->cabecera_html();
		?>
		<script type="text/javascript">cargar_perfiles();</script>
        <div class="title-bar">
            <div class="wrapper">
                <div class="breadcrumb">
                <ul>
                    <li class="breadcrumb_on">Administraci&oacute;n de perfiles</li>
                </ul>
            </div>
            </div>
        </div>
        <div class="contenedor_principal volumen">
            <div class="padding">
            <table  border="0" style="width: 100%; margin-bottom: 5px;">
        	<tr><td colspan="2" style="text-align: center;">
            	<?php
                	if ($tipo_acceso_menu == 2) {
				?>
        		<input type="button" id="btn_crear_perfil" nombre="btn_crear_perfil" value="Crear nuevo Perfil" class="btnPrincipal peq" onclick="cargar_formulario_crear();"/>
                <?php
					}
				?>
        		<input type="button" id="btn_lista_perfil" nombre="btn_lista_perfil" value="Ver lista perfiles" class="btnSecundario peq" onclick="cargar_perfiles();"/>
        	</td></tr>
        	<tr><td colspan="2" style="text-align: left;">
        		<div class="contenedor_error" id="contenedor_error"></div>
                <div class="contenedor_exito" id="contenedor_exito"></div>
            </td></tr>
        	</table>
        	</div>
            <div id="principal_perfiles"></div>
        </div>
        <?php
        $contenido->footer();
        ?>  

    </body>
</html>
