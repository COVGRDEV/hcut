<?php
session_start();

header('Content-Type: text/xml; charset=UTF-8');

require_once("../db/DbUsuarios.php");
require_once("../db/DbListas.php");
require_once("../db/DbDespacho.php");
require_once("../db/DbMenus.php");
require_once("../db/DbHistoriaClinica.php");
require_once("../db/DbVariables.php");
require_once("../db/DbPacientes.php");
require_once("../db/DbFormulasMedicamentos.php");
require_once("../db/DbListas.php");
require_once("../db/DbComunicacionSiesa.php");

require_once("../funciones/Class_Combo_Box.php");
require_once("../principal/ContenidoHtml.php");
require_once("../funciones/FuncionesPersona.php");
require_once("../funciones/Utilidades.php");
require_once("../funciones/Class_Ordenes_Remisiones.php");
require_once("../funciones/Class_Conector_Siesa.php");
require_once("../funciones/Class_Terceros_Siesa.php");


$usuarios = new DbUsuarios();
$listas = new DbListas();
$despacho = new DbDespacho();
$menus = new DbMenus();
$variables = new Dbvariables();
$contenido = new ContenidoHtml();
$contenido->validar_seguridad(1);
$funciones_persona = new FuncionesPersona();
$utilidades = new Utilidades();
$pacientes = new DbPacientes();
$combo = new Combo_Box();
$opcion = $_POST["opcion"];
$dbHistoria = new DbHistoriaClinica();
$dbFormulasMedicamentos = new DbFormulasMedicamentos();
$dbListas = new DbListas();
$dbComunicacionSiesa = new DbComunicacionSiesa();

$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);
$id_usuario = $_SESSION["idUsuario"];

function ver_formulaciones($id_paciente, $nombre_persona, $documento_persona, $tipo_documento, $telefonos, $fecha_nacimiento, $edad_paciente, $nombre_convenio, $nombre_plan, $estado_convenio, $tipoAccion) {
    $dbHistoria = new DbHistoriaClinica();
    $class_ordenes_remisiones = new Class_Ordenes_Remisiones();

    //Edad
	$arr_aux = explode("/", $edad_paciente);
    $edad = $arr_aux[0];
    $unidad_edad = "";
    switch ($arr_aux[1]) {
        case 1://Años
            $unidad_edad = "Años";
            break;
        case 2://Meses
            $unidad_edad = "Meses";
            break;
        case 3://Días
            $unidad_edad = "Días";
            break;
    }
    ?>
    <fieldset style="width: 65%; margin: auto;">
        <legend>Datos actuales del paciente:</legend>
        <table style="width: 500px; margin: auto;">
            <tr>
                <td align="right" style="width:40%">Tipo de documento:</td>
                <td align="left" style="width:60%"><b><?php echo($tipo_documento); ?></b></td>
            </tr>
            <tr>
                <td align="right">N&uacute;mero de identificaci&oacute;n:</td>
                <td align="left">
                    <b><?php echo($documento_persona); ?></b>
                    <input type="hidden" name="hdd_idPaciente" id="hdd_idPaciente" value="<?= $id_paciente ?>" />
                </td>
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
        $dbFormulasMedicamentos = new DbFormulasMedicamentos();
        $tabla_formulaciones = $dbFormulasMedicamentos->formulacionesPacienteDespacho($id_paciente);
        ?>
        <div id="divFormulas">
            <div style="width: 90%;margin: auto;text-align: left;">
                <p>Seleccione las siguientes formulaciones para ver los detalles:</p>
            </div>

            <table id="tbl_formulas" class="paginated modal_table" style="width: 90%; margin: auto;" >
                <thead>
                    <tr>
                        <th class="th_reducido" align="center" style="width:15%;">C&oacute;digo</th>
                        <th class="th_reducido" align="center" style="">Lugar</th>
                        <th class="th_reducido" align="center" style="width:15%;">Fecha</th>
                        <th class="th_reducido" align="center" style="">Tipo</th>
                        <th class="th_reducido" align="center" style="">Con/Plan</th>                         
                        <th class="th_reducido" align="center" style="">Por entregar</th>
                        <th class="th_reducido" align="center" style="">Pendiente</th>
                        <th class="th_reducido" align="center" style="">Estado</th>
                    </tr>
                </thead>
                <?php
				
                foreach ($tabla_formulaciones as $fila_formulacion) {
                    $id_formulacion = $fila_formulacion['id_formula_medicamento'];
                    $fecha_formulacion = $fila_formulacion['fechaFormulacion'];
                    $tipoCitaFormulacion = $fila_formulacion['tipoCitaFormulacion'];
                    $lugar = $fila_formulacion['lugar_aux'];
                    $nombreConvenio = $fila_formulacion['nombreConvenioAux'];
                    $nombrePlan = $fila_formulacion['nombrePlanAux'];
                    $cantidadOrden = $fila_formulacion['cantidad_orden'];
                    $cantidadPendiente = $fila_formulacion['cantidad_pendiente'];
                    $estadoFormula = "";
                    $colorFilaEstadoFormula = "";
                    $colorFilaTdEstadoFormula = "";

                    switch ($fila_formulacion['estado_formula_medicamento']) {
                        case 1://Nueva
                            $estadoFormula = "Nueva";

                            break;
                        case 2://Con pendientes
                            $estadoFormula = "Con pendientes";
                            $colorFilaEstadoFormula = "background:#D8A31E;";
                            break;
                        case 3://Cerrada
                            $estadoFormula = "Cerrada";
                            $colorFilaEstadoFormula = "background:#CEFDAF;";
                            break;
                        case 4://Anulada
                            $estadoFormula = "Anulada";
                            $colorFilaEstadoFormula = "background:#DD5043;";
                            $colorFilaTdEstadoFormula = "color:#FFF;";
                            break;
                    }
                    ?>
                    <tr onclick="seleccionar_formulacion(<?php echo ($id_formulacion); ?>);" style="<?= $colorFilaEstadoFormula ?>">
                        <td class="td_reducido" align="center" style="<?= $colorFilaTdEstadoFormula ?>"><?php echo($id_formulacion); ?></td>
                        <td class="td_reducido" align="center" style="<?= $colorFilaTdEstadoFormula ?>"><?= $lugar ?></td>
                        <td class="td_reducido" align="center" style="<?= $colorFilaTdEstadoFormula ?>"><?php echo($fecha_formulacion); ?></td>
                        <td class="td_reducido" align="left" style="<?= $colorFilaTdEstadoFormula ?>"><?php echo($tipoCitaFormulacion); ?></td>
                        <td class="td_reducido" align="left" style="<?= $colorFilaTdEstadoFormula ?>"><?= ($nombreConvenio . "-" . $nombrePlan) ?></td>
                        <td class="td_reducido" align="center" style="<?= $colorFilaTdEstadoFormula ?>"><?php echo($cantidadOrden); ?></td>
                        <td class="td_reducido" align="center" style="<?= $colorFilaTdEstadoFormula ?>"><?php echo($cantidadPendiente); ?></td>
                        <td class="td_reducido" align="left" style="<?= $colorFilaTdEstadoFormula ?>"><?php echo($estadoFormula); ?></td>
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
                            <span>Nueva</span>
                        </td>
                        <td>
                            <input type="text" disabled="" style="background: #D8A31E;width: 30px;margin: 0;padding: 0;">
                        </td>
                        <td style="text-align: left;">
                            <span>Con pendientes</span>
                        </td>

                        <td>
                            <input type="text" disabled="" style="background: #CEFDAF;width: 30px;margin: 0;padding: 0;">
                        </td>
                        <td style="text-align: left;">
                            <span>Cerrada</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <script id='ajax'>
                //<![CDATA[ 
                $(function () {
                    $('#tbl_formulas', 'table').each(function (i) {
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
        </div>
        <?php
    } else {
        ?>
        <script id='ajax'>
            /* Ciclo para medicamentos */
            for (var i = 0; i < 10; i++) {
                initCKEditorOptom("frecAdmMed_" + i);
            }
        </script>
        <?php
        $class_ordenes_remisiones->getFormularioOrdenarMedicamentos(NULL, $id_paciente, 2, 1);
    }
    ?>

    <br>
    <br>
    <?php
}

switch ($opcion) {
    case "1": //Consultar HC del paciente
        $txt_paciente = $_POST["txt_paciente"];
        $tipoAccion = $_POST["tipoDeAccion"];

        if ($tipoAccion == 1) {
            $tabla_pacientes = $dbFormulasMedicamentos->consultarDespachoFormulacionesPacientes($txt_paciente);
        } else {
            $tabla_pacientes = $dbFormulasMedicamentos->consultarPacientesHomologarFormula($txt_paciente);
            ?>
            <h3>Homologar formula</h3>
            <?php
        }
        	$cantidad_pacientes = count($tabla_pacientes);

        if ($cantidad_pacientes == 1) {//1 registro
            $id_paciente = $tabla_pacientes[0]['id_paciente'];
            $nombre_1 = $tabla_pacientes[0]['nombre_1'];
            $nombre_2 = $tabla_pacientes[0]['nombre_2'];
            $apellido_1 = $tabla_pacientes[0]['apellido_1'];
            $apellido_2 = $tabla_pacientes[0]['apellido_2'];
            $numero_documento = $tabla_pacientes[0]['numero_documento'];
            $tipo_documento = $tabla_pacientes[0]['tipo_documento'];
            $fecha_nacimiento = $tabla_pacientes[0]['fecha_nac_persona'];
            $telefonos = $tabla_pacientes[0]['telefono_1'] . " - " . $tabla_personas[0]['telefono_2'];
            $nombres_apellidos = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
            $edad_paciente = $tabla_pacientes[0]['edadPaciente'];
            $nombre_convenio = $tabla_pacientes[0]['nombre_convenio'];
            $nombre_plan = $tabla_pacientes[0]["nombrePlanAux"];
            $estado_convenio = $tabla_pacientes[0]['status_convenio_paciente'];

            ver_formulaciones($tabla_pacientes[0]['id_paciente'], $nombres_apellidos, $numero_documento, $tipo_documento, $telefonos, $fecha_nacimiento, $edad_paciente, $nombre_convenio, $nombre_plan, $estado_convenio, $tipoAccion);
        } else if ($cantidad_pacientes > 1) {//Múltiples registros
            ?>
            <table id='tabla_persona_hc'  border='0' class="paginated modal_table" style="width: 70%; margin: auto;">
                <thead>
                    <tr class='headegrid'>
                        <th class="headegrid" align="center">Documento</th>	
                        <th class="headegrid" align="center">Pacientes</th>
                    </tr>
                    <?php
                    foreach ($tabla_pacientes as $fila_paciente) {

                        $nombre_1 = $fila_paciente['nombre_1'];
                        $nombre_2 = $fila_paciente['nombre_2'];
                        $apellido_1 = $fila_paciente['apellido_1'];
                        $apellido_2 = $fila_paciente['apellido_2'];
                        $idPaciente = $fila_paciente['id_paciente'];
                        $numero_documento = $fila_paciente['numero_documento'];
                        $nombres_apellidos = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
                        $telefonos = $fila_paciente['telefono_1'];
                        $fechaNacimiento = $fila_paciente['fecha_nac_persona'];


                        $edad = $fila_paciente['edadPaciente'];
                        $convenio = $fila_paciente['nombre_convenio'];
                        $nombre_plan = $fila_paciente["nombrePlanAux"];
                        $estadoConvenio = $fila_paciente['status_convenio_paciente'];
                        $tipo_documento = $fila_paciente['tipo_documento'];
                        ?>
                        <tr class='celdagrid' onclick="ver_formulaciones_paciente(<?= $idPaciente ?>, '<?= $nombres_apellidos ?>', '<?= $numero_documento ?>', '<?= $tipo_documento ?>', '<?= $telefonos ?>', '<?= $fechaNacimiento ?>', '<?= $edad ?>', '<?= $convenio ?>', '<?= $nombre_plan ?>', '<?= $estadoConvenio ?>', <?= $tipoAccion ?>)">
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
                    <p>No se encontraron formulaciones de medicamentos para el paciente</p>
                </div>
                <?php
            } else {
                ?>

                <div class='msj-vacio'>
                    <p>!El paciente no se encuentra en la base de datos¡</p>
                    <div>
                        <input class="btnPrincipal" type="button" value="Nuevo paciente" id="btn_consultar" name="btn_consultar" onclick="nuevo_paciente_btn();" />
                    </div>
                </div>               
                <?php
            }
        }
        break;

    case "2":
        $cantidadMaximaPorDespachar = $variables->getVariable(21);
        $idFormulacion = $utilidades->str_decode($_POST["idFormulacion"]);
        $formulacion = $dbFormulasMedicamentos->getFormulaByiD($idFormulacion);
        $medicamentos = $dbFormulasMedicamentos->getMedicamentosByFormulaDespacho($formulacion['id_formula_medicamento']);
		
		
        $despachos = $dbFormulasMedicamentos->getDespachosByFormulacion($formulacion['id_formula_medicamento']);

        $cantidadMedicamentos = count($medicamentos);
        $estadoFormula = "";
        $indVisibleBtnDespachar = "";
        $indDespachar = false;
        $displayDiasTranscurridos = "";

        $indEstadoConvenio = $formulacion['ind_activo_convenio_aux'];
        $indEstadoPlan = $formulacion['ind_activo_plan_aux'];
        $indPlanDespachoMedicamentos = $formulacion['ind_despacho_medicamentos_aux'];
        $mensajeExcepcion = "";
        $indExcepcion = false;

        switch ($formulacion['estado_formula_medicamento']) {
            case 1://Nueva
                $estadoFormula = "Nueva";
                break;
            case 2://Con pendientes
                $estadoFormula = "Con pendientes";
                $colorFilaEstadoFormula = "color:#D8A31E;";
                break;
            case 3://Cerrada
                $estadoFormula = "Cerrada";
                $colorFilaEstadoFormula = "color:#399000;";
                $indVisibleBtnDespachar = "display:none";
                $displayDiasTranscurridos = "display:none";
                break;
        }

        $lugar = $formulacion['lugar_aux'];

        $tipoFormulaMedica = "";
        $displayObservacionHomologada = "";
        switch ($formulacion['tipo_formula_medicamento']) {
            case 1://Directa desde UT
                $tipoFormulaMedica = "Directa desde UT";
                break;
            case 2://Homologada
                $tipoFormulaMedica = "Homologada";
                break;
        }

        //Valida estados del plan, del convenio y los permisos para despachar medicamentos
        if ($indEstadoPlan == 0 || $indEstadoConvenio == 0) {
            $indExcepcion = true;
            $mensajeExcepcion = "El convenio o el plan al que pertenece la f&oacute;rmula m&eacute;dica se encuentra inactivo";
        } else {
            if ($indPlanDespachoMedicamentos != 1) {
                $indExcepcion = true;
                $mensajeExcepcion = "El plan del convenio al que pertecene la f&oacute;rmula m&eacute;dica no se encuentra habilitado para despachar medicamentos";
            }
        }
        ?>
        <div style="width: 90%; margin: 0 auto;padding: 5px 0px">
            <div style="text-align: right;">
                <input class="btnPrincipal" type="button" value="Regresar" id="btn_consultar" name="btn_consultar" onclick="btnRegresar();" />
                <input type="hidden" id="hddIdPaciente" name="hddIdPaciente" value="<?= $formulacion['numero_documento'] ?>" />
                <input type="hidden" id="hddCantidadMaximaDespacho" name="hddCantidadMaximaDespacho" value="<?= $cantidadMaximaPorDespachar['valor_variable'] ?>" />
            </div>
            <div>
                <table style="width: 100%;">
                    <tr>
                        <td>
                            <table style="border: 1px solid #333;">
                                <tr>                                    
                                    <td align="left" style="font-size: 24pt;text-align: center;">
                                        <b><?php echo($formulacion['id_formula_medicamento']); ?></b>
                                        <input type="hidden" value="<?php echo($formulacion['id_formula_medicamento']); ?>" id="hddIdFormulaMedicamento" name="hddIdFormulaMedicamento" />
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" >Código de la formulación:</td>
                                </tr>                                
                            </table>
                        </td>
                        <td>
                            <table style="border: 1px solid #333;">
                                <tr>
                                    <td align="right">Convenio/Plan:</td>
                                    <td align="left"><b style="color: #DD504F;"><?php echo($formulacion['nombreConvenioAux'] . "/" . $formulacion['nombrePlanAux']); ?></b></td>
                                </tr>
                                <tr>
                                    <td align="right">Fecha de formulaci&oacute;n:</td>
                                    <td align="left"><b><?php echo($formulacion['fechaFormulacion']); ?></b></td>
                                </tr>
                                <tr>
                                    <td align="right">Cantidad de productos:</td>
                                    <td align="left"><b><?php echo($cantidadMedicamentos); ?></b></td>
                                </tr>
                                <tr style="<?= $displayDiasTranscurridos ?>">
                                    <td align="right">D&iacute;as transcurridos desde la formulaci&oacute;n:</td>
                                    <td align="left"><b><?php echo($formulacion['diasTranscurridos']); ?> D&iacute;as</b></td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table style="border: 1px solid #333;">
                                <tr>                                    
                                    <td align="left" style="font-size: 24pt;text-align: center;<?= $colorFilaEstadoFormula ?>"><b><?php echo($estadoFormula); ?></b></td>
                                </tr>
                                <tr>
                                    <td align="right">Estado de la formulaci&oacute;n</td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                    <?php
                    if ($formulacion['estado_formula_medicamento'] == 3) {
                        ?>
                        <tr>
                            <td colspan="3">
                                <span>Fecha de Cierre </span><span style="color: #399000;font-weight: bold;"><?= $formulacion['fecha_cierre'] ?></span>
                            </td>
                        </tr>
                        <?php
                    }
                    if ($formulacion['tipo_formula_medicamento'] == 2) {
                        ?>
                        <tr>
                            <td colspan="">
                                <span style="font-weight: bold;">Observación de la homologaci&oacute;n:</span>
                            </td>
                            <td colspan="2" style="text-align: left;">
                                <span style="color: #BE8F1A;"><?= $formulacion['observ_formula_medicamento'] ?></span>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>

            <div>
                <p>Tipo: <span style="color: #399000;font-weight: bold;"><?= $tipoFormulaMedica ?></span></p>
                <p>Lugar de radicaci&oacute;n: <span style="color: #399000;font-weight: bold;"><?= $lugar ?></span></p>
                <input class="btnPrincipal" type="button" value="Ver f&oacute;rmula" id="btnDespachar" name="btnDespachar" onclick="imprimir_orden_medicamentos_despacho();" />
            </div>

            <div>   
                <table id="tablaPrecios" class="modal_table">
                    <tbody><tr id="tabla_encabezado">
                            <th style="width:23%;">Medicamento</th>
                            <th style="width:5%;">Cantidad formulada</th>
                            <th style="width:5%;">Cantidad pendiente</th>
                            <th style="width:5%;">Cantidad por despachar</th>
                            <th style="width:10%;">Lote</th>
						 <?php
                         if($tipo_acceso_menu == 2){
                             ?>
                              	<th style="width:3%;"></th>     
                             <?php
                             
                             }
                         ?>
                                                                         
                        </tr>
                        <?php
                        $contador = 1;

                        foreach ($medicamentos as $medicamento) {
							//var_dump($medicamento);
							
                            $indDisable = "disabled";
                            $unidadMedida = $medicamento['unidad_medida'];
                            $codigoSiesa = $medicamento['cod_siesa'];
							$id_lugar_formula_medica = $medicamento['id_lugar_formula_medica'];
							 
                            if ($medicamento['cantidad_pendiente'] > 0) {
                                $indDisable = "";
                            }
                            ?>
                            <tr title="<?= strip_tags($medicamento['forma_admin']) ?>">
                                <td style="text-align: left;">
                                    <?= $medicamento["cod_siesa"]." - ".$medicamento["nombre_comercial"]." (".$medicamento["nombre_generico"].")" ?>
                                    <input type="hidden" id="hddIdFormulaMedicamentoDet<?= $contador ?>" name="hddIdFormulaMedicamentoDet<?= $contador ?>" value="<?= $medicamento['id_formula_medicamento_det'] ?>" />
                                    <input type="hidden" id="hddUnidadMedida<?= $contador ?>" name="hddUnidadMedida<?= $contador ?>" value="<?= $unidadMedida ?>" />
                                    <input type="hidden" id="hddCodigoSiesa<?= $contador ?>" name="hddCodigoSiesa<?= $contador ?>" value="<?= $codigoSiesa ?>" />
                                     <input type="hidden" id="hddIdLugarFormulaMedica<?= $contador ?>" name="hddIdLugarFormulaMedica<?= $contador ?>" value="<?= $id_lugar_formula_medica ?>" />
                                    
                                   
                                </td>
                                <td>
                                    <?= $medicamento['cantidad_orden'] ?>
                                     <input type="hidden" id="hddCantOrden<?= $contador ?>" name="hddCantOrden<?= $contador ?>" value="<?= $medicamento['cantidad_orden'] ?>" />
                                </td>
                                <td>
                                    <?= $medicamento['cantidad_pendiente'] ?>
                                    <input type="hidden" id="hddCantPendiente<?= $contador ?>" name="hddCantPendiente<?= $contador ?>" value="<?= $medicamento['cantidad_pendiente'] ?>" />
                                </td>
                                <td>
                                    <input type="text" id="txtCantidad<?= $contador ?>" name="txtCantidad<?= $contador ?>" style="color: #399000;" <?= $indDisable ?>  />
                                </td>
                                <td>
                                    <input type="text" id="txtLote<?= $contador ?>" name="txtLote<?= $contador ?>" style="color: #399000;" <?= $indDisable ?> />
                                </td>
                                 <?php
									 if($tipo_acceso_menu == 2 && $formulacion["estado_formula_medicamento"] <> 1){
										 ?>
											 <td align="center">
                                	 				<img src="../imagenes/icon-error.png" class="btnAnularMedicamento" name="btnAnularMedicamento" id="btnAnularMedicamento" onclick="eliminar_medicamento(<?php echo($contador); ?>);">
                                             </td>
                                        <?php
										 
										 }
								?>
                             
                            </tr>
                            <?php
                            $contador ++;
                        }
                        ?>
                        <tr>
                            <td colspan="5">
                                <input type="hidden" id="hddCantMedicamentosDespacho" name="hddCantMedicamentosDespacho" value="<?= count($medicamentos) ?>" />
                            </td>
                        </tr>    
                    </tbody>
                </table>
                <br />
                
                 <div id="d_gif_cargando" class=""></div>  
                <br>
                <?php
                if ($indExcepcion) {
                    ?>
                    <div>
                        <h4 style="color: #D32F24;"><?= $mensajeExcepcion ?></h4>
                    </div>
                    <?php
                } else {
                    if ($tipo_acceso_menu == 2) {//Permisos de creación
                        ?>
                        <table style="width: 550px; margin-bottom: 20px; margin: 0 auto;">
                            <tr>
                            	<td align="left">
                                	Observaciones*
                                </td>
                            </tr>
                            <tr>
                            	<td align="left">
                                	<textarea id="txt_observaciones_despacho" name="txt_observaciones_despacho" style="height:80px;"></textarea>
                                </td>
                            </tr>
                            <tr>                               
                                <td align="left">
                                    <?php
                                    $disabled = 1;

                                    $lista_lugares = $dbListas->getListaDetalles(12, 1);
                                    $combo->getComboDb("cmb_lugar_despacho", "", $lista_lugares, "id_detalle, nombre_detalle", "-- Seleccione el lugar de despacho --", "", "", "width:100%;");
                                    ?>
                                </td>
                            </tr>
                        </table>
                        <div>
                            <input style="<?= $indVisibleBtnDespachar ?>" class="btnPrincipal" type="button" value="Despachar medicamentos" id="btnDespachar" name="btnDespachar" onclick="validarDespacho();" />
                            <div id="d_resultado_despacho" style="display:none;"></div>
                        </div>
                        <!-- Resultado anulacion de un medicamento -->    
                        <div id="d_resultado_anulacion" style="display:none;"></div>            
                        <?php
                    }
                }
                ?>
            </div>
            <div>
                <?php
                $cantidadDespachos = count($despachos);
                $ultimoDespacho = -1;
                if ($cantidadDespachos > 0) {
                    ?>
                    <p style="font-weight: bold;">Historial de despachos</p>
                    <div style="width: 50%">
                        <span>(Úbique el puntero del mouse sobre cada registro para ver la observaci&oacute;n del despacho, <span style="font-weight: bold;">para ver el reporte de salida de inventario de clic en el mismo</span>)</span>
                    </div>
                    <table id="tablaPrecios" class="modal_table peq" style="width: 50%;">
                        <tbody><tr id="tabla_encabezado">
                                <th style="width:12%;">Código</th>
                                <th style="width:35%;">Lugar</th>  
                                <th style="width:20%;">Fecha</th>
                                <th style="width:32%;">Conector</th>
                            </tr>
                            <?php
                            $tmpContador = 0;
                            $fechaUltimoDespacho = "";
                            foreach ($despachos as $despacho) {
                                if ($tmpContador == 0) {
                                    $fechaUltimoDespacho = $despacho['fecha_crea'];
                                    $tmpContador = 1;
                                }
                                ?>
                                <tr onclick="generar_reporte_despacho_medicamentos(<?= $despacho['id_formula_medicamento_desp'] ?>);" title="<?= $despacho['observ_formula_medicamento_desp'] ?>">
                                    <td style="text-align: center;">
                                        <?= $despacho['id_formula_medicamento_desp'] ?>                                    
                                    </td>
                                    <td style="text-align: center;">
                                        <?= $despacho['lugar_desp_aux'] ?>                                    
                                    </td>
                                    <td style="text-align: center;">
                                        <?= $despacho['fecha_crea'] ?>                                    
                                    </td>
                                    <td style="text-align: center;">
                                        <?= $despacho['doct_siesa_desp'] ?>                                    
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                    </table>
                    <?php
                    $fechainicial = new DateTime($fechaUltimoDespacho);
                    $fechafinal = new DateTime("now");
                    $diferencia = $fechainicial->diff($fechafinal);
                    $mesesDiferencia = (($diferencia->y * 12) + $diferencia->m);
                    $ultimoDespacho = $mesesDiferencia;
                }
                ?>

                <input type="hidden" id="hddUltimoDespacho" name="hddUltimoDespacho" value="<?= $ultimoDespacho ?>" />
            </div>	
        </div>
        <?php
        break;

    case "3"://Realiza el despacho
	
        $conectorSiesa = new Class_Conector_Siesa();
        $classTercerosSiesa = new Class_Terceros_Siesa();
        $totalMedicamentos = $utilidades->str_decode($_POST["totalMedicamentos"]);
        $idFormulacion = $utilidades->str_decode($_POST["idFormulacion"]);
        $observacion = $utilidades->str_decode($_POST["observacion"]);
        $idPaciente = $utilidades->str_decode($_POST["idPaciente"]);
        $arrayDespacho = "";
        $banderaPasoUno = false;
        $banderaVerificarTerceroCliente = false;
        $banderaPasoDos = false;
        $mensajeErrorIntegracion = "";

        //Obtiene datos para SIESA
        $lugarDespacho = $utilidades->str_decode($_POST["lugar_despacho"]);
        $sedeDetalle = $dbListas->getSedesDetalle($lugarDespacho);
        $compania = $sedeDetalle["id_compania_dispensacion"]; //Código de compañía SIESA para dispensación
        $coDispensacion = $sedeDetalle["id_co_dispensacion"]; //Código del centro de operaciones SIESA para dispensación
        $bodega = $sedeDetalle["id_bodega_dispensacion"]; //Código de la bodega SIESA para dispensación
		
        /* Arma el array */
        $tmpContador = 1;
        $arrayMovimientos = array();
        for ($i = 1; $i <= $totalMedicamentos; $i++) {
            $tmpIdFormulaMedicamentoDet = $utilidades->str_decode($_POST["idFormulaMedicamentoDet" . $i]);
            $tmpCantidad = $utilidades->str_decode($_POST["cantidad" . $i]);
            $tmpLote = $utilidades->str_decode($_POST["lote" . $i]);
            $unidadMedida = $utilidades->str_decode($_POST["unidadMedida" . $i]);
            $codigoSiesa = $utilidades->str_decode($_POST["codigoSiesa" . $i]);

            /* Array para el procedimiento de almacenado HCUT */
            $arrayDespacho .= $codigoSiesa . ";" . $unidadMedida . ";" . $tmpIdFormulaMedicamentoDet . ";" . $tmpCantidad . ";" . $tmpLote . ($i == $totalMedicamentos ? "" : "|");
            $arrayMovimientos[$i] = array(
				"NUM_REGISTRO" => $i,
				"BODEGA" => $bodega,
				"LOTE" => $tmpLote,
				"CENTRO_OPERACION" => $coDispensacion,
				"UNIDAD_MEDIDA" => "UND",
				"CANTIDAD" => $tmpCantidad,
				"REFERENCIA" => $codigoSiesa
			);
        }

        /* realiza paso 1 en el procedimiento de almacenado */
        $resultado = -5;

        /* Realiza el paso uno (1) en el procedimiento de almacenado */
        $lugarDespacho = $_SESSION["idLugarUsuario"];
		
        $rtaPasoUno = $dbFormulasMedicamentos->despacharMedicamentos($arrayDespacho, $totalMedicamentos, $idFormulacion, $_SESSION["idUsuario"], $observacion, 1, "", $lugarDespacho);
				
        if ($rtaPasoUno == 1) {
            $banderaPasoUno = true;
        }

        if ($banderaPasoUno) {//Sí el paso 1 del pa_ es exitoso            
		
            //verifica existencia del tercero, si no existe lo crea como tercero cliente
            $rtaTerceroCliente = $classTercerosSiesa->crearTerceroCliente($idPaciente, $compania, $lugarDespacho, $id_usuario);
			
            if ($rtaTerceroCliente == 1) {
                $banderaVerificarTerceroCliente = true;
            }
			
            if ($banderaVerificarTerceroCliente) {//Continuar con la dispensación
                $auxPaciente = $pacientes->getPaciente($idPaciente);
                $array = array(
                    "Documentos" => array(
                        "FECHA_DOC" => $conectorSiesa->getFechaActual(),
                        "TERCERO" => $auxPaciente['numero_documento'], //Numero de documento del tercero Cliente en SIESA
                        "NOTAS_DOCUMENTO" => "Despacho por HCUT - Formula medica: ".$idFormulacion." - Codigo usuario: ".$_SESSION["idUsuario"]
                    ),
                    "Movimientos" => $arrayMovimientos//Este array es construido en el for que recorre la variable $totalMedicamentos
                );

                //Llama al conector de SALIDAS POR DISPENSACIÓN
                $rtaSalidasPorDispensacion = $conectorSiesa->enviarXML(8, $array, $compania, 0);

                //Valida el resultado de la importación
                if (strlen($rtaSalidasPorDispensacion['ImportarDatosXMLResult']) == 19 && $rtaSalidasPorDispensacion['ImportarDatosXMLResult'] == "Importacion exitosa") {//19 es la longitud de caracteres para la respuesta: Importacion exitosa
                    $banderaPasoDos = true;
                } else {
                    $resultado = -6; //Error
                    $mensajeErrorIntegracion = $rtaSalidasPorDispensacion['ImportarDatosXMLResult'];
					$dbComunicacionSiesa->insertar_comunicacion_siesa(8, $id_paciente, "", "", "", $mensajeErrorIntegracion, $id_usuario);
                }
				
                if ($banderaPasoDos) {
                    //Realiza consulta de la dispensación en SIESA.
                    //La siguiente es la estructura de parámetros para la consulta de "CONSULTA_SALIDAS_INTEGRACION" en SIESA.
                    $array2['CIA'] = $compania; //Compañia, tomada de la sede del usuario que inicia sesión.
                    $array2['CO'] = 100; //Centro de operaciones: 100.
                    $array2['TIPO_DOCTO'] = "SLI"; //Salidas por Integración.
                    $array2['NIT'] = $auxPaciente['numero_documento'];
                    $array2['USUARIO'] = "unoee"; //Usuario Integrador en SIESA.
                    $rtaConsultaDispensacion = $conectorSiesa->ejecutarConsulta(4, $compania, $array2); //Consulta con código 4 en la tabla: servicios_integracion_consulta
                    $documento = $rtaConsultaDispensacion['DOCUMENTO'];
					
                    $rtaPasoDos = $dbFormulasMedicamentos->despacharMedicamentos($arrayDespacho, $totalMedicamentos, $idFormulacion, $_SESSION["idUsuario"], $observacion, 2, $documento, $lugarDespacho);

                    if ($rtaPasoDos > 0) {//Sí el paso dos es exitoso.
                        $resultado = $rtaPasoDos;
                    } else {
                        $resultado = -7; //Despacho exitoso en SIESA pero, error interno en HCUT.
                    }
                }
            } else {
                $resultado = -6; //Error en crearTerceroCliente.
                $mensajeErrorIntegracion = $rtaTerceroCliente;
            }
        } else {
            $resultado = $rtaPasoUno;
        }
        ?>
        <input type="hidden" id="hddResultadoDespacho" name="hddResultadoDespacho" value="<?= $resultado ?>" />
        <input type="hidden" id="hddResultadoIntegracionDespacho" name="hddResultadoIntegracionDespacho" value="<?= $mensajeErrorIntegracion ?>" />
        <?php
        break;

    case "4"://
        require_once("../funciones/pdf/fpdf.php");
        require_once("../funciones/pdf/makefont/makefont.php");
        require_once("../funciones/pdf/funciones.php");
        require_once("../funciones/pdf/WriteHTML.php");

        @$idDespachoMedicamento = $utilidades->str_decode($_POST["idDespachoMedicamento"]);
        $despacho = $dbFormulasMedicamentos->getDesachoById($idDespachoMedicamento);
        $medicamentos = $dbFormulasMedicamentos->getMedicamentosDespachadosByDespacho($idDespachoMedicamento);
        $paciente = $pacientes->getPaciente($despacho['id_paciente']);

        $fontSize = 7;
        $pdf = new FPDF('P', 'mm', array(216, 279));
        $pdf->AliasNbPages();
        $pdfHTML = new PDF_HTML();
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(false);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->bordeMulticell = 0; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde
        $pdf->pie_pagina = false;
        $pdf->AddPage();
        $pdf->SetFont("Arial", "", $fontSize);
        srand(microtime() * 1000000);

        //Logo
        $pdf->Image($despacho['dir_logo_sede_det'], 20, 7, 20);
        $pdf->Cell(40, 24, "", 0, 0, "C");
        $pdf->SetFont("Arial", "B", $fontSize);
        $pdf->SetY(8);
        $pdf->SetX(50);
        $pdf->Cell(15, 4, ajustarCaracteres("FECHA:"), 0, 0, "L");
        $pdf->SetFont("Arial", "", $fontSize);
        $pdf->Cell(50, 4, ajustarCaracteres($despacho['fecha_creacion_aux']), 0, 0, "L");
        $pdf->SetY(12);
        $pdf->SetX(50);
        $pdf->SetFont("Arial", "B", $fontSize);
        $pdf->Cell(15, 4, ajustarCaracteres("USUARIO:"), 0, 0, "L");
        $pdf->SetFont("Arial", "", $fontSize);
        $pdf->Cell(50, 4, ajustarCaracteres($despacho['usuario_despacha_aux']), 0, 0, "L");
        $pdf->SetY(16);
        $pdf->SetX(50);
        $pdf->SetFont("Arial", "B", $fontSize);
        $pdf->Cell(17, 4, ajustarCaracteres("CONECTOR:"), 0, 0, "L");
        $pdf->SetFont("Arial", "", $fontSize);
        $pdf->Cell(48, 4, ajustarCaracteres($despacho['doct_siesa_desp']), 0, 0, "L");
        $pdf->SetY(20);
        $pdf->SetX(50);
        $pdf->SetFont("Arial", "B", $fontSize);
        $pdf->Cell(22, 4, ajustarCaracteres("FORMULACIÓN:"), 0, 0, "L");
        $pdf->SetFont("Arial", "", $fontSize);
        $pdf->Cell(43, 4, ajustarCaracteres($despacho['id_formula_medicamento']), 0, 0, "L");
        $pdf->SetY(10);
        $pdf->SetX(115);
        $pdf->SetFont("Arial", "B", ($fontSize + 8));
        $pdf->Cell(90, 10, ajustarCaracteres("DESPACHO DE MEDICAMENTOS"), 0, 0, "C");
        $pdf->SetY(20);
        $pdf->SetX(120);
        $pdf->SetFont("Arial", "", $fontSize);
        $pdf->Cell(90, 4, ajustarCaracteres("*" . $despacho['id_formula_medicamento_desp'] . "*"), 0, 0, "C");
        $pdf->Ln(7);
        $pdf->SetFont("Arial", "B", $fontSize);
        $pdf->Cell(20, 4, ajustarCaracteres("DOCUMENTO:"), 1, 0, "R");
        $pdf->SetFont("Arial", "", $fontSize);
        $pdf->Cell(76, 4, ajustarCaracteres($paciente['numero_documento']), 1, 0, "C");
        $pdf->SetFont("Arial", "B", $fontSize);
        $pdf->Cell(20, 4, ajustarCaracteres("NOMBRES:"), 1, 0, "R");
        $pdf->SetFont("Arial", "", $fontSize);
        $pdf->Cell(76, 4, ajustarCaracteres($paciente['nombre_1'] . " " . $paciente['nombre_2']), 1, 1, "C");
        $pdf->SetX($pdf->GetX());
        $pdf->SetFont("Arial", "B", $fontSize);
        $pdf->Cell(20, 4, ajustarCaracteres("TIPO:"), 1, 0, "R");
        $pdf->SetFont("Arial", "", $fontSize);
        $pdf->Cell(76, 4, ajustarCaracteres($paciente['codigoDocumento']), 1, 0, "C");
        $pdf->SetFont("Arial", "B", $fontSize);
        $pdf->Cell(20, 4, ajustarCaracteres("APELLIDOS:"), 1, 0, "R");
        $pdf->SetFont("Arial", "", $fontSize);
        $pdf->Cell(76, 4, ajustarCaracteres($paciente['apellido_1'] . " " . $paciente['apellido_2']), 1, 1, "C");

        $pdf->SetX(10);

        $pdf->SetX($pdf->GetX());
        $pdf->SetFont("Arial", "B", $fontSize);
        $pdf->Cell(20, 4, ajustarCaracteres("SEXO:"), 1, 0, "R");
        $pdf->SetFont("Arial", "", $fontSize);
        $pdf->Cell(10, 4, ajustarCaracteres($paciente['codigoSexo']), 1, 0, "C");
        $pdf->SetFont("Arial", "B", $fontSize);
        $pdf->Cell(20, 4, ajustarCaracteres("FECH.NAC"), 1, 0, "R");
        $pdf->SetFont("Arial", "", $fontSize);
        $pdf->Cell(20, 4, ajustarCaracteres($paciente['fecha_nacimiento_t']), 1, 0, "C");
        $pdf->SetFont("Arial", "B", $fontSize);
        $pdf->Cell(10, 4, ajustarCaracteres("EDAD:"), 1, 0, "R");
        $pdf->SetFont("Arial", "", $fontSize);

        $edad = explode('/', $paciente['edad']);
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
        $pdf->Cell(76, 4, ajustarCaracteres($paciente['nombre_convenio']), 1, 1, "C");
        $pdf->Ln(1);

        $pdf->SetFont("Arial", "B", $fontSize);
        $pdf->Cell(192, 5, ajustarCaracteres("-- MEDICAMENTOS DESPACHADOS --"), 0, 1, "C");

        $pdf->SetFont("Arial", "B", $fontSize);
        $pdf->Cell(15, 4, ajustarCaracteres("CANTIDAD:"), 1, 0, "C");
        $pdf->Cell(177, 4, ajustarCaracteres("MEDICAMENTO:"), 1, 1, "C");

        $pdf->SetFont("Arial", "", $fontSize);
        $pdf->bordeMulticell = 1;
        $pdf->h_row2 = 3;
        foreach ($medicamentos as $medicamento) {
            $pdf->SetWidths2(array(15, 177));
            $pdf->SetAligns2(array('C', 'J'));
            $pdf->Row2(array(ajustarCaracteres($medicamento['cantidad_medicamento_desp_det']), ajustarCaracteres($medicamento['nombre_comercial'])));
        }
    	$pdf->Ln(4);
		if(count($medicamentos)<= 0){
			 $pdf->SetTextColor(249,111,111);
			 $pdf->SetFont("Arial", "B", 30);
        	 $pdf->Cell(192, 5, ajustarCaracteres("ANULADO"), 0, 1, "C");
			
		}else{

        $pdf->Ln(30);
        $y_aux = $pdf->GetY();
        $pdf->Line(70, $y_aux, 140, $y_aux);
        $pdf->Cell(192, 4, ajustarCaracteres("RECIBÍ A CONFORMIDAD"), 0, 1, "C");

        $pdf->SetY(120);
        $pdf->SetFont("Arial", "", $fontSize - 1);
        $pdf->Cell(192, 4, ajustarCaracteres($despacho['dir_sede_det'] . " - " . $despacho['tel_sede_det']), 0, 1, "C");
        $pdf->SetFont("Arial", "I", $fontSize - 1);
        $pdf->Cell(96, 4, ajustarCaracteres("Fecha y hora de impresión: " . date("Y/m/d H:i:s")), 0, 0, "L");
        $pdf->Cell(96, 4, ajustarCaracteres("Página " . $pdf->PageNo() . "/{nb}"), 0, 1, "R");

		}
        //Se guarda el documento pdf
        $nombreArchivo = "../tmp/despacho_medicamentos_" . $_SESSION["idUsuario"] . ".pdf";
        $pdf->Output($nombreArchivo, "F");
        ?>
        <input type="hidden" id="hdd_ruta_reporte_pdf" name="hdd_ruta_reporte_pdf" value="<?php echo($nombreArchivo); ?>" />
        <?php
        break;

    case "5"://
        $idPaciente = $utilidades->str_decode($_POST["idPaciente"]);
        $nombre = $utilidades->str_decode($_POST["nombre"]);
        $documento = $utilidades->str_decode($_POST["documento"]);
        $telefonos = $utilidades->str_decode($_POST["telefonos"]);
        $fechaNacimiento = $utilidades->str_decode($_POST["fechaNacimiento"]);
        $edad = $utilidades->str_decode($_POST["edad"]);
        $estadoConvenio = $utilidades->str_decode($_POST["estadoConvenio"]);
        $tipoAccion = $utilidades->str_decode($_POST["tipoAccion"]);
        $tipo_documento = $utilidades->str_decode($_POST["tipoDocumento"]);
        $nombre_convenio = $utilidades->str_decode($_POST["nombre_convenio"]);
        $nombre_plan = $utilidades->str_decode($_POST["nombre_plan"]);

        ver_formulaciones($idPaciente, $nombre, $documento, $tipo_documento, $telefonos, $fechaNacimiento, $edad, $nombre_convenio, $nombre_plan, $estadoConvenio, $tipoAccion);
        break;
		
	case "6": //Realiza la anulación 
		$conectorSiesa = new Class_Conector_Siesa();
		$classConsultasSiesa = new Class_Consultas_Siesa();
		
		//Se toman los datos que vienen del POST.
		$id_usuario 	 				= 	$_SESSION["idUsuario"];
		$documento_paciente		 		= 	$utilidades->str_decode($_POST["idPaciente"]);
		$id_medicamento_det	 			= 	$utilidades->str_decode($_POST["idMedicamento_det"]);
		$id_lugar_formula 				= 	$utilidades->str_decode($_POST["lugarFormula"]);
		$cantidad 	     				=  	$utilidades->str_decode($_POST["cantidad"]);
		$cantidad_pendiente         	= 	$utilidades->str_decode($_POST["cantidadPendiente"]);
		$cod_siesa						=   $utilidades->str_decode($_POST["codSiesa"]);
		$cantidad_medicamentos_table 	= 	$utilidades->str_decode($_POST["cantidadMedicamentosTbl"]);
		$id_formula 	     			=  	$utilidades->str_decode($_POST["idFormula"]);
		
		//Obtiene datos para SIESA
        $sedeDetalle = $dbListas->getSedesDetalle($id_lugar_formula);
        $compania = $sedeDetalle["id_compania_dispensacion"]; //Código de compañía SIESA para dispensación
        $centro_operacion = $sedeDetalle["id_co_dispensacion"]; //Código del centro de operaciones SIESA para dispensación
        $bodega = $sedeDetalle["id_bodega_dispensacion"]; //Código de la bodega SIESA para dispensación
	
		//Se obtienen los datos de los medicamentos despachados
		$medicamentos_despachados_det = $dbFormulasMedicamentos->getMedicamentosDespachadosDet($id_medicamento_det);
		$auxPaciente = $pacientes->getPacienteNumeroDocumento($documento_paciente);
		$id_paciente = $auxPaciente["id_paciente"];
		//$rta = $dbFormulasMedicamentos->cambiarEstadoFormulaMedicamentos($id_medicamento, $cantidad, $cantidad_medicamentos_table, $id_formula, $id_usuario);
		
		//For para anular cada despacho de un medicamento que se ha real
		 $contador = 1;
		 $arrayMovimientos = array();
		 $cantidad = "";
		 $cantidad_orden = "";	
		  
		for($i=0;$i<1;$i++){
			
			$lote = ($medicamentos_despachados_det[$i]["lote"]);
			$cantidad = ($medicamentos_despachados_det[$i]["cantidad_depachada"]);
			$cantidad_orden = ($medicamentos_despachados_det[$i]["cantidad_orden"]);
			
			//Se consultan los valores del medicamento en SIESA por medio del integrador, CONSULTA_INVENTARIOS_X_REFERENCIA.
			$datos_medicamentos = $classConsultasSiesa->consultarCostoUnitarioMedicamento($compania, $cod_siesa, $bodega, $lote);
			
			foreach($datos_medicamentos as $value){
				$costo = $value["COSTO_PROMEDIO_UNITARIO"];
			}
			
			$arrayMovimientos[$i+1] = array(
				"NUM_REGISTRO" => $i+1,
				"BODEGA" => $bodega,
				"LOTE" => $lote,
				"CENTRO_OPERACION_MOVTO" => $centro_operacion,
				"UNIDAD_MEDIDA" => "UND",
				"CANTIDAD" => $cantidad,
				"COSTO_PROMEDIO_UNITARIO" => $costo,
				"REFERENCIA" => $cod_siesa
			);	

		}
			
		//Se crea el array de documentos
		$array = array(
			 "Documentos" => array(
					"FECHA_DOC" => $conectorSiesa->getFechaActual(),
					"TERCERO" => $documento_paciente, //Numero de documento del tercero Cliente en SIESA
					"NOTAS_DOCUMENTO" => "Despacho por HCUT - Formula medica: ".$id_formula." - Codigo usuario: ".$id_usuario,
					),
			 "Movimientos" => $arrayMovimientos //array que se construye en el for   
		);
		
		
		//Llama al conector de ENTRADAS POR DISPENSACIÓN
		$rtaEntradasPorDispensacion = $conectorSiesa->enviarXML(9, $array, $compania, 0);
		$resultado = ($rtaEntradasPorDispensacion['ImportarDatosXMLResult'] == "Importacion exitosa") ? 1 : $rtaEntradasPorDispensacion['ImportarDatosXMLResult'];

		if($resultado == 1){
				//Realiza la anulación en la base de datos.
				$rta = $dbFormulasMedicamentos->cambiarEstadoFormulaMedicamentos($id_medicamento_det, $cantidad,$cantidad_orden, $cantidad_medicamentos_table, $id_formula, $id_usuario);
				if($rta<0){
					//Error en la anulación por base de datos
					$rta =-2;	
				}				
		}else{
				//Error en la anulación de SIESA
				$rta = -3;
				//Guardar el error en la tabla log_comunicacion_SIESA
				$dbComunicacionSiesa->insertar_comunicacion_siesa(9, $id_paciente, "", "", "", $resultado, $id_usuario);		
		}
		?>
        	<input type="hidden" id="hdd_rta_anulacion_medicamento" name="hdd_rta_anulacion_medicamento" value="<?php echo($rta); ?>" />
            <input type="hidden" id="hdd_id_paciente" name="hdd_id_paciente" value="<?php echo($documento_paciente); ?>" />
        <?php

	break;
	
}