<?php
	header("Content-Type: text/xml; charset=UTF-8");
	session_start();
	
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../db/DbCitas.php");
	
	$utilidades = new Utilidades();
	$funcionesPersona = new FuncionesPersona();
	$citas = new DbCitas();
	
	@$opcion = $utilidades->str_decode($_POST["opcion"]);
	
	switch ($opcion) {
		case "1": //Genera reporte para Colpatria
			require_once("../funciones/pdf/fpdf.php");
			require_once("../funciones/pdf/makefont/makefont.php");
			require_once("../funciones/pdf/funciones.php");
			require_once("../db/DbConvenios.php");
			require_once("../db/DbPagos.php");
			
			$dbConvenios = new DbConvenios();
			$dbPagos = new DbPagos();
			
			$pdf = new FPDF("P", "mm", "Letter");
			
			@$fecha_ini = $utilidades->str_decode($_POST["fecha_ini"]);
			@$fecha_fin = $utilidades->str_decode($_POST["fecha_fin"]);
			@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
			
			//Se obtienen los datos del convenio y de los pagos
			$convenio_obj = $dbConvenios->getConvenio($id_convenio);
			$lista_pagos = $dbPagos->getListaPagosConsultasConvenioFechas($fecha_ini, $fecha_fin, $id_convenio);
			
			$pdf->bordeMulticell = 1; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde
			
			$pdf->AddPage();
			
			srand(microtime() * 1000000);
			
			$texto_documento = "";
			if ($convenio_obj["tipo_documento"] != "") {
				$texto_documento = strtoupper($convenio_obj["tipo_documento"]);
			}
			if ($convenio_obj["numero_documento"] != "") {
				$texto_documento .= " ".$convenio_obj["numero_documento"];
			}
			
			$pdf->Ln(5);
			$pdf->SetFont("Arial", "B", 10);
			$pdf->Cell(0, 0, ajustarCaracteres($texto_documento), 0, 0, "C");
			$pdf->Ln(5);
			$pdf->Cell(0, 0, ajustarCaracteres($convenio_obj["nombre_convenio"]), 0, 0, "C");
			$pdf->Ln(10);
			
			$pdf->SetAligns2(array("L"));
			$pdf->SetWidths2(array(195));
			$pdf->Row2(array("CONSULTAS"));
			$pdf->SetAligns2(array("C"));
			$pdf->SetWidths2(array(195));
			$pdf->Row2(array($fecha_ini." - ".$fecha_fin));
			$pdf->SetAligns2(array("C", "C", "C", "C", "C"));
			$pdf->SetWidths2(array(60, 35, 30, 20, 50));
			$pdf->Row2(array("Nombre", "No. Carnet", "Fecha", "1Vez", ajustarCaracteres("Diagnóstico")));
			$pdf->SetFont("Arial", "", 8);
			
			for ($i = 0; $i < count($lista_pagos); $i++) {
				$pago_aux = $lista_pagos[$i];
				
				//Se verifica si hay consultas anteriores para el paciente
				$reg_aux = $dbPagos->getCantidadPagosConsultasAnteriores($id_convenio, $pago_aux["id_paciente"], $pago_aux["fecha_pago"]);
				$marca_1vez = "X";
				if (isset($reg_aux["cantidad"]) && $reg_aux["cantidad"] != "0") {
					$marca_1vez = "";
				}
				
				$pdf->SetAligns2(array("L", "L", "C", "C", "L"));
				$pdf->SetWidths2(array(60, 35, 30, 20, 50));
				$pdf->Row2(array(ajustarCaracteres($funcionesPersona->obtenerNombreCompleto($pago_aux["nombre_1"], $pago_aux["nombre_2"], $pago_aux["apellido_1"], $pago_aux["apellido_2"])), $pago_aux["num_carnet"], $pago_aux["fecha_pago_t"], $marca_1vez, ajustarCaracteres($pago_aux["nombre_ciex"])));
			}
			
			$pdf->Ln();
	
			$pdf->Output();
			//Se guarda el documento pdf
			$nombreArchivo = "../tmp/reporte_colpatria_".$_SESSION["idUsuario"].".pdf";
			$pdf->Output($nombreArchivo, "F");
		?>
		<input type="hidden" name="hdd_archivo_pdf" id="hdd_archivo_pdf" value="<?php echo($nombreArchivo); ?>" />
		<?php
			break;
			
		case "2": //Genera reporte para Colmédica
			require_once("../funciones/pdf/fpdf.php");
			require_once("../funciones/pdf/makefont/makefont.php");
			require_once("../funciones/pdf/funciones.php");
			require_once("../db/DbConvenios.php");
			require_once("../db/DbPagos.php");
			
			$dbConvenios = new DbConvenios();
			$dbPagos = new DbPagos();
			
			$pdf = new FPDF("P", "mm", "Letter");
			
			@$fecha_ini = $utilidades->str_decode($_POST["fecha_ini"]);
			@$fecha_fin = $utilidades->str_decode($_POST["fecha_fin"]);
			@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
			
			//Se obtienen los datos del convenio y de los pagos
			$convenio_obj = $dbConvenios->getConvenio($id_convenio);
			$lista_pagos = $dbPagos->getListaPagosConsultasConvenioFechas($fecha_ini, $fecha_fin, $id_convenio);
			
			$pdf->bordeMulticell = 2; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde
			
			$pdf->AddPage();
			
			srand(microtime() * 1000000);
			
			$pdf->SetFont("Arial", "", 8);
			
			for ($i = 0; $i < count($lista_pagos); $i++) {
				$pago_aux = $lista_pagos[$i];
				
				$pdf->SetAligns2(array("L", "L", "L", "L"));
				$pdf->SetWidths2(array(15, 100, 20, 60));
				$pdf->Row2(array("Nombre:", ajustarCaracteres($funcionesPersona->obtenerNombreCompleto($pago_aux["nombre_1"], $pago_aux["nombre_2"], $pago_aux["apellido_1"], $pago_aux["apellido_2"])), ajustarCaracteres("Identificación:"), $pago_aux["cod_tipo_documento"]." ".$pago_aux["numero_documento"]));
				$y_aux = $pdf->GetY();
				$pdf->Line(25, $y_aux, 125, $y_aux);
				$pdf->Line(145, $y_aux, 205, $y_aux);
				
				$lugar_nac_aux = $pago_aux["nom_mun_n"];
				if ($lugar_nac_aux == "") {
					$lugar_nac_aux = $pago_aux["nom_mun_nac"];
				}
				$edad_aux = "";
				if ($pago_aux["edad"] != "") {
					$arr_edad = explode("/", $pago_aux["edad"]);
					if (count($arr_edad) == 2) {
						$edad_aux = $arr_edad[0]." ";
						switch ($arr_edad[1]) {
							case "1":
								$edad_aux .= "A";
								break;
							case "2":
								$edad_aux .= "M";
								break;
							case "3":
								$edad_aux .= "D";
								break;
						}
					}
				}
				$pdf->SetAligns2(array("L", "L", "L", "L", "L", "L"));
				$pdf->SetWidths2(array(40, 75, 10, 10, 20, 40));
				$pdf->Row2(array("Fecha y Lugar de Nacimiento:", ajustarCaracteres($pago_aux["fecha_nacimiento_t"]." ".$lugar_nac_aux), "Edad:", $edad_aux, "Estado Civil:", $pago_aux["estado_civil"]));
				$y_aux = $pdf->GetY();
				$pdf->Line(50, $y_aux, 125, $y_aux);
				$pdf->Line(135, $y_aux, 145, $y_aux);
				$pdf->Line(165, $y_aux, 205, $y_aux);
				
				$pdf->SetAligns2(array("L", "L", "L", "L", "L", "L"));
				$pdf->SetWidths2(array(15, 50, 12, 52, 25, 40));
				$pdf->Row2(array("Empresa:", ajustarCaracteres($convenio_obj["nombre_convenio"]), ajustarCaracteres("Póliza:"), "", "Tipo de Usuario:", ""));
				$y_aux = $pdf->GetY();
				$pdf->Line(25, $y_aux, 75, $y_aux);
				$pdf->Line(87, $y_aux, 140, $y_aux);
				$pdf->Line(165, $y_aux, 205, $y_aux);
				
				$pdf->SetAligns2(array("L", "L", "L", "L", "L", "L"));
				$pdf->SetWidths2(array(15, 60, 16, 64, 12, 28));
				$pdf->Row2(array(ajustarCaracteres("Profesión:"), ajustarCaracteres($pago_aux["profesion"]), "Acudiente:", ajustarCaracteres($pago_aux["nombre_acompa"]), "Fecha:", $pago_aux["fecha_pago_t"]));
				$y_aux = $pdf->GetY();
				$pdf->Line(25, $y_aux, 85, $y_aux);
				$pdf->Line(101, $y_aux, 165, $y_aux);
				$pdf->Line(177, $y_aux, 205, $y_aux);
				
				$pdf->SetAligns2(array("L", "L", "L", "L"));
				$pdf->SetWidths2(array(15, 100, 15, 65));
				$pdf->Row2(array(ajustarCaracteres("Dirección:"), ajustarCaracteres($pago_aux["direccion"]), ajustarCaracteres("Teléfono:"), $pago_aux["telefono_1"]));
				$y_aux = $pdf->GetY();
				$pdf->Line(25, $y_aux, 125, $y_aux);
				$pdf->Line(140, $y_aux, 205, $y_aux);
				
				$pdf->SetAligns2(array("L", "L", "L", "L"));
				$pdf->SetWidths2(array(15, 100, 20, 60));
				$pdf->Row2(array(ajustarCaracteres("Diagnóst:"), ajustarCaracteres($pago_aux["nombre_ciex"]), ajustarCaracteres("No. Autoriz:"), $pago_aux["num_autorizacion"]));
				$y_aux = $pdf->GetY();
				$pdf->Line(25, $y_aux, 125, $y_aux);
				$pdf->Line(140, $y_aux, 205, $y_aux);
				
				//Se inserta una nueva página cada 6 registros
				if ($i % 6 == 5) {
					$pdf->AddPage();
				} else {
					$pdf->Ln(10);
				}
			}
			
			$pdf->Ln();
	
			$pdf->Output();
			//Se guarda el documento pdf
			$nombreArchivo = "../tmp/reporte_colmedica_".$_SESSION["idUsuario"].".pdf";
			$pdf->Output($nombreArchivo, "F");
		?>
		<input type="hidden" name="hdd_archivo_pdf" id="hdd_archivo_pdf" value="<?php echo($nombreArchivo); ?>" />
		<?php
			break;
			
			//obtener los planes 
		case "3":
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
