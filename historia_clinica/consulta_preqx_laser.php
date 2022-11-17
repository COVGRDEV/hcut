<?php
	session_start();
	/*
	  Pagina para crear consulta prequirurgica de laser 
	  Autor: Helio Ruber López - 19/02/2014
	*/
	require_once("../db/DbVariables.php");
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbConsultaPreqxLaser.php");
	require_once("../db/DbConsultaOptometria.php");
	require_once("../db/DbAdmision.php");
	require_once("../db/DbTiposCitas.php");
	require_once("../db/DbPacientes.php");
	require_once("../db/DbDiagnosticos.php");
	require_once("../db/DbTiposCitasDetalle.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/Class_Diagnosticos.php");
	require_once("../funciones/Utilidades.php");
	require_once("FuncionesHistoriaClinica.php");
	
	$dbVariables = new Dbvariables();
	$dbUsuarios = new DbUsuarios();
	$dbListas = new DbListas();
	$dbConsultaPreqxLaser = new DbConsultaPreqxLaser();
	$dbConsultaOptometria = new DbConsultaOptometria();
	$dbAdmision = new DbAdmision();
	$dbTiposCitas = new DbTiposCitas();
	$dbPacientes = new DbPacientes();
	$db_diagnosticos = new DbDiagnosticos();
	$dbTiposCitasDetalle = new DbTiposCitasDetalle();
	$contenido = new ContenidoHtml();
	$combo = new Combo_Box();
	$class_diagnosticos = new Class_Diagnosticos();
	$utilidades = new Utilidades();
	$funciones_hc = new FuncionesHistoriaClinica();
	
	//variables
	$titulo = $dbVariables->getVariable(1);
	$horas_edicion = $dbVariables->getVariable(7);
	
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
        <script type='text/javascript' src='historia_clinica_v1.1.js'></script>
        <script type='text/javascript' src='FuncionesHistoriaClinica.js'></script>
        <script type='text/javascript' src='consulta_preqx_laser_v1.6.js'></script>
        <?php
			$tabla_diagnosticos = $db_diagnosticos->getDiagnosticoCiexTotal();
			$i = 0;
			$cadena_diagnosticos = "";
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
					$("#txt_busca_diagnostico_"+k).autocomplete({ source: Tags_diagnosticos });
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
			
			//Array valores cilindro
			$cadena_cilindro = "'0'";
	        for ($i = 0; $i <= 10; $i++) {
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
				$("#querato_kplano_od").autocomplete({ source: Tags_querato });
				//Para queratometria OI
				$("#querato_cilindro_oi").autocomplete({ source: Tags_cilindro });
				$("#querato_kplano_oi").autocomplete({ source: Tags_querato });
				
				//Para REFRACCIÓN OD
				$("#refraccion_esfera_od").autocomplete({ source: Tags_esfera });
				$("#refraccion_cilindro_od").autocomplete({ source: Tags_cilindro });
				//Para REFRACCIÓN OI
				$("#refraccion_esfera_oi").autocomplete({ source: Tags_esfera });
				$("#refraccion_cilindro_oi").autocomplete({ source: Tags_cilindro });
				
				//Para REFRACCIÓN OD COMPLEMENTO
				$("#refraccion_complemento_esfera_od").autocomplete({ source: Tags_esfera });
				$("#refraccion_complemento_cilindro_od").autocomplete({ source: Tags_cilindro });
				//Para REFRACCIÓN OI COMPLEMENTO
				$("#refraccion_complemento_esfera_oi").autocomplete({ source: Tags_esfera });
				$("#refraccion_complemento_cilindro_oi").autocomplete({ source: Tags_cilindro });
				
				//para CICLOPEGIA OD
				$("#cicloplejio_esfera_od").autocomplete({ source: Tags_esfera });
				$("#cicloplejio_cilindro_od").autocomplete({ source: Tags_cilindro });
				//para CICLOPEGIA OI
				$("#cicloplejio_esfera_oi").autocomplete({ source: Tags_esfera });
				$("#cicloplejio_cilindro_oi").autocomplete({ source: Tags_cilindro });
				
				//para DEFECTO REFRACTIVO PROGRAMADO OD
				$("#refractivo_esfera_od").autocomplete({ source: Tags_esfera });
				$("#refractivo_cilindro_od").autocomplete({ source: Tags_cilindro });
				//para DEFECTO REFRACTIVO PROGRAMADO OI
				$("#refractivo_esfera_oi").autocomplete({ source: Tags_esfera });
				$("#refractivo_cilindro_oi").autocomplete({ source: Tags_cilindro });
				
				//para NOMOGRAMA OD
				$("#nomograma_esfera_od").autocomplete({ source: Tags_esfera });
				$("#nomograma_cilindro_od").autocomplete({ source: Tags_cilindro });
				//para NOMOGRAMA OI
				$("#nomograma_esfera_oi").autocomplete({ source: Tags_esfera });
				$("#nomograma_cilindro_oi").autocomplete({ source: Tags_cilindro });
			});
		</script>
    </head>
    <body>
    	<?php
			$contenido->validar_seguridad(0);
			if (!isset($_POST['tipo_entrada'])) {
				$contenido->cabecera_html();	
			}
			$valores_av = $dbListas->getListaDetalles(11);
			$equipos_nomograma = $dbListas->getListaDetalles(16);
			$valores_sino = $dbListas->getListaDetalles(10);
			$tabla_ojos = $dbListas->getListaDetalles(14);
			$id_tipo_reg = 6; // Tipo de registros Tabla:tipos_registros_hc= CONSULTA PREQUIRÚRGICA LÁSER (OPTOMETRÍA) para prequirúrgico de laser
			$id_usuario_crea = $_SESSION["idUsuario"];
			$id_menu_preqx_laser = 26;
			
			if (isset($_POST['hdd_id_paciente'])) {
				$id_paciente = $_POST['hdd_id_paciente'];
				$nombre_paciente = $_POST['hdd_nombre_paciente'];
				$id_admision = $_POST['hdd_id_admision'];
				
				//Se obtienen los datos de la admision
				$admision_obj = $dbAdmision->get_admision($id_admision);
				
				//Se obtienen los datos del tipo de cita
				$tipo_cita_obj = $dbTiposCitas->get_tipo_cita($admision_obj["id_tipo_cita"]);
				
				if (!isset($_POST['tipo_entrada'])) {
					$tabla_hc = $dbConsultaPreqxLaser->getHistoriaClinicaAdmision($id_admision, $id_tipo_reg);
				} else {
					$id_hc = $_POST['hdd_id_hc'];
					$tabla_hc = $dbConsultaPreqxLaser->getHistoriaClinicaId($id_hc);
				}
				
				if (count($tabla_hc) > 0) { //Si se encuentra una HC para este paciente se carga los datos de lo contrario se crea la HC
					$tipo_accion = '2'; //Editar consulta optometria para prequirúrgico de laser
					$id_hc_consulta = $tabla_hc['id_hc'];
					
					//se obtiene el registro de la consulta de optometria a partir del ID de la Historia Clinica para prequirúrgico de laser
					$tabla_preqx_laser = $dbConsultaPreqxLaser->getConsultaPreqxLaser($id_hc_consulta);	
					
					$ojo_operar = $tabla_preqx_laser['ojo_operar'];
					$querato_cilindro_od = $tabla_preqx_laser['querato_cilindro_od'];
					$querato_eje_od = $tabla_preqx_laser['querato_eje_od'];
					$querato_kplano_od = $tabla_preqx_laser['querato_kplano_od'];
					$querato_cilindro_oi = $tabla_preqx_laser['querato_cilindro_oi'];
					$querato_eje_oi = $tabla_preqx_laser['querato_eje_oi'];
					$querato_kplano_oi = $tabla_preqx_laser['querato_kplano_oi'];
					$refraccion_esfera_od = $tabla_preqx_laser['refraccion_esfera_od'];
					$refraccion_cilindro_od = $tabla_preqx_laser['refraccion_cilindro_od'];
					$refraccion_eje_od = $tabla_preqx_laser['refraccion_eje_od'];
					$refraccion_lejos_od = $tabla_preqx_laser['refraccion_lejos_od'];
					$refraccion_cerca_od = $tabla_preqx_laser['refraccion_cerca_od'];
					$refraccion_esfera_oi = $tabla_preqx_laser['refraccion_esfera_oi'];
					$refraccion_cilindro_oi = $tabla_preqx_laser['refraccion_cilindro_oi'];
					$refraccion_eje_oi = $tabla_preqx_laser['refraccion_eje_oi'];
					$refraccion_lejos_oi = $tabla_preqx_laser['refraccion_lejos_oi'];
					$refraccion_cerca_oi = $tabla_preqx_laser['refraccion_cerca_oi'];
					$cicloplejio_esfera_od = $tabla_preqx_laser['cicloplejio_esfera_od'];
					$cicloplejio_cilindro_od = $tabla_preqx_laser['cicloplejio_cilindro_od'];
					$cicloplejio_eje_od = $tabla_preqx_laser['cicloplejio_eje_od'];
					$cicloplejio_avcc_lejos_od = $tabla_preqx_laser['cicloplejio_avcc_lejos_od'];
					$cicloplejio_esfera_oi = $tabla_preqx_laser['cicloplejio_esfera_oi'];
					$cicloplejio_cilindro_oi = $tabla_preqx_laser['cicloplejio_cilindro_oi'];
					$cicloplejio_eje_oi = $tabla_preqx_laser['cicloplejio_eje_oi'];
					$cicloplejio_avcc_lejos_oi = $tabla_preqx_laser['cicloplejio_avcc_lejos_oi'];
					$refractivo_deseado_od = $tabla_preqx_laser['refractivo_deseado_od'];
					$refractivo_esfera_od = $tabla_preqx_laser['refractivo_esfera_od'];
					$refractivo_cilindro_od = $tabla_preqx_laser['refractivo_cilindro_od'];
					$refractivo_eje_od = $tabla_preqx_laser['refractivo_eje_od'];
					$refractivo_deseado_oi = $tabla_preqx_laser['refractivo_deseado_oi'];
					$refractivo_esfera_oi = $tabla_preqx_laser['refractivo_esfera_oi'];
					$refractivo_cilindro_oi = $tabla_preqx_laser['refractivo_cilindro_oi'];
					$refractivo_eje_oi = $tabla_preqx_laser['refractivo_eje_oi'];
					$nomograma_equipo = $tabla_preqx_laser['nomograma_equipo'];
					$nomograma_esfera_od = $tabla_preqx_laser['nomograma_esfera_od'];
					$nomograma_cilindro_od = $tabla_preqx_laser['nomograma_cilindro_od'];
					$nomograma_eje_od = $tabla_preqx_laser['nomograma_eje_od'];
					$nomograma_esfera_oi = $tabla_preqx_laser['nomograma_esfera_oi'];
					$nomograma_cilindro_oi = $tabla_preqx_laser['nomograma_cilindro_oi'];
					$nomograma_eje_oi = $tabla_preqx_laser['nomograma_eje_oi'];
					$patologia_ocular_valor = $tabla_preqx_laser['patologia_ocular_valor'];
					$patologia_ocular_descripcion = $tabla_preqx_laser['patologia_ocular_descripcion'];
					$cirugia_ocular_valor = $tabla_preqx_laser['cirugia_ocular_valor'];
					$cirugia_ocular_descripcion = $tabla_preqx_laser['cirugia_ocular_descripcion'];
					$paquimetria_central_od = $tabla_preqx_laser['paquimetria_central_od'];
					$paquimetria_central_oi = $tabla_preqx_laser['paquimetria_central_oi'];
					$diagnostico_preqx_laser = $tabla_preqx_laser['diagnostico_preqx_laser'];
					$paquimetria_periferica_od = $tabla_preqx_laser['paquimetria_periferica_od'];
					$paquimetria_periferica_oi = $tabla_preqx_laser['paquimetria_periferica_oi'];
					
					//se obtiene el registro del complemento de la consulta de optometria a partir del ID de la Historia Clinica para prequirúrgico de laser
					$tabla_complemento_preqx_laser = $dbConsultaPreqxLaser->getComplementoPreqxLaser($id_hc_consulta);	
					$refraccion_complemento_esfera_od = $tabla_complemento_preqx_laser['refraccion_esfera_od'];
					$refraccion_complemento_cilindro_od = $tabla_complemento_preqx_laser['refraccion_cilindro_od'];
					$refraccion_complemento_eje_od = $tabla_complemento_preqx_laser['refraccion_eje_od'];
					$refraccion_complemento_lejos_od = $tabla_complemento_preqx_laser['refraccion_lejos_od'];
					$refraccion_complemento_cerca_od = $tabla_complemento_preqx_laser['refraccion_cerca_od'];
					$refraccion_complemento_esfera_oi = $tabla_complemento_preqx_laser['refraccion_esfera_oi'];
					$refraccion_complemento_cilindro_oi = $tabla_complemento_preqx_laser['refraccion_cilindro_oi'];
					$refraccion_complemento_eje_oi = $tabla_complemento_preqx_laser['refraccion_eje_oi'];
					$refraccion_complemento_lejos_oi = $tabla_complemento_preqx_laser['refraccion_lejos_oi'];
					$refraccion_complemento_cerca_oi = $tabla_complemento_preqx_laser['refraccion_cerca_oi'];
					
					//Se verifica si se debe actualizar el estado de la admisión asociada
					$en_atencion = "0";
					if (isset($_POST["hdd_en_atencion"])) {
						$en_atencion = $_POST["hdd_en_atencion"];
					}
					
					if ($en_atencion == "1") {
						$dbAdmision->editar_admision_estado($id_admision, 4, 1, $id_usuario_crea);
					}
				} else { //Entre en procesos de crear HC
					$tipo_accion = '1'; //Crear consulta optometria para prequirúrgico de laser
					
					//Se crea la historia clinica y se inicia la consulta de optometria
					$id_hc_consulta = $dbConsultaPreqxLaser->CrearConsultaPreqxLaser($id_paciente, $id_tipo_reg, $id_usuario_crea, $id_admision);
					
					if ($id_hc_consulta < 0) { //Ninguna accion Error
						$tipo_accion = '0';
					} else {
						$tabla_preqx_laser = $dbConsultaPreqxLaser->getConsultaPreqxLaser($id_hc_consulta);
						$tabla_complemento_preqx_laser = $dbConsultaPreqxLaser->getComplementoPreqxLaser($id_hc_consulta);
					}
					
					//Variables de inicio de conuslta de optometria
					$ojo_operar = "";
					$querato_cilindro_od = "";
					$querato_eje_od = "";
					$querato_kplano_od = "";
					$querato_cilindro_oi = "";
					$querato_eje_oi = "";
					$querato_kplano_oi = "";
					$refraccion_esfera_od = "";
					$refraccion_cilindro_od = "";
					$refraccion_eje_od = "";
					$refraccion_lejos_od = "";
					$refraccion_cerca_od = "";
					$refraccion_esfera_oi = "";
					$refraccion_cilindro_oi = "";
					$refraccion_eje_oi = "";
					$refraccion_lejos_oi = "";
					$refraccion_cerca_oi = "";
					$cicloplejio_esfera_od = "";
					$cicloplejio_cilindro_od = "";
					$cicloplejio_eje_od = "";
					$cicloplejio_avcc_lejos_od = "";
					$cicloplejio_esfera_oi = "";
					$cicloplejio_cilindro_oi = "";
					$cicloplejio_eje_oi = "";
					$cicloplejio_avcc_lejos_oi = "";
					$refractivo_deseado_od = "";
					$refractivo_esfera_od = "";
					$refractivo_cilindro_od = "";
					$refractivo_eje_od = "";
					$refractivo_deseado_oi = "";
					$refractivo_esfera_oi = "";
					$refractivo_cilindro_oi = "";
					$refractivo_eje_oi = "";
					$nomograma_equipo = "";
					$nomograma_esfera_od = "";
					$nomograma_cilindro_od = "";
					$nomograma_eje_od = "";
					$nomograma_esfera_oi = "";
					$nomograma_cilindro_oi = "";
					$nomograma_eje_oi = "";
					$patologia_ocular_valor = "";
					$patologia_ocular_descripcion = "";
					$cirugia_ocular_valor = "";
					$cirugia_ocular_descripcion = "";
					$paquimetria_central_od = "";
					$paquimetria_central_oi = "";
					$diagnostico_preqx_laser = "";
					$paquimetria_periferica_od = "";
					$paquimetria_periferica_oi = "";
					
					//Datos complemento consulta preqx laser
					$refraccion_complemento_esfera_od = '';
					$refraccion_complemento_cilindro_od = '';
					$refraccion_complemento_eje_od = '';
					$refraccion_complemento_lejos_od = '';
					$refraccion_complemento_cerca_od = '';
					$refraccion_complemento_esfera_oi = '';
					$refraccion_complemento_cilindro_oi = '';
					$refraccion_complemento_eje_oi = '';
					$refraccion_complemento_lejos_oi = '';
					$refraccion_complemento_cerca_oi = '';
				}
				
				//Se obtienen los datos del registro de historia clínica
				$historia_clinica_obj = $dbConsultaPreqxLaser->getHistoriaClinicaId($id_hc_consulta);
			} else {
				$tipo_accion = '0'; //Ninguna accion Error
			}
			
			//Edad del paciente
			$datos_paciente = $dbPacientes->getEdadPaciente($id_paciente, '');
			$edad_paciente = $datos_paciente['edad'];
			$profesion_paciente = $datos_paciente['profesion'];
			
			//Nombre del profesional que atiende la consulta
			$id_usuario_profesional = $tabla_preqx_laser['id_usuario_crea'];
			$tabla_usuario_profesional = $dbUsuarios->getUsuario($id_usuario_profesional);
			$nombre_usuario_profesional = $tabla_usuario_profesional['nombre_usuario'].' '.$tabla_usuario_profesional['apellido_usuario'];
			
			//Validar que el usuario que inicio session puede agregar datos complementarios  
			//$ind_datos_complementarios = $dbConsultaPreqxLaser->ValidarUsuarioOptometra($id_hc_consulta, $id_usuario_crea, $id_menu_preqx_laser);
			
			if (!isset($_POST['tipo_entrada'])) {
	    ?>
        <div class="title-bar title_hc">
            <div class="wrapper">
                <div class="breadcrumb">
                    <ul>
                        <li class="breadcrumb_on">Consulta Prequir&uacute;rgica L&aacute;ser (Optometr&iacute;a)</li>
                    </ul>
                </div>
            </div>
        </div>
        <?php
			}
			
			if ($tipo_accion > 0) {
				//Para verificaro que tiene permiso de hacer cambio
				$ind_editar = $dbConsultaPreqxLaser->getIndicadorEdicion($id_hc_consulta, $horas_edicion['valor_variable']);
				$ind_editar_enc_hc = $ind_editar;
				if ($ind_editar == 1 && isset($_POST['tipo_entrada'])) {
					$ind_editar_enc_hc = 0;
				}
				
				if (!isset($_POST['tipo_entrada']) || $_POST['tipo_entrada'] == 1) {
					$funciones_hc->encabezado_historia_clinica($id_paciente, $id_admision, $id_hc_consulta, $ind_editar_enc_hc, false);
				}
        ?>
        <div class="contenedor_principal" id="id_contenedor_principal">
            <div id="guardar_preqx_laser" style="width: 100%; display: block;">
                <div class='contenedor_error' id='contenedor_error'></div>
                <div class='contenedor_exito' id='contenedor_exito'></div>
            </div>	
            <div class="formulario" id="principal_optometria" style="width: 100%; display: block;">
        		<?php
					//Se inserta el registro de ingreso a la historia clínica
					$dbConsultaPreqxLaser->crear_ingreso_hc($id_usuario_crea, $id_paciente, $id_admision, $id_hc_consulta, 160);
					
					//Se verifica la información de que ojo se va a solicitar
					$bol_od = false;
					$bol_oi = false;
					switch ($tabla_preqx_laser["ojo"]) {
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
					
					//Se obtienen los datos de la última consulta de optometría realizada antes del registro actual
					$consulta_optometria_obj = $dbConsultaOptometria->getConsultaOptometriaAnterior($id_hc_consulta);
					
					$refraccion_complemento_esfera_od = "";
					$refraccion_complemento_cilindro_od = "";
					$refraccion_complemento_eje_od = "";
					$refraccion_complemento_lejos_od = "";
					$refraccion_complemento_cerca_od = "";
					
					$refraccion_complemento_esfera_oi = "";
					$refraccion_complemento_cilindro_oi = "";
					$refraccion_complemento_eje_oi = "";
					$refraccion_complemento_lejos_oi = "";
					$refraccion_complemento_cerca_oi = "";
					
					$nombre_usuario_profesional_complemento = "";
					$fecha_complemento = "";
					
					if (isset($consulta_optometria_obj["id_hc"])) {
						if ($bol_od) {
							$refraccion_complemento_esfera_od = $consulta_optometria_obj["refrafinal_esfera_od"];
							$refraccion_complemento_cilindro_od = $consulta_optometria_obj["refrafinal_cilindro_od"];
							$refraccion_complemento_eje_od = $consulta_optometria_obj["refrafinal_eje_od"];
							$refraccion_complemento_lejos_od = $consulta_optometria_obj["subjetivo_lejos_od"];
							$refraccion_complemento_cerca_od = $consulta_optometria_obj["subjetivo_cerca_od"];
						}
						
						if ($bol_oi) {
							$refraccion_complemento_esfera_oi = $consulta_optometria_obj["refrafinal_esfera_oi"];
							$refraccion_complemento_cilindro_oi = $consulta_optometria_obj["refrafinal_cilindro_oi"];
							$refraccion_complemento_eje_oi = $consulta_optometria_obj["refrafinal_eje_oi"];
							$refraccion_complemento_lejos_oi = $consulta_optometria_obj["subjetivo_lejos_oi"];
							$refraccion_complemento_cerca_oi = $consulta_optometria_obj["subjetivo_cerca_oi"];
						}
						
						//Nombre del profesional que atendió la consulta de los datos complementarios
						$id_usuario_profesional_complemento = $consulta_optometria_obj["id_usuario_crea"];
						$usuario_profesional_complemento_obj = $dbUsuarios->getUsuario($id_usuario_profesional_complemento);
						$nombre_usuario_profesional_complemento = $usuario_profesional_complemento_obj["nombre_usuario"]." ".$usuario_profesional_complemento_obj["apellido_usuario"];
						
						$fecha_complemento = $consulta_optometria_obj["fecha_hc_t"];
					}
				?>
				<script type="text/javascript">
					//Se verifica la información de que ojo se va a solicitar
					var bol_od = false;
					var bol_oi = false;
					<?php
						switch ($tabla_preqx_laser["ojo"]) {
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
                <form id='frm_consulta_preqx_laser' name='frm_consulta_preqx_laser' method="post">
                    <input type="hidden" value="<?php echo($id_hc_consulta); ?>" name="hdd_id_hc_consulta" id="hdd_id_hc_consulta"  />
                    <input type="hidden" value="<?php echo($id_admision); ?>" name="hdd_id_admision" id="hdd_id_admision"  />
                    <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
                        <tr valign="middle">
                            <td align="center" colspan="2">
                                <div class='contenedor_error' id='contenedor_error'></div>
                                <div class='contenedor_exito' id='contenedor_exito'></div>
                            </td>
                        </tr>	
                        <tr>
                            <th colspan="2" align="left">
                                <h6 style="margin:1px;">
                                    <b>Profesional que atiende:</b> <?php echo($nombre_usuario_profesional); ?>
                                </h6>
            				</th>
                        </tr>    
                        <tr>
                            <th align="left" style="width:90%;">
                                <h6 style="margin:1px;">
                                    <b>Cirug&iacute;a:</b> <?php echo($tabla_preqx_laser["nombre_cirugia"]); ?>
                                    <br />
                                    <b>Fecha de la cirug&iacute;a:</b> <?php echo($tabla_preqx_laser["fecha_cirugia_t"]); ?>
                                </h6>
                            </th>   
                            <th align="left" style="width:10%;">   
                                <h6 style="margin:1px;">
                                    <b>Ojo:</b> <?php echo($tabla_preqx_laser["ojo"]); ?>
                                    <br />
                                    <?php echo($tabla_preqx_laser["num_cirugia"]); ?>a cirug&iacute;a
                                </h6>
                            </th>
                        </tr>
                    </table>
                    <br />
				  	<table border="0" cellpadding="0" cellspacing="0" align="center" style="width:98%;">
                        <tr>
                            <td align="center" colspan="5" class="td_tabla">
                                <div class="odoi_t">
                                    <div class="od_t"><h5 style="margin: 0px;">OD</h5></div>
                                    <div class="oi_t"><h5 style="margin: 0px;">OI</h5></div>
                                </div>
                            </td>
                        </tr>
                        <tr style="height:5px;"></tr>
                        <tr>
                            <td align="right" colspan="2" style="width:40%;">
                            	<table border="0" cellpadding="1" cellspacing="0" align="right">
                                    <tr>
                                        <td align="center">
                                            Cilindro<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $querato_cilindro_od; ?>" name="querato_cilindro_od" id="querato_cilindro_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                        <td align="center">
                                            Eje<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $querato_eje_od; ?>" name="querato_eje_od" id="querato_eje_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                        <td align="center">
                                            K+Plano<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $querato_kplano_od; ?>" name="querato_kplano_od" id="querato_kplano_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_querato, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td align="center" style="width:20%;">
                            	<h5>Queratometr&iacute;a</h5>
                            </td>
                            <td align="left" style="width:40%;" colspan="2">
                                <table border="0" cellpadding="1" cellspacing="0" align="left">
                                    <tr>
                                        <td align="center">
                                            Cilindro<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $querato_cilindro_oi; ?>" name="querato_cilindro_oi" id="querato_cilindro_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                        <td align="center">
                                            Eje<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $querato_eje_oi; ?>" name="querato_eje_oi" id="querato_eje_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                        <td align="center">
                                            K+Plano<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $querato_kplano_oi; ?>" name="querato_kplano_oi" id="querato_kplano_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_querato, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr class="opt_panel_1">
                            <td align="center" colspan="5">
                                <h5>Refracci&oacute;n</h5>
                                <div id="guardar_complemento_preqx_laser" style="width: 100%; display: block;">
                                    <div class='contenedor_error' id='contenedor_error_complemento'></div>
                                    <div class='contenedor_exito' id='contenedor_exito_complemento'></div>
                                </div>
                            </td>
                        </tr>
                        <tr class="opt_panel_1">
                            <td align="right" valign="top" style="width:28%;">
                                <table border="0" cellpadding="1" cellspacing="0" align="right">
                                    <tr>
                                        <td align="center">
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $refraccion_complemento_esfera_od; ?>" name="refraccion_complemento_esfera_od" id="refraccion_complemento_esfera_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);" disabled="disabled" />
                                        </td>
                                    </tr>
                                    <tr>		
                                        <td align="center">
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $refraccion_complemento_cilindro_od; ?>" name="refraccion_complemento_cilindro_od" id="refraccion_complemento_cilindro_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);" disabled="disabled" />
                                        </td>
                                    </tr>
                                    <tr>	
                                        <td align="center">
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $refraccion_complemento_eje_od; ?>" name="refraccion_complemento_eje_od" id="refraccion_complemento_eje_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);" disabled="disabled" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center">
                                            <?php
												$combo->getComboDb('refraccion_complemento_lejos_od', $refraccion_complemento_lejos_od, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '0', 'width:90px;', '', 'select_hc');
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>	
                                        <td align="center">
                                            <?php
												$combo->getComboDb('refraccion_complemento_cerca_od', $refraccion_complemento_cerca_od, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '0', 'width:90px;', '', 'select_hc');
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>	
                                        <td align="center" valign="top">
                                            Primer Evaluador<br />
                                           	<?php
												if ($bol_od) {
													echo("<b>".$nombre_usuario_profesional_complemento."</b><br />".$fecha_complemento);
												}
											?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td align="left" valign="top" style="width:12%;">
                                <table border="0" cellpadding="1" cellspacing="0" align="left">
                                    <tr>
                                        <td align="center">
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $refraccion_esfera_od; ?>" name="refraccion_esfera_od" id="refraccion_esfera_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                    </tr>
                                    <tr>		
                                        <td align="center">
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $refraccion_cilindro_od; ?>" name="refraccion_cilindro_od" id="refraccion_cilindro_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                    </tr>
                                    <tr>	
                                        <td align="center">
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $refraccion_eje_od; ?>" name="refraccion_eje_od" id="refraccion_eje_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center">
                                            <?php
												$combo->getComboDb('refraccion_lejos_od', $refraccion_lejos_od, $valores_av, 'id_detalle, nombre_detalle', ' ', '', $bol_od, 'width:90px;', '', 'select_hc');	
											?>
                                        </td>
                                    </tr>
                                    <tr>	
                                        <td align="center">
                                            <?php
												$combo->getComboDb('refraccion_cerca_od', $refraccion_cerca_od, $valores_av, 'id_detalle, nombre_detalle', ' ', '', $bol_od, 'width:90px;', '', 'select_hc');	
											?>
                                        </td>
                                    </tr>
                                    <tr>	
                                        <td align="center" valign="top">
                                            Segundo Evaluador<br />
                                            <b>
                                            	<?php
													if ($bol_od) {
														echo($nombre_usuario_profesional);
													}
												?>
                                            </b>
                                        </td>
                                    </tr>
                                </table>
                            </td>
		   					<td align="center" valign="top">
                            	<table border="0" cellpadding="7" cellspacing="0" style="width:100%;">
                                	<tr><td align="center"><h6 style="margin:0;">Esfera</h6></td></tr>
                                	<tr><td align="center"><h6 style="margin:0;">Cilindro</h6></td></tr>
                                	<tr><td align="center"><h6 style="margin:0;">Eje</h6></td></tr>
                                	<tr><td align="center"><h6 style="margin:0;">AVCC Lejos</h6></td></tr>
                                	<tr><td align="center"><h6 style="margin:0;">AVCC Cerca</h6></td></tr>
                                	<tr><td align="center"><h6 style="margin:0;">Evaluador</h6></td></tr>
                                </table>
                            </td>
                            <td align="right" valign="top" style="width:12%;">
                                <table border="0" cellpadding="1" cellspacing="0" align="right">
                                    <tr>
                                        <td align="center">
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $refraccion_complemento_esfera_oi; ?>" name="refraccion_complemento_esfera_oi" id="refraccion_complemento_esfera_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);" disabled="disabled" />
                                        </td>
                                    </tr>
                                    <tr>		
                                        <td align="center">
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $refraccion_complemento_cilindro_oi; ?>" name="refraccion_complemento_cilindro_oi" id="refraccion_complemento_cilindro_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);" disabled="disabled" />
                                        </td>
                                    </tr>
                                    <tr>	
                                        <td align="center">
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $refraccion_complemento_eje_oi; ?>" name="refraccion_complemento_eje_oi" id="refraccion_complemento_eje_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);" disabled="disabled" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center">
                                            <?php
												$combo->getComboDb('refraccion_complemento_lejos_oi', $refraccion_complemento_lejos_oi, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '0', 'width:90px;', '', 'select_hc');
											?>
                                        </td>
                                    </tr>
                                    <tr>	
                                        <td align="center">
                                            <?php
												$combo->getComboDb('refraccion_complemento_cerca_oi', $refraccion_complemento_cerca_oi, $valores_av, 'id_detalle, nombre_detalle', ' ', '', '0', 'width:90px;', '', 'select_hc');
											?>
                                        </td>
                                    </tr>
                                    <tr>	
                                        <td align="center" valign="top">
                                            Primer Evaluador<br />
											<?php
												if ($bol_oi) {
													echo("<b>".$nombre_usuario_profesional_complemento."</b><br />".$fecha_complemento);
												}
											?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td align="left" valign="top" style="width:28%;">
                                <table border="0" cellpadding="1" cellspacing="0" align="left">
                                    <tr>
                                        <td align="center">
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $refraccion_esfera_oi; ?>" name="refraccion_esfera_oi" id="refraccion_esfera_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                    </tr>
                                    <tr>		
                                        <td align="center">
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $refraccion_cilindro_oi; ?>" name="refraccion_cilindro_oi" id="refraccion_cilindro_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                    </tr>
                                    <tr>	
                                        <td align="center">
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $refraccion_eje_oi; ?>" name="refraccion_eje_oi" id="refraccion_eje_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center">
                                            <?php
												$combo->getComboDb('refraccion_lejos_oi', $refraccion_lejos_oi, $valores_av, 'id_detalle, nombre_detalle', ' ', '', $bol_oi, 'width:90px;', '', 'select_hc');	
											?>
                                        </td>
                                    </tr>
                                    <tr>	
                                        <td align="center">
                                            <?php
												$combo->getComboDb('refraccion_cerca_oi', $refraccion_cerca_oi, $valores_av, 'id_detalle, nombre_detalle', ' ', '', $bol_oi, 'width:90px;', '', 'select_hc');	
											?>
                                        </td>
                                    </tr>
                                    <tr>	
                                        <td align="center" valign="top">
                                            Segundo Evaluador<br />
                                            <b>
	                                            <?php
													if ($bol_oi) {
														echo($nombre_usuario_profesional);
													}
												?>
                                            </b>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
		   				<tr>
                            <td align="right" colspan="2">
                                <table border="0" cellpadding="1" cellspacing="0" align="right">
                                    <tr>
                                        <td align="center">
                                            Esfera<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $cicloplejio_esfera_od; ?>" name="cicloplejio_esfera_od" id="cicloplejio_esfera_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                        <td align="center">
                                            Cilindro<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $cicloplejio_cilindro_od; ?>" name="cicloplejio_cilindro_od" id="cicloplejio_cilindro_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                        <td align="center">
                                            Eje<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $cicloplejio_eje_od; ?>" name="cicloplejio_eje_od" id="cicloplejio_eje_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" colspan="3">
                                            Lejos<br />
                                            <?php
												$combo->getComboDb('cicloplejio_avcc_lejos_od', $cicloplejio_avcc_lejos_od, $valores_av, 'id_detalle, nombre_detalle', ' ', '', $bol_od, 'width:90px;', '', 'select_hc');	
                                            ?>
                                        </td>
                                    </tr>	
                                </table>
                            </td>
                            <td align="center"><h5>Cicloplejia</h5></td>
                            <td align="left" colspan="2">
                               <table border="0" cellpadding="3" cellspacing="0" align="left">
                                    <tr>
                                        <td align="center">
                                            Esfera<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $cicloplejio_esfera_oi; ?>" name="cicloplejio_esfera_oi" id="cicloplejio_esfera_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                        <td align="center">
                                            Cilindro<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $cicloplejio_cilindro_oi; ?>" name="cicloplejio_cilindro_oi" id="cicloplejio_cilindro_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                        <td align="center">
                                            Eje<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $cicloplejio_eje_oi; ?>" name="cicloplejio_eje_oi" id="cicloplejio_eje_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" colspan="3">
                                            Lejos<br />
                                            <?php
												$combo->getComboDb('cicloplejio_avcc_lejos_oi', $cicloplejio_avcc_lejos_oi, $valores_av, 'id_detalle, nombre_detalle', ' ', '', $bol_oi, 'width:90px;', '', 'select_hc');	
                                            ?>
                                        </td>
                                    </tr>	
                                </table>
                            </td>
                        </tr>
                        <tr class="opt_panel_1">
                            <td align="right" colspan="2">
                                <table border="0" cellpadding="1" cellspacing="0" align="right">
                                    <tr>
                                        <td align="center" colspan="3">
                                            Valor Deseado OD<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $refractivo_deseado_od; ?>" name="refractivo_deseado_od" id="refractivo_deseado_od" maxlength="10" onkeypress="formato_hc(event, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                    </tr>
                                    <tr>	
                                        <td align="center">
                                            Esfera<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $refractivo_esfera_od; ?>" name="refractivo_esfera_od" id="refractivo_esfera_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                        <td align="center">
                                            Cilindro<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $refractivo_cilindro_od; ?>" name="refractivo_cilindro_od" id="refractivo_cilindro_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                        <td align="center">
                                            Eje<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $refractivo_eje_od; ?>" name="refractivo_eje_od" id="refractivo_eje_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                    </tr>	
                                </table>
                            </td>
                            <td align="center"><h5>Defecto Refractivo Programado</h5></td>
                            <td align="left" colspan="2">
                                <table border="0" cellpadding="1" cellspacing="0" align="left">
                                    <tr>
                                        <td align="center" colspan="3">
                                            Valor Deseado OI<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $refractivo_deseado_oi; ?>" name="refractivo_deseado_oi" id="refractivo_deseado_oi" maxlength="10" onkeypress="formato_hc(event, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center">
                                            Esfera<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $refractivo_esfera_oi; ?>" name="refractivo_esfera_oi" id="refractivo_esfera_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                        <td align="center">
                                            Cilindro<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $refractivo_cilindro_oi; ?>" name="refractivo_cilindro_oi" id="refractivo_cilindro_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                        <td align="center">
                                            Eje<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $refractivo_eje_oi; ?>" name="refractivo_eje_oi" id="refractivo_eje_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                    </tr>	
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" colspan="5">
                                Equipo:&nbsp; 
                                <?php
									$combo->getComboDb('nomograma_equipo', $nomograma_equipo, $equipos_nomograma, 'id_detalle, nombre_detalle', ' ', '', '', 'width:150px;', '', 'select_hc');
								?>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" colspan="2">
                                <table border="0" cellpadding="1" cellspacing="0" align="right">
                                    <tr>
                                        <td align="center">
                                            Esfera<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $nomograma_esfera_od; ?>" name="nomograma_esfera_od" id="nomograma_esfera_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                        <td align="center">
                                            Cilindro<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $nomograma_cilindro_od; ?>" name="nomograma_cilindro_od" id="nomograma_cilindro_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                        <td align="center">
                                            Eje<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $nomograma_eje_od; ?>" name="nomograma_eje_od" id="nomograma_eje_od" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                    </tr>	
                                </table>
                            </td>
                            <td align="center"><h5>Nomograma</h5></td>
                            <td align="left" colspan="2">
                                <table border="0" cellpadding="1" cellspacing="0" align="left">
                                    <tr>
                                        <td align="center">
                                            Esfera<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $nomograma_esfera_oi; ?>" name="nomograma_esfera_oi" id="nomograma_esfera_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                        <td align="center">
                                            Cilindro<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $nomograma_cilindro_oi; ?>" name="nomograma_cilindro_oi" id="nomograma_cilindro_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                        <td align="center">
                                            Eje<br />
                                            <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $nomograma_eje_oi; ?>" name="nomograma_eje_oi" id="nomograma_eje_oi" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                        </td>
                                    </tr>	
                                </table>
                            </td>
                        </tr>
                        <tr style="height:20px;"></tr>
                        <tr class="opt_panel_1">
                            <td align="center" colspan="5">
                                <b>Existe Patolog&iacute;a Ocular&nbsp;</b>
                                <?php
									$combo->getComboDb('patologia_ocular_valor', $patologia_ocular_valor, $valores_sino, 'id_detalle, nombre_detalle', ' ', '', '', 'width:90px;', '', 'select_hc');
								?>
                                <br /><br />
								<?php
                                    $patologia_ocular_descripcion = $utilidades->ajustar_texto_wysiwyg($patologia_ocular_descripcion);
                                ?>
                                <div id="patologia_ocular_descripcion"><?php echo($patologia_ocular_descripcion); ?></div>
                            </td >
                        </tr>
                        <tr style="height:10px;"></tr>
                        <tr>
                            <td align="center" colspan="5">
                                <b>Existe Cirug&iacute;a Ocular Previa&nbsp;</b>
                                <?php
									$combo->getComboDb('cirugia_ocular_valor', $cirugia_ocular_valor, $valores_sino, 'id_detalle, nombre_detalle', ' ', '', '', 'width:90px;', '', 'select_hc');	
								?>
                                <br /><br />
								<?php
                                    $cirugia_ocular_descripcion = $utilidades->ajustar_texto_wysiwyg($cirugia_ocular_descripcion);
                                ?>
                                <div id="cirugia_ocular_descripcion"><?php echo($cirugia_ocular_descripcion); ?></div>
                            </td>
                        </tr>
                        <tr class="opt_panel_1">
                            <td align="right" colspan="2">
                                <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $paquimetria_central_od; ?>" name="paquimetria_central_od" id="paquimetria_central_od" maxlength="10" onkeypress="formato_hc(event, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                            </td>
                            <td align="center"><h5>Paquimetr&iacute;a Central</h5></td>
                            <td align="left" colspan="2">
                                <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $paquimetria_central_oi; ?>" name="paquimetria_central_oi" id="paquimetria_central_oi" maxlength="10" onkeypress="formato_hc(event, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                            </td>
                        </tr>
                        <tr class="opt_panel_1">
                            <td align="right" colspan="2">
                                <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $paquimetria_periferica_od; ?>" name="paquimetria_periferica_od" id="paquimetria_periferica_od" maxlength="10" onkeypress="formato_hc(event, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                            </td>
                            <td align="center"><h5>Paquimetr&iacute;a Perif&eacute;rica</h5></td>
                            <td align="left" colspan="2">
                                <input type="text" class="input input_hc" style="width:80px;" value="<?php echo $paquimetria_periferica_oi; ?>" name="paquimetria_periferica_oi" id="paquimetria_periferica_oi" maxlength="10" onkeypress="formato_hc(event, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                            </td>
			   			</tr>
                        <tr>
                            <td align="left" colspan="5">
                                <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:100%;">	
                                    <tr>
                                        <td align="center" style="width:30%;">
                                            <h6>Diagn&oacute;sticos</h6>
                                            <?php
												$class_diagnosticos->getFormularioDiagnosticos($id_hc_consulta);
                                            ?>
                                            <label><b>Otros diagn&oacute;sticos y an&aacute;lisis</b></label>
											<?php
                                                $diagnostico_preqx_laser = $utilidades->ajustar_texto_wysiwyg($diagnostico_preqx_laser);
                                            ?>
                                            <div id="diagnostico_preqx_laser"><?php echo($diagnostico_preqx_laser); ?></div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
					</table>
                    <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:90%;">
                        <tr valign="top">
                            <td colspan='3'>
                                <?php
									if (!isset($_POST['tipo_entrada'])) {
								?>
                                <input class="btnPrincipal" type="button" id="btn_imprimir" nombre="btn_imprimir" value="Imprimir" onclick="crear_preqx_laser(2, 1);" />
                                <?php
									} else {
								?>
                                <input class="btnPrincipal" type="button" id="btn_imprimir" nombre="btn_imprimir" value="Imprimir" onclick="imprimir_preqx_laser();" />
                                <?php
									}
									
									if ($ind_editar == 1) {
										if (!isset($_POST['tipo_entrada'])) {
								?>
                                <input type="button" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Guardar cambios" onclick="crear_preqx_laser(2, 0);"/>
								<?php
                                    $id_tipo_cita = $admision_obj["id_tipo_cita"];
                                    $lista_tipos_citas_det_remisiones = $dbTiposCitasDetalle->get_lista_tipos_citas_det_remisiones($id_tipo_cita, $id_tipo_reg);
                                    
                                    if (count($lista_tipos_citas_det_remisiones) > 0) {
                                ?>
                                <input class="btnPrincipal" type="button" id="btn_enviar_a" nombre="btn_enviar_a" value="Enviar a ..." onclick="enviar_a_estados();" />
                                <?php
                                    }
                                ?>
                                <input type="button" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Finalizar consulta" onclick="crear_preqx_laser(1, 0);"/>
                                <?php
										} else {
								?>
                                <input type="button" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Guardar" onclick="crear_preqx_laser(3, 0);"/>
                                <?php
										}	
									}
								?>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
        <?php
			} else {
		?>
        <div class="contenedor_principal" id="id_contenedor_principal">
            <div class="contenedor_error" style="display:block;">Error al ingresar a la consulta prequir&uacute;rgica l&aacute;ser</div>
        </div>
        <?php
			}
		?>        	
		<script type='text/javascript' src='../js/foundation.min.js'></script>
        <script>
            $(document).foundation();
			
			initCKEditorPreQx("patologia_ocular_descripcion");
			initCKEditorPreQx("cirugia_ocular_descripcion");
			initCKEditorPreQx("diagnostico_preqx_laser");
        </script>
        <?php
			if (!isset($_POST['tipo_entrada'])) {
				$contenido->ver_historia($id_paciente);
				$contenido->footer();
			} else {
				$contenido->footer_iframe();
			}
		?>	
    </body>
</html>
