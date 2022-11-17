<?php
	session_start();
	/*
	  Pagina para la generación de pdf de fórmula de gafas
	  Autor: Feisar Moreno - 03/02/2016
	 */
	header('Content-Type: text/xml; charset=UTF-8');
	
	require_once("../db/DbAdmision.php");
	require_once("../db/DbPacientes.php");
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbHistoriaClinica.php");
	require_once("../funciones/Utilidades.php");
	require_once("../db/Dbvariables.php");
	require_once("../db/DbRemisiones.php");
	require_once("../db/DbFormulasMedicamentos.php");
	require_once("../db/DbOrdenesMedicas.php");
	require_once("../db/DbPaquetesProcedimientos.php");
	
	require_once("../funciones/pdf/fpdf.php");
	require_once("../funciones/pdf/makefont/makefont.php");
	require_once("../funciones/pdf/funciones.php");
	require_once("../funciones/pdf/WriteHTML.php");
	
	$dbAdmision = new DbAdmision();
	$dbPacientes = new DbPacientes();
	$dbUsuarios = new DbUsuarios();
	$dbHistoriaClinica = new DbHistoriaClinica();
	$utilidades = new Utilidades();
	$dbvariables = new Dbvariables();
	$dbRemisiones = new DbRemisiones();
	$dbformulasMedicamentos = new DbFormulasMedicamentos();
	$dbOrdenesMedicas = new DbOrdenesMedicas();
	$dbPaquetesProcedimientos = new DbPaquetesProcedimientos();
	
	$opcion = $utilidades->str_decode($_POST["opcion"]);
	
	switch ($opcion) {
		case "1": //ORDEN MÉDICA REMISIÓN           
			@$idPaciente = $utilidades->str_decode($_POST["idPaciente"]);
			@$idHc = $utilidades->str_decode($_POST["idHc"]);
			@$idRemision = $utilidades->str_decode($_POST["idRemision"]);
			
			$paciente = $dbPacientes->getPaciente($idPaciente);
			
			$remision = $dbRemisiones->getRemisionById($idRemision);
			$historia = $dbHistoriaClinica->getHistoriaClinicaId($idHc);
			
			$nombre_usuario_alt = "";
			if ($remision["ind_anonimo"] == "1" && $historia["nombre_usuario_alt"] != "") {
				$nombre_usuario_alt = $historia["nombre_usuario_alt"];
			} else {
				$nombre_usuario_alt = $remision["nombre_usuario"] . " " . $remision["apellido_usuario"];
			}
			
			$fontSize = 7;
			$pdf = new FPDF('P', 'mm', array(216, 279));
			$pdf->AliasNbPages();
			$pdfHTML = new PDF_HTML();
			$pdf->SetMargins(10, 10, 10);
			$pdf->SetAutoPageBreak(false);
			$pdf->SetFillColor(255, 255, 255);
			
			$pdf->bordeMulticell = 0; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde
			$pdf->pie_pagina = false;
			
			$pdf->AddPage();
			
			$pdf->SetFont("Arial", "", $fontSize);
			srand(microtime() * 1000000);
			
			$yAux = 8;
			
			//Se obtienen las dimensiones del logo
			$arr_prop_imagen = getimagesize($remision["dir_logo_sede_det"]);
			$ancho_aux = floatval($arr_prop_imagen[0]);
			$alto_aux = floatval($arr_prop_imagen[1]);
			
			$ancho_max = 40.0;
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
			$pdf->Image($remision["dir_logo_sede_det"], 10 + (40 - $ancho_aux) / 2, 7, $ancho_aux, $alto_aux);
			
			$pdf->Cell(40, 24, "", 0, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->SetY($yAux);
			$pdf->SetX(50);
			$pdf->Cell(35, 4, ajustarCaracteres("FECHA DE LA REMISIÓN:"), 0, 0, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(30, 4, ajustarCaracteres($remision['fechaCreacionAux']), 0, 0, "L");
			
			$pdf->SetY($yAux+=4);
			$pdf->SetX(50);
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(27, 4, ajustarCaracteres("TIPO DE REMISIÓN:"), 0, 0, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(38, 4, ajustarCaracteres($remision['tipoRemisionAux']), 0, 0, "L");
			
			$pdf->SetY(10);
			$pdf->SetX(115);
			$pdf->SetFont("Arial", "B", ($fontSize + 8));
			$pdf->Cell(90, 10, ajustarCaracteres("REMISIÓN MÉDICA"), 0, 0, "C");
			$pdf->SetY(20);
			$pdf->SetX(115);
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(90, 4, ajustarCaracteres("No. ".$remision['id_remision']), 0, 0, "C");
			$pdf->Ln(7);
			
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("DOCUMENTO:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente['numero_documento']), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("NOMBRES:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente['nombre_1'] . " " . $paciente['nombre_2']), 1, 1, "C");
			$pdf->SetX($pdf->GetX());
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("TIPO:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente['codigoDocumento']), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("APELLIDOS:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente['apellido_1'] . " " . $paciente['apellido_2']), 1, 1, "C");
			
			$pdf->SetX(10);
			
			$pdf->SetX($pdf->GetX());
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("SEXO:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(10, 4, ajustarCaracteres($paciente['codigoSexo']), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("FECH.NAC"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres($paciente['fecha_nacimiento_t']), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(10, 4, ajustarCaracteres("EDAD:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			
			$edad = explode('/', $paciente['edad']);
			$edadMagnitud = $edad[1];
			
			switch ($edadMagnitud) {
				case 1:
					$edadMagnitud = "Años";
					break;
				case 2:
					$edadMagnitud = "Meses";
					break;
				case 3:
					$edadMagnitud = "Días";
					break;
			}
			
			$pdf->Cell(16, 4, ajustarCaracteres($edad[0] . " " . $edadMagnitud), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("ASEGURADOR:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($remision['nombreConvenioAux']." / ".$remision['nombrePlanAux']), 1, 1, "C");
			$pdf->Ln(1);
			
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(192, 5, ajustarCaracteres("ORDEN MÉDICA REMISIÓN"), 0, 1, "C");
			
			$pdf->Ln(4);
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->MultiCell(192, 5, $pdfHTML->WriteHTML(ajustarCaracteres($remision['desc_remision']), $pdf), 0, 'J');
			
			$pdf->Ln(4);
			
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(22, 4, ajustarCaracteres("RESPONSABLE:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(74, 4, ajustarCaracteres($utilidades->convertir_a_mayusculas($nombre_usuario_alt)), 1, 0, "L");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(30, 4, ajustarCaracteres($utilidades->convertir_a_mayusculas($remision["tipo_num_reg"]).":"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(66, 4, ajustarCaracteres($remision["num_reg_medico"]), 1, 1, "C");
			$pdf->SetFont("Arial", "I", $fontSize);
			
			$pdf->Ln(4);
			$pdf->SetFont("Arial", "", $fontSize - 1);
			$pdf->Cell(192, 4, ajustarCaracteres($remision['dir_sede_det'] . " - " . $remision['tel_sede_det']), 0, 1, "C");
			$pdf->SetFont("Arial", "I", $fontSize - 1);
			$pdf->Cell(96, 4, ajustarCaracteres("Fecha y hora de impresión: " . date("Y/m/d H:i:s")), 0, 0, "L");
			$pdf->Cell(96, 4, ajustarCaracteres("Página " . $pdf->PageNo() . "/{nb}"), 0, 1, "R");
			
			//Se guarda el documento pdf
			$nombreArchivo = "../tmp/remision_" . $_SESSION["idUsuario"] . ".pdf";
			$pdf->Output($nombreArchivo, "F");
		?>
		<input type="hidden" name="hdd_ruta_remision_pdf" id="hdd_ruta_remision_pdf" value="<?php echo($nombreArchivo); ?>" />
		<?php
			break;
			
		case "2": //ORDEN DE MEDICAMENTOS - FÓRMULA MÉDICA   
			@$idFormulaMedicamento = $utilidades->str_decode($_POST["idFormulaMedicamento"]);
			
			$formulaMedicamento = $dbformulasMedicamentos->getFormulaByiD($idFormulaMedicamento);
			$paciente = $dbPacientes->getPaciente($formulaMedicamento['id_paciente']);
			$formulas = $dbformulasMedicamentos->getMedicamentosActivosByFormula($idFormulaMedicamento);
			
			$diagnosticos = $dbformulasMedicamentos->getDiagnosticosByFormula($idFormulaMedicamento);
			
			$historia = $dbHistoriaClinica->getHistoriaClinicaId($formulaMedicamento['id_hc']);
			$id_prof = "";
			
			//Se configuran los diagnosticos que se van a imprimir en la formula medicamentos			
			$array_diagnosticos=array();
			foreach ($diagnosticos as $k => &$diagnostico) {
				
				$cod_ciex = $diagnostico["cod_ciex"] == "" ? $diagnostico["cod_ciex_2"] : $diagnostico["cod_ciex"];
				$nombre_ciex = $diagnostico["nombre_ciex"] == "" ? $diagnostico["nombre_ciex_2"] : $diagnostico["nombre_ciex"];
				if($cod_ciex<>""){
					$array_diagnosticos[$cod_ciex] = $nombre_ciex;
				}
				
			}
										
			if(isset($historia["id_usuario_crea"])){
				$id_prof = $historia["id_usuario_crea"];
			}
			
			$tipoOrdenMedica = $formulaMedicamento['tipo_formula_medicamento'];
			if ($tipoOrdenMedica == 1) {//Sí es del tipo Directa desde UT
				$nombre_usuario_alt = "";
				if ($formulaMedicamento["ind_anonimo"] == "1" && $historia["nombre_usuario_alt"] != "") {
					$nombre_usuario_alt = $historia["nombre_usuario_alt"];
				} else {
					$nombre_usuario_alt = $formulaMedicamento["nombre_usuario"] . " " . $formulaMedicamento["apellido_usuario"];
				}
			}
			
			
			$direccion = $dbvariables->getVariable(20);
			
			$fontSize = 7;
			$pdf = new FPDF('P', 'mm', array(216, 279));
			$pdf->AliasNbPages();
			$pdfHTML = new PDF_HTML();
			$pdf->SetMargins(10, 10, 10);
			$pdf->SetAutoPageBreak(false);
			$pdf->SetFillColor(255, 255, 255);
			
			$pdf->bordeMulticell = 0; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde
			$pdf->pie_pagina = false;
			
			$pdf->AddPage();
			
			$pdf->SetFont("Arial", "", $fontSize);
			srand(microtime() * 1000000);
			
			//Se obtienen las dimensiones del logo
			$arr_prop_imagen = getimagesize($formulaMedicamento["dir_logo_sede_det"]);
			$ancho_aux = floatval($arr_prop_imagen[0]);
			$alto_aux = floatval($arr_prop_imagen[1]);
			
			$ancho_max = 40.0;
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
			$pdf->Image($formulaMedicamento["dir_logo_sede_det"], 10 + (40 - $ancho_aux) / 2, 7, $ancho_aux, $alto_aux);
			
			$pdf->Cell(40, 24, "", 0, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->SetY(8);
			$pdf->SetX(50);
			$pdf->Cell(40, 4, ajustarCaracteres("FECHA DE LA FORMULACIÓN:"), 0, 0, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(25, 4, ajustarCaracteres($formulaMedicamento['fechaFormulacion']), 0, 0, "L");
			
			$tipoFormulaMedicamento = "Directa";
			switch ($formulaMedicamento['tipo_formula_medicamento']) {
				case 1://1=Directa desde UT
					$tipoFormulaMedicamento = "Directa";
					break;
				case 2://2=Homologada
					$tipoFormulaMedicamento = "Homologada";
					break;
			}
			
			$pdf->SetY(12);
			$pdf->SetX(50);
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(35, 4, ajustarCaracteres("TIPO DE FORMULACIÓN:"), 0, 0, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(30, 4, ajustarCaracteres($tipoFormulaMedicamento), 0, 0, "L");
			
			if ($formulaMedicamento['tipo_formula_medicamento'] == 1) {//Sí es directa desde UT
				$pdf->SetY(16);
				$pdf->SetX(50);
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(15, 4, ajustarCaracteres("SERVICIO:"), 0, 0, "L");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(50, 4, ajustarCaracteres($formulaMedicamento['nombre_tipo_cita']), 0, 0, "L");
				$pdf->SetY(20);
				$pdf->SetX(50);
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(15, 4, ajustarCaracteres("ADMISIÓN:"), 0, 0, "L");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(50, 4, ajustarCaracteres($formulaMedicamento['id_admision']), 0, 0, "L");
			}
			
			$pdf->SetY(10);
			$pdf->SetX(115);
			$pdf->SetFont("Arial", "B", ($fontSize + 8));
			$pdf->Cell(90, 10, ajustarCaracteres("FÓRMULA MÉDICA"), 0, 0, "C");
			$pdf->SetY(20);
			$pdf->SetX(115);
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(90, 4, ajustarCaracteres("No. ".$formulaMedicamento['id_formula_medicamento']), 0, 0, "C");
			$pdf->Ln(7);
			
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("DOCUMENTO:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente['numero_documento']), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("NOMBRES:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente['nombre_1'] . " " . $paciente['nombre_2']), 1, 1, "C");
			$pdf->SetX($pdf->GetX());
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("TIPO:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente['codigoDocumento']), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("APELLIDOS:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente['apellido_1'] . " " . $paciente['apellido_2']), 1, 1, "C");
			
			$pdf->SetX(10);
			
			$pdf->SetX($pdf->GetX());
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("SEXO:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(10, 4, ajustarCaracteres($paciente['codigoSexo']), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("FECH.NAC"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres($paciente['fecha_nacimiento_t']), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(10, 4, ajustarCaracteres("EDAD:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			
			$edad = explode('/', $paciente['edad']);
			$edadMagnitud = $edad[1];
			
			switch ($edadMagnitud) {
				case 1:
					$edadMagnitud = "Años";
					break;
				case 2:
					$edadMagnitud = "Meses";
					break;
				case 3:
					$edadMagnitud = "Días";
					break;
			}
			
			$pdf->Cell(16, 4, ajustarCaracteres($edad[0] . " " . $edadMagnitud), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("ASEGURADOR:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($formulaMedicamento['nombreConvenioAux']." / ".$formulaMedicamento['nombrePlanAux']), 1, 1, "C");
			$pdf->Ln(1);
			
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(192, 5, ajustarCaracteres("MEDICAMENTOS FORMULADOS"), 0, 1, "C");
			
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(10, 4, ajustarCaracteres("CANT.:"), 1, 0, "C");
			$pdf->Cell(86, 4, ajustarCaracteres("MEDICAMENTO:"), 1, 0, "C");
			$pdf->Cell(10, 4, ajustarCaracteres("TIEMPO:"), 1, 0, "C");
			$pdf->Cell(86, 4, ajustarCaracteres("POSOLOGÍA:"), 1, 1, "C");
			
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->bordeMulticell = 1;
			$pdf->h_row2 = 3;
			foreach ($formulas as $formula) {
				//$pdf->Ln(4);
				$posologia = strip_tags($formula['forma_admin']);
				$pdf->SetWidths2(array(10, 86, 10, 86));
				$pdf->SetAligns2(array('C', 'J', 'C', 'J'));
				if($formula['cod_medicamento'] == "70"){//Configuracion temporal
					$pdf->Row2(array(ajustarCaracteres($formula['cantidad_orden']), ajustarCaracteres($formula['nombre_generico'] . " - (" . $formula['presentacion'] . ")".  "- (" . $formula['nombre_comercial'] . ")"), ajustarCaracteres($formula['tiempo_formula_medicamento_det'] . " Días"), ajustarCaracteres($posologia)));
				}else{
					$pdf->Row2(array(ajustarCaracteres($formula['cantidad_orden']), ajustarCaracteres($formula['nombre_comercial'] . " - (" . $formula['presentacion'] . ")"), ajustarCaracteres($formula['tiempo_formula_medicamento_det'] . " Días"), ajustarCaracteres($posologia)));
				}
				
			}
			
			if(count($array_diagnosticos)>0){
				
				$pdf->Ln(1);
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(192, 5, ajustarCaracteres("DIAGNOSTICOS"), 0, 1, "C");
				
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(20, 4, ajustarCaracteres("CODIGO CIEX"), 1, 0, "C");
				$pdf->Cell(172, 4, ajustarCaracteres("DIAGNOSTICO"), 1, 0, "L");
						
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->bordeMulticell = 1;
				$pdf->h_row2 = 3;
				$aux=0;
				foreach ($array_diagnosticos as $k=>$item) {
					if($aux==0){
						$pdf->Ln(4);
					}
					
					$pdf->SetWidths2(array(20, 172));
					$pdf->SetAligns2(array('C', 'L'));
					
						$pdf->Row2(array(ajustarCaracteres($k), ajustarCaracteres($item)));
						$aux++;		
				}
			}
			$pdf->Ln(4);

			if ($tipoOrdenMedica == 1) {//Sí es del tipo Directa desde UT
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(22, 4, ajustarCaracteres("RESPONSABLE:"), 1, 0, "R");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(74, 4, ajustarCaracteres($utilidades->convertir_a_mayusculas($nombre_usuario_alt)), 1, 0, "L");
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(30, 4, ajustarCaracteres("REGISTRO MÉDICO:"), 1, 0, "R");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(66, 4, ajustarCaracteres($formulaMedicamento['num_reg_medico']), 1, 1, "C");
				$pdf->SetFont("Arial", "I", $fontSize);
				imprimir_firma($id_prof,$pdf,$nombre_usuario_alt);
			} else if ($tipoOrdenMedica == 2) {//Homologada
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(192, 4, ajustarCaracteres("FORMULA MÉDICA HOMOLOGADA:"), 1, 1, "C");
				$pdf->Cell(35, 4, ajustarCaracteres("REMITENTE RESPONSABLE"), 1, 0, "C");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(112, 4, ajustarCaracteres($formulaMedicamento['medico_homologacion']), 1, 0, "L");
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(30, 4, ajustarCaracteres("FECHA FORMULACIÓN:"), 1, 0, "C");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(15, 4, ajustarCaracteres($formulaMedicamento['fechaFormulaHomologada']), 1, 1, "C");
				
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->bordeMulticell = 1;
				$pdf->h_row2 = 3;
				$pdf->SetWidths2(array(192));
				$pdf->SetAligns2(array('J'));
				$pdf->Row2(array(ajustarCaracteres($formulaMedicamento['observ_formula_medicamento'])));
			}
			/*foreach ($array_diagnosticos as $k=>$item){
				var_dump($k);
				var_dump($item);
			}*/
			//Diagnosticos asociados a la formula medica.
					
			$pdf->Ln(4);
			
			$pdf->SetFont("Arial", "", $fontSize - 1);
			$pdf->Cell(192, 4, ajustarCaracteres($formulaMedicamento['dir_sede_det'] . " - " . $formulaMedicamento['tel_sede_det']), 0, 1, "C");
			$pdf->SetFont("Arial", "I", $fontSize - 1);
			$pdf->Cell(96, 4, ajustarCaracteres("Fecha y hora de impresión: " . date("Y/m/d H:i:s")), 0, 0, "L");
			$pdf->Cell(96, 4, ajustarCaracteres("Página " . $pdf->PageNo() . "/{nb}"), 0, 1, "R");
			
			//Se guarda el documento pdf
			$nombreArchivo = "../tmp/orden_medicamentos_" . $_SESSION["idUsuario"] . ".pdf";
			$pdf->Output($nombreArchivo, "F");
		?>
		<input type="hidden" name="hdd_ruta_ordenMericamentos_pdf" id="hdd_ruta_ordenMericamentos_pdf" value="<?php echo($nombreArchivo); ?>" />
		<?php
			break;
			
		case "3": //ORDENES MÉDICAS   
			$idOrdenMedica = $utilidades->str_decode($_POST["idOrdenMedica"]);
			$ordenMedica = $dbOrdenesMedicas->getOrdenMedicaActivaById($idOrdenMedica);
			$procedimientos = $dbOrdenesMedicas->getProcedimientosActivosOrdenMedicaByIdOrdenMedica($idOrdenMedica);
			$paciente = $dbPacientes->getPaciente($ordenMedica['id_paciente']);
			$historia = $dbHistoriaClinica->getHistoriaClinicaId($ordenMedica['id_hc']);
			$diagnosticos = $dbHistoriaClinica->getListaCiexAdmision($historia['id_admision']);
			$usuarioCrea = $dbUsuarios->getUsuario($ordenMedica['usuario_crea']);
			
			$nombre_usuario_alt = "";
			if ($usuarioCrea["ind_anonimo"] == "1" && $historia["nombre_usuario_alt"] != "") {
				$nombre_usuario_alt = $historia["nombre_usuario_alt"];
			} else {
				$nombre_usuario_alt = $usuarioCrea["nombre_usuario"] . " " . $usuarioCrea["apellido_usuario"];
			}
			
			$tipo_orden_aux = $ordenMedica['tipo_orden_medica'];
			
			$fontSize = 7;
			$pdf = new FPDF('P', 'mm', array(216, 279));
			$pdf->AliasNbPages();
			$pdf->SetMargins(10, 10, 10);
			$pdf->SetAutoPageBreak(false);
			$pdf->SetFillColor(255, 255, 255);
			
			$pdf->bordeMulticell = 0; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde
			$pdf->pie_pagina = false;
			
			$pdf->AddPage();
			$pdf->SetFont("Arial", "", $fontSize);
			
			//Se obtienen las dimensiones del logo
			$arr_prop_imagen = getimagesize($ordenMedica["dir_logo_sede_det"]);
			$ancho_aux = floatval($arr_prop_imagen[0]);
			$alto_aux = floatval($arr_prop_imagen[1]);
			
			$ancho_max = 40.0;
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
			$pdf->Image($ordenMedica["dir_logo_sede_det"], 10 + (40 - $ancho_aux) / 2, 7, $ancho_aux, $alto_aux);
			
			$pdf->Cell(40, 24, "", 0, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->SetY(8);
			$pdf->SetX(50);
			$pdf->Cell(40, 4, ajustarCaracteres("FECHA DE LA ORDEN MÉDICA:"), 0, 0, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(25, 4, ajustarCaracteres($ordenMedica['fechaOrdenMedica']), 0, 0, "L");
			
			switch ($ordenMedica['tipo_orden_medica']) {
				case 1://1=Directa desde UT
					$tipoOrdenMedica = "Directa";
					break;
				case 2://2=Homologada
					$tipoOrdenMedica = "Homologada";
					break;
			}
			
			$pdf->SetY(12);
			$pdf->SetX(50);
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(35, 4, ajustarCaracteres("TIPO DE ORDEN MÉDICA:"), 0, 0, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(30, 4, ajustarCaracteres($tipoOrdenMedica), 0, 0, "L");
			
			if ($ordenMedica['tipo_orden_medica'] == 1) {//Sí es directa desde UT
				$pdf->SetY(16);
				$pdf->SetX(50);
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(15, 4, ajustarCaracteres("SERVICIO:"), 0, 0, "L");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(50, 4, ajustarCaracteres($ordenMedica['nombre_tipo_cita']), 0, 0, "L");
				$pdf->SetY(20);
				$pdf->SetX(50);
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(15, 4, ajustarCaracteres("ADMISIÓN:"), 0, 0, "L");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(50, 4, ajustarCaracteres($ordenMedica['id_admision']), 0, 0, "L");
			}
			
			$pdf->SetY(10);
			$pdf->SetX(115);
			$pdf->SetFont("Arial", "B", ($fontSize + 8));
			$pdf->Cell(90, 10, ajustarCaracteres("ORDEN MÉDICA"), 0, 0, "C");
			$pdf->SetY(20);
			$pdf->SetX(115);
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(90, 4, ajustarCaracteres("No. ".$ordenMedica['id_orden_m']), 0, 0, "C");
			$pdf->Ln(7);
			
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("DOCUMENTO:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente['numero_documento']), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("NOMBRES:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente['nombre_1'] . " " . $paciente['nombre_2']), 1, 1, "C");
			$pdf->SetX($pdf->GetX());
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("TIPO:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente['codigoDocumento']), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("APELLIDOS:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente['apellido_1'] . " " . $paciente['apellido_2']), 1, 1, "C");
			
			$pdf->SetX(10);
			
			$pdf->SetX($pdf->GetX());
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("SEXO:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(10, 4, ajustarCaracteres($paciente['codigoSexo']), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("FECH.NAC"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres($paciente['fecha_nacimiento_t']), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(10, 4, ajustarCaracteres("EDAD:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			
			$edad = explode('/', $paciente['edad']);
			$edadMagnitud = $edad[1];
			
			switch ($edadMagnitud) {
				case 1:
					$edadMagnitud = "Años";
					break;
				case 2:
					$edadMagnitud = "Meses";
					break;
				case 3:
					$edadMagnitud = "Días";
					break;
			}
			
			$pdf->Cell(16, 4, ajustarCaracteres($edad[0] . " " . $edadMagnitud), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("ASEGURADOR:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($ordenMedica['nombreConvenioAux']." / ".$ordenMedica['nombrePlanAux']), 1, 1, "C");
			$pdf->Ln(1);
			
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(192, 5, ajustarCaracteres("PROCEDIMIENTOS"), 0, 1, "C");
			
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(25, 4, ajustarCaracteres("TIPO"), 1, 0, "C");
			$pdf->Cell(15, 4, ajustarCaracteres("CÓDIGO:"), 1, 0, "C");
			$pdf->Cell(142, 4, ajustarCaracteres("DESCRIPCIÓN"), 1, 0, "C");
			$pdf->Cell(10, 4, ajustarCaracteres("OJO"), 1, 1, "C");
			
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->bordeMulticell = 1;
			$pdf->h_row2 = 3;
			$tipoProcedimiento = "";
			$codigoProcedimiento = "";
			$descripcionProcedimiento = "";
			$banderaPaquete=false;
			
			foreach ($procedimientos as $procedimiento) {
			
				if ($procedimiento['tipo_proc_orden_m_det'] == 1) {//Paquete
					$banderaPaquete=true;
					$tipoProcedimiento = "PAQUETE";
					$codigoProcedimiento = $procedimiento['id_paquete_p'];
					$descripcionProcedimiento = $procedimiento['nom_paquete_p'];
				} else if ($procedimiento['tipo_proc_orden_m_det'] == 2) {//Procedimiento
					$tipoProcedimiento = "PROCEDIMIENTO";
					$codigoProcedimiento = $procedimiento['cod_procedimiento'];
					$descripcionProcedimiento = $procedimiento['nombre_procedimiento'];
				}
				
				$textoOjo = "";
				switch ($procedimiento['ojo_orden_m_det']) {
					case 1://AO
						$textoOjo = "AO";
						break;
					case 2://OI
						$textoOjo = "OI";
						break;
					case 3://OD
						$textoOjo = "OD";
						break;
					case 4://No aplica
						$textoOjo = "NA";
						break;
				}
				
				$pdf->SetWidths2(array(25, 15, 142, 10));
				$pdf->SetAligns2(array('J', 'C', 'J', 'C'));
				$pdf->Row2(array(ajustarCaracteres($tipoProcedimiento), ajustarCaracteres($codigoProcedimiento), ajustarCaracteres(strtoupper($descripcionProcedimiento)), ajustarCaracteres($textoOjo))); //
				
				if($banderaPaquete){//Cuando la autorización es un paquete, entonces imprime cada uno de los procedimientos o insumos que contiene dicho paquete.
					$tipo_producto="";
					$paquetes_det= $dbPaquetesProcedimientos->getPaquetesProcedimientosDetalle($procedimiento["id_paquete_p"]);
					foreach($paquetes_det as $paquete_det){
						switch($paquete_det['tipo_producto']){
							case "P":
								$tipo_producto="PROCEDIMIENTO";
							break;
							case "I":
								$tipo_producto="INSUMO";
							break;
						}
						$pdf->SetWidths2(array(25, 15, 152));
						$pdf->SetAligns2(array('J', 'C', 'J'));
						$pdf->Row2(array(ajustarCaracteres($tipo_producto), ajustarCaracteres($paquete_det['cups']),ajustarCaracteres($paquete_det['nombre_cups'])));  		
					}
				}
				
				if($tipo_orden_aux==2){//Si la orden médica es homologada
					$pdf->SetWidths2(array(40, 152));
					$pdf->SetAligns2(array('J', 'J'));
					$pdf->Row2(array(ajustarCaracteres("DIAGNÓSTICO:"), ajustarCaracteres($procedimiento['cod_ciex_aux']." - ".strtoupper($procedimiento['nom_ciex_aux']))));
				}
				
				$pdf->SetWidths2(array(40, 152));
				$pdf->SetAligns2(array('J', 'J'));
				$pdf->Row2(array(ajustarCaracteres("DATOS CLÍNICOS:"), ajustarCaracteres($procedimiento['datos_clinicos'])));
				
				$pdf->SetWidths2(array(40, 152));
				$pdf->SetAligns2(array('J', 'J'));
				$pdf->Row2(array(ajustarCaracteres("JUSTIFI/OBSERVACIONES:"), ajustarCaracteres($procedimiento['des_orden_m_det'])));  
				$pdf->Ln(4);
							
			}
			
			if (count($diagnosticos) >= 1) {
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(192, 5, ajustarCaracteres("SOLICITADO CON DIAGNÓSTICOS"), 0, 1, "C");
				
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(20, 4, ajustarCaracteres("CÓDIGO CIEX"), 1, 0, "C");
				$pdf->Cell(172, 4, ajustarCaracteres("DESCRIPCIÓN:"), 1, 1, "C");
				
				$pdf->SetFont("Arial", "", $fontSize);
				foreach ($diagnosticos as $diagnostico) {
					$pdf->SetWidths2(array(20, 172));
					$pdf->SetAligns2(array('C', 'J'));
					$pdf->Row2(array(ajustarCaracteres($diagnostico['codciex']), ajustarCaracteres(strtoupper($diagnostico['nombre'])))); //
				}
			}
			
			$pdf->Ln(4);
			
			if ($tipo_orden_aux == 1) {//Sí es directa desde UT
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(22, 4, ajustarCaracteres("RESPONSABLE:"), 1, 0, "R");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(74, 4, ajustarCaracteres($utilidades->convertir_a_mayusculas($nombre_usuario_alt)), 1, 0, "L");
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(30, 4, ajustarCaracteres("REGISTRO MÉDICO:"), 1, 0, "R");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(66, 4, ajustarCaracteres($usuarioCrea['num_reg_medico']), 1, 1, "C");
				$pdf->SetFont("Arial", "I", $fontSize);
				//$pdf->Cell(192, 4, ajustarCaracteres("FIRMA ELECTRÓNICA"), 1, 1, "C");
			} else if($tipo_orden_aux == 2) {//Homologada
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(192, 4, ajustarCaracteres("ORDEN MÉDICA HOMOLOGADA:"), 1, 1, "C");
				$pdf->Cell(35, 4, ajustarCaracteres("REMITENTE RESPONSABLE"), 1, 0, "C");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(112, 4, ajustarCaracteres($ordenMedica['medico_homologacion']), 1, 0, "L");
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(30, 4, ajustarCaracteres("FECHA FORMULACIÓN:"), 1, 0, "C");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(15, 4, ajustarCaracteres($ordenMedica['fecha_homologacion_aux']), 1, 1, "C");
				
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->bordeMulticell = 1;
				$pdf->h_row2 = 3;
				$pdf->SetWidths2(array(192));
				$pdf->SetAligns2(array('J'));
				$pdf->Row2(array(ajustarCaracteres($ordenMedica['observ_orden_m'])));
			}
			
			$pdf->Ln(3);
			$pdf->SetFont("Arial", "", $fontSize - 1);
			$pdf->Cell(192, 4, ajustarCaracteres($ordenMedica['dir_sede_det'] . " - " . $ordenMedica['tel_sede_det']), 0, 1, "C");
			$pdf->SetFont("Arial", "I", $fontSize - 1);
			$pdf->Cell(96, 4, ajustarCaracteres("Fecha y hora de impresión: " . date("Y/m/d H:i:s")), 0, 0, "L");
			$pdf->Cell(96, 4, ajustarCaracteres("Página " . $pdf->PageNo() . "/{nb}"), 0, 1, "R");
			
			//Se guarda el documento pdf
			$nombreArchivo = "../tmp/orden_medicamentos_" . $_SESSION["idUsuario"] . ".pdf";
			$pdf->Output($nombreArchivo, "F");
		?>
		<input type="hidden" name="hdd_ruta_ordenMedica_pdf" id="hdd_ruta_ordenMedica_pdf" value="<?php echo($nombreArchivo); ?>" />
		<?php
			break;
	}
	
function validateSpaceHeight($pdf,$alto_pagina) {
			
	$y_aux = $pdf->GetY();
	if ($y_aux + $alto_pagina > 269) {
		$pdf->AddPage();
		$y_aux = 31;
	}
	return $y_aux;
}
function imprimir_firma($id_usuario_prof, $pdf, $nombre_usuario_alt) {
	$dbUsuarios = new DbUsuarios();

    $usuario_obj = $dbUsuarios->getUsuario($id_usuario_prof);
    $usuario_base = $usuario_obj;
    $ind_instructor = false;
    if ($usuario_obj["id_usuario_firma"] != "") {
        //El usuario que firma es otro, se cargan los datos de este usuario
        $usuario_obj = $dbUsuarios->getUsuario($usuario_obj["id_usuario_firma"]);
        $ind_instructor = true;
    }
    if ($usuario_obj["reg_firma"] != "" && file_exists($usuario_obj["reg_firma"])) {
        //Se obtienen las dimensiones de la imagen de la firma
        $arr_prop_imagen = getimagesize($usuario_obj["reg_firma"]);
        $ancho_aux = floatval($arr_prop_imagen[0]);
        $alto_aux = floatval($arr_prop_imagen[1]);

        $ancho_max = 50.0;
        $alto_max = 30.0;

        if ($ancho_aux > $ancho_max) {
            $alto_aux = floor($alto_aux * ($ancho_max / $ancho_aux));
            $ancho_aux = floor($ancho_aux * ($ancho_max / $ancho_aux));
        }
        if ($alto_aux > $alto_max) {
            $ancho_aux = floor($ancho_aux * ($alto_max / $alto_aux));
            $alto_aux = floor($alto_aux * ($alto_max / $alto_aux));
        }

        //Se verifica si la firma cabe en la página, de no ser así, se inserta una nueva página
        $alto_total = $alto_aux + 5;
        if ($ind_instructor) {
            $alto_total += 3;
            if ($nombre_usuario_alt != "") {
                $alto_total += 9;
            }
        }
        if ($usuario_obj["num_reg_medico"] != "") {
            $alto_total += 3;
        }

        $x_aux = 155 + floor((50 - $ancho_aux) / 2);
		$y_aux = validateSpaceHeight($pdf,$alto_total);
       
        $incremento_aux = 1;
        if ($ind_instructor) {
            $pdf->SetFont("Arial", "B", 9);
            $pdf->Cell(145, 3, "");
            $pdf->Cell(50, 3, ajustarCaracteres("Instructor"), 0, 1, 'C');
            $pdf->SetFont("Arial", "", 9);
            $incremento_aux += 3;
        }

        $pdf->Image($usuario_obj["reg_firma"], $x_aux, $y_aux + $incremento_aux, $ancho_aux, $alto_aux);
        $pdf->SetY($y_aux + $alto_aux + 1);
        $pdf->Cell(145, 3, "");
        $pdf->Cell(50, 3, ajustarCaracteres($usuario_obj["nombre_usuario"] . " " . $usuario_obj["apellido_usuario"]), 0, 1, 'C');

        $cadena_aux = "";
        if ($usuario_obj["num_reg_medico"] != "") {
            if ($usuario_obj["tipo_num_reg"] != "") {
                $cadena_aux = $usuario_obj["tipo_num_reg"] . ": " . $usuario_obj["num_reg_medico"];
            } else {
                $cadena_aux = "Registro: " . $usuario_obj["num_reg_medico"];
            }
        }
        if ($cadena_aux != "") {
            $pdf->Cell(145, 3, "");
            $pdf->Cell(50, 3, ajustarCaracteres($cadena_aux), 0, 1, 'C');
        }

        if ($ind_instructor && $nombre_usuario_alt != "") {
            $pdf->Ln(3);
            $pdf->SetFont("Arial", "B", 9);
            $pdf->Cell(145, 3, "");
            $pdf->Cell(50, 3, ajustarCaracteres("Profesional que atiende"), 0, 1, 'C');
            $pdf->SetFont("Arial", "", 9);
            $pdf->Cell(145, 3, "");
            $pdf->Cell(50, 3, ajustarCaracteres($nombre_usuario_alt), 0, 1, 'C');
        }
    }
}
?>
