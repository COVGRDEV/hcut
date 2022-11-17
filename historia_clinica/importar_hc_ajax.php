<?php
	session_start();
	/*
	  Pagina para consultar HC de las pacientes
	  Autor: Helio Ruber López - 30/03/2014
	 */
	
	header("Content-Type: text/xml; charset=UTF-8");
	
	require_once("../db/DbListas.php");
	require_once("../db/DbHistoriaClinica.php");
	require_once("../db/DbMenus.php");
	require_once("../db/DbImportarHc.php");
	require_once("../db/DbVariables.php");
	require_once("../db/DbPacientes.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/Class_Barra_Progreso.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/Utilidades.php");
	require_once("../principal/ContenidoHtml.php");
	
	$dbListas = new DbListas();
	$dbHistoriaClinica = new DbHistoriaClinica();
	$dbMenus = new DbMenus();
	$dbImportarHc = new DbImportarHc();
	$dbVariables = new Dbvariables();
	$dbPacientes = new DbPacientes();
	
	$combo = new Combo_Box();
	$barra_progreso = new Barra_Progreso();
	$contenido = new ContenidoHtml();
	$funciones_persona = new FuncionesPersona();
	$utilidades = new Utilidades();
	
	$contenido->validar_seguridad(1);
	
	$opcion = $utilidades->str_decode($_POST["opcion"]);
	
	function get_ruta_archivo($id_paciente) {
		$dbVariables = new Dbvariables();
		
		//Se obtiene la ruta actual de las imágenes
		$arr_ruta_base = $dbVariables->getVariable(17);
		$ruta_base = $arr_ruta_base["valor_variable"];
		
		//Se obtiene la fecha actual
		$arr_fecha_act = $dbVariables->getAnoMesDia();
		
		$ruta = $ruta_base."/".$arr_fecha_act["anio_actual"]."/".$arr_fecha_act["mes_actual"]."/".
				 $arr_fecha_act["dia_actual"]."/".$id_paciente."/";
		
		return $ruta;
	}
	
	switch ($opcion) {
		case "1": //Búsqueda de pacientes
			@$txt_paciente_hc = $utilidades->str_decode($_POST["txt_paciente_hc"]);
			
			$tabla_personas = $dbHistoriaClinica->getPacientesHistoriaClinica($txt_paciente_hc);
			
			if (count($tabla_personas) > 0) {
    ?>
    <table id="tabla_persona_hc"  border="0" class="paginated modal_table" style="width: 70%; margin: auto;">
        <thead>
            <tr class="headegrid">
                <th class="headegrid" align="center" style="width:22%;">Documento</th>
                <th class="headegrid" align="center" style="width:58%;">Pacientes</th>
                <th class="headegrid" align="center" style="width:20%;">HC antigua registrada</th>
            </tr>
        </thead>
        <?php
				foreach ($tabla_personas as $fila_personas) {
					$id_paciente = $fila_personas["id_paciente"];
					$nombre_1 = $fila_personas["nombre_1"];
					$nombre_2 = $fila_personas["nombre_2"];
					$apellido_1 = $fila_personas["apellido_1"];
					$apellido_2 = $fila_personas["apellido_2"];
					$numero_documento = $fila_personas["numero_documento"];
					$cod_tipo_documento = $fila_personas["cod_tipo_documento"];
					$nombre_completo = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
					
					$cantidad_hc_ant = intval($fila_personas["cantidad_hc_ant"], 10);
					if ($cantidad_hc_ant > 0) {
						$texto_registrada = "S&iacute;";
						$class_registrada = "activo";
					} else {
						$texto_registrada = "No";
						$class_registrada = "inactivo";
					}
		?>
        <tr class="celdagrid" onclick="cargar_formulario_crear_hc(<?php echo($id_paciente); ?>);">
            <td align="center"><?php echo($cod_tipo_documento." ".$numero_documento); ?></td>	
            <td align="left"><?php echo($nombre_completo); ?></td>
            <td align="center"><span class="<?php echo($class_registrada); ?>"><?php echo($texto_registrada); ?></span></td>
        </tr>
        <?php
				}
		?>
    </table>
    <script id="ajax">
		//<![CDATA[ 
		$(function() {
			$(".paginated", "tabla_persona_hc").each(function(i) {
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
    <?php
			} else {
	?>
    <div class="msj-vacio">
        <p>No se encontraron pacientes</p>
    </div>
    <input class="btnPrincipal" type="submit" value="Importar y Crear Historia Cl&iacute;nica" id="btn_consultar" name="btn_consultar" onclick="cargar_formulario_crear_hc(0);" />
    <?php
			}
			break;
			
		case "2": //Formulario de registro de datos del paciente
			@$id_paciente = intval($_POST["id_paciente"], 10);
			if ($id_paciente > 0) {
				$tbl_pacientes = $dbPacientes->getExistepaciente3($id_paciente);
				$id_tipo_documento_aux = $tbl_pacientes["id_tipo_documento"];
				$numero_documento_aux = $tbl_pacientes["numero_documento"];
				$sexo_aux = $tbl_pacientes["sexo"];
				$nombre_1_aux = $tbl_pacientes["nombre_1"];
				$nombre_2_aux = $tbl_pacientes["nombre_2"];
				$apellido_1_aux = $tbl_pacientes["apellido_1"];
				$apellido_2_aux = $tbl_pacientes["apellido_2"];	
				$activo_caja = "readonly";
				$activo_cmb = "0";
			} else {
				$id_tipo_documento_aux = "";
				$numero_documento_aux = "";
				$sexo_aux = "";
				$nombre_1_aux = "";
				$nombre_2_aux = "";
				$apellido_1_aux = "";
				$apellido_2_aux = "";
				$activo_caja = "";
				$activo_cmb = "";
			}
	?>
    <input type="hidden" id="hdd_id_paciente" name="hdd_id_paciente" value="<?php echo($id_paciente); ?>"></input>
	<div class="padding">
		<fieldset style="">
            <legend>Datos del paciente:</legend>
            <table style="width:100%;" border="0">
                <tr>
                    <td align="left" style="width:25%;">
                        <label>Tipo de Identificaci&oacute;n*</label>
                    </td>
                    <td align="left" style="width:25%;">
                        <label>Nro. de identificaci&oacute;n*</label>
                    </td>
                    <td align="left" style="width:25%;">
                    	<label>Sexo*</label>
                    </td>
                    <td align="left" style="width:25%;"></td>
                </tr>
                <tr>
                    <td align="left">
                        <?php
							$lista_tipos_documento = $dbListas->getTipodocumento();
                        	$combo->getComboDb("cmb_tipo_id", $id_tipo_documento_aux, $lista_tipos_documento, "id_detalle, nombre_detalle", "Seleccione el tipo de documento", "", $activo_cmb, "width: 100%;");
						?>
                    </td>
                    <td align="left">
                        <input type="text" id="txt_id" name="txt_id" value="<?php echo($numero_documento_aux); ?>" style="width:100%;" <?php echo($activo_caja);?> onblur="trim_cadena(this);" />
                    </td>
                    <td align="left">
                        <?php
							$lista_sexos = $dbListas->getTipoSexo();
                        	$combo->getComboDb("cmb_sexo", $sexo_aux, $lista_sexos, "id_detalle, nombre_detalle", "Seleccione el sexo", "", $activo_cmb, "width: 100%;");
						?>
                    </td>
                </tr>
                <tr>
                    <td align="left">
                        <label>Primer nombre*</label>
                    </td>
                    <td align="left">
                        <label>Segundo nombre</label>
                    </td>
                    <td align="left">
                        <label>Primer apellido*</label>
                    </td>
                    <td align="left">
                        <label>Segundo apellido</label>
                    </td>
                </tr>
                <tr>
                    <td align="left">
                        <input type="text" id="txt_nombre" name="txt_nombre" value="<?php echo($nombre_1_aux); ?>" onblur="trim_cadena(this);" <?php echo($activo_caja);?> ></input>      
                    </td>
                    <td align="left">
                        <input type="text" id="txt_nombre2" name="txt_nombre2" value="<?php echo($nombre_2_aux); ?>" onblur="trim_cadena(this);" <?php echo($activo_caja);?> ></input>
                    </td>
                    <td align="left">
                        <input type="text" id="txt_apellido" name="txt_apellido" value="<?php echo($apellido_1_aux); ?>" onblur="trim_cadena(this);" <?php echo($activo_caja);?> ></input>
                    </td>
                    <td align="left">
                        <input type="text" id="txt_apellido2" name="txt_apellido2" value="<?php echo($apellido_2_aux); ?>" onblur="trim_cadena(this);" <?php echo($activo_caja);?> ></input>
                    </td>
                </tr>
            </table>
        </fieldset>
        <fieldset style="">
            <legend>Historias Cl&iacute;nicas:</legend>
            <table id="buscar" border="0" style="width: 100%;">
                <tr>
                    <td align="left" style="width:50%;" valign="middle">
                        <input type="text" id="txt_archivo_hc" name="txt_archivo_hc" class="no-margin" style="width:100%;" placeholder="Buscar historias cl&iacute;nicas por nombre o n&uacute;mero de documento" onblur="trim_cadena(this);" />
                    </td>
                    <td align="left" style="width:50%;" valign="middle">
                        <input type="button" id="btn_buscar_hc_antiguas" value="Buscar" class="btnPrincipal peq no-margin" onclick="buscar_historias_ant();" />
                    </td>
                </tr>
                <tr style="height:10px;"></tr>
                <tr>
                    <td align="left" colspan="2">
                        <div id="d_buscar_hc_ant"></div>
                    </td>
                </tr>
            </table>
        </fieldset>
        <input id="btn_crear_hc" name="btn_crear_hc" class="btnPrincipal" type="button" value="Crear Historia Cl&iacute;nica" onclick="crear_hc();" />
        <?php
        	$barra_progreso->get("d_barra_progreso", "50%", false, 0);
		?>
    </div>
	<?php
			break;
			
		case "3": //Registrar historia clínica antigua
			$id_usuario = $_SESSION["idUsuario"];
			@$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
			@$id_tipo_documento = $utilidades->str_decode($_POST["id_tipo_documento"]);
			@$numero_documento = trim($utilidades->str_decode($_POST["numero_documento"]));
			@$sexo = $utilidades->str_decode($_POST["sexo"]);
			@$nombre_1 = $utilidades->str_decode($_POST["nombre_1"]);
			@$nombre_2 = $utilidades->str_decode($_POST["nombre_2"]);
			@$apellido_1 = $utilidades->str_decode($_POST["apellido_1"]);
			@$apellido_2 = $utilidades->str_decode($_POST["apellido_2"]);
			@$nombre_archivo_base = $utilidades->str_decode($_POST["nombre_archivo"]);
			
			$bol_arch_externo = ($nombre_archivo_base == "");
			if ($bol_arch_externo) {
				@$nombre_archivo_base = $_FILES["fil_hc_antigua"]["name"];
			}
			
			//Se obtiene la ruta de donde se leyó el archivo
			$variable_obj = $dbVariables->getVariable(11);
			$ruta_base = $variable_obj["valor_variable"];
			
			$bol_resul = false;
			$id_hc = $dbImportarHc->importar_hc($id_paciente, $id_tipo_documento, $numero_documento, $sexo, $nombre_1, $nombre_2, $apellido_1, $apellido_2, $id_usuario);
			if ($id_hc > 0) {
				if ($id_paciente <= 0) {
					//Se busca el identificador del paciente dentro del registro de historia respectivo
					$hc_obj = $dbHistoriaClinica->getHistoriaClinicaId($id_hc);
					$id_paciente = $hc_obj["id_paciente"];
				}
				
				//Se busca el nombre que tendrá el archivo
				$extension_aux = $utilidades->get_extension_arch($nombre_archivo_base);
				$ruta_archivo = get_ruta_archivo($id_paciente);
				$nombre_archivo = $ruta_archivo."historia_antigua_".mt_rand(1, 10000).".".$extension_aux;
				
				//Se crea el directorio del archivo
				@mkdir($ruta_archivo, 0755, true);
				
				//Se copia el archivo en el repositorio
				if ($bol_arch_externo) {
					$extension_aux = $utilidades->get_extension_arch($nombre_archivo_base);
					if ($extension_aux == "pdf") {
						@$nombre_tmp = $_FILES["fil_hc_antigua"]["tmp_name"];
						
						//Se copia el archivo
						$bol_resul = copy($nombre_tmp, $nombre_archivo);
					}
				} else {
					$nombre_archivo_base = iconv("UTF-8", "ISO-8859-1", $nombre_archivo_base);
					$bol_resul = copy($ruta_base."\\".$nombre_archivo_base, $nombre_archivo);
				}
				
				if ($bol_resul) {
					//Se actualiza el nombre del registro en la historia clínica
					$resul_aux = $dbImportarHc->editar_importar_hc($id_hc, $nombre_archivo, $id_usuario);
					$bol_resul = ($resul_aux > 0);
				}
				
				if (!$bol_resul) {
					//Se borra el archivo creado
					$dbHistoriaClinica->borrar_historia_clinica($id_hc, "Error al importar el archivo de historia antigua", $id_usuario);
				}
			}
			
			if (!$bol_resul && $id_hc > 0) {
				$id_hc = -1;
			}
			
			$reg_menu = $dbMenus->getMenu(46);
			$url_menu = $reg_menu["pagina_menu"];
	?>
    <input type="hidden" value="<?php echo($id_hc); ?>" name="hdd_exito" id="hdd_exito" />
	<input type="hidden" value="<?php echo($url_menu); ?>" name="hdd_url_menu" id="hdd_url_menu" />
	<div class="contenedor_error" id="contenedor_error"></div>
    <div class="contenedor_exito" id="contenedor_exito"></div>
	<?php
			break;
			
		case "4": //Búsqueda de archivos de historia clínica antigua
			@$txt_archivo_hc = $utilidades->str_decode($_POST["txt_archivo_hc"]);
			
			$lista_archivos = $dbHistoriaClinica->getListaArchivosHCAntiguas($txt_archivo_hc, 20);
			$cant_archivos = count($lista_archivos);
		?>
        <input type="hidden" id="hdd_cant_registros" value="<?php echo($cant_archivos); ?>" />
        <?php
			if ($cant_archivos > 0) {
		?>
        <table id="tabla_archivos_hc"  border="0" class="paginated modal_table" style="width:80%; margin:auto;">
            <thead>
                <tr class="headegrid">
                    <th class="headegrid" align="center">&nbsp;</th>	
                    <th class="headegrid" align="center">Archivos de las historia cl&iacute;nicas</th>
                </tr>
            </thead>
            <?php
				for ($i = 0; $i < $cant_archivos; $i++) {
					$archivo_aux = $lista_archivos[$i];
			?>
            <tr class="celdagrid" id="tr_archivos_<?php echo($i);?>" >
                <td align="center">
                    <input type="hidden" id="hdd_con_datos_<?php echo($i); ?>" value="1" />
                    <input type="checkbox" id="chk_archivo_hc_<?php echo($i);?>" class="no-margin" value="<?php echo($archivo_aux["nombre_archivo"]); ?>" onclick="marcar_checkbox('chk_archivo_hc_<?php echo($i); ?>');" />
                </td>	
                <td align="left" id="td_nombre_<?php echo($i); ?>" onclick="marcar_checkbox('chk_archivo_hc_<?php echo($i); ?>');">
                    <?php echo($archivo_aux["nombre_archivo"]); ?>
                </td>
            </tr>
            <?php
				}
			?>
	    </table>
        <?php
			} else {
		?>
		<div class="msj-vacio">
			<p>No se encontraron archivos</p>
		</div>
		<?php
			}
			
			//Opción de búsqueda de archivos
		?>
        <table align="center">
            <tr>
                <td>
                    <h6><b>Cargar archivo:&nbsp;</b></h6>
                </td>
                <td>
                    <input type="file" id="fil_hc_antigua" name="fil_hc_antigua" class="no-margin" />
                </td>
            </tr>
        </table>
        <?php
			break;
	}
?>
