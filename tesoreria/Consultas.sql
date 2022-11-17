/*Consulta de formula de gafas*/
SELECT TD.nombre_detalle AS tipo_documento, P.numero_documento, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2,
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
WHERE HC.fecha_hora_hc BETWEEN '2017-03-01 00:00:00' AND '2017-03-31 23:59:59'
AND CO.ind_formula_gafas=1

UNION ALL

SELECT TD.nombre_detalle AS tipo_documento, P.numero_documento, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2,
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
WHERE HC.fecha_hora_hc BETWEEN '2017-03-01 00:00:00' AND '2017-03-31 23:59:59'
AND CO.ind_formula_gafas=1

UNION ALL

SELECT TD.nombre_detalle AS tipo_documento, P.numero_documento, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2,
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
WHERE HC.fecha_hora_hc BETWEEN '2017-03-01 00:00:00' AND '2017-03-31 23:59:59'
AND CO.ind_formula_gafas=1

ORDER BY fecha_hc_t;


/*Consulta de TASS - oftalmología*/
SELECT TD.nombre_detalle AS tipo_documento, P.numero_documento, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2,
DATE_FORMAT(HC.fecha_hora_hc, '%d/%m/%Y') AS fecha_hc_t, L.nombre_detalle AS lugar_cita, TC.nombre_tipo_cita,
CASE U.ind_anonimo WHEN 1 THEN HC.nombre_usuario_alt ELSE CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) END AS oftalmologo,
CONCAT(U2.nombre_usuario, ' ', U2.apellido_usuario) AS optometra -- , CO.diagnostico_oftalmo, CO.enfermedad_actual
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
WHERE HC.fecha_hora_hc BETWEEN '2016-01-01 00:00:00' AND '2017-01-31 23:59:59'
AND (CO.diagnostico_oftalmo LIKE '%ultra%hopper%'
OR CO.enfermedad_actual LIKE '%ultra%hopper%'
OR CO.solicitud_examenes LIKE '%ultra%hopper%'
OR CO.tratamiento_oftalmo LIKE '%ultra%hopper%'
OR CO.enfermedad_actual LIKE '%ultra%hopper%')
ORDER BY HC.fecha_hora_hc;

/*Consulta de TASS - control*/
SELECT TD.nombre_detalle AS tipo_documento, P.numero_documento, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2,
DATE_FORMAT(HC.fecha_hora_hc, '%d/%m/%Y') AS fecha_hc_t, L.nombre_detalle AS lugar_cita, TC.nombre_tipo_cita,
CASE U.ind_anonimo WHEN 1 THEN HC.nombre_usuario_alt ELSE CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) END AS oftalmologo,
CONCAT(U2.nombre_usuario, ' ', U2.apellido_usuario) AS optometra -- , CO.diagnostico_evolucion , CO.texto_evolucion
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
WHERE HC.fecha_hora_hc BETWEEN '2016-01-01 00:00:00' AND '2017-01-31 23:59:59'
AND (CO.diagnostico_evolucion LIKE '%ultra%hopper%'
OR CO.texto_evolucion LIKE '%ultra%hopper%'
OR CO.solicitud_examenes_evolucion LIKE '%ultra%hopper%'
OR CO.tratamiento_evolucion LIKE '%ultra%hopper%')
ORDER BY HC.fecha_hora_hc;

/*Consulta de TASS - control láser*/
SELECT TD.nombre_detalle AS tipo_documento, P.numero_documento, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2,
DATE_FORMAT(HC.fecha_hora_hc, '%d/%m/%Y') AS fecha_hc_t, L.nombre_detalle AS lugar_cita, TC.nombre_tipo_cita,
CASE U.ind_anonimo WHEN 1 THEN HC.nombre_usuario_alt ELSE CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) END AS oftalmologo,
CONCAT(U2.nombre_usuario, ' ', U2.apellido_usuario) AS optometra -- , CO.diagnostico_control_laser_of
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
WHERE HC.fecha_hora_hc BETWEEN '2016-01-01 00:00:00' AND '2017-01-31 23:59:59'
AND (CO.diagnostico_control_laser_of LIKE '%ultra%hoper%'
OR CO.hallazgos_control_laser LIKE '%ultra%hoper%'
OR CO.solicitud_examenes_control_laser LIKE '%ultra%hoper%'
OR CO.tratamiento_control_laser LIKE '%ultra%hoper%')
ORDER BY HC.fecha_hora_hc;

/*Consulta de TASS - preqx láser*/
SELECT TD.nombre_detalle AS tipo_documento, P.numero_documento, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2,
DATE_FORMAT(HC.fecha_hora_hc, '%d/%m/%Y') AS fecha_hc_t, L.nombre_detalle AS lugar_cita, TC.nombre_tipo_cita,
CASE U.ind_anonimo WHEN 1 THEN HC.nombre_usuario_alt ELSE CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) END AS oftalmologo,
CONCAT(U2.nombre_usuario, ' ', U2.apellido_usuario) AS optometra -- , CO.diagnostico_preqx_laser_of
FROM historia_clinica HC
INNER JOIN consultas_preqx_laser_of CO ON HC.id_hc=CO.id_hc
INNER JOIN pacientes P ON HC.id_paciente=P.id_paciente
LEFT JOIN listas_detalle TD ON P.id_tipo_documento=TD.id_detalle
INNER JOIN admisiones A ON HC.id_admision=A.id_admision
INNER JOIN tipos_citas TC ON A.id_tipo_cita=TC.id_tipo_cita
LEFT JOIN listas_detalle L ON A.id_lugar_cita=L.id_detalle
INNER JOIN usuarios U ON HC.id_usuario_crea=U.id_usuario
LEFT JOIN historia_clinica H2 ON HC.id_admision=H2.id_admision AND HC.id_hc<>H2.id_hc AND H2.id_tipo_reg=1
LEFT JOIN usuarios U2 ON H2.id_usuario_crea=U2.id_usuario
WHERE HC.fecha_hora_hc BETWEEN '2016-01-01 00:00:00' AND '2017-01-31 23:59:59'
AND (CO.diagnostico_preqx_laser_of LIKE '%ultra%hopper%'
OR CO.solicitud_examenes_preqx_laser LIKE '%ultra%hopper%'
OR CO.tratamiento_preqx_laser LIKE '%ultra%hopper%')
ORDER BY HC.fecha_hora_hc;

/*Consulta de TASS - preqx catarata*/
SELECT TD.nombre_detalle AS tipo_documento, P.numero_documento, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2,
DATE_FORMAT(HC.fecha_hora_hc, '%d/%m/%Y') AS fecha_hc_t, L.nombre_detalle AS lugar_cita, TC.nombre_tipo_cita,
CASE U.ind_anonimo WHEN 1 THEN HC.nombre_usuario_alt ELSE CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) END AS oftalmologo,
CONCAT(U2.nombre_usuario, ' ', U2.apellido_usuario) AS optometra -- , CO.diagnostico_preqx_catarata
FROM historia_clinica HC
INNER JOIN consultas_preqx_catarata CO ON HC.id_hc=CO.id_hc
INNER JOIN pacientes P ON HC.id_paciente=P.id_paciente
LEFT JOIN listas_detalle TD ON P.id_tipo_documento=TD.id_detalle
INNER JOIN admisiones A ON HC.id_admision=A.id_admision
INNER JOIN tipos_citas TC ON A.id_tipo_cita=TC.id_tipo_cita
LEFT JOIN listas_detalle L ON A.id_lugar_cita=L.id_detalle
INNER JOIN usuarios U ON HC.id_usuario_crea=U.id_usuario
LEFT JOIN historia_clinica H2 ON HC.id_admision=H2.id_admision AND HC.id_hc<>H2.id_hc AND H2.id_tipo_reg=1
LEFT JOIN usuarios U2 ON H2.id_usuario_crea=U2.id_usuario
WHERE HC.fecha_hora_hc BETWEEN '2016-01-01 00:00:00' AND '2017-01-31 23:59:59'
AND (CO.diagnostico_preqx_catarata LIKE '%ultra%hopper%'
OR CO.solicitud_examenes_preqx_catarata LIKE '%ultra%hopper%'
OR CO.tratamiento_preqx_catarata LIKE '%ultra%hopper%')
ORDER BY HC.fecha_hora_hc;
