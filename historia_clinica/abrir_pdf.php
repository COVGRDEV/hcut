<?php
 	$ruta = utf8_decode($_GET['ruta']);
	$nombre="historia_clinica.pdf";
	
	header("Content-type: application/pdf");
	header("Content-Disposition: inline; filename=".$nombre);
	header("Content-Transfer-Encoding: binary");
	header("Accept-Ranges: bytes");
	
	readfile($ruta);
?>
