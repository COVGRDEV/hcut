<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
header('Content-Type: text/xml; charset=UTF-8');
session_start();

//$citas = new DbCitas();

require_once '../funciones/Utilidades.php';
require_once '../funciones/FuncionesPersona.php';

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
		require_once("../db/DbPlanes.php");
		require_once("../db/DbMaestroProcedimientos.php");
		require_once("../db/DbMaestroMedicamentos.php");
		require_once("../db/DbMaestroInsumos.php");
		require_once("../db/DbUsuarios.php");
		
		$funcionesPersona = new FuncionesPersona();
		
        $pdf = new FPDF('P', 'mm', 'Letter');
		$pdf->SetMargins(5, 5, 5);
        $dbPagos = new DbPagos();
        $dbTiposPago = new DbTiposPago();
		$dbConvenios = new DbConvenios();
		$dbPlanes = new DbPlanes();
		$dbMaestroProcedimientos = new DbMaestroProcedimientos();
		$dbMaestroMedicamentos = new DbMaestroMedicamentos();
		$dbMaestroInsumos = new DbMaestroInsumos();
		$dbUsuarios = new DbUsuarios();
		
        $fechaInicial = $utilidades->str_decode($_POST['fecha_inicial']);
        $fechaFinal = $utilidades->str_decode($_POST['fecha_final']);
        $id_convenio = $utilidades->str_decode($_POST['id_convenio']);
        $id_plan = $utilidades->str_decode($_POST['id_plan']);
        $cod_insumo = $utilidades->str_decode($_POST['cod_insumo']);
        $tipo_precio = $utilidades->str_decode($_POST['tipo_precio']);
        $usuario_adm = $utilidades->str_decode($_POST['id_usuario_adm']);
		$usuario = $utilidades->str_decode($_POST['id_usuario']);
		$tipo_formato = $utilidades->str_decode($_POST['tipo_formato']);
		// Establecer variables para la limitación de fecha teniendo en cuenta como se encuentra el formato
		$arr_diferencia = $dbVariables->getDiferenciaFechas($fecha_inicial, $fecha_final, $tipo_formato);
		$diferencia_dias = intval($arr_diferencia["dias"], 10);
		//Inicio del limitante de Fechas
		if($diferencia_dias>=34){
			
			echo'<script type="text/javascript">
			alert("SUPERA EL MÁXIMO DE DÍAS CONSULTADOS");
		</script>';
			
		}else{
		
        $rta_tipos_pagos_aux = $dbTiposPago->getListaTiposPagoNP();
        $rta_procedimientos_aux = $dbPagos->reporteTesoseriaProcedimientosPlanes($fechaInicial, $fechaFinal, $id_convenio, $id_plan, $cod_insumo, $tipo_precio, $usuario, $usuario_adm);
        $rta_pacientes_aux = $dbPagos->reporteTesoseriaProcedimientosPacientesPlanes($fechaInicial, $fechaFinal, $id_convenio, $id_plan, $cod_insumo, $tipo_precio, $usuario, $usuario_adm);
		
        $pdf->header = 2; //Selecciona el header
        $pdf->bordeMulticell = 2; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde
        $pdf->pie_pagina = true;
        
        $pdf->AddPage();
		
        $pdf->SetFont('Arial', '', 7);
        srand(microtime() * 1000000);
		
		//Se agregan los filtros seleccionados al reporte
		if ($fechaInicial != "") {
			$arr_aux = explode("-", $fechaInicial);
			$pdf->SetAligns2(array('L'));
			$pdf->SetWidths2(array(100));
			$pdf->Row2(array('Fecha inicial: '.$arr_aux[2]."/".$arr_aux[1]."/".$arr_aux[0]));
		}
		if ($fechaFinal != "") {
			$arr_aux = explode("-", $fechaFinal);
			$pdf->SetAligns2(array('L'));
			$pdf->SetWidths2(array(100));
			$pdf->Row2(array('Fecha final: '.$arr_aux[2]."/".$arr_aux[1]."/".$arr_aux[0]));
		}
		if ($id_convenio != "") {
			$convenio_obj = $dbConvenios->getConvenio($id_convenio);
			$pdf->SetAligns2(array('L'));
			$pdf->SetWidths2(array(100));
			$pdf->Row2(array(ajustarCaracteres('Convenio: '.$convenio_obj["nombre_convenio"])));
		}
		if ($id_plan != "") {
			$plan_obj = $dbPlanes->getPlan($id_plan);
			$pdf->SetAligns2(array('L'));
			$pdf->SetWidths2(array(100));
			$pdf->Row2(array(ajustarCaracteres('Plan: '.$plan_obj["nombre_plan"])));
		}
		if ($cod_insumo != "" & $tipo_precio != "") {
			$nombre_aux = "";
			switch ($tipo_precio) {
				case "P":
					$obj_aux = $dbMaestroProcedimientos->getProcedimiento($cod_insumo);
					if (isset($obj_aux["nombre_procedimiento"])) {
						$nombre_aux = $obj_aux["nombre_procedimiento"];
					}
					break;
				case "M":
					$obj_aux = $dbMaestroMedicamentos->getMedicamentos($cod_insumo);
					if (isset($obj_aux[0]["nombre_generico"])) {
						$nombre_aux = $obj_aux[0]["nombre_generico"]." - ".$obj_aux[0]["nombre_comercial"];
					}
					break;
				case "I":
					$obj_aux = $dbMaestroInsumos->getInsumos($cod_insumo);
					if (isset($obj_aux[0]["nombre_insumo"])) {
						$nombre_aux = $obj_aux[0]["nombre_insumo"];
					}
					break;
			}
			if ($nombre_aux != "") {
				$pdf->SetAligns2(array('L'));
				$pdf->SetWidths2(array(100));
				$pdf->Row2(array(ajustarCaracteres('Concepto: '.$cod_insumo." - ".$nombre_aux)));
			}
		}
		if ($usuario_adm != "") {
			$usuario_obj = $dbUsuarios->getUsuario($usuario_adm);
			$pdf->SetAligns2(array('L'));
			$pdf->SetWidths2(array(100));
			$pdf->Row2(array(ajustarCaracteres('Usuario admisión: '.$usuario_obj["nombre_usuario"]." ".$usuario_obj["apellido_usuario"])));
		}
		if ($usuario != "") {
			$usuario_obj = $dbUsuarios->getUsuario($usuario);
			$pdf->SetAligns2(array('L'));
			$pdf->SetWidths2(array(100));
			$pdf->Row2(array(ajustarCaracteres('Usuario registro pago: '.$usuario_obj["nombre_usuario"]." ".$usuario_obj["apellido_usuario"])));
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
                    if ($rta_pacientes_aux[$e]['cod_insumo'] == $rta_procedimientos_aux[$i]['cod_insumo'] && $rta_pacientes_aux[$e]["tipo_precio"] == $rta_procedimientos_aux[$i]["tipo_precio"]) {
                        if ($pasaporte_titulo_cups == 0) {
                            $pasaporte_titulo_cups = 1;
                        }

                        $n1 = 0;
						if ($rta_tipos_pagos_aux[$a]['id'] == $rta_pacientes_aux[$e]['id_medio_pago']
								|| ($rta_tipos_pagos_aux[$a]["id"] == "99" && trim($rta_pacientes_aux[$e]['id_medio_pago']) == "")) {
							//Si el tipo de pago es igual
                            if ($pasaporte_titulo_tpago == 0) {
                                $pasaporte_titulo_tpago = 1;
                            }
							
                            if ($pasaporte_titulo_tpago == 1) {//Imprime el titulo del tipo de pago
                                $pdf->SetAligns2(array('L'));
                                $pdf->SetWidths2(array(100));
								$pdf->SetFont('Arial', 'B', 7);
                                $pdf->Row2(array('FORMA DE PAGO: '.$rta_tipos_pagos_aux[$a]['nombre']));
								$pdf->SetFont('Arial', '', 7);
                                $pasaporte_titulo_tpago++;
                            }
							
                            if ($pasaporte_titulo_cups == 1) {//Imprime el titulo del cups y titulos de nombre completo, documento, precio ,etc...
                                $pdf->SetAligns2(array('L', 'L'));
                                $pdf->SetWidths2(array(15, 200));
								$pdf->SetFont('Arial', 'B', 7);
                                $pdf->Row2(array($rta_procedimientos_aux[$i]['cod_insumo'], ajustarCaracteres(mb_strtoupper($rta_procedimientos_aux[$i]['nombre_insumo'], "UTF-8"))));
								
                                $pdf->SetAligns2(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'R', 'R', 'R', 'R'));
								$pdf->SetWidths2(array(15, 11, 37, 16, 15, 12, 43, 16, 16, 8, 16));
                                $pdf->Row2(array('Fecha', 'Recibo', 'Nombre completo', 'Documento', 'Factura', 'Entidad', 'Convenio', 'Valor base', 'Valor pago', 'Cant', 'Valor total'));
								$pdf->SetFont('Arial', '', 7);
								
                                $pasaporte_titulo_cups++;
                            }
							
							//Calcula el valor por pagar para el tipo de pago
							$valor_por_pagar = 0;
							$abrev_pago = "";
							if ($rta_pacientes_aux[$e]['t_pago'] == $rta_tipos_pagos_aux[$a]['id']) {
								$valor_por_pagar = $rta_pacientes_aux[$e]['valor_pago'];
							} else if ($rta_pacientes_aux[$e]['valor_pago'] > 0) {
								//Se obtiene la abreviatura del tipo de pago
								$tipo_pago_aux = $tiposPago->getTipoPago($rta_pacientes_aux[$e]['id_medio_pago']);
								$abrev_pago = $tipo_pago_aux["abrev_pago"];
							}
                            
							$valor_total_aux = intval($rta_pacientes_aux[$e]['total_pago'], 10);
							if ($rta_pacientes_aux[$e]['ind_boleta'] == '1') {
								if ($rta_pacientes_aux[$e]['id_medio_pago'] == '0') {
									$valor_por_pagar = (intval($rta_pacientes_aux[$e]['valor'], 10) - intval($rta_pacientes_aux[$e]['valor_cuota'], 10)) * intval($rta_pacientes_aux[$e]["cantidad"], 10);
								} else if (intval($rta_pacientes_aux[$e]["total_cuota"], 10) > 0) {
									$valor_por_pagar = intval($rta_pacientes_aux[$e]['valor_cuota'], 10) * $rta_pacientes_aux[$e]["cantidad"] * $rta_pacientes_aux[$e]["valor_pago"] / $rta_pacientes_aux[$e]["total_cuota"];
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
							
							if ($valor_por_pagar > 0 || $rta_pacientes_aux[$e]['ind_boleta'] == "0") {
								$pasaporte_cantidades++;
								$pdf->SetAligns2(array('C', 'R', 'L', 'C', 'C', 'C', 'L', 'R', 'R', 'R', 'R'));
								$pdf->SetWidths2(array(15, 11, 37, 16, 15, 12, 43, 16, 16, 8, 16));
								$pdf->Row2(array(
									$rta_pacientes_aux[$e]["fecha_pago_t"],
									$rta_pacientes_aux[$e]["id_pago"],
									ajustarCaracteres(mb_strtoupper($funcionesPersona->obtenerNombreCompleto($rta_pacientes_aux[$e]['apellido_1'], $rta_pacientes_aux[$e]['apellido_2'], $rta_pacientes_aux[$e]['nombre_1'], $rta_pacientes_aux[$e]['nombre_2']), "UTF-8")),
									$rta_pacientes_aux[$e]['numero_documento'],
									$rta_pacientes_aux[$e]['num_factura'],
									$rta_pacientes_aux[$e]['nombre_entidad'],
									ajustarCaracteres($rta_pacientes_aux[$e]['nombre_convenio']." - ".$rta_pacientes_aux[$e]['nombre_plan']),
									'$'.number_format($valor_base_aux),
									'$'.number_format($valor_por_pagar / $rta_pacientes_aux[$e]['cantidad']),
									$rta_pacientes_aux[$e]['cantidad'],
									'$'.number_format($valor_por_pagar)));
								
								$contador_cantidades += $rta_pacientes_aux[$e]['cantidad'];
								$contador_precios += $valor_por_pagar;
							}
                            $pasaporte_totales = true;
                        }
                    }
                    if ($e >= (count($rta_pacientes_aux) - 1) && $pasaporte_totales) {
                        $pdf->SetAligns2(array('L', 'R', 'R', 'R'));
						$pdf->SetWidths2(array(163, 16, 8, 18));
						$pdf->SetFont('Arial', 'B', 7);
                        $pdf->Row2(array('', 'Total', $contador_cantidades, '$'.number_format($contador_precios)));
						$pdf->SetFont('Arial', '', 7);
                        $pdf->Ln(2);
						
						//Se acumula en el total por tipo de pago
						if (isset($arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]])) {
							$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["cantidad"] += $contador_cantidades;
							$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["valor"] += $contador_precios;
						} else {
							$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["nombre"] = $rta_tipos_pagos_aux[$a]["nombre"];
							$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["cantidad"] = $contador_cantidades;
							$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["valor"] = $contador_precios;
						}
                    }
                }
            }
			
			//Total por tipo de pago
			if (isset($arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]])) {
				$pdf->SetAligns2(array('L', 'R', 'R'));
				$pdf->SetWidths2(array(133, 54, 18));
				$pdf->SetFont('Arial', 'B', 7);
				$pdf->Row2(array('', ajustarCaracteres('Total '.$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["nombre"]), '$'.number_format($arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["valor"])));
				$pdf->SetFont('Arial', '', 7);
				$pdf->Ln(2);
			}
        }
		
		//Se agregan los subtotales
		$pdf->SetFont('Arial', '', 7);
		$pdf->SetAligns2(array('L', 'L', 'R'));
		$pdf->SetWidths2(array(71, 45, 40));
		$total_aux = 0;
		foreach ($arr_totales_fp as $total_fp_aux) {
			$pdf->Row2(array("", "Subtotal ".$total_fp_aux["nombre"], "$".number_format($total_fp_aux["valor"])));
			$total_aux += $total_fp_aux["valor"];
		}
		$pdf->SetFont('Arial', 'B', 7);
		$pdf->Row2(array("", "TOTAL", "$".number_format($total_aux)));
		
		//Se agrega una nueva página con los pagos que presentan diferencias con su valor base
		if (count($lista_cambios_precios) > 0) {
	        $pdf->AddPage();
            $pdf->SetAligns2(array('L'));
			$pdf->SetWidths2(array(100));
			$pdf->SetFont('Arial', 'B', 7);
			$pdf->Row2(array("VALORES BASE MODIFICADOS"));
			
			$pdf->SetAligns2(array('C', 'C', 'C', 'C', 'R', 'R', 'C'));
			$pdf->SetWidths2(array(11, 40, 16, 45, 16, 16, 60));
			$pdf->Row2(array('Recibo', 'Nombre completo', 'Documento', 'Convenio', 'Valor base', 'Valor pago', 'Observaciones'));
			$pdf->SetFont('Arial', '', 7);
			
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
				
				$pdf->SetAligns2(array('R', 'L', 'C', 'L', 'R', 'R', 'L'));
				$pdf->SetWidths2(array(11, 40, 16, 45, 16, 16, 60));
				$pdf->Row2(array(
					$precio_aux["id_pago"],
					ajustarCaracteres(mb_strtoupper($funcionesPersona->obtenerNombreCompleto($precio_aux['apellido_1'], $precio_aux['apellido_2'], $precio_aux['nombre_1'], $precio_aux['nombre_2']), "UTF-8")),
					$precio_aux['numero_documento'],
					ajustarCaracteres($precio_aux['nombre_convenio']),
					'$'.number_format($valor_base_aux),
					'$'.number_format($precio_aux['valor']),
					$precio_aux['observaciones_pago']));
			}
		}
		
		$pdf->Ln();

        $pdf->Output();
        //Se guarda el documento pdf
        $nombreArchivo = "../tmp/reporte_tesoreria_" . $_SESSION["idUsuario"] . ".pdf";
        $pdf->Output($nombreArchivo, "F");
	?>
	<input type="hidden" name="hdd_archivo_pdf" id="hdd_archivo_pdf" value="<?php echo($nombreArchivo); ?>" />
	<?php
	//Fin del limitante
	}
        break;
		
    case "2": //Genera reporte estadístico por paciente
		require_once("../funciones/pdf/fpdf.php");
		require_once("../funciones/pdf/makefont/makefont.php");
		require_once("../funciones/pdf/funciones.php");
		require_once("../db/DbPagos.php");
		require_once("../db/DbTiposPago.php");
		require_once("../db/DbPacientes.php");
		
		$pdf = new FPDF('P', 'mm', 'Letter');
		$pdf->SetMargins(5, 5, 5);
		
		$dbPacientes = new DbPacientes();
		$dbPagos = new DbPagos();
		$dbTiposPagos = new DbTiposPago();
		$funcionesPersona = new FuncionesPersona();
		
		$fechaInicial = $utilidades->str_decode($_POST['fechaInicial']);
		$fechaFinal = $utilidades->str_decode($_POST['fechaFinal']);
		$idPaciente = $utilidades->str_decode($_POST['idPaciente']);
		
		$paciente_obj = $dbPacientes->getExistepaciente3($idPaciente);
		$rta_tipos_pagos_aux = $dbTiposPagos->getListaTiposPagoNP();
		$rta_pacientes_aux = $dbPagos->reporteTesoseriaPaciente($fechaInicial, $fechaFinal, $idPaciente);
		
        $pdf->header = 2; //Selecciona el header
        $pdf->bordeMulticell = 2; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde
        $pdf->pie_pagina = true;
        
        $pdf->AddPage();
		
        $pdf->SetFont('Arial', '', 7);
        srand(microtime() * 1000000);
		
		$nombre_paciente = ajustarCaracteres(mb_strtoupper($funcionesPersona->obtenerNombreCompleto($paciente_obj["nombre_1"], $paciente_obj["nombre_2"], $paciente_obj["apellido_1"], $paciente_obj["apellido_2"]), "UTF-8"));
		$pdf->SetAligns2(array('L'));
		$pdf->SetWidths2(array(100));
		$pdf->SetFont('Arial', 'B', 7);
		$pdf->Row2(array('Paciente: '.$nombre_paciente));
		$pdf->SetFont('Arial', '', 7);
		
		if ($fechaInicial != "") {
			$arr_aux = explode("-", $fechaInicial);
			$pdf->SetAligns2(array('L'));
			$pdf->SetWidths2(array(100));
			$pdf->Row2(array('Fecha inicial: '.$arr_aux[2]."/".$arr_aux[1]."/".$arr_aux[0]));
		}
		if ($fechaFinal != "") {
			$arr_aux = explode("-", $fechaFinal);
			$pdf->SetAligns2(array('L'));
			$pdf->SetWidths2(array(100));
			$pdf->Row2(array('Fecha final: '.$arr_aux[2]."/".$arr_aux[1]."/".$arr_aux[0]));
		}
		$pdf->Ln(5);
		
		$arr_totales_fp = array();
        for ($a = 0; $a < count($rta_tipos_pagos_aux); $a++) {
            $pasaporte_titulo_tpago = 0;
            $valor_por_pagar = 0;
			
			$pasaporte_titulo_cups = 0;
			$pasaporte_totales = false;
			$contador_cantidades = 0;
			$contador_precios = 0;
			$pasaporte_cantidades = 0;
			
			for ($e = 0; $e < count($rta_pacientes_aux); $e++) {
				if ($pasaporte_titulo_cups == 0) {
					$pasaporte_titulo_cups = 1;
				}
				
				$n1 = 0;
				if ($rta_tipos_pagos_aux[$a]['id'] == $rta_pacientes_aux[$e]['id_medio_pago']
						|| ($rta_tipos_pagos_aux[$a]["id"] == "99" && trim($rta_pacientes_aux[$e]['id_medio_pago']) == "")) {
					//Si el tipo de pago es igual
					if ($pasaporte_titulo_tpago == 0) {
						$pasaporte_titulo_tpago = 1;
					}
					
					if ($pasaporte_titulo_tpago == 1) {//Imprime el titulo del tipo de pago
						$pdf->SetAligns2(array('L'));
						$pdf->SetWidths2(array(100));
						$pdf->SetFont('Arial', 'B', 7);
						$pdf->Row2(array('FORMA DE PAGO: '.$rta_tipos_pagos_aux[$a]['nombre']));
						$pdf->SetFont('Arial', '', 7);
						$pasaporte_titulo_tpago++;
					}
					
					if ($pasaporte_titulo_cups == 1) {//Imprime el titulo del cups y titulos de nombre completo, documento, precio ,etc...
						$pdf->SetAligns2(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'R', 'R', 'R', 'R'));
						$pdf->SetWidths2(array(15, 11, 16, 37, 15, 12, 43, 16, 16, 8, 16));
						$pdf->Row2(array('Fecha', 'Recibo', ajustarCaracteres('Código'), 'Concepto', 'Factura', 'Entidad', 'Convenio', 'Valor base', 'Valor pago', 'Cant.', 'Valor total'));
						$pdf->SetFont('Arial', '', 7);
						
						$pasaporte_titulo_cups++;
					}
					
					//Calcula el valor por pagar para el tipo de pago
					$valor_por_pagar = 0;
					$abrev_pago = "";
					if ($rta_pacientes_aux[$e]['t_pago'] == $rta_tipos_pagos_aux[$a]['id']) {
						$valor_por_pagar = $rta_pacientes_aux[$e]['valor_pago'];
					} else if ($rta_pacientes_aux[$e]['valor_pago'] > 0) {
						//Se obtiene la abreviatura del tipo de pago
						$tipo_pago_aux = $tiposPago->getTipoPago($rta_pacientes_aux[$e]['id_medio_pago']);
						$abrev_pago = $tipo_pago_aux["abrev_pago"];
					}
					
					$valor_total_aux = intval($rta_pacientes_aux[$e]['total_pago'], 10);
					if ($rta_pacientes_aux[$e]['ind_boleta'] == '1') {
						if ($rta_pacientes_aux[$e]['id_medio_pago'] == '0') {
							$valor_por_pagar = (intval($rta_pacientes_aux[$e]['valor'], 10) - intval($rta_pacientes_aux[$e]['valor_cuota'], 10)) * intval($rta_pacientes_aux[$e]["cantidad"], 10);
						} else if (intval($rta_pacientes_aux[$e]["total_cuota"], 10) > 0) {
							$valor_por_pagar = intval($rta_pacientes_aux[$e]['valor_cuota'], 10) * $rta_pacientes_aux[$e]["cantidad"] * $rta_pacientes_aux[$e]["valor_pago"] / $rta_pacientes_aux[$e]["total_cuota"];
						} else {
							$valor_por_pagar = 0;
						}
					} else {
						if ($valor_total_aux > 0) {
							$valor_por_pagar *= (($rta_pacientes_aux[$e]["valor"] * $rta_pacientes_aux[$e]["cantidad"]) / $valor_total_aux);
						}
					}
					
					$valor_base_aux = $rta_pacientes_aux[$e]["valor_b"];
					
					if ($valor_por_pagar > 0 || $rta_pacientes_aux[$e]['ind_boleta'] == "0") {
						$pasaporte_cantidades++;
						$pdf->SetAligns2(array('C', 'R', 'C', 'L', 'C', 'C', 'L', 'R', 'R', 'R', 'R'));
						$pdf->SetWidths2(array(15, 11, 16, 37, 15, 12, 43, 16, 16, 8, 16));
						$pdf->Row2(array(
							$rta_pacientes_aux[$e]["fecha_pago_t"],
							$rta_pacientes_aux[$e]["id_pago"],
							$rta_pacientes_aux[$e]['cod_insumo'],
							ajustarCaracteres($rta_pacientes_aux[$e]['nombre_insumo']),
							$rta_pacientes_aux[$e]['num_factura'],
							$rta_pacientes_aux[$e]['nombre_entidad'],
							ajustarCaracteres($rta_pacientes_aux[$e]['nombre_convenio']." - ".$rta_pacientes_aux[$e]['nombre_plan']),
							'$'.number_format($valor_base_aux),
							'$'.number_format($valor_por_pagar / $rta_pacientes_aux[$e]['cantidad']),
							$rta_pacientes_aux[$e]['cantidad'],
							'$'.number_format($valor_por_pagar)));
						
						$contador_cantidades += $rta_pacientes_aux[$e]['cantidad'];
						$contador_precios += $valor_por_pagar;
					}
					$pasaporte_totales = true;
				}
				
				if ($e >= (count($rta_pacientes_aux) - 1) && $pasaporte_totales) {
					$pdf->SetAligns2(array('L', 'R', 'R', 'R'));
					$pdf->SetWidths2(array(163, 16, 8, 18));
					$pdf->SetFont('Arial', 'B', 7);
					$pdf->Row2(array('', 'Total', $contador_cantidades, '$'.number_format($contador_precios)));
					$pdf->SetFont('Arial', '', 7);
					$pdf->Ln(2);
					
					//Se acumula en el total por tipo de pago
					if (isset($arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]])) {
						$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["cantidad"] += $contador_cantidades;
						$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["valor"] += $contador_precios;
					} else {
						$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["nombre"] = $rta_tipos_pagos_aux[$a]["nombre"];
						$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["cantidad"] = $contador_cantidades;
						$arr_totales_fp[$rta_tipos_pagos_aux[$a]["id"]]["valor"] = $contador_precios;
					}
				}
			}
        }
		
		//Se agregan los subtotales
		$pdf->SetFont('Arial', '', 7);
		$pdf->SetAligns2(array('L', 'L', 'R'));
		$pdf->SetWidths2(array(71, 45, 40));
		$total_aux = 0;
		foreach ($arr_totales_fp as $total_fp_aux) {
			$pdf->Row2(array("", "Subtotal ".$total_fp_aux["nombre"], "$".number_format($total_fp_aux["valor"])));
			$total_aux += $total_fp_aux["valor"];
		}
		$pdf->SetFont('Arial', 'B', 7);
		$pdf->Row2(array("", "TOTAL", "$".number_format($total_aux)));
		
		$pdf->Ln();

        $pdf->Output();
		
		//Se guarda el documento pdf
		$nombreArchivo = "../tmp/reporte_tesoreria_estadistico_paciente_" . $_SESSION["idUsuario"] . ".pdf";
		$pdf->Output($nombreArchivo, "F");
	?>
	<input type="hidden" name="hdd_archivo_pdf2" id="hdd_archivo_pdf2" value="<?php echo($nombreArchivo); ?>" />
	<?php
        break;
		
    case "3": //Imprime ventana flotante de pacientes
        $hddPacientes = $_POST['hddPacientes'];

        $pacientes = explode("--", $hddPacientes);
        ?>
        <div class="encabezado">
            <h3>Pacientes</h3>
        </div>
        <h6 style="text-align: left;">Se han encontrado <?php echo count($pacientes); ?> resultados</h6>
        <table class="paginated modal_table" style="width: 99%; margin: auto;">
            <thead>
                <tr>
                    <th style="width:5%;">N&uacute;mero de documento</th>
                    <th style="width:36%;">Nombre completo</th>
                </tr>
            </thead>
            <?php
				for ($e = 0; $e <= (count($pacientes) - 1); $e++) {
	
					$paciente = explode("||", $pacientes[$e]);
					?>
					<tr onclick="reporteEstadisticopaciente2('<?php echo $paciente[2]; ?>')">
						<td style="text-align: left;"><?php echo $paciente[2]; ?></td>
						<td style="text-align: left;"><?php echo $paciente[1]; ?></td>
					</tr>
					<?php
				}
            ?>
        </table>
        <script id='ajax'>
            //<![CDATA[ 
            $(function() {
                $('.paginated', 'table').each(function(i) {
                    $(this).text(i + 1);
                });
				
                $('table.paginated').each(function() {
                    var currentPage = 0;
                    var numPerPage = 10;
                    var $table = $(this);
                    $table.bind('repaginate', function() {
                        $table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
                    });
                    $table.trigger('repaginate');
                    var numRows = $table.find('tbody tr').length;
                    var numPages = Math.ceil(numRows / numPerPage);
                    var $pager = $('<div class="pager"></div>');
                    for (var page = 0; page < numPages; page++) {
                        $('<span class="page-number"></span>').text(page + 1).bind('click', {
                            newPage: page
                        }, function(event) {
                            currentPage = event.data['newPage'];
                            $table.trigger('repaginate');
                            $(this).addClass('active').siblings().removeClass('active');
                        }).appendTo($pager).addClass('clickable');
                    }
                    $pager.insertBefore($table).find('span.page-number:first').addClass('active');
                });
            });
            //]]>
        </script>
        <?php
        break;
		
    case "4": //procesa el excel de Reporte estadístico por paciente
        break;
        
    case "5"://Imprime la ventana flotante para generar el excel de Reporte estadístico por paciente
        $hddPacientes = $_POST['hddPacientes'];
		
        $pacientes = explode("--", $hddPacientes);
        ?>
        <div class="encabezado">
            <h3>Pacientes</h3>
        </div>
        <h6 style="text-align: left;">Se han encontrado <?php echo count($pacientes); ?> resultados</h6>
        <table class="paginated modal_table" style="width: 99%; margin: auto;">
            <thead>
                <tr>
                    <th style="width:5%;">N&uacute;mero de documento</th>
                    <th style="width:36%;">Nombre completo</th>
                </tr>
            </thead>
            <?php
            for ($e = 0; $e <= (count($pacientes) - 1); $e++) {

                $paciente = explode("||", $pacientes[$e]);
                ?>
                <tr onclick="generar_excel2('<?php echo $paciente[0]; ?>')">
                    <td style="text-align: left;"><?php echo $paciente[2]; ?></td>
                    <td style="text-align: left;"><?php echo $paciente[1]; ?></td>
                </tr>
                <?php
            }
            ?>
        </table>
        <script id='ajax'>
            //<![CDATA[ 
            $(function() {
                $('.paginated', 'table').each(function(i) {
                    $(this).text(i + 1);
                });

                $('table.paginated').each(function() {
                    var currentPage = 0;
                    var numPerPage = 10;
                    var $table = $(this);
                    $table.bind('repaginate', function() {
                        $table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
                    });
                    $table.trigger('repaginate');
                    var numRows = $table.find('tbody tr').length;
                    var numPages = Math.ceil(numRows / numPerPage);
                    var $pager = $('<div class="pager"></div>');
                    for (var page = 0; page < numPages; page++) {
                        $('<span class="page-number"></span>').text(page + 1).bind('click', {
                            newPage: page
                        }, function(event) {
                            currentPage = event.data['newPage'];
                            $table.trigger('repaginate');
                            $(this).addClass('active').siblings().removeClass('active');
                        }).appendTo($pager).addClass('clickable');
                    }
                    $pager.insertBefore($table).find('span.page-number:first').addClass('active');
                });
            });
            //]]>
        </script>
        <?php
        break;
		
	case "6": //Búsqueda de procedimientos
		?>
        <div class="encabezado">
            <h3>Buscar Conceptos</h3>
        </div>
        <div>
            <table>
                <tr>
                    <td id="advertenciasg">
                        <div class='contenedor_error' id='contenedor_error'></div>
                    </td>
                </tr>
            </table>
            <table style="width: 100%;">
                <tr>
                    <td>
                        <input type="text" id="txp_procedimiento_b" name="txp_procedimiento_b" placeholder="C&oacute;digo o nombre del procedimiento, medicamento o insumo" onblur="trim_cadena(this);" />
                    </td>
                    <td style="width: 8%;">
                        <input type="button" id="btnBuscar" nombre="btn_buscar" value="Buscar" class="btnPrincipal peq" onclick="buscar_procedimientos();" />
                    </td>
                </tr>
            </table>
            <div id="d_buscar_procedimientos"></div>
        </div>    
        <?php
		break;
		
	case "7": //Resultados de búsqueda de procedimientos
        @$texto_b = trim($utilidades->str_decode($_POST['texto_b']));
		
        require_once("../db/DbMaestroProcedimientos.php");
		$dbMaestroProcedimientos = new DbMaestroProcedimientos();
		
		$lista_procedimientos = $dbMaestroProcedimientos->getListaConceptos($texto_b);
        ?>
        <table class="paginated modal_table" style="width:99%; margin:auto;">
            <thead>
                <tr>
                    <th style="width:15%;">C&oacute;digo</th>
                    <th style="width:85%;">Nombre</th>
                </tr>
            </thead>
            <?php
				if (count($lista_procedimientos) > 0) {
					foreach ($lista_procedimientos as $proc_aux) {
			?>
            <tr onclick="seleccionar_procedimiento('<?php echo($proc_aux["cod_insumo"]); ?>', '<?php echo($proc_aux["nombre_insumo"]); ?>', '<?php echo($proc_aux["tipo_precio"]); ?>');">
            	<td align="center"><?php echo($proc_aux['cod_insumo']); ?></td>
            	<td align="left"><?php echo($proc_aux['nombre_insumo']); ?></td>
            </tr>
            <?php
					}
				} else {
			?>
            <td colspan="2">No se encontraron resultados</td>
            <?php
				}
            ?>
        </table>
        <script id='ajax'>
            //<![CDATA[ 
            $(function() {
                $('.paginated', 'table').each(function(i) {
                    $(this).text(i + 1);
                });

                $('table.paginated').each(function() {
                    var currentPage = 0;
                    var numPerPage = 10;
                    var $table = $(this);
                    $table.bind('repaginate', function() {
                        $table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
                    });
                    $table.trigger('repaginate');
                    var numRows = $table.find('tbody tr').length;
                    var numPages = Math.ceil(numRows / numPerPage);
                    var $pager = $('<div class="pager"></div>');
                    for (var page = 0; page < numPages; page++) {
                        $('<span class="page-number"></span>').text(page + 1).bind('click', {
                            newPage: page
                        }, function(event) {
                            currentPage = event.data['newPage'];
                            $table.trigger('repaginate');
                            $(this).addClass('active').siblings().removeClass('active');
                        }).appendTo($pager).addClass('clickable');
                    }
                    $pager.insertBefore($table).find('span.page-number:first').addClass('active');
                });
            });
            //]]>
        </script>
        <?php
		break;
		
	case "8": //Búsqueda de pacientes
		?>
        <div class="encabezado">
            <h3>Buscar Paciente</h3>
        </div>
        <div>
            <table>
                <tr>
                    <td id="advertenciasg">
                        <div class='contenedor_error' id='contenedor_error'></div>
                    </td>
                </tr>
            </table>
            <table style="width: 100%;">
                <tr>
                    <td>
                        <input type="text" id="txp_paciente_b" name="txp_paciente_b" placeholder="Nombre o n&uacute;mero de documento del paciente" onblur="trim_cadena(this);" />
                    </td>
                    <td style="width: 8%;">
                        <input type="button" id="btnBuscar" nombre="btn_buscar" value="Buscar" class="btnPrincipal peq" onclick="buscar_paciente();" />
                    </td>
                </tr>
            </table>
            <div id="d_buscar_pacientes"></div>
        </div>    
        <?php
		break;
		
	case "9": //Resultados de búsqueda de pacientes
        @$texto_b = trim($utilidades->str_decode($_POST['texto_b']));
		
        require_once("../db/DbPacientes.php");
		$dbPacientes = new DbPacientes();
		
		$funcionesPersona = new FuncionesPersona();
		
		$lista_pacientes = $dbPacientes->getBuscarpacientes($texto_b);
        ?>
        <table class="paginated modal_table" style="width:99%; margin:auto;">
            <thead>
                <tr>
                    <th style="width:25%;">N&uacute;mero documento</th>
                    <th style="width:75%;">Nombre</th>
                </tr>
            </thead>
            <?php
				if (count($lista_pacientes) > 0) {
					foreach ($lista_pacientes as $paciente_aux) {
						$nombre_completo_aux = $funcionesPersona->obtenerNombreCompleto($paciente_aux["nombre_1"], $paciente_aux["nombre_2"], $paciente_aux["apellido_1"], $paciente_aux["apellido_2"]);
			?>
            <tr onclick="seleccionar_paciente('<?php echo($paciente_aux["id_paciente"]); ?>', '<?php echo($nombre_completo_aux); ?>');">
            	<td align="center"><?php echo($paciente_aux['cod_tipo_documento']." ".$paciente_aux["numero_documento"]); ?></td>
            	<td align="left"><?php echo($nombre_completo_aux); ?></td>
            </tr>
            <?php
					}
				} else {
			?>
            <td colspan="2">No se encontraron resultados</td>
            <?php
				}
            ?>
        </table>
        <script id='ajax'>
            //<![CDATA[ 
            $(function() {
                $('.paginated', 'table').each(function(i) {
                    $(this).text(i + 1);
                });

                $('table.paginated').each(function() {
                    var currentPage = 0;
                    var numPerPage = 10;
                    var $table = $(this);
                    $table.bind('repaginate', function() {
                        $table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
                    });
                    $table.trigger('repaginate');
                    var numRows = $table.find('tbody tr').length;
                    var numPages = Math.ceil(numRows / numPerPage);
                    var $pager = $('<div class="pager"></div>');
                    for (var page = 0; page < numPages; page++) {
                        $('<span class="page-number"></span>').text(page + 1).bind('click', {
                            newPage: page
                        }, function(event) {
                            currentPage = event.data['newPage'];
                            $table.trigger('repaginate');
                            $(this).addClass('active').siblings().removeClass('active');
                        }).appendTo($pager).addClass('clickable');
                    }
                    $pager.insertBefore($table).find('span.page-number:first').addClass('active');
                });
            });
            //]]>
        </script>
        <?php
		break;
		
	case "10": //Carga de combo de planes
		require_once("../db/DbPlanes.php");
		require_once("../funciones/Class_Combo_Box.php");
		
		$dbPlanes = new DbPlanes();
		$combo = new Combo_Box();
		
		@$id_convenio = trim($utilidades->str_decode($_POST["id_convenio"]));
		
		//Se carga el listado de planes asociados al convenio
		$lista_planes = $dbPlanes->getListaPlanesActivos($id_convenio);
		$combo->getComboDb("cmbPlan", '', $lista_planes, "id_plan, nombre_plan", "Todos los planes", "", "", "width:250px;");
		break;
}
?>
