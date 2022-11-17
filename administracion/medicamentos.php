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
        <script type="text/javascript" src="../funciones/ckeditor/ckeditor.js"></script>
        <script type="text/javascript" src="../funciones/ckeditor/config.js"></script>
        <script type="text/javascript" src="medicamentos_v1.1.js"></script>
		
        <link href="../src/skin-vista/ui.dynatree.css" rel="stylesheet" type="text/css" >
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
                    <ul>
                        <li class="breadcrumb_on">Administraci&oacute;n de medicamentos</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="contenedor_principal volumen">
            <div class="padding">
                <form id="frm_buscar_medicamento" name="frm_buscar_medicamento"> 
                    <table style="width: 100%;">
                        <tr valign="middle">
                            <td align="center" colspan="2">
                                <div id="advertenciasg">
                                    <div class="contenedor_error" id="contenedor_error"></div>
                                    <div class="contenedor_exito" id="contenedor_exito"></div>
                                    <div style="display:none;" id="d_resultado_medicamento"></div>
                                </div> 
                            </td>
                        </tr>
                        <tr>
                            <td style="width:65%;">
                                <input type="text" id="txt_parametro" name="txt_parametro" placeholder="Código o nombre del medicamento" onblur="trim_cadena(this);" />
                            </td>
                            <td align="left" style="width:35%;">
                                <input type="submit" id="btn_buscar_proc" nombre="btn_buscar_proc" value="Buscar" class="btnPrincipal peq" onclick="buscar_medicamento();" />
                                &nbsp;&nbsp;
                                <input type="button" id="btn_nuevo_proc" nombre="btn_nuevo_proc" value="Nuevo medicamento" class="btnPrincipal peq" onclick="nuevo_medicamento();" />
                                &nbsp;&nbsp;
                                <!--<input type="button" id="btn_actualizar_proc" nombre="btn_actualizar_proc" value="Cargar actualizaci&oacute;n" class="btnPrincipal peq" onclick="cargar_actualizacion();" />-->
                            </td>
                        </tr>
                    </table>
                </form>
                <div id="d_principal_medicamentos"></div>
                <input type="hidden" id="hdd_resultado" name="hdd_resultado" />
            </div>
        </div>
        <?php
			$contenido->footer();
		?>  
    </body>
</html>
