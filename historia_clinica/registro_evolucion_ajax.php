<?php session_start();
	/*
	  Pagina para consultar HC de las pacientes 
	  Autor: Helio Ruber López - 30/03/2014
	 */
	
	//header('Content-Type: text/xml; charset=UTF-8');
	
	require_once("../db/DbHistoriaClinica.php");	
	require_once("../db/DbPacientes.php");
	require_once("../db/DbMenus.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/Utilidades.php");
	
	$dbHistoriaClinica = new DbHistoriaClinica();
	$dbPacientes = new DbPacientes();
	$dbMenus = new DbMenus();
	$funciones_persona = new FuncionesPersona();
	$utilidades = new Utilidades();
	$combo = new Combo_Box();
	$contenido = new ContenidoHtml();
	//$contenido->validar_seguridad(1);
	
	$opcion = $_POST["opcion"];
	
	switch ($opcion) {
		case "1": //Buscar pacientes
			@$txt_paciente_hc = $_POST["txt_paciente_hc"];
			@$credencial = $_POST["credencial"];
			
			//Se busca el menú de evoluciones
			$id_tipo_reg = 21;
			$menu_obj = $dbMenus->getMenuTipoReg($id_tipo_reg);
			
			$lista_personas = $dbHistoriaClinica->getPacientesHistoriaClinica($txt_paciente_hc);
			
			if (count($lista_personas) > 0) {
		?>
        <table id='tabla_persona_hc'  border='0' class="paginated modal_table" style="width: 95%; margin: auto;">
        	<thead>
            	<tr class='headegrid'>
                	<th class="headegrid" align="center" style="width:15%;">Documento</th>
                    <th class="headegrid" align="center" style="width:30%;">Nombre completo</th>
                    <th class="headegrid" align="center" style="width:12%;">Fecha nacimiento</th>
                    <th class="headegrid" align="center" style="width:10%;">Edad</th>
                    <th class="headegrid" align="center" style="width:21%;">Tel&eacute;fono(s)</th>
                    <th class="headegrid" align="center" style="width:12%;">Registrar evoluci&oacute;n</th>
                </tr>
            </thead>
			<?php
				foreach ($lista_personas as $fila_personas) {
					$id_paciente = $fila_personas['id_paciente'];
					$nombre_1 = $fila_personas['nombre_1'];
					$nombre_2 = $fila_personas['nombre_2'];
					$apellido_1 = $fila_personas['apellido_1'];
					$apellido_2 = $fila_personas['apellido_2'];
					$numero_documento = $fila_personas['numero_documento'];
					$tipo_documento = $fila_personas['tipo_documento'];
					$cod_tipo_documento = $fila_personas['cod_tipo_documento'];
					$nombres_apellidos = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
					$fecha_nacimiento = $fila_personas['fecha_nac_persona'];
					$telefonos = $fila_personas['telefono_1'].($fila_personas['telefono_2'] != "" ? " - ".$fila_personas['telefono_2'] : "");
					$nombres_apellidos = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
					
					//Edad del paciente
					$datos_paciente = $dbPacientes->getEdadPaciente($id_paciente, '');
					$edad_paciente = $datos_paciente['edad'];
			?>
            <tr class='celdagrid'>
            	<td align="center"><?php echo($cod_tipo_documento." ".$numero_documento); ?></td>
                <td align="left"><?php echo($nombres_apellidos); ?></td>
                <td align="center"><?php echo($fecha_nacimiento); ?></td>
                <td align="center"><?php echo($edad_paciente." a&ntilde;os"); ?></td>
                <td align="center"><?php echo($telefonos); ?></td>
                <td align="center">
                	<input type="button" class="btnPrincipal peq" value="Registrar" style="margin:1px;" onclick="mostrar_formulario_evolucion(<?php echo($id_paciente); ?>, <?php echo($menu_obj["id_menu"]) ?>, '<?php echo($menu_obj["pagina_menu"]); ?>', <?php echo($id_tipo_reg); ?>, '<?php echo($credencial); ?>');" />
                </td>
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
	}
?>
