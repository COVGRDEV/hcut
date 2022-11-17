<?php
	session_start();
	
	require_once("../db/DbVariables.php");
	require_once("../funciones/Utilidades.php");
	require_once("../principal/ContenidoHtml.php");
	$variables = new DbVariables();
	$utilidades = new Utilidades();
	$contenido = new ContenidoHtml();
	
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
    <link href="../src/skin-vista/ui.dynatree.css" rel="stylesheet" type="text/css" >
    <script type='text/javascript' src='../js/jquery.js'></script>
    <script type="text/javascript" src="../js/jquery-ui.custom.js"></script>
    <script type="text/javascript" src="../js/jquery.cookie.js"></script>
    <script type='text/javascript' src='../js/jquery.validate.js'></script>
    <script type='text/javascript' src='../js/jquery.validate.add.js'></script>
    <script type='text/javascript' src='../js/ajax.js'></script>
    <script type='text/javascript' src='../js/funciones.js'></script>
    <script type='text/javascript' src='datos_entidad_v1.2.js'></script>
    <script src="../src/jquery.dynatree.js" type="text/javascript"></script>
</head>
<body>
	<?php
		$contenido->validar_seguridad(0);
		$contenido->cabecera_html();
	?>
    <div class="title-bar">
    	<div class="wrapper">
        	<div class="breadcrumb">
            	<ul><li class="breadcrumb_on">Datos de la entidad</li></ul>
            </div>
        </div>
    </div>
    <div class="contenedor_principal volumen">
    	<div class="padding">
			<div class="contenedor_error" id="contenedor_error"></div>
        	<div class="contenedor_exito" id="contenedor_exito"></div>
            <input type="submit" class="btnPrincipal" id="btnCrearDE" name="btnCrearDE" value="Crear entidad" onclick="iniciar_crear_datos_entidad();" />
            <div id="d_principal"></div>
        </div>
    </div>
    <?php
		$contenido->footer();
	?>
</body>
</html>
