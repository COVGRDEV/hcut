<?php
	session_start();
	
	require_once("../db/DbHistoriaClinica.php");
	require_once("../db/DbPacientes.php");
	require_once("../db/DbAdmision.php");
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbListas.php");
	require_once("../db/Dbvariables.php");
	require_once("../db/DbTiposRegistrosHc.php");
	require_once("../db/DbConsultasOftalmologiaRetina.php");
	require_once("../db/DbConsultasOculoplastia.php");
	require_once("../db/DbConsultasPterigio.php");
	require_once("../db/DbConsultasNeso.php");
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/FuncionesPersona.php");
	
	//Requires para FPDF
	require_once("../funciones/pdf/fpdf.php");
	require_once("../funciones/pdf/makefont/makefont.php");
	require_once("../funciones/pdf/funciones.php");
	require_once("../funciones/pdf/WriteHTML.php");
	require_once("../funciones/pdf/FPDI/fpdi.php");
	
    class PDF extends FPDI {
		public $nombre_tipo_reg;
		public $fecha_hc;
		public $nombre_completo;
		public $tipo_documento;
		public $numero_documento;
		public $nombre_convenio;
		public $nombre_plan;
		public $fecha_nacimiento_t;
		public $edad_texto;
		public $sexo_t;
		public $presion_arterial;
		public $pulso;
		public $nombre_acompa;
		public $angulo;
        public $dir_logo;
        public $dir_sede;
        public $tel_sede;
		public $edad_funcion;
		public $tipo_usuario_t;
		
		function __construct($orientation, $unit, $size) {
			parent::__construct($orientation, $unit, $size);
		}
		
		function setData($nombre_tipo_reg, $fecha_hc, $nombre_completo, $tipo_documento, $numero_documento, $nombre_convenio, $nombre_plan, $fecha_nacimiento_t, $edad_texto, $sexo_t, $presion_arterial, $pulso, $nombre_acompa, $dirLogo, $dir_sede, $tel_sede, $edad_funcion, $tipo_usuario_t) {
			$this->nombre_tipo_reg = $nombre_tipo_reg;
			$this->fecha_hc = $fecha_hc;
			$this->nombre_completo = $nombre_completo;
			$this->tipo_documento = $tipo_documento;
			$this->numero_documento = $numero_documento;
			$this->nombre_convenio = $nombre_convenio;
			$this->nombre_plan = $nombre_plan;
			$this->edad_texto = $edad_texto;
			$this->fecha_nacimiento_t = $fecha_nacimiento_t;
			$this->sexo_t = $sexo_t;
			$this->presion_arterial = $presion_arterial;
			$this->pulso = $pulso;
			$this->nombre_acompa = $nombre_acompa;
			$this->dir_logo = $dirLogo;
			$this->dir_sede = $dir_sede;
			$this->tel_sede = $tel_sede;  
			$this->edad_funcion = $edad_funcion;   
			$this->tipo_usuario_t = $tipo_usuario_t;                                            
		}
		
		function Header() {
			$funciones_persona = new FuncionesPersona();
			
			//Logo
            if (strlen($this->dir_logo) > 0) {
				$this->Image($this->dir_logo, 10, 10, 55);
			}
			
			$this->SetFont('Arial', 'B', 9);
			$this->SetY(10);
			
			// Movernos a la derecha
			$this->Cell(65, 4);
			// Texto de encabezado
			$this->Cell(130, 4, ajustarCaracteres($this->nombre_completo), 0, 0, 'L');
			$this->Ln();
			$this->Cell(65, 4);
			$this->Cell(65, 4, ajustarCaracteres($this->tipo_documento.': '.$this->numero_documento), 0, 0, 'L');
			$this->Cell(65, 4, ajustarCaracteres('Fecha: '.$funciones_persona->obtenerFecha6($this->fecha_hc)), 0, 0, 'L');
			$this->Ln();
			
			$texto_aux = "";
			if ($this->presion_arterial != "") {
				$texto_aux = 'Presión arterial: '.$this->presion_arterial."   ";
			}
			if ($this->pulso != "") {
				$texto_aux .= 'Pulso: '.$this->pulso;
			}
			
			
			if ($this->nombre_convenio != "" || $texto_aux != "") {
				$this->Cell(65, 4);
				if ($this->nombre_convenio != "") {
					$this->Cell(65, 4, ajustarCaracteres($this->nombre_convenio.' / '.$this->nombre_plan), 0, 0, 'L');
				} else {
					$this->Cell(65, 4);
				}
				$this->Cell(65, 4, ajustarCaracteres($texto_aux), 0, 0, 'L', true);
				$this->Ln();
			}
			$this->Cell(65, 4);
			$this->Cell(65, 4, ajustarCaracteres('Fecha de nacimiento: '.$funciones_persona->obtenerFecha6($this->fecha_nacimiento_t)), 0, 0, 'L');
			//$this->Cell(33, 4, ajustarCaracteres('Edad: '.$this->edad_texto), 0, 0, 'L');
			$this->Cell(33, 4, ajustarCaracteres('Edad: '.$this->edad_funcion), 0, 0, 'L');
			$this->Cell(32, 4, ajustarCaracteres('Sexo: '.$this->sexo_t), 0, 0, 'L');
			$this->Ln(8);
			$this->SetFont('Arial', 'B', 10);
			$this->Cell(130, 0, ajustarCaracteres('Historia Clínica Electrónica'), 0, 0, 'L');
		}
		
		// Pie de página
		function Footer() {
                    $this->Ln();
                    $this->SetFont("Arial", "", 7);
                    $this->Cell(192, 4, ajustarCaracteres($this->dir_sede." - ". $this->tel_sede), 0, 1, "C");
                    $this->SetFont("Arial", "I", 7);
                    $this->Cell(96, 4, ajustarCaracteres("Fecha y hora de impresión: " . date("Y/m/d H:i:s")), 0, 0, "L");
                    $this->Cell(96, 4, ajustarCaracteres("Página " . $this->PageNo() . "/{nb}"), 0, 1, "R");
		}
		
		function setDefaultLine() {
			$this->SetLineWidth(0.1);
			$this->SetDrawColor(0, 0, 0);
		}
		
		function setSeparationLine() {
			$this->SetLineWidth(0.1);
			$this->SetDrawColor(120, 120, 120);
		}
		
		function validateSpaceHeight($neededHeight) {
			$y_aux = $this->GetY();
			if ($y_aux + $neededHeight > 269) {
				$this->AddPage();
				$y_aux = 31;
			}
			
			return $y_aux;
		}
		
		function rotate($angle, $x= -1, $y= -1) {
			if ($x == -1)
				$x = $this->x;
			if ($y == -1)
				$y = $this->y;
			if ($this->angle != 0)
				$this->_out('Q');
			$this->angle = $angle;
			if ($angle != 0) {
				$angle *= M_PI/180;
				$c = cos($angle);
				$s = sin($angle);
				$cx = $x * $this->k;
				$cy = ($this->h - $y) * $this->k;
				$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
			}
		}
		
		function _endpage() {
			if (@$this->angle != 0) {
				@$this->angle = 0;
				$this->_out('Q');
			}
			parent::_endpage();
		}
	}
	
	function imprimir_encabezado($historia_clinica_obj, $pdf) {
		$db_pacientes = new DbPacientes();
		$db_admision = new DbAdmision();
		$funciones_persona = new FuncionesPersona();
		$utilidades = new Utilidades();
		
		$id_paciente = $historia_clinica_obj["id_paciente"];
		$id_admision = $historia_clinica_obj["id_admision"];
		$nombre_tipo_reg = $historia_clinica_obj["nombre_tipo_reg"];
		
		//Se obtienen los datos del paciente y la admision
		$paciente_obj = $db_pacientes->getPaciente($id_paciente, $historia_clinica_obj["fecha_hora_hc"]);
		$admision_obj = $db_admision->get_admision($id_admision);
		
		//Se obtiene el nombre completo y la edad
		$nombre_completo = "";
		$edad_texto = "";
		$edad_funcion = "";
		if (isset($paciente_obj["edad"])) {
			$nombre_completo = $funciones_persona->obtenerNombreCompleto($paciente_obj["nombre_1"], $paciente_obj["nombre_2"], $paciente_obj["apellido_1"], $paciente_obj["apellido_2"]);
			
			$arr_edad_aux = explode("/", $paciente_obj["edad"]);
			$edad_texto = $funciones_persona->obtenerEdad2($arr_edad_aux[0], $arr_edad_aux[1]);
		}
		
		$fecha_hc = $historia_clinica_obj["fecha_hc_t"] . " " . $historia_clinica_obj["hora_hc_t"];
		
			/** Se halla la edad del paciente con respecto a la historia clínica **/
			$fecha_hc_aux = $historia_clinica_obj["fecha_hc_t"];	
			$fecha_hc_aux =  explode( '/', $fecha_hc_aux);
			$dia = $fecha_hc_aux[0]; $mes = $fecha_hc_aux[1]; $año = $fecha_hc_aux[2];
			$fecha_hc_aux = $año."-".$mes."-".$dia;
			$fecha_hc_aux = date("Y-m-d", strtotime($fecha_hc_aux));
			
			$fecha_nacimiento_paciente = $paciente_obj["fecha_nacimiento_t"];
			$fecha_nacimiento_paciente = explode( '/', $fecha_nacimiento_paciente);
			$dia = $fecha_nacimiento_paciente[0]; $mes = $fecha_nacimiento_paciente[1]; $año = $fecha_nacimiento_paciente[2];
			$fecha_nacimiento_paciente = $año."-".$mes."-".$dia;
			$fecha_nacimiento_paciente = date("Y-m-d", strtotime($fecha_nacimiento_paciente));
			
			$edad_funcion_arr =  $db_pacientes->getEdad_HC($fecha_nacimiento_paciente,$fecha_hc_aux);
			$edad_funcion_arr = $edad_funcion_arr["edad"];
			$edad_funcion_arr = explode("/", $edad_funcion_arr);
			$edad_funcion = $funciones_persona->obtenerEdad2($edad_funcion_arr[0],$edad_funcion_arr[1]);
			
			$tipo_usuario_t = "";	
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
		
		/* Encabezado FPDF */
		if (isset($pdf)) {
			$pdf->setData($nombre_tipo_reg, $fecha_hc, $utilidades->convertir_a_mayusculas($nombre_completo), $paciente_obj["tipo_documento"], $paciente_obj["numero_documento"], $admision_obj["nombre_convenio"], $admision_obj["nombre_plan"], $paciente_obj["fecha_nacimiento_t"], $edad_texto, $paciente_obj["sexo_t"], $admision_obj["presion_arterial"], $admision_obj["pulso"], $admision_obj["nombre_acompa"], $admision_obj["dir_logo_sede_det"], $admision_obj["dir_sede_det"], $admision_obj["tel_sede_det"], $edad_funcion, 
$tipo_usuario_t);
			$pdf->AliasNbPages();
			$pdf->AddPage();
			
			$pdf->setXY(10, 30);
		}
	}
	
	function imprimir_antecedentes($id_hc, $texto_antecedentes, $pdf, $pdfHTML) {
		require_once("../db/DbAntecedentes.php");
		$dbAntecedentes = new DbAntecedentes();
		
		$pdf->SetFont("Arial", "B", 10);
		$pdf->Cell(195, 5, "Antecedentes", 0, 1, 'C');
		$pdf->Ln(1);
		
		$pdf->SetFont("Arial", "", 9);
		
		if ($texto_antecedentes != "") {
			$texto_aux = ajustarCaracteres(trim($texto_antecedentes));
			$pdfHTML->WriteHTML($texto_aux, $pdf);
			$pdf->Ln(3);
		}
		
		$lista_antecedentes = $dbAntecedentes->get_lista_antecedentes_medicos_hc2($id_hc);
		$texto_antecedentes = "";
		foreach ($lista_antecedentes as $antecedente_aux) {
			$texto_antecedentes .= ajustarCaracteres("<p><b>".$antecedente_aux["titulo_antecedente_medico"].": </b>".$antecedente_aux["texto_antecedente"]."</p>");
		}
		$pdfHTML->WriteHTML($texto_antecedentes, $pdf);
	}
	
	function imprimir_tonometrias($id_hc, $id_paciente, $id_admision, $pdf) {
		require_once("../db/DbConsultaOftalmologia.php");
		$db_consulta_oftalmologia = new DbConsultaOftalmologia();
		
		//Presión intraocular neumática
		$consulta_optometria_obj = $db_consulta_oftalmologia->getOptometriaPaciente($id_paciente, $id_admision);
		if (isset($consulta_optometria_obj["id_hc"])) {
			$presion_intraocular_od = $consulta_optometria_obj["presion_intraocular_od"];
			$presion_intraocular_oi = $consulta_optometria_obj["presion_intraocular_oi"];
			
			//Se verifica si el cuadro de PIO cabe en la página, de no ser así, se inserta una nueva página
			$alto_total = 11;
			$pdf->validateSpaceHeight($alto_total);
			
			$y_aux = $pdf->GetY();
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(75, 5, "OD", "TBL", 0, 'C');
			$pdf->Cell(45, 5, "", "TB", 0, 'C');
			$pdf->Cell(75, 5, "OI", "TBR", 0, 'C');
			$pdf->Ln();
			
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(75, 5, ($presion_intraocular_od != "" ? $presion_intraocular_od." mmHg" : "-"), "BL", 0, 'C');
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(45, 5, ajustarCaracteres("Tonometría neumática"), "B", 0, 'C');
			$pdf->SetFont("Arial", "", 9);
			$pdf->Cell(75, 5, ($presion_intraocular_oi != "" ? $presion_intraocular_oi." mmHg" : "-"), "BR", 1, 'C');
			$pdf->Ln(2);
		}
		
		//Tonometría aplanática
		$lista_tonometria = $db_consulta_oftalmologia->getTonometria($id_hc);
		
		if (count($lista_tonometria) > 0) {
			//Se verifica si la tonometría aplanática cabe en la página, de no ser así, se inserta una nueva página
			$alto_total = 11 + count($lista_tonometria) * 5;
			$pdf->validateSpaceHeight($alto_total);		
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(75, 5, "OD", "TBL", 0, 'C');
			$pdf->Cell(45, 5, ajustarCaracteres("Tonometría aplanática"), "TB", 0, 'C');
			$pdf->Cell(75, 5, "OI", "TBR", 0, 'C');
			$pdf->Ln();
			
			$pdf->Cell(38, 5, ajustarCaracteres("Sin dilatar"), "L", 0, 'C');
			$pdf->Cell(37, 5, ajustarCaracteres("Dilatado"), 0, 0, 'C');
			$pdf->Cell(45, 5, ajustarCaracteres("Fecha/Hora"), 0, 0, 'C');
			$pdf->Cell(38, 5, ajustarCaracteres("Sin dilatar"), 0, 0, 'C');
			$pdf->Cell(37, 5, ajustarCaracteres("Dilatado"), "R", 1, 'C');
			
			$y_aux = $pdf->GetY();
			$pdf->setSeparationLine();
			$pdf->Line(10, $y_aux, 205, $y_aux);
			$pdf->setDefaultLine();
			
			$pdf->SetFont("Arial", "", 9);
			foreach ($lista_tonometria as $tonometria_aux) {
				$pdf->Cell(38, 5, ajustarCaracteres($tonometria_aux["tonometria_valor_od"]), "L", 0, 'C');
				$pdf->Cell(37, 5, ajustarCaracteres($tonometria_aux["tonometria_dilatado_od"]), 0, 0, 'C');
				$pdf->Cell(45, 5, ajustarCaracteres($tonometria_aux["fecha_hora_tonometria"]), 0, 0, 'C');
				$pdf->Cell(38, 5, ajustarCaracteres($tonometria_aux["tonometria_valor_oi"]), 0, 0, 'C');
				$pdf->Cell(37, 5, ajustarCaracteres($tonometria_aux["tonometria_dilatado_oi"]), "R", 1, 'C');
			}
			
			$y_aux = $pdf->GetY();
			$pdf->Line(10, $y_aux, 205, $y_aux);
			
			$pdf->Ln(2);
		}
	}
	
	function imprimir_diagnosticos($id_hc, $pdf, $ind_ojos) {
		require_once("../db/DbDiagnosticos.php");
		$db_diagnosticos = new DbDiagnosticos();
		$lista_diagnosticos = $db_diagnosticos->getHcDiagnostico($id_hc);
		
		//Se verifica si los diagnósticos caben en la página, de no ser así, se inserta una nueva página
		$alto_total = 12 + count($lista_diagnosticos) * 5;
		
		$pdf->validateSpaceHeight($alto_total);
		
		$pdf->SetFont("Arial", "B", 10);
		$pdf->Cell(195, 6, ajustarCaracteres("Diagnósticos"), "TLR", 1, 'C');
		$y_aux = $pdf->GetY();
		$pdf->setSeparationLine();
		$pdf->Line(10, $y_aux, 205, $y_aux);
		$pdf->setDefaultLine();
		$pdf->SetFont("Arial", "B", 9);
		$pdf->Cell(15, 5, ajustarCaracteres("Código"), "L", 0, 'L');
		if ($ind_ojos) {
			$pdf->Cell(170, 5, ajustarCaracteres("Diagnóstico"), 0, 0, 'L');
			$pdf->Cell(10, 5, ajustarCaracteres("Ojo"), "R", 0, 'L');
		} else {
			$pdf->Cell(180, 5, ajustarCaracteres("Diagnóstico"), "R", 0, 'L');
		}
		$pdf->Ln();
		
		$y_aux = $pdf->GetY();
		$pdf->setSeparationLine();
		$pdf->Line(10, $y_aux, 205, $y_aux);
		$pdf->setDefaultLine();
		$pdf->SetFont("Arial", "", 9);
		
		if (count($lista_diagnosticos) > 0) {
			foreach ($lista_diagnosticos as $diagAux) {
				$pdf->Cell(15, 5, ajustarCaracteres($diagAux["cod_ciex"]), "L", 0, 'L');
				if ($ind_ojos) {
					$pdf->Cell(170, 5, ajustarCaracteres($diagAux["nombre"]), 0, 0, 'L');
					$pdf->Cell(10, 5, ajustarCaracteres($diagAux["ojo"]), "R", 0, 'L');
				} else {
					$pdf->Cell(180, 5, ajustarCaracteres($diagAux["nombre"]), "R", 0, 'L');
				}
				$pdf->Ln();
			}
		} else {
			$pdf->Cell(195, 5, ajustarCaracteres("(Sin diagnósticos)"), "LR", 1, 'C');
		}
		
		//Se traza la línea de cierre
		$x_aux = $pdf->GetX();
		$y_aux = $pdf->GetY();
		$pdf->Line($x_aux, $y_aux, $x_aux + 195, $y_aux);
	}
	
	function imprimir_hc_procedimientos_solic($id_hc, $pdf, $ind_ojos) {
		require_once("../db/DbMaestroProcedimientos.php");
		$db_maestro_procedimientos = new DbMaestroProcedimientos();
		$lista_procedimientos_solic = $db_maestro_procedimientos->getListaHCProcedimientosSolic($id_hc);
		
		if (count($lista_procedimientos_solic) > 0) {
			//Se verifica si los procedimientos caben en la página, de no ser así, se inserta una nueva página
			$alto_total = 6 + count($lista_procedimientos_solic) * 5;
			
			$pdf->validateSpaceHeight($alto_total);
			
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(16, 5, ajustarCaracteres("Código"), "TL", 0, 'L');
			if ($ind_ojos) {
				$pdf->Cell(163, 5, ajustarCaracteres("Procedimiento/Examen"), "T", 0, 'L');
				$pdf->Cell(16, 5, ajustarCaracteres("Ojo"), "TR", 0, 'C');
			} else {
				$pdf->Cell(179, 5, ajustarCaracteres("Procedimiento/Examen"), "TR", 0, 'L');
			}
			$pdf->Ln();
			
			$y_aux = $pdf->GetY();
			$pdf->setSeparationLine();
			$pdf->Line(10, $y_aux, 205, $y_aux);
			$pdf->setDefaultLine();
			$pdf->SetFont("Arial", "", 9);
			
			foreach ($lista_procedimientos_solic as $proc_aux) {
				$pdf->Cell(16, 5, ajustarCaracteres($proc_aux["cod_procedimiento"]), "L", 0, 'L');
				if ($ind_ojos) {
					$pdf->Cell(163, 5, ajustarCaracteres($proc_aux["nombre_procedimiento"]), 0, 0, 'L');
					$pdf->Cell(16, 5, ajustarCaracteres($proc_aux["ojo"] != "" ? $proc_aux["ojo"] : "No aplica"), "R", 0, 'C');
				} else {
					$pdf->Cell(179, 5, ajustarCaracteres($proc_aux["nombre_procedimiento"]), "R", 0, 'L');
				}
				$pdf->Ln();
			}
			
			//Se traza la línea de cierre
			$x_aux = $pdf->GetX();
			$y_aux = $pdf->GetY();
			$pdf->Line($x_aux, $y_aux, $x_aux + 195, $y_aux);
			$pdf->Ln(2);
		}
	}
	
	function imprimir_formulacion_hc($id_hc, $pdf) {
		require_once("../db/DbFormulacionHC.php");
		$db_formulacion_hc = new DbFormulacionHC();
		$lista_formulacion_hc = $db_formulacion_hc->getListaFormulacionHC($id_hc);
		
		if (count($lista_formulacion_hc) > 0) {
			//Se verifica si los diagnósticos caben en la página, de no ser así, se inserta una nueva página
			$alto_total = 12 + count($lista_formulacion_hc) * 5;
			
			$pdf->validateSpaceHeight($alto_total);
			
			$pdf->SetFont("Arial", "B", 10);
			$pdf->Cell(195, 6, ajustarCaracteres("Formulación de medicamentos"), "TLR", 1, 'C');
			
			foreach ($lista_formulacion_hc as $formulacion_aux) {
				$y_aux = $pdf->GetY();
				$pdf->setSeparationLine();
				$pdf->Line(10, $y_aux, 205, $y_aux);
				$pdf->setDefaultLine();
				$pdf->SetFont("Arial", "", 9);
				
				$texto_aux = $formulacion_aux["nombre_medicamento"];
				if ($formulacion_aux["presentacion"] != "") {
					$texto_aux .= " - ".$formulacion_aux["presentacion"];
				}
				$texto_aux .= " - Cantidad: ".$formulacion_aux["cantidad"];
				if ($formulacion_aux["dosificacion"] != "" || $formulacion_aux["unidades"] != "" || $formulacion_aux["duracion"] != "") {
					$texto_aux .= " - ".$formulacion_aux["dosificacion"]." ".$formulacion_aux["unidades"]." ".$formulacion_aux["duracion"];
				}
				
				$pdf->MultiCell(195, 5, ajustarCaracteres($texto_aux), "LR", "L");
			}
			
			//Se traza la línea de cierre
			$pdf->setDefaultLine();
			$x_aux = $pdf->GetX();
			$y_aux = $pdf->GetY();
			$pdf->Line(10, $y_aux, 205, $y_aux);
			$pdf->Ln(2);
		}
	}
	
	function imprimir_firma($id_usuario_prof, $pdf, $nombre_usuario_alt) {
		$db_usuarios = new DbUsuarios();
		
		$usuario_obj = $db_usuarios->getUsuario($id_usuario_prof);
		$usuario_base = $usuario_obj;
		$ind_instructor = false;
		if ($usuario_obj["id_usuario_firma"] != "") {
			//El usuario que firma es otro, se cargan los datos de este usuario
			$usuario_obj = $db_usuarios->getUsuario($usuario_obj["id_usuario_firma"]);
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
			$y_aux = $pdf->validateSpaceHeight($alto_total);
			
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
				$pdf->Cell(50, 3, ajustarCaracteres("Atiende"), 0, 1, 'C');
				$pdf->SetFont("Arial", "", 9);
				$pdf->Cell(145, 3, "");
				$pdf->Cell(50, 3, ajustarCaracteres($nombre_usuario_alt), 0, 1, 'C');
			}
		}
	}
	
	function mapear_husos($neso_obj, $sufijo_ojo){
		$cadena="";
		$sufijo_ojo=strtolower($sufijo_ojo);
		for ($i=1; $i<=12; $i++) {
			switch ($i){
				case 1: $etiqueta="I"; break;
				case 2: $etiqueta="II"; break;
				case 3: $etiqueta="III"; break;
				case 4: $etiqueta="IV"; break;
				case 5: $etiqueta="V"; break;
				case 6: $etiqueta="VI"; break;
				case 7: $etiqueta="VII"; break;
				case 8: $etiqueta="VIII"; break;
				case 9: $etiqueta="IX"; break;
				case 10: $etiqueta="X"; break;
				case 11: $etiqueta="XI"; break;
				case 12: $etiqueta="XII"; break;
				default: ""; 
			} 
			$cadena .= $neso_obj["huso".$i."_".$sufijo_ojo] == 1 ? $etiqueta.", " : "";
		}
		$cadena=substr($cadena, 0, -2);	
		return $cadena;
	}	
	
	function imprimir_extension_consulta($tipo_registro_hc_obj, $id_hc, $pdf) {
		$pdfHTML = new PDF_HTML();

		switch ($tipo_registro_hc_obj["tipo_reg_adicional"]) {
			case "2": //Retina
				$db_consultas_oftalmologia_retina = new DbConsultasOftalmologiaRetina();
				$retina_obj = $db_consultas_oftalmologia_retina->getConsultaOftalmologiaRetina($id_hc);
				
				$pdf->SetFont("Arial", "B", 10);
				$pdf->Cell(195, 5, "Retina", 0, 1, "C");
				$pdf->Ln(1);
				
				$pdf->SetFont("Arial", "B", 10);
				$pdf->Cell(12, 5, ajustarCaracteres("Láser:"), 0, 0, "L");
				$pdf->SetFont("Arial", "", 9);
				$pdf->Cell(15, 5, ajustarCaracteres($retina_obj["ind_laser"] == "1" ? "Sí" : ($retina_obj["ind_laser"] == "0" ? "No" : "-")), 0, 1, "L");
				
				$pdf->SetFont("Arial", "B", 10);
				$pdf->Cell(43, 5, ajustarCaracteres("Inyecciones intravítreas:"), 0, 0, "L");
				$pdf->SetFont("Arial", "", 9);
				$pdf->Cell(15, 5, ajustarCaracteres($retina_obj["ind_intravitreas"] == "1" ? "Sí" : ($retina_obj["ind_intravitreas"] == "0" ? "No" : "-")), 0, 0, "L");
				if ($retina_obj["ind_intravitreas"] == "1") {
					$pdf->SetFont("Arial", "B", 10);
					$pdf->Cell(20, 5, ajustarCaracteres("Cantidad"), 0, 0, "L");
					$pdf->Cell(10, 5, ajustarCaracteres("OD:"), 0, 0, "R");
					$pdf->SetFont("Arial", "", 9);
					$pdf->Cell(6, 5, ajustarCaracteres($retina_obj["cant_intr_od"] != "" ? $retina_obj["cant_intr_od"] : "-"), 0, 0, "L");
					$pdf->SetFont("Arial", "B", 10);
					$pdf->Cell(10, 5, ajustarCaracteres("OI:"), 0, 0, "R");
					$pdf->SetFont("Arial", "", 9);
					$pdf->Cell(6, 5, ajustarCaracteres($retina_obj["cant_intr_oi"] != "" ? $retina_obj["cant_intr_oi"] : "-"), 0, 0, "L");
				}
				$pdf->Cell(1, 5, ajustarCaracteres(""), 0, 1, "L");
				
				$pdf->SetFont("Arial", "B", 10);
				$pdf->Cell(33, 5, ajustarCaracteres("Cirugías de retina:"), 0, 0, "L");
				$pdf->SetFont("Arial", "", 9);
				$pdf->Cell(15, 5, ajustarCaracteres($retina_obj["ind_cx_retina"] == "1" ? "Sí" : ($retina_obj["ind_cx_retina"] == "0" ? "No" : "-")), 0, 1, "L");
				if ($retina_obj["ind_cx_retina"] == "1") {
					$pdf->SetFont("Arial", "B", 9);
					$pdf->Cell(165, 5, ajustarCaracteres("Cirugía"), "TL", 0, "L");
					$pdf->Cell(30, 5, ajustarCaracteres("Fecha"), "TR", 1, "C");
					
					$y_aux = $pdf->GetY();
					$pdf->setSeparationLine();
					$pdf->Line(10, $y_aux, 205, $y_aux);
					$pdf->setDefaultLine();
					
					$pdf->SetFont("Arial", "", 9);
					$lista_retina_cx = $db_consultas_oftalmologia_retina->getListaConsultasOftalmologiaRetinaCx($id_hc);
					foreach ($lista_retina_cx as $retina_cx_aux) {
						$pdf->Cell(165, 5, ajustarCaracteres($retina_cx_aux["texto_cx"]), "L", 0, "L");
						$pdf->Cell(30, 5, ajustarCaracteres($retina_cx_aux["fecha_cx_t"] != "" ? $retina_cx_aux["fecha_cx_t"] : "-"), "R", 1, "C");
					}
					$y_aux = $pdf->GetY();
					$pdf->Line(10, $y_aux, 205, $y_aux);
				}
				
				$pdf->Ln(5);
				break;
				
			case "3": //Oculoplastia
				$db_consultas_oculoplastia = new DbConsultasOculoplastia();
				$oculoplastia_obj = $db_consultas_oculoplastia->getConsultaOculoplastia($id_hc);
				
				$pdf->SetFont("Arial", "B", 10);
				$pdf->Cell(195, 5, "Antecedentes de Oculoplastia", 0, 1, "C");
				$pdf->Ln(1);
				
				$pdf->SetFont("Arial", "B", 9);
				$pdf->Cell(170, 5, ajustarCaracteres("Antecedente"), "TL", 0, "L");
				$pdf->Cell(25, 5, ajustarCaracteres("Fecha"), "TR", 1, "C");
				
				$y_aux = $pdf->GetY();
				$pdf->setSeparationLine();
				$pdf->Line(10, $y_aux, 205, $y_aux);
				$pdf->setDefaultLine();
				
				$pdf->SetFont("Arial", "", 9);
				$ind_antec_ocp = false;
				$lista_oculoplastia_antec = $db_consultas_oculoplastia->getListaConsultasOculoplastiaAntec($id_hc);
				foreach ($lista_oculoplastia_antec as $antec_aux) {
					if ($antec_aux["texto_antec_ocp"] != "") {
						$pdf->Cell(170, 5, ajustarCaracteres($antec_aux["nombre_detalle"].": ".($antec_aux["texto_antec_ocp"] != "" ? $antec_aux["texto_antec_ocp"] : "-")), "L", 0, "L");
						$pdf->Cell(25, 5, ajustarCaracteres($antec_aux["fecha_antec_ocp_t"] != "" ? $antec_aux["fecha_antec_ocp_t"] : "-"), "R", 1, "C");
						
						$y_aux = $pdf->GetY();
						$pdf->setSeparationLine();
						$pdf->Line(10, $y_aux, 205, $y_aux);
						$pdf->setDefaultLine();
						
						$ind_antec_ocp = true;
					}
				}
				
				$lista_oculoplastia_compl = $db_consultas_oculoplastia->getListaConsultasOculoplastiaCompl($id_hc);
				$ind_compl_ocp = false;
				foreach ($lista_oculoplastia_compl as $compl_aux) {
					if ($compl_aux["ind_compl_ocp"] == 1) {
						$ind_compl_ocp = true;
						break;
					}
				}
				if ($ind_compl_ocp) {
					$texto_aux = "";
					foreach ($lista_oculoplastia_compl as $compl_aux) {
						if ($compl_aux["ind_compl_ocp"] == 1) {
							if ($texto_aux != "") {
								$texto_aux .= ", ";
							}
							$texto_aux .= $compl_aux["nombre_detalle"];
						}
					}
					
					$pdf->Cell(195, 5, ajustarCaracteres("Está tomando: ".$texto_aux), "LR", 1, "L");
				}
				if (!$ind_antec_ocp && !$ind_compl_ocp) {
					$pdf->Cell(195, 5, ajustarCaracteres("(Sin antecedentes)"), "LR", 1, "C");
				}
				
				$y_aux = $pdf->GetY();
				$pdf->Line(10, $y_aux, 205, $y_aux);
				
				$pdf->Ln(3);
				
				/**********/
				/**Órbita**/
				/**********/
				$pdf->SetFont("Arial", "B", 10);
				$pdf->Cell(195, 5, ajustarCaracteres("Órbita"), 1, 1, 'C');
				
				//Exoftalmometría
				$pdf->SetFont("Arial", "B", 9);
				$pdf->Cell(75, 5, "OD", "TBL", 0, "C");
				$pdf->Cell(45, 5, ajustarCaracteres("Exoftalmometría"), "TB", 0, "C");
				$pdf->Cell(75, 5, "OI", "TBR", 1, "C");
				
				$y_aux = $pdf->GetY();
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($oculoplastia_obj["exoftalmometria_od"] != "" ? $oculoplastia_obj["exoftalmometria_od"] : "-"), 0, 'C');
				$y_aux2 = $pdf->GetY();
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}
				
				$pdf->SetXY(130, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($oculoplastia_obj["exoftalmometria_oi"] != "" ? $oculoplastia_obj["exoftalmometria_oi"] : "-"), 0, 'C');
				if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
					$y_aux2 = $pdf->GetY();
				}
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}
				
				$pdf->SetXY(85, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Base: ".($oculoplastia_obj["exoftalmometria_base"] != "" ? $oculoplastia_obj["exoftalmometria_base"] : "-")), 0, 0, 'C');
				$pdf->Ln();
				
				$pdf->SetXY(10, $y_aux);
				$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 1, 'C');
				
				if (trim($oculoplastia_obj["observ_orbita"]) != "") {
					$pdf->SetFont("Arial", "", 9);
					$y_aux = $pdf->GetY();
					$pdf->setSeparationLine();
					$pdf->Line(10, $y_aux, 205, $y_aux);
					$pdf->setDefaultLine();
					$pdf->Ln(1);
					$texto_aux = ajustarCaracteres($oculoplastia_obj["observ_orbita"]);
					$pdfHTML->WriteHTML(ajustarCaracteres("<b>Observaciones: </b>").$texto_aux, $pdf);
					$pdf->Ln(3);
					$y_aux2 = $pdf->GetY();
					$pdf->SetY($y_aux);
					$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 0, 'L');
					$pdf->Ln();
				}
				$y_aux = $pdf->GetY();
				$pdf->Line(10, $y_aux, 205, $y_aux);
				$pdf->Ln(3);
				
				/*********/
				/**Cejas**/
				/*********/
				$pdf->SetFont("Arial", "B", 10);
				$pdf->Cell(195, 5, ajustarCaracteres("Cejas"), 1, 1, 'C');
				
				$pdf->SetFont("Arial", "", 9);
				$y_aux = $pdf->GetY();
				$pdf->setSeparationLine();
				$pdf->Line(10, $y_aux, 205, $y_aux);
				$pdf->setDefaultLine();
				$pdf->Ln(1);
				$texto_aux = ajustarCaracteres($oculoplastia_obj["observ_cejas"] != "" ? $oculoplastia_obj["observ_cejas"] : "<p>-</p>");
				$pdfHTML->WriteHTML(ajustarCaracteres("<b>Observaciones: </b>").$texto_aux, $pdf);
				$pdf->Ln(3);
				$y_aux2 = $pdf->GetY();
				$pdf->SetY($y_aux);
				$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 0, 'L');
				$pdf->Ln();
				
				$y_aux = $pdf->GetY();
				$pdf->Line(10, $y_aux, 205, $y_aux);
				$pdf->Ln(3);
				
				/************/
				/**Párpados**/
				/************/
				$pdf->SetFont("Arial", "B", 9);
				$pdf->Cell(75, 5, "OD", "TBL", 0, "C");
				$pdf->SetFont("Arial", "B", 10);
				$pdf->Cell(45, 5, ajustarCaracteres("Párpados"), "TB", 0, "C");
				$pdf->SetFont("Arial", "B", 9);
				$pdf->Cell(75, 5, "OI", "TBR", 1, "C");
				
				//Función elevador
				$y_aux = $pdf->GetY();
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($oculoplastia_obj["fme_od"] != "" ? $oculoplastia_obj["fme_od"] : "-"), 0, 'C');
				$y_aux2 = $pdf->GetY();
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}
				
				$pdf->SetXY(130, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($oculoplastia_obj["fme_oi"] != "" ? $oculoplastia_obj["fme_oi"] : "-"), 0, 'C');
				if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
					$y_aux2 = $pdf->GetY();
				}
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}
				
				$pdf->SetXY(85, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Función elevador (FME)"), 0, 0, 'C');
				$pdf->Ln();
				
				$pdf->SetXY(10, $y_aux);
				$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 1, 'C');
				
				$y_aux = $pdf->GetY();
				$pdf->setSeparationLine();
				$pdf->Line(10, $y_aux, 205, $y_aux);
				$pdf->setDefaultLine();
				
				//Distancia margen reflejo
				$y_aux = $pdf->GetY();
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($oculoplastia_obj["dmr_od"] != "" ? $oculoplastia_obj["dmr_od"] : "-"), 0, 'C');
				$y_aux2 = $pdf->GetY();
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}
				
				$pdf->SetXY(130, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($oculoplastia_obj["dmr_oi"] != "" ? $oculoplastia_obj["dmr_oi"] : "-"), 0, 'C');
				if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
					$y_aux2 = $pdf->GetY();
				}
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}
				
				$pdf->SetXY(85, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Distancia margen reflejo (DMR)"), 0, 0, 'C');
				$pdf->Ln();
				
				$pdf->SetXY(10, $y_aux);
				$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 1, 'C');
				
				$y_aux = $pdf->GetY();
				$pdf->setSeparationLine();
				$pdf->Line(10, $y_aux, 205, $y_aux);
				$pdf->setDefaultLine();
				
				//Respuesta fenilefrina
				$y_aux = $pdf->GetY();
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($oculoplastia_obj["fen_od"] != "" ? $oculoplastia_obj["fen_od"] : "-"), 0, 'C');
				$y_aux2 = $pdf->GetY();
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}
				
				$pdf->SetXY(130, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($oculoplastia_obj["fen_oi"] != "" ? $oculoplastia_obj["fen_oi"] : "-"), 0, 'C');
				if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
					$y_aux2 = $pdf->GetY();
				}
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}
				
				$pdf->SetXY(85, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Respuesta fenilefrina (FEN)"), 0, 0, 'C');
				$pdf->Ln();
				
				$pdf->SetXY(10, $y_aux);
				$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 1, 'C');
				
				$y_aux = $pdf->GetY();
				$pdf->setSeparationLine();
				$pdf->Line(10, $y_aux, 205, $y_aux);
				$pdf->setDefaultLine();
				
				if (trim($oculoplastia_obj["observ_parpados"]) != "") {
					$pdf->SetFont("Arial", "", 9);
					$y_aux = $pdf->GetY();
					$pdf->setSeparationLine();
					$pdf->Line(10, $y_aux, 205, $y_aux);
					$pdf->setDefaultLine();
					$pdf->Ln(1);
					$texto_aux = ajustarCaracteres($oculoplastia_obj["observ_parpados"]);
					$pdfHTML->WriteHTML(ajustarCaracteres("<b>Observaciones: </b>").$texto_aux, $pdf);
					$pdf->Ln(3);
					$y_aux2 = $pdf->GetY();
					$pdf->SetY($y_aux);
					$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 0, 'L');
					$pdf->Ln();
				}
				$y_aux = $pdf->GetY();
				$pdf->Line(10, $y_aux, 205, $y_aux);
				$pdf->Ln(3);
				
				/************/
				/**Pestañas**/
				/************/
				$pdf->SetFont("Arial", "B", 10);
				$pdf->Cell(195, 5, ajustarCaracteres("Pestañas"), 1, 1, 'C');
				
				$pdf->SetFont("Arial", "", 9);
				$y_aux = $pdf->GetY();
				$pdf->setSeparationLine();
				$pdf->Line(10, $y_aux, 205, $y_aux);
				$pdf->setDefaultLine();
				$pdf->Ln(1);
				$texto_aux = ajustarCaracteres($oculoplastia_obj["observ_pestanas"] != "" ? $oculoplastia_obj["observ_pestanas"] : "<p>-</p>");
				$pdfHTML->WriteHTML(ajustarCaracteres("<b>Observaciones: </b>").$texto_aux, $pdf);
				$pdf->Ln(3);
				$y_aux2 = $pdf->GetY();
				$pdf->SetY($y_aux);
				$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 0, 'L');
				$pdf->Ln();
				
				$y_aux = $pdf->GetY();
				$pdf->Line(10, $y_aux, 205, $y_aux);
				$pdf->Ln(3);
				
				/*************************/
				/**Glándulas de Meibomio**/
				/*************************/
				$pdf->SetFont("Arial", "B", 9);
				$pdf->Cell(75, 5, "OD", "TBL", 0, "C");
				$pdf->SetFont("Arial", "B", 10);
				$pdf->Cell(45, 5, ajustarCaracteres("Glándulas de Meibomio"), "TB", 0, "C");
				$pdf->SetFont("Arial", "B", 9);
				$pdf->Cell(75, 5, "OI", "TBR", 1, "C");
				
				//Expresibilidad
				$y_aux = $pdf->GetY();
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($oculoplastia_obj["gm_expresibilidad_od"] != "" ? $oculoplastia_obj["gm_expresibilidad_od"] : "-"), 0, 'C');
				$y_aux2 = $pdf->GetY();
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}
				
				$pdf->SetXY(130, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($oculoplastia_obj["gm_expresibilidad_oi"] != "" ? $oculoplastia_obj["gm_expresibilidad_oi"] : "-"), 0, 'C');
				if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
					$y_aux2 = $pdf->GetY();
				}
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}
				
				$pdf->SetXY(85, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Expresibilidad"), 0, 0, 'C');
				$pdf->Ln();
				
				$pdf->SetXY(10, $y_aux);
				$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 1, 'C');
				
				$y_aux = $pdf->GetY();
				$pdf->setSeparationLine();
				$pdf->Line(10, $y_aux, 205, $y_aux);
				$pdf->setDefaultLine();
				
				//Calidad de expresión
				$y_aux = $pdf->GetY();
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($oculoplastia_obj["gm_calidad_expr_od"] != "" ? $oculoplastia_obj["gm_calidad_expr_od"] : "-"), 0, 'C');
				$y_aux2 = $pdf->GetY();
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}
				
				$pdf->SetXY(130, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($oculoplastia_obj["gm_calidad_expr_oi"] != "" ? $oculoplastia_obj["gm_calidad_expr_oi"] : "-"), 0, 'C');
				if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
					$y_aux2 = $pdf->GetY();
				}
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}
				
				$pdf->SetXY(85, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Calidad de expresión"), 0, 0, 'C');
				$pdf->Ln();
				
				$pdf->SetXY(10, $y_aux);
				$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 1, 'C');
				
				$y_aux = $pdf->GetY();
				$pdf->setSeparationLine();
				$pdf->Line(10, $y_aux, 205, $y_aux);
				$pdf->setDefaultLine();
				
				if (trim($oculoplastia_obj["observ_glandulas_meib"]) != "") {
					$pdf->SetFont("Arial", "", 9);
					$y_aux = $pdf->GetY();
					$pdf->setSeparationLine();
					$pdf->Line(10, $y_aux, 205, $y_aux);
					$pdf->setDefaultLine();
					$pdf->Ln(1);
					$texto_aux = ajustarCaracteres($oculoplastia_obj["observ_glandulas_meib"]);
					$pdfHTML->WriteHTML(ajustarCaracteres("<b>Observaciones: </b>").$texto_aux, $pdf);
					$pdf->Ln(3);
					$y_aux2 = $pdf->GetY();
					$pdf->SetY($y_aux);
					$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 0, 'L');
					$pdf->Ln();
				}
				$y_aux = $pdf->GetY();
				$pdf->Line(10, $y_aux, 205, $y_aux);
				$pdf->Ln(3);
				
				/****************/
				/**Vía Lagrimal**/
				/****************/
				$pdf->SetFont("Arial", "B", 9);
				$pdf->Cell(75, 5, "OD", "TBL", 0, "C");
				$pdf->SetFont("Arial", "B", 10);
				$pdf->Cell(45, 5, ajustarCaracteres("Vía Lagrimal"), "TB", 0, "C");
				$pdf->SetFont("Arial", "B", 9);
				$pdf->Cell(75, 5, "OI", "TBR", 1, "C");
				
				//Prueba de irrigación
				$y_aux = $pdf->GetY();
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($oculoplastia_obj["prueba_irrigacion_od"] != "" ? $oculoplastia_obj["prueba_irrigacion_od"] : "-"), 0, 'C');
				$y_aux2 = $pdf->GetY();
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}
				
				$pdf->SetXY(130, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($oculoplastia_obj["prueba_irrigacion_oi"] != "" ? $oculoplastia_obj["prueba_irrigacion_oi"] : "-"), 0, 'C');
				if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
					$y_aux2 = $pdf->GetY();
				}
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}
				
				$pdf->SetXY(85, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Prueba de irrigación"), 0, 0, 'C');
				$pdf->Ln();
				
				$pdf->SetXY(10, $y_aux);
				$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 1, 'C');
				
				$y_aux = $pdf->GetY();
				$pdf->setSeparationLine();
				$pdf->Line(10, $y_aux, 205, $y_aux);
				$pdf->setDefaultLine();
				
				if (trim($oculoplastia_obj["observ_via_lagrimal"]) != "") {
					$pdf->SetFont("Arial", "", 9);
					$y_aux = $pdf->GetY();
					$pdf->setSeparationLine();
					$pdf->Line(10, $y_aux, 205, $y_aux);
					$pdf->setDefaultLine();
					$pdf->Ln(1);
					$texto_aux = ajustarCaracteres($oculoplastia_obj["observ_via_lagrimal"]);
					$pdfHTML->WriteHTML(ajustarCaracteres("<b>Observaciones: </b>").$texto_aux, $pdf);
					$pdf->Ln(3);
					$y_aux2 = $pdf->GetY();
					$pdf->SetY($y_aux);
					$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 0, 'L');
					$pdf->Ln();
				}
				$y_aux = $pdf->GetY();
				$pdf->Line(10, $y_aux, 205, $y_aux);
				$pdf->Ln(3);
				
				$pdf->Ln(5);
				break;
				
			case "4": //Pterigio
				$db_consultas_pterigio = new DbConsultasPterigio();				
				$db_listas = new DbListas;
				
				$pterigio_obj = $db_consultas_pterigio->getConsultaPterigio($id_hc);				
				
				$pdf->SetFont("Arial", "B", 10);
				$pdf->Cell(195, 5, "Pterigio", 0, 1, "C");
				$pdf->Ln(1);

				$pdf->SetFont("Arial", "B", 9);
				$pdf->Cell(75, 5, "OD", "B", 0, "C");
				$pdf->Cell(45, 5, ajustarCaracteres(" "), "B", 0, "C");
				$pdf->Cell(75, 5, "OI", "B", 1, "C");

				// Grado
				$detalle_lista = $db_listas->getDetalle($pterigio_obj["grado_od"]);
				$pterigio_obj["grado_od"] = $detalle_lista["nombre_detalle"];
				$y_aux = $pdf->GetY();
				$pdf->SetFont("Arial", "", 9);		
				$pdf->MultiCell(75, 5, ajustarCaracteres($pterigio_obj["grado_od"] != "" ? $pterigio_obj["grado_od"] : "-"), "BL", 'C');
				$y_aux2 = $pdf->GetY();
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}				
				
				$detalle_lista = $db_listas->getDetalle($pterigio_obj["grado_oi"]);
				$pterigio_obj["grado_oi"] = $detalle_lista["nombre_detalle"];
				$pdf->SetXY(130, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($pterigio_obj["grado_oi"] != "" ? $pterigio_obj["grado_oi"] : "-"), "BR", 'C');
				if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
					$y_aux2 = $pdf->GetY();
				}
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}
				
				$pdf->SetXY(85, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Grado"), "B", 0, 'C');
				$pdf->Ln();				
				
				//Reproducido
				$pterigio_obj["ind_reproducido_od"] = $pterigio_obj["ind_reproducido_od"] == 1 ? "SI" : "NO";
				$y_aux = $pdf->GetY();
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($pterigio_obj["ind_reproducido_od"] != "" ? $pterigio_obj["ind_reproducido_od"] : "-"), "BL", 'C');
				$y_aux2 = $pdf->GetY();
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}				
				
				$pterigio_obj["ind_reproducido_oi"] = $pterigio_obj["ind_reproducido_oi"] == 1 ? "SI" : "NO"; 
				$pdf->SetXY(130, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($pterigio_obj["ind_reproducido_oi"] != "" ? $pterigio_obj["ind_reproducido_oi"] : "-"), "BR", 'C');
				if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
					$y_aux2 = $pdf->GetY();
				}
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				} 				
				
				$pdf->SetXY(85, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Reproducido"), "B", 0, 'C');
				$pdf->Ln();								

				//Conjuntiva superior
				$detalle_lista = $db_listas->getDetalle($pterigio_obj["mov_conjuntiva_sup_od"]);
				$pterigio_obj["mov_conjuntiva_sup_od"] = $detalle_lista["nombre_detalle"];
				$y_aux = $pdf->GetY();
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($pterigio_obj["mov_conjuntiva_sup_od"] != "" ? $pterigio_obj["mov_conjuntiva_sup_od"] : "-"), "BL", 'C');
				$y_aux2 = $pdf->GetY();
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}	

				$detalle_lista = $db_listas->getDetalle($pterigio_obj["mov_conjuntiva_sup_oi"]);
				$pterigio_obj["mov_conjuntiva_sup_oi"] = $detalle_lista["nombre_detalle"];
				$pdf->SetXY(130, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($pterigio_obj["mov_conjuntiva_sup_oi"] != "" ? $pterigio_obj["mov_conjuntiva_sup_oi"] : "-"), "BR", 'C');
				if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
					$y_aux2 = $pdf->GetY();
				}
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}	

				$pdf->SetXY(85, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Conjuntiva Superior"), "B", 0, 'C');
				$pdf->Ln();								

				//Astigmatismo inducido
				$pterigio_obj["ind_astigmatismo_ind_od"] = $pterigio_obj["ind_astigmatismo_ind_od"] == 1 ? "SI" : "NO"; 
				$y_aux = $pdf->GetY();
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($pterigio_obj["ind_astigmatismo_ind_od"] != "" ? $pterigio_obj["ind_astigmatismo_ind_od"] : "-"), "BL", 'C');
				$y_aux2 = $pdf->GetY();
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}	
				
				$pterigio_obj["ind_astigmatismo_ind_oi"] = $pterigio_obj["ind_astigmatismo_ind_oi"] == 1 ? "SI" : "NO"; 
				$pdf->SetXY(130, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($pterigio_obj["ind_astigmatismo_ind_oi"] != "" ? $pterigio_obj["ind_astigmatismo_ind_oi"] : "-"), "BR", 'C');
				if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
					$y_aux2 = $pdf->GetY();
				}
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}				

				$pdf->SetXY(85, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Astigmatismo Inducido"), "B", 0, 'C');
				
				$pdf->Ln(5);				
				if (trim($pterigio_obj["observaciones"]) != "") {
					$pdf->SetFont("Arial", "", 9);
					$y_aux = $pdf->GetY();
					$pdf->setSeparationLine();
					$pdf->Line(10, $y_aux, 205, $y_aux);
					$pdf->setDefaultLine();
					$pdf->Ln(1);
					$texto_aux = ajustarCaracteres($pterigio_obj["observaciones"]);
					$pdfHTML->WriteHTML(ajustarCaracteres("<b>Observaciones: </b>").$texto_aux, $pdf);
					$pdf->Ln(3);
					$y_aux2 = $pdf->GetY();
					$pdf->SetY($y_aux);
					$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 0, 'L');
					$pdf->Ln();
				}
				$y_aux = $pdf->GetY();
				$pdf->Line(10, $y_aux, 205, $y_aux);				
				$pdf->Ln(3);
				
				$pdf->Ln(5);
				break; 
				
			case "5": //NESO
				$db_consultas_neso = new DbConsultasNeso(); 
				$db_listas = new DbListas;
				
				$neso_obj = $db_consultas_neso->getConsultaNeso($id_hc);
				
				$pdf->SetFont("Arial", "B", 10);
				$pdf->Cell(195, 5, "Neoplastia Escamosa de la Superficie Ocular", 0, 1, "C");
				$pdf->Ln(1);

				//Interferón
				$neso_obj["ind_interferon"] = $neso_obj["ind_interferon"] == 1 ? "SI" : "NO"; 
				$y_aux = $pdf->GetY(); 
				$pdf->SetFont("Arial", "", 9); 
				$cadena="Usa Interferón: "; 
				$cadena.=$neso_obj["ind_interferon"] != "" ? $neso_obj["ind_interferon"] : "-"; 
				$cadena.="          Dosis: ".$neso_obj["cantidad_dosis"]; 
				$pdf->MultiCell(75, 5, ajustarCaracteres($cadena), "", 'L'); 
				$y_aux2 = $pdf->GetY(); 
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}

				$pdf->SetFont("Arial", "B", 9);
				$pdf->Cell(75, 5, "OD", "B", 0, "C");
				$pdf->Cell(45, 5, ajustarCaracteres(" "), "B", 0, "C");
				$pdf->Cell(75, 5, "OI", "B", 1, "C");	

				// Husos
				$cadena = mapear_husos($neso_obj, "OD");
				$y_aux = $pdf->GetY();
				$pdf->SetFont("Arial", "", 9);		
				$pdf->MultiCell(75, 5, ajustarCaracteres($cadena), "BL", 'C');
				$y_aux2 = $pdf->GetY();
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}
				
				$cadena = mapear_husos($neso_obj, "OI");				
				$pdf->SetXY(130, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($cadena), "BR", 'C');
				if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
					$y_aux2 = $pdf->GetY();
				}
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}
				
				$pdf->SetXY(85, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Husos horarios comprometidos"), "B", 0, 'C');
				$pdf->Ln();	

				//Córnea comprometida
				$cadena=$neso_obj["cornea_comprometida_od"]; 
				$y_aux = $pdf->GetY();
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($cadena), "BL", 'C');
				$y_aux2 = $pdf->GetY();
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}				
				
				$cadena=$neso_obj["cornea_comprometida_oi"]; 
				$pdf->SetXY(130, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($cadena), "BR", 'C');
				if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
					$y_aux2 = $pdf->GetY();
				}
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				} 				
				
				$pdf->SetXY(85, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Córnea comprometida a partir del limbo [MM]"), "B", 0, 'C');
				$pdf->Ln();								

				//Lesión recidivante 
				$neso_obj["ind_recidivante_od"] = $neso_obj["ind_recidivante_od"] == 1 ? "SI" : "NO"; 
				$y_aux = $pdf->GetY();
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($neso_obj["ind_recidivante_od"] != "" ? $neso_obj["ind_recidivante_od"] : "-"), "BL", 'C');
				$y_aux2 = $pdf->GetY();
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}	
				
				$neso_obj["ind_recidivante_oi"] = $neso_obj["ind_recidivante_oi"] == 1 ? "SI" : "NO"; 
				$pdf->SetXY(130, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->MultiCell(75, 5, ajustarCaracteres($neso_obj["ind_recidivante_oi"] != "" ? $neso_obj["ind_recidivante_oi"] : "-"), "BR", 'C');
				if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
					$y_aux2 = $pdf->GetY();
				}
				
				if ($y_aux > $y_aux2) {
					//Hubo salto de página
					$y_aux = 30;
				}				

				$pdf->SetXY(85, $y_aux);
				$pdf->SetFont("Arial", "", 9);
				$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Lesión recidivante"), "B", 0, 'C');
				
				$pdf->Ln(5);				
				if (trim($neso_obj["observaciones"]) != "") {
					$pdf->SetFont("Arial", "", 9);
					$y_aux = $pdf->GetY();
					$pdf->setSeparationLine();
					$pdf->Line(10, $y_aux, 205, $y_aux);
					$pdf->setDefaultLine();
					$pdf->Ln(1);
					$texto_aux = ajustarCaracteres($neso_obj["observaciones"]);
					$pdfHTML->WriteHTML(ajustarCaracteres("<b>Observaciones: </b>").$texto_aux, $pdf);
					$pdf->Ln(3);
					$y_aux2 = $pdf->GetY();
					$pdf->SetY($y_aux);
					$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 0, 'L');
					$pdf->Ln();
				}
				$y_aux = $pdf->GetY();
				$pdf->Line(10, $y_aux, 205, $y_aux);				
				$pdf->Ln(3);
				
				$pdf->Ln(5);
				break;					
		}
	}
	
	function cambiar_enter_br($texto) {
		$texto_alt = $texto;
		$pos = strpos($texto, "<p>");
		if ($pos === false) {
			$texto_alt = str_replace(chr(10), "<br />", $texto);
			if ($texto == $texto_alt) {
				$texto_alt = str_replace(chr(13), "<br />", $texto);
			}
		}
		
		return $texto_alt;
	}
	
	function remplazar_vacio_guion($texto) {
		if (trim($texto) == "") {
			$texto = "-";
		}
		
		return $texto;
	}
	
	if (isset($_SESSION["idUsuario"])) {
		$id_usuario = $_SESSION["idUsuario"];
		$db_historia_clinica = new DbHistoriaClinica();
		$db_pacientes = new DbPacientes();
		$db_admision = new DbAdmision();
		$db_usuarios = new DbUsuarios();
		$db_listas = new DbListas();
		$db_variables = new Dbvariables();
		$db_tipos_registros_hc = new DbTiposRegistrosHc();
		
		$utilidades = new Utilidades();
		$funciones_persona = new FuncionesPersona();
		
		//Se obtiene la ruta actual de las imágenes
		$arr_ruta_base = $db_variables->getVariable(17);
		$ruta_base = $arr_ruta_base["valor_variable"];
		
		//Se obtiene la ruta del comando de Ghostscript
		$gs = $db_variables->getVariable(18);
		$gs = $gs["valor_variable"];
		$ind_imagenes_hc = 0;
		
		if (isset($_POST["id_hc"])) {
			@$id_hc = $utilidades->str_decode($_POST["id_hc"]);
			
			//Se obtienen los datos del registro de historia clínica
			$historia_clinica_obj = $db_historia_clinica->getHistoriaClinicaId($id_hc);
		
			
			$lista_historia_clinica = array();
			array_push($lista_historia_clinica, $historia_clinica_obj);
		} else if (isset($_POST["id_paciente"])) {
			$id_paciente = $_POST["id_paciente"];
			if (isset($_POST["ind_imagenes_hc"])) {
				$ind_imagenes_hc = intval($_POST["ind_imagenes_hc"], 10);
			}
	
			if ($ind_imagenes_hc == 1) {
				$lista_historia_clinica = $db_historia_clinica->getRegistrosHistoriaClinicaExamenes($id_paciente);
			} else {
				$lista_historia_clinica = $db_historia_clinica->getRegistrosHistoriaClinica($id_paciente);
			}
		}
		
		if (count($lista_historia_clinica) > 0) {
		
			$pdf = new PDF('P', 'mm', array(216, 279));
			$pdf->SetAutoPageBreak(true, 10);
			$pdf->SetFillColor(255, 255, 255);
			$pdfHTML = new PDF_HTML();
			
			//Se cargan los filtros
			$ind_tipo_reg_todos = 1;
			$cant_tipos_reg_filtros = 0;
			$arr_tipos_reg = array();
			$ind_usuario_prof_todos = 1;
			$cant_usuarios_prof_filtros = 0;
			$arr_usuarios_prof = array();
			if (isset($_POST["ind_tipo_reg_todos"])) {
				$ind_tipo_reg_todos = intval($_POST["ind_tipo_reg_todos"], 10);
				$cant_tipos_reg_filtros = intval($_POST["cant_tipos_reg_filtros"], 10);
				for ($i = 0; $i < $cant_tipos_reg_filtros; $i++) {
					array_push($arr_tipos_reg, $utilidades->str_decode($_POST["id_tipo_reg_".$i]));
				}
				
				$ind_usuario_prof_todos = intval($_POST["ind_usuario_prof_todos"], 10);
				$cant_usuarios_prof_filtros = intval($_POST["cant_usuarios_prof_filtros"], 10);
				for ($i = 0; $i < $cant_usuarios_prof_filtros; $i++) {
					array_push($arr_usuarios_prof, $utilidades->str_decode($_POST["id_usuario_prof_".$i]));
				}
			}
			
			foreach ($lista_historia_clinica as $historia_clinica_obj) {
				?>					
					<input type="hidden" id="hdd_test_bug_pdf_impresion" value="<?php var_dump($historia_clinica_obj); ?>" />
				<?php
				$id_hc = $historia_clinica_obj["id_hc"];
				$id_tipo_reg = $historia_clinica_obj["id_tipo_reg"];
				$id_tipo_reg_base = $historia_clinica_obj["id_tipo_reg_base"];
				$id_usuario_reg = "";
				if (isset($historia_clinica_obj["id_usuario_reg"])) {
					$id_usuario_reg = $historia_clinica_obj["id_usuario_reg"];
				}
				
				//Se revisan los filtros
				if (($ind_tipo_reg_todos != 1 && !in_array($id_tipo_reg, $arr_tipos_reg)) || ($ind_usuario_prof_todos != 1 && !in_array($id_usuario_reg, $arr_usuarios_prof))) {
					continue;
				}
				
				$admision_obj = $db_admision->get_admision($historia_clinica_obj["id_admision"]);
				
				//Se obtiene la información del tipo de registro
				$tipo_registro_hc_obj = $db_tipos_registros_hc->getTipoRegistroHc($id_tipo_reg);
				
				switch ($id_tipo_reg_base) {
					case "1": //CONSULTA DE OPTOMETRIA
						require_once("../db/DbConsultaOptometria.php");
						$db_consulta_optometria = new DbConsultaOptometria();
						
						$consulta_optometria_obj = $db_consulta_optometria->getConsultaOptometria($id_hc);
						
						imprimir_encabezado($historia_clinica_obj, $pdf);
						
						if (count($consulta_optometria_obj) > 0) {
							$usuario_prof_obj = $db_usuarios->getUsuario($consulta_optometria_obj["id_usuario_crea"]);
							
							//Se obtiene el listado de agudeza visual
							$lista_ag_visual = $db_listas->getListaDetalles(11);
							$mapa_ag_visual = array();
							foreach ($lista_ag_visual as $ag_visual_aux) {
								$mapa_ag_visual[$ag_visual_aux["id_detalle"]] = $ag_visual_aux["nombre_detalle"];
							}
							
							//Se define ancho y color por defecto de las líneas
							$pdf->setDefaultLine();
							
							$pdf->SetFont("Arial", "B", 9);
							
							$pdf->Ln(5);
							
							$nombre_usuario_alt = "";
							if ($usuario_prof_obj["ind_anonimo"] == "1" && $historia_clinica_obj["nombre_usuario_alt"] != "") {
								$nombre_usuario_alt = $historia_clinica_obj["nombre_usuario_alt"];
							} else {
								$nombre_usuario_alt = $usuario_prof_obj["nombre_usuario"]." ".$usuario_prof_obj["apellido_usuario"];
							}
							
							$pdf->Cell(195, 4, ajustarCaracteres("Atiende: ".$nombre_usuario_alt), 0, 2, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, ajustarCaracteres("Lugar de la cita: "), 0, 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(50, 5, $consulta_optometria_obj["lugar_cita"], 0, 0, "R");
							$pdf->Ln(5);
							/*$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(12, 5, ajustarCaracteres("Teléfono:"), 0, 0, "L");
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(40, 5, $consulta_optometria_obj["tel_sede_det"], 0, 0,"R");
							$pdf->Ln(5);
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(12, 5, ajustarCaracteres("Dirección:"), 0, 0, "L");
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(117, 5, $consulta_optometria_obj["dir_sede_det"], 0, 1,"R");*/
							
							if ($usuario_prof_obj["id_usuario_firma"] != "") {
								//Se buscan los datos del usuario que firma
								$usuario_firma_obj = $db_usuarios->getUsuario($usuario_prof_obj["id_usuario_firma"]);
								$pdf->Cell(195, 4, ajustarCaracteres("Instructor: ".$usuario_firma_obj["nombre_usuario"]." ".$usuario_firma_obj["apellido_usuario"]), 0, 2, 'L');
							}
							$pdf->SetFont("Arial", "B", 9);
							//$pdf->MultiCell(195, 4, ajustarCaracteres("Motivo de Consulta: ".(trim($admision_obj["motivo_consulta"]) != "" ? $admision_obj["motivo_consulta"] : "-")), 0, 'L');
							//$pdf->Ln(2);
							
							$texto_anamnesis = ajustarCaracteres($consulta_optometria_obj["anamnesis"]);
							$pdfHTML->WriteHTML("<b>Anamnesis: </b>".$texto_anamnesis, $pdf);
							$pdf->Ln(10);
							
							imprimir_antecedentes($id_hc, "", $pdf, $pdfHTML);
							
							//Dominancia ocular
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(31, 5, "Dominancia Ocular:", 0, 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(20, 5, $consulta_optometria_obj["dominancia_ocular"], 0, 1, 'L');
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(80, 5, "OD", "TBL", 0, 'C');
							$pdf->Cell(35, 5, "", "TB", 0, 'C');
							$pdf->Cell(80, 5, "OI", "TBR", 0, 'C');
							$pdf->Ln();
							
							//AVSC
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(12, 5, "Lejos:", "BL", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(15, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_optometria_obj["avsc_lejos_od"]]), "B", 0, 'L');
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(13, 5, "Interm:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(15, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_optometria_obj["avsc_media_od"]]), "B", 0, 'L');
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(12, 5, "Cerca:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(15, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_optometria_obj["avsc_cerca_od"]]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(30, 5, "AVSC", "B", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(12, 5, "Lejos:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(15, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_optometria_obj["avsc_lejos_oi"]]), "B", 0, 'L');
														
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(13, 5, "Interm:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(15, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_optometria_obj["avsc_media_oi"]]), "B", 0, 'L');
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(12, 5, "Cerca:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(16, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_optometria_obj["avsc_cerca_oi"]]), "BR", 0, 'L');
							$pdf->Ln();
							
							//Lensometría
							$tabla_gafas = $db_consulta_optometria->getHcGafas($id_hc); 
							$i=0; 							 
							foreach ($tabla_gafas as $registro_lenso) {
								$i++;								
								/*$consulta_gafas_obj = $consulta_optometria_obj; 
								$consulta_gafas_obj["lenso_esfera_od"]="+88,88";
								$registro_lenso["lenso_cilindro_od"]="+88,88";
								$registro_lenso["lenso_eje_od"]="88";
								$registro_lenso["lenso_adicion_od"]="8,88";
								$registro_lenso["lenso_lejos_od"]="255";
								$registro_lenso["lenso_media_od"]="255";
								$registro_lenso["lenso_cerca_od"]="255"; 							
								$registro_lenso["lenso_esfera_oi"]="";
								$registro_lenso["lenso_cilindro_oi"]="";
								$registro_lenso["lenso_eje_oi"]="";
								$registro_lenso["lenso_adicion_oi"]="";
								$registro_lenso["lenso_lejos_oi"]="255";
								$registro_lenso["lenso_media_oi"]="255";
								$registro_lenso["lenso_cerca_oi"]="255";*/
								
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(47, 5, $registro_lenso["lenso_esfera_od"]." / ".$registro_lenso["lenso_cilindro_od"]." / ".$registro_lenso["lenso_eje_od"], "L", 0, 'C');
								
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(25, 5, ajustarCaracteres("Adición:"), "", 0, 'C');
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(10, 5, remplazar_vacio_guion($registro_lenso["lenso_adicion_od"]), "", 0, 'L');				
							
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(30, 10, ajustarCaracteres("Lensometría ".$i), "B", 0, 'C');
								$pdf->SetFont("Arial", "", 9);								
								$pdf->Cell(50, 5, $registro_lenso["lenso_esfera_oi"]." / ".$registro_lenso["lenso_cilindro_oi"]." / ".$registro_lenso["lenso_eje_oi"], "", 0, 'C');
				
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(20, 5, ajustarCaracteres("Adición:"), "", 0, 'C');
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(13, 5, remplazar_vacio_guion($registro_lenso["lenso_adicion_oi"]), "R", 0, 'L');								
								
								$pdf->Ln();
								$x_aux = $pdf->GetX();
								$y_aux = $pdf->GetY();
								$pdf->setSeparationLine();
								$pdf->Line($x_aux, $y_aux, $x_aux + 82, $y_aux);
								$pdf->Line($x_aux + 112, $y_aux, $x_aux + 195, $y_aux);
								$pdf->setDefaultLine();
								
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(12, 5, "Lejos:", "BL", 0, 'R');
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(15, 5, remplazar_vacio_guion(@$mapa_ag_visual[$registro_lenso["lenso_lejos_od"]]), "B", 0, 'L');
								
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(13, 5, "Interm:", "B", 0, 'R');
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(15, 5, remplazar_vacio_guion(@$mapa_ag_visual[$registro_lenso["lenso_media_od"]]), "B", 0, 'L');
																
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(12, 5, "Cerca:", "B", 0, 'R');
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(15, 5, remplazar_vacio_guion(@$mapa_ag_visual[$registro_lenso["lenso_cerca_od"]]), "B", 0, 'L');
								$pdf->Cell(30, 10, "", "", 0, 'C');
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(12, 5, "Lejos:", "B", 0, 'R');
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(15, 5, remplazar_vacio_guion(@$mapa_ag_visual[$registro_lenso["lenso_lejos_oi"]]), "B", 0, 'L');
								
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(13, 5, "Interm:", "B", 0, 'R');
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(15, 5, remplazar_vacio_guion(@$mapa_ag_visual[$registro_lenso["lenso_media_oi"]]), "B", 0, 'L');
								
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(12, 5, "Cerca:", "B", 0, 'R');
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(16, 5, remplazar_vacio_guion(@$mapa_ag_visual[$registro_lenso["lenso_cerca_oi"]]), "BR", 0, 'L');
								$pdf->Ln();
							}
							
							if ($i==0) {
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(85, 10, remplazar_vacio_guion("-"), "BL", 0, 'C');								
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(25, 10, ajustarCaracteres("Lensometría"), "B", 0, 'C');								
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(85, 10, remplazar_vacio_guion("-"), "BR", 0, 'C');								
								$pdf->Ln(); 
							}
							
							//Queratometría
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Cilindro:", "BL", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion($consulta_optometria_obj["querato_dif_od"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Eje:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion($consulta_optometria_obj["querato_ejek1_od"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "K+Plano:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(12, 5, remplazar_vacio_guion($consulta_optometria_obj["querato_k1_od"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("Queratometría"), "B", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Cilindro:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion($consulta_optometria_obj["querato_dif_oi"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Eje:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion($consulta_optometria_obj["querato_ejek1_oi"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "K+Plano:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(12, 5, remplazar_vacio_guion($consulta_optometria_obj["querato_k1_oi"]), "BR", 0, 'L');
							$pdf->Ln();
							
							//Subjetivo
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(47, 5, $consulta_optometria_obj["subjetivo_esfera_od"]." / ".$consulta_optometria_obj["subjetivo_cilindro_od"]." / ".$consulta_optometria_obj["subjetivo_eje_od"], "L", 0, 'C');

							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(25, 5, ajustarCaracteres("Adición:"), "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion($consulta_optometria_obj["subjetivo_adicion_od"]), "B", 0, 'L');
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(30, 10, ajustarCaracteres("Subjetivo"), "B", 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(50, 5, $consulta_optometria_obj["subjetivo_esfera_oi"]." / ".$consulta_optometria_obj["subjetivo_cilindro_oi"]." / ".$consulta_optometria_obj["subjetivo_eje_oi"], "", 0, 'C');
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(20, 5, ajustarCaracteres("Adición:"), "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(13, 5, remplazar_vacio_guion($consulta_optometria_obj["subjetivo_adicion_oi"]), "BR", 0, 'L');
							
							$pdf->Ln();
							$x_aux = $pdf->GetX();
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line($x_aux, $y_aux, $x_aux + 82, $y_aux);
							$pdf->Line($x_aux + 112, $y_aux, $x_aux + 195, $y_aux);
							$pdf->setDefaultLine();
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(12, 5, "Lejos:", "BL", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(15, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_optometria_obj["subjetivo_lejos_od"]]), "B", 0, 'L');
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(13, 5, ajustarCaracteres("Interm:"), "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(15, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_optometria_obj["subjetivo_media_od"]]), "B", 0, 'L');
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(12, 5, "Cerca:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(15, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_optometria_obj["subjetivo_cerca_od"]]), "B", 0, 'L');
							$pdf->Cell(30, 10, "", "", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(12, 5, "Lejos:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(15, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_optometria_obj["subjetivo_lejos_oi"]]), "B", 0, 'L');

							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(13, 5, ajustarCaracteres("Interm:"), "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(15, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_optometria_obj["subjetivo_media_oi"]]), "B", 0, 'L');
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(12, 5, "Cerca:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(16, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_optometria_obj["subjetivo_cerca_oi"]]), "BR", 0, 'L');
							$pdf->Ln();
							
							//Observaciones
							if ($consulta_optometria_obj["observaciones_optometria"] != "") {
								$y_aux = $pdf->GetY();
								$texto_obs_optometria = ajustarCaracteres($consulta_optometria_obj["observaciones_optometria"]);
								$pdf->Ln(1);
								$pdfHTML->WriteHTML("<b>Observaciones: </b>".$texto_obs_optometria, $pdf);
								$pdf->Ln(1);
								$y_aux2 = $pdf->GetY();
								
								$pdf->Line(10, $y_aux, 10, $y_aux2);
								$pdf->Line(205, $y_aux, 205, $y_aux2);
								$pdf->Line(10, $y_aux2, 205, $y_aux2);
							}
							
							//Cicloplejia
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(52, 5, $consulta_optometria_obj["cicloplejio_esfera_od"]." / ".$consulta_optometria_obj["cicloplejio_cilindro_od"]." / ".$consulta_optometria_obj["cicloplejio_eje_od"], "BL", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Lejos:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(12, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_optometria_obj["cicloplejio_lejos_od"]]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("Cicloplejia"), "B", 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(52, 5, $consulta_optometria_obj["cicloplejio_esfera_oi"]." / ".$consulta_optometria_obj["cicloplejio_cilindro_oi"]." / ".$consulta_optometria_obj["cicloplejio_eje_oi"], "B", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Lejos:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(12, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_optometria_obj["cicloplejio_lejos_oi"]]), "BR", 0, 'L');
							$pdf->Ln();
							
							//Refracción Final
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(52, 5, $consulta_optometria_obj["refrafinal_esfera_od"]." / ".$consulta_optometria_obj["refrafinal_cilindro_od"]." / ".$consulta_optometria_obj["refrafinal_eje_od"], "L", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, ajustarCaracteres("Adición:"), 0, 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(12, 5, remplazar_vacio_guion($consulta_optometria_obj["refrafinal_adicion_od"]), 0, 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("Refracción Final"), 0, 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(52, 5, $consulta_optometria_obj["refrafinal_esfera_oi"]." / ".$consulta_optometria_obj["refrafinal_cilindro_oi"]." / ".$consulta_optometria_obj["refrafinal_eje_oi"], 0, 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, ajustarCaracteres("Adición:"), 0, 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(12, 5, remplazar_vacio_guion($consulta_optometria_obj["refrafinal_adicion_oi"]), "R", 0, 'L');
							$pdf->Ln();
							if (trim($consulta_optometria_obj["observaciones_rxfinal"]) != "") {
								$y_aux = $pdf->GetY();
								$pdf->setSeparationLine();
								$pdf->Line(10, $y_aux, 205, $y_aux);
								$pdf->setDefaultLine();
								$pdf->Ln(1);
								$texto_rx_final = ajustarCaracteres($consulta_optometria_obj["observaciones_rxfinal"]);
								$pdfHTML->WriteHTML(ajustarCaracteres("<b>Fórmula de gafas: </b>").$texto_rx_final, $pdf);
								$pdf->Ln(3);
								$y_aux2 = $pdf->GetY();
								$pdf->SetX($x_aux);
								$pdf->SetY($y_aux);
								$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 0, 'L');
								$pdf->Ln();
							}
							
							//Presión intraocular neumática
							if ($consulta_optometria_obj["presion_intraocular_od"] != "" || $consulta_optometria_obj["presion_intraocular_oi"] != "") {
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(75, 5, (trim($consulta_optometria_obj["presion_intraocular_od"]) != "" ? $consulta_optometria_obj["presion_intraocular_od"]." mmHg" : "-"), "TBL", 0, 'C');
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(45, 5, ajustarCaracteres("Presión Intraocular Neumática"), "TB", 0, 'C');
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(75, 5, (trim($consulta_optometria_obj["presion_intraocular_oi"]) != "" ? $consulta_optometria_obj["presion_intraocular_oi"]." mmHg" : "-"), "TBR", 0, 'C');
								$pdf->Ln();
							} else {
								//Se traza una línea de cierre
								$x_aux = $pdf->GetX();
								$y_aux = $pdf->GetY();
								$pdf->Line($x_aux, $y_aux, $x_aux + 195, $y_aux);
							}
							
							//Diagnósticos
							$pdf->Ln(2);
							imprimir_diagnosticos($id_hc, $pdf, true);
							$pdf->Ln(1);
							
							//Diagnosticos generales del lente
							$pdf->Ln(2);
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
							$pdf->Cell(52, 5, $consulta_optometria_obj["form_cantidad"] !="" ? $consulta_optometria_obj["form_cantidad"] : "-", "TB", 0, 'R');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(92, 5, ajustarCaracteres("Distancia pupilar:"), "TB", 0, 'R');
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
							/*$pdfHTML->WriteHTML($texto_aux, $pdf);*/
							
				
							$x_aux = $pdf->GetX();
							$y_aux = $pdf->GetY();
							$pdf->Line($x_aux, $y_aux, $x_aux + 195, $y_aux);
							
							//Observaciones
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(45, 5, ajustarCaracteres("Otros Diagnósticos y Análisis"), 0, 1, 'L');
							$pdf->Ln(1);
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_optometria_obj["diagnostico_optometria"]) != "" ? $consulta_optometria_obj["diagnostico_optometria"] : "-");
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(5);
							
							imprimir_firma($consulta_optometria_obj["id_usuario_crea"], $pdf, $nombre_usuario_alt);
                            $pdf->Footer();
						}
						break;
						
					case "2": //CONSULTA DE OFTALMOLOGIA
						require_once("../db/DbConsultaOftalmologia.php");
						$db_consulta_oftalmologia = new DbConsultaOftalmologia();
						
						imprimir_encabezado($historia_clinica_obj, $pdf);
						
						$consulta_oftalmologia_obj = $db_consulta_oftalmologia->getConsultaOftalmologia($id_hc);
						$consulta_ofp_obj = $db_consulta_oftalmologia->getConsultaOftalmologiaPediatrica($id_hc);
						
						$consulta_optometria_obj = $db_consulta_oftalmologia->getOptometriaPaciente($historia_clinica_obj["id_paciente"], $historia_clinica_obj["id_admision"]);
						if (count($consulta_oftalmologia_obj) > 0) {
							$usuario_prof_obj = $db_usuarios->getUsuario($consulta_oftalmologia_obj["id_usuario_crea"]);
							
							//Se define ancho y color por defecto de las líneas
							$pdf->setDefaultLine();
							
							$pdf->SetFont("Arial", "B", 9);
							
							$pdf->Ln(5);
							
							$nombre_usuario_alt = "";
							if ($usuario_prof_obj["ind_anonimo"] == "1" && $historia_clinica_obj["nombre_usuario_alt"] != "") {
								$nombre_usuario_alt = $historia_clinica_obj["nombre_usuario_alt"];
							} else {
								$nombre_usuario_alt = $usuario_prof_obj["nombre_usuario"]." ".$usuario_prof_obj["apellido_usuario"];
							}
							
							$pdf->Cell(195, 4, ajustarCaracteres("Atiende: ".$nombre_usuario_alt), 0, 2, 'L');
							
							if ($usuario_prof_obj["id_usuario_firma"] != "") {
								//Se buscan los datos del usuario que firma
								$usuario_firma_obj = $db_usuarios->getUsuario($usuario_prof_obj["id_usuario_firma"]);
								$pdf->Cell(195, 4, ajustarCaracteres("Instructor: ".$usuario_firma_obj["nombre_usuario"]." ".$usuario_firma_obj["apellido_usuario"]), 0, 2, 'L');
							}
							
							//$pdf->MultiCell(195, 4, ajustarCaracteres("Motivo de Consulta: ".(trim($admision_obj["motivo_consulta"]) != "" ? $admision_obj["motivo_consulta"] : "-")), 0, 'L');
							//$pdf->Ln(2);
							
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres("<b>Enfermedad actual: </b>".trim($consulta_oftalmologia_obj["enfermedad_actual"]));
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(10);
							
							imprimir_antecedentes($id_hc, $consulta_oftalmologia_obj["desc_antecedentes_medicos"], $pdf, $pdfHTML);
							
							if (isset($consulta_ofp_obj["id_hc"]) && ($tipo_registro_hc_obj["tipo_reg_adicional"] == "1" || $consulta_oftalmologia_obj["ind_eval_muscular"] == "1")) {
								//Oftalmología pediátrica
								$pdf->SetFont("Arial", "B", 10);
								$pdf->Cell(195, 5, ajustarCaracteres("Evaluación Muscular y Sensorialidad"), 1, 1, "C");
								
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(13, 5, ajustarCaracteres("Método:"), "L", 0, "L");
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(182, 5, ajustarCaracteres($consulta_ofp_obj["metodo_ofp"] != "" ? $consulta_ofp_obj["metodo_ofp"] : "-"), "R", 1, "L");
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(17, 5, ajustarCaracteres("Ortotropia:"), "L", 0, "L");
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(58, 5, ajustarCaracteres($consulta_ofp_obj["ortotropia"] != "" ? $consulta_ofp_obj["ortotropia"] : "-"), 0, 0, "L");
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(19, 5, ajustarCaracteres("Corrección:"), 0, 0, "L");
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(66, 5, ajustarCaracteres($consulta_ofp_obj["correccion"] != "" ? $consulta_ofp_obj["correccion"] : "-"), 0, 0, "L");
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(18, 5, ajustarCaracteres("Ojo fijador:"), 0, 0, "L");
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(17, 5, ajustarCaracteres($consulta_ofp_obj["ojo_fijador"] != "" ? $consulta_ofp_obj["ojo_fijador"] : "-"), "R", 1, "L");
								
								$pdf->Cell(195, 2, "", "LR", 1, "C");
								
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(49, 4, ajustarCaracteres("Lejos"), "L", 0, "C");
								$pdf->Cell(49, 4, ajustarCaracteres("Cerca"), 0, 0, "C");
								$pdf->Cell(49, 4, ajustarCaracteres("Cerca +3,00"), 0, 0, "C");
								$pdf->Cell(48, 4, ajustarCaracteres("Cerca bifocales"), "R", 1, "C");
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(49, 4, ajustarCaracteres("H: ".($consulta_ofp_obj["lejos_h"] != "" ? $consulta_ofp_obj["lejos_h"] : "-")." ".$consulta_ofp_obj["lejos_h_delta"]), "L", 0, "C");
								$pdf->Cell(49, 4, ajustarCaracteres("H: ".($consulta_ofp_obj["cerca_h"] != "" ? $consulta_ofp_obj["cerca_h"] : "-")." ".$consulta_ofp_obj["cerca_h_delta"]), 0, 0, "C");
								$pdf->Cell(49, 4, ajustarCaracteres("H: ".($consulta_ofp_obj["cerca_c_h"] != "" ? $consulta_ofp_obj["cerca_c_h"] : "-")." ".$consulta_ofp_obj["cerca_c_h_delta"]), 0, 0, "C");
								$pdf->Cell(48, 4, ajustarCaracteres("H: ".($consulta_ofp_obj["cerca_b_h"] != "" ? $consulta_ofp_obj["cerca_b_h"] : "-")." ".$consulta_ofp_obj["cerca_b_h_delta"]), "R", 1, "C");
								$pdf->Cell(49, 4, ajustarCaracteres("V: ".($consulta_ofp_obj["lejos_v"] != "" ? $consulta_ofp_obj["lejos_v"] : "-")." ".$consulta_ofp_obj["lejos_v_delta"]), "LB", 0, "C");
								$pdf->Cell(49, 4, ajustarCaracteres("V: ".($consulta_ofp_obj["cerca_v"] != "" ? $consulta_ofp_obj["cerca_v"] : "-")." ".$consulta_ofp_obj["cerca_v_delta"]), "B", 0, "C");
								$pdf->Cell(49, 4, ajustarCaracteres("V: ".($consulta_ofp_obj["cerca_c_v"] != "" ? $consulta_ofp_obj["cerca_c_v"] : "-")." ".$consulta_ofp_obj["cerca_c_v_delta"]), "B", 0, "C");
								$pdf->Cell(48, 4, ajustarCaracteres("V: ".($consulta_ofp_obj["cerca_b_v"] != "" ? $consulta_ofp_obj["cerca_b_v"] : "-")." ".$consulta_ofp_obj["cerca_b_v_delta"]), "RB", 1, "C");
								
								$pdf->Cell(195, 2, "", "LRT", 1, "C");
								
								$pdf->Cell(45, 4, ajustarCaracteres("H: ".($consulta_ofp_obj["derecha_alto_h"] != "" ? $consulta_ofp_obj["derecha_alto_h"] : "-")." ".$consulta_ofp_obj["derecha_alto_h_delta"]), "L", 0, "C");
								$pdf->Cell(30, 4, "", 0, 0, "C");
								$pdf->Cell(45, 4, ajustarCaracteres("H: ".($consulta_ofp_obj["centro_alto_h"] != "" ? $consulta_ofp_obj["centro_alto_h"] : "-")." ".$consulta_ofp_obj["centro_alto_h_delta"]), 0, 0, "C");
								$pdf->Cell(30, 4, "", 0, 0, "C");
								$pdf->Cell(45, 4, ajustarCaracteres("H: ".($consulta_ofp_obj["izquierda_alto_h"] != "" ? $consulta_ofp_obj["izquierda_alto_h"] : "-")." ".$consulta_ofp_obj["izquierda_alto_h_delta"]), "R", 1, "C");
								
								$pdf->Cell(45, 4, ajustarCaracteres("V: ".($consulta_ofp_obj["derecha_alto_v"] != "" ? $consulta_ofp_obj["derecha_alto_v"] : "-")." ".$consulta_ofp_obj["derecha_alto_v_delta"]), "L", 0, "C");
								$pdf->Cell(10, 4, ajustarCaracteres($consulta_ofp_obj["alto_derecha_od"] != "" ? $consulta_ofp_obj["alto_derecha_od"] : "-"), 0, 0, "C");
								$pdf->Cell(10, 4, ajustarCaracteres($consulta_ofp_obj["alto_centro_od"] != "" ? $consulta_ofp_obj["alto_centro_od"] : "-"), 0, 0, "C");
								$pdf->Cell(10, 4, ajustarCaracteres($consulta_ofp_obj["alto_izquierda_od"] != "" ? $consulta_ofp_obj["alto_izquierda_od"] : "-"), 0, 0, "C");
								$pdf->Cell(45, 4, ajustarCaracteres("V: ".($consulta_ofp_obj["centro_alto_v"] != "" ? $consulta_ofp_obj["centro_alto_v"] : "-")." ".$consulta_ofp_obj["centro_alto_v_delta"]), 0, 0, "C");
								$pdf->Cell(10, 4, ajustarCaracteres($consulta_ofp_obj["alto_derecha_oi"] != "" ? $consulta_ofp_obj["alto_derecha_oi"] : "-"), 0, 0, "C");
								$pdf->Cell(10, 4, ajustarCaracteres($consulta_ofp_obj["alto_centro_oi"] != "" ? $consulta_ofp_obj["alto_centro_oi"] : "-"), 0, 0, "C");
								$pdf->Cell(10, 4, ajustarCaracteres($consulta_ofp_obj["alto_izquierda_oi"] != "" ? $consulta_ofp_obj["alto_izquierda_oi"] : "-"), 0, 0, "C");
								$pdf->Cell(45, 4, ajustarCaracteres("V: ".($consulta_ofp_obj["izquierda_alto_v"] != "" ? $consulta_ofp_obj["izquierda_alto_v"] : "-")." ".$consulta_ofp_obj["izquierda_alto_v_delta"]), "R", 1, "C");
								
								$pdf->Cell(195, 2, "", "LR", 1, "C");
								$y_aux = $pdf->GetY();
								
								$pdf->Cell(45, 4, ajustarCaracteres("H: ".($consulta_ofp_obj["derecha_medio_h"] != "" ? $consulta_ofp_obj["derecha_medio_h"] : "-")." ".$consulta_ofp_obj["derecha_medio_h_delta"]), "L", 0, "C");
								$pdf->Cell(30, 4, "", 0, 0, "C");
								$pdf->Cell(45, 4, ajustarCaracteres("H: ".($consulta_ofp_obj["centro_medio_h"] != "" ? $consulta_ofp_obj["centro_medio_h"] : "-")." ".$consulta_ofp_obj["centro_medio_h_delta"]), 0, 0, "C");
								$pdf->Cell(30, 4, "", 0, 0, "C");
								$pdf->Cell(45, 4, ajustarCaracteres("H: ".($consulta_ofp_obj["izquierda_medio_h"] != "" ? $consulta_ofp_obj["izquierda_medio_h"] : "-")." ".$consulta_ofp_obj["izquierda_medio_h_delta"]), "R", 1, "C");
								
								$pdf->Cell(45, 4, ajustarCaracteres("V: ".($consulta_ofp_obj["derecha_medio_v"] != "" ? $consulta_ofp_obj["derecha_medio_v"] : "-")." ".$consulta_ofp_obj["derecha_medio_v_delta"]), "L", 0, "C");
								$pdf->Cell(30, 4, "", 0, 0, "C");
								$pdf->Cell(45, 4, ajustarCaracteres("V: ".($consulta_ofp_obj["centro_medio_v"] != "" ? $consulta_ofp_obj["centro_medio_v"] : "-")." ".$consulta_ofp_obj["centro_medio_v_delta"]), 0, 0, "C");
								$pdf->Cell(30, 4, "", 0, 0, "C");
								$pdf->Cell(45, 4, ajustarCaracteres("V: ".($consulta_ofp_obj["izquierda_medio_v"] != "" ? $consulta_ofp_obj["izquierda_medio_v"] : "-")." ".$consulta_ofp_obj["izquierda_medio_v_delta"]), "R", 1, "C");
								
								$pdf->SetY($y_aux);
								
								$pdf->Cell(45, 8, "", 0, 0, "C");
								$pdf->Cell(10, 8, ajustarCaracteres($consulta_ofp_obj["medio_derecha_od"] != "" ? $consulta_ofp_obj["medio_derecha_od"] : "-"), 0, 0, "C");
								$pdf->Cell(10, 8, "", 0, 0, "C");
								$pdf->Cell(10, 8, ajustarCaracteres($consulta_ofp_obj["medio_izquierda_od"] != "" ? $consulta_ofp_obj["medio_izquierda_od"] : "-"), 0, 0, "C");
								$pdf->Cell(45, 8, "", 0, 0, "C");
								$pdf->Cell(10, 8, ajustarCaracteres($consulta_ofp_obj["medio_derecha_oi"] != "" ? $consulta_ofp_obj["medio_derecha_oi"] : "-"), 0, 0, "C");
								$pdf->Cell(10, 8, "", 0, 0, "C");
								$pdf->Cell(10, 8, ajustarCaracteres($consulta_ofp_obj["medio_izquierda_oi"] != "" ? $consulta_ofp_obj["medio_izquierda_oi"] : "-"), 0, 1, "C");
								
								$pdf->Cell(195, 2, "", "LR", 1, "C");
								
								$pdf->Cell(45, 4, ajustarCaracteres("H: ".($consulta_ofp_obj["derecha_bajo_h"] != "" ? $consulta_ofp_obj["derecha_bajo_h"] : "-")." ".$consulta_ofp_obj["derecha_bajo_h_delta"]), "L", 0, "C");
								$pdf->Cell(10, 4, ajustarCaracteres($consulta_ofp_obj["bajo_derecha_od"] != "" ? $consulta_ofp_obj["bajo_derecha_od"] : "-"), 0, 0, "C");
								$pdf->Cell(10, 4, ajustarCaracteres($consulta_ofp_obj["bajo_centro_od"] != "" ? $consulta_ofp_obj["bajo_centro_od"] : "-"), 0, 0, "C");
								$pdf->Cell(10, 4, ajustarCaracteres($consulta_ofp_obj["bajo_izquierda_od"] != "" ? $consulta_ofp_obj["bajo_izquierda_od"] : "-"), 0, 0, "C");
								$pdf->Cell(45, 4, ajustarCaracteres("H: ".($consulta_ofp_obj["centro_bajo_h"] != "" ? $consulta_ofp_obj["centro_bajo_h"] : "-")." ".$consulta_ofp_obj["centro_bajo_h_delta"]), 0, 0, "C");
								$pdf->Cell(10, 4, ajustarCaracteres($consulta_ofp_obj["bajo_derecha_oi"] != "" ? $consulta_ofp_obj["bajo_derecha_oi"] : "-"), 0, 0, "C");
								$pdf->Cell(10, 4, ajustarCaracteres($consulta_ofp_obj["bajo_centro_oi"] != "" ? $consulta_ofp_obj["bajo_centro_oi"] : "-"), 0, 0, "C");
								$pdf->Cell(10, 4, ajustarCaracteres($consulta_ofp_obj["bajo_izquierda_oi"] != "" ? $consulta_ofp_obj["bajo_izquierda_oi"] : "-"), 0, 0, "C");
								$pdf->Cell(45, 4, ajustarCaracteres("H: ".($consulta_ofp_obj["izquierda_bajo_h"] != "" ? $consulta_ofp_obj["izquierda_bajo_h"] : "-")." ".$consulta_ofp_obj["izquierda_bajo_h_delta"]), "R", 1, "C");
								
								$pdf->Cell(45, 4, ajustarCaracteres("V: ".($consulta_ofp_obj["derecha_bajo_v"] != "" ? $consulta_ofp_obj["derecha_bajo_v"] : "-")." ".$consulta_ofp_obj["derecha_bajo_v_delta"]), "L", 0, "C");
								$pdf->Cell(30, 4, "DVD: ".ajustarCaracteres($consulta_ofp_obj["dvd_od"] != "" ? $consulta_ofp_obj["dvd_od"] : "-"), 0, 0, "C");
								$pdf->Cell(45, 4, ajustarCaracteres("V: ".($consulta_ofp_obj["centro_bajo_v"] != "" ? $consulta_ofp_obj["centro_bajo_v"] : "-")." ".$consulta_ofp_obj["centro_bajo_v_delta"]), 0, 0, "C");
								$pdf->Cell(30, 4, "DVD: ".ajustarCaracteres($consulta_ofp_obj["dvd_oi"] != "" ? $consulta_ofp_obj["dvd_oi"] : "-"), 0, 0, "C");
								$pdf->Cell(45, 4, ajustarCaracteres("V: ".($consulta_ofp_obj["izquierda_bajo_v"] != "" ? $consulta_ofp_obj["izquierda_bajo_v"] : "-")." ".$consulta_ofp_obj["izquierda_bajo_v_delta"]), "R", 1, "C");
								
								$pdf->Cell(195, 2, "", "LRB", 1, "C");
								
								$y_aux = $pdf->GetY();
								$pdf->Ln(1);
								
								$pdf->SetFont("Arial", "", 9);
								$texto_aux = ajustarCaracteres("<b>Observaciones: </b>".trim($consulta_ofp_obj["observaciones_oft_pediat"] != "" ? $consulta_ofp_obj["observaciones_oft_pediat"] : "<p>-</p>"));
								$pdfHTML->WriteHTML($texto_aux, $pdf);
								$pdf->Ln(1);
								
								$y_aux2 = $pdf->GetY();
								if ($y_aux <= $y_aux2) {
									$pdf->SetY($y_aux);
									$pdf->Rect(10, $y_aux, 195, ($y_aux2 - $y_aux));
									$pdf->SetY($y_aux2);
								}
								
								//Inclinación de la cabeza
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(97, 4, ajustarCaracteres("Inclinación derecha"), "LT", 0, "C");
								$pdf->Cell(98, 4, ajustarCaracteres("Inclinación izquierda"), "RT", 1, "C");
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(97, 4, ajustarCaracteres("H: ".($consulta_ofp_obj["inclinacion_der_h"] != "" ? $consulta_ofp_obj["inclinacion_der_h"] : "-")." ".$consulta_ofp_obj["inclinacion_der_h_delta"]), "L", 0, "C");
								$pdf->Cell(98, 4, ajustarCaracteres("H: ".($consulta_ofp_obj["inclinacion_izq_h"] != "" ? $consulta_ofp_obj["inclinacion_izq_h"] : "-")." ".$consulta_ofp_obj["inclinacion_izq_h_delta"]), "R", 1, "C");
								$pdf->Cell(97, 4, ajustarCaracteres("V: ".($consulta_ofp_obj["inclinacion_der_v"] != "" ? $consulta_ofp_obj["inclinacion_der_v"] : "-")." ".$consulta_ofp_obj["inclinacion_der_v_delta"]), "L", 0, "C");
								$pdf->Cell(98, 4, ajustarCaracteres("V: ".($consulta_ofp_obj["inclinacion_izq_v"] != "" ? $consulta_ofp_obj["inclinacion_izq_v"] : "-")." ".$consulta_ofp_obj["inclinacion_izq_v_delta"]), "R", 1, "C");
								
								//Nistagmo
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(16, 5, ajustarCaracteres("Nistagmo: "), "LT", 0, "L");
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(6, 5, ajustarCaracteres($consulta_ofp_obj["nistagmo"] != "" ? $consulta_ofp_obj["nistagmo"] : "-"), "T", 0, "L");
								$pdf->Cell(173, 5, ajustarCaracteres($consulta_ofp_obj["texto_nistagmo"]), "RT", 1, "L");
								
								//Posición anormal de la cabeza
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(48, 5, ajustarCaracteres("Posición anormal de la cabeza: "), "LB", 0, "L");
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(6, 5, ajustarCaracteres($consulta_ofp_obj["pac"] != "" ? $consulta_ofp_obj["pac"] : "-"), "B", 0, "L");
								$pdf->Cell(141, 5, ajustarCaracteres($consulta_ofp_obj["texto_pac"]), "RB", 1, "L");
								
								//Convergencia y divergencia - Luces de Worth
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(47, 4, "", "LT", 0, "L");
								$pdf->Cell(20, 4, ajustarCaracteres("Lejos"), "T", 0, "C");
								$pdf->Cell(20, 4, ajustarCaracteres("Cerca"), "T", 0, "C");
								$pdf->Cell(10, 4, "", "RT", 0, "L");
								
								$pdf->Cell(98, 4, ajustarCaracteres("Luces de Worth"), "RT", 1, "C");
								
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(10, 4, "", "L", 0, "L");
								$pdf->Cell(37, 4, ajustarCaracteres("Convergencia fusional:"), 0, 0, "L");
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(20, 4, ajustarCaracteres($consulta_ofp_obj["conv_fusional_lejos"] != "" ? $consulta_ofp_obj["conv_fusional_lejos"] : "-"), 0, 0, "C");
								$pdf->Cell(20, 4, ajustarCaracteres($consulta_ofp_obj["conv_fusional_cerca"] != "" ? $consulta_ofp_obj["conv_fusional_cerca"] : "-"), 0, 0, "C");
								$pdf->Cell(10, 4, "", "R", 0, "L");
								
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(15, 4, "", 0, 0, "C");
								$pdf->Cell(34, 4, ajustarCaracteres("Lejos"), 0, 0, "C");
								$pdf->Cell(34, 4, ajustarCaracteres("Cerca"), 0, 0, "C");
								$pdf->Cell(15, 4, "", "R", 1, "C");
								
								
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(10, 4, "", "LB", 0, "L");
								$pdf->Cell(37, 4, ajustarCaracteres("Divergencia fusional:"), "B", 0, "L");
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(20, 4, ajustarCaracteres($consulta_ofp_obj["div_fusional_lejos"] != "" ? $consulta_ofp_obj["div_fusional_lejos"] : "-"), "B", 0, "C");
								$pdf->Cell(20, 4, ajustarCaracteres($consulta_ofp_obj["div_fusional_cerca"] != "" ? $consulta_ofp_obj["div_fusional_cerca"] : "-"), "B", 0, "C");
								$pdf->Cell(10, 4, "", "RB", 0, "L");
								
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(15, 4, "", 0, 0, "C");
								$pdf->Cell(34, 4, ajustarCaracteres($consulta_ofp_obj["worth_lejos"] != "" ? $consulta_ofp_obj["worth_lejos"] : "-"), 0, 0, "C");
								$pdf->Cell(34, 4, ajustarCaracteres($consulta_ofp_obj["worth_cerca"] != "" ? $consulta_ofp_obj["worth_cerca"] : "-"), 0, 0, "C");
								$pdf->Cell(15, 4, "", "R", 1, "C");
								
								//Estereopsis y rejilla de Maddox
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(97, 4, ajustarCaracteres("Test de estereopsis"), "LRT", 0, "C");
								
								$pdf->Cell(98, 4, ajustarCaracteres("Rejilla de Maddox"), "RT", 1, "C");
								
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(10, 4, "", "L", 0, "L");
								$pdf->Cell(40, 4, ajustarCaracteres("Mosca:"), 0, 0, "R");
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(37, 4, ajustarCaracteres($consulta_ofp_obj["estereopsis_mosca"] != "" ? $consulta_ofp_obj["estereopsis_mosca"] : "-"), 0, 0, "L");
								$pdf->Cell(10, 4, "", "R", 0, "L");
								
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(15, 4, "", 0, 0, "C");
								$pdf->Cell(34, 4, ajustarCaracteres("Derecha"), 0, 0, "C");
								$pdf->Cell(34, 4, ajustarCaracteres("Izquierda"), 0, 0, "C");
								$pdf->Cell(15, 4, "", "R", 1, "C");
								
								
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(10, 4, "", "L", 0, "L");
								$pdf->Cell(40, 4, ajustarCaracteres("Animales:"), 0, 0, "R");
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(37, 4, ajustarCaracteres($consulta_ofp_obj["valor_estereopsis_animales"] != "" ? $consulta_ofp_obj["valor_estereopsis_animales"] : "-"), 0, 0, "L");
								$pdf->Cell(10, 4, "", "R", 0, "L");
								
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(15, 4, "", 0, 0, "C");
								$pdf->Cell(34, 4, ajustarCaracteres(($consulta_ofp_obj["maddox_der"] != "" ? $consulta_ofp_obj["maddox_der"]." " : "-").$consulta_ofp_obj["valor_maddox_der"]), 0, 0, "C");
								$pdf->Cell(34, 4, ajustarCaracteres(($consulta_ofp_obj["maddox_izq"] != "" ? $consulta_ofp_obj["maddox_izq"]." " : "-").$consulta_ofp_obj["valor_maddox_izq"]), 0, 0, "C");
								$pdf->Cell(15, 4, "", "R", 1, "C");
								
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(10, 4, "", "LB", 0, "L");
								$pdf->Cell(40, 4, ajustarCaracteres("Circulos:"), "B", 0, "R");
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(37, 4, ajustarCaracteres($consulta_ofp_obj["valor_estereopsis_circulos"] != "" ? $consulta_ofp_obj["valor_estereopsis_circulos"] : "-"), "B", 0, "L");
								$pdf->Cell(10, 4, "", "RB", 0, "L");
								
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(98, 4, "", "RB", 1, "C");
							}
							
							$pdf->Ln(2);
							
							$muscular_balance = trim($consulta_oftalmologia_obj["muscular_balance"]);
							$muscular_ppc = trim($consulta_oftalmologia_obj["muscular_ppc"]);
							$muscular_motilidad = trim($consulta_oftalmologia_obj["muscular_motilidad"]);
							if ($muscular_balance != "" || $muscular_motilidad != "" || $muscular_ppc != "") {
								$pdf->SetFont("Arial", "B", 10);
								$pdf->Cell(195, 5, "Muscular", 1, 1, 'C');
								
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(15, 5, "Balance:", 0, 0, 'L');
								$pdf->SetX(10);
								$pdf->SetFont("Arial", "", 9);
								$pdf->MultiCell(195, 5, "                ".ajustarCaracteres($muscular_balance != "" ? $muscular_balance : "-"), "LR", 'L');
								//$pdf->Cell(83, 5, ajustarCaracteres($muscular_balance != "" ? $muscular_balance : "-"), "T", 0, 'L');
								
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(9, 5, "PPC:", 0, 0, 'L');
								$pdf->SetX(10);
								$pdf->SetFont("Arial", "", 9);
								$pdf->MultiCell(195, 5, "          ".ajustarCaracteres($muscular_ppc != "" ? $muscular_ppc : "-"), "LR", 'L');
								//$pdf->Cell(88, 5, ajustarCaracteres($muscular_ppc != "" ? $muscular_ppc : "-"), "RT", 1, 'L');
								
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(16, 5, "Motilidad:", 0, 0, 'L');
								$pdf->SetX(10);
								$pdf->SetFont("Arial", "", 9);
								$pdf->MultiCell(195, 5, "                  ".ajustarCaracteres($muscular_motilidad != "" ? $muscular_motilidad : "-"), "BLR", 'L');
								
								$pdf->Ln(2);
							}
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(75, 5, "OD", "TBL", 0, 'C');
							$pdf->Cell(45, 5, "", "TB", 0, 'C');
							$pdf->Cell(75, 5, "OI", "TBR", 0, 'C');
							$pdf->Ln();
							
							/*****************/
							/*Anexos oculares*/
							/*****************/
							$pdf->SetFont("Arial", "B", 10);
							$pdf->Cell(195, 5, ajustarCaracteres("Anexos ocuares"), 1, 1, 'C');
							
							//Órbita y pápados
							$y_aux = $pdf->GetY();
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_oftalmologia_obj["biomi_orbita_parpados_od"] != "" ? $consulta_oftalmologia_obj["biomi_orbita_parpados_od"] : "-"), 0, 'C');
							$y_aux2 = $pdf->GetY();
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(130, $y_aux);
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_oftalmologia_obj["biomi_orbita_parpados_oi"] != "" ? $consulta_oftalmologia_obj["biomi_orbita_parpados_oi"] : "-"), 0, 'C');
							if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
								$y_aux2 = $pdf->GetY();
							}
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(85, $y_aux);
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Órbita y párpados"), 0, 0, 'C');
							$pdf->Ln();
							
							$pdf->SetXY(10, $y_aux);
							$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 1, 'C');
							
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Sistema lagrimal
							$y_aux = $pdf->GetY();
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_oftalmologia_obj["biomi_sist_lagrimal_od"] != "" ? $consulta_oftalmologia_obj["biomi_sist_lagrimal_od"] : "-"), 0, 'C');
							$y_aux2 = $pdf->GetY();
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(130, $y_aux);
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_oftalmologia_obj["biomi_sist_lagrimal_oi"] != "" ? $consulta_oftalmologia_obj["biomi_sist_lagrimal_oi"] : "-"), 0, 'C');
							if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
								$y_aux2 = $pdf->GetY();
							}
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(85, $y_aux);
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Sistema lagrimal"), 0, 0, 'C');
							$pdf->Ln();
							
							$pdf->SetXY(10, $y_aux);
							$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 1, 'C');
							
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Imagen biomicroscopía
							$img_biomiocroscopia = $consulta_oftalmologia_obj["img_biomiocroscopia"];
							if (trim($img_biomiocroscopia) != "") {
								$img_biomiocroscopia = str_replace("../imagenes/imagenes_hce", $ruta_base, $img_biomiocroscopia);
							}
							if (trim($img_biomiocroscopia) == "" || !file_exists($img_biomiocroscopia)) {
								$img_biomiocroscopia = "../imagenes/ojos_oftalmologia_print.png";
							}
							
							//Se obtienen las dimensiones de la imagen
							$arr_prop_imagen = getimagesize($img_biomiocroscopia);
							$ancho_aux = floatval($arr_prop_imagen[0]);
							$alto_aux = floatval($arr_prop_imagen[1]);
							
							$ancho_max = 180.0;
							$alto_max = 100.0;
							
							if ($ancho_aux > $ancho_max) {
								$alto_aux = floor($alto_aux * ($ancho_max / $ancho_aux));
								$ancho_aux = floor($ancho_aux * ($ancho_max / $ancho_aux));
							}
							if ($alto_aux > $alto_max) {
								$ancho_aux = floor($ancho_aux * ($alto_max / $alto_aux));
								$alto_aux = floor($alto_aux * ($alto_max / $alto_aux));
							}
							
							//Se verifica si la imagen cabe en la página, de no ser así, se inserta una nueva página
							$alto_total = $alto_aux + 2;
							$x_aux = 10 + floor((195 - $ancho_aux) / 2);
							$y_aux = $pdf->validateSpaceHeight($alto_total);
							
							$pdf->Image($img_biomiocroscopia, $x_aux, $y_aux + 1, $ancho_aux, $alto_aux);
							$pdf->Cell(195, $alto_aux + 2, "", 1, 1);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(75, 5, "OD", "TBL", 0, 'C');
							$pdf->Cell(45, 5, "", "TB", 0, 'C');
							$pdf->Cell(75, 5, "OI", "TBR", 0, 'C');
							$pdf->Ln();
							
							/******************/
							/**Biomicroscopía**/
							/******************/
							$pdf->SetFont("Arial", "B", 10);
							$pdf->Cell(195, 5, ajustarCaracteres("Biomicroscopía"), 1, 1, 'C');
							
							//Conjuntiva
							$y_aux = $pdf->GetY();
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_oftalmologia_obj["biomi_conjuntiva_od"] != "" ? $consulta_oftalmologia_obj["biomi_conjuntiva_od"] : "-"), 0, 'C');
							$y_aux2 = $pdf->GetY();
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(130, $y_aux);
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_oftalmologia_obj["biomi_conjuntiva_oi"] != "" ? $consulta_oftalmologia_obj["biomi_conjuntiva_oi"] : "-"), 0, 'C');
							if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
								$y_aux2 = $pdf->GetY();
							}
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(85, $y_aux);
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Conjuntiva"), 0, 0, 'C');
							$pdf->Ln();
							
							$pdf->SetXY(10, $y_aux);
							$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 1, 'C');
							
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Córnea
							$y_aux = $pdf->GetY();
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_oftalmologia_obj["biomi_cornea_od"] != "" ? $consulta_oftalmologia_obj["biomi_cornea_od"] : "-"), 0, 'C');
							$y_aux2 = $pdf->GetY();
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(130, $y_aux);
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_oftalmologia_obj["biomi_cornea_oi"] != "" ? $consulta_oftalmologia_obj["biomi_cornea_oi"] : "-"), 0, 'C');
							if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
								$y_aux2 = $pdf->GetY();
							}
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(85, $y_aux);
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Córnea"), 0, 0, 'C');
							$pdf->Ln();
							
							$pdf->SetXY(10, $y_aux);
							$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 1, 'C');
							
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Cámara anterior
							$y_aux = $pdf->GetY();
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_oftalmologia_obj["biomi_cam_anterior_od"] != "" ? $consulta_oftalmologia_obj["biomi_cam_anterior_od"] : "-"), 0, 'C');
							$y_aux2 = $pdf->GetY();
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(130, $y_aux);
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_oftalmologia_obj["biomi_cam_anterior_oi"] != "" ? $consulta_oftalmologia_obj["biomi_cam_anterior_oi"] : "-"), 0, 'C');
							if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
								$y_aux2 = $pdf->GetY();
							}
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(85, $y_aux);
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Cámara anterior"), 0, 0, 'C');
							$pdf->Ln();
							
							$pdf->SetXY(10, $y_aux);
							$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 1, 'C');
							
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Iris
							$y_aux = $pdf->GetY();
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_oftalmologia_obj["biomi_iris_od"] != "" ? $consulta_oftalmologia_obj["biomi_iris_od"] : "-"), 0, 'C');
							$y_aux2 = $pdf->GetY();
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(130, $y_aux);
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_oftalmologia_obj["biomi_iris_oi"] != "" ? $consulta_oftalmologia_obj["biomi_iris_oi"] : "-"), 0, 'C');
							if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
								$y_aux2 = $pdf->GetY();
							}
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(85, $y_aux);
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Iris"), 0, 0, 'C');
							$pdf->Ln();
							
							$pdf->SetXY(10, $y_aux);
							$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 1, 'C');
							
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Cristalino
							$y_aux = $pdf->GetY();
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_oftalmologia_obj["biomi_cristalino_od"] != "" ? $consulta_oftalmologia_obj["biomi_cristalino_od"] : "-"), 0, 'C');
							$y_aux2 = $pdf->GetY();
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(130, $y_aux);
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_oftalmologia_obj["biomi_cristalino_oi"] != "" ? $consulta_oftalmologia_obj["biomi_cristalino_oi"] : "-"), 0, 'C');
							if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
								$y_aux2 = $pdf->GetY();
							}
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(85, $y_aux);
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Cristalino"), 0, 0, 'C');
							$pdf->Ln();
							
							$pdf->SetXY(10, $y_aux);
							$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 1, 'C');
							
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Escala de Van Herick
							$y_aux = $pdf->GetY();
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_oftalmologia_obj["biomi_vanherick_od"] != "" ? $consulta_oftalmologia_obj["biomi_vanherick_od"] : "-"), 0, 'C');
							$y_aux2 = $pdf->GetY();
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(130, $y_aux);
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_oftalmologia_obj["biomi_vanherick_oi"] != "" ? $consulta_oftalmologia_obj["biomi_vanherick_oi"] : "-"), 0, 'C');
							if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
								$y_aux2 = $pdf->GetY();
							}
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(85, $y_aux);
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Escala de Van Herick"), 0, 0, 'C');
							$pdf->Ln();
							
							$pdf->SetXY(10, $y_aux);
							$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LBR", 1, 'C');
							
							$pdf->Ln(2);
							
							//Gonioscopia
							$goniosco_superior_od = $consulta_oftalmologia_obj["goniosco_superior_od"] != "-1" ? $consulta_oftalmologia_obj["goniosco_superior_od"] : "";
							$goniosco_inferior_od = $consulta_oftalmologia_obj["goniosco_inferior_od"] != "-1" ? $consulta_oftalmologia_obj["goniosco_inferior_od"] : "";
							$goniosco_nasal_od = $consulta_oftalmologia_obj["goniosco_nasal_od"] != "-1" ? $consulta_oftalmologia_obj["goniosco_nasal_od"] : "";
							$goniosco_temporal_od = $consulta_oftalmologia_obj["goniosco_temporal_od"] != "-1" ? $consulta_oftalmologia_obj["goniosco_temporal_od"] : "";
							$goniosco_superior_oi = $consulta_oftalmologia_obj["goniosco_superior_oi"] != "-1" ? $consulta_oftalmologia_obj["goniosco_superior_oi"] : "";
							$goniosco_inferior_oi = $consulta_oftalmologia_obj["goniosco_inferior_oi"] != "-1" ? $consulta_oftalmologia_obj["goniosco_inferior_oi"] : "";
							$goniosco_nasal_oi = $consulta_oftalmologia_obj["goniosco_nasal_oi"] != "-1" ? $consulta_oftalmologia_obj["goniosco_nasal_oi"] : "";
							$goniosco_temporal_oi = $consulta_oftalmologia_obj["goniosco_temporal_oi"] != "-1" ? $consulta_oftalmologia_obj["goniosco_temporal_oi"] : "";
							
							//Se verifica si el cuadro de gonioscopia cabe en la página, de no ser así, se inserta una nueva página
							$alto_total = 16;
							$pdf->validateSpaceHeight($alto_total);
							
							$y_aux = $pdf->GetY();
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(25, 5, "", 0, 0, 'C');
							$pdf->Cell(25, 5, ajustarCaracteres($goniosco_superior_od), 0, 0, 'C');
							$pdf->Cell(95, 5, "", 0, 0, 'C');
							$pdf->Cell(25, 5, ajustarCaracteres($goniosco_superior_oi), 0, 1, 'C');
							$pdf->Cell(25, 5, ajustarCaracteres($goniosco_temporal_od), 0, 0, 'C');
							$pdf->Cell(25, 5, "", 0, 0, 'C');
							$pdf->Cell(25, 5, ajustarCaracteres($goniosco_nasal_od), 0, 0, 'C');
							$pdf->Cell(45, 5, "", 0, 0, 'C');
							$pdf->Cell(25, 5, ajustarCaracteres($goniosco_nasal_oi), 0, 0, 'C');
							$pdf->Cell(25, 5, "", 0, 0, 'C');
							$pdf->Cell(25, 5, ajustarCaracteres($goniosco_temporal_oi), 0, 1, 'C');
							$pdf->Cell(25, 5, "", 0, 0, 'C');
							$pdf->Cell(25, 5, ajustarCaracteres($goniosco_inferior_od), 0, 0, 'C');
							$pdf->Cell(95, 5, "", 0, 0, 'C');
							$pdf->Cell(25, 5, ajustarCaracteres($goniosco_inferior_oi), 0, 1, 'C');
							
							$pdf->Line(20, $y_aux + 2, 75, $y_aux + 13);
							$pdf->Line(20, $y_aux + 13, 75, $y_aux + 2);
							$pdf->Line(140, $y_aux + 2, 195, $y_aux + 13);
							$pdf->Line(140, $y_aux + 13, 195, $y_aux + 2);
							
							$pdf->SetY($y_aux);
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 15, "Gonioscopia", 1, 1, 'C');
							$pdf->Ln(2);
							
							//Tonometrías
							imprimir_tonometrias($id_hc, $historia_clinica_obj["id_paciente"], $historia_clinica_obj["id_admision"], $pdf);
							
							/****************/
							/**Fondo de ojo**/
							/****************/
							$img_tonometria_od = $consulta_oftalmologia_obj["img_tonometria_od"];
							if (trim($img_tonometria_od) != "") {
								$img_tonometria_od = str_replace("../imagenes/imagenes_hce", $ruta_base, $img_tonometria_od);
							}
							if (trim($img_tonometria_od) == "" || !file_exists($img_tonometria_od)) {
								$img_tonometria_od = "../imagenes/ojos_tonometria_od_print.png";
							}
							
							$img_tonometria_oi = $consulta_oftalmologia_obj["img_tonometria_oi"];
							if (trim($img_tonometria_oi) != "") {
								$img_tonometria_oi = str_replace("../imagenes/imagenes_hce", $ruta_base, $img_tonometria_oi);
							}
							if (trim($img_tonometria_oi) == "" || !file_exists($img_tonometria_oi)) {
								$img_tonometria_oi = "../imagenes/ojos_tonometria_oi_print.png";
							}
							
							//Se obtienen las dimensiones de las imagenes
							$arr_prop_imagen = getimagesize($img_tonometria_od);
							$ancho_aux = floatval($arr_prop_imagen[0]);
							$alto_aux = floatval($arr_prop_imagen[1]);
							
							$ancho_max = 95.0;
							$alto_max = 100.0;
							if ($ancho_aux == 211.0 && $alto_aux == 189.0) {
								$alto_max = 50.0;
							}
							
							if ($ancho_aux > $ancho_max) {
								$alto_aux = floor($alto_aux * ($ancho_max / $ancho_aux));
								$ancho_aux = floor($ancho_aux * ($ancho_max / $ancho_aux));
							}
							if ($alto_aux > $alto_max) {
								$ancho_aux = floor($ancho_aux * ($alto_max / $alto_aux));
								$alto_aux = floor($alto_aux * ($alto_max / $alto_aux));
							}
							
							//Se verifica si la imagen cabe en la página, de no ser así, se inserta una nueva página
							$alto_total = $alto_aux + 7;
							$y_aux = $pdf->validateSpaceHeight($alto_total) + 5;
							
							$pdf->SetFont("Arial", "B", 10);
							$pdf->Cell(75, 5, "OD", "TBL", 0, 'C');
							$pdf->Cell(45, 5, ajustarCaracteres("Fondo de ojo"), "TB", 0, 'C');
							$pdf->Cell(75, 5, "OI", "TBR", 1, 'C');
							
							$x_aux = 10 + floor((95 - $ancho_aux) / 2);
							$pdf->Image($img_tonometria_od, $x_aux, $y_aux + 1, $ancho_aux, $alto_aux);
							
							$x_aux = 110 + floor((95 - $ancho_aux) / 2);
							$pdf->Image($img_tonometria_oi, $x_aux, $y_aux + 1, $ancho_aux, $alto_aux);
							
							$pdf->Cell(195, $alto_aux + 2, "", "BLR", 1);
							
							//Nervio óptico
							$y_aux = $pdf->GetY();
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_oftalmologia_obj["tonometria_nervio_optico_od"] != "" ? $consulta_oftalmologia_obj["tonometria_nervio_optico_od"] : "-"), 0, 'C');
							$y_aux2 = $pdf->GetY();
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(130, $y_aux);
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_oftalmologia_obj["tonometria_nervio_optico_oi"] != "" ? $consulta_oftalmologia_obj["tonometria_nervio_optico_oi"] : "-"), 0, 'C');
							if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
								$y_aux2 = $pdf->GetY();
							}
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(85, $y_aux);
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Nervio óptico"), 0, 0, 'C');
							$pdf->Ln();
							
							$pdf->SetXY(10, $y_aux);
							$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 1, 'C');
							
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							$pdf->SetFont("Arial", "B", 10);
							$pdf->Cell(195, 5, ajustarCaracteres("Retina"), "TBLR", 1, 'C');
							
							//Mácula
							$y_aux = $pdf->GetY();
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_oftalmologia_obj["tonometria_macula_od"] != "" ? $consulta_oftalmologia_obj["tonometria_macula_od"] : "-"), 0, 'C');
							$y_aux2 = $pdf->GetY();
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(130, $y_aux);
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_oftalmologia_obj["tonometria_macula_oi"] != "" ? $consulta_oftalmologia_obj["tonometria_macula_oi"] : "-"), 0, 'C');
							if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
								$y_aux2 = $pdf->GetY();
							}
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(85, $y_aux);
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Mácula"), 0, 0, 'C');
							$pdf->Ln();
							
							$pdf->SetXY(10, $y_aux);
							$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 1, 'C');
							
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Periferia
							$y_aux = $pdf->GetY();
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_oftalmologia_obj["tonometria_periferia_od"] != "" ? $consulta_oftalmologia_obj["tonometria_periferia_od"] : "-"), 0, 'C');
							$y_aux2 = $pdf->GetY();
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(130, $y_aux);
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_oftalmologia_obj["tonometria_periferia_oi"] != "" ? $consulta_oftalmologia_obj["tonometria_periferia_oi"] : "-"), 0, 'C');
							if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
								$y_aux2 = $pdf->GetY();
							}
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(85, $y_aux);
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Periferia"), 0, 0, 'C');
							$pdf->Ln();
							
							$pdf->SetXY(10, $y_aux);
							$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 1, 'C');
							
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Vítreo
							$y_aux = $pdf->GetY();
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_oftalmologia_obj["tonometria_vitreo_od"] != "" ? $consulta_oftalmologia_obj["tonometria_vitreo_od"] : "-"), 0, 'C');
							$y_aux2 = $pdf->GetY();
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(130, $y_aux);
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_oftalmologia_obj["tonometria_vitreo_oi"] != "" ? $consulta_oftalmologia_obj["tonometria_vitreo_oi"] : "-"), 0, 'C');
							if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
								$y_aux2 = $pdf->GetY();
							}
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(85, $y_aux);
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Vítreo"), 0, 0, 'C');
							$pdf->Ln();
							
							$pdf->SetXY(10, $y_aux);
							$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 1, 'C');
							
							$x_aux = $pdf->GetX();
							$y_aux = $pdf->GetY();
							$pdf->Line($x_aux, $y_aux, $x_aux + 195, $y_aux);
							
							$pdf->Ln(2);
							
							imprimir_diagnosticos($id_hc, $pdf, true);
							$pdf->Ln(2);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 5, ajustarCaracteres("Otros diagnósticos y análisis"), 0, 1, 'L');
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_oftalmologia_obj["diagnostico_oftalmo"]));
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(5);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 5, ajustarCaracteres("Solicitud de procedimientos y exámenes complementarios"), 0, 1, 'L');
							imprimir_hc_procedimientos_solic($id_hc, $pdf, true);
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_oftalmologia_obj["solicitud_examenes"]) != "" ? cambiar_enter_br($consulta_oftalmologia_obj["solicitud_examenes"]) : "-");
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(3);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 5, ajustarCaracteres("Recomendaciones clínicas, médicas, optométricas y quirúrgicas"), 0, 1, 'L');
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_oftalmologia_obj["tratamiento_oftalmo"]) != "" ? cambiar_enter_br($consulta_oftalmologia_obj["tratamiento_oftalmo"]) : "-");
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(3);
							
							imprimir_formulacion_hc($id_hc, $pdf);
							
							imprimir_firma($consulta_oftalmologia_obj["id_usuario_crea"], $pdf, $nombre_usuario_alt);                                                        
						}
                                           
						break;
						
					case "4": //PROCEDIMIENTO QUIRÚRGICO
						require_once("../db/DbCirugias.php");
						$db_cirugias = new DbCirugias();
						
						imprimir_encabezado($historia_clinica_obj, $pdf);
						
						$cirugia_obj = $db_cirugias->get_cirugia($id_hc);
						
						if (count($cirugia_obj) > 0) {
							$usuario_prof_obj = $db_usuarios->getUsuario($cirugia_obj["id_usuario_crea"]);
							
							//Se define ancho y color por defecto de las líneas
							$pdf->setDefaultLine();
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(30, 5, ajustarCaracteres("Fecha de la cirugía:"), "TL", 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(68, 5, ajustarCaracteres($cirugia_obj["fecha_cx_t"]), "TR", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(27, 5, ajustarCaracteres("Tipo de atención:"), "T", 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(70, 5, ajustarCaracteres($cirugia_obj["amb_rea"]), "TR", 1, 'L');
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, ajustarCaracteres("Finalidad:"), "L", 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(82, 5, ajustarCaracteres($cirugia_obj["fin_pro"]), "R", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(11, 5, ajustarCaracteres("Opera:"), 0, 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(86, 5, ajustarCaracteres($cirugia_obj["nombre_usuario"]." ".$cirugia_obj["apellido_usuario"]), "R", 1, 'L');
							
							$reoperacion_aux = "";
							if ($cirugia_obj["ind_reoperacion"] == "1") {
								$reoperacion_aux = "Si";
							} else if ($cirugia_obj["ind_reoperacion"] == "0") {
								$reoperacion_aux = "No";
							}
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(25, 5, ajustarCaracteres("Es reoperación:"), "L", 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(73, 5, ajustarCaracteres($reoperacion_aux), "R", 0, 'L');
							
							if ($cirugia_obj["ind_reoperacion"] == "1") {
								$reop_ent_aux = "";
								if ($cirugia_obj["ind_reop_ent"] == "1") {
									$reop_ent_aux = "Si";
								} else if ($cirugia_obj["ind_reop_ent"] == "0") {
									$reop_ent_aux = "No";
								}
								
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(25, 5, ajustarCaracteres("Del consultorio:"), 0, 0, 'L');
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(72, 5, ajustarCaracteres($reop_ent_aux), "R", 1, 'L');
								
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(35, 5, ajustarCaracteres("Fecha cirugía anterior:"), "L", 0, 'L');
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(63, 5, ajustarCaracteres($cirugia_obj["fecha_cx_ant_t"]), "R", 0, 'L');
								$pdf->Cell(97, 5, "", "R", 1, 'L');
							} else {
								$pdf->Cell(97, 5, "", "R", 1, 'L');
							}
							$y_aux = $pdf->GetY();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->Ln(2);
							
							$pdf->SetFont("Arial", "B", 10);
							$pdf->Cell(195, 5, ajustarCaracteres("Procedimientos quirúrgicos"), "TLR", 1, 'C');
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(15, 5, ajustarCaracteres("Código"), "L", 0, 'L');
							$pdf->Cell(150, 5, ajustarCaracteres("Procedimiento"), 0, 0, 'L');
							$pdf->Cell(15, 5, ajustarCaracteres("Ojo"), 0, 0, 'C');
							$pdf->Cell(15, 5, ajustarCaracteres("Vía"), "R", 1, 'C');
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Se carga el listado de procedimientos
							$lista_procedimientos = $db_cirugias->get_lista_cirugias_procedimientos($id_hc);
							
							if (count($lista_procedimientos) > 0) {
								foreach ($lista_procedimientos as $proc_aux) {
									$pdf->SetFont("Arial", "", 9);
									$pdf->Cell(15, 5, ajustarCaracteres($proc_aux["cod_procedimiento"]), "L", 0, 'L');
									$pdf->Cell(150, 5, ajustarCaracteres($proc_aux["nombre_procedimiento"]), 0, 0, 'L');
									$pdf->Cell(15, 5, ajustarCaracteres($proc_aux["ojo"]), 0, 0, 'C');
									$pdf->Cell(15, 5, ajustarCaracteres($proc_aux["via_procedimiento"]), "R", 1, 'C');
								}
							} else {
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(195, 5, ajustarCaracteres("(Sin procedimientos quirúrgicos)"), "LR", 1, 'C');
							}
							$y_aux = $pdf->GetY();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->Ln(2);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 5, ajustarCaracteres("Observaciones"), 0, 1, 'L');
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($cirugia_obj["observaciones_cx"]) != "" ? cambiar_enter_br($cirugia_obj["observaciones_cx"]) : "-");
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(3);
							
							$pdf->Ln(2);
							imprimir_diagnosticos($id_hc, $pdf, true);
						}
						break;
						
					case "5": //CONSULTA PREQUIRÚRGICA DE CATARATA
						require_once("../db/DbConsultaPreqxCatarata.php");
						$db_consulta_preqx_catarata = new DbConsultaPreqxCatarata();
						
						imprimir_encabezado($historia_clinica_obj, $pdf);
						
						$consulta_preqx_catarata_obj = $db_consulta_preqx_catarata->get_consulta_preqx_catarata($id_hc);
						
						if (count($consulta_preqx_catarata_obj) > 0) {
							$usuario_prof_obj = $db_usuarios->getUsuario($consulta_preqx_catarata_obj["id_usuario_crea"]);
							
							//Se verifica de que información de ojo se dispone
							$bol_od = false;
							$bol_oi = false;
							switch ($consulta_preqx_catarata_obj["ojo"]) {
								case "OD":
									$bol_od = true;
									break;
								case "OI":
									$bol_oi = true;
									break;
								case "AO":
									$bol_od = true;
									$bol_oi = true;
								break;
							}
							
							//Se define ancho y color por defecto de las líneas
							$pdf->setDefaultLine();
							
							$pdf->SetFont("Arial", "B", 9);
							
							$pdf->Ln(5);
							
							$nombre_usuario_alt = "";
							if ($usuario_prof_obj["ind_anonimo"] == "1" && $historia_clinica_obj["nombre_usuario_alt"] != "") {
								$nombre_usuario_alt = $historia_clinica_obj["nombre_usuario_alt"];
							} else {
								$nombre_usuario_alt = $usuario_prof_obj["nombre_usuario"]." ".$usuario_prof_obj["apellido_usuario"];
							}
							
							$pdf->Cell(195, 4, ajustarCaracteres("Atiende: ".$nombre_usuario_alt), 0, 2, 'L');
							
							if ($usuario_prof_obj["id_usuario_firma"] != "") {
								//Se buscan los datos del usuario que firma
								$usuario_firma_obj = $db_usuarios->getUsuario($usuario_prof_obj["id_usuario_firma"]);
								$pdf->Cell(195, 4, ajustarCaracteres("Instructor: ".$usuario_firma_obj["nombre_usuario"]." ".$usuario_firma_obj["apellido_usuario"]), 0, 2, 'L');
							}
							
							//$pdf->MultiCell(195, 4, ajustarCaracteres("Motivo de Consulta: ".(trim($admision_obj["motivo_consulta"]) != "" ? $admision_obj["motivo_consulta"] : "-")), 0, 'L');
							//$pdf->Ln(1);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(13, 5, ajustarCaracteres("Cirugía:"), 0, 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(85, 5, ajustarCaracteres($consulta_preqx_catarata_obj["nombre_cirugia"]), 0, 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(8, 5, ajustarCaracteres("Ojo:"), 0, 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(89, 5, ajustarCaracteres($consulta_preqx_catarata_obj["ojo"]), 0, 1, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(31, 5, ajustarCaracteres("Fecha de la cirugía:"), 0, 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(67, 5, ajustarCaracteres($consulta_preqx_catarata_obj["fecha_cirugia_t"]), 0, 0, 'L');
							$pdf->Cell(97, 5, ajustarCaracteres($consulta_preqx_catarata_obj["num_cirugia"]."a cirugía"), 0, 1, 'L');
							$pdf->Ln(1);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("Clasificación LOCS III:"), "TL", 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(63, 5, ajustarCaracteres($consulta_preqx_catarata_obj["locs3"]), "TR", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(10, 5, ajustarCaracteres("Valor:"), "T", 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(87, 5, ajustarCaracteres($consulta_preqx_catarata_obj["val_locs3"]), "TR", 1, 'L');
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(32, 5, ajustarCaracteres("Recuento endotelial:"), "L", 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(66, 5, ajustarCaracteres(trim($consulta_preqx_catarata_obj["val_rec_endotelial"]) != "" ? $consulta_preqx_catarata_obj["val_rec_endotelial"]." Cel/mm".chr(178) : ""), "R", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(20, 5, ajustarCaracteres("Paquimetría:"), 0, 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(77, 5, ajustarCaracteres(trim($consulta_preqx_catarata_obj["val_paquimetria"]) != "" ? $consulta_preqx_catarata_obj["val_paquimetria"]." um" : ""), "R", 1, 'L');
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(17, 5, ajustarCaracteres("Plegables:"), "L", 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(81, 5, ajustarCaracteres($consulta_preqx_catarata_obj["plegables"]), "R", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(12, 5, ajustarCaracteres("Rígido:"), 0, 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(85, 5, ajustarCaracteres($consulta_preqx_catarata_obj["rigido"]), "R", 1, 'L');
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(19, 5, ajustarCaracteres("Especiales:"), "BL", 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(79, 5, ajustarCaracteres($consulta_preqx_catarata_obj["especiales"]), "BR", 0, 'L');
							$pdf->Cell(97, 5, "", "BR", 1, 'L');
							$pdf->Ln(2);
							
							$pdf->SetFont("Arial", "B", 10);
							$pdf->Cell(19, 5, ajustarCaracteres("Evolución"), 0, 1, 'L');
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_preqx_catarata_obj["texto_evolucion"]) != "" ? cambiar_enter_br($consulta_preqx_catarata_obj["texto_evolucion"]) : "-");
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(3);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(17, 5, ajustarCaracteres("Anestesia:"), 0, 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(178, 5, ajustarCaracteres($consulta_preqx_catarata_obj["anestesia"]), 0, 1, 'L');
							$pdf->Ln(1);
							
							$pdf->SetFont("Arial", "B", 10);
							$pdf->Cell(195, 5, ajustarCaracteres("Plan Quirúrgico: Incisión Principal/Arqueada"), "TLR", 1, 'C');
							$pdf->Cell(195, 5, ajustarCaracteres("Queratometría"), "BLR", 1, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(80, 5, "OD", "BL", 0, 'C');
							$pdf->Cell(35, 5, "", "B", 0, 'C');
							$pdf->Cell(80, 5, "OI", "BR", 1, 'C');
							$pdf->Cell(40, 5, "Valor", "BL", 0, 'C');
							$pdf->Cell(40, 5, "Eje", "B", 0, 'C');
							$pdf->Cell(35, 5, "", "B", 0, 'C');
							$pdf->Cell(40, 5, "Valor", "B", 0, 'C');
							$pdf->Cell(40, 5, "Eje", "BR", 1, 'C');
							
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(40, 5, ajustarCaracteres($consulta_preqx_catarata_obj["querato_val_biometria_od"]), "L", 0, 'C');
							$pdf->Cell(40, 5, ajustarCaracteres($consulta_preqx_catarata_obj["querato_eje_biometria_od"]), 0, 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("Biometría"), 0, 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(40, 5, ajustarCaracteres($consulta_preqx_catarata_obj["querato_val_biometria_oi"]), 0, 0, 'C');
							$pdf->Cell(40, 5, ajustarCaracteres($consulta_preqx_catarata_obj["querato_eje_biometria_oi"]), "R", 1, 'C');
							
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(40, 5, ajustarCaracteres($consulta_preqx_catarata_obj["querato_val_iol_master_od"]), "L", 0, 'C');
							$pdf->Cell(40, 5, ajustarCaracteres($consulta_preqx_catarata_obj["querato_eje_iol_master_od"]), 0, 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("IOL Master"), 0, 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(40, 5, ajustarCaracteres($consulta_preqx_catarata_obj["querato_val_iol_master_oi"]), 0, 0, 'C');
							$pdf->Cell(40, 5, ajustarCaracteres($consulta_preqx_catarata_obj["querato_eje_iol_master_oi"]), "R", 1, 'C');
							
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(40, 5, ajustarCaracteres($consulta_preqx_catarata_obj["querato_val_topografia_od"]), "L", 0, 'C');
							$pdf->Cell(40, 5, ajustarCaracteres($consulta_preqx_catarata_obj["querato_eje_topografia_od"]), 0, 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("Topografía"), 0, 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(40, 5, ajustarCaracteres($consulta_preqx_catarata_obj["querato_val_topografia_oi"]), 0, 0, 'C');
							$pdf->Cell(40, 5, ajustarCaracteres($consulta_preqx_catarata_obj["querato_eje_topografia_oi"]), "R", 1, 'C');
							
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(40, 5, ajustarCaracteres($consulta_preqx_catarata_obj["querato_val_definitiva_od"]), "BL", 0, 'C');
							$pdf->Cell(40, 5, ajustarCaracteres($consulta_preqx_catarata_obj["querato_eje_definitiva_od"]), "B", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("Definitiva"), "B", 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(40, 5, ajustarCaracteres($consulta_preqx_catarata_obj["querato_val_definitiva_oi"]), "B", 0, 'C');
							$pdf->Cell(40, 5, ajustarCaracteres($consulta_preqx_catarata_obj["querato_eje_definitiva_oi"]), "BR", 1, 'C');
							
							$img_queratometria_od = $consulta_preqx_catarata_obj["img_queratometria_od"];
							if (trim($img_queratometria_od) != "") {
								$img_queratometria_od = str_replace("../imagenes/imagenes_hce", $ruta_base, $img_queratometria_od);
							}
							if (trim($img_queratometria_od) == "" || !file_exists($img_queratometria_od)) {
								$img_queratometria_od = "../imagenes/queratometria_img_print.png";
							}
							
							$img_queratometria_oi = $consulta_preqx_catarata_obj["img_queratometria_oi"];
							if (trim($img_queratometria_oi) != "") {
								$img_queratometria_oi = str_replace("../imagenes/imagenes_hce", $ruta_base, $img_queratometria_oi);
							}
							if (trim($img_queratometria_oi) == "" || !file_exists($img_queratometria_oi)) {
								$img_queratometria_oi = "../imagenes/queratometria_img_print.png";
							}
							
							//Se obtienen las dimensiones de las imagenes
							$arr_prop_imagen = getimagesize($img_queratometria_od);
							$ancho_aux = floatval($arr_prop_imagen[0]);
							$alto_aux = floatval($arr_prop_imagen[1]);
							
							$ancho_max = 90.0;
							$alto_max = 75.0;
							
							if ($ancho_aux > $ancho_max) {
								$alto_aux = floor($alto_aux * ($ancho_max / $ancho_aux));
								$ancho_aux = floor($ancho_aux * ($ancho_max / $ancho_aux));
							}
							if ($alto_aux > $alto_max) {
								$ancho_aux = floor($ancho_aux * ($alto_max / $alto_aux));
								$alto_aux = floor($alto_aux * ($alto_max / $alto_aux));
							}
							
							//Se verifica si la imagen cabe en la página, de no ser así, se inserta una nueva página
							$alto_total = $alto_aux + 2;
							$y_aux = $pdf->validateSpaceHeight($alto_total);
							
							$x_aux = 10 + floor((95 - $ancho_aux) / 2);
							$pdf->Image($img_queratometria_od, $x_aux, $y_aux + 1, $ancho_aux, $alto_aux);
							
							$x_aux = 110 + floor((95 - $ancho_aux) / 2);
							$pdf->Image($img_queratometria_oi, $x_aux, $y_aux + 1, $ancho_aux, $alto_aux);
							
							$pdf->Cell(195, $alto_aux + 2, "", 0, 1);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 4, ajustarCaracteres("Meridiano más plano: Línea contínua"), 0, 1, 'C');
							$pdf->Cell(195, 4, ajustarCaracteres("Meridiano más curvo: Incisiones"), 0, 1, 'C');
							$pdf->Cell(195, 4, ajustarCaracteres("Incisión principal: Línea roja"), 0, 1, 'C');
							$pdf->Ln(2);
							
							$incision_arq_aux = "-";
							switch ($consulta_preqx_catarata_obj["ind_incision_arq"]) {
								case "1":
									$incision_arq_aux = "Si";
									break;
								case "0":
									$incision_arq_aux = "No";
									break;
							}
							
							$y_aux = $pdf->GetY();
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 5, ajustarCaracteres("Incisión arqueada:               Valor:     "), 0, 1, 'C');
							
							$pdf->SetY($y_aux);
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(98, 5, "", 0);
							$pdf->Cell(22, 5, ajustarCaracteres($incision_arq_aux), 0, 0, 'L');
							$pdf->Cell(10, 5, ajustarCaracteres(trim($consulta_preqx_catarata_obj["val_incision_arq"]) != "" ? $consulta_preqx_catarata_obj["val_incision_arq"] : "-"), 0, 1, 'L');
							$pdf->Ln(2);
							
							imprimir_diagnosticos($id_hc, $pdf, true);
							$pdf->Ln(2);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 5, ajustarCaracteres("Otros diagnósticos y análisis"), 0, 1, 'L');
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_preqx_catarata_obj["diagnostico_preqx_catarata"]) != "" ? cambiar_enter_br($consulta_preqx_catarata_obj["diagnostico_preqx_catarata"]) : "-");
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(5);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 5, ajustarCaracteres("Solicitud de procedimientos y exámenes complementarios"), 0, 1, 'L');
							imprimir_hc_procedimientos_solic($id_hc, $pdf, true);
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_preqx_catarata_obj["solicitud_examenes_preqx_catarata"]) != "" ? cambiar_enter_br($consulta_preqx_catarata_obj["solicitud_examenes_preqx_catarata"]) : "-");
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(3);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 5, ajustarCaracteres("Recomendaciones clínicas, médicas, optométricas y quirúrgicas"), 0, 1, 'L');
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_preqx_catarata_obj["tratamiento_preqx_catarata"]) != "" ? cambiar_enter_br($consulta_preqx_catarata_obj["tratamiento_preqx_catarata"]) : "-");
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(3);
							
							imprimir_formulacion_hc($id_hc, $pdf);
							
							imprimir_firma($consulta_preqx_catarata_obj["id_usuario_crea"], $pdf, $nombre_usuario_alt);
						}
						break;
						
					case "6": //CONSULTA PREQUIRÚRGICA LÁSER (OPTOMETRÍA)
						require_once("../db/DbConsultaPreqxLaser.php");
						require_once("../db/DbConsultaOptometria.php");
						require_once("../db/DbUsuarios.php");
						
						$db_consulta_preqx_laser = new DbConsultaPreqxLaser();
						$db_consulta_optometria = new DbConsultaOptometria();
						$db_usuarios = new DbUsuarios();
						
						imprimir_encabezado($historia_clinica_obj, $pdf);
						
						$consulta_preqx_laser_obj = $db_consulta_preqx_laser->getConsultaPreqxLaser($id_hc);
						$complemento_preqx_laser_obj = $db_consulta_preqx_laser->getComplementoPreqxLaser($id_hc);
						
						if (count($consulta_preqx_laser_obj) > 0) {
							$usuario_prof_obj = $db_usuarios->getUsuario($consulta_preqx_laser_obj["id_usuario_crea"]);
							@$usuario_prof_2_obj = $db_usuarios->getUsuario($complemento_preqx_laser_obj["id_usuario_crea"]);
							
							//Se verifica de que información de ojo se dispone
							$bol_od = false;
							$bol_oi = false;
							switch ($consulta_preqx_laser_obj["ojo"]) {
								case "OD":
									$bol_od = true;
									break;
								case "OI":
									$bol_oi = true;
									break;
								case "AO":
									$bol_od = true;
									$bol_oi = true;
									break;
							}
							
							//Se obtienen los datos de la última consulta de optometría realizada antes del registro actual
							$consulta_optometria_obj = $db_consulta_optometria->getConsultaOptometriaAnterior($id_hc);
							
							$refraccion_complemento_esfera_od = "";
							$refraccion_complemento_cilindro_od = "";
							$refraccion_complemento_eje_od = "";
							$refraccion_complemento_lejos_od = "";
							$refraccion_complemento_cerca_od = "";
							
							$refraccion_complemento_esfera_oi = "";
							$refraccion_complemento_cilindro_oi = "";
							$refraccion_complemento_eje_oi = "";
							$refraccion_complemento_lejos_oi = "";
							$refraccion_complemento_cerca_oi = "";
							
							$nombre_usuario_profesional_complemento = "";
							$fecha_complemento = "";
							
							if (isset($consulta_optometria_obj["id_hc"])) {
								if ($bol_od) {
									$refraccion_complemento_esfera_od = $consulta_optometria_obj["refrafinal_esfera_od"];
									$refraccion_complemento_cilindro_od = $consulta_optometria_obj["refrafinal_cilindro_od"];
									$refraccion_complemento_eje_od = $consulta_optometria_obj["refrafinal_eje_od"];
									$refraccion_complemento_lejos_od = $consulta_optometria_obj["subjetivo_lejos_od"];
									$refraccion_complemento_cerca_od = $consulta_optometria_obj["subjetivo_cerca_od"];
								}
								
								if ($bol_oi) {
									$refraccion_complemento_esfera_oi = $consulta_optometria_obj["refrafinal_esfera_oi"];
									$refraccion_complemento_cilindro_oi = $consulta_optometria_obj["refrafinal_cilindro_oi"];
									$refraccion_complemento_eje_oi = $consulta_optometria_obj["refrafinal_eje_oi"];
									$refraccion_complemento_lejos_oi = $consulta_optometria_obj["subjetivo_lejos_oi"];
									$refraccion_complemento_cerca_oi = $consulta_optometria_obj["subjetivo_cerca_oi"];
								}
								
								//Nombre del profesional que atendió la consulta de los datos complementarios
								$id_usuario_profesional_complemento = $consulta_optometria_obj["id_usuario_crea"];
								$usuario_profesional_complemento_obj = $db_usuarios->getUsuario($id_usuario_profesional_complemento);
								$nombre_usuario_profesional_complemento = $usuario_profesional_complemento_obj["nombre_usuario"]." ".$usuario_profesional_complemento_obj["apellido_usuario"];
								
								$fecha_complemento = $consulta_optometria_obj["fecha_hc_t"];
							}
							
							//Se obtiene el listado de agudeza visual
							$lista_ag_visual = $db_listas->getListaDetalles(11);
							$mapa_ag_visual = array();
							foreach ($lista_ag_visual as $ag_visual_aux) {
								$mapa_ag_visual[$ag_visual_aux["id_detalle"]] = $ag_visual_aux["nombre_detalle"];
							}
							
							//Se define ancho y color por defecto de las líneas
							$pdf->setDefaultLine();
							
							$pdf->SetFont("Arial", "B", 9);
							
							$pdf->Ln(5);
							
							$nombre_usuario_alt = "";
							if ($usuario_prof_obj["ind_anonimo"] == "1" && $historia_clinica_obj["nombre_usuario_alt"] != "") {
								$nombre_usuario_alt = $historia_clinica_obj["nombre_usuario_alt"];
							} else {
								$nombre_usuario_alt = $usuario_prof_obj["nombre_usuario"]." ".$usuario_prof_obj["apellido_usuario"];
							}
							
							$pdf->Cell(195, 4, ajustarCaracteres("Atiende: ".$nombre_usuario_alt), 0, 2, 'L');
							
							if ($usuario_prof_obj["id_usuario_firma"] != "") {
								//Se buscan los datos del usuario que firma
								$usuario_firma_obj = $db_usuarios->getUsuario($usuario_prof_obj["id_usuario_firma"]);
								$pdf->Cell(195, 4, ajustarCaracteres("Instructor: ".$usuario_firma_obj["nombre_usuario"]." ".$usuario_firma_obj["apellido_usuario"]), 0, 2, 'L');
							}
							
							//$pdf->MultiCell(195, 4, ajustarCaracteres("Motivo de Consulta: ".(trim($admision_obj["motivo_consulta"]) != "" ? $admision_obj["motivo_consulta"] : "-")), 0, 'L');
							//$pdf->Ln(2);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(13, 5, ajustarCaracteres("Cirugía:"), 0, 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(85, 5, ajustarCaracteres($consulta_preqx_laser_obj["nombre_cirugia"]), 0, 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(8, 5, ajustarCaracteres("Ojo:"), 0, 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(89, 5, ajustarCaracteres($consulta_preqx_laser_obj["ojo"]), 0, 1, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(31, 5, ajustarCaracteres("Fecha de la cirugía:"), 0, 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(67, 5, ajustarCaracteres($consulta_preqx_laser_obj["fecha_cirugia_t"]), 0, 0, 'L');
							$pdf->Cell(97, 5, ajustarCaracteres($consulta_preqx_laser_obj["num_cirugia"]."a cirugía"), 0, 1, 'L');
							$pdf->Ln(1);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(80, 5, "OD", "TBL", 0, 'C');
							$pdf->Cell(35, 5, "", "TB", 0, 'C');
							$pdf->Cell(80, 5, "OI", "TBR", 0, 'C');
							$pdf->Ln();
							
							//Queratometría
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Cilindro:", "BL", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion($consulta_preqx_laser_obj["querato_cilindro_od"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Eje:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion($consulta_preqx_laser_obj["querato_eje_od"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "K+Plano:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(12, 5, remplazar_vacio_guion($consulta_preqx_laser_obj["querato_kplano_od"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 10);
							$pdf->Cell(35, 5, ajustarCaracteres("Queratometría"), "B", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Cilindro:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion($consulta_preqx_laser_obj["querato_cilindro_oi"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Eje:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion($consulta_preqx_laser_obj["querato_eje_oi"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "K+Plano:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(12, 5, remplazar_vacio_guion($consulta_preqx_laser_obj["querato_kplano_oi"]), "BR", 0, 'L');
							$pdf->Ln();
							
							//Refracción
							$pdf->SetFont("Arial", "B", 10);
							$pdf->Cell(195, 5, ajustarCaracteres("Refracción"), "LR", 1, 'C');
							
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(40, 5, ajustarCaracteres($refraccion_complemento_esfera_od." / ".$refraccion_complemento_cilindro_od." / ".$refraccion_complemento_eje_od), "BLR", 0, 'C');
							$pdf->Cell(40, 5, ajustarCaracteres($consulta_preqx_laser_obj["refraccion_esfera_od"]." / ".$consulta_preqx_laser_obj["refraccion_cilindro_od"]." / ".$consulta_preqx_laser_obj["refraccion_eje_od"]), "B", 0, 'C');
							$pdf->Cell(35, 5, "", 0, 0, 'C');
							$pdf->Cell(40, 5, ajustarCaracteres($refraccion_complemento_esfera_oi." / ".$refraccion_complemento_cilindro_oi." / ".$refraccion_complemento_eje_oi), "BR", 0, 'C');
							$pdf->Cell(40, 5, ajustarCaracteres($consulta_preqx_laser_obj["refraccion_esfera_oi"]." / ".$consulta_preqx_laser_obj["refraccion_cilindro_oi"]." / ".$consulta_preqx_laser_obj["refraccion_eje_oi"]), "BR", 1, 'C');
							
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(40, 5, ajustarCaracteres(remplazar_vacio_guion(@$mapa_ag_visual[$refraccion_complemento_lejos_od])), "BLR", 0, 'C');
							$pdf->Cell(40, 5, ajustarCaracteres(remplazar_vacio_guion(@$mapa_ag_visual[$consulta_preqx_laser_obj["refraccion_lejos_od"]])), "B", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("AVCC Lejos"), 0, 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(40, 5, ajustarCaracteres(remplazar_vacio_guion(@$mapa_ag_visual[$refraccion_complemento_lejos_oi])), "BR", 0, 'C');
							$pdf->Cell(40, 5, ajustarCaracteres(remplazar_vacio_guion(@$mapa_ag_visual[$consulta_preqx_laser_obj["refraccion_lejos_oi"]])), "BR", 1, 'C');
							
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(40, 5, ajustarCaracteres(remplazar_vacio_guion(@$mapa_ag_visual[$refraccion_complemento_cerca_od])), "BLR", 0, 'C');
							$pdf->Cell(40, 5, ajustarCaracteres(remplazar_vacio_guion(@$mapa_ag_visual[$consulta_preqx_laser_obj["refraccion_cerca_od"]])), "B", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("AVCC Cerca"), 0, 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(40, 5, ajustarCaracteres(remplazar_vacio_guion(@$mapa_ag_visual[$refraccion_complemento_cerca_oi])), "BR", 0, 'C');
							$pdf->Cell(40, 5, ajustarCaracteres(remplazar_vacio_guion(@$mapa_ag_visual[$consulta_preqx_laser_obj["refraccion_cerca_oi"]])), "BR", 1, 'C');
							
							$pdf->Cell(40, 1, "", "LR", 0, 'C');
							$pdf->Cell(40, 1, "", 0, 0, 'C');
							$pdf->Cell(35, 1, "", 0, 0, 'C');
							$pdf->Cell(40, 1, "", "R", 0, 'C');
							$pdf->Cell(40, 1, "", "R", 1, 'C');
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(40, 3, ajustarCaracteres("Primer evaluador"), "LR", 0, 'C');
							$pdf->Cell(40, 3, ajustarCaracteres("Segundo evaluador"), 0, 0, 'C');
							$pdf->Cell(35, 3, "", 0, 0, 'C');
							$pdf->Cell(40, 3, ajustarCaracteres("Primer evaluador"), "R", 0, 'C');
							$pdf->Cell(40, 3, ajustarCaracteres("Segundo evaluador"), "R", 1, 'C');
							
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(40, 3, ajustarCaracteres($bol_od ? $nombre_usuario_profesional_complemento : ""), "LR", 0, 'C');
							$pdf->Cell(40, 3, ajustarCaracteres($bol_od ? $usuario_prof_obj["nombre_usuario"]." ".$usuario_prof_obj["apellido_usuario"] : ""), 0, 0, 'C');
							$pdf->Cell(35, 3, "", 0, 0, 'C');
							$pdf->Cell(40, 3, ajustarCaracteres($bol_oi ? $nombre_usuario_profesional_complemento : ""), "R", 0, 'C');
							$pdf->Cell(40, 3, ajustarCaracteres($bol_oi ? $usuario_prof_obj["nombre_usuario"]." ".$usuario_prof_obj["apellido_usuario"] : ""), "R", 1, 'C');
							
							$pdf->Cell(40, 3, ajustarCaracteres($bol_od ? $fecha_complemento : ""), "LR", 0, 'C');
							$pdf->Cell(75, 3, "", 0, 0, 'C');
							$pdf->Cell(40, 3, ajustarCaracteres($bol_oi ? $fecha_complemento : ""), "R", 0, 'C');
							$pdf->Cell(40, 3, "", "R", 1, 'C');
							
							$pdf->Cell(40, 2, "", "BL", 0, 'C');
							$pdf->Cell(40, 2, "", "B", 0, 'C');
							$pdf->Cell(35, 2, "", "B", 0, 'C');
							$pdf->Cell(40, 2, "", "B", 0, 'C');
							$pdf->Cell(40, 2, "", "BR", 1, 'C');
							
							//Cicloplejia
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(40, 5, $consulta_preqx_laser_obj["cicloplejio_esfera_od"]." / ".$consulta_preqx_laser_obj["cicloplejio_cilindro_od"]." / ".$consulta_preqx_laser_obj["cicloplejio_eje_od"], "BL", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(20, 5, "Lejos:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(20, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_preqx_laser_obj["cicloplejio_avcc_lejos_od"]]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 10);
							$pdf->Cell(35, 5, ajustarCaracteres("Cicloplejia"), "B", 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(40, 5, $consulta_preqx_laser_obj["cicloplejio_esfera_oi"]." / ".$consulta_preqx_laser_obj["cicloplejio_cilindro_oi"]." / ".$consulta_preqx_laser_obj["cicloplejio_eje_oi"], "B", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(20, 5, "Lejos:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(20, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_preqx_laser_obj["cicloplejio_avcc_lejos_oi"]]), "BR", 1, 'L');
							
							//Defecto refractivo programado
							$pdf->SetFont("Arial", "B", 10);
							$pdf->Cell(195, 5, ajustarCaracteres("Defecto refractivo programado"), "LR", 1, 'C');
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(30, 5, "Valor deseado:", "BL", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion(@$consulta_preqx_laser_obj["refractivo_deseado_od"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(40, 5, $consulta_preqx_laser_obj["refractivo_esfera_od"]." / ".$consulta_preqx_laser_obj["refractivo_cilindro_od"]." / ".$consulta_preqx_laser_obj["refractivo_eje_od"], "B", 0, 'C');
							$pdf->Cell(35, 5, "", "B", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(30, 5, "Valor deseado:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion(@$consulta_preqx_laser_obj["refractivo_deseado_oi"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(40, 5, $consulta_preqx_laser_obj["refractivo_esfera_oi"]." / ".$consulta_preqx_laser_obj["refractivo_cilindro_oi"]." / ".$consulta_preqx_laser_obj["refractivo_eje_oi"], "BR", 1, 'C');
							
							//Nomograma
							$pdf->SetFont("Arial", "B", 10);
							$pdf->Cell(195, 5, ajustarCaracteres("Nomograma"), "LR", 1, 'C');
							
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, $consulta_preqx_laser_obj["nomograma_esfera_od"]." / ".$consulta_preqx_laser_obj["nomograma_cilindro_od"]." / ".$consulta_preqx_laser_obj["nomograma_eje_od"], "BL", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(19, 5, ajustarCaracteres("Equipo:"), "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(16, 5, ajustarCaracteres(remplazar_vacio_guion($consulta_preqx_laser_obj["nombre_nomograma_equipo"])), "B", 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, $consulta_preqx_laser_obj["nomograma_esfera_oi"]." / ".$consulta_preqx_laser_obj["nomograma_cilindro_oi"]." / ".$consulta_preqx_laser_obj["nomograma_eje_oi"], "BR", 1, 'C');
							
							$bol_observ_aux = false;
							$y_aux = $pdf->GetY();
							if (trim($consulta_preqx_laser_obj["patologia_ocular_descripcion"]) != "") {
								$bol_observ_aux = true;
								$texto_aux = ajustarCaracteres("<b>Patología ocular: </b>".$consulta_preqx_laser_obj["patologia_ocular_descripcion"]);
								$pdf->Ln(2);
								$pdfHTML->WriteHTML($texto_aux, $pdf);
								$pdf->Ln(3);
							}
							if (trim($consulta_preqx_laser_obj["cirugia_ocular_descripcion"]) != "") {
								$bol_observ_aux = true;
								$texto_aux = ajustarCaracteres("<b>Cirugías oculares previas: </b>".$consulta_preqx_laser_obj["cirugia_ocular_descripcion"]);
								$pdf->Ln(2);
								$pdfHTML->WriteHTML($texto_aux, $pdf);
								$pdf->Ln(3);
							}
							
							if ($bol_observ_aux) {
								$y_aux2 = $pdf->GetY();
								$pdf->SetY($y_aux);
								$pdf->Cell(195, ($y_aux2 - $y_aux), "", "BLR", 1, 'C');
							}
							
							//Paquimetría
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(24, 5, ajustarCaracteres("Central:"), "BL", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(16, 5, ajustarCaracteres(remplazar_vacio_guion($consulta_preqx_laser_obj["paquimetria_central_od"])), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(24, 5, ajustarCaracteres("Periférica:"), "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(16, 5, ajustarCaracteres(remplazar_vacio_guion($consulta_preqx_laser_obj["paquimetria_periferica_od"])), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 10);
							$pdf->Cell(35, 5, ajustarCaracteres("Paquimetría"), "B", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(24, 5, ajustarCaracteres("Central:"), "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(16, 5, ajustarCaracteres(remplazar_vacio_guion($consulta_preqx_laser_obj["paquimetria_central_oi"])), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(24, 5, ajustarCaracteres("Periférica:"), "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(16, 5, ajustarCaracteres(remplazar_vacio_guion($consulta_preqx_laser_obj["paquimetria_periferica_oi"])), "BR", 1, 'L');
							
							$pdf->Ln(2);
							imprimir_diagnosticos($id_hc, $pdf, true);
							$pdf->Ln(1);
							
							//Observaciones
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(45, 5, ajustarCaracteres("Otros Diagnósticos y Análisis"), 0, 1, 'L');
							$pdf->Ln(1);
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_preqx_laser_obj["diagnostico_preqx_laser"]) != "" ? $consulta_preqx_laser_obj["diagnostico_preqx_laser"] : "-");
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(5);
							
							imprimir_firma($consulta_preqx_laser_obj["id_usuario_crea"], $pdf, $nombre_usuario_alt);
						}
						break;
						
					case "7": //CONSULTA PREQUIRÚRGICA LÁSER (OFTALMOLOGÍA)
						require_once("../db/DbConsultaPreqxLaserOf.php");
						$db_consulta_preqx_laser_of = new DbConsultaPreqxLaserOf();
						
						imprimir_encabezado($historia_clinica_obj, $pdf);
						
						$consulta_preqx_laser_of_obj = $db_consulta_preqx_laser_of->getConsultaPreqxLaserOf($id_hc);
						
						if (count($consulta_preqx_laser_of_obj) > 0) {
							$usuario_prof_obj = $db_usuarios->getUsuario($consulta_preqx_laser_of_obj["id_usuario_crea"]);
							
							//Se verifica de que información de ojo se dispone
							$bol_od = false;
							$bol_oi = false;
							switch ($consulta_preqx_laser_of_obj["ojo"]) {
								case "OD":
									$bol_od = true;
									break;
								case "OI":
									$bol_oi = true;
									break;
								case "AO":
									$bol_od = true;
									$bol_oi = true;
									break;
							}
							
							//Se define ancho y color por defecto de las líneas
							$pdf->setDefaultLine();
							
							$pdf->SetFont("Arial", "B", 9);
							
							$pdf->Ln(5);
							
							$nombre_usuario_alt = "";
							if ($usuario_prof_obj["ind_anonimo"] == "1" && $historia_clinica_obj["nombre_usuario_alt"] != "") {
								$nombre_usuario_alt = $historia_clinica_obj["nombre_usuario_alt"];
							} else {
								$nombre_usuario_alt = $usuario_prof_obj["nombre_usuario"]." ".$usuario_prof_obj["apellido_usuario"];
							}
							
							$pdf->Cell(195, 4, ajustarCaracteres("Atiende: ".$nombre_usuario_alt), 0, 2, 'L');
							
							if ($usuario_prof_obj["id_usuario_firma"] != "") {
								//Se buscan los datos del usuario que firma
								$usuario_firma_obj = $db_usuarios->getUsuario($usuario_prof_obj["id_usuario_firma"]);
								$pdf->Cell(195, 4, ajustarCaracteres("Instructor: ".$usuario_firma_obj["nombre_usuario"]." ".$usuario_firma_obj["apellido_usuario"]), 0, 2, 'L');
							}
							
							//$pdf->MultiCell(195, 4, ajustarCaracteres("Motivo de Consulta: ".(trim($admision_obj["motivo_consulta"]) != "" ? $admision_obj["motivo_consulta"] : "-")), 0, 'L');
							//$pdf->Ln(2);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(13, 5, ajustarCaracteres("Cirugía:"), 0, 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(85, 5, ajustarCaracteres($consulta_preqx_laser_of_obj["nombre_cirugia"]), 0, 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(8, 5, ajustarCaracteres("Ojo:"), 0, 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(89, 5, ajustarCaracteres($consulta_preqx_laser_of_obj["ojo"]), 0, 1, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(31, 5, ajustarCaracteres("Fecha de la cirugía:"), 0, 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(67, 5, ajustarCaracteres($consulta_preqx_laser_of_obj["fecha_cirugia_t"]), 0, 0, 'L');
							$pdf->Cell(97, 5, ajustarCaracteres($consulta_preqx_laser_of_obj["num_cirugia"]."a cirugía"), 0, 1, 'L');
							
							$texto_aux = ajustarCaracteres("<b>Subjetivo: </b>".(trim($consulta_preqx_laser_of_obj["preqx_laser_subjetivo"]) != "" ? $consulta_preqx_laser_of_obj["preqx_laser_subjetivo"] : "-"));
							$pdf->Ln(2);
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(3);
							
							$texto_aux = ajustarCaracteres("<b>Biomicroscopía: </b>".(trim($consulta_preqx_laser_of_obj["preqx_laser_biomiocroscopia"]) != "" ? $consulta_preqx_laser_of_obj["preqx_laser_biomiocroscopia"] : "-"));
							$pdf->Ln(2);
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(3);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(80, 5, "OD", "TBL", 0, 'C');
							$pdf->Cell(35, 5, "", "TB", 0, 'C');
							$pdf->Cell(80, 5, "OI", "TBR", 0, 'C');
							$pdf->Ln();
							
							//Presión intraocular neumática
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, (trim($consulta_preqx_laser_of_obj["presion_intraocular_od"]) != "" ? $consulta_preqx_laser_of_obj["presion_intraocular_od"] . " mmHg" : "-"), "BL", 0, 'C');
							$pdf->SetFont("Arial", "B", 10);
							$pdf->Cell(35, 5, ajustarCaracteres("Presión intraocular neumática"), "B", 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, (trim($consulta_preqx_laser_of_obj["presion_intraocular_oi"]) != "" ? $consulta_preqx_laser_of_obj["presion_intraocular_oi"] . " mmHg" : "-"), "BR", 1, 'C');
							
							//Fondo de ojo
							$pdf->SetFont("Arial", "B", 10);
							$pdf->Cell(195, 5, ajustarCaracteres("Fondo de ojo"), "LR", 1, 'C');
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Nervio óptico
							$y_aux = $pdf->GetY();
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_preqx_laser_of_obj["fondo_ojo_nervio_optico_od"] != "" ? $consulta_preqx_laser_of_obj["fondo_ojo_nervio_optico_od"] : "-"), 0, 'C');
							$y_aux2 = $pdf->GetY();
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(130, $y_aux);
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_preqx_laser_of_obj["fondo_ojo_nervio_optico_oi"] != "" ? $consulta_preqx_laser_of_obj["fondo_ojo_nervio_optico_oi"] : "-"), 0, 'C');
							if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
								$y_aux2 = $pdf->GetY();
							}
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(85, $y_aux);
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Nervio óptico"), 0, 0, 'C');
							$pdf->Ln();
							
							$pdf->SetXY(10, $y_aux);
							$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 1, 'C');
							
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Mácula
							$y_aux = $pdf->GetY();
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_preqx_laser_of_obj["fondo_ojo_macula_od"] != "" ? $consulta_preqx_laser_of_obj["fondo_ojo_macula_od"] : "-"), 0, 'C');
							$y_aux2 = $pdf->GetY();
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(130, $y_aux);
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_preqx_laser_of_obj["fondo_ojo_macula_oi"] != "" ? $consulta_preqx_laser_of_obj["fondo_ojo_macula_oi"] : "-"), 0, 'C');
							if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
								$y_aux2 = $pdf->GetY();
							}
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(85, $y_aux);
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Mácula"), 0, 0, 'C');
							$pdf->Ln();
							
							$pdf->SetXY(10, $y_aux);
							$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 1, 'C');
							
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Periferia
							$y_aux = $pdf->GetY();
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_preqx_laser_of_obj["fondo_ojo_periferia_od"] != "" ? $consulta_preqx_laser_of_obj["fondo_ojo_periferia_od"] : "-"), 0, 'C');
							$y_aux2 = $pdf->GetY();
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(130, $y_aux);
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_preqx_laser_of_obj["fondo_ojo_periferia_oi"] != "" ? $consulta_preqx_laser_of_obj["fondo_ojo_periferia_oi"] : "-"), 0, 'C');
							if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
								$y_aux2 = $pdf->GetY();
							}
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(85, $y_aux);
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Periferia"), 0, 0, 'C');
							$pdf->Ln();
							
							$pdf->SetXY(10, $y_aux);
							$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 1, 'C');
							
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Vítreo
							$y_aux = $pdf->GetY();
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_preqx_laser_of_obj["fondo_ojo_vitreo_od"] != "" ? $consulta_preqx_laser_of_obj["fondo_ojo_vitreo_od"] : "-"), 0, 'C');
							$y_aux2 = $pdf->GetY();
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(130, $y_aux);
							$pdf->SetFont("Arial", "", 9);
							$pdf->MultiCell(75, 5, ajustarCaracteres($consulta_preqx_laser_of_obj["fondo_ojo_vitreo_oi"] != "" ? $consulta_preqx_laser_of_obj["fondo_ojo_vitreo_oi"] : "-"), 0, 'C');
							if ($pdf->GetY() > $y_aux2 || $y_aux > $pdf->GetY()) {
								$y_aux2 = $pdf->GetY();
							}
							
							if ($y_aux > $y_aux2) {
								//Hubo salto de página
								$y_aux = 30;
							}
							
							$pdf->SetXY(85, $y_aux);
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(45, ($y_aux2 - $y_aux), ajustarCaracteres("Vítreo"), 0, 0, 'C');
							$pdf->Ln();
							
							$pdf->SetXY(10, $y_aux);
							$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 1, 'C');
							
							$x_aux = $pdf->GetX();
							$y_aux = $pdf->GetY();
							$pdf->Line($x_aux, $y_aux, $x_aux + 195, $y_aux);
							
							//Plan
							$texto_aux = ajustarCaracteres("<b>Plan: </b>".(trim($consulta_preqx_laser_of_obj["preqx_laser_plan"]) != "" ? $consulta_preqx_laser_of_obj["preqx_laser_plan"] : "-"));
							$pdf->Ln(2);
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(3);
							
							$pdf->Ln(2);
							imprimir_diagnosticos($id_hc, $pdf, true);
							$pdf->Ln(1);

							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 5, ajustarCaracteres("Otros diagnósticos y análisis"), 0, 1, 'L');
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_preqx_laser_of_obj["diagnostico_preqx_laser_of"]));
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(5);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 5, ajustarCaracteres("Solicitud de procedimientos y exámenes complementarios"), 0, 1, 'L');
							imprimir_hc_procedimientos_solic($id_hc, $pdf, true);
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_preqx_laser_of_obj["solicitud_examenes_preqx_laser"]) != "" ? cambiar_enter_br($consulta_preqx_laser_of_obj["solicitud_examenes_preqx_laser"]) : "-");
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(3);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 5, ajustarCaracteres("Recomendaciones clínicas, médicas, optométricas y quirúrgicas"), 0, 1, 'L');
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_preqx_laser_of_obj["tratamiento_preqx_laser"]) != "" ? cambiar_enter_br($consulta_preqx_laser_of_obj["tratamiento_preqx_laser"]) : "-");
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(3);
							
							imprimir_formulacion_hc($id_hc, $pdf);
							
							imprimir_firma($consulta_preqx_laser_of_obj["id_usuario_crea"], $pdf, $nombre_usuario_alt);
						}
						break;
						
					case "8": //CONSULTA CONTROL LÁSER (OPTOMETRIA)
						require_once("../db/DbConsultaControlLaser.php");
						$db_consulta_control_laser = new DbConsultaControlLaser();
						
						imprimir_encabezado($historia_clinica_obj, $pdf);
						
						$consulta_control_laser_obj = $db_consulta_control_laser->getConsultaControlLaser($id_hc);
						
						if (count($consulta_control_laser_obj) > 0) {
							$usuario_prof_obj = $db_usuarios->getUsuario($consulta_control_laser_obj["id_usuario_crea"]);
							
							//Se obtiene el listado de agudeza visual
							$lista_ag_visual = $db_listas->getListaDetalles(11);
							$mapa_ag_visual = array();
							foreach ($lista_ag_visual as $ag_visual_aux) {
								$mapa_ag_visual[$ag_visual_aux["id_detalle"]] = $ag_visual_aux["nombre_detalle"];
							}
							
							//Se define ancho y color por defecto de las líneas
							$pdf->setDefaultLine();
							
							$pdf->SetFont("Arial", "B", 9);
							
							$pdf->Ln(5);
							
							$nombre_usuario_alt = "";
							if ($usuario_prof_obj["ind_anonimo"] == "1" && $historia_clinica_obj["nombre_usuario_alt"] != "") {
								$nombre_usuario_alt = $historia_clinica_obj["nombre_usuario_alt"];
							} else {
								$nombre_usuario_alt = $usuario_prof_obj["nombre_usuario"]." ".$usuario_prof_obj["apellido_usuario"];
							}
							
							$pdf->Cell(195, 4, ajustarCaracteres("Atiende: ".$nombre_usuario_alt), 0, 2, 'L');
							
							if ($usuario_prof_obj["id_usuario_firma"] != "") {
								//Se buscan los datos del usuario que firma
								$usuario_firma_obj = $db_usuarios->getUsuario($usuario_prof_obj["id_usuario_firma"]);
								$pdf->Cell(195, 4, ajustarCaracteres("Instructor: ".$usuario_firma_obj["nombre_usuario"]." ".$usuario_firma_obj["apellido_usuario"]), 0, 2, 'L');
							}
							
							//$pdf->MultiCell(195, 4, ajustarCaracteres("Motivo de Consulta: ".(trim($admision_obj["motivo_consulta"]) != "" ? $admision_obj["motivo_consulta"] : "-")), 0, 'L');
							//$pdf->Ln(2);
							
							$texto_anamnesis = ajustarCaracteres($consulta_control_laser_obj["anamnesis"]);
							$pdfHTML->WriteHTML("<b>Anamnesis: </b>".$texto_anamnesis, $pdf);
							$pdf->Ln(3);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(80, 5, "OD", "TBL", 0, 'C');
							$pdf->Cell(35, 5, "", "TB", 0, 'C');
							$pdf->Cell(80, 5, "OI", "TBR", 0, 'C');
							$pdf->Ln();
							
							//AVSC
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(20, 5, "Lejos:", "BL", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(20, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_control_laser_obj["avsc_lejos_od"]]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(20, 5, "Cerca:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(20, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_control_laser_obj["avsc_cerca_od"]]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, "AVSC", "B", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(20, 5, "Lejos:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(20, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_control_laser_obj["avsc_lejos_oi"]]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(20, 5, "Cerca:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(20, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_control_laser_obj["avsc_cerca_oi"]]), "BR", 0, 'L');
							$pdf->Ln();
							
							//Queratometría
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Cilindro:", "BL", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion($consulta_control_laser_obj["querato_cilindro_od"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Eje:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion($consulta_control_laser_obj["querato_eje_od"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "K+Plano:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(12, 5, remplazar_vacio_guion($consulta_control_laser_obj["querato_mplano_od"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("Queratometría"), "B", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Cilindro:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion($consulta_control_laser_obj["querato_cilindro_oi"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Eje:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion($consulta_control_laser_obj["querato_eje_oi"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "K+Plano:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(12, 5, remplazar_vacio_guion($consulta_control_laser_obj["querato_mplano_oi"]), "BR", 0, 'L');
							$pdf->Ln();
							
							//Refracción Final
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(52, 5, $consulta_control_laser_obj["avc_esfera_od"]." / ".$consulta_control_laser_obj["avc_cilindro_od"]." / ".$consulta_control_laser_obj["avc_eje_od"], "L", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, ajustarCaracteres("Adición:"), 0, 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(12, 5, remplazar_vacio_guion($consulta_control_laser_obj["avcc_adicion_od"]), 0, 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 10, ajustarCaracteres("Refracción y AVCC"), 0, 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(52, 5, $consulta_control_laser_obj["avc_esfera_oi"]." / ".$consulta_control_laser_obj["avc_cilindro_oi"]." / ".$consulta_control_laser_obj["avc_eje_oi"], 0, 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, ajustarCaracteres("Adición:"), 0, 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(12, 5, remplazar_vacio_guion($consulta_control_laser_obj["avcc_adicion_oi"]), "R", 1, 'L');
							
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 90, $y_aux);
							$pdf->Line(125, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(20, 5, ajustarCaracteres("Lejos:"), "L", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(20, 5, @$mapa_ag_visual[$consulta_control_laser_obj["avcc_lejos_od"]], 0, 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(20, 5, ajustarCaracteres("Cerca:"), 0, 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(20, 5, @$mapa_ag_visual[$consulta_control_laser_obj["avcc_cerca_od"]], 0, 0, 'L');
							$pdf->Cell(35, 5, "", 0, 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(20, 5, ajustarCaracteres("Lejos:"), 0, 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(20, 5, @$mapa_ag_visual[$consulta_control_laser_obj["avcc_lejos_oi"]], 0, 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(20, 5, ajustarCaracteres("Cerca:"), 0, 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(20, 5, @$mapa_ag_visual[$consulta_control_laser_obj["avcc_cerca_oi"]], "R", 1, 'L');
							
							if (trim($consulta_control_laser_obj["observaciones_avc"]) != "") {
								$y_aux = $pdf->GetY();
								$pdf->setSeparationLine();
								$pdf->Line(10, $y_aux, 205, $y_aux);
								$pdf->setDefaultLine();
								$pdf->Ln(1);
								$texto_rx_final = ajustarCaracteres($consulta_control_laser_obj["observaciones_avc"]);
								$pdfHTML->WriteHTML(ajustarCaracteres("<b>Fórmula de gafas: </b>").$texto_rx_final, $pdf);
								$pdf->Ln(3);
								$y_aux2 = $pdf->GetY();
								$pdf->SetX($x_aux);
								$pdf->SetY($y_aux);
								$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 0, 'L');
								$pdf->Ln();
							}
							$y_aux = $pdf->GetY();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							
							//Diagnósticos
							$pdf->Ln(2);
							imprimir_diagnosticos($id_hc, $pdf, true);
							$pdf->Ln(1);
							
							//Observaciones
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(45, 5, ajustarCaracteres("Otros Diagnósticos y Análisis"), 0, 1, 'L');
							$pdf->Ln(1);
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_control_laser_obj["diagnostico_control_laser"]) != "" ? $consulta_control_laser_obj["diagnostico_control_laser"] : "-");
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(5);
							
							imprimir_firma($consulta_control_laser_obj["id_usuario_crea"], $pdf, $nombre_usuario_alt);
						}
						break;
						
					case "9": //CONSULTA CONTROL LÁSER (OFTALMOLOGÍA)
						require_once("../db/DbConsultaControlLaserOf.php");
						$db_consulta_control_laser_of = new DbConsultaControlLaserOf();
						
						imprimir_encabezado($historia_clinica_obj, $pdf);
						
						$consulta_control_laser_of_obj = $db_consulta_control_laser_of->getConsultaControlLaserOf($id_hc);
						
						if (count($consulta_control_laser_of_obj) > 0) {
							$usuario_prof_obj = $db_usuarios->getUsuario($consulta_control_laser_of_obj["id_usuario_crea"]);
							
							//Se define ancho y color por defecto de las líneas
							$pdf->setDefaultLine();
							
							$pdf->SetFont("Arial", "B", 9);
							
							$pdf->Ln(5);
							
							$nombre_usuario_alt = "";
							if ($usuario_prof_obj["ind_anonimo"] == "1" && $historia_clinica_obj["nombre_usuario_alt"] != "") {
								$nombre_usuario_alt = $historia_clinica_obj["nombre_usuario_alt"];
							} else {
								$nombre_usuario_alt = $usuario_prof_obj["nombre_usuario"]." ".$usuario_prof_obj["apellido_usuario"];
							}
							
							$pdf->Cell(195, 4, ajustarCaracteres("Atiende: ".$nombre_usuario_alt), 0, 2, 'L');
							
							if ($usuario_prof_obj["id_usuario_firma"] != "") {
								//Se buscan los datos del usuario que firma
								$usuario_firma_obj = $db_usuarios->getUsuario($usuario_prof_obj["id_usuario_firma"]);
								$pdf->Cell(195, 4, ajustarCaracteres("Instructor: ".$usuario_firma_obj["nombre_usuario"]." ".$usuario_firma_obj["apellido_usuario"]), 0, 2, 'L');
							}
							
							//$pdf->MultiCell(195, 4, ajustarCaracteres("Motivo de Consulta: ".(trim($admision_obj["motivo_consulta"]) != "" ? $admision_obj["motivo_consulta"] : "-")), 0, 'L');
							//$pdf->Ln(2);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(80, 5, "OD", "TBL", 0, 'C');
							$pdf->Cell(35, 5, "", "TB", 0, 'C');
							$pdf->Cell(80, 5, "OI", "TBR", 0, 'C');
							$pdf->Ln();
							
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres(trim($consulta_control_laser_of_obj["presion_intraocular_aplanatica_od"]) != "" ? $consulta_control_laser_of_obj["presion_intraocular_aplanatica_od"] . " mmHg" : "-"), "BL", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("Presión intraocular aplanática"), "B", 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres(trim($consulta_control_laser_of_obj["presion_intraocular_aplanatica_oi"]) != "" ? $consulta_control_laser_of_obj["presion_intraocular_aplanatica_oi"] . " mmHg" : "-"), "BR", 1, 'C');
							
							$y_aux = $pdf->GetY();
							$pdf->Ln(1);
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres("<b>Hallazgos: </b>".trim($consulta_control_laser_of_obj["hallazgos_control_laser"]));
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(3);
							$y_aux2 = $pdf->GetY();
							$pdf->SetY($y_aux);
							$pdf->Cell(195, ($y_aux2 - $y_aux), "", "BLR", 1, 'C');
							
							$pdf->Ln(2);
							imprimir_diagnosticos($id_hc, $pdf, true);
							$pdf->Ln(1);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 5, ajustarCaracteres("Otros diagnósticos y análisis"), 0, 1, 'L');
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_control_laser_of_obj["diagnostico_control_laser_of"]));
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(5);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 5, ajustarCaracteres("Solicitud de procedimientos y exámenes complementarios"), 0, 1, 'L');
							imprimir_hc_procedimientos_solic($id_hc, $pdf, true);
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_control_laser_of_obj["solicitud_examenes_control_laser"]) != "" ? cambiar_enter_br($consulta_control_laser_of_obj["solicitud_examenes_control_laser"]) : "-");
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(3);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 5, ajustarCaracteres("Recomendaciones clínicas, médicas, optométricas y quirúrgicas"), 0, 1, 'L');
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_control_laser_of_obj["tratamiento_control_laser"]) != "" ? cambiar_enter_br($consulta_control_laser_of_obj["tratamiento_control_laser"]) : "-");
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(3);
							
							imprimir_formulacion_hc($id_hc, $pdf);
							
							imprimir_firma($consulta_control_laser_of_obj["id_usuario_crea"], $pdf, $nombre_usuario_alt);
						}
						break;
						
					case "10": //EXAMEN (OPTOMETRÍA)
						require_once("../db/DbExamenesOptometria.php");
						$db_examenes_optometria = new DbExamenesOptometria();
						
						imprimir_encabezado($historia_clinica_obj, $pdf);
						
						$examen_optometria_obj = $db_examenes_optometria->get_examen_optometria($id_hc);
												
							
						
						$bol_agregar_pagina = false;
						if (count($examen_optometria_obj) > 0) {
							$usuario_prof_obj = $db_usuarios->getUsuario($examen_optometria_obj["id_usuario_crea"]);
							
							//Se define ancho y color por defecto de las líneas
							$pdf->setDefaultLine();
							
							$pdf->SetFont("Arial", "B", 9);
							
							$pdf->Ln(5);
							
							$nombre_usuario_alt = "";
							if ($usuario_prof_obj["ind_anonimo"] == "1" && $historia_clinica_obj["nombre_usuario_alt"] != "") {
								$nombre_usuario_alt = $historia_clinica_obj["nombre_usuario_alt"];
							} else {
								$nombre_usuario_alt = $usuario_prof_obj["nombre_usuario"]." ".$usuario_prof_obj["apellido_usuario"];
							}
							
							$pdf->Cell(195, 4, ajustarCaracteres("Atiende: ".$nombre_usuario_alt), 0, 2, 'L');
							
							if ($usuario_prof_obj["id_usuario_firma"] != "") {
								//Se buscan los datos del usuario que firma
								$usuario_firma_obj = $db_usuarios->getUsuario($usuario_prof_obj["id_usuario_firma"]);
								$pdf->Cell(195, 4, ajustarCaracteres("Instructor: ".$usuario_firma_obj["nombre_usuario"]." ".$usuario_firma_obj["apellido_usuario"]), 0, 2, 'L');
							}
							
							//$pdf->MultiCell(195, 4, ajustarCaracteres("Motivo de Consulta: ".(trim($admision_obj["motivo_consulta"]) != "" ? $admision_obj["motivo_consulta"] : "-")), 0, 'L');
							//$pdf->Ln(2);
							
							//Se obtiene el listado de exámenes
							$lista_examenes = $db_examenes_optometria->get_lista_examenes_optometria_hc($id_hc);
							
							if (count($lista_examenes) > 0) {
								$cont_base = 0;
								for ($i = 0; $i < count($lista_examenes); $i++) {
									$examen_aux = $lista_examenes[$i];
									if ($ind_imagenes_hc != 1 || $examen_aux["id_examen_hc"] == $historia_clinica_obj["id_examen_hc"]) {
										//Se obtienen los archivos asociados al examen
										$lista_archivos = $db_examenes_optometria->get_lista_examenes_optometria_hc_det2($examen_aux["id_examen_hc"]);
										
										//Si no se trata del primer examen y el exámen tiene adjuntos, se inserta una nueva página
										$bol_nueva_pagina = false;
										if (count($lista_archivos) > 0) {
											foreach ($lista_archivos as $archivo_aux) {
												$ruta_arch_examen = trim($archivo_aux["ruta_arch_examen"]);
												
												
												//Se verifica si hay algún archivo que mostrar
												if ($ruta_arch_examen != "") {
													$ruta_arch_examen = str_replace("../imagenes/imagenes_hce", $ruta_base, $ruta_arch_examen);
													
												}
												if ($ruta_arch_examen != "" && file_exists($ruta_arch_examen)) {
													$bol_nueva_pagina = true;
												}
											}
										}
										
										if (($bol_nueva_pagina && $cont_base > 0) || $bol_agregar_pagina) {
											$pdf->AddPage();
										}
										if ($bol_nueva_pagina) {
											$bol_agregar_pagina = true;
										} else {
											$bol_agregar_pagina = false;
										}

										$pdf->SetFont("Arial", "B", 10);
										$pdf->Cell(195, 5, ajustarCaracteres("Examen No. ".($i + 1)), 0, 1, 'C');
										$pdf->SetFont("Arial", "B", 9);
										$pdf->Cell(150, 5, ajustarCaracteres("Tipo de examen: ".$examen_aux["nombre_examen"]), 0, 0, 'L');
										$pdf->Cell(45, 5, ajustarCaracteres("Ojo: ".$examen_aux["ojo"]), 0, 1, 'L');
										$pdf->Ln(2);
										
										if (trim($examen_aux["observaciones_examen"]) != "") {
											$pdf->SetFont("Arial", "", 9);
											$texto_aux = ajustarCaracteres(trim($examen_aux["observaciones_examen"]));
											$pdfHTML->WriteHTML($texto_aux, $pdf);
											$pdf->Ln(3);
										}
										
										switch ($examen_aux["id_examen_compl"]) {
											case "344": //Paquimetría ultrasónica
												$pdf->SetFont("Arial", "", 10);
												$pdf->Cell(70, 5, ajustarCaracteres("OD: ".($examen_aux["pu_od"] != "" ? $examen_aux["pu_od"] : "-")), 0, 0, "C");
												$pdf->SetFont("Arial", "B", 9);
												$pdf->Cell(55, 5, ajustarCaracteres("Paquimetría ultrasónica"), 0, 0, "C");
												$pdf->SetFont("Arial", "", 10);
												$pdf->Cell(70, 5, ajustarCaracteres("OI: ".($examen_aux["pu_oi"] != "" ? $examen_aux["pu_oi"] : "-")), 0, 1, "C");
												$pdf->Ln(2);
												break;
										}
										
										if (count($lista_archivos) > 0) {
											for ($j = 0; $j < count($lista_archivos); $j++) {
												$archivo_aux = $lista_archivos[$j];
												$ruta_arch_examen = trim($archivo_aux["ruta_arch_examen"]);
												if ($ruta_arch_examen != "") {
													$ruta_arch_examen = str_replace("../imagenes/imagenes_hce", $ruta_base, $ruta_arch_examen);
												}
												
												//Se verifica si hay algún archivo que mostrar
												if ($ruta_arch_examen != "" && file_exists($ruta_arch_examen)) {
													//Se obtiene el tipo de archivo
													$extension = $utilidades->get_extension_arch($ruta_arch_examen);
													$ancho_max = 600;
													$alto_max = 700;
													switch ($extension) {
														case "jpg":
														case "png":
														case "bmp":
														case "gif":
															//Se obtienen las dimensiones del archivo
															$arr_prop_imagen = getimagesize($ruta_arch_examen);
															$ancho_aux = floatval($arr_prop_imagen[0]);
															$alto_aux = floatval($arr_prop_imagen[1]);
															
															$ancho_max = 195.0;
															$y_aux = $pdf->GetY();
															$alto_max = 266 - $y_aux;
															
															if ($alto_max < 120 || ($alto_max < 160 && $ancho_aux < $alto_aux)) {
																$pdf->AddPage();
																$y_aux = $pdf->GetY();
																$alto_max = 266 - $y_aux;
															}
															if ($ancho_aux > $ancho_max) {
																$alto_aux = floor($alto_aux * ($ancho_max / $ancho_aux));
																$ancho_aux = floor($ancho_aux * ($ancho_max / $ancho_aux));
															}
															if ($alto_aux > $alto_max) {
																$ancho_aux = floor($ancho_aux * ($alto_max / $alto_aux));
																$alto_aux = floor($alto_aux * ($alto_max / $alto_aux));
															}
															
															$pdf->Image($ruta_arch_examen, 10 + (195 - $ancho_aux) / 2, $y_aux, $ancho_aux, $alto_aux);
															$pdf->Cell(195, $alto_aux, "", 0, 1);
															$pdf->Ln(2);
															break;
															
														case "pdf":
															//Se convierten las páginas del pdf a jpg
															$prefijo_aux = __DIR__."\\tmp\\"."img_examen_".$id_usuario."_".$id_hc."_".$i."_".$j."_";
															$comando_aux = "\"".$gs."\" -dNOPAUSE -sDEVICE=jpeg -dUseCIEColor -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -r150x150 -dJPEGQ=90 -sOutputFile=\"".$prefijo_aux."%d.jpg\" \"".$ruta_arch_examen."\" -c quit";
															$salida_aux = array();
															exec($comando_aux, $salida_aux, $resultado_aux);
															
															if ($resultado_aux == "0") {
																$cont_aux = 1;
																$ruta_imagen_tmp = $prefijo_aux.$cont_aux.".jpg";
																while (file_exists($ruta_imagen_tmp)) {
																	//Se obtienen las dimensiones del archivo
																	$arr_prop_imagen = getimagesize($ruta_imagen_tmp);
																	$ancho_aux = floatval($arr_prop_imagen[0]);
																	$alto_aux = floatval($arr_prop_imagen[1]);
																	
																	$ancho_max = 195.0;
																	$y_aux = $pdf->GetY();
																	$alto_max = 266 - $y_aux;
																	
																	if ($alto_max < 120 || ($alto_max < 160 && $ancho_aux < $alto_aux)) {
																		$pdf->AddPage();
																		$y_aux = $pdf->GetY();
																		$alto_max = 266 - $y_aux;
																	}
																	if ($ancho_aux > $ancho_max) {
																		$alto_aux = floor($alto_aux * ($ancho_max / $ancho_aux));
																		$ancho_aux = floor($ancho_aux * ($ancho_max / $ancho_aux));
																	}
																	if ($alto_aux > $alto_max) {
																		$ancho_aux = floor($ancho_aux * ($alto_max / $alto_aux));
																		$alto_aux = floor($alto_aux * ($alto_max / $alto_aux));
																	}
																	
																	$pdf->Image($ruta_imagen_tmp, 10 + (195 - $ancho_aux) / 2, $y_aux, $ancho_aux, $alto_aux);
																	$pdf->Cell(195, $alto_aux, "", 0, 1);
																	$pdf->Ln(2);
																	
																	unlink($ruta_imagen_tmp);
																	
																	$cont_aux++;
																	$ruta_imagen_tmp = $prefijo_aux.$cont_aux.".jpg";
																}
															}
															break;
															
														default:
															break;
													}
												}
											}
										}
										$cont_base++;
									}
								}
							}
							
							if ($ind_imagenes_hc != 1) {
								if ($bol_agregar_pagina > 0) {
									$pdf->AddPage();
								}
								
								//Diagnósticos
								$pdf->Ln(2);
								imprimir_diagnosticos($id_hc, $pdf, true);
								$pdf->Ln(1);
								
								imprimir_firma($examen_optometria_obj["id_usuario_crea"], $pdf, $nombre_usuario_alt);
								
							
							}
						}
						
						break;
						
					case "11": //PROCEDIMIENTO QUIRÚRGICO LÁSER
						require_once("../db/DbCirugias.php");
						$db_cirugias = new DbCirugias();
						
						imprimir_encabezado($historia_clinica_obj, $pdf);
						
						$cirugia_obj = $db_cirugias->get_cirugia_laser($id_hc);
						
						if (count($cirugia_obj) > 0) {
							$usuario_prof_obj = $db_usuarios->getUsuario($cirugia_obj["id_usuario_crea"]);
							
							//Se define ancho y color por defecto de las líneas
							$pdf->setDefaultLine();
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(30, 5, ajustarCaracteres("Fecha de la cirugía:"), "TL", 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(68, 5, ajustarCaracteres($cirugia_obj["fecha_cx_t"]), "TR", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(27, 5, ajustarCaracteres("Tipo de atención:"), "T", 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(70, 5, ajustarCaracteres($cirugia_obj["amb_rea"]), "TR", 1, 'L');
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, ajustarCaracteres("Finalidad:"), "L", 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(82, 5, ajustarCaracteres($cirugia_obj["fin_pro"]), "R", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(11, 5, ajustarCaracteres("Opera:"), 0, 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(86, 5, ajustarCaracteres($cirugia_obj["nombre_usuario"]." ".$cirugia_obj["apellido_usuario"]), "R", 1, 'L');
							
							$reoperacion_aux = "";
							if ($cirugia_obj["ind_reoperacion"] == "1") {
								$reoperacion_aux = "Si";
							} else if ($cirugia_obj["ind_reoperacion"] == "0") {
								$reoperacion_aux = "No";
							}
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(25, 5, ajustarCaracteres("Es reoperación:"), "L", 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(73, 5, ajustarCaracteres($reoperacion_aux), "R", 0, 'L');
							
							if ($cirugia_obj["ind_reoperacion"] == "1") {
								$reop_ent_aux = "";
								if ($cirugia_obj["ind_reop_ent"] == "1") {
									$reop_ent_aux = "Si";
								} else if ($cirugia_obj["ind_reop_ent"] == "0") {
									$reop_ent_aux = "No";
								}
								
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(25, 5, ajustarCaracteres("Del consultorio:"), 0, 0, 'L');
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(72, 5, ajustarCaracteres($reop_ent_aux), "R", 1, 'L');
								
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(35, 5, ajustarCaracteres("Fecha cirugía anterior:"), "L", 0, 'L');
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(63, 5, ajustarCaracteres($cirugia_obj["fecha_cx_ant_t"]), "R", 0, 'L');
								$pdf->Cell(97, 5, "", "R", 1, 'L');
							} else {
								$pdf->Cell(97, 5, "", "R", 1, 'L');
							}
							$y_aux = $pdf->GetY();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->Ln(2);
							
							$pdf->SetFont("Arial", "B", 10);
							$pdf->Cell(195, 5, ajustarCaracteres("Procedimientos quirúrgicos"), "TLR", 1, 'C');
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(15, 5, ajustarCaracteres("Código"), "L", 0, 'L');
							$pdf->Cell(150, 5, ajustarCaracteres("Procedimiento"), 0, 0, 'L');
							$pdf->Cell(15, 5, ajustarCaracteres("Ojo"), 0, 0, 'C');
							$pdf->Cell(15, 5, ajustarCaracteres("Vía"), "R", 1, 'C');
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Se carga el listado de procedimientos
							$lista_procedimientos = $db_cirugias->get_lista_cirugias_procedimientos($id_hc);
							
							if (count($lista_procedimientos) > 0) {
								foreach ($lista_procedimientos as $proc_aux) {
									$pdf->SetFont("Arial", "", 9);
									$pdf->Cell(15, 5, ajustarCaracteres($proc_aux["cod_procedimiento"]), "L", 0, 'L');
									$pdf->Cell(150, 5, ajustarCaracteres($proc_aux["nombre_procedimiento"]), 0, 0, 'L');
									$pdf->Cell(15, 5, ajustarCaracteres($proc_aux["ojo"]), 0, 0, 'C');
									$pdf->Cell(15, 5, ajustarCaracteres($proc_aux["via_procedimiento"]), "R", 1, 'C');
								}
							} else {
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(195, 5, ajustarCaracteres("(Sin procedimientos quirúrgicos)"), "LR", 1, 'C');
							}
							$y_aux = $pdf->GetY();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->Ln(2);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 5, ajustarCaracteres("Observaciones"), 0, 1, 'L');
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($cirugia_obj["observaciones_cx"]) != "" ? cambiar_enter_br($cirugia_obj["observaciones_cx"]) : "-");
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(5);
							
							$pdf->SetFont("Arial", "B", 10);
							$pdf->Cell(195, 5, ajustarCaracteres("Láser quirúrgico"), "TLR", 1, 'C');
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(22, 5, ajustarCaracteres("Tipo de láser:"), "BL", 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(76, 5, ajustarCaracteres($cirugia_obj["tipo_laser"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(21, 5, ajustarCaracteres("Ojo a operar:"), "B", 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(76, 5, ajustarCaracteres($cirugia_obj["ojo"]), "BR", 1, 'L');
							$pdf->Ln(2);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(80, 5, "OD", "TBL", 0, 'C');
							$pdf->Cell(35, 5, "", "TB", 0, 'C');
							$pdf->Cell(80, 5, "OI", "TBR", 0, 'C');
							$pdf->Ln();
							
							//Técnica
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["tecnica_od"] != "" ? $cirugia_obj["tecnica_od"] : "-"), "L", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("Técnica"), 0, 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["tecnica_oi"] != "" ? $cirugia_obj["tecnica_oi"] : "-"), "R", 1, 'C');
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Microquerato
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["microquerato_od"] != "" ? $cirugia_obj["microquerato_od"] : "-"), "L", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("Microquerato"), 0, 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["microquerato_oi"] != "" ? $cirugia_obj["microquerato_oi"] : "-"), "R", 1, 'C');
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Placas
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["num_placas_od"] != "" ? $cirugia_obj["num_placas_od"] : "-"), "L", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("Placas"), 0, 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["num_placas_oi"] != "" ? $cirugia_obj["num_placas_oi"] : "-"), "R", 1, 'C');
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Tiempo vacío
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["tiempo_vacio_od"] != "" ? $cirugia_obj["tiempo_vacio_od"] : "-"), "L", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("Tiempo vacío"), 0, 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["tiempo_vacio_oi"] != "" ? $cirugia_obj["tiempo_vacio_oi"] : "-"), "R", 1, 'C');
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Uso de cuchilla
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["uso_cuchilla_od"] != "" ? $cirugia_obj["uso_cuchilla_od"] : "-"), "L", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("Uso de cuchilla"), 0, 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["uso_cuchilla_oi"] != "" ? $cirugia_obj["uso_cuchilla_oi"] : "-"), "R", 1, 'C');
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Bisagra
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["bisagra_od"] != "" ? $cirugia_obj["bisagra_od"] : "-"), "L", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("Bisagra"), 0, 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["bisagra_oi"] != "" ? $cirugia_obj["bisagra_oi"] : "-"), "R", 1, 'C');
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Tiempo quirúrgico
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["tiempo_qx_od"] != "" ? $cirugia_obj["tiempo_qx_od"] : "-"), "BL", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("Tiempo quirúrgico*"), "B", 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["tiempo_qx_oi"] != "" ? $cirugia_obj["tiempo_qx_oi"] : "-"), "BR", 1, 'C');
							$pdf->Cell(195, 5, ajustarCaracteres("*Tiempo preláser (tiempo entre el levantamiento del disco y la aplicación del láser)"), 0, 1, 'L');
							$pdf->Ln(2);
							
							//Datos de la operación
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(80, 5, "OD", "TL", 0, 'C');
							$pdf->SetFont("Arial", "B", 10);
							$pdf->Cell(35, 5, ajustarCaracteres("Datos de la operación"), "T", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(80, 5, "OI", "TR", 1, 'C');
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Tipo
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["tipo_od"] != "" ? $cirugia_obj["tipo_od"] : "-"), "L", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("Tipo"), 0, 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["tipo_oi"] != "" ? $cirugia_obj["tipo_oi"] : "-"), "R", 1, 'C');
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Esfera - Cilindro - Eje
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["esfera_od"]." / ".$cirugia_obj["cilindro_od"]." / ".$cirugia_obj["eje_od"]), "L", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("Esfera / Cilindro / Eje"), 0, 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["esfera_oi"]." / ".$cirugia_obj["cilindro_oi"]." / ".$cirugia_obj["eje_oi"]), "R", 1, 'C');
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Zona óptica
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["zona_optica_od"] != "" ? $cirugia_obj["zona_optica_od"] : "-"), "L", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("Zona óptica"), 0, 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["zona_optica_oi"] != "" ? $cirugia_obj["zona_optica_oi"] : "-"), "R", 1, 'C');
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Ablación
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["ablacion_od"] != "" ? $cirugia_obj["ablacion_od"] : "-"), "L", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("Ablación"), 0, 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["ablacion_oi"] != "" ? $cirugia_obj["ablacion_oi"] : "-"), "R", 1, 'C');
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Espesor corneal base
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["esp_corneal_base_od"] != "" ? $cirugia_obj["esp_corneal_base_od"] : "-"), "L", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("Espesor corneal base"), 0, 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["esp_corneal_base_oi"] != "" ? $cirugia_obj["esp_corneal_base_oi"] : "-"), "R", 1, 'C');
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Humedad
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["humedad_od"] != "" ? $cirugia_obj["humedad_od"] : "-"), "L", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("Humedad"), 0, 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["humedad_oi"] != "" ? $cirugia_obj["humedad_oi"] : "-"), "R", 1, 'C');
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Temperatura
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["temperatura_od"] != "" ? $cirugia_obj["temperatura_od"] : "-"), "L", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("Temperatura"), 0, 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["temperatura_oi"] != "" ? $cirugia_obj["temperatura_oi"] : "-"), "R", 1, 'C');
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//White to white
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["wtw_od"] != "" ? $cirugia_obj["wtw_od"] : "-"), "BL", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("White to white"), "B", 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, ajustarCaracteres($cirugia_obj["wtw_oi"] != "" ? $cirugia_obj["wtw_oi"] : "-"), "BR", 1, 'C');
							
							if (trim($cirugia_obj["anotaciones_ev"]) != "") {
								$pdf->Ln(2);
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(195, 5, ajustarCaracteres("Evaluación"), 0, 1, 'L');
								$pdf->SetFont("Arial", "", 9);
								$texto_aux = ajustarCaracteres($cirugia_obj["anotaciones_ev"]);
								$pdfHTML->WriteHTML($texto_aux, $pdf);
								$pdf->Ln(3);
							}
							
							$pdf->Ln(2);
							imprimir_diagnosticos($id_hc, $pdf, true);
						}
						break;
						
					case "18": //ARCHIVOS ADJUNTOS
						require_once("../db/DbHistoriaClinica.php");
						$db_historia_clinica = new DbHistoriaClinica();
						imprimir_encabezado($historia_clinica_obj, $pdf);
						
						$historia_clinica_obj = $db_historia_clinica->getHistoriaClinicaId($id_hc);
						
						if (count($historia_clinica_obj) > 0) {
							$usuario_prof_obj = $db_usuarios->getUsuario($historia_clinica_obj["id_usuario_crea"]);
							
							//Se define ancho y color por defecto de las líneas
							$pdf->setDefaultLine();
							
							$pdf->Ln(1);
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(12, 5, ajustarCaracteres("Anexo:"), 0, 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(183, 5, ajustarCaracteres($historia_clinica_obj["nombre_alt_tipo_reg"]), 0, 1, 'L');
							
							$observaciones_hc = trim($historia_clinica_obj["observaciones_hc"]);
							if ($observaciones_hc != "") {
								$pdf->SetFont("Arial", "", 9);
								$pdfHTML->WriteHTML(ajustarCaracteres("<b>Observaciones: </b>".$observaciones_hc), $pdf);
							}
							
							$pdf->Ln(3);
							
							//Se obtiene el listado de archivos adjuntos
							$lista_arch_adjuntos = $db_historia_clinica->getListaHCArchivosAdjuntos($id_hc);
							
							if (count($lista_arch_adjuntos) > 0) {
								foreach ($lista_arch_adjuntos as $arch_adjunto_aux) {
									$ruta_arch_adjunto = trim($arch_adjunto_aux["ruta_archivo"]);
									if ($ruta_arch_adjunto != "") {
										$ruta_arch_adjunto = str_replace("../imagenes/imagenes_hce", $ruta_base, $ruta_arch_adjunto);
									}
									
									//Se verifica si hay algún archivo que mostrar
									if ($ruta_arch_adjunto != "" && file_exists($ruta_arch_adjunto)) {
										//Se obtiene el tipo de archivo
										$extension = $utilidades->get_extension_arch($ruta_arch_adjunto);
										
										switch ($extension) {
											case "jpg":
											case "png":
											case "bmp":
											case "gif":
												//Se obtienen las dimensiones del archivo
												$arr_prop_imagen = getimagesize($ruta_arch_adjunto);
												$ancho_aux = floatval($arr_prop_imagen[0]);
												$alto_aux = floatval($arr_prop_imagen[1]);
												
												$ancho_max = 195.0;
												$y_aux = $pdf->GetY();
												$alto_max = 266 - $y_aux;
												
												if ($alto_max < 120 || ($alto_max < 160 && $ancho_aux < $alto_aux)) {
													$pdf->AddPage();
													$y_aux = $pdf->GetY();
													$alto_max = 266 - $y_aux;
												}
												if ($ancho_aux > $ancho_max) {
													$alto_aux = floor($alto_aux * ($ancho_max / $ancho_aux));
													$ancho_aux = floor($ancho_aux * ($ancho_max / $ancho_aux));
												}
												if ($alto_aux > $alto_max) {
													$ancho_aux = floor($ancho_aux * ($alto_max / $alto_aux));
													$alto_aux = floor($alto_aux * ($alto_max / $alto_aux));
												}
												
												$pdf->Image($ruta_arch_adjunto, 10 + (195 - $ancho_aux) / 2, $y_aux, $ancho_aux, $alto_aux);
												$pdf->Cell(195, $alto_aux, "", 0, 1);
												break;
												
											case "pdf":
												//Se convierten las páginas del pdf a jpg
												$prefijo_aux = __DIR__."\\tmp\\"."img_adjunto_".$id_usuario."_".$id_hc."_";
												$comando_aux = "\"".$gs."\" -dNOPAUSE -sDEVICE=jpeg -dUseCIEColor -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -r150x150 -dJPEGQ=90 -sOutputFile=\"".$prefijo_aux."%d.jpg\" \"".$ruta_arch_adjunto."\" -c quit";
												$salida_aux = array();
												exec($comando_aux, $salida_aux, $resultado_aux);
												
												if ($resultado_aux == "0") {
													$cont_aux = 1;
													$ruta_imagen_tmp = $prefijo_aux.$cont_aux.".jpg";
													while (file_exists($ruta_imagen_tmp)) {
														//Se obtienen las dimensiones del archivo
														$arr_prop_imagen = getimagesize($ruta_imagen_tmp);
														$ancho_aux = floatval($arr_prop_imagen[0]);
														$alto_aux = floatval($arr_prop_imagen[1]);
														
														$ancho_max = 195.0;
														$y_aux = $pdf->GetY();
														$alto_max = 266 - $y_aux;
														
														if ($alto_max < 120 || ($alto_max < 160 && $ancho_aux < $alto_aux)) {
															$pdf->AddPage();
															$y_aux = $pdf->GetY();
															$alto_max = 266 - $y_aux;
														}
														if ($ancho_aux > $ancho_max) {
															$alto_aux = floor($alto_aux * ($ancho_max / $ancho_aux));
															$ancho_aux = floor($ancho_aux * ($ancho_max / $ancho_aux));
														}
														if ($alto_aux > $alto_max) {
															$ancho_aux = floor($ancho_aux * ($alto_max / $alto_aux));
															$alto_aux = floor($alto_aux * ($alto_max / $alto_aux));
														}
														
														$pdf->Image($ruta_imagen_tmp, 10 + (195 - $ancho_aux) / 2, $y_aux, $ancho_aux, $alto_aux);
														$pdf->Cell(195, $alto_aux, "", 0, 1);
														$pdf->Ln(2);
														
														unlink($ruta_imagen_tmp);
														
														$cont_aux++;
														$ruta_imagen_tmp = $prefijo_aux.$cont_aux.".jpg";
													}
												}
												break;
												
											default:
												break;
										}
									}
								}
							}
						}
						break;
						
					case "19": //CONSULTA DE CONTROL DE OPTOMETRIA
						require_once("../db/DbConsultaControlOptometria.php");
						$db_consulta_control_optometria = new DbConsultaControlOptometria();
						
						imprimir_encabezado($historia_clinica_obj, $pdf);
						
						$consulta_control_optometria_obj = $db_consulta_control_optometria->getConsultaControlOptometria($id_hc);
						if (count($consulta_control_optometria_obj) > 0) {
							$usuario_prof_obj = $db_usuarios->getUsuario($consulta_control_optometria_obj["id_usuario_crea"]);
							
							//Se obtiene el listado de agudeza visual
							$lista_ag_visual = $db_listas->getListaDetalles(11);
							$mapa_ag_visual = array();
							foreach ($lista_ag_visual as $ag_visual_aux) {
								$mapa_ag_visual[$ag_visual_aux["id_detalle"]] = $ag_visual_aux["nombre_detalle"];
							}
							
							//Se define ancho y color por defecto de las líneas
							$pdf->setDefaultLine();
							
							$pdf->SetFont("Arial", "B", 9);
							
							$pdf->Ln(5);
							
							$nombre_usuario_alt = "";
							if ($usuario_prof_obj["ind_anonimo"] == "1" && $historia_clinica_obj["nombre_usuario_alt"] != "") {
								$nombre_usuario_alt = $historia_clinica_obj["nombre_usuario_alt"];
							} else {
								$nombre_usuario_alt = $usuario_prof_obj["nombre_usuario"]." ".$usuario_prof_obj["apellido_usuario"];
							}
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 4, ajustarCaracteres("Lugar de la cita: "), 0, 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(50, 4, $consulta_control_optometria_obj["lugar_cita"], 0, 0, "R");
							$pdf->Ln(4);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 4, ajustarCaracteres("Atiende: ".$nombre_usuario_alt), 0, 2, 'L');
							
							if ($usuario_prof_obj["id_usuario_firma"] != "") {
								//Se buscan los datos del usuario que firma
								$usuario_firma_obj = $db_usuarios->getUsuario($usuario_prof_obj["id_usuario_firma"]);
								$pdf->Cell(195, 4, ajustarCaracteres("Instructor: ".$usuario_firma_obj["nombre_usuario"]." ".$usuario_firma_obj["apellido_usuario"]), 0, 2, 'L');
							}
							$pdf->SetFont("Arial", "B", 9);
							//$pdf->MultiCell(195, 4, ajustarCaracteres("Motivo de Consulta: ".(trim($admision_obj["motivo_consulta"]) != "" ? $admision_obj["motivo_consulta"] : "-")), 0, 'L');
							//$pdf->Ln(1);
							
							$texto_anamnesis = ajustarCaracteres($consulta_control_optometria_obj["anamnesis"]);
							$pdfHTML->WriteHTML("<b>Anamnesis: </b>".$texto_anamnesis, $pdf);
							$pdf->Ln(3);
							
							//Ojo
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(8, 5, "Ojo:", 0, 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(20, 5, $consulta_control_optometria_obj["ojo"], 0, 1, 'L');
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(80, 5, "OD", "TBL", 0, 'C');
							$pdf->Cell(35, 5, "", "TB", 0, 'C');
							$pdf->Cell(80, 5, "OI", "TBR", 0, 'C');
							$pdf->Ln();
							
							//AVSC
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(20, 5, "Lejos:", "BL", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(20, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_control_optometria_obj["avsc_lejos_od"]]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(20, 5, "Cerca:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(20, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_control_optometria_obj["avsc_cerca_od"]]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, "AVSC", "B", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(20, 5, "Lejos:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(20, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_control_optometria_obj["avsc_lejos_oi"]]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(20, 5, "Cerca:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(20, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_control_optometria_obj["avsc_cerca_oi"]]), "BR", 0, 'L');
							$pdf->Ln();
							
							//Lensometría
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, $consulta_control_optometria_obj["lenso_esfera_od"]." / ".$consulta_control_optometria_obj["lenso_cilindro_od"]." / ".$consulta_control_optometria_obj["lenso_eje_od"], "L", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 10, ajustarCaracteres("Lensometría"), "B", 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, $consulta_control_optometria_obj["lenso_esfera_oi"]." / ".$consulta_control_optometria_obj["lenso_cilindro_oi"]." / ".$consulta_control_optometria_obj["lenso_eje_oi"], "R", 0, 'C');
							$pdf->Ln();
							$x_aux = $pdf->GetX();
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line($x_aux, $y_aux, $x_aux + 80, $y_aux);
							$pdf->Line($x_aux + 115, $y_aux, $x_aux + 195, $y_aux);
							$pdf->setDefaultLine();
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, ajustarCaracteres("Adición:"), "BL", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion($consulta_control_optometria_obj["lenso_adicion_od"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Lejos:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_control_optometria_obj["lenso_lejos_od"]]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Cerca:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(12, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_control_optometria_obj["lenso_cerca_od"]]), "B", 0, 'L');
							$pdf->Cell(35, 10, "", "", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, ajustarCaracteres("Adición:"), "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion($consulta_control_optometria_obj["lenso_adicion_oi"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Lejos:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_control_optometria_obj["lenso_lejos_oi"]]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Cerca:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(12, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_control_optometria_obj["lenso_cerca_oi"]]), "BR", 0, 'L');
							$pdf->Ln();
							
							//Queratometría
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Cilindro:", "BL", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion($consulta_control_optometria_obj["querato_dif_od"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Eje:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion($consulta_control_optometria_obj["querato_ejek1_od"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "K+Plano:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(12, 5, remplazar_vacio_guion($consulta_control_optometria_obj["querato_k1_od"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 5, ajustarCaracteres("Queratometría"), "B", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Cilindro:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion($consulta_control_optometria_obj["querato_dif_oi"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Eje:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion($consulta_control_optometria_obj["querato_ejek1_oi"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "K+Plano:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(12, 5, remplazar_vacio_guion($consulta_control_optometria_obj["querato_k1_oi"]), "BR", 0, 'L');
							$pdf->Ln();
							
							//Subjetivo
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, $consulta_control_optometria_obj["subjetivo_esfera_od"]." / ".$consulta_control_optometria_obj["subjetivo_cilindro_od"]." / ".$consulta_control_optometria_obj["subjetivo_eje_od"], "L", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(35, 10, ajustarCaracteres("Subjetivo"), "B", 0, 'C');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(80, 5, $consulta_control_optometria_obj["subjetivo_esfera_oi"]." / ".$consulta_control_optometria_obj["subjetivo_cilindro_oi"]." / ".$consulta_control_optometria_obj["subjetivo_eje_oi"], "R", 0, 'C');
							$pdf->Ln();
							$x_aux = $pdf->GetX();
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line($x_aux, $y_aux, $x_aux + 80, $y_aux);
							$pdf->Line($x_aux + 115, $y_aux, $x_aux + 195, $y_aux);
							$pdf->setDefaultLine();
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, ajustarCaracteres("Adición:"), "BL", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion($consulta_control_optometria_obj["subjetivo_adicion_od"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Lejos:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_control_optometria_obj["subjetivo_lejos_od"]]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Cerca:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(12, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_control_optometria_obj["subjetivo_cerca_od"]]), "B", 0, 'L');
							$pdf->Cell(35, 10, "", "", 0, 'C');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, ajustarCaracteres("Adición:"), "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion($consulta_control_optometria_obj["subjetivo_adicion_oi"]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Lejos:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(10, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_control_optometria_obj["subjetivo_lejos_oi"]]), "B", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, "Cerca:", "B", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(12, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_control_optometria_obj["subjetivo_cerca_oi"]]), "BR", 0, 'L');
							$pdf->Ln();
							
							//Cicloplejia
							if ($consulta_control_optometria_obj["cicloplejio_esfera_od"] != "" || $consulta_control_optometria_obj["cicloplejio_cilindro_od"] != "" ||
									$consulta_control_optometria_obj["cicloplejio_eje_od"] != "" || $consulta_control_optometria_obj["cicloplejio_esfera_oi"] != "" ||
									$consulta_control_optometria_obj["cicloplejio_cilindro_oi"] != "" || $consulta_control_optometria_obj["cicloplejio_eje_oi"] != "" ||
									$consulta_control_optometria_obj["cicloplejio_lejos_od"] != "0" || $consulta_control_optometria_obj["cicloplejio_lejos_oi"] != "0") {
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(52, 5, $consulta_control_optometria_obj["cicloplejio_esfera_od"]." / ".$consulta_control_optometria_obj["cicloplejio_cilindro_od"]." / ".$consulta_control_optometria_obj["cicloplejio_eje_od"], "BL", 0, 'C');
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(16, 5, "Lejos:", "B", 0, 'R');
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(12, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_control_optometria_obj["cicloplejio_lejos_od"]]), "B", 0, 'L');
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(35, 5, ajustarCaracteres("Cicloplejia"), "B", 0, 'C');
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(52, 5, $consulta_control_optometria_obj["cicloplejio_esfera_oi"]." / ".$consulta_control_optometria_obj["cicloplejio_cilindro_oi"]." / ".$consulta_control_optometria_obj["cicloplejio_eje_oi"], "B", 0, 'C');
								$pdf->SetFont("Arial", "B", 9);
								$pdf->Cell(16, 5, "Lejos:", "B", 0, 'R');
								$pdf->SetFont("Arial", "", 9);
								$pdf->Cell(12, 5, remplazar_vacio_guion(@$mapa_ag_visual[$consulta_control_optometria_obj["cicloplejio_lejos_oi"]]), "BR", 0, 'L');
								$pdf->Ln();
							}
							
							//Diagnósticos
							$pdf->Ln(2);
							imprimir_diagnosticos($id_hc, $pdf, true);
							$pdf->Ln(1);
							
							//Diagnosticos generales del lente
							$pdf->Ln(2);
							$x_aux = $pdf->GetX();
							$y_aux = $pdf->GetY();
						
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, ajustarCaracteres("Tipo de lente seleccionado:"), "TBL", 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(50, 5, $consulta_control_optometria_obj["slct_lente"] !="" ? $consulta_control_optometria_obj["slct_lente"] : "-", "TB", 0, 'R');
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(104, 5, ajustarCaracteres("Tipo filtro seleccionado:"), "TB", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(25, 5, $consulta_control_optometria_obj["slct_filtro"]  != "" ? $consulta_control_optometria_obj["slct_filtro"] : "-", "TR", 0, 'R');
							$pdf->Ln();
							
							$x_aux = $pdf->GetX();
							$y_aux = $pdf->GetY();
							$pdf->Line($x_aux, $y_aux, $x_aux + 195, $y_aux);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, ajustarCaracteres("Tiempo de vigencia de la formulación:"), 'TBL', 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(58, 5, $consulta_control_optometria_obj["tiempo_vigencia"] !="" ? $consulta_control_optometria_obj["tiempo_vigencia"] : "-", "TB", 0, 'R');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(99.5, 5, ajustarCaracteres("Periodo de la formulación:"), "TB", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(21.5, 5, $consulta_control_optometria_obj["periodo"]  != "" ? $consulta_control_optometria_obj["periodo"] : "-", "TR", 0, 'R');
							$pdf->Ln();
							
							$x_aux = $pdf->GetX();
							$y_aux = $pdf->GetY();
							$pdf->Line($x_aux, $y_aux, $x_aux + 195, $y_aux);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(16, 5, ajustarCaracteres("Cantidad prescrita por el especialista:"), 'TBL', 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(49, 5, $consulta_control_optometria_obj["form_cantidad"] !="" ? $consulta_control_optometria_obj["form_cantidad"] : "-", "TB", 0, 'R');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(95, 5, ajustarCaracteres("Distancia pupilar:"), "TB", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(35, 5, $consulta_control_optometria_obj["distancia_pupilar"]  != "" ? $consulta_control_optometria_obj["distancia_pupilar"] : "-", "TR", 0, 'R');
							$pdf->Ln();
							
							$x_aux = $pdf->GetX();
							$y_aux = $pdf->GetY();
							$pdf->Line($x_aux, $y_aux, $x_aux + 195, $y_aux);
							
							$pdf->SetFont("Arial", "B",  9.5);
							$pdf->Cell(195, 5, ajustarCaracteres("Especificaciones del lente: "), 'LR', 1, 'L');
							
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres($consulta_control_optometria_obj["tipo_lente"]  != "" ? $consulta_control_optometria_obj["tipo_lente"] : "-");
							$pdf->Cell(195, 5, $texto_aux, 'LR', 1, 'L');
							/*$pdfHTML->WriteHTML($texto_aux, $pdf);*/
							
				
							$x_aux = $pdf->GetX();
							$y_aux = $pdf->GetY();
							$pdf->Line($x_aux, $y_aux, $x_aux + 195, $y_aux);
							
							//Observaciones
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(45, 5, ajustarCaracteres("Otros Diagnósticos y Análisis"), 0, 1, 'L');
							$pdf->Ln(1);
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_control_optometria_obj["diagnostico_optometria"]) != "" ? $consulta_control_optometria_obj["diagnostico_optometria"] : "-");
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(5);
							
							imprimir_firma($consulta_control_optometria_obj["id_usuario_crea"], $pdf, $nombre_usuario_alt);
						}
						break;
					
					case "17": //HISTORIA CLÍNICA FÍSICA
						break;
					
					case "56": //CONSULTA DERMATOLÓGICA
						require_once("../db/DbConsultaDermatologia.php");
						$db_consulta_dermatologia = new DbConsultaDermatologia();
						
						imprimir_encabezado($historia_clinica_obj, $pdf);
						
						$consulta_dermatologia_obj = $db_consulta_dermatologia->getConsultaDermatologia($id_hc);
						if (count($consulta_dermatologia_obj) > 0) {
							$usuario_prof_obj = $db_usuarios->getUsuario($consulta_dermatologia_obj["id_usuario_crea"]);
							
							//Se define ancho y color por defecto de las líneas
							$pdf->setDefaultLine();
							
							$pdf->SetFont("Arial", "B", 9);
							
							$pdf->Ln(5);
							
							$nombre_usuario_alt = "";
							if ($usuario_prof_obj["ind_anonimo"] == "1" && $historia_clinica_obj["nombre_usuario_alt"] != "") {
								$nombre_usuario_alt = $historia_clinica_obj["nombre_usuario_alt"];
							} else {
								$nombre_usuario_alt = $usuario_prof_obj["nombre_usuario"]." ".$usuario_prof_obj["apellido_usuario"];
							}
							
							$pdf->Cell(195, 4, ajustarCaracteres("Atiende: ".$nombre_usuario_alt), 0, 2, 'L');						
							
							if ($usuario_prof_obj["id_usuario_firma"] != "") {
								//Se buscan los datos del usuario que firma
								$usuario_firma_obj = $db_usuarios->getUsuario($usuario_prof_obj["id_usuario_firma"]);
								$pdf->Cell(195, 4, ajustarCaracteres("Instructor: ".$usuario_firma_obj["nombre_usuario"]." ".$usuario_firma_obj["apellido_usuario"]), 0, 2, 'L');
							}
							
							//$pdf->MultiCell(195, 4, ajustarCaracteres("Motivo de Consulta: ".(trim($admision_obj["motivo_consulta"]) != "" ? $admision_obj["motivo_consulta"] : "-")), 0, 'L');
							$pdf->MultiCell(195, 4, ajustarCaracteres("Consulta Dermatológica"), 0, 'L');
							
							//Se imprimen los antecedentes
							imprimir_antecedentes($id_hc, $consulta_dermatologia_obj["desc_antecedentes_medicos"], $pdf, $pdfHTML);
							$pdf->Ln(2);
							
							//Examen físico
							$pdf->SetFont("Arial", "B", 10);
							$pdf->Cell(195, 6, ajustarCaracteres("Examen Físico"), "TLR", 1, 'C');
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(28, 5, "Peso (Kg):", "L", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(18, 5, $consulta_dermatologia_obj["peso"], "", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(28, 5, "Talla (cm):", "", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(18, 5, $consulta_dermatologia_obj["talla"], "", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(28, 5, "IMC:", "", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(18, 5, $consulta_dermatologia_obj["imc"], "", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(38, 5, "Escala Ludwig:", "", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(19, 5, $consulta_dermatologia_obj["ludwig"], "R", 0, 'L');
							$pdf->Ln();
							
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 7, "Escala Ferriman y Gallway", "LR", 0, 'C');
							$pdf->Ln();
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(32, 5, "Labio superior:", "L", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(7, 5, $consulta_dermatologia_obj["fg_labio_superior"] != "" ? $consulta_dermatologia_obj["fg_labio_superior"] : "-", "", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(32, 5, "Mejilla:", "", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(7, 5, $consulta_dermatologia_obj["fg_mejilla"] != "" ? $consulta_dermatologia_obj["fg_mejilla"] : "-", "", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(32, 5, ajustarCaracteres("Tórax:"), "", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(7, 5, $consulta_dermatologia_obj["fg_torax"] != "" ? $consulta_dermatologia_obj["fg_torax"] : "-", "", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(32, 5, "Espalda superior:", "", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(7, 5, $consulta_dermatologia_obj["fg_espalda_superior"] != "" ? $consulta_dermatologia_obj["fg_espalda_superior"] : "-", "", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(32, 5, "Espalda inferior:", "", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(7, 5, $consulta_dermatologia_obj["fg_espalda_inferior"] != "" ? $consulta_dermatologia_obj["fg_espalda_inferior"] : "-", "R", 0, 'L');
							$pdf->Ln();
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(32, 5, "Abdomen superior:", "L", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(7, 5, $consulta_dermatologia_obj["fg_abdomen_superior"] != "" ? $consulta_dermatologia_obj["fg_abdomen_superior"] : "-", "", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(32, 5, "Abdomen inferior:", "", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(7, 5, $consulta_dermatologia_obj["fg_abdomen_inferior"] != "" ? $consulta_dermatologia_obj["fg_abdomen_inferior"] : "-", "", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(32, 5, "Brazo:", "", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(7, 5, $consulta_dermatologia_obj["fg_brazo"] != "" ? $consulta_dermatologia_obj["fg_brazo"] : "-", "", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(32, 5, "Muslo:", "", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(7, 5, $consulta_dermatologia_obj["fg_muslo"] != "" ? $consulta_dermatologia_obj["fg_muslo"] : "-", "", 0, 'L');
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(32, 5, "Total:", "TL", 0, 'R');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(7, 5, $consulta_dermatologia_obj["fg_total"] != "" ? $consulta_dermatologia_obj["fg_total"] : "-", "TR", 0, 'L');
							$pdf->Ln();
							
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Cara
							$y_aux = $pdf->GetY();
							$texto_aux = ajustarCaracteres("<b>Cara: </b>".($consulta_dermatologia_obj["descripcion_cara"] != "" ? $consulta_dermatologia_obj["descripcion_cara"] : "<p>-</p>"));
							$pdf->Ln(2);
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(3);
							
							$y_aux2 = $pdf->GetY();
							$pdf->SetY($y_aux);
							$pdf->Cell(195, ($y_aux2 - $y_aux), "", "LR", 1, 'C');
							
							$y_aux = $pdf->GetY();
							$pdf->setSeparationLine();
							$pdf->Line(10, $y_aux, 205, $y_aux);
							$pdf->setDefaultLine();
							
							//Cuerpo
							$y_aux = $pdf->GetY();
							$texto_aux = ajustarCaracteres("<b>Cuerpo: </b>".($consulta_dermatologia_obj["descripcion_cuerpo"] != "" ? $consulta_dermatologia_obj["descripcion_cuerpo"] : "<p>-</p>"));
							$pdf->Ln(2);
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(3);
							
							$y_aux2 = $pdf->GetY();
							$pdf->SetY($y_aux);
							$pdf->Cell(195, ($y_aux2 - $y_aux), "", "BLR", 1, 'C');
							
							$pdf->Ln(3);
							
							/*$pdf->SetFont("Arial", "B", 10);
							$pdf->Cell(195, 4, ajustarCaracteres("Evolución"), 0, 1, 'C');
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_evolucion_obj["texto_evolucion"]) != "" ? cambiar_enter_br($consulta_evolucion_obj["texto_evolucion"]) : "-");
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(3);
							
							//Se agregan los datos de las tonometrìas
							imprimir_tonometrias($id_hc, $historia_clinica_obj["id_paciente"], $historia_clinica_obj["id_admision"], $pdf);*/
							
							$pdf->Ln(2);
							imprimir_diagnosticos($id_hc, $pdf, false);
							$pdf->Ln(1);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 5, ajustarCaracteres("Otros diagnósticos y análisis"), 0, 1, 'L');
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_dermatologia_obj["diagnostico_dermat"]));
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(5);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 5, ajustarCaracteres("Solicitud de procedimientos y exámenes complementarios"), 0, 1, 'L');
							imprimir_hc_procedimientos_solic($id_hc, $pdf, false);
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_dermatologia_obj["solicitud_examenes"]) != "" ? cambiar_enter_br($consulta_dermatologia_obj["solicitud_examenes"]) : "-");
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(3);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 5, ajustarCaracteres("Recomendaciones clínicas, médicas y quirúrgicas"), 0, 1, 'L');
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_dermatologia_obj["tratamiento_dermat"]) != "" ? cambiar_enter_br($consulta_dermatologia_obj["tratamiento_dermat"]) : "-");
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(3);
							
							imprimir_formulacion_hc($id_hc, $pdf);
							
							imprimir_firma($consulta_dermatologia_obj["id_usuario_crea"], $pdf, $nombre_usuario_alt);
						}
						break;
						
					case "3": //CONSULTA DE CONTROL
					case "12": //CONSULTA PREQUIRÚRGICA
					case "13": //CAPSULOTOMÍA
					case "14": //IRIDOTOMÍA
					case "15": //JUNTA MÉDICA
					case "20": //FOTOCOAGULACIÓN
					default:
						require_once("../db/DbConsultaEvolucion.php");
						$db_consulta_evolucion = new DbConsultaEvolucion();
						
						imprimir_encabezado($historia_clinica_obj, $pdf);
						
						$consulta_evolucion_obj = $db_consulta_evolucion->get_consulta_evolucion($id_hc);
						if (count($consulta_evolucion_obj) > 0) {
							$usuario_prof_obj = $db_usuarios->getUsuario($consulta_evolucion_obj["id_usuario_crea"]);
							
							//Se obtiene el listado de agudeza visual
							$lista_ag_visual = $db_listas->getListaDetalles(11);
							$mapa_ag_visual = array();
							foreach ($lista_ag_visual as $ag_visual_aux) {
								$mapa_ag_visual[$ag_visual_aux["id_detalle"]] = $ag_visual_aux["nombre_detalle"];
							}
							
							//Se define ancho y color por defecto de las líneas
							$pdf->setDefaultLine();
							
							$pdf->SetFont("Arial", "B", 9);
							
							$pdf->Ln(5);
							
							$nombre_usuario_alt = "";
							if ($usuario_prof_obj["ind_anonimo"] == "1" && $historia_clinica_obj["nombre_usuario_alt"] != "") {
								$nombre_usuario_alt = $historia_clinica_obj["nombre_usuario_alt"];
							} else {
								$nombre_usuario_alt = $usuario_prof_obj["nombre_usuario"]." ".$usuario_prof_obj["apellido_usuario"];
							}
							
							$pdf->Cell(195, 4, ajustarCaracteres("Atiende: ".$nombre_usuario_alt), 0, 2, 'L');						
							
							if ($usuario_prof_obj["id_usuario_firma"] != "") {
								//Se buscan los datos del usuario que firma
								$usuario_firma_obj = $db_usuarios->getUsuario($usuario_prof_obj["id_usuario_firma"]);
								$pdf->Cell(195, 4, ajustarCaracteres("Instructor: ".$usuario_firma_obj["nombre_usuario"]." ".$usuario_firma_obj["apellido_usuario"]), 0, 2, 'L');
							}
							
							//$pdf->MultiCell(195, 4, ajustarCaracteres("Motivo de Consulta: ".(trim($admision_obj["motivo_consulta"]) != "" ? $admision_obj["motivo_consulta"] : "-")), 0, 'L');
							
							//Se imprimen los antecedentes
							imprimir_antecedentes($id_hc, $consulta_evolucion_obj["desc_antecedentes_medicos"], $pdf, $pdfHTML);
							$pdf->Ln(10);
							
							//Se agrega la información de los formatos extendidos
							imprimir_extension_consulta($tipo_registro_hc_obj, $id_hc, $pdf);
							
							$pdf->SetFont("Arial", "B", 10);
							$pdf->Cell(195, 4, ajustarCaracteres("Evolución"), 0, 1, 'C');
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_evolucion_obj["texto_evolucion"]) != "" ? cambiar_enter_br($consulta_evolucion_obj["texto_evolucion"]) : "-");
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(3);
							
							//Se agregan los datos de las tonometrìas
							imprimir_tonometrias($id_hc, $historia_clinica_obj["id_paciente"], $historia_clinica_obj["id_admision"], $pdf);
							
							$pdf->Ln(2);
							imprimir_diagnosticos($id_hc, $pdf, true);
							$pdf->Ln(1);
							
							$pdf->Ln(15);
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 15, ajustarCaracteres("Otros diagnósticos y análisis"), 0, 1, 'L');
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_evolucion_obj["diagnostico_evolucion"]));
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(5);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 5, ajustarCaracteres("Solicitud de procedimientos y exámenes complementarios"), 0, 1, 'L');
							imprimir_hc_procedimientos_solic($id_hc, $pdf, true);
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_evolucion_obj["solicitud_examenes_evolucion"]) != "" ? cambiar_enter_br($consulta_evolucion_obj["solicitud_examenes_evolucion"]) : "-");
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(3);
							
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(195, 5, ajustarCaracteres("Recomendaciones clínicas, médicas, optométricas y quirúrgicas"), 0, 1, 'L');
							$pdf->SetFont("Arial", "", 9);
							$texto_aux = ajustarCaracteres(trim($consulta_evolucion_obj["tratamiento_evolucion"]) != "" ? cambiar_enter_br($consulta_evolucion_obj["tratamiento_evolucion"]) : "-");
							$pdfHTML->WriteHTML($texto_aux, $pdf);
							$pdf->Ln(3);
							
							imprimir_formulacion_hc($id_hc, $pdf);
							
							imprimir_firma($consulta_evolucion_obj["id_usuario_crea"], $pdf, $nombre_usuario_alt);
						}
						break;
				}
			}
		}
		
		//Se guarda el documento pdf
		$nombreArchivo = "../tmp/historia_clinica_".$id_usuario.".pdf";
		$pdf->Output($nombreArchivo, "F");
		
?>
	<input type="hidden" id="hdd_ruta_arch_hc_pdf" value="<?php echo($nombreArchivo); ?>" />
 

<?php
	}
?>
