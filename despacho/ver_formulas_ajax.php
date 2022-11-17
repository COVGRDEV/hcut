<?php session_start();
/*
  Pagina para consultar HC de las pacientes 
  Autor: Helio Ruber López - 30/03/2014
 */
 
 	header('Content-Type: text/xml; charset=UTF-8');

    require_once("../db/DbUsuarios.php");
    require_once("../db/DbListas.php");
	require_once("../db/DbDespacho.php");	
	require_once("../db/DbMenus.php");
	require_once("../db/DbHistoriaClinica.php");
	require_once("../funciones/Class_Combo_Box.php");
    require_once("../db/DbVariables.php");	
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/Utilidades.php");
	require_once("../db/DbPacientes.php");
	
	$usuarios = new DbUsuarios();
	$listas = new DbListas();
	$despacho = new DbDespacho();
	$menus = new DbMenus();
	$variables = new Dbvariables();
	$contenido = new ContenidoHtml();
	$contenido->validar_seguridad(1);
	$funciones_persona = new FuncionesPersona();
	$utilidades = new Utilidades();
	$pacientes=new DbPacientes();
	$combo=new Combo_Box();
	$opcion=$_POST["opcion"];
	
	
	
	function ver_formulas_despacho($id_paciente, $nombre_persona, $documento_persona, $tipo_documento, $telefonos, $fecha_nacimiento, $edad_paciente){
		    $despacho = new DbDespacho();
			$menus = new DbMenus();
			$historiaClinica = new DbHistoriaClinica();
			$funciones_persona = new FuncionesPersona();
			
			$id_usuario = $_SESSION["idUsuario"];
			@$credencial = $_POST["credencial"];
			@$id_menu = $_POST["hdd_numero_menu"];
			
			//Se inserta el registro de ingreso a la historia clínica
			$historiaClinica->crear_ingreso_hc($id_usuario, $id_paciente, "", "", 163);
			
    		$tabla_registro=$despacho->getRegistrosDespacho($id_paciente);
			
    		?>
    		<fieldset style="width: 65%; margin: auto;">
                <legend>Datos del paciente:</legend>
                <table style="width: 500px; margin: auto; font-size: 10pt;">
                    <tr>
                        <td align="right" style="width:40%">Tipo de documento:</td>
                        <td align="left" style="width:60%"><b><?php echo($tipo_documento); ?></b></td>
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
                        <td align="left"><b><?php echo($telefonos); ?></b></td>
                    </tr>
                </table>
            </fieldset>
    		<table class="modal_table" style="width: 70%; margin: auto;" >
                <thead>
                    <tr>
                        <th class="th_reducido" align="center" style="width:15%;">Fecha</th>
                        <th class="th_reducido" align="center" style="width:90%;">Tipo de cita</th>
                    </tr>
                </thead>
                <?php
                
                if(count($tabla_registro)>0){
                    foreach($tabla_registro as $fila_registro){
                    	
						$id_paciente = $fila_registro['id_paciente'];
						$nombre_1= $fila_registro['nombre_1'];
						$nombre_2= $fila_registro['nombre_2'];
						$apellido_1= $fila_registro['apellido_1'];
						$apellido_2= $fila_registro['apellido_2'];
						$nombre_persona = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
						$id_admision = $fila_registro['id_admision'];
						
						$reg_menu=$menus->getMenu(39);
						$pagina_consulta = $reg_menu['pagina_menu'];
						
						$id_hc = 0;
                    	$nombre_tipo_reg = $fila_registro['nombre_tipo_cita'];
						$fecha_hc = $fila_registro['fecha_despacho_t'];
						    ?>
                            <tr onclick="mostrar_consultas_div(<?php echo($id_paciente); ?>, '<?php echo($nombre_persona); ?>', <?php echo($id_admision); ?>, '<?php echo($pagina_consulta); ?>', <?php echo($id_hc); ?>, <?php echo($credencial); ?>, <?php echo($id_menu); ?>);">
                                <td class="td_reducido" align="left"><?php echo($fecha_hc); ?></td>
                                <td class="td_reducido" align="left"><?php echo($nombre_tipo_reg); ?></td>
                            </tr>
                            <?php
                    }
                }
				else{
					//Si no se encontraron registros de historia clinica
                    ?>
                    <tr>
                        <td colspan="2">
                            <div class="msj-vacio">
                                <p>No hay formuas para este paciente</p>
                            </div>
                        </td>
                    </tr>
                    <?php
				}
                ?>
           	 </table>
           	 <?php
	}
	
	
	switch ($opcion) {
	case "1": //Consultar HC del paciente	
	    $txt_paciente_hc = $_POST["txt_paciente_hc"];
	    $id_usuario_crea = $_SESSION["idUsuario"];
		$tabla_personas = $despacho->getPacientesAdmisiones($txt_paciente_hc);
		$cantidad_datos = count($tabla_personas);
		
		if($cantidad_datos == 1){//Si se encontro un solo registro
			
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
			$datos_paciente = $pacientes->getEdadPaciente($id_paciente, '');
			$edad_paciente = $datos_paciente['edad'];
			
			ver_formulas_despacho($tabla_personas[0]['id_paciente'], $nombres_apellidos, $numero_documento, $tipo_documento, $telefonos, $fecha_nacimiento, $edad_paciente);
			
		}
		else if($cantidad_datos > 1){
			?>
			<table id='tabla_persona_hc'  border='0' class="paginated modal_table" style="width: 70%; margin: auto;">
	        	<thead>
	        	<tr class='headegrid'>
					<th class="headegrid" align="center">Documento</th>	
					<th class="headegrid" align="center">Pacientes</th>
				</tr>
				 </thead>
			<?php	
			foreach($tabla_personas as $fila_personas){
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
				$datos_paciente = $pacientes->getEdadPaciente($id_personas, '');
				$edad_paciente = $datos_paciente['edad'];
				?>
				<tr class='celdagrid' onclick="ver_registros_formulas(<?php echo($id_personas);?>, '<?php echo($nombres_apellidos);?>', '<?php echo($numero_documento);?>', '<?php echo($tipo_documento);?>', '<?php echo($telefonos);?>', '<?php echo($fecha_nacimiento);?>', '<?php echo($edad_paciente);?>');">
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
			
		}
		else if($cantidad_datos == 0){
			echo"<div class='msj-vacio'>
					<p>No se encontraron pacientes</p>
			     </div>";
		}
		
        														 
	
	break;		
	
	case "2":
		
		$id_persona = $_POST['id_persona'];
		$nombre_persona = $_POST['nombre_persona'];
		$documento_persona = $_POST['documento_persona'];
		$tipo_documento = $_POST['tipo_documento'];
		$telefonos = $_POST['telefonos'];
		$fecha_nacimiento = $_POST['fecha_nacimiento'];
		$edad_paciente = $_POST['edad_paciente']; 
		ver_formulas_despacho($id_persona, $nombre_persona, $documento_persona, $tipo_documento, $telefonos, $fecha_nacimiento, $edad_paciente);
		
    break;				
				
	
	
		
		
	}
	




?>