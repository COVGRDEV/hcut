<?php

require_once("DbConexion.php");

class DbUsuariosPerfiles extends DbConexion {
    public function getUsuarios($perfil) {
        try {
            $sql = "SELECT U.*, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_completo
                    FROM usuarios U
                    INNER JOIN usuarios_perfiles UP ON UP.id_usuario = U.id_usuario
                    INNER JOIN perfiles P ON P.id_perfil = UP.id_perfil
                    WHERE P.id_perfil = $perfil AND U.ind_activo = 1
                    ORDER BY U.nombre_usuario, U.apellido_usuario";
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
    public function getListaUsuariosIndAtiende($ind_atiende) {
        try {
            $sql = "SELECT DISTINCT U.*, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_completo ".
				   "FROM usuarios U ".
				   "INNER JOIN usuarios_perfiles UP ON U.id_usuario=UP.id_usuario ".
				   "INNER JOIN perfiles P ON UP.id_perfil=P.id_perfil ".
				   "WHERE U.ind_activo=1 ".
				   "AND P.ind_atiende=".$ind_atiende." ".
				   "ORDER BY U.nombre_usuario, U.apellido_usuario";
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
    public function getListaUsuariosIndCirugia($ind_cirugia, $estado, $otro) {
        try {
            $sql = "SELECT DISTINCT U.id_usuario, U.ind_activo, P.ind_activo, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_completo, U.nombre_usuario, U.apellido_usuario ".
				   "FROM usuarios U ".
				   "INNER JOIN usuarios_perfiles UP ON U.id_usuario=UP.id_usuario ".
				   "INNER JOIN perfiles P ON UP.id_perfil=P.id_perfil ".
				   "WHERE ". //U.ind_activo=1 ".
				   "P.ind_cirugia=".$ind_cirugia." AND ind_anonimo=0 "; 
				   
				   if ($estado==1 or $estado==0) { 
						$sql .= "AND U.ind_activo=".$estado." AND P.ind_activo=".$estado. " "; 
				   } //else //recupera el histórico
				   
				   if ($otro==1) { 
						$sql .= "UNION SELECT 0, 0, 0, 'Otro' AS nombre_usuario, 'ZZZ' AS nombre_usuario, 'ZZZ' AS apellido_usuario "; 
						$sql .= "UNION SELECT 0, 0, 0, 'Marcos Octavio Cuadros Segovia' AS nombre_usuario, 'ZZZ' AS nombre_usuario, 'ZZZ' AS apellido_usuario "; 
				   }
				   
			$sql.= "ORDER BY nombre_usuario, apellido_usuario";
			//echo "<br>".$sql;
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }	
}

?>