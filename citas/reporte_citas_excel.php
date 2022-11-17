<?php
session_start();

/** Include PHPExcel */
require_once '../funciones/PHPExcel/Classes/PHPExcel.php';
require_once("../db/DbCitas.php");
require_once '../db/DbListas.php';
require_once("../funciones/FuncionesPersona.php");
require_once '../db/DbVariables.php';

$citas = new DbCitas();
$listas = new DbListas();
$funciones_persona = new FuncionesPersona();
$dbVariables = new DbVariables();

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

$profesional = $_POST['hddProfesional'];
$lugar = $_POST['hddLugar'];
//$estado = $_POST['hddEstado'];
$fechaInicial = $_POST['hddFechaInicial'];
$fechaFinal = $_POST['hddFechaFinal'];
$cita = $_POST['hddCita'];
$tipoCita = $_POST['hddTipoCita'];
$hora = $_POST['hddHora'];
$convenio = $_POST['convenio'];

//Enviar el array a la funcion buscar
$contadorTp = $_POST['contadorTp'];
$tiposCitas = array();
for ($a = 1; $a <= $contadorTp; $a++) {
    $tiposCitas[$a] = $_POST['tp' . $a];
}



//echo $fechaInicial."#".$fechaFinal."#".$tipo_formato;
$arr_diferencia = $dbVariables->getDiferenciaFechas($fechaInicial, $fechaFinal, 2);
$diferencia_dias = intval($arr_diferencia["dias"], 10);

if($diferencia_dias >= 34){
	//Mostrar mensaje de error
	?>
		<script id="ajax" type="text/javascript">
			alert("Existe más de un mes entre las fechas seleccionadas");	
			window.close();
		</script>
	<?php
}else{
	
	function operacion_horas($hora, $minutos, $tipo, $cantidad, $formato) {
		if ($tipo == 1) { //Sumar minutos
			$horaInicial = $hora . ":" . $minutos;
			$segundos_horaInicial = strtotime($horaInicial);
			$segundos_minutoAnadir = $cantidad * 60;
			$nuevaHora = date("H:i", $segundos_horaInicial + $segundos_minutoAnadir);
		} else if ($tipo == 2) { //Restar minutos
			$horaInicial = $hora . ":" . $minutos;
			$segundos_horaInicial = strtotime($horaInicial);
			$segundos_minutoAnadir = $cantidad * 60;
			$nuevaHora = date("H:i", $segundos_horaInicial - $segundos_minutoAnadir);
		}
	
		if ($formato == 12) {
			$hora_nueva = explode(":", $nuevaHora);
			$hora_resultado = mostrar_hora_format($hora_nueva[0], $hora_nueva[1]);
		} else {
			$hora_resultado = $nuevaHora;
		}
	
		return $hora_resultado;
	}
	
	//Devulve la hora en formato 12 horas con la jornada
	function mostrar_hora_format($hora, $minutos) {
		$hora = cifras_numero($hora, 2);
		$minutos = cifras_numero($minutos, 2);
	
		$hora_res = '';
		if ($hora > 12) {
			$hora = $hora - 12;
			$hora_res = $hora . ":" . $minutos . " PM";
		} else {
			$hora_res = $hora . ":" . $minutos . " AM";
		}
	
		return $hora_res;
	}
	
	function cifras_numero($consecutivo, $cifras) {
		$longitud = strlen($consecutivo);
		while ($longitud <= $cifras - 1) {
			$consecutivo = "0" . $consecutivo;
			$longitud = strlen($consecutivo);
		}
		return $consecutivo;
	}
	

		$rta_aux = $citas->getReporteCitas($profesional, $lugar, $fechaInicial, $fechaFinal, $cita, 1, $tipoCita, $hora, $tiposCitas, $convenio);
		
		// Set document properties
	$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
			->setLastModifiedBy("Maarten Balliauw")
			->setTitle("Office 2007 XLSX Test Document")
			->setSubject("Office 2007 XLSX Test Document")
			->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
			->setKeywords("office 2007 openxml php")
			->setCategory("Test result file");
	
	// Add some data
	$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', 'No.')
			->setCellValue('B1', 'Fecha Asignación')
			->setCellValue('C1', 'Fecha Cita')
			->setCellValue('D1', 'Hora')
			->setCellValue('E1', 'Documento')
			->setCellValue('F1', 'Paciente')
			->setCellValue('G1', 'Teléfono')
			->setCellValue('H1', 'Lugar cita')
			->setCellValue('I1', 'Convenio')
			->setCellValue('J1', 'Profesional')
			->setCellValue('K1', 'Observación')
			->setCellValue('L1', 'Tipo de cita')
			->setCellValue('M1', 'Estado')
			->setCellValue('N1', 'Usuario crea')
			->setCellValue('O1', 'Usuario última modificación')
			->setCellValue('P1', 'Fecha')
			->setCellValue('Q1', 'Hora');
	
	$contador = 2;
	$contador2 = 1;
	
	foreach ($rta_aux as $value) {
		$anio_cita_aux = substr($value['fecha_cita'], 0, 4);
		$mes_cita_aux = substr($value['fecha_cita'], 5, 2);
		$dia_cita_aux = substr($value['fecha_cita'], 8, 2);
		
		$anio_cita_aux2 = substr($value['fecha_crea'], 0, 4);
		$mes_cita_aux2 = substr($value['fecha_crea'], 5, 2);
		$dia_cita_aux2 = substr($value['fecha_crea'], 8, 2);
		
		$hora = substr($value['hora_aux'], 0, 2);
		$minutos = substr($value['hora_aux'], 3, 4);
		$hora_cita = operacion_horas(intval($hora), intval($minutos), 1, 0, 12);
	
		$paciente_aux = '';
		
		$paciente_aux = $value['Pnombre_1'] . ' ' . $value['Pnombre_2'] . ' ' . $value['Papellido_1']. ' '. $value['Papellido_2'];
		$profesional_aux = $value['nombre_usuario'] . ' ' . $value['apellido_usuario'];
	
		$convenio_aux = '';
		if (strlen($value['nombre_convenio_aux']) >= 17) {
			$convenio_aux = substr($value['nombre_convenio_aux'], 0, 17) . '...';
		} else {
			$convenio_aux = $value['nombre_convenio_aux'];
		}
		$nombre_tipo_cita = $value['nombre_tipo_cita'];
		$estado_cita = $value['nombre_detalle'];
		$lugar_cita = $listas->getDetalle($value['id_lugar_cita']);
		$telefono =  $value['telefono_1'];
		$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $contador, $contador2)
				->setCellValue('B' . $contador, $dia_cita_aux2 . '/' . $mes_cita_aux2 . '/' . $anio_cita_aux2)
				->setCellValue('C' . $contador, $dia_cita_aux . '/' . $mes_cita_aux . '/' . $anio_cita_aux)
				->setCellValue('D' . $contador, $hora_cita)
				->setCellValue('E' . $contador, $value['numero_documento'])
				->setCellValue('F' . $contador, $paciente_aux)
				->setCellValue('G' . $contador, $telefono)
				->setCellValue('H' . $contador, $lugar_cita['nombre_detalle'])
				->setCellValue('I' . $contador, $convenio_aux)
				->setCellValue('J' . $contador, mb_strtoupper($profesional_aux , 'utf-8'))
				->setCellValue('K' . $contador, strip_tags(html_entity_decode($value['observacion_cita'])))
				->setCellValue('L' . $contador, ($nombre_tipo_cita))
				->setCellValue('M' . $contador, $estado_cita)
				->setCellValue('N' . $contador,$value['nombre_usuario_crea'] )
				->setCellValue('O' . $contador, $value['nombre_usuario_modifica'])
				->setCellValue('P' . $contador, $value['fecha_mod_t'])
				->setCellValue('Q' . $contador, $value['hora_mod_t']);
	
		$contador++;
		$contador2++;
	}
	
	$objSheet = $objPHPExcel->getActiveSheet();
	
	$objSheet->getColumnDimension('A')->setWidth(7.71);
	$objSheet->getColumnDimension('B')->setWidth(19.57);
	$objSheet->getColumnDimension('C')->setWidth(19.57);
	$objSheet->getColumnDimension('D')->setWidth(15);
	$objSheet->getColumnDimension('E')->setWidth(15);
	$objSheet->getColumnDimension('F')->setWidth(30.14);
	$objSheet->getColumnDimension('G')->setWidth(15);
	$objSheet->getColumnDimension('H')->setWidth(15);
	$objSheet->getColumnDimension('I')->setWidth(30);
	$objSheet->getColumnDimension('J')->setWidth(25);
	$objSheet->getColumnDimension('K')->setWidth(30);
	$objSheet->getColumnDimension('L')->setWidth(30);
	$objSheet->getColumnDimension('M')->setWidth(30.14);
	$objSheet->getColumnDimension('N')->setWidth(19.57);
	$objSheet->getColumnDimension('O')->setWidth(15);
	$objSheet->getColumnDimension('P')->setWidth(15);
	$objSheet->getColumnDimension('Q')->setWidth(15);
	
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle('Reporte de citas');
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
	
	//Se borra el reporte previamente generado por el usuario
	@unlink("./tmp/reporte_citas_" . $id_usuario . ".xlsx");
	
	// Save Excel 2007 file
	$id_usuario = $_SESSION["idUsuario"];
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("./tmp/reporte_citas_" . $id_usuario . ".xlsx");
	?>
	
	
	<form name="frm_reporte_citas" id="frm_reporte_citas" method="post" action="tmp/reporte_citas_<?php echo($id_usuario); ?>.xlsx">
	</form>
	<script id="ajax" type="text/javascript">
		document.getElementById("frm_reporte_citas").submit();
	</script>
	
	<?php
	exit;
}

?>
