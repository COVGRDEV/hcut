<?php
session_start();
require_once("../db/DbVariables.php");
require_once("../db/DbEstadosAtencion.php");
require_once("../db/DbCitas.php");
require_once("../db/DbUsuarios.php");
require_once("../db/DbAsignarCitas.php");
require_once("../db/DbListas.php");
require_once '../principal/ContenidoHtml.php';
require_once("../funciones/Class_Combo_Box.php");
require_once("../funciones/FuncionesPersona.php");
require_once("../funciones/Utilidades.php");

$variables = new Dbvariables();
$estadosAtencion = new DbEstadosAtencion();
$citas = new DbCitas();
$usuarios = new DbUsuarios();
$asignar_citas = new DbAsignarCitas();
$dbListas = new DbListas();
$contenidoHtml = new ContenidoHtml();
$combo = new Combo_Box();
$funcionesPersona = new FuncionesPersona();
$utilidades = new Utilidades();

$id_usuario = $_SESSION["idUsuario"];

$id_lugar_usuario = "";
if (isset($_SESSION["idLugarUsuario"])) {
    $id_lugar_usuario = $_SESSION["idLugarUsuario"];
}

$id_lugar = "";
if (isset($_SESSION["id_lugar_cita"])) {
    $id_lugar = $_SESSION["id_lugar_cita"];
} else if ($id_lugar_usuario != "999") {
    $id_lugar = $id_lugar_usuario;
}

//variables
$titulo = $variables->getVariable(1);
$listadoEstadosAtencion = $estadosAtencion->getEstadosatencion();

$id_menu = $_POST["hdd_numero_menu"];
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
        <script type='text/javascript' src='estado_atencion_v1.17.js'></script>
        <script type='text/javascript' src='../js/validaFecha.js'></script>
    </head>
    <body>
        <?php
        $contenidoHtml->validar_seguridad(0);
        $contenidoHtml->cabecera_html();
        ?>
        <input type="hidden" id="hdd_menu" value="<?php echo($id_menu); ?>" />
        <input type="hidden" id="hdd_lugar_usuario" value="<?php echo($id_lugar_usuario); ?>" />
        <div class="title-bar">
            <div class="wrapper">
                <div class="breadcrumb breadcrumb-width" style="width: 300px; float: left; margin-left: -50px; ">
                    <ul>
                        <li class="breadcrumb_on">Estados de atenci&oacute;n</li>
                    </ul>
                </div>
                <div id="d_cargar_hc_ea"></div>
                <div class="right" style='margin-right: -50px; '>
                    <form id="frmbuscarPaciente" name="frmbuscarPaciente">
                        <table border='0' style="width: 400px; margin: auto;">
                            <tr>
                                <td>
                                    <?php
                                    $combo->getComboDb('cmb_lista_usuarios', $id_usuario, $asignar_citas->getListaUsuariosCitas('', -1), 'id_usuario, nombre_completo', '--Todos los especialistas--', 'buscar_estadoatencion(0)', '', 'width:250px;');
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $lista_lugares = $dbListas->getListaDetalles(12);
                                    $combo->getComboDb("cmb_lugar_cita", $id_lugar, $lista_lugares, "id_detalle, nombre_detalle", "--Todos los lugares--", 'buscar_estadoatencion(0)', true, "width: 210px;");
                                    ?>
                                </td>
                                <td>
                                    <input type="text" id="txt_buscarpaciente" name="txt_buscarpaciente" placeholder="Buscar por nombre del Paciente" style="width: 220px;"/>
                                </td>
                                <td>
                                    <input class="btnPrincipal peq" type="submit" value="Consultar" id="btn_consultar" name="btn_consultar" onclick="buscar_estadoatencion(1)" />
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
        <div class="contenedor_principal_estados_atencion">
            <div class="padding">
                <div id="contenedor"></div>
                <div id="d_registro_lugar_adm" style="display:none;"></div>
            </div>
        </div>
        <?php
        $contenidoHtml->footer();
        ?>
    </body>
</html>
