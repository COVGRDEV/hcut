<?php
session_start();
/*
  Autor: Juan Pablo Gomez Quiroga - 16/09/2013
 */

require_once '../db/DbUsuarios.php';
require_once '../funciones/Class_Combo_Box.php';
require_once '../db/DbUsuariosPerfiles.php';
require_once '../db/DbUsuarios.php';
require_once '../db/DbTiemposCitasProf.php';

$usuarios_perfiles = new DbUsuariosPerfiles();
$combo = new Combo_Box();
$usuarios = new DbUsuarios();
$tiempos_citas_prof = new DbTiemposCitasProf();

header('Content-Type: text/xml; charset=UTF-8');

require_once("../principal/ContenidoHtml.php");
require_once("../db/DbUsuarios.php");

$contenido = new ContenidoHtml();
$contenido->validar_seguridad(1);
$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);

$opcion = $_POST["opcion"];
$perfil = isset($_POST["perfil"]) ? $_POST["perfil"] : '';
$usuario = isset($_POST["usuario"]) ? $_POST["usuario"] : '';
$tipocitas = isset($_POST["tipocitas"]) ? $_POST["tipocitas"] : '';

//variables recibidas en el evento clic del datagrid
$idusuario = isset($_POST["idusuario"]) ? $_POST["idusuario"] : '';
$idtiponoticia = isset($_POST["idtiponoticia"]) ? $_POST["idtiponoticia"] : '';

//variables recibidas en el formulario de actualizar: tiempos citas
$idtipocita = isset($_POST['idtipocita']) ? $_POST['idtipocita'] : '';
$txtIdusuario = isset($_POST['txtIdusuario']) ? $_POST['txtIdusuario'] : '';
$cmbTiempo = isset($_POST['cmbTiempo']) ? $_POST['cmbTiempo'] : '';
$indactivo = isset($_POST['indactivo']) ? $_POST['indactivo'] : '';

switch ($opcion) {
    case "1": //Opcion para buscar usuarios

        if ($perfil == '') {
            $combo->getComboDb("cmb_usuarios", "", $usuarios_perfiles->getListaUsuariosIndAtiende(1), "id_usuario, nombre_completo", "Seleccione el Especialista", "");
        } else {
            $combo->getComboDb("cmb_usuarios", "", $usuarios_perfiles->getUsuarios($perfil), "id_usuario, nombre_completo", "Seleccione el Especialista", "");
        }

        break;

    case "2": //Opcion para crear el datagrid
		?>
        <table class="paginated modal_table">
            <thead>
                <tr>
                    <th style="width:40%;"><p>Usuario</p></th>
                    <th style="width:30%;"><p>Tipo de cita</p></th>  
                    <th style="width:15%;"><p>Tiempo</p></th> 
                    <th style="width:15%;"><p>Estado</p></th>
                </tr>
            </thead>
        	<tbody>
         	   <?php
					$rta = $tiempos_citas_prof->getTiemposcitasprofe2($perfil, $usuario, $tipocitas);
					
					if (count($rta) > 0) {
						$contAux = 0;
		                foreach ($rta as $value) {
				?>
                <tr onclick="update_datagrid(<?php echo $contAux; ?>)" id="<?php echo $contAux; ?>">
                	<td>
						<?php echo($value['nombre_usuario'] . " " . $value['apellido_usuario']); ?>
                        <p style="display: none"><?php echo $value['tcpIdtipocita']; ?></p>
                        <p style="display: none"><?php echo $value['tcpIdusuario']; ?></p>
					</td>
                    <td>
						<?php echo($value['nombre_tipo_cita']); ?>
                    </td>
                    <td>
                    	<?php echo($value['tiempo_tipo_cita']); ?>
                    </td>
                    <td>
                    	<?php
                        	if ($value['ind_activo'] == 1) {
						?>
                        <span class="activo">Activo</span>
                        <?php
							} else {
						?>
                        <span class="inactivo">No Activo</span>
                        <?php
							}
						?>
                    </td>
                </tr>
                <?php
							$contAux++;
						}
					} else {
				?>
                <tr>
                	<td colspan="4">No se encontraron resultados</td>
                </tr>
                <?php
					}
				?>
            </tbody>
        </table>
        <script type="text/javascript" id="ajax">
            //<![CDATA[ 
            $(function() {
                $('.paginated', 'table').each(function(i) {
                    $(this).text(i + 1);
                });

                $('table.paginated').each(function() {
                    var currentPage = 0;
                    var numPerPage = 10;
                    var $table = $(this);
                    $table.bind('repaginate', function() {
                        $table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
                    });
                    $table.trigger('repaginate');
                    var numRows = $table.find('tbody tr').length;
                    var numPages = Math.ceil(numRows / numPerPage);
                    var $pager = $('<div class="pager"></div>');
                    for (var page = 0; page < numPages; page++) {
                        $('<span class="page-number"></span>').text(page + 1).bind('click', {
                            newPage: page
                        }, function(event) {
                            currentPage = event.data['newPage'];
                            $table.trigger('repaginate');
                            $(this).addClass('active').siblings().removeClass('active');
                        }).appendTo($pager).addClass('clickable');
                    }
                    $pager.insertBefore($table).find('span.page-number:first').addClass('active');
                });
            });
            //]]>
        </script>
        <?php
            break;

        case "3":
            $perfil = '';
            $rta = $tiempos_citas_prof->getTiemposcitasprofe2($perfil, $idusuario, $idtiponoticia);
            ?>
        <form id="frmUpdatedatagrid">
            <table style="width: 100%;">
				<tr>
					<td colspan="2" style="text-align: center;"><h4>Modificar tiempo de cita</h4></td>
				</tr>
                <tr>
                    <td style="text-align: right; width: 50%;">
                        <input type="text" style="display: none;" name="txtIdtipocita" id="txtIdtipocita" disabled value="<?php echo $rta[0]['id_tipo_cita']; ?>" />
                    </td>
                    <td style="text-align: left; width: 50%;">
                        <input type="text" style="display: none;" id="txtIdusuario" name="txtIdusuario" disabled value="<?php echo $rta[0]['id_usuario']; ?>" />
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right;">
                        <label><b>Usuario:</b></label>
                    </td>
                    <td style="text-align: left;">
                        <input type="text" disabled value="<?php echo $rta[0]['nombre_usuario'] . " " . $rta[0]['apellido_usuario']; ?>" />
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right;">
                        <label><b>Tipo de Cita:</b></label>
                    </td>
                    <td style="text-align: left;">
                        <input type="text" disabled value="<?php echo $rta[0]['nombre_tipo_cita']; ?>" />
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
									$selected_aux = "";
									if ($i == $rta[0]['tiempo_tipo_cita']) {
										$selected_aux = "selected";
									}
							?>
							<option value="<?php echo($i); ?>" <?php echo($selected_aux); ?>><?php echo($i); ?></option>
							<?php
								}
								
								for ($i = 15; $i <= 75; $i += 5) {
									$selected_aux = "";
									if ($i == $rta[0]['tiempo_tipo_cita']) {
										$selected_aux = "selected";
									}
							?>
							<option value="<?php echo($i); ?>" <?php echo($selected_aux); ?>><?php echo($i); ?></option>
							<?php
								}
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right;">
                        <label><b>Activo:</b></label>
                    </td>
                    <td style="text-align: left;">
                        <?php if ($rta[0]['ind_activo'] == 1) { ?>
                            <input type="checkbox" name="indactivo" id="indactivo"  checked><br>
                        <?php } else { ?>
                            <input type="checkbox" name="indactivo" id="indactivo" ><br>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                    	<?php
                        	if ($tipo_acceso_menu == 2) {
						?>
                        <input type="submit" value="Guardar" class="btnPrincipal peq" onclick="update_datagrid_form_editar()" />
                        <?php
							}
						?>
                        <input type="button" value="Cancelar" class="btnSecundario peq" onclick="display_update_d_datagrid(0)" />
                    </td>     
                </tr>
            </table>
        </form>
        <?php
        break;

    case "4":
        //echo 'El registro de ha guardado de forma exitosa!';
        $tiempos_citas_prof->UpdateTiemposcitasprofe($idtipocita, $txtIdusuario, $cmbTiempo, $indactivo);
        break;


    case "5":
        $rta = $tiempos_citas_prof->CreateTiemposcitasprofe($txtIdusuario, $idtipocita, $cmbTiempo, 1);
        ?>
        <input type="hidden" name="hdd_rta_save" id="hdd_rta_save" value="<?php echo $rta; ?>" />
        <?php
        break;
}
?>