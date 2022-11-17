<?php @session_start(); 
	/*
	 * Pagina para crear registros extendidos de Pterigio
	 * Autor: ZJJC - 18/04/2017
	 */
 	header("Content-Type: text/xml; charset=UTF-8"); 
	
	require_once("../db/DbConsultasPterigio.php"); 
	require_once("../funciones/Utilidades.php"); 
	
	
	// Proceso de ajax: 
	$opcion=4; //_POST["opcion"]; 
	//echo "<br>OPCION = ".$opcion; 
	$upload_folder="../tmp/uploads/"; 	
	switch ($opcion) {	
		
		case 4: 
			//echo '{"success":true,"uuid":"9e03e42d-a274-45c3-a3aa-c2f2645d230a","uploadName":"ojo5.jpg"}'; 

			for ($i=0; $i<1; $i++) {
				
				//echo "<br>";
				//var_dump($_FILES['file'.$i]);
				$nombre_archivo = $_FILES['qqfile']['name'];
				$tipo_archivo = $_FILES['qqfile']['type'];
				$tamano_archivo = $_FILES['qqfile']['size'];
				$tmp_archivo = $_FILES['qqfile']['tmp_name'];
				$archivador = $upload_folder . '/' . $nombre_archivo;			
				if (!move_uploaded_file($tmp_archivo, $archivador)) {
					//echo "Ocurrio un error al subir el file. No pudo guardarse.";
					$respuesta = array('success' => false, 'msj' => "Ocurrio un error al subir el file. No pudo guardarse.");	
				} else {					
					//echo "<br>CARGADO: $upload_folder / $nombre_archivo ($archivador)  -  TIPO: $tipo_archivo  -  TAM: $tamano_archivo - TMP: $tmp_archivo";
					$respuesta = array('success' => true, 'ruta' => $archivador, 'de'=>$tmp_archivo, 'tipo' => $tipo_archivo, 'tamaño' => $tamano_archivo, "uuid"=>$nombre_archivo);	
				}
			}			
			$respuesta=array_merge($respuesta, $_FILES);
			
			echo json_encode($respuesta);
			break; 
			
		case "0": //Prueba archivos; Función ajax original OSPS2017
		
			echo "<br>0****************************************************************************<br>";
			var_dump($_FILES);
			echo "<br>****************************************************************************<br>";
			/*
			array(2) { ["file1"]=> array(5) { ["name"]=> string(8) "ojo1.jpg" ["type"]=> string(10) "image/jpeg" ["tmp_name"]=> string(17) "C:\tmp\phpAD1.tmp" ["error"]=> int(0) ["size"]=> int(7885) } 
					   ["file2"]=> array(5) { ["name"]=> string(8) "ojo2.jpg" ["type"]=> string(10) "image/jpeg" ["tmp_name"]=> string(17) "C:\tmp\phpAD2.tmp" ["error"]=> int(0) ["size"]=> int(5345) } } 
					   
					   // ERRRRRR! ====> no sube ningún tipo de múltiple ni vectoriales!!!!
			*/
			
			echo "<br>POST: ".$_POST["pte_grado_od"];
						
			break;
			
		case "1": //Prueba archivos; Función ajax original OSPS2017 modificada para procesar múltiples:  			
			
			echo "<br>0****************************************************************************<br>";
			var_dump($_FILES);
			echo "<br>****************************************************************************<br>"; 
			/*
			array(5) { 
						["file1"]=> array(5) { ["name"]=> string(8) "ojo1.jpg" ["type"]=> string(10) "image/jpeg" ["tmp_name"]=> string(17) "C:\tmp\php9AB.tmp" ["error"]=> int(0) ["size"]=> int(7885) } 
						["file2"]=> array(5) { ["name"]=> string(8) "ojo2.jpg" ["type"]=> string(10) "image/jpeg" ["tmp_name"]=> string(17) "C:\tmp\php9AC.tmp" ["error"]=> int(0) ["size"]=> int(5345) } 
						["filemu"]=> array(5) { ["name"]=> string(8) "ojo4.jpg" ["type"]=> string(10) "image/jpeg" ["tmp_name"]=> string(17) "C:\tmp\php9AE.tmp" ["error"]=> int(0) ["size"]=> int(13823) } 
						["filemuc"]=> array(5) { ["name"]=> array(3) { [0]=> string(8) "ojo5.jpg" [1]=> string(8) "ojo6.jpg" [2]=> string(8) "ojo7.jpg" } 
												["type"]=> array(3) { [0]=> string(10) "image/jpeg" [1]=> string(10) "image/jpeg" [2]=> string(10) "image/jpeg" } 
												["tmp_name"]=> array(3) { [0]=> string(17) "C:\tmp\php9AF.tmp" [1]=> string(17) "C:\tmp\php9B0.tmp" [2]=> string(17) "C:\tmp\php9B1.tmp" } 
												["error"]=> array(3) { [0]=> int(0) [1]=> int(0) [2]=> int(0) } 
												["size"]=> array(3) { [0]=> int(7439) [1]=> int(8672) [2]=> int(6089) } 
											} 
						["filemuv"]=> array(5) { ["name"]=> array(4) { [0]=> string(8) "ojo8.jpg" [1]=> string(8) "ojo9.jpg" [2]=> string(9) "ojo10.jpg" [3]=> string(9) "ojo11.jpg" } 
												["type"]=> array(4) { [0]=> string(10) "image/jpeg" [1]=> string(10) "image/jpeg" [2]=> string(10) "image/jpeg" [3]=> string(10) "image/jpeg" } 
												["tmp_name"]=> array(4) { [0]=> string(17) "C:\tmp\php9B2.tmp" [1]=> string(17) "C:\tmp\php9B3.tmp" [2]=> string(17) "C:\tmp\php9B4.tmp" [3]=> string(17) "C:\tmp\php9B5.tmp" } 
												["error"]=> array(4) { [0]=> int(0) [1]=> int(0) [2]=> int(0) [3]=> int(0) } 
												["size"]=> array(4) { [0]=> int(8997) [1]=> int(4848) [2]=> int(8810) [3]=> int(8909) } 
											} 
			} 
			*/			
			echo "<br>POST: ".$_POST["pte_grado_od"];
						
			break;		
		
		case "22": //Prueba new FormData(); 		
		
			echo "<br>22****************************************************************************<br>";
			var_dump($_FILES);
			echo "<br>****************************************************************************<br>";			
			/*
			array(5) {  ["file1"]=> array(5) { ["name"]=> string(8) "ojo1.jpg" ["type"]=> string(10) "image/jpeg" ["tmp_name"]=> string(18) "C:\tmp\php14E9.tmp" ["error"]=> int(0) ["size"]=> int(7885) } 
						["file2"]=> array(5) { ["name"]=> string(8) "ojo2.jpg" ["type"]=> string(10) "image/jpeg" ["tmp_name"]=> string(18) "C:\tmp\php14EA.tmp" ["error"]=> int(0) ["size"]=> int(5345) } 
						
						// ERRRRRR! ====> name="filemu" en múltiples trae sólo el último del obj múltiple: 
						["filemu"]=> array(5) { ["name"]=> string(8) "ojo4.jpg" ["type"]=> string(10) "image/jpeg" ["tmp_name"]=> string(18) "C:\tmp\php14EC.tmp" ["error"]=> int(0) ["size"]=> int(13823) } 
						
						// ====> name="filemuc[]" Trae OK todos los archivos en el obj múltiple: 		
						["filemuc"]=> array(5) { ["name"]=> array(2) { [0]=> string(8) "ojo5.jpg" [1]=> string(8) "ojo6.jpg" } 
												 ["type"]=> array(2) { [0]=> string(10) "image/jpeg" [1]=> string(10) "image/jpeg" } 
												 ["tmp_name"]=> array(2) { [0]=> string(18) "C:\tmp\php14ED.tmp" [1]=> string(18) "C:\tmp\php14EE.tmp" } 
												 ["error"]=> array(2) { [0]=> int(0) [1]=> int(0) } 
												 ["size"]=> array(2) { [0]=> int(7439) [1]=> int(8672) } 
						} 
						
						// ====> name="filemuv[]" Trae OK todos los archivos en todos los obj múltiples:  
						["filemuv"]=> array(5) { ["name"]=> array(4) { [0]=> string(8) "ojo7.jpg" [1]=> string(8) "ojo8.jpg" [2]=> string(8) "ojo9.jpg" [3]=> string(9) "ojo10.jpg" } 
												 ["type"]=> array(4) { [0]=> string(10) "image/jpeg" [1]=> string(10) "image/jpeg" [2]=> string(10) "image/jpeg" [3]=> string(10) "image/jpeg" } 
												 ["tmp_name"]=> array(4) { [0]=> string(18) "C:\tmp\php14EF.tmp" [1]=> string(18) "C:\tmp\php14F0.tmp" [2]=> string(18) "C:\tmp\php14F1.tmp" [3]=> string(18) "C:\tmp\php14F2.tmp" } 
												 ["error"]=> array(4) { [0]=> int(0) [1]=> int(0) [2]=> int(0) [3]=> int(0) } 
												 ["size"]=> array(4) { [0]=> int(6089) [1]=> int(8997) [2]=> int(4848) [3]=> int(8810) } 
						} 
					} 			
			*/
			
			echo "<br>POST: ".$_POST["pte_grado_od"]."<br>";
			$upload_folder ='tmp';			
			
			for ($i=1; $i<=2; $i++) {
				
				echo "<br>";
				var_dump($_FILES['file'.$i]);
				$nombre_archivo = $_FILES['file'.$i]['name'];
				$tipo_archivo = $_FILES['file'.$i]['type'];
				$tamano_archivo = $_FILES['file'.$i]['size'];
				$tmp_archivo = $_FILES['file'.$i]['tmp_name'];
				$archivador = $upload_folder . '/' . $nombre_archivo;			
				if (!move_uploaded_file($tmp_archivo, $archivador)) {
					echo "Ocurrio un error al subir el file$i. No pudo guardarse.";
				} else {
					echo "<br>CARGADO: $upload_folder / $nombre_archivo ($archivador)  -  TIPO: $tipo_archivo  -  TAM: $tamano_archivo - TMP: $tmp_archivo";
				}
			}			

			//----------------------------
			
			$nombre_archivo = sizeof($_FILES['filemu']['name']);
			echo "<p>TAM VECTOR 3 = $nombre_archivo<br>";
			var_dump($_FILES['filemu']);
			$i="mu";
			$nombre_archivo = $_FILES['file'.$i]['name'];
			$tipo_archivo = $_FILES['file'.$i]['type'];
			$tamano_archivo = $_FILES['file'.$i]['size'];
			$tmp_archivo = $_FILES['file'.$i]['tmp_name'];
			$archivador = $upload_folder . '/' . $nombre_archivo;			
			if (!move_uploaded_file($tmp_archivo, $archivador)) {
				echo "Ocurrio un error al subir el file$i. No pudo guardarse.";
			} else {
				echo "<br>CARGADO: $upload_folder / $nombre_archivo ($archivador)  -  TIPO: $tipo_archivo  -  TAM: $tamano_archivo - TMP: $tmp_archivo";
			}			
			
			//----------------------------
			
			$nombre_archivo = sizeof($_FILES['filemuc']['name']);
			echo "<p>TAM VECTOR 4(c) = $nombre_archivo<br>";
			var_dump($_FILES['filemuc']);
			
			$canti_files_multiple=sizeof($_FILES['filemuc']['name']);
			for ($i=0; $i<$canti_files_multiple; $i++){
				$nombre_archivo = $_FILES['filemuc']['name'][$i]; 
				$tipo_archivo = $_FILES['filemuc']['type'][$i]; 
				$tamano_archivo = $_FILES['filemuc']['size'][$i]; 
				$tmp_archivo = $_FILES['filemuc']['tmp_name'][$i]; 
				$archivador = $upload_folder . '/' . $nombre_archivo; 
				if (!move_uploaded_file($tmp_archivo, $archivador)) {
					echo "Ocurrio un error al subir el filemuc[$i]. No pudo guardarse.";
				} else {
					echo "<br>CARGADO: $upload_folder / $nombre_archivo ($archivador)  -  TIPO: $tipo_archivo  -  TAM: $tamano_archivo - TMP: $tmp_archivo";
				} 			
			}		

			//----------------------------
			
			$nombre_archivo = sizeof($_FILES['filemuv']['name']);
			echo "<p>TAM VECTOR 5(v) = $nombre_archivo<br>";
			var_dump($_FILES['filemuv']);			
			
			$canti_files_multiple=sizeof($_FILES['filemuv']['name']);
			for ($i=0; $i<$canti_files_multiple; $i++){
				$nombre_archivo = $_FILES['filemuv']['name'][$i]; 
				$tipo_archivo = $_FILES['filemuv']['type'][$i]; 
				$tamano_archivo = $_FILES['filemuv']['size'][$i]; 
				$tmp_archivo = $_FILES['filemuv']['tmp_name'][$i]; 
				$archivador = $upload_folder . '/' . $nombre_archivo; 
				if (!move_uploaded_file($tmp_archivo, $archivador)) {
					echo "Ocurrio un error al subir el filemuv[$i]. No pudo guardarse.";
				} else {
					echo "<br>CARGADO: $upload_folder / $nombre_archivo ($archivador)  -  TIPO: $tipo_archivo  -  TAM: $tamano_archivo - TMP: $tmp_archivo";
				} 			
			}				
			
			break;			
	}	
?>