<?php
session_start();
/*
  Pagina para asignar citas
  Autor: Helio Ruber López - 16/09/2013
 */
require_once("../db/DbVariables.php");
require_once("../db/DbListas.php");
require_once("../db/DbAsignarCitas.php");
require_once("../db/DbMenus.php");
require_once("../funciones/Utilidades.php");
require_once("../principal/ContenidoHtml.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../funciones/FuncionesPersona.php");

$dbVariables = new Dbvariables();
$dbAsignarCitas = new DbAsignarCitas();
$dbMenus = new DbMenus();

$utilidades = new Utilidades();
$contenido = new ContenidoHtml();
$combo = new Combo_Box();
$funcionesPersona = new FuncionesPersona();

//variables
$titulo = $dbVariables->getVariable(1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $titulo["valor_variable"]; ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
        <link href="../css/azul.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="../js/jquery.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.add.js"></script>
        <script type="text/javascript" src="../js/ajax.js"></script>
        <script type="text/javascript" src="../js/funciones.js"></script>
        <script type="text/javascript" src="../js/validaFecha.js"></script>
        <script type="text/javascript" src="../funciones/ckeditor/ckeditor.js"></script>
        <script type="text/javascript" src="../funciones/ckeditor/config.js"></script>
        <script type='text/javascript'  src="../js/sweetalert2.all.min.js"></script>  

        <script type="text/javascript" src="asignar_citas_v1.40.js"></script>

        <script type="text/javascript">
            $(document).ready(function () {

                mostrar_mesaje_citas();
                calendario("", "");

                setInterval("refrescar_calendario()", 30000);

                setInterval("mostrar_mesaje_citas();", 30000);
            });
        </script>
    </head>
    <body>
        <?php
        $contenido->validar_seguridad(0);
        $contenido->cabecera_html();
        $lista_usuarios = $dbAsignarCitas->getListaUsuariosCitas("", 1);

        //Se verifica si es una reasignación de cita
        $ind_reasignar = 0;
        $id_cita_reasignar = 0;
        $id_usuario_prof_c = 0;
        $id_prog_cx = "";
        $id_paciente_prog_cx = "";
        $id_usuario_prof_prog_cx = "";
        $cita_cancelar_obj = array();
        if (isset($_POST["hdd_reasignar"])) {
            $ind_reasignar = intval($_POST["hdd_reasignar"], 10);
            $id_cita_reasignar = intval($_POST["hdd_cita_cancelar"], 10);

            //Se buscan los datos de la cita a cancelar
            $cita_cancelar_obj = $dbAsignarCitas->getCita($id_cita_reasignar);
            if (isset($cita_cancelar_obj["id_usuario_prof"])) {
                $id_usuario_prof_c = $cita_cancelar_obj["id_usuario_prof"];
            }
        } else if (isset($_POST["hdd_id_prog_cx"])) {
            //Es una asignación relacionada con programación de cirugías
            @$id_prog_cx = $utilidades->str_decode($_POST["hdd_id_prog_cx"]);
            @$id_paciente_prog_cx = $utilidades->str_decode($_POST["hdd_id_paciente"]);
            @$id_usuario_prof_prog_cx = $utilidades->str_decode($_POST["hdd_id_usuario_prof"]);
        } else if (isset($_POST["hdd_id_paciente"])) {
            @$id_paciente_prog_cx = $utilidades->str_decode($_POST["hdd_id_paciente"]);
        }
        ?>
        <input type="hidden" name="hdd_reasignar" id="hdd_reasignar" value="<?php echo($ind_reasignar); ?>" />
        <input type="hidden" name="hdd_cita_reasignar" id="hdd_cita_reasignar" value="<?php echo($id_cita_reasignar); ?>" />
        <input type="hidden" name="hdd_id_prog_cx" id="hdd_id_prog_cx" value="<?php echo($id_prog_cx); ?>" />
        <input type="hidden" name="hdd_id_paciente_prog_cx" id="hdd_id_paciente_prog_cx" value="<?php echo($id_paciente_prog_cx); ?>" />
        <input type="hidden" id="d_generar_pdf2" name="d_generar_pdf2" />
        <div class="title-bar">
            <div class="wrapper">
                <?php
                if ($ind_reasignar != 1) {
                    if ($id_prog_cx == "") {
                        ?>
                        <h3 style="color: #fff;">Asignaci&oacute;n de citas</h3>
                        <?php
                    } else {
                        //Se buscan los datos del menú de origen
                        $menu_obj = $dbMenus->getMenu($_POST["hdd_numero_menu"]);
                        ?>
                        <div class="breadcrumb">
                            <ul>
                                <li class="breadcrumb_on"><?php echo($menu_obj["nombre_menu"]); ?> (<a href="#" onclick="volver_programacion_cx(<?php echo($id_prog_cx); ?>, <?php echo($id_paciente_prog_cx); ?>, <?php echo($_POST["hdd_numero_menu"]); ?>);">Volver</a>)</li>
                            </ul>
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <h3 class="texto_resaltar" style="color: #fff;">Reasignaci&oacute;n (<?php echo($funcionesPersona->obtenerNombreCompleto($cita_cancelar_obj["nombre_1"], "", $cita_cancelar_obj["apellido_1"], "")); ?>)</h3>
                    <?php
                }
                ?>
                <table border="0" cellpadding="0" cellspacing="0" class="right filtros">
                    <tr>
                        <?php
                        if ($ind_reasignar == 1) {
                            ?>
                            <td>
                                <input type="button" id="btn_cancel_reasignar" nombre="btn_cancel_reasignar" value="Cancelar reasignaci&oacute;n" onclick="cancelar_reasignar_cita('<?php echo($_POST["hdd_numero_menu"]); ?>');" class="btnPrincipal" />
                            </td>
                            <?php
                        }
                        ?>
                        <td>
                            <select name="cmb_lista_usuarios" id="cmb_lista_usuarios" onchange="calendario('', '');">
                                <option value="" selected>Todos los Profesionales</option>
                                <?php
                                foreach ($lista_usuarios as $usuario_aux) {
                                    $selected_aux = "";
                                    if ($usuario_aux["id_usuario"] == $id_usuario_prof_c || $usuario_aux["id_usuario"] == $id_usuario_prof_prog_cx) {
                                        $selected_aux = " selected=\"selected\"";
                                    }
                                    ?>
                                    <option value="<?php echo($usuario_aux["id_usuario"]); ?>"<?php echo($selected_aux); ?>><?php echo($usuario_aux["nombre_completo"]); ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </td>
                        <?php
                        if ($ind_reasignar != 1) {
                            ?>
                            <td>
                                <form id="frm_buscar_cita" name="frm_buscar_cita" method="post">
                                    <input type="text" name="txt_busca_usuario" id="txt_busca_usuario" placeholder="Buscar Paciente" onkeyup="evento_enter();" >
                                        <a href="#" class="btn ir" onclick="buscar_persona_cita()">Buscar</a>
                                </form>
                            </td>
                            <?php
                        }
                        ?>
                    </tr>
                </table>
            </div>
        </div>
        <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%;">
            <tr><td colspan="2">
                    <div class="contenedor_error" id="msg_buscar"></div>	
                </td>
            </tr>
            <tr>
                <td align="center" colspan="2">
                    <div id="mensaje_agenda"></div>
                    <div id="agenda"></div>	
                </td>
            </tr>
        </table>
        <div id="fondo_negro_citas" class="d_fondo_negro div_centro_sobre"></div>
        <div id="d_centro_cita" class="div_centro div_centro_sobre" style="display:none;">
            <a name="a_cierre_panel" id="a_cierre_panel" href="#" onclick="cerrar_div_centro_citas();"></a>
            <div id="d_interno_citas" class="div_interno"></div>
        </div>
        <div id="fondo_negro_citas2" class="d_fondo_negro div_centro_sobre"></div>
        <div id="d_centro_cita2" class="div_centro div_centro_sobre" style="display:none;">
            <a name="a_cierre_panel2" id="a_cierre_panel2" href="#" onclick="cerrar_div_centro_citas2();"></a>
            <div id="d_interno_citas2" class="div_interno"></div>
        </div>
        <?php
        $contenido->footer();
        ?>
        <div id="d_guardar_cita" style="display:none;"></div>
    </body>
</html>
