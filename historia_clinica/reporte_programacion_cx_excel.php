<?php
	session_start();
	
	require_once("../db/DbProgramacionCx.php");
	require_once("../db/DbMaestroProcedimientos.php");
	require_once("../db/DbMaestroMedicamentos.php");
	require_once("../db/DbMaestroInsumos.php");
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbListas.php");
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/PHPExcel/Classes/PHPExcel.php");
	require_once("../funciones/pdf/funciones.php");
	
	$dbProgramacionCx = new DbProgramacionCx();
	$dbMaestroProcedimientos = new DbMaestroProcedimientos();
	$dbMaestroMedicamentos = new DbMaestroMedicamentos();
	$dbMaestroInsumos = new DbMaestroInsumos();
	$dbUsuarios = new DbUsuarios();
	$dbListas = new DbListas();
	
	$utilidades = new Utilidades();
	$funciones_persona = new FuncionesPersona();
	
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	
	$tipo_reporte = $_POST["hdd_tipo_reporte"];
	
	switch ($tipo_reporte) {
		case "1": //Reporte general
			@$fecha_ini = $utilidades->str_decode($_POST["hdd_fecha_ini"]);
			@$fecha_fin = $utilidades->str_decode($_POST["hdd_fecha_fin"]);
			@$tipo_fecha = $utilidades->str_decode($_POST["hdd_tipo_fecha"]);
			@$tipo_concepto = $utilidades->str_decode($_POST["hdd_tipo_concepto_r"]);
			@$cod_concepto = $utilidades->str_decode($_POST["hdd_cod_concepto_r"]);
			@$id_usuario_prof = $utilidades->str_decode($_POST["hdd_id_usuario_prof"]);
			@$cant_estados_prog = intval($_POST["hdd_cant_estados_prog"], 10);
			
			$lista_estados = array();
			for ($i = 0; $i < $cant_estados_prog; $i++) {
				$lista_estados[$i]["id_estado_prog"] = $utilidades->str_decode($_POST["hdd_estado_prog_".$i]);
				$lista_estados[$i]["sel_estado_prog"] = intval($_POST["hdd_sel_estado_prog_".$i], 10);
			}
			
			//Se obtiene el listado de las programaciones de cirugía
			$lista_programacion_cx = $dbProgramacionCx->getListaProgramacionCxFechas($fecha_ini, $fecha_fin, $tipo_fecha, $tipo_concepto, $cod_concepto, $id_usuario_prof, $lista_estados);
			
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("A")->setWidth(20);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("B")->setWidth(20);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("C")->setWidth(30);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("D")->setWidth(20);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("E")->setWidth(40);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("F")->setWidth(20);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("G")->setWidth(20);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("H")->setWidth(30);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("I")->setWidth(30);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("J")->setWidth(40);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("K")->setWidth(20);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("L")->setWidth(20);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("M")->setWidth(20);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("N")->setWidth(20);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("O")->setWidth(20);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("P")->setWidth(20);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("Q")->setWidth(30);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("R")->setWidth(30);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("S")->setWidth(20);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("T")->setWidth(20);
			
			//Se agregan los filtros seleccionados al reporte
			$contador_linea = 1;
			
			if ($tipo_fecha == "1") {
				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A".$contador_linea, "Fecha de programación");
			} else {
				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A".$contador_linea, "Fecha de registro");
			}
			$contador_linea++;
			
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue("A".$contador_linea, "Inicio:")
						->setCellValue("B".$contador_linea, $fecha_ini);
			$contador_linea++;
			
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue("A".$contador_linea, "Fin:")
						->setCellValue("B".$contador_linea, $fecha_fin);
			$contador_linea++;
			
			if ($cod_concepto != "") {
				switch ($tipo_concepto) {
					case "P":
						$procedimiento_obj = $dbMaestroProcedimientos->getProcedimiento($cod_concepto);
						$objPHPExcel->setActiveSheetIndex(0)
									->setCellValue("A".$contador_linea, "Procedimiento:")
									->setCellValue("B".$contador_linea, $procedimiento_obj["nombre_procedimiento"]);
						break;
						
					case "M":
						$medicamento_obj = $dbMaestroMedicamentos->getMedicamento($cod_concepto);
						$objPHPExcel->setActiveSheetIndex(0)
									->setCellValue("A".$contador_linea, "Medicamento:")
									->setCellValue("B".$contador_linea, $medicamento_obj["nombre_comercial"]." (".$medicamento_obj["nombre_generico"].") - ".$medicamento_obj["presentacion"]);
						break;
						
					case "I":
						$insumo_obj = $dbMaestroInsumos->getInsumo($cod_concepto);
						$objPHPExcel->setActiveSheetIndex(0)
									->setCellValue("A".$contador_linea, "Insumo:")
									->setCellValue("B".$contador_linea, $insumo_obj["nombre_insumo"]);
						break;
				}
				$contador_linea++;
			}
			
			if ($id_usuario_prof != "") {
				$usuario_prof_obj = $dbUsuarios->getUsuario($id_usuario_prof);
				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A".$contador_linea, "Profesional:")
							->setCellValue("B".$contador_linea, $usuario_prof_obj["nombre_usuario"]." ".$usuario_prof_obj["apellido_usuario"]);
				$contador_linea++;
			}
			
			$cadena_aux = "";
			$bol_vacios = false;
			foreach ($lista_estados as $estado_aux) {
				if ($estado_aux["sel_estado_prog"] == 1) {
					$detalle_aux = $dbListas->getDetalle($estado_aux["id_estado_prog"]);
					if ($cadena_aux != "") {
						$cadena_aux .= ", ";
					}
					$cadena_aux .= $detalle_aux["nombre_detalle"];
				} else {
					$bol_vacios = true;
				}
			}
			if ($cadena_aux != "" && $bol_vacios) {
				$usuario_prof_obj = $dbUsuarios->getUsuario($id_usuario_prof);
				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A".$contador_linea, "Estado(s):")
							->setCellValue("B".$contador_linea, $cadena_aux);
				$contador_linea++;
			}
			
			$contador_linea++;
			
			$objPHPExcel->getActiveSheet()
						->setCellValue("A".$contador_linea, "FECHA PROGRAMADA")
						->setCellValue("B".$contador_linea, "HORA PROGRAMADA")
						->setCellValue("C".$contador_linea, "TIPO DOCUMENTO")
						->setCellValue("D".$contador_linea, "NÚMERO DOCUMENTO")
						->setCellValue("E".$contador_linea, "NOMBRE COMPLETO")
						->setCellValue("F".$contador_linea, "TELÉFONO 1")
						->setCellValue("G".$contador_linea, "TELÉFONO 2")
						->setCellValue("H".$contador_linea, "CONVENIO")
						->setCellValue("I".$contador_linea, "PROFESIONAL")
						->setCellValue("J".$contador_linea, "PRODUCTOS/SERVICIOS")
						->setCellValue("K".$contador_linea, "TIPO LENTE")
						->setCellValue("L".$contador_linea, "SERIAL LENTE")
						->setCellValue("M".$contador_linea, "PODER LENTE")
						->setCellValue("N".$contador_linea, "ESTADO")
						->setCellValue("O".$contador_linea, "FECHA REGISTRO")
						->setCellValue("P".$contador_linea, "HORA REGISTRO")
						->setCellValue("Q".$contador_linea, "USUARIO CANCELACIÓN")
						->setCellValue("R".$contador_linea, "MOTIVO CANCELACIÓN")
						->setCellValue("S".$contador_linea, "FECHA CANCELACIÓN")
						->setCellValue("T".$contador_linea, "HORA CANCELACIÓN");
			
			$objPHPExcel->getActiveSheet()->getStyle("A".$contador_linea.":T".$contador_linea)->getFont()->setBold(true);
			
			$contador_linea++;
			
			foreach ($lista_programacion_cx as $prog_cx_aux) {
				$lista_programacion_cx_det = $dbProgramacionCx->getListaProgramacionCxDet($prog_cx_aux["id_prog_cx"]);
				
				$cadena_productos = "";
				$cadena_tipos_lentes = "";
				$cadena_seriales_lentes = "";
				$cadena_poderes_lentes = "";
				if (count($lista_programacion_cx_det) > 0) {
					foreach ($lista_programacion_cx_det as $prog_det_aux) {
						if ($cadena_productos != "") {
							$cadena_productos .= " --- ";
						}
						switch ($prog_det_aux["tipo_elemento"]) {
							case "P":
								$cadena_productos .= $prog_det_aux["cod_procedimiento"]." - ".$prog_det_aux["nombre_procedimiento"];
								break;
							case "M":
								$cadena_productos .= $prog_det_aux["cod_medicamento"]." - ".$prog_det_aux["nombre_comercial"]." (".$prog_det_aux["nombre_generico"].") - ".$prog_det_aux["presentacion"];
								break;
							case "I":
								$cadena_productos .= $prog_det_aux["cod_insumo"]." - ".$prog_det_aux["nombre_insumo"];
								
								//Lentes
								$lista_programacion_cx_det_val = $dbProgramacionCx->getListaProgramacionCxDetValores($prog_det_aux["id_prog_cx_det"]);
								if (count($lista_programacion_cx_det_val) > 0) {
									foreach ($lista_programacion_cx_det_val as $prog_val_aux) {
										if ($cadena_tipos_lentes != "") {
											$cadena_tipos_lentes .= " --- ";
											$cadena_seriales_lentes .= " --- ";
											$cadena_poderes_lentes .= " --- ";
										}
										$cadena_tipos_lentes .= $prog_val_aux["tipo_lente"];
										$cadena_seriales_lentes .= $prog_val_aux["serial_lente"];
										$cadena_poderes_lentes .= $prog_val_aux["poder_lente"];
									}
								}
								break;
						}
					}
				}
				
				//Nombre del paciente
				$nombre_completo = $funciones_persona->obtenerNombreCompleto($prog_cx_aux["nombre_1"], $prog_cx_aux["nombre_2"], $prog_cx_aux["apellido_1"], $prog_cx_aux["apellido_2"]);
				
				$objPHPExcel->getActiveSheet()
					->setCellValue("A".$contador_linea, $prog_cx_aux["fecha_prog_t"])
					->setCellValue("B".$contador_linea, $prog_cx_aux["hora_prog_t"])
					->setCellValue("C".$contador_linea, $prog_cx_aux["tipo_documento"])
					->setCellValue("D".$contador_linea, $prog_cx_aux["numero_documento"])
					->setCellValue("E".$contador_linea, $nombre_completo)
					->setCellValue("F".$contador_linea, $prog_cx_aux["telefono_1"])
					->setCellValue("G".$contador_linea, $prog_cx_aux["telefono_2"])
					->setCellValue("H".$contador_linea, $prog_cx_aux["nombre_convenio"])
					->setCellValue("I".$contador_linea, $prog_cx_aux["nombre_usuario_prof"]." ".$prog_cx_aux["apellido_usuario_prof"])
					->setCellValue("J".$contador_linea, $cadena_productos)
					->setCellValue("K".$contador_linea, $cadena_tipos_lentes)
					->setCellValue("L".$contador_linea, $cadena_seriales_lentes)
					->setCellValue("M".$contador_linea, $cadena_poderes_lentes)
					->setCellValue("N".$contador_linea, $prog_cx_aux["estado_prog"])
					->setCellValue("O".$contador_linea, $prog_cx_aux["fecha_crea_t"])
					->setCellValue("P".$contador_linea, $prog_cx_aux["hora_crea_t"])
					->setCellValue("Q".$contador_linea, $prog_cx_aux["nombre_usuario_cancela"]." ".$prog_cx_aux["apellido_usuario_cancela"])
					->setCellValue("R".$contador_linea, $prog_cx_aux["nombre_motivo"])
					->setCellValue("S".$contador_linea, $prog_cx_aux["fecha_cancela_t"])
					->setCellValue("T".$contador_linea, $prog_cx_aux["hora_cancela_t"]);
				
				$contador_linea++;
			}
			
			//Se renombra la hoja actual
			$objPHPExcel->getActiveSheet()->setTitle("Reporte Programación Cx");
			
			//Set document properties
			$objPHPExcel->getProperties()->setCreator("OSPS")
					->setLastModifiedBy("OSPS")
					->setTitle("Office 2007 XLSX")
					->setSubject("Office 2007 XLSX")
					->setDescription("Document for Office 2007 XLSX.")
					->setKeywords("office 2007")
					->setCategory("result");
			
			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);
			
			//Se borra el reporte previamente generado por el usuario
			@unlink("./tmp/reporte_programacion_cx_".$id_usuario.".xlsx");
			
			// Save Excel 2007 file
			$id_usuario = $_SESSION["idUsuario"];
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
			$objWriter->save("./tmp/reporte_programacion_cx_".$id_usuario.".xlsx");
		?>
		<form name="frm_reporte_prog_cx" id="frm_reporte_prog_cx" method="post" action="tmp/reporte_programacion_cx_<?php echo($id_usuario); ?>.xlsx">
		</form>
		<script id="ajax" type="text/javascript">
			document.getElementById("frm_reporte_prog_cx").submit();
		</script>
		<?php
			break;
	}
	
	exit;
?>
