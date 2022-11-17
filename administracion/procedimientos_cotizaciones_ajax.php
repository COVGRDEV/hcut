<?php
	session_start();
	/*
	  Pagina listado de usuarios, muestra los usuarios existentes, para modificar o crear uno nuevo
	  Autor: Helio Ruber López - 16/09/2013
	 */
	
	require_once("../db/DbProcedimientosCotizaciones.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbPerfiles.php");
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../principal/ContenidoHtml.php");
	
	$dbProcedimientosCotizaciones = new DbProcedimientosCotizaciones();
	$dbListas = new DbListas();
	$dbPefiles = new DbPerfiles();
	$utilidades = new Utilidades();
	$contenido = new ContenidoHtml();
	
	if (isset($_POST["hdd_numero_menu"])) {
		$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);
	}
	
	$opcion = $_POST["opcion"];
	if ($opcion != "4" && $opcion != "6") {
		header('Content-Type: text/xml; charset=UTF-8');
	}
	
	switch ($opcion) {
		case "1": //Opcion para buscar usuarios
			$txt_buscar_procedimiento = $utilidades->str_decode($_POST["txt_buscar_procedimiento"]);
			
			$lista_proc_cotiz = $dbProcedimientosCotizaciones->getListaProcedimientosCotizacionesNombre($txt_buscar_procedimiento);
		?>
        <br />
        <table class="paginated modal_table" style="width:50%; margin:auto;">
            <thead>
                <tr>
                	<th colspan='5'>Procedimientos</th>
                </tr>
                <tr>
                    <th style="width:10%;">Id</th>
                    <th style="width:70%;">Nombre</th>
                    <th style="width:20%;">Estado</th>
                </tr>
            </thead>
            <?php
				$i = 1;
				if (count($lista_proc_cotiz) > 0) {
					foreach ($lista_proc_cotiz as $proc_cotiz_aux) {
						@$estado = $proc_cotiz_aux['ind_activo'];
						if ($estado == 1) {
							$estado = 'Activo';
							$class_estado = 'activo';
						} else if ($estado == 0) {
							$estado = 'No Activo';
							$class_estado = 'inactivo';
						}
            ?>
            <tr style="cursor:pointer;" onclick="seleccionar_procedimiento('<?php echo($proc_cotiz_aux["id_proc_cotiz"]); ?>');">
                <td align="center"><?php echo($proc_cotiz_aux["id_proc_cotiz"]); ?></td>
                <td align="left"><?php echo($proc_cotiz_aux["nombre_proc_cotiz"]); ?></td>
                <td align="center"><span class="<?php echo($class_estado); ?>"><?php echo($estado); ?></span></td>
            </tr>
            <?php
						$i++;
					}
				} else {
            ?>
            <tr>
                <td align="center" colspan="3">No se encontraron datos</td>
            </tr>
            <?php
				}
            ?>
        </table>
        <script id='ajax'>
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
        </script>
        <?php
			break;
			
		case "2": //Opcion para crear el formulario de crear usuarios
			$combo = new Combo_Box();
			$tipo_accion = "";
			if (isset($_POST["id_proc_cotiz"])) {
				@$id_proc_cotiz = $utilidades->str_decode($_POST['id_proc_cotiz']);
				$proc_cotiz_obj = $dbProcedimientosCotizaciones->getProcedimientoCotizacion($id_proc_cotiz);
				$nombre_proc_cotiz = $proc_cotiz_obj["nombre_proc_cotiz"];
				$ind_activo = $proc_cotiz_obj["ind_activo"];
				$tipo_accion = 2;
				$titulo_formulario = "Editar procedimiento";
			} else {
				$id_proc_cotiz = "";
				$nombre_proc_cotiz = "";
				$ind_activo = "";
				$tipo_accion = 1;
				$titulo_formulario = "Crear nuevo procedimiento";
			}
        ?>
        <input type="hidden" name="hdd_id_proc_cotiz" id="hdd_id_proc_cotiz" value="<?php echo($id_proc_cotiz); ?>" />
        <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:52em;">
            <tr><th colspan="2" align="center"><h3><?php echo $titulo_formulario; ?></h3></th></tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr valign="top">
                <td align="right">
                    <label class="inline" for="txt_nombre_usuario">Nombre del procedimiento*</label>
                </td>
                <td align="left">
                    <input type="text" name="txt_nombre_proc_cotiz" id="txt_nombre_proc_cotiz" class="input" value="<?php echo($nombre_proc_cotiz); ?>" maxlength="100" style="width:300px;" onblur="trim_cadena(this);" />
                </td>
            </tr>
            <?php
				if ($tipo_accion == 2) {//Solo se muestra si se va a editar un nuevo usuario
					if ($ind_activo == 1) {
						$checked_estado = "checked";
					} else {
						$checked_estado = "";
					}
			?>
            <tr valign="top">
                <td align="right">
                    <label class="inline" for="txt_clave">Registro activo</label>	
                </td>
                <td align="left">
                    <label class="inline" >
                        <input type="checkbox" name="chk_activo" id="chk_activo" <?php echo($checked_estado); ?> />
                    </label>
                </td>
            </tr>
            <?php
				}
			?>
            <tr valign="top">
                <td colspan='2'>
                    <?php
						if ($tipo_accion == 1) {//Boton para crear usuario
							if ($tipo_acceso_menu == 2) {
					?>
                    <input class="btnPrincipal" type="button" id="btn_crear" nombre="btn_crear" value="Crear" onclick="validar_crear_procedimiento();"/>
                    <?php
							}
						} else if ($tipo_accion == 2) {//Boton para editar usuario
							if ($tipo_acceso_menu == 2) {
					?>
                    <input class="btnPrincipal" type="button" id="btn_editar" nombre="btn_editar" value="Guardar" onclick="validar_editar_procedimiento();"/>
                    <?php
							}
						}
					?>
                    <input class="btnSecundario" type="button" id="btn_cancelar" nombre="btn_cancelar" value="Cancelar" onclick="volver_inicio();"/>
                </td>
            </tr>
        </table>
        <?php
			break;
			
		case "3": //Opcion para editar procedimientos
			$id_usuario = $_SESSION["idUsuario"];
			@$id_proc_cotiz = $utilidades->str_decode($_POST["id_proc_cotiz"]);
			@$nombre_proc_cotiz = $utilidades->str_decode($_POST["nombre_proc_cotiz"]);
			@$ind_activo = $utilidades->str_decode($_POST["ind_activo"]);
			
			$resultado = $dbProcedimientosCotizaciones->actualizarProcedimientoCotizacion($id_proc_cotiz, $nombre_proc_cotiz, $ind_activo, $id_usuario);
		?>
        <input type="hidden" id="hdd_exito" value="<?php echo($resultado); ?>" />
        <?php
			break;
			
		case "4": //Opcion para crear nuevo procedimiento
			$id_usuario = $_SESSION["idUsuario"];
			@$nombre_proc_cotiz = $utilidades->str_decode($_POST["nombre_proc_cotiz"]);
			
			$resultado = $dbProcedimientosCotizaciones->crearProcedimientoCotizacion($nombre_proc_cotiz, $id_usuario);
		?>
        <input type="hidden" id="hdd_exito" value="<?php echo($resultado); ?>" />
        <?php
			break;
	}
?>
