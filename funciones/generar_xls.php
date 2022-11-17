<?php
	header('Content-Type: text/xml; charset=ISO-8859-1');
	header('Content-Type: application/vnd.ms-excel');
	
	$nombre_archivo="archivo.xls";
	$cuerpo="";
	
	if (trim($_POST["hdd_nombre_archivo_xls"])!="") {
		$nombre_archivo=trim($_POST["hdd_nombre_archivo_xls"]);
	}
	if (trim($_POST["hdd_cuerpo_xls"])!="") {
		$cuerpo=trim($_POST["hdd_cuerpo_xls"]);
	}
	
	//response.setHeader("Content-Disposition", "attachment; filename=\"" + nombreArchivo + "\"");
	//response.setDateHeader("Expires", 0); //No guarda el caché en servidores Proxy
	header("Content-Disposition: attachment; filename=\"".$nombre_archivo."\"");
	header("Expires: 0"); //No guarda el caché en servidores Proxy
	
	echo($cuerpo);
?>
