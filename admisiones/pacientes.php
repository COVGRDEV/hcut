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
require_once("../funciones/Class_Conector_Siesa.php");
require_once("../funciones/Class_Terceros_Siesa.php");
require_once("../funciones/Class_Consultas_Siesa.php");
require_once("../funciones/nusoap.php");

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
$conectorSiesa = new Class_Conector_Siesa();
$classTercerosSiesa = new Class_Terceros_Siesa();
$classConsultasSiesa = new Class_Consultas_Siesa();
$contenido->validar_seguridad(0);
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
        <title><?php echo $titulo['valor_variable']; ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
        <link href="../css/azul.css" rel="stylesheet" type="text/css" />

        <script type="text/javascript" src="../js/jquery.js"></script>
        <script type='text/javascript' src='../js/jquery.validate.js'></script>
        <script type='text/javascript' src='../js/jquery.validate.add.js'></script>
        <script type='text/javascript' src='../js/ajax.js'></script>
        <script type='text/javascript' src='../js/funciones.js'></script>
        <script type='text/javascript' src='../js/validaFecha.js'></script>  
        <script type='text/javascript'  src="../js/sweetalert2.all.min.js"></script>   

        <script type='text/javascript' src='pacientes_v1.14.js'></script>
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
                        <li class="breadcrumb_on">Pacientes</li>
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
            <div class="formulario" id="principal_historia_clinica" style="width: 100%; display: block;">
                <table border="0" style="width:90%; margin: 0 auto;">
                    <tr>
                        <td colspan="3"><h4>Buscar paciente</h4></td>
                    </tr>
                    <tr>
                        <td style="width:70%; text-align:right;">
                            <input type="text" id="txt_paciente_hc" placeholder="Buscar por nombre o documento del paciente" style="width:100%; float:right;" onblur="trim_cadena(this);" />
                        </td>
                        <td style="text-align:center;">
                            <input type="button" id="btn_consultar" class="btnPrincipal" value="Buscar" onclick="buscar_paciente();" />
                        </td>
                        <?php
                        if ($tipo_acceso_menu == 2) {
                            ?>
                            <td style="text-align:center;">
                                <input type="button" id="btn_consultar" class="btnPrincipal" value="Nuevo" onclick="nuevo_paciente();" />
                            </td>
                            <td>
                                <input type="button" id="btn_consultar" class="btnPrincipal" value="Importar" onclick="muestraImportarPacientes();" />
                            </td>
                            <?php
                        }
                        ?>
                    </tr>
                    <tr>
                        <td colspan="2" valign="top"></td>
                    </tr>
                </table>
            </div>
            <div id="d_contenedor_paciente_hc" style="min-height: 30px;padding: 30px;"></div>
            <div id="d_guardar_paciente" style="display:none;"></div>         
        </div>       
        <?php
        $contenido->footer();
        ?>
    </body>
</html>
