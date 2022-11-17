<?php
session_start();

header("Content-Type: text/xml; charset=UTF-8");

require_once("../db/DbOrdenesMedicas.php");
require_once("../db/DbHistoriaClinica.php");
require_once("../db/DbAutorizaciones.php");
require_once("../db/DbPacientes.php");
require_once("../db/Dbvariables.php");
require_once("../db/DbMaestroProcedimientos.php");
require_once("../db/DbDiagnosticos.php");
require_once("../db/DbConvenios.php");
require_once("../db/DbPlanes.php");
require_once("../db/DbPaquetesProcedimientos.php");
require_once("../db/DbListasPrecios.php");
require_once("../db/DbPagos.php");
require_once("../funciones/FuncionesPersona.php");
require_once("../funciones/Utilidades.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../funciones/Class_Ordenes_Remisiones.php");
require_once("../funciones/LiquidadorPrecios.php");
require_once("../funciones/pdf/fpdf.php");
require_once("../funciones/pdf/makefont/makefont.php");
require_once("../funciones/pdf/funciones.php");
require_once("../funciones/pdf/WriteHTML.php");
require_once("../principal/ContenidoHtml.php");

$dbOrdenesMedicas = new DbOrdenesMedicas();
$dbHistoriaClinica = new DbHistoriaClinica();
$dbAutorizaciones = new DbAutorizaciones();
$dbPacientes = new DbPacientes();
$dbvariables = new Dbvariables();
$dbMaestroProcedimientos = new DbMaestroProcedimientos();
$dbDiagnosticos = new DbDiagnosticos();
$dbConvenios = new DbConvenios();
$dbPlanes = new DbPlanes();
$dbPaquetesProcedimientos = new DbPaquetesProcedimientos();
$dbListasPrecios = new DbListasPrecios();
$dbPagos = new DbPagos();
$contenido = new ContenidoHtml();
$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);

$funciones_persona = new FuncionesPersona();
$utilidades = new Utilidades();
$combo = new Combo_Box();
$liquidadorPrecios = new LiquidadorPrecios();

$opcion = $_POST["opcion"];
$usuarioCrea = $_SESSION["idUsuario"];
$lugar = $_SESSION["idLugarUsuario"];

function ver_formulaciones($id_paciente, $nombre_persona, $documento_persona, $tipo_documento, $telefonos, $fecha_nacimiento, $edad_paciente, $nombre_convenio, $nombre_plan, $estado_convenio, $tipoAccion) {
    $dbHistoria = new DbHistoriaClinica();
    $class_ordenes_remisiones = new Class_Ordenes_Remisiones();

    //Edad
    $arr_edad = explode("/", $edad_paciente);
    $edad = $arr_edad[0];
    $unidad_edad = "";
    switch ($arr_edad[1]) {
        case 1://Años
            $unidad_edad = "A&ntilde;os";
            break;
        case 2://Meses
            $unidad_edad = "Meses";
            break;
        case 3://Días
            $unidad_edad = "D&iacute;as";
            break;
    }
    /* FIN EDAD */
    ?>
    <fieldset style="width: 65%; margin: auto;">
        <legend>Datos actuales del paciente:</legend>
        <input type="hidden" name="hdd_paciente" id="hdd_paciente" value="<?= $id_paciente ?>" />
        <table style="width: 500px; margin: auto;">
            <tr>
                <td align="right" style="width:40%">Tipo de documento:</td>
                <td align="left" style="width:60%"><b><?php echo($tipo_documento); ?></b></td>
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
                <td align="left"><b><?php echo($fecha_nacimiento); ?></b></td>
            </tr>
            <tr>
                <td align="right">Edad:</td>
                <td align="left"><b><?php echo($edad . " " . $unidad_edad); ?></b></td>
            </tr>
            <tr>
                <td align="right">Tel&eacute;fonos:</td>
                <td align="left"><b><?php echo($telefonos); ?></b></td>
            </tr>
            <tr>
                <td align="right">Convenio/Plan:</td>
                <td align="left" style="color: #008CC7;"><b><?php echo($nombre_convenio . " / " . $nombre_plan); ?></b></td>
            </tr>
            <tr>
                <?php
                $nombre_estado_convenio = "";
                $background_style = "background-color:#E3B02F;";
                $color_style = "color:#FFF;";
                switch ($estado_convenio) {
                    case 1://Activo
                        $nombre_estado_convenio = "ACTIVO";
                        $background_style = "background-color:#399000;";
                        break;
                    case 2://Inactivo
                        $nombre_estado_convenio = "INACTIVO";
                        $background_style = "background-color:#E11111;";
                        break;
                    case 3://Atención especial
                        $nombre_estado_convenio = "ATENCIÓN ESPECIAL";
                        break;
                    case 4://Retirado
                        $nombre_estado_convenio = "RETIRADO";
                        break;
                }
                ?>
                <td align="right" style="<?= $background_style . $color_style ?>">Estado:</td>
                <td align="left" style="<?= $background_style . $color_style ?>"><b><?php echo($nombre_estado_convenio); ?></b></td>
            </tr>
        </table>
    </fieldset>
    <?php
    if ($tipoAccion == 1) {
        $dbOrdenesMedicas = new DbOrdenesMedicas();
        $tabla_ordenes = $dbOrdenesMedicas->ordenesMedicasByPaciente($id_paciente);
        ?>
        <div id="divOrdenesMedicas">
            <div style="width: 90%;margin: auto;text-align: left;">
                <p>Para ver el detalle seleccione una &oacute;rden m&edot;dica:</p>
            </div>

            <table id="tbl_formulas" class="paginated modal_table" style="width: 90%; margin: auto;" >
                <thead>
                    <tr>
                        <th class="th_reducido" align="center" style="width:15%;">C&oacute;digo</th>
                        <th class="th_reducido" align="center" style="">Lugar</th>
                        <th class="th_reducido" align="center" style="width:15%;">Fecha de la orden</th>
                        <th class="th_reducido" align="center" style="">Tipo</th>
                        <th class="th_reducido" align="center" style="">Con/Plan</th>                        
                        <th class="th_reducido" align="center" style="width:15%;">Cantidad de procedimientos</th>
                        <th class="th_reducido" align="center" style="">Estado</th>
                    </tr>
                </thead>
                <?php
                //$reg_menu = $menus->getMenu(72);
                //$pagina_consulta = $reg_menu["pagina_menu"];
                //$id_hc = 0;

                foreach ($tabla_ordenes as $fila_orden) {
                    $id_orden_m = $fila_orden["id_orden_m"];
                    $fecha_crea = $fila_orden["fecha_crea_auto_aux"];
                    $ind_estado = $fila_orden["ind_estado"];
                    $tipo_orden_medica = $fila_orden["tipo_orden_medica"];
                    $cantProcedimientos = $fila_orden["cantProcedimientos"];
                    $lugar = $fila_orden["nom_sede_aux"];
                    $convenioPlan = ($fila_orden["nombre_convenio"] . "-" . $fila_orden["nombre_plan"]);

                    $estadoFormula = "";
                    $colorFilaEstadoFormula = "";
                    $textoTipoOrdenMedica = "";

                    switch ($ind_estado) {
                        case 1://Nueva
                            $estadoFormula = "Pendiente";
                            break;
                        case 2://Con autorizaciones
                            $estadoFormula = "Con autorizaciones";
                            $colorFilaEstadoFormula = "background:#CEFDAF;";
                            break;
                        case 3://Cerrada
                            $estadoFormula = "Cancelada";
                            $colorFilaEstadoFormula = "background:#F79999;";
                            break;
                    }

                    switch ($tipo_orden_medica) {
                        case 1://Directa desde UT
                            $textoTipoOrdenMedica = "Directa desde UT";
                            break;
                        case 2://Homologada
                            $textoTipoOrdenMedica = "Homologada";
                            break;
                    }
                    ?>
                    <tr onclick="seleccionar_ordenMedica(<?= $id_orden_m ?>)" style="<?= $colorFilaEstadoFormula ?>">
                        <td class="td_reducido" align="center" style=""><?php echo($id_orden_m); ?></td>
                        <td class="td_reducido" align="center" style=""><?= $lugar ?></td>
                        <td class="td_reducido" align="center" style=""><?php echo($fecha_crea); ?></td>
                        <td class="td_reducido" align="center" style=""><?php echo($textoTipoOrdenMedica); ?></td>
                        <td class="td_reducido" align="center" style=""><?php echo($convenioPlan); ?></td>
                        <td class="td_reducido" align="center" style=""><?php echo($cantProcedimientos); ?></td>
                        <td class="td_reducido" align="center" style=""><?php echo($estadoFormula); ?></td>

                    </tr>
                    <?php
                }
                ?>
            </table>
            <table style="width: 70%;margin: 0 auto;">
                <tbody><tr>
                        <td colspan="6">
                            <hr>Tabla de colores:
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" disabled="" style="background: #F7F6F6;width: 30px;margin: 0;padding: 0;">
                        </td>
                        <td style="text-align: left;">
                            <span>Pendiente</span>
                        </td>
                        <td>
                            <input type="text" disabled="" style="background: #CEFDAF;width: 30px;margin: 0;padding: 0;">
                        </td>
                        <td style="text-align: left;">
                            <span>Con autorizaciones</span>
                        </td>

                        <td>
                            <input type="text" disabled="" style="background: #F79999;width: 30px;margin: 0;padding: 0;">
                        </td>
                        <td style="text-align: left;">
                            <span>Cancelada</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <script id="ajax">
                //<![CDATA[ 
                $(function () {
                    $("#tbl_formulas", "table").each(function (i) {
                        $(this).text(i + 1);
                    });

                    $("table.paginated").each(function () {
                        var currentPage = 0;
                        var numPerPage = 10;
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
        </div>
        <?php
    } else {
        ?>
        <script id="ajax">
            /* Ciclo para medicamentos */
            for (var i = 0; i < 10; i++) {
                initCKEditorOptom("frecAdmMed_" + i);
            }
        </script>
        <?php
        //$class_ordenes_remisiones->getFormularioOrdenarMedicamentos(NULL, $id_paciente, 2, 1);
        $class_ordenes_remisiones->getFormularioOrdenesMedicas(NULL, $id_paciente, 2, 1);
    }
    ?>
    <br /><br />
    <?php
}

switch ($opcion) {
    case "1": //Consultar HC del paciente
        $txt_paciente = $_POST["txt_paciente"];
        $tipoAccion = $_POST["tipoDeAccion"];

        if ($tipoAccion == 1) {//Buscar autorizaciones
            $tabla_pacientes = $dbOrdenesMedicas->consultarOrdenesMedicasPacientes($txt_paciente);
        } else {//Homologar
            $tabla_pacientes = $dbOrdenesMedicas->consultarPacientesHomologarOrdenMedica($txt_paciente);
            ?>
            <h3>Homologar orden m&eacute;dica</h3>
            <?php
        }

        $cantidad_pacientes = count($tabla_pacientes);

        if ($cantidad_pacientes == 1) {//1 registro
            $id_paciente = $tabla_pacientes[0]["id_paciente"];
            $nombre_1 = $tabla_pacientes[0]["nombre_1"];
            $nombre_2 = $tabla_pacientes[0]["nombre_2"];
            $apellido_1 = $tabla_pacientes[0]["apellido_1"];
            $apellido_2 = $tabla_pacientes[0]["apellido_2"];
            $numero_documento = $tabla_pacientes[0]["numero_documento"];
            $tipo_documento = $tabla_pacientes[0]["tipo_documento"];
            $fecha_nacimiento = $tabla_pacientes[0]["fecha_nac_persona"];
            $telefonos = $tabla_pacientes[0]["telefono_1"] . " - " . $tabla_personas[0]["telefono_2"];
            $nombres_apellidos = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
            $edad_paciente = $tabla_pacientes[0]["edadPaciente"];
            $nombre_convenio = $tabla_pacientes[0]["nombreConvenioAux"];
            $nombre_plan = $tabla_pacientes[0]["nombrePlanAux"];
            $estado_convenio = $tabla_pacientes[0]["status_convenio_paciente"];

            ver_formulaciones($tabla_pacientes[0]["id_paciente"], $nombres_apellidos, $numero_documento, $tipo_documento, $telefonos, $fecha_nacimiento, $edad_paciente, $nombre_convenio, $nombre_plan, $estado_convenio, $tipoAccion);
        } else if ($cantidad_pacientes > 1) {//Múltiples registros
            ?>
            <table id="tabla_persona_hc"  border="0" class="paginated modal_table" style="width: 70%; margin: auto;">
                <thead>
                    <tr class="headegrid">
                        <th class="headegrid" align="center">Documento</th>	
                        <th class="headegrid" align="center">Paciente</th>
                    </tr>
                    <?php
                    foreach ($tabla_pacientes as $fila_paciente) {
                        $idPaciente = $fila_paciente["id_paciente"];
                        $nombre_1 = $fila_paciente["nombre_1"];
                        $nombre_2 = $fila_paciente["nombre_2"];
                        $apellido_1 = $fila_paciente["apellido_1"];
                        $apellido_2 = $fila_paciente["apellido_2"];
                        $numero_documento = $fila_paciente["numero_documento"];
                        $nombres_apellidos = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
                        $telefonos = $fila_paciente["telefono_1"];
                        $fechaNacimiento = $fila_paciente["fecha_nac_persona"];
                        $edad = $fila_paciente["edadPaciente"];
                        $nombre_convenio = $fila_paciente["nombreConvenioAux"];
                        $nombre_plan = $fila_paciente["nombrePlanAux"];
                        $estadoConvenio = $fila_paciente["status_convenio_paciente"];
                        $tipo_documento = $fila_paciente["tipo_documento"];
                        ?>
                        <tr class="celdagrid" onclick="ver_ordenes_paciente(<?= $idPaciente ?>, '<?= $nombres_apellidos ?>', '<?= $numero_documento ?>', '<?= $tipo_documento ?>', '<?= $telefonos ?>', '<?= $fechaNacimiento ?>', '<?= $edad ?>', '<?= $nombre_convenio ?>', '<?= $nombre_plan ?>', '<?= $estadoConvenio ?>', <?= $tipoAccion ?>)">
                            <td align="left"><?php echo $numero_documento; ?></td>	
                            <td align="left"><?php echo $nombres_apellidos; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </thead>                
            </table>
            <?php
        } else if ($cantidad_pacientes == 0) {//No hay datos
            if ($tipoAccion == 1) {
                ?>
                <div class='msj-vacio'>
                    <p>No se encontraron ordenes m&eacute;dicas para el paciente</p>
                </div>
                <?php
            } else {
                ?>

                <div class='msj-vacio'>
                    <p>!El paciente no se encuentra en la base de datos¡</p>
                    <div>
                        <input class="btnPrincipal" type="button" value="Nuevo paciente" id="btn_consultar" name="btn_consultar" onclick="nuevo_paciente_btn();" value="2" />
                    </div>
                </div>               
                <?php
            }
        }
        break;

    case "2":
        $idOrdenMedica = $utilidades->str_decode($_POST["idOrdenMedica"]);
        $ordenMedica = $dbOrdenesMedicas->getOrdenMedicaActivaById($idOrdenMedica);
        $lista_procedimientos = $dbOrdenesMedicas->getProcedimientosActivosOrdenMedicaByIdOrdenMedica($idOrdenMedica);
        $lista_autorizaciones = $dbAutorizaciones->getAutorizacionesByOrdenMedica($idOrdenMedica);
		
        $cantidadProcedimientos = count($lista_procedimientos);
        $cantidadAutorizaciones = count($lista_autorizaciones);
        $estadoOrdenMedica = "";
        $indVisibleBtnDespachar = "";
        $displayDiasTranscurridos = "";
        $indEstadoConvenio = $ordenMedica['ind_activo_convenio'];
        $indEstadoPlan = $ordenMedica['ind_activo_plan'];

        switch ($ordenMedica["ind_estado"]) {
            case 1://Activa
                $estadoOrdenMedica = "Activa";
                break;
            case 2://Con autorizaciones
                $estadoOrdenMedica = "Con autorizaciones";
                $colorFilaEstadoFormula = "color:#D8A31E;";
                break;
            case 3://Inactiva
                $estadoOrdenMedica = "Inactiva";
                $colorFilaEstadoFormula = "color:#399000;";
                $indVisibleBtnDespachar = "display:none";
                $displayDiasTranscurridos = "display:none";
                break;
        }
		
        $lugar = $ordenMedica["lugar_aux"];

        $tipo_ordenMedica_aux = $ordenMedica["tipo_orden_medica"];
        $tipo_ordenMedica_texto = "";
        switch ($tipo_ordenMedica_aux) {
            case 1://Directa desde UT
                $tipo_ordenMedica_texto = "Directa desde UT";
                break;
            case 2://Homologada
                $tipo_ordenMedica_texto = "Homologada";
                break;
        }
        ?>
        <div style="width: 90%; margin: 0 auto;padding: 5px 0px">
            <div style="text-align:center;">
                <input class="btnPrincipal" type="button" value="Volver" id="btn_consultar" name="btn_consultar" onclick="btnRegresar();" />
                <input type="hidden" id="hddIdPaciente" name="hddIdPaciente" value="<?= $ordenMedica["numero_documento"] ?>" />              
                <input type="hidden" id="hdd_idProfesional" name="hdd_idProfesional" value="<?= $ordenMedica["profesional_aux"] ?>" />
            </div>
            <div>
                <h3>Orden m&eacute;dica</h3>
            </div>
            <div>
                <table style="width: 100%;">
                    <tr>
                        <td>
                            <table style="border: 1px solid #333;">
                                <tr>                                    
                                    <td align="left" style="font-size: 24pt;text-align: center;">
                                        <b><?php echo($ordenMedica["id_orden_m"]); ?></b>
                                        <input type="hidden" value="<?php echo($ordenMedica["id_orden_m"]); ?>" id="hddIdOrdenMedica" name="hddIdOrdenMedica" />
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" >Código de la orden m&eacute;dica:</td>
                                </tr>                                
                            </table>
                        </td>
                        <td>
                            <table style="border: 1px solid #333;">
                                <tr>
                                    <td align="right">Conevio/Plan:</td>
                                    <td align="left"><b style="color: #DD504F;"><?php echo($ordenMedica['nombreConvenioAux'] . "/" . $ordenMedica['nombrePlanAux']); ?></b></td>
                                </tr>
                                <tr>
                                    <td align="right">Fecha de la orden</td>
                                    <td align="left"><b><?php echo($ordenMedica["fechaOrdenMedica"]); ?></b></td>
                                </tr>
                                <tr>
                                    <td align="right">Cantidad de procedimientos:</td>
                                    <td align="left"><b><?php echo($cantidadMedicamentos); ?></b></td>
                                </tr>
                                <tr style="<?= $displayDiasTranscurridos ?>">
                                    <td align="right">D&iacute;as transcurridos desde la creaci&oacute;n de la orden:</td>
                                    <td align="left"><b><?php echo($ordenMedica["diasTranscurridos"]); ?> D&iacute;as</b></td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table style="border: 1px solid #333;">
                                <tr>                                    
                                    <td align="left" style="font-size: 24pt;text-align: center;<?= $colorFilaEstadoFormula ?>"><b><?php echo($estadoOrdenMedica); ?></b></td>
                                </tr>
                                <tr>
                                    <td align="right">Estado de la orden</td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                    <?php
                    if ($tipo_ordenMedica_aux == 2) {//Homologada
                        ?>
                        <tr>
                            <td colspan="">
                                <span style="font-weight: bold;">Observación de la homologaci&oacute;n:</span>
                            </td>
                            <td colspan="2" style="text-align: left;">
                                <span style="color: #BE8F1A;"><?= $ordenMedica["observ_orden_m"] ?></span>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
            <div style="text-align:center;">
                <p>
                    Tipo: <span style="color: #399000;font-weight: bold;"><?= $tipo_ordenMedica_texto ?></span>
                    <br />
                    Lugar: <span style="color: #399000;font-weight: bold;"><?= $lugar ?></span>
                </p>
                <input class="btnPrincipal" type="button" value="Imprimir orden m&eacute;dica" id="btnDespachar" name="btnDespachar" onclick="imprimir_orden_medica_autorizaciones();" />
            </div>
            <div>
                <h5 style="text-align: left;">Procedimientos ordenados:</h5>
                <table id="tablaPrecios" class="modal_table">
                    <tbody><tr id="tabla_encabezado">
                            <th style="width:5%;" class="th_reducido">C&oacute;d. procedimiento/paquete</th>
                            <th style="width:8%;" class="th_reducido">Tipo</th>
                            <th style="width:23%;" class="th_reducido">Nombre procedimiento</th>
                            <th style="width:5%;" class="th_reducido">Ojo</th>
                            <th style="width:5%;" class="th_reducido">Cantidad</th>
                            <th style="width:5%;" class="th_reducido">Acciones</th>
                        </tr>
                        <?php
                        $contador = 1;

                        $indDisplayAcciones = false;
                        $displayAcciones = "display:none;";
                        if ($tipo_acceso_menu == 2 && $indEstadoConvenio == 1 && $indEstadoPlan == 1) {
                            $indDisplayAcciones  = true;
                        }
                        
                        if ($indDisplayAcciones) {
                            $displayAcciones = "display:initial;";
                        }

                        foreach ($lista_procedimientos as $procedimiento) {
                            $tipoProcedimiento = $procedimiento["tipo_proc_orden_m_det"];
                            $ojo = $procedimiento["ojo_orden_m_det"];
                            $cant_orden_m_det = $procedimiento["cant_orden_m_det"];
                            $id_orden_m_det = $procedimiento["id_orden_m_det"];
                            $codigoProcedimiento = "";
                            $nombreProcedimiento = "";
                            $textoOjo = "";
                            $nombreTipoProcedimiento = "";

                            if ($tipoProcedimiento == 1) {//Si es un procedimiento
                                $nombreProcedimiento = $procedimiento["nom_paquete_p"];
                                $nombreTipoProcedimiento = "Paquete";
                                $codigoProcedimiento = $procedimiento["id_paquete_p"];
								$codigoPaquete = $procedimiento["id_paquete_p"];
                            } else if ($tipoProcedimiento == 2) {//Si es un paquete
                                $nombreProcedimiento = $procedimiento["nombre_procedimiento"];
                                $nombreTipoProcedimiento = "Procedimiento";
                                $codigoProcedimiento = $procedimiento["cod_procedimiento"];
                            }

                            switch ($ojo) {
                                case 1://AO (Ambos ojos)
                                    $textoOjo = "AO";
                                    break;
                                case 2://OI (Ojo izquierdo)
                                    $textoOjo = "OI";
                                    break;
                                case 3://OD (Ojo derechos)
                                    $textoOjo = "OD";
                                    break;
                                case 4://No aplica
                                    $textoOjo = "No aplica";
                                    break;
                            }

                            $indDisable = "disabled";
                            if ($medicamento["cantidad_pendiente"] > 0) {
                                $indDisable = "";
                            }
                            ?>
                            <tr title="<?= strip_tags($medicamento["forma_admin"]) ?>">
                                <td style="text-align: center;" class="td_reducido">
                                    <?= $codigoProcedimiento ?>
                                    <input type="hidden" id="hddIdFormulaMedicamentoDet<?= $contador ?>" name="hddIdFormulaMedicamentoDet<?= $contador ?>" value="<?= $procedimiento["id_orden_m_det"] ?>" />
                                </td>                                
                                <td style="" class="td_reducido">
                                    <?= $nombreTipoProcedimiento ?>
                                </td>
                                <td style="text-align: left;" class="td_reducido">
                                    <?= $nombreProcedimiento ?>
                                </td>
                                <td class="td_reducido">
                                    <?= $textoOjo ?>                                    
                                </td>                                
                                <td class="td_reducido">
                                    <?= $cant_orden_m_det ?>                                    
                                </td>
                                <td class="td_reducido">
                                    <img src="../imagenes/add_elemento.png" style="<?= $displayAcciones ?>" class="img_button no-margin" onclick="seleccionar_orden_medica_det(<?= $id_orden_m_det ?>);" />
                                </td>
                            </tr>
                            <?php
                            $contador ++;
                        }
                        ?>
                    </tbody>
                </table>
                <br /><br /><br />
            </div>
            <div>
                <?php
                if ($indDisplayAcciones) {
                    ?>
                    <div style="text-align:center;">
                        <input type="button" id="btnDespachar" name="btnDespachar" class="btnPrincipal" value="Agregar &iacute;tem" onclick="autorizar_procedimientos_adicionales();" />
                    </div>
                    <?php
                }
                ?>

            </div>
            <?php
            if ($cantidadAutorizaciones >= 1) {
                ?>
                <div>
                    <h5 style="text-align: left;">Autorizaciones realizadas:</h5>                   
                    <table style="width:100%;" id="tablaAutorizaciones" class="modal_table">
                        <tbody><tr id="tabla_encabezado">
                                <th style="width:5%;" class="th_reducido">&nbsp;Selecci&oacute;n&nbsp;</th>
                                <th style="width:5%;" class="th_reducido">Cod. autorizaci&oacute;n</th>
                               <!-- <th style="width:5%;" class="th_reducido">Tipo</th>   -->                    
                                <th style="width:30%;" class="th_reducido">Procedimiento</th>
                                <th style="width:5%;" class="th_reducido">Autorizado</th>
                                <th style="width:5%;" class="th_reducido">Ojo</th>                           
                                <th style="width:5%;" class="th_reducido">Lugar</th>
                                <th style="width:5%;" class="th_reducido">Convenio</th>
                                <th style="width:5%;" class="th_reducido">Plan</th>
                                <th style="width:5%;" class="th_reducido">Acciones</th>
                            </tr>
                            <?php
                            $contador = 1;
                            $banderaBtnLiquidar = false;
                            foreach ($lista_autorizaciones as $autorizacion) {
                                $codigoAutorizacion = $autorizacion["id_auto"];
                                $tipoProcedimiento = $autorizacion["tipo_proc_orden_m_det"];

                                $lugar = $autorizacion["nombre_detalle"];
                                $fechaAutorizacion = $autorizacion["fechaAutorizacion"];
                                $ojo = $autorizacion["ojo_auto"];

                                $textoOjo = "";
                                $codigoProcedimiento = "";
                                $nombreProcedimiento = "";
                                $nombreTipoProcedimiento = "";
                                $cant = $autorizacion["cant"];

                                if ($tipoProcedimiento == 1) {//Si es un paquete
                                    $nombreTipoProcedimiento = "Paquete";
                                    $nombreProcedimiento = $autorizacion["id_paquete_p"] . " - " . $autorizacion["nom_paquete_p"];
                                } else if ($tipoProcedimiento == 2) {//Si es un procedimiento
                                    $nombreProcedimiento = $autorizacion["cod_procedimiento"] . " - " . $autorizacion["nombre_procedimiento"];
                                    $nombreTipoProcedimiento = "Procedimiento";
                                }

                                switch ($ojo) {
                                    case 1://AO (Ambos ojos)
                                        $textoOjo = "AO";
                                        break;
                                    case 2://OI (Ojo izquierdo)
                                        $textoOjo = "OI";
                                        break;
                                    case 3://OD (Ojo derechos)
                                        $textoOjo = "OD";
                                        break;
                                    case 4://No aplica
                                        $textoOjo = "No aplica";
                                        break;
                                }

                                $backgroundAux = "";
                                $displaySeleccionar = "display: none;";
                                switch ($autorizacion["ind_estado_auto"]) {
                                    case 0://Des Autorizado
                                        $backgroundAux = "background: #F79999;";
                                        break;
                                    case 1://Autorizado
                                        $backgroundAux = "background: #F0C97C;";
                                        $displaySeleccionar = "display: inline;";
                                        $banderaBtnLiquidar = true;
                                        break;
                                    default://Autorizado con pagos a SIESA
                                        $backgroundAux = "background: #CEFDAF;";
                                        break;
                                }
                                ?>
                                <tr style="<?= $backgroundAux ?>">
                                    <td class="td_reducido">
                                        <input type="checkbox" style="<?= $displaySeleccionar ?>" id="auto_<?= $contador ?>" value="<?= $codigoAutorizacion ?>">
                                    </td>
                                    <td style="text-align: center;" class="td_reducido">
                                        <?= $codigoAutorizacion ?>                                        
                                    </td>
                                   <!-- <td style="" class="td_reducido">
                                        <?= $nombreTipoProcedimiento ?>
                                    </td>
									-->
                                    <td style="text-align: left;" class="td_reducido">
                                        <?= $nombreProcedimiento ?>
                                    </td>
                                    <td class="td_reducido">
                                        <?= $fechaAutorizacion ?>                                    
                                    </td>                                
                                    <td class="td_reducido">
                                        <?= $textoOjo ?>                                    
                                    </td>                                  
                                    <td class="td_reducido">
                                        <?= $lugar ?>                                    
                                    </td>
                                    <td class="td_reducido">
                                        <?= $combo->getComboDb("cmb_convenio_" . $contador, $autorizacion["id_convenio"], $dbConvenios->getListaConveniosActivosAutorizaciones(), "id_convenio, nombre_convenio", "--Seleccione--", "seleccionar_convenio('cmb_convenio_" . $contador . "', " . $contador . ");", "", $displaySeleccionar, "") ?>
                                    </td>
                                    <td class="td_reducido">
                                        <div id="d_cmb_plan">
                                            <?= $combo->getComboDb("cmb_plan_" . $contador, $autorizacion["id_plan"], $dbPlanes->getListaPlanesActivosAutorizaciones($autorizacion["id_convenio"]), "id_plan, nombre_plan", "--Seleccione--", "", "", $displaySeleccionar, "") ?>
                                        </div>                                        
                                    </td>
                                    <td class="td_reducido">
                                        <img src="../imagenes/add_elemento.png" style="<?= $displayAcciones ?>" class="img_button no-margin" onclick="seleccionar_autorizacion(<?= $codigoAutorizacion ?>);" />
                                    </td>
                                </tr>
                                <?php
                                $contador ++;
                            }
                            ?>                        
                        </tbody>
                    </table>
                    <?php                                                              
                    if ($banderaBtnLiquidar) {
                        if ($indDisplayAcciones) {
                            ?>
                            <div>
                                <div style="text-align:center;">
                                    <br>
                                    <input type="button" id="btn_liquidar_autorizacion" name="btn_liquidar_autorizacion" value="Liquidar autorizaci&oacute;n" class="btnPrincipal" onclick="liquidar_autorizacion();" />
                                    <input type="hidden" id="id_paquete_procedimiento" name="id_paquete_procedimiento" value="<?= $codigoPaquete; ?>"/>
                                    <div id="d_autorizar" style="display:none"></div>
                                </div>
                            </div>
                            <?php
                        }else{
                            ?>
                            <div>
                                <h4 style="color: #D32F24;">El convenio o el plan al que pertenece la autorizaci&oacute;n se encuentra inactivo</h4>
                            </div>
                            <?php
                        }
                    }
                    ?>
                    <br />
                    <table style="width: 70%;margin: 0 auto;">
                        <tbody><tr>
                                <td colspan="6">
                                    <hr>Tabla de colores:
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" disabled="" style="background: #F0C97C;width: 30px;margin: 0;padding: 0;">
                                </td>
                                <td style="text-align: left;">
                                    <span>Autorizado sin pago</span>
                                </td>
                                <td>
                                    <input type="text" disabled="" style="background: #CEFDAF;width: 30px;margin: 0;padding: 0;">
                                </td>
                                <td style="text-align: left;">
                                    <span>Autorizado con pago</span>
                                </td>
                                <td>
                                    <input type="text" disabled="" style="background: #F79999;width: 30px;margin: 0;padding: 0;">
                                </td>
                                <td style="text-align: left;">
                                    <span>Cancelada</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
        break;

    case "3"://Orden procedimiento Detalle
        $idOrdenMedicaDet = $utilidades->str_decode($_POST["idOrdenMedicaDet"]);
        $ordenMedicaDet = $dbOrdenesMedicas->getOrdenMedicaDetById($idOrdenMedicaDet);
		
        $tipoProcedimiento = $ordenMedicaDet["tipo_proc_orden_m_det"];
        $datos_clinicos = $ordenMedicaDet["datos_clinicos"];
        $ojo = $ordenMedicaDet["ojo_orden_m_det"];
        $codigoProcedimiento = "";
        $nombreProcedimiento = "";
        $nombreTipoProcedimiento = "";
        $textoOjo = "";
        $id_hc = $ordenMedicaDet["id_hc"];
        $textoProcedimientos = "";
        $textoDiagnosticoAsociado = "";
				
		$display_esp = "none;"; 
		$display_ins = "none;";
		$display_ane= "none;";
		
        if (strlen($id_hc) <= 0) {//Si no hay HC
            $textoDiagnosticoAsociado = "*" . $ordenMedicaDet["ciex_orden_m_det"] . " - " . $ordenMedicaDet["nombreCiex"];
        } else {//Sí hay HC
            $diagnosticos = $dbDiagnosticos->getHcDiagnostico($id_hc);
            foreach ($diagnosticos as $diagnostico) {
                $textoDiagnosticoAsociado .= "*" . $diagnostico["codciexori"] . " - " . $diagnostico["nombre"] . "\n\n";
            }
        }

        $cantidad = $ordenMedicaDet["cant_orden_m_det"];
        $displayBtnSeleccionaProveedor = "";
        $displayProcedimientos = "display: none;";
        if ($tipoProcedimiento == 1) {//Si es un Paquete
			$ind_especialista = $ordenMedicaDet["ind_auto_honorarios_medicos"]; 
			$ind_derechos_sala = $ordenMedicaDet["ind_auto_derechos_sala"];
			$ind_anestesia = $ordenMedicaDet["ind_auto_anestesia"];
		
			if($ind_especialista == "1"){$check_esp="checked"; $display_esp= "display;";}else{$display_esp = "none;";}
			if($ind_derechos_sala == "1"){$check_sal="checked"; $display_sal= "display;";}else{$display_sal = "none;";}
			if($ind_anestesia == "1"){$check_ane="checked"; $display_ane = "display;";}else{$display_ane = "none;";}

            $nombreProcedimiento = $ordenMedicaDet["nom_paquete_p"];
            $nombreTipoProcedimiento = "Paquete";
            $codigoProcedimiento = $ordenMedicaDet["id_paquete_p"];
            $displayBtnSeleccionaProveedor = "display: none;";
            $displayProcedimientos = "display: inline;";

            $lista_procedimientos = $dbPaquetesProcedimientos->getProcedimientosByPaquete($codigoProcedimiento);
            foreach ($lista_procedimientos as $procedimiento) {
                $textoProcedimientos .= "+ " . $procedimiento["cod_procedimiento"] . " - " . $procedimiento["nombre_procedimiento"] . "<br />";
            }
        } else if ($tipoProcedimiento == 2) {//Si es un procedimiento
            $nombreProcedimiento = $ordenMedicaDet["nombre_procedimiento"];
            $nombreTipoProcedimiento = "Procedimiento";
            $codigoProcedimiento = $ordenMedicaDet["cod_procedimiento"];
        }

        switch ($ojo) {
            case 1://AO (Ambos ojos)
                $textoOjo = "AO";
                break;
            case 2://OI (Ojo izquierdo)
                $textoOjo = "OI";
                break;
            case 3://OD (Ojo derechos)
                $textoOjo = "OD";
                break;
            case 4://No aplica
                $textoOjo = "No aplica";
                break;
        }
        ?>
        <div class="encabezado">
            <h3>Detalles del procedimiento</h3>
        </div>
        <br>
        <div>
            <input type="hidden" id="hddIdOrdenMedicaDet" name="hddIdOrdenMedicaDet" value="<?= $idOrdenMedicaDet ?>" />
            <input type="hidden" id="hddNombreTipoProcedimiento" name="hddNombreTipoProcedimiento" value="<?= $nombreTipoProcedimiento ?>" />
            <table style="width:95%; margin:auto;">
                <tr>
                    <td style="width:50%;">
                        <table style="width:100%">                           
                            <tr>
                                <td align="left" style="width:15%;"><b><?= $nombreTipoProcedimiento ?>:</b></td>
                                <td align="left" class="verde" style="width:85%;" colspan="3">
                                    <?= $codigoProcedimiento . " - " . $nombreProcedimiento ?>
                                    <input type="hidden" id="hddNombreProcedimiento" name="hddNombreProcedimiento" value="<?= $nombreProcedimiento ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td align="left"><b>Cantidad:</b></td>
                                <td align="left" class="verde" style="width:35%;"><?= $cantidad ?></td>
                                <td align="left" style="width:10%;"><b>Ojo:</b></td>
                                <td align="left" class="verde" style="width:40%;">
                                    <?= $textoOjo ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="left" colspan="4"><b>Datos cl&iacute;nicos:</b></td>
                            </tr>
                            <tr>
                                <td style="text-align:justify;" colspan="4">
                                    <?= $datos_clinicos ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="left" colspan="4"><b>Justificaci&oacute;n/Observaciones:</b></td>
                            </tr>
                            <tr>
                                <td style="text-align:justify;" colspan="4">
                                    <?= $datos_clinicos ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="width:50%;">
                        <table style="width:100%;">
                            <tr>
                                <td align="left"><b>Diagn&oacute;stico asociado:</b></td>
                            </tr>
                            <tr>
                                <td align="left">
                                    <?= $textoDiagnosticoAsociado; ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="left">
                                    <div style="<?= $displayProcedimientos ?>">
                                        <b>Procedimientos:</b><br />
                                        <?= $textoProcedimientos; ?>
                                    </div>

                                </td>                                
                            </tr>                            
                        </table>
                    </td>
                </tr>
                <tr>
                  <td align="center" colspan="2">
                  	<table  style="width:75%; margin:auto; margin-top:10px;">
                     <tr>
                          <td><input onchange="mostrarProveedorModal(1);" type="checkbox" name="checkAutoDerechosSala" id="checkAutoDerechosSala" <?=$check_sal?>>&nbsp;Derechos de sala
                          </td>  
                          <td><input onchange="mostrarProveedorModal(2);" type="checkbox" name="checkAutoAnestesiologo" id="checkAutoAnestesiologo" <?=$check_ane?> >&nbsp;Anestesi&oacute;logo
                          </td>  
                          <td><input onchange="mostrarProveedorModal(3);" type="checkbox" name="checkAutoEspecialista" id="checkAutoEspecialista"  <?=$check_esp?> >&nbsp;Especialista
                          </td> 
                      </tr>
                    </table>
                  </td>                           
          	    </tr>
                <tr>
                    <td align="center" colspan="2">
					<br><b>Proveedores</b>
                        <table style="width:75%; margin:auto;">
                            <tr id="tr_codProvModal_sala"  style="display:<?= $display_sal?>">
                                <td align="right" style="width:100px;"><b>Sala:</b></td>
                                <td align="left" style="width:600px;">
                                    <input type="hidden" id="codProvModalSala" value="" />
                                    <span id="nomProvModalSala"></span>
                                </td>
                                <td align="center" style="width:5%;">
                                    <div class="d_buscar" onclick="btnVentanaBuscarProveedor(1);"></div>
                                </td>
                            </tr>
							<tr id="tr_codProvModal_anestesia" style="display:<?= $display_ane?>">
                                <td align="right" style="width:100px;"><b>Anestesia:</b></td>
                                <td align="left" style="width:600px;">
                                    <input type="hidden" id="codProvModalAnestesia" value="" />
                                    <span id="nomProvModalAnestesia"></span>
                                </td>
                                <td align="center" style="width:5%;">
                                    <div class="d_buscar" onclick="btnVentanaBuscarProveedor(2);"></div>
                                </td>
                            </tr>
							<tr id="tr_codProvModal_especialista" style="display:<?= $display_esp?>">
                                <td align="right" style="width:100px;"><b>Especialista:</b></td>
                                <td align="left" style="width:600px;">
                                    <input type="hidden" id="codProvModalEspecialista" value="" />
                                    <span id="nomProvModalEspecialista"></span>
                                </td>
                                <td align="center" style="width:5%;">
                                    <div class="d_buscar" onclick="btnVentanaBuscarProveedor(3);"></div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="2">
                        <h5 class="texto-rojo">La fecha de vencimiento será asignada automáticamente con un plazo de 3 meses a partir de la fecha actual</h5>
                    </td>                    
                </tr>             
                <tr>
                    <td align="center" colspan="2">
                        <br>
                        <input class="btnPrincipal" type="button" value="Autorizar" id="btn_consultar" name="btn_consultar" onclick="autorizar_procedimiento();" />
                    </td>
                </tr>
                <?php
                if ($tipoProcedimiento == 1) {//Si es un Paquete
                    ?>
                    <tr>
                        <td colspan="2">
                            <h5>Autorizaciones adicionales requeridas</h5>
                            <h6>Deben agregarse de forma manual</h6>
                            <ol style="list-style:aliceblue; text-align:left;">
                                <?php
                                if ($ordenMedicaDet["ind_auto_honorarios_medicos"] == 1) {
                                    ?>
                                    <li> Honorarios m&eacute;dicos</li>
                                    <?php
                                }
                                if ($ordenMedicaDet["ind_auto_anestesia"] == 1) {
                                    ?>
                                    <li> Anestesiólogo</li>
                                    <?php
                                }
                                if ($ordenMedicaDet["ind_auto_ayudantia"] == 1) {
                                    ?>
                                    <li> Ayudantía</li>
                                    <?php
                                }
                                if ($ordenMedicaDet["ind_auto_derechos_sala"] == 1) {
                                    ?>
                                    <li> Derechos de sala</li>
                                    <?php
                                }
                                ?>
                            </ol>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
        <?php
        break;

    case "4"://Orden procedimiento Detalle
        $idOrdenMedicaDet = $utilidades->str_decode($_POST["idOrdenMedicaDet"]);
        $textoTipo = $utilidades->str_decode($_POST["textoTipo"]);
        $textoNombre = $utilidades->str_decode($_POST["nombreProcedimiento"]);
        ?>
        <input type="hidden" id="hddIdOrdenMedicaDet" name="hddIdOrdenMedicaDet" value="<?= $idOrdenMedicaDet ?>" />
        <div class="encabezado">
            <h3>Seleccione el proveedor y asigne la vigencia de la autorización</h3>
        </div>
        <br>
        <div>           
            <table style="width: 60%;margin: 0 auto;">
                <tr>
                    <td style="text-align: right;width: 40%;font-weight: bold;">
                        Tipo:
                    </td>
                    <td style="text-align: left;color: #399000;">
                        <?= $textoTipo ?>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right;font-weight: bold;">
                        Nombre:
                    </td>
                    <td style="text-align: left;color: #399000;">
                        <?= $textoNombre ?>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center;font-weight: bold;" colspan="2">
                        <br>
                        <h5>Seleccione el proveedor:</h5>
                    </td>                    
                </tr>
                <tr>
                    <td style="text-align: center;" colspan="2">
                        <?php
                        $proveedores = $dbOrdenesMedicas->getProveedoresActivosProcedimientos();
                        $combo->getComboDb("cmbProveedor", "", $proveedores, "id_proveedor_procedimiento, nombre_proveedor_procedimiento", "Seleccione", "cargarDatosProveedor()", $activo_combo, $estilo, $index, $class);
                        ?>
                    </td>                    
                </tr>
                <tr>
                    <td style="text-align: center;" colspan="2">
                        <div id="divSeleccionarProveedor">
                            <table>
                                <tr>
                                    <td colspan="" style="text-align: right;">
                                        Nombre:
                                    </td>
                                    <td colspan="3" style="color: #012996;text-align: justify;">

                                        <input type="text" disabled value="" />                                    
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">
                                        Documento/NIT:
                                    </td>
                                    <td style="color: #012996;text-align: justify;">
                                        <input type="text" disabled value="" />                                    
                                    </td>
                                    <td style="text-align: right;"> 
                                        Tel&eacute;fono:
                                    </td>
                                    <td style="color: #012996;text-align: justify;">
                                        <input type="text" disabled value="" />                                    
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="" style="text-align: right;">
                                        Direcci&oacute;n:
                                    </td>
                                    <td colspan="3" style="color: #012996;text-align: justify;">
                                        <input type="text" disabled value="" />                                    
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>                    
                </tr>
                <tr>
                    <td style="text-align: center;font-weight: bold;" colspan="2">
                        <br>
                        <h5 style="color: #DD5043;">La fecha de vencimiento será asignada automáticamente con un plazo de 3 meses a partir de la fecha actual</h5>
                    </td>                    
                </tr>             
                <tr>
                    <td style="text-align: center;" colspan="2">
                        <br>
                        <input class="btnPrincipal" type="button" value="Autorizar" id="btn_consultar" name="btn_consultar" onclick="autorizar_procedimiento();" />
                    </td>
                </tr>
            </table>
        </div>
        <?php
        break;

    case "5"://
        $idProveedor = $utilidades->str_decode($_POST["idProveedor"]);
        $proveedor = $dbOrdenesMedicas->getProveedorProcedimientosById($idProveedor);
        ?>
        <table>
            <tr>
                <td colspan="" style="text-align: right;">
                    Nombre:
                </td>
                <td colspan="3" style="color: #012996;text-align: justify;">
                    <input type="text" disabled value="<?= $proveedor["nombre_proveedor_procedimiento"] ?>" id="nomProvModal2" />                                    
                </td>
            </tr>
            <tr>
                <td style="text-align: right;">
                    Documento/NIT:
                </td>
                <td style="color: #012996;text-align: justify;">
                    <input type="text" disabled value="<?= $proveedor["numero_documento"] ?>" />                                    
                </td>
                <td style="text-align: right;"> 
                    Tel&eacute;fono:
                </td>
                <td style="color: #012996;text-align: justify;">
                    <input type="text" disabled value="<?= $proveedor["tel_proveedor_procedimiento"] ?>" />                                    
                </td>
            </tr>
            <tr>
                <td colspan="" style="text-align: right;">
                    Direcci&oacute;n:
                </td>
                <td colspan="3" style="color: #012996;text-align: justify;">
                    <input type="text" disabled value="<?= $proveedor["dir_proveedor_procedimiento"] ?>" />                                    
                </td>
            </tr>
        </table>
        <?php
        break;

    case "6"://Autorizacion con base en orde medica detalle
        $tipoAuto = $utilidades->str_decode($_POST["tipoAuto"]);
        $idOrdenMedicaDet = $utilidades->str_decode($_POST["idOrdenMedicaDet"]);
        $idOrdenMedica = $utilidades->str_decode($_POST["idOrdenMedica"]);
        $observacion = $utilidades->str_decode($_POST["observacion"]);
        $idProveedorSala = $utilidades->str_decode($_POST["idProveedorSala"]);
		$idProveedorAnestesiologo = $utilidades->str_decode($_POST["idProveedorAnestesiologo"]);
	  	$idProveedorEspecialista = $utilidades->str_decode($_POST["idProveedorEspecialista"]);		
		
        $rta = $dbAutorizaciones->autorizarProcedimiento($tipoAuto, $idOrdenMedica, $idOrdenMedicaDet, "", $idProveedorSala, $idProveedorAnestesiologo, $idProveedorEspecialista, $observacion, $fechaVenc, $usuarioCrea, "", "", "", $lugar, "");
        ?>
        <input type="hidden" id="hddResultadoAutorizar" name="hddResultadoAutorizar" value="<?= $rta ?>" />
        <?php
        break;

    case "7"://Orden procedimiento Detalle
        $idAutorizacion = $utilidades->str_decode($_POST["idAutorizacion"]);

        $autorizacion = $dbAutorizaciones->getAutorizacionById($idAutorizacion);

        $codigoAutorizacion = $autorizacion["id_auto"];
        $tipoProcedimiento = $autorizacion["tipo_proc_orden_m_det"];
        $ojo = $autorizacion["ojo_auto"];
        $cantidad = $autorizacion["cant"];
        $nombreProcedimiento = "";
        $textoOjo = "";
        $textoDiagnosticoAsociado = "";
        $textoProcedimientos = "";

        //Verifica si la autorizaciòn fue realizada con base en un detalle de la orden mèdica
        if (strlen($autorizacion["id_orden_m_det"]) >= 1) {
            //Verifica el tipo de orden mèdica
            if ($autorizacion["tipo_orden_medica_aux"] == 2) {//Homologada
                $textoDiagnosticoAsociado = $autorizacion["nom_ciex_ordenmedica_det_aux"];
            } else if ($autorizacion["tipo_orden_medica_aux"] == 1) {//Directa
                $diagnosticos_aux = $dbHistoriaClinica->getListaCiexAdmision($autorizacion["id_admision_aux"]);
                foreach ($diagnosticos_aux as $diagnostico) {
                    $textoDiagnosticoAsociado .= $diagnostico["codciex"] . " - " . $diagnostico["nombre"] . "\n\n";
                }
            }
        } else {
            $textoDiagnosticoAsociado = $autorizacion["nom_ciex_autorizacion_aux"];
        }

        if ($tipoProcedimiento == 1) {//Si es un paquete
            $nombreProcedimiento = $autorizacion["nom_paquete_p"];
            $codigoProcedimiento = $autorizacion["id_paquete_p"];
            $procedimiento_aux = $dbPaquetesProcedimientos->getProcedimientosByPaquete($codigoProcedimiento);
            foreach ($procedimiento_aux as $procedimiento) {
                switch ($procedimiento["tipo_producto"]) {
                    case "P":
                        $textoProcedimientos .= "+ " . $procedimiento["cod_procedimiento"] . " - " . $procedimiento["nombre_procedimiento"] . "<br />";
                        break;
                    case "I":
                        $textoProcedimientos .= "+ " . $procedimiento["cod_insumo"] . " - " . $procedimiento["nombre_insumo"] . "<br />";
                        break;
                }
            }
        } else if ($tipoProcedimiento == 2) {//Si es Procedimiento
            $nombreProcedimiento = $autorizacion["nombre_procedimiento"];
            $nombreTipoProcedimiento = "Procedimiento";
            $codigoProcedimiento = $autorizacion["cod_procedimiento"];
            $textoProcedimientos = "+ " . $codigoProcedimiento . " - " . $nombreProcedimiento . "<br />";
        }

        switch ($ojo) {
            case 1://AO (Ambos ojos)
                $textoOjo = "AO";

                break;
            case 2://OI (Ojo izquierdo)
                $textoOjo = "OI";
                break;
            case 3://OD (Ojo derechos)
                $textoOjo = "OD";
                break;
            case 4://No aplica
                $textoOjo = "No aplica";
                break;
        }
        ?>
        <div class="encabezado">
            <h3>Autorizaci&oacute;n #<?= $codigoAutorizacion ?></h3>
        </div>
        <br>
        <div>
            <h4><?= $nombreProcedimiento ?></h4>
        </div>
        <?php
        if ($autorizacion["ind_estado_auto"] == 0) {//Sí ha sido desautorizada
            ?>
            <div>
                <h5 style="color: #DD5043;margin: 0;">La autorización ha sido cancelada</h5>
            </div>
            <?php
        }
        ?>
        <div>           
            <table style="width: 85%;margin: 0 auto;">
                <tr>
                    <td align="right" style="width:28%;">
                        <b>C&oacute;digo procedimiento:</b>
                    </td>
                    <td align="left" class="verde" style="width:22%;">
                        <?= $codigoProcedimiento ?>
                        <input type="hidden" id="hdd_idautorizacion" name="hdd_idautorizacion" value="<?= $codigoAutorizacion ?>" />
                    </td>
                    <td align="left" style="width:50%;">
                        <span><b>Autorizado por:</b>&nbsp;<?= $autorizacion["usuario_auto_aux"] ?></span>
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        <b>Ojo:</b>
                    </td>
                    <td align="left" class="verde">
                        <?= $textoOjo ?>
                    </td>
                    <td align="left">
                        <span><b>Fecha:</b>&nbsp;<?= $autorizacion["fechaAutorizacion"] ?></span>
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        <b>Cantidad:</b>
                    </td>
                    <td align="left" class="verde">
                        <?= $cantidad ?>
                    </td>
                </tr>                         
                <tr>
                    <td align="left" colspan="2">
                        <span><b>Diagn&oacute;stico asociado:</b></span>
                        <br />
                        <?= $textoDiagnosticoAsociado ?>
                    </td> 
                    <td align="left" valign="bottom">
                        <b>Observaciones de la autorizaci&oacute;n:</b>
                    </td>
                </tr>
                <tr>
                    <td align="left" colspan="2">
                        <span><b>Procedimientos:</b></span>
                        <br />
                        <?= $textoProcedimientos ?>
                    </td>
                    <td align="left" valign="top">
                        <?= $autorizacion["observ_auto"] ?>
                    </td>
                </tr>
                <?php
                if ($autorizacion["ind_estado_auto"] == 0) { //Sí se encuentra desautorizado
                    ?>
                    <tr>
                        <td colspan="3">
                            <span class="texto-rojo">Nota aclaratoria:</span>
                            <br />
                            <?= $autorizacion["nota_aclaratoria_auto"] ?>
                            <br />
                            <span>Desautoriz&oacute;:&oacute;</span><span><?= $autorizacion["usuarioModifica"] . " - " . $autorizacion["fechaModificacion"] ?></span>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td align="center" colspan="3">
                        <input class="btnPrincipal" type="button" value="Imprimir autorización" id="btn_consultar" name="btn_consultar" onclick="imprimir_autorizacion();" />
                        <?php
                        //La autorización solo puede ser desautorizada por el usuario que la creó y desde la sede en que fue creada
                        if ($autorizacion["ind_estado_auto"] != 0 && $autorizacion["usuario_crea_auto"] == $_SESSION["idUsuario"] && $autorizacion["id_lugar_auto"] == $_SESSION["idLugarUsuario"] && $autorizacion["ind_estado_auto"] == 1) {
                            ?>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <input class="btnPrincipal" type="button" value="Cancelar autorizaci&oacute;n" id="btn_desautorizar" name="btn_desautorizar" onclick="desautorizar();" style="background: #DD5043 !important;" />
                            <?php
                        }
                        ?>
                    </td>
                </tr>
            </table>
            <?php
            if ($autorizacion["ind_estado_auto"] == 1) {//Sí ha sido desautorizada
                ?>
                <div style="background: #FDEBB7;padding: 3px 5px;width: 70%;margin: 0 auto;">
                    <span>NOTA: Únicamente puede cancelar la autorización el usuario que autorizó, desde la sede en que fue autorizado y si esta se encuentra con estado "Autorizado sin pago"</span>
                </div>                
                <?php
            }
            ?>
        </div>
        <?php
        break;

    case "8":
        $idAutorizacion = $utilidades->str_decode($_POST["idAutorizacion"]);
        $autorizacion = $dbAutorizaciones->getAutorizacionById($idAutorizacion);
        $paciente = $dbPacientes->getPaciente($autorizacion["id_paciente"]);
        $tipo_orden_medica_aux = $autorizacion["tipo_orden_medica_aux"];
			
		//Se realizan validaciones para imprimir un PDF para cada proveedor 
		//PDF para derechos de sala
		if(!is_null($autorizacion["id_proveedor_sala"])){
			  $proveedor_sala = $dbOrdenesMedicas->getProveedorProcedimientosById($autorizacion["id_proveedor_sala"]);				$fontSize = 7;
				$pdf = new FPDF("P", "mm", array(216, 279));
				$pdf->AliasNbPages();
				$pdf->SetMargins(10, 10, 10);
				$pdf->SetAutoPageBreak(false);
				$pdf->SetFillColor(255, 255, 255);
				$pdf->bordeMulticell = 1; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde
				$pdf->pie_pagina = false;
				$pdf->AddPage();
				$pdf->SetFont("Arial", "", $fontSize);
			//Logo
			if(!is_null($autorizacion["dir_logo_sede_det"]) && !empty($autorizacion["dir_logo_sede_det"])){
			$pdf->Image($autorizacion["dir_logo_sede_det"], 20, 7, 20);
			}
				$pdf->Cell(40, 24, "", 0, 0, "C");
				$pdf->SetFont("Arial", "B", $fontSize);
				$y_aux = 8;
				$pdf->SetY($y_aux);
				$pdf->SetX(50);
				$pdf->Cell(15, 4, ajustarCaracteres("FECHA:"), 0, 0, "L");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(50, 4, ajustarCaracteres($autorizacion["fechaAutorizacion"]), 0, 0, "L");
				$y_aux += 4;
				$pdf->SetY($y_aux);
				$pdf->SetX(50);
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(35, 4, ajustarCaracteres("FECHA DE VENCIMIENTO:"), 0, 0, "L");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(30, 4, ajustarCaracteres($autorizacion["fechaVencimiento"]), 0, 0, "L");
				$y_aux += 4;
				$pdf->SetY($y_aux);
				$pdf->SetX(50);
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(23, 4, ajustarCaracteres("ORDEN MÉDICA:"), 0, 0, "L");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(53, 4, ajustarCaracteres("" . $autorizacion["id_orden_m"]), 0, 0, "L");
				$pdf->SetY(10);
				$pdf->SetX(115);
				$pdf->SetFont("Arial", "B", ($fontSize + 8));
				$pdf->Cell(90, 10, ajustarCaracteres("AUTORIZACIÓN "), 0, 0, "C");
				$pdf->SetY(20);
				$pdf->SetX(115);
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(90, 4, ajustarCaracteres("*" . $autorizacion["id_auto"] . "*"), 0, 0, "C");
				$pdf->Ln(4);
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(192, 5, ajustarCaracteres("-- PROVEEDOR SALA DIRÍJASE A --"), 0, 1, "C");
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(27, 4, ajustarCaracteres("AUTORIZA A:"), 1, 0, "R");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(165, 4, ajustarCaracteres($proveedor_sala["nombre_proveedor_procedimiento"]), 1, 1, "L");
				$pdf->SetX($pdf->GetX());
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(27, 4, ajustarCaracteres("IDENTIFICADO CON:"), 1, 0, "L");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(69, 4, ajustarCaracteres($proveedor_sala["numero_documento"]), 1, 0, "L");
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(27, 4, ajustarCaracteres("TELÉFONO:"), 1, 0, "L");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(69, 4, ajustarCaracteres($proveedor_sala["tel_proveedor_procedimiento"]), 1, 1, "L");
				$pdf->SetX(10);
				$pdf->SetX($pdf->GetX());
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(27, 4, ajustarCaracteres("DIRECCIÓN:"), 1, 0, "R");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(165, 4, ajustarCaracteres($proveedor_sala["dir_proveedor_procedimiento"]), 1, 1, "L");
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(192, 5, ajustarCaracteres("-- PACIENTE --"), 0, 1, "C");
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(20, 4, ajustarCaracteres("DOCUMENTO:"), 1, 0, "R");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(76, 4, ajustarCaracteres($paciente["numero_documento"]), 1, 0, "C");
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(20, 4, ajustarCaracteres("NOMBRES:"), 1, 0, "R");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(76, 4, ajustarCaracteres($paciente["nombre_1"] . " " . $paciente["nombre_2"]), 1, 1, "C");
				$pdf->SetX($pdf->GetX());
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(20, 4, ajustarCaracteres("TIPO:"), 1, 0, "R");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(76, 4, ajustarCaracteres($paciente["codigoDocumento"]), 1, 0, "C");
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(20, 4, ajustarCaracteres("APELLIDOS:"), 1, 0, "R");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(76, 4, ajustarCaracteres($paciente["apellido_1"] . " " . $paciente["apellido_2"]), 1, 1, "C");
				$pdf->SetX(10);
				$pdf->SetX($pdf->GetX());
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(20, 4, ajustarCaracteres("SEXO:"), 1, 0, "R");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(10, 4, ajustarCaracteres($paciente["codigoSexo"]), 1, 0, "C");
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(20, 4, ajustarCaracteres("FECH.NAC"), 1, 0, "R");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(20, 4, ajustarCaracteres($paciente["fecha_nacimiento_t"]), 1, 0, "C");
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(10, 4, ajustarCaracteres("EDAD:"), 1, 0, "R");
				$pdf->SetFont("Arial", "", $fontSize);
				$edad = explode("/", $paciente["edad"]);
				$edadMagnitud = $edad[1];
		
			switch ($edadMagnitud) {
				case 1:
					$edadMagnitud = "Años";
					break;
				case 2:
					$edadMagnitud = "Meses";
					break;
				case 3:
					$edadMagnitud = "Días";
					break;
			}
		
				$pdf->Cell(16, 4, ajustarCaracteres($edad[0] . " " . $edadMagnitud), 1, 0, "C");
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(20, 4, ajustarCaracteres("ASEGURADOR:"), 1, 0, "R");
				$pdf->SetFont("Arial", "", $fontSize);
				$pdf->Cell(76, 4, ajustarCaracteres($paciente["nombre_convenio"]), 1, 1, "C");
				$pdf->Ln(4);
				$pdf->SetFont("Arial", "B", $fontSize);
				$pdf->Cell(20, 4, ajustarCaracteres("CÓDIGO:"), 1, 0, "C");
				$pdf->Cell(132, 4, ajustarCaracteres("PROCEDIMIENTO"), 1, 0, "C");
				$pdf->Cell(20, 4, ajustarCaracteres("LATERALIDAD"), 1, 0, "C");
				$pdf->Cell(10, 4, ajustarCaracteres("OJO"), 1, 0, "C");
				$pdf->Cell(10, 4, ajustarCaracteres("CANT"), 1, 1, "C");
	
			//Procesa el tipo de producto, Paquete o prodecimiento
			$tipo_proc_aux = $autorizacion["tipo_proc_orden_m_det"];
			$codigo_proc_aux = "";
			$nom_proc_aux = "";
			$proc_paquete_txt_aux = "";
			if ($tipo_proc_aux == 1) {//paquete
				$codigo_proc_aux = $autorizacion["id_paquete_p"];
				$proc_paquete_aux = $dbPaquetesProcedimientos->getProcedimientosByPaquete($codigo_proc_aux);
				$proc_paquete_txt_aux = " (";
				foreach ($proc_paquete_aux as $proc_paquete) {
					$proc_paquete_txt_aux .= "*" . $proc_paquete["cod_procedimiento"] . " - " . $proc_paquete["nombre_procedimiento"] . " ";
				}
				$proc_paquete_txt_aux .= ")";
				$nom_proc_aux = strtoupper($autorizacion["nom_paquete_p"]) . $proc_paquete_txt_aux;
			} else if ($tipo_proc_aux == 2) {//Procedimiento
				$codigo_proc_aux = $autorizacion["cod_procedimiento"];
				$nom_proc_aux = strtoupper($autorizacion["nombre_procedimiento"]);
			}
	
			$dominancia_ocular = $autorizacion["ojo_auto"];
			$bilateralidad = $autorizacion["bilateralidad_auto"];
			$textoOjo = "";
			switch ($dominancia_ocular) {
				case 1://AO (Ambos ojos)
					$textoOjo = "AO";
					break;
				case 2://OI (Ojo izquierdo)
					$textoOjo = "OI";
					break;
	
				case 3://OD (Ojo derechos)
					$textoOjo = "OD";
					break;
				case 4://No aplica
					$textoOjo = "NA";
					break;
			}
	
			$textoBilateralidad = "";
			switch ($bilateralidad) {
				case 1://Unilateral
					$textoBilateralidad = "UNILATERAL";
					break;
				case 2://Bilateral
					$textoBilateralidad = "BILATERAL";
					break;
				default:
					$textoBilateralidad = "NO APLICA";
					break;
			}
	
			$cantProcedimiento = $autorizacion["cant"];
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->SetWidths2(array(20, 132, 20, 10, 10));
			$pdf->SetAligns2(array("C", "L", "C", "C", "C"));
			$pdf->Row2(array(ajustarCaracteres($codigo_proc_aux), ajustarCaracteres($nom_proc_aux), $textoBilateralidad, ajustarCaracteres($textoOjo), ajustarCaracteres($cantProcedimiento))); //
			//Procesa los diagnósticos
			$diagnosticos_txt_aux = "";
			if ($tipo_orden_medica_aux == 2) {//Homologada
				$diagnosticos_txt_aux = $autorizacion["nom_ciex_autorizacion_aux"];
			} else if ($tipo_orden_medica_aux == 1) {//Directa desde UT
				$diagnosticos_aux = $dbHistoriaClinica->getListaCiexAdmision($autorizacion["id_admision_aux"]);
				foreach ($diagnosticos_aux as $diagnostico) {
					$diagnosticos_txt_aux .= $diagnostico["codciex"] . " - " . $diagnostico["nombre"] . "\n";
				}
			}
	
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->SetWidths2(array(45, 147));
			$pdf->SetAligns2(array("C", "L"));
			$pdf->Row2(array(ajustarCaracteres("SOLICITADO CON DIAGNÓSTICO:"), ajustarCaracteres($diagnosticos_txt_aux)));
			$pdf->Ln(4);
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(192, 4, ajustarCaracteres("OBSERVACIÓNES:"), 0, 1, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->bordeMulticell = 0; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde
			$pdf->SetWidths2(array(192));
			$pdf->SetAligns2(array("J"));
			$pdf->Row2(array(ajustarCaracteres($autorizacion["observ_auto"])));
			$pdf->Cell(192, 4, ajustarCaracteres($autorizacion["usuario_auto_aux"]), 0, 1, "R");
			$pdf->Ln(4);
			$pdf->SetFont("Arial", "", $fontSize - 1);
			$pdf->Cell(192, 4, ajustarCaracteres($autorizacion["dir_sede_det"] . " - " . $autorizacion["tel_sede_det"]), 0, 1, "C");
			$pdf->SetFont("Arial", "I", $fontSize - 1);
			$pdf->Cell(96, 4, ajustarCaracteres("Fecha y hora de impresión: " . date("Y/m/d H:i:s")), 0, 0, "L");
			$pdf->Cell(96, 4, ajustarCaracteres("Página " . $pdf->PageNo() . "/{nb}"), 0, 1, "R");
			//Se guarda el documento pdf
			$nombreArchivo = "../tmp/orden_medicamentos_sala_" . $_SESSION["idUsuario"] . ".pdf";
			$pdf->Output($nombreArchivo, "F");
		
			?>
				<input type="hidden" name="hdd_ruta_autorizacion_sala_pdf" id="hdd_ruta_autorizacion_sala_pdf" value="<?php echo($nombreArchivo); ?>" />
			<?php	

		}
				
		//PDF para derechos de anestesiologo
		if(!is_null($autorizacion["id_proveedor_anestesiologo"])){
			$proveedor_anestesiologo = $dbOrdenesMedicas->getProveedorProcedimientosById($autorizacion["id_proveedor_anestesiologo"]);
			$fontSize = 7;
			$pdf = new FPDF("P", "mm", array(216, 279));
			$pdf->AliasNbPages();
			$pdf->SetMargins(10, 10, 10);
			$pdf->SetAutoPageBreak(false);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->bordeMulticell = 1; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde
			$pdf->pie_pagina = false;
			$pdf->AddPage();
			$pdf->SetFont("Arial", "", $fontSize);
			if(!is_null($autorizacion["dir_logo_sede_det"]) && !empty($autorizacion["dir_logo_sede_det"])){
			$pdf->Image($autorizacion["dir_logo_sede_det"], 20, 7, 20);
			}
			$pdf->Cell(40, 24, "", 0, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$y_aux = 8;
			$pdf->SetY($y_aux);
			$pdf->SetX(50);
			$pdf->Cell(15, 4, ajustarCaracteres("FECHA:"), 0, 0, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(50, 4, ajustarCaracteres($autorizacion["fechaAutorizacion"]), 0, 0, "L");
			$y_aux += 4;
			$pdf->SetY($y_aux);
			$pdf->SetX(50);
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(35, 4, ajustarCaracteres("FECHA DE VENCIMIENTO:"), 0, 0, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(30, 4, ajustarCaracteres($autorizacion["fechaVencimiento"]), 0, 0, "L");
			$y_aux += 4;
			$pdf->SetY($y_aux);
			$pdf->SetX(50);
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(23, 4, ajustarCaracteres("ORDEN MÉDICA:"), 0, 0, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(53, 4, ajustarCaracteres("" . $autorizacion["id_orden_m"]), 0, 0, "L");
			$pdf->SetY(10);
			$pdf->SetX(115);
			$pdf->SetFont("Arial", "B", ($fontSize + 8));
			$pdf->Cell(90, 10, ajustarCaracteres("AUTORIZACIÓN "), 0, 0, "C");
			$pdf->SetY(20);
			$pdf->SetX(115);
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(90, 4, ajustarCaracteres("*" . $autorizacion["id_auto"] . "*"), 0, 0, "C");
			$pdf->Ln(4);
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(192, 5, ajustarCaracteres("-- PROVEEDOR ANESTESIÓLOGO DIRÍJASE A --"), 0, 1, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(27, 4, ajustarCaracteres("AUTORIZA A:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(165, 4, ajustarCaracteres($proveedor_anestesiologo["nombre_proveedor_procedimiento"]), 1, 1, "L");
			$pdf->SetX($pdf->GetX());
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(27, 4, ajustarCaracteres("IDENTIFICADO CON:"), 1, 0, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(69, 4, ajustarCaracteres($proveedor_anestesiologo["numero_documento"]), 1, 0, "L");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(27, 4, ajustarCaracteres("TELÉFONO:"), 1, 0, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(69, 4, ajustarCaracteres($proveedor_anestesiologo["tel_proveedor_procedimiento"]), 1, 1, "L");
			$pdf->SetX(10);
			$pdf->SetX($pdf->GetX());
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(27, 4, ajustarCaracteres("DIRECCIÓN:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(165, 4, ajustarCaracteres($proveedor_anestesiologo["dir_proveedor_procedimiento"]), 1, 1, "L");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(192, 5, ajustarCaracteres("-- PACIENTE --"), 0, 1, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("DOCUMENTO:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente["numero_documento"]), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("NOMBRES:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente["nombre_1"] . " " . $paciente["nombre_2"]), 1, 1, "C");
			$pdf->SetX($pdf->GetX());
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("TIPO:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente["codigoDocumento"]), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("APELLIDOS:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente["apellido_1"] . " " . $paciente["apellido_2"]), 1, 1, "C");
			$pdf->SetX(10);
			$pdf->SetX($pdf->GetX());
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("SEXO:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(10, 4, ajustarCaracteres($paciente["codigoSexo"]), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("FECH.NAC"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres($paciente["fecha_nacimiento_t"]), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(10, 4, ajustarCaracteres("EDAD:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$edad = explode("/", $paciente["edad"]);
			$edadMagnitud = $edad[1];
			switch ($edadMagnitud) {
				case 1:
					$edadMagnitud = "Años";
					break;
				case 2:
					$edadMagnitud = "Meses";
					break;
				case 3:
					$edadMagnitud = "Días";
					break;
			}
			$pdf->Cell(16, 4, ajustarCaracteres($edad[0] . " " . $edadMagnitud), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("ASEGURADOR:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente["nombre_convenio"]), 1, 1, "C");
			$pdf->Ln(4);
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("CÓDIGO:"), 1, 0, "C");
			$pdf->Cell(132, 4, ajustarCaracteres("PROCEDIMIENTO"), 1, 0, "C");
			$pdf->Cell(20, 4, ajustarCaracteres("LATERALIDAD"), 1, 0, "C");
			$pdf->Cell(10, 4, ajustarCaracteres("OJO"), 1, 0, "C");
			$pdf->Cell(10, 4, ajustarCaracteres("CANT"), 1, 1, "C");

        //Procesa el tipo de producto, Paquete o prodecimiento
			$tipo_proc_aux = $autorizacion["tipo_proc_orden_m_det"];
			$codigo_proc_aux = "";
			$nom_proc_aux = "";
			$proc_paquete_txt_aux = "";
			if ($tipo_proc_aux == 1) {//paquete
				$codigo_proc_aux = $autorizacion["id_paquete_p"];
				$proc_paquete_aux = $dbPaquetesProcedimientos->getProcedimientosByPaquete($codigo_proc_aux);
				$proc_paquete_txt_aux = " (";
				foreach ($proc_paquete_aux as $proc_paquete) {
					$proc_paquete_txt_aux .= "*" . $proc_paquete["cod_procedimiento"] . " - " . $proc_paquete["nombre_procedimiento"] . " ";
				}
				$proc_paquete_txt_aux .= ")";
				$nom_proc_aux = strtoupper($autorizacion["nom_paquete_p"]) . $proc_paquete_txt_aux;
			} else if ($tipo_proc_aux == 2) {//Procedimiento
				$codigo_proc_aux = $autorizacion["cod_procedimiento"];
				$nom_proc_aux = strtoupper($autorizacion["nombre_procedimiento"]);
			}
			$dominancia_ocular = $autorizacion["ojo_auto"];
			$bilateralidad = $autorizacion["bilateralidad_auto"];
			$textoOjo = "";
			switch ($dominancia_ocular) {
				case 1://AO (Ambos ojos)
					$textoOjo = "AO";
					break;
				case 2://OI (Ojo izquierdo)
					$textoOjo = "OI";
					break;
	
				case 3://OD (Ojo derechos)
					$textoOjo = "OD";
					break;
				case 4://No aplica
					$textoOjo = "NA";
					break;
			}
			$textoBilateralidad = "";
			switch ($bilateralidad) {
				case 1://Unilateral
					$textoBilateralidad = "UNILATERAL";
					break;
				case 2://Bilateral
					$textoBilateralidad = "BILATERAL";
					break;
				default:
					$textoBilateralidad = "NO APLICA";
					break;
			}
			$cantProcedimiento = $autorizacion["cant"];
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->SetWidths2(array(20, 132, 20, 10, 10));
			$pdf->SetAligns2(array("C", "L", "C", "C", "C"));
			$pdf->Row2(array(ajustarCaracteres($codigo_proc_aux), ajustarCaracteres($nom_proc_aux), $textoBilateralidad, ajustarCaracteres($textoOjo), ajustarCaracteres($cantProcedimiento))); //
			//Procesa los diagnósticos
			$diagnosticos_txt_aux = "";
			if ($tipo_orden_medica_aux == 2) {//Homologada
				$diagnosticos_txt_aux = $autorizacion["nom_ciex_autorizacion_aux"];
			} else if ($tipo_orden_medica_aux == 1) {//Directa desde UT
				$diagnosticos_aux = $dbHistoriaClinica->getListaCiexAdmision($autorizacion["id_admision_aux"]);
				foreach ($diagnosticos_aux as $diagnostico) {
					$diagnosticos_txt_aux .= $diagnostico["codciex"] . " - " . $diagnostico["nombre"] . "\n";
				}
			}
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->SetWidths2(array(45, 147));
			$pdf->SetAligns2(array("C", "L"));
			$pdf->Row2(array(ajustarCaracteres("SOLICITADO CON DIAGNÓSTICO:"), ajustarCaracteres($diagnosticos_txt_aux)));
			$pdf->Ln(4);
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(192, 4, ajustarCaracteres("OBSERVACIÓNES:"), 0, 1, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->bordeMulticell = 0; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde
			$pdf->SetWidths2(array(192));
			$pdf->SetAligns2(array("J"));
			$pdf->Row2(array(ajustarCaracteres($autorizacion["observ_auto"])));
			$pdf->Cell(192, 4, ajustarCaracteres($autorizacion["usuario_auto_aux"]), 0, 1, "R");
			$pdf->Ln(4);
			$pdf->SetFont("Arial", "", $fontSize - 1);
			$pdf->Cell(192, 4, ajustarCaracteres($autorizacion["dir_sede_det"] . " - " . $autorizacion["tel_sede_det"]), 0, 1, "C");
			$pdf->SetFont("Arial", "I", $fontSize - 1);
			$pdf->Cell(96, 4, ajustarCaracteres("Fecha y hora de impresión: " . date("Y/m/d H:i:s")), 0, 0, "L");
			$pdf->Cell(96, 4, ajustarCaracteres("Página " . $pdf->PageNo() . "/{nb}"), 0, 1, "R");
			//Se guarda el documento pdf
			$nombreArchivo = "../tmp/orden_medicamentos_anestesia_" . $_SESSION["idUsuario"] . ".pdf";
			$pdf->Output($nombreArchivo, "F");
			?>
			<input type="hidden" name="hdd_ruta_autorizacion_anestesia_pdf" id="hdd_ruta_autorizacion_anestesia_pdf" value="<?php echo($nombreArchivo); ?>" />
			<?php
		}
		
		//PDF para derechos de anestesiologo
		if(!is_null($autorizacion["id_proveedor_especialista"])){	
			$proveedor_especialista = $dbOrdenesMedicas->getProveedorProcedimientosById($autorizacion["id_proveedor_especialista"]);
			$fontSize = 7;
			$pdf = new FPDF("P", "mm", array(216, 279));
			$pdf->AliasNbPages();
			//$pdfHTML = new PDF_HTML();
			$pdf->SetMargins(10, 10, 10);
			$pdf->SetAutoPageBreak(false);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->bordeMulticell = 1; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde
			$pdf->pie_pagina = false;
			$pdf->AddPage();
			$pdf->SetFont("Arial", "", $fontSize);
			//Logo
			if(!is_null($autorizacion["dir_logo_sede_det"]) && !empty($autorizacion["dir_logo_sede_det"])){
			$pdf->Image($autorizacion["dir_logo_sede_det"], 20, 7, 20);
			}
			$pdf->Cell(40, 24, "", 0, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$y_aux = 8;
			$pdf->SetY($y_aux);
			$pdf->SetX(50);
			$pdf->Cell(15, 4, ajustarCaracteres("FECHA:"), 0, 0, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(50, 4, ajustarCaracteres($autorizacion["fechaAutorizacion"]), 0, 0, "L");
			$y_aux += 4;
			$pdf->SetY($y_aux);
			$pdf->SetX(50);
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(35, 4, ajustarCaracteres("FECHA DE VENCIMIENTO:"), 0, 0, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(30, 4, ajustarCaracteres($autorizacion["fechaVencimiento"]), 0, 0, "L");
			$y_aux += 4;
			$pdf->SetY($y_aux);
			$pdf->SetX(50);
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(23, 4, ajustarCaracteres("ORDEN MÉDICA:"), 0, 0, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(53, 4, ajustarCaracteres("" . $autorizacion["id_orden_m"]), 0, 0, "L");
			$pdf->SetY(10);
			$pdf->SetX(115);
			$pdf->SetFont("Arial", "B", ($fontSize + 8));
			$pdf->Cell(90, 10, ajustarCaracteres("AUTORIZACIÓN "), 0, 0, "C");
			$pdf->SetY(20);
			$pdf->SetX(115);
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(90, 4, ajustarCaracteres("*" . $autorizacion["id_auto"] . "*"), 0, 0, "C");
			$pdf->Ln(4);
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(192, 5, ajustarCaracteres("-- PROVEEDOR ESPECIALISTA DIRÍJASE A --"), 0, 1, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(27, 4, ajustarCaracteres("AUTORIZA A:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(165, 4, ajustarCaracteres($proveedor_especialista["nombre_proveedor_procedimiento"]), 1, 1, "L");
			$pdf->SetX($pdf->GetX());
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(27, 4, ajustarCaracteres("IDENTIFICADO CON:"), 1, 0, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(69, 4, ajustarCaracteres($proveedor_especialista["numero_documento"]), 1, 0, "L");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(27, 4, ajustarCaracteres("TELÉFONO:"), 1, 0, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(69, 4, ajustarCaracteres($proveedor_especialista["tel_proveedor_procedimiento"]), 1, 1, "L");
			$pdf->SetX(10);
			$pdf->SetX($pdf->GetX());
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(27, 4, ajustarCaracteres("DIRECCIÓN:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(165, 4, ajustarCaracteres($proveedor_especialista["dir_proveedor_procedimiento"]), 1, 1, "L");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(192, 5, ajustarCaracteres("-- PACIENTE --"), 0, 1, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("DOCUMENTO:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente["numero_documento"]), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("NOMBRES:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente["nombre_1"] . " " . $paciente["nombre_2"]), 1, 1, "C");
			$pdf->SetX($pdf->GetX());
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("TIPO:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente["codigoDocumento"]), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("APELLIDOS:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente["apellido_1"] . " " . $paciente["apellido_2"]), 1, 1, "C");
			$pdf->SetX(10);
			$pdf->SetX($pdf->GetX());
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("SEXO:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(10, 4, ajustarCaracteres($paciente["codigoSexo"]), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("FECH.NAC"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres($paciente["fecha_nacimiento_t"]), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(10, 4, ajustarCaracteres("EDAD:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$edad = explode("/", $paciente["edad"]);
			$edadMagnitud = $edad[1];
			switch ($edadMagnitud) {
				case 1:
					$edadMagnitud = "Años";
					break;
				case 2:
					$edadMagnitud = "Meses";
					break;
				case 3:
					$edadMagnitud = "Días";
					break;
			}

			$pdf->Cell(16, 4, ajustarCaracteres($edad[0] . " " . $edadMagnitud), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("ASEGURADOR:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente["nombre_convenio"]), 1, 1, "C");
			$pdf->Ln(4);
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("CÓDIGO:"), 1, 0, "C");
			$pdf->Cell(132, 4, ajustarCaracteres("PROCEDIMIENTO"), 1, 0, "C");
			$pdf->Cell(20, 4, ajustarCaracteres("LATERALIDAD"), 1, 0, "C");
			$pdf->Cell(10, 4, ajustarCaracteres("OJO"), 1, 0, "C");
			$pdf->Cell(10, 4, ajustarCaracteres("CANT"), 1, 1, "C");
			//Procesa el tipo de producto, Paquete o prodecimiento
			$tipo_proc_aux = $autorizacion["tipo_proc_orden_m_det"];
			$codigo_proc_aux = "";
			$nom_proc_aux = "";
			$proc_paquete_txt_aux = "";
			if ($tipo_proc_aux == 1) {//paquete
				$codigo_proc_aux = $autorizacion["id_paquete_p"];
				$proc_paquete_aux = $dbPaquetesProcedimientos->getProcedimientosByPaquete($codigo_proc_aux);
				$proc_paquete_txt_aux = " (";
				foreach ($proc_paquete_aux as $proc_paquete) {
					$proc_paquete_txt_aux .= "*" . $proc_paquete["cod_procedimiento"] . " - " . $proc_paquete["nombre_procedimiento"] . " ";
				}
				$proc_paquete_txt_aux .= ")";
				$nom_proc_aux = strtoupper($autorizacion["nom_paquete_p"]) . $proc_paquete_txt_aux;
			} else if ($tipo_proc_aux == 2) {//Procedimiento
				$codigo_proc_aux = $autorizacion["cod_procedimiento"];
				$nom_proc_aux = strtoupper($autorizacion["nombre_procedimiento"]);
			}

			$dominancia_ocular = $autorizacion["ojo_auto"];
			$bilateralidad = $autorizacion["bilateralidad_auto"];
			$textoOjo = "";
			switch ($dominancia_ocular) {
				case 1://AO (Ambos ojos)
					$textoOjo = "AO";
					break;
				case 2://OI (Ojo izquierdo)
					$textoOjo = "OI";
					break;
				case 3://OD (Ojo derechos)
					$textoOjo = "OD";
					break;
				case 4://No aplica
					$textoOjo = "NA";
					break;
			}

			$textoBilateralidad = "";
			switch ($bilateralidad) {
				case 1://Unilateral
					$textoBilateralidad = "UNILATERAL";
					break;
				case 2://Bilateral
					$textoBilateralidad = "BILATERAL";
					break;
				default:
					$textoBilateralidad = "NO APLICA";
					break;
			}

			$cantProcedimiento = $autorizacion["cant"];

			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->SetWidths2(array(20, 132, 20, 10, 10));
			$pdf->SetAligns2(array("C", "L", "C", "C", "C"));
			$pdf->Row2(array(ajustarCaracteres($codigo_proc_aux), ajustarCaracteres($nom_proc_aux), $textoBilateralidad, ajustarCaracteres($textoOjo), ajustarCaracteres($cantProcedimiento))); //
			//Procesa los diagnósticos
			$diagnosticos_txt_aux = "";
			if ($tipo_orden_medica_aux == 2) {//Homologada
				$diagnosticos_txt_aux = $autorizacion["nom_ciex_autorizacion_aux"];
			} else if ($tipo_orden_medica_aux == 1) {//Directa desde UT
				$diagnosticos_aux = $dbHistoriaClinica->getListaCiexAdmision($autorizacion["id_admision_aux"]);
				foreach ($diagnosticos_aux as $diagnostico) {
					$diagnosticos_txt_aux .= $diagnostico["codciex"] . " - " . $diagnostico["nombre"] . "\n";
				}
			}

			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->SetWidths2(array(45, 147));
			$pdf->SetAligns2(array("C", "L"));
			$pdf->Row2(array(ajustarCaracteres("SOLICITADO CON DIAGNÓSTICO:"), ajustarCaracteres($diagnosticos_txt_aux)));
			$pdf->Ln(4);
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(192, 4, ajustarCaracteres("OBSERVACIÓNES:"), 0, 1, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->bordeMulticell = 0; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde
			$pdf->SetWidths2(array(192));
			$pdf->SetAligns2(array("J"));
			$pdf->Row2(array(ajustarCaracteres($autorizacion["observ_auto"])));
			$pdf->Cell(192, 4, ajustarCaracteres($autorizacion["usuario_auto_aux"]), 0, 1, "R");
			$pdf->Ln(4);
			$pdf->SetFont("Arial", "", $fontSize - 1);
			$pdf->Cell(192, 4, ajustarCaracteres($autorizacion["dir_sede_det"] . " - " . $autorizacion["tel_sede_det"]), 0, 1, "C");
			$pdf->SetFont("Arial", "I", $fontSize - 1);
			$pdf->Cell(96, 4, ajustarCaracteres("Fecha y hora de impresión: " . date("Y/m/d H:i:s")), 0, 0, "L");
			$pdf->Cell(96, 4, ajustarCaracteres("Página " . $pdf->PageNo() . "/{nb}"), 0, 1, "R");

			//Se guarda el documento pdf
			$nombreArchivo = "../tmp/orden_medicamentos_especialista_" . $_SESSION["idUsuario"] . ".pdf";
			$pdf->Output($nombreArchivo, "F");
			?>
			<input type="hidden" name="hdd_ruta_autorizacion_especialista_pdf" id="hdd_ruta_autorizacion_especialista_pdf" value="<?php echo($nombreArchivo); ?>" />
			<?php
		}
		
		//PDF para materiales e insumos
		if(!is_null($autorizacion["id_proveedor_materiales"])){
			$proveedor_materiales = $dbOrdenesMedicas->getProveedorProcedimientosById($autorizacion["id_proveedor_materiales"]);
			$fontSize = 7;
			$pdf = new FPDF("P", "mm", array(216, 279));
			$pdf->AliasNbPages();
			$pdf->SetMargins(10, 10, 10);
			$pdf->SetAutoPageBreak(false);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->bordeMulticell = 1; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde
			$pdf->pie_pagina = false;
			$pdf->AddPage();
			$pdf->SetFont("Arial", "", $fontSize);
			//Logo
			if(!is_null($autorizacion["dir_logo_sede_det"]) && !empty($autorizacion["dir_logo_sede_det"])){
			$pdf->Image($autorizacion["dir_logo_sede_det"], 20, 7, 20);
			}
			
			$pdf->Cell(40, 24, "", 0, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$y_aux = 8;
			$pdf->SetY($y_aux);
			$pdf->SetX(50);
			$pdf->Cell(15, 4, ajustarCaracteres("FECHA:"), 0, 0, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(50, 4, ajustarCaracteres($autorizacion["fechaAutorizacion"]), 0, 0, "L");
			$y_aux += 4;
			$pdf->SetY($y_aux);
			$pdf->SetX(50);
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(35, 4, ajustarCaracteres("FECHA DE VENCIMIENTO:"), 0, 0, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(30, 4, ajustarCaracteres($autorizacion["fechaVencimiento"]), 0, 0, "L");
			$y_aux += 4;
			$pdf->SetY($y_aux);
			$pdf->SetX(50);
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(23, 4, ajustarCaracteres("ORDEN MÉDICA:"), 0, 0, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(53, 4, ajustarCaracteres("" . $autorizacion["id_orden_m"]), 0, 0, "L");
			$pdf->SetY(10);
			$pdf->SetX(115);
			$pdf->SetFont("Arial", "B", ($fontSize + 8));
			$pdf->Cell(90, 10, ajustarCaracteres("AUTORIZACIÓN "), 0, 0, "C");
			$pdf->SetY(20);
			$pdf->SetX(115);
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(90, 4, ajustarCaracteres("*" . $autorizacion["id_auto"] . "*"), 0, 0, "C");
			$pdf->Ln(4);
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(192, 5, ajustarCaracteres("-- PROVEEDOR MATERIALES DIRÍJASE A --"), 0, 1, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(27, 4, ajustarCaracteres("AUTORIZA A:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(165, 4, ajustarCaracteres($proveedor_materiales["nombre_proveedor_procedimiento"]), 1, 1, "L");
			$pdf->SetX($pdf->GetX());
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(27, 4, ajustarCaracteres("IDENTIFICADO CON:"), 1, 0, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(69, 4, ajustarCaracteres($proveedor_materiales["numero_documento"]), 1, 0, "L");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(27, 4, ajustarCaracteres("TELÉFONO:"), 1, 0, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(69, 4, ajustarCaracteres($proveedor_materiales["tel_proveedor_procedimiento"]), 1, 1, "L");
			$pdf->SetX(10);
			$pdf->SetX($pdf->GetX());
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(27, 4, ajustarCaracteres("DIRECCIÓN:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(165, 4, ajustarCaracteres($proveedor_materiales["dir_proveedor_procedimiento"]), 1, 1, "L");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(192, 5, ajustarCaracteres("-- PACIENTE --"), 0, 1, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("DOCUMENTO:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente["numero_documento"]), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("NOMBRES:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente["nombre_1"] . " " . $paciente["nombre_2"]), 1, 1, "C");
			$pdf->SetX($pdf->GetX());
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("TIPO:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente["codigoDocumento"]), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("APELLIDOS:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente["apellido_1"] . " " . $paciente["apellido_2"]), 1, 1, "C");
			$pdf->SetX(10);
			$pdf->SetX($pdf->GetX());
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("SEXO:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(10, 4, ajustarCaracteres($paciente["codigoSexo"]), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("FECH.NAC"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres($paciente["fecha_nacimiento_t"]), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(10, 4, ajustarCaracteres("EDAD:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$edad = explode("/", $paciente["edad"]);
			$edadMagnitud = $edad[1];

			switch ($edadMagnitud) {
				case 1:
					$edadMagnitud = "Años";
					break;
				case 2:
					$edadMagnitud = "Meses";
					break;
				case 3:
					$edadMagnitud = "Días";
					break;
			}

			$pdf->Cell(16, 4, ajustarCaracteres($edad[0] . " " . $edadMagnitud), 1, 0, "C");
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("ASEGURADOR:"), 1, 0, "R");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->Cell(76, 4, ajustarCaracteres($paciente["nombre_convenio"]), 1, 1, "C");
			$pdf->Ln(4);
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(20, 4, ajustarCaracteres("CÓDIGO:"), 1, 0, "C");
			$pdf->Cell(132, 4, ajustarCaracteres("PROCEDIMIENTO"), 1, 0, "C");
			$pdf->Cell(20, 4, ajustarCaracteres("LATERALIDAD"), 1, 0, "C");
			$pdf->Cell(10, 4, ajustarCaracteres("OJO"), 1, 0, "C");
			$pdf->Cell(10, 4, ajustarCaracteres("CANT"), 1, 1, "C");
			//Procesa el tipo de producto, Paquete o prodecimiento
			$tipo_proc_aux = $autorizacion["tipo_proc_orden_m_det"];
			$codigo_proc_aux = "";
			$nom_proc_aux = "";
			$proc_paquete_txt_aux = "";
			if ($tipo_proc_aux == 1) {//paquete
				$codigo_proc_aux = $autorizacion["id_paquete_p"];
				$proc_paquete_aux = $dbPaquetesProcedimientos->getProcedimientosByPaquete($codigo_proc_aux);
				$proc_paquete_txt_aux = " (";
				foreach ($proc_paquete_aux as $proc_paquete) {
					$proc_paquete_txt_aux .= "*" . $proc_paquete["cod_procedimiento"] . " - " . $proc_paquete["nombre_procedimiento"] . " ";
				}
				$proc_paquete_txt_aux .= ")";
				$nom_proc_aux = strtoupper($autorizacion["nom_paquete_p"]) . $proc_paquete_txt_aux;
			} else if ($tipo_proc_aux == 2) {//Procedimiento
				$codigo_proc_aux = $autorizacion["cod_procedimiento"];
				$nom_proc_aux = strtoupper($autorizacion["nombre_procedimiento"]);
			}

			$dominancia_ocular = $autorizacion["ojo_auto"];
			$bilateralidad = $autorizacion["bilateralidad_auto"];
			$textoOjo = "";
			switch ($dominancia_ocular) {
				case 1://AO (Ambos ojos)
					$textoOjo = "AO";
					break;
				case 2://OI (Ojo izquierdo)
					$textoOjo = "OI";
					break;
				case 3://OD (Ojo derechos)
					$textoOjo = "OD";
					break;
				case 4://No aplica
					$textoOjo = "NA";
					break;
			}

			$textoBilateralidad = "";
			switch ($bilateralidad) {
				case 1://Unilateral
					$textoBilateralidad = "UNILATERAL";
					break;
				case 2://Bilateral
					$textoBilateralidad = "BILATERAL";
					break;
				default:
					$textoBilateralidad = "NO APLICA";
					break;
			}

			$cantProcedimiento = $autorizacion["cant"];

			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->SetWidths2(array(20, 132, 20, 10, 10));
			$pdf->SetAligns2(array("C", "L", "C", "C", "C"));
			$pdf->Row2(array(ajustarCaracteres($codigo_proc_aux), ajustarCaracteres($nom_proc_aux), $textoBilateralidad, ajustarCaracteres($textoOjo), ajustarCaracteres($cantProcedimiento))); //
			//Procesa los diagnósticos
			$diagnosticos_txt_aux = "";
			if ($tipo_orden_medica_aux == 2) {//Homologada
				$diagnosticos_txt_aux = $autorizacion["nom_ciex_autorizacion_aux"];
			} else if ($tipo_orden_medica_aux == 1) {//Directa desde UT
				$diagnosticos_aux = $dbHistoriaClinica->getListaCiexAdmision($autorizacion["id_admision_aux"]);
				foreach ($diagnosticos_aux as $diagnostico) {
					$diagnosticos_txt_aux .= $diagnostico["codciex"] . " - " . $diagnostico["nombre"] . "\n";
				}
			}
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->SetWidths2(array(45, 147));
			$pdf->SetAligns2(array("C", "L"));
			$pdf->Row2(array(ajustarCaracteres("SOLICITADO CON DIAGNÓSTICO:"), ajustarCaracteres($diagnosticos_txt_aux)));
			$pdf->Ln(4);
			$pdf->SetFont("Arial", "B", $fontSize);
			$pdf->Cell(192, 4, ajustarCaracteres("OBSERVACIÓNES:"), 0, 1, "L");
			$pdf->SetFont("Arial", "", $fontSize);
			$pdf->bordeMulticell = 0; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde
			$pdf->SetWidths2(array(192));
			$pdf->SetAligns2(array("J"));
			$pdf->Row2(array(ajustarCaracteres($autorizacion["observ_auto"])));
			$pdf->Cell(192, 4, ajustarCaracteres($autorizacion["usuario_auto_aux"]), 0, 1, "R");
			$pdf->Ln(4);
			$pdf->SetFont("Arial", "", $fontSize - 1);
			$pdf->Cell(192, 4, ajustarCaracteres($autorizacion["dir_sede_det"] . " - " . $autorizacion["tel_sede_det"]), 0, 1, "C");
			$pdf->SetFont("Arial", "I", $fontSize - 1);
			$pdf->Cell(96, 4, ajustarCaracteres("Fecha y hora de impresión: " . date("Y/m/d H:i:s")), 0, 0, "L");
			$pdf->Cell(96, 4, ajustarCaracteres("Página " . $pdf->PageNo() . "/{nb}"), 0, 1, "R");

			//Se guarda el documento pdf
			$nombreArchivo = "../tmp/orden_medicamentos_materiales_" . $_SESSION["idUsuario"] . ".pdf";
			$pdf->Output($nombreArchivo, "F");
			?>
			<input type="hidden" name="hdd_ruta_autorizacion_materiales_pdf" id="hdd_ruta_autorizacion_materiales_pdf" value="<?php echo($nombreArchivo); ?>" />
			<?php
		}
		
        break;
    case "9": //Formulario para agregar un elemento a la autorización
        ?>
        <div class="encabezado">
            <h3>Procedimientos adicionales</h3>
        </div>
        <br>
        <div>           
            <table style="width:90%; margin:0 auto;">
                <tr>
                    <td align="right" style="width:20%;">Procedimiento/paquete:</td>
                    <td align="left" style="width:15%;">
                        <input type="hidden" id="hddTipoProductos" value="" />
                        <input type="hidden" id="hddCodProcModal" value="" />
                        <input type="text" id="codProcModal" value="" class="no-margin" onchange="buscar_procedimiento_codigo(this.value);" />
                    </td>
                    <td align="left" style="width:60%;">
                <spam id="nomProcModal"></spam>
                </td>
                <td align="left" style="width:5%;">
                    <div class="d_buscar" onclick="btnVentanaBuscarProcedimiento();"></div>
                </td>
                </tr>
                <tr>
                    <td align="right">Diagn&oacute;stico asociado:</td>
                    <td align="left">
                        <input type="hidden" id="hddCodCiexModal" value="" />
                        <input type="text" id="codCiexModal" value="" class="no-margin" onchange="buscar_diagnostico_codigo(this.value);" />
                    </td>
                    <td align="left">
                        <span id="nomCiexModal"></span>
                    </td>
                    <td align="left">
                        <div class="d_buscar" onclick="btnVentanaBuscarDiagnostico();"></div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%;text-align: right;">Ojo:</td>
                    <td style="text-align: left;color: #399000;font-weight: bold;" colspan="2">                                       
                        <?php
                        $array_statusAseguradora = array();
                        $array_statusAseguradora[0][0] = "3";
                        $array_statusAseguradora[0][1] = "OD";
                        $array_statusAseguradora[1][0] = "2";
                        $array_statusAseguradora[1][1] = "OI";
                        $array_statusAseguradora[2][0] = "1";
                        $array_statusAseguradora[2][1] = "AO";
                        $array_statusAseguradora[3][0] = "4";
                        $array_statusAseguradora[3][1] = "No aplica";

                        $combo->get("cmb_ojo", NULL, $array_statusAseguradora, "-- Seleccione --", "", "", "", "", "no-margin");
                        ?>
                    </td>                   
                </tr>
                 <tr>
                  <td align="center" colspan="4">
                  	<table  style="width:75%; margin:auto; margin-top:10px;">
                     <tr>
                          <td><input onchange="mostrarProveedorModal(1);" type="checkbox" name="checkAutoDerechosSala" id="checkAutoDerechosSala" >&nbsp;Derechos de sala
                          </td>  
                          <td><input onchange="mostrarProveedorModal(2);" type="checkbox" name="checkAutoAnestesiologo" id="checkAutoAnestesiologo" >&nbsp;Anestesi&oacute;logo
                          </td>  
                          <td><input onchange="mostrarProveedorModal(3);" type="checkbox" name="checkAutoEspecialista" id="checkAutoEspecialista" >&nbsp;Especialista
                          </td> 
                          <td><input onchange="mostrarProveedorModal(4);" type="checkbox" name="checkAutoMateriales" id="checkAutoMateriales" >&nbsp;Materiales e insumos
                          </td>  
                      </tr>
                    </table>
                  </td>                           
          	    </tr>
			
				   <?php $display = "none"; ?>
                    <td align="center" colspan="4">
					<br><b>Proveedores</b>
                        <table style="width:75%; margin:auto;">
                            <tr id="tr_codProvModal_sala"  style="display:<?= $display?>">
                                <td align="right" style="width:100px;"><b>Sala:</b></td>
                                <td align="left" style="width:600px;">
                                    <input type="hidden" id="codProvModalSala" value="" />
                                    <span id="nomProvModalSala"></span>
                                </td>
                                <td align="center" style="width:5%;">
                                    <div class="d_buscar" onclick="btnVentanaBuscarProveedor(1);"></div>
                                </td>
                            </tr>
							<tr id="tr_codProvModal_anestesia" style="display:<?= $display?>">
                                <td align="right" style="width:100px;"><b>Anestesia:</b></td>
                                <td align="left" style="width:600px;">
                                    <input type="hidden" id="codProvModalAnestesia" value="" />
                                    <span id="nomProvModalAnestesia"></span>
                                </td>
                                <td align="center" style="width:5%;">
                                    <div class="d_buscar" onclick="btnVentanaBuscarProveedor(2);"></div>
                                </td>
                            </tr>
							<tr id="tr_codProvModal_especialista" style="display:<?= $display?>">
                                <td align="right" style="width:100px;"><b>Especialista:</b></td>
                                <td align="left" style="width:600px;">
                                    <input type="hidden" id="codProvModalEspecialista" value="" />
                                    <span id="nomProvModalEspecialista"></span>
                                </td>
                                <td align="center" style="width:5%;">
                                    <div class="d_buscar" onclick="btnVentanaBuscarProveedor(3);"></div>
                                </td>
                            </tr>
							<tr id="tr_codProvModal_materiales"  style="display:<?= $display?>">
                                <td align="right" style="width:100px;"><b>Materiales:</b></td>
                                <td align="left" style="width:600px;">
                                    <input type="hidden" id="codProvModalMateriales" value="" />
                                    <span id="nomProvModalMateriales"></span>
                                </td>
                                <td align="center" style="width:5%;">
                                    <div class="d_buscar" onclick="btnVentanaBuscarProveedor(4);"></div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="5">
                        <h5 style="color: #DD5043;">La fecha de vencimiento será asignada automáticamente con un plazo de 3 meses a partir de la fecha actual</h5>
                        <br>
                        <input type="button" id="btn_consultar" name="btn_consultar" value="Agregar" class="btnPrincipal no-margin" onclick="autorizar_procedimiento_adicional();" />
                        <div id="d_adicionales_aut" style="display:none;"></div>
                    </td>
                </tr>
            </table>
            <br>
        </div>
        <?php
        break;

    case "10": //Formulario de búsqueda de procedimientos
        ?>
        <input type="hidden" id="hdd_rastro" name="hdd_rastro" value="<?= $rastro ?>" />
        <br />
        <table style="width: 100%;">
            <tbody>                
                <tr>
                    <td>
                        <input type="text" id="txtParametro" name="txtParametro" placeholder="Codigo o nombre del procedimiento">
                    </td>
                    <td style="width: 10%;">
                        <input type="submit" onclick="buscarProcedimientos()" value="Buscar" class="btnPrincipal peq">
                    </td>
                </tr>
            </tbody>
        </table>
        <div id="resultadoTbl"></div>
        <?php
        break;

    case "11": //Resultado buscar procedimientos o paquetes
        $parametro = $utilidades->str_decode($_POST["parametro"]);

        $resultados_p = $dbMaestroProcedimientos->getProcedimientos($parametro, 1);
        $resultados_q = $dbPaquetesProcedimientos->buscarPaquetesActivos($parametro);

        $totalResultados = count($resultados_p) + count($resultados_q);
        if ($totalResultados >= 1) {
            ?>
            <table class="paginated modal_table" id="tblProductos">
                <thead>
                    <tr>                   
                        <th style="width:20%;">C&oacute;digo</th>
                        <th style="width:80%;">Nombre</th>
                    </tr>
                </thead>
                <?php
                foreach ($resultados_p as $proced_aux) {
                    $codigo = $proced_aux["cod_procedimiento"];
                    $nombre = $proced_aux["nombre_procedimiento"];
                    ?>
                    <tr onclick="agregarProcedimientoAutorizaciones('<?= $codigo ?>', '<?= $nombre ?>', 2);">
                        <td align="center"><?= $codigo ?></td>
                        <td align="left"><?= $nombre ?></td>
                    </tr>
                    <?php
                }

                foreach ($resultados_q as $paquete_aux) {
                    $codigo = $paquete_aux["id_paquete_p"];
                    $nombre = $paquete_aux["nom_paquete_p"];
                    ?>
                    <tr onclick="agregarProcedimientoAutorizaciones('<?= $codigo ?>', '<?= $nombre ?>', 1);">
                        <td align="center"><?= $codigo ?></td>
                        <td align="left"><?= $nombre ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <script id="ajax">
                //<![CDATA[ 
                $(function () {
                    $("#tblProductos", "table").each(function (i) {
                        $(this).text(i + 1);
                    });

                    $("table.paginated").each(function () {
                        var currentPage = 0;
                        var numPerPage = 7;
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
        } else {
            ?>
            <p>No hay resultados para la b&uacute;squeda</p>
            <?php
        }
        break;

    case "12": //Formulario de búsqueda de diagnósticos
        ?>
        <input type="hidden" id="hdd_rastro" name="hdd_rastro" value="<?= $rastro ?>" />
        <br>
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td>
                        <input type="text" id="txtParametro" name="txtParametro" placeholder="C&oacute;digo CIE-X o nombre del diagn&oacute;stico">
                    </td>
                    <td style="width: 10%;">
                        <input type="submit" onclick="buscarDiagnostico()" value="Buscar" class="btnPrincipal peq">
                    </td>
                </tr>
            </tbody>
        </table>
        <div id="resultadoTblDiagnostico"></div>
        <?php
        break;

    case "13": //Resultados de la búsqueda de diagnósticos
        @$parametro = $utilidades->str_decode($_POST["parametro"]);

        $resultados = $dbDiagnosticos->getBuscarDiagnosticos($parametro);

        $totalResultados = count($resultados);
        if ($totalResultados >= 1) {
            ?>
            <table class="paginated modal_table" id="tblProductos">
                <thead>
                    <tr>                   
                        <th style="width:20%;">C&oacute;digo</th>
                        <th style="width:80%;">Nombre</th>
                    </tr>
                </thead>
                <?php
                foreach ($resultados as $resultado) {
                    $nombre = $resultado["nombre"];
                    $codigo = $resultado["codciex"];
                    ?>
                    <tr onclick="agregarDiagnosticoAutorizaciones('<?= $codigo ?>', '<?= $nombre ?>');">
                        <td align="center"><?= $codigo ?></td>
                        <td align="left"><?= $nombre ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <script id="ajax">
                //<![CDATA[ 
                $(function () {
                    $("#tblProductos", "table").each(function (i) {
                        $(this).text(i + 1);
                    });

                    $("table.paginated").each(function () {
                        var currentPage = 0;
                        var numPerPage = 10;
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
        } else {
            ?>
            <p>No hay resultados para la b&uacute;squeda</p>
            <?php
        }
        break;

    case "14": //Formulario de búsqueda de proveedores
	 	 $case = $utilidades->str_decode($_POST["case"]);
	   
        ?>
        <input type="hidden" id="hdd_rastro" name="hdd_rastro" value="<?= $rastro ?>" />
        <br>
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td>
                        <input type="text" id="txtParametro" name="txtParametro" placeholder="Documento o nombre del proveedor">
                    </td>
                    <td style="width: 10%;">
                        <input type="submit" onclick="buscarProveedor(<?=$case?>)" value="Buscar" class="btnPrincipal peq">
                    </td>
                </tr>
            </tbody>
        </table>
        <div id="resultadoTblProveedor"></div>
        <?php
        break;

    case "15"://Autorizacion con base en orde medica adicional
        $tipoAuto = $utilidades->str_decode($_POST["tipoAuto"]);
        $idOrdenMedica = $utilidades->str_decode($_POST["idOrdenMedica"]);
        $observacion = $utilidades->str_decode($_POST["observacion"]);
        $idProveedorSala = $utilidades->str_decode($_POST["idProveedorSala"]);
		$idProveedorAnestesiologo = $utilidades->str_decode($_POST["idProveedorAnestesiologo"]);
		$idProveedorEspecialista = $utilidades->str_decode($_POST["idProveedorEspecialista"]);
		
        $fechaVenc = $utilidades->str_decode($_POST["fechaVenc"]);

        $procedimiento = $utilidades->str_decode($_POST["procedimiento"]);
        $ciex = $utilidades->str_decode($_POST["ciex"]);
        $ojo = $utilidades->str_decode($_POST["ojo"]);
        $tipoProducto = $utilidades->str_decode($_POST["tipoProducto"]);

        $codigo_paquete = "";
        if ($tipoProducto == 1) {//Si es un paquete
            $codigo_paquete = $procedimiento;
            $procedimiento = "";
        }

        $rta = $dbAutorizaciones->autorizarProcedimiento($tipoAuto, $idOrdenMedica, "", $ciex, $idProveedorSala, $idProveedorAnestesiologo, $idProveedorEspecialista, $observacion, $fechaVenc, $usuarioCrea, $tipoProducto, $procedimiento, $codigo_paquete, $lugar, $ojo);
        ?>
        <input type="hidden" id="hddResultadoAutorizar" name="hddResultadoAutorizar" value="<?= $rta ?>" />
        <?php
        break;

    case "16":
        $idPaciente = $utilidades->str_decode($_POST["idPaciente"]);
        $nombre = $utilidades->str_decode($_POST["nombre"]);
        $documento = $utilidades->str_decode($_POST["documento"]);
        $telefonos = $utilidades->str_decode($_POST["telefonos"]);
        $fechaNacimiento = $utilidades->str_decode($_POST["fechaNacimiento"]);
        $edad = $utilidades->str_decode($_POST["edad"]);
        $nombre_convenio = $utilidades->str_decode($_POST["nombre_convenio"]);
        $nombre_plan = $utilidades->str_decode($_POST["nombre_plan"]);
        $estadoConvenio = $utilidades->str_decode($_POST["estadoConvenio"]);
        $tipoAccion = $utilidades->str_decode($_POST["tipoAccion"]);
        $tipo_documento = $utilidades->str_decode($_POST["tipoDocumento"]);

        ver_formulaciones($idPaciente, $nombre, $documento, $tipo_documento, $telefonos, $fechaNacimiento, $edad, $nombre_convenio, $nombre_plan, $estadoConvenio, $tipoAccion);
        break;

    case "17"://Cancelar autorización
        $idAutorizacion = $utilidades->str_decode($_POST["idAutorizacion"]);
        $observacion = $utilidades->str_decode($_POST["observacion"]);
        $usuario = $_SESSION["idUsuario"];
        $lugar = $_SESSION["idLugarUsuario"];

        $rta = $dbAutorizaciones->desautorizarProcedimiento($idAutorizacion, $observacion, $usuario, $lugar);
        echo $rta;
        ?>
        <input type="hidden" id="hddResultadoDesautorizar" name="hddResultadoDesautorizar" value="<?= $rta ?>" />
        <?php
        break;

    case "18": //Registrar pago de la autorización
        $id_usuario = $_SESSION["idUsuario"];
        @$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
        @$id_profesional = $utilidades->str_decode($_POST["id_profesional"]);
        @$cant_autorizaciones = intval($_POST["cant_autorizaciones"], 10);
		@$id_paquete = $utilidades->str_decode($_POST["id_paquete"]);
		
		//echo($id_paquete);
		
        $cont_det = 1;
        $mapa_autorizaciones = array();
        $mapa_productos = array();
        $mapa_procedimientos = array();
        $resultado = 0;
		
		for ($i = 0; $i < $cant_autorizaciones; $i++) {
            @$id_convenio_aux = $utilidades->str_decode($_POST["id_convenio_autorizacion_" . $i]);
            @$id_plan_aux = $utilidades->str_decode($_POST["id_plan_autorizacion_" . $i]);
            @$id_auto = $utilidades->str_decode($_POST["id_autorizacion_" . $i]);

            if (isset($mapa_autorizaciones[$id_plan_aux])) {
                $arr_aut_aux = $mapa_autorizaciones[$id_plan_aux];
            } else {
                $arr_aut_aux = array();
            }
            array_push($arr_aut_aux, $id_auto);
            $mapa_autorizaciones[$id_plan_aux] = $arr_aut_aux;

            if (isset($mapa_productos[$id_plan_aux])) {
                $arr_aux = $mapa_productos[$id_plan_aux];
            } else {
                $arr_aux = array();
            }
			//Se busca si el paquete trae insumos adicionales
			
			$id_paquete_aux = $dbPaquetesProcedimientos->getPaqueteById($id_paquete);
			$valor_insumos_ad = $id_paquete_aux["valor_insumos_adicionales"];
			
			
            //Se busca el detalle de la autorización
            $lista_autorizaciones_det = $dbAutorizaciones->getListaAutorizacionesDet($id_auto);
			
            $arr_procedimientos_aux = array();
            foreach ($lista_autorizaciones_det as $aut_det_aux) {
				 
                $tipo_producto_aux = $aut_det_aux["tipo_producto"];
                $tipo_bilateral_aux = $tipo_producto_aux == "P" ? $aut_det_aux["bilateralidad_auto"] : "0";
                $cod_servicio_aux = "";
                switch ($tipo_producto_aux) {
                    case "P":
                        $cod_servicio_aux = $aut_det_aux["cod_procedimiento"];
                        break;
                    case "I":
                        $cod_servicio_aux = $aut_det_aux["cod_insumo"];
                        break;
                }

                //Se busca el precio configurado para el servicio
                $precio_obj = $dbListasPrecios->getPrecioFecha($id_plan_aux, $cod_servicio_aux, $tipo_producto_aux, $tipo_bilateral_aux);
			
                if (!isset($precio_obj["id_precio"]) && $tipo_bilateral_aux != "0") {
                    $precio_obj = $dbListasPrecios->getPrecioFecha($id_plan_aux, $cod_servicio_aux, $tipo_producto_aux, "0");
                    if (isset($precio_obj["id_precio"])) {
                        $tipo_bilateral_aux = "0";
                    }
                }

                if (isset($precio_obj["id_precio"])) {
                    $valor_aux = $precio_obj["valor"];
                    $valor_cuota_aux = $precio_obj["valor_cuota"];
                } else {
                    $valor_aux = "0";
                    $valor_cuota_aux = "0";
                }	
				
                //Se verifica si para el plan ya se tiene el mismo producto para alterar únicamente la cantidad
                $ind_hallado_busq_aux = false;
				
                foreach ($arr_aux as $indice_busq_aux => $prod_busq_aux) {
                    if ($prod_busq_aux["tipo_precio"] == $tipo_producto_aux && $prod_busq_aux["cod_servicio"] == $cod_servicio_aux && $prod_busq_aux["tipo_bilateral"] == $tipo_bilateral_aux) {
                        $prod_busq_aux["cantidad"] += $aut_det_aux["cant"];
                        $arr_aux[$indice_busq_aux] = $prod_busq_aux;
                        $ind_hallado_busq_aux = true;
                        break;
                    }
                }

                if (!$ind_hallado_busq_aux) {
                    $arr_aut_aux = array();
                    $arr_aut_aux["orden"] = $cont_det;
                    $arr_aut_aux["id_convenio"] = $id_convenio_aux;
                    $arr_aut_aux["id_plan"] = $id_plan_aux;
                    $arr_aut_aux["id_detalle_precio"] = $cont_det;
                    $arr_aut_aux["tipo_precio"] = $tipo_producto_aux;
                    $arr_aut_aux["cod_servicio"] = $cod_servicio_aux;
                    $arr_aut_aux["cod_producto"] = $cod_servicio_aux;
                    $arr_aut_aux["tipo_bilateral"] = $tipo_bilateral_aux;
                    $arr_aut_aux["id_lugar"] = $aut_det_aux["id_lugar_orden_m"];
                    $arr_aut_aux["num_autorizacion"] = "";
                    $arr_aut_aux["cantidad"] = $aut_det_aux["cant"];
                    $arr_aut_aux["valor"] = $valor_aux;
                    $arr_aut_aux["valor_cuota"] = $valor_cuota_aux;
					//$arr_aut_aux["valor_insumos_adicionales"] = $valor_insumos_ad; 
                    array_push($arr_aux, $arr_aut_aux);
					

                    if ($tipo_producto_aux == "P") {
                        $arr_proc_aux = array();
                        $arr_proc_aux["orden"] = $cont_det;
                        $arr_proc_aux["cod_procedimiento"] = $cod_servicio_aux;
                        $arr_proc_aux["tipo_bilateral"] = $tipo_bilateral_aux;
						$arr_proc_aux["valor_insumos_adicionales"] = $valor_insumos_ad;
                        array_push($arr_procedimientos_aux, $arr_proc_aux);
                    }

                    $cont_det++;
                }
            }

            $mapa_productos[$id_plan_aux] = $arr_aux;
            if (count($arr_procedimientos_aux) > 0) {
                $mapa_procedimientos[$id_plan_aux] = $arr_procedimientos_aux;
            }
        }

        //Se hace un pago para cada plan
	
        foreach ($mapa_productos as $id_plan_aux => $arr_aut_aux) {
			
            //Se verifica si se deben liquidar las cirugías, los copagos y las cuotas moderadoras
            $plan_obj = $dbPlanes->getPlan($id_plan_aux);
			if ($plan_obj["ind_calc_cc"] == "1") {
                //Cirugías
                $mapa_precios_cx = $liquidadorPrecios->liquidar_cirugias($id_plan_aux, $mapa_procedimientos[$id_plan_aux]);
				
				//var_dump($mapa_precios_cx);
                for ($ii = 0; $ii < count($arr_aut_aux); $ii++) {
                    $precio_aux = $arr_aut_aux[$ii];
                    if (isset($mapa_precios_cx[$precio_aux["orden"]])) {
                        $precio_aux["valor"] = $mapa_precios_cx[$precio_aux["orden"]]["valor_total"];
                        $arr_aut_aux[$ii] = $precio_aux;
					}
				}
                //Copago y cuota moderadora
                if (isset($mapa_procedimientos[$id_plan_aux])) {
                    $mapa_precios_cop_cm = $liquidadorPrecios->liquidar_cop_cm($id_paciente, $arr_aut_aux);
					
					//var_dump($mapa_precios_cop_cm );
                    for ($ii = 0; $ii < count($arr_aut_aux); $ii++) {
                        $precio_aux = $arr_aut_aux[$ii];
                        if (isset($mapa_precios_cop_cm[$precio_aux["orden"]])) {
                            $precio_aux["valor_cuota"] = $mapa_precios_cop_cm[$precio_aux["orden"]]["valor_cuota"];
                            $arr_aut_aux[$ii] = $precio_aux;
                        }
                    }
                }
            }
			
           // var_dump($mapa_productos);
			//Se crea el pago pendiente
            $resultado = $dbPagos->registrarPagos("", "", $id_paciente, $id_usuario, "", "", "", "", "", "", "", "", "", $id_plan_aux, $arr_aut_aux[0]["id_convenio"], "", "", "Pago generado desde autorizaciones", 1, $arr_aut_aux, array(), $arr_aut_aux[0]["id_lugar"], $id_profesional, "", "", $mapa_autorizaciones[$id_plan_aux]);
			//var_dump($mapa_autorizaciones[$id_plan_aux]);
            if ($resultado <= 0) {
                break;
            }
			//var_dump($resultado);
		}
        ?>
        <input type="hidden" id="hdd_resultado_pago_aut" name="hdd_resultado_pago_aut" value="<?= $resultado ?>" />
        <?php

        break;
    case "19": //Carga el cmb de planes para cada movimiento
        @$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
        @$contador = $utilidades->str_decode($_POST["contador"]);

        echo $combo->getComboDb("cmb_plan_" . $contador, "", $dbPlanes->getListaPlanesActivos($id_convenio), "id_plan, nombre_plan", "-- Seleccione --", "", "", "", "");
        break;

    case "20": //Resultados de la búsqueda de proveedores
        @$parametro = $utilidades->str_decode($_POST["parametro"]);
		@$case = $utilidades->str_decode($_POST["case"]);
		
        $lista_proveedores = $dbOrdenesMedicas->getListaProveedoresParametro($parametro, 1);

        $totalResultados = count($lista_proveedores);
        if ($totalResultados >= 1) {
            ?>
            <table class="paginated modal_table" id="tblProductos">
                <thead>
                    <tr>                   
                        <th style="width:20%;">Documento</th>
                        <th style="width:80%;">Nombre</th>
                    </tr>
                </thead>
                <?php
                foreach ($lista_proveedores as $proveedor_aux) {
                    $id_proveedor = $proveedor_aux["id_proveedor_procedimiento"];
                    $numero_documento = $proveedor_aux["numero_documento"];
                    $nombre_proveedor = $proveedor_aux["nombre_proveedor_procedimiento"];
                    ?>
                    <tr onclick="agregarProveedorAutorizaciones(<?= $id_proveedor ?>, '<?= $numero_documento ?>', '<?= $nombre_proveedor ?>', <?= $case?>);">
                        <td align="center"><?= $numero_documento ?></td>
                        <td align="left"><?= $nombre_proveedor ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <script id="ajax">
                //<![CDATA[ 
                $(function () {
                    $("#tblProductos", "table").each(function (i) {
                        $(this).text(i + 1);
                    });

                    $("table.paginated").each(function () {
                        var currentPage = 0;
                        var numPerPage = 10;
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
        } else {
            ?>
            <p>No hay resultados para la b&uacute;squeda</p>
            <?php
        }
        break;

    case "21": //Búsqueda de procedimientos o paquetes por código
        @$cod_servicio = $utilidades->str_decode($_POST["cod_servicio"]);

        //Se busca el procedimiento por código
        $procedimiento_obj = $dbMaestroProcedimientos->getProcedimiento($cod_servicio, 1);

        $tipo_hallado = 0;
        $nombre_servicio = "Producto no hallado";
        if (isset($procedimiento_obj["cod_procedimiento"])) {
            $tipo_hallado = 2;
            $nombre_servicio = $procedimiento_obj["nombre_procedimiento"];
        } else {
            $paquete_obj = $dbPaquetesProcedimientos->getPaqueteById($cod_servicio);
            if (isset($paquete_obj["id_paquete_p"])) {
                $tipo_hallado = 1;
                $nombre_servicio = $paquete_obj["nom_paquete_p"];
            }
        }
        ?>
        <input type="hidden" id="hdd_tipo_servicio_b_aut" name="hdd_tipo_servicio_b_aut" value="<?= $tipo_hallado ?>" />
        <input type="hidden" id="hdd_nombre_servicio_b_aut" name="hdd_nombre_servicio_b_aut" value="<?= $nombre_servicio ?>" />
        <?php
        break;

    case "22": //Búsqueda de diagnósticos por código
        @$cod_ciex = $utilidades->str_decode($_POST["cod_ciex"]);

        //Se busca el procedimiento por código
        $diagnostico_obj = $dbDiagnosticos->getDiagnosticoCiex($cod_ciex);

        $ind_hallado = 0;
        $nombre_ciex = "Producto no hallado";
        if (isset($diagnostico_obj["codciex"])) {
            $ind_hallado = 1;
            $nombre_ciex = $diagnostico_obj["nombre"];
        } else {
            $ind_hallado = 0;
            $nombre_ciex = "Diagn&oacute;stico no hallado";
        }
        ?>
        <input type="hidden" id="hdd_hallado_ciex_b_aut" name="hdd_hallado_ciex_b_aut" value="<?= $ind_hallado ?>" />
        <input type="hidden" id="hdd_nombre_ciex_b_aut" name="hdd_nombre_ciex_b_aut" value="<?= $nombre_ciex ?>" />
        <?php
        break;
		

}
