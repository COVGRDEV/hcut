<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
header('Content-Type: text/xml; charset=UTF-8');
session_start();

require_once '../funciones/Utilidades.php';
require_once '../funciones/FuncionesPersona.php';

$utilidades = new Utilidades();

$opcion = $_POST["opcion"];

switch ($opcion) {
	case "10": //Carga de combo de planes
		require_once("../db/DbPlanes.php");
		require_once("../funciones/Class_Combo_Box.php");
		
		$dbPlanes = new DbPlanes();
		$combo = new Combo_Box();
		
		@$id_convenio = trim($utilidades->str_decode($_POST["id_convenio"]));
		
		//Se carga el listado de planes asociados al convenio
		$lista_planes = $dbPlanes->getListaPlanesActivos($id_convenio);
		$combo->getComboDb("cmbPlan", '', $lista_planes, "id_plan, nombre_plan", "Todos los planes", "", "", "width:250px;");
		break;
}
?>
