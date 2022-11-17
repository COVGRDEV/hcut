<?php
session_start();
/*
  Pagina para ver las historias clinicas
  Autor: Helio Ruber López - 29/03/2014
 */
require_once("../db/DbVariables.php");
require_once("../db/DbUsuarios.php");
require_once("../db/DbListas.php");
require_once("../db/DbDespacho.php");
require_once("../db/DbAdmision.php");
require_once("../db/DbTiposCitas.php");
require_once("../db/DbMenus.php");
require_once("../db/DbPacientes.php");
require_once("../principal/ContenidoHtml.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../db/DbConvenios.php");
require_once("../funciones/Utilidades.php");

$variables = new Dbvariables();
$usuarios = new DbUsuarios();
$dbAdmision = new DbAdmision();
$dbTiposCitas = new DbTiposCitas();
$despacho = new DbDespacho();
$menus = new DbMenus();
$contenido = new ContenidoHtml();
$listas = new DbListas();
$combo = new Combo_Box();
$pacientes = new DbPacientes();
$utilidades = new Utilidades();
$dbConvenios = new DbConvenios();
$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);

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
        <title><?php echo $titulo["valor_variable"]; ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
        <link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
        <link href="../css/azul.css" rel="stylesheet" type="text/css" />
        <!--Para autocompletar DEBE IR DE PRIMERO-->
        <script type="text/javascript" src="../js/jquery_autocompletar.js"></script>
        <script type="text/javascript" src="../js/jquery-ui.js"></script>
        <!--Para validar DEBE IR DE SEGUNDO-->
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.add.js"></script>

        <script type="text/javascript" src="../funciones/ckeditor/ckeditor.js"></script>
        <script type="text/javascript" src="../funciones/ckeditor/config.js"></script>
        <script type="text/javascript" src="../js/Class_Color_Pick.js"></script>
        <script type="text/javascript" src="../js/jquery.textarea_autosize.js"></script>

        <!--Para funciones de optometria DEBE IR DE TERCERO-->
        <script type="text/javascript" src="../js/ajax.js"></script>
        <script type="text/javascript" src="../js/funciones.js"></script>
        <script type="text/javascript" src="../js/validaFecha.js"></script>
        <script type="text/javascript" src="../js/sweetalert2.all.min.js"></script>                

        <script type="text/javascript" src="autorizaciones_v1.3.js"></script>
        <script type="text/javascript" src="../js/Class_Ordenes_Remisiones_v1.js"></script>
        <script type='text/javascript' src='../admisiones/pacientes_v1.13.js'></script>
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
                        <li class="breadcrumb_on">Autorizaciones</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="contenedor_principal">
            <div id="guardar_formulas" style="width: 100%; display: block;">
                <div class="contenedor_error" id="contenedor_error"></div>
                <div class="contenedor_exito" id="contenedor_exito"></div>
            </div>           
            <div class="formulario" id="principal_frm_medicamentos" style="width: 100%; display: block; ">
                <form id="frm_formulas" name="frm_formulas" method="post">
                    <table id="buscar" border="0" style="width: 100%;">
                        <tr><td colspan="2"> <h4>Buscar pacientes</h4></td></tr>
                        <tr>
                            <td style="width: 55%;text-align: right;">
                                <input type="text" id="txt_paciente" name="txt_paciente" placeholder="Ingrese el documento o el nombre del paciente" style="width:90%;float: right;"  onblur="trim_cadena(this);" />
                            </td>
                            <td style="width: 45%;text-align: left;">
                                <input class="btnPrincipal peq" type="submit" value="Consultar &oacute;rdenes m&eacute;dicas" id="btn_consultar" name="btn_consultar" onclick="validarBuscarOrdenes();" />
                                <?php
                                if ($tipo_acceso_menu == 2) {
                                    ?>
                                    <input class="btnPrincipal peq" type="button" value="Homologar orden m&eacute;dica" id="btn_consultar" name="btn_consultar" onclick="validarBuscarPacientes();" />
                                    <?php
                                }
                                ?>
                            </td>

                        </tr>
                    </table>
                </form>
                <div id="contenedor_ordenes" style="min-height: 30px;"></div>
                <div id="div_ruta_reporte_pdf"></div>

            </div>
        </div>
        <script type="text/javascript" src="../js/foundation.min.js"></script>
        <script>
                                        $(document).foundation();
        </script>
        <?php
        $contenido->footer();
        ?>
    </body>
</html>

