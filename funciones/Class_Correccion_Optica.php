<?php
	/*
	Pagina para Correcciones Opticas de Optometría: gafas, LdC, Ayudas Baja Visión, Cx Refractiva
	Autor: ZJJC - 27/04/2017
	*/
	require_once("../db/DbListas.php");
	require_once("Class_Combo_Box.php");
	require_once("../db/DbUsuariosPerfiles.php");
	require_once("../funciones/Class_Color_Pick.php");
	
	class Class_Correccion_Optica {
		
		public function getFormularioGafas($consecutivo_frm, $registro_gafas) {
			$dbListas = new DbListas(); 
			$combo = new Combo_Box();
			
			if ($registro_gafas["id_gafas"] == null) {
				$registro_gafas["id_gafas"] = "";
				$registro_gafas["tipo_lente"] = " ";
				$registro_gafas["tipo_lente_det"] = ""; 
				$registro_gafas["tipo_lente_otro"] = "";
				$registro_gafas["tipo_filtro"] = "";
				$registro_gafas["tiempo_rx"] = "";
				$registro_gafas["und_tiempo_rx"] = "";
				$registro_gafas["grado_satisfacc"] = "";		
				$registro_gafas["ind_presentes"] = 1;
			}
			
			if ($registro_gafas["ind_presentes"] == 0) {
				$chk_presentes = "checked";
			} else {
				$chk_presentes = "";
			}  
			if ($registro_gafas["tipo_lente"] == null) {
				$registro_gafas["tipo_lente"] = " ";
			}
			
			$i = $consecutivo_frm; 
			?>	
				<div class="frm_gafas_<?php echo($i); ?>">
				<br><label>Par de Anteojos No. <?php echo($i); ?>: </label>
				<table border="0" cellpadding="0" cellspacing="4" align="center" class="opt_panel_1" style="width:98%;"> 
					<tbody align="left">
					<tr>
						<td colspan="6" align="left">
							<br>
							<input id="chk_presentes_gafas_<?php echo($i); ?>" type="checkbox" style="margin: 1px;" <?php echo($chk_presentes); ?>><label for="chk_presentes_gafas_<?php echo($i); ?>">No los trae a consulta*</label> 
							<br><br>
						</td>
					</tr>					
					<tr> 
						<td align="left"><label>Tipo Anteojos*:</label></td>
						<td colspan="3"> 
							<?php 									
								$lista_tipos_gafas0 = $dbListas->getListaDetallesRecBase(12, null, 1); 								
								$combo->getComboDb("cmb_tipo_gafas_".$i, $registro_gafas["tipo_lente"], $lista_tipos_gafas0, "id_detalle, nombre_detalle", "--Seleccione--", "cmb_dependientes(1, ".$i.")", "1", "width:180px", "", "select_hc no-margin"); 
							?>
							<span id="d_cmb_tgafas_<?php echo($i); ?>">
								<?php								
									$lista_tipos_gafas1 = $dbListas->getListaDetallesRecBase(12, $registro_gafas["tipo_lente"], 1);
									$combo->getComboDb("cmb_tipo_gafas_det_".$i, $registro_gafas["tipo_lente_det"], $lista_tipos_gafas1, "id_detalle, nombre_detalle", " ", "cmb_dependientes(11, ".$i.")", "1", "width:160px", "", "select_hc no_margin");
								?> 	
							</span>							
						</td>
						<td colspan="2"><label for="otro_tipo_gafas" style="display:inline-block">Cu&aacute;l:</label>
							&nbsp;&nbsp;
							<input type="text" id="otro_tipo_gafas_<?php echo($i); ?>" name="otro_tipo_gafas_<?php echo($i); ?>" class="input input_hc" style="width:400px; margin: 0px; display:inline-block" value="<?php echo($registro_gafas["tipo_lente_otro"]); ?>" tabindex="" />
						</td>
					</tr>
					<tr>
						<td><label>Tratamiento-Filtro:</label></td>
						<td>
							<?php
								$lista_filtros = $dbListas->getListaDetalles(60);
								$combo->getComboDb("cmb_filtro_".$i, $registro_gafas["tipo_filtro"], $lista_filtros, "id_detalle, nombre_detalle", " ", "", " ", "", "", "select_hc no_margin");
							?>							
						</td>
						<td><label>Tiempo de uso:</label></td>
						<td>
							<input type="text" name="txt_tiempo_gafas_<?php echo($i); ?>" id="txt_tiempo_gafas_<?php echo($i); ?>" value="<?php echo($registro_gafas["tiempo_rx"]); ?>" maxlength="3" class="input input_hc componente_color_pick_0 ui-autocomplete-input" onkeypress="return solo_numeros(event, false);" /> 
							<?php
								$lista_unidades_tiempo = $dbListas->getListaDetalles(38);
								$combo->getComboDb("cmb_tiempo_gafas_".$i, $registro_gafas["und_tiempo_rx"], $lista_unidades_tiempo, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc no-margin");
							?>									
						</td>
						<td colspan="2"><label style="display:inline-block">Grado de satisfacci&oacute;n:</label>
							&nbsp;&nbsp;
							<?php
								$lista_satisfaccion = $dbListas->getListaDetalles(41);
								$combo->getComboDb("cmb_grado_satisfaccion_gafas_".$i, $registro_gafas["grado_satisfacc"], $lista_satisfaccion, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc no-margin");
							?> 
						</td>
					</tr>
					</tbody>
				</table> 		
				</div>
			<?php 		
			
		}
		
		public function getFormularioLensometria($consecutivo_frm, $registro_gafas, $colorPick = NULL) {
			$dbListas = new DbListas(); 
			$combo = new Combo_Box();
			
			if ($registro_gafas["id_gafas"] == null) { 
				$registro_gafas["lenso_esfera_od"] = "";
				$registro_gafas["lenso_cilindro_od"] = "";
				$registro_gafas["lenso_eje_od"] = "";
				$registro_gafas["lenso_lejos_od"] = "";
				//lenso_ph_od
				$registro_gafas["lenso_adicion_od"] = ""; 
				$registro_gafas["lenso_cerca_od"] = ""; 
				$registro_gafas["lenso_media_od"] = ""; 
				$registro_gafas["lenso_esfera_oi"] = ""; 
				$registro_gafas["lenso_cilindro_oi"] = ""; 
				$registro_gafas["lenso_eje_oi"] = ""; 
				$registro_gafas["lenso_lejos_oi"] = ""; 
				//lenso_ph_oi
				$registro_gafas["lenso_adicion_oi"] = ""; 
				$registro_gafas["lenso_cerca_oi"] = ""; 
				$registro_gafas["lenso_media_oi"] = ""; 

				$registro_gafas["prisma_tipo_od"] = "";	
				$registro_gafas["prisma_potencia_od"] = "";
				$registro_gafas["prisma_base_od"] = "";
				$registro_gafas["prisma_base_otra_od"] = "";				
				$registro_gafas["prisma_tipo_oi"] = "";	
				$registro_gafas["prisma_potencia_oi"] = "";
				$registro_gafas["prisma_base_oi"] = "";
				$registro_gafas["prisma_base_otra_oi"] = "";				
			}
			
			$display_prisma = ""; 
			$display_prisma = $registro_gafas["prisma_tipo_od"].$registro_gafas["prisma_potencia_od"].$registro_gafas["prisma_base_od"]; 
			$display_prisma .= $registro_gafas["prisma_tipo_oi"].$registro_gafas["prisma_potencia_oi"].$registro_gafas["prisma_base_oi"]; 
			if ($display_prisma=="") {
				$display_prisma="none";
				$class_prisma="ver_obser";
			} else {
				$display_prisma="";
				$class_prisma="ocultar_obser";
			}
			
			$valores_av = $dbListas->getListaDetalles(11); 			
			
			
			/********************************************************/
			/*Variable que contiene la cantidad de campos de colores*/
			/********************************************************/
			$cantidad_campos_colores = 280; //del 1 al 100 para objetos "estáticos" del formulario; del 100-250 para objetos creados en tiempo de ejecución (LensoAnteojos)
			// pickColor Anteojos: para cada frm Anteojos(con Lenso) se reservan 30 posiciones (para 30 campos) según el contador de formularios, así: frm1->ids 100 al 129; frm2->ids 130 al 159... frm5->ids 220 al 250 
			
			if (is_null($colorPick)) { 				
				// cadena de colores por defecto: 
				$arr_cadenas_colores = array();
				$arr_cadenas_colores[0]="0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000"; 
				$arr_cadenas_colores[1]="2222222000222222200000000000002222222000222222200000000000002222222000222222200000000000002222222000"; //campos azules lensometría 
				$arr_cadenas_colores[2]="22222220000000000000222222200022222220000000000000"; //campos azules lensometría 

				$colorPick = new Color_Pick($arr_cadenas_colores, $cantidad_campos_colores);				
			} 
			
			$arr_colores = $colorPick->getArrayColores();			
			
			$i = $consecutivo_frm;
			$icolor = 100 + ($i * 30) - 30; //ADVERTENCIA PARA MANTENIMIENTO de objetos con pickcolor: si el objeto se elimina no se puede reutilizar el número de su pickcolor; tampoco se puede cambiar el número pickcolor asignado a estos objetos
			?>
				<div class="frm_gafas_<?php echo($i); ?>">
				<div class="div_separador"></div>				
				<table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;" class="opt_panel_1">				
				<tr>
					<td align="left" style="width:42%;">
						<table border="0" cellpadding="1" cellspacing="0" align="center" style="width:100%;">
							<tr>
								<td align="center" style="width:25%;">
									ESFERA *<br />
									<input type="text" name="lenso_esfera_od_<?php echo($i); ?>" id="lenso_esfera_od_<?php echo($i); ?>" class="input input_hc componente_color_pick_<?php echo($arr_colores[$icolor + 0]); ?>" value="<?php echo($registro_gafas["lenso_esfera_od"]); ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);" />
									<?php
										$colorPick->getColorPick("lenso_esfera_od_".$i, $icolor + 0);
									?>
								</td>
								<td align="center" style="width:25%;">
									CILINDRO<br />
									<input type="text" name="lenso_cilindro_od_<?php echo($i); ?>" id="lenso_cilindro_od_<?php echo($i); ?>" class="input input_hc componente_color_pick_<?php echo($arr_colores[$icolor + 1]); ?>" value="<?php echo($registro_gafas["lenso_cilindro_od"]); ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);" />
									<?php
										$colorPick->getColorPick("lenso_cilindro_od_".$i, $icolor + 1);										
									?>
								</td>
								<td align="center" style="width:25%;">
									EJE *<br />
									<input type="text" name="lenso_eje_od_<?php echo($i); ?>" id="lenso_eje_od_<?php echo($i); ?>" class="input input_hc componente_color_pick_<?php echo($arr_colores[$icolor + 2]); ?>" value="<?php echo($registro_gafas["lenso_eje_od"]); ?>" maxlength="3" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);" />
									<?php
										$colorPick->getColorPick("lenso_eje_od_".$i, $icolor + 2);
									?>
								</td>
								<td align="center" style="width:25%;">
									ADICI&Oacute;N<br />
									<input type="text" name="lenso_adicion_od_<?php echo($i); ?>" id="lenso_adicion_od_<?php echo($i); ?>" class="input input_hc componente_color_pick_<?php echo($arr_colores[$icolor + 3]); ?>" value="<?php echo($registro_gafas["lenso_adicion_od"]); ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_adicion, this);" />
									<?php
										$colorPick->getColorPick("lenso_adicion_od_".$i, $icolor + 3);
									?>
								</td>
							</tr>
							<tr>
								<td> </td>
								<td align="center">
									LEJOS *<br />
									<?php
										$combo->getComboDb("lenso_lejos_od_".$i, $registro_gafas["lenso_lejos_od"], $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_".$arr_colores[$icolor + 4]);
										$colorPick->getColorPick("lenso_lejos_od_".$i, $icolor + 4);
									?>
								</td>
								<td align="center">
									INTERMEDIA<br />
									<?php
										$combo->getComboDb("lenso_media_od_".$i, $registro_gafas["lenso_media_od"], $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_".$arr_colores[$icolor + 5]);
										$colorPick->getColorPick("lenso_media_od_".$i, $icolor + 5);
									?>
								</td>
								<td align="center">
									CERCA *<br />
									<?php
										$combo->getComboDb("lenso_cerca_od_".$i, $registro_gafas["lenso_cerca_od"], $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_".$arr_colores[$icolor + 6]);
										$colorPick->getColorPick("lenso_cerca_od_".$i, $icolor + 6);
									?>
								</td>
							</tr>
							<tr id="div_pris_od<?php echo($i); ?>" style="display:<?php echo($display_prisma); ?>">
								<td> </td>
								<td align="center" style="width:25%;">
									TIPO PRISMA<br />
									<?php
										$lista_tipos_prisma = $dbListas->getListaDetalles(62);
										$combo->getComboDb("cmb_prisma_tipo_od_".$i, $registro_gafas["prisma_tipo_od"], $lista_tipos_prisma, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_".$arr_colores[$icolor + 7]);
										$colorPick->getColorPick("cmb_prisma_tipo_od_".$i, $icolor + 7);
									?>
								</td>
								<td align="center" style="width:25%;">
									POTENCIA<br />
									<input type="text" name="prisma_potencia_od_<?php echo($i); ?>" id="prisma_potencia_od_<?php echo($i); ?>" class="input input_hc componente_color_pick_<?php echo($arr_colores[$icolor + 8]); ?>" value="<?php echo($registro_gafas["prisma_potencia_od"]); ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_prisma_potencia, this);" />
									<?php
										$colorPick->getColorPick("lenso_cilindro_od", $icolor + 8);
									?>
								</td>
								<td align="center" style="width:25%;">
									BASE<br />
									<?php
										$lista_base_prisma = $dbListas->getListaDetalles(63);
										$combo->getComboDb("cmb_prisma_base_od_".$i, $registro_gafas["prisma_base_od"], $lista_base_prisma, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_".$arr_colores[$icolor + 9]);
										$colorPick->getColorPick("cmb_prisma_base_od_".$i, $icolor + 9);
									?>
								</td>
							</tr>
						</table>	
					</td>
					<td align="center" style="width:16%;"><label>Lensometría<br>Par de Anteojos No. <?php echo($i); ?>:</label> 
						<table width="100%"><tr>
							<td width="30%"></td>
							<td  width="40%" align="center">
								<span id="div_ver_pris_od<?php echo($i); ?>" class="<?php echo($class_prisma); ?>" onClick="capas_prisma(<?php echo($i); ?>)" title="Ver/Ocultar Prismas">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label style="display:inline">Prisma</label></span>
							</td>
							<td width="30%"></td>
						</tr></table>
					</td>
					<td align="left" style="width:42%;">
						<table border="0" cellpadding="1" cellspacing="0" align="center" style="width:100%;">
							<tr>
								<td align="center" style="width:25%;">
									ESFERA *<br />
									<input type="text" name="lenso_esfera_oi_<?php echo($i); ?>" id="lenso_esfera_oi_<?php echo($i); ?>" class="input input_hc componente_color_pick_<?php echo($arr_colores[$icolor + 10]); ?>" value="<?php echo($registro_gafas["lenso_esfera_oi"]); ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);" />
									<?php
										$colorPick->getColorPick("lenso_esfera_oi_".$i, $icolor + 10);
									?>
								</td>
								<td align="center" style="width:25%;">
									CILINDRO<br />
									<input type="text" name="lenso_cilindro_oi_<?php echo($i); ?>" id="lenso_cilindro_oi_<?php echo($i); ?>" class="input input_hc componente_color_pick_<?php echo($arr_colores[$icolor + 11]); ?>" value="<?php echo($registro_gafas["lenso_cilindro_oi"]); ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);" />
									<?php
										$colorPick->getColorPick("lenso_cilindro_oi_".$i, $icolor + 11);
									?>
								</td>
								<td align="center" style="width:25%;">
									EJE *<br />
									<input type="text" name="lenso_eje_oi_<?php echo($i); ?>" id="lenso_eje_oi_<?php echo($i); ?>" class="input input_hc componente_color_pick_<?php echo($arr_colores[$icolor + 12]); ?>" value="<?php echo($registro_gafas["lenso_eje_oi"]); ?>" maxlength="3" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);" />
									<?php
										$colorPick->getColorPick("lenso_eje_oi_".$i, $icolor + 12);
									?>
								</td>
								<td align="center" valign="top" style="width:25%;">
									ADICI&Oacute;N<br />
									<input type="text" name="lenso_adicion_oi_<?php echo($i); ?>" id="lenso_adicion_oi_<?php echo($i); ?>" class="input input_hc componente_color_pick_<?php echo($arr_colores[$icolor + 13]); ?>" value="<?php echo($registro_gafas["lenso_adicion_oi"]); ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_adicion, this);" />
									<?php
										$colorPick->getColorPick("lenso_adicion_oi_".$i, $icolor + 13);
									?>
								</td>
							</tr>
							<tr>
								<td align="center">
									LEJOS *<br />
									<?php
										$combo->getComboDb("lenso_lejos_oi_".$i, $registro_gafas["lenso_lejos_oi"], $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_".$arr_colores[$icolor + 14]);
										$colorPick->getColorPick("lenso_lejos_oi_".$i, $icolor + 14);
									?>
								</td>
								<td align="center">
									INTERMEDIA<br />
									<?php
										$combo->getComboDb("lenso_media_oi_".$i, $registro_gafas["lenso_media_oi"], $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_".$arr_colores[$icolor + 15]);
										$colorPick->getColorPick("lenso_media_oi_".$i, $icolor + 15);
									?>
								</td>									
								<td align="center">
									CERCA *<br />
									<?php
										$combo->getComboDb("lenso_cerca_oi_".$i, $registro_gafas["lenso_cerca_oi"], $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_".$arr_colores[$icolor + 16]); 																																													
										$colorPick->getColorPick("lenso_cerca_oi_".$i, $icolor + 16);
									?>
								</td>
								<td> </td>								
							</tr>
							<tr id="div_pris_oi<?php echo($i); ?>" style="display:<?php echo($display_prisma); ?>">				
								<td align="center" style="width:25%;">
									TIPO PRISMA<br />
									<?php
										$combo->getComboDb("cmb_prisma_tipo_oi_".$i, $registro_gafas["prisma_tipo_oi"], $lista_tipos_prisma, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_".$arr_colores[$icolor + 17]);
										$colorPick->getColorPick("cmb_prisma_tipo_oi_".$i, $icolor + 17);
									?>
								</td>
								<td align="center" style="width:25%;">
									POTENCIA<br />
									<input type="text" name="prisma_potencia_oi_<?php echo($i); ?>" id="prisma_potencia_oi_<?php echo($i); ?>" class="input input_hc componente_color_pick_<?php echo($arr_colores[$icolor + 18]); ?>" value="<?php echo($registro_gafas["prisma_potencia_oi"]); ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_prisma_potencia, this);" />
									<?php
										$colorPick->getColorPick("lenso_cilindro_oi", $icolor + 18);
									?>
								</td>
								<td align="center" style="width:25%;">
									BASE<br />
									<?php
										$lista_base_prisma = $dbListas->getListaDetalles(63);
										$combo->getComboDb("cmb_prisma_base_oi_".$i, $registro_gafas["prisma_base_oi"], $lista_base_prisma, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_".$arr_colores[$icolor + 19]);
										$colorPick->getColorPick("cmb_prisma_base_oi_".$i, $icolor + 19);
									?>
								</td>
								<td align="center"></td>
							</tr>						
						</table>				
					</td>
				</tr>
				</table>
				</div>
			<?php  
		}
		
		public function getFormularioLentesDeContacto($consecutivo_frm, $registro_ldc) {
			$dbListas = new DbListas(); 
			$combo = new Combo_Box(); 
						
			if ($registro_ldc["id_ldc"] == null) {
				$registro_ldc["tipo_ldc"] = " ";
				$registro_ldc["tipo_ldc_det"] = ""; 
				$registro_ldc["tipo_ldc_otro"] = "";	
				$registro_ldc["disenio"] = "";
				$registro_ldc["tipo_duracion"] = " ";
				$registro_ldc["tipo_duracion_det"] = "";
				$registro_ldc["ojo"] = "";
				$registro_ldc["tiempo_uso"] = "";
				$registro_ldc["und_tiempo_uso"] = "";				
				$registro_ldc["tiempo_no_uso"] = "";
				$registro_ldc["und_tiempo_no_uso"] = "";				
				$registro_ldc["modalidad_uso"] = "";
				$registro_ldc["tiempo_reemplazo"] = "";
				$registro_ldc["grado_satisfacc"] = "";				
				$registro_ldc["ind_presentes"] = 1;
			}
			
			if ($registro_ldc["ind_presentes"] == 0) {
				$chk_presentes = "checked";
			} else {
				$chk_presentes = "";
			}
			if ($registro_ldc["tipo_ldc"] == null) {
				$registro_ldc["tipo_ldc"] = " ";
			}
			if ($registro_ldc["tipo_duracion"] == null) {
				$registro_ldc["tipo_duracion"] = " ";
			}
			
			$i = $consecutivo_frm; 			
			?>
				<div class="frm_ldc_<?php echo($i); ?>">
				<br><label>Lentes de Contacto No. <?php echo($i); ?>: </label>
				<table border="0" cellpadding="0" cellspacing="4" align="center" class="opt_panel_1" style="width:98%;"> 
					<tbody align="left">
					<tr>
						<td colspan="3" align="left">
							<br>
							<input id="chk_presentes_ldc_<?php echo($i); ?>" type="checkbox" style="margin: 1px;" <?php echo($chk_presentes); ?>><label for="chk_presentes_ldc_<?php echo($i); ?>">No los trae a consulta*</label> 
							<br><br>
						</td>
					</tr>					
					<tr> 
						<td align="left"><label>Tipo LdC*:</label></td>
						<td>
							<?php 							
								$lista_tipos_ldc0 = $dbListas->getListaDetallesRecBase(16, null, 1); 								
								$combo->getComboDb("cmb_tipo_ldc_".$i, $registro_ldc["tipo_ldc"], $lista_tipos_ldc0, "id_detalle, nombre_detalle", "--Seleccione--", "cmb_dependientes(2, ".$i.")", "1", "width:160px", "", "select_hc no_margin"); 
							?>
							<span id="d_cmb_tldc_<?php echo($i); ?>">
								<?php								
									$lista_tipos_ldc1 = $dbListas->getListaDetallesRecBase(16, $registro_ldc["tipo_ldc"], 1);
									$combo->getComboDb("cmb_tipo_ldc_det_".$i, $registro_ldc["tipo_ldc_det"], $lista_tipos_ldc1, "id_detalle, nombre_detalle", " ", "cmb_dependientes(5, ".$i.")", "1", "width:180px", "", "select_hc no_margin");
								?> 
							</span>						
						</td>
						<td>
							<label style="display:inline-block">Cu&aacute;l:</label>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="text" id="otro_tipo_ldc_<?php echo($i); ?>" name="otro_tipo_ldc_<?php echo($i); ?>" class="input input_hc" style="width:400px; margin: 0px; display:inline-block" value="<?php echo($registro_ldc["tipo_ldc_otro"]); ?>" tabindex="" />							
						</td>
					</tr>
					<tr>
						<td><label>Dise&ntilde;o:</label></td>
						<td colspan="2">
							<?php 									
								$lista_tipos_ldc0 = $dbListas->getListaDetallesRecBase(16, 361, 1); 								
								$combo->getComboDb("cmb_disenioldc_".$i, $registro_ldc["disenio"], $lista_tipos_ldc0, "id_detalle, nombre_detalle", " ", "", "1", "width:160px", "", "select_hc no_margin"); 
							?>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<label style="display:inline-block">Duraci&oacute;n:</label>	
							&nbsp;&nbsp;
							<?php 									
								$lista_tipos_ldc0 = $dbListas->getListaDetallesRecBase(16, 365, 1); 								
								$combo->getComboDb("cmb_tipoduracion_ldc_".$i, $registro_ldc["tipo_duracion"], $lista_tipos_ldc0, "id_detalle, nombre_detalle", " ", "cmb_dependientes(5, ".$i.")", "1", "width:190px", "", "select_hc no_margin"); 
							?>
							<span id="d_cmb_duracion_det_<?php echo($i); ?>">
								<?php								
									$lista_tipos_ldc1 = $dbListas->getListaDetallesRecBase(16, $registro_ldc["tipo_duracion"], 1);
									$combo->getComboDb("cmb_tipoduracion_det_ldc_".$i, $registro_ldc["tipo_duracion_det"], $lista_tipos_ldc1, "id_detalle, nombre_detalle", " ", "", "1", "width:160px", "", "select_hc no_margin");
								?> 
							</span>								
						</td>
					</tr>
					<tr>
						<td align="left"><label>Ojo:</label></td>
						<td colspan="2">
							<?php 
								$tabla_ojos = $dbListas->getListaDetalles(14); 
								$combo->getComboDb("cmb_ojos_ldc_".$i, $registro_ldc["ojo"], $tabla_ojos, "id_detalle, nombre_detalle", " ", "", "", "width:160px", "", "select_hc no-margin");
							?>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<label style="display:inline-block">¿Hace cu&aacute;nto tiempo?:</label>
							<input type="text" name="txt_tiempo_uso_ldc_<?php echo($i); ?>" id="txt_tiempo_uso_ldc_<?php echo($i); ?>" value="<?php echo($registro_ldc["tiempo_uso"]); ?>" maxlength="3" class="input input_hc componente_color_pick_0 ui-autocomplete-input" onkeypress="return solo_numeros(event, false);" /> 
							<?php
								$lista_unidades_tiempo = $dbListas->getListaDetalles(38);
								$combo->getComboDb("cmb_tiempo_uso_ldc_".$i, $registro_ldc["und_tiempo_uso"], $lista_unidades_tiempo, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc no-margin"); 
							?>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;							
							<label style="display:inline-block">No los usa hace:</label>
							<input type="text" name="txt_tiempo_nouso_ldc_<?php echo($i); ?>" id="txt_tiempo_nouso_ldc_<?php echo($i); ?>" value="<?php echo($registro_ldc["tiempo_no_uso"]); ?>" maxlength="3" class="input input_hc componente_color_pick_0 ui-autocomplete-input" onkeypress="return solo_numeros(event, false);" /> 
							<?php
								$lista_unidades_tiempo2 = $dbListas->getListaDetalles(67); 
								$combo->getComboDb("cmb_tiempo_nouso_ldc_".$i, $registro_ldc["und_tiempo_no_uso"], $lista_unidades_tiempo2, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc no-margin");
							?>					
						</td>						
					</tr>					
					<tr>
						<td><label>Modalidad de uso:</label></td>
						<td colspan="3">
							<?php
								$lista_modalidad_uso = $dbListas->getListaDetalles(65);
								$combo->getComboDb("cmb_modalidad_uso_ldc_".$i, $registro_ldc["modalidad_uso"], $lista_modalidad_uso, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc no-margin");
							?>
							&nbsp;&nbsp;
							<label style="display:inline-block">Tiempo de reemplazo:</label>
							<?php
								$lista_tiempo_reemplazo = $dbListas->getListaDetalles(66);
								$combo->getComboDb("cmb_tiempo_reemplazo_ldc_".$i, $registro_ldc["tiempo_reemplazo"], $lista_tiempo_reemplazo, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc no-margin");
							?>
							&nbsp;&nbsp;
							<label style="display:inline-block">Grado de satisfacci&oacute;n:</label>
							<?php
								$lista_satisfaccion = $dbListas->getListaDetalles(41);
								$combo->getComboDb("cmb_grado_satisfaccion_ldc_".$i, $registro_ldc["grado_satisfacc"], $lista_satisfaccion, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc no-margin");
							?> 
						</td> 
					</tr>
					</tbody>
				</table>
				</div>
			<?php 	 	
		}			
		
		public function getFormularioAyudaBajaVision($consecutivo_frm, $registro_abv) {
			$dbListas = new DbListas(); 
			$combo = new Combo_Box();
						
			if ($registro_abv["id_abv"] == null) { 
				$registro_abv["tipo_abv"] = " ";
				$registro_abv["tipo_abv_det"] = "";
				$registro_abv["tipo_abv_otro"] = "";
				$registro_abv["grado_satisfacc"] = "";
				$registro_abv["ind_presentes"] = 1;
			}
			
			if ($registro_abv["ind_presentes"] == 0) {
				$chk_presentes = "checked";
			} else{
				$chk_presentes = "";
			}
			
			$i = $consecutivo_frm; 
			?>
				<div class="frm_abv_<?php echo($i); ?>">
				<br><label>Ayudas en Baja Visi&oacute;n No. <?php echo($i); ?>: </label>
				<table border="0" cellpadding="0" cellspacing="4" align="center" class="opt_panel_1" style="width:98%;"> 
					<tbody align="left">					
					<tr>
						<td colspan="3" align="left">
							<br>
							<input id="chk_presentes_abv_<?php echo($i); ?>" type="checkbox" style="margin: 1px;" <?php echo($chk_presentes); ?>><label for="chk_presentes_abv_<?php echo($i); ?>">No la trae a consulta*</label> 
							<br><br>
						</td>
					</tr>					
					<tr> 
						<td align="left"><label>Tipo Ayuda*:</label></td>						
						<td><label>Cu&aacute;l:</label>
						<td><label>Grado de satisfacci&oacute;n:</label></td>
					</tr>
					<tr>
						<td>    
							<?php 									
								$lista_tipos_abv0 = $dbListas->getListaDetallesRecBase(14, null, 1); 								
								$combo->getComboDb("cmb_tipo_abv_".$i, $registro_abv["tipo_abv"], $lista_tipos_abv0, "id_detalle, nombre_detalle", "--Seleccione--", "cmb_dependientes(3, ".$i.")", "1", "width:230px", "", "select_hc no_margin"); 
							?>
							<span id="d_cmb_tabv_<?php echo($i); ?>">
								<?php								
									$lista_tipos_abv1 = $dbListas->getListaDetallesRecBase(14, $registro_abv["tipo_abv"], 1);
									$combo->getComboDb("cmb_tipo_abv_det_".$i, $registro_abv["tipo_abv_det"], $lista_tipos_abv1, "id_detalle, nombre_detalle", " ", "cmb_dependientes(33, ".$i.")", "1", "width:230px", "", "select_hc no-margin");
								?> 	
							</span>							
						</td>							
						<td>	
							<input type="text" id="otro_tipo_abv_<?php echo($i); ?>" name="otro_tipo_abv_<?php echo($i); ?>" class="input input_hc" style="width:300px; margin: 0px;" value="<?php echo($registro_abv["tipo_abv_otro"]); ?>" tabindex="" />
						</td>						
						<td align="center">
							<?php
								$lista_satisfaccion = $dbListas->getListaDetalles(41);
								$combo->getComboDb("cmb_grado_satisfaccion_abv_".$i, $registro_abv["grado_satisfacc"], $lista_satisfaccion, "id_detalle, nombre_detalle", " ", "", "", "width:100px", "", "select_hc no-margin");
							?> 
						</td> 
					</tr>
					</tbody>
				</table>
				</div>
			<?php 	
		}	

		public function getFormularioCirugiaRefractiva($consecutivo_frm, $registro_cxr) {
			$dbListas = new DbListas(); 
			$combo = new Combo_Box();			
			$usuarios_perfiles = new DbUsuariosPerfiles(); 
						
			if ($registro_cxr["id_cxr"] == null) {
				$registro_cxr["tipo_cxr"] = " ";
				$registro_cxr["tipo_cxr_det"] = "";
				$registro_cxr["tipo_cxr_otro"] = "";
				$registro_cxr["ind_ajuste"] = "";				
				$registro_cxr["ojo"] = "";
				$registro_cxr["entidad"] = "";
				$registro_cxr["id_usuario_cirujano"] = "";
				$registro_cxr["cirujano_otro"] = "";
				$registro_cxr["tiempo_uso"] = "";
				$registro_cxr["und_tiempo_uso"] = "";
				$registro_cxr["grado_satisfacc"] = "";
			}
			
			if ($registro_cxr["ind_ajuste"] == 1) {
				$chk_ajuste = "checked";
			} else {
				$chk_ajuste = "";
			}
			
			$i = $consecutivo_frm; 			
			?>
				<div class="frm_cxr_<?php echo($i); ?>">
				<br><label>Cirug&iacute;a Refractiva No. <?php echo($i); ?>: </label>
				<table border="0" cellpadding="0" cellspacing="4" align="center" class="opt_panel_1" style="width:98%;"> 
					<tbody align="left">					
					<tr> 
						<td align="left"><label>Tipo Cirug&iacute;a*:</label></td>
						<td colspan="5">
							<?php 									
								$lista_tipos_cxr0 = $dbListas->getListaDetallesRecBase(15, null, 1); 								
								$combo->getComboDb("cmb_tipo_cxr_".$i, $registro_cxr["tipo_cxr"], $lista_tipos_cxr0, "id_detalle, nombre_detalle", "--Seleccione--", "cmb_dependientes(4, ".$i.")", "1", "width:170px", "", "select_hc no_margin"); 
							?>
							<span id="d_cmb_tcxr_<?php echo($i); ?>">
								<?php								
									$lista_tipos_cxr1 = $dbListas->getListaDetallesRecBase(15, $registro_cxr["tipo_cxr"], 1);
									$combo->getComboDb("cmb_tipo_cxr_det_".$i, $registro_cxr["tipo_cxr_det"], $lista_tipos_cxr1, "id_detalle, nombre_detalle", " ", "cmb_dependientes(44, ".$i.")", "1", "width:225px", "", "select_hc no_margin");
								?> 	
							</span>	
							&nbsp;&nbsp;&nbsp;
							<label style="display:inline-block">¿Cu&aacute;l?</label>
							&nbsp;&nbsp;
							<input type="text" id="otro_tipo_cxr_<?php echo($i); ?>" name="otro_tipo_cxr_<?php echo($i); ?>" class="input input_hc" style="width:285px; margin: 0px; display:inline-block" value="<?php echo($registro_cxr["tipo_cxr_otro"]); ?>" tabindex="" /> 
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input id="chk_ajuste_<?php echo($i); ?>" type="checkbox" style="margin: 1px;" <?php echo($chk_ajuste); ?>><label for="chk_ajuste_<?php echo($i); ?>">Ajuste</label>
						</td>
					</tr>
					<tr>
						<td align="left"><label>Ojo:</label></td>
						<td>
							<?php 
								$tabla_ojos = $dbListas->getListaDetalles(14); 
								$combo->getComboDb("cmb_ojos_cxr_".$i, $registro_cxr["ojo"], $tabla_ojos, "id_detalle, nombre_detalle", " ", "", "", "width:105px;margin:0;", "", "select_hc no-margin");
							?>
						</td>
						<td align="left" colspan="4"><label style="display:inline-block">¿Hace cu&aacute;nto tiempo?</label> 
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="text" name="txt_tiempo_cxr_<?php echo($i); ?>" id="txt_tiempo_cxr_<?php echo($i); ?>" value="<?php echo($registro_cxr["tiempo_uso"]); ?>" maxlength="3" class="input input_hc componente_color_pick_0 ui-autocomplete-input" onkeypress="return solo_numeros(event, false);" /> 
							<?php
								$lista_unidades_tiempo = $dbListas->getListaDetalles(38);
								$combo->getComboDb("cmb_tiempo_cxr_".$i, $registro_cxr["und_tiempo_uso"], $lista_unidades_tiempo, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc no-margin");
							?>									
						</td>
					</tr>					
					<tr>
						<td align="left"><label>¿En d&oacute;nde?</label></td> 
						<td>
							<?php
								$lista_lugares_cxr = $dbListas->getListaDetalles(64);
								$combo->getComboDb("cmb_entidad_cxr_".$i, $registro_cxr["entidad"], $lista_lugares_cxr, "id_detalle, nombre_detalle", " ", "config_dr(1, ".$i.")", "", "", "", "select_hc no-margin");
							?>
						</td>
						<td colspan="2">
							<label style="display:inline-block">¿Qui&eacute;n?</label>
							<?php			
								$lista_profesionales_foscal = $usuarios_perfiles->getListaUsuariosIndCirugia(1, -1, 1);
								$combo->getComboDb("cmb_cirujano_".$i, $registro_cxr["id_usuario_cirujano"], $lista_profesionales_foscal, "id_usuario, nombre_completo", " ",  "config_dr(2, ".$i.")", "", "width:230px", "", "select_hc no-margin");
							?> 
							<input type="text" id="txt_otro_cirujano_<?php echo($i); ?>" name="txt_otro_cirujano_<?php echo($i); ?>" class="input input_hc" style="width:230px; margin: 0px; display:inline-block" value="<?php echo($registro_cxr["cirujano_otro"]); ?>" tabindex="" />
						</td>	
						<td><label style="inline-block">Grado de satisfacci&oacute;n:</label></td>
						<td>
							<?php
								$lista_satisfaccion = $dbListas->getListaDetalles(41);
								$combo->getComboDb("cmb_grado_satisfaccion_cxr_".$i, $registro_cxr["grado_satisfacc"], $lista_satisfaccion, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc no-margin");
							?> 
						</td> 
					</tr>
					</tbody>
				</table>
				</div>
			<?php 	
		}		
	}
?>
