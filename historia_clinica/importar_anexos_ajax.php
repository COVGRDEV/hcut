<?php
	session_start();
	/*
	  Pagina para consultar HC de las pacientes
	  Autor: Helio Ruber López - 30/03/2014
	 */
	
	//header('Content-Type: text/xml; charset=UTF-8');
	
	require_once("../db/DbPacientes.php");
	require_once("../db/DbHistoriaClinica.php");
	require_once("../db/DbVariables.php");
	
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/Class_Barra_Progreso.php");
	require_once("../principal/ContenidoHtml.php");
	
	$dbPacientes = new DbPacientes();
	$dbHistoriaClinica = new DbHistoriaClinica();
	$dbVariables = new Dbvariables();
	
	$contenido = new ContenidoHtml();
	$funciones_persona = new FuncionesPersona();
	$utilidades = new Utilidades();
	$combo = new Combo_Box();
	$barra_progreso = new Barra_Progreso();
	
	$opcion = $_POST["opcion"];
	
	function listar_directorios_ruta($ruta) {
		// abrir un directorio y listarlo recursivo
	?>
    <table id='tabla_archivos_hc'  border='0' class="paginated modal_table" style="width: 80%; margin: auto;">
        <thead>
            <tr class='headegrid'>
                <th class="headegrid" align="center">&nbsp;</th>	
                <th class="headegrid" align="center">Archivos de las historia Cl&iacute;nicas</th>
            </tr>
        </thead>
   		<?php
			$i = 0;
			if (is_dir($ruta)) {
				if ($dh = opendir($ruta)) {
					$i = 0;
					while (($file = readdir($dh)) !== false) {
						if (is_file($ruta.'/'.$file)) {
							$i++;
							$texto_archivo = utf8_encode($file);
        ?>
        <tr class='celdagrid' id="tr_archivos_<?php echo($i);?>" >
            <td align="left">
                <input type="hidden" id="hdd_con_datos_<?php echo($i); ?>" value="1" />
                <input id="archivo_hc_<?php echo($i);?>" type="checkbox" style="margin: 1px;" value="<?php echo($texto_archivo);?>" onclick='marcar_checkbox("archivo_hc_<?php echo($i);?>");' >
            </td>	
            <td align="left" for="archivo_hc_<?php echo($i);?>"   id="td_nombre_<?php echo($i); ?>"    onclick='marcar_checkbox("archivo_hc_<?php echo($i);?>");'>
                <?php echo $texto_archivo; ?>
            </td>
        </tr>
        <?php
						}
					}
					closedir($dh);
				}
			} else {
	  	?>
	    <tr class='celdagrid'>
	        <td align="left">
	        	 <div class='msj-vacio'>
					<p>No se encontraron Archivos</p>
			     </div>
	        </td>	
	    </tr>
	    <?php
			}
		?>
    </table>
    <input type="hidden" id="hdd_cant_registros" value="<?php echo($i); ?>" />
	<script id='ajax'>
        //<![CDATA[ 
        $(function() {
            $('.paginated', 'tabla_archivos_hc').each(function(i) {
                $(this).text(i + 1);
            });

            $('table.paginated').each(function() {
                var currentPage = 0;
                var numPerPage = 5;
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
	}
	
	function get_ruta_archivo($id_hc) {
		$dbVariables = new Dbvariables();
		
		//Se obtiene la fecha actual
		$arr_fecha_act = $dbVariables->getAnoMesDia();
		
		//Se obtiene la ruta actual de las imágenes
		$arr_ruta_base = $dbVariables->getVariable(17);
		$ruta_base = $arr_ruta_base["valor_variable"];
		
		//Se obtienen los datos de la historia clinica
		$dbHistoriaClinica = new DbHistoriaClinica();
		$hc_obj = $dbHistoriaClinica->getHistoriaClinicaId($id_hc);
		
		$ruta = $ruta_base."/".$arr_fecha_act["anio_actual"]."/".$arr_fecha_act["mes_actual"]."/".
				 $arr_fecha_act["dia_actual"]."/".$hc_obj["id_paciente"]."/";
		
		return $ruta;
	}
	
	switch ($opcion) {
		case "1": //Buscar pacientes
			$id_usuario_crea = $_SESSION["idUsuario"];
			@$parametro = $utilidades->str_decode($_POST["parametro"]);
			
			$lista_pacientes = $dbPacientes->getBuscarpacientes($parametro);
			if (count($lista_pacientes) > 0) {
		?>
        <table id='tabla_pacientes_hc'  border='0' class="paginated modal_table" style="width: 50%; margin: auto;">
            <thead>
                <tr class='headegrid'>
                    <th class="headegrid" align="center" style="width:25%;">Documento</th>	
                    <th class="headegrid" align="center" style="width:75%;">Paciente</th>
                </tr>
            </thead>
            <?php
				foreach ($lista_pacientes as $paciente_aux) {
                    $nombres_apellidos = $funciones_persona->obtenerNombreCompleto($paciente_aux["nombre_1"], $paciente_aux["nombre_2"], $paciente_aux["apellido_1"], $paciente_aux["apellido_2"]);
            ?>
            <tr class='celdagrid' onclick="mostrar_form_importar(<?php echo($paciente_aux["id_paciente"]);?>);">
                <td align="center"><?php echo($paciente_aux["numero_documento"]); ?></td>	
                <td align="left"><?php echo($nombres_apellidos); ?></td>
            </tr>
            <?php
				}
			?>
        </table>
        <script id='ajax'>
			//<![CDATA[ 
			$(function() {
				$('.paginated', 'tabla_pacientes_hc').each(function(i) {
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
			} else {
		?>
        <div class='msj-vacio'>
        	<p>No se encontraron pacientes</p>
        </div>
        <?php
			}
			break;
			
	    case "2": //Mostrar formulario de carga de archivos
			@$id_paciente = $_POST["id_paciente"];
			
			$paciente_obj = $dbPacientes->getExistepaciente3($id_paciente);
			$id_tipo_documento = $paciente_obj["id_tipo_documento"];
			$numero_documento = $paciente_obj["numero_documento"];
			$nombres_apellidos = $funciones_persona->obtenerNombreCompleto($paciente_obj["nombre_1"], $paciente_obj["nombre_2"], $paciente_obj["apellido_1"], $paciente_obj["apellido_2"]);
		?>
        <input type="hidden" id="hdd_id_paciente" name="hdd_id_paciente" value="<?php echo($paciente_obj["id_paciente"]); ?>"></input>
		<div class="padding">
            <table style="width:60%;" border="0" align="center">
                <tr>
                    <td align="right" style="width:30%;">
                        <label><b><?php echo($paciente_obj["tipodocumento"]); ?>:</b>&nbsp;</label>
					</td>
                    <td align="left" style="width:70%;">
						<label><?php echo($paciente_obj["numero_documento"]); ?></label>
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        <label><b>Nombre:</b>&nbsp;</label>
					</td>
                    <td align="left">
						<label><?php echo($nombres_apellidos); ?></label>
                    </td>
                </tr>
                <tr style="height:20px;"></tr>
                <tr>
                    <td align="right">
                    	<label class="inline"><b>Tipo de anexo:</b>&nbsp;</label>
                    </td>
                    <td align="left">
	                    <input type="text" id="txt_nombre_alt_tipo_reg" maxlength="100" style="width:100%;" />
                    </td>
                </tr>
                <tr>
                    <td align="right">
                    	<label class="inline"><b>Seleccione los archivos:</b>&nbsp;</label>
                    </td>
                    <td align="left">
                        <input type="file" id="fil_arch_adjunto" name="fil_arch_adjunto[]" multiple="multiple" accept="image/*, .pdf" />
                    </td>
                </tr>
                <tr>
                    <td align="right">
                    	<label class="inline"><b>Observaciones:</b>&nbsp;</label>
                    </td>
                    <td align="left">
                        <textarea id="txt_observaciones_hc" class="textarea_ajustable"></textarea>
                    </td>
                </tr>
                 <tr>
                <td align="center">
                	<label class="inline"><b>¿Autoriza el tratamiento de datos personales?</b>&nbsp;</label>
				</td>
					<td align="rigth">
						<?php $ind_habeas_data = $ind_habeas_data == "1" ? $ind_habeas_data : $ind_habeas_data="";  ?>
						<input type="checkbox" onchange="validar_check(1);" id="ind_habeas_1" name="ind_habeas_1" value="1" ><label><b>S&iacute;</b></label>
						<input type="checkbox" onchange="validar_check(0);" id="ind_habeas_0" name="ind_habeas_0" value="" ><label><b>No</b></label>
						<input type="hidden" id="ind_habeas_db" name="ind_habeas_db" value="<?= $ind_habeas_data ?>" />
					</td>
				</tr>
                <tr>
                	<td align="center" colspan="2">
                        <input type="button" id="btn_cargar_anexo" name="btn_cargar_anexo" class="btnPrincipal" value="Agregar anexo" onclick="cargar_archivo_anexo();" />
                        <div id="d_guardar_arch_adjunto" style="display:none;"></div>
                    </td>
                <tr>
                <tr>
                	<td align="center" colspan="2">
                        <?php
							$barra_progreso->get("d_barra_progreso_adj", "50%", false, 0);
						?>
                    </td>
                </tr>
            </table>
        </div>
	    <?php
			break;
			
		case "3": //Cargar los archivos anexos y crear el registro de historia clínica
			$id_usuario = $_SESSION["idUsuario"];
			@$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
			@$nombre_alt_tipo_reg = $utilidades->str_decode($_POST["nombre_alt_tipo_reg"]);
			@$observaciones_hc = $utilidades->str_decode($_POST["observaciones_hc"]);
			@$ind_habeas_data = $utilidades->str_decode($_POST["ind_habeas_data"]);
			
			//Se crea el registro de historia clínica
			$id_hc = $dbHistoriaClinica->crear_historia_clinica_alt($id_paciente, 18, 2, "", $nombre_alt_tipo_reg, "", $id_usuario, $observaciones_hc, $ind_habeas_data);
			
			if ($id_hc > 0) {
				$gs = $dbVariables->getVariable(18);
				$gs = $gs["valor_variable"];
				
				//Se crea la carpeta para los archivos
				$ruta_arch_adjunto = get_ruta_archivo($id_hc);
				@mkdir($ruta_arch_adjunto, 0755, true);
				
				//Listado de formatos permitidos
				$arr_formatos = array("jpg", "png", "bmp", "gif", "pdf");
				
				//Se busca el siguiente contador de archivos para el examen
				$cont_aux_obj = $dbHistoriaClinica->getSiguienteContArchAdjunto($id_hc);
				$cont_aux = $cont_aux_obj["cont_arch"];
				
				if (isset($_FILES["fil_arch_adjunto"])) {
					//Se cargan los nombres de los archivos
					$arr_nombres_aux = $_FILES["fil_arch_adjunto"]["name"];
					$arr_tmp_nombres_aux = $_FILES["fil_arch_adjunto"]["tmp_name"];
					
					if (!is_array($arr_nombres_aux)) {
						$arr_nombres_aux = array($arr_nombres_aux);
						$arr_tmp_nombres_aux = array($arr_tmp_nombres_aux);
					}
					
					foreach ($arr_nombres_aux as $k => $nombre_ori_aux) {
						$extension_aux = $utilidades->get_extension_arch($nombre_ori_aux);
						if (in_array($extension_aux, $arr_formatos)) {
							@$nombre_tmp = $arr_tmp_nombres_aux[$k];
							
							if ($extension_aux == "pdf") {
								//Si se trata de un documento pdf, se convierte cada página a jpg y se guardan como archivos separados
								$prefijo_aux = $ruta_arch_adjunto.$id_hc."_adjunto_hc_".$cont_aux."_";
								$comando_aux = "\"".$gs."\" -dNOPAUSE -sDEVICE=jpeg -dUseCIEColor -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -r150x150 -dJPEGQ=90 -sOutputFile=\"".$prefijo_aux."%d.jpg\" \"".$nombre_tmp."\" -c quit";
								$salida_aux = array();
								exec($comando_aux, $salida_aux, $resultado_aux);
								
								if ($resultado_aux == "0") {
									$cont_aux2 = 1;
									$ruta_imagen_tmp = $prefijo_aux.$cont_aux2.".jpg";
									while (file_exists($ruta_imagen_tmp)) {
										//Se agrega el registro de archivo a la base de datos
										$dbHistoriaClinica->crearHCArchivoAdjunto($id_hc, $ruta_imagen_tmp, $cont_aux, 1, $id_usuario);
										
										$cont_aux2++;
										$ruta_imagen_tmp = $prefijo_aux.$cont_aux2.".jpg";
										
										$cont_aux++;
									}
								}
							} else {
								//Se obtiene el nombre que tendrá el archivo
								$nombre_arch = $dbHistoriaClinica->construir_nombre_arch($id_hc, $nombre_ori_aux, "adjunto_hc", $cont_aux);
								
								//Se copia el archivo
								copy($nombre_tmp, $nombre_arch);
								
								//Se agrega el registro de archivo a la base de datos
								$id_archivo_aux = $dbHistoriaClinica->crearHCArchivoAdjunto($id_hc, $nombre_arch, $cont_aux, 1, $id_usuario);
								
								$cont_aux++;
							}
						}
					}
				}
			}
		?>
        <input type="hidden" id="hdd_id_hc_adjunto" value="<?php echo($id_hc); ?>" />
        <?php
			break;
	}
?>
