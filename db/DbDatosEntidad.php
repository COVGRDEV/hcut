<?php
require_once("DbConexion.php");

class DbDatosEntidad extends DbConexion {
    //Muestra el listado completo de datos_entidad
    public function getListaDatosEntidad($ind_activo = "") {
        try {
            $sql = "SELECT DE.*, LD.codigo_detalle AS cod_tipo_documento, LD.nombre_detalle AS tipo_documento
					FROM datos_entidad DE
					INNER JOIN listas_detalle LD ON LD.id_detalle = DE.id_tipo_documento ";
			if ($ind_activo != "") {
				$sql .= "WHERE DE.ind_activo=".$ind_activo." ";
			}
			$sql .= "ORDER BY DE.id_prestador";
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
    //Obtiene una entidad por su id
    public function getDatosEntidadId($id_prestador) {
        try {
            $sql = "SELECT DE.*, LD.codigo_detalle AS cod_tipo_documento, LD.nombre_detalle AS tipo_documento
					FROM datos_entidad DE
					INNER JOIN listas_detalle LD ON LD.id_detalle = DE.id_tipo_documento
					WHERE DE.id_prestador=".$id_prestador;
			
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
    //Muestra el listado completo de datos_entidad
    public function getDatosEntidad() {
        try {
            $sql = "SELECT DE.*, LD.codigo_detalle, LD.nombre_detalle
                    FROM datos_entidad DE
                    INNER JOIN listas_detalle LD ON LD.id_detalle = DE.id_tipo_documento
                    GROUP BY DE.nombre_prestador";
			
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
    //Guarda e registro
    public function guardaDatosEntidad($id_prestador, $cod_prestador, $nombre_prestador, $sigla_prestador, $id_tipo_documento, $numero_documento, $ind_activo, $id_usuario) {
        try {
            $sql = "CALL pa_crear_datos_entidad(".$id_prestador.", '".$cod_prestador."', '".$nombre_prestador."', '".
					$sigla_prestador."', ".$id_tipo_documento.", '".$numero_documento."', ".$ind_activo.", ".$id_usuario.", @id)";
			
            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $resultado = $arrResultado["@id"];
			
            return $resultado;
        } catch (Exception $e) {
            return array();
        }
    }
}
?>
