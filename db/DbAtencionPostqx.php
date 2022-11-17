<?php
	require_once("DbConexion.php");
	
	class DbAtencionPostqx extends DbConexion {
		                
		public function vincular_paciente_postqx($hdd_id_paciente, $hdd_id_seguimiento, $txt_fecha_primer, $txt_fecha_segundo, $txt_fecha_tercer, $cmb_ojos, $cmb_sino_voz, $cmb_sino_excluye, $id_usuario_crea, $clave_verificacion) {
			try {
				$sql = "CALL pa_crear_editar_seguimiento_postqx(".$hdd_id_paciente.", ".$hdd_id_seguimiento.", STR_TO_DATE('".$txt_fecha_primer. "', '%d/%m/%Y %H:%i:%s'), STR_TO_DATE('".$txt_fecha_segundo. "', '%d/%m/%Y %H:%i:%s'), STR_TO_DATE('".$txt_fecha_tercer. "', '%d/%m/%Y'), '".$cmb_ojos."', '".$cmb_sino_voz."', '".$cmb_sino_excluye."', '".$clave_verificacion."', ".$id_usuario_crea.",  @id)";
				//echo $sql;
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$resultado = $arrResultado["@id"];
				
				return $resultado;
			} catch (Exception $e) {
				return -2;
			}
		}
		
		
		public function getPacienteVinculado($id_paciente) {
			try {
				$sql = "SELECT DATE_FORMAT(fecha_seguimiento_uno, '%H:%i:%s') AS hora_preferible, DATE_FORMAT(s.fecha_seguimiento_uno, '%d/%m/%Y') AS fecha_seguimiento_uno_t, DATE_FORMAT(s.fecha_seguimiento_dos, '%d/%m/%Y') AS fecha_seguimiento_dos_t, DATE_FORMAT(s.fecha_seguimiento_tres, '%d/%m/%Y') AS fecha_seguimiento_tres_t,
						lo.nombre_detalle AS id_ojo_t, lv.nombre_detalle AS ind_voz_t, li.nombre_detalle AS ind_inclusion_t, s.clave_verificacion, s.id_seguimiento, s.*
						FROM seguimiento_postqx_catarata s 
						INNER JOIN listas_detalle lo ON lo.id_detalle = s.id_ojos
						INNER JOIN listas_detalle lv ON lv.id_detalle = s.ind_voz
						INNER JOIN listas_detalle li ON li.id_detalle = s.ind_inclusion
						WHERE s.id_paciente = ".$id_paciente;
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		
		public function getSeguimientoPaciente($id_seguimiento) {
			try {
				$sql = "SELECT *, DATE_FORMAT(fecha_seguimiento_uno, '%H:%i:%s') AS hora_preferible, DATE_FORMAT(fecha_seguimiento_uno, '%d/%m/%Y') AS fecha_seguimiento_uno_t, DATE_FORMAT(fecha_seguimiento_dos, '%d/%m/%Y') AS fecha_seguimiento_dos_t, DATE_FORMAT(fecha_seguimiento_tres, '%d/%m/%Y') AS fecha_seguimiento_tres_t 
						FROM seguimiento_postqx_catarata WHERE id_seguimiento = ".$id_seguimiento;
				//echo $sql;
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		
		public function getRespuestaPaciente($id_persona) {
			try {
				//$sql = "SELECT *, DATE_FORMAT(fecha_respuesta, '%d/%m/%Y') AS fecha_respuesta_t FROM respuestas WHERE id_paciente = ".$id_persona." ORDER BY id_paciente, id";
				$sql = "SELECT 
						(SELECT r1.id_respuesta FROM respuestas r1 WHERE r1.id_paciente = ".$id_persona." AND r1.cod_respuestas = re.cod_respuestas AND r1.id_pregunta = 1) AS pregunta_1,
						(SELECT r2.id_respuesta FROM respuestas r2 WHERE r2.id_paciente = ".$id_persona." AND r2.cod_respuestas = re.cod_respuestas AND r2.id_pregunta = 2) AS pregunta_2,
						(SELECT r3.id_respuesta FROM respuestas r3 WHERE r3.id_paciente = ".$id_persona." AND r3.cod_respuestas = re.cod_respuestas AND r3.id_pregunta = 3) AS pregunta_3,
						(SELECT r4.id_respuesta FROM respuestas r4 WHERE r4.id_paciente = ".$id_persona." AND r4.cod_respuestas = re.cod_respuestas AND r4.id_pregunta = 4) AS pregunta_4,
						(SELECT r5.id_respuesta FROM respuestas r5 WHERE r5.id_paciente = ".$id_persona." AND r5.cod_respuestas = re.cod_respuestas AND r5.id_pregunta = 5) AS pregunta_5,
						re.*, DATE_FORMAT(re.fecha_respuesta, '%d/%m/%Y') AS fecha_respuesta_t
						FROM registro_foto re WHERE re.id_paciente = ".$id_persona." GROUP BY re.cod_respuestas";
				//echo $sql."<br /> ----";
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		
		public function getPacientesMatriculados() {
			try {
				/*$sql = "SELECT p.*, s.*, DATE_FORMAT(s.fecha_seguimiento_uno, '%d-%m-%Y') AS fecha_seguimiento_ini,
						l.nombre_detalle AS tipo_documento, l.codigo_detalle AS cod_tipo_documento, DATE_FORMAT(p.fecha_nacimiento, '%d/%m/%Y') AS fecha_nac_persona  
						FROM seguimiento_postqx_catarata s
						INNER JOIN pacientes p ON p.id_paciente = s.id_paciente
						INNER JOIN listas_detalle l ON l.id_detalle = p.id_tipo_documento
						ORDER BY s.fecha_seguimiento_uno";*/
				$sql = "SELECT p.*, s.*, DATE_FORMAT(s.fecha_seguimiento_uno, '%d-%m-%Y') AS fecha_seguimiento_ini,
						l.nombre_detalle AS tipo_documento, l.codigo_detalle AS cod_tipo_documento, DATE_FORMAT(p.fecha_nacimiento, '%d/%m/%Y') AS fecha_nac_persona  
						FROM seguimiento_postqx_catarata s
						INNER JOIN pacientes p ON p.id_paciente = s.id_paciente
						INNER JOIN listas_detalle l ON l.id_detalle = p.id_tipo_documento
						WHERE s.estado = 1
						ORDER BY s.fecha_seguimiento_uno";		
				//echo $sql;		
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		
		public function getRespuestaPacienteFecha($id_persona, $fecha) {
			try {
				//$sql = "SELECT *, DATE_FORMAT(fecha_respuesta, '%d/%m/%Y') AS fecha_respuesta_t FROM respuestas WHERE id_paciente = ".$id_persona." ORDER BY id_paciente, id";
				$sql = "SELECT 
						(SELECT r1.id_respuesta FROM respuestas r1 WHERE r1.id_paciente = ".$id_persona." AND r1.cod_respuestas = re.cod_respuestas AND r1.id_pregunta = 1) AS pregunta_1,
						(SELECT r2.id_respuesta FROM respuestas r2 WHERE r2.id_paciente = ".$id_persona." AND r2.cod_respuestas = re.cod_respuestas AND r2.id_pregunta = 2) AS pregunta_2,
						(SELECT r3.id_respuesta FROM respuestas r3 WHERE r3.id_paciente = ".$id_persona." AND r3.cod_respuestas = re.cod_respuestas AND r3.id_pregunta = 3) AS pregunta_3,
						(SELECT r4.id_respuesta FROM respuestas r4 WHERE r4.id_paciente = ".$id_persona." AND r4.cod_respuestas = re.cod_respuestas AND r4.id_pregunta = 4) AS pregunta_4,
						(SELECT r5.id_respuesta FROM respuestas r5 WHERE r5.id_paciente = ".$id_persona." AND r5.cod_respuestas = re.cod_respuestas AND r5.id_pregunta = 5) AS pregunta_5,
						re.id, re.cod_respuestas, re.nombre_paciente, re.cedula_paciente, re.id_ojo, re.id_paciente, re.fecha_respuesta, re.id_hc, re.fecha_hora_hc, re.id_seguimiento, re.ind_estado, 
						DATE_FORMAT(re.fecha_respuesta, '%d/%m/%Y') AS fecha_respuesta_t
						FROM registro_foto re 
						WHERE re.id_paciente = ".$id_persona."	
						AND DATE(re.fecha_respuesta) = '".$fecha."'					 
						GROUP BY re.cod_respuestas";
				//echo $sql."<br /> ---- <br />";
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		
		public function getRespuestaPacienteFechaOjo($id_persona, $fecha, $id_ojo) {
			try {
				//$sql = "SELECT *, DATE_FORMAT(fecha_respuesta, '%d/%m/%Y') AS fecha_respuesta_t FROM respuestas WHERE id_paciente = ".$id_persona." ORDER BY id_paciente, id";
				$sql = "SELECT 
						(SELECT r1.id_respuesta FROM respuestas r1 WHERE r1.id_paciente = ".$id_persona." AND r1.cod_respuestas = re.cod_respuestas AND r1.id_pregunta = 1 AND id_ojo = ".$id_ojo.") AS pregunta_1,
						(SELECT r2.id_respuesta FROM respuestas r2 WHERE r2.id_paciente = ".$id_persona." AND r2.cod_respuestas = re.cod_respuestas AND r2.id_pregunta = 2 AND id_ojo = ".$id_ojo.") AS pregunta_2,
						(SELECT r3.id_respuesta FROM respuestas r3 WHERE r3.id_paciente = ".$id_persona." AND r3.cod_respuestas = re.cod_respuestas AND r3.id_pregunta = 3 AND id_ojo = ".$id_ojo.") AS pregunta_3,
						(SELECT r4.id_respuesta FROM respuestas r4 WHERE r4.id_paciente = ".$id_persona." AND r4.cod_respuestas = re.cod_respuestas AND r4.id_pregunta = 4 AND id_ojo = ".$id_ojo.") AS pregunta_4,
						(SELECT r5.id_respuesta FROM respuestas r5 WHERE r5.id_paciente = ".$id_persona." AND r5.cod_respuestas = re.cod_respuestas AND r5.id_pregunta = 5 AND id_ojo = ".$id_ojo.") AS pregunta_5,
						re.id, re.cod_respuestas, re.nombre_paciente, re.cedula_paciente, re.id_ojo, re.id_paciente, re.fecha_respuesta, re.id_hc, re.fecha_hora_hc, re.id_seguimiento, re.ind_estado, 
						DATE_FORMAT(re.fecha_respuesta, '%d/%m/%Y') AS fecha_respuesta_t
						FROM registro_foto re 
						WHERE re.id_paciente = ".$id_persona."	
						AND DATE(re.fecha_respuesta) = '".$fecha."'		
						AND id_ojo = ".$id_ojo."					
						GROUP BY re.cod_respuestas";
				//echo $sql."<br /> ---- <br />";
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		
		public function getVerificaRespuestasPacientes(){
			
			try {
				$sql = "SELECT 
						a1.nombre_paciente, 
						a1.pregunta_1, a1.pregunta_2, a1.pregunta_3, a1.pregunta_4, a1.pregunta_5,
						IF((a1.pregunta_1 = 0 AND a1.pregunta_2 = 0 AND a1.pregunta_3 = 0 AND a1.pregunta_4 = 0 AND a1.pregunta_5 = 1), 1, 0) AS valor_resultado
						FROM
						(SELECT 
						(SELECT r1.id_respuesta FROM respuestas r1 WHERE r1.id_paciente = re.id_paciente AND r1.cod_respuestas = re.cod_respuestas AND r1.id_pregunta = 1) AS pregunta_1,
						(SELECT r2.id_respuesta FROM respuestas r2 WHERE r2.id_paciente = re.id_paciente AND r2.cod_respuestas = re.cod_respuestas AND r2.id_pregunta = 2) AS pregunta_2,
						(SELECT r3.id_respuesta FROM respuestas r3 WHERE r3.id_paciente = re.id_paciente AND r3.cod_respuestas = re.cod_respuestas AND r3.id_pregunta = 3) AS pregunta_3,
						(SELECT r4.id_respuesta FROM respuestas r4 WHERE r4.id_paciente = re.id_paciente AND r4.cod_respuestas = re.cod_respuestas AND r4.id_pregunta = 4) AS pregunta_4,
						(SELECT r5.id_respuesta FROM respuestas r5 WHERE r5.id_paciente = re.id_paciente AND r5.cod_respuestas = re.cod_respuestas AND r5.id_pregunta = 5) AS pregunta_5,
						re.id, re.cod_respuestas, re.nombre_paciente, re.cedula_paciente, re.id_ojo, re.id_paciente, re.fecha_respuesta, re.id_hc, re.fecha_hora_hc, re.id_seguimiento, re.ind_estado, 
						DATE_FORMAT(re.fecha_respuesta, '%d/%m/%Y') AS fecha_respuesta_t
						FROM registro_foto re 
						WHERE DATE(re.fecha_respuesta) BETWEEN ADDDATE(DATE(NOW()), INTERVAL -1 DAY) AND ADDDATE(DATE(NOW()), INTERVAL 1 DAY)
						ORDER  BY cedula_paciente, cod_respuestas) a1";
				//echo $sql."<br /> ---- <br />";
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
			
			
		}
		
		
		
		
		
		
		
		
		
		
		
   	}
?>
