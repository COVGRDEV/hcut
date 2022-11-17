<?php
session_start();
/*
  Pagina para crear consulta de optometria
  Autor: Helio Ruber López - 15/11/2013
 */
require_once("../db/DbVariables.php");
require_once("../db/DbUsuarios.php");
require_once("../db/DbListas.php");
require_once("../db/DbConsultaOptometria.php");
require_once("../db/DbAdmision.php");
require_once("../db/DbDiagnosticos.php");
require_once("../db/DbTiposCitasDetalle.php");
require_once("../principal/ContenidoHtml.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../funciones/Class_Color_Pick.php");
require_once("../funciones/Class_Diagnosticos.php");
require_once("../funciones/Class_Correccion_Optica.php");
require_once("../funciones/Utilidades.php");
require_once("../funciones/FuncionesPersona.php");
require_once("../funciones/Class_Atencion_Remision.php");
require_once("../funciones/Class_Ordenes_Remisiones.php");
require_once("FuncionesHistoriaClinica.php");
require_once("antecedentes_funciones.php");

$dbVariables = new Dbvariables();
$dbUsuarios = new DbUsuarios();
$dbListas = new DbListas();
$dbConsultaOptometria = new DbConsultaOptometria();
$dbAdmision = new DbAdmision();
$dbDiagnosticos = new DbDiagnosticos();
$dbTiposCitasDetalle = new DbTiposCitasDetalle();
$class_ordenes_remisiones = new Class_Ordenes_Remisiones();

$contenido = new ContenidoHtml();
$combo = new Combo_Box();
$class_diagnosticos = new Class_Diagnosticos();
$correccion_optica = new Class_Correccion_Optica();
$utilidades = new Utilidades();

//variables
$titulo = $dbVariables->getVariable(1);
$horas_edicion = $dbVariables->getVariable(7);

//Lista para campos Sí/No 
$lista_si_no = $dbListas->getListaDetalles(61);

//Lista para combos cantidades
$lista_numerica = array();
for ($i = 0; $i <= 5; $i++) {
    $lista_numerica[$i]["id"] = $i;
    $lista_numerica[$i]["valor"] = $i;
}

$combo = new Combo_Box();

//Cambiar las variables get a post
$utilidades->get_a_post();
$funciones_personas = new FuncionesPersona();
$funciones_hc = new FuncionesHistoriaClinica();
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
        <!--Para autocompletar DEBE IR DE PRIMERO-->
        <script type="text/javascript" src="../js/jquery_autocompletar.js"></script>
        <script type="text/javascript" src="../js/jquery-ui.js"></script>
        <!--Para validar DEBE IR DE SEGUNDO-->
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.add.js"></script>
        <script type='text/javascript'  src="../js/sweetalert2.all.min.js"></script>
        <!--Para funciones de optometria DEBE IR DE TERCERO-->
        <script type="text/javascript" src="../js/ajax.js"></script>
        <script type="text/javascript" src="../js/funciones.js"></script>
        <script type="text/javascript" src="../js/validaFecha.js"></script>
        <script type="text/javascript" src="../js/Class_Diagnosticos_v1.1.js"></script>
        <script type="text/javascript" src="../js/Class_Atencion_Remision_v1.3.js"></script>
        <script type="text/javascript" src="../js/Class_Ordenes_Remisiones_v1.js"></script>
        <script type="text/javascript" src="../funciones/ckeditor/ckeditor.js"></script>
        <script type="text/javascript" src="../funciones/ckeditor/config.js"></script>
        <script type="text/javascript" src="../js/Class_Color_Pick.js"></script>
        <script type="text/javascript" src="../js/jquery.textarea_autosize.js"></script>
        <script type="text/javascript" src="historia_clinica_v1.1.js"></script>
        <script type="text/javascript" src="FuncionesHistoriaClinica.js"></script>
        <script type="text/javascript" src="consulta_optometria_v1.1.js"></script>
        <script type="text/javascript" src="antecedentes_v1.0.js"></script>
        <?php
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
        <script id="ajax">
            $(function () {
                var Tags_diagnosticos = [<?php echo($cadena_diagnosticos) ?>];

                for (k = 1; k <= 10; k++) {
                    $("#txt_busca_diagnostico_" + k).autocomplete({source: Tags_diagnosticos});
                }
            });
        </script>	
        <?php
        //Array valores esfera
        $cadena_esfera = "'0'";
        for ($i = 0; $i <= 30; $i++) {
            if ($cadena_esfera != "") {
                $cadena_esfera .= ",";
            }
            if ($i > 0) {
                $cadena_esfera .= "'-" . $i . ",00',";
            }
            $cadena_esfera .= "'-" . $i . ",25',";
            $cadena_esfera .= "'-" . $i . ",50',";
            $cadena_esfera .= "'-" . $i . ",75',";
            if ($i > 0) {
                $cadena_esfera .= "'+" . $i . ",00',";
            }
            $cadena_esfera .= "'+" . $i . ",25',";
            $cadena_esfera .= "'+" . $i . ",50',";
            $cadena_esfera .= "'+" . $i . ",75'";
        }
        $cadena_esfera .= ",'NP/NA'";

        //Array valores cilindro
        $cadena_cilindro = "'0'";
        for ($i = 0; $i <= 10; $i++) {
            if ($cadena_cilindro != "") {
                $cadena_cilindro .= ",";
            }
            if ($i > 0) {
                $cadena_cilindro .= "'-" . $i . ",00',";
            }
            $cadena_cilindro .= "'-" . $i . ",25',";
            $cadena_cilindro .= "'-" . $i . ",50',";
            $cadena_cilindro .= "'-" . $i . ",75'";
        }
        $cadena_cilindro .= ",'NP/NA'";

        //Array valores eje
        $cadena_eje = "'0'";
        for ($i = 1; $i <= 180; $i++) {
            if ($cadena_eje != "") {
                $cadena_eje .= ",";
            }
            $cadena_eje .= "'" . $i . "'";
        }
        $cadena_eje .= ",'NP/NA'";

        //Array valores adicion
        $cadena_adicion = "'0'";
        for ($i = 0; $i <= 3; $i++) {
            if ($cadena_adicion != "") {
                $cadena_adicion .= ",";
            }
            if ($i > 0) {
                $cadena_adicion .= "'" . $i . ",00',";
            }
            $cadena_adicion .= "'" . $i . ",25',";
            $cadena_adicion .= "'" . $i . ",50',";
            $cadena_adicion .= "'" . $i . ",75'";
        }
        $cadena_adicion .= ",'NP/NA'";

        //Array valores querato
        $cadena_querato = "";
        for ($i = 30; $i <= 65; $i++) {
            if ($cadena_querato != "") {
                $cadena_querato .= ",";
            }
            $cadena_querato .= "'" . $i . ",00',";
            $cadena_querato .= "'" . $i . ",25',";
            $cadena_querato .= "'" . $i . ",50',";
            $cadena_querato .= "'" . $i . ",75'";
        }
        $cadena_querato .= ",'NP/NA'";

        //Array valores Potencia Prisma
        $cadena_prisma_potencia = "";
        for ($i = 1; $i <= 50; $i++) {
            $cadena_prisma_potencia .= "'" . $i . "',";
        }
        $cadena_prisma_potencia = substr($cadena_prisma_potencia, 0, -1);
        ?>	
        <script id="ajax">
            var array_esfera = [<?php echo($cadena_esfera) ?>];
            var array_cilindro = [<?php echo($cadena_cilindro) ?>];
            var array_eje = [<?php echo($cadena_eje) ?>];
            var array_adicion = [<?php echo($cadena_adicion) ?>];
            var array_querato = [<?php echo($cadena_querato) ?>];
            var array_prisma_potencia = [<?php echo($cadena_prisma_potencia) ?>];

            var Tags_esfera = [<?php echo($cadena_esfera) ?>];
            var Tags_cilindro = [<?php echo($cadena_cilindro) ?>];
            var Tags_eje = [<?php echo($cadena_eje) ?>];
            var Tags_adicion = [<?php echo($cadena_adicion) ?>];
            var Tags_querato = [<?php echo($cadena_querato) ?>];
            var Tags_prisma_potencia = [<?php echo($cadena_prisma_potencia) ?>];

            $(function () {

                //Para formularios de gafas
                config_autocomplete(4);

                //Para Queratometria OD
                $("#querato_dif_od").autocomplete({source: Tags_cilindro});
                $("#querato_k1_od").autocomplete({source: Tags_querato});

                //Para Queratometria OI
                $("#querato_dif_oi").autocomplete({source: Tags_cilindro});
                $("#querato_k1_oi").autocomplete({source: Tags_querato});

                //Para refraccion objetivo OD
                $("#refraobj_esfera_od").autocomplete({source: Tags_esfera});
                $("#refraobj_cilindro_od").autocomplete({source: Tags_cilindro});

                //Para refraccion objetivo OI
                $("#refraobj_esfera_oi").autocomplete({source: Tags_esfera});
                $("#refraobj_cilindro_oi").autocomplete({source: Tags_cilindro});

                //Para Subjetivo OD
                $("#subjetivo_esfera_od").autocomplete({source: Tags_esfera});
                $("#subjetivo_cilindro_od").autocomplete({source: Tags_cilindro});
                $("#subjetivo_adicion_od").autocomplete({source: Tags_adicion});

                //Para Subjetivo OI
                $("#subjetivo_esfera_oi").autocomplete({source: Tags_esfera});
                $("#subjetivo_cilindro_oi").autocomplete({source: Tags_cilindro});
                $("#subjetivo_adicion_oi").autocomplete({source: Tags_adicion});

                //Para Cicloplejio OD
                $("#cicloplejio_esfera_od").autocomplete({source: Tags_esfera});
                $("#cicloplejio_cilindro_od").autocomplete({source: Tags_cilindro});

                //Para Cicloplejio OI
                $("#cicloplejio_esfera_oi").autocomplete({source: Tags_esfera});
                $("#cicloplejio_cilindro_oi").autocomplete({source: Tags_cilindro});

                //Para Refraccion Final OD
                $("#refrafinal_esfera_od").autocomplete({source: Tags_esfera});
                $("#refrafinal_cilindro_od").autocomplete({source: Tags_cilindro});
                $("#refrafinal_adicion_od").autocomplete({source: Tags_adicion});

                //Para Refraccion Final OI
                $("#refrafinal_esfera_oi").autocomplete({source: Tags_esfera});
                $("#refrafinal_cilindro_oi").autocomplete({source: Tags_cilindro});
                $("#refrafinal_adicion_oi").autocomplete({source: Tags_adicion});
            });
        </script>
    </head>
    <body onload="ajustar_textareas();">
        <?php
        $contenido->validar_seguridad(0);
        if (!isset($_POST["tipo_entrada"])) {
            $contenido->cabecera_html();
        }

        $valores_av = $dbListas->getListaDetalles(11);
        $valores_satisfaccion = $dbListas->getListaDetalles(41);

        $id_tipo_reg = 1; // Tipo de registros Tabla:tipos_registros_hc= CONSULTA DE OPTOMETRIA 
        $id_usuario = $_SESSION["idUsuario"];

        //Variables de Optometria
		if (isset($_POST["hdd_id_paciente"])) {
            $id_paciente = $_POST["hdd_id_paciente"];
            $nombre_paciente = $_POST["hdd_nombre_paciente"];
            $id_admision = $_POST["hdd_id_admision"];

            //Se obtienen los datos de la admision
            $admision_obj = $dbAdmision->get_admision($id_admision);
			
            if (!isset($_POST["tipo_entrada"])) {
                $tabla_hc = $dbConsultaOptometria->getHistoriaClinicaAdmision($id_admision, $id_tipo_reg);
            } else {
                $id_hc = $_POST["hdd_id_hc"];
                $tabla_hc = $dbConsultaOptometria->getHistoriaClinicaId($id_hc);
            }

            if (count($tabla_hc) > 0) { //Si se encuentra una HC para este paciente se carga los datos de lo contrario se crea la HC
                $tipo_accion = "2"; //Editar consulta optometria
                $id_hc_consulta = $tabla_hc["id_hc"];
                //se obtiene el registro de la consulta de optometria a partir del ID de la Historia Clinica 
                $tabla_optometria = $dbConsultaOptometria->getConsultaOptometria($id_hc_consulta);
                $txt_anamnesis = $tabla_optometria["anamnesis"];
                $avsc_lejos_od = $tabla_optometria["avsc_lejos_od"];
                $avsc_media_od = $tabla_optometria["avsc_media_od"];
                $avsc_cerca_od = $tabla_optometria["avsc_cerca_od"];
                $avsc_lejos_oi = $tabla_optometria["avsc_lejos_oi"];
                $avsc_media_oi = $tabla_optometria["avsc_media_oi"];
                $avsc_cerca_oi = $tabla_optometria["avsc_cerca_oi"];
                $querato_k1_od = $tabla_optometria["querato_k1_od"];
                $querato_ejek1_od = $tabla_optometria["querato_ejek1_od"];
                $querato_dif_od = $tabla_optometria["querato_dif_od"];
                $querato_k1_oi = $tabla_optometria["querato_k1_oi"];
                $querato_ejek1_oi = $tabla_optometria["querato_ejek1_oi"];
                $querato_dif_oi = $tabla_optometria["querato_dif_oi"];
                $refraobj_esfera_od = $tabla_optometria["refraobj_esfera_od"];
                $refraobj_cilindro_od = $tabla_optometria["refraobj_cilindro_od"];
                $refraobj_eje_od = $tabla_optometria["refraobj_eje_od"];
                $refraobj_lejos_od = $tabla_optometria["refraobj_lejos_od"];
                $refraobj_esfera_oi = $tabla_optometria["refraobj_esfera_oi"];
                $refraobj_cilindro_oi = $tabla_optometria["refraobj_cilindro_oi"];
                $refraobj_eje_oi = $tabla_optometria["refraobj_eje_oi"];
                $refraobj_lejos_oi = $tabla_optometria["refraobj_lejos_oi"];
                $subjetivo_esfera_od = $tabla_optometria["subjetivo_esfera_od"];
                $subjetivo_cilindro_od = $tabla_optometria["subjetivo_cilindro_od"];
                $subjetivo_eje_od = $tabla_optometria["subjetivo_eje_od"];
                $subjetivo_lejos_od = $tabla_optometria["subjetivo_lejos_od"];
                $subjetivo_media_od = $tabla_optometria["subjetivo_media_od"];
                $subjetivo_ph_od = $tabla_optometria["subjetivo_ph_od"];
                $subjetivo_adicion_od = $tabla_optometria["subjetivo_adicion_od"];
                $subjetivo_cerca_od = $tabla_optometria["subjetivo_cerca_od"];
                $subjetivo_esfera_oi = $tabla_optometria["subjetivo_esfera_oi"];
                $subjetivo_cilindro_oi = $tabla_optometria["subjetivo_cilindro_oi"];
                $subjetivo_eje_oi = $tabla_optometria["subjetivo_eje_oi"];
                $subjetivo_lejos_oi = $tabla_optometria["subjetivo_lejos_oi"];
                $subjetivo_media_oi = $tabla_optometria["subjetivo_media_oi"];
                $subjetivo_ph_oi = $tabla_optometria["subjetivo_ph_oi"];
                $subjetivo_adicion_oi = $tabla_optometria["subjetivo_adicion_oi"];
                $subjetivo_cerca_oi = $tabla_optometria["subjetivo_cerca_oi"];
                $cicloplejio_esfera_od = $tabla_optometria["cicloplejio_esfera_od"];
                $cicloplejio_cilindro_od = $tabla_optometria["cicloplejio_cilindro_od"];
                $cicloplejio_eje_od = $tabla_optometria["cicloplejio_eje_od"];
                $cicloplejio_lejos_od = $tabla_optometria["cicloplejio_lejos_od"];
                $cicloplejio_esfera_oi = $tabla_optometria["cicloplejio_esfera_oi"];
                $cicloplejio_cilindro_oi = $tabla_optometria["cicloplejio_cilindro_oi"];
                $cicloplejio_eje_oi = $tabla_optometria["cicloplejio_eje_oi"];
                $cicloplejio_lejos_oi = $tabla_optometria["cicloplejio_lejos_oi"];
                $refrafinal_esfera_od = $tabla_optometria["refrafinal_esfera_od"];
                $refrafinal_cilindro_od = $tabla_optometria["refrafinal_cilindro_od"];
                $refrafinal_eje_od = $tabla_optometria["refrafinal_eje_od"];
                $refrafinal_adicion_od = $tabla_optometria["refrafinal_adicion_od"];
                $refrafinal_esfera_oi = $tabla_optometria["refrafinal_esfera_oi"];
                $refrafinal_cilindro_oi = $tabla_optometria["refrafinal_cilindro_oi"];
                $refrafinal_eje_oi = $tabla_optometria["refrafinal_eje_oi"];
                $refrafinal_adicion_oi = $tabla_optometria["refrafinal_adicion_oi"];
                $presion_intraocular_od = $tabla_optometria["presion_intraocular_od"];
                $presion_intraocular_oi = $tabla_optometria["presion_intraocular_oi"];
                $diagnostico_optometria = $tabla_optometria["diagnostico_optometria"];
                $txt_observaciones_subjetivo = $tabla_optometria["observaciones_subjetivo"];
                $txt_observaciones_optometria = $tabla_optometria["observaciones_optometria"];
                $txt_observaciones_rxfinal = $tabla_optometria["observaciones_rxfinal"];

                $txt_observaciones_avsc = $tabla_optometria["observaciones_avsc"];
                $txt_observaciones_lensometria = $tabla_optometria["observaciones_lensometria"];
                $txt_observaciones_queratometria = $tabla_optometria["observaciones_queratometria"];
                $txt_observaciones_objetivo = $tabla_optometria["observaciones_objetivo"];
                $txt_observaciones_subjetivo_2 = $tabla_optometria["observaciones_subjetivo_2"];
                $txt_observaciones_cicloplejia = $tabla_optometria["observaciones_cicloplejia"];
                $txt_observaciones_rxfinal_2 = $tabla_optometria["observaciones_rxfinal_2"];
                $txt_observaciones_pin = $tabla_optometria["observaciones_pin"];
                $cmb_validar_consulta = $tabla_optometria["validar_completa"];
                $cmb_examinado_antes = $tabla_optometria["ind_examinado_ant"];
                $rx_gafas = $tabla_optometria["rx_anteojos"];
                $rx_ldc = $tabla_optometria["rx_lc"];
                $rx_refractiva = $tabla_optometria["rx_refractiva"];
                $rx_ayudas_bv = $tabla_optometria["rx_ayudas_bv"];
                $alternativa_anteojos = $tabla_optometria["alternativa_anteojos"];
                $alternativa_lc = $tabla_optometria["alternativa_lc"];
                $alternativa_refractiva = $tabla_optometria["alternativa_refractiva"];
                $alternativa_otra = $tabla_optometria["alternativa_otra"];
                $cmb_grado_satisfaccion = $tabla_optometria["cmb_grado_satisfaccion"];
                $cmb_paciente_dilatado = $tabla_optometria["ind_dilatado"];
                $observaciones_optometricas_finales = $tabla_optometria["observaciones_optometricas_finales"];
                $id_ojo = $tabla_optometria["id_ojo"];
                $cmb_dominancia_ocular = $tabla_optometria["cmb_dominancia_ocular"];
                $nombre_usuario_alt = $tabla_hc["nombre_usuario_alt"];
                $tipo_lente = $tabla_optometria["tipo_lente"];
				$id_tipo_lente = $tabla_optometria["tipos_lentes_slct"];
				$id_tipo_filtro = $tabla_optometria["tipo_filtro_slct"];
				$id_tiempo_vigencia = $tabla_optometria["tiempo_vigencia_slct"];
				$id_tiempo_periodo = $tabla_optometria["tiempo_periodo"];
				$distancia_pupilar = $tabla_optometria["distancia_pupilar"];
				$form_cantidad	= $tabla_optometria["form_cantidad"];
				$observaciones_admision = $admision_obj["observaciones_admision"];

                // Detalle Rx en uso:  
                if ($rx_gafas == 1 or $rx_ldc == 1 or $rx_refractiva == 1 or $rx_ayudas_bv == 1) {
                    $cmb_usa_correccion = 1;
                } else {
                    $cmb_usa_correccion = 0;
                }

                //Se verifica si se debe actualizar el estado de la admisión asociada
                $en_atencion = "0";
                if (isset($_POST["hdd_en_atencion"])) {
                    $en_atencion = $_POST["hdd_en_atencion"];
                }

                if ($en_atencion == "1") {
                    $dbAdmision->editar_admision_estado($id_admision, 4, 1, $id_usuario);
                }
            } else {//Entre en procesos de crear HC
                $tipo_accion = "1"; //Crear consulta optometria
                //Se crea la historia clinica y se inicia la consulta de optometria
                $id_hc_consulta = $dbConsultaOptometria->CrearConsultaOptometria($id_paciente, $id_tipo_reg, $id_usuario, $id_admision);
                if ($id_hc_consulta < 0) {//Ninguna accion Error
                    $tipo_accion = "0";
                } else {
                    $tabla_optometria = $dbConsultaOptometria->getConsultaOptometria($id_hc_consulta);
                }
                //Variables de inicio de conuslta de optometria
                $txt_anamnesis = "";
                $avsc_lejos_od = "";
                $avsc_media_od = "";
                $avsc_cerca_od = "";
                $avsc_lejos_oi = "";
                $avsc_media_oi = "";
                $avsc_cerca_oi = "";
                $querato_k1_od = "";
                $querato_ejek1_od = "";
                $querato_dif_od = "";
                $querato_k1_oi = "";
                $querato_ejek1_oi = "";
                $querato_dif_oi = "";
                $refraobj_esfera_od = "";
                $refraobj_cilindro_od = "";
                $refraobj_eje_od = "";
                $refraobj_lejos_od = "";
                $refraobj_esfera_oi = "";
                $refraobj_cilindro_oi = "";
                $refraobj_eje_oi = "";
                $refraobj_lejos_oi = "";
                $subjetivo_esfera_od = "";
                $subjetivo_cilindro_od = "";
                $subjetivo_eje_od = "";
                $subjetivo_lejos_od = "";
                $subjetivo_media_od = "";
                $subjetivo_ph_od = "";
                $subjetivo_adicion_od = "";
                $subjetivo_cerca_od = "";
                $subjetivo_esfera_oi = "";
                $subjetivo_cilindro_oi = "";
                $subjetivo_eje_oi = "";
                $subjetivo_lejos_oi = "";
                $subjetivo_media_oi = "";
                $subjetivo_ph_oi = "";
                $subjetivo_adicion_oi = "";
                $subjetivo_cerca_oi = "";
                $cicloplejio_esfera_od = "";
                $cicloplejio_cilindro_od = "";
                $cicloplejio_eje_od = "";
                $cicloplejio_lejos_od = "";
                $cicloplejio_esfera_oi = "";
                $cicloplejio_cilindro_oi = "";
                $cicloplejio_eje_oi = "";
                $cicloplejio_lejos_oi = "";
                $refrafinal_esfera_od = "";
                $refrafinal_cilindro_od = "";
                $refrafinal_eje_od = "";
                $refrafinal_adicion_od = "";
                $refrafinal_esfera_oi = "";
                $refrafinal_cilindro_oi = "";
                $refrafinal_eje_oi = "";
                $refrafinal_adicion_oi = "";
                $presion_intraocular_od = "";
                $presion_intraocular_oi = "";
                $diagnostico_optometria = "";
                $txt_observaciones_subjetivo = "";
                $txt_observaciones_optometria = "";
                $txt_observaciones_rxfinal = "";

                $txt_observaciones_avsc = "";
                $txt_observaciones_lensometria = "";
                $txt_observaciones_queratometria = "";
                $txt_observaciones_objetivo = "";
                $txt_observaciones_subjetivo_2 = "";
                $txt_observaciones_cicloplejia = "";
                $txt_observaciones_rxfinal_2 = "";
                $txt_observaciones_pin = "";
                $cmb_validar_consulta = 1;

                $cmb_examinado_antes = "";
                $cmb_usa_correccion = "";

                $rx_gafas = "";
                $rx_ldc = "";
                $rx_refractiva = "";
                $rx_ayudas_bv = "";
                $alternativa_anteojos = "";
                $alternativa_lc = "";
                $alternativa_refractiva = "";
                $alternativa_otra = "";
                $cmb_grado_satisfaccion = "";
                $cmb_paciente_dilatado = "";
                $observaciones_optometricas_finales = "";
                $id_ojo = "81";
                $cmb_dominancia_ocular = $tabla_optometria["cmb_dominancia_ocular"];
                $nombre_usuario_alt = $tabla_hc["nombre_usuario_alt"];
                $tipo_lente = "";
				
				$id_tipo_lente = '';
				$id_tipo_filtro = '';
				$id_tiempo_vigencia = '';
				$id_tiempo_periodo = '';
				$distancia_pupilar = '';
				$form_cantidad	= '';

                $observaciones_admision = "";
            }

            //Se obtienen los datos del registro de historia clínica
            $historia_clinica_obj = $dbConsultaOptometria->getHistoriaClinicaId($id_hc_consulta);
        } else {
            $tipo_accion = "0"; //Ninguna accion Error
        }

        //fecha de la historia clinica
        $fecha_hc_t = $tabla_optometria["fecha_hc_t"];

        //Nombre del profesional que atiende la consulta
        $id_usuario_profesional = $tabla_optometria["id_usuario_crea"];
        $usuario_profesional_obj = $dbUsuarios->getUsuario($id_usuario_profesional);
        $nombre_usuario_profesional = $usuario_profesional_obj["nombre_usuario"] . " " . $usuario_profesional_obj["apellido_usuario"];

        if (!isset($_POST["tipo_entrada"])) {
            ?>
            <div class="title-bar title_hc">
                <div class="wrapper">
                    <div class="breadcrumb">
                        <ul>
                            <li class="breadcrumb_on">Consulta de Optometr&iacute;a</li>
                        </ul>
                    </div>
                </div>
            </div>
            <?php
        }
		
        if ($tipo_accion > 0) {
            /* ****************************************************** */
            /* Variable que contiene la cantidad de campos de colores */
            /* ****************************************************** */
            $cantidad_campos_colores = 280; //del 1 al 100 para objetos del formulario inicial; del 100-280 para objetos creados en tiempo de ejecución - LensoAnteojos
            //Se obtiene el listado de colores
            $arr_colores = array();
            $arr_cadenas_colores = array();
            $lista_cadenas = $dbConsultaOptometria->getListaHistoriaClinicaColoresCampos($id_hc_consulta);
            if (count($lista_cadenas) > 0) {
                foreach ($lista_cadenas as $reg_cadena) {
                    array_push($arr_cadenas_colores, $reg_cadena["cadena_colores"]);
                }
            } else {
                // cadena de colores por defecto: campos AVSC Verdes
                $arr_cadenas_colores[0] = "0003033030000000000000000000000000000000000000000000000000000000000000000000000003300000000000000000";
            }

            //Se instancia la clase que administrará los colores de los campos
            $colorPick = new Color_Pick($arr_cadenas_colores, $cantidad_campos_colores);
            $arr_colores = $colorPick->getArrayColores();

            //Para verificaro que tiene permiso de hacer cambio
            $ind_editar = $dbConsultaOptometria->getIndicadorEdicion($id_hc_consulta, $horas_edicion["valor_variable"]);

            $ind_editar_enc_hc = $ind_editar;
            if ($ind_editar == 1 && isset($_POST["tipo_entrada"])) {
                $ind_editar_enc_hc = 0;
            }

            if (!isset($_POST["tipo_entrada"]) || $_POST["tipo_entrada"] == 1) {
                $funciones_hc->encabezado_historia_clinica($id_paciente, $id_admision, $id_hc_consulta, $ind_editar_enc_hc, false);
            }
            ?>
            <div class="contenedor_principal" id="id_contenedor_principal">
                <div id="guardar_optometria" style="width: 100%; display: block;">
                    <div class="contenedor_error" id="contenedor_error"></div>
                    <div class="contenedor_exito" id="contenedor_exito"></div>
                </div>	
                <div id="imprimir_formula" style="width: 100%; display: none;"></div>
                <div class="formulario" id="principal_optometria" style="width: 100%; display: block; ">
                    <?php
                    //Se inserta el registro de ingreso a la historia clínica
                    $dbConsultaOptometria->crear_ingreso_hc($id_usuario, $id_paciente, $id_admision, $id_hc_consulta, 160);
                    ?>
                    <form id="frm_consulta_optometria" name="frm_consulta_optometria" method="post">
                        <input type="hidden" name="hdd_id_hc_consulta" id="hdd_id_hc_consulta" value="<?php echo($id_hc_consulta); ?>" />
                        <input type="hidden" name="hdd_id_admision" id="hdd_id_admision" value="<?php echo($id_admision); ?>" />
                        <input type="hidden" name="hdd_id_paciente" id="hdd_id_paciente" value="<?php echo($id_paciente); ?>" />
                        <input type="hidden" name="hdd_id_convenio" id="hdd_id_convenio" value="<?php echo($admision_obj["id_convenio"]); ?>" />
                        <input type="hidden" name="hdd_id_plan" id="hdd_id_plan" value="<?php echo($admision_obj["id_plan"]); ?>" />
                        <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
                            <tr>
                                <th align="left" style="width:60%;">
                                    <h6 style="margin: 1px;">
                                        <input type="hidden" id="hdd_usuario_anonimo" value="<?php echo($usuario_profesional_obj["ind_anonimo"]); ?>" />
                                        <b>Opt&oacute;metra: </b>
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
                                <th align="left" style="width:40%;">
                                    <h6 style="margin: 1px;">
                                        <b>Consulta completa:</b> 
                                        <select id="cmb_validar_consulta" class="select_hc" style="width: 60px; margin: 1px;">
                                            <?php
                                            for ($i = 1; $i <= 2; $i++) {
                                                $selected = "";
                                                if ($i == 1) {
                                                    $text = "Si";
                                                } else if ($i == 2) {
                                                    $text = "No";
                                                }

                                                if ($i == $cmb_validar_consulta) {
                                                    $selected = " selected=\"selected\"";
                                                }
                                                ?>
                                                <option value="<?php echo $i; ?>"<?php echo($selected); ?>><?php echo $text; ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </h6>
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
                        <div class="tabs-container">
                            <dl class="tabs" data-tab>
                                <dd id="panel_opt_1"><a href="#panel2-1" onclick="setTimeout(function () {
                                                ajustar_textareas();
                                            }, 100);">Antecedentes</a></dd>
                                <dd id="panel_oft_2" class="active"><a href="#panel2-2" onclick="setTimeout(function () {
                                                ajustar_textareas();
                                            }, 100);">Optometr&iacute;a</a></dd>
                                <dd id="panel_oft_3"><a href="#panel2-3" onclick="setTimeout(function () {
                                                ajustar_textareas();
                                            }, 100);">&Oacute;rdenes y Remisiones</a></dd>
                            </dl>
                            <div class="tabs-content" style="padding:0px;margin: 0px;">
                                <div class="content" id="panel2-1">
                                    <?php
                                    require("antecedentes.php");
                                    ?>
                                </div>
                                <div class="content active" id="panel2-2">
                                    <!--INICIO ANAMNESIS-->
                                    <table border="0" cellpadding="0" cellspacing="0" align="center" style="width:98%;">
                                        <tr>
                                            <td align="center" colspan="3" class="">
                                                <h5 style="margin: 10px">Anamnesis *</h5>
                                                <div id="txt_anamnesis"><?php echo($utilidades->ajustar_texto_wysiwyg($txt_anamnesis)); ?></div>
                                                <br /><br />
                                            </td>
                                        </tr>
                                    </table>
                                    <!--FIN ANAMNESIS-->
                                    <?php
                                    if ($rx_gafas == 1) {
                                        $rx_gafas = "checked";
                                    } else {
                                        $rx_gafas = "";
                                    }
                                    if ($rx_ldc == 1) {
                                        $rx_ldc = "checked";
                                    } else {
                                        $rx_ldc = "";
                                    }
                                    if ($rx_refractiva == 1) {
                                        $rx_refractiva = "checked";
                                    } else {
                                        $rx_refractiva = "";
                                    }
                                    if ($rx_ayudas_bv == 1) {
                                        $rx_ayudas_bv = "checked";
                                    } else {
                                        $rx_ayudas_bv = "";
                                    }
                                    if ($alternativa_anteojos == 1) {
                                        $alternativa_anteojos = "checked";
                                    } else {
                                        $alternativa_anteojos = "";
                                    }
                                    if ($alternativa_lc == 1) {
                                        $alternativa_lc = "checked";
                                    } else {
                                        $alternativa_lc = "";
                                    }
                                    if ($alternativa_refractiva == 1) {
                                        $alternativa_refractiva = "checked";
                                    } else {
                                        $alternativa_refractiva = "";
                                    }

                                    $lista_ojos = $dbListas->getListaDetalles(14);
                                    ?>
                                    <input type="hidden" id="hdd_cant_color_pick" value="<?php echo($cantidad_campos_colores); ?>"/>				
                                    <table border="0" cellpadding="0" cellspacing="4" align="center" style="width:98%;">					
                                        <tr>
                                            <td align="left" style="width:35%;"><h6 style="margin: 1px;"><b>&iquest;Ha sido examinado de los ojos anteriormente?:</b></h6></td>
                                            <td align="left">
                                                <?php
                                                $combo->getComboDb("cmb_examinado_antes", $cmb_examinado_antes, $lista_si_no, "codigo_detalle, nombre_detalle", " ", "obj_dependientes(1)", "", "", "", "select_hc");
                                                ?>
                                            </td>
                                        </tr> 
                                        <tr><td colspan="4">&nbsp;</td></tr>
                                        <tr id="zona_uso_co">
                                            <td align="left"><h6 style="margin: 1px;"><b>&iquest;Usa alg&uacute;n tipo de correcci&oacute;n &oacute;ptica?:</b></h6></td>
                                            <td align="left" colspan="3">
                                                <?php
                                                $combo->getComboDb("cmb_usa_correccion", $cmb_usa_correccion, $lista_si_no, "codigo_detalle, nombre_detalle", " ", "obj_dependientes(2)", "", "", "", "select_hc");
                                                ?>
                                            </td>
                                        </tr>	
                                        <tr id="zona_tipo_co">
                                            <td align="left"><h6 style="margin: 1px;"><b>Tipo de Correcci&oacute;n &Oacute;ptica en uso:</b></h6></td>
                                            <td align="left" colspan="3">
                                                <input id="rx_gafas" type="checkbox" style="margin: 1px;" <?php echo ($rx_gafas); ?> onclick="config_seccion_correccion_optica('gafas')"><label for="rx_gafas">Anteojos</label>
                                                    <input id="rx_ldc" type="checkbox" style="margin: 1px;" <?php echo ($rx_ldc); ?> onclick="config_seccion_correccion_optica('ldc')"><label for="rx_ldc">LdC</label>
                                                        <input id="rx_abv" type="checkbox" style="margin: 1px;" <?php echo ($rx_ayudas_bv); ?> onclick="config_seccion_correccion_optica('abv')"><label for="rx_abv">Ayudas en Baja Visi&oacute;n</label>
                                                            <input id="rx_cxr" type="checkbox" style="margin: 1px;" <?php echo ($rx_refractiva); ?> onclick="config_seccion_correccion_optica('cxr')"><label for="rx_cxr">Cx Refractiva</label>
                                                                </td>					
                                                                </tr>
                                                                <tr><td colspan="4">&nbsp;</td></tr>
                                                                </table>
                                                                <div id="zona_frms_tipo_co">
                                                                    <div id="div_ajax_gafas" style="display:none"></div> 
                                                                    <div id="div_ajax_ldc" style="display:none"></div> 
                                                                    <div id="div_ajax_abv" style="display:none"></div> 
                                                                    <div id="div_ajax_cxr" style="display:none"></div> 						
                                                                    <fieldset class="grupo_formularios" id="seccion_gafas"> 						
                                                                        <div class="restar_alemetos_dere" onclick="quitar_frm('gafas');" title="Quitar Último Par de Anteojos"></div>
                                                                        <div id="btn_add_gafas" class="agregar_alemetos_dere" onclick="agregar_frm('gafas');" title="Agregar Otro Par de Anteojos"></div>
                                                                        <legend>Anteojos en uso</legend>					
                                                                        <div id="div_gafas">
                                                                            <?php
                                                                            // Dibujar los formularios de gafas registradas
                                                                            $tabla_gafas = $dbConsultaOptometria->getHcGafas($id_hc_consulta);
                                                                            $canti = count($tabla_gafas);

                                                                            $id_nueva_fila = 0; //índice de los objetos a pintar
                                                                            foreach ($tabla_gafas as $registro) {
                                                                                $id_nueva_fila++;
                                                                                $correccion_optica->getFormularioGafas($id_nueva_fila, $registro);
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                        <div class="div_separador"></div> 
                                                                        <div align="right"> 
                                                                            <img src="../imagenes/icon-blue-down.png" id="img_ocultar_gafas" class="img_button" onclick="efecto_capas('gafas');" title="Mostrar Sección Anteojos"> 
                                                                                <img src="../imagenes/icon-blue-up.png" id="img_ver_gafas" class="img_button" onclick="efecto_capas('gafas');" title="Ocultar Sección Anteojos"> 
                                                                                    </div>						
                                                                                    </fieldset>
                                                                                    <input type="hidden" id="hdd_canti_gafas" value="<?php echo($canti); ?>">
                                                                                        <fieldset class="grupo_formularios" id="seccion_ldc">
                                                                                            <div class="restar_alemetos_dere" onclick="quitar_frm('ldc');" title="Quitar Último Par de Lentes de Contacto"></div>
                                                                                            <div id="btn_add_ldc" class="agregar_alemetos_dere" onclick="agregar_frm('ldc');" title="Agregar Otro Par de Lentes de Contacto"></div>					
                                                                                            <legend>Lentes de Contacto en uso</legend>
                                                                                            <div id="div_ldc">
                                                                                                <?php
                                                                                                // Dibujar los formularios de lentes de contacto registrados
                                                                                                $tabla = $dbConsultaOptometria->getHcLentesDeContacto($id_hc_consulta);
                                                                                                $canti = count($tabla);

                                                                                                $id_nueva_fila = 0; //índice de los objetos a pintar
                                                                                                foreach ($tabla as $registro) {
                                                                                                    $id_nueva_fila++;
                                                                                                    $correccion_optica->getFormularioLentesDeContacto($id_nueva_fila, $registro);
                                                                                                }
                                                                                                ?>
                                                                                            </div>						
                                                                                            <div align="right"> 
                                                                                                <img src="../imagenes/icon-blue-down.png" id="img_ocultar_ldc" class="img_button" onclick="efecto_capas('ldc');" title="Mostrar Sección Lentes de Contacto"> 
                                                                                                    <img src="../imagenes/icon-blue-up.png" id="img_ver_ldc" class="img_button" onclick="efecto_capas('ldc');" title="Ocultar Sección Lentes de Contacto"> 
                                                                                                        </div> 						
                                                                                                        </fieldset>
                                                                                                        <input type="hidden" id="hdd_canti_ldc" value="<?php echo $canti ?>">
                                                                                                            <fieldset class="grupo_formularios" id="seccion_abv">
                                                                                                                <div class="restar_alemetos_dere" onclick="quitar_frm('abv');" title="Quitar Última Ayuda en Baja Visión"></div>
                                                                                                                <div id="btn_add_abv" class="agregar_alemetos_dere" onclick="agregar_frm('abv');" title="Agregar Otra Ayuda en Baja Visión"></div>										
                                                                                                                <legend>Ayudas en Baja Visi&oacute;n en uso</legend> 
                                                                                                                <div id="div_abv">
                                                                                                                    <?php
                                                                                                                    // Dibujar los formularios de ayudas en baja visión registradas
                                                                                                                    $tabla = $dbConsultaOptometria->getHcAyudasBajaVision($id_hc_consulta);
                                                                                                                    $canti = count($tabla);

                                                                                                                    $id_nueva_fila = 0; //índice de los objetos a pintar
                                                                                                                    foreach ($tabla as $registro) {
                                                                                                                        $id_nueva_fila++;
                                                                                                                        $correccion_optica->getFormularioAyudaBajaVision($id_nueva_fila, $registro);
                                                                                                                    }
                                                                                                                    ?>
                                                                                                                </div>
                                                                                                                <div align="right">
                                                                                                                    <img src="../imagenes/icon-blue-down.png" id="img_ocultar_abv" class="img_button" onclick="efecto_capas('abv');" title="Mostrar Sección Ayuda Baja Visión">
                                                                                                                        <img src="../imagenes/icon-blue-up.png" id="img_ver_abv" class="img_button" onclick="efecto_capas('abv');" title="Ocultar Sección Ayuda Baja Visión">
                                                                                                                            </div>
                                                                                                                            </fieldset>
                                                                                                                            <input type="hidden" id="hdd_canti_abv" value="<?php echo $canti ?>">
                                                                                                                                <fieldset class="grupo_formularios" id="seccion_cxr">
 <div class="restar_alemetos_dere" onclick="quitar_frm('cxr');" title="Quitar Última Cirugía Refractiva"></div>
 <div id="btn_add_cxr" class="agregar_alemetos_dere" onclick="agregar_frm('cxr');" title="Agregar Otra Cirugía Refractiva"></div>															
 <legend>Cx Refractiva  en uso</legend>
 <div id="div_cxr">
     <?php
     // Dibujar los formularios de cirugías refractivas registradas
     $tabla = $dbConsultaOptometria->getHcCirugiasRefractivas($id_hc_consulta);
     $canti = count($tabla);

     $id_nueva_fila = 0; //índice de los objetos a pintar
     foreach ($tabla as $registro) {
         $id_nueva_fila++;
         $correccion_optica->getFormularioCirugiaRefractiva($id_nueva_fila, $registro);
     }
     ?>
 </div>
 <div align="right">
     <img src="../imagenes/icon-blue-down.png" id="img_ocultar_cxr" class="img_button" onclick="efecto_capas('cxr');" title="Mostrar Cirugía Refractiva">
         <img src="../imagenes/icon-blue-up.png" id="img_ver_cxr" class="img_button" onclick="efecto_capas('cxr');" title="Ocultar Cirugía Refractiva">
             </div>
             </fieldset>
             <input type="hidden" id="hdd_canti_cxr" value="<?php echo $canti ?>">
                 </div>
                 <div class="div_separador"></div>
                 <table border="0" cellpadding="0" cellspacing="4" align="center" style="width:98%;">
                     <tr>
                         <td align="left" style="width:15%;"><h6 style="margin:1px;"><b>Dominancia Ocular:</b></h6></td>
                         <td align="left" style="width:52%;" colspan="3">
                             <?php
                             $combo->getComboDb("cmb_dominancia_ocular", $cmb_dominancia_ocular, $lista_ojos, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_" . $arr_colores[1]);
                             $colorPick->getColorPick("cmb_dominancia_ocular", 1);
                             ?>
                         </td>
                     </tr>						
                 </table>
                 <br />
                 <table border="0" cellpadding="0" cellspacing="0" align="center" style="width:98%;">
                     <tr>
                         <td align="center" class="td_tabla">
                             <div class="odoi_t">
                                 <div class="od_t"><h5 style="margin: 0px;">OD</h5></div>
                                 <div class="oi_t"><h5 style="margin: 0px;">OI</h5></div>
                             </div>
                         </td>
                     </tr>
                 </table>
                 <br />
                 <!--INICIO AVSC-->
                 <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
                     <tr>
                         <td align="left" style="width:42%;">
                             <table border="0" cellpadding="0" cellspacing="0" align="right" style="width:80%;">
                                 <tr>
                                     <td align="center" style="width:33%;">
                                         LEJOS *<br />
                                         <?php
                                         $combo->getComboDb("avsc_lejos_od", $avsc_lejos_od, $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_" . $arr_colores[3]);
                                         $colorPick->getColorPick("avsc_lejos_od", 3);
                                         ?>
                                     </td>
                                     <td align="center" style="width:34%;">
                                         INTERMEDIA<br />
                                         <?php
                                         $combo->getComboDb("avsc_media_od", $avsc_media_od, $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_" . $arr_colores[81]);
                                         $colorPick->getColorPick("avsc_media_od", 81);
                                         ?>
                                     </td>									
                                     <td align="center" style="width:33%;">
                                         CERCA *<br />
                                         <?php
                                         $combo->getComboDb("avsc_cerca_od", $avsc_cerca_od, $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_" . $arr_colores[5]);
                                         $colorPick->getColorPick("avsc_cerca_od", 5);
                                         ?>
                                     </td>
                                 </tr>
                             </table>
                         </td>	
                         <td align="center" style="width:16%;"><h5 style="margin: 0px">AVSC</h5></td>	
                         <td align="left" style="width:42%;">
                             <table border="0" cellpadding="0" cellspacing="0" align="left" style="width:80%;">
                                 <tr>	
                                     <td align="center" style="width:33%;">
                                         LEJOS *<br />
                                         <?php
                                         $combo->getComboDb("avsc_lejos_oi", $avsc_lejos_oi, $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_" . $arr_colores[6]);
                                         $colorPick->getColorPick("avsc_lejos_oi", 6);
                                         ?>
                                     </td>
                                     <td align="center" style="width:34%;">
                                         INTERMEDIA<br />
                                         <?php
                                         $combo->getComboDb("avsc_media_oi", $avsc_media_oi, $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_" . $arr_colores[82]);
                                         $colorPick->getColorPick("avsc_media_oi", 82);
                                         ?>
                                     </td>									
                                     <td align="center" style="width:33%;">
                                         CERCA *<br />
                                         <?php
                                         $combo->getComboDb("avsc_cerca_oi", $avsc_cerca_oi, $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_" . $arr_colores[8]);
                                         $colorPick->getColorPick("avsc_cerca_oi", 8);
                                         ?>
                                     </td>
                                 </tr>
                             </table>
                         </td> 	
                     </tr>
                 </table>
                 <!--FIN AVSC-->
                 <div class="div_separador"></div>
                 <!--INICIO LENSO-->
                 <div id="seccion_lensometria">
                     <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;" class="opt_panel_1">				
                         <tr>
                             <td></td>
                             <td align="center" style="width:16%;"><h5 style="margin: 0px">Lensometr&iacute;a</h5></td>
                             <td></td>
                         </tr>
                     </table>
                     <?php
                     //Dibujar los formularios de lensometrías registradas
                     $id_nueva_fila = 0; //índice de los objetos a pintar
                     foreach ($tabla_gafas as $registro_gafas) {
                         $id_nueva_fila++;
                         $correccion_optica->getFormularioLensometria($id_nueva_fila, $registro_gafas, $colorPick);
                     }
                     ?>
                 </div>
                 <!--FIN LENSO-->
                 <div class="div_separador"></div>
                 <!--INICIO QUERATO-->
                 <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;" >
                     <tr>
                         <td align="left" style="width:42%;">
                             <table border="0" cellpadding="1" cellspacing="0" align="right" style="width:75%;">
                                 <tr>
                                     <td align="center" valign="top" style="width:33%;">
                                         CILINDRO *<br />
                                         <input type="text" name="querato_dif_od" id="querato_dif_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[23]); ?>" value="<?php echo $querato_dif_od; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);" />
                                         <?php
                                         $colorPick->getColorPick("querato_dif_od", 23);
                                         ?>
                                     </td>
                                     <td align="center" valign="top" style="width:34%;">
                                         EJE *<br />
                                         <input type="text" name="querato_ejek1_od" id="querato_ejek1_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[24]); ?>" value="<?php echo $querato_ejek1_od; ?>" maxlength="3" onBlur="validar_array(array_eje, this);" />
                                         <?php
                                         $colorPick->getColorPick("querato_ejek1_od", 24);
                                         ?>
                                     </td>
                                     <td align="center" valign="top" style="width:33%;">
                                         K+PLANO *<br />
                                         <input type="text" name="querato_k1_od" id="querato_k1_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[25]); ?>" value="<?php echo $querato_k1_od; ?>" maxlength="10" onBlur="validar_array(array_querato, this);" />
                                         <?php
                                         $colorPick->getColorPick("querato_k1_od", 25);
                                         ?>
                                     </td>
                                 </tr>
                             </table>	
                         </td>
                         <td align="center" style="width:16%;"><h5>Queratometr&iacute;a</h5></td>
                         <td align="left" style="width:42%;">
                             <table border="0" cellpadding="1" cellspacing="0" align="left" style="width:75%;">
                                 <tr>
                                     <td align="center" valign="top" style="width:33%;">
                                         CILINDRO *<br />
                                         <input type="text" name="querato_dif_oi" id="querato_dif_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[26]); ?>" value="<?php echo $querato_dif_oi; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);" />
                                         <?php
                                         $colorPick->getColorPick("querato_dif_oi", 26);
                                         ?>
                                     </td>
                                     <td align="center" valign="top" style="width:34%;">
                                         EJE *<br />
                                         <input type="text" name="querato_ejek1_oi" id="querato_ejek1_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[27]); ?>" value="<?php echo $querato_ejek1_oi; ?>" maxlength="3" onBlur="validar_array(array_eje, this);" />
                                         <?php
                                         $colorPick->getColorPick("querato_ejek1_oi", 27);
                                         ?>
                                     </td>
                                     <td align="center" valign="top" style="width:33%;">
                                         K+PLANO *<br />
                                         <input type="text" name="querato_k1_oi" id="querato_k1_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[28]); ?>" value="<?php echo $querato_k1_oi; ?>" maxlength="10" onBlur="validar_array(array_querato, this);" />
                                         <?php
                                         $colorPick->getColorPick("querato_k1_oi", 28);
                                         ?>
                                     </td>
                                 </tr>
                             </table>	
                         </td>
                     </tr>
                 </table>
                 <!--FIN QUERATO-->
                 <div class="div_separador"></div>
                 <!--INICIO OBJETIVO-->
                 <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;" class="opt_panel_1">
                     <tr>
                         <td align="left" style="width:42%;">
                             <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:100%;">
                                 <tr>
                                     <td align="center" style="width:25%;">
                                         ESFERA<br />
                                         <input type="text" name="refraobj_esfera_od" id="refraobj_esfera_od" class="input input_hc signed componente_color_pick_<?php echo($arr_colores[29]); ?>" value="<?php echo $refraobj_esfera_od; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);" />
                                         <?php
                                         $colorPick->getColorPick("refraobj_esfera_od", 29);
                                         ?>
                                     </td>
                                     <td align="center" style="width:25%;">
                                         CILINDRO<br />
                                         <input type="text" name="refraobj_cilindro_od" id="refraobj_cilindro_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[30]); ?>" value="<?php echo $refraobj_cilindro_od; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);" />
                                         <?php
                                         $colorPick->getColorPick("refraobj_cilindro_od", 30);
                                         ?>
                                     </td>
                                     <td align="center" style="width:25%;">
                                         EJE<br />
                                         <input type="text" name="refraobj_eje_od" id="refraobj_eje_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[31]); ?>" value="<?php echo $refraobj_eje_od; ?>" maxlength="3" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);" />
                                         <?php
                                         $colorPick->getColorPick("refraobj_eje_od", 31);
                                         ?>
                                     </td>
                                     <td align="center" style="width:25%;">
                                         LEJOS<br />
                                         <?php
                                         $combo->getComboDb("refraobj_lejos_od", $refraobj_lejos_od, $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_" . $arr_colores[32]);
                                         $colorPick->getColorPick("refraobj_lejos_od", 32);
                                         ?>
                                     </td>
                                 </tr>
                             </table>
                         </td>
                         <td align="center" style="width:16%;"><h5 style="margin: 0px">Objetivo</h5></td>
                         <td align="left" style="width:42%;">
                             <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:100%;">
                                 <tr>
                                     <td align="center" style="width:25%;">
                                         ESFERA<br />
                                         <input type="text" name="refraobj_esfera_oi" id="refraobj_esfera_oi" class="input input_hc signed componente_color_pick_<?php echo($arr_colores[36]); ?>" value="<?php echo $refraobj_esfera_oi; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);" />
                                         <?php
                                         $colorPick->getColorPick("refraobj_esfera_oi", 36);
                                         ?>
                                     </td>
                                     <td align="center" style="width:25%;">
                                         CILINDRO<br />
                                         <input type="text" name="refraobj_cilindro_oi" id="refraobj_cilindro_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[37]); ?>" value="<?php echo $refraobj_cilindro_oi; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);" />
                                         <?php
                                         $colorPick->getColorPick("refraobj_cilindro_oi", 37);
                                         ?>
                                     </td>
                                     <td align="center" style="width:25%;">
                                         EJE<br />
                                         <input type="text" name="refraobj_eje_oi" id="refraobj_eje_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[38]); ?>" value="<?php echo $refraobj_eje_oi; ?>" maxlength="3" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);" />
                                         <?php
                                         $colorPick->getColorPick("refraobj_eje_oi", 38);
                                         ?>
                                     </td>
                                     <td align="center" style="width:25%;">
                                         LEJOS<br />
                                         <?php
                                         $combo->getComboDb("refraobj_lejos_oi", $refraobj_lejos_oi, $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_" . $arr_colores[39]);
                                         $colorPick->getColorPick("refraobj_lejos_oi", 39);
                                         ?>
                                     </td>
                                 </tr>
                             </table>
                         </td>
                     </tr>
                 </table>
                 <!--FIN OBJETIVO-->
                 <div class="div_separador"></div>
                 <!--INICIO SUBJETIVO-->
                 <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
                     <tr>
                         <td align="left" style="width:42%;">
                             <div style="float: left;">
                                 <img src="../imagenes/copy_opt.png" onclick="copiar_objetivo("OD")">
                         </div>
                         <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:100%;">
                             <tr>
                                 <td align="center" style="width:25%;">
                                     ESFERA *<br />
                                     <input type="text" name="subjetivo_esfera_od" id="subjetivo_esfera_od" class="input input_hc signed componente_color_pick_<?php echo($arr_colores[43]); ?>" value="<?php echo $subjetivo_esfera_od; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);" />
                                     <?php
                                     $colorPick->getColorPick("subjetivo_esfera_od", 43);
                                     ?>
                                 </td>
                                 <td align="center" style="width:25%;">
                                     CILINDRO<br />
                                     <input type="text" name="subjetivo_cilindro_od" id="subjetivo_cilindro_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[44]); ?>" value="<?php echo $subjetivo_cilindro_od; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);" />
                                     <?php
                                     $colorPick->getColorPick("subjetivo_cilindro_od", 44);
                                     ?>
                                 </td>
                                 <td align="center" style="width:25%;">
                                     EJE<br />
                                     <input type="text" name="subjetivo_eje_od" id="subjetivo_eje_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[45]); ?>" value="<?php echo $subjetivo_eje_od; ?>" maxlength="3" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);" />
                                     <?php
                                     $colorPick->getColorPick("subjetivo_eje_od", 45);
                                     ?>
                                 </td>
                                 <td align="center" style="width:25%;">
                                     ADICI&Oacute;N<br />
                                     <input type="text" name="subjetivo_adicion_od" id="subjetivo_adicion_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[48]); ?>"  value="<?php echo $subjetivo_adicion_od; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_adicion, this);" />
                                     <?php
                                     $colorPick->getColorPick("subjetivo_adicion_od", 48);
                                     ?>
                                 </td>
                             </tr>
                             <tr>
                                 <!--<td></td>	-->
                                 <td align="center">
                                     LEJOS *<br />
                                     <?php
                                     $combo->getComboDb("subjetivo_lejos_od", $subjetivo_lejos_od, $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_" . $arr_colores[46]);
                                     $colorPick->getColorPick("subjetivo_lejos_od", 46);
                                     ?>
                                 </td>
                                 <td align="center">
                                     INTERMEDIA<br />
                                     <?php
                                     $combo->getComboDb("subjetivo_media_od", $subjetivo_media_od, $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_" . $arr_colores[83]);
                                     $colorPick->getColorPick("subjetivo_media_od", 83);
                                     ?>
                                 </td>									
                                 <td align="center">
                                     PIN HOLE<br />
                                     <?php
                                     $combo->getComboDb("subjetivo_ph_od", $subjetivo_ph_od, $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_" . $arr_colores[47]);
                                     $colorPick->getColorPick("subjetivo_ph_od", 47);
                                     ?>
                                 </td>
                                 <td align="center">
                                     CERCA *<br />
                                     <?php
                                     $combo->getComboDb("subjetivo_cerca_od", $subjetivo_cerca_od, $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_" . $arr_colores[49]);
                                     $colorPick->getColorPick("subjetivo_cerca_od", 49);
                                     ?>
                                 </td>
                             </tr>
                         </table>
                     </td>
                     <td align="center" style="width:16%;"><h5 style="margin: 0px">Subjetivo</h5></td>
                     <td align="left" style="width:42%;">
                         <div style="float: right;">
                             <img src="../imagenes/copy_opt.png" onclick='copiar_objetivo("OI")'>
                         </div>
                         <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:100%;">
                             <tr>
                                 <td align="center" style="width:25%;">
                                     ESFERA *<br />
                                     <input type="text" name="subjetivo_esfera_oi" id="subjetivo_esfera_oi" class="input input_hc signed componente_color_pick_<?php echo($arr_colores[50]); ?>" value="<?php echo $subjetivo_esfera_oi; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);" />
                                     <?php
                                     $colorPick->getColorPick("subjetivo_esfera_oi", 50);
                                     ?>
                                 </td>
                                 <td align="center" style="width:25%;">
                                     CILINDRO<br />
                                     <input type="text" name="subjetivo_cilindro_oi" id="subjetivo_cilindro_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[51]); ?>" value="<?php echo $subjetivo_cilindro_oi; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);" />
                                     <?php
                                     $colorPick->getColorPick("subjetivo_cilindro_oi", 51);
                                     ?>
                                 </td>
                                 <td align="center" style="width:25%;">
                                     EJE<br />
                                     <input type="text" name="subjetivo_eje_oi" id="subjetivo_eje_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[52]); ?>" value="<?php echo $subjetivo_eje_oi; ?>" maxlength="3" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);" />
                                     <?php
                                     $colorPick->getColorPick("subjetivo_eje_oi", 52);
                                     ?>
                                 </td>
                                 <td align="center" style="width:25%;">
                                     ADICI&Oacute;N<br />
                                     <input type="text" name="subjetivo_adicion_oi" id="subjetivo_adicion_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[55]); ?>" value="<?php echo $subjetivo_adicion_oi; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_adicion, this);" />
                                     <?php
                                     $colorPick->getColorPick("subjetivo_adicion_oi", 55);
                                     ?>
                                 </td>
                             </tr>
                             <tr>
                                 <td align="center">
                                     LEJOS *<br />
                                     <?php
                                     $combo->getComboDb("subjetivo_lejos_oi", $subjetivo_lejos_oi, $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_" . $arr_colores[53]);
                                     $colorPick->getColorPick("subjetivo_lejos_oi", 53);
                                     ?>
                                 </td>
                                 <td align="center">
                                     INTERMEDIA<br />
                                     <?php
                                     $combo->getComboDb("subjetivo_media_oi", $subjetivo_media_oi, $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_" . $arr_colores[84]);
                                     $colorPick->getColorPick("subjetivo_media_oi", 84);
                                     ?>
                                 </td>									
                                 <td align="center">
                                     PIN HOLE<br />
                                     <?php
                                     $combo->getComboDb("subjetivo_ph_oi", $subjetivo_ph_oi, $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_" . $arr_colores[54]);
                                     $colorPick->getColorPick("subjetivo_ph_oi", 54);
                                     ?>
                                 </td>
                                 <td align="center">
                                     CERCA *<br />
                                     <?php
                                     $combo->getComboDb("subjetivo_cerca_oi", $subjetivo_cerca_oi, $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_" . $arr_colores[56]);
                                     $colorPick->getColorPick("subjetivo_cerca_oi", 56);
                                     ?>
                                 </td>
                             </tr>
                         </table>
                     </td>
                 </tr>
             </table>
             <!--FIN SUBJETIVO-->
             <div class="div_separador"></div>
             <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
                 <tr>
                     <td align="center" class="">
                         <h5 style="margin: 0px">
                             Observaciones internas
                         </h5>
                         <div id="txt_observaciones_subjetivo"><?php echo($utilidades->ajustar_texto_wysiwyg($txt_observaciones_subjetivo)); ?></div>
                     </td>
                 </tr>
             </table>
             <div class="div_separador"></div>
             <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
                 <tr>
                     <td align="center" class="">
                         <h5 style="margin: 0px">
                             Observaciones optometr&iacute;a
                         </h5>
                         <div id="txt_observaciones_optometria"><?php echo($utilidades->ajustar_texto_wysiwyg($txt_observaciones_optometria)); ?></div>
                     </td>
                 </tr>
             </table>
             <div class="div_separador"></div>
             <!--INICIO CICLOPLEJIA-->
             <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;" class="opt_panel_1">
                 <tr>
                     <td align="left" style="width:42%;">
                         <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:100%;">
                             <tr>
                                 <td align="center" style="width:25%;">
                                     ESFERA<br />
                                     <input type="text" name="cicloplejio_esfera_od" id="cicloplejio_esfera_od" class="input input_hc signed componente_color_pick_<?php echo($arr_colores[57]); ?>" value="<?php echo $cicloplejio_esfera_od; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);" />
                                     <?php
                                     $colorPick->getColorPick("cicloplejio_esfera_od", 57);
                                     ?>
                                 </td>
                                 <td align="center" style="width:25%;">
                                     CILINDRO<br />
                                     <input type="text" name="cicloplejio_cilindro_od" id="cicloplejio_cilindro_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[58]); ?>" value="<?php echo $cicloplejio_cilindro_od; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);" />
                                     <?php
                                     $colorPick->getColorPick("cicloplejio_cilindro_od", 58);
                                     ?>
                                 </td>
                                 <td align="center" style="width:25%;">
                                     EJE<br />
                                     <input type="text" name="cicloplejio_eje_od" id="cicloplejio_eje_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[59]); ?>"  value="<?php echo $cicloplejio_eje_od; ?>" maxlength="3" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);" />
                                     <?php
                                     $colorPick->getColorPick("cicloplejio_eje_od", 59);
                                     ?>
                                 </td>
                                 <td align="center" style="width:25%;">
                                     LEJOS<br />
                                     <?php
                                     $combo->getComboDb("cicloplejio_lejos_od", $cicloplejio_lejos_od, $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_" . $arr_colores[60]);
                                     $colorPick->getColorPick("cicloplejio_lejos_od", 60);
                                     ?>
                                 </td>
                             </tr>
                         </table>
                     </td>
                     <td align="center" style="width:16%;"><h5 style="margin: 0px">Cicloplejia</h5></td>
                     <td align="left" style="width:42%;">
                         <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:100%;">
                             <tr>
                                 <td align="center" style="width:25%;">
                                     ESFERA<br />
                                     <input type="text" name="cicloplejio_esfera_oi" id="cicloplejio_esfera_oi" class="input input_hc signed componente_color_pick_<?php echo($arr_colores[63]); ?>" value="<?php echo $cicloplejio_esfera_oi; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);" />
                                     <?php
                                     $colorPick->getColorPick("cicloplejio_esfera_oi", 63);
                                     ?>
                                 </td>
                                 <td align="center" style="width:25%;">
                                     CILINDRO<br />
                                     <input type="text" name="cicloplejio_cilindro_oi" id="cicloplejio_cilindro_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[64]); ?>" value="<?php echo $cicloplejio_cilindro_oi; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);" />
                                     <?php
                                     $colorPick->getColorPick("cicloplejio_cilindro_oi", 64);
                                     ?>
                                 </td>
                                 <td align="center" style="width:25%;">
                                     EJE<br />
                                     <input type="text" name="cicloplejio_eje_oi" id="cicloplejio_eje_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[65]); ?>" value="<?php echo $cicloplejio_eje_oi; ?>" maxlength="3" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);" />
                                     <?php
                                     $colorPick->getColorPick("cicloplejio_eje_oi", 65);
                                     ?>
                                 </td>
                                 <td align="center" style="width:25%;">
                                     LEJOS<br />
                                     <?php
                                     $combo->getComboDb("cicloplejio_lejos_oi", $cicloplejio_lejos_oi, $valores_av, "id_detalle, nombre_detalle", " ", "", "", "", "", "select_hc componente_color_pick_" . $arr_colores[66]);
                                     $colorPick->getColorPick("cicloplejio_lejos_oi", 66);
                                     ?>
                                 </td>
                             </tr>
                         </table>
                     </td>
                 </tr>
             </table>
             <!--FIN CICLOPLEJIA-->
             <div class="div_separador"></div>
             <!--INICIO RX FINAL - PIO-->
             <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
                 <tr>
                     <td align="left" style="width:42%;">
                         <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:100%;">
                             <tr>
                                 <td align="center">
                                     <div style="float: left;">
                                         <img src="../imagenes/copy_opt.png" onclick='copiar_subjetivo("OD")'>
                                     </div>
                                 </td>
                                 <td align="center">
                                     ESFERA *<br />
                                     <input type="text" name="refrafinal_esfera_od" id="refrafinal_esfera_od" class="input input_hc signed componente_color_pick_<?php echo($arr_colores[69]); ?>" value="<?php echo $refrafinal_esfera_od; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);" />
                                     <?php
                                     $colorPick->getColorPick("refrafinal_esfera_od", 69);
                                     ?>
                                 </td>
                                 <td align="center">
                                     CILINDRO<br />
                                     <input type="text" name="refrafinal_cilindro_od" id="refrafinal_cilindro_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[70]); ?>" value="<?php echo $refrafinal_cilindro_od; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);" />
                                     <?php
                                     $colorPick->getColorPick("refrafinal_cilindro_od", 70);
                                     ?>
                                 </td>
                                 <td align="center">
                                     EJE<br />
                                     <input type="text" name="refrafinal_eje_od" id="refrafinal_eje_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[71]); ?>" value="<?php echo $refrafinal_eje_od; ?>" maxlength="3" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);" />
                                     <?php
                                     $colorPick->getColorPick("refrafinal_eje_od", 71);
                                     ?>
                                 </td>
                                 <td align="center">
                                     ADICI&Oacute;N<br />
                                     <input type="text" name="refrafinal_adicion_od" id="refrafinal_adicion_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[72]); ?>" value="<?php echo $refrafinal_adicion_od; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_adicion, this);" />
                                     <?php
                                     $colorPick->getColorPick("refrafinal_adicion_od", 72);
                                     ?>
                                 </td>
                             </tr>
                         </table>
                     </td>
                     <td align="center" style="width:16%;"><h5 style="margin: 0px">RX Final</h5></td>
                     <td align="left" style="width:42%;">
                         <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:100%;">
                             <tr>
                                 <td align="center">
                                     ESFERA *<br />
                                     <input type="text" name="refrafinal_esfera_oi" id="refrafinal_esfera_oi" class="input input_hc signed componente_color_pick_<?php echo($arr_colores[73]); ?>"  value="<?php echo $refrafinal_esfera_oi; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_esfera, this);" />
                                     <?php
                                     $colorPick->getColorPick("refrafinal_esfera_oi", 73);
                                     ?>
                                 </td>
                                 <td align="center">
                                     CILINDRO<br />
                                     <input type="text" name="refrafinal_cilindro_oi" id="refrafinal_cilindro_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[74]); ?>"  value="<?php echo $refrafinal_cilindro_oi; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_cilindro, this);" />
                                     <?php
                                     $colorPick->getColorPick("refrafinal_cilindro_oi", 74);
                                     ?>
                                 </td>
                                 <td align="center">
                                     EJE<br />
                                     <input type="text" name="refrafinal_eje_oi" id="refrafinal_eje_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[75]); ?>" value="<?php echo $refrafinal_eje_oi; ?>" maxlength="3" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_eje, this);" />
                                     <?php
                                     $colorPick->getColorPick("refrafinal_eje_oi", 75);
                                     ?>
                                 </td>
                                 <td align="center">
                                     ADICI&Oacute;N<br />
                                     <input type="text" name="refrafinal_adicion_oi" id="refrafinal_adicion_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[76]); ?>" value="<?php echo $refrafinal_adicion_oi; ?>" maxlength="10" onkeypress="formato_hc(event, this);" onBlur="validar_array(array_adicion, this);" />
                                     <?php
                                     $colorPick->getColorPick("refrafinal_adicion_oi", 76);
                                     ?>
                                 </td>
                                 <td align="center">
                                     <div style="float: right;">
                                         <img src="../imagenes/copy_opt.png" onclick='copiar_subjetivo("OI")'>
                                     </div>
                                 </td>
                             </tr>
                         </table>
                     </td>
                 </tr>
                 <tr>
                     <td align="center" colspan="3">
                         <h6 style="margin:0px; width:360px;">
                             Observaciones f&oacute;rmula de gafas Refracci&oacute;n final
                         </h6>
                         <div id="txt_observaciones_rxfinal"><?php echo($utilidades->ajustar_texto_wysiwyg($txt_observaciones_rxfinal)); ?></div>
                     </td>
                 </tr>
                 <tr>
                     <td align="center" colspan="3">
                        	<table border="0" cellpadding="0" cellspacing="5" style="width:100%;">
                            	
                                	<td align="left" style="width:15%;">
                                    	<h6 style="margin:0px;">Especificaciones del lente*:&nbsp;</h6>
                                    </td>
                                	<td align="left" style="width:85%;">
                                    	<input type="text" name="txt_tipo_lente" id="txt_tipo_lente" class="componente_color_pick_<?php echo($arr_colores[80]); ?>" style="width:90%; margin:0;" value="<?php echo($tipo_lente); ?>" maxlength="200" />
										<?php
											$colorPick->getColorPick("txt_tipo_lente", 80);
										?>
                                    </td>
                              </table>
                              <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                                <tr>	
                                    <td align="left" >
                                    	<h6 style="margin:0px;">Tipos de lentes*:&nbsp;</h6>
                                    </td>
                                	<td align="left" >
                                    	<?php $lista_tipo_lente = $dbListas->getListaDetalles(102);
	                                	$combo->getComboDb("cmb_tipo_lente",$id_tipo_lente, $lista_tipo_lente, "id_detalle, nombre_detalle", "Seleccione el tipo de lente", "", true, "width: 210px;");
										?>
                                    </td>
                                    <td align="left">
                                    	<h6 style="margin:0px;">Vigencia*:&nbsp;</h6>
                                    </td>
                                	<td align="left" >
                                     <?php $lista_tiempo_vigencia = $dbListas->getListaDetalles(103);
	                                	$combo->getComboDb("cmb_tiempo_vigencia",$id_tiempo_vigencia, $lista_tiempo_vigencia, "id_detalle, nombre_detalle", "Seleccione vigencia formula", "", true, "width: 210px;");?>
									</td>
                                    <td align="left" >
                                    	<h6 style="margin:0px;">Filtro*:&nbsp;</h6>
                                    </td>
                                	<td align="left" >
                                    <?php $lista_tipo_filtro = $dbListas->getListaDetalles(60);
	                                	$combo->getComboDb("cmb_tipo_filtro",$id_tipo_filtro, $lista_tipo_filtro, "id_detalle, nombre_detalle", "Seleccione el tipo del filtro", "", true, "width: 210px;");?>
                                    	
									</td>
                                </tr>
                                <tr>
                                	<td align="left" >
                                    	<h6 style="margin:0px;">Tiempo formulación*:&nbsp;</h6>
                                    </td>
                                	<td align="left" >
                                    
                                    <?php $lista_tiempo_periodo = $dbListas->getListaDetalles(103);
									$combo->getComboDb("cmb_timepo_periodo",$id_tiempo_periodo, $lista_tiempo_periodo,"id_detalle, nombre_detalle", "Seleccione el tiempo de formulación", "", true, "width: 210px;")?>
                                    </td>
                                    <td align="left">
                                    	<h6 style="margin:0px;">Distancia pupilar*:&nbsp;</h6>
                                    </td>
                                	<td align="left" >
                                    	<input type="text" name="txt_distancia_pupilar" id="txt_distancia_pupilar" class="componente_color_pick_<?php echo($arr_colores[80]); ?>" style="width:90%; margin:0;" value="<?php echo($distancia_pupilar); ?>" maxlength="200" />
										
                                    </td>
                                    <td align="left" >
                                    	<h6 style="margin:0px;">Cantidad*:&nbsp;</h6>
                                    </td>
                                	<td align="left" >
                                    	<input type="text" name="txt_cantidad" id="txt_cantidad" class="componente_color_pick_<?php echo($arr_colores[80]); ?>" style="width:90%; margin:0;" value="<?php echo($form_cantidad); ?>" maxlength="200" />
										
                                    </td>
                                </tr>
                            </table>
                        </td>
                 </tr>
                 <tr>
                     <td align="center" colspan="3">
                         <input type="button" id="btn_imprimir_rxfinal" nombre="btn_imprimir_rxfinal" class="btnPrincipal peq" style="font-size: 16px;" value="Imprimir f&oacute;rmula" onclick="generar_formula_gafas('txt_observaciones_rxfinal', '<?php echo($fecha_hc_t); ?>', '<?php echo($nombre_paciente); ?>', '<?php echo($nombre_usuario_profesional); ?>', 'refrafinal_esfera_od', 'refrafinal_cilindro_od', 'refrafinal_eje_od', 'refrafinal_adicion_od', 'refrafinal_esfera_oi', 'refrafinal_cilindro_oi', 'refrafinal_eje_oi', 'refrafinal_adicion_oi', '<?php echo($id_admision); ?>');" />
                     </td>
                 </tr>
             </table>
             <div class="div_separador"></div>
                 <!--<table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;" class="opt_panel_1">
                     <tr>
                         <td align="center" style="width:35%;">
                             <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:100%;">
                                 <tr>
                                     <td align="right" style="width:80%;">	
                                         <input type="text" name="presion_intraocular_od" id="presion_intraocular_od" class="input input_hc componente_color_pick_<?php echo($arr_colores[77]); ?>" value="<?php echo $presion_intraocular_od; ?>" maxlength="5" onkeypress="formato_hc(event, this);" />
             <?php
             //$colorPick->getColorPick("presion_intraocular_od", 77);
             ?>
                                     </td>
                                     <td align="left" style="width:23%;"> 
                                         <label>mmHg</label>
                                     </td>
                                 </tr>
                             </table>
                         </td>
                         <td align="center" style="width:30%;"><h5 style="margin: 0px">Presi&oacute;n Intraocular Neum&aacute;tica</h5></td>
                         <td align="center" style="width:35%;">
                             <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:100%;">
                                 <tr>
                                     <td align="right" style="width:23%;">
                                         <input type="text" name="presion_intraocular_oi" id="presion_intraocular_oi" class="input input_hc componente_color_pick_<?php echo($arr_colores[78]); ?>" value="<?php echo $presion_intraocular_oi; ?>" maxlength="5" onkeypress="formato_hc(event, this);"/>
             <?php
             //$colorPick->getColorPick("presion_intraocular_oi", 78);
             ?>
                                     </td>
                                     <td align="left" style="width:77%;"> 
                                         <label>mmHg</label>
                                     </td>
                                 </tr>
                             </table>	
                         </td>
                     </tr>
                     <tr>
                         <td align="center" colspan="3" class="">
                             <h6 style="margin: 1px;">
                                 <b>Paciente Dilatado:</b> 
                                 <select id="cmb_paciente_dilatado" class="select_hc componente_color_pick_<?php echo($arr_colores[79]); ?>" style="width: 60px; margin: 1px;">
                                     <option value="" ></option>
             <?php
             /* for ($i = 1; $i <= 2; $i++) {
               $selected = "";
               f($i==1){$text="Si";}else if($i==2){$text="No";}
               if ($i == $cmb_paciente_dilatado) {
               $selected = " selected=\"selected\"";
               }
               ?>
               <option value="<?php echo $i; ?>"<?php echo($selected); ?>><?php echo $text; ?></option>
               <?php
               } */
             ?>
                                 </select>
             <?php
             //$colorPick->getColorPick("cmb_paciente_dilatado", 79);
             ?>
                             </h6>
                         </td>
                     </tr>
                 </table>-->
             <!--FIN RX FINAL - PIO-->
             <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%; display:none;">
                 <tr>
                     <td align="center"><h5 style="margin: 0px">Observaciones de la Admisi&oacute;n</h5></td>
                 </tr>
                 <tr>
                     <td align="center" class="">
                         <textarea id="txt_observaciones_admision" style="width:100%; min-height:150px;" onblur="trim_cadena(this);"><?php echo($observaciones_admision); ?></textarea>
                     </td>
                 </tr>
             </table>
             <!--INICIO DIAGNOSTICO-->
             <table border="0" cellpadding="1" cellspacing="0" align="center" style="width:98%;">
                 <tr>
                     <td align="left" class="" colspan="3">
                         <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:100%;">	
                             <tr>
                                 <td align="center" style="width:30%;"><h6>Diagn&oacute;sticos</h6>
                                     <?php
                                     $class_diagnosticos->getFormularioDiagnosticos($id_hc_consulta);
                                     ?>
                                     <label><b>Otros diagn&oacute;sticos y an&aacute;lisis</b></label>
                                     <div id="txt_diagnostico_optometria"><?php echo($utilidades->ajustar_texto_wysiwyg($diagnostico_optometria)); ?></div>
                                 </td>
                             </tr>
                         </table>
                     </td>
                 </tr>
             </table>
             <!--FIN DIAGNOSTICO-->
             </div>
             <!--INICIO ÓRDENES Y REMISIONES-->
             <div class="content" id="panel2-3">
                 <?php
                 $class_ordenes_remisiones->getFormularioRemisiones($id_hc_consulta, 1, $ind_editar);
                 $class_ordenes_remisiones->getFormularioOrdenarMedicamentos($id_hc_consulta, NULL, 1, $ind_editar);
                 $class_ordenes_remisiones->getFormularioOrdenesMedicas($id_hc_consulta, NULL, 1, $ind_editar);
                 ?>
             </div>
             <!--FIN REMISIONES-->
             </div>
             </div>
             <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:90%;">
                 <tr valign="top">
                     <td colspan="3">
                         <?php
                         if (!isset($_POST["tipo_entrada"])) {
                             ?>
                             <input class="btnPrincipal" type="button" id="btn_imprimir" nombre="btn_imprimir" value="Imprimir" onclick="validar_crear_optometria(2, 1);" />
                             <?php
                         } else {
                             ?>
                             <input class="btnPrincipal" type="button" id="btn_imprimir" nombre="btn_imprimir" value="Imprimir Optometr&iacute;a" onclick="imprimir_optometria();" />
                             <?php
                         }

                         if ($ind_editar == 1) {
                             if (!isset($_POST["tipo_entrada"])) {
                                 ?>
                                 <input type="button" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Guardar cambios" onclick="validar_crear_optometria(2, 0);" />
                                 <?php
                                 $id_tipo_cita = $admision_obj["id_tipo_cita"];

                                 $lista_tipos_citas_det_remisiones = $dbTiposCitasDetalle->get_lista_tipos_citas_det_remisiones($id_tipo_cita, $id_tipo_reg);
                                 if (count($lista_tipos_citas_det_remisiones) > 0) {
                                     ?>
                                     <input class="btnPrincipal" type="button" id="btn_enviar_a" nombre="btn_enviar_a" value="Enviar a ..." onclick="enviar_a_estados();" />
                                     <?php
                                 }
                                 ?>
                                 <input type="button" id="btn_finalizar" nombre="btn_finalizar" class="btnPrincipal" value="Finalizar consulta" onclick="validar_crear_optometria(1, 0);" />
                                 <?php
                             } else {
                                 ?>
                                 <input type="button" id="btn_crear" nombre="btn_crear" class="btnPrincipal" value="Guardar Optometr&iacute;a" onclick="validar_crear_optometria(3, 0);"/>
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
             echo"<div class='contenedor_error' style='display:block;'>Error al ingresar a consulta de optometr&iacute;a</div>";
         }
         ?>
         </div>
         <script type="text/javascript" src="../js/foundation.min.js"></script>
         <script id="ajax">
		 	seleccionar_ojo_op("<?php echo($id_ojo); ?>");
            $(document).foundation();
            initCKEditorOptom("txt_anamnesis");
            initCKEditorOptom("txt_observaciones_subjetivo");
            initCKEditorOptom("txt_observaciones_optometria");
            initCKEditorOptom("txt_observaciones_rxfinal");
			initCKEditorOptom("txt_diagnostico_optometria");
			
            for (var i = 0; i < <?php echo($cantidad_antecedentes); ?>; i++) {
				initCKEditorOptom("txt_texto_antecedente_" + i);
			}
			
            /* Ciclo para las remisiones */
			for (var i = 0; i < 10; i++) {
				initCKEditorOptom("tabla_rem_desc_" + i);
			}
			
			/* Ciclo para medicamentos */
			for (var i = 0; i < 10; i++) {
				initCKEditorOptom("frecAdmMed_" + i);
			}
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