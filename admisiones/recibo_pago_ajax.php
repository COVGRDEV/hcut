<?php
	session_start();
	
	header('Content-Type: text/xml; charset=UTF-8');
	
	require_once("../db/DbPagos.php");
	require_once("../db/DbAnticipos.php");
	require_once("../db/DbAdmision.php");
	require_once("../db/DbPacientes.php");
	require_once("../db/DbVariables.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbPlanes.php");
	require_once("../db/DbMaestroInsumos.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/Class_Numeros_A_Letras.php");
	require_once("../funciones/Class_Consultas_Siesa.php");
	
	$dbPagos = new DbPagos();
	$dbAnticipos = new DbAnticipos();
	$dbAdmision = new DbAdmision();
	$dbPacientes = new DbPacientes();
	$dbVariables = new Dbvariables();
	$dbListas = new DbListas();
	$dbPlanes = new DbPlanes();
	$dbMaestroInsumos = new DbMaestroInsumos();
	
	$contenidoHtml = new ContenidoHtml();
	$utilidades = new Utilidades();
	$funcionesPersona = new FuncionesPersona();
	$numerosALetras = new Class_Numeros_A_Letras();
	$classConsultasSiesa = new Class_Consultas_Siesa();
	
	$contenidoHtml->validar_seguridad(1);
	
	$opcion = $utilidades->str_decode($_POST["opcion"]);
	
	switch ($opcion) {
		case "1": //Impresión de recibo de pago
			require_once("../funciones/pdf/fpdf.php");
			require_once("../funciones/pdf/makefont/makefont.php");
			require_once("../funciones/pdf/funciones.php");
			require_once("../funciones/pdf/WriteHTML.php");
			
			@$id_pago = $utilidades->str_decode($_POST["id_pago"]);
			@$id_admision = $utilidades->str_decode($_POST["id_admision"]);
			
			$pago_obj = array();
			//Se obtienen los datos del pago
			if ($id_pago != "") {
				$pago_obj = $dbPagos->get_pago_id($id_pago);
				$lista_pagos = array($pago_obj);
				$id_admision = $pago_obj["id_admision"];
			} else {
				$lista_pagos = $dbPagos->get_lista_pagos($id_admision);
			}
			
			$admision_obj = array();
			if ($id_admision != "") {
				$admision_obj = $dbAdmision->get_admision($id_admision);
			}
			
			//Se obtienen los datos del paciente
			$paciente_obj = $dbPacientes->getPaciente($lista_pagos[0]["id_paciente"]);
			
			$cont_pagos = 0;
			foreach ($lista_pagos as $pago_obj) {
				$id_pago = $pago_obj["id_pago"];
				
				//Se obtienen los datos de la sede a la que corresponde el pago
				$sede_obj = $dbListas->getSedesDetalle($pago_obj["id_lugar_cita"]);
				
				//Se obtienen los datos de la sede principal
				$sede_ppal_obj = $dbListas->getSedesDetalleId($sede_obj["id_sede_ppal"]);
				
				//Se obtiene el detalle del pago
				$lista_pagos_det = $dbPagos->get_lista_pagos_detalle($id_pago);
				
				//Se obtienen los medios de pago adscritos al pago
				$lista_pagos_det_medios = $dbPagos->getListaPagosDetMedios($id_pago, 2);
				
				$pdf = new FPDF('P', 'mm', array(216, 279));
				$pdfHTML = new PDF_HTML();
				$pdf->SetMargins(10, 10, 10);
				$pdf->SetAutoPageBreak(false);
				$pdf->SetFillColor(255, 255, 255);
				
				$pdf->bordeMulticell = 0; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde
				$pdf->pie_pagina = false;
				
				$pdf->AddPage();
				
				$pdf->SetFont("Arial", "", 9);
				srand(microtime() * 1000000);
				
				if ($pago_obj["estado_pago"] == "3") {
					//Se marca el pago como anulado
					$pdf->SetFont("Arial", "B", 32);
					$pdf->SetTextColor(255, 150, 150);
					$pdf->SetY(90);
					$pdf->Cell(195, 12, ajustarCaracteres("ANULADO"), 0, 0, "C");
					$pdf->SetTextColor(0, 0, 0);
				}
				
				//Se obtienen las dimensiones del logo
				$arr_prop_imagen = getimagesize($sede_ppal_obj["dir_logo_sede_det"]);
				$ancho_aux = floatval($arr_prop_imagen[0]);
				$alto_aux = floatval($arr_prop_imagen[1]);
				
				$ancho_max = 50.0;
				$alto_max = 15.0;
				
				if ($ancho_aux > $ancho_max) {
					$alto_aux = floor($alto_aux * ($ancho_max / $ancho_aux));
					$ancho_aux = floor($ancho_aux * ($ancho_max / $ancho_aux));
				}
				if ($alto_aux > $alto_max) {
					$ancho_aux = floor($ancho_aux * ($alto_max / $alto_aux));
					$alto_aux = floor($alto_aux * ($alto_max / $alto_aux));
				}
				
				//Logo
				$pdf->Image($sede_ppal_obj["dir_logo_sede_det"], 10 + (50 - $ancho_aux) / 2, 10, $ancho_aux, $alto_aux);
				
				$pdf->SetFont("Arial", "BU", 9);
				$pdf->SetY(10);
				$pdf->Cell(165);
				$pdf->Cell(30, 4, ajustarCaracteres("RECIBO DE PAGO"), 0, 0, "R");
				$pdf->SetFont("Arial", "", 9);
				$pdf->ln();
				$pdf->Cell(165);
				$pdf->Cell(30, 4, ajustarCaracteres("No. ".$id_pago), 0, 0, "R");
				
				$pdf->ln(10);
				$pdf->SetFont("Arial", "B", 9);
				$pdf->Cell(25, 6, ajustarCaracteres("Código:"), 0, 0, "R");
				$pdf->SetFont("Arial", "", 9);
				$pdf->Cell(30, 6, ajustarCaracteres($id_admision), 0, 0, "L");
				$pdf->ln();
				$pdf->SetFont("Arial", "B", 9);
				$pdf->Cell(25, 6, ajustarCaracteres("Historia:"), 0, 0, "R");
				$pdf->SetFont("Arial", "", 9);
				$pdf->Cell(30, 6, ajustarCaracteres($paciente_obj["numero_documento"]), 0, 0, "L");
				$nombre_completo = $funcionesPersona->obtenerNombreCompleto($paciente_obj["nombre_1"], $paciente_obj["nombre_2"], $paciente_obj["apellido_1"], $paciente_obj["apellido_2"]);
				$pdf->Cell(100, 6, ajustarCaracteres($nombre_completo), 0, 0, "L");
				$pdf->ln();
				$pdf->SetFont("Arial", "B", 9);
				$pdf->Cell(25, 6, ajustarCaracteres("Fecha:"), 0, 0, "R");
				$pdf->SetFont("Arial", "", 9);
				$pdf->Cell(30, 6, ajustarCaracteres($pago_obj["fecha_pago_t"] != "" ? $pago_obj["fecha_pago_t"] : $admision_obj["fecha_admision_t"]), 0, 0, "L");
				$pdf->SetFont("Arial", "B", 9);
				$pdf->Cell(25, 6, ajustarCaracteres("No. documento:"), 0, 0, "R");
				$pdf->SetFont("Arial", "", 9);
				$pdf->Cell(30, 6, ajustarCaracteres($paciente_obj["numero_documento"]), 0, 0, "L");
				$pdf->ln();
				$pdf->SetFont("Arial", "B", 9);
				$pdf->Cell(25, 6, ajustarCaracteres("Hora:"), 0, 0, "R");
				$pdf->SetFont("Arial", "", 9);
				$pdf->Cell(30, 6, ajustarCaracteres($pago_obj["hora_pago_t"] != "" ? $pago_obj["hora_pago_t"] : $admision_obj["hora_admision_t"]), 0, 0, "L");
				$pdf->SetFont("Arial", "B", 9);
				$pdf->Cell(25, 6, ajustarCaracteres("Entidad:"), 0, 0, "R");
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(115, 6, ajustarCaracteres($pago_obj["nombre_convenio"]." - ".$pago_obj["nombre_plan"]), 0, "L");
				
				$motivo_aux = "";
				$total_aux = 0;
				if (count($lista_pagos_det) > 0) {
					foreach ($lista_pagos_det as $pago_det_aux) {
						if ($motivo_aux != "") {
							$motivo_aux .= " - ";
						}
						switch ($pago_det_aux["tipo_precio"]) {
							case "P":
								$motivo_aux .= $pago_det_aux["nombre_procedimiento"];
								break;
							case "M":
								$motivo_aux .= $pago_det_aux["nombre_generico"];
								break;
							case "I":
								$motivo_aux .= $pago_det_aux["nombre_insumo"];
								break;
						}
						switch ($pago_det_aux["tipo_bilateral"]) {
							case "1":
								$motivo_aux .= " (unilateral)";
								break;
							case "1":
								$motivo_aux .= " (bilateral)";
								break;
						}
						
						$total_aux += floatval($pago_det_aux["total"]);
					}
				}
				
				$pdf->SetFont("Arial", "B", 9);
				$pdf->Cell(25, 6, ajustarCaracteres("Motivo:"), 0, 0, "R");
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(170, 6, ajustarCaracteres($motivo_aux), 0, "L");
				$pdf->SetFont("Arial", "B", 9);
				$pdf->Cell(25, 6, ajustarCaracteres("Observaciones:"), 0, 0, "R");
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(170, 6, ajustarCaracteres(isset($admision_obj["observaciones_admision"]) ? $admision_obj["observaciones_admision"] : ""), 0, "L");
				
				$pdf->Ln(3);
				$pdf->Cell(60, 5, ajustarCaracteres("Pago"), 1, 0, "C");
				$pdf->Cell(105, 5, ajustarCaracteres("Notas"), 1, 0, "C");
				$pdf->Cell(30, 5, ajustarCaracteres("Valor"), 1, 0, "C");
				
				$cont_conceptos_adic = 0;
				if (count($lista_pagos_det_medios) > 0) {
					foreach ($lista_pagos_det_medios as $pago_det_medio_aux) {
						if ($pago_det_medio_aux["id_tipo_concepto"] == "414") {
							$pdf->Ln();
							$pdf->Cell(60, 5, ajustarCaracteres($pago_det_medio_aux["nombre_tipo_pago"]), 0, 0, "L");
							$pdf->Cell(105, 5, ajustarCaracteres($pago_det_medio_aux["nombre_banco"]), 0, 0, "L");
							if ($pago_det_medio_aux["ind_negativo"] == "0") {
								$pdf->Cell(30, 5, ajustarCaracteres(number_format($pago_det_medio_aux["valor_pago"], 0, ",", ".")), 0, 0, "R");
							} else {
								$pdf->Cell(30, 5, ajustarCaracteres("(".number_format($pago_det_medio_aux["valor_pago"], 0, ",", ".").")"), 0, 0, "R");
							}
						} else {
							$cont_conceptos_adic++;
						}
					}
				} else {
					$medio_pago_aux = $admision_obj["medio_pago"];
					
					$pdf->Ln();
					$pdf->Cell(60, 5, ajustarCaracteres($medio_pago_aux), 0, 0, "L");
					$pdf->Cell(105, 5, "", 0, 0, "L");
					$pdf->Cell(30, 5, ajustarCaracteres(number_format($total_aux, 0, ",", ".")), 0, 0, "R");
				}
				
				$pdf->SetFont("Arial", "B", 9);
				if ($cont_conceptos_adic == 0) {
					$pdf->SetY(110);
					$pdf->Cell(100, 5);
					$pdf->Cell(95, 5, "", "B", 0, "L");
					$pdf->Ln();
					$pdf->Cell(100, 5, "", 0, 0, "L");
					$pdf->Cell(70, 5, ajustarCaracteres("TOTAL"), 0, 0, "R");
					$pdf->Cell(25, 5, ajustarCaracteres(number_format($total_aux, 0, ",", ".")), 0, 0, "R");
				} else {
					$pdf->SetY(110 - 5 * ($cont_conceptos_adic + 1) - 1);
					$pdf->Cell(100, 5);
					$pdf->Cell(95, 5, "", "B", 0, "L");
					$pdf->Ln();
					$pdf->Cell(100, 5, "", 0, 0, "L");
					$pdf->Cell(70, 5, ajustarCaracteres("SUBTOTAL"), 0, 0, "R");
					$pdf->Cell(25, 5, ajustarCaracteres(number_format($total_aux, 0, ",", ".")), 0, 0, "R");
					foreach ($lista_pagos_det_medios as $pago_det_medio_aux) {
						if ($pago_det_medio_aux["id_tipo_concepto"] != "414") {
							$pdf->Ln();
							$pdf->Cell(100, 5, "", 0, 0, "L");
							$pdf->Cell(70, 5, ajustarCaracteres($pago_det_medio_aux["nombre_tipo_pago"]), 0, 0, "R");
							if ($pago_det_medio_aux["ind_negativo"] == "1") {
								$pdf->Cell(25, 5, ajustarCaracteres(number_format($pago_det_medio_aux["valor_pago"], 0, ",", ".")), 0, 0, "R");
								$total_aux += $pago_det_medio_aux["valor_pago"];
							} else {
								$pdf->Cell(25, 5, ajustarCaracteres("(".number_format($pago_det_medio_aux["valor_pago"], 0, ",", ".").")"), 0, 0, "R");
								$total_aux -= $pago_det_medio_aux["valor_pago"];
							}
						}
					}
					$pdf->Ln(1);
					$pdf->Cell(100, 5);
					$pdf->Cell(95, 5, "", "B", 0, "L");
					$pdf->Ln();
					$pdf->Cell(100, 5, "", 0, 0, "L");
					$pdf->Cell(70, 5, ajustarCaracteres("TOTAL"), 0, 0, "R");
					$pdf->Cell(25, 5, ajustarCaracteres(number_format($total_aux, 0, ",", ".")), 0, 0, "R");
				}
				$pdf->SetY(115);
				$pdf->Cell(150, 5, ajustarCaracteres('"PÁGUESE ÚNICAMENTE EN CAJA"'), 0, 0, "L");
				
				//Se guarda el documento pdf
				$nombreArchivo = "../tmp/recibo_pago_" . $_SESSION["idUsuario"]."_".$cont_pagos.".pdf";
				$pdf->Output($nombreArchivo, "F");
		?>
	    <input type="hidden" name="hdd_ruta_arch_pdf_<?php echo($cont_pagos); ?>" id="hdd_ruta_arch_pdf_<?php echo($cont_pagos); ?>" value="<?php echo($nombreArchivo); ?>" />
        <?php
				$cont_pagos++;
			}
			break;
			
		case "2": //Impresión de recibo de anticipo
			require_once("../funciones/pdf/fpdf.php");
			require_once("../funciones/pdf/makefont/makefont.php");
			require_once("../funciones/pdf/funciones.php");
			require_once("../funciones/pdf/WriteHTML.php");
			
			@$id_anticipo = $utilidades->str_decode($_POST["id_anticipo"]);
			$anticipo_obj = $dbAnticipos->get_anticipo($id_anticipo);
			
			//Se obtienen los datos del paciente
			$paciente_obj = $dbPacientes->getPaciente($anticipo_obj["id_paciente"]);
			
			//Se obtienen los datos de la sede a la que corresponde el pago
			$sede_obj = $dbListas->getSedesDetalle($anticipo_obj["id_lugar"]);
			
			//Se obtienen los datos de la sede principal
			$sede_ppal_obj = $dbListas->getSedesDetalleId($sede_obj["id_sede_ppal"]);
			
			//Se obtienen los medios de pago adscritos al anticipo
			$lista_anticipos_det_medios = $dbAnticipos->get_lista_anticipos_det_medios($id_anticipo, 2);
			
			$pdf = new FPDF('P', 'mm', array(216, 279));
			$pdfHTML = new PDF_HTML();
			$pdf->SetMargins(10, 10, 10);
			$pdf->SetAutoPageBreak(false);
			$pdf->SetFillColor(255, 255, 255);
			
			$pdf->bordeMulticell = 0; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde
			$pdf->pie_pagina = false;
			
			$pdf->AddPage();
			
			$pdf->SetFont("Arial", "", 9);
			srand(microtime() * 1000000);
			
			$pdf->SetFont("Arial", "B", 32);
			$pdf->SetY(90);
			if ($anticipo_obj["estado_anticipo"] == "3") {
				//Se marca el anticipo como anulado
				$pdf->SetTextColor(255, 150, 150);
				$pdf->Cell(195, 12, ajustarCaracteres("ANULADO"), 0, 0, "C");
			} else {
				//Se marca el anticipo como pagado
				$pdf->SetTextColor(0, 220, 0);
				$pdf->Cell(195, 12, ajustarCaracteres("PAGADO"), 0, 0, "C");
			}
			$pdf->SetTextColor(0, 0, 0);
			
			//Se obtienen las dimensiones del logo
			$arr_prop_imagen = getimagesize($sede_ppal_obj["dir_logo_sede_det"]);
			$ancho_aux = floatval($arr_prop_imagen[0]);
			$alto_aux = floatval($arr_prop_imagen[1]);
			
			$ancho_max = 50.0;
			$alto_max = 15.0;
			
			if ($ancho_aux > $ancho_max) {
				$alto_aux = floor($alto_aux * ($ancho_max / $ancho_aux));
				$ancho_aux = floor($ancho_aux * ($ancho_max / $ancho_aux));
			}
			if ($alto_aux > $alto_max) {
				$ancho_aux = floor($ancho_aux * ($alto_max / $alto_aux));
				$alto_aux = floor($alto_aux * ($alto_max / $alto_aux));
			}
			
			//Logo
			$pdf->Image($sede_ppal_obj["dir_logo_sede_det"], 10 + (50 - $ancho_aux) / 2, 10, $ancho_aux, $alto_aux);
			//Logo
			//$pdf->Image("../imagenes/logo-color.png", 10, 10, 40);
			
			$pdf->SetFont("Arial", "BU", 9);
			$pdf->SetY(10);
			$pdf->Cell(165);
			$pdf->Cell(30, 4, ajustarCaracteres("ANTICIPO"), 0, 0, "R");
			$pdf->SetFont("Arial", "", 9);
			$pdf->ln();
			$pdf->Cell(165);
			$pdf->Cell(30, 4, ajustarCaracteres("No. ".$id_anticipo), 0, 0, "R");
			
			$pdf->ln(10);
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(25, 6, ajustarCaracteres("Historia:"), 0, 0, "R");
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(30, 6, ajustarCaracteres($paciente_obj["numero_documento"]), 0, 0, "L");
			$nombre_completo = $funcionesPersona->obtenerNombreCompleto($paciente_obj["nombre_1"], $paciente_obj["nombre_2"], $paciente_obj["apellido_1"], $paciente_obj["apellido_2"]);
			$pdf->Cell(100, 6, ajustarCaracteres($nombre_completo), 0, 0, "L");
			$pdf->ln();
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(25, 6, ajustarCaracteres("Fecha:"), 0, 0, "R");
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(30, 6, ajustarCaracteres($anticipo_obj["fecha_crea_t"]), 0, 0, "L");
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(25, 6, ajustarCaracteres("No. documento:"), 0, 0, "R");
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(30, 6, ajustarCaracteres($paciente_obj["numero_documento"]), 0, 0, "L");
			$pdf->ln();
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(25, 6, ajustarCaracteres("Hora:"), 0, 0, "R");
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(30, 6, ajustarCaracteres($anticipo_obj["hora_crea_t"]), 0, 0, "L");
			
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(25, 6, ajustarCaracteres("Observaciones:"), 0, 0, "R");
			$pdf->SetFont("Arial", "", 9);
			$pdf->MultiCell(170, 6, ajustarCaracteres($anticipo_obj["observaciones_anticipo"]), 0, "L");
			
			$pdf->Ln(3);
			$pdf->Cell(60, 5, ajustarCaracteres("Pago"), 1, 0, "C");
			$pdf->Cell(105, 5, ajustarCaracteres("Notas"), 1, 0, "C");
			$pdf->Cell(30, 5, ajustarCaracteres("Valor"), 1, 0, "C");
			
			$cont_conceptos_adic = 0;
			foreach ($lista_anticipos_det_medios as $det_medio_aux) {
				if ($det_medio_aux["id_tipo_concepto"] == "414") {
					$pdf->Ln();
					$pdf->Cell(60, 5, ajustarCaracteres($det_medio_aux["nombre_tipo_pago"]), 0, 0, "L");
					$pdf->Cell(105, 5, ajustarCaracteres($det_medio_aux["nombre_banco"]), 0, 0, "L");
					if ($det_medio_aux["ind_negativo"] == "0") {
						$pdf->Cell(30, 5, ajustarCaracteres(number_format($det_medio_aux["valor_pago"], 0, ",", ".")), 0, 0, "R");
					} else {
						$pdf->Cell(30, 5, ajustarCaracteres("(".number_format($det_medio_aux["valor_pago"], 0, ",", ".").")"), 0, 0, "R");
					}
				} else {
					$cont_conceptos_adic++;
				}
			}
			
			$total_aux = $anticipo_obj["valor"];
			$pdf->SetFont("Arial", "B", 9);
			if ($cont_conceptos_adic == 0) {
				$pdf->SetY(105);
				$pdf->Cell(100, 5);
				$pdf->Cell(95, 5, "", "B", 0, "L");
				$pdf->Ln();
				$pdf->Cell(100, 5, "", 0, 0, "L");
				$pdf->Cell(70, 5, ajustarCaracteres("TOTAL"), 0, 0, "R");
				$pdf->Cell(25, 5, ajustarCaracteres(number_format($total_aux, 0, ",", ".")), 0, 0, "R");
			} else {
				$pdf->SetY(105 - 5 * ($cont_conceptos_adic + 1) - 1);
				$pdf->Cell(100, 5);
				$pdf->Cell(95, 5, "", "B", 0, "L");
				$pdf->Ln();
				$pdf->Cell(100, 5, "", 0, 0, "L");
				$pdf->Cell(70, 5, ajustarCaracteres("SUBTOTAL"), 0, 0, "R");
				$pdf->Cell(25, 5, ajustarCaracteres(number_format($total_aux, 0, ",", ".")), 0, 0, "R");
				foreach ($lista_anticipos_det_medios as $det_medio_aux) {
					if ($det_medio_aux["id_tipo_concepto"] != "414") {
						$pdf->Ln();
						$pdf->Cell(100, 5, "", 0, 0, "L");
						$pdf->Cell(70, 5, ajustarCaracteres($det_medio_aux["nombre_tipo_pago"]), 0, 0, "R");
						if ($det_medio_aux["ind_negativo"] == "1") {
							$pdf->Cell(25, 5, ajustarCaracteres(number_format($det_medio_aux["valor_pago"], 0, ",", ".")), 0, 0, "R");
							$total_aux += $det_medio_aux["valor_pago"];
						} else {
							$pdf->Cell(25, 5, ajustarCaracteres("(".number_format($det_medio_aux["valor_pago"], 0, ",", ".").")"), 0, 0, "R");
							$total_aux -= $det_medio_aux["valor_pago"];
						}
					}
				}
				$pdf->Ln(1);
				$pdf->Cell(100, 5);
				$pdf->Cell(95, 5, "", "B", 0, "L");
				$pdf->Ln();
				$pdf->Cell(100, 5, "", 0, 0, "L");
				$pdf->Cell(70, 5, ajustarCaracteres("TOTAL"), 0, 0, "R");
				$pdf->Cell(25, 5, ajustarCaracteres(number_format($total_aux, 0, ",", ".")), 0, 0, "R");
			}
			
			$pdf->Ln();
			$pdf->Cell(100, 5, "", 0, 0, "L");
			$pdf->Cell(70, 5, ajustarCaracteres("SALDO"), 0, 0, "R");
			$pdf->Cell(25, 5, ajustarCaracteres(number_format($anticipo_obj["saldo"], 0, ",", ".")), 0, 0, "R");
			
			//Se obtienen la fecha y hora actual
			$fecha_hora_obj = $dbVariables->getFechaActualMostrar();
			
			$pdf->SetY(110);
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(150, 5, ajustarCaracteres("Fecha de impresión: ".$fecha_hora_obj["fecha_actual_mostrar"]." ".$fecha_hora_obj["hora_actual_mostrar"]), 0, 1, "L");
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(150, 5, ajustarCaracteres('"PÁGUESE ÚNICAMENTE EN CAJA"'), 0, 0, "L");
			
			//Se guarda el documento pdf
			$nombreArchivo = "../tmp/recibo_anticipo_" . $_SESSION["idUsuario"].".pdf";
			$pdf->Output($nombreArchivo, "F");
		?>
	    <input type="hidden" name="hdd_ruta_arch_pdf" id="hdd_ruta_arch_pdf" value="<?php echo($nombreArchivo); ?>" />
        <?php
			break;
			
		case "3": //Impresión de factura
			require_once("../funciones/pdf/fpdf.php");
			require_once("../funciones/pdf/makefont/makefont.php");
			require_once("../funciones/pdf/funciones.php");
			require_once("../funciones/pdf/WriteHTML.php");
			
			@$id_pago = $utilidades->str_decode($_POST["id_pago"]);
			
			//Se obtienen los datos del pago
			$pago_obj = $dbPagos->get_pago_id($id_pago);
			
			//Se obtienen los datos del paciente
			$paciente_obj = $dbPacientes->getPaciente($pago_obj["id_paciente"]);
			
			//Se obtiene el detalle del pago
			$lista_pagos_det = $dbPagos->get_lista_pagos_detalle($id_pago);
			
			//Se obtienen los medios de pago adscritos al pago
			$lista_pagos_det_medios = $dbPagos->getListaPagosDetMedios($id_pago, 2);
			
			//Se obtienen los datos del plan
			$plan_obj = $dbPlanes->getPlan($pago_obj["id_plan"]);
			$ind_tipo_pago = $plan_obj["ind_tipo_pago"];
			$fecha_texto = $funcionesPersona->obtenerFecha6($pago_obj["fecha_pago_t"]);
			
			//Se obtienen los datos de la sede a la que corresponde el pago
			$sede_obj = $dbListas->getSedesDetalle($pago_obj["id_lugar_cita"]);
			
			//Se obtienen los datos de la sede principal
			$sede_ppal_obj = $dbListas->getSedesDetalleId($sede_obj["id_sede_ppal"]);
			
			$pdf = new FPDF('P', 'mm', array(216, 279));
			$pdfHTML = new PDF_HTML();
			$pdf->SetMargins(10, 10, 10);
			$pdf->SetAutoPageBreak(false);
			$pdf->SetFillColor(255, 255, 255);
			
			$pdf->bordeMulticell = 0; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde
			$pdf->pie_pagina = false;
			
			$pdf->AddPage();
			
			$pdf->SetFont("Arial", "", 9);
			srand(microtime() * 1000000);
			
			if ($pago_obj["estado_pago"] == "3") {
				//Se marca el pago como anulado
				$pdf->SetFont("Arial", "B", 32);
				$pdf->SetTextColor(255, 150, 150);
				$pdf->SetY(90);
				$pdf->Cell(195, 12, ajustarCaracteres("ANULADO"), 0, 0, "C");
				$pdf->SetTextColor(0, 0, 0);
			}
			
			//Se obtienen las dimensiones del logo
			$arr_prop_imagen = getimagesize($sede_ppal_obj["dir_logo_sede_det"]);
			$ancho_aux = floatval($arr_prop_imagen[0]);
			$alto_aux = floatval($arr_prop_imagen[1]);
			
			$ancho_max = 50.0;
			$alto_max = 20.0;
			
			if ($ancho_aux > $ancho_max) {
				$alto_aux = floor($alto_aux * ($ancho_max / $ancho_aux));
				$ancho_aux = floor($ancho_aux * ($ancho_max / $ancho_aux));
			}
			if ($alto_aux > $alto_max) {
				$ancho_aux = floor($ancho_aux * ($alto_max / $alto_aux));
				$alto_aux = floor($alto_aux * ($alto_max / $alto_aux));
			}
			
			//Logo
			$pdf->Image($sede_ppal_obj["dir_logo_sede_det"], 10 + (50 - $ancho_aux) / 2, 10, $ancho_aux, $alto_aux);
			
			$pdf->SetY(10);
			$pdf->SetX(65);
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(70, 4, ajustarCaracteres($sede_ppal_obj["nombre_prestador"]), 0, 1, "C");
			$pdf->SetX(65);
			$pdf->SetFont("Arial", "", 8);
			$pdf->Cell(70, 3, ajustarCaracteres($sede_ppal_obj["tipo_documento"].": ".$sede_ppal_obj["numero_documento"]), 0, 1, "C");
			$pdf->SetX(65);
			$pdf->MultiCell(70, 3, ajustarCaracteres($sede_ppal_obj["dir_sede_det"]), 0, "C");
			$pdf->SetX(65);
			$pdf->Cell(70, 3, ajustarCaracteres("Tel: ".$sede_ppal_obj["tel_sede_det"]), 0, 1, "C");
			
			$pdf->SetFont("Arial", "B", 9);
			$pdf->SetY(10);
			$pdf->Cell(150);
			$pdf->Cell(45, 4, ajustarCaracteres("FACTURA DE VENTA"), "TLR", 1, "C");
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(150);
			$pdf->Cell(45, 4, ajustarCaracteres("Número: ".$sede_ppal_obj["prefijo_resol_facturas"].$pago_obj["num_factura"]), "LR", 1, "L");
			$pdf->Cell(150);
			$pdf->Cell(45, 4, ajustarCaracteres("Fecha: ".$fecha_texto), "TLR", 1, "L");
			$pdf->Cell(150);
			$pdf->Cell(45, 4, ajustarCaracteres("Página: 1 de 1"), "BLR", 1, "L");
			
			if ($pago_obj["numero_documento_tercero"] != "") {
				$numerod_documento = $pago_obj["numero_documento_tercero"];
				$nombre_completo = $pago_obj["nombre_tercero"];
			} else {
				$numerod_documento = $pago_obj["numero_documento"];
				$nombre_completo = $funcionesPersona->obtenerNombreCompleto($pago_obj["nombre_1"], $pago_obj["nombre_2"], $pago_obj["apellido_1"], $pago_obj["apellido_2"]);
			}
			$pdf->SetY(30);
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(20, 5, ajustarCaracteres("Cliente:"), "TL", 0, "R");
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(85, 5, ajustarCaracteres($nombre_completo), "T", 0, "L");
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(60, 5, ajustarCaracteres("Forma de pago"), "TL", 0, "L");
			$pdf->Cell(30, 5, ajustarCaracteres("Fecha vcto."), "TLR", 0, "L");
			
			//Se buscan los medios de pago que no sea boleta ni descuento
			$medio_pago_texto = "";
			foreach ($lista_pagos_det_medios as $medio_aux) {
				if ($medio_aux["id_medio_pago"] != "0" && $medio_aux["id_medio_pago"] != "97") {
					$medio_pago_texto = $medio_aux["nombre_tipo_pago"];
				}
			}
			
			$pdf->ln();
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(20, 5, ajustarCaracteres("Nit o C.C.:"), "L", 0, "R");
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(85, 5, ajustarCaracteres($numerod_documento), 0, 0, "L");
			$pdf->Cell(60, 5, ajustarCaracteres($medio_pago_texto), "L", 0, "L");
			$pdf->Cell(30, 5, ajustarCaracteres($fecha_texto), "LR", 0, "L");
			
			$pdf->ln();
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(20, 5, ajustarCaracteres("Dirección:"), "L", 0, "R");
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(85, 5, ajustarCaracteres($paciente_obj["direccion"]), 0, 0, "L");
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(90, 5, ajustarCaracteres("Vendedor"), "TLR", 0, "L");
			
			$pdf->ln();
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(20, 5, ajustarCaracteres("Ciudad:"), "L", 0, "R");
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(85, 5, ajustarCaracteres($paciente_obj["nom_mun_t"]." (".$paciente_obj["nom_dep_t"].")"), 0, 0, "L");
			$pdf->Cell(90, 5, ajustarCaracteres($pago_obj["nombre_usuario"]." ".$pago_obj["apellido_usuario"]), "LR", 0, "L");
			
			$pdf->ln();
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(20, 5, ajustarCaracteres("Teléfono:"), "L", 0, "R");
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(85, 5, ajustarCaracteres($paciente_obj["telefono_1"]), 0, 0, "L");
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(60, 5, ajustarCaracteres("O.C. número"), "TL", 0, "L");
			$pdf->Cell(30, 5, ajustarCaracteres("Moneda"), "TLR", 0, "L");
			
			$pdf->ln();
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(20, 5, ajustarCaracteres("Correo:"), "LB", 0, "R");
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(85, 5, ajustarCaracteres($paciente_obj["email"]), "B", 0, "L");
			$pdf->Cell(60, 5, "", "LB", 0, "L");
			$pdf->Cell(30, 5, "COP", "LRB", 1, "L");
			
			$pdf->ln(5);
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(20, 5, ajustarCaracteres("Código"), "TLB", 0, "C");
			$pdf->Cell(90, 5, ajustarCaracteres("Detalle"), "TB", 0, "C");
			$pdf->Cell(25, 5, ajustarCaracteres("Cantidad"), "TB", 0, "C");
			$pdf->Cell(30, 5, ajustarCaracteres("Precio unit."), "TB", 0, "C");
			$pdf->Cell(30, 5, ajustarCaracteres("Vlr. total"), "TRB", 1, "C");
			$pdf->SetFont("Arial", "", 9);
			
			
			$total_aux = 0;
			$copago_aux = 0;
			foreach ($lista_pagos_det as $pago_det_aux) {
				$total_aux += floatval($pago_det_aux["total"]);
				$copago_aux += floatval($pago_det_aux["valor_cuota"]);
			}
			
			//Se verifica el tipo de recaudo
			if ($ind_tipo_pago == "1") {
				//Copago/cuota moderadora
				
				//Se halla el concepto de copago/cuota moderadora
				$variable_obj_aux = $dbVariables->getVariable(22);
				$cod_concepto_cc = $variable_obj_aux["valor_variable"];
				$insumo_cc_obj = $dbMaestroInsumos->getInsumo($cod_concepto_cc);
				$arr_aux = array(
					"tipo_precio" => "I",
					"cod_insumo" => $cod_concepto_cc,
					"nombre_insumo" => $insumo_cc_obj["nombre_insumo"],
					"tipo_bilateral" => "0",
					"cantidad" => 1,
					"valor" => $copago_aux,
					"total" => $copago_aux);
				
				$lista_conceptos = array();
				array_push($lista_conceptos, $arr_aux);
				
				$total_aux = $copago_aux;
			} else {
				//Completo
				$lista_conceptos = $lista_pagos_det;
			}
			
			foreach ($lista_conceptos as $pago_det_aux) {
				$cod_concepto_aux = "";
				$nombre_concepto_aux = "";
				switch ($pago_det_aux["tipo_precio"]) {
					case "P":
						$cod_concepto_aux = $pago_det_aux["cod_procedimiento"];
						$nombre_concepto_aux = $pago_det_aux["nombre_procedimiento"];
						break;
					case "M":
						$cod_concepto_aux = $pago_det_aux["cod_medicamento"];
						$nombre_concepto_aux = $pago_det_aux["nombre_generico"];
						break;
					case "I":
						$cod_concepto_aux = $pago_det_aux["cod_insumo"];
						$nombre_concepto_aux = $pago_det_aux["nombre_insumo"];
						break;
				}
				switch ($pago_det_aux["tipo_bilateral"]) {
					case "1":
						$nombre_concepto_aux .= " (unilateral)";
						break;
					case "1":
						$nombre_concepto_aux .= " (bilateral)";
						break;
				}
				
				//Se ubica primero el nombre y se verifica si ocupa más de una linea
				$y_aux = $pdf->GetY();
				$pdf->SetX(35);
				$pdf->MultiCell(90, 5, ajustarCaracteres($nombre_concepto_aux), 0, "L");
				$y2_aux = $pdf->GetY();
				
				//Se agregan los demás campos
				$pdf->SetY($y_aux);
				$pdf->SetX(10);
				$pdf->Cell(20, $y2_aux - $y_aux, ajustarCaracteres($cod_concepto_aux), "L", 0, "C");
				$pdf->SetX(120);
				$pdf->Cell(25, $y2_aux - $y_aux, $pago_det_aux["cantidad"], "", 0, "C");
				$pdf->Cell(30, $y2_aux - $y_aux, "$".str_replace(",", ".", number_format($pago_det_aux["valor"])), "", 0, "C");
				$pdf->Cell(30, $y2_aux - $y_aux, "$".str_replace(",", ".", number_format($pago_det_aux["total"])), "R", 1, "C");
			}
			
			//Se buscan los totales por impuestos y retenciones
			$lista_detalle_factura = $classConsultasSiesa->consultarFacturasDetalle($sede_obj["id_compania"], $pago_obj["num_factura"]);
			if (isset($lista_detalle_factura[0])) {
				$total_impuestos = $lista_detalle_factura[0]["TOTAL_IMPUESTOS_FACTURA"];
				$total_retencion = $lista_detalle_factura[0]["TOTAL_RETENCIONES_FACTURA"];
				$total_descuentos = $lista_detalle_factura[0]["TOTAL_DSCTO_FACTURA"];
				$total_neto = $lista_detalle_factura[0]["TOTAL_NETO_FACTURA"];
			} else {
				$total_impuestos = 0;
				$total_retencion = 0;
				$total_descuentos = 0;
				$total_neto = $total_aux;
			}
			
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(39, 5, ajustarCaracteres("Total bruto"), "TLR", 0, "R");
			$pdf->Cell(39, 5, ajustarCaracteres("Vlr. impuestos"), "TR", 0, "R");
			$pdf->Cell(39, 5, ajustarCaracteres("Vlr. retención"), "TR", 0, "R");
			$pdf->Cell(39, 5, ajustarCaracteres("Descuentos"), "TR", 0, "R");
			$pdf->Cell(39, 5, ajustarCaracteres("Total"), "TR", 1, "R");
			
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(39, 5, "$".str_replace(",", ".", number_format($total_aux)), "LRB", 0, "R");
			$pdf->Cell(39, 5, "$".str_replace(",", ".", number_format($total_impuestos)), "RB", 0, "R");
			$pdf->Cell(39, 5, "$".str_replace(",", ".", number_format($total_retencion)), "RB", 0, "R");
			$pdf->Cell(39, 5, "$".str_replace(",", ".", number_format($total_descuentos)), "RB", 0, "R");
			$pdf->Cell(39, 5, "$".str_replace(",", ".", number_format($total_neto)), "RB", 1, "R");
			
			$pdf->ln(2);
			
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(25, 5, "Valor en letras:", 0, 0, "L");
			$pdf->SetFont("Arial", "", 9);
			$pdf->MultiCell(170, 5, ajustarCaracteres($numerosALetras->convertir($total_aux)." PESOS M/CTE"), 0, "L");
			
			$y_pie = 230;
			
			$pdf->SetFont("Arial", "", 8);
			$pdf->Line(10, $y_pie, 205, $y_pie);
			$pdf->SetY($y_pie + 3);
			$pdf->MultiCell(75, 4, ajustarCaracteres("El lugar del cumplimiento de la obligación es en el domicilio principal ".$sede_obj["dir_sede_det"]), 0, "L");
			
			$pdf->SetFont("Arial", "B", 9);
			$pdf->SetY($y_pie + 3);
			$pdf->SetX(95);
			$pdf->Cell(55, 4, ajustarCaracteres("Recibe:"), "0", 0, "L");
			$pdf->Cell(55, 4, ajustarCaracteres("Acepta:"), "0", 0, "L");
			$pdf->Line(95, $y_pie + 23, 145, $y_pie + 23);
			$pdf->Line(150, $y_pie + 23, 200, $y_pie + 23);
			
			$pdf->Line(10, $y_pie + 25, 205, $y_pie + 25);
			
			$pdf->SetY($y_pie + 27);
			$pdf->SetFont("Arial", "", 8);
			$pdf->Cell(195, 4, ajustarCaracteres("Responsable de IVA - Somos Agentes de Retención en la Fuente - Resolución DIAN No. ".$sede_ppal_obj["num_resol_facturas"]." - Fecha ".$funcionesPersona->obtenerFecha6($sede_ppal_obj["fecha_resol_faturas_t"])."  Vencimiento ".$funcionesPersona->obtenerFecha6($sede_ppal_obj["fecha_resol_vence_t"])), "0", 1, "L");
			
			if ($sede_ppal_obj["prefijo_resol_facturas"] != "") {
				$texto_prefijo = "prefijo ".$sede_ppal_obj["prefijo_resol_facturas"]." ";
			} else {
				$texto_prefijo = "";
			}
			$pdf->Cell(195, 4, ajustarCaracteres("Factura autorizada ".$texto_prefijo.$sede_ppal_obj["num_factura_ini"]." AL ".$sede_ppal_obj["num_factura_fin"]), "0", 1, "L");
			$pdf->Cell(195, 4, ajustarCaracteres("Factura impresa por computador con Software del Centro Oftalmológico Virgilio Galvis"), "0", 1, "L");
			
			//Se guarda el documento pdf
			$nombreArchivo = "../tmp/recibo_pago_" . $_SESSION["idUsuario"].".pdf";
			$pdf->Output($nombreArchivo, "F");
		?>
	    <input type="hidden" name="hdd_ruta_arch_pdf_fact" id="hdd_ruta_arch_pdf_fact" value="<?php echo($nombreArchivo); ?>" />
        <?php
	}
?>
