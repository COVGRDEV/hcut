<?php
require_once("../db/DbConexion.php");


class DbPacientesClave extends DbConexion {

	public function getPacienteId($id) {
		try {
			$sql = "SELECT h.fecha_hora_hc, h.id_hc, p.id_paciente, p.nombre_1, p.nombre_2, p.apellido_1, p.apellido_2, p.numero_documento, s.clave_verificacion, s.*
					FROM pacientes p
					INNER JOIN seguimiento_postqx_catarata s ON s.id_paciente = p.id_paciente
					INNER JOIN admisiones a ON a.id_paciente = p.id_paciente
					INNER JOIN historia_clinica h ON h.id_admision = a.id_admision
					LEFT JOIN consultas_evoluciones e ON e.id_hc = h.id_hc
					WHERE p.id_paciente = $id
					ORDER BY h.fecha_hora_hc DESC
					LIMIT 1 ";
			
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	public function getCodRespuestaPaciente($id) {
		try {
			$sql = "SELECT IFNULL(MAX(cod_respuestas),0) max_cod_respuesta FROM registro_foto WHERE id_paciente = ".$id;
			
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	
	public function validarClavePaciente($id_paciente, $clave) {
		try {
		
		
			$tabla_paciente = $this->getPacienteId($id_paciente);
			
			$tabla_cod_respuesta = $this->getCodRespuestaPaciente($id_paciente);
			$cod_respuesta = $tabla_cod_respuesta['max_cod_respuesta'];
			
			
			$clave_paciente = $tabla_paciente[0]['clave_verificacion'];
			$nombre_1 = $tabla_paciente[0]['nombre_1'];
			$nombre_2 = $tabla_paciente[0]['nombre_2'];
			$apellido_1 = $tabla_paciente[0]['apellido_1'];
			$apellido_2 = $tabla_paciente[0]['apellido_2'];
			$numero_documento = $tabla_paciente[0]['numero_documento'];
			
			$fecha_seguimiento_uno = $tabla_paciente[0]['fecha_seguimiento_uno'];
			$fecha_seguimiento_dos = $tabla_paciente[0]['fecha_seguimiento_dos'];
			$fecha_seguimiento_tres = $tabla_paciente[0]['fecha_seguimiento_tres'];
			$ind_ojos = $tabla_paciente[0]['id_ojos'];
			$ind_voz = $tabla_paciente[0]['ind_voz'];
			$ind_inclusion = $tabla_paciente[0]['ind_inclusion'];
			
			$id_hc = $tabla_paciente[0]['id_hc'];
			$fecha_hora_hc = $tabla_paciente[0]['fecha_hora_hc'];
			$id_seguimiento = $tabla_paciente[0]['id_seguimiento'];
			
			if($clave_paciente == $clave){
				$resultado = "|1|".$id_paciente."|".$nombre_1."|".$nombre_2."|".$apellido_1."|".$apellido_2."|".$numero_documento."|".$fecha_seguimiento_uno."|".$fecha_seguimiento_dos."|".$fecha_seguimiento_tres."|".$ind_ojos."|".$ind_voz."|".$ind_inclusion."|".$id_hc."|".$fecha_hora_hc."|".$id_seguimiento."|".$cod_respuesta."|";
			}
			else{
				$resultado = "|0|".$id_paciente."|".$nombre_1."|".$nombre_2."|".$apellido_1."|".$apellido_2."|".$numero_documento."|".$fecha_seguimiento_uno."|".$fecha_seguimiento_dos."|".$fecha_seguimiento_tres."|".$ind_ojos."|".$ind_voz."|".$ind_inclusion."|".$id_hc."|".$fecha_hora_hc."|".$id_seguimiento."|".$cod_respuesta."|";
			}
			return $resultado;
			
		} catch (Exception $e) {
		
			return array();
		}
	}
	
	
	public function getBuscarpacientesVinculados($parametro) {//Busca pacientes por numero de identificacion o nombre
			try {
				$parametro = str_replace(" ", "%", $parametro);
				$sql = "SELECT p.*, ld.nombre_detalle, ps.nombre_pais, pt.nom_dep AS nom_dep2 , m.nom_mun AS nom_mun2,
						DATE_FORMAT(p.fecha_nacimiento, '%d/%m/%Y') AS fecha_nacimiento_aux, LT.nombre_detalle AS tipo_sangre2,
						LT2.nombre_detalle AS factor_rh2, ld.codigo_detalle AS cod_tipo_documento
						FROM pacientes p
						INNER JOIN listas_detalle ld ON ld.id_detalle=p.id_tipo_documento
						INNER JOIN paises ps ON ps.id_pais=p.id_pais
						LEFT JOIN departamentos pt ON pt.cod_dep=p.cod_dep
						LEFT JOIN municipios m ON m.cod_mun_dane=p.cod_mun
						INNER JOIN listas_detalle LT ON LT.id_detalle=p.tipo_sangre
						INNER JOIN listas_detalle LT2 ON LT2.id_detalle=p.factor_rh
						INNER JOIN seguimiento_postqx_catarata s ON s.id_paciente = p.id_paciente
						WHERE CONCAT(p.nombre_1, ' ', IFNULL(p.nombre_2, ''), ' ', p.apellido_1, ' ', IFNULL(p.apellido_2, '')) LIKE '%".$parametro."%'
						OR p.numero_documento='".$parametro."'
						ORDER BY p.nombre_1, p.nombre_2, p.apellido_1, p.apellido_2";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	
	
}
?>
