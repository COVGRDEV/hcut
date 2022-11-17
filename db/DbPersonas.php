<?php
	require_once("DbConexion.php");
	
	class DbPersonas extends DbConexion {
		public function getListaPersonas($idFamilia) {
			try {
				$sql = "SELECT P.* ".
					   "FROM personas P ".
					   "WHERE P.id_familia=".$idFamilia." ".
					   "ORDER BY P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, P.id_persona";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getListaPersonasVisitaPorNombre($idVisita) {
			try {
				$sql = "SELECT VP.id_visita_persona, P.* ".
					   "FROM personas P ".
					   "INNER JOIN visitas_personas VP ON P.id_familia=VP.id_familia AND P.id_persona=VP.id_persona ".
					   "WHERE VP.id_visita=".$idVisita." ".
					   "ORDER BY P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, P.id_persona";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getPersona($idFamilia, $idPersona) {
			try {
				$sql = "SELECT * ".
					   "FROM personas ".
					   "WHERE id_familia=".$idFamilia." ".
					   "AND id_persona=".$idPersona;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function crearPersona($idFamilia, $nombre1, $nombre2, $apellido1, $apellido2,
				$sexo, $fechaNacimiento, $idParentesco, $idTipoDocumento, $numDocumento, $idUsuario) {
			//Se verifica cuales campos vienen vacíos
			if ($idParentesco == "-1") {
				$idParentesco = "NULL";
			}
			
			$sql = "CALL pa_crear_persona(".$idFamilia.", '".$nombre1."', '".$nombre2."', '".
				   $apellido1."', '".$apellido2."', '".$sexo."', STR_TO_DATE('".$fechaNacimiento."', '%d/%m/%Y'), ".
				   $idParentesco.", ".$idTipoDocumento.", '".$numDocumento."', ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function modificarPersona($idFamilia, $idPersona, $nombre1, $nombre2, $apellido1, $apellido2,
				$sexo, $fechaNacimiento, $idParentesco, $idTipoDocumento, $numDocumento, $idUsuario) {
			//Se verifica cuales campos vienen vacíos
			if ($idParentesco == "-1") {
				$idParentesco = "NULL";
			}
			
			$sql = "CALL pa_modificar_persona(".$idFamilia.", ".$idPersona.", '".$nombre1."', '".$nombre2."', '".
				   $apellido1."', '".$apellido2."', '".$sexo."', STR_TO_DATE('".$fechaNacimiento."', '%d/%m/%Y'), ".
				   $idParentesco.", ".$idTipoDocumento.", '".$numDocumento."', ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
		
		public function modificarPersonaEMail($idFamilia, $idPersona, $email, $idUsuario) {
			$sql = "CALL pa_modificar_persona_email(".$idFamilia.", ".$idPersona.", '".$email."', ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		}
	}
?>
