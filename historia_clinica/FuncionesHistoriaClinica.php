<?php
	class FuncionesHistoriaClinica {
		public function encabezado_historia_clinica($id_paciente, $id_admision, $id_hc = "", $ind_editar = 0, $ind_actualizado = false) {
			require_once("../db/DbAdmision.php");
			require_once("../db/DbPacientes.php");
			require_once("../db/DbPagos.php");
			require_once("../db/DbHistoriaClinica.php");
			require_once("../db/DbListas.php");
			require_once("../db/DbPaises.php");
			require_once("../db/DbDepartamentos.php");
			require_once("../db/DbDepMuni.php");
			require_once("../funciones/FuncionesPersona.php");
			require_once("../funciones/Utilidades.php");
			require_once("../funciones/Class_Combo_Box.php");
			require_once("../funciones/Class_Color_Pick.php");
			
			$dbAdmision = new DbAdmision();
			$dbPacientes = new DbPacientes();
			$dbPagos = new DbPagos();
			$dbHistoriaClinica = new DbHistoriaClinica();
			$dbListas = new DbListas();
			$dbPaises = new DbPaises();
			$dbDepartamentos = new DbDepartamentos();
			$dbDepMuni = new DbDepMuni();
			$funciones_personas = new FuncionesPersona();
			$utilidades = new Utilidades();
			$comboBox = new Combo_Box();
			
			$cantidad_campos_colores_adm = 9;
			$arr_colores_adm = array();
			$arr_cadenas_colores_adm = array();
			
			//Se obtienen los datos de la admision
			if ($id_admision != "") {
				$admision_obj = $dbAdmision->get_admision($id_admision);
				
				$observacion_aux = trim($admision_obj["observacion_cita"]);
				if ($observacion_aux == "") {
					$observacion_aux = "-";
				}
				$motivo_consulta = $admision_obj["motivo_consulta"];
				$observaciones_admision = $admision_obj["observaciones_admision"];
				$nombre_acompa = $admision_obj["nombre_acompa"];
				$numero_hijos = $admision_obj["numero_hijos"];
				$numero_hijas = $admision_obj["numero_hijas"];
				$numero_hermanos = $admision_obj["numero_hermanos"];
				$numero_hermanas = $admision_obj["numero_hermanas"];
				$presion_arterial = $admision_obj["presion_arterial"];
				$pulso = $admision_obj["pulso"];
				$nombre_convenio = $admision_obj["nombre_convenio"];
				$nombre_plan = $admision_obj["nombre_plan"];
				$cod_medio_pago = $admision_obj["cod_medio_pago"];
				
				$usuario_crea_cita = $admision_obj["usuario_crea_cita"];
				$fecha_crea_cita = $admision_obj["fecha_crea_cita_t"]." ".$admision_obj["hora_crea_cita_t"];
				$usuario_crea_admision = $admision_obj["usuario_crea_admision"];
				$fecha_admision = $admision_obj["fecha_admision_t"]." ".$admision_obj["hora_admision_t"];
				
				//Se obtiene el listado de colores de la admisión
				if ($admision_obj["cadena_colores"] != "") {
					array_push($arr_cadenas_colores_adm, $admision_obj["cadena_colores"]);
				} else {
					//Se inicia por defecto en azul la presión arterial
					array_push($arr_cadenas_colores_adm, "000002000");
				}
			} else {
				$observacion_aux = "-";
				$motivo_consulta = "-";
				$observaciones_admision = "-";
				$nombre_acompa = "-";
				$numero_hijos = "";
				$numero_hijas = "";
				$numero_hermanos = "";
				$numero_hermanas = "";
				$presion_arterial = "";
				$pulso = "";
				$nombre_convenio = "-";
				$nombre_plan = "-";
				$cod_medio_pago = "";
				
				$usuario_crea_cita = "-";
				$fecha_crea_cita = "-";
				$usuario_crea_admision = "-";
				$fecha_admision = "-";
			}
			
			//Se instancia la clase que administrará los colores de los campos de admisiones
			$colorPickAdm = new Color_Pick($arr_cadenas_colores_adm, $cantidad_campos_colores_adm, "adm");
			$arr_colores_adm = $colorPickAdm->getArrayColores();
			
			$observaciones_remision = "";
			$fecha_hc = "";
			if ($id_hc != "" && $id_hc != "0") {
				$hc_obj = $dbHistoriaClinica->getHistoriaClinicaId($id_hc);
				if (isset($hc_obj["id_hc"])) {
					$observaciones_remision = $hc_obj["observaciones_remision"];
					$fecha_hc = $funciones_personas->obtenerFecha6($hc_obj["fecha_hc_t"]);
				}
			}
						
			//Datos del paciente
			$datos_paciente = $dbPacientes->getEdadPaciente($id_paciente, "");
			$edad_paciente = $datos_paciente["edad"];
			$profesion_paciente = $datos_paciente["profesion"];			
			$numero_documento = $datos_paciente["numero_documento"];
			$fecha_nacimiento = $datos_paciente["fecha_nacimiento_t"];
			$direccion = $datos_paciente["direccion"];
			$sexo = $datos_paciente["sexo"];
			$sexo_t = $datos_paciente["sexo_t"];
			$id_estado_civil = $datos_paciente["id_estado_civil"];			
			$estado_civil = $datos_paciente["estado_civil"];			
			$id_pais_res = $datos_paciente["id_pais"];
			$cod_dep_res = $datos_paciente["cod_dep"];
			$cod_mun_res = $datos_paciente["cod_mun"];
			$pais_res = $datos_paciente["pais_res"]; 
			$dept_res = $datos_paciente["dept_res"];
			$muni_res = $datos_paciente["muni_res"];
			$nom_dep_res = $datos_paciente["nom_dep"];
			$nom_mun_res = $datos_paciente["nom_mun"];
			$telefono_1 = $datos_paciente["telefono_1"];
			$telefono_2 = $datos_paciente["telefono_2"];
			$nombre_1 = $datos_paciente["nombre_1"]; 
			$nombre_2 = $datos_paciente["nombre_2"];
			$apellido_1 = $datos_paciente["apellido_1"];
			$apellido_2 = $datos_paciente["apellido_2"];
			
			$id_pais_nac = $datos_paciente["id_pais_nac"];
			$cod_dep_nac = $datos_paciente["cod_dep_nac"];
			$cod_mun_nac = $datos_paciente["cod_mun_nac"];
			$pais_nac = $datos_paciente["pais_nac"];
			$dept_nac = $datos_paciente["dept_nac"];
			$muni_nac = $datos_paciente["muni_nac"];
			$nom_dep_nac = $datos_paciente["nom_dep_nac"];
			$nom_mun_nac = $datos_paciente["nom_mun_nac"];
			$email = $datos_paciente["email"];
			$observ_paciente = $datos_paciente["observ_paciente"];
			
			//Calcular la edad del paciente con respecto a la fecha de HC
			$fecha_nacimiento_aux = explode( '/', $fecha_nacimiento);
			$dia = $fecha_nacimiento_aux[0]; $mes = $fecha_nacimiento_aux[1]; $año = $fecha_nacimiento_aux[2];
			$fecha_nacimiento_aux = $año."-".$mes."-".$dia;
			$fecha_nacimiento_aux = date("Y-m-d", strtotime($fecha_nacimiento_aux));
			
			if($id_hc <> 0){
				$hc_obj_aux = $dbHistoriaClinica->getHistoriaClinicaId($id_hc);
				$fecha_hc_aux = $funciones_personas->obtenerFecha($hc_obj["fecha_hc_t"]);
				$fecha_hc_aux = date("Y-m-d", strtotime($fecha_hc_aux));	
			}else{
			  $fecha_hc_aux = "";
			}
			
		
			$edad_paciente_arr = $dbPacientes->getEdad_HC($fecha_nacimiento_aux, $fecha_hc_aux);
			$edad_paciente_arr = $edad_paciente_arr["edad"];
			$edad_paciente_arr = explode("/", $edad_paciente_arr);
			$edad_paciente_fin = $funciones_personas->obtenerEdad2($edad_paciente_arr[0],$edad_paciente_arr[1]);
			
			
			$cantidad_campos_colores_pac = 23;		
			//Se obtiene el listado de colores del paciente
			$arr_colores_pac = array();
			$arr_cadenas_colores_pac = array();
			if ($datos_paciente["cadena_colores"] != "") {
				array_push($arr_cadenas_colores_pac, $datos_paciente["cadena_colores"]);
			} else {
				//Se inicia por defecto en azul la presión arterial
				array_push($arr_cadenas_colores_pac, "22220000000200000000000");
			}
			
			//Se instancia la clase que administrará los colores de los campos de pacientes
			$colorPickPac = new Color_Pick($arr_cadenas_colores_pac, $cantidad_campos_colores_pac, "pac");
			$arr_colores_pac = $colorPickPac->getArrayColores();
			
			if (!$ind_actualizado) {
	?>
    <div class="contenedor_principal" id="encabezado_hc_principal" style="border: 1px solid #999 !important;">
    	<?php
			}
		?>
        <input type="hidden" id="hdd_cant_color_pick_adm" value="<?php echo($cantidad_campos_colores_adm); ?>" />
        <input type="hidden" id="hdd_cant_color_pick_pac" value="<?php echo($cantidad_campos_colores_pac); ?>" />
    	<input type="hidden" id="hdd_id_paciente_enc_hc" value="<?php echo($id_paciente); ?>" />
    	<input type="hidden" id="hdd_id_admision_enc_hc" value="<?php echo($id_admision); ?>" />
    	<input type="hidden" id="hdd_id_hc_enc_hc" value="<?php echo($id_hc); ?>" />
    	<input type="hidden" id="hdd_ind_editar_enc_hc" value="<?php echo($ind_editar); ?>" />
		<table border="0" cellpadding="2" cellspacing="0" align="center" style="width:98%; cursor: pointer;" onclick="mostrar_observaciones('encabezado_hc');">
			<tr>
				<th align="left" style="width:30%;">
					<h5 style="margin: 1px;">
						<div id="encabezado_hc_ver" class="ver_obser">&nbsp;</div>&nbsp;
						<?php
							$nombre_aux = '<span class="componente_color_pick_'.$arr_colores_pac[0].'">'.$nombre_1.'</span>';
							if ($nombre_2 != "") {
								$nombre_aux .= ' <span class="componente_color_pick_'.$arr_colores_pac[1].'">'.$nombre_2.'</span>';
							}
							$nombre_aux .= ' <span class="componente_color_pick_'.$arr_colores_pac[2].'">'.$apellido_1.'</span>';
							if ($apellido_2 != "") {
								$nombre_aux .= ' <span class="componente_color_pick_'.$arr_colores_pac[3].'">'.$apellido_2.'</span>';
							}
                        	echo($nombre_aux);
						?>
					</h5>
				</th>
                <th align="left" style="width:8%;">
                	<h5 style="margin: 1px;">
	                	<span class="componente_color_pick_<?php echo($arr_colores_pac[11]); ?>"><?php echo($edad_paciente_fin); ?></span>
                    </h5>
                </th>
				<th align="left" style="width:35%;">
                	<?php
                    	$texto_aux = $nombre_convenio." / ".$nombre_plan;
					?>
					<h5 style="margin: 1px;"><b><?php echo($texto_aux); ?></b></h5>
				</th>
                <th align="left" style="width:12%;">
                    <h5 style="margin: 1px;">
	                	<?php
							if ($cod_medio_pago == "99" /*|| $id_admision == "0"*/) {
						?>
                    	<b><span style="color:#FF0000;">NP</span></b>
            	        <?php
							} else if ($id_admision != "0") {
								//Se obtiene el valor total pagado en la admisión
								$total_pago_obj = $dbPagos->get_total_pago($id_admision);
						?>
                        <b><?php echo($utilidades->enmascarar_valor($total_pago_obj["total"])); ?></b>
                        <?php
							}
						?>
                    </h5>
                </th>
                <th align="left" style="width:11%;">
                	<h5 style="margin: 1px;"><?php echo($fecha_hc); ?></h5>
                </th>
				<th align="right" style="width:4%;">
                	<?php
                    	if ($observaciones_remision != "") {
					?>
					<img src="../imagenes/icon-refer.png" />
                    <?php
						}
					?>
				</th>	
			</tr>
		</table>
        <div>
        	<div class="contenedor_error" id="d_contenedor_error_enc_hc"></div>
        	<div class="contenedor_exito" id="d_contenedor_exito_enc_hc"></div>
            <div id="d_guardar_enc_hc" style="display:none;"></div>
        </div>
		<div id="encabezado_hc" style="display:none;">
			<table id="tbl_encabezado_hc_1" border="0" cellpadding="2" cellspacing="0" align="center" style="width:98%;">
				<tr>
					<th align="left" colspan="3">
						<h6 style="margin: 1px;"><b>H.C No: </b><?php echo($numero_documento); ?></h6>
				   </th>
				</tr>
				<tr>
					<th align="left" colspan="2" style="width:55%;">
						<h6 style="margin: 1px;"><b>Acompa&ntilde;ante: </b><span class="componente_color_pick_<?php echo($arr_colores_adm[0]); ?>"><?php echo($nombre_acompa); ?></span></h6>
					</th>
					<th align="left" style="width:45%;">
						<h6 style="margin: 1px;"><b>Estado Civil: </b><span class="componente_color_pick_<?php echo($arr_colores_pac[4]); ?>"><?php echo($estado_civil); ?></span></h6>
					</th>
				</tr>
				<tr>
					<th align="left" colspan="2">
						<h6 style="margin: 1px;"><b>Profesi&oacute;n: </b><span class="componente_color_pick_<?php echo($arr_colores_pac[5]); ?>"><?php echo($profesion_paciente); ?></span></h6>
					</th>
					<th align="left">
						<h6 style="margin: 1px;">
                        	<b>Natural de: </b>
							<?php
								if ($id_pais_nac <> 1) {
									$ciudad_nacimiento = '<span class="componente_color_pick_'.$arr_colores_pac[10].'">'.$nom_mun_nac.'</span>, <span class="componente_color_pick_'.$arr_colores_pac[8].'">'.$nom_dep_nac.'</span> (<span class="componente_color_pick_'.$arr_colores_pac[6].'">'.$pais_nac.'</span>)';	
								} else {
									$ciudad_nacimiento = '<span class="componente_color_pick_'.$arr_colores_pac[9].'">'.$muni_nac.'</span>, <span class="componente_color_pick_'.$arr_colores_pac[7].'">'.$dept_nac.'</span>';
								}
								
                            	echo($ciudad_nacimiento);
							?>
                        </h6>
					</th>
				</tr>
				<tr>
					<th align="left" colspan="2">
						<h6 style="margin: 1px;"><b>Fecha de nacimiento: </b><span class="componente_color_pick_<?php echo($arr_colores_pac[11]); ?>"><?php echo($fecha_nacimiento); ?></span></h6>
					</th>
					<th align="left">
						<h6 style="margin: 1px;"><b>G&eacute;nero: </b><span class="componente_color_pick_<?php echo($arr_colores_pac[12]); ?>"><?php echo($sexo_t); ?></span></h6>
					</th>
                </tr>
                <tr>
					<th align="left" style="width:25%;">
						<h6 style="margin: 1px;"><b>Hijos: </b><span class="componente_color_pick_<?php echo($arr_colores_adm[1]); ?>"><?php echo($numero_hijos); ?></span> / <b>Hijas: </b><span class="componente_color_pick_<?php echo($arr_colores_adm[2]); ?>"><?php echo($numero_hijas); ?></span></h6>
					</th>
					<th align="left" style="width:30%;">
						<h6 style="margin: 1px;"><b>Hermanos: </b><span class="componente_color_pick_<?php echo($arr_colores_adm[3]); ?>"><?php echo($numero_hermanos); ?></span> / <b>Hermanas: </b><span class="componente_color_pick_<?php echo($arr_colores_adm[4]); ?>"><?php echo($numero_hermanas); ?></span></h6>
					</th>
					<th align="left">
						<h6 style="margin: 1px;">
                        	<b>Ciudad de residencia: </b>
							<?php
								if ($id_pais_res <> 1) {
									$ciudad_residencia = '<span class="componente_color_pick_'.$arr_colores_pac[18].'">'.$nom_mun_res.'</span>, <span class="componente_color_pick_'.$arr_colores_pac[16].'">'.$nom_dep_res.'</span> (<span class="componente_color_pick_'.$arr_colores_pac[14].'">'.$pais_res.'</span>)';	
								} else {
									$ciudad_residencia = '<span class="componente_color_pick_'.$arr_colores_pac[17].'">'.$muni_res.'</span>, <span class="componente_color_pick_'.$arr_colores_pac[15].'">'.$dept_res.'</span>';
								}
								
                            	echo($ciudad_residencia);
							?>
                        </h6>
					</th>
				</tr>
				<tr>
					<th align="left" colspan="2">
						<h6 style="margin: 1px;"><b>Direcci&oacute;n: </b><span class="componente_color_pick_<?php echo($arr_colores_pac[13]); ?>"><?php echo($direccion); ?></span></h6>
					</th>
					<th align="left">
						<h6 style="margin: 1px;">
                        	<b>Tel&eacute;fonos: </b>
							<?php
								$telefonos = '<span class="componente_color_pick_'.$arr_colores_pac[19].'">'.$telefono_1.'</span>'.(trim($telefono_2) != "" ? ' - <span class="componente_color_pick_'.$arr_colores_pac[20].'">'.$telefono_2.'</span>' : "");
								
                            	echo($telefonos);
							?>
                        </h6>
					</th>
                </tr>
                <tr>
					<th align="left" colspan="2">
						<h6 style="margin: 1px;"><b>e-mail: </b><span class="componente_color_pick_<?php echo($arr_colores_pac[21]); ?>"><?php echo($email); ?></span></h6>
					</th>
                </tr>
                <tr>
                	<th align="left" colspan="3">
                    	<h6 style="margin: 1px;"><b>Observaciones del paciente: </b><span class="componente_color_pick_<?php echo($arr_colores_pac[22]); ?>"><?php echo($observ_paciente != "" ? $observ_paciente : "-"); ?></span></h6>
                    </th>
                </tr>
			</table>
			<table id="tbl_encabezado_hc_2" border="0" cellpadding="2" cellspacing="0" align="center" style="width:98%; display:none;">
				<tr>
					<th align="left" colspan="2">
						<h6 style="margin: 1px;"><b>H.C No: </b><?php echo($numero_documento); ?></h6>
				   </th>
				</tr>
                <tr>
                </tr>
                	<th align="left" style="width:55%;">
						<h6 style="margin: 1px;">
                        	<b>Nombres*:&nbsp;</b>
                        	<input type="text" id="txt_nombre_1_enc_hc" maxlength="100" class="no-margin no-padding componente_color_pick_<?php echo($arr_colores_pac[0]); ?>" style="width:120px; display:inline-block;" value="<?php echo($nombre_1); ?>" />
                            <?php
								$colorPickPac->getColorPick("txt_nombre_1_enc_hc", 0, "");
							?>
                            &nbsp;&nbsp;
                        	<input type="text" id="txt_nombre_2_enc_hc" maxlength="100" class="no-margin no-padding componente_color_pick_<?php echo($arr_colores_pac[1]); ?>" style="width:120px; display:inline-block;" value="<?php echo($nombre_2); ?>" />
                            <?php
								$colorPickPac->getColorPick("txt_nombre_2_enc_hc", 1, "");
							?>
                        </h6>
                    </th>
                    <th align="left" style="width:45%;">
						<h6 style="margin: 1px;">
                        	<b>Apellidos*:&nbsp;</b>
                        	<input type="text" id="txt_apellido_1_enc_hc" maxlength="100" class="no-margin no-padding componente_color_pick_<?php echo($arr_colores_pac[2]); ?>" style="width:120px; display:inline-block;" value="<?php echo($apellido_1); ?>" />
                            <?php
								$colorPickPac->getColorPick("txt_apellido_1_enc_hc", 2, "");
							?>
                            &nbsp;&nbsp;
                        	<input type="text" id="txt_apellido_2_enc_hc" maxlength="100" class="no-margin no-padding componente_color_pick_<?php echo($arr_colores_pac[3]); ?>" style="width:120px; display:inline-block;" value="<?php echo($apellido_2); ?>" />
                            <?php
								$colorPickPac->getColorPick("txt_apellido_2_enc_hc", 3, "");
							?>
                        </h6>
                    </th>
				<tr>
					<th align="left">
						<h6 style="margin: 1px;">
                        	<b>Acompa&ntilde;ante:&nbsp;</b>
                        	<input type="text" id="txt_nombre_acompa_enc_hc" maxlength="100" class="no-margin no-padding componente_color_pick_<?php echo($arr_colores_adm[0]); ?>" style="width:250px; display:inline-block;" value="<?php echo($nombre_acompa); ?>" />
                            <?php
                            	$colorPickAdm->getColorPick("txt_nombre_acompa_enc_hc", 0, "");
							?>
                        </h6>
					</th>
					<th align="left">
						<h6 style="margin: 1px;">
                        	<b>Estado Civil*:&nbsp;</b>
							<?php
								$lista_estados_civiles = $dbListas->getListaDetalles(40, 1);
								$comboBox->getComboDb("cmb_estado_civil_enc_hc", $id_estado_civil, $lista_estados_civiles, "id_detalle,nombre_detalle", "--Seleccione--", "", true, "width:120px;", "", "no-margin no-padding componente_color_pick_".$arr_colores_pac[4]);
								$colorPickPac->getColorPick("cmb_estado_civil_enc_hc", 4, "");
							?>
                        </h6>
					</th>
				</tr>
				<tr>
					<th align="left">
						<h6 style="margin: 1px;">
                        	<b>Profesi&oacute;n*:&nbsp;</b>
                            <input type="text" id="txt_profesion_enc_hc" maxlength="100" class="no-margin no-padding componente_color_pick_<?php echo($arr_colores_pac[5]); ?>" style="width:250px; display:inline-block;" value="<?php echo($profesion_paciente); ?>" />
                            <?php
                            	$colorPickPac->getColorPick("txt_profesion_enc_hc", 5, "");
							?>
                        </h6>
					</th>
					<th align="left">
						<h6 style="margin: 1px;">
                        	<b>Natural de*:&nbsp;</b>
                            <?php
								$lista_paises = $dbPaises->getPaises();
								$lista_departamentos = $dbDepartamentos->getDepartamentos();
								$lista_municipios = $dbDepMuni->getMunicipiosDepartamento($cod_dep_nac);
								
								$comboBox->getComboDb("cmb_pais_nac_enc_hc", $id_pais_nac, $lista_paises, "id_pais, nombre_pais", "--Seleccione--", "cambiar_pais_enc_hc(this.value, 'nac');", true, "width:250px;", "", "no-margin no-padding componente_color_pick_".$arr_colores_pac[6]);
								$colorPickPac->getColorPick("cmb_pais_nac_enc_hc", 6, "");
							?>
                        </h6>
					</th>
				</tr>
                <tr>
                	<th align="left">
                    	<h6 style="margin: 1px;">
                        	<div id="d_cod_dep_nac_enc_hc" style="display:<?php if ($id_pais_nac == "1") { ?>block<?php } else { ?>none<?php } ?>;">
                        	    <b>Departamento*:&nbsp;</b>
                                <?php
                            		$comboBox->getComboDb("cmb_dep_nac_enc_hc", $cod_dep_nac, $lista_departamentos, "cod_dep, nom_dep", "--Seleccione--", "cambiar_departamento_enc_hc(this.value, 'nac');", true, "width:250px;", "", "no-margin no-padding componente_color_pick_".$arr_colores_pac[7]);
									$colorPickPac->getColorPick("cmb_dep_nac_enc_hc", 7, "");
								?>
                            </div>
                        	<div id="d_nom_dep_nac_enc_hc" style="display:<?php if ($id_pais_nac != "1") { ?>block<?php } else { ?>none<?php } ?>;">
                        	    <b>Estado/Provincia*:&nbsp;</b>
                                <input type="text" id="txt_nom_dep_nac_enc_hc" maxlength="50" class="no-margin no-padding componente_color_pick_<?php echo($arr_colores_pac[8]); ?>" style="width:250px; display:inline-block;" value="<?php echo($nom_dep_nac); ?>" />
                                <?php
                                	$colorPickPac->getColorPick("txt_nom_dep_nac_enc_hc", 8, "");
								?>
                            </div>
                        </h6>
                    </th>
                	<th align="left">
                    	<h6 style="margin: 1px;">
                        	<div id="d_cod_mun_nac_enc_hc" style="display:<?php if ($id_pais_nac == "1") { ?>block<?php } else { ?>none<?php } ?>;">
                        	    <b>Municipio*:&nbsp;</b>
                                <div id="d_mun_nac_enc_hc" style="display:inline-block;">
                                    <?php
										$comboBox->getComboDb("cmb_mun_nac_enc_hc", $cod_mun_nac, $lista_municipios, "cod_mun_dane, nom_mun", "--Seleccione--", "", true, "width:250px;", "", "no-margin no-padding componente_color_pick_".$arr_colores_pac[9]);
										$colorPickPac->getColorPick("cmb_mun_nac_enc_hc", 9, "");
									?>
                                </div>
                            </div>
                            <div id="d_nom_mun_nac_enc_hc" style="display:<?php if ($id_pais_nac != "1") { ?>block<?php } else { ?>none<?php } ?>;">
                                <b>Municipio*:&nbsp;</b>
                                <input type="text" id="txt_nom_mun_nac_enc_hc" maxlength="50" class="no-margin no-padding componente_color_pick_<?php echo($arr_colores_pac[10]); ?>" style="width:250px; display:inline-block;" value="<?php echo($nom_mun_nac); ?>" />
                                <?php
                                	$colorPickPac->getColorPick("txt_nom_mun_nac_enc_hc", 10, "");
								?>
                            </div>
                        </h6>
                    </th>
                </tr>
				<tr>
					<th align="left">
						<h6 style="margin: 1px;">
                        	<b>Fecha de nacimiento*:&nbsp;</b>
                            <input type="text" id="txt_fecha_nacimiento_enc_hc" class="no-margin no-padding componente_color_pick_<?php echo($arr_colores_pac[11]); ?>" value="<?php echo($fecha_nacimiento); ?>" maxlength="10" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" style="width:120px; display:inline-block;" />
                            <?php
                            	$colorPickPac->getColorPick("txt_fecha_nacimiento_enc_hc", 11, "");
							?>
                        </h6>
					</th>
					<th align="left">
						<h6 style="margin: 1px;">
                        	<b>G&eacute;nero*:&nbsp;</b>
                            <?php
                            	$comboBox->getComboDb("cmb_sexo_enc_hc", $sexo, $dbListas->getTipoSexo(), "id_detalle, nombre_detalle", "--Seleccione--", "", true, "width:120px;", "", "no-margin no-padding componente_color_pick_".$arr_colores_pac[12]);
								$colorPickPac->getColorPick("cmb_sexo_enc_hc", 12, "");
							?>
                        </h6>
					</th>
                </tr>
                <tr>
					<th align="left">
						<h6 style="margin: 1px;">
                        	<b>Hijos:&nbsp;</b>
                            <select id="cmb_numero_hijos_enc_hc" class="select no-margin no-padding componente_color_pick_<?php echo($arr_colores_adm[1]); ?>">
                                <option value="0">0</option>
                                <?php
									for ($i = 1; $i <= 25; $i++) {
										$selected_aux = "";
										if ($i == $numero_hijos) {
											$selected_aux = " selected=\"selected\"";
										}
								?>
                                <option value="<?php echo $i; ?>"<?php echo($selected_aux); ?>><?php echo $i; ?></option>
                                <?php
									}
								?>
                            </select>
                            <?php
                            	$colorPickAdm->getColorPick("cmb_numero_hijos_enc_hc", 1, "");
							?>
                            &nbsp;&nbsp;/&nbsp;&nbsp;<b>Hijas:&nbsp;</b>
                            <select id="cmb_numero_hijas_enc_hc" class="select no-margin no-padding componente_color_pick_<?php echo($arr_colores_adm[2]); ?>">
                                <option value="0">0</option>
                                <?php
									for ($i = 1; $i <= 25; $i++) {
										$selected_aux = "";
										if ($i == $numero_hijas) {
											$selected_aux = " selected=\"selected\"";
										}
								?>
                                <option value="<?php echo $i; ?>"<?php echo($selected_aux); ?>><?php echo $i; ?></option>
                                <?php
									}
								?>
                            </select>
                            <?php
                            	$colorPickAdm->getColorPick("cmb_numero_hijas_enc_hc", 2, "");
							?>
                        </h6>
					</th>
					<th align="left" colspan="2">
						<h6 style="margin: 1px;">
                        	<b>Hermanos:&nbsp;</b>
                            <select id="cmb_numero_hermanos_enc_hc" class="select no-margin no-padding componente_color_pick_<?php echo($arr_colores_adm[3]); ?>">
                                <option value="0">0</option>
                                <?php
									for ($i = 1; $i <= 25; $i++) {
										$selected_aux = "";
										if ($i == $numero_hermanos) {
											$selected_aux = " selected=\"selected\"";
										}
								?>
                                <option value="<?php echo $i; ?>"<?php echo($selected_aux); ?>><?php echo $i; ?></option>
                                <?php
									}
								?>
                            </select>
                            <?php
                            	$colorPickAdm->getColorPick("cmb_numero_hermanos_enc_hc", 3, "");
							?>
                            &nbsp;&nbsp;/&nbsp;&nbsp;<b>Hermanas:&nbsp;</b>
                            <select id="cmb_numero_hermanas_enc_hc" class="select no-margin no-padding componente_color_pick_<?php echo($arr_colores_adm[4]); ?>">
                                <option value="0">0</option>
                                <?php
									for ($i = 1; $i <= 25; $i++) {
										$selected_aux = "";
										if ($i == $numero_hermanas) {
											$selected_aux = " selected=\"selected\"";
										}
								?>
                                <option value="<?php echo $i; ?>"<?php echo($selected_aux); ?>><?php echo $i; ?></option>
                                <?php
									}
								?>
                            </select>
                            <?php
                            	$colorPickAdm->getColorPick("cmb_numero_hermanas_enc_hc", 4, "");
							?>
                        </h6>
					</th>
				</tr>
				<tr>
					<th align="left">
						<h6 style="margin: 1px;">
                        	<b>Direcci&oacute;n*:&nbsp;</b>
                            <input type="text" id="txt_direccion_enc_hc" maxlength="200" class="no-margin no-padding componente_color_pick_<?php echo($arr_colores_pac[13]); ?>" style="width:350px; display:inline-block;" value="<?php echo($direccion); ?>" />
                            <?php
                            	$colorPickPac->getColorPick("txt_direccion_enc_hc", 13, "");
							?>
                        </h6>
					</th>
					<th align="left">
						<h6 style="margin: 1px;">
                        	<b>Pa&iacute;s de residencia*:&nbsp;</b>
                            <?php
								$lista_municipios = $dbDepMuni->getMunicipiosDepartamento($cod_dep_res);
								
								$comboBox->getComboDb("cmb_pais_res_enc_hc", $id_pais_res, $lista_paises, "id_pais, nombre_pais", "--Seleccione--", "cambiar_pais_enc_hc(this.value, 'res');", true, "width:250px;", "", "no-margin no-padding componente_color_pick_".$arr_colores_pac[14]);
								$colorPickPac->getColorPick("cmb_pais_res_enc_hc", 14, "");
							?>
                        </h6>
					</th>
				</tr>
                <tr>
                	<th align="left">
                    	<h6 style="margin: 1px;">
                        	<div id="d_cod_dep_res_enc_hc" style="display:<?php if ($id_pais_res == "1") { ?>block<?php } else { ?>none<?php } ?>;">
                        	    <b>Departamento*:&nbsp;</b>
                                <?php
                            		$comboBox->getComboDb("cmb_dep_res_enc_hc", $cod_dep_res, $lista_departamentos, "cod_dep, nom_dep", "--Seleccione--", "cambiar_departamento_enc_hc(this.value, 'res');", true, "width:250px;", "", "no-margin no-padding componente_color_pick_".$arr_colores_pac[15]);
									$colorPickPac->getColorPick("cmb_dep_res_enc_hc", 15, "");
								?>
                            </div>
                        	<div id="d_nom_dep_res_enc_hc" style="display:<?php if ($id_pais_res != "1") { ?>block<?php } else { ?>none<?php } ?>;">
                        	    <b>Estado/Provincia*:&nbsp;</b>
                                <input type="text" id="txt_nom_dep_res_enc_hc" maxlength="50" class="no-margin no-padding componente_color_pick_<?php echo($arr_colores_pac[16]); ?>" style="width:250px; display:inline-block;" value="<?php echo($nom_dep_res); ?>" />
                                <?php
                                	$colorPickPac->getColorPick("txt_nom_dep_res_enc_hc", 16, "");
								?>
                            </div>
                        </h6>
                    </th>
                	<th align="left">
                    	<h6 style="margin: 1px;">
                        	<div id="d_cod_mun_res_enc_hc" style="display:<?php if ($id_pais_res == "1") { ?>block<?php } else { ?>none<?php } ?>;">
                        	    <b>Municipio*:&nbsp;</b>
                                <div id="d_mun_res_enc_hc" style="display:inline-block;">
                                    <?php
										$comboBox->getComboDb("cmb_mun_res_enc_hc", $cod_mun_res, $lista_municipios, "cod_mun_dane, nom_mun", "--Seleccione--", "", true, "width:250px;", "", "no-margin no-padding componente_color_pick_".$arr_colores_pac[17]);
										$colorPickPac->getColorPick("cmb_mun_res_enc_hc", 17, "");
									?>
                                </div>
                            </div>
                            <div id="d_nom_mun_res_enc_hc" style="display:<?php if ($id_pais_res != "1") { ?>block<?php } else { ?>none<?php } ?>;">
                                <b>Municipio*:&nbsp;</b>
                                <input type="text" id="txt_nom_mun_res_enc_hc" maxlength="50" class="no-margin no-padding componente_color_pick_<?php echo($arr_colores_pac[18]); ?>" style="width:250px; display:inline-block;" value="<?php echo($nom_mun_res); ?>" />
                                <?php
                                	$colorPickPac->getColorPick("txt_nom_mun_res_enc_hc", 18, "");
								?>
                            </div>
                        </h6>
                    </th>
				</tr>
                <tr>
					<th align="left">
						<h6 style="margin: 1px;">
                        	<b>Tel&eacute;fonos*:&nbsp;</b>
                            <input type="text" id="txt_telefono_1_enc_hc" value="<?php echo($telefono_1); ?>" maxlength="20" class="no-margin no-padding componente_color_pick_<?php echo($arr_colores_pac[19]); ?>" style="display:inline-block; width:100px;" />
                            <?php
                            	$colorPickPac->getColorPick("txt_telefono_1_enc_hc", 19, "");
							?>
                            &nbsp;&nbsp;
                            <input type="text" id="txt_telefono_2_enc_hc" value="<?php echo($telefono_2); ?>" maxlength="20" class="no-margin no-padding componente_color_pick_<?php echo($arr_colores_pac[20]); ?>" style="display:inline-block; width:100px;" />
                            <?php
                            	$colorPickPac->getColorPick("txt_telefono_2_enc_hc", 20, "");
							?>
                        </h6>
					</th>
					<th align="left">
						<h6 style="margin: 1px;">
                        	<b>e-mail*:&nbsp;</b>
                        	<input type="text" id="txt_email_enc_hc" value="<?php echo($email); ?>" maxlength="50" class="no-margin no-padding componente_color_pick_<?php echo($arr_colores_pac[21]); ?>" style="display:inline-block; width:250px;" />
                            <?php
                            	$colorPickPac->getColorPick("txt_email_enc_hc", 21, "");
							?>
                         </h6>
					</th>
                </tr>
                <tr>
                	<th align="left">
                        <h6 style="margin: 1px;"><b>Observaciones del paciente:&nbsp;</b></h6>
                        <div style="position:relative; width:100%;">
                            <textarea id="txt_observ_paciente_enc_hc" style="width:95%; height:39px; padding:0;" class="no-margin componente_color_pick_<?php echo($arr_colores_pac[22]); ?>" onblur="trim_cadena(this);"><?php echo($observ_paciente); ?></textarea>
                            <?php
								$colorPickPac->getColorPick("txt_observ_paciente_enc_hc", 22, "position:absolute; right:10px; top:0;");
							?>
                        </div>
                    </th>
                </tr>
			</table>
			<table id="tbl_encabezado_hc_3" border="0" cellpadding="2" cellspacing="0" align="center" style="width:98%;" class="opt_panel_1">
				<tr>
					<th align="left" style="width:55%;">
						<h6 style="margin: 1px;"><b>Presi&oacute;n arterial: </b><span class="componente_color_pick_<?php echo($arr_colores_adm[5]); ?>"><?php echo($presion_arterial); ?></span></h6>
					</th>
					<th align="left" style="width:45%;">
						<h6 style="margin: 1px;"><b>Pulso: </b><span class="componente_color_pick_<?php echo($arr_colores_adm[6]); ?>"><?php echo($pulso); ?></span></h6>
					</th>
				</tr>
                <tr>
					<th align="left">
						<h6 style="margin: 1px;"><b>Cita creada por: </b><?php echo($usuario_crea_cita); ?></h6>
					</th>
					<th align="left">
						<h6 style="margin: 1px;"><b>Fecha: </b><?php echo($fecha_crea_cita); ?></h6>
					</th>
                </tr>
				<tr>
					<th align="left" colspan="2">
                    	<h6 style="margin: 1px;"><b>Observaciones de la Cita: <span style="color:#FF0000;"><?php echo($observacion_aux); ?></span></b></h6>
					</th>
				</tr>
                <tr>
					<th align="left">
						<h6 style="margin: 1px;"><b>Admisi&oacute;n creada por: </b><?php echo($usuario_crea_admision); ?></h6>
					</th>
					<th align="left">
						<h6 style="margin: 1px;"><b>Fecha: </b><?php echo($fecha_admision); ?></h6>
					</th>
                </tr>
				<tr>
					<th align="left" colspan="2">
						<h6 style="margin: 1px;"><b>Observaciones de la Admisi&oacute;n:</b>&nbsp;<span class="componente_color_pick_<?php echo($arr_colores_adm[7] != 0 ? $arr_colores_adm[7] : 1); ?>"><?php echo($observaciones_admision); ?></span></h6>
					</th>
				</tr>
				<tr>
					<th align="left" colspan="2">
						<h6 style="margin: 1px;"><b>Motivo de la consulta:</b>&nbsp;<span class="componente_color_pick_<?php echo($arr_colores_adm[8] != 0 ? $arr_colores_adm[8] : 1); ?>"><?php echo($motivo_consulta); ?></span></h6>
					</th>
				</tr>
                <?php
                	if ($observaciones_remision != "") {
				?>
				<tr>
					<th align="left" colspan="2">
						<h6 style="margin: 1px;"><b>Observaciones de la remisi&oacute;n: </b><?php echo($observaciones_remision); ?></h6>
					</th>
				</tr>
                <?php
					}
					
					if ($ind_editar == 1) {
				?>
                <tr>
                	<th align="center" colspan="2">
                    	<input type="button" class="btnPrincipal peq" value="Editar datos" onclick="iniciar_edicion_datos_enc_hc();" />
                    </th>
                </tr>
                <?php
					}
				?>
			</table>
			<table id="tbl_encabezado_hc_4" border="0" cellpadding="2" cellspacing="0" align="center" style="width:98%; display:none;" class="opt_panel_1">
				<tr>
					<th align="left" style="width:55%;">
						<h6 style="margin: 1px;">
                        	<b>Presi&oacute;n arterial:&nbsp;</b>
                            <input type="text" id="txt_presion_arterial_enc_hc" value="<?php echo($presion_arterial); ?>" maxlength="7" class="no-margin no-padding componente_color_pick_<?php echo($arr_colores_adm[5]); ?>" style="display:inline-block; width:100px;" />
                            <?php
                            	$colorPickAdm->getColorPick("txt_presion_arterial_enc_hc", 5, "");
							?>
                        </h6>
					</th>
					<th align="left" style="width:45%;">
						<h6 style="margin: 1px;">
                        	<b>Pulso:&nbsp;</b>
                            <input type="text" id="txt_pulso_enc_hc" value="<?php echo($pulso); ?>" onkeypress="return solo_numeros(event, false);" maxlength="3" class="no-margin no-padding componente_color_pick_<?php echo($arr_colores_adm[6]); ?>" style="display:inline-block; width:50px;" />
                            <?php
                            	$colorPickAdm->getColorPick("txt_pulso_enc_hc", 6, "");
							?>
                        </h6>
					</th>
				</tr>
                <tr>
					<th align="left">
						<h6 style="margin: 1px;"><b>Cita creada por: </b><?php echo($usuario_crea_cita); ?></h6>
					</th>
					<th align="left">
						<h6 style="margin: 1px;"><b>Fecha: </b><?php echo($fecha_crea_cita); ?></h6>
					</th>
                </tr>
				<tr>
					<th align="left" colspan="2">
						<h6 style="margin: 1px;"><b>Observaciones de la Cita: </b><?php echo($observacion_aux); ?></h6>
					</th>
				</tr>
                <tr>
					<th align="left">
						<h6 style="margin: 1px;"><b>Admisi&oacute;n creada por: </b><?php echo($usuario_crea_admision); ?></h6>
					</th>
					<th align="left">
						<h6 style="margin: 1px;"><b>Fecha: </b><?php echo($fecha_admision); ?></h6>
					</th>
                </tr>
				<tr>
					<th align="left">
						<h6 style="margin: 1px;"><b>Observaciones de la Admisi&oacute;n:&nbsp;</b></h6>
                        <div style="position:relative; width:100%;">
	                        <textarea id="txt_observaciones_admision_enc_hc" style="width:95%; height:39px; padding:0;" class="no-margin componente_color_pick_<?php echo($arr_colores_adm[7]); ?>" onblur="trim_cadena(this);"><?php echo($observaciones_admision); ?></textarea>
    	                    <?php
        	                	$colorPickAdm->getColorPick("txt_observaciones_admision_enc_hc", 7, "position:absolute; right:10px; top:0;");
							?>
                        </div>
					</th>
					<th align="left">
						<h6 style="margin: 1px;"><b>Motivo de la consulta*:&nbsp;</b></h6>
                    	<div style="position:relative; width:100%;">
                        	<textarea id="txt_motivo_consulta_enc_hc" style="width:95%; height:39px; padding:0;" class="no-margin componente_color_pick_<?php echo($arr_colores_adm[8]); ?>" onblur="trim_cadena(this);"><?php echo($motivo_consulta); ?></textarea>
	                        <?php
    	                    	$colorPickAdm->getColorPick("txt_motivo_consulta_enc_hc", 8, "position:absolute; right:0; top:0;");
							?>
                        </div>
					</th>
				</tr>
                <?php
                	if ($observaciones_remision != "") {
				?>
				<tr>
					<th align="left" colspan="2">
						<h6 style="margin: 1px;"><b>Observaciones de la remisi&oacute;n: </b><?php echo($observaciones_remision); ?></h6>
					</th>
				</tr>
                <?php
					}
					
					if ($ind_editar == 1) {
				?>
                <tr>
                	<th align="center" colspan="2">
                    	<input type="button" class="btnSecundario peq" value="Cancelar" onclick="cancelar_edicion_datos_enc_hc();" />
                        &nbsp;&nbsp;
                    	<input type="button" class="btnPrincipal peq" value="Guardar cambios" onclick="guardar_datos_enc_hc();" />
                    </th>
                </tr>
                <?php
					}
				?>
			</table>
		</div>
        <?php
        	if (!$ind_actualizado) {
		?>
	</div>
	<?php
			}
		}
		
		//Función que determina si una admisión tiene asociado un registro de optometría (consulta de optometría, control de optometría, control láser de optometria)
		public function tieneConsultaOptometria($id_admision) {
			require_once("../db/DbConsultaOptometria.php");
			require_once("../db/DbConsultaControlOptometria.php");
			require_once("../db/DbConsultaControlLaser.php");
			
			$dbConsultaOptometria = new DbConsultaOptometria();
			$dbConsultaControlOptometria = new DbConsultaControlOptometria();
			$dbConsultaControlLaser = new DbConsultaControlLaser();
			
			$resultado = false;
			
			//Se busca la consulta de optometría
			$consulta_obj = $dbConsultaOptometria->getConsultaOptometriaAdmision($id_admision);
			if (count($consulta_obj) > 1) {
				$resultado = true;
			} else {
				//Se busca la consulta de control de optometría
				$consulta_obj = $dbConsultaControlOptometria->getConsultaControlOptometriaAdmision($id_admision);
				if (count($consulta_obj) > 1) {
					$resultado = true;
				} else {
					//Se busca la consulta de control láser de optometría
					$consulta_obj = $dbConsultaControlLaser->getConsultaControlLaserAdmision($id_admision);
					if (count($consulta_obj) > 1) {
						$resultado = true;
					}
				}
			}
			
			return $resultado;
		}
	}
?>
