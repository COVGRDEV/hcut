<?php
/*
  Pagina para marcar las citas como no atendidas
  Autor: Helio Ruber Lopez - 18/12/2013
*/
    require_once("../db/DbVariables.php");
    require_once("../db/DbCitas.php");
	$variables = new Dbvariables();
	$citas = new DbCitas();
	$id_usuario = $variables->getVariable(6);
	$citas->marcar_cita_no_atendida($id_usuario['valor_variable']);
?>