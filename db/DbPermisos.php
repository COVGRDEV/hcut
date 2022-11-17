<?php

require_once("DbConexion.php");

class DbPermisos extends DbConexion {
    public function getPermiso($idUsuario) {
        try {
            $sql = "SELECT * " .
                    "FROM permisos " .
                    "WHERE id_usuario=" . $idUsuario;
			
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
	public function getPermisoUsuarioMenu($idUsuario, $idMenu) {
		try {
			$sql = "SELECT PE.* ".
				   "FROM usuarios_perfiles UP ".
				   "INNER JOIN permisos PE ON UP.id_perfil=PE.id_perfil ".
				   "WHERE UP.id_usuario=".$idUsuario." ".
				   "AND PE.id_menu=".$idMenu." ".
				   "ORDER BY PE.tipo_acceso DESC";
			//echo($sql);
			
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}
}
?>
