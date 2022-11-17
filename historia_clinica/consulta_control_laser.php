<?php session_start();
	/*
	  Pagina para crear consulta prequirurgica de laser 
	  Autor: Helio Ruber López - 19/02/2014
	*/
	require_once("../db/DbVariables.php");
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbConsultaControlLaser.php");
	require_once("../db/DbAdmision.php");
	require_once("../db/DbTiposCitas.php");
	require_once("../db/DbMenus.php");
	require_once("../db/DbPacientes.php");
	require_once("../db/DbDiagnosticos.php");
	require_once("../db/DbTiposCitasDetalle.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/Class_Diagnosticos.php");
	require_once("../funciones/Utilidades.php");
	require_once("FuncionesHistoriaClinica.php");
	
	$variables = new Dbvariables();
	$usuarios = new DbUsuarios();
	$listas = new DbListas();
	$controllaser = new DbConsultaControlLaser();
	$dbAdmision = new DbAdmision();
	$dbTiposCitas = new DbTiposCitas();
	$menus = new DbMenus();
	$pacientes = new DbPacientes();
	$db_diagnosticos = new DbDiagnosticos();
	$dbTiposCitasDetalle = new DbTiposCitasDetalle();
	$contenido = new ContenidoHtml();
	$combo = new Combo_Box();
	$class_diagnosticos = new Class_Diagnosticos();
	$utilidades = new Utilidades();
	$funciones_hc = new FuncionesHistoriaClinica();
	
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
        <link href="../css/Class_Color_Pick.css" rel="stylesheet" type="text/css" />
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
        <script type='text/javascript' src='../js/Class_Diagnosticos_v1.2.js'></script>
        <script type='text/javascript' src='../js/Class_Atencion_Remision_v1.3.js'></script>
        <script type='text/javascript' src='../funciones/ckeditor/ckeditor.js'></script>
        <script type='text/javascript' src='../funciones/ckeditor/config.js'></script>
        <script type='text/javascript' src='../js/Class_Color_Pick.js'></script>
        <script type='text/javascript' src='../js/jquery.textarea_autosize.js'></script>
        <script type='text/javascript' src='historia_clinica_v1.1.js'></script>
        <script type='text/javascript' src='FuncionesHistoriaClinica.js'></script>
        <script type='text/javascript' src='consulta_control_laser_v1.9.js'></script>
        <?php
			$tabla_diagnosticos = $db_diagnosticos->getDiagnosticoCiexTotal();
			$i = 0;
			$cadena_diagnosticos = '';
			foreach ($tabla_diagnosticos as $fila_diagnosticos) {
				$cod_ciex = $fila_diagnosticos['codciex'];
				$nom_ciex = $fila_diagnosticos['nombre'];
				
				if ($cadena_diagnosticos != "") {
					$cadena_diagnosticos .= ",";
				}
				$cadena_diagnosticos .= "'".$nom_ciex." | ".$cod_ciex."'";
				
				$i++;
			}
		?>
		<script>
        	$(function() {
				var Tags_diagnosticos = [<?php echo($cadena_diagnosticos) ?>];
				
				for (k = 1; k <= 10; k++) {
					$("#txt_busca_diagnostico_" + k).autocomplete({ source: Tags_diagnosticos });
				}
			});
		</script>
        <?php
			//Array valores esfera
			$cadena_esfera = "'0'";
			for ($i = 0; $i <= 30; $i++) {
				if ($cadena_esfera != "") {
					$cadena_esfera .= ",";
				}
				if ($i > 0) {
					$cadena_esfera .= "'-".$i.",00',";
				}
				$cadena_esfera .= "'-".$i.",25',";
				$cadena_esfera .= "'-".$i.",50',";
				$cadena_esfera .= "'-".$i.",75',";
				if ($i > 0) {
					$cadena_esfera .= "'+".$i.",00',";
				}
				$cadena_esfera .= "'+".$i.",25',";
				$cadena_esfera .= "'+".$i.",50',";
				$cadena_esfera .= "'+".$i.",75'";
			}
			$cadena_esfera .= ",'NP/NA'";
			
			//Array valores cilindro
			$cadena_cilindro = "'0'";
			for ($i = 0; $i <= 9; $i++) {
				if ($cadena_cilindro != "") {
					$cadena_cilindro .= ",";
				}
				if ($i > 0) {
					$cadena_cilindro .= "'-".$i.",00',";
				}
				$cadena_cilindro .= "'-".$i.",25',";
				$cadena_cilindro .= "'-".$i.",50',";
				$cadena_cilindro .= "'-".$i.",75'";
			}
			$cadena_cilindro .= ",'NP/NA'";
			
			//Array valores eje
			$cadena_eje = "'0'";
			for ($i = 1; $i <= 180; $i++) {
				if ($cadena_eje != "") {
					$cadena_eje .= ",";
				}
				$cadena_eje .= "'".$i."'";
			}
			$cadena_eje .= ",'NP/NA'";
			
			//Array valores adicion
			$cadena_adicion = "'0'";
			for ($i = 0; $i <= 3; $i++) {
				if ($cadena_adicion != "") {
					$cadena_adicion .= ",";
				}
				if ($i > 0) {
					$cadena_adicion .= "'".$i.",00',";
				}
				$cadena_adicion .= "'".$i.",25',";
				$cadena_adicion .= "'".$i.",50',";
				$cadena_adicion .= "'".$i.",75'";
			}
			$cadena_adicion .= ",'NP/NA'";
			
			//Array valores querato
			$cadena_querato = "";
			for ($i = 30; $i <= 65; $i++) {
				if ($cadena_querato != "") {
					$cadena_querato .= ",";
				}
				$cadena_querato .= "'".$i.",00',";
				$cadena_querato .= "'".$i.",25',";
				$cadena_querato .= "'".$i.",50',";
				$cadena_querato .= "'".$i.",75'";
			}
			$cadena_querato .= ",'NP/NA'";
		?>
        <script>
            var array_esfera = [<?php echo($cadena_esfera) ?>];
            var array_cilindro = [<?php echo($cadena_cilindro) ?>];
            var array_eje = [<?php echo($cadena_eje) ?>];
            var array_adicion = [<?php echo($cadena_adicion) ?>];
            var array_querato = [<?php echo($cadena_querato) ?>];
            
        	$(function() {
				var Tags_esfera = [<?php echo($cadena_esfera) ?>];
				var Tags_cilindro = [<?php echo($cadena_cilindro) ?>];
				var Tags_eje = [<?php echo($cadena_eje) ?>];
				var Tags_adicion = [<?php echo($cadena_adicion) ?>];
				var Tags_querato = [<?php echo($cadena_querato) ?>];
				
				//Para queratometria OD
				$("#querato_cilindro_od").autocomplete({ source: Tags_cilindro });
				$("#querato_mplano_od").autocomplete({ source: Tags_querato });
				//Para queratometria OI
				$("#querato_cilindro_oi").autocomplete({ source: Tags_cilindro });
				$("#querato_mplano_oi").autocomplete({ source: Tags_querato });
				
				//Para REFRACCIÓN OD
				$("#avc_esfera_od").autocomplete({ source: Tags_esfera });
				$("#avc_cilindro_od").autocomplete({ source: Tags_cilindro });
				$("#avcc_adicion_od").autocomplete({ source: Tags_adicion });			
				
				//Para REFRACCIÓN OI
				$("#avc_esfera_oi").autocomplete({ source: Tags_esfera });
				$("#avc_cilindro_oi").autocomplete({ source: Tags_cilindro });
				$("#avcc_adicion_oi").autocomplete({ source: Tags_adicion });
			});
		</script>
    </head>
    <body>
    	<?php
		$contenido->validar_seguridad(0);
		if (!isset($_POST['tipo_entrada'])) {
	    	$contenido->cabecera_html();	
    	}
		
		$valores_av = $listas->getListaDetalles(11);
		$equipos_nomograma = $listas->getListaDetalles(16);
		$valores_sino = $listas->getListaDetalles(10);
		$tabla_ojos = $listas->getListaDetalles(14);
		$id_tipo_reg = 8; // Tipo de registros Tabla:tipos_registros_hc= CONSULTA CONTROL LÁSER (OPTOMETRIA) para control de laser
		$id_usuario_crea = $_SESSION["idUsuario"];
		
		//Variables de Optometria
		if (isset($_POST['hdd_id_paciente'])) {
			$id_paciente = $_POST['hdd_id_paciente'];
			$nombre_paciente = $_POST['hdd_nombre_paciente'];
			$id_admision = $_POST['hdd_id_admision'];
			
			//Se obtienen los datos de la admision
			$admision_obj = $dbAdmision->get_admision($id_admision);
			
			//Se obtienen los datos del tipo de cita
			$tipo_cita_obj = $dbTiposCitas->get_tipo_cita($admision_obj["id_tipo_cita"]);
			
			if (!isset($_POST['tipo_entrada'])) {
				$tabla_hc = $controllaser->getHistoriaClinicaAdmision($id_admision, $id_tipo_reg);
			} else {
				$id_hc = $_POST['hdd_id_hc'];
				$tabla_hc = $controllaser->getHistoriaClinicaId($id_hc);
			}
			
			if (count($tabla_hc) > 0) { //Si se encuentra una HC para este paciente se carga los datos de lo contrario se crea la HC
				$tipo_accion='2';//Editar consulta optometria para prequirúrgico de laser
				$id_hc_consulta = $tabla_hc['id_hc'];
				//se obtiene el registro de la consulta de optometria a partir del ID de la Historia Clinica para prequirúrgico de laser
				$tabla_control_laser = $controllaser->getConsultaControlLaser($id_hc_consulta);	
				$anamnesis = $tabla_control_laser['anamnesis'];
				$avsc_lejos_od = $tabla_control_laser['avsc_lejos_od'];
				$avsc_cerca_od = $tabla_control_laser['avsc_cerca_od'];
				$avsc_lejos_oi = $tabla_control_laser['avsc_lejos_oi'];
				$avsc_cerca_oi = $tabla_control_laser['avsc_cerca_oi'];
				$querato_cilindro_od = $tabla_control_laser['querato_cilindro_od'];
				$querato_eje_od = $tabla_control_laser['querato_eje_od'];
				$querato_mplano_od = $tabla_control_laser['querato_mplano_od'];
				$querato_cilindro_oi = $tabla_control_laser['querato_cilindro_oi'];
				$querato_eje_oi = $tabla_control_laser['querato_eje_oi'];
				$querato_mplano_oi = $tabla_control_laser['querato_mplano_oi'];
				$avc_esfera_od = $tabla_control_laser['avc_esfera_od'];
				$avc_cilindro_od = $tabla_control_laser['avc_cilindro_od'];
				$avc_eje_od = $tabla_control_laser['avc_eje_od'];
				$avcc_lejos_od = $tabla_control_laser['avcc_lejos_od'];
				$avcc_adicion_od = $tabla_control_laser['avcc_adicion_od'];
				$avcc_cerca_od = $tabla_control_laser['avcc_cerca_od'];
				$avc_esfera_oi = $tabla_control_laser['avc_esfera_oi'];
				$avc_cilindro_oi = $tabla_control_laser['avc_cilindro_oi'];
				$avc_eje_oi = $tabla_control_laser['avc_eje_oi'];
				$avcc_lejos_oi = $tabla_control_laser['avcc_lejos_oi'];
				$avcc_adicion_oi = $tabla_control_laser['avcc_adicion_oi'];
				$avcc_cerca_oi = $tabla_control_laser['avcc_cerca_oi'];
				$diagnostico_control_laser = $tabla_control_laser['diagnostico_control_laser'];
				$txt_observaciones_avc = $tabla_control_laser['observaciones_avc'];
				
				//Se verifica si se debe actualizar el estado de la admisión asociada
				$en_atencion = "0";
				if (isset($_POST["hdd_en_atencion"])) {
					$en_atencion = $_POST["hdd_en_atencion"];
				}
				
				if ($en_atencion == "1") {
					$dbAdmision->editar_admision_estado($id_admision, 4, 1, $id_usuario_crea);
				}
			} else { //Entre en procesos de crear HC
				$tipo_accion='1';//Crear consulta optometria para control de laser
				//Se crea la historia clinica y se inicia la consulta de optometria
				$id_hc_consulta=$controllaser->CrearConsultaControlLaser($id_paciente, $id_tipo_reg, $id_usuario_crea, $id_admision);
				if ($id_hc_consulta < 0) { //Ninguna accion Error
					$tipo_accion = '0';
				} else {
					$tabla_control_laser = $controllaser->getConsultaControlLaser($id_hc_consulta);
				}
				
				//Variables de inicio de conuslta de optometria
				$anamnesis = "";
				$avsc_lejos_od = "";
				$avsc_cerca_od = "";
				$avsc_lejos_oi = "";
				$avsc_cerca_oi = "";
				$querato_cilindro_od = "";
				$querato_eje_od = "";
				$querato_mplano_od = "";
				$querato_cilindro_oi = "";
				$querato_eje_oi = "";
				$querato_mplano_oi = "";
				$avc_esfera_od = "";
				$avc_cilindro_od = "";
				$avc_eje_od = "";
				$avcc_lejos_od = "";
				$avcc_adicion_od = "";
				$avcc_cerca_od = "";
				$avc_esfera_oi = "";
				$avc_cilindro_oi = "";
				$avc_eje_oi = "";
				$avcc_lejos_oi = "";
				$avcc_adicion_oi = "";
				$avcc_cerca_oi = "";
				$diagnostico_control_laser = "";
				$txt_observaciones_avc = "";
			}
			
			//Se obtienen los datos del registro de historia clínica
			$historia_clinica_obj = $controllaser->getHistoriaClinicaId($id_hc_consulta);
		} else {
			$tipo_accion='0';//Ninguna accion Error
		}
		
		//fecha de la historia clinica
		$fecha_hc_t = $tabla_control_laser['fecha_hc_t'];		
		
		//Datos del paciente
		$datos_paciente = $pacientes->getEdadPaciente($id_paciente, '');
		$edad_paciente = $datos_paciente['edad'];
		$profesion_paciente = $datos_paciente['profesion'];
		
		//Nombre del profesional que atiende la consulta
		$id_usuario_profesional = $tabla_control_laser['id_usuario_crea'];
		$tabla_usuario_profesional = $usuarios->getUsuario($id_usuario_profesional);
		$nombre_usuario_profesional = $tabla_usuario_profesional['nombre_usuario'].' '.$tabla_usuario_profesional['apellido_usuario'];
		
		if (!isset($_POST['tipo_entrada'])) {
	?>
    <div class="title-bar title_hc">
        <div class="wrapper">
            <div class="breadcrumb">
                <ul>
                    <li class="breadcrumb_on">Consulta Control L&aacute;ser (Optometr&iacute;a)</li>
                </ul>
            </div>
        </div>
    </div>
    <?php
		}
		
	    if ($tipo_accion > 0) {
			//Para verificaro que tiene permiso de hacer cambio
			$ind_editar = $controllaser->getIndicadorEdicion($id_hc_consulta, $horas_edicion['valor_variable']);
			$ind_editar_enc_hc = $ind_editar;
			if ($ind_editar == 1 && isset($_POST['tipo_entrada'])) {
				$ind_editar_enc_hc = 0;
			}
			
			if (!isset($_POST['tipo_entrada']) || $_POST['tipo_entrada'] == 1) {
				$funciones_hc->encabezado_historia_clinica($id_paciente, $id_admision, $id_hc_consulta, $ind_editar_enc_hc, false);
			}
        ?>
        <div class="contenedor_principal" id="id_contenedor_principal">
	        <div id="guardar_control_laser" style="width: 100%; display: block;">
            	<div class='contenedor_error' id='contenedor_error'></div>
            	<div class='contenedor_exito' id='contenedor_exito'></div>
	        </div>
            <div id="d_imprimir_formula" style="width:100%; display:none;"></div>
            <div class="formulario" id="principal_optometria" style="width: 100%; display: block; ">
        		<?php
					//Se inserta el registro de ingreso a la historia clínica
					$controllaser->crear_ingreso_hc($id_usuario_crea, $id_paciente, $id_admision, $id_hc_consulta, 160);
					
					//Se verifica la información de que ojo se va a solicitar
					$bol_od = false;
					$bol_oi = false;
					switch ($tabla_control_laser["ojo"]) {
						case "OD":
							$bol_od = true;
							break;
						case "OI":
							$bol_oi = true;
							break;
						case "AO":
							$bol_od = true;
							$bol_oi = true;
							break;
					}
				?>
				<script type="text/javascript">
					//Se verifica la información de que ojo se va a solicitar
					var bol_od = false;
					var bol_oi = false;
					<?php
						switch ($tabla_control_laser["ojo"]) {
							case "OD":
					?>
					bol_od = true;
					<?php
								break;
							case "OI":
					?>
					bol_oi = true;
					<?php
								break;
							case "AO":
					?>
					bol_od = true;
					bol_oi = true;
					<?php
								break;
						}
					?>
				</script>
            	<form id='frm_consulta_control_laser' name='frm_consulta_control_laser' method="post">
                    <input type="hidden" value="<?php echo($id_hc_consulta); ?>" name="hdd_id_hc_consulta" id="hdd_id_hc_consulta"  />
                    <input type="hidden" value="<?php echo($id_admision); ?>" name="hdd_id_admision" id="hdd_id_admision"  />
                    <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
                        <tr valign="middle">
                            <td align="center" colspan="3">
                                <div class='contenedor_error' id='contenedor_error'></div>
                                <div class='contenedor_exito' id='contenedor_exito'></div>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="3" align="left">
								<h6 style="margin: 1px;">
									<b>Profesional que atiende:</b> <?php echo($nombre_usuario_profesional); ?>
								</h6>
							</th>
						</tr>    
						<tr>
							<th colspan="2" align="left"></th>
							<th colspan="1" align="left"></th>
						</tr>
					</table>
                    
                    <!--INICIO ANAMNESIS - AGUDEZA VISUAL SIN CORRECCI&Oacute;N -->	
		      		<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:98%;">
                    	<tr>
                        	<td align="center">
                            	<h5>Anamnesis *</h5>
                                <?php
									$anamnesis = $utilidades->ajustar_texto_wysiwyg($anamnesis);
								?>
                                <div id="anamnesis"><?php echo($anamnesis); ?></div>
                            </td>
                        </tr>
                    </table>
		  			<table border="0" cellpadding="0" cellspacing="0" align="center" style="width:98%;">
                        <tr>
                            <td align="center" colspan='3' class="td_tabla">
                                <div class="odoi_t">
                                <div class="od_t"><h5 style="margin: 0px;">OD</h5></div>
                                <div class="oi_t"><h5 style="margin: 0px;">OI</h5></div>
                                </div>
                            </td>
                        </tr>
				   	</table>
                    <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:98%;">
                        <tr>
                        	<td align="center" style="width:40%;">
                            	<table border="0" cellpadding="3" cellspacing="0" align="center" style="float:center; width:60%;">
                                	<tr>
                                    	<td align="center">
                                        	AVSC Lejos *<br />
                                            <?php
												$combo->getComboDb("avsc_lejos_od", $avsc_lejos_od, $valores_av, "id_detalle, nombre_detalle", " ", "validar_np_na('', '', '', '', 'avsc_lejos_od', 'avsc_cerca_od');", "", "", "", "select_hc");
											?>
                                        </td>
                                        <td align="center">
                                        	AVSC Cerca *<br />
                                            <?php
												$combo->getComboDb("avsc_cerca_od", $avsc_cerca_od, $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc");
											?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td align="center" style="width:20%;">
                            	<h5 style="margin:0;">Agudeza Visual sin Correcci&oacute;n</h5>
                            </td>
                            <td align="center" style="width:40%;">
                            	<table border="0" cellpadding="3" cellspacing="0" align="center" style="float:center; width:60%">
                                	<tr>
                                    	<td align="center">
                                        	AVSC Lejos *<br />
                                            <?php
												$combo->getComboDb("avsc_lejos_oi", $avsc_lejos_oi, $valores_av, "id_detalle, nombre_detalle", " ", "validar_np_na('', '', '', '', 'avsc_lejos_oi', 'avsc_cerca_oi');", "", "", "", "select_hc");
											?>
                                        </td>
                                        <td align="center">
                                        	AVSC Cerca *<br />
                                            <?php
												$combo->getComboDb("avsc_cerca_oi", $avsc_cerca_oi, $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc");
											?>
                                        </td>
                                    </tr>
                                </table>
            				</td>
					    </tr>
                        <tr class="opt_panel_1">
                            <td align="left">
                                <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:100%;">
                                	<tr>
                                        <td align="center">
                                            Cilindro *<br />
                                            <input type="text" class="input input_hc" value="<?php echo $querato_cilindro_od; ?>" name="querato_cilindro_od" id="querato_cilindro_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this); validar_np_na('querato_cilindro_od', 'querato_eje_od', 'querato_mplano_od', '', '', '');" />
                                        </td>
                                        <td align="center">
                                            Eje *<br />
                                            <input type="text" class="input input_hc" value="<?php echo $querato_eje_od; ?>" name="querato_eje_od" id="querato_eje_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);" />
                                        </td>
                                        <td align="center">
                                            K+Plano *<br />
                                            <input type="text" class="input input_hc" value="<?php echo $querato_mplano_od; ?>" name="querato_mplano_od" id="querato_mplano_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_querato, this);" />
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td align="center">
                            	<h5 style="margin:0;">Queratometr&iacute;a</h5>
                            </td>
                            <td align="left">
                                <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:100%;">
                                    <tr>
                                        <td align="center">
                                            Cilindro *<br />
                                            <input type="text" class="input input_hc" value="<?php echo $querato_cilindro_oi; ?>" name="querato_cilindro_oi" id="querato_cilindro_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this); validar_np_na('querato_cilindro_oi', 'querato_eje_oi', 'querato_mplano_oi', '', '', '');" />
                                        </td>
                                        <td align="center">
                                            Eje *<br />
                                            <input type="text" class="input input_hc" value="<?php echo $querato_eje_oi; ?>" name="querato_eje_oi" id="querato_eje_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);" />
                                        </td>
                                        <td align="center">
                                            K+Plano *<br />
                                            <input type="text" class="input input_hc" value="<?php echo $querato_mplano_oi; ?>" name="querato_mplano_oi" id="querato_mplano_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_querato, this);" />
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <table border="0" cellpadding="3" cellspacing="0" align="center" style="float:center; width:100%;">
                                    <tr>
                                        <td align="center">
                                            Esfera *<br />
                                            <input type="text" class="input input_hc" value="<?php echo $avc_esfera_od; ?>" name="avc_esfera_od" id="avc_esfera_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this); validar_np_na('avc_esfera_od', 'avc_cilindro_od', 'avc_eje_od', 'avcc_adicion_od', 'avcc_lejos_od', 'avcc_cerca_od');" />
                                        </td>
                                        <td align="center">
                                            Cilindro *<br />
                                            <input type="text" class="input input_hc" value="<?php echo $avc_cilindro_od; ?>" name="avc_cilindro_od" id="avc_cilindro_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);" />
                                        </td>
                                        <td align="center">
                                            Eje *<br />
                                            <input type="text" class="input input_hc" value="<?php echo $avc_eje_od; ?>" name="avc_eje_od" id="avc_eje_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);" />
                                        </td>
                    				</tr>
                                    <tr>
                                        <td align="center">
                                            AVCC Lejos *<br />
                                            <?php
                                            	$combo->getComboDb('avcc_lejos_od', $avcc_lejos_od, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '', 'select_hc');
                                            ?>
                                        </td>
                                        <td align="center">
                                            Adici&oacute;n<br />
                                            <input type="text" class="input input_hc" value="<?php echo $avcc_adicion_od; ?>" name="avcc_adicion_od" id="avcc_adicion_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_adicion, this);" />
                                        </td>
                                        <td align="center">
                                            AVCC Cerca *<br />
                                            <?php
                                            	$combo->getComboDb('avcc_cerca_od', $avcc_cerca_od, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '', 'select_hc');
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td align="center">
                            	<h5 style="margin:0;">Refracci&oacute;n y Agudeza Visual Corregida</h5>
                            </td>
                            <td align="left">
                                <table border="0" cellpadding="3" cellspacing="0" align="center" style="float:center; width:100%;">
                                    <tr>
                                        <td align="center">
                                            Esfera *<br />
                                            <input type="text" class="input input_hc" value="<?php echo $avc_esfera_oi; ?>" name="avc_esfera_oi" id="avc_esfera_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this); validar_np_na('avc_esfera_oi', 'avc_cilindro_oi', 'avc_eje_oi', 'avcc_adicion_oi', 'avcc_lejos_oi', 'avcc_cerca_oi');" />
                                        </td>
                                        <td align="center">
                                            Cilindro *<br />
                                            <input type="text" class="input input_hc" value="<?php echo $avc_cilindro_oi; ?>" name="avc_cilindro_oi" id="avc_cilindro_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);" />
                                        </td>
                                        <td align="center">
                                            Eje *<br />
                                            <input type="text" class="input input_hc" value="<?php echo $avc_eje_oi; ?>" name="avc_eje_oi" id="avc_eje_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center">
                                            AVCC Lejos *<br />
                                            <?php
                                            	$combo->getComboDb('avcc_lejos_oi', $avcc_lejos_oi, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '', 'select_hc');
                                            ?>
                                        </td>
                                        <td align="center">
                                            Adici&oacute;n<br />
                                            <input type="text" class="input input_hc" value="<?php echo $avcc_adicion_oi; ?>" name="avcc_adicion_oi" id="avcc_adicion_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_adicion, this);" />
                                        </td>
                                        <td align="center">
                                            AVCC Cerca *<br />
                                            <?php
                                            	$combo->getComboDb('avcc_cerca_oi', $avcc_cerca_oi, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '', '', '', 'select_hc');
                                            ?>
                                        </td>
                                    </tr>
                                </table>
	            			</td>
			            </tr>
                        <tr>
                            <td align="center" colspan='3' class="">
                                <h6 style="margin: 0px; width: 340px;">
                                    Observaciones f&oacute;rmula de gafas
                                </h6>
                                <div id="txt_observaciones_avc"><?php echo($utilidades->ajustar_texto_wysiwyg($txt_observaciones_avc)); ?></div>
                                <input type="button" id="btn_imprimir_avc" nombre="btn_imprimir_avc" class="btnPrincipal peq" style="font-size: 16px;" value="Imprimir f&oacute;rmula" onclick="generar_formula_gafas('txt_observaciones_avc', '<?php echo($fecha_hc_t);?>', '<?php echo($nombre_paciente);?>', '<?php echo($nombre_usuario_profesional); ?>', 'avc_esfera_od', 'avc_cilindro_od', 'avc_eje_od', 'avcc_adicion_od', 'avc_esfera_oi', 'avc_cilindro_oi', 'avc_eje_oi', 'avcc_adicion_oi', '<?php echo($id_admision); ?>');" /> 
                            </td>
                        </tr>
		            </table>
		  			<!--FIN REFRACCIÓN Y AGUDEZA VISUAL CORREGIDA  -->
		  			
		  			<!--INICIO DIAGNOSTICO-->
		  			<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:90%;">
                        <tr>
                            <td align="left" colspan="3">
                                <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:100%;">	
                                <tr>
                                    <td align="center" style="width:30%;">
                                    	<h5>Diagn&oacute;sticos</h5>
                                        <?php
                                        	$class_diagnosticos->getFormularioDiagnosticos($id_hc_consulta);
                                        ?>
                                        <label><b>Otros diagn&oacute;sticos y an&aacute;lisis</b></label>
                                        <?php
											$diagnostico_control_laser = $utilidades->ajustar_texto_wysiwyg($diagnostico_control_laser);
										?>
                                        <div id="diagnostico_control_laser"><?php echo($diagnostico_control_laser); ?></div>
                                    </td>
                                </tr>
                                </table>
                            </td>
                        </tr>
		   			</table>
		  			<!--FIN DIAGNOSTICO-->
                    
                    <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:98%;">
                        <tr valign="top">
                            <td colspan='3'>
							<?php
								if (!isset($_POST['tipo_entrada'])) {
							?>
							<input class="btnPrincipal" type="button" id="btn_imprimir" nombre="btn_imprimir" value="Imprimir" onclick="crear_control_laser(2, 1);" />
							<?php
								} else {
							?>
							<input class="btnPrincipal" type="button" id="btn_imprimir" nombre="btn_imprimir" value="Imprimir" onclick="imprimir_control_laser();" />
							<?php
								}
								
								if ($ind_editar == 1) {
									if (!isset($_POST['tipo_entrada'])) {
							?>
							<input type="button" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Guardar cambios" onclick="crear_control_laser(2, 0);" />
                            <?php
								$id_tipo_cita = $admision_obj["id_tipo_cita"];
								$lista_tipos_citas_det_remisiones = $dbTiposCitasDetalle->get_lista_tipos_citas_det_remisiones($id_tipo_cita, $id_tipo_reg);
								
								if (count($lista_tipos_citas_det_remisiones) > 0) {
							?>
							<input class="btnPrincipal" type="button" id="btn_enviar_a" nombre="btn_enviar_a" value="Enviar a ..." onclick="enviar_a_estados();" />
							<?php
								}
                            ?>
							<input type="button" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Finalizar consulta" onclick="crear_control_laser(1, 0);" />
							<?php
									} else {
							?>
                            <input type="button" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Guardar" onclick="crear_control_laser(3, 0);" />
                            <?php
									}
								}
							?>
                            </td>
                        </tr>
                    </table>
                    <br/><br/>
				</form>
		    </div>
    	</div>
    	<?php
			} else {
		?>
		<div class="contenedor_principal">
			<div class="contenedor_error" style="display:block;">Error al ingresar a la consulta de control l&aacute;ser</div>
		</div>
		<?php
			}
    	?>
   <script type='text/javascript' src='../js/foundation.min.js'></script>
   <script>
	 	$(document).foundation();
		
		initCKEditorControl("anamnesis");
		initCKEditorControl("txt_observaciones_avc");
		initCKEditorControl("diagnostico_control_laser");
   </script>
    <?php
	   if(!isset($_POST['tipo_entrada'])){
	    	$contenido->ver_historia($id_paciente);
		    $contenido->footer();
	   }
	   else{
			$contenido->footer_iframe();
	   }
	?>
</body>
</html>
