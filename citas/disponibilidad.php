<?php
session_start();
require_once("../db/DbVariables.php");
require_once("../principal/ContenidoHtml.php");
require_once("../db/DbTiemposCitasProf.php");
require_once("../funciones/Class_Combo_Box.php");

$contenidoHtml = new ContenidoHtml();
$variables = new Dbvariables();
$tiemposcitasprof = new DbTiemposCitasProf();
$combo = new Combo_Box();

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
        <script type="text/javascript" src="../js/jquery.min.js"></script>
        <script type="text/javascript" src="../js/funciones.js"></script>
        <script type="text/javascript" src="../js/ajax.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="disponibilidad_v1.2.js"></script>
    </head>
    <body onload="regresar_disponibilidad();">
        <?php
        $contenidoHtml->validar_seguridad(0);
        $contenidoHtml->cabecera_html();
        ?>
        <div class="title-bar">
            <div class="wrapper">
                <div class="breadcrumb">
                    <ul>
                        <li class="breadcrumb_on">Disponibilidad de Especialistas</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="contenedor_principal volumen">
	        <div class="contenedor_error" id="contenedor_error" style="height: 37px;"></div>
    	    <div class="contenedor_exito" id="contenedor_exito" style="height: 37px;"></div>
            <div class="padding">
                <div id="d_disponibilidad">
                </div>
            </div>
        </div>
        <div id="d_guardar_disponibilidad" style="display:none;"></div>
        <?php
			$contenidoHtml->footer();
		?>
    </body>
</html>
