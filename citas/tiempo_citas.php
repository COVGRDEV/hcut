<?php
session_start();
require_once("../db/DbVariables.php");
require_once '../principal/ContenidoHtml.php';
require_once '../db/DbPerfiles.php';
require_once '../db/DbUsuarios.php';
require_once '../db/DbUsuariosPerfiles.php';
require_once '../db/DbTiposCitas.php';
require_once '../funciones/Class_Combo_Box.php';


$contenidoHtml = new ContenidoHtml();
$tipo_acceso_menu = $contenidoHtml->obtener_permisos_menu($_POST["hdd_numero_menu"]);

$variables = new Dbvariables();
$perfiles = new DbPerfiles();
$usuarios = new DbUsuarios();
$usuarios_perfiles = new DbUsuariosPerfiles();
$tiposcitas = new DbTiposCitas();
$combo = new Combo_Box();

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
        <script type='text/javascript' src='../js/jquery.js'></script>
        <script type='text/javascript' src='../js/funciones.js'></script>
        <script type='text/javascript' src='../js/ajax.js'></script>
        <script type='text/javascript' src='../js/jquery.validate.js'></script>
        <script type='text/javascript' src='tiempo_citas.js'></script>
    </head>
    <body>
        <?php
        $contenidoHtml->validar_seguridad(0);
        $contenidoHtml->cabecera_html();
        ?>
        <div class="title-bar">
            <div class="wrapper">
                <div class="breadcrumb">
                    <ul>
                        <li class="breadcrumb_on">Tiempo de citas</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="contenedor_principal volumen">
            <div class="padding">
                <div style="">
                        <table style="width:100%;">
                            <tr>
                                <td width="33%" align="center">
                                    <div id="d_cmb_perfil">
                                        <?php
                                        $combo->getComboDb("cmb_perfil", "", $perfiles->getPerfiles(), "id_perfil, descripcion", "Seleccione el Perfil", "usuarios_perfil()");
                                        ?>
                                    </div>
                                </td>
                                <td width="34%" align="center">
                                    <div id="d_cmb_usuarios">
                                        <?php
                                        $combo->getComboDb("cmb_usuarios", "", $usuarios_perfiles->getListaUsuariosIndAtiende(1), "id_usuario, nombre_completo", "Seleccione el Especialista", "");
                                        ?>
                                    </div>
                                </td>
                                <td width="33%" align="center">
                                    <div id="d_cmb_tiposcitas">
                                        <?php
                                        $combo->getComboDb("cmb_tiposcitas", "", $tiposcitas->getTiposcitas(), "id_tipo_cita, nombre_tipo_cita", "Seleccione el Tipo de Cita", "");
                                        ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    <div>
                        <input class="btnPrincipal peq" type="button" id="btnBuscar" value="Buscar" onclick="datagrid()" />
                        <?php
                        	if ($tipo_acceso_menu == 2) {
						?>
                        <input class="btnSecundario peq" type="button" id="btnNuevo" value="Nuevo" onclick="btnNuevo()" />
                        <?php
                        	}
						?>
                    </div>
                    <div class='contenedor_error' id='contenedor_error' style="height: 37px;"></div>
                    <div class='contenedor_exito' id='contenedor_exito' style="height: 37px;"></div>
                    <div id="d_datagrid" class="d_datagrid">  
                    </div>
                    <div class="update_d_datagrid" id="update_d_datagrid" >
                    </div>

                    <div id="formulario" class="formulario">
                        <form id="frmNuevotiempocitas">
                            <table width="100%">
                                <tr>
                                    <td colspan="2" style="text-align: center;"><h4>Asignar nuevo tiempo de cita</h4></td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width:50%">
                                        <label><b>Nombre de usuario:</b></label>
                                    </td>
                                    <td style="text-align: left;width: 50%;">
                                        <?php
                                        $combo->getComboDb("cmb_usuarios2", "", $usuarios_perfiles->getListaUsuariosIndAtiende(1), "id_usuario, nombre_completo", "Seleccione el Usuario", "");
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">
                                        <label><b>Tipo de cita:</b></label>
                                    </td>
                                    <td style="text-align: left;">
                                        <?php
                                        $combo->getComboDb("cmb_tiposcitas2", "", $tiposcitas->getTiposcitas(), "id_tipo_cita, nombre_tipo_cita", "Seleccione el Tipo de Cita", "");
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">
                                        <label><b>Tiempo (minutos):</b></label>
                                    </td>
                                    <td style="text-align: left;">
                                        <select class="select valid" id="cmbTiempo" name="cmbTiempo" style="width: 162px;">
                                            <option value="" >Seleccione el tiempo</option>
                                            <?php
												for ($i = 1; $i <= 10; $i++) {
											?>
											<option value="<?php echo($i); ?>"><?php echo($i); ?></option>
											<?php
												}
												
												for ($i = 15; $i <= 75; $i += 5) {
											?>
											<option value="<?php echo($i); ?>"><?php echo($i); ?></option>
											<?php
												}
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                    	<?php
                                        	if ($tipo_acceso_menu == 2) {
										?>
                                        <input type="submit" id="btnNuevo" class="btnPrincipal peq" value="Asignar Tiempo" onclick="nuevo_tiempo_citas()"/>
                                        <?php
											}
										?>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <div style="height: 17px; width: 100%;" id="d_guardar">
	                </div>
            </div>
        </div>
    </div>

    <?php
    $contenidoHtml->footer();
    ?>
</body>
</html>