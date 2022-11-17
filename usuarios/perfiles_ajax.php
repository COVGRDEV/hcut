<?php
session_start();
/*
  Pagina listado de perfiles, 
  Autor: Helio Ruber López - 16/09/2013
 */

header("Content-Type: text/xml; charset=UTF-8");

require_once("../db/DbPerfiles.php");
require_once("../db/DbMenus.php");
require_once("../funciones/Utilidades.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../principal/ContenidoHtml.php");

$dbPerfiles = new DbPerfiles();
$dbMenus = new DbMenus();
$utilidades = new Utilidades();
$contenido = new ContenidoHtml();
$contenido->validar_seguridad(1);
$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);

$opcion = $_POST["opcion"];

switch ($opcion) {
    case "1": //Formulario de creacion de perfiles
    $combo = new Combo_Box();
	$tipo_accion = "";
	if (isset($_POST["id_perfil"])) {
		$titulo_formulario = "Editar perfil";
		$id_perfil = $utilidades->str_decode($_POST["id_perfil"]);
		$tabla_perfil = $dbPerfiles->getUnPerfil($id_perfil);
		$tabla_permisos = $dbPerfiles->getPermisosMenus($id_perfil);
		$txt_nombre_perfil = $tabla_perfil["nombre_perfil"];
		$txt_desc_perfil = $tabla_perfil["descripcion"];
		$cmb_atiende_consulta = $tabla_perfil["ind_atiende"];
		$cmb_recibe_citas = $tabla_perfil["ind_citas"];
		$cmd_cirugia = $tabla_perfil["ind_cirugia"];
		$cmb_menu_inicio = $tabla_perfil["id_menu_inicio"];
		$cmb_registrar_pagos = $tabla_perfil["ind_registrar_pagos"];
		$cmb_modificar_pagos = $tabla_perfil["ind_modificar_pagos"];
		$cmb_modificar_autorizaciones = $tabla_perfil["ind_modificar_autorizaciones"];
		$cmd_estado = $tabla_perfil["ind_activo"];
		$tipo_accion = 2; //Editar Perfil
	} else {
		$tabla_permisos=array();
		$titulo_formulario = "Crear nuevo perfil";
		$txt_nombre_perfil = "";
		$txt_desc_perfil = "";
		$cmb_atiende_consulta = "";
		$cmb_recibe_citas = "";
		$cmd_cirugia = "";
		$cmb_menu_inicio = "";
		$cmb_registrar_pagos = "0";
		$cmb_modificar_pagos = "0";
		$cmb_modificar_autorizaciones = "0";
		$id_perfil = "";
		$tipo_accion = 1; //Crear Perfil
	}
	
	$lista_si_no = array();
	$lista_si_no[0][0] = "1";
	$lista_si_no[0][1] = "SI";
	$lista_si_no[1][0] = "0";
	$lista_si_no[1][1] = "NO";
    ?>
            <form id="frm_perfiles" name="frm_perfiles" method="post">
            <input type="hidden" value="0" name="hdd_exito" id="hdd_exito" />
            <input type="hidden" value="<?php echo $id_perfil;?>" name="hdd_id_perfil" id="hdd_id_perfil" />	
            
            <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:55em;">
                <tr><th colspan="2" align="center"><h3><?php echo $titulo_formulario; ?></h3></th></tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr valign="top">
                    <td align="right" style="width:20em;">
                        <label class="inline" for="txt_nombre_perfil">Nombre del perfil*</label>	
                    </td>
                    <td align="left">
                        <input type="text" class="input required" value="<?php echo $txt_nombre_perfil; ?>" name="txt_nombre_perfil" id="txt_nombre_perfil"  onblur="trim_cadena(this);" />
                    </td>
                </tr>
                <tr valign="top">
                    <td align="right">
                        <label class="inline" for="txt_desc_perfil">Descripci&oacute;n del perfil*</label>	
                    </td>
                    <td align="left">
                        <input type="text" class="input required" value="<?php echo $txt_desc_perfil; ?>" name="txt_desc_perfil" id="txt_desc_perfil"  onblur="trim_cadena(this);" />
                    </td>
                </tr>
                <tr valign="top">
                    <td align="right">
                        <label class="inline" for="cmb_atiende_consulta">Atiende consultas*</label>	
                    </td>
                    <td align="left">
                        <?php
							$combo->get("cmb_atiende_consulta", $cmb_atiende_consulta, $lista_si_no, "--Seleccione--", "seleccionar_atiende_consulta(this.value);", "", "width:350px;");
                        ?>
                    </td>	
                </tr>
                <tr valign="top">
                    <td align="right">
                        <label class="inline" for="cmb_recibe_citas">Recibe asignaci&oacute;n de citas*</label>	
                    </td>
                    <td align="left">
                        <?php
							$ind_activo_aux = "";
							if ($cmb_atiende_consulta != "") {
								$ind_activo_aux = intval($cmb_atiende_consulta, 10);
							}
							$combo->get("cmb_recibe_citas", $cmb_recibe_citas, $lista_si_no, "--Seleccione--", "", $ind_activo_aux, "width:350px;");
                        ?>
                    </td>	
                </tr>
                <tr valign="top">
                    <td align="right">
                        <label class="inline" for="cmb_atiende_cirugias">Atiende cirug&iacute;as*</label>	
                    </td>
                    <td align="left">
                        <?php
							$combo->get("cmb_atiende_cirugias", $cmd_cirugia, $lista_si_no, "--Seleccione--", "", "", "width:350px;");
                        ?>
                    </td>	
                </tr>
                <tr valign="top">
                    <td align="right">
                        <label class="inline" for="cmb_menu_inicio">Men&uacute; inicial</label>	
                    </td>
                    <td align="left">
                        <?php
							$lista_menus = $dbMenus->getListaMenusVisibles();
							$combo->getComboDb("cmb_menu_inicio", $cmb_menu_inicio, $lista_menus, "id_menu, nombre_completo", "--Seleccione--", "", "", "width:350px;");
                        ?>
                    </td>	
                </tr>
				<tr valign="top">
                    <td align="right">
                        <label class="inline" for="cmb_registrar_pagos">Registra pagos*</label>	
                    </td>
                    <td align="left">
                        <?php
							$combo->get("cmb_registrar_pagos", $cmb_registrar_pagos, $lista_si_no, "--Seleccione--", "", "", "width:350px;");
                        ?>
                    </td>	
                </tr>
				<tr valign="top">
                    <td align="right">
                        <label class="inline" for="cmb_modificar_pagos">Modificar conceptos en pagos*</label>	
                    </td>
                    <td align="left">
                        <?php
							$combo->get("cmb_modificar_pagos", $cmb_modificar_pagos, $lista_si_no, "--Seleccione--", "", "", "width:350px;");
                        ?>
                    </td>	
                </tr>
                <tr valign="top">
                    <td align="right">
                        <label class="inline" for="cmb_modificar_autorizaciones">Modificar autorizaciones*</label>	
                    </td>
                    <td align="left">
                        <?php
							$combo->get("cmb_modificar_autorizaciones", $cmb_modificar_autorizaciones, $lista_si_no, "--Seleccione--", "", "", "width:350px;");
                        ?>
                    </td>	
                </tr>
                <?php
	                if ($tipo_accion == 2) {
                ?>
				<tr valign="top">
                    <td align="right">
                        <label class="inline" for="cmb_activo">Activo*</label>	
                    </td>
                    <td align="left">
                        <?php
							$combo->get("cmb_activo", $cmd_estado, $lista_si_no, "--Seleccione--", "", "", "width:350px;");
                        ?>
                    </td>	
                </tr>
				<?php	
                	}
                ?>
                <tr valign="top">
                    <td align="center" colspan="2">
                        <h5>Permisos de menus</h5>	
                    </td>
                </tr>    
                <tr valign="top">    
                    <td align="center" colspan="2">
                    	<table id="tabla_menus"  border="0" class="paginated modal_table" style="width: 100%; margin: auto;">
				        	<thead>
				        	<tr class="headegrid">
								<th class="headegrid" align="center" >Menus</th>	
								<th class="headegrid" align="center" style="width: 180px;">Permisos</th>
							</tr>
							 </thead>
							<?php
							$lista_permiso = array();
							$lista_permiso[0][0] = "1";
							$lista_permiso[0][1] = "CONSULTA";
							$lista_permiso[1][0] = "2";
							$lista_permiso[1][1] = "COMPLETO";
							$tabla_menus = $dbMenus->getMenusRuta();
							$ids_menus = "";
							foreach($tabla_menus as $fila_menus){
								$id_menu = $fila_menus["id_menu"];
								$menu_principal = $fila_menus["nombre_menu"];
								$menu_padre2 = $fila_menus["nombre_2"];
								$menu_padre3 = $fila_menus["nombre_3"];
								$ruta_menu=$menu_principal;
								$ids_menus.=$id_menu."-";
								
								if($menu_padre2 <> ""){
									$ruta_menu=$menu_padre2."->".$ruta_menu; 
								}
								if($menu_padre3 <> ""){
									$ruta_menu=$menu_padre3."->".$ruta_menu;
								}
								
								$valor_permiso = "";
								if(count($tabla_permisos)>0){
									
									foreach($tabla_permisos as $fila_permisos)
									{
										$id_menu_permiso = $fila_permisos["id_menu"];
										$tipo_acceso = $fila_permisos["tipo_acceso"];
										if ($id_menu_permiso == $id_menu) {
								           $valor_permiso = $tipo_acceso;	
										}
									}
									
								}
								?>
								<tr class="celdagrid">
									<td align="left"><?php echo $ruta_menu;?></td>	
									<td align="left">
									<?php
									$combo->get("cmb_permiso_".$id_menu, $valor_permiso, $lista_permiso, "--Ninguno--", "", "", "width:170px;");
									?>	
									</td>
								</tr>
								<?php	
							}
							
							?>
						</table>	
						
						<script id="ajax">
						//<![CDATA[ 
						$(function() {
						    $(".paginated", "tabla_menus").each(function(i) {
						        $(this).text(i + 1);
						    });
						
						    $("table.paginated").each(function() {
						        var currentPage = 0;
						        var numPerPage = 5;
						        var $table = $(this);
						        $table.bind("repaginate", function() {
						            $table.find("tbody tr").hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
						        });
						        $table.trigger("repaginate");
						        var numRows = $table.find("tbody tr").length;
						        var numPages = Math.ceil(numRows / numPerPage);
						        var $pager = $('<div class="pager"></div>');
						        for (var page = 0; page < numPages; page++) {
						            $('<span class="page-number"></span>').text(page + 1).bind("click", {
						                newPage: page
						            }, function(event) {
						                currentPage = event.data["newPage"];
						                $table.trigger("repaginate");
						                $(this).addClass("active").siblings().removeClass("active");
						            }).appendTo($pager).addClass("clickable");
						        }
						        $pager.insertBefore($table).find("span.page-number:first").addClass("active");
						    });
						});
						//]]>
					</script>
                    </td>	
                </tr>
                <?php
                if($tipo_accion==2){
                ?>
                <tr valign="top">
	                <td align="center" colspan="2">
	                	<input type="hidden"  id="hdd_idmenus" nombre="hdd_idmenus" value="<?php echo $ids_menus;?>" />
                        <?php
                        	if ($tipo_acceso_menu == 2) {
						?>
	                	<input type="submit" id="btn_editar_perfil" nombre="btn_editar_perfil" value="Guardar" class="btnPrincipal" onclick="validar_editar_perfil();"/>
                        <?php
							}
						?>
	                	<input type="button" id="btn_cancelar" nombre="btn_cancelar" value="Cancelar" class="btnSecundario" onclick="cargar_perfiles();"/>
	                </td>
                </tr>
                
                <?php
				}else{
				?>
				
				<tr valign="top">
	                <td align="center" colspan="2">
	                	<input type="hidden"  id="hdd_idmenus" nombre="hdd_idmenus" value="<?php echo $ids_menus;?>" />
                        <?php
                        	if ($tipo_acceso_menu == 2) {
						?>
	                	<input type="submit" id="btn_crear_perfil" nombre="btn_crear_perfil" value="Crear" class="btnPrincipal" onclick="validar_crear_perfil();"/>
                        <?php
							}
						?>
	                	<input type="button" id="btn_cancelar" nombre="btn_cancelar" value="Cancelar" class="btnSecundario" onclick="cargar_perfiles();"/>
	                </td>
                </tr>
                <?php
				}
                ?>
            </table>
            <br />
            </form>
        <?php
        break;

    case "2": //Listado de los perfiles
        $tabla_perfiles = $dbPerfiles->getListaPerfiles();
        ?>
        <br/>
        <table class="paginated modal_table" style="width: 65%;  margin: auto;">
        	<thead>
        	<tr><th colspan="5">Perfiles de usuarios</th></tr>	
        	<tr>
				<th style="width: 5%;">Id</th>	
				<th style="width: 23%;">Perfil</th>	
				<th style="width: 42%;">Descripci&oacute;n</th>
				<th style="width: 15%;">Atiende consultas</th>
				<th style="width: 15%;">Estado</th>
			</tr>
			 </thead>
			<?php
			foreach ($tabla_perfiles as $fila_perfiles) {
				$id_perfil = $fila_perfiles["id_perfil"];
				$nombre_perfil = $fila_perfiles["nombre_perfil"];
				$descripcion = $fila_perfiles["descripcion"];
				$ind_activo = $fila_perfiles["ind_activo"];
				$ind_atiende = $fila_perfiles["ind_atiende"];
				
				if ($ind_activo == 1) {
					$class_estado = "activo";
					$texto_estado = "Activo";
				} else {
					$class_estado = "inactivo";
					$texto_estado = "Inactivo";
				}
				if ($ind_atiende == 1) {
					$ind_atiende = "SI";
				} else {
					$ind_atiende = "NO";
				}
				?>
				<tr onclick="cargar_formulario_editar(<?php echo $id_perfil;?>);" style="cursor: pointer;">
					<td align="center"><?php echo $id_perfil; ?></td>
					<td align="left"><?php echo $nombre_perfil; ?></td>
					<td align="left"><?php echo $descripcion; ?></td>
					<td align="center"><?php echo $ind_atiende; ?></td>
					<td align="center"><span class="<?php echo $class_estado; ?>"><?php echo $texto_estado;?></span></td>
				</tr>
				<?php	
			}
			?>
		</table>
        <br/>
		<script id="ajax">
			//<![CDATA[ 
			$(function() {
			    $(".paginated", "table").each(function(i) {
			        $(this).text(i + 1);
			    });
			
			    $("table.paginated").each(function() {
			        var currentPage = 0;
			        var numPerPage = 10;
			        var $table = $(this);
			        $table.bind("repaginate", function() {
			            $table.find("tbody tr").hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
			        });
			        $table.trigger("repaginate");
			        var numRows = $table.find("tbody tr").length;
			        var numPages = Math.ceil(numRows / numPerPage);
			        var $pager = $('<div class="pager"></div>');
			        for (var page = 0; page < numPages; page++) {
			            $('<span class="page-number"></span>').text(page + 1).bind("click", {
			                newPage: page
			            }, function(event) {
			                currentPage = event.data["newPage"];
			                $table.trigger("repaginate");
			                $(this).addClass("active").siblings().removeClass("active");
			            }).appendTo($pager).addClass("clickable");
			        }
			        $pager.insertBefore($table).find("span.page-number:first").addClass("active");
			    });
			});
			//]]>
		</script>
    	<?php
    	break;

    case "3": //Crear perfil
        $id_usuario = $_SESSION["idUsuario"];
		
		@$txt_nombre_perfil = $utilidades->str_decode($_POST["txt_nombre_perfil"]);
        @$txt_desc_perfil = $utilidades->str_decode($_POST["txt_desc_perfil"]);
        @$cmb_atiende_consulta = $utilidades->str_decode($_POST["cmb_atiende_consulta"]);
        @$cmb_recibe_citas = $utilidades->str_decode($_POST["cmb_recibe_citas"]);
        @$cmb_atiende_cirugias = $utilidades->str_decode($_POST["cmb_atiende_cirugias"]);
		@$cmb_menu_inicio = $utilidades->str_decode($_POST["cmb_menu_inicio"]);
		@$cmb_registrar_pagos = $utilidades->str_decode($_POST["cmb_registrar_pagos"]);
		@$cmb_modificar_pagos = $utilidades->str_decode($_POST["cmb_modificar_pagos"]);
		@$hdd_idmenus = $utilidades->str_decode($_POST["hdd_idmenus"]);
		
		$array_menus = explode("-", $hdd_idmenus);
		$menus_permisos = "";
		foreach ($array_menus as $fila_menus) {
			if ($fila_menus != "") {
			  $val_permiso = $_POST["cmb_permiso_".$fila_menus];
			  $menus_permisos = $menus_permisos.$fila_menus.",".$val_permiso."-";
			}
		}
		
		$valor_exito = $dbPerfiles->crearPerfil($txt_nombre_perfil, $txt_desc_perfil, $cmb_atiende_consulta, $cmb_recibe_citas,
				$cmb_atiende_cirugias, $cmb_menu_inicio, $id_usuario, $menus_permisos, $cmb_modificar_pagos, $cmb_registrar_pagos);
		?>
        <input type="hidden" value="<?php echo $valor_exito; ?>" name="hdd_exito" id="hdd_exito" />
		<?php
		break;

    case "4": //Editar perfil
        $id_usuario = $_SESSION["idUsuario"];
		
		@$hdd_id_perfil = $utilidades->str_decode($_POST["hdd_id_perfil"]);
    	@$txt_nombre_perfil = $utilidades->str_decode($_POST["txt_nombre_perfil"]);
        @$txt_desc_perfil = $utilidades->str_decode($_POST["txt_desc_perfil"]);
        @$cmb_atiende_consulta = $utilidades->str_decode($_POST["cmb_atiende_consulta"]);
        @$cmb_recibe_citas = $utilidades->str_decode($_POST["cmb_recibe_citas"]);
        @$cmb_atiende_cirugias = $utilidades->str_decode($_POST["cmb_atiende_cirugias"]);
		@$cmb_menu_inicio = $utilidades->str_decode($_POST["cmb_menu_inicio"]);
		@$cmb_registrar_pagos = $utilidades->str_decode($_POST["cmb_registrar_pagos"]);
		@$cmb_modificar_pagos = $utilidades->str_decode($_POST["cmb_modificar_pagos"]);
		@$cmb_activo = $utilidades->str_decode($_POST["cmb_activo"]);
		@$hdd_idmenus = $utilidades->str_decode($_POST["hdd_idmenus"]);
        @$cmb_modificar_autorizaciones = $utilidades->str_decode($_POST["cmb_modificar_autorizaciones"]);
		
		$array_menus = explode("-", $hdd_idmenus);
		$menus_permisos = "";
		foreach ($array_menus as $fila_menus) {
			if ($fila_menus != "") {
			  $val_permiso = $utilidades->str_decode($_POST["cmb_permiso_".$fila_menus]);
			  $menus_permisos = $menus_permisos.$fila_menus.",".$val_permiso."-";
			}
		}
		
		$valor_exito = $dbPerfiles->editarPerfil($hdd_id_perfil, $txt_nombre_perfil, $txt_desc_perfil, $cmb_atiende_consulta,
				$cmb_recibe_citas, $cmb_atiende_cirugias, $cmb_menu_inicio, $cmb_activo, $id_usuario, $menus_permisos, $cmb_modificar_pagos, 
				$cmb_registrar_pagos,$cmb_modificar_autorizaciones);
		?>
        <input type="hidden" value="<?php echo $valor_exito; ?>" name="hdd_exito" id="hdd_exito" />
		<?php
		break;
}
?>
