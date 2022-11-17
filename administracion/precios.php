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
        <link href="../css/foundation-datepicker.css" rel="stylesheet" type="text/css" />
        
        <script type="text/javascript" src="../js/jquery.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.add.js"></script>      
        <script type="text/javascript" src="../js/ajax.js"></script>
        <script type="text/javascript" src="../js/funciones.js"></script>        
        <script type="text/javascript" src="../js/validaFecha.js"></script>	
        <script type='text/javascript'  src="../js/sweetalert2.all.min.js"></script> 
        <script type="text/javascript" src="precios_v1.7.js"></script>
       	
		
        <link href="../src/skin-vista/ui.dynatree.css" rel="stylesheet" type="text/css" />
        <script src="../src/jquery.dynatree.js" type="text/javascript"></script>
        <link href="http://netdna.bootstrapcdn.com/font-awesome/3.0.2/css/font-awesome.css" rel="stylesheet">
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
                        <li class="breadcrumb_on">Precios por convenios</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="contenedor_principal volumen">
            <div class="padding">
                <form id="frmBuscarConvenio" name="frmBuscarConvenio">
                    <table style="width: 100%;">
                        <tr valign="middle">
                            <td align="center" colspan="2">
                                <div id="advertenciasg">
                                    <div class="contenedor_error" id="contenedor_error"></div>
                                    <div class="contenedor_exito" id="contenedor_exito"></div>
                                </div> 
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" id="txtParametro" name="txtParametro" placeholder="Codigo o nombre del convenio" onblur="trim_cadena(this);" />
                            </td>
                            <td style="width: 10%;">
                                <input type="submit" id="btnBuscar" nombre="btnBuscar" value="Buscar" class="btnPrincipal peq" onclick="buscarConvenio();"/>
                            </td>
                            <td style="width: 10%;">
                                <input type="button" id="btn_buscar_usuario" nombre="btn_buscar_usuario" value="Ver todos" class="btnSecundario peq" onclick="muestra_convenios();"/>
                            </td>
                        </tr>
                    </table>
                </form>
                <div id="principal_convenios"></div>
                <input type="hidden" id="hdd_resultado" name="hdd_resultado" />
            </div>
        </div>
        <script type="text/javascript" src="../js/foundation.min.js"></script>
        <div id="fondo_negro_agregar_servicios" class="d_fondo_negro"></div>
        <div class="div_centro" id="d_centro_agregar_servicios" style="display:none;">
            <a name="a_cierre_panel" id="a_cierre_panel" href="#" onclick="ventanaAgregarPrecio(0);"></a>
            <div class="div_interno" id="d_interno_agregar_servicios"></div>
        </div>
        <div id="fondo_negro_servicios" class="d_fondo_negro"></div>
        <div class="div_centro" id="d_centro_servicios" style="display:none;">
            <a name="a_cierre_panel" id="a_cierre_panel" href="#" onclick="ventanaServicios(0);"></a>
            <div class="div_interno" id="d_interno_servicios"></div>
        </div>
        <?php
			$contenido->footer();
		?>  
    </body>
</html>
