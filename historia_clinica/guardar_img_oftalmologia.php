<?php session_start();
	require_once("../db/DbVariables.php");
	$variables = new Dbvariables();
	$fecha_hoy = $variables->getAnoMesDia();
	$anio = $fecha_hoy['anio_actual'];
	$mes = $fecha_hoy['mes_actual'];
	$dia = $fecha_hoy['dia_actual'];
    $IdUsuario = $_SESSION["idUsuario"];
	
	//Leer flujo de entrada
	$data = file_get_contents("php://input");
	
	//Filtrar las cabeceras (data:,) parte.
	$filteredData = substr($data, strpos($data, ",")+1);
	
	//Extraer el id de la cabecera de los datos
	$cadena_data = substr($data, 0, strpos($data, ",")+1);
	$array_datas = explode("|", $cadena_data);
	$tipo_oftalmologia = $array_datas[0];
	$id_paciente = $array_datas[1];
	$id_hc = $array_datas[2];
	
	//Necesidad de descifrar antes de guardar ya que los datos que hemos recibido ya está codificada en base64
	$decodedData = base64_decode($filteredData);
	$ruta = "../imagenes/imagenes_hce/".$anio."/".$mes."/".$dia."/".$id_paciente."/";
	
	if (!file_exists($ruta)) {
		mkdir($ruta, 0, true);
	}
	$fic_name = $id_hc.'_'.$tipo_oftalmologia.'.png';
	$ruta_archivo = $ruta.$fic_name;
	
	$fp = fopen($ruta_archivo, 'wb');
	$ok = fwrite( $fp, $decodedData);
	fclose($fp);
	
	if ($ok) {
		echo $ruta_archivo;
	} else {
		echo "ERROR";
	}
?>
