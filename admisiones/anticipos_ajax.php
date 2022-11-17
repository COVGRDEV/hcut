<?php
	header("Content-Type: text/xml; charset=UTF-8");
	
	session_start();
	
	require_once("../db/DbAnticipos.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbTiposPago.php");
	require_once("../db/DbVariables.php");
	require_once("../db/DbPaises.php");
	require_once("../db/DbDepartamentos.php");
	require_once("../db/DbDepMuni.php");
	require_once("../db/DbPacientes.php");
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbTerceros.php");
	require_once("../db/DbAsignarCitas.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/Class_Conector_Siesa.php");
	require_once("../funciones/Class_Terceros_Siesa.php");
	require_once("../funciones/Class_Anticipos_Siesa.php");
	require_once("../funciones/Class_Consultas_Siesa.php");
	
	$dbAnticipos = new DbAnticipos();
	$dbListas = new DbListas();
	$dbTiposPago = new DbTiposPago();
	$dbVariables = new Dbvariables();
	$dbPaises = new DbPaises();
	$dbDepartamentos = new DbDepartamentos();
	$dbDepMuni = new DbDepMuni();
	$dbPacientes = new DbPacientes();
	$dbUsuarios = new DbUsuarios();
	$dbTerceros = new DbTerceros();
	$dbAsignarCitas = new DbAsignarCitas();
	
	$contenido_html = new ContenidoHtml();
	$funcionesPersona = new FuncionesPersona();
	$combo = new Combo_Box();
	$utilidades = new Utilidades();
	$conectorSiesa = new Class_Conector_Siesa();
	$classTercerosSiesa = new Class_Terceros_Siesa();
	$classAnticiposSiesa = new Class_Anticipos_Siesa();
	$classConsultasSiesa = new Class_Consultas_Siesa();
	
	//Lista de bilateralidades
	$arr_bilateralidades = array();
	$arr_bilateralidades[0]["id"] = 0;
	$arr_bilateralidades[0]["valor"] = "No aplica";
	$arr_bilateralidades[1]["id"] = 1;
	$arr_bilateralidades[1]["valor"] = "Unilateral";
	$arr_bilateralidades[2]["id"] = 2;
	$arr_bilateralidades[2]["valor"] = "Bilateral";
	
	$id_usuario = $_SESSION["idUsuario"];
	$opcion = $_POST["opcion"];
	
	$tipo_acceso_menu = $contenido_html->obtener_permisos_menu($_POST["hdd_numero_menu"]);
	
	switch ($opcion) {
		case "1": //Muestra el formulario de creación/edición de anticipos
			@$parametro = $utilidades->str_decode($_POST["parametro"]);
			@$ind_crear = intval($_POST["ind_crear"], 10);
			@$id_anticipo = $utilidades->str_decode($_POST["id_anticipo"]);
			
			//Se verifica si el usuario tiene permisos para modificar la definición del anticipo o de registrar anticipos
			$ind_modificar_pagos = 0;
			$ind_registrar_pagos = 0;
			$lista_perfiles_usuario = $dbUsuarios->getListaPerfilUsuarios($id_usuario);
			if (count($lista_perfiles_usuario) > 0) {
				foreach ($lista_perfiles_usuario as $perfil_aux) {
					if ($perfil_aux["ind_modificar_pagos"] == "1") {
						$ind_modificar_pagos = 1;
					}
					if ($perfil_aux["ind_registrar_pagos"] == "1") {
						$ind_registrar_pagos = 1;
					}
				}
			}
			
			$id_pais = "1";
			$cod_dep = "68";
			
			$lista_anticipos = array();
			if ($ind_crear == 0) {
				if ($id_anticipo == "") {
					$lista_anticipos = $dbAnticipos->get_lista_anticipos($parametro);
				} else {
					$anticipo_obj = $dbAnticipos->get_anticipo($id_anticipo);
					array_push($lista_anticipos, $anticipo_obj);
				}
			}
			
			if (count($lista_anticipos) == 0 && $ind_crear == 0) {
		?>
        <legend>No se hallaron anticipos</legend>
        <?php
			} else {
				if (count($lista_anticipos) <= 1) {
					//Se busca la fecha actual
					$arr_fecha_actual = $dbVariables->getFechaactual();
					$fecha_actual = $arr_fecha_actual["fecha_actual"];
					
					$fecha_anticipo = "";
					
					//Edición o creación de un anticipo
					$anticipo_obj = array();
					$lista_anticipos_det_medios = array();
					if ($ind_crear == 0) {
						$anticipo_obj = $lista_anticipos[0];
						
						//Se busca el detalle de medios de pago
						$lista_anticipos_det_medios = $dbAnticipos->get_lista_anticipos_det_medios($anticipo_obj["id_anticipo"]);
						
						$fecha_anticipo = substr($anticipo_obj["fecha_crea"], 0, 10);
					} else {
						$fecha_anticipo = $fecha_actual;
						$lista_tipos_documento = $dbListas->getListaDetalles(2);
						$lista_paises = $dbPaises->getPaises();
						$lista_departamentos = $dbDepartamentos->getDepartamentos();
						$lista_municipios = $dbDepMuni->getMunicipiosDepartamento($cod_dep);
					}
					
					//Se obtiene el lugra en el que ingresó el usuario
					$id_lugar_usuario = $_SESSION["idLugarUsuario"];
			?>
            <input type="hidden" id="hdd_fecha_pago" value="<?php echo($fecha_anticipo); ?>" />
			<fieldset>
				<legend>Datos del paciente:</legend>
				<table border="0" style="width: 500px; margin: auto; font-size: 10pt;">
					<tr>
						<td align="right" style="width:35%;">
							Tipo de documento*:
						</td>
						<td align="left" style="width:65%;">
                        	<?php
                            	if ($ind_crear == 0) {
                            ?>
							<b><?php echo($anticipo_obj["tipo_documento"]); ?></b>
                            <?php
								} else {
									$combo->getComboDb("cmb_tipo_documento", "", $lista_tipos_documento, "id_detalle, nombre_detalle", "--Seleccione--", "", true, "width: 240px;");
								}
							?>
						</td>
					</tr>
					<tr>
						<td align="right">
							N&uacute;mero de identificaci&oacute;n*:
						</td>
						<td align="left">
                        	<?php
                            	if ($ind_crear == 0) {
                            ?>
                            <b><?php echo($anticipo_obj["numero_documento"]); ?></b>
							<?php
								} else {
							?>
                            <input type="text" id="txt_numero_documento" value="" maxlength="20" style="width:180px;" onblur="trim_cadena(this); verificar_paciente(this.value);" />
                            <div id="d_val_paciente" style="display:none;"></div>
                            <?php
								}
							?>
						</td>
					</tr>
					<tr>
                       	<?php
                           	if ($ind_crear == 0) {
						?>
						<td align="right">
							Nombre completo:
						</td>
						<td align="left">
							<b><?php echo($funcionesPersona->obtenerNombreCompleto($anticipo_obj["nombre_1"], $anticipo_obj["nombre_2"], $anticipo_obj["apellido_1"], $anticipo_obj["apellido_2"])); ?></b>
						</td>
                        <?php
							} else {
						?>
                        <td align="left" colspan="2">
                        	<table border="0" cellpadding="3" cellspacing="0" width="100%">
                            	<tr>
                                	<td align="center" style="width:25%;">Primer nombre*</td>
                                	<td align="center" style="width:25%;">Segundo nombre</td>
                                	<td align="center" style="width:25%;">Primer apellido*</td>
                                	<td align="center" style="width:25%;">Segundo apellido</td>
                            	</tr>
                                <tr>
                                	<td align="center">
                            			<input type="text" id="txt_nombre_1" value="" maxlength="100" style="width:100%;" onblur="trim_cadena(this);" />
                                    </td>
                                	<td align="center">
                            			<input type="text" id="txt_nombre_2" value="" maxlength="100" style="width:100%;" onblur="trim_cadena(this);" />
                                    </td>
                                	<td align="center">
                            			<input type="text" id="txt_apellido_1" value="" maxlength="100" style="width:100%;" onblur="trim_cadena(this);" />
                                    </td>
                                	<td align="center">
                            			<input type="text" id="txt_apellido_2" value="" maxlength="100" style="width:100%;" onblur="trim_cadena(this);" />
                                    </td>
                            	<tr>
                            </table>
                        </td>
                        <?php
							}
						?>
					</tr>
                    <tr>
						<td align="right">
							Edad:
						</td>
						<td align="left">
                        	<?php
                            	if ($ind_crear == 0) {
                            ?>
							<b><?php echo($anticipo_obj["edad"]." a&ntilde;os"); ?></b>
							<?php
								} else {
							?>
                            <span id="sp_edad" style="font-weight:bold;">-</span>
                            <?php
								}
							?>
						</td>
                    </tr>
					<tr>
						<td align="right">
							Direcci&oacute;n*:
						</td>
						<td align="left">
                        	<?php
                            	if ($ind_crear == 0) {
                            ?>
							<b><?php echo($anticipo_obj["direccion"]); ?></b>
							<?php
								} else {
							?>
                            <input type="text" id="txt_direccion" value="" maxlength="200" style="width:240px;" onblur="trim_cadena(this);" />
                            <?php
								}
							?>
						</td>
					</tr>
					<tr>
						<td align="right">
							Pa&iacute;s*:
						</td>
						<td align="left">
                        	<?php
                            	if ($ind_crear == 0) {
                            ?>
                            <b><?php echo($anticipo_obj["nombre_pais"]); ?></b>
                            <?php
								} else {
									$combo->getComboDb("cmb_pais", $id_pais, $lista_paises, "id_pais, nombre_pais", "Seleccione el pa&iacute;s", "seleccionar_pais(this.value);", true, "width: 240px;");
								}
							?>
						</td>
					</tr>
					<tr>
						<td align="right">
							Departamento / Estado*:
						</td>
						<td align="left">
                        	<div id="d_departamento">
								<?php
                                    if ($ind_crear == 0) {
                                        $departamento_aux = "";
                                        if (trim($anticipo_obj["nom_dep_t"]) != "") {
                                            $departamento_aux = $anticipo_obj["nom_dep_t"];
                                        } else {
                                            $departamento_aux = $anticipo_obj["nom_dep"];
                                        }
                                ?>
                                <b><?php echo($departamento_aux); ?></b>
                                <?php
                                    } else {
                                        $combo->getComboDb("cmb_departamento", $cod_dep, $lista_departamentos, "cod_dep, nom_dep", "Seleccione el departamento", "seleccionar_departamento(this.value, '');", true, "width: 240px;");
                                    }
                                ?>
                            </div>
                            <div id="d_nombre_dep" style="display:none;">
                            	<input type="text" id="txt_nom_dep" value="" maxlength="50" style="width:240px; display:inline;" onblur="trim_cadena(this);" />
                            </div>
						</td>
					</tr>
					<tr>
						<td align="right">
							Municipio*:
						</td>
						<td align="left">
                        	<div id="d_municipios">
								<?php
                                    if ($ind_crear == 0) {
                                        $municipio_aux = "";
                                        if (trim($anticipo_obj["nom_mun_t"]) != "") {
                                            $municipio_aux = $anticipo_obj["nom_mun_t"]." (".$anticipo_obj["cod_mun_dane"].")";
                                        } else {
                                            $municipio_aux = $anticipo_obj["nom_mun"];
                                        }
                                ?>
                                <b><?php echo($municipio_aux); ?></b>
                                <?php
                                    } else {
                                        $combo->getComboDb("cmb_municipio", "", $lista_municipios, "cod_mun_dane, nom_mun", "Seleccione el municipio", "", true, "width: 240px;");
                                    }
                                ?>
                            </div>
                            <div id="d_nombre_mun" style="display:none;">
                            	<input type="text" id="txt_nom_mun" value="" maxlength="50" style="width:240px; display:inline;" onblur="trim_cadena(this);" />
                            </div>
						</td>
					</tr>
					<tr>
                        <td align="right">
							Tel&eacute;fono(s)*:
						</td>
						<td align="left">
							<?php
                            	if ($ind_crear == 0) {
									$telefono_aux = $anticipo_obj["telefono_1"];
									if (trim($anticipo_obj["telefono_2"]) != "") {
										$telefono_aux .= " - ".$anticipo_obj["telefono_2"];
									}
							?>
                            <b><?php echo($telefono_aux); ?></b>
                            <?php
								} else {
							?>
                            <input type="text" id="txt_telefono_1" value="" maxlength="20" style="width:120px; display:inline;" onblur="trim_cadena(this);" />
                            &nbsp;-&nbsp;
                            <input type="text" id="txt_telefono_2" value="" maxlength="20" style="width:120px; display:inline;" onblur="trim_cadena(this);" />
                            <?php
								}
							?>
						</td>
					</tr>
				</table>
			</fieldset>
			<div style="margin-bottom:30px;"> 
				<table style="width:100%; margin:auto;" border="0">
					<tr>
						<td style="text-align:right; width:20%;">
							<label class="inline no-margin">Sede*:&nbsp;</label>
						</td>
						<td style="width:30%; text-align:left;">
							<?php
								$id_lugar_aux = "";
								if ($ind_crear == 0) {
									$id_lugar_aux = $anticipo_obj["id_lugar"];
								} else if ($id_lugar_usuario != "999") {
									$id_lugar_aux = $id_lugar_usuario;
								}
								
								if ($ind_crear != 0) {
									$combo->getComboDb("cmb_lugar", $id_lugar_aux, $dbListas->getListaDetalles(12), "id_detalle, nombre_detalle", "--Seleccione la sede--", "", true, "width:200px;", "", "no-margin");
								} else {
									$lugar_obj = $dbListas->getDetalle($id_lugar_aux);
							?>
                            <input type="hidden" id="cmb_lugar" name="cmb_lugar" value="<?php echo($id_lugar_aux); ?>" />
                            <label class="inline no-margin"><b><?php echo($lugar_obj["nombre_detalle"]); ?></b></label>
                            <?php
								}
							?>
						</td>
						<td style="text-align:right; width:20%;">
							<label class="inline no-margin">Profesional asociado*:&nbsp;</label>
						</td>
						<td style="width:30%; text-align:left;">
							<?php
								$id_usuario_prof_aux = "0";
								if ($ind_crear == 0) {
									$id_usuario_prof_aux = $anticipo_obj["id_usuario_prof"];
								}
								
								if ($ind_crear != 0) {
									$lista_usuarios_aux = $dbAsignarCitas->getListaUsuariosCitas("", -1);
									
									$combo->getComboDb("cmb_usuario_prof", $id_usuario_prof_aux, $lista_usuarios_aux, "id_usuario,nombre_completo", "--Seleccione el profesional--", "", true, "width:200px;", "", "no-margin");
								} else {
									$usuario_obj = $dbUsuarios->getUsuario($id_usuario_prof_aux);
							?>
                            <input type="hidden" id="cmb_usuario_prof" name="cmb_usuario_prof" value="<?php echo($id_usuario_prof_aux); ?>" />
                            <label class="inline no-margin"><b><?php echo($usuario_obj["nombre_usuario"]." ".$usuario_obj["apellido_usuario"]); ?></b></label>
                            <?php
								}
							?>
						</td>
                    </tr>
				</table>
                <?php
					if ($ind_crear == 0) {
				?>
				<table style="width: 100%;">
					<tr>
						<td align="left" style="font-size:10pt;">
							<b>N&uacute;mero de anticipo: <?php echo($anticipo_obj["id_anticipo"]); ?></b>
                            <?php
									switch ($anticipo_obj["estado_anticipo"]) {
										case "1":
							?>
                            &nbsp;-&nbsp;<span class="verde"><b>CON SALDO</b></span>
                            <?php
											break;
										case "2":
							?>
                            &nbsp;-&nbsp;<span style="color:#000;"><b>AGOTADO</b></span>
                            <?php
											break;
										case "3":
							?>
                            &nbsp;-&nbsp;<span style="color:#F00;"><b>ANULADO</b></span>
                            <?php
											break;
									}
									
							?>
                            &nbsp;-&nbsp;<b>REGISTRADO POR <?php echo($anticipo_obj["nombre_usuario_crea"]." ".$anticipo_obj["apellido_usuario_crea"]." (".$anticipo_obj["fecha_crea_t"]." ".$anticipo_obj["hora_crea_t"].")"); ?></b>
                            <?php
									if ($anticipo_obj["estado_anticipo"] == "3") {
							?>
                            <br /><span style="color:#F00;"><b>ANULADO POR <?php echo($anticipo_obj["nombre_usuario_anula"]." ".$anticipo_obj["apellido_usuario_anula"]." (".$anticipo_obj["fecha_anula_t"].")"); ?></b></span>
                            <?php
									}
							?>
						</td>
					</tr>
				</table>
                <br />
                <?php
					}
				?>
				<div style="width:800px; margin:auto;">
					<table border="0" cellpadding="5" cellspacing="0" style="width: 100%;">
						<tr>
							<td align="right" valign="top" style="width:17%;"><label>Observaciones</label></td>
							<td align="left" style="width:83%;">
                            	<?php
                                	$observaciones_anticipo_aux = "";
									if ($ind_crear == 0) {
										$observaciones_anticipo_aux = $anticipo_obj["observaciones_anticipo"];
									}
								?>
								<textarea id="txt_observaciones_anticipo" name="txt_observaciones_anticipo" class="textarea_ajustable" onblur="trim_cadena(this);"><?php echo($observaciones_anticipo_aux); ?></textarea>
							</td>
						</tr>
                        <tr>
							<td align="right"><label>N&uacute;mero externo</label></td>
							<td align="left">
                            	<?php
                                	if ($ind_crear == 0) {
										$num_anticipo_aux = $anticipo_obj["num_anticipo"];
									} else {
										$num_anticipo_aux = "-";
									}
								?>
								<label id="lb_num_anticipo" class="verde"><?= $num_anticipo_aux ?></label>
							</td>
                        </tr>
					</table>
					<br/>
                    <?php
                    	$lista_tipos_pagos = $dbTiposPago->getListaTiposPagoAct(1, "414", false, false);
						$lista_bancos = $dbListas->getListaDetalles(15);
						$lista_franquicias_tc = $dbListas->getListaDetalles(79);
						$lista_usuarios_autoriza = $dbUsuarios->getListaUsuariosAutoriza(1, 1);
						foreach ($lista_tipos_pagos as $tipo_pago_aux) {
					?>
                    <input type="hidden" id="hdd_tipo_pago_banco_<?php echo($tipo_pago_aux["id"]); ?>" value="<?php echo($tipo_pago_aux["ind_banco"]); ?>" />
                    <input type="hidden" id="hdd_tipo_pago_tercero_<?php echo($tipo_pago_aux["id"]); ?>" value="<?php echo($tipo_pago_aux["ind_tercero"]); ?>" />
                    <input type="hidden" id="hdd_tipo_pago_negativo_<?php echo($tipo_pago_aux["id"]); ?>" value="<?php echo($tipo_pago_aux["ind_negativo"]); ?>" />
                    <input type="hidden" id="hdd_tipo_pago_rel_<?php echo($tipo_pago_aux["id"]); ?>" value="<?php echo($tipo_pago_aux["id_tipo_rel"]); ?>" />
                    <input type="hidden" id="hdd_tipo_pago_usuario_aut_<?php echo($tipo_pago_aux["id"]); ?>" value="<?php echo($tipo_pago_aux["ind_usuario_aut"]); ?>" />
                    <input type="hidden" id="hdd_tipo_pago_ind_cheque_<?php echo($tipo_pago_aux["id"]); ?>" value="<?php echo($tipo_pago_aux["ind_cheque"]); ?>" />
                    <input type="hidden" id="hdd_tipo_pago_ind_cuenta_<?php echo($tipo_pago_aux["id"]); ?>" value="<?php echo($tipo_pago_aux["ind_cuenta"]); ?>" />
                    <input type="hidden" id="hdd_tipo_pago_ind_cod_seguridad_<?php echo($tipo_pago_aux["id"]); ?>" value="<?php echo($tipo_pago_aux["ind_cod_seguridad"]); ?>" />
                    <input type="hidden" id="hdd_tipo_pago_ind_num_autoriza_<?php echo($tipo_pago_aux["id"]); ?>" value="<?php echo($tipo_pago_aux["ind_num_autoriza"]); ?>" />
                    <input type="hidden" id="hdd_tipo_pago_ind_fecha_vence_<?php echo($tipo_pago_aux["id"]); ?>" value="<?php echo($tipo_pago_aux["ind_fecha_vence"]); ?>" />
                    <input type="hidden" id="hdd_tipo_pago_ind_referencia_<?php echo($tipo_pago_aux["id"]); ?>" value="<?php echo($tipo_pago_aux["ind_referencia"]); ?>" />
                    <input type="hidden" id="hdd_tipo_pago_ind_fecha_consigna_<?php echo($tipo_pago_aux["id"]); ?>" value="<?php echo($tipo_pago_aux["ind_fecha_consigna"]); ?>" />
                    <input type="hidden" id="hdd_tipo_pago_ind_franquicia_tc_<?php echo($tipo_pago_aux["id"]); ?>" value="<?php echo($tipo_pago_aux["ind_franquicia_tc"]); ?>" />
                    <?php
						}
						
						//Se verifica si se trata de un pago nuevo con un plan con cuota moderadora
						if ($ind_crear == 0 && count($resultado2_aux) > 0) {
							if (isset($plan_obj["ind_tipo_pago"]) && $plan_obj["ind_tipo_pago"] == "1") {
								foreach ($resultado2_aux as $reg_aux) {
									$cod_aux = "";
									switch ($reg_aux["tipo_precio"]) {
										case "P":
											$cod_aux = $reg_aux["cod_procedimiento"];
											break;
										case "M":
											$cod_aux = $reg_aux["cod_medicamento"];
											break;
										case "I":
											$cod_aux = $reg_aux["cod_insumo"];
											break;
									}
								}
							}
						}
						
						$total_aux = 0;
					?>
                    <table style="width:100%;" cellpadding="5" cellspacing="0">
                        <tr>
                            <td align="center" style="width:24%; font-size:11pt;">Medio de pago</td>
                            <td align="center" style="width:13%; font-size:11pt;">Valor</td>
                            <td align="center" style="width:42%; font-size:11pt;">Datos adicionales</td>
                            <td align="center" style="width:21%; font-size:11pt;">Autorizaci&oacute;n</td>
                        </tr>
                        <tr>
                       	    <td colspan="4"><hr class="no-margin" /></td>
                        </tr>
                    	<?php
							for ($i = 0; $i < count($lista_anticipos_det_medios); $i++) {
								$det_medio_aux = $lista_anticipos_det_medios[$i];
								$total_aux += floatval($det_medio_aux["valor_pago"]);
						?>
                        <tr id="tr_medio_pago_<?php echo($i); ?>">
                        	<td>
                            	<?php
									$combo->getComboDb("cmb_tipo_pago_".$i, $det_medio_aux["id_medio_pago"], $lista_tipos_pagos, "id, nombre", "&lt;Seleccione el tipo de pago&gt;", "validar_tipo_pago(".$i."); validar_mostrar_datos_tercero();", true, "width:200px;", "", "no-margin");
								?>
                            </td>
							<td>
								<input type="text" disabled id="txt_valor_pago_<?php echo($i); ?>" name="txt_valor_pago_<?php echo($i); ?>" class="no-margin" value="<?php echo(floatval("0".$det_medio_aux["valor_pago"])); ?>" onkeypress="solo_numeros(event, false);" onblur="procesar_tipo_pago(<?php echo($i); ?>);" />
							</td>
							<td align="left" style="font-size:11pt;">
							    <div id="d_banco_mp_<?= $i ?>" style="display:none;">
                                    Banco
                            	    <?php
										$combo->getComboDb("cmb_banco_".$i, $det_medio_aux["id_banco"], $lista_bancos, "id_detalle, nombre_detalle", "&lt;Seleccione el banco&gt;", "seleccionar_banco('txt_valor_pago_".$i."');", 1, "width:100%;", "", "no-margin");
									?>
                                </div>
                                <div id="d_franquicia_tc_mp_<?= $i ?>" style="display:none;">
                                    Franquicia
                                    <?php
										$combo->getComboDb("cmb_franquicia_tc_".$i, $det_medio_aux["id_franquicia_tc"], $lista_franquicias_tc, "id_detalle, nombre_detalle", "&lt;Seleccione la franquicia&gt;", "", 1, "width:100%;", "", "no-margin");
									?>
                                </div>
                                <div id="d_cheque_mp_<?= $i ?>" style="display:none;">
                                    N&uacute;mero cheque
                                    <input type="text" id="txt_num_cheque_<?= $i ?>" name="txt_num_cheque_<?= $i ?>" value="<?= $det_medio_aux["num_cheque"] ?>" maxlength="8" class="no-margin" onblur="convertirAMayusculas(this);" />
                                </div>
                                <div id="d_cuenta_mp_<?= $i ?>" style="display:none;">
                                    N&uacute;mero cuenta/tarjeta
                                    <input type="text" id="txt_num_cuenta_<?= $i ?>" name="txt_num_cuenta_<?= $i ?>" value="<?= $det_medio_aux["num_cuenta"] ?>" maxlength="25" class="no-margin" />
                                </div>
                                <div id="d_num_autoriza_mp_<?= $i ?>" style="display:none;">
                                    N&uacute;mero autorizaci&oacute;n
                                    <input type="text" id="txt_num_autoriza_<?= $i ?>" name="txt_num_autoriza_<?= $i ?>" value="<?= $det_medio_aux["num_autoriza"] ?>" maxlength="10" class="no-margin" />
                                </div>
                                <div id="d_fecha_vence_mp_<?= $i ?>" style="display:none;">
                                    Fecha vencimiento (aaaa/mm)
                                    <br />
                                    <input type="text" id="txt_ano_vence_<?= $i ?>" name="txt_ano_vence_<?= $i ?>" value="<?= $det_medio_aux["ano_vence"] ?>" maxlength="4" class="txt_no_block no-margin" style="width:70px;" onkeypress="return solo_numeros(event, false);" />
                                    &nbsp;/&nbsp;
                                    <input type="text" id="txt_mes_vence_<?= $i ?>" name="txt_mes_vence_<?= $i ?>" value="<?= $det_medio_aux["mes_vence"] ?>" maxlength="2" class="txt_no_block no-margin" style="width:50px;" onkeypress="return solo_numeros(event, false);" />
                                </div>
                                <div id="d_referencia_mp_<?= $i ?>" style="display:none;">
                                    Referencia
                                    <input type="text" id="txt_referencia_<?= $i ?>" name="txt_referencia_<?= $i ?>" value="<?= $det_medio_aux["referencia"] ?>" maxlength="30" class="no-margin" />
                                </div>
                                <div id="d_fecha_consigna_mp_<?= $i ?>" style="display:none;">
                                    Fecha consignaci&oacute;n (dd/mm/aaaa)
                                    <input type="text" name="txt_fecha_consigna_<?= $i ?>" id="txt_fecha_consigna_<?= $i ?>" value="<?= $det_medio_aux["fecha_consigna_t"] ?>" maxlength="10" class="no-margin" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" style="width:120px;" />
                                </div>
							</td>
                            <td>
                            	<?php
                                	$combo->getComboDb("cmb_usuario_autoriza_".$i, $det_medio_aux["id_usuario_autoriza"], $lista_usuarios_autoriza, "id_usuario, nombre_completo", "&lt;Autorizado por&gt;", "", false, "width:100%;", "", "no-margin");
								?>
                            </td>
                        </tr>
						<?php
                            	if ($ind_crear == 0) {
                        ?>
                        <script id="ajax" type="text/javascript">
                            <?php
                                	if ($det_medio_aux["id_medio_pago"] != "") {
                            ?>
                            validar_tipo_pago(<?php echo($i); ?>);
                            procesar_tipo_pago(<?php echo($i); ?>);
                            <?php
                                	}
                            ?>
                        </script>
                        <?php
                            	}
							}
							
							$cont_medios_pago = count($lista_anticipos_det_medios);
							for ($i = count($lista_anticipos_det_medios); $i < 10; $i++) {
								$display_aux = "none";
								if ($i == 0 && count($lista_anticipos_det_medios) == 0) {
									$display_aux = "table-row";
									$cont_medios_pago++;
								}
						?>
                        <tr id="tr_medio_pago_<?php echo($i); ?>" style="display:<?php echo($display_aux); ?>;">
                        	<td>
                            	<?php
									$combo->getComboDb("cmb_tipo_pago_".$i, "", $lista_tipos_pagos, "id, nombre", "&lt;Seleccione el tipo de pago&gt;", "validar_tipo_pago(".$i."); validar_mostrar_datos_tercero();", true, "width: 200px;", "", "no-margin");
								?>
                            </td>
							<td>
								<input type="text" disabled id="txt_valor_pago_<?php echo($i); ?>" name="txt_valor_pago_<?php echo($i); ?>" class="no-margin" value="0" onkeypress="solo_numeros(event, false);" onblur="procesar_tipo_pago(<?php echo($i); ?>);" />
							</td>
							<td align="left" style="font-size:11pt;">
                                <div id="d_banco_mp_<?= $i ?>" style="display:none;">
                                    Banco
								    <?php
                                		$combo->getComboDb("cmb_banco_".$i, "", $lista_bancos, "id_detalle, nombre_detalle", "&lt;Seleccione el banco&gt;", "seleccionar_banco('txt_valor_pago_".$i."');", 1, "width:100%;", "", "no-margin");
									?>
                                </div>
                                <div id="d_franquicia_tc_mp_<?= $i ?>" style="display:none;">
                                    Franquicia
                                    <?php
										$combo->getComboDb("cmb_franquicia_tc_".$i, "", $lista_franquicias_tc, "id_detalle, nombre_detalle", "&lt;Seleccione la franquicia&gt;", "", 1, "width:100%;", "", "no-margin");
									?>
                                </div>
                                <div id="d_cheque_mp_<?= $i ?>" style="display:none;">
                                    N&uacute;mero cheque
                                    <input type="text" id="txt_num_cheque_<?= $i ?>" name="txt_num_cheque_<?= $i ?>" value="" maxlength="8" class="no-margin" onblur="convertirAMayusculas(this);" />
                                </div>
                                <div id="d_cuenta_mp_<?= $i ?>" style="display:none;">
                                    N&uacute;mero cuenta/tarjeta
                                    <input type="text" id="txt_num_cuenta_<?= $i ?>" name="txt_num_cuenta_<?= $i ?>" value="" maxlength="25" class="no-margin" />
                                </div>
                                <div id="d_num_autoriza_mp_<?= $i ?>" style="display:none;">
                                    N&uacute;mero autorizaci&oacute;n
                                    <input type="text" id="txt_num_autoriza_<?= $i ?>" name="txt_num_autoriza_<?= $i ?>" value="" maxlength="10" class="no-margin" />
                                </div>
                                <div id="d_fecha_vence_mp_<?= $i ?>" style="display:none;">
                                    Fecha vencimiento (aaaa/mm)
                                    <br />
                                    <input type="text" id="txt_ano_vence_<?= $i ?>" name="txt_ano_vence_<?= $i ?>" value="" maxlength="4" class="txt_no_block no-margin" style="width:70px;" onkeypress="return solo_numeros(event, false);" />
                                    &nbsp;/&nbsp;
                                    <input type="text" id="txt_mes_vence_<?= $i ?>" name="txt_mes_vence_<?= $i ?>" value="" maxlength="2" class="txt_no_block no-margin" style="width:50px;" onkeypress="return solo_numeros(event, false);" />
                                </div>
                                <div id="d_referencia_mp_<?= $i ?>" style="display:none;">
                                    Referencia
                                    <input type="text" id="txt_referencia_<?= $i ?>" name="txt_referencia_<?= $i ?>" value="" maxlength="30" class="no-margin" />
                                </div>
                                <div id="d_fecha_consigna_mp_<?= $i ?>" style="display:none;">
                                    Fecha consignaci&oacute;n (dd/mm/aaaa)
                                    <input type="text" name="txt_fecha_consigna_<?= $i ?>" id="txt_fecha_consigna_<?= $i ?>" value="" maxlength="10" class="no-margin" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" style="width:120px;" />
                                </div>
							</td>
                            <td>
                            	<?php
                                	$combo->getComboDb("cmb_usuario_autoriza_".$i, "", $lista_usuarios_autoriza, "id_usuario, nombre_completo", "&lt;Autorizado por&gt;", "", false, "width:100%;", "", "no-margin");
								?>
                            </td>
                        </tr>
                        <?php
							}
							
							if ($ind_crear != 0) {
						?>
                        <tr>
                        	<td colspan="5">
                                <div class="agregar_alemetos" onclick="agregar_medio_pago();" title="Agregar medio de pago"></div> 
                                <div class="restar_alemetos" onclick="restar_medio_pago(); validar_mostrar_datos_tercero();" title="Borrar medio de pago"></div>
                            </td>
                        </tr>
                        <?php
							}
						?>
						<tr>
							<td align="right" colspan="2" style="font-size:11pt;">
								Total: <b>$<span id="sp_total_pagar"><?php echo(str_replace(",", ".", number_format($total_aux))); ?></span></b>
							</td>
							<td align="right" style="font-size:11pt;">
                            	<?php
									if ($ind_crear == 0) {
										$saldo_aux = str_replace(",", ".", number_format($anticipo_obj["saldo"]));
									} else {
										$saldo_aux = "0";
									}
								?>
								Saldo: <b>$<span id="sp_saldo_anticipo"><?= $saldo_aux ?></span></b>
							</td>
						</tr>
					</table>
                    <input type="hidden" id="hdd_cont_medios_pago" value="<?php echo($cont_medios_pago); ?>" />
                    <?php
						$id_tercero = "";
						$nombre_tercero = "";
						$display_aux = "none";
                    	if ($ind_crear == 0) {
							$id_tercero = $anticipo_obj["id_tercero"];
							$nombre_tercero = $anticipo_obj["nombre_tercero"];
							$display_aux = "block";
						}
					?>
                    <fieldset id="fs_datos_tercero" style="display:<?php echo($display_aux); ?>;">
                        <legend>Datos del tercero:</legend>
                        <table id="tbl_tercero" style="width:100%;" cellpadding="5" cellspacing="0">
                            <tr>
                                <td>
                                    <label id="lbl_texto_tercero" style="font-weight:bold;" class="texto_resaltar"></label>
                                </td>
                            </tr>
                            <tr>
                                <td align="left">
                                    <div id="d_tercero">
                                        <input type="hidden" id="hdd_id_tercero" value="<?php echo($id_tercero); ?>" />
                                        <input type="hidden" id="hdd_ind_tercero_obl" value="0" />
                                        <div id="d_buscar_tercero" class="d_buscar" style="float:left;" onclick="mostrar_buscar_tercero();" title="Buscar tercero"></div>
                                        <img id="img_borrar_tercero" src="../imagenes/Error-icon.png" style="float:left;" onclick="borrar_tercero();" title="Quitar tercero" />
                                        <h6 style="float:left; width:85%;">
                                            <div id="d_nombre_tercero" style="text-align:left; width:100%;"><?php echo($nombre_tercero); ?></div>
                                        </h6>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                    <script id="ajax" type="text/javascript">
						validar_mostrar_datos_tercero();
					</script>
                    <table width="100%" cellpadding="5" cellspacing="0">
                    	<tr>
                        	<td align="right" style="width:20%"><label>Causal de anulaci&oacute;n:</label></td>
                            <td align="left" style="width:80%">
                            	<?php
									if (isset($anticipo_obj["estado_anticipo"]) && $anticipo_obj["estado_anticipo"] != "3" && $tipo_acceso_menu == "2" && $ind_modificar_pagos == 1) {
										$ind_activo_aux = 1;
									} else {
										$ind_activo_aux = 0;
									}
									
                                	$lista_causales = $dbListas->getListaDetalles(78, 1);
									
									$combo->getComboDb("cmb_causales_devolucion", $anticipo_obj["id_causal_anula"], $lista_causales, "id_detalle, nombre_detalle", "--Seleccione la causal--", "", $ind_activo_aux, "width:400px; margin:0;");
								?>
                            </td>
                        </tr>
                    </table>
				</div>
				<br/>
                <?php
					if ($ind_crear == 0) {
	                	if ($tipo_acceso_menu == "2" && $ind_modificar_pagos == 1) {
                			if (isset($anticipo_obj["estado_anticipo"]) && $anticipo_obj["estado_anticipo"] != "3") {
				?>
				<input class="btnPrincipal" type="button" name="btnBorrarPagos" id="btnBorrarPagos" value="Anular anticipo" onclick="anular_anticipo();" />
                &nbsp;&nbsp;
                <?php
							}
						}
				?>
				<input class="btnPrincipal" type="button" name="btnImprimirPago" id="btnImprimirPago" value="Imprimir recibo" onclick="imprimir_recibo_anticipo();" />
                <?php
					} else if ($tipo_acceso_menu == "2") {
				?>
				<input class="btnPrincipal" type="button" name="btn_crear_anticipo" id="btn_crear_anticipo" value="Crear anticipo" onclick="validar_registrar_anticipo();" />
                <?php
					}
					
					$id_antecedente_aux = "";
					$id_paciente_aux = "";
					$edad_paciente_aux = "";
					$id_plan_aux = "";
					$ind_tipo_pago_aux = "";
					if ($ind_crear == 0) {
						$id_anticipo_aux = $anticipo_obj["id_anticipo"];
						$id_paciente_aux = $anticipo_obj["id_paciente"];
						
						//Se busca la edad del paciente
						$paciente_obj = $dbPacientes->getEdadPaciente($id_paciente_aux, $fecha_anticipo);
						$edad_paciente_aux = $paciente_obj["edad"];
					}
				?>
				<input type="hidden" id="hdd_id_anticipo" name="hdd_id_anticipo" value="<?php echo($id_anticipo_aux); ?>" />
				<input type="hidden" id="hdd_id_paciente" name="hdd_id_paciente" value="<?php echo($id_paciente_aux); ?>" />
				<input type="hidden" id="hdd_edad_paciente" name="hdd_edad_paciente" value="<?php echo($edad_paciente_aux); ?>" />
				<input type="hidden" id="hdd_pagar" name="hdd_pagar" value="<?php echo($total); ?>" />
				<input type="hidden" id="hdd_pagar_aux" name="hdd_pagar_aux" value="0" />
				<div id="fondo_negro_servicios" class="d_fondo_negro"></div>
				<div class="div_centro" id="d_centro_servicios" style="display:none;">
					<a name="a_cierre_panel" id="a_cierre_panel" href="#" onclick="muestraFormularioFlotante(0);"></a>
					<div class="div_interno" id="d_interno_servicios"></div>
				</div>
			</div>
			<?php
				} else { //Si existen registros de anticipos
			?>
			<div style="margin-bottom: 30px;"> 
				<div style="text-align: left;">
					<h5>Anticipos encontrados: <?php echo(count($lista_anticipos)); ?></h5>
				</div>
				<table class="paginated modal_table" style="width: 99%; margin: auto;">
					<thead>
						<tr>
							<th style="width:8%;">Id</th>
							<th style="width:25%;">Nombre</th>
							<th style="width:15%;">N&uacute;mero documento</th>
							<th style="width:12%;">Valor</th>
							<th style="width:12%;">Saldo</th>
							<th style="width:15%;">Fecha</th>
							<th style="width:13%;">Estado</th>
						</tr>
						<?php
							foreach ($lista_anticipos as $anticipo_aux) {
						?>
						<tr onclick="seleccionar_anticipo('<?php echo($anticipo_aux["id_anticipo"]); ?>');">
                        	<td align="center">
                            	<?= $anticipo_aux["id_anticipo"] ?>
                            </td>
							<td align="left">
								<?php echo($funcionesPersona->obtenerNombreCompleto($anticipo_aux["nombre_1"], $anticipo_aux["nombre_2"], $anticipo_aux["apellido_1"], $anticipo_aux["apellido_2"])); ?>
							</td>
							<td><?php echo($anticipo_aux["cod_tipo_documento"]." ".$anticipo_aux["numero_documento"]); ?></td>
							<td align="center">
                            	<?= "$".str_replace(",", ".", number_format($anticipo_aux["valor"])) ?>
                            </td>
							<td align="center">
                            	<?= "$".str_replace(",", ".", number_format($anticipo_aux["saldo"])) ?>
                            </td>
							<td><?php echo($anticipo_aux["fecha_crea_t"]); ?></td>
							<?php
								$estado_aux = "";
								switch ($anticipo_aux["estado_anticipo"]) {
									case "1":
										$estado_aux = "CON SALDO";
										break;
									case "2":
										$estado_aux = "AGOTADO";
										break;
									case "3":
										$estado_aux = "<span class='rojo'>ANULADO</span>";
										break;
								}
							?>
							<td><?php echo($estado_aux); ?></td>
						</tr>
						<?php
							}
						?>
					</thead>
				</table>
			</div>
			<?php
				}
			}
			break;
			
		case "2": //Registra el anticipo
			@$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
			@$id_lugar = $utilidades->str_decode($_POST["id_lugar"]);
			@$id_usuario_prof = $utilidades->str_decode($_POST["id_usuario_prof"]);
			@$observaciones_anticipo = trim($utilidades->str_decode($_POST["observaciones_anticipo"]));
			@$id_tercero = $utilidades->str_decode($_POST["id_tercero"]);
			
			@$cant_medios_pago = intval($_POST["cant_medios_pago"]);
			$arr_medios_pago = array();
			for ($i = 0; $i < $cant_medios_pago; $i++) {
				if ($_POST["tipo_pago_".$i] != "") {
					$arr_aux = array();
					@$arr_aux["tipo_pago"] = $utilidades->str_decode($_POST["tipo_pago_".$i]);
					@$arr_aux["banco_pago"] = $utilidades->str_decode($_POST["banco_pago_".$i]);
					@$arr_aux["num_cheque"] = $utilidades->str_decode($_POST["num_cheque_".$i]);
					@$arr_aux["num_cuenta"] = $utilidades->str_decode($_POST["num_cuenta_".$i]);
					@$arr_aux["cod_seguridad"] = $utilidades->str_decode($_POST["cod_seguridad_".$i]);
					@$arr_aux["num_autoriza"] = $utilidades->str_decode($_POST["num_autoriza_".$i]);
					@$arr_aux["ano_vence"] = $utilidades->str_decode($_POST["ano_vence_".$i]);
					@$arr_aux["mes_vence"] = $utilidades->str_decode($_POST["mes_vence_".$i]);
					@$arr_aux["referencia"] = $utilidades->str_decode($_POST["referencia_".$i]);
					@$arr_aux["fecha_consigna"] = $utilidades->str_decode($_POST["fecha_consigna_".$i]);
					@$arr_aux["id_franquicia_tc"] = $utilidades->str_decode($_POST["id_franquicia_tc_".$i]);
					@$arr_aux["valor_pago"] = $utilidades->str_decode($_POST["valor_pago_".$i]);
					@$arr_aux["id_usuario_autoriza"] = $utilidades->str_decode($_POST["id_usuario_autoriza_".$i]);
					
					array_push($arr_medios_pago, $arr_aux);
				}
			}
			
			@$id_tipo_documento = $utilidades->str_decode($_POST["id_tipo_documento"]);
			@$numero_documento = $utilidades->str_decode($_POST["numero_documento"]);
			@$nombre_1 = $utilidades->str_decode($_POST["nombre_1"]);
			@$nombre_2 = $utilidades->str_decode($_POST["nombre_2"]);
			@$apellido_1 = $utilidades->str_decode($_POST["apellido_1"]);
			@$apellido_2 = $utilidades->str_decode($_POST["apellido_2"]);
			@$direccion = $utilidades->str_decode($_POST["direccion"]);
			@$id_pais = $utilidades->str_decode($_POST["id_pais"]);
			@$cod_dep = $utilidades->str_decode($_POST["cod_dep"]);
			@$nom_dep = $utilidades->str_decode($_POST["nom_dep"]);
			@$cod_mun = $utilidades->str_decode($_POST["cod_mun"]);
			@$nom_mun = $utilidades->str_decode($_POST["nom_mun"]);
			@$telefono_1 = $utilidades->str_decode($_POST["telefono_1"]);
			@$telefono_2 = $utilidades->str_decode($_POST["telefono_2"]);
			
			//Se hallan los datos de compañía asociados al lugar del pago
			$sede_det_obj = $dbListas->getSedesDetalle($id_lugar);
			$compania = $sede_det_obj["id_compania"];
			$co_ventas = $sede_det_obj["id_co_ventas"];
			$bodega_ventas = $sede_det_obj["id_bodega_ventas"];
			
			//Se crea/actualiza el paciente
			$id_paciente_resul = $dbPacientes->crear_editar_paciente($id_paciente, $id_tipo_documento, $numero_documento, $nombre_1,
								 $nombre_2, $apellido_1, $apellido_2, $direccion, $id_pais, $cod_dep, $cod_mun, $nom_dep, $nom_mun,
								 $telefono_1, $telefono_2, $id_usuario);
			
		?>
        <input type="hidden" id="hdd_id_paciente_resul" value="<?php echo($id_paciente_resul); ?>" />
        <?php
			if ($id_paciente_resul > 0) {
				$id_paciente = $id_paciente_resul;
				$resultado = -1;
				
				//Se verifica que el tercero (paciente o responsable) exista en SIESA
				if ($id_tercero != "") {
					$tercero_obj = $dbTerceros->getTercero($id_tercero);
					$tipo_doc_tercero = $tercero_obj["codigo_doc_siesa"];
					$num_doc_tercero = $tercero_obj["numero_documento"];
					
					$resultado_aux = $classTercerosSiesa->crearTerceroClienteNoPaciente($id_tercero, $id_paciente, $compania, $id_usuario);
				} else {
					$paciente_obj = $dbPacientes->getExistepaciente3($id_paciente);
					$tipo_doc_tercero = $paciente_obj["codigo_doc_siesa"];
					$num_doc_tercero = $paciente_obj["numero_documento"];
					
					$resultado_aux = $classTercerosSiesa->crearTerceroCliente($id_paciente, $compania, $id_lugar, $id_usuario);
				}
				
				if ($resultado_aux == 1) {
		?>
        <input type="hidden" id="hdd_resul_tercero_siesa" name="hdd_resul_tercero_siesa" value="1" />
		<?php
				} else {
		?>
        <input type="hidden" id="hdd_resul_tercero_siesa" name="hdd_resul_tercero_siesa" value="-1" />
        <input type="hidden" id="hdd_mensaje_tercero_siesa" name="hdd_mensaje_tercero_siesa" value="<?= $resultado_aux ?>" />
		<?php
				}
				
				if ($resultado_aux == 1) {
					//Se obtienen los datos del usuario
					$usuario_obj = $dbUsuarios->getUsuario($id_usuario);
					
					//Se registra el anticipo en Siesa
					$resul_anticipo = $classAnticiposSiesa->crearAnticipo($compania, $co_ventas, $bodega_ventas, $num_doc_tercero,
							$usuario_obj["numero_documento"], $id_paciente, $observaciones_anticipo, $arr_medios_pago, $id_usuario, 
							$usuario_obj["id_usuario_siesa"]);
					
					$num_anticipo = "";
					if ($resul_anticipo == 1) {
						//Se consulta el número del recibo de caja asociado
						$num_anticipo = $classConsultasSiesa->consultarReciboCaja($compania, $num_doc_tercero);
		?>
        <input type="hidden" id="hdd_resul_anticipo_siesa" name="hdd_resul_anticipo_siesa" value="1" />
        <input type="hidden" id="hdd_num_recibo_caja_siesa" name="hdd_num_recibo_caja_siesa" value="<?= $num_anticipo ?>" />
        <?php
					} else {
		?>
        <input type="hidden" id="hdd_resul_anticipo_siesa" name="hdd_resul_anticipo_siesa" value="-1" />
        <input type="hidden" id="hdd_mensaje_anticipo_siesa" name="hdd_mensaje_anticipo_siesa" value="<?= $resul_anticipo ?>" />
        <?php
						$resul_anticipo = -1;
					}
					
					if ($resul_anticipo == 1) {
						$resultado = $dbAnticipos->registrar_anticipo($id_paciente, $id_lugar, $id_usuario_prof,
								$num_anticipo, $observaciones_anticipo, $id_tercero, $arr_medios_pago, $id_usuario);
					}
				}
		?>
        <input type="hidden" id="hdd_id_anticipo_resul" name="hdd_id_anticipo_resul" value="<?php echo($resultado); ?>" />
        <?php
			}
			break;
			
		case "9": //Combo de municipios
			@$cod_dep = $utilidades->str_decode($_POST["cod_dep"]);
			@$cod_mun = $utilidades->str_decode($_POST["cod_mun"]);
			
			$lista_municipios = $dbDepMuni->getMunicipiosDepartamento($cod_dep);
			$combo->getComboDb("cmb_municipio", $cod_mun, $lista_municipios, "cod_mun_dane, nom_mun", "Seleccione el municipio", "", true, "width: 240px;");
			break;
			
		case "10": //Validación de existencia de pacientes
			@$numero_documento = $utilidades->str_decode($_POST["numero_documento"]);
			@$id_tipo_documento = $utilidades->str_decode($_POST["id_tipo_documento"]);
			
			$paciente_obj = $dbPacientes->getVerificacionPost($numero_documento, $id_tipo_documento);
			
			if (isset($paciente_obj["id_paciente"])) {
		?>
        <input type="hidden" id="hdd_id_paciente_b" value="<?php echo($paciente_obj["id_paciente"]); ?>" />
        <input type="hidden" id="hdd_tipo_documento_b" value="<?php echo($paciente_obj["tipodocumento"]); ?>" />
        <input type="hidden" id="hdd_nombre_1_b" value="<?php echo($paciente_obj["nombre_1"]); ?>" />
        <input type="hidden" id="hdd_nombre_2_b" value="<?php echo($paciente_obj["nombre_2"]); ?>" />
        <input type="hidden" id="hdd_apellido_1_b" value="<?php echo($paciente_obj["apellido_1"]); ?>" />
        <input type="hidden" id="hdd_apellido_2_b" value="<?php echo($paciente_obj["apellido_2"]); ?>" />
        <input type="hidden" id="hdd_direccion_b" value="<?php echo($paciente_obj["direccion"]); ?>" />
        <input type="hidden" id="hdd_telefono_1_b" value="<?php echo($paciente_obj["telefono_1"]); ?>" />
        <input type="hidden" id="hdd_telefono_2_b" value="<?php echo($paciente_obj["telefono_2"]); ?>" />
        <input type="hidden" id="hdd_id_pais_b" value="<?php echo($paciente_obj["id_pais"]); ?>" />
        <input type="hidden" id="hdd_cod_dep_b" value="<?php echo($paciente_obj["cod_dep"]); ?>" />
        <input type="hidden" id="hdd_cod_mun_b" value="<?php echo($paciente_obj["cod_mun"]); ?>" />
        <input type="hidden" id="hdd_nom_dep_b" value="<?php echo($paciente_obj["nom_dep"]); ?>" />
        <input type="hidden" id="hdd_nom_mun_b" value="<?php echo($paciente_obj["nom_mun"]); ?>" />
        <input type="hidden" id="hdd_edad_b" value="<?php echo($paciente_obj["edad"]); ?>" />
        <?php
			}
			break;
			
		case "11": //Anular un anticipo
			@$id_anticipo = $utilidades->str_decode($_POST["id_anticipo"]);
			@$observaciones_anticipo = $utilidades->str_decode($_POST["observaciones_anticipo"]);
			@$id_causal_borra = $utilidades->str_decode($_POST["id_causal_borra"]);
			
			$resultado = $dbAnticipos->anular_anticipo($id_anticipo, $observaciones_anticipo, $id_causal_borra, $id_usuario);
		?>
        <input type="hidden" id="hdd_resul_anular_anticipo" value="<?php echo($resultado); ?>" />
        <?php
			break;
			
		case "12"://Imprime formulario flotante de búsqueda de terceros
		?>
		<div class="encabezado">
			<h3>Agregar terceros</h3>
		</div>
		<div>
			<form id="frm_listado_terceros" name="frm_listado_terceros">
				<table style="width: 100%;">
					<tbody>
                    	<tr valign="middle">
							<td align="center" colspan="2">
								<div id="advertenciasg"></div>
							</td>
						</tr>
						<tr>
							<td>
								<input type="text" id="txt_parametro_terceros" name="txt_parametro_terceros" placeholder="N&uacute;mero de documento o nombre del tercero" onblur="trim_cadena(this);" />
							</td>
							<td style="width: 8%;">
								<input type="submit" value="Buscar" class="btnPrincipal peq" onclick="buscar_terceros_pago();" />
							</td>
						</tr>
					</tbody>
                </table>
			</form>
			<div id="d_resultado_b_terceros"></div>
		</div>    
		<?php
			break;
			
		case "13": //Resultados de búsqueda de terceros
			@$parametro_terceros = $utilidades->str_decode($_POST["parametro_terceros"]);
			
			$lista_terceros = $dbTerceros->getListasTercerosParametro($parametro_terceros, "1");
		?>
		<table class="paginated modal_table" style="width:80%; margin:auto;">
			<thead>
				<tr>
					<th style="width:15%;">Tipo de documento</th>
					<th style="width:20%;">N&uacute;mero de documento</th>
					<th style="width:65%;">Nombre</th>
				</tr>
			</thead>
			<?php
				if (count($lista_terceros) > 0) {
					foreach ($lista_terceros as $tercero_aux) {
			?>
			<tr onclick="agregar_tercero(<?php echo($tercero_aux["id_tercero"]); ?>, '<?php echo($tercero_aux["nombre_tercero"]); ?>');">
				<td align="center"><?php echo($tercero_aux["tipo_documento"]); ?></td>
				<td align="center">
					<?php
						$numero_documento_aux = $tercero_aux["numero_documento"];
						if ($tercero_aux["numero_verificacion"] != "") {
							$numero_documento_aux .= "-".$tercero_aux["numero_verificacion"];
						}
						echo($numero_documento_aux);
					?>
                </td>
				<td align="left"><?php echo($tercero_aux["nombre_tercero"]); ?></td>
			</tr>
			<?php
					}
				} else {
			?>
			<tr>
				<td colspan="3">No se hallaron terceros</td>
			</tr>
			<?php
				}
			?>
		</table>
        <br />
        <script id="ajax">
			//<![CDATA[ 
			$(function() {
				$(".paginated", "table").each(function(i) {
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
        <div style="text-align:center;">
        	<input type="button" id="btn_nuevo_tercero" value="Nuevo tercero" class="btnPrincipal peq" onclick="cargar_formulario_nuevo_tercero();" />
        </div>
        <br />
		<?php
			break;
	}
?>
