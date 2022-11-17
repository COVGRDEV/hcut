<?php
	session_start();
	/*
	  Pagina para habilitar pacientes al modulo de consulta de examenes  
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
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../db/DbPacientes.php");
	
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
	$funciones_persona = new FuncionesPersona();
	$db_pacientes = new DbPacientes();
	
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
        <!--<script type='text/javascript' src='acceso_resultados_v1.1.js'></script>-->
        <script type='text/javascript' src='../historia_clinica/historia_clinica_v1.4.js'></script>
        
        <!-- Para timepicker-->
        <script type="text/javascript" src="../js/timepicker/jquery.timepicker.js"></script>
  		<link rel="stylesheet" type="text/css" href="../js/timepicker/jquery.timepicker.css" />  		
  		<script type='text/javascript' src='resultados_v1.2.js'></script>
    </head>
    <body>
        <?php
			$contenido->validar_seguridad_resultados(0);
			$contenido->cabecera_resultados_html();
			$id_usuario_paciente = $_SESSION["idUsuario"];
			
			@$credencial = $utilidades->str_decode($_POST["credencial"]);
    		@$id_menu = 0;
        ?>
        <div class="title-bar">
            <div class="wrapper">
                <div class="breadcrumb">
                    <ul>
                        <li class="breadcrumb_on">Descargar resultados de examenes</li>
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
                <?php
					$tabla_registro_hc = $historia_clinica->getRegistrosExamenes($id_usuario_paciente);
					$tabla_paciente_hc = $db_pacientes->getExistepaciente3($id_usuario_paciente);
					
					$nombre_pa_1 = $tabla_paciente_hc['nombre_1']; 
					$nombre_pa_2 = $tabla_paciente_hc['nombre_2'];
					$apellido_pa_1 = $tabla_paciente_hc['apellido_1'];
					$apellido_pa_2 = $tabla_paciente_hc['apellido_2'];
					$fecha_nacimiento = $tabla_paciente_hc['fecha_nacimiento_aux'];
					$telefonos = $tabla_paciente_hc['telefono_1']." - ".$tabla_paciente_hc['telefono_2'];
					$documento_persona = $tabla_paciente_hc['numero_documento'];
					$tipo_documento = $tabla_paciente_hc['tipodocumento'];
					$nombre_pacientes = $funciones_persona->obtenerNombreCompleto($nombre_pa_1, $nombre_pa_2, $apellido_pa_1, $apellido_pa_2);
                ?>
                <fieldset style="width: 65%; margin: auto;">
			        <legend>Datos del paciente:</legend>
			        <table style="width: 500px; margin: auto; font-size: 10pt;">
			            <tr>
			                <td align="right" style="width:40%"><?php echo($tipo_documento); ?>:</td>
			                <td align="left" style="width:60%"><b><?php echo($documento_persona); ?></b></td>
			            </tr>
			            <tr>
			                <td align="right">Nombre completo:</td>
			                <td align="left"><b><?php echo($nombre_pacientes); ?></b></td>
			            </tr>
			        </table>
			    </fieldset>
                <div style="width:70%; margin:auto; height:200px; overflow:auto;">
			    <table class="modal_table" style="width:99%; margin:auto;" align="left">
			        <thead>
			            <tr>
			                <th class="th_reducido" align="center" style="width:15%;">Fecha</th>
			                <th class="th_reducido" align="center" style="width:85%;">Tipo de registro</th>
			            </tr>
			        </thead>
			        <?php
						if (count($tabla_registro_hc) > 0) {
							foreach ($tabla_registro_hc as $fila_registro_hc) {
								$id_paciente = $fila_registro_hc['id_paciente'];
								$nombre_1 = $fila_registro_hc['nombre_1'];
								$nombre_2 = $fila_registro_hc['nombre_2'];
								$apellido_1 = $fila_registro_hc['apellido_1'];
								$apellido_2 = $fila_registro_hc['apellido_2'];
								$nombre_persona = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
								$id_admision = $fila_registro_hc['id_admision'];
								$pagina_consulta = $fila_registro_hc['pagina_menu'];
								
								$id_hc = $fila_registro_hc['id_hc'];
								$id_tipo_reg = $fila_registro_hc['id_tipo_reg'];
								$nombre_tipo_reg = $fila_registro_hc['nombre_tipo_reg'];
								if ($fila_registro_hc["nombre_alt_tipo_reg"] != "") {
									$nombre_tipo_reg .= " (".$fila_registro_hc["nombre_alt_tipo_reg"].")";
								}
								
								$fecha_hc = $fila_registro_hc['fecha_hora_hc_t'];
								$estado_hc = $fila_registro_hc['ind_estado'];
								
								if ($estado_hc == 1) {
									$img_estado = "<img src='../imagenes/icon-convencion-no-disponible.png' />";
								} else if ($estado_hc == 2) {
									$img_estado = "<img src='../imagenes/icon-convencion-disponible.png' />";
								}
								
								$extension_arch = "";
								if ($fila_registro_hc["ruta_arch_adjunto"] != "") {
									$extension_arch = strtolower($utilidades->get_extension_arch($fila_registro_hc["ruta_arch_adjunto"]));
								}
					?>
			        <tr onclick="imprimir_registro_resultados(<?php echo($id_hc); ?>);">
				        <td class="td_reducido" align="left"><?php echo($fecha_hc); ?></td>
				        <td class="td_reducido" align="left"><?php echo($nombre_tipo_reg); ?></td>
			        </tr>
			        <?php
							}
						} else {
							//Si no se encontraron registros de historia clinica
					?>
                    <tr>
			            <td colspan="2">
			                <div class="msj-vacio">
			                    <p>No se encontraron registros para este paciente</p>
			                </div>
			            </td>
			        </tr>
			        <?php
						}
					?>
			    </table>
			    </div>
                <div id="d_cargando_resultados" style="width:70%; margin:auto; display:none;">
                	<img src="../imagenes/ajax-loader.gif" />
                </div>
                <div id="contenedor_paciente_hc" style="min-height: 30px;"></div>
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
