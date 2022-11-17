<?php
header("Content-Type: text/xml; charset=UTF-8");
session_start();

require_once("../funciones/Utilidades.php");
require_once("../funciones/FuncionesPersona.php");

$utilidades = new Utilidades();

$opcion = $_POST["opcion"];

switch ($opcion) {
    case "1": //Genera reporte general de tesorería
        require_once("../funciones/pdf/fpdf.php");
        require_once("../funciones/pdf/makefont/makefont.php");
        require_once("../funciones/pdf/funciones.php");
        require_once("../db/DbPagos.php");
        require_once("../db/DbTiposPago.php");
		require_once("../db/DbConvenios.php");
		require_once("../db/DbUsuarios.php");
		require_once("../db/DbAnticipos.php");
		
		$funcionesPersona = new FuncionesPersona();
		
        $pdf = new FPDF("P", "mm", "Letter");
		$pdf->SetMargins(5, 5, 5);
        $dbPagos = new DbPagos();
        $dbTiposPago = new DbTiposPago();
		$dbConvenios = new DbConvenios();
		$dbUsuarios = new DbUsuarios();
		$dbAnticipos = new DbAnticipos();
		
		$usuario = $_SESSION["idUsuario"];
		@$fecha = $utilidades->str_decode($_POST["fecha"]);
		@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
		
        $rta_tipos_pagos_aux = $dbTiposPago->getListaTiposPagoNP();
        $rta_procedimientos_aux = $dbPagos->reporteTesoseriaProcedimientos($fecha, $fecha, $id_convenio, "", "", $usuario, "");
        $rta_pacientes_aux = $dbPagos->reporteTesoseriaProcedimientosPacientesPlanes($fecha, $fecha, $id_convenio, "", "", "", "", $usuario, "");
		
        $pdf->header = 2; //Selecciona el header
		$pdf->h_row2 = 3;
        $pdf->bordeMulticell = 2; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde
        $pdf->pie_pagina = true;
        
        $pdf->AddPage();
		
        $pdf->SetFont("Arial", "", 7);
        //srand(microtime() * 1000000);
		
		//Se agregan los filtros seleccionados al reporte
		if ($fecha != "") {
			$arr_aux = explode("-", $fecha);
			$pdf->SetAligns2(array("L"));
			$pdf->SetWidths2(array(100));
			$pdf->Row2(array("Fecha: ".$arr_aux[2]."/".$arr_aux[1]."/".$arr_aux[0]));
		}
		if ($id_convenio != "") {
			$convenio_obj = $dbConvenios->getConvenio($id_convenio);
			$pdf->SetAligns2(array("L"));
			$pdf->SetWidths2(array(100));
			$pdf->Row2(array(ajustarCaracteres("Convenio: ".$convenio_obj["nombre_convenio"])));
		}
		if ($usuario != "") {
			$usuario_obj = $dbUsuarios->getUsuario($usuario);
			$pdf->SetAligns2(array("L"));
			$pdf->SetWidths2(array(100));
			$pdf->Row2(array(ajustarCaracteres("Usuario registro pago: ".$usuario_obj["nombre_usuario"]." ".$usuario_obj["apellido_usuario"])));
		}
		$pdf->Ln(5);
		
		$lista_cambios_precios = array();
		$arr_totales_fp = array();
        for ($a = 0; $a < count($rta_tipos_pagos_aux); $a++) {
            $pasaporte_titulo_tpago = 0;
            $valor_por_pagar = 0;
			
            for ($i = 0; $i < count($rta_procedimientos_aux); $i++) {
                $pasaporte_titulo_cups = 0;
                $pasaporte_totales = false;
                $contador_cantidades = 0;
                $contador_precios = 0;
                $pasaporte_cantidades = 0;
				
                for ($e = 0; $e < count($rta_pacientes_aux); $e++) {
                    if ($rta_pacientes_aux[$e]["cod_insumo"] == $rta_procedimientos_aux[$i]["cod_insumo"] && $rta_pacientes_aux[$e]["tipo_precio"] == $rta_procedimientos_aux[$i]["tipo_precio"]) {
                        if ($pasaporte_titulo_cups == 0) {
                            $pasaporte_titulo_cups = 1;
                        }

                        $n1 = 0;
						if ($rta_tipos_pagos_aux[$a]["id"] == $rta_pacientes_aux[$e]["id_medio_pago"]
								|| ($rta_tipos_pagos_aux[$a]["id"] == "99" && trim($rta_pacientes_aux[$e]["id_medio_pago"]) == "")) {
							//Si el tipo de pago es igual
                            if ($pasaporte_titulo_tpago == 0) {
                                $pasaporte_titulo_tpago = 1;
                            }
							
							//Calcula el valor por pagar para el tipo de pago
							$valor_por_pagar = 0;
							$abrev_pago = "";
							if ($rta_pacientes_aux[$e]["t_pago"] == $rta_tipos_pagos_aux[$a]["id"]) {
								$valor_por_pagar = $rta_pacientes_aux[$e]["valor_pago"];
							} else if ($rta_pacientes_aux[$e]["valor_pago"] > 0) {
								//Se obtiene la abreviatura del tipo de pago
								$tipo_pago_aux = $tiposPago->getTipoPago($rta_pacientes_aux[$e]["id_medio_pago"]);
								$abrev_pago = $tipo_pago_aux["abrev_pago"];
							}
                            
							$valor_total_aux = intval($rta_pacientes_aux[$e]["total_pago"], 10);
							if ($rta_pacientes_aux[$e]["ind_boleta"] == "1") {
								if ($rta_pacientes_aux[$e]["id_medio_pago"] == "0") {
									$valor_por_pagar = (intval($rta_pacientes_aux[$e]["valor"], 10) - intval($rta_pacientes_aux[$e]["valor_cuota"], 10)) * intval($rta_pacientes_aux[$e]["cantidad"], 10);
								} else if (intval($rta_pacientes_aux[$e]["total_cuota"], 10) > 0) {
									$valor_por_pagar = intval($rta_pacientes_aux[$e]["valor_cuota"], 10) * $rta_pacientes_aux[$e]["cantidad"] * $rta_pacientes_aux[$e]["valor_pago"] / $rta_pacientes_aux[$e]["total_cuota"];
								} else {
									$valor_por_pagar = 0;
								}
							} else {
								if ($valor_total_aux > 0) {
									$valor_por_pagar *= (($rta_pacientes_aux[$e]["valor"] * $rta_pacientes_aux[$e]["cantidad"]) / $valor_total_aux);
								}
							}
							
							$valor_base_aux = $rta_pacientes_aux[$e]["valor_b"];
							
							//Se marcan los valores que no correspondan al valor base
							if ($valor_base_aux != $rta_pacientes_aux[$e]["valor"] /*&& intval($valor_base_aux, 10) != 0*/) {
								$lista_cambios_precios[$rta_pacientes_aux[$e]["id_pago"]] = $rta_pacientes_aux[$e];
							}
							
							if ($valor_por_pagar > 0 || $rta_pacientes_aux[$e]["ind_boleta"] == "0") {
								//Encabezado
								if ($pasaporte_titulo_tpago == 1) {//Imprime el titulo del tipo de pago
									$pdf->SetAligns2(array("L"));
									$pdf->SetWidths2(array(100));
									$pdf->SetFont("Arial", "B", 7);
									$pdf->Row2(array("FORMA DE PAGO: ".ajustarCaracteres($rta_tipos_pagos_aux[$a]["nombre"])));
									$pdf->SetFont("Arial", "", 7);
									$pasaporte_titulo_tpago++;
								}
								
								if ($pasaporte_titulo_cups == 1) {//Imprime el titulo del cups y titulos de nombre completo, documento, precio ,etc...
									$pdf->SetAligns2(array("L", "L"));
									$pdf->SetWidths2(array(15, 200));
									$pdf->SetFont("Arial", "B", 7);
									$pdf->Row2(array($rta_procedimientos_aux[$i]["cod_insumo"], strtoupper(ajustarCaracteres($rta_procedimientos_aux[$i]["nombre_insumo"]))));
									
									$pdf->SetAligns2(array("C", "C", "C", "C", "C", "C", "R", "R", "R", "R"));
									$pdf->SetWidths2(array(11, 52, 16, 15, 12, 43, 16, 16, 8, 16));
									$pdf->Row2(array("Recibo", "Nombre completo", "Documento", "Factura", "Entidad", "Convenio", "Valor base", "Valor pago", "Cant", "Valor total"));
									$pdf->SetFont("Arial", "", 7);
									
									$pasaporte_titulo_cups++;
								}
								
								//Variables de identificación del paciente/tercero
								if ($rta_pacientes_aux[$e]["tipo_documento_tercero"] == "") {
									$tipo_documento_aux = $rta_pacientes_aux[$e]["tipo_documento_aux"];
									$numero_documento_aux = $rta_pacientes_aux[$e]["numero_documento"];
									$nombre_aux = ajustarCaracteres(mb_strtoupper($funcionesPersona->obtenerNombreCompleto($rta_pacientes_aux[$e]["apellido_1"], $rta_pacientes_aux[$e]["apellido_2"], $rta_pacientes_aux[$e]["nombre_1"], $rta_pacientes_aux[$e]["nombre_2"]), "UTF-8"));
								} else {
									$tipo_documento_aux = $rta_pacientes_aux[$e]["tipo_documento_tercero"];
									$numero_documento_aux = $rta_pacientes_aux[$e]["numero_documento_tercero"];
									if ($rta_pacientes_aux[$e]["numero_verificacion"] != "") {
										$numero_documento_aux .= "-".$rta_pacientes_aux[$e]["numero_verificacion"];
									}
									$nombre_aux = ajustarCaracteres(mb_strtoupper($rta_pacientes_aux[$e]["nombre_tercero"]));
								}
								
								//Valores
								$pasaporte_cantidades++;
								$pdf->SetAligns2(array("R", "L", "C", "C", "C", "L", "R", "R", "R", "R"));
								$pdf->SetWidths2(array(11, 52, 16, 15, 12, 43, 16, 16, 8, 16));
								$pdf->Row2(array(
									$rta_pacientes_aux[$e]["id_pago"],
									$nombre_aux,
									$numero_documento_aux,
									$utilidades->crear_formato_corto_factura($rta_pacientes_aux[$e]["num_factura"]),
									$rta_pacientes_aux[$e]["nombre_entidad"],
									ajustarCaracteres($rta_pacientes_aux[$e]["nombre_convenio"]." - ".$rta_pacientes_aux[$e]["nombre_plan"]),
									"$".number_format($valor_base_aux),
									"$".number_format($valor_por_pagar / $rta_pacientes_aux[$e]["cantidad"]),
									$rta_pacientes_aux[$e]["cantidad"],
									"$".number_format($valor_por_pagar)));
								
								$contador_cantidades += $rta_pacientes_aux[$e]["cantidad"];
								$contador_precios += $valor_por_pagar;
								$pasaporte_totales = true;
							}
                        }
                    }
                    if ($e >= (count($rta_pacientes_aux) - 1) && $pasaporte_totales) {
                        $pdf->SetAligns2(array("L", "R", "R", "R"));
						$pdf->SetWidths2(array(163, 16, 8, 18));
						$pdf->SetFont("Arial", "B", 7);
                        $pdf->Row2(array("", "Total", $contador_cantidades, "$".number_format($contador_precios)));
						$pdf->SetFont("Arial", "", 7);
                        $pdf->Ln(2);
						
						//Se acumula en el total por tipo de pago
						if (isset($arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]])) {
							$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["cantidad"] += $contador_cantidades;
							$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["valor"] += $contador_precios;
						} else {
							$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["nombre"] = $rta_tipos_pagos_aux[$a]["nombre"];
							$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["ind_negativo"] = $rta_tipos_pagos_aux[$a]["ind_negativo"];
							$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["cantidad"] = $contador_cantidades;
							$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["valor"] = $contador_precios;
						}
                    }
                }
            }
			
			//Total por tipo de pago
			if (isset($arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]])) {
				$pdf->SetAligns2(array("L", "R", "R"));
				$pdf->SetWidths2(array(133, 54, 18));
				$pdf->SetFont("Arial", "B", 7);
				$pdf->Row2(array("", ajustarCaracteres("Total ".$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["nombre"]), "$".number_format($arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["valor"])));
				$pdf->SetFont("Arial", "", 7);
				$pdf->Ln(2);
			}
        }
		
		//Se agregan los subtotales
		$pdf->SetFont("Arial", "", 7);
		$pdf->SetAligns2(array("L", "L", "R"));
		$pdf->SetWidths2(array(66, 65, 25));
		$total_aux = 0;
		foreach ($arr_totales_fp as $total_fp_aux) {
			if ($total_fp_aux["ind_negativo"] == "1") {
				$total_aux -= $total_fp_aux["valor"];
				$pdf->SetTextColor(255, 0, 0);
				$pdf->Row2(array("", "Subtotal ".ajustarCaracteres($total_fp_aux["nombre"]), "($".number_format($total_fp_aux["valor"]).")"));
				$pdf->SetTextColor(0, 0, 0);
			} else {
				$total_aux += $total_fp_aux["valor"];
				$pdf->Row2(array("", "Subtotal ".ajustarCaracteres($total_fp_aux["nombre"]), "$".number_format($total_fp_aux["valor"])));
			}
		}
		$pdf->SetFont("Arial", "B", 7);
		$pdf->Row2(array("", "TOTAL", "$".number_format($total_aux)));
		
		/************************************/
		//Se agrega una nueva página con los anticipos recaudados si no se seleccionaron convenio, plan, usuario de admisión o concepto
		if ($id_convenio == "") {
			$lista_anticipos_det_medios = $dbAnticipos->get_lista_anticipos_det_medios_fechas($fecha, $fecha, "", $usuario, 0);
			if (count($lista_anticipos_det_medios) > 0) {
				$pdf->AddPage();
				$pdf->SetAligns2(array('L'));
				$pdf->SetWidths2(array(100));
				$pdf->SetFont('Arial', 'B', 7);
				$pdf->Row2(array("RECAUDOS ANTICIPOS"));
				
				//Se calculan los subtotales y totales
				$total_anticipos = 0;
				$arr_totales_anticipos = array();
				foreach ($lista_anticipos_det_medios as $det_medio_aux) {
					$total_anticipos += $det_medio_aux["valor_pago"];
					
					if (isset($arr_totales_anticipos[$det_medio_aux["id_medio_pago"]])) {
						$arr_totales_anticipos[$det_medio_aux["id_medio_pago"]]["valor"] += $det_medio_aux["valor_pago"];
					} else {
						//Se busca el valor del medio de pago
						$nombre_aux = "";
						foreach ($rta_tipos_pagos_aux as $tipo_pago_aux) {
							if ($tipo_pago_aux["id"] == $det_medio_aux["id_medio_pago"]) {
								$nombre_aux = $tipo_pago_aux["nombre"];
								break;
							}
						}
						$arr_totales_anticipos[$det_medio_aux["id_medio_pago"]]["nombre"] = $nombre_aux;
						$arr_totales_anticipos[$det_medio_aux["id_medio_pago"]]["valor"] = $det_medio_aux["valor_pago"];
					}
				}
				
				//Se agregan los datos al reporte por medio de pago
				foreach ($rta_tipos_pagos_aux as $tipo_pago_aux) {
					$fecha_ant = "";
					$bol_agregado = false;
					foreach ($lista_anticipos_det_medios as $det_medio_aux) {
						if ($tipo_pago_aux["id"] == $det_medio_aux["id_medio_pago"]) {
							if (!$bol_agregado) {
								//Se agrega el título del medio de pago
								$pdf->SetAligns2(array('L'));
								$pdf->SetWidths2(array(100));
								$pdf->SetFont('Arial', 'B', 7);
								$pdf->Row2(array('FORMA DE PAGO: '.ajustarCaracteres($tipo_pago_aux["nombre"])));
								$pdf->SetAligns2(array('C', 'L', 'C', 'C', 'R', 'L'));
								$pdf->SetWidths2(array(15, 50, 23, 20, 23, 73));
								$pdf->Row2(array('Anticipo', 'Nombre completo', 'Documento', 'No. externo', 'Valor', 'Observaciones'));
								$pdf->SetFont('Arial', '', 7);
								$bol_agregado = true;
							}
							if ($det_medio_aux["fecha_crea_t"] != $fecha_ant && $fecha_inicial != $fecha_final) {
								$pdf->SetAligns2(array('L', 'L'));
								$pdf->SetWidths2(array(15, 100));
								$pdf->SetFont('Arial', 'B', 7);
								$pdf->Row2(array("Fecha", $det_medio_aux["fecha_crea_t"]));
								$pdf->SetFont('Arial', '', 7);
							}
							$pdf->SetAligns2(array('C', 'L', 'C', 'C', 'R', 'L'));
							$pdf->SetWidths2(array(15, 50, 23, 20, 23, 73));
							$pdf->Row2(array(
								$det_medio_aux["id_anticipo"],
								ajustarCaracteres(mb_strtoupper($funcionesPersona->obtenerNombreCompleto($det_medio_aux['apellido_1'], $det_medio_aux['apellido_2'], $det_medio_aux['nombre_1'], $det_medio_aux['nombre_2']), "UTF-8")),
								$det_medio_aux["numero_documento"],
								$det_medio_aux["num_anticipo"],
								'$'.number_format($det_medio_aux["valor_pago"]),
								$det_medio_aux["observaciones_anticipo"]
							));
							
							if ($det_medio_aux["id_tercero"] != "") {
								$pdf->SetAligns2(array('C', 'L', 'C'));
								$pdf->SetWidths2(array(15, 50, 23));
								$pdf->Row2(array(
									"",
									ajustarCaracteres(mb_strtoupper($det_medio_aux["nombre_tercero"], "UTF-8")),
									$det_medio_aux["numero_documento_tercero"]
								));
							}
							$pdf->ln(2);
							
							$fecha_ant = $det_medio_aux["fecha_crea_t"];
						}
					}
					
					//Se agrega el total del medio de pago
					if (isset($arr_totales_anticipos[$tipo_pago_aux["id"]])) {
						$total_tipo_pago_aux = $arr_totales_anticipos[$tipo_pago_aux["id"]];
						$pdf->SetAligns2(array('R', 'R'));
						$pdf->SetWidths2(array(108, 23));
						$pdf->SetFont('Arial', 'B', 7);
						$pdf->Row2(array("Total ".$total_tipo_pago_aux["nombre"], "$".number_format($total_tipo_pago_aux["valor"])));
						$pdf->SetFont('Arial', '', 7);
						$pdf->ln(2);
					}
				}
				
				//Se imprime el total por concepto de anticipos
				$pdf->SetAligns2(array('L', 'L', 'R'));
				$pdf->SetWidths2(array(66, 65, 25));
				$pdf->SetFont('Arial', '', 7);
				foreach ($rta_tipos_pagos_aux as $tipo_pago_aux) {
					if (isset($arr_totales_anticipos[$tipo_pago_aux["id"]])) {
						$total_anticipo_aux = $arr_totales_anticipos[$tipo_pago_aux["id"]];
						$pdf->Row2(array("", "Subtotal ".ajustarCaracteres($total_anticipo_aux["nombre"]), "$".number_format($total_anticipo_aux["valor"])));
					}
				}
				$pdf->SetFont('Arial', 'B', 7);
				$pdf->Row2(array("", "TOTAL RECAUDO ANTICIPOS", "$".number_format($total_anticipos)));
			}
		}
		
		//Se agrega una nueva página con los pagos que presentan diferencias con su valor base
		if (count($lista_cambios_precios) > 0) {
	        $pdf->AddPage();
            $pdf->SetAligns2(array("L"));
			$pdf->SetWidths2(array(100));
			$pdf->SetFont("Arial", "B", 7);
			$pdf->Row2(array("VALORES BASE MODIFICADOS"));
			
			$pdf->SetAligns2(array("C", "C", "C", "C", "R", "R", "C"));
			$pdf->SetWidths2(array(11, 40, 16, 45, 16, 16, 60));
			$pdf->Row2(array("Recibo", "Nombre completo", "Documento", "Convenio", "Valor base", "Valor pago", "Observaciones"));
			$pdf->SetFont("Arial", "", 7);
			
			foreach ($lista_cambios_precios as $precio_aux) {
				$valor_base_aux = "";
				switch ($precio_aux["ind_tipo_pago"]) {
					case "1": //Cuota moderadora
						$valor_base_aux = $precio_aux["valor_cuota_b"];
						break;
					case "2": //Valor total
						$valor_base_aux = $precio_aux["valor_b"];
						break;
				}
				
				$pdf->SetAligns2(array("R", "L", "C", "L", "R", "R", "L"));
				$pdf->SetWidths2(array(11, 40, 16, 45, 16, 16, 60));
				$pdf->Row2(array(
					$precio_aux["id_pago"],
					ajustarCaracteres($funcionesPersona->obtenerNombreCompleto($precio_aux["apellido_1"], $precio_aux["apellido_2"], $precio_aux["nombre_1"], $precio_aux["nombre_2"])),
					$precio_aux["numero_documento"],
					ajustarCaracteres($precio_aux["nombre_convenio"]),
					"$".number_format($valor_base_aux),
					"$".number_format($precio_aux["valor"]),
					$precio_aux["observaciones_pago"]));
			}
		}
		
		$pdf->Ln();

        $pdf->Output();
        //Se guarda el documento pdf
        $nombreArchivo = "../tmp/reporte_tesoreria_usuario_".$_SESSION["idUsuario"].".pdf";
        $pdf->Output($nombreArchivo, "F");
	?>
	<input type="hidden" name="hdd_archivo_pdf" id="hdd_archivo_pdf" value="<?php echo($nombreArchivo); ?>" />
	<?php
        break;
}
?>
