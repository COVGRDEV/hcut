<?php
	/*
	Pagina para crear diagnosticos generales 
	Autor: Helio Ruber López - 14/02/2014
	*/
	
	require_once("../db/DbListas.php");
	require_once("../db/DbDiagnosticos.php");
	require_once("Class_Combo_Box.php");
	
	class Class_Diagnosticos {
		public function getFormularioDiagnosticos($id_hc) {
			$listas = new DbListas();
			$combo = new Combo_Box();
			$diagnosticos = new DbDiagnosticos();
			$tabla_ojos = $listas->getListaDetalles(14);
			$tabla_hc_diagnosticos = $diagnosticos->getHcDiagnostico($id_hc);
			$cant_diagnosticos = count($tabla_hc_diagnosticos);
			
			if ($cant_diagnosticos <= 4) {
				$cant_diagnosticos = 4;
			}
	?>
	<input type="hidden" value="<?php echo($cant_diagnosticos);?>" name="lista_tabla" id="lista_tabla" />
	<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:90%;">
		<tr>
			<td align="center" style="width:20%;">
				<h7><b>C&oacute;digo CIEX</b></h7>
			</td>
			<td align="center" style="width:60%;" colspan="2">
				<h7><b>Diagn&oacute;stico</b></h7>
			</td>
			<td align="center" style="width:25%;">
				<h7><b>Ojo</b></h7>
			</td>
		</tr>
	</table>
	<div id='texto_diagnostico'></div>
	<?php
			for ($i = 1; $i <= 10; $i++) {
				$tabla_diagnosticos = $this->obtener_diagnostico($tabla_hc_diagnosticos, $i);
				if (count($tabla_diagnosticos) == 0) {
					$cod_ciex = '';
					$val_ojo = '';
					$texto_ciex = '';
				} else {
					$cod_ciex = $tabla_diagnosticos[0];
					$val_ojo = $tabla_diagnosticos[1];
					$texto_ciex = $tabla_diagnosticos[2];	
				}
	?>
	<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:90%; display:none;" id='tabla_diag_<?php echo($i);?>'>
		<tr>
			<td align="left" style="width:20%;">
				<input type="text" name="ciex_diagnostico_<?php echo($i);?>" id="ciex_diagnostico_<?php echo($i);?>" style="width:100px; margin:0;" maxlength="4" class="input" value="<?php echo($cod_ciex);?>" onblur="convertirAMayusculas(this);trim_cadena(this); buscar_diagnosticos_ciex('<?php echo($i);?>');" />
				<input type="hidden" name="hdd_ciex_diagnostico_<?php echo($i);?>" id="hdd_ciex_diagnostico_<?php echo($i);?>" value="<?php echo($cod_ciex);?>" />
			</td>
			<td align="left" style="width:80%;">
				<input type="text" name="txt_busca_diagnostico_<?php echo($i);?>" id="txt_busca_diagnostico_<?php echo($i);?>" style="margin:0;" value="<?php echo($texto_ciex);?>" onblur="cargar_diagnosticos('txt_busca_diagnostico_<?php echo($i);?>', '<?php echo($i);?>');" />
				<div id='texto_diagnostico_<?php echo($i);?>'></div>
			</td>
			<td align="left" style="width:20%;">
				<?php
					$combo->getComboDb('valor_ojos_'.$i, $val_ojo, $tabla_ojos, 'id_detalle, nombre_detalle', ' ', '', '', 'width:90px;margin:0;', '', 'select');
				?>
			</td>
		</tr>
	</table>
	<?php	    
			}
	?>	
	<table border="0" cellpadding="3" cellspacing="0" align="center" style="width:90%;">
	   	<tr>
	   		<td>
				<div class="agregar_alemetos" onclick="agregar_tabla_daig();"></div> 
				<div class="restar_alemetos" onclick="restar_tabla_daig();"></div>
	   		</td>
	   	</tr>
	</table>	
	<script>
		mostrar_lista_tabla();
	</script>
	<?php	
		}
		
		/**
		 * Se obtiene le diagnosticos a partir del array y el orden
		 */
		public function obtener_diagnostico($tabla_diagnostico_hc, $orden){
			$array_diagnosticos_hc = array();
			foreach ($tabla_diagnostico_hc as $fila_diagnostico_hc) {
				$cod_ciex = $fila_diagnostico_hc['cod_ciex'];
				$id_ojo = $fila_diagnostico_hc['id_ojo'];
				$orden_hc = $fila_diagnostico_hc['orden'];
				$texto_ciex = $fila_diagnostico_hc['nombre'];
				$array_diagnosticos_hc = array();
				if ($orden == $orden_hc) {
					$array_diagnosticos_hc[0]=$cod_ciex;
					$array_diagnosticos_hc[1]=$id_ojo;
					$array_diagnosticos_hc[2]=$texto_ciex;
					break;
				}
			}
			return $array_diagnosticos_hc;
		}
	}
?>
