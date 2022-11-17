<?php session_start();
require_once("../db/DbVariables.php");
require_once("ContenidoHtml.php");
$contenidoHtml = new ContenidoHtml();
$variables = new Dbvariables();
//variables
$titulo = $variables->getVariable(1);
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
        <script type='text/javascript' src='../js/funciones.js'></script>
        <script type='text/javascript' src='../js/ajax.js'></script>
    </head>
    <body>
<?php
    $contenidoHtml->validar_seguridad(0);
    $contenidoHtml->cabecera_html();
	/*
	 * pagina
	 */
    $contenidoHtml->footer();

?>
</body>
</html>
