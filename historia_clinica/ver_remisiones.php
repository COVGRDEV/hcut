<?php
session_start();

//Encabezados para evitar el caché de la página
header("Expires: Sun, 01 Jan 2014 00:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once("../db/DbVariables.php");
require_once("../db/DbAdmision.php");
require_once("../db/DbPacientes.php");
require_once("../db/DbDespacho.php");
require_once("../db/DbUsuarios.php");
require_once("../db/DbHistoriaClinica.php");
require_once("../db/DbPermisos.php");
require_once("../db/DbMenus.php");
require_once("../db/DbTiposRegistrosHc.php");
require_once("../db/DbProcedimientosCotizaciones.php");
require_once("../db/DbFormulacionHC.php");
require_once("../db/DbMaestroProcedimientos.php");
require_once("../principal/ContenidoHtml.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../funciones/Utilidades.php");
require_once("../funciones/html2text.php");
require_once("../historia_clinica/FuncionesHistoriaClinica.php");
require_once("../funciones/Class_Ordenes_Remisiones.php");

$dbVariables = new Dbvariables();
$dbAdmision = new DbAdmision();
$dbPacientes = new DbPacientes();
$dbDespacho = new DbDespacho();
$dbUsuarios = new DbUsuarios();
$dbHistoriaClinica = new DbHistoriaClinica();
$dbPermisos = new DbPermisos();
$dbMenus = new DbMenus();
$dbTiposRegistrosHc = new DbTiposRegistrosHc();
$dbProcedimientosCotizaciones = new DbProcedimientosCotizaciones();
$dbFormulacionHC = new DbFormulacionHC();
$dbMaestroProcedimientos = new DbMaestroProcedimientos();

$class_remisiones = new Class_Ordenes_Remisiones();

$contenidoHtml = new ContenidoHtml();
$combo = new Combo_Box();
$utilidades = new Utilidades();
$funciones_hc = new FuncionesHistoriaClinica();

//variables
$titulo = $dbVariables->getVariable(1);
$dias_edicion = $dbVariables->getVariable(12);

//Cambiar las variables get a post
$utilidades->get_a_post();

//Identificador del menú de despacho
$id_menu = 39;

$bol_confirma_bloqueo = false;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo($titulo["valor_variable"]); ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
        <link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
        <link href="../css/azul.css" rel="stylesheet" type="text/css" />
        <link href="../css/Class_Color_Pick.css" rel="stylesheet" type="text/css" />

        <!--Para autocompletar DEBE IR DE PRIMERO-->
        <script type="text/javascript" src="../js/jquery_autocompletar.js"></script>
        <script type="text/javascript" src="../js/jquery-ui.js"></script>

        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="../js/funciones.js"></script>
        <script type="text/javascript" src="../js/ajax.js"></script>
        <script type="text/javascript" src="../js/Class_Color_Pick.js"></script>
        <script type="text/javascript" src="../funciones/ckeditor/ckeditor.js"></script>
        <script type="text/javascript" src="../funciones/ckeditor/config.js"></script>
        <script type="text/javascript" src="../historia_clinica/FuncionesHistoriaClinica.js"></script>
        <script type="text/javascript" src="../js/validaFecha.js"></script>
        <script type="text/javascript" src="../js/Class_Ordenes_Remisiones_v1.js"></script>
        <script type="text/javascript" src="ver_remisiones.js"></script>
    </head>
    <body onload="ajustar_textareas();">
        <?php
        $contenidoHtml->validar_seguridad(0);
        if (!isset($_POST["tipo_entrada"])) {
            $contenidoHtml->cabecera_html();
        }

        $id_hc = $_POST["hdd_id_hc"];

        if (isset($_POST["hdd_id_paciente"])) {
            $id_paciente = $utilidades->str_decode($_POST["hdd_id_paciente"]);
            $nombre_paciente = $utilidades->str_decode($_POST["hdd_nombre_paciente"]);
            $id_admision = $utilidades->str_decode($_POST["hdd_id_admision"]);
            $id_usuario = $_SESSION["idUsuario"];

            //Se halla el tipo de permiso que tiene el usuario para la ventana
            $permiso_obj = $dbPermisos->getPermisoUsuarioMenu($id_usuario, $id_menu);

            //Se obtienen los datos de la admision
            $admision_obj = $dbAdmision->get_admision($id_admision);
            $id_usuario_prof = $admision_obj["id_usuario_prof"];
            $fecha_admision = $admision_obj["fecha_admision_t"];
            $nombre_convenio = $admision_obj["nombre_convenio"];
            $nombre_plan = $admision_obj["nombre_plan"];

            //Se obtiene las hc que se le realizaron segun la admision
            $tabla_hc_admisiones = $dbDespacho->getHcAdmisiones($id_admision);

            //Edad del paciente
            $datos_paciente = $dbPacientes->getEdadPaciente($id_paciente, "");
            $edad_paciente = $datos_paciente["edad"];
            $documento_paciente = $datos_paciente["numero_documento"];

            //Nombre del profesional que atiende la consulta
            $tabla_usuario_profesional = $dbUsuarios->getUsuario($id_usuario_prof);
            $nombre_usuario_profesional = $tabla_usuario_profesional["nombre_usuario"] . " " . $tabla_usuario_profesional["apellido_usuario"];

            //Nombre del profesional que atiende la consulta de Optometria
            $profesional_optometra = $dbAdmision->get_profesional_optometra($id_admision);
        } else {
            $tipo_accion = 0; //Ninguna accion Error
        }

        $funciones_hc->encabezado_historia_clinica($id_paciente, $id_admision, 0, 0, false);
        ?>
        <div class="contenedor_principal" id="id_contenedor_principal">
            <div id="guardar_despacho" style="width:100%; display:none;"></div>
            <div class="contenedor_error" id="contenedor_error"></div>
            <div class="contenedor_exito" id="contenedor_exito"></div>
            <div class="formulario" id="principal_despacho" style="width: 100%; display: block;">
                <?php
                //Se inserta el registro de ingreso a la historia clínica
                //$dbHistoriaClinica->crear_ingreso_hc($id_usuario, $id_paciente, $id_admision, "", 164);
                ?>
                <form id="frm_despacho" name="frm_despacho" method="post">

                    <input type="hidden" name="hdd_id_hc_consulta" id="hdd_id_hc_consulta" value="<?php echo($id_hc); ?>" />
                    <input type="hidden" name="hdd_id_paciente" id="hdd_id_paciente" value="<?php echo($id_paciente); ?>" />
                    <input type="hidden" name="hdd_id_admision" id="hdd_id_admision" value="<?php echo($id_admision); ?>" />
                    <input type="hidden" name="hdd_nombre_paciente" id="hdd_nombre_paciente" value="<?php echo($nombre_paciente); ?>" />
                    <input type="hidden" name="hdd_fecha_admision" id="hdd_fecha_admision" value="<?php echo($fecha_admision); ?>" />
                    <input type="hidden" name="hdd_documento_paciente" id="hdd_documento_paciente" value="<?php echo($documento_paciente); ?>" />
                    <input type="hidden" name="hdd_id_profesional" id="hdd_id_profesional" value="<?php echo($id_usuario_prof); ?>" />
                    <input type="hidden" name="hdd_nombre_profesional" id="hdd_nombre_profesional" value="<?php echo($nombre_usuario_profesional); ?>" />
                    <input type="hidden" name="hdd_nombre_profesional_optometra" id="hdd_nombre_profesional_optometra" value="<?php echo($nombre_profesional_optometra); ?>" />
                    <?php
                    $nombre_usuario_crea = trim($despacho_obj["nombre_usuario_crea"] . " " . $despacho_obj["apellido_usuario_crea"]);
                    $nombre_usuario_mod = trim($despacho_obj["nombre_usuario_mod"] . " " . $despacho_obj["apellido_usuario_mod"]);
                    ?>

                    <div class="tabs-container">
                        <dl class="tabs" data-tab>
                            <dd class="active"><a href="#panel2-1" onclick="setTimeout(function () {
                                        ajustar_textareas();
                                    }, 100);">REMISIONES</a></dd>

                        </dl>


                        <div class="tabs-content">
                            <div class="content active" id="panel2-1">
                                <?php
                                $ind_editar = 0;
                                $class_remisiones->getFormularioRemisiones($id_hc, 1, $ind_editar);
                                ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script type="text/javascript" src="../js/foundation.min.js"></script>
        <script>
                                $(document).foundation();

                                for (var i = 0; i < 10; i++) {
                                    initCKEditorRemisiones("tabla_rem_desc_" + i);
                                }
        </script>
        <?php
        if (!isset($_POST["tipo_entrada"])) {
            $contenidoHtml->ver_historia($id_paciente);
            $contenidoHtml->footer();
        } else {
            $contenidoHtml->footer_iframe();
        }
        ?>
    </body>
</html>
