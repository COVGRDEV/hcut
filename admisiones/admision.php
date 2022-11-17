<?php
	session_start();
	require_once("../db/DbAdmision.php");
	require_once("../db/DbVariables.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbCitas.php");
	require_once("../db/DbTiposCitas.php");
	require_once("../db/DbPacientes.php");
	require_once("../db/DbPaises.php");
	require_once("../db/DbDepartamentos.php");
	require_once("../db/DbDepMuni.php");
	require_once("../db/DbConvenios.php");
	require_once("../db/DbDisponibilidadProf.php");
	require_once("../db/DbMaestroExamenes.php");
	require_once("../db/DbTiemposCitasProf.php");
	require_once("../db/DbEstadosAtencion.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/FuncionesPersona.php");
	
	$dbAdmision = new DbAdmision();
	$dbVariables = new Dbvariables();
	$dbListas = new DbListas();
	$dbCitas = new DbCitas();
	$dbTiposCitas = new DbTiposCitas();
	$dbPacientes = new DbPacientes();
	$dbPaises = new DbPaises();
	$dbDepartamentos = new DbDepartamentos();
	$dbDepMuni = new DbDepMuni();
	$dbConvenios = new DbConvenios();
	$dbDisponibilidadProf = new DbDisponibilidadProf();
	$dbMaestroExamenes = new DbMaestroExamenes();
	$dbTiemposCitasProf = new DbTiemposCitasProf();
	$dbEstadosAtencion = new DbEstadosAtencion();
	
	$contenidoHtml = new ContenidoHtml();
	$combo = new Combo_Box();
	$funcionesPersona = new FuncionesPersona();
	
	$tipo_acceso_menu = $contenidoHtml->obtener_permisos_menu($_POST["hdd_numero_menu"]);
	
	$id_usuario = $_SESSION["idUsuario"];
	
	//variables
	$titulo = $dbVariables->getVariable(1);
	$variableHuella = $dbVariables->getVariable(8);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo($titulo["valor_variable"]); ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
        <link href="../css/azul.css" rel="stylesheet" type="text/css" />
        <link href="../css/foundation-datepicker.css" rel="stylesheet" type="text/css" />

        <script type="text/javascript" src="../js/jquery.min.js"></script>
        <script type="text/javascript" src="../js/jquery-ui.custom.js"></script>
        <script type="text/javascript" src="../js/jquery.cookie.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.add.js"></script>
        <script type="text/javascript" src="../js/ajax.js"></script>
        <script type="text/javascript" src="../js/funciones.js"></script>
        <script type="text/javascript" src="admision_v1.3.js"></script>
        <script type="text/javascript" src="../js/validaFecha.js"></script>
        <script type="text/javascript" src="../js/foundation-datepicker.js"></script>
        <script type="text/javascript" src="../js/jquery.maskedinput.js"></script>
        <script type="text/javascript"  src="../js/sweetalert2.all.min.js"></script>
    </head>
    <?php
    //Elimina el temporal de la huella
    @$dbPacientes->deleteTemporalHuella($id_usuario);

    //Si las variables son recibidas por POST
	
    //id_cita = 0 - id_paciente = 1 - id_admision = 2
    $bol_adm_desde_cita = false;
    if (isset($_POST["hdd_id_consulta"])) {
        $arr_cita_aux = explode("-", $_POST["hdd_id_consulta"]);

        if (!isset($arr_cita_aux[2])) {
            // Se verifica si ya existe una admisión asociada a la cita
            $admision_obj = $dbAdmision->get_admision_id_cita($arr_cita_aux[0]);
            if (isset($admision_obj["id_admision"])) {
                $arr_cita_aux[2] = $admision_obj["id_admision"];
                $bol_adm_desde_cita = true;
            }
        }
    } else {
        $arr_cita_aux = "";
    }

    if (isset($_POST["hdd_id_post"])) {
        $hdd_id_post_aux = $_POST["hdd_id_post"];
    } else {
        $hdd_id_post_aux = "";
    }

    //Declara el valor de los input
    $hdd_paciente_existe = 0;
    $tipo_identificacion_aux = "";
    $identificacion_aux = "";
    $nombres_aux = "";
    $nombres2_aux = "";
    $apellidos_aux = "";
    $apellidos2_aux = "";
    $fecha_nacimiento_aux = "";
    $direccion_aux = "";
    $telefono_aux = "";
    $pais_nac_aux = "";
    $departamento_nac_aux = "68";
    $municipio_nac_aux = "";
    $nombre_dep_nac_aux = "";
    $nombre_mun_nac_aux = "";
    $pais_aux = "";
    $departamento_aux = "68";
    $municipio_aux = "";
    $nombre_dep_aux = "";
    $nombre_mun_aux = "";
    $desplazado_aux = "46";
    $zona_aux = "19";
    $etnia_aux = "21";
    $sexo_aux = "";
    $telefono2_aux = "";
    $email_aux = "";
    $tipo_sangre = "";
    $factor_rh = "";
    $id_convenio = "";
    $id_plan = "0";
    $acompanante = "";
    $pulso = "";
    $presionarterial = "";
    $cancelar_hdd = "";
    $profesion_aux = "";
    $mconsulta_aux = "";
	$mercadeo_aux = "";
    $huella_digital_aux = "";
    $numero_hijos_aux = "";
    $numero_hijas_aux = "";
    $numero_hermanos_aux = "";
    $numero_hermanas_aux = "";
    $txt_nombre_cirugia = "";
    $txt_fecha_cirugia = "";
    $cmb_ojo = 0;
    $cmb_num_cirugia = 0;
    $txt_mconsulta = "";
    $txt_observaciones_admision = "";
    $cmb_medio_pago = "";
    $estado_consulta = 0;
    $id_estado_civil_aux = "";
    $num_carnet = "";
    $ind_num_carnet = "0";
    $ind_num_carnet_obl = "0";
    $nombre_med_orden = "";
    $tipo_coti_paciente = "";
	$rango_paciente = "";
    $status_convenio = "";
    $exentoCuotaModeradora = "";
	
    //variable para ocultar los campos
    $estilo = 'style="display:none;"';

    $cita_aux = "";
    $tipo_cita_obj = "";
    $id_paciente = "0";

    $bol_confirma_bloqueo = false;

    if (strlen(@$arr_cita_aux[0]) > 0) { //comprueba si lo recibido por post tiene caracteres
        $cita_aux = $dbCitas->getCita($arr_cita_aux[0]);
		$ind_num_carnet = $cita_aux["ind_num_carnet"];

        //Se verifica si ya se inició una admisión
        if (!isset($arr_cita_aux[2]) || $bol_adm_desde_cita) { //No existe id de la admisión
            //Se verifica en la cita si ya se inició el proceso de admisión
            if ($cita_aux["id_estado_cita"] == "258") {
                $bol_confirma_bloqueo = true;
            } else if ($cita_aux["id_estado_cita"] == "130" || $cita_aux["id_estado_cita"] == "14") {
                //Se marca en la cita el inicio del proceso de admisión
                $rta_aux = $dbCitas->marca_cita_atendida($arr_cita_aux[0], $id_usuario, 130);
            }
        }

        //Se buscan los datos del tipo de cita
        $tipo_cita_obj = $dbTiposCitas->get_tipo_cita($cita_aux["id_tipo_cita"]);

        if ($arr_cita_aux[1] != "0") { //Verifica si trae por post el ID de la cita
            $paciente_aux = $dbPacientes->getExistepaciente($arr_cita_aux[0], $arr_cita_aux[1]);
			
            $hdd_paciente_existe = 1;

            if (count($paciente_aux) > 0) {
                $value = $paciente_aux[0];

                $id_paciente = $value["id_paciente"];
                $tipo_identificacion_aux = $value["id_tipo_documento"];
                $identificacion_aux = $value["numero_documento"];

                $nombres_aux = $value["nombre_1"];
                $nombres2_aux = $value["nombre_2"];

                $apellidos_aux = $value["apellido_1"];
                $apellidos2_aux = $value["apellido_2"];

                $fecha_nacimiento_aux = $value["fecha_nacimiento"];
                $direccion_aux = $value["direccion"];

                //Se verifican los teléfonos suministrados en la cita
                $arr_telefonos_aux2 = explode("-", $value["telefono_contacto"]);
                $arr_telefonos_aux = array();
                foreach ($arr_telefonos_aux2 as $tel_aux) {
                    if (trim($tel_aux) != "") {
                        array_push($arr_telefonos_aux, trim($tel_aux));
                    }
                }
                $telefono_aux = isset($arr_telefonos_aux[0]) ? $arr_telefonos_aux[0] : $value["telefono_1"];
                $telefono2_aux = isset($arr_telefonos_aux[1]) ? $arr_telefonos_aux[1] : $value["telefono_2"];

                $email_aux = $value["email"];
                $pais_nac_aux = $value["id_pais_nac"];
                $departamento_nac_aux = $value["cod_dep_nac"];
                $municipio_nac_aux = $value["cod_mun_nac"];
                $nombre_dep_nac_aux = $value["nom_dep_nac"];
                $nombre_mun_nac_aux = $value["nom_mun_nac"];
                $pais_aux = $value["id_pais"];
                $departamento_aux = $value["cod_dep"];
                $municipio_aux = $value["cod_mun"];
                $nombre_dep_aux = $value["nom_dep"];
                $nombre_mun_aux = $value["nom_mun"];
                $desplazado_aux = $value["ind_desplazado"];
				$mercadeo_aux = $value[""];
                $zona_aux = $value["id_zona"];
                $etnia_aux = $value["id_etnia"];
                $sexo_aux = $value["sexo"];
                $mconsulta_aux = $value["observacion_cita"];
                $tipo_sangre = $value["tipo_sangre"];
                $factor_rh = $value["factor_rh"];
                $profesion_aux = $value["profesion"];
                $id_estado_civil_aux = $value["id_estado_civil"];
                $huella_digital_aux = base64_encode($value["huella"]);
                $status_aux = $value["status_convenio_paciente"];
                $status_convenio = $value["PstatusC"];
				$tipo_coti_paciente = $value["tipo_coti_paciente"];
                $rango_paciente = $value["rango_paciente"];
                $id_convenio = $value["id_convenio_paciente"];
				$id_plan = $value["id_plan"];
                $exentoCuotaModeradora = $value["exento_pamo_paciente"];
            }
        } else {
            $paciente_aux = $dbPacientes->getExistepaciente2($arr_cita_aux[0]);

            if (count($paciente_aux) > 0) {
                $hdd_paciente_existe = "2:".$funcionesPersona->obtenerNombreCompleto($paciente_aux["nombre_1"], $paciente_aux["nombre_2"], $paciente_aux["apellido_1"], $paciente_aux["apellido_2"]).":".$paciente_aux["numero_documento"].":".$paciente_aux["tipodocumento"]; //asigna el valor 2 al hidden, si el paciente es encontrado

                $id_paciente = $paciente_aux["id_paciente"];
                $tipo_identificacion_aux = $paciente_aux["id_tipo_documento"];
                $identificacion_aux = $paciente_aux["numero_documento"];
                $nombres_aux = $paciente_aux["nombre_1"];
                $nombres2_aux = $paciente_aux["nombre_2"];
                $apellidos_aux = $paciente_aux["apellido_1"];
                $apellidos2_aux = $paciente_aux["apellido_2"];
                $fecha_nacimiento_aux = $paciente_aux["fecha_nacimiento_aux"];
                $direccion_aux = $paciente_aux["direccion"];
                $telefono_aux = $paciente_aux["telefono_1"];
                $telefono2_aux = $paciente_aux["telefono_2"];
                $email_aux = $paciente_aux["email"];
                $pais_nac_aux = $paciente_aux["id_pais_nac"];
                $departamento_nac_aux = $paciente_aux["cod_dep_nac"];
                $municipio_nac_aux = $paciente_aux["cod_mun_nac"];
                $nombre_dep_nac_aux = $paciente_aux["nom_dep_nac"];
                $nombre_mun_nac_aux = $paciente_aux["nom_mun_nac"];
                $pais_aux = $paciente_aux["id_pais"];
                $departamento_aux = $paciente_aux["cod_dep"];
                $municipio_aux = $paciente_aux["cod_mun"];
                $nombre_dep_aux = $paciente_aux["nom_dep"];
                $nombre_mun_aux = $paciente_aux["nom_mun"];
                $desplazado_aux = $paciente_aux["ind_desplazado"];
                $zona_aux = $paciente_aux["id_zona"];
                $etnia_aux = $paciente_aux["id_etnia"];
                $sexo_aux = $paciente_aux["sexo"];
                $tipo_sangre = $paciente_aux["tipo_sangre"];
                $factor_rh = $paciente_aux["factor_rh"];
                $mconsulta_aux = $cita_aux["observacion_cita"];
                $profesion_aux = $paciente_aux["profesion"];
                $id_estado_civil_aux = $paciente_aux["id_estado_civil"];
				$tipo_coti_paciente = $paciente_aux["tipo_coti_paciente"];
                $rango_paciente = $paciente_aux["rango_paciente"];

                $cancelar_hdd = $cita_aux["nombre_1"].":".$cita_aux["nombre_2"].":".$cita_aux["apellido_1"].":".$cita_aux["apellido_2"].":".$cita_aux["id_tipo_documento"].":".$cita_aux["telefono_contacto"];
            } else {
                $hdd_paciente_existe = 3; //asigna el valor 3 al hidden, si el paciente no es encontrado

                $tipo_identificacion_aux = $cita_aux["id_tipo_documento"];
                $identificacion_aux = $cita_aux["numero_documento"];
                $nombres_aux = $cita_aux["nombre_1"];
                $nombres2_aux = $cita_aux["nombre_2"];
                $apellidos_aux = $cita_aux["apellido_1"];
                $apellidos2_aux = $cita_aux["apellido_2"];
                $fecha_nacimiento_aux = $cita_aux["fecha_nacimiento_aux"];
                $telefono_aux = $cita_aux["telefono_contacto"];
            }
        }

        if (isset($arr_cita_aux[2])) { //Este es el id de la admision
            $tabla_admision = $dbAdmision->get_admision($arr_cita_aux[2]);
            $acompanante = $tabla_admision["nombre_acompa"];
            $pulso = $tabla_admision["pulso"];
            $presionarterial = $tabla_admision["presion_arterial"];
            $txt_nombre_cirugia = $tabla_admision["nombre_cirugia"];
            $txt_fecha_cirugia = $tabla_admision["fecha_cirugia_t"];
            $cmb_ojo = $tabla_admision["id_ojo"];
            $cmb_num_cirugia = $tabla_admision["num_cirugia"];
            $txt_mconsulta = $tabla_admision["motivo_consulta"];
            $numero_hijos_aux = intval($tabla_admision["numero_hijos"], 10);
            $numero_hijas_aux = intval($tabla_admision["numero_hijas"], 10);
            $numero_hermanos_aux = intval($tabla_admision["numero_hermanos"], 10);
            $numero_hermanas_aux = intval($tabla_admision["numero_hermanas"], 10);
            $id_convenio = $tabla_admision["id_convenio"];
            $id_plan = $tabla_admision["id_plan"];
            $id_tipo_cita_admision = $tabla_admision["id_tipo_cita"];
            $id_prof_admision = $tabla_admision["id_usuario_prof"];
            $id_lugar_cita_admision = $tabla_admision["id_lugar_cita"];
            $tipo_cita_obj = $dbTiposCitas->get_tipo_cita($id_tipo_cita_admision);
            $estado_consulta = $tabla_admision["id_estado_atencion"];
            $profesional_atiende_admision = $tabla_admision["profesional_atiende_admision"];
            $txt_observaciones_admision = $tabla_admision["observaciones_admision"];
            $cmb_medio_pago = $tabla_admision["id_medio_pago"];
            $num_carnet = $tabla_admision["num_carnet"];
            $ind_num_carnet = $tabla_admision["ind_num_carnet"];
            $ind_num_carnet_obl = $tabla_admision["ind_num_carnet_obl"];
            $nombre_med_orden = $tabla_admision["nombre_med_orden"];

            //Se busca los datos del paciente
            $paciente_aux = $dbPacientes->getExistepaciente3($arr_cita_aux[1]);

            if (count($paciente_aux) >= 1) {
                $hdd_paciente_existe = "4:".$funcionesPersona->obtenerNombreCompleto($paciente_aux["nombre_1"], $paciente_aux["nombre_2"], $paciente_aux["apellido_1"], $paciente_aux["apellido_2"]).":".$paciente_aux["numero_documento"].":".$paciente_aux["tipodocumento"]; //asigna el valor 2 al hidden, si el paciente es encontrado

                $id_paciente = $paciente_aux["id_paciente"];
                $tipo_identificacion_aux = $paciente_aux["id_tipo_documento"];
                $identificacion_aux = $paciente_aux["numero_documento"];
                $nombres_aux = $paciente_aux["nombre_1"];
                $nombres2_aux = $paciente_aux["nombre_2"];
                $apellidos_aux = $paciente_aux["apellido_1"];
                $apellidos2_aux = $paciente_aux["apellido_2"];
                $fecha_nacimiento_aux = $paciente_aux["fecha_nacimiento_aux"];
                $direccion_aux = $paciente_aux["direccion"];
                $telefono_aux = $paciente_aux["telefono_1"];
                $telefono2_aux = $paciente_aux["telefono_2"];
                $email_aux = $paciente_aux["email"];
                $pais_nac_aux = $paciente_aux["id_pais_nac"];
                $departamento_nac_aux = $paciente_aux["cod_dep_nac"];
                $municipio_nac_aux = $paciente_aux["cod_mun_nac"];
                $nombre_dep_nac_aux = $paciente_aux["nom_dep_nac"];
                $nombre_mun_nac_aux = $paciente_aux["nom_mun_nac"];
                $pais_aux = $paciente_aux["id_pais"];
                $departamento_aux = $paciente_aux["cod_dep"];
                $municipio_aux = $paciente_aux["cod_mun"];
                $nombre_dep_aux = $paciente_aux["nom_dep"];
                $nombre_mun_aux = $paciente_aux["nom_mun"];
                $desplazado_aux = $paciente_aux["ind_desplazado"];
                $zona_aux = $paciente_aux["id_zona"];
                $etnia_aux = $paciente_aux["id_etnia"];
                $sexo_aux = $paciente_aux["sexo"];
                $tipo_sangre = $paciente_aux["tipo_sangre"];
                $factor_rh = $paciente_aux["factor_rh"];
                $mconsulta_aux = $cita_aux["observacion_cita"];
                $profesion_aux = $paciente_aux["profesion"];
                $id_estado_civil_aux = $paciente_aux["id_estado_civil"];

                $cancelar_hdd = $cita_aux["nombre_1"].":".$cita_aux["nombre_2"].":".$cita_aux["apellido_1"].":".$cita_aux["apellido_2"].":".$cita_aux["id_tipo_documento"].":".$cita_aux["telefono_contacto"];
            } else {
                $hdd_paciente_existe = 3; //asigna el valor 3 al hidden, si el paciente no es encontrado

                $tipo_identificacion_aux = $cita_aux["id_tipo_documento"];
                $identificacion_aux = $cita_aux["numero_documento"];
                $nombres_aux = $cita_aux["nombre_1"];
                $nombres2_aux = $cita_aux["nombre_2"];
                $apellidos_aux = $cita_aux["apellido_1"];
                $apellidos2_aux = $cita_aux["apellido_2"];
                $fecha_nacimiento_aux = $cita_aux["fecha_nacimiento_aux"];
                $telefono_aux = $cita_aux["telefono_contacto"];
            }
        }

        //Se buscan los datos de la última admisión
        //$admision_obj = $dbAdmision->get_ultima_admision_tiempo($id_paciente, "M", 12);
		
        //Se busca la edad del paciente
		/*if ($fecha_nacimiento_aux != "") {
			$edad_obj = $dbPacientes->getEdad($fecha_nacimiento_aux, "", 1);
			$arr_edad = explode("/", $edad_obj["edad"]);
			$edad_aux = intval($arr_edad[0], 10);
			$unidad_edad_aux = intval($arr_edad[1], 10);
			if ($unidad_edad_aux != 1) {
				$edad_aux = 0;
			}
		} else {
			$edad_aux = -1;
			$unidad_edad_aux = -1;
		}
			
		if (count($admision_obj) > 0 || $edad_aux == -1 || $edad_aux < 12 || $edad_aux > 50) {
			//Ha asistido a consultas en los últimos 12 meses, o el usuario no está entre los 12 y los 50 años
			$numero_hijos_aux = intval($admision_obj["numero_hijos"], 10);
			$numero_hijas_aux = intval($admision_obj["numero_hijas"], 10);
			$numero_hermanos_aux = intval($admision_obj["numero_hermanos"], 10);
			$numero_hermanas_aux = intval($admision_obj["numero_hermanas"], 10);
		} else if (count($admision_obj) == 0) {
			$direccion_aux = "";
			$municipio_aux = "";
			$nombre_mun_aux = "";
			$zona_aux = "";
			$telefono_aux = isset($arr_telefonos_aux[0]) ? $arr_telefonos_aux[0] : "";
			$telefono2_aux = isset($arr_telefonos_aux[1]) ? $arr_telefonos_aux[1] : "";
			$id_estado_civil_aux = "";
		}*/
    }
    ?>
    <body>
        <div style="display:none">
            <?php
				echo("Contenido del post:<br />");
				var_dump($_POST);
				if (strlen(@$arr_cita_aux[0]) > 0) {
					$cita_aux = $dbCitas->getCita($arr_cita_aux[0]);
					echo("<br /><br />Cita:<br />");
					var_dump($cita_aux);
				} else {
					echo("<br /><br />Sin id de cita");
				}
            ?>
        </div>
        <?php
			$contenidoHtml->validar_seguridad(0);
			$contenidoHtml->cabecera_html();
        ?>
        <div class="title-bar">
            <div class="wrapper">
                <div class="breadcrumb">
                    <ul>
                        <li class="breadcrumb_on">Admisiones</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="contenedor_principal volumen">
            <div class="padding">
                <h2>Admisi&oacute;n</h2>
                <div id="advertenciasg">
                    <div class="contenedor_advertencia" id="contenedor_advertencia"></div>
                    <div class="contenedor_exito" id="contenedor_exito"></div>
                    <div class="contenedor_error" id="contenedor_error"></div>
                    <div class="contenedor_confirmacion" id="contenedor_confirmacion"></div>
                </div>
                <input type="hidden" id="hdd_id_paciente" value="<?php echo($id_paciente); ?>" />
                <?php
                	if (isset($arr_cita_aux[2])) {
				?>
                <input type="hidden" name="hdd_id_admision_paciente" id="hdd_id_admision_paciente" value="<?php echo($arr_cita_aux[2]); ?>" />	
                <?php
					} else {
				?>	
                <input type="hidden" name="hdd_id_admision_paciente" id="hdd_id_admision_paciente" value="0" />	
                <?php
					}
					//Se halla el orden de atención del estado actual y del estado de espera de pago
					$estado_atencion_aux = $dbEstadosAtencion->getEstadoAtencion($estado_consulta);
					$orden_estado_atencion = intval($estado_atencion_aux["orden"], 10);
					
					$estado_atencion_aux = $dbEstadosAtencion->getEstadoAtencion(2);
					$orden_estado_espera_pago = intval($estado_atencion_aux["orden"], 10);
					
					//Se verifica si se debe permitir la modificación de valores de precios
					if ($orden_estado_atencion > $orden_estado_espera_pago) { //Si ya pasó de pagos no se puede modificar lugar de la cita
						$modificar_valores = false;
						$modificar_script = 0;
					} else {
						$modificar_valores = true;
						$modificar_script = 1;
					}
                ?>
                <input type="hidden" name="hdd_estado_consulta" id="hdd_estado_consulta" value="<?php echo($estado_consulta); ?>" />
                <div style="text-align: left;" id="d_admision">
                    <form id="frmAdmision">
                        <div>
                            <div>
                                <fieldset style="">
                                    <legend>Datos del paciente:</legend>
                                    <div id="hdd_post_existe" name="hdd_post_existe"></div>
                                    <table style="width:100%;">
                                        <tr>
                                            <td align="left" style="width:25%;">
                                                <label>Tipo de Identificaci&oacute;n*</label>
                                            </td>
                                            <td align="left" style="width:25%;">
                                                <label>Nro. de identificaci&oacute;n*</label>
                                            </td>
                                            <td align="left" style="width:25%;"></td>
                                            <td align="left" style="width:25%;"></td>
                                        </tr>
                                        <tr>
                                            <td align="left">
                                                <?php $combo->getComboDb("cmb_tipo_id", $tipo_identificacion_aux, $dbListas->getTipodocumento(), "id_detalle, nombre_detalle", "Seleccione el tipo de documento", "", 1, "width: 188px;"); ?>
                                            </td>
                                            <td align="left">
                                                <?php
                                                	$nombre_completo_aux = $funcionesPersona->obtenerNombreCompleto($nombres_aux, $nombres2_aux, $apellidos_aux, $apellidos2_aux);
                                                ?>
                                                <input type="hidden" id="hdd_numero_documento_ori" value="<?php echo($identificacion_aux); ?>" />
                                                <input type="hidden" id="hdd_nombre_completo_ori" value="<?php echo($nombre_completo_aux); ?>" />
                                                <input type="text" id="txt_id" name="txt_id" value="<?php echo($identificacion_aux); ?>" onkeypress="return leer_codigo_cedula(event, 2);" style="width:120px;" />
                                            </td>
                                            <td align="left" id="btnBuscarusuario">
                                                <?php
													if (!isset($arr_cita_aux[2])) {
												?>
                                                <input class="btnPrincipal peq" type="button" onclick="mostrar_formulario_flotante_adm(1)" value="Buscar paciente" />
                                                <?php
													}
												?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left">
                                                <label>Primer nombre*</label>
                                            </td>
                                            <td align="left">
                                                <label>Segundo nombre</label>
                                            </td>
                                            <td align="left">
                                                <label>Primer apellido*</label>
                                            </td>
                                            <td align="left">
                                                <label>Segundo apellido</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left">
                                                <input type="text" id="txt_nombre" name="txt_nombre" value="<?php echo($nombres_aux); ?>" onblur="trim_cadena(this);" />      
                                            </td>
                                            <td align="left">
                                                <input type="text" id="txt_nombre2" name="txt_nombre2" value="<?php echo($nombres2_aux); ?>" onblur="trim_cadena(this);" />
                                            </td>
                                            <td align="left">
                                                <input type="text" id="txt_apellido" name="txt_apellido" value="<?php echo($apellidos_aux); ?>" onblur="trim_cadena(this);" />
                                            </td>
                                            <td align="left">
                                                <input type="text" id="txt_apellido2" name="txt_apellido2" value="<?php echo($apellidos2_aux); ?>" onblur="trim_cadena(this);" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left">
                                                <label>Sexo*</label>
                                            </td>
                                            <td align="left">
                                                <label>Fecha de nacimiento*</label>
                                            </td>
                                            <td align="left">
                                                <label>Teléfono*</label>
                                            </td>
                                            <td align="left">
                                                <label>e-mail*</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left">
                                                <?php $combo->getComboDb("cmb_sexo", $sexo_aux, $dbListas->getTipoSexo(), "id_detalle, nombre_detalle", "Seleccione el sexo", "", "", "width: 188px;"); ?>
                                            </td>
                                            <td align="left">
                                                <input type="text" class="input required"  name="txt_fecha_nacimiento" id="txt_fecha_nacimiento" value="<?php echo($fecha_nacimiento_aux); ?>" maxlength="10" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" style="width:120px;" />
                                            </td>
                                            <td align="left">
                                                <input type="text" id="txt_telefono" name="txt_telefono" value="<?php echo($telefono_aux); ?>" maxlength="20" />
                                            </td>
                                            <td align="left">
                                                <input type="text" id="txt_email" name="txt_email" value="<?php echo($email_aux); ?>" />
                                            </td>
                                        </tr>
                                        </tr>
                                            <td align="left">
                                                <label>Pa&iacute;s*</label>
                                            </td>
                                            <td align="left" id="td_departamento_label">
                                                <label>Departamento*</label>
                                            </td>
                                            <td align="left" id="td_municipio_label">
                                                <label>Municipio*</label>
                                            </td>
                                            <td align="left" id="td_departamento_n_label" style="display:none;">
                                                <label>Estado / Provincia de residencia*</label>
                                            </td>
                                            <td align="left" id="td_municipio_n_label" style="display:none;">
                                                <label>Municipio de residencia*</label>
                                            </td>
                                            <td align="left">
                                                <label>Direcci&oacute;n*</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left">
                                                <?php $combo->getComboDb("cmb_id_pais", $pais_aux == "" ? "1" : $pais_aux, $dbPaises->getPaises(), "id_pais, nombre_pais", "Seleccione el pa&iacute;s", "seleccionar_pais(this.value, '')", 1, "width: 188px;"); ?>
                                            </td>
                                            <td align="left" id="td_departamento">
                                                <?php $combo->getComboDb("cmb_departamento", $departamento_aux, $dbDepartamentos->getDepartamentos(), "cod_dep, nom_dep", "Seleccione el departamento", "seleccionar_departamento(this.value, '', '');", "", "width: 188px;"); ?>
                                            </td>
                                            <td align="left" id="td_municipio">
                                                <div id="d_municipios">
                                                    <?php $combo->getComboDb("cmb_municipio", $municipio_aux, $dbDepMuni->getMunicipiosDepartamento($departamento_aux), "cod_mun_dane, nom_mun", "Seleccione el municipio", "", "", "width: 188px;"); ?>
                                                </div>
                                            </td>
                                            <td align="left" id="td_departamento_n" style="display:none;">
                                                <input type="text" id="txt_nombre_dep"  name="txt_nombre_dep" value="<?php echo($nombre_dep_aux); ?>" onblur="trim_cadena(this);" />
                                            </td>
                                            <td align="left" id="td_municipio_n" style="display:none;">
                                                <input type="text" id="txt_nombre_mun" name="txt_nombre_mun" value="<?php echo($nombre_mun_aux); ?>" onblur="trim_cadena(this);" />
                                            </td>
                                            <td align="left">
                                                <input type="text" id="txt_direccion" name="txt_direccion" value="<?php echo($direccion_aux); ?>" onblur="trim_cadena(this);" />
                                            </td>
                                            </tr>
                                                <tr>
                                       		 <td style="display:none" id="tr_dispositivos">
                                                <select onchange="tomarFoto();" style="width: 188px;" class="select" name="listaDeDispositivos" id="listaDeDispositivos">
                                                </select>
                                          	 </td>
                                             <td style="display:none">
                                             	<input id="btn-abrir-camara"  class="btnPrincipal peq" type="button" value="Abrir cámara" onClick="abrirCamara();"> 	
                                                                    <input type="hidden" name="hdd_foto_paciente" id="hdd_foto_paciente" value="" /> 
                                                    <input type="hidden" name="hdd_cargar_select" id="hdd_cargar_select" value="" /> 
                                             </td>
                                         			
                                          </tr>
                                           <tr id="tr_camara" style="display:none;">
                                        	<td>
                                               <video style="width:150px; height:150px;" id="camara" autoplay="autoplay"></video>
                                               <canvas id="resultado" style="display:none;" ></canvas>
                                            </td>
                                        </tr>
                                        <tr id="tr_tomar_foto" style="display:none;">
                                        	<td>
                                        	 	<input id="btn-tomar-foto" class="btnPrincipal peq" type="button" value="Tomar foto" onClick="tomarFoto();"> 
                                             </td>
                                        </tr>
                                        <tr>
                                         <!-- <td id="tr_tomar_foto" style="display:none;">			 					
                                                <input id="btn-tomar-foto" class="btnPrincipal peq" type="button" value="Tomar foto" onClick="continuar_tomarFoto();"> 	
                               	
                                           </td>-->
                                        </tr>
                                            
                                            
                                            
                                            
                                            
                                             <input type="hidden" name="hdd_id_lugar_session" id="hdd_id_lugar_session" value="<?=$_SESSION["idLugarUsuario"];?>" />
                                            <?php												
												if($_SESSION["idLugarUsuario"] == "491" || $_SESSION["idLugarUsuario"] == "466" || $_SESSION["idLugarUsuario"] == "465"
												|| $_SESSION["idLugarUsuario"] == "511" || $_SESSION["idLugarUsuario"] == "464"){
																																			
											?>
                                            <td align="left">
                                            	<input class="btnPrincipal peq" type="button" onclick="mostrarFormularioMercadeo();" value="Datos adicionales" /> 
                                                <input type="hidden" name="hdd_categoria_mercadeo" id="hdd_categoria_mercadeo" value="" />
                                                <input type="hidden" name="hdd_subcategoria_mercadeo" id="hdd_subcategoria_mercadeo" value="" />
                                                <input type="hidden" name="hdd_remitido_mercadeo" id="hdd_remitido_mercadeo" value="" />
                                                <input type="hidden" name="hdd_otro_mercadeo" id="hdd_otro_mercadeo" value="" />
                                                <input type="hidden" name="hdd_referido_mercadeo" id="hdd_referido_mercadeo" value="" />
                                            </td>
                                            
                                            <?php
												}else{
												?>
                                                <input type="hidden" name="hdd_categoria_mercadeo" id="hdd_categoria_mercadeo" value="" />
                                                <input type="hidden" name="hdd_subcategoria_mercadeo" id="hdd_subcategoria_mercadeo" value="" />
                                                <input type="hidden" name="hdd_remitido_mercadeo" id="hdd_remitido_mercadeo" value="" />
                                                <input type="hidden" name="hdd_otro_mercadeo" id="hdd_otro_mercadeo" value="" />
                                                <input type="hidden" name="hdd_referido_mercadeo" id="hdd_referido_mercadeo" value="" />
                                                <?php
												}
											?>
                                    </table>
                                </fieldset>
                            </div>
                            <div>
                                <fieldset style="">
                                    <legend>Datos de la cita:</legend>
                                    <table style="width:100%;">
                                        <tr>
                                            <td align="left" style="width:25%;">
                                                <label>Profesional que atiende*</label>
                                            </td>
                                            <td align="left" style="width:25%;">
                                                <label>Fecha/Hora de la consulta</label>
                                            </td>
                                            <td align="left" style="width:25%;">
                                                <label>Lugar*</label>
                                            </td>
                                            <td align="left" style="width:25%;">
                                                <label>Tipo de cita*</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left">
                                                <?php
													if (isset($id_tipo_cita_admision)) {
														$profesional_atiende = $profesional_atiende_admision;
														$id_usuario_prof = $id_prof_admision;
													} else if (isset($cita_aux["id_tipo_cita"])) {
														$profesional_atiende = $cita_aux["profesional_atiende"];
														$id_usuario_prof = $cita_aux["id_usuario_prof"];
													} else {
														$profesional_atiende = "";
														$id_usuario_prof = "";
													}
													
													if (is_array($arr_cita_aux)) {
														//Si los datos viene por post muestra un parrafo. Si no, muestra un combo-box
												?>
                                                <input type="hidden" name="hdd_usuario_prof" id="hdd_usuario_prof" value="<?php echo($id_usuario_prof); ?>" />
                                                <label class="inline">
                                                    <b><?php echo(isset($profesional_atiende) ? $profesional_atiende : $cita_aux); ?></b>
                                                </label>
                                                <?php
													} else {
												?>
                                                <div id="d_usuario_prof">
                                                <?php
														$lista_usuarios_disponibles = $dbDisponibilidadProf->getDisponibilidadProfActual();
														
														$combo->getComboDb("cmd_profesionalAtiende", "", $lista_usuarios_disponibles, "id_usuario, nombre_del_usuario", "Seleccione al profesional", "settipocita(".$modificar_script.", '');", "", "width: 188px;", "");
													}
												?>
                                                </div>
                                            </td>
                                            <td align="left">
                                                <label>
                                                    <?php
														if (isset($cita_aux["fecha_consulta"])) {
													?>
                                                    <b><?php echo($cita_aux["fecha_consulta"]." ".$cita_aux["hora_consulta_t"]); ?></b>
                                                    <?php
														}
													?>
                                                </label>
                                            </td>
                                            <td align="left">
                                                <?php
													$lista_lugares = $dbListas->getListaDetalles(12);
													
													if (isset($id_lugar_cita_admision)) {
														$id_lugar = $id_lugar_cita_admision;
													} else if (isset($cita_aux["id_lugar_cita"])) {
														$id_lugar = $cita_aux["id_lugar_cita"];
													} else {
														$id_lugar = "";
													}
													
													echo $combo->getComboDb("cmb_lugar_cita", $id_lugar, $lista_lugares, "id_detalle, nombre_detalle", "Seleccione el Lugar", "", $modificar_valores, "width:188px;");
                                                ?>
                                            </td>
                                            <td align="left">
                                                <?php
													$lista_tipos_citas = $dbTiposCitas->getTiposcitas();
													foreach ($lista_tipos_citas as $tipo_cita_aux) {
												?>
                                                <input type="hidden" name="hdd_ind_preqx_<?php echo($tipo_cita_aux["id_tipo_cita"]); ?>" id="hdd_ind_preqx_<?php echo($tipo_cita_aux["id_tipo_cita"]); ?>" value="<?php echo($tipo_cita_aux["ind_preqx"]); ?>" />
                                                <input type="hidden" name="hdd_ind_signos_vitales_<?php echo($tipo_cita_aux["id_tipo_cita"]); ?>" id="hdd_ind_signos_vitales_<?php echo($tipo_cita_aux["id_tipo_cita"]); ?>" value="<?php echo($tipo_cita_aux["ind_signos_vitales"]); ?>" />
                                                <input type="hidden" name="hdd_ind_examenes_<?php echo($tipo_cita_aux["id_tipo_cita"]); ?>" id="hdd_ind_examenes_<?php echo($tipo_cita_aux["id_tipo_cita"]); ?>" value="<?php echo($tipo_cita_aux["ind_examenes"]); ?>" />
                                                <?php
													}
												?>
                                                <table border="0" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td>
                                                            <div id="cmbTipoCita">
                                                                <?php
																	if (isset($id_tipo_cita_admision)) {
																		$id_tipo_cita = $id_tipo_cita_admision;
																		$id_usuario_prof = $id_prof_admision;
																	} else if (isset($cita_aux["id_tipo_cita"])) {
																		$id_tipo_cita = $cita_aux["id_tipo_cita"];
																		$id_usuario_prof = $cita_aux["id_usuario_prof"];
																	} else {
																		$id_tipo_cita = "";
																		$id_usuario_prof = "";
																	}
																	
																	if (is_array($arr_cita_aux)) {//Si los datos viene por post muestra un parrafo. Si no, muestra un combo-box
																		echo $combo->getComboDb("cmb_tipo_cita", $id_tipo_cita, $dbTiemposCitasProf->getTiemposcitasprofeAdmision($id_usuario_prof), "id_tipo_cita, nombre_tipo_cita", "Seleccione el Tipo de Cita", "seleccionar_tipo_cita(this.value, ".$modificar_script."); cargar_precios(".$modificar_script.", 0);", $modificar_valores, "width: 188px;");
																	} else {
																?>
                                                                <select class="select" style="width: 188px;" id="cmb_tipo_cita" name="cmb_tipo_cita">
                                                                    <option value="">Seleccione el Tipo de Cita</option>
                                                                </select> 
                                                                <?php
																	}
																?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <img src="../imagenes/refresh-icon.png" class="img_button" title="Refrescar profesionales" onclick="actualizar_citas_profesionales(<?php echo($modificar_script); ?>);" />
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td align="center" colspan="2">
                                                <div id="d_usuarios_cita"></div>
                                                <?php
													if (isset($arr_cita_aux[2])) { //Este es el id de la admision  
												?>
                                                <script id="ajax" type="text/javascript">
													cargar_usuarios_tipo_cita_admision(<?php echo($arr_cita_aux[2]); ?>, <?php echo($id_tipo_cita_admision); ?>, <?php echo($id_prof_admision); ?>, <?php echo($estado_consulta); ?>);
												</script>
                                                <?php
													} else {
												?>
                                                <script id="ajax" type="text/javascript">
													cargar_usuarios_tipo_cita();
												</script>
                                                <?php
													}
												?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left" colspan="4" style="background-color: #FAD462;padding:5px 20px 5px 20px;">
                                                <label style="font-weight: bold;">Observaciones de la cita</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left" colspan="4" style="background-color: #FCE398; padding: 20px;">
                                                <span style="font-size: 10pt;">
                                                    <?php
                                                    if (isset($cita_aux["observacion_cita"])) {
                                                        echo($cita_aux["observacion_cita"]);
                                                    } else {
                                                        echo("-");
                                                    }
                                                    ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr id="tr_cirugia_label"<?php if (!isset($tipo_cita_obj["ind_preqx"]) || $tipo_cita_obj["ind_preqx"] != "1") { ?> style="display:none;"<?php } ?>>
                                            <td align="left">
                                                <label>Cirug&iacute;a*</label>
                                            </td>
                                            <td align="left">
                                                <label>Fecha de la cirug&iacute;a*</label>
                                            </td>
                                            <td align="left">
                                                <label>Ojo*</label>
                                            </td>
                                            <td align="left">
                                                <label class="inline">N&uacute;mero cirug&iacute;a*</label>
                                            </td>
                                        </tr>
                                        <tr id="tr_cirugia"<?php if (!isset($tipo_cita_obj["ind_preqx"]) || $tipo_cita_obj["ind_preqx"] != "1") { ?> style="display:none;"<?php } ?>>
                                            <td align="left">
                                                <input type="text" id="txt_nombre_cirugia" name="txt_nombre_cirugia"  value="<?php echo($txt_nombre_cirugia); ?>"   maxlength="500" onblur="trim_cadena(this);" />
                                            </td>
                                            <td align="left">
                                                <input type="text" id="txt_fecha_cirugia" name="txt_fecha_cirugia" value="<?php echo($txt_fecha_cirugia); ?>" maxlength="10" class="input" style="width:120px;" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" />
                                            </td>
                                            <td align="left">
                                                <?php
													//Se obtiene el listado de las opciones de ojos
													$lista_ojos = $dbListas->getListaDetalles(14);
													$combo->getComboDb("cmb_ojo", $cmb_ojo, $lista_ojos, "id_detalle, nombre_detalle", "Seleccione", "", "", "width:120px;");
												?>
                                            </td>
                                            <td align="left">
                                                <select name="cmb_num_cirugia" id="cmb_num_cirugia" class="select" style="width:120px;">
                                                    <option value="">Seleccione</option>
                                                    <option value="0">&nbsp;</option>
                                                    <?php
														for ($i = 1; $i <= 3; $i++) {
															$selected_qx = "";
															if ($i == $cmb_num_cirugia) {
																$selected_qx = " selected=\"selected\"";
															}
													?>
                                                    <option value="<?php echo($i); ?>" <?php echo($selected_qx); ?>><?php echo($i); ?></option>
                                                    <?php
														}
													?>
                                                </select>
                                            </td>
                                        </tr>
                                        <?php
											//Obtener los examenes segun la admision
											if (isset($arr_cita_aux[2])) { //Este es el id de la admision
												$tabla_examenes_admisiones = $dbAdmision->get_examenes_admision($arr_cita_aux[2]);
											} else {
												$tabla_examenes_admisiones = $dbAdmision->get_examenes_admision(0);
											}
											
											$cantidad_examenes = count($tabla_examenes_admisiones);
										?>
                                        <tr id="tr_examenes"<?php if (!isset($tipo_cita_obj["ind_examenes"]) || $tipo_cita_obj["ind_examenes"] != "1") { ?> style="display:none;"<?php } ?>>
                                            <td align="left">
                                                <label>Cantidad de ex&aacute;menes*</label>
                                            </td>
                                            <td align="center" colspan="2" rowspan="2">
                                                <table class="modal_table">
                                                    <tr>
                                                        <th align="center" style="width:75%;">Examen*</th>
                                                        <th align="center" style="width:25%;">Ojo*</th>
                                                    </tr>
                                                    <?php
														$lista_examenes = $dbMaestroExamenes->get_lista_examenes(1);
														for ($i = 0; $i < 10; $i++) {
															$ind_oculto = "none";
															$cmb_examen_id = "";
															$cmb_ojo_id = "";
															if (count($tabla_examenes_admisiones) > 0) {
																foreach ($tabla_examenes_admisiones as $fila_examenes) {
																	if ($i == $fila_examenes["numero_examen"] - 1) {
																		$ind_oculto = "";
																		$cmb_examen_id = $fila_examenes["id_examen"];
																		$cmb_ojo_id = $fila_examenes["id_ojo"];
																	}
																}
															}
													?>
                                                    <tr id="tr_examen_<?php echo($i); ?>" style="display:<?php echo($ind_oculto); ?>;">
                                                        <td align="center">
                                                            <?php
																echo($combo->getComboDb("cmb_examen_" . $i, $cmb_examen_id, $lista_examenes, "id_examen, nombre_examen", "Seleccione un examen", "cargar_precios(".$modificar_script.", 0);", true, "width:320px;"));
															?>
                                                        </td>
                                                        <td align="center">
                                                            <?php
																echo($combo->getComboDb("cmb_ojo_examen_" . $i, $cmb_ojo_id, $lista_ojos, "id_detalle, nombre_detalle", "Seleccione", "cargar_precios(".$modificar_script.", 0);", true, "width:100px;"));
															?>
                                                        </td>
                                                    </tr>
                                                    <?php
														}
													?>
                                                    <tr id="tr_examen_vacio">
                                                        <td align="center" colspan="2">Seleccione una cantidad</td>
                                                    </tr>
                                                </table>
                                                <br />
                                            </td>
                                        </tr>
                                        <tr id="tr_examenes2"<?php if (!isset($tipo_cita_obj["ind_examenes"]) || $tipo_cita_obj["ind_examenes"] != "1") { ?> style="display:none;"<?php } ?>>
                                            <td align="left" valign="top">
                                                <select name="cmb_cant_examenes" id="cmb_cant_examenes" class="select" style="width:188px;" onchange="mostrar_examenes(this.value); cargar_precios(<?php echo($modificar_script); ?>, 0);">
                                                    <option value="">0</option>
                                                    <?php
														for ($i = 1; $i <= 10; $i++) {
															$valor_checked = "";
															if ($cantidad_examenes == $i) {
																$valor_checked = " selected=\"selected\"";
															}
													?>
                                                    <option value="<?php echo($i); ?>" <?php echo($valor_checked); ?>><?php echo($i); ?></option>
                                                    <?php
														}
													?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr id="tr_nombre_med_orden_label"<?php if (!isset($cita_aux["ind_eco"]) || $cita_aux["ind_eco"] != "1") { ?> style="display:none;"<?php } ?>>
                                            <td align="left">
                                                <label>M&eacute;dico de la orden</label>
                                            </td>
                                        </tr>
                                        <tr id="tr_nombre_med_orden"<?php if (!isset($cita_aux["ind_eco"]) || $cita_aux["ind_eco"] != "1") { ?> style="display:none;"<?php } ?>>
                                            <td align="left">
                                                <input type="text" id="txt_nombre_med_orden" name="txt_nombre_med_orden" maxlength="200" value="<?php echo(@$nombre_med_orden); ?>" onblur="trim_cadena(this);" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="text-align: center;">
                                                <div>
                                                    <span><label class="inline">Motivo de la consulta*</label></span>
                                                </div>
                                                <textarea class="textAreaAdmision" id="txt_mconsulta" name="txt_mconsulta" rows="4" cols="50" onblur="trim_cadena(this);"><?php echo($txt_mconsulta); ?></textarea>
                                            </td>
                                            <td colspan="2" style="text-align: center;">
                                                <div>
                                                    <span><label class="inline">Observaciones</label></span>
                                                </div>
                                                <textarea class="textAreaAdmision" id="txt_observaciones_admision" name="txt_observaciones_admision" rows="4" cols="50" onblur="trim_cadena(this);"><?php echo($txt_observaciones_admision); ?></textarea>
                                            </td>
                                        </tr>
                                    </table>
                                </fieldset>
                            </div>
                            <div class="clear_both"></div>
                            <div style="text-align: center; padding-top: 10px;">
                                <?php
									$estilo = "background: #399000;";
									if ($status_aux == 442) {/* Si el seguro está inactivo */
										$estilo = "background: #DD5043;";
									}
								?>
                                <div style="<?= $estilo ?> width: 50%;margin: 0 auto;padding: 1px;">
                                    <h5 style="color: #FFF;">Estado del Convenio/Seguro: <span id="sp_status_convenio"><?= $status_convenio ?></span></h5>
                                    <input type="hidden" name="hdd_exento" id="hdd_exento" value="<?= $exentoCuotaModeradora ?>" />
                                </div>
                                <table border="0" style="width:100%;">
                                    <?php
										//Se verifica si en la cita se marcó el indicador de no pago
										$ind_no_pago = "0";
										if (isset($cita_aux["ind_no_pago"])) {
											$ind_no_pago = $cita_aux["ind_no_pago"];
										}
										
										if ($ind_no_pago == "1") {
									?>
                                    <tr>
                                        <td align="center" colspan="4">
                                            <label class="texto_resaltar"><b>Cita marcada como NP</b></label>
                                        </td>
                                    </tr>
                                    <?php
										}
									?>
                                    <tr>
                                        <td align="right" style="width:13%;">
                                            <label class="inline">Convenio/Seguro*:</label>
                                        </td>
                                        <td align="left" style="width:21%;">
                                            <?php
												$lista_convenios = $dbConvenios->getListaConveniosActivos();
												
												if (count($lista_convenios) > 0) {
													foreach ($lista_convenios as $convenio_aux) {
											?>
                                            <input type="hidden" id="hdd_eco_convenio_<?php echo($convenio_aux["id_convenio"]); ?>" value="<?php echo($convenio_aux["ind_eco"]); ?>" />
                                            <input type="hidden" id="hdd_ind_num_carnet_<?php echo($convenio_aux["id_convenio"]); ?>" value="<?php echo($convenio_aux["ind_num_carnet"]); ?>" />
                                            <input type="hidden" id="hdd_ind_num_carnet_obl_<?php echo($convenio_aux["id_convenio"]); ?>" value="<?php echo($convenio_aux["ind_num_carnet_obl"]); ?>" />
                                            <?php
													}
												}
												
												if (isset($arr_cita_aux[2])) { //Este es el id de la admision
											?>
                                            <input type="hidden" id="hdd_convenio_ini" value="<?php echo($id_convenio); ?>" />
                                            <?php
												} else {
											?>
                                            <input type="hidden" id="hdd_convenio_ini" value="<?php echo(isset($cita_aux["id_convenio"]) ? $cita_aux["id_convenio"] : 0); ?>" />
                                            <?php
												}
											?>
                                            <div id="d_convenio">
                                                <?php
													if (isset($arr_cita_aux[2])) { //Este es el id de la admision
														$combo->getComboDb("cmb_convenio", $id_convenio, $lista_convenios, "id_convenio, nombre_convenio", "Seleccione el Convenio", "seleccionar_convenio(".$id_plan.", ".$modificar_script.");", 1, "width: 188px;");
													} else {
														$combo->getComboDb("cmb_convenio", isset($cita_aux["id_convenio"]) ? $cita_aux["id_convenio"] : 0, $lista_convenios, "id_convenio, nombre_convenio", "Seleccione el Convenio", "seleccionar_convenio(".$id_plan.", ".$modificar_script.");", 1, "width: 188px;");
													}
												?>
                                            </div>
                                        </td>
                                        <td align="right" style="width:8%;">
                                            <label class="inline">Plan*:</label>
                                        </td>
                                        <td align="left" style="width:20%;">
                                            <div id="d_plan">
                                                <select class="select" style="width: 188px;" id="cmb_plan" name="cmb_plan">
                                                    <option>Seleccione el Plan</option>
                                                </select>
                                                <input type="hidden" id="hdd_ind_cuota_moderadora" name="hdd_ind_cuota_moderadora" value="<?= $indCuotaModeradora ?>" />
                                            </div>
                                            <?php
												if (isset($arr_cita_aux[0])) { //Este es el id de la cita
											?>
                                            <script id="ajax">
												//Ejecuta la funcion de precios que me muestra el listado de precios en la tabla con id: tablaPrecios
												seleccionar_convenio(<?php echo($id_plan); ?>, <?php echo($modificar_script); ?>);
											</script>
                                            <?php
												}
											?>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<td align="right">
                                        	<label class="inline">Tipo de usuario*:</label>
                                        </td>
                                        <td align="left">
                                        	<?php
												/*$array_tipo_usuario = array();
												$array_tipo_usuario[0]["id"] = "0";
												$array_tipo_usuario[0]["nombre"] = "No aplica";
												$array_tipo_usuario[1]["id"] = "1";
												$array_tipo_usuario[1]["nombre"] = "Cotizante";
												$array_tipo_usuario[2]["id"] = "2";
												$array_tipo_usuario[2]["nombre"] = "Beneficiario";
												$array_tipo_usuario[3]["id"] = "3";
												$array_tipo_usuario[3]["nombre"] = "Subsidiado";
                                            	$combo->getComboDb("cmb_tipo_coti_paciente", $tipo_coti_paciente, $array_tipo_usuario, "id, nombre", "Seleccione", "cargar_precios(".$modificar_script.", 0);", 1, "width: 188px;");*/
												$lista_tipo_usuario = $dbListas->getListaDetalles(99);
                                            	$combo->getComboDb("cmb_tipo_coti_paciente", $tipo_coti_paciente, $lista_tipo_usuario, "id_detalle, nombre_detalle", "Seleccione", "cargar_precios(".$modificar_script.", 0);", 1, "width: 188px;");
											?>
                                        </td>
                                    	<td align="right">
                                        	<label class="inline">Rango*:</label>
                                        </td>
                                        <td align="left">
                                        	<?php
												$array_rango = array();
												$array_rango[0]["id"] = "0";
												$array_rango[0]["nombre"] = "No aplica";
												$array_rango[1]["id"] = "1";
												$array_rango[1]["nombre"] = "Uno";
												$array_rango[2]["id"] = "2";
												$array_rango[2]["nombre"] = "Dos";
												$array_rango[3]["id"] = "3";
												$array_rango[3]["nombre"] = "Tres";
												$array_rango[4]["id"] = "6";
												$array_rango[4]["nombre"] = "Subsidiado nivel 1";
												$array_rango[5]["id"] = "7";
												$array_rango[5]["nombre"] = "Subsidiado nivel 2";
                                            	$combo->getComboDb("cmb_rango_paciente", $rango_paciente, $array_rango, "id, nombre", "Seleccione", "cargar_precios(".$modificar_script.", 0);", 1, "width: 188px;");
											?>
                                        </td>
                                    </tr>
                                    <?php
										
										$visible_carnet_aux = "none";
										if ($ind_num_carnet == "1") {
											$visible_carnet_aux = "table-cell";
										}
                                    ?>
                                    <tr>
                                        <td align="right" id="td_num_carnet_l" style="display:<?php echo($visible_carnet_aux) ?>;">
                                            <label class="inline">No. Carnet:</label>
                                        </td>
                                        <td align="left" id="td_num_carnet" style="display:<?php echo($visible_carnet_aux) ?>;">
                                            <input type="hidden" id="hdd_num_carnet_obl" value="<?php echo($ind_num_carnet_obl); ?>" />
                                            <input style="width: 188px;" type="text" id="txt_num_carnet" name="txt_num_carnet" value="<?php echo($num_carnet); ?>" maxlength="20" onblur="trim_cadena(this);" />
                                            <div id="d_num_carnet_busq" style="display:none;"></div>
                                        </td>
                                        <td align="right" id="td_num_poliza_l" style="display:<?php echo($visible_carnet_aux) ?>;">
                                            <label class="inline">No. P&oacute;liza:</label>
                                        </td>
                                        <td align="left" id="td_num_poliza" style="display:<?php echo($visible_carnet_aux) ?>;">
                                        	 <input style="width: 188px;" type="text" id="txt_num_poliza" name="txt_num_poliza" value="" maxlength="20" onblur="trim_cadena(this);" />
                                        </td>
                                        
                                    </tr>
                                    <tr>
                                         <td align="right" id="td_num_mipress_l" style="display:<?php echo($visible_carnet_aux) ?>;">
                                            <label class="inline">No. Mipress:</label>
                                        </td>
                                        <td align="left" id="td_num_mipress" style="display:<?php echo($visible_carnet_aux) ?>;">
                                            <input style="width: 188px;" type="text" id="txt_num_mipress" name="txt_num_mipress" value="" maxlength="20" onblur="trim_cadena(this);" />
                                           
                                        </td>
                                        <td align="right" id="td_num_ent_mipress_l" style="display:<?php echo($visible_carnet_aux) ?>;">
                                            <label class="inline">No. entrega mipress:</label>
                                        </td>
                                        <td align="left" id="td_num_ent_mipress" style="display:<?php echo($visible_carnet_aux) ?>;">
                                        	 <input style="width: 188px;" type="text" id="txt_num_ent_mipress" name="txt_num_ent_mipress" value="" maxlength="20" onblur="trim_cadena(this);" />
                                        </td>
                                    </tr>
                                    <tr>
                                    	<td align="right">
                                            <label class="inline">Atenci&oacute;n NP:</label>
                                        </td>
                                        <td align="left">
                                        	<input type="checkbox" id="chk_np_atencion" name="chk_np_atencion" onchange="marcar_np_atencion('<?php echo($modificar_script); ?>');" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" style="text-align: center;">
                                            <div class="d_contenedor_precios" id="precios">

                                            </div>
                                            <?php
												if (isset($arr_cita_aux[0])) { //Este es el id de la cita
											?>
                                            <script id="ajax">
												setTimeout(function() { cargar_precios(<?php echo($modificar_script); ?>, 1); }, 1000);
											</script>
                                            <?php
												}
											?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <?php
								if ($variableHuella["valor_variable"] == "1") {
							?>
                            <fieldset style="">
                                <legend>Huella del paciente:</legend>
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="text-align: center;">
                                            <?php
												require_once("../db/DbPacientes.php");
												$dbPacientes = new DbPacientes();
												
										
											?>
                                            <applet codebase="../manejo_huella/." code="sensorhuella.HuellaApplet.class" archive="SensorHuella.jar" width=205 height=240>
                                                <param name="id_usuario" id="id_usuario" value="<?php echo($id_usuario); ?>" />
                                                <param name="huella" id="huella" value="<?php echo($huella_digital_aux); ?>" />
                                            </applet>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                            <?php
								}
							?>
                            <div style="text-align: center; padding-top: 10px;">
                                <?php
									if (isset($arr_cita_aux[2]) && !$bol_adm_desde_cita) {
								?>
                                <input type="button" id="btnNuevo" class="btnPrincipal" value="Imprimir recibo" onclick="imprimir_recibo_pago();" />
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <?php
									}
									
									if ($tipo_acceso_menu == 2) {
								?>
                                <input type="submit" id="btnNuevo" class="btnPrincipal" value="Registrar admisi&oacute;n" onclick="guardar_admision();" />
                                <?php
									}
								?>
                            </div>
                    </form>
                    <input type="hidden" id="hdd_id_post" name="hdd_id_post" value="<?php echo($hdd_id_post_aux); ?>" />
                    <input type="hidden" id="hdd_id_consulta" name="hdd_id_consulta" value="<?php
						if (is_array($arr_cita_aux)) {
							if ($arr_cita_aux[1] != "0") {
								echo($arr_cita_aux[0]."-".$arr_cita_aux[1]);
							} else {
								echo($arr_cita_aux[0]."-0");
							}
						} else {
							echo("0-0");
						}
					?>" />
                    <input type="hidden" id="hdd_paciente_existe" name="hdd_paciente_existe" value="<?php echo($hdd_paciente_existe); ?>" />
                    <input type="hidden" id="hdd_jueves" name="hdd_jueves"  />
                    <input type="hidden" id="hdd_paciente_cancelar" name="hdd_paciente_cancelar" value="<?php echo($cancelar_hdd); ?>" />
                    <input type="hidden" id="hdd_post" name="hdd_post" value="<?php
						if (is_array($arr_cita_aux)) {//Si los datos viene por post muestra un parrafo. Si no, muestra un combo-box
							echo(strlen($arr_cita_aux[0]) >= 1 ? 1 : 0);
						} else {
							echo(strlen($arr_cita_aux) >= 1 ? 1 : 0);
						}
					?>" />
                    <input type="hidden" id="hdd_evento_pistola" name="hdd_evento_pistola" value="0"  />
                    <p id="sqle" style="font-size: 12pt;"></p>
                </div>
            </div>
            <div id="d_cargar_hc_adm">
                <?php
					if ($id_paciente != "0" && $id_paciente != "") {
						$contenidoHtml->ver_historia($id_paciente);
					}
				?>
            </div>
        </div>
        <div id="d_guardar_precios" style="display:none;"></div>
        <div id="d_guardar_datos_mercadeo" style=""></div>
        <div id="d_imprimir_recibo" style="display:none;"></div>
        <?php
			if ($bol_confirma_bloqueo) {
		?>
        <div id="d_confirma_bloqueo" style="display:none;">
            <table class="datagrid" border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">
                <tr class="headegrid">
                    <th align="center" class="msg_alerta" style="border: 1px solid #fff;">
                        <h4>Es posible que otro usuario se encuentre realizando el registro de admisi&oacute;n del paciente seleccionado &iquest;Desea continuar con el registro de admisi&oacute;n?</h4>
                    </th>
                </tr>
                <tr>
                    <th align="center" style="width:5%;border: 1px solid #fff;">
                        <input type="button" value="S&iacute;" class="btnPrincipal" onclick="cerrar_div_centro();"/>
                        &nbsp;&nbsp;
                        <input type="button" value="No" class="btnPrincipal" onclick="cancelar_registro_admision();"/>
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
			
			$(function() {
				window.prettyPrint && prettyPrint();
				
				$("#txt_fecha_cirugia").fdatepicker({
					format: "dd/mm/yyyy"
				});
			});
		</script>
        <?php
			$contenidoHtml->footer();
			
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
