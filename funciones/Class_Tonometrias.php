<?php
	require_once("../db/DbConsultaOftalmologia.php");
	require_once("../db/DbVariables.php");
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/FuncionesPersona.php");
	
	class Class_Tonometrias {
		//Función para el cargue del componente de tonometría
		public function agregar_tonometria($id_hc, $observaciones_tonometria = "", $colorPick = null) {
			$dbConsultaOftalmologia = new DbConsultaOftalmologia();
			$dbVariables = new Dbvariables();
			
			if ($colorPick != null) {
				$arr_colores = $colorPick->getArrayColores();
			} else {
				$arr_colores = array();
			}
			
			$utilidades = new Utilidades();
			$funciones_persona = new FuncionesPersona();
			
			$fecha_mostrar = $dbVariables->getFechaActualMostrar();
			
			$historia_clinica_obj = $dbConsultaOftalmologia->getHistoriaClinicaId($id_hc);
			
			//Se cargan los datos de la consulta de optometría asociada (tonometría neumática)
			$consulta_optometria_obj = $dbConsultaOftalmologia->getOptometriaPaciente($historia_clinica_obj["id_paciente"], $historia_clinica_obj["id_admision"]);
			if (isset($consulta_optometria_obj["id_hc"])) {
				$presion_intraocular_od = $consulta_optometria_obj["presion_intraocular_od"];
				$presion_intraocular_oi = $consulta_optometria_obj["presion_intraocular_oi"];
				
				$dilatado_opt = "-";
				switch ($consulta_optometria_obj["ind_dilatado"]) {
					case "1":
						$dilatado_opt = "Sí";
						break;
					case "2":
						$dilatado_opt = "No";
						break;
				}
			} else {
				$presion_intraocular_od = "";
				$presion_intraocular_oi = "";
				$dilatado_opt = "-";
			}
			
			//Se cargan los datos de la tonomatría aplanática
			$lista_tonometria = $dbConsultaOftalmologia->getTonometria($id_hc);
			$cant_tonometria = count($lista_tonometria);
			if ($cant_tonometria <= 1) {
				$cant_tonometria = 1;
			}
	?>
    <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:98%;">
		<tr>
		   	<td align="center" colspan="3" style="width:100%;">
				<table border="0" cellpadding="1" cellspacing="0" align="center" style="width:100%;">
				   	<!--<tr>
				   		<td align="center" style="width:40%;">
				   			<table border="0" cellpadding="3" cellspacing="0" align="center" style="width:100%;">
					   			<tr>
                                	<td align="right" style="width:50%;">
								   		<label><?php echo($presion_intraocular_od != "" ? $presion_intraocular_od : "-"); ?></label>
                                    </td>
                                    <td align="left" style="width:30%;"> 
                                        <label><?php echo($presion_intraocular_od != "" ? "mmHg" : ""); ?></label>
								   	</td>
                                </tr>
					   		</table>
				   		</td>
				   		<td align="center" style="width:20%;"><h5 style="margin: 0px">Tonometr&iacute;a Neum&aacute;tica</h5></td>
				   		<td align="center" style="width:40%;">
				   			<table border="0" cellpadding="3" cellspacing="0" align="center" style="width:100%;">
                                <tr>
                                    <td align="right" style="width:25%;">
                                        <label><?php echo($presion_intraocular_oi != "" ? $presion_intraocular_oi : "-");?></label>
                                    </td>
                                    <td align="left" style="width:75%;"> 
                                        <label><?php echo($presion_intraocular_oi != "" ? "mmHg" : ""); ?></label>
	                                </td>
                                </tr>
					   		</table>	
				   		</td>
				   	</tr>
                    <tr>
                    	<td align="center" colspan="3">
                        	<label><b>Dilatado:</b>&nbsp;<?php echo($dilatado_opt); ?></label>
                        </td>
                    </tr>-->
		  			<tr>
				   		<td align="center" colspan="3" >
					   		<h5 style="margin: 0px">Tonometr&iacute;a Aplan&aacute;tica</h5>
				   		</td>
				   	</tr>
				   	<tr>
				   		<th align="center" colspan="3" style="width:300px;" class="">
                            <input type="hidden" value="<?php echo($cant_tonometria);?>" name="cant_tonometria" id="cant_tonometria" />
                            <table border="0" cellpadding="4" cellspacing="0" align="center" style="width:100%;"> 
                                <?php
									$cont_colores_aux = 70;
									for ($i = 1; $i <= 10; $i++) {
										$tabla_tonometria_hc = $this->obtener_tonometria($lista_tonometria, $i);
										
										if (count($tabla_tonometria_hc) == 0) {
											$tonometria_valor_od = "";
											$tonometria_dilatado_od = "";
											$tonometria_valor_oi = "";
											$tonometria_dilatado_oi = "";
											$tonometria_fecha = $fecha_mostrar["fecha_actual_mostrar"];
											$tonometria_hora = $fecha_mostrar["hora24_actual_mostrar"];
										} else {
											$tonometria_valor_od = $tabla_tonometria_hc["tonometria_valor_od"];
											$tonometria_dilatado_od = $tabla_tonometria_hc["tonometria_dilatado_od"];
											$tonometria_valor_oi = $tabla_tonometria_hc["tonometria_valor_oi"];
											$tonometria_dilatado_oi = $tabla_tonometria_hc["tonometria_dilatado_oi"];
											$tonometria_fecha = $tabla_tonometria_hc["tonometria_fecha"];
											$tonometria_hora = $tabla_tonometria_hc["tonometria_hora"];
										}
								?>
                                <tr id="tabla_tono_<?php echo($i);?>" style="display:none;">
                                    <td align="right" style="width:12%;">
                                        <label><b>Sin dilatar</b></label>
                                    </td>
                                    <td align="left" style="width:5%;">
                                        <input type="text" name="tonometria_valor_od_<?php echo($i);?>" id="tonometria_valor_od_<?php echo($i);?>" class="input input_hc componente_color_pick_<?php echo($arr_colores[$cont_colores_aux]); ?>" style="width:50px; margin: 0px;" value="<?php echo $tonometria_valor_od; ?>" onkeypress="formato_hc(event, this);" tabindex="" />
                                    </td>
                                    <td align="left" style="width:4%;">
                                        <?php
											if ($colorPick != null) {
												$colorPick->getColorPick("tonometria_valor_od_".$i, $cont_colores_aux);
											}
											$cont_colores_aux++;
										?>
                                    </td>
                                    <td align="right" style="width:7%;">
                                        <label><b>Dilatado</b></label>
                                    </td>
                                    <td align="left" style="width:5%;">
                                        <input type="text" name="tonometria_dilatado_od_<?php echo($i);?>" id="tonometria_dilatado_od_<?php echo($i);?>" class="input input_hc componente_color_pick_<?php echo($arr_colores[$cont_colores_aux]); ?>" style="width:50px; margin: 0px;" value="<?php echo $tonometria_dilatado_od; ?>" onkeypress="formato_hc(event, this);" tabindex="" />
                                    </td>
                                    <td align="left" style="width:4%;">
                                        <?php
											if ($colorPick != null) {
												$colorPick->getColorPick("tonometria_dilatado_od_".$i, $cont_colores_aux);
											}
											$cont_colores_aux++;
										?>
                                    </td>
                                    <td align="right" style="width:17%;">
                                        <label><b>Sin dilatar</b></label>
                                    </td>
                                    <td align="left" style="width:5%;">
                                        <input type="text" name="tonometria_valor_oi_<?php echo($i);?>" id="tonometria_valor_oi_<?php echo($i);?>" class="input input_hc componente_color_pick_<?php echo($arr_colores[$cont_colores_aux]); ?>" style="width:50px; margin: 0px;" value="<?php echo $tonometria_valor_oi; ?>" onkeypress="formato_hc(event, this);" tabindex="" />
                                    </td>
                                    <td align="left" style="width:4%;">
                                        <?php
											if ($colorPick != null) {
												$colorPick->getColorPick("tonometria_valor_oi_".$i, $cont_colores_aux);
											}
											$cont_colores_aux++;
										?>
                                    </td>
                                    <td align="right" style="width:7%;">
                                        <label><b>Dilatado</b></label>
                                    </td>
                                    <td align="left" style="width:5%;">
                                        <input type="text" name="tonometria_dilatado_oi_<?php echo($i);?>" id="tonometria_dilatado_oi_<?php echo($i);?>" class="input input_hc componente_color_pick_<?php echo($arr_colores[$cont_colores_aux]); ?>" style="width:50px; margin: 0px;" value="<?php echo $tonometria_dilatado_oi; ?>" onkeypress="formato_hc(event, this);" tabindex="" />
                                    </td>
                                    <td align="left" style="width:4%;">
                                        <?php
											if ($colorPick != null) {
												$colorPick->getColorPick("tonometria_dilatado_oi_".$i, $cont_colores_aux);
											}
											$cont_colores_aux++;
										?>
                                    </td>
                                    <td align="left" style="width:8%;">
                                        <input type="text" name="tonometria_fecha_<?php echo($i);?>" id="tonometria_fecha_<?php echo($i);?>" class="input input_hc componente_color_pick_<?php echo($arr_colores[$cont_colores_aux]); ?>" maxlength="10" style="width:80px;" value="<?php echo($tonometria_fecha); ?>" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                                    </td>
                                    <td align="left" style="width:4%;">
                                        <?php
											if ($colorPick != null) {
												$colorPick->getColorPick("tonometria_fecha_".$i, $cont_colores_aux);
											}
											$cont_colores_aux++;
										?>
                                    </td>
                                    <td align="left" style="width:5%;" >
                                        <input type="text" name="tonometria_hora_<?php echo($i);?>" id="tonometria_hora_<?php echo($i);?>"  class="input input_hc componente_color_pick_<?php echo($arr_colores[$cont_colores_aux]); ?>" style="width:50px;" value="<?php echo($tonometria_hora); ?>" onblur="validar_hora(this);" />
                                    </td>
                                    <td align="left" style="width:4%;">
                                        <?php
											if ($colorPick != null) {
												$colorPick->getColorPick("tonometria_hora_".$i, $cont_colores_aux);
											}
											$cont_colores_aux++;
										?>
                                    </td>
                                </tr>
                                <?php
									}
								?>
                            </table>
                            <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:80%;">
                                <tr>
                                    <td>
                                        <div class="agregar_alemetos" onclick="agregar_tabla_tono();"></div>
                                        <div class="restar_alemetos" onclick="restar_tabla_tono();"></div>
                                    </td>
                                </tr>
                            </table>
                            <script type="text/javascript">
                                mostrar_tonometria();
                            </script>
                        </th>
                    </tr>
                    <tr>
                        <td colspan="3" align="left">
                            <label><b>Observaciones</b></label>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="3">
                            <div id="txt_observaciones_tonometria"><?php echo($utilidades->ajustar_texto_wysiwyg($observaciones_tonometria)); ?></div>
                        </td>
                    </tr>
                    <?php
						//Se carga el listado de tonometrías anteriores
						$lista_tono_ant = $dbConsultaOftalmologia->getListaTonometriasAnteriores($id_hc);
						if (count($lista_tono_ant) > 0) {
					?>
                    <tr>
                        <td align="center" colspan="3">
                            <h5 style="margin:0px; display:inline;">Tonometr&iacute;as aplan&aacute;ticas anteriores</h5>
                            <img id="img_mostrar_tono_ant" class="img_button" style="margin:0; display:inline;" onclick="ver_tonometrias_anteriores(true);" src="../imagenes/icon-blue-down.png" title="Mostrar tonometr&iacute;as aplan&aacute;ticas anteriores" />
                            <img id="img_ocultar_tono_ant" class="img_button" style="margin:0; display:none;" onclick="ver_tonometrias_anteriores(false);" src="../imagenes/icon-blue-up.png" title="Ocultar tonometr&iacute;as aplan&aacute;ticas anteriores" />
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="3">
                            <div id="d_tonometrias_ant" style="display:none; text-align:left;">
                                <table class="modal_table" style="width:97%; text-align:center;">
                                    <thead>
                                        <tr class="headegrid">
                                            <th class="th_reducido" style="width:22%;" colspan="2">OD</th>
                                            <th class="th_reducido" style="width:22%;" colspan="2">OI</th>
                                            <th class="th_reducido" style="width:17%;" rowspan="2">Fecha/Hora</th>
                                            <th class="th_reducido" style="width:39%;" rowspan="2">Observaciones</th>
                                        </tr>
                                        <tr class="headegrid">
                                            <th class="th_reducido" style="width:11%;">Sin dilatar</th>
                                            <th class="th_reducido" style="width:11%;">Dilatado</th>
                                            <th class="th_reducido" style="width:11%;">Sin dilatar</th>
                                            <th class="th_reducido" style="width:11%;">Dilatado</th>
                                        </tr>
                                    </thead>
                                </table>
                                <div style="width:100%; overflow:auto; max-height:350px; text-align:left;">
                                    <table class="modal_table" style="width:98.5%; text-align:center;">
                                        <?php
											foreach ($lista_tono_ant as $tono_aux) {
										?>
                                        <tr>
                                            <td class="td_reducido" style="width:11%;">
                                                <?php echo($tono_aux["tonometria_valor_od"]); ?>
                                            </td>
                                            <td class="td_reducido" style="width:11%;">
                                                <?php echo($tono_aux["tonometria_dilatado_od"]); ?>
                                            </td>
                                            <td class="td_reducido" style="width:11%;">
                                                <?php echo($tono_aux["tonometria_valor_oi"]); ?>
                                            </td>
                                            <td class="td_reducido" style="width:11%;">
                                                <?php echo($tono_aux["tonometria_dilatado_oi"]); ?>
                                            </td>
                                            <td class="td_reducido" style="width:17%;">
                                                <?php echo($funciones_persona->obtenerFecha6($tono_aux["fecha_tono_t"])." ".$tono_aux["hora_tono_t"]); ?>
                                            </td>
                                            <td class="td_reducido" style="width:39%;">
                                                <?php echo($tono_aux["observaciones_tonometria"]); ?>
                                            </td>
                                        </tr>
                                        <?php
											}
										?>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php
						}
					?>
			    </table>
			</td>
		</tr>
    </table>
    <?php
		}
		
		//Funciòn que retorna el registro de tonometría que viene en un orden dado formateando fechas y horas
		private function obtener_tonometria($tabla_tonometria, $orden) {
			$array_tonometria_hc = array();
			
			foreach ($tabla_tonometria as $fila_tonometria) {
				$orden_hc = $fila_tonometria["orden"];
				if ($orden == $orden_hc) {
					$array_tonometria_hc["tonometria_valor_od"] = $fila_tonometria["tonometria_valor_od"];
					$array_tonometria_hc["tonometria_dilatado_od"] = $fila_tonometria["tonometria_dilatado_od"];
					$array_tonometria_hc["tonometria_valor_oi"] = $fila_tonometria["tonometria_valor_oi"];
					$array_tonometria_hc["tonometria_dilatado_oi"] = $fila_tonometria["tonometria_dilatado_oi"];
					$tonometria_fecha_hora = $fila_tonometria["fecha_hora_tonometria"];
					$array_tonometria_hc["tonometria_fecha"] = substr($tonometria_fecha_hora, 0, 10);
					$array_tonometria_hc["tonometria_hora"] = substr($tonometria_fecha_hora, 11, 5);
					break;
				}
			}
			return $array_tonometria_hc;
		}
		
		//Funciòn que retorna el listado de valores de tonometría para guardar
		public function obtener_listado_tonometria_guardar($post) {
			$utilidades = new Utilidades();
			$cant_tono = $utilidades->str_decode($post["cant_tono"]);
			$array_tonometria = array();
			for ($i = 1; $i <= $cant_tono; $i++) {
				$tonometria_valor_od = $utilidades->str_decode($post["tonometria_valor_od_".$i]);
				$tonometria_dilatado_od = $utilidades->str_decode($post["tonometria_dilatado_od_".$i]);
				$tonometria_valor_oi = $utilidades->str_decode($post["tonometria_valor_oi_".$i]);
				$tonometria_dilatado_oi = $utilidades->str_decode($post["tonometria_dilatado_oi_".$i]);
				$tonometria_fecha = $utilidades->str_decode($post["tonometria_fecha_".$i]);
				$tonometria_hora = $utilidades->str_decode($post["tonometria_hora_".$i]);
				
				$array_tonometria[$i]["valor_od"] = $tonometria_valor_od;
				$array_tonometria[$i]["dilatado_od"] = $tonometria_dilatado_od;
				$array_tonometria[$i]["valor_oi"] = $tonometria_valor_oi;
				$array_tonometria[$i]["dilatado_oi"] = $tonometria_dilatado_oi;
				$array_tonometria[$i]["fecha"] = $tonometria_fecha;
				$array_tonometria[$i]["hora"] = $tonometria_hora;
			}
			
			return $array_tonometria;
		}
	}
?>
