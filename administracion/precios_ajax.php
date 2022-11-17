<?php
header("Content-Type: text/xml; charset=UTF-8");
session_start();
require_once("../db/DbConvenios.php");
require_once("../db/DbPlanes.php");
require_once("../db/DbListasPrecios.php");
require_once("../db/DbMaestroProcedimientos.php");
require_once("../db/DbMaestroMedicamentos.php");
require_once("../db/DbMaestroInsumos.php");
require_once("../db/DbPaquetesProcedimientos.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../funciones/utilidades.php");
require_once("../db/DbVariables.php");
require_once("../db/DbPrecios.php");

$dbConvenios = new DbConvenios();
$dbPlanes = new DbPlanes();
$dbListasPrecios = new DbListasPrecios();
$dbMaestroProcedimientos = new DbMaestroProcedimientos();
$dbMaestroMedicamentos = new DbMaestroMedicamentos();
$dbMaestroInsumos = new DbMaestroInsumos();
$dbPaquetesProcedimientos = new DbPaquetesProcedimientos();
$combo = new Combo_Box();
$utilidades = new Utilidades();
$variables = new DbVariables();
$dbPrecios = new DbPrecios();

$opcion = $_POST["opcion"];

switch ($opcion) {
    case "1": //Carga el listado total de convenios / El resultado de buscar convenios
        $parametro = "";
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
                    <tr onclick="seleccionar_convenio('<?php echo $value["id_convenio"]; ?>', '<?php echo $value["ind_eco"]; ?>');">
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
            $(function () {
                $(".paginated", "table").each(function (i) {
                    $(this).text(i + 1);
                });

                $("table.paginated").each(function () {
                    var currentPage = 0;
                    var numPerPage = 10;
                    var $table = $(this);
                    $table.bind("repaginate", function () {
                        $table.find("tbody tr").hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
                    });
                    $table.trigger("repaginate");
                    var numRows = $table.find("tbody tr").length;
                    var numPages = Math.ceil(numRows / numPerPage);
                    var $pager = $('<div class="pager"></div>');
                    for (var page = 0; page < numPages; page++) {
                        $('<span class="page-number"></span>').text(page + 1).bind("click", {
                            newPage: page
                        }, function (event) {
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

    case "2"://Convenio seleccionado
        $idConvenio = $utilidades->str_decode($_POST["idConvenio"]);
        $idEcopetrol = $utilidades->str_decode($_POST["idEcopetrol"]);
        $rta_aux = $dbConvenios->getConvenio($idConvenio);
        ?>
        <div id="PreciosConvenio">
            <fieldset style="">
                <table style="width: 750px;margin: auto;">
                    <tr>
                        <td style="width: 10%;">
                            <label class="inline">
                                Codigo de Convenio:
                            </label>
                        </td>
                        <td style="text-align: left;width: 40%;">
                            <label class="inline" style="font-weight: 600;">
                                <?php echo $rta_aux["id_convenio"]; ?>
                            </label>
                        </td>
                        <td style="width: 10%;">
                            <label class="inline">
                                Convenio
                            </label>
                        </td>
                        <td style="text-align: left;width: 40%;">
                            <label class="inline" style="font-weight: 600;">
                                <?php echo $rta_aux["nombre_convenio"]; ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 10%;">
                            <label class="inline">
                                Plan
                            </label>
                        </td>
                        <td style="text-align: left; width: 40%;">
                            <?php $combo->getComboDb("cmb_plan", "", $dbPlanes->getListaPlanesActivos($rta_aux["id_convenio"]), "id_plan, nombre_plan", "Seleccione el Plan", "resetServicio();", "", "width: 200px;"); ?>
                        </td>
                        <td style="width: 10%;">
                            <label class="inline">
                                Tipo de servicio
                            </label>
                        </td>
                        <td style="text-align: left; width: 40%;">
                            <select onchange="listasPrecios();" id="cmb_tipo_servicio" name="cmb_tipo_servicio">
                                <option value="0">Seleccione el tipo de servicio</option>
                                <option value="I">Insumos</option>
                                <option value="M">Medicamentos</option>
                                <option value="P">Procedimientos</option>
                                <?php /*<option value="Q">Paquetes</option>*/ ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <div id="resultado"></div>
            <input type="hidden" id="hdd_ecopetrol" name="hdd_ecopetrol" value="<?php echo $idEcopetrol; ?>" />
            <input type="hidden" id="hdd_nombre_convenio" name="hdd_nombre_convenio" value="<?php echo $rta_aux["nombre_convenio"]; ?>" />
        </div>
        <?php
        break;

    case "3"://Imprime el listado de precios
        @$parametro = $utilidades->str_decode($_POST["parametro"]);
        @$idPlan = $utilidades->str_decode($_POST["idPlan"]);
        @$accion = $utilidades->str_decode($_POST["accion"]);
        @$txtBuscar = $utilidades->str_decode($_POST["txtBuscar"]);

        $plan = $dbPlanes->getPlan($idPlan);
        $rta_aux = $dbListasPrecios->getListasPreciosServicio($idPlan, $parametro, $accion, $txtBuscar);
        ?>
        <div>
            <?php
				if ($plan['ind_iss']) {/* Verifica si lleva manual tarifario ISS 2001 */
			?>
            <fieldset style="">
                <legend>Detalles del plan seleccionado: <span style="font-weight: bold;"><?= $plan['nombre_plan'] . ($plan['ind_tipo_pago'] == 1 ? " (Cuota moderadora)" : "") ?></span></legend>
                <div>
                    <p>El plan seleccionado está basado en el manual tarifario <span style="color: #399000;font-weight: bold;">ISS 2001</span> con porcentaje de incremento del: <span style="font-weight: bold; color: #012996;"><?= $plan['porc_iss'] ?>%</span></p>
                </div>
            </fieldset>                        
            <?php
				}
				
				if ($parametro == "P" || $parametro == "I") {
			?>
            <div>
                <input type="button" id="" nombre="" value="Importar precios por .CSV" class="btnPrincipal" onclick="muestraImportarCsv()"/>
                <div id="divImportarCsv"></div>
            </div>
            <?php
				}
			?>
            <form id="frmListadoPrecios" name="frmListadoPrecios">
                <table style="width: 100%;">
                    <tr valign="middle">
                        <td align="center" colspan="2">
                            <div id="advertenciasg"></div> 
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" id="txtParametroPrecios" name="txtParametroPrecios" placeholder="C&oacute;digo o nombre del procedimiento/medicamento/insumo/paquete" onblur="trim_cadena(this);" value="<?php echo($txtBuscar); ?>" />
                        </td>
                        <td style="width: 8%;">
                            <input type="submit" id="btnBuscar" nombre="btnBuscar" value="Buscar" class="btnPrincipal peq" onclick="buscarPrecio()"/>
                        </td>
                        <td style="width: 20%;">
                            <input type="button" id="btn_buscar_usuario" nombre="btn_buscar_usuario" value="Ver todos" class="btnSecundario peq" onclick="listasPrecios()"/>
                            <input type="button" id="btn_buscar_usuario" nombre="btn_buscar_usuario" value="Nuevo precio" class="btnPrincipal peq" onclick="ventanaAgregarPrecio(1, 0, false);"/>
                            <?php
                            if ($plan['ind_iss']) {
                                ?>
                                <input type="button" id="btn_buscar_usuario" nombre="btn_buscar_usuario" value="Importar precios ISS 2001" class="btnPrincipal peq" onclick="ventanaAgregarPrecio(1, 0, true);"/>
                                <?php
                            }
                            ?>                            
                        </td>
                    </tr>                    
                </table>
            </form>
        </div>
        <table id="tblPrecios" class="modal_table" style="width: 99%; margin: auto;">
            <thead>
                <tr>
                    <th colspan="9">Listado de precios</th>
                </tr>
                <tr>
                    <th style="width:10%;">Codigo</th>
                    <th style="width:40%;">Nombre</th>
                    <th style="width:10%;">Fecha inicio</th>
                    <th style="width:10%;">Fecha fin</th>
                    <th style="width:10%;">Tipo valor</th>
                    <th style="width:10%;">Valor</th>
                    <th style="width:10%;">Valor cuota</th>
                    <th style="width:10%;" colspan="2">&nbsp;</th>
                </tr>
            </thead>
            <?php
            if (count($rta_aux) > 0) {
                foreach ($rta_aux as $value) {
                    ?>
                    <tr>
                        <td onclick="ventanaAgregarPrecio(1, <?php echo $value["id_precio"]; ?>, false)" align="center">
                            <?php
								switch ($parametro) {
									case "P":
										echo($value["cod_procedimiento"]);
										$codigo_servicio = $value["cod_procedimiento"];
										break;
									case "M":
										echo($value["cod_medicamento"]);
										$codigo_servicio = $value["cod_medicamento"];
										break;
									case "I":
										echo($value["cod_insumo"]);
										$codigo_servicio = $value["cod_insumo"];
										break;
									case "Q":
										echo($value["id_paquete_p"]);
										$codigo_servicio = $value["id_paquete_p"];
										break;
								}
								$nombre_prod_aux = $value["nombre_aux"];
								if ($value["ind_activo_aux"] != "1") {
									$nombre_prod_aux .= "<br /><span class=\"texto-rojo\"><b>[Producto inactivo]</b></span>";
								}
                            ?>
                        </td>
                        <td onclick="ventanaAgregarPrecio(1, <?php echo $value["id_precio"]; ?>, false);" align="left"><?php echo($nombre_prod_aux); ?></td>
                        <td onclick="ventanaAgregarPrecio(1, <?php echo $value["id_precio"]; ?>, false);" align="center"><?php echo($value["fecha_ini_aux"]); ?></td>
                        <td onclick="ventanaAgregarPrecio(1, <?php echo $value["id_precio"]; ?>, false);" align="center"><?php echo($value["fecha_final_aux"]); ?></td>
                        <td onclick="ventanaAgregarPrecio(1, <?php echo $value["id_precio"]; ?>, false);" align="center">
                            <?php
                            switch ($value["tipo_bilateral"]) {
                                case "0":
                                    echo("");
                                    $tipo_bilateral = "-1";
                                    break;
                                case "1":
                                    echo("Unilateral");
                                    $tipo_bilateral = "1";
                                    break;
                                case "2":
                                    echo("Bilateral");
                                    $tipo_bilateral = "2";
                                    break;
                            }
                            ?>
                        </td>
                        <td onclick="ventanaAgregarPrecio(1, <?php echo $value["id_precio"]; ?>, false);" align="right"><?php echo("$" . number_format($value["valor"])); ?></td>
                        <td onclick="ventanaAgregarPrecio(1, <?php echo $value["id_precio"]; ?>, false);" align="right"><?php echo("$" . number_format($value["valor_cuota"])); ?></td>
                        <td onclick="ver_historia_precios(1, <?php echo($codigo_servicio); ?>, <?php echo($tipo_bilateral); ?>)" align="right"><img src="../imagenes/ver_lista.png" alt="Ver Precio"></td>
                        <td align="right">
                			<?php
                    			if ($value["ind_activo_aux"] == "1") {
							?>
                        	<img src="../imagenes/add_elemento.png" title="Agregar Precio" onclick="ventanaAgregarPrecio(2, <?php echo$value["id_precio"]; ?>, false);" />
		                    <?php
								}
							?>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="9">No hay resultados</td>
                </tr>
                <?php
            }
            ?>
        </table>
        <script id="ajax">
            //<![CDATA[ 
            $(function () {
                $("#tblPrecios", "table").each(function (i) {
                    $(this).text(i + 1);
                });

                $("#tblPrecios").each(function () {
                    var currentPage = 0;
                    var numPerPage = 10;
                    var $table = $(this);
                    $table.bind("repaginate", function () {
                        $table.find("tbody tr").hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
                    });
                    $table.trigger("repaginate");
                    var numRows = $table.find("tbody tr").length;
                    var numPages = Math.ceil(numRows / numPerPage);
                    var $pager = $('<div class="pager"></div>');
                    for (var page = 0; page < numPages; page++) {
                        $('<span class="page-number"></span>').text(page + 1).bind("click", {
                            newPage: page
                        }, function (event) {
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

    case "4": //Formulario de creación/edición de precios
        @$tipoServicio = $utilidades->str_decode($_POST["tipoServicio"]);
        @$idPrecio = $utilidades->str_decode($_POST["idPrecio"]);
        //1=modificar; 2=Agregar
        @$tipo_accion = $utilidades->str_decode($_POST["tipo_accion"]);
		
        $idServicio = "0";
        $fecha_inicial = "";
        $fecha_final = "";
        $tipo_bilateral = "0";
        $valor_total = "0";
        $valor_cuota = "0";
        $servicio = "";
        $rta_aux = 0;
        $texto_boton = "";
        $nombre_procedicimiento = "";

        if ($idPrecio != "0") {
            $rta_aux = $dbListasPrecios->getPrecio($idPrecio);
			
            if ($tipo_accion == 1) {//Modificar precio
                if (count($rta_aux) >= 1) {
                    $fecha_inicial = $rta_aux["fecha_ini_aux"];
                    $fecha_final = $rta_aux["fecha_fin_aux"];

                    //Evita que la fecha se imprima 00/00/0000 si esta vacia
                    if ($rta_aux["fecha_fin_aux"] == "00/00/0000") {
                        $fecha_final = "";
                    } else {
                        $fecha_final = $rta_aux["fecha_fin_aux"];
                    }

                    $tipo_bilateral = $rta_aux["tipo_bilateral"];
                    $valor_total = $rta_aux["valor"];
                    $valor_cuota = $rta_aux["valor_cuota"];

                    if ($tipoServicio == "P") {
                        $nombre_procedicimiento = "Procedimiento";
                        $servicio = $rta_aux["nombre_procedimiento"];
                        $idServicio = $rta_aux["cod_procedimiento"];
                    } else if ($tipoServicio == "I") {
                        $nombre_procedicimiento = "Insumo";
                        $servicio = $rta_aux["nombre_insumo"];
                        $idServicio = $rta_aux["cod_insumo"];
                    } else if ($tipoServicio == "M") {
                        $nombre_procedicimiento = "Medicamento";
                        $servicio = $rta_aux["nombre_generico"];
                        $idServicio = $rta_aux["cod_medicamento"];
                    } else if ($tipoServicio == "Q") {
                        $nombre_procedicimiento = "Paquete";
                        $servicio = $rta_aux["nom_paquete_p"];
                        $idServicio = $rta_aux["id_paquete_p"];
                    }
                }
                $texto_boton = "Modificar precio";
            } else if ($tipo_accion == 2) {//Agregar nuevo precio
                if (count($rta_aux) >= 1) {
                    //Fecha inicial
                    $fecha_inicial = $rta_aux["fecha_ini_aux"];
                    list($dia_ini, $mes_ini, $ano_ini) = explode("/", $fecha_inicial);
                    $var_fecha_ini = strtotime($ano_ini . "-" . $mes_ini . "-" . $dia_ini);

                    //Fecha final					
                    $fecha_final = $rta_aux["fecha_fin_aux"];
                    $var_fecha_fin = "";
                    if ($fecha_final != "") {
                        list($dia_fin, $mes_fin, $ano_fin) = explode("/", $fecha_final);
                        $var_fecha_fin = strtotime($ano_fin . "-" . $mes_fin . "-" . $dia_fin);
                    }

                    //Fecha final anterior
                    $fecha_final_anterior = $rta_aux["fecha_fin_aux"];
                    $var_fecha_fin_ant = "";
                    if ($fecha_final_anterior != "") {
                        list($dia_fin_ant, $mes_fin_ant, $ano_fin_ant) = explode("/", $fecha_final_anterior);
                        $var_fecha_fin_ant = strtotime($ano_fin_ant . "-" . $mes_fin_ant . "-" . $dia_fin_ant);
                    }

                    if ($rta_aux["fecha_fin_aux"] == "00/00/0000") {
                        $fecha_final = "";
                    } else {
                        $fecha_final = $rta_aux["fecha_fin_aux"];
                    }

                    //Fecha hoy
                    $fecha_hoy = $variables->getFechaActualMostrar();
                    list($dia_hoy, $mes_hoy, $ano_hoy) = explode("/", $fecha_hoy["fecha_actual_mostrar"]);
                    $var_fecha_hoy = strtotime($ano_hoy . "-" . $mes_hoy . "-" . $dia_hoy);

                    // Fecha inicial del nuevo precio es la fecha final del anterior y se le suma un dia  
                    $fecha_resultado = $variables->sumar_dias_fecha($fecha_final, 1);
                    $fecha_inicial = $fecha_resultado["fecha_resultado"];

                    if ($fecha_final == "") { // Si la fecha final del anterior precio es vacia  
                        // Fecha inicial del nuevo precio es la fecha actual
                        $fecha_inicial = $fecha_hoy["fecha_actual_mostrar"];
                    }
                    $fecha_final = "";

                    $tipo_bilateral = $rta_aux["tipo_bilateral"];
                    $valor_total = "";
                    $valor_cuota = "";

                    if ($tipoServicio == "P") {
                        $nombre_procedicimiento = "Procedimiento";
                        $servicio = $rta_aux["nombre_procedimiento"];
                        $idServicio = $rta_aux["cod_procedimiento"];
                    } else if ($tipoServicio == "I") {
                        $nombre_procedicimiento = "Insumo";
                        $servicio = $rta_aux["nombre_insumo"];
                        $idServicio = $rta_aux["cod_insumo"];
                    } else if ($tipoServicio == "M") {
                        $nombre_procedicimiento = "Medicamento";
                        $servicio = $rta_aux["nombre_generico"];
                        $idServicio = $rta_aux["cod_medicamento"];
                    } else if ($tipoServicio == "Q") {
                        $nombre_procedicimiento = "Paquete";
                        $servicio = $rta_aux["nom_paquete_p"];
                        $idServicio = $rta_aux["id_paquete_p"];
                    }
                }
                $texto_boton = "Agregar precio";
            }
        } else {
            if ($tipoServicio == "P") {
                $nombre_procedicimiento = "Procedimiento";
            } else if ($tipoServicio == "I") {
                $nombre_procedicimiento = "Insumo";
            } else if ($tipoServicio == "M") {
                $nombre_procedicimiento = "Medicamento";
            } else if ($tipoServicio == "Q") {
                $nombre_procedicimiento = "Paquete";
            }

            $texto_boton = "Agregar precio";
        }
        ?>
        <div class="encabezado">
            <h3>Agregar Precio</h3>
        </div>
        <br/><br/>
        <div id="advertenciasAgregarPrecio">
            <div class="contenedor_error" id="contenedor_error_gurdar"></div>
            <div class="contenedor_exito" id="contenedor_exito_gurdar"></div>
        </div>
        <div>
            <table style="width: 75%; margin: auto;">
                <tr>
                    <td style="width: 10%;">
                        <label class="inline"><?php echo $nombre_procedicimiento; ?>:</label>
                    </td>
                    <td>
                        <label class="inline" id="txtNombreServicio" style="font-weight: 700;text-align: left;"><?php echo $servicio; ?></label>
                        <input type="hidden" id="hdd_idServicio" name="hdd_idServicio" value="<?php echo $idServicio; ?>" />
                    </td>
                    <?php
                    if ($idPrecio == 0) {//Muestra / Oculta el boton de buscar Servicio
                        ?>
                        <td style="width: 10%;">
                            <img style="cursor: pointer;" src="../imagenes/Search-icon.png" onclick="ventanaServicios(1);" />
                        </td>
                        <?php
                    }
                    ?>
                    <td style="width:5%;"></td>
                </tr>
            </table>
            <form id="frmAgregarPrecio" name="frmAgregarPrecio">
                <table style="width: 80%;margin: auto;">
                    <tr>
                        <td align="right">
                            <label class="inline">Fecha inicial:</label>
                        </td>
                        <td>
                            <input type="text" class="input" maxlength="10" style="width:120px;" value="<?php echo $fecha_inicial; ?>" name="fechaInicial" id="fechaInicial" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                        </td>
                        <td align="right">
                            <label class="inline">Fecha final:</label>
                        </td>
                        <td style="">
                            <input type="text" class="input" maxlength="10" style="width:120px;" value="<?php echo $fecha_final; ?>" name="fechaFinal" id="fechaFinal" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                        </td>
                    </tr>
                    <tr>
                        <td align="right">
                            <label class="inline">Tipo de valor:</label>
                        </td>
                        <td colspan="3" align="left">
                            <?php
                            //Lista de bilateralidades
                            $arr_bilateralidades = array();
                            $arr_bilateralidades[0]["id"] = 0;
                            $arr_bilateralidades[0]["valor"] = "No aplica";
                            $arr_bilateralidades[1]["id"] = 1;
                            $arr_bilateralidades[1]["valor"] = "Unilateral";
                            $arr_bilateralidades[2]["id"] = 2;
                            $arr_bilateralidades[2]["valor"] = "Bilateral";

                            $combo->getComboDb("cmb_tipo_bilateral", $tipo_bilateral, $arr_bilateralidades, "id, valor", "", "", true, "width:120px;");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">
                            <label class="inline">Valor cuota: $</label>
                        </td>
                        <td>
                            <input type="text" id="txtValorCuota" name="txtValorCuota" onkeypress="return validarNro(event);" onblur="trim_cadena(this);" value="<?php echo $valor_cuota; ?>" style="width: 160px;" />
                        </td>
                        <td align="right">
                            <label class="inline">Valor total: $</label>
                        </td>
                        <td>
                            <input type="text" id="txtValorTotal" name="txtValorTotal" onkeypress="return validarNro(event);" onblur="trim_cadena(this);" value="<?php echo $valor_total; ?>" style="width: 160px;" />
                        </td>  
                    </tr>

                </table>
                <div>
                    <input type="hidden" id="idServicio" name="idServicio" value="<?php echo $tipoServicio; ?>" />
                    <input type="hidden" id="rtaAgregarPrecio" name="rtaAgregarPrecio" value="0" />
                    <input type="hidden" id="idPrecio" name="idPrecio" value="<?php echo $idPrecio; ?>" />
                    <div id="d_agregar_precio" style="display:none;"></div>
                    <?php
                    if ($tipo_accion == 1) {//Modificar precio
                        ?><input type="submit" id="btnGuardarValor" name="btnGuardarValor" class="btnPrincipal" value="<?php echo($texto_boton); ?>" onclick="AgregarPrecio();" /><?php
                    } else if ($tipo_accion == 2) {//Agregar nuevo precio
                        ?>
                        <input type="hidden" id="fecha_final_anterior" name="fecha_final_anterior" value="<?php echo $fecha_final_anterior; ?>" />
                        <input type="hidden" id="fecha_hoy" name="fecha_hoy" value="<?php echo($fecha_hoy["fecha_actual_mostrar"]); ?>" />
                        <input type="button" id="btnGuardarValor" name="btnGuardarValor" class="btnPrincipal" value="<?php echo($texto_boton); ?>" onclick="crear_precios();" />
                        <?php
                    }
                    ?>
            </form>
        </div>
        <script id="ajax">
            $(document).foundation();

            $(function () {
                window.prettyPrint && prettyPrint();
                $("#fechaFinal").fdatepicker({
                    format: "dd/mm/yyyy"
                });
                $("#fechaInicial").fdatepicker({
                    format: "dd/mm/yyyy"
                });
            });
            new imagen_muscular.GuardandoPNGs().resetCanvasMuscular();
            new imagen_tonometria_od.GuardandoPNGs().resetCanvasTonometria_od();
            new imagen_tonometria_oi.GuardandoPNGs().resetCanvasTonometria_oi();
        </script>
        <?php
        break;

    case "5"://Imprime la cabecera de Servicios
        $tipoServicio = $utilidades->str_decode($_POST["tipoServicio"]);
        $tipoServicioTexto = "";

        if ($tipoServicio == "P") {
            $tipoServicioTexto = "Procedimiento";
        } else if ($tipoServicio == "I") {
            $tipoServicioTexto = "Insumo";
        } else if ($tipoServicio == "M") {
            $tipoServicioTexto = "Medicamento";
        } else if ($tipoServicio == "Q") {
            $tipoServicioTexto = "Paquete";
        }
        ?>
        <div class="encabezado">
            <h3><?php echo $tipoServicioTexto; ?></h3>
        </div>
        <form id="frmBuscarServicioFlotante" name="frmBuscarServicioFlotante" > 
            <table style="width: 100%;">
                <tr valign="middle">
                    <td align="center" colspan="2">
                        <div id="advertenciasg">
                            <div class="contenedor_error" id="contenedor_error"></div>
                        </div> 
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="text" id="txtParametroServicio" name="txtParametroServicio" placeholder="Codigo o nombre del <?php echo $tipoServicioTexto; ?>" onblur="convertirAMayusculas(this); trim_cadena(this);" />
                    </td>
                    <td style="width: 10%;">
                        <input type="submit" onclick="buscarServicio()" value="Buscar" class="btnPrincipal peq" />
                    </td>
                </tr>
            </table>
        </form>
        <div id="servicios"></div>
        <input type="hidden" id="hdd_tipoServicio" name="hdd_tipoServicio" value="<?php echo $tipoServicio; ?>" />
        <?php
        break;

    case "6"://Imprime el resultado de la busqueda para Servicios
        $parametro = $utilidades->str_decode($_POST["parametro"]);
        $tipoServicio = $utilidades->str_decode($_POST["tipoServicio"]);
        $indEcopetrol = $utilidades->str_decode($_POST["indEcopetrol"]);
        $idPlan = $utilidades->str_decode($_POST["idPlan"]);
        $rta_aux = "";

        $plan = $dbPlanes->getPlan($id_plan);

        if ($tipoServicio == "P") {
            if ($indEcopetrol == "0") {
                if ($plan['ind_iss'] == 1) {//Consulta el manual ISS 2001
                    $rta_aux = $dbMaestroProcedimientos->getProcedimientosIss2001($parametro);
                } else {//Consula los procedimientos de la tabla maestro_procedimientos
                    $rta_aux = $dbMaestroProcedimientos->getProcedimientos($parametro);
                }
            } else if ($indEcopetrol == "1") {
                $rta_aux = $dbMaestroProcedimientos->getProcedimientosEcopetrol($parametro);
            }
            ?>
            <table class="paginated modal_table" id="tablaServicios" style="width: 99%; margin: auto;">
                <thead>

                    <tr>
                        <th style="width:5%;">Codigo</th>
                        <th style="width:36%;">Nombre</th>

                    </tr>
                </thead>
                <?php
                if (count($rta_aux) >= 1) {
                    foreach ($rta_aux as $value) {
                        ?>
                        <tr onclick="seleccionar_servicio('<?php echo($value["cod_procedimiento"]); ?>', '<?php echo($value["nombre_procedimiento"]); ?>')">
                            <td><?php echo($value["cod_procedimiento"]); ?></td>
                            <td style="text-align: left;"><?php echo($value["nombre_procedimiento"]); ?></td>

                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <td colspan="6">No hay resultados</td>
                    <?php
                }
                ?>
            </table>
            <?php
        } else if ($tipoServicio == "M") {
            $rta_aux = $dbMaestroMedicamentos->getMedicamentos($parametro);
            ?>
            <table class="paginated modal_table" id="tablaServicios" style="width: 99%; margin: auto;">
                <thead>

                    <tr>
                        <th style="width:5%;">Codigo</th>
                        <th style="width:36%;">Nombre</th>

                    </tr>
                </thead>
                <?php
                if (count($rta_aux) >= 1) {

                    foreach ($rta_aux as $value) {
                        ?>
                        <tr onclick="seleccionar_servicio('<?php echo $value["cod_medicamento"]; ?>', '<?php echo $value["nombre_generico"]; ?>')">
                            <td><?php echo $value["cod_medicamento"]; ?></td>
                            <td style="text-align: left;"><?php echo $value["nombre_aux"]; ?></td>

                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <td colspan="6">No hay resultados</td>
                    <?php
                }
                ?>
            </table>
            <?php
        } else if ($tipoServicio == "I") {
            $rta_aux = $dbMaestroInsumos->getInsumos($parametro);
            ?>
            <table class="paginated modal_table" id="tablaServicios" style="width: 99%; margin: auto;">
                <thead>

                    <tr>
                        <th style="width:5%;">Codigo</th>
                        <th style="width:36%;">Nombre</th>

                    </tr>
                </thead>
                <?php
                if (count($rta_aux) >= 1) {
                    foreach ($rta_aux as $value) {
                        ?>
                        <tr onclick="seleccionar_servicio('<?php echo $value["cod_insumo"]; ?>', '<?php echo $value["nombre_insumo"]; ?>')">
                            <td><?php echo $value["cod_insumo"]; ?></td>
                            <td style="text-align: left;"><?php echo $value["nombre_insumo"]; ?></td>

                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <td colspan="6">No hay resultados</td>
                    <?php
                }
                ?>
            </table>
            <?php
        } else if ($tipoServicio == "Q") {
			$rta_aux = $dbPaquetesProcedimientos->buscarPaquetesActivos($parametro);
            ?>
            <table class="paginated modal_table" id="tablaServicios" style="width: 99%; margin: auto;">
                <thead>

                    <tr>
                        <th style="width:5%;">Codigo</th>
                        <th style="width:36%;">Nombre</th>

                    </tr>
                </thead>
                <?php
                if (count($rta_aux) >= 1) {
                    foreach ($rta_aux as $value) {
                        ?>
                        <tr onclick="seleccionar_servicio('<?php echo $value["id_paquete_p"]; ?>', '<?php echo $value["nom_paquete_p"]; ?>')">
                            <td><?php echo $value["id_paquete_p"]; ?></td>
                            <td style="text-align: left;"><?php echo $value["nom_paquete_p"]; ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <td colspan="6">No hay resultados</td>
                    <?php
                }
                ?>
            </table>
            <?php
        }
        ?>
        <script id="ajax">
            //<![CDATA[ 
            $(function () {
                $("#tablaServicios", "table").each(function (i) {
                    $(this).text(i + 1);
                });

                $("#tablaServicios").each(function () {
                    var currentPage = 0;
                    var numPerPage = 10;
                    var $table = $(this);
                    $table.bind("repaginate", function () {
                        $table.find("tbody tr").hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
                    });
                    $table.trigger("repaginate");
                    var numRows = $table.find("tbody tr").length;
                    var numPages = Math.ceil(numRows / numPerPage);
                    var $pager = $('<div class="pager"></div>');
                    for (var page = 0; page < numPages; page++) {
                        $('<span class="page-number"></span>').text(page + 1).bind("click", {
                            newPage: page
                        }, function (event) {
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

    case "7"://Funcion que agrega el precio
        $idUsuario = $_SESSION["idUsuario"];
        $fechaInicial = $utilidades->str_decode($_POST["fechaInicial"]);
        $fechaFinal = $utilidades->str_decode($_POST["fechaFinal"]);
        $idPrecio = $utilidades->str_decode($_POST["idPrecio"]);
        $codServicio = $utilidades->str_decode($_POST["codigoServicioSeleccionado"]);
        $tipoServicio = $utilidades->str_decode($_POST["tipoServicio"]);
        $plan = $utilidades->str_decode($_POST["plan"]);
        $tipoBilateral = $utilidades->str_decode($_POST["tipoBilateral"]);
        $txtValorTotal = $utilidades->str_decode($_POST["txtValorTotal"]);
        $txtValorCuota = $utilidades->str_decode($_POST["txtValorCuota"]);

        if ($idPrecio != "0") {
            $accion = 2; //Actualiza el registro
        } else {
            $accion = 1; //Crea nuevo registro
        }

        $rta_aux = $dbListasPrecios->guardarEditarListasPrecios($accion, $tipoServicio, $fechaInicial, $fechaFinal,
				   $codServicio, $plan, $tipoBilateral, $txtValorTotal, $txtValorCuota, $idUsuario, $idPrecio);
        ?>
        <input type="hidden" id="hdd_resul_agregar_precio" value="<?php echo($rta_aux); ?>" />
        <?php
        break;

    case "8"://Para ver los precios de un procedimiento seleccionado
        $cod_procedimiento = $utilidades->str_decode($_POST["cod_procedimiento"]);
        $id_plan = $utilidades->str_decode($_POST["id_plan"]);
        $tipo_servicio = $utilidades->str_decode($_POST["tipo_servicio"]);
        $nombre_convenio = $utilidades->str_decode($_POST["nombre_convenio"]);
        $tipo_bilateral = $utilidades->str_decode($_POST["tipo_bilateral"]);
        $tabla_planes = $dbPlanes->getPlan($id_plan);
        $nombre_plan = $tabla_planes["nombre_plan"];
        $tabla_precios = $dbListasPrecios->getHistorialPrecios($cod_procedimiento, $id_plan, $tipo_servicio, $tipo_bilateral);
        $nombre_procedimiento = $tabla_precios[0]["nombre_aux"];
        ?>
        <div>
            <table class="paginated modal_table" style="width: 99%; margin: auto;">
                <thead>
                    <tr>
                        <th colspan="5">Precios de <br /><?php echo($nombre_procedimiento); ?> <br /> Plan: <?php echo($nombre_plan); ?> - Convenio: <?php echo($nombre_convenio); ?></th>
                    </tr>
                    <tr>
                        <th style="width:10%;">Fecha inicio</th>
                        <th style="width:10%;">Fecha fin</th>
                        <th style="width:10%;">Tipo valor</th>
                        <th style="width:10%;">Valor</th>
                        <th style="width:10%;">Valor cuota</th>
                    </tr>
                </thead>
                <?php
                if (count($tabla_precios) > 0) {
                    foreach ($tabla_precios as $value) {
                        ?>
                        <tr>
                            <td align="center"><?php echo $value["fecha_ini_aux"]; ?></td>
                            <td align="center"><?php echo $value["fecha_final_aux"]; ?></td>
                            <td align="center">
                                <?php
                                switch ($value["tipo_bilateral"]) {
                                    case "0":
                                        echo("");
                                        break;
                                    case "1":
                                        echo("Unilateral");
                                        break;
                                    case "2":
                                        echo("Bilateral");
                                        break;
                                }
                                ?>
                            </td>
                            <td align="right"><?php echo "$" . number_format($value["valor"]); ?></td>
                            <td align="right"><?php echo "$" . number_format($value["valor_cuota"]); ?></td>
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
        </div>
        <script id="ajax">
            //<![CDATA[ 
            $(function () {
                $(".paginated", "table").each(function (i) {
                    $(this).text(i + 1);
                });

                $("table.paginated").each(function () {
                    var currentPage = 0;
                    var numPerPage = 10;
                    var $table = $(this);
                    $table.bind("repaginate", function () {
                        $table.find("tbody tr").hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
                    });
                    $table.trigger("repaginate");
                    var numRows = $table.find("tbody tr").length;
                    var numPages = Math.ceil(numRows / numPerPage);
                    var $pager = $('<div class="pager"></div>');
                    for (var page = 0; page < numPages; page++) {
                        $('<span class="page-number"></span>').text(page + 1).bind("click", {
                            newPage: page
                        }, function (event) {
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

    case "9"://Funcion que agrega el precio
        $idUsuario = $_SESSION["idUsuario"];
        $fechaInicial = $utilidades->str_decode($_POST["fechaInicial"]);
        $fechaFinal = $utilidades->str_decode($_POST["fechaFinal"]);
        $fechaFinalAnterior = $utilidades->str_decode($_POST["fechaFinalAnterior"]);
        $idPrecio = $utilidades->str_decode($_POST["idPrecio"]);
        $codServicio = $utilidades->str_decode($_POST["codigoServicioSeleccionado"]);
        $tipoServicio = $utilidades->str_decode($_POST["tipoServicio"]);
        $plan = $utilidades->str_decode($_POST["plan"]);
        $tipoBilateral = $utilidades->str_decode($_POST["tipoBilateral"]);
        $txtValorTotal = $utilidades->str_decode($_POST["txtValorTotal"]);
        $txtValorCuota = $utilidades->str_decode($_POST["txtValorCuota"]);

        $rta_aux = $dbListasPrecios->GuardarListasPrecios($tipoServicio, $fechaInicial, $fechaFinal, $fechaFinalAnterior, $codServicio, $plan, $tipoBilateral,
				$txtValorTotal, $txtValorCuota, $idUsuario, $idPrecio);
        ?>
        <input type="hidden" id="hdd_exito" name="hdd_exito" value="<?php echo($rta_aux); ?>" />
        <div class="contenedor_error" id="contenedor_error_gurdar"></div>
        <div class="contenedor_exito" id="contenedor_exito_gurdar"></div>
        <?php
        break;

    case "10":
        $id_plan = $utilidades->str_decode($_POST["idPlan"]);
        $plan = $dbPlanes->getPlan($id_plan);
        ?>
        <div class="encabezado">
            <h3>Importar precios manual ISS 2001</h3>
        </div>
        <br/><br/>
        <div id="advertenciasAgregarPrecio">
            <div class="contenedor_error" id="contenedor_error_gurdar"></div>
            <div class="contenedor_exito" id="contenedor_exito_gurdar"></div>
        </div>
        <div>
            <p>Este procedimiento realiza la importación de precios del manual tarifario ISS 2001 tomando como base los parámetros de configuración para el plan seleccionado: <span style="font-weight: bold;"><?= $plan['nombre_plan'] ?></span>; incremento del <span style="font-weight: bold;"><?= $plan['porc_iss'] ?>%</span> </p>
            <br/>
            <p>¡Los precios actuales serán actualizados y los nuevos serán agregados!</p>
            <br/>
            <div>
                <input type="button" id="btnGuardarValor" name="btnGuardarValor" class="btnPrincipal" value="¿Importar ISS 2001?" onclick="importarISS();" />
            </div>
        </div>

        <div id="divResultadoISS"></div>

        <?php
        break;

    case "11"://Importar manual ISS 2001
        $idUsuario = $_SESSION["idUsuario"];
        $plan = $utilidades->str_decode($_POST["plan"]);

        $resultado = $dbPrecios->importarISS2001($plan, $idUsuario);
        ?>
        <input type="hidden" name="hdd_importarISS2001" id="hdd_importarISS2001" value="<?= $resultado ?>"/>
        <?php
        break;
		
    case "12": //Formulario de importación de precios por archivos CSV
		@$tipo_servicio = $utilidades->str_decode($_POST["tipo_servicio"]);
		$cod_servicio = "";
		$liquidador = "";
		$tipo_liquidador = "";
		$tipo_servicio = strtoupper($tipo_servicio);
        ?>
        <fieldset style="width: 90%; margin: auto;">
            <legend>Importar precios por .CSV:</legend>
            <div>
                <p>Este procedimiento finalizar&aacute; la fecha de vigencia de los precios que actualmente se encuentran vigentes y, asignará los precios que se encuentren en el archivo con extensi&oacute;n .CSV</p>
            </div>

            <div style="text-align: left;">
            
              <div style="text-align:left; float: left; width: 50%;" > 
              
                    <p>El archivo .CSV debe contar con la siguiente estructura jer&aacute;rquica y de encabezado (Sensible a may&uacute;sculas):</p>
                    <ol>
                        <?php
                            if($tipo_servicio === "I"){
                                $cod_servicio = "cod_insumo";
                                $liquidador = "(Porcentaje)";
                                $tipo_liquidador = "(%)";
                            }else if($tipo_servicio === "P"){
                                $cod_servicio = "cod_procedimiento";
                                $liquidador = "(Número de UVRs o Porcentaje)";
                                $tipo_liquidador = "(UVR - %)";
                            }
                        ?>
                        
                        <li><strong><?= $cod_servicio;?></strong></li>
                        <li><strong>valor</strong></li>
                        <li><strong>fecha_ini </strong>(formato: dd/mm/aaaa)</li>
                        <li><strong>fecha_fin </strong>(formato: dd/mm/aaaa)</li>
                        <li><strong>valor_cuota</strong></li>
                        <li><strong>tipo_bilateral </strong>(0: No aplica - 1: Unilateral - 2: Bilateral)</li>
                        <li><strong>liquidador </strong><?= $liquidador?></li>
                        <li><strong>tipo_liquidador </strong><?= $tipo_liquidador?></li>
                    </ol>
                </div>
                
                <div style="text-align:center; float: left; width: 40%; height:100%; margin-top:90px; margin-left:; " > 
                 <form id="frmTiposLiquidacion" name="frmTiposLiquidacion">
                    	 <table style="width: 100%;margin: auto;">
                          <tr>
                            <td align="right">
                                <label class="inline"><b>Precio UVR:</b></label>
                            </td>
                            <td>
                                <input onkeypress="solo_numeros(event, false);" type="text" class="input" maxlength="10" style="width:150px;"<?php if ($tipo_servicio === "I") { ?> disabled="disabled"<?php } ?> value="" name="txt_precio_uvr" id="txt_precio_uvr" tabindex=""  />
                            </td>
                          </tr>
                          <tr>
                            <td align="right">
                                <label class="inline"><b>Porcentaje:</b></label>
                            </td>
                            <td style="">
                                <input onkeypress="solo_numeros(event, false);" type="text" class="input" maxlength="10" style="width:150px;" value="" name="txt_porcentaje" id="txt_porcentaje" tabindex="" />
                            </td>
                   		</tr> 
                        <tr>
                            <td style="width:70%;" align="right">
                                <label class="inline"><b>Porcentaje Excel:</b></label>
                            </td>
                            <td align="left" style="width30%;">
                               <input type="checkbox" id="chk_porcentaje_xsx" onchange='mostrarAlertEstado(this);' name="chk_porcentaje_xsx" value="">
                            </td>
                   		</tr> 
                         </table>
                     
                     </form>
               </div>   
                             
                
            </div>
            <form id="frm_preciosCSV" name="frm_preciosCSV" method="post">
                <table border="0" style="width:100%; margin: 0 auto;">
                    <tr>
                        <td style="text-align: center;">
                            <label>Seleccione el archivo (.csv)*</label>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center;">
                            <input type="file" id="fileCSV" name="fileCSV" accept=".csv" />
                        </td>
                        <td align="right" style="">
                            <input type="submit" value="Subir .CSV" onclick="validarFrmPreciosCSV();" class="btnPrincipal"/>
                        </td>
                         <td align="left" style="">
                            <input type="button" value="Descargar Excel" onclick="generarArchivoExcel();" class="btnPrincipal"/>
                        </td>
                    </tr>
                </table>
            </form>
            <div id="resultadoImportarCSV"></div>
        </fieldset>
        <br>
        <?php
        break;
		
    case "13": //Subir archivo de precios CSV
        @$file = $_FILES["fileCSV"]["name"];
        $rta = copy($_FILES["fileCSV"]["tmp_name"], "tmp/" . $_FILES["fileCSV"]["name"]);
	?>
    <input type="text" id="hddArchivo" name="hddArchivo" value="<?= $rta ? $_FILES["fileCSV"]["name"] : -1 ?>" />
    <?php
        break;

    case "14": //Procesar archivo de precios CSV
        @$file = $utilidades->str_decode($_POST["file"]);
        @$idPlan = $utilidades->str_decode($_POST["idPlan"]);
        @$tipoServicio = $utilidades->str_decode($_POST["tipoServicio"]);
		@$porcentaje = $utilidades->str_decode($_POST["porcentaje"]);
		@$precioUvr = $utilidades->str_decode($_POST["precioUvr"]);
		@$check_porc = $utilidades->str_decode($_POST["check_porc"]);
		
		if(!empty($porcentaje)){
			$porcentaje = $porcentaje/100;
		}
		
        $id_usuario = $_SESSION["idUsuario"];
        $rutaFile = "tmp/".$file;
        $rta = -3;

        if ($fp = file($rutaFile)) {
            $filas = count($fp);

            if ($filas > 0) {/* Valida Filas */
                /* Valida la versión del archivo - Deben de existir 6 columnas */
                $columnas = 0;
                for ($index = 1; $index < 2; $index++) {
                    $columnas = count(explode(";", $fp[$index]));
                }

                if ($columnas == 8) {
                    $arrDatos = "";
                    $contador = 0;
                    $cod_servicio = "";
                    $valor = "";
                    $fecha_ini = "";
                    $fecha_fin = "";
                    $valor_cuota = "";
                    $tipo_bilateral = "";
					$liquidador = "";
					$tipo_liquidador = "";

                    for ($index = 1; $index <= $filas; $index++) {
                        $contador++;
                        $arrDato = explode(";", $fp[$index]);

                        $cod_servicio = trim($arrDato[0]);
                        $valor = trim($arrDato[1]);
                        $fecha_ini = trim($arrDato[2]);
                        $fecha_fin = trim($arrDato[3]);
                        $valor_cuota = trim($arrDato[4]);
                        $tipo_bilateral = trim($arrDato[5]);
						$liquidador = (trim($arrDato[6]));
						$tipo_liquidador = trim($arrDato[7]);
						
						
						if($tipoServicio === "I" && strcasecmp($tipo_liquidador, "UVR") === 0 ){
							$tipo_liquidador = "";
							$liquidador = "";	
						}
						/* Hace el cálculo de el valor del procedimiento, dependiendo del tipo de liquidador*/
						if(strcasecmp($tipo_liquidador, "UVR") === 0 && !empty($precioUvr)){
							$valor = $liquidador*$precioUvr;
	
						}else{
							$valor = $valor;	
						}
						if($check_porc == "true"){
							if(strcasecmp($tipo_liquidador, "%") === 0){
								$porcentaje_liq = ($liquidador/100);
								$valor = ceil($valor+($valor*$porcentaje_liq));
							}
						}else if($check_porc == "false"){
							if(strcasecmp($tipo_liquidador, "%") === 0){
								
								$valor = ceil($valor+($valor*$porcentaje));	
							}
						}else{
							$valor = $valor;
						}	
						
						$guarda = false;
                        if ($contador < 1000) {/* Realiza el salto cada 1000 registro (OJO,el mismo valor 4 l�neas m�s abajo) */
                            if ($index == $filas) {
                                $guarda = true;
                            }
							if ($cod_servicio != "") {
								if ($arrDatos != "") {
									$arrDatos .= "|";
								}
                            	$arrDatos .= $cod_servicio.";".$valor.";".$fecha_ini.";".$fecha_fin.";".$valor_cuota.";".$tipo_bilateral.";".$liquidador.";".$tipo_liquidador;
							}
                        } else {
							if ($arrDatos != "") {
								$arrDatos .= "|";
							}
                            $arrDatos .= $cod_servicio.";".$valor.";".$fecha_ini.";".$fecha_fin.";".$valor_cuota.";".$tipo_bilateral.";".$liquidador.";".$tipo_liquidador;
                            $guarda = true;
                        }
						
                        if ($guarda) {
                            $tmpFilas = count(explode("|", $arrDatos)); //Calcula las filas temporales
                           $rta = $dbPrecios->cargarPreciosCSV($id_usuario, $idPlan, $arrDatos, $tmpFilas, $tipoServicio);

                            if ($rta == -1) { //Si hay error al momento de guardar
                                break;
                            }

                            $arrDatos = "";
                            $contador = 0;
                            $guarda = false;
                        }
                    }
                } else {
                    $rta = -5; //Error de versión del archivo
                }
            } else {
                $rta = -4; //El archivo está vacio
            }
        }
	?>
    <input type="hidden" id="hddResultadoImportacionCSV" name="hddResultadoImportacionCSV" value="<?= $rta ?>" />
    <input type="hidden" id="hddRutaFile" name="hddRutaFile" value="<?= $rutaFile ?>" />
    <?php
        break;

    case "16": //Elimina el archivo que fue cargado de forma temporal
        @$file = $utilidades->str_decode($_POST["file"]);
		
        unlink($file);
        break;
	
	case "17":
	   require_once("../funciones/PHPExcel/Classes/PHPExcel.php");
	   require_once("../db/DbVariables.php");
	   require_once("../db/DbPlanes.php");
	   
	   $objPHPExcel = new PHPExcel();
	   $dbVariables = new Dbvariables();	
	   $dbPlanes = new DbPlanes();
	   
	   @$id_plan = $utilidades->str_decode($_POST["id_plan"]);
	   @$tipo_servicio = $utilidades->str_decode($_POST["tipo_servicio"]);
	   $lista_precios = $dbPrecios->consultarDatosListaPrecios($id_plan,$tipo_servicio);
	   $plan = $dbPlanes->getPlan($id_plan);
	   $cod_servicio = "";
	   $nombre_serv = "";
	   
	   if($tipo_servicio === "P"){
		   $cod_servicio = "COD_PROCEDIMIENTO";
		   $nombre_serv = "PROCEDIMIENTO"; 
	   }else if($tipo_servicio === "I"){
		   $cod_servicio = "COD_INSUMO";
		   $nombre_serv = "INSUMO"; 
	   }
	   
	    if(count($lista_precios)>0){
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("A")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("B")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("C")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("D")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("E")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("F")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("G")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("H")->setWidth(25);
			$max_col_letra = "H";
			$contador_linea = 1;
			$objPHPExcel->getActiveSheet()						 
						->setCellValue("A".$contador_linea, $cod_servicio)
						->setCellValue("B".$contador_linea, "VALOR")
						->setCellValue("C".$contador_linea, "FECHA_INI")
						->setCellValue("D".$contador_linea, "FECHA_FIN")
						->setCellValue("E".$contador_linea, "VALOR_CUOTA")
						->setCellValue("F".$contador_linea, "TIPO_BILATERALIDAD")
						->setCellValue("G".$contador_linea, "LIQUIDADOR")
						->setCellValue("H".$contador_linea, "TIPO_LIQUIDADOR");
			$objPHPExcel->getActiveSheet()->getStyle("A".$contador_linea.":".$max_col_letra.$contador_linea)->getFont()->setBold(true);
			
			$contador_linea++;
			$cod_servicio = "";
			
			foreach($lista_precios as $value){
				
				$fecha_ini =  $dbVariables->cambiarFormatoFecha($value["fecha_ini"]);
				$fecha_fin =  $dbVariables->cambiarFormatoFecha($value["fecha_fin"]);
				
				if(!is_null($value["cod_procedimiento"])){
					$cod_servicio = $value["cod_procedimiento"];
				}else if(!is_null($value["cod_insumo"])){
					$cod_servicio = $value["cod_insumo"];
				}
				
				$objPHPExcel->getActiveSheet()
							->setCellValueExplicit("A".$contador_linea,$cod_servicio,PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValue("B".$contador_linea, $value["valor"])
							->setCellValue("C".$contador_linea,	$fecha_ini)
							->setCellValue("D".$contador_linea,	$fecha_fin)
							->setCellValue("E".$contador_linea, $value["valor_cuota"])
							->setCellValue("F".$contador_linea, $value["tipo_bilateral"])
							->setCellValue("G".$contador_linea, $value["ind_liquidacion"])
							->setCellValue("H".$contador_linea, $value["tipo_liquidacion"]);
				$contador_linea++;
				
			}
			$objPHPExcel->getActiveSheet()->setTitle("Lista precios");
			
			//Se borra el reporte previamente generado por el usuario
			$id_usuario = $_SESSION["idUsuario"];
			$nombre_plan = mb_strtoupper($plan["nombre_plan"], 'UTF-8');
			@unlink("./tmp/LISTA_PRECIO_".$nombre_serv."_".$nombre_plan.".xlsx");
			
			// Save Excel 2007 file
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
			$objWriter->save("./tmp/LISTA_PRECIO_".$nombre_serv."_".$nombre_plan.".xlsx");
			 ?>
        		<form name="frm_archivo_lista_precios" id="frm_archivo_lista_precios" method="post" action="tmp/LISTA_PRECIO_<?php echo($nombre_serv."_".$nombre_plan); ?>.xlsx">
        		</form>
        		<script id="ajax" type="text/javascript">
					document.getElementById("frm_archivo_lista_precios").submit();
				</script>
			<?php 
			
		}
	   	
	break;
}
?>
