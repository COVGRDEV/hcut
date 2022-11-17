<?php
session_start();
//Autor: Feisar Moreno - 01/06/2015

header("Content-Type: text/xml; charset=UTF-8");

require_once("../db/DbHistoriaClinica.php");
require_once("../db/DbAdmision.php");
require_once("../db/DbMenus.php");
require_once("../db/DbDisponibilidadProf.php");
require_once("../principal/ContenidoHtml.php");
require_once("../funciones/Utilidades.php");
require_once("Class_Combo_Box.php");

$dbHistoriaClinica = new DbHistoriaClinica();
$dbAdmision = new DbAdmision();
$dbMenus = new DbMenus();
$dbDisponibilidadProf = new DbDisponibilidadProf();
$contenido = new ContenidoHtml();
$utilidades = new Utilidades();
$combo = new Combo_Box();

$contenido->validar_seguridad(1);
$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);

$opcion = $_POST["opcion"];

switch ($opcion) {
    case "1": //Registrar la remisión
        $id_usuario = $_SESSION["idUsuario"];
        @$id_hc = $utilidades->str_decode($_POST["id_hc"]);
        @$id_admision = $utilidades->str_decode($_POST["id_admision"]);
        @$id_tipo_cita = $utilidades->str_decode($_POST["id_tipo_cita"]);
        @$id_tipo_reg = $utilidades->str_decode($_POST["id_tipo_reg"]);
        @$id_tipo_cita_rem = $utilidades->str_decode($_POST["id_tipo_cita_rem"]);
        @$id_tipo_reg_rem = $utilidades->str_decode($_POST["id_tipo_reg_rem"]);
        @$id_usuario_rem = $utilidades->str_decode($_POST["id_usuario_rem"]);
        @$id_lugar_rem = $utilidades->str_decode($_POST["id_lugar_rem"]);
        @$observaciones_remision = $utilidades->str_decode($_POST["observaciones_remision"]);
        @$cant_examenes = intval($_POST["cant_examenes"], 10);

        $arr_examenes = array();
        for ($i = 0; $i < $cant_examenes; $i++) {
            @$arr_examenes[$i]["id_examen"] = $utilidades->str_decode($_POST["id_examen_" . $i]);
            @$arr_examenes[$i]["id_ojo"] = $utilidades->str_decode($_POST["id_ojo_" . $i]);
        }

        //Se obtiene la página que representa el menú de estados de atención
        $menu_obj = $dbMenus->getMenu(13);
        $url_menu = $menu_obj['pagina_menu'];

        if ($id_tipo_cita == $id_tipo_cita_rem) {
            //Si se trata del mismo tipo de cita solamente se mueve el flujo
            $resultado = $dbHistoriaClinica->editar_historia_clinica_rem($id_admision, $id_tipo_cita_rem, $id_tipo_reg_rem, $id_usuario_rem, $observaciones_remision, $id_usuario);
        } else {
            //Si no se trata del mismo tipo de cita, se debe crear una nueva admisión
            $resultado = $dbAdmision->crear_admision_remision($id_admision, $id_tipo_cita_rem, $observaciones_remision, $arr_examenes, $id_usuario, $id_lugar_rem);
        }
        ?>
        <input type="hidden" id="hdd_resultado_admision_remision" value="<?php echo($resultado); ?>" />
        <input type="hidden" id="hdd_url_menu_admision_remision" value="<?php echo $url_menu; ?>" />
        <?php
        break;

    case "2": //Se buscan los usuarios disponibles para atender la remisión
        @$id_admision = $utilidades->str_decode($_POST["id_admision"]);
        @$id_tipo_reg = $utilidades->str_decode($_POST["id_tipo_reg"]);
        @$id_lugar_cita = $utilidades->str_decode($_POST["id_lugar_cita"]);

        //Se verifica si ya existe el registro de historia clínica para la admisión respectiva
        $historia_clinica_obj = $dbHistoriaClinica->getHistoriaClinicaAdmision($id_admision, $id_tipo_reg);

        if (isset($historia_clinica_obj["id_hc"])) {
            ?>
            <input type="hidden" id="hdd_mostrar_usuarios_rem" value="0" />
            <input type="hidden" id="cmb_usuario_rem" value="" />
            <?php
        } else {
            ?>
            <input type="hidden" id="hdd_mostrar_usuarios_rem" value="1" />
            <?php
            //Se obtiene el listado de usuarios disponibles
            $lista_usuarios = $dbDisponibilidadProf->getListaUsuariosDisponiblesTipoReg2($id_tipo_reg, $id_lugar_cita, true);

            //Se determina el número de usuarios con disponibilidad en la misma sede
            $cant_usuarios_sede = 0;
            foreach ($lista_usuarios as $usuario_aux) {
                if ($usuario_aux["id_lugar_disp"] == $id_lugar_cita) {
                    $cant_usuarios_sede++;
                }
            }

            if (count($lista_usuarios) > 0) {
                ?>
                <select id="cmb_usuario_rem">
                    <option value="">&lt;Seleccione&gt;</option>
                    <?php
                    //Se selecciona uno de los usuarios de forma aleatoria
                    $num_sel = -1;
                    if ($cant_usuarios_sede == 1) {
                        $num_sel = 0;
                    } else if ($cant_usuarios_sede > 1) {
                        $num_sel = rand(0, count($cant_usuarios_sede) - 1);
                    }

                    $id_lugar_disp_ant = "";
                    for ($i = 0; $i < count($lista_usuarios); $i++) {
                        $usuario_aux = $lista_usuarios[$i];

                        if ($usuario_aux["id_lugar_disp"] != $id_lugar_disp_ant) {
                            ?>
                            <optgroup label="<?php echo($usuario_aux["lugar_disp"]); ?>"></optgroup>
                            <?php
                        }
                        $selected_aux = "";
                        if ($i == $num_sel) {
                            $selected_aux = ' selected="selected"';
                        }
                        ?>
                        <option value="<?php echo($usuario_aux["id_usuario"]); ?>"<?php echo($selected_aux); ?>><?php echo($usuario_aux["nombre_usuario"] . " " . $usuario_aux["apellido_usuario"]); ?></option>
                        <?php
                        $id_lugar_disp_ant = $usuario_aux["id_lugar_disp"];
                    }
                    ?>
                </select>
                <?php
            } else {
                ?>
                <select id="cmb_usuario_rem">
                    <option value="">&lt;No se encontraron usuarios disponibles&gt;</option>
                </select>
                <?php
            }
        }
        break;
}
?>
