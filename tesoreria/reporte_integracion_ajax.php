<?php
	header("Content-Type: text/xml; charset=UTF-8");
	session_start();
	
	require_once("../db/DbPagos.php");
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/FuncionesPersona.php");
	
	$dbPagos = new DbPagos();
	
	$utilidades = new Utilidades();
	$funcionesPersona = new FuncionesPersona();
	
	$opcion = $_POST["opcion"];
	
	switch ($opcion) {
		case "1": //Reporte de integración
			$usuario = $_SESSION["idUsuario"];
			@$fecha_ini = $utilidades->str_decode($_POST["fecha_ini"]);
			@$fecha_fin = $utilidades->str_decode($_POST["fecha_fin"]);
			@$id_prestador = $utilidades->str_decode($_POST["id_prestador"]);
			
			//Se obtienen los rangos de numeración de pedidos y facturas para las fechas dadas
			$lista_pagos_pedidos = $dbPagos->get_lista_pagos_pedidos_compania($id_prestador, $fecha_ini, $fecha_fin);
			$valor_aux = $dbPagos->get_ultimo_pedido_ant_compania($id_prestador, $fecha_ini);
			$num_pedido_ant = $valor_aux["num_pedido"];
			$valor_aux = $dbPagos->get_primer_pedido_post_compania($id_prestador, $fecha_fin);
			$num_pedido_post = $valor_aux["num_pedido"];
			$lista_pagos_facturas = $dbPagos->get_lista_pagos_facturas_compania($id_prestador, $fecha_ini, $fecha_fin);
			$valor_aux = $dbPagos->get_ultima_factura_ant_compania($id_prestador, $fecha_ini);
			$num_factura_ant = $valor_aux["num_factura"];
			$valor_aux = $dbPagos->get_primera_factura_post_compania($id_prestador, $fecha_fin);
			$num_factura_post = $valor_aux["num_factura"];
			
			//Se obtiene el listado de números de pedido faltantes
			$arr_pedidos_faltantes = array();
			if (count($lista_pagos_pedidos) > 0) {
				$num_pedido_ini = $lista_pagos_pedidos[0]["num_pedido"];
				$num_pedido_fin = $lista_pagos_pedidos[count($lista_pagos_pedidos) - 1]["num_pedido"];
				//Pedidos anteriores al inicial y faltantes del día anterior
				if ($num_pedido_ant != "") {
					for ($i = $num_pedido_ant + 1; $i < $num_pedido_ini; $i++) {
						array_push($arr_pedidos_faltantes, $i);
					}
				}
				//Pedidos en el rango
				$num_pedido_ant2 = $num_pedido_ini - 1;
				foreach ($lista_pagos_pedidos as $pago_aux) {
					for ($i = $num_pedido_ant2 + 1 ; $i < $pago_aux["num_pedido"]; $i++) {
						array_push($arr_pedidos_faltantes, $i);
					}
					$num_pedido_ant2 = $pago_aux["num_pedido"];
				}
				//Pedidos posteriores al final y faltantes del día siguiente
				if ($num_pedido_post != "") {
					for ($i = $num_pedido_fin + 1; $i < $num_pedido_post; $i++) {
						array_push($arr_pedidos_faltantes, $i);
					}
				}
			} else if ($num_pedido_ant != "" && $num_pedido_post != ""){
				for ($i = $num_pedido_ant + 1 ; $i < $num_pedido_post; $i++) {
					array_push($arr_pedidos_faltantes, $i);
				}
			}
			
			//Se obtiene el listado de números de factura faltantes
			$arr_facturas_faltantes = array();
			if (count($lista_pagos_facturas) > 0) {
				$num_factura_ini = $lista_pagos_facturas[0]["num_factura"];
				$num_factura_fin = $lista_pagos_facturas[count($lista_pagos_facturas) - 1]["num_factura"];
				//Facturas anteriores al inicial y faltantes del día anterior
				if ($num_factura_ant != "") {
					for ($i = $num_factura_ant + 1; $i < $num_factura_ini; $i++) {
						array_push($arr_facturas_faltantes, $i);
					}
				}
				//Facturas en el rango
				$num_factura_ant2 = $num_factura_ini - 1;
				foreach ($lista_pagos_facturas as $pago_aux) {
					for ($i = $num_factura_ant2 + 1 ; $i < $pago_aux["num_factura"]; $i++) {
						array_push($arr_facturas_faltantes, $i);
					}
					$num_factura_ant2 = $pago_aux["num_factura"];
				}
				//Facturas posteriores al final y faltantes del día siguiente
				if ($num_factura_post != "") {
					for ($i = $num_factura_fin + 1; $i < $num_factura_post; $i++) {
						array_push($arr_facturas_faltantes, $i);
					}
				}
			} else if ($num_factura_ant != "" && $num_factura_post != ""){
				for ($i = $num_factura_ant + 1 ; $i < $num_factura_post; $i++) {
					array_push($arr_facturas_faltantes, $i);
				}
			}
			
			//Pagos con pedidos y facturas erroneos
			$lista_pagos_pedidos_error = $dbPagos->get_lista_pagos_pedido_error_compania($id_prestador, $fecha_ini, $fecha_fin);
			$lista_pagos_facturas_error = $dbPagos->get_lista_pagos_facturas_error_compania($id_prestador, $fecha_ini, $fecha_fin);
		?>
       	<div class="tabs-container">
    		<dl class="tabs" data-tab>
            	<dd id="panel_opt_1" class="active"><a href="#panel2-1">Pedidos&nbsp;(<?php echo(count($lista_pagos_pedidos)); ?>)</a></dd>
            	<dd id="panel_opt_2"><a href="#panel2-2">Pedidos faltantes&nbsp;(<?php echo(count($arr_pedidos_faltantes)); ?>)</a></dd>
            	<dd id="panel_opt_3"><a href="#panel2-3">Pedidos en -1&nbsp;(<?php echo(count($lista_pagos_pedidos_error)); ?>)</a></dd>
            	<dd id="panel_opt_4"><a href="#panel2-4">Facturas&nbsp;(<?php echo(count($lista_pagos_facturas)); ?>)</a></dd>
            	<dd id="panel_opt_5"><a href="#panel2-5">Facturas Faltantes&nbsp;(<?php echo(count($arr_facturas_faltantes)); ?>)</a></dd>
            	<dd id="panel_opt_6"><a href="#panel2-6">Facturas en -1&nbsp;(<?php echo(count($lista_pagos_facturas_error)); ?>)</a></dd>
			</dl>
            <div class="tabs-content">
            	<!--Listado de pedidos-->
            	<div class="content active" id="panel2-1">
                    <table id="tbl_pedidos" class="paginated modal_table" style="width: 98%; margin: auto;">
                        <thead>
                            <tr>
                                <th align="center" style="width:10%;">N&uacute;m. pedido</th>
                                <th align="center" style="width:10%;">N&uacute;m. pago</th>
                                <th align="center" style="width:12%;">N&uacute;m. documento</th>
                                <th align="center" style="width:24%;">Paciente</th>
                                <th align="center" style="width:24%;">Usuario</th>
                                <th align="center" style="width:10%;">Fecha</th>
                                <th align="center" style="width:10%;">Hora</th>
                            </tr>
                        </thead>
                        <?php
							if (count($lista_pagos_pedidos) > 0) {
	                        	foreach ($lista_pagos_pedidos as $pago_aux) {
						?>
                        <tr>
                        	<td align="center"><?php echo($pago_aux["num_pedido"]); ?></td>
                        	<td align="center"><?php echo($pago_aux["id_pago"]); ?></td>
                        	<td align="center">
								<?php echo($pago_aux["cod_tipo_documento"]." ".$pago_aux["numero_documento"]); ?>
                            </td>
                        	<td align="left">
								<?php echo($funcionesPersona->obtenerNombreCompleto($pago_aux["nombre_1"], $pago_aux["nombre_2"], $pago_aux["apellido_1"], $pago_aux["apellido_2"])); ?>
                            </td>
                        	<td align="left">
								<?php echo($pago_aux["nombre_usuario"]." ".$pago_aux["apellido_usuario"]); ?>
                            </td>
                        	<td align="center"><?php echo($pago_aux["fecha_pago_t"]); ?></td>
                        	<td align="center"><?php echo($pago_aux["hora_pago_t"]); ?></td>
                        </tr>
                        <?php
								}
							} else {
						?>
                        <td align="center" colspan="7">
                        	No se encontraron pedidos para el rango de fechas dado.
                        </td>
                        <?php
							}
						?>
                    </table>
                    <script id="ajax">
						//<![CDATA[ 
						$(function() {
							$(".paginated", "#tbl_pedidos").each(function(i) {
								$(this).text(i + 1);
							});
			
							$("#tbl_pedidos.paginated").each(function() {
								var currentPage = 0;
								var numPerPage = 20;
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
                </div>
                
                <!--Números de pedido faltantes-->
            	<div class="content" id="panel2-2">
                	<?php
						if (count($arr_pedidos_faltantes) > 0) {
					?>
                    <table class="modal_table" style="width: 98%; margin: auto;">
                    	<tr>
                            <?php
								foreach ($arr_pedidos_faltantes as $i => $num_pedido_aux) {
									if ($i % 10 == 0 && $i > 0) {
							?>
                        </tr>
                        <tr>
                            <?php
									}
							?>
                            <td align="center" style="width:10%;"><?php echo($num_pedido_aux); ?></td>
                            <?php
								}
							?>
                    	</tr>
                    </table>
                    <?php
						} else {
					?>
                    <div class="msj-vacio">
                        <p>No se encontraron n&uacute;meros de pedido faltantes.</p>
                    </div>
                    <?php
						}
					?>
                </div>
                
                <!--Pedidos en -1-->
            	<div class="content" id="panel2-3">
                    <table id="tbl_pedidos_error" class="paginated modal_table" style="width: 98%; margin: auto;">
                        <thead>
                            <tr>
                                <th align="center" style="width:10%;">N&uacute;m. pago</th>
                                <th align="center" style="width:12%;">N&uacute;m. documento</th>
                                <th align="center" style="width:30%;">Paciente</th>
                                <th align="center" style="width:28%;">Usuario</th>
                                <th align="center" style="width:10%;">Fecha</th>
                                <th align="center" style="width:10%;">Hora</th>
                            </tr>
                        </thead>
                        <?php
							if (count($lista_pagos_pedidos_error) > 0) {
	                        	foreach ($lista_pagos_pedidos_error as $pago_aux) {
						?>
                        <tr>
                        	<td align="center"><?php echo($pago_aux["id_pago"]); ?></td>
                        	<td align="center">
								<?php echo($pago_aux["cod_tipo_documento"]." ".$pago_aux["numero_documento"]); ?>
                            </td>
                        	<td align="left">
								<?php echo($funcionesPersona->obtenerNombreCompleto($pago_aux["nombre_1"], $pago_aux["nombre_2"], $pago_aux["apellido_1"], $pago_aux["apellido_2"])); ?>
                            </td>
                        	<td align="left">
								<?php echo($pago_aux["nombre_usuario"]." ".$pago_aux["apellido_usuario"]); ?>
                            </td>
                        	<td align="center"><?php echo($pago_aux["fecha_pago_t"]); ?></td>
                        	<td align="center"><?php echo($pago_aux["hora_pago_t"]); ?></td>
                        </tr>
                        <?php
								}
							} else {
						?>
                        <td align="center" colspan="6">
                        	No se encontraron pagos con pedidos erroneos para el rango de fechas dado.
                        </td>
                        <?php
							}
						?>
                    </table>
                    <script id="ajax">
						//<![CDATA[ 
						$(function() {
							$(".paginated", "#tbl_pedidos_error").each(function(i) {
								$(this).text(i + 1);
							});
			
							$("#tbl_pedidos_error.paginated").each(function() {
								var currentPage = 0;
								var numPerPage = 20;
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
                </div>
                
                <!--Listado de facturas-->
            	<div class="content" id="panel2-4">
                    <table id="tbl_facturas" class="paginated modal_table" style="width: 98%; margin: auto;">
                        <thead>
                            <tr>
                                <th align="center" style="width:10%;">N&uacute;m. factura</th>
                                <th align="center" style="width:10%;">N&uacute;m. pago</th>
                                <th align="center" style="width:12%;">N&uacute;m. documento</th>
                                <th align="center" style="width:24%;">Paciente</th>
                                <th align="center" style="width:24%;">Usuario</th>
                                <th align="center" style="width:10%;">Fecha</th>
                                <th align="center" style="width:10%;">Hora</th>
                            </tr>
                        </thead>
                        <?php
							if (count($lista_pagos_facturas) > 0) {
                        		foreach ($lista_pagos_facturas as $pago_aux) {
						?>
                        <tr>
                        	<td align="center"><?php echo($pago_aux["num_factura"]); ?></td>
                        	<td align="center"><?php echo($pago_aux["id_pago"]); ?></td>
                        	<td align="center">
								<?php echo($pago_aux["cod_tipo_documento"]." ".$pago_aux["numero_documento"]); ?>
                            </td>
                        	<td align="left">
								<?php echo($funcionesPersona->obtenerNombreCompleto($pago_aux["nombre_1"], $pago_aux["nombre_2"], $pago_aux["apellido_1"], $pago_aux["apellido_2"])); ?>
                            </td>
                        	<td align="left">
								<?php echo($pago_aux["nombre_usuario"]." ".$pago_aux["apellido_usuario"]); ?>
                            </td>
                        	<td align="center"><?php echo($pago_aux["fecha_pago_t"]); ?></td>
                        	<td align="center"><?php echo($pago_aux["hora_pago_t"]); ?></td>
                        </tr>
                        <?php
								}
							} else {
						?>
                        <td align="center" colspan="7">
                        	No se encontraron facturas para el rango de fechas dado.
                        </td>
                        <?php
							}
						?>
                    </table>
                    <script id="ajax">
						//<![CDATA[ 
						$(function() {
							$(".paginated", "#tbl_facturas").each(function(i) {
								$(this).text(i + 1);
							});
			
							$("#tbl_facturas.paginated").each(function() {
								var currentPage = 0;
								var numPerPage = 20;
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
                </div>
                
                <!--Números de factura faltantes-->
            	<div class="content" id="panel2-5">
                	<?php
						if (count($arr_facturas_faltantes) > 0) {
					?>
                    <table class="modal_table" style="width: 98%; margin: auto;">
                    	<tr>
                            <?php
								foreach ($arr_facturas_faltantes as $i => $num_factura_aux) {
									if ($i % 10 == 0 && $i > 0) {
							?>
                        </tr>
                        <tr>
                            <?php
									}
							?>
                            <td align="center" style="width:10%;"><?php echo($num_factura_aux); ?></td>
                            <?php
								}
							?>
                    	</tr>
                    </table>
                    <?php
						} else {
					?>
                    <div class="msj-vacio">
                        <p>No se encontraron n&uacute;meros de factura faltantes.</p>
                    </div>
                    <?php
						}
					?>
                </div>
                
                <!--Facturas en -1-->
            	<div class="content" id="panel2-6">
                    <table id="tbl_facturas_error" class="paginated modal_table" style="width: 98%; margin: auto;">
                        <thead>
                            <tr>
                                <th align="center" style="width:10%;">N&uacute;m. pago</th>
                                <th align="center" style="width:12%;">N&uacute;m. documento</th>
                                <th align="center" style="width:30%;">Paciente</th>
                                <th align="center" style="width:28%;">Usuario</th>
                                <th align="center" style="width:10%;">Fecha</th>
                                <th align="center" style="width:10%;">Hora</th>
                            </tr>
                        </thead>
                        <?php
							if (count($lista_pagos_facturas_error) > 0) {
                        		foreach ($lista_pagos_facturas_error as $pago_aux) {
						?>
                        <tr>
                        	<td align="center"><?php echo($pago_aux["id_pago"]); ?></td>
                        	<td align="center">
								<?php echo($pago_aux["cod_tipo_documento"]." ".$pago_aux["numero_documento"]); ?>
                            </td>
                        	<td align="left">
								<?php echo($funcionesPersona->obtenerNombreCompleto($pago_aux["nombre_1"], $pago_aux["nombre_2"], $pago_aux["apellido_1"], $pago_aux["apellido_2"])); ?>
                            </td>
                        	<td align="left">
								<?php echo($pago_aux["nombre_usuario"]." ".$pago_aux["apellido_usuario"]); ?>
                            </td>
                        	<td align="center"><?php echo($pago_aux["fecha_pago_t"]); ?></td>
                        	<td align="center"><?php echo($pago_aux["hora_pago_t"]); ?></td>
                        </tr>
                        <?php
								}
							} else {
						?>
                        <td align="center" colspan="6">
                        	No se encontraron pagos con facturas erroneas para el rango de fechas dado.
                        </td>
                        <?php
							}
						?>
                    </table>
                    <script id="ajax">
						//<![CDATA[ 
						$(function() {
							$(".paginated", "#tbl_facturas_error").each(function(i) {
								$(this).text(i + 1);
							});
			
							$("#tbl_facturas_error.paginated").each(function() {
								var currentPage = 0;
								var numPerPage = 20;
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
                </div>
            </div>
        </div>
		<script id="ajax" type="text/javascript">
            $(document).foundation();
        </script>
        <?php
			break;
	}
?>
