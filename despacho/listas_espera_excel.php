<?php
	session_start();
	
	require_once("../db/DbListasEspera.php");
	require_once '../funciones/PHPExcel/Classes/PHPExcel.php';
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/pdf/funciones.php");
	require_once '../funciones/Utilidades.php';
	
	$dbListasEspera = new DbListasEspera();
	$funciones_persona = new FuncionesPersona();
	$utilidades = new Utilidades();
	
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	
	$tipo_reporte = $_POST["hdd_tipo_reporte_e"];
	
	switch ($tipo_reporte) {
		case "1": //Reporte de listado de pacientes en espera
			$parametro = $utilidades->str_decode($_POST['hdd_parametro_e']);
			$id_tipo_lista = $utilidades->str_decode($_POST['hdd_tipo_lista_e']);
			
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(19.00);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20.00);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(21.50);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40.00);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(22.00);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20.00);
			
			$objPHPExcel->getActiveSheet()
					->setCellValue('A1', 'Fecha de inscripción')
					->setCellValue('B1', 'Tipo de documento')
					->setCellValue('C1', 'Número de documento')
					->setCellValue('D1', 'Nombre')
					->setCellValue('E1', 'Teléfono(s)')
					->setCellValue('F1', 'Cirugía');
			
			$objPHPExcel->getActiveSheet()->getStyle("A1:F1")->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle("A1:F1")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			
			$objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle("B1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle("C1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle("D1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle("E1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle("F1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			//Se obtiene la lista de espera
			$lista_espera = $dbListasEspera->get_listas_espera($parametro, $id_tipo_lista, 235);
			
			$num_linea = 2;
			foreach($lista_espera as $espera_aux) {
				$objPHPExcel->getActiveSheet()
							->setCellValue("A".$num_linea, $espera_aux["fecha_lista_t"])
							->setCellValue("B".$num_linea, $espera_aux["tipo_documento"])
							->setCellValue("C".$num_linea, $espera_aux["numero_documento"])
							->setCellValue("D".$num_linea, $funciones_persona->obtenerNombreCompleto($espera_aux["nombre_1"], $espera_aux["nombre_2"], $espera_aux["apellido_1"], $espera_aux["apellido_2"]))
							->setCellValue("E".$num_linea, $espera_aux["telefono_contacto"])
							->setCellValue("F".$num_linea, $espera_aux["tipo_cirugia"]);
					
				$objPHPExcel->getActiveSheet()->getStyle("A".$num_linea.":F".$num_linea)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
				$num_linea++;
			}
			
			//Se renombra la hoja
			$objPHPExcel->getActiveSheet()->setTitle("Pacientes");
			
			// Set document properties
			$objPHPExcel->getProperties()->setCreator("OSPS")
					->setLastModifiedBy("OSPS")
					->setTitle("Office 2007 XLSX")
					->setSubject("Office 2007 XLSX")
					->setDescription("Document for Office 2007 XLSX.")
					->setKeywords("office 2007")
					->setCategory("result");
			
			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			//$objPHPExcel->setActiveSheetIndex(0);
			
			//Se borra el reporte previamente generado por el usuario
			$id_usuario = $_SESSION["idUsuario"];
			@unlink("../tesoreria/tmp/reporte_listado_espera_".$id_usuario.".xlsx");
			
			// Save Excel 2007 file
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save("../tesoreria/tmp/reporte_listado_espera_".$id_usuario.".xlsx");
		?>
        <form name="frm_reporte_listado_espera" id="frm_reporte_listado_espera" method="post" action="../tesoreria/tmp/reporte_listado_espera_<?php echo($id_usuario); ?>.xlsx">
        </form>
        <script id="ajax" type="text/javascript">
			document.getElementById("frm_reporte_listado_espera").submit();
		</script>
		<?php
			break;
	}
	exit;
?>
