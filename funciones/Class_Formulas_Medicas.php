<?php
	/**
	 * Pagina para carga de fórmulas médicas predefinidas
	 * Autor: Helio Ruber López - 29/10/2014
	 */
	
	session_start();
	
	require_once("../db/DbFormulasMedicas.php");
	require_once("../funciones/Utilidades.php");
	
	$dbFormulasMedicas = new DbFormulasMedicas();
	$utilidades = new Utilidades();
	
	//Parámetros de entrada
	@$txt_id = $utilidades->str_decode($_POST["txt_id"]);
	@$id_convenio = intval($_POST["id_convenio"], 10);
	@$id_plan = intval($_POST["id_plan"], 10);
	@$cant_ciex = intval($_POST["cant_ciex"], 10);
	
	$arr_ciex = array();
	for ($i = 0; $i < $cant_ciex; $i++) {
		$arr_ciex[$i] = $utilidades->str_decode($_POST["cod_ciex_".$i]);
	}
	
	$lista_formulas = $dbFormulasMedicas->get_lista_formulas_hc($id_convenio, $id_plan, $arr_ciex);
	if (count($lista_formulas) > 0) {
?>
<input type="hidden" id="hdd_cant_formulas_prefed" value="<?php echo(count($lista_formulas)); ?>" />
<?php
		$i = 0;
		foreach ($lista_formulas as $formula_aux) {
			$nombre_convenio = $formula_aux["nombre_convenio"];
			if ($nombre_convenio != "") {
				if ($formula_aux["nombre_plan"] != "") {
					$nombre_convenio .= " - ".$formula_aux["nombre_plan"];
				} else {
					$nombre_convenio .= " - (Todos los planes)";
				}
			} else {
				$nombre_convenio = "(Todos los convenios)";
			}
			
			//Se obtiene el listado de textos
			$lista_textos = $dbFormulasMedicas->get_lista_formulas_texto($formula_aux["id_formula_med"]);
		?>
		<table class="modal_table" style="width:100%;">
			<tr>
    			<th align="center" colspan="3">
        			F&oacute;rmula m&eacute;dica No. <?php echo($i + 1); ?>:&nbsp;&nbsp;&nbsp;&nbsp;<?php echo($nombre_convenio); ?>
		        </th>
    		</tr>
	    	<input type="hidden" id="hdd_cant_textos_<?php echo($i); ?>" value="<?php echo(count($lista_textos)); ?>" />
			<?php
				$j = 0;
				foreach ($lista_textos as $texto_aux) {
			?>
		    <tr valign="top">
    			<td align="center" valign="middle" style="width:4%;">
        			<input type="checkbox" id="chk_texto_<?php echo($i."_".$j); ?>" class="input_margenes_eq"<?php if ($i == 0) { ?> checked="checked"<?php } ?> onchange="cambiar_check_texto(<?php echo($i); ?>, <?php echo($j); ?>);" />
		        </td>
    		    <td align="left" valign="middle" style="width:<?php if (count($lista_textos) > 1) { echo(76); } else { echo(96); } ?>%;">
                	<input type="hidden" id="hdd_texto_<?php echo($i."_".$j); ?>" value="<?php echo($texto_aux["desc_texto"]); ?>" />
        			<?php echo($texto_aux["desc_texto"]); ?>
	        	</td>
	    	    <?php
    	    		if ($j == 0 && count($lista_textos) > 1) {
				?>
    	    	<td align="left" valign="middle" style="width:20%;" rowspan="<?php echo(count($lista_textos)); ?>">
        			<input type="checkbox" id="chk_todos_<?php echo($i); ?>" class="input_margenes_eq"<?php if ($i == 0) { ?> checked="checked"<?php } ?> onchange="cambiar_check_todos(<?php echo($i); ?>);" />
	        		Seleccionar todos
		        </td>
    		    <?php
					}
				?>
    		</tr>
	    	<?php
					$j++;
				}
			?>
		</table>
		<br />
		<?php
			$i++;
		}
?>
<input type="button" id="btn_agregar_formulas" value="Agregar" class="btnPrincipal" onclick="agregar_formulas_medicas('<?php echo($txt_id); ?>');" />
&nbsp;&nbsp;
<input type="button" id="btn_agregar_formulas" value="Cancelar" class="btnSecundario" onclick="cerrar_div_centro();" />
<?php
	} else {
?>
<h6>
	<b>No se encontraron f&oacute;rmulas m&eacute;dicas predefinidas para los diagn&oacute;sticos seleccionados</b>
</h6>
<?php
	}
?>
