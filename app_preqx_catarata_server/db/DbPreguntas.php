<?php
require_once("../db/DbConexion.php");
//require_once("DbConexion.php");

class DbPreguntas extends DbConexion {
	public function getListaPreguntas() {
		try {
			$sql = "SELECT P.*
					FROM preguntas P
					ORDER BY P.cod_pregunta";
			
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	public function getPregunta($id_pregunta) {
		try {
			$sql = "SELECT P.*
					FROM preguntas P
					WHERE id_pregunta = id_pregunta
					ORDER BY P.cod_pregunta";
			
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	
	public function doGuardarRespuestas($rta_p1, $rta_p2, $rta_p3, $rta_p4, $rta_p5, $nombre, $cedula, $id_ojo, $id_paciente, $id_hc, $fecha_hora_hc, $id_seguimiento) {
		try {
		
			$sql = "INSERT INTO respuestas (id_pregunta, id_respuesta, nombre_paciente, cedula_paciente, id_ojo, id_paciente, fecha_respuesta, id_hc, fecha_hora_hc, id_seguimiento, ind_estado)
					VALUES ('1', $rta_p1, '".$nombre."', '".$cedula."', ".$id_ojo.", ".$id_paciente.", NOW(), ".$id_hc.", '".$fecha_hora_hc."', ".$id_seguimiento.", '1'), 
						   ('2', $rta_p2, '".$nombre."', '".$cedula."', ".$id_ojo.", ".$id_paciente.", NOW(), ".$id_hc.", '".$fecha_hora_hc."', ".$id_seguimiento.", '1'), 
						   ('3', $rta_p3, '".$nombre."', '".$cedula."', ".$id_ojo.", ".$id_paciente.", NOW(), ".$id_hc.", '".$fecha_hora_hc."', ".$id_seguimiento.", '1'), 
						   ('4', $rta_p4, '".$nombre."', '".$cedula."', ".$id_ojo.", ".$id_paciente.", NOW(), ".$id_hc.", '".$fecha_hora_hc."', ".$id_seguimiento.", '1'), 
						   ('5', $rta_p5, '".$nombre."', '".$cedula."', ".$id_ojo.", ".$id_paciente.", NOW(), ".$id_hc.", '".$fecha_hora_hc."', ".$id_seguimiento.", '1')";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			$id_respuestas = $arrResultado["@id"];
			return $id_respuestas;
			
		} catch (Exception $e) {
		
			return array();
		}
	}
	
	
	
	
	public function sincronizarRespuestas($arr_datos_reg, $arr_datos) {
		try {
			
		//Se agregan los registros a la tabla temporal
		$cadena_values='';
		if (count($arr_datos) > 0) {
			$j=1;
			foreach ($arr_datos as $registro_aux) {
				$id_pregunta = $registro_aux["id_pregunta"];
				$id_respuesta = $registro_aux["id_respuesta"];
				$nombre_paciente = $registro_aux["nombre_paciente"];
				$cedula_paciente = $registro_aux["cedula_paciente"];
				$id_ojo = $registro_aux["id_ojo"];
				$id_paciente = $registro_aux["id_paciente"];
				$fecha_respuesta = $registro_aux["fecha_respuesta"];
				$id_hc = $registro_aux["id_hc"];
				$fecha_hora_hc = $registro_aux["fecha_hora_hc"];
				$id_seguimiento = $registro_aux["id_seguimiento"];
				$cod_respuestas = $registro_aux["cod_respuestas"];
				
				if($j == count($arr_datos)){
					$cadena_values= $cadena_values.'('.$id_pregunta.', '.$id_respuesta.', "'.$nombre_paciente.'", "'.$cedula_paciente.'", '.$id_ojo.', '.$id_paciente.', "'.$fecha_respuesta.'", '.$id_hc.', "'.$fecha_hora_hc.'", '.$id_seguimiento.', "1", '.$cod_respuestas.') ';
				}
				else{
					$cadena_values= $cadena_values.'('.$id_pregunta.', '.$id_respuesta.', "'.$nombre_paciente.'", "'.$cedula_paciente.'", '.$id_ojo.', '.$id_paciente.', "'.$fecha_respuesta.'", '.$id_hc.', "'.$fecha_hora_hc.'", '.$id_seguimiento.', "1", '.$cod_respuestas.'), ';
				}
				
				$j++;
			}
		}
		
		$cadena_values_reg='';
		if (count($arr_datos_reg) > 0) {
			$i=1;
			foreach ($arr_datos_reg as $registro_aux_reg) {
				$cod_respuestas = $registro_aux_reg["cod_respuestas"];
				$valor_imagen = $registro_aux_reg["valor_imagen"];
				$nombre_paciente = $registro_aux_reg["nombre_paciente"];
				$cedula_paciente = $registro_aux_reg["cedula_paciente"];
				$id_ojo = $registro_aux_reg["id_ojo"];
				$id_paciente = $registro_aux_reg["id_paciente"];
				$fecha_respuesta = $registro_aux_reg["fecha_respuesta"];
				$id_hc = $registro_aux_reg["id_hc"];
				$fecha_hora_hc = $registro_aux_reg["fecha_hora_hc"];
				$id_seguimiento = $registro_aux_reg["id_seguimiento"];
				
				if($i == count($arr_datos_reg)){
					$cadena_values_reg= $cadena_values_reg.'('.$cod_respuestas.', "'.$valor_imagen.'", "'.$nombre_paciente.'", "'.$cedula_paciente.'", '.$id_ojo.', '.$id_paciente.', "'.$fecha_respuesta.'", '.$id_hc.', "'.$fecha_hora_hc.'", '.$id_seguimiento.', "1") ';
				}
				else{
					$cadena_values_reg= $cadena_values_reg.'('.$cod_respuestas.', "'.$valor_imagen.'", "'.$nombre_paciente.'", "'.$cedula_paciente.'", '.$id_ojo.', '.$id_paciente.', "'.$fecha_respuesta.'", '.$id_hc.', "'.$fecha_hora_hc.'", '.$id_seguimiento.', "1"), ';
				}
				
				$i++;
			}
		}
		
		
		
		
			$sql = "INSERT INTO respuestas (id_pregunta, id_respuesta, nombre_paciente, cedula_paciente, id_ojo, id_paciente, fecha_respuesta, id_hc, fecha_hora_hc, id_seguimiento, ind_estado, cod_respuestas)
					VALUES ".$cadena_values;
					
			$sql_reg = "INSERT INTO registro_foto (cod_respuestas, valor_imagen, nombre_paciente, cedula_paciente, id_ojo, id_paciente, fecha_respuesta, id_hc, fecha_hora_hc, id_seguimiento, ind_estado)
					VALUES ".$cadena_values_reg;		
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			$arrResultado_reg = $this->ejecutarSentencia($sql_reg, $arrCampos);
			
			return 1;
			
		
		} catch (Exception $e) {
			return -1;
		}
	}
	
	
	public function sincronizarRegistroFoto($arr_datos) {
		try {
			
		//Se agregan los registros a la tabla temporal
		$cadena_values='';
		if (count($arr_datos) > 0) {
			$j=1;
			foreach ($arr_datos as $registro_aux) {
				$cod_respuestas = $registro_aux["cod_respuestas"];
				$valor_imagen = $registro_aux["valor_imagen"];
				$nombre_paciente = $registro_aux["nombre_paciente"];
				$cedula_paciente = $registro_aux["cedula_paciente"];
				$id_ojo = $registro_aux["id_ojo"];
				$id_paciente = $registro_aux["id_paciente"];
				$fecha_respuesta = $registro_aux["fecha_respuesta"];
				$id_hc = $registro_aux["id_hc"];
				$fecha_hora_hc = $registro_aux["fecha_hora_hc"];
				$id_seguimiento = $registro_aux["id_seguimiento"];
				
				if($j == count($arr_datos)){
					$cadena_values= $cadena_values.'('.$cod_respuestas.', "'.$valor_imagen.'", "'.$nombre_paciente.'", "'.$cedula_paciente.'", '.$id_ojo.', '.$id_paciente.', "'.$fecha_respuesta.'", '.$id_hc.', "'.$fecha_hora_hc.'", '.$id_seguimiento.', "1") ';
				}
				else{
					$cadena_values= $cadena_values.'('.$cod_respuestas.', "'.$valor_imagen.'", "'.$nombre_paciente.'", "'.$cedula_paciente.'", '.$id_ojo.', '.$id_paciente.', "'.$fecha_respuesta.'", '.$id_hc.', "'.$fecha_hora_hc.'", '.$id_seguimiento.', "1"), ';
				}
				
				$j++;
			}
		}
		
			$sql = "INSERT INTO registro_foto (cod_respuestas, valor_imagen, nombre_paciente, cedula_paciente, id_ojo, id_paciente, fecha_respuesta, id_hc, fecha_hora_hc, id_seguimiento, ind_estado)
					VALUES ".$cadena_values;
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			return 1;
			
		
		} catch (Exception $e) {
			return -1;
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>
