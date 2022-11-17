<?php
require_once("../db/DbVariables.php");
require_once("../db/DbUsuarios.php");
require_once("../db/DbMenus.php");
require_once("../db/DbHistoriaClinica.php");
require_once("../db/DbExamenesOptometria.php");
require_once("../db/DbCirugias.php");
require_once("../funciones/FuncionesPersona.php");
require_once("../funciones/Utilidades.php");

require_once("../db/Configuracion.php");

class ContenidoHtml {
    /*
     * Funcion para generar el enbabezadao de la pagina
     */

    public function cabecera_html() {
        $dbVariables = new Dbvariables();
        $usuarios = new DbUsuarios();
        $menus = new DbMenus();

        //variables
        $titulo = $dbVariables->getVariable(1);

        //usuarios
        $usuarios_r = $usuarios->getUsuario($_SESSION["nomUsuario"]);
        ?>
        <div class="topbar">
            <div class="wrapper">
                
                <img src="<?= $_SESSION["logo"] ?>" class="logo" style="margin-top:15px;" />
                <ul class="dropdown top-nav">
                    <?php
                    //Array que contendrá los accesos del usuario
                    $arr_accesos_usuario = array();

                    //Imprime el menu
                    $menus_r = $menus->getListaMenus2($_SESSION["idUsuario"]);

                    foreach ($menus_r as $value) {
                        if ($value['id_menu_padre'] == 0) {
                            ?>
                            <li>
                                <a href="#" onclick="enviar_credencial('<?php echo($value['pagina_menu']); ?>', <?php echo($value['id_menu']); ?>)"><?php echo($value['nombre_menu']); ?></a>
                                <ul>
                                    <?php
                                    for ($i = 0; $i <= count($menus_r) - 1; $i++) {
                                        if ($menus_r[$i]['id_menu_padre'] == $value['id_menu']) {
                                            ?>
                                            <li>
                                                <a href="#" onclick="enviar_credencial('<?php echo($menus_r[$i]['pagina_menu']); ?>', <?php echo($menus_r[$i]['id_menu']); ?>)"><?php echo($menus_r[$i]['nombre_menu']); ?></a>
                                                <ul>
                                                    <?php
                                                    for ($e = 0; $e <= count($menus_r) - 1; $e++) {
                                                        if ($menus_r[$e]['id_menu_padre'] == $menus_r[$i]['id_menu']) {
                                                            ?>
                                                            <li><a href="#" onclick="enviar_credencial('<?php echo($menus_r[$e]['pagina_menu']); ?>', <?php echo($menus_r[$e]['id_menu']); ?>)"><?php echo($menus_r[$e]['nombre_menu']); ?></a></li>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </ul>
                                            </li>
                                            <?php
                                        }
                                    }
                                    ?>
                                </ul>
                            </li>
                            <?php
                        }

                        //Se el menú con el tipo de acceso al array
                        $arr_accesos_usuario[$value["id_menu"]] = intval($value["tipo_acceso"]);
                    }

                    //Se agregan los tipos de acceso a la sesión
                    $_SESSION["accesos_usuario"] = $arr_accesos_usuario;
                    ?>
                    <li><a href="#">Opciones</a>
                        <ul class="sub_menu">
                            <li><a href="#" onclick="enviar_credencial('../principal/pass.php', 0)">Cambiar contrase&ncaron;a</a></li>
                            <li><a href="#" onclick="confirmar()">Salir</a>
                        </ul>
                    </li>


                    </li>
                </ul>
                
                <?php
                    //Genera randomico para el color
                    $tmpRand = rand(1, 6);
                    $colorAux = "";
                    switch ($tmpRand) {
                        case 1:
                            $colorAux = "BE8F1A";
                            break;
                        case 2:
                            $colorAux = "012996";
                            break;
                        case 3:
                            $colorAux = "399000";
                            break;
                        case 4:
                            $colorAux = "DD5043";
                            break;
                        case 5:
                            $colorAux = "008CC7";
                            break;
                        case 5:
                            $colorAux = "E11111";
                            break;
                    }
                ?>
                
                <div style="position: relative;height: 30px;">
                    <div style="position: absolute;">
                        <img src="../imagenes/home-icon.png" style="width: 23px;" /><span style="color: #<?= $colorAux ?>; font-weight: bold;"><?php echo $_SESSION["nomLugarUsuario"]; ?></span>
                    </div>
                    <div style="position: absolute;right: 0;">
                        <img src="../imagenes/User-2-icon3.png" style="width: 23px;" /><span style="color: #<?= $colorAux ?>; font-weight: bold;"><?php echo $_SESSION["nomUsuario"]; ?></span>
                    </div>
                </div>
                
                
                
            </div>
        </div>
        <?php
    }

    public function cabecera_resultados_html() {
        $dbVariables = new Dbvariables();
        $usuarios = new DbUsuarios();
        $menus = new DbMenus();

        //variables
        $titulo = $dbVariables->getVariable(1);
        ?>
        <div class="topbar">
            <div class="wrapper">
                <a href="#" rel="home"><h1 class="ir logo">Historia Cl&iacute;nica</h1></a>
                <ul class="dropdown top-nav">

                    <li><a href="#">Opciones</a>
                        <ul class="sub_menu">
                            <!-- <li><a href="#" onclick="enviar_credencial('../principal/pass.php', 0)">Cambiar contrase&ncaron;a</a></li> -->
                            <li><a href="#" onclick="confirmar_cerrar_resultados()">Salir</a>
                        </ul>
                    </li>

                </ul>
                <p style="margin: 0; margin-top: -10px; color: #FFF; text-align: right;font-size: 14pt;">Bienvenido: <span style="font-weight: 600;"><?php echo $_SESSION["nomUsuario"]; ?></span></p>
            </div>
        </div>
        <?php
    }

    /*
     * Funcion para generar el pie de pagina 
     */

    public function footer() {
        ?>      
        <div class="footer clearfix">
            <div class="wrapper">
                <p class="left" style="text-align: left;"><?php echo Configuracion::$FOOTER_LEFT; ?></p>
                <p class="right"><?php echo Configuracion::$FOOTER_RIGHT . " " . date("Y"); ?></p>
            </div>
        </div>
        <div id="fondo_negro_adic" class="d_fondo_negro" style="z-index:4;"></div>
        <div class="div_centro" id="d_centro_adic" style="display:none; z-index:5;">
            <a name="a_cierre_panel_adic" id="a_cierre_panel_adic" class="a_cierre_panel" href="#" onclick="cerrar_div_centro_adic();"></a>
            <div class="div_interno" id="d_interno_adic"></div>
        </div>
        <div id="fondo_negro_extend" class="d_fondo_negro"></div>
        <div class="div_centro" id="d_centro_extend" style="display:none; width:99%; height:98%; top:1%;z-index: 10;">
            <a name="a_cierre_panel_extend" id="a_cierre_panel_extend" class="a_cierre_panel" href="#" onclick="cerrar_div_centro_extend();"></a>
            <div class="div_interno" id="d_interno_extend"></div>
        </div>
        <div id="fondo_negro" class="d_fondo_negro"></div>
        <div class="div_centro" id="d_centro" style="display:none;">
            <a name="a_cierre_panel" id="a_cierre_panel" class="a_cierre_panel" href="#" onclick="cerrar_div_centro();"></a>
            <div class="div_interno" id="d_interno"></div>
        </div>
        <div id="d_impresion_hc" style="display:none;"></div>
        <?php
    }

    /*
     * Pie de pagina pra las paginas iframe
     */

    public function footer_iframe() {
        ?>
        <div id="fondo_negro" class="d_fondo_negro"></div>
        <div class="div_centro" id="d_centro" style="display:none;">
            <a name="a_cierre_panel" id="a_cierre_panel" href="#" onclick="cerrar_div_centro();"></a>
            <div class="div_interno" id="d_interno"></div>
        </div>
        <div id="d_impresion_hc" style="display:none;"></div>
        <?php
    }

    /*
     * Funcion para generar DIV flotante ver HC
     */

    public function ver_historia($id_paciente) {
        $dbHistoriaClinica = new DbHistoriaClinica();
        $dbExamenesOptometria = new DbExamenesOptometria();
        $dbCirugias = new DbCirugias();
        $dbVariables = new Dbvariables();
        $personas = new FuncionesPersona();
        $utilidades = new Utilidades();

        @$credencial = $_POST["credencial"];
        @$id_menu = $_POST["hdd_numero_menu"];

        //Se borran las imágenes temporales creadas por el usuario actual
        $ruta_tmp = "../historia_clinica/tmp/" . $_SESSION["idUsuario"];

        @mkdir($ruta_tmp);

        //Se obtiene la ruta actual de las imágenes
        $arr_ruta_base = $dbVariables->getVariable(17);
        $ruta_base = $arr_ruta_base["valor_variable"];
        ?>
        <div id="caja_flotante" style="width:100px;">
            <div id="ver_hc_panel" onclick="ver_hc_panel();">
                <h6>Ver HC</h6>
                <div class="ver_hc"></div>
            </div>
            <div id="ocultar_hc_panel" style="display:none;" onclick="ocultar_hc_panel();">
                <h6>Ocultar Historia Cl&iacute;nica</h6>
                <div class="ocultar_hc"></div>
            </div>
            <div id="detalle_hc" style="display:none; overflow:auto; max-height:500px;">
                <?php
                $tabla_registro_hc_aux = $dbHistoriaClinica->getRegistrosHistoriaClinica($id_paciente);

                //Se obtiene el listado de exámenes de optometría
                $lista_examenes_paciente = $dbExamenesOptometria->get_lista_examenes_optometria_paciente($id_paciente);
                $mapa_examenes_paciente = array();
                if (count($lista_examenes_paciente) > 0) {
                    foreach ($lista_examenes_paciente as $examene_aux) {
                        if (!isset($mapa_examenes_paciente[$examene_aux["id_hc"]])) {
                            $mapa_examenes_paciente[$examene_aux["id_hc"]] = array();
                        }
                        array_push($mapa_examenes_paciente[$examene_aux["id_hc"]], $examene_aux);
                    }
                }

                //Se construyen los filtros para la HC actual
                $lista_tipos_reg = array();
                $lista_usuarios_prof = array();

                foreach ($tabla_registro_hc_aux as $registro_hc_aux) {
                    if (!isset($lista_tipos_reg[$registro_hc_aux["id_tipo_reg"]])) {
                        $lista_tipos_reg[$registro_hc_aux["id_tipo_reg"]] = $registro_hc_aux["nombre_tipo_reg"];
                    }
                    if ($registro_hc_aux["id_clase_reg"] != "4" && $registro_hc_aux["id_clase_reg"] != "5") {
                        if (!isset($lista_usuarios_prof[$registro_hc_aux["id_usuario_reg"]])) {
                            $lista_usuarios_prof[$registro_hc_aux["id_usuario_reg"]] = $registro_hc_aux["nombre_usuario"] . " " . $registro_hc_aux["apellido_usuario"];
                        }
                    }
                }
                //Se ordenan las listas
                asort($lista_tipos_reg);
                asort($lista_usuarios_prof);
                ?>
                <table border="0" width="100%;">
                    <tr>
                        <td align="left" colspan="2">
                            <div id="d_btn_impr_completa_1" style="display:block; height:20px;">
                                <input type="button" id="btn_historia_completa" value="Ver Historia Completa" class="btnPrincipal peq no-margin" onclick="imprimir_hc_completa_ext(<?php echo($id_paciente); ?>);" />
                                <input type="button" id="btn_resumen_hc" value="Ver Resumen" class="btnPrincipal peq no-margin" onclick="imprimir_hc_resumen_ext(<?php echo($id_paciente); ?>);" />
                                &nbsp;&nbsp;&nbsp;&nbsp;
                            </div>
                            <div id="d_btn_impr_completa_2" style="display:none; height:20px;">
                                <img src="../imagenes/ajax-loader.gif" />
                            </div>
                        </td>
                    </tr>
                    <tr style="height:5px;"></tr>
                    <tr>
                        <td align="left" style="width:99%;">
                            <div id="d_btn_impr_completa_3" style="display:block; height:20px;">
                                <input type="button" id="btn_imagenes_hc" value="Ver Im&aacute;genes" class="btnPrincipal peq no-margin" onclick="mostrar_consultas_div(<?php echo($id_paciente); ?>, '', 0, '../historia_clinica/ver_imagenes_hc.php', 0, 0, 65);" />
                            </div>
                        </td>
                        <?php
                        if (count($lista_tipos_reg) > 0 || count($lista_usuarios_prof) > 0) {
                            ?>
                            <td align="left">
                                <h6 class="no-margin" style="display:inline;">&nbsp;Filtros</h6>
                            </td>
                            <td align="left">
                                <img src="../imagenes/add_elemento.png" class="img_button no-margin" title="Agregar filtros" style="padding:3px 0 0 0;" onclick="abrir_cerrar_filtros_hc_ext();" />
                            </td>
                            <?php
                        }
                        ?>
                    </tr>
                    <?php
                    if (count($lista_tipos_reg) > 0 || count($lista_usuarios_prof) > 0) {
                        ?>
                        <tr style="height:7px;"></tr>
                        <tr>
                            <td align="left" colspan="3">
                                <div id="d_filtros_hc" style="display:none;">
                                    <fieldset style="width:85%; margin:auto; padding:1.25rem;">
                                        <input type="hidden" id="hdd_cant_tipos_reg_filtros" value="<?php echo(count($lista_tipos_reg)); ?>" />
                                        <?php
                                        if (count($lista_tipos_reg) > 0) {
                                            ?>
                                            <table border="0" style="width:100%;">
                                                <tr>
                                                    <td align="left" style="width:50%;">
                                                        <h6 class="no-margin"><b>Tipos de registros:</b></h6>
                                                    </td>
                                                    <td align="left" style="width:50%;">
                                                        <input type="checkbox" id="chk_tipo_reg_todos" class="no-margin" checked="checked" onchange="seleccionar_fitro_tipo_reg_ext('todos');" />
                                                        <b>VER TODOS</b>
                                                    </td>
                                                    <td align="left" style="width:33%;"></td>
                                                </tr>
                                                <tr style="height:10px;"></tr>
                                                <?php
                                                $i = 0;
                                                foreach ($lista_tipos_reg as $id_tipo_reg_aux => $nombre_tipo_reg_aux) {
                                                    if ($i % 2 == 0) {
                                                        ?>
                                                        <tr>
                                                            <?php
                                                        }
                                                        ?>
                                                        <td align="left">
                                                            <input type="hidden" id="hdd_tipo_reg_<?php echo($i); ?>" value="<?php echo($id_tipo_reg_aux); ?>" />
                                                            <input type="checkbox" id="chk_tipo_reg_<?php echo($i) ?>" class="no-margin" checked="checked" onchange="seleccionar_fitro_tipo_reg_ext(<?php echo($i) ?>);" />
                                                            <?php echo($nombre_tipo_reg_aux); ?>
                                                        </td>
                                                        <?php
                                                        if ($i % 2 == 1 || $i == count($lista_tipos_reg) - 1) {
                                                            ?>
                                                        </tr>
                                                        <?php
                                                    }

                                                    $i++;
                                                }
                                                ?>
                                            </table>
                                            <br /><br />
                                            <?php
                                        }
                                        ?>
                                        <input type="hidden" id="hdd_cant_usuarios_prof_filtros" value="<?php echo(count($lista_usuarios_prof)); ?>" />
                                        <?php
                                        if (count($lista_usuarios_prof) > 0) {
                                            ?>
                                            <table border="0" style="width:100%;">
                                                <tr>
                                                    <td align="left" style="width:50%;">
                                                        <h6 class="no-margin"><b>Profesionales:</b></h6>
                                                    </td>
                                                    <td align="left" style="width:50%;">
                                                        <input type="checkbox" id="chk_usuario_prof_todos" class="no-margin" checked="checked" onchange="seleccionar_fitro_usuario_prof_ext('todos');" />
                                                        <b>VER TODOS</b>
                                                    </td>
                                                    <td align="left" style="width:33%;"></td>
                                                </tr>
                                                <tr style="height:10px;"></tr>
                                                <?php
                                                $i = 0;
                                                foreach ($lista_usuarios_prof as $id_usuario_prof_aux => $nombre_usuario_prof_aux) {
                                                    if ($i % 2 == 0) {
                                                        ?>
                                                        <tr>
                                                            <?php
                                                        }
                                                        ?>
                                                        <td align="left">
                                                            <input type="hidden" id="hdd_usuario_prof_<?php echo($i); ?>" value="<?php echo($id_usuario_prof_aux); ?>" />
                                                            <input type="checkbox" id="chk_usuario_prof_<?php echo($i) ?>" class="no-margin" checked="checked" onchange="seleccionar_fitro_usuario_prof_ext(<?php echo($i) ?>);" />
                                                            <?php echo($nombre_usuario_prof_aux); ?>
                                                        </td>
                                                        <?php
                                                        if ($i % 2 == 1 || $i == count($lista_usuarios_prof) - 1) {
                                                            ?>
                                                        </tr>
                                                        <?php
                                                    }

                                                    $i++;
                                                }
                                                ?>
                                            </table>
                                            <br />
                                            <?php
                                        }
                                        ?>
                                    </fieldset>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                <br />
                <input type="hidden" id="hdd_cant_registros_hc" value="<?php echo(count($tabla_registro_hc_aux)); ?>" />
                <table class="modal_table" style="width:98%;" >
                    <thead>
                        <tr>
                            <th class="th_reducido" align="center" style="width:1%;"></th>
                            <th class="th_reducido" align="center" style="width:15%;">Fecha</th>
                            <th class="th_reducido" align="center" style="width:83%;">Tipo de registro</th>
                            <th class="th_reducido" align="center" style="width:1%;"></th>
                        </tr>
                    </thead>
                    <?php
                    if (count($tabla_registro_hc_aux) > 0) {
                        $tabla_registro_hc = array();
                        for ($i = count($tabla_registro_hc_aux) - 1; $i >= 0; $i--) {
                            $reg_aux = $tabla_registro_hc_aux[$i];
                            array_push($tabla_registro_hc, $reg_aux);
                        }

                        //Se reordena la historia clínica de forma descendente
                        $i = 0;
                        foreach ($tabla_registro_hc as $fila_registro_hc) {
                            $id_paciente = $fila_registro_hc['id_paciente'];
                            $nombre_1 = $fila_registro_hc['nombre_1'];
                            $nombre_2 = $fila_registro_hc['nombre_2'];
                            $apellido_1 = $fila_registro_hc['apellido_1'];
                            $apellido_2 = $fila_registro_hc['apellido_2'];
                            $nombre_persona = $personas->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
                            $id_admision = $fila_registro_hc['id_admision'];
                            $pagina_consulta = $fila_registro_hc['pagina_menu'];

                            $id_hc = $fila_registro_hc['id_hc'];
                            $id_tipo_reg = $fila_registro_hc['id_tipo_reg'];
                            $id_clase_reg = $fila_registro_hc["id_clase_reg"];
                            $nombre_tipo_reg = $fila_registro_hc['nombre_tipo_reg'];
                            if ($fila_registro_hc["nombre_alt_tipo_reg"] != "") {
                                $nombre_tipo_reg .= " (" . $fila_registro_hc["nombre_alt_tipo_reg"] . ")";
                            }
                            $fecha_hc = $personas->obtenerFecha6($fila_registro_hc['fecha_hora_hc_t']);
                            $estado_hc = $fila_registro_hc['ind_estado'];

                            //Usuario del registro
                            $nombre_usuario_prof = "";
                            if ($id_clase_reg != "4" && $id_clase_reg != "5") {
                                $nombre_usuario_prof = trim($fila_registro_hc["nombre_usuario"] . " " . $fila_registro_hc["apellido_usuario"]);
                                if ($fila_registro_hc["ind_anonimo"] != "0" && $fila_registro_hc["nombre_usuario_alt"] != "") {
                                    $nombre_usuario_prof .= " (" . $fila_registro_hc["nombre_usuario_alt"] . ")";
                                }
                            }

                            if ($estado_hc == 1) {
                                $img_estado = "<img src='../imagenes/icon-convencion-no-disponible.png' />";
                            } else if ($estado_hc == 2) {
                                $img_estado = "<img src='../imagenes/icon-convencion-disponible.png' />";
                            }

                            if ($id_tipo_reg == 17) { // Si es HC fisica
                                if ($fila_registro_hc["ruta_arch_adjunto"] != "") {
                                    $ruta_hc_new = str_replace('\\', '/', $fila_registro_hc["ruta_arch_adjunto"]);
                                } else {
                                    $tabla_ruta_hc = $dbVariables->getVariable(11);
                                    $tabla_hc_fisica = $dbHistoriaClinica->getHistoriaFisica($id_hc);
                                    $ruta_hc = $tabla_ruta_hc['valor_variable'];
                                    $archivo_hc = $tabla_hc_fisica['archivo_hc'];
                                    $ruta_hc_new = str_replace('\\', '/', $ruta_hc) . '/' . $archivo_hc;
                                }

                                //Se crea una copia local del archivo a mostrar
                                if ($ruta_hc_new != "") {
                                    $extension_arch = strtolower($utilidades->get_extension_arch($ruta_hc_new));
                                    $ruta_hc_new = str_replace("../imagenes/imagenes_hce", $ruta_base, $ruta_hc_new);
                                    @copy($ruta_hc_new, $ruta_tmp . "/hc_antigua_" . $id_hc . "." . $extension_arch);
                                    $ruta_hc_new = $ruta_tmp . "/hc_antigua_" . $id_hc . "." . $extension_arch;
                                }
                                ?>
                                <tr id="tr_registro_hc_<?php echo($i); ?>">
                                    <td class="td_reducido" align="center">
                                        <input type="hidden" id="hdd_tipo_reg_hc_<?php echo($i); ?>" value="<?php echo($fila_registro_hc["id_tipo_reg"]); ?>" />
                                        <input type="hidden" id="hdd_usuario_prof_hc_<?php echo($i); ?>" value="<?php echo($fila_registro_hc["id_usuario_reg"]); ?>" />
                                        <img src="../imagenes/imprimir_hc.png" title="Imprimir" />
                                    </td>
                                    <td class="td_reducido" align="left"><?php echo($fecha_hc); ?></td>
                                    <td class="td_reducido" align="left">
                                        <a href="../historia_clinica/abrir_pdf.php?ruta=<?php echo($ruta_hc_new); ?>" target="_blank">
                                            <?php
                                            echo($nombre_tipo_reg);
                                            ?>
                                        </a>
                                    </td>
                                    <td class="td_reducido" align="center"><?php echo($img_estado); ?></td>
                                </tr>
                                <?php
                            } else {
                                ?>
                                <tr id="tr_registro_hc_<?php echo($i); ?>">
                                    <td class="td_reducido" align="center">
                                        <input type="hidden" id="hdd_tipo_reg_hc_<?php echo($i); ?>" value="<?php echo($fila_registro_hc["id_tipo_reg"]); ?>" />
                                        <input type="hidden" id="hdd_usuario_prof_hc_<?php echo($i); ?>" value="<?php echo($fila_registro_hc["id_usuario_reg"]); ?>" />
                                        <img src="../imagenes/imprimir_hc.png" title="Imprimir" onclick="imprimir_reg_hc(<?php echo($id_hc); ?>);" />
                                    </td>
                                    <td class="td_reducido" align="left" onclick="mostrar_consultas_div(<?php echo($id_paciente); ?>, '<?php echo($nombre_persona); ?>', <?php echo(intval($id_admision, 10)); ?>, '<?php echo($pagina_consulta); ?>', <?php echo($id_hc); ?>, <?php echo($credencial); ?>, <?php echo($id_menu); ?>);">
                                        <?php echo($fecha_hc); ?>
                                    </td>
                                    <td class="td_reducido" align="left" onclick="mostrar_consultas_div(<?php echo($id_paciente); ?>, '<?php echo($nombre_persona); ?>', <?php echo(intval($id_admision, 10)); ?>, '<?php echo($pagina_consulta); ?>', <?php echo($id_hc); ?>, <?php echo($credencial); ?>, <?php echo($id_menu); ?>);">
                                        <?php
                                        $texto_aux = $nombre_tipo_reg;
                                        if (trim($nombre_usuario_prof) != "") {
                                            $texto_aux .= " - " . $nombre_usuario_prof;
                                        }
                                        echo($texto_aux);

                                        if (isset($mapa_examenes_paciente[$id_hc])) {
                                            foreach ($mapa_examenes_paciente[$id_hc] as $examen_aux) {
                                                echo("<br />&nbsp;&nbsp;-&nbsp;" . $examen_aux["nombre_examen"]);
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td class="td_reducido" align="center"><?php echo($img_estado); ?></td>
                                </tr>
                                <?php
                            }
                            $i++;
                        }
                    } else {
                        //Si no se encontraron registros de historia clinica
                        ?>
                        <tr>
                            <td colspan="4">
                                <div class="msj-vacio">
                                    <p>No hay HC para este paciente</p>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
        </div> 
        <?php
    }

    /**
     * Funcion para validar si existe un a session o si la sesion expiro
     * $ajax= 1 : SI entro por ajax
     * $ajax= 0 : NO entro por ajax
     */
    public function validar_seguridad($ajax) {
        $id_usuario = $_SESSION["idUsuario"];
        @$credencial = $_REQUEST["credencial"];
        @$id_menu = $_REQUEST["hdd_numero_menu"];

        if (isset($_SESSION["idUsuario"])) {
            if ($credencial == '' && $ajax == 0) {
                $credencial = $id_usuario;
                //Se crear la variable para continuar con la credencial activa
                ?>
                <form name="frm_credencial" id="frm_credencial" method="post" action="../principal/principal.php">
                    <input type="hidden" name="credencial" id="credencial" value="<?php echo($credencial); ?>" />
                    <input type="hidden" name="hdd_numero_menu" id="hdd_numero_menu" value="<?php echo($id_menu); ?>" />
                </form>
                <script type="text/javascript">
                    document.frm_credencial.submit();
                </script>
                <?php
            } else if ($credencial == '' && $ajax == 1) {
                header("Location: ../principal/sesion_finalizada.html");
            } else if (($credencial == '' && $id_usuario == '') || ($credencial != $id_usuario)) {
                header("Location: ../principal/sesion_finalizada.html");
            } else if (($credencial == $id_usuario) && ($ajax == 0)) {
                ?>
                <form name="frm_credencial" id="frm_credencial" method="post" action="">
                    <input type="hidden" name="credencial" id="credencial" value="<?php echo($credencial); ?>" />
                    <input type="hidden" name="hdd_numero_menu" id="hdd_numero_menu" value="<?php echo($id_menu); ?>" />
                </form>
                <?php
            }
        } else {
            header("Location: ../principal/sesion_finalizada.html");
        }
    }

    /**
     * Funcion para validar si existe un a session o si la sesion expiro
     * $ajax= 1 : SI entro por ajax
     * $ajax= 0 : NO entro por ajax
     */
    public function validar_seguridad_resultados($ajax) {
        $id_usuario = $_SESSION["idUsuario"];
        @$credencial = $_REQUEST["credencial"];
        @$id_menu = $_REQUEST["hdd_numero_menu"];

        if (isset($_SESSION["idUsuario"])) {
            if ($credencial == '' && $ajax == 0) {
                $credencial = $id_usuario;
                //Se crear la variable para continuar con la credencial activa
                ?>
                <form name="frm_credencial" id="frm_credencial" method="post" action="../resultados/resultados.php">
                    <input type="hidden" name="credencial" id="credencial" value="<?php echo($credencial); ?>" />
                    <input type="hidden" name="hdd_numero_menu" id="hdd_numero_menu" value="<?php echo($id_menu); ?>" />
                </form>
                <script type="text/javascript">
                    document.frm_credencial.submit();
                </script>
                <?php
            } else if ($credencial == '' && $ajax == 1) {
                header("Location: ../principal/sesion_finalizada_resultados.html");
            } else if (($credencial == '' && $id_usuario == '') || ($credencial != $id_usuario)) {
                header("Location: ../principal/sesion_finalizada_resultados.html");
            } else if (($credencial == $id_usuario) && ($ajax == 0)) {
                ?>
                <form name="frm_credencial" id="frm_credencial" method="post" action="">
                    <input type="hidden" name="credencial" id="credencial" value="<?php echo($credencial); ?>" />
                    <input type="hidden" name="hdd_numero_menu" id="hdd_numero_menu" value="<?php echo($id_menu); ?>" />
                </form>
                <?php
            }
        } else {
            header("Location: ../principal/sesion_finalizada_resultados.html");
        }
    }

    function obtener_permisos_menu($id_menu) {
        $arr_accesos_usuario = $_SESSION["accesos_usuario"];
        $tipo_acceso = 0;
        if (isset($arr_accesos_usuario[$id_menu])) {
            $tipo_acceso = $arr_accesos_usuario[$id_menu];
        }

        return $tipo_acceso;
    }

}
?>
