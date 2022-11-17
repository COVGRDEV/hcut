<?php
	header("Content-Type: text/xml; charset=UTF-8");
	
	session_start();
	
	require_once("../db/DbPagos.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbTiposPago.php");
	require_once("../db/DbListasPrecios.php");
	require_once("../db/DbPlanes.php");
	require_once("../db/DbConvenios.php");
	require_once("../db/DbVariables.php");
	require_once("../db/DbPaises.php");
	require_once("../db/DbDepartamentos.php");
	require_once("../db/DbDepMuni.php");
	require_once("../db/DbPacientes.php");
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbFormatosFacturas.php");
	require_once("../db/DbTerceros.php");
	require_once("../db/DbAsignarCitas.php");
	require_once("../db/DbMaestroProcedimientos.php");
	require_once("../db/DbMaestroInsumos.php");
	require_once("../db/DbAnticipos.php");
	require_once("../db/DbPaquetesProcedimientos.php");
	require_once("../db/DbDatosEntidad.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/Class_Conector_Siesa.php");
	require_once("../funciones/Class_Terceros_Siesa.php");
	require_once("../funciones/Class_Facturas_Siesa.php");
	require_once("../funciones/Class_Pedidos_Siesa.php");
	require_once("../funciones/Class_Notas_Credito_Siesa.php");
	require_once("../funciones/Class_Consultas_Siesa.php");
	
	
	$dbPagos = new DbPagos();
	$dbListas = new DbListas();
	$dbTiposPago = new DbTiposPago();
	$dbListasPrecios = new DbListasPrecios();
	$dbPlanes = new DbPlanes();
	$dbConvenios = new DbConvenios();
	$dbVariables = new Dbvariables();
	$dbPaises = new DbPaises();
	$dbDepartamentos = new DbDepartamentos();
	$dbDepMuni = new DbDepMuni();
	$dbPacientes = new DbPacientes();
	$dbUsuarios = new DbUsuarios();
	$dbFormatosFacturas = new DbFormatosFacturas();
	$dbTerceros = new DbTerceros();
	$dbAsignarCitas = new DbAsignarCitas();
	$dbMaestroProcedimientos = new DbMaestroProcedimientos();
	$dbMaestroInsumos = new DbMaestroInsumos();
	$dbAnticipos = new DbAnticipos();
	$dbPaquetesProcedimientos = new DbPaquetesProcedimientos();
	$dbDatosEntidad = new DbDatosEntidad();
	$contenido_html = new ContenidoHtml();
	$funcionesPersona = new FuncionesPersona();
	$combo = new Combo_Box();
	$utilidades = new Utilidades();
	$conectorSiesa = new Class_Conector_Siesa();
	$classTercerosSiesa = new Class_Terceros_Siesa();
	$classFacturasSiesa = new Class_Facturas_Siesa();
	$classPedidosSiesa = new Class_Pedidos_Siesa();
	$classNotasCreditoSiesa = new Class_Notas_Credito_Siesa();
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
		case "1": //Muestra el formulario de creación/edición de pagos
			@$parametro = $utilidades->str_decode($_POST["parametro"]);
			@$id_admision = $utilidades->str_decode($_POST["id_admision"]);
			@$ind_pago_auto = intval($_POST["ind_pago_auto"], 10);
			@$ind_crear = intval($_POST["ind_crear"], 10);
			@$id_pago = $utilidades->str_decode($_POST["id_pago"]);
			
			//Se verifica si el usuario tiene permisos para modificar la definición del pago o de registrar pagos
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
	
			$resultado1_aux = array();
			if ($ind_crear == 0) {
				$resultado1_aux = $dbPagos->pagosPendientes($parametro, $id_admision, 0, $id_pago);
			}
			$admision = "";
	
			if (count($resultado1_aux) <= 0 && $ind_crear == 0) {
		?>
        <legend>No se hallaron pagos</legend>
        <?php
			} else {
				if (count($resultado1_aux) <= 1) {
					//Se busca la fecha actual
					$arr_fecha_actual = $dbVariables->getFechaactual();
					$fecha_actual = $arr_fecha_actual["fecha_actual"];
					$fecha_pago="";
					$num_mipress="";
					$num_ent_mipress="";
					$num_poliza="";
					//Edición de un pago o creación
					$value = array();
					$lista_pagos_det_medios = array();
					$ind_pasado = false;
					if ($ind_crear == 0) {
						$value = $resultado1_aux[0];
						
						//Se busca el detalle de medios de pago
						$lista_pagos_det_medios = $dbPagos->getListaPagosDetMedios($value["id_pago"]);
						$id_pago = $value["id_pago"];
						
						$fecha_pago = substr($value["fecha_pago"], 0, 10);
						
						$num_mipress = $value["num_mipress"];
						$num_ent_mipress = $value["num_ent_mipress"];
						$num_poliza = $value["num_poliza"];
						
						if ($fecha_pago != "" && $fecha_pago < $fecha_actual) {
							//$ind_pasado = true;
						}
					} else {
						$fecha_pago = $fecha_actual;
						$lista_tipos_documento = $dbListas->getListaDetalles(2);
						$lista_paises = $dbPaises->getPaises();
						$lista_departamentos = $dbDepartamentos->getDepartamentos();
						$lista_municipios = $dbDepMuni->getMunicipiosDepartamento($cod_dep);
					}
					
					//Se verifica el día inicial del mes para número de factura obligatorio
					$variable_obj = $dbVariables->getVariable(13);
					$dia_limite = intval($variable_obj["valor_variable"], 10);
					
					//Se obtiene el día de la fecha actual
					$arr_aux = explode("-", $fecha_actual);
					$dia_actual = intval($arr_aux[2], 10);
					
					$ind_factura_obligatoria = 0;
					if ($dia_actual >= $dia_limite) {
						$ind_factura_obligatoria = 1;
					}
					
					//Se obtiene el identificador del convenio NP
					$variable_obj = $dbVariables->getVariable(14);
					$cadena_convenios_np = ";".$variable_obj["valor_variable"].";";
					
					//Se obtiene el lugra en el que ingresó el usuario
					$id_lugar_usuario = $_SESSION["idLugarUsuario"];
					
					$contadorid_aux = 1;
			?>
            <input type="hidden" id="hdd_fecha_pago" value="<?php echo($fecha_pago); ?>" />
            <input type="hidden" id="hdd_fact_obl" value="<?php echo($ind_factura_obligatoria); ?>" />
            <input type="hidden" id="hdd_convenios_np" value="<?php echo($cadena_convenios_np); ?>" />
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
                            <b><?php echo($value["tipo_documento"]); ?></b>
                            <?php
								} else {
									$combo->getComboDb("cmb_tipo_documento", "", $lista_tipos_documento, "id_detalle, nombre_detalle", "Seleccione el tipo de documento", "", true, "width: 240px;");
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
                            <b><?php echo($value["numero_documento"]); ?></b>
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
                            <b><?php echo($funcionesPersona->obtenerNombreCompleto($value["nombre_1"], $value["nombre_2"], $value["apellido_1"], $value["apellido_2"])); ?></b>
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
                            <b><?php echo($value["edad"]." a&ntilde;os"); ?></b>
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
                            <b><?php echo($value["direccion"]); ?></b>
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
                            <b><?php echo($value["nombre_pais"]); ?></b>
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
										if (trim($value["nom_dep_t"]) != "") {
											$departamento_aux = $value["nom_dep_t"];
										} else {
											$departamento_aux = $value["nom_dep"];
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
										if (trim($value["nom_mun_t"]) != "") {
											$municipio_aux = $value["nom_mun_t"]." (".$value["cod_mun_dane"].")";
										} else {
											$municipio_aux = $value["nom_mun"];
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
									$telefono_aux = $value["telefono_1"];
									if (trim($value["telefono_2"]) != "") {
										$telefono_aux .= " - ".$value["telefono_2"];
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
                    <tr>
                    	<td align="right">
							Correo*:
						</td>
                        <td align="left">
							<?php
                            	if ($ind_crear == 0) {
									$email = $value["email"];									
							?>
                            <b><?php echo($email); ?></b>
                            <?php
								} else {
							?>
                            	<input type="text" id="txt_email" value="" maxlength="" style="width:240px; display:inline;" onblur="trim_cadena(this);" />
                            <?php
								}
							?>
						</td>
                    </tr>
                    <?php
						if (isset($value["num_carnet"]) && $value["num_carnet"] != "") {
					?>
                    <tr>
                        <td align="right">
                            Carnet No.:
                        </td>
                        <td align="left">
                            <b><?php echo($value["num_carnet"]); ?></b>
                        </td>
                    </tr>
                    <?php
						}
					?>
                </table>
            </fieldset>
            <div style="margin-bottom: 30px;" id="a<?php echo($contadorid_aux); ?>"> 
                <table style="width:100%; margin:auto;" border="0">
                    <tr>
                        <td style="text-align:right; width:20%;">
                            <label class="inline no-margin">Sede:&nbsp;</label>
                        </td>
                        <td style="width:30%; text-align:left;">
                            <?php
								$id_lugar_cita_aux = "";
								if ($ind_crear == 0) {
									$id_lugar_cita_aux = $value["id_lugar_cita"];
								} else if ($id_lugar_usuario != "999") {
									$id_lugar_cita_aux = $id_lugar_usuario;
								}
								
								if ($ind_crear != 0 || $ind_modificar_pagos == 1) {
									$combo->getComboDb("cmb_lugar_cita".$contadorid_aux, $id_lugar_cita_aux, $dbListas->getListaDetalles(12), "id_detalle, nombre_detalle", "--Seleccione la sede--", "", !$ind_pasado, "width:200px; margin:0;");
								} else {
									$lugar_cita_obj = $dbListas->getDetalle($id_lugar_cita_aux);
							?>
                            <input type="hidden" id="cmb_lugar_cita<?php echo($contadorid_aux); ?>" name="cmb_lugar_cita<?php echo($contadorid_aux); ?>" value="<?php echo($id_lugar_cita_aux); ?>" />
                            <label class="inline no-margin"><b><?php echo($lugar_cita_obj["nombre_detalle"]); ?></b></label>
                            <?php
								}
							?>
                        </td>
                        <td style="text-align:right; width:20%;">
                            <label class="inline no-margin">Profesional que atiende:&nbsp;</label>
                        </td>
                        <td style="width:30%; text-align:left;">
                            <?php
								$id_usuario_prof_aux = "0";
								if ($ind_crear == 0) {
									$id_usuario_prof_aux = $value["id_usuario_prof"];
								}
								//El código de abajo se debe quitar lo antes posible.
								$ind_pago_autorizaciones_obser = false;
								if($ind_pago_auto==0 && $value["observaciones_pago"] === "Pago generado desde autorizaciones"){
									$ind_pago_autorizaciones_obser=true;
								}
								//El código de arriba de se debe quitar lo antes posible.
								
								if ($ind_crear != 0 || $ind_modificar_pagos == 1 || $ind_pago_auto==1 || $ind_pago_autorizaciones_obser) {
									$lista_usuarios_aux = $dbAsignarCitas->getListaUsuariosCitas("", -1);
									
									$combo->getComboDb("cmb_usuario_prof".$contadorid_aux, $id_usuario_prof_aux, $lista_usuarios_aux, "id_usuario,nombre_completo", "--Seleccione el profesional--", "", true, "width:200px;", "", "no-margin");
								} else {
									$usuario_obj = $dbUsuarios->getUsuario($id_usuario_prof_aux);
							?>
                            <input type="hidden" id="cmb_usuario_prof<?php echo($contadorid_aux); ?>" name="cmb_usuario_prof<?php echo($contadorid_aux); ?>" value="<?php echo($id_usuario_prof_aux); ?>" />
                            <label class="inline no-margin"><b><?php echo($usuario_obj["nombre_usuario"]." ".$usuario_obj["apellido_usuario"]); ?></b></label>
                            <?php
								}
							?>
                        </td>
                    </tr>
                    <tr>
                            <td style="text-align:right;">
                                <label class="inline no-margin">Convenio:&nbsp;</label>
                            </td>
                            <td style="text-align:left;">
                                <?php
                                    $id_convenio_aux = "";
                                    if ($ind_crear == 0) {
                                        $id_convenio_aux = $value["id_convenio"];
                                    }
                                    
                                    $convenio_obj = $dbConvenios->getConvenio($id_convenio_aux);
                                ?>
                                <input type="hidden" id="hdd_ind_num_aut" name="hdd_ind_num_aut" value="<?php echo($convenio_obj["ind_num_aut"]); ?>" />
                                <input type="hidden" id="hdd_ind_num_aut_obl" name="hdd_ind_num_aut_obl" value="<?php echo($convenio_obj["ind_num_aut_obl"]); ?>" />
                                <?php
                                    if ($ind_crear != 0 || $ind_modificar_pagos == 1) {
                                        $combo->getComboDb("cmb_convenio".$contadorid_aux, $id_convenio_aux, $dbConvenios->getListaConveniosActivos(), "id_convenio, nombre_convenio", "Seleccione el convenio", "cambiarConvenio(".$contadorid_aux.");", !$ind_pasado, "width: 200px;margin:0;");
                                    } else {
                                ?>
                                <input type="hidden" id="cmb_convenio<?php echo($contadorid_aux); ?>" name="cmb_convenio<?php echo($contadorid_aux); ?>" value="<?php echo($id_convenio_aux); ?>" />
                                <label class="inline no-margin"><b><?php echo($convenio_obj["nombre_convenio"]); ?></b></label>
                                <?php
                                    }
                                ?>
                            </td>
                            <td style="text-align:right;">
                                <label class="inline no-margin">Plan:&nbsp;</label>
                            </td>
                            <td style="text-align:left;">
                                <div id="comboPlan">
                                    <?php
                                        $id_plan_aux = "";
                                        if ($ind_crear == 0) {
                                            $id_plan_aux = $value["id_plan"];
                                        }
                                        
                                        if ($ind_crear != 0 || $ind_modificar_pagos == 1) {
                                            $combo->getComboDb("cmb_plan".$contadorid_aux, $id_plan_aux, $dbPlanes->getListaPlanesActivos($id_convenio_aux), "id_plan, nombre_plan", "Seleccione el plan", "cambiarPlan('cmb_plan".$contadorid_aux."', '".$contadorid_aux."');", !$ind_pasado, "width:200px; margin:0;");
                                        } else {
                                            $plan_obj = $dbPlanes->getPlan($id_plan_aux);
                                    ?>
                                    <input type="hidden" id="cmb_plan<?php echo($contadorid_aux); ?>" name="cmb_plan<?php echo($contadorid_aux); ?>" value="<?php echo($id_plan_aux); ?>" />
                                    <label class="inline" style="margin: 0;"><b><?php echo($plan_obj["nombre_plan"]); ?></b></label>
                                    <?php
                                        }
                                    ?>
                                </div>
                                <div id="d_cambiar_plan" style="display:none;"></div>
                            </td>
                        </tr>
                        <?php 
						
						$display = intval($convenio_obj["ind_num_carnet"]) == 1 ? "table-cell" : "none";
						$disabled = $ind_crear == 0 ? "disabled" : ""; 
						
						if ($ind_crear != 0 || $ind_modificar_pagos == 1) { 
						
						?>
                        <tr>
                            <td id="td_num_mipress_l" style="text-align:right; display:<?= $display ?>" >
                                <label class="inline no-margin">No. Mipress:&nbsp;</label>
                            </td>
                            <td id="td_num_mipress" style="text-align:left; display:<?= $display ?>">
                                <input <?= $disabled ?> style="width: 200px; margin:0;" type="text" id="txt_num_mipress" placeholder="<?=$num_mipress ?>" name="txt_num_mipress" value="<?=$num_mipress ?>" />                             
                            </td>
                            <td id="td_num_ent_mipress_l" style="text-align:right; display:<?= $display ?>">
                                <label class="inline no-margin">No. Entrega Mipress:&nbsp;</label>
                            </td>
                            <td id="td_num_ent_mipress" style="text-align:left; display:<?= $display ?>">
                                <input <?= $disabled ?> style="width: 200px; margin:0;" type="text" id="txt_num_ent_mipress" placeholder="<?=$num_ent_mipress ?>" name="txt_num_ent_mipress" value="<?= $num_ent_mipress  ?>" />
                            </td>
                        <tr>
                        <tr>
                        	<td id="td_num_poliza_l" style="text-align:right; display:<?= $display ?>">
                                <label class="inline no-margin">No. P&oacute;liza:&nbsp;</label>
                            </td>
                            <td id="td_num_poliza" style="text-align:left; display:<?= $display ?>">
                                <input <?= $disabled ?> style="width: 200px; margin:0;" type="text" id="txt_num_poliza"  placeholder="<?=$num_poliza ?>" name="txt_num_poliza" value="<?= $num_poliza ?>" />                             
                            </td>
                        </tr>
                    <?php }else{ ?>
                    		<input  type="hidden" id="txt_num_mipress" name="txt_num_mipress" value="<?=$num_mipress ?>" /> 	
							<input  type="hidden" id="txt_num_ent_mipress" name="txt_num_ent_mipress" value="<?= $num_ent_mipress  ?>" />
                            <input  type="hidden" id="txt_num_poliza" name="txt_num_poliza" value="<?= $num_poliza ?>" /> 
					<?php } ?>
                    
                   
                </table>
                <table style="width: 100%;">
                    <tr>
                        <td align="left" style="font-size:10pt;">
                            <?php
								if ($ind_crear == 0) {
							?>
                            <b>N&uacute;mero de pago: <?php echo($value["id_pago"]); ?></b>
                            <?php
									if ($value["estado_pago"] == "1") {
							?>
                            &nbsp;-&nbsp;<span style="color:#F00;"><b>PENDIENTE</b></span>
                            <?php
									} else {
										if ($value["fecha_hora_pago_t"] != "") {
							?>
                            &nbsp;-&nbsp;<b>REGISTRADO POR <?php echo($value["nombre_usuario_pago"]." ".$value["apellido_usuario_pago"]." (".$value["fecha_hora_pago_t"].")"); ?></b>
                            <?php
										}
										
										if ($value["estado_pago"] == "3") {
							?>
                            <br /><span style="color:#F00;"><b>BORRADO POR <?php echo($value["nombre_usuario_borra"]." ".$value["apellido_usuario_borra"]." (".$value["fecha_borra_t"].")"); ?></b></span>
                            <?php
										}
									}
									
									if ($value["id_admision"] != "") {
							?>
                            <br />
                            <b>N&uacute;mero de admisi&oacute;n: <?php echo($value["id_admision"]); ?> - REGISTRADA POR <?php echo($value["nombre_usuario_admision"]." ".$value["apellido_usuario_admision"]." (".$value["fecha_crea_admision_t"].")"); ?></b>
                            <br />
                            <b>Tipo de cita: <?php echo($value["nombre_tipo_cita"]); ?></b>
                            <?php
									} else {
							?>
                            <br />
                            <b>Solicitud - REGISTRADA POR <?php echo($value["nombre_usuario_crea"]." ".$value["apellido_usuario_crea"]." (".$value["fecha_crea_t"].")"); ?></b>
                            <?php
									}
								}
							?>
                        </td>
                    </tr>
                </table>
                <?php
					$resultado2_aux = array();
					if ($ind_crear == 0) {
						$resultado2_aux = $dbPagos->getListaPagosDetalle($value["id_pago"]);
					}
					
					if (isset($value["id_plan"])) {
						$plan_obj = $dbPlanes->getPlan($value["id_plan"]);
						$ind_tipo_pago = $plan_obj["ind_tipo_pago"];
					} else {
						$plan_obj = array();
						$ind_tipo_pago = "0";
					}
					$valor_cuota_total = 0;
				?>
                <div id="d_liq_cx" style="display:none;"></div>
                <input type="hidden" name="hdd_cant_productos" id="hdd_cant_productos" value='<?php echo(count($resultado2_aux)); ?>' />
                 <input type="hidden" name="hdd_cant_productos_aux" id="hdd_cant_productos_aux" value='<?php echo(count($resultado2_aux)); ?>' />
                  <input type="hidden" name="hdd_estado_pago" id="hdd_estado_pago" value='<?php echo($value["estado_pago"]); ?>' />
                <table  class="modal_table" id="tablaPrecios<?php echo($contadorid_aux); ?>" style="width:100%;">
 						<?php
                        //echo($value["estado_pago"]);
                        ?>
                    <tr id="tabla_encabezado">
                        <th style="width:7%;">C&oacute;digo</th>
                        <th style="width:29%;">Nombre</th>
                        <th style="width:10%;">Tipo de valor</th>
                        <th style="width:12%;">N&uacute;mero autorizaci&oacute;n</th>
                        <th style="width:6%;">Cant.</th>
                        <th style="width:10%;">Valor unitario</th>
                        <th style="width:11%;">Total</th>
                        <th style="width:10%;">Cuota moderadora / Copago</th>
                        <th style="width:5%;">
                            <?php
								if ($ind_crear != 0 || $ind_modificar_pagos == 1) {
							?>
                            <img src="../imagenes/Add-icon.png" onclick="mostrar_agregar_servicio(<?php echo($contadorid_aux); ?>);"/>
                            <?php
								}
							?>
                        </th>
                    </tr>
                    <?php
						$total = 0;
						$cont_aux = 0;
						foreach ($resultado2_aux as $value2) {
							$codigo_aux = "";
							if ($value2["tipo_precio"] == "P") {
								$codigo_aux = $value2["cod_procedimiento"];
							} else if ($value2["tipo_precio"] == "M") {
								$codigo_aux = $value2["cod_medicamento"];
							} else if ($value2["tipo_precio"] == "I") {
								$codigo_aux = $value2["cod_insumo"];
							}
					?>
                    <tr id="tr_producto_<?php echo($cont_aux); ?>">
                        <td align="center">
                            <input type="hidden" name="hdd_detalle_precio_<?php echo($cont_aux); ?>" id="hdd_detalle_precio_<?php echo($cont_aux); ?>" value="<?php echo($value2["id_detalle_precio"]); ?>" />
                            <input type="hidden" name="hdd_cod_servicio_<?php echo($cont_aux); ?>" id="hdd_cod_servicio_<?php echo($cont_aux); ?>" value="<?php echo($codigo_aux); ?>" />
                            <input type="hidden" name="hdd_tipo_precio_<?php echo($cont_aux); ?>" id="hdd_tipo_precio_<?php echo($cont_aux); ?>" value="<?php echo($value2["tipo_precio"]); ?>" />
                            <input type="hidden" name="hdd_tipo_bilateral_<?php echo($cont_aux); ?>" id="hdd_tipo_bilateral_<?php echo($cont_aux); ?>" value="<?php echo($value2["tipo_bilateral"]); ?>" />
                            <?php echo($codigo_aux); ?>
                        </td>
                        <td align="left">
                            <?php
								if ($value2["tipo_precio"] == "P") {
									echo($value2["nombre_procedimiento"]);
								} else if ($value2["tipo_precio"] == "M") {
									echo($value2["nombre_generico"]);
								} else if ($value2["tipo_precio"] == "I") {
									echo($value2["nombre_insumo"]);
								}
							?>
                        </td>
                        <td align="center">
                            <?php
								foreach ($arr_bilateralidades as $bilateralidad_aux) {
									if ($bilateralidad_aux["id"] == $value2["tipo_bilateral"]) {
										echo($bilateralidad_aux["valor"]);
										break;
									}
								}
							?>
                        </td>
                        <td align="left">
                            <input type="text" name="txt_num_autorizacion_<?php echo($cont_aux); ?>" id="txt_num_autorizacion_<?php echo($cont_aux); ?>" value="<?php echo($value2["num_autorizacion"]); ?>" maxlength="20" onblur="trim_cadena(this);" class="no-margin" style="font-size:12pt;" <?php if ($convenio_obj["ind_num_aut"] != "1" || $ind_pasado || $ind_tipo_pago != "1" || $ind_modificar_pagos == 0) { ?>disabled<?php } ?> />
                        </td>
                        <td>
                            <input type="text" style="text-align:center; font-size:12pt;" id="txt_cantidad_<?php echo($cont_aux); ?>" name="txt_cantidad_<?php echo($cont_aux); ?>" value="<?php echo($value2["cantidad"]); ?>" onkeypress="solo_numeros(event, false);" onblur="validar_cantidades(<?php echo($cont_aux); ?>, 2); registrar_cambios_totales();" maxlength="4" class="no-margin" <?php if ($ind_pasado || $ind_modificar_pagos == 0) { ?>disabled<?php } ?> />
                        </td>
                        <td>
                            <input type="text" style="text-align:right; font-size:12pt;" id="txt_valor_<?php echo($cont_aux); ?>" name="txt_valor_<?php echo($cont_aux); ?>" value="<?php echo($value2["valor"]); ?>" onkeypress="solo_numeros(event, false);" onblur="validar_cantidades(<?php echo($cont_aux); ?>, 1); registrar_cambios_totales();" maxlength="9" class="no-margin" <?php if ($ind_pasado || $ind_modificar_pagos == 0) { ?>disabled<?php } ?> />
                        </td>
                        <td id="td_valor_total_<?php echo($cont_aux); ?>">
                            <?php echo("$".number_format($value2["total"])); ?>
                        </td>
                        <td>
                            <input type="text" style="text-align:right; font-size:12pt;" id="txt_valor_cuota_<?php echo($cont_aux); ?>" name="txt_valor_cuota_<?php echo($cont_aux); ?>" value="<?php echo($value2["valor_cuota"]); ?>" onkeypress="solo_numeros(event, false);" onblur="registrar_cambios_totales();" maxlength="9" class="no-margin" <?php if ($ind_pasado || $ind_tipo_pago != "1" || $ind_modificar_pagos == 0) { ?>disabled<?php } ?> />
                        </td>
                        <td>
                            <?php
								if (!$ind_pasado && $ind_modificar_pagos == 1) {
							?>
                            <img src="../imagenes/icon-error.png" onclick="eliminar_producto(<?php echo($cont_aux); ?>);" />
                            <?php
								}
							?>
                        </td>
                    </tr>
                    <?php
							$admision = $value2["id_admision"];
							$total += $value2["total"];
							$valor_cuota_total += intval($value2["valor_cuota"]) * intval($value2["cantidad"], 10);
							$cont_aux++;
						}
					?>
                </table>
                <p style="font-size:11pt; text-align:right; margin-right:10px;">
                    Total: <b>$<span id="sp_total_pagar"><?php echo(str_replace(",", ".", number_format($total))); ?></span></b>
                    <br />
                    Total copago/cuota moderadora: <b>$<span id="sp_total_cuota"><?php echo(str_replace(",", ".", number_format($valor_cuota_total))); ?></span></b>
                    <br />
                    Diferencia: <b>$<span id="sp_total_diferencia"><?php echo(str_replace(",", ".", number_format($total - $valor_cuota_total))); ?></span></b>&nbsp;
                </p>
                <div style="width:800px; margin:auto;">
                    <table border="0" cellpadding="5" cellspacing="0" style="width: 100%;">
                        <tr>
                            <td align="right" valign="top" style="width:17%;"><label>Observaciones</label></td>
                            <td align="left" colspan="5" style="width:83%;">
                                <?php
									$observaciones_pago_aux = "";
									if ($ind_crear == 0) {
										$observaciones_pago_aux = $value["observaciones_pago"];
									}
								?>
                                <textarea id="txt_observaciones_pago" class="textarea_ajustable" onblur="trim_cadena(this);"><?php echo($observaciones_pago_aux); ?></textarea>
                                <span style="width:13%;"></span>
                            </td>
                        </tr>
                        <tr>
                          	<?php /*<td align="right"><label>Entidad que factura*</label></td>
                            <td align="left">
                                <?php
									$entidad_aux = "";
									if ($ind_crear == 0) {
										$entidad_aux = $value["entidad"];
									}
									
									$combo->getComboDb("cmb_entidad".$contadorid_aux, $entidad_aux, $dbListas->getListaDetalles(23), "id_detalle, nombre_detalle", "Seleccione la entidad que factura", "", !$ind_pasado, "width: 200px;");
								?>
                            </td>*/ ?>
                            <td align="right"><label>N&uacute;mero de factura<?php /*if ($ind_factura_obligatoria == 1) { ?>*<?php }*/ ?></label></td>
                            <td align="left" style="width:13%;">
                                <?php
									$num_factura = $value["num_factura"];
									if ($ind_crear == 0) {
										if(isset($value["estado_pago"]) && $value["estado_pago"] == "1" && $tipo_acceso_menu == "2" && !$ind_pasado && $ind_modificar_pagos == 1) {	
								?>
                                <input  type="text" id="txt_factura" name="txt_factura"  value="<?php echo($num_factura); ?>" maxlength="20" onblur="trim_cadena(this);"  /> 
                                <?php 
										}else if(isset($value["estado_pago"]) && $value["estado_pago"] == "2" && $tipo_acceso_menu == "2" && $ind_modificar_pagos == 1 ){
								?>
                                <input style="color:#006600" type="text" id="txt_factura" name="txt_factura"  value="<?php echo($num_factura); ?>" maxlength="20" onblur="trim_cadena(this);" />
                                <?php
										
									}else{
								?>
                              <label id="lb_num_factura" class="verde"><?= ($num_factura != "" ? $num_factura : "-") ?></label>
                            </td>
                            <?php
									}
								}
							?>
                            
                            <td align="right" style="width:22%;"><label>N&uacute;mero de pedido</label></td>
                            <td align="left" style="width:13%;">
                                <?php
									$num_pedido_aux = $value["num_pedido"];
								
									if ($ind_crear == 0) {
										if (isset($value["estado_pago"]) && $value["estado_pago"] == "1" && $tipo_acceso_menu == "2"  && $ind_modificar_pagos == 1) {
											
								?>
                                			 <input  type="text" id="txt_pedido" name="txt_pedido"  value="<?php echo($num_pedido_aux); ?>" maxlength="20" onblur="trim_cadena(this);" onkeypress="solo_numeros(event, false);" /> 
                                <?php
											} else if (isset($value["estado_pago"]) && $value["estado_pago"] == "2" && $tipo_acceso_menu == "2"  && $ind_modificar_pagos == 1){
												?>
													 <input style="color:#006600;" type="text" id="txt_pedido" name="txt_pedido"  value="<?php echo($num_pedido_aux); ?>" maxlength="20" onblur="trim_cadena(this);" onkeypress="solo_numeros(event, false);" /> 
												<?php
											
										}else{
								?>
                               				 <label id="lb_num_pedido" class="verde"><?= ($num_pedido_aux != "" ? $num_pedido_aux : "-") ?></label>
                                  <?php
										}
									}
								?>
                                
                            </td>
                            <?php
                        		if (isset($value["estado_pago"]) && $value["estado_pago"] == "3") {
							?>
							<td align="right" style="width:22%;"><label>N&uacute;mero de nota cr&eacute;dito</label></td>
							<td align="left" style="width:13%;">
                            	<?php
                                	$num_nota_credito_aux = "";
									if ($ind_crear == 0) {
										$num_nota_credito_aux = $value["num_nota_credito"];
									}
								?>
                                <label id="lb_num_nota_credito" class="verde"><?= ($num_nota_credito_aux != "" ? $num_nota_credito_aux : "-") ?></label>
							</td>
	                          <?php
								} else if(isset($value["estado_pago"]) && $value["estado_pago"] == "2" ) {
							?>
                                <td align="right" style="width:15%;"><label>Nota cr&eacute;dito</label></td>
							    <td align="left" style="width:13%;">
                            	<?php
                                	$num_nota_credito_aux = "";
									if ($ind_crear == 0) {
										$num_nota_credito_aux = $value["num_nota_credito"];
										if($tipo_acceso_menu == "2"  && $ind_modificar_pagos == 1){
											?>
											<input  type="text" id="txt_nota_credito" name="txt_nota_credito"  value="<?php echo($num_nota_credito_aux); ?>" placeholder="" maxlength="20" onblur="trim_cadena(this);" onkeypress="solo_numeros(event, false);" />
										<?php
										}else{
											?>
												<label id="lb_num_nota_credito" class="verde"><?= ($num_nota_credito_aux != "" ? $num_nota_credito_aux : "-") ?></label>
											
											  <?php
										}
									
									}
								?>
                                	
							</td>
                            <?php
								}else{
								?>
                                 <td align="right" style="width:22%;">
                                 <td align="left" style="width:13%;">
                                <?php
									
								}
							?>
                        </tr>
                    </table>
                    <div>
                       	<?php
							if (isset($value["id_pago"])) {
						?>
                        <input class="btnPrincipal peq" type="button" name="btnConsultarSiesa" id="btnConsultarSiesa" value="Consultar pago en SIESA" onclick="consultar_pago_siesa('<?= $value["id_pago"]; ?>', '<?= $value["id_lugar_cita"]; ?>', '<?= $value["numero_documento"];?>' );" />
                        <div id="d_tipo_pago_siesa" class="texto-rojo"></div>  
                        <?php
							}
						?>																															 
                       </div>

                    <br/>
                    <?php
						$lista_tipos_pagos = $dbTiposPago->getListaTiposPagoAct(1);
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
                    <input type="hidden" id="hdd_tipo_pago_id_tipo_concepto_<?php echo($tipo_pago_aux["id"]); ?>" value="<?php echo($tipo_pago_aux["id_tipo_concepto"]); ?>" />
                    <?php
						}
						
						//Se verifica si se trata de un pago nuevo con un plan con cuota moderadora
						if (!$ind_pasado && $ind_crear == 0 && count($resultado2_aux) > 0) {
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
						
						//Si hay cuota moderadora se calculan valores para medios de pago
						if (!$ind_pasado && $ind_crear == 0 && count($lista_pagos_det_medios) == 0 && $ind_tipo_pago == "1") {
							//Se agrega un valor de boleta
							$total_aux = $total - $valor_cuota_total;
							if ($total_aux > 0) {
								$arr_aux = array();
								$arr_aux["id_medio_pago"] = "0";
								$arr_aux["id_banco"] = "";
								$arr_aux["valor_pago"] = $total_aux;
								
								array_push($lista_pagos_det_medios, $arr_aux);
							}
							
							//Se agrega el valor de la cuota
							if ($valor_cuota_total > 0) {
								$arr_aux = array();
								$arr_aux["id_medio_pago"] = "";
								$arr_aux["id_banco"] = "";
								$arr_aux["valor_pago"] = $valor_cuota_total;
								
								array_push($lista_pagos_det_medios, $arr_aux);
							}
						}
						
						$total_aux = 0;
						
						$estado_pago = "1";
						if (isset($value["estado_pago"])) {
							$estado_pago = $value["estado_pago"];
						}
					?>
                    <table style="width:100%;" cellpadding="5" cellspacing="0">
                        <tr>
                            <td align="center" style="width:24%;">Medio de pago</td>
                            <td align="center" style="width:13%;">Valor</td>
                            <td align="center" style="width:42%;">Datos adicionales</td>
                            <td align="center" style="width:21%;">Autorizaci&oacute;n</td>
                        </tr>
                        <tr>
                       	    <td colspan="4"><hr class="no-margin" /></td>
                        </tr>
						<?php
							for ($i = 0; $i < count($lista_pagos_det_medios); $i++) {
								$pago_det_aux = $lista_pagos_det_medios[$i];
								$total_aux += floatval($pago_det_aux["valor_pago"]);
						?>
                        <tr id="tr_medio_pago_<?php echo($i); ?>">
                            <td>
                                <?php
									$combo->getComboDb("cmb_tipo_pago".$contadorid_aux."_".$i, $pago_det_aux["id_medio_pago"], $lista_tipos_pagos, "id, nombre", "&lt;Seleccione el tipo de pago&gt;", "tipoPago(".$contadorid_aux.", ".$i.", ".$id_pago.", ".$estado_pago."); validar_mostrar_datos_tercero(".$contadorid_aux.");", !$ind_pasado, "width:200px;", "", "no-margin");
								?>
                            </td>
                            <td>
                                <input type="text" disabled id="tipoPago<?php echo($contadorid_aux."_".$i); ?>" name="tipoPago<?php echo($contadorid_aux."_".$i); ?>" class="no-margin" value="<?php echo(floatval("0".$pago_det_aux["valor_pago"])); ?>" onkeypress="solo_numeros(event, false);" onblur="procesarTipoPago(<?php echo($contadorid_aux); ?>, <?php echo($i); ?>);" />
                            </td>
                            <td align="left">
                                <div id="d_banco_mp_<?= $i ?>" style="display:none;">
                                    Banco
                                    <?php
										$combo->getComboDb("cmb_banco".$contadorid_aux."_".$i, $pago_det_aux["id_banco"], $lista_bancos, "id_detalle, nombre_detalle", "&lt;Seleccione el banco&gt;", "seleccionarBanco('tipoPago".$contadorid_aux."_".$i."');", 1, "width:100%;", "", "no-margin");
									?>
                                </div>
                                <div id="d_franquicia_tc_mp_<?= $i ?>" style="display:none;">
                                    Franquicia
                                    <?php
										$combo->getComboDb("cmb_franquicia_tc_".$i, $pago_det_aux["id_franquicia_tc"], $lista_franquicias_tc, "id_detalle, nombre_detalle", "&lt;Seleccione la franquicia&gt;", "", 1, "width:100%;", "", "no-margin");
									?>
                                </div>
                                <div id="d_cheque_mp_<?= $i ?>" style="display:none;">
                                    N&uacute;mero cheque
                                    <input type="text" id="txt_num_cheque_<?= $i ?>" name="txt_num_cheque_<?= $i ?>" value="<?= $pago_det_aux["num_cheque"] ?>" maxlength="8" class="no-margin" onblur="convertirAMayusculas(this);" />
                                </div>
                                <div id="d_cuenta_mp_<?= $i ?>" style="display:none;">
                                    N&uacute;mero cuenta/tarjeta
                                    <input type="text" id="txt_num_cuenta_<?= $i ?>" name="txt_num_cuenta_<?= $i ?>" value="<?= $pago_det_aux["num_cuenta"] ?>" maxlength="25" class="no-margin" />
                                </div>
                                <div id="d_num_autoriza_mp_<?= $i ?>" style="display:none;">
                                    N&uacute;mero autorizaci&oacute;n
                                    <input type="text" id="txt_num_autoriza_<?= $i ?>" name="txt_num_autoriza_<?= $i ?>" value="<?= $pago_det_aux["num_autoriza"] ?>" maxlength="10" class="no-margin" />
                                </div>
                                <div id="d_fecha_vence_mp_<?= $i ?>" style="display:none;">
                                    Fecha vencimiento (aaaa/mm)
                                    <br />
                                    <input type="text" id="txt_ano_vence_<?= $i ?>" name="txt_ano_vence_<?= $i ?>" value="<?= $pago_det_aux["ano_vence"] ?>" maxlength="4" class="txt_no_block no-margin" style="width:70px;" onkeypress="return solo_numeros(event, false);" />
                                    &nbsp;/&nbsp;
                                    <input type="text" id="txt_mes_vence_<?= $i ?>" name="txt_mes_vence_<?= $i ?>" value="<?= $pago_det_aux["mes_vence"] ?>" maxlength="2" class="txt_no_block no-margin" style="width:50px;" onkeypress="return solo_numeros(event, false);" />
                                </div>
                                <div id="d_referencia_mp_<?= $i ?>" style="display:none;">
                                    Referencia
                                    <input type="text" id="txt_referencia_<?= $i ?>" name="txt_referencia_<?= $i ?>" value="<?= $pago_det_aux["referencia"] ?>" maxlength="30" class="no-margin" />
                                </div>
                                <div id="d_fecha_consigna_mp_<?= $i ?>" style="display:none;">
                                    Fecha consignaci&oacute;n (dd/mm/aaaa)
                                    <input type="text" name="txt_fecha_consigna_<?= $i ?>" id="txt_fecha_consigna_<?= $i ?>" value="<?= $pago_det_aux["fecha_consigna_t"] ?>" maxlength="10" class="no-margin" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" style="width:120px;" />
                                </div>
                                <div id="d_anticipos_mp_<?= $i ?>" style="display:none;">
                                </div>
                            </td>
                            <td>
                                <?php
									$combo->getComboDb("cmb_usuario_autoriza".$contadorid_aux."_".$i, $pago_det_aux["id_usuario_autoriza"], $lista_usuarios_autoriza, "id_usuario, nombre_completo", "&lt;Autorizado por&gt;", "", false, "width:100%;", "", "no-margin");
								?>
                            </td>
                        </tr>
                        <?php
								if (!$ind_pasado && $ind_crear == 0) {
						?>
                        <script id="ajax" type="text/javascript">
                            <?php
									if ($pago_det_aux["id_medio_pago"] != "") {
							?>
                            tipoPago(<?php echo($contadorid_aux); ?>, <?php echo($i); ?>, <?php echo($id_pago); ?>, <?php echo($estado_pago); ?>);
                            procesarTipoPago(<?php echo($contadorid_aux); ?>, <?php echo($i); ?>);
                            <?php
									}
							?>
                        </script>
                        <?php
								}
							}
							
							$cont_medios_pago = count($lista_pagos_det_medios);
							for ($i = count($lista_pagos_det_medios); $i < 10; $i++) {
								$display_aux = "none";
								if ($i == 0 && count($lista_pagos_det_medios) == 0) {
									$display_aux = "table-row";
									$cont_medios_pago++;
								}
						?>
                        <tr id="tr_medio_pago_<?php echo($i); ?>" style="display:<?php echo($display_aux); ?>;">
                            <td>
                                <?php
									$combo->getComboDb("cmb_tipo_pago".$contadorid_aux."_".$i, "", $lista_tipos_pagos, "id, nombre", "&lt;Seleccione el tipo de pago&gt;", "tipoPago(".$contadorid_aux.", ".$i.", ".$id_pago.", ".$estado_pago."); validar_mostrar_datos_tercero(".$contadorid_aux.");", !$ind_pasado, "width: 200px;", "", "no-margin");
								?>
                            </td>
                            <td>
                                <input type="text" disabled id="tipoPago<?php echo($contadorid_aux."_".$i); ?>" name="tipoPago<?php echo($contadorid_aux."_".$i); ?>" class="no-margin" value="0" onkeypress="solo_numeros(event, false);" onblur="procesarTipoPago(<?php echo($contadorid_aux); ?>, <?php echo($i); ?>);" /> 
                            </td>
                            <td align="left">
                                <div id="d_banco_mp_<?= $i ?>" style="display:none;">
                                    Banco
                                    <?php
										$combo->getComboDb("cmb_banco".$contadorid_aux."_".$i, "", $lista_bancos, "id_detalle, nombre_detalle", "&lt;Seleccione el banco&gt;", "seleccionarBanco('tipoPago".$contadorid_aux."_".$i."');", 1, "width:100%;", "", "no-margin");
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
                                <div id="d_anticipos_mp_<?= $i ?>" style="display:none;">
                                </div>
                            </td>
                            <td>
                                <?php
									$combo->getComboDb("cmb_usuario_autoriza".$contadorid_aux."_".$i, "", $lista_usuarios_autoriza, "id_usuario, nombre_completo", "&lt;Autorizado por&gt;", "", false, "width:100%;", "", "no-margin");
								?>
                            </td>
                        </tr>
                        <?php
							}
							
							if (!$ind_pasado) {
						?>
                        <tr>
                            <td colspan="5">
                                <div class="agregar_alemetos" onclick="agregar_medio_pago();" title="Agregar medio de pago"></div> 
                                <div class="restar_alemetos" onclick="restar_medio_pago(); validar_mostrar_datos_tercero(<?php echo($contadorid_aux); ?>);" title="Borrar medio de pago"></div>
                            </td>
                        </tr>
                        <?php
							}
						?>
                        <tr style="text-align:right; font-size:11pt;">
                            <td colspan="2">
                                Total: <b>$<span id="tipoPagoTotalPagar"><?php echo(str_replace(",", ".", number_format($total_aux))); ?></span></b>
                            </td>
                        </tr>
                    </table>
                    <input type="hidden" id="hdd_cont_medios_pago" value="<?php echo($cont_medios_pago); ?>" />
                    <?php
						$id_tercero = "";
						$nombre_tercero = "";
						$display_aux = "none";
						if ($ind_crear == 0) {
							$id_tercero = $value["id_tercero"];
							$nombre_tercero = $value["nombre_tercero"];
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
                                           <?php
										   
												if (isset($value["estado_pago"]) && $value["estado_pago"] <> "2" || !isset($value["estado_pago"])) {
											?>
                                        <div id="d_buscar_tercero" class="d_buscar" style="float:left;" onclick="mostrar_buscar_tercero(<?php echo($contadorid_aux); ?>);" title="Buscar tercero"></div>
                                        <img id="img_borrar_tercero" src="../imagenes/Error-icon.png" style="float:left;" onclick="borrar_tercero();" title="Quitar tercero" />
                                        
                                              <?php
												}
											   ?>
                                        <h6 style="float:left; width:85%;">
                                            <div id="d_nombre_tercero" style="text-align:left; width:100%;"><?php echo($nombre_tercero); ?></div>
                                        </h6>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                    <script id="ajax" type="text/javascript">
                        validar_mostrar_datos_tercero("<?php echo($contadorid_aux); ?>");
                    </script>
                    <table width="100%" cellpadding="5" cellspacing="0">
                    	<tr>
                        	<td align="right" style="width:20%"><label>Causal de anulaci&oacute;n:</label></td>
                            <td align="left" style="width:80%">
                            	<?php
									if (isset($value["estado_pago"]) && $value["estado_pago"] != "3" && $ind_modificar_pagos == 1) {
										$ind_activo_aux = 1;
									} else {
										$ind_activo_aux = 0;
									}
									
                                	$lista_causales = $dbListas->getListaDetalles(78, 1);
									
									if (isset($value["id_causal_borra"])) {
										$id_causal_borra = $value["id_causal_borra"];
									} else {
										$id_causal_borra = "";
									}
									$combo->getComboDb("cmb_causales_devolucion", $id_causal_borra, $lista_causales, "id_detalle, nombre_detalle", "--Seleccione la causal--", "", $ind_activo_aux, "width:400px; margin:0;");
								?>
                            </td>
                        </tr>
                    </table>
                </div>
                <br/>
                <?php
					if ($ind_crear == 0) {
						if ($tipo_acceso_menu == "2") {
							if (!$ind_pasado) {
								if (isset($value["estado_pago"]) && $value["estado_pago"] == "1") {
									if ($ind_modificar_pagos == 1) {
				?>
                <input class="btnPrincipal" type="button" name="btnRegistrarpagos" id="btnRegistrarpagos" value="Guardar cambios" onclick="validar_registrar_pago(<?php echo($contadorid_aux); ?>, 3);" />
                &nbsp;&nbsp;
                <?php
									}
									if ($ind_registrar_pagos == 1) {
				?>
                <input class="btnPrincipal" type="button" name="btnRegistrarpagos" id="btnRegistrarpagos" value="Registrar pago" onclick="validar_registrar_pago(<?php echo($contadorid_aux); ?>, 5);" />
                &nbsp;&nbsp;
                <?php
									}
								} else if (isset($value["estado_pago"]) && $value["estado_pago"] == "2" && $ind_modificar_pagos == 1) {
				?>
                <input class="btnPrincipal" type="button" name="btnRegistrarpagos" id="btnRegistrarpagos" value="Guardar cambios" onclick="validar_registrar_pago(<?php echo($contadorid_aux); ?>, 4);" />
                &nbsp;&nbsp;
                <?php
								}
							}
							
							if (isset($value["estado_pago"]) && $value["estado_pago"] != "3" && $ind_modificar_pagos == 1) {
				?>
                <input class="btnPrincipal" type="button" name="btnBorrarPagos" id="btnBorrarPagos" value="Anular pago" onclick="borrar_pago();" />
                &nbsp;&nbsp;
                <?php
							}
						}
				?>
                <input class="btnPrincipal" type="button" name="btnImprimirPago" id="btnImprimirPago" value="Imprimir recibo" onclick="imprimir_recibo();" />
                <?php
					
						if ($num_factura != "" && $fecha_pago <= "2020-08-02") {
							
				?>
                &nbsp;&nbsp;
                <input class="btnPrincipal" type="button" name="btn_imprimir_factura" id="btn_imprimir_factura" value="Imprimir factura" onclick="imprimir_factura();" />
                <?php
						}
					} else if ($tipo_acceso_menu == "2") {
				?>
                <input class="btnPrincipal" type="button" name="btnCrearPagos" id="btnCrearPagos" value="Crear pago pendiente" onclick="validar_registrar_pago(<?php echo($contadorid_aux); ?>, 1);" />
                <?php
					}
					
					$id_pago_aux = "";
					$id_paciente_aux = "";
					$edad_paciente_aux = "";
					$id_plan_aux = "";
					$ind_tipo_pago_aux = "";
					$ind_desc_cc = "";
					if ($ind_crear == 0) {
						$id_pago_aux = $value["id_pago"];
						$id_paciente_aux = $value["id_paciente"];
						$id_plan_aux = $value["id_plan"];
						$ind_tipo_pago_aux = $value["ind_tipo_pago"];
						$ind_desc_cc = $value["ind_desc_cc"];
						
						//Se busca la edad del paciente
						$paciente_obj = $dbPacientes->getEdadPaciente($id_paciente_aux, $fecha_pago);
						$edad_paciente_aux = $paciente_obj["edad"];
					}
				?>
                <input type="hidden" id="hdd_id_pago" name="hdd_id_pago" value="<?php echo($id_pago_aux); ?>" />
                <input type="hidden" id="hdd_admision" name="hdd_admision" value="<?php echo($admision); ?>" />
                <input type="hidden" id="hdd_idPaciente" name="hdd_idPaciente" value="<?php echo($id_paciente_aux); ?>" />
                <input type="hidden" id="hdd_edadPaciente" name="hdd_edadPaciente" value="<?php echo($edad_paciente_aux); ?>" />
                <input type="hidden" id="hdd_idPlan" name="hdd_idPlan" value="<?php echo($id_plan_aux); ?>" />
                <input type="hidden" id="hdd_idTipoPago" name="hdd_idTipoPago" value="<?php echo($ind_tipo_pago_aux); ?>" />
                <input type="hidden" id="hdd_descCC" name="hdd_descCC" value="<?php echo($ind_desc_cc); ?>" />
                <input type="hidden" id="hdd_pagar<?php echo($contadorid_aux); ?>" name="hdd_pagar<?php echo($contadorid_aux); ?>" value="<?php echo($total); ?>" />
                <input type="hidden" id="hdd_cuotaPagar" name="hdd_cuotaPagar" value="<?php echo($valor_cuota_total); ?>" />
                <input type="hidden" id="hdd_pagar<?php echo($contadorid_aux); ?>_aux" name="hdd_pagar<?php echo($contadorid_aux); ?>_aux" value="0" />
                <div id="fondo_negro_servicios<?php echo($contadorid_aux); ?>" class="d_fondo_negro"></div>
                <div class="div_centro" id="d_centro_servicios<?php echo($contadorid_aux); ?>" style="display:none;">
                    <a name="a_cierre_panel" id="a_cierre_panel" href="#" onclick="muestraFormularioFlotante(0,<?php echo($contadorid_aux); ?>);"></a>
                    <div class="div_interno" id="d_interno_servicios<?php echo($contadorid_aux); ?>"></div>
                </div>
            </div>
            <?php
				} else { //Si existen registros de pago
					//Se buscan los detalle de los pagos
					$lista_pagos_detalle = $dbPagos->pagosPendientesDetalle($parametro, $id_admision, $id_pago);
					
					//Se reacomodan en un array por id de pago
					$arr_pagos_detalle = array();
					foreach ($lista_pagos_detalle as $detalle_aux) {
						if (!isset($arr_pagos_detalle[$detalle_aux["id_pago"]])) {
							$arr_pagos_detalle[$detalle_aux["id_pago"]] = array();
						}
						array_push($arr_pagos_detalle[$detalle_aux["id_pago"]], $detalle_aux);
					}
			?>
            <div style="margin-bottom: 30px;"> 
                <div style="text-align: left;">
                    <h5>Pagos encontrados: <?php echo(count($resultado1_aux)); ?></h5>
                </div>
                <table class="paginated modal_table" style="width: 99%; margin: auto;">
                    <thead>
                        <tr>
                            <th style="width:25%;">Nombre</th>
                            <th style="width:15%;">N&uacute;mero documento</th>
                            <th style="width:30%;">Conceptos</th>
                            <th style="width:15%;">Fecha pago</th>
                            <th style="width:15%;">Estado pago</th>
                        </tr>
                        <?php
							foreach ($resultado1_aux as $value) {
						?>
                        <tr onclick="seleccionar_paciente('<?php echo($value["id_pago"]); ?>');">
                            <td style="text-align: left;">
                                <?php echo($funcionesPersona->obtenerNombreCompleto($value["nombre_1"], $value["nombre_2"], $value["apellido_1"], $value["apellido_2"])); ?>
                            </td>
                            <td><?php echo($value["cod_tipo_documento"]." ".$value["numero_documento"]); ?></td>
                            <td align="left">
                                <?php
									if (isset($arr_pagos_detalle[$value["id_pago"]]) && count($arr_pagos_detalle[$value["id_pago"]]) > 0) {
								?>
                                <ul class="no-margin">
                                    <?php
										foreach ($arr_pagos_detalle[$value["id_pago"]] as $concepto_aux) {
											switch ($concepto_aux["tipo_precio"]) {
												case "P":
									?>
                                    <li><?php echo($concepto_aux["nombre_procedimiento"]); ?></li>
                                    <?php
													break;
												case "M":
									?>
                                    <li><?php echo($concepto_aux["nombre_generico"]); ?></li>
                                    <?php
													break;
												case "I":
									?>
                                    <li><?php echo($concepto_aux["nombre_insumo"]); ?></li>
                                    <?php
													break;
											}
										}
									?>
                                </ul>
                                <?php
									} else {
										echo("-");
									}
								?>
                            </td>
                            <td><?php echo($value["fecha_pago_t"]); ?></td>
                            <?php
								$estado_aux = "";
								switch ($value["estado_pago"]) {
									case "1":
										$estado_aux = "PENDIENTE";
										break;
									case "2":
										$estado_aux = "PAGADO";
										break;
									case "3":
										$estado_aux = "<span style='color:#F00;'>ANULADO</span>";
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
			
		case "2": //Registra el Pago
			@$idPago = $utilidades->str_decode($_POST["id_pago"]);
			@$tipo_accion = intval($_POST["tipo_accion"], 10);
			@$idAdmision = $utilidades->str_decode($_POST["idAdmision"]);
			@$idPaciente = $utilidades->str_decode($_POST["idPaciente"]);
			@$idLugarCita = $utilidades->str_decode($_POST["idLugarCita"]);
			@$idUsuarioProf = $utilidades->str_decode($_POST["idUsuarioProf"]);
			@$idConvenio = $utilidades->str_decode($_POST["idConvenio"]);
			@$idPlan = $utilidades->str_decode($_POST["idPlan"]);
			@$observaciones_pago = trim($utilidades->str_decode($_POST["observaciones_pago"]));
			@$id_tercero_pago = $utilidades->str_decode($_POST["idTerceroPago"]);
			
			@$cmbEntidad = 131; //$utilidades->str_decode($_POST["cmbEntidad"]);
			
			@$num_factura = $utilidades->str_decode($_POST["num_factura"]);
			@$num_pedido = $utilidades->str_decode($_POST["num_pedido"]);
			@$cant_productos = intval($_POST["cant_productos"], 10);
			
			@$num_mipress =  $utilidades->str_decode($_POST["num_mipress"]);
			@$num_ent_mipress =  $utilidades->str_decode($_POST["num_ent_mipress"]);
			@$num_poliza =  $utilidades->str_decode($_POST["num_poliza"]);
			
			$valor_copagos = 0;
			$valor_total = 0;
			$arr_pagos_detalle = array();
			for ($i = 0; $i < $cant_productos; $i++) {
				@$arr_pagos_detalle[$i]["id_detalle_precio"] = $utilidades->str_decode($_POST["id_detalle_precio_".$i]);
				@$arr_pagos_detalle[$i]["cod_servicio"] = $utilidades->str_decode($_POST["cod_servicio_".$i]);
				@$arr_pagos_detalle[$i]["tipo_precio"] = $utilidades->str_decode($_POST["tipo_precio_".$i]);
				@$arr_pagos_detalle[$i]["tipo_bilateral"] = $utilidades->str_decode($_POST["tipo_bilateral_".$i]);
				@$arr_pagos_detalle[$i]["num_autorizacion"] = $utilidades->str_decode($_POST["num_autorizacion_".$i]);
				@$arr_pagos_detalle[$i]["cantidad"] = $utilidades->str_decode($_POST["cantidad_".$i]);
				@$arr_pagos_detalle[$i]["valor"] = $utilidades->str_decode($_POST["valor_".$i]);
				@$arr_pagos_detalle[$i]["valor_cuota"] = $utilidades->str_decode($_POST["valor_cuota_".$i]);
				
				$valor_copagos += (intval(@$arr_pagos_detalle[$i]["cantidad"], 10) * floatval(@$arr_pagos_detalle[$i]["valor_cuota"]));
				$valor_total += (intval(@$arr_pagos_detalle[$i]["cantidad"], 10) * floatval(@$arr_pagos_detalle[$i]["valor"]));
			}
			
			//Se hallan los datos de compañía asociados al lugar del pago
			$sede_det_obj = $dbListas->getSedesDetalle($idLugarCita);
			$compania = $sede_det_obj["id_compania"];
			$co_ventas = $sede_det_obj["id_co_ventas"];
			$bodega_ventas = $sede_det_obj["id_bodega_ventas"];
			
			//Se consulta el código del prestador del servicio
			$obj_entidad = $dbDatosEntidad->getDatosEntidadId($compania);
			$cod_prestador = $obj_entidad["cod_prestador"];
						
			if ($cant_productos > 0) {
				$convenio_obj = $dbConvenios->getConvenio($idConvenio);
				$plan_obj = $dbPlanes->getPlan($idPlan);
				
				//Se busca el tipo de pago asociado al plan
				$ind_tipo_pago = $plan_obj["ind_tipo_pago"];
				
				//if ($ind_formato_valido) {
					@$cant_medios_pago = intval($_POST["cant_medios_pago"]);
					$arr_medios_pago = array();
					$arr_anticipos = array();
					for ($i = 0; $i < $cant_medios_pago; $i++) {
						if ($_POST["tipoPago".$i] != "") {
							$arr_aux = array();
							@$arr_aux["tipoPago"] = $utilidades->str_decode($_POST["tipoPago".$i]);
							@$arr_aux["BancoPago"] = $utilidades->str_decode($_POST["bancoPago".$i]);
							@$arr_aux["numCheque"] = $utilidades->str_decode($_POST["numCheque".$i]);
							@$arr_aux["numCuenta"] = $utilidades->str_decode($_POST["numCuenta".$i]);
							@$arr_aux["codSeguridad"] = $utilidades->str_decode($_POST["codSeguridad".$i]);
							@$arr_aux["numAutoriza"] = $utilidades->str_decode($_POST["numAutoriza".$i]);
							@$arr_aux["anoVence"] = $utilidades->str_decode($_POST["anoVence".$i]);
							@$arr_aux["mesVence"] = $utilidades->str_decode($_POST["mesVence".$i]);
							@$arr_aux["referencia"] = $utilidades->str_decode($_POST["referencia".$i]);
							@$arr_aux["fechaConsigna"] = $utilidades->str_decode($_POST["fechaConsigna".$i]);
							@$arr_aux["idFranquiciaTC"] = $utilidades->str_decode($_POST["idFranquiciaTC".$i]);
							@$arr_aux["valorPago"] = $utilidades->str_decode($_POST["valorPago".$i]);
							@$arr_aux["IdUsuarioAutoriza"] = $utilidades->str_decode($_POST["IdUsuarioAutoriza".$i]);
							$cant_anticipos_aux = intval($_POST["cant_anticipos_".$i], 10);
							@$arr_aux["cant_anticipos"] = $cant_anticipos_aux;
							if ($cant_anticipos_aux > 0) {
								for ($j = 0; $j < $cant_anticipos_aux; $j++) {
									@$arr_anticipos[$j]["id_anticipo"] = $utilidades->str_decode($_POST["id_anticipo_".$i."_".$j]);
									@$arr_anticipos[$j]["valor"] = $utilidades->str_decode($_POST["valor_anticipo_".$i."_".$j]);
								}
								$arr_aux["lista_anticipos"] = $arr_anticipos;
							}
							
							array_push($arr_medios_pago, $arr_aux);
						}
					}
					
					$ind_continuar = true;
					if ($tipo_accion == 1 || $tipo_accion == 2) {
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
						@$email = $utilidades->str_decode($_POST["email"]);
						
						
						@$num_factura = strtoupper($utilidades->str_decode(trim($_POST["num_factura"])));
						@$num_pedido = strtoupper($utilidades->str_decode(trim($_POST["num_pedido"])));
						//Se crea/actualiza el paciente
						$id_paciente_resul = $dbPacientes->crear_editar_paciente($idPaciente, $id_tipo_documento, $numero_documento, $nombre_1,
											 $nombre_2, $apellido_1, $apellido_2, $direccion, $id_pais, $cod_dep, $cod_mun, $nom_dep, $nom_mun,
											 $telefono_1, $telefono_2, $id_usuario,$email);
						
						if ($id_paciente_resul <= 0) {
							$ind_continuar = false;
						} else {
							$idPaciente = $id_paciente_resul;
						}
		?>
        <input type="hidden" id="hdd_id_paciente_resul" value="<?php echo($id_paciente_resul); ?>" />
        <?php
					} else {
		?>
        <input type="hidden" id="hdd_id_paciente_resul" value="1" />
        <?php
					}
					
					@$num_mipress =  $utilidades->str_decode($_POST["num_mipress"]);
					@$num_ent_mipress =  $utilidades->str_decode($_POST["num_ent_mipress"]);
					@$num_poliza =  $utilidades->str_decode($_POST["num_poliza"]);
					
					if ($ind_continuar) {
						$resultado = -1;
						if ($tipo_accion == 1) {
							//Nuevo pago sin registro
							$resultado = $dbPagos->registrarPagos("", "", $idPaciente, $id_usuario, "", "", "", "", "", "", "", "", "", $idPlan, $idConvenio,
										 "", "", $observaciones_pago, 1, $arr_pagos_detalle, array(), $idLugarCita, $idUsuarioProf, $id_tercero_pago, "", array(),
										 $num_mipress,$num_ent_mipress,$num_poliza);
						} else if ($tipo_accion == 3) {
							//Edición de pago sin registro
							$resultado = $dbPagos->registrarPagos($idPago, $idAdmision, $idPaciente, $id_usuario, "", "", "", "", "", "", "", "", "", $idPlan, $idConvenio,
										 $num_factura, "", $observaciones_pago, 1, $arr_pagos_detalle, array(), $idLugarCita, $idUsuarioProf, $id_tercero_pago, $num_pedido, array(),
										 $num_mipress,$num_ent_mipress,$num_poliza);
						} else if ($tipo_accion == 4) {
							//Edición de pagos registrados
							$resultado = $dbPagos->registrarPagos($idPago, $idAdmision, $idPaciente, $id_usuario, "", "", "", "", "", "", "", "", "", $idPlan, $idConvenio,
										 $num_factura, "", $observaciones_pago, 2, $arr_pagos_detalle, $arr_medios_pago, $idLugarCita, $idUsuarioProf, $id_tercero_pago, $num_pedido, array(),
										 $num_mipress,$num_ent_mipress,$num_poliza);
						} else {
							//Se obtienen los datos actuales del pago
							$pago_obj = $dbPagos->get_pago_id($idPago);
							$num_pedido_aut = $pago_obj["num_pedido"];
							$num_factura_aut = $pago_obj["num_factura"];
							
							if(($arr_medios_pago[0]["tipoPago"] <> "100" && $arr_medios_pago[0]["tipoPago"] <> "99")  && ($num_pedido_aut == "" || $num_factura_aut == "")){
								//Se verifica que el pago no tenga asociados pedidos o facturas activos en Siesa
								$num_pedido_ant = -1;
								if($num_pedido_aut == ""){
								$lista_pedidos_ant = $classConsultasSiesa->consultarPedidoEstados($compania, $idPago);
								
									foreach ($lista_pedidos_ant as $pedido_aux) {
										if ($pedido_aux["ESTADO"] != "9") {
											$num_pedido_ant = $pedido_aux["DOCUMENTO"];
											break;
										}
									}
								}
								$num_factura_ant = -1;		
								if ($num_factura_aut == "") {
									$num_factura_ant = $classConsultasSiesa->consultarFacturaPago($compania, $idPago);
								}
								
								$resultado_aux = 1;
							
								//$num_pedido_ant =-1;//Se borra							
							if ($num_pedido_ant == -1) {
								?>
								<input type="hidden" id="hdd_resul_pedido_ant_siesa" name="hdd_resul_pedido_ant_siesa" value="1" />
								<?php
							} else {
								$resultado_aux = -1;
								?>
								<input type="hidden" id="hdd_resul_pedido_ant_siesa" name="hdd_resul_pedido_ant_siesa" value="-1" />
								<input type="hidden" id="hdd_num_pedido_ant_siesa" name="hdd_num_pedido_ant_siesa" value="<?php echo($num_pedido_ant); ?>" />
								<?php
							}
							
							//$num_factura_ant =-1;//Se borra
							if ($num_factura_ant == -1) {
								?>
								<input type="hidden" id="hdd_resul_factura_ant_siesa" name="hdd_resul_factura_ant_siesa" value="1" />
								<?php
							} else {
								$resultado_aux = -1;
								?>
								<input type="hidden" id="hdd_resul_factura_ant_siesa" name="hdd_resul_factura_ant_siesa" value="-1" />
								<input type="hidden" id="hdd_num_factura_ant_siesa" name="hdd_num_factura_ant_siesa" value="<?php echo($num_factura_ant); ?>" />
								<?php
							}
										
							if ($resultado_aux == 1) {
								//Se verifica que el tercero (paciente o responsable) exista en SIESA
								if ($id_tercero_pago != "") {
									$tercero_obj = $dbTerceros->getTercero($id_tercero_pago);
									$tipo_doc_tercero = $tercero_obj["codigo_doc_siesa"];
									$num_doc_tercero = $tercero_obj["numero_documento"];
									
									$resultado_aux = $classTercerosSiesa->crearTerceroClienteNoPaciente($id_tercero_pago, $idPaciente, $compania,$id_usuario);
								} else {
									
									$paciente_obj = $dbPacientes->getExistepaciente3($idPaciente);
									$tipo_doc_tercero = $paciente_obj["codigo_doc_siesa"];
									$num_doc_tercero = $paciente_obj["numero_documento"];
									
									$resultado_aux = $classTercerosSiesa->crearTerceroCliente($idPaciente, $compania, $idLugarCita, $id_usuario);
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
								
								//Se verifica que el tercero del convenio se encuentre configurado en SIESA obteniendo su condición de pago
								$condicion_pago_entidad = "";
								if ($resultado_aux == 1 && $ind_tipo_pago == "1") {
									//El plan no es de pago directo (particular)
									$num_doc_tercero_aux = $convenio_obj["numero_documento"];
									$resultado_aux = $classConsultasSiesa->consultarCondicionPagoTercero($compania, $num_doc_tercero_aux);
								if ($resultado_aux != -1) {
									$condicion_pago_entidad = $resultado_aux;
									$resultado_aux = 1;
								}
								
								?>
								<input type="hidden" id="hdd_resul_tercero_entidad_siesa" name="hdd_resul_tercero_entidad_siesa" value="<?= $resultado_aux ?>" />
								<?php
									}
									
								
								if ($resultado_aux == 1) {
									
									//Se obtienen los datos del usuario
									$usuario_obj = $dbUsuarios->getUsuario($id_usuario);
									
									$resul_pedido = 1;
									if($valor_copagos<$valor_total){
										if ($num_pedido_aut == "") {
											if ($ind_tipo_pago == "1") {
											//Se crea un pedido
											$resul_pedido = $classPedidosSiesa->crearPedido($compania, $co_ventas, $bodega_ventas, $usuario_obj["numero_documento"], $idPago, 
											$idConvenio, $idPlan, $idPaciente, $condicion_pago_entidad, $arr_pagos_detalle, $id_usuario);
											
										}	
													
										if ($resul_pedido > 0) {
											if ($ind_tipo_pago == "1" && $resul_pedido == 1) {
												//Se consulta el número del pedido
												sleep(8); //Se hace un delay debido a que el sistema no es capaz de consultar el pedido en SIESA		
												$num_pedido = $classConsultasSiesa->consultarPedidoPago($compania, $idPago);
												
												if(intval($num_pedido)==-1){
													
													for($i=0; $i<=3; $i++){
														$num_pedido = $classConsultasSiesa->consultarPedidoPago($compania, $idPago);
														if(intval($num_pedido)>0){
															break;
														}
													}
												}
																											
											}
											
											if ($num_pedido != "-1") {
												
												if($num_pedido != "" && $num_pedido>0){
													$resul_pedido = 1;
													?>
														<input type="hidden" id="hdd_resul_pedido_siesa" name="hdd_resul_pedido_siesa" value="1" />
														<input type="hidden" id="hdd_num_pedido_siesa" name="hdd_num_pedido_siesa" value="<?= $num_pedido ?>" />
													<?php
														
													 //Se envían las entidades dinámicas del pedido, tanto del documento, como para los movimientos.
													  $result_entidad_doc = $classPedidosSiesa->crearEntidadPedidoDocumento($compania, $cod_prestador, $co_ventas, $bodega_ventas, $usuario_obj["numero_documento"], 
													  $idPago, $idConvenio, $idPlan, $idPaciente, $id_usuario,$num_pedido);
														
													  if($result_entidad_doc == 1){
												
														$result_entidad_mov = $classPedidosSiesa->crearEntidadPedido($compania, $co_ventas, $bodega_ventas, $idPago, $id_usuario,$num_pedido);
														  
													  }if($result_entidad_doc == 1 && $result_entidad_mov == 1){
														  ?> <input type="hidden" id="hdd_resul_pedido_entidad_siesa" name="hdd_resul_pedido_entidad_siesa" value="1" /> <?php
														  
													  }else{
														 $msg_pedido="";
														
														 if($result_entidad_doc<>1){ $msg_pedido=$result_entidad_doc; } else if($result_entidad_mov<>1){ $msg_pedido=$result_entidad_mov; }
														
														 ?> 
															<input type="hidden" id="hdd_resul_pedido_entidad_siesa" name="hdd_resul_pedido_entidad_siesa" value="-1" /> 
															<input type="hidden" id="hdd_mensaje_pedido_entidad_siesa" name="hdd_mensaje_pedido_entidad_siesa" value="<?= $msg_pedido?>" />
														 <?php
														 
														 $resul_pedido = -1;
												 }
										
												}
												
											} else {
									?>
									<input type="hidden" id="hdd_resul_pedido_siesa" name="hdd_resul_pedido_siesa" value="-1" />
									<input type="hidden" id="hdd_mensaje_pedido_siesa" name="hdd_mensaje_pedido_siesa" value="No se hall&oacute; un n&uacute;mero de pedido asociado." />
									<?php
												$resul_pedido = -1;
											}
										} else {
									?>
									<input type="hidden" id="hdd_resul_pedido_siesa" name="hdd_resul_pedido_siesa" value="-1" />
									<input type="hidden" id="hdd_mensaje_pedido_siesa" name="hdd_mensaje_pedido_siesa" value="<?= $resul_pedido ?>" />
									<?php
											$resul_pedido = -1;
										}
									}else {
											//El pedido ya existe y fue autorizado
											$num_pedido = $num_pedido_aut;
									?>
									<input type="hidden" id="hdd_resul_pedido_siesa" name="hdd_resul_pedido_siesa" value="1" />
									<input type="hidden" id="hdd_num_pedido_siesa" name="hdd_num_pedido_siesa" value="<?= $num_pedido_aut ?>" />
									<?php 
									
									}	
								}
									
								if ($resul_pedido == 1) {
									$resul_factura = 1;
									if ($num_factura_aut == "") {
										if (($ind_tipo_pago == "2" && $valor_total > 0) || $valor_copagos > 0) {
											if ($ind_tipo_pago == "1") {
											//Se factura el concepto de cuota moderadora / copago
											$variable_obj_aux = $dbVariables->getVariable(22);
											$cod_servicio_cc = $variable_obj_aux["valor_variable"];
											
											//Se busca el concepto con el mayor valor, se tomarán de él la unidad de negocios y el centro de costos
											$valor_max_aux = -1;
											$tipo_precio_aux = "";
											$cod_servicio_max_aux = "";
											foreach ($arr_pagos_detalle as $detalle_aux) {
												if ($detalle_aux["valor"] > $valor_max_aux) {
													$valor_max_aux = $detalle_aux["valor"];
													$tipo_precio_aux = $detalle_aux["tipo_precio"];
													$cod_servicio_max_aux = $detalle_aux["cod_servicio"];
												}
											}
											switch ($tipo_precio_aux) {
												case "P": //Procedimientos
													$prod_obj_aux = $dbMaestroProcedimientos->getProcedimiento($cod_servicio_max_aux);
													$cod_und_negocios_aux = $prod_obj_aux["cod_und_negocios"];
													$cod_centro_costos_aux = $prod_obj_aux["cod_centro_costos"];
													break;
												case "I": //Insumos
													$prod_obj_aux = $dbMaestroInsumos->getInsumo($cod_servicio_max_aux);
													$cod_und_negocios_aux = $prod_obj_aux["cod_und_negocios"];
													$cod_centro_costos_aux = $prod_obj_aux["cod_centro_costos"];
													break;
												default:
													$cod_und_negocios_aux = "";
													$cod_centro_costos_aux = "";
													break;
											}
											
											//Se crea el registro
											$arr_aux = array();
											$arr_aux["cod_servicio"] = $cod_servicio_cc;
											$arr_aux["tipo_precio"] = "C"; //Tipo copago / cuota moderadora
											$arr_aux["tipo_bilateral"] = "0";
											$arr_aux["cantidad"] = 1;
											$arr_aux["valor"] = 0;
											$arr_aux["valor_cuota"] = $valor_copagos;
											$arr_aux["cod_und_negocios"] = $cod_und_negocios_aux;
											$arr_aux["cod_centro_costos"] = $cod_centro_costos_aux;
											
											$arr_productos_aux = array();
											array_push($arr_productos_aux, $arr_aux);
										} else {
											//Se facturan los conceptos seleccionados
											$arr_productos_aux = $arr_pagos_detalle;
										}
											
											//Se crea una factura
											$resul_factura = $classFacturasSiesa->crearFactura($compania, $co_ventas, $bodega_ventas, $num_doc_tercero,
													$usuario_obj["numero_documento"], $idPago, $idConvenio, $idPlan, $ind_tipo_pago, $idPaciente,
													$arr_productos_aux, $arr_medios_pago, $arr_anticipos, $id_usuario);
											
											$num_factura = $resul_factura[1];
											$resul_factura = $resul_factura[0];
											
											if(intval($num_factura)==-1){
												sleep(7); //Se hace un delay debido a que el sistema no es capaz de consultar la factura en SIESA.		
												
												for($i=0; $i<=3; $i++){
													$num_factura = $classConsultasSiesa->consultarFacturaPago($compania, $idPago);
													if(intval($num_factura)>0){
														break;
													}
												}
											}
																					
											if ($resul_factura == 1 && $num_factura != "-1") {
												
													?>
													<input type="hidden" id="hdd_resul_factura_siesa" name="hdd_resul_factura_siesa" value="1" />
													<input type="hidden" id="hdd_num_factura_siesa" name="hdd_num_factura_siesa" value="<?= $num_factura ?>" />
													<?php
												
												
												$resul_entidad_factura_doc = $classFacturasSiesa->crearEntidadFactura($compania, $cod_prestador, $co_ventas, $num_factura, 
												                             $bodega_ventas,$arr_productos_aux, $num_doc_tercero, $usuario_obj["numero_documento"], $idPago, 
																			 $idConvenio, $idPlan, $ind_tipo_pago, $idPaciente, $id_usuario, 1);
														
												if($resul_entidad_factura_doc==1){
													
													$resul_entidad_factura_mov = $classFacturasSiesa->crearEntidadFactura($compania, $cod_prestador, $co_ventas, $num_factura,
													$bodega_ventas,$arr_productos_aux, $num_doc_tercero, $usuario_obj["numero_documento"], $idPago,
													$idConvenio, $idPlan, $ind_tipo_pago, $idPaciente, $id_usuario,2);
												}
												
													
												 if($resul_entidad_factura_doc==1 && $resul_entidad_factura_mov==1){
															 
														?> <input type="hidden" id="hdd_resul_entidad_factura_siesa" name="hdd_resul_entidad_factura_siesa" value="1"/> <?php
															
												 }else{
													$msg_factura="";
													 if($resul_entidad_factura_doc<>1){ $msg_factura=$resul_entidad_factura_doc; } 
													 else if($resul_entidad_factura_mov<>1){ $msg_factura=$resul_entidad_factura_mov; }
														 ?> 
															<input type="hidden" id="hdd_resul_entidad_factura_siesa" name="hdd_resul_entidad_factura_siesa" value="-1" /> 
															<input type="hidden" id="hdd_mensaje_entidad_factura_siesa" name="hdd_mensaje_entidad_factura_siesa" value="<?= $msg_factura?>" />
														<?php
														$resul_factura=-1;
													}	
											} else {
												if ($resul_factura == 1)  {
													$resul_factura = "No se hall&oacute; un n&uacute;mero de factura asociado.";
												}
												if ($num_pedido != "") {
													$resul_factura .= "<br /><b>Se gener&oacute; el pedido n&uacute;mero ".$num_pedido.", por favor realice la anulaci&oacute;n manual del mismo.</b>";
												}
										?>
										<input type="hidden" id="hdd_resul_factura_siesa" name="hdd_resul_factura_siesa" value="-1" />
										<input type="hidden" id="hdd_mensaje_factura_siesa" name="hdd_mensaje_factura_siesa" value="<?= $resul_factura ?>" />
										<?php
												$resul_factura = -1;
											}
										}
										} else {
											//La factura ya existe y fue autorizada
											$num_factura = $num_factura_aut;
										?>
										<input type="hidden" id="hdd_resul_factura_siesa" name="hdd_resul_factura_siesa" value="1" />
										<input type="hidden" id="hdd_num_factura_siesa" name="hdd_num_factura_siesa" value="<?= $num_factura_aut ?>" />
										<?php
										}	
									} else {
										$resul_factura = -1;
									}if ($resul_factura == 1) {
										
									
										$num_pedido == "-1" ? $num_pedido="" : $num_pedido;
										$num_factura == "-1" ? $num_factura="" : $num_factura;
										 
										$resultado = $dbPagos->registrarPagos($idPago, $idAdmision, $idPaciente, $id_usuario, "", "", "", "", "", "", "", "", "", $idPlan, $idConvenio,
													 $num_factura, $cmbEntidad, $observaciones_pago, 2, $arr_pagos_detalle, $arr_medios_pago, $idLugarCita, $idUsuarioProf, $id_tercero_pago,                                                     $num_pedido,array(),$num_mipress,$num_ent_mipress,$num_poliza);
									}
								}
							}
							
							}else{
								$resultado = $dbPagos->registrarPagos($idPago, $idAdmision, $idPaciente, $id_usuario, "", "", "", "", "", "", "", "", "", $idPlan, $idConvenio,
													 $num_factura, $cmbEntidad, $observaciones_pago, 2, $arr_pagos_detalle, $arr_medios_pago, $idLugarCita, $idUsuarioProf, $id_tercero_pago,                                                     $num_pedido,array(),$num_mipress,$num_ent_mipress,$num_poliza);
								
							}
						}
				?>
				<input type="hidden" id="hdd_id_pago_resul" value="<?php echo($resultado); ?>" />
				
				<?php
					}
			} else {
		?>
        <input type="hidden" id="hdd_id_paciente_resul" value="-3" />
        <?php
			}
			break;
			
		case "3": //Indicadores de banco, tercero y autorización
			$idTipoPago = $utilidades->str_decode($_POST["idTipoPago"]);
			$resultado = $dbTiposPago->getTipoPago($idTipoPago);
		?>
        <input type="hidden" id="hdd_ind_banco_tp" value="<?php echo($resultado["ind_banco"]); ?>" />
        <input type="hidden" id="hdd_ind_tercero_tp" value="<?php echo($resultado["ind_tercero"]); ?>" />
        <input type="hidden" id="hdd_ind_usuario_aut_tp" value="<?php echo($resultado["ind_usuario_aut"]); ?>" />
        <?php
			break;
	
		case "4": //Imprime formulario flotante: Agregar Servicio
			$idcontador = $_POST["idcontador"];
		?>
        <div class="encabezado">
            <h3>Agregar consultas, procedimientos, medicamentos, insumos y paquetes</h3>
        </div>
        <div>
            <form id="frmListadoPrecios" name="frmListadoPrecios">
                <table style="width: 100%;">
                    <tbody>
                        <tr valign="middle">
                            <td align="center" colspan="2">
                                <div id="advertenciasg"></div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:90%;">
                                <input type="text" id="txtParametroPrecios" name="txtParametroPrecios" placeholder="C&oacute;digo o nombre de la consulta, procedimiento, medicamento, insumo o paquete" onblur="trim_cadena(this);" />
                            </td>
                            <td style="width:10%;">
                                <input type="submit" id="btnBuscar" nombre="btnBuscar" value="Buscar" class="btnPrincipal peq" onclick="buscarProcedimientos(1, <?php echo($idcontador); ?>);" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
            <div id="resultadoProcedimientos"></div>
        </div>    
        <?php
			break;
			
		case "5"://Imprime el resultado para el formulario flotante: Agregar Servicio
			$idPlan = $utilidades->str_decode($_POST["idPlan"]);
			$tipoPago = $utilidades->str_decode($_POST["tipoPago"]);
			$parametroPrecios = $utilidades->str_decode($_POST["parametroPrecios"]);
			
			$lista_precios = $dbListasPrecios->getListasPreciosPorPlanParametro($idPlan, $parametroPrecios, "", 1);
			$lista_paquetes = $dbPaquetesProcedimientos->buscarPaquetesActivos($parametroPrecios);
			if (count($lista_paquetes)) {
				foreach ($lista_paquetes as $paquete_aux) {
					$arr_aux = array();
					$arr_aux["tipo_precio"] = "Q";
					$arr_aux["id_paquete_p"] = $paquete_aux["id_paquete_p"];
					$arr_aux["nom_paquete_p"] = $paquete_aux["nom_paquete_p"];
					$arr_aux["valor"] = "0";
					$arr_aux["valor_cuota"] = "0";
					$arr_aux["tipo_bilateral"] = "0";
					$arr_aux["ind_tipo_pago"] = "1";
					
					array_push($lista_precios, $arr_aux);
				}
			}
			
			//Pasa la variable númerica auto incrementable para diferenciar a cual div en la página pagos.php se le va agregar el procedimiento seleccionado 
			$idContador = $utilidades->str_decode($_POST["idContador"]);
		?>
        <table class="paginated modal_table" style="width: 99%; margin: auto;">
            <thead>
                <tr>
                    <th style="width:8%;">C&oacute;digo</th>
                    <th style="width:12%;">Tipo</th>
                    <th style="width:55%;">Nombre</th>
                    <th style="width:15%;">Tipo de valor</th>
                    <th style="width:10%;">Precio</th>
                </tr>
            </thead>
            <?php
				if (count($lista_precios) >= 1) {
					for ($i = 0; $i < count($lista_precios); $i++) {
						$value = $lista_precios[$i];
						
						$cod_servicio_aux = "";
						$nombre_servicio_aux = "";
						$texto_tipo_precio_aux = "";
						switch ($value["tipo_precio"]) {
							case "P":
								$cod_servicio_aux = $value["cod_procedimiento"];
								$nombre_servicio_aux = $value["nombre_procedimiento"];
								$texto_tipo_precio_aux = "Procedimiento";
								break;
							case "M":
								$cod_servicio_aux = $value["cod_medicamento"];
								$nombre_servicio_aux = $value["nombre_medicamento_aux"];
								$texto_tipo_precio_aux = "Medicamento";
								break;
							case "I":
								$cod_servicio_aux = $value["cod_insumo"];
								$nombre_servicio_aux = $value["nombre_insumo"];
								$texto_tipo_precio_aux = "Insumo";
								break;
							case "Q":
								$cod_servicio_aux = $value["id_paquete_p"];
								$nombre_servicio_aux = $value["nom_paquete_p"];
								$texto_tipo_precio_aux = "Paquete";
								break;
						}
						
						$valor_cuota_aux = $value["valor_cuota"];
						if ($valor_cuota_aux == "") {
							$valor_cuota_aux = "0";
						}
						
						$texto_tipo_bilateral_aux = "";
						foreach ($arr_bilateralidades as $bilateralidad_aux) {
							if ($bilateralidad_aux["id"] == $value["tipo_bilateral"]) {
								$texto_tipo_bilateral_aux = $bilateralidad_aux["valor"];
							}
						}
			?>
            <tr onclick="agregar_servicio('<?php echo($cod_servicio_aux); ?>', '<?php echo($texto_tipo_precio_aux); ?>', '<?php echo($nombre_servicio_aux); ?>', '<?php echo($value["valor"]); ?>', '<?php echo($valor_cuota_aux); ?>', '<?php echo($value["tipo_precio"]); ?>', <?php echo($value["tipo_bilateral"]); ?>, '<?php echo($texto_tipo_bilateral_aux); ?>', '<?php echo($value["ind_tipo_pago"]); ?>', '<?php echo($idContador); ?>', <?php echo($i); ?>);">
                <td>
					<?php
						//Si es un paquete, se buscan los datos de detalle
						if ($value["tipo_precio"] == "Q") {
							$lista_paquete_det = $dbListasPrecios->getListaPreciosPaquetePlan($cod_servicio_aux, $idPlan);
							$cod_servicio_aux_ant = "";
							$j = 0;
							foreach ($lista_paquete_det as $det_aux) {
								$cod_servico_b_det = "";
								$nombre_servico_b_det = "";
								switch ($det_aux["tipo_producto"]) {
									case "P":
										$cod_servico_b_det = $det_aux["cod_procedimiento"];
										$nombre_servico_b_det = $det_aux["nombre_procedimiento"];
										break;
									case "I":
										$cod_servico_b_det = $det_aux["cod_insumo"];
										$nombre_servico_b_det = $det_aux["nombre_insumo"];
										break;
								}
								
								if ($cod_servico_b_det != $cod_servicio_aux_ant) {
									$nombre_bilateral_b_det = "";
									foreach ($arr_bilateralidades as $bilateralidad_aux) {
										if ($bilateralidad_aux["id"] == $det_aux["tipo_bilateral"]) {
											$nombre_bilateral_b_det = $bilateralidad_aux["valor"];
											break;
										}
									}
					?>
                    <input type="hidden" name="hdd_tipo_precio_b_det_<?= $i."_".$j ?>" id="hdd_tipo_precio_b_det_<?= $i."_".$j ?>" value="<?= $det_aux["tipo_producto"] ?>" />
                    <input type="hidden" name="hdd_cod_servicio_b_det_<?= $i."_".$j ?>" id="hdd_cod_servicio_b_det_<?= $i."_".$j ?>" value="<?= $cod_servico_b_det ?>" />
                    <input type="hidden" name="hdd_nombre_servicio_b_det_<?= $i."_".$j ?>" id="hdd_nombre_servicio_b_det_<?= $i."_".$j ?>" value="<?= $nombre_servico_b_det ?>" />
                    <input type="hidden" name="hdd_tipo_bilateral_b_det_<?= $i."_".$j ?>" id="hdd_tipo_bilateral_b_det_<?= $i."_".$j ?>" value="<?= $det_aux["tipo_bilateral"] ?>" />
                    <input type="hidden" name="hdd_nombre_bilateral_b_det_<?= $i."_".$j ?>" id="hdd_nombre_bilateral_b_det_<?= $i."_".$j ?>" value="<?= $nombre_bilateral_b_det ?>" />
                    <input type="hidden" name="hdd_valor_b_det_<?= $i."_".$j ?>" id="hdd_valor_b_det_<?= $i."_".$j ?>" value="<?= $det_aux["valor"] ?>" />
                    <input type="hidden" name="hdd_valor_cuota_b_det_<?= $i."_".$j ?>" id="hdd_valor_cuota_b_det_<?= $i."_".$j ?>" value="<?= $det_aux["valor_cuota"] ?>" />
                    <?php
									$j++;
								}
								
								$cod_servicio_aux_ant = $cod_servico_b_det;
							}
					?>
                    <input type="hidden" name="hdd_cantidad_b_det_<?= $i ?>" id="hdd_cantidad_b_det_<?= $i ?>" value="<?= $j ?>" />
                    <?php
						}
						
						echo($cod_servicio_aux);
					?>
                </td>
                <td style="text-align: left;"><?php echo($texto_tipo_precio_aux); ?></td>
                <td style="text-align: left;"><?php echo($nombre_servicio_aux); ?></td>
                <td align="center"><?php echo($texto_tipo_bilateral_aux); ?></td>
                <td style=" text-align: right;"><?php echo("$".number_format($value["valor"])); ?></td>
            </tr>
            <?php
					}
				} else {
			?>
            <tr>
                <td colspan="5">No hay resultados</td>
            </tr>
            <?php
				}
			?>
        </table>
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
        <?php
			break;
			
		case "7"://Cambia los valores con base en el plan seleccionado
			@$id_plan = $utilidades->str_decode($_POST["id_plan"]);
			@$fecha_pago = $utilidades->str_decode($_POST["fecha_pago"]);
			@$cant_productos = intval($_POST["cant_productos"], 10);
			
			$arr_productos = array();
			for ($i = 1; $i <= $cant_productos; $i++) {
				@$arr_productos[$i]["cod_producto"] = $utilidades->str_decode($_POST["cod_producto_".$i]);
				@$arr_productos[$i]["tipo_precio"] = $utilidades->str_decode($_POST["tipo_precio_".$i]);
				@$arr_productos[$i]["tipo_bilateral"] = $utilidades->str_decode($_POST["tipo_bilateral_".$i]);
			}
			
			//Se busca el tipo de pago asociado al plan
			$plan_obj = $dbPlanes->getPlan($id_plan);
			$tipo_pago = "";
			$ind_desc_cc = "";
			if (isset($plan_obj["ind_tipo_pago"])) {
				$tipo_pago = $plan_obj["ind_tipo_pago"];
				$ind_desc_cc = $plan_obj["ind_desc_cc"];
			}
			
			$lista_precios = $dbListasPrecios->getListasPreciosPorPlanParametro($id_plan, "", $fecha_pago);
			
			$cadena_texto = "";
			foreach ($lista_precios as $precio_aux) {
				//Se valida si el precio corresponde
				$ind_hallado = false;
				foreach ($arr_productos as $producto_aux) {
					if ($producto_aux["tipo_precio"] == $precio_aux["tipo_precio"] && $producto_aux["tipo_bilateral"] == $precio_aux["tipo_bilateral"]) {
						switch ($precio_aux["tipo_precio"]) {
							case "P":
								if ($producto_aux["cod_producto"] == $precio_aux["cod_procedimiento"]) {
									$ind_hallado = true;
								}
								break;
							case "M":
								if ($producto_aux["cod_producto"] == $precio_aux["cod_medicamento"]) {
									$ind_hallado = true;
								}
								break;
							case "I":
								if ($producto_aux["cod_producto"] == $precio_aux["cod_insumo"]) {
									$ind_hallado = true;
								}
								break;
						}
						
						if ($ind_hallado) {
							break;
						}
					}
				}
				
				if ($ind_hallado) {
					$codigo_aux = "";
					switch ($precio_aux["tipo_precio"]) {
						case "P":
							$codigo_aux = $precio_aux["cod_procedimiento"];
							break;
						case "M":
							$codigo_aux = $precio_aux["cod_medicamento"];
							break;
						case "I":
							$codigo_aux = $precio_aux["cod_insumo"];
							break;
					}
					
					$valor_aux = "";
					/*if ($tipo_pago == "1") {
						$valor_aux = $precio_aux["valor_cuota"];
					} else if ($tipo_pago == "2") {*/
						$valor_aux = $precio_aux["valor"];
					//}
					
					if ($cadena_texto != "") {
						$cadena_texto .= "|";
					}
					$cadena_texto .= $codigo_aux.":".$precio_aux["tipo_precio"].":".$precio_aux["tipo_bilateral"].":".$valor_aux;
				}
			}
		?>
        <input type="hidden" id="hdd_tipo_pago_val" value="<?php echo($tipo_pago); ?>" />
        <input type="hidden" id="hdd_desc_cc_val" value="<?php echo($ind_desc_cc); ?>" />
        <input type="hidden" id="hdd_cadena_precios_val" value="<?php echo($cadena_texto); ?>" />
        <?php
			break;
			
		case "8": //Carga de nuevo el combo box de planes
			$idConvenio = $utilidades->str_decode($_POST["idConvenio"]);
			$idContador = $utilidades->str_decode($_POST["idContador"]);
			
			$combo->getComboDb("cmb_plan".$idContador."", "", $dbPlanes->getListaPlanesActivos($idConvenio), "id_plan, nombre_plan", "Seleccione el plan", "cambiarPlan('cmb_plan".$idContador."', '".$idContador."');", "", "width: 200px;margin:0;");
			
			$convenio_obj = $dbConvenios->getConvenio($idConvenio);
			?>
        <input type="hidden" id="hdd_ind_num_aut_aux" name="hdd_ind_num_aut_aux" value="<?php echo($convenio_obj["ind_num_aut"]); ?>" />
        <input type="hidden" id="hdd_ind_num_aut_obl_aux" name="hdd_ind_num_aut_obl_aux" value="<?php echo($convenio_obj["ind_num_aut_obl"]); ?>" />
         <input type="hidden" id="hdd_ind_num_carnet" name="hdd_ind_num_carnet" value="<?php echo($convenio_obj["ind_num_carnet"]); ?>" />
        <?php
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
        <input type="hidden" id="hdd_email_b" value="<?php echo($paciente_obj["email"]); ?>" />
        <?php
			}
			break;
			
		case "11": //Borrar un pago
			var_dump($_POST);
			@$id_pago = $utilidades->str_decode($_POST["id_pago"]);
			@$observaciones_pago = $utilidades->str_decode($_POST["observaciones_pago"]);
			@$id_causal_borra = $utilidades->str_decode($_POST["id_causal_borra"]);
			@$num_nota_credito = $utilidades->str_decode($_POST["num_nota_credito"]);
			
			$resultado = -1;
			
			$pago_obj = $dbPagos->get_pago_id($id_pago);
			
			
			//Se hallan los datos de compañía asociados al lugar del pago
			$sede_det_obj = $dbListas->getSedesDetalle($pago_obj["id_lugar_cita"]);
			$compania = $sede_det_obj["id_compania"];
			$co_ventas = $sede_det_obj["id_co_ventas"];
			$bodega_ventas = $sede_det_obj["id_bodega_ventas"];
			
			if ($pago_obj["estado_pago"] == "1" || $pago_obj["id_medio_pago"] == "99" ||  
				$pago_obj["id_medio_pago"] == "100" || $num_nota_credito <> "") {
				//Pago no registrado, solamente se marca como borrado
				$resultado = $dbPagos->borrar_pago($id_pago, $id_usuario, $observaciones_pago, $id_causal_borra, $num_nota_credito);
			} else if ($pago_obj["estado_pago"] == "2") {
				//El pago se encuentra registrado, se requiere nota crédito
				
				$lista_pagos_detalle = $dbPagos->get_lista_pagos_detalle($id_pago);
				$valor_copagos = 0;
				foreach ($lista_pagos_detalle as $detalle_aux) {
					$valor_copagos += $detalle_aux["valor_cuota"];
				}
				
				//Se verifica que el tercero (paciente o responsable) exista en SIESA
				if ($pago_obj["id_tercero"] != "") {
					$tercero_obj = $dbTerceros->getTercero($pago_obj["id_tercero"]);
					$tipo_doc_tercero = $tercero_obj["codigo_doc_siesa"];
					$num_doc_tercero = $tercero_obj["numero_documento"];
					
					$resultado_aux = $classTercerosSiesa->crearTerceroClienteNoPaciente($pago_obj["id_tercero"], $pago_obj["id_paciente"], $compania,$id_usuario);
				} else {
					$paciente_obj = $dbPacientes->getExistepaciente3($pago_obj["id_paciente"]);
					$tipo_doc_tercero = $paciente_obj["codigo_doc_siesa"];
					$num_doc_tercero = $paciente_obj["numero_documento"];
					
					$resultado_aux = $classTercerosSiesa->crearTerceroCliente($pago_obj["id_paciente"], $compania, $pago_obj["id_lugar_cita"], $id_usuario);
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
				
				$convenio_obj = $dbConvenios->getConvenio($pago_obj["id_convenio"]);
				$plan_obj = $dbPlanes->getPlan($pago_obj["id_plan"]);
				
				//Se busca el tipo de pago asociado al plan
				$ind_tipo_pago = $plan_obj["ind_tipo_pago"];
				
				//Se verifica que el tercero del convenio se encuentre configurado en SIESA obteniendo su condición de pago
				$condicion_pago_entidad = "";
				if ($resultado_aux == 1 && $ind_tipo_pago == "1") {
					//El plan no es de pago directo (particular)
					$num_doc_tercero_aux = $convenio_obj["numero_documento"];
					
					$resultado_aux = $classConsultasSiesa->consultarCondicionPagoTercero($compania, $num_doc_tercero_aux);
					if ($resultado_aux != -1) {
						$condicion_pago_entidad = $resultado_aux;
						$resultado_aux = 1;
					}
		?>
		<input type="hidden" id="hdd_resul_tercero_entidad_siesa" name="hdd_resul_tercero_entidad_siesa" value="<?= $resultado_aux ?>" />
		<?php
				}
				
				if ($resultado_aux == 1) {
					//Se obtiene el número de documento del usuario
					$usuario_obj = $dbUsuarios->getUsuario($id_usuario);
					
					if ($ind_tipo_pago == "2" || $valor_copagos > 0) {
						//Se crea una nota crédito
						$resul_nota_credito = $classNotasCreditoSiesa->crearNotaCredito($compania, $co_ventas, $bodega_ventas,
								$usuario_obj["numero_documento"], $id_pago, $ind_tipo_pago, $id_causal_borra, $id_usuario);
						
						$num_nota_credito = $resul_nota_credito[1];
						$resul_nota_credito = $resul_nota_credito[0];
						
						if ($resul_nota_credito == 1) {
		?>
		<input type="hidden" id="hdd_resul_nota_credito_siesa" name="hdd_resul_nota_credito_siesa" value="1" />
		<input type="hidden" id="hdd_num_nota_credito_siesa" name="hdd_num_nota_credito_siesa" value="<?= $num_nota_credito ?>" />
		<input type="hidden" id="hdd_num_pedido_nota_credito_siesa" name="hdd_num_pedido_nota_credito_siesa" value="<?= $pago_obj["num_pedido"] ?>" />
		<?php
						} else {
		?>
		<input type="hidden" id="hdd_resul_nota_credito_siesa" name="hdd_resul_nota_credito_siesa" value="-1" />
		<input type="hidden" id="hdd_mensaje_nota_credito_siesa" name="hdd_mensaje_nota_credito_siesa" value="<?= $resul_nota_credito ?>" />
		<?php
							$resul_nota_credito = -1;
						}
					} else {
						$resul_nota_credito = 1;
					}
					
					if ($resul_nota_credito == 1) {
						$resultado = $dbPagos->borrar_pago($id_pago, $id_usuario, $observaciones_pago, $id_causal_borra, $num_nota_credito);
					}
				}
			}
		?>
        <input type="hidden" id="hdd_resul_borrar" value="<?php echo($resultado); ?>" />
        <?php
			break;
			
		case "12"://Imprime formulario flotante de búsqueda de terceros
			@$idcontador = $utilidades->str_decode($_POST["idcontador"]);
		?>
        <div class="encabezado">
            <h3>Agregar terceros</h3>
        </div>
        <div>
            <form id="frmListadoTerceros" name="frmListadoTerceros">
                <table style="width: 100%;">
                    <tbody>
                        <tr valign="middle">
                            <td align="center" colspan="2">
                                <div id="advertenciasg"></div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" id="txtParametroTerceros" name="txtParametroTerceros" placeholder="N&uacute;mero de documento o nombre del tercero" onblur="trim_cadena(this);" />
                            </td>
                            <td style="width: 8%;">
                                <input type="submit" value="Buscar" class="btnPrincipal peq" onclick="buscar_terceros_pago(<?php echo($idcontador); ?>);" />
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
			@$id_contador = $utilidades->str_decode($_POST["id_contador"]);
			
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
            <input type="button" id="btn_nuevo_tercero" value="Nuevo tercero" class="btnPrincipal peq" onclick="cargar_formulario_nuevo_tercero(<?php echo($id_contador); ?>);" />
        </div>
        <br />
        <?php
			break;
			
		case "14": //Selección de anticipos disponibles
			@$indice = $utilidades->str_decode($_POST["indice"]);
			@$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
			@$id_tercero = $utilidades->str_decode($_POST["id_tercero"]);
			@$id_pago = $utilidades->str_decode($_POST["id_pago"]);
			@$estado_pago = $utilidades->str_decode($_POST["estado_pago"]);
			
			if ($estado_pago == "1") {
				$lista_anticipos = $dbAnticipos->get_lista_anticipos_disponibles($id_paciente, $id_tercero);
			} else {
				$lista_anticipos = $dbAnticipos->get_lista_anticipos_pago($id_pago, "");
			}
		?>
        <input type="hidden" id="hdd_cant_anticipos_<?= $indice ?>" name="hdd_cant_anticipos_<?= $indice ?>" value="<?= count($lista_anticipos) ?>" />
        <table border="0" style="width:100%;">
        	<?php
				if (count($lista_anticipos) > 0) {
					for ($i = 00; $i < count($lista_anticipos); $i++) {
						$anticipo_aux = $lista_anticipos[$i];
			?>
            <tr>
            	<td align="left" colspan="4">
                    <input type="hidden" id="hdd_id_anticipo_<?= $indice."_".$i ?>" name="hdd_id_anticipo_<?= $indice."_".$i ?>" value="<?= $anticipo_aux["id_anticipo"] ?>" />
                    <input type="hidden" id="hdd_saldo_anticipo_<?= $indice."_".$i ?>" name="hdd_saldo_anticipo_<?= $indice."_".$i ?>" value="<?= $anticipo_aux["saldo"] ?>" />
                	<b>No.:&nbsp;</b><?= $anticipo_aux["id_anticipo"] ?>
                    &nbsp;&nbsp;&nbsp;
                    <b>No. externo:&nbsp;</b><?= $anticipo_aux["num_anticipo"] ?>
                    &nbsp;&nbsp;&nbsp;
                    <b>Saldo:&nbsp;</b>$<?= str_replace(",", ".", number_format($anticipo_aux["saldo"])) ?>
                </td>
            </tr>
            <?php
				if ($anticipo_aux["id_tercero"] != "") {
			?>
            <tr>
            	<td align="left" colspan="4">
                	<b>Tercero:&nbsp;</b><?= $anticipo_aux["nombre_tercero"] ?>
                </td>
            </tr>
            <?php
				}
			?>
            <tr>
                <td align="right" style="width:20%;">
                	<?php
                    	if ($estado_pago == "1") {
					?>
                	<b>Seleccione&nbsp;</b>
                    <?php
						}
					?>
                </td>
                <td align="left" style="width:15%;">
                	<?php
                    	if ($estado_pago == "1") {
					?>
                	<input type="checkbox" id="chk_anticipo_<?= $indice."_".$i ?>" name="chk_anticipo_<?= $indice."_".$i ?>" class="no-margin" onchange="seleccionar_anticipo(<?= $indice ?>, <?= $i ?>);" />
                    <?php
						}
					?>
                </td>
                <td align="right" style="width:20%;">
                	<b>Valor&nbsp;</b>
                </td>
                <td align="left" style="width:45%;">
                	<?php
                    	if ($estado_pago == "1") {
							$valor_aux = "0";
						} else {
							$valor_aux = $anticipo_aux["valor_pago"];
						}
					?>
                	<input type="text" id="txt_valor_anticipo_<?= $indice."_".$i ?>" name="txt_valor_anticipo_<?= $indice."_".$i ?>" value="<?= $valor_aux ?>" class="no-margin" onkeypress="solo_numeros(event, false);" onblur="totalizar_anticipos(<?= $indice ?>);" disabled="disabled" />
                </td>
            </tr>
            <tr>
                <td colspan="4"><hr class="no-margin" /></td>
            </tr>
            <?php
					}
				} else {
			?>
            <tr>
            	<td align="center">
                	<span class="rojo">No se hallaron anticipos para el paciente o tercero</span>
                </td>
            </tr>
            <?php
				}
			?>
        </table>
        <?php
			break;
		
		case "15":
			@$id_pago = $utilidades->str_decode($_POST["id_pago"]);
			@$id_lugar_cita = $utilidades->str_decode($_POST["id_lugar_cita"]);
			@$numero_documento = $utilidades->str_decode($_POST["numero_documento"]);
			
			$sede_det_obj = $dbListas->getSedesDetalle($id_lugar_cita);
			$compania = $sede_det_obj["id_compania"];
			$co_ventas = $sede_det_obj["id_co_ventas"];
			//echo($co_ventas);
			$lista_pedidos_ant = $classConsultasSiesa->consultarPedidoEstados($compania, $id_pago);
			;
			$num_pedido_ant = "";
			foreach ($lista_pedidos_ant as $pedido_aux) {
				if ($pedido_aux["ESTADO"] != "9") {
					$num_pedido_ant = $pedido_aux["DOCUMENTO"];
					break;
				}
			}
			
			$num_factura_ant = "";
			$num_factura_ant = $classConsultasSiesa->consultarFacturaPago($compania, $id_pago);
			$num_nota_credito_ant = "";
			$num_nota_credito_ant = $classConsultasSiesa->consultarUltimoDocumentoTercero($compania, $co_ventas, "NCE", $numero_documento);
			$num_nota_credito_ant = "";
			
		?>
			N&uacute;mero de factura: <?= ($num_factura_ant == "" || $num_factura_ant == "-1" ? "-" : $num_factura_ant) ?>
			<input type="hidden" id="hdd_num_factura_siesa" value="<?= $num_factura_ant ?>">
			<br>
			N&uacute;mero de pedido: <?= ($num_pedido_ant == "" ? "-" : $num_pedido_ant) ?>
			<input type="hidden" id="hdd_num_pedido_siesa" value="<?= $num_pedido_ant ?>">
			<br>
			N&uacute;mero nota cr&eacute;dito: <?= ($num_nota_credito_ant == "" || $num_nota_credito_ant == "-1" ? "-" : $num_nota_credito_ant) ?>
			<input type="hidden" id="hdd_num_nota_siesa" value="<?= $num_nota_credito_ant ?>">
        <?php
			break;
			
			
		case "16":
			@$id_pago = $utilidades->str_decode($_POST["id_pago"]);
			@$num_pedido = $utilidades->str_decode($_POST["num_pedido"]);
			@$num_factura = $utilidades->str_decode($_POST["num_factura"]);
			@$num_nota = $utilidades->str_decode($_POST["num_nota"]);
			
			$num_factura = $num_factura == "-1" || $num_factura == -1 ? "" : $num_factura;
			$num_pedido = $num_pedido == "-1" || $num_pedido == -1 ? "" : $num_pedido;
			$num_nota = $num_nota == "-1" || $num_nota == -1 ? "" : $num_nota;
			
			$resultado = -1;
			if(!empty($num_pedido) ||!empty($num_factura) || !empty($num_nota) ){
				$resultado = $dbPagos->actualizarPagoDatos($id_pago, $num_pedido, $num_factura, $num_nota);
			}
		
		
		?>
			<input type="hidden" id="hdd_result_actualizar_pago" value="<?= $resultado ?>">
		<?php
		

		break;
	}
?>
