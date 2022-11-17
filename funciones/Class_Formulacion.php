<?php
	/*
		Pagina para crear formulaciones de medicamentos
		Autor: Feisar Moreno - 29/12/2016
	*/
	require_once("../db/DbListas.php");
	require_once("../db/DbFormulacionHC.php");
	require_once("../db/DbMaestroMedicamentos.php");
	require_once("../db/DbAdmision.php");
	require_once("../funciones/Utilidades.php");
	
	class Class_Formulacion {
		/**
		 * cod_tipo_medicamento
		 * 1 - Genéricos
		 * 2 - Comerciales
		 **/
		public function iniciarComponentesFormulacion($cod_tipo_medicamento, $id_hc = "") {
			$dbMaestroMedicamentos = new DbMaestroMedicamentos();
			$dbListas = new DbListas();
			$dbAdmision = new DbAdmision();
			
			$lista_medicamentos = array();
			if ($id_hc != "") {
				//Se buscan el convenio y el plan de la admisión asociada al registro de historia clínica
				$admision_obj = $dbAdmision->get_admision_hc($id_hc);
				if (isset($admision_obj["id_admision"])) {
					$lista_medicamentos = $dbMaestroMedicamentos->getListaMedicamentosConvenioPlan("1", $cod_tipo_medicamento,
							$admision_obj["id_convenio"], $admision_obj["id_plan"], $admision_obj["id_lugar_cita"]);
				}
				if (count($lista_medicamentos) == 0) {
					$lista_medicamentos = $dbMaestroMedicamentos->getListaMedicamentosNombreTipo("1", $cod_tipo_medicamento, $admision_obj["id_lugar_cita"]);
				}
			}
			
			if (count($lista_medicamentos) == 0) {
				$lista_medicamentos = $dbMaestroMedicamentos->getListaMedicamentosNombreTipo("1", $cod_tipo_medicamento, "");
			}
			$lista_dosificaciones = $dbListas->getListaDetalles(46, 1);
			$lista_unidades = $dbListas->getListaDetalles(47, 1);
			$lista_duraciones = $dbListas->getListaDetalles(48, 1);
			
			//Array para nombres de medicamentos
			$cadena_medicamentos_fm = "";
			
			foreach ($lista_medicamentos as $medicamento_aux) {
				if ($cadena_medicamentos_fm != "") {
					$cadena_medicamentos_fm .= ",";
				}
				
				$nombre_medicamento_aux = str_replace("'", "", str_replace("#", "", $medicamento_aux["nombre_medicamento"]));
				$cod_medicamento_aux = str_replace("'", "", str_replace("#", "", $medicamento_aux["cod_medicamento"]));
				$presentacion_aux = str_replace("'", "", str_replace("#", "", $medicamento_aux["presentacion"]));
				$cod_tipo_medicamento_aux = str_replace("'", "", str_replace("#", "", $medicamento_aux["cod_tipo_medicamento"]));
				$grupo_terapeutico = str_replace("'", "", str_replace("#", "", $medicamento_aux["grupo_terapeutico"]));
				$laboratorio = str_replace("'", "", str_replace("#", "", $medicamento_aux["laboratorio"]));
				
				if ($laboratorio != "") {
					$nombre_medicamento_aux .= " - ".$laboratorio;
				}
		
				$cadena_medicamentos_fm .= "'".$nombre_medicamento_aux." [# ".$presentacion_aux." #".$cod_medicamento_aux."#".$cod_tipo_medicamento_aux."#".$grupo_terapeutico."#]'";

			}
			
			//var_dump($cadena_medicamentos_fm);
			
			//Array para dosificaciones
			$cadena_dosificaciones_fm = "";
			foreach ($lista_dosificaciones as $dosificacion_aux) {
				if ($cadena_dosificaciones_fm != "") {
					$cadena_dosificaciones_fm .= ",";
				}
				
				$nombre_dosificacion_aux = str_replace("'", "", str_replace("#", "", $dosificacion_aux["nombre_detalle"]));
				
				$cadena_dosificaciones_fm .= "'".$nombre_dosificacion_aux."'";
			}
			
			//Array para unidades
			$cadena_unidades_fm = "";
			foreach ($lista_unidades as $unidad_aux) {
				if ($cadena_unidades_fm != "") {
					$cadena_unidades_fm .= ",";
				}
				
				$nombre_unidad_aux = str_replace("'", "", str_replace("#", "", $unidad_aux["nombre_detalle"]));
				
				$cadena_unidades_fm .= "'".$nombre_unidad_aux."'";
			}
			
			//Array para duraciones
			$cadena_duraciones_fm = "";
			foreach ($lista_duraciones as $duracion_aux) {
				if ($cadena_duraciones_fm != "") {
					$cadena_duraciones_fm .= ",";
				}
				
				$nombre_duracion_aux = str_replace("'", "", str_replace("#", "", $duracion_aux["nombre_detalle"]));
				
				$cadena_duraciones_fm .= "'".$nombre_duracion_aux."'";
			}
	?>
    <script id="ajax">
		var array_medicamentos_fm = [<?php echo($cadena_medicamentos_fm) ?>];
		var array_dosificaciones_fm = [<?php echo($cadena_dosificaciones_fm) ?>];
		var array_unidades_fm = [<?php echo($cadena_unidades_fm) ?>];
		var array_duraciones_fm = [<?php echo($cadena_duraciones_fm) ?>];
		
		$(function() {
			var Tags_medicamentos_fm = [<?php echo($cadena_medicamentos_fm) ?>];
			var Tags_dosificaciones_fm = [<?php echo($cadena_dosificaciones_fm) ?>];
			var Tags_unidades_fm = [<?php echo($cadena_unidades_fm) ?>];
			var Tags_duraciones_fm = [<?php echo($cadena_duraciones_fm) ?>];
			
			for (var i = 0; i < 20; i++) {
				$("#txt_nombre_medicamento_fm_" + i).autocomplete({ source: Tags_medicamentos_fm });
				$("#txt_dosificacion_fm_" + i).autocomplete({ source: Tags_dosificaciones_fm });
				$("#txt_unidades_fm_" + i).autocomplete({ source: Tags_unidades_fm });
				$("#txt_duracion_fm_" + i).autocomplete({ source: Tags_duraciones_fm });
			}
		});
	</script>
    <?php
		}
		
		public function getFormularioFormulacion($id_hc, $ind_crear_medicamento = 0) {
			$dbListas = new DbListas();
			$dbFormulacionHC = new DbFormulacionHC();
			
			$lista_dosificaciones = $dbListas->getListaDetalles(46);
			$lista_unidades = $dbListas->getListaDetalles(47);
			$lista_duraciones = $dbListas->getListaDetalles(48);
			
			$lista_formulacion_hc = $dbFormulacionHC->getListaFormulacionHC($id_hc);
			
			$cant_formulaciones = count($lista_formulacion_hc);
			if ($cant_formulaciones == 0) {
				$cant_formulaciones = 1;
			}
	?>
	<input type="hidden" name="hdd_cant_formulaciones" id="hdd_cant_formulaciones" value="<?php echo($cant_formulaciones);?>" />
    <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%;">
    	<tr>
        	<td align="right" colspan="9">
            	<div id="d_formulacion_anterior_fm" style="display:none;"></div>
            	<div class="texto_subtitulo" style="float:right; cursor:pointer;" onclick="copiar_formulacion_anterior_fm(<?php echo($id_hc); ?>);" >
                	&nbsp;&nbsp;&nbsp;&nbsp;Copiar formulaci&oacute;n anterior
                	<img src="../imagenes/copy_opt.png" class="img_button no-margin" title="Copiar formulaci&oacute;n anterior" />
                </div>
            	<div class="texto_subtitulo" style="float:right; cursor:pointer;" onclick="mostrar_crear_medicamento_fm(<?php echo($id_hc); ?>);" >
                	Crear medicamento
                	<img src="../imagenes/add_elemento.png" class="img_button no-margin" title="Crear medicamento" />
                </div>
            </td>
        </tr>
    	<?php
			for ($i = 0; $i < 20; $i++) {
				if (count($lista_formulacion_hc) > $i) {
					$formulacion_hc_aux = $lista_formulacion_hc[$i];
					
					$cod_medicamento = $formulacion_hc_aux["cod_medicamento"];
					$nombre_medicamento = $formulacion_hc_aux["nombre_medicamento"];
					$cod_tipo_medicamento = $formulacion_hc_aux["cod_tipo_medicamento"];
					$presentacion = $formulacion_hc_aux["presentacion"];
					$cantidad = $formulacion_hc_aux["cantidad"];
					$dosificacion = $formulacion_hc_aux["dosificacion"];
					$unidades = $formulacion_hc_aux["unidades"];
					$duracion = $formulacion_hc_aux["duracion"];
				} else {
					$cod_medicamento = "";
					$nombre_medicamento = "";
					$cod_tipo_medicamento = "";
					$presentacion = "";
					$cantidad = "";
					$dosificacion = "";
					$unidades = "";
					$duracion = "";
				}
		?>
    	<tr id="tr_formulacion_fm_<?php echo($i); ?>" <?php if ($i >= $cant_formulaciones) { ?>style="display:none;"<?php } ?>>
        	<td align="right" style="width:10%;">
            	<label>Medicamento:</label>
            </td>
            <td align="left" style="width:57%;" colspan="5">
                <input type="hidden" name="hdd_cod_medicamento_fm_<?php echo($i);?>" id="hdd_cod_medicamento_fm_<?php echo($i);?>" value="<?php echo($cod_medicamento);?>" />
                <input type="hidden" name="hdd_cod_tipo_medicamento_fm_<?php echo($i);?>" id="hdd_cod_tipo_medicamento_fm_<?php echo($i);?>" value="<?php echo($cod_tipo_medicamento);?>" />
                <input type="text" name="txt_nombre_medicamento_fm_<?php echo($i);?>" id="txt_nombre_medicamento_fm_<?php echo($i);?>" value="<?php echo($nombre_medicamento);?>" maxlength="256" class="no-margin" onblur="trim_cadena(this); procesar_seleccion_fm(<?php echo($i);?>);" onkeyup="procesar_seleccion_fm(<?php echo($i);?>);" />
            </td>
        	<td align="right" style="width:10%;">
            	<label>Presentaci&oacute;n:</label>
            </td>
            <td align="left" style="width:18%;">
            	<input type="text" name="txt_presentacion_fm_<?php echo($i);?>" id="txt_presentacion_fm_<?php echo($i);?>" value="<?php echo($presentacion);?>" maxlength="256" class="no-margin" onblur="trim_cadena(this);" />
            </td>
            <td align="center" valign="middle" style="width:5%;" rowspan="2">
            	<img src="../imagenes/Error-icon.png" class="img_button no-margin" title="Borrar registro" onclick="borrar_medicamento_fm(<?php echo($i); ?>);" />
            </td>
        </tr>
        <tr id="tr_formulacion_fm2_<?php echo($i); ?>" <?php if ($i >= $cant_formulaciones) { ?>style="display:none;"<?php } ?>>
        	<td align="right">
            	<label>Cantidad:</label>
            </td>
            <td align="left" style="width:6%;">
            	<input type="text" name="txt_cantidad_fm_<?php echo($i);?>" id="txt_cantidad_fm_<?php echo($i);?>" value="<?php echo($cantidad);?>" maxlength="3" class="no-margin" onkeypress="return solo_numeros(event, false);" />
            </td>
        	<td align="right" style="width:8%;">
            	<label>Dosificaci&oacute;n:</label>
            </td>
            <td align="left" style="width:18%;">
            	<input type="text" name="txt_dosificacion_fm_<?php echo($i);?>" id="txt_dosificacion_fm_<?php echo($i);?>" value="<?php echo($dosificacion);?>" maxlength="256" class="no-margin" onblur="trim_cadena(this);" />
            </td>
        	<td align="right" style="width:7%;">
            	<label>Unidades:</label>
            </td>
            <td align="left" style="width:18%;">
            	<input type="text" name="txt_unidades_fm_<?php echo($i);?>" id="txt_unidades_fm_<?php echo($i);?>" value="<?php echo($unidades);?>" maxlength="256" class="no-margin" onblur="trim_cadena(this);" />
            </td>
        	<td align="right">
            	<label>Duraci&oacute;n:</label>
            </td>
            <td align="left">
            	<input type="text" name="txt_duracion_fm_<?php echo($i);?>" id="txt_duracion_fm_<?php echo($i);?>" value="<?php echo($duracion);?>" maxlength="256" class="no-margin" onblur="trim_cadena(this);" />
            </td>
        </tr>
        <tr id="tr_formulacion_fm3_<?php echo($i); ?>" <?php if ($i >= $cant_formulaciones) { ?>style="display:none;"<?php } ?>>
        	<td><div class="div_separador"></div></td>
        </tr>
        <?php
			}
		?>
    	<tr>
        	<td>
            	<div class="agregar_alemetos" onclick="agregar_medicamento_fm();"></div>
                <div class="restar_alemetos" onclick="restar_medicamento_fm();"></div>
            </td>
        </tr>
    </table>
    <?php	
		}
		
		public function guardarFormulacionHC($id_hc, $id_usuario) {
			$dbFormulacionHC = new DbFormulacionHC();
			$utilidades = new Utilidades();
			
			@$cant_formulaciones = intval($_POST["cant_formulaciones_fm"], 10);
			
			$lista_formulaciones = array();
			for ($i = 0; $i < $cant_formulaciones; $i++) {
				@$lista_formulaciones[$i]["nombre_medicamento"] = $utilidades->str_decode($_POST["nombre_medicamento_fm_".$i]);
				@$lista_formulaciones[$i]["cod_medicamento"] = $utilidades->str_decode($_POST["cod_medicamento_fm_".$i]);
				@$lista_formulaciones[$i]["cod_tipo_medicamento"] = $utilidades->str_decode($_POST["cod_tipo_medicamento_fm_".$i]);
				@$lista_formulaciones[$i]["presentacion"] = $utilidades->str_decode($_POST["presentacion_fm_".$i]);
				@$lista_formulaciones[$i]["cantidad"] = intval($_POST["cantidad_fm_".$i], 10);
				@$lista_formulaciones[$i]["dosificacion"] = $utilidades->str_decode($_POST["dosificacion_fm_".$i]);
				@$lista_formulaciones[$i]["unidades"] = $utilidades->str_decode($_POST["unidades_fm_".$i]);
				@$lista_formulaciones[$i]["duracion"] = $utilidades->str_decode($_POST["duracion_fm_".$i]);
			}
			
			//Se guarda la formulación
			$resultado = $dbFormulacionHC->crearEditarFormulacionesHC($id_hc, $lista_formulaciones, $id_usuario);
			
			return $resultado;
		}
	}
?>
