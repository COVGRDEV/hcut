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
require_once("../db/DbListas.php");
require_once("../funciones/Utilidades.php");
require_once("../db/DbConsultaOptometria.php");
require_once("../db/DbConsultaControlOptometria.php");
require_once("../funciones/pdf/fpdf.php");
require_once("../funciones/pdf/makefont/makefont.php");
require_once("../funciones/pdf/funciones.php");
require_once("../funciones/pdf/WriteHTML.php");

$dbAdmision = new DbAdmision();
$dbPacientes = new DbPacientes();
$dbUsuarios = new DbUsuarios();
$dbHistoriaClinica = new DbHistoriaClinica();
$dbListas = new DbListas();
$utilidades = new Utilidades();
$db_consulta_optometria = new DbConsultaOptometria();
$dbControlOpt = new DbConsultaControlOptometria();
$opcion = $utilidades->str_decode($_POST["opcion"]);


switch ($opcion) {
    case "1": //Se imprime la fórmula de gafas
	
	
		@$hdd_id_hc_consulta = $utilidades->str_decode($_POST["hdd_id_hc_consulta"]);
		@$id_admision = $utilidades->str_decode($_POST["id_admision"]);
        @$hdd_nombre_paciente = $utilidades->str_decode($_POST["hdd_nombre_paciente"]);
        @$hdd_fecha_admision = $utilidades->str_decode($_POST["hdd_fecha_admision"]);
        @$esfera_od = $utilidades->str_decode($_POST["esfera_od"]);
        @$cilindro_od = $utilidades->str_decode($_POST["cilindro_od"]);
        @$eje_od = $utilidades->str_decode($_POST["eje_od"]);
        @$adicion_od = $utilidades->str_decode($_POST["adicion_od"]);
        @$esfera_oi = $utilidades->str_decode($_POST["esfera_oi"]);
        @$cilindro_oi = $utilidades->str_decode($_POST["cilindro_oi"]);
        @$eje_oi = $utilidades->str_decode($_POST["eje_oi"]);
        @$adicion_oi = $utilidades->str_decode($_POST["adicion_oi"]);
        @$observacion = $utilidades->str_decode($_POST["observacion"]);
        @$nombre_profesional_optometra = $utilidades->str_decode($_POST["hdd_nombre_profesional_optometra"]);
		@$tipo_impresion = $utilidades->str_decode($_POST["tipo_impresion"]);
		
		$consulta_optometria_obj = array();
        $tipo_lente = "";
			
        if (isset($_POST["tipo_lente"])) {
            @$tipo_lente = $utilidades->str_decode($_POST["tipo_lente"]);
        }
		
		if($tipo_impresion==1){//Consultas optometrías.
			$consulta_optometria_obj = $db_consulta_optometria->getConsultaOptometria($hdd_id_hc_consulta);
		}else if($tipo_impresion==2){//Control optometrías.
			$consulta_optometria_obj = $dbControlOpt->getConsultaControlOptometria($hdd_id_hc_consulta);
		}
			
	
        //Si el número de admisión no es vacío, se buscan los datos de los profesionales de la admisión
        $nombre_usuario_prof_oft = "";
        $usuario_prof_opt_obj = array();

        $tipo_documento = "";
        $numero_documento = "";
        if (trim($id_admision) != "") {
            $admision_obj = $dbAdmision->get_admision($id_admision);
            $id_usuario_prof_oft = $admision_obj["id_usuario_prof"];

            //Se buscan los datos del profesional principal
            $usuario_prof_oft_obj = $dbUsuarios->getUsuario($id_usuario_prof_oft);
            if ($usuario_prof_oft_obj["ind_anonimo"] == "1") {
                //Se trata de un usuario anónimo, se busca en la historia clínica el nombre del usuario
                $lista_historia_clinica_aux = $dbHistoriaClinica->getListaHistoriaClinicaAdmUsuario($id_admision, $id_usuario_prof_oft);
                if (count($lista_historia_clinica_aux) > 0) {
                    $nombre_usuario_prof_oft = $lista_historia_clinica_aux[0]["nombre_usuario_alt"];
                }
            } else {
                $nombre_usuario_prof_oft = $usuario_prof_oft_obj["nombre_usuario"] . " " . $usuario_prof_oft_obj["apellido_usuario"];
            }

            //Se buscan los datos del profesional optómetra
            $usuario_prof_opt_obj = $dbUsuarios->getUsuarioOptometra($id_admision);

            //Se buscan los datos del paciente
            $paciente_obj = $dbPacientes->getExistepaciente3($admision_obj["id_paciente"]);
            $tipo_documento = $paciente_obj["tipodocumento"];
            $numero_documento = $paciente_obj["numero_documento"];
			
			//Se busca la imagen de logo de la sede de la admisión
			$sede_det_obj = $dbListas->getSedesDetalle($admision_obj["id_lugar_cita"]);
			$img_logo = $sede_det_obj["dir_logo_sede_det"];
        } else {
			$img_logo = "../imagenes/LogoUT.jpg";
		}
		
		$tipo_usuario_t = " ";	
			if($paciente_obj["tipo_coti_paciente"]<=3){
				switch($paciente_obj["tipo_coti_paciente"]){
					case 0:
						$tipo_usuario_t = "NO APLICA ";
						break;
					case 1: 
						$tipo_usuario_t = "COTIZANTE";
						break; 
					case 2: 
						$tipo_usuario_t = "BENEFICIARIO";
						break;	
					case 3: 
						$tipo_usuario_t = "SUBSIDIADO";
						break;	
				}				
			}else{
				$tipo_usuario_t = $paciente_obj["tipo_usuario_t"];
			}

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
		
		$text_logo_sede_aux ="";
		if($consulta_optometria_obj["id_lugar_cita"] == 465 ){
			$text_logo_sede_aux = " CENTRO OFTALMOLÓGICO SAN GIL ";
		}else{ $text_logo_sede_aux = "CENTRO OFTALMOLÓGICO VIRGILIO GALVIS";}
		
		//echo($consulta_optometria_obj["lugar_cita"] == 465);
		//Logo
		$pdf->Image($img_logo, 10, 8, $ancho_aux, $alto_aux);
		
		$pdf->SetY(15);
		$pdf->SetX(90);
		$pdf->SetFont("Arial", "B", 10);
		$pdf->Cell(0, 4, ajustarCaracteres($text_logo_sede_aux), "C", "C", "C" );
		$pdf->SetY(18);
		$pdf->SetX(90);
		$pdf->SetFont("Arial", "", 7);
		$pdf->Cell(0, 4, ajustarCaracteres($consulta_optometria_obj["dir_sede_det"]), "C","C","C");
		$pdf->SetY(21);
		$pdf->SetX(90);
		$pdf->SetFont("Arial", "", 7);
		$pdf->Cell(0, 4, ajustarCaracteres($consulta_optometria_obj["tel_sede_det"]), "C","C","C");
		$pdf->SetY(24);
		$pdf->SetX(90);
		$pdf->SetFont("Arial", "", 7);
		$pdf->Cell(0, 4, ajustarCaracteres($consulta_optometria_obj["email_sede_det"]), "C","C","C");
		
        $pdf->SetX(10);
        $pdf->SetY(31);
		
        $pdf->SetFont("Arial", "B", 9);
        $pdf->Cell(10, 4, ajustarCaracteres("Sede:"), 0, 0, "L");
        $pdf->SetFont("Arial", "", 9);
        $pdf->Cell(50, 4, ajustarCaracteres($consulta_optometria_obj["lugar_cita"]), 0, 0, "L");
        
		$pdf->SetFont("Arial", "B", 9);
        $pdf->Cell(15, 4, ajustarCaracteres("Nombre:"), 0, 0, "L");
        $pdf->SetFont("Arial", "", 9);
        $pdf->Cell(60, 4, ajustarCaracteres($utilidades->convertir_a_mayusculas($hdd_nombre_paciente)), 0, 0, "L");
		
		$pdf->SetFont("Arial", "B", 9);
        $pdf->Cell(15, 4, ajustarCaracteres("Fecha:"), 0, 0, "L");
        $pdf->SetFont("Arial", "", 9);
        $pdf->Cell(20, 4, ajustarCaracteres($hdd_fecha_admision), 0, 0, "L");	
		
		
		$pdf->Ln(5);
			
		
		$pdf->SetFont("Arial", "B", 9);
        $pdf->Cell(15, 4, ajustarCaracteres("No. HC: "), 0, 0, "L");
		$pdf->SetFont("Arial", "", 9);
		$pdf->Cell(45, 4, ajustarCaracteres($numero_documento), 0, 0, "L");
				
		if ($tipo_documento != "" && $numero_documento != "") {
            $pdf->SetFont("Arial", "B", 9);
            $pdf->Cell(35, 4, ajustarCaracteres($tipo_documento . ":"), 0, 0, "L");
            $pdf->SetFont("Arial", "", 9);
            $pdf->Cell(40, 4, ajustarCaracteres($numero_documento), 0, 0, "L");
        }
		//$pdf->Cell(78, 0, " ", 0, 0);
		$pdf->SetFont("Arial", "B", 9);
		$pdf->Cell(21, 4, ajustarCaracteres("Tipo usuario: "), 0, 0, 'L');
		$pdf->SetFont("Arial", "", 9);
		$pdf->Cell(20, 4, ajustarCaracteres($tipo_usuario_t), 0, 1, 'L');
		
		$pdf->Ln(1);
        $x_aux = $pdf->GetX();
        $y_aux = $pdf->GetY();

        $pdfHTML->WriteHTML(ajustarCaracteres("<b>Observaciones / Formas de uso:</b>&nbsp;&nbsp;&nbsp;&nbsp;" . ($observacion != "" ? $observacion : "-")), $pdf);
        $pdf->SetY($y_aux + 13);

        $pdf->SetX(15);
        $pdf->SetFont("Arial", "B", 9);
       	$pdf->Cell(17, 6, ajustarCaracteres("Lejos"), 0, 0, "C", true);
		$pdf->Cell(28, 6, ajustarCaracteres("Esfera"), 0, 0, "C", true);
		$pdf->Cell(28, 6, ajustarCaracteres("Cilindro"), 0, 0, "C", true);
		$pdf->Cell(28, 6, ajustarCaracteres("Eje"), 0, 0, "C", true);
		$pdf->Cell(28, 6, ajustarCaracteres("Adición"), 0, 0, "C", true);
		$pdf->Cell(28, 6, ajustarCaracteres("AV Lejos"), 0, 0, "C", true);
		$pdf->Cell(28, 6, ajustarCaracteres("AV Cerca"), 0, 1, "C", true);
			
        $pdf->SetFont("Arial", "", 9);

        $pdf->SetX(15);
        $pdf->SetFont("Arial", "B", 9);
        $pdf->Cell(17, 6, ajustarCaracteres("O.D."), 0, 0, "C", true);
        $pdf->SetFont("Arial", "", 9);
        $pdf->Cell(28, 6, ajustarCaracteres($esfera_od), 1, 0, "C", true);
        $pdf->Cell(28, 6, ajustarCaracteres($cilindro_od), 1, 0, "C", true);
        $pdf->Cell(28, 6, ajustarCaracteres($eje_od), 1, 0, "C", true);
        $pdf->Cell(28, 6, ajustarCaracteres($adicion_od), 1, 0, "C", true);
		$pdf->Cell(28, 6, ajustarCaracteres($consulta_optometria_obj["agudezaL_ojoD"] !="" ? $consulta_optometria_obj["agudezaL_ojoD"] : "-"), 1, 0, "C", true);
		$pdf->Cell(28, 6, ajustarCaracteres($consulta_optometria_obj["agudezaC_ojoD"] !="" ? $consulta_optometria_obj["agudezaC_ojoD"] : "-"), 1, 1 , "C", true);

        $pdf->SetX(15);
        $pdf->SetFont("Arial", "B", 9);
        $pdf->Cell(17, 6, ajustarCaracteres("O.I."), 0, 0, "C", true);
        $pdf->SetFont("Arial", "", 9);
        $pdf->Cell(28, 6, ajustarCaracteres($esfera_oi), 1, 0, "C", true);
        $pdf->Cell(28, 6, ajustarCaracteres($cilindro_oi), 1, 0, "C", true);
        $pdf->Cell(28, 6, ajustarCaracteres($eje_oi), 1, 0, "C", true);
        $pdf->Cell(28, 6, ajustarCaracteres($adicion_oi), 1, 0, "C", true);
		$pdf->Cell(28, 6, ajustarCaracteres($consulta_optometria_obj["agudezaL_ojoI"] !="" ? $consulta_optometria_obj["agudezaL_ojoI"] : "-"), 1, 0, "C", true);
		$pdf->Cell(28, 6, ajustarCaracteres($consulta_optometria_obj["agudezaC_ojoI"] !="" ? $consulta_optometria_obj["agudezaC_ojoI"] : "-"), 1, 1, "C", true);;

        // DATOS GENERALES DE LA PRESCRIPCIÓN MÉDICA
			$pdf->Ln(4);
			$x_aux = $pdf->GetX();
			$y_aux = $pdf->GetY();
						
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(16, 5, ajustarCaracteres("Tipo de lente seleccionado:"), "TBL", 0, 'L');
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(50, 5, $consulta_optometria_obj["slct_lente"] !="" ? $consulta_optometria_obj["slct_lente"] : "-", "TB", 0, 'R');
							
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(104, 5, ajustarCaracteres("Tipo filtro seleccionado:"), "TB", 0, 'R');
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(25, 5, $consulta_optometria_obj["slct_filtro"]  != "" ? $consulta_optometria_obj["slct_filtro"] : "-", "TR", 0, 'R');
			$pdf->Ln();
							
			$x_aux = $pdf->GetX();
			$y_aux = $pdf->GetY();
			$pdf->Line($x_aux, $y_aux, $x_aux + 195, $y_aux);
							
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(16, 5, ajustarCaracteres("Tiempo de vigencia de la formulación:"), 'TBL', 0, 'L');
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(58, 5, $consulta_optometria_obj["tiempo_vigencia"] !="" ? $consulta_optometria_obj["tiempo_vigencia"] : "-", "TB", 0, 'R');
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(99.5, 5, ajustarCaracteres("Periodo de la formulación:"), "TB", 0, 'R');
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(21.5, 5, $consulta_optometria_obj["periodo"]  != "" ? $consulta_optometria_obj["periodo"] : "-", "TR", 0, 'R');
			$pdf->Ln();
							
			$x_aux = $pdf->GetX();
			$y_aux = $pdf->GetY();
			$pdf->Line($x_aux, $y_aux, $x_aux + 195, $y_aux);
							
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(16, 5, ajustarCaracteres("Cantidad prescrita por el especialista:"), 'TBL', 0, 'L');
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(49, 5, $consulta_optometria_obj["form_cantidad"] !="" ? $consulta_optometria_obj["form_cantidad"] : "-", "TB", 0, 'R');
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(95, 5, ajustarCaracteres("Distancia pupilar:"), "TB", 0, 'R');
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(35, 5, $consulta_optometria_obj["distancia_pupilar"]  != "" ? $consulta_optometria_obj["distancia_pupilar"] : "-", "TR", 0, 'R');
			$pdf->Ln();
							
			$x_aux = $pdf->GetX();
			$y_aux = $pdf->GetY();
			$pdf->Line($x_aux, $y_aux, $x_aux + 195, $y_aux);
							
			$pdf->SetFont("Arial", "B",  9.5);
			$pdf->Cell(195, 5, ajustarCaracteres("Especificaciones del lente: "), 'LR', 1, 'L');
							
			$pdf->SetFont("Arial", "", 9);
			$texto_aux = ajustarCaracteres($consulta_optometria_obj["tipo_lente"]  != "" ? $consulta_optometria_obj["tipo_lente"] : "-");
			$pdf->Cell(195, 5, $texto_aux, 'LR', 1, 'L');
			
			$x_aux = $pdf->GetX();
			$y_aux = $pdf->GetY();
			$pdf->Line($x_aux, $y_aux, $x_aux + 195, $y_aux);
		
        $pdf->Ln(1);
        $pdf->SetFont("Arial", "B", 9);
        $pdf->Cell(195, 4, ajustarCaracteres("¡Muy Importante!"), 0, 1, "L", true);
        $pdf->SetFont("Arial", "", 7);
        $pdf->Cell(195, 3, ajustarCaracteres("Hemos formulado la mejor y m&aacute;s adecuada tecnolog&iacute;a en lentes para usted. A la &oacute;ptica, que vaya, demande excelente atenci&oacute;n y exija precisi&oacute;n y garant&iacute;a. No permita cambio"), 0, 1, "L", true);
        $pdf->Cell(195, 3, ajustarCaracteres("alguno en su f&oacute;rmula."), 0, 1, "L", true); 

        //Se agrega la firma del optómetra
        //$pdf->Cell(30, 0, imprimir_firma($usuario_prof_opt_obj["id_usuario"], $pdf, ""), 0, 1, "L", true);
        imprimir_firma($usuario_prof_opt_obj["id_usuario"], $pdf, "");

        //Se guarda el documento pdf
        $nombreArchivo = "../tmp/formula_gafas_" . $_SESSION["idUsuario"] . ".pdf";
        $pdf->Output($nombreArchivo, "F");
        ?>
        <input type="hidden" name="hdd_ruta_formula_gafas_pdf" id="hdd_ruta_formula_gafas_pdf" value="<?php echo($nombreArchivo); ?>" />
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

        //Se verifica si la firma cabe en la página, de no ser así, se inserta una nueva página
        $alto_total = $alto_aux + 0;
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
