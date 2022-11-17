<?php
	class Class_Componente_Rec_Oft {
		function __construct() {
		}
		
		/*
		$tipo_componente: 1: text input - 2: textarea - 3: hidden input
		*/
		public function get_componente($nombre_componente, $ojo, $valor_componente, $arr_colores, $id_color, $color_pick,
				$id_lista, $tipo_componente, $clase_css, $estilo_css, $default_op = "Normal") {
			require_once("../db/DbListas.php");
			$dbListas = new DbListas();
			
			if ($tipo_componente != 3) {
				$id_componente = "txt_".$nombre_componente;
			} else {
				$id_componente = "hdd_".$nombre_componente."_valor";
			}
			$id_componente_rec = "d_".$nombre_componente."_rec";
			$sufijo_ojo = "";
			$sufijo_ojo_alt = "";
			$float_comp = "left";
			if ($ojo != "") {
				$id_componente .= "_".$ojo;
				$id_componente_rec .= "_".$ojo;
				$sufijo_ojo = "_".$ojo;
				switch ($ojo) {
					case "od":
						$sufijo_ojo_alt = "_oi";
						break;
					case "oi":
						$sufijo_ojo_alt = "_od";
						$float_comp = "right";
						break;
				}
			}
	?>
    <input type="hidden" id="hdd_<?php echo($nombre_componente.$sufijo_ojo); ?>_tipo" value="<?php echo($tipo_componente); ?>" />
    <input type="hidden" id="hdd_<?php echo($nombre_componente.$sufijo_ojo); ?>_lista" value="<?php echo($id_lista); ?>" />
    <input type="hidden" id="hdd_<?php echo($nombre_componente.$sufijo_ojo); ?>_default" value="<?php echo($default_op); ?>" />
    <?php
			//Botón de copia de valor
			if ($tipo_componente != 3 && $sufijo_ojo != "") {
	?>
	<div style="float:<?php echo($float_comp); ?>;"><img src="../imagenes/copy_opt.png" class="img_button" onclick="copiar_campo_rec_oft('<?php echo($nombre_componente); ?>', '<?php echo($sufijo_ojo); ?>', '<?php echo($sufijo_ojo_alt); ?>');"></div>
    <?php
			}
			
			//Si la alineación es derecha (ojo izquierdo), se incluyen primero los elementos de botón de lista y color pick
			if ($float_comp == "right") {
	?>
    <div style="float:<?php echo($float_comp); ?>;"><img src="../imagenes/add_elemento.png" class="img_button" onclick="mostrar_lista_rec_oft(this, event, '<?php echo($id_componente_rec); ?>', '<?php echo($id_componente); ?>', '<?php echo("hdd_".$nombre_componente.$sufijo_ojo); ?>', '<?php echo("d_".$nombre_componente.$sufijo_ojo); ?>', '<?php echo("img_".$nombre_componente.$sufijo_ojo); ?>');"></div>
    <?php
				if ($tipo_componente != 3) {
					$color_pick->getColorPick($id_componente, $id_color, "float:".$float_comp."; margin:8px 3px 8px 3px");
				}
			}
			
			switch ($tipo_componente) {
				case 1: //text input
	?>
    <input type="text" id="<?php echo($id_componente); ?>" class="componente_color_pick_<?php echo($arr_colores[$id_color]); ?> no-margin <?php echo($clase_css); ?>" style="float:<?php echo($float_comp); ?>; padding:0; <?php echo($estilo_css); ?>" onfocus="ocultar_listas_rec_oft();" onblur="trim_cadena(this);" value="<?php echo($valor_componente); ?>" />
    <?php
					break;
					
				case 2: //textarea
	?>
    <textarea id="<?php echo($id_componente); ?>" class="componente_color_pick_<?php echo($arr_colores[$id_color]); ?> no-margin <?php echo($clase_css); ?>" style="float:<?php echo($float_comp); ?>; padding:0; <?php echo($estilo_css); ?>" onfocus="ocultar_listas_rec_oft();" onblur="trim_cadena(this);"><?php echo($valor_componente); ?></textarea>
    <?php
					break;
					
				case 3: //hidden input
	?>
    <input type="hidden" id="<?php echo($id_componente); ?>" value="<?php echo($valor_componente); ?>" />
    <?php
					break;
			}
			
			//Si la alineación es izquierda (ojo derecho), se incluyen a continuación los elementos de color pick y botón de lista
			if ($float_comp == "left") {
				if ($tipo_componente != 3) {
					$color_pick->getColorPick($id_componente, $id_color, "float:".$float_comp."; margin:8px 3px 8px 3px");
				}
	?>
    <div style="float:<?php echo($float_comp); ?>;"><img src="../imagenes/add_elemento.png" class="img_button" onclick="mostrar_lista_rec_oft(this, event, '<?php echo($id_componente_rec); ?>', '<?php echo($id_componente); ?>', '<?php echo("hdd_".$nombre_componente.$sufijo_ojo); ?>', '<?php echo("d_".$nombre_componente.$sufijo_ojo); ?>', '<?php echo("img_".$nombre_componente.$sufijo_ojo); ?>');"></div>
    <?php
			}
			
			//Contenedor de listas recursivas
			$lista_recursiva_obj = $dbListas->getListaRecursiva($id_lista);
			$titulo_lista = "<b>".$lista_recursiva_obj["nombre_lista"]."</b>";
			$subtitulo_lista = "";
			switch ($ojo) {
				case "od":
					$subtitulo_lista .= "Ojo derecho";
					break;
				case "oi":
					$subtitulo_lista .= "Ojo izquierdo";
					break;
			}
			
			$mapa_recursiva_det = array();
			$mapa_recursiva_det["base"] = array();
			
			//Se obtiene la lista recursiva
			$lista_recursiva_det = $dbListas->getListaDetallesRec($id_lista, 1);
			
			foreach ($lista_recursiva_det as $detalle_aux) {
				$id_detalle_base = $detalle_aux["id_detalle_base"];
				if ($id_detalle_base == "") {
					$id_detalle_base = "base";
				}
				if (!isset($mapa_recursiva_det[$id_detalle_base])) {
					$mapa_recursiva_det[$id_detalle_base] = array();
				}
				array_push($mapa_recursiva_det[$id_detalle_base], $detalle_aux);
			}
			
        	$this->mostrar_lista_rec($mapa_recursiva_det, $nombre_componente, $sufijo_ojo, "", "", $default_op, $valor_componente, $id_componente_rec, $titulo_lista, $subtitulo_lista);
	?>
    <script>
		<?php
			if ($tipo_componente == 2) {
		?>
		//Se agrega el componente a la lista de textareas
		$("#<?php echo($id_componente); ?>").textareaAutoSize();
		arr_textarea_ids.push("<?php echo($id_componente); ?>");
		<?php
			}
		?>
		
		g_arr_nombres_componentes.push("<?php echo($nombre_componente); ?>");
		g_arr_sufijos_ojos.push("<?php echo($sufijo_ojo); ?>");
		g_arr_componentes_rec.push("<?php echo($id_componente_rec); ?>");
	</script>
    <?php
		}
		
		public function mostrar_lista_rec($mapa_recursiva_det, $nombre_componente, $sufijo_ojo, $id_detalle_base, $sufijo, $default_op, $valor_componente, $id_componente_rec, $titulo_lista, $subtitulo_lista) {
			$id_hidden = "hdd_".$nombre_componente.$sufijo_ojo;
			$id_check = "chk_".$nombre_componente.$sufijo_ojo;
			$id_img = "img_".$nombre_componente.$sufijo_ojo;
			$id_div = "d_".$nombre_componente.$sufijo_ojo;
			$id_texto_valor = "txt_".$nombre_componente.$sufijo_ojo."_valor";
			
			$estilo_ubic = "";
			if ($id_detalle_base == "") {
				$id_detalle_base = "base";
			} else {
				$estilo_ubic = "position:absolute; left:20px;";
			}
			$cant_base = count($mapa_recursiva_det[$id_detalle_base]);
		?>
        <div id="<?php echo($id_componente_rec); ?>" class="div_contenedor_lista_rec" style="display:none;<?php echo($estilo_ubic); ?>">
        	<div class="a_cierre_panel" onclick="ocultar_lista_rec_oft('<?php echo($id_componente_rec); ?>');"></div>
            <div id="d_header">
                <div><?php echo($titulo_lista.($subtitulo_lista != "" ? "<br />".$subtitulo_lista : "")); ?></div>
            </div>
            <input type="hidden" id="<?php echo($id_hidden.$sufijo); ?>_cant" value="<?php echo($cant_base); ?>" />
            <?php
				for ($i = 0; $i < $cant_base; $i++) {
					$detalle_aux = $mapa_recursiva_det[$id_detalle_base][$i];
					$nombre_compuesto_aux = $this->obtener_nombre_rec_mapa($detalle_aux["id_detalle"], $mapa_recursiva_det);
					$valor_texto = "";
			?>
            <div id="<?php echo($id_div.$sufijo."_".$i); ?>" class="div_encabezado_lista_rec">
                <input type="hidden" id="<?php echo($id_hidden.$sufijo."_".$i); ?>" value="<?php echo($detalle_aux["id_detalle"]); ?>" />
                <div class="div_left">
                    <input type="hidden" id="<?php echo($id_hidden."_texto".$sufijo."_".$i); ?>" value="<?php echo($detalle_aux["nombre_detalle"]); ?>" />
                    <input type="hidden" id="<?php echo($id_hidden."_excluye".$sufijo."_".$i); ?>" value="<?php echo($detalle_aux["ind_excluye_padre"]); ?>" />
                    <?php
						if (isset($mapa_recursiva_det[$detalle_aux["id_detalle"]])) {
					?>
                    <img id="<?php echo($id_img."_plus".$sufijo."_".$i); ?>" src="../imagenes/ver_derecha.png" class="img_button no-margin" style="display:inline-block;" onclick="mostrar_detalle_lista_rec('<?php echo($id_hidden); ?>', '<?php echo($id_div); ?>', '<?php echo($id_img); ?>', '<?php echo($sufijo); ?>', '<?php echo("_".$i); ?>');" />
                    <img id="<?php echo($id_img."_minus".$sufijo."_".$i); ?>" src="../imagenes/ver_abajo.png" class="img_button no-margin" style="display:none;" onclick="mostrar_detalle_lista_rec('<?php echo($id_hidden); ?>', '<?php echo($id_div); ?>', '<?php echo($id_img); ?>', '<?php echo($sufijo); ?>', '<?php echo("_".$i); ?>');" />
                    <?php
						} else {
							//Se determina si se debe marcar el check de acuerdo con el valor de texto del componente
							$ind_checked = "";
							$pos_aux = stripos(", ".$valor_componente.", ", ", ".$nombre_compuesto_aux.", ");
							if ($pos_aux !== false) {
								$ind_checked = "checked=\"checked\"";
							} else {
								$pos_aux = stripos(", ".$valor_componente.", ", ", ".$nombre_compuesto_aux.": ");
								if ($pos_aux !== false) {
									$ind_checked = "checked=\"checked\"";
									
									//Se halla el valor adicional guardado
									$pos_aux2 = stripos(substr(", ".$valor_componente.", ", $pos_aux + 1), ": ");
									$pos_aux3 = stripos(substr(", ".$valor_componente.", ", $pos_aux + 1), ", ");
									if ($pos_aux2 !== false && $pos_aux3 !== false) {
										$valor_texto = trim(substr(", ".$valor_componente.", ", $pos_aux + $pos_aux2 + 2, $pos_aux3 - $pos_aux2 - 1));
										
										//Se verifica si el texto tiene unidades
										$len_aux = strlen($detalle_aux["nombre_unidad"]);
										if ($len_aux > 0) {
											$unidad_aux = substr($valor_texto, strlen($valor_texto) - $len_aux);
											if (strtolower($unidad_aux) == strtolower($detalle_aux["nombre_unidad"])) {
												$valor_texto = trim(substr($valor_texto, 0, strlen($valor_texto) - $len_aux));
											}
										}
									}
								}
							}
					?>
                    <input type="checkbox" id="<?php echo($id_check.$sufijo."_".$i); ?>" class="no-margin" onchange="marcar_check_rec_oft('<?php echo($nombre_componente); ?>', '<?php echo($sufijo_ojo); ?>', '<?php echo($sufijo); ?>', '<?php echo("_".$i); ?>');" <?php echo($ind_checked); ?> />
                    <?php
						}
					?>
                </div>
                <?php
						if (isset($mapa_recursiva_det[$detalle_aux["id_detalle"]])) {
				?>
                <div class="div_right" onclick="mostrar_detalle_lista_rec('<?php echo($id_hidden); ?>', '<?php echo($id_div); ?>', '<?php echo($id_img); ?>', '<?php echo($sufijo); ?>', '<?php echo("_".$i); ?>');"><?php echo($detalle_aux["nombre_detalle"]); ?></div>
                <?php
						} else {
				?>
                <div class="div_right" onclick="cambiar_check_rec_oft('<?php echo($nombre_componente); ?>', '<?php echo($sufijo_ojo); ?>', '<?php echo($sufijo); ?>', '<?php echo("_".$i); ?>');">
					<?php
							if ($detalle_aux["ind_tipo_valor"] == "0") {
								echo($detalle_aux["nombre_detalle"]);
							} else {
								echo($detalle_aux["nombre_detalle"].":&nbsp;");
							}
					?>
                </div>
                <?php
							switch ($detalle_aux["ind_tipo_valor"]) {
								case "1": //Entero
				?>
                <input type="text" id="<?php echo($id_texto_valor.$sufijo."_".$i); ?>" value="<?php echo($valor_texto); ?>" maxlength="10" class="no-margin no-padding" style="width:50px; display:inline;" onkeypress="return solo_numeros(event, false);" onkeyup="cambiar_texto_rec_oft('<?php echo($nombre_componente); ?>', '<?php echo($sufijo_ojo); ?>', '<?php echo($sufijo); ?>', '<?php echo("_".$i); ?>');" />
                <?php
									break;
								case "2": //Decimal
				?>
                <input type="text" id="<?php echo($id_texto_valor.$sufijo."_".$i); ?>" value="<?php echo($valor_texto); ?>" maxlength="10" class="no-margin no-padding" style="width:50px; display:inline;" onkeypress="return solo_numeros(event, true);" onkeyup="cambiar_texto_rec_oft('<?php echo($nombre_componente); ?>', '<?php echo($sufijo_ojo); ?>', '<?php echo($sufijo); ?>', '<?php echo("_".$i); ?>');" />
                <?php
									break;
								case "3": //Texto
				?>
                <input type="text" id="<?php echo($id_texto_valor.$sufijo."_".$i); ?>" value="<?php echo($valor_texto); ?>" maxlength="100" class="no-margin no-padding" style="width:100px; display:inline;" onkeyup="cambiar_texto_rec_oft('<?php echo($nombre_componente); ?>', '<?php echo($sufijo_ojo); ?>', '<?php echo($sufijo); ?>', '<?php echo("_".$i); ?>');" />
                <?php
									break;
							}
							
							if ($detalle_aux["ind_tipo_valor"] != "0") {
				?>
                <input type="hidden" id="<?php echo($id_hidden."_unidad".$sufijo."_".$i); ?>" value="<?php echo($detalle_aux["nombre_unidad"]); ?>" />
                <?php
								if ($detalle_aux["nombre_detalle"] != "") {
									echo("&nbsp;".$detalle_aux["nombre_unidad"]);
								}
							}
						}
                ?>
            </div>
            <?php
					if (isset($mapa_recursiva_det[$detalle_aux["id_detalle"]])) {
						$this->mostrar_lista_rec($mapa_recursiva_det, $nombre_componente, $sufijo_ojo, $detalle_aux["id_detalle"], $sufijo."_".$i, $default_op, $valor_componente, $id_div."_cont".$sufijo."_".$i, $detalle_aux["nombre_detalle"], $subtitulo_lista);
					}
				}
			?>
            <div id="d_footer">
                <input type="button" value="Cerrar" class="btnPrincipal peq no-margin" onclick="ocultar_lista_rec_oft('<?php echo($id_componente_rec); ?>');" />
            </div>
        </div>
        <?php
		}
		
		public function obtener_lista_valores_sel($texto_base, $lista_detalle) {
			$arr_resultado = array();
			foreach ($lista_detalle as $lista_aux) {
				//Se halla el nombre compuesto del elemento de la lista
				$nombre_aux = $this->obtener_nombre_rec($lista_aux["id_detalle"], $lista_detalle);
				$ind_hallado = false;
				$valor_aux = "";
				$pos_aux = stripos(", ".$texto_base.", ", ", ".$nombre_aux.", ");
				if ($pos_aux !== false) {
					//Hallado sin valores adicionales
					$ind_hallado = true;
				} else {
					$pos_aux = stripos(", ".$texto_base.", ", ", ".$nombre_aux.": ");
					if ($pos_aux !== false) {
						//Hallado con valores adicionales
						$ind_hallado = true;
						
						$pos_aux2 = stripos(substr(", ".$texto_base.", ", $pos_aux + 1), ": ");
						$pos_aux3 = stripos(substr(", ".$texto_base.", ", $pos_aux + 1), ", ");
						if ($pos_aux2 !== false && $pos_aux3 !== false) {
							$valor_aux = trim(substr(", ".$texto_base.", ", $pos_aux + $pos_aux2 + 2, $pos_aux3 - $pos_aux2 - 1));
							
							//Se verifica si hay unidades
							if ($lista_aux["nombre_unidad"] != "") {
								$len_aux = strlen($lista_aux["nombre_unidad"]);
								$unidad_aux = trim(substr($valor_aux, strlen($valor_aux) - $len_aux));
								if ($unidad_aux == $lista_aux["nombre_unidad"]) {
									$valor_aux = trim(substr($valor_aux, 0, strlen($valor_aux) - $len_aux));
								}
							}
						}
					}
				}
				
				//Se halla el texto y se agrega al listado de resultados
				if ($ind_hallado) {
					$arr_aux = array("lista" => $lista_aux, "valor" => $valor_aux);
					array_push($arr_resultado, $arr_aux);
				}
			}
			
			return $arr_resultado;
		}
		
		private function obtener_nombre_rec_mapa($id_detalle, $mapa_detalle, $valor = "") {
			$lista_detalle = array();
			foreach ($mapa_detalle as $lista_detalle_aux) {
				foreach ($lista_detalle_aux as $detalle_aux) {
					array_push($lista_detalle, $detalle_aux);
				}
			}
			
			return $this->obtener_nombre_rec($id_detalle, $lista_detalle, $valor);
		}
		
		private function obtener_nombre_rec($id_detalle, $lista_detalle, $valor = "") {
			//Se busca el registro por su id
			$detalle_obj = array();
			$ind_hallado = false;
			foreach ($lista_detalle as $detalle_aux) {
				if ($detalle_aux["id_detalle"] == $id_detalle) {
					$detalle_obj = $detalle_aux;
					$ind_hallado = true;
					break;
				}
			}
			
			if ($ind_hallado) {
				$valor = trim($detalle_obj["nombre_detalle"]." ".$valor);
			}
			
			if ($detalle_obj["id_detalle_base"] != "" && $detalle_obj["ind_excluye_padre"] != "1") {
				$valor = $this->obtener_nombre_rec($detalle_obj["id_detalle_base"], $lista_detalle, $valor);
			}
			
			return $valor;
		}
	}
?>
