<?php session_start();
	/*
	 * Pagina para crear consulta de evolución 
	 * Autor: Feisar Moreno - 14/02/2014
	 */
	require_once("../db/DbVariables.php");
	require_once("../db/DbConsultaPreqxCatarata.php");
	require_once("../db/DbAdmision.php");
	require_once("../db/DbPacientes.php");
	require_once("../db/DbTiposCitas.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbDiagnosticos.php");
	require_once("../db/DbTiposCitasDetalle.php");
	require_once("../db/DbPlanes.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/Class_Diagnosticos.php");
	require_once("../funciones/Class_Formulacion.php");
	require_once("../funciones/Class_Espera_Dilatacion.php");
	require_once("../funciones/Class_Solic_Procs.php");
	require_once("../funciones/Utilidades.php");
	require_once("FuncionesHistoriaClinica.php");
	
	$dbVariables = new Dbvariables();
	$dbConsultaPreqxCatarata = new DbConsultaPreqxCatarata();
	$dbAdmision = new DbAdmision();
	$dbPacientes = new DbPacientes();
	$dbTiposCitas = new DbTiposCitas();
	$dbListas = new DbListas();
	$dbUsuarios = new DbUsuarios();
	$dbDiagnosticos = new DbDiagnosticos();
	$dbTiposCitasDetalle = new DbTiposCitasDetalle();
	$dbPlanes = new DbPlanes();
	
	$contenido = new ContenidoHtml();
	$combo = new Combo_Box();
	$class_diagnosticos = new Class_Diagnosticos();
	$class_formulacion = new Class_Formulacion();
	$class_espera_dilatacion = new Class_Espera_Dilatacion();
	$class_solic_procs = new Class_Solic_Procs();
	
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
    <title><?php echo $titulo["valor_variable"]; ?></title>
    <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
    <link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
    <link href="../css/azul.css" rel="stylesheet" type="text/css" />
    <link href="../css/Class_Color_Pick.css" rel="stylesheet" type="text/css" />
    
    <script type="text/javascript" src="../js/jquery_autocompletar.js"></script>
    <script type="text/javascript" src="../js/jquery-ui.js"></script>
    
    <!--Para color picker-->
    <script type="text/javascript" src="../js/jquery.colorPicker.js"/></script>
    
    <!--Para validar-->
    <script type="text/javascript" src="../js/jquery.validate.js"></script>
    <script type="text/javascript" src="../js/jquery.validate.add.js"></script>
    <script type="text/javascript" src="../js/ajax.js"></script>
    <script type="text/javascript" src="../js/funciones.js"></script>
    <script type="text/javascript" src="../js/validaFecha.js"></script>
    <script type="text/javascript" src="../js/Class_Diagnosticos_v1.2.js"></script>
    <script type="text/javascript" src="../js/Class_Formulas_Medicas.js"></script>
    <script type="text/javascript" src="../js/Class_Atencion_Remision_v1.3.js"></script>
    <script type="text/javascript" src="../js/Class_Formulacion_v1.5.js"></script>
    <script type="text/javascript" src="../funciones/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="../funciones/ckeditor/config.js"></script>
    <script type="text/javascript" src="../js/Class_Color_Pick.js"></script>
    <script type="text/javascript" src="../js/Class_Espera_Dilatacion.js"></script>
    <script type="text/javascript" src="../js/Class_Solic_Procs.js"></script>
    <script type="text/javascript" src="historia_clinica_v1.1.js"></script>
    <script type="text/javascript" src="FuncionesHistoriaClinica.js"></script>
    <script type="text/javascript" src="consulta_preqx_catarata_v1.10.js"></script>
	<?php
		$tabla_diagnosticos = $dbDiagnosticos->getDiagnosticoCiexTotal();
		$i = 0;
		$cadena_diagnosticos = "";
		foreach($tabla_diagnosticos as $fila_diagnosticos){
			$cod_ciex = $fila_diagnosticos["codciex"];
			$nom_ciex = $fila_diagnosticos["nombre"];
			
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
    <script type="text/javascript">
		$(function() {
			$("#color_queratometria_oi").colorPicker({showHexField: false});
			$("#color_queratometria_od").colorPicker({showHexField: false});
		});
	</script>
</head>
<body>
	<?php
		$contenido->validar_seguridad(0);
		if (!isset($_POST["tipo_entrada"])) {
	    	$contenido->cabecera_html();	
    	}
		$id_tipo_reg = 5;
		$id_usuario = $_SESSION["idUsuario"];
		
		if (isset($_POST["hdd_id_paciente"])) {
			$id_paciente = $utilidades->str_decode($_POST["hdd_id_paciente"]);
			$nombre_paciente = $utilidades->str_decode($_POST["hdd_nombre_paciente"]);
			$id_admision = $utilidades->str_decode($_POST["hdd_id_admision"]);
			
			//Se obtienen los datos de la admision
			$admision_obj = $dbAdmision->get_admision($id_admision);
			
			//Se obtienen los datos del tipo de cita
			$tipo_cita_obj = $dbTiposCitas->get_tipo_cita($admision_obj["id_tipo_cita"]);
			
			if (!isset($_POST["tipo_entrada"])) {
				$tabla_hc = $dbConsultaPreqxCatarata->getHistoriaClinicaAdmision($id_admision, $id_tipo_reg);
	    	} else {
				$id_hc = $_POST["hdd_id_hc"];
				$tabla_hc = $dbConsultaPreqxCatarata->getHistoriaClinicaId($id_hc);
			}
			
			if (count($tabla_hc) > 0) { //Si se encuentra una HC para este paciente se carga los datos de lo contrario se crea la HC
				$tipo_accion="2"; //Editar consulta de evolución
				$id_hc_consulta = $tabla_hc["id_hc"];
				$nombre_usuario_alt = $tabla_hc["nombre_usuario_alt"];
				
				//se obtiene el registro de la consulta de evolución a partir del ID de la Historia Clinica 
				$tabla_consulta = $dbConsultaPreqxCatarata->get_consulta_preqx_catarata($id_hc_consulta);
				
				//Se verifica si se debe actualizar el estado de la admisión asociada
				$en_atencion = "0";
				if (isset($_POST["hdd_en_atencion"])) {
					$en_atencion = $_POST["hdd_en_atencion"];
				}
				
				if ($en_atencion == "1") {
					$dbAdmision->editar_admision_estado($id_admision, 6, 1, $id_usuario);
				}
			} else { //Entre en procesos de crear HC
				$tipo_accion = "1";
				$nombre_usuario_alt = $tabla_hc["nombre_usuario_alt"];
				
				//Se crea la historia clinica y se inicia la consulta
				$id_hc_consulta = $dbConsultaPreqxCatarata->crear_consulta_preqx_catarata($id_paciente, $id_tipo_reg, $id_usuario, $id_admision);
				$tabla_consulta = array();
				if ($id_hc_consulta < 0) {
					$tipo_accion = "0";
				} else {
					$tabla_consulta = $dbConsultaPreqxCatarata->get_consulta_preqx_catarata($id_hc_consulta);
				}
			}
			
			//Se obtienen los datos del registro de historia clínica
			$historia_clinica_obj = $dbConsultaPreqxCatarata->getHistoriaClinicaId($id_hc_consulta);
		} else {
			$tipo_accion="0";//Ninguna accion Error
		}
		
		//Se inicia el componente de formulación de medicamentos
		$cod_tipo_medicamento = "";
		if (isset($_POST["hdd_id_admision"])) {
			$admision_obj = $dbAdmision->get_admision($_POST["hdd_id_admision"]);
			$plan_obj = $dbPlanes->getPlan($admision_obj["id_plan"]);
			
			$cod_tipo_medicamento = $plan_obj["cod_tipo_medicamento"];
		}
		$class_formulacion->iniciarComponentesFormulacion($cod_tipo_medicamento, $id_hc_consulta);
		
		//Edad del paciente
		$datos_paciente = $dbPacientes->getEdadPaciente($id_paciente, "");
		$edad_paciente = $datos_paciente["edad"];
		$profesion_paciente = $datos_paciente["profesion"];
		
		//Nombre del profesional que atiende la consulta
		$id_usuario_profesional = $tabla_consulta["id_usuario_crea"];
		$usuario_profesional_obj = $dbUsuarios->getUsuario($id_usuario_profesional);
		$nombre_usuario_profesional = $usuario_profesional_obj["nombre_usuario"]." ".$usuario_profesional_obj["apellido_usuario"];
		
		if(!isset($_POST["tipo_entrada"])){
    ?>
    <div class="title-bar title_hc">
        <div class="wrapper">
            <div class="breadcrumb">
                <ul>
                    <li class="breadcrumb_on">Consulta Prequir&uacute;rgica de Catarata</li>
                </ul>
            </div>
        </div>
    </div>
    <?php
		}
		
		if ($tipo_accion > 0) {
			//Para verificaro que tiene permiso de hacer cambio
			$ind_editar = $dbConsultaPreqxCatarata->getIndicadorEdicion($id_hc_consulta, $horas_edicion["valor_variable"]);
			$ind_editar_enc_hc = $ind_editar;
			if ($ind_editar == 1 && isset($_POST["tipo_entrada"])) {
				$ind_editar_enc_hc = 0;
			}
			
			//Se borran las imágenes temporales creadas por el usuario actual
			$ruta_tmp = "../historia_clinica/tmp/".$id_usuario;
			/*if (file_exists($ruta_tmp)) {
				@array_map("unlink", glob($ruta_tmp."/img*.*"));
			}*/
			
			@mkdir($ruta_tmp);
			
			//Se obtiene la ruta actual de las imágenes
			$arr_ruta_base = $dbVariables->getVariable(17);
			$ruta_base = $arr_ruta_base["valor_variable"];
			
			$img_queratometria_od = $tabla_consulta["img_queratometria_od"];
			$img_queratometria_oi = $tabla_consulta["img_queratometria_oi"];
			
			//Se crea una copia local de las imágenes a mostrar
			if ($img_queratometria_od != "") {
				$img_queratometria_od = str_replace("../imagenes/imagenes_hce", $ruta_base, $img_queratometria_od);
				@copy($img_queratometria_od, $ruta_tmp."/img_queratometria_od_".$id_hc_consulta.".png");
				$img_queratometria_od = $ruta_tmp."/img_queratometria_od_".$id_hc_consulta.".png";
			}
			if ($img_queratometria_oi != "") {
				$img_queratometria_oi = str_replace("../imagenes/imagenes_hce", $ruta_base, $img_queratometria_oi);
				@copy($img_queratometria_oi, $ruta_tmp."/img_queratometria_oi_".$id_hc_consulta.".png");
				$img_queratometria_oi = $ruta_tmp."/img_queratometria_oi_".$id_hc_consulta.".png";
			}
			
			$funciones_hc->encabezado_historia_clinica($id_paciente, $id_admision, $id_hc_consulta, $ind_editar_enc_hc, false);
    ?>
    <div class="contenedor_principal" id="id_contenedor_principal">
    	<div id="d_guardar_consulta" style="width: 100%; display: block;">
        	<div class="contenedor_error" id="contenedor_error"></div>
            <div class="contenedor_exito" id="contenedor_exito"></div>
        </div>	
        <div class="formulario" id="principal_consulta" style="width:100%; display:block;">
        	<?php
				//Se inserta el registro de ingreso a la historia clínica
				$dbConsultaPreqxCatarata->crear_ingreso_hc($id_usuario, $id_paciente, $id_admision, $id_hc_consulta, 160);
				
				//Se verifica la información de que ojo se va a solicitar
				$bol_od = false;
				$bol_oi = false;
				switch ($tabla_consulta["ojo"]) {
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
				
		        //Array valores LOCS III No
				$cadena_locs3_no = "'0'";
				for ($i = 0; $i <= 6; $i++) {
					for ($j = 0; $j <= 9; $j++) {
						if ($i != 0 || $j != 0) {
							if ($cadena_locs3_no != "") {
								$cadena_locs3_no .= ",";
							}
							$cadena_locs3_no .= "'".$i.",".$j."'";
						}
					}
				}
				
		        //Array valores LOCS III Nc
				$cadena_locs3_nc = $cadena_locs3_no;
				
		        //Array valores LOCS III SCP
				$cadena_locs3_scp = "'0'";
				for ($i = 0; $i <= 5; $i++) {
					for ($j = 0; $j <= 9; $j++) {
						if ($i != 0 || $j != 0) {
							if ($cadena_locs3_scp != "") {
								$cadena_locs3_scp .= ",";
							}
							$cadena_locs3_scp .= "'".$i.",".$j."'";
						}
					}
				}
				
		        //Array valores LOCS III C
				$cadena_locs3_c = $cadena_locs3_scp;
				$lista_locs3_c = $dbListas->getListaDetalles(22);
				foreach ($lista_locs3_c as $locs3_c_aux) {
					$cadena_locs3_c .= ",'".$locs3_c_aux["nombre_detalle"]."'";
				}
				
				//Array valores -0.1 a -10.0
				$cadena_decimal_neg = "'0'";
				for ($i = 0; $i <= 9; $i++) {
					for ($j = 0; $j <= 99; $j++) {
						if ($i != 0 || $j != 0) {
							if ($cadena_decimal_neg != "") {
								$cadena_decimal_neg .= ",";
							}
							$dec_aux = "".$j;
							if ($j < 10) {
								$dec_aux = "0".$j;
							}
							$cadena_decimal_neg .= "'-".$i.",".$dec_aux."'";
						}
					}
				}
				$cadena_decimal_neg .= ",'-10,0','NP/NA'";
				
				//Array valores eje
				$cadena_eje = "'0'";
				for ($i = 1; $i <= 180; $i++) {
					if ($cadena_eje != "") {
						$cadena_eje .= ",";
					}
					$cadena_eje .= "'".$i."'";
				}
				$cadena_eje .= ",'NP/NA'";
				
				//Array Si/No
				$arr_sino = array();
				$arr_sino[0]["id"] = "1";
				$arr_sino[0]["valor"] = "Si";
				$arr_sino[1]["id"] = "0";
				$arr_sino[1]["valor"] = "No";
	        ?>
	        <script type="text/javascript">
				//Se verifica la información de que ojo se va a solicitar
				var bol_od = false;
				var bol_oi = false;
				<?php
					switch ($tabla_consulta["ojo"]) {
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
				
    	        var array_locs3_no = [<?php echo($cadena_locs3_no) ?>];
    	        var array_locs3_nc = [<?php echo($cadena_locs3_nc) ?>];
    	        var array_locs3_scp = [<?php echo($cadena_locs3_scp) ?>];
    	        var array_locs3_c = [<?php echo($cadena_locs3_c) ?>];
    	        var array_decimal_neg = [<?php echo($cadena_decimal_neg) ?>];
    	        var array_eje = [<?php echo($cadena_eje) ?>];
				var array_npna = ["NP/NA"];
	        	$(function() {
					<?php
						switch ($tabla_consulta["id_locs3"]) {
							case "111": //No
					?>
					$("#txt_val_locs3").autocomplete({ source: array_locs3_no });
					<?php
								break;
								
							case "112": //Nc
					?>
					$("#txt_val_locs3").autocomplete({ source: array_locs3_nc });
					<?php
								break;
								
							case "113": //C
					?>
					$("#txt_val_locs3").autocomplete({ source: array_locs3_c });
					<?php
								break;
								
							case "114": //SCP
					?>
					$("#txt_val_locs3").autocomplete({ source: array_locs3_scp });
					<?php
								break;
						}
					?>
					$("#txt_q_val_biometria_od").autocomplete({ source: array_decimal_neg });
					$("#txt_q_val_biometria_oi").autocomplete({ source: array_decimal_neg });
					$("#txt_q_val_iol_master_od").autocomplete({ source: array_decimal_neg });
					$("#txt_q_val_iol_master_oi").autocomplete({ source: array_decimal_neg });
					$("#txt_q_val_topografia_od").autocomplete({ source: array_decimal_neg });
					$("#txt_q_val_topografia_oi").autocomplete({ source: array_decimal_neg });
					$("#txt_q_val_definitiva_od").autocomplete({ source: array_decimal_neg });
					$("#txt_q_val_definitiva_oi").autocomplete({ source: array_decimal_neg });
					$("#txt_q_eje_biometria_od").autocomplete({ source: array_npna });
					$("#txt_q_eje_biometria_oi").autocomplete({ source: array_npna });
					$("#txt_q_eje_iol_master_od").autocomplete({ source: array_eje });
					$("#txt_q_eje_iol_master_oi").autocomplete({ source: array_eje });
					$("#txt_q_eje_topografia_od").autocomplete({ source: array_eje });
					$("#txt_q_eje_topografia_oi").autocomplete({ source: array_eje });
					$("#txt_q_eje_definitiva_od").autocomplete({ source: array_eje });
					$("#txt_q_eje_definitiva_oi").autocomplete({ source: array_eje });
				});
			</script>
            <form id="frm_consulta" name="frm_consulta" method="post">
	            <input type="hidden" name="hdd_id_hc_consulta" id="hdd_id_hc_consulta" value="<?php echo($id_hc_consulta); ?>" />
    	        <input type="hidden" name="hdd_id_admision" id="hdd_id_admision" value="<?php echo($id_admision); ?>" />
    	        <input type="hidden" name="hdd_id_paciente" id="hdd_id_paciente" value="<?php echo($id_paciente); ?>" />
    	        <input type="hidden" name="hdd_id_convenio" id="hdd_id_convenio" value="<?php echo($admision_obj["id_convenio"]); ?>" />
    	        <input type="hidden" name="hdd_id_plan" id="hdd_id_plan" value="<?php echo($admision_obj["id_plan"]); ?>" />
        	    <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
            		<tr valign="middle">
		                <td align="center" colspan="2">
        		            <div class="contenedor_error" id="contenedor_error"></div>
                		    <div class="contenedor_exito" id="contenedor_exito"></div>
		                </td>
        		    </tr>
                    <tr>
						<th align="left" colspan="2">
                            <h6 style="margin: 1px;">
                                <input type="hidden" id="hdd_usuario_anonimo" value="<?php echo($usuario_profesional_obj["ind_anonimo"]); ?>" />
                                <b>Profesional que atiende: </b>
                                <?php
									if ($usuario_profesional_obj["ind_anonimo"] == "0") {
								?>
                                <input type="hidden" id="txt_nombre_usuario_alt" value="" />
                                <?php
										echo($nombre_usuario_profesional);
									} else {
								?>
                                <input type="text" id="txt_nombre_usuario_alt" maxlength="100" value="<?php echo($nombre_usuario_alt); ?>" style="width:60%; display:inline;" onblur="trim_cadena(this);" />
                                <?php
									}
								?>
                            </h6>
                        </th>
                    </tr>
                    <tr>
                    	<th align="left" style="width:90%;">
                        	<h6 style="margin: 1px;">
                            	<b>Cirug&iacute;a:</b> <?php echo($tabla_consulta["nombre_cirugia"]); ?>
                                <br />
                                <b>Fecha de la cirug&iacute;a:</b> <?php echo($tabla_consulta["fecha_cirugia_t"]); ?>
                            </h6>
                        </th>
                        <th align="left" style="width:10%;">
                        	<h6 style="margin: 1px;">
                            	<b>Ojo:</b> <?php echo($tabla_consulta["ojo"]); ?>
                                <br />
                                <?php echo($tabla_consulta["num_cirugia"]); ?>a cirug&iacute;a
                            </h6>
                        </th>
                    </tr>
                </table>
                <br />
                
                <table border="0" cellpadding="0" cellspacing="0" align="center" style="width:98%;">
                    <tr>
                        <td align="center" colspan="3">
                            <table border="0" cellpadding="1" style="width:100%;">
                                <tr>
                                    <td align="right" style="width:20%;">
			                            <label>Clasificaci&oacute;n LOCS III *</label>
                                    </td>
                                    <td align="left" style="width:30%;">
                                        <?php
											//Se obtiene la lista de LOCS III
											$lista_locs3 = $dbListas->getListaDetalles(17);
											$combo->getComboDb("cmb_locs3", $tabla_consulta["id_locs3"], $lista_locs3, "id_detalle, nombre_detalle", " ", "cambiar_lista_locs3(this.value);", "", "", "", "select_hc");
										?>
                                    </td>
                                    <td align="right" style="width:17%;">
			                            <label>Valor *</label>
                                    </td>
                                    <td align="left" style="width:33%;">
			                            <input type="text" name="txt_val_locs3" id="txt_val_locs3" maxlength="20" value="<?php echo($tabla_consulta["val_locs3"]); ?>" style="width:100px;" class="input input_hc" onblur="validar_array_locs3(this);" />
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
			                            <label>Recuento Endotelial (Cel/mm&sup2;) *</label>
                                    </td>
                                    <td align="left">
			                            <input type="text" name="txt_val_rec_endotelial" id="txt_val_rec_endotelial" maxlength="4" value="<?php echo($tabla_consulta["val_rec_endotelial"]); ?>" style="width:100px;" class="input input_hc" onkeypress="return solo_numeros(event, false);" />
                                    </td>
                                    <td align="right">
			                            <label>Paquimetr&iacute;a (um) *</label>
                                    </td>
                                    <td align="left">
			                            <input type="text" name="txt_val_paquimetria" id="txt_val_paquimetria" maxlength="4" value="<?php echo($tabla_consulta["val_paquimetria"]); ?>" style="width:100px;" class="input input_hc" onkeypress="return solo_numeros(event, false);" />
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
			                            <label>Plegables *</label>
                                    </td>
                                    <td align="left">
                                        <?php
											//Se obtiene la lista de Plegables
											$lista_plegables = $dbListas->getListaDetalles(18);
											$combo->getComboDb("cmb_plegables", $tabla_consulta["id_plegables"], $lista_plegables, "id_detalle, nombre_detalle", " ", "", "", "width:240px;", "", "select_hc");
										?>
                                    </td>
                                    <td align="right">
			                            <label>R&iacute;gido *</label>
                                    </td>
                                    <td align="left">
                                        <?php
											//Se obtiene la lista de Plegables
											$lista_rigido = $dbListas->getListaDetalles(19);
											$combo->getComboDb("cmb_rigido", $tabla_consulta["id_rigido"], $lista_rigido, "id_detalle, nombre_detalle", " ", "", "", "width:240px;", "", "select_hc");
										?>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
			                            <label>Especiales *</label>
                                    </td>
                                    <td align="left">
                                        <?php
											//Se obtiene la lista de Plegables
											$lista_especiales = $dbListas->getListaDetalles(20);
											$combo->getComboDb("cmb_especiales", $tabla_consulta["id_especiales"], $lista_especiales, "id_detalle, nombre_detalle", " ", "", "", "width:240px;", "", "select_hc");
										?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <table border="0" cellpadding="0" cellspacing="0" align="center" style="width:98%;">
                    <tr>
                        <td align="center">
                            <h5>Evoluci&oacute;n *</h5>
							<?php
								$texto_evolucion = $utilidades->ajustar_texto_wysiwyg($tabla_consulta["texto_evolucion"]);
							?>
                            <div id="txt_evolucion"><?php echo($texto_evolucion); ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <table border="0" cellpadding="1" style="width:100%;">
                                <tr>
			                        <td align="right" style="width:20%;">
            			                <label>Anestesia *</label>
                        			</td>
                                	<td align="left" style="width:80%;">
			                            <?php
											//Se obtiene la lista de anestesias
											$lista_anestesias = $dbListas->getListaDetalles(21);
											$combo->getComboDb("cmb_anestesia", $tabla_consulta["id_anestesia"], $lista_anestesias, "id_detalle, nombre_detalle", " ", "", "", "width:240px;", "", "select_hc");
										?>
            			            </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <table border="0" cellpadding="0" cellspacing="0" align="center" style="width:98%;">
                    <tr>
                        <td>
                            <table border="0" cellpadding="1" style="width:100%;">
			                    <tr>
            			            <td align="center" colspan="6">
                        			    <h5>
                                            Incisi&oacute;n Principal / Arqueada<br />
                                            Queratometr&iacute;a
                                        </h5>
                                    </td>
			                    </tr>
                                <tr>
                                    <td align="center" colspan="6" class="td_tabla">
                                        <div class="odoi_t">
                                            <div class="od_t"><h5 style="margin: 0px;">OD</h5></div>
                                            <div class="oi_t"><h5 style="margin: 0px;">OI</h5></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                        			<td align="center" style="width:22%;"><label><b>Valor</b></label></td>
                        			<td align="center" style="width:22%;"><label><b>Eje</b></label></td>
                        			<td align="center" colspan="2"></td>
                        			<td align="center" style="width:22%;"><label><b>Valor</b></label></td>
                        			<td align="center" style="width:22%;"><label><b>Eje</b></label></td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <input type="text" name="txt_q_val_biometria_od" id="txt_q_val_biometria_od" maxlength="10" value="<?php echo($tabla_consulta["querato_val_biometria_od"]); ?>" style="width:100px;" class="input input_hc" onblur="validar_array(array_decimal_neg, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                    </td>
                                    <td align="center">
                                        <input type="text" name="txt_q_eje_biometria_od" id="txt_q_eje_biometria_od" maxlength="10" value="<?php echo($tabla_consulta["querato_eje_biometria_od"]); ?>" style="width:100px;" class="input input_hc" onblur="validar_array(array_eje, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                    </td>
                                    <td align="center" colspan="2"><label><b>Biometr&iacute;a *</b></label></td>
                                    <td align="center">
                                        <input type="text" name="txt_q_val_biometria_oi" id="txt_q_val_biometria_oi" maxlength="10" value="<?php echo($tabla_consulta["querato_val_biometria_oi"]); ?>" style="width:100px;" class="input input_hc" onblur="validar_array(array_decimal_neg, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                    </td>
                                    <td align="center">
                                        <input type="text" name="txt_q_eje_biometria_oi" id="txt_q_eje_biometria_oi" maxlength="10" value="<?php echo($tabla_consulta["querato_eje_biometria_oi"]); ?>" style="width:100px;" class="input input_hc" onblur="validar_array(array_eje, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <input type="text" name="txt_q_val_iol_master_od" id="txt_q_val_iol_master_od" maxlength="10" value="<?php echo($tabla_consulta["querato_val_iol_master_od"]); ?>" style="width:100px;" class="input input_hc" onblur="validar_array(array_decimal_neg, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                    </td>
                                    <td align="center">
                                        <input type="text" name="txt_q_eje_iol_master_od" id="txt_q_eje_iol_master_od" maxlength="10" value="<?php echo($tabla_consulta["querato_eje_iol_master_od"]); ?>" style="width:100px;" class="input input_hc" onblur="validar_array(array_eje, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                    </td>
                                    <td align="center" colspan="2"><label><b>IOL Master *</b></label></td>
                                    <td align="center">
                                        <input type="text" name="txt_q_val_iol_master_oi" id="txt_q_val_iol_master_oi" maxlength="10" value="<?php echo($tabla_consulta["querato_val_iol_master_oi"]); ?>" style="width:100px;" class="input input_hc" onblur="validar_array(array_decimal_neg, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                    </td>
                                    <td align="center">
                                        <input type="text" name="txt_q_eje_iol_master_oi" id="txt_q_eje_iol_master_oi" maxlength="10" value="<?php echo($tabla_consulta["querato_eje_iol_master_oi"]); ?>" style="width:100px;" class="input input_hc" onblur="validar_array(array_eje, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <input type="text" name="txt_q_val_topografia_od" id="txt_q_val_topografia_od" maxlength="10" value="<?php echo($tabla_consulta["querato_val_topografia_od"]); ?>" style="width:100px;" class="input input_hc" onblur="validar_array(array_decimal_neg, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                    </td>
                                    <td align="center">
                                        <input type="text" name="txt_q_eje_topografia_od" id="txt_q_eje_topografia_od" maxlength="10" value="<?php echo($tabla_consulta["querato_eje_topografia_od"]); ?>" style="width:100px;" class="input input_hc" onblur="validar_array(array_eje, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                    </td>
                                    <td align="center" colspan="2"><label><b>Topograf&iacute;a *</b></label></td>
                                    <td align="center">
                                        <input type="text" name="txt_q_val_topografia_oi" id="txt_q_val_topografia_oi" maxlength="10" value="<?php echo($tabla_consulta["querato_val_topografia_oi"]); ?>" style="width:100px;" class="input input_hc" onblur="validar_array(array_decimal_neg, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                    </td>
                                    <td align="center">
                                        <input type="text" name="txt_q_eje_topografia_oi" id="txt_q_eje_topografia_oi" maxlength="10" value="<?php echo($tabla_consulta["querato_eje_topografia_oi"]); ?>" style="width:100px;" class="input input_hc" onblur="validar_array(array_eje, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <input type="text" name="txt_q_val_definitiva_od" id="txt_q_val_definitiva_od" maxlength="10" value="<?php echo($tabla_consulta["querato_val_definitiva_od"]); ?>" style="width:100px;" class="input input_hc" onblur="validar_array(array_decimal_neg, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                    </td>
                                    <td align="center">
                                        <input type="text" name="txt_q_eje_definitiva_od" id="txt_q_eje_definitiva_od" maxlength="10" value="<?php echo($tabla_consulta["querato_eje_definitiva_od"]); ?>" style="width:100px;" class="input input_hc" onblur="validar_array(array_eje, this);"<?php if (!$bol_od) {?> disabled="disabled"<?php } ?> />
                                    </td>
                                    <td align="center" colspan="2"><label><b>Definitiva *</b></label></td>
                                    <td align="center">
                                        <input type="text" name="txt_q_val_definitiva_oi" id="txt_q_val_definitiva_oi" maxlength="10" value="<?php echo($tabla_consulta["querato_val_definitiva_oi"]); ?>" style="width:100px;" class="input input_hc" onblur="validar_array(array_decimal_neg, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                    </td>
                                    <td align="center">
                                        <input type="text" name="txt_q_eje_definitiva_oi" id="txt_q_eje_definitiva_oi" maxlength="10" value="<?php echo($tabla_consulta["querato_eje_definitiva_oi"]); ?>" style="width:100px;" class="input input_hc" onblur="validar_array(array_eje, this);"<?php if (!$bol_oi) {?> disabled="disabled"<?php } ?> />
                                    </td>
                                </tr>
                                <tr>
                                	<td colspan="6">
                                    	<table width="100%">
                                        	<tr>
                                            	<td style="width:50%;">
                                                	<?php
                                                    	$params = "&id_paciente=".$id_paciente.
																  "&id_imagen=".$id_hc_consulta."_queratometriaod".
																  "&nombre_imagen=".$img_queratometria_od.
																  "&nombre_imagen_base=../imagenes/queratometria_img.png".
																  "&ancho_img=400".
																  "&alto_img=280";
													?>
                                                    <input type="hidden" name="hdd_img_queratometria_od" id="hdd_img_queratometria_od" value="<?php echo($tabla_consulta["img_queratometria_od"]); ?>" />
                                                    <iframe id="ifr_img_queratometria_od" width="100%" height="355" style="border-width:0;" src="../funciones/wPaint/Class_Pintar.php?<?php echo($params); ?>"></iframe>
                                                </td>
                                            	<td style="width:50%;">
                                                	<?php
                                                    	$params = "&id_paciente=".$id_paciente.
																  "&id_imagen=".$id_hc_consulta."_queratometriaoi".
																  "&nombre_imagen=".$img_queratometria_oi.
																  "&nombre_imagen_base=../imagenes/queratometria_img.png".
																  "&ancho_img=400".
																  "&alto_img=280";
													?>
                                                    <input type="hidden" name="hdd_img_queratometria_oi" id="hdd_img_queratometria_oi" value="<?php echo($tabla_consulta["img_queratometria_oi"]); ?>" />
                                                    <iframe id="ifr_img_queratometria_oi" width="100%" height="355" style="border-width:0;" src="../funciones/wPaint/Class_Pintar.php?<?php echo($params); ?>"></iframe>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" colspan="3">
                                        <label>
	                                        MERIDIANO M&Aacute;S PLANO:<br />
    	                                    MERIDIANO M&Aacute;S CURVO:<br />
        	                                INCISI&Oacute;N PRINCIPAL:
                                        </label>
                                    </td>
                                    <td align="left" colspan="3">
                                        <label>
	                                        L&iacute;nea cont&iacute;nua<br />
    	                                    Incisiones<br />
        	                                L&iacute;nea roja
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" colspan="2">
                                        <label>Incisi&oacute;n Arqueada *</label>
                                    </td>
                                    <td align="left">
                                        <?php
											//Se obtiene la lista de anestesias
											$combo->getComboDb("cmb_incision_arq", $tabla_consulta["ind_incision_arq"], $arr_sino, "id, valor", " ", "seleccionar_incision_arq(this.value);", "", "", "", "select_hc");
										?>
                                    </td>
                                    <td align="right">
                                        <label>Valor</label>
                                    </td>
                                    <td align="left">
                                        <?php
											$disabled_aux = "";
											if ($tabla_consulta["ind_incision_arq"] != "1") {
												$disabled_aux = " disabled=\"disabled\"";
											}
										?>
                                        <input type="text" name="txt_val_incision_arq" id="txt_val_incision_arq" maxlength="4" value="<?php echo($tabla_consulta["val_incision_arq"]); ?>" style="width:100px;" class="input input_hc" onkeypress="return solo_numeros(event, false);"<?php echo($disabled_aux); ?> />
                                    </td>
                                </tr>
            			    </table>
                        </td>
                    </tr>
                </table>
              <table border="0" cellpadding="0" cellspacing="0" align="center" style="width:98%;">
                    <tr>
                        <td align="center">
                            <h5>Observaciones</h5>
							<?php
                                $observaciones_preqx = $utilidades->ajustar_texto_wysiwyg($tabla_consulta["observaciones_preqx"]);
                            ?>
                            <div id="txt_observaciones_preqx"><?php echo($observaciones_preqx); ?></div>
                        </td>
                    </tr>
                    <?php
						if (!isset($_POST["tipo_entrada"])) {
					?>
                    <tr style="height:10px;"></tr>
                    <tr>
                        <td align="center" colspan="3">
                            <?php
								//Se carga el componente indicador de espera por dilatación de pupila
								$class_espera_dilatacion->getEsperaDilatacion($id_admision, $admision_obj["id_tipo_espera"] != "" ? 1 : 0);
							?>
                        </td>
                    </tr>
                    <?php
						}
					?>
                </table>
                <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
                    <tr>
                        <td align="center" colspan="3">
                        	<h6>Diagn&oacute;sticos</h6>
                            <?php
								$class_diagnosticos->getFormularioDiagnosticos($id_hc_consulta);
							?>
                            <label><b>Otros diagn&oacute;sticos y an&aacute;lisis</b></label>
							<?php
                                $diagnostico_preqx_catarata = $utilidades->ajustar_texto_wysiwyg($tabla_consulta["diagnostico_preqx_catarata"]);
                            ?>
                            <div id="diagnostico_preqx_catarata"><?php echo($diagnostico_preqx_catarata); ?></div>
                        </td>
                    </tr>
                    <tr>
						<td align="center" style="width:30%;">
                            <label><b>Solicitud de Procedimientos y Ex&aacute;menes Complemetarios</b></label>
							<?php
								$class_solic_procs->getFormularioSolicitud($id_hc_consulta);
								
                                $solicitud_examenes_preqx_catarata = $utilidades->ajustar_texto_wysiwyg($tabla_consulta["solicitud_examenes_preqx_catarata"]);
                            ?>
                            <div id="solicitud_examenes_preqx_catarata"><?php echo($solicitud_examenes_preqx_catarata); ?></div>
						</td>
					</tr>
					<tr>	
						<td align="center" style="width:30%;">
                        	<label><b>Recomendaciones Cl&iacute;nicas, M&eacute;dicas, Optom&eacute;tricas y Quir&uacute;rgicas&nbsp;</b></label>
							<?php
                                $tratamiento_preqx_catarata = $utilidades->ajustar_texto_wysiwyg($tabla_consulta["tratamiento_preqx_catarata"]);
                            ?>
                            <div id="tratamiento_preqx_catarata"><?php echo($tratamiento_preqx_catarata); ?></div>
						</td>
					</tr>
                    <tr><td><div class="div_separador"></div></td></tr>
                    <tr>
                        <td align="center" colspan="2">
                            <label style="display:inline;"><b>Formulaci&oacute;n de Medicamentos</b></label>
                            <?php
                               	$class_formulacion->getFormularioFormulacion($id_hc_consulta);
							?>
                        </td>
                    </tr>
					<tr style="display:none;">	
						<td align="center" style="width:30%;">
                            <label><b>F&oacute;rmula M&eacute;dica</b></label>
							<textarea style="text-align: justify;" class="textarea_oftalmo" id="medicamentos_preqx_catarata" nombre="medicamentos_preqx_catarata" onblur="trim_cadena(this);" tabindex="1" ><?php echo($tabla_consulta["medicamentos_preqx_catarata"]);?></textarea>
						</td>
					</tr>
                </table>
                <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:90%;">
                	<tr valign="top">
                    	<td colspan="3">
							<?php
								if (!isset($_POST["tipo_entrada"])) {
							?>
							<input class="btnPrincipal" type="button" id="btn_imprimir" nombre="btn_imprimir" value="Imprimir" onclick="guardar_consulta(2, 1);" />
							<?php
								} else {
							?>
							<input class="btnPrincipal" type="button" id="btn_imprimir" nombre="btn_imprimir" value="Imprimir" onclick="imprimir_consulta();" />
							<?php
								}
								
                                if ($ind_editar == 1) {
                                    if (!isset($_POST["tipo_entrada"])) {
                            ?>
                        	<input type="button" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Guardar cambios" onclick="guardar_consulta(2, 0);" />
                            <?php
										$id_tipo_cita = $admision_obj["id_tipo_cita"];
										$lista_tipos_citas_det_remisiones = $dbTiposCitasDetalle->get_lista_tipos_citas_det_remisiones($id_tipo_cita, $id_tipo_reg);
										
										if (count($lista_tipos_citas_det_remisiones) > 0) {
							?>
							<input class="btnPrincipal" type="button" id="btn_enviar_a" nombre="btn_enviar_a" value="Enviar a ..." onclick="enviar_a_estados();" />
							<?php
										}
                            ?>
                            <input type="button" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Finalizar consulta" onclick="guardar_consulta(1, 0);" />
							<?php
                                    } else {
                            ?>
                        	<input type="button" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Guardar" onclick="guardar_consulta(3, 0);" />
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
	    <div class="contenedor_error" style="display:block;">Error al ingresar a la consulta prequir&uacute;rgica de catarata</div>
    </div>
    <?php
		}
	?>
    <script type="text/javascript" src="../js/foundation.min.js"></script>
    <script>
		$(document).foundation();
		
		initCKEditorPreQx("txt_evolucion");
		initCKEditorPreQx("txt_observaciones_preqx");
		initCKEditorPreQx("diagnostico_preqx_catarata");
		initCKEditorPreQx("solicitud_examenes_preqx_catarata");
		initCKEditorPreQx("tratamiento_preqx_catarata");
    </script>
    <?php
	   if (!isset($_POST["tipo_entrada"])) {
			$contenido->ver_historia($id_paciente);
	    	$contenido->footer();
	   } else {
			$contenido->footer_iframe();
	   }
	?>
</body>
</html>
