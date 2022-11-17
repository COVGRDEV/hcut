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
	require_once("../db/DbCirugias.php");
	
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
	$dbCirugias = new DbCirugias();
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
        <script type='text/javascript' src='registro_cx_v1.2.js'></script>
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
                        <li class="breadcrumb_on">Registro de Cirug&iacute;as</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="contenedor_principal">
            <div id="guardar_historia_clinica" style="width: 100%; display: block;">
                <div class='contenedor_error' id='contenedor_error'></div>
                <div class='contenedor_exito' id='contenedor_exito'></div>
            </div>
            <div class="formulario" id="principal_historia_clinica" style="width: 100%; display: block; ">
            	<form name="frmRegistroCx" id="frmRegistroCx">
                    <table id="buscar" border="0" style="width: 100%;">
                        <tr><td colspan="2"> <h4>Buscar pacientes</h4></td></tr>
                        <tr>
                            <td style="width: 60%;text-align: right;">
                                <input type="text" id="txt_paciente_hc" name="txt_paciente_hc" placeholder="Buscar por nombre o documento del paciente" style="width:320px; float:right;" onblur="trim_cadena(this);" />
                            </td>
                            <td style="width: 40%;text-align: left;">
                                <input class="btnPrincipal peq" type="submit" value="Consultar" id="btn_consultar" name="btn_consultar" onclick="validarBuscarPersonasHc();" />
                            </td>
                        </tr>
                    </table>
                </form>
                <div id="contenedor_paciente_hc" style="min-height: 30px;"></div>
            </div>
        </div>
        <div id="d_impresion_stickers" style="display:none;"></div>
        <div class="div_centro" id="d_centro_stickers" style="width:600px; top:100px; display:none;">
        	<a name="a_cierre_panel2" id="a_cierre_panel2" href="#" onclick="cerrar_div_centro_stickers();"></a>
            <div id="d_interno_stickers" class="div_interno">
                <table class="datagrid" border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%;">
                    <tr style="height:30px;"></tr>
                    <tr class="headegrid">
                        <th align="right" style="width:30%; border: 1px solid #fff;">Cargar archivo:&nbsp;</th>
                        <th align="left" style="width:70%; border: 1px solid #fff;">
                            <form name="frm_arch_stickers" id="frm_arch_stickers" target="ifr_arch_stickers" action="registro_cx_ajax.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" id="opcion" name="opcion" value="3" />
                                <input type="file" id="fil_hoja_stickers" name="fil_hoja_stickers" />
                                <input type="hidden" id="hdd_id_hc_stickers" name="hdd_id_hc_stickers" value="" />
                            </form>
                            <div style="display:none;">
                                <iframe name="ifr_arch_stickers" id="ifr_arch_stickers"></iframe>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th align="center" colspan="2" style="border: 1px solid #fff;">
                            <input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" value="Aceptar" class="btnPrincipal" onclick="cargar_hoja_stickers();"/>&nbsp;&nbsp;
                            <input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" value="Cancelar" class="btnSecundario" onclick="cerrar_div_centro_stickers();"/>
                        </th>
                    </tr>
                </table>
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
