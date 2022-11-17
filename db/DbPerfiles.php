<?php

require_once("DbConexion.php");

class DbPerfiles extends DbConexion {

    //Muestra los perfiles que estan en estado activos e indicador de atiende con valor de 1
    public function getPerfiles() {
        try {
            $sql = "SELECT *
						FROM perfiles
						WHERE ind_activo=1
						AND ind_atiende=1
						ORDER BY nombre_perfil";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    //Muestra los perfiles que estan en estado activos e indicador de atiende con valor de 1
    public function getListaPerfiles() {
        try {
            $sql = "SELECT * FROM perfiles
						ORDER BY ind_activo DESC, nombre_perfil";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    //Devulve los datos de un perfil
    public function getUnPerfil($id_perfil) {
        try {
            $sql = "SELECT * FROM perfiles WHERE id_perfil = $id_perfil";
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    //Devulve los menus y los permiso que tiene el perfil
    public function getPermisosMenus($id_perfil) {
        try {
            $sql = "SELECT * FROM permisos WHERE id_perfil = $id_perfil";
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Metodo para crear un perfil
     */
    public function crearPerfil($nombre_perfil, $descripcion, $ind_atiende, $ind_citas, $ind_cirugia, $id_menu_inicio, $id_usuario, $menus_permisos, $ind_modificar_pagos = "0", $ind_registrar_pagos = "0") {
        try {
            $sql = "CALL pa_crear_perfil('" . $nombre_perfil . "', '" . $descripcion . "', " . $ind_atiende . ", " . $ind_citas . ", " .
                    $ind_cirugia . ", " . $id_menu_inicio . ", " . $ind_modificar_pagos . ", " . $ind_registrar_pagos . ", " . $id_usuario . ", @id)";
            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $id_perfil = $arrResultado["@id"];

            if ($id_perfil > 0) {
                $array_menus_permisos = explode("-", $menus_permisos);
                foreach ($array_menus_permisos as $fila) {
                    if ($fila != '') {
                        $menu_permiso = explode(",", $fila);
                        $menu = $menu_permiso[0];
                        $permiso = $menu_permiso[1];
                        if ($permiso != '') {
                            $sql = "INSERT INTO permisos
										(id_perfil, id_menu, tipo_acceso, id_usuario_crea, fecha_crea)
										VALUES (" . $id_perfil . ", " . $menu . ", " . $permiso . ", " . $id_usuario . ", NOW())";
                            $arrCampos[0] = "@id";
                            $this->ejecutarSentencia($sql, $arrCampos);
                        }
                    }
                }
            }
            return $id_perfil;
        } catch (Exception $e) {
            return -2;
        }
    }

    /**
     * Metodo para editar un perfil
     */
    public function editarPerfil($id_perfil, $nombre_perfil, $descripcion, $ind_atiende, $ind_citas, $ind_cirugia, $id_menu_inicio, $ind_activo, $id_usuario, $menus_permisos, $ind_modificar_pagos = "0", $ind_registrar_pagos = "0", $ind_modificar_autorizaciones = "0") {
        try {
            if (trim($id_menu_inicio) == "") {
                $id_menu_inicio = "NULL";
            }
            $sql = "CALL pa_editar_perfil(" . $id_perfil . ", '" . $nombre_perfil . "', '" . $descripcion . "', " . $ind_atiende . ", " . $ind_citas . ", " .
                    $ind_cirugia . ", " . $id_menu_inicio . ", " . $ind_modificar_pagos . ", " . $ind_registrar_pagos . ", " . $ind_modificar_autorizaciones . ",
					" . $ind_activo . ", " . $id_usuario . ", @id)";

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $id_resultado = $arrResultado["@id"];

            if ($id_resultado > 0) {
                $array_menus_permisos = explode("-", $menus_permisos);
                foreach ($array_menus_permisos as $fila) {
                    if ($fila != '') {
                        $menu_permiso = explode(",", $fila);
                        $menu = $menu_permiso[0];
                        $permiso = $menu_permiso[1];
                        if ($permiso != '') {
                            $sql = "INSERT INTO permisos
										(id_perfil, id_menu, tipo_acceso, id_usuario_crea, fecha_crea)
										VALUES (" . $id_perfil . ", " . $menu . ", " . $permiso . ", " . $id_usuario . ", NOW())";
                            $arrCampos[0] = "@id";
                            $this->ejecutarSentencia($sql, $arrCampos);
                        }
                    }
                }
            }
            return $id_resultado;
        } catch (Exception $e) {
            return -2;
        }
    }

    //Muestra los perfiles activos que tienen permisos sobre un menú específico
    public function getListaPerfilesMenu($id_menu, $tipo_acceso) {
        try {
            $sql = "SELECT DISTINCT P.*
						FROM perfiles P
						INNER JOIN permisos PR ON P.id_perfil=PR.id_perfil
						WHERE PR.id_menu=" . $id_menu . "
						AND PR.tipo_acceso=" . $tipo_acceso . "
						ORDER BY P.nombre_perfil";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

}

?>
