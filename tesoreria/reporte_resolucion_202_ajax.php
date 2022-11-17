<?php

header('Content-Type: text/xml; charset=UTF-8');
session_start();

require_once '../funciones/Utilidades.php';
require_once '../funciones/FuncionesPersona.php';

$utilidades = new Utilidades();

$opcion = $_POST["opcion"];

switch ($opcion) {
   		case "1":
		break;
}
?>
