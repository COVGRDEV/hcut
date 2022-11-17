<?php
	session_start();
	
	require_once("../db/DbAdmision.php");
	require_once("../funciones/PHPExcel/Classes/PHPExcel.php");
	
	
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	
	$tipoReporte = $_POST["tipoReporte"];

	switch ($tipoReporte) {
		case "1": //Reporte tiempos de atención 
			require_once("../db/DbConvenios.php");
			require_once("../db/DbPlanes.php");
			require_once("../funciones/Utilidades.php");

			$utilidades = new Utilidades(); 
			$dbConvenios = new DbConvenios(); 
			$dbPlanes = new DbPlanes(); 
			$dbAtenciones = new dbAdmision(); 
			
			$fechaInicial = $utilidades->str_decode($_POST["hddfechaInicial"]);
			$fechaFinal = $utilidades->str_decode($_POST["hddfechaFinal"]);
			$id_convenio = $utilidades->str_decode($_POST["hddconvenio"]);
			$id_plan = $utilidades->str_decode($_POST["hddplan"]);
			
			$vec_tiempos = [];
			$vec_estadisticas_estado = [];
			$subindice = "";			
			$subindice_e = ""; 
			$fecha_referencia = "01-01-1970 00:00:00"; //DD-MM-YYYY H-MI-S
			
			//Se obtiene el listado base del reporte
			$rta_atenciones = $dbAtenciones->reporteTiemposAtencion($fechaInicial, $fechaFinal, $id_convenio, $id_plan);
			//echo "\n\n canti admisiones: ".sizeof($rta_atenciones)."\n\n <p>"; 
					
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("A")->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("B")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("C")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("D")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("E")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("F")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("G")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("H")->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("I")->setWidth(35);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("J")->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("K")->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("L")->setWidth(15);
			$max_col_letra = "N";;
			
			//Se agregan los filtros seleccionados al reporte
			$contador_linea = 1;
			if ($fechaInicial != "") {
				$arr_aux = explode("-", $fechaInicial);
				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A".$contador_linea, "Fecha inicial:")
							->setCellValue("B".$contador_linea, $arr_aux[2]."/".$arr_aux[1]."/".$arr_aux[0]);
				$contador_linea++;
			}
			if ($fechaFinal != "") {
				$arr_aux = explode("-", $fechaFinal);
				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A".$contador_linea, "Fecha final:")
							->setCellValue("B".$contador_linea, $arr_aux[2]."/".$arr_aux[1]."/".$arr_aux[0]);
				$contador_linea++;
			}
			if ($id_convenio != "") {
				$convenio_obj = $dbConvenios->getConvenio($id_convenio);
				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A".$contador_linea, "Convenio:")
							->setCellValue("B".$contador_linea, $convenio_obj["nombre_convenio"]);
				$contador_linea++;
			}
			if ($id_plan != "") {
				$plan_obj = $dbPlanes->getPlan($id_plan);
				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A".$contador_linea, "Plan:")
							->setCellValue("B".$contador_linea, $plan_obj["nombre_plan"]);
				$contador_linea++;
			}
			
			$contador_linea++; 
			$objPHPExcel->getActiveSheet() 
						->mergeCells("F".$contador_linea.":H".$contador_linea) 
						->setCellValue("F".$contador_linea, "TIEMPO PROMEDIO POR ATENCION")
						->getStyle("A".$contador_linea.":".$max_col_letra.$contador_linea)->getFont()->setBold(true); 
			
			$contador_linea++;
			$objPHPExcel->getActiveSheet()						 
						->setCellValue("A".$contador_linea, "ID ADMISIÓN")
						->setCellValue("B".$contador_linea, "PRIMER NOMBRE")
						->setCellValue("C".$contador_linea, "SEGUNDO NOMBRE")
						->setCellValue("D".$contador_linea, "PRIMER APELLIDO")
						->setCellValue("E".$contador_linea, "SEGUNDO APELLIDO")
						->setCellValue("F".$contador_linea, "NÚMERO DE DOCUMENTO")
						->setCellValue("G".$contador_linea, "CONVENIO")  
						->setCellValue("H".$contador_linea, "PLAN") 
						->setCellValue("I".$contador_linea, "ESTADO") 		
						->setCellValue("J".$contador_linea, "HORAS") 
						->setCellValue("K".$contador_linea, "MINUTOS") 
						->setCellValue("L".$contador_linea, "SEGUNDOS"); 
			
			$objPHPExcel->getActiveSheet()->getStyle("A".$contador_linea.":".$max_col_letra.$contador_linea)->getFont()->setBold(true);
			
			$contador_linea++;
				
			
			// Calcular duración de la atención y acumular por convenio/plan/estado 
			
			$reg_anterior = "0"; 
			foreach ($rta_atenciones as $indice => $value) { 						
				// Obviar estados finales: 9->Despachado; 16->Atención cancelada
				if ($value["id_estado_atencion"] == 9 || $value["id_estado_atencion"] == 16) { 
					//echo " <b> XXXXX obviado x estado final </b>";
					continue;
				}
				//echo "\n \n <br><br>id_admision ".$value["id_admision"].", id_estado ".$value["id_estado_atencion"];
				$fecha_estado = date_create($value["fecha_estado"]); 
				$fecha_siguiente_estado = date_create($rta_atenciones[$indice + 1]["fecha_estado"]); 
				//echo " * FECHA_ESTADO creada para ".$value["fecha_estado"].": ".$fecha_estado->format("Y/m/d H:i:s")." - - > siguiente fecha: ".$fecha_siguiente_estado->format("Y/m/d H:i:s");
				
				$duracion = $fecha_estado->diff($fecha_siguiente_estado); 
			
				//echo " - - > duracion1: ".$duracion->format("%H:%I:%S"). "; ".var_dump($duracion)."<br>"; 				
				
				$subindice = $value["nombre_convenio"]."-".$value["nombre_plan"]."-".$value["orden_estado"]."-".$value["nombre_estado"]; 
				$subindice_e = $subindice; 
				
				if (array_key_exists($subindice_e, $vec_estadisticas_estado)) {
					$vec_estadisticas_estado[$subindice_e]["canti_atenciones"]++; 
				} else {
					$vec_estadisticas_estado[$subindice_e]["canti_atenciones"] = 1;
					$vec_estadisticas_estado[$subindice_e]["canti_atenciones_2dias"] = 0;
				}				
				
				// Obviar estados que cambian de fecha (generan un peso ilógico en el informe) 
				if ($fecha_siguiente_estado->format("Y-m-d") <> $fecha_estado->format("Y-m-d")) { 
					$vec_estadisticas_estado[$subindice_e]["canti_atenciones_2dias"]++; 
					//echo " <b> XXXXX obviado x 2días </b>";
					next($rta_atenciones); 
					continue; 
				} 

				if (array_key_exists($subindice, $vec_tiempos)) { 
					$vec_tiempos[$subindice]["fecha_acumulada"]->add($duracion); 
					/*
					echo "<br>A C U M U L É ::::: "; 					
					echo $subindice.": ".$vec_tiempos[$subindice]["nombre_estado"]." --- + ".$duracion->format("%H:%I:%S")." ===> fecha_acumulada = ".$vec_tiempos[$subindice]["fecha_acumulada"]->format("Y/m/d H:i:s"); 
					*/
				}  else { 
					$vec_tiempos[$subindice]["id_admision"] = $value["id_admision"];
					$vec_tiempos[$subindice]["nombre_1"] = $value["nombre_1"];
					$vec_tiempos[$subindice]["nombre_2"] = $value["nombre_2"];
					$vec_tiempos[$subindice]["apellido_1"] = $value["apellido_1"];
					$vec_tiempos[$subindice]["apellido_2"] = $value["apellido_2"];
					$vec_tiempos[$subindice]["numero_documento"] = $value["numero_documento"];
					$vec_tiempos[$subindice]["id_estado_atencion"] = $value["id_estado_atencion"]; 
					$vec_tiempos[$subindice]["nombre_estado"] = $value["nombre_estado"]; 
					$vec_tiempos[$subindice]["nombre_convenio"] = $value["nombre_convenio"]; 
					$vec_tiempos[$subindice]["nombre_plan"] = $value["nombre_plan"]; 
					$vec_tiempos[$subindice]["fecha_acumulada"] = DateTime::createFromFormat("d-m-Y H:i:s", $fecha_referencia); 
					$vec_tiempos[$subindice]["fecha_acumulada"]->add($duracion); 					
					/*
					echo "<br>C R E É !!! ";
					echo $subindice.": ".$vec_tiempos[$subindice]["nombre_estado"]." --- + ".$duracion->format("%H:%I:%S")." ===> fecha_acumulada = ".$vec_tiempos[$subindice]["fecha_acumulada"]->format("Y/m/d H:i:s"); 					
					*/
				} 

				next($rta_atenciones); 
			}			
			
			//echo "<br><br>************************************************************ Generación excel: ******************************************************<br><br>";			
			
			// Ordernar vector de duraciones, e imprimir en celdas del excel: 
			
			ksort($vec_tiempos, SORT_NATURAL);

			foreach ($vec_tiempos as $clave=>$valor) {				

				$subindice_e = $clave;

				$duracion = DateTime::createFromFormat("d-m-Y H:i:s", $fecha_referencia)->diff($valor["fecha_acumulada"]); 
				$segundos_acumulados = $duracion->s + ($duracion->i * 60) + ($duracion->h * 3600) + ($duracion->d * 24*3600) + ($duracion->m * 30*24*3600) + ($duracion->y * 365*24*3600); 								
				
				$atenciones_acumuladas = $vec_estadisticas_estado[$subindice_e]["canti_atenciones"]-$vec_estadisticas_estado[$subindice_e]["canti_atenciones_2dias"];
				$segundos_promedio = $segundos_acumulados / $atenciones_acumuladas; 				
				$horas = floor($segundos_promedio / 3600); 
				$minutos = floor(($segundos_promedio - $horas*3600) / 60); 
				$segundos = round($segundos_promedio - ($horas*3600 + $minutos*60));
				
				$indicador = round(100 - ($vec_estadisticas_estado[$subindice_e]["canti_atenciones_2dias"] / $vec_estadisticas_estado[$subindice_e]["canti_atenciones"]), 4);
				/*
				echo "<br><br>estado ".$valor["id_estado_atencion"]." :::: ".$clave.": fecha_referencia: ".$fecha_referencia."; fecha_acumulada: ".$valor["fecha_acumulada"]->format("Y/m/d H:i:s")." --> DuraciónAcumulada: ".$duracion->format("%d-%m-%y %H:%I:%S"); 				
				echo " ===> segundos_acumulados = ".$segundos_acumulados;
				echo "; canti_atns=".$vec_estadisticas_estado[$subindice_e]["canti_atenciones"]."; canti_atns_2dias=".$vec_estadisticas_estado[$subindice_e]["canti_atenciones_2dias"]." ==> atenciones_acumuladas=".$atenciones_acumuladas; 
				echo "<br>segundos_promedio=".$segundos_promedio."; horas=".$horas."; minutos=".$minutos."; segundos=".$segundos; 
				*/
				$objPHPExcel->getActiveSheet() 							
					->setCellValue("A".$contador_linea, $valor["id_admision"])				
						->setCellValue("B".$contador_linea, $valor["nombre_1"])
						->setCellValue("C".$contador_linea, $valor["nombre_2"])
						->setCellValue("D".$contador_linea, $valor["apellido_1"])
						->setCellValue("E".$contador_linea, $valor["apellido_2"])
						->setCellValue("F".$contador_linea, $valor["numero_documento"])
						->setCellValue("G".$contador_linea, $valor["nombre_convenio"]) 
						->setCellValue("H".$contador_linea, $valor["nombre_plan"]) 					
						->setCellValue("I".$contador_linea, $valor["nombre_estado"]) 
						->setCellValue("J".$contador_linea, $horas) 
						->setCellValue("K".$contador_linea, $minutos) 
						->setCellValue("L".$contador_linea, $segundos); 
					
				$contador_linea++; 
			}			

			//Se renombra la hoja actual
			$objPHPExcel->getActiveSheet()->setTitle("Reporte de Tiempos de Atención");

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
			@unlink("./tmp/reporte_tiempos_atencion_".$id_usuario.".xlsx");
			
			// Save Excel 2007 file
			$id_usuario = $_SESSION["idUsuario"];
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
			$objWriter->save("./tmp/reporte_tiempos_atencion_".$id_usuario.".xlsx");
		?>
		<form name="frm_reporte_tiempos_atencion" id="frm_reporte_tiempos_atencion" method="post" action="tmp/reporte_tiempos_atencion_<?php echo($id_usuario); ?>.xlsx">
		</form>
		<script id="ajax" type="text/javascript">
			document.getElementById("frm_reporte_tiempos_atencion").submit();
		</script>
		<?php
			break;
	}
	
	exit;
?>