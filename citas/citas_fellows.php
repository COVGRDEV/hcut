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
        <script type='text/javascript' src='citas_fellows.js'></script>
    </head>
    <body>
        <?php
        $contenidoHtml->validar_seguridad(0);
        $contenidoHtml->cabecera_html();
        ?>
        
        <div class="title-bar">
            <div class="wrapper">
            	<h3>Citas para Fellows</h3>
                <table border="0" cellpadding="0" cellspacing="0" class="right filtros">
                	<tr>
                    	<td>
				        	<select name="cmb_lista_usuarios" id="cmb_lista_usuarios" onchange="calendario('', '');">
								<option value="" selected>--Seleccione--</option>
        		                <?php
									//Se obtiene el listado de los usuarios fellow genéricos
									$lista_usuarios = $usuarios_perfiles->getUsuarios($id_perfil_fellow_g["valor_variable"]);
                                	foreach ($lista_usuarios as $usuario_aux) {
								?>
                                <option value="<?php echo($usuario_aux["id_usuario"]); ?>"><?php echo($usuario_aux["nombre_completo"]); ?></option>
                                <?php
									}
								?>
        		                <?php
									//Se obtiene el listado de los usuarios fellow
									$lista_usuarios = $usuarios_perfiles->getUsuarios($id_perfil_fellow["valor_variable"]);
                                	foreach ($lista_usuarios as $usuario_aux) {
								?>
                                <option value="<?php echo($usuario_aux["id_usuario"]); ?>"><?php echo($usuario_aux["nombre_completo"]); ?></option>
                                <?php
									}
								?>
							</select>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="contenido wrapper clearfix" id="d_fellows"></div>
        <?php
        $contenidoHtml->footer();
        ?>
    </body>
</html>
