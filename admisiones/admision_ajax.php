<?php
header("Content-Type: text/xml; charset=UTF-8");

session_start();

require_once("../principal/ContenidoHtml.php");
require_once("../funciones/FuncionesPersona.php");
require_once("../funciones/Utilidades.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../funciones/LiquidadorPrecios.php");

require_once("../db/DbPacientes.php");
require_once("../db/DbDepMuni.php");
require_once("../db/DbAdmision.php");
require_once("../db/DbConvenios.php");
require_once("../db/DbPlanes.php");
require_once("../db/DbTiemposCitasProf.php");
require_once("../db/DbPrecios.php");
require_once("../db/DbTiposCitas.php");
require_once("../db/DbTiposCitasDetalle.php");
require_once("../db/DbUsuarios.php");
require_once("../db/DbPagos.php");
require_once("../db/DbListas.php");
require_once("../db/DbListasPrecios.php");
require_once("../db/DbEstadosAtencion.php");
require_once("../db/DbDisponibilidadProf.php");
require_once("../db/DbPaquetesProcedimientos.php");

$contenido = new ContenidoHtml();
$funcionesPersona = new FuncionesPersona();
$utilidades = new Utilidades();
$combo = new Combo_Box();
$liquidadorPrecios = new LiquidadorPrecios();

$dbPacientes = new DbPacientes();
$dbDepMuni = new DbDepMuni();
$dbAdmision = new DbAdmision();
$dbConvenios = new DbConvenios();
$dbPlanes = new DbPlanes();
$dbTiemposCitasProf = new DbTiemposCitasProf();
$dbPrecios = new DbPrecios();
$dbTiposCitas = new DbTiposCitas();
$dbTiposCitasDetalle = new DbTiposCitasDetalle();
$dbUsuarios = new DbUsuarios();
$dbPagos = new DbPagos();
$dbListas = new DbListas();
$dbListasPrecios = new DbListasPrecios();
$dbEstadosAtencion = new DbEstadosAtencion();
$dbDisponibilidadProf = new DbDisponibilidadProf();
$dbPaquetesProcedimientos = new DbPaquetesProcedimientos();

$contenido->validar_seguridad(1);

$id_menu = $utilidades->str_decode($_POST["hdd_numero_menu"]);
$opcion = $utilidades->str_decode($_POST["opcion"]);
$tipo_acceso_menu = $contenido->obtener_permisos_menu($id_menu);

//Lista de bilateralidades
$arr_bilateralidades = array();
$arr_bilateralidades[0]["id"] = 0;
$arr_bilateralidades[0]["valor"] = "No aplica";
$arr_bilateralidades[1]["id"] = 1;
$arr_bilateralidades[1]["valor"] = "Unilateral";
$arr_bilateralidades[2]["id"] = 2;
$arr_bilateralidades[2]["valor"] = "Bilateral";

switch ($opcion) {
    case "1": //Se obtienen los datos del paciente
        $hdd_id_consulta = $utilidades->str_decode($_POST["hdd_id_consulta"]);

        $pacientes_aux = $dbPacientes->getExistepaciente2($hdd_id_consulta);

        echo $pacientes_aux[0]["resultado"].":".$pacientes_aux[0]["numero_documento"].":".$funcionesPersona->obtenerNombreCompleto($pacientes_aux[0]["nombre_1"], $pacientes_aux[0]["nombre_2"], $pacientes_aux[0]["apellido_1"], $pacientes_aux[0]["apellido_2"]);
        break;

    case "2": //Carga el combo box de municipios
        @$cod_dep = $utilidades->str_decode($_POST["cod_dep"]);
        @$cod_mun_dane = $utilidades->str_decode($_POST["cod_mun_dane"]);
        @$sufijo = $utilidades->str_decode($_POST["sufijo"]);

        echo $combo->getComboDb("cmb_municipio".$sufijo, $cod_mun_dane, $dbDepMuni->getMunicipiosDepartamento($cod_dep), "cod_mun_dane, nom_mun", "Seleccione el municipio", "", "", "width: 188px;");
        break;

    case "3": //Crea la admision
        $in_id_usuario_crea = $_SESSION["idUsuario"];
        @$post = $utilidades->str_decode($_POST["post"]);
        @$in_nombre_1 = $utilidades->str_decode($_POST["txtnombre1"]);
        @$in_nombre_2 = isset($_POST["txtnombre2"]) ? $utilidades->str_decode($_POST["txtnombre2"]) : NULL;
        @$in_apellido_1 = $utilidades->str_decode($_POST["txtapellido1"]);
        @$in_apellido_2 = isset($_POST["txtapellido2"]) ? $utilidades->str_decode($_POST["txtapellido2"]) : NULL;
        @$in_id_tipo_documento = $utilidades->str_decode($_POST["cmbtipoid"]);
        @$in_numero_documento = trim($utilidades->str_decode($_POST["txtid"]));
        @$in_telefono_1 = $utilidades->str_decode($_POST["txttelefono"]);
        @$in_fecha_nacimiento = $utilidades->str_decode($_POST["txtfechanacimientoaux"]);
        @$in_id_pais_nac = $utilidades->str_decode($_POST["cmb_id_pais_nac"]);
        @$in_cod_dep_nac = $utilidades->str_decode($_POST["cmb_departamento_nac"]);
        @$in_cod_mun_nac = $utilidades->str_decode($_POST["cmb_municipio_nac"]);
        @$in_nom_dep_nac = $utilidades->str_decode($_POST["txt_nombre_dep_nac"]);
        @$in_nom_mun_nac = $utilidades->str_decode($_POST["txt_nombre_mun_nac"]);
        @$in_id_pais = $utilidades->str_decode($_POST["cmb_id_pais"]);
        @$in_cod_dep = $utilidades->str_decode($_POST["cmb_departamento"]);
        @$in_cod_mun = $utilidades->str_decode($_POST["cmb_municipio"]);
        @$in_nom_dep = $utilidades->str_decode($_POST["txt_nombre_dep"]);
        @$in_nom_mun = $utilidades->str_decode($_POST["txt_nombre_mun"]);
        @$in_direccion = $utilidades->str_decode($_POST["txtdireccion"]);
        @$in_sexo = $utilidades->str_decode($_POST["cmbsexo"]);
        @$in_ind_desplazado = $utilidades->str_decode($_POST["cmbdesplazado"]);
        @$in_id_etnia = $utilidades->str_decode($_POST["cmbetnia"]);
        @$in_id_zona = $utilidades->str_decode($_POST["cmbzona"]);
        @$in_id_estado_civil = $utilidades->str_decode($_POST["cmb_estado_civil"]);
        @$in_telefono_2 = isset($_POST["txttelefono2"]) ? $utilidades->str_decode($_POST["txttelefono2"]) : NULL;
        @$in_email = isset($_POST["txtemail"]) ? $utilidades->str_decode($_POST["txtemail"]) : NULL;
        @$in_id_paciente_cita = $utilidades->str_decode($_POST["hddidconsultaaux"]);
        @$in_id_cita = $utilidades->str_decode($_POST["id_cita"]);
        @$in_id_tipo_cita = $utilidades->str_decode($_POST["id_tipo_cita"]);
        @$in_hdd_paciente_existe = $utilidades->str_decode($_POST["hddpacienteexisteaux"]);
        @$in_cmbtiposangre = $utilidades->str_decode($_POST["cmbtiposangre"]);
        @$in_cmbfactorrh = $utilidades->str_decode($_POST["factorrh"]);
        @$in_cmb_lugar_cita = $utilidades->str_decode($_POST["cmb_lugar_cita"]);
        @$in_txtpresionarterial = $utilidades->str_decode($_POST["txtpresionarterial"]);
        @$in_txtpulso = $utilidades->str_decode($_POST["txtpulso"]);
        @$in_txt_nombre_cirugia = $utilidades->str_decode($_POST["txt_nombre_cirugia"]);
        @$in_txt_fecha_cirugia = $utilidades->str_decode($_POST["txt_fecha_cirugia"]);
        @$in_cmb_ojo = $utilidades->str_decode($_POST["cmb_ojo"]);
        @$in_cmb_num_cirugia = $utilidades->str_decode($_POST["cmb_num_cirugia"]);
        @$in_txt_nombre_med_orden = trim($utilidades->str_decode($_POST["txt_nombre_med_orden"]));
        @$in_cmbnumerohijos = $utilidades->str_decode($_POST["cmbnumerohijos"]);
        @$in_cmbnumerohijas = $utilidades->str_decode($_POST["cmbnumerohijas"]);
        @$in_cmbnumerohermanos = $utilidades->str_decode($_POST["cmbnumerohermanos"]);
        @$in_cmbnumerohermanas = $utilidades->str_decode($_POST["cmbnumerohermanas"]);
        @$in_txtacompanante = $utilidades->str_decode($_POST["txtacompanante"]);
        @$in_txtplan = $utilidades->str_decode($_POST["txtplan"]);
        @$cmb_convenio = $utilidades->str_decode($_POST["cmbconvenio"]);
        @$txtprofesion = $utilidades->str_decode($_POST["txtprofesion"]);
        @$txtmconsulta = trim($utilidades->str_decode($_POST["txtmconsulta"]));
        @$txt_observaciones_admision = trim($utilidades->str_decode($_POST["txt_observaciones_admision"]));
        @$cmb_medio_pago = $utilidades->str_decode($_POST["cmb_medio_pago"]);
        @$cmbprofesionalAtiende = $utilidades->str_decode($_POST["cmbprofesionalAtiende"]);
        @$txt_num_carnet = $utilidades->str_decode($_POST["txt_num_carnet"]);
		
		@$num_poliza = $utilidades->str_decode($_POST["num_poliza"]);
		@$num_mipress = $utilidades->str_decode($_POST["num_mipress"]);
		@$num_ent_mipress = $utilidades->str_decode($_POST["num_ent_mipress"]);
		
		@$tipo_coti_paciente = $utilidades->str_decode($_POST["tipo_coti_paciente"]);
		@$rango_paciente = $utilidades->str_decode($_POST["rango_paciente"]);
		@$foto_paciente = $utilidades->str_decode($_POST["foto_paciente"]);
        
        //Datos del formulario de mercadeo
		@$cmb_lista_merc = $utilidades->str_decode($_POST["cmb_lista_merc"]);
		@$cmb_subcategoria_merc = $utilidades->str_decode($_POST["cmb_subcategoria_merc"]);
		@$txt_remitido_merc = $utilidades->str_decode($_POST["txt_remitido_merc"]);
		@$txt_referido_merc = $utilidades->str_decode($_POST["txt_referido_merc"]);
		//El Id de la admision por defecto entra en 0, si es mayor que 0 es para editar
        @$hdd_id_admision_paciente = $utilidades->str_decode($_POST["hdd_id_admision_paciente"]);

        //Estado de la cita 
        @$hdd_estado_consulta = $utilidades->str_decode($_POST["hdd_estado_consulta"]);

        //Se buscan los datos del tipo de cita
        $tipo_cita_obj = $dbTiposCitas->get_tipo_cita($in_id_tipo_cita);
        $ind_preconsulta = "0";
        if (isset($tipo_cita_obj["ind_preconsulta"])) {
            $ind_preconsulta = $tipo_cita_obj["ind_preconsulta"];
        }

        @$cant_tipo_cita_det = intval($utilidades->str_decode($_POST["cant_tipo_cita_det"]), 10);
        $arr_usuarios_citas_det = array();
        for ($i = 0; $i < $cant_tipo_cita_det; $i++) {
            $arr_usuarios_citas_det[$i]["id_tipo_reg"] = $utilidades->str_decode($_POST["id_tipo_reg_cita_det_".$i]);
            $arr_usuarios_citas_det[$i]["id_estado_atencion"] = $utilidades->str_decode($_POST["id_estado_atencion_cita_det_".$i]);
            $arr_usuarios_citas_det[$i]["id_usuario_prof"] = $utilidades->str_decode($_POST["id_usuario_cita_det_".$i]);
        }

        @$cant_examenes = intval($utilidades->str_decode($_POST["cmb_cant_examenes"]), 10);
        $arr_examenes = array();
        for ($i = 0; $i < $cant_examenes; $i++) {
            $arr_examenes[$i]["id_examen"] = $utilidades->str_decode($_POST["id_examen_".$i]);
            $arr_examenes[$i]["id_ojo"] = $utilidades->str_decode($_POST["id_ojo_examen_".$i]);
        }
		
        $rta_aux = $dbAdmision->CrearEditarAdmision($in_nombre_1, $in_nombre_2, $in_apellido_1, $in_apellido_2, $in_id_tipo_documento, $in_numero_documento,
				$in_telefono_1, $in_telefono_2, $in_email, $in_fecha_nacimiento, $in_id_pais_nac, $in_cod_dep_nac, $in_cod_mun_nac, $in_nom_dep_nac,
				$in_nom_mun_nac, $in_id_pais, $in_cod_dep, $in_cod_mun, $in_nom_dep, $in_nom_mun, $in_direccion, $in_sexo, $in_ind_desplazado, $in_id_etnia,
				$in_id_zona, $in_id_paciente_cita, $in_id_cita, $in_id_tipo_cita, $ind_preconsulta, $in_cmb_lugar_cita, $in_hdd_paciente_existe, $in_txtplan,
				$in_txtpresionarterial, $in_txtpulso, $in_txt_nombre_cirugia, $in_txt_fecha_cirugia, $in_cmb_ojo, $in_cmb_num_cirugia,
				$in_txt_nombre_med_orden, $in_cmbtiposangre, $in_cmbnumerohijos, $in_cmbnumerohijas, $in_cmbnumerohermanos, $in_cmbnumerohermanas,
				$in_txtacompanante, $in_cmbfactorrh, $cmb_convenio, $txtprofesion, $txtmconsulta, $txt_observaciones_admision, $cmb_medio_pago, $post,
				$cmbprofesionalAtiende, $arr_usuarios_citas_det, $arr_examenes, $in_id_usuario_crea, $hdd_id_admision_paciente, $hdd_estado_consulta,
				$in_id_estado_civil, $txt_num_carnet, $tipo_coti_paciente, $rango_paciente,
				$foto_paciente,$num_poliza,$num_mipress,$num_ent_mipress);

		$rta_aux_merc = "";
		if($rta_aux>0){
			
			//INSERTAR LOS DATOS DE MERCADEO EN LA TABLA FORMULARIO MERCADEO
			if($txt_remitido_merc=="0" || $txt_referido_merc=="0"){
				$rta_aux_merc=1;
			}else{
				$rta_aux_merc = $dbAdmision->CrearEditarDatosMercadeo($cmb_lista_merc, $cmb_subcategoria_merc, $txt_remitido_merc, $txt_referido_merc, $rta_aux);
			}
		}
				
    ?>
    <input type="hidden" name="hdd_resultado_crear" id="hdd_resultado_crear" value="<?php echo($rta_aux); ?>" />
    <input type="hidden" name="hdd_resultado_crear_form_mercadeo" id="hdd_resultado_crear_form_mercadeo" value="<?php echo($rta_aux_merc); ?>" />
    <?php
        break;

    case "4": //Recibe las opciones del div de confirmación
        $txtidentificacioninterno = $utilidades->str_decode($_POST["txtidentificacioninterno"]);
        $pacientes2_aux = $dbPacientes->getBuscarpacientes($txtidentificacioninterno);
		
        ?>
        <div id="d_datagrid" style="width: 925px; margin: auto;">
            <script id="ajax">
                //<![CDATA[ 
                $(function() {
                    $(".paginated", "table").each(function(i) {
                        $(this).text(i + 1);
                    });

                    $("table.paginated").each(function() {
                        var currentPage = 0;
                        var numPerPage = 10;
                        var $table = $(this);
                        $table.bind("repaginate", function() {
                            $table.find("tbody tr").hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
                        });
                        $table.trigger("repaginate");
                        var numRows = $table.find("tbody tr").length;
                        var numPages = Math.ceil(numRows / numPerPage);
                        var $pager = $('<div class="pager"></div>');
                        for (var page = 0; page < numPages; page++) {
                            $('<span class="page-number"></span>').text(page + 1).bind("click", {
                                newPage: page
                            }, function(event) {
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
            <table class="paginated modal_table" style="width: 925px; margin: auto;">
                <thead>
                    <tr class="headegrid">
                        <th scope="col">Tipo de documento</th>
                        <th scope="col">N&uacute;mero de identificaci&oacute;n</th>
                        <th scope="col">Nombres</th>
                        <th scope="col">Apellidos</th>
                        <th scope="col">Pa&iacute;s</th>
                        <th scope="col">Departamento</th>
                        <th scope="col">Municipio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($pacientes2_aux) >= 1) {
                        foreach ($pacientes2_aux as $value) {
                            $id_paciente = $value["id_paciente"];

                            //Se buscan los datos de la última admisión
                            $ultima_admision = $dbAdmision->get_ultima_admision_tiempo($id_paciente, "M", 3);

                            $id_tipo_documento = $value["id_tipo_documento"];
                            $numero_documento = $value["numero_documento"];
                            $sexo = $value["sexo"];
                            $nombre_1 = $value["nombre_1"];
                            $nombre_2 = $value["nombre_2"];
                            $apellido_1 = $value["apellido_1"];
                            $apellido_2 = $value["apellido_2"];
                            $fecha_nacimiento_aux = $value["fecha_nacimiento_aux"];
                            $id_pais_nac = $value["id_pais_nac"];
                            $cod_dep_nac = $value["cod_dep_nac"];
                            $cod_mun_nac = $value["cod_mun_nac"];
                            $nom_dep_nac = $value["nom_dep_nac"];
                            $nom_mun_nac = $value["nom_mun_nac"];
                            $tipo_sangre = $value["tipo_sangre"];
                            $factor_rh = $value["factor_rh"];
                            $telefono_1 = $value["telefono_1"];
                            $telefono_2 = $value["telefono_2"];
                            $email = $value["email"];
                            $id_pais = $value["id_pais"];
                            $cod_dep = $value["cod_dep"];
                            $cod_mun = $value["cod_mun"];
                            $nom_dep = $value["nom_dep"];
                            $nom_mun = $value["nom_mun"];
                            $direccion = $value["direccion"];
                            $id_zona = $value["id_zona"];
                            $profesion = $value["profesion"];
                            $id_etnia = $value["id_etnia"];
                            $ind_desplazado = $value["ind_desplazado"];
							$id_convenio = $value["id_convenio_paciente"];
							
                            if (count($ultima_admision) > 0) {
                                $nombre_acompa = $ultima_admision["nombre_acompa"];
                                $numero_hijos = $ultima_admision["numero_hijos"];
                                $numero_hijas = $ultima_admision["numero_hijas"];
                                $numero_hermanos = $ultima_admision["numero_hermanos"];
                                $numero_hermanas = $ultima_admision["numero_hermanas"];
                                $presion_arterial = $ultima_admision["presion_arterial"];
                                $pulso = $ultima_admision["pulso"];
                                $motivo_consulta = $ultima_admision["motivo_consulta"];
                                //$id_convenio = $ultima_admision["id_convenio"];
								$id_formulario_mercadeo = $ultima_admision["formulario_mercadeo"];
								$id_categoria = $ultima_admision["categoria"];
								$id_sub_categoria = $ultima_admision["subcategoria"];
								$otro_mercadeo = $ultima_admision["otro"];
								$remitido_mercadeo = $ultima_admision["remitido"];
                            } else {
                                $nombre_acompa = "";
                                $numero_hijos = "";
                                $numero_hijas = "";
                                $numero_hermanos = "";
                                $numero_hermanas = "";
                                $presion_arterial = "";
                                $pulso = "";
                                $motivo_consulta = "";
								$id_formulario_mercadeo ="";
								$id_categoria = "";
								$id_sub_categoria = "";
								$otro_mercadeo = "";
								$remitido_mercadeo = "";
                                //$id_convenio = "";
                            }
                            ?>
                            <input type="hidden" name="hdd_id_paciente_<?php echo($id_paciente); ?>" id="hdd_id_paciente_<?php echo($id_paciente); ?>" value="<?php echo($id_paciente); ?>" />
                            <input type="hidden" name="hdd_id_tipo_documento_<?php echo($id_paciente); ?>" id="hdd_id_tipo_documento_<?php echo($id_paciente); ?>" value="<?php echo($id_tipo_documento); ?>" />
                            <input type="hidden" name="hdd_numero_documento_<?php echo($id_paciente); ?>" id="hdd_numero_documento_<?php echo($id_paciente); ?>" value="<?php echo($numero_documento); ?>" />
                            <input type="hidden" name="hdd_sexo_<?php echo($id_paciente); ?>" id="hdd_sexo_<?php echo($id_paciente); ?>" value="<?php echo($sexo); ?>" />
                            <input type="hidden" name="hdd_nombre_1_<?php echo($id_paciente); ?>" id="hdd_nombre_1_<?php echo($id_paciente); ?>" value="<?php echo($nombre_1); ?>" />
                            <input type="hidden" name="hdd_nombre_2_<?php echo($id_paciente); ?>" id="hdd_nombre_2_<?php echo($id_paciente); ?>" value="<?php echo($nombre_2); ?>" />
                            <input type="hidden" name="hdd_apellido_1_<?php echo($id_paciente); ?>" id="hdd_apellido_1_<?php echo($id_paciente); ?>" value="<?php echo($apellido_1); ?>" />
                            <input type="hidden" name="hdd_apellido_2_<?php echo($id_paciente); ?>" id="hdd_apellido_2_<?php echo($id_paciente); ?>" value="<?php echo($apellido_2); ?>" />
                            <input type="hidden" name="hdd_fecha_nacimiento_aux_<?php echo($id_paciente); ?>" id="hdd_fecha_nacimiento_aux_<?php echo($id_paciente); ?>" value="<?php echo($fecha_nacimiento_aux); ?>" />
                            <input type="hidden" name="hdd_id_pais_nac_<?php echo($id_paciente); ?>" id="hdd_id_pais_nac_<?php echo($id_paciente); ?>" value="<?php echo($id_pais_nac); ?>" />
                            <input type="hidden" name="hdd_cod_dep_nac_<?php echo($id_paciente); ?>" id="hdd_cod_dep_nac_<?php echo($id_paciente); ?>" value="<?php echo($cod_dep_nac); ?>" />
                            <input type="hidden" name="hdd_cod_mun_nac_<?php echo($id_paciente); ?>" id="hdd_cod_mun_nac_<?php echo($id_paciente); ?>" value="<?php echo($cod_mun_nac); ?>" />
                            <input type="hidden" name="hdd_nom_dep_nac_<?php echo($id_paciente); ?>" id="hdd_nom_dep_nac_<?php echo($id_paciente); ?>" value="<?php echo($nom_dep_nac); ?>" />
                            <input type="hidden" name="hdd_nom_mun_nac_<?php echo($id_paciente); ?>" id="hdd_nom_mun_nac_<?php echo($id_paciente); ?>" value="<?php echo($nom_mun_nac); ?>" />
                            <input type="hidden" name="hdd_tipo_sangre_<?php echo($id_paciente); ?>" id="hdd_tipo_sangre_<?php echo($id_paciente); ?>" value="<?php echo($tipo_sangre); ?>" />
                            <input type="hidden" name="hdd_factor_rh_<?php echo($id_paciente); ?>" id="hdd_factor_rh_<?php echo($id_paciente); ?>" value="<?php echo($factor_rh); ?>" />
                            <input type="hidden" name="hdd_telefono_1_<?php echo($id_paciente); ?>" id="hdd_telefono_1_<?php echo($id_paciente); ?>" value="<?php echo($telefono_1); ?>" />
                            <input type="hidden" name="hdd_telefono_2_<?php echo($id_paciente); ?>" id="hdd_telefono_2_<?php echo($id_paciente); ?>" value="<?php echo($telefono_2); ?>" />
                            <input type="hidden" name="hdd_email_<?php echo($id_paciente); ?>" id="hdd_email_<?php echo($id_paciente); ?>" value="<?php echo($email); ?>" />
                            <input type="hidden" name="hdd_id_pais_<?php echo($id_paciente); ?>" id="hdd_id_pais_<?php echo($id_paciente); ?>" value="<?php echo($id_pais); ?>" />
                            <input type="hidden" name="hdd_cod_dep_<?php echo($id_paciente); ?>" id="hdd_cod_dep_<?php echo($id_paciente); ?>" value="<?php echo($cod_dep); ?>" />
                            <input type="hidden" name="hdd_cod_mun_<?php echo($id_paciente); ?>" id="hdd_cod_mun_<?php echo($id_paciente); ?>" value="<?php echo($cod_mun); ?>" />							
                            <input type="hidden" name="hdd_nom_dep_<?php echo($id_paciente); ?>" id="hdd_nom_dep_<?php echo($id_paciente); ?>" value="<?php echo($nom_dep); ?>" />
                            <input type="hidden" name="hdd_nom_mun_<?php echo($id_paciente); ?>" id="hdd_nom_mun_<?php echo($id_paciente); ?>" value="<?php echo($nom_mun); ?>" />							
                            <input type="hidden" name="hdd_direccion_<?php echo($id_paciente); ?>" id="hdd_direccion_<?php echo($id_paciente); ?>" value="<?php echo($direccion); ?>" />
                            <input type="hidden" name="hdd_id_zona_<?php echo($id_paciente); ?>" id="hdd_id_zona_<?php echo($id_paciente); ?>" value="<?php echo($id_zona); ?>" />
                            <input type="hidden" name="hdd_profesion_<?php echo($id_paciente); ?>" id="hdd_profesion_<?php echo($id_paciente); ?>" value="<?php echo($profesion); ?>" />
                            <input type="hidden" name="hdd_ind_desplazado_<?php echo($id_paciente); ?>" id="hdd_ind_desplazado_<?php echo($id_paciente); ?>" value="<?php echo($ind_desplazado); ?>" />
                            <input type="hidden" name="hdd_id_etnia_<?php echo($id_paciente); ?>" id="hdd_id_etnia_<?php echo($id_paciente); ?>" value="<?php echo($id_etnia); ?>" />
                            <!-- Datos de la ultima admision-->
                            <input type="hidden" name="hdd_nombre_acompa_<?php echo($id_paciente); ?>" id="hdd_nombre_acompa_<?php echo($id_paciente); ?>" value="<?php echo($nombre_acompa); ?>" />
                            <input type="hidden" name="hdd_numero_hijos_<?php echo($id_paciente); ?>" id="hdd_numero_hijos_<?php echo($id_paciente); ?>" value="<?php echo($numero_hijos); ?>" />
                            <input type="hidden" name="hdd_numero_hijas_<?php echo($id_paciente); ?>" id="hdd_numero_hijas_<?php echo($id_paciente); ?>" value="<?php echo($numero_hijas); ?>" />
                            <input type="hidden" name="hdd_numero_hermanos_<?php echo($id_paciente); ?>" id="hdd_numero_hermanos_<?php echo($id_paciente); ?>" value="<?php echo($numero_hermanos); ?>" />
                            <input type="hidden" name="hdd_numero_hermanas_<?php echo($id_paciente); ?>" id="hdd_numero_hermanas_<?php echo($id_paciente); ?>" value="<?php echo($numero_hermanas); ?>" />
                            <input type="hidden" name="hdd_presion_arterial_<?php echo($id_paciente); ?>" id="hdd_presion_arterial_<?php echo($id_paciente); ?>" value="<?php echo($presion_arterial); ?>" />
                            <input type="hidden" name="hdd_pulso_<?php echo($id_paciente); ?>" id="hdd_pulso_<?php echo($id_paciente); ?>" value="<?php echo($pulso); ?>" />
                            <input type="hidden" name="hdd_motivo_consulta_<?php echo($id_paciente); ?>" id="hdd_motivo_consulta_<?php echo($id_paciente); ?>" value="<?php echo($motivo_consulta); ?>" />
                            <input type="hidden" name="hdd_id_convenio_<?php echo($id_paciente); ?>" id="hdd_id_convenio_<?php echo($id_paciente); ?>" value="<?php echo($id_convenio); ?>" />
                            
                             <!-- PARAMETRIZACIÓN PARA EL FORMULARIO DE MERCADEO --->
                            
                            <input type="hidden" name="hdd_id_formulario_mercadeo_<?php echo($id_paciente); ?>" id="hdd_id_formulario_mercadeo_<?php echo($id_paciente); ?>" value="<?php echo($id_formulario_mercadeo); ?>" />
                            <input type="hidden" name="hdd_id_categoria_<?php echo($id_paciente); ?>" id="hdd_id_categoria_<?php echo($id_paciente); ?>" value="<?php echo($id_categoria); ?>" />
                            <input type="hidden" name="hdd_id_subcategoria_<?php echo($id_paciente); ?>" id="hdd_id_subcategoria_<?php echo($id_paciente); ?>" value="<?php echo($id_sub_categoria); ?>" />
                            <input type="hidden" name="hdd_otro_<?php echo($id_paciente); ?>" id="hdd_otro_<?php echo($id_paciente); ?>" value="<?php echo($otro_mercadeo); ?>" />
                            <input type="hidden" name="hdd_remitido_<?php echo($id_paciente); ?>" id="hdd_remitido_<?php echo($id_paciente); ?>" value="<?php echo($remitido_mercadeo); ?>" />
                            <tr class="celdagrid" id="<?php echo $value["id_paciente"]; ?>" onclick="seleccionUsuario(<?php echo $value["id_paciente"]; ?>)">
                                <td class="td_reducido"><?php echo $value["nombre_detalle"]; ?></td>
                                <td class="td_reducido"><?php echo $value["numero_documento"]; ?></td>
                                <td class="td_reducido"><?php echo $funcionesPersona->obtenerNombreCompleto($value["nombre_1"], $value["nombre_2"], NULL, NULL); ?></td>
                                <td class="td_reducido"><?php echo $funcionesPersona->obtenerNombreCompleto(NULL, NULL, $value["apellido_1"], $value["apellido_2"]); ?></td>
                                <td class="td_reducido"><?php echo $value["nombre_pais"]; ?></td>
                                <td class="td_reducido"><?php echo strlen($value["nom_dep"]) > 0 ? $value["nom_dep"] : $value["nom_dep2"]; ?></td>
                                <td class="td_reducido"><?php echo strlen($value["nom_mun"]) > 0 ? $value["nom_mun"] : $value["nom_mun2"]; ?></td>
                            </tr>
                        	<?php
						}
					} else {
                    	?>
                        <tr class="celdagrid">
                            <td colspan="7" style="text-align: center;">
                                <p>No hay Resultado</p>
                            </td>
                        </tr>
                        <?php
                    }
                	?>
                </tbody>
            </table>
        </div>
        <?php
        break;

    case "5": //Carga el combo box de planes
        @$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
        @$id_plan = $utilidades->str_decode($_POST["id_plan"]);
        @$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
        @$modificar_valores = $utilidades->str_decode($_POST["modificar_valores"]);
		
        if ($modificar_valores == 1) {
            $modificar_valores_b = true;
        } else {
            $modificar_valores_b = false;
        }

        //Listado de planes del convenio
        $lista_planes = $dbPlanes->getListaPlanesActivos($id_convenio);
        echo $combo->getComboDb("cmb_plan", $id_plan, $lista_planes, "id_plan, nombre_plan", "Seleccione el Plan", "cargar_precios(".$modificar_valores.", 0);", $modificar_valores_b, "width: 188px;");
		
		//Se verifica el estado del paciente en el convenio seleccionado
		$paciente_obj = $dbPacientes->getPacienteEstadoConvenio($id_paciente, $id_convenio);
	?>
    <input type="hidden" id="hdd_status_convenio_paciente_b" name="hdd_status_convenio_paciente_b" value="<?= $paciente_obj["status_convenio"] ?>" />
    <input type="hidden" id="hdd_nombre_status_convenio_paciente_b" name="hdd_nombre_status_convenio_paciente_b" value="<?= $paciente_obj["PstatusC"] ?>" />
    <?php
        break;

    case "6": //carga el Tipo de Cita
        $idprofesional = $utilidades->str_decode($_POST["idprofesional"]);
        $id_tipo_cita_base = $utilidades->str_decode($_POST["id_tipo_cita_base"]);
        $modificar_valores = $utilidades->str_decode($_POST["modificar_valores"]);

        echo $combo->getComboDb("cmb_tipo_cita", $id_tipo_cita_base, $dbTiemposCitasProf->getTiemposcitasprofeAdmision($idprofesional), "id_tipo_cita, nombre_tipo_cita", "Seleccione el Tipo de Cita", "seleccionar_tipo_cita(this.value, ".$modificar_valores."); cargar_precios(".$modificar_valores.", 0);", true, "width: 188px;");
        break;

    case "7": //Verifica si el paciente existe
        $ndocumento = $utilidades->str_decode($_POST["ndocumento"]);
        $tipodocumento = $utilidades->str_decode($_POST["tipodocumento"]);
        $resultado = $dbPacientes->getPacienteNumeroDocumentoAndTipoDocumento($ndocumento, $tipodocumento);
		
        $value = "";
        if (isset($resultado["tipodocumento"])) {
            //Se buscan los datos de la admisión anterior
            $admision_obj = $dbAdmision->get_ultima_admision_tiempo($resultado["id_paciente"], "M", 3);
			
            $value = "1:".(count($admision_obj) > 0 ? $resultado["telefono_1"] : "").
                     ":".(count($admision_obj) > 0 ? $resultado["telefono_2"] : "").
                     ":".$resultado["email"].
                     ":".$resultado["telefono_1"].
                     ":".$resultado["tipodocumento"].
                     ":".$resultado["numero_documento"].
                     ":".$funcionesPersona->obtenerNombreCompleto($resultado["nombre_1"], "", $resultado["nombre_2"], "").
                     ":".$funcionesPersona->obtenerNombreCompleto($resultado["apellido_1"], "", $resultado["apellido_2"], "").
                     ":".$resultado["tipo_sangre"].
                     ":".$resultado["factor_rh"].
                     ":".$resultado["sexo"].
                     ":".$resultado["id_pais"].
                     ":".$resultado["cod_dep"].
                     ":".(count($admision_obj) > 0 ? $resultado["cod_mun"] : "").
                     ":".$resultado["nom_dep"].
                     ":".(count($admision_obj) > 0 ? $resultado["nom_mun"] : "").
                     ":".$resultado["fecha_nacimiento_aux"].
                     ":".(count($admision_obj) > 0 ? $resultado["direccion"] : "").
                     ":".$resultado["profesion"].
                     ":".(count($admision_obj) > 0 ? $resultado["id_zona"] : "").
                     ":".$resultado["ind_desplazado"].
                     ":".$resultado["id_etnia"].
                     ":".$resultado["nombre_1"].
                     ":".$resultado["nombre_2"].
                     ":".$resultado["apellido_1"].
                     ":".$resultado["apellido_2"];
			
            if (count($admision_obj) > 0) {
                $value .= ":".$admision_obj["numero_hijos"].
                          ":".$admision_obj["numero_hermanos"];
            } else {
                $value .= "::";
            }
            $value .= ":".(count($admision_obj) > 0 ? $resultado["id_estado_civil"] : "").
            		  ":".$resultado["id_pais_nac"].
                      ":".$resultado["cod_dep_nac"].
                      ":".$resultado["cod_mun_nac"].
                      ":".$resultado["nom_dep_nac"].
                      ":".$resultado["nom_mun_nac"].
                      ":".$resultado["id_paciente"];
            if (count($admision_obj) > 0) {
                $value .= ":".$admision_obj["numero_hijas"].
                          ":".$admision_obj["numero_hermanas"];
            } else {
                $value .= "::";
            }
			if(count($admision_obj) > 0){
				
				$value .= ":".$admision_obj["formulario_mercadeo"].
						  ":".$admision_obj["categoria"].
						  ":".$admision_obj["subcategoria"].
						  ":".$admision_obj["remitido"].
						  ":".$admision_obj["referido"]; 
			}else {
				$value .= "::";
			}
        }
        ?>
        <input type="hidden" name="hdd_datos_paciente" id="hdd_datos_paciente" value="<?php echo($value); ?>" />
        <?php
        break;
		
    case "8": //Dibuja el div de precios
		$id_usuario = $_SESSION["idUsuario"];
        @$tipoCita = $utilidades->str_decode($_POST["tipoCita"]);
        @$idPaciente = $utilidades->str_decode($_POST["id_paciente"]);
        @$idConvenio = $utilidades->str_decode($_POST["id_convenio"]);
        @$idPlan = $utilidades->str_decode($_POST["id_plan"]);
        @$cant_examenes = intval($utilidades->str_decode($_POST["cant_examenes"]), 10);
        @$id_admision_paciente = intval($_POST["id_admision_paciente"], 10);
        @$modificar_precios = $utilidades->str_decode($_POST["modificar_precios"]);
        @$medio_pago = $utilidades->str_decode($_POST["medio_pago"]);
        @$ind_inicial = intval($_POST["ind_inicial"], 10);
		@$tipo_coti_paciente = $utilidades->str_decode($_POST["tipo_coti_paciente"]);
		@$rango_paciente = $utilidades->str_decode($_POST["rango_paciente"]);
		@$ind_np_atencion = intval($_POST["ind_np_atencion"], 10);
        @$nombreConvenio = $utilidades->str_decode($_POST["nombreConvenio"]);
		
        $paciente_obj = $dbPacientes->getPacienteEstadoConvenio($idPaciente, $idConvenio);
        $plan_obj = $dbPlanes->getPlan($idPlan);
		
		$exento = $paciente_obj["exento_pamo_paciente"];
		if ($paciente_obj["id_convenio_paciente"] == $idConvenio) {
			$status_convenio = $paciente_obj["status_convenio"];
			$nombre_status_convenio = $paciente_obj["PstatusC"];
		} else {
			$status_convenio = "";
			$nombre_status_convenio = "No registrado/No aplica";
		}
        $medio_pago_obj = $dbListas->getDetalle($medio_pago);
        $bol_np = false;
        if ($ind_np_atencion == 1 || (isset($medio_pago_obj["codigo_detalle"]) && $medio_pago_obj["codigo_detalle"] == "99")) {
            $bol_np = true;
        }
		
		//Se verifica si el usuario tiene permisos para modificar la definición del pago
        $ind_modificar_pagos = 0;
        $lista_perfiles_usuario = $dbUsuarios->getListaPerfilUsuarios($id_usuario);
        if (count($lista_perfiles_usuario) > 0) {
            foreach ($lista_perfiles_usuario as $perfil_aux) {
                if ($perfil_aux["ind_modificar_pagos"] == "1") {
                    $ind_modificar_pagos = 1;
                }
            }
        }
		
        $arr_examenes = array();
        $arr_examenes_ojos = array();
        for ($i = 0; $i < $cant_examenes; $i++) {
            @$arr_examenes[$i] = intval($utilidades->str_decode($_POST["id_examen_".$i]), 10);
            @$arr_examenes_ojos[$arr_examenes[$i]] = intval($utilidades->str_decode($_POST["id_ojo_".$i]), 10);
        }
		
        if ($id_admision_paciente > 0 && $ind_inicial == 1) {
            $lista_precios = $dbPrecios->getPreciosAdmision($id_admision_paciente);
            if (count($lista_precios) == 0) {
                //Si no hay precios asignados a la admisión, se buscan en los listados por convenio y plan
                $lista_precios = $dbPrecios->getPrecios($tipoCita, $idPlan, $cant_examenes, $arr_examenes, 0);
            }
        } else {
            $lista_precios = $dbPrecios->getPrecios($tipoCita, $idPlan, $cant_examenes, $arr_examenes, 0);
        }
        ?>
        <div style="width:99%;">
        	<div id="d_liq_cx_cop_cm" style="display:block;"></div>
            <table id="tablaPrecios" class="modal_table">
                <tr id="tabla_encabezado">
                    <th style="width:23%;">Producto</th>
                    <th style="width:22%;">Convenio / Plan</th>
                    <th style="width:13%;">N&uacute;mero Autorizaci&oacute;n</th>
                    <th style="width:10%;">Tipo</th>
                    <th style="width:6%;">Cant.</th>
                    <th style="width:10%;" id="precio">Valor unitario</th>
                    <th style="width:10%;" id="precio">Cuota mod. / Copago</th>
                    <th style="width:6%;" id="icono">
                        <?php
	                        if ($modificar_precios == 1 && $status_convenio != "2") {
                        ?>
                        <div class="Add-icon full" onclick="preciosFlotantes();"></div>                    	
                        <?php
    	                    }
                        ?>
                    </th>
                </tr>
                <?php
					$cont_precios_mostrados = 0;
					$ind_precios_faltantes = false;
					if (count($lista_precios) > 0 && $status_convenio != "2") {
						$plan_obj = $dbPlanes->getPlan($idPlan);
						
						//Se hace la liquidación de cirugías
						$arr_procedimientos_cx = array();
						
						$cont_aux = 1;
						foreach ($lista_precios as $precio_aux) {
							if ($precio_aux["tipo_precio"] == "P" && ($precio_aux["id_plan"] == $idPlan || $precio_aux["id_plan"] == "")) {
								$arr_aux = array();
								$arr_aux["orden"] = $cont_aux;
								$arr_aux["cod_procedimiento"] = $precio_aux["cod_procedimiento"];
								$arr_aux["tipo_bilateral"] = $precio_aux["tipo_bilateral"];
								array_push($arr_procedimientos_cx, $arr_aux);
							}
							
							$cont_aux++;
						}
						
						//Caso de prueba, no borrar
						/*$arr_procedimientos_cx = array();
						$arr_procedimientos_cx[0] = array("orden" => 1, "cod_procedimiento" => "890201", "tipo_bilateral" => "0");
						$arr_procedimientos_cx[1] = array("orden" => 2, "cod_procedimiento" => "020201", "tipo_bilateral" => "0");
						$arr_procedimientos_cx[2] = array("orden" => 3, "cod_procedimiento" => "180100", "tipo_bilateral" => "0");
						$arr_procedimientos_cx[3] = array("orden" => 4, "cod_procedimiento" => "180200", "tipo_bilateral" => "0");
						$arr_procedimientos_cx[4] = array("orden" => 5, "cod_procedimiento" => "147402", "tipo_bilateral" => "1");
						$arr_procedimientos_cx[5] = array("orden" => 6, "cod_procedimiento" => "136504", "tipo_bilateral" => "1");
						$arr_procedimientos_cx[6] = array("orden" => 7, "cod_procedimiento" => "115301", "tipo_bilateral" => "2");
						$arr_procedimientos_cx[7] = array("orden" => 8, "cod_procedimiento" => "115302", "tipo_bilateral" => "1");
						$arr_procedimientos_cx[8] = array("orden" => 9, "cod_procedimiento" => "115306", "tipo_bilateral" => "1");
						$arr_procedimientos_cx[9] = array("orden" => 10, "cod_procedimiento" => "147401", "tipo_bilateral" => "2");
						$arr_procedimientos_cx[10] = array("orden" => 11, "cod_procedimiento" => "012501", "tipo_bilateral" => "0");
						$arr_procedimientos_cx[11] = array("orden" => 12, "cod_procedimiento" => "015101", "tipo_bilateral" => "0");*/
						
						$mapa_precios_cx = $liquidadorPrecios->liquidar_cirugias($idPlan, $arr_procedimientos_cx);
						/*var_dump($mapa_precios_cx);
						echo("<br /><br />");*/
						
						//Se actualizan los precios en el listado a mostrar
						for ($ii = 0; $ii < count($lista_precios); $ii++) {
							$precio_aux = $lista_precios[$ii];
							if (isset($mapa_precios_cx[$ii + 1])) {
								$precio_aux["valor"] = $mapa_precios_cx[$ii + 1]["valor_total"];
								$lista_precios[$ii] = $precio_aux;
							}
						}
						
						if ($plan_obj["ind_calc_cc"] == "1") {
							//Se hace la liquidación de cuotas moderadoras y copagos
							$arr_productos = array();
							
							$cont_aux = 1;
							//var_dump($lista_precios);
							foreach ($lista_precios as $precio_aux) {
								$arr_aux = array();
								$arr_aux["orden"] = $cont_aux;
								$arr_aux["tipo_precio"] = $precio_aux["tipo_precio"];
								switch ($precio_aux["tipo_precio"]) {
									case "P":
										$arr_aux["cod_producto"] = $precio_aux["cod_procedimiento"];
										break;
									case "M":
										$arr_aux["cod_producto"] = $precio_aux["cod_medicamento"];
										break;
									case "I":
										$arr_aux["cod_producto"] = $precio_aux["cod_insumo"];
										break;
								}
								$arr_aux["tipo_bilateral"] = $precio_aux["tipo_bilateral"];
								if ($precio_aux["id_plan"] != "") {
									$arr_aux["id_plan"] = $precio_aux["id_plan"];
								} else {
									$arr_aux["id_plan"] = $idPlan;
								}
								$arr_aux["valor"] = $precio_aux["valor"];
								$arr_aux["valor_cuota"] = $precio_aux["valor_cuota"];
								$arr_aux["cantidad"] = $precio_aux["cantidad"];
								array_push($arr_productos, $arr_aux);
								
								$cont_aux++;
							}			
							//Caso de prueba, no borrar
							/*$arr_productos = array();
							$arr_productos[0] = array("orden" => 1, "tipo_precio" => "P", "cod_producto" => "890202", "tipo_bilateral" => "0", "id_plan" => "1", "valor" => "17577", "valor_cuota" => "0", "cantidad" => "1");
							$arr_productos[1] = array("orden" => 2, "tipo_precio" => "M", "cod_producto" => "1", "tipo_bilateral" => "0", "id_plan" => "1", "valor" => "500", "valor_cuota" => "0", "cantidad" => "10");
							$arr_productos[2] = array("orden" => 3, "tipo_precio" => "I", "cod_producto" => "2", "tipo_bilateral" => "0", "id_plan" => "1", "valor" => "250000", "valor_cuota" => "0", "cantidad" => "1");
							$arr_productos[3] = array("orden" => 4, "tipo_precio" => "P", "cod_producto" => "952100", "tipo_bilateral" => "0", "id_plan" => "1", "valor" => "104075", "valor_cuota" => "0", "cantidad" => "1");
							$arr_productos[4] = array("orden" => 5, "tipo_precio" => "P", "cod_producto" => "950610", "tipo_bilateral" => "0", "id_plan" => "1", "valor" => "54198", "valor_cuota" => "0", "cantidad" => "1");
							$arr_productos[5] = array("orden" => 6, "tipo_precio" => "P", "cod_producto" => "952500", "tipo_bilateral" => "0", "id_plan" => "1", "valor" => "41581", "valor_cuota" => "0", "cantidad" => "1");
							$arr_productos[6] = array("orden" => 7, "tipo_precio" => "P", "cod_producto" => "147402", "tipo_bilateral" => "1", "id_plan" => "1", "valor" => "1199104", "valor_cuota" => "0", "cantidad" => "1");
							$arr_productos[7] = array("orden" => 8, "tipo_precio" => "P", "cod_producto" => "147401", "tipo_bilateral" => "2", "id_plan" => "1", "valor" => "928682", "valor_cuota" => "0", "cantidad" => "1");*/
							
							$mapa_precios_cop_cm = $liquidadorPrecios->liquidar_cop_cm($idPaciente, $arr_productos, $tipo_coti_paciente, $rango_paciente);
							
							echo("<br /><br />");
						}
						
						//Se actualizan los precios en el listado a mostrar
						for ($ii = 0; $ii < count($lista_precios); $ii++) {
							$precio_aux = $lista_precios[$ii];
							if (isset($mapa_precios_cop_cm[$ii + 1])) {
								$precio_aux["valor_cuota"] = $mapa_precios_cop_cm[$ii + 1]["valor_cuota"];
								$lista_precios[$ii] = $precio_aux;
							}
						}
						
						$p = 1;
						$valor_total = 0;
						$valor_cuota_total = 0;
						foreach ($lista_precios as $precio_aux) {
							//Se valida si es un examen
							$bol_mostrar = true;
							$id_examen_aux = intval($precio_aux["id_examen"], 10);
							if ($id_examen_aux > 0) {
								//dependiendo del ojo se define cual bilateralidad mostrar
								if ($precio_aux["tipo_bilateral"] == "1" && $arr_examenes_ojos[$id_examen_aux] == "81") {
									//Un solo ojo
									$bol_mostrar = false;
								}
								if ($precio_aux["tipo_bilateral"] == "2" && ($arr_examenes_ojos[$id_examen_aux] == "79" || $arr_examenes_ojos[$id_examen_aux] == "80")) {
									//Bilateral
									$bol_mostrar = false;
								}
							}
	
							if ($bol_mostrar) {
								$cont_precios_mostrados++;
								
								//Se busca el código del servicio
								$cod_servicio_aux = "";
								switch ($precio_aux["tipo_precio"]) {
									case "P":
										$cod_servicio_aux = $precio_aux["cod_procedimiento"];
										break;
									case "M":
										$cod_servicio_aux = $precio_aux["cod_medicamento"];
										break;
									case "I":
										$cod_servicio_aux = $precio_aux["cod_insumo"];
										break;
								}
				?>
                <tr id="tr_precios_<?php echo($p); ?>">
                    <td align="left" class="td_reducido">
                        <input type="hidden" name="hdd_id_pago_<?php echo($p); ?>" id="hdd_id_pago_<?php echo($p); ?>" value="<?php echo($precio_aux["id_pago"]); ?>" />
                        <input type="hidden" name="hdd_id_precio_<?php echo($p); ?>" id="hdd_id_precio_<?php echo($p); ?>" value="<?php echo($precio_aux["id_precio"] != "" ? $precio_aux["id_precio"] : "0"); ?>" />
                        <input type="hidden" name="hdd_tipo_precio_<?php echo($p); ?>" id="hdd_tipo_precio_<?php echo($p); ?>" value="<?php echo($precio_aux["tipo_precio"]); ?>" />
                        <input type="hidden" name="hdd_cod_servicio_<?php echo($p); ?>" id="hdd_cod_servicio_<?php echo($p); ?>" value="<?php echo($cod_servicio_aux); ?>" />
                        <input type="hidden" name="hdd_tipo_bilateral_<?php echo($p); ?>" id="hdd_tipo_bilateral_<?php echo($p); ?>" value="<?php echo($precio_aux["tipo_bilateral"]); ?>" />
                        <?php
							echo($precio_aux["nombre_procedimiento"]." (".$cod_servicio_aux.")");
						?>
                    </td>
                    <td align="left" class="td_reducido">
                        <?php
							$id_convenio_aux = $precio_aux["id_convenio"];
							if ($id_convenio_aux == "") {
								$id_convenio_aux = $idConvenio;
							}
							
							$id_plan_aux = $precio_aux["id_plan"];
							if ($id_plan_aux == "") {
								$id_plan_aux = $idPlan;
							}
							
							//Se busca el tipo de autorización del convenio
							$ind_num_aut_aux = "0";
							$ind_num_aut_obl_aux = "0";
							if ($id_convenio_aux != "") {
								$convenio_obj_aux = $dbConvenios->getConvenio($id_convenio_aux);
								if (isset($convenio_obj_aux["id_convenio"])) {
									$ind_num_aut_aux = $convenio_obj_aux["ind_num_aut"];
									$ind_num_aut_obl_aux = $convenio_obj_aux["ind_num_aut_obl"];
								}
							}
							
							//Se obtiene la lista de convenios
							$lista_convenios = $dbConvenios->getListaConveniosActivos();
							
							//Se obtiene la lista de planes
							$lista_planes = $dbPlanes->getListaPlanesActivos($id_convenio_aux);
						?>
                        <div id="d_convenio_pago_<?php echo($p); ?>" style="width:100%;">
                            <input type="hidden" id="hdd_ind_num_aut_<?php echo($p); ?>" value="<?php echo($ind_num_aut_aux); ?>" />
                            <input type="hidden" id="hdd_ind_num_aut_obl_<?php echo($p); ?>" value="<?php echo($ind_num_aut_obl_aux); ?>" />
                            <?php
								$combo->getComboDb("cmb_convenio_pago_".$p, $id_convenio_aux, $lista_convenios, "id_convenio, nombre_convenio", "--Seleccione el convenio--", "seleccionar_convenio_pago(".$p.", this.value, '');", $modificar_precios, "width:100%; margin:0 0 2px 0;");
							?>
                        </div>
                        <div id="d_plan_pago_<?php echo($p); ?>" style="width:100%;">
                            <?php
								$combo->getComboDb("cmb_plan_pago_".$p, $id_plan_aux, $lista_planes, "id_plan, nombre_plan", "--Seleccione el plan--", "actualizar_precio_det(".$p.", this.value);", $modificar_precios, "width:100%; margin:0 0 2px 0;");
							?>
                        </div>
                        <div id="d_plan_pago_sel_<?php echo($p); ?>" style="display:none;"></div>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_num_autorizacion_<?php echo($p); ?>" id="txt_num_autorizacion_<?php echo($p); ?>" value="<?php echo($precio_aux["num_autorizacion"]); ?>" class="no-margin" maxlength="20" onblur="trim_cadena(this);" <?php if ($modificar_precios != 1 || $ind_num_aut_aux != "1") { ?>disabled<?php } ?> />
                    </td>
                    <td align="center">
                    	<?php
							foreach ($arr_bilateralidades as $bilateral_aux) {
								if ($bilateral_aux["id"] == $precio_aux["tipo_bilateral"]) {
									echo($bilateral_aux["valor"]);
									break;
								}
							}
						?>
                    </td>
                    <td class="td_reducido">
                        <input type="text" name="txt_cant_precio_<?php echo($p); ?>" id="txt_cant_precio_<?php echo($p); ?>" value="<?php echo($precio_aux["cantidad"]); ?>" class="texto_centrado no-margin" maxlength="3" onkeypress="return solo_numeros(event, false);" onchange="recalcular_totales_liquidacion();" <?php if ($modificar_precios != 1) { ?>$disabled<?php } ?> />
                    </td>
                    <td class="td_reducido">
                        <?php
							if ($bol_np) {
								$valor_aux = "0";
							} else {
								
								$valor_aux = $precio_aux["valor"];
							}
							
							$valor_total += ($valor_aux * $precio_aux["cantidad"]);
							
						?>
                        <input type="hidden" name="hdd_tipo_pago_<?php echo($p); ?>" id="hdd_tipo_pago_<?php echo($p); ?>" value="<?php echo($precio_aux["ind_tipo_pago"]); ?>" />
                        <input type="text" name="txt_valor_pago_<?php echo($p); ?>" id="txt_valor_pago_<?php echo($p); ?>" value="<?php echo($valor_aux); ?>" class="texto_centrado no-margin" maxlength="9" onkeypress="return solo_numeros(event, false);" onchange="recalcular_totales_liquidacion();" <?php if ($modificar_precios != 1 || $ind_modificar_pagos != 1) { ?>disabled<?php } ?> />
                    </td>
                   
                    <td class="td_reducido">
                        <?php
							$valor_aux = $precio_aux["valor_cuota"];
							if ($valor_aux == "" || $bol_np) {
								$valor_aux = "0";
							}
							
							$valor_cuota_total += $valor_aux;
						?>
                        <input type="text" name="txt_valor_cuota_<?php echo($p); ?>" id="txt_valor_cuota_<?php echo($p); ?>" value="<?php echo($valor_aux); ?>" class="texto_centrado no-margin" maxlength="9" onkeypress="return solo_numeros(event, false);" onchange="recalcular_totales_liquidacion();" <?php if ($modificar_precios != 1 || $ind_modificar_pagos != 1 || $precio_aux["ind_tipo_pago"] != "1") { ?>disabled<?php } ?> />
                    </td>
                    <td style="width: 40px;">
                        <?php
							if ($modificar_precios == 1) {
						?>
                        <div class="Error-icon" onclick="borrarPrecio('<?php echo("tr_precios_".$p); ?>');"></div>
                        <?php
							}
						?>
                    </td>
                </tr>
                <?php

								$p++;
								//var_dump($precio_aux);
							}
						}
	
						//Si no se encontraron todos los precios se agrega una alerta
						if ($cont_precios_mostrados < ($cant_examenes + 1)) {
							$ind_precios_faltantes = true;
						}
					} else {
				?>
                <tr id="tr_precio_no_hallado">
                    <td colspan="8">No se hallaron resultados</td>
                </tr>
                <?php
                	}
                ?>
            </table>
            <input type="hidden" name="hdd_ult_index_precio" id="hdd_ult_index_precio" value="<?php echo($cont_precios_mostrados); ?>" />
            <br/>
            <br/>
            <div id="resumenLiquidacion" class="div-informacion-liquidacion">
                <h5>Resumen de liquidación</h5>
                <table>
                	<tr>
                    	<td class="col1">
                            <p>Convenio/Seguro: <span style="font-weight: bold;"><?= $nombreConvenio ?></span></p>
                            <p>Estado del Convenio/Seguro: <span style="font-weight: bold;"><?= $nombre_status_convenio ?></span></p>
                            <p>Plan: <span  style="font-weight: bold;"><?= $plan_obj["nombre_plan"].($plan_obj["ind_tipo_pago"] == 1 ? " (Con cuota moderadora)" : "") ?></span></p>
                        </td>
                        <td class="col2">
                            <p>&iquest;Paciente exento cuota moderadora?: <span style="font-weight: bold;"><?= $exento == 1 ? "No" : "S&iacute;" ?></span></p>
                            <p>Total: <span id="sp_valor_total" name="sp_valor_total" class="verde"><?php echo("$".str_replace(",", ".", number_format($valor_total))); ?></span></p>
                            <p>Total cuota moderadora/copago: <span id="sp_cuota_mod" name="sp_cuota_mod" class="verde"><?php echo("$".str_replace(",", ".", number_format($valor_cuota_total))); ?></span></p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
			if ($ind_precios_faltantes) {
		?>
        <div class="contenedor_advertencia" style="display:block;">
            No se encontr&oacute; precio para todos los conceptos.
        </div>
        <?php
			}
		break;
		
	case "9": //Dibuja el div Flotante de Todos los precios
        ?>
        <div class="encabezado">
            <h3>Listado de precios</h3>
        </div>
        <br/>
        <form id="frmBuscarPrecios">
            <table>
                <tr>
                    <td>
                        <input type="text" id="txt_identificacion_interno" name="txt_identificacion_interno" placeholder="Digite el c&oacute;digo o nombre del producto o servicio" style="width:500px;" />
                    </td>
                    <td>
                        <input type="submit" value="Buscar" class="btnPrincipal peq" onclick="validarFormularioPrecios();" />
                    </td>
                </tr>
            </table>
        </form>
        <br/>
        <div style="clear: both;"></div>
        <br/>
        <div id="rPrecios"></div>
        <?php
        break;

    case "10": //Dibuja el div Flotante de Todos los precios
		$id_usuario = $_SESSION["idUsuario"];
        $id_plan = $utilidades->str_decode($_POST["id_plan"]);
        $parametro = $utilidades->str_decode($_POST["parametro"]);
        $aux_precios = $dbPrecios->getTodosLosPrecios($id_plan, $parametro, 1);
        $aux_precios_otros = $dbPrecios->getListaPreciosOtrosPlanes($id_plan, $parametro, 1);
		$aux_paquetes = $dbPaquetesProcedimientos->buscarPaquetes($parametro, 1);
		//Se agregan los paquetes a la lista
	
		if (count($aux_paquetes) > 0) {
			foreach ($aux_paquetes as $paquete_aux) {
				$pecio_aux = array();
				$pecio_aux["id_precio"] = "0";
				$pecio_aux["tipo_precio"] = "Q";
				$pecio_aux["id_paquete_p"] = $paquete_aux["id_paquete_p"];
				$pecio_aux["nombre_procedimiento"] = $paquete_aux["nom_paquete_p"];
				$pecio_aux["tipo_bilateral"] = "0";
				$pecio_aux["ind_tipo_pago"] = "1";
				$pecio_aux["valor"] = "0";
				$pecio_aux["valor_cuota"] = "0";
				$pecio_aux["precio_paquete"] = $paquete_aux["precio_paquete"];
				array_push($aux_precios, $pecio_aux);	
			}
		}
			
        //Se agregan los productos no existentes a la lista
	
        if (count($aux_precios_otros) > 0) {
            foreach ($aux_precios_otros as $otro_aux) {
                $ind_hallado_aux = false;
                if (count($aux_precios) > 0) {
                    foreach ($aux_precios as $precio_aux) {
						
                        if ((($precio_aux["cod_procedimiento"] != "" && $precio_aux["cod_procedimiento"] == $otro_aux["cod_procedimiento"]) ||
                                ($precio_aux["cod_medicamento"] != "" && $precio_aux["cod_medicamento"] == $otro_aux["cod_medicamento"]) ||
                                ($precio_aux["cod_insumo"] != "" && $precio_aux["cod_insumo"] == $otro_aux["cod_insumo"])) &&
                                $precio_aux["tipo_bilateral"] == $otro_aux["tipo_bilateral"]) {
                            $ind_hallado_aux = true;
                            break;
                        }
                    }
                }
				
                if (!$ind_hallado_aux) {
                    array_push($aux_precios, $otro_aux);
                }
            }
        }
		
		
        if (count($aux_precios) > 0) {
            ?>
            <div style="max-height:280px; overflow:auto;">
                <table class="modal_table" id="tablaPreciosModal" style="width:99%;" align="center">
                    <tr id="tabla_encabezado">
                        <th style="width:12%;" id="icono">Seleccionar</th>
                        <th style="width:58%;">Producto</th>
                        <th style="width:15%;">Tipo de valor</th>
                        <th style="width:15%;" id="precio">Precio</th>
                    </tr>
					<?php
						$contador = 1;
						foreach ($aux_precios as $value) {
							
							$cod_servicio_aux = "";
							switch ($value["tipo_precio"]) {
								case "P":
									$cod_servicio_aux = $value["cod_procedimiento"];
									break;
								case "M":
									$cod_servicio_aux = $value["cod_medicamento"];
									break;
								case "I":
									$cod_servicio_aux = $value["cod_insumo"];
									break;
								case "Q":
									$cod_servicio_aux = $value["id_paquete_p"];
									break;
							}
					?>
                    <tr id="<?php echo("b".$contador); ?>">
                        <td align="center" valign="middle">
                            <input type="checkbox" name="hdd_sel_proc_b_<?php echo($contador); ?>" id="hdd_sel_proc_b_<?php echo($contador); ?>" value="<?php echo($value["id_precio"]); ?>" class="input_sin_margen" />
                        </td>
                        <td align="left">
							<?php
						
								if ($value["tipo_precio"] == "Q") {
									//Se buscan los precios del detalle del paquete en el plan
									$lista_precios_paquete_det = $dbListasPrecios->getListaPreciosPaquetePlan($value["id_paquete_p"], $id_plan);
									$cont_aux_p = 0;
									$cod_servicio_aux_ant = "";
									foreach ($lista_precios_paquete_det as $prod_aux) {
										$cod_servicio_b_det = "";
										$nombre_servicio_b_det = "";
										switch ($prod_aux["tipo_producto"]) {
											case "P":
												$cod_servicio_b_det = $prod_aux["cod_procedimiento"];
												$nombre_servicio_b_det = $prod_aux["nombre_procedimiento"];
												break;
											case "I":
												$cod_servicio_b_det = $prod_aux["cod_insumo"];
												$nombre_servicio_b_det = $prod_aux["nombre_insumo"];
												break;
										}
										if ($cod_servicio_b_det != $cod_servicio_aux_ant) {
											$nombre_bilateral_aux = "";
											foreach ($arr_bilateralidades as $bilateral_aux) {
												if ($bilateral_aux["id"] == $prod_aux["ind_bilateralidad"]) {
														$nombre_bilateral_aux = $bilateral_aux["valor"];
													break;
												}
											}
											//var_dump($prod_aux);
								
							?>
                            <input type="hidden" name="hdd_id_precio_b_det_<?= $contador."_".$cont_aux_p ?>" id="hdd_id_precio_b_det_<?= $contador."_".$cont_aux_p ?>" value="<?= $prod_aux["id_precio"] ?>" />
                            <input type="hidden" name="hdd_tipo_precio_b_det_<?= $contador."_".$cont_aux_p ?>" id="hdd_tipo_precio_b_det_<?= $contador."_".$cont_aux_p ?>" value="<?= $prod_aux["tipo_producto"] ?>" />
                            <input type="hidden" name="hdd_cod_servicio_b_det_<?= $contador."_".$cont_aux_p ?>" id="hdd_cod_servicio_b_det_<?= $contador."_".$cont_aux_p ?>" value="<?= $cod_servicio_b_det ?>" />
                            <input type="hidden" name="hdd_nombre_servicio_b_det_<?= $contador."_".$cont_aux_p ?>" id="hdd_nombre_servicio_b_det_<?= $contador."_".$cont_aux_p ?>" value="<?= $nombre_servicio_b_det ?>" />
                            <input type="hidden" name="hdd_tipo_bilateral_b_det_<?= $contador."_".$cont_aux_p ?>" id="hdd_tipo_bilateral_b_det_<?= $contador."_".$cont_aux_p ?>" value="<?= $prod_aux["tipo_bilateral"] ?>" />
                            <input type="hidden" name="hdd_nombre_bilateral_b_det_<?= $contador."_".$cont_aux_p ?>" id="hdd_nombre_bilateral_b_det_<?= $contador."_".$cont_aux_p ?>" value="<?= $nombre_bilateral_aux ?>" />
                            <input type="hidden" name="hdd_ind_tipo_pago_b_det_<?= $contador."_".$cont_aux_p ?>" id="hdd_ind_tipo_pago_b_det_<?= $contador."_".$cont_aux_p ?>" value="<?= $prod_aux["ind_tipo_pago"] ?>" />
                            <input type="hidden" name="hdd_valor_b_det_<?= $contador."_".$cont_aux_p ?>" id="hdd_valor_b_det_<?= $contador."_".$cont_aux_p ?>" value="<?= $prod_aux["valor"] ?>" />
                            <input type="hidden" name="hdd_valor_cuota_b_det_<?= $contador."_".$cont_aux_p ?>" id="hdd_valor_cuota_b_det_<?= $contador."_".$cont_aux_p ?>" value="<?= $prod_aux["valor_cuota"] ?>" />
							<?php
								$cont_aux_p++;
									}
									$cod_servicio_aux_ant = $cod_servicio_b_det;
								}
							?>
                            <input type="hidden" name="hdd_cantidad_b_det_<?= $contador ?>" id="hdd_cantidad_b_det_<?= $contador ?>" value="<?= $cont_aux_p ?>" />
                            <span class="activo">[Paquete]</span>&nbsp;
                            <?php
								} else if ($value["id_precio"] == "") {
							?>
                            <span class="inactivo">[No configurado]</span>&nbsp;
                            <?php
								}
								echo($value["nombre_procedimiento"]);
							?>
                        </td>
                        <td align="center">
                        <?php
							$nombre_bilateral_aux = "";
							foreach ($arr_bilateralidades as $bilateral_aux) {
								if ($bilateral_aux["id"] == $value["tipo_bilateral"]) {
									$nombre_bilateral_aux = $bilateral_aux["valor"];
									break;
								}
							}
							
							echo($nombre_bilateral_aux);
						?>
                        </td>
                        <?php
							//Dependiendo del tipo de plan se determina el precio que se muestra
							$precio_aux = $value["valor_cuota"];
							if ($precio_aux == "") {
								$precio_aux = "0";
							}
						
						?>
                        
                        <td align="center">
                            <input type="hidden" id="hdd_id_precio_b_<?php echo($contador); ?>" value="<?php echo($value["id_precio"]); ?>" />
                            <input type="hidden" id="hdd_tipo_precio_b_<?php echo($contador); ?>" value="<?php echo($value["tipo_precio"]); ?>" />
                            <input type="hidden" id="hdd_cod_servicio_b_<?php echo($contador); ?>" value="<?php echo($cod_servicio_aux); ?>" />
                            <input type="hidden" id="hdd_nombre_proc_b_<?php echo($contador); ?>" value="<?php echo($value["nombre_procedimiento"]); ?>" />
                            <input type="hidden" id="hdd_tipo_bilateral_b_<?php echo($contador); ?>" value="<?php echo($value["tipo_bilateral"]); ?>" />
                            <input type="hidden" id="hdd_nombre_bilateral_b_<?php echo($contador); ?>" value="<?php echo($nombre_bilateral_aux); ?>" />
                            <input type="hidden" id="hdd_tipo_pago_b_<?php echo($contador); ?>" value="<?php echo($value["ind_tipo_pago"]); ?>" />
                            <input type="hidden" id="hdd_precio_b_<?php echo($contador); ?>" value="<?php echo($value["valor"]); ?>" />
                            <input type="hidden" id="hdd_precio_cuota_b_<?php echo($contador); ?>" value="<?php echo($precio_aux); ?>" />
                            <input type="hidden" id="hdd_ind_tipo_pago_b_<?php echo($contador); ?>" value="<?php echo($value["ind_tipo_pago"]); ?>" />
                              <?php 
							if($value["tipo_precio"] == "Q"){
								echo("$".number_format($value["precio_paquete"]));	
							}else{
								echo("$".number_format($value["valor"]));
							}
							?>
                        </td>
                    </tr>
                    <?php
						$contador++;
					}
					?>
                </table>
            </div>
            <br/>
            <?php
				//Se verifica si el usuario tiene permisos para modificar la definición del pago
				$ind_modificar_pagos = 0;
				$lista_perfiles_usuario = $dbUsuarios->getListaPerfilUsuarios($id_usuario);
				if (count($lista_perfiles_usuario) > 0) {
					foreach ($lista_perfiles_usuario as $perfil_aux) {
						if ($perfil_aux["ind_modificar_pagos"] == "1") {
							$ind_modificar_pagos = 1;
						}
					}
				}
			?>
            <input id="btnModalAgregar" name="btnModalAgregar" class="btnPrincipal" type="button" value="Agregar" onclick="agregarPrecios(<?= $ind_modificar_pagos ?>);" />
            <?php
        } else {
            ?>
            <table class="modal_table" id="tablaPreciosModal" >
                <tr id="tabla_encabezado">
                    <th>Producto</th>
                    <th id="precio">Precio</th>
                    <th id="icono"></th>
                </tr>
                <tr>
                    <td colspan="3">No hay resultados</td>
                </tr>
            </table>
            <?php
        }
        break;

    case "11": //Guardar lista de precios en tabla temporal
        $idUsuario = $_SESSION["idUsuario"];
        $dbAdmision->deleteTemporalPagosDetalle($idUsuario);
		
        @$id_medio_pago = $utilidades->str_decode($_POST["id_medio_pago"]);

        //Se verifica si el medio de pago es NP
        $medio_pago_obj = $dbListas->getDetalle($id_medio_pago);
        $bol_np = false;
        if (isset($medio_pago_obj["codigo_detalle"]) && $medio_pago_obj["codigo_detalle"] == "99") {
            $bol_np = true;
        }

        @$cantPrecios = intval($_POST["cant_precios"], 10);
		//var_dump($cantPrecios);
        $resultado_aux = 1;
        for ($i = 0; $i < $cantPrecios; $i++) {
            @$idListaPrecios = $utilidades->str_decode($_POST["idListaPrecios_".$i]);
            @$idConvenioPago = $utilidades->str_decode($_POST["id_convenio_pago_".$i]);
            @$idPlanPago = $utilidades->str_decode($_POST["id_plan_pago_".$i]);
            @$numAutorizacion = $utilidades->str_decode($_POST["num_autorizacion_".$i]);
            @$cantidad = $utilidades->str_decode($_POST["cantidad_".$i]);
            @$tipoPrecio = $utilidades->str_decode($_POST["tipoPrecio_".$i]);
            @$codServicio = $utilidades->str_decode($_POST["cod_servicio_".$i]);
            @$tipoBilateral = $utilidades->str_decode($_POST["tipoBilateral_".$i]);
            if ($bol_np) {
                @$valor = "0";
                @$valorCuota = "0";
            } else {
                @$valor = $utilidades->str_decode($_POST["valor_".$i]);
                @$valorCuota = $utilidades->str_decode($_POST["valor_cuota_".$i]);
            }
            @$indTipoPago = $utilidades->str_decode($_POST["indTipoPago_".$i]);

            $cod_procedimiento = "";
            $cod_medicamento = "";
            $cod_insumo = "";
            switch ($tipoPrecio) {
                case "P":
                    $cod_procedimiento = $codServicio;
                    break;
                case "M":
                    $cod_medicamento = $codServicio;
                    break;
                case "I":
                    $cod_insumo = $codServicio;
                    break;
            }

            $resultado_aux = $dbAdmision->guardarTemporalPagosDetalle($idUsuario, $idListaPrecios, $idConvenioPago, $idPlanPago, $numAutorizacion, $tipoBilateral,
							 $cantidad, $tipoPrecio, $valor, $indTipoPago, $valorCuota, $cod_procedimiento, $cod_medicamento, $cod_insumo);

            if ($resultado_aux <= 0) {
                break;
            }
        }
	?>
    <input type="hidden" id="hdd_tmp_pagos_detalle" value="<?php echo($resultado_aux); ?>" />
    <?php
        break;

    case "12": //Listado de usuarios por detalle de tipo de cita
        @$id_tipo_cita = intval($utilidades->str_decode($_POST["id_tipo_cita"]), 10);
        @$id_usuario_prof = intval($utilidades->str_decode($_POST["id_usuario_prof"]), 10);

        $id_admision = "0";
        if (isset($_POST["id_admision"])) {
            $id_admision = $utilidades->str_decode($_POST["id_admision"]);
        }

        $estado_consulta = 0;
        $orden_estado = 0;
        if (isset($_POST["estado_consulta"])) {
            $estado_consulta = $_POST["estado_consulta"];
            $estado_atencion_obj = $dbEstadosAtencion->getEstadoAtencion($estado_consulta);
            if (isset($estado_atencion_obj["orden"])) {
                $orden_estado = intval($estado_atencion_obj["orden"]);
            }
        }

        //Se obtiene el listado de detalle del tipo de cita
        $lista_detalle = $dbTiposCitasDetalle->get_lista_tipos_citas_detalles_usuarios_prof($id_admision, $id_tipo_cita, "1");
	?>
    <input type="hidden" name="hdd_cant_tipo_cita_det" id="hdd_cant_tipo_cita_det" value="<?php echo(count($lista_detalle)); ?>" />
    <?php
        if (count($lista_detalle) > 0) {
	?>
    <table class="modal_table">
        <tr>
            <th align="center" style="width:50%;">Registro</th>
            <th align="center" style="width:50%;">Profesional</th>
        </tr>
        <?php
			for ($i = 0; $i < count($lista_detalle); $i++) {
				$disabled = "disabled";
				$detalle_aux = $lista_detalle[$i];
				$id_estado_atencion = $detalle_aux["id_estado_atencion"];
				if ($orden_estado <= $detalle_aux["orden_estado_atencion"]) {
					$disabled = "";
				}
				
				//Se buscar los usuarios que pueden atender el tipo de cita
				$lista_usuarios = $dbUsuarios->getListaUsuariosTipoCitaDet($id_tipo_cita, $detalle_aux["id_tipo_reg"], $id_usuario_prof);
		?>
        <tr>
            <td align="left" class="td_reducido">
                <input type="hidden" name="hdd_tipo_reg_cita_det_<?php echo($i); ?>" id="hdd_tipo_reg_cita_det_<?php echo($i); ?>" value="<?php echo($detalle_aux["id_tipo_reg"]); ?>" />
                <input type="hidden" name="hdd_estado_atencion_cita_det_<?php echo($i); ?>" id="hdd_estado_atencion_cita_det_<?php echo($i); ?>" value="<?php echo($detalle_aux["id_estado_atencion"]); ?>" />
                <?php echo($detalle_aux["nombre_tipo_reg"]); ?>*
            </td>
            <td align="left" class="td_reducido">
                <select <?php echo($disabled); ?>  name="cmb_usuario_cita_det_<?php echo($i); ?>" id="cmb_usuario_cita_det_<?php echo($i); ?>" style="width:220px;">
                    <option value="">Seleccione el profesional</option>
                    <?php
						$lugar_disp_ant = "";
						for ($j = 0; $j < count($lista_usuarios); $j++) {
							$usuario_aux = $lista_usuarios[$j];
							
							//Se verifica si hay que crear un nuevo grupo por lugar
							if ($usuario_aux["lugar_disp"] != $lugar_disp_ant && $usuario_aux["lugar_disp"] != "") {
					?>
                    <optgroup label="<?php echo($usuario_aux["lugar_disp"]); ?>"></optgroup>
                    <?php
							}
							
							$selected_aux = "";
							if ($usuario_aux["id_usuario"] == $detalle_aux["id_usuario_prof"] || ($detalle_aux["id_usuario_prof"] == "" && $usuario_aux["id_usuario"] == $id_usuario_prof)) {
								$selected_aux = ' selected="selected"';
							}
					?>
                    <option value="<?php echo($usuario_aux["id_usuario"]); ?>"<?php echo($selected_aux); ?>><?php echo($usuario_aux["nombre_completo"]); ?></option>
                    <?php
							$lugar_disp_ant = $usuario_aux["lugar_disp"];
						}
					?>
                </select>
            </td>
        </tr>
        <?php
			}
		?>
    </table>
    <br />
    <?php
        }
        break;

    case "13": //Busca el código DANE del departamento y municipio dado el código del municipio (registraduría)
        @$cod_mun_reg_aux = $utilidades->str_decode($_POST["cod_mun_reg"]);
        $bol_hallado_aux = false;
        $cod_dep = "";
        $cod_mun_dane = "";

        if (substr($cod_mun_reg_aux, 5, 1) == "0") {
            $cod_mun_reg = substr($cod_mun_reg_aux, 0, 5);
            $municipio_obj = $dbDepMuni->getMunicipioReg($cod_mun_reg);

            if (isset($municipio_obj["cod_mun_dane"])) {
                $cod_dep = $municipio_obj["cod_dep"];
                $cod_mun_dane = $municipio_obj["cod_mun_dane"];
                $bol_hallado_aux = true;
            }
        }

        if (substr($cod_mun_reg_aux, 0, 1) == "0" && !$bol_hallado_aux) {
            $cod_mun_reg = substr($cod_mun_reg_aux, 1, 5);
            $municipio_obj = $dbDepMuni->getMunicipioReg($cod_mun_reg);

            if (isset($municipio_obj["cod_mun_dane"])) {
                $cod_dep = $municipio_obj["cod_dep"];
                $cod_mun_dane = $municipio_obj["cod_mun_dane"];
                $bol_hallado_aux = true;
            }
        }
	?>
    <input type="hidden" id="hdd_cod_dep_b" value="<?php echo($cod_dep); ?>" />
    <input type="hidden" id="hdd_cod_mun_dane_b" value="<?php echo($cod_mun_dane); ?>" />
    <?php
        break;

    case "14": //Cargar el componente de historia clínica para un paciente dado
        @$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
        if ($id_paciente != "0" && $id_paciente != "") {
            $contenido->ver_historia($id_paciente);
        }
        break;

    case "15": //Combo de convenios para precios
        @$indice = $utilidades->str_decode($_POST["indice"]);
        @$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);

        //Se obtiene la lista de planes
        $lista_convenios = $dbConvenios->getListaConveniosActivos();
	?>
    <input type="hidden" id="hdd_ind_num_aut_<?php echo($indice); ?>" value="0" />
    <input type="hidden" id="hdd_ind_num_aut_obl_<?php echo($indice); ?>" value="0" />
    <?php
        $combo->getComboDb("cmb_convenio_pago_".$indice, $id_convenio, $lista_convenios, "id_convenio, nombre_convenio", "--Seleccione el convenio--", "seleccionar_convenio_pago(".$indice.", this.value, '');", true, "width:100%; margin:0 0 2px 0;");
        break;

    case "16": //Combo de planes para precios
        @$indice = $utilidades->str_decode($_POST["indice"]);
        @$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
        @$id_plan = $utilidades->str_decode($_POST["id_plan"]);

        //Se obtiene la lista de planes
        $lista_planes = $dbPlanes->getListaPlanesActivos($id_convenio);

        $combo->getComboDb("cmb_plan_pago_".$indice, $id_plan, $lista_planes, "id_plan, nombre_plan", "--Seleccione el plan--", "actualizar_precio_det(".$indice.", this.value);", true, "width:100%; margin:0 0 2px 0;");

        //Se obtiene la configuración de autorizaciones del convenio
        $convenio_obj = $dbConvenios->getConvenio($id_convenio);
        $ind_num_aut = "0";
        $ind_num_aut_obl = "0";
        if (isset($convenio_obj["id_convenio"])) {
            $ind_num_aut = $convenio_obj["ind_num_aut"];
            $ind_num_aut_obl = $convenio_obj["ind_num_aut_obl"];
        }
	?>
    <input type="hidden" id="hdd_ind_num_aut_aux_<?php echo($indice); ?>" value="<?php echo($ind_num_aut); ?>" />
    <input type="hidden" id="hdd_ind_num_aut_obl_aux_<?php echo($indice); ?>" value="<?php echo($ind_num_aut_obl); ?>" />
    <?php
        break;

    case "17": //Buscar valores de un plan seleccionado para precios
        @$indice = $utilidades->str_decode($_POST["indice"]);
        @$id_plan = $utilidades->str_decode($_POST["id_plan"]);
        @$cod_servicio = $utilidades->str_decode($_POST["cod_servicio"]);
        @$tipo_precio = $utilidades->str_decode($_POST["tipo_precio"]);
        @$tipo_bilateral = $utilidades->str_decode($_POST["tipo_bilateral"]);

        //Si hay un plan seleccionado, se busca en el plan el nuevo precio
        if ($id_plan != "" && $id_plan > 0) {
            //Se obtienen los datos del plan
            $plan_obj = $dbPlanes->getPlan($id_plan);

            $ind_tipo_pago_aux = "";
            if (isset($plan_obj["id_plan"])) {
                $ind_tipo_pago_aux = $plan_obj["ind_tipo_pago"];

                //Se busca dentro del plan un precio para el producto
                $precio_obj = $dbListasPrecios->getPrecioFecha($id_plan, $cod_servicio, $tipo_precio, $tipo_bilateral, "");
				
				if (isset($precio_obj["id_precio"])) {
	?>
    <input type="hidden" id="hdd_id_precio_detalle_<?php echo($indice); ?>" value="<?php echo($precio_obj["id_precio"]); ?>" />
    <input type="hidden" id="hdd_valor_detalle_<?php echo($indice); ?>" value="<?php echo($precio_obj["valor"]); ?>" />
    <input type="hidden" id="hdd_valor_cuota_detalle_<?php echo($indice); ?>" value="<?php echo($precio_obj["valor_cuota"]); ?>" />
    <?php
				} else {
	?>
    <input type="hidden" id="hdd_id_precio_detalle_<?php echo($indice); ?>" value="0" />
    <input type="hidden" id="hdd_valor_detalle_<?php echo($indice); ?>" value="0" />
    <input type="hidden" id="hdd_valor_cuota_detalle_<?php echo($indice); ?>" value="0" />
    <?php
                }
            }
    ?>
    <input type="hidden" id="hdd_tipo_pago_detalle_<?php echo($indice); ?>" value="<?php echo($ind_tipo_pago_aux); ?>" />
    <?php
        } else {
	?>
    <input type="hidden" id="hdd_id_precio_detalle_<?php echo($indice); ?>" value="0" />
    <input type="hidden" id="hdd_valor_detalle_<?php echo($indice); ?>" value="0" />
    <input type="hidden" id="hdd_valor_cuota_detalle_<?php echo($indice); ?>" value="0" />
    <input type="hidden" id="hdd_tipo_pago_detalle_<?php echo($indice); ?>" value="2" />
    <?php
        }
        break;

    case "18": //Búsqueda de número de carné en atenciones anteriores
        @$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
        @$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);

        $num_carnet = "";
        $admision_ant_obj = $dbAdmision->get_ultima_admision_convenio($id_paciente, $id_convenio);
        if (isset($admision_ant_obj["num_carnet"])) {
            $num_carnet = $admision_ant_obj["num_carnet"];
        }
	?>
    <input type="hidden" id="hdd_num_carnet_busq" value="<?php echo($num_carnet); ?>" />
    <?php
        break;

    case "19": //Combo de profesionales que atienden
        @$id_usuario_prof_base = $utilidades->str_decode($_POST["id_usuario_prof_base"]);
        @$modificar_valores = $utilidades->str_decode($_POST["modificar_valores"]);

        $lista_usuarios_disponibles = $dbDisponibilidadProf->getDisponibilidadProfActual();
		

        $combo->getComboDb("cmd_profesionalAtiende", $id_usuario_prof_base, $lista_usuarios_disponibles, "id_usuario, nombre_del_usuario", "Seleccione al profesional", "settipocita(".$modificar_valores.", '');", "", "width: 188px;", "");
        break;
		
    case "20": //Liquida consultas
	?>
    <div style="width:99%;">
        <table id="tablaPrecios" class="modal_table">
            <tr id="tabla_encabezado">
                <th style="width:23%;">Liquidación</th>                   
            </tr>
        </table>
    </div>
    <?php
        break;
case "21":
	
		$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
		$categoria = $utilidades->str_decode($_POST["categoria"]);
		$subcategoria = $utilidades->str_decode($_POST["subcategoria"]);
		$remitido = "";
		$otro = "";
		$referido = $utilidades->str_decode($_POST["referido"]);

	?>
        <div class="encabezado center">
        	<h3>Formulario mercadeo</h3>
        </div>
        <form id="frmFlotanteMercadeo">
            <table class="modal_table" cellpadding="5" cellspacing="0" align="center" style="width:95%; margin-top:20px; margin-bottom:15px;">
              <tr>
             	<tr>
                    <td class="td_reducido" align="right" style="width:10%;"><label class="inline">Categoría:</label>
                        <td colspan="2" align="left" style="width:10%;">
                        
                        	<input type="hidden" name="hdd_otro_mercadeo_form" id="hdd_otro_mercadeo_form" value="<?= $otro?>"/>
                            <input type="hidden" name="hdd_remitido_mercadeo_form" id="hdd_remitido_mercadeo_form" value="<?= $remitido?>"/>
                             <input type="hidden" name="hdd_referido_mercadeo_form" id="hdd_referido_mercadeo_form" value="<?= $referido?>"/>
                            <?php 
							 $lista_categorias = $dbListas->getListasMercadeoAdmision();	
							 $combo->getComboDb("cmb_lista_merc", $categoria, $lista_categorias, "id_lista, nombre_lista", "Seleccione--", "seleccionar_mercadeo();", "", "width: 100%;", "");
							?>
						
                    	</td> 
                    
                    <td class="td_reducido" align="right" style="width:10%;"><label class="inline">Subcategoría:</label>
                        <td colspan="2" align="left" style="width:10%;">
                             <div id="d_subcategoria">
                             	 <?php 
								 $lista_subcategorias = $dbListas->getListaDetalles($categoria);
								 $combo->getComboDb("cmb_subcategoria_merc", $subcategoria, $lista_subcategorias, "id_detalle, nombre_detalle", "Seleccione--", "", "", "width: 100%;", "");
							   	?>	
                             </div>
                      </td>
             	 </tr>
               
            </table>
            
     			<input class="btnPrincipal peq" type="button" onclick="guardar_datos_mercadeo();" value="Guardar">
                <input type="hidden" name="hdd_id_paciente_aux" id="hdd_id_paciente_aux" value="<?= $id_paciente ?>">     
        </form>
        <br/>
        <div style="clear: both;"></div>
       <br/>
      <div id="rPrecios"></div>
    <?php
	break;
		
		 
	case "22":
	
	$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
	$id_categoria= $utilidades->str_decode($_POST["categoria"]);
	$id_sub_categoria = $utilidades->str_decode($_POST["subcategoria"]);
	$remitido= $utilidades->str_decode($_POST["remitido"]);
	$referido=$utilidades->str_decode($_POST["referido"]);
	
	?>
     <input type="hidden" name="hdd_id_paciente" id="hdd_id_paciente" value="<?= $id_paciente?>">
     <input type="hidden" name="hdd_categoria_<?=$id_paciente?>" id="hdd_categoria_<?=$id_paciente?>" value="<?= $id_categoria?>">
     <input type="hidden" name="hdd_subcategoria_<?=$id_paciente?>" id="hdd_subcategoria_<?=$id_paciente?>" value="<?= $id_sub_categoria?>">
     <input type="hidden" name="hdd_remitido_<?=$id_paciente?>" id="hdd_remitido_<?=$id_paciente?>" value="<?= $remitido?>">
     <input type="hidden" name="hdd_referido_<?=$id_paciente?>" id="hdd_referido_<?=$id_paciente?>" value="<?= $referido?>"> 
    <?php
		
	break;
	
	case "23":
		 
	@$id_categoria = $utilidades->str_decode($_POST["id_lista_mercadeo"]);
	$lista_categorias = $dbListas->getListaDetalles($id_categoria);
	$referido = $utilidades->str_decode($_POST["referido"]);
	
	if($id_categoria == 95){
		?>
		<td align="left" id="td_remitido_merc" >
			<input type="text" name="txt_referido_merc" id="txt_referido_merc" value="<?= $referido?>" placeholder="Ingrese nombre de la persona">
		</td>  
        <?php	
	
	}
	else{
		echo $combo->getComboDb("cmb_subcategoria_merc", $id_detalle, $lista_categorias, "id_detalle, nombre_detalle", "----Seleccione----", ", 0);", "", "width: 100%;");
		}	

	}
?>
