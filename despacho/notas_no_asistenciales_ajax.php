<?php
header('Content-Type: text/xml; charset=UTF-8');
session_start();

require_once("../db/DbNotasNoAsistenciales.php");
require_once("../db/DbAdmision.php");
require_once("../db/DbPacientes.php");
require_once("../db/DbPermisos.php");
require_once("../funciones/Utilidades.php");
require_once("../funciones/FuncionesPersona.php");
require_once("../principal/ContenidoHtml.php");

$dbNotasNoAsistenciales = new DbNotasNoAsistenciales();
$dbAdmision = new DbAdmision();
$dbpacientes = new DbPacientes();
$dbPermisos = new DbPermisos();
$utilidades = new Utilidades();
$funcionesPersona = new FuncionesPersona();
$contenido = new ContenidoHtml();
$contenido->validar_seguridad(1);

$id_usuario = $_SESSION["idUsuario"];

//Identificador del menú de notas
$id_menu = $_POST["hdd_numero_menu"];
$tipo_acceso_menu = $contenido->obtener_permisos_menu($id_menu);

$opcion = $_POST["opcion"];

switch ($opcion) {
	case "1": //Resultado de búsqueda de notas
		$parametro = $utilidades->str_decode($_POST["parametro"]);
		$fecha_admision = $utilidades->str_decode($_POST["fecha_admision"]);
		
		$lista_notas = $dbNotasNoAsistenciales->getListaNotasNoAsistencialesB($parametro, $fecha_admision);
	?>
    <table class="paginated modal_table" style="width:90%; margin:auto;">
    	<thead>
        	<tr>
                <th align="center" style="width:15%;">N&uacute;mero documento</th>
                <th align="center" style="width:35%;">Nombre</th>
                <th align="center" style="width:15%;">Fecha de atenci&oacute;n</th>
                <th align="center" style="width:20%;">Tipo de atenci&oacute;n</th>
                <th align="center" style="width:15%;">Estado</th>
            </tr>
        </thead>
        <?php
			if (count($lista_notas) >= 1) {
				foreach ($lista_notas as $nota_aux) {
					$estado = "Sin notas";
					$class_estado = "inactivo";
					if ($nota_aux["id_nota"] != "") {
						$estado = "Con notas";
						$class_estado = "activo";
					}
		?>
        <tr onclick="abrir_editar_nota('<?php echo($nota_aux["id_nota"]); ?>', <?php echo($nota_aux["id_admision"]); ?>)">
            <td align="center"><?php echo $nota_aux["codigo_tipo_documento"]." ".$nota_aux["numero_documento"]; ?></td>
            <td align="left"><?php echo($funcionesPersona->obtenerNombreCompleto($nota_aux["nombre_1"], $nota_aux["nombre_2"], $nota_aux["apellido_1"], $nota_aux["apellido_2"])); ?></td>
            <td align="center"><?php echo($nota_aux["fecha_admision_t"]); ?></td>
            <td align="center"><?php echo($nota_aux["nombre_tipo_cita"]); ?></td>
			<td align="center"><span class="<?php echo($class_estado); ?>"><?php echo($estado); ?></span></td>
        </tr>
        <?php
				}
			} else {
		?>
        <td colspan="5">No se hallaron atenciones</td>
        <?php
			}
		?>
    </table>
    <script id='ajax'>
		//<![CDATA[ 
		$(function() {
			$('.paginated', 'table').each(function(i) {
				$(this).text(i + 1);
			});
			
			$('table.paginated').each(function() {
				var currentPage = 0;
				var numPerPage = 10;
				var $table = $(this);
				$table.bind('repaginate', function() {
					$table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
				});
				$table.trigger('repaginate');
				var numRows = $table.find('tbody tr').length;
				var numPages = Math.ceil(numRows / numPerPage);
				var $pager = $('<div class="pager"></div>');
				for (var page = 0; page < numPages; page++) {
					$('<span class="page-number"></span>').text(page + 1).bind('click', {
						newPage: page
					}, function(event) {
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
		break;
		
	case "2": //Formulario de edición de notas
        $id_nota = $utilidades->str_decode($_POST["id_nota"]);
        $id_admision = $utilidades->str_decode($_POST["id_admision"]);
		
		//Se cargan los datos de la admisión y el paciente
		$admision_obj = $dbAdmision->get_admision($id_admision);
		$id_paciente = $admision_obj["id_paciente"];
		$paciente_obj = $dbpacientes->getExistepaciente3($id_paciente);
		
		//Se cargan los datos de la nota
		$nota_obj = array();
		$lista_notas_det = array();
		if ($id_nota != "") {
			$nota_obj = $dbNotasNoAsistenciales->getNotaNoAsistencial($id_nota);
			$lista_notas_det = $dbNotasNoAsistenciales->getListaNotasNoAsistencialesDet($id_nota);
		}
		
		$nombre_completo = $funcionesPersona->obtenerNombreCompleto($paciente_obj["nombre_1"], $paciente_obj["nombre_2"], $paciente_obj["apellido_1"], $paciente_obj["apellido_2"]);
	?>
    <div class="encabezado">
        <h3>Nota administrativa</h3>
    </div>
    <br/>
    <div class="contenedor_exito" id="d_contenedor_exito_2"></div>
    <div class="contenedor_error" id="d_contenedor_error_2"></div>
    <input type="hidden" name="hdd_id_nota" id="hdd_id_nota" value="<?php echo($id_nota); ?>" />
    <input type="hidden" name="hdd_id_admision" id="hdd_id_admision" value="<?php echo($id_admision); ?>" />
    <input type="hidden" name="hdd_id_paciente" id="hdd_id_paciente" value="<?php echo($id_paciente); ?>" />
    <table border="0" style="width:100%;">
        <tr valign="top">
            <td align="right" style="width:10%;">
                <label>Paciente:&nbsp;</label>
            </td>
            <td align="left" style="width:55%;">
                <label><b><?php echo($nombre_completo); ?></b></label>
            </td>
            <td align="right" style="width:15%;">
                <label>Fecha admisi&oacute;n:&nbsp;</label>
            </td>
            <td align="left" style="width:20%;">
            	<label><b><?php echo($admision_obj["fecha_admision_t"]); ?></b></label>
            </td>
        </tr>
        <tr valign="top">
        	<td align="right" colspan="3">
                <label>Tipo de cita:&nbsp;</label>
            </td>
            <td align="left" style="width:20%;">
            	<label><b><?php echo($admision_obj["nombre_tipo_cita"]); ?></b></label>
            </td>
        </tr>
        <tr>
        	<td align="center" colspan="4">
				<table border="0" cellpadding="3" cellspacing="0" align="center" style="width:100%;">
                    <tr>
                    	<td align="right" valign="middle" style="width:70px;">
                            <?php
								//Se verifica el número de fórmulas existentes
								$cant_notas_aux = count($lista_notas_det);
								if ($cant_notas_aux == 0) {
									$cant_notas_aux = 1;
								}
							?>
                            <input type="hidden" id="hdd_cant_notas" name="hdd_cant_notas" value="<?php echo($cant_notas_aux); ?>" />
                            <?php
								for ($i = 0; $i < 20; $i++) {
							?>
                            <input type="hidden" id="hdd_act_nota_<?php echo($i); ?>" value="<?php echo($i < $cant_notas_aux ? 1 : 0); ?>" />
                            <?php
								}
							?>
                            <label class="inline">Ver nota:</label>
                        </td>
                        <td align="left" id="td_ver_notas" style="width:60px;">
                            <select name="cmb_num_nota" id="cmb_num_nota" style="width:50px;" onchange="mostrar_nota(this.value);">
                            	<?php
									for ($i = 0; $i < $cant_notas_aux; $i++) {
								?>
                                <option value="<?php echo($i); ?>"><?php echo($i + 1); ?></option>
                                <?php
									}
								?>
                            </select>
                        </td>
                        <td align="left" valign="top">
                        	<div class="agregar_alemetos" onclick="agregar_nota();" title="Agregar nota"></div> 
                            <div class="restar_alemetos" onclick="restar_nota();" title="Borrar nota"></div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <?php
		for ($i = 0; $i < 20; $i++) {
			$texto_nota_aux = "";
			$ind_existe_det = false;
			if (isset($lista_notas_det[$i])) {
				$texto_nota_aux = $lista_notas_det[$i]["texto_nota"];
				$fecha_nota_aux = $lista_notas_det[$i]["fecha_nota_t"];
				$ind_existe_det = true;
			} else if ($i == 0) {
				$texto_nota_aux = "";
				$fecha_nota_aux = $admision_obj["fecha_admision_t"];
			}
	?>
    <div id="d_detalle_nota_<?php echo($i);?>" class="div_formula">
        <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%;">
            <tr>
            	<td align="right" style="width:60px;">
                    <label class="inline">Fecha:</label>
                </td>
                <td align="left">
                    <input type="text" id="txt_fecha_nota_<?php echo($i); ?>" name="txt_fecha_nota_<?php echo($i); ?>" class="input" maxlength="10" style="width:120px;" value="<?php echo($fecha_nota_aux); ?>" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" />
                    <script id="ajax">
						$(function() {
							$("#txt_fecha_nota_<?php echo($i); ?>").fdatepicker({
								format: "dd/mm/yyyy"
							});
						});
					</script>
                </td>
            </tr>
            <tr>
                <td align="left" colspan="2">
                    <?php
						if (!$ind_existe_det) {
					?>
                    <div id="text_nota_<?php echo($i);?>"></div>
                    <?php
						} else {
							$texto_nota_aux = $utilidades->ajustar_texto_wysiwyg($texto_nota_aux);
					?>
                    <div id="text_nota_<?php echo($i);?>"><?php echo($texto_nota_aux); ?></div>
                    <?php
						}
					?>
                </td>
            </tr>
        </table>
    </div>
    <script id="ajax">
		<?php
			if ($i > 0) {
		?>
		setTimeout("ocultar_nota(<?php echo($i); ?>);", 500);
		<?php
			}
		?>
	</script>
    <?php
		}
	?>
    <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%;">
        <?php
			if ($tipo_acceso_menu == "2") {
		?>
        <tr>
            <td align="center" colspan="4" style="font-size: 12px;">
                <input type="button" id="btn_imprimir" nombre="btn_imprimir" class="btnPrincipal" value="Imprimir" onclick="guardar_nota(2);"/>
                &nbsp;&nbsp;
                <input type="button" id="btn_guardar" nombre="btn_guardar" class="btnPrincipal" value="Guardar" onclick="guardar_nota(1);"/>
            </td>
        </tr>
        <?php
			} else {
		?>
        <tr>
            <td align="center" colspan="4" style="font-size: 12px;">
                <input type="button" id="btn_imprimir" nombre="btn_imprimir" class="btnPrincipal" value="Imprimir" onclick="guardar_nota(3);"/>	
            </td>
        </tr>
        <?php
			}
		?>
    </table>
    <div id="d_resultado_guardar" style="display:none;"></div>
    <script id="ajax">
		for (var i = 0; i < 20; i++) {
			initCKEditorNotas("text_nota_" + i);
		}
	</script>
    <?php
        break;
		
    case "3": //Guarda notas
		@$id_admision = $utilidades->str_decode($_POST["id_admision"]);
		@$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
		@$id_nota = $utilidades->str_decode($_POST["id_nota"]);
		@$id_tipo = $utilidades->str_decode($_POST["id_tipo"]);
		@$num_nota_det = $utilidades->str_decode($_POST["num_nota"]);
		@$cant_notas = intval($_POST["cant_notas"], 10);
		
		$arr_notas_det = array();
		for ($i = 0; $i < $cant_notas; $i++) {
			@$arr_notas_det[$i]["texto_nota"] = $utilidades->str_decode($_POST["texto_nota_".$i]);
			@$arr_notas_det[$i]["fecha_nota"] = $utilidades->str_decode($_POST["fecha_nota_".$i]);
		}
		
		if ($id_tipo == "1" || $id_tipo == "2") {
	        $resultado = $dbNotasNoAsistenciales->crearEditarNotaNoAsistencial($id_nota, $id_admision, $id_paciente, $arr_notas_det, $id_usuario);
		} else {
			$resultado = "1";
		}
	?>
    <input type="hidden" id="hdd_resultado_nota" value="<?php echo($resultado); ?>" />
    <?php
		
		if ($resultado > 0) {
			$id_nota = $resultado;
			//Si se seleccionó un tipo de impresión
			if ($id_tipo == "2" || $id_tipo == "3") {
				require_once("../funciones/pdf/fpdf.php");
				require_once("../funciones/pdf/makefont/makefont.php");
				require_once("../funciones/pdf/funciones.php");
				require_once("../funciones/pdf/WriteHTML.php");
				
				//Se obtienen los datos de la admisión y del paciente
				$admision_obj = $dbAdmision->get_admision($id_admision);
				$paciente_obj = $dbpacientes->getExistepaciente3($id_paciente);
				$nombre_completo = $funcionesPersona->obtenerNombreCompleto($paciente_obj["nombre_1"], $paciente_obj["nombre_2"], $paciente_obj["apellido_1"], $paciente_obj["apellido_2"]);
				
				//Se obtiene el registro de detalle que se desea imprimir
				$nota_det_obj = $dbNotasNoAsistenciales->getNotaNoAsistencialDet($id_nota, $num_nota_det + 1);
				
				$pdf = new FPDF('P', 'mm', array(216, 279));
				$pdfHTML = new PDF_HTML();
				$pdf->SetMargins(10, 10, 10);
				$pdf->SetAutoPageBreak(false);
				$pdf->SetFillColor(255, 255, 255);
				
				$pdf->bordeMulticell = 0; //Si la tabla tiene borde. 1 = Con borde. 2 = Sin borde
				$pdf->pie_pagina = false;
				
				$pdf->AddPage();
				
				$pdf->SetFont("Arial", "", 9);
				srand(microtime() * 1000000);
				
				//Logo
				$pdf->Image("../imagenes/logo-color.png", 15, 11, 55);
				
				$pdf->SetX(10);
				$pdf->SetY(30);
				$pdf->Cell(150, 4, ajustarCaracteres("Paciente: ".$utilidades->convertir_a_mayusculas($nombre_completo)), 0, 0, "L");
				$pdf->Cell(40, 4, ajustarCaracteres("Fecha: ".$nota_det_obj["fecha_nota_t"]), 0, 1, "R");
				$pdf->Ln(2);
				$pdf->Cell(150, 4, ajustarCaracteres($paciente_obj["tipodocumento"].": ".$paciente_obj["numero_documento"]), 0, 0, "L");
				$pdf->Cell(40, 4, ajustarCaracteres("Tipo de cita: ".$admision_obj["nombre_tipo_cita"]), 0, 1, "R");
				$pdf->Ln(2);
				$pdf->SetFont("Arial", "B", 10);
				$pdf->Cell(195, 6, ajustarCaracteres("NOTA ADMINISTRATIVA"), 0, 1, "C");
				$pdf->SetFont("Arial", "", 9);
				
				//Cuerpo de la nota
				$x_aux = $pdf->GetX();
				$y_aux = $pdf->GetY();
				
				$texto_nota = ajustarCaracteres($nota_det_obj["texto_nota"]);
				$pdfHTML->WriteHTML($texto_nota, $pdf);
				
		        $pdf->SetY($y_aux + 77);
				
				$pdf->SetFont("Arial", "", 7);
				$pdf->Cell(195, 4, ajustarCaracteres("Centro M&eacute;dico Carlos Ardila L&uuml;lle, Torre A, Piso 3, M&oacute;dulo 7, Floridablanca, Colombia."), 0, 1, "C", true);
				$pdf->Ln(-1);
				$pdf->Cell(112, 4, ajustarCaracteres("PBX: 6392929 - 6392828 - 6392727  Fax: 6392626"), 0, 0, "R", true);
				$pdf->SetFont("Arial", "B", 7);
				$pdf->Cell(83, 4, ajustarCaracteres("www.virgiliogalvis.com"), 0, 1, "L", true);
				$pdf->SetFont("Arial", "", 7);
				$pdf->SetFont("Arial", "B", 7);
				$pdf->Cell(72, 4, ajustarCaracteres("FOSCAL Internacional"), 0, 0, "R", true);
				$pdf->SetFont("Arial", "", 7);
				$pdf->Cell(123, 4, ajustarCaracteres("Zona Franca Especial Calle 158 No. 20-95 - Torre C - Consultorio 301"), 0, 1, "L", true);
				
				//Se guarda el documento pdf
				$nombre_archivo = "../tmp/nota_no_asistencial_".$_SESSION["idUsuario"].".pdf";
				$pdf->Output($nombre_archivo, "F");
		?>
	    <input type="hidden" name="hdd_ruta_arch_pdf" id="hdd_ruta_arch_pdf" value="<?php echo($nombre_archivo); ?>" />
        <?php
			}
		}
        break;
		
	case "6": //Funcion que procesa el tipo de impresion
        $tipoImpresion = $_POST['tipoImpresion'];
        $idFormula = $_POST['idFormula'];
		$text_despacho = $_POST['text_despacho'];
		
		if ($tipoImpresion == '1') { //Consultorio
            ?>
	        <div id='campo_imprimir' style="display:none;">
	        	<center>
    			<table style='width:83%;margin-top:12%;' border='0'>
				<tr>
					<td>&nbsp;&nbsp;</td>
					<td><?php //echo ($hdd_nombre_paciente);?></td>
					<td align="right"><?php //echo ($hdd_fecha_admision);?></td>
				</tr>
				</table>
				</center>
				<table style='width: 100%;' border='0'>
				<tr>
					<td colspan='4'>Documento: <?php echo (/*$hdd_documento_paciente.*/'<br />'.$text_despacho);?></td>
				</tr>
				
    			</table>
    		</div>
    		<?php
        }else if($tipoImpresion == '2'){//Ecopetrol
            ?>
			<div id='campo_imprimir' style="display:none;">
	        	<center>
    			<table style='width:100%; margin-top: -5px;' border='0'>
				<tr>
					<td>&nbsp;&nbsp;</td>
					<td><?php //echo ($hdd_nombre_paciente);?></td>
					<td>&nbsp;&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;</td>
					<td><?php //echo ($hdd_nombre_profesional);?></td>
					<td align="right" style="margin-right: -5px;"><?php //echo ($hdd_fecha_admision);?></td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;</td>
					<td><?php //echo ($text_remitido);?></td>
					<td>&nbsp;&nbsp;</td>
				</tr>
				</table>
				</center>
				<table style='width: 98%;' border='0'>
				<tr>
					<td colspan='4'>&nbsp;&nbsp;</td>
				</tr>
				<tr>
					<td colspan='4'>&nbsp;&nbsp;</td>
				</tr>	
				<tr>
					<td colspan='4'>Documento: <?php echo (/*$hdd_documento_paciente.*/'<br />'.$text_despacho);?></td>
				</tr>
    			</table>
    		</div>
	        <?php
        }
        break;
}
?>
