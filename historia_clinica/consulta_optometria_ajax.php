<?php
session_start();
/*
  Pagina para crear consulta de optometria
  Autor: Helio Ruber López - 15/11/2013
 */

header("Content-Type: text/xml; charset=UTF-8");

require_once("../db/DbUsuarios.php");
require_once("../db/DbListas.php");
require_once("../db/DbConsultaOptometria.php");
require_once("../db/DbMenus.php");
require_once("../db/DbVariables.php");
require_once("../db/DbAdmision.php");
require_once("../db/DbDiagnosticos.php");
require_once("../db/DbTiposCitasDetalle.php");

require_once("../funciones/Class_Combo_Box.php");
require_once("../funciones/Class_Atencion_Remision.php");
require_once("../funciones/Class_Color_Pick.php");
require_once("../funciones/Class_Correccion_Optica.php");
require_once("../principal/ContenidoHtml.php");
require_once("../funciones/FuncionesPersona.php");
require_once("../funciones/Utilidades.php");

$dbUsuarios = new DbUsuarios();
$dbListas = new DbListas();
$dbConsultaOptometria = new DbConsultaOptometria();
$dbMenus = new DbMenus();
$dbVariables = new Dbvariables();
$dbAdmision = new DbAdmision();
$dbDiagnosticos = new DbDiagnosticos();
$dbTiposCitasDetalle = new DbTiposCitasDetalle();

$contenido = new ContenidoHtml();
$contenido->validar_seguridad(1);
$funciones_persona = new FuncionesPersona();
$utilidades = new Utilidades();

$combo = new Combo_Box();

$opcion = $_POST["opcion"];

function cambiar_mas($texto) {
    $resultado = str_replace("|mas", "+", $texto);
    return $resultado;
}

function cambiar_espacio($texto, $cambio) {
    $valor = "";
    if ($texto == "") {
        $valor = $cambio;
    } else {
        $valor = $texto;
    }
    return $valor;
}

switch ($opcion) {
    case "1": //Guardar Consulta de Optometria
        $id_usuario_crea = $_SESSION["idUsuario"];

        $hdd_id_hc_consulta = $_POST["hdd_id_hc_consulta"];
        $hdd_id_admision = $_POST["hdd_id_admision"];
        $txt_anamnesis = $utilidades->str_decode($_POST["txt_anamnesis"]);
        $avsc_lejos_od = cambiar_espacio($_POST["avsc_lejos_od"], 0);
        $avsc_media_od = cambiar_espacio($_POST["avsc_media_od"], 0);
        $avsc_cerca_od = cambiar_espacio($_POST["avsc_cerca_od"], 0);
        $avsc_lejos_oi = cambiar_espacio($_POST["avsc_lejos_oi"], 0);
        $avsc_media_oi = cambiar_espacio($_POST["avsc_media_oi"], 0);
        $avsc_cerca_oi = cambiar_espacio($_POST["avsc_cerca_oi"], 0);
        $querato_k1_od = $_POST["querato_k1_od"];
        $querato_ejek1_od = $_POST["querato_ejek1_od"];
        $querato_dif_od = $_POST["querato_dif_od"];
        $querato_k1_oi = $_POST["querato_k1_oi"];
        $querato_ejek1_oi = $_POST["querato_ejek1_oi"];
        $querato_dif_oi = $_POST["querato_dif_oi"];
        $refraobj_esfera_od = cambiar_mas($_POST["refraobj_esfera_od"]);
        $refraobj_cilindro_od = $_POST["refraobj_cilindro_od"];
        $refraobj_eje_od = $_POST["refraobj_eje_od"];
        $refraobj_lejos_od = cambiar_espacio($_POST["refraobj_lejos_od"], 0);
        $refraobj_esfera_oi = cambiar_mas($_POST["refraobj_esfera_oi"]);
        $refraobj_cilindro_oi = $_POST["refraobj_cilindro_oi"];
        $refraobj_eje_oi = $_POST["refraobj_eje_oi"];
        $refraobj_lejos_oi = cambiar_espacio($_POST["refraobj_lejos_oi"], 0);
        $subjetivo_esfera_od = cambiar_mas($_POST["subjetivo_esfera_od"]);
        $subjetivo_cilindro_od = $_POST["subjetivo_cilindro_od"];
        $subjetivo_eje_od = $_POST["subjetivo_eje_od"];
        $subjetivo_lejos_od = cambiar_espacio($_POST["subjetivo_lejos_od"], 0);
        $subjetivo_media_od = cambiar_espacio($_POST["subjetivo_media_od"], 0);
        $subjetivo_ph_od = cambiar_espacio($_POST["subjetivo_ph_od"], 0);
        $subjetivo_adicion_od = $_POST["subjetivo_adicion_od"];
        $subjetivo_cerca_od = cambiar_espacio($_POST["subjetivo_cerca_od"], 0);
        $subjetivo_esfera_oi = cambiar_mas($_POST["subjetivo_esfera_oi"]);
        $subjetivo_cilindro_oi = $_POST["subjetivo_cilindro_oi"];
        $subjetivo_eje_oi = $_POST["subjetivo_eje_oi"];
        $subjetivo_lejos_oi = cambiar_espacio($_POST["subjetivo_lejos_oi"], 0);
        $subjetivo_media_oi = cambiar_espacio($_POST["subjetivo_media_oi"], 0);
        $subjetivo_ph_oi = cambiar_espacio($_POST["subjetivo_ph_oi"], 0);
        $subjetivo_adicion_oi = $_POST["subjetivo_adicion_oi"];
        $subjetivo_cerca_oi = cambiar_espacio($_POST["subjetivo_cerca_oi"], 0);
        $cicloplejio_esfera_od = cambiar_mas($_POST["cicloplejio_esfera_od"]);
        $cicloplejio_cilindro_od = $_POST["cicloplejio_cilindro_od"];
        $cicloplejio_eje_od = $_POST["cicloplejio_eje_od"];
        $cicloplejio_lejos_od = cambiar_espacio($_POST["cicloplejio_lejos_od"], 0);
        $cicloplejio_esfera_oi = cambiar_mas($_POST["cicloplejio_esfera_oi"]);
        $cicloplejio_cilindro_oi = $_POST["cicloplejio_cilindro_oi"];
        $cicloplejio_eje_oi = $_POST["cicloplejio_eje_oi"];
        $cicloplejio_lejos_oi = cambiar_espacio($_POST["cicloplejio_lejos_oi"], 0);
        $refrafinal_esfera_od = cambiar_mas($_POST["refrafinal_esfera_od"]);
        $refrafinal_cilindro_od = $_POST["refrafinal_cilindro_od"];
        $refrafinal_eje_od = $_POST["refrafinal_eje_od"];
        $refrafinal_adicion_od = $_POST["refrafinal_adicion_od"];
        $refrafinal_esfera_oi = cambiar_mas($_POST["refrafinal_esfera_oi"]);
        $refrafinal_cilindro_oi = $_POST["refrafinal_cilindro_oi"];
        $refrafinal_eje_oi = $_POST["refrafinal_eje_oi"];
        $refrafinal_adicion_oi = $_POST["refrafinal_adicion_oi"];
        $tipo_guardar = $_POST["tipo_guardar"];
        //$presion_intraocular_od = $_POST["presion_intraocular_od"];
        //$presion_intraocular_oi = $_POST["presion_intraocular_oi"];
        $diagnostico_optometria = $utilidades->str_decode($_POST["diagnostico_optometria"]);
        $txt_observaciones_subjetivo = $utilidades->str_decode($_POST["txt_observaciones_subjetivo"]);
        $txt_observaciones_optometria = $utilidades->str_decode($_POST["txt_observaciones_optometria"]);
        $txt_observaciones_rxfinal = $utilidades->str_decode($_POST["txt_observaciones_rxfinal"]);
        $cmb_validar_consulta = $utilidades->str_decode($_POST["cmb_validar_consulta"]);
        $cmb_examinado_antes = $_POST["cmb_examinado_antes"];
        $rx_anteojos = $_POST["rx_gafas"];
        $rx_lc = $_POST["rx_ldc"];
        $rx_refractiva = $_POST["rx_cxr"];
        $rx_ayudas_bv = $_POST["rx_abv"];
        //$cmb_paciente_dilatado = $utilidades->str_decode($_POST["cmb_paciente_dilatado"]);
        $observaciones_optometricas_finales = $utilidades->str_decode($_POST["observaciones_optometricas_finales"]);
        $cmb_dominancia_ocular = $utilidades->str_decode($_POST["cmb_dominancia_ocular"]);
        $nombre_usuario_alt = $utilidades->str_decode($_POST["nombre_usuario_alt"]);
        $observaciones_admision = $utilidades->str_decode($_POST["observaciones_admision"]);
        $tipo_lente = $utilidades->str_decode($_POST["tipo_lente"]);
		
		$tipos_lentes_slct = $utilidades->str_decode($_POST["tipos_letnes_slct"]);
		$tipo_filtro_slct = $utilidades->str_decode($_POST["tipo_filtro_slct"]);
		$tiempo_vigencia_slct = $utilidades->str_decode($_POST["tiempo_vigencia_slct"]);
		$tiempo_periodo = $utilidades->str_decode($_POST["tiempo_periodo"]);
		$distancia_pupilar = $utilidades->str_decode($_POST["distancia_pupilar"]);
		$form_cantidad = $utilidades->str_decode($_POST["form_cantidad"]);
        $cadena_colores = $utilidades->str_decode($_POST["cadena_colores"]);
		
		$colorPick = new Color_Pick(array(), 0);
        $arr_cadenas_colores = $colorPick->getListasColores($cadena_colores);
		
        //Antecedentes
        $cant_antecedentes = $_POST["cant_antecedentes"];
        $array_antecedentes = array();
        for ($i = 0; $i < $cant_antecedentes; $i++) {
            $array_antecedentes[$i]["id_antecedentes_medicos"] = $utilidades->str_decode($_POST["id_antecedentes_medicos_".$i]);
            $array_antecedentes[$i]["texto_antecedente"] = $utilidades->str_decode($_POST["texto_antecedente_".$i]);
        }

        //Diagnósticos
        $cant_ciex = $_POST["cant_ciex"];
        $array_diagnosticos = array();
        for ($i = 1; $i <= $cant_ciex; $i++) {
            if (isset($_POST["cod_ciex_" . $i])) {
                $array_diagnosticos[$i][0] = $_POST["cod_ciex_" . $i];
                $array_diagnosticos[$i][1] = $_POST["val_ojos_" . $i];
            }
        }

        $array_gafas = array();
        $array_lenso = array();
        $array_ldc = array();
        $array_ayudas_bv = array();
        $array_cxr = array();


        // Procesar registros de detalles - gafas 						
        $canti = $_POST["hdd_canti_gafas"];
        for ($i = 1; $i <= $canti; $i++) {
            if (isset($_POST["tipo_gafas_" . $i]) || isset($_POST["ind_presentes_gafas_" . $i])) {
                $array_gafas[$i][0] = $_POST["cmb_tipo_gafas_" . $i];
                $array_gafas[$i][1] = $_POST["cmb_tipo_gafas_det_" . $i];
                $array_gafas[$i][2] = $_POST["otro_tipo_gafas_" . $i];
                $array_gafas[$i][3] = $_POST["cmb_filtro_" . $i];
                $array_gafas[$i][4] = $_POST["txt_tiempo_gafas_" . $i];
                $array_gafas[$i][5] = $_POST["cmb_tiempo_gafas_" . $i];
                $array_gafas[$i][6] = $_POST["cmb_grado_satisfaccion_gafas_" . $i];

                $array_gafas[$i][7] = cambiar_mas($_POST["lenso_esfera_od_" . $i]);
                $array_gafas[$i][8] = $_POST["lenso_cilindro_od_" . $i];
                $array_gafas[$i][9] = $_POST["lenso_eje_od_" . $i];
                $array_gafas[$i][10] = $_POST["lenso_adicion_od_" . $i];
                $array_gafas[$i][11] = cambiar_espacio($_POST["lenso_lejos_od_" . $i], 0);
                $array_gafas[$i][12] = cambiar_espacio($_POST["lenso_media_od_" . $i], 0);
                $array_gafas[$i][13] = cambiar_espacio($_POST["lenso_cerca_od_" . $i], 0);
                $array_gafas[$i][14] = cambiar_mas($_POST["lenso_esfera_oi_" . $i]);
                $array_gafas[$i][15] = $_POST["lenso_cilindro_oi_" . $i];
                $array_gafas[$i][16] = $_POST["lenso_eje_oi_" . $i];
                $array_gafas[$i][17] = $_POST["lenso_adicion_oi_" . $i];
                $array_gafas[$i][18] = cambiar_espacio($_POST["lenso_lejos_oi_" . $i], 0);
                $array_gafas[$i][19] = cambiar_espacio($_POST["lenso_media_oi_" . $i], 0);
                $array_gafas[$i][20] = cambiar_espacio($_POST["lenso_cerca_oi_" . $i], 0);

                $array_gafas[$i][21] = $_POST["cmb_prisma_tipo_od_" . $i];
                $array_gafas[$i][22] = $_POST["prisma_potencia_od_" . $i];
                $array_gafas[$i][23] = $_POST["cmb_prisma_base_od_" . $i];
                $array_gafas[$i][24] = $_POST["cmb_prisma_tipo_oi_" . $i];
                $array_gafas[$i][25] = $_POST["prisma_potencia_oi_" . $i];
                $array_gafas[$i][26] = $_POST["cmb_prisma_base_oi_" . $i];
                $array_gafas[$i][27] = $_POST["ind_presentes_gafas_" . $i];
            }
        }

        // Procesar registros de detalles - lentes de contacto
        $canti = $_POST["hdd_canti_ldc"];
        for ($i = 1; $i <= $canti; $i++) {
            if (isset($_POST["tipo_ldc_" . $i]) || isset($_POST["ind_presentes_ldc_" . $i])) {
                $array_ldc[$i][0] = $_POST["tipo_ldc_" . $i];
                $array_ldc[$i][1] = $_POST["tipo_ldc_det_" . $i];
                $array_ldc[$i][2] = $_POST["disenio_ldc_" . $i];
                $array_ldc[$i][3] = $_POST["tipoduracion_ldc_" . $i];
                $array_ldc[$i][4] = $_POST["ojo_ldc_" . $i];
                $array_ldc[$i][5] = $_POST["tiempo_uso_ldc_" . $i];
                $array_ldc[$i][6] = $_POST["und_tiempo_uso_ldc_" . $i];
                $array_ldc[$i][7] = $_POST["tiempo_nouso_ldc_" . $i];
                $array_ldc[$i][8] = $_POST["und_tiempo_nouso_ldc_" . $i];
                $array_ldc[$i][9] = $_POST["modalidad_uso_ldc_" . $i];
                $array_ldc[$i][10] = $_POST["tiempo_reemplazo_ldc_" . $i];
                $array_ldc[$i][11] = $_POST["grado_satisfacc_ldc_" . $i];
                $array_ldc[$i][12] = $_POST["tipo_ldc_otro_" . $i];
                $array_ldc[$i][13] = $_POST["tipoduracion_det_ldc_" . $i];
                $array_ldc[$i][14] = $_POST["ind_presentes_ldc_" . $i];
            }
        }

        // Procesar registros de detalles - ayudas baja visión
        $canti = $_POST["hdd_canti_abv"];
        for ($i = 1; $i <= $canti; $i++) {
            if (isset($_POST["cmb_tipo_abv_" . $i]) || isset($_POST["ind_presentes_abv_" . $i])) {
                $array_ayudas_bv[$i][0] = $_POST["cmb_tipo_abv_" . $i];
                $array_ayudas_bv[$i][1] = $_POST["cmb_tipo_abv_det_" . $i];
                $array_ayudas_bv[$i][2] = $_POST["cmb_grado_satisfaccion_abv_" . $i];
                $array_ayudas_bv[$i][3] = $_POST["tipo_abv_otro_" . $i];
                $array_ayudas_bv[$i][4] = $_POST["ind_presentes_abv_" . $i];
            }
        }

        // Procesar registros de detalles - cirugía refractiva
        $canti = $_POST["hdd_canti_cxr"];
        for ($i = 1; $i <= $canti; $i++) {
            if (isset($_POST["tipo_cxr_" . $i])) {
                $array_cxr[$i][0] = $_POST["tipo_cxr_" . $i];
                $array_cxr[$i][1] = $_POST["tipo_cxr_det_" . $i];
                $array_cxr[$i][2] = $_POST["ind_ajuste_" . $i];
                $array_cxr[$i][3] = $_POST["ojo_" . $i];
                $array_cxr[$i][4] = $_POST["tiempo_uso_" . $i];
                $array_cxr[$i][5] = $_POST["und_tiempo_uso_" . $i];
                $array_cxr[$i][6] = $_POST["entidad_" . $i];
                $array_cxr[$i][7] = $_POST["id_usuario_cirujano_" . $i];
                $array_cxr[$i][8] = $_POST["cirujano_otro_" . $i];
                $array_cxr[$i][9] = $_POST["grado_satisfacc_" . $i];
                $array_cxr[$i][10] = $_POST["tipo_cxr_otro_" . $i];
            }
        }

        $ind_opt = $dbConsultaOptometria->EditarConsultaOptometria($hdd_id_hc_consulta, $hdd_id_admision, $txt_anamnesis, $avsc_lejos_od, $avsc_media_od, $avsc_cerca_od,
				$avsc_lejos_oi, $avsc_media_oi, $avsc_cerca_oi, $querato_k1_od, $querato_ejek1_od, $querato_dif_od, $querato_k1_oi, $querato_ejek1_oi, $querato_dif_oi,
				$refraobj_esfera_od, $refraobj_cilindro_od, $refraobj_eje_od, $refraobj_lejos_od, $refraobj_esfera_oi, $refraobj_cilindro_oi, $refraobj_eje_oi,
				$refraobj_lejos_oi, $subjetivo_esfera_od, $subjetivo_cilindro_od, $subjetivo_eje_od, $subjetivo_lejos_od, $subjetivo_media_od, $subjetivo_ph_od,
				$subjetivo_adicion_od, $subjetivo_cerca_od, $subjetivo_esfera_oi, $subjetivo_cilindro_oi, $subjetivo_eje_oi, $subjetivo_lejos_oi, $subjetivo_media_oi,
				$subjetivo_ph_oi, $subjetivo_adicion_oi, $subjetivo_cerca_oi, $cicloplejio_esfera_od, $cicloplejio_cilindro_od, $cicloplejio_eje_od, $cicloplejio_lejos_od,
				$cicloplejio_esfera_oi, $cicloplejio_cilindro_oi, $cicloplejio_eje_oi, $cicloplejio_lejos_oi, $refrafinal_esfera_od, $refrafinal_cilindro_od,
				$refrafinal_eje_od, $refrafinal_adicion_od, $refrafinal_esfera_oi, $refrafinal_cilindro_oi, $refrafinal_eje_oi, $refrafinal_adicion_oi, $id_usuario_crea,
				$tipo_guardar, /* $presion_intraocular_od, $presion_intraocular_oi, */ $diagnostico_optometria, $array_diagnosticos, $txt_observaciones_subjetivo,
				$txt_observaciones_rxfinal, $cmb_validar_consulta, $cmb_examinado_antes, $rx_anteojos, $rx_lc, $rx_refractiva, $rx_ayudas_bv, $array_gafas, $array_ldc,
				$array_cxr, $array_ayudas_bv, $array_lenso, /* $cmb_paciente_dilatado, */ $observaciones_optometricas_finales, $cmb_dominancia_ocular,
				$observaciones_admision, $nombre_usuario_alt, $tipo_lente,$tipos_lentes_slct,$tipo_filtro_slct, $tiempo_vigencia_slct, $tiempo_periodo, $distancia_pupilar, 			                $form_cantidad , $txt_observaciones_optometria, $array_antecedentes);
        
        
        //Se guardan los datos de los colores
        $resul_aux = $dbConsultaOptometria->crear_editar_colores_hc($hdd_id_hc_consulta, $arr_cadenas_colores, $id_usuario_crea);

        $reg_menu = $dbMenus->getMenu(13);
        $url_menu = $reg_menu["pagina_menu"];
        ?>
        <input type="hidden" name="hdd_exito" id="hdd_exito" value="<?php echo($ind_opt); ?>" />
        <input type="hidden" name="hdd_exito_colores" id="hdd_exito_colores" value="<?php echo($resul_aux); ?>" />
        <input type="hidden" name="hdd_url_menu" id="hdd_url_menu" value="<?php echo($url_menu); ?>" />
        <input type="hidden" name="hdd_tipo_guardar" id="hdd_tipo_guardar" value="<?php echo($tipo_guardar); ?>" />
        <div class="contenedor_error" id="contenedor_error"></div>
        <div class="contenedor_exito" id="contenedor_exito"></div>
        <?php
        break;

    case "3": //Opciones de flujos alternativos
        $id_hc = $_POST["id_hc"];
        $id_admision = $_POST["id_admision"];

        $atencion_remision = new Class_Atencion_Remision();
        $atencion_remision->getFormularioRemisiones($id_hc, $id_admision, "validar_crear_optometria(4, 0);", "hdd_exito");
        break;


    case "4": //Pintar formulario en blanco para gafas
        $correccion_optica = new Class_Correccion_Optica();
        $registro_gafas = array();

        $id_nueva_fila = $_POST["nfrm"]; //índice de los objetos a pintar
        $registro_gafas["id_gafas"] = 0;

        $correccion_optica->getFormularioGafas($id_nueva_fila, $registro_gafas);

        // Pintar formulario en blanco para Lensometría de los gafas: 
        echo "|*;|separador|*;|"; // para distingir frm_gafas de frm_lenso
        $correccion_optica->getFormularioLensometria($id_nueva_fila, $registro_gafas);

        break;

    case "5": //Pintar formulario en blanco para lentes de contacto 
        $correccion_optica = new Class_Correccion_Optica();
        $registro_ldc = array();

        $id_nueva_fila = $_POST["nfrm"]; //índice de los objetos a pintar
        $registro_ldc["id_ldc"] = 0;

        $correccion_optica->getFormularioLentesDeContacto($id_nueva_fila, $registro_ldc);
        break;

    case "6": //Pintar formulario en blanco para ayudas en baja visión 		
        $correccion_optica = new Class_Correccion_Optica();
        $registro_abv = array();

        $id_nueva_fila = $_POST["nfrm"]; //índice de los objetos a pintar
        $registro_abv["id_abv"] = 0;

        $correccion_optica->getFormularioAyudaBajaVision($id_nueva_fila, $registro_abv);
        break;

    case "7": //Pintar formulario en blanco para cirugía refractiva 
        $correccion_optica = new Class_Correccion_Optica();
        $registro_cxr = array();

        $id_nueva_fila = $_POST["nfrm"]; //índice de los objetos a pintar
        $registro_cxr["id_cxr"] = 0;

        $correccion_optica->getFormularioCirugiaRefractiva($id_nueva_fila, $registro_cxr);
        break;

    case "8": //Opcion para buscar nivel 2 de lista Tipos de Anteojos			
        $i = $_POST["f"]; //consecutivo de fila
        $nodo_padre = $_POST["vp"];
        if ($nodo_padre == "") {
            $nodo_padre = " ";
        }

        $lista_tipos_gafas1 = $dbListas->getListaDetallesRecBase(12, $nodo_padre, 1);
        $combo->getComboDb("cmb_tipo_gafas_det_" . $i, " ", $lista_tipos_gafas1, "id_detalle, nombre_detalle", " ", "cmb_dependientes(11, " . $i . ")", "1", "width:160px", "", "select_hc no-margin");
        break;

    case "9": //Opcion para buscar nivel 2 de lista Tipos de LdC			
        $i = $_POST["f"]; //consecutivo de fila
        $nodo_padre = $_POST["vp"];
        if ($nodo_padre == "") {
            $nodo_padre = " ";
        }

        $lista_tipos_ldc1 = $dbListas->getListaDetallesRecBase(16, $nodo_padre, 1);
        $combo->getComboDb("cmb_tipo_ldc_det_" . $i, " ", $lista_tipos_ldc1, "id_detalle, nombre_detalle", " ", "", "1", "width:180px", "", "select_hc no_margin");
        break;

    case "10": //Opcion para buscar nivel 2 de lista Tipos de Ayudas Baja Visión			
        $i = $_POST["f"]; //consecutivo de fila
        $nodo_padre = $_POST["vp"];
        if ($nodo_padre == "") {
            $nodo_padre = " ";
        }

        $lista_tipos_abv1 = $dbListas->getListaDetallesRecBase(14, $nodo_padre, 1);
        $combo->getComboDb("cmb_tipo_abv_det_" . $i, " ", $lista_tipos_abv1, "id_detalle, nombre_detalle", " ", "cmb_dependientes(33, " . $i . ")", "1", "width:230px", "", "select_hc no-margin");
        break;

    case "11": //Opcion para buscar nivel 2 de lista Tipos de Cirugía Refractiva
        $i = $_POST["f"]; //consecutivo de fila
        $nodo_padre = $_POST["vp"];
        if ($nodo_padre == "") {
            $nodo_padre = " ";
        }

        $lista_tipos_cxr1 = $dbListas->getListaDetallesRecBase(15, $nodo_padre, 1);
        $combo->getComboDb("cmb_tipo_cxr_det_" . $i, " ", $lista_tipos_cxr1, "id_detalle, nombre_detalle", " ", "cmb_dependientes(44, " . $i . ")", "1", "width:225px", "", "select_hc no-margin");
        break;

    case "12": //Opcion para buscar nivel 2 de lista Tipos de duración LdC
        $i = $_POST["f"]; //consecutivo de fila
        $nodo_padre = $_POST["vp"];
        if ($nodo_padre == "") {
            $nodo_padre = " ";
        }

        $lista_tipos_ldc2 = $dbListas->getListaDetallesRecBase(16, $nodo_padre, 1);
        $combo->getComboDb("cmb_tipoduracion_det_ldc_" . $i, " ", $lista_tipos_ldc2, "id_detalle, nombre_detalle", " ", "", "1", "width:160px", "", "select_hc no-margin");
        break;
}
?>