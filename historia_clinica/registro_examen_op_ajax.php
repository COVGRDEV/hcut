<?php session_start();
	/*
	 * Pagina para crear consulta de optometria
	 * Autor: Feisar Moreno - 14/02/2014
	 */
 	header("Content-Type: text/xml; charset=UTF-8");
	
	require_once("../db/DbExamenesOptometria.php");
	require_once("../db/DbMenus.php");
	require_once("../db/DbVariables.php");
	require_once("../funciones/Utilidades.php");
	
	$dbExamenesOptometria = new DbExamenesOptometria();
	$dbMenus = new DbMenus();
	$dbVariables = new Dbvariables();
	$utilidades = new Utilidades();
	
	function get_ruta_archivo($id_hc) {
		$dbVariables = new Dbvariables();
		
		//Se obtiene la fecha actual
		$arr_fecha_act = $dbVariables->getAnoMesDia();
		
		//Se obtiene la ruta actual de las imágenes
		$arr_ruta_base = $dbVariables->getVariable(17);
		$ruta_base = $arr_ruta_base["valor_variable"];
		
		//Se obtienen los datos de la historia clinica
		$dbExamenesOptometria = new DbExamenesOptometria();
		$hc_obj = $dbExamenesOptometria->getHistoriaClinicaId($id_hc);
		
		$ruta = $ruta_base."/".$arr_fecha_act["anio_actual"]."/".$arr_fecha_act["mes_actual"]."/".
				 $arr_fecha_act["dia_actual"]."/".$hc_obj["id_paciente"]."/";
		
		return $ruta;
	}
	
	$opcion = $utilidades->str_decode($_POST["opcion"]);
	
	switch ($opcion) {
		case "1": //Guardar Examen
			$id_usuario = $_SESSION["idUsuario"];
			@$id_hc = $utilidades->str_decode($_POST["id_hc"]);
			@$id_admision = $utilidades->str_decode($_POST["id_admision"]);
			@$nombre_usuario_alt = $utilidades->str_decode($_POST["nombre_usuario_alt"]);
			@$tipo_guardar = $utilidades->str_decode($_POST["tipo_guardar"]);
			@$cant_examenes = intval($utilidades->str_decode($_POST["cant_examenes"]), 10);
			
			//Se limpia el temporal de examenes
			$dbExamenesOptometria->borrar_temporal_examenes($id_usuario);
			
			for ($i = 0; $i < $cant_examenes; $i++) {
				@$id_examen = trim($utilidades->str_decode($_POST["id_examen_".$i]));
				@$id_ojo = trim($utilidades->str_decode($_POST["id_ojo_examen_".$i]));
				@$observaciones_examen = trim($utilidades->str_decode($_POST["observaciones_examen_".$i]));
				@$id_examen_hc = trim($utilidades->str_decode($_POST["id_examen_hc_".$i]));
				@$pu_od = $utilidades->str_decode($_POST["pu_od_".$i]);
				@$pu_oi = $utilidades->str_decode($_POST["pu_oi_".$i]);
				
				//Se agrega el registro al temporal de archivos
				$dbExamenesOptometria->crear_temporal_examen_op_hc($id_usuario, $id_examen, $id_ojo, $observaciones_examen, $id_examen_hc, $pu_od, $pu_oi);
			}
			
			$cant_ciex = $utilidades->str_decode($_POST["cant_ciex"]);
			$array_diagnosticos = array();
			for ($i = 1; $i <= $cant_ciex; $i++) {
				if(isset($_POST["cod_ciex_".$i])){
					$ciex_diagnostico = $utilidades->str_decode($_POST["cod_ciex_".$i]);
					$valor_ojos = $utilidades->str_decode($_POST["val_ojos_".$i]);
					$array_diagnosticos[$i][0]=$ciex_diagnostico;
					$array_diagnosticos[$i][1]=$valor_ojos;
				}	
			}
			
			$resultado = $dbExamenesOptometria->editar_examen_optometria($id_hc, $id_admision, $nombre_usuario_alt, $array_diagnosticos, $tipo_guardar, $id_usuario);
			$reg_menu = $dbMenus->getMenu(13);
			
			//Se cargan los archivos físicos adjuntos
			if (count($_FILES) > 0) {
				$gs = $dbVariables->getVariable(18);
				$gs = $gs["valor_variable"];
				
				//Se crea la carpeta para los archivos
				$ruta_arch_examen = get_ruta_archivo($id_hc);
				@mkdir($ruta_arch_examen, 0755, true);
				
				//Listado de formatos permitidos
				$arr_formatos = array("jpg", "png", "bmp", "gif", "mp3", "mp4", "avi", "wmv", "pdf");
				
				for ($i = 0; $i < $cant_examenes; $i++) {
					@$id_examen = trim($utilidades->str_decode($_POST["id_examen_".$i]));
					@$id_ojo = trim($utilidades->str_decode($_POST["id_ojo_examen_".$i]));
				
					//Se busca el identificador del examen hc
					$id_examen_hc = "";
					$obj_aux = $dbExamenesOptometria->get_examen_optometria_hc($id_hc, $id_examen, $id_ojo);
					if (isset($obj_aux["id_examen_hc"])) {
						$id_examen_hc = $obj_aux["id_examen_hc"];
					}
					
					if ($id_examen_hc != "") {
						//Se busca el siguiente contador de archivos para el examen
						$cont_aux_obj = $dbExamenesOptometria->get_siguiente_cont_arch_det($id_examen_hc);
						$cont_aux = $cont_aux_obj["cont_arch"];
						
						@$cant_archivos = intval($_POST["cant_archivos_".$i], 10);
						for ($j = 0; $j < $cant_archivos; $j++) {
							if (isset($_FILES["fil_arch_examen_".$i."_".$j])) {
								//Se cargan los nombres de los archivos
								$arr_nombres_aux = $_FILES["fil_arch_examen_".$i."_".$j]["name"];
								$arr_tmp_nombres_aux = $_FILES["fil_arch_examen_".$i."_".$j]["tmp_name"];
								
								if (!is_array($arr_nombres_aux)) {
									$arr_nombres_aux = array($arr_nombres_aux);
									$arr_tmp_nombres_aux = array($arr_tmp_nombres_aux);
								}
								
								foreach ($arr_nombres_aux as $k => $nombre_ori_aux) {
									$extension_aux = $utilidades->get_extension_arch($nombre_ori_aux);
									if (in_array($extension_aux, $arr_formatos)) {
										@$nombre_tmp = $_FILES["fil_arch_examen_".$i."_".$j]["tmp_name"][$k];
										
										if ($extension_aux == "pdf") {
											//Si se trata de un documento pdf, se convierte cada página a jpg y se guardan como archivos separados
											$prefijo_aux = $ruta_arch_examen.$id_hc."_examen_op_".$i."_".$cont_aux."_";
											$comando_aux = "\"".$gs."\" -dNOPAUSE -sDEVICE=jpeg -dUseCIEColor -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -r150x150 -dJPEGQ=90 -sOutputFile=\"".$prefijo_aux."%d.jpg\" \"".$nombre_tmp."\" -c quit";
											$salida_aux = array();
											exec($comando_aux, $salida_aux, $resultado_aux);
											
											if ($resultado_aux == "0") {
												$cont_aux2 = 1;
												$ruta_imagen_tmp = $prefijo_aux.$cont_aux2.".jpg";
												while (file_exists($ruta_imagen_tmp)) {
													//Se agrega el registro de archivo a la base de datos
													$dbExamenesOptometria->crear_examen_optometria_hc_det($id_examen_hc, $ruta_imagen_tmp, $cont_aux, $id_usuario);
													
													$cont_aux2++;
													$ruta_imagen_tmp = $prefijo_aux.$cont_aux2.".jpg";
												}
											}
										} else {
											//Se genera el nombre del archivo
											$nombre_aux = $ruta_arch_examen.$id_hc."_examen_op_".$i."_".$cont_aux.".".$extension_aux;
											
											//Se copia el archivo
											copy($nombre_tmp, $nombre_aux);
											
											//Se agrega el registro de archivo a la base de datos
											$dbExamenesOptometria->crear_examen_optometria_hc_det($id_examen_hc, $nombre_aux, $cont_aux, $id_usuario);
										}
										
										$cont_aux++;
									}
								}
							}
						}
					}
				}
			}
		?>
		<input type="hidden" name="hdd_exito" id="hdd_exito" value="<?php echo($resultado); ?>" />
		<input type="hidden" name="hdd_url_menu" id="hdd_url_menu" value="<?php echo($reg_menu["pagina_menu"]); ?>" />
		<input type="hidden" name="hdd_tipo_guardar" id="hdd_tipo_guardar" value="<?php echo($tipo_guardar); ?>" />
		<div class="contenedor_error" id="contenedor_error"></div>
		<div class="contenedor_exito" id="contenedor_exito"></div>
		<?php
			break;
	}
?>
