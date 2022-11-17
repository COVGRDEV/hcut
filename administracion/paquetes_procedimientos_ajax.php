<?php
header("Content-Type: text/xml; charset=UTF-8");
session_start();
require_once("../funciones/Class_Combo_Box.php");
require_once("../funciones/Utilidades.php");
require_once("../db/DbMaestroProcedimientos.php");
require_once("../db/DbMaestroInsumos.php");
require_once("../db/DbPaquetesProcedimientos.php");


$utilidades = new Utilidades();
$dbMaestroProcedimientos = new DbMaestroProcedimientos();
$dbMaestroInsumos = new DbMaestroInsumos();
$dbPaquetesProcedimientos = new DbPaquetesProcedimientos();
$combo = new Combo_Box();

$id_usuario = $_SESSION["idUsuario"];
$opcion = $_POST["opcion"];
$ind_check_insumos_adic = "none";
$ind_text_adic = "none";
switch ($opcion) {
    case "1": //Formulario de paquetes
        @$funcion = $utilidades->str_decode($_POST["funcion"]);
		
        $cant_procs = 0;
        $nom_paquete = "";
        $titulo = "Nuevo paquete";
        $id_paquete = "";
        $ind_estado = "";
		
        /*$ind_valor_unico = "";
        $ind_anestesia = "";
        $ind_audantia = "";
        $ind_derechos_sala = "";
        $ind_insumos = "";*/
        $ind_auto_anestesia = "";
        $ind_auto_honorarios_medicos = "";
        $ind_auto_ayudantia = "";
        $ind_auto_derechos_sala = "";
		$ind_auto_insumosAdicionales  = "";
		$display_insAdi= "";
        /*$valor_unico = "";
        $valor_insumos_adicionales = "";*/

        if ($funcion == 2) {
            $id_paquete = $utilidades->str_decode($_POST["idPaquete"]);
            $paquete = $dbPaquetesProcedimientos->getPaqueteById($id_paquete);
		
            $lista_procedimientos = $dbPaquetesProcedimientos->getProcedimientosByPaquete($id_paquete);
		
            $cant_procs = count($lista_procedimientos);
            $nom_paquete = $paquete["nom_paquete_p"];
            $titulo = "Paquete ".$paquete["id_paquete_p"];
            $id_paquete = $paquete["id_paquete_p"];
			$precio_paquete = $paquete["precio_paquete"];
			$valor = $paquete["valor"];
            $ind_estado = $paquete["ind_estado"];
			
            $texto_estado = "";
            switch ($ind_estado) {
				case 1:
                    $texto_estado = "Activo";
                    break;
                case 0:
                    $texto_estado = "Inactivo";
                    break;
            }
			
				
            /*$ind_valor_unico = $paquete["ind_valor_unico"] == 1 ? "checked" : "";
            $ind_anestesia = $paquete["ind_anestesia"] == 1 ? "checked" : "";
            $ind_audantia = $paquete["ind_ayudantia"] == 1 ? "checked" : "";
            $ind_derechos_sala = $paquete["ind_derechos_sala"] == 1 ? "checked" : "";
            $ind_insumos = $paquete["ind_insumos"] == 1 ? "checked" : "";*/
            $ind_auto_anestesia = $paquete["ind_auto_anestesia"] == 1 ? "checked" : "";
            $ind_auto_honorarios_medicos = $paquete["ind_auto_honorarios_medicos"] == 1 ? "checked" : "";
            $ind_auto_ayudantia = $paquete["ind_auto_ayudantia"] == 1 ? "checked" : "";
            $ind_auto_derechos_sala = $paquete["ind_auto_derechos_sala"] == 1 ? "checked" : "";
			if($paquete["valor_insumos_adicionales"] >0){
				$ind_check_insumos_adic = "checked";
				$ind_text_adic = "display";
			}
			
			//if($ind_auto_insumosAdicionales == "1"){$check_insumosAdicionales="checked"; $display_insAdi= "display;";}else{$display_insAdi = "none;";}
			
            /*$valor_unico = $paquete["valor_unico"];
            $valor_insumos_adicionales = $paquete["valor_insumos_adicionales"];*/
        }
        ?>
        <fieldset class="grupo_formularios" id="seccion_paquetes"> 						
            <legend style="color:#012996; font-weight:bold;"><?= $titulo ?></legend>

            <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:90%;padding-right: 15px;" >

                <tr>
                    <td style="width: 20%;text-align: right;">
                        Nombre
                    </td>
                    <td style="text-align: left;color: #399000;font-weight: bold;">                                       
                        <input style="margin: 0" type="text" id="txt_nom_paquete" name="txt_nom_paquete" value="<?= $nom_paquete ?>" />
                    </td>
                </tr>
                <?php /*<tr>
                    <td style="width: 100%;text-align: center;" colspan="2">
                        <table style="width: 100%;">
                            <tr>
                                <td align="right">
                                	<input type="checkbox" name="checkValorUnico" id="checkValorUnico" <?= $ind_valor_unico ?> class="no-margin" />
                                    &nbsp;Valor &uacute;nico
                                </td>
                                <td colspan="4"><input style="margin: 0" type="text" id="txtValorUnico" name="ff" value="<?= $valor_unico ?>" /></td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="checkAnestesia" id="checkAnestesia" <?= $ind_anestesia ?> />&nbsp;Anestesi&oacute;logo
                                </td>
                                <td>
                                    <input type="checkbox" name="checkAyudantia" id="checkAyudantia" <?= $ind_audantia ?> />&nbsp;Ayudant&iacute;a
                                </td>
                                <td>
                                    <input type="checkbox" name="checkDerechosSala" id="checkDerechosSala" <?= $ind_derechos_sala ?> />&nbsp;Derechos de sala
                                </td>
                                <td>
                                    <input type="checkbox" name="checkInsumos" id="checkInsumos" <?= $ind_insumos ?> />&nbsp;Insumos
                                </td>
                                <td>
                                    <label>Insumos adicionales</label>
                                    <input style="margin: 0" type="text" id="txtValorInsumosAdic" name="txtValorInsumosAdic" value="<?= $valor_insumos_adicionales ?>" />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr> */?>
                <tr>
                    <td colspan="3">
                        <table style="width: 100%">
                            <tr>
                                <td colspan="4">
                                    <h5 style="color: #DD5043;">Autorizaciones</h5>
                                </td>
                            </tr>                                   
                            <tr>
                                <td><input type="checkbox" name="checkAutoHonorariosMed" id="checkAutoHonorariosMed" <?= $ind_auto_honorarios_medicos ?> />&nbsp;Honorario m&eacute;dicos</td>
                                <td><input type="checkbox" name="checkAutoAnestesiologo" id="checkAutoAnestesiologo" <?= $ind_auto_anestesia ?> />&nbsp;Anestesi&oacute;logo</td>
                                <td><input type="checkbox" name="checkAutoAyudantia" id="checkAutoAyudantia" <?= $ind_auto_ayudantia ?> />&nbsp;Ayudant&iacute;a</td>
                                <td><input type="checkbox" name="checkAutoDerechosSala" id="checkAutoDerechosSala" <?= $ind_auto_derechos_sala ?> />&nbsp;Derechos de sala</td>
                            </tr>
                            <tr>
                            	<td><input onchange="mostrarTxtInsumosAdicionales(this);" type="checkbox" name="checkAutoInsusmoAdicionales" id="checkAutoInsusmoAdicionales" <?=$ind_check_insumos_adic ?>  />&nbsp;Insumos Adicionales</td
                            >
                            	<td>
                                  <input type="text" style="display:<?= $ind_text_adic ?>" name="valorInsumosAdicionales" id="valorInsumosAdicionales" value="<?= $paquete["valor_insumos_adicionales"]; ?>" onkeypress="solo_numeros(event, false);" />
                              	</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <?php
                if ($cant_procs > 0) {
                    $estilo_estado = "";
					
                    switch ($ind_estado) {
                        case 1:
                            $estilo_estado = "color:#399000;";
                            break;
                        case 0:
                            $estilo_estado = "color:#DD5043;";
                            break;
                    }
                    ?>
                    <tr>
                        <td align="right" style="width:20%;">Estado</td>
                        <td align="left" style="color:#399000;">
                        	<span style="<?= $estilo_estado ?>"><b><?= $texto_estado ?></b></span>
                        </td>
                          <td align="right" style="width:20%;">Precio paquete:</td>
                        <td align="left" style="color:#399000;">
                        	<span style="<?= $estilo_estado ?>"><b><?= $valor <> "" ? "$".number_format($valor): "$"."0" ?></b></span>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <br>
            <?php
            if ($cant_procs <= 0) {
                ?>
                <div style="background: #FDEBB7;padding: 3px 5px;width: 70%;margin: 0 auto;">
                    <span>Agregue los procedimientos e insumos que conformar&aacute;n el paquete</span>
                </div>
                <br>  
                <?php
            }
            ?>
            <input type="hidden" name="hdd_cant_productos" id="hdd_cant_productos" value="<?= $cant_procs ?>" />
            <input type="hidden" id="hdd_id_paquete_procedimientos" name="hdd_id_paquete_procedimientos" value="<?= $id_paquete ?>" />
            <div id="rta_formulacion_medicamentos"></div>
          <div>
                <div>
                    <table class="modal_table" style="width:99%; margin:auto;">
                        <thead>
                            <tr>
                                <th style="width:10%;">Tipo</th>
                                <th style="width:10%;">C&oacute;digo</th>
                                <th style="width:50%;" colspan="2">Nombre</th>
                                <th style="width:12%;">Tipo de valor</th>
                                <th style="width:18%;">Valor</th>
                            </tr>
                        </thead>
                    	<?php
							$id_paquete_p = "";
							for ($i = 0; $i < 20; $i++) {
								if ($i < $cant_procs) {
									$proc_aux = $lista_procedimientos[$i];
									$tipo_producto = $proc_aux["tipo_producto"];
									$id_paquete_p = $proc_aux["id_paquete_p"];
									
									switch ($tipo_producto) {
										case "P":
											$cod_producto = $proc_aux["cod_procedimiento"];
											$nombre_producto = $proc_aux["nombre_procedimiento"];
											$valor = $proc_aux["valor"];
											$ind_bilateralidad = $proc_aux["ind_bilateralidad"];
											
											break;
										case "I":
											$cod_producto = $proc_aux["cod_insumo"];
											$nombre_producto = $proc_aux["nombre_insumo"];
											$valor = $proc_aux["valor"];
											$ind_bilateralidad = $proc_aux["ind_bilateralidad"];
											break;
										default:
											$nombre_producto = "";
									}
									$display = "table-row";
								} else {
									$proc_aux = array();
									$tipo_producto = "";
									$cod_producto = "";
									$nombre_producto = "";
									$valor = 0;
									$display = "none";
									$ind_bilateralidad = "";
								}
						?>
                      <tr id="tr_productos_<?= $i ?>" style="display:<?= $display ?>">
                        	<td>
                            	<select id="cmb_tipo_<?= $i ?>" name="cmb_tipo_<?= $i ?>" class="no-margin" style="width:100%;" onchange="cambiar_tipo_producto(<?= $i ?>);">
                                	<option value="">--Seleccione--</option>
                                    <option value="P" <?php if ($tipo_producto == "P") { ?>selected<?php } ?>>Procedimiento</option>
                                    <option value="I" <?php if ($tipo_producto == "I") { ?>selected<?php } ?>>Insumo</option>
                                </select>
                            </td>
                 
                            <td>
                            	<input type="hidden" id="hdd_cod_servicio_<?= $i ?>" name="hdd_cod_servicio_<?= $i ?>" value="<?= $cod_producto ?>" />
                            	<input type="text" id="txt_cod_servicio_<?= $i ?>" name="txt_cod_servicio_<?= $i ?>" value="<?= $cod_producto ?>" class="no-margin" onchange="buscar_producto_codigo(this.value, <?= $i ?>)" />
                            </td>
                            
                          
                            <td align="left" style="width:46%;"><span id="sp_nombre_servicio_<?= $i ?>"><?= $nombre_producto ?></td>
                            <td style="width:4%;">
                            	<?php
                                	//if ($funcion == 1) {
								?>
                            	<div class="d_buscar" onclick="mostrar_buscar_procedimientos('<?= $i ?>');"></div>
                                <?php
									//}
								?>
                            </td>
                              <td>
                        		<select id="cmb_tipo_valor_<?= $i ?>" name="cmb_tipo_valor_<?= $i ?>" class="no-margin" style="width:100%;" onchange="">
                                    <option value="0" <?php if ($ind_bilateralidad == "0") { ?>selected<?php } ?>>No aplica</option>
                                    <option value="1" <?php if ($ind_bilateralidad == "1") { ?>selected<?php } ?>>Unilateral</option>
                                    <option value="2" <?php if ($ind_bilateralidad == "2") { ?>selected<?php } ?>>Bilateral</option>
                                </select>
                            </td>
                            <td>
                             
                              	<input type="hidden" id="hdd_valor_servicio_<?= $i ?>" name="hdd_valor_servicio_<?= $i ?>" value="<?= $valor?>"  />
                            	<input type="text" id="txt_valor_servicio_<?= $i ?>" name="txt_valor_servicio_<?= $i ?>" value="<?= $valor?>" class="no-margin" onchange="" onkeypress="solo_numeros(event, false);"/>
                            </td>
                        </tr>
                        <?php
							}
							//if ($funcion == 1) {
						?>
                        
                        <tr>
                            <td>
                                <div class="restar_alemetos" onclick="ocultar_agregar_producto();" title="Eliminar producto"></div>
                                <div class="agregar_alemetos" onclick="mostrar_agregar_producto();" title="Agregar producto"></div>
                            </td>
                        </tr>
                        <?php
							//}
						?>
                        <tr>
                            <td>
                                <div id="resultadoRemision"></div>
                            </td>
                        </tr>
                    </table>                           
                </div>
                <table id="tblImprimirOrden" style="width:100%;margin-top: 15px;<?= $displayBtnImprimir ?>">
                    <tr>
                        <td colspan="" style="">
                            <?php
								if ($cant_procs <= 0) {
							?>
                            <input type="button" id="" nombre="" class="btnPrincipal" style="font-size: 14px;" value="Crear paquete" onclick="crear_paquete();" />
                            <?php
								} else if ($ind_estado == 1) {
							?>
                          <input type="button" id="" nombre="" class="btnPrincipal" style="font-size: 14px;" value="Inhabilitar paquete" onclick="inhabilitar_paquete();" />
                          <input type="button" id="" nombre="" class="btnPrincipal" style="font-size: 14px;" value="Guardar paquete" onclick="crear_paquete();" />
                            <?php
								}
							?>
                            <div id="d_acciones_paquete" style="display:none;"></div>
                        </td>
                    </tr>
                </table>
            </div>    
        </fieldset>
        <?php
        break;
		
    case "2": //Buscar procedimientos
        @$rastro = $utilidades->str_decode($_POST["rastro"]);
        ?>
        <div class="encabezado">
            <h3>Procedimientos</h3>
        </div>
        <input type="hidden" id="hdd_rastro" name="hdd_rastro" value="<?= $rastro ?>" />
        <br>
        <table style="width: 100%;">
            <tbody>               
                <tr>
                    <td>
                        <input type="text" id="txtParametroProcedimineto" name="txtParametroProcedimineto" placeholder="C&oacute;digo o nombre del procedimiento o insumo" />
                    </td>
                    <td style="width: 10%;">
                        <input type="submit" onclick="buscar_procedimientos();" value="Buscar" class="btnPrincipal">
                    </td>
                </tr>
            </tbody>
        </table>
        <div id="resultadoTblProcedimientos">
        </div>
        <?php
        break;

    case "3": //Resultado - Buscar procedimientos
        @$parametro = $utilidades->str_decode($_POST["parametro"]);
		
        $lista_procedimientos = $dbMaestroProcedimientos->getProcedimientos($parametro, 1);
		$lista_insumos = $dbMaestroInsumos->getInsumos($parametro);
		
        $cantidad_servicios = count($lista_procedimientos) + count($lista_insumos);
        if ($cantidad_servicios > 0) {
            ?>
            <p><span style="font-weight: bold;"><?= $cantidad_servicios ?></span> productos encontrados</p>
            <table class="paginated modal_table">
                <thead>
                    <tr>                   
                        <th style="">C&oacute;digo</th>
                        <th style="">Descripci&oacute;n</th>
                    </tr>
                </thead>
                <?php
					foreach ($lista_procedimientos as $procedimiento_aux) {
				?>
                <tr onclick="comfirmar_agregar_procedimiento('<?= $procedimiento_aux["cod_procedimiento"] ?>', 'P')">
                    <td align="center"><?= $procedimiento_aux["cod_procedimiento"] ?></td>
                    <td align="left">
                        <?= $procedimiento_aux["nombre_procedimiento"] ?>
                        <input type="hidden" id="hdd_nomProc_<?= $procedimiento_aux["cod_procedimiento"] ?>" name="hdd_nomProc_<?= $procedimiento_aux["cod_procedimiento"] ?>" value="<?= $procedimiento_aux["nombre_procedimiento"] ?>" />
                    </td>
                </tr>
                <?php
					}
					
					foreach ($lista_insumos as $insumo_aux) {
				?>
                <tr onclick="comfirmar_agregar_procedimiento('<?= $insumo_aux["cod_insumo"] ?>', 'I')">
                    <td align="center"><?= $insumo_aux["cod_insumo"] ?></td>
                    <td align="left">
                        <?= $insumo_aux["nombre_insumo"] ?>
                        <input type="hidden" id="hdd_nomProc_<?= $insumo_aux["cod_insumo"] ?>" name="hdd_nomProc_<?= $insumo_aux["cod_insumo"] ?>" value="<?= $insumo_aux["nombre_insumo"] ?>" />
                    </td>
                </tr>
                <?php
					}
				?>
            </table>
            <script id="ajax">
                //<![CDATA[ 
                $(function () {
                    $("#tblMedicamentosBuscados", "table").each(function (i) {
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
        } else {
            ?>
            <p>No hay resultados para la b&uacute;squeda</p>
            <?php
        }
        break;
		
    case "4": //Crear un paquete
        $nom_paquete = $utilidades->str_decode($_POST["nom_paquete"]);
		$id_paquete = $utilidades->str_decode($_POST["id_paquete"]);
        $ind_auto_honorarios_medicos = $utilidades->str_decode($_POST["ind_auto_honorarios_medicos"]);
        $ind_auto_anestesia = $utilidades->str_decode($_POST["ind_auto_anestesia"]);
        $ind_auto_ayudantia = $utilidades->str_decode($_POST["ind_auto_ayudantia"]); 
        $ind_auto_derechos_sala = $utilidades->str_decode($_POST["ind_auto_derechos_sala"]);
		$ind_auto_insumos_adicionales = $utilidades->str_decode($_POST["ind_auto_insumos_adicionales"]);
		$valor_insumos_adicionales = $utilidades->str_decode($_POST["valor_insumos"]);
        $cantidad_det = intval($_POST["cantidad_det"], 10);
		$arr_detalle = array();
		
		for ($i = 0; $i < $cantidad_det; $i++) {
			echo("entra");
			$arr_detalle[$i]["tipo_producto"] = $utilidades->str_decode($_POST["tipo_producto_".$i]);
			$arr_detalle[$i]["cod_producto"] = $utilidades->str_decode($_POST["cod_producto_".$i]);
			$arr_detalle[$i]["tipo_valor"] = $utilidades->str_decode($_POST["tipo_valor".$i]);
			$arr_detalle[$i]["valor"] = $utilidades->str_decode($_POST["valor".$i]);
			$arr_detalle[$i]["id_paquete"] = $utilidades->str_decode($_POST["id_paquete".$i]);
			$precio_paquete += $arr_detalle[$i]["valor"];
			
		}	
		
		$resultado = $dbPaquetesProcedimientos->crear_editar_paquete($id_paquete,$nom_paquete, $ind_auto_honorarios_medicos, 
																	 $ind_auto_anestesia, $ind_auto_ayudantia, $ind_auto_derechos_sala, 
																	 $ind_auto_insumos_adicionales,$valor_insumos_adicionales,$arr_detalle, $id_usuario, $precio_paquete);
		   
    ?>
    <input type="hidden" id="hdd_resultado_crear" name="hdd_resultado_crear" value="<?= $resultado ?>" />
    <?php
        break;

    case "5": //Resultado de búsqueda de paquetes
        $proceso = $utilidades->str_decode($_POST["proceso"]);
        $rta = "";
        if ($proceso == 1) {//Todos
            $rta = $dbPaquetesProcedimientos->getPaquetes();
        } else if ($proceso == 2) {
            $parametro = $utilidades->str_decode($_POST["parametro"]);
            $rta = $dbPaquetesProcedimientos->buscarPaquetes($parametro);
        }
       	 	$cantidad = count($rta);
        ?>
        <table class="paginated modal_table" style="width: 99%; margin: auto;">
            <thead>
                <tr><th colspan="5">Listado de paquetes de procedimientos - <?php echo count($cantidad)." registros"; ?></th></tr>
                <tr>
                    <th style="width:5%;">Codigo</th>
                    <th style="width:36%;">Nombre</th>
                    <th style="width:6%;">Estado</th>
                    <th style="width:5%;">Procedimientos</th>
                </tr>
            </thead>
            <?php
            if ($cantidad >= 1) {
                foreach ($rta as $value) {
                    $estado = $value["ind_estado"];
                    if ($estado == 1) {
                        $estado = "Activo";
                        $class_estado = "activo";
                    } else if ($estado == 0) {
                        $estado = "Inactivo";
                        $class_estado = "inactivo";
                    }
                    ?>
                    <tr onclick="obtener_paquete('<?php echo $value["id_paquete_p"]; ?>');">
                        <td><?php echo $value["id_paquete_p"]; ?></td>
                        <td style="text-align: left;"><?php echo $value["nom_paquete_p"]; ?></td>
                        <td><span class="<?php echo $class_estado; ?>"><?= $estado ?></span></td>
                        <td style=""><?php echo $value["cantProcedimientos"]; ?></td>
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

    case "6": //Inhabilitar un paquete
	
        @$id_paquete = $utilidades->str_decode($_POST["idPaquete"]);
        @$observacion = $utilidades->str_decode($_POST["observacion"]);
		
		
        $rta = $dbPaquetesProcedimientos->eliminar_paquete($id_paquete, $observacion, $id_usuario);
    ?>
    <input type="hidden" id="hdd_resultado_inhabilitar" name="hdd_resultado_inhabilitar" value="<?= $rta ?>" />
    <?php
        break;
		
	case "7": //Búsqueda de un producto por su código
		@$tipo_producto = $utilidades->str_decode($_POST["tipo_producto"]);
		@$cod_producto = $utilidades->str_decode($_POST["cod_producto"]);
		
		$cod_producto_b = "";
		$nombre_producto_b = "Producto no hallado";
		switch ($tipo_producto) {
			case "P": //procedimiento
				$procedimiento_obj = $dbMaestroProcedimientos->getProcedimiento($cod_producto);
				if (isset($procedimiento_obj["cod_procedimiento"])) {
					$cod_producto_b = $cod_producto;
					$nombre_producto_b = $procedimiento_obj["nombre_procedimiento"];
				}
				break;
				
			case "I": //insumo
				$insumo_obj = $dbMaestroInsumos->getInsumo($cod_producto);
				if (isset($insumo_obj["cod_insumo"])) {
					$cod_producto_b = $cod_producto;
					$nombre_producto_b = $insumo_obj["nombre_insumo"];
				}
				break;
		}
	?>
    <input type="hidden" id="hdd_cod_producto_b" name="hdd_cod_producto_b" value="<?= $cod_producto_b ?>" />
    <input type="hidden" id="hdd_nombre_producto_b" name="hdd_nombre_producto_b" value="<?= $nombre_producto_b ?>" />
    <?php
		break;
		
	 
	break;
}
?>
