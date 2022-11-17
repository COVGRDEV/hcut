<?php
	header('Content-type: application/json; charset=utf-8');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
	
	require("json/JSON_WebService.php");
	
	/*require("db/DbItemsIndicadores.php");
	require("db/DbVariables.php");
	require("db/DbMunicipios.php");
	require("db/DbIndicadores.php");*/
	require("db/DbLlaves.php");	
	require("db/DbPreguntas.php");
	require("db/DbPacientesClave.php");
	
	
	//Se obtiene la llave de la base de datos
	$dbLlaves = new DbLlaves();
	$llaveObj = $dbLlaves->getLlave();
	
	//Obtiene el contenido de la solicitud POST
	$clientData = file_get_contents("php://input");
	
	//Instancia de la clase JSON_WebService
	$server = new JSON_WebService($llaveObj["key_value"], $clientData);
	
	//Registra los metodos del servicio web
	/*$server->register("obtenerListaItems");
	$server->register("obtenerVariablesItem");
	$server->register("obtenerMunicipios");
	$server->register("obtenerValoresItem");*/
	
	$server->register("obtenerListaPreguntas");
	$server->register("guardarPreguntasRespuestas");
	$server->register("obtenerListaPacientes");
	$server->register("validarClavePaciente");	
	$server->register("obtenerListasDB");
	$server->register("sincronizarRespuestas");
	$server->register("sincronizarRegistroFoto");
	
	
	//Inicializa el servicio
	$server->start();
	
	//Define los metodos del servicio web
	
	
	function obtenerListaPreguntas() {
		$dbPreguntas = new DbPreguntas();
		$listaPreguntas = $dbPreguntas->getListaPreguntas();
		return $listaPreguntas;
	}
	
	function guardarPreguntasRespuestas($rta_p1, $rta_p2, $rta_p3, $rta_p4, $rta_p5, $nombre, $cedula, $id_ojo, $id_paciente, $id_hc, $fecha_hora_hc, $id_seguimiento){
		$dbPreguntas = new DbPreguntas();
		$respuestas = $dbPreguntas->doGuardarRespuestas($rta_p1, $rta_p2, $rta_p3, $rta_p4, $rta_p5, $nombre, $cedula, $id_ojo, $id_paciente, $id_hc, $fecha_hora_hc, $id_seguimiento);
		return $respuestas;
	}
	
	
	function obtenerListaPacientes($documento) {
		$pacientes = new DbPacientesClave();
		$resultado = $pacientes->getBuscarpacientesVinculados($documento);
		return $resultado;
	}
	
	
	function validarClavePaciente($id_paciente, $clave) {
		$pacientes = new DbPacientesClave();
		$resultados = $pacientes->validarClavePaciente($id_paciente, $clave);
		return $resultados;
	}
	
	//Define los metodos del servicio web
	function obtenerListasDB() {
		$listaPreguntas = obtenerListaPreguntas();		
		$listaDB = array();
		$listaDB["preguntas"] = $listaPreguntas;		
		return $listaDB;
	}
	
	
	function sincronizarRespuestas($cadena_datos_reg, $cadena_datos) {
		
		$arr_datos = array();
		$lista_datos = explode("|", $cadena_datos);
		foreach ($lista_datos as $cadena_aux) {
			if ($cadena_aux != "") {
				$arr_aux = explode("#", $cadena_aux);
				
				$registro_aux = array();
				$registro_aux["id_pregunta"] = $arr_aux[0];
				$registro_aux["id_respuesta"] = $arr_aux[1];
				$registro_aux["nombre_paciente"] = $arr_aux[2];
				$registro_aux["cedula_paciente"] = $arr_aux[3];
				$registro_aux["id_ojo"] = $arr_aux[4];
				$registro_aux["id_paciente"] = $arr_aux[5];
				$registro_aux["fecha_respuesta"] = $arr_aux[6];
				$registro_aux["id_hc"] = $arr_aux[7];
				$registro_aux["fecha_hora_hc"] = $arr_aux[8];
				$registro_aux["id_seguimiento"] = $arr_aux[9];
				$registro_aux["cod_respuestas"] = $arr_aux[10];
				
				array_push($arr_datos, $registro_aux);
			}
		}
		
		
		$arr_datos_reg = array();
		$lista_datos_reg = explode("|", $cadena_datos_reg);
		foreach ($lista_datos_reg as $cadena_aux_reg) {
			if ($cadena_aux_reg != "") {
				$arr_aux_reg = explode("#", $cadena_aux_reg);
				
				$registro_aux_reg = array();
				$registro_aux_reg["cod_respuestas"] = $arr_aux_reg[0];
				$registro_aux_reg["valor_imagen"] = $arr_aux_reg[1];
				$registro_aux_reg["nombre_paciente"] = $arr_aux_reg[2];
				$registro_aux_reg["cedula_paciente"] = $arr_aux_reg[3];
				$registro_aux_reg["id_ojo"] = $arr_aux_reg[4];
				$registro_aux_reg["id_paciente"] = $arr_aux_reg[5];
				$registro_aux_reg["fecha_respuesta"] = $arr_aux_reg[6];
				$registro_aux_reg["id_hc"] = $arr_aux_reg[7];
				$registro_aux_reg["fecha_hora_hc"] = $arr_aux_reg[8];
				$registro_aux_reg["id_seguimiento"] = $arr_aux_reg[9];
				
				array_push($arr_datos_reg, $registro_aux_reg);
			}
		}
		
		
		$dbPreguntas = new DbPreguntas();
		
		$resultado = $dbPreguntas->sincronizarRespuestas($arr_datos_reg, $arr_datos);
		
		return array("resultado" => $resultado);
		//return $cadena_datos;
	}
	
	
	function sincronizarRegistroFoto($cadena_datos) {
		
		$arr_datos = array();
		$lista_datos = explode("|", $cadena_datos);
		foreach ($lista_datos as $cadena_aux) {
			if ($cadena_aux != "") {
				$arr_aux = explode("#", $cadena_aux);
				
				$registro_aux = array();
				$registro_aux["cod_respuestas"] = $arr_aux[0];
				$registro_aux["valor_imagen"] = $arr_aux[1];
				$registro_aux["nombre_paciente"] = $arr_aux[2];
				$registro_aux["cedula_paciente"] = $arr_aux[3];
				$registro_aux["id_ojo"] = $arr_aux[4];
				$registro_aux["id_paciente"] = $arr_aux[5];
				$registro_aux["fecha_respuesta"] = $arr_aux[6];
				$registro_aux["id_hc"] = $arr_aux[7];
				$registro_aux["fecha_hora_hc"] = $arr_aux[8];
				$registro_aux["id_seguimiento"] = $arr_aux[9];
				
				array_push($arr_datos, $registro_aux);
			}
		}
		
		$dbPreguntas = new DbPreguntas();
		
		$resultado = $dbPreguntas->sincronizarRegistroFoto($arr_datos);
		
		return array("resultado" => $resultado);
		//return $cadena_datos;
	}
	
	
	
	
	
	
	
	
	
?>
