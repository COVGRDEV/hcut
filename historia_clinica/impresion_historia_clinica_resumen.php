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
	require_once("../db/DbCirugias.php");
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/FuncionesPersona.php");
	
	//Requires para FPDF
	require_once("../funciones/pdf/fpdf.php");
	require_once("../funciones/pdf/makefont/makefont.php");
	require_once("../funciones/pdf/funciones.php");
	require_once("../funciones/pdf/WriteHTML.php");
	require_once("../funciones/pdf/FPDI/fpdi.php");
	
    class PDF extends FPDI {
		public $nombre_completo;
		public $tipo_documento;
		public $numero_documento;
		public $fecha_nacimiento_t;
		public $sexo_t;
		public $angulo;
		
		function __construct($orientation, $unit, $size) {
			parent::__construct($orientation, $unit, $size);
		}
		
		function setData($nombre_completo, $tipo_documento, $numero_documento, $fecha_nacimiento_t, $sexo_t) {
			$this->nombre_completo = $nombre_completo;
			$this->tipo_documento = $tipo_documento;
			$this->numero_documento = $numero_documento;
			$this->fecha_nacimiento_t = $fecha_nacimiento_t;
			$this->sexo_t = $sexo_t;
		}
		
		function Header() {
			$funciones_persona = new FuncionesPersona();
			
			// Logo
			$this->Image('../imagenes/logo-color_v2.png', 10, 10, 55);
			
			$this->SetFont('Arial', 'B', 9);
			$this->SetY(10);
			
			// Texto de encabezado
			$this->Cell(65, 4);
			$this->Cell(130, 4, ajustarCaracteres($this->nombre_completo), 0, 0, 'L');
			$this->Ln();
			
			$this->Cell(65, 4);
			$this->Cell(65, 4, ajustarCaracteres($this->tipo_documento.': '.$this->numero_documento), 0, 0, 'L');
			$this->Cell(65, 4, ajustarCaracteres('Fecha de nacimiento: '.$funciones_persona->obtenerFecha6($this->fecha_nacimiento_t)), 0, 0, 'L');
			$this->Ln();
			
			$this->Cell(65, 4);
			$this->Cell(65, 4, ajustarCaracteres('Sexo: '.$this->sexo_t), 0, 0, 'L');
			$this->Ln(8);
		}
		
		// Pie de página
		function Footer() {
			// Posición a 1,5 cm del final
			$this->SetY(5);
			// Arial itálica 8
			$this->SetFont('Arial', 'I', 8);
			// Color del texto en gris
			$this->SetTextColor(128);
			// Número de página
			$this->Cell(10,20,'Página '.$this->PageNo(),1,0,'C');
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
	
	function imprimir_encabezado($id_paciente, $pdf) {
		$db_pacientes = new DbPacientes();
		$db_admision = new DbAdmision();
		$funciones_persona = new FuncionesPersona();
		$utilidades = new Utilidades();
		
		//Se obtienen los datos del paciente
		$paciente_obj = $db_pacientes->getExistepaciente3($id_paciente);
		
		//Se obtiene el nombre completo y la edad
		$nombre_completo = "";
		if (isset($paciente_obj["id_paciente"])) {
			$nombre_completo = $funciones_persona->obtenerNombreCompleto($paciente_obj["nombre_1"], $paciente_obj["nombre_2"], $paciente_obj["apellido_1"], $paciente_obj["apellido_2"]);
		}
		
		/* Encabezado FPDF */
		if (isset($pdf)) {
			$pdf->setData($utilidades->convertir_a_mayusculas($nombre_completo), $paciente_obj["tipodocumento"], $paciente_obj["numero_documento"], $paciente_obj["fecha_nacimiento_aux"], $paciente_obj["sexo_t"]);
			$pdf->AliasNbPages();
			$pdf->AddPage();
			
			$pdf->setXY(10, 30);
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
			
			//Se agrega el encabezado
			imprimir_encabezado($lista_historia_clinica[0]["id_paciente"], $pdf);
			
			foreach ($lista_historia_clinica as $historia_clinica_obj) {
				$id_hc = $historia_clinica_obj["id_hc"];
				$id_tipo_reg = $historia_clinica_obj["id_tipo_reg"];
				$nombre_tipo_reg = $historia_clinica_obj["nombre_tipo_reg"];
				$id_tipo_reg_base = $historia_clinica_obj["id_tipo_reg_base"];
				$id_usuario_reg = $historia_clinica_obj["id_usuario_reg"];
				
				//Se revisan los filtros
				if (($ind_tipo_reg_todos != 1 && !in_array($id_tipo_reg, $arr_tipos_reg)) || ($ind_usuario_prof_todos != 1 && !in_array($id_usuario_reg, $arr_usuarios_prof))) {
					continue;
				}
				
				//No se imprime el registro de historia clínica antigua
				if ($historia_clinica_obj["id_clase_reg"] == "5") {
					continue;
				}
				
				$admision_obj = $db_admision->get_admision($historia_clinica_obj["id_admision"]);
				
				//Se obtiene la información del tipo de registro
				$tipo_registro_hc_obj = $db_tipos_registros_hc->getTipoRegistroHc($id_tipo_reg);
				
				//Configuraciones básicas
				$pdf->setDefaultLine();
				$pdf->Ln(1);
				
				$y_aux = $pdf->GetY();
				$pdf->Line(10, $y_aux, 205, $y_aux);
				$pdf->Ln(1);
				
				//Se imprime el encabezado del registro de historia clínica
				if ($historia_clinica_obj["id_clase_reg"] == "2") {
					$db_cirugias = new DbCirugias();
					$cirugia_obj = $db_cirugias->get_cirugia($id_hc);
					$usuario_prof_obj = $db_usuarios->getUsuario($cirugia_obj["id_usuario_prof"]);
				} else {
					$usuario_prof_obj = $db_usuarios->getUsuario($historia_clinica_obj["id_usuario_crea"]);
				}
				
				$nombre_usuario_alt = "";
				if ($usuario_prof_obj["ind_anonimo"] == "1" && $historia_clinica_obj["nombre_usuario_alt"] != "") {
					$nombre_usuario_alt = $historia_clinica_obj["nombre_usuario_alt"];
				} else {
					$nombre_usuario_alt = $usuario_prof_obj["nombre_usuario"]." ".$usuario_prof_obj["apellido_usuario"];
				}
				
				$pdf->SetFont("Arial", "B", 11);
				$pdf->Cell(90, 4, ajustarCaracteres($nombre_tipo_reg), 0, 0, 'L');
				$pdf->SetFont("Arial", "B", 9);
				$pdf->Cell(70, 4, ajustarCaracteres("Atiende: ".$nombre_usuario_alt), 0, 0, 'L');
				$pdf->Cell(35, 4, ajustarCaracteres("Fecha: ".$funciones_persona->obtenerFecha6($historia_clinica_obj["fecha_hc_t"])), 0, 2, 'L');
				$pdf->Ln(2);
				
				$pdf->SetFont("Arial", "", 9);
				
				$texto_observaciones = "";
				
				switch ($id_tipo_reg_base) {
					case "1": //CONSULTA DE OPTOMETRIA
						require_once("../db/DbConsultaOptometria.php");
						$db_consulta_optometria = new DbConsultaOptometria();
						
						$consulta_optometria_obj = $db_consulta_optometria->getConsultaOptometria($id_hc);
						if (count($consulta_optometria_obj) > 0) {
							if ($consulta_optometria_obj["observaciones_optometria"] != "") {
								$texto_observaciones = ajustarCaracteres($consulta_optometria_obj["observaciones_optometria"]);
							}
							
							if ($consulta_optometria_obj["diagnostico_optometria"] != "") {
								if ($texto_observaciones != "") {
									$texto_observaciones .= "<p></p>";
								}
								$texto_observaciones .= ajustarCaracteres($consulta_optometria_obj["diagnostico_optometria"]);
							}
						}
						break;
						
					case "2": //CONSULTA DE OFTALMOLOGIA
						require_once("../db/DbConsultaOftalmologia.php");
						$db_consulta_oftalmologia = new DbConsultaOftalmologia();
						
						$consulta_oftalmologia_obj = $db_consulta_oftalmologia->getConsultaOftalmologia($id_hc);
						if (count($consulta_oftalmologia_obj) > 0) {
							if ($consulta_oftalmologia_obj["diagnostico_oftalmo"] != "") {
								$texto_observaciones = ajustarCaracteres($consulta_oftalmologia_obj["diagnostico_oftalmo"]);
							}
						}
						break;
						
					case "4": //PROCEDIMIENTO QUIRÚRGICO
					case "11": //PROCEDIMIENTO QUIRÚRGICO LÁSER
						$db_cirugias = new DbCirugias();
						
						$cirugia_obj = $db_cirugias->get_cirugia($id_hc);
						if (count($cirugia_obj) > 0) {
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(15, 5, ajustarCaracteres("Código"), "TL", 0, 'L');
							$pdf->Cell(150, 5, ajustarCaracteres("Procedimiento"), "T", 0, 'L');
							$pdf->Cell(15, 5, ajustarCaracteres("Ojo"), "T", 0, 'C');
							$pdf->Cell(15, 5, ajustarCaracteres("Vía"), "TR", 1, 'C');
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
							
							if ($cirugia_obj["observaciones_cx"] != "") {
								$texto_observaciones = ajustarCaracteres($cirugia_obj["observaciones_cx"]);
							}
						}
						break;
						
					case "5": //CONSULTA PREQUIRÚRGICA DE CATARATA
						require_once("../db/DbConsultaPreqxCatarata.php");
						$db_consulta_preqx_catarata = new DbConsultaPreqxCatarata();
						
						$consulta_preqx_catarata_obj = $db_consulta_preqx_catarata->get_consulta_preqx_catarata($id_hc);
						if (count($consulta_preqx_catarata_obj) > 0) {
							if ($consulta_preqx_catarata_obj["diagnostico_preqx_catarata"] != "") {
								$texto_observaciones = ajustarCaracteres($consulta_preqx_catarata_obj["diagnostico_preqx_catarata"]);
							}
						}
						break;
						
					case "6": //CONSULTA PREQUIRÚRGICA LÁSER (OPTOMETRÍA)
						require_once("../db/DbConsultaPreqxLaser.php");
						$db_consulta_preqx_laser = new DbConsultaPreqxLaser();
						
						$consulta_preqx_laser_obj = $db_consulta_preqx_laser->getConsultaPreqxLaser($id_hc);
						if (count($consulta_preqx_laser_obj) > 0) {
							if ($consulta_preqx_laser_obj["diagnostico_preqx_laser"] != "") {
								$texto_observaciones = ajustarCaracteres($consulta_preqx_laser_obj["diagnostico_preqx_laser"]);
							}
						}
						break;
						
					case "7": //CONSULTA PREQUIRÚRGICA LÁSER (OFTALMOLOGÍA)
						require_once("../db/DbConsultaPreqxLaserOf.php");
						$db_consulta_preqx_laser_of = new DbConsultaPreqxLaserOf();
						
						$consulta_preqx_laser_of_obj = $db_consulta_preqx_laser_of->getConsultaPreqxLaserOf($id_hc);
						if (count($consulta_preqx_laser_of_obj) > 0) {
							if ($consulta_preqx_laser_of_obj["diagnostico_preqx_laser_of"] != "") {
								$texto_observaciones = ajustarCaracteres($consulta_preqx_laser_of_obj["diagnostico_preqx_laser_of"]);
							}
						}
						break;
						
					case "8": //CONSULTA CONTROL LÁSER (OPTOMETRIA)
						require_once("../db/DbConsultaControlLaser.php");
						$db_consulta_control_laser = new DbConsultaControlLaser();
						
						$consulta_control_laser_obj = $db_consulta_control_laser->getConsultaControlLaser($id_hc);
						if (count($consulta_control_laser_obj) > 0) {
							if ($consulta_control_laser_obj["diagnostico_control_laser"] != "") {
								$texto_observaciones = ajustarCaracteres($consulta_control_laser_obj["diagnostico_control_laser"]);
							}
						}
						break;
						
					case "9": //CONSULTA CONTROL LÁSER (OFTALMOLOGÍA)
						require_once("../db/DbConsultaControlLaserOf.php");
						$db_consulta_control_laser_of = new DbConsultaControlLaserOf();
						
						$consulta_control_laser_of_obj = $db_consulta_control_laser_of->getConsultaControlLaserOf($id_hc);
						if (count($consulta_control_laser_of_obj) > 0) {
							if ($consulta_control_laser_of_obj["diagnostico_control_laser_of"] != "") {
								$texto_observaciones = ajustarCaracteres($consulta_control_laser_of_obj["diagnostico_control_laser_of"]);
							}
						}
						break;
						
					case "10": //EXAMEN (OPTOMETRÍA)
						require_once("../db/DbExamenesOptometria.php");
						$db_examenes_optometria = new DbExamenesOptometria();
						
						$examen_optometria_obj = $db_examenes_optometria->get_examen_optometria($id_hc);
						if (count($examen_optometria_obj) > 0) {
							//Se obtiene el listado de exámenes
							$lista_examenes = $db_examenes_optometria->get_lista_examenes_optometria_hc($id_hc);
							
							if (count($lista_examenes) > 0) {
								for ($i = 0; $i < count($lista_examenes); $i++) {
									$examen_aux = $lista_examenes[$i];
									if ($ind_imagenes_hc != 1 || $examen_aux["id_examen_hc"] == $historia_clinica_obj["id_examen_hc"]) {
										$pdf->SetFont("Arial", "B", 9);
										$pdf->Cell(30, 5, ajustarCaracteres("Examen No. ".($i + 1)), 0, 0, 'L');
										$pdf->Cell(135, 5, ajustarCaracteres($examen_aux["nombre_examen"]), 0, 0, 'L');
										$pdf->Cell(30, 5, ajustarCaracteres("Ojo: ".$examen_aux["ojo"]), 0, 1, 'L');
										$pdf->Ln(0);
										
										$pdf->SetFont("Arial", "", 9);
										$texto_examen = "";
										if ($examen_aux["observaciones_examen"] != "") {
											$texto_examen = ajustarCaracteres($examen_aux["observaciones_examen"]);
										}
										
										if ($texto_examen == "") {
											$texto_examen = "(Sin observaciones)";
										}
										$pdf->Ln(1);
										$pdfHTML->WriteHTML($texto_examen, $pdf);
										$pdf->Ln(5);
										
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
									}
								}
							}
						}
						break;
						
					case "18": //ARCHIVOS ADJUNTOS
						if (count($historia_clinica_obj) > 0) {
							$pdf->Ln(1);
							$pdf->SetFont("Arial", "B", 9);
							$pdf->Cell(14, 5, ajustarCaracteres("Nombre:"), 0, 0, 'L');
							$pdf->SetFont("Arial", "", 9);
							$pdf->Cell(181, 5, ajustarCaracteres($historia_clinica_obj["nombre_alt_tipo_reg"]), 0, 1, 'L');
							
							if ($historia_clinica_obj["observaciones_hc"] != "") {
								$texto_observaciones = ajustarCaracteres($historia_clinica_obj["observaciones_hc"]);
							}
						}
						break;
						
					case "19": //CONSULTA DE CONTROL DE OPTOMETRIA
						require_once("../db/DbConsultaControlOptometria.php");
						$db_consulta_control_optometria = new DbConsultaControlOptometria();
						
						$consulta_control_optometria_obj = $db_consulta_control_optometria->getConsultaControlOptometria($id_hc);
						if (count($consulta_control_optometria_obj) > 0) {
							if ($consulta_control_optometria_obj["diagnostico_optometria"] != "") {
								$texto_observaciones = ajustarCaracteres($consulta_control_optometria_obj["diagnostico_optometria"]);
							}
						}
						break;
					
					case "17": //HISTORIA CLÍNICA FÍSICA
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
						
						$consulta_evolucion_obj = $db_consulta_evolucion->get_consulta_evolucion($id_hc);
						if (count($consulta_evolucion_obj) > 0) {
							if ($consulta_evolucion_obj["texto_evolucion"] != "") {
								$texto_observaciones = ajustarCaracteres($consulta_evolucion_obj["texto_evolucion"]);
							}
							
							if ($consulta_evolucion_obj["diagnostico_evolucion"] != "") {
								if ($texto_observaciones != "") {
									$texto_observaciones .= "<p></p>";
								}
								$texto_observaciones .= ajustarCaracteres($consulta_evolucion_obj["diagnostico_evolucion"]);
							}
						}
						break;
				}
				
				if ($historia_clinica_obj["id_clase_hc"] != "3") {
					if ($texto_observaciones == "") {
						$texto_observaciones = "(Sin observaciones)";
					}
					$pdf->Ln(1);
					$pdfHTML->WriteHTML($texto_observaciones, $pdf);
					$pdf->Ln(5);
				}
			}
		}
		
		//Se guarda el documento pdf
		$nombreArchivo = "../tmp/historia_clinica_resumen_".$id_usuario.".pdf";
		$pdf->Output($nombreArchivo, "F");
?>
<input type="hidden" id="hdd_ruta_arch_hc_pdf" value="<?php echo($nombreArchivo); ?>" />
<?php
	}
?>
