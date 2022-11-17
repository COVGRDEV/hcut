<?php
session_start();
/*
  Pagina para ver y matricular pacientes para atencion post QX de Catarata
  Autor: Helio Ruber López - 27/04/2016
 */

header('Content-Type: text/xml; charset=UTF-8');

require_once("../db/DbHistoriaClinica.php");
require_once("../db/DbPacientes.php");
require_once("../db/DbVariables.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../funciones/FuncionesPersona.php");
require_once("../funciones/Utilidades.php");
require_once("../principal/ContenidoHtml.php");
require_once("../historia_clinica/FuncionesHistoriaClinica.php");
require_once("../db/DbListas.php");
require_once("../db/DbCalendario.php");
require_once("../db/DbAtencionPostqx.php");
require_once("../funciones/Class_Generar_Clave.php");
include('../funciones/phpqrcode/qrlib.php'); 


$dbHistoriaClinica = new DbHistoriaClinica();
$dbPacientes = new DbPacientes();
$dbListas = new DbListas();
$dbCalendario = new DbCalendario();
$dbPostQx = new DbAtencionPostqx();

$combo = new Combo_Box();
$funciones_persona = new FuncionesPersona();
$utilidades = new Utilidades();
$contenido = new ContenidoHtml();
$contenido->validar_seguridad(1);
$funciones_hc = new FuncionesHistoriaClinica();
$dbVariables = new Dbvariables();

$opcion = $utilidades->str_decode($_REQUEST["opcion"]);

function ver_historia_clinica($id_paciente, $nombre_persona, $documento_persona, $tipo_documento, $telefonos, $fecha_nacimiento, $edad_paciente, $clave_verificacion) {
	$dbHistoriaClinica = new DbHistoriaClinica();
	$dbVariables = new Dbvariables();
	$dbPostQx = new DbAtencionPostqx();
	
    $funciones_persona = new FuncionesPersona();
	$utilidades = new Utilidades();
	$contenido = new ContenidoHtml();
	$tipo_acceso_menu = $contenido->obtener_permisos_menu($utilidades->str_decode($_POST["hdd_numero_menu"]));
	
	//Datos de pacientes vinculados a seguimiento post qx
	$tabla_seguimiento = $dbPostQx->getPacienteVinculado($id_paciente);
	
	
	//Datos de pacientes vinculados a seguimiento post qx
	$tabla_respuestas = $dbPostQx->getRespuestaPaciente($id_paciente);
	
	$id_usuario = $_SESSION["idUsuario"];
    @$credencial = $utilidades->str_decode($_POST["credencial"]);
    @$id_menu = $utilidades->str_decode($_POST["hdd_numero_menu"]);

    
    ?>
    <fieldset style="width: 65%; margin: auto;">
        <legend>Datos del paciente:</legend>
        <table style="width: 500px; margin: auto; font-size: 10pt;">
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
                <td align="left"><b><?php echo($edad_paciente); ?> a&ntilde;os</b></td>
            </tr>
            <tr>
                <td align="right">Tel&eacute;fonos:</td>
                <td align="left"><b><?php echo($telefonos); ?></b></td>
            </tr>
        
			<tr>
            	<td align="center" colspan="2">
            		<br />  
               		<input type="button" id="btn_vincular_paciente" value="Habilitar Paciente" class="btnPrincipal"  onclick="habilitar_paciente('<?php echo($id_paciente); ?>', '<?php echo($clave_verificacion); ?>', '<?php echo($nombre_persona); ?>', '<?php echo($documento_persona); ?>');" />
               		<!-- onclick="vincular_paciente(<?php echo($id_paciente); ?>, '<?php echo($nombre_persona); ?>', '<?php echo($id_seguimiento); ?>');" -->
                </td>
            </tr>
		
        </table>
    </fieldset>
    <div style="width:70%; margin:auto; height:320px; overflow:auto;">
    </div>
    <?php
}


switch ($opcion) {
    case "1": //Consultar HC del paciente	
        $txt_paciente_hc = $utilidades->str_decode($_POST["txt_paciente_hc"]);
        $id_usuario_crea = $_SESSION["idUsuario"];
        $tabla_personas = $dbHistoriaClinica->getPacientesHistoriaClinica($txt_paciente_hc);
        $cantidad_datos = count($tabla_personas);

        if ($cantidad_datos == 1) {//Si se encontro un solo registro
            $id_paciente = $tabla_personas[0]['id_paciente'];
            $nombre_1 = $tabla_personas[0]['nombre_1'];
            $nombre_2 = $tabla_personas[0]['nombre_2'];
            $apellido_1 = $tabla_personas[0]['apellido_1'];
            $apellido_2 = $tabla_personas[0]['apellido_2'];
            $numero_documento = $tabla_personas[0]['numero_documento'];
            $tipo_documento = $tabla_personas[0]['tipo_documento'];
            $fecha_nacimiento = $tabla_personas[0]['fecha_nac_persona'];
            $telefonos = $tabla_personas[0]['telefono_1'];
			if ($tabla_personas[0]['telefono_2'] != "") {
	            $telefonos .= " - ".$tabla_personas[0]['telefono_2'];
			}
			$clave_verificacion = $tabla_personas[0]['clave_verificacion'];
			
            $nombres_apellidos = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
            //Edad del paciente
            $datos_paciente = $dbPacientes->getEdadPaciente($id_paciente, '');
            $edad_paciente = $datos_paciente['edad'];
			
            ver_historia_clinica($tabla_personas[0]['id_paciente'], $nombres_apellidos, $numero_documento, $tipo_documento, $telefonos, $fecha_nacimiento, $edad_paciente, $clave_verificacion);
        } else if ($cantidad_datos > 1) {
            ?>
            <table id='tabla_persona_hc'  border='0' class="paginated modal_table" style="width: 70%; margin: auto;">
                <thead>
                    <tr class='headegrid'>
                        <th class="headegrid" align="center">Documento</th>	
                        <th class="headegrid" align="center">Pacientes</th>
                    </tr>
                </thead>
                <?php
                foreach ($tabla_personas as $fila_personas) {
                    $id_personas = $fila_personas['id_paciente'];
                    $nombre_1 = $fila_personas['nombre_1'];
                    $nombre_2 = $fila_personas['nombre_2'];
                    $apellido_1 = $fila_personas['apellido_1'];
                    $apellido_2 = $fila_personas['apellido_2'];
                    $numero_documento = $fila_personas['numero_documento'];
                    $tipo_documento = $fila_personas['tipo_documento'];
                    $nombres_apellidos = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
                    $fecha_nacimiento = $fila_personas['fecha_nac_persona'];
                    $telefonos = $fila_personas['telefono_1'] . " - " . $fila_personas['telefono_2'];
					$clave_verificacion = $fila_personas['clave_verificacion'];
					
                    $nombres_apellidos = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
                    //Edad del paciente
                    $datos_paciente = $dbPacientes->getEdadPaciente($id_personas, '');
                    $edad_paciente = $datos_paciente['edad'];
                    ?>
                    <tr class='celdagrid' onclick="ver_registros_hc(<?php echo($id_personas); ?>, '<?php echo($nombres_apellidos); ?>', '<?php echo($numero_documento); ?>', '<?php echo($tipo_documento); ?>', '<?php echo($telefonos); ?>', '<?php echo($fecha_nacimiento); ?>', '<?php echo($edad_paciente); ?>', '<?php echo($clave_verificacion); ?>');">
                        <td align="left"><?php echo $numero_documento; ?></td>	
                        <td align="left"><?php echo $nombres_apellidos; ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>

            <script id='ajax'>
                //<![CDATA[ 
                $(function() {
                    $('.paginated', 'tabla_persona_hc').each(function(i) {
                        $(this).text(i + 1);
                    });

                    $('table.paginated').each(function() {
                        var currentPage = 0;
                        var numPerPage = 5;
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
        } else if ($cantidad_datos == 0) {
            echo"<div class='msj-vacio'>
					<p>No se encontraron pacientes</p>
			     </div>";
        }
        break;
		
    case "2": //Mostrar los registros de historia clínica de un paciente
        $id_persona = $utilidades->str_decode($_POST['id_persona']);
        $nombre_persona = $utilidades->str_decode($_POST['nombre_persona']);
        $documento_persona = $utilidades->str_decode($_POST['documento_persona']);
        $tipo_documento = $utilidades->str_decode($_POST['tipo_documento']);
        $telefonos = $utilidades->str_decode($_POST['telefonos']);
        $fecha_nacimiento = $utilidades->str_decode($_POST['fecha_nacimiento']);
        $edad_paciente = $utilidades->str_decode($_POST['edad_paciente']);
		$clave_verificacion = $utilidades->str_decode($_POST['clave_verificacion']);
		
        ver_historia_clinica($id_persona, $nombre_persona, $documento_persona, $tipo_documento, $telefonos, $fecha_nacimiento, $edad_paciente, $clave_verificacion);
     	break;
		
	case "3":
		
		$id_usuario = $_SESSION["idUsuario"];
			
		@$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
		@$clave_verificacion = $utilidades->str_decode($_POST["clave_verificacion"]);
		@$nombre_persona = $utilidades->str_decode($_POST["nombre_persona"]);
		@$documento_persona = $utilidades->str_decode($_POST["documento_persona"]);
		
		
		$tabla_variables = $dbVariables->getVariable(16);
		
		$url_pagina = $tabla_variables['valor_variable'];
		
		
		//Se obtiene la fecha actual
		$fechas_obj = $dbVariables->getFechaActualMostrar();
		$fecha_mostrar = $fechas_obj["fecha_actual_mostrar"];
		
		
		
		if ($clave_verificacion == '') {
			
			/*Para generar clave del paciente*/
			$clave_paciente = new Class_Generar_Clave();
			$InitalizationKey = $clave_paciente->generate_secret_key(16);
			$TimeStamp = $clave_paciente->get_timestamp();
			$secretkey = $clave_paciente->base32_decode($InitalizationKey);
			$clave_verificacion = $clave_paciente->oath_hotp($secretkey, $TimeStamp);
			 
			$ind_clave = $dbPacientes->guardar_clave_verificacion($id_paciente, $clave_verificacion);
			
			if($ind_clave == 1){
				$ind_clave = $clave_verificacion;
			}
				
	    } else { //Solo imprime
			$ind_clave = $clave_verificacion;
		}
		
		
		//Aquí se genera el código QR
		
		//QRcode::png('PHP QR Code :)');
		$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'../tmp/'.DIRECTORY_SEPARATOR;
		$PNG_WEB_DIR = '../tmp/';			
		//ofcourse we need rights to create temp dir
		if (!file_exists($PNG_TEMP_DIR))
			mkdir($PNG_TEMP_DIR);
		$filename = $PNG_TEMP_DIR.'test.png';

		$matrixPointSize = 6;
		$errorCorrectionLevel = 'L';

		$filename = $PNG_TEMP_DIR.'test'.md5($errorCorrectionLevel.'|'.$matrixPointSize).'.png';
		
		//$url_pagina
		$texto_qr = $url_pagina;
		
									
		QRcode::png($texto_qr, $filename, $errorCorrectionLevel, $matrixPointSize, 2); 
		
		
		//$PNG_WEB_DIR.basename($filename) 
		
		
		
		
		require_once("../funciones/pdf/fpdf.php");
		require_once("../funciones/pdf/makefont/makefont.php");
		require_once("../funciones/pdf/funciones.php");
		require_once("../funciones/pdf/WriteHTML.php");
		
		
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
		
		//Código QR
		$pdf->Image($PNG_WEB_DIR.basename($filename), 140, 60, 45);
		
		$pdf->SetX(10);
		$pdf->SetY(30);
		$pdf->Cell(150, 4, ajustarCaracteres("Paciente: ".$utilidades->convertir_a_mayusculas($nombre_persona)), 0, 0, "L");
		$pdf->Cell(45, 4, ajustarCaracteres("Fecha: ".$fecha_mostrar), 0, 1, "L");
		$pdf->Ln(2);
		$pdf->Cell(195, 4, ajustarCaracteres("Documento: ".$documento_persona), 0, 1, "L");
		$pdf->Ln(2);
		
		//Cuerpo de la fórmula
		$x_aux = $pdf->GetX();
		$y_aux = $pdf->GetY();
		
		$pdf->SetX(10);
		$pdf->SetY(50);
		$pdf->SetFont('Arial','',12);   
		$pdf->Cell(180, 4, ajustarCaracteres("Para descargar los resultados de examenes, debe dirigirse al siguiente sitio web"), 0, 0, "L");
		$pdf->Ln(6);
		$pdf->SetFont('Arial','B',14);
		$pdf->Cell(180, 4, ajustarCaracteres($url_pagina), 0, 0, "L");
		$pdf->Ln(6);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(180, 4, ajustarCaracteres("e ingresar con los siguientes datos."), 0, 0, "L");
		$pdf->Ln(8);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(180, 4, ajustarCaracteres("Usuario: ".$documento_persona), 0, 0, "L");
		$pdf->Ln(6);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(180, 4, ajustarCaracteres("Contraseña: ".$ind_clave), 0, 0, "L");
		
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
		$nombreArchivo = "../tmp/formula_examen_resultados_" . $_SESSION["idUsuario"] . ".pdf";
		$pdf->Output($nombreArchivo, "F");
		
		?>
	    <input type="hidden" name="hdd_ruta_arch_pdf" id="hdd_ruta_arch_pdf" value="<?php echo($nombreArchivo); ?>" />
	    <input type="hidden" value="<?php echo($ind_clave); ?>" name="hdd_exito" id="hdd_exito" />
        <?php
	
	break;
		
	case "4": //Vincular Paciente
		
	
	break;
		
	
	
	
	case "5": //
		
	break;
}
?>
