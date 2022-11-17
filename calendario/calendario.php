<?php
session_start();
require_once '../principal/ContenidoHtml.php';
require_once '../db/DbUsuariosPerfiles.php';
require_once '../db/DbVariables.php';
require_once '../funciones/Class_Combo_Box.php';

$contenidoHtml = new ContenidoHtml();
$variables = new Dbvariables();
$usuarios_perfiles = new DbUsuariosPerfiles();

$combo = new Combo_Box();

//variables
$titulo = $variables->getVariable(1);
$id_perfil_fellow_g = $variables->getVariable(4);
$id_perfil_fellow = $variables->getVariable(5);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $titulo['valor_variable']; ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
        <link href="../css/azul.css" rel="stylesheet" type="text/css" />
        <script type='text/javascript' src='../js/jquery.js'></script>
        <script type='text/javascript' src='../js/funciones.js'></script>
        <script type='text/javascript' src='../js/ajax.js'></script>
        <script type='text/javascript' src='../js/jquery.validate.js'></script>
        <script type='text/javascript' src='calendario.js'></script>
    </head>
    <body onload="calendario('', '');">
        <?php
	        $contenidoHtml->validar_seguridad(0);
    	    $contenidoHtml->cabecera_html();
			$tipo_acceso_menu = $contenidoHtml->obtener_permisos_menu($_POST["hdd_numero_menu"]);
        ?>
        <div class="title-bar">
            <div class="wrapper">
                <div class="breadcrumb">
                    <ul>
                        <li class="breadcrumb_on">Calendario - D&iacute;as Laborales</li>
                    </ul>
                </div>
            </div>
        </div>               
        
        <div class="contenido wrapper clearfix" id="d_calendario"></div>
        <?php
        	$contenidoHtml->footer();
        ?>
    </body>
</html>
