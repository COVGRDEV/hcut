<?php
session_start();
/*
  Pagina listado de usuarios, muestra los usuarios existentes, para modificar o crear uno nuevo
  Autor: Helio Ruber López - 16/09/2013
 */

require_once("../db/DbUsuarios.php");
require_once("../db/DbListas.php");
require_once("../db/DbPerfiles.php");
require_once("../funciones/Utilidades.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../principal/ContenidoHtml.php");

$dbUsuarios = new DbUsuarios();
$dbListas = new DbListas();
$dbPefiles = new DbPerfiles();
$utilidades = new Utilidades();
$contenido = new ContenidoHtml();
//$contenido->validar_seguridad(1);

if (isset($_POST["hdd_numero_menu"])) {
    $tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);
}

$opcion = $_POST["opcion"];
if ($opcion != "4" && $opcion != "6") {
    header("Content-Type: text/xml; charset=UTF-8");
}

switch ($opcion) {
    case "1": //Opcion para buscar usuarios
        $txt_busca_usuario = urldecode($_POST["txt_busca_usuario"]);
        $tabla_busca_persona = $dbUsuarios->getListaUsuariosBuscar($txt_busca_usuario);
        ?>
        <br />
        <table class="paginated modal_table" style="width: 65%; margin: auto;">
            <thead>
                <tr><th colspan="5">Usuarios del Sistema</th></tr>
                <tr>
                    <th style="width:5%;">Id</th>
                    <th style="width:36%;">Nombre</th>
                    <th style="width:20%;">Usuario</th>
                    <th style="width:24%;">Perfil</th>
                    <th style="width:15%;">Estado</th>
                </tr>
            </thead>
            <?php
            $cantidad_registro = count($tabla_busca_persona);
            $i = 1;
            if ($cantidad_registro > 0) {
                foreach ($tabla_busca_persona as $fila_usuario) {
                    @$id_usuario = $fila_usuario["id_usuario"];
                    @$nombre_usuario = $fila_usuario["nombre_usuario"] . " " . $fila_usuario["apellido_usuario"];
                    @$usuario = $fila_usuario["login_usuario"];
                    @$estado = $fila_usuario["ind_activo"];
                    if ($estado == 1) {
                        $estado = "Activo";
                        $class_estado = "activo";
                    } else if ($estado == 0) {
                        $estado = "No Activo";
                        $class_estado = "inactivo";
                    }
                    $tabla_perfil_persona = $dbUsuarios->getListaPerfilUsuarios($id_usuario);
                    $cantidad_perfiles = count($tabla_perfil_persona);
                    $perfiles_usuarios = "";
                    $contador = 1;
                    foreach ($tabla_perfil_persona as $fila_perfil) {
                        if ($contador == $cantidad_perfiles) {
                            $perfiles_usuarios = $perfiles_usuarios . $fila_perfil["nombre_perfil"];
                        } else {
                            $perfiles_usuarios = $perfiles_usuarios . $fila_perfil["nombre_perfil"] . ", ";
                        }
                        $contador = $contador + 1;
                    }
                    ?>
                    <tr style="cursor:pointer;" onclick="seleccionar_usuarios('<?php echo $id_usuario; ?>');">
                        <td align="center"><?php echo $id_usuario; ?></td>
                        <td align="left"><?php echo $nombre_usuario; ?></td>
                        <td align="left"><?php echo $usuario; ?></td>
                        <td align="left"><?php echo $perfiles_usuarios; ?></td>
                        <td align="left"><span class="<?php echo $class_estado; ?>"><?php echo $estado; ?></span></td>

                    </tr>
                    <?php
                    $i = $i + 1;
                }
            } else {
                ?>
                <tr>
                    <td align="center" colspan="5">No se encontraron datos</td>
                </tr>
                <?php
            }
            ?>
        </table>
        <script id="ajax">
            //<![CDATA[ 
            $(function () {
                $(".paginated", "table").each(function (i) {
                    $(this).text(i + 1);
                });

                $("table.paginated").each(function () {
                    var currentPage = 0;
                    var numPerPage = 10;
                    var $table = $(this);
                    $table.bind("repaginate", function () {
                        $table.find("tbody tr").hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
                    });
                    $table.trigger("repaginate");
                    var numRows = $table.find("tbody tr").length;
                    var numPages = Math.ceil(numRows / numPerPage);
                    var $pager = $('<div class="pager"></div>');
                    for (var page = 0; page < numPages; page++) {
                        $('<span class="page-number"></span>').text(page + 1).bind("click", {
                            newPage: page
                        }, function (event) {
                            currentPage = event.data["newPage"];
                            $table.trigger("repaginate");
                            $(this).addClass("active").siblings().removeClass("active");
                        }).appendTo($pager).addClass("clickable");
                    }
                    $pager.insertBefore($table).find("span.page-number:first").addClass("active");
                });
            });
            //]]>
        </script>
        <br />
        <?php
        break;

    case "2": //Opcion para crear el formulario de crear usuarios
        $combo = new Combo_Box();
        $tipo_accion = "";
        if (isset($_POST["id_usuario"])) {
            $tabla_usuario = $dbUsuarios->getUsuario($_POST["id_usuario"]);
            $id_usuario = $_POST["id_usuario"];
            $tabla_perfiles_usuario = $dbUsuarios->getListaPerfilUsuarios($_POST["id_usuario"]);
            $txt_nombre_usuario = $tabla_usuario["nombre_usuario"];
            $txt_apellido_usuario = $tabla_usuario["apellido_usuario"];
            $cmb_tipo_documento = $tabla_usuario["id_tipo_documento"];
            $txt_numero_documento = $tabla_usuario["numero_documento"];
            $id_usuario_firma = $tabla_usuario["id_usuario_firma"];
            $cmb_tipo_num_reg = $tabla_usuario["id_tipo_num_reg"];
            $txt_num_reg_medico = $tabla_usuario["num_reg_medico"];
            $ind_autoriza = $tabla_usuario["ind_autoriza"];
            $txt_reg_firma = $tabla_usuario["reg_firma"];
            $ind_anonimo = $tabla_usuario["ind_anonimo"];
            $estado_usuario = $tabla_usuario["ind_activo"];
            $tipo_accion = 2; //Editar usuario
            $titulo_formulario = "Editar usuario";
            $nombre_de_usuario = $tabla_usuario["login_usuario"];
        } else {
            $id_usuario = "";
            $tabla_perfiles_usuario = array();
            $txt_nombre_usuario = "";
            $txt_apellido_usuario = "";
            $cmb_tipo_documento = "";
            $txt_numero_documento = "";
            $id_usuario_firma = "";
            $cmb_tipo_num_reg = "";
            $ind_autoriza = 0;
            $txt_num_reg_medico = "";
            $txt_reg_firma = "";
            $ind_anonimo = 0;
            $txt_usuario = "";
            $txt_clave = "";
            $tipo_accion = 1; //Crear usuario
            $titulo_formulario = "Crear nuevo usuario";
        }
        ?>
        <div id="div_usuario_existe"><input type="hidden" value="true" name="hdd_usuario_existe" id="hdd_usuario_existe" /></div>
        <div id="div_documento_existe"><input type="hidden" value="true" name="hdd_documento_existe" id="hdd_documento_existe" /></div>
        <input type="hidden" value="<?php echo $id_usuario; ?>" name="hdd_id_usuario" id="hdd_id_usuario" />
        <input type="hidden" value="0" name="hdd_exito" id="hdd_exito" />
        <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:52em;">
            <tr><th colspan="2" align="center"><h3><?php echo $titulo_formulario; ?></h3></th></tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr valign="top">
                <td align="right">
                    <label class="inline" for="txt_nombre_usuario">Nombres del usuario*</label>	
                </td>
                <td align="left">
                    <input type="text" class="input" value="<?php echo $txt_nombre_usuario; ?>" name="txt_nombre_usuario" id="txt_nombre_usuario" maxlength="30" size="20" onblur="trim_cadena(this);" />
                </td>
            </tr>
            <tr valign="top">
                <td align="right">
                    <label class="inline" for="txt_apellido_usuario">Apellidos del usuario*</label>	
                </td>
                <td align="left">
                    <input type="text" class="input" value="<?php echo $txt_apellido_usuario; ?>" name="txt_apellido_usuario" id="txt_apellido_usuario" maxlength="30" size="20" onblur="trim_cadena(this);" />
                </td>
            </tr>
            <tr valign="top">
                <td align="right">
                    <label class="inline" for="cmb_tipo_documento">Tipo de documento*</label>	
                </td>
                <td align="left">
                    <?php
                    $lista_tipo_documento = $dbListas->getListaDetalles(2);
                    $combo->getComboDb("cmb_tipo_documento", $cmb_tipo_documento, $lista_tipo_documento, "id_detalle, nombre_detalle", "--Seleccione--", "", "", "width:350px;", "", "");
                    ?>
                </td>	
            </tr>
            <tr valign="top">
                <td align="right">
                    <label class="inline" for="txt_numero_documento">N&uacute;mero de documento*</label>	
                </td>
                <td align="left">
                    <input type="text" class="input" value="<?php echo $txt_numero_documento; ?>" name="txt_numero_documento" id="txt_numero_documento" maxlength="20" size="20"  onblur="trim_cadena(this);
                                    validar_documento_existente(this, '<?php echo $tipo_accion; ?>', '<?php echo $id_usuario; ?>');
                                    quitar_espacios(this);"/>
                </td>
            </tr>
            <tr valign="top">
                <td align="right">
                    <label class="inline" for="cmb_tipo_num_reg">Tipo de registro</label>	
                </td>
                <td align="left">
                    <?php
                    $lista_tipos_num_reg = $dbListas->getListaDetalles(45);
                    $combo->getComboDb("cmb_tipo_num_reg", $cmb_tipo_num_reg, $lista_tipos_num_reg, "id_detalle, nombre_detalle", "--Seleccione--", "", "", "width:250px;", "", "");
                    ?>
                </td>
            </tr>
            <tr valign="top">
                <td align="right">
                    <label class="inline" for="txt_numero_documento">N&uacute;mero de registro</label>	
                </td>
                <td align="left">
                    <input type="text" class="input" value="<?php echo $txt_num_reg_medico; ?>" name="txt_num_reg_medico" id="txt_num_reg_medico" maxlength="20" size="20" onblur="trim_cadena(this);" />
                </td>
            </tr>
            <?php
            if ($ind_autoriza == 1) {
                $checked_autoriza = "checked";
            } else {
                $checked_autoriza = "";
            }
            ?>
            <tr valign="top">
                <td align="right">
                    <label class="inline no-margin">Autoriza cambios en pagos</label>	
                </td>
                <td align="left">
                    <label class="inline no-margin" >
                        <input type="checkbox" name="check_autoriza" id="check_autoriza" class="no-margin" <?php echo($checked_autoriza); ?> />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <td align="right">
                    <label class="inline" for="cmb_usuario_firma">Usuario que firma</label>	
                </td>
                <td align="left">
                    <?php
                    //Se carga el listado de usuarios autorizados a firmar
                    $lista_usuarios_firma = $dbUsuarios->getListaUsuariosFirma();
                    ?>
                    <select id="cmb_usuario_firma" style="width:250px;">
                        <option value="">Usuario actual</option>
                        <?php
                        foreach ($lista_usuarios_firma as $usuario_aux) {
                            $selected_aux = "";
                            if ($usuario_aux["id_usuario"] == $id_usuario_firma) {
                                $selected_aux = " selected";
                            }
                            ?>
                            <option value="<?php echo($usuario_aux["id_usuario"]); ?>"<?php echo($selected_aux); ?>><?php echo($usuario_aux["nombre_completo"]); ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <td align="right">
                    <label for="txt_numero_documento">Imagen de la firma</label>	
                </td>
                <td align="left">
                    <form name="frm_registro_usuarios" id="frm_registro_usuarios" target="ifr_registro_usuarios" action="usuarios_ajax.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="opcion" id="opcion" value="" />
                        <input type="hidden" name="hdd_id_usuario_edit" id="hdd_id_usuario_edit" value="" />
                        <input type="hidden" name="hdd_nombre_usuario" id="hdd_nombre_usuario" value="" />
                        <input type="hidden" name="hdd_apellido_usuario" id="hdd_apellido_usuario" value="" />
                        <input type="hidden" name="hdd_tipo_documento" id="hdd_tipo_documento" value="" />
                        <input type="hidden" name="hdd_numero_documento" id="hdd_numero_documento" value="" />
                        <input type="hidden" name="hdd_id_usuario_firma" id="hdd_id_usuario_firma" value="" />
                        <input type="hidden" name="hdd_tipo_num_reg" id="hdd_tipo_num_reg" value="" />
                        <input type="hidden" name="hdd_num_reg_medico" id="hdd_num_reg_medico" value="" />
                        <input type="hidden" name="hdd_ind_autoriza" id="hdd_ind_autoriza" value="" />
                        <input type="hidden" name="hdd_login_usuario" id="hdd_login_usuario" value="" />
                        <input type="hidden" name="hdd_clave_usuario" id="hdd_clave_usuario" value="" />
                        <input type="hidden" name="hdd_ind_anonimo" id="hdd_ind_anonimo" value="" />
                        <input type="hidden" name="hdd_lista_perfiles" id="hdd_lista_perfiles" value="" />
                        <input type="hidden" name="hdd_ind_estado" id="hdd_ind_estado" value="" />
                        <input type="file" name="fil_reg_firma" id="fil_reg_firma" />
                    </form>
                    <div style="display:none;">
                        <iframe name="ifr_registro_usuarios" id="ifr_registro_usuarios"></iframe>
                    </div>
                </td>
            </tr>
            <?php
            if ($txt_reg_firma != "") {
                ?>
                <tr>
                    <td></td>
                    <td align="left">
                        <img src="<?php echo($txt_reg_firma); ?>" style="max-width:250px; max-height:150px;" />
                    </td>
                </tr>
                <?php
            }

            if ($ind_anonimo == 1) {
                $checked_anonimo = "checked";
            } else {
                $checked_anonimo = "";
            }
            ?>
            <tr valign="top">
                <td align="right">
                    <label class="inline no-margin" for="txt_clave">Usuario an&oacute;nimo</label>	
                </td>
                <td align="left">
                    <label class="inline no-margin" >
                        <input type="checkbox" name="check_anonimo" id="check_anonimo" class="no-margin" <?php echo $checked_anonimo; ?> />
                    </label>
                </td>
            </tr>
            <?php
            if ($tipo_accion == 1) { //Solo se muestra si se va a crear un nuevo usuario
                ?>
                <tr valign="top">
                    <td align="right">
                        <label class="inline" for="txt_usuario">Usuario*</label>	
                    </td>
                    <td align="left">
                        <input type="text" class="input" value="<?php echo $txt_nombre_usuario; ?>" name="txt_usuario" id="txt_usuario" maxlength="20" size="20"  onblur="convertirAMinusculas(this);
                                            trim_cadena(this);
                                            validar_usuario_existente(this);
                                            quitar_espacios(this);"/>
                    </td>
                </tr>
                <tr valign="top">
                    <td align="right">
                        <label class="inline" for="txt_clave">Contrase&ntilde;a*</label>	
                    </td>
                    <td align="left">
                        <input type="password" class="input" value="" name="txt_clave" id="txt_clave" maxlength="50" size="20" onblur="convertirAMinusculas(this);
                                            trim_cadena(this);" />
                    </td>
                </tr>
                <?php
            } else if ($tipo_accion == 2) {//Solo se muestra si se va a editar un nuevo usuario
                if ($estado_usuario == 1) {
                    $checked_estado = "checked";
                } else {
                    $checked_estado = "";
                }
                ?>
                <tr valign="top">
                    <td align="right">
                        <label class="inline no-margin" for="txt_clave">Usuario activo</label>	
                    </td>
                    <td align="left">
                        <label class="inline no-margin">
                            <input type="checkbox" name="check_estado" id="check_estado" class="no-margin" <?php echo $checked_estado; ?> />
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <td align="center" colspan="2" >
                        <fieldset>
                            <legend>Usuario del sistema</legend>
                            <table border="0" cellpadding="4" cellspacing="0" align="left" style="width: 100%;">
                                <tr>
                                    <td>
                                        <label class="inline">Usuario: <span style="font-weight: 800; font-style: italic;"><?php echo $nombre_de_usuario; ?></span></label>
                                    </td>
                                    <td>
                                        <input type="button" id="btnResetearPass" name="btnResetearPass" class="btnPrincipal" value="Reiniciar contrase&ntilde;a" onclick="resetear_pass('<?php echo $nombre_de_usuario; ?>');" />
                                        <input type="hidden" id="rtaResetearPass" name="rtaResetearPass" />
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                </tr>
                <?php
            }
            ?>
            <tr valign="top">
                <td align="center" colspan="2" >
                    <fieldset>
                        <legend>Perfiles</legend>
                        <table border="0" cellpadding="4" cellspacing="0" align="left" style="width: 100%;">
                            <?php
                            $tabla_pefiles = $dbPefiles->getListaPerfiles();
                            ?>
                            <tr>
                                <?php
                                $i = 1;
                                foreach ($tabla_pefiles as $fila_perfiles) {
                                    $id_perfil = $fila_perfiles["id_perfil"];
                                    $nombre_perfil = $fila_perfiles["nombre_perfil"];

                                    $checked = "";
                                    //Se recorre el array donde tien los perfiles encontrados
                                    if (count($tabla_perfiles_usuario) != 0) {
                                        foreach ($tabla_perfiles_usuario as $fila_perfiles_usuario) {
                                            $id_perfil_usuario = $fila_perfiles_usuario["id_perfil"];
                                            if ($id_perfil == $id_perfil_usuario) {
                                                $checked = "checked";
                                            }
                                        }
                                    }
                                    if ($i == 1) {
                                        ?>
                                    </tr><tr>
                                        <td style="text-align: left;" align="left">
                                            <input type="checkbox" name="check_pefiles" id="check_pefiles_<?php echo $id_perfil; ?>" value="<?php echo $id_perfil; ?>" <?php echo $checked; ?> /> <label for="check_pefiles_<?php echo $id_perfil; ?>" ><?php echo $nombre_perfil; ?></label>
                                        </td>
                                        <?php
                                        $i = 0;
                                    } else {
                                        ?>
                                        <td style="text-align: left;" align="left">
                                            <input type="checkbox" name="check_pefiles" id="check_pefiles_<?php echo $id_perfil; ?>" value="<?php echo $id_perfil; ?>" <?php echo $checked; ?> /> <label for="check_pefiles_<?php echo $id_perfil; ?>" ><?php echo $nombre_perfil; ?></label>
                                        </td>
                                        <?php
                                        $i = $i + 1;
                                    }
                                }
                                echo"</tr>";
                                ?>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <td colspan="2">
                    <?php
                    if ($tipo_accion == 1) {//Boton para crear usuario
                        if ($tipo_acceso_menu == 2) {
                            ?>
                            <input class="btnPrincipal" type="button" id="btn_crear" nombre="btn_crear" value="Crear" onclick="validar_crear_usuarios();"/>
                            <?php
                        }
                    } else if ($tipo_accion == 2) {//Boton para editar usuario
                        if ($tipo_acceso_menu == 2) {
                            ?>
                            <input class="btnPrincipal" type="button" id="btn_editar" nombre="btn_editar" value="Guardar" onclick="validar_editar_usuarios();"/>
                            <?php
                        }
                    }
                    ?>
                    <input class="btnPrincipal" type="button" id="btn_cancelar" nombre="btn_cancelar" value="Cancelar" onclick="volver_inicio();"/>

                </td>
            </tr>
        </table>
        <br />
        <?php
        break;

    case "3": //Opcion para validar usuario existente
        $txt_busca_usuario = urldecode($_POST["nombre_usuario"]);
        $tabla_busca_persona = $dbUsuarios->getNombreUsuariosBuscar($txt_busca_usuario);
        $cantidad = count($tabla_busca_persona);
        if ($cantidad >= 1) {
            ?>
            <input type="hidden" value="false" name="hdd_usuario_existe" id="hdd_usuario_existe" />
            <?php
        } else if ($cantidad == 0) {
            ?>
            <input type="hidden" value="true" name="hdd_usuario_existe" id="hdd_usuario_existe" />
            <?php
        }
        break;

    case "4": //Opcion para crear nuevo usuarios
        $id_usuario_crea = $_SESSION["idUsuario"];
        @$txt_nombre_usuario = urldecode($_POST["hdd_nombre_usuario"]);
        @$txt_apellido_usuario = urldecode($_POST["hdd_apellido_usuario"]);
        @$cmb_tipo_documento = $_POST["hdd_tipo_documento"];
        @$txt_numero_documento = $_POST["hdd_numero_documento"];
        @$id_usuario_firma = $_POST["hdd_id_usuario_firma"];
        @$cmb_tipo_num_reg = $_POST["hdd_tipo_num_reg"];
        @$txt_num_reg_medico = $_POST["hdd_num_reg_medico"];
        @$ind_autoriza = urldecode($_POST["hdd_ind_autoriza"]);
        @$txt_usuario = urldecode($_POST["hdd_login_usuario"]);
        @$txt_clave = urldecode($_POST["hdd_clave_usuario"]);
        @$ind_anonimo = urldecode($_POST["hdd_ind_anonimo"]);
        @$perfiles_usuarios = $_POST["hdd_lista_perfiles"];

        @$nombre_tmp = $_FILES["fil_reg_firma"]["tmp_name"];
        @$nombre_ori = $_FILES["fil_reg_firma"]["name"];

        $resultado = $dbUsuarios->crearUsuario($txt_nombre_usuario, $txt_apellido_usuario, $cmb_tipo_documento, $txt_numero_documento, $id_usuario_firma, $cmb_tipo_num_reg, $txt_num_reg_medico, "", $txt_usuario, $txt_clave, $ind_anonimo, $ind_autoriza, $perfiles_usuarios, $id_usuario_crea);

        $nombre_arch = "";
        if (trim($nombre_ori) && intval($resultado) > 0) {
            //Se construye el nombre que tendrá el archivo
            $nombre_arch = "../imagenes/firmas/" . $resultado . ".jpg";

            //Se copia el archivo
            copy($nombre_tmp, $nombre_arch);

            $dbUsuarios->actualizarRutaImagenFirma($resultado, $nombre_arch);
        }
        ?>
        <script id="ajax" type="text/javascript">
            window.parent.document.getElementById("hdd_exito").value = "<?php echo($resultado); ?>";
            window.parent.validar_exito();
        </script>
        <?php
        break;

    case "5": //Opcion para validar documento existente
        $txt_documento_usuario = $_POST["documento_usuario"];
        $tipo = $_POST["tipo"];
        $id_usuario = $_POST["id_usuario"];

        if ($tipo == 1) {
            $tabla_busca_documento = $dbUsuarios->getBuscarDocumento($txt_documento_usuario, $id_usuario);
        } else if ($tipo == 2) {
            $tabla_busca_documento = $dbUsuarios->getBuscarDocumento($txt_documento_usuario, $id_usuario);
        }
        $cantidad = count($tabla_busca_documento);
        if ($cantidad >= 1) {
            ?>
            <input type="hidden" value="false" name="hdd_documento_existe" id="hdd_documento_existe" />
            <?php
        } else if ($cantidad == 0) {
            ?>
            <input type="hidden" value="true" name="hdd_documento_existe" id="hdd_documento_existe" />
            <?php
        }
        break;

    case "6": //Opcion para editar nuevo usuarios
        $id_usuario_crea = $_SESSION["idUsuario"];
        @$hdd_id_usuario = $_POST["hdd_id_usuario_edit"];
        @$txt_nombre_usuario = urldecode($_POST["hdd_nombre_usuario"]);
        @$txt_apellido_usuario = urldecode($_POST["hdd_apellido_usuario"]);
        @$cmb_tipo_documento = $_POST["hdd_tipo_documento"];
        @$txt_numero_documento = $_POST["hdd_numero_documento"];
        @$id_usuario_firma = $_POST["hdd_id_usuario_firma"];
        @$cmb_tipo_num_reg = $_POST["hdd_tipo_num_reg"];
        @$txt_num_reg_medico = $_POST["hdd_num_reg_medico"];
        @$ind_autoriza = urldecode($_POST["hdd_ind_autoriza"]);
        @$ind_anonimo = urldecode($_POST["hdd_ind_anonimo"]);
        @$check_estado = $_POST["hdd_ind_estado"];
        @$perfiles_usuarios = $_POST["hdd_lista_perfiles"];

        @$nombre_tmp = $_FILES["fil_reg_firma"]["tmp_name"];
        @$nombre_ori = $_FILES["fil_reg_firma"]["name"];

        $nombre_arch = "";
        if (trim($nombre_ori)) {
            //Se construye el nombre que tendrá el archivo
            $nombre_arch = "../imagenes/firmas/" . $hdd_id_usuario . ".jpg";

            //Se copia el archivo
            copy($nombre_tmp, $nombre_arch);
        }

        $msg_resultado = $dbUsuarios->editarUsuario($hdd_id_usuario, $txt_nombre_usuario, $txt_apellido_usuario, $cmb_tipo_documento, $txt_numero_documento, $id_usuario_firma, $cmb_tipo_num_reg, $txt_num_reg_medico, $nombre_arch, $ind_anonimo, $check_estado, $perfiles_usuarios, $id_usuario_crea, $ind_autoriza);
        ?>
        <script id="ajax" type="text/javascript">
                    window.parent.document.getElementById("hdd_exito").value = "<?php echo($msg_resultado); ?>";
                    window.parent.cerrar_div_centro();
                    window.parent.validar_exito();
        </script>
        <?php
        break;

    case "7": //Resetea la contarseña del usuario
        $idUsuario = $_POST["id_usuario"];

        $rta_aux = $dbUsuarios->resetearPass($idUsuario);

        echo $rta_aux;

        break;
}
?>