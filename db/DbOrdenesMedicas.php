<?php

require_once("DbConexion.php");

class DbOrdenesMedicas extends DbConexion {

    //Crea el procedimiento
    public function crear_orden_medica($ciex, $descripcion, $codProcedimiento, $tipoOrdenMedica, $idOrdenMedica, $idOrdenMedicaDet, $idHc, $idPaciente, $usuarioCrea, $notaAclaratoria, $datClinicos, $ojo, $idLugar, $medicoRemitente, $fechaHomologacion, $observacion, $tipoProducto, $rango, $tipoCotizante, $plan, $convenio) {
        try {
            $ciex == "" ? $ciex = "NULL" : $ciex = "'" . $ciex . "'";
            $descripcion == "" ? $descripcion = "NULL" : $descripcion = "'" . $descripcion . "'";
            $codProcedimiento == "" ? $codProcedimiento = "NULL" : $codProcedimiento = "'" . $codProcedimiento . "'";
            $tipoOrdenMedica == "" ? $tipoOrdenMedica = "NULL" : $tipoOrdenMedica = "" . $tipoOrdenMedica . "";
            $idOrdenMedica == "" ? $idOrdenMedica = "NULL" : $idOrdenMedica = "" . $idOrdenMedica . "";
            $idHc == "" ? $idHc = "NULL" : $idHc = "" . $idHc . "";
            $idPaciente == "" ? $idPaciente = "NULL" : $idPaciente = "" . $idPaciente . "";
            $usuarioCrea == "" ? $usuarioCrea = "NULL" : $usuarioCrea = "" . $usuarioCrea . "";
            $idOrdenMedicaDet == "" ? $idOrdenMedicaDet = "NULL" : $idOrdenMedicaDet = "" . $idOrdenMedicaDet . "";
            $notaAclaratoria == "" ? $notaAclaratoria = "NULL" : $notaAclaratoria = "'" . $notaAclaratoria . "'";
            $ojo == "" ? $ojo = "NULL" : $ojo = "" . $ojo . "";
            $datClinicos == "" ? $datClinicos = "NULL" : $datClinicos = "'" . $datClinicos . "'";
            $idLugar == "" ? $idLugar = "NULL" : $idLugar = "" . $idLugar . "";
            $medicoRemitente == "" ? $medicoRemitente = "NULL" : $medicoRemitente = "'" . $medicoRemitente . "'";
            $fechaHomologacion == "" ? $fechaHomologacion = "NULL" : $fechaHomologacion = "'" . $fechaHomologacion . "'";
            $observacion == "" ? $observacion = "NULL" : $observacion = "'" . $observacion . "'";
            $tipoProducto == "" ? $tipoProducto = "NULL" : $tipoProducto = "" . $tipoProducto . "";

            $rango == "" ? $rango = "NULL" : $rango = "" . $rango . "";
            $tipoCotizante == "" ? $tipoCotizante = "NULL" : $tipoCotizante = "" . $tipoCotizante . "";
            $plan == "" ? $plan = "NULL" : $plan = "" . $plan . "";
            $convenio == "" ? $convenio = "NULL" : $convenio = "" . $convenio . "";
			
            $sql = "CALL pa_crear_orden_medica(" . $ciex . "," . $descripcion . "," . $codProcedimiento . ", " . $tipoOrdenMedica . "," .
                    $idOrdenMedica . "," . $idOrdenMedicaDet . "," . $idHc . "," . $idPaciente . ", " . $usuarioCrea . ", " . $notaAclaratoria . ", " .
                    $datClinicos . ", " . $ojo . ", " . $idLugar . ", " . $medicoRemitente . ", " . $observacion . ", " . $fechaHomologacion . ", " .
                    $tipoProducto . ", " . $rango. ", " . $tipoCotizante. ", " . $plan. ", " . $convenio .", @id, @id2)";
            
            //echo($sql);
            $arrCampos[0] = "@id";
            $arrCampos[1] = "@id2";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $resultado = $arrResultado["@id"] . ";" . $arrResultado["@id2"];
            
            return $resultado;
        } catch (Exception $e) {
            return array();
        }
    }

    //Crea el procedimiento
    public function eliminar_orden_medica($idOrdenMedica, $idOrdenMedDet, $notaAclaratoria, $id_usuario) {
        try {
            $idOrdenMedica == "" ? $idOrdenMedica = "NULL" : $idOrdenMedica = "" . $idOrdenMedica . "";
            $idOrdenMedDet == "" ? $idOrdenMedDet = "NULL" : $idOrdenMedDet = "" . $idOrdenMedDet . "";
            $notaAclaratoria == "" ? $notaAclaratoria = "NULL" : $notaAclaratoria = "'" . $notaAclaratoria . "'";
            $id_usuario == "" ? $id_usuario = "NULL" : $id_usuario = "" . $id_usuario . "";

            $sql = "CALL pa_eliminar_orden_medica(" . $idOrdenMedica . "," . $idOrdenMedDet . "," . $notaAclaratoria . ", " . $id_usuario . ", @id)";
            //echo $sql;
            
            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $resultado = $arrResultado["@id"];
            
            return $resultado;
        } catch (Exception $e) {
            return array();
        }
    }

    /*
     * Esta funcion obtiene todo el listado de procedimientos
     */

    public function getOrdenMedicaActivaByHC($hc) {
        try {
            $sql = "SELECT * FROM ordenes_medicas WHERE id_hc = $hc AND ind_estado IN (1,2);";

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getOrdenMedicaActivaById($id) {
        try {
            $sql = "SELECT OM.*, DATE_FORMAT(OM.fecha_crea,'%d/%m/%Y %H:%i:%s %p') AS fechaOrdenMedica, TP.nombre_tipo_cita, 
                    DATEDIFF(NOW(),OM.fecha_crea) diasTranscurridos, C.nombre_convenio AS nombreConvenioAux, A.id_admision,
                    SD.dir_logo_sede_det, SD.dir_sede_det, SD.tel_sede_det, P.numero_documento, HC.id_usuario_crea AS profesional_aux,
                    DATE_FORMAT(OM.fecha_homologacion,'%d/%m/%Y') AS fecha_homologacion_aux, LD.nombre_detalle AS lugar_aux, PL.nombre_plan  AS nombrePlanAux,
                    C.ind_activo AS ind_activo_convenio, PL.ind_activo AS ind_activo_plan
                    FROM ordenes_medicas  OM
                    LEFT JOIN historia_clinica HC ON OM.id_hc = HC.id_hc
                    LEFT JOIN admisiones A ON HC.id_admision = A.id_admision
                    LEFT JOIN tipos_citas TP ON A.id_tipo_cita = TP.id_tipo_cita
                    LEFT JOIN convenios C ON OM.id_convenio = C.id_convenio
                    LEFT JOIN planes PL ON PL.id_plan = OM.id_plan
                    LEFT JOIN sedes_det SD ON SD.id_detalle = OM.id_lugar_orden_m
                    LEFT JOIN listas_detalle LD ON LD.id_detalle = SD.id_detalle 
                    LEFT JOIN pacientes P ON P.id_paciente = OM.id_paciente
                    WHERE OM.id_orden_m = $id;";

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /*
     * Esta funcion obtiene todo el listado de procedimientos
     */

    public function getProcedimientosActivosOrdenMedicaByHC($hc) {
        try {
            $sql = "SELECT OMD.*, PP.nom_paquete_p, MP.nombre_procedimiento, CX.nombre AS nombreCiex
						FROM ordenes_medicas_det OMD
						INNER JOIN ordenes_medicas OM ON OMD.id_orden_m = OM.id_orden_m
						LEFT JOIN paquetes_procedimientos PP ON OMD.id_paquete_p = PP.id_paquete_p
						LEFT JOIN maestro_procedimientos MP ON OMD.cod_procedimiento = MP.cod_procedimiento
						LEFT JOIN ciex CX ON OMD.ciex_orden_m_det = CX.codciex 
						WHERE OM.id_hc = $hc
						AND OMD.ind_estado = 1;";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getProcedimientosActivosOrdenMedicaByIdOrdenMedica($id) {
        try {
            $sql = "SELECT OMD.*, PP.nom_paquete_p, MP.nombre_procedimiento,
						CX.codciex AS cod_ciex_aux, CX.nombre AS nom_ciex_aux
						FROM ordenes_medicas_det OMD
						INNER JOIN ordenes_medicas OM ON OMD.id_orden_m = OM.id_orden_m
						LEFT JOIN  paquetes_procedimientos PP ON OMD.id_paquete_p = PP.id_paquete_p
						LEFT JOIN  maestro_procedimientos MP ON OMD.cod_procedimiento = MP.cod_procedimiento
						LEFT JOIN vi_ciex CX ON CX.codciex = OMD.ciex_orden_m_det
						WHERE OMD.id_orden_m = $id
						AND OMD.ind_estado = 1;";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function consultarOrdenesMedicasPacientes($param) {
        try {
            $sql = "SELECT OM.*, P.*, l.nombre_detalle AS tipo_documento, DATE_FORMAT(P.fecha_nacimiento, '%d/%m/%Y') AS fecha_nac_persona,
                    CV.nombre_convenio AS nombreConvenioAux, fu_calcular_edad(P.fecha_nacimiento,CURDATE()) AS edadPaciente, PL.nombre_plan AS nombrePlanAux
                    FROM ordenes_medicas OM
                    INNER JOIN pacientes P ON OM.id_paciente = P.id_paciente
                    INNER JOIN listas_detalle l ON l.id_detalle = P.id_tipo_documento
                    LEFT JOIN convenios CV ON P.id_convenio_paciente = CV.id_convenio
                    LEFT JOIN planes PL ON PL.id_plan = P.id_plan
                    WHERE (P.numero_documento LIKE '%$param%'
                    OR CONCAT(P.nombre_1, ' ', IFNULL(P.nombre_2, ''), ' ', P.apellido_1, ' ', IFNULL(P.apellido_2, '')) LIKE '%$param%')
                    GROUP BY OM.id_paciente
                    ORDER BY OM.ind_estado, OM.fecha_crea
                    LIMIT 10;";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function ordenesMedicasByPaciente($idPaciente) {
        try {
            $sql = "SELECT OM.*, P.*, l.nombre_detalle AS tipo_documento, DATE_FORMAT(P.fecha_nacimiento, '%d/%m/%Y') AS fecha_nac_persona, 
						CV.nombre_convenio, fu_calcular_edad(P.fecha_nacimiento,CURDATE()) AS edadPaciente, COUNT(OMD.id_orden_m_det) AS cantProcedimientos,
						DATE_FORMAT(OM.fecha_crea,'%d/%m/%Y') AS fecha_crea_auto_aux, LDS.nombre_detalle AS nom_sede_aux, PL.nombre_plan
						FROM ordenes_medicas OM
						INNER JOIN ordenes_medicas_det OMD ON OM.id_orden_m = OMD.id_orden_m
						INNER JOIN pacientes P ON OM.id_paciente = P.id_paciente
						INNER JOIN listas_detalle l ON l.id_detalle = P.id_tipo_documento
						LEFT JOIN convenios CV ON P.id_convenio_paciente = CV.id_convenio
                                                LEFT JOIN planes PL ON PL.id_plan = OM.id_plan
						INNER JOIN sedes_det SD ON SD.id_detalle = OM.id_lugar_orden_m
						INNER JOIN listas_detalle LDS ON LDS.id_detalle = SD.id_detalle
						WHERE P.id_paciente = $idPaciente
						GROUP BY OM.id_orden_m
						ORDER BY OM.ind_estado, OM.fecha_crea DESC;";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getOrdenMedicaDetById($idDet) {
        try {
            $sql = "SELECT OMD.*, PP.nom_paquete_p, MP.nombre_procedimiento, CX.nombre AS nombreCiex, 
						PP.ind_auto_anestesia, PP.ind_auto_honorarios_medicos, PP.ind_auto_ayudantia, PP.ind_auto_derechos_sala,
						O.id_hc
						FROM ordenes_medicas_det OMD
						INNER JOIN ordenes_medicas O ON O.id_orden_m = OMD.id_orden_m
						LEFT JOIN  paquetes_procedimientos PP ON OMD.id_paquete_p = PP.id_paquete_p
						LEFT JOIN  maestro_procedimientos MP ON OMD.cod_procedimiento = MP.cod_procedimiento
						LEFT JOIN ciex CX ON OMD.ciex_orden_m_det = CX.codciex                    
						WHERE OMD.id_orden_m_det=" . $idDet;

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getProveedoresActivosProcedimientos() {
        try {
            $sql = "SELECT * 
						FROM proveedores_procedimientos 
						WHERE ind_activo=1";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getProveedorProcedimientosById($id) {
        try {
            $sql = "SELECT * FROM proveedores_procedimientos
						WHERE id_proveedor_procedimiento=" . $id;

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function consultarPacientesHomologarOrdenMedica($param) {
        try {
            $sql = "SELECT P.*, l.nombre_detalle AS tipo_documento, DATE_FORMAT(P.fecha_nacimiento, '%d/%m/%Y') AS fecha_nac_persona,
						CV.nombre_convenio AS nombreConvenioAux, fu_calcular_edad(P.fecha_nacimiento,CURDATE()) AS edadPaciente, PL.nombre_plan AS nombrePlanAux
						FROM pacientes P
						INNER JOIN listas_detalle l ON l.id_detalle = P.id_tipo_documento						  
						LEFT JOIN convenios CV ON P.id_convenio_paciente = CV.id_convenio
                                                LEFT JOIN planes PL ON PL.id_plan = P.id_plan                                                
						WHERE (P.numero_documento LIKE '$param%' OR
						CONCAT(P.nombre_1, ' ', IFNULL(P.nombre_2, ''), ' ', P.apellido_1, ' ', IFNULL(P.apellido_2,'')) LIKE '$param%')
						LIMIT 20;";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaProveedoresParametro($parametro, $ind_activo = "") {
        try {
            $parametro = str_replace(" ", "%", $parametro);
            $sql = "SELECT * FROM proveedores_procedimientos
						WHERE (numero_documento LIKE '" . $parametro . "%'
						OR nombre_proveedor_procedimiento LIKE '%" . $parametro . "%') ";
            if ($ind_activo != "") {
                $sql .= "AND ind_activo=" . $ind_activo . " ";
            }
            $sql .= "ORDER BY nombre_proveedor_procedimiento";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

}

?>
