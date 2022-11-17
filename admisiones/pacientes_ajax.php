<?php
session_start();

header('Content-Type: text/xml; charset=UTF-8');

require_once("../db/DbListas.php");
require_once("../db/DbHistoriaClinica.php");
require_once("../db/DbVariables.php");
require_once("../db/DbPacientes.php");
require_once("../db/DbPaises.php");
require_once("../db/DbDepartamentos.php");
require_once("../db/DbDepMuni.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../principal/ContenidoHtml.php");
require_once("../funciones/FuncionesPersona.php");
require_once("../funciones/Utilidades.php");
require_once("../funciones/Class_Generar_Clave.php");
require_once("../db/DbConvenios.php");
require_once("../funciones/Class_Barra_Progreso.php");
require_once("../db/DbConvenios.php");
require_once("../funciones/Class_Conector_Siesa.php");
require_once("../db/DbPlanes.php");

$dbListas = new DbListas();
$dbHistoriaClinica = new DbHistoriaClinica();
$dbVariables = new Dbvariables();
$dbPacientes = new DbPacientes();
$dbDepMuni = new DbDepMuni();
$contenido = new ContenidoHtml();
$contenido->validar_seguridad(1);
$funciones_persona = new FuncionesPersona();
$utilidades = new Utilidades();
$combo = new Combo_Box();
$opcion = $utilidades->str_decode($_POST["opcion"]);
$barra_progreso = new Barra_Progreso();
$dbConvenios = new DbConvenios();
$conectorSiesa = new Class_Conector_Siesa();
$dbPlanes = new DbPlanes();
$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);

function ver_datos_paciente($id_paciente = null, $ind_overflow = 0, $ind_ventana_flotante = 0) {
    $dbListas = new DbListas();
    $dbPacientes = new DbPacientes();
    $dbPaises = new DbPaises();
    $dbDepartamentos = new DbDepartamentos();
    $dbDepMuni = new DbDepMuni();
    $utilidades = new Utilidades();
    $contenido = new ContenidoHtml();
    $contenido->validar_seguridad(1);
    $combo = new Combo_Box();
    $tipo_acceso_menu = $contenido->obtener_permisos_menu($utilidades->str_decode($_POST["hdd_numero_menu"]));
    $dbConvenios = new DbConvenios();
    $dbPlanes = new DbPlanes();

    $id_tipo_documento = "";
    $numero_documento = "";
    $nombre_1 = "";
    $nombre_2 = "";
    $apellido_1 = "";
    $apellido_2 = "";
    $sexo = "";
    $id_pais = "";
    $cod_dep = "";
    $cod_mun = "";
    $nom_dep = "";
    $nom_mun = "";
    $direccion = "";
    $telefono_1 = "";
    $email = "";
    $fecha_nacimiento = "";
    $observ_paciente = "";
    $cmb_convenio = "";
    $cmb_estado_convenio = 1; //Quemado con valor
    $cmb_exentoSeguro = 1; //Quemado con valor
    $rango = "";
    $tipoUsuario = "";
    $clave_verificacion = "";
    $texto_tbn_submit = "Registrar";
    $tipo_accion = "validar_paciente(1);";
    $ind_activo = "disabled";
    $id_plan = "";

    if (isset($id_paciente)) {

        //Se obtienen los datos del paciente
        $paciente_obj = $dbPacientes->getExistepaciente3($id_paciente);

        $id_tipo_documento = $paciente_obj["id_tipo_documento"];
        $numero_documento = $paciente_obj["numero_documento"];
        $nombre_1 = $paciente_obj["nombre_1"];
        $nombre_2 = $paciente_obj["nombre_2"];
        $apellido_1 = $paciente_obj["apellido_1"];
        $apellido_2 = $paciente_obj["apellido_2"];
        $sexo = $paciente_obj["sexo"];
        $id_pais = $paciente_obj["id_pais"];
        $cod_dep = $paciente_obj["cod_dep"];
        $cod_mun = $paciente_obj["cod_mun"];
        $nom_dep = $paciente_obj["nom_dep"];
        $nom_mun = $paciente_obj["nom_mun"];
        $direccion = $paciente_obj["direccion"];
        $telefono_1 = $paciente_obj["telefono_1"];
        $email = $paciente_obj["email"];
        $fecha_nacimiento = $paciente_obj["fecha_nacimiento_aux"];
        $observ_paciente = $paciente_obj["observ_paciente"];
        $cmb_convenio = $paciente_obj["id_convenio_paciente"];
        $cmb_estado_convenio = $paciente_obj["status_convenio_paciente"];
        $cmb_exentoSeguro = $paciente_obj["exento_pamo_paciente"];
        $rango = $paciente_obj["rango_paciente"];
        $tipoUsuario = $paciente_obj["tipo_coti_paciente"];
        $id_plan = $paciente_obj["id_plan"];

        $clave_verificacion = $paciente_obj["clave_verificacion"];
        if ($clave_verificacion == '') {
            /* Para generar clave del paciente */
            $clave_paciente = new Class_Generar_Clave();
            $InitalizationKey = $clave_paciente->generate_secret_key(16);
            $TimeStamp = $clave_paciente->get_timestamp();
            $secretkey = $clave_paciente->base32_decode($InitalizationKey);
            $clave_verificacion = $clave_paciente->oath_hotp($secretkey, $TimeStamp);

            $dbPacientes->guardar_clave_verificacion($id_paciente, $clave_verificacion);
        }

        $texto_tbn_submit = "Actualizar";
        $tipo_accion = "validar_paciente(2);";
        $ind_activo = "";
    }

    $estilos_fieldset = "";
    if ($ind_overflow == 1) {
        $estilos_fieldset = "height: 550px;overflow: overlay;";
    }
    ?>
    <input type="hidden" id="hdd_id_paciente" value="<?php echo($id_paciente); ?>" />
    <fieldset style="width: 90%; margin: auto;<?= $estilos_fieldset ?>">
        <legend>Datos del paciente:</legend>
        <form id="frm_paciente" name="frm_paciente" method="post">
            <table border="0" style="width: 100%; margin: auto; font-size: 10pt;">
                <tr>
                    <td align="left" style="width:25%;"><label>Tipo de identificaci&oacute;n*</label></td>
                    <td align="left" style="width:25%;"><label>N&uacute;mero de identificaci&oacute;n*</label></td>
                    <td align="center" colspan="2" rowspan="2" valign="top"><b>C&oacute;digo de verificaci&oacute;n <br /> <?php echo($clave_verificacion); ?> </b></td>
                </tr>
                <tr>
                    <td align="left">
                        <?php
                        //Se carga la lista de tipos de documento
                        $lista_tipos_documento = $dbListas->getListaDetalles(2, 1);
                        $combo->getComboDb("cmb_tipo_documento", $id_tipo_documento, $lista_tipos_documento, "id_detalle,nombre_detalle", "-- Seleccione --", "", 1, "width:100%;");
                        ?>
                    </td>
                    <td align="left">
                        <input type="text" id="txt_numero_documento" name="txt_numero_documento" value="<?php echo($numero_documento); ?>" maxlength="20" style="width:100%;" onblur="trim(this.value);" />
                    </td>

                </tr>
                <tr>
                    <td align="left"><label>Primer nombre*</label></td>
                    <td align="left"><label>Segundo nombre</label></td>
                    <td align="left"><label>Primer apellido*</label></td>
                    <td align="left"><label>Segundo apellido</label></td>
                </tr>
                <tr>
                    <td align="left">
                        <input type="text" id="txt_nombre_1" name="txt_nombre_1" value="<?php echo($nombre_1); ?>" maxlength="100" style="width:100%;" onblur="trim(this.value);" />
                    </td>
                    <td align="left">
                        <input type="text" id="txt_nombre_2" name="txt_nombre_2" value="<?php echo($nombre_2); ?>" maxlength="100" style="width:100%;" onblur="trim(this.value);" />
                    </td>
                    <td align="left">
                        <input type="text" id="txt_apellido_1" name="txt_apellido_1" value="<?php echo($apellido_1); ?>" maxlength="100" style="width:100%;" onblur="trim(this.value);" />
                    </td>
                    <td align="left">
                        <input type="text" id="txt_apellido_2" name="txt_apellido_2" value="<?php echo($apellido_2); ?>" maxlength="100" style="width:100%;" onblur="trim(this.value);" />
                    </td>
                </tr>
                <tr>
                    <td align="left"><label>G&eacute;nero*</label></td>
                    <td align="left"><label>Fecha de nacimiento*</label></td>
                </tr>
                <tr>
                    <td align="left">
                        <?php
                        //Se carga la lista de sexos
                        $lista_sexos = $dbListas->getListaDetalles(1, 1);
                        $combo->getComboDb("cmb_sexo", $sexo, $lista_sexos, "id_detalle,nombre_detalle", "-- Seleccione --", "", 1, "width:100%;");
                        ?>
                    </td>
                    <td align="left">                        
                        <input type="text" class="input required"  name="txt_fecha_nacimiento" id="txt_fecha_nacimiento" value="<?php echo($fecha_nacimiento); ?>" maxlength="10" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" style="width:120px;" <?= $disabled ?> />
                    </td>

                </tr>

                <tr>
                    <td align="left"><label>Pa&iacute;s de residencia*</label></td>
                    <td align="left" id="td_dep_col_res" style="display:<?php if ($id_pais == "1") { ?>table-cell<?php } else { ?>none<?php } ?>;"><label>Departamento de residencia*</label></td>
                    <td align="left" id="td_mun_col_res" style="display:<?php if ($id_pais == "1") { ?>table-cell<?php } else { ?>none<?php } ?>;"><label>Municipio de residencia*</label></td>
                    <td align="left" id="td_dep_otro_res" style="display:<?php if ($id_pais != "1") { ?>table-cell<?php } else { ?>none<?php } ?>;"><label>Estado/regi&oacute;n de residencia*</label></td>
                    <td align="left" id="td_mun_otro_res" style="display:<?php if ($id_pais != "1") { ?>table-cell<?php } else { ?>none<?php } ?>;"><label>Municipio de residencia*</label></td>
                </tr>
                <tr>
                    <td align="left">
                        <?php
                        $lista_paises = $dbPaises->getPaises();
                        $combo->getComboDb("cmb_pais_res", $id_pais, $lista_paises, "id_pais,nombre_pais", "-- Seleccione --", "seleccionar_pais(this.value, 'res');", 1, "width:100%;");
                        ?>
                    </td>
                    <td align="left" id="td_dep_col_val_res" style="display:<?php if ($id_pais == "1") { ?>table-cell<?php } else { ?>none<?php } ?>;">
                        <?php
                        $lista_departamentos = $dbDepartamentos->getDepartamentos();
                        $combo->getComboDb("cmb_cod_dep_res", $cod_dep, $lista_departamentos, "cod_dep,nom_dep", "-- Seleccione --", "seleccionar_departamento(this.value, 'res');", 1, "width:100%;");
                        ?>
                    </td>
                    <td align="left" id="td_mun_col_val_res" style="display:<?php if ($id_pais == "1") { ?>table-cell<?php } else { ?>none<?php } ?>;">
                        <div id="d_municipio_res">
                            <?php
                            $lista_municipios = $dbDepMuni->getMunicipiosDepartamento($cod_dep);
                            $combo->getComboDb("cmb_cod_mun_res", $cod_mun, $lista_municipios, "cod_mun_dane,nom_mun", " ", "", 1, "width:100%;");
                            ?>
                        </div>
                    </td>
                    <td align="left" id="td_dep_otro_val_res" style="display:<?php if ($id_pais != "1") { ?>table-cell<?php } else { ?>none<?php } ?>;">
                        <input type="text" id="txt_nom_dep_res" name="txt_nom_dep_res" value="<?php echo($nom_dep); ?>" maxlength="50" style="width:100%;" onblur="trim(this.value);" />
                    </td>
                    <td align="left" id="td_mun_otro_val_res" style="display:<?php if ($id_pais != "1") { ?>table-cell<?php } else { ?>none<?php } ?>;">
                        <input type="text" id="txt_nom_mun_res" name="txt_nom_mun_res" value="<?php echo($nom_mun); ?>" maxlength="50" style="width:100%;" onblur="trim(this.value);" />
                    </td>
                </tr>
                <tr>
                    <td align="left" colspan="2"><label>Direcci&oacute;n*</label></td>
                    <td align="left"><label>Tel&eacute;fono*</label></td>
                    <td align="left"><label>Email*</label></td>
                </tr>
                <tr>
                    <td align="left" colspan="2">
                        <input type="text" id="txt_direccion" name="txt_direccion" value="<?php echo($direccion); ?>" maxlength="200" style="width:100%;" onblur="trim(this.value);" />
                    </td>
                    <td align="left">
                        <input type="text" id="txt_telefono_1" name="txt_telefono_1" value="<?php echo($telefono_1); ?>" maxlength="10" style="width:100%;" onkeypress="" onblur="trim(this.value);" />
                    </td>
                    <td align="left">
                        <input type="text" id="txt_email" name="txt_email" value="<?php echo($email); ?>" style="width:100%;" onkeypress="" onblur="trim(this.value);" />
                    </td>
                </tr>
                <tr>
                    <td align="left"><label>Convenio/Entidad*</label></td>
                    <td align="left"><label>Plan*</label></td>
                    <td align="left"><label>Rango*</label></td>
                    <td align="left"><label>Tipo de usuario*</label></td>
                </tr>
                <tr>
                    <td align="left">
                        <?php
                        $lista_convenios = $dbConvenios->getListaConveniosActivos();
                        $combo->getComboDb("cmb_convenio", $cmb_convenio, $lista_convenios, "id_convenio, nombre_convenio", "--Seleccione el convenio--", "getPlanes(this.value);", "", "width:100%;");
                        ?>
                    </td>     
                    <td align="left">
                        <div id="div_plan">
                            <?php
                            if (strlen($cmb_convenio) <= 0) {
                                $combo->get("cmb_plan", "", "", "-- Seleccione el plan --", "", "", "width: 200px;", "", "");
                            } else {
                                $lista_planes = $dbPlanes->getListaPlanesActivos($cmb_convenio);
                                $combo->getComboDb("cmb_plan", $id_plan, $lista_planes, "id_plan, nombre_plan", "-- Seleccione el plan --", "", "", "width: 200px;");
                            }
                            ?>
                        </div>
                    </td>
                    <td align="left">
                        <?php
                        $array_rango = array();
                        $array_rango[0][0] = "0";
                        $array_rango[0][1] = "No aplica";
                        $array_rango[1][0] = "1";
                        $array_rango[1][1] = "Uno";
                        $array_rango[2][0] = "2";
                        $array_rango[2][1] = "Dos";
                        $array_rango[3][0] = "3";
                        $array_rango[3][1] = "Tres";
						
						$array_rango[4][0] = "6";
                        $array_rango[4][1] = "Subsidiado nivel 1";
						$array_rango[5][0] = "7";
                        $array_rango[5][1] = "Subsidiado nivel 2";



                        $combo->get("cmb_rango", $rango, $array_rango, "-- Seleccione --", "");
                        ?>
                    </td>
                    <td align="left">
                        <?php
                        /*$array_tipoUsuario = array();
                        $array_tipoUsuario[0][0] = "0";
                        $array_tipoUsuario[0][1] = "No aplica";
                        $array_tipoUsuario[1][0] = "1";
                        $array_tipoUsuario[1][1] = "Cotizante";
                        $array_tipoUsuario[2][0] = "2";
                        $array_tipoUsuario[2][1] = "Beneficiario";
                        $array_tipoUsuario[3][0] = "3";
                        $array_tipoUsuario[3][1] = "Subsidiado";*/
						$lista_tipo_usuario = $dbListas->getListaDetalles(99, 1);
						
                        $combo->getComboDb("cmb_tipoUsuario", $tipoUsuario, $lista_tipo_usuario, "id_detalle, nombre_detalle", "-- Seleccione --", "", "", "");
                        ?>
                    </td>
                </tr>
                <tr>                    
                    <td align="left"><label>Estado aseguradora*</label></td>
                    <td align="left"><label>Exento cuota moderadora*</label></td>
                </tr>
                <tr>                   
                    <td align="left">
                        <?php
                        $array_estadoConvenio = array();
                        $array_estadoConvenio[0][0] = "1";
                        $array_estadoConvenio[0][1] = "Activo";
                        $array_estadoConvenio[1][0] = "2";
                        $array_estadoConvenio[1][1] = "Inactivo";
                        $array_estadoConvenio[2][0] = "3";
                        $array_estadoConvenio[2][1] = "Atencion especial";

                        $combo->get("cmb_estatus_convenio", $cmb_estado_convenio, $array_estadoConvenio, "-- Seleccione el estado del seguro --", "", $ind_activo);
                        ?>
                    </td>
                    <td align="left">                        
                        <?php
                        $array_exento = array();
                        $array_exento[0][0] = "0";
                        $array_exento[0][1] = "S&iacute;";
                        $array_exento[1][0] = "1";
                        $array_exento[1][1] = "No";

                        $combo->get("cmb_exento_convenio", $cmb_exentoSeguro, $array_exento, "-- Seleccione --", "", $ind_activo);
                        ?>
                    </td>
                </tr>    
                <tr>
                    <td align="left"><label>Observaciones</label></td>
                </tr>
                <tr>
                    <td align="left" colspan="4">
                        <textarea id="txt_observ_paciente" name="txt_observ_paciente" style="width:100%; height:75px;" onblur="trim(this.value);"><?php echo($observ_paciente); ?></textarea>
                    </td>
                </tr>
            </table>
            <br />
            <div id="d_btn_guardar_paciente">
                <?php
                if ($tipo_acceso_menu == 2) {
                    ?>
                    <input type="submit" id="btn_guardar" nombre="btn_guardar" value="<?= $texto_tbn_submit ?>" onclick="<?= $tipo_accion ?>" class="btnPrincipal"/>
                    <?php
                }
                ?>
                <input type="hidden" id="hdd_ind_ventana_flotante" name="hdd_ind_ventana_flotante" value="<?php echo($ind_ventana_flotante); ?>" />
            </div>
        </form>    

    </fieldset>




    <div id="d_esperar_guardar_paciente" style="display:none;">
        <img src="../imagenes/ajax-loader.gif" />
    </div>
    <?php
}

switch ($opcion) {
    case "1": //Cargar datos del paciente
        $texto_busqueda = $utilidades->str_decode($_POST["texto_busqueda"]);

        $tabla_personas = $dbHistoriaClinica->getPacientesHistoriaClinica($texto_busqueda);
        $cantidad_datos = count($tabla_personas);

        if ($cantidad_datos == 1) {//Si se encontro un solo registro
            $id_paciente = $tabla_personas[0]['id_paciente'];

            ver_datos_paciente($id_paciente);
        } else if ($cantidad_datos > 1) {
            ?>
            <table id="tabla_persona_hc" border="0" class="paginated modal_table" style="width:100%;">
                <thead>
                    <tr class="headegrid">
                        <th class="headegrid" align="center">Documento</th>	
                        <th class="headegrid" align="center">Pacientes</th>
                    </tr>
                </thead>
                <?php
                foreach ($tabla_personas as $fila_personas) {
                    $id_paciente = $fila_personas['id_paciente'];
                    $nombre_1 = $fila_personas['nombre_1'];
                    $nombre_2 = $fila_personas['nombre_2'];
                    $apellido_1 = $fila_personas['apellido_1'];
                    $apellido_2 = $fila_personas['apellido_2'];
                    $numero_documento = $fila_personas['numero_documento'];
                    $nombres_apellidos = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
                    ?>
                    <tr class="celdagrid" onclick="ver_datos_paciente(<?php echo($id_paciente); ?>);">
                        <td align="left"><?php echo($numero_documento); ?></td>	
                        <td align="left"><?php echo($nombres_apellidos); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <script id='ajax'>
                //<![CDATA[ 
                $(function () {
                    $('.paginated', 'tabla_persona_hc').each(function (i) {
                        $(this).text(i + 1);
                    });

                    $('table.paginated').each(function () {
                        var currentPage = 0;
                        var numPerPage = 5;
                        var $table = $(this);
                        $table.bind('repaginate', function () {
                            $table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
                        });
                        $table.trigger('repaginate');
                        var numRows = $table.find('tbody tr').length;
                        var numPages = Math.ceil(numRows / numPerPage);
                        var $pager = $('<div class="pager"></div>');
                        for (var page = 0; page < numPages; page++) {
                            $('<span class="page-number"></span>').text(page + 1).bind('click', {
                                newPage: page
                            }, function (event) {
                                currentPage = event.data['newPage'];
                                $table.trigger('repaginate');
                                $(this).addClass('active').siblings().removeClass('active');
                            }).appendTo($pager).addClass('clickable');
                        }
                        $pager.insertBefore($table).find('span.page-number:first').addClass('active');
                    });
                });
                //]]>
            </script>
            <?php
        } else if ($cantidad_datos == 0) {
            echo"<div class='msj-vacio'>
					<p>No se encontraron pacientes</p>
			     </div>";
        }
        break;

    case "2": //Mostrar los datos de un paciente
        @$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
        ver_datos_paciente($id_paciente);
        break;

    case "3": //Guardar datos del paciente
        $id_usuario = $_SESSION["idUsuario"];
        @$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
        @$id_tipo_documento = $utilidades->str_decode($_POST["id_tipo_documento"]);
        @$numero_documento = $utilidades->str_decode($_POST["numero_documento"]);
        @$nombre_1 = $utilidades->str_decode($_POST["nombre_1"]);
        @$nombre_2 = $utilidades->str_decode($_POST["nombre_2"]);
        @$apellido_1 = $utilidades->str_decode($_POST["apellido_1"]);
        @$apellido_2 = $utilidades->str_decode($_POST["apellido_2"]);
        @$sexo = $utilidades->str_decode($_POST["sexo"]);
        @$fecha_nacimiento = $utilidades->str_decode($_POST["fecha_nacimiento"]);
        @$tipo_sangre = $utilidades->str_decode($_POST["tipo_sangre"]);
        @$factor_rh = $utilidades->str_decode($_POST["factor_rh"]);
        @$id_pais_nac = $utilidades->str_decode($_POST["id_pais_nac"]);
        @$cod_dep_nac = $utilidades->str_decode($_POST["cod_dep_nac"]);
        @$cod_mun_nac = $utilidades->str_decode($_POST["cod_mun_nac"]);
        @$nom_dep_nac = $utilidades->str_decode($_POST["nom_dep_nac"]);
        @$nom_mun_nac = $utilidades->str_decode($_POST["nom_mun_nac"]);
        @$id_pais = $utilidades->str_decode($_POST["id_pais"]);
        @$cod_dep = $utilidades->str_decode($_POST["cod_dep"]);
        @$cod_mun = $utilidades->str_decode($_POST["cod_mun"]);
        @$nom_dep = $utilidades->str_decode($_POST["nom_dep_res"]);
        @$nom_mun = $utilidades->str_decode($_POST["nom_mun_res"]);
        @$id_zona = $utilidades->str_decode($_POST["id_zona"]);
        @$direccion = $utilidades->str_decode($_POST["direccion"]);
        @$email = $utilidades->str_decode($_POST["email"]);
        @$telefono_1 = $utilidades->str_decode($_POST["telefono_1"]);
        @$telefono_2 = $utilidades->str_decode($_POST["telefono_2"]);
        @$profesion = $utilidades->str_decode($_POST["profesion"]);
        @$id_estado_civil = $utilidades->str_decode($_POST["id_estado_civil"]);
        @$ind_desplazado = $utilidades->str_decode($_POST["ind_desplazado"]);
        @$id_etnia = $utilidades->str_decode($_POST["id_etnia"]);
        @$observ_paciente = $utilidades->str_decode($_POST["observ_paciente"]);

        @$convenio = $utilidades->str_decode($_POST["convenio"]);
        @$estado_convenio = $utilidades->str_decode($_POST["estado_convenio"]);
        @$exento = $utilidades->str_decode($_POST["exento"]);
        @$rango = $utilidades->str_decode($_POST["rango"]);
        @$tipoUsuario = $utilidades->str_decode($_POST["tipoUsuario"]);
        @$tipo_accion = $utilidades->str_decode($_POST["tipo_accion"]);
        @$id_plan = $utilidades->str_decode($_POST["id_plan"]);
		@$ind_habeas_data = "";
        if ($tipo_accion == 1) {//Nuevo paciente
            $resultado = $dbPacientes->crear_paciente($id_tipo_documento, $numero_documento, $nombre_1, $nombre_2, $apellido_1, $apellido_2, $sexo, $fecha_nacimiento, $tipo_sangre, $factor_rh, $id_pais_nac, $cod_dep_nac, $cod_mun_nac, $nom_dep_nac, $nom_mun_nac, $id_pais, $cod_dep, $cod_mun, $nom_dep, $nom_mun, $id_zona, $direccion, $email, $telefono_1, $telefono_2, $profesion, $id_estado_civil, $ind_desplazado, $id_etnia, $id_usuario, $observ_paciente, $ind_habeas_data, $convenio, $estado_convenio, $exento, $rango, $tipoUsuario, $id_plan);
        } else if ($tipo_accion == 2) {//Editar paciente
            $resultado = $dbPacientes->editar_paciente($id_paciente, $id_tipo_documento, $numero_documento, $nombre_1, $nombre_2, $apellido_1, $apellido_2, $sexo, $fecha_nacimiento, $tipo_sangre, $factor_rh, $id_pais_nac, $cod_dep_nac, $cod_mun_nac, $nom_dep_nac, $nom_mun_nac, $id_pais, $cod_dep, $cod_mun, $nom_dep, $nom_mun, $id_zona, $direccion, $email, $telefono_1, $telefono_2, $profesion, $id_estado_civil, $ind_desplazado, $id_etnia, $id_usuario, $observ_paciente, $ind_habeas_data, $convenio, $estado_convenio, $exento, $rango, $tipoUsuario, $id_plan);
        }
        ?>
        <input type="hidden" id="hdd_resultado_guardar_paciente" value="<?php echo($resultado); ?>" />
        <?php
        break;

    case "4": //Se carga el combo de municipios
        @$cod_dep = $utilidades->str_decode($_POST["cod_dep"]);
        @$sufijo = $utilidades->str_decode($_POST["sufijo"]);

        $lista_municipios = $dbDepMuni->getMunicipiosDepartamento($cod_dep);
        $combo->getComboDb("cmb_cod_mun_" . $sufijo, "", $lista_municipios, "cod_mun_dane,nom_mun", " ", "", 1, "width:100%;");
        break;

    case "5":
        ?>
        <fieldset style="width: 90%; margin: auto;">
            <legend>Importar pacientes:</legend>
            <div style="text-align: left;margin-bottom: 40px;">
                <ul>
                    <li>Los siguientes planes de convenios han sido parametrizados:
                        <ul>
                            <li><strong>Nueva EPS</strong>, resoluci&oacute;n 14. del 19/12/2018</li>
                            <li><strong>Avanzar FOS</strong>, resoluci&oacute;n 29. del 12/09/2018</li>
                        </ul>
                    </li>
                </ul>

            </div>
            <!--<form id="frm_importarISS" name="frm_importarISS" method="post">-->
            <table border="0" style="width:100%; margin: 0 auto;">
                <tr>
                    <td style="text-align: left;">
                        <label>Convenio o aseguradora*</label>
                    </td>
                    <td style="text-align: left;">
                        <label>Plan*</label>
                    </td>
                    <td style="text-align: left;">
                        <label>Seleccione el archivo (.csv)*</label>
                    </td>
                    <td style="">

                    </td>
                </tr>
                <tr>
                    <td style="text-align: left;">
                        <?php
                        $lista_convenios = $dbConvenios->getListaConveniosActivos();
                        $combo->getComboDb("cmb_convenio", "", $lista_convenios, "id_convenio, nombre_convenio", "Seleccione el Convenio/Aseguradora", "getPlanes(this.value);", "", "width: 200px;");
                        ?>
                    </td>
                    <td style="text-align: left;">
                        <div id="div_plan">
                            <?php
                            $combo->get("cmb_plan", "", "", "-- Seleccione el plan --", "", "", "width: 200px;", "", "");
                            ?>
                        </div>                        
                    </td>
                    <td style="text-align: left;display: none;">
                        <div id="divIPS">
                            <?php
                            $combo->getComboDb("cmb_ips", "", "", "id_ips, nom_ips", "Seleccione la IPS", "", "", "width: 200px;");
                            ?>
                        </div>
                    </td>
                    <td style="text-align: left;">
                        <input type="file" id="fileISS" name="fileISS" accept=".csv" />
                    </td>
                    <td style="">
                        <?php
                        if ($tipo_acceso_menu == 2) {
                            ?>
                            <input type="button" value="Importar" onclick="validarImportarPacientes();" class="btnPrincipal"/>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" valign="top"></td>
                </tr>
            </table>
            <!--</form>-->
            <div id="resultadoImportarISS"></div>
            <?= $barra_progreso->get("d_barra_progreso_adj", "50%", FALSE, 1); ?>
        </fieldset>
        <?php
        break;

    case "6":/* Subir archivo */
        $id_usuario = $_SESSION["idUsuario"];
        @$file = $_FILES["fileISS"]["name"];
        @$idConvenio = $utilidades->str_decode($_POST["idConvenio"]);
        @$idPlan = $utilidades->str_decode($_POST["idPlan"]);

        $ruta = "tmp/" . $file;

        copy($_FILES["fileISS"]["tmp_name"], $ruta);
        ?>
        <input type="hidden" id="hddArchivo" name="hddArchivo" value="<?= $ruta ?>" />
        <?php
        $rta = -3; /* Error al leer el archivo */

        $dbPacientes->eliminarTmpPacientes(); /* Elimina la tabla tmp_pacientes */

        if ($fp = mb_convert_encoding(file($ruta), 'UTF-8')) {
            $filas = count($fp);

            /* Valida la versión del archivo - Deben de existir 33 columnas */
            $columnas = 0;
            for ($index = 1; $index < 2; $index++) {
                $columnas = count(explode(";", $fp[$index]));
            }
			
            switch ($idConvenio) {
                case "48": //Convenio FOSCAL - NUEVA EPS, resolución 14 [14 columnas]
                    if ($columnas == 14) {//Valida la cantidad de columnas para la resolución 33
                        $arrDatos = "";
                        $contador = 0;
                        $guarda = false;
                        $tipoDocumento = "";
                        $documento = "";
                        $apellido = "";
                        $apellido2 = "";
                        $nombre = "";
                        $tipoAfi = "";
                        $sexo = "";
                        $dir = "";
                        $tel = "";
                        $fechaNac = "";

                        $rango = "";
                        $dep = "";

                        $arrDatosGrupo = array();
                        for ($index = 1; $index <= $filas; $index++) {
                            $contador++;
							$linea = $fp[$index];
							if ($linea != "") {
								$arrDato = explode(";", $linea);
								
								$arrAux = array();
								$arrAux["tipoDocumento"] = $arrDato[0];
								$arrAux["documento"] = $arrDato[1];
								$arrAux["apellido"] = $arrDato[2];
								$arrAux["apellido2"] = $arrDato[3];
								$arrAux["nombre"] = $arrDato[4];
								$arrAux["nombre2"] = "";
								$arrAux["tipoAfi"] = $arrDato[5];
								$arrAux["sexo"] = $arrDato[6];
								$arrAux["dir"] = $arrDato[7];
								$arrAux["tel"] = $arrDato[8];
								$arrAux["fechaNac"] = $arrDato[9];
								$arrAux["rango"] = $arrDato[10];
								$arrAux["dep"] = $arrDato[11];
								$arrAux["mun"] = $arrDato[12];
								$arrAux["exento"] = $arrDato[13];
								
								array_push($arrDatosGrupo, $arrAux);
							}
							
							if ($contador % 1000 == 0 || $index == $filas) {//Realiza el salto cada 1000 registro (OJO, el mismo valor 4 líneas más abajo)
								$rta = $dbPacientes->cargarTmpPacientes($id_usuario, $idConvenio, $arrDatosGrupo, $idPlan);
								$arrDatosGrupo = array();
							}
                        }
                    } else {
                        $rta = -4; /* Error de versión del archivo */
                    }
                    break;
					
                case "49": //Convenio FOSCAL - Avanzar FOS, resolucion 29 [29 columnas]
                    if ($columnas == 29) { //Valida la cantidad de columnas para la resolución 29
                        $arrDatos = "";
                        $contador = 0;
                        $guarda = false;
                        $tipoDocumento = "";
                        $documento = "";
                        $apellido = "";
                        $apellido2 = "";
                        $nombre = "";
                        $tipoAfi = "";
                        $sexo = "";
                        $dir = "";
                        $tel = "";
                        $fechaNac = "";

                        $rango = "";
                        $dep = "";

                        $arrDatosGrupo = array();
                        for ($index = 1; $index <= $filas; $index++) {
                            $contador++;
                            $arrDato = explode(";", $fp[$index]);

                            $arrAux = array();
                            $arrAux["tipoDocumento"] = $arrDato[1];
                            $arrAux["documento"] = $arrDato[2];
                            $arrAux["apellido"] = $arrDato[3];
                            $arrAux["apellido2"] = $arrDato[4];
                            $arrAux["nombre"] = $arrDato[5];
                            $arrAux["nombre2"] = $arrDato[6];
                            $arrAux["tipoAfi"] = $arrDato[12];
                            $arrAux["sexo"] = $arrDato[8];
                            $arrAux["dir"] = $arrDato[27];
                            $arrAux["tel"] = $arrDato[28];
                            $arrAux["fechaNac"] = $arrDato[7];
                            $arrAux["dep"] = $arrDato[13];
                            $arrAux["mun"] = $arrDato[14];

                            array_push($arrDatosGrupo, $arrAux);

                            if ($contador % 1000 == 0 || $index == $filas) {// Realiza el salto cada 1000 registro (OJO,el mismo valor 4 l�neas m�s abajo)
                                $rta = $dbPacientes->cargarTmpPacientes($id_usuario, $idConvenio, $arrDatosGrupo, $idPlan);
                                $arrDatosGrupo = array();
                            }
                        }
                    } else {
                        $rta = -4; /* Error de versón del archivo */
                    }
                    break;
                default:
                    echo "El convenio no ha sido parametrizado";
            }
        }
        /* Elimina el archivo */
        unlink($ruta);
        ?>
        <input type="hidden" id="hddResultadoValidaArchivo" name="hddResultadoValidaArchivo" value="<?= $rta ?>" />
        <?php
        if ($rta == 1) {
            
			$rta = $dbPacientes->importarTmpPacientes($id_usuario, $idConvenio, $idPlan);
            ?>
            <input type="hidden" id="hddResultadoImportarPacientes" name="hddResultadoImportarPacientes" value="<?= $rta ?>" />
            <?php
        }
        break;

    case "8"://Combo planes
        @$id_convenio = $utilidades->str_decode($_POST["idConvenio"]);

        $lista_planes = $dbPlanes->getListaPlanesActivos($id_convenio);
        $combo->getComboDb("cmb_plan", "", $lista_planes, "id_plan, nombre_plan", "-- Seleccione el plan --", "", "", "width: 200px;");
        break;

    case "10"://Formulario de creación nuevo paciente
        @$ind_overflow = $utilidades->str_decode($_POST["ind_overflow"]);
        @$ind_ventana_flotante = $utilidades->str_decode($_POST["ind_ventana_flotante"]);
        ver_datos_paciente(null, $ind_overflow, $ind_ventana_flotante);
        break;
}
?>
