<?php @session_start();
	/*
	 * Pagina para crear registros extendidos de Pterigio
	 * Autor: ZJJC - 18/04/2017
	 */
 	header("Content-Type: text/plain; charset=UTF-8");
	
	require_once("../db/DbConsultasPterigio.php"); 
	require_once("../db/DbArchivos.php"); 
	require_once("../funciones/Utilidades.php"); 
	require_once "../funciones/fine-uploader/Class_UploadHandler.php"; 	
	
	function guardar_consulta_pterigio($arr_post, $id_usuario) {	
	
		$arr_resultado=array();
		$arr_resultado["codigo_resultado"]=1;
		$arr_resultado["msj_resultado"]="";
	
		$dbConsultasPterigio = new DbConsultasPterigio();
		$dbHistoriaClinica = new DbHistoriaClinica(); 
		$utilidades = new Utilidades();
		$uploader = new UploadHandler(); 
		$dbLoteArchivos = new DBRegistroArchivos(); 
		$dbArchivos = new DBArchivo(); 				
				
		@$id_hc = $arr_post["id_hc"];
		@$grado_od = $arr_post["pte_grado_od"];		
		@$ind_reproducido_od = $arr_post["pte_ind_reproducido_od"];
		@$mov_conjuntiva_sup_od = $arr_post["pte_conjuntiva_sup_od"];		
		@$ind_astigmatismo_ind_od = $arr_post["pte_ind_astigmatismo_od"];	
		@$grado_oi = $arr_post["pte_grado_oi"];
		@$ind_reproducido_oi = $arr_post["pte_ind_reproducido_oi"];
		@$mov_conjuntiva_sup_oi = $arr_post["pte_conjuntiva_sup_oi"];
		@$ind_astigmatismo_ind_oi = $arr_post["pte_ind_astigmatismo_oi"];
		@$observaciones = $utilidades->str_decode($arr_post["pte_observaciones"]); 
		//@$uploader_pte_od = json_decode($arr_post["uploader_pte_od"], false, 512, JSON_BIGINT_AS_STRING); 
		//@$uploader_pte_oi = json_decode($arr_post["uploader_pte_oi"], false, 512, JSON_BIGINT_AS_STRING); 
		
		$id_usuario = $_SESSION["idUsuario"];	
		
		// Guardar consulta de pterigio (datos diferentes a adjuntos) 
		$resultado = $dbConsultasPterigio->crearEditarConsultaPterigio($id_hc, 
				$grado_od, $ind_reproducido_od, $mov_conjuntiva_sup_od, $ind_astigmatismo_ind_od, 
				$grado_oi, $ind_reproducido_oi, $mov_conjuntiva_sup_oi, $ind_astigmatismo_ind_oi, $observaciones, $id_usuario); 
				
		if ($resultado<0) { 
			$arr_resultado["codigo_resultado"]=-1;
			$arr_resultado["msj_resultado"]="Error -6- al Crear/Editar Consulta Pterigio"; 
			return $arr_resultado; 			
		}		
		

		// Guardar/Procesar archivos adjuntos - Fotos Pterigio OD: 		
		// Se reciben parámetros: 
		$arr_uploader_pte_od = json_decode(@$arr_post["pte_foto_uploader_od"], true, 512, JSON_BIGINT_AS_STRING); 
		$dbLoteArchivos->id_tipo_archivo=@$arr_post["pte_foto_tipo_arch_od"]; 
		$dbLoteArchivos->id_reg_archivos = (@$arr_post["pte_foto_id_lote_od"]=="") ? -1 : $arr_post["pte_foto_id_lote_od"]; 
		$dbLoteArchivos->observaciones=@$arr_post["pte_foto_obs_lote_od"]; 
		$dbLoteArchivos->prefijo="pterigio_od"; 
		$dbLoteArchivos->id_hc=$id_hc; 
		
		// Se calcula directorio destino: 
		$directorio_origen="../tmp/uploads/".$id_usuario."/".$id_hc."/".$dbLoteArchivos->prefijo; 		
		$ruta_archivo_ficticio = $dbHistoriaClinica->construir_nombre_arch($id_hc, "nomarchi.ext", $dbLoteArchivos->prefijo, ""); 
		$directorio_destino = substr($ruta_archivo_ficticio, 0, strrpos($ruta_archivo_ficticio, "/", -1));		
		//echo "<br>pasar OI de directorio_origen=$directorio_origen ==> directorio_destino".$directorio_destino;  				
		
		// Se registra el lote y sus archivos: 
		$resultado=$uploader->registrarArchivosAdjuntos($arr_uploader_pte_od, $dbLoteArchivos, $directorio_origen, $directorio_destino, $id_usuario, "HC", $id_hc);  		
		
		
		// Guardar/Procesar archivos adjuntos - Fotos Pterigio OI: 		
		// Se reciben parámetros: 
		$arr_uploader_pte_oi = json_decode(@$arr_post["pte_foto_uploader_oi"], true, 512, JSON_BIGINT_AS_STRING); 
		$dbLoteArchivos->id_tipo_archivo=@$arr_post["pte_foto_tipo_arch_oi"]; 
		$dbLoteArchivos->id_reg_archivos = (@$arr_post["pte_foto_id_lote_oi"]=="") ? -1 : $arr_post["pte_foto_id_lote_oi"]; 
		$dbLoteArchivos->observaciones=@$arr_post["pte_foto_obs_lote_oi"]; 
		$dbLoteArchivos->prefijo="pterigio_oi"; 
		$dbLoteArchivos->id_hc=$id_hc; 
		
		// Se calcula directorio destino: 
		$directorio_origen="../tmp/uploads/".$id_usuario."/".$id_hc."/".$dbLoteArchivos->prefijo; 		
		$ruta_archivo_ficticio = $dbHistoriaClinica->construir_nombre_arch($id_hc, "nomarchi.ext", $dbLoteArchivos->prefijo, ""); 
		$directorio_destino = substr($ruta_archivo_ficticio, 0, strrpos($ruta_archivo_ficticio, "/", -1));
		//echo "<br>pasar OI de directorio_origen=$directorio_origen ==> directorio_destino".$directorio_destino;  
				
		// Se registra el lote y sus archivos: 
		$resultado=$uploader->registrarArchivosAdjuntos($arr_uploader_pte_oi, $dbLoteArchivos, $directorio_origen, $directorio_destino, $id_usuario, "HC", $id_hc); 
	
		return $arr_resultado;	
	}
	
	
	
	// Procesar acciones ajax: 
	
	if ($_POST) {
		$opcion=@$_POST["opcion"]; 
	} else {
		$opcion=@$_GET["opcion"]; 
	}
		
	if ($opcion<90) {
		$opcion_esta_pagina=0; 
	} else {
		$opcion_esta_pagina=$opcion;  	
	} 	
	
	$id_usuario = $_SESSION["idUsuario"]; 	
$opcion_esta_pagina=92;	
	switch ($opcion_esta_pagina) {			
		
		case "91": 
			/*
				Cargar/borrar archivo desde plugin FineUploader (carga al server; no registra en BD) 
				Como respuesta al plugin FineUploader sólo debe imprimirse un objeto JSON; si se imprime algo más el plugin carga el archivo pero en el front-end visualiza ERROR
			*/
			$respuesta_json=array();
			$respuesta_json["params"]=" opcion=opcion_esta_pagina(pterigio_ajax)=$opcion"; 	
			
			@$tipo_archivo_lote=$_POST["idTipoAdjunto"]; 
			@$id_hc = $_POST["idHc"]; 
			
			$uploader = new UploadHandler();			
			
			// Directorio del upload: 
			switch ($tipo_archivo_lote) { 
				case 1: 
					$uploader->inputName = "pte_foto_od"; 
					$prefijo_tarchivo = "pterigio_od"; 
					break; 
				case 2: $uploader->inputName = "pte_foto_oi"; 
					$prefijo_tarchivo = "pterigio_oi"; 
					break; 
				default: 
					$uploader->inputName = "qqfile"; 
					$prefijo_tarchivo = "qqfile"; 
					break; 
			} 
			
			$directorio="../tmp/uploads/".$id_usuario."/".$id_hc."/".$prefijo_tarchivo; 
			
			//Se guarda temporalmente el archivo: 
			$result=$uploader->procesarUploadFile($directorio/*, $nombre_archivo*/); 
			$respuesta_json=array_merge($result, $respuesta_json); 
			
			echo json_encode($respuesta_json);
		break; 
		
		case "92": 
			/*
				Cargar lista inicial de archivos para FineUploader
				Como respuesta al plugin FineUploader sólo debe imprimirse un objeto JSON
			*/
			$respuesta_json=array();
						
			$dbArchivos = new DBArchivo();			
			$uploader = new UploadHandler();			
			$archivos=array();
			$arr_params=array(); 
			
			//Obtener archivos guardados en BD: 
			$tipo_archivo_lote=1;//$_GET["idTipoAdjunto"];
			$id_hc_consulta=711; //169258; 711;//$_GET["idHc"]; 			
			$tabla_archivos = $dbArchivos->getArchivosHc($id_hc_consulta, $tipo_archivo_lote); 		
echo "<li>idHc = ".$id_hc_consulta;
			foreach ($tabla_archivos as $archivo) { 				
echo " file ";
				// Incluír el archivo en las variables de sesión para FineUploader				
				$arr_params["opcion"]=93;
				$arr_params["idHc"]=$id_hc_consulta;
				$arr_params["idArchivo"]=$archivo["id_archivo"]; 
				
				$thumbnailUrl=$uploader->getThumbnail($archivo["ruta"]);				
				
				array_push($archivos, 
					array ( 
						"uuid" => $archivo["id_archivo"], 
						"name" => basename($archivo["ruta"]), 
						"thumbnailUrl" => $thumbnailUrl=$uploader->getThumbnail($archivo["ruta"]), 						
						"deleteFileParams" => $arr_params 						
						//"size" => filesize($archivo["ruta"]) // no se envía para reconocer (en el GUARDAR) los archivos que ya están en BD
						//deleteFileEndpoint: String - Endpoint for the associated delete file request. If omitted, the deleteFile.endpoint is used.
					)
				);
			} 
			
			echo json_encode($archivos);
		break; 
		
		case "93": 
			/*
				Borrar archivo ya cargado y registrado
			*/
			$respuesta_json=array();		
			$dbArchivos = new DBArchivo();			
			
			@$tipo_archivo_lote=$_POST["idTipoAdjunto"]; 
			@$id_hc = $_POST["idHc"];
			@$id_archivo = $_POST["idArchivo"]; 
			
			$respuesta_json["success"]=true;
			
			// borrado de BD 
			$dbArchivos->id_archivo=$id_archivo; 
			$reg_archivo = $dbArchivos->getArchivo(); 			
			$resultado = $dbArchivos->BorrarArchivo($id_usuario); 
			
			// borrado físico 
			if ($resultado<0) {
				$respuesta_json["success"]=false;
			} else {				
				if ($resultado<0) {
					$respuesta_json["success"]=false;
				} else{
					unlink($reg_archivo["ruta"]);
				}
			}
			
			$respuesta_json["params"]=" opcion_esta_pagina=$opcion_esta_pagina - hc=$id_hc - id_archivo=$id_archivo"; 			
			$respuesta_json["accion"]="borrar de BD";
			
			echo json_encode($respuesta_json);			
		break; 		
	}	
?>