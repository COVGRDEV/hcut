<?php
session_start();
/*
  Pagina para consultar HC de las pacientes
  Autor: Helio Ruber López - 30/03/2014
 */

header("Content-Type: text/xml; charset=UTF-8");

require_once("../db/DbHistoriaClinica.php");
require_once("../db/DbExamenesOptometria.php");
require_once("../db/DbCirugias.php");
require_once("../db/DbPacientes.php");
require_once("../db/DbVariables.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../funciones/FuncionesPersona.php");
require_once("../funciones/Utilidades.php");
require_once("../principal/ContenidoHtml.php");
require_once("FuncionesHistoriaClinica.php");

$dbHistoriaClinica = new DbHistoriaClinica();
$dbPacientes = new DbPacientes();

$combo = new Combo_Box();
$funciones_persona = new FuncionesPersona();
$utilidades = new Utilidades();
$contenido = new ContenidoHtml();
$contenido->validar_seguridad(1);
$funciones_hc = new FuncionesHistoriaClinica();

$opcion = $utilidades->str_decode($_REQUEST["opcion"]);

function ver_historia_clinica($id_paciente, $nombre_persona, $documento_persona, $tipo_documento, $telefonos, $fecha_nacimiento, $edad_paciente) {
    $dbHistoriaClinica = new DbHistoriaClinica();
    $dbExamenesOptometria = new DbExamenesOptometria();
    $dbCirugias = new DbCirugias();
    $dbVariables = new Dbvariables();
	$dbPacientes = new DbPacientes();
	
    $funciones_persona = new FuncionesPersona();
    $utilidades = new Utilidades();
    $contenido = new ContenidoHtml();
    $tipo_acceso_menu = $contenido->obtener_permisos_menu($utilidades->str_decode($_POST["hdd_numero_menu"]));

    $id_usuario = $_SESSION["idUsuario"];
    @$credencial = $utilidades->str_decode($_POST["credencial"]);
    @$id_menu = $utilidades->str_decode($_POST["hdd_numero_menu"]);

    //Se borran las imágenes temporales creadas por el usuario actual
    $ruta_tmp = "../historia_clinica/tmp/" . $id_usuario;

    @mkdir($ruta_tmp);

    //Se obtiene la ruta actual de las imágenes
    $arr_ruta_base = $dbVariables->getVariable(17);
    $ruta_base = $arr_ruta_base["valor_variable"];

    //Se inserta el registro de ingreso a la historia clínica
    $dbHistoriaClinica->crear_ingreso_hc($id_usuario, $id_paciente, "", "", 161);

    $tabla_registro_hc = $dbHistoriaClinica->getRegistrosHistoriaClinica($id_paciente);
	
	foreach ($tabla_registro_hc as &$valor) {
		$fecha_hc_t = $valor["fecha_hc_t"];
		$fecha_hc_t =  explode("/",$fecha_hc_t);
		$dia = $fecha_hc_t[0];
		$mes = $fecha_hc_t[1];
		$anyo = $fecha_hc_t[2];
		$fecha_hc_t = ($anyo."-".$mes."-".$dia);
		
		$datos_paciente_aux_hc = $dbPacientes->getEdadPaciente($id_paciente, $fecha_hc_t);
		$edad_paciente_aux_hc = $datos_paciente_aux_hc["edad"];
		
	} 
		$edad_paciente_aux_hc = $edad_paciente_aux_hc;

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
    ?>
    <fieldset style="width:65%; margin: auto;">
        <legend>Datos del paciente:</legend>
        <table style="width:99%; margin:auto; font-size:10pt;">
            <tr>
                <td align="right" style="width:50%">Tipo de documento:</td>
                <td align="left" style="width:50%"><b><?php echo($tipo_documento); ?></b></td>
            </tr>
            <tr>
                <td align="right">N&uacute;mero de identificaci&oacute;n:</td>
                <td align="left"><b><?php echo($documento_persona); ?></b></td>
            </tr>
            <tr>
                <td align="right">Nombre completo:</td>
                <td align="left"><b><?php echo($nombre_persona); ?></b></td>
            </tr>
            <tr>
                <td align="right">Fecha de nacimiento:</td>
                <td align="left"><b><?php echo($funciones_persona->obtenerFecha6($fecha_nacimiento)); ?></b></td>
            </tr>
            <tr>
                <td align="right">Edad:</td>
                <td align="left"><b><?php echo($edad_paciente != "" ? $edad_paciente . " a&ntilde;os" : ""); ?></b></td>
            </tr>
            <tr>
                <td align="right">Tel&eacute;fono(s):</td>
                <td align="left"><b><?php echo($telefonos); ?></b></td>
            </tr>
            <?php
            //Se construyen los filtros para la HC actual
            $lista_tipos_reg = array();
            $lista_usuarios_prof = array();

            foreach ($tabla_registro_hc as $registro_hc_aux) {
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

            if (count($lista_tipos_reg) > 0 || count($lista_usuarios_prof) > 0) {
                ?>
                <tr>
                    <td align="right">Filtros:</td>
                    <td align="left">
                        <img src="../imagenes/add_elemento.png" class="img_button no-margin" title="Agregar filtros" onclick="abrir_cerrar_filtros_hc();" />
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="2">
                        <div id="d_filtros_hc" style="display:none;">
                            <fieldset style="width:95%; margin:auto; padding:1.25rem;">
                                <input type="hidden" id="hdd_cant_tipos_reg_filtros" value="<?php echo(count($lista_tipos_reg)); ?>" />
                                <?php
                                if (count($lista_tipos_reg) > 0) {
                                    ?>
                                    <table border="0" style="width:100%;">
                                        <tr>
                                            <td align="left" style="width:33%;">
                                                <h6 class="no-margin"><b>Tipos de registros:</b></h6>
                                            </td>
                                            <td align="left" style="width:34%;">
                                                <input type="checkbox" id="chk_tipo_reg_todos" class="no-margin" checked="checked" onchange="seleccionar_fitro_tipo_reg('todos');" />
                                                <b>VER TODOS</b>
                                            </td>
                                            <td align="left" style="width:33%;"></td>
                                        </tr>
                                        <tr style="height:10px;"></tr>
                                        <?php
                                        $i = 0;
                                        foreach ($lista_tipos_reg as $id_tipo_reg_aux => $nombre_tipo_reg_aux) {
                                            if ($i % 3 == 0) {
                                                ?>
                                                <tr>
                                                    <?php
                                                }
                                                ?>
                                                <td align="left">
                                                    <input type="hidden" id="hdd_tipo_reg_<?php echo($i); ?>" value="<?php echo($id_tipo_reg_aux); ?>" />
                                                    <input type="checkbox" id="chk_tipo_reg_<?php echo($i) ?>" class="no-margin" checked="checked" onchange="seleccionar_fitro_tipo_reg(<?php echo($i) ?>);" />
                                                    <?php echo($nombre_tipo_reg_aux); ?>
                                                </td>
                                                <?php
                                                if ($i % 3 == 2 || $i == count($lista_tipos_reg) - 1) {
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
                                            <td align="left" style="width:33%;">
                                                <h6 class="no-margin"><b>Profesionales:</b></h6>
                                            </td>
                                            <td align="left" style="width:34%;">
                                                <input type="checkbox" id="chk_usuario_prof_todos" class="no-margin" checked="checked" onchange="seleccionar_fitro_usuario_prof('todos');" />
                                                <b>VER TODOS</b>
                                            </td>
                                            <td align="left" style="width:33%;"></td>
                                        </tr>
                                        <tr style="height:10px;"></tr>
                                        <?php
                                        $i = 0;
                                        foreach ($lista_usuarios_prof as $id_usuario_prof_aux => $nombre_usuario_prof_aux) {
                                            if ($i % 3 == 0) {
                                                ?>
                                                <tr>
                                                    <?php
                                                }
                                                ?>
                                                <td align="left">
                                                    <input type="hidden" id="hdd_usuario_prof_<?php echo($i); ?>" value="<?php echo($id_usuario_prof_aux); ?>" />
                                                    <input type="checkbox" id="chk_usuario_prof_<?php echo($i) ?>" class="no-margin" checked="checked" onchange="seleccionar_fitro_usuario_prof(<?php echo($i) ?>);" />
                                                    <?php echo($nombre_usuario_prof_aux); ?>
                                                </td>
                                                <?php
                                                if ($i % 3 == 2 || $i == count($lista_usuarios_prof) - 1) {
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
            <tr>
                <td align="center" colspan="2">
                    <div id="d_btn_impr_completa_1" style="display:block; height:20px;">
                        <input type="button" id="btn_historia_completa" value="Ver Historia Completa" class="btnPrincipal peq" onclick="imprimir_hc_completa(<?php echo($id_paciente); ?>);" />
                        &nbsp;
                        <input type="button" id="btn_historia_resumen" value="Ver Resumen" class="btnPrincipal peq" onclick="imprimir_hc_resumen(<?php echo($id_paciente); ?>);" />
                        &nbsp;
                        <input type="button" id="btn_imagenes_hc" value="Ver Im&aacute;genes" class="btnPrincipal peq" onclick="ver_imagenes_hc(<?php echo($id_paciente); ?>, 0, 65);" />
                    </div>
                    <div id="d_btn_impr_completa_2" style="display:none; height:20px;">
                        <img src="../imagenes/ajax-loader.gif" />
                    </div>
                </td>
            </tr>
        </table>
    </fieldset>
    <div style="width:70%; margin:auto; height:320px; overflow:auto;">
        <input type="hidden" id="hdd_cant_registros_hc" value="<?php echo(count($tabla_registro_hc)); ?>" />
        <table class="modal_table" style="width:99%; margin:auto;" align="left">
            <thead>
                <tr>
                    <th class="th_reducido" align="center" style="width:1%;"></th>
                    <th class="th_reducido" align="center" style="width:1%;"></th>
                    <th class="th_reducido" align="center" style="width:15%;">Fecha</th>
                    <th class="th_reducido" align="center" style="width:82%;">Tipo de registro</th>
                    <th class="th_reducido" align="center" style="width:1%;"></th>
                </tr>
            </thead>
            <?php
            if (count($tabla_registro_hc) > 0) {
                $i = 0;
                foreach ($tabla_registro_hc as $fila_registro_hc) {
					
                    $id_paciente = $fila_registro_hc["id_paciente"];
                    $nombre_1 = $fila_registro_hc["nombre_1"];
                    $nombre_2 = $fila_registro_hc["nombre_2"];
                    $apellido_1 = $fila_registro_hc["apellido_1"];
                    $apellido_2 = $fila_registro_hc["apellido_2"];
                    $nombre_persona = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
                    $id_admision = $fila_registro_hc["id_admision"];
                    $pagina_consulta = $fila_registro_hc["pagina_menu"];
                    $id_hc = $fila_registro_hc["id_hc"];
                    $id_tipo_reg = $fila_registro_hc["id_tipo_reg"];
                    $id_clase_reg = $fila_registro_hc["id_clase_reg"];
                    $nombre_tipo_reg = $fila_registro_hc["nombre_tipo_reg"];
                    if ($fila_registro_hc["nombre_alt_tipo_reg"] != "") {
                        $nombre_tipo_reg .= " (" . $fila_registro_hc["nombre_alt_tipo_reg"] . ")";
                    }

                    //Usuario del registro
                    $nombre_usuario_prof = "";
                    if ($id_clase_reg != "4" && $id_clase_reg != "5") {
                        $nombre_usuario_prof = trim($fila_registro_hc["nombre_usuario"] . " " . $fila_registro_hc["apellido_usuario"]);
                        if ($fila_registro_hc["ind_anonimo"] != "0" && $fila_registro_hc["nombre_usuario_alt"] != "") {
                            $nombre_usuario_prof .= " (" . $fila_registro_hc["nombre_usuario_alt"] . ")";
                        }
                    }

                    //Se cambia el formato de la hora
                    $fecha_hc = $funciones_persona->obtenerFecha6($fila_registro_hc["fecha_hora_hc_t"]);
                    $fecha_hc_aux = $fila_registro_hc["fecha_hora_hc_t"];
                    $estado_hc = $fila_registro_hc["ind_estado"];

                    if ($estado_hc == 1) {
                        $img_estado = "<img src='../imagenes/icon-convencion-no-disponible.png' />";
                    } else if ($estado_hc == 2) {
                        $img_estado = "<img src='../imagenes/icon-convencion-disponible.png' />";
                    }

                    if ($id_tipo_reg == 17) { // Si es HC fisica
                        if ($fila_registro_hc["ruta_arch_adjunto"] != "") {
                            $ruta_hc_new = str_replace("\\", "/", $fila_registro_hc["ruta_arch_adjunto"]);
                        } else {
                            $tabla_hc_fisica = $dbHistoriaClinica->getHistoriaFisica($id_hc);
                            $tabla_ruta_hc = $dbVariables->getVariable(11);
                            $ruta_hc = $tabla_ruta_hc["valor_variable"];
                            $ruta_hc_new = str_replace("\\", "/", $ruta_hc) . "/" . $tabla_hc_fisica["archivo_hc"];
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
                            </td>
                            <td class="td_reducido" align="center">
                                <?php
                                if ($tipo_acceso_menu == "2") {
                                    ?>
                                    <img src="../imagenes/minus-elemento.png" title="Borrar registro" width="18" onclick="confirmar_borrar_registro_hc(<?php echo($id_hc); ?>);" />
                                    <?php
                                }
                                ?>
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
                                 <input type="hidden" id="hdd_edad_paciente_hc_<?php echo($i); ?>" value="<?php echo($edad_paciente_aux_hc); ?>" />
                                <img src="../imagenes/imprimir_hc.png" title="Imprimir" onclick="imprimir_registro_hc(<?php echo($id_hc); ?>);" />
                            </td>
                            <td class="td_reducido" align="center">
                                <?php
                                if ($tipo_acceso_menu == "2" && ($fila_registro_hc["id_tipo_reg"] == "18" || $fila_registro_hc["id_tipo_reg"] == "21")) {
                                    ?>
                                    <img src="../imagenes/minus-elemento.png" title="Borrar registro" width="18" onclick="confirmar_borrar_registro_hc(<?php echo($id_hc); ?>);" />
                                    <?php
                                }
                                ?>
                            </td>
                            <td class="td_reducido" align="left" onclick="mostrar_consultas_div(<?php echo($id_paciente); ?>, '<?php echo($nombre_persona); ?>', <?php echo(intval($id_admision, 10)); ?>, '<?php echo($pagina_consulta); ?>', <?php echo($id_hc); ?>, <?php echo($credencial); ?>, <?php echo($id_menu); ?>);"><?php echo($fecha_hc); ?></td>
                            <td class="td_reducido" align="left" onclick="mostrar_consultas_div(<?php echo($id_paciente); ?>, '<?php echo($nombre_persona); ?>', <?php echo(intval($id_admision, 10)); ?>, '<?php echo($pagina_consulta); ?>', <?php echo($id_hc); ?>, <?php echo($credencial); ?>, <?php echo($id_menu); ?>);">
                                <?php
                                $texto_aux = $nombre_tipo_reg;
                                if (trim($nombre_usuario_prof) != "") {
                                    $texto_aux .= " - " . $nombre_usuario_prof;
                                }
                                echo($texto_aux);

                                if (isset($mapa_examenes_paciente[$id_hc])) {
                                    foreach ($mapa_examenes_paciente[$id_hc] as $examen_aux) {
                                        echo("<br />&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;" . $examen_aux["nombre_examen"]);
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
                    <td colspan="5">
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
    <?php
}

function copy_dir($desde, $destino, $patron = "*.*") {
    $errors = array();
    if (!is_dir($desde)) {
        $errors[] = "El directorio $desde no existe";
        return $errors;
    }

    if (!is_dir($destino)) {
        $exito = @mkdir($destino, 0777, true);
        if (!$exito) {
            $errors[] = "El directorio $destino no existe y no se pudo crear.";
            return $errors;
        }
    }
    $files = glob($desde . $patron);
    foreach ($files as $file) {
        if ($file != "..") {
            $filename = basename($file);
            if (!@copy($file, $destino . $filename)) {
                $errors[] = $filename . "no se pudo copiar en " . $destino;
            }
        }
    }
    if (empty($errors)) {
        return true;
    }
    return $errors;
}

switch ($opcion) {
    case "1": //Consultar HC del paciente	
        $txt_paciente_hc = $utilidades->str_decode($_POST["txt_paciente_hc"]);
        $id_usuario_crea = $_SESSION["idUsuario"];
        $tabla_personas = $dbHistoriaClinica->getPacientesHistoriaClinica($txt_paciente_hc);
		//var_dump($tabla_personas);
        $cantidad_datos = count($tabla_personas);
		


        if ($cantidad_datos == 1) {//Si se encontro un solo registro
            $id_paciente = $tabla_personas[0]["id_paciente"];
            $nombre_1 = $tabla_personas[0]["nombre_1"];
            $nombre_2 = $tabla_personas[0]["nombre_2"];
            $apellido_1 = $tabla_personas[0]["apellido_1"];
            $apellido_2 = $tabla_personas[0]["apellido_2"];
            $numero_documento = $tabla_personas[0]["numero_documento"];
            $tipo_documento = $tabla_personas[0]["tipo_documento"];
            $fecha_nacimiento = $tabla_personas[0]["fecha_nac_persona"];
            $telefonos = $tabla_personas[0]["telefono_1"];
            if ($tabla_personas[0]["telefono_2"] != "") {
                $telefonos .= " - " . $tabla_personas[0]["telefono_2"];
            }
            $nombres_apellidos = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
            //Edad actual del paciente
            $datos_paciente = $dbPacientes->getEdadPaciente($id_paciente, "");
            $edad_paciente = $datos_paciente["edad"];
		
			
            ver_historia_clinica($tabla_personas[0]["id_paciente"], $nombres_apellidos, $numero_documento, $tipo_documento, $telefonos, $fecha_nacimiento, $edad_paciente);
        } else if ($cantidad_datos > 1) {
            ?>
            <table id="tabla_persona_hc"  border="0" class="paginated modal_table" style="width: 70%; margin: auto;">
                <thead>
                    <tr class="headegrid">
                        <th class="headegrid" align="center" style="width:20%;">Documento</th>	
                        <th class="headegrid" align="center" style="width:80%;">Pacientes</th>
                    </tr>
                </thead>
                <?php
                foreach ($tabla_personas as $fila_personas) {
                    $id_personas = $fila_personas["id_paciente"];
                    $nombre_1 = $fila_personas["nombre_1"];
                    $nombre_2 = $fila_personas["nombre_2"];
                    $apellido_1 = $fila_personas["apellido_1"];
                    $apellido_2 = $fila_personas["apellido_2"];
                    $numero_documento = $fila_personas["numero_documento"];
                    $tipo_documento = $fila_personas["tipo_documento"];
                    $nombres_apellidos = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
                    $fecha_nacimiento = $fila_personas["fecha_nac_persona"];
                    $telefonos = $fila_personas["telefono_1"];
                    if ($fila_personas["telefono_2"] != "") {
                        $telefonos .= " - " . $fila_personas["telefono_2"];
                    }
                    $nombres_apellidos = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
				
				    //Edad del paciente	
                    $datos_paciente = $dbPacientes->getEdadPaciente($id_personas, "");
                    $edad_paciente = $datos_paciente["edad"];

					
                    ?>
                    <tr class="celdagrid" onclick="ver_registros_hc(<?php echo($id_personas); ?>, '<?php echo($nombres_apellidos); ?>', '<?php echo($numero_documento); ?>', '<?php echo($tipo_documento); ?>', '<?php echo($telefonos); ?>', '<?php echo($fecha_nacimiento); ?>', '<?php echo($edad_paciente); ?>');">
                        <td align="left"><?php echo $numero_documento; ?></td>	
                        <td align="left"><?php echo $nombres_apellidos; ?></td>

                    </tr>
                    <?php
                }
                ?>
            </table>

            <script id="ajax">
                //<![CDATA[ 
                $(function () {
                    $(".paginated", "tabla_persona_hc").each(function (i) {
                        $(this).text(i + 1);
                    });

                    $("table.paginated").each(function () {
                        var currentPage = 0;
                        var numPerPage = 5;
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


            <?php
        } else if ($cantidad_datos == 0) {
            ?>
            <div class="msj-vacio">
                <p>No se encontraron pacientes</p>
            </div>
            <?php
        }
        break;

    case "2": //Mostrar los registros de historia clínica de un paciente
        $id_persona = $utilidades->str_decode($_POST["id_persona"]);
        $nombre_persona = $utilidades->str_decode($_POST["nombre_persona"]);
        $documento_persona = $utilidades->str_decode($_POST["documento_persona"]);
        $tipo_documento = $utilidades->str_decode($_POST["tipo_documento"]);
        $telefonos = $utilidades->str_decode($_POST["telefonos"]);
        $fecha_nacimiento = $utilidades->str_decode($_POST["fecha_nacimiento"]);
        $edad_paciente = $utilidades->str_decode($_POST["edad_paciente"]);
        ver_historia_clinica($id_persona, $nombre_persona, $documento_persona, $tipo_documento, $telefonos, $fecha_nacimiento, $edad_paciente);
        break;

    case "3": //Formulario de borrado de registros de HC
        @$id_hc = $utilidades->str_decode($_POST["id_hc"]);
        ?>
        <div class="encabezado">
            <h3>Borrar registro de Historia Cl&iacute;nica</h3>
        </div>
        <div style="width:100%;">
            <div class="contenedor_error" id="d_contenedor_error_borrar"></div>
        </div>
        <table cellpadding="5" style="width:100%;">
            <tr>
                <td align="center">
                    <label>Observaciones</label>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <textarea id="txt_observaciones_hc_borrar" class="textarea_alto"></textarea>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <h5>&iquest;Est&aacute; seguro de borrar el registro de historia cl&iacute;nica?</h5>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <input type="button" class="btnPrincipal peq" value="Aceptar" onclick="borrar_registro_hc(<?php echo($id_hc); ?>)" />
                    &nbsp;&nbsp;
                    <input type="button" class="btnSecundario peq" value="Cancelar" onclick="cerrar_div_centro();" />
                </td>
            </tr>
        </table>
        <?php
        break;

    case "4": //Borrar registro de historia clínica
        $id_usuario = $_SESSION["idUsuario"];
        @$id_hc = $utilidades->str_decode($_POST["id_hc"]);
        @$observaciones_hc = $utilidades->str_decode($_POST["observaciones_hc"]);

        //Se valida si el registro es de historia clínica dado que son los únicos que pueden ser borrados
        $hc_obj = $dbHistoriaClinica->getHistoriaClinicaId($id_hc);
 ;
        //if ($hc_obj["id_clase_reg"] == "5") {
        $resultado = $dbHistoriaClinica->borrar_historia_clinica($id_hc, $observaciones_hc, $id_usuario);
        /* } else {
          $resultado = -3;
          } */
        ?>
        <input type="hidden" id="hdd_resultado_hc" value="<?php echo($resultado); ?>" />
        <?php
        break;

    case "5": //Historia clínica completa
        echo('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
        echo('<html xmlns="http://www.w3.org/1999/xhtml">');
        echo('<head>');
        echo('<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />');
        echo('<link href="../css/estilos.css" rel="stylesheet" type="text/css" />');
        echo('<link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />');
        echo('<link href="../css/azul.css" rel="stylesheet" type="text/css" />');
        echo('<link href="../css/Class_Color_Pick.css" rel="stylesheet" type="text/css" />');
        echo('<script type="text/javascript" src="../js/jquery_autocompletar.js"></script>');
        echo('<script type="text/javascript" src="../js/jquery-ui.js"></script>');
        echo('<script type="text/javascript" src="../js/ajax.js"></script>');
        echo('<script type="text/javascript" src="../js/funciones.js"></script>');
        echo('<script type="text/javascript" src="../js/validaFecha.js"></script>');
        echo('<script type="text/javascript" src="../js/Class_Diagnosticos.js"></script>');
        echo('<script type="text/javascript" src="../js/Class_Atencion_Remision_v1.1.js"></script>');
        echo('<script type="text/javascript" src="../funciones/ckeditor/ckeditor.js"></script>');
        echo('<script type="text/javascript" src="../funciones/ckeditor/config.js"></script>');
        echo('<script type="text/javascript" src="../js/Class_Color_Pick.js"></script>');
        echo('<script type="text/javascript" src="../js/jquery.textarea_autosize.js"></script>');
        echo('<script type="text/javascript" src="historia_clinica_v1.1.js"></script>');
        echo('<script type="text/javascript" src="FuncionesHistoriaClinica.js"></script>');
        echo('</head>');
        echo("<body>");
        @$id_paciente = $utilidades->str_decode($_REQUEST["id_paciente"]);
        @$credencial = $utilidades->str_decode($_REQUEST["credencial"]);
        @$id_menu = $utilidades->str_decode($_REQUEST["hdd_numero_menu"]);
        ?>
        <input type="hidden" name="credencial" id="credencial" value="<?php echo($credencial); ?>" />
        <input type="hidden" name="hdd_numero_menu" id="hdd_numero_menu" value="<?php echo($id_menu); ?>" />
        <?php
        //Se carga el encabezado del paciente
        $funciones_hc->encabezado_historia_clinica($id_paciente, 0, "", 0, false);

        //Se obtiene la historia clínica completa del paciente
        $lista_historia_clinica = $dbHistoriaClinica->getRegistrosHistoriaClinica($id_paciente);

        $cont_aux = 0;
        foreach ($lista_historia_clinica as $hc_aux) {
            ?>
            <div id="d_hc_completa_<?php echo($cont_aux); ?>"><?php echo($hc_aux["id_hc"]); ?></div>
            <script type="text/javascript">
                var params = "ind_hc_completa=1";
                llamarAjax("<?php echo($hc_aux["pagina_menu"]); ?>", params, "d_hc_completa_<?php echo($cont_aux); ?>", "");
            </script>
            <?php
            $cont_aux++;
        }
        echo("</body></html>");
        break;
}
?>
