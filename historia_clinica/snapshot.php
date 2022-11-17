<?php session_start();

    $IdUsuario = $_SESSION["idUsuario"];
	// read input stream
	$data = file_get_contents("php://input");
	// filtering and decoding code adapted from  
	// Filter out the headers (data:,) part.
	$filteredData=substr($data, strpos($data, ",")+1);
	// Need to decode before saving since the data we received is already base64 encoded
	$decodedData=base64_decode($filteredData);
	
	// store in server
	$fic_name = $IdUsuario.'_oftalmologia'.rand(1000,9999).'.png';
	$fp = fopen('../imagenes/hc_temporal/'.$fic_name, 'wb');
	$ok = fwrite( $fp, $decodedData);
	fclose( $fp );
	
	if($ok)
		echo $fic_name;
	else
		echo "ERROR";
?>