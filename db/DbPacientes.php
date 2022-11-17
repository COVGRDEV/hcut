<?php

require_once("DbConexion.php");

class DbPacientes extends DbConexion {

	public function getExistepaciente($id_cita, $id_paciente) {//Consulta si en la cita el campo id_paciente existe
		try {
			$sql = "SELECT P.*, DATE_FORMAT(P.fecha_nacimiento, '%d/%m/%Y') AS fecha_nacimiento,
						C.telefono_contacto, C.observacion_cita, CV.nombre_convenio,
						CASE P.status_convenio_paciente
							WHEN 1 THEN 'Activo'
							WHEN 2 THEN 'Inactivo'
							WHEN 3 THEN 'Atención especial'
							WHEN 4 THEN 'Retirado'
							ELSE 'No registrado/No aplica'
						END AS PstatusC
						FROM pacientes P
						INNER JOIN citas C ON C.id_paciente=P.id_paciente
						LEFT JOIN convenios CV ON CV.id_convenio=P.id_convenio_paciente
						WHERE C.id_cita=" . $id_cita . "
						AND C.id_paciente=" . $id_paciente;

			//echo($sql);
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	public function getPacienteDetalle($id_paciente){
		try {
		
		$sql = "SELECT P.*, LD.codigo_detalle FROM pacientes P
				LEFT JOIN listas_detalle LD ON  P.id_tipo_documento = LD.id_detalle
				WHERE P.id_paciente =".$id_paciente;
				
		return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}	
				
	}

	public function getExistepaciente2($documento) {//Si en citas el campo id_paciente esta null, Busca por n�mero de documento
		try {
			$sql = "SELECT p.*, DATE_FORMAT(fecha_nacimiento, '%d/%m/%Y') AS fecha_nacimiento_aux, ld.nombre_detalle AS tipodocumento
						FROM pacientes p
						INNER JOIN listas_detalle ld ON ld.id_detalle = p.id_tipo_documento
						WHERE p.numero_documento = (SELECT numero_documento FROM citas WHERE id_cita = $documento) AND 
						p.id_tipo_documento = (SELECT id_tipo_documento FROM citas WHERE id_cita = $documento)";
			//echo($sql);

			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}

	public function getExistepaciente3($id_paciente) {//Se busca por id del paciente
		try {
			$sql = "SELECT p.*, ld.nombre_detalle AS tipodocumento, sx.nombre_detalle AS sexo_t, ps.nombre_pais, pt.nom_dep AS nom_dep_t , m.nom_mun AS nom_mun_t,
					DATE_FORMAT(fecha_nacimiento, '%d/%m/%Y') AS fecha_nacimiento_aux, fu_calcular_edad(p.fecha_nacimiento, CURDATE()) AS edad,
					ld.text_adicional_detalle AS codigo_doc_siesa, ps.cod_siesa AS paisCodSiesa, tp.nombre_detalle AS tipo_cotizante,
					CASE p.status_convenio_paciente
						WHEN 1 THEN 'Activo'
						WHEN 2 THEN 'Inactivo'
						WHEN 3 THEN 'Atención especial'
						WHEN 4 THEN 'Retirado'
						ELSE 'No registrado/No aplica'
					END AS PstatusC
					FROM pacientes p
					INNER JOIN listas_detalle ld ON ld.id_detalle = p.id_tipo_documento
					LEFT JOIN listas_detalle sx ON p.sexo=sx.id_detalle
					LEFT JOIN listas_detalle tp ON p.tipo_coti_paciente = tp.id_detalle
					LEFT JOIN paises ps ON ps.id_pais=p.id_pais
					LEFT JOIN departamentos pt ON pt.cod_dep=p.cod_dep
					LEFT JOIN municipios m ON m.cod_mun_dane=p.cod_mun
					WHERE p.id_paciente=".$id_paciente;
			
			//echo($sql."<br />");
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}

	//Funci�n que busca todos los pacientes que tengan el n�mero de documento dado ubicando primero el que coincida con el tipo de documento
	public function getExistepaciente4($numero_documento, $id_tipo_documento) {
		try {
			$sql = "SELECT p.*, DATE_FORMAT(fecha_nacimiento, '%d/%m/%Y') AS fecha_nacimiento_aux, ld.nombre_detalle AS tipodocumento
						FROM pacientes p
						INNER JOIN listas_detalle ld ON ld.id_detalle = p.id_tipo_documento
						WHERE p.numero_documento='" . $numero_documento . "'
						ORDER BY CASE WHEN p.id_tipo_documento=" . $id_tipo_documento . " THEN 0 ELSE 1 END, p.id_paciente";

			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}

	public function getBuscarpacientes($parametro) {//Busca pacientes por numero de identificacion o nombre
		return $this->getListaPacientesBusc($parametro, 0);
	}

	public function getListaPacientesBusc($parametro, $limite) {
		try {
			$parametro = str_replace(" ", "%", $parametro);
			$sql = "SELECT p.*, ld.nombre_detalle, ps.nombre_pais, pt.nom_dep AS nom_dep2 , m.nom_mun AS nom_mun2,
						DATE_FORMAT(p.fecha_nacimiento, '%d/%m/%Y') AS fecha_nacimiento_aux, LT.nombre_detalle AS tipo_sangre2,
						LT2.nombre_detalle AS factor_rh2, ld.codigo_detalle AS cod_tipo_documento,
						ld.nombre_detalle AS tipo_documento
						FROM pacientes p
						INNER JOIN listas_detalle ld ON ld.id_detalle=p.id_tipo_documento
						LEFT JOIN paises ps ON ps.id_pais=p.id_pais
						LEFT JOIN departamentos pt ON pt.cod_dep=p.cod_dep
						LEFT JOIN municipios m ON m.cod_mun_dane=p.cod_mun
						LEFT JOIN listas_detalle LT ON LT.id_detalle=p.tipo_sangre
						LEFT JOIN listas_detalle LT2 ON LT2.id_detalle=p.factor_rh
						WHERE CONCAT(p.nombre_1, ' ', IFNULL(p.nombre_2, ''), ' ', p.apellido_1, ' ', IFNULL(p.apellido_2, '')) LIKE '%" . $parametro . "%'
						OR p.numero_documento='" . $parametro . "'
						ORDER BY p.nombre_1, p.nombre_2, p.apellido_1, p.apellido_2";
			if ($limite > 0) {
				$sql .= " LIMIT " . $limite;
			}

			//echo($sql);
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}

	//Esta funcion busca el paciente para el formulario Registrar Pago - cuando recibe datos por post
	public function getBuscarPacientesPost($idAdmision) {
		try {
			$sql = "SELECT p.*, ld.nombre_detalle, ps.nombre_pais, pt.nom_dep AS nom_dep2 , m.nom_mun AS nom_mun2,
						DATE_FORMAT(p.fecha_nacimiento, '%d/%m/%Y') AS fecha_nacimiento_aux, LT.nombre_detalle AS tipo_sangre2, LT2.nombre_detalle AS factor_rh2, A.id_plan
						FROM pacientes p
						INNER JOIN listas_detalle ld ON ld.id_detalle = p.id_tipo_documento
						INNER JOIN paises ps ON ps.id_pais = p.id_pais
						LEFT JOIN departamentos pt ON pt.cod_dep = p.cod_dep
						LEFT JOIN municipios m ON m.cod_mun_dane = p.cod_mun
						INNER JOIN listas_detalle LT ON LT.id_detalle = p.tipo_sangre
						INNER JOIN listas_detalle LT2 ON LT2.id_detalle = p.factor_rh
						INNER JOIN admisiones A ON A.id_paciente = p.id_paciente
						WHERE A.id_admision = $idAdmision";

			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}

	//Consulta si el usuario en el evento blur del formulario admisiones cuando este no recibe datos por post
	public function getVerificacionPost($numero_documento, $id_tipo_documento) {
		try {
			$sql = "SELECT P.*, LD.nombre_detalle AS tipodocumento, DATE_FORMAT(P.fecha_nacimiento, '%d/%m/%Y') AS fecha_nacimiento_aux,
						(YEAR(CURDATE())-YEAR(p.fecha_nacimiento))-(RIGHT(CURDATE(), 5)<RIGHT(p.fecha_nacimiento, 5)) AS edad, CON.nombre_convenio as Txtconvenio,
												PL.nombre_plan AS nombre_plan_aux
						FROM pacientes P
						INNER JOIN listas_detalle LD ON LD.id_detalle=P.id_tipo_documento
						LEFT JOIN convenios CON ON P.id_convenio_paciente=CON.id_convenio
												LEFT JOIN planes PL ON PL.id_plan = P.id_plan
						WHERE P.numero_documento='" . $numero_documento . "'
						AND P.id_tipo_documento=" . $id_tipo_documento;

			//echo $sql;
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}

	public function getPacienteNumeroDocumento($numero_documento) {
		try {
			$sql = "SELECT P.*, LD.nombre_detalle AS tipodocumento, DATE_FORMAT(P.fecha_nacimiento, '%d/%m/%Y') AS fecha_nacimiento_aux
						FROM pacientes P
						INNER JOIN listas_detalle LD ON LD.id_detalle=P.id_tipo_documento
						WHERE P.numero_documento='" . $numero_documento . "'";

			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	public function getPacienteNumeroDocumentoAndTipoDocumento($numero_documento, $tipo_documento) {
		try {
			$sql = "SELECT P.*, LD.nombre_detalle AS tipodocumento, DATE_FORMAT(P.fecha_nacimiento, '%d/%m/%Y') AS fecha_nacimiento_aux
						FROM pacientes P
						INNER JOIN listas_detalle LD ON LD.id_detalle=P.id_tipo_documento
						WHERE P.numero_documento='" . $numero_documento . "' AND P.id_tipo_documento=$tipo_documento  ";
			
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}

	public function getEdadPaciente($id_paciente, $fecha) {
		try {
			if ($fecha == "") {
				$fecha = "CURDATE()";
			} else {
				$fecha = "'" . $fecha . "'";
			}
			$sql = "SELECT p.*, (YEAR(" . $fecha . ")-YEAR(p.fecha_nacimiento))-(RIGHT(" . $fecha . ", 5)<RIGHT(p.fecha_nacimiento, 5)) AS edad,
						SX.nombre_detalle AS sexo_t, DATE_FORMAT(p.fecha_nacimiento, '%d/%m/%Y') AS fecha_nacimiento_t,
						EC.nombre_detalle AS estado_civil, pa_res.nombre_pais as pais_res, dep_res.nom_dep AS dept_res, mun_res.nom_mun AS muni_res,
						pa_nac.nombre_pais AS pais_nac, dep_nac.nom_dep AS dept_nac, mun_nac.nom_mun AS muni_nac, TD.nombre_detalle AS tipo_documento
						FROM pacientes p
						LEFT JOIN listas_detalle SX ON p.sexo=SX.id_detalle
						LEFT JOIN listas_detalle EC ON p.id_estado_civil=EC.id_detalle
						LEFT JOIN listas_detalle TD ON p.id_tipo_documento=TD.id_detalle
						LEFT JOIN paises pa_res ON p.id_pais=pa_res.id_pais
						LEFT JOIN departamentos dep_res ON p.cod_dep=dep_res.cod_dep
						LEFT JOIN municipios mun_res ON p.cod_mun=mun_res.cod_mun_dane
						LEFT JOIN paises pa_nac ON p.id_pais_nac=pa_nac.id_pais
						LEFT JOIN departamentos dep_nac ON p.cod_dep_nac=dep_nac.cod_dep
						LEFT JOIN municipios mun_nac ON p.cod_mun_nac=mun_nac.cod_mun_dane
						WHERE p.id_paciente=" . $id_paciente;

			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	public function getEdad_HC($fecha_nacimiento, $fecha_base) {
			
			try {
				if ($fecha_base != "") {
					$fecha_base = "STR_TO_DATE('".$fecha_base."', '%Y-%m-%d')";	
				} else {
					$fecha_base = "CURDATE()";
				}
				$sql = "SELECT fu_calcular_edad(STR_TO_DATE('".$fecha_nacimiento."', '%Y-%m-%d'), ".$fecha_base.") AS edad";
				
				return $this->getUnDato($sql);
				
			} catch (Exception $e) {
				return array();
			}
		}
	
	public function insertarTemporalHuella($id_usuario, $nombre_arch) {
		try {
			//Se borra el registro del usuario
			$sql = "DELETE FROM temporal_huella
						WHERE id_usuario=" . $id_usuario;

			$this->ejecutarSentencia($sql, array());

			$sql = "INSERT INTO temporal_huella
						(id_usuario, huella)
						VALUES (" . $id_usuario . ", '" . mysql_escape_string(file_get_contents($nombre_arch)) . "')";

			$this->ejecutarSentencia($sql, array());

			return 1;
		} catch (Exception $e) {
			return -2;
		}
	}

	public function getTemporalHuella($id_usuario) {
		try {
			$sql = "SELECT *
						FROM temporal_huella
						WHERE id_usuario=" . $id_usuario;

			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}

	public function deleteTemporalHuella($id_usuario) {
		try {
			$sql = "DELETE FROM temporal_huella
						WHERE id_usuario =" . $id_usuario;

			$this->ejecutarSentencia($sql, array());
		} catch (Exception $e) {
			return array();
		}
	}

	public function getPaciente($id_paciente, $fecha = "") {
		try {
			if (trim($fecha == "")) {
				$fecha = "CURDATE()";
			} else {
				$fecha = "'" . $fecha . "'";
			}
			$sql = "SELECT P.*, TD.nombre_detalle AS tipo_documento, TS.nombre_detalle AS tipo_sangre_t,
						RH.nombre_detalle AS factor_rh_t, SX.nombre_detalle AS sexo_t, PA.nombre_pais, D.nom_dep AS nom_dep_t,
						M.nom_mun AS nom_mun_t, fu_calcular_edad(P.fecha_nacimiento, " . $fecha . ") AS edad,
						DATE_FORMAT(P.fecha_nacimiento, '%d/%m/%Y') AS fecha_nacimiento_t, EC.nombre_detalle AS estado_civil,
						PN.nombre_pais AS nombre_pais_nac, DN.nom_dep AS nom_dep_nac_t, MN.nom_mun AS nom_mun_nac_t, TD.codigo_detalle AS codigoDocumento,
						SX.codigo_detalle AS codigoSexo, fu_calcular_edad(fecha_nacimiento,curdate()) AS edad, C.nombre_convenio
						FROM pacientes P
						LEFT JOIN listas_detalle TD ON P.id_tipo_documento=TD.id_detalle
						LEFT JOIN listas_detalle TS ON P.tipo_sangre=TS.id_detalle
						LEFT JOIN listas_detalle RH ON P.factor_rh=RH.id_detalle
						LEFT JOIN listas_detalle SX ON P.sexo=SX.id_detalle
						LEFT JOIN listas_detalle EC ON P.id_estado_civil=EC.id_detalle
						LEFT JOIN paises PA ON P.id_pais=PA.id_pais
						LEFT JOIN departamentos D ON P.cod_dep=D.cod_dep
						LEFT JOIN municipios M ON P.cod_mun=M.cod_mun_dane
						LEFT JOIN paises PN ON P.id_pais_nac=PN.id_pais
						LEFT JOIN departamentos DN ON P.cod_dep_nac=DN.cod_dep
						LEFT JOIN municipios MN ON P.cod_mun_nac=MN.cod_mun_dane
						LEFT JOIN convenios C ON P.id_convenio_paciente = C.id_convenio
						WHERE P.id_paciente=" . $id_paciente;

			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}

	public function crear_editar_paciente($id_paciente, $id_tipo_documento, $numero_documento, $nombre_1, $nombre_2, $apellido_1, $apellido_2, $direccion, $id_pais, $cod_dep, $cod_mun, $nom_dep, $nom_mun, $telefono_1, $telefono_2, $id_usuario, $email="") {
		try {
			if ($id_paciente == "") {
				$id_paciente = "NULL";
			}
			if ($nombre_2 == "") {
				$nombre_2 = "NULL";
			} else {
				$nombre_2 = "'" . $nombre_2 . "'";
			}
			if ($apellido_2 == "") {
				$apellido_2 = "NULL";
			} else {
				$apellido_2 = "'" . $apellido_2 . "'";
			}
			if ($cod_dep == "") {
				$cod_dep = "NULL";
			} else {
				$cod_dep = "'" . $cod_dep . "'";
			}
			if ($cod_mun == "") {
				$cod_mun = "NULL";
			} else {
				$cod_mun = "'" . $cod_mun . "'";
			}
			if ($nom_dep == "") {
				$nom_dep = "NULL";
			} else {
				$nom_dep = "'" . $nom_dep . "'";
			}
			if ($nom_mun == "") {
				$nom_mun = "NULL";
			} else {
				$nom_mun = "'" . $nom_mun . "'";
			}
			if ($telefono_2 == "") {
				$telefono_2 = "NULL";
			} else {
				$telefono_2 = "'" . $telefono_2 . "'";
			}
			if ($email == "") {
				$email = "NULL";
			} else {
				$email = "'" . $email . "'";
			}

			$sql = "CALL pa_crear_editar_paciente(" . $id_paciente . ", " . $id_tipo_documento . ", '" . $numero_documento . "', '" . $nombre_1 . "', " .
					$nombre_2 . ", '" . $apellido_1 . "', " . $apellido_2 . ", '" . $direccion . "', " . $id_pais . ", " . $cod_dep . ", " . $cod_mun . ", 
					".$nom_dep . ", " . $nom_mun . ", '" . $telefono_1 . "', " . $telefono_2 . ", " . $id_usuario . ", ".$email.", 1 , @id)";
			//echo($sql);

			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			$resultado_out = $arrResultado["@id"];

			return $resultado_out;
		} catch (Exception $e) {
			return array();
		}
	}

	public function editar_paciente($id_paciente, $id_tipo_documento, $numero_documento, $nombre_1, $nombre_2, $apellido_1, $apellido_2, $sexo, 
			$fecha_nacimiento, $tipo_sangre, $factor_rh, $id_pais_nac, $cod_dep_nac, $cod_mun_nac, $nom_dep_nac, $nom_mun_nac, $id_pais, $cod_dep, 
			$cod_mun, $nom_dep, $nom_mun, $id_zona, $direccion, $email, $telefono_1, $telefono_2, $profesion, $id_estado_civil, $ind_desplazado, 
			$id_etnia, $id_usuario, $observ_paciente = "", $ind_habeas_data, $convenio, $estado_convenio, $exento, $rango, $tipoUsuario, $id_plan) {
		try {
			$id_paciente == "" ? $id_paciente = "NULL" : $id_paciente = "" . $id_paciente . "";
			$id_tipo_documento == "" ? $id_tipo_documento = "NULL" : $id_tipo_documento = "" . $id_tipo_documento . "";
			$numero_documento == "" ? $numero_documento = "NULL" : $numero_documento = "" . $numero_documento . "";
			$nombre_1 == "" ? $nombre_1 = "NULL" : $nombre_1 = "'" . $nombre_1 . "'";
			$nombre_2 == "" ? $nombre_2 = "NULL" : $nombre_2 = "'" . $nombre_2 . "'";
			$apellido_1 == "" ? $apellido_1 = "NULL" : $apellido_1 = "'" . $apellido_1 . "'";
			$apellido_2 == "" ? $apellido_2 = "NULL" : $apellido_2 = "'" . $apellido_2 . "'";
			$sexo == "" ? $sexo = "NULL" : $sexo = "" . $sexo . "";
			$fecha_nacimiento == "" ? $fecha_nacimiento = "NULL" : $fecha_nacimiento = "'" . $fecha_nacimiento . "'";
			$tipo_sangre == "" ? $tipo_sangre = "NULL" : $tipo_sangre = "'" . $tipo_sangre . "'";
			$factor_rh == "" ? $factor_rh = "NULL" : $factor_rh = "" . $factor_rh . "";
			$id_pais_nac == "" ? $id_pais_nac = "NULL" : $id_pais_nac = "" . $id_pais_nac . "";
			$cod_dep_nac == "" ? $cod_dep_nac = "NULL" : $cod_dep_nac = "" . $cod_dep_nac . "";
			$cod_mun_nac == "" ? $cod_mun_nac = "NULL" : $cod_mun_nac = "" . $cod_mun_nac . "";
			$nom_dep_nac == "" ? $nom_dep_nac = "NULL" : $nom_dep_nac = "" . $nom_dep_nac . "";
			$nom_mun_nac == "" ? $nom_mun_nac = "NULL" : $nom_mun_nac = "" . $nom_mun_nac . "";
			$id_pais == "" ? $id_pais = "NULL" : $id_pais = "" . $id_pais . "";
			$cod_dep == "" ? $cod_dep = "NULL" : $cod_dep = "" . $cod_dep . "";
			$cod_mun == "" ? $cod_mun = "NULL" : $cod_mun = "" . $cod_mun . "";
			$nom_dep == "" ? $nom_dep = "NULL" : $nom_dep = "'" . $nom_dep . "'";
			$nom_mun == "" ? $nom_mun = "NULL" : $nom_mun = "'" . $nom_mun . "'";
			$id_zona == "" ? $id_zona = "NULL" : $id_zona = "" . $id_zona . "";
			$direccion == "" ? $direccion = "NULL" : $direccion = "'" . $direccion . "'";
			$email == "" ? $email = "NULL" : $email = "'" . $email . "'";
			$telefono_1 == "" ? $telefono_1 = "NULL" : $telefono_1 = "'" . $telefono_1 . "'";
			$telefono_2 == "" ? $telefono_2 = "NULL" : $telefono_2 = "'" . $telefono_2 . "'";
			$profesion == "" ? $profesion = "NULL" : $profesion = "'" . $profesion . "'";
			$id_estado_civil == "" ? $id_estado_civil = "NULL" : $id_estado_civil = "" . $id_estado_civil . "";
			$ind_desplazado == "" ? $ind_desplazado = "NULL" : $ind_desplazado = "" . $ind_desplazado . "";
			$id_etnia == "" ? $id_etnia = "NULL" : $id_etnia = "" . $id_etnia . "";
			$id_usuario == "" ? $id_usuario = "NULL" : $id_usuario = "" . $id_usuario . "";
			$observ_paciente == "" ? $observ_paciente = "NULL" : $observ_paciente = "'" . $observ_paciente . "'";
			$convenio == "" ? $convenio = "NULL" : $convenio = $convenio;
			$estado_convenio == "" ? $estado_convenio = "NULL" : $estado_convenio = "" . $estado_convenio . "";
			$exento == "" ? $exento = "NULL" : $exento = "" . $exento . "";
			$rango == "" ? $rango = "NULL" : $rango = "" . $rango . "";
			$tipoUsuario == "" ? $tipoUsuario = "NULL" : $tipoUsuario = "" . $tipoUsuario . "";
			$id_plan == "" ? $id_plan = "NULL" : $id_plan = "" . $id_plan . "";
			$ind_habeas_data == "" ? $ind_habeas_data = "NULL" : $ind_habeas_data = "" . $ind_habeas_data . "";
			
			$sql = "CALL pa_editar_paciente(" . $id_paciente . ", " . $id_tipo_documento . ", " . $numero_documento . ", " . $nombre_1 . ", " . $nombre_2 . ", " .
					$apellido_1 . ", " . $apellido_2 . ", " . $sexo . ", STR_TO_DATE(" . $fecha_nacimiento . ", '%d/%m/%Y'), " . $tipo_sangre . ", " . $factor_rh . ", " .
					$id_pais_nac . ", " . $cod_dep_nac . ", " . $cod_mun_nac . ", " . $nom_dep_nac . ", " . $nom_mun_nac . ", " . $id_pais . ", " . $cod_dep . ", " . $cod_mun . ", " .
					$nom_dep . ", " . $nom_mun . ", " . $id_zona . ", " . $direccion . ", " . $email . ", " . $telefono_1 . ", " . $telefono_2 . ", " . $profesion . ", " .
					$id_estado_civil . ", " . $ind_desplazado . ", " . $id_etnia . ", " . $observ_paciente . ", 1, " . $id_usuario . ", " . $convenio . ", " .
					$estado_convenio . ", " . $exento . ", " . $rango . ", " . $tipoUsuario. ", " . $id_plan . "," . $ind_habeas_data . " , @id)";
		

			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			$resultado_out = $arrResultado["@id"];

			return $resultado_out;
		} catch (Exception $e) {
			return array();
		}
	}

	public function guardar_clave_verificacion($id_usuario, $clave_verificacion) {
		try {
			$sql = "UPDATE pacientes SET clave_verificacion = '" . $clave_verificacion . "' WHERE id_paciente = " . $id_usuario . " ";

			$this->ejecutarSentencia($sql, array());

			return 1;
		} catch (Exception $e) {
			return -2;
		}
	}

	public function validarIngresoResultados($documento, $clave) {
		try {
			$sql = "SELECT * FROM pacientes WHERE numero_documento = $documento AND clave_verificacion = $clave";

			$arrResultado = $this->getUnDato($sql);

			if (count($arrResultado) <= 0) {
				$sql = "SELECT 0 AS id_paciente, NULL AS nombre_1, NULL AS nombre_2, NULL AS apellido_1, NULL AS apellido_2";

				$arrResultado = $this->getUnDato($sql);
			}

			return $arrResultado;
		} catch (Exception $e) {
			return array();
		}
	}

	/**
	 * Tipo de formato:
	 * 1 - dd/mm/yyyy
	 * 2 - yyyy-mm-dd
	 * */
	public function getEdad($fecha_nacimiento, $fecha_base, $tipo_formato) {
		try {
			$formato = "";
			switch ($tipo_formato) {
				case 1: //dd/mm/yyyy
					$formato = "%d/%m/%Y";
					break;
				case 2: //yyyy-mm-dd
					$formato = "%Y-%m-%d";
					break;
			}
			if ($fecha_base != "") {
				$fecha_base = "STR_TO_DATE('" . $fecha_base . "', '" . $formato . "')";
			} else {
				$fecha_base = "CURDATE()";
			}
			$sql = "SELECT fu_calcular_edad(STR_TO_DATE('" . $fecha_nacimiento . "', '" . $formato . "'), " . $fecha_base . ") AS edad";

			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}

	public function crear_paciente($id_tipo_documento, $numero_documento, $nombre_1, $nombre_2, $apellido_1, $apellido_2, $sexo, 
			$fecha_nacimiento, $tipo_sangre, $factor_rh, $id_pais_nac, $cod_dep_nac, $cod_mun_nac, $nom_dep_nac, $nom_mun_nac, $id_pais, 
			$cod_dep, $cod_mun, $nom_dep, $nom_mun, $id_zona, $direccion, $email, $telefono_1, $telefono_2, $profesion, $id_estado_civil, 
			$ind_desplazado, $id_etnia, $id_usuario, $observ_paciente = "", $ind_habeas_data, $id_convenio = "", $status_convenio = "", $exento_pamo = "", 
			$rango = "", $tipo_cotizante = "", $id_plan) {
		try {
			if ($nombre_2 == "") {
				$nombre_2 = "NULL";
			} else {
				$nombre_2 = "'" . $nombre_2 . "'";
			}
			if ($apellido_2 == "") {
				$apellido_2 = "NULL";
			} else {
				$apellido_2 = "'" . $apellido_2 . "'";
			}
			if ($id_pais_nac == "1") {
				$nom_dep_nac = "";
				$nom_mun_nac = "";
			} else {
				$cod_dep_nac = "";
				$cod_mun_nac = "";
			}
			if ($cod_dep_nac == "") {
				$cod_dep_nac = "NULL";
			} else {
				$cod_dep_nac = "'" . $cod_dep_nac . "'";
			}
			if ($cod_mun_nac == "") {
				$cod_mun_nac = "NULL";
			} else {
				$cod_mun_nac = "'" . $cod_mun_nac . "'";
			}
			if ($nom_dep_nac == "") {
				$nom_dep_nac = "NULL";
			} else {
				$nom_dep_nac = "'" . $nom_dep_nac . "'";
			}
			if ($nom_mun_nac == "") {
				$nom_mun_nac = "NULL";
			} else {
				$nom_mun_nac = "'" . $nom_mun_nac . "'";
			}
			if ($id_pais == "1") {
				$nom_dep = "";
				$nom_mun = "";
			} else {
				$cod_dep = "";
				$cod_mun = "";
			}
			if ($cod_dep == "") {
				$cod_dep = "NULL";
			} else {
				$cod_dep = "'" . $cod_dep . "'";
			}
			if ($cod_mun == "") {
				$cod_mun = "NULL";
			} else {
				$cod_mun = "'" . $cod_mun . "'";
			}
			if ($nom_dep == "") {
				$nom_dep = "NULL";
			} else {
				$nom_dep = "'" . $nom_dep . "'";
			}
			if ($nom_mun == "") {
				$nom_mun = "NULL";
			} else {
				$nom_mun = "'" . $nom_mun . "'";
			}
			if ($telefono_2 == "") {
				$telefono_2 = "NULL";
			} else {
				$telefono_2 = "'" . $telefono_2 . "'";
			}
			if ($email == "") {
				$email = "NULL";
			} else {
				$email = "'" . $email . "'";
			}
			if ($ind_desplazado == "") {
				$ind_desplazado = "NULL";
			}
			if ($id_etnia == "") {
				$id_etnia = "NULL";
			}
			if ($observ_paciente == "") {
				$observ_paciente = "NULL";
			} else {
				$observ_paciente = "'" . $observ_paciente . "'";
			}
						 

			$id_estado_civil == "" ? $id_estado_civil = "NULL" : $id_estado_civil = "" . $id_estado_civil . "";
			$id_zona == "" ? $id_zona = "NULL" : $id_zona = "" . $id_zona . "";
			$tipo_sangre == "" ? $tipo_sangre = "NULL" : $tipo_sangre = "" . $tipo_sangre . "";
			$factor_rh == "" ? $factor_rh = "NULL" : $factor_rh = "" . $factor_rh . "";
			$id_pais_nac == "" ? $id_pais_nac = "NULL" : $id_pais_nac = "" . $id_pais_nac . "";
			$id_convenio == "" ? $id_convenio = "NULL" : $id_convenio = "" . $id_convenio . "";
			$status_convenio == "" ? $status_convenio = "NULL" : $status_convenio = "" . $status_convenio . "";
			$exento_pamo == "" ? $exento_pamo = "NULL" : $exento_pamo = "" . $exento_pamo . "";
			$rango == "" ? $rango = "NULL" : $rango = "" . $rango . "";
			$tipo_cotizante == "" ? $tipo_cotizante = "NULL" : $tipo_cotizante = "" . $tipo_cotizante . "";
			$id_plan == "" ? $id_plan = "NULL" : $id_plan = "" . $id_plan . "";		
			$ind_habeas_data == "" ? $ind_habeas_data = "NULL" : $ind_habeas_data = "" . $ind_habeas_data . "";		

			$sql = "CALL pa_crear_paciente(" . $id_tipo_documento . ", '" . $numero_documento . "', '" . $nombre_1 . "', " . $nombre_2 . ", '" .
					$apellido_1 . "', " . $apellido_2 . ", '" . $sexo . "', STR_TO_DATE('" . $fecha_nacimiento . "', '%d/%m/%Y'), " . $tipo_sangre . ", " . $factor_rh . ", " .
					$id_pais_nac . ", " . $cod_dep_nac . ", " . $cod_mun_nac . ", " . $nom_dep_nac . ", " . $nom_mun_nac . ", " . $id_pais . ", " . $cod_dep . ", " . $cod_mun . ", " .
					$nom_dep . ", " . $nom_mun . ", " . $id_zona . ", '" . $direccion . "', " . $email . ", '" . $telefono_1 . "', " . $telefono_2 . ", '" . $profesion . "', " .
					$id_estado_civil . ", " . $ind_desplazado . ", " . $id_etnia . ", " . $observ_paciente . ", 1, " . $id_usuario . ", " .
					$id_convenio . ", " . $status_convenio . ", " . $exento_pamo . ", " . $rango . ", " . $tipo_cotizante. ", " . $id_plan . ", ".$ind_habeas_data.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			$resultado_out = $arrResultado["@id"];

			return $resultado_out;
		} catch (Exception $e) {
			return array();
		}
	}

	public function cargarTmpPacientes($idUsuario, $idConvenio, $arrDatos, $idPlan) {
		try {
			$sql_base = "INSERT INTO tmp_pacientes
						(id_tipo_documento_tmp_paciente,doc_tmp_paciente,apellido_tmp_paciente,
						apellido2_tmp_paciente,nom_tmp_paciente,nom2_tmp_paciente,tipo_afi_tmp_paciente,sexo_tmp_paciente,dir_tmp_paciente,tel_tmp_paciente,
						fecha_nacimiento_tmp_paciente,rango_tmp_paciente,dep_tmp_paciente,mun_tmp_paciente,exento_tmp_paciente,convenio_tmp_paciente,
						pais_tmp_paciente,status_tmp_paciente,id_tmp_plan) VALUES ";
			
			$sql = "";
			
			switch ($idConvenio) {
				case "48": //Nueva EPS
					foreach ($arrDatos as $datoAux) {
						if (strlen(trim($datoAux["tipoDocumento"])) <= 0 || strlen(trim($datoAux["documento"])) <= 0) {
							continue;
						}
						
						switch ($datoAux["tipoDocumento"]) {
							case "1":
								$datoAux["tipoDocumento"] = 6; //Extranjería
								break;
							case "2":
								$datoAux["tipoDocumento"] = 4; //T. Identidad
								break;
							case "3":
								$datoAux["tipoDocumento"] = 5; //C. ciudadanía	
								break;
							case "5":
								$datoAux["tipoDocumento"] = 3; //Registro civil
								break;
							case "6":
								$datoAux["tipoDocumento"] = 7; //Pasaporte
								break;
							case "7":
								$datoAux["tipoDocumento"] = 8; //Menor sin identificación			
								break;
							case "11":
								$datoAux["tipoDocumento"] = 10; //Certificado de nacido vivo
								break;
							case "13":
								$datoAux["tipoDocumento"] = 463; //Permiso especial
								break;
							default:
								$datoAux["tipoDocumento"] = "NULL";
								break;
						}

						$posAux = strpos($datoAux["nombre"], " ");
						if ($posAux !== false) {
							$datoAux["nombre2"] = substr($datoAux["nombre"], $posAux + 1);
							$datoAux["nombre"] = substr($datoAux["nombre"], 0, $posAux);
						}

						if ($datoAux["nombre2"] != "") {
							$datoAux["nombre2"] = "'" . $datoAux["nombre2"] . "'";
						} else {
							$datoAux["nombre2"] = "NULL";
						}

						if ($datoAux["apellido2"] != "") {
							$datoAux["apellido2"] = "'" . $datoAux["apellido2"] . "'";
						} else {
							$datoAux["apellido2"] = "NULL";
						}

						//Se homologan los tipos de cotizantes que se tienen en la base de datos.
						if ($datoAux["rango"] == "6" || $datoAux["rango"] == "7") {
							
							$datoAux["tipoAfi"] = "661";
						}
						else if ($datoAux["tipoAfi"] == "1") {
							
							$datoAux["tipoAfi"] = "658";
						}
						else if ($datoAux["tipoAfi"] == "2") {
							$datoAux["tipoAfi"] = "659";
						}
						else if ($datoAux["tipoAfi"] == "3") {
							$datoAux["tipoAfi"] = "660";
						}
					
						switch ($datoAux["sexo"]) {
							case "M":
								$datoAux["sexo"] = "2";
								break;
							case "F":
								$datoAux["sexo"] = "1";
								break;
							default:
								$datoAux["sexo"] = "NULL";
								break;
						}

						$tel_aux = mb_strimwidth(trim($datoAux["tel"]), 0, 20);
						if (strlen(trim($tel_aux)) <= 0) {
							$tel_aux = "NA";
						}
						
						switch ($datoAux["rango"]) {
							case "1":
								$datoAux["rango"] = 1; //Rango 1
								break;
							case "2":
								$datoAux["rango"] = 2; //Rango 2
								break;
							case "3":
								$datoAux["rango"] = 3; //Rango 3
								break;
							case "6":
								$datoAux["rango"] = 6; //Rango 6 o N1 para subsidiados.
								break;
							case "7":
								$datoAux["rango"] = 7;//Rango 6 o N2 para subsidiados.
								break;
							default:
								$datoAux["rango"] = 0; //No aplica.
								break;
						}

						if (strlen($datoAux["dep"]) == 1) {
							$datoAux["dep"] = "0" . $datoAux["dep"];
						}

						switch (strlen($datoAux["mun"])) {
							case 0:
								$datoAux["mun"] = "NULL";
								break;
							case 1:
								$datoAux["mun"] = "'" . $datoAux["dep"] . "00" . $datoAux["mun"] . "'";
								break;
							case 2:
								$datoAux["mun"] = "'" . $datoAux["dep"] . "0" . $datoAux["mun"] . "'";
								break;
							default:
								$datoAux["mun"] = "'" . $datoAux["dep"] . $datoAux["mun"] . "'";
								break;
						}

						if ($datoAux["dep"] != "") {
							$datoAux["dep"] = "'" . $datoAux["dep"] . "'";
						} else {
							$datoAux["dep"] = "NULL";
						}

						if (strlen(trim($datoAux["exento"])) > 0) {
							$datoAux["exento_post"] = 0;
						} else {
							$datoAux["exento_post"] = 1;
						}
						
						if ($datoAux["tipoDocumento"] != "NULL") {
							if ($sql != "") {
								$sql .= ",";
							}
							$sql .= "(" . $datoAux["tipoDocumento"] . ", '" . $datoAux["documento"] . "', '" . $datoAux["apellido"] . "', " . $datoAux["apellido2"] . ", '" . $datoAux["nombre"] . "', " .
									$datoAux["nombre2"] . ", " . $datoAux["tipoAfi"] . ", " . $datoAux["sexo"] . ", '" . $datoAux["dir"] . "', '" . $tel_aux . "', " .
									"STR_TO_DATE('" . $datoAux["fechaNac"] . "', '%d/%m/%Y'), " . $datoAux["rango"] . ", " . $datoAux["dep"] . ", " . $datoAux["mun"] . ", " .
									$datoAux["exento_post"] . ", " . $idConvenio . ", 1, 1," . $idPlan . ")";
						}
					}
					break;
					
				case "49": //Avanzar FOS
					foreach ($arrDatos as $datoAux) {
						if (strlen(trim($datoAux["tipoDocumento"])) <= 0 || strlen(trim($datoAux["documento"])) <= 0) {
							continue;
						}

						switch ($datoAux["tipoDocumento"]) {
							case "ce":
							case "CE":
								$datoAux["tipoDocumento"] = 6; //Extranjería
								break;
							case "ti":
							case "TI":
								$datoAux["tipoDocumento"] = 4; //T. Identidad
								break;
							case "cc":
							case "CC":
								$datoAux["tipoDocumento"] = 5; //C. ciudadanía	
								break;
							case "rc":
							case "RC":
								$datoAux["tipoDocumento"] = 3; //Registro civil
								break;
							case "pa":
							case "PA":
								$datoAux["tipoDocumento"] = 7; //Pasaporte
								break;
							case "nu":
							case "NU":
								$datoAux["tipoDocumento"] = 439; //Número único			
								break;
							default:
								$datoAux["tipoDocumento"] = "NULL";
								break;
						}

						switch ($datoAux["sexo"]) {
							case "m":
							case "M":
								$datoAux["sexo"] = "2";
								break;
							case "f":
							case "F":
								$datoAux["sexo"] = "1";
								break;
							default:
								$datoAux["sexo"] = "NULL";
								break;
						}

						$tipoAfiliado = 0;
						switch ($datoAux["tipoAfi"]) {
							case "ben":
							case "BEN":
								$tipoAfiliado = "2"; //Beneficiario
								break;
							case "afi":
							case "AFI":
								$tipoAfiliado = "1"; //Cotizante
								break;
							default:
								$tipoAfiliado = "0"; //No aplica
								break;
						}


						if (strlen($datoAux["dep"]) == 1) {
							$datoAux["dep"] = "0" . $datoAux["dep"];
						}

						switch (strlen($datoAux["mun"])) {
							case 0:
								$datoAux["mun"] = "NULL";
								break;
							case 1:
								$datoAux["mun"] = "'" . $datoAux["dep"] . "00" . $datoAux["mun"] . "'";
								break;
							case 2:
								$datoAux["mun"] = "'" . $datoAux["dep"] . "0" . $datoAux["mun"] . "'";
								break;
							default:
								$datoAux["mun"] = "'" . $datoAux["dep"] . $datoAux["mun"] . "'";
								break;
						}

						if ($datoAux["dep"] != "") {
							$datoAux["dep"] = "'" . $datoAux["dep"] . "'";
						} else {
							$datoAux["dep"] = "NULL";
						}

						if (strlen(trim($datoAux["exento"])) > 0) {
							$datoAux["exento_post"] = 0;
						} else {
							$datoAux["exento_post"] = 1;
						}

						$tel_aux = mb_strimwidth(trim($datoAux["tel"]), 0, 20);
						if (strlen(trim($tel_aux)) <= 0) {
							$tel_aux = "NA";
						}

						if ($sql != "") {
							$sql .= ",";
						}
						$sql .= "(" . $datoAux["tipoDocumento"] . ", '" . $datoAux["documento"] . "', '" . $datoAux["apellido"] . "', '" . $datoAux["apellido2"] . "', '" . $datoAux["nombre"] . "', '" .
								$datoAux["nombre2"] . "', " . $tipoAfiliado . ", " . $datoAux["sexo"] . ", '" . $datoAux["dir"] . "', '" . $tel_aux . "', " .
								"STR_TO_DATE('" . $datoAux["fechaNac"] . "', '%d/%m/%Y'), 0, " . $datoAux["dep"] . ", " . $datoAux["mun"] . ", 1, " . $idConvenio . ", 1, 1," . $idPlan . ")";
					}
					break;
			}
			
			$sql = $sql_base . $sql;
			//echo($sql."<br />");
			$this->ejecutarSentencia($sql, array());
		

			return 1;
		} catch (Exception $e) {
			return -2;
		}
	}

	public function eliminarTmpPacientes() {
		try {

			$sql = "DELETE
						FROM tmp_pacientes;";

			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			$resultado_out = $arrResultado["@id"];

			return $resultado_out;
		} catch (Exception $e) {
			return array();
		}
	}

	public function importarTmpPacientes($idUsuario, $idConvenio, $idPlan) {
		try {

			$idUsuario == "" ? $idUsuario = "NULL" : $idUsuario = "" . $idUsuario . "";
			$idConvenio == "" ? $idConvenio = "NULL" : $idConvenio = "" . $idConvenio . "";
			$idPlan == "" ? $idPlan = "NULL" : $idPlan = "" . $idPlan . "";

			$sql = "CALL pa_importar_pacientes(" . $idUsuario . "," . $idConvenio . "," . $idPlan . ", @id)";
			//echo($sql);

			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			$resultado_out = $arrResultado["@id"];

			return $resultado_out;
		} catch (Exception $e) {
			return array();
		}
	}

	//Función que obtiene los datos del paciente y el estado del mismo para un convenio dado
	public function getPacienteEstadoConvenio($id_paciente, $id_convenio) {
		try {
			$sql = "SELECT P.*, CASE WHEN P.id_convenio_paciente=" . $id_convenio . "
							THEN P.status_convenio_paciente
							ELSE NULL
						END AS status_convenio, CASE WHEN P.id_convenio_paciente=" . $id_convenio . "
							THEN
								CASE P.status_convenio_paciente
									WHEN 1 THEN 'Activo'
									WHEN 2 THEN 'Inactivo'
									WHEN 3 THEN 'Atención especial'
									WHEN 4 THEN 'Retirado'
									ELSE 'No registrado/No aplica'
								END
							ELSE 'No registrado/No aplica'
						END AS PstatusC
						FROM pacientes P
						WHERE P.id_paciente=" . $id_paciente;

			//echo $sql;
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	public function reportePacientesAVSCLejos($fecha_inicial, $fecha_final, $id_convenio) {//Consulta si en la cita el campo id_paciente existe
		try {
			$sql = "SELECT TD.nombre_detalle AS tipo_documento, P.numero_documento, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2,
					P.fecha_nacimiento, CASE WHEN P.sexo = 1 THEN 'Femenino' WHEN P.sexo = 2 THEN 'Masculino' END AS sexo,
					IFNULL(AVOI.nombre_detalle, 0) AS avsc_lejos_oi, IFNULL(AVOD.nombre_detalle, 0) AS avsc_lejos_od, 
					DATE_FORMAT(CO.fecha_crea, '%d/%m/%Y  %H:%i:%s') AS fecha_valoracion
					FROM pacientes P
					INNER JOIN historia_clinica HC ON HC.id_paciente = P.id_paciente
					INNER JOIN consultas_optometria CO ON CO.id_hc = HC.id_hc
					INNER JOIN listas_detalle TD ON TD.id_detalle = P.id_tipo_documento
					LEFT JOIN listas_detalle AVOI ON AVOI.id_detalle = CO.avsc_lejos_oi
					LEFT JOIN listas_detalle AVOD ON AVOD.id_detalle = CO.avsc_lejos_od
					
					WHERE P.id_convenio_paciente = $id_convenio AND CO.fecha_crea BETWEEN '$fecha_inicial 00:00:00' AND '$fecha_final 23:59:59'
					
					UNION ALL
					
					SELECT TD.nombre_detalle AS tipo_documento, P.numero_documento, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2,
					P.fecha_nacimiento, CASE WHEN P.sexo = 1 THEN 'Femenino' WHEN P.sexo = 2 THEN 'Masculino' END AS sexo,
					IFNULL(AVOI.nombre_detalle, 0) AS avsc_lejos_oi, IFNULL(AVOD.nombre_detalle, 0) AS avsc_lejos_od, 
					DATE_FORMAT(CO.fecha_crea, '%d/%m/%Y  %H:%i:%s') AS fecha_valoracion
					
					FROM pacientes P
					INNER JOIN historia_clinica HC ON HC.id_paciente = P.id_paciente
					INNER JOIN consultas_control_optometria CO ON CO.id_hc = HC.id_hc
					INNER JOIN listas_detalle TD ON TD.id_detalle = P.id_tipo_documento
					LEFT JOIN listas_detalle AVOI ON AVOI.id_detalle = CO.avsc_lejos_oi
					LEFT JOIN listas_detalle AVOD ON AVOD.id_detalle = CO.avsc_lejos_od
					WHERE P.id_convenio_paciente = $id_convenio AND CO.fecha_crea BETWEEN '$fecha_inicial 00:00:00' AND '$fecha_final 23:59:59';";

			//echo($sql);
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}

}

?>
