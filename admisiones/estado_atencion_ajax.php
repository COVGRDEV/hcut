<?php
/*
  Página que permite los procedimiento ajax de estado
  Autor: Juan Pablo Gomez Quiroga - 01/10/2013
 */

session_start();

header("Content-Type: text/xml; charset=UTF-8");

require_once("../db/DbUsuarios.php");
require_once("../db/DbTiemposCitasProf.php");
require_once("../db/DbEstadosAtencion.php");
require_once("../db/DbCitas.php");
require_once("../db/DbTiposCitas.php");
require_once("../db/DbPacientes.php");
require_once("../db/DbPermisos.php");
require_once("../db/DbAdmision.php");
require_once("../db/DbMenus.php");
require_once("../db/DbAtencionPostqx.php");
require_once("../principal/ContenidoHtml.php");
require_once("../funciones/FuncionesPersona.php");
require_once("../funciones/Utilidades.php");

$utilidades = new Utilidades();
$contenido = new ContenidoHtml();
$contenido->validar_seguridad(1);
$tipo_acceso_menu = $contenido->obtener_permisos_menu($utilidades->str_decode($_POST["hdd_numero_menu"]));

$id_usuario = $_SESSION["idUsuario"];
$opcion = $utilidades->str_decode($_POST["opcion"]);

switch ($opcion) {
    case "1"://Crea el div Disponibilidad de especialistas
        @$id_usuario_sel = $utilidades->str_decode($_POST["id_usuario_sel"]);
        @$id_lugar_cita = $utilidades->str_decode($_POST["id_lugar_cita"]);

        //Se guarda el lugar de la cita en la sesión
        $_SESSION["id_lugar_cita"] = $id_lugar_cita;

        $dbCitas = new DbCitas();
        $estadosAtencion = new DbEstadosAtencion();
        $funcionesPersona = new FuncionesPersona();

        $listadoEstadosAtencion = $estadosAtencion->getListaEstadosAtencion(1, 1);

        $rta_aux = $dbCitas->getFecha();
        $fecha_aux = explode(":", $rta_aux["fecha"]);


        $dbUsuarios = new DbUsuarios();
        $tabla_perfiles = $dbUsuarios->getListaPerfilUsuarios($id_usuario);


        //Inicio Proceso para mostrar pacientes que necesitan atención en post-qx-catarata		
        $val_perfil = 0;
        foreach ($tabla_perfiles as $fila_perfiles) {
            $id_perfil_usuario = $fila_perfiles["id_perfil"];

            if ($id_perfil_usuario == 1 || $id_perfil_usuario == 20) {
                $val_perfil = 1;
                break;
            }
        }

        if ($val_perfil == 1) {
            $dbPostQx = new DbAtencionPostqx();

            $tabla_verificar = $dbPostQx->getVerificaRespuestasPacientes();
            $ban_mensaje = 0;
            foreach ($tabla_verificar as $fila_verificar) {
                $nombre_paciente_verifica = $fila_verificar["nombre_paciente"];
                $valor_verifica = $fila_verificar["valor_resultado"];
                if ($valor_verifica == 0) {
                    $ban_mensaje = 1;
                    break;
                }
            }

            if ($ban_mensaje == 1) {
                ?>
                <div class="div_mensaje_citas">					
                    <a href="#" style="color: #FF0000" onclick="enviar_credencial('../historia_clinica/atencion_postqx.php', 60)">
                        En seguimiento Post-Quir&uacute;rgico de Catarata, uno o m&aacute;s pacientes necesitan atenci&oacute;n	
                    </a>
                </div>
                <?php
            }
            //Fin Proceso para mostrar pacientes que necesitan atención en post-qx-catarata
        }
        ?>
        <input type="hidden" id="fecha_actual" name="fecha_actual" value="<?php echo $funcionesPersona->obtenerFecha3($fecha_aux[2], $fecha_aux[1], $fecha_aux[0]); ?>" />
        <table style="width: 100%; text-align: left;">
            <?php
            $contadortabla = 0;
            $contador = 3;
            $contadorA_aux = 0;
            $onClic = "";
            $dbPermisos = new DbPermisos();
            $dbAdmision = new DbAdmision();
            $dbMenus = new DbMenus();
            $num_filas = ceil(count($listadoEstadosAtencion) / 3);

            for ($i = 1; $i <= $num_filas; $i++) {
                ?>
                <tr>
                    <?php
                    for ($a = 1; $a <= $contador; $a++) {
                        $contadorA_aux++;
                        $contadortabla++;

                        if (strlen($id_usuario_sel) >= 1 || strlen($id_lugar_cita) >= 1) {
                            $citas_aux = $dbCitas->getEstadoadmitidos($id_usuario_sel, $id_lugar_cita);
                        } else {
                            $citas_aux = $dbCitas->getEstadoadmitidos("");
                        }

                        if (isset($listadoEstadosAtencion[($contadorA_aux - 1)])) {
                            $estado_atencion = $listadoEstadosAtencion[($contadorA_aux - 1)];

                            //Se carga la página de destino
                            $id_menu_orig = trim($estado_atencion["id_menu"]);

                            $num_columnas_aux = 1;
                            if ($estado_atencion["id_estado_atencion"] == 9) {
                                $num_columnas_aux = 3;
                            }
                            ?>
                            <td class="estado_atencion_grid" colspan="<?php echo($num_columnas_aux); ?>">
                                <div style="padding: 0px 5px 5px 0px;">
                                    <div class="estado volumen">
                                        <div class="encabezado">
                                            <h6><?php echo($estado_atencion["nombre_estado"] . "&nbsp;"); ?><span id="sp_cantidad_ea_<?php echo($estado_atencion["id_estado_atencion"]); ?>"></span></h6>
                                            <?php
                                            //if ($estado_atencion["id_estado_atencion"] != 9) {
                                            ?>
                                            <a href="#" class="btn-expandir ir" onclick="expandir_listado('<?php echo($estado_atencion["nombre_estado"]); ?>', '<?php echo("tabla" . $contadortabla); ?>')">Expandir listado</a>
                                            <?php
                                            //}
                                            ?>
                                        </div>
                                        <div id="d_estado_<?php echo($estado_atencion["id_estado_atencion"]); ?>" style="height:245px; overflow:auto;"></div>
                                    </div>
                                </div>
                            </td>
                            <?php
                        }
                    }
                    ?>
                </tr>      
                <?php
            }
            ?>
        </table>
        <div id="d_estados_nv" style="display:none;"></div>
        <script id="ajax" type="text/javascript">
            cargar_estados_atencion();
            g_id_intervalo_estados = setInterval(
                    function () {
                        cargar_estados_atencion();
                    }, 30000);
        </script>
        <?php
        break;

    case "2":
        $combo = new Combo_Box();
        $listas = new DbListas();
        $tiposCitas = new DbTiposCitas();
        $pacientes = new DbPacientes();
        $funcionesPersona = new FuncionesPersona();
        $dbCitas = new DbCitas();

        $id_consulta = $utilidades->str_decode($_POST["idconsulta"]);

        $paciente_aux = $pacientes->getExistepaciente($id_consulta);
        $cita_aux = $dbCitas->getCita($id_consulta);

        //Decara el valor de los input
        $tipo_identificacion_aux = "";
        $identificacion_aux = "";
        $nombres_aux = "";
        $apellidos_aux = "";
        $fecha_nacimiento_aux = "";
        $direccion_aux = "";
        $telefono_aux = "";

        foreach ($paciente_aux as $value) {//Si la consulta trae valores
            if ($value["resultado"] == 1) {
                $tipo_identificacion_aux = $value["id_tipo_documento"];
                $identificacion_aux = $value["numero_documento"];
                $nombres_aux = $funcionesPersona->obtenerNombreCompleto($value["nombre_1"], $value["nombre_2"], null, null);
                $apellidos_aux = $funcionesPersona->obtenerNombreCompleto($value["apellido_1"], $value["apellido_2"], null, null);
                $fecha_nacimiento_aux = $value["fecha_nacimiento_aux"];
                $direccion_aux = strlen($value["direccion"]) == 0 ? "null" : $value["direccion"];
                $telefono_aux = $value["telefono_1"];
            }
        }
        ?>
        <h2>Admisi&oacute;n</h2>
        <div style="text-align: left;">
            <div class="contenedor_advertencia" id="contenedor_advertencia"></div>
            <div class="contenedor_exito" id="contenedor_exito"></div>
            <form id="frmAdmision">
                <table style="width: 97%;">
                    <tr>
                        <td style="width: 50%;">
                            <fieldset style="height: 312px;">
                                <legend>Datos del paciente:</legend>

                                <table>
                                    <tr>
                                        <td style="width: 45%;text-align: right;">
                                            Tipo de Identificacion:
                                        </td>
                                        <td>
                                            <?php $combo->getComboDb("cmb_tipo_id", $tipo_identificacion_aux, $listas->getTipodocumento(), "id_detalle, nombre_detalle", "Seleccione el Perfil", "", "", "width: 188px;"); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 45%;text-align: right;">
                                            Numero de identificacion:
                                        </td>
                                        <td>
                                            <input type="text" id="txt_id" name="txt_id" value="<?php echo $identificacion_aux; ?>"></input>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 45%;text-align: right;">
                                            Nombres:
                                        </td>
                                        <td>
                                            <input type="text" id="txt_nombre" name="txt_nombre" value="<?php echo $nombres_aux; ?>"></input>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 45%;text-align: right;">
                                            Apellidos:
                                        </td>
                                        <td>
                                            <input type="text" id="txt_apellido" name="txt_apellido" value="<?php echo $apellidos_aux; ?>"></input>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 45%;text-align: right;">
                                            Fecha de nacimiento:
                                        </td>
                                        <td>
                                            <input type="text" class="input required"  name="txt_fecha_nacimiento" id="txt_fecha_nacimiento" value="<?php echo $fecha_nacimiento_aux; ?>" maxlength="10" size="20" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 45%;text-align: right;">
                                            Direccion:
                                        </td>
                                        <td>
                                            <input type="text" id="txt_direccion" name="txt_direccion" value="<?php echo $direccion_aux; ?>"></input>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 45%;text-align: right;">
                                            Telefonos de contacto:
                                        </td>
                                        <td>
                                            <input type="text" id="txt_telefono" name="txt_telefono" value="<?php echo $telefono_aux; ?>"></input>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                        <td style="width: 50%;">
                            <fieldset style="height: 312px;">
                                <legend>Datos de la cita:</legend>

                                <table>
                                    <tr>
                                        <td style="width: 45%;text-align: right;">
                                            Tipo de Consulta:
                                        </td>
                                        <td>
                                            <p style="font-weight: bold;">
                                                <?php
                                                echo $cita_aux["nombre_tipo_cita"];
                                                ?>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 45%;text-align: right;">
                                            Profesional que atiende:
                                        </td>
                                        <td>
                                            <p style="font-weight: bold;">
                                                <?php
                                                echo $cita_aux["profesional_atiende"];
                                                ?>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 45%;text-align: right;">
                                            Fecha consulta:
                                        </td>
                                        <td>
                                            <input type="text" id="txt_fecha_consulta" name="txt_fecha_consulta" value="<?php echo $cita_aux["fecha_consulta"]; ?>"></input>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 45%;text-align: right;">
                                            Hora de la consulta:
                                        </td>
                                        <td>
                                            <p style="font-weight: bold;">
                                                <?php
                                                $hora_consulta_aux = explode(":", $cita_aux["hora_consulta"]);
                                                if (intval($hora_consulta_aux) <= 9) {
                                                    echo $cita_aux["hora_consulta"] . " A.M.";
                                                } else if (intval($hora_consulta_aux) >= 12) {
                                                    echo $cita_aux["hora_consulta"] . " P.M.";
                                                }
                                                ?>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 45%;text-align: right;">
                                            Tipo de cita:
                                        </td>
                                        <td>
                                            <?php $combo->getComboDb("cmb_tipo_cita", $cita_aux["id_tipo_cita"], $tiposCitas->getTiposcitas(), "id_tipo_cita, nombre_tipo_cita", "Seleccione el Perfil", "", "", "width: 188px;", ""); ?>
                                        </td>
                                    </tr>

                                </table>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="submit" id="btnNuevo" class="btnNuevo" value="GUARDAR" onclick="guardar_admision()"/>
                        </td>
                    </tr>
                </table>
            </form>
            <input type="hidden" id="hdd_paciente" name="hdd_paciente" value="<?php echo $paciente_aux[0]["resultado"]; ?>" />
            <input type="hidden" id="hdd_numero_documento" name="hdd_numero_documento" value="<?php echo $paciente_aux[0]["numero_documento"]; ?>" />
            <input type="hidden" id="hdd_nombre_paciente" name="hdd_nombre_paciente" value="<?php echo $funcionesPersona->obtenerNombreCompleto($paciente_aux[0]["nombre_1"], $paciente_aux[0]["nombre_2"], $paciente_aux[0]["apellido_1"], $paciente_aux[0]["apellido_2"]); ?>" />
        </div>
        <?php
        break;

    case "3": //Resultado de búsqueda de pacientes
        @$parametro = $utilidades->str_decode($_POST["parametro"]);

        $funcionesPersona = new FuncionesPersona();
        $dbPermisos = new DbPermisos();
        $dbMenus = new DbMenus();
        $dbAdmision = new DbAdmision();

        //Se verifica si el usuario tiene permisos para editar admisiones
        $permiso_obj = $dbPermisos->getPermisoUsuarioMenu($id_usuario, 14);
        $tipo_acceso_admisiones = intval($permiso_obj["tipo_acceso"], 10);
        $tabla_menu = $dbMenus->getMenu(14);
        $url_menu_admision = $tabla_menu["pagina_menu"];
        ?>
        <div style="text-align: left;">
            <div id="d_datagrid" style="width: 925px; margin: auto;">
                <script id="ajax">
                    //<![CDATA[ 
                    $(function () {
                        $(".paginated", "table").each(function (i) {
                            $(this).text(i + 1);
                        });

                        $("table.paginated").each(function () {
                            var currentPage = 0;
                            var numPerPage = 20;
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
                <table class="paginated modal_table" style="width: 100%; margin: auto;">
                    <thead>
                        <tr>
                            <th align="center" style="width:20%;">Estado</th>
                            <th align="center" style="width:16%;">N&uacute;mero documento</th>
                            <th align="center" style="width:19%;">Paciente</th>
                            <th align="center" style="width:16%;">Profesionales</th>
                            <th align="center" style="width:18%;">&Uacute;ltimas ubicaciones</th>
                            <th align="center" style="width:11%;">Hora / Tiempo espera</th>
                            <?php
                            if ($tipo_acceso_admisiones == 2) {
                                ?>
                                <th align="center" style="width:14%;">Editar admisi&oacute;n</th>
                                <?php
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dbCitas = new DbCitas();

                        $listadoCitas = $dbCitas->getBuscarPacientesAnt($parametro, 0);
                        ?>
                    <div style="color: #000;font-size: 13px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;margin-top: 15px;">
                        <p style="padding: 0;margin: 0;">Resultado para: <span style="font-weight: 700;"><?php echo $parametro; ?></span></p>
                        <p style="padding: 0;margin: 0;"><?php echo count($listadoCitas); ?>&nbsp;registro(s) encontrado(s)</p>
                    </div>
                    <?php
                    //Si hay resultados:
                    if (count($listadoCitas) > 0) {
                        $estadosAtencion = new DbEstadosAtencion();
                        $dbPermisos = new DbPermisos();
                        $dbMenus = new DbMenus();

                        //Se carga el listado de estados de atención
                        $listadoEstadosAtencion = $estadosAtencion->getEstadosatencion();
                        $arr_estados_atencion = array();
                        if (count($listadoEstadosAtencion) > 0) {
                            foreach ($listadoEstadosAtencion as $estado_aux) {
                                $arr_estados_atencion[$estado_aux["id_estado_atencion"]] = $estado_aux;
                            }
                        }

                        foreach ($listadoCitas as $value) {
                            $id_estado_atencion = $value["id_estado_atencion"];
                            $nombre_completo_aux = $funcionesPersona->obtenerNombreCompleto($value["nombre_1"], $value["nombre_2"], $value["apellido_1"], $value["apellido_2"]);
                            //Se busca la función asociada al estado de atención
                            $funcion_click = "";

                            switch ($id_estado_atencion) {
                                case "1": //AGENDADOS
                                    if ($tipo_acceso_menu == "2") {
                                        $funcion_click = "confirmar_llegada_persona(" . $value["id_cita"] . ", " . $value["id_lugar_cita"] . ", '" . $value["nombre_lugar_cita"] . "','".$arr_estados_atencion[$id_estado_atencion]["pagina_menu"]."');";
                                    }
                                    break;

                                case "10": //EN ESPERA DE ADMISIÓN
                                case "17": //EN ADMISIÓN
                                    if ($tipo_acceso_menu == "2") {
                                        $funcion_click = "modulo_admision('" . $value["id_cita"] . "-" . intval($value["id_paciente"], 10) . "', '" . $arr_estados_atencion[$id_estado_atencion]["pagina_menu"] . "', " . $value["id_lugar_cita"] . ", '" . $value["nombre_lugar_cita"] . "');";
                                    }
                                    break;

                                case "9": //DESPACHADOS
                                    break;

                                default: //Demás estados
                                    $id_menu_aux = trim($arr_estados_atencion[$id_estado_atencion]["id_menu"]);
                                    $pagina_menu_aux = trim($arr_estados_atencion[$id_estado_atencion]["pagina_menu"]);
                                    if ($pagina_menu_aux == "") {
                                        //Si la página es vacía se busca en tipo de cita respectivo
                                        if ($id_estado_atencion != 11 && $id_estado_atencion != 12) {
                                            $menu_obj = $dbMenus->getMenuTipoCitaEstado($value["id_tipo_cita"], $id_estado_atencion);
                                        } else {
                                            $menu_obj = $dbMenus->getMenuTipoCitaEstado($value["id_tipo_cita"], $id_estado_atencion - 6);
                                        }
                                        if (isset($menu_obj["id_menu"])) {
                                            $id_menu_aux = trim($menu_obj["id_menu"]);
                                            $pagina_menu_aux = trim($menu_obj["pagina_menu"]);
                                        }
                                    }

                                    //Se verifica si el usuario tiene permisos para acceder al menú
                                    $permiso_obj = $dbPermisos->getPermisoUsuarioMenu($id_usuario, $id_menu_aux);
                                    $tipo_acceso = 0;
                                    if (isset($permiso_obj["tipo_acceso"])) {
                                        $tipo_acceso = intval($permiso_obj["tipo_acceso"], 10);
                                    }

                                    $acceso_usuario = 1;
                                    if ($id_estado_atencion >= 3 && $id_estado_atencion <= 6) {
                                        //Se verifica si el usuario tiene permisos sobre el estado
                                        $estado_atencion_obj = $dbAdmision->getAdmisionEstadoAtencionUsuario($value["id_admision"], $id_estado_atencion, $id_usuario);
                                        if (!isset($estado_atencion_obj["id_admision"])) {
                                            $acceso_usuario = 0;
                                        }
                                    }
                                    if (($id_estado_atencion == 3 || $id_estado_atencion == 5) && $tipo_acceso != 2) {
                                        $acceso_usuario = 0;
                                    }

                                    if ($tipo_acceso > 0 && $acceso_usuario == 1) {
                                        $funcion_click = "validar_destino(" . $value["id_admision"] . ", " . $value["id_paciente"] . ", " . $id_estado_atencion . ", '" . $nombre_completo_aux . "', '" . $pagina_menu_aux . "', " . $value["id_lugar_cita"] . ", '" . $value["nombre_lugar_cita"] . "', " . $value["id_pago"] . ");";
                                    }
                                    break;
                            }

                            $tabla_profesionales_atencion = $estadosAtencion->getProfesionalesAtencion($value["id_admision"]);
                            $nombre_profesionales = "";
                            if (count($tabla_profesionales_atencion) != 0) {
                                foreach ($tabla_profesionales_atencion as $fila_profesionales_atencion) {
                                    $nombre_profesionales = $nombre_profesionales . $fila_profesionales_atencion["profesionales"] . "<br />";
                                }
                            } else {
                                $nombre_profesionales = $nombre_profesionales . $value["nombre_usuario"];
                            }

                            //Se hallan las últimas ubicaciones del paciente
                            $lista_lugares = $dbAdmision->get_lista_lugares_admision($value["id_admision"], 3);
                            $cadena_lugares = "";
                            if (count($lista_lugares) > 0) {
                                foreach ($lista_lugares as $lugar_aux) {
                                    if ($cadena_lugares != "") {
                                        $cadena_lugares .= "<br />";
                                    }
                                    $cadena_lugares .= $lugar_aux["nombre_lugar"] . " (" . $lugar_aux["nombre_sede"] . ")";
                                }
                            } else {
                                $cadena_lugares = "-";
                            }
                            ?>
                            <tr>
                                <td onclick="<?php echo($funcion_click); ?>" align="left"><?php echo($value["nombre_estado"]); ?></td>
                                <td onclick="<?php echo($funcion_click); ?>" align="center"><?php echo($value["nombre_detalle2"] . " " . $value["numero_documento"]); ?></td>
                                <td onclick="<?php echo($funcion_click); ?>" align="left"><?php echo($nombre_completo_aux); ?></td>
                                <td onclick="<?php echo($funcion_click); ?>" align="left"><?php echo($nombre_profesionales); ?></td>
                                <td onclick="<?php echo($funcion_click); ?>" align="left"><?php echo($cadena_lugares); ?></td>
                                <td onclick="<?php echo($funcion_click); ?>" align="center"><?php echo(substr($value["tiempo_t"], 0, 20)); ?></td>
                                <?php
                                //Si tiene permiso par la venta de admisiones y si todavia no ha ingresado a la consulta
                                if ($tipo_acceso_admisiones == 2 && $value["id_admision"] != 0 && ($id_estado_atencion == 2 || $id_estado_atencion == 3 || $id_estado_atencion == 4 || $id_estado_atencion == 5)) {
                                    ?>
                                    <td align="center">
                                        <?php
                                        if ($value["id_cita"] != "") {
                                            $id_cita = $value["id_cita"];
                                        } else {
                                            $id_cita = 0;
                                        }

                                        $parametro_edita = $id_cita . "-" . $value["id_paciente"] . "-" . $value["id_admision"];
                                        ?>
                                        <input class="btnSecundario peq" type="submit" value="Editar" id="btn_consultar" name="btn_consultar" onclick="modulo_admision('<?php echo($parametro_edita); ?>', '<?php echo($url_menu_admision); ?>', <?php echo($value["id_lugar_cita"]); ?>, '<?php echo($value["nombre_lugar_cita"]); ?>')" />
                                    </td>
                                    <?php
                                } else {
                                    ?>
                                    <td align="center">&nbsp;</td>
                                    <?php
                                }
                                ?>
                            </tr>
                            <?php
                        }
                    } else {
                        //Si no hay resultados:
                        ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">No hay resultados</td> 
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
        break;

    case "4": //Se marca en la cita que la persona ya se encuentra presente
        @$id_cita = $utilidades->str_decode($_POST["id_cita"]);
        $id_estado = 14;

        $dbCitas = new DbCitas();
        $rta_aux = $dbCitas->marca_cita_atendida($id_cita, $id_usuario, $id_estado);
        ?>
        <input type="hidden" name="hdd_resultado_marcar_llegada" id="hdd_resultado_marcar_llegada" value="<?php echo($rta_aux); ?>" />
        <?php
        break;

    case "5": //Detalle de estados de atención
        @$id_usuario_sel = $utilidades->str_decode($_POST["id_usuario_sel"]);
        @$id_lugar_cita = $utilidades->str_decode($_POST["id_lugar_cita"]);

        $dbCitas = new DbCitas();
        $funcionesPersona = new FuncionesPersona();
        $dbPermisos = new DbPermisos();
        $dbAdmision = new DbAdmision();
        $dbMenus = new DbMenus();
        $dbEstadosAtencion = new DbEstadosAtencion();

        $advertencia = "color: #FF5500; ";
        $advertencia_null = "";

        //Estados antes de admisión
        if (strlen($id_usuario_sel) >= 1 || strlen($id_lugar_cita) >= 1) {
            $lista_citas_aux = $dbCitas->getEstadoagendados(0, $id_usuario_sel, $id_lugar_cita);
        } else {
            $lista_citas_aux = $dbCitas->getEstadoagendados(0, "", 0);
        }

        //Estados después de admisión
        if (strlen($id_usuario_sel) >= 1 || strlen($id_lugar_cita) >= 1) {
            $lista_admisiones_aux = $dbCitas->getEstadoadmitidos($id_usuario_sel, $id_lugar_cita);
        } else {
            $lista_admisiones_aux = $dbCitas->getEstadoadmitidos("", 0);
        }

        //Se obtiene el listado de estados de atención
        $lista_estados_atencion = $dbEstadosAtencion->getListaEstadosAtencion(1, 1);
        ?>
        <input type="hidden" id="hdd_cant_estados_nv" value="<?php echo(count($lista_estados_atencion)); ?>" />
        <?php
        foreach ($lista_estados_atencion as $i => $estado_atencion_aux) {
            $id_estado_atencion = $estado_atencion_aux["id_estado_atencion"];
            $id_menu = $estado_atencion_aux["id_menu"];
            $pagina_menu = $estado_atencion_aux["pagina_menu"];
            ?>
            <input type="hidden" id="hdd_estado_nv_<?php echo($i); ?>" value="<?php echo($id_estado_atencion); ?>" />
            <div id="d_estado_nv_<?php echo($id_estado_atencion); ?>">
                <?php
                switch ($id_estado_atencion) {
                    case 1: //AGENDADOS
                    case 10: //EN ESPERA DE ADMISIÓN
                    case 17: //EN ADMISIÓN
                        ?>
                        <table class="tabla_padding" style="width:100%;" id="<?php echo("tabla" . ($i + 1)); ?>">
                            <thead style="display:none;">
                                <tr>
                                    <th align="center" style="width:40%;">Paciente</th>
                                    <th align="center" style="width:20%;">Especialista</th>
                                    <th align="center" style="width:15%;">Convenio</th>
                                    <th align="center" style="width:15%;">Lugar</th>
                                    <th align="center" style="width:10%;">Tiempo</th>
                                </tr>
                            </thead>
                            <?php
                            $estado_cita = 14;
                            if ($id_estado_atencion == 10) {
                                $estado_cita = 130;
                            } else if ($id_estado_atencion == 17) {
                                $estado_cita = 258;
                            }

                            $contador2 = 1;
                            $cont_estado = 0;
                            if (count($lista_citas_aux) > 0) {
                                $tipo_acceso = 0;
                                $funcion_click = "";
                                if ($id_estado_atencion == 10) {
                                    //Se verifica si el usuario tiene permisos para acceder al menú
                                    $permiso_obj = $dbPermisos->getPermisoUsuarioMenu($id_usuario, $id_menu);
                                    if (isset($permiso_obj["tipo_acceso"])) {
                                        $tipo_acceso = intval($permiso_obj["tipo_acceso"], 10);
                                    }
                                }

                                foreach ($lista_citas_aux as $value) {
                                    if ($estado_cita == $value["id_estado_cita"]) {
                                        $nombre_aux = $funcionesPersona->obtenerNombreCompleto($value["PCnombre1"], $value["PCnombre2"], $value["PCapellido1"], $value["PCapellido2"]);
                                        $prof_aux = $funcionesPersona->obtenerNombreCompleto($value["nombre_usuario"], $value["apellido_usuario"], null, null);

                                        if ($tipo_acceso_menu == "2") {
                                            if ($id_estado_atencion == 1) {
                                                $funcion_click = "confirmar_llegada_persona(" . $value["id_cita"] . ", " . $value["id_lugar_cita"] . ", '" . $value["nombre_lugar_cita"] . "','".$pagina_menu."');";
                                            } else {
                                                //Se valida que el usuario tenga permisos sobre el menú de admisiones
                                                $permiso_admision_obj = $dbPermisos->getPermisoUsuarioMenu($id_usuario, 14);
                                                if (isset($permiso_admision_obj["tipo_acceso"]) && $permiso_admision_obj["tipo_acceso"] == 2) {
                                                    $funcion_click = "modulo_admision('" . $value["id_cita"] . "-" . intval($value["id_paciente"], 10) . "', '" . $pagina_menu . "', " . $value["id_lugar_cita"] . ", '" . $value["nombre_lugar_cita"] . "')";
                                                }
                                            }
                                        }
                                        ?>
                                        <tr onclick="<?php echo($funcion_click); ?>" id="tr_<?php echo($id_estado_atencion . "_" . $contador2); ?>" class="estado_resultados">
                                            <td id="<?php echo($value["id_cita"] . "-" . intval($value["id_paciente"], 10)); ?>" align="left" style="width:40%;">
                                                <?php echo($nombre_aux . " (" . $value["nombre_tipo_cita"] . ")"); ?>
                                            </td>
                                            <td align="left" style="display:none; width:20%;"><?php echo($prof_aux); ?></td>
                                            <td align="center" style="display:none; width:15%;"><?php echo($value["nombre_convenio"]); ?></td>
                                            <td align="center" style="display:none; width:15%;"><?php echo($value["nombre_lugar_cita"]); ?></td>
                                            <td align="right" style="width:10%;">
                                                <?php
                                                if ($id_estado_atencion == 1) {
                                                    $hora = explode(":", $value["fecha_cita"]);
                                                    if (intval($hora[0]) > 12) {
                                                        $hora_aux = intval($hora[0]) - 12; //Agrega el 0 a la cadena de texto
                                                        if (strlen($hora_aux) <= 1) {
                                                            $hora_aux = "0" . $hora_aux;
                                                        }
                                                        echo($hora_aux . ":" . $hora[1] . "&nbsp;P.M.");
                                                    } else if (intval($hora[0]) <= 11) {
                                                        echo($hora[0] . ":" . $hora[1] . "&nbsp;A.M.");
                                                    } else if (intval($hora[0]) == 12) {
                                                        echo($hora[0] . ":" . $hora[1] . "&nbsp;P.M.");
                                                    }
                                                } else {
                                                    ?>
                                                    <span id="sp_<?php echo $id_estado_atencion . "_" . $contador2; ?>"></span>
                                                    <?php
                                                    if ($id_estado_atencion == 10) {
                                                        $hora_aux = explode(":", $value["dif_fecha_llegada"]);
                                                    } else if ($id_estado_atencion == 17) {
                                                        $hora_aux = explode(":", $value["dif_fecha_ingreso"]);
                                                    }
                                                    ?>
                                                    <script type="text/javascript" id="ajax">
                                    actualizar_reloj("<?php echo intval($hora_aux[0]); ?>", "<?php echo intval($hora_aux[1]); ?>", "<?php echo intval($hora_aux[2]); ?>", "", "", "", "<?php echo $contador2; ?>", <?php echo($id_estado_atencion); ?>);
                                                    </script>
                                                    <?php
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $contador2++;
                                        $cont_estado++;
                                    }
                                }
                            }

                            //Si no se encontraron pacientes en el estado
                            if ($cont_estado == 0) {
                                ?>
                                <tr>
                                    <td colspan="5">
                                        <div class="msj-vacio">
                                            <p>No hay pacientes en esta zona</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </table>
                        <script type="text/javascript" id="ajax">
                    <?php
                    if ($cont_estado > 0) {
                        ?>
                                var texto_aux = "(<?php echo($cont_estado); ?>)";
                        <?php
                    } else {
                        ?>
                                var texto_aux = "";
                        <?php
                    }
                    ?>
                            $("#sp_cantidad_ea_<?php echo($id_estado_atencion); ?>").html(texto_aux);
                        </script>
                        <?php
                        break;

                    case 9: //DESPACHADOS
                        ?>
                        <table class="tabla_padding" style="width:100%;" id="<?php echo("tabla" . ($i + 1)); ?>">
                            <thead style="display:none;">
                                <tr>
                                    <th align="center" style="width:40%;">Paciente</th>
                                    <th align="center" style="width:20%;">Especialista</th>
                                    <th align="center" style="width:15%;">Convenio</th>
                                    <th align="center" style="width:15%;">Lugar</th>
                                    <th align="center" style="width:10%;">Tiempo</th>
                                </tr>
                            </thead>
                            <?php
                            $contador2 = 1;
                            $cont_estado = 0;

                            foreach ($lista_admisiones_aux as $value) {
                                if ($value["id_estado_atencion"] == $id_estado_atencion && (strlen($id_usuario_sel) == 0 || $id_usuario_sel == $value["id_usuario_prof"])) {
                                    $id_menu = "";
                                    $pagina_destino = "";
                                    if ($pagina_menu == "" && isset($lista_admisiones_aux[0])) {
                                        //Si la página es vacía se busca en tipo de cita respectivo
                                        $menu_obj = $dbMenus->getMenuTipoCitaEstado($value["id_tipo_cita"], $id_estado_atencion);
                                        if (isset($menu_obj["id_menu"])) {
                                            $id_menu = trim($menu_obj["id_menu"]);
                                            $pagina_destino = trim($menu_obj["pagina_menu"]);
                                        }
                                    } else {
                                        $id_menu = $id_menu_orig;
                                        $pagina_destino = $pagina_menu;
                                    }

                                    //Se verifica si el usuario tiene permisos para acceder al menú
                                    $permiso_obj = $dbPermisos->getPermisoUsuarioMenu($id_usuario, $id_menu);
                                    $tipo_acceso = 0;
                                    if (isset($permiso_obj["tipo_acceso"])) {
                                        $tipo_acceso = intval($permiso_obj["tipo_acceso"], 10);
                                    }

                                    $nombre_aux = $funcionesPersona->obtenerNombreCompleto($value["nombre_1"], $value["nombre_2"], $value["apellido_1"], $value["apellido_2"]);
                                    $prof_aux = $funcionesPersona->obtenerNombreCompleto($value["nombre_usuario"], $value["apellido_usuario"], null, null);
                                    $id_paciente = $value["id_paciente"];
                                    $id_admision = $value["id_admision"];
                                    $id_usuario_prof = $value["id_usuario_prof"];
                                    ?>
                                    <tr class="estado_resultados">
                                        <td align="left"><?php echo($nombre_aux . " (" . $value["nombre_tipo_cita"] . ")"); ?></td>
                                        <td align="left" style="display:none;"><?php echo($prof_aux); ?></td>
                                        <td align="center" style="display:none;"><?php echo($value["nombre_convenio"]); ?></td>
                                        <td align="center" style="display:none;"><?php echo($value["nombre_lugar_cita"]); ?></td>
                                        <?php
                                        $estilo_aux = "";
                                        if ($cont_estado % 3 != 2) {
                                            $estilo_aux = "border-right:1px solid rgba(218, 218, 218, 0.73);";
                                        }
                                        ?>
                                        <td align="right" style="<?php echo($estilo_aux); ?>">
                                            <?php
                                            $texto_aux = "";
                                            if ($value["dif_fecha_atencion"] != "") {
                                                $hora2_aux = explode(":", $value["dif_fecha_atencion"]);
                                                $texto_aux = "(" . $hora2_aux[0] . ":" . $hora2_aux[1] . ")";
                                            }
                                            if (trim($value["hora_llegada"]) != "") {
                                                $hora = explode(":", $value["hora_llegada"]);
                                                if (intval($hora[0]) > 12) {
                                                    $hora_aux = intval($hora[0]) - 12; //Agrega el 0 a la cadena de texto
                                                    if (strlen($hora_aux) <= 1) {
                                                        $hora_aux = "0" . $hora_aux;
                                                    }
                                                    echo($hora_aux . ":" . $hora[1] . "&nbsp;P.M.");
                                                } else if (intval($hora[0]) <= 11) {
                                                    echo($hora[0] . ":" . $hora[1] . "&nbsp;A.M.");
                                                } else if (intval($hora[0]) == 12) {
                                                    echo($hora[0] . ":" . $hora[1] . "&nbsp;P.M.");
                                                }
                                                if ($texto_aux != "") {
                                                    echo("<br />");
                                                }
                                            }
                                            if ($texto_aux != "") {
                                                echo($texto_aux);
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                    $cont_estado++;
                                }
                                $contador2++;
                            }

                            //Si no se encontraron pacientes en el estado
                            if ($cont_estado == 0) {
                                ?>
                                <tr>
                                    <td colspan="5">
                                        <div class="msj-vacio">
                                            <p>No hay pacientes en esta zona</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </table>
                        <script type="text/javascript" id="ajax">
                    <?php
                    if ($cont_estado > 0) {
                        ?>
                                var texto_aux = "(<?php echo($cont_estado); ?>)";
                        <?php
                    } else {
                        ?>
                                var texto_aux = "";
                        <?php
                    }
                    ?>
                            $("#sp_cantidad_ea_<?php echo($id_estado_atencion); ?>").html(texto_aux);
                        </script>
                        <?php
                        break;

                    default: //Los demás estados
                        ?>
                        <table class="tabla_padding" style="width:100%;" id="<?php echo("tabla" . ($i + 1)); ?>">
                            <thead style="display:none;">
                                <tr>
                                    <th align="center" style="width:40%;">Paciente</th>
                                    <th align="center" style="width:20%;">Especialista</th>
                                    <th align="center" style="width:15%;">Convenio</th>
                                    <th align="center" style="width:15%;">Lugar</th>
                                    <th align="center" style="width:10%;">Tiempo</th>
                                </tr>
                            </thead>
                            <?php
                            //id_paciente
                            $contador2 = 1;
                            $cont_estado = 0;

                            foreach ($lista_admisiones_aux as $value) {
                                if ($value["id_estado_atencion"] == $id_estado_atencion) {
                                    $id_menu_aux = "";
                                    $pagina_destino = "";
                                    if ($pagina_menu == "" && isset($lista_admisiones_aux[0])) {
                                        //Si la página es vacía se busca en tipo de cita respectivo
                                        if ($id_estado_atencion == 11 || $id_estado_atencion == 12) {
                                            //Preconsultas
                                            $menu_obj = $dbMenus->getMenuTipoCitaEstado($value["id_tipo_cita"], $id_estado_atencion - 6);
                                        } else {
                                            $menu_obj = $dbMenus->getMenuTipoCitaEstado($value["id_tipo_cita"], $id_estado_atencion);
                                        }

                                        if (isset($menu_obj["id_menu"])) {
                                            $id_menu_aux = trim($menu_obj["id_menu"]);
                                            $pagina_destino = trim($menu_obj["pagina_menu"]);
                                        }
                                    } else {
                                        $id_menu_aux = $id_menu;
                                        $pagina_destino = $pagina_menu;
                                    }

                                    //Se verifica si el usuario tiene permisos para acceder al menú
                                    $permiso_obj = $dbPermisos->getPermisoUsuarioMenu($id_usuario, $id_menu_aux);
                                    $tipo_acceso = 0;
                                    if (isset($permiso_obj["tipo_acceso"])) {
                                        $tipo_acceso = intval($permiso_obj["tipo_acceso"], 10);
                                    }

                                    $nombre_aux = $funcionesPersona->obtenerNombreCompleto($value["nombre_1"], $value["nombre_2"], $value["apellido_1"], $value["apellido_2"]);
                                    $prof_aux = $funcionesPersona->obtenerNombreCompleto($value["nombre_usuario"], "", $value["apellido_usuario"], "");
                                    $id_paciente = $value["id_paciente"];
                                    $id_admision = $value["id_admision"];
                                    $id_pago = $value["id_pago"];
                                    $id_usuario_prof = $value["id_usuario_prof"];

                                    $acceso_usuario = 1;
                                    if (($id_estado_atencion >= 3 && $id_estado_atencion <= 6) || ($id_estado_atencion >= 11 && $id_estado_atencion <= 14)) {
                                        //Se verifica si el usuario tiene permisos sobre el estado
                                        $estado_atencion_obj = $dbAdmision->getAdmisionEstadoAtencionUsuario($id_admision, $id_estado_atencion, $id_usuario);
                                        if (!isset($estado_atencion_obj["id_admision"]) && $id_estado_atencion != 3 && $id_estado_atencion != 13) {
                                            $acceso_usuario = 0;
                                        }
                                    }
                                    if (($id_estado_atencion == 3 || $id_estado_atencion == 5 || $id_estado_atencion == 11 || $id_estado_atencion == 13) && $tipo_acceso != 2) {
                                        $acceso_usuario = 0;
                                    }

                                    $anexo_nombre_aux = " (" . ($value["nombre_tipo_cita"] != "" ? $value["nombre_tipo_cita"] : "Sin admisi&oacute;n") . ")";
                                    if ($value["id_tipo_espera"] != "") {
                                        $anexo_nombre_aux .= " - <span class=\"activo\">" . $value["tipo_espera"] . " " . $value["dif_fecha_espera"] . "</span>";
                                    }
                                    ?>
                                    <tr <?php if ($tipo_acceso > 0 && $acceso_usuario == 1) { ?>onclick="validar_destino(<?php echo($id_admision); ?>, <?php echo($id_paciente); ?>, <?php echo($id_estado_atencion); ?>, '<?php echo($nombre_aux); ?>', '<?php echo($pagina_destino); ?>', <?php echo($value["id_lugar_cita"]); ?>, '<?php echo($value["nombre_lugar_cita"]); ?>', <?php echo($id_pago); ?>)" <?php } ?>class="estado_resultados">
                                        <td align="left" style="width:40%;"><?php echo($nombre_aux . $anexo_nombre_aux); ?></td>
                                        <td align="left" style="display:none; width:20%;"><?php echo($prof_aux); ?></td>
                                        <td align="center" style="display:none; width:15%;"><?php echo($value["nombre_convenio"]); ?></td>
                                        <td align="center" style="display:none; width:15%;"><?php echo($value["nombre_lugar_cita"]); ?></td>
                                        <td align="right" style="<?php echo($value["dif_fecha_admision"] >= "00:10" ? $advertencia : $advertencia_null); ?> width:10%;">
                                            <span id="sp_<?php echo $id_estado_atencion . "_" . $contador2; ?>"></span>
                                            <script type="text/javascript" id="ajax">
                            <?php
                            $hora_aux = explode(":", $value["dif_fecha_admision"]);
                            if ($id_estado_atencion != 2) {
                                $hora2_aux = explode(":", $value["dif_fecha_admitido"]);
                                ?>
                                                    actualizar_reloj("<?php echo intval($hora_aux[0]); ?>", "<?php echo intval($hora_aux[1]); ?>", "<?php echo intval($hora_aux[2]); ?>", "<?php echo intval($hora2_aux[0]); ?>", "<?php echo intval($hora2_aux[1]); ?>", "<?php echo intval($hora2_aux[2]); ?>", "<?php echo $contador2; ?>", <?php echo($id_estado_atencion); ?>);
                                <?php
                            } else {
                                ?>
                                                    actualizar_reloj("<?php echo intval($hora_aux[0]); ?>", "<?php echo intval($hora_aux[1]); ?>", "<?php echo intval($hora_aux[2]); ?>", "", "", "", "<?php echo $contador2; ?>", <?php echo($id_estado_atencion); ?>);
                                <?php
                            }
                            ?>
                                            </script>
                                        </td>
                                    </tr>
                                    <?php
                                    $cont_estado++;
                                }
                                $contador2++;
                            }

                            //Si no se encontraron pacientes en el estado
                            if ($cont_estado == 0) {
                                ?>
                                <tr>
                                    <td colspan="5">
                                        <div class="msj-vacio">
                                            <p>No hay pacientes en esta zona</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </table>
                        <script type="text/javascript" id="ajax">
                    <?php
                    if ($cont_estado > 0) {
                        ?>
                                var texto_aux = "(<?php echo($cont_estado); ?>)";
                        <?php
                    } else {
                        ?>
                                var texto_aux = "";
                        <?php
                    }
                    ?>
                            $("#sp_cantidad_ea_<?php echo($id_estado_atencion); ?>").html(texto_aux);
                        </script>
                        <?php
                        break;
                }
                ?>
            </div>
            <?php
        }
        break;

    case "6": //Formulario de llegada a citas
        $id_cita = $utilidades->str_decode($_POST["id_cita"]);
        $pagina_menu = $utilidades->str_decode($_POST["paginaMenu"]);
        
        $funcionesPersona = new FuncionesPersona();
        $dbCitas = new DbCitas();
        $cita_obj = $dbCitas->getCita($id_cita);

        $id_paciente = $cita_obj["id_paciente"];
        
        /*
        if ($id_paciente != "0" && $id_paciente != "") {
            $contenido->ver_historia($id_paciente);
        }
        */
        ?>
        <input type="hidden" id="hdd_id_paciente_llegada" value="<?php echo($id_paciente); ?>" />
        <div class="encabezado">
            <h3>Detalles de la cita</h3>           
        </div>
        <div id="dContenedorInterno">
            <div id="d_loader" style="height: 16px; width: 16px; margin: auto; padding: 10px 0 0 0;"></div>
            <?php
            if (count($cita_obj) > 0) {
                ?>
                <input type="hidden" id="id_cita_llegada" value="<?php echo($id_cita); ?>" />
                <table style="width:750px; margin:auto;" class="texto_subtitulo">   
                    <tr>
                        <td colspan="2">
                            <h6>Asignada por: <?php echo($cita_obj["usuario_creador"]); ?></h6>
                        </td>
                        <td colspan="2">
                            <h6><?php echo $funcionesPersona->obtenerFecha5(strtotime($cita_obj["fecha_crea"])); ?></h6>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" style="width:25%;">Tipo de cita:</td>
                        <td align="left" style="width:25%;"><b><span style="color: #E3B02F"><?php echo $cita_obj["nombre_tipo_cita"]; ?></span></b></td>
                        <td align="right" style="width:25%;">Hora:</td>
                        <td align="left" style="width:25%;"><b><?php echo $cita_obj["hora_consulta_t"]; ?></b></td>
                    </tr>
                    <tr>
                        <td align="right">Profesional que atiende:</td>
                        <td align="left"><b><?php echo $cita_obj["profesional_atiende"]; ?></b></td>
                        <td align="right">Lugar:</td>
                        <td align="left"><b><?php echo $cita_obj["nombreLugarAux"]; ?></b></td>
                    </tr>
                    <tr>
                        <td align="right">Tipo de documento:</td>
                        <td align="left"><b><?php echo $cita_obj["nombre_detalle"]; ?></b></td>
                        <td align="right">N&uacute;mero de documento:</td>
                        <td align="left"><b><?php echo $cita_obj["numero_documento"]; ?></b></td>
                    </tr>
                    <tr>
                        <td align="right">Nombre del paciente:</td>
                        <td align="left"><b><?php echo $funcionesPersona->obtenerNombreCompleto($cita_obj["PCnombre1"], $cita_obj["PCnombre2"], $cita_obj["PCapellido1"], $cita_obj["PCapellido2"]) ?></b></td>
                        <td align="right">Tel&eacute;fono de contacto:</td>
                        <td align="left"><b><?php echo $cita_obj["PCtelefono"]; ?></b></td>
                    </tr>
                    <!-- HCUT
                    <tr>                        
                        <td align="right">Usa lentes:</td>
                        <td align="left"><b><?php //echo $cita_obj["ind_lentes"] == 1 ? "Si" : "No";      ?></b></td>                        
                        <td align="right">Tiempo de cita:</td>
                        <td align="left"><b><?php echo $cita_obj["tiempo_cita"] . " Minutos"; ?></b></td>
                    </tr>
                        END HCUT-->
                    <tr>
                        <td align="right">Convenio:</td>
                        <?php
                        $texto_convenio = $cita_obj["nombre_convenio_aux"];
                        if ($cita_obj["ind_no_pago"] == "1") {
                            $texto_convenio .= ' <span class="texto_resaltar">(NP)</span>';
                        }
                        ?>
                        <td align="left"><b><span style="color: #008CC7"><?php echo($texto_convenio); ?></span></b></td>
                        <td align="right">Plan:</td>
                        <?php
                            $texto_plan = $cita_obj["nom_plan_aux"];                            
                        ?>
                        <td align="left"><b><span style="color: #008CC7"><?php echo($texto_plan); ?></span></b></td>                           
                    </tr>
                    <tr>
                        <td align="right">Rango:</td>
                        <?php
                        $rango = "";
                        switch ($cita_obj["rango_paciente"]) {
                            case 1:
                                $rango = "Rango 1";
                                break;
                            case 2:
                                $rango = "Rango 2";
                                break;
                            case 3:
                                $rango = "Rango 3";
                                break;
                            case 0:
                                $rango = "No aplica";
                                break;
                            default:
                                $rango = "";
                                break;
                        }
                        ?>
                        <td align="left"><b><span style="color: #008CC7"><?= $rango ?></span></b></td>
                        <td align="right">Tipo de usuario:</td>
                        <?php
                        $tipoUsuario = "";
                        switch ($cita_obj["tipo_coti_paciente"]) {
                            case 1:
                                $tipoUsuario = "Cotizante";
                                break;
                            case 2:
                                $tipoUsuario = "Beneficiario";
                                break;
                            case 0:
                                $tipoUsuario = "No aplica";
                                break;
                            default:
                                $tipoUsuario = "";
                                break;
                        }
                        ?>
                        <td align="left"><b><span style="color: #008CC7"><?= $tipoUsuario ?></span></b></td>
                    </tr>
                    <tr>
                        <td align="right">¿Exento pago cuota moderadora?:</td>
                        <?php
                        $exento = "";
                        switch ($cita_obj["exento_pamo_paciente"]) {
                            case 1:
                                $exento = "No";
                                break;
                            case 0:
                                $exento = "Sí";
                                break;
                            default:
                                $exento = "";
                                break;
                        }
                        ?>
                        <td align="left"><b><span style="color: #008CC7"><?= $exento ?></span></b></td>
                    </tr>
                    <tr>
                        <?php
                        $estilo = "color:#399000;";
                        $estadoSeguro = "";

                        switch ($cita_obj['PCstatusseguro']) {
                            case 1:
                                $estadoSeguro = "Activo";
                                break;
                            case 2:
                                $estadoSeguro = "Inactivo";
                                $estilo = "color:#DD5043;";
                                break;
                            case 3:
                                $estadoSeguro = "Atención especial";
                                break;
                            case 4:
                                $estadoSeguro = "Retirado";
                                break;
                        }
                        ?>
                        <td colspan="4" style="<?= $estilo ?>"><b>Estado del seguro: <span><?= $estadoSeguro ?></span></b></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: left;"><b>Observaciones de la cita:</b></td>
                    </tr>
                    <tr>
                        <td style="text-align: justify;font-size: 12pt;" colspan="4">
                            <textarea style="height: 150px;" readonly="" rows="4" cols="50"><?php echo(strip_tags ($cita_obj["observacion_cita"])); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center;" colspan="4">
                            <!-- HCUT <input class="btnPrincipal" type="button" value="¿Admisionar?" onclick="marcar_llegada_persona(<?php //echo($id_cita); ?>)" />END HCUT-->
                            <input class="btnPrincipal" type="button" value="Admisionar" onclick="<?= "modulo_admision('".($id_cita."-".$id_paciente)."','".$pagina_menu."',".$cita_obj['id_lugar_cita'].",'".$cita_obj["nombreLugarAux"]."')" ?>" />                
                        </td>
                    </tr>
                </table>
                <?php
            }
            ?>
        </div>
        <?php
        break;

    case "7": //Se registra la ubicación actual del paciente en consulta
        $id_admision = $utilidades->str_decode($_POST["id_admision"]);

        //Se obtiene la dirección IP del usuario
        $direccion_ip = filter_var($_SERVER["HTTP_X_FORWARDED_FOR"], FILTER_VALIDATE_IP);
        if ($direccion_ip == "") {
            $direccion_ip = filter_var($_SERVER["REMOTE_ADDR"], FILTER_VALIDATE_IP);
        }

        $dbAdmision = new DbAdmision();
        $resultado = $dbAdmision->crearEditarAdmisionLugar($id_admision, $direccion_ip, $id_usuario);
        ?>
        <input type="hidden" id="hdd_resul_registrar_lugar_adm" value="<?php echo($resultado); ?>" />
        <?php
        break;
}
?>
