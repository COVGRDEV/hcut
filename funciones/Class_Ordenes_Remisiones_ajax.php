<?php
session_start();
//Autor: Feisar Moreno - 01/06/2015

header("Content-Type: text/xml; charset=UTF-8");

require_once("../db/DbHistoriaClinica.php");
require_once("../db/DbAdmision.php");
require_once("../db/DbMenus.php");
require_once("../db/DbDisponibilidadProf.php");
require_once("../db/DbRemisiones.php");
require_once("../db/DbMaestroMedicamentos.php");
require_once("../db/DbMaestroProcedimientos.php");
require_once("../db/DbPaquetesProcedimientos.php");
require_once("../db/DbFormulasMedicamentos.php");
require_once("../db/DbOrdenesMedicas.php");
require_once("../db/DbCiex.php");
require_once("../db/DbPlanes.php");
require_once("../db/DbDiagnosticos.php");

require_once("../principal/ContenidoHtml.php");
require_once("../funciones/Utilidades.php");
require_once("Class_Combo_Box.php");

$dbHistoriaClinica = new DbHistoriaClinica();
$dbAdmision = new DbAdmision();
$dbMenus = new DbMenus();
$dbDisponibilidadProf = new DbDisponibilidadProf();
$contenido = new ContenidoHtml();
$utilidades = new Utilidades();
$combo = new Combo_Box();
$dbRemisiones = new DbRemisiones();
$dbmaestroMedicamentos = new DbMaestroMedicamentos();
$dbmaestroProcedimientos = new DbMaestroProcedimientos();
$dbformulasMedicamentos = new DbFormulasMedicamentos();
$dbpaquetesProcedimientos = new DbPaquetesProcedimientos();
$dbOrdenesMedicas = new DbOrdenesMedicas();
$dbCiex = new DbCiex();
$dbPlanes = new DbPlanes();
$dbDiagnosticos = new DbDiagnosticos();

$contenido->validar_seguridad(1);
$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);

$opcion = $_POST["opcion"];
$id_usuario = $_SESSION["idUsuario"];

switch ($opcion) {
	case "1": /* Guardar/Editar Remisión UT */
		@$tipoRemision = $utilidades->str_decode($_POST["tipoRemision"]);
		@$observacion = $utilidades->str_decode($_POST["observacion"]);
		@$idRemision = intval($utilidades->str_decode($_POST["idRemision"]), 10);
		@$idHc = $utilidades->str_decode($_POST["idHc"]);
		@$indEstado = $utilidades->str_decode($_POST["indEstado"]);
		
		//Se obtiene el lugar de la admisión
		if ($idHc != "") {
			$admision_obj = $dbAdmision->get_admision_hc($idHc);
			$idLugar = $admision_obj["id_lugar_cita"];
		} else {
			$idLugar = $_SESSION["idLugarUsuario"];
		}
		
		$rta = $dbRemisiones->guardarEditarRemision($idHc, $idRemision, $tipoRemision, $observacion, $id_usuario, $indEstado, $idLugar);
		?>
		<input type="hidden" id="hdd_resultadoRemision" name="hdd_resultadoRemision" value="<?= $rta ?>" />		
		<?php
		break;
		
	case "2": //Buscar medicamentos UT
		$rastro = $utilidades->str_decode($_POST["rastro"]);
		?>
		<div class="encabezado">
			<h3>Medicamentos</h3>
		</div>
		<input type="hidden" id="hdd_rastro" name="hdd_rastro" value="<?= $rastro ?>" />
		<table style="width: 100%;">
			<tbody>
				<tr>
					<td>
						<input onkeypress="" type="text" id="txtParametroMedicamentos" name="txtParametroMedicamentos" placeholder="Codigo o nombre del medicamento">
					</td>
					<td style="width: 10%;">
						<input type="submit" onclick="buscarMedicamentos()"  value="Buscar" class="btnPrincipal peq">
					</td>
				</tr>
			</tbody>
		</table>
		<div id="resultadoTblMedicamentos">
		</div>
		<?php
		break;
		
	case "3": /* Resultado - Buscar medicamentos UT */
		$parametro = $utilidades->str_decode($_POST["parametro"]);
		$medicamentos = $dbmaestroMedicamentos->getMedicamentos($parametro);
		$totalMedicamentos = count($medicamentos);
		if ($totalMedicamentos > 0) {
			?>
			<p>(<span style="font-weight: bold;"><?= $totalMedicamentos ?></span>) Medicamentos encontrados</p>
			<table class="paginated modal_table" id="tblMedicamentosBuscados" style="width:98%; margin:auto;">
				<thead>
					<tr>
						<th style="width:9%;">C&oacute;digo</th>
						<th style="width:13%;">Referencia</th>
						<th style="width:40%;">Nombre</th>
						<th style="width:38%;">presentación</th>
					</tr>
				</thead>
				<?php
				foreach ($medicamentos as $medicamento) {
					?>
					<tr id="tbl_Med<?= $medicamento["cod_medicamento"] ?>" onclick="comfirmAgregarMedicamento(<?= $medicamento["cod_medicamento"] ?>)">
                    	<td style="text-align:center;">
                        	<?= $medicamento["cod_medicamento"] ?>
                        </td>
                    	<td style="text-align:center;">
                        	<?= $medicamento["cod_item_siesa"] ?>
                        </td>
						<td style="text-align:left;">
							<span><?= $medicamento["nombre_comercial"]." (".$medicamento["nombre_generico"].")" ?></span>
							<input type="hidden" id="hdd_nomGenMed_<?= $medicamento["cod_medicamento"] ?>" name="hdd_nomGenMed_<?= $medicamento["cod_medicamento"] ?>" value="<?= $medicamento['nombre_generico'] ?>" />
							<input type="hidden" id="hdd_nomComMed_<?= $medicamento["cod_medicamento"] ?>" name="hdd_nomComMed_<?= $medicamento["cod_medicamento"] ?>" value="<?= $medicamento['nombre_comercial'] ?>" />
						</td>
						<td style="text-align:left;">
							<span><?= $medicamento["presentacion"] ?></span>
							<input type="hidden" id="hdd_nomPresMed_<?= $medicamento['cod_medicamento'] ?>" name="hdd_nomPresMed_<?= $medicamento['cod_medicamento'] ?>" value="<?= $medicamento['presentacion'] ?>" />
						</td>
					</tr>
					<?php
				}
				?>
			</table>
			<script id='ajax'>
				//<![CDATA[ 
				$(function () {
					$('#tblMedicamentosBuscados', 'table').each(function (i) {
						$(this).text(i + 1);
					});

					$('table.paginated').each(function () {
						var currentPage = 0;
						var numPerPage = 10;
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
		} else {
			?>
			<p>NO hay resultados para la b&uacute;squeda</p>
			<?php
		}
		break;

	case "4": //Formular medicamentos UT
		$idFormulacionMedicamentos = $utilidades->str_decode($_POST["idFormulacionMedicamentos"]);
		$codMed = $utilidades->str_decode($_POST["codMed"]);
		$cant = $utilidades->str_decode($_POST["cant"]);
		$tiempo = $utilidades->str_decode($_POST["tiempo"]);
		$frecAdmMed = $utilidades->str_decode($_POST["frecAdmMed"]);
		$idFormMedDet = $utilidades->str_decode($_POST["idFormMedDet"]);
		$idHc = $utilidades->str_decode($_POST["idHc"]);
		$idPaciente = $utilidades->str_decode($_POST["idPaciente"]);
		
		$tipoFormulacion = $utilidades->str_decode($_POST["tipoFormulacion"]);
		$medicoRemitente = $utilidades->str_decode($_POST["medicoRemitente"]);
		$fechaMologacion = $utilidades->str_decode($_POST["fechaMologacion"]);
		$observacion = $utilidades->str_decode($_POST["observacion"]);
		
		//Se obtiene el lugar de la admisión
		if ($idHc != "") {
			$admision_obj = $dbAdmision->get_admision_hc($idHc);
			$lugarFormulacion = $admision_obj["id_lugar_cita"];
		} else {
			$lugarFormulacion = $_SESSION["idLugarUsuario"];
		}
		
		$rango = $utilidades->str_decode($_POST["rango"]);
		$tipoCotizante = $utilidades->str_decode($_POST["tipoCotizante"]);
		$plan = $utilidades->str_decode($_POST["plan"]);
		$convenio = $utilidades->str_decode($_POST["convenio"]);
		
		$cod_ciex = $utilidades->str_decode($_POST["cod_ciex"]);
		$ojo = $utilidades->str_decode($_POST["ojo"]);
		
		$resultado = $dbformulasMedicamentos->formularMedicamento($cant, $frecAdmMed, $codMed, $idFormMedDet, $idFormulacionMedicamentos, $idHc, $idPaciente, $id_usuario, $tiempo, $tipoFormulacion, $medicoRemitente, $fechaMologacion, $observacion, $lugarFormulacion, $convenio, $plan, $tipoCotizante, $rango, $cod_ciex, $ojo);
		?>
		<input type="hidden" id="hdd_rta_formulacion_medicamentos" name="hdd_rta_formulacion_medicamentos" value="<?= $resultado ?>" />
		<?php
		break;

	case "5": /* Eliminar medicamentos UT */
		$formulaMedicamento = $utilidades->str_decode($_POST["formulaMedicamento"]);
		$formulaMedicamentoDet = $utilidades->str_decode($_POST["formulaMedicamentoDet"]);

		$resultado = $dbformulasMedicamentos->eliminarFormularMedicamento($formulaMedicamentoDet, $formulaMedicamento, $id_usuario);
		?>
		<input type="hidden" id="hdd_rta_eliminar_formulacion_medicamentos" name="hdd_rta_eliminar_formulacion_medicamentos" value="<?= $resultado ?>" />
		<?php
		break;
	case "6": /* Buscar procedimientos o paquetes UT */
		?>
		<div class="encabezado">
			<h3>Procedimientos y paquetes</h3>
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
					<div class="d_buscar" onclick="btnVentanaBuscarProcedimientoOrdenMedica();"></div>
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
					<td align="right">Proveedor:</td>
					<td align="left" colspan="2">
						<input type="hidden" id="codProvModal" value="" />
						<span id="nomProvModal"></span>
					</td>
					<td style="text-align: center;color: #399000;font-weight: bold;">
						<div class="d_buscar" onclick="btnVentanaBuscarProveedor();"></div>
					</td>
				</tr>
				<tr>
					<td colspan="5">
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
	case "7": /* Buscar procedimientos o paquetes UT */
		$secuencia = $utilidades->str_decode($_POST["secuencia"]);
		?>
		<input type="hidden" id="hdd_secuencia" name="hdd_secuencia" value="<?= $secuencia ?>" />
		<br />
		<table style="width: 100%;">
			<tbody>				
				<tr>
					<td>
						<input type="text" id="txtParametro" name="txtParametro" placeholder="Codigo o nombre del procedimiento">
					</td>
					<td style="width: 10%;">
						<input type="submit" onclick="buscarProcedimientosOrdenMedica(<?= $secuencia ?>)" value="Buscar" class="btnPrincipal peq">
					</td>
				</tr>
			</tbody>
		</table>
		<div id="resultadoTbl"></div>
		<?php
		break;
	case "8": /* Detalles del paquete UT */
		$idPaquete = $utilidades->str_decode($_POST["idPaquete"]);
		$msgTipo = $utilidades->str_decode($_POST["msgTipo"]);
		$nombrePaquete = $utilidades->str_decode($_POST["nombrePaquete"]);

		$procedimientos = $dbpaquetesProcedimientos->getProcedimientosByPaquete($idPaquete);
		?>
		<div>
			<table style="width: 100%;">
				<tr>
					<td style="text-align: right; width: 50%;">
						Tipo:
					</td>
					<td style="text-align: left;">
						<span style="color: #399000;"><?= $msgTipo ?></span>
						<input type="hidden" id="hddDetTipoPaq" name="hddDetTipoPaq" value="<?= $msgTipo ?>" />
					</td>
				</tr>
				<tr>
					<td style="text-align: right; width: 50%;">
						Nombre:
					</td>
					<td style="text-align: left;">
						<span style="color: #399000;"><?= $nombrePaquete ?></span>
						<input type="hidden" id="hddDetNomPaq" name="hddDetNomPaq" value="<?= $nombrePaquete ?>" />
					</td>
				</tr>
				<tr>
					<td style="text-align: right; width: 50%;">
						C&oacute;digo:
					</td>
					<td style="text-align: left;">
						<span style="color: #399000;">#<?= $idPaquete ?></span>
						<input type="hidden" id="hddDetIdPaq" name="hddDetIdPaq" value="<?= $idPaquete ?>" />
					</td>
				</tr>
				<tr>
					<td style="text-align: right; width: 50%;">
						Procedimientos:
					</td>
					<td style="text-align: left;">
						<span style="color: #399000;"><?= count($procedimientos) ?></span>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div style="height: 300px;overflow: scroll;">
							<?php
							if (count($procedimientos) >= 1) {
								?>
								<table class="paginated modal_table" id="tblProcedimientosPorParquete">
									<thead>
										<tr>				   
											<th style="">C&oacute;digo CUPS</th>
											<th style="">Nombre</th>
										</tr>
									</thead>
									<?php
									foreach ($procedimientos as $procedimiento) {
										$codigo = $procedimiento['cod_procedimiento'];
										$nombre = $procedimiento['nombre_procedimiento'];
										?>
										<tr id="" onclick="">
											<td style="text-align: left;">
												<span><?= $codigo ?></span>							
											</td>
											<td style="text-align: left;">
												<span><?= $nombre ?></span>							
											</td>
										</tr>
										<?php
									}
									?>
								</table>

								<?php
							} else {
								?>
								<table>
									<tr>
										<td>
											No hay procedimientos agregados
										</td>
									</tr>
								</table>
								<?php
							}
							?>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<?php
		break;

	case "9": /* Detalles del procedimiento UT */
		$idProcedimiento = $utilidades->str_decode($_POST["idProcedimiento"]);
		$descripcion = $utilidades->str_decode($_POST["descripcion"]);
		?>

		<h5>C&oacute;digo CUPS:</h5>
		<h5 style="color: #399000;"><?= $idProcedimiento ?></h5>
		<input type="hidden" id="hddDetIdProcedimiento" name="hddDetIdProcedimiento" value="<?= $idProcedimiento ?>" />
		<input type="hidden" id="hddDetTipoProcedimiento" name="hddDetTipoProcedimiento" value="Procedimiento" />
		<h5>Descripci&oacute;n:</h5>
		<h5 style="color: #399000;"><?= $descripcion ?></h5>
		<input type="hidden" id="hddDetDescProcedimiento" name="hddDetDescProcedimiento" value="<?= $descripcion ?>" />
		<?php
		break;

	case "10": /* Detalles del paquete UT */
		$idPaquete = $utilidades->str_decode($_POST["idPaquete"]);

		$procedimientos = $dbpaquetesProcedimientos->getProcedimientosByPaquete($idPaquete);
		?>
		<div>
			<table style="width: 100%;">
				<tr>
					<td colspan="2">
						<div style="height: 300px;overflow: scroll;">
							<?php
							if (count($procedimientos) >= 1) {
								?>
								<table class="paginated modal_table" id="tblProcedimientosPorParquete">
									<thead>
										<tr>															   
											<th style="" class="th_reducido">Tipo</th>
											<th style="" class="th_reducido">C&oacute;digo</th>
											<th style="" class="th_reducido">Nombre</th>
										</tr>
									</thead>
									<?php
									foreach ($procedimientos as $procedimiento) {
										$tipo = $procedimiento['tipo_producto'];
										$codigo = "";
										$nombre = "";
										switch ($tipo) {
											case "P":
												$codigo = $procedimiento['cod_procedimiento'];
												$nombre = $procedimiento['nombre_procedimiento'];
												break;
											default:
												$codigo = $procedimiento['cod_insumo'];
												$nombre = $procedimiento['nombre_insumo'];
												break;
										}
										?>
										<tr id="" onclick="">											
											<td style="text-align: center;" class="td_reducido">
												<span><?= $tipo ?></span>							
											</td>
											<td style="text-align: center;" class="td_reducido">
												<span><?= $codigo ?></span>							
											</td>
											<td style="text-align: left;" class="td_reducido">
												<span><?= $nombre ?></span>							
											</td>
										</tr>
										<?php
									}
									?>
								</table>

								<?php
							} else {
								?>
								<table>
									<tr>
										<td>
											No hay procedimientos agregados
										</td>
									</tr>
								</table>
								<?php
							}
							?>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<?php
		break;
		
	case "11": /* Crear orden médica Homologada o Directa */
		$ciex = $utilidades->str_decode($_POST["ciex"]);
		$descripcion = $utilidades->str_decode($_POST["descripcion"]);
		$codProcedimiento = $utilidades->str_decode($_POST["codProcedimiento"]);
		$tipoOrdenMedica = $utilidades->str_decode($_POST["tipoOrdenMedica"]);
		$tipoProducto = $utilidades->str_decode($_POST["tipoProducto"]);
		$idHc = $utilidades->str_decode($_POST["idHc"]);
		$idPaciente = $utilidades->str_decode($_POST["idPaciente"]);
		$usuarioCrea = $id_usuario;
		$notaAclaratoria = $utilidades->str_decode($_POST["notaAclaratoria"]);
		$datClinicos = $utilidades->str_decode($_POST["datosClinicos"]);
		$ojo = $utilidades->str_decode($_POST["ojo"]);
		$medicoRemitente = isset($_POST["medicoRemitente"]) ? $utilidades->str_decode($_POST["medicoRemitente"]) : "";
		$fechaHomologacion = isset($_POST["fechaHomologacion"]) ? $utilidades->str_decode($_POST["fechaHomologacion"]) : "";
		$observacion = isset($_POST["observacion"]) ? $utilidades->str_decode($_POST["observacion"]) : "";
		$rango = isset($_POST["rango"]) ? $utilidades->str_decode($_POST["rango"]) : "";
		$tipoCotizante = isset($_POST["tipoCotizante"]) ? $utilidades->str_decode($_POST["tipoCotizante"]) : "";
		$plan = isset($_POST["plan"]) ? $utilidades->str_decode($_POST["plan"]) : "";
		$convenio = isset($_POST["convenio"]) ? $utilidades->str_decode($_POST["convenio"]) : "";
		
		//Se obtiene el lugar de la admisión
		if ($idHc != "") {
			$admision_obj = $dbAdmision->get_admision_hc($idHc);
			$idLugar = $admision_obj["id_lugar_cita"];
		} else {
			$idLugar = $_SESSION["idLugarUsuario"];
		}
		
		$idOrdenMedDet = $utilidades->str_decode($_POST["idOrdenMedDet"]);
		$idOrdenMedica = $utilidades->str_decode($_POST["idOrdenMedica"]);

		$resultado = $dbOrdenesMedicas->crear_orden_medica($ciex, $descripcion, $codProcedimiento, $tipoOrdenMedica, $idOrdenMedica, $idOrdenMedDet, $idHc, $idPaciente, $id_usuario, $notaAclaratoria, $datClinicos, $ojo, $idLugar, $medicoRemitente, $fechaHomologacion, $observacion, $tipoProducto, $rango, $tipoCotizante, $plan, $convenio);
		//$resultado = -1;
		?>
		<input type="hidden" id="hdd_rta_ordenar_procedimiento" name="hdd_rta_ordenar_procedimiento" value="<?= $resultado ?>" />
		<?php
		break;

	case "12": /* Eliminar orden médica */

		$notaAclaratoria = $utilidades->str_decode($_POST["notaAclaratoria"]);
		$idOrdenMedica = $utilidades->str_decode($_POST["idOrdenMedica"]);
		$idOrdenMedDet = $utilidades->str_decode($_POST["idOrdenMedDet"]);

		$resultado = $dbOrdenesMedicas->eliminar_orden_medica($idOrdenMedica, $idOrdenMedDet, $notaAclaratoria, $id_usuario);
		?>
		<input type="hidden" id="hdd_rta_eliminar_procedimiento_ordenMedica" name="hdd_rta_eliminar_procedimiento_ordenMedica" value="<?= $resultado ?>" />
		<?php
		break;

	case "13": /* Buscar procedimientos o paquetes UT */
		$rastro = $utilidades->str_decode($_POST["rastro"]);
		?>
		<div class="encabezado">
			<h3>&Oacute;rdenes m&eacute;dicas</h3>
		</div>
		<input type="hidden" id="hdd_rastro" name="hdd_rastro" value="<?= $rastro ?>" />
		<br>
		<table style="width: 100%;">
			<tbody>
				<tr>
					<td colspan="2">
						<div style="background: #FDEBB7;padding: 3px 5px;width: 70%;margin: 0 auto;">
							<span>¡Busque el diagn&oacute;stico que desea agregar!</span>
						</div>
						<br>
					</td>
				</tr>				
				<tr>
					<td>
						<input type="text" id="txtParametro" name="txtParametro" placeholder="Codigo o nombre del diagn&oacute;stico">
					</td>
					<td style="width: 10%;">
						<input type="submit" onclick="buscarDiagnosticoOrdenesMedicas()" value="Buscar" class="btnPrincipal">
					</td>
				</tr>
			</tbody>
		</table>
		<div id="resultadoTbl">
		</div>
		<?php
		break;
	case "14": /* Resultado - Buscar diagnosticos UT */
		$parametro = $utilidades->str_decode($_POST["parametro"]);
		$resultados = "";

		$resultados = $dbCiex->buscarDiagnosticos($parametro);

		$totalResultados = count($resultados);

		if ($totalResultados >= 1) {
			?>
			<p>(<span style="font-weight: bold;"><?= $totalResultados ?></span>) Diagn&oacute;sticos encontrados</p>
			<table class="paginated modal_table" id="tblDiagnosticos">
				<thead>
					<tr>				   
						<th style="">C&oacute;digo ciex</th>
						<th style="">Nombre</th>
					</tr>
				</thead>

				<?php
				foreach ($resultados as $resultado) {
					$nombre = "";
					$codigo = "";

					$nombre = $resultado['nombre'];
					$codigo = $resultado['codciex'];
					?>
					<tr id="tbl_diag_<?= $medicamento['cod_medicamento'] ?>" onclick="detallesDiagnóstico('<?= $codigo ?>', '<?= $nombre ?>');">
						<td style="text-align: left;">
							<span><?= $codigo ?></span>							
						</td>
						<td style="text-align: left;">
							<span><?= $nombre ?></span>							
						</td>						
					</tr>
					<?php
				}
				?>

			</table>
			<script id='ajax'>
				//<![CDATA[ 
				$(function () {
					$('#tblDiagnosticos', 'table').each(function (i) {
						$(this).text(i + 1);
					});

					$('table.paginated').each(function () {
						var currentPage = 0;
						var numPerPage = 10;
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
		} else {
			?>
			<p>NO hay resultados para la b&uacute;squeda</p>
			<?php
		}
		break;

	case "15": /*  */
		$nombre = $utilidades->str_decode($_POST["nombre"]);
		$codigo = $utilidades->str_decode($_POST["codigo"]);
		?>
		<div>
			<table style="width: 100%;">
				<tr>
					<td style="text-align: right; width: 50%;">
						C&oacute;digo:
					</td>
					<td style="text-align: left;">
						<span style="color: #399000;"><?= $codigo ?></span>
						<input type="hidden" id="hddDetCodDiag" name="hddDetCodDiag" value="<?= $codigo ?>" />
					</td>
				</tr>
				<tr>
					<td style="text-align: right; width: 50%;">
						Nombre:
					</td>
					<td style="text-align: left;">
						<span style="color: #399000;"><?= $nombre ?></span>
						<input type="hidden" id="hddDetNomDiag" name="hddDetNomDiag" value="<?= $nombre ?>" />
					</td>
				</tr>

			</table>
		</div>
		<?php
		break;

	case "16":
		@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);

		$lista_planes = $dbPlanes->getListaPlanesActivos($id_convenio);
		$combo->getComboDb("cmb_cod_plan", "", $lista_planes, "id_plan,nombre_plan", "-- Seleccione --", "", 1, "width:100%;");
		break;

	case "17":
		$secuencia = $utilidades->str_decode($_POST["secuencia"]);
		$parametro = $utilidades->str_decode($_POST["parametro"]);
		$resultados_p = $dbmaestroProcedimientos->getProcedimientos($parametro, 1);
		$resultados_q = $dbpaquetesProcedimientos->buscarPaquetesActivos($parametro);
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
					<tr onclick="agregarProcedimientoOrdenMedica('<?= $codigo ?>', '<?= $nombre ?>', 2, <?= $secuencia ?>);">
						<td align="center"><?= $codigo ?></td>
						<td align="left"><?= $nombre ?></td>
					</tr>
					<?php
				}

				foreach ($resultados_q as $paquete_aux) {
					$codigo = $paquete_aux["id_paquete_p"];
					$nombre = $paquete_aux["nom_paquete_p"];
					?>
					<tr onclick="agregarProcedimientoOrdenMedica('<?= $codigo ?>', '<?= $nombre ?>', 1, <?= $secuencia ?>);">
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

	case "18": //Formulario de búsqueda de diagnósticos
		$secuencia = $utilidades->str_decode($_POST["secuencia"]);
		?>
		<input type="hidden" id="hdd_secuencia" name="hdd_secuencia" value="<?= $secuencia ?>" />
		<br>
		<table style="width: 100%;">
			<tbody>
				<tr>
					<td>
						<input type="text" id="txtParametro" name="txtParametro" placeholder="C&oacute;digo CIE-X o nombre del diagn&oacute;stico">
					</td>
					<td style="width: 10%;">
						<input type="submit" onclick="buscarDiagnosticoOrdenMedica(<?= $secuencia ?>)" value="Buscar" class="btnPrincipal peq">
					</td>
				</tr>
			</tbody>
		</table>
		<div id="resultadoTblDiagnostico"></div>
		<?php
		break;

	case "19": //Resultados de la búsqueda de diagnósticos
		$secuencia = $utilidades->str_decode($_POST["secuencia"]);
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
					<tr onclick="agregarDiagnosticoOrdenMedica('<?= $codigo ?>', '<?= $nombre ?>', <?= $secuencia ?>);">
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

	case "20": //Búsqueda de procedimientos o paquetes por código
		@$cod_servicio = $utilidades->str_decode($_POST["cod_servicio"]);
		@$secuencia = $utilidades->str_decode($_POST["secuencia"]);

		//Se busca el procedimiento por código
		$procedimiento_obj = $dbmaestroProcedimientos->getProcedimiento($cod_servicio, 1);

		$tipo_hallado = 0;
		$nombre_servicio = "Producto no hallado";
		if (isset($procedimiento_obj["cod_procedimiento"])) {
			$tipo_hallado = 2;
			$nombre_servicio = $procedimiento_obj["nombre_procedimiento"];
		} else {
			$paquete_obj = $dbpaquetesProcedimientos->getPaqueteById($cod_servicio);
			if (isset($paquete_obj["id_paquete_p"])) {
				$tipo_hallado = 1;
				$nombre_servicio = $paquete_obj["nom_paquete_p"];
			}
		}
		?>
		<input type="hidden" id="hdd_tipo_servicio_b_aut" name="hdd_tipo_servicio_b_aut" value="<?= $tipo_hallado ?>" />
		<input type="hidden" id="hdd_nombre_servicio_b_aut" name="hdd_nombre_servicio_b_aut" value="<?= $nombre_servicio ?>" />
		<input type="hidden" id="hdd_secuencia_b_aut" name="hdd_secuencia_b_aut" value="<?= $secuencia ?>" />		
		<?php
		break;
	case "21": //Búsqueda de diagnósticos por código
		@$cod_ciex = $utilidades->str_decode($_POST["cod_ciex"]);
		@$secuencia = $utilidades->str_decode($_POST["secuencia"]);

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
		<input type="hidden" id="hdd_secuencia_b_aut" name="hdd_secuencia_b_aut" value="<?= $secuencia ?>" />	
		<?php
		break;
}