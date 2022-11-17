<?php
	session_start();
	
 	header("Content-Type: text/xml; charset=UTF-8");
	
    require_once("../db/DbVariables.php");
	require_once("../db/DbDespacho.php");
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbMenus.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbPlanes.php");
	require_once("../db/DbPacientes.php");
	require_once("../db/DbDiagnosticos.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Utilidades.php");
	require_once("../db/DbHistoriaClinica.php");
		
	$dbVariables = new Dbvariables();
	$dbDespacho = new DbDespacho();
	$dbUsuarios = new DbUsuarios();
	$dbMenus = new DbMenus();
	$contenidoHtml = new ContenidoHtml();
	$utilidades = new Utilidades();
	$dbListas = new DbListas();
	$dbPlanes = new DbPlanes();
	$dbPacientes = new DbPacientes();
	$dbDiagnosticos = new DbDiagnosticos();
  	$dbHistoriaClinica = new DbHistoriaClinica();
	
	$opcion = $utilidades->str_decode($_POST["opcion"]);
	
		function imprimir_firma($id_usuario_prof, $pdf, $nombre_usuario_alt="") {
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
				$y_aux = 190 - $alto_total;
				
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
				$pdf->Cell(50, 3, ajustarCaracteres($usuario_obj["nombre_usuario"]." ".$usuario_obj["apellido_usuario"]), 0, 1, 'C');
				
				$cadena_aux = "";
				if ($usuario_obj["num_reg_medico"] != "") {
					if ($usuario_obj["tipo_num_reg"] != "") {
						$cadena_aux = $usuario_obj["tipo_num_reg"].": ".$usuario_obj["num_reg_medico"];
					} else {
						$cadena_aux = "Registro: ".$usuario_obj["num_reg_medico"];
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
	switch ($opcion) {
		case "1": //Guardar o Editar Despacho
			$id_usuario = $_SESSION["idUsuario"];
			
			@$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
			@$id_admision = $utilidades->str_decode($_POST["id_admision"]);
			@$nombre_paciente = $utilidades->str_decode($_POST["nombre_paciente"]);
			@$fecha_admision = $utilidades->str_decode($_POST["fecha_admision"]);
			@$documento_paciente = $utilidades->str_decode($_POST["documento_paciente"]);
			@$id_usuario_prof = $utilidades->str_decode($_POST["id_usuario_prof"]);
			@$nombre_profesional = $utilidades->str_decode($_POST["nombre_profesional"]);
			@$tipo_impresion = $utilidades->str_decode($_POST["tipo_impresion"]);
			@$fecha_actual_impr = intval($_POST["fecha_actual_impr"]);
			@$id_tipo = $utilidades->str_decode($_POST["id_tipo"]);
			@$num_formula = intval($_POST["num_formula"], 10);
			@$cant_formulas = intval($_POST["cant_formulas"], 10);
			@$num_cotizacion = intval($_POST["num_cotizacion"], 10);
			@$cant_cotizaciones = intval($_POST["cant_cotizaciones"], 10);
			$arr_formulas_medicas = array();
			
			for ($i = 0; $i < $cant_formulas; $i++) {
				@$arr_formulas_medicas[$i]["remitido"] = $utilidades->str_decode($_POST["remitido_".$i]);
				@$arr_formulas_medicas[$i]["num_carnet"] = $utilidades->str_decode($_POST["num_carnet_".$i]);
				@$arr_formulas_medicas[$i]["fecha_det"] = $utilidades->str_decode($_POST["fecha_det_".$i]);
				@$arr_formulas_medicas[$i]["formula_medica"] = $utilidades->str_decode($_POST["formula_medica_".$i]);
			}
			
			$arr_cotizaciones = array();
			for ($i = 0; $i < $cant_cotizaciones; $i++) {
				@$arr_cotizaciones[$i]["id_proc_cotiz"] = $utilidades->str_decode($_POST["id_proc_cotiz_".$i]);
				@$arr_cotizaciones[$i]["valor_cotiz"] = $utilidades->str_decode($_POST["valor_cotiz_".$i]);
				@$arr_cotizaciones[$i]["observaciones_cotiz"] = $utilidades->str_decode($_POST["observaciones_cotiz_".$i]);
			}
			
			if ($fecha_actual_impr == 1) {
				//Se obtiene la fecha actual
				$fechas_obj = $dbVariables->getFechaActualMostrar();
				$fecha_mostrar = $fechas_obj["fecha_actual_mostrar"];
			} else {
				$fecha_mostrar = $fecha_admision;
			}
			
			if ($id_tipo == 4) { //Solo imprime
				$ind_despacho = 1;
			} else {
				$ind_despacho = $dbDespacho->crearEditarDespacho($id_admision, $id_paciente, "", $arr_formulas_medicas, $arr_cotizaciones, $tipo_impresion, $id_tipo, $id_usuario);
			}
															 
			$reg_menu = $dbMenus->getMenu(13);
			$url_menu = $reg_menu["pagina_menu"];
        ?>
		<input type="hidden" value="<?php echo($ind_despacho); ?>" name="hdd_exito" id="hdd_exito" />
		<input type="hidden" value="<?php echo($url_menu); ?>" name="hdd_url_menu" id="hdd_url_menu" />
	    <?php
			//Se obtienen los datos del profesional
			$usuario_prof_obj = $dbUsuarios->getUsuario($id_usuario_prof);
			
			//Si se seleccionó un tipo de impresión de fórmulas
			if ($id_tipo == 2 || $id_tipo == 4) {
				require_once("../funciones/pdf/fpdf.php");
				require_once("../funciones/pdf/makefont/makefont.php");
				require_once("../funciones/pdf/funciones.php");
				require_once("../funciones/pdf/WriteHTML.php");
				
				//Se obtiene el registro de detalle que se desea imprimir
				$despacho_det_obj = $dbDespacho->getDespachoDetNumFormula($id_admision, $num_formula + 1);
				
				//Se selecciona la fecha de la fórmula
				if ($despacho_det_obj["fecha_det_t"] != "" && $fecha_actual_impr != 1) {
					$fecha_mostrar = $despacho_det_obj["fecha_det_t"];
				}
				
				//Se buscan los diagnósticos asociados a la admisión
				$lista_diagnosticos = $dbDespacho->getListaCiexAdmision($id_admision);
				$diag_ppal_obj = array();
				if (count($lista_diagnosticos) > 0) {
					$diag_ppal_obj = $lista_diagnosticos[0];
				}
				
	        	if ($tipo_impresion == 1) { //Formato Consultorio
					$pdf = new FPDF("P", "mm", array(216, 279));
					$pdfHTML = new PDF_HTML();
					$pdf->SetMargins(10, 10, 10);
					$pdf->SetAutoPageBreak(false);
					$pdf->SetFillColor(255, 255, 255);
					
					$pdf->bordeMulticell = 0; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde
					$pdf->pie_pagina = false;
					
					$pdf->AddPage();
					
					$pdf->SetFont("Arial", "", 9);
					srand(microtime() * 1000000);
					
					//Se obtienen las dimensiones del logo
					$arr_prop_imagen = getimagesize($despacho_det_obj["dir_logo_sede_det"]);
					$ancho_aux = floatval($arr_prop_imagen[0]);
					$alto_aux = floatval($arr_prop_imagen[1]);
					
					$ancho_max = 55.0;
					$alto_max = 18.0;
					
					if ($ancho_aux > $ancho_max) {
						$alto_aux = floor($alto_aux * ($ancho_max / $ancho_aux));
						$ancho_aux = floor($ancho_aux * ($ancho_max / $ancho_aux));
					}
					if ($alto_aux > $alto_max) {
						$ancho_aux = floor($ancho_aux * ($alto_max / $alto_aux));
						$alto_aux = floor($alto_aux * ($alto_max / $alto_aux));
					}
					
					//Logo
					$pdf->Image($despacho_det_obj["dir_logo_sede_det"], 15, 11, $ancho_aux, $alto_aux);
					
					$pdf->SetX(10);
					$pdf->SetY(30);
					$pdf->Cell(150, 4, ajustarCaracteres("Paciente: ".$utilidades->convertir_a_mayusculas($nombre_paciente)), 0, 0, "L");
					$pdf->Cell(45, 4, ajustarCaracteres("Fecha: ".$fecha_mostrar), 0, 1, "L");
					$pdf->Ln(2);
					$pdf->Cell(195, 4, ajustarCaracteres("Documento: ".$documento_paciente), 0, 1, "L");
					$pdf->Ln(2);
					
					//Cuerpo de la fórmula
					$x_aux = $pdf->GetX();
					$y_aux = $pdf->GetY();
					
					$texto_formula = ajustarCaracteres($despacho_det_obj["formula_medica"]);
					$pdfHTML->WriteHTML($texto_formula, $pdf);
					
		            /*if (isset($diag_ppal_obj["codciex"])) {
						$texto_formula .= ajustarCaracteres("<p>&nbsp;</p><p><b>DIAGNÓSTICO:</b> ".$diag_ppal_obj["codciex"]." - ".$diag_ppal_obj["nombre"]."</p>");
					}*/
					
					//Se agrega la firma del médico
					if ($usuario_prof_obj["id_usuario_firma"] == "") {
						$usuario_firma_obj = $usuario_prof_obj;
					} else {
						//El usuario que firma es otro, se cargan los datos de este usuario
						$usuario_firma_obj = $dbUsuarios->getUsuario($usuario_prof_obj["id_usuario_firma"]);
					}
					
					if ($usuario_firma_obj["reg_firma"] != "") {
						//Se obtienen las dimensiones de la imagen de la firma
						$arr_prop_imagen = getimagesize($usuario_firma_obj["reg_firma"]);
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
						
						//Se halla el alto total de la firma
						$alto_total = $alto_aux + 5;
						if ($usuario_firma_obj["num_reg_medico"] != "") {
							$alto_total += 3;
						}
						
						$x_aux2 = 155 + floor((50 - $ancho_aux) / 2);
						$y_aux2 = $y_aux + 75 - $alto_total;
						
						$pdf->Image($usuario_firma_obj["reg_firma"], $x_aux2, $y_aux2 + 1, $ancho_aux, $alto_aux);
						$pdf->SetY($y_aux2 + $alto_aux + 1);
						$pdf->Cell(145, 3, "");
						$pdf->Cell(50, 3, ajustarCaracteres($usuario_firma_obj["nombre_usuario"]." ".$usuario_firma_obj["apellido_usuario"]), 0, 1, "C");
						
						$cadena_aux = "";
						if ($usuario_firma_obj["num_reg_medico"] != "") {
							$cadena_aux = "Reg. Médico: ".$usuario_firma_obj["num_reg_medico"];
						}
						if ($cadena_aux != "") {
							$pdf->Cell(145, 3, "");
							$pdf->Cell(50, 3, ajustarCaracteres($cadena_aux), 0, 1, "C");
						}
					}
					
					$pdf->SetY($y_aux + 77);
					
					$pdf->SetFont("Arial", "", 7);
					$pdf->Cell(195, 4, ajustarCaracteres($despacho_det_obj["dir_sede_det"]), 0, 1, "C", true);
					$pdf->Ln(-1);
					$pdf->Cell(195, 4, ajustarCaracteres("PBX: ".$despacho_det_obj["tel_sede_det"]), 0, 1, "C", true);
					
					//Se guarda el documento pdf
					$nombreArchivo = "../tmp/formula_" . $_SESSION["idUsuario"] . ".pdf";
					$pdf->Output($nombreArchivo, "F");
				} else if ($tipo_impresion == 2) { //Formato Ecopetrol
					$pdf = new FPDF("P", "mm", array(216, 279));
					$pdfHTML = new PDF_HTML();
					$pdf->SetMargins(10, 10, 10);
					$pdf->SetAutoPageBreak(false);
					$pdf->SetFillColor(255, 255, 255);
					
					$pdf->bordeMulticell = 1; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde
					$pdf->pie_pagina = false;
					
					$pdf->AddPage();
					
					$pdf->SetFont("Arial", "B", 7);
					srand(microtime() * 1000000);
					
					//Logo
					$pdf->Rect(10, 10, 60, 16);
					$pdf->Image("../imagenes/logo_ecopetrol.png", 25, 11, 28);
					
					$pdf->SetX(70);
					$pdf->MultiCell(135, 4, ajustarCaracteres("DECISIONES CLÍNICAS"), 1, "C");
					$pdf->SetX(70);
					$pdf->MultiCell(135, 4, ajustarCaracteres("RESPONSABILIDAD INTEGRAL\nUNIDAD DE SALUD INTEGRAL"), 1, "C");
					$pdf->SetX(70);
					$pdf->Cell(45, 4, ajustarCaracteres("USD-DHS-F-287"), 1, 0, "C");
					$pdf->Cell(45, 4, ajustarCaracteres("Elaborado: 26/02/2013"), 1, 0, "C");
					$pdf->Cell(45, 4, ajustarCaracteres("Versión: 2"), 1, 1, "C");
					
					$usuario_firma_obj = $usuario_prof_obj;
					if ($usuario_prof_obj["id_usuario_firma"] != "") {
						//El usuario que firma es otro, se cargan los datos de este usuario
						$usuario_firma_obj = $dbUsuarios->getUsuario($usuario_prof_obj["id_usuario_firma"]);
					}
					
					$pdf->SetFont("Arial", "", 7);
					$pdf->Ln(1);
					$pdf->Cell(145, 4, ajustarCaracteres("Orden de Servicio: "), 1, 0, "L");
					$pdf->Cell(50, 4, ajustarCaracteres("No. Identificación: ".$documento_paciente), 1, 1, "L");
					$pdf->Cell(145, 4, ajustarCaracteres("Nombre: ".$nombre_paciente), 1, 0, "L");
					$pdf->Cell(50, 4, ajustarCaracteres("Registro: ".$despacho_det_obj["num_carnet"]), 1, 1, "L");
					$pdf->Cell(145, 4, ajustarCaracteres("Remitido por: ".$usuario_firma_obj["nombre_usuario"]." ".$usuario_firma_obj["apellido_usuario"]), 1, 0, "L");
					$pdf->Cell(50, 4, ajustarCaracteres("Fecha: ".$fecha_mostrar), 1, 1, "L");
					$pdf->Cell(75, 4, ajustarCaracteres("Remitido a: ".$despacho_det_obj["remitido"]), 1, 0, "L");
					$pdf->Cell(70, 4, ajustarCaracteres("Dirección: "), 1, 0, "L");
					$pdf->Cell(50, 4, ajustarCaracteres("Tel: "), 1, 1, "L");
					
					$pdf->Cell(195, 4, ajustarCaracteres("ATENTAMENTE SOLICITAMOS A USTED PRACTICAR A NUESTROS PACIENTES EL SERVICIO/SUMINISTRO"), "TLR", 1, "C");
					$pdf->Cell(35, 4, ajustarCaracteres("CÓDIGO"), "BL", 0, "C");
					$pdf->Cell(125, 4, ajustarCaracteres("DESCRIPCIÓN"), "B", 0, "C");
					$pdf->Cell(35, 4, ajustarCaracteres("VALOR"), "BR", 1, "C");
					
					//Cuerpo de la fórmula
					$x_aux = $pdf->GetX();
					$y_aux = $pdf->GetY();
					$pdf->Rect($x_aux, $y_aux, 195, 45);
					
					$texto_formula = ajustarCaracteres($despacho_det_obj["formula_medica"]);
					
					/*if (isset($diag_ppal_obj["codciex"])) {
						$texto_formula .= ajustarCaracteres("<p>&nbsp;</p><p>DIAGNÓSTICO: ".$diag_ppal_obj["codciex"]." - ".$diag_ppal_obj["nombre"]."</p>");
					}*/
					
					$pdfHTML->WriteHTML($texto_formula, $pdf);
					$pdf->SetY($y_aux + 45);
					
					$y_aux = $pdf->GetY();
					$pdf->Cell(50, 6, ajustarCaracteres("TOTAL"), "TBL", 0, "L", true);
					$pdf->Cell(90, 6, ajustarCaracteres("No. SERVICIOS/SUMINISTROS"), "TB", 0, "L", true);
					$pdf->Cell(55, 6, ajustarCaracteres("VALOR TOTAL $"), "TBR", 1, "L", true);
					$pdf->Rect(99, $y_aux + 1, 20, 4);
					
					$pdf->Cell(195, 4, ajustarCaracteres("OBSERVACIONES: "), "TLR", 1, "L", true);
					$pdf->Cell(195, 3, ajustarCaracteres(" "), "BLR", 1, "L", true);
					$x_aux = $pdf->GetX();
					$y_aux = $pdf->GetY();
					$pdf->Cell(195, 16, ajustarCaracteres(" "), "TLR", 1, "L", true);
					
					//Firma
					if ($usuario_firma_obj["reg_firma"] != "") {
						$arr_prop_imagen = getimagesize($usuario_firma_obj["reg_firma"]);
						$ancho_aux = $arr_prop_imagen[0];
						$alto_aux = $arr_prop_imagen[1];
						$ancho_imagen = round((14 * $ancho_aux) / $alto_aux, 0);
						
						$pdf->Image($usuario_firma_obj["reg_firma"], $x_aux + 155 - round($ancho_imagen / 2, 0), $y_aux + 1, $ancho_imagen, 14);
					}
					
					$x_aux = $pdf->GetX();
					$y_aux = $pdf->GetY();
					$pdf->Line($x_aux + 15, $y_aux, $x_aux + 65, $y_aux);
					$pdf->Line($x_aux + 130, $y_aux, $x_aux + 180, $y_aux);
					$pdf->Cell(80, 4, ajustarCaracteres("FIRMA PACIENTE"), "BL", 0, "C", true);
					$pdf->Cell(35, 4, ajustarCaracteres(" "), "B", 0, "C", true);
					$pdf->Cell(80, 4, ajustarCaracteres("FIRMA AUTORIZADA"), "BR", 1, "C", true);
					
					//Se guarda el documento pdf
					$nombreArchivo = "../tmp/formula_" . $_SESSION["idUsuario"] . ".pdf";
					$pdf->Output($nombreArchivo, "F");
	        	}
		?>
	    <input type="hidden" name="hdd_ruta_arch_pdf" id="hdd_ruta_arch_pdf" value="<?php echo($nombreArchivo); ?>" />
        <?php
			} else if ($id_tipo == 5 || $id_tipo == 6) { //Impresión de cotizaciones
				require_once("../funciones/pdf/fpdf.php");
				require_once("../funciones/pdf/makefont/makefont.php");
				require_once("../funciones/pdf/funciones.php");
				require_once("../funciones/pdf/WriteHTML.php");
				
				$lista_despacho_cotizacion = $dbDespacho->getListaDespachoCotizaciones($id_admision);
				$despacho_cotizacion_obj = $lista_despacho_cotizacion[$num_cotizacion];
				
				$pdf = new FPDF("P", "mm", array(216, 279));
				$pdfHTML = new PDF_HTML();
				$pdf->SetMargins(10, 10, 10);
				$pdf->SetAutoPageBreak(false);
				$pdf->SetFillColor(255, 255, 255);
				
				$pdf->bordeMulticell = 0; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde
				$pdf->pie_pagina = false;
				
				$pdf->AddPage();
				
				$pdf->SetFont("Arial", "", 9);
				srand(microtime() * 1000000);
				
				//Se obtienen las dimensiones del logo
				$arr_prop_imagen = getimagesize($despacho_cotizacion_obj["dir_logo_sede_det"]);
				$ancho_aux = floatval($arr_prop_imagen[0]);
				$alto_aux = floatval($arr_prop_imagen[1]);
				
				$ancho_max = 55.0;
				$alto_max = 18.0;
				
				if ($ancho_aux > $ancho_max) {
					$alto_aux = floor($alto_aux * ($ancho_max / $ancho_aux));
					$ancho_aux = floor($ancho_aux * ($ancho_max / $ancho_aux));
				}
				if ($alto_aux > $alto_max) {
					$ancho_aux = floor($ancho_aux * ($alto_max / $alto_aux));
					$alto_aux = floor($alto_aux * ($alto_max / $alto_aux));
				}
				
				//Logo
				$pdf->Image($despacho_cotizacion_obj["dir_logo_sede_det"], 15, 11, $ancho_aux, $alto_aux);
				
				$pdf->SetX(10);
				$pdf->SetY(30);
				$pdf->Cell(150, 4, ajustarCaracteres("Paciente: ".$utilidades->convertir_a_mayusculas($nombre_paciente)), 0, 0, "L");
				$pdf->Cell(45, 4, ajustarCaracteres("Fecha: ".$fecha_mostrar), 0, 1, "L");
				$pdf->Ln(2);
				$pdf->Cell(150, 4, ajustarCaracteres("Documento: ".$documento_paciente), 0, 0, "L");
				$pdf->SetFont("Arial", "B", 9);
				$pdf->Cell(45, 4, ajustarCaracteres("COTIZACIÓN"), 0, 1, "L");
				$pdf->SetFont("Arial", "", 9);
				$pdf->Ln(2);
				
				//Procedimiento
				$pdf->Cell(150, 4, ajustarCaracteres("Procedimiento: ".$utilidades->convertir_a_mayusculas($despacho_cotizacion_obj["nombre_proc_cotiz"])), 0, 0, "L");
				$pdf->Cell(45, 4, ajustarCaracteres("Valor: ".str_replace(",", ".", number_format($despacho_cotizacion_obj["valor_cotiz"]))), 0, 1, "L");
				$pdf->Ln(2);
				
				//Cuerpo de la cotización
				$x_aux = $pdf->GetX();
				$y_aux = $pdf->GetY();
				
				$observaciones_cotiz = ajustarCaracteres($despacho_cotizacion_obj["observaciones_cotiz"]);
				
				$pdfHTML->WriteHTML($observaciones_cotiz, $pdf);
				
				//Se agrega la firma del médico
				if ($usuario_prof_obj["id_usuario_firma"] == "") {
					$usuario_firma_obj = $usuario_prof_obj;
				} else {
					//El usuario que firma es otro, se cargan los datos de este usuario
					$usuario_firma_obj = $dbUsuarios->getUsuario($usuario_prof_obj["id_usuario_firma"]);
				}
				
				if ($usuario_firma_obj["reg_firma"] != "") {
					//Se obtienen las dimensiones de la imagen de la firma
					$arr_prop_imagen = getimagesize($usuario_firma_obj["reg_firma"]);
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
					
					//Se halla el alto total de la firma
					$alto_total = $alto_aux + 5;
					if ($usuario_firma_obj["num_reg_medico"] != "") {
						$alto_total += 3;
					}
					
					$x_aux2 = 155 + floor((50 - $ancho_aux) / 2);
					$y_aux2 = $y_aux + 75 - $alto_total;
					
					$pdf->Image($usuario_firma_obj["reg_firma"], $x_aux2, $y_aux2 + 1, $ancho_aux, $alto_aux);
					$pdf->SetY($y_aux2 + $alto_aux + 1);
					$pdf->Cell(145, 3, "");
					$pdf->Cell(50, 3, ajustarCaracteres($usuario_firma_obj["nombre_usuario"]." ".$usuario_firma_obj["apellido_usuario"]), 0, 1, "C");
					
					$cadena_aux = "";
					if ($usuario_firma_obj["num_reg_medico"] != "") {
						$cadena_aux = "Reg. Médico: ".$usuario_firma_obj["num_reg_medico"];
					}
					if ($cadena_aux != "") {
						$pdf->Cell(145, 3, "");
						$pdf->Cell(50, 3, ajustarCaracteres($cadena_aux), 0, 1, "C");
					}
				}
					
				$pdf->SetY($y_aux + 77);
				//$pdf->SetY($y_aux + 71);
				
					$pdf->SetFont("Arial", "", 7);
					$pdf->Cell(195, 4, ajustarCaracteres($despacho_cotizacion_obj["dir_sede_det"]), 0, 1, "C", true);
					$pdf->Ln(-1);
					$pdf->Cell(195, 4, ajustarCaracteres("PBX: ".$despacho_cotizacion_obj["tel_sede_det"]), 0, 1, "C", true);
				
				//Se guarda el documento pdf
				$nombreArchivo = "../tmp/cotizacion_" . $_SESSION["idUsuario"] . ".pdf";
				$pdf->Output($nombreArchivo, "F");
		?>
	    <input type="hidden" name="hdd_ruta_cotiz_pdf" id="hdd_ruta_cotiz_pdf" value="<?php echo($nombreArchivo); ?>" />
    	<?php
			}
			break;	
			
		case "2"://Guarda los datos de la incapacidad en la base de datos
					
			@$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
			@$id_profesional = $utilidades->str_decode($_POST["id_profesional"]);
			@$id_usuario = $utilidades->str_decode($_POST["id_usuario"]);
			@$tipo_atencion = $utilidades->str_decode($_POST["tipo_atencion"]);
			@$origen_incapacidad = $utilidades->str_decode($_POST["origen_incapacidad"]);
			@$prorroga = $utilidades->str_decode($_POST["prorroga"]);
			@$fecha_inicial = $utilidades->str_decode($_POST["fecha_inicial"]);
			@$fecha_final = $utilidades->str_decode($_POST["fecha_final"]);
			@$observaciones = $utilidades->str_decode($_POST["observaciones"]);
			@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
			@$id_lugar_cita = $utilidades->str_decode($_POST["id_lugar_cita"]);
			@$id_plan = $utilidades->str_decode($_POST["id_plan"]);
			@$diagnostico_principal = $utilidades->str_decode($_POST["cod_ciex"]);
			@$diagnostico_relacionado = $utilidades->str_decode($_POST["cod_ciex_rel"]);
				
			@$id_hc = $utilidades->str_decode($_POST["id_hc"]);
			@$id_admision = $utilidades->str_decode($_POST["id_admision"]);
											
			$rta = $dbDespacho->GuardarIncapacidad($id_paciente,$id_profesional,$id_usuario,$id_hc,$id_admision,$tipo_atencion,$origen_incapacidad,$prorroga,$fecha_inicial,$fecha_final,$observaciones, $id_convenio,$id_plan,$id_lugar_cita, $diagnostico_principal, $diagnostico_relacionado);
						
        	?>
          	   <input type="hidden" name="hdd_rta_guardar_incapacidad" id="hdd_rta_guardar_incapacidad" value="<?= $rta ?>"/>
        	<?php
			
		break;	
		
		case "3": //Imprimir el PDF de incapacidades
			require_once("../funciones/pdf/fpdf.php");
			require_once("../funciones/pdf/makefont/makefont.php");
			require_once("../funciones/pdf/funciones.php");
			require_once("../funciones/pdf/WriteHTML.php");
			
			@$dias = $utilidades->str_decode($_POST["dias"]);
			@$id_incapacidad = $utilidades->str_decode($_POST["id_incapacidad"]);
			
			$incapacidad_obj = $dbHistoriaClinica->getIncapacidadesDet($id_incapacidad);
			
			$id_paciente = $incapacidad_obj["id_paciente"];
			$id_lugar_cita = $incapacidad_obj["id_lugar_cita"];
			/*$id_convenio =  $incapacidad_obj["id_convenio"];
			$id_plan = $incapacidad_obj["id_plan"];*/
			$id_admision = $incapacidad_obj["id_admision"];
			
			$fecha_inicial = $dbVariables->cambiarFormatoFecha($incapacidad_obj["fecha_inicio_incapacidad"]);
			$fecha_final = $dbVariables->cambiarFormatoFecha($incapacidad_obj["fecha_fin_incapacidad"]);
			
			if(empty($id_lugar_cita)){ $id_lugar_cita = $_SESSION["idLugarUsuario"]; }
			if(empty($incapacidad_obj["id_convenio"])){ $id_convenio = ""; }
			if(empty($incapacidad_obj["id_plan"])){ $id_plan = ""; }
		
			$despacho_det = $dbDespacho->getRegistrosDespachoDet($id_paciente, $id_admision);			
			
			//Validación de id_lugar_cita es vacía en POST 			
		
			//Se consultan los datos que se van a mostrar en el PDF
			$sedes_detalle = 			$dbListas->getSedesDetalleIncapacidad($id_lugar_cita);
			$paciente_detalle = 		$dbPacientes->getPacienteDetalle($id_paciente);
						
			if(empty($diagnostico_principal)){
				$diagnostico_clinico_det =  $dbDespacho->getDiagnosticoClinicoDet($id_admision);
				for($i = 0; $i < count($diagnostico_clinico_det); ++$i) {
				 $diagnostico_principal  	=	$diagnostico_clinico_det[0]['cod_ciex'];
				 $diagnostico_relacionado  	=	$diagnostico_clinico_det[1]['cod_ciex'];
					 
					 if(empty($diagnostico_principal)){
							$diagnostico_principal  	=	$diagnostico_clinico_det[1]['cod_ciex'];
							$diagnostico_relacionado  	=	$diagnostico_clinico_det[2]['cod_ciex'];			
						}
				}	
			}else{
				$diagnostico_principal  	=	$diagnostico_principal;
				$diagnostico_relacionado  	=	$diagnostico_relacionado;
			}
				            	
			$nombre_profesional = $incapacidad_obj["profesional"];
			$img_firma_profesional = $incapacidad_obj["reg_firma"];
			$nombre_paciente = $paciente_detalle["nombre_1"]." ".$paciente_detalle["nombre_2"]." ".$paciente_detalle["apellido_1"]." ".$paciente_detalle["apellido_2"];
			$img_logo = $sedes_detalle["dir_logo_sede_det"];
				
			try{	
			$pdf = new FPDF('P', 'mm', array(216, 279));
        	$pdfHTML = new PDF_HTML();
        	$pdf->SetMargins(10, 10, 10);
        	$pdf->SetAutoPageBreak(false);
        	$pdf->SetFillColor(255, 255, 255);
        	$pdf->bordeMulticell = 0; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde.
        	$pdf->pie_pagina = false;
        	$pdf->AddPage();
        	$pdf->SetFont("Arial", "", 9);
        	srand(microtime() * 1000000);
				
			//Se obtienen las dimensiones del logo

			$arr_prop_imagen = getimagesize($img_logo);
			$ancho_aux = floatval($arr_prop_imagen[0]);
			$alto_aux = floatval($arr_prop_imagen[1]);
			$ancho_max = 55.0;
			$alto_max = 22.0;
			
			if ($ancho_aux > $ancho_max) {
				$alto_aux = floor($alto_aux * ($ancho_max / $ancho_aux));
				$ancho_aux = floor($ancho_aux * ($ancho_max / $ancho_aux));
			}
			if ($alto_aux > $alto_max) {
				$ancho_aux = floor($ancho_aux * ($alto_max / $alto_aux));
				$alto_aux = floor($alto_aux * ($alto_max / $alto_aux));
			
			}
			
			//Se arma el texto que contiene toda la información del paciente en Multicell
			$datos_paciente = 'Documento: '.$paciente_detalle["codigo_detalle"].". ".$paciente_detalle["numero_documento"]."\n";
			$datos_paciente .= 'Nombre: ' . $nombre_paciente . '';
			
			//Titulo del archivo en el navegador
			$pdf->SetTitle("Certificado de incapacidad");			
			$pdf->SetFont("Arial", "B", 9);
			if($img_logo<>""){
				$pdf->Image($img_logo, 10, 8, $ancho_aux, $alto_aux);
			}
        	$pdf->Cell(0, 0, ajustarCaracteres($sedes_detalle["nombre_prestador"]), 0, 0, "C");
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(-20, 0,ajustarCaracteres("No. "),0,0,"R");
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(-20, 0, ajustarCaracteres($id_incapacidad),0,0,"L");
			$pdf->Ln();
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(0, 10, ajustarCaracteres($sedes_detalle["numero_documento"]), 0, 0, "C");
			
			
			$pdf->Ln(8);
			$pdf->SetX(10);
			$pdf->SetFont("Arial", "B", 9);
        	$pdf->Cell(105, 4, ajustarCaracteres("Código de habilitación:"), 0, 0, "R");
			$pdf->SetFont("Arial", "", 9);
        	$pdf->Cell(23, 4, ajustarCaracteres('682760035501'), 0, 0, "R");
			
			
        	$pdf->SetX(10);
        	$pdf->SetY(35);	
			$pdf->SetFont("Arial", "B", 9);    	
			$pdf->Cell(0,0,"CERTIFICADO DE INCAPACIDAD",0,1,"C");
			$pdf->Ln(-4);
			$pdf->SetFont("Arial", "B", 9);
        	$pdf->Cell(170, 4, ajustarCaracteres("Fecha:"), 0, 0, "R");
			$pdf->SetFont("Arial", "", 9);
        	$pdf->Cell(18, 4, ajustarCaracteres(date("d/m/Y")), 0, 0, "R");
			$pdf->Ln(5);
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(170, 4, ajustarCaracteres("Lugar:"), 0, 0, "R");
			$pdf->SetFont("Arial", "", 9);	
        	$pdf->Cell(15, 4, ajustarCaracteres($sedes_detalle["lugar_sede"]), 0, 0, "L");
			$pdf->Ln(10);	
			$pdf->SetFont("Arial", "B", 9);
        	$pdf->Cell(17, 4, ajustarCaracteres("Convenio:"), 0, 0, "L");
			$pdf->SetFont("Arial", "", 9);
        	$pdf->Cell(1, 4, ajustarCaracteres($incapacidad_obj["nombre_convenio"]), 0, 0, "L");
			$pdf->Ln(10);	
			$pdf->SetFont("Arial", "B", 9);
        	$pdf->Cell(17, 4, ajustarCaracteres("Paciente:"), 0, 0, "L");
			$pdf->Ln();
			$pdf->SetFont("Arial", "", 9);
			$pdf->MultiCell(190, 5, ajustarCaracteres($datos_paciente), 1, 1, "L");
			$pdf->Ln(8);
			$pdf->SetFont("Arial", "B", 9);
        	$pdf->Cell(23, 4, ajustarCaracteres("Tipo atención:"), 0, 0, "L");
			$pdf->SetFont("Arial", "", 9);
        	$pdf->Cell(8, 4, ajustarCaracteres($incapacidad_obj["nombre_atencion"]), 0, 0, "L");
			$pdf->Ln(8);
			$pdf->SetFont("Arial", "B", 9);
        	$pdf->Cell(32, 4, ajustarCaracteres("Origen incapacidad:"), 0, 0, "L");
			$pdf->SetFont("Arial", "", 9);
        	$pdf->Cell(8, 4, ajustarCaracteres($incapacidad_obj["nombre_origen_inc"]), 0, 0, "L");
			$pdf->Ln(10);
			$pdf->SetFont("Arial", "B", 9);
        	$pdf->Cell(20, 4, ajustarCaracteres("Fecha inicio:"), 0, 0, "L");
			$pdf->SetFont("Arial", "", 9);
        	$pdf->Cell(8, 4, ajustarCaracteres($fecha_inicial), 0, 0, "L");
			$pdf->SetFont("Arial", "B", 9);
        	$pdf->Cell(50, 4, ajustarCaracteres("Fecha fin:"), 0, 0, "R");
			$pdf->SetFont("Arial", "", 9);
        	$pdf->Cell(10, 4, ajustarCaracteres($fecha_final), 0, 0, "L");
			$pdf->SetFont("Arial", "B", 9);
        	$pdf->Cell(68, 4, ajustarCaracteres("Días:"), 0, 0, "R");
			$pdf->SetFont("Arial", "", 9);
        	$pdf->Cell(10, 4, ajustarCaracteres($dias), 0, 0, "L");
			$pdf->Ln(10);
			$pdf->SetFont("Arial", "B", 9);
        	$pdf->Cell(28, 4, ajustarCaracteres("Diagnóstico pral:"), 0, 0, "L"); substr('abcdef', 0, 4);  // abcd
			$pdf->SetFont("Arial", "", 9);
        	$pdf->Cell(7, 4, ajustarCaracteres(substr($diagnostico_principal, 0, 4)), 0, 0, "L");
			$pdf->SetFont("Arial", "B", 9);
        	$pdf->Cell(66, 4, ajustarCaracteres("Diagnóstico relacionado:"), 0, 0, "R");
			$pdf->SetFont("Arial", "", 9);
        	$pdf->Cell(9, 4, ajustarCaracteres(substr($diagnostico_relacionado, 0, 4)), 0, 0, "L");
			$pdf->Ln(15);
			$pdf->SetFont("Arial", "B", 9);
        	$pdf->Cell(7, 4, ajustarCaracteres("Observaciones:"), 0, 0, "L");
			$pdf->Ln();
			$pdf->SetFont("Arial", "", 9);
			$pdf->MultiCell(190, 10, ajustarCaracteres($incapacidad_obj["observaciones"]), 0); 
			$pdf->Ln();	
			imprimir_firma($incapacidad_obj["id_profesional"], $pdf, "");	
			$nombreArchivo = "../tmp/incapacidad_" . $_SESSION["idUsuario"] . ".pdf";
        	$pdf->Output($nombreArchivo, "F");
			?>
        		<input type="hidden" name="hdd_ruta_incapacidad" id="hdd_ruta_incapacidad" value="<?php echo($nombreArchivo); ?>" />
        	<?php
			
			}catch(Exception $e){
				 echo($e->getMessage());
			}
			
		break;
	}
?>
