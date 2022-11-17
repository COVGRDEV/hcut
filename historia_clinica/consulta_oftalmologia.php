<?php
session_start();

require_once("../db/DbVariables.php");
require_once("../db/DbUsuarios.php");
require_once("../db/DbListas.php");
require_once("../db/DbConsultaOftalmologia.php");
require_once("../db/DbAdmision.php");
require_once("../db/DbTiposCitas.php");
require_once("../db/DbPacientes.php");
require_once("../db/DbDiagnosticos.php");
require_once("../db/DbTiposCitasDetalle.php");
require_once("../db/DbTiposRegistrosHc.php");
require_once("../db/DbPlanes.php");
require_once("../db/DbDespacho.php");
require_once("../db/DbHistoriaClinica.php");
require_once("../principal/ContenidoHtml.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../funciones/Class_Componente_Rec_Oft.php");
require_once("../funciones/Class_Color_Pick.php");
require_once("../funciones/Class_Diagnosticos.php");
require_once("../funciones/Class_Formulacion.php");
require_once("../funciones/Class_Espera_Dilatacion.php");
require_once("../funciones/Class_Tonometrias.php");
require_once("../funciones/Class_Solic_Procs.php");
require_once("../funciones/Utilidades.php");
require_once("../funciones/FuncionesPersona.php");
require_once("FuncionesHistoriaClinica.php");
require_once("antecedentes_funciones.php");
require_once("../funciones/Class_Ordenes_Remisiones.php");
require_once("../funciones/Class_Incapacidades.php");

$dbVariables = new Dbvariables();
$dbUsuarios = new DbUsuarios();
$dbListas = new DbListas();
$dbConsultaOftalmologia = new DbConsultaOftalmologia();
$dbAdmision = new DbAdmision();
$dbTiposCitas = new DbTiposCitas();
$dbPacientes = new DbPacientes();
$dbDiagnosticos = new DbDiagnosticos();
$dbTiposCitasDetalle = new DbTiposCitasDetalle();
$dbTiposRegistrosHc = new DbTiposRegistrosHc();
$dbPlanes = new DbPlanes();
$dbDespacho = new DbDespacho();
$dbHistoriaClinica = new DbHistoriaClinica();

$contenido = new ContenidoHtml();
$combo = new Combo_Box();
$componenteRecOft = new Class_Componente_Rec_Oft();
$class_diagnosticos = new Class_Diagnosticos();
$class_formulacion = new Class_Formulacion();
$class_espera_dilatacion = new Class_Espera_Dilatacion();
$class_tonometrias = new Class_Tonometrias();
$class_solic_procs = new Class_Solic_Procs();
$class_ordenes_remisiones = new Class_Ordenes_Remisiones();
$class_incapacidades = new Class_Incapacidades();

$utilidades = new Utilidades();
$funciones_persona = new FuncionesPersona();
$funciones_hc = new FuncionesHistoriaClinica();

//variables
$titulo = $dbVariables->getVariable(1);
$horas_edicion = $dbVariables->getVariable(7);

//Cambiar las variables get a post
$utilidades->get_a_post();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $titulo["valor_variable"]; ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
        <link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
        <link href="../css/azul.css" rel="stylesheet" type="text/css" />
        <link href="../css/foundation-datepicker.css" rel="stylesheet" type="text/css" />
        <link href="../css/Class_Color_Pick.css" rel="stylesheet" type="text/css" />

        <!--Para autocompletar DEBE IR DE PRIMERO-->
        <script type="text/javascript" src="../js/jquery.min.js"></script>
        <script type="text/javascript" src="../js/jquery.maskedinput.js"></script>
        <script type="text/javascript">
            jQuery(function ($) {
                $.mask.definitions["H"] = "[012]";
                $.mask.definitions["N"] = "[012345]";
                $.mask.definitions["n"] = "[0123456789]";
                $("#tonometria_hora").mask("Hn:Nn");
            });
        </script>
        <!--Para data picker y time picker DEBE IR DE SEGUNDO-->
        <script type="text/javascript" src="../js/jquery_autocompletar.js"></script>
        <script type="text/javascript" src="../js/jquery-ui.js"></script>
		<script type='text/javascript' src='../js/foundation-datepicker.js'></script>
        <!--Para color picker DEBE IR DE TERCERO-->
        <script type="text/javascript" src="../js/jquery.colorPicker.js"/></script>
        <!--Para validar DEBE IR DE CUARTO-->
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.add.js"></script>
        <script type='text/javascript'  src="../js/sweetalert2.all.min.js"></script>
        <script type="text/javascript" src="../js/moment.js"></script>
        <!--Para funciones de oftalmologia DEBE IR DE QUINTO-->
        <script type="text/javascript" src="../js/ajax.js"></script>
        <script type="text/javascript" src="../js/funciones.js"></script>
        <script type="text/javascript" src="../js/validaFecha.js"></script>
        <script type="text/javascript" src="../js/Class_Diagnosticos_v1.2.js"></script>
        <script type="text/javascript" src="../js/Class_Formulas_Medicas.js"></script>
        <script type="text/javascript" src="../js/Class_Atencion_Remision_v1.3.js"></script>
        <script type="text/javascript" src="../js/Class_Ordenes_Remisiones_v1.js"></script>
        <script type="text/javascript" src="../js/Class_Formulacion_v1.5.js"></script>
        <script type="text/javascript" src="../funciones/ckeditor/ckeditor.js"></script>
        <script type="text/javascript" src="../funciones/ckeditor/config.js"></script>
        <script type="text/javascript" src="../js/Class_Componente_Rec_Oft_v1.1.js"></script>
        <script type="text/javascript" src="../js/Class_Color_Pick.js"></script>
        <script type="text/javascript" src="../js/Class_Espera_Dilatacion.js"></script>
        <script type="text/javascript" src="../js/Class_Tonometrias.js"></script>
        <script type="text/javascript" src="../js/Class_Solic_Procs.js"></script>
        <script type="text/javascript" src="../js/jquery.textarea_autosize.js"></script>
        <script type="text/javascript" src="historia_clinica_v1.1.js"></script>
        <script type="text/javascript" src="FuncionesHistoriaClinica.js"></script>
        <script type="text/javascript" src="consulta_oftalmologia_v1.1.js"></script>
        <script type="text/javascript" src="antecedentes_v1.0.js"></script>
        
        <?php
        //Array valores oftalmología pediátrica
        $cadena_ofp = "'0'";
        for ($i = 1; $i <= 5; $i++) {
            $cadena_ofp .= ",'+" . $i . "','-" . $i . "'";
        }
        ?>
        <script id="ajax">
            var array_ofp = [<?php echo($cadena_ofp) ?>];

            $(function () {
                var Tags_ofp = [<?php echo($cadena_ofp) ?>];

                $("#txt_alto_derecha_od").autocomplete({source: Tags_ofp});
                $("#txt_alto_centro_od").autocomplete({source: Tags_ofp});
                $("#txt_alto_izquierda_od").autocomplete({source: Tags_ofp});
                $("#txt_medio_derecha_od").autocomplete({source: Tags_ofp});
                $("#txt_medio_izquierda_od").autocomplete({source: Tags_ofp});
                $("#txt_bajo_derecha_od").autocomplete({source: Tags_ofp});
                $("#txt_bajo_centro_od").autocomplete({source: Tags_ofp});
                $("#txt_bajo_izquierda_od").autocomplete({source: Tags_ofp});

                $("#txt_alto_derecha_oi").autocomplete({source: Tags_ofp});
                $("#txt_alto_centro_oi").autocomplete({source: Tags_ofp});
                $("#txt_alto_izquierda_oi").autocomplete({source: Tags_ofp});
                $("#txt_medio_derecha_oi").autocomplete({source: Tags_ofp});
                $("#txt_medio_izquierda_oi").autocomplete({source: Tags_ofp});
                $("#txt_bajo_derecha_oi").autocomplete({source: Tags_ofp});
                $("#txt_bajo_centro_oi").autocomplete({source: Tags_ofp});
                $("#txt_bajo_izquierda_oi").autocomplete({source: Tags_ofp});
            });
        </script>
        <?php
        //Lista para campos Sí/No
        $lista_si_no = array();
        $lista_si_no[0]["id"] = "1";
        $lista_si_no[0]["valor"] = "S&iacute;";
        $lista_si_no[1]["id"] = "0";
        $lista_si_no[1]["valor"] = "No";

        //Lista de valores de corrección
        $lista_correccion = $dbListas->getListaDetalles(49, 1);

        //Lista de valores de ojos
        $lista_ojos = $dbListas->getListaDetalles(14, 1);

        //Lista de valores de luces de Worth
        $lista_worth = $dbListas->getListaDetalles(50, 1);

        //Lista de valores de estereopsis
        $lista_estereopsis = $dbListas->getListaDetalles(51, 1);

        //Lista de valores de rejilla de Maddox
        $lista_maddox = $dbListas->getListaDetalles(52, 1);

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
        <script type="text/javascript">
            $(function () {
                var Tags_diagnosticos = [<?php echo($cadena_diagnosticos) ?>];

                for (k = 1; k <= 10; k++) {
                    $("#txt_busca_diagnostico_" + k).autocomplete({source: Tags_diagnosticos});
                }
            });
        </script>
        <script type="text/javascript">
            $(function () {
                $("#color_muscular").colorPicker({showHexField: false});
                $("#color_tonometria_od").colorPicker({showHexField: false});
                $("#color_tonometria_oi").colorPicker({showHexField: false});
            });
        </script>
        <?php
        //Array valores de cristalino
        $cadena_cristalino = "'Catarata','Catarata N','Catarata C','Catarata P'";
        for ($i = 1; $i <= 5; $i++) {
            $cadena_cristalino .= ",'Catarata N" . $i . "'";
        }
        for ($i = 1; $i <= 5; $i++) {
            $cadena_cristalino .= ",'Catarata C" . $i . "'";
        }
        for ($i = 1; $i <= 5; $i++) {
            $cadena_cristalino .= ",'Catarata P" . $i . "'";
        }
        ?>
        <script type="text/javascript">
            var array_cristalino = [<?php echo($cadena_cristalino) ?>];

            $(function () {
                var Tags_cristalino = [<?php echo($cadena_cristalino) ?>];

                //Para cristalino OD
                $("#biomi_cristalino_od").autocomplete({source: Tags_cristalino});

                //Para cristalino OI
                $("#biomi_cristalino_oi").autocomplete({source: Tags_cristalino});
            });
        </script>
    </head>
    <body onload="ajustar_textareas();
            ocultar_panels_oftalmologia();">
              <?php

              //Devuelve el valor del antecedente medico a partir del id enviado 
              function obtener_antecedente_medico($tabla_hc_antecedentes_medicos, $id_antecedente_medico) {
                  $valor = "";
                  foreach ($tabla_hc_antecedentes_medicos as $fila_hc_antecedentes_medicos) {
                      $id_hc_antecedentes_medicos = $fila_hc_antecedentes_medicos["id_antecedentes_medicos"];
                      if ($id_antecedente_medico == $id_hc_antecedentes_medicos) {
                          $valor = $fila_hc_antecedentes_medicos["valor_antecedentes_medicos"];
                          break;
                      }
                  }
                  return $valor;
              }

              //Funcion para cambiar el texto por un valor diferente
              function cambiar_texto($texto, $val_anterior, $val_siguiente) {
                  if ($texto == $val_anterior) {
                      $valor = $val_siguiente;
                  } else {
                      $valor = $texto;
                  }
                  return $valor;
              }

              $contenido->validar_seguridad(0);
              if (!isset($_POST["tipo_entrada"])) {
                  $contenido->cabecera_html();
              }

              $id_usuario_crea = $_SESSION["idUsuario"];
              $id_menu_base = 21;

              //Variables de Oftalmologia
              if (isset($_POST["hdd_id_paciente"])) {
                  $id_paciente = $_POST["hdd_id_paciente"];
                  $nombre_paciente = $_POST["hdd_nombre_paciente"];
                  $id_admision = $_POST["hdd_id_admision"];

                  $ind_preconsulta = "0";
                  if (isset($_GET["ind_preconsulta"])) {
                      $ind_preconsulta = $_GET["ind_preconsulta"];
                  }

                  //Se obtienen los datos de la admision
                  $admision_obj = $dbAdmision->get_admision($id_admision);
                  $id_tipo_cita = $admision_obj["id_tipo_cita"];

                  //Se busca el tipo de registro de la atención
                  $tipo_registro_hc_obj = $dbTiposRegistrosHc->getTipoRegistroHcCitaMenu($id_tipo_cita, $id_menu_base);
                  $id_tipo_reg = $tipo_registro_hc_obj["id_tipo_reg"];

                  //Se obtienen los datos del tipo de cita
                  $tipo_cita_obj = $dbTiposCitas->get_tipo_cita($id_tipo_cita);
                  $tipo_cita_det_obj = $dbTiposCitasDetalle->get_tipos_citas_detalle($id_tipo_cita, $id_tipo_reg);
                  $tipo_reg_adicional = $tipo_cita_det_obj["tipo_reg_adicional"];

                  if (!isset($_POST["tipo_entrada"])) {
                      $tabla_hc = $dbConsultaOftalmologia->getHistoriaClinicaAdmision($id_admision, $id_tipo_reg);
                  } else {
                      $id_hc = $_POST["hdd_id_hc"];
                      $tabla_hc = $dbConsultaOftalmologia->getHistoriaClinicaId($id_hc);
                  }

                  if (count($tabla_hc) > 0) { //Si se encuentra una HC para este paciente se carga los datos de lo contrario se crea la HC
                      $tipo_accion = "2"; //Editar consulta oftalmologia
                      $id_hc_consulta = $tabla_hc["id_hc"];

                      //se obtiene el registro de la consulta de oftalmologia a partir del ID de la Historia Clinica 
                      $tabla_oftalmologia = $dbConsultaOftalmologia->getConsultaOftalmologia($id_hc_consulta);

                      $enfermedad_actual = $tabla_oftalmologia["enfermedad_actual"];
                      $muscular_balance = $tabla_oftalmologia["muscular_balance"];
                      $muscular_motilidad = $tabla_oftalmologia["muscular_motilidad"];
                      $muscular_ppc = $tabla_oftalmologia["muscular_ppc"];
                      $biomi_orbita_parpados_od = $tabla_oftalmologia["biomi_orbita_parpados_od"];
                      $biomi_sist_lagrimal_od = $tabla_oftalmologia["biomi_sist_lagrimal_od"];
                      $biomi_conjuntiva_od = $tabla_oftalmologia["biomi_conjuntiva_od"];
                      $biomi_cornea_od = $tabla_oftalmologia["biomi_cornea_od"];
                      $biomi_cam_anterior_od = $tabla_oftalmologia["biomi_cam_anterior_od"];
                      $biomi_iris_od = $tabla_oftalmologia["biomi_iris_od"];
                      $biomi_cristalino_od = $tabla_oftalmologia["biomi_cristalino_od"];
                      $biomi_vanherick_od = $tabla_oftalmologia["biomi_vanherick_od"];
                      $biomi_orbita_parpados_oi = $tabla_oftalmologia["biomi_orbita_parpados_oi"];
                      $biomi_sist_lagrimal_oi = $tabla_oftalmologia["biomi_sist_lagrimal_oi"];
                      $biomi_conjuntiva_oi = $tabla_oftalmologia["biomi_conjuntiva_oi"];
                      $biomi_cornea_oi = $tabla_oftalmologia["biomi_cornea_oi"];
                      $biomi_cam_anterior_oi = $tabla_oftalmologia["biomi_cam_anterior_oi"];
                      $biomi_iris_oi = $tabla_oftalmologia["biomi_iris_oi"];
                      $biomi_cristalino_oi = $tabla_oftalmologia["biomi_cristalino_oi"];
                      $biomi_vanherick_oi = $tabla_oftalmologia["biomi_vanherick_oi"];
                      $goniosco_superior_od = $tabla_oftalmologia["goniosco_superior_od"];
                      $goniosco_inferior_od = $tabla_oftalmologia["goniosco_inferior_od"];
                      $goniosco_nasal_od = $tabla_oftalmologia["goniosco_nasal_od"];
                      $goniosco_temporal_od = $tabla_oftalmologia["goniosco_temporal_od"];
                      $goniosco_superior_oi = $tabla_oftalmologia["goniosco_superior_oi"];
                      $goniosco_inferior_oi = $tabla_oftalmologia["goniosco_inferior_oi"];
                      $goniosco_nasal_oi = $tabla_oftalmologia["goniosco_nasal_oi"];
                      $goniosco_temporal_oi = $tabla_oftalmologia["goniosco_temporal_oi"];
                      $tonometria_nervio_optico_od = $tabla_oftalmologia["tonometria_nervio_optico_od"];
                      $tonometria_macula_od = $tabla_oftalmologia["tonometria_macula_od"];
                      $tonometria_periferia_od = $tabla_oftalmologia["tonometria_periferia_od"];
                      $tonometria_vitreo_od = $tabla_oftalmologia["tonometria_vitreo_od"];
                      $tonometria_nervio_optico_oi = $tabla_oftalmologia["tonometria_nervio_optico_oi"];
                      $tonometria_macula_oi = $tabla_oftalmologia["tonometria_macula_oi"];
                      $tonometria_periferia_oi = $tabla_oftalmologia["tonometria_periferia_oi"];
                      $tonometria_vitreo_oi = $tabla_oftalmologia["tonometria_vitreo_oi"];
                      $diagnostico_oftalmo = $tabla_oftalmologia["diagnostico_oftalmo"];
                      $solicitud_examenes = $tabla_oftalmologia["solicitud_examenes"];
                      $tratamiento_oftalmo = $tabla_oftalmologia["tratamiento_oftalmo"];
                      $img_biomiocroscopia = $tabla_oftalmologia["img_biomiocroscopia"];
                      $img_tonometria_od = $tabla_oftalmologia["img_tonometria_od"];
                      $img_tonometria_oi = $tabla_oftalmologia["img_tonometria_oi"];
                      $observaciones_gonioscopia = $tabla_oftalmologia["observaciones_gonioscopia"];
                      $medicamentos_oftalmo = $tabla_oftalmologia["medicamentos_oftalmo"];
                      $nombre_usuario_alt = $tabla_hc["nombre_usuario_alt"];
                      $nombre_usuario_preconsulta = $tabla_hc["nombre_usuario_preconsulta"];
                      $observaciones_tonometria = $tabla_oftalmologia["observaciones_tonometria"];
                      $ind_antecedentes_ant = $tabla_oftalmologia["ind_antecedentes_ant"];
                      $ind_formula_gafas = $tabla_oftalmologia["ind_formula_gafas"];
                      $ind_eval_muscular = $tabla_oftalmologia["ind_eval_muscular"];

                      if ($tipo_reg_adicional == "1" || $ind_eval_muscular == "1") {
                          //Se cargan los datos de oftalmología pediátrica
                          $consulta_ofp_obj = $dbConsultaOftalmologia->getConsultaOftalmologiaPediatrica($id_hc_consulta);

                          $metodo_ofp = $consulta_ofp_obj["metodo_ofp"];
                          $ind_ortotropia = $consulta_ofp_obj["ind_ortotropia"];
                          $id_correccion = $consulta_ofp_obj["id_correccion"];
                          $id_ojo_fijador = $consulta_ofp_obj["id_ojo_fijador"];
                          $lejos_h = $consulta_ofp_obj["lejos_h"];
                          $lejos_h_delta = $consulta_ofp_obj["lejos_h_delta"];
                          $lejos_v = $consulta_ofp_obj["lejos_v"];
                          $lejos_v_delta = $consulta_ofp_obj["lejos_v_delta"];
                          $cerca_h = $consulta_ofp_obj["cerca_h"];
                          $cerca_h_delta = $consulta_ofp_obj["cerca_h_delta"];
                          $cerca_v = $consulta_ofp_obj["cerca_v"];
                          $cerca_v_delta = $consulta_ofp_obj["cerca_v_delta"];
                          $cerca_c_h = $consulta_ofp_obj["cerca_c_h"];
                          $cerca_c_h_delta = $consulta_ofp_obj["cerca_c_h_delta"];
                          $cerca_c_v = $consulta_ofp_obj["cerca_c_v"];
                          $cerca_c_v_delta = $consulta_ofp_obj["cerca_c_v_delta"];
                          $cerca_b_h = $consulta_ofp_obj["cerca_b_h"];
                          $cerca_b_h_delta = $consulta_ofp_obj["cerca_b_h_delta"];
                          $cerca_b_v = $consulta_ofp_obj["cerca_b_v"];
                          $cerca_b_v_delta = $consulta_ofp_obj["cerca_b_v_delta"];
                          $derecha_alto_h = $consulta_ofp_obj["derecha_alto_h"];
                          $derecha_alto_h_delta = $consulta_ofp_obj["derecha_alto_h_delta"];
                          $derecha_alto_v = $consulta_ofp_obj["derecha_alto_v"];
                          $derecha_alto_v_delta = $consulta_ofp_obj["derecha_alto_v_delta"];
                          $derecha_medio_h = $consulta_ofp_obj["derecha_medio_h"];
                          $derecha_medio_h_delta = $consulta_ofp_obj["derecha_medio_h_delta"];
                          $derecha_medio_v = $consulta_ofp_obj["derecha_medio_v"];
                          $derecha_medio_v_delta = $consulta_ofp_obj["derecha_medio_v_delta"];
                          $derecha_bajo_h = $consulta_ofp_obj["derecha_bajo_h"];
                          $derecha_bajo_h_delta = $consulta_ofp_obj["derecha_bajo_h_delta"];
                          $derecha_bajo_v = $consulta_ofp_obj["derecha_bajo_v"];
                          $derecha_bajo_v_delta = $consulta_ofp_obj["derecha_bajo_v_delta"];
                          $centro_alto_h = $consulta_ofp_obj["centro_alto_h"];
                          $centro_alto_h_delta = $consulta_ofp_obj["centro_alto_h_delta"];
                          $centro_alto_v = $consulta_ofp_obj["centro_alto_v"];
                          $centro_alto_v_delta = $consulta_ofp_obj["centro_alto_v_delta"];
                          $centro_medio_h = $consulta_ofp_obj["centro_medio_h"];
                          $centro_medio_h_delta = $consulta_ofp_obj["centro_medio_h_delta"];
                          $centro_medio_v = $consulta_ofp_obj["centro_medio_v"];
                          $centro_medio_v_delta = $consulta_ofp_obj["centro_medio_v_delta"];
                          $centro_bajo_h = $consulta_ofp_obj["centro_bajo_h"];
                          $centro_bajo_h_delta = $consulta_ofp_obj["centro_bajo_h_delta"];
                          $centro_bajo_v = $consulta_ofp_obj["centro_bajo_v"];
                          $centro_bajo_v_delta = $consulta_ofp_obj["centro_bajo_v_delta"];
                          $izquierda_alto_h = $consulta_ofp_obj["izquierda_alto_h"];
                          $izquierda_alto_h_delta = $consulta_ofp_obj["izquierda_alto_h_delta"];
                          $izquierda_alto_v = $consulta_ofp_obj["izquierda_alto_v"];
                          $izquierda_alto_v_delta = $consulta_ofp_obj["izquierda_alto_v_delta"];
                          $izquierda_medio_h = $consulta_ofp_obj["izquierda_medio_h"];
                          $izquierda_medio_h_delta = $consulta_ofp_obj["izquierda_medio_h_delta"];
                          $izquierda_medio_v = $consulta_ofp_obj["izquierda_medio_v"];
                          $izquierda_medio_v_delta = $consulta_ofp_obj["izquierda_medio_v_delta"];
                          $izquierda_bajo_h = $consulta_ofp_obj["izquierda_bajo_h"];
                          $izquierda_bajo_h_delta = $consulta_ofp_obj["izquierda_bajo_h_delta"];
                          $izquierda_bajo_v = $consulta_ofp_obj["izquierda_bajo_v"];
                          $izquierda_bajo_v_delta = $consulta_ofp_obj["izquierda_bajo_v_delta"];
                          $alto_derecha_od = $consulta_ofp_obj["alto_derecha_od"];
                          $alto_centro_od = $consulta_ofp_obj["alto_centro_od"];
                          $alto_izquierda_od = $consulta_ofp_obj["alto_izquierda_od"];
                          $medio_derecha_od = $consulta_ofp_obj["medio_derecha_od"];
                          $medio_izquierda_od = $consulta_ofp_obj["medio_izquierda_od"];
                          $bajo_derecha_od = $consulta_ofp_obj["bajo_derecha_od"];
                          $bajo_centro_od = $consulta_ofp_obj["bajo_centro_od"];
                          $bajo_izquierda_od = $consulta_ofp_obj["bajo_izquierda_od"];
                          $dvd_od = $consulta_ofp_obj["dvd_od"];
                          $alto_derecha_oi = $consulta_ofp_obj["alto_derecha_oi"];
                          $alto_centro_oi = $consulta_ofp_obj["alto_centro_oi"];
                          $alto_izquierda_oi = $consulta_ofp_obj["alto_izquierda_oi"];
                          $medio_derecha_oi = $consulta_ofp_obj["medio_derecha_oi"];
                          $medio_izquierda_oi = $consulta_ofp_obj["medio_izquierda_oi"];
                          $bajo_derecha_oi = $consulta_ofp_obj["bajo_derecha_oi"];
                          $bajo_centro_oi = $consulta_ofp_obj["bajo_centro_oi"];
                          $bajo_izquierda_oi = $consulta_ofp_obj["bajo_izquierda_oi"];
                          $dvd_oi = $consulta_ofp_obj["dvd_oi"];
                          $observaciones_oft_pediat = $consulta_ofp_obj["observaciones_oft_pediat"];
                          $inclinacion_der_h = $consulta_ofp_obj["inclinacion_der_h"];
                          $inclinacion_der_h_delta = $consulta_ofp_obj["inclinacion_der_h_delta"];
                          $inclinacion_der_v = $consulta_ofp_obj["inclinacion_der_v"];
                          $inclinacion_der_v_delta = $consulta_ofp_obj["inclinacion_der_v_delta"];
                          $inclinacion_izq_h = $consulta_ofp_obj["inclinacion_izq_h"];
                          $inclinacion_izq_h_delta = $consulta_ofp_obj["inclinacion_izq_h_delta"];
                          $inclinacion_izq_v = $consulta_ofp_obj["inclinacion_izq_v"];
                          $inclinacion_izq_v_delta = $consulta_ofp_obj["inclinacion_izq_v_delta"];
                          $ind_nistagmo = $consulta_ofp_obj["ind_nistagmo"];
                          $texto_nistagmo = $consulta_ofp_obj["texto_nistagmo"];
                          $ind_pac = $consulta_ofp_obj["ind_pac"];
                          $texto_pac = $consulta_ofp_obj["texto_pac"];
                          $conv_fusional_lejos = $consulta_ofp_obj["conv_fusional_lejos"];
                          $conv_fusional_cerca = $consulta_ofp_obj["conv_fusional_cerca"];
                          $div_fusional_lejos = $consulta_ofp_obj["div_fusional_lejos"];
                          $div_fusional_cerca = $consulta_ofp_obj["div_fusional_cerca"];
                          $id_worth_lejos = $consulta_ofp_obj["id_worth_lejos"];
                          $id_worth_cerca = $consulta_ofp_obj["id_worth_cerca"];
                          $id_estereopsis_mosca = $consulta_ofp_obj["id_estereopsis_mosca"];
                          $valor_estereopsis_animales = $consulta_ofp_obj["valor_estereopsis_animales"];
                          $valor_estereopsis_circulos = $consulta_ofp_obj["valor_estereopsis_circulos"];
                          $id_maddox_der = $consulta_ofp_obj["id_maddox_der"];
                          $valor_maddox_der = $consulta_ofp_obj["valor_maddox_der"];
                          $id_maddox_izq = $consulta_ofp_obj["id_maddox_izq"];
                          $valor_maddox_izq = $consulta_ofp_obj["valor_maddox_izq"];
                      } else {
                          $metodo_ofp = "";
                          $ind_ortotropia = "";
                          $id_correccion = "";
                          $id_ojo_fijador = "";
                          $lejos_h = "";
                          $lejos_h_delta = "";
                          $lejos_v = "";
                          $lejos_v_delta = "";
                          $cerca_h = "";
                          $cerca_h_delta = "";
                          $cerca_v = "";
                          $cerca_v_delta = "";
                          $cerca_c_h = "";
                          $cerca_c_h_delta = "";
                          $cerca_c_v = "";
                          $cerca_c_v_delta = "";
                          $cerca_b_h = "";
                          $cerca_b_h_delta = "";
                          $cerca_b_v = "";
                          $cerca_b_v_delta = "";
                          $derecha_alto_h = "";
                          $derecha_alto_h_delta = "";
                          $derecha_alto_v = "";
                          $derecha_alto_v_delta = "";
                          $derecha_medio_h = "";
                          $derecha_medio_h_delta = "";
                          $derecha_medio_v = "";
                          $derecha_medio_v_delta = "";
                          $derecha_bajo_h = "";
                          $derecha_bajo_h_delta = "";
                          $derecha_bajo_v = "";
                          $derecha_bajo_v_delta = "";
                          $centro_alto_h = "";
                          $centro_alto_h_delta = "";
                          $centro_alto_v = "";
                          $centro_alto_v_delta = "";
                          $centro_medio_h = "";
                          $centro_medio_h_delta = "";
                          $centro_medio_v = "";
                          $centro_medio_v_delta = "";
                          $centro_bajo_h = "";
                          $centro_bajo_h_delta = "";
                          $centro_bajo_v = "";
                          $centro_bajo_v_delta = "";
                          $izquierda_alto_h = "";
                          $izquierda_alto_h_delta = "";
                          $izquierda_alto_v = "";
                          $izquierda_alto_v_delta = "";
                          $izquierda_medio_h = "";
                          $izquierda_medio_h_delta = "";
                          $izquierda_medio_v = "";
                          $izquierda_medio_v_delta = "";
                          $izquierda_bajo_h = "";
                          $izquierda_bajo_h_delta = "";
                          $izquierda_bajo_v = "";
                          $izquierda_bajo_v_delta = "";
                          $alto_derecha_od = "";
                          $alto_centro_od = "";
                          $alto_izquierda_od = "";
                          $medio_derecha_od = "";
                          $medio_izquierda_od = "";
                          $bajo_derecha_od = "";
                          $bajo_centro_od = "";
                          $bajo_izquierda_od = "";
                          $dvd_od = "";
                          $alto_derecha_oi = "";
                          $alto_centro_oi = "";
                          $alto_izquierda_oi = "";
                          $medio_derecha_oi = "";
                          $medio_izquierda_oi = "";
                          $bajo_derecha_oi = "";
                          $bajo_centro_oi = "";
                          $bajo_izquierda_oi = "";
                          $dvd_oi = "";
                          $observaciones_oft_pediat = "";
                          $inclinacion_der_h = "";
                          $inclinacion_der_h_delta = "";
                          $inclinacion_der_v = "";
                          $inclinacion_der_v_delta = "";
                          $inclinacion_izq_h = "";
                          $inclinacion_izq_h_delta = "";
                          $inclinacion_izq_v = "";
                          $inclinacion_izq_v_delta = "";
                          $ind_nistagmo = "";
                          $texto_nistagmo = "";
                          $ind_pac = "";
                          $texto_pac = "";
                          $conv_fusional_lejos = "";
                          $conv_fusional_cerca = "";
                          $div_fusional_lejos = "";
                          $div_fusional_cerca = "";
                          $id_worth_lejos = "";
                          $id_worth_cerca = "";
                          $id_estereopsis_mosca = "";
                          $valor_estereopsis_animales = "";
                          $valor_estereopsis_circulos = "";
                          $id_maddox_der = "";
                          $valor_maddox_der = "";
                          $id_maddox_izq = "";
                          $valor_maddox_izq = "";
                      }

                      //Se verifica si se debe actualizar el estado de la admisión asociada
                      $en_atencion = "0";
                      if (isset($_POST["hdd_en_atencion"])) {
                          $en_atencion = $_POST["hdd_en_atencion"];
                      }

                      if ($en_atencion == "1") {
                          $id_estado_atencion_aux = "6";
                          if ($ind_preconsulta == "1") {
                              $id_estado_atencion_aux = "12";
                          }
                          $dbAdmision->editar_admision_estado($id_admision, $id_estado_atencion_aux, 1, $id_usuario_crea);
                      }
                  } else {
                      $tipo_accion = "1"; //Crear consulta de oftalmología
                      //Se crea la historia clínica y se inicia la consulta de oftalmología
                      $id_hc_consulta = $dbConsultaOftalmologia->CrearConsultaOftalmologia($id_paciente, $id_tipo_reg, $id_usuario_crea, $id_admision, $ind_preconsulta);

                      if ($id_hc_consulta < 0) { //Ninguna accion Error
                          $tipo_accion = "0";
                      } else {
                          $tabla_oftalmologia = $dbConsultaOftalmologia->getConsultaOftalmologia($id_hc_consulta);

                          //Variables de inicio de consulta de oftalmología
                          $enfermedad_actual = $tabla_oftalmologia["enfermedad_actual"];
                          $muscular_balance = $tabla_oftalmologia["muscular_balance"];
                          $muscular_motilidad = $tabla_oftalmologia["muscular_motilidad"];
                          $muscular_ppc = $tabla_oftalmologia["muscular_ppc"];
                          $biomi_orbita_parpados_od = $tabla_oftalmologia["biomi_orbita_parpados_od"];
                          $biomi_sist_lagrimal_od = $tabla_oftalmologia["biomi_sist_lagrimal_od"];
                          $biomi_conjuntiva_od = $tabla_oftalmologia["biomi_conjuntiva_od"];
                          $biomi_cornea_od = $tabla_oftalmologia["biomi_cornea_od"];
                          $biomi_cam_anterior_od = $tabla_oftalmologia["biomi_cam_anterior_od"];
                          $biomi_iris_od = $tabla_oftalmologia["biomi_iris_od"];
                          $biomi_cristalino_od = $tabla_oftalmologia["biomi_cristalino_od"];
                          $biomi_vanherick_od = $tabla_oftalmologia["biomi_vanherick_od"];
                          $biomi_orbita_parpados_oi = $tabla_oftalmologia["biomi_orbita_parpados_oi"];
                          $biomi_sist_lagrimal_oi = $tabla_oftalmologia["biomi_sist_lagrimal_oi"];
                          $biomi_conjuntiva_oi = $tabla_oftalmologia["biomi_conjuntiva_oi"];
                          $biomi_cornea_oi = $tabla_oftalmologia["biomi_cornea_oi"];
                          $biomi_cam_anterior_oi = $tabla_oftalmologia["biomi_cam_anterior_oi"];
                          $biomi_iris_oi = $tabla_oftalmologia["biomi_iris_oi"];
                          $biomi_cristalino_oi = $tabla_oftalmologia["biomi_cristalino_oi"];
                          $biomi_vanherick_oi = $tabla_oftalmologia["biomi_vanherick_oi"];
                          $goniosco_superior_od = $tabla_oftalmologia["goniosco_superior_od"];
                          $goniosco_inferior_od = $tabla_oftalmologia["goniosco_inferior_od"];
                          $goniosco_nasal_od = $tabla_oftalmologia["goniosco_nasal_od"];
                          $goniosco_temporal_od = $tabla_oftalmologia["goniosco_temporal_od"];
                          $goniosco_superior_oi = $tabla_oftalmologia["goniosco_superior_oi"];
                          $goniosco_inferior_oi = $tabla_oftalmologia["goniosco_inferior_oi"];
                          $goniosco_nasal_oi = $tabla_oftalmologia["goniosco_nasal_oi"];
                          $goniosco_temporal_oi = $tabla_oftalmologia["goniosco_temporal_oi"];
                          $tonometria_nervio_optico_od = $tabla_oftalmologia["tonometria_nervio_optico_od"];
                          $tonometria_macula_od = $tabla_oftalmologia["tonometria_macula_od"];
                          $tonometria_periferia_od = $tabla_oftalmologia["tonometria_periferia_od"];
                          $tonometria_vitreo_od = $tabla_oftalmologia["tonometria_vitreo_od"];
                          $tonometria_nervio_optico_oi = $tabla_oftalmologia["tonometria_nervio_optico_oi"];
                          $tonometria_macula_oi = $tabla_oftalmologia["tonometria_macula_oi"];
                          $tonometria_periferia_oi = $tabla_oftalmologia["tonometria_periferia_oi"];
                          $tonometria_vitreo_oi = $tabla_oftalmologia["tonometria_vitreo_oi"];
                          $diagnostico_oftalmo = $tabla_oftalmologia["diagnostico_oftalmo"];
                          $solicitud_examenes = $tabla_oftalmologia["solicitud_examenes"];
                          $tratamiento_oftalmo = $tabla_oftalmologia["tratamiento_oftalmo"];
                          $img_biomiocroscopia = $tabla_oftalmologia["img_biomiocroscopia"];
                          $img_tonometria_od = $tabla_oftalmologia["img_tonometria_od"];
                          $img_tonometria_oi = $tabla_oftalmologia["img_tonometria_oi"];
                          $observaciones_gonioscopia = $tabla_oftalmologia["observaciones_gonioscopia"];
                          $medicamentos_oftalmo = $tabla_oftalmologia["medicamentos_oftalmo"];
                          $nombre_usuario_alt = $tabla_hc["nombre_usuario_alt"];
                          $nombre_usuario_preconsulta = $tabla_hc["nombre_usuario_preconsulta"];
                          $observaciones_tonometria = $tabla_oftalmologia["observaciones_tonometria"];
                          $ind_antecedentes_ant = "0";
                          $ind_formula_gafas = $tabla_oftalmologia["ind_formula_gafas"];
                          $ind_eval_muscular = $tabla_oftalmologia["ind_eval_muscular"];

                          //Campos de evaluación muscular
                          $metodo_ofp = "";
                          $ind_ortotropia = "";
                          $id_correccion = "";
                          $id_ojo_fijador = "";
                          $lejos_h = "";
                          $lejos_h_delta = "";
                          $lejos_v = "";
                          $lejos_v_delta = "";
                          $cerca_h = "";
                          $cerca_h_delta = "";
                          $cerca_v = "";
                          $cerca_v_delta = "";
                          $cerca_c_h = "";
                          $cerca_c_h_delta = "";
                          $cerca_c_v = "";
                          $cerca_c_v_delta = "";
                          $cerca_b_h = "";
                          $cerca_b_h_delta = "";
                          $cerca_b_v = "";
                          $cerca_b_v_delta = "";
                          $derecha_alto_h = "";
                          $derecha_alto_h_delta = "";
                          $derecha_alto_v = "";
                          $derecha_alto_v_delta = "";
                          $derecha_medio_h = "";
                          $derecha_medio_h_delta = "";
                          $derecha_medio_v = "";
                          $derecha_medio_v_delta = "";
                          $derecha_bajo_h = "";
                          $derecha_bajo_h_delta = "";
                          $derecha_bajo_v = "";
                          $derecha_bajo_v_delta = "";
                          $centro_alto_h = "";
                          $centro_alto_h_delta = "";
                          $centro_alto_v = "";
                          $centro_alto_v_delta = "";
                          $centro_medio_h = "";
                          $centro_medio_h_delta = "";
                          $centro_medio_v = "";
                          $centro_medio_v_delta = "";
                          $centro_bajo_h = "";
                          $centro_bajo_h_delta = "";
                          $centro_bajo_v = "";
                          $centro_bajo_v_delta = "";
                          $izquierda_alto_h = "";
                          $izquierda_alto_h_delta = "";
                          $izquierda_alto_v = "";
                          $izquierda_alto_v_delta = "";
                          $izquierda_medio_h = "";
                          $izquierda_medio_h_delta = "";
                          $izquierda_medio_v = "";
                          $izquierda_medio_v_delta = "";
                          $izquierda_bajo_h = "";
                          $izquierda_bajo_h_delta = "";
                          $izquierda_bajo_v = "";
                          $izquierda_bajo_v_delta = "";
                          $alto_derecha_od = "";
                          $alto_centro_od = "";
                          $alto_izquierda_od = "";
                          $medio_derecha_od = "";
                          $medio_izquierda_od = "";
                          $bajo_derecha_od = "";
                          $bajo_centro_od = "";
                          $bajo_izquierda_od = "";
                          $dvd_od = "";
                          $alto_derecha_oi = "";
                          $alto_centro_oi = "";
                          $alto_izquierda_oi = "";
                          $medio_derecha_oi = "";
                          $medio_izquierda_oi = "";
                          $bajo_derecha_oi = "";
                          $bajo_centro_oi = "";
                          $bajo_izquierda_oi = "";
                          $dvd_oi = "";
                          $observaciones_oft_pediat = "";
                          $inclinacion_der_h = "";
                          $inclinacion_der_h_delta = "";
                          $inclinacion_der_v = "";
                          $inclinacion_der_v_delta = "";
                          $inclinacion_izq_h = "";
                          $inclinacion_izq_h_delta = "";
                          $inclinacion_izq_v = "";
                          $inclinacion_izq_v_delta = "";
                          $ind_nistagmo = "";
                          $texto_nistagmo = "";
                          $ind_pac = "";
                          $texto_pac = "";
                          $conv_fusional_lejos = "";
                          $conv_fusional_cerca = "";
                          $div_fusional_lejos = "";
                          $div_fusional_cerca = "";
                          $id_worth_lejos = "";
                          $id_worth_cerca = "";
                          $id_estereopsis_mosca = "";
                          $valor_estereopsis_animales = "";
                          $valor_estereopsis_circulos = "";
                          $id_maddox_der = "";
                          $valor_maddox_der = "";
                          $id_maddox_izq = "";
                          $valor_maddox_izq = "";
                      }
                  }

                  //Se obtienen los datos del registro de historia clínica
                  $historia_clinica_obj = $dbConsultaOftalmologia->getHistoriaClinicaId($id_hc_consulta);
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

              //Obtener los datos de la consulta de optometria
              $datos_optometria = $dbConsultaOftalmologia->getOptometriaPaciente($id_paciente, $id_admision);
              $id_hc_optometria = $datos_optometria["id_hc"];

              //Listado de valores para ESCALA DE VAN HERICK
              $tabla_vanherick = array();
              $tabla_vanherick[0][0] = "I";
              $tabla_vanherick[0][1] = "I";
              $tabla_vanherick[1][0] = "II";
              $tabla_vanherick[1][1] = "II";
              $tabla_vanherick[2][0] = "III";
              $tabla_vanherick[2][1] = "III";
              $tabla_vanherick[3][0] = "IV";
              $tabla_vanherick[3][1] = "IV";

              //Listado de valores para GONIOSCOPIA
              $tabla_gonioscopia = array();
              $tabla_gonioscopia[0][0] = "0";
              $tabla_gonioscopia[0][1] = "0";
              $tabla_gonioscopia[1][0] = "1";
              $tabla_gonioscopia[1][1] = "1";
              $tabla_gonioscopia[2][0] = "2";
              $tabla_gonioscopia[2][1] = "2";
              $tabla_gonioscopia[3][0] = "3";
              $tabla_gonioscopia[3][1] = "3";
              $tabla_gonioscopia[4][0] = "4";
              $tabla_gonioscopia[4][1] = "4";

              //Datos del paciente
              $datos_paciente = $dbPacientes->getEdadPaciente($id_paciente, "");
              $edad_paciente = $datos_paciente["edad"];
              $profesion_paciente = $datos_paciente["profesion"];

              //Nombre del profesional que atiende la consulta
              $id_usuario_profesional = $tabla_oftalmologia["id_usuario_crea"];
              $usuario_profesional_obj = $dbUsuarios->getUsuario($id_usuario_profesional);
              $nombre_usuario_profesional = $usuario_profesional_obj["nombre_usuario"] . " " . $usuario_profesional_obj["apellido_usuario"];
              if (!isset($_POST["tipo_entrada"])) {
                  ?>
            <div class="title-bar title_hc">
                <div class="wrapper">
                    <div class="breadcrumb">
                        <ul>
                            <li class="breadcrumb_on">Consulta de Oftalmolog&iacute;a</li>
                        </ul>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
        <?php
        if ($tipo_accion > 0) {
            /*             * ***************************************************** */
            /* Variable que contiene la cantidad de campos de colores */
            /*             * ***************************************************** */
            $cantidad_campos_colores = 130;

            //Se obtiene el listado de colores
            $arr_colores = array();
            $arr_cadenas_colores = array();
            $lista_cadenas = $dbConsultaOftalmologia->getListaHistoriaClinicaColoresCampos($id_hc_consulta);
            if (count($lista_cadenas) > 0) {
                foreach ($lista_cadenas as $reg_cadena) {
                    array_push($arr_cadenas_colores, $reg_cadena["cadena_colores"]);
                }
            }

            //Se instancia la clase que administrará los colores de los campos
            $colorPick = new Color_Pick($arr_cadenas_colores, $cantidad_campos_colores);
            $arr_colores = $colorPick->getArrayColores();

            //Para verificaro que tiene permiso de hacer cambio
            $ind_editar = $dbConsultaOftalmologia->getIndicadorEdicion($id_hc_consulta, $horas_edicion["valor_variable"]);
            $ind_editar_enc_hc = $ind_editar;
            if ($ind_editar == 1 && isset($_POST["tipo_entrada"])) {
                $ind_editar_enc_hc = 0;
            }

            //Se borran las imágenes temporales creadas por el usuario actual
            $ruta_tmp = "../historia_clinica/tmp/" . $id_usuario_crea;
            /* if (file_exists($ruta_tmp)) {
              @array_map("unlink", glob($ruta_tmp."/img*.*"));
              } */

            @mkdir($ruta_tmp);

            //Se obtiene la ruta actual de las imágenes
            $arr_ruta_base = $dbVariables->getVariable(17);
            $ruta_base = $arr_ruta_base["valor_variable"];

            //Se crea una copia local de las imágenes a mostrar
            if ($img_biomiocroscopia != "") {
                $img_biomiocroscopia = str_replace("../imagenes/imagenes_hce", $ruta_base, $img_biomiocroscopia);
                @copy($img_biomiocroscopia, $ruta_tmp . "/img_biomiocroscopia_" . $id_hc_consulta . ".png");
                $img_biomiocroscopia = $ruta_tmp . "/img_biomiocroscopia_" . $id_hc_consulta . ".png";
            }
            if ($img_tonometria_od != "") {
                $img_tonometria_od = str_replace("../imagenes/imagenes_hce", $ruta_base, $img_tonometria_od);
                @copy($img_tonometria_od, $ruta_tmp . "/img_tonometria_od_" . $id_hc_consulta . ".png");
                $img_tonometria_od = $ruta_tmp . "/img_tonometria_od_" . $id_hc_consulta . ".png";
            }
            if ($img_tonometria_oi != "") {
                $img_tonometria_oi = str_replace("../imagenes/imagenes_hce", $ruta_base, $img_tonometria_oi);
                @copy($img_tonometria_oi, $ruta_tmp . "/img_tonometria_oi_" . $id_hc_consulta . ".png");
                $img_tonometria_oi = $ruta_tmp . "/img_tonometria_oi_" . $id_hc_consulta . ".png";
            }

            $funciones_hc->encabezado_historia_clinica($id_paciente, $id_admision, $id_hc_consulta, $ind_editar_enc_hc, false);
            ?>
            <div class="contenedor_principal" id="id_contenedor_principal">
                <div id="guardar_oftalmologia" style="width: 100%; display: block;">
                    <div class="contenedor_error" id="contenedor_error"></div>
                    <div class="contenedor_exito" id="contenedor_exito"></div>
                </div>
                <div class="formulario" id="principal_oftalmologia" style="width: 100%; display: block;">
                    <?php
                    //Se inserta el registro de ingreso a la historia clínica
                    $dbConsultaOftalmologia->crear_ingreso_hc($id_usuario_crea, $id_paciente, $id_admision, $id_hc_consulta, 160);
                    ?>
                    <form id="frm_consulta_oftalmologia" name="frm_consulta_oftalmologia" method="post">

                        <input type="hidden" name="hdd_id_hc_consulta" id="hdd_id_hc_consulta" value="<?php echo($id_hc_consulta); ?>" />
                        <input type="hidden" name="hdd_id_admision" id="hdd_id_admision" value="<?php echo($id_admision); ?>" />
                        <input type="hidden" name="hdd_id_paciente" id="hdd_id_paciente" value="<?php echo($id_paciente); ?>" />
                        <input type="hidden" name="hdd_id_convenio" id="hdd_id_convenio" value="<?php echo($admision_obj["id_convenio"]); ?>" />
                        <input type="hidden" name="hdd_id_plan" id="hdd_id_plan" value="<?php echo($admision_obj["id_plan"]); ?>" />
                        <input type="hidden" name="hdd_ind_preconsulta" id="hdd_ind_preconsulta" value="<?php echo($ind_preconsulta); ?>" />

                        <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:98%;">
                            <tr>
                                <th align="left" valign="top" style="width:50%;">
                                    <h6 style="margin:1px;">
                                        <input type="hidden" id="hdd_usuario_anonimo" value="<?php echo($usuario_profesional_obj["ind_anonimo"]); ?>" />
                                        <b>Profesional que atiende: </b>
                                        <?php
                                        if ($usuario_profesional_obj["ind_anonimo"] == "0") {
                                            ?>
                                            <input type="hidden" id="txt_nombre_usuario_alt" value="" />
                                            <br />
                                            <?php
                                            echo($nombre_usuario_profesional);
                                        } else {
                                            ?>
                                            <input type="text" id="txt_nombre_usuario_alt" maxlength="100" value="<?php echo($nombre_usuario_alt); ?>" style="width:90%;" onblur="trim_cadena(this);" />
                                            <?php
                                        }
                                        ?>
                                    </h6>
                                </th>
                                <th align="left" valign="top" style="width:50%;">
                                    <?php
                                    if ($ind_preconsulta == "1" || $nombre_usuario_preconsulta != "") {
                                        ?>
                                        <input type="hidden" id="hdd_ind_preconsulta_n" name="hdd_ind_preconsulta_n" value="1" />
                                        <?php
                                        $display_aux = "block";
                                    } else {
                                        ?>
                                        <input type="hidden" id="hdd_ind_preconsulta_n" name="hdd_ind_preconsulta_n" value="0" />
                                        <?php
                                        $display_aux = "none";
                                    }
                                    ?>
                                    <div style="display:<?php echo($display_aux); ?>;">
                                        <h6 style="margin:1px;">
                                            <b>M&eacute;dico que atiende preconsulta:</b>
                                            <input type="text" name="nombre_usuario_preconsulta" id="nombre_usuario_preconsulta" style="width:90%;" class="input" maxlength="100" value="<?php echo($nombre_usuario_preconsulta); ?>" onblur="trim_cadena(this);" />
                                        </h6>
                                    </div>
                                </th>
                            </tr>
                            <?php
                            if (trim($historia_clinica_obj["observaciones_remision"]) != "") {
                                ?>
                                <tr>
                                    <th align="left" colspan="2">
                                        <h6 style="margin: 1px;"><b>Observaciones de atenci&oacute;n: </b><?php echo($historia_clinica_obj["observaciones_remision"]); ?></h6>
                                    </th>
                                </tr>
                                <?php
                            }
                            ?>
                        </table>
                        <input type="hidden" id="hdd_cant_color_pick" value="<?php echo($cantidad_campos_colores); ?>" />
                        <input type="hidden" id="hdd_tipo_reg_adicional" value="<?php echo($tipo_reg_adicional); ?>" />
                        <div class="tabs-container">
                            <dl class="tabs" data-tab>
                                <dd id="panel_oft_2" class="active"><a href="#panel2-2" onclick="setTimeout(function () {
                                                ajustar_textareas();
                                            }, 100);">Enf. actual y antecedentes</a></dd>
                                <!--<dd id="panel_oft_1"><a href="#panel2-1" onclick="setTimeout(function() { ajustar_textareas(); ajustar_div_optometria(); }, 100);">Optometr&iacute;a</a></dd>-->
                                <dd id="panel_oft_3"><a href="#panel2-3" onclick="setTimeout(function () {
                                                ajustar_textareas();
                                            }, 100);">Motilidad y seg. anterior</a></dd>
                                    <?php
                                    $display_aux = "none";
                                    if ($tipo_reg_adicional == "1" || $ind_eval_muscular == "1") {
                                        $display_aux = "block";
                                    }
                                    ?>
                                <dd id="panel_oft_6" style="display:<?php echo($display_aux); ?>;"><a href="#panel2-6" onclick="setTimeout(function () {
                                                ajustar_textareas();
                                            }, 100);">Eval. muscular y sensorialidad</a></dd>
                                <dd id="panel_oft_4"><a href="#panel2-4" onclick="setTimeout(function () {
                                                ajustar_textareas();
                                            }, 100);">Fondo de ojo</a></dd>
                                <dd id="panel_oft_5"><a href="#panel2-5" onclick="setTimeout(function () {
                                                ajustar_textareas();
                                            }, 100);">Diagn&oacute;stico</a></dd>
                                <dd id="panel_oft_7"><a href="#panel2-7" onclick="setTimeout(function () {
                                                ajustar_textareas();
                                            }, 100);">Órdenes y remisiones</a></dd>
                                            
                                 <dd id="panel_oft_8"><a href="#panel2-8" onclick="setTimeout(function () {
                                                ajustar_textareas();
                                            }, 100);">Incapacidades</a></dd>
                            </dl>
                            <div class="tabs-content" style="padding:0px;margin: 0px;">
                                <!--<div class="content" id="panel2-1">
                                      <div id="div_consulta_optometria"></div>
                                <?php
								$ind_incapacidad = "";
                                /* $id_menu_aux = "13";
                                  if (isset($_POST["hdd_numero_menu"]) && trim($_POST["hdd_numero_menu"]) != "") {
                                  $id_menu_aux = $_POST["hdd_numero_menu"];
                                  } */
                                ?>
                                      <script type="text/javascript">
                                              mostrar_consulta_iframe(<?php echo($id_paciente); ?>, "<?php echo($nombre_paciente); ?>", <?php echo($id_admision); ?>, "../historia_clinica/consulta_optometria.php", <?php echo($id_hc_optometria); ?>, <?php echo($_POST["credencial"]); ?>, <?php echo($id_menu_aux); ?>, "div_consulta_optometria")
                                      </script>
                                </div>-->
                                <div class="content active" id="panel2-2">
                                    <!--INICIO ENFERMEDAD ACTUAL - ANTECEDENTES-->
                                    <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:98%;">
                                        <tr>
                                            <td align="center" colspan="3" >
                                                <h5 style="margin: 10px">Enfermedad actual *</h5>
                                                <?php
                                                $enfermedad_actual = $utilidades->ajustar_texto_wysiwyg($enfermedad_actual);
                                                ?>
                                                <div id="txt_enfermedad_actual"><?php echo($enfermedad_actual); ?></div>
                                            </td>
                                        </tr>
                                    </table>
                                    <?php
                                    require("antecedentes.php");
                                    ?>
                                    <!--FIN ENFERMEDAD ACTUAL - ANTECEDENTES-->
                                </div>
                                <div class="content active" id="panel2-3">
                                    <!--INICIO MUSCULAR-->
                                    <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:98%;">	
                                        <tr>
                                            <td align="center" colspan="3">
                                                <h5 style="margin: 5px;">Muscular</h5>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" colspan="3">
                                                <table border="0" cellpadding="3" cellspacing="0" align="left" style="width:100%;">
                                                    <tr>
                                                        <td align="right" style="width:10%;"><h5 style="margin: 0px;">Balance:</h5></td>
                                                        <td align="left" style="width:40%;">
                                                            <?php
                                                            $componenteRecOft->get_componente("muscular_balance", "", $muscular_balance, $arr_colores, 0, $colorPick, 13, 2, "", "width:80%;", "Ortoforia");
                                                            ?>
                                                        </td>
                                                        <td align="right" style="width:10%;"><h5 style="margin: 0px;">PPC:</h5></td>
                                                        <td align="left" style="width:40%;">
                                                            <textarea id="txt_muscular_ppc" nombre="txt_muscular_ppc" class="textarea componente_color_pick_<?php echo($arr_colores[2]); ?>" style="height:30px;padding:2px;width:80%;margin:0px;" onblur="trim_cadena(this);" tabindex="" ><?php echo $muscular_ppc; ?></textarea>
                                                            <?php
                                                            $colorPick->getColorPick("txt_muscular_ppc", 2, "margin:8px 0px 8px 0px;");
                                                            ?>
                                                            <script type="text/javascript">
                                                                $("#txt_muscular_ppc").textareaAutoSize();
                                                                arr_textarea_ids.push("txt_muscular_ppc");
                                                            </script>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="right" style="margin:0px;"><h5 style="margin: 0px;">Motilidad:</h5></td>
                                                        <td align="left" colspan="3">
                                                            <textarea id="txt_muscular_motilidad" nombre="txt_muscular_motilidad" class="textarea componente_color_pick_<?php echo($arr_colores[1]); ?>" style="height:30px;padding:2px;width:95%;margin:0px;" onblur="trim_cadena(this);" tabindex="" ><?php echo $muscular_motilidad; ?></textarea>
                                                            <?php
                                                            $colorPick->getColorPick("txt_muscular_motilidad", 1, "margin:8px 0px 8px 0px;");
                                                            ?>
                                                            <script type="text/javascript">
                                                                $("#txt_muscular_motilidad").textareaAutoSize();
                                                                arr_textarea_ids.push("txt_muscular_motilidad");
                                                            </script>
                                                        </td>
                                                    </tr>
                                                    <tr style="display:none;">
                                                        <td align="right" style="margin:0px;" colspan="3">
                                                            <h5 style="margin: 0px;">Agregar evaluaci&oacute;n muscular:</h5>
                                                        </td>
                                                        <td align="left" style="margin:0px;">
                                                            <input type="checkbox" id="chk_eval_muscular" class="no-margin" onchange="seleccionar_eval_muscular();" <?php if ($ind_eval_muscular == "1") { ?>checked="checked"<?php } ?> />
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" colspan="3">
                                                <br />
                                                <table border="0" cellpadding="0" cellspacing="0" align="center" style="width:98%;">
                                                    <tr>
                                                        <td align="center" colspan="3" class="td_tabla">
                                                            <div class="odoi_t">
                                                                <div class="od_t"><h5 style="margin: 0px;">OD</h5></div>
                                                                <div class="oi_t"><h5 style="margin: 0px;">OI</h5></div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <!--INICIO ANEXOS OCULARES-->
                                                <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:98%;">
                                                    <tr>
                                                        <td align="center" colspan="3" >
                                                            <h5 style="margin: 0px">Anexos oculares</h5>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <table border="0" cellpadding="3" cellspacing="0" align="left" style="width:100%;">
                                                    <tr>
                                                        <td align="center" colspan="3">
                                                            <img src="../imagenes/sano_hc_24.png" class="img_button no-margin" style="display:inline-block;" onclick="marcar_sano_oft_todos(3);" />
                                                            <label style="display:inline-block;"><b>Marcar vac&iacute;os como normales</b></label>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:41%;"></td>
                                                        <td align="center" style="width:18%;"></td>
                                                        <td style="width:41%;"></td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center">
                                                            <?php
                                                            $componenteRecOft->get_componente("biomi_orbita_parpados", "od", $biomi_orbita_parpados_od, $arr_colores, 5, $colorPick, 1, 2, "", "width:80%;");
                                                            ?>
                                                        </td>
                                                        <td align="center" style="width:150px;margin-top: 5px;">
                                                            <label><b>&Oacute;rbita y P&aacute;rpados *</b></label>
                                                        </td>
                                                        <td align="center">
                                                            <?php
                                                            $componenteRecOft->get_componente("biomi_orbita_parpados", "oi", $biomi_orbita_parpados_oi, $arr_colores, 6, $colorPick, 1, 2, "", "width:80%;");
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center">
                                                            <?php
                                                            $componenteRecOft->get_componente("biomi_sist_lagrimal", "od", $biomi_sist_lagrimal_od, $arr_colores, 7, $colorPick, 2, 2, "", "width:80%;");
                                                            ?>
                                                        </td>	
                                                        <td align="center" style="width:150px;margin-top: 5px;"><label><b>Sistema Lagrimal *</b></label></td>
                                                        <td align="center">
                                                            <?php
                                                            $componenteRecOft->get_componente("biomi_sist_lagrimal", "oi", $biomi_sist_lagrimal_oi, $arr_colores, 8, $colorPick, 2, 2, "", "width:80%;");
                                                            ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" colspan="3" style="width:100%;">
                                                <?php
                                                $params = "&id_paciente=" . $id_paciente .
                                                        "&id_imagen=" . $id_hc_consulta . "_biomiocroscopia" .
                                                        "&nombre_imagen=" . $img_biomiocroscopia .
                                                        "&nombre_imagen_base=../imagenes/ojos_oftalmologia.png" .
                                                        "&ancho_img=850" .
                                                        "&alto_img=220";
                                                ?>
                                                <input type="hidden" name="img_biomiocroscopia" id="img_biomiocroscopia" value="<?php echo($img_biomiocroscopia); ?>" />
                                                <iframe id="ifr_img_biomicroscopia" width="100%" height="295" style="border-width:0;" src="../funciones/wPaint/Class_Pintar.php?<?php echo($params); ?>"></iframe>
                                            </td>
                                        </tr>
                                    </table>
                                    <!--FIN MUSCULAR-->	

                                    <table border="0" cellpadding="0" cellspacing="0" align="center" style="width:98%;">
                                        <tr>
                                            <td align="center" colspan="3" class="td_tabla">
                                                <div class="odoi_t">
                                                    <div class="od_t"><h5 style="margin: 0px;">OD</h5></div>
                                                    <div class="oi_t"><h5 style="margin: 0px;">OI</h5></div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>

                                    <!--INICIO BIOMICROSCOPIA-->
                                    <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:98%;">
                                        <tr>
                                            <td align="center" colspan="3" >
                                                <h5 style="margin: 0px">Biomicroscopia</h5>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left" colspan="3">
                                                <table border="0" cellpadding="3" cellspacing="0" align="left" style="width:100%;">
                                                    <tr>
                                                        <td align="center" colspan="3">
                                                            <img src="../imagenes/sano_hc_24.png" class="img_button no-margin" style="display:inline-block;" onclick="marcar_sano_oft_todos(1);" />
                                                            <label style="display:inline-block;"><b>Marcar vac&iacute;os como normales</b></label>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:41%;"></td>
                                                        <td align="center" style="width:18%;"></td>
                                                        <td style="width:41%;"></td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center">
                                                            <?php
                                                            $componenteRecOft->get_componente("biomi_conjuntiva", "od", $biomi_conjuntiva_od, $arr_colores, 9, $colorPick, 3, 2, "", "width:80%;", "Sana");
                                                            ?>
                                                        </td>
                                                        <td align="center" style="width:150px;margin-top: 5px;"><label><b>Conjuntiva *</b></label></td>
                                                        <td align="center">
                                                            <?php
                                                            $componenteRecOft->get_componente("biomi_conjuntiva", "oi", $biomi_conjuntiva_oi, $arr_colores, 10, $colorPick, 3, 2, "", "width:80%;", "Sana");
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center">
                                                            <?php
                                                            $componenteRecOft->get_componente("biomi_cornea", "od", $biomi_cornea_od, $arr_colores, 11, $colorPick, 4, 2, "", "width:80%;", "Sana, Transparente");
                                                            ?>
                                                        </td>
                                                        <td align="center" style="width:150px;margin-top: 5px;"><label><b>C&oacute;rnea *</b></label></td>
                                                        <td align="center">
                                                            <?php
                                                            $componenteRecOft->get_componente("biomi_cornea", "oi", $biomi_cornea_oi, $arr_colores, 12, $colorPick, 4, 2, "", "width:80%;", "Sana, Transparente");
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center">
                                                            <?php
                                                            $componenteRecOft->get_componente("biomi_cam_anterior", "od", $biomi_cam_anterior_od, $arr_colores, 13, $colorPick, 5, 2, "", "width:80%;", "Tranquila normal");
                                                            ?>
                                                        </td>
                                                        <td align="center" style="width:150px;margin-top: 5px;"><label><b>C&aacute;mara Anterior *</b></label></td>
                                                        <td align="center">
                                                            <?php
                                                            $componenteRecOft->get_componente("biomi_cam_anterior", "oi", $biomi_cam_anterior_oi, $arr_colores, 14, $colorPick, 5, 2, "", "width:80%;", "Tranquila normal");
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center">
                                                            <?php
                                                            $componenteRecOft->get_componente("biomi_iris", "od", $biomi_iris_od, $arr_colores, 15, $colorPick, 6, 2, "", "width:80%;", "Marrón, Pupila central, redonda, normorreactiva");
                                                            ?>
                                                        </td>
                                                        <td align="center" style="width:150px;margin-top: 5px;"><label><b>Iris *</b></label></td>
                                                        <td align="center">
                                                            <?php
                                                            $componenteRecOft->get_componente("biomi_iris", "oi", $biomi_iris_oi, $arr_colores, 16, $colorPick, 6, 2, "", "width:80%;", "Marrón, Pupila central, redonda, normorreactiva");
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center">
                                                            <?php
                                                            $componenteRecOft->get_componente("biomi_cristalino", "od", $biomi_cristalino_od, $arr_colores, 17, $colorPick, 7, 2, "", "width:80%;", "Normal - Transparente");
                                                            ?>
                                                        </td>
                                                        <td align="center" style="width:150px;margin-top: 5px;"><label><b>Cristalino *</b></label></td>
                                                        <td align="center">
                                                            <?php
                                                            $componenteRecOft->get_componente("biomi_cristalino", "oi", $biomi_cristalino_oi, $arr_colores, 18, $colorPick, 7, 2, "", "width:80%;", "Normal - Transparente");
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <tr style="height:25px;"></tr>
                                                    <tr>
                                                        <td align="center" >
                                                            <div class="biomicroscopia_div"></div>
                                                            <?php
                                                            $combo->get("biomi_vanherick_od", $biomi_vanherick_od, $tabla_vanherick, " ", "", "", "width:80px;margin: 0px;", "", "select_hc componente_color_pick_" . $arr_colores[3]);
                                                            $colorPick->getColorPick("biomi_vanherick_od", 3);
                                                            ?>
                                                        </td>
                                                        <td align="center" style="width:150px;margin-top: 5px;"><label><b>Escala de Van Herick</b></label></td>
                                                        <td align="center" >
                                                            <div class="biomicroscopia_div_oi"></div>
                                                            <?php
                                                            $combo->get("biomi_vanherick_oi", $biomi_vanherick_oi, $tabla_vanherick, " ", "", "", "width:80px;margin: 0px;margin-left:50px;", "", "select_hc componente_color_pick_" . $arr_colores[4]);
                                                            $colorPick->getColorPick("biomi_vanherick_oi", 4);
                                                            ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!--FIN BIOMICROSCOPIA-->

                                    <!--INICIO GONIOSCOPIA-->
                                    <br />
                                    <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:98%;">
                                        <tr>
                                            <td align="center" colspan="5">
                                                <h5 style="margin: 0px">Gonioscopia</h5>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" style="width:15%;"><h5>&nbsp;</h5></td>
                                            <td align="left" class="">
                                                <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:100%;">	
                                                    <tr>
                                                        <th align="center" style=""> <div class="linea_derecha">&nbsp;</div>  </th>
                                                        <th align="center" style="">
                                                            <input type="text" name="goniosco_superior_od" id="goniosco_superior_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[19]); ?>" style="width:50px; margin: 0px;" value="<?php echo $goniosco_superior_od; ?>" tabindex="" maxlength="5" />
                                                            <?php
                                                            $colorPick->getColorPick("goniosco_superior_od", 19);
                                                            ?>
                                                        </th>
                                                        <th align="center" style=""> <div class="linea_izquierda">&nbsp;</div></th>
                                                    </tr>
                                                    <tr>
                                                        <th align="center" style="">
                                                            <input type="text" name="goniosco_temporal_od" id="goniosco_temporal_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[20]); ?>" style="width:50px; margin: 0px;" value="<?php echo $goniosco_temporal_od; ?>" tabindex="" maxlength="5" />
                                                            <?php
                                                            $colorPick->getColorPick("goniosco_temporal_od", 20);
                                                            ?>
                                                        </th>
                                                        <th align="center" style=""></th>
                                                        <th align="center" style="">
                                                            <input type="text" name="goniosco_nasal_od" id="goniosco_nasal_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[21]); ?>" style="width:50px; margin: 0px;" value="<?php echo $goniosco_nasal_od; ?>" tabindex="" maxlength="5" />
                                                            <?php
                                                            $colorPick->getColorPick("goniosco_nasal_od", 21);
                                                            ?>
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th align="center" style=""></th>
                                                        <th align="center" style="">
                                                            <input type="text" name="goniosco_inferior_od" id="goniosco_inferior_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[22]); ?>" style="width:50px; margin: 0px;" value="<?php echo $goniosco_inferior_od; ?>" tabindex="" maxlength="5" />
                                                            <?php
                                                            $colorPick->getColorPick("goniosco_inferior_od", 22);
                                                            ?>
                                                        </th>
                                                        <th align="center" style=""></th>
                                                    </tr>
                                                </table>
                                            </td>	   		
                                            <td align="center" style="width:15%;"><h5>&nbsp;</h5></td>
                                            <td align="left" class="">
                                                <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:100%;">	
                                                    <tr>
                                                        <th align="center" style=""> <div class="linea_derecha">&nbsp;</div> </th>
                                                        <th align="center" style="">
                                                            <input type="text" name="goniosco_superior_oi" id="goniosco_superior_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[23]); ?>" style="width:50px; margin: 0px;" value="<?php echo $goniosco_superior_oi; ?>" tabindex="" maxlength="5" />
                                                            <?php
                                                            $colorPick->getColorPick("goniosco_superior_oi", 23);
                                                            ?>
                                                        </th>
                                                        <th align="center" style=""> <div class="linea_izquierda">&nbsp;</div> </th>
                                                    </tr>
                                                    <tr>
                                                        <th align="center" style="">
                                                            <input type="text" name="goniosco_nasal_oi" id="goniosco_nasal_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[24]); ?>" style="width:50px; margin: 0px;" value="<?php echo $goniosco_nasal_oi; ?>" tabindex="" maxlength="5" />
                                                            <?php
                                                            $colorPick->getColorPick("goniosco_nasal_oi", 24);
                                                            ?>
                                                        </th>
                                                        <th align="center" style=""></th>
                                                        <th align="center" style="">
                                                            <input type="text" name="goniosco_temporal_oi" id="goniosco_temporal_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[25]); ?>" style="width:50px; margin: 0px;" value="<?php echo $goniosco_temporal_oi; ?>" tabindex="" maxlength="5" />
                                                            <?php
                                                            $colorPick->getColorPick("goniosco_temporal_oi", 25);
                                                            ?>
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th align="center" style=""></th>
                                                        <th align="center" style="">
                                                            <input type="text" name="goniosco_inferior_oi" id="goniosco_inferior_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[26]); ?>" style="width:50px; margin: 0px;" value="<?php echo $goniosco_inferior_oi; ?>" tabindex="" maxlength="5" />
                                                            <?php
                                                            $colorPick->getColorPick("goniosco_inferior_oi", 26);
                                                            ?>
                                                        </th>
                                                        <th align="center" style=""></th>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td align="center" style="width:15%;"><h5>&nbsp;</h5></td>
                                        </tr>
                                        <tr>
                                            <td align="left" colspan="5">
                                                <label><b>Observaciones</b></label>
                                                <div id="txt_observaciones_gonioscopia"><?php echo($utilidades->ajustar_texto_wysiwyg($observaciones_gonioscopia)); ?></div>
                                            </td>
                                        </tr>
                                    </table>
                                    <!--FIN GONIOSCOPIA-->

                                    <!--INICIO TONOMETRIA-->
                                    <br />
                                    <?php
                                    //Se inserta en componente de tonometrías
                                    $class_tonometrias->agregar_tonometria($id_hc_consulta, $observaciones_tonometria, $colorPick);
                                    ?>
                                    <!--FIN TONOMETRIA-->
                                </div>
                                <div class="content active" id="panel2-4">
                                    <table border="0" cellpadding="0" cellspacing="0" align="center" style="width:98%;">
                                        <tr>
                                            <td align="center" colspan="3" class="td_tabla">
                                                <div class="odoi_t">
                                                    <div class="od_t"><h5 style="margin: 0px;">OD</h5></div>
                                                    <div class="oi_t"><h5 style="margin: 0px;">OI</h5></div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>

                                    <!--INICIO FONDO DE OJO-->
                                    <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:98%;">
                                        <tr>
                                            <td align="center" colspan="3">
                                                <h5 style="margin: 0px">Fondo de ojo</h5>
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
                                        <tr>
                                            <td align="center" colspan="3">
                                                <table width="100%">
                                                    <tr>
                                                        <td align="center" style="width:50%;">
                                                            <?php
                                                            $params = "&id_paciente=" . $id_paciente .
                                                                    "&id_imagen=" . $id_hc_consulta . "_tonometriaod" .
                                                                    "&nombre_imagen=" . $img_tonometria_od .
                                                                    "&nombre_imagen_base=../imagenes/ojos_tonometria_od.png" .
                                                                    "&ancho_img=375" .
                                                                    "&alto_img=250";
                                                            ?>
                                                            <input type="hidden" name="img_tonometria_od" id="img_tonometria_od" value="<?php echo($img_tonometria_od); ?>" />
                                                            <iframe id="ifr_img_tonometria_od" width="100%" height="325" style="border-width:0;" src="../funciones/wPaint/Class_Pintar.php?<?php echo($params); ?>"></iframe>
                                                        </td>
                                                        <td align="center" style="width:50%;">
                                                            <?php
                                                            $params = "&id_paciente=" . $id_paciente .
                                                                    "&id_imagen=" . $id_hc_consulta . "_tonometriaoi" .
                                                                    "&nombre_imagen=" . $img_tonometria_oi .
                                                                    "&nombre_imagen_base=../imagenes/ojos_tonometria_oi.png" .
                                                                    "&ancho_img=375" .
                                                                    "&alto_img=250";
                                                            ?>
                                                            <input type="hidden" name="img_tonometria_oi" id="img_tonometria_oi" value="<?php echo($img_tonometria_oi); ?>" />
                                                            <iframe id="ifr_img_tonometria_oi" width="100%" height="325" style="border-width:0;" src="../funciones/wPaint/Class_Pintar.php?<?php echo($params); ?>"></iframe>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left" colspan="3">
                                                <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:100%;">
                                                    <tr>
                                                        <td align="center" colspan="3">
                                                            <img src="../imagenes/sano_hc_24.png" class="img_button no-margin" style="display:inline-block;" onclick="marcar_sano_oft_todos(2);" />
                                                            <label style="display:inline-block;"><b>Marcar vac&iacute;os como normales</b></label>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:41%;"></td>
                                                        <td align="center" style="width:18%;"></td>
                                                        <td style="width:41%;"></td>
                                                    </tr>
                                                    <tr>
                                                        <th align="center">
                                                            <?php
                                                            $componenteRecOft->get_componente("tonometria_nervio_optico", "od", $tonometria_nervio_optico_od, $arr_colores, 27, $colorPick, 8, 2, "", "width:80%;");
                                                            ?>
                                                        </th>
                                                        <th align="center"><label><b>Nervio &Oacute;ptico *</b></label></th>
                                                        <th align="center">
                                                            <?php
                                                            $componenteRecOft->get_componente("tonometria_nervio_optico", "oi", $tonometria_nervio_optico_oi, $arr_colores, 28, $colorPick, 8, 2, "", "width:80%;");
                                                            ?>
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <td align="center" colspan="3">
                                                            <br />
                                                            <h5 style="margin: 0px">Retina</h5>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th align="center">
                                                            <?php
                                                            $componenteRecOft->get_componente("tonometria_macula", "od", $tonometria_macula_od, $arr_colores, 29, $colorPick, 9, 2, "", "width:80%;");
                                                            ?>
                                                        </th>
                                                        <th align="center"><label><b>M&aacute;cula *</b></label></th>
                                                        <th align="center">
                                                            <?php
                                                            $componenteRecOft->get_componente("tonometria_macula", "oi", $tonometria_macula_oi, $arr_colores, 30, $colorPick, 9, 2, "", "width:80%;");
                                                            ?>
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th align="center">
                                                            <?php
                                                            $componenteRecOft->get_componente("tonometria_periferia", "od", $tonometria_periferia_od, $arr_colores, 31, $colorPick, 10, 2, "", "width:80%;");
                                                            ?>
                                                        </th>
                                                        <th align="center"><label><b>Periferia</b></label></th>
                                                        <th align="center">
                                                            <?php
                                                            $componenteRecOft->get_componente("tonometria_periferia", "oi", $tonometria_periferia_oi, $arr_colores, 32, $colorPick, 10, 2, "", "width:80%;");
                                                            ?>
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th align="center">
                                                            <?php
                                                            $componenteRecOft->get_componente("tonometria_vitreo", "od", $tonometria_vitreo_od, $arr_colores, 33, $colorPick, 11, 2, "", "width:80%;", "Claro");
                                                            ?>
                                                        </th>
                                                        <th align="center"><label><b>V&iacute;treo</b></label></th>
                                                        <th align="center">
                                                            <?php
                                                            $componenteRecOft->get_componente("tonometria_vitreo", "oi", $tonometria_vitreo_oi, $arr_colores, 34, $colorPick, 11, 2, "", "width:80%;", "Claro");
                                                            ?>
                                                        </th>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!--FIN TONOMETRIA-->
                                </div>
                                <div class="content" id="panel2-5">
                                    <!--INICIO DIAGNOSTICO-->
                                    <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:98%;">
                                        <tr>
                                            <td align="left" colspan="3">
                                                <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:100%;">	
                                                    <tr>
                                                        <td align="center" colspan="2">
                                                            <h6>Diagn&oacute;sticos</h6>
                                                            <?php
                                                            $class_diagnosticos->getFormularioDiagnosticos($id_hc_consulta);
                                                            ?>
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
                                                            <div id="txt_diagnostico_oftalmo"><?php echo($utilidades->ajustar_texto_wysiwyg($diagnostico_oftalmo)); ?></div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center" colspan="2">
                                                            <label><b>Solicitud de Procedimientos y Ex&aacute;menes Complemetarios</b></label>
                                                            <?php
                                                            $class_solic_procs->getFormularioSolicitud($id_hc_consulta);
                                                            ?>
                                                            <div id="txt_solicitud_examenes"><?php echo($utilidades->ajustar_texto_wysiwyg($solicitud_examenes)); ?></div>
                                                        </td>
                                                    </tr>
                                                    <tr>	
                                                        <td align="center" colspan="2">
                                                            <label><b>Recomendaciones Cl&iacute;nicas, M&eacute;dicas, Optom&eacute;tricas y Quir&uacute;rgicas&nbsp;</b></label>
                                                            <div id="txt_tratamiento_oftalmo"><?php echo($utilidades->ajustar_texto_wysiwyg($tratamiento_oftalmo)); ?></div>
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
                                                    <tr>
                                                        <td align="center" colspan="2" style="display:none;">
                                                            <label><b>F&Oacute;RMULA M&Eacute;DICA</b></label>
                                                            <textarea style="text-align: justify;" class="textarea_oftalmo" id="medicamentos_oftalmo" nombre="medicamentos_oftalmo" onblur="trim_cadena(this);" tabindex="1" ><?php echo $medicamentos_oftalmo; ?></textarea>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!--FIN DIAGNOSTICO-->
                                </div>
                                <div class="content" id="panel2-6">
                                    <!--INICIO EVALUACIÓN MUSCULAR Y SENSORIALIDAD-->
                                    <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:98%;">
                                        <tr>
                                            <td align="center">
                                                <table style="width:100%;">
                                                    <tr>
                                                        <td align="left" style="width:40%;"><label>M&eacute;todo</label></td>
                                                        <td align="left" style="width:20%;"><label>Ortotropia</label></td>
                                                        <td align="left" style="width:20%;"><label>Correcci&oacute;n</label></td>
                                                        <td align="left" style="width:20%;"><label>Ojo fijador</label></td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left">
                                                            <input type="text" id="txt_metodo_ofp" name="txt_metodo_ofp" maxlength="256" value="<?php echo($metodo_ofp); ?>" onblur="trim_cadena(this);" class="no-margin" style="width:80%;" />
                                                        </td>
                                                        <td align="left">
                                                            <?php
                                                            $combo->getComboDb("cmb_ortotropia", $ind_ortotropia, $lista_si_no, "id,valor", "--Seleccione--", "", 1, "", "", "no-margin");
                                                            ?>
                                                        </td>
                                                        <td align="left">
                                                            <?php
                                                            $combo->getComboDb("cmb_correccion", $id_correccion, $lista_correccion, "id_detalle,nombre_detalle", "--Seleccione--", "", 1, "", "", "no-margin");
                                                            ?>
                                                        </td>
                                                        <td align="left">
                                                            <?php
                                                            $combo->getComboDb("cmb_ojo_fijador", $id_ojo_fijador, $lista_ojos, "id_detalle,nombre_detalle", "--Seleccione--", "", 1, "", "", "no-margin");
                                                            ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <br />
                                                <table style="width:100%;">
                                                    <tr>
                                                        <td align="center" style="width:25%;">
                                                            <label><b>Lejos</b></label>
                                                        </td>
                                                        <td align="center" style="width:25%;">
                                                            <label><b>Cerca</b></label>
                                                        </td>
                                                        <td align="center" style="width:25%;">
                                                            <label><b>Cerca +3,00</b></label>
                                                        </td>
                                                        <td align="center" style="width:25%;">
                                                            <label><b>Cerca bifocales</b></label>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center">
                                                            <table style="width:100%;">
                                                                <tr>
                                                                    <td align="right" style="width:20%"><label>H</label></td>
                                                                    <td align="left" style="width:40%">
                                                                        <input type="text" id="txt_lejos_h" name="txt_lejos_h" maxlength="50" value="<?php echo($lejos_h); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                    </td>
                                                                    <td align="left" style="width:20%">
                                                                        <input type="text" id="txt_lejos_h_delta" name="txt_lejos_h_delta" maxlength="3" value="<?php echo($lejos_h_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                    </td>
                                                                    <td align="left" style="width:20%"><label>&Delta;</label></td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="right"><label>V</label></td>
                                                                    <td align="left">
                                                                        <input type="text" id="txt_lejos_v" name="txt_lejos_v" maxlength="50" value="<?php echo($lejos_v); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                    </td>
                                                                    <td align="left">
                                                                        <input type="text" id="txt_lejos_v_delta" name="txt_lejos_v_delta" maxlength="3" value="<?php echo($lejos_v_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                    </td>
                                                                    <td align="left"><label>&Delta;</label></td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        <td align="center">
                                                            <table style="width:100%;">
                                                                <tr>
                                                                    <td align="right" style="width:20%"><label>H</label></td>
                                                                    <td align="left" style="width:40%">
                                                                        <input type="text" id="txt_cerca_h" name="txt_cerca_h" maxlength="50" value="<?php echo($cerca_h); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                    </td>
                                                                    <td align="left" style="width:20%">
                                                                        <input type="text" id="txt_cerca_h_delta" name="txt_cerca_h_delta" maxlength="3" value="<?php echo($cerca_h_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                    </td>
                                                                    <td align="left" style="width:20%"><label>&Delta;</label></td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="right"><label>V</label></td>
                                                                    <td align="left">
                                                                        <input type="text" id="txt_cerca_v" name="txt_cerca_v" maxlength="50" value="<?php echo($cerca_v); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                    </td>
                                                                    <td align="left">
                                                                        <input type="text" id="txt_cerca_v_delta" name="txt_cerca_v_delta" maxlength="3" value="<?php echo($cerca_v_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                    </td>
                                                                    <td align="left"><label>&Delta;</label></td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        <td align="center">
                                                            <table style="width:100%;">
                                                                <tr>
                                                                    <td align="right" style="width:20%"><label>H</label></td>
                                                                    <td align="left" style="width:40%">
                                                                        <input type="text" id="txt_cerca_c_h" name="txt_cerca_c_h" maxlength="50" value="<?php echo($cerca_c_h); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                    </td>
                                                                    <td align="left" style="width:20%">
                                                                        <input type="text" id="txt_cerca_c_h_delta" name="txt_cerca_c_h_delta" maxlength="3" value="<?php echo($cerca_c_h_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                    </td>
                                                                    <td align="left" style="width:20%"><label>&Delta;</label></td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="right"><label>V</label></td>
                                                                    <td align="left">
                                                                        <input type="text" id="txt_cerca_c_v" name="txt_cerca_c_v" maxlength="50" value="<?php echo($cerca_c_v); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                    </td>
                                                                    <td align="left">
                                                                        <input type="text" id="txt_cerca_c_v_delta" name="txt_cerca_c_v_delta" maxlength="3" value="<?php echo($cerca_c_v_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                    </td>
                                                                    <td align="left"><label>&Delta;</label></td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        <td align="center">
                                                            <table style="width:100%;">
                                                                <tr>
                                                                    <td align="right" style="width:20%"><label>H</label></td>
                                                                    <td align="left" style="width:40%">
                                                                        <input type="text" id="txt_cerca_b_h" name="txt_cerca_b_h" maxlength="50" value="<?php echo($cerca_b_h); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                    </td>
                                                                    <td align="left" style="width:20%">
                                                                        <input type="text" id="txt_cerca_b_h_delta" name="txt_cerca_b_h_delta" maxlength="3" value="<?php echo($cerca_b_h_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                    </td>
                                                                    <td align="left" style="width:20%"><label>&Delta;</label></td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="right"><label>V</label></td>
                                                                    <td align="left">
                                                                        <input type="text" id="txt_cerca_b_v" name="txt_cerca_b_v" maxlength="50" value="<?php echo($cerca_b_v); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                    </td>
                                                                    <td align="left">
                                                                        <input type="text" id="txt_cerca_b_v_delta" name="txt_cerca_b_v_delta" maxlength="3" value="<?php echo($cerca_b_v_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                    </td>
                                                                    <td align="left"><label>&Delta;</label></td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <br />
                                                <fieldset class="no-margin no-padding">
                                                    <table style="width:100%">
                                                        <tr>
                                                            <td align="center" style="width:18%;">
                                                                <table style="width:100%;">
                                                                    <tr>
                                                                        <td align="right" style="width:8%"><label>H</label></td>
                                                                        <td align="left" style="width:56%">
                                                                            <input type="text" id="txt_derecha_alto_h" name="txt_derecha_alto_h" maxlength="50" value="<?php echo($derecha_alto_h); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                        </td>
                                                                        <td align="left" style="width:28%">
                                                                            <input type="text" id="txt_derecha_alto_h_delta" name="txt_derecha_alto_h_delta" maxlength="3" value="<?php echo($derecha_alto_h_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                        </td>
                                                                        <td align="left" style="width:8%"><label>&Delta;</label></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align="right"><label>V</label></td>
                                                                        <td align="left">
                                                                            <input type="text" id="txt_derecha_alto_v" name="txt_derecha_alto_v" maxlength="50" value="<?php echo($derecha_alto_v); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                        </td>
                                                                        <td align="left">
                                                                            <input type="text" id="txt_derecha_alto_v_delta" name="txt_derecha_alto_v_delta" maxlength="3" value="<?php echo($derecha_alto_v_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                        </td>
                                                                        <td align="left"><label>&Delta;</label></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td align="center" valign="bottom" style="width:22%;">
                                                                <table style="width:100%">
                                                                    <tr>
                                                                        <td align="center" style="width:33%;">
                                                                            <input type="text" name="txt_alto_derecha_od" id="txt_alto_derecha_od" value="<?php echo($alto_derecha_od); ?>" maxlength="2" class="no-margin" style="width:35px;" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_ofp, this);" />
                                                                        </td>
                                                                        <td align="center" style="width:34%;">
                                                                            <input type="text" name="txt_alto_centro_od" id="txt_alto_centro_od" value="<?php echo($alto_centro_od); ?>" maxlength="2" class="no-margin" style="width:35px;" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_ofp, this);" />
                                                                        </td>
                                                                        <td align="center" style="width:33%;">
                                                                            <input type="text" name="txt_alto_izquierda_od" id="txt_alto_izquierda_od" value="<?php echo($alto_izquierda_od); ?>" maxlength="2" class="no-margin" style="width:35px;" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_ofp, this);" />
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td align="center" style="width:18%;">
                                                                <table style="width:100%;">
                                                                    <tr>
                                                                        <td align="right" style="width:8%"><label>H</label></td>
                                                                        <td align="left" style="width:56%">
                                                                            <input type="text" id="txt_centro_alto_h" name="txt_centro_alto_h" maxlength="50" value="<?php echo($centro_alto_h); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                        </td>
                                                                        <td align="left" style="width:28%">
                                                                            <input type="text" id="txt_centro_alto_h_delta" name="txt_centro_alto_h_delta" maxlength="3" value="<?php echo($centro_alto_h_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                        </td>
                                                                        <td align="left" style="width:8%"><label>&Delta;</label></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align="right"><label>V</label></td>
                                                                        <td align="left">
                                                                            <input type="text" id="txt_centro_alto_v" name="txt_centro_alto_v" maxlength="50" value="<?php echo($centro_alto_v); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                        </td>
                                                                        <td align="left">
                                                                            <input type="text" id="txt_centro_alto_v_delta" name="txt_centro_alto_v_delta" maxlength="3" value="<?php echo($centro_alto_v_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                        </td>
                                                                        <td align="left"><label>&Delta;</label></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td align="center" valign="middle" style="width:2%;">
                                                            </td>
                                                            <td align="center" valign="bottom" style="width:22%;">
                                                                <table style="width:100%">
                                                                    <tr>
                                                                        <td align="center" style="width:33%;">
                                                                            <input type="text" name="txt_alto_derecha_oi" id="txt_alto_derecha_oi" value="<?php echo($alto_derecha_oi); ?>" maxlength="2" class="no-margin" style="width:35px;" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_ofp, this);" />
                                                                        </td>
                                                                        <td align="center" style="width:34%;">
                                                                            <input type="text" name="txt_alto_centro_oi" id="txt_alto_centro_oi" value="<?php echo($alto_centro_oi); ?>" maxlength="2" class="no-margin" style="width:35px;" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_ofp, this);" />
                                                                        </td>
                                                                        <td align="center" style="width:33%;">
                                                                            <input type="text" name="txt_alto_izquierda_oi" id="txt_alto_izquierda_oi" value="<?php echo($alto_izquierda_oi); ?>" maxlength="2" class="no-margin" style="width:35px;" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_ofp, this);" />
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td align="center" style="width:18%;">
                                                                <table style="width:100%;">
                                                                    <tr>
                                                                        <td align="right" style="width:8%"><label>H</label></td>
                                                                        <td align="left" style="width:56%">
                                                                            <input type="text" id="txt_izquierda_alto_h" name="txt_izquierda_alto_h" maxlength="50" value="<?php echo($izquierda_alto_h); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                        </td>
                                                                        <td align="left" style="width:28%">
                                                                            <input type="text" id="txt_izquierda_alto_h_delta" name="txt_izquierda_alto_h_delta" maxlength="3" value="<?php echo($izquierda_alto_h_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                        </td>
                                                                        <td align="left" style="width:8%"><label>&Delta;</label></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align="right"><label>V</label></td>
                                                                        <td align="left">
                                                                            <input type="text" id="txt_izquierda_alto_v" name="txt_izquierda_alto_v" maxlength="50" value="<?php echo($izquierda_alto_v); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                        </td>
                                                                        <td align="left">
                                                                            <input type="text" id="txt_izquierda_alto_v_delta" name="txt_izquierda_alto_v_delta" maxlength="3" value="<?php echo($izquierda_alto_v_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                        </td>
                                                                        <td align="left"><label>&Delta;</label></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr><td><div class="div_separador"></div></td></tr>
                                                        <tr>
                                                            <td align="center" style="width:18%;">
                                                                <table style="width:100%;">
                                                                    <tr>
                                                                        <td align="right" style="width:8%"><label>H</label></td>
                                                                        <td align="left" style="width:56%">
                                                                            <input type="text" id="txt_derecha_medio_h" name="txt_derecha_medio_h" maxlength="50" value="<?php echo($derecha_medio_h); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                        </td>
                                                                        <td align="left" style="width:28%">
                                                                            <input type="text" id="txt_derecha_medio_h_delta" name="txt_derecha_medio_h_delta" maxlength="3" value="<?php echo($derecha_medio_h_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                        </td>
                                                                        <td align="left" style="width:8%"><label>&Delta;</label></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align="right"><label>V</label></td>
                                                                        <td align="left">
                                                                            <input type="text" id="txt_derecha_medio_v" name="txt_derecha_medio_v" maxlength="50" value="<?php echo($derecha_medio_v); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                        </td>
                                                                        <td align="left">
                                                                            <input type="text" id="txt_derecha_medio_v_delta" name="txt_derecha_medio_v_delta" maxlength="3" value="<?php echo($derecha_medio_v_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                        </td>
                                                                        <td align="left"><label>&Delta;</label></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td align="center" valign="middle" style="width:22%;">
                                                                <table style="width:100%">
                                                                    <tr>
                                                                        <td align="center" style="width:33%;">
                                                                            <input type="text" name="txt_medio_derecha_od" id="txt_medio_derecha_od" value="<?php echo($medio_derecha_od); ?>" maxlength="2" class="no-margin" style="width:35px;" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_ofp, this);" />
                                                                        </td>
                                                                        <td align="center" style="width:34%;"></td>
                                                                        <td align="center" style="width:33%;">
                                                                            <input type="text" name="txt_medio_izquierda_od" id="txt_medio_izquierda_od" value="<?php echo($medio_izquierda_od); ?>" maxlength="2" class="no-margin" style="width:35px;" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_ofp, this);" />
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td align="center" style="width:18%;">
                                                                <table style="width:100%;">
                                                                    <tr>
                                                                        <td align="right" style="width:8%"><label>H</label></td>
                                                                        <td align="left" style="width:56%">
                                                                            <input type="text" id="txt_centro_medio_h" name="txt_centro_medio_h" maxlength="50" value="<?php echo($centro_medio_h); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                        </td>
                                                                        <td align="left" style="width:28%">
                                                                            <input type="text" id="txt_centro_medio_h_delta" name="txt_centro_medio_h_delta" maxlength="3" value="<?php echo($centro_medio_h_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                        </td>
                                                                        <td align="left" style="width:8%"><label>&Delta;</label></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align="right"><label>V</label></td>
                                                                        <td align="left">
                                                                            <input type="text" id="txt_centro_medio_v" name="txt_centro_medio_v" maxlength="50" value="<?php echo($centro_medio_v); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                        </td>
                                                                        <td align="left">
                                                                            <input type="text" id="txt_centro_medio_v_delta" name="txt_centro_medio_v_delta" maxlength="3" value="<?php echo($centro_medio_v_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                        </td>
                                                                        <td align="left"><label>&Delta;</label></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td align="center" style="width:2%;">
                                                                <img src="../imagenes/icon-up-down.png" class="img_button no-margin" onclick="copiar_ofp(1);" />
                                                                <img src="../imagenes/icon-left-right.png" class="img_button no-margin" style="padding:3px 0 3px 0" onclick="copiar_ofp(2);" />
                                                                <img src="../imagenes/icon-left-right-up-down.png" class="img_button no-margin" onclick="copiar_ofp(3);" />
                                                            </td>
                                                            <td align="center" valign="middle" style="width:22%;">
                                                                <table style="width:100%">
                                                                    <tr>
                                                                        <td align="center" style="width:33%;">
                                                                            <input type="text" name="txt_medio_derecha_oi" id="txt_medio_derecha_oi" value="<?php echo($medio_derecha_oi); ?>" maxlength="2" class="no-margin" style="width:35px;" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_ofp, this);" />
                                                                        </td>
                                                                        <td align="center" style="width:34%;"></td>
                                                                        <td align="center" style="width:33%;">
                                                                            <input type="text" name="txt_medio_izquierda_oi" id="txt_medio_izquierda_oi" value="<?php echo($medio_izquierda_oi); ?>" maxlength="2" class="no-margin" style="width:35px;" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_ofp, this);" />
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td align="center" style="width:18%;">
                                                                <table style="width:100%;">
                                                                    <tr>
                                                                        <td align="right" style="width:8%"><label>H</label></td>
                                                                        <td align="left" style="width:56%">
                                                                            <input type="text" id="txt_izquierda_medio_h" name="txt_izquierda_medio_h" maxlength="50" value="<?php echo($izquierda_medio_h); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                        </td>
                                                                        <td align="left" style="width:28%">
                                                                            <input type="text" id="txt_izquierda_medio_h_delta" name="txt_izquierda_medio_h_delta" maxlength="3" value="<?php echo($izquierda_medio_h_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                        </td>
                                                                        <td align="left" style="width:8%"><label>&Delta;</label></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align="right"><label>V</label></td>
                                                                        <td align="left">
                                                                            <input type="text" id="txt_izquierda_medio_v" name="txt_izquierda_medio_v" maxlength="50" value="<?php echo($izquierda_medio_v); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                        </td>
                                                                        <td align="left">
                                                                            <input type="text" id="txt_izquierda_medio_v_delta" name="txt_izquierda_medio_v_delta" maxlength="3" value="<?php echo($izquierda_medio_v_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                        </td>
                                                                        <td align="left"><label>&Delta;</label></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr><td><div class="div_separador"></div></td></tr>
                                                        <tr>
                                                            <td align="center" style="width:18%;">
                                                                <table style="width:100%;">
                                                                    <tr>
                                                                        <td align="right" style="width:8%"><label>H</label></td>
                                                                        <td align="left" style="width:56%">
                                                                            <input type="text" id="txt_derecha_bajo_h" name="txt_derecha_bajo_h" maxlength="50" value="<?php echo($derecha_bajo_h); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                        </td>
                                                                        <td align="left" style="width:28%">
                                                                            <input type="text" id="txt_derecha_bajo_h_delta" name="txt_derecha_bajo_h_delta" maxlength="3" value="<?php echo($derecha_bajo_h_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                        </td>
                                                                        <td align="left" style="width:8%"><label>&Delta;</label></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align="right"><label>V</label></td>
                                                                        <td align="left">
                                                                            <input type="text" id="txt_derecha_bajo_v" name="txt_derecha_bajo_v" maxlength="50" value="<?php echo($derecha_bajo_v); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                        </td>
                                                                        <td align="left">
                                                                            <input type="text" id="txt_derecha_bajo_v_delta" name="txt_derecha_bajo_v_delta" maxlength="3" value="<?php echo($derecha_bajo_v_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                        </td>
                                                                        <td align="left"><label>&Delta;</label></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td align="center" valign="top" style="width:22%;">
                                                                <table style="width:100%">
                                                                    <tr>
                                                                        <td align="center" style="width:33%;">
                                                                            <input type="text" name="txt_bajo_derecha_od" id="txt_bajo_derecha_od" value="<?php echo($bajo_derecha_od); ?>" maxlength="2" class="no-margin" style="width:35px;" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_ofp, this);" />
                                                                        </td>
                                                                        <td align="center" style="width:34%;" colspan="2">
                                                                            <input type="text" name="txt_bajo_centro_od" id="txt_bajo_centro_od" value="<?php echo($bajo_centro_od); ?>" maxlength="2" class="no-margin" style="width:35px;" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_ofp, this);" />
                                                                        </td>
                                                                        <td align="center" style="width:33%;">
                                                                            <input type="text" name="txt_bajo_izquierda_od" id="txt_bajo_izquierda_od" value="<?php echo($bajo_izquierda_od); ?>" maxlength="2" class="no-margin" style="width:35px;" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_ofp, this);" />
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align="right" style="width:50%;" colspan="2"><label>DVD:</label></td>
                                                                        <td align="left" style="width:50%;" colspan="2">
                                                                            <input type="text" id="txt_dvd_od" name="txt_dvd_od" maxlength="3" value="<?php echo($dvd_od); ?>" class="no-margin" style="width:45px;" onkeypress="return solo_numeros(event, false);" />
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td align="center" style="width:18%;">
                                                                <table style="width:100%;">
                                                                    <tr>
                                                                        <td align="right" style="width:8%"><label>H</label></td>
                                                                        <td align="left" style="width:56%">
                                                                            <input type="text" id="txt_centro_bajo_h" name="txt_centro_bajo_h" maxlength="50" value="<?php echo($centro_bajo_h); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                        </td>
                                                                        <td align="left" style="width:28%">
                                                                            <input type="text" id="txt_centro_bajo_h_delta" name="txt_centro_bajo_h_delta" maxlength="3" value="<?php echo($centro_bajo_h_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                        </td>
                                                                        <td align="left" style="width:8%"><label>&Delta;</label></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align="right"><label>V</label></td>
                                                                        <td align="left">
                                                                            <input type="text" id="txt_centro_bajo_v" name="txt_centro_bajo_v" maxlength="50" value="<?php echo($centro_bajo_v); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                        </td>
                                                                        <td align="left">
                                                                            <input type="text" id="txt_centro_bajo_v_delta" name="txt_centro_bajo_v_delta" maxlength="3" value="<?php echo($centro_bajo_v_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                        </td>
                                                                        <td align="left"><label>&Delta;</label></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td align="center" style="width:2%;">
                                                            </td>
                                                            <td align="center" valign="top" style="width:22%;">
                                                                <table style="width:100%">
                                                                    <tr>
                                                                        <td align="center" style="width:33%;">
                                                                            <input type="text" name="txt_bajo_derecha_oi" id="txt_bajo_derecha_oi" value="<?php echo($bajo_derecha_oi); ?>" maxlength="2" class="no-margin" style="width:35px;" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_ofp, this);" />
                                                                        </td>
                                                                        <td align="center" style="width:34%;" colspan="2">
                                                                            <input type="text" name="txt_bajo_centro_oi" id="txt_bajo_centro_oi" value="<?php echo($bajo_centro_oi); ?>" maxlength="2" class="no-margin" style="width:35px;" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_ofp, this);" />
                                                                        </td>
                                                                        <td align="center" style="width:33%;">
                                                                            <input type="text" name="txt_bajo_izquierda_oi" id="txt_bajo_izquierda_oi" value="<?php echo($bajo_izquierda_oi); ?>" maxlength="2" class="no-margin" style="width:35px;" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_ofp, this);" />
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align="right" style="width:50%;" colspan="2"><label>DVD:</label></td>
                                                                        <td align="left" style="width:50%;" colspan="2">
                                                                            <input type="text" id="txt_dvd_oi" name="txt_dvd_oi" maxlength="3" value="<?php echo($dvd_oi); ?>" class="no-margin" style="width:45px;" onkeypress="return solo_numeros(event, false);" />
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td align="center" style="width:18%;">
                                                                <table style="width:100%;">
                                                                    <tr>
                                                                        <td align="right" style="width:8%"><label>H</label></td>
                                                                        <td align="left" style="width:56%">
                                                                            <input type="text" id="txt_izquierda_bajo_h" name="txt_izquierda_bajo_h" maxlength="50" value="<?php echo($izquierda_bajo_h); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                        </td>
                                                                        <td align="left" style="width:28%">
                                                                            <input type="text" id="txt_izquierda_bajo_h_delta" name="txt_izquierda_bajo_h_delta" maxlength="3" value="<?php echo($izquierda_bajo_h_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                        </td>
                                                                        <td align="left" style="width:8%"><label>&Delta;</label></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align="right"><label>V</label></td>
                                                                        <td align="left">
                                                                            <input type="text" id="txt_izquierda_bajo_v" name="txt_izquierda_bajo_v" maxlength="50" value="<?php echo($izquierda_bajo_v); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                        </td>
                                                                        <td align="left">
                                                                            <input type="text" id="txt_izquierda_bajo_v_delta" name="txt_izquierda_bajo_v_delta" maxlength="3" value="<?php echo($izquierda_bajo_v_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                        </td>
                                                                        <td align="left"><label>&Delta;</label></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </fieldset>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center">
                                                <h5 style="margin: 10px">Observaciones</h5>
                                                <?php
                                                $observaciones_oft_pediat = $utilidades->ajustar_texto_wysiwyg($observaciones_oft_pediat);
                                                ?>
                                                <div id="txt_observaciones_oft_pediat"><?php echo($observaciones_oft_pediat); ?></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center">
                                                <table style="width:100%;">
                                                    <tr>
                                                        <td align="center" style="width:18%;">
                                                            <table style="width:100%;">
                                                                <tr>
                                                                    <td align="center" colspan="4">
                                                                        <label><b>Inclinaci&oacute;n derecha</b></label>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="right" style="width:8%"><label>H</label></td>
                                                                    <td align="left" style="width:56%">
                                                                        <input type="text" id="txt_inclinacion_der_h" name="txt_inclinacion_der_h" maxlength="50" value="<?php echo($inclinacion_der_h); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                    </td>
                                                                    <td align="left" style="width:28%">
                                                                        <input type="text" id="txt_inclinacion_der_h_delta" name="txt_inclinacion_der_h_delta" maxlength="3" value="<?php echo($inclinacion_der_h_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                    </td>
                                                                    <td align="left" style="width:8%"><label>&Delta;</label></td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="right"><label>V</label></td>
                                                                    <td align="left">
                                                                        <input type="text" id="txt_inclinacion_der_v" name="txt_inclinacion_der_v" maxlength="50" value="<?php echo($inclinacion_der_v); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                    </td>
                                                                    <td align="left">
                                                                        <input type="text" id="txt_inclinacion_der_v_delta" name="txt_inclinacion_der_v_delta" maxlength="3" value="<?php echo($inclinacion_der_v_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                    </td>
                                                                    <td align="left"><label>&Delta;</label></td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        <td align="center" style="width:18%;">
                                                            <table style="width:100%;">
                                                                <tr>
                                                                    <td align="center" colspan="4">
                                                                        <label><b>Inclinaci&oacute;n izquierda</b></label>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="right" style="width:8%"><label>H</label></td>
                                                                    <td align="left" style="width:56%">
                                                                        <input type="text" id="txt_inclinacion_izq_h" name="txt_inclinacion_izq_h" maxlength="50" value="<?php echo($inclinacion_izq_h); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                    </td>
                                                                    <td align="left" style="width:28%">
                                                                        <input type="text" id="txt_inclinacion_izq_h_delta" name="txt_inclinacion_izq_h_delta" maxlength="3" value="<?php echo($inclinacion_izq_h_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                    </td>
                                                                    <td align="left" style="width:8%"><label>&Delta;</label></td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="right"><label>V</label></td>
                                                                    <td align="left">
                                                                        <input type="text" id="txt_inclinacion_izq_v" name="txt_inclinacion_izq_v" maxlength="50" value="<?php echo($inclinacion_izq_v); ?>" onblur="trim_cadena(this);" class="no-margin" />
                                                                    </td>
                                                                    <td align="left">
                                                                        <input type="text" id="txt_inclinacion_izq_v_delta" name="txt_inclinacion_izq_v_delta" maxlength="3" value="<?php echo($inclinacion_izq_v_delta); ?>" class="no-margin" onkeypress="return solo_numeros(event, false);" />
                                                                    </td>
                                                                    <td align="left"><label>&Delta;</label></td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        <td align="center" valign="top" style="width:32%;">
                                                            <table style="width:100%;">
                                                                <tr>
                                                                    <td align="left" style="width:22%;">
                                                                        <label><b>Nistagmo:</b></label>
                                                                    </td>
                                                                    <td align="left" style="width:78%;">
                                                                        <?php
                                                                        $combo->getComboDb("cmb_nistagmo", $ind_nistagmo, $lista_si_no, "id,valor", "--Seleccione--", "seleccionar_nistagmo(this.value);", 1, "", "", "no-margin");
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="left" colspan="2">
                                                                        <input type="text" id="txt_nistagmo" name="txt_nistagmo" maxlength="256" value="<?php echo($texto_nistagmo); ?>" onblur="trim_cadena(this);" class="no-margin" style="width:100%;" />
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        <td align="center" valign="top" style="width:32%;">
                                                            <table style="width:100%;">
                                                                <tr>
                                                                    <td align="left" style="width:62%;">
                                                                        <label><b>Posici&oacute;n anormal de la cabeza:</b></label>
                                                                    </td>
                                                                    <td align="left" style="width:38%;">
                                                                        <?php
                                                                        $combo->getComboDb("cmb_pac", $ind_pac, $lista_si_no, "id,valor", "--Seleccione--", "seleccionar_pac(this.value);", 1, "", "", "no-margin");
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="left" colspan="2">
                                                                        <input type="text" id="txt_pac" name="txt_pac" maxlength="256" value="<?php echo($texto_pac); ?>" onblur="trim_cadena(this);" class="no-margin" style="width:100%;" />
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <script type="text/javascript">
                                                    seleccionar_nistagmo("<?php echo($ind_nistagmo); ?>");
                                                    seleccionar_pac("<?php echo($ind_pac); ?>");
                                                </script>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center">
                                                <table style="width:100%;">
                                                    <tr>
                                                        <td align="center" valign="top" style="width:33%">
                                                            <table style="width:100%;">
                                                                <tr>
                                                                    <td align="center" style="width:50%">
                                                                    </td>
                                                                    <td align="center" style="width:25%">
                                                                        <label>Lejos</label>
                                                                    </td>
                                                                    <td align="center" style="width:25%">
                                                                        <label>Cerca</label>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="right">
                                                                        <label>Convergencia fusional:</label>
                                                                    </td>
                                                                    <td align="center">
                                                                        <input type="text" id="txt_conv_fusional_lejos" name="txt_conv_fusional_lejos" maxlength="3" value="<?php echo($conv_fusional_lejos); ?>" class="no-margin" style="width:50px;" onkeypress="return solo_numeros(event, false);" />
                                                                    </td>
                                                                    <td align="center">
                                                                        <input type="text" id="txt_conv_fusional_cerca" name="txt_conv_fusional_cerca" maxlength="3" value="<?php echo($conv_fusional_cerca); ?>" class="no-margin" style="width:50px;" onkeypress="return solo_numeros(event, false);" />
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="right">
                                                                        <label>Divergencia fusional:</label>
                                                                    </td>
                                                                    <td align="center">
                                                                        <input type="text" id="txt_div_fusional_lejos" name="txt_div_fusional_lejos" maxlength="3" value="<?php echo($div_fusional_lejos); ?>" class="no-margin" style="width:50px;" onkeypress="return solo_numeros(event, false);" />
                                                                    </td>
                                                                    <td align="center">
                                                                        <input type="text" id="txt_div_fusional_cerca" name="txt_div_fusional_cerca" maxlength="3" value="<?php echo($div_fusional_cerca); ?>" class="no-margin" style="width:50px;" onkeypress="return solo_numeros(event, false);" />
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        <td align="center" valign="top" style="width:34%">
                                                            <table style="width:100%;">
                                                                <tr>
                                                                    <td align="center" colspan="4">
                                                                        <label><b>Luces de Worth</b></label>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width:12%"></td>
                                                                    <td align="center" style="width:38%;">
                                                                        <label>Lejos</label>
                                                                    </td>
                                                                    <td align="center" style="width:38%;">
                                                                        <label>Cerca</label>
                                                                    </td>
                                                                    <td style="width:12%"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td></td>
                                                                    <td align="center">
                                                                        <?php
                                                                        $combo->getComboDb("cmb_worth_lejos", $id_worth_lejos, $lista_worth, "id_detalle,nombre_detalle", "--Seleccione--", "", 1, "", "", "no-margin");
                                                                        ?>
                                                                    </td>
                                                                    <td align="center">
                                                                        <?php
                                                                        $combo->getComboDb("cmb_worth_cerca", $id_worth_cerca, $lista_worth, "id_detalle,nombre_detalle", "--Seleccione--", "", 1, "", "", "no-margin");
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        <td align="center" valign="top" style="width:33%">
                                                            <table style="width:100%;">
                                                                <tr>
                                                                    <td align="center" colspan="2">
                                                                        <label><b>Test de estereopsis</b></label>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="right" style="width:35%;">
                                                                        <label>Mosca:</label>
                                                                    </td>
                                                                    <td align="left" style="width:65%;">
                                                                        <?php
                                                                        $combo->getComboDb("cmb_estereopsis_mosca", $id_estereopsis_mosca, $lista_estereopsis, "id_detalle,nombre_detalle", "--Seleccione--", "", 1, "", "", "no-margin");
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="right">
                                                                        <label>Animales:</label>
                                                                    </td>
                                                                    <td align="left">
                                                                        <input type="text" id="txt_valor_estereopsis_animales" name="txt_valor_estereopsis_animales" maxlength="1" value="<?php echo($valor_estereopsis_animales); ?>" class="no-margin" style="width:35px;" onkeypress="return solo_numeros(event, false);" />
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="right">
                                                                        <label>C&iacute;rculos:</label>
                                                                    </td>
                                                                    <td align="left">
                                                                        <input type="text" id="txt_valor_estereopsis_circulos" name="txt_valor_estereopsis_circulos" maxlength="1" value="<?php echo($valor_estereopsis_circulos); ?>" class="no-margin" style="width:35px;" onkeypress="return solo_numeros(event, false);" />
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center">
                                                <table style="width:100%;">
                                                    <tr>
                                                        <td align="center" style="width:30%">
                                                        </td>
                                                        <td align="center" style="width:40%">
                                                            <table style="width:100%;">
                                                                <tr>
                                                                    <td align="center" colspan="4">
                                                                        <label><b>Rejilla de Maddox</b></label>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="center" colspan="2" style="width:50%;">
                                                                        <label>Derecha</label>
                                                                    </td>
                                                                    <td align="center" colspan="2" style="width:50%;">
                                                                        <label>Izquierda</label>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="right" style="width:33%;">
                                                                        <?php
                                                                        $combo->getComboDb("cmb_maddox_der", $id_maddox_der, $lista_maddox, "id_detalle,nombre_detalle", "--Seleccione--", "", 1, "", "", "no-margin");
                                                                        ?>
                                                                    </td>
                                                                    <td align="left" style="width:17%;">
                                                                        <input type="text" id="txt_valor_maddox_der" name="txt_valor_maddox_der" maxlength="3" value="<?php echo($valor_maddox_der); ?>" class="no-margin" style="width:50px;" onkeypress="return solo_numeros(event, false);" />
                                                                    </td>
                                                                    <td align="right" style="width:33%;">
                                                                        <?php
                                                                        $combo->getComboDb("cmb_maddox_izq", $id_maddox_izq, $lista_maddox, "id_detalle,nombre_detalle", "--Seleccione--", "", 1, "", "", "no-margin");
                                                                        ?>
                                                                    </td>
                                                                    <td align="left" style="width:17%;">
                                                                        <input type="text" id="txt_valor_maddox_izq" name="txt_valor_maddox_izq" maxlength="3" value="<?php echo($valor_maddox_izq); ?>" class="no-margin" style="width:50px;" onkeypress="return solo_numeros(event, false);" />
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        <td align="center" style="width:30%">
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!--FIN EVALUACIÓN MUSCULAR Y SENSORIALIDAD-->
                                </div>
                                <div class="content" id="panel2-7">
                                    <?php
                                    $class_ordenes_remisiones->getFormularioRemisiones($id_hc_consulta, 1, $ind_editar);
                                    $class_ordenes_remisiones->getFormularioOrdenarMedicamentos($id_hc_consulta, NULL, 1, $ind_editar);
                                    $class_ordenes_remisiones->getFormularioOrdenesMedicas($id_hc_consulta, NULL, 1, $ind_editar);
                                    ?>
                                </div>
								
								<div class="content" id="panel2-8">
								 <div id="imprimir_incapacidad" style="display: none;"></div>
									<?php 
										$id_hc = $_POST["hdd_id_hc"];
										$class_incapacidades->getFormulacionIncapacidades($id_hc,$id_admision,$id_paciente,$id_usuario_profesional,$admision_obj);
									?>
								</div>
                            </div>
                        </div>
                        <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:90%;">
                            <tr valign="top">
                                <td colspan="3">
                                    <?php
                                    if (!isset($_POST["tipo_entrada"])) {
                                        ?>
                                        <input class="btnPrincipal" type="button" id="btn_imprimir" nombre="btn_imprimir" value="Imprimir" onclick="crear_oftalmologia(2, 1);" />
                                        <?php
                                    } else {
                                        ?>
                                        <input class="btnPrincipal" type="button" id="btn_imprimir" nombre="btn_imprimir" value="Imprimir" onclick="imprimir_oftalmologia();" />
                                        <?php
                                    }
                                    //Para verificar que tiene permiso de hacer cambio
                                    if ($ind_editar == 1) {
                                        if (!isset($_POST["tipo_entrada"])) {
                                            ?>
                                            <input class="btnPrincipal" type="button" id="btn_crear" nombre="btn_crear" value="Guardar cambios" onclick="crear_oftalmologia(2, 0);" />
                                            <?php
                                            $lista_tipos_citas_det_remisiones = $dbTiposCitasDetalle->get_lista_tipos_citas_det_remisiones($id_tipo_cita, $id_tipo_reg);

                                            if (count($lista_tipos_citas_det_remisiones) > 0) {
                                                ?>
                                                <input class="btnPrincipal" type="button" id="btn_enviar_a" nombre="btn_enviar_a" value="Enviar a ..." onclick="enviar_a_estados();" />
                                                <?php
                                            }

                                            if ($ind_preconsulta == "1") {
                                                ?>
                                                <input class="btnPrincipal" type="button" id="btn_finalizar" nombre="btn_finalizar" value="Finalizar preconsulta" onclick="crear_oftalmologia(4, 0);" />
                                                <?php
                                            } else {
                                                ?>
                                                <input class="btnPrincipal" type="button" id="btn_crear" nombre="btn_crear" value="Finalizar consulta" onclick="crear_oftalmologia(1, 0);" />
                                                <?php
                                            }
                                        } else {
                                            ?>
                                            <input class="btnPrincipal" type="button" id="btn_crear" nombre="btn_crear" value="Guardar" onclick="crear_oftalmologia(3, 0);" />
                                            <?php
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                        </table>
                        <br/><br/>
                    </form>
                </div>
                <?php
            } else {
                ?>
                <div class="contenedor_error" style="display:block;">Error al ingresar a consulta de oftalmolog&iacute;a</div>
                <?php
            }
            ?>        	
        </div>
        <?php
        //Se agrega el panel derecho de contactos
        obtener_listado_contactos();
        ?>
        <script type="text/javascript" src="../js/foundation.min.js"></script>
        <script type="text/javascript">
                                                $(document).foundation();

                                                $(function () {
                                                    window.prettyPrint && prettyPrint();
                                                    $("#tonometria_fecha").fdatepicker({
                                                        format: "dd/mm/yyyy"
                                                    });

                                                });

                                                initCKEditorOftalmo("txt_enfermedad_actual");
                                                initCKEditorOftalmo("txt_observaciones_gonioscopia");
                                                initCKEditorOftalmo("txt_diagnostico_oftalmo");
                                                initCKEditorOftalmo("txt_solicitud_examenes");
                                                initCKEditorOftalmo("txt_tratamiento_oftalmo");
                                                initCKEditorOftalmo("txt_observaciones_oft_pediat");
                                                initCKEditorOftalmo("txt_observaciones_tonometria");
												initCKEditorOftalmo("txt_observaciones_adicionales");
												
                                                for (var i = 0; i < <?php echo($cantidad_antecedentes); ?>; i++) {
                                                    initCKEditorOftalmo("txt_texto_antecedente_" + i);
                                                }

                                                /* Ciclo para las remisiones */
                                                for (var i = 0; i < 10; i++) {
                                                    initCKEditorOftalmo("tabla_rem_desc_" + i);
                                                }

                                                /* Ciclo para medicamentos */
                                                for (var i = 0; i < 10; i++) {
                                                    initCKEditorOftalmo("frecAdmMed_" + i);
                                                }

                                                $(document).on("ready page:load", function () {
                                                    $(function () {
                                                        $(document).foundation();
                                                    });
                                                });
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
