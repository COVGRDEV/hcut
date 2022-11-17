<?php
	session_start();
	/*
	  Pagina para ver y matricular pacientes para atencion post QX de Catarata
	  Autor: Helio Ruber López - 27/04/2016
	 */
	require_once("../db/DbVariables.php");
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbHistoriaClinica.php");
	require_once("../db/DbAdmision.php");
	require_once("../db/DbTiposCitas.php");
	require_once("../db/DbMenus.php");
	require_once("../db/DbPacientes.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/Utilidades.php");
	require_once("../db/DbAtencionPostqx.php");
	
	$variables = new Dbvariables();
	$usuarios = new DbUsuarios();
	$dbAdmision = new DbAdmision();
	$dbTiposCitas = new DbTiposCitas();
	$historia_clinica = new DbHistoriaClinica();
	$menus = new DbMenus();
	$contenido = new ContenidoHtml();
	$listas = new DbListas();
	$combo = new Combo_Box();
	$pacientes = new DbPacientes();
	$utilidades = new Utilidades();
	$dbPostQx = new DbAtencionPostqx();
	$funciones_persona = new FuncionesPersona();
	
	//variables
	$titulo = $variables->getVariable(1);
	$horas_edicion = $variables->getVariable(7);
	
	//Cambiar las variables get a post
	$utilidades->get_a_post();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $titulo['valor_variable']; ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
        <link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
        <link href="../css/azul.css" rel="stylesheet" type="text/css" />
        <!--Para autocompletar DEBE IR DE PRIMERO-->
        <script type='text/javascript' src='../js/jquery_autocompletar.js'></script>
        <script type='text/javascript' src='../js/jquery-ui.js'></script>
        <!--Para validar DEBE IR DE SEGUNDO-->
        <script type='text/javascript' src='../js/jquery.validate.js'></script>
        <script type='text/javascript' src='../js/jquery.validate.add.js'></script>
        <!--Para funciones de optometria DEBE IR DE TERCERO-->
        <script type='text/javascript' src='../js/ajax.js'></script>
        <script type='text/javascript' src='../js/funciones.js'></script>
        <script type='text/javascript' src='../js/validaFecha.js'></script>
        <script type='text/javascript' src='atencion_postqx_v1.1.js'></script>
        
        <!-- Para timepicker-->
        <script type="text/javascript" src="../js/timepicker/jquery.timepicker.js"></script>
  		<link rel="stylesheet" type="text/css" href="../js/timepicker/jquery.timepicker.css" />
        
        
    </head>
    <body>
        <?php
			$contenido->validar_seguridad(0);
			$contenido->cabecera_html();
			$id_usuario_crea = $_SESSION["idUsuario"];
        ?>
        <div class="title-bar">
            <div class="wrapper">
                <div class="breadcrumb">
                    <ul>
                        <li class="breadcrumb_on">Seguimiento post-quir&uacute;rgico de Catarata </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="contenedor_principal">
            <div id="guardar_vincular_paciente" style="width:100%;">
                <div class='contenedor_error' id='contenedor_error'></div>
                <div class='contenedor_exito' id='contenedor_exito'></div>
            </div>
            <div class="formulario" id="principal_historia_clinica" style="width: 100%; display: block; ">
                <form id='frm_historia_clinica' name='frm_historia_clinica' method="post">
                    <table id="buscar" border="0" style="width: 100%;">
                        <tr><td colspan="2"> <h4>Buscar pacientes</h4></td></tr>
                        <tr>
                            <td style="width: 60%;text-align: right;">
                                <input type="text" id="txt_paciente_hc" name="txt_paciente_hc" placeholder="Buscar por nombre o documento del paciente" style="width:320px;float: right;"  onblur="trim_cadena(this);" />
                            </td>
                            <td style="width: 40%;text-align: left;">
                                <input class="btnPrincipal peq" type="submit" value="Consultar" id="btn_consultar" name="btn_consultar" onclick="validarBuscarPersonasHc();" />
                            </td>
                        </tr>
                    </table>
                </form>
                <div id="contenedor_paciente_hc" style="min-height: 30px;">
                	
                <table class="paginated modal_table" style="width: 100%;  margin: auto;">
	        	<thead>
	        	<tr>
					<th colspan="2" style="width: 5%;">Pacientes</th>
					<th colspan="20">Encuestas realizadas</th>						
				</tr>
				 </thead>	
                	
                	
            	<?php
            	
            	$tabla_pacientes = $dbPostQx->getPacientesMatriculados();
            	
				
				foreach($tabla_pacientes as $fila_pacientes){
					
					$id_paciente = $fila_pacientes['id_paciente'];
		            $nombre_1 = $fila_pacientes['nombre_1'];
		            $nombre_2 = $fila_pacientes['nombre_2'];
		            $apellido_1 = $fila_pacientes['apellido_1'];
		            $apellido_2 = $fila_pacientes['apellido_2'];
		            $numero_documento = $fila_pacientes['numero_documento'];
					$fecha_seguimiento_ini = $fila_pacientes['fecha_seguimiento_ini'];
					$tipo_documento = $fila_pacientes['tipo_documento'];
					$fecha_nacimiento = $fila_pacientes['fecha_nac_persona'];
					$id_ojos = $fila_pacientes['id_ojos'];
					
		            //$fecha_nacimiento = $fila_pacientes['fecha_nac_persona'];
		            $telefonos = $fila_pacientes['telefono_1'];
					if ($fila_pacientes['telefono_2'] != "") {
			            $telefonos .= " - ".$fila_pacientes['telefono_2'];
					}
		            $nombres_apellidos = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
		            //Edad del paciente
		            $datos_paciente = $pacientes->getEdadPaciente($id_paciente, '');
		            $edad_paciente = $datos_paciente['edad'];
					
					
					if($id_ojos == 81){
					

					?>
					<tr class='celdagrid' onclick="ver_registros_hc(<?php echo($id_paciente); ?>, '<?php echo($nombres_apellidos); ?>', '<?php echo($numero_documento); ?>', '<?php echo($tipo_documento); ?>', '<?php echo($telefonos); ?>', '<?php echo($fecha_nacimiento); ?>', '<?php echo($edad_paciente); ?>');">
						<td align="left"><?php echo $nombres_apellidos; ?></td>
						<td align="left">OD</td>
					<?php
					
					$fechaInicio=strtotime($fecha_seguimiento_ini);
					$fechaFin= $fechaInicio + (86400 * 19);
					for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){
							
						$fecha_consultar = date("Y-m-d", $i);
						
					    $tabla_respuestas = $dbPostQx->getRespuestaPacienteFechaOjo($id_paciente, $fecha_consultar, 1);
						if (count($tabla_respuestas) > 0){
							$rta1=$tabla_respuestas[0]['pregunta_1'];
							$rta2=$tabla_respuestas[0]['pregunta_2'];
							$rta3=$tabla_respuestas[0]['pregunta_3'];
							$rta4=$tabla_respuestas[0]['pregunta_4'];
							$rta5=$tabla_respuestas[0]['pregunta_5'];
							
							if($rta1 == 0 && $rta2 == 0 && $rta3 == 0 && $rta4 == 0 && $rta5 == 1){
								$color="icon_ok.png";	
								$respuesta_ok=1;
							}
							else{
								$color="icon_alerta.png";	
								$respuesta_ok=2;
							}
							
						}
						else{
							$color="icon_nada.png";	
							$respuesta_ok=0;
						}
						
						?>
						<td style="width: 0.5%;" align="center">
							<?php //echo($respuesta_ok);?>
							<div style="background-image: url('../imagenes/<?php echo($color);?>'); background-repeat:no-repeat;">&nbsp;</div>
						</td>
						<?php
						
					}
					?>	
					</tr>
					<?php

					?>
					<tr class='celdagrid' onclick="ver_registros_hc(<?php echo($id_paciente); ?>, '<?php echo($nombres_apellidos); ?>', '<?php echo($numero_documento); ?>', '<?php echo($tipo_documento); ?>', '<?php echo($telefonos); ?>', '<?php echo($fecha_nacimiento); ?>', '<?php echo($edad_paciente); ?>');">
						<td align="left"><?php echo $nombres_apellidos; ?></td>
						<td align="left">OI</td>
					<?php
					
					
					for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){
							
						$fecha_consultar = date("Y-m-d", $i);
						
					    $tabla_respuestas = $dbPostQx->getRespuestaPacienteFechaOjo($id_paciente, $fecha_consultar, 2);
						if (count($tabla_respuestas) > 0){
							$rta1=$tabla_respuestas[0]['pregunta_1'];
							$rta2=$tabla_respuestas[0]['pregunta_2'];
							$rta3=$tabla_respuestas[0]['pregunta_3'];
							$rta4=$tabla_respuestas[0]['pregunta_4'];
							$rta5=$tabla_respuestas[0]['pregunta_5'];
							
							if($rta1 == 0 && $rta2 == 0 && $rta3 == 0 && $rta4 == 0 && $rta5 == 1){
								$color="icon_ok.png";	
								$respuesta_ok=1;
							}
							else{
								$color="icon_alerta.png";	
								$respuesta_ok=2;
							}
							
						}
						else{
							$color="icon_nada.png";	
							$respuesta_ok=0;
						}
						
						?>
						<td style="width: 0.5%;" align="center">
							<?php //echo($respuesta_ok);?>
							<div style="background-image: url('../imagenes/<?php echo($color);?>'); background-repeat:no-repeat;">&nbsp;</div>
						</td>
						<?php
						
					}
					?>	
					</tr>
					<?php		
					
						
						
					}
					else{
						
						if($id_ojos == 79){
							$nom_ojo = 'OD';
						}
						else{
							$nom_ojo = 'OI';
						}
						
						
					?>
					<tr class='celdagrid' onclick="ver_registros_hc(<?php echo($id_paciente); ?>, '<?php echo($nombres_apellidos); ?>', '<?php echo($numero_documento); ?>', '<?php echo($tipo_documento); ?>', '<?php echo($telefonos); ?>', '<?php echo($fecha_nacimiento); ?>', '<?php echo($edad_paciente); ?>');">
						<td align="left"><?php echo $nombres_apellidos; ?></td>
						<td align="left"><?php echo $nom_ojo; ?></td>
					<?php
					
					$fechaInicio=strtotime($fecha_seguimiento_ini);
					$fechaFin= $fechaInicio + (86400 * 19);
					for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){
							
						$fecha_consultar = date("Y-m-d", $i);
						
					    $tabla_respuestas = $dbPostQx->getRespuestaPacienteFecha($id_paciente, $fecha_consultar);
						if (count($tabla_respuestas) > 0){
							$rta1=$tabla_respuestas[0]['pregunta_1'];
							$rta2=$tabla_respuestas[0]['pregunta_2'];
							$rta3=$tabla_respuestas[0]['pregunta_3'];
							$rta4=$tabla_respuestas[0]['pregunta_4'];
							$rta5=$tabla_respuestas[0]['pregunta_5'];
							
							if($rta1 == 0 && $rta2 == 0 && $rta3 == 0 && $rta4 == 0 && $rta5 == 1){
								$color="icon_ok.png";	
								$respuesta_ok=1;
							}
							else{
								$color="icon_alerta.png";	
								$respuesta_ok=2;
							}
							
						}
						else{
							$color="icon_nada.png";	
							$respuesta_ok=0;
						}
						
						?>
						<td style="width: 0.5%;" align="center">
							<?php //echo($respuesta_ok);?>
							<div style="background-image: url('../imagenes/<?php echo($color);?>'); background-repeat:no-repeat;">&nbsp;</div>
						</td>
						<?php
						
					}
					?>	
					</tr>
					<?php	
						
						
						
					}
					
					
					
					
				}
		        ?>
		        
		        </table>
		        <br/>
				<script id='ajax'>
					//<![CDATA[ 
					$(function() {
					    $('.paginated', 'table').each(function(i) {
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
		        
		        
                </div>
            </div>

        </div>

        <script type='text/javascript' src='../js/foundation.min.js'></script>
        <script>
                                    $(document).foundation();
        </script>

        <?php
        $contenido->footer();
        ?>
    </body>
</html>

