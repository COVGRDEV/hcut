<?php
	$image = imagecreatefrompng($_POST['image']);
	$nombre_img = $_POST['nombre_img'];
	$id_paciente = $_POST['id_paciente'];
	
	require_once("../../../db/DbVariables.php");
	$variables = new Dbvariables();
	
	$fecha_hoy = $variables->getAnoMesDia();
	$anio = $fecha_hoy['anio_actual'];
	$mes = $fecha_hoy['mes_actual'];
	$dia = $fecha_hoy['dia_actual'];
	
	$arr_ruta_base = $variables->getVariable(17);
	$ruta = $arr_ruta_base["valor_variable"]."/".$anio."/".$mes."/".$dia."/".$id_paciente."/";
	
	if (!file_exists($ruta)) {
		mkdir($ruta, 0, true);
	}
	
	imagealphablending($image, false);
	imagesavealpha($image, true);
	imagepng($image, $ruta.$nombre_img.'.png');
	
	// return image path
	echo '{"img": "'.$ruta.$nombre_img.'.png"}';
?>
