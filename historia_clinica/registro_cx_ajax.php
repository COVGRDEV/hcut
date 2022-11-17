<?php session_start();
	/*
	  Pagina para consultar HC de las pacientes 
	  Autor: Helio Ruber López - 30/03/2014
	 */
	
	//header('Content-Type: text/xml; charset=UTF-8');
	
	require_once("../db/DbHistoriaClinica.php");	
	require_once("../db/DbAdmision.php");	
	require_once("../db/DbPacientes.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/Utilidades.php");
	require_once("../db/DbCirugias.php");
	
	$dbHistoriaClinica = new DbHistoriaClinica();
	$dbAdmision = new DbAdmision();
	$dbPacientes = new DbPacientes();
	$funciones_persona = new FuncionesPersona();
	$utilidades = new Utilidades();
	$combo = new Combo_Box();
	$contenido = new ContenidoHtml();
	//$contenido->validar_seguridad(1);
	$dbCirugias = new DbCirugias();
	
	$opcion = $_POST["opcion"];
	
	function ver_cirugias_pendientes($id_paciente, $nombre_persona, $documento_persona, $tipo_documento, $telefonos, $fecha_nacimiento, $edad_paciente) {
		$dbAdmision = new DbAdmision();
		$dbHistoriaClinica = new DbHistoriaClinica();
		$funciones_persona = new FuncionesPersona();
		$dbCirugias = new DbCirugias();
		
		$id_usuario = $_SESSION["idUsuario"];
		@$credencial = $_POST["credencial"];
		@$id_menu = $_POST["hdd_numero_menu"];
		
		//Se inserta el registro de ingreso a la historia clínica
		$dbHistoriaClinica->crear_ingreso_hc($id_usuario, $id_paciente, "", "", 162);
		
		$lista_admisiones_preqx = $dbAdmision->get_lista_admisiones_preqx($id_paciente, 3650);
	?>
    <fieldset style="width: 90%; margin: auto;">
    	<legend>Datos del paciente:</legend>
     	<table style="width: 500px; margin: auto; font-size: 10pt;">
           	<tr>
               	<td align="right" style="width:40%;">Tipo de documento:</td>
                <td align="left" style="width:60%;"><b><?php echo($tipo_documento); ?></b></td>
            </tr>
            <tr>
               	<td align="right">N&uacute;mero de identificaci&oacute;n:</td>
                <td align="left"><b><?php echo($documento_persona); ?></b></td>
            </tr>
            <tr>
               	<td align="right">Nombre completo:</td>
                <td align="left"><b><?php echo($nombre_persona); ?></b></td>
            </tr>
            <tr>
               	<td align="right">Fecha de nacimiento:</td>
                <td align="left"><b><?php echo($fecha_nacimiento); ?></b></td>
            </tr>
            <tr>
               	<td align="right">Edad:</td>
                <td align="left"><b><?php echo($edad_paciente); ?> a&ntilde;os</b></td>
            </tr>
            <tr>
               	<td align="right">Tel&eacute;fonos:</td>
                <td align="left"><b><?php echo($telefonos); ?></td>
            </tr>
        </table>
    </fieldset>
    <table class="modal_table" style="width: 95%; margin: auto;">
       	<thead>
           	<tr>
               	<th class="th_reducido" align="center" style="width:10%;">Fecha</th>
                <th class="th_reducido" align="center" style="width:90%;" colspan="5">Procedimiento(s)</th>
            </tr>
        </thead>
        <?php
			if (count($lista_admisiones_preqx) > 0) {
                foreach ($lista_admisiones_preqx as $admin_aux) {
					$id_admision = $admin_aux["id_admision"];
					$id_hc = intval($admin_aux["id_hc"]);
					$fecha_admision = $admin_aux["fecha_admision_t"];
					$pagina_menu = trim($admin_aux["pagina_menu"]);
					$tipo_cita = trim($admin_aux["id_tipo_cita"]); 
					
					//Si es 9 se muestra el boton de complemento
					
					if ($id_hc > 0) {
						$img_estado = "icon-convencion-disponible.png";
					} else {
						$img_estado = "icon-convencion-no-disponible.png";
					}
		?>
        <tr>
        	<td class="td_reducido" align="center" valign="middle"  onclick="mostrar_formulario_cx(<?php echo($id_admision); ?>, <?php echo($id_hc); ?>, '<?php echo($pagina_menu); ?>', <?php echo($credencial); ?>, <?php echo($id_menu); ?>, 0);"><?php echo($fecha_admision); ?></td>
            <td class="td_reducido" align="left" style="width:60%;" onclick="mostrar_formulario_cx(<?php echo($id_admision); ?>, <?php echo($id_hc); ?>, '<?php echo($pagina_menu); ?>', <?php echo($credencial); ?>, <?php echo($id_menu); ?>, 0);">
            	<?php
					//Se carga el listado de procedimientos
					if ($id_hc > 0) {
						$lista_procedimientos = $dbCirugias->get_lista_cirugias_procedimientos($id_hc);
					} else {
						$lista_procedimientos = $dbAdmision->get_lista_proc_adicionales_adm($id_admision);
					}
					//Se obtienen los procedimientos asociado con la consulta prequirúrgica
					//$lista_procedimientos = $dbAdmision->get_lista_proc_adicionales_adm($id_admision);
					
                	if (count($lista_procedimientos) > 0) {
				?>
                <ul>
	                <?php
						foreach ($lista_procedimientos as $proc_aux) {
					?>
                    <li><?php echo($proc_aux["nombre_procedimiento"]); ?></li>
                    <?php
						}
					?>
                </ul>
                <?php
					} else {
						echo("-");
					}
				?>
			</td>
            <td class="td_reducido" align="center" style="width:10%;">
            	<input class="btnPrincipal peq" type="button" value="Imprimir hoja stickers" id="btn_impr_stickers" name="btn_impr_stickers" onclick="imprimir_hoja_stickers(<?php echo($id_admision); ?>, <?php echo($id_hc); ?>);" />
            </td>
            <td class="td_reducido" align="center" style="width:10%;">
            	<input class="btnPrincipal peq" type="button" value="Cargar hoja stickers" id="btn_cargar_stickers" name="btn_cargar_stickers" onclick="mostrar_cargar_stickers(<?php echo($id_hc); ?>);" />
            </td>
			<?php
				if ($tipo_cita == 9 && $id_hc > 0) {
					//Si el tipo de cita es prequirurgico LASER y ya existe en la HC
					$tabla_cirugia = $dbCirugias->get_cirugia_laser($id_hc);
					$id_usuario_evalua = $tabla_cirugia['id_usuario_ev'];
					if ($id_usuario_evalua > 0) {
						$texto_evaluacion='Evaluaci&oacute;n';
					} else {
						$texto_evaluacion='Evaluaci&oacute;n (Pendiente)';
					}
			?>
            <td class="td_reducido" align="center" valign="middle" style="width:15%;">
            	<a href="#" onclick="mostrar_formulario_cx(<?php echo($id_admision); ?>, <?php echo($id_hc); ?>, '<?php echo($pagina_menu); ?>', <?php echo($credencial); ?>, <?php echo($id_menu); ?>, 1);" ><?php echo($texto_evaluacion); ?></a>
            </td>
            <?php
				} else {
			?>
            <td class="td_reducido" align="center" valign="middle" style="width:5%;">&nbsp;</td>
            <?php
				}
			?>
			<td class="td_reducido" align="center" valign="middle" style="width:5%;">
            	<img src="../imagenes/<?php echo($img_estado); ?>" />
            </td>
        </tr>
        <?php
				}
			} else {
				//Si no se encontraron registros
		?>
        <tr>
           	<td colspan="3">
               	<div class="msj-vacio">
                   	<p>No hay registros prequir&uacute;rgicos pendientes para este paciente</p>
                </div>
            </td>
        </tr>
        <?php
			}
        ?>
    </table>
    <br />
    <?php
	}
	
	switch ($opcion) {
		case "1": //Consultar HC del paciente
			$txt_paciente_hc = $_POST["txt_paciente_hc"];
			$id_usuario_crea = $_SESSION["idUsuario"];
			$tabla_personas = $dbHistoriaClinica->getPacientesHistoriaClinica($txt_paciente_hc);
			$cantidad_datos = count($tabla_personas);
			
			if ($cantidad_datos == 1) { //Si se encontro un solo registro
				$id_paciente = $tabla_personas[0]['id_paciente'];
				$nombre_1 = $tabla_personas[0]['nombre_1'];
				$nombre_2 = $tabla_personas[0]['nombre_2'];
				$apellido_1 = $tabla_personas[0]['apellido_1'];
				$apellido_2 = $tabla_personas[0]['apellido_2'];
				$numero_documento = $tabla_personas[0]['numero_documento'];
				$tipo_documento = $tabla_personas[0]['tipo_documento'];
				$fecha_nacimiento = $tabla_personas[0]['fecha_nac_persona'];
				$telefonos = $tabla_personas[0]['telefono_1']." - ".$tabla_personas[0]['telefono_2'];
				$nombres_apellidos = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
				//Edad del paciente
				$datos_paciente = $dbPacientes->getEdadPaciente($id_paciente, '');
				$edad_paciente = $datos_paciente['edad'];
				
				ver_cirugias_pendientes($tabla_personas[0]['id_paciente'], $nombres_apellidos, $numero_documento, $tipo_documento, $telefonos, $fecha_nacimiento, $edad_paciente);
			} else if($cantidad_datos > 1) {
		?>
        <table id='tabla_persona_hc'  border='0' class="paginated modal_table" style="width: 70%; margin: auto;">
        	<thead>
            	<tr class='headegrid'>
                	<th class="headegrid" align="center">Documento</th>
                    <th class="headegrid" align="center">Pacientes</th>
                </tr>
            </thead>
			<?php
				foreach ($tabla_personas as $fila_personas) {
					$id_personas = $fila_personas['id_paciente'];
					$nombre_1 = $fila_personas['nombre_1'];
					$nombre_2 = $fila_personas['nombre_2'];
					$apellido_1 = $fila_personas['apellido_1'];
					$apellido_2 = $fila_personas['apellido_2'];
					$numero_documento = $fila_personas['numero_documento'];
					$tipo_documento = $fila_personas['tipo_documento'];
					$nombres_apellidos = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
					$fecha_nacimiento = $fila_personas['fecha_nac_persona'];
					$telefonos = $fila_personas['telefono_1']." - ".$fila_personas['telefono_2'];
					$nombres_apellidos = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
					//Edad del paciente
					$datos_paciente = $dbPacientes->getEdadPaciente($id_personas, '');
					$edad_paciente = $datos_paciente['edad'];
			?>
            <tr class='celdagrid' onclick="ver_registros_hc(<?php echo($id_personas);?>, '<?php echo($nombres_apellidos);?>', '<?php echo($numero_documento);?>', '<?php echo($tipo_documento);?>', '<?php echo($telefonos);?>', '<?php echo($fecha_nacimiento);?>', '<?php echo($edad_paciente);?>');">
            	<td align="left"><?php echo $numero_documento;?></td>
                <td align="left"><?php echo $nombres_apellidos;?></td>
            </tr>
            <?php
				}
			?>
        </table>
        <script id='ajax'>
			//<![CDATA[ 
			$(function() {
			    $('.paginated', 'tabla_persona_hc').each(function(i) {
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
			} else if ($cantidad_datos == 0) {
		?>
        <div class='msj-vacio'>
        	<p>No se encontraron pacientes</p>
        </div>
        <?php
			}
			break;		
			
		case "2": //Ver historia clínica del paciente
			$id_persona = $_POST['id_persona'];
			$nombre_persona = $_POST['nombre_persona'];
			$documento_persona = $_POST['documento_persona'];
			$tipo_documento = $_POST['tipo_documento'];
			$telefonos = $_POST['telefonos'];
			$fecha_nacimiento = $_POST['fecha_nacimiento'];
			$edad_paciente = $_POST['edad_paciente']; 
			ver_cirugias_pendientes($id_persona, $nombre_persona, $documento_persona, $tipo_documento, $telefonos, $fecha_nacimiento, $edad_paciente);
		    break;
			
		case "3": //Carga de imagen de stickers
			$id_usuario = $_SESSION["idUsuario"];
			@$id_hc = $utilidades->str_decode($_POST["hdd_id_hc_stickers"]);
			@$nombre_tmp = $_FILES["fil_hoja_stickers"]["tmp_name"];
			@$nombre_ori = $_FILES["fil_hoja_stickers"]["name"];
			
			//Se obtiene el nombre que tendrá el archivo
			$nombre_arch = $dbHistoriaClinica->construir_nombre_arch($id_hc, $nombre_ori, "stickers", "");
			
			//Se crea el directorio del archivo
			$pos_aux = strrpos($nombre_arch, "/", -1);
			$ruta_arch_examen = substr($nombre_arch, 0, $pos_aux);
			@mkdir($ruta_arch_examen, 0755, true);
			
			//Se copia el archivo
			copy($nombre_tmp, $nombre_arch);
			
			//Se actualiza el registro de cirugía
			$resultado = $dbCirugias->editar_cirugia_stickers($id_hc, $nombre_arch, $id_usuario);
		?>
        <input type="hidden" id="hdd_resul_stickers" value="<?php echo($resultado); ?>" />
        <?php
			break;
	}
?>
