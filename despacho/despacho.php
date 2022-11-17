<?php
session_start();

//Encabezados para evitar el caché de la página
header("Expires: Sun, 01 Jan 2014 00:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once("../db/DbVariables.php");
require_once("../db/DbAdmision.php");
require_once("../db/DbPacientes.php");
require_once("../db/DbDespacho.php");
require_once("../db/DbUsuarios.php");
require_once("../db/DbHistoriaClinica.php");
require_once("../db/DbPermisos.php");
require_once("../db/DbMenus.php");
require_once("../db/DbTiposRegistrosHc.php");
require_once("../db/DbProcedimientosCotizaciones.php");
require_once("../db/DbFormulacionHC.php");
require_once("../db/DbMaestroProcedimientos.php");
require_once("../db/DbListas.php");
require_once("../principal/ContenidoHtml.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../funciones/Utilidades.php");
require_once("../funciones/html2text.php");
require_once("../historia_clinica/FuncionesHistoriaClinica.php");
require_once("../funciones/Class_Incapacidades.php");

$dbVariables = new Dbvariables();
$dbAdmision = new DbAdmision();
$dbPacientes = new DbPacientes();
$dbDespacho = new DbDespacho();
$dbUsuarios = new DbUsuarios();
$dbHistoriaClinica = new DbHistoriaClinica();
$dbPermisos = new DbPermisos();
$dbMenus = new DbMenus();
$dbTiposRegistrosHc = new DbTiposRegistrosHc();
$dbProcedimientosCotizaciones = new DbProcedimientosCotizaciones();
$dbFormulacionHC = new DbFormulacionHC();
$dbMaestroProcedimientos = new DbMaestroProcedimientos();
$dbListas = new DbListas();

$contenidoHtml = new ContenidoHtml();
$combo = new Combo_Box();
$utilidades = new Utilidades();
$funciones_hc = new FuncionesHistoriaClinica();
$class_incapacidades = new Class_Incapacidades();

//variables
$titulo = $dbVariables->getVariable(1);
$dias_edicion = $dbVariables->getVariable(12);

//Cambiar las variables get a post
$utilidades->get_a_post();

//Identificador del menú de despacho
$id_menu = 39;

$bol_confirma_bloqueo = false;

function ajustar_texto_despacho($texto) {
    //Se verifica si el texto trae formato HTML
    $pos = strpos($texto, "<p>");
    if ($pos !== false) {
        @$h2t = new html2text($texto);
        @$texto = $h2t->get_text();
    }

    return $texto;
}

function quitar_espacios_formato($texto) {
    //Se verifica si el texto trae formato HTML
    $pos = strpos($texto, "<p>");
    if ($pos !== false) {
        $texto = str_replace(chr(13), "", $texto);
        $texto = str_replace(chr(10), "", $texto);
    }

    return $texto;
}

function unir_textos($arr_textos) {
    $texto_rta = "";
    foreach ($arr_textos as $texto_aux) {
        $texto_aux = quitar_espacios_formato($texto_aux);
        $pos = strpos($texto_aux, "<p>");
        if ($texto_aux != "" && $texto_rta != "" && $pos === false) {
            $texto_rta .= "<br />";
        }
        $texto_rta .= $texto_aux;
    }

    $pos_aux = strpos($texto_rta, chr(13));
    if ($pos_aux !== false) {
        $texto_rta = str_replace(chr(13), "<br />", $texto_rta);
        $texto_rta = str_replace(chr(10), "", $texto_rta);
    } else {
        $texto_rta = str_replace(chr(10), "<br />", $texto_rta);
    }

    return $texto_rta;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo($titulo["valor_variable"]); ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
        <link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
        <link href="../css/azul.css" rel="stylesheet" type="text/css" />
        <link href="../css/Class_Color_Pick.css" rel="stylesheet" type="text/css" />
        <link href="../css/foundation-datepicker.css" rel="stylesheet" type="text/css" />

        <!--Para autocompletar DEBE IR DE PRIMERO-->
        <script type="text/javascript" src="../js/jquery_autocompletar.js"></script>
        <script type="text/javascript" src="../js/jquery-ui.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="../js/funciones.js"></script>
        <script type="text/javascript" src="../js/ajax.js"></script>
        <script type="text/javascript" src="../js/Class_Color_Pick.js"></script>
        <script type="text/javascript" src="../funciones/ckeditor/ckeditor.js"></script>
        <script type="text/javascript" src="../funciones/ckeditor/config.js"></script>
        <script type="text/javascript" src="../historia_clinica/FuncionesHistoriaClinica.js"></script>
        <script type='text/javascript' src='../js/foundation-datepicker.js'></script>
        <script type="text/javascript" src="../js/validaFecha.js"></script>
        <script type="text/javascript" src="../js/moment.js"></script>
        <script type="text/javascript" src="despacho_v1.16.js"></script>
        <script type="text/javascript"  src="../js/sweetalert2.all.min.js"></script>  
    </head>
    
    <body onload="ajustar_textareas();">
        <?php
        $contenidoHtml->validar_seguridad(0);
        if (!isset($_POST["tipo_entrada"])) {
            $contenidoHtml->cabecera_html();
        }

        if (isset($_POST["hdd_id_paciente"])) {
            $id_paciente = $utilidades->str_decode($_POST["hdd_id_paciente"]);
            $nombre_paciente = $utilidades->str_decode($_POST["hdd_nombre_paciente"]);
            $id_admision = $utilidades->str_decode($_POST["hdd_id_admision"]);
            $id_usuario = $_SESSION["idUsuario"];

            //Se halla el tipo de permiso que tiene el usuario para la ventana
            $permiso_obj = $dbPermisos->getPermisoUsuarioMenu($id_usuario, $id_menu);

            //Se obtienen los datos de la admision
            $admision_obj = $dbAdmision->get_admision($id_admision);
            $id_usuario_prof = $admision_obj["id_usuario_prof"];
            $fecha_admision = $admision_obj["fecha_admision_t"];
            $nombre_convenio = $admision_obj["nombre_convenio"];
            $nombre_plan = $admision_obj["nombre_plan"];

            //Se obtiene las hc que se le realizaron segun la admision
            $tabla_hc_admisiones = $dbDespacho->getHcAdmisiones($id_admision);

            //Edad del paciente
            $datos_paciente = $dbPacientes->getEdadPaciente($id_paciente, "");
            $edad_paciente = $datos_paciente["edad"];
            $documento_paciente = $datos_paciente["numero_documento"];

            //Nombre del profesional que atiende la consulta
            $tabla_usuario_profesional = $dbUsuarios->getUsuario($id_usuario_prof);
            $nombre_usuario_profesional = $tabla_usuario_profesional["nombre_usuario"] . " " . $tabla_usuario_profesional["apellido_usuario"];

            //Nombre del profesional que atiende la consulta de Optometria
            $profesional_optometra = $dbAdmision->get_profesional_optometra($id_admision);

            if (count($profesional_optometra) > 0) {
                $nombre_profesional_optometra = $profesional_optometra["profesional_optometra"];
            } else {
                $nombre_profesional_optometra = "";
            }

            $text_despacho = "";
            $formula_gafas = 0;
			$ind_incapacidad = 0;
            $ind_formula_gafas = "0";
            $formulacion_med = "";
            $solicitud_proc = "";
            //Se recorre los datos de las hc realizadas para obtener el tipo de registro
            foreach ($tabla_hc_admisiones as $fila_hc_admisiones) {
                $id_tipo_reg = $fila_hc_admisiones["id_tipo_reg"];
                $id_hc = $fila_hc_admisiones["id_hc"];

                switch ($id_tipo_reg) {
                    case "1": //CONSULTA DE OPTOMETRIA
                        $formula_gafas = 1;
						
                        $tabla_ooptometria = $dbDespacho->getOptometria($id_hc);
						$fg_id_hc = $tabla_ooptometria["id_hc"];
                        $fg_esfera_od = $tabla_ooptometria["refrafinal_esfera_od"];
                        $fg_cilindro_od = $tabla_ooptometria["refrafinal_cilindro_od"];
                        $fg_eje_od = $tabla_ooptometria["refrafinal_eje_od"];
                        $fg_adicion_od = $tabla_ooptometria["refrafinal_adicion_od"];
                        $fg_esfera_oi = $tabla_ooptometria["refrafinal_esfera_oi"];
                        $fg_cilindro_oi = $tabla_ooptometria["refrafinal_cilindro_oi"];
                        $fg_eje_oi = $tabla_ooptometria["refrafinal_eje_oi"];
                        $fg_adicion_oi = $tabla_ooptometria["refrafinal_adicion_oi"];
                        $fg_observaciones = $tabla_ooptometria["observaciones_rxfinal"];
                        $fg_tipo_lente = $tabla_ooptometria["tipo_lente"];
						$tipo_impresion = 1;

                        $text_despacho = $tabla_ooptometria["diagnostico_optometria"];
                        break;

                    case "2": //CONSULTA DE OFTALMOLOGIA
                    case "50": //CONSULTA DE OFTALMOLOGIA PEDIÁTRICA
						$ind_incapacidad = 1;
                        $tabla_oftalmologia = $dbDespacho->getOftalmologia($id_hc);
                        $solicitud_examenes = $tabla_oftalmologia["solicitud_examenes"];
                        $tratamiento_oftalmo = $tabla_oftalmologia["tratamiento_oftalmo"];
                        $medicamentos_oftalmo = $tabla_oftalmologia["medicamentos_oftalmo"];

                        $text_despacho = unir_textos(array($solicitud_examenes, $tratamiento_oftalmo, $medicamentos_oftalmo, $text_despacho));
                        $ind_formula_gafas = $tabla_oftalmologia["ind_formula_gafas"];
                        break;

                    case "4": //PROCEDIMIENTO QUIRÚRGICO
                        break;

                    case "5": //CONSULTA PREQUIRÚRGICA DE CATARATA
						$ind_incapacidad = 1;
                        $tabla_preqx_catarata = $dbDespacho->getConsultaPreqxCatarata($id_hc);
                        $solicitud_examenes_preqx_catarata = $tabla_preqx_catarata["solicitud_examenes_preqx_catarata"];
                        $tratamiento_preqx_catarata = $tabla_preqx_catarata["tratamiento_preqx_catarata"];
                        $medicamentos_preqx_catarata = $tabla_preqx_catarata["medicamentos_preqx_catarata"];

                        $text_despacho = unir_textos(array($solicitud_examenes_preqx_catarata, $tratamiento_preqx_catarata, $medicamentos_preqx_catarata, $text_despacho));
                        break;

                    case "6": //CONSULTA PREQUIRÚRGICA LÁSER (OPTOMETRÍA)
                        break;

                    case "7": //CONSULTA PREQUIRÚRGICA LÁSER (OFTALMOLOGÍA)
						$ind_incapacidad = 1;
                        $tabla_preqx_laser_of = $dbDespacho->getConsultaPreqxLaserOf($id_hc);
                        $solicitud_examenes_preqx_laser = $tabla_preqx_laser_of["solicitud_examenes_preqx_laser"];
                        $tratamiento_preqx_laser = $tabla_preqx_laser_of["tratamiento_preqx_laser"];
                        $medicamentos_preqx_laser = $tabla_preqx_laser_of["medicamentos_preqx_laser"];

                        $text_despacho = unir_textos(array($solicitud_examenes_preqx_laser, $tratamiento_preqx_laser, $medicamentos_preqx_laser, $text_despacho));
                        break;

                    case "8": //CONSULTA CONTROL LÁSER (OPTOMETRIA)
                        $formula_gafas = 1;

                        $tabla_control_laser_op = $dbDespacho->getControlLaser($id_hc);
                        $fg_esfera_od = $tabla_control_laser_op["avc_esfera_od"];
                        $fg_cilindro_od = $tabla_control_laser_op["avc_cilindro_od"];
                        $fg_eje_od = $tabla_control_laser_op["avc_eje_od"];
                        $fg_adicion_od = $tabla_control_laser_op["avcc_adicion_od"];
                        $fg_esfera_oi = $tabla_control_laser_op["avc_esfera_oi"];
                        $fg_cilindro_oi = $tabla_control_laser_op["avc_cilindro_oi"];
                        $fg_eje_oi = $tabla_control_laser_op["avc_eje_oi"];
                        $fg_adicion_oi = $tabla_control_laser_op["avcc_adicion_oi"];
                        $fg_observaciones = $tabla_control_laser_op["observaciones_avc"];
                        $fg_tipo_lente = "";
						

                        $text_despacho = $tabla_control_laser_op["diagnostico_control_laser"];
                        break;

                    case "9": //CONSULTA CONTROL LÁSER (OFTALMOLOGÍA)
						$ind_incapacidad = 1;
                        $tabla_control_oftalmologia = $dbDespacho->getControlLaserOftalmologia($id_hc);
                        $solicitud_examenes_control_laser = $tabla_control_oftalmologia["solicitud_examenes_control_laser"];
                        $tratamiento_control_laser = $tabla_control_oftalmologia["tratamiento_control_laser"];
                        $medicamentos_control_laser = $tabla_control_oftalmologia["medicamentos_control_laser"];

                        $text_despacho = unir_textos(array($solicitud_examenes_control_laser, $tratamiento_control_laser, $medicamentos_control_laser, $text_despacho));
                        $ind_formula_gafas = $tabla_control_oftalmologia["ind_formula_gafas"];
                        break;

                    case "10": //EXAMEN (OPTOMETRÍA)
                        break;

                    case "11": //PROCEDIMIENTO QUIRÚRGICO LÁSER
                        break;

                    case "19": //CONSULTA DE CONTROL (OPTOMETRÍA)
                        $formula_gafas = 1;
	

                        $tabla_control_laser_op = $dbDespacho->getControlOptometria($id_hc);
						$fg_id_hc = $tabla_control_laser_op["id_hc"];
                        $fg_esfera_od = $tabla_control_laser_op["subjetivo_esfera_od"];
                        $fg_cilindro_od = $tabla_control_laser_op["subjetivo_cilindro_od"];
                        $fg_eje_od = $tabla_control_laser_op["subjetivo_eje_od"];
                        $fg_adicion_od = $tabla_control_laser_op["subjetivo_adicion_od"];
                        $fg_esfera_oi = $tabla_control_laser_op["subjetivo_esfera_oi"];
                        $fg_cilindro_oi = $tabla_control_laser_op["subjetivo_cilindro_oi"];
                        $fg_eje_oi = $tabla_control_laser_op["subjetivo_eje_oi"];
                        $fg_adicion_oi = $tabla_control_laser_op["subjetivo_adicion_oi"];
                        $fg_observaciones = $tabla_control_laser_op["observaciones_subjetivo"];
                        $fg_tipo_lente = "";
						$tipo_impresion = 2;

                        $text_despacho = $tabla_control_laser_op["diagnostico_optometria"];
                        break;

                    case "56": //CONSULTA DERMATOLÓGICA
                        $tabla_dermatologia = $dbDespacho->getDermatologia($id_hc);
                        $solicitud_examenes_dermat = $tabla_dermatologia["solicitud_examenes"];
                        $tratamiento_dermat = $tabla_dermatologia["tratamiento_dermat"];

                        $text_despacho = unir_textos(array($solicitud_examenes_dermat, $tratamiento_dermat, $text_despacho));
                        break;

                    default:
                        $tipo_registro_hc_obj = $dbTiposRegistrosHc->getTipoRegistroHc($id_tipo_reg);
                        if ($tipo_registro_hc_obj["id_menu"] == "24") { //EVOLUCIÓN
                            $tabla_evolucion = $dbDespacho->getEvolucion($id_hc);
                            $solicitud_examenes_evolucion = $tabla_evolucion["solicitud_examenes_evolucion"];
                            $tratamiento_evolucion = $tabla_evolucion["tratamiento_evolucion"];
                            $medicamentos_evolucion = $tabla_evolucion["medicamentos_evolucion"];

                            $text_despacho = unir_textos(array($solicitud_examenes_evolucion, $tratamiento_evolucion, $medicamentos_evolucion, $text_despacho));
                            $ind_formula_gafas = $tabla_evolucion["ind_formula_gafas"];
                        }
                        break;
                }

                //Se buscan las solicitudes de procedimientos asociadas a la historia clínica
                $lista_solicitudes_proc = $dbMaestroProcedimientos->getListaHCProcedimientosSolic($id_hc);

                if (count($lista_solicitudes_proc) > 0) {
                    foreach ($lista_solicitudes_proc as $solicitud_aux) {
                        $solicitud_proc .= "<br />" . $solicitud_aux["cod_procedimiento"] . " - " . $solicitud_aux["nombre_procedimiento"];
                        if ($solicitud_aux["ojo"] != "") {
                            $solicitud_proc .= " - Ojo: " . $solicitud_aux["ojo"];
                        }
                    }
                }

                //Se buscan las formulaciones de medicamentos asociadas a la historia clínica
                $lista_formulaciones_hc = $dbFormulacionHC->getListaFormulacionHC($id_hc);

                if (count($lista_formulaciones_hc) > 0) {
                    foreach ($lista_formulaciones_hc as $formulacion_aux) {
                        if ($formulacion_med == "") {
                            $formulacion_med .= "<br />";
                        } else {
                            $formulacion_med .= "<br /><br />";
                        }
                        $formulacion_med .= $formulacion_aux["nombre_medicamento"] . " - " . $formulacion_aux["presentacion"] . " - Cantidad: " . $formulacion_aux["cantidad"] .
                                "<br />" . $formulacion_aux["dosificacion"] . " " . $formulacion_aux["unidades"] . " " . $formulacion_aux["duracion"];
                    }
                }
            }

            if ($ind_formula_gafas == "1") {
                //Se agrega el texto de entrega de fórmula de gafas
                $text_despacho = unir_textos(array($text_despacho, "<p><strong>Entregar f&oacute;rmula de gafas.</strong></p>"));
            }

            //Si hay solicitud de procedimientos, se agrega al texto de despacho
            if ($solicitud_proc != "") {
                $text_despacho .= "<p><strong>Solicitud de procedimientos y ex&aacute;menes:</strong>" . $solicitud_proc . "</p>";
            }

            //Si hay formulación, se agrega al texto de despacho
            if ($formulacion_med != "") {
                $text_despacho .= "<p><strong>Formulaci&oacute;n de medicamentos:</strong>" . $formulacion_med . "</p>";
            }

            $despacho_obj = $dbDespacho->getDespacho($id_admision);

            if (count($despacho_obj) > 0) { //Se obtiene los datos de despacho
                $tipo_accion = 2; //Editar despacho
                //Se actualiza el registro de fórmula médica
                $dbDespacho->editarDespachoFormulaMedica($despacho_obj["id_despacho"], $text_despacho, $id_usuario);

                //Se valida si el usuario entró desde la opción de iniciar despacho, de ser así, se tiene que bloquear, ya hay alguien más trabajando en el registro
                if (isset($_POST["hdd_en_atencion"]) && isset($_POST["hdd_id_estado_atencion"]) && $_POST["hdd_id_estado_atencion"] == "7") {
                    $bol_confirma_bloqueo = true;
                    //$tipo_accion = -1; //Registro bloqueado
                }
            } else { //Entre en procesos de crear Despacho
                $tipo_accion = 1; //Crear despacho
                $dbDespacho->crearEditarDespacho($id_admision, $id_paciente, $text_despacho, array(), array(), 1, 0, $id_usuario);
            }

            //Se carga nuevamente el registro
            $despacho_obj = $dbDespacho->getDespacho($id_admision);

            //Se obtiene el detalle de despacho
            $lista_despacho_det = $dbDespacho->getListaDespachoDet($id_admision);

            //Se obtienen las cotizaciones de despacho
            $lista_despacho_cotiz = $dbDespacho->getListaDespachoCotizaciones($id_admision);


            $text_despacho = $despacho_obj["formula_medica"];
            $tipo_formula = $despacho_obj["tipo_formula"];

            if ($tipo_formula == 2) {
                $check_activo = "checked";
                $input_visible = "block";
            } else {
                $check_activo = "";
                $input_visible = "none";
            }
        } else {
            $tipo_accion = 0; //Ninguna accion Error
        }

        if (!isset($_POST["tipo_entrada"])) {
            ?>
            <div class="title-bar">
                <div class="wrapper">
                    <div class="breadcrumb">
                        <ul>
                            <li class="breadcrumb_on">Despacho</li>
                        </ul>
                    </div>
                </div>
            </div>
            <?php
        }

        $funciones_hc->encabezado_historia_clinica($id_paciente, $id_admision, 0, 0, false);
        ?>
        <div class="contenedor_principal" id="id_contenedor_principal">
            <div id="guardar_despacho" style="width:100%; display:none;"></div>
            <div id="guardar_incapacidad" style="width:100%;"></div>
            <div class="contenedor_error" id="contenedor_error"></div>
            <div class="contenedor_exito" id="contenedor_exito"></div>
            <div class="formulario" id="principal_despacho" style="width: 100%; display: block;">
                <?php
                if ($tipo_accion > 0) {
                    //Se inserta el registro de ingreso a la historia clínica
                    $dbHistoriaClinica->crear_ingreso_hc($id_usuario, $id_paciente, $id_admision, "", 164);
                    ?>
                    <form id="frm_despacho" name="frm_despacho" method="post">
                        <input type="hidden" name="hdd_id_paciente" id="hdd_id_paciente" value="<?php echo($id_paciente); ?>" />
                        <input type="hidden" name="hdd_id_usuario" id="hdd_id_usuario" value="<?php echo($_SESSION["idUsuario"]); ?>" />
                        <input type="hidden" name="hdd_id_admision" id="hdd_id_admision" value="<?php echo($id_admision); ?>" />
                        <input type="hidden" name="hdd_nombre_paciente" id="hdd_nombre_paciente" value="<?php echo($nombre_paciente); ?>" />
                        <input type="hidden" name="hdd_fecha_admision" id="hdd_fecha_admision" value="<?php echo($fecha_admision); ?>" />
                        <input type="hidden" name="hdd_documento_paciente" id="hdd_documento_paciente" value="<?php echo($documento_paciente); ?>" />
                        <input type="hidden" name="hdd_id_profesional" id="hdd_id_profesional" value="<?php echo($id_usuario_prof); ?>" />
                        <input type="hidden" name="hdd_nombre_profesional" id="hdd_nombre_profesional" value="<?php echo($nombre_usuario_profesional); ?>" />
                        <input type="hidden" name="hdd_nombre_profesional_optometra" id="hdd_nombre_profesional_optometra" value="<?php echo($nombre_profesional_optometra); ?>" />
                        <?php
                        $nombre_usuario_crea = trim($despacho_obj["nombre_usuario_crea"] . " " . $despacho_obj["apellido_usuario_crea"]);
                        $nombre_usuario_mod = trim($despacho_obj["nombre_usuario_mod"] . " " . $despacho_obj["apellido_usuario_mod"]);
                        ?>
                        <table border="0" cellpadding="2" cellspacing="0" align="center" style="width:95%;">
                            <tr valign="middle">
                                <td align="center" colspan="4"></td>
                            </tr>	
                            <tr>
                                <th align="left" valign="top" style="width:65%;">
                                    <h6>
                                        <b>Profesional:</b>&nbsp;<?php echo($nombre_usuario_profesional); ?><br />
                                        <b>Opt&oacute;metra:</b>&nbsp;<?php echo($nombre_profesional_optometra); ?><br />
                                        <b>Formato Ecopetrol:</b>&nbsp;&nbsp;<input type="checkbox" name="tipo_impresion" id="tipo_impresion" value="0" <?php echo($check_activo); ?> onclick="mostrar_remitido();"><br />
                                            <b>Registrado por:</b>&nbsp;<?php echo($nombre_usuario_crea != "" ? $nombre_usuario_crea : "-"); ?>
                                            <?php
                                            if ($despacho_obj["fecha_crea_t"] != "") {
                                                echo("&nbsp;(" . $despacho_obj["fecha_crea_t"] . ")");
                                            }
                                            ?>
                                            <br />
                                            <b>&Uacute;ltima modificaci&oacute;n:</b>&nbsp;<?php echo($nombre_usuario_mod != "" ? $nombre_usuario_mod : "-"); ?>
                                            <?php
                                            if ($despacho_obj["fecha_mod_t"] != "") {
                                                echo("&nbsp;(" . $despacho_obj["fecha_mod_t"] . ")");
                                            }
                                            ?>
                                    </h6>
                                </th>
                                <th align="left" valign="top" style="width:35%;">
                                    <h6><b>Fecha:</b>&nbsp;<?php echo($fecha_admision); ?><br />
                                        <?php
                                        if (!isset($_POST["tipo_entrada"])) {
                                            ?>
                                            &nbsp;<input type="checkbox" name="chk_fecha_actual_impr" id="chk_fecha_actual_impr" value="0" style="display:none;">
                                                <?php
                                            } else {
                                                ?>
                                                <b>Imprimir con fecha actual:</b>&nbsp;&nbsp;<input type="checkbox" name="chk_fecha_actual_impr" id="chk_fecha_actual_impr" value="0">
                                                    <?php
                                                }
                                                ?>
                                                </h6>
                                                </th>
                                                </tr>
                                                </table>
                                                <div class="tabs-container">
                                                    <dl class="tabs" data-tab>
                                                        <dd class="active"><a href="#panel2-1" onclick="setTimeout(function () {
                                                                    ajustar_textareas();
                                                                }, 100);">F&Oacute;RMULAS M&Eacute;DICAS</a></dd>
                                                            <?php
                                                            if ($formula_gafas == 1) {
                                                                ?>
                                                            <dd><a href="#panel2-2" onclick="setTimeout(function () {
                                                                        ajustar_textareas();
                                                                    }, 100);">F&Oacute;RMULA DE GAFAS</a></dd>
                                                                <?php
                                                            }
                                                            ?>
                                                        <dd><a href="#panel2-3" onclick="setTimeout(function () {
                                                                    ajustar_textareas();
                                                                }, 100);">COTIZACIONES</a></dd>
                                                         <?php
														 if($ind_incapacidad == 1){
															?>
                                                            	<dd><a href="#panel2-4" onclick="setTimeout(function () {
                                                                    ajustar_textareas();
                                                                }, 100);">INCAPACIDADES</a></dd>
                                                            <?php
														 }
														 ?>
                                                       
                                                    </dl>
                                                    <div class="tabs-content">
                                                        <div class="content active" id="panel2-1">
                                                            <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:95%;">
                                                                <tr>
                                                                    <td align="center" colspan="2">
                                                                        <label><b>OBSERVACIONES DE LA CONSULTA</b></label>
                                                                        <?php
                                                                        $text_despacho_aux = ajustar_texto_despacho($text_despacho);
                                                                        ?>
                                                                        <div style="text-align:left;" class="div_marco"><?php echo($text_despacho); ?></div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="left" id="td_ver_formulas" style="width:150px;">
                                                                        <?php
                                                                        //Se verifica el número de fórmulas existentes
                                                                        $cant_formulas_aux = count($lista_despacho_det);
                                                                        if ($cant_formulas_aux == 0) {
                                                                            $cant_formulas_aux = 1;
                                                                        }
                                                                        ?>
                                                                        <input type="hidden" name="hdd_cant_formulas" id="hdd_cant_formulas" value="<?php echo($cant_formulas_aux); ?>" />
                                                                        <?php
                                                                        for ($i = 0; $i < 20; $i++) {
                                                                            ?>
                                                                            <input type="hidden" id="hdd_act_formula_<?php echo($i); ?>" value="<?php echo($i < $cant_formulas_aux ? 1 : 0); ?>" />
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                        Ver f&oacute;rmula:&nbsp;
                                                                        <select name="cmb_num_formula" id="cmb_num_formula" style="width:50px;" onchange="mostrar_formula(this.value);">
                                                                            <?php
                                                                            for ($i = 0; $i < $cant_formulas_aux; $i++) {
                                                                                ?>
                                                                                <option value="<?php echo($i); ?>"><?php echo($i + 1); ?></option>
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </td>
                                                                    <td align="left">
                                                                        <div class="agregar_alemetos" onclick="agregar_formula();" title="Agregar f&oacute;rmula"></div> 
                                                                        <div class="restar_alemetos" onclick="restar_formula();" title="Borrar f&oacute;rmula"></div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <?php
                                                            //Se verifica si se tiene permiso para hacer cambio
                                                            $ind_editar = 0;
                                                            if ($permiso_obj["tipo_acceso"] == "2") {
                                                                $ind_editar = $dbDespacho->getIndicadorEdicionFormulas($id_admision, intval($dias_edicion["valor_variable"], 10) * 24);
                                                            }

                                                            for ($i = 0; $i < 20; $i++) {
                                                                $remitido_aux = "";
                                                                $num_carnet_aux = "";
                                                                $fecha_det_aux = $fecha_admision;
                                                                $text_despacho_aux = "";
                                                                $ind_existe_det = false;
                                                                if (isset($lista_despacho_det[$i])) {
                                                                    $remitido_aux = $lista_despacho_det[$i]["remitido"];
                                                                    $num_carnet_aux = $lista_despacho_det[$i]["num_carnet"];
                                                                    $fecha_det_aux = $lista_despacho_det[$i]["fecha_det_t"];
                                                                    $text_despacho_aux = $lista_despacho_det[$i]["formula_medica"];
                                                                    $ind_existe_det = true;
                                                                } else if ($i == 0) {
                                                                    $text_despacho_aux = $text_despacho;
                                                                }
                                                                ?>
                                                                <div id="d_detalle_formula_<?php echo($i); ?>" class="div_formula">
                                                                    <div id="d_remitido_<?php echo($i); ?>" style="display:<?php echo($input_visible); ?>;">
                                                                        <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%;">
                                                                            <tr>
                                                                                <td align="right" style="width:20%;">Remitido a:</td>
                                                                                <td align="left" style="width:30%;">
                                                                                    <input type="text" name="txt_remitido_<?php echo($i); ?>" id="txt_remitido_<?php echo($i); ?>" class="input input_hc" style="width:200px;" value="<?php echo($remitido_aux); ?>" onblur="convertirAMayusculas(this);
                                                                                            trim_cadena(this);" />
                                                                                </td>
                                                                                <td align="right" style="width:20%;">Carnet No.:</td>
                                                                                <td align="left" style="width:30%;">
                                                                                    <input type="text" name="txt_num_carnet_<?php echo($i); ?>" id="txt_num_carnet_<?php echo($i); ?>" class="input input_hc" style="width:200px;" value="<?php echo($num_carnet_aux); ?>" onblur="trim_cadena(this);" />
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </div>
                                                                    <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%;">
                                                                        <tr>
                                                                            <td align="right" style="width:20%;">Fecha de la f&oacute;rmula:</td>
                                                                            <td align="left" style="width:80%;">
                                                                                <input type="text" class="input required"  name="txt_fecha_det_<?php echo($i); ?>" id="txt_fecha_det_<?php echo($i); ?>" value="<?php echo($fecha_det_aux); ?>" maxlength="10" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" style="width:120px;" />
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                    <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%;">
                                                                        <tr>
                                                                            <td colspan="2" align="right" style="width:45%;">
                                                                                <input type="text" class="input input_hc" style="width:50px; margin-right: 5px;" onkeypress="solo_numeros(event, false);" value="" name="texto_cod_formula_<?php echo($i); ?>" id="texto_cod_formula_<?php echo($i); ?>" maxlength="2" />
                                                                            </td>	
                                                                            <td colspan="2" align="left" style="width:55%;">	
                                                                                <input type="button" id="btn_cargar_formula" nombre="btn_cargar_formula" 
                                                                                       class="btnPrincipal peq" style="font-size: 10px; float: left;"  
                                                                                       value="Cargar Formula"
                                                                                       onclick="cargar_formula_id(<?php echo($i); ?>);"/>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td align="left" colspan="4" style="font-size: 12px;">
                                                                                <?php
                                                                                if (!isset($_POST["tipo_entrada"]) && !$ind_existe_det) {
                                                                                    ?>
                                                                                    <div id="text_despacho_<?php echo($i); ?>"></div>
                                                                                    <?php
                                                                                } else {
                                                                                    $text_despacho_aux = $utilidades->ajustar_texto_wysiwyg($text_despacho_aux);
                                                                                    ?>
                                                                                    <div id="text_despacho_<?php echo($i); ?>"><?php echo($text_despacho_aux); ?></div>
                                                                                    <?php
                                                                                }
                                                                                ?>
                                                                            </td>
                                                                        </tr>
                                                                        <?php
                                                                        if ($ind_editar == 1) {
                                                                            if (!isset($_POST["tipo_entrada"])) {
                                                                                ?>
                                                                                <tr>
                                                                                    <td align="center" colspan="4" style="font-size: 12px;">
                                                                                        <input type="button" id="btn_imprimir" nombre="btn_imprimir" class="btnPrincipal" value="Imprimir" onclick="guardar_despacho(2);"/>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php
                                                                            } else {
                                                                                ?>
                                                                                <tr>
                                                                                    <td align="center" colspan="4" style="font-size: 12px;">
                                                                                        <input type="button" id="btn_imprimir" nombre="btn_imprimir" class="btnPrincipal" value="Imprimir" onclick="guardar_despacho(2);"/>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php
                                                                            }
                                                                        } else {
                                                                            ?>
                                                                            <tr>
                                                                                <td align="center" colspan="4" style="font-size: 12px;">
                                                                                    <input type="button" id="btn_imprimir" nombre="btn_imprimir" class="btnPrincipal" value="Imprimir" onclick="guardar_despacho(4);"/>	
                                                                                </td>
                                                                            </tr>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </table>
                                                                </div>
                                                                <script>
        <?php
        if ($i > 0) {
            ?>
                                                                        setTimeout("ocultar_formula(<?php echo($i); ?>)", 500);
            <?php
        }
        ?>
                                                                </script>
                                                                <?php
                                                            }
                                                            ?>
                                                            <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:95%;">
                                                                <tr>
                                                                    <th colspan="4" align="center" valign="top">
                                                                        <table class="paginated modal_table" id="tabla_formulas" style="width: 100%; margin: auto;" >
                                                                            <thead>
                                                                                <tr>
                                                                                    <th class="th_reducido" align="center" style="width:15%;">C&oacute;digo</th>
                                                                                    <th class="th_reducido" align="center" style="width:90%;">F&oacute;rmula</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <?php
                                                                            $tabla_formulas = $dbDespacho->getFormulasMedicas();
                                                                            if (count($tabla_formulas) > 0) {
                                                                                foreach ($tabla_formulas as $fila_formulas) {
                                                                                    $codigo_formula = $fila_formulas["id_formula"];
                                                                                    $texto_formula = $fila_formulas["titulo_formula"];
                                                                                    $texto_formula_completo = $fila_formulas["text_formulas"];
                                                                                    ?>
                                                                                    <tr ondblclick="cargar_formula('<?php echo($codigo_formula); ?>');">
                                                                                        <td class="td_reducido" align="center"><?php echo($codigo_formula); ?></td>
                                                                                        <td class="td_reducido" align="left"><?php echo($texto_formula); ?></td>
                                                                                        <input type="hidden" value='<?php echo($utilidades->ajustar_texto_wysiwyg($texto_formula_completo)); ?>' name="hdd_formula_<?php echo($codigo_formula); ?>" id="hdd_formula_<?php echo $codigo_formula; ?>" />
                                                                                    </tr>
                                                                                    <?php
                                                                                }
                                                                            } else {
                                                                                //Si no se encontraron registros
                                                                                ?>
                                                                                <tr>
                                                                                    <td colspan="2">
                                                                                        <div class="msj-vacio">
                                                                                            <p>No hay HC para este paciente</p>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                        </table>
                                                                        <script>
                                                                            //<![CDATA[ 
                                                                            $(function () {
                                                                                $(".paginated", "tabla_formulas").each(function (i) {
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
                                                                    </th>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                        <?php
                                                        if ($formula_gafas == 1) {
                                                            ?>
                                                            <div class="content" id="panel2-2">
                                                                <div id="imprimir_formula" style="display: none;"></div>
                                                                <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:95%;">
                                                                    <tr>
                                                                        <td align="center" colspan="4">
                                                                            <label><b>IMPRIMIR F&Oacute;RMULA DE GAFAS</b></label>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                                <table id="tabla_refrafinal" class="modal_table" cellpadding="5" cellspacing="0" align="center" style="width:95%;">
                                                                    <tr>
                                                                        <td class="td_reducido"><b>Lente</b></td>
                                                                        <td class="td_reducido" colspan="4" align="left"><?php echo($fg_tipo_lente != "" ? $fg_tipo_lente : "-"); ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="td_reducido"><b>Observaciones</b></td>
                                                                        <td class="td_reducido" colspan="4" align="left"><?php echo($fg_observaciones); ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="td_reducido" style="width:16%;"><b>&nbsp;</b></td>
                                                                        <td class="td_reducido" style="width:21%;"><b>Esfera</b></td>
                                                                        <td class="td_reducido" style="width:21%;"><b>Cilindro</b></td>
                                                                        <td class="td_reducido" style="width:21%;"><b>Eje</b></td>
                                                                        <td class="td_reducido" style="width:21%;"><b>Adici&oacute;n</b></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="td_reducido"><b>OD</b></td>
                                                                        <td class="td_reducido"><?php echo($fg_esfera_od); ?></td>
                                                                        <td class="td_reducido"><?php echo($fg_cilindro_od); ?></td>
                                                                        <td class="td_reducido"><?php echo($fg_eje_od); ?></td>
                                                                        <td class="td_reducido"><?php echo($fg_adicion_od); ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="td_reducido"><b>OI</b></td>
                                                                        <td class="td_reducido"><?php echo($fg_esfera_oi); ?></td>
                                                                        <td class="td_reducido"><?php echo($fg_cilindro_oi); ?></td>
                                                                        <td class="td_reducido"><?php echo($fg_eje_oi); ?></td>
                                                                        <td class="td_reducido"><?php echo($fg_adicion_oi); ?></td>
                                                                    </tr>
                                                                    <tr>
                                                        <input type="hidden" name="hdd_id_hc_consulta" id="hdd_id_hc_consulta" value='<?php echo($fg_id_hc);?>'/>
                                                                     </tr>
                                                                    <tr>
                                                                        <td colspan="5">
                                                                            <input type="button" id="btn_imprimir_subjetivo" nombre="btn_imprimir_subjetivo" class="btnPrincipal peq" style="font-size: 16px;" value="Imprimir f&oacute;rmula" onclick="imprimir_formula_gafas('hdd_id_hc_consulta','<?php echo($tipo_impresion);?>','<?php echo($fg_esfera_od); ?>', '<?php echo($fg_cilindro_od); ?>', '<?php echo($fg_eje_od); ?>', '<?php echo($fg_adicion_od); ?>', '<?php echo($fg_esfera_oi); ?>', '<?php echo($fg_cilindro_oi); ?>', '<?php echo($fg_eje_oi); ?>', '<?php echo($fg_adicion_oi); ?>', '<?php echo($utilidades->str_encode($fg_observaciones)); ?>', '<?php echo($id_admision); ?>', '<?php echo($fg_tipo_lente); ?>');" />
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                        <!--COTIZACIONES-->
                                                        <div class="content" id="panel2-3">
                                                            <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:95%;">
                                                                <tr>
                                                                    <td align="center" colspan="2">
                                                                        <label><b>OBSERVACIONES DE LA CONSULTA</b></label>
                                                                        <?php
                                                                        $text_despacho_aux = ajustar_texto_despacho($text_despacho);
                                                                        ?>
                                                                        <div style="text-align:left;" class="div_marco"><?php echo($text_despacho); ?></div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="left" width="150px;">
                                                                        <?php
                                                                        //Se verifica el número de cotizaciones existentes
                                                                        $cant_cotizaciones_aux = count($lista_despacho_cotiz);
                                                                        if ($cant_cotizaciones_aux == 0) {
                                                                            $cant_cotizaciones_aux = 1;
                                                                        }
                                                                        ?>
                                                                        <input type="hidden" name="hdd_cant_cotizaciones" id="hdd_cant_cotizaciones" value="<?php echo($cant_cotizaciones_aux); ?>" />
                                                                        <?php
                                                                        for ($i = 0; $i < 20; $i++) {
                                                                            ?>
                                                                            <input type="hidden" id="hdd_act_cotizacion_<?php echo($i); ?>" value="<?php echo($i < $cant_cotizaciones_aux ? 1 : 0); ?>" />
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                        Ver cotizaci&oacute;n:&nbsp;
                                                                        <select name="cmb_num_cotizacion" id="cmb_num_cotizacion" style="width:50px;" onchange="mostrar_cotizacion(this.value);">
                                                                            <?php
                                                                            for ($i = 0; $i < $cant_cotizaciones_aux; $i++) {
                                                                                ?>
                                                                                <option value="<?php echo($i); ?>"><?php echo($i + 1); ?></option>
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </td>
                                                                    <td align="left">
                                                                        <div class="agregar_alemetos" onclick="agregar_cotizacion();" title="Agregar cotizaci&oacute;n"></div> 
                                                                        <div class="restar_alemetos" onclick="restar_cotizacion();" title="Borrar cotizaci&oacute;n"></div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <?php
                                                            //Se carga el listado de procedimientos para cotizaciones
                                                            $lista_procedimientos_cotiz = $dbProcedimientosCotizaciones->getListaProcedimientosCotizaciones(1);

                                                            for ($i = 0; $i < 20; $i++) {
                                                                $id_proc_cotiz_aux = "";
                                                                $valor_cotiz_aux = "";
                                                                $observaciones_cotiz_aux = "";
                                                                if (isset($lista_despacho_cotiz[$i])) {
                                                                    $id_proc_cotiz_aux = $lista_despacho_cotiz[$i]["id_proc_cotiz"];
                                                                    $valor_cotiz_aux = $lista_despacho_cotiz[$i]["valor_cotiz"];
                                                                    $observaciones_cotiz_aux = $lista_despacho_cotiz[$i]["observaciones_cotiz"];
                                                                }
                                                                ?>
                                                                <div id="d_detalle_cotizacion_<?php echo($i); ?>" class="div_formula">
                                                                    <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%;">
                                                                        <tr>
                                                                            <td align="right" style="width:20%;"><label class="inline">Procedimiento:</label></td>
                                                                            <td align="left" style="width:30%;">
                                                                                <?php
                                                                                $combo->getComboDb("cmb_proc_cotiz_" . $i, $id_proc_cotiz_aux, $lista_procedimientos_cotiz, "id_proc_cotiz,nombre_proc_cotiz", "--Seleccione--", "", true, "width:100%;");
                                                                                ?>
                                                                            </td>
                                                                            <td align="right" style="width:20%;"><label class="inline">Valor:</label></td>
                                                                            <td align="left" style="width:30%;">
                                                                                <input type="text" id="txt_valor_cotiz_<?php echo($i); ?>" value="<?php echo($valor_cotiz_aux); ?>" class="input" maxlength="8" style="width:110px;" onkeypress="return solo_numeros(event, false);" />
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td align="left" colspan="4">
                                                                                Observaciones:<br />
                                                                                <?php
                                                                                $observaciones_cotiz_aux = $utilidades->ajustar_texto_wysiwyg($observaciones_cotiz_aux);
                                                                                ?>
                                                                                <div id="txt_observaciones_cotiz_<?php echo($i); ?>"><?php echo($observaciones_cotiz_aux); ?></div>
                                                                                
                                                                            </td>
                                                                        </tr>
                                                                        <?php
                                                                        if ($ind_editar == 1) {
                                                                            if (!isset($_POST["tipo_entrada"])) {
                                                                                ?>
                                                                                <tr>
                                                                                    <td align="center" colspan="4">
                                                                                        <input type="button" id="btn_imprimir_cotiz" nombre="btn_imprimir_cotiz" class="btnPrincipal" value="Imprimir" onclick="guardar_despacho(5);"/>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php
                                                                            } else {
                                                                                ?>
                                                                                <tr>
                                                                                    <td align="center" colspan="4">
                                                                                        <input type="button" id="btn_imprimir_cotiz" nombre="btn_imprimir_cotiz" class="btnPrincipal" value="Imprimir" onclick="guardar_despacho(5);"/>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php
                                                                            }
                                                                        } else {
                                                                            ?>
                                                                            <tr>
                                                                                <td align="center" colspan="4">
                                                                                    <input type="button" id="btn_imprimir_cotiz" nombre="btn_imprimir_cotiz" class="btnPrincipal" value="Imprimir" onclick="guardar_despacho(6);"/>	
                                                                                </td>
                                                                            </tr>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </table>
                                                                </div>
                                                                <script>
        <?php
        if ($i > 0) {
            ?>
                                                                        setTimeout("ocultar_cotizacion(<?php echo($i); ?>)", 500);
            <?php
        }
        ?>
                                                                </script>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                         <?php
                                                        if ($ind_incapacidad == 1) {
                                                            ?>
														<div class="content" id="panel2-4">
															<div id="imprimir_incapacidad" style="display: none;"></div>
															<?php 
																$incapacidades = $dbHistoriaClinica->getIncapacidades($id_admision);
																$id_hc = $_POST["hdd_id_hc"];
																$class_incapacidades->getFormulacionIncapacidades($id_hc,$id_admision,$id_paciente,
                                                                $id_usuario_prof,$admision_obj);
													
															?>														 
															 <input type="hidden" id="hdd_ciex_diagnostico_1" nombre="hdd_ciex_diagnostico_1" value="<?=$incapacidades["diagnostico_principal"];?>" />
															 <input type="hidden" id="hdd_ciex_diagnostico_2" nombre="hdd_ciex_diagnostico_2" value="<?=$incapacidades["diagnostico_relacionado"];?>" />                                    
														</div>
													<?php
                                                        }
													?>
                                                    <table style="width:100%;">
                                                        <?php
                                                        if ($ind_editar == 1) {
                                                            if (!isset($_POST["tipo_entrada"])) {
                                                                ?>
                                                                <tr>
                                                                    <td align="center" colspan="4" style="font-size: 12px;">
                                                                        <input type="button" id="btn_guardar" nombre="btn_guardar" class="btnPrincipal" value="Guardar cambios" onclick="guardar_despacho(3);"/>
                                                                        <input type="button" id="btn_finalizar" nombre="btn_finalizar" class="btnPrincipal" value="Finalizar" onclick="guardar_despacho(1);"/>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <tr>
                                                                    <td align="center" colspan="4" style="font-size: 12px;">
                                                                        <input type="button" id="btn_guardar" nombre="btn_guardar" class="btnPrincipal" value="Guardar" onclick="guardar_despacho(3);"/>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </table>
                                                </div>
                                                <br/><br/>
                                                </form>
                                                <?php
                                            } else if ($tipo_accion == -1) {
                                                $id_menu_rem = 13;
                                                $menu_obj = $dbMenus->getMenu($id_menu_rem);
                                                ?>
                                                <div class="contenedor_error" style="display:block;">El registro de despacho ya ha sido iniciado por otro usuario.</div><br />
                                                <label><b><a onclick="enviar_credencial('<?php echo($menu_obj["pagina_menu"]); ?>', <?php echo($id_menu_rem); ?>);">&lt;&lt;Volver</a></b></label>
                                                <?php
                                            } else {
                                                ?>
                                                <div class="contenedor_error" style="display:block;">Error al ingresar al registro de despacho</div>
                                                <?php
                                            }
                                            ?>
                                            </div>
                                            </div>
                                            <?php
                                            if ($bol_confirma_bloqueo) {
                                                ?>
                                                <div id="d_confirma_bloqueo" style="display:none;">
                                                    <table class="datagrid" border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">
                                                        <tr class="headegrid">
                                                            <th align="center" class="msg_alerta" style="border: 1px solid #fff;">
                                                                <h4>Es posible que otro usuario se encuentre realizando el registro de despacho del paciente seleccionado &iquest;Desea continuar con el registro de despacho?</h4>
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th align="center" style="width:5%;border: 1px solid #fff;">
                                                                <input type="button" value="S&iacute;" class="btnPrincipal" onclick="cerrar_div_centro();"/>
                                                                &nbsp;&nbsp;
                                                                <input type="button" value="No" class="btnPrincipal" onclick="cancelar_registro_despacho();"/>
                                                            </th>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                            <script type="text/javascript" src="../js/foundation.min.js"></script>
                                            <script>
												$(document).foundation();
																		
												for (var i = 0; i < 20; i++) {
													initCKEditorDespacho("text_despacho_" + i);
													initCKEditorDespacho("txt_observaciones_cotiz_" + i);
												}
											<?php
												if ($ind_incapacidad == 1) {
											?>
												initCKEditorDespacho("txt_observaciones_adicionales");
											<?php
												}
											?>
                                            </script>
                                            <?php
                                            if (!isset($_POST["tipo_entrada"])) {
                                                $contenidoHtml->ver_historia($id_paciente);
                                                $contenidoHtml->footer();
                                            } else {
                                                $contenidoHtml->footer_iframe();
                                            }

                                            if ($bol_confirma_bloqueo) {
                                                ?>
                                                <script>
                                                    $("#d_interno").html($("#d_confirma_bloqueo").html());
                                                    mostrar_formulario_flotante(1);
                                                </script>
                                            <?php
                                            }
                                            ?>
                                            </body>
                                            </html>
