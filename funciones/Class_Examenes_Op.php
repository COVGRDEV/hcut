<?php
	/**
	 * Pagina para registrar exámenes de optometría
	 * Autor: Helio Ruber López - 27/03/2014
	 */
	
	require_once("../db/DbListas.php");
	require_once("../db/DbMaestroExamenes.php");
	require_once("../db/DbExamenesOptometria.php");
	require_once("../db/DbVariables.php");
	require_once("Class_Combo_Box.php");
	require_once("Utilidades.php");
	
	class Class_Examenes_Op {
		public function getFormularioExamenesOp($id_hc, $id_admision) {
			$combo = new Combo_Box();
			$utilidades = new Utilidades();
			
			$dbVariables = new Dbvariables();
			
			//Se borran las imágenes temporales creadas por el usuario actual
			$ruta_tmp = "../historia_clinica/tmp/".$_SESSION["idUsuario"];
			/*if (file_exists($ruta_tmp)) {
				@array_map("unlink", glob($ruta_tmp."/img*.*"));
			}*/
			
			@mkdir($ruta_tmp);
			
			//Se obtiene la ruta actual de las imágenes
			$arr_ruta_base = $dbVariables->getVariable(17);
			$ruta_base = $arr_ruta_base["valor_variable"];
			
			$dbListas = new DbListas();
			$dbMaestroExamenes = new DbMaestroExamenes();
			$dbExamenesOptometria = new DbExamenesOptometria();
			$lista_ojos = $dbListas->getListaDetalles(14);
			$lista_examenes_optometria_hc = $dbExamenesOptometria->get_lista_examenes_optometria_hc($id_hc);
			$cant_examenes_op = count($lista_examenes_optometria_hc);
			if ($cant_examenes_op == 0) {
				$cant_examenes_op++;
			}
			
			//Se consultan las direcciones de las imágenes existentes
			$lista_examenes_optometria_hc_det = $dbExamenesOptometria->get_lista_examenes_optometria_hc_det($id_hc);
			$arr_examenes_optometria_hc_det = array();
			if (count($lista_examenes_optometria_hc_det) > 0) {
				foreach ($lista_examenes_optometria_hc_det as $registro_aux) {
					if (isset($arr_examenes_optometria_hc_det[$registro_aux["id_examen_hc"]])) {
						array_push($arr_examenes_optometria_hc_det[$registro_aux["id_examen_hc"]], $registro_aux);
					} else {
						$arr_examenes_optometria_hc_det[$registro_aux["id_examen_hc"]][0] = $registro_aux;
					}
				}
			}
			
			//Se obtienen los listados
			$lista_examenes = $dbMaestroExamenes->get_lista_examenes(1);
			$lista_ojos = $dbListas->getListaDetalles(14);
	?>
    <div id="d_borrar_archivo_examen" style="display:none;"></div>
    <input type="hidden" name="hdd_cant_examenes_op" id="hdd_cant_examenes_op" value="<?php echo($cant_examenes_op);?>" />
    <input type="hidden" name="hdd_id_hc_consulta" id="hdd_id_hc_consulta" value="<?php echo($id_hc); ?>" />
    <input type="hidden" name="hdd_id_admision" id="hdd_id_admision" value="<?php echo($id_admision); ?>" />
	<table border="0" cellpadding="3" cellspacing="0" align="center" style="width:95%;">
	   	<tr>
        	<td style="width:15%;">
            	Examen:&nbsp;
                <select name="cmb_num_examen" id="cmb_num_examen" onchange="mostrar_examen(this.value);">
                	<?php
                    	for ($i = 0; $i < count($lista_examenes_optometria_hc); $i++) {
					?>
                	<option value="<?php echo($i); ?>"><?php echo($i + 1); ?></option>
                    <?php
						}
					?>
                </select>
            </td>
	   		<td style="width:85%;">
				<div class="agregar_alemetos" onclick="agregar_tabla_examen();" title="Agregar examen"></div> 
				<div class="restar_alemetos" onclick="restar_tabla_examen();" title="Borrar examen"></div>
	   		</td>
	   	</tr>
	</table>
	<table border="0" cellpadding="0" cellspacing="0" align="center" style="width:100%;">
		<?php
			for ($i = 0; $i < 20; $i++) {
				$id_examen_hc = "";
				$id_examen = "";
				$id_ojo = "";
				$id_examen_compl = "";
				$observaciones_examen = "";
				$pu_od = "";
				$pu_oi = "";
				$ruta_arch_examen = "";
				
				$examen_aux = $this->obtener_examen($lista_examenes_optometria_hc, $i);
				if (count($examen_aux) > 0) {
					$id_examen_hc = $examen_aux["id_examen_hc"];
					$id_examen = $examen_aux["id_examen"];
					$id_ojo = $examen_aux["id_ojo"];
					$id_examen_compl = $examen_aux["id_examen_compl"];
					$observaciones_examen = $examen_aux["observaciones_examen"];
					$pu_od = $examen_aux["pu_od"];
					$pu_oi = $examen_aux["pu_oi"];
					
					$ruta_arch_examen = $examen_aux["ruta_arch_examen"];
					if ($ruta_arch_examen != "") {
						$ruta_arch_examen = str_replace("../imagenes/imagenes_hce", $ruta_base, $ruta_arch_examen);
					}
				}
		?>
        <tr id="tr_examen_<?php echo($i); ?>">
        	<td>
            	<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%;">
                	<tr>
			        	<td align="right" style="width:15%;">
    			        	<label class="inline" for="cmb_examen_<?php echo($i); ?>">Tipo de examen</label>
                        </td>
                        <td align="left" style="width:55%;">
                            <?php
                                echo($combo->getComboDb("cmb_examen_".$i, $id_examen, $lista_examenes, "id_examen,nombre_examen", "Seleccione un examen", "seleccionar_examen_op(this.value, ".$i.");", true, "width:350px;"));
                            ?>
                            <div id="d_examen_compl_b_<?php echo($i); ?>" style="display:none;"></div>
                        </td>
			        	<td align="right" style="width:10%;">
    			        	<label class="inline" for="cmb_examen_<?php echo($i); ?>">Ojo</label>
                        </td>
                        <td align="left" style="width:20%;">
                            <?php
                                echo($combo->getComboDb("cmb_ojo_examen_".$i, $id_ojo, $lista_ojos, "id_detalle,nombre_detalle", "Seleccione", "", true, "width:120px;"));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="left" colspan="4">
                        	<?php
                            	if (isset($arr_examenes_optometria_hc_det[$id_examen_hc]) && count($arr_examenes_optometria_hc_det[$id_examen_hc]) > 0) {
									for ($j = 0; $j < count($arr_examenes_optometria_hc_det[$id_examen_hc]); $j++) {
										$registro_aux = $arr_examenes_optometria_hc_det[$id_examen_hc][$j];
										$ruta_arch_examen_aux = $registro_aux["ruta_arch_examen"];
										if ($ruta_arch_examen_aux != "") {
											$ruta_arch_examen_aux = str_replace("../imagenes/imagenes_hce", $ruta_base, $ruta_arch_examen_aux);
										}
							?>
                            <input type="hidden" name="hdd_ruta_arch_examen_<?php echo($i."_".$j); ?>" id="hdd_ruta_arch_examen_<?php echo($i."_".$j); ?>" value="<?php echo($ruta_arch_examen_aux); ?>" />
                            <?php
									}
								}
							?>
                            <form name="frm_arch_examen_<?php echo($i); ?>" id="frm_arch_examen_<?php echo($i); ?>" target="ifr_carga_arch_<?php echo($i); ?>" action="../funciones/Class_Examenes_Op_ajax.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="opcion" id="opcion" value="1" />
                                <input type="hidden" name="hdd_numero_menu" id="hdd_numero_menu" value="<?php echo($i); ?>" />
                                <input type="hidden" name="hdd_indice_examen" id="hdd_indice_examen" value="<?php echo($i); ?>" />
                                <input type="hidden" name="hdd_id_examen_hc_<?php echo($i); ?>" id="hdd_id_examen_hc_<?php echo($i); ?>" value="<?php echo($id_examen_hc); ?>" />
                                <input type="hidden" name="hdd_id_examen_sel_<?php echo($i); ?>" id="hdd_id_examen_sel_<?php echo($i); ?>" value="<?php echo($id_examen); ?>" />
                                <input type="hidden" name="hdd_id_ojo_sel_<?php echo($i); ?>" id="hdd_id_ojo_sel_<?php echo($i); ?>" value="<?php echo($id_ojo); ?>" />
                                <input type="hidden" name="hdd_id_hc_consulta_<?php echo($i); ?>" id="hdd_id_hc_consulta_<?php echo($i); ?>" value="<?php echo($id_hc); ?>" />
                                <input type="hidden" name="hdd_id_admision_<?php echo($i); ?>" id="hdd_id_admision_<?php echo($i); ?>" value="<?php echo($id_admision); ?>" />
                                <input type="hidden" name="hdd_ind_actualizar_<?php echo($i); ?>" id="hdd_ind_actualizar_<?php echo($i); ?>" value="0" />
                                <div id="d_input_carga_examenes_<?php echo($i); ?>"></div>
                                <script type="text/javascript">
									<?php
										$ind_mostrar = 0;
										if (!isset($arr_examenes_optometria_hc_det[$id_examen_hc]) || count($arr_examenes_optometria_hc_det[$id_examen_hc]) == 0) {
											$ind_mostrar = 1;
										}
									?>
									cargar_componentes_carga(<?php echo($i); ?>, <?php echo($ind_mostrar); ?>);
                                </script>
                            </form>
                            <div style="display:none;">
                                <iframe name="ifr_carga_arch_<?php echo($i); ?>" id="ifr_carga_arch_<?php echo($i); ?>"></iframe>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="4">
                            <div id="d_archivo_examen_<?php echo($i); ?>" class="div_marco" style="height:450px; position:relative;"></div>
                            <script type="text/javascript">
                                cargar_archivo(<?php echo($i); ?>, 0);
                            </script>
                        </td>
                    </tr>
                    <?php
                    	$visible_pu = "display:none;"; //Paquimetría ultrasónica
						switch ($id_examen_compl) {
							case "344": //Paquimetría ultrasónica
								$visible_pu = "";
								break;
						}
					?>
                    <tr id="tr_pu_<?php echo($i); ?>" style="<?php echo($visible_pu); ?>">
                    	<td align="center" colspan="4">
                        	<table style="width:100%;">
                                <tr>
                                	<td align="right" style="width:20%">
                                    	<label>OD:</label>
                                    </td>
                                    <td align="center" style="width:5%;">
                                    	<input type="text" id="txt_pu_od_<?php echo($i); ?>" name="txt_pu_od_<?php echo($i); ?>" value="<?php echo($pu_od); ?>" maxlength="3" onkeypress="return solo_numeros(event, false);" class="no-margin" />
                                    </td>
                                    <td align="left" style="width:15%;">
                                    	<label>&mu;m</label>
                                    </td>
                                	<td align="center" style="width:20%;">
                                    	<h6>Paquimetr&iacute;a ultras&oacute;nica</h6>
                                    </td>
                                	<td align="right" style="width:15%;">
                                    	<label>OI:</label>
                                    </td>
                                    <td align="center" style="width:5%;">
                                    	<input type="text" id="txt_pu_oi_<?php echo($i); ?>" name="txt_pu_oi_<?php echo($i); ?>" value="<?php echo($pu_oi); ?>" maxlength="3" onkeypress="return solo_numeros(event, false);" class="no-margin" />
                                    </td>
                                    <td align="left" style="width:20%;">
                                    	<label>&mu;m</label>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="4">
                            <h6>Observaciones</h6>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="4">
                        	<div id="txt_observaciones_examen_<?php echo($i); ?>"><?php echo($utilidades->ajustar_texto_wysiwyg($observaciones_examen)); ?></div>
                        </td>
                    </tr>
		        </table>
        	</td>
        </tr>
		<?php	    
			}
		?>
	</table>
    <script>
		mostrar_lista_tabla();
		<?php
			for ($i = 0; $i < 20; $i++) {
		?>
		initCKEditorExamen(<?php echo($i); ?>);
		<?php
			}
		?>
		
		setTimeout(function() {
			for (var i = 1; i < 20; i++) {
				$("#tr_examen_" + i).css("display", "none");
			}
		}, 750);
	</script>
	<?php	
		}
		
		/**
		 * Se obtiene un examen a partir un array
		 **/
		public function obtener_examen($lista_examenes_optometria_hc, $indice) {
			$examen_resul = array();
			if (isset($lista_examenes_optometria_hc[$indice])) {
				$examen_resul = $lista_examenes_optometria_hc[$indice];
			}
			
			return $examen_resul;
		}
	}
?>
