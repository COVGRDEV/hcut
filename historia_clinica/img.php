<?php
// El archivo
$nombre_archivo = 'C:/imagenes_hce_dev/2017/07/12/205/711_pterigio_od_6.jpg';
$porcentaje = 0.5;

// Tipo de contenido
header('Content-Type: image/jpeg');

// Obtener nuevas dimensiones
list($ancho, $alto) = getimagesize($nombre_archivo);
$nuevo_ancho = $ancho * $porcentaje;
$nuevo_alto = $alto * $porcentaje;

// Redimensionar
$imagen_p = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
$imagen = imagecreatefromjpeg($nombre_archivo);
imagecopyresampled($imagen_p, $imagen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);

// Imprimir
imagejpeg($imagen_p, null, 100);
imagejpeg($imagen_p, 'C:/imagenes_hce_dev/2017/07/12/205/thumbnail.jpg');
?>