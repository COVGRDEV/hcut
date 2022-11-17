<?php
session_start();
/*
  Pagina listado de usuarios, muestra los usuarios existentes, para modificar o crear uno nuevo
  Autor: Helio Ruber López - 16/09/2013
 */

header('Content-Type: text/xml; charset=UTF-8');

require_once("../db/DbFormulasMedicas.php");
require_once("../db/DbConvenios.php");
require_once("../db/DbPlanes.php");
require_once("../db/DbDiagnosticos.php");
require_once("../db/DbListas.php");
require_once("../funciones/Utilidades.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../principal/ContenidoHtml.php");

$dbFormulasMedicas = new DbFormulasMedicas();
$dbConvenios = new DbConvenios();
$dbPlanes = new DbPlanes();
$dbDiagnosticos = new DbDiagnosticos();
$dbListas = new DbListas();

$utilidades = new Utilidades();
$combo = new Combo_Box();
$contenido = new ContenidoHtml();

$contenido->validar_seguridad(1);
$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);

$opcion = $_POST["opcion"];

switch ($opcion) {
    case "1": //Búsqueda de fórmulas médicas
        $txt_buscar_formula = urldecode($_POST["txt_buscar_formula"]);
        $lista_formulas_medicas = $dbFormulasMedicas->get_lista_formulas_medicas_texto($txt_buscar_formula);
    ?>
    <table class="paginated modal_table" style="width:100%;">
        <thead>
            <tr>
                <th style="width:20%;">Convenio</th>
                <th style="width:20%;">Plan</th>
                <th style="width:50%;">Diagn&oacute;stico(s)</th>
                <th style="width:10%;">Estado</th>
            </tr>
        </thead>
        <?php
			if (count($lista_formulas_medicas) > 0) {
				$i = 1;
				foreach ($lista_formulas_medicas as $formula_aux) {
					$nombre_convenio = $formula_aux["nombre_convenio"];
					if ($nombre_convenio == "") {
						$nombre_convenio = "(Todos los convenios)";
					}
					
					$nombre_plan = $formula_aux["nombre_plan"];
					if ($nombre_plan == "") {
						$nombre_plan = "(Todos los planes)";
					}
					
					$estado = "";
					if ($formula_aux["ind_activo"] == "1") {
						$estado = "Activo";
						$class_estado = "activo";
					} else {
						$estado = "No Activo";
						$class_estado = "inactivo";
					}
					
					$lista_formulas_ciex = $dbFormulasMedicas->get_lista_formulas_ciex($formula_aux["id_formula_med"]);
		?>
        <tr style="cursor:pointer;" onclick="seleccionar_formula(<?php echo($i); ?>);">
        	<td align="left" valign="middle">
            	<input type="hidden" id="hdd_id_formula_med_<?php echo($i); ?>" value="<?php echo($formula_aux["id_formula_med"]); ?>" />
				<?php echo($nombre_convenio) ?>
            </td>
            <td align="left" valign="middle"><?php echo($nombre_plan); ?></td>
            <td align="left" valign="middle">
            	<?php
					for ($j = 0; $j < count($lista_formulas_ciex); $j++) {
						$ciex_aux = $lista_formulas_ciex[$j];
						if ($j > 0) {
							echo("<br />");
						}
						echo($ciex_aux["cod_ciex"]." - ".$ciex_aux["nombre_ciex"]);
					}
				?>
            </td>
            <td align="center" valign="middle"><span class="<?php echo $class_estado; ?>"><?php echo($estado); ?></span></td>
        </tr>
        <?php
					$i++;
				}
			} else {
		?>
        <tr>
        	<td align="center" colspan="4">No se encontraron registros</td>
        </tr>
        <?php
			}
		?>
    </table>
    <script id='ajax'>
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
		
    case "2": //Formulario de creación/edición de fórmulas médicas
        $tipo_accion = "";
		$id_formula_med = "";
        if (isset($_POST["id_formula_med"])) {
			$id_formula_med = intval($_POST["id_formula_med"], 10);
            $formula_medica_obj = $dbFormulasMedicas->get_formula_medica($id_formula_med);
            
			$lista_formulas_ciex = $dbFormulasMedicas->get_lista_formulas_ciex($id_formula_med);
			$lista_formulas_texto = $dbFormulasMedicas->get_lista_formulas_texto($id_formula_med);
            $tipo_accion = 2; //Edición
            $titulo_formulario = "Editar f&oacute;rmula m&eacute;dica";
			$id_convenio = $formula_medica_obj["id_convenio"];
			$id_plan = $formula_medica_obj["id_plan"];
        } else {
            $formula_medica_obj = array();
			$lista_formulas_ciex = array();
			$lista_formulas_texto = array();
            $tipo_accion = 1; //Creación
            $titulo_formulario = "Crear nueva f&oacute;rmula m&eacute;dica";
			$id_convenio = "";
			$id_plan = "";
        }
        ?>
        <form id="frm_formula_medica" name="frm_formula_medica" method="post">
            <input type="hidden" id="hdd_id_formula_med" value="<?php echo($id_formula_med); ?>" />
            <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:52em;">
                <tr>
                	<th colspan="2" align="center"><h3><?php echo($titulo_formulario); ?></h3></th>
                </tr>
                <tr><td style="height:10px;"></td></tr>
                <tr valign="top">
                    <td align="right" style="width:15%;">
                        <label class="inline" for="cmb_convenio">Convenio</label>	
                    </td>
                    <td align="left" style="width:85%;">
                    	<?php
                        	$lista_convenios = $dbConvenios->getListaConveniosActivos();
							$combo->getComboDb("cmb_convenio", $id_convenio, $lista_convenios, "id_convenio, nombre_convenio", "(Todos los convenios)", "seleccionar_convenio(this.value);", true, "width:100%;");
						?>
                    </td>
                </tr>
                <tr valign="top">
                    <td align="right">
                        <label class="inline" for="cmb_plan">Plan</label>	
                    </td>
                    <td align="left">
                    	<div id="d_planes">
	                    	<?php
    	                    	$lista_planes = array();
								if ($id_convenio != "") {
									$lista_planes = $dbPlanes->getListaPlanesActivos($id_convenio);
								}
								$combo->getComboDb("cmb_plan", $id_plan, $lista_planes, "id_plan, nombre_plan", "(Todos los planes)", "", true, "width:100%;");
							?>
                        </div>
                    </td>
                </tr>
                <?php
                	if ($tipo_accion == 2) {
				?>
                <tr valign="top">
                	<td align="right">
                    	<label class="inline" for="chk_activo">Activo</label>	
                    </td>
                    <td align="left">
                    	<label class="inline">
	                    	<input type="checkbox" id="chk_activo"<?php if ($formula_medica_obj["ind_activo"] == "1") { ?> checked="checked"<?php } ?> />
                        </label>
                    </td>
                </tr>
                <?php
					}
				?>
                <tr>
                	<td colspan="2" align="center">
                    	<input type="hidden" id="hdd_cont_cx" value="<?php echo(count($lista_formulas_ciex)); ?>" />
                        <table class="modal_table" style="width:100%;">
                        	<tr>
                            	<th align="center" colspan="2" style="width:90%;">Diagn&oacute;sticos Asociados</th>
                                <th align="center" style="width:10%;">
                                	<div class="Add-icon full" onclick="agregar_diagnostico();" title="Agregar diagn&oacute;stico"></div>
                                </th>
                            </tr>
                            <?php
                            	$cont_cx = 0;
								if (count($lista_formulas_ciex)) {
									foreach ($lista_formulas_ciex as $ciex_aux) {
							?>
                            <tr id="tr_cx_<?php echo($cont_cx); ?>">
                            	<td align="center" style="width:10%;">
	                            	<img style="cursor:pointer;" src="../imagenes/Search-icon.png" onclick="mostrar_buscar_diagnostico(<?php echo($cont_cx); ?>);" title="Buscar diagn&oacute;stico" />
                                    <input type="hidden" id="hdd_cod_ciex_<?php echo($cont_cx); ?>" value="<?php echo($ciex_aux["cod_ciex"]); ?>" />
                                </td>
                            	<td align="left" id="td_cx_<?php echo($cont_cx); ?>" style="width:80%;">
                                	<?php echo($ciex_aux["cod_ciex"]." - ".$ciex_aux["nombre_ciex"]); ?>
                                </td>
                            	<td align="center" style="width:10%;">
	                                <div class="Error-icon" onclick="borrar_diagnostico(<?php echo($cont_cx); ?>);" title="Borrar diagn&oacute;stico"></div>
                                </td>
                            </tr>
                            <?php
										$cont_cx++;
									}
								}
								
								//se agregan más filas de diagnósticos
								for ($i = $cont_cx; $i < 50; $i++) {
							?>
                            <tr id="tr_cx_<?php echo($i); ?>" style="display:none;">
                            	<td align="center" style="width:10%;">
	                            	<img style="cursor:pointer;" src="../imagenes/Search-icon.png" onclick="mostrar_buscar_diagnostico(<?php echo($i); ?>);" title="Buscar diagn&oacute;stico" />
                                    <input type="hidden" id="hdd_cod_ciex_<?php echo($i); ?>" value="" />
                                </td>
                            	<td align="left" id="td_cx_<?php echo($i); ?>" style="width:80%;"></td>
                            	<td align="center" style="width:10%;">
	                                <div class="Error-icon" onclick="borrar_diagnostico(<?php echo($i); ?>);" title="Borrar diagn&oacute;stico"></div>
                                </td>
                            </tr>
                            <?php
								}
							?>
                        </table>
                    </td>
                </tr>
                <tr><td style="height:10px;"></td></tr>
                <tr>
                	<td colspan="2" align="center">
                    	<input type="hidden" id="hdd_cont_ftexto" value="<?php echo(count($lista_formulas_texto)); ?>" />
                        <table class="modal_table" style="width:100%;">
                        	<tr>
                            	<th align="center" style="width:90%;">Detalle de la F&oacute;rmula M&eacute;dica</th>
                                <th align="center" style="width:10%;">
                                	<div class="Add-icon full" onclick="agregar_ftexto();" title="Agregar detalle"></div>
                                </th>
                            </tr>
                            <?php
                            	$cont_ftexto = 0;
								if (count($lista_formulas_texto)) {
									foreach ($lista_formulas_texto as $ftexto_aux) {
							?>
                            <tr id="tr_ftexto_<?php echo($cont_ftexto); ?>">
                            	<td align="left">
                                	<input type="text" id="txt_desc_texto_<?php echo($cont_ftexto); ?>" value="<?php echo($ftexto_aux["desc_texto"]); ?>" class="input" style="width:100%; margin:0;" />
                                </td>
                            	<td align="center">
	                                <div class="Error-icon" onclick="borrar_ftexto(<?php echo($cont_ftexto); ?>);" title="Borrar detalle"></div>
                                </td>
                            </tr>
                            <?php
										$cont_ftexto++;
									}
								}
								
								//se agregan más filas de diagnósticos
								for ($i = $cont_ftexto; $i < 50; $i++) {
							?>
                            <tr id="tr_ftexto_<?php echo($i); ?>" style="display:none;">
                            	<td align="left">
                                	<input type="text" id="txt_desc_texto_<?php echo($i); ?>" value="" class="input" style="width:100%; margin:0;" />
                                </td>
                            	<td align="center">
	                                <div class="Error-icon" onclick="borrar_ftexto(<?php echo($i); ?>);" title="Borrar detalle"></div>
                                </td>
                            </tr>
                            <?php
								}
							?>
                        </table>
                    </td>
                </tr>
                <tr><td style="height:10px;"></td></tr>
                <tr valign="top">
                	<td colspan="2">
                    	<?php
							if ($tipo_acceso_menu == 2) {
								if ($tipo_accion == 1) {//Boton para crear
						?>
						<input class="btnPrincipal" type="button" id="btn_crear" nombre="btn_crear" value="Crear" onclick="crear_formula();"/>
                        &nbsp;&nbsp;
						<?php
								} else if ($tipo_accion == 2) {//Boton para editar
						?>
						<input class="btnPrincipal" type="button" id="btn_editar" nombre="btn_editar" value="Guardar" onclick="editar_formula();"/>
                        &nbsp;&nbsp;
						<?php
								}
							}
						?>
                        <input class="btnSecundario" type="button" id="btn_cancelar" nombre="btn_cancelar" value="Cancelar" onclick="volver_inicio();" />
                    </td>
                </tr>
            </table>
        </form>
        <?php
        break;

    case "3": //Listado de planes
        @$id_convenio = intval($_POST["id_convenio"], 10);
		
		$lista_planes = $dbPlanes->getListaPlanesActivos($id_convenio);
		$combo->getComboDb("cmb_plan", "", $lista_planes, "id_plan, nombre_plan", "(Todos los planes)", "", true, "width:100%;");
        break;
		
    case "4": //Búsqueda de diagnósticos
			@$indice = $utilidades->str_decode($_POST["indice"]);
			@$txt_busca_diagnostico = $utilidades->str_decode($_POST["txt_busca_diagnostico"]);
			@$tabla_diagnosticos = $dbDiagnosticos->getListaDiagnosticosDetParam($txt_busca_diagnostico);
        ?>
        <br />
        <table class="paginated modal_table" style="width: 80%; margin: auto;">
        	<thead>
            	<tr><th colspan="2">Diagnosticos CIEX</th></tr>
                <tr>
                    <th style="width:5%;">C&oacute;digo CIEX</th>
                    <th style="width:36%;">Nombre</th>
                </tr>
            </thead>
            <?php
				if (count($tabla_diagnosticos) > 0) {
					foreach ($tabla_diagnosticos as $fila_ciex) {
						@$cod_ciex = $fila_ciex["codciex"];
						@$nombre_ciex = $fila_ciex["nombre"];
            ?>
            <tr style="cursor:pointer;" onclick="seleccionar_diagnostico_ciex('<?php echo $cod_ciex; ?>', '<?php echo $nombre_ciex; ?>', '<?php echo($indice); ?>');">
            	<td align="center"><?php echo $cod_ciex; ?></td>
                <td align="left"><?php echo $nombre_ciex; ?></td>
            </tr>
            <?php
					}
				} else {
			?>
            <tr>
            	<td align="center" colspan="2">No se encontraron datos</td>
            </tr>
            <?php
				}
			?>
        </table>
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
        <br />
        <?php
        break;
		
    case "5": //Crear fórmula médica
        $id_usuario = $_SESSION["idUsuario"];
		@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
		@$id_plan = $utilidades->str_decode($_POST["id_plan"]);
		
		@$cant_cx = intval($_POST["cant_cx"], 10);
		$arr_ciex = array();
		for ($i = 0; $i < $cant_cx; $i++) {
			$arr_ciex[$i] = $utilidades->str_decode($_POST["cod_ciex_".$i]);
		}
		
		@$cant_ftexto = intval($_POST["cant_ftexto"], 10);
		$arr_ftexto = array();
		for ($i = 0; $i < $cant_ftexto; $i++) {
			$arr_ftexto[$i] = $utilidades->str_decode($_POST["desc_texto_".$i]);
		}
		
		$resultado_aux = $dbFormulasMedicas->crear_formula_medica($id_convenio, $id_plan, 1, $arr_ciex, $arr_ftexto, $id_usuario);
	?>
    <input type="hidden" id="hdd_resul_guardar_formula" value="<?php echo($resultado_aux); ?>" />
	<?php
		break;
		
    case "6": //Editar fórmula médica
        $id_usuario = $_SESSION["idUsuario"];
		@$id_formula_med = $utilidades->str_decode($_POST["id_formula_med"]);
		@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
		@$id_plan = $utilidades->str_decode($_POST["id_plan"]);
		@$ind_activo = $utilidades->str_decode($_POST["ind_activo"]);
		
		@$cant_cx = intval($_POST["cant_cx"], 10);
		$arr_ciex = array();
		for ($i = 0; $i < $cant_cx; $i++) {
			$arr_ciex[$i] = $utilidades->str_decode($_POST["cod_ciex_".$i]);
		}
		
		@$cant_ftexto = intval($_POST["cant_ftexto"], 10);
		$arr_ftexto = array();
		for ($i = 0; $i < $cant_ftexto; $i++) {
			$arr_ftexto[$i] = $utilidades->str_decode($_POST["desc_texto_".$i]);
		}
		
		$resultado_aux = $dbFormulasMedicas->editar_formula_medica($id_formula_med, $id_convenio, $id_plan, $ind_activo, $arr_ciex, $arr_ftexto, $id_usuario);
	?>
    <input type="hidden" id="hdd_resul_guardar_formula" value="<?php echo($resultado_aux); ?>" />
	<?php
        break;


    case "7": //Resetea la contarseña del usuario

        $idUsuario = $_POST['id_usuario'];

        $rta_aux = $dbUsuarios->resetearPass($idUsuario);

        echo $rta_aux;

        break;
}
?>