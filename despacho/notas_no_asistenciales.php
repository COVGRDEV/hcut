<?php
	session_start();
	/*
	  Pagina para la creación y edición de notas no asistenciales
	  Autor: Feisar Moreno - 09/12/2016
	 */
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
        <link href="../css/foundation-datepicker.css" rel="stylesheet" type="text/css" />
		<link href="http://netdna.bootstrapcdn.com/font-awesome/3.0.2/css/font-awesome.css" rel="stylesheet">
        
        <script type='text/javascript' src='../js/jquery.min.js'></script>
        <script type="text/javascript" src="../js/jquery-ui.custom.js"></script>
        <script type="text/javascript" src="../js/jquery.cookie.js"></script>
        <script type='text/javascript' src='../js/jquery.validate.js'></script>
        <script type='text/javascript' src='../js/jquery.validate.add.js'></script>
        <script type='text/javascript' src='../js/ajax.js'></script>
        <script type='text/javascript' src='../js/funciones.js'></script>
        
        <script type='text/javascript' src='../js/validaFecha.js'></script>
        <script type='text/javascript' src='../js/foundation-datepicker.js'></script>
        <script type="text/javascript" src="../js/jquery.maskedinput.js"></script>
        
        <script type='text/javascript' src='../funciones/ckeditor/ckeditor.js'></script>
        <script type='text/javascript' src='../funciones/ckeditor/config.js'></script>
        <script type='text/javascript' src='notas_no_asistenciales.js'></script>
        
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
                        <li class="breadcrumb_on">Notas administrativas</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="contenedor_principal volumen">
            <div class="padding">
                <div class='contenedor_exito' id='contenedor_exito'></div>
                <div class='contenedor_error' id='contenedor_error'></div>
                <form id="frm_buscar_notas" name="frm_buscar_notas"> 
                    <table style="width:100%;">
                        <tr>
                            <td align="right" style="width:10%;">
                                <label class="inline">Paciente:</label>
                            </td>
                            <td align="left" style="width:64%;">
                                <input type="text" id="txt_parametro" name="txt_parametro" placeholder="Nombre o n&uacute;mero de documento del paciente" onblur="convertirAMayusculas(this); trim_cadena(this);" />
                            </td>
                            <td align="right" style="width:6%;">
                                <label class="inline">Fecha:</label>
                            </td>
                            <td align="left" style="width:10%;">
                                <input type="text" id="txt_fecha_admision_b" name="txt_fecha_admision_b" class="input" maxlength="10" style="width:120px;" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" />
                            </td>
                            <td style="width:10%;">
                                <input type="submit" id="btn_buscar" nombre="btn_buscar" value="Buscar" class="btnPrincipal peq" onclick="buscar_admisiones();" />
                            </td>
                        </tr>
                    </table>
                </form>
                <div id="d_resultados_b"></div>
            </div>
        </div>
        <script type='text/javascript' src='../js/foundation.min.js'></script>
        <script>
			$(document).foundation();
			
			$(function() {
				$("#txt_fecha_admision_b").fdatepicker({
					format: "dd/mm/yyyy"
				});
			});
        </script>
        <?php
			$contenido->footer();
		?>  
    </body>
</html>
