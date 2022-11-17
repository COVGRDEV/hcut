<?php
session_start();
/*
 * Pagina para crear consulta de evolución 
 * Autor: Feisar Moreno - 14/02/2014
 */
require_once("../db/DbVariables.php");
require_once("../db/DbUsuarios.php");
require_once("../db/DbConsultaEvolucion.php");
require_once("../db/DbAdmision.php");
require_once("../db/DbPacientes.php");
require_once("../db/DbTiposCitas.php");
require_once("../db/DbDiagnosticos.php");
require_once("../db/DbTiposRegistrosHc.php");
require_once("../db/DbTiposCitasDetalle.php");
require_once("../db/DbPlanes.php");
require_once("../db/DbListas.php");
require_once("../db/DbHistoriaClinica.php");
require_once("../db/DbConsultasOculoplastia.php");
require_once("../principal/ContenidoHtml.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../funciones/Class_Diagnosticos.php");
require_once("../funciones/Utilidades.php");
require_once("../funciones/FuncionesPersona.php");
require_once("../funciones/Class_Formulacion.php");
require_once("../funciones/Class_Espera_Dilatacion.php");
require_once("../funciones/Class_Tonometrias.php");
require_once("../funciones/Class_Solic_Procs.php");
require_once("FuncionesHistoriaClinica.php");
require_once("antecedentes_funciones.php");
require_once("../funciones/Class_Ordenes_Remisiones.php");
require_once("../funciones/Class_Incapacidades.php");

$dbVariables = new Dbvariables();
$dbConsultaEvolucion = new DbConsultaEvolucion();
$dbAdmision = new DbAdmision();
$dbPacientes = new DbPacientes();
$dbTiposCitas = new DbTiposCitas();
$dbUsuarios = new DbUsuarios();
$dbDiagnosticos = new DbDiagnosticos();
$dbTiposRegistrosHc = new DbTiposRegistrosHc();
$dbTiposCitasDetalle = new DbTiposCitasDetalle();
$dbPlanes = new DbPlanes();
$dbConsultasOculoplastia = new DbConsultasOculoplastia();
$dbListas = new DbListas();
$dbHistoriaClinica = new DbHistoriaClinica();

$contenido = new ContenidoHtml();
$utilidades = new Utilidades();
$funciones_persona = new FuncionesPersona();
$funciones_hc = new FuncionesHistoriaClinica();
$class_ordenes_remisiones = new Class_Ordenes_Remisiones();
$class_incapacidades = new Class_Incapacidades();

//variables
$titulo = $dbVariables->getVariable(1);
$horas_edicion = $dbVariables->getVariable(7);
//Cambiar las variables get a post
$utilidades->get_a_post();
$id_menu = 24;

$combo = new Combo_Box();
$class_diagnosticos = new Class_Diagnosticos();
$class_formulacion = new Class_Formulacion();
$class_espera_dilatacion = new Class_Espera_Dilatacion();
$class_tonometrias = new Class_Tonometrias();
$class_solic_procs = new Class_Solic_Procs();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $titulo["valor_variable"]; ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
        <link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
        <link href="../css/azul.css" rel="stylesheet" type="text/css" />
        <link href="../css/Class_Color_Pick.css" rel="stylesheet" type="text/css" />
        <link href="../css/foundation-datepicker.css" rel="stylesheet" type="text/css" />
        <link href="../funciones/fine-uploader/fine-uploader-gallery.css" rel="stylesheet" type="text/css"/>

        <script type="text/javascript" src="../js/jquery_autocompletar.js"></script>
        <script type="text/javascript" src="../js/jquery-ui.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.add.js"></script>	
        <script type="text/javascript" src="../js/sweetalert2.all.min.js"></script>
        <script type="text/javascript" src="../js/ajax.js"></script>
        <script type="text/javascript" src="../js/funciones.js"></script>
        <script type="text/javascript" src="../js/validaFecha.js"></script>
        <script type='text/javascript' src='../js/foundation-datepicker.js'></script>
        <script type="text/javascript" src="../js/Class_Diagnosticos_v1.2.js"></script>
        <script type="text/javascript" src="../js/Class_Formulas_Medicas.js"></script>
        <script type="text/javascript" src="../js/Class_Atencion_Remision_v1.3.js"></script>
        <script type="text/javascript" src="../js/Class_Formulacion_v1.5.js"></script>	
        <script type="text/javascript" src="../js/Class_Componente_Archivos.js"></script>
        <script type="text/javascript" src="../funciones/ckeditor/ckeditor.js"></script>
        <script type="text/javascript" src="../funciones/ckeditor/config.js"></script>	
        <script type="text/javascript" src="../funciones/fine-uploader/fine-uploader.min.js"></script>
        <script type="text/javascript" src="../js/Class_Color_Pick.js"></script>
        <script type="text/javascript" src="../js/Class_Espera_Dilatacion.js"></script>
        <script type="text/javascript" src="../js/Class_Tonometrias.js"></script>
        <script type="text/javascript" src="../js/Class_Solic_Procs.js"></script>
        <script type="text/javascript" src="historia_clinica_v1.1.js"></script>
        <script type="text/javascript" src="FuncionesHistoriaClinica.js"></script>
        <script type="text/javascript" src="evolucion_v1.1.js"></script>
        <script type="text/javascript" src="antecedentes_v1.0.js"></script>
        <script type="text/javascript" src="extension_retina.js"></script>
        <script type="text/javascript" src="extension_oculoplastia.js"></script>
        <script type="text/javascript" src="extension_pterigio.js"></script>
        <script type="text/javascript" src="extension_neso.js"></script>
        <script type="text/javascript" src="../js/Class_Ordenes_Remisiones_v1.js"></script>
        <script type="text/javascript" src="../js/moment.js"></script>
        <?php
        $lista_si_no = array();
        $lista_si_no[0]["id"] = "1";
        $lista_si_no[0]["valor"] = "S&iacute;";
        $lista_si_no[1]["id"] = "0";
        $lista_si_no[1]["valor"] = "No";

        $tabla_diagnosticos = $dbDiagnosticos->getDiagnosticoCiexTotal();
        $i = 0;
        $cadena_diagnosticos = "";
        foreach ($tabla_diagnosticos as $fila_diagnosticos) {
            $cod_ciex = $fila_diagnosticos["codciex"];
            $nom_ciex = $fila_diagnosticos["nombre"];

            if ($cadena_diagnosticos != "") {
                $cadena_diagnosticos .= ",";
            }
            $cadena_diagnosticos .= "'" . $nom_ciex . " | " . $cod_ciex . "'";

            $i++;
        }
        ?>
        <script>
            $(function () {
                var Tags_diagnosticos = [<?php echo($cadena_diagnosticos) ?>];

                for (k = 1; k <= 10; k++) {
                    $("#txt_busca_diagnostico_" + k).autocomplete({source: Tags_diagnosticos});
                }
            });
        </script>
    </head>
    <body onload="ajustar_textareas();
            ocultar_panels_evolucion();">
              <?php
              $contenido->validar_seguridad(0);
              if (!isset($_POST["tipo_entrada"])) {
                  $contenido->cabecera_html();
              }

              $id_usuario = $_SESSION["idUsuario"];

              if (isset($_POST["hdd_id_paciente"])) {
                  $id_paciente = $utilidades->str_decode($_POST["hdd_id_paciente"]);

                  //Se obtienen los datos del paciente
                  $paciente_obj = $dbPacientes->getExistepaciente3($id_paciente);
                  $nombre_paciente = $funciones_persona->obtenerNombreCompleto($paciente_obj["nombre_1"], $paciente_obj["nombre_2"], $paciente_obj["apellido_1"], $paciente_obj["apellido_2"]);

                  $id_admision = "";
                  $tipo_reg_adicional = "";
                  if (isset($_POST["hdd_id_admision"])) {
                      $id_admision = $utilidades->str_decode($_POST["hdd_id_admision"]);

                      if ($id_admision != "" && $id_admision != "0") {
                          //Se obtienen los datos de la admision
                          $admision_obj = $dbAdmision->get_admision($id_admision);

                          //Se obtienen los datos del tipo de cita
                          $tipo_cita_obj = $dbTiposCitas->get_tipo_cita($admision_obj["id_tipo_cita"]);

                          //Se busca el tipo de registro de la atención
                          $tipo_reg_hc_obj = $dbTiposRegistrosHc->getTipoRegistroHcCitaMenu($admision_obj["id_tipo_cita"], $id_menu);
                          $id_tipo_reg = $tipo_reg_hc_obj["id_tipo_reg"];

                          //Se obtienen los datos del tipo de cita
                          $tipo_cita_obj = $dbTiposCitas->get_tipo_cita($admision_obj["id_tipo_cita"]);
                          $tipo_cita_det_obj = $dbTiposCitasDetalle->get_tipos_citas_detalle($admision_obj["id_tipo_cita"], $id_tipo_reg);
                          $tipo_reg_adicional = $tipo_cita_det_obj["tipo_reg_adicional"];
                      }
                  }

                  if (isset($_POST["hdd_id_hc"])) {
                      $id_hc = $utilidades->str_decode($_POST["hdd_id_hc"]);
                      $tabla_hc = $dbConsultaEvolucion->getHistoriaClinicaId($id_hc);
                      $id_tipo_reg = $tabla_hc["id_tipo_reg"];
                  } else if (isset($_POST["hdd_id_admision"])) {
                      $tabla_hc = $dbConsultaEvolucion->getHistoriaClinicaAdmision($id_admision, $id_tipo_reg);
                  } else {
                      //Registro de evoluciones sin admisión
                      @$id_tipo_reg = $utilidades->str_decode($_POST["id_tipo_reg"]);
                      $tabla_hc = array();
                  }

                  if (count($tabla_hc) > 0) { //Si se encuentra una HC para este paciente se carga los datos de lo contrario se crea la HC
                      $tipo_accion = "2"; //Editar consulta de evolución
                      $id_hc_consulta = $tabla_hc["id_hc"];

                      //se obtiene el registro de la consulta de evolución a partir del ID de la Historia Clinica 
                      $tabla_consulta = $dbConsultaEvolucion->get_consulta_evolucion($id_hc_consulta);
                      $texto_evolucion = $tabla_consulta["texto_evolucion"];
                      $diagnostico_evolucion = $tabla_consulta["diagnostico_evolucion"];
                      $solicitud_examenes_evolucion = $tabla_consulta["solicitud_examenes_evolucion"];
                      $tratamiento_evolucion = $tabla_consulta["tratamiento_evolucion"];
                      $medicamentos_evolucion = $tabla_consulta["medicamentos_evolucion"];
                      $nombre_usuario_alt = $tabla_hc["nombre_usuario_alt"];
                      $ind_formula_gafas = $tabla_consulta["ind_formula_gafas"];
                      //$desc_antecedentes_medicos = $tabla_consulta["desc_antecedentes_medicos"];
                      $ind_antec_cx_refrac = $tabla_consulta["ind_antec_cx_refrac"];
                      $observaciones_tonometria = $tabla_consulta["observaciones_tonometria"];

                      //Se verifica si se debe actualizar el estado de la admisión asociada
                      $en_atencion = "0";
                      if (isset($_POST["hdd_en_atencion"])) {
                          $en_atencion = $utilidades->str_decode($_POST["hdd_en_atencion"]);
                      }

                      if ($en_atencion == "1") {
                          $dbAdmision->editar_admision_estado($id_admision, 6, 1, $id_usuario);
                      }
                  } else { //Entre en procesos de crear HC
                      $tipo_accion = "1";

                      //Se crea la historia clinica y se inicia la consulta de evolución
                      $id_hc_consulta = $dbConsultaEvolucion->crear_consulta_evolucion($id_paciente, $id_tipo_reg, $id_usuario, $id_admision);

                      //Se llama al creador de la extensión
                      switch ($tipo_reg_adicional) {
                          case "3": //Oculoplastia
                              $dbConsultasOculoplastia->crearConsultaOculoplastia($id_hc_consulta, $id_usuario);
                              break;
                      }

                      if ($id_hc_consulta > 0) {
                          $tabla_consulta = $dbConsultaEvolucion->get_consulta_evolucion($id_hc_consulta);
                      } else {
                          $tipo_accion = "0";
                      }
                      //Variables de inicio de consulta de evolucion
                      $texto_evolucion = "";
                      $diagnostico_evolucion = "";
                      $solicitud_examenes_evolucion = "";
                      $tratamiento_evolucion = "";
                      $medicamentos_evolucion = "";
                      $nombre_usuario_alt = $tabla_hc["nombre_usuario_alt"];
                      $ind_formula_gafas = "0";
                      //$desc_antecedentes_medicos = $tabla_consulta["desc_antecedentes_medicos"];;
                      $ind_antec_cx_refrac = $tabla_consulta["ind_antec_cx_refrac"];
                      $observaciones_tonometria = "";
                  }

                  //Se obtienen los datos del registro de historia clínica
                  $historia_clinica_obj = $dbConsultaEvolucion->getHistoriaClinicaId($id_hc_consulta);
              } else {
                  $tipo_accion = "0"; //Ninguna accion Error
              }

              //Se inicia el componente de formulación de medicamentos
              $cod_tipo_medicamento = "";
              if (isset($_POST["hdd_id_admision"])) {
                  $admision_obj = $dbAdmision->get_admision($_POST["hdd_id_admision"]);
                  $plan_obj = $dbPlanes->getPlan($admision_obj["id_plan"]);

                  $cod_tipo_medicamento = $plan_obj["cod_tipo_medicamento"];
              }
              $class_formulacion->iniciarComponentesFormulacion($cod_tipo_medicamento, $id_hc_consulta);

              //Edad del paciente
              $datos_paciente = $dbPacientes->getEdadPaciente($id_paciente, "");
              $edad_paciente = $datos_paciente["edad"];
              $profesion_paciente = $datos_paciente["profesion"];

              //Nombre del profesional que atiende la consulta
              $id_usuario_profesional = $tabla_consulta["id_usuario_crea"];
              $usuario_profesional_obj = $dbUsuarios->getUsuario($id_usuario_profesional);
              $nombre_usuario_profesional = $usuario_profesional_obj["nombre_usuario"] . " " . $usuario_profesional_obj["apellido_usuario"];

              if (!isset($_POST["tipo_entrada"])) {
                  ?>
            <div class="title-bar title_hc">
                <div class="wrapper">
                    <div class="breadcrumb">
                        <ul>
                            <?php
                            if (isset($tipo_cita_obj["nombre_tipo_cita"])) {
                                ?>
                                <li class="breadcrumb_on"><?php echo($tipo_cita_obj["nombre_tipo_cita"]); ?> (Evoluci&oacute;n - Interconsulta)</li>
                                <?php
                            } else {
                                ?>
                                <li class="breadcrumb_on">Evoluci&oacute;n - Interconsulta</li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
            <?php
        }

        if ($tipo_accion > 0) {
            //Para verificaro que tiene permiso de hacer cambio
            $ind_editar = $dbConsultaEvolucion->getIndicadorEdicion($id_hc_consulta, $horas_edicion["valor_variable"]);
            $ind_editar_enc_hc = $ind_editar;
            if ($ind_editar == 1 && isset($_POST["tipo_entrada"])) {
                $ind_editar_enc_hc = 0;
            }

            $funciones_hc->encabezado_historia_clinica($id_paciente, $id_admision, $id_hc_consulta, $ind_editar_enc_hc, false);

            $tabla_postqx_catarata = $dbConsultaEvolucion->getPostQxCatarata($id_paciente);

            $id_seguimiento = $tabla_postqx_catarata["id_seguimiento"];
            $cantidad_respuestas = $tabla_postqx_catarata["cantidad_respuestas"];

            if ($id_seguimiento > 0) {
                ?>	
                <div id="mensaje_agenda">
                    <div class="div_mensaje_citas" style="margin: 0 auto;">
                        Atenci&oacute;n, Este paciente esta matriculado para Seguimiento post-quir&uacute;rgico de Catarata <br />
                        Sus respuestas a la fecha son = <b><?php echo($cantidad_respuestas); ?></b>
                    </div>
                </div>
                <div id="agenda"></div>	
                <?php
            }
            ?>
            <div class="contenedor_principal" id="id_contenedor_principal">	
                <div id="d_guardar_evolucion" style="width: 100%; display: block;">
                    <div class="contenedor_error" id="contenedor_error"></div>
                    <div class="contenedor_exito" id="contenedor_exito"></div>
                </div>
                <div class="formulario" id="principal_evolucion" style="width:100%; display:block;">
                    <?php
                    //Se inserta el registro de ingreso a la historia clínica
                    $dbConsultaEvolucion->crear_ingreso_hc($id_usuario, $id_paciente, $id_admision, $id_hc_consulta, 160);
                    ?>
                    <form id="frm_consulta_evolucion" name="frm_consulta_evolucion" method="post">
                        <input type="hidden" name="hdd_id_hc_consulta" id="hdd_id_hc_consulta" value="<?php echo($id_hc_consulta); ?>" />
                        <input type="hidden" name="hdd_id_admision" id="hdd_id_admision" value="<?php echo($id_admision); ?>" />
                        <?php
                        if (isset($admision_obj["id_convenio"])) {
                            ?>
                            <input type="hidden" name="hdd_id_convenio" id="hdd_id_convenio" value="<?php echo($admision_obj["id_convenio"]); ?>" />
                            <input type="hidden" name="hdd_id_plan" id="hdd_id_plan" value="<?php echo($admision_obj["id_plan"]); ?>" />
                            <?php
                        } else {
                            ?>
                            <input type="hidden" name="hdd_id_convenio" id="hdd_id_convenio" value="" />
                            <input type="hidden" name="hdd_id_plan" id="hdd_id_plan" value="" />
                            <?php
                        }
                        ?>
                        <input type="hidden" id="hdd_tipo_reg_adicional" value="<?php echo($tipo_reg_adicional); ?>" />
                        <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
                            <tr valign="middle">
                                <td align="center" colspan="2">
                                    <div class="contenedor_error" id="contenedor_error"></div>
                                    <div class="contenedor_exito" id="contenedor_exito"></div>
                                </td>
                            </tr>
                            <tr>
                                <th align="left" colspan="2">
                                    <h6 style="margin: 1px;">
                                        <input type="hidden" id="hdd_usuario_anonimo" value="<?php echo($usuario_profesional_obj["ind_anonimo"]); ?>" />
                                        <b>Profesional que atiende: </b>
                                        <?php
                                        if ($usuario_profesional_obj["ind_anonimo"] == "0") {
                                            ?>
                                            <input type="hidden" id="txt_nombre_usuario_alt" value="" />
                                            <?php
                                            echo($nombre_usuario_profesional);
                                        } else {
                                            ?>
                                            <input type="text" id="txt_nombre_usuario_alt" maxlength="100" value="<?php echo($nombre_usuario_alt); ?>" style="width:60%; display:inline;" onblur="trim_cadena(this);" />
                                            <?php
                                        }
                                        ?>
                                    </h6>
                                </th>
                            </tr>			
                            <?php
                            if (isset($tipo_cita_obj["ind_preqx"]) && $tipo_cita_obj["ind_preqx"] == "1") {
                                ?>
                                <tr>
                                    <th align="left" style="width:90%;">
                                        <h6 style="margin: 1px;">
                                            <b>Cirug&iacute;a:</b> <?php echo($tabla_consulta["nombre_cirugia"]); ?>
                                            <br />
                                            <b>Fecha de la cirug&iacute;a:</b> <?php echo($tabla_consulta["fecha_cirugia_t"]); ?>
                                        </h6>
                                    </th>
                                    <th align="left" style="width:10%;">
                                        <h6 style="margin: 1px;">
                                            <b>Ojo:</b> <?php echo($tabla_consulta["ojo"]); ?>
                                            <br />
                                            <?php echo($tabla_consulta["num_cirugia"]); ?>a cirug&iacute;a
                                        </h6>
                                    </th>
                                </tr>
                                <?php
                            }
                            ?>
                        </table>
                        <?php
                        $ind_optometria = false;
                        $id_hc_optometria = 0;
                        $nombre_pagina_op = "";
                        $alto_frame = 0;

                        //Se verifica si existe una consulta de optometría en la atención
                        $datos_optometria = $dbConsultaEvolucion->getOptometriaPaciente($id_paciente, $id_admision);
                        if (isset($datos_optometria["id_hc"])) {
                            $ind_optometria = true;
                            $nombre_pagina_op = "../historia_clinica/consulta_optometria.php";
                            $alto_frame = 2100;

                            $id_hc_optometria = $datos_optometria["id_hc"];
                        } else {
                            //Se verifica si existe una consulta de control de optometría en la atención
                            $datos_optometria = $dbConsultaEvolucion->getOptometriaControlPaciente($id_paciente, $id_admision);
                            if (isset($datos_optometria["id_hc"])) {
                                $ind_optometria = true;
                                $nombre_pagina_op = "../historia_clinica/control_optometria.php";
                                $alto_frame = 720;

                                $id_hc_optometria = $datos_optometria["id_hc"];
                            }
                        }
                        ?>
                        <div class="tabs-container">
                            <dl class="tabs" data-tab>
                                <dd id="panel_oft_3" onclick="setTimeout(function () {
                                                ajustar_textareas();
                                            }, 100);"><a href="#panel2-3">Antecedentes</a></dd>
                                <dd id="panel_oft_2" class="active" onclick="setTimeout(function () {
                                                ajustar_textareas();
                                            }, 100);"><a href="#panel2-2">Evoluci&oacute;n</a></dd>
                                            
                                <dd id="panel_oft_9" class="" onclick="setTimeout(function () {
                                                ajustar_textareas();
                                            }, 100);"><a href="#panel2-9">Incapacidad</a></dd>
                                            
                                    <?php
                                    switch ($tipo_reg_adicional) {
                                        case "2": //Retina
                                            ?>
                                        <dd id="panel_oft_4" onclick="setTimeout(function () {
                                                                ajustar_textareas();
                                                            }, 100);"><a href="#panel2-4">Retina</a></dd>
                                            <?php
                                            break;
                                        case "3": //Oculoplastia
                                            ?>
                                        <dd id="panel_oft_5" onclick="setTimeout(function () {
                                                                ajustar_textareas();
                                                            }, 100);"><a href="#panel2-5">Oculoplastia</a></dd>
                                            <?php
                                            break;
                                        case "4": //Pterigio
                                            ?>
                                        <dd id="panel_oft_6" onclick="setTimeout(function () {
                                                                ajustar_textareas();
                                                            }, 100);"><a href="#panel2-6">Pterigio</a></dd>
                                            <?php
                                            break;
                                        case "5": //NESO
                                            ?>
                                        <dd id="panel_oft_7" onclick="setTimeout(function () {
                                                                ajustar_textareas();
                                                            }, 100);"><a href="#panel2-7">NESO</a></dd>
                                            <?php
                                            break;
                                    }

                                    if ($ind_optometria) {
                                        ?>
                                    <dd id="panel_oft_1" onclick="setTimeout(function () {
                                                        ajustar_textareas();
                                                        ajustar_div_optometria();
                                                    }, 100);"><a href="#panel2-1">Optometr&iacute;a</a></dd>
                                        <?php
                                    }
                                    ?>
                                <dd id="panel_oft_8" onclick="setTimeout(function () {
                                                ajustar_textareas();
                                            }, 100);"><a href="#panel2-8">Remisiones</a></dd>
                            </dl>
                            <div class="tabs-content no-margin no-padding">
                                <?php
                                if ($ind_optometria) {
                                    ?>
                                    <!-- OPTOMETRÍA -->
                                    <div class="content" id="panel2-1">
                                        <div id="div_consulta_optometria"></div>
                                        <?php
                                        $id_menu_aux = "13";
                                        if (isset($_POST["hdd_numero_menu"]) && trim($_POST["hdd_numero_menu"]) != "") {
                                            $id_menu_aux = $_POST["hdd_numero_menu"];
                                        }
                                        ?>
                                        <script type="text/javascript">
                                            mostrar_consulta_iframe(<?php echo($id_paciente); ?>, "<?php echo($nombre_paciente); ?>", <?php echo($id_admision); ?>, "<?php echo($nombre_pagina_op); ?>", <?php echo($id_hc_optometria); ?>, <?php echo($_POST["credencial"]); ?>, <?php echo($id_menu_aux); ?>, "div_consulta_optometria");
                                        </script>
                                    </div>
                                    <!-- FIN - OPTOMETRÍA -->
                                    <?php
                                }
                                ?>
                                <!-- EVOLUCIÓN -->
                                <div class="content active" id="panel2-2">
                                    <table border="0" cellpadding="0" cellspacing="0" align="center" style="width:98%;">
                                        <tr>
                                            <td align="center" colspan="3">
                                                <h5 style="margin: 10px">Control de Evoluci&oacute;n *</h5>
                                                <div id="txt_evolucion"><?php echo($utilidades->ajustar_texto_wysiwyg($texto_evolucion)); ?></div>
                                            </td>
                                        </tr>
                                        <?php
                                        if (!isset($_POST["tipo_entrada"])) {
                                            ?>
                                            <tr style="height:10px;"></tr>
                                            <tr>
                                                <td align="center" colspan="3">
                                                    <?php
                                                    //Se carga el componente indicador de espera por dilatación de pupila
                                                    $class_espera_dilatacion->getEsperaDilatacion($id_admision, $admision_obj["id_tipo_espera"] != "" ? 1 : 0);
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <br />
                                    <?php
                                    //Se inserta en componente de tonometrías
                                    $class_tonometrias->agregar_tonometria($id_hc_consulta, $observaciones_tonometria, null);
                                    ?>
                                    <!-- Diagnósticos -->
                                    <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
                                        <tr>
                                            <td align="center" colspan="2">
                                                <h6>Diagn&oacute;sticos</h6>
                                                <?php
                                                //Se define el número de diagnósticos obligatorios
                                                $num_diag_oblig = 1;
                                                if ($id_tipo_reg == "21") {
                                                    $num_diag_oblig = 0;
                                                }
                                                $class_diagnosticos->getFormularioDiagnosticos($id_hc_consulta);
                                                ?>
                                                <input type="hidden" id="hdd_num_diag_oblig" value="<?php echo($num_diag_oblig); ?>" />
                                            </td>
                                        </tr>
                                        <?php
                                        //Se verifica si hay un registro de optometría asociado
                                        $bol_optometria = $funciones_hc->tieneConsultaOptometria($id_admision);

                                        if ($bol_optometria) {
                                            ?>
                                            <tr>
                                                <td align="right" style="width:25%;">
                                                    <h6 class="no-margin"><b>Entregar f&oacute;rmula de gafas:</b></h6>
                                                </td>
                                                <td align="left" style="width:75%;">
                                                    <?php
                                                    $combo->getComboDb("cmb_formula_gafas", $ind_formula_gafas, $lista_si_no, "id,valor", "--Seleccione--", "", 1, "", "", "no-margin");
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                        } else {
                                            ?>
                                            <input type="hidden" id="cmb_formula_gafas" name="cmb_formula_gafas" value="" />
                                            <?php
                                        }
                                        ?>
                                        <input type="hidden" id="hdd_ind_optometria" name="hdd_ind_optometria" value="<?php echo($bol_optometria ? 1 : 0) ?>" />
                                        <tr>
                                            <td align="center" colspan="2">
                                                <label><b>Otros diagn&oacute;sticos y an&aacute;lisis</b></label>
                                                <div id="txt_diagnostico_evolucion"><?php echo($utilidades->ajustar_texto_wysiwyg($diagnostico_evolucion)); ?></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" colspan="2">
                                                <label><b>Solicitud de Procedimientos y Ex&aacute;menes Complemetarios</b></label>
                                                <?php
                                                $class_solic_procs->getFormularioSolicitud($id_hc_consulta);
                                                ?>
                                                <div id="txt_solicitud_examenes_evolucion"><?php echo($utilidades->ajustar_texto_wysiwyg($solicitud_examenes_evolucion)); ?></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" colspan="2">
                                                <label><b>Recomendaciones Cl&iacute;nicas, M&eacute;dicas, Optom&eacute;tricas y Quir&uacute;rgicas&nbsp;</b></label>
                                                <div id="txt_tratamiento_evolucion"><?php echo($utilidades->ajustar_texto_wysiwyg($tratamiento_evolucion)); ?></div>
                                            </td>
                                        </tr>
                                        <tr><td><div class="div_separador"></div></td></tr>
                                        <tr>
                                            <td align="center" colspan="2">
                                                <label style="display:inline;"><b>Formulaci&oacute;n de Medicamentos</b></label>
                                                <?php
                                                $class_formulacion->getFormularioFormulacion($id_hc_consulta);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr style="display:none;">
                                            <td align="center" colspan="2">
                                                <label><b>F&oacute;rmula M&eacute;dica</b></label>
                                                <textarea style="text-align: justify;" class="textarea_oftalmo" id="medicamentos_evolucion" nombre="medicamentos_evolucion" onblur="trim_cadena(this);"><?php echo($medicamentos_evolucion); ?></textarea>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
								<div class="content active" id="panel2-9">
                                  <div id="imprimir_incapacidad" style="display: none;"></div>
                                  	<?php 
										$id_hc = $_POST["hdd_id_hc"];
										$class_incapacidades->getFormulacionIncapacidades($id_hc,$id_admision,$id_paciente,$id_usuario_profesional,$admision_obj);
									?>
								</div>
						
                                <!-- FIN - EVOLUCIÓN -->
                                <!-- ANTECEDENTES -->
                                <div class="content active" id="panel2-3">
                                    <?php
                                    require("antecedentes.php");
                                    ?>
                                </div>
                                <!-- FIN - ANTECEDENTES -->
                                <?php
                                switch ($tipo_reg_adicional) {
                                    case "2": //Retina
                                        ?>
                                        <!-- RETINA -->
                                        <div class="content active" id="panel2-4">
                                            <?php
                                            require("extension_retina.php");
                                            ?>
                                        </div>
                                        <!-- FIN - RETINA -->
                                        <?php
                                        break;

                                    case "3": //Oculoplastia
                                        ?>
                                        <!-- OCULOPLASTIA -->
                                        <div class="content active" id="panel2-5">
                                            <?php
                                            require("extension_oculoplastia.php");
                                            ?>
                                        </div>
                                        <!-- FIN - OCULOPLASTIA -->
                                        <?php
                                        break;

                                    case "4": //Pterigio
                                        require_once("../funciones/fine-uploader/templates/gallery.html");
                                        ?>
                                        <!-- PTERIGIO -->						
                                        <div class="content active" id="panel2-6">							
                                            <?php
                                            require("extension_pterigio.php");
                                            ?>
                                        </div>
                                        <!-- FIN - PTERIGIO -->
                                        <?php
                                        break;

                                    case "5": //NESO
                                        ?>
                                        <!-- NESO -->						
                                        <div class="content active" id="panel2-7">							
                                            <?php
                                            require("extension_neso.php");
                                            ?>
                                        </div>
                                        <!-- FIN - NESO -->
                                        <?php
                                        break;
                                }
                                ?>
                                <div class="content active" id="panel2-8">
                                    <?php
                                    $class_ordenes_remisiones->getFormularioRemisiones($id_hc_consulta, 1, $ind_editar);
									$class_ordenes_remisiones->getFormularioOrdenarMedicamentos($id_hc_consulta, NULL, 1, $ind_editar);
                                    $class_ordenes_remisiones->getFormularioOrdenesMedicas($id_hc_consulta, NULL, 1, $ind_editar);
                                    ?>
                                </div>
                            </div>
                        </div>
                        <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:98%;">
                            <tr valign="top">
                                <td colspan="3">
                                    <?php
                                    if (!isset($_POST["tipo_entrada"])) {
                                        ?>
                                        <input class="btnPrincipal" type="button" id="btn_imprimir" nombre="btn_imprimir" value="Imprimir" onclick="guardar_evolucion(2, 1);" />
                                        <?php
                                    } else {
                                        ?>
                                        <input class="btnPrincipal" type="button" id="btn_imprimir" nombre="btn_imprimir" value="Imprimir" onclick="imprimir_evolucion();" />
                                        <?php
                                    }

                                    //Para verificar que tiene permiso de hacer cambio
                                    if ($ind_editar == 1) {
                                        if (!isset($_POST["tipo_entrada"])) {
                                            ?>
                                            <input type="button" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Guardar cambios" onclick="guardar_evolucion(2, 0);" />
                                            <?php
                                            if (isset($admision_obj["id_tipo_cita"])) {
                                                $id_tipo_cita = $admision_obj["id_tipo_cita"];
                                                $lista_tipos_citas_det_remisiones = $dbTiposCitasDetalle->get_lista_tipos_citas_det_remisiones($id_tipo_cita, $id_tipo_reg);

                                                if (count($lista_tipos_citas_det_remisiones) > 0) {
                                                    ?>
                                                    <input class="btnPrincipal" type="button" id="btn_enviar_a" nombre="btn_enviar_a" value="Enviar a ..." onclick="enviar_a_estados();" />
                                                    <?php
                                                }
                                            }
                                            ?>
                                            <input type="button" id="btn_finalizar" nombre="btn_finalizar" class="btnPrincipal" value="Finalizar consulta" onclick="guardar_evolucion(1, 0);" />
                                            <?php
                                        } else {
                                            ?>
                                            <input type="button" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Guardar" onclick="guardar_evolucion(3, 0);" />
                                            <?php
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
            <?php
        } else {
            ?>
            <div class="contenedor_principal">
                <div class="contenedor_error" style="display:block;">Error al ingresar a la consulta de evoluci&oacute;n</div>
            </div>
            <?php
        }

        //Se agrega el panel derecho de contactos
        obtener_listado_contactos();
        ?>
        <script type="text/javascript" src="../js/foundation.min.js"></script>
        <script>
			$(document).foundation();
			
			initCKEditorEvolucion("txt_evolucion");
			initCKEditorEvolucion("txt_diagnostico_evolucion");
			initCKEditorEvolucion("txt_solicitud_examenes_evolucion");
			initCKEditorEvolucion("txt_observaciones_adicionales");
			initCKEditorEvolucion("txt_tratamiento_evolucion");
			initCKEditorEvolucion("txt_observaciones_tonometria");
			for (var i = 0; i < <?php echo($cantidad_antecedentes); ?>; i++) {
				initCKEditorEvolucion("txt_texto_antecedente_" + i);
			}
			
			//Ciclo para las remisiones
			for (var i = 0; i < 10; i++) {
				initCKEditorEvolucion("tabla_rem_desc_" + i);
			}
			
			//Ciclo para medicamentos
			for (var i = 0; i < 10; i++) {
				initCKEditorEvolucion("frecAdmMed_" + i);
			}
			<?php
				switch ($tipo_reg_adicional) {
					case "3": //Oculoplastia
			?>
			agregar_ckeditor_oculoplastia();
			<?php
						break;
				}
			?>
        </script>
        <?php
			if (!isset($_POST["tipo_entrada"])) {
				$contenido->ver_historia($id_paciente);
				$contenido->footer();
			} else {
				$contenido->footer_iframe();
			}
        ?>
    </body>
</html>
