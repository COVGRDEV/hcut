<?php
	require_once("DbConexion.php");
	
	class DbFormulacion extends DbConexion {
		public function reporteFormulacionGafas($fecha_ini, $fecha_fin, $id_convenio, $id_plan, $id_usuario_atiende, $id_lugar_cita) {
			try {
				
				$sql_filtros_busq = "AND HC.fecha_hora_hc BETWEEN '".$fecha_ini."' AND '".$fecha_fin." 23:59:59' ";
				
				if ($id_convenio != "") {
					$sql_filtros_busq .= "AND A.id_convenio=".$id_convenio." ";
				}
				if ($id_plan != "") {
					$sql_filtros_busq .= "AND A.id_plan=".$id_plan." ";
				}				
				if ($id_usuario_atiende != '') {
					$sql_filtros_busq .= "AND A.id_usuario_prof=".$id_usuario_atiende." ";
				}
				if ($id_lugar_cita != "") {
					$sql_filtros_busq .= "AND A.id_lugar_cita=".$id_lugar_cita." ";
				}				
				
				$sql =  "SELECT TD.nombre_detalle AS tipo_documento, P.numero_documento, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2,
						DATE_FORMAT(HC.fecha_hora_hc, '%d/%m/%Y') AS fecha_hc_t, L.nombre_detalle AS lugar_cita, TC.nombre_tipo_cita,
						CASE U.ind_anonimo WHEN 1 THEN HC.nombre_usuario_alt ELSE CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) END AS oftalmologo,
						CONCAT(U2.nombre_usuario, ' ', U2.apellido_usuario) AS optometra
						FROM historia_clinica HC
						INNER JOIN consultas_oftalmologia CO ON HC.id_hc=CO.id_hc
						INNER JOIN pacientes P ON HC.id_paciente=P.id_paciente
						LEFT JOIN listas_detalle TD ON P.id_tipo_documento=TD.id_detalle
						INNER JOIN admisiones A ON HC.id_admision=A.id_admision
						INNER JOIN tipos_citas TC ON A.id_tipo_cita=TC.id_tipo_cita
						LEFT JOIN listas_detalle L ON A.id_lugar_cita=L.id_detalle
						INNER JOIN usuarios U ON HC.id_usuario_crea=U.id_usuario
						LEFT JOIN historia_clinica H2 ON HC.id_admision=H2.id_admision AND HC.id_hc<>H2.id_hc AND H2.id_tipo_reg=1
						LEFT JOIN usuarios U2 ON H2.id_usuario_crea=U2.id_usuario
						WHERE CO.ind_formula_gafas=1 ".$sql_filtros_busq." ";
						
				$sql .=  "UNION ALL ";
				$sql .=  "SELECT TD.nombre_detalle AS tipo_documento, P.numero_documento, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2,
						DATE_FORMAT(HC.fecha_hora_hc, '%d/%m/%Y') AS fecha_hc_t, L.nombre_detalle AS lugar_cita, TC.nombre_tipo_cita,
						CASE U.ind_anonimo WHEN 1 THEN HC.nombre_usuario_alt ELSE CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) END AS oftalmologo,
						CONCAT(U2.nombre_usuario, ' ', U2.apellido_usuario) AS optometra
						FROM historia_clinica HC
						INNER JOIN consultas_evoluciones CO ON HC.id_hc=CO.id_hc
						INNER JOIN pacientes P ON HC.id_paciente=P.id_paciente
						LEFT JOIN listas_detalle TD ON P.id_tipo_documento=TD.id_detalle
						INNER JOIN admisiones A ON HC.id_admision=A.id_admision
						INNER JOIN tipos_citas TC ON A.id_tipo_cita=TC.id_tipo_cita
						LEFT JOIN listas_detalle L ON A.id_lugar_cita=L.id_detalle
						INNER JOIN usuarios U ON HC.id_usuario_crea=U.id_usuario
						LEFT JOIN historia_clinica H2 ON HC.id_admision=H2.id_admision AND HC.id_hc<>H2.id_hc AND H2.id_tipo_reg=1
						LEFT JOIN usuarios U2 ON H2.id_usuario_crea=U2.id_usuario
						WHERE CO.ind_formula_gafas=1 ".$sql_filtros_busq." ";	
						
				$sql .=  "UNION ALL ";
				$sql .=  "SELECT TD.nombre_detalle AS tipo_documento, P.numero_documento, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2,
						DATE_FORMAT(HC.fecha_hora_hc, '%d/%m/%Y') AS fecha_hc_t, L.nombre_detalle AS lugar_cita, TC.nombre_tipo_cita,
						CASE U.ind_anonimo WHEN 1 THEN HC.nombre_usuario_alt ELSE CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) END AS oftalmologo,
						CONCAT(U2.nombre_usuario, ' ', U2.apellido_usuario) AS optometra
						FROM historia_clinica HC
						INNER JOIN consultas_control_laser_of CO ON HC.id_hc=CO.id_hc
						INNER JOIN pacientes P ON HC.id_paciente=P.id_paciente
						LEFT JOIN listas_detalle TD ON P.id_tipo_documento=TD.id_detalle
						INNER JOIN admisiones A ON HC.id_admision=A.id_admision
						INNER JOIN tipos_citas TC ON A.id_tipo_cita=TC.id_tipo_cita
						LEFT JOIN listas_detalle L ON A.id_lugar_cita=L.id_detalle
						INNER JOIN usuarios U ON HC.id_usuario_crea=U.id_usuario
						LEFT JOIN historia_clinica H2 ON HC.id_admision=H2.id_admision AND HC.id_hc<>H2.id_hc AND H2.id_tipo_reg=1
						LEFT JOIN usuarios U2 ON H2.id_usuario_crea=U2.id_usuario
						WHERE CO.ind_formula_gafas=1 ".$sql_filtros_busq." ";							
				$sql .= "ORDER BY fecha_hc_t "; 

				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
	}
?>
