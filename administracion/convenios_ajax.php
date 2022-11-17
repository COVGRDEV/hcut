<?php
header("Content-Type: text/xml; charset=UTF-8");
session_start();
require_once("../db/DbConvenios.php");
require_once("../db/DbPlanes.php");
require_once("../db/DbListas.php");
require_once("../db/DbTiposLiquidacionQx.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../funciones/Utilidades.php");
require_once("../principal/ContenidoHtml.php");

$dbConvenios = new DbConvenios();
$dbPlanes = new DbPlanes();
$dbListas = new DbListas();
$dbTiposLiquidacionQx = new DbTiposLiquidacionQx();
$combo = new Combo_Box();
$utilidades = new Utilidades();
$contenido = new ContenidoHtml();
$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);

$opcion = $utilidades->str_decode($_POST["opcion"]);

switch ($opcion) {
    case "1": //Carga el listado total de convenios / El resultado de buscar convenios
        $parametro = $utilidades->str_decode($_POST["parametro"]);

        if ($parametro == "-5") {
            $resultado = $dbConvenios->getConvenios();
        } else if ($parametro != "-5") {
            $resultado = $dbConvenios->getConveniosBuscar($parametro);
        }
        ?>
        <table class="paginated modal_table" style="width: 99%; margin: auto;">
            <thead>
                <tr><th colspan="5">Listado de convenios - <?php echo count($resultado) . " registros"; ?></th></tr>
                <tr>
                    <th style="width:5%;">Codigo</th>
                    <th style="width:36%;">Nombre</th>
                    <th style="width:6%;">Estado</th>
                    <th style="width:5%;">Planes</th>
                </tr>
            </thead>
            <?php
            if (count($resultado) >= 1) {
                foreach ($resultado as $value) {
                    $estado = $value["ind_activo"];
                    if ($estado == 1) {
                        $estado = "Activo";
                        $class_estado = "activo";
                    } else if ($estado == 0) {
                        $estado = "No Activo";
                        $class_estado = "inactivo";
                    }
                    ?>
                    <tr onclick="seleccionar_convenio('<?php echo $value["id_convenio"]; ?>', 1);">
                        <td><?php echo $value["id_convenio"]; ?></td>
                        <td style="text-align: left;"><?php echo $value["nombre_convenio"]; ?></td>
                        <td><span class="<?php echo $class_estado; ?>"><?php echo $estado; ?></span></td>
                        <td style=""><?php echo $value["cantidad"]; ?></td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <td colspan="4">No hay resultados</td>
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

    case "2": //Imprime el formulario de crear nuevo convenion | Actualizar Convenios
        $parametro = $utilidades->str_decode($_POST["parametro"]);
        $accion_aux = "";
        $nombre = "";
        $cod_administradora = "";
        $id_tipo_documento = "";
        $numero_documento = "";
        $indActivo = "checked"; //indicador de activo
        $indEco = ""; //indicador de ecopetrol
        $indNumAut = "";
        $indNumAutObl = "";
        $indNumCarnet = "";
        $indNumCarnetObl = "";
        $idConvenio = "0";
        $visibilidad_aux = 0; //variable que define si el resto del cuerpo de codigo se imprime en la página
        
		$contratacion="";
		$num_contrato="";
		$rng_fact_ini="";
		$rng_fact_fin="";
		
        $indDespachoMedicamentos = ""; //indicador de despacho de medicamentos

        if ($parametro == "-5") {
            $accion_aux = 1; //1= Crear nuevo
        } else if ($parametro != "-5") {
            $idConvenio = $utilidades->str_decode($_POST["idConvenio"]);
            $rta = "";
            $rtaPlanes = "";

            $accion_aux = 2; //2= Modificar

            $rta = $dbConvenios->getConvenio($idConvenio); //Busca los datos del convenio
            $rtaPlanes = $dbConvenios->getListaPlanes($idConvenio); //Busca los planes del convenio
            $nombre = $rta["nombre_convenio"];
            $cod_administradora = $rta["cod_administradora"];
            $id_tipo_documento = $rta["id_tipo_documento"];
            $numero_documento = $rta["numero_documento"];
			
			$contratacion=$rta["contratacion"];
			$rng_fact_ini=$rta["fact_inicial"];
			$rng_fact_fin=$rta["fact_final"];
			$num_contrato=$rta["num_contrato"];
			
            if ($rta["ind_activo"] == "1") {
                $indActivo = "checked";
            } else {
                $indActivo = "";
            }
            if ($rta["ind_eco"] == "1") {
                $indEco = "checked";
            } else {
                $indEco = "";
            }
            if ($rta["ind_num_aut"] == "1") {
                $indNumAut = "checked";
            } else {
                $indNumAut = "";
            }
            if ($rta["ind_num_aut_obl"] == "1") {
                $indNumAutObl = "checked";
            } else {
                $indNumAutObl = "";
            }
            if ($rta["ind_num_carnet"] == "1") {
                $indNumCarnet = "checked";
            } else {
                $indNumCarnet = "";
            }
            if ($rta["ind_num_carnet_obl"] == "1") {
                $indNumCarnetObl = "checked";
            } else {
                $indNumCarnetObl = "";
            }                        
			
            $idConvenio = $utilidades->str_decode($_POST["idConvenio"]);
            $visibilidad_aux = 1;
        }
        ?>
        <form id="frmNuevoConvenio" name="frmNuevoConvenio">
            <div style="text-align: left;">    
                <fieldset style="">
                    <legend>Convenio:</legend>
                    <table style="width: 100%; margin: auto; font-size: 10pt;">
                        <?php
							if ($parametro != "-5") {
                        ?>
                        <tr>
                            <td style="text-align:right; width:15%;">
                                <label class="inline" style="font-weight: 600;">Codigo :</label>
                            </td>
                            <td style="text-align:left; width:35%;">
                                <label class="inline" style="font-weight: 600;"><?php echo $idConvenio; ?></label>
                            </td>
                        </tr>
                        <?php
							}
                        ?>
                        <tr>
                            <td style="text-align:right; width:15%;">
                                <label class="inline">Nombre :</label>
                            </td>
                            <td style="text-align:left; width:35%;">
                                <input type="text" id="txtNombre" name="txtNombre" value="<?php echo($nombre); ?>" placeholder="Nombre del convenio" onblur="trim_cadena(this);" />
                            </td>
                            <td style="text-align:right; width:15%;">
                                <label>Codigo de la entidad :</label>
                            </td>
                            <td style="text-align:left; width:35%;">
                                <input type="text" name="txtCodAdministradora"  id="txtCodAdministradora" value="<?php echo($cod_administradora); ?>" onblur="trim_cadena(this);" style="width:150px;">
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align:right;">
                                <label class="inline">Tipo de identificaci&oacute;n :</label>
                            </td>
                            <td style="text-align:left;">
                                <?php
									$lista_tipos_doc = $dbListas->getListaDetalles(27);
									$combo->getComboDb("cmbTipoDocumento", $id_tipo_documento, $lista_tipos_doc, "id_detalle, nombre_detalle", "--Seleccione--", "", true);
                                ?>
                            </td>
                            <td style="text-align:right;">
                                <label>No. de identificaci&oacute;n :</label>
                            </td>
                            <td style="text-align:left;">
                                <input type="text" name="txtNumeroDocumento"  id="txtNumeroDocumento" value="<?php echo($numero_documento); ?>" onblur="trim_cadena(this);" style="width:150px;" maxlength="20">
                            </td>
                        </tr>
                         <tr>                         
                            <td style="text-align:right;">
                                <label class="inline">Modalidades de contratación :</label>
                            </td>
                            <td style="text-align:left;">
                            	<?php
                                	$lista_modalidades_contratacion = $dbListas->getListaDetalles(98);
									$combo->getComboDb("cmbTipoContratacion", $contratacion, $lista_modalidades_contratacion, "id_detalle, nombre_detalle", "--Seleccione--", "", true);
								?>
                            </td>
                          	<td style="text-align:right;">
                                   <label>Número de contrato :</label>
                            </td>
                            <td style="text-align:left;">
                                <label style="margin: 6px 0 0 0;">
                                    <input type="text" name="num_contrato" id="num_contrato"  value="<?php echo($num_contrato); ?>" onblur="trim_cadena(this);" style="width:150px;" maxlength="20"/>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align:right;">
                                <label>Fecha inicial facturación:</label>
                            </td>
                            <td style="text-align:left;">
                               <input type="text" name="txtRngFechaIni"  id="txtRngFechaIni" value="<?php echo($rng_fact_ini); ?>" onblur="trim_cadena(this);" style="width:150px;" maxlength="20">
                            </td>
                            <td style="text-align:right;">
                                <label>Fecha final facturación:</label>
                            </td>
                            <td style="text-align:left;">
                                <input type="text" name="txtRngFechaFin"  id="txtRngFechaFin" value="<?php echo($rng_fact_fin); ?>" onblur="trim_cadena(this);" style="width:150px;" maxlength="20">
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align:right;">
                                <label>Convenio Ecopetrol :</label>
                            </td>
                            <td style="text-align:left;">
                                <label style="margin: 6px 0 0 0;">
                                    <input type="checkbox" name="indEco" id="indEco" <?php echo($indEco); ?> />
                                </label>
                            </td>
                            <td style="text-align:right;">
                                <label>Registro activo :</label>
                            </td>
                            <td style="text-align:left;">
                                <label style="margin: 6px 0 0 0;">
                                    <input type="checkbox" name="indActivo" id="indActivo" <?php echo($indActivo); ?> />
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align:right;">
                                <label>N&uacute;m. autorizaci&oacute;n :</label>
                            </td>
                            <td style="text-align:left;">
                                <label style="margin: 6px 0 0 0;">
                                    <input type="checkbox" name="indNumAut" id="indNumAut" <?php echo($indNumAut); ?> />
                                </label>
                            </td>
                            <td style="text-align:right;">
                                <label>Obligatorio :</label>
                            </td>
                            <td style="text-align:left;">
                                <label style="margin: 6px 0 0 0;">
                                    <input type="checkbox" name="indNumAutObl" id="indNumAutObl" <?php echo($indNumAutObl); ?> />
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align:right;">
                                <label>N&uacute;m. carnet :</label>
                            </td>
                            <td style="text-align:left;">
                                <label style="margin: 6px 0 0 0;">
                                    <input type="checkbox" name="indNumCarnet" id="indNumCarnet" <?php echo($indNumCarnet); ?> />
                                </label>
                            </td>                                                      
                        </tr>
                    </table>
                </fieldset>
            </div>
            <?php
				if ($idConvenio != 0) {
            ?>
            <table class="paginated modal_table" style="width: 99%; margin: auto;" id="tablaPlanes">
                <thead>
                    <tr>
                        <th colspan="7">
                            <p class="no-margin">Listado de planes </p>
                            <img style="float:right; margin:0 20px 0 0;" onclick="ventanaAgregarPlan(1, 0)" src="../imagenes/Add-icon.png" />
                        </th>
                    </tr>
                    <tr>
                        <th style="width:5%;">Codigo</th>
                        <th style="width:33%;">Nombre del plan</th>
                        <th style="width:15%;">Tipo de pago</th>
                        <th style="width:15%;">Tipo de usuario</th>
                        <th style="width:15%;">Tipo de formulaci&oacute;n</th>
                        <th style="width:10%;">Estado</th>
                        <th style="width:7%;"></th>
                    </tr>
                </thead>
                <?php
                    if ($visibilidad_aux == "1") {
                        if (count($rtaPlanes) >= 1) {
                            foreach ($rtaPlanes as $value) {
                                $estado = $value["ind_activo"];
                                if ($estado == 1) {
                                    $estado = "Activo";
                                    $class_estado = "activo";
                                } else if ($estado == 0) {
                                    $estado = "No Activo";
                                    $class_estado = "inactivo";
                                }
                            ?>
                            <tr id="<?php echo $value["id_plan"]; ?>" onclick="edicionPlan(<?php echo $value["id_plan"]; ?>);">
                                <td align="center">
                                    <?php echo $value["id_plan"]; ?>
                                </td>
                                <td align="left">
                                    <?php echo $value["nombre_plan"]; ?>
                                </td>
                                <td align="center">
                                    <?php
                                        if ($value["ind_tipo_pago"] == "1") {
                                            echo("Cuota moderadora");
                                        } else if ($value["ind_tipo_pago"] == "2") {
                                            echo("Total");
                                        }
                                        ?>
                                </td>
                                <td align="center">
                                    <?php echo($value["tipo_usuario"]); ?>
                                </td>
                                <td align="center">
                                    <?php echo($value["tipo_medicamento"] != "" ? $value["tipo_medicamento"] : "-"); ?>
                                </td>
                                <td align="center">
                                    <span class="<?php echo $class_estado; ?>"><?php echo $estado; ?></span>
                                </td>
                                <td align="center">
                                    <img src="../imagenes/Search-icon.png" />
                                </td>
                            </tr>
                            <?php
						}
					} else {
                        ?>
                        <tr id="tablaPlanesNull">
                            <td colspan="7">
                                No hay Resultados
                            </td>
                        </tr>
                        <?php
					}
				} else {
                    ?>
                    <tr id="tablaPlanesNull">
                        <td colspan="7">
                            No hay Resultados
                        </td>
                    </tr>
                    <?php
				}
                ?>
            </table>
			<?php
				}
            ?>
            <br/>
            <input type="button" id="btnCancelarConvenio" nombre="btnCancelarConvenio" value="Cancelar" class="btnSecundario" onclick="muestra_convenios()"/> 
            <?php
                if ($tipo_acceso_menu == 2) {
            ?>
                <input type="submit" id="btnGuardarConvenio" nombre="btnGuardarConvenio" value="Guardar" class="btnPrincipal" onclick="guardaModificaConvenio(<?php echo $accion_aux; ?>)"/>
            <?php
                }
            ?>
        </form>
        <input type="hidden" id="idConvenio" name="idConvenio" value="<?php echo $idConvenio; ?>" />
        <?php
        break;
		
    case "3": //Guarda | Actualiza Convenio
        $parametro = "";
        $nombre = "";
        $indActivo = "";
        @$nombre = $utilidades->str_decode($_POST["nombre"]);
        @$indActivo = $utilidades->str_decode($_POST["indActivo"]);
        @$parametro = $utilidades->str_decode($_POST["parametro"]);
        @$idConvenio = $utilidades->str_decode($_POST["idConvenio"]);
        @$indEco = $utilidades->str_decode($_POST["idEco"]);
        @$txtCodAdministradora = $utilidades->str_decode($_POST["txtCodAdministradora"]);
        @$cmbTipoDocumento = $utilidades->str_decode($_POST["cmbTipoDocumento"]);
        @$txtNumeroDocumento = $utilidades->str_decode($_POST["txtNumeroDocumento"]);
        @$indNumAut = $utilidades->str_decode($_POST["indNumAut"]);
        @$indNumAutObl = $utilidades->str_decode($_POST["indNumAutObl"]);
        @$indNumCarnet = $utilidades->str_decode($_POST["indNumCarnet"]);
        @$indNumCarnetObl = $utilidades->str_decode($_POST["indNumCarnetObl"]);        

		@$fec_ini_fac = $utilidades->str_decode($_POST["fecha_ini_fac"]);
		@$fec_fin_fac = $utilidades->str_decode($_POST["fecha_fin_fac"]);
		@$contratacion = $utilidades->str_decode($_POST["contratacion"]);
		@$num_contrato = $utilidades->str_decode($_POST["num_contrato"]);
		
        $resultado = $dbConvenios->guardarModificaConvenio($nombre, $indActivo, $_SESSION["idUsuario"], $idConvenio, $parametro, $indEco,
				$txtCodAdministradora, $indNumAut, $indNumAutObl, $indNumCarnet, $indNumCarnetObl, $cmbTipoDocumento, $txtNumeroDocumento, 
				$fec_ini_fac, $fec_fin_fac, $contratacion, $num_contrato);
				
       ?> <input type="hidden" id="hdd_resultado_convenio" name="hdd_resultado_convenio" value="<?php echo $resultado ?>" /> <?php
		
        break;

    case "4": //Ventana Modal Agregar Planes
        $idConvenio = isset($_POST["idConvenio"]) ? $utilidades->str_decode($_POST["idConvenio"]) : 0;
        $valor_boton = "";
        $accionAgregarConvenio = 0;
        $tipopagoSelect = "";
        $tipopagoSelect2 = "";
        $idPlan = $utilidades->str_decode($_POST["idPlan"]);
        $indDespachoMedicamentos = 0; 

        $nombrePlan = "";
        $tipoPago = 0;
        $indActivo = "checked";
        $indCalcCC = "";
		$indDescCC = "";
        $rta = "";
        $tipo_usuario = 154; //Seleccion del tipo de usuario del tipo CONTRIBUTIVO por defecto.
        $cod_tipo_medicamento = "";
        $indISS2001 = "";
        $procentajeISS2001 = "";
		$cobertura = "";
		
        if ($idPlan != 0) {
            $rta = $dbPlanes->getPlan($idPlan);
            $nombrePlan = $rta["nombre_plan"];
            $tipoPago = $rta["ind_tipo_pago"];
            $tipo_usuario = $rta["id_tipo_usuario"];
            $cod_tipo_medicamento = $rta["cod_tipo_medicamento"];
            $indDespachoMedicamentos = $rta["ind_despacho_medicamentos"];
            $id_liq_qx = $rta["id_liq_qx"];
			$cobertura = $rta["cobertura"];
			
			if ($rta["ind_activo"] != "0") {
				$indActivo = "checked";
			} else {
				$indActivo = "";
			}
			
			if ($rta["ind_calc_cc"] == "1") {
				$indCalcCC = "checked";
			} else {
				$indCalcCC = "";
			}
			
			if ($rta["ind_desc_cc"] == "1") {
				$indDescCC = "checked";
			} else {
				$indDescCC = "";
			}
			
            //selecciona cual tipo de pago es seleccionado
            if ($tipoPago == "1") {
                $tipopagoSelect = "selected";
            } else if ($tipoPago == "2") {
                $tipopagoSelect2 = "selected";
            }
			
            if ($rta["ind_iss"] == "1") {
                $indISS2001 = "checked";
            }
			
            $procentajeISS2001 = $rta["porc_iss"];
			
            $accionAgregarConvenio = 1; //Actualiza el Plan
        }

        if ($idConvenio == "0") {
            $valor_boton = "Agregar";
        } else {
            $valor_boton = "Guardar";
        }
        ?>
        <div class="encabezado">
            <h3><?php echo($valor_boton); ?> Plan</h3>
        </div>
        <div class="contenedor_error" id="contenedor_error"></div>
        <form id="frmAgregarPlan" name="frmAgregarPlan">
            <div style="width:98%; margin:auto;">
                <table style="width: 100%;">
                    <tr>
                        <td align="right" style="width:20%;">
                            <label class="inline">Nombre del plan*:</label>
                        </td>
                        <td align="left" style="width:30%;">
                            <input type="text" id="txtNombrePlan" name="txtNombrePlan" onblur="trim_cadena(this);" value="<?php echo $nombrePlan; ?>" /> 
                        </td>
                        <td align="right" style="width:20%;">
                            <label class="inline">Tipo de pago*:</label>

                        </td>
                        <td align="left" style="width:30%;">
                            <select id="cmb_tipoPago" name="cmb_tipoPago" class="select">
                                <option value>--Seleccione--</option>
                                <option value="1" <?php echo $tipopagoSelect; ?>>Cuota moderadora</option>
                                <option value="2" <?php echo $tipopagoSelect2; ?>>Total</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:right;">
                            <label class="inline">Tipo de usuario*:</label>
                        </td>
                        <td style="text-align:left; font-weight:600;">
                            <?php
								echo $combo->getComboDb("cmb_tipo_usuario", $tipo_usuario, $dbListas->getListaDetalles(29), "id_detalle, nombre_detalle", "--Seleccione--", "", "");
                            ?>
                        </td>
                        <td style="text-align:right;">
                            <label class="inline">Tipo de formulaci&oacute;n:</label>
                        </td>
                        <td style="text-align:left;">
                            <?php
								$lista_tipos_medicamentos = $dbListas->getListaDetalles(53, 1);
								
								$combo->getComboDb("cmb_tipo_medicamento", $cod_tipo_medicamento, $lista_tipos_medicamentos, "codigo_detalle,nombre_detalle", "--Seleccione--", "", 1);
                            ?>
                        </td>
                    </tr>
                      <tr>
                        <td style="text-align:right;">
                            <label class="inline">Cobertura*:</label>
                        </td>
                        <td style="text-align:left; font-weight:600;">
                            <?php
								$lista_tipos_cobertura = $dbListas->getListaDetalles(97, 1);
								echo $combo->getComboDb("cmb_cobertura", $cobertura, $lista_tipos_cobertura, "id_detalle, nombre_detalle", "--Seleccione--", "", "");
							?>
                        </td>
                       
                    </tr>
                    <tr>
                        <td style="text-align:right;">
                            <label class="inline">¿Tarifas ISS 2001?:</label>
                        </td>
                        <td style="text-align:left; font-weight:600;">
                            <input type="checkbox" name="indISS2001" id="indISS2001" <?= $indISS2001 ?>>
                        </td>
                        <td style="text-align:right;">
                            <label class="inline">Porcentaje ISS 2001:</label>
                        </td>
                        <td style="text-align:left; font-weight:600;">
                            <input type="text" id="iss2001porc" name="iss2001porc" onblur="trim_cadena(this);" value="<?= $procentajeISS2001 ?>" /> 
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:right;">
                            <label class="inline">Liquidaci&oacute;n quir&uacute;rgica:</label>
                        </td>
                        <td style="text-align:left;">
                        	<?php
                            	//Se obtiene el listado de tipos de liquidación disponibles
								$lista_tipos_liq_qx = $dbTiposLiquidacionQx->getListasTiposLiquidacionQx(1);
								$combo->getComboDb("cmb_liq_qx", $id_liq_qx, $lista_tipos_liq_qx, "id_liq_qx,nombre_liq_qx", "--Seleccione--", "", 1, "width: 100%;");
							?>
                        </td>
                    	<td style="text-align:right;">
                            <label class="inline">Despacho de medicamentos:</label>
                        </td>
                        <td style="text-align:left; font-weight:600;">
                            <input type="checkbox" name="indDespachoMedicamentos" id="indDespachoMedicamentos" <?php echo($indDespachoMedicamentos != 0 ? "checked" : ""); ?> />
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:right;">
                            <label class="inline">Calcular copagos y cuotas moderadoras:</label>
                        </td>
                        <td style="text-align:left; font-weight:600;">
                            <input type="checkbox" name="chk_calc_cc" id="chk_calc_cc" <?php echo($idPlan != 0 ? $indCalcCC: ""); ?> />
                        </td>
                        <td style="text-align:right;">
                            <label class="inline">Descontar copagos y cuotas moderadoras en pedidos:</label>
                        </td>
                        <td style="text-align:left; font-weight:600;">
                            <input type="checkbox" name="chk_desc_cc" id="chk_desc_cc" <?php echo($idPlan != 0 ? $indDescCC: ""); ?> />
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="2"></td>
                        <td style="text-align:right;">
                            <label class="inline">Plan activo:</label>
                        </td>
                        <td style="text-align:left; font-weight:600;">
                            <input type="checkbox" name="indActivoPlan" id="indActivoPlan" <?php echo($idPlan != 0 ? $indActivo : "checked"); ?> />
                        </td>
                    </tr>
                </table>                
                <?php
                    if ($tipo_acceso_menu == 2) {
                ?>
                    <input type="submit" id="btnGuardarConvenio" nombre="btnGuardarConvenio" value="<?php echo($valor_boton); ?>" class="btnPrincipal" onclick="agregar_plan(<?php echo $idPlan; ?>)"/>
                <?php
                    }
                ?>
            </div>
        </form>
        <input type="hidden" id="hdd_resultadoPlanes" name="hdd_resultadoPlanes" />
        <?php
        break;

	case "5": //Guarda el plan para el convenio
		$id_usuario = $_SESSION["idUsuario"];
		
		@$idConvenio = $utilidades->str_decode($_POST["idConvenio"]);
		@$tipoAccion = $utilidades->str_decode($_POST["tipoAccion"]);
		@$txtNombrePlan = $utilidades->str_decode($_POST["txtNombrePlan"]);
		@$tipoPago = $utilidades->str_decode($_POST["tipoPago"]);
		@$indCalcCC = $utilidades->str_decode($_POST["indCalcCC"]);
		@$indDescCC = $utilidades->str_decode($_POST["indDescCC"]);
		@$indActivoPlan = $utilidades->str_decode($_POST["indActivoPlan"]);
		@$idPlan = $utilidades->str_decode($_POST["idPlan"]);
		@$tipoUsuario = $utilidades->str_decode($_POST["tipoUsuario"]);
		@$codTipoMedicamento = $utilidades->str_decode($_POST["codTipoMedicamento"]);
		@$indISS2001 = $utilidades->str_decode($_POST["indISS2001"]);
		@$iss2001porc = $utilidades->str_decode($_POST["iss2001porc"]);
		@$idLiqQx = $utilidades->str_decode($_POST["idLiqQx"]);
		@$indDespachoMedicamentos = $utilidades->str_decode($_POST["indDespachoMedicamentos"]);
		@$cmb_cobertura = $utilidades->str_decode($_POST["cmb_cobertura"]);
		//Formatea el valor del porcentaje ISS 2001, si el indicador no está seleccionado asigna valor 0 a la variable $iss2001porc
		$indISS2001 == 0 ? $iss2001porc = 0 : $iss2001porc = $iss2001porc;
		
		$rta_aux = $dbConvenios->crearModificarPlan($txtNombrePlan, $tipoPago, $indActivoPlan, $idConvenio, $id_usuario,
				$tipoAccion, $idPlan, $tipoUsuario, $codTipoMedicamento, $indISS2001, $iss2001porc, $idLiqQx, $indCalcCC,
				$indDespachoMedicamentos, $indDescCC, $cmb_cobertura);
		
		echo($rta_aux);
		break;
}
?>
