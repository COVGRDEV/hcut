<?php
	require_once("DbHistoriaClinica.php");
	
	class DbDespacho extends DbHistoriaClinica {
		
		/**
		 * Obtener los datos de a consulta de optometria a partir del ID de la HC
		 */
		public function getHcAdmisiones($id_admisiones) {
			try {
				$sql = "SELECT HC.*
						FROM historia_clinica HC
						INNER JOIN admisiones A ON HC.id_admision=A.id_admision
						INNER JOIN tipos_citas_det CD ON A.id_tipo_cita=CD.id_tipo_cita AND HC.id_tipo_reg=CD.id_tipo_reg
						WHERE A.id_admision=".$id_admisiones."
						ORDER BY CD.ind_obligatorio, CD.orden";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Obtener la hc de oftalmologia 
		 */
		public function getOftalmologia($id_hc) {
			try {
				$sql = "SELECT * FROM consultas_oftalmologia
						WHERE id_hc=".$id_hc;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Obtener la hc de optometria 
		 */
		public function getOptometria($id_hc) {
			try {
				$sql = "SELECT * FROM consultas_optometria
						WHERE id_hc=".$id_hc;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Obtener la hc de control optometria 
		 */
		public function getControlOptometria($id_hc) {
			try {
				$sql = "SELECT * FROM consultas_control_optometria
						WHERE id_hc=".$id_hc;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Obtener la hc de evolucion 
		 */
		public function getEvolucion($id_hc) {
			try {
				$sql = "SELECT * FROM consultas_evoluciones
						WHERE id_hc=".$id_hc;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Obtener la hc de control laser oftalmologia 
		 */
		public function getControlLaser($id_hc) {
			try {
				$sql = "SELECT * FROM consultas_control_laser
						WHERE id_hc=".$id_hc;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Obtener la hc de control laser oftalmologia 
		 */
		public function getControlLaserOftalmologia($id_hc) {
			try {
				$sql = "SELECT * FROM consultas_control_laser_of
						WHERE id_hc=".$id_hc;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Obtener la hc de control laser oftalmologia 
		 */
		public function getConsultaPreqxLaserOf($id_hc) {
			try {
				$sql = "SELECT * FROM consultas_preqx_laser_of
						WHERE id_hc=".$id_hc;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Obtener la hc de pre qx Catarata 
		 */
		public function getConsultaPreqxCatarata($id_hc) {
			try {
				$sql = "SELECT * FROM consultas_preqx_catarata
						WHERE id_hc=".$id_hc;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Obtener la hc de dermatología
		 */
		public function getDermatologia($id_hc) {
			try {
				$sql = "SELECT * FROM consultas_dermatologia
						WHERE id_hc=".$id_hc;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Obtener las formuals medicas predeterminadas
		 */
		public function getFormulasMedicas() {
			try {
				$sql = "SELECT * FROM formulas_predef
						WHERE ind_activo=1
						ORDER BY id_formula";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Crear despacho
		public function crearEditarDespacho($id_admision, $id_paciente, $formula_medica, $lista_formulas_medicas, $lista_cotizaciones, $tipo_formula, $id_tipo, $id_usuario) {
			try {
				//Se limpia la tabla temporal de detalle
				$sql = "DELETE FROM temporal_despacho_det
						WHERE id_usuario=".$id_usuario;
				
				$this->ejecutarSentencia($sql, array());
				
				$bol_continuar = true;
				//Se insertan las fórmulas mpédicas en la tabla temporal de detalle
				for ($i = 0; $i < count($lista_formulas_medicas); $i++) {
					$reg_aux = $lista_formulas_medicas[$i];
					
					$fecha_aux = $reg_aux["fecha_det"];
					if ($fecha_aux != "") {
						$fecha_aux = "STR_TO_DATE('" . $fecha_aux . "', '%d/%m/%Y')";
					} else {
						$fecha_aux = "NULL";
					}
					$sql = "CALL pa_crear_temporal_despacho_det(" . $id_usuario . ", " . ($i + 1) . ", '" . $reg_aux["formula_medica"] . "', '" . substr($reg_aux["remitido"], 0, 100) . "', '" . substr($reg_aux["num_carnet"], 0, 20) . "', " . $fecha_aux . ", @id)";
					
					$arrCampos[0] = "@id";
					$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
					$resul_aux = intval($arrResultado["@id"], 10);
					
					if ($resul_aux <= 0) {
						$bol_continuar = false;
						break;
					}
				}
				
				if ($bol_continuar) {
					//Se limpia la tabla temporal de cotizaciones
					$sql = "DELETE FROM temporal_despacho_cotizaciones
							WHERE id_usuario=".$id_usuario;
					
					$this->ejecutarSentencia($sql, array());
					
					//Se insertan las cotizaciones en la tabla temporal
					for ($i = 0; $i < count($lista_cotizaciones); $i++) {
						$reg_aux = $lista_cotizaciones[$i];
						
						$sql = "CALL pa_crear_temporal_despacho_cotizaciones(" . $id_usuario . ", " . ($i + 1) . ", " . $reg_aux["id_proc_cotiz"] . ", " . $reg_aux["valor_cotiz"] . ", '" . $reg_aux["observaciones_cotiz"] . "', @id)";
						
						$arrCampos[0] = "@id";
						$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
						$resul_aux = intval($arrResultado["@id"], 10);
						
						if ($resul_aux <= 0) {
							$bol_continuar = false;
							break;
						}
					}
					
					if ($bol_continuar) {
						$sql = "CALL pa_crear_editar_despacho(" . $id_admision . ", " . $id_paciente . ", '" .
								$formula_medica . "', " . $tipo_formula . ", " . $id_tipo . ", " . $id_usuario . ", @id)";
						
						$arrCampos[0] = "@id";
						$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
						$id_despacho = $arrResultado["@id"];
						
						return $id_despacho;
					} else {
						//Error al registrar los temporales de despacho_cotizaciones
						return -4;
					}
				} else {
					//Error al registrar los temporales de despacho_det
					return -3;
				}
			} catch (Exception $e) {
				return -2;
			}
		}
		
		public function editarDespachoFormulaMedica($id_despacho, $formula_medica, $id_usuario) {
			try {
				$sql = "UPDATE despacho
						SET formula_medica='".$formula_medica."'
						WHERE id_despacho=".$id_despacho;
				
				$this->ejecutarSentencia($sql, array());
				
				return 1;
			} catch (Exception $e) {
				return -2;
			}
		}
		
		/**
		 * Obtener el despacho
		 */
		public function getDespacho($id_admision) {
			try {
				$sql = "SELECT D.*, UC.nombre_usuario AS nombre_usuario_crea, UC.apellido_usuario AS apellido_usuario_crea,
						DATE_FORMAT(D.fecha_crea, '%d/%m/%Y %h:%i:%s %p') AS fecha_crea_t,
						UM.nombre_usuario AS nombre_usuario_mod, UM.apellido_usuario AS apellido_usuario_mod,
						DATE_FORMAT(D.fecha_mod, '%d/%m/%Y %h:%i:%s %p') AS fecha_mod_t
						FROM despacho D
						LEFT JOIN usuarios UC ON D.id_usuario_crea=UC.id_usuario
						LEFT JOIN usuarios UM ON D.id_usuario_mod=UM.id_usuario
						WHERE D.id_admision=".$id_admision;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Buscar personas con admisiones
		 */
		public function getPacientesAdmisiones($txt_paciente_hc) {
			$txt_paciente_hc = str_replace(" ", "%", $txt_paciente_hc);
			try {
				$sql = "SELECT DISTINCT p.*, l.nombre_detalle AS tipo_documento, DATE_FORMAT(p.fecha_nacimiento, '%d/%m/%Y') AS fecha_nac_persona 
						FROM pacientes p 
						INNER JOIN admisiones a ON a.id_paciente = p.id_paciente
						INNER JOIN listas_detalle l ON l.id_detalle = p.id_tipo_documento
						WHERE (p.numero_documento LIKE '%".$txt_paciente_hc."%'
						OR CONCAT(IFNULL(p.nombre_1,''),' ',IFNULL(p.nombre_2,''),' ',IFNULL(p.apellido_1,''),' ',IFNULL(p.apellido_2,'')) LIKE '%$txt_paciente_hc%')
						ORDER BY p.nombre_1, p.nombre_2, p.apellido_1, p.apellido_2";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Obtener los despachos de los pacientes
		 */
		public function getRegistrosDespacho($id_paciente) {
			try {
				$sql = "SELECT p.*, d.*, c.nombre_tipo_cita, a.id_lugar_cita, a.motivo_consulta, DATE_FORMAT(d.fecha_despacho, '%d/%m/%Y') AS fecha_despacho_t FROM despacho d
						INNER JOIN pacientes p ON p.id_paciente=d.id_paciente
						INNER JOIN admisiones a ON a.id_admision=d.id_admision
						INNER JOIN tipos_citas c ON c.id_tipo_cita=a.id_tipo_cita
						WHERE p.id_paciente=".$id_paciente;
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Obtener los detalles despachos de los pacientes
		 */
		public function getRegistrosDespachoDet($id_paciente, $id_admision) {
			try {
				$sql = "SELECT p.*, d.*, c.nombre_tipo_cita, a.id_lugar_cita, a.motivo_consulta, a.fecha_admision, DATE_FORMAT(d.fecha_despacho, '%d/%m/%Y') AS fecha_despacho_t 
						FROM despacho d
						INNER JOIN pacientes p ON p.id_paciente=d.id_paciente
						INNER JOIN admisiones a ON a.id_admision=d.id_admision
						INNER JOIN tipos_citas c ON c.id_tipo_cita=a.id_tipo_cita
						WHERE p.id_paciente=".$id_paciente.
						" AND  a.id_admision=".$id_admision;
					//echo($sql);	
					
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		
		
		/**
		 * Indica si se puede editar una HC dependiendo de las horas predeterminadas
		 */
		public function getIndicadorHorasFormulas($id_admision, $hora_edicion) {
			try {
				$sql = "SELECT (fecha_despacho + INTERVAL $hora_edicion HOUR) - NOW() AS diferencia
						FROM despacho
						WHERE id_admision=".$id_admision;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Indica si un usuario puede o no hacer acciones en la HC 
		 */
		public function getIndicadorFormulaUsuario($id_admision, $id_usuario) {
			try {
				$sql = "SELECT COUNT(*) AS ind_pertenece
						FROM despacho
						WHERE id_admision=".$id_admision."
						AND id_usuario_crea=".$id_usuario;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Funcion para indicar que se puedo editar una formula de despacho
		 */
		public function getIndicadorEdicionFormulas($id_admision, $hora_edicion) {
			$id_usuario = $_SESSION["idUsuario"];
			$ind_edicion = 0;
			$ind_hora_editar = $this->getIndicadorHorasFormulas($id_admision, $hora_edicion);
			
			if ($ind_hora_editar['diferencia'] >= 0) { //Si se puede editar dependiendo de las horas para editar
				$ind_edicion = 1;
			}
			return $ind_edicion;
		}
		
		/**
		 * Obtener los registros de detalle de despacho
		 */
		public function getListaDespachoDet($id_admision) {
			try {
				$sql = "SELECT *, DATE_FORMAT(fecha_det, '%d/%m/%Y') AS fecha_det_t
						FROM despacho_det
						WHERE id_admision=".$id_admision."
						ORDER BY id_despacho_det";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Obtener un registro de detalle de despacho
		 */
		public function getDespachoDetNumFormula($id_admision, $num_formula) {
			try {
				$sql = "SELECT DD.*, DATE_FORMAT(DD.fecha_det, '%d/%m/%Y') AS fecha_det_t,
						SD.tel_sede_det, SD.dir_sede_det, SD.dir_logo_sede_det
						FROM despacho_det DD
						INNER JOIN admisiones A ON DD.id_admision=A.id_admision
						LEFT JOIN sedes_det SD ON A.id_lugar_cita=SD.id_detalle
						WHERE DD.id_admision=".$id_admision."
						AND DD.num_formula=".$num_formula;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaDespachoDetTotal() {
			try {
				$sql = "SELECT * FROM despacho_det";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function actualizarFormulaDespachoDet($id_despacho_det, $formula_medica) {
			try {
				$sql = "UPDATE despacho_det
						SET formula_medica='".$formula_medica."'
						WHERE id_despacho_det=".$id_despacho_det;
				
				$this->ejecutarSentencia($sql, array());
				
				return 1;
			} catch (Exception $e) {
				return -2;
			}
		}
		
		/**
		 * Obtener los registros de cotización de despacho
		 */
		public function getListaDespachoCotizaciones($id_admision) {
			try {
				$sql = "SELECT DC.*, PC.nombre_proc_cotiz, SD.tel_sede_det, SD.dir_sede_det, SD.dir_logo_sede_det
						FROM despacho_cotizaciones DC
						INNER JOIN admisiones A ON DC.id_admision=A.id_admision
						LEFT JOIN sedes_det SD ON A.id_lugar_cita=SD.id_detalle
						INNER JOIN procedimientos_cotizaciones PC ON DC.id_proc_cotiz=PC.id_proc_cotiz
						WHERE DC.id_admision=".$id_admision."
						ORDER BY DC.id_despacho_cotiz";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Obtener registros de cotización de despacho por parámetros de búsqueda
		 */
		public function getListaDespachoCotizacionesParams($txt_paciente, $id_proc_cotiz, $observaciones_cotiz, $fecha_ini, $fecha_fin, $id_usuario_prof = "", $id_lugar_cita = "") {
			try {
				$txt_paciente = str_replace(" ", "%", trim($txt_paciente));
				$observaciones_cotiz = str_replace(" ", "%", trim($observaciones_cotiz));
				
				$sql = "SELECT DC.*, PC.nombre_proc_cotiz, DATE_FORMAT(D.fecha_despacho, '%d/%m/%Y') AS fecha_despacho_t,
						TD.codigo_detalle AS cod_tipo_documento, TD.nombre_detalle AS tipo_documento, P.numero_documento,
						P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, P.telefono_1, P.telefono_2, A.id_lugar_cita,
						LC.nombre_detalle AS lugar_cita, A.id_usuario_prof, UP.nombre_usuario, UP.apellido_usuario
						FROM despacho_cotizaciones DC
						INNER JOIN despacho D ON DC.id_despacho=D.id_despacho
						INNER JOIN admisiones A ON D.id_admision=A.id_admision
						INNER JOIN pacientes P ON D.id_paciente=P.id_paciente
						INNER JOIN listas_detalle TD ON P.id_tipo_documento=TD.id_detalle
						INNER JOIN procedimientos_cotizaciones PC ON DC.id_proc_cotiz=PC.id_proc_cotiz
						INNER JOIN listas_detalle LC ON A.id_lugar_cita=LC.id_detalle
						INNER JOIN usuarios UP ON A.id_usuario_prof=UP.id_usuario
						WHERE DATE(D.fecha_despacho) BETWEEN STR_TO_DATE('".$fecha_ini."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_fin."', '%d/%m/%Y') ";
				if ($txt_paciente != "") {
					$sql .= "AND (P.numero_documento='".$txt_paciente."'
							OR CONCAT(P.nombre_1, ' ', IFNULL(P.nombre_2, ''), P.apellido_1, IFNULL(P.apellido_2, '')) LIKE '%".$txt_paciente."%') ";
				}
				if ($id_proc_cotiz != "") {
					$sql .= "AND DC.id_proc_cotiz=".$id_proc_cotiz." ";
				}
				if ($id_usuario_prof != "") {
					$sql .= "AND A.id_usuario_prof=".$id_usuario_prof." ";
				}
				if ($id_lugar_cita != "") {
					$sql .= "AND A.id_lugar_cita=".$id_lugar_cita." ";
				}
				if ($observaciones_cotiz != "") {
					$sql .= "AND DC.observaciones_cotiz LIKE '%".$observaciones_cotiz."%' ";
				}
				$sql .= "ORDER BY DC.id_despacho_cotiz";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getDiagnosticoClinicoDet($id_admision){
			
			try{
				$sql = "SELECT AD.*, DH.cod_ciex, DH.id_ojo, DH.orden FROM admisiones AD
						LEFT JOIN historia_clinica HC ON AD.id_admision = HC.id_admision
						LEFT JOIN diagnosticos_hc  DH ON HC.id_hc = DH.id_hc 
						WHERE AD.id_admision=".$id_admision."
						ORDER BY DH.orden";

						
				return $this->getDatos($sql);	
			}catch(Exception $e){
				return array();
			}
			
		}
		
public function verificarExistenciaIncapacidad($array){
			
			$id_incapacidad = "";
			foreach($array as $value){
				$id_incapacidad = $value["id_incapacidad"];	
			}
			
			return($id_incapacidad);
}
				
		
		public function GuardarIncapacidad($id_paciente,$id_profesional,$id_usuario,$id_hc,$id_admision,$tipo_atencion,$origen_incapacidad,$prorroga,$fecha_inicial, $fecha_final,$observaciones, $id_convenio,$id_plan,$id_lugar_cita, $diagnostico_principal, $diagnostico_relacionado){
				
				try{
					//Consulta para saber si ya realizó alguna incapacidad para esa HC	
					if($id_admision <> "" || $id_hc <> "")	{		
										$incapacidad = "SELECT * FROM incapacidades
										WHERE"; 
										if($id_hc <> ""){
											$incapacidad .= " id_hc=".$id_hc."";
										}
										if($id_admision <> ""){
											if($id_hc <> ""){
												$incapacidad .=" OR ";
											}
											$incapacidad .=" id_admision=".$id_admision;
					}
											
						$incapacidad = $this->getDatos($incapacidad);						
						$id_incapacidad = $this->verificarExistenciaIncapacidad($incapacidad);
				
					}
					
					$id_incapacidad == "" ? $id_incapacidad = 0 : $id_incapacidad = $id_incapacidad;
					$id_paciente == "" ? $id_paciente = "NULL" : $id_paciente = $id_paciente;
					$id_profesional == "" ? $id_profesional = "NULL" : $id_profesional = $id_profesional;
					$id_usuario == "" ? $id_usuario = "NULL" : $id_usuario = $id_usuario;
					$id_hc == "" ? $id_hc = "NULL" : $id_hc = $id_hc;
					$id_admision == "" ? $id_admision = "NULL" : $id_admision = $id_admision;
					$tipo_atencion == "" ? $tipo_atencion = "NULL" : $tipo_atencion = $tipo_atencion;
					$origen_incapacidad == "" ? $origen_incapacidad = "NULL" : $origen_incapacidad = $origen_incapacidad;
					$prorroga == "" ? $prorroga = "NULL" : $prorroga = $prorroga;
					$fecha_inicial == "" ? $fecha_inicial = "NULL" : $fecha_inicial = $fecha_inicial;
					$fecha_final == "" ? $fecha_final = "NULL" : $fecha_final = $fecha_final;
					$observaciones == "" ? $observaciones = "NULL" : $observaciones = $observaciones;
					$id_convenio == "" ? $id_convenio = "NULL" : $id_convenio = $id_convenio;
					$id_lugar_cita == "" ? $id_lugar_cita = "NULL" : $id_lugar_cita = $id_lugar_cita;
					$id_plan == "" ? $id_plan = "NULL" : $id_plan = $id_plan;
					$observaciones == "" ? $observaciones = "NULL" : $observaciones = strip_tags($observaciones);
					
					$diagnostico_principal  == "" ? $diagnostico_principal = "NULL" : $diagnostico_principal = $diagnostico_principal;
					$diagnostico_relacionado  == "" ? $diagnostico_relacionado = "NULL" : $diagnostico_relacionado = $diagnostico_relacionado;	
												
					$sql = "CALL pa_crear_incapacidad(".$id_paciente.", ".$id_profesional.", ".$id_usuario.", ".$id_hc.", ".$id_admision.", ".$tipo_atencion.",
					".$origen_incapacidad.", ".$prorroga.", '".$fecha_inicial."', '".$fecha_final."', '".$observaciones."', ".$id_convenio.",".$id_plan.", 
					".$id_lugar_cita.",".$id_incapacidad.", '".$diagnostico_principal."', '".$diagnostico_relacionado."', @id)";
					 
					$arrCampos[0] 	= "@id";
					$arrResultado 	= $this->ejecutarSentencia($sql, $arrCampos);
					$resultado_out	= $arrResultado["@id"];
					return $resultado_out;	
					
				}catch(Exception $e){
					return array();
				}
				
				
		}
		
	}
?>
