<?php
require_once("BaseDatos/Servidor_Base_Datos.php");


 class Combo_Box{
 	//VARIABLES DE LA CLASE
 	private $conector;
	private $nombre_calendario_seleccionado;
 	/**
 	 * FUNCION DE LA CLASE
 	 **/
 	function __construct(){
 		$this->conector=Servidor_Base_Datos::conectar_base($this->servidor, $this->usuario, $this->pass, $this->base_datos);
 	}
 	 public function get($nombre, $activo, $sql, $mensaje, $al_cambio = 0, $activo_combo='0'){
 	 	$select=$sql;
		
 	 	$tabla=$this->conector->select("$select");
 	 	$i=0;
 	 	$j=0;
		if ($activo_combo == "disabled"){
			echo"<SELECT id='$nombre' name=\"$nombre\" onChange='$al_cambio' disabled=\"$activo_combo\" >";
		}
		else{
		    echo"<SELECT id='$nombre' name=\"$nombre\" onChange='$al_cambio' >";	
		}
		if ($mensaje != ''){
		  echo'<option value=0> '.$mensaje.'  ';
		} 
 	 	while($tabla[$i][0]){
 	 		$id=$tabla[$i][0];
 	 		$nombre=$tabla[$i][1];
 	 		if($id==$activo){
 	 			echo"<OPTION VALUE=\"$id\" selected='selected'>$nombre<br>";
 	 		}
 	 		else{
 	 			echo"<OPTION VALUE=\"$id\">$nombre<br>";
 	 		}
 	 		$i++;
 	 	}
 	 	echo"</SELECT>";
 	 }
 	
 }
?>
