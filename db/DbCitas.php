<?php

require_once("DbConexion.php");

class DbCitas extends DbConexion {
    /*
     * Esta funcion obtiene los registros para el formulario admisiones/estado.php
     */

    public function getEstadoagendados($id_estado_cita, $id_usuario_prof, $id_lugar = 0) {
        try {
            $sql = "SELECT C.nombre_1, C.nombre_2, C.apellido_1, C.apellido_2, C.id_estado_cita, DATE_FORMAT(C.fecha_cita, '%H:%i') AS fecha_cita,
						U.nombre_usuario, U.apellido_usuario, C.id_cita, C.id_paciente, C.numero_documento, LD.nombre_detalle,
						CV.nombre_convenio, DATE_FORMAT(C.fecha_cita, '%h:%i %p') AS hora_cita, C.observacion_cita, C.fecha_llegada,
						C.fecha_ingreso, DATE_FORMAT(C.fecha_llegada,'%h:%i %p') AS fecha_llegada2_aux, TC.nombre_tipo_cita,
						DATE_FORMAT(C.fecha_ingreso,'%h:%i %p') AS fecha_ingreso2_aux, TIMEDIFF(NOW(), C.fecha_llegada) AS dif_fecha_llegada,
						TIMEDIFF(NOW(), C.fecha_ingreso) AS dif_fecha_ingreso, C.id_lugar_cita, LC.nombre_detalle AS nombre_lugar_cita,
						PC.nombre_1 as PCnombre1, PC.nombre_2 as PCnombre2, PC.apellido_1 as PCapellido1, PC.apellido_2 as PCapellido2
						FROM citas C
						INNER JOIN usuarios U ON C.id_usuario_prof=U.id_usuario
						INNER JOIN listas_detalle LD ON C.id_estado_cita=LD.id_detalle
						INNER JOIN convenios CV ON C.id_convenio=CV.id_convenio
						INNER JOIN tipos_citas TC ON C.id_tipo_cita=TC.id_tipo_cita
						LEFT JOIN listas_detalle LC ON C.id_lugar_cita=LC.id_detalle
						LEFT JOIN admisiones A ON C.id_cita=A.id_cita
						LEFT JOIN admisiones AB ON A.id_admision_base=AB.id_admision
						INNER JOIN pacientes PC on C.id_paciente = PC.id_paciente
						WHERE C.fecha_cita BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 DAY) ";
            if ($id_estado_cita > 0) {
                $sql .= "AND C.id_estado_cita=" . $id_estado_cita . " ";
            }
            if (strlen($id_usuario_prof) > 0) {
                $sql .= "AND (C.id_usuario_prof=" . $id_usuario_prof . " OR AB.id_usuario_prof=" . $id_usuario_prof . ") ";
            }
            if ($id_lugar > 0) {
                $sql .= "AND (C.id_lugar_cita=" . $id_lugar . ")";
            }

            $sql .= "ORDER BY C.fecha_cita ASC";

			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /*
     * Esta funcion obtiene los registros para el formulario admisiones/citas.php
     */

    public function getEstadoagendados2() {
        try {
            $sql = "SELECT P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, DATE_FORMAT(C.fecha_cita, '%H:%i') AS fecha_cita,
					U.nombre_usuario, U.apellido_usuario, C.id_cita, C.id_paciente, P.numero_documento, LD.nombre_detalle,
					CV.nombre_convenio, DATE_FORMAT(C.fecha_cita, '%h:%i %p') AS hora_cita, C.observacion_cita, C.fecha_llegada,
					C.fecha_ingreso, DATE_FORMAT(C.fecha_llegada,'%h:%i %p') AS fecha_llegada2_aux, DATE_FORMAT(C.fecha_ingreso,'%h:%i %p') AS fecha_ingreso2_aux
					FROM citas C
					INNER JOIN pacientes P ON C.id_paciente=P.id_paciente
					INNER JOIN usuarios U ON U.id_usuario=C.id_usuario_prof
					INNER JOIN listas_detalle LD ON LD.id_detalle=C.id_estado_cita
					INNER JOIN convenios CV ON CV.id_convenio=C.id_convenio
					WHERE C.fecha_cita BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 DAY)
					ORDER BY fecha_cita ASC";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /*
     * Esta funcion obtiene los registros adminitos para el formulario admisiones/estado.php
     */

    public function getEstadoadmitidos($id_usuario_prof, $id_lugar = 0) {
        try {
            //Admisiones del día actual
            $sql = "SELECT A.id_admision, A.id_estado_atencion, A.id_tipo_cita, P.id_paciente, P.nombre_1, P.nombre_2, P.apellido_1,
							P.apellido_2, DATE_FORMAT(A.fecha_admision, '%H:%i') AS fecha_admision, A.id_usuario_prof, U.nombre_usuario,
							U.apellido_usuario, TIMEDIFF(NOW(), A.fecha_estado) AS dif_fecha_admision, CV.nombre_convenio,
							TC.nombre_tipo_cita, DATE_FORMAT(C.fecha_cita, '%H:%i') AS fecha_cita, TIMEDIFF(NOW(), A.fecha_crea) AS dif_fecha_admitido,
							DATE_FORMAT(CASE WHEN C.fecha_llegada IS NOT NULL THEN C.fecha_llegada ELSE A.fecha_crea END, '%H:%i') AS hora_llegada,
							CASE WHEN C.fecha_llegada IS NOT NULL THEN TIMEDIFF(NOW(), C.fecha_llegada) ELSE TIMEDIFF(NOW(), A.fecha_crea) END AS dif_fecha_llegada,
							TIMEDIFF(A.fecha_estado, A.fecha_crea) AS dif_fecha_atencion, A.id_lugar_cita, LC.nombre_detalle AS nombre_lugar_cita, 0 AS id_pago,
							A.id_tipo_espera, A.fecha_espera, TE.nombre_detalle AS tipo_espera, DATE_FORMAT(TIMEDIFF(NOW(), A.fecha_espera), '%H:%i') AS dif_fecha_espera
							FROM admisiones A
							INNER JOIN pacientes P ON A.id_paciente=P.id_paciente
							INNER JOIN usuarios U ON A.id_usuario_prof=U.id_usuario
							INNER JOIN convenios CV ON A.id_convenio=CV.id_convenio
							INNER JOIN tipos_citas TC ON A.id_tipo_cita=TC.id_tipo_cita
							LEFT JOIN citas C ON A.id_cita=C.id_cita
							LEFT JOIN listas_detalle LC ON A.id_lugar_cita=LC.id_detalle
							LEFT JOIN admisiones AB ON A.id_admision_base=AB.id_admision
							LEFT JOIN listas_detalle TE ON A.id_tipo_espera=TE.id_detalle
							WHERE A.fecha_admision>=CURDATE() ";

            if (strlen($id_usuario_prof) > 0) {
                $sql .= "AND (A.id_usuario_prof=" . $id_usuario_prof . " OR AB.id_usuario_prof=" . $id_usuario_prof . "
								 OR EXISTS (
									 SELECT * FROM admisiones_estados_atencion AE
									 WHERE A.id_admision=AE.id_admision
									 AND A.id_estado_atencion=AE.id_estado_atencion
									 AND AE.id_usuario_prof=" . $id_usuario_prof . "
								 ))";
            }
            if ($id_lugar > 0) {
                $sql .= "AND A.id_lugar_cita=" . $id_lugar . " ";
            }

            //Admisiones retomadas de días anteriores
            $sql .= "UNION ALL
							
							SELECT DISTINCT A.id_admision, A.id_estado_atencion, A.id_tipo_cita, P.id_paciente, P.nombre_1, P.nombre_2, P.apellido_1,
							P.apellido_2, DATE_FORMAT(A.fecha_admision, '%H:%i') AS fecha_admision, A.id_usuario_prof, U.nombre_usuario,
							U.apellido_usuario, TIMEDIFF(NOW(), A.fecha_estado) AS dif_fecha_admision, CV.nombre_convenio,
							TC.nombre_tipo_cita, DATE_FORMAT(C.fecha_cita, '%H:%i') AS fecha_cita, TIMEDIFF(NOW(), A.fecha_crea) AS dif_fecha_admitido,
							DATE_FORMAT(CASE WHEN C.fecha_llegada IS NOT NULL THEN C.fecha_llegada ELSE A.fecha_crea END, '%H:%i') AS hora_llegada,
							CASE WHEN C.fecha_llegada IS NOT NULL THEN TIMEDIFF(NOW(), C.fecha_llegada) ELSE TIMEDIFF(NOW(), A.fecha_crea) END AS dif_fecha_llegada,
							TIMEDIFF(A.fecha_estado, A.fecha_crea) AS dif_fecha_atencion, A.id_lugar_cita, LC.nombre_detalle AS nombre_lugar_cita, 0 AS id_pago,
							A.id_tipo_espera, A.fecha_espera, TE.nombre_detalle AS tipo_espera, DATE_FORMAT(TIMEDIFF(NOW(), A.fecha_espera), '%H:%i') AS dif_fecha_espera
							FROM admisiones A
							INNER JOIN pacientes P ON A.id_paciente=P.id_paciente
							INNER JOIN usuarios U ON A.id_usuario_prof=U.id_usuario
							INNER JOIN convenios CV ON A.id_convenio=CV.id_convenio
							INNER JOIN tipos_citas TC ON A.id_tipo_cita=TC.id_tipo_cita
							LEFT JOIN citas C ON A.id_cita=C.id_cita
							LEFT JOIN listas_detalle LC ON A.id_lugar_cita=LC.id_detalle
							LEFT JOIN admisiones AB ON A.id_admision_base=AB.id_admision
							INNER JOIN admisiones_estados_tiempos ET ON A.id_admision=ET.id_admision
							LEFT JOIN listas_detalle TE ON A.id_tipo_espera=TE.id_detalle
							WHERE ET.fecha_estado>=CURDATE()
							AND A.fecha_admision<CURDATE() ";

            if (strlen($id_usuario_prof) > 0) {
                $sql .= "AND (A.id_usuario_prof=" . $id_usuario_prof . " OR AB.id_usuario_prof=" . $id_usuario_prof . "
								 OR EXISTS (
									 SELECT * FROM admisiones_estados_atencion AE
									 WHERE A.id_admision=AE.id_admision
									 AND A.id_estado_atencion=AE.id_estado_atencion
									 AND AE.id_usuario_prof=" . $id_usuario_prof . "
								 ))";
            }
            if ($id_lugar > 0) {
                $sql .= "AND A.id_lugar_cita=" . $id_lugar . " ";
            }

            if (strlen($id_usuario_prof) == 0) {
                //Pagos pendientes del día actual y sin admisión 
                $sql .= "UNION ALL
								
								SELECT 0, 2, NULL, P.id_paciente, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2,
								NULL, NULL, NULL, NULL, TIMEDIFF(NOW(), PA.fecha_crea) AS dif_fecha_admision,
								CV.nombre_convenio, NULL, NULL, TIMEDIFF(NOW(), PA.fecha_crea) AS dif_fecha_admitido,
								DATE_FORMAT(PA.fecha_crea, '%H:%i') AS hora_llegada, TIMEDIFF(NOW(), PA.fecha_crea) AS dif_fecha_llegada,
								NULL, PA.id_lugar_cita, LC.nombre_detalle AS nombre_lugar_cita, PA.id_pago, NULL, NULL, NULL, NULL
								FROM pagos PA
								INNER JOIN pacientes P ON PA.id_paciente=P.id_paciente
								INNER JOIN convenios CV ON PA.id_convenio=CV.id_convenio
								LEFT JOIN listas_detalle LC ON PA.id_lugar_cita=LC.id_detalle
								WHERE PA.fecha_crea>=CURDATE()
								AND PA.estado_pago=1
								AND PA.id_admision IS NULL ";
                if ($id_lugar > 0) {
                    $sql .= "AND PA.id_lugar_cita=" . $id_lugar . " ";
                }
            }
            $sql .= "ORDER BY dif_fecha_admision DESC";
            //echo($sql."<br /><br />");

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /*
     * Esta funcion obtiene la fecha actual del sistema de la base de datos para el formulario admisiones/estado.php
     */

    public function getFecha() {
        try {
            $sql = "SELECT DATE_FORMAT(NOW(), '%Y:%m:%e') AS fecha";

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /*
     * Esta funcion obtiene la cita de la base de datos para el formulario admisiones/estado.php
     */

    public function getCita($id_cita) {
        try {
            $sql = "SELECT c.*, tc.nombre_tipo_cita AS nombre_tipo_cita, CONCAT(u.nombre_usuario,' ',u.apellido_usuario) AS profesional_atiende, 
						DATE_FORMAT(c.fecha_cita, '%d/%m/%Y') AS fecha_consulta, DATE_FORMAT(c.fecha_cita, '%H:%i %p') AS hora_consulta, c.nombre_2 AS nombre2, 
						ld.nombre_detalle, CONCAT(up.nombre_usuario, ' ', up.apellido_usuario) AS nombre_usuario_creador, up.login_usuario AS usuario_creador, 
						DATE_FORMAT(c.fecha_crea, '%d/%m/%Y %H:%i:%s') AS fecha_creacion_aux, cv.nombre_convenio AS nombre_convenio_aux, 
						DATE_FORMAT(c.fecha_llegada,'%h:%i %p') AS fecha_llegada2_aux, DATE_FORMAT(c.fecha_ingreso,'%h:%i %p') AS fecha_ingreso2_aux, cv.ind_eco,
						 DATE_FORMAT(c.fecha_cita, '%h:%i %p') AS hora_consulta_t, ec.nombre_detalle AS nombre_estado_cita, PC.nombre_1 AS PCnombre1, PC.nombre_2 AS PCnombre2, 
						 PC.apellido_1 AS PCapellido1, PC.apellido_2 AS PCapellido2, PC.telefono_1 AS PCtelefono, PC.status_convenio_paciente AS PCstatusseguro,
						 PC.rango_paciente, PC.tipo_coti_paciente, PC.exento_pamo_paciente, DATE_FORMAT(c.fecha_cita,'%d') AS Dia, DATE_FORMAT(c.fecha_cita,'%m') AS Mes, 
						 DATE_FORMAT(c.fecha_cita,'%Y') AS Anio, DATE_FORMAT(c.fecha_cita,'%h:%i:%s %p') AS Hora, SD.dir_sede_det AS dir_consulta_aux, SA.dir_sede AS dir_sede_alter, 
						 SD.tel_sede_det AS tel_consulta_aux, CD.valor AS valor_c_m,PC.nombre_1 AS nombre_1, PC.nombre_2 AS nombre_2, PC.apellido_1 AS apellido_1, 
						 PC.apellido_2 AS apellido_2, SD.dir_logo_sede_det AS dir_logo_aux, P.nombre_plan AS nom_plan_aux, ldd.nombre_detalle AS nombreLugarAux 
						 FROM citas c 
						 INNER JOIN tipos_citas tc ON tc.id_tipo_cita=c.id_tipo_cita 
						 INNER JOIN usuarios u ON u.id_usuario=c.id_usuario_prof 
						 INNER JOIN listas_detalle ld ON ld.id_detalle=c.id_tipo_documento 
						 INNER JOIN usuarios up ON up.id_usuario=c.id_usuario_crea 
						 INNER JOIN convenios cv ON cv.id_convenio=c.id_convenio 
						 INNER JOIN planes P ON P.id_plan = c.id_plan 
						 INNER JOIN listas_detalle ec ON c.id_estado_cita=ec.id_detalle 
						 INNER JOIN pacientes PC ON c.id_paciente = PC.id_paciente 
						 INNER JOIN listas_detalle ldd ON ldd.id_detalle=c.id_lugar_cita 
						 INNER JOIN sedes_det SD ON c.id_lugar_cita=SD.id_detalle 
						 LEFT JOIN sedes_alternativas SA ON c.id_lugar_cita_alter=SA.id
						 LEFT JOIN listas_detalle TP ON TP.id_detalle = PC.tipo_coti_paciente
						 LEFT JOIN cuotas_moderadoras_base CD ON TP.codigo_detalle=CD.tipo_cotizante  AND PC.rango_paciente=CD.rango_valor AND CURDATE() 
							BETWEEN fecha_ini_valor AND IFNULL(fecha_fin_valor, CURDATE()) 
						WHERE c.id_cita=".$id_cita;
			
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /*
     * Esta funcion obtiene datos para el filtro buscar pacientes de la página admisiones/estado_atencion.php
     */

    public function getBuscarpacientes($parametro) {//Busca pacientes por numero de identificacion o nombre
        try {
            $parametro = str_replace(" ", "%", $parametro);
            $sql = "SELECT 0 AS id_admision, C.id_cita, CASE C.id_estado_cita WHEN 14 THEN 1 ELSE 10 END AS id_estado_atencion,
							CASE C.id_estado_cita WHEN 14 THEN 'AGENDADOS' ELSE 'EN ESPERA DE ADMISIÓN' END AS nombre_estado,
							C.nombre_1, C.nombre_2, C.apellido_1, C.apellido_2, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_usuario,
							C.fecha_cita, DATE_FORMAT(C.fecha_cita, '%H:%i %p') AS tiempo_t, DATE_FORMAT(C.fecha_cita, '%d/%m/%Y') AS fecha_cita_aux,
							TD.codigo_detalle AS nombre_detalle2, C.numero_documento, C.id_paciente, C.id_tipo_cita
							FROM citas C
							INNER JOIN listas_detalle EC ON C.id_estado_cita=EC.id_detalle
							INNER JOIN listas_detalle TD ON C.id_tipo_documento=TD.id_detalle
							INNER JOIN usuarios U ON C.id_usuario_prof=U.id_usuario
							WHERE DATE(C.fecha_cita)=DATE_FORMAT(NOW(), '%Y-%m-%e')
							AND (CONCAT(C.nombre_1, ' ', IFNULL(C.nombre_2, ''), ' ', C.apellido_1, ' ', IFNULL(C.apellido_2, '')) LIKE '%" . $parametro . "%'
							OR C.numero_documento LIKE '%" . $parametro . "%')
							AND C.id_estado_cita IN (14, 130)
							UNION
							SELECT A.id_admision, A.id_cita, A.id_estado_atencion, EA.nombre_estado, P.nombre_1, P.nombre_2, P.apellido_1,
							P.apellido_2, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_usuario, A.fecha_estado,
							TIMEDIFF(NOW(), A.fecha_estado) AS tiempo_t, DATE_FORMAT(A.fecha_estado, '%d/%m/%Y') AS fecha_estado_aux,
							LD.codigo_detalle AS nombre_detalle2, P.numero_documento, A.id_paciente, A.id_tipo_cita
							FROM admisiones A
							INNER JOIN pacientes P ON A.id_paciente=P.id_paciente
							INNER JOIN estados_atencion EA ON A.id_estado_atencion=EA.id_estado_atencion
							INNER JOIN usuarios U ON A.id_usuario_prof=U.id_usuario
							INNER JOIN listas_detalle LD ON P.id_tipo_documento=LD.id_detalle
							WHERE DATE(A.fecha_admision) = DATE_FORMAT(NOW(), '%Y-%m-%e')
							AND (CONCAT(P.nombre_1, ' ', IFNULL(P.nombre_2, ''), ' ', P.apellido_1, ' ', IFNULL(P.apellido_2, '')) LIKE '%" . $parametro . "%'
							OR P.numero_documento LIKE '%" . $parametro . "%')
							ORDER BY nombre_1, nombre_2, apellido_1, apellido_2";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /*
     * Esta funcion obtiene datos para el filtro buscar pacientes de la página admisiones/estado_atencion.php
     */

    public function getBuscarPacientesAnt($parametro, $num_dias) {//Busca pacientes por numero de identificacion o nombre
        try {
            $parametro = str_replace(" ", "%", $parametro);
            $sql = "SELECT 0 AS id_admision, C.id_cita, CASE C.id_estado_cita WHEN 14 THEN 1 ELSE 10 END AS id_estado_atencion,
					CASE C.id_estado_cita WHEN 14 THEN 'AGENDADOS' WHEN 130 THEN 'EN ESPERA DE ADMISIÓN' ELSE 'EN ADMISIÓN' END AS nombre_estado,
					P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_usuario,
					C.fecha_cita, DATE_FORMAT(C.fecha_cita, '%H:%i %p') AS tiempo_t, DATE_FORMAT(C.fecha_cita, '%d/%m/%Y') AS fecha_cita_aux,
					TD.codigo_detalle AS nombre_detalle2, P.numero_documento, C.id_paciente, C.id_tipo_cita, C.id_lugar_cita,
					LC.nombre_detalle AS nombre_lugar_cita, 0 AS id_pago
					FROM citas C
					INNER JOIN pacientes P ON C.id_paciente=P.id_paciente
					INNER JOIN listas_detalle EC ON C.id_estado_cita=EC.id_detalle
					INNER JOIN listas_detalle TD ON P.id_tipo_documento=TD.id_detalle
					INNER JOIN usuarios U ON C.id_usuario_prof=U.id_usuario
					LEFT JOIN listas_detalle LC ON C.id_lugar_cita=LC.id_detalle
					WHERE DATE(C.fecha_cita) BETWEEN DATE_SUB(CURDATE(), INTERVAL ".$num_dias." DAY) AND CURDATE()
					AND (CONCAT(P.nombre_1, ' ', IFNULL(P.nombre_2, ''), ' ', P.apellido_1, ' ', IFNULL(P.apellido_2, '')) LIKE '%".$parametro."%'
					OR P.numero_documento LIKE '%".$parametro."%')
					AND C.id_estado_cita IN (14, 130, 258)
					
					UNION ALL
					
					SELECT A.id_admision, A.id_cita, A.id_estado_atencion, EA.nombre_estado, P.nombre_1, P.nombre_2, P.apellido_1,
					P.apellido_2, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_usuario, A.fecha_estado,
					TIMEDIFF(NOW(), A.fecha_estado) AS tiempo_t, DATE_FORMAT(A.fecha_estado, '%d/%m/%Y') AS fecha_estado_aux,
					LD.codigo_detalle AS nombre_detalle2, P.numero_documento, A.id_paciente, A.id_tipo_cita, A.id_lugar_cita,
					LC.nombre_detalle AS nombre_lugar_cita, 0
					FROM admisiones A
					INNER JOIN pacientes P ON A.id_paciente=P.id_paciente
					INNER JOIN estados_atencion EA ON A.id_estado_atencion=EA.id_estado_atencion
					INNER JOIN usuarios U ON A.id_usuario_prof=U.id_usuario
					INNER JOIN listas_detalle LD ON P.id_tipo_documento=LD.id_detalle
					LEFT JOIN listas_detalle LC ON A.id_lugar_cita=LC.id_detalle
					WHERE DATE(A.fecha_admision) BETWEEN DATE_SUB(CURDATE(), INTERVAL ".$num_dias." DAY) AND CURDATE()
					AND (CONCAT(P.nombre_1, ' ', IFNULL(P.nombre_2, ''), ' ', P.apellido_1, ' ', IFNULL(P.apellido_2, '')) LIKE '%".$parametro."%'
					OR P.numero_documento LIKE '%".$parametro."%')
					
					UNION ALL
					
					SELECT 0, 0, EA.id_estado_atencion, EA.nombre_estado, P.nombre_1, P.nombre_2, P.apellido_1,
					P.apellido_2, NULL, PA.fecha_crea,
					TIMEDIFF(NOW(), PA.fecha_crea), DATE_FORMAT(PA.fecha_crea, '%d/%m/%Y') AS fecha_estado_aux,
					LD.codigo_detalle AS nombre_detalle2, P.numero_documento, PA.id_paciente, NULL, PA.id_lugar_cita,
					LC.nombre_detalle AS nombre_lugar_cita, PA.id_pago
					FROM pagos PA
					INNER JOIN pacientes P ON PA.id_paciente=P.id_paciente
					CROSS JOIN estados_atencion EA
					INNER JOIN listas_detalle LD ON P.id_tipo_documento=LD.id_detalle
					LEFT JOIN listas_detalle LC ON PA.id_lugar_cita=LC.id_detalle
					WHERE DATE(PA.fecha_crea) BETWEEN DATE_SUB(CURDATE(), INTERVAL " . $num_dias . " DAY) AND CURDATE()
					AND (CONCAT(P.nombre_1, ' ', IFNULL(P.nombre_2, ''), ' ', P.apellido_1, ' ', IFNULL(P.apellido_2, '')) LIKE '%" . $parametro . "%'
					OR P.numero_documento LIKE '%" . $parametro . "%')
					AND PA.estado_pago=1
					AND PA.id_admision IS NULL
					AND EA.id_estado_atencion=2
					
					ORDER BY nombre_1, nombre_2, apellido_1, apellido_2";
			
            //echo $sql;
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    //Marcar las citas anteriores de estado PROGRAMADA = 14  a estado NO ASISTIO = 18   
    public function marcar_cita_no_atendida($id_usuario) {
        try {
            $sql = "CALL pa_marcar_cita_no_atendida(" . $id_usuario . ", @id)";
            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $ind_cita = $arrResultado["@id"];

            return $ind_cita;
        } catch (Exception $e) {
            return -2;
        }
    }

    /*
     * Esta funcion retorna las citas pendientes por día de un usuario en un mes dado
     */

    public function getConsolidadoCitasUsuarioMes($id_usuario, $anio, $mes) {
        try {
            $sql = "SELECT CA.fecha_cal, CA.ind_laboral, CASE WHEN C.cant_citas IS NULL THEN 0 ELSE C.cant_citas END AS cant_citas " .
                    "FROM calendarios CA " .
                    "LEFT JOIN (" .
                    "SELECT DATE(fecha_cita) AS fecha_cita, COUNT(*) AS cant_citas " .
                    "FROM citas " .
                    "WHERE id_usuario_prof=" . $id_usuario . " " .
                    "AND id_estado_cita=14 " .
                    "AND DATE_FORMAT(fecha_cita, '%Y')='" . $anio . "' " .
                    "AND DATE_FORMAT(fecha_cita, '%m')='" . $mes . "' " .
                    "GROUP BY DATE(fecha_cita)" .
                    ") C ON CA.fecha_cal=C.fecha_cita " .
                    "WHERE DATE_FORMAT(CA.fecha_cal, '%Y')='" . $anio . "' " .
                    "AND DATE_FORMAT(CA.fecha_cal, '%m')='" . $mes . "' " .
                    "ORDER BY CA.fecha_cal";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    //Marcar las citas anteriores de estado PROGRAMADA = 14  a estado NO ASISTIO = 18   
    public function mover_citas_pendientes($fecha, $id_usuario_dest, $id_fellow_g, $id_usuario) {
        try {
            $sql = "CALL pa_mover_citas_pendientes(STR_TO_DATE('" . $fecha . "', '%Y-%m-%d'), " . $id_fellow_g . ", " . $id_usuario_dest . ", " . $id_usuario . ", @id)";

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);

            return $arrResultado["@id"];
        } catch (Exception $e) {
            return -2;
        }
    }

    public function marca_cita_atendida($id, $usuario_crea, $idEstado) {
        try {
            $sql = "CALL pa_marca_cita_atendida($id, $usuario_crea, $idEstado, @id)";

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);

            return $arrResultado["@id"];
        } catch (Exception $e) {
            return -2;
        }
    }

    /*
     * Esta funcion obtiene el reporte de las citas
     */

    public function getReporteCitas($profesional, $lugar, $fechaInicial, $fechaFinal, $cita, $limitado, $tipoCita, $hora, $tiposCitas, $convenio, $ind_conteo = 0) {
        try {
            $cita = str_replace(" ", "%", $cita);
            $concatenacion = "";
            $conector = "WHERE ";
            $limite = "";
            if ($profesional != "") {
                $concatenacion .= $conector . "C.id_usuario_prof=" . $profesional . " ";
                $conector = "AND ";
            }
            if ($lugar != "") {
                $concatenacion .= $conector . "C.id_lugar_cita=" . $lugar . " ";
                $conector = "AND ";
            }
            if ($tipoCita != "") {
                $concatenacion .= $conector . "C.id_tipo_cita=" . $tipoCita . " ";
                $conector = "AND ";
            }

            $idEstadoCita = array();
            $contado = 1;
            for ($a = 1; $a <= count($tiposCitas); $a++) {
                if ($tiposCitas[$a] != "0") {
                    $idEstadoCita[$contado] = $tiposCitas[$a];
                    $contado++;
                }
            }

            //Agrega los valores a la sentencia IN
            if (count($idEstadoCita) >= 1) {
                $sentencia = "(";
                for ($e = 1; $e <= count($idEstadoCita); $e++) {
                    if (count($idEstadoCita) == 1) {
                        $sentencia .= $idEstadoCita[$e];
                    } else {
                        $sentencia .= $idEstadoCita[$e];
                        //Concatena la , en la sentencia IN
                        if ($e < count($idEstadoCita)) {
                            $sentencia .= ",";
                        }
                    }
                }
                $sentencia .= ')';
                $concatenacion .= $conector . "C.id_estado_cita IN " . $sentencia . " ";
                $conector = "AND ";
            }

            if ($convenio != '') {
                $concatenacion .= $conector . "C.id_convenio=" . $convenio . " ";
                $conector = "AND ";
            }

            if ($fechaInicial != '' && $fechaFinal != '') {
                $concatenacion .= $conector . "DATE(C.fecha_cita) BETWEEN '" . $fechaInicial . "' AND '" . $fechaFinal . "' ";
                $conector = "AND ";
            } else if ($fechaInicial != '') {
                $concatenacion .= $conector . "DATE(C.fecha_cita)>='" . $fechaInicial . "' ";
                $conector = "AND ";
            } else if ($fechaFinal != '') {
                $concatenacion .= $conector . "DATE(C.fecha_cita)<='" . $fechaFinal . "' ";
                $conector = "AND ";
            }
            if ($cita != '') {
                $concatenacion .= $conector . " (CONCAT(C.nombre_1, ' ',C.nombre_2, ' ',C.apellido_1, ' ',C.apellido_2) LIKE '%" . $cita . "%'
											OR C.numero_documento LIKE '%" . $cita . "%'
											OR C.telefono_contacto LIKE '%" . $cita . "%'
											OR C.observacion_cita LIKE '%" . $cita . "%'
											OR C.observacion_cancela LIKE '%" . $cita . "%'
											OR C.id_cita LIKE '%" . $cita . "%')";
                $conector = "AND ";
            }
            if ($hora != "") {
                $hora_aux = "";
                if ($hora == "1") {
                    $hora_aux = "<='11:00'";
                } else if ($hora == "2") {
                    $hora_aux = ">='12:00'";
                }
                $concatenacion .= $conector . " DATE_FORMAT(C.fecha_cita, '%H:%i') " . $hora_aux;
                $conector = "AND ";
            }

            if ($ind_conteo != 1) {
                //Limite de sentencia
                if ($limitado == 0) {
                    $limite = "LIMIT 100";
                } else if ($limitado == 1) {
                    $limite = "";
                }

                $sql = "SELECT * FROM (
								SELECT P.nombre_1 AS Pnombre_1, P.nombre_2 AS Pnombre_2,P.apellido_1 AS Papellido_1, P.apellido_2 AS Papellido_2 , P.telefono_1,C.*, DATE_FORMAT(C.fecha_cita, '%H:%i') AS hora_aux, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS profesional_aux,
								TC.nombre_tipo_cita, LD.nombre_detalle, CV.nombre_convenio AS nombre_convenio_aux, U.nombre_usuario, U.apellido_usuario,
								LC.nombre_detalle AS lugar_cita, DATE_FORMAT(C.fecha_cita, '%d/%m/%Y') AS fecha_cita_t, DATE_FORMAT(C.fecha_cita, '%h:%i %p') AS hora_cita_t,
								CONCAT(UC.nombre_usuario, ' ', UC.apellido_usuario) AS nombre_usuario_crea,
								CONCAT(UM.nombre_usuario, ' ', UM.apellido_usuario) AS nombre_usuario_modifica,
								DATE_FORMAT(C.fecha_mod, '%d/%m/%Y') AS fecha_mod_t, DATE_FORMAT(C.fecha_mod, '%h:%i %p') AS hora_mod_t
								FROM citas C
								INNER JOIN usuarios U ON U.id_usuario=C.id_usuario_prof
								INNER JOIN listas_detalle LD ON C.id_estado_cita=LD.id_detalle
								INNER JOIN listas_detalle LC ON C.id_lugar_cita=LC.id_detalle
								INNER JOIN tipos_citas TC ON TC.id_tipo_cita=C.id_tipo_cita
								INNER JOIN convenios CV ON CV.id_convenio=C.id_convenio
								LEFT JOIN usuarios UC ON C.id_usuario_crea = UC.id_usuario 
								LEFT JOIN usuarios UM ON C.id_usuario_mod=UM.id_usuario 
								LEFT JOIN pacientes P ON C.id_paciente = P.id_paciente " .
                        $concatenacion .
                        "ORDER BY C.fecha_cita
								) T " .
                        $limite;
            } else {
                $sql = "SELECT COUNT(*) AS cantidad FROM (
								SELECT C.id_cita
								FROM citas C " .
                        $concatenacion .
                        ") T";
            }
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /*
     * Esta funcion obtiene los registros con base en el parametro ingresado para buscar en el formulario admisiones/estado.php
     */

    public function buscarEstadoagendados($estado_cita, $parametro) {
        try {
            $parametro = str_replace(" ", "%", $parametro);

            $sql = "SELECT P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, DATE_FORMAT(C.fecha_cita, '%H:%i') AS fecha_cita,
					U.nombre_usuario, U.apellido_usuario, C.id_cita, C.id_paciente, P.numero_documento, LD.nombre_detalle,
					CV.nombre_convenio, DATE_FORMAT(C.fecha_cita, '%h:%i %p') AS hora_cita, C.observacion_cita, C.fecha_llegada,
					C.fecha_ingreso, DATE_FORMAT(C.fecha_llegada,'%h:%i %p') AS fecha_llegada2_aux, DATE_FORMAT(C.fecha_ingreso,'%h:%i %p') AS fecha_ingreso2_aux
					FROM citas C
					INNER JOIN pacientes P ON C.id_paciente=P.id_paciente
					INNER JOIN usuarios U ON U.id_usuario=C.id_usuario_prof
					INNER JOIN listas_detalle LD ON LD.id_detalle=C.id_estado_cita
					INNER JOIN convenios CV ON CV.id_convenio=C.id_convenio
					WHERE C.fecha_cita BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 DAY)
					AND (C.id_estado_cita=".$estado_cita." OR C.id_estado_cita=130 OR C.id_estado_cita=17)
					AND (CONCAT(P.nombre_1, ' ', IFNULL(P.nombre_2, ''), ' ', P.apellido_1, ' ', IFNULL(P.apellido_2, '')) LIKE '%".$parametro."%'
					OR P.numero_documento LIKE '%".$parametro."%'
					OR CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) LIKE '%".$parametro."%'
					OR LD.nombre_detalle LIKE '%".$parametro."%') 
					ORDER BY fecha_cita ASC";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /*
     * Esta funcion retorna las citas pendientes por día de un usuario en un mes dado
     * $ind_pago: 1. Sí - 0. No
     */

    public function getReporteOportunidad($fecha_inicial, $fecha_final, $id_convenio, $id_plan, $ind_pago) {
        try {
            if ($ind_pago == 1) {
                $sql = "SELECT DH.id_hc, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, P.numero_documento, fu_calcular_edad(P.fecha_nacimiento, C.fecha_cita) AS edad_aux, 				LD.codigo_detalle AS sexo_aux, 
DATE_FORMAT(CASE WHEN C2.id_cita IS NOT NULL THEN C.fecha_crea ELSE NULL END, '%d/%m/%Y') AS fecha_repro,
DATE_FORMAT(COALESCE(C3.fecha_crea, C2.fecha_crea, C.fecha_crea), '%d/%m/%Y') AS fecha_ori,
DATE_FORMAT(CASE WHEN C2.id_cita IS NOT NULL THEN C.fecha_cita ELSE NULL END, '%d/%m/%Y') AS fecha_cita_repro,
DATE_FORMAT(COALESCE(C3.fecha_cita, C2.fecha_cita, C.fecha_cita), '%d/%m/%Y') AS fecha_cita_ori,
CX2.codciex AS cod_ciex, CX2.nombre AS descripcion_diagnostico, PD.cod_procedimiento, MP.nombre_procedimiento, PD.cantidad, 
A.nombre_med_orden, C.observacion_cita, A.motivo_consulta, CV.nombre_convenio, CV.id_convenio, PL.nombre_plan, PL.id_plan
								FROM citas C 
								LEFT JOIN citas AS C2 ON C.id_cita_ori=C2.id_cita
								LEFT JOIN citas AS C3 ON C2.id_cita_ori=C3.id_cita 
								INNER JOIN admisiones A ON A.id_cita=C.id_cita
								INNER JOIN pacientes P ON A.id_paciente=P.id_paciente
								LEFT JOIN listas_detalle LD ON P.sexo=LD.id_detalle
								LEFT JOIN (
									SELECT id_admision, MAX(id_hc) AS id_hc
									FROM historia_clinica
									GROUP BY id_admision
								) HC ON A.id_admision=HC.id_admision
								LEFT JOIN diagnosticos_hc DH ON DH.id_hc=HC.id_hc AND DH.orden=1
								LEFT JOIN vi_ciex CX ON CX.codciex=DH.cod_ciex
								LEFT JOIN ciex CX2 ON CX.codciexori=CX2.codciex
								LEFT JOIN convenios CV ON C.id_convenio = CV.id_convenio
								LEFT JOIN planes PL ON A.id_plan= PL.id_plan
								INNER JOIN pagos PA ON A.id_admision=PA.id_admision
								INNER JOIN pagos_detalle PD ON PA.id_pago=PD.id_pago AND PD.tipo_precio='P'
								INNER JOIN maestro_procedimientos MP ON PD.cod_procedimiento=MP.cod_procedimiento
								WHERE C.fecha_cita BETWEEN '".$fecha_inicial."' AND '".$fecha_final."'
								AND C.id_convenio=".$id_convenio." ";
					if ($id_plan != "") {
							$sql .= "AND A.id_plan=".$id_plan." ";
								}
								
								$sql .= " AND PA.estado_pago=2
								ORDER BY C.id_cita ";
            } else {
                $sql = " SELECT '' AS id_hc, C.nombre_1, C.nombre_2, C.apellido_1, C.apellido_2, C.numero_documento, '' AS edad_aux, '' AS sexo_aux,
								DATE_FORMAT(IFNULL(C2.fecha_crea, C.fecha_crea), '%d/%m/%Y') AS fecha_cita, DATE_FORMAT(C.fecha_cita, '%d/%m/%Y') AS fecha_admision,
								'' AS cod_ciex, '' AS descripcion_diagnostico, '' AS cod_procedimiento, '' AS nombre_procedimiento, '' AS cantidad,
								'' AS nombre_med_orden, C.observacion_cita, '' AS motivo_consulta, ''AS nombre_convenio, '' AS id_convenio, '' AS nombre_plan, '' AS id_plan
								FROM citas C 
								LEFT JOIN citas AS C2 ON C.id_cita_ori=C2.id_cita 
								WHERE C.id_estado_cita=17
								AND C.fecha_cita BETWEEN '".$fecha_inicial."' AND '".$fecha_final."'
								AND C.id_convenio=".$id_convenio." ";
								
					if ($id_plan != "") {
							$sql .= "AND A.id_plan=".$id_plan." ";
								}
								
								$sql .= "AND C.id_cita NOT IN (
									SELECT id_cita
									FROM admisiones A
									INNER JOIN pagos P ON A.id_admision=P.id_admision
									WHERE A.id_cita IS NOT NULL
									AND P.estado_pago=2
								)
								ORDER BY C.id_cita";
								
			
								
            }
			echo ($sql);
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function get_lista_citas_cambio_estado($parametro) {
        try {
            $parametro = str_replace(" ", "%", $parametro);
            $sql = "SELECT C.*, TD.codigo_detalle AS cod_tipo_documento, TD.nombre_detalle AS tipo_documento,
							EC.nombre_detalle AS nombre_estado_cita, TC.nombre_tipo_cita, DATE_FORMAT(C.fecha_cita, '%d/%m/%Y') AS fecha_cita_t
							FROM citas C
							INNER JOIN listas_detalle EC ON C.id_estado_cita=EC.id_detalle
							INNER JOIN tipos_citas TC ON C.id_tipo_cita=TC.id_tipo_cita
							LEFT JOIN listas_detalle TD ON C.id_tipo_documento=TD.id_detalle
							WHERE (C.numero_documento LIKE '" . $parametro . "%'
							OR CONCAT(IFNULL(C.nombre_1,''),' ',IFNULL(C.nombre_2,''),' ',IFNULL(C.apellido_1,''),' ',IFNULL(C.apellido_2,'')) LIKE '%" . $parametro . "%')
							AND DATE(C.fecha_cita)=CURDATE()
							AND C.id_estado_cita IN (130, 258)
							ORDER BY C.nombre_1, C.nombre_2, C.apellido_1, C.apellido_2, C.fecha_cita
							LIMIT 10";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function get_lista_estados_mover_cita_atencion($id_estado_cita) {
        try {
            $sql = "SELECT * FROM listas_detalle
							WHERE id_lista=4
							AND ind_activo=1
							AND id_detalle IN (14, 130)
							AND orden<(SELECT orden FROM listas_detalle WHERE id_detalle=" . $id_estado_cita . ")
							ORDER BY orden";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function guardar_cambio_estado_cita($id_cita, $id_estado_cita, $id_usuario) {
        try {
            $sql = "CALL pa_guardar_cambio_estado_cita(" . $id_cita . ", " . $id_estado_cita . ", " . $id_usuario . ", @id)";

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $resultado = $arrResultado["@id"];

            return $resultado;
        } catch (Exception $e) {
            return -2;
        }
    }

    //Función que retorna las citas no canceladas asociadas a una programación de cirugía
    public function getListaCitasProgCx($id_prog_cx) {
        try {
            $sql = "SELECT C.*, DATE_FORMAT(C.fecha_cita, '%d/%m/%Y') AS fecha_cita_t,
							DATE_FORMAT(C.fecha_cita, '%h:%i %p') AS hora_cita_t, LC.nombre_detalle AS lugar_cita
							FROM citas C
							INNER JOIN listas_detalle LC ON C.id_lugar_cita=LC.id_detalle
							WHERE C.id_prog_cx=" . $id_prog_cx . "
							AND C.id_estado_cita NOT IN (15, 159)
							AND C.id_estado_cita
							ORDER BY C.fecha_cita, C.id_cita";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getRecomendacionesActivas() {
        try {
            $sql = "SELECT *
                    FROM citas_recom_predef
                    WHERE ind_activo = 1;";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    
    public function getHistoricoCitasByIdPaciente($idPaciente) {
        try {
            $sql = "SELECT P.nombre_1 AS Pnombre_1, P.nombre_2 AS Pnombre_2,P.apellido_1 AS Papellido_1, P.apellido_2 AS Papellido_2 ,C.*, DATE_FORMAT(C.fecha_cita, '%H:%i') AS hora_aux, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS profesional_aux, TC.nombre_tipo_cita, LD.nombre_detalle, CV.nombre_convenio AS nombre_convenio_aux, U.nombre_usuario, U.apellido_usuario, LC.nombre_detalle AS lugar_cita, DATE_FORMAT(C.fecha_cita, '%d/%m/%Y') AS fecha_cita_t, DATE_FORMAT(C.fecha_cita, '%h:%i %p') AS hora_cita_t, CONCAT(UM.nombre_usuario, ' ', UM.apellido_usuario) AS nombre_usuario_modifica, DATE_FORMAT(C.fecha_mod, '%d/%m/%Y') AS fecha_mod_t, DATE_FORMAT(C.fecha_mod, '%h:%i %p') AS hora_mod_t 
                    FROM citas C 
                    INNER JOIN usuarios U ON U.id_usuario=C.id_usuario_prof 
                    INNER JOIN listas_detalle LD ON C.id_estado_cita=LD.id_detalle 
                    INNER JOIN listas_detalle LC ON C.id_lugar_cita=LC.id_detalle 
                    INNER JOIN tipos_citas TC ON TC.id_tipo_cita=C.id_tipo_cita 
                    INNER JOIN convenios CV ON CV.id_convenio=C.id_convenio 
                    LEFT JOIN usuarios UM ON C.id_usuario_mod=UM.id_usuario 
                    LEFT JOIN pacientes P ON C.id_paciente = P.id_paciente 
                    WHERE C.id_paciente = $idPaciente 
                    ORDER BY C.fecha_cita DESC LIMIT 100";
					
				
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
	public function getSedesAlternativas(){
		try{
			$sql="SELECT * FROM sedes_alternativas
				WHERE ind_activo=1";
			return $this->getDatos($sql);	
		}catch(Exception $e){
			return array();
		}
	}

}

?>
