<?php
	require_once("DbConexion.php");
	
	class DbVisitas extends DbConexion {
		public function crearVisita($tipoVisita, $fechaVisita, $idFamilia, $idUsuario) {
			$sql = "CALL pa_crear_visita(".$tipoVisita.", STR_TO_DATE('".
				   $fechaVisita."', '%d/%m/%Y'), ".$idFamilia.", ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function modificarVisitaFecha($idVisita, $fechaVisita, $idUsuario) {
			$sql = "CALL pa_modificar_visita_fecha(".$idVisita.", STR_TO_DATE('".
				   $fechaVisita."', '%d/%m/%Y'), ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function modificarVisita($idVisita, $indRedUnidos, $indInseguridad, $razonInseguridad, $indFronteras, $idFronteraEst,
				$indSuicidio, $indDeportista, $indArtistica, $indInstrMusical, $indCodigoPolicia, $indManualNuevoCiud,
				$indConocePito, $indDiagVIH, $idTipoVivienda, $idTenencia, $idServicioSanitario, $numPersHabitacion,
				$idCombustibleCocina, $idProvieneAgua, $indReciclaje, $indActProductiva, $actProductiva, $indLavarAlimentos,
				$indComputador, $indInternet, $idUsuario) {
			if ($idFronteraEst == "-1") {
				$idFronteraEst = "NULL";
			}
			
			$sql = "CALL pa_modificar_visita(".$idVisita.", ".$indRedUnidos.", ".$indInseguridad.", '".$razonInseguridad."', ".
				   $indFronteras.", ".$idFronteraEst.", ".$indSuicidio.", ".$indDeportista.", ".$indArtistica.", ".
				   $indInstrMusical.", ".$indCodigoPolicia.", ".$indManualNuevoCiud.", ".$indConocePito.", ".$indDiagVIH.", ".
				   $idTipoVivienda.", ".$idTenencia.", ".$idServicioSanitario.", ".$numPersHabitacion.", ".$idCombustibleCocina.", ".
				   $idProvieneAgua.", ".$indReciclaje.", ".$indActProductiva.", '".$actProductiva."', ".$indLavarAlimentos.", ".
				   $indComputador.", ".$indInternet.", ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function modificarVisitaDatosFin($idVisita, $idPercepcionAlcalde, $idTipoFamilia, $observacionPercepcion, $idUsuario) {
			$sql = "CALL pa_modificar_visita_datos_fin(".$idVisita.", ".$idPercepcionAlcalde.", ".$idTipoFamilia.", '".$observacionPercepcion."', ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function crearVisitaPersona($idVisita, $idFamilia, $idPersona, $edad, $unidadEdad, $sexo, $idSeguridadSocial,
				$codEPS, $indSisben, $idEscolaridad, $gradoEstudio, $indEstudianteActivo, $ocupacion, $indDesplazado,
				$indDamnificado, $indReubicado, $procedencia, $indDiscapacitado, $indMotora, $indCognitiva, $indSensorial,
				$idPertenenciaEtnica, $indConsumePsicoactivas, $indAlcohol, $indMarihuana, $indBazuco, $indCocaina, $otrasPsicoactivas,
				$tiempoActFisica, $frecuenciaActFisica, $numCigarrillosDia, $indCepilloDental, $numCepilladoDia, $numConsulOdontAno,
				$indProtesisDental, $indAgudezaVisual, $indViolentado, $idTipoViolencia, $agresorViolencia, $indDenuncia, $idUsuario) {
			//Se verifica cuales campos vienen vacíos
			if ($idSeguridadSocial == "-1") {
				$idSeguridadSocial = "NULL";
			}
			if ($codEPS == "-1") {
				$codEPS = "NULL";
			} else {
				$codEPS = "'".$codEPS."'";
			}
			if ($indSisben == "-1") {
				$indSisben = "NULL";
			}
			if ($idEscolaridad == "-1") {
				$idEscolaridad = "NULL";
			}
			if ($gradoEstudio == "") {
				$gradoEstudio = "NULL";
			}
			if ($indEstudianteActivo == "-1") {
				$indEstudianteActivo = "NULL";
			}
			if ($indDesplazado == "-1") {
				$indDesplazado = "NULL";
			}
			if ($indDamnificado == "-1") {
				$indDamnificado = "NULL";
			}
			if ($indReubicado == "-1") {
				$indReubicado = "NULL";
			}
			if ($indDiscapacitado == "-1") {
				$indDiscapacitado = "NULL";
			}
			if ($indMotora == "-1") {
				$indMotora = "NULL";
			}
			if ($indCognitiva == "-1") {
				$indCognitiva = "NULL";
			}
			if ($indSensorial == "-1") {
				$indSensorial = "NULL";
			}
			if ($idPertenenciaEtnica == "-1") {
				$idPertenenciaEtnica = "NULL";
			}
			if ($indAlcohol == "-1") {
				$indAlcohol = "NULL";
			}
			if ($indMarihuana == "-1") {
				$indMarihuana = "NULL";
			}
			if ($indBazuco == "-1") {
				$indBazuco = "NULL";
			}
			if ($indCocaina == "-1") {
				$indCocaina = "NULL";
			}
			if ($tiempoActFisica == "") {
				$tiempoActFisica = "NULL";
			}
			if ($frecuenciaActFisica == "") {
				$frecuenciaActFisica = "NULL";
			}
			if ($numCigarrillosDia == "") {
				$numCigarrillosDia = "NULL";
			}
			if ($indCepilloDental == "-1") {
				$indCepilloDental = "NULL";
			}
			if ($numCepilladoDia == "") {
				$numCepilladoDia = "NULL";
			}
			if ($numConsulOdontAno == "") {
				$numConsulOdontAno = "NULL";
			}
			if ($indProtesisDental == "-1") {
				$indProtesisDental = "NULL";
			}
			if ($indAgudezaVisual == "-1") {
				$indAgudezaVisual = "NULL";
			}
			if ($indViolentado == "-1") {
				$indViolentado = "NULL";
			}
			if ($idTipoViolencia == "-1") {
				$idTipoViolencia = "NULL";
			}
			if ($indDenuncia == "-1") {
				$indDenuncia = "NULL";
			}
			
			$sql = "CALL pa_crear_visita_persona(".$idVisita.", ".$idFamilia.", ".$idPersona.", ".$edad.", '".$unidadEdad."', '".
				$sexo."', ".$idSeguridadSocial.", ".$codEPS.", ".$indSisben.", ".$idEscolaridad.", ".$gradoEstudio.", ".
				$indEstudianteActivo.", '".$ocupacion."', ".$indDesplazado.", ".$indDamnificado.", ".$indReubicado.", '".
				$procedencia."', ".$indDiscapacitado.", ".$indMotora.", ".$indCognitiva.", ".$indSensorial.", ".
				$idPertenenciaEtnica.", ".$indConsumePsicoactivas.", ".$indAlcohol.", ".$indMarihuana.", ".$indBazuco.", ".
				$indCocaina.", '".$otrasPsicoactivas."', ".$tiempoActFisica.", ".$frecuenciaActFisica.", ".$numCigarrillosDia.", ".
				$indCepilloDental.", ".$numCepilladoDia.", ".$numConsulOdontAno.", ".$indProtesisDental.", ".
				$indAgudezaVisual.", ".$indViolentado.", ".$idTipoViolencia.", '".$agresorViolencia."', ".$indDenuncia.", ".
				$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function borrarVisitaPersona($idVisitaPersona, $idUsuario) {
			$sql = "CALL pa_borrar_visita_persona(".$idVisitaPersona.", ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function crearVisitaDeporte($idVisitaPersona, $deporte, $idUsuario) {
			$sql = "CALL pa_crear_visita_deporte(".$idVisitaPersona.", '".$deporte."', ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function borrarVisitaDeporte($idVisitaDeporte, $idUsuario) {
			$sql = "CALL pa_borrar_visita_deporte(".$idVisitaDeporte.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function crearVisitaActividadArte($idVisitaPersona, $idDisciplinaArte, $idUsuario) {
			$sql = "CALL pa_crear_visita_actividad_arte(".$idVisitaPersona.", ".$idDisciplinaArte.", ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function borrarVisitaActividadArte($idVisitaActArte, $idUsuario) {
			$sql = "CALL pa_borrar_visita_actividad_arte(".$idVisitaActArte.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function crearVisitaInstrMusical($idVisitaPersona, $instrMusical, $idProcPractica, $idUsuario) {
			$sql = "CALL pa_crear_visita_instr_musical(".$idVisitaPersona.", '".$instrMusical."', ".$idProcPractica.", ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function borrarVisitaInstrMusical($idVisitaInstr, $idUsuario) {
			$sql = "CALL pa_borrar_visita_instr_musical(".$idVisitaInstr.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function modificarVisitaPersonaPito($idVisitaPersona, $indPicadoPito, $indTratamientoPito, $idUsuario) {
			if ($indPicadoPito == "-1") {
				$indPicadoPito = "NULL";
			}
			if ($indTratamientoPito == "-1") {
				$indTratamientoPito = "NULL";
			}
			
			$sql = "CALL pa_modificar_visita_persona_pito(".$idVisitaPersona.", ".$indPicadoPito.", ".$indTratamientoPito.", ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function modificarVisitaPersonaSR($idVisitaPersona, $indSintomaResp, $indTratamientoResp, $idUsuario) {
			if ($indSintomaResp == "-1") {
				$indSintomaResp = "NULL";
			}
			if ($indTratamientoResp == "-1") {
				$indTratamientoResp = "NULL";
			}
			
			$sql = "CALL pa_modificar_visita_persona_sr(".$idVisitaPersona.", ".$indSintomaResp.", ".$indTratamientoResp.", ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function modificarVisitaPersonaSP($idVisitaPersona, $indSintomaPiel, $indTratamientoPiel, $idUsuario) {
			if ($indSintomaPiel == "-1") {
				$indSintomaPiel = "NULL";
			}
			if ($indTratamientoPiel == "-1") {
				$indTratamientoPiel = "NULL";
			}
			
			$sql = "CALL pa_modificar_visita_persona_sp(".$idVisitaPersona.", ".$indSintomaPiel.", ".$indTratamientoPiel.", ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function modificarVisitaPersonaVIH($idVisitaPersona, $indDiagVIH, $idUsuario) {
			if ($indDiagVIH == "-1") {
				$indDiagVIH = "NULL";
			}
			
			$sql = "CALL pa_modificar_visita_persona_vih(".$idVisitaPersona.", ".$indDiagVIH.", ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function crearVisitaAnimal($idVisita, $tipoAnimal, $cantidadAnimales, $cantidadVacunados, $cantidadEsterilizados, $idUsuario) {
			$sql = "CALL pa_crear_visita_animal(".$idVisita.", '".$tipoAnimal."', ".$cantidadAnimales.", ".$cantidadVacunados.", ".$cantidadEsterilizados.", ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function borrarVisitaAnimal($idVisitaAnimal, $idUsuario) {
			$sql = "CALL pa_borrar_visita_animal(".$idVisitaAnimal.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function modificarVisitaPersona_0_5($idVisitaPersona, $indCD, $indLME, $indCarnet, $fechaBCG, $fechaHepBRN, $fechaHepB1,
				$fechaHepB2, $fechaHepB3, $fechaPolio1, $fechaPolio2, $fechaPolio3, $fechaPentavalente1, $fechaPentavalente2,
				$fechaPentavalente3, $fechaNeumococo1, $fechaNeumococo2, $fechaNeumococoAnual, $fechaRotavirus1, $fechaRotavirus2,
				$fechaInfluenza1, $fechaInfluenza2, $fechaInfluenzaAnual, $fechaSrp, $fechaFA, $fechaTV1, $fechaHepA, $fechaRef1DPT,
				$fechaRef1Polio, $fechaRef2DPT, $fechaRef2Polio, $fechaRef2TV, $morbilidadNacer, $morbilidad1, $morbilidad2, $idUsuario) {
			if ($fechaBCG != "") {
				$fechaBCG = "STR_TO_DATE('".$fechaBCG."', '%d/%m/%Y')";
			} else {
				$fechaBCG = "NULL";
			}
			if ($fechaHepBRN != "") {
				$fechaHepBRN = "STR_TO_DATE('".$fechaHepBRN."', '%d/%m/%Y')";
			} else {
				$fechaHepBRN = "NULL";
			}
			if ($fechaHepB1 != "") {
				$fechaHepB1 = "STR_TO_DATE('".$fechaHepB1."', '%d/%m/%Y')";
			} else {
				$fechaHepB1 = "NULL";
			}
			if ($fechaHepB2 != "") {
				$fechaHepB2 = "STR_TO_DATE('".$fechaHepB2."', '%d/%m/%Y')";
			} else {
				$fechaHepB2 = "NULL";
			}
			if ($fechaHepB3 != "") {
				$fechaHepB3 = "STR_TO_DATE('".$fechaHepB3."', '%d/%m/%Y')";
			} else {
				$fechaHepB3 = "NULL";
			}
			if ($fechaPolio1 != "") {
				$fechaPolio1 = "STR_TO_DATE('".$fechaPolio1."', '%d/%m/%Y')";
			} else {
				$fechaPolio1 = "NULL";
			}
			if ($fechaPolio2 != "") {
				$fechaPolio2 = "STR_TO_DATE('".$fechaPolio2."', '%d/%m/%Y')";
			} else {
				$fechaPolio2 = "NULL";
			}
			if ($fechaPolio3 != "") {
				$fechaPolio3 = "STR_TO_DATE('".$fechaPolio3."', '%d/%m/%Y')";
			} else {
				$fechaPolio3 = "NULL";
			}
			if ($fechaPentavalente1 != "") {
				$fechaPentavalente1 = "STR_TO_DATE('".$fechaPentavalente1."', '%d/%m/%Y')";
			} else {
				$fechaPentavalente1 = "NULL";
			}
			if ($fechaPentavalente2 != "") {
				$fechaPentavalente2 = "STR_TO_DATE('".$fechaPentavalente2."', '%d/%m/%Y')";
			} else {
				$fechaPentavalente2 = "NULL";
			}
			if ($fechaPentavalente3 != "") {
				$fechaPentavalente3 = "STR_TO_DATE('".$fechaPentavalente3."', '%d/%m/%Y')";
			} else {
				$fechaPentavalente3 = "NULL";
			}
			if ($fechaNeumococo1 != "") {
				$fechaNeumococo1 = "STR_TO_DATE('".$fechaNeumococo1."', '%d/%m/%Y')";
			} else {
				$fechaNeumococo1 = "NULL";
			}
			if ($fechaNeumococo2 != "") {
				$fechaNeumococo2 = "STR_TO_DATE('".$fechaNeumococo2."', '%d/%m/%Y')";
			} else {
				$fechaNeumococo2 = "NULL";
			}
			if ($fechaNeumococoAnual != "") {
				$fechaNeumococoAnual = "STR_TO_DATE('".$fechaNeumococoAnual."', '%d/%m/%Y')";
			} else {
				$fechaNeumococoAnual = "NULL";
			}
			if ($fechaRotavirus1 != "") {
				$fechaRotavirus1 = "STR_TO_DATE('".$fechaRotavirus1."', '%d/%m/%Y')";
			} else {
				$fechaRotavirus1 = "NULL";
			}
			if ($fechaRotavirus2 != "") {
				$fechaRotavirus2 = "STR_TO_DATE('".$fechaRotavirus2."', '%d/%m/%Y')";
			} else {
				$fechaRotavirus2 = "NULL";
			}
			if ($fechaInfluenza1 != "") {
				$fechaInfluenza1 = "STR_TO_DATE('".$fechaInfluenza1."', '%d/%m/%Y')";
			} else {
				$fechaInfluenza1 = "NULL";
			}
			if ($fechaInfluenza2 != "") {
				$fechaInfluenza2 = "STR_TO_DATE('".$fechaInfluenza2."', '%d/%m/%Y')";
			} else {
				$fechaInfluenza2 = "NULL";
			}
			if ($fechaInfluenzaAnual != "") {
				$fechaInfluenzaAnual = "STR_TO_DATE('".$fechaInfluenzaAnual."', '%d/%m/%Y')";
			} else {
				$fechaInfluenzaAnual = "NULL";
			}
			if ($fechaSrp != "") {
				$fechaSrp = "STR_TO_DATE('".$fechaSrp."', '%d/%m/%Y')";
			} else {
				$fechaSrp = "NULL";
			}
			if ($fechaFA != "") {
				$fechaFA = "STR_TO_DATE('".$fechaFA."', '%d/%m/%Y')";
			} else {
				$fechaFA = "NULL";
			}
			if ($fechaTV1 != "") {
				$fechaTV1 = "STR_TO_DATE('".$fechaTV1."', '%d/%m/%Y')";
			} else {
				$fechaTV1 = "NULL";
			}
			if ($fechaHepA != "") {
				$fechaHepA = "STR_TO_DATE('".$fechaHepA."', '%d/%m/%Y')";
			} else {
				$fechaHepA = "NULL";
			}
			if ($fechaRef1DPT != "") {
				$fechaRef1DPT = "STR_TO_DATE('".$fechaRef1DPT."', '%d/%m/%Y')";
			} else {
				$fechaRef1DPT = "NULL";
			}
			if ($fechaRef1Polio != "") {
				$fechaRef1Polio = "STR_TO_DATE('".$fechaRef1Polio."', '%d/%m/%Y')";
			} else {
				$fechaRef1Polio = "NULL";
			}
			if ($fechaRef2DPT != "") {
				$fechaRef2DPT = "STR_TO_DATE('".$fechaRef2DPT."', '%d/%m/%Y')";
			} else {
				$fechaRef2DPT = "NULL";
			}
			if ($fechaRef2Polio != "") {
				$fechaRef2Polio = "STR_TO_DATE('".$fechaRef2Polio."', '%d/%m/%Y')";
			} else {
				$fechaRef2Polio = "NULL";
			}
			if ($fechaRef2TV != "") {
				$fechaRef2TV = "STR_TO_DATE('".$fechaRef2TV."', '%d/%m/%Y')";
			} else {
				$fechaRef2TV = "NULL";
			}
			
			$sql = "CALL pa_modificar_visita_persona_0_5(".$idVisitaPersona.", ".$indCD.", ".$indLME.", ".$indCarnet.", ".
				   $fechaBCG.", ".$fechaHepBRN.", ".$fechaHepB1.", ".$fechaHepB2.", ".$fechaHepB3.", ".$fechaPolio1.", ".
				   $fechaPolio2.", ".$fechaPolio3.", ".$fechaPentavalente1.", ".$fechaPentavalente2.", ".$fechaPentavalente3.", ".
				   $fechaNeumococo1.", ".$fechaNeumococo2.", ".$fechaNeumococoAnual.", ".$fechaRotavirus1.", ".
				   $fechaRotavirus2.", ".$fechaInfluenza1.", ".$fechaInfluenza2.", ".$fechaInfluenzaAnual.", ".
				   $fechaSrp.", ".$fechaFA.", ".$fechaTV1.", ".$fechaHepA.", ".$fechaRef1DPT.", ".$fechaRef1Polio.", ".
				   $fechaRef2DPT.", ".$fechaRef2Polio.", ".$fechaRef2TV.", '".$morbilidadNacer."', '".$morbilidad1."', '".
				   $morbilidad2."', ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function modificarVisitaPersona_6_9($idVisitaPersona, $indCD, $morbilidad1, $morbilidad2,
				$morbilidad3, $idPrograma1, $idPrograma2, $idPrograma3, $idUsuario) {
			if ($idPrograma1 == "-1") {
				$idPrograma1 = "NULL";
			}
			if ($idPrograma2 == "-1") {
				$idPrograma2 = "NULL";
			}
			if ($idPrograma3 == "-1") {
				$idPrograma3 = "NULL";
			}
			
			$sql = "CALL pa_modificar_visita_persona_6_9(".$idVisitaPersona.", ".$indCD.", '".$morbilidad1."', '".
				   $morbilidad2."', '".$morbilidad3."', ".$idPrograma1.", ".$idPrograma2.", ".$idPrograma3.", ".
				   $idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function modificarVisitaPersona_10_52($idVisitaPersona, $indSexualActiva, $indMetodoPlanifica,
				$tiempoMetodoPlanifica, $indControlesPlanifica, $motivoNoPlanifica, $indInscAdultoJoven,
				$morbilidad1, $morbilidad2, $morbilidad3, $idUsuario) {
			if ($indMetodoPlanifica == "-1") {
				$indMetodoPlanifica = "NULL";
			}
			if ($tiempoMetodoPlanifica == "") {
				$tiempoMetodoPlanifica = "NULL";
			}
			if ($indControlesPlanifica == "-1") {
				$indControlesPlanifica = "NULL";
			}
			
			$sql = "CALL pa_modificar_visita_persona_10_52(".$idVisitaPersona.", ".$indSexualActiva.", ".
					$indMetodoPlanifica.", ".$tiempoMetodoPlanifica.", ".$indControlesPlanifica.", '".
					$motivoNoPlanifica."', ".$indInscAdultoJoven.", '".$morbilidad1."', '".$morbilidad2."', '".
					$morbilidad3."', ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function modificarVisitaMujer_10_69($idVisitaPersona, $indCitologia, $indExamenMama,
				$indTetano, $indRubeola, $numeroHijos, $numeroAbortos, $morbilidad1, $morbilidad2,
				$idUsuario) {
			if ($indTetano == "-1") {
				$indTetano = "NULL";
			}
			if ($indRubeola == "-1") {
				$indRubeola = "NULL";
			}
			if ($numeroHijos == "") {
				$numeroHijos = "NULL";
			}
			if ($numeroAbortos == "") {
				$numeroAbortos = "NULL";
			}
			
			$sql = "CALL pa_modificar_visita_mujer_10_69(".$idVisitaPersona.", ".$indCitologia.", ".
					$indExamenMama.", ".$indTetano.", ".$indRubeola.", ".$numeroHijos.", ".$numeroAbortos.", '".
					$morbilidad1."', '".$morbilidad2."', ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function modificarVisitaHombre_40($idVisitaPersona, $indTamizajeProstata, $indAdultoMayor,
				$indHTA, $indDiabetes, $morbilidad1, $morbilidad2, $idUsuario) {
			if ($indTamizajeProstata == "-1") {
				$indTamizajeProstata = "NULL";
			}
			if ($indAdultoMayor == "-1") {
				$indAdultoMayor = "NULL";
			}
			
			$sql = "CALL pa_modificar_visita_hombre_40(".$idVisitaPersona.", ".$indTamizajeProstata.", ".
					$indAdultoMayor.", ".$indHTA.", ".$indDiabetes.", '".$morbilidad1."', '".
					$morbilidad2."', ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function modificarVisitaGPP($idVisitaPersona, $indAcepEmbarazo, $indSuplementos, $indAsistePsicoprof,
				$edadPadre, $idParentescoPadre, $indSedentarismo, $indAsisteControl, $indExamenVIH, $indResultadoVIH,
				$indCesarea, $indAtencionInst, $morbilidad1, $morbilidad2, $morbilidad3, $idUsuario) {
			if ($indAcepEmbarazo == "-1") {
				$indAcepEmbarazo = "NULL";
			}
			if ($indSuplementos == "-1") {
				$indSuplementos = "NULL";
			}
			if ($indAsistePsicoprof == "-1") {
				$indAsistePsicoprof = "NULL";
			}
			if ($edadPadre == "") {
				$edadPadre = "NULL";
			}
			if ($idParentescoPadre == "-1") {
				$idParentescoPadre = "NULL";
			}
			if ($indSedentarismo == "-1") {
				$indSedentarismo = "NULL";
			}
			if ($indAsisteControl == "-1") {
				$indAsisteControl = "NULL";
			}
			if ($indExamenVIH == "-1") {
				$indExamenVIH = "NULL";
			}
			if ($indResultadoVIH == "-1") {
				$indResultadoVIH = "NULL";
			}
			if ($indCesarea == "-1") {
				$indCesarea = "NULL";
			}
			if ($indAtencionInst == "-1") {
				$indAtencionInst = "NULL";
			}
			
			$sql = "CALL pa_modificar_visita_gpp(".$idVisitaPersona.", ".$indAcepEmbarazo.", ".$indSuplementos.", ".
				   $indAsistePsicoprof.", ".$edadPadre.", ".$idParentescoPadre.", ".$indSedentarismo.", ".$indAsisteControl.", ".
				   $indExamenVIH.", ".$indResultadoVIH.", ".$indCesarea.", ".$indAtencionInst.", '".$morbilidad1."', '".
				   $morbilidad2."', '".$morbilidad3."', ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function crearVisitaPersonaServicioInst($idVisitaPersona, $idServicio, $idDetalle, $idUsuario) {
			$sql = "CALL pa_crear_visita_persona_servicio_inst(".$idVisitaPersona.", ".$idServicio.", ".$idDetalle.", ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function borrarVisitaPersonaServicioInst($idVisitaPersonaServ, $idUsuario) {
			$sql = "CALL pa_borrar_visita_persona_servicio_inst(".$idVisitaPersonaServ.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function crearVisitaPersonaProgramaRem($idVisitaPersona, $idPrograma, $idDetalle, $descDetalle, $idUsuario) {
			if ($idDetalle == "-1") {
				$idDetalle = "NULL";
			}
			if ($descDetalle == "") {
				$descDetalle = "NULL";
			} else {
				$descDetalle = "'".$descDetalle."'";
			}
			
			$sql = "CALL pa_crear_visita_persona_programa_rem(".$idVisitaPersona.", ".$idPrograma.", ".$idDetalle.", ".$descDetalle.", ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function borrarVisitaPersonaProgramaRem($idVisitaPersonaProg, $idUsuario) {
			$sql = "CALL pa_borrar_visita_persona_programa_rem(".$idVisitaPersonaProg.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function modificarVisitaPersonaFechaRemision($idVisitaPersona, $fechaRemision, $idUsuario) {
			if ($fechaRemision != "") {
				$fechaRemision = "STR_TO_DATE('".$fechaRemision."', '%d/%m/%Y')";
			} else {
				$fechaRemision = "NULL";
			}
			
			$sql = "CALL pa_modificar_visita_persona_fecha_remision(".$idVisitaPersona.", ".$fechaRemision.", ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function modificarVisitaPersonaEPSRemision($idVisitaPersona, $fechaRemision, $nombreEPSRemision, $idUsuario) {
			if ($fechaRemision != "") {
				$fechaRemision = "STR_TO_DATE('".$fechaRemision."', '%d/%m/%Y')";
			} else {
				$fechaRemision = "NULL";
			}
			
			$sql = "CALL pa_modificar_visita_persona_eps_remision(".$idVisitaPersona.", ".$fechaRemision.", '".$nombreEPSRemision."', ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function modificarVisitaLlamadaSeg($idVisita, $indLlamadaSeg, $idUsuario) {
			$sql = "CALL pa_modificar_visita_llamada_seg(".$idVisita.", ".$indLlamadaSeg.", ".$idUsuario.", @id)";
			echo($sql."<br />");
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function modificarVisitaPersonaProgramaRem($idVisitaPersonaProg, $idResulLlamada, $idUsuario) {
			if ($idResulLlamada == "-1") {
				$idResulLlamada = "NULL";
			}
			
			$sql = "CALL pa_modificar_visita_persona_programa_rem(".$idVisitaPersonaProg.", ".$idResulLlamada.", ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function modificarVisitaFechaActEducativa($idVisita, $fechaActEducativa, $idUsuario) {
			$sql = "CALL pa_modificar_visita_fecha_act_educativa(".$idVisita.", STR_TO_DATE('".
				   $fechaActEducativa."', '%d/%m/%Y'), ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function crearVisitaPersonaActEducativas($idVisitaPersona, $idActEducativa, $idUsuario) {
			$sql = "CALL pa_crear_visita_persona_act_educativas(".$idVisitaPersona.", ".
				   $idActEducativa.", ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function borrarVisitaPersonaActEducativa($idVisitaPersona, $idActEducativa, $idUsuario) {
			$sql = "CALL pa_borrar_visita_persona_act_educativa(".$idVisitaPersona.", ".$idActEducativa.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function asignarVisitaFechaGuiaCrec($idVisita, $idUsuario) {
			$sql = "CALL pa_asignar_visita_fecha_guia_crec(".$idVisita.", ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function crearVisitaGuiaCrecimiento($idVisita, $idGrupoGuia, $textoCompromiso, $idUsuario) {
			$sql = "CALL pa_crear_visita_guia_crecimiento(".$idVisita.", ".$idGrupoGuia.", '".$textoCompromiso."', ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function crearVisitaGuiaCrecimientoDet($idVisita, $idGrupoGuia, $idDetalle, $indSeleccion, $idUsuario) {
			$sql = "CALL pa_crear_visita_guia_crecimiento_det(".$idVisita.", ".$idGrupoGuia.", ".$idDetalle.", ".$indSeleccion.", ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function crearVisitaSeg($fechaVisitaSeg, $idFamilia, $idPersonaEnc, $idPercepcionAlcalde, $observacionPercepcion, $idUsuario) {
			$sql = "CALL pa_crear_visita_seg(STR_TO_DATE('".$fechaVisitaSeg."', '%d/%m/%Y'), ".$idFamilia.", ".
				   $idPersonaEnc.", ".$idPercepcionAlcalde.", '".$observacionPercepcion."', ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function modificarVisitaSeg($idVisitaSeg, $fechaVisitaSeg, $idPersonaEnc, $idPercepcionAlcalde, $observacionPercepcion, $idUsuario) {
			$sql = "CALL pa_modificar_visita_seg(".$idVisitaSeg.", STR_TO_DATE('".$fechaVisitaSeg."', '%d/%m/%Y'), ".
				   $idPersonaEnc.", ".$idPercepcionAlcalde.", '".$observacionPercepcion."', ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function crearVisitaPersonaSeg($idVisitaSeg, $idFamilia, $idPersona, $edad, $unidadEdad, $idTipoDocumento,
				$numDocumento, $fechaUltMenstruacion, $numSemanasGest, $numTrimestreCP, $indCarnetCP, $numControles,
				$idCentroSalud1, $idCentroSalud2, $idCentroSalud3, $idUsuario) {
			if ($numTrimestreCP == "-1") {
				$numTrimestreCP = "NULL";
			}
			if ($fechaUltMenstruacion != "") {
				$fechaUltMenstruacion = "STR_TO_DATE('".$fechaUltMenstruacion."', '%d/%m/%Y')";
			} else {
				$fechaUltMenstruacion = "NULL";
			}
			if ($numSemanasGest == "") {
				$numSemanasGest = "NULL";
			}
			if ($indCarnetCP == "-1") {
				$indCarnetCP = "NULL";
			}
			if ($numControles == "") {
				$numControles = "NULL";
			}
			if ($idCentroSalud1 == "-1") {
				$idCentroSalud1 = "NULL";
			}
			if ($idCentroSalud2 == "-1") {
				$idCentroSalud2 = "NULL";
			}
			if ($idCentroSalud3 == "-1") {
				$idCentroSalud3 = "NULL";
			}
			$sql = "CALL pa_crear_visita_persona_seg(".$idVisitaSeg.", ".$idFamilia.", ".$idPersona.", ".$edad.", '".
				   $unidadEdad."', ".$idTipoDocumento.", UPPER('".$numDocumento."'), ".$fechaUltMenstruacion.", ".
				   $numSemanasGest.", ".$numTrimestreCP.", ".$indCarnetCP.", ".$numControles.", ".$idCentroSalud1.", ".
				   $idCentroSalud2.", ".$idCentroSalud3.", ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function modificarVisitaPersonaProgramaRemInasistencia($idVisitaPersonaProg, $idCausalInasist, $idUsuario) {
			if ($idCausalInasist == "-1") {
				$idCausalInasist = "NULL";
			}
			$sql = "CALL pa_modificar_visita_persona_programa_rem_inasistencia(".$idVisitaPersonaProg.", ".$idCausalInasist.", ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function crearVisitaSegAgente($idVisitaSeg, $indConoceRuta, $indRecicla, $indConsultorioJuridico, $valTema1,
				$valTema2, $valTema3, $valTema4, $valTema5, $valTema6, $valTema7, $idFamilia, $idPersonaEnc, $idUsuario) {
			$sql = "CALL pa_crear_visita_seg_agente(".$idVisitaSeg.", ".$indConoceRuta.", ".$indRecicla.", ".
				   $indConsultorioJuridico.", '".$valTema1."', '".$valTema2."', '".$valTema3."', '".$valTema4."', '".
				   $valTema5."', '".$valTema6."', '".$valTema7."', ".$idFamilia.", ".$idPersonaEnc.", ".$idUsuario.", @id)";
			echo($sql);
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function getVisita($idVisita) {
			try {
				$sql = "SELECT V.*, DATE_FORMAT(V.fecha_visita, '%d/%m/%Y') AS fecha_visita_t, ".
					   "F.id_vivienda, F.telefono, F.celular_1, F.celular_2, VV.direccion, ".
					   "VV.id_comuna, VV.nombre_barrio, C.nombre_comuna, U.nombre_usuario, ".
					   "DATE_FORMAT(V.fecha_llamada_seg, '%d/%m/%Y') AS fecha_llamada_seg_t, ".
					   "DATE_FORMAT(V.fecha_act_educativa, '%d/%m/%Y') AS fecha_act_educativa_t ".
					   "FROM visitas V ".
					   "INNER JOIN familias F ON V.id_familia=F.id_familia ".
					   "INNER JOIN viviendas VV ON F.id_vivienda=VV.id_vivienda ".
					   "INNER JOIN comunas C ON VV.id_comuna=C.id_comuna ".
					   "INNER JOIN usuarios U ON V.id_usuario_crea=U.id_usuario ".
					   "WHERE V.id_visita=".$idVisita;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getVisitaSeg($idVisitaSeg) {
			try {
				$sql = "SELECT V.*, DATE_FORMAT(V.fecha_visita_seg, '%d/%m/%Y') AS fecha_visita_t, ".
					   "F.id_vivienda, F.telefono, F.celular_1, F.celular_2, VV.direccion, ".
					   "VV.id_comuna, VV.nombre_barrio, C.nombre_comuna, U.nombre_usuario ".
					   "FROM visitas_seg V ".
					   "INNER JOIN familias F ON V.id_familia=F.id_familia ".
					   "INNER JOIN viviendas VV ON F.id_vivienda=VV.id_vivienda ".
					   "INNER JOIN comunas C ON VV.id_comuna=C.id_comuna ".
					   "INNER JOIN usuarios U ON V.id_usuario_crea=U.id_usuario ".
					   "WHERE V.id_visita_seg=".$idVisitaSeg;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getVisitaPersona($idVisitaPersona) {
			try {
				$sql = "SELECT VP.*, P.num_documento, P.email, TD.codigo_detalle AS tipo_documento, ".
					   "P.sexo, S.nombre_detalle AS sexo_desc, R.nombre_detalle AS seguridad_social, ".
					   "E.nombre_eps, DATE_FORMAT(VP.fecha_remision, '%d/%m/%Y') AS fecha_remision_2, ".
					   "DATE_FORMAT(V.fecha_visita, '%d/%m/%Y') AS fecha_visita ".
					   "FROM visitas_personas VP ".
					   "INNER JOIN personas P ON VP.id_familia=P.id_familia AND VP.id_persona=P.id_persona ".
					   "INNER JOIN visitas V ON VP.id_visita=V.id_visita ".
					   "LEFT JOIN listas_detalle TD ON P.id_tipo_documento=TD.id_detalle ".
					   "LEFT JOIN listas_detalle S ON P.sexo=S.codigo_detalle AND S.id_lista=1 ".
					   "LEFT JOIN listas_detalle R ON VP.id_seguridad_social=R.id_detalle ".
					   "LEFT JOIN eps E ON VP.cod_eps=E.cod_eps ".
					   "WHERE VP.id_visita_persona=".$idVisitaPersona;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasPersonas($idVisita) {
			try {
				$sql = "SELECT VP.*, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, P.sexo, ".
					   "DATE_FORMAT(P.fecha_nacimiento, '%d/%m/%Y') AS fecha_nacimiento, P.id_parentesco, ".
					   "P.id_tipo_documento, TD.codigo_detalle AS tipo_documento, P.num_documento, ".
					   "P.email, EPS.nombre_eps ".
					   "FROM visitas_personas VP ".
					   "INNER JOIN personas P ON VP.id_familia=P.id_familia AND VP.id_persona=P.id_persona ".
					   "LEFT JOIN listas_detalle TD ON P.id_tipo_documento=TD.id_detalle ".
					   "LEFT JOIN eps EPS ON VP.cod_eps=EPS.cod_eps ".
					   "WHERE VP.id_visita=".$idVisita." ".
					   "ORDER BY VP.id_persona";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasPersonasSeg($idVisita) {
			try {
				$sql = "SELECT VP.*, DATE_FORMAT(VP.fecha_ult_menstruacion, '%d/%m/%Y') AS fecha_ult_menstruacion_t, ".
					   "VPP.id_visita_persona, VPP.edad, VPP.unidad_edad, CS1.nombre_detalle AS centro_salud_1, ".
					   "CS2.nombre_detalle AS centro_salud_2, CS3.nombre_detalle AS centro_salud_3, P.nombre_1, ".
					   "P.nombre_2, P.apellido_1, P.apellido_2, P.sexo, DATE_FORMAT(P.fecha_nacimiento, '%d/%m/%Y') AS fecha_nacimiento, ".
					   "P.id_parentesco, P.id_tipo_documento, TD.codigo_detalle AS tipo_documento, P.num_documento, P.email ".
					   "FROM visitas_personas_seg VP ".
					   "INNER JOIN personas P ON VP.id_familia=P.id_familia AND VP.id_persona=P.id_persona ".
					   "INNER JOIN visitas_seg VS ON VP.id_visita_seg=VS.id_visita_seg ".
					   "INNER JOIN visitas V ON VS.id_visita=V.id_visita ".
					   "INNER JOIN visitas_personas VPP ON V.id_visita=VPP.id_visita AND VP.id_familia=VPP.id_familia AND VP.id_persona=VPP.id_persona ".
					   "LEFT JOIN listas_detalle CS1 ON VP.id_centro_salud_1=CS1.id_detalle ".
					   "LEFT JOIN listas_detalle CS2 ON VP.id_centro_salud_2=CS2.id_detalle ".
					   "LEFT JOIN listas_detalle CS3 ON VP.id_centro_salud_3=CS3.id_detalle ".
					   "LEFT JOIN listas_detalle TD ON P.id_tipo_documento=TD.id_detalle ".
					   "WHERE VP.id_visita_seg=".$idVisita." ".
					   "ORDER BY VP.id_persona";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasBusqueda($textoBuscar, $tipoVisita, $idUsuario) {
			try {
				if ($tipoVisita == "2") {
					$sql = "SELECT P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, TD.nombre_detalle AS tipo_documento, ".
						   "P.num_documento, VV.direccion, VV.nombre_barrio, U.nombre_usuario, V.id_visita_seg AS id_visita, ".
						   "CONCAT('SEGUIMIENTO ', V.orden_visita) AS tipo_visita, DATE_FORMAT(V.fecha_visita_seg, '%d/%m/%Y') AS fecha_visita ".
						   "FROM personas P ".
						   "LEFT JOIN listas_detalle TD ON P.id_tipo_documento=TD.id_detalle ".
						   "INNER JOIN visitas_seg V ON P.id_familia=V.id_familia ".
						   "INNER JOIN familias F ON P.id_familia=F.id_familia ".
						   "INNER JOIN viviendas VV ON F.id_vivienda=VV.id_vivienda ".
						   "INNER JOIN usuarios U ON V.id_usuario_crea=U.id_usuario ".
						   "WHERE (V.id_usuario_crea=".$idUsuario." OR U.id_usuario_coordina=".$idUsuario." ".
						   "OR EXISTS (".
							   "SELECT * FROM usuarios ".
							   "WHERE id_usuario=".$idUsuario." ".
							   "AND id_perfil IN (1, 5)".
						   ") ".
						   "OR EXISTS (".
							   "SELECT UR.* FROM usuarios_relacion UR ".
							   "WHERE UR.id_usuario=".$idUsuario." ".
							   "AND UR.id_usuario_rel=V.id_usuario_crea".
						   ")) ".
						   "AND (LOWER(CONCAT(P.nombre_1, ' ', P.nombre_2, ' ', P.apellido_1, ' ', P.apellido_2)) LIKE LOWER(CONCAT('%', REPLACE('".$textoBuscar."', ' ', '%'), '%')) ".
						   "OR P.num_documento='".$textoBuscar."') ".
						   "ORDER BY V.id_visita_seg, V.fecha_visita_seg ".
						   "LIMIT 10";
				} else {
					$sql = "SELECT P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, TD.nombre_detalle AS tipo_documento, ".
						   "P.num_documento, VV.direccion, VV.nombre_barrio, U.nombre_usuario, V.id_visita, ".
						   "TV.nombre_detalle AS tipo_visita, VS.orden_visita, DATE_FORMAT(V.fecha_visita, '%d/%m/%Y') AS fecha_visita ".
						   "FROM personas P ".
						   "LEFT JOIN listas_detalle TD ON P.id_tipo_documento=TD.id_detalle ".
						   "INNER JOIN visitas V ON P.id_familia=V.id_familia ".
						   "LEFT JOIN visitas_seg VS ON V.id_visita=VS.id_visita ".
						   "INNER JOIN listas_detalle TV ON V.tipo_visita=TV.codigo_detalle AND TV.id_lista=28 ".
						   "INNER JOIN familias F ON P.id_familia=F.id_familia ".
						   "INNER JOIN viviendas VV ON F.id_vivienda=VV.id_vivienda ".
						   "INNER JOIN usuarios U ON V.id_usuario_crea=U.id_usuario ".
						   "WHERE (V.id_usuario_crea=".$idUsuario." OR U.id_usuario_coordina=".$idUsuario." ".
						   "OR EXISTS (".
							   "SELECT * FROM usuarios ".
							   "WHERE id_usuario=".$idUsuario." ".
							   "AND id_perfil IN (1, 5)".
						   ") ".
						   "OR EXISTS (".
							   "SELECT UR.* FROM usuarios_relacion UR ".
							   "WHERE UR.id_usuario=".$idUsuario." ".
							   "AND UR.id_usuario_rel=V.id_usuario_crea".
						   ")) ".
						   "AND (LOWER(CONCAT(P.nombre_1, ' ', P.nombre_2, ' ', P.apellido_1, ' ', P.apellido_2)) LIKE LOWER(CONCAT('%', REPLACE('".$textoBuscar."', ' ', '%'), '%')) ".
						   "OR P.num_documento='".$textoBuscar."') ".
						   "ORDER BY V.id_visita, V.fecha_visita ".
						   "LIMIT 10";
				}
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasFecha($fechaVisita, $idUsuario) {
			try {
				$sql = "SELECT P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, TD.codigo_detalle AS tipo_documento, ".
					   "P.num_documento, VV.direccion, VV.nombre_barrio, F.telefono, F.celular_1, F.celular_2, V.id_visita, ".
					   "TV.nombre_detalle AS tipo_visita, DATE_FORMAT(V.fecha_visita, '%d/%m/%Y') AS fecha_visita, ".
					   "V.ind_llamada_seg, DATE_FORMAT(V.fecha_llamada_seg, '%d/%m/%Y') AS fecha_llamada_seg ".
					   "FROM personas P ".
					   "LEFT JOIN listas_detalle TD ON P.id_tipo_documento=TD.id_detalle ".
					   "INNER JOIN visitas V ON P.id_familia=V.id_familia ".
					   "INNER JOIN listas_detalle TV ON V.tipo_visita=TV.codigo_detalle AND TV.id_lista=28 ".
					   "INNER JOIN familias F ON P.id_familia=F.id_familia ".
					   "INNER JOIN viviendas VV ON F.id_vivienda=VV.id_vivienda ".
					   "INNER JOIN usuarios UC ON V.id_usuario_crea=UC.id_usuario ".
					   "WHERE V.fecha_visita=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ".
					   "AND P.id_persona=1 ".
					   "AND (V.id_usuario_crea=".$idUsuario." ".
					   "OR EXISTS (".
						   "SELECT * FROM usuarios ".
						   "WHERE id_usuario=".$idUsuario." ".
						   "AND id_perfil IN (1, 5)".
					   ") ".
					   "OR EXISTS (".
						   "SELECT UR.* FROM usuarios_relacion UR ".
						   "WHERE UR.id_usuario=".$idUsuario." ".
						   "AND UR.id_usuario_rel=V.id_usuario_crea".
					   ")) ".
					   "ORDER BY V.id_visita";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasPersonas_0_5($idVisita) {
			try {
				$sql = "SELECT P.id_persona, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, P.sexo, ".
					   "VP.edad, VP.unidad_edad, V0.id_visita_persona, V0.ind_cd, V0.ind_lme, V0.ind_carnet, ".
					   "DATE_FORMAT(V0.fecha_bcg, '%d/%m/%Y') AS fecha_bcg, DATE_FORMAT(V0.fecha_hep_b_rn, '%d/%m/%Y') AS fecha_hep_b_rn, ".
					   "DATE_FORMAT(V0.fecha_hep_b_1, '%d/%m/%Y') AS fecha_hep_b_1, DATE_FORMAT(V0.fecha_hep_b_2, '%d/%m/%Y') AS fecha_hep_b_2, ".
					   "DATE_FORMAT(V0.fecha_hep_b_3, '%d/%m/%Y') AS fecha_hep_b_3, DATE_FORMAT(V0.fecha_polio_1, '%d/%m/%Y') AS fecha_polio_1, ".
					   "DATE_FORMAT(V0.fecha_polio_2, '%d/%m/%Y') AS fecha_polio_2, DATE_FORMAT(V0.fecha_polio_3, '%d/%m/%Y') AS fecha_polio_3, ".
					   "DATE_FORMAT(V0.fecha_pentavalente_1, '%d/%m/%Y') AS fecha_pentavalente_1, DATE_FORMAT(V0.fecha_pentavalente_2, '%d/%m/%Y') AS fecha_pentavalente_2, ".
					   "DATE_FORMAT(V0.fecha_pentavalente_3, '%d/%m/%Y') AS fecha_pentavalente_3, DATE_FORMAT(V0.fecha_neumococo_1, '%d/%m/%Y') AS fecha_neumococo_1, ".
					   "DATE_FORMAT(V0.fecha_neumococo_2, '%d/%m/%Y') AS fecha_neumococo_2, DATE_FORMAT(V0.fecha_neumococo_anual, '%d/%m/%Y') AS fecha_neumococo_anual, ".
					   "DATE_FORMAT(V0.fecha_rotavirus_1, '%d/%m/%Y') AS fecha_rotavirus_1, DATE_FORMAT(V0.fecha_rotavirus_2, '%d/%m/%Y') AS fecha_rotavirus_2, ".
					   "DATE_FORMAT(V0.fecha_influenza_1, '%d/%m/%Y') AS fecha_influenza_1, DATE_FORMAT(V0.fecha_influenza_2, '%d/%m/%Y') AS fecha_influenza_2, ".
					   "DATE_FORMAT(V0.fecha_influenza_anual, '%d/%m/%Y') AS fecha_influenza_anual, DATE_FORMAT(V0.fecha_srp, '%d/%m/%Y') AS fecha_srp, ".
					   "DATE_FORMAT(V0.fecha_fa, '%d/%m/%Y') AS fecha_fa, DATE_FORMAT(V0.fecha_tv_1, '%d/%m/%Y') AS fecha_tv_1, ".
					   "DATE_FORMAT(V0.fecha_hep_a, '%d/%m/%Y') AS fecha_hep_a, DATE_FORMAT(V0.fecha_ref_1_dpt, '%d/%m/%Y') AS fecha_ref_1_dpt, ".
					   "DATE_FORMAT(V0.fecha_ref_1_polio, '%d/%m/%Y') AS fecha_ref_1_polio, DATE_FORMAT(V0.fecha_ref_2_dpt, '%d/%m/%Y') AS fecha_ref_2_dpt, ".
					   "DATE_FORMAT(V0.fecha_ref_2_polio, '%d/%m/%Y') AS fecha_ref_2_polio, DATE_FORMAT(V0.fecha_ref_2_tv, '%d/%m/%Y') AS fecha_ref_2_tv, ".
					   "V0.morbilidad_nacer, V0.morbilidad_1, V0.morbilidad_2 ".
					   "FROM visitas_personas_0_5 V0 ".
					   "INNER JOIN visitas_personas VP ON V0.id_visita_persona=VP.id_visita_persona ".
					   "INNER JOIN personas P ON VP.id_familia=P.id_familia AND VP.id_persona=P.id_persona ".
					   "WHERE VP.id_visita=".$idVisita." ".
					   "ORDER BY P.id_persona";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasProgramas_0_5($idVisitaPersona) {
			try {
				$sql = "SELECT * FROM visitas_programas_per_0_5 ".
					   "WHERE id_visita_persona=".$idVisitaPersona." ".
					   "ORDER BY fecha_crea, id_programa";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasPersonas_6_9($idVisita) {
			try {
				$sql = "SELECT P.id_persona, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, P.sexo, VP.edad, VP.unidad_edad, V0.* ".
					   "FROM visitas_personas_6_9 V0 ".
					   "INNER JOIN visitas_personas VP ON V0.id_visita_persona=VP.id_visita_persona ".
					   "INNER JOIN personas P ON VP.id_familia=P.id_familia AND VP.id_persona=P.id_persona ".
					   "WHERE VP.id_visita=".$idVisita." ".
					   "ORDER BY P.id_persona";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasProgramas_6_9($idVisitaPersona) {
			try {
				$sql = "SELECT * FROM visitas_programas_per_6_9 ".
					   "WHERE id_visita_persona=".$idVisitaPersona." ".
					   "ORDER BY fecha_crea, id_programa";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasPersonas_10_52($idVisita) {
			try {
				$sql = "SELECT P.id_persona, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, P.sexo, VP.edad, VP.unidad_edad, V0.* ".
					   "FROM visitas_personas_10_52 V0 ".
					   "INNER JOIN visitas_personas VP ON V0.id_visita_persona=VP.id_visita_persona ".
					   "INNER JOIN personas P ON VP.id_familia=P.id_familia AND VP.id_persona=P.id_persona ".
					   "WHERE VP.id_visita=".$idVisita." ".
					   "ORDER BY P.id_persona";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasProgramas_10_52($idVisitaPersona) {
			try {
				$sql = "SELECT * FROM visitas_programas_per_10_52 ".
					   "WHERE id_visita_persona=".$idVisitaPersona." ".
					   "ORDER BY fecha_crea, id_programa";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasMujeres_10_69($idVisita) {
			try {
				$sql = "SELECT P.id_persona, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, P.sexo, VP.edad, VP.unidad_edad, V0.* ".
					   "FROM visitas_mujeres_10_69 V0 ".
					   "INNER JOIN visitas_personas VP ON V0.id_visita_persona=VP.id_visita_persona ".
					   "INNER JOIN personas P ON VP.id_familia=P.id_familia AND VP.id_persona=P.id_persona ".
					   "WHERE VP.id_visita=".$idVisita." ".
					   "ORDER BY P.id_persona";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasProgramasMuj_10_69($idVisitaPersona) {
			try {
				$sql = "SELECT * FROM visitas_programas_muj_10_69 ".
					   "WHERE id_visita_persona=".$idVisitaPersona." ".
					   "ORDER BY fecha_crea, id_programa";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasHombres_40($idVisita) {
			try {
				$sql = "SELECT P.id_persona, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, P.sexo, VP.edad, VP.unidad_edad, V0.* ".
					   "FROM visitas_hombres_40 V0 ".
					   "INNER JOIN visitas_personas VP ON V0.id_visita_persona=VP.id_visita_persona ".
					   "INNER JOIN personas P ON VP.id_familia=P.id_familia AND VP.id_persona=P.id_persona ".
					   "WHERE VP.id_visita=".$idVisita." ".
					   "ORDER BY P.id_persona";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasProgramasHom_40($idVisitaPersona) {
			try {
				$sql = "SELECT * FROM visitas_programas_hom_40 ".
					   "WHERE id_visita_persona=".$idVisitaPersona." ".
					   "ORDER BY fecha_crea, id_programa";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasMujeresEdadFertilFaltantes($idVisita) {
			try {
				$sql = "SELECT VP.id_visita_persona, P.id_persona, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, P.sexo, VP.edad, VP.unidad_edad ".
					   "FROM visitas_personas VP ".
					   "INNER JOIN personas P ON VP.id_familia=P.id_familia AND VP.id_persona=P.id_persona ".
					   "WHERE VP.id_visita=".$idVisita." ".
					   "AND P.sexo='F' ".
					   "AND VP.unidad_edad='A' ".
					   "AND VP.edad BETWEEN 8 AND 65 ".
					   "AND NOT EXISTS (".
						   "SELECT V2.id_visita_persona ".
						   "FROM visitas_gest_parto_post VG ".
						   "INNER JOIN visitas_personas V2 ON VG.id_visita_persona=V2.id_visita_persona ".
						   "WHERE V2.id_visita_persona=VP.id_visita_persona".
					   ") ".
					   "ORDER BY P.id_persona";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasGPP($idVisita) {
			try {
				$sql = "SELECT VP.id_visita_persona, P.id_persona, P.nombre_1, P.nombre_2, P.apellido_1, ".
					   "P.apellido_2, P.sexo, VP.edad, VP.unidad_edad, V0.ind_acep_embarazo, V0.ind_suplementos, ".
					   "V0.ind_asiste_psicoprof, V0.edad_padre, V0.id_parentesco_padre, V0.ind_sedentarismo, ".
					   "V0.ind_asiste_control, V0.ind_examen_vih, V0.ind_resultado_vih, V0.ind_cesarea, ".
					   "V0.ind_atencion_inst, V0.morbilidad_1, V0.morbilidad_2, V0.morbilidad_3 ".
					   "FROM visitas_gest_parto_post V0 ".
					   "INNER JOIN visitas_personas VP ON V0.id_visita_persona=VP.id_visita_persona ".
					   "INNER JOIN personas P ON VP.id_familia=P.id_familia AND VP.id_persona=P.id_persona ".
					   "WHERE VP.id_visita=".$idVisita." ".
					   "ORDER BY P.id_persona";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasProgramasGPP($idVisitaPersona) {
			try {
				$sql = "SELECT * FROM visitas_programas_gest_parto_post ".
					   "WHERE id_visita_persona=".$idVisitaPersona." ".
					   "ORDER BY fecha_crea, id_programa";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasDeportes($idVisita) {
			try {
				$sql = "SELECT VD.* ".
					   "FROM visitas_deportes VD ".
					   "INNER JOIN visitas_personas VP ON VD.id_visita_persona=VP.id_visita_persona ".
					   "WHERE VP.id_visita=".$idVisita." ".
					   "ORDER BY VD.id_visita_persona, VD.deporte";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasActividadesArte($idVisita) {
			try {
				$sql = "SELECT VA.* ".
					   "FROM visitas_actividades_arte VA ".
					   "INNER JOIN visitas_personas VP ON VA.id_visita_persona=VP.id_visita_persona ".
					   "WHERE VP.id_visita=".$idVisita." ".
					   "ORDER BY VA.id_visita_persona, VA.id_disciplina_arte";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasInstrMusicales($idVisita) {
			try {
				$sql = "SELECT VI.* ".
					   "FROM visitas_instr_musicales VI ".
					   "INNER JOIN visitas_personas VP ON VI.id_visita_persona=VP.id_visita_persona ".
					   "WHERE VP.id_visita=".$idVisita." ".
					   "ORDER BY VI.id_visita_persona, VI.instr_musical";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasPito($idVisita) {
			try {
				$sql = "SELECT VP.id_visita_persona, VP.ind_picado_pito, VP.ind_tratamiento_pito ".
					   "FROM visitas_personas VP ".
					   "WHERE VP.id_visita=".$idVisita." ".
					   "AND VP.ind_picado_pito=1 ".
					   "ORDER BY VP.id_visita_persona";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasSR($idVisita) {
			try {
				$sql = "SELECT VP.id_visita_persona, VP.ind_sintoma_resp, VP.ind_tratamiento_resp ".
					   "FROM visitas_personas VP ".
					   "WHERE VP.id_visita=".$idVisita." ".
					   "AND VP.ind_sintoma_resp=1 ".
					   "ORDER BY VP.id_visita_persona";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasSP($idVisita) {
			try {
				$sql = "SELECT VP.id_visita_persona, VP.ind_sintoma_piel, VP.ind_tratamiento_piel ".
					   "FROM visitas_personas VP ".
					   "WHERE VP.id_visita=".$idVisita." ".
					   "AND VP.ind_sintoma_piel=1 ".
					   "ORDER BY VP.id_visita_persona";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasVIH($idVisita) {
			try {
				$sql = "SELECT VP.id_visita_persona, VP.ind_diag_vih ".
					   "FROM visitas_personas VP ".
					   "WHERE VP.id_visita=".$idVisita." ".
					   "AND VP.ind_diag_vih=1 ".
					   "ORDER BY VP.id_visita_persona";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasAnimales($idVisita) {
			try {
				$sql = "SELECT VA.* ".
					   "FROM visitas_animales VA ".
					   "WHERE VA.id_visita=".$idVisita." ".
					   "ORDER BY VA.tipo_animal";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasPersonasServiciosInst($idVisitaPersona) {
			try {
				$sql = "SELECT * FROM visitas_personas_servicios_inst ".
					   "WHERE id_visita_persona=".$idVisitaPersona." ".
					   "ORDER BY id_servicio, id_detalle";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasPersonasProgramasRem($idVisitaPersona) {
			try {
				$sql = "SELECT * FROM visitas_personas_programas_rem ".
					   "WHERE id_visita_persona=".$idVisitaPersona." ".
					   "ORDER BY id_programa, id_detalle";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasPersonasServiciosInstServ($idVisitaPersona, $idServicio) {
			try {
				$sql = "SELECT * FROM visitas_personas_servicios_inst ".
					   "WHERE id_visita_persona=".$idVisitaPersona." ".
					   "AND id_servicio=".$idServicio." ".
					   "ORDER BY id_detalle";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasPersonasProgramasRemProg($idVisitaPersona, $idPrograma) {
			try {
				$sql = "SELECT * FROM visitas_personas_programas_rem ".
					   "WHERE id_visita_persona=".$idVisitaPersona." ".
					   "AND id_programa=".$idPrograma." ".
					   "ORDER BY id_detalle";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getConsolidadoVisitasFechaUsuario($idUsuarioCoordina, $fechaVisita, $idUsuario, $idPerfil) {
			try {
				$sql = "SELECT DATE_FORMAT(V.fecha_visita, '%d/%m/%Y') AS fecha_visita, V.fecha_visita AS fecha_visita_d, ".
					   "U.id_usuario, U.nombre_usuario, PU.nombre_perfil, COUNT(*) AS cantidad, ".
					   "SUM(CASE WHEN V.id_tipo_familia IS NULL THEN 0 ELSE 1 END) AS cantidad_c, ".
					   "SUM(CASE WHEN V.id_tipo_familia IS NULL THEN 1 ELSE 0 END) AS cantidad_i ".
					   "FROM visitas V ".
					   "INNER JOIN usuarios U ON V.id_usuario_crea=U.id_usuario ".
					   "INNER JOIN perfiles_usuario PU ON U.id_perfil=PU.id_perfil ".
					   "WHERE U.id_usuario_coordina=".$idUsuarioCoordina." ";
				if ($fechaVisita != "") {
					$sql .= "AND V.fecha_visita=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ";
				}
				if ($idUsuario > 0) {
					$sql .= "AND V.id_usuario_crea=".$idUsuario." ";
				}
				if ($idPerfil > 0) {
					$sql .= "AND U.id_perfil=".$idPerfil." ";
				}
				$sql .= "GROUP BY V.fecha_visita, V.id_usuario_crea, U.nombre_usuario, PU.nombre_perfil ".
						"UNION ALL ".
						"SELECT DATE_FORMAT(DATE(PS.fecha_crea), '%d/%m/%Y'), DATE(PS.fecha_crea), ".
						"U.id_usuario, U.nombre_usuario, PU.nombre_perfil, COUNT(*), COUNT(*), 0 ".
						"FROM (".
						    "SELECT VP.id_visita, PS.id_usuario_crea, MAX(DATE(PS.fecha_crea)) AS fecha_crea ".
						    "FROM visitas_personas_servicios_inst PS ".
						    "INNER JOIN visitas_personas VP ON PS.id_visita_persona=VP.id_visita_persona ".
						    "GROUP BY VP.id_visita, PS.id_usuario_crea".
						") PS ".
						"INNER JOIN usuarios U ON PS.id_usuario_crea=U.id_usuario ".
						"INNER JOIN perfiles_usuario PU ON U.id_perfil=PU.id_perfil ".
						"WHERE U.id_usuario_coordina=".$idUsuarioCoordina." ";
				if ($fechaVisita != "") {
					$sql .= "AND DATE(PS.fecha_crea)=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ";
				}
				if ($idUsuario > 0) {
					$sql .= "AND PS.id_usuario_crea=".$idUsuario." ";
				}
				if ($idPerfil > 0) {
					$sql .= "AND U.id_perfil=".$idPerfil." ";
				}
				$sql .= "GROUP BY DATE(PS.fecha_crea), PS.id_usuario_crea, U.nombre_usuario, PU.nombre_perfil ".
						"ORDER BY fecha_visita_d DESC, nombre_usuario";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasProgramasRem($idVisita) {
			try {
				$sql = "SELECT PP.*, PR.nombre_programa, PD.nombre_detalle, P.id_persona, P.nombre_1, P.nombre_2, ".
					   "P.apellido_1, P.apellido_2, P.sexo, S.nombre_detalle AS sexo_desc, VP.edad, VP.unidad_edad ".
					   "FROM visitas_personas VP ".
					   "INNER JOIN visitas_personas_programas_rem PP ON VP.id_visita_persona=PP.id_visita_persona ".
					   "INNER JOIN programas_remision PR ON PP.id_programa=PR.id_programa ".
					   "LEFT JOIN programas_remision_det PD ON PP.id_programa=PD.id_programa AND PP.id_detalle=PD.id_detalle ".
					   "INNER JOIN personas P ON VP.id_familia=P.id_familia AND VP.id_persona=P.id_persona ".
					   "LEFT JOIN listas_detalle S ON P.sexo=S.codigo_detalle AND S.id_lista=1 ".
					   "WHERE VP.id_visita=".$idVisita." ".
					   "ORDER BY P.id_persona, PR.orden, PD.orden";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasProgramasRem2($idVisita) {
			try {
				$sql = "SELECT PP.*, PR.nombre_programa, PD.nombre_detalle, P.id_persona, P.nombre_1, P.nombre_2, ".
					   "P.apellido_1, P.apellido_2, P.sexo, S.nombre_detalle AS sexo_desc, VP.edad, VP.unidad_edad ".
					   "FROM visitas_personas VP ".
					   "INNER JOIN visitas_personas_programas_rem PP ON VP.id_visita_persona=PP.id_visita_persona ".
					   "INNER JOIN programas_remision PR ON PP.id_programa=PR.id_programa ".
					   "LEFT JOIN programas_remision_det PD ON PP.id_programa=PD.id_programa AND PP.id_detalle=PD.id_detalle ".
					   "INNER JOIN personas P ON VP.id_familia=P.id_familia AND VP.id_persona=P.id_persona ".
					   "LEFT JOIN listas_detalle S ON P.sexo=S.codigo_detalle AND S.id_lista=1 ".
					   "WHERE VP.id_visita=".$idVisita." ".
					   "ORDER BY P.id_persona, PR.id_programa, PD.id_detalle";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasUsuarioFecha($idUsuario, $fechaVisita) {
			try {
				$sql = "SELECT V.id_visita, TV.nombre_detalle AS tipo_visita, P.nombre_1, P.nombre_2, P.apellido_1, ".
					   "P.apellido_2, P.sexo, S.nombre_detalle AS sexo_desc, VP.edad, VP.unidad_edad, 'Encuesta' AS tipo, ".
					   "CASE WHEN V.id_tipo_familia IS NULL THEN 0 ELSE 1 END AS ind_completo ".
					   "FROM visitas V ".
					   "INNER JOIN listas_detalle TV ON V.tipo_visita=TV.codigo_detalle AND TV.id_lista=28 ".
					   "INNER JOIN visitas_personas VP ON V.id_visita=VP.id_visita ".
					   "INNER JOIN personas P ON VP.id_familia=P.id_familia AND VP.id_persona=P.id_persona ".
					   "INNER JOIN listas_detalle S ON P.sexo=S.codigo_detalle AND S.id_lista=1 ".
					   "WHERE V.id_usuario_crea=".$idUsuario." ".
					   "AND V.fecha_visita=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ".
					   "AND VP.id_persona=1 ".
					   "UNION ALL ".
					   "SELECT V.id_visita, TV.nombre_detalle, P.nombre_1, P.nombre_2, P.apellido_1, ".
					   "P.apellido_2, P.sexo, S.nombre_detalle, VP.edad, VP.unidad_edad, 'Servicios Institucionales', 1 ".
					   "FROM (".
						   "SELECT VP.id_visita, PS.id_usuario_crea, MAX(DATE(PS.fecha_crea)) AS fecha_crea ".
						   "FROM visitas_personas_servicios_inst PS ".
						   "INNER JOIN visitas_personas VP ON PS.id_visita_persona=VP.id_visita_persona ".
						   "GROUP BY VP.id_visita, PS.id_usuario_crea".
					   ") PS ".
					   "INNER JOIN visitas V ON PS.id_visita=V.id_visita ".
					   "INNER JOIN visitas_personas VP ON V.id_visita=VP.id_visita ".
					   "INNER JOIN listas_detalle TV ON V.tipo_visita=TV.codigo_detalle AND TV.id_lista=28 ".
					   "INNER JOIN personas P ON VP.id_familia=P.id_familia AND VP.id_persona=P.id_persona ".
					   "INNER JOIN listas_detalle S ON P.sexo=S.codigo_detalle AND S.id_lista=1 ".
					   "WHERE PS.id_usuario_crea=".$idUsuario." ".
					   "AND PS.fecha_crea=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ".
					   "AND VP.id_persona=1 ".
					   "ORDER BY id_visita";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasPersonasActEducativas($idVisita) {
			try {
				$sql = "SELECT VA.*, TD.codigo_detalle AS tipo_documento, P.num_documento, EPS.nombre_eps ".
					   "FROM visitas_personas_act_educativas VA ".
					   "INNER JOIN visitas_personas VP ON VA.id_visita_persona=VP.id_visita_persona ".
					   "INNER JOIN personas P ON VP.id_familia=P.id_familia AND VP.id_persona=P.id_persona ".
					   "LEFT JOIN listas_detalle TD ON P.id_tipo_documento=TD.id_detalle ".
					   "LEFT JOIN eps EPS ON VP.cod_eps=EPS.cod_eps ".
					   "WHERE VP.id_visita=".$idVisita." ".
					   "ORDER BY VA.id_visita_persona, VA.id_act_educativa";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasGuiasCrecimiento($idVisita) {
			try {
				$sql = "SELECT VG.* ".
					   "FROM visitas_guias_crecimiento VG ".
					   "INNER JOIN guias_crecimiento GC ON VG.id_grupo_guia=GC.id_grupo_guia ".
					   "WHERE VG.id_visita=".$idVisita." ".
					   "AND GC.ind_activo=1 ".
					   "ORDER BY GC.orden";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasGuiasCrecimientoDet($idVisita) {
			try {
				$sql = "SELECT VD.* ".
					   "FROM visitas_guias_crecimiento_det VD ".
					   "INNER JOIN guias_crecimiento GC ON VD.id_grupo_guia=GC.id_grupo_guia ".
					   "INNER JOIN guias_crecimiento_det GD ON VD.id_grupo_guia=GD.id_grupo_guia AND VD.id_detalle=GD.id_detalle ".
					   "WHERE VD.id_visita=".$idVisita." ".
					   "AND GC.ind_activo=1 ".
					   "ORDER BY GC.orden, GD.orden";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getConsolidadoVisitasAuxiliares($idUsuarioCoordina, $fechaIni, $fechaFin, $idUsuario, $idPerfil) {
			try {
				$sql = "SELECT DATE_FORMAT(V.fecha_visita, '%d/%m/%Y') AS fecha_visita, U.id_usuario, U.nombre_usuario, ".
					   "SUM(CASE V.tipo_visita WHEN 1 THEN 1 ELSE 0 END) AS cantidad, ".
					   "SUM(CASE V.tipo_visita WHEN 1 THEN CASE WHEN V.id_tipo_familia IS NULL THEN 0 ELSE 1 END ELSE 0 END) AS cantidad_c, ".
					   "SUM(CASE V.tipo_visita WHEN 1 THEN CASE WHEN V.id_tipo_familia IS NULL THEN 1 ELSE 0 END ELSE 0 END) AS cantidad_i, ".
					   "SUM(CASE V.tipo_visita WHEN 1 THEN CASE WHEN RM.id_visita IS NULL THEN 0 ELSE 1 END ELSE 0 END) AS cantidad_rem, ".
					   "SUM(CASE V.tipo_visita WHEN 1 THEN CASE WHEN V.fecha_act_educativa IS NULL THEN 0 ELSE 1 END ELSE 0 END) AS cantidad_act_ed, ".
					   "SUM(CASE V.tipo_visita WHEN 2 THEN 1 ELSE 0 END) AS cantidad_seg, ".
					   "SUM(CASE V.tipo_visita WHEN 2 THEN CASE WHEN RM.id_visita IS NULL THEN 0 ELSE 1 END ELSE 0 END) AS cantidad_seg_rem, ".
					   "SUM(CASE V.tipo_visita WHEN 2 THEN CASE WHEN V.fecha_act_educativa IS NULL THEN 0 ELSE 1 END ELSE 0 END) AS cantidad_seg_act_ed ".
					   "FROM visitas V ".
					   "INNER JOIN usuarios U ON V.id_usuario_crea=U.id_usuario ".
					   "LEFT JOIN (".
						   "SELECT VP.id_visita ".
						   "FROM visitas_personas_programas_rem PR ".
						   "INNER JOIN visitas_personas VP ON PR.id_visita_persona=VP.id_visita_persona ".
						   "GROUP BY VP.id_visita".
					   ") RM ON V.id_visita=RM.id_visita ".
					   "WHERE U.id_usuario_coordina=".$idUsuarioCoordina." ".
					   "AND V.fecha_visita BETWEEN STR_TO_DATE('".$fechaIni."', '%d/%m/%Y') AND STR_TO_DATE('".$fechaFin."', '%d/%m/%Y') ";
				if ($idUsuario > 0) {
					$sql .= "AND V.id_usuario_crea=".$idUsuario." ";
				}
				if ($idPerfil > 0) {
					$sql .= "AND U.id_perfil=".$idPerfil." ";
				}
				$sql .= "GROUP BY V.fecha_visita, V.id_usuario_crea, U.nombre_usuario ".
						"ORDER BY V.fecha_visita, U.nombre_usuario";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getConsolidadoVisitasAuxiliaresTotales($fechaIni, $fechaFin) {
			try {
				$sql = "SELECT SUM(CASE V.tipo_visita WHEN 1 THEN 1 ELSE 0 END) AS cantidad, ".
					   "SUM(CASE V.tipo_visita WHEN 1 THEN CASE WHEN V.id_tipo_familia IS NULL THEN 0 ELSE 1 END ELSE 0 END) AS cantidad_c, ".
					   "SUM(CASE V.tipo_visita WHEN 1 THEN CASE WHEN V.id_tipo_familia IS NULL THEN 1 ELSE 0 END ELSE 0 END) AS cantidad_i, ".
					   "SUM(CASE V.tipo_visita WHEN 1 THEN CASE WHEN RM.id_visita IS NULL THEN 0 ELSE 1 END ELSE 0 END) AS cantidad_rem, ".
					   "SUM(CASE V.tipo_visita WHEN 1 THEN CASE WHEN V.fecha_act_educativa IS NULL THEN 0 ELSE 1 END ELSE 0 END) AS cantidad_act_ed, ".
					   "SUM(CASE V.tipo_visita WHEN 2 THEN 1 ELSE 0 END) AS cantidad_seg, ".
					   "SUM(CASE V.tipo_visita WHEN 2 THEN CASE WHEN RM.id_visita IS NULL THEN 0 ELSE 1 END ELSE 0 END) AS cantidad_seg_rem, ".
					   "SUM(CASE V.tipo_visita WHEN 2 THEN CASE WHEN V.fecha_act_educativa IS NULL THEN 0 ELSE 1 END ELSE 0 END) AS cantidad_seg_act_ed ".
					   "FROM visitas V ".
					   "LEFT JOIN (".
						   "SELECT VP.id_visita ".
						   "FROM visitas_personas_programas_rem PR ".
						   "INNER JOIN visitas_personas VP ON PR.id_visita_persona=VP.id_visita_persona ".
						   "GROUP BY VP.id_visita".
					   ") RM ON V.id_visita=RM.id_visita ".
					   "WHERE V.fecha_visita BETWEEN STR_TO_DATE('".$fechaIni."', '%d/%m/%Y') AND STR_TO_DATE('".$fechaFin."', '%d/%m/%Y') ".
					   "AND V.id_familia>10000";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getConsolidadoVisitasAgentes($idUsuarioCoordina, $fechaIni, $fechaFin, $idUsuario, $idPerfil) {
			try {
				$sql = "SELECT DATE_FORMAT(T.fecha_visita, '%d/%m/%Y') AS fecha_visita, U.id_usuario, U.nombre_usuario, ".
					   "SUM(T.cantidad_serv_inst) AS cantidad_serv_inst, SUM(T.cantidad_gcf) AS cantidad_gcf, ".
					   "SUM(cantidad_seg_ag) AS cantidad_seg_ag, SUM(T.cantidad_seg_serv_inst) AS cantidad_seg_serv_inst, ".
					   "SUM(T.cantidad_seg_gcf) AS cantidad_seg_gcf ".
					   "FROM (".
						   "SELECT V.fecha_visita, SA.id_usuario_crea, 0 AS cantidad_serv_inst, 0 AS cantidad_gcf, ".
						   "COUNT(*) AS cantidad_seg_ag, 0 AS cantidad_seg_serv_inst, 0 AS cantidad_seg_gcf ".
						   "FROM visitas_seg_agentes SA ".
						   "INNER JOIN visitas_seg VS ON SA.id_visita_seg=VS.id_visita_seg ".
						   "INNER JOIN visitas V ON VS.id_visita=V.id_visita ".
						   "WHERE V.fecha_visita BETWEEN STR_TO_DATE('".$fechaIni."', '%d/%m/%Y') AND STR_TO_DATE('".$fechaFin."', '%d/%m/%Y') ".
						   "GROUP BY V.fecha_visita, SA.id_usuario_crea ".
						   "UNION ALL ".
						   "SELECT T.fecha_visita, T.id_usuario_crea, SUM(CASE T.tipo_visita WHEN 1 THEN 1 ELSE 0 END), 0, 0, ".
						   "SUM(CASE T.tipo_visita WHEN 2 THEN 1 ELSE 0 END), 0 ".
						   "FROM (".
							   "SELECT V.id_visita, V.fecha_visita, PR.id_usuario_crea, V.tipo_visita ".
							   "FROM visitas_personas_servicios_inst PR ".
							   "INNER JOIN visitas_personas VP ON PR.id_visita_persona=VP.id_visita_persona ".
							   "INNER JOIN visitas V ON VP.id_visita=V.id_visita ".
							   "WHERE V.fecha_visita BETWEEN STR_TO_DATE('".$fechaIni."', '%d/%m/%Y') AND STR_TO_DATE('".$fechaFin."', '%d/%m/%Y') ".
							   "GROUP BY V.id_visita, V.fecha_visita, PR.id_usuario_crea, V.tipo_visita".
						   ") T ".
						   "GROUP BY T.fecha_visita, T.id_usuario_crea ".
						   "UNION ALL ".
						   "SELECT T.fecha_visita, T.id_usuario_crea, 0, SUM(CASE T.tipo_visita WHEN 1 THEN 1 ELSE 0 END), 0, 0, ".
						   "SUM(CASE T.tipo_visita WHEN 2 THEN 1 ELSE 0 END) ".
						   "FROM (".
							   "SELECT V.id_visita, V.fecha_visita, GC.id_usuario_crea, V.tipo_visita ".
							   "FROM visitas_guias_crecimiento GC ".
							   "INNER JOIN visitas V ON GC.id_visita=V.id_visita ".
							   "WHERE V.fecha_visita BETWEEN STR_TO_DATE('".$fechaIni."', '%d/%m/%Y') AND STR_TO_DATE('".$fechaFin."', '%d/%m/%Y') ".
							   "GROUP BY V.id_visita, V.fecha_visita, GC.id_usuario_crea, V.tipo_visita".
						   ") T ".
						   "GROUP BY T.fecha_visita, T.id_usuario_crea".
					   ") T ".
					   "INNER JOIN usuarios U ON T.id_usuario_crea=U.id_usuario ".
					   "WHERE U.id_usuario_coordina=".$idUsuarioCoordina." ";
				if ($idUsuario > 0) {
					$sql .= "AND U.id_usuario=".$idUsuario." ";
				}
				if ($idPerfil > 0) {
					$sql .= "AND U.id_perfil=".$idPerfil." ";
				}
				$sql .= "GROUP BY T.fecha_visita, U.id_usuario, U.nombre_usuario ".
						"ORDER BY T.fecha_visita, U.nombre_usuario";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getConsolidadoVisitasAgentesTotales($fechaIni, $fechaFin) {
			try {
				$sql = "SELECT SUM(T.cantidad_serv_inst) AS cantidad_serv_inst, SUM(T.cantidad_gcf) AS cantidad_gcf, ".
					   "SUM(cantidad_seg_ag) AS cantidad_seg_ag, SUM(T.cantidad_seg_serv_inst) AS cantidad_seg_serv_inst, ".
					   "SUM(T.cantidad_seg_gcf) AS cantidad_seg_gcf ".
					   "FROM (".
						   "SELECT 0 AS cantidad_serv_inst, 0 AS cantidad_gcf, COUNT(*) AS cantidad_seg_ag, ".
						   "0 AS cantidad_seg_serv_inst, 0 AS cantidad_seg_gcf ".
						   "FROM visitas_seg_agentes SA ".
						   "INNER JOIN visitas_seg VS ON SA.id_visita_seg=VS.id_visita_seg ".
						   "INNER JOIN visitas V ON VS.id_visita=V.id_visita ".
						   "WHERE V.fecha_visita BETWEEN STR_TO_DATE('".$fechaIni."', '%d/%m/%Y') AND STR_TO_DATE('".$fechaFin."', '%d/%m/%Y') ".
						   "AND V.id_familia>10000 ".
						   "UNION ALL ".
						   "SELECT SUM(CASE T.tipo_visita WHEN 1 THEN 1 ELSE 0 END), 0, 0, ".
						   "SUM(CASE T.tipo_visita WHEN 2 THEN 1 ELSE 0 END), 0 ".
						   "FROM (".
							   "SELECT V.id_visita, V.tipo_visita ".
							   "FROM visitas_personas_servicios_inst PR ".
							   "INNER JOIN visitas_personas VP ON PR.id_visita_persona=VP.id_visita_persona ".
							   "INNER JOIN visitas V ON VP.id_visita=V.id_visita ".
							   "WHERE V.fecha_visita BETWEEN STR_TO_DATE('".$fechaIni."', '%d/%m/%Y') AND STR_TO_DATE('".$fechaFin."', '%d/%m/%Y') ".
							   "AND V.id_familia>10000 ".
							   "GROUP BY V.id_visita, V.tipo_visita".
						   ") T ".
						   "UNION ALL ".
						   "SELECT 0, SUM(CASE T.tipo_visita WHEN 1 THEN 1 ELSE 0 END), 0, 0, ".
						   "SUM(CASE T.tipo_visita WHEN 2 THEN 1 ELSE 0 END) ".
						   "FROM (".
							   "SELECT V.id_visita, V.tipo_visita ".
							   "FROM visitas_guias_crecimiento GC ".
							   "INNER JOIN visitas V ON GC.id_visita=V.id_visita ".
							   "WHERE V.fecha_visita BETWEEN STR_TO_DATE('".$fechaIni."', '%d/%m/%Y') AND STR_TO_DATE('".$fechaFin."', '%d/%m/%Y') ".
							   "AND V.id_familia>10000 ".
							   "GROUP BY V.id_visita, V.tipo_visita".
						   ") T".
					   ") T";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getConsolidadoVisitasAuxiliaresCrea($idUsuarioCoordina, $fechaIni, $fechaFin, $idUsuario, $idPerfil) {
			try {
				$sql = "SELECT fecha_crea, id_usuario, nombre_usuario, SUM(cantidad) AS cantidad, ".
					   "SUM(cantidad_c) AS cantidad_c, SUM(cantidad_i) AS cantidad_i, ".
					   "SUM(cantidad_rem) AS cantidad_rem, SUM(cantidad_act_ed) AS cantidad_act_ed, ".
					   "SUM(cantidad_seg) AS cantidad_seg, SUM(cantidad_seg_rem) AS cantidad_seg_rem, ".
					   "SUM(cantidad_seg_act_ed) AS cantidad_seg_act_ed ".
					   "FROM (".
					   "SELECT DATE_FORMAT(DATE(V.fecha_crea), '%d/%m/%Y') AS fecha_crea, U.id_usuario, U.nombre_usuario, ".
					   "SUM(CASE V.tipo_visita WHEN 1 THEN 1 ELSE 0 END) AS cantidad, ".
					   "SUM(CASE V.tipo_visita WHEN 1 THEN CASE WHEN V.id_tipo_familia IS NULL THEN 0 ELSE 1 END ELSE 0 END) AS cantidad_c, ".
					   "SUM(CASE V.tipo_visita WHEN 1 THEN CASE WHEN V.id_tipo_familia IS NULL THEN 1 ELSE 0 END ELSE 0 END) AS cantidad_i, ".
					   "0 AS cantidad_rem, 0 AS cantidad_act_ed, SUM(CASE V.tipo_visita WHEN 2 THEN 1 ELSE 0 END) AS cantidad_seg, ".
					   "0 AS cantidad_seg_rem, 0 AS cantidad_seg_act_ed, DATE(V.fecha_crea) AS fecha_crea_a ".
					   "FROM visitas V ".
					   "INNER JOIN usuarios U ON V.id_usuario_crea=U.id_usuario ".
					   "WHERE U.id_usuario_coordina=".$idUsuarioCoordina." ".
					   "AND DATE(V.fecha_crea) BETWEEN STR_TO_DATE('".$fechaIni."', '%d/%m/%Y') AND STR_TO_DATE('".$fechaFin."', '%d/%m/%Y') ";
				if ($idUsuario > 0) {
					$sql .= "AND V.id_usuario_crea=".$idUsuario." ";
				}
				if ($idPerfil > 0) {
					$sql .= "AND U.id_perfil=".$idPerfil." ";
				}
				$sql .= "GROUP BY DATE(V.fecha_crea), V.id_usuario_crea, U.nombre_usuario ".
						"UNION ALL ".
						"SELECT DATE_FORMAT(DATE(RM.fecha_crea), '%d/%m/%Y'), ".
						"U.id_usuario, U.nombre_usuario, 0, 0, 0, ".
						"SUM(CASE V.tipo_visita WHEN 1 THEN CASE WHEN RM.id_visita IS NULL THEN 0 ELSE 1 END ELSE 0 END), 0, 0, ".
						"SUM(CASE V.tipo_visita WHEN 2 THEN CASE WHEN RM.id_visita IS NULL THEN 0 ELSE 1 END ELSE 0 END), 0, DATE(RM.fecha_crea) ".
						"FROM visitas V ".
						"INNER JOIN usuarios U ON V.id_usuario_crea=U.id_usuario ".
						"LEFT JOIN (".
							"SELECT VP.id_visita, MIN(PR.fecha_crea) AS fecha_crea ".
							"FROM visitas_personas_programas_rem PR ".
							"INNER JOIN visitas_personas VP ON PR.id_visita_persona=VP.id_visita_persona ".
							"GROUP BY VP.id_visita ".
						") RM ON V.id_visita=RM.id_visita ".
						"WHERE U.id_usuario_coordina=".$idUsuarioCoordina." ".
						"AND DATE(RM.fecha_crea) BETWEEN STR_TO_DATE('".$fechaIni."', '%d/%m/%Y') AND STR_TO_DATE('".$fechaFin."', '%d/%m/%Y') ";
				if ($idUsuario > 0) {
					$sql .= "AND V.id_usuario_crea=".$idUsuario." ";
				}
				if ($idPerfil > 0) {
					$sql .= "AND U.id_perfil=".$idPerfil." ";
				}
				$sql .= "GROUP BY DATE(RM.fecha_crea), V.id_usuario_crea, U.nombre_usuario ".
						"UNION ALL ".
						"SELECT DATE_FORMAT(DATE(VA.fecha_crea), '%d/%m/%Y'), ".
						"U.id_usuario, U.nombre_usuario, 0, 0, 0, 0, ".
						"SUM(CASE V.tipo_visita WHEN 1 THEN CASE WHEN VA.id_visita IS NULL THEN 0 ELSE 1 END ELSE 0 END), 0, 0, ".
						"SUM(CASE V.tipo_visita WHEN 2 THEN CASE WHEN VA.id_visita IS NULL THEN 0 ELSE 1 END ELSE 0 END), DATE(VA.fecha_crea) ".
						"FROM visitas V ".
						"INNER JOIN usuarios U ON V.id_usuario_crea=U.id_usuario ".
						"LEFT JOIN (".
							"SELECT VP.id_visita, MIN(AE.fecha_crea) AS fecha_crea ".
							"FROM visitas_personas_act_educativas AE ".
							"INNER JOIN visitas_personas VP ON AE.id_visita_persona=VP.id_visita_persona ".
							"GROUP BY VP.id_visita".
						") VA ON V.id_visita=VA.id_visita ".
						"WHERE U.id_usuario_coordina=".$idUsuarioCoordina." ".
						"AND DATE(VA.fecha_crea) BETWEEN STR_TO_DATE('".$fechaIni."', '%d/%m/%Y') AND STR_TO_DATE('".$fechaFin."', '%d/%m/%Y') ";
				if ($idUsuario > 0) {
					$sql .= "AND V.id_usuario_crea=".$idUsuario." ";
				}
				if ($idPerfil > 0) {
					$sql .= "AND U.id_perfil=".$idPerfil." ";
				}
				$sql .= "GROUP BY DATE(VA.fecha_crea), V.id_usuario_crea, U.nombre_usuario".
						") T ".
						"GROUP BY fecha_crea, id_usuario, nombre_usuario, fecha_crea_a ".
						"ORDER BY fecha_crea_a, nombre_usuario";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getConsolidadoVisitasAuxiliaresCreaTotales($fechaIni, $fechaFin) {
			try {
				$sql = "SELECT SUM(cantidad) AS cantidad, SUM(cantidad_c) AS cantidad_c, SUM(cantidad_i) AS cantidad_i, ".
					   "SUM(cantidad_rem) AS cantidad_rem, SUM(cantidad_act_ed) AS cantidad_act_ed, ".
					   "SUM(cantidad_seg) AS cantidad_seg, SUM(cantidad_seg_rem) AS cantidad_seg_rem, ".
					   "SUM(cantidad_seg_act_ed) AS cantidad_seg_act_ed ".
					   "FROM (".
						   "SELECT SUM(CASE V.tipo_visita WHEN 1 THEN 1 ELSE 0 END) AS cantidad, ".
						   "SUM(CASE V.tipo_visita WHEN 1 THEN CASE WHEN V.id_tipo_familia IS NULL THEN 0 ELSE 1 END ELSE 0 END) AS cantidad_c, ".
						   "SUM(CASE V.tipo_visita WHEN 1 THEN CASE WHEN V.id_tipo_familia IS NULL THEN 1 ELSE 0 END ELSE 0 END) AS cantidad_i, ".
						   "0 AS cantidad_rem, 0 AS cantidad_act_ed, SUM(CASE V.tipo_visita WHEN 2 THEN 1 ELSE 0 END) AS cantidad_seg, ".
						   "0 AS cantidad_seg_rem, 0 AS cantidad_seg_act_ed ".
						   "FROM visitas V ".
						   "WHERE DATE(V.fecha_crea) BETWEEN STR_TO_DATE('".$fechaIni."', '%d/%m/%Y') AND STR_TO_DATE('".$fechaFin."', '%d/%m/%Y') ".
						   "AND V.id_familia>10000 ".
						   "UNION ALL ".
						   "SELECT 0, 0, 0, ".
						   "SUM(CASE V.tipo_visita WHEN 1 THEN CASE WHEN RM.id_visita IS NULL THEN 0 ELSE 1 END ELSE 0 END), 0, 0, ".
						   "SUM(CASE V.tipo_visita WHEN 2 THEN CASE WHEN RM.id_visita IS NULL THEN 0 ELSE 1 END ELSE 0 END), 0 ".
						   "FROM visitas V ".
						   "LEFT JOIN (".
							   "SELECT VP.id_visita, MIN(PR.fecha_crea) AS fecha_crea ".
							   "FROM visitas_personas_programas_rem PR ".
							   "INNER JOIN visitas_personas VP ON PR.id_visita_persona=VP.id_visita_persona ".
							   "GROUP BY VP.id_visita ".
						   ") RM ON V.id_visita=RM.id_visita ".
						   "WHERE DATE(RM.fecha_crea) BETWEEN STR_TO_DATE('".$fechaIni."', '%d/%m/%Y') AND STR_TO_DATE('".$fechaFin."', '%d/%m/%Y') ".
						   "AND V.id_familia>10000 ".
						   "UNION ALL ".
						   "SELECT 0, 0, 0, 0, ".
						   "SUM(CASE V.tipo_visita WHEN 1 THEN CASE WHEN VA.id_visita IS NULL THEN 0 ELSE 1 END ELSE 0 END), 0, 0, ".
						   "SUM(CASE V.tipo_visita WHEN 2 THEN CASE WHEN VA.id_visita IS NULL THEN 0 ELSE 1 END ELSE 0 END) ".
						   "FROM visitas V ".
						   "LEFT JOIN (".
							   "SELECT VP.id_visita, MIN(AE.fecha_crea) AS fecha_crea ".
							   "FROM visitas_personas_act_educativas AE ".
							   "INNER JOIN visitas_personas VP ON AE.id_visita_persona=VP.id_visita_persona ".
							   "GROUP BY VP.id_visita".
						   ") VA ON V.id_visita=VA.id_visita ".
						   "WHERE DATE(VA.fecha_crea) BETWEEN STR_TO_DATE('".$fechaIni."', '%d/%m/%Y') AND STR_TO_DATE('".$fechaFin."', '%d/%m/%Y') ".
						   "AND V.id_familia>10000".
					   ") T";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getConsolidadoVisitasAgentesCrea($idUsuarioCoordina, $fechaIni, $fechaFin, $idUsuario, $idPerfil) {
			try {
				$sql = "SELECT DATE_FORMAT(T.fecha_crea, '%d/%m/%Y') AS fecha_crea, U.id_usuario, U.nombre_usuario, ".
					   "SUM(T.cantidad_serv_inst) AS cantidad_serv_inst, SUM(T.cantidad_gcf) AS cantidad_gcf, ".
					   "SUM(T.cantidad_seg_ag) AS cantidad_seg_ag, SUM(T.cantidad_seg_serv_inst) AS cantidad_seg_serv_inst, ".
					   "SUM(T.cantidad_seg_gcf) AS cantidad_seg_gcf ".
					   "FROM (".
						   "SELECT DATE(SA.fecha_crea) AS fecha_crea, SA.id_usuario_crea, 0 AS cantidad_serv_inst, 0 AS cantidad_gcf, ".
						   "COUNT(*) AS cantidad_seg_ag, 0 AS cantidad_seg_serv_inst, 0 AS cantidad_seg_gcf ".
						   "FROM visitas_seg_agentes SA ".
						   "WHERE DATE(SA.fecha_crea) BETWEEN STR_TO_DATE('".$fechaIni."', '%d/%m/%Y') AND STR_TO_DATE('".$fechaFin."', '%d/%m/%Y') ".
						   "GROUP BY DATE(SA.fecha_crea), SA.id_usuario_crea ".
						   "UNION ALL ".
						   "SELECT T.fecha_crea, T.id_usuario_crea, SUM(CASE T.tipo_visita WHEN 1 THEN 1 ELSE 0 END), 0, 0, ".
						   "SUM(CASE T.tipo_visita WHEN 2 THEN 1 ELSE 0 END), 0 ".
						   "FROM (".
							   "SELECT V.id_visita, MIN(DATE(PR.fecha_crea)) AS fecha_crea, MIN(PR.id_usuario_crea) AS id_usuario_crea, V.tipo_visita ".
							   "FROM visitas_personas_servicios_inst PR ".
							   "INNER JOIN visitas_personas VP ON PR.id_visita_persona=VP.id_visita_persona ".
							   "INNER JOIN visitas V ON VP.id_visita=V.id_visita ".
							   "WHERE DATE(PR.fecha_crea) BETWEEN STR_TO_DATE('".$fechaIni."', '%d/%m/%Y') AND STR_TO_DATE('".$fechaFin."', '%d/%m/%Y') ".
							   "GROUP BY V.id_visita, V.tipo_visita".
						   ") T ".
						   "GROUP BY T.fecha_crea, T.id_usuario_crea ".
						   "UNION ALL ".
						   "SELECT T.fecha_crea, T.id_usuario_crea, 0, SUM(CASE T.tipo_visita WHEN 1 THEN 1 ELSE 0 END), 0, 0, ".
						   "SUM(CASE T.tipo_visita WHEN 2 THEN 1 ELSE 0 END) ".
						   "FROM (".
							   "SELECT GC.id_visita, MIN(DATE(GC.fecha_crea)) AS fecha_crea, MIN(GC.id_usuario_crea) AS id_usuario_crea, V.tipo_visita ".
							   "FROM visitas_guias_crecimiento GC ".
							   "INNER JOIN visitas V ON GC.id_visita=V.id_visita ".
							   "WHERE DATE(GC.fecha_crea) BETWEEN STR_TO_DATE('".$fechaIni."', '%d/%m/%Y') AND STR_TO_DATE('".$fechaFin."', '%d/%m/%Y') ".
							   "GROUP BY GC.id_visita, V.tipo_visita".
						   ") T ".
						   "GROUP BY T.fecha_crea, T.id_usuario_crea".
					   ") T ".
					   "INNER JOIN usuarios U ON T.id_usuario_crea=U.id_usuario ".
					   "WHERE U.id_usuario_coordina=".$idUsuarioCoordina." ";
				if ($idUsuario > 0) {
					$sql .= "AND U.id_usuario=".$idUsuario." ";
				}
				if ($idPerfil > 0) {
					$sql .= "AND U.id_perfil=".$idPerfil." ";
				}
				$sql .= "GROUP BY T.fecha_crea, U.id_usuario, U.nombre_usuario ".
						"ORDER BY T.fecha_crea, U.nombre_usuario";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getConsolidadoVisitasAgentesCreaTotales($fechaIni, $fechaFin) {
			try {
				$sql = "SELECT SUM(T.cantidad_serv_inst) AS cantidad_serv_inst, SUM(T.cantidad_gcf) AS cantidad_gcf, ".
					   "SUM(T.cantidad_seg_ag) AS cantidad_seg_ag, SUM(T.cantidad_seg_serv_inst) AS cantidad_seg_serv_inst, ".
					   "SUM(T.cantidad_seg_gcf) AS cantidad_seg_gcf ".
					   "FROM (".
						   "SELECT 0 AS cantidad_serv_inst, 0 AS cantidad_gcf, ".
						   "COUNT(*) AS cantidad_seg_ag, 0 AS cantidad_seg_serv_inst, 0 AS cantidad_seg_gcf ".
						   "FROM visitas_seg_agentes SA ".
						   "INNER JOIN visitas_seg VS ON SA.id_visita_seg=VS.id_visita_seg ".
						   "WHERE DATE(SA.fecha_crea) BETWEEN STR_TO_DATE('".$fechaIni."', '%d/%m/%Y') AND STR_TO_DATE('".$fechaFin."', '%d/%m/%Y') ".
						   "AND VS.id_familia>10000 ".
						   "UNION ALL ".
						   "SELECT SUM(CASE T.tipo_visita WHEN 1 THEN 1 ELSE 0 END), 0, 0, ".
						   "SUM(CASE T.tipo_visita WHEN 2 THEN 1 ELSE 0 END), 0 ".
						   "FROM (".
							   "SELECT V.id_visita, V.tipo_visita ".
							   "FROM visitas_personas_servicios_inst PR ".
							   "INNER JOIN visitas_personas VP ON PR.id_visita_persona=VP.id_visita_persona ".
							   "INNER JOIN visitas V ON VP.id_visita=V.id_visita ".
							   "WHERE DATE(PR.fecha_crea) BETWEEN STR_TO_DATE('".$fechaIni."', '%d/%m/%Y') AND STR_TO_DATE('".$fechaFin."', '%d/%m/%Y') ".
							   "AND V.id_familia>10000 ".
							   "GROUP BY V.id_visita, V.tipo_visita".
						   ") T ".
						   "UNION ALL ".
						   "SELECT 0, SUM(CASE T.tipo_visita WHEN 1 THEN 1 ELSE 0 END), 0, 0, ".
						   "SUM(CASE T.tipo_visita WHEN 2 THEN 1 ELSE 0 END) ".
						   "FROM (".
							   "SELECT GC.id_visita, V.tipo_visita ".
							   "FROM visitas_guias_crecimiento GC ".
							   "INNER JOIN visitas V ON GC.id_visita=V.id_visita ".
							   "WHERE DATE(GC.fecha_crea) BETWEEN STR_TO_DATE('".$fechaIni."', '%d/%m/%Y') AND STR_TO_DATE('".$fechaFin."', '%d/%m/%Y') ".
							   "AND V.id_familia>10000 ".
							   "GROUP BY GC.id_visita, V.tipo_visita".
						   ") T ".
					   ") T";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasFechaEncuestas($idUsuario, $fechaVisita, $tipoFecha) {
			try {
				$sql = "SELECT V.id_visita, TV.nombre_detalle AS tipo_visita, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, ".
					   "DATE_FORMAT(V.fecha_visita, '%d/%m/%Y') AS fecha_visita, DATE_FORMAT(V.fecha_crea, '%d/%m/%Y %h:%i:%s %p') AS fecha_crea, ".
					   "CASE WHEN V.id_tipo_familia IS NULL THEN 0 ELSE 1 END AS ind_completo ".
					   "FROM visitas V ".
					   "INNER JOIN listas_detalle TV ON V.tipo_visita=TV.codigo_detalle AND TV.id_lista=28 ".
					   "INNER JOIN visitas_personas VP ON V.id_visita=VP.id_visita ".
					   "INNER JOIN personas P ON VP.id_familia=P.id_familia AND VP.id_persona=P.id_persona ".
					   "WHERE V.id_usuario_crea=".$idUsuario." ";
				if ($tipoFecha == "1") {
					$sql .= "AND V.fecha_visita=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ";
				} else {
					$sql .= "AND DATE(V.fecha_crea)=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ";
				}
				$sql .= "AND VP.id_persona=1 ".
						"ORDER BY V.id_visita";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasFechaEncuestasTipoVisita($idUsuario, $fechaVisita, $tipoFecha, $tipoVisita) {
			try {
				$sql = "SELECT V.id_visita, TV.nombre_detalle AS tipo_visita, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, ".
					   "DATE_FORMAT(V.fecha_visita, '%d/%m/%Y') AS fecha_visita, DATE_FORMAT(V.fecha_crea, '%d/%m/%Y %h:%i:%s %p') AS fecha_crea, ".
					   "CASE WHEN V.id_tipo_familia IS NULL THEN 0 ELSE 1 END AS ind_completo ".
					   "FROM visitas V ".
					   "INNER JOIN listas_detalle TV ON V.tipo_visita=TV.codigo_detalle AND TV.id_lista=28 ".
					   "INNER JOIN visitas_personas VP ON V.id_visita=VP.id_visita ".
					   "INNER JOIN personas P ON VP.id_familia=P.id_familia AND VP.id_persona=P.id_persona ".
					   "WHERE V.id_usuario_crea=".$idUsuario." ".
					   "AND V.tipo_visita=".$tipoVisita." ";
				if ($tipoFecha == "1") {
					$sql .= "AND V.fecha_visita=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ";
				} else {
					$sql .= "AND DATE(V.fecha_crea)=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ";
				}
				$sql .= "AND VP.id_persona=1 ".
						"ORDER BY V.id_visita";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasFechaRemisiones($idUsuario, $fechaVisita, $tipoFecha) {
			try {
				$sql = "SELECT V.id_visita, TV.nombre_detalle AS tipo_visita, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, ".
					   "DATE_FORMAT(V.fecha_visita, '%d/%m/%Y') AS fecha_visita, DATE_FORMAT(RM.fecha_crea, '%d/%m/%Y %h:%i:%s %p') AS fecha_crea, ".
					   "CASE WHEN V.id_tipo_familia IS NULL THEN 0 ELSE 1 END AS ind_completo ".
					   "FROM visitas V ".
					   "INNER JOIN listas_detalle TV ON V.tipo_visita=TV.codigo_detalle AND TV.id_lista=28 ".
					   "INNER JOIN visitas_personas VP ON V.id_visita=VP.id_visita ".
					   "INNER JOIN personas P ON VP.id_familia=P.id_familia AND VP.id_persona=P.id_persona ".
					   "INNER JOIN (".
						   "SELECT VP.id_visita, MIN(PR.fecha_crea) AS fecha_crea ".
						   "FROM visitas_personas_programas_rem PR ".
						   "INNER JOIN visitas_personas VP ON PR.id_visita_persona=VP.id_visita_persona ".
						   "GROUP BY VP.id_visita".
					   ") RM ON V.id_visita=RM.id_visita ".
					   "WHERE V.id_usuario_crea=".$idUsuario." ";
				if ($tipoFecha == "1") {
					$sql .= "AND V.fecha_visita=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ";
				} else {
					$sql .= "AND DATE(RM.fecha_crea)=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ";
				}
				$sql .= "AND VP.id_persona=1 ".
						"ORDER BY V.id_visita";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasFechaRemisionesTipoVisita($idUsuario, $fechaVisita, $tipoFecha, $tipoVisita) {
			try {
				$sql = "SELECT V.id_visita, TV.nombre_detalle AS tipo_visita, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, ".
					   "DATE_FORMAT(V.fecha_visita, '%d/%m/%Y') AS fecha_visita, DATE_FORMAT(RM.fecha_crea, '%d/%m/%Y %h:%i:%s %p') AS fecha_crea, ".
					   "CASE WHEN V.id_tipo_familia IS NULL THEN 0 ELSE 1 END AS ind_completo ".
					   "FROM visitas V ".
					   "INNER JOIN listas_detalle TV ON V.tipo_visita=TV.codigo_detalle AND TV.id_lista=28 ".
					   "INNER JOIN visitas_personas VP ON V.id_visita=VP.id_visita ".
					   "INNER JOIN personas P ON VP.id_familia=P.id_familia AND VP.id_persona=P.id_persona ".
					   "INNER JOIN (".
						   "SELECT VP.id_visita, MIN(PR.fecha_crea) AS fecha_crea ".
						   "FROM visitas_personas_programas_rem PR ".
						   "INNER JOIN visitas_personas VP ON PR.id_visita_persona=VP.id_visita_persona ".
						   "GROUP BY VP.id_visita".
					   ") RM ON V.id_visita=RM.id_visita ".
					   "WHERE V.id_usuario_crea=".$idUsuario." ".
					   "AND V.tipo_visita=".$tipoVisita." ";
				if ($tipoFecha == "1") {
					$sql .= "AND V.fecha_visita=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ";
				} else {
					$sql .= "AND DATE(RM.fecha_crea)=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ";
				}
				$sql .= "AND VP.id_persona=1 ".
						"ORDER BY V.id_visita";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasFechaActividadesEducativas($idUsuario, $fechaVisita, $tipoFecha) {
			try {
				$sql = "SELECT V.id_visita, TV.nombre_detalle AS tipo_visita, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, ".
					   "DATE_FORMAT(V.fecha_visita, '%d/%m/%Y') AS fecha_visita, DATE_FORMAT(VA.fecha_crea, '%d/%m/%Y %h:%i:%s %p') AS fecha_crea, ".
					   "CASE WHEN V.id_tipo_familia IS NULL THEN 0 ELSE 1 END AS ind_completo ".
					   "FROM visitas V ".
					   "INNER JOIN listas_detalle TV ON V.tipo_visita=TV.codigo_detalle AND TV.id_lista=28 ".
					   "INNER JOIN visitas_personas VP ON V.id_visita=VP.id_visita ".
					   "INNER JOIN personas P ON VP.id_familia=P.id_familia AND VP.id_persona=P.id_persona ".
					   "INNER JOIN (".
						   "SELECT VP.id_visita, MIN(AE.fecha_crea) AS fecha_crea ".
						   "FROM visitas_personas_act_educativas AE ".
						   "INNER JOIN visitas_personas VP ON AE.id_visita_persona=VP.id_visita_persona ".
						   "GROUP BY VP.id_visita".
					   ") VA ON V.id_visita=VA.id_visita ".
					   "WHERE V.id_usuario_crea=".$idUsuario." ";
				if ($tipoFecha == "1") {
					$sql .= "AND V.fecha_visita=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ";
				} else {
					$sql .= "AND DATE(VA.fecha_crea)=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ";
				}
				$sql .= "AND VP.id_persona=1 ".
						"ORDER BY V.id_visita";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasFechaActividadesEducativasTipoVisita($idUsuario, $fechaVisita, $tipoFecha, $tipoVisita) {
			try {
				$sql = "SELECT V.id_visita, TV.nombre_detalle AS tipo_visita, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, ".
					   "DATE_FORMAT(V.fecha_visita, '%d/%m/%Y') AS fecha_visita, DATE_FORMAT(VA.fecha_crea, '%d/%m/%Y %h:%i:%s %p') AS fecha_crea, ".
					   "CASE WHEN V.id_tipo_familia IS NULL THEN 0 ELSE 1 END AS ind_completo ".
					   "FROM visitas V ".
					   "INNER JOIN listas_detalle TV ON V.tipo_visita=TV.codigo_detalle AND TV.id_lista=28 ".
					   "INNER JOIN visitas_personas VP ON V.id_visita=VP.id_visita ".
					   "INNER JOIN personas P ON VP.id_familia=P.id_familia AND VP.id_persona=P.id_persona ".
					   "INNER JOIN (".
						   "SELECT VP.id_visita, MIN(AE.fecha_crea) AS fecha_crea ".
						   "FROM visitas_personas_act_educativas AE ".
						   "INNER JOIN visitas_personas VP ON AE.id_visita_persona=VP.id_visita_persona ".
						   "GROUP BY VP.id_visita".
					   ") VA ON V.id_visita=VA.id_visita ".
					   "WHERE V.id_usuario_crea=".$idUsuario." ".
					   "AND V.tipo_visita=".$tipoVisita." ";
				if ($tipoFecha == "1") {
					$sql .= "AND V.fecha_visita=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ";
				} else {
					$sql .= "AND DATE(VA.fecha_crea)=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ";
				}
				$sql .= "AND VP.id_persona=1 ".
						"ORDER BY V.id_visita";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasFechaServiciosInstitucionales($idUsuario, $fechaVisita, $tipoFecha) {
			try {
				$sql = "SELECT V.id_visita, TV.nombre_detalle AS tipo_visita, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, ".
					   "DATE_FORMAT(V.fecha_visita, '%d/%m/%Y') AS fecha_visita, DATE_FORMAT(PR.fecha_crea, '%d/%m/%Y %h:%i:%s %p') AS fecha_crea, ".
					   "CASE WHEN V.id_tipo_familia IS NULL THEN 0 ELSE 1 END AS ind_completo ".
					   "FROM visitas_personas_servicios_inst PR ".
					   "INNER JOIN visitas_personas VP ON PR.id_visita_persona=VP.id_visita_persona ".
					   "INNER JOIN visitas V ON VP.id_visita=V.id_visita ".
					   "INNER JOIN listas_detalle TV ON V.tipo_visita=TV.codigo_detalle AND TV.id_lista=28 ".
					   "INNER JOIN visitas_personas V2 ON V.id_visita=V2.id_visita ".
					   "INNER JOIN personas P ON V2.id_familia=P.id_familia AND V2.id_persona=P.id_persona ".
					   "WHERE PR.id_usuario_crea=".$idUsuario." ";
				if ($tipoFecha == "1") {
					$sql .= "AND V.fecha_visita=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ";
				} else {
					$sql .= "AND DATE(PR.fecha_crea)=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ";
				}
				$sql .= "GROUP BY V.id_visita, V.fecha_visita, PR.id_usuario_crea ".
						"ORDER BY V.id_visita";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasFechaServiciosInstitucionalesTipoVisita($idUsuario, $fechaVisita, $tipoFecha, $tipoVisita) {
			try {
				$sql = "SELECT V.id_visita, TV.nombre_detalle AS tipo_visita, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, ".
					   "DATE_FORMAT(V.fecha_visita, '%d/%m/%Y') AS fecha_visita, DATE_FORMAT(PR.fecha_crea, '%d/%m/%Y %h:%i:%s %p') AS fecha_crea, ".
					   "CASE WHEN V.id_tipo_familia IS NULL THEN 0 ELSE 1 END AS ind_completo ".
					   "FROM visitas_personas_servicios_inst PR ".
					   "INNER JOIN visitas_personas VP ON PR.id_visita_persona=VP.id_visita_persona ".
					   "INNER JOIN visitas V ON VP.id_visita=V.id_visita ".
					   "INNER JOIN listas_detalle TV ON V.tipo_visita=TV.codigo_detalle AND TV.id_lista=28 ".
					   "INNER JOIN visitas_personas V2 ON V.id_visita=V2.id_visita ".
					   "INNER JOIN personas P ON V2.id_familia=P.id_familia AND V2.id_persona=P.id_persona ".
					   "WHERE PR.id_usuario_crea=".$idUsuario." ".
					   "AND V.tipo_visita=".$tipoVisita." ";
				if ($tipoFecha == "1") {
					$sql .= "AND V.fecha_visita=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ";
				} else {
					$sql .= "AND DATE(PR.fecha_crea)=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ";
				}
				$sql .= "GROUP BY V.id_visita, V.fecha_visita, PR.id_usuario_crea ".
						"ORDER BY V.id_visita";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasFechaGuiasCrecimientoFamiliar($idUsuario, $fechaVisita, $tipoFecha) {
			try {
				$sql = "SELECT V.id_visita, TV.nombre_detalle AS tipo_visita, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, ".
					   "DATE_FORMAT(V.fecha_visita, '%d/%m/%Y') AS fecha_visita, DATE_FORMAT(GC.fecha_crea, '%d/%m/%Y %h:%i:%s %p') AS fecha_crea, ".
					   "CASE WHEN V.id_tipo_familia IS NULL THEN 0 ELSE 1 END AS ind_completo ".
					   "FROM visitas_guias_crecimiento GC ".
					   "INNER JOIN visitas V ON GC.id_visita=V.id_visita ".
					   "INNER JOIN listas_detalle TV ON V.tipo_visita=TV.codigo_detalle AND TV.id_lista=28 ".
					   "INNER JOIN visitas_personas VP ON V.id_visita=VP.id_visita ".
					   "INNER JOIN personas P ON VP.id_familia=P.id_familia AND VP.id_persona=P.id_persona ".
					   "WHERE GC.id_usuario_crea=".$idUsuario." ";
				if ($tipoFecha == "1") {
					$sql .= "AND V.fecha_visita=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ";
				} else {
					$sql .= "AND DATE(GC.fecha_crea)=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ";
				}
				$sql .= "GROUP BY V.id_visita, V.fecha_visita, GC.id_usuario_crea ".
						"ORDER BY V.id_visita";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasFechaGuiasCrecimientoFamiliarTipoVisita($idUsuario, $fechaVisita, $tipoFecha, $tipoVisita) {
			try {
				$sql = "SELECT V.id_visita, TV.nombre_detalle AS tipo_visita, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, ".
					   "DATE_FORMAT(V.fecha_visita, '%d/%m/%Y') AS fecha_visita, DATE_FORMAT(GC.fecha_crea, '%d/%m/%Y %h:%i:%s %p') AS fecha_crea, ".
					   "CASE WHEN V.id_tipo_familia IS NULL THEN 0 ELSE 1 END AS ind_completo ".
					   "FROM visitas_guias_crecimiento GC ".
					   "INNER JOIN visitas V ON GC.id_visita=V.id_visita ".
					   "INNER JOIN listas_detalle TV ON V.tipo_visita=TV.codigo_detalle AND TV.id_lista=28 ".
					   "INNER JOIN visitas_personas VP ON V.id_visita=VP.id_visita ".
					   "INNER JOIN personas P ON VP.id_familia=P.id_familia AND VP.id_persona=P.id_persona ".
					   "WHERE GC.id_usuario_crea=".$idUsuario." ".
					   "AND V.tipo_visita=".$tipoVisita." ";
				if ($tipoFecha == "1") {
					$sql .= "AND V.fecha_visita=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ";
				} else {
					$sql .= "AND DATE(GC.fecha_crea)=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ";
				}
				$sql .= "GROUP BY V.id_visita, V.fecha_visita, GC.id_usuario_crea ".
						"ORDER BY V.id_visita";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasSeguimientoAgFecha($idUsuario, $fechaVisita, $tipoFecha) {
			try {
				$sql = "SELECT V.id_visita, TV.nombre_detalle AS tipo_visita, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, ".
					   "DATE_FORMAT(V.fecha_visita, '%d/%m/%Y') AS fecha_visita, DATE_FORMAT(SA.fecha_crea, '%d/%m/%Y %h:%i:%s %p') AS fecha_crea ".
					   "FROM visitas_seg_agentes SA ".
					   "INNER JOIN visitas_seg VS ON SA.id_visita_seg=VS.id_visita_seg ".
					   "INNER JOIN visitas V ON VS.id_visita=V.id_visita ".
					   "INNER JOIN listas_detalle TV ON V.tipo_visita=TV.codigo_detalle AND TV.id_lista=28 ".
					   "INNER JOIN visitas_personas VP ON V.id_visita=VP.id_visita ".
					   "INNER JOIN personas P ON VP.id_familia=P.id_familia AND VP.id_persona=P.id_persona ".
					   "WHERE SA.id_usuario_crea=".$idUsuario." ";
				if ($tipoFecha == "1") {
					$sql .= "AND V.fecha_visita=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ";
				} else {
					$sql .= "AND DATE(SA.fecha_crea)=STR_TO_DATE('".$fechaVisita."', '%d/%m/%Y') ";
				}
				$sql .= "AND VP.id_persona=1 ".
						"ORDER BY V.id_visita";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getConsolidadoVisitasUsuario($idUsuarioCoordina, $fechaIni, $fechaFin, $idUsuario) {
			try {
				$sql = "SELECT U.id_usuario, U.nombre_usuario, COUNT(*) AS cantidad ".
					   "FROM visitas V ".
					   "INNER JOIN usuarios U ON V.id_usuario_crea=U.id_usuario ".
					   "WHERE U.id_usuario_coordina=".$idUsuarioCoordina." ".
					   "AND V.fecha_visita BETWEEN STR_TO_DATE('".$fechaIni."', '%d/%m/%Y') AND STR_TO_DATE('".$fechaFin."', '%d/%m/%Y') ";
				if ($idUsuario > 0) {
					$sql .= "AND V.id_usuario_crea=".$idUsuario." ";
				}
				$sql .= "AND V.id_tipo_familia IS NOT NULL ".
						"GROUP BY V.id_usuario_crea, U.nombre_usuario ".
						"ORDER BY U.nombre_usuario";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaIdVisitasUsuario($idUsuarioCoordina, $fechaIni, $fechaFin, $idUsuario) {
			try {
				$sql = "SELECT V.id_visita, V.id_familia, U.id_usuario, U.nombre_usuario, ".
					   "DATE_FORMAT(V.fecha_visita, '%d/%m/%Y') AS fecha_visita, VI.direccion, ".
					   "VI.nombre_barrio, F.telefono, F.celular_1, F.celular_2 ".
					   "FROM visitas V ".
					   "INNER JOIN usuarios U ON V.id_usuario_crea=U.id_usuario ".
					   "INNER JOIN familias F ON V.id_familia=F.id_familia ".
					   "INNER JOIN viviendas VI ON F.id_vivienda=VI.id_vivienda ".
					   "WHERE U.id_usuario_coordina=".$idUsuarioCoordina." ".
					   "AND V.fecha_visita BETWEEN STR_TO_DATE('".$fechaIni."', '%d/%m/%Y') AND STR_TO_DATE('".$fechaFin."', '%d/%m/%Y') ";
				if ($idUsuario > 0) {
					$sql .= "AND V.id_usuario_crea=".$idUsuario." ";
				}
				$sql .= "AND V.id_tipo_familia IS NOT NULL ".
						"ORDER BY V.fecha_visita, U.nombre_usuario, V.id_familia";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaVisitasFamilia($idFamilia) {
			try {
				$sql = "SELECT V.id_visita, V.tipo_visita, VS.orden_visita ".
					   "FROM visitas V ".
					   "LEFT JOIN visitas_seg VS ON V.id_visita=VS.id_visita ".
					   "WHERE V.id_familia=".$idFamilia." ".
					   "ORDER BY V.tipo_visita, VS.orden_visita";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getVisitaSegAgente($idVisistaSeg) {
			try {
				$sql = "SELECT SA.* ".
					   "FROM visitas_seg_agentes SA ".
					   "WHERE SA.id_visita_seg=".$idVisistaSeg;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	}
?>
