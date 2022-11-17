<?php

require_once("DbConexion.php");

class DbUsuarios extends DbConexion {

    public function validarIngreso($loginUsuario, $claveUsuario) {
        try {
            $sql = "SELECT *
						FROM usuarios
						WHERE login_usuario= '" . $loginUsuario . "'
						AND clave_usuario=SHA(CONCAT('" . $loginUsuario . "', '|', '" . $claveUsuario . "'))
						AND ind_activo=1";

            $arrResultado = $this->getUnDato($sql);

            if (count($arrResultado) <= 0) {
                $sql = "SELECT 0 AS id_usuario, NULL AS nombre_usuario, NULL AS id_perfil";

                $arrResultado = $this->getUnDato($sql);
            }

            return $arrResultado;
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaUsuariosBuscar($txt_buscar) {
        try {
            $txt_buscar = str_replace(" ", "%", $txt_buscar);
            $sql = "SELECT *
						FROM usuarios u
						WHERE CONCAT(u.nombre_usuario, ' ', u.apellido_usuario) LIKE '%" . $txt_buscar . "%'
						OR u.login_usuario LIKE '%" . $txt_buscar . "%'
						OR u.numero_documento LIKE '%" . $txt_buscar . "%'
						ORDER BY u.ind_activo DESC, u.nombre_usuario, u.apellido_usuario";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaPerfilUsuarios($id_usuario) {
        try {
            $sql = "SELECT *
						FROM perfiles p
						INNER JOIN usuarios_perfiles up ON up.id_perfil=p.id_perfil
						WHERE up.id_usuario=" . $id_usuario . "
						ORDER BY p.nombre_perfil";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getUsuario($id_usuario) {
        try {
            $sql = "SELECT U.*, TD.nombre_detalle AS tipo_documento, TD.codigo_detalle AS cod_tipo_documento, TR.nombre_detalle AS tipo_num_reg
						FROM usuarios U
						LEFT JOIN listas_detalle TD ON U.id_tipo_documento=TD.id_detalle
						LEFT JOIN listas_detalle TR ON U.id_tipo_num_reg=TR.id_detalle
						WHERE U.id_usuario=" . $id_usuario;

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function updatePass($idUsuario, $claveUsuario, $claveUsuario2) {
        try {
            $rta = '';
            $sql1 = "SELECT * 
						 FROM usuarios 
						 WHERE id_usuario = $idUsuario AND clave_usuario = SHA(CONCAT(login_usuario, '|', '" . $claveUsuario2 . "'))";
            $rta = $this->getUnDato($sql1);
            if (count($rta) >= 1) {
                $sql = "UPDATE usuarios 
							SET clave_usuario=SHA(CONCAT(login_usuario, '|', '" . $claveUsuario . "'))
							WHERE id_usuario=" . $idUsuario . "
							AND clave_usuario= SHA(CONCAT(login_usuario, '|', '" . $claveUsuario2 . "'))";
                $arrCampos[0] = "@id";
                $this->ejecutarSentencia($sql, $arrCampos);
                return 1;
            } else {
                return 2;
            }
        } catch (Exception $e) {
            
        }
    }

    /* Resetea la contraseña */

    public function resetearPass($idUsuario) {
        try {
            $sql = "CALL pa_resetear_contrasena_usuario(" . $idUsuario . ", @id)";

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $resultado_out = $arrResultado["@id"];

            return $resultado_out;
        } catch (Exception $e) {
            return array();
        }
    }

    public function getUsuarios() {
        try {
            $sql = "SELECT *, CONCAT(nombre_usuario, ' ', apellido_usuario) AS nombre_completo  FROM usuarios 
						ORDER BY nombre_usuario, apellido_usuario";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getNombreUsuariosBuscar($txt_buscar) {
        try {
            $sql = "SELECT *
						FROM usuarios u
						WHERE u.login_usuario LIKE '" . $txt_buscar . "' ";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getBuscarDocumento($txt_buscar, $id_usuario) {
        try {
            if ($id_usuario == '') {//Validar para crear usuario
                $sql = "SELECT *
							FROM usuarios u
							WHERE u.numero_documento LIKE '" . $txt_buscar . "'";
            } else if ($id_usuario != '') {//Validar para editar usuario
                $sql = "SELECT *
							FROM usuarios u
							WHERE u.numero_documento LIKE '" . $txt_buscar . "'
							AND id_usuario<>" . $id_usuario;
            }
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function crearUsuario($nombre_usuario, $apellido_usuario, $id_tipo_documento, $numero_documento, $id_usuario_firma, $id_tipo_num_reg, $num_reg_medico, $reg_firma, $login_usuario, $clave_usuario, $ind_anonimo, $ind_autoriza, $perfiles_usuarios, $id_usuario_crea) {
        try {
            if ($id_usuario_firma == "") {
                $id_usuario_firma = "NULL";
            }
            if ($id_tipo_num_reg == "") {
                $id_tipo_num_reg = "NULL";
            }
            if ($num_reg_medico != "") {
                $num_reg_medico = "'" . $num_reg_medico . "'";
            } else {
                $num_reg_medico = "NULL";
            }
            if ($reg_firma != "") {
                $reg_firma = "'" . $reg_firma . "'";
            } else {
                $reg_firma = "NULL";
            }
            $sql = "CALL pa_crear_usuario('" . $nombre_usuario . "', '" . $apellido_usuario . "', " . $id_tipo_documento . ", '" . $numero_documento . "', " . $id_usuario_firma . ", " .
                    $id_tipo_num_reg . ", " . $num_reg_medico . ", " . $reg_firma . ", '" . $login_usuario . "', '" . $clave_usuario . "', " . $ind_anonimo . ", " . $ind_autoriza . ", " . $id_usuario_crea . ", @id)";

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $id_usuario_creado = $arrResultado["@id"];

            if ($id_usuario_creado > 0) {
                $array_perfiles_usuarios = explode(",", $perfiles_usuarios);
                foreach ($array_perfiles_usuarios as $fila_perfiles) {
                    $sql = "INSERT INTO usuarios_perfiles 
								(id_usuario, id_perfil, id_usuario_crea, fecha_crea)
								VALUES (" . $id_usuario_creado . ", " . $fila_perfiles . ", " . $id_usuario_crea . ", NOW())";

                    $arrCampos[0] = "@id";
                    $this->ejecutarSentencia($sql, $arrCampos);
                }
            }
            return $id_usuario_creado;
        } catch (Exception $e) {
            return "-2";
        }
    }

    public function editarUsuario($id_usuario, $nombre_usuario, $apellido_usuario, $id_tipo_documento, $numero_documento, $id_usuario_firma, $id_tipo_num_reg, $num_reg_medico, $reg_firma, $ind_anonimo, $ind_activo, $perfiles_usuarios, $id_usuario_crea, $ind_autoriza) {
        try {
            $array_perfiles_usuarios = explode(",", $perfiles_usuarios);
            $sql = "DELETE FROM temporal_hceo
						WHERE id_usuario = " . $id_usuario;

            $arrCampos[0] = "@id";
            $this->ejecutarSentencia($sql, $arrCampos);

            foreach ($array_perfiles_usuarios as $fila_perfiles) {
                $sql = "INSERT INTO temporal_hceo
							(id_usuario, valor)
							VALUES (" . $id_usuario . ", " . $fila_perfiles . ")";

                $arrCampos[0] = "@id";
                $this->ejecutarSentencia($sql, $arrCampos);
            }

            if ($id_usuario_firma == "") {
                $id_usuario_firma = "NULL";
            }
            if ($id_tipo_num_reg == "") {
                $id_tipo_num_reg = "NULL";
            }
            if ($num_reg_medico != "") {
                $num_reg_medico = "'" . $num_reg_medico . "'";
            } else {
                $num_reg_medico = "NULL";
            }
            if ($reg_firma != "") {
                $reg_firma = "'" . $reg_firma . "'";
            } else {
                $reg_firma = "NULL";
            }

            $sql = "CALL pa_editar_usuario(" . $id_usuario . ", '" . $nombre_usuario . "', '" . $apellido_usuario . "', " . $id_tipo_documento . ", '" . $numero_documento . "', " .
                    $id_usuario_firma . ", " . $id_tipo_num_reg . ", " . $num_reg_medico . ", " . $reg_firma . ", " . $ind_anonimo . ", " . $ind_activo . ", " . $ind_autoriza . ", " . $id_usuario_crea . ", @id)";

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $id_resultado = $arrResultado["@id"];

            return $id_resultado;
        } catch (Exception $e) {
            return -2;
        }
    }

    public function actualizarRutaImagenFirma($id_usuario, $reg_firma) {
        try {
            $sql = "UPDATE usuarios 
						SET reg_firma='" . $reg_firma . "'
						WHERE id_usuario=" . $id_usuario;

            $arrCampos[0] = "@id";
            $this->ejecutarSentencia($sql, $arrCampos);
            return 1;
        } catch (Exception $e) {
            return -1;
        }
    }

    //Funcion que retorna los usuario profesionales en el combo box del formulario estado_atencion.php
    public function getListaUsuariosEstadoAtencion() {
        try {
            $sql = "SELECT U.*, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_completo
						FROM usuarios U
						INNER JOIN admisiones A ON A.id_usuario_prof=U.id_usuario
						WHERE DATE_FORMAT(A.fecha_admision,'%Y-%m-%d')=DATE_FORMAT(NOW(),'%Y-%m-%d') AND 
						EXISTS (SELECT id_usuario_prof FROM admisiones)
						GROUP BY U.nombre_usuario";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaUsuariosTipoCitaDet($id_tipo_cita, $id_tipo_reg, $id_usuario_prof) {
        try {
            $sql = "/*Verifica que el usuario tenga permisos*/
						SELECT U.*, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_completo, 0 AS id_lugar_disp, '' AS lugar_disp
						FROM tipos_citas_det CD
						INNER JOIN tipos_registros_hc TR ON CD.id_tipo_reg=TR.id_tipo_reg
						INNER JOIN permisos P ON TR.id_menu=P.id_menu
						INNER JOIN usuarios_perfiles UP ON P.id_perfil=UP.id_perfil
						INNER JOIN usuarios U ON UP.id_usuario=U.id_usuario
						WHERE CD.id_tipo_cita=" . $id_tipo_cita . "
						AND CD.id_tipo_reg=" . $id_tipo_reg . "
						AND U.id_usuario=" . $id_usuario_prof . "
						AND CD.ind_usuario_alt=0
						AND P.tipo_acceso=2
						AND U.ind_activo=1
						
						UNION
						
						/*Obtiene los usuarios disponibles*/
						SELECT U.*, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_completo, D.id_lugar_disp, D.lugar_disp
						FROM tipos_citas_det CD
						INNER JOIN tipos_registros_hc TR ON CD.id_tipo_reg=TR.id_tipo_reg
						INNER JOIN permisos P ON TR.id_menu=P.id_menu
						INNER JOIN usuarios_perfiles UP ON P.id_perfil=UP.id_perfil
						INNER JOIN usuarios U ON UP.id_usuario=U.id_usuario
						INNER JOIN (
							SELECT D.id_usuario, D.id_lugar_disp, LD.nombre_detalle AS lugar_disp
							FROM disponibilidad_prof D
							INNER JOIN listas_detalle LD ON D.id_lugar_disp=LD.id_detalle
							WHERE DATE(D.fecha_cal)=DATE(NOW())
							AND D.id_tipo_disponibilidad=11
							UNION
							SELECT D.id_usuario, D.id_lugar_disp, LD.nombre_detalle AS lugar_disp
							FROM disponibilidad_prof D
							INNER JOIN disponibilidad_prof_det DD ON D.id_disponibilidad=DD.id_disponibilidad
							INNER JOIN listas_detalle LD ON D.id_lugar_disp=LD.id_detalle
							WHERE DATE(D.fecha_cal)=DATE(NOW())
							AND D.id_tipo_disponibilidad=12
							AND NOW() BETWEEN DD.hora_ini AND DD.hora_final
						) D ON UP.id_usuario=D.id_usuario
						WHERE CD.id_tipo_cita=" . $id_tipo_cita . "
						AND CD.id_tipo_reg=" . $id_tipo_reg . "
						AND U.id_usuario<>" . $id_usuario_prof . "
						AND P.tipo_acceso=2
						AND U.ind_activo=1
						
						ORDER BY id_lugar_disp, nombre_usuario, apellido_usuario";
            //echo($sql);

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaUsuariosCirugia($ind_cirugia) {
        try {
            $sql = "SELECT DISTINCT U.*, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_completo
						FROM usuarios U
						INNER JOIN usuarios_perfiles UP ON U.id_usuario=UP.id_usuario
						INNER JOIN perfiles P ON UP.id_perfil=P.id_perfil
						WHERE U.ind_activo=1
						AND P.ind_cirugia=" . $ind_cirugia . "
						ORDER BY U.nombre_usuario, U.apellido_usuario";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /* Función que retorna los usuarios que tienen permisos sobre un listado de menús */

    public function getListaUsuariosAcceso($arr_menus, $tipo_acceso) {
        try {
            $cadena_menus = "";
            foreach ($arr_menus as $id_menu_aux) {
                if ($cadena_menus != "") {
                    $cadena_menus .= ", ";
                }
                $cadena_menus .= $id_menu_aux;
            }

            $sql = "SELECT DISTINCT U.*, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_completo
						FROM usuarios U
						INNER JOIN usuarios_perfiles UP ON U.id_usuario=UP.id_usuario
						INNER JOIN permisos P ON UP.id_perfil=P.id_perfil
						WHERE U.ind_activo=1
						AND P.id_menu IN (" . $cadena_menus . ")
						AND P.tipo_acceso=" . $tipo_acceso . "
						ORDER BY U.nombre_usuario, U.apellido_usuario";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    //Funcion que retorna los usuario profesionales que pueden firmar historias clínicas
    public function getListaUsuariosFirma() {
        try {
            $sql = "SELECT DISTINCT U.*, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_completo
						FROM usuarios U
						INNER JOIN usuarios_perfiles UP ON U.id_usuario=UP.id_usuario
						INNER JOIN perfiles P ON UP.id_perfil=P.id_perfil
						WHERE U.ind_activo=1
						AND U.id_usuario_firma IS NULL
						AND P.ind_activo=1
						AND P.ind_atiende=1
						ORDER BY U.nombre_usuario, U.apellido_usuario";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Obtener el usuario optómetra que atendió en una admisión
     */
    public function getUsuarioOptometra($id_admision) {
        try {
            $sql = "SELECT U.*, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_completo
						FROM admisiones_estados_atencion AE
						INNER JOIN usuarios U ON U.id_usuario=AE.id_usuario_prof
						WHERE AE.id_admision=" . $id_admision . "
						AND AE.id_estado_atencion=4";

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getUsuarioEstadoAtencion($id_admision, $id_estado_atencion) {
        try {
            $sql = "SELECT U.*, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_completo
						FROM admisiones_estados_atencion AE
						INNER JOIN usuarios U ON AE.id_usuario_prof=U.id_usuario
						WHERE AE.id_admision=" . $id_admision . "
						AND AE.id_estado_atencion=" . $id_estado_atencion;

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaUsuariosMenuDisponibilidad($id_menu, $id_usuario_adicional = "", $id_lugar_adicional) {
        try {
            $sql = "SELECT U.*, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_completo
						FROM permisos PE
						INNER JOIN usuarios_perfiles UP ON PE.id_perfil=UP.id_perfil
						INNER JOIN (
							SELECT U.*, D.id_lugar_disp
							FROM disponibilidad_prof D
							INNER JOIN usuarios U ON D.id_usuario=U.id_usuario
							WHERE D.fecha_cal=DATE(NOW())
							AND D.id_tipo_disponibilidad=11
							
							UNION
							
							SELECT U.*, DD.id_lugar_disp
							FROM disponibilidad_prof D
							INNER JOIN usuarios U ON D.id_usuario=U.id_usuario
							INNER JOIN disponibilidad_prof_det DD ON D.id_disponibilidad=DD.id_disponibilidad
							WHERE D.fecha_cal=DATE(NOW())
							AND D.id_tipo_disponibilidad=12
							AND NOW() BETWEEN DD.hora_ini AND DD.hora_final
						) U ON UP.id_usuario=U.id_usuario
						WHERE PE.id_menu=" . $id_menu . "
						AND PE.tipo_acceso=2
						AND U.ind_activo=1 ";

            if ($id_usuario_adicional != "") {
                $sql .= "UNION
							SELECT *, '" . $id_lugar_adicional . "', CONCAT(nombre_usuario, ' ', apellido_usuario) AS nombre_completo
							FROM usuarios
							WHERE id_usuario=" . $id_usuario_adicional . " ";
            }

            $sql .= "ORDER BY nombre_usuario, apellido_usuario";
            //echo($sql);

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaUsuariosDisponibles($id_usuario_prof = 0) {
        try {
            $sql = "/*Usuario actual*/
						SELECT U.*, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_completo, 0 AS id_lugar_disp, '' AS lugar_disp
						FROM usuarios U
						WHERE U.id_usuario=" . $id_usuario_prof . "
						AND U.ind_activo=1
						
						UNION
						
						/*Obtiene los usuarios disponibles*/
						SELECT U.*, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_completo, D.id_lugar_disp, D.lugar_disp
						FROM usuarios U
						INNER JOIN (
							SELECT D.id_usuario, D.id_lugar_disp, LD.nombre_detalle AS lugar_disp
							FROM disponibilidad_prof D
							INNER JOIN listas_detalle LD ON D.id_lugar_disp=LD.id_detalle
							WHERE DATE(D.fecha_cal)=DATE(NOW())
							AND D.id_tipo_disponibilidad=11
							UNION
							SELECT D.id_usuario, D.id_lugar_disp, LD.nombre_detalle AS lugar_disp
							FROM disponibilidad_prof D
							INNER JOIN disponibilidad_prof_det DD ON D.id_disponibilidad=DD.id_disponibilidad
							INNER JOIN listas_detalle LD ON D.id_lugar_disp=LD.id_detalle
							WHERE DATE(D.fecha_cal)=DATE(NOW())
							AND D.id_tipo_disponibilidad=12
							AND NOW() BETWEEN DD.hora_ini AND DD.hora_final
						) D ON U.id_usuario=D.id_usuario
						WHERE U.id_usuario<>" . $id_usuario_prof . "
						AND U.ind_activo=1
						AND U.ind_anonimo=0
						
						ORDER BY id_lugar_disp, nombre_usuario, apellido_usuario";
            //echo($sql);

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /* Función que retorna los usuarios que tienen permisos para registrar pagos */

    public function getListaUsuariosRegistroPagos($ind_activo) {
        try {
            $sql = "SELECT DISTINCT U.*, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_completo
						FROM perfiles P
						INNER JOIN usuarios_perfiles UP ON P.id_perfil=UP.id_perfil
						INNER JOIN usuarios U ON UP.id_usuario=U.id_usuario
						WHERE P.ind_registrar_pagos=1 ";
            if ($ind_activo != "") {
                $sql .= "AND U.ind_activo=" . $ind_activo . " ";
            }
            $sql .= "ORDER BY U.nombre_usuario, U.apellido_usuario";
            //echo($sql);

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaUsuariosAdmisionPerfil($id_admision, $id_perfil) {
        try {
            $sql = "SELECT U.*
						FROM historia_clinica HC
						INNER JOIN usuarios_perfiles UP ON HC.id_usuario_crea=UP.id_usuario
						INNER JOIN usuarios U ON UP.id_usuario=U.id_usuario
						WHERE HC.id_admision=" . $id_admision . "
						AND UP.id_perfil=" . $id_perfil . "
						ORDER BY U.id_usuario";
            //echo($sql);

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaUsuariosAutoriza($ind_autoriza, $ind_activo) {
        try {
            $sql = "SELECT *, CONCAT(nombre_usuario, ' ', apellido_usuario) AS nombre_completo
						FROM usuarios
						WHERE ind_autoriza=" . $ind_autoriza . " ";
            if ($ind_activo != "") {
                $sql .= "AND ind_activo=" . $ind_activo . " ";
            }
            $sql .= "ORDER BY nombre_usuario, apellido_usuario";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

}

?>
