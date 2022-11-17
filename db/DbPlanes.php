<?php
require_once("DbConexion.php");

class DbPlanes extends DbConexion {
    public function getListaPlanesActivos($id_convenio) {//Plan segun el Convenio
        try {
			$sql = "SELECT P.*, TU.codigo_detalle AS cod_tipo_usuario, TU.nombre_detalle AS tipo_usuario
					FROM planes P
					LEFT JOIN listas_detalle TU ON P.id_tipo_usuario=TU.id_detalle
					WHERE P.id_convenio=".$id_convenio."
					AND P.ind_activo=1
					ORDER BY P.nombre_plan";
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
	public function getPlanDetalle($id_plan){
		try{
			
			 $sql = "SELECT P.*, TU.codigo_detalle AS cod_tipo_usuario, TU.nombre_detalle AS tipo_usuario, C.nombre_convenio, C.numero_documento,
			 		TC.nombre_detalle AS cobertura_nombre
					FROM planes P
					LEFT JOIN listas_detalle TU ON P.id_tipo_usuario=TU.id_detalle
					LEFT JOIN listas_detalle TC ON P.cobertura = TC.id_detalle
					LEFT JOIN convenios C ON P.id_convenio = C.id_convenio
					WHERE P.id_plan=".$id_plan;

			return $this->getUnDato($sql);
		}catch(Exception $e){
			return array();
		}
	}
	public function getListaPlanesActivosAutorizaciones($id_convenio) {//Plan segun el Convenio
        try {					
				$sql =	"SELECT P.id_plan, P.id_convenio, SUBSTRING(P.nombre_plan, 1, 18) AS nombre_plan, 
						P.ind_activo, P.id_usuario_crea, P.fecha_crea, P.id_usuario_mod, P.fecha_mod, P.ind_tipo_pago, 
						P.id_tipo_usuario, P.ind_iss, P.porc_iss, P.id_liq_qx, P.ind_despacho_medicamentos, P.ind_desc_cc,
						TU.codigo_detalle AS cod_tipo_usuario, TU.nombre_detalle AS tipo_usuario 
						FROM planes P 
						LEFT JOIN listas_detalle TU ON P.id_tipo_usuario=TU.id_detalle 
						WHERE P.id_convenio=".$id_convenio."
						AND P.ind_activo=1 
						ORDER BY P.nombre_plan";
			//echo($sql);
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
	
    public function getPlan($id_plan) {//Get los datos del plan seleccionado
        try {
            $sql = "SELECT P.*, TU.codigo_detalle AS cod_tipo_usuario, TU.nombre_detalle AS tipo_usuario
					FROM planes P
					LEFT JOIN listas_detalle TU ON P.id_tipo_usuario=TU.id_detalle
					WHERE P.id_plan=".$id_plan;
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    public function getPlanByConvenio($id_convenio) {//Get los planes segundo el convenio
        try {
            $sql = "SELECT *
                    FROM planes
                    WHERE id_convenio=".$id_convenio."
					ORDER BY nombre_plan";
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
}
?>
