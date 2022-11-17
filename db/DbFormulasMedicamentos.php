<?php

require_once("DbConexion.php");

class DbFormulasMedicamentos extends DbConexion {

	public function getFormulaByiD($id) {
		try {
			$sql = "SELECT FM.*, DATE_FORMAT(FM.fecha_crea,'%d') AS Dia, DATE_FORMAT(FM.fecha_crea,'%m') AS Mes, DATE_FORMAT(FM.fecha_crea,'%Y') AS Anio, DATE_FORMAT(FM.fecha_crea,'%H:%i:%s %p') AS Hora, 
					U.ind_anonimo, U.nombre_usuario, U.apellido_usuario, U.num_reg_medico, PC.numero_documento, DATE_FORMAT(FM.fecha_crea,'%d/%m/%Y %H:%i:%s %p') AS fechaFormulacion,
					DATEDIFF(NOW(),FM.fecha_crea) diasTranscurridos, DATE_FORMAT(FM.fecha_homologacion,'%d/%m/%Y ') AS fechaFormulaHomologada, A.id_admision, TP.nombre_tipo_cita, C.nombre_convenio AS nombreConvenioAux,
					SD.dir_logo_sede_det, SD.dir_sede_det, SD.tel_sede_det, LD.nombre_detalle AS lugar_aux, PL.nombre_plan AS nombrePlanAux, PL.ind_activo AS ind_activo_plan_aux, 
					PL.ind_despacho_medicamentos AS ind_despacho_medicamentos_aux, C.ind_activo AS ind_activo_convenio_aux
					FROM formulas_medicamentos FM
					LEFT JOIN historia_clinica HC ON FM.id_hc = HC.id_hc
					LEFT JOIN admisiones A ON HC.id_admision = A.id_admision
					LEFT JOIN tipos_citas TP ON A.id_tipo_cita = TP.id_tipo_cita
					INNER JOIN usuarios U ON FM.usuario_crea = U.id_usuario
					INNER JOIN pacientes PC ON FM.id_paciente = PC.id_paciente
					LEFT JOIN convenios C ON FM.id_convenio = C.id_convenio
					LEFT JOIN planes PL ON PL.id_plan = FM.id_plan
					LEFT JOIN sedes_det SD ON FM.id_lugar_formula_medica=SD.id_detalle
					LEFT JOIN listas_detalle LD ON LD.id_detalle = FM.id_lugar_formula_medica
					WHERE id_formula_medicamento = $id;";
					//echo($sql);
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}

	public function getFormulaActivaByHc($idHc) {
		try {
			$sql = "SELECT *
					FROM formulas_medicamentos
					WHERE id_hc = $idHc
					AND estado_formula_medicamento != 4	
					ORDER BY id_formula_medicamento
					LIMIT 1;";
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}

	public function getMedicamentosActivosByFormula($idFormula) {
		try {
			$sql = "SELECT FMD.*, MM.nombre_generico, MM.nombre_comercial, MM.presentacion, FM.estado_formula_medicamento
					FROM formulas_medicamentos_det FMD
					INNER JOIN maestro_medicamentos MM ON FMD.cod_medicamento = MM.cod_medicamento
					INNER JOIN formulas_medicamentos FM ON FMD.id_formula_medicamento = FM.id_formula_medicamento
					WHERE FMD.id_formula_medicamento = $idFormula
					AND FMD.estado_formula_medicamento_det = 1
					ORDER BY FMD.id_formula_medicamento;";
			//echo($sql);
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	public function getDiagnosticosByFormula($idFormula) {
		try {
			$sql = "SELECT
					  FMD.*,
					  MM.nombre_generico,
					  MM.nombre_comercial,
					  MM.presentacion,
					  FM.estado_formula_medicamento,  
					  DHC.cod_ciex,
					  CCX.nombre AS nombre_ciex,
					  FM.cod_ciex AS cod_ciex_2,
					  CCF.nombre AS nombre_ciex_2
					FROM
					  formulas_medicamentos_det FMD 
					  INNER JOIN maestro_medicamentos MM 
						ON FMD.cod_medicamento = MM.cod_medicamento 
					  INNER JOIN formulas_medicamentos FM 
						ON FMD.id_formula_medicamento = FM.id_formula_medicamento
					  LEFT JOIN historia_clinica HC ON FM.id_hc = HC.id_hc
					  LEFT JOIN diagnosticos_hc DHC ON HC.id_hc = DHC.id_hc
					  LEFT JOIN ciex_consolidado CCX ON CCX.codciex = DHC.cod_ciex
					  LEFT JOIN ciex_consolidado CCF ON CCF.codciex = FM.cod_ciex
					WHERE FMD.id_formula_medicamento = $idFormula
					AND FMD.estado_formula_medicamento_det = 1
					ORDER BY FMD.id_formula_medicamento;";
			//echo($sql);
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}

	public function getMedicamentosByFormula($idFormula) {
		try {
			$sql = "SELECT FMD.*, MM.nombre_generico, MM.nombre_comercial, MM.presentacion, FM.estado_formula_medicamento
					FROM formulas_medicamentos_det FMD
					INNER JOIN maestro_medicamentos MM ON FMD.cod_medicamento = MM.cod_medicamento
					INNER JOIN formulas_medicamentos FM ON FMD.id_formula_medicamento = FM.id_formula_medicamento
					WHERE FMD.id_formula_medicamento = $idFormula
					ORDER BY FMD.id_formula_medicamento;";
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}

	public function getMedicamentosByFormulaDespacho($idFormula) {
		try {
			$sql = "SELECT FMD.*, MM.nombre_generico, MM.nombre_comercial, MM.presentacion, FM.estado_formula_medicamento, FM.id_lugar_formula_medica, IF(MM.unidad_medida IS NULL,-1,MM.unidad_medida) AS unidad_medida,  IF(MM.cod_item_siesa IS NULL OR MM.cod_item_siesa = '',-1,MM.cod_item_siesa)  AS cod_siesa
					FROM formulas_medicamentos_det FMD
					INNER JOIN maestro_medicamentos MM ON FMD.cod_medicamento = MM.cod_medicamento
					INNER JOIN formulas_medicamentos FM ON FMD.id_formula_medicamento = FM.id_formula_medicamento
					WHERE FMD.id_formula_medicamento = $idFormula AND FMD.estado_formula_medicamento_det = 1
					ORDER BY FMD.id_formula_medicamento;";
					//echo($sql);
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}

	public function formularMedicamento($cantidad, $modoAdmin, $codMed, $idFormulaMedicamentoDet, $idFormulaMedicamento, $idHc, 
			$idPaciente, $userCrea, $tiempo, $tipoFormulacion, $medicoRemitente, $fechaMologacion, $observacion, $lugarFormula,
			$convenio, $plan, $tipoCotizante, $rango,$cod_ciex="", $ojo="") {
		try {
			$cantidad == "" ? $cantidad = "NULL" : $cantidad = "" . $cantidad . "";
			$modoAdmin == "" ? $modoAdmin = "NULL" : $modoAdmin = "'" . $modoAdmin . "'";
			$codMed == "" ? $codMed = "NULL" : $codMed = "" . $codMed . "";
			$idFormulaMedicamentoDet == "" ? $idFormulaMedicamentoDet = "NULL" : $idFormulaMedicamentoDet = "" . $idFormulaMedicamentoDet . "";
			$idFormulaMedicamento == "" ? $idFormulaMedicamento = "NULL" : $idFormulaMedicamento = "" . $idFormulaMedicamento . "";
			$idHc == "" ? $idHc = "NULL" : $idHc = "" . $idHc . "";
			$idPaciente == "" ? $idPaciente = "NULL" : $idPaciente = "" . $idPaciente . "";
			$userCrea == "" ? $userCrea = "NULL" : $userCrea = "" . $userCrea . "";
			$tiempo == "" ? $tiempo = "NULL" : $tiempo = "" . $tiempo . "";
			
			$tipoFormulacion == "" ? $tipoFormulacion = "NULL" : $tipoFormulacion = "" . $tipoFormulacion . "";
			$medicoRemitente == "" ? $medicoRemitente = "NULL" : $medicoRemitente = "'" . $medicoRemitente . "'";
			$fechaMologacion == "" ? $fechaMologacion = "NULL" : $fechaMologacion = "'" . $fechaMologacion . "'";
			$observacion == "" ? $observacion = "NULL" : $observacion = "'" . $observacion . "'";
			$lugarFormula == "" ? $lugarFormula = "NULL" : $lugarFormula = "" . $lugarFormula . "";
			
			$convenio == "" ? $convenio = "NULL" : $convenio = "" . $convenio . "";
			$plan == "" ? $plan = "NULL" : $plan = "" . $plan . "";
			$tipoCotizante == "" ? $tipoCotizante = "NULL" : $tipoCotizante = "" . $tipoCotizante . "";
			$rango == "" ? $rango = "NULL" : $rango = "" . $rango . "";
			
			$cod_ciex == "" ? $cod_ciex = "NULL" : $cod_ciex = "'" . $cod_ciex . "'";
			$ojo == "" ? $ojo = "NULL" : $ojo = "" . $ojo . "";
			
			$sql = "CALL pa_formular_medicamentos(".$cantidad.", ".$modoAdmin.", ".$codMed.", ".$idFormulaMedicamentoDet.", ".
					$idFormulaMedicamento.", ".$idHc.", ".$idPaciente.", ".$userCrea.", ".$tiempo.", ".$tipoFormulacion.", ".
					$medicoRemitente.", ".$fechaMologacion.", ".$observacion.", ".$lugarFormula.", ".$rango.", ".
					$tipoCotizante.", ".$plan.", ".$convenio.",".$cod_ciex.", ".$ojo.", @id, @id2)";
			

			$arrCampos[0] = "@id";
			$arrCampos[1] = "@id2";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			$resultado = $arrResultado["@id"] . ";" . $arrResultado["@id2"];

			return $resultado;
		} catch (Exception $e) {
			return -2;
		}
	}

	public function eliminarFormularMedicamento($idFormulaMedicamentoDet, $idFormulaMedicamento, $userCrea) {
		try {
			$idFormulaMedicamentoDet == "" ? $idFormulaMedicamentoDet = "NULL" : $idFormulaMedicamentoDet = "" . $idFormulaMedicamentoDet . "";
			$idFormulaMedicamento == "" ? $idFormulaMedicamento = "NULL" : $idFormulaMedicamento = "" . $idFormulaMedicamento . "";
			$userCrea == "" ? $userCrea = "NULL" : $userCrea = "" . $userCrea . "";

			$sql = "CALL pa_eliminar_formular_medicamentos(" . $idFormulaMedicamentoDet . "," . $idFormulaMedicamento . "," . $userCrea . ", @id)";
			//echo($sql)."<br />";

			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			$resultado = $arrResultado["@id"];

			return $resultado;
		} catch (Exception $e) {
			return -2;
		}
	}

	public function consultarDespachoFormulacionesPacientes($param) {
		try {
			$sql = "SELECT FM.*, P.*, l.nombre_detalle AS tipo_documento, DATE_FORMAT(P.fecha_nacimiento, '%d/%m/%Y') AS fecha_nac_persona, CV.nombre_convenio, 
					fu_calcular_edad(P.fecha_nacimiento,CURDATE()) AS edadPaciente, PL.nombre_plan As nombrePlanAux
					FROM formulas_medicamentos FM
					INNER JOIN pacientes P ON FM.id_paciente = P.id_paciente
					INNER JOIN listas_detalle l ON l.id_detalle = P.id_tipo_documento
					LEFT JOIN convenios CV ON P.id_convenio_paciente = CV.id_convenio
					LEFT JOIN planes PL ON PL.id_plan = P.id_plan 
					WHERE (P.numero_documento LIKE '%$param%' OR CONCAT(IFNULL(P.nombre_1,''),' ', IFNULL(P.nombre_2,''),' ', IFNULL(P.apellido_1,''),' ', IFNULL(P.apellido_2,'')) LIKE '%$param%')					
					GROUP BY FM.id_paciente
					ORDER BY P.nombre_1
					LIMIT 10";
			//echo($sql);
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}

	public function formulacionesPacienteDespacho($param) {
		try {
			$sql = "SELECT FM.*, IF(FM.tipo_formula_medicamento = 2, 'Homologada', TR.nombre_tipo_reg) AS tipoCitaFormulacion, SUM(FMD.cantidad_orden) AS cantidad_orden, 
					SUM(FMD.cantidad_pendiente) AS cantidad_pendiente, FM.estado_formula_medicamento, DATE_FORMAT(FM.fecha_crea, '%d/%m/%Y') AS fechaFormulacion,
					LD.nombre_detalle AS lugar_aux, C.nombre_convenio AS nombreConvenioAux, PL.nombre_plan AS nombrePlanAux
					FROM formulas_medicamentos FM
					INNER JOIN pacientes P ON FM.id_paciente = P.id_paciente
					LEFT JOIN historia_clinica HC ON FM.id_hc = HC.id_hc
					LEFT JOIN tipos_registros_hc TR ON HC.id_tipo_reg=TR.id_tipo_reg
					INNER JOIN formulas_medicamentos_det FMD ON FM.id_formula_medicamento = FMD.id_formula_medicamento										
					LEFT JOIN listas_detalle LD ON LD.id_detalle=FM.id_lugar_formula_medica
					LEFT JOIN convenios C ON C.id_convenio = FM.id_convenio
					LEFT JOIN planes PL ON PL.id_plan = FM.id_plan
					WHERE P.id_paciente = $param 
					AND FM.estado_formula_medicamento IN (1,2,3)					
					GROUP BY FM.id_formula_medicamento
					ORDER BY FM.estado_formula_medicamento ASC, FM.fecha_crea DESC;";

			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}

	public function despacharMedicamentos($arrayMedicamentos, $tamanoArrayMedicamentos, $idFormulacion, $usuarioDespacha, $observacion, $paso, $doctSiesa, $idLugarDespacho) {
		try {
			$arrayMedicamentos == "" ? $arrayMedicamentos = "NULL" : $arrayMedicamentos = "'" . $arrayMedicamentos . "'";
			$tamanoArrayMedicamentos == "" ? $tamanoArrayMedicamentos = "NULL" : $tamanoArrayMedicamentos = "" . $tamanoArrayMedicamentos . "";
			$usuarioDespacha == "" ? $usuarioDespacha = "NULL" : $usuarioDespacha = "" . $usuarioDespacha . "";
			$idFormulacion == "" ? $idFormulacion = "NULL" : $idFormulacion = "" . $idFormulacion . "";
			$observacion == "" ? $observacion = "NULL" : $observacion = "'" . $observacion . "'";
			$paso == "" ? $paso = "NULL" : $paso = "'" . $paso . "'";
			$doctSiesa == "" ? $doctSiesa = "NULL" : $doctSiesa = "'" . $doctSiesa . "'";
			$idLugarDespacho == "" ? $idLugarDespacho = "NULL" : $idLugarDespacho = "" . $idLugarDespacho . "";
			
			$sql = "CALL pa_despachar_medicamentos(" . $arrayMedicamentos . "," . $tamanoArrayMedicamentos . ", " . $idFormulacion . ", " . 
					$usuarioDespacha . ", " . $observacion. ", " . $paso. ", " . $doctSiesa . ", " . $idLugarDespacho . ", @id)";


			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			$resultado = $arrResultado["@id"];

			return $resultado;
		} catch (Exception $e) {
			return -2;
		}
	}

	public function getDesachoById($id) {
		try {
			$sql = "select FMD.*, DATE_FORMAT(FMD.fecha_crea, '%d/%m/%Y %I:%i:%s %p') AS fecha_creacion_aux, 
					CONCAT(U.login_usuario, ' - ', U.nombre_usuario,' ', IF(U.apellido_usuario IS NULL,'',U.apellido_usuario)) AS usuario_despacha_aux, 
					FM.id_paciente, SD.dir_logo_sede_det, SD.dir_sede_det, SD.tel_sede_det
					from formulas_medicamentos_desp FMD
					inner join usuarios U on FMD.usuario_crea = U.id_usuario
					inner join formulas_medicamentos FM on FMD.id_formula_medicamento = FM.id_formula_medicamento
					LEFT JOIN sedes_det SD ON FMD.id_lugar_desp=SD.id_detalle
					where FMD.id_formula_medicamento_desp = $id;";

			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}

	public function getMedicamentosDespachadosByDespacho($idDespacho) {
		try {
			$sql = "SELECT MMDD.*, MM.nombre_comercial
					FROM formulas_medicamentos_desp_det MMDD
					INNER JOIN formulas_medicamentos_det FMD ON MMDD.id_formula_medicamento_det = FMD.id_formula_medicamento_det
					INNER JOIN maestro_medicamentos MM ON FMD.cod_medicamento = MM.cod_medicamento
					WHERE MMDD.id_formula_medicamento_desp = $idDespacho
					AND MMDD.ind_anulado IS NULL;";
           //echo($sql);
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}

	public function getDespachosByFormulacion($idFormulacion) {
		try {
			$sql = "SELECT FMD.*, LD.nombre_detalle AS lugar_desp_aux 
					FROM formulas_medicamentos_desp FMD 
					LEFT JOIN listas_detalle LD ON LD.id_detalle = FMD.id_lugar_desp 
					WHERE FMD.id_formula_medicamento=".$idFormulacion."  
					AND FMD.ind_anulado IS NULL
					ORDER BY FMD.fecha_crea DESC;";
		
			//echo $sql;
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	public function getLugarUltimoDespachosByFormulacion($idFormulacion) {
		try {
			$sql = "SELECT id_lugar_desp FROM formulas_medicamentos_desp
					WHERE id_formula_medicamento=".$idFormulacion." 
					ORDER BY fecha_crea DESC LIMIT 1;";
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}

	public function consultarPacientesHomologarFormula($param) {
		try {
			$sql = "SELECT P.*, l.nombre_detalle AS tipo_documento, DATE_FORMAT(P.fecha_nacimiento, '%d/%m/%Y') AS fecha_nac_persona, 
					CV.nombre_convenio, fu_calcular_edad(P.fecha_nacimiento,CURDATE()) AS edadPaciente, PL.ind_despacho_medicamentos AS ind_despacho_medicamentos_aux,
					PL.nombre_plan AS nombrePlanAux
					FROM pacientes P
					INNER JOIN listas_detalle l ON l.id_detalle = P.id_tipo_documento						  
					LEFT JOIN convenios CV ON P.id_convenio_paciente = CV.id_convenio 
					LEFT JOIN planes PL ON PL.id_plan = P.id_plan					  
					WHERE (P.numero_documento LIKE '%$param%' OR CONCAT(IFNULL(P.nombre_1,''),' ', IFNULL(P.nombre_2,''),' ', IFNULL(P.apellido_1,''),' ', IFNULL(P.apellido_2,'')) LIKE '%$param%')										  
					ORDER BY P.nombre_1
					LIMIT 10;";

			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	public function getMedicamentosFechaConvenioPlan($fechaInicial, $fechaFinal, $id_convenio, $id_plan){
	try {
		$sql = "SELECT DATE_FORMAT(FM.fecha_crea, '%d/%m/%Y') AS fecha_formula, LG.nombre_detalle AS sede, P.numero_documento, P.nombre_1,
				P.nombre_2, P.apellido_1, P.apellido_2, CASE WHEN FM.tipo_formula_medicamento=1 THEN
				CONCAT(UP.nombre_usuario, ' ', UP.apellido_usuario) ELSE FM.medico_homologacion END AS nombre_medico,
				MM.cod_medicamento, MM.nombre_comercial, MM.nombre_generico, MM.presentacion, MD.cantidad_orden,
				IFNULL(SUM(DD.cantidad_medicamento_desp_det), 0) AS cantidad_entregada
				FROM formulas_medicamentos FM
				INNER JOIN formulas_medicamentos_det MD ON FM.id_formula_medicamento=MD.id_formula_medicamento
				INNER JOIN listas_detalle LG ON FM.id_lugar_formula_medica=LG.id_detalle
				INNER JOIN pacientes P ON FM.id_paciente=P.id_paciente
				INNER JOIN maestro_medicamentos MM ON MM.cod_medicamento=MD.cod_medicamento
				INNER JOIN planes PL ON FM.id_plan=PL.id_plan
				LEFT JOIN historia_clinica HC ON FM.id_hc=HC.id_hc
				LEFT JOIN usuarios UP ON HC.id_usuario_crea=UP.id_usuario
				LEFT JOIN formulas_medicamentos_desp_det DD ON MD.id_formula_medicamento_det=DD.id_formula_medicamento_det
				WHERE FM.id_convenio=".$id_convenio." ";
		if ($id_plan != "") {
			$sql .= "AND FM.id_plan=".$id_plan." ";
		}
		$sql .= "AND FM.fecha_crea BETWEEN STR_TO_DATE('" .$fechaInicial." 00:00:00', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('".$fechaFinal." 23:59:59', '%Y-%m-%d %H:%i:%s')
				AND FM.estado_formula_medicamento<>4
				GROUP BY FM.fecha_crea, LG.nombre_detalle, P.numero_documento, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2,
				CASE WHEN FM.tipo_formula_medicamento=1 THEN CONCAT(UP.nombre_usuario, ' ', UP.apellido_usuario) ELSE FM.medico_homologacion END,
				MM.cod_medicamento, MM.nombre_comercial, MM.nombre_generico, MM.presentacion, MD.cantidad_orden
				ORDER BY fecha_formula, P.numero_documento";
				
			return $this->getDatos($sql);
		} catch(Exception $e) {
			return array();
		}
	}
	
	public function cambiarEstadoFormulaMedicamentos($medicamentos_despachados, $codDespacho, $id_usuario){
		try{
			
			$resultado =-1;
			
			foreach($medicamentos_despachados as $value){
																
 
				
			$sql ="CALL pa_cambiar_estado_formula_medicamentos(".$codDespacho."," . $value["id_formula_medicamento_det"] . "," . $value["cantidad_medicamento_desp_det"] . ",
					".$value["cantidad_pendiente"].", ". $value["id_formula_medicamento"] . ",  '". $value["lote_medicamento"] . "', " . $id_usuario . ", @id)";
			
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado = $arrResultado["@id"];
				
			}
			
			return $resultado;	
			
		}catch(Exception $e){
			return array();
		}
	}
	
	public function getMedicamentosDespachadosDet($codDespacho){
		
		try{
			/*$sql = "SELECT FM.*, FMD.cantidad_medicamento_desp_det AS cantidad_depachada, FMD.lote_medicamento AS lote, FMD.ind_anulado
					FROM formulas_medicamentos_det FM
					LEFT JOIN formulas_medicamentos_desp_det FMD ON FMD.id_formula_medicamento_det = FM.id_formula_medicamento_det
					WHERE FM.id_formula_medicamento_det =".$id_medicamento."
					AND FMD.ind_anulado IS NULL ORDER BY FMD.id_formula_medicamento_desp DESC LIMIT 1";*/
					
			$sql = "SELECT * FROM formulas_medicamentos_desp_det FMDD
					INNER JOIN formulas_medicamentos_det FMD ON FMD.id_formula_medicamento_det = FMDD.id_formula_medicamento_det
					INNER JOIN formulas_medicamentos FM ON FM.id_formula_medicamento = FMD.id_formula_medicamento
					INNER JOIN maestro_medicamentos MM ON MM.cod_medicamento = FMD.cod_medicamento
					WHERE FMDD.id_formula_medicamento_desp=".$codDespacho;
		
			return $this->getDatos($sql);
		}catch(Exception $e){
			return array();
		}
		
		
	}
	
}

