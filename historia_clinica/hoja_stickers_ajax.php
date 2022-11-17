<?php
	session_start();
	
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../db/DbAdmision.php");
	require_once("../db/DbPacientes.php");
	
	/* Requires para FPDF */
	require_once("../funciones/pdf/fpdf.php");
	require_once("../funciones/pdf/makefont/makefont.php");
	require_once("../funciones/pdf/funciones.php");
	
	$contenidoHtml = new ContenidoHtml();
	$utilidades = new Utilidades();
	$funcionesPersona = new FuncionesPersona();
	$dbAdmision = new DbAdmision();
	$dbPacientes = new DbPacientes();
	
	$contenidoHtml->validar_seguridad(1);
	
	$opcion = $utilidades->str_decode($_POST["opcion"]);
	
	switch ($opcion) {
		case "1": //Impresión de la hoja de stickers
			@$id_admision = $utilidades->str_decode($_POST["id_admision"]);
			@$id_hc = $utilidades->str_decode($_POST["id_hc"]);
			
			//Se obtienen los datos de la admisión y del paciente
			$admision_obj = $dbAdmision->get_admision($id_admision);
			$paciente_obj = $dbPacientes->getPaciente($admision_obj["id_paciente"]);
			
			$pdf = new FPDF('P', 'mm', array(216, 279));
			$pdf->SetAutoPageBreak(true, 10);
			
			$pdf->AliasNbPages();
			$pdf->AddPage();
			
			//Logo
			$pdf->Image('../imagenes/logo-color.png', 10, 10, 55);
			$pdf->Cell(70, 5, "", 0, 0, 'C');
			$pdf->SetFont("Arial", "", 11);
			$pdf->Cell(55, 5, $admision_obj["fecha_admision_t"], 0, 0, 'C');
			$pdf->SetFont("Arial", "B", 11);
			$pdf->Cell(70, 5, ajustarCaracteres("Historia Clínica"), 0, 1, 'R');
			
			$pdf->SetFont("Arial", "", 11);
			$pdf->Cell(125, 5, "", 0, 0, 'C');
			$pdf->Cell(70, 5, ajustarCaracteres("No. ".$paciente_obj["numero_documento"]), 0, 1, 'R');
			
			$pdf->SetY(35);
			
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(35, 5, ajustarCaracteres("Paciente"), 0, 0, 'R');
			$nombre_completo = $funcionesPersona->obtenerNombreCompleto($paciente_obj["nombre_1"], $paciente_obj["nombre_2"], $paciente_obj["apellido_1"], $paciente_obj["apellido_2"]);
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(63, 5, ajustarCaracteres($nombre_completo), 0, 0, 'L');
			
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(35, 5, ajustarCaracteres($paciente_obj["tipo_documento"]), 0, 0, 'R');
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(62, 5, ajustarCaracteres($paciente_obj["numero_documento"]), 0, 1, 'L');
			
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(35, 5, ajustarCaracteres("Convenio"), 0, 0, 'R');
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(63, 5, ajustarCaracteres($admision_obj["nombre_convenio"]), 0, 0, 'L');
			
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(35, 5, ajustarCaracteres("Plan"), 0, 0, 'R');
			$pdf->SetFont("Arial", "", 9);
			$pdf->MultiCell(62, 5, ajustarCaracteres($admision_obj["nombre_plan"]), 0, 'L');
			
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(35, 5, ajustarCaracteres("Acompañante"), 0, 0, 'R');
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(63, 5, ajustarCaracteres($admision_obj["nombre_acompa"]), 0, 0, 'L');
			
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(35, 5, ajustarCaracteres("Estado civil"), 0, 0, 'R');
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(62, 5, ajustarCaracteres($paciente_obj["estado_civil"]), 0, 1, 'L');
			
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(35, 5, ajustarCaracteres("Fecha de nacimiento"), 0, 0, 'R');
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(30, 5, ajustarCaracteres($paciente_obj["fecha_nacimiento_t"]), 0, 0, 'L');
			
			$arr_edad = explode("/", $paciente_obj["edad"]);
			$texto_edad = $arr_edad[0];
			if (isset($arr_edad[1])) {
				switch ($arr_edad[1]) {
					case "1":
						$texto_edad .= " a&ntilde;os";
						break;
					case "2":
						$texto_edad .= " meses";
						break;
					case "3":
						$texto_edad .= " d&iacute;as";
						break;
				}
			}
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(15, 5, ajustarCaracteres("Edad"), 0, 0, 'R');
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(18, 5, ajustarCaracteres($texto_edad), 0, 0, 'L');
			
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(35, 5, ajustarCaracteres("Sexo"), 0, 0, 'R');
			$pdf->SetFont("Arial", "", 9);
			$pdf->MultiCell(62, 5, ajustarCaracteres($paciente_obj["sexo_t"]), 0, 'L');
			
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(35, 5, ajustarCaracteres("No. hijos / hijas"), 0, 0, 'R');
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(30, 5, ajustarCaracteres($admision_obj["numero_hijos"]." / ".$admision_obj["numero_hijas"]), 0, 0, 'L');
			
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(15, 5, ajustarCaracteres("No. hnos / hnas"), 0, 0, 'R');
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(18, 5, ajustarCaracteres($admision_obj["numero_hermanos"]." / ".$admision_obj["numero_hermanas"]), 0, 0, 'L');
			
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(35, 5, ajustarCaracteres("Profesión"), 0, 0, 'R');
			$pdf->SetFont("Arial", "", 9);
			$pdf->MultiCell(62, 5, ajustarCaracteres($paciente_obj["profesion"]), 0, 'L');
			
			$y_aux = $pdf->GetY();
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(35, 5, ajustarCaracteres("Dirección"), 0, 0, 'R');
			$pdf->SetFont("Arial", "", 9);
			$pdf->MultiCell(63, 5, ajustarCaracteres($paciente_obj["direccion"]), 0, 'L');
			$y_aux2 = $pdf->GetY();
			
			$pdf->SetXY(108, $y_aux);
			$municipio_aux = "";
			if ($paciente_obj["nom_mun"] != "") {
				$municipio_aux = $paciente_obj["nom_mun"].", ".$paciente_obj["nom_dep"];
			} else if ($paciente_obj["cod_dep"] != "68") {
				$municipio_aux = $paciente_obj["nom_mun_t"].", ".$paciente_obj["nom_dep_t"];
			} else {
				$municipio_aux = $paciente_obj["nom_mun_t"];
			}
			if ($paciente_obj["id_pais"] != "1") {
				$municipio_aux .= " (".$paciente_obj["id_pais"].")";
			}
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(35, 5, ajustarCaracteres("Municipio"), 0, 0, 'R');
			$pdf->SetFont("Arial", "", 9);
			$pdf->MultiCell(62, 5, ajustarCaracteres($municipio_aux), 0, 'L');
			
			if ($pdf->GetY() < $y_aux2) {
				$pdf->SetY($y_aux2);
			}
			
			$telefono_aux = $paciente_obj["telefono_1"];
			if ($paciente_obj["telefono_2"] != "") {
				$telefono_aux .= " - ".$paciente_obj["telefono_2"];
			}
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(35, 5, ajustarCaracteres("Teléfono(s)"), 0, 0, 'R');
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(63, 5, ajustarCaracteres($telefono_aux), 0, 0, 'L');
			
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(35, 5, ajustarCaracteres("Tipo de cita"), 0, 0, 'R');
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(62, 5, ajustarCaracteres($admision_obj["nombre_tipo_cita"]), 0, 1, 'L');
			$pdf->Ln(3);
			
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(35, 5, ajustarCaracteres("Motivo de la consulta"), 0, 0, 'R');
			$pdf->SetFont("Arial", "", 9);
			$pdf->MultiCell(160, 5, ajustarCaracteres($admision_obj["motivo_consulta"]), 0, 'L');
			$pdf->Ln(20);
			
			$pdf->Cell(25, 55, "", 0, 0, 'C');
			$pdf->Cell(145, 55, "", 1, 1, 'C');
			
			//Se guarda el documento pdf
			$nombreArchivo = "../tmp/hoja_stickers_".$_SESSION["idUsuario"].".pdf";
			$pdf->Output($nombreArchivo, "F");
?>
<input type="hidden" id="hdd_ruta_stickers_pdf" value="<?php echo($nombreArchivo); ?>" />
<?php
			break;
	}
?>
