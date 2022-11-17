<?php
/*
  Pagina para crear formularios de remisiones en citas
  Autor: Feisar Moreno - 01/06/2015
 */
require_once("../db/DbAdmision.php");
require_once("../db/DbHistoriaClinica.php");
require_once("../db/DbMaestroExamenes.php");
require_once("../db/DbTiposCitasDetalle.php");
require_once("../db/DbListas.php");
require_once("Class_Combo_Box.php");
require_once("../funciones/Utilidades.php");
require_once("../db/Dbremisiones.php");
require_once("../db/DbFormulasMedicamentos.php");
require_once("../db/DbOrdenesMedicas.php");
require_once("../db/DbPacientes.php");
require_once("../db/DbConvenios.php");
require_once("../db/DbPlanes.php");

class Class_Ordenes_Remisiones {

    public function getFormularioRemisiones($id_hc, $tipo = 1, $ind_editar = 0) {
        switch ($tipo) {
            case 1:/* Remisión UT */
                $dbListas = new DbListas();
                $combo = new Combo_Box();
                $utilidades = new Utilidades();
                $dbRemisiones = new DbRemisiones();

                $remisiones = $dbRemisiones->getRemisionesActivasByHc($id_hc);
                $cantidadRemisiones = count($remisiones);
                ?>
                <fieldset class="grupo_formularios" id="seccion_gafas"> 						
                    <legend style="color: #012996;font-weight: bold;">Remisiones</legend>
                    <input type="hidden" name="cant_remisiones" id="cant_remisiones" value="<?= $cantidadRemisiones ?>" />
                    <div>
                        <div>
                            <table style="width: 150; margin-bottom: 20px;">
                                <tr>
                                    <td>
                                        Ver remisi&oacute;n:
                                        <select name="cmb_num_remision" id="cmb_num_remision" style="width:50px;" onchange="mostrar_remision(this.value)">
                                            <?php
                                            for ($i = 0; $i < $cantidadRemisiones; $i++) {
                                                ?>
                                                <option value="<?php echo($i); ?>"><?php echo($i + 1); ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>                                       
                                        <?php
                                        if ($ind_editar == 1) {
                                            ?>
                                            <div class="restar_alemetos" onclick="restar_remision();" title="Eliminar Agregar remisi&oacute;n"></div>
                                            <div id="btn_add_gafas" class="agregar_alemetos" onclick="agregar_remision();" title="Agregar remisi&oacute;n"></div>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                <tr>
                                    <td>
                                        <div id="resultadoRemision" style="display:none;"></div>
                                        <div id="d_impresion_remision" style="display:none;"></div>
                                    </td>
                                </tr>
                                </tr>
                            </table>                           
                        </div>

                        <?php
                        for ($i = 0; $i < 10; $i++) {
                            $display = "display:none;";
                            $idRemision = 0;
                            $tipoRemision = "";
                            $observacion = "";
                            if ($cantidadRemisiones > 0 && $i < $cantidadRemisiones) {
                                $i == 0 ? $display = "display:block;" : $display = "display:none;";
                                $idRemision = $remisiones[$i]['id_remision'];
                                $tipoRemision = $remisiones[$i]['id_tipo_remision'];
                                $observacion = $remisiones[$i]['desc_remision'];
                            }
                            ?>
                            <div id="tabla_rem_<?= $i ?>" style="<?= $display ?>" class="div_formula">
                                <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:90%;padding-right: 15px;" >
                                    <tr>
                                        <td colspan="2" class="text-center">
                                            <h5>Remisi&oacute;n #<?= ($i + 1) ?></h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 20%;text-align: right;">
                                            <label>Tipo de remisi&oacute;n</label>
                                            <input type="hidden" id="hdd_idRemision_<?= $i ?>" name="hdd_idRemision_<?= $i ?>" value="<?= $idRemision ?>" />
                                        </td>
                                        <td>
                                            <?php
                                            $lista_remisiones = $dbListas->getListaDetalles(75);
                                            $combo->getComboDb("cmb_tipo_remision_" . $i, $tipoRemision, $lista_remisiones, "id_detalle, nombre_detalle", "-- Seleccione el tipo de remisi&oacute;n --", "", true, "width:100%;");
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 20%;text-align: right;">
                                            <label>Observaci&oacute;n</label>
                                        </td>
                                        <td>
                                            <div id="tabla_rem_desc_<?= $i ?>"><?= $utilidades->ajustar_texto_wysiwyg($observacion) ?></div>
                                        </td>
                                    </tr>
                                </table>

                                <table style="width:90%;">

                                    <tr>
                                        <?php
                                        if ($ind_editar == 1) {

                                            $accion = "guardar_remision($i);"; /* Guardar - Imprimir */
                                            $mensaje = "Guardar remisi&oacute;n #" . ($i + 1);

                                            if ($idRemision > 0) {
                                                $mensaje = "Actualizar/Imprimir remisi&oacute;n #" . ($i + 1);
                                            }
                                            ?>
                                            <td colspan="" style="">
                                                <span style="background: #FDEBB7;padding: 6px;color: #E11111;">NOTA:</span> <span style="background: #FDEBB7;padding: 6px;">Oprima el bot&oacute;n de la derecha para guardar los cambios</span>
                                            </td>
                                            <?php
                                        } else {
                                            $accion = "imprimir_remision($i);"; /* Imprimir */
                                            $mensaje = "Imprimir remisi&oacute;n #" . ($i + 1);
                                        }
                                        ?>
                                        <td colspan="" style="text-align: right;">
                                            <input type="button" id="btn_imprimir_rxfinal" nombre="btn_imprimir_rxfinal" class="btnPrincipal peq" style="font-size: 16px;" value="<?= $mensaje ?>" onclick="<?= $accion ?>" />
                                        </td>
                                    </tr>

                                </table>

                            </div>

                            <?php
                        }
                        ?>
                    </div>   
                </fieldset>                
                <?php
                break;
        }
    }

    public function getFormularioOrdenarMedicamentos($id_hc = NULL, $idPaciente = NULL, $tipo = 1, $ind_editar = 0) {

        $tituloGeneral = "";

        switch ($tipo) {
            case 1:/* Ordenar medicamentos UT */
                $tituloGeneral = "Formular medicamentos";
                break;
            case 2:/* Homologar medicamentos UT */
                $tituloGeneral = "Homologar fórmula";
                break;
        }

        $utilidades = new Utilidades();
        $dbformulasMedicamentos = new DbFormulasMedicamentos();
		$dbListas = new DbListas();
        $cantidadFormulaMedicamentos = 0;
        $cantidadMedicamentos = 0;
        $idFormulacionMedicamentos = "";
        $displayBtnImprimir = "display:none";
        $tipoFormulacion = 0;
        $tipoAccionBtnFormular = "";
        $idConvenio = "";
        $idPlan = "";
        $tipoCotizante = "";
        $rango = "";
        $statusConvenio = "";
	
		$dbAdmisiones = new DbAdmision();
		 /* Consultar la ultima admision del paciente*/
		$lista_ultima_admision = $dbAdmisiones->get_ultima_admision($idPaciente);
		$idConvenioAdm = $lista_ultima_admision['id_convenio'];
		$idPlanAdm =  $lista_ultima_admision['id_plan'];
								

							

        if (isset($id_hc)) {/* Sí la función es llamada con id de H.C. */
            /* Verifica si la H.C. contiene formulación de medicamentos */
            $formulaMedicamento = $dbformulasMedicamentos->getFormulaActivaByHc($id_hc);
            $cantidadFormulaMedicamentos = count($formulaMedicamento['id_hc']);
            $tipoFormulacion = 1; //Asigna tipo de formulación Directa desde UT
            $tipoAccionBtnFormular = "formular_medicamento";
			
        }else {
            $tipoFormulacion = 2; //Asigna tipo de formulación Homologada
            $tipoAccionBtnFormular = "homologar_formular_medicamento";

            $combo = new Combo_Box();
            $dbConvenios = new DbConvenios();
            $dbPlanes = new DbPlanes();
            $dbPacientes = new DbPacientes();
            $paciente = $dbPacientes->getExistepaciente3($idPaciente);
            $idConvenio = $paciente['id_convenio_paciente'];
            $idPlan = $paciente['id_plan'];
            $tipoCotizante = $paciente['tipo_coti_paciente'];
            $rango = $paciente['rango_paciente'];
            $statusConvenio = $paciente['status_convenio_paciente'];
        }

        if ($cantidadFormulaMedicamentos > 0) {
            $medicamentos = $dbformulasMedicamentos->getMedicamentosActivosByFormula($formulaMedicamento['id_formula_medicamento']);
            $cantidadMedicamentos = count($medicamentos);
            $idFormulacionMedicamentos = $formulaMedicamento['id_formula_medicamento'];
            $displayBtnImprimir = "display:inline-table";
        }
        ?>
        <fieldset class="grupo_formularios" id="seccion_gafas"> 						
            <legend style="color: #012996;font-weight: bold;"><?= $tituloGeneral ?></legend>
            <?php
            if ($tipoFormulacion == 2) {
                ?>
                <div style="display: none;" id="divNumeroFormulacion">
                    <h5>C&oacute;digo de la formulaci&oacute;n #<span style="color: #399000;font-weight: bold;"></span></h5>                    
                </div>
                <br> 
                <table style="width: 550px; margin-bottom: 20px; margin: 0 auto;">
                
                    <tr>
                        <td align="right">
                            <label class="inline" for="cmb_convenio"><b>Convenio/Entidad&nbsp;</b></label>
                        </td>
                        <td align="left">
                            <?php
                            $disabled = 1;
                            $lista_convenios = $dbConvenios->getListaConveniosActivos();
							 if(isset($lista_ultima_admision)){ /*Si el paciente tiene admision, muestra el convenio de esa última admision*/
								
					 			$combo->getComboDb("cmb_convenio", $idConvenioAdm, $lista_convenios, "id_convenio, nombre_convenio", "-- Seleccione el convenio --", "seleccionar_convenio(this.value)", $disabled, "width:100%;");
								}else{

                            $combo->getComboDb("cmb_convenio", $idConvenio, $lista_convenios, "id_convenio, nombre_convenio", "-- Seleccione el convenio --", "seleccionar_convenio(this.value)", $disabled, "width:100%;");
								}
                            ?>
                        </td>
                        <td align="right">
                            <label class="inline" for="cmb_convenio"><b>Plan*:</b></label>
                        </td>
                        <td align="left">
                            <div id="d_plan">                                                            
                                <?php
                                $disabled = 1;

                                $combo = new Combo_Box(); 
                               
								//$lista_planes = $dbPlanes->getListaPlanesActivos($idConvenio);
								
								if(isset($lista_ultima_admision)){
									//var_dump($lista_ultima_admision);
									$lista_planes = $dbPlanes->getListaPlanesActivos($idConvenioAdm);
									$combo->getComboDb("cmb_cod_plan", $idPlanAdm, $lista_planes, "id_plan, nombre_plan", "-- Seleccione el plan --", "", $disabled, "width:100%;");
								}else{
									$lista_planes = $dbPlanes->getListaPlanesActivos($idConvenio);
                                	$combo->getComboDb("cmb_cod_plan", $idPlan, $lista_planes, "id_plan, nombre_plan", "-- Seleccione el plan --", "", $disabled, "width:100%;");
								}
                                ?>
                            </div>
                        </td>                                                                        
                    </tr>
                    <tr>
                        <td align="right">
                            <label class="inline" for="cmb_convenio"><b>Tipo de cotizante*:</b></label>
                        </td>
                        <td align="left">
                            <?php
                            $disabled = 1;
                            if ($ind_reasignar == 1) {
                                $disabled = 0;
                            }
						
                           /* $array_tipoCotizante = array();
                            $array_tipoCotizante[0][0] = "0";
                            $array_tipoCotizante[0][1] = "No aplica";
                            $array_tipoCotizante[1][0] = "1";
                            $array_tipoCotizante[1][1] = "Cotizante";
                            $array_tipoCotizante[2][0] = "2";
                            $array_tipoCotizante[2][1] = "Beneficiario";

                            $combo->get("cmb_tipoCotizante", $tipoCotizante, $array_tipoCotizante, "-- Seleccione el tipo de cotizante --", "", $disabled, "width:100%;"); */
							$dbListas = new DbListas();
							$lista_tipo_cotizante = $dbListas->getListaDetalles(99);
							$combo->getComboDb("cmb_tipoCotizante", $tipoCotizante, $lista_tipo_cotizante, "id_detalle, nombre_detalle", "-- Seleccione --", "", $disabled,"width:100%;");
                            ?>
                        </td>

                        <td align="right">
                            <label class="inline" for="cmb_convenio"><b>Rango*:</b></label>
                        </td>
                        <td align="left">
                            <?php
                            $disabled = 1;
                            if ($ind_reasignar == 1) {
                                $disabled = 0;
                            }

                            $array_rango = array();
                            $array_rango[0][0] = "0";
                            $array_rango[0][1] = "No aplica";
                            $array_rango[1][0] = "1";
                            $array_rango[1][1] = "Uno";
                            $array_rango[2][0] = "2";
                            $array_rango[2][1] = "Dos";
                            $array_rango[3][0] = "3";
                            $array_rango[3][1] = "Tres";

                            $combo->get("cmb_rango", $rango, $array_rango, "-- Seleccione el rango --", "", $disabled, "width:100%;");
                            ?>
                            <input type="hidden" id="hdd_id_convenio_pc" name="hdd_id_convenio_pc" value="<?= $idConvenio ?>" />
                            <input type="hidden" id="hdd_id_plan_pc" name="hdd_id_plan_pc" value="<?= $idPlan ?>" />
                            <input type="hidden" id="hdd_status_convenio" name="hdd_status_convenio" value="<?= $statusConvenio ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%;text-align: right;">
                            <label>Medico remitente</label>                                            
                        </td>
                        <td style="text-align: left;color: #399000;font-weight: bold;" colspan="3">                                       
                            <input style="margin: 0" type="text" id="medicoRemitenteHomologacion" value=" <?php //echo(($_SESSION['nomUsuario']));?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%;text-align: right;">
                        <?php
						/* Tomar la fecha actual y mostrarla en el input text: Fecha de creaci&oacute;n de la formulación por homologar*/
						 	date_default_timezone_set('America/Bogota');
						  	setlocale(LC_TIME, 'spanish');
						  	$fecha_hoy = getdate(); 
						 	$dia = $fecha_hoy['mday'];
						 	$mes = $fecha_hoy['mon'];
							$anyo = $fecha_hoy['year'];

						  if(mb_strlen($dia) <=1){
							$dia = (str_pad($dia, 2, 0, STR_PAD_LEFT));
						  }
						  if(mb_strlen($mes) <=1){
							$mes = (str_pad($mes, 2, 0, STR_PAD_LEFT));
						  }
						  $fecha_hoy_aux = ($dia."/".$mes."/".$anyo);
						  
						?>
                            <label>Fecha de creaci&oacute;n de la formulación por homologar</label>                                            
                        </td>
                        <td style="text-align: left;color: #399000;font-weight: bold;" colspan="3">

                            <input type="text" maxlength="10"  name="fechaFormulacionHomologacion" style="width: 120px;" id="fechaFormulacionHomologacion" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" value="<?php echo($fecha_hoy_aux);                                                 ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%;text-align: right;">
                            <label>Observaci&oacute;n </label>                                            
                        </td>
                        <td style="text-align: left;color: #399000;font-weight: bold;" colspan="3">
                            <textarea rows="4" cols="50" style="height:100px;" id="observacionHomologacion" name="observacionHomologacion"></textarea>

                        </td>
                    </tr>
                </table>
                <br/>
                <br/>
                <br/>
                
                 <?php
					$ind_ojos=true;
					for ($i = 1; $i <= 1; $i++) {
						$tabla_diagnosticos = array();//$this->obtener_diagnostico($tabla_hc_diagnosticos, $i);
						if (count($tabla_diagnosticos) == 0) {
							$cod_ciex = "";
							$val_ojo = "";
							$texto_ciex = "";
						} else {
							$cod_ciex = $tabla_diagnosticos[0];
							$val_ojo = $tabla_diagnosticos[1];
							$texto_ciex = $tabla_diagnosticos[2];
						}
           		?>
                   <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:59%;">
                   <tr>
                        <td align="center" style="width:15%;">
                    <h7><b>C&oacute;d. CIEX</b></h7>
                </td>
                <?php
                if ($ind_ojos) {
                    ?>
                    <td align="center" style="width:70%;" colspan="2">
                    <h7><b>Diagn&oacute;stico</b></h7>
                    </td>
                    <td align="center" style="width:15%;">
                    <h7><b>Ojo</b></h7>
                    </td>
                    <?php
                } else {
                    ?>
                    <td align="center" style="width:88%;" colspan="2">
                    <h7><b>Diagn&oacute;stico</b></h7>
                    </td>
                    <?php
                }
                ?>
                </tr>
        </table>
                <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:59%; display:block;" id="tabla_diagnosticos">
                    <tr>
                        <td align="center" style="width:15%;">
                            <input type="text" name="ciex_diagnostico_<?php echo($i); ?>" id="ciex_diagnostico_<?php echo($i); ?>" value="<?php echo($cod_ciex); ?>" style="width:100px;" maxlength="6" class="input no-margin" onblur="convertirAMayusculas(this); trim_cadena(this); buscar_diagnosticos_ciex('<?php echo($i); ?>');" />
                            <input type="hidden" name="hdd_ciex_diagnostico_<?php echo($i); ?>" id="hdd_ciex_diagnostico_<?php echo($i); ?>" value="<?php echo($cod_ciex); ?>" />
                        </td>
                        <?php
                        if ($ind_ojos) {
                            $porcentaje_aux = 70;
                        } else {
                            $porcentaje_aux = 80;
                        }
                        ?>
                        <td align="left" style="width:<?php echo($porcentaje_aux); ?>%;">
                            <h6 class="no-margin">
                                <div id="texto_diagnostico_<?php echo($i); ?>"><?php echo($texto_ciex); ?></div>
                            </h6>
                        </td>
                        <td align="center" style="width:5%;">
                            <div class="d_buscar" onclick="abrir_buscar_diagnostico('<?php echo($i); ?>');"></div>
                        </td>
                        <?php
                        if ($ind_ojos) {
                            ?>
                            <td align="center" style="width:15%;">
                                <?php
								$tabla_ojos = $dbListas->getListaDetalles(14);
                                $combo->getComboDb("cmb_ojos_" . $i, $val_ojo, $tabla_ojos, "id_detalle, nombre_detalle", " ", "", "", "width:90px;", "", "select no-margin");
                                ?>
                            </td>
                            <?php
                        } else {
                            ?>
                        <input id="valor_ojos_<?php echo($i); ?>" name="valor_ojos_<?php echo($i); ?>" value="" type="hidden" />
                        <?php
                    }
                    ?>
                </tr>
                </table>
                <br/>
                <br/>
                <?php
            }
            ?>

                <div style="background: #FDEBB7;padding: 3px 5px;width: 70%;margin: 0 auto;">
                    <span>A continuaci&oacute;n agregue los medicamentos por despachar</span>
                </div>
                <br>                
                <?php
            }
            ?>
            <input type="hidden" name="cant_medicamentos" id="cant_medicamentos" value="<?= $cantidadMedicamentos ?>" />
            <input type="hidden" id="hdd_id_formulacion_medicamentos" name="hdd_id_formulacion_medicamentos" value="<?= $idFormulacionMedicamentos ?>" />
            <input type="hidden" id="hdd_idHC_ordenesRemisiones" name="hdd_idHC_ordenesRemisiones" value="<?= $id_hc ?>" />
            <input type="hidden" id="hdd_tipoFormulacion_ordenesRemisiones" name="hdd_tipoFormulacion_ordenesRemisiones" value="<?= $tipoFormulacion ?>" />
            <input type="hidden" id="hdd_idPaciente_ordenesRemisiones" name="hdd_idPaciente_ordenesRemisiones" value="<?= $idPaciente ?>" />
            <div id="rta_formulacion_medicamentos"></div>
            <div>
                <div>
                    <table style="width: 150; margin-bottom: 20px;">
                        <tr>
                            <td>
                                Ver medicamentos:
                                <select name="cmb_num_med" id="cmb_num_med" style="width:50px;" onchange="mostrar_medicamento(this.value)">
                                    <?php
                                    for ($i = 0; $i < $cantidadMedicamentos; $i++) {
                                        ?>
                                        <option value="<?php echo($i); ?>"><?php echo($i + 1); ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>                                       
                                <?php
                                if ($ind_editar == 1) {
                                    ?>
                                    <div class="restar_alemetos" onclick="eliminarFormularMedicamentos();" title="Eliminar medicamentos formulados"></div>
                                    <div id="btn_add_gafas" class="agregar_alemetos" onclick="agregar_formula_medicamentos();" title="Agregar fórmula medicamnetos"></div>
                                    <?php
                                }
                                ?>
                            </td>
                        <tr>
                            <td>
                                <div id="resultadoRemision"></div>
                            </td>
                        </tr>
                        </tr>
                    </table>                           
                </div>
                <?php
                for ($i = 0; $i < 10; $i++) {
                    $display = "display:none;";
                    $displayBtnBuscarMedicamento = "";
                    $displayBtnGuardarModificar = "";
                    $codMed = "";
                    $idFormMedDet = "";
                    $nomGen = "";
                    $nomCom = "";
                    $presMed = "";
                    $cantidad = "";
                    $tiempo = "";
                    $frecAdmMed = "";
                    $txtBtn = "Formular medicamento #".($i + 1);
                    $backgroundCodigo = "background: #F79999;";

                    if ($cantidadMedicamentos > 0 && $i < $cantidadMedicamentos) {
                        $estadoMedicamento = $medicamentos[$i]['estado_formula_medicamento'];
                        $i == 0 ? $display = "display:block;" : $display = "display:none;";
                        $codMed = $medicamentos[$i]['cod_medicamento'];
                        $idFormMedDet = $medicamentos[$i]['id_formula_medicamento_det'];
                        $nomGen = $medicamentos[$i]['nombre_generico'];
                        $nomCom = $medicamentos[$i]['nombre_comercial'];
                        $presMed = $medicamentos[$i]['presentacion'];
                        $cantidad = $medicamentos[$i]['cantidad_orden'];
                        $frecAdmMed = $medicamentos[$i]['forma_admin'];
                        $tiempo = $medicamentos[$i]['tiempo_formula_medicamento_det'];

                        $displayBtnBuscarMedicamento = "display:none;";

                        if ($estadoMedicamento != 1) {
                            $displayBtnGuardarModificar = "display:none;";
                        }
                        $backgroundCodigo = "background: #CEFDAF;";
                    }
                    ?>
                    <div id="tabla_med_<?= $i ?>" style="<?= $display ?>" class="div_formula">
                        <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:90%;padding-right: 15px;" >
                            <tr>
                                <td colspan="2" class="text-center">
                                    <h5>Medicamento #<?= ($i + 1) ?></h5>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: right;">
                                    <input type="button" id="btn_imprimir_rxfinal" nombre="btn_imprimir_rxfinal" class="btnPrincipal peq" style="font-size: 16px;<?= $displayBtnBuscarMedicamento ?>" value="Buscar medicamento" onclick="btnBuscarMedicamentos(<?= $i ?>);" />
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20%;text-align: right;">
                                    <label>C&oacute;digo</label>
                                    <input type="hidden" id="hdd_codMed_<?= $i ?>" name="hdd_codMed_<?= $i ?>" value="<?= $codMed ?>" />
                                    <input type="hidden" id="hdd_idFormMedDet_<?= $i ?>" name="hdd_idFormMedDet_<?= $i ?>" value="<?= $idFormMedDet ?>" />
                                </td>
                                <td style="text-align: left;color: #399000;font-weight: bold;">                                            
                                    <input style="margin: 0;<?= $backgroundCodigo ?>" disabled type="text" id="codMed_<?= $i ?>" value="<?= $codMed ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20%;text-align: right;">
                                    <label>Nombre gen&eacute;rico</label>                                            
                                </td>
                                <td style="text-align: left;color: #399000;font-weight: bold;">                                       
                                    <input style="margin: 0" disabled type="text" id="nomGen_<?= $i ?>" value="<?= $nomGen ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20%;text-align: right;">
                                    <label>Nombre comercial</label>                                            
                                </td>
                                <td style="text-align: left;color: #399000;font-weight: bold;">                                           
                                    <input style="margin: 0" disabled type="text" id="nomCom_<?= $i ?>" value="<?= $nomGen ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20%;text-align: right;">
                                    <label>Presentaci&oacute;n</label>                                            
                                </td>
                                <td style="text-align: left;color: #399000;font-weight: bold;">                                            
                                    <input style="margin: 0" disabled type="text" id="presMed_<?= $i ?>" value="<?= $presMed ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20%;text-align: right;">
                                    <label>Cantidad</label> 

                                </td>
                                <td>
                                    <input style="margin: 0; color: #399000;font-weight: bold;" type="text" id="cantMed_<?= $i ?>" value="<?= $cantidad ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20%;text-align: right;">
                                    <label>Tiempo del tratamiento (Valor en días)</label> 

                                </td>
                                <td>
                                    <input style="margin: 0; color: #399000;font-weight: bold;" type="text" id="tiempoMed_<?= $i ?>" value="<?= $tiempo ?>" />
                                </td>
                            </tr>                                    
                        </table>

                        <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:90%;padding-right: 15px;">
                            <tr>
                                <td style="">
                                    <label>Posolog&iacute;a</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div id="frecAdmMed_<?= $i ?>"><?= $utilidades->ajustar_texto_wysiwyg($frecAdmMed) ?></div>
                                </td>
                            </tr>
                        </table>


                        <table style="width: 87%;margin: 0 auto;">
                            <tr>
                                <td colspan="5">
                                    <hr>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" disabled style="background: #F79999;width: 30px;margin: 0;padding: 0;" />
                                </td>
                                <td style="text-align: left;">
                                    <span>Medicamento sin formular</span>
                                </td>
                                <td>
                                    <input type="text" disabled style="background: #CEFDAF;width: 30px;;margin: 0;padding: 0;" />
                                </td>
                                <td style="text-align: left;">
                                    <span>Medicamento formulado</span>
                                </td>
                                <td colspan="" style="text-align: right;<?= $displayBtnGuardarModificar ?>">
                                    <?php
                                    if ($ind_editar == 1) {
                                        ?>
                                        <input type="button" id="btn_imprimir_rxfinal" nombre="btn_imprimir_rxfinal" class="btnPrincipal peq" style="font-size: 16px;" value="<?= $txtBtn ?>" onclick="<?= $tipoAccionBtnFormular ?>(<?= $i ?>);" />
                                        <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                        </table>

                    </div>
                    <?php
                }
                ?>                        
                <table id="tblImprimirOrden" style="width:100%;margin-top: 15px;<?= $displayBtnImprimir ?>">
                    <tr>
                        <td colspan="" style="">
                            <input type="button" id="" nombre="" class="btnPrincipal peq" style="font-size: 14px;" value="Imprimir orden de medicamentos" onclick="imprimir_orden_medicamentos();" />
                        </td>
                    </tr>
                </table>
            </div>               
        </fieldset>
        <?php
    }
	
    public function getFormularioOrdenesMedicas($id_hc = NULL, $idPaciente = NULL, $tipo = 1, $ind_editar = 0) {
        $tituloGeneral = "";
        $accion_ordenar_procedimiento = "";

        switch ($tipo) {
            case 1:/* Directa desde UT */
                $tituloGeneral = "&Oacute;rdenes M&eacute;dicas";
                $accion_ordenar_procedimiento = "ordenar_procedimiento";
                break;
            case 2:/* Homologada */
                $tituloGeneral = "Homologar orden M&eacute;dica";
                $accion_ordenar_procedimiento = "ordenar_procedimiento_homologado";

                $combo = new Combo_Box();
                $dbConvenios = new DbConvenios();
                $dbPlanes = new DbPlanes();
                $dbPacientes = new DbPacientes();
                $paciente = $dbPacientes->getExistepaciente3($idPaciente);
                $idConvenio = $paciente['id_convenio_paciente'];
                $idPlan = $paciente['id_plan'];
                $tipoCotizante = $paciente['tipo_coti_paciente'];
                $rango = $paciente['rango_paciente'];
                $statusConvenio = $paciente['status_convenio_paciente'];
                break;
        }

        $combo = new Combo_Box();
        $dbordenesMedicas = new DbOrdenesMedicas();
        $cantidadProc = 0;
        $displayBtnImprimir = "display:none";


        if (isset($id_hc)) {/* Sí la función es llamada con id de H.C. */
            /* Verifica si la H.C. contiene formulación de medicamentos */
            $ordenMedica = $dbordenesMedicas->getOrdenMedicaActivaByHC($id_hc);
            $cantidadOrdenMedica = count($ordenMedica['id_orden_m']);
        }

        if ($cantidadOrdenMedica > 0) {
            $procedimientos = $dbordenesMedicas->getProcedimientosActivosOrdenMedicaByHC($ordenMedica['id_hc']);
            $cantidadProc = count($procedimientos);
            $idOrdenMedica = $ordenMedica['id_orden_m'];
            $displayBtnImprimir = "display:inline-table";
        }
        ?>
        <fieldset class="grupo_formularios" id="seccion_gafas"> 						
            <legend style="color: #012996;font-weight: bold;"><?= $tituloGeneral ?></legend>

            <div style="<?= $displayBtnImprimir ?>" id="divIdOrdenMedica">
                <h5>C&oacute;digo de la orden m&eacute;dica #<span style="color: #399000;font-weight: bold;" id="spanIdOrdenM"><?= $ordenMedica['id_orden_m'] ?></span></h5>                    
            </div>
            <br>

            <?php
            if ($tipo == 2) {//Homologada
                ?>
                <table style="width: 550px; margin-bottom: 20px; margin: 0 auto;">
                    <tr>
                        <td align="right">
                            <label class="inline" for="cmb_convenio"><b>Convenio/Entidad&nbsp;</b></label>
                        </td>
                        <td align="left">
                            <?php
                            $disabled = 1;
                            $lista_convenios = $dbConvenios->getListaConveniosActivos();
                            $combo->getComboDb("cmb_convenio", $idConvenio, $lista_convenios, "id_convenio, nombre_convenio", "-- Seleccione el convenio --", "seleccionar_convenio(this.value)", $disabled, "width:100%;");
                            ?>
                        </td>
                        <td align="right">
                            <label class="inline" for="cmb_convenio"><b>Plan*:</b></label>
                        </td>
                        <td align="left">
                            <div id="d_plan">                                                            
                                <?php
                                $disabled = 1;

                                $combo = new Combo_Box();
                                $lista_planes = $dbPlanes->getListaPlanesActivos($idConvenio); 
                                $combo->getComboDb("cmb_cod_plan", $idPlan, $lista_planes, "id_plan,nombre_plan", "-- Seleccione el plan --", "", $disabled, "width:100%;");
                                ?>
                            </div>
                        </td>                                                                        
                    </tr>
                    <tr>
                        <td align="right">
                            <label class="inline" for="cmb_convenio"><b>Tipo de cotizante*:</b></label>
                        </td>
                        <td align="left">
                            <?php
                            $disabled = 1;
                            if ($ind_reasignar == 1) {
                                $disabled = 0;
                            }

                            /*$array_tipoCotizante = array();
                            $array_tipoCotizante[0][0] = "0";
                            $array_tipoCotizante[0][1] = "No aplica";
                            $array_tipoCotizante[1][0] = "1";
                            $array_tipoCotizante[1][1] = "Cotizante";
                            $array_tipoCotizante[2][0] = "2";
                            $array_tipoCotizante[2][1] = "Beneficiario";

                            $combo->get("cmb_tipoCotizante", $tipoCotizante, $array_tipoCotizante, "-- Seleccione el tipo de cotizante --", "", $disabled, "width:100%;");*/
							$dbListas = new DbListas();
							$lista_tipo_cotizante = $dbListas->getListaDetalles(99);
							$combo->getComboDb("cmb_tipoCotizante", $tipoCotizante, $lista_tipo_cotizante, "id_detalle, nombre_detalle", "-- Seleccione --", "", $disabled, "width:100%;");
                            ?>
                        </td>

                        <td align="right">
                            <label class="inline" for="cmb_convenio"><b>Rango*:</b></label>
                        </td>
                        <td align="left">
                            <?php
                            $disabled = 1;
                            if ($ind_reasignar == 1) {
                                $disabled = 0;
                            }

                            $array_rango = array();
                            $array_rango[0][0] = "0";
                            $array_rango[0][1] = "No aplica";
                            $array_rango[1][0] = "1";
                            $array_rango[1][1] = "Uno";
                            $array_rango[2][0] = "2";
                            $array_rango[2][1] = "Dos";
                            $array_rango[3][0] = "3";
                            $array_rango[3][1] = "Tres";

                            $combo->get("cmb_rango", $rango, $array_rango, "-- Seleccione el rango --", "", $disabled, "width:100%;");
                            ?>
                            <input type="hidden" id="hdd_id_convenio_pc" name="hdd_id_convenio_pc" value="<?= $idConvenio ?>" />
                            <input type="hidden" id="hdd_id_plan_pc" name="hdd_id_plan_pc" value="<?= $idPlan ?>" />
                            <input type="hidden" id="hdd_status_convenio" name="hdd_status_convenio" value="<?= $statusConvenio ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%;text-align: right;">
                            <label>Medico remitente</label>                                            
                        </td>
                        <td style="text-align: left;color: #399000;font-weight: bold;" colspan="3">                                       
                            <input style="margin: 0" type="text" id="medicoRemitenteOrdenMedica"/>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%;text-align: right;">
                            <label>Fecha de creaci&oacute;n de la orden médica por homologar</label>                                            
                        </td>
                        <td style="text-align: left;color: #399000;font-weight: bold;" colspan="3">

                            <input type="text" maxlength="10"  name="fechaHomologacionOrdenMedica" id="fechaHomologacionOrdenMedica" style="width: 120px;" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" value="<?php //echo($fecha_fin);                                                 ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%;text-align: right;">
                            <label>Observaci&oacute;n </label>                                            
                        </td>
                        <td style="text-align: left;color: #399000;font-weight: bold;" colspan="3">
                            <textarea rows="4" cols="50" style="height:100px;" id="observacionOrdenMedica" name="observacionOrdenMedica"></textarea>
                        </td>
                    </tr>
                </table>

                <div style="background: #FDEBB7;padding: 3px 5px;width: 70%;margin: 0 auto;">
                    <span>¡Agregue los procedimientos o paquetes por ordenar!</span>
                </div>
                <br>                
                <?php
            }
            ?>

            <input type="hidden" name="cant_proc" id="cant_proc" value="<?= $cantidadProc ?>" />
            <input type="hidden" id="hdd_id_orden_medica" name="hdd_id_orden_medica" value="<?= $idOrdenMedica ?>" />
            <input type="hidden" id="hdd_idHC_ordenMedica" name="hdd_idHC_ordenMedica" value="<?= $id_hc ?>" />
            <input type="hidden" id="hdd_idPaciente_ordenMedica" name="hdd_idPaciente_ordenMedica" value="<?= $idPaciente ?>" />
            <input type="hidden" id="hdd_tipoOrdenMedica" name="hdd_tipoOrdenMedica" value="<?= $tipo ?>" />                                  
            <div>
                <div>
                    <table style="width: 150; margin-bottom: 20px;">
                        <tr>
                            <td>
                                Ver &oacute;rdenes m&eacute;dicas:
                                <select name="cmb_num_proc" id="cmb_num_proc" style="width:50px;" onchange="mostrar_proc(this.value)">
                                    <?php
                                    for ($i = 0; $i < $cantidadProc; $i++) {
                                        ?>
                                        <option value="<?php echo($i); ?>"><?php echo($i + 1); ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>                                       
                                <?php
                                if ($ind_editar == 1) {
                                    ?>
                                    <div class="restar_alemetos" onclick="eliminarOrdenMedica();" title="Eliminar orden médica"></div>
                                    <div id="btn_add_gafas" class="agregar_alemetos" onclick="agregar_proc();" title="Agregar procedimiento"></div>
                                    <?php
                                }
                                ?>
                            </td>
                        <tr>
                            <td>
                                <div id="resultadoOrdenMedica"></div>
                            </td>
                        </tr>
                        </tr>
                    </table>                           
                </div>

                <?php
                for ($i = 0; $i < 10; $i++) {
                    $display = "display:none;";
                    $displayBtnordenarProc = "";

                    $nomProc = "";
                    $tipoProc = "";
                    $codProc = "";
                    $nomTipoProc = "";
                    $cantProc = "";
                    $descProc = "";
                    $txtBtn = "¿Ordenar procedimiento #" . ($i + 1) . "?";
                    $displayBtnVerProcPaquete = "display:none";
                    $idOrdenMedicaDet = "";
                    $datosClinicos = "";
                    $ojo = "";
                    $nomDiag = "";
                    $codDiag = "";

                    $backgroundCodigo = "background: #F79999;";

                    if ($cantidadProc > 0 && $i < $cantidadProc) {
                        $tipoProc = $procedimientos[$i]['tipo_proc_orden_m_det'];
                        $cantProc = $procedimientos[$i]['cant_orden_m_det'];
                        $descProc = $procedimientos[$i]['des_orden_m_det'];
                        $idOrdenMedicaDet = $procedimientos[$i]['id_orden_m_det'];
                        $displayBtnordenarProc = "display:none;";
                        $i == 0 ? $display = "display:block;" : $display = "display:none;";
                        $datosClinicos = $procedimientos[$i]['datos_clinicos'];
                        $ojo = $procedimientos[$i]['ojo_orden_m_det'];

                        $nomDiag = $procedimientos[$i]['ciex_orden_m_det'] . " - " . $procedimientos[$i]['nombreCiex'];
                        $codDiag = $procedimientos[$i]['ciex_orden_m_det'];

                        switch ($tipoProc) {
                            case 1://Paquete
                                $nomProc = $procedimientos[$i]['nom_paquete_p'];
                                $nomTipoProc = "Paquete";
                                $codProc = $procedimientos[$i]['id_paquete_p'];
                                $displayBtnVerProcPaquete = "display:inline-block;";
                                break;
                            case 2://Procedimiento
                                $nomProc = $procedimientos[$i]['nombre_procedimiento'];
                                $nomTipoProc = "Procedimiento";
                                $codProc = $procedimientos[$i]['cod_procedimiento'];
                                break;
                        }

                        $backgroundCodigo = "background: #CEFDAF;";
                    }
                    ?>
                    <div id="tabla_proc_<?= $i ?>" style="<?= $display ?>" class="div_formula">
                        <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:90%;padding-right: 15px;" >
                            <tr>
                                <td colspan="6" class="text-center">
                                    <h5>Procedimiento/Paquete #<?= ($i + 1) ?></h5>
                                    <input type="hidden" id="hdd_idOrdenMedDet_<?= $i ?>" value="<?= $idOrdenMedicaDet ?>" />                                    
                                </td>
                            </tr>   
                            <tr>
                                <td align="right" style="width:20%;">Procedimiento/paquete:</td>
                                <td align="left" style="width:15%;">
                                    <input type="hidden" id="hddTipoProdOrdenMedica_<?= $i ?>" value="<?= $tipoProc ?>" />
                                    <input type="hidden" id="hddCodProcOrdenMedica_<?= $i ?>" value="<?= $codProc ?>" />
                                    <input type="text" style="<?= $backgroundCodigo ?>" id="codProcOrdenMedica_<?= $i ?>" name="codProc_<?= $i ?>" value="<?= $codProc ?>" class="no-margin" onchange="buscar_procedimiento_codigo_ordenMedica(this.value,<?= $i ?>);" />                                    
                                </td>
                                <td align="left" style="width:60%;">
                            <spam id="nomProcOrdenMedica_<?= $i ?>"><?= $nomProc ?></spam>
                            </td>
                            <td align="left" style="width:5%;">
                                <div class="d_buscar" onclick="btnVentanaBuscarProcedimientoOrdenMedica(<?= $i ?>);" style="<?= $displayBtnordenarProc ?>"></div>
                            </td>
                            </tr>

                            <?php
                            if ($tipo == 2) {//Homologada
                                ?>
                                <tr>
                                    <td align="right">Diagn&oacute;stico asociado:</td>
                                    <td align="left">
                                        <input type="hidden" id="hddCodCiexOrdenMedica_<?= $i ?>" value="" />
                                        <input type="text" id="codCiexOrdenMedica_<?= $i ?>" value="" class="no-margin" onchange="buscar_diagnostico_codigo_ordenMedica(this.value,<?= $i ?>);" />
                                    </td>
                                    <td align="left">
                                        <span id="nomCiexOrdenMedica_<?= $i ?>"></span>
                                    </td>
                                    <td align="left">
                                        <div class="d_buscar" onclick="btnVentanaBuscarDiagnosticoOrdenMedica(<?= $i ?>);"></div>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            <tr>
                                <td style="width: 20%;text-align: right;">Ojo:</td>
                                <td style="text-align: left;color: #399000;font-weight: bold;" colspan="2">                                       
                                    <?php
                                    $array_statusAseguradora = array();
                                    $array_statusAseguradora[0][0] = "3";
                                    $array_statusAseguradora[0][1] = "OD";
                                    $array_statusAseguradora[1][0] = "2";
                                    $array_statusAseguradora[1][1] = "OI";
                                    $array_statusAseguradora[2][0] = "1";
                                    $array_statusAseguradora[2][1] = "AO";
                                    $array_statusAseguradora[3][0] = "4";
                                    $array_statusAseguradora[3][1] = "No aplica";

                                    $combo->get("cmb_ojoOrdenMedica_" . $i, $ojo, $array_statusAseguradora, "-- Seleccione --", "", "", "", "", "no-margin");
                                    ?>
                                </td>                   
                            </tr>    
                            <tr>
                                <td style="width: 20%;text-align: right;">
                                    <label>Datos clínicos</label> 

                                </td>
                                <td colspan="5">
                                    <input style="margin: 0;" type="text" id="datosClinicosOrdenMedica_<?= $i ?>" value="<?= $datosClinicos ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20%;text-align: right;">
                                    <label>Justificación/Observaciones</label> 

                                </td>
                                <td colspan="5">
                                    <input style="margin: 0;" type="text" id="descProcOrdenMedica_<?= $i ?>" value="<?= $descProc ?>" />
                                </td>
                            </tr>         
                        </table>

                        <div>
                            <input type="button" id="btn_ver_proc_paquetes_<?= $i ?>" nombre="btn_ver_proc_paquetes_<?= $i ?>" class="btnPrincipal peq" style="font-size: 16px;<?= $displayBtnVerProcPaquete ?>" value="Detalle del paquete" onclick="verProcedimientosPaquete(<?= $i ?>);" />
                        </div>   

                        <table style="width: 87%;margin: 0 auto;">
                            <tr>
                                <td colspan="5">
                                    <hr>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" disabled style="background: #F79999;width: 30px;margin: 0;padding: 0;" />
                                </td>
                                <td style="text-align: left;">
                                    <span>Procedimiento sin ordenar</span>
                                </td>
                                <td>
                                    <input type="text" disabled style="background: #CEFDAF;width: 30px;;margin: 0;padding: 0;" />
                                </td>
                                <td style="text-align: left;">
                                    <span>Procedimiento ordenado</span>
                                </td>
                                <td colspan="" style="text-align: right;">
                                    <?php
                                    //if ($ind_editar == 1) {
                                    ?>
                                    <input type="button" id="btn_ordenar_medicamento_<?= $i ?>" nombre="btn_ordenar_medicamento_<?= $i ?>" class="btnPrincipal peq" style="font-size: 16px;<?= $displayBtnordenarProc ?>" value="<?= $txtBtn ?>" onclick="<?= $accion_ordenar_procedimiento ?>(<?= $i ?>);" />
                                    <?php
                                    //}
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php
                }
                ?>
                <div id="d_ordenar_procedimiento" style="display:none;"></div>                        
                <table id="tblImprimirOrdenMedicamentos" style="width:100%;margin-top: 15px;<?= $displayBtnImprimir ?>">
                    <tr>
                        <td colspan="" style="">
                            <input type="button" id="btnImprimirOrdenMedica" nombre="" class="btnPrincipal peq" style="font-size: 14px;" value="Imprimir orden m&eacute;dica" onclick="imprimir_orden_medica();" />                                                        
                        </td>
                    </tr>
                </table>
                <div id="d_buscar_procedimiento_ciex_codigo"></div>
            </div>    
        </fieldset>
        <?php
    }

}
