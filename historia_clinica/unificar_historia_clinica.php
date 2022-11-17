<?php
	session_start();
	/*
	  Pagina para ver las historias clinicas
	  Autor: Helio Ruber López - 29/03/2014
	 */
	require_once("../db/DbVariables.php");
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbHistoriaClinica.php");
	require_once("../db/DbAdmision.php");
	require_once("../db/DbTiposCitas.php");
	require_once("../db/DbMenus.php");
	require_once("../db/DbPacientes.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/Utilidades.php");
	
	$variables = new Dbvariables();
	$usuarios = new DbUsuarios();
	$dbAdmision = new DbAdmision();
	$dbTiposCitas = new DbTiposCitas();
	$historia_clinica = new DbHistoriaClinica();
	$menus = new DbMenus();
	$contenido = new ContenidoHtml();
	$listas = new DbListas();
	$combo = new Combo_Box();
	$pacientes = new DbPacientes();
	$utilidades = new Utilidades();
	
	//variables
	$titulo = $variables->getVariable(1);
	$horas_edicion = $variables->getVariable(7);
	
	//Cambiar las variables get a post
	$utilidades->get_a_post();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $titulo['valor_variable']; ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
        <link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
        <link href="../css/azul.css" rel="stylesheet" type="text/css" />
        <link href="../css/foundation-datepicker.css" rel="stylesheet" type="text/css" />
        <!--Para autocompletar DEBE IR DE PRIMERO-->
        <script type='text/javascript' src='../js/jquery_autocompletar.js'></script>
        <script type='text/javascript' src='../js/jquery-ui.js'></script>
        <!--Para validar DEBE IR DE SEGUNDO-->
        <script type='text/javascript' src='../js/jquery.validate.js'></script>
        <script type='text/javascript' src='../js/jquery.validate.add.js'></script>
        <!--Para funciones de optometria DEBE IR DE TERCERO-->
        <script type='text/javascript' src='../js/ajax.js'></script>
        <script type='text/javascript' src='../js/funciones.js'></script>
        <script type='text/javascript' src='../js/validaFecha.js'></script>
        <script type='text/javascript' src='../js/foundation-datepicker.js'></script>
        <script type='text/javascript' src='unificar_historia_clinica.js'></script>
    </head>
    <body>
        <?php
			$contenido->validar_seguridad(0);
			$contenido->cabecera_html();
			
			$id_usuario_crea = $_SESSION["idUsuario"];
        ?>
        <div class="title-bar">
            <div class="wrapper">
                <div class="breadcrumb">
                    <ul>
                        <li class="breadcrumb_on">Unificaci&oacute;n de Historias Cl&iacute;nicas</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="contenedor_principal">
            <div id="guardar_historia_clinica" style="width:100%;">
                <div class='contenedor_error' id='contenedor_error'></div>
                <div class='contenedor_exito' id='contenedor_exito'></div>
                <div id="d_guardar_hc" style="display:none;"></div>
            </div>
            <div class="formulario" id="principal_historia_clinica" style="width: 100%; display: block; ">
                <table border="0" cellpadding="5" style="width: 100%;">
                    <tr>
                    	<td colspan="2" style="width:50%;"><h4>Buscar paciente 1</h4></td>
                    	<td colspan="2" style="width:50%;"><h4>Buscar paciente 2</h4></td>
                    </tr>
                    <tr>
                        <td style="width:35%; text-align:right;">
                            <input type="text" id="txt_paciente_hc_1" placeholder="Buscar por nombre o documento del paciente" style="width:320px; float:right;" onblur="trim_cadena(this);" />
                        </td>
                        <td style="width:15%; text-align:left;">
                            <input type="button" id="btn_consultar_1" class="btnPrincipal peq" value="Buscar" onclick="buscar_paciente(1);" />
                        </td>
                        <td style="width:35%; text-align:right;">
                            <input type="text" id="txt_paciente_hc_2" placeholder="Buscar por nombre o documento del paciente" style="width:320px; float:right;" onblur="trim_cadena(this);" />
                        </td>
                        <td style="width:15%; text-align:left;">
                            <input type="button" id="btn_consultar_2" class="btnPrincipal peq" value="Buscar" onclick="buscar_paciente(2);" />
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="2" valign="top"><div id="d_contenedor_paciente_hc_1" style="min-height: 30px;"></div></td>
                    	<td colspan="2" valign="top"><div id="d_contenedor_paciente_hc_2" style="min-height: 30px;"></div></td>
                    </tr>
                </table>
                <div id="d_guardar_unificacion" style="display:none;"></div>
            </div>
        </div>
        <script type='text/javascript' src='../js/foundation.min.js'></script>
        <script>
			$(document).foundation();
		</script>
        <?php
			$contenido->footer();
		?>
    </body>
</html>
