<?php 
	equire_once("../db/DbMenus.php");
class EncabezadoPagina{
 		
 	
	private $conector= null;
	
	  public function __construct(){
	  $this->conector=Servidor_Base_Datos::conectar_base(Configuracion::$SERVIDOR, Configuracion::$USUARIOS, Configuracion::$PASS, Configuracion::$BASE_DATOS);	
	}
 	 public function enlaces()
 	 {
 	 	$url_pagina = explode("/", $_SERVER['REQUEST_URI']);
	    $pagina_actual=$url_pagina[2];
	    $perfil_usuario=$_SESSION["IdPerfil"];
	    $tabla_menu = $this->tabla_menu();
	    
	   echo'<div id="menu">
             <ul class="level1" id="root">';
	    foreach($tabla_menu as $fila_menu){
			$id_menu=$fila_menu[0];
			$nombre_menu=$fila_menu[1];
			$nombre_pagina=$fila_menu[2];
			$id_menu_padre=$fila_menu[3];
			if($id_menu_padre == ' '){
			    echo'<li>
					  <a href="#">'.$nombre_menu.'</a>';
				$tabla_hijo=$this->obtener_hijo($tabla_menu, $id_menu);
				echo'<ul class="level2">';   
				foreach($tabla_hijo as $fila_hijo){
				    $id_menu_hijo=$fila_hijo[0];
					$nombre_menu_hijo=$fila_hijo[1];
					$nombre_pagina_hijo=$fila_hijo[2];
					$id_menu_padre_hijo=$fila_hijo[3];
					$tipo_permiso_hijo=$fila_hijo[4];
					
					if($nombre_pagina_hijo == ' '){
					  echo'<li><a href="#">'.$nombre_menu_hijo.'</a>';
					  
					    $tabla_hijo_2=$this->obtener_hijo($tabla_menu, $id_menu_hijo);
						echo'<ul class="level3">';   
						foreach($tabla_hijo_2 as $fila_hijo_2){
						    $id_menu_hijo_2=$fila_hijo_2[0];
							$nombre_menu_hijo_2=$fila_hijo_2[1];
							$nombre_pagina_hijo_2=$fila_hijo_2[2];
							$id_menu_padre_hijo_2=$fila_hijo_2[3];
							$tipo_permiso_hijo_2=$fila_hijo_2[4];
						    //echo'<li><a href="'.$nombre_pagina_hijo_2.'?tipo_permiso='.$tipo_permiso_hijo_2.'">'.$nombre_menu_hijo_2.'</a></li>';
						    echo'<li><a href="'.$nombre_pagina_hijo_2.'">'.$nombre_menu_hijo_2.'</a></li>';
						}
						echo'</ul>';   
					  echo'</li>';
					}
					else{
					  //echo'<li><a href="'.$nombre_pagina_hijo.'?tipo_permiso='.$tipo_permiso_hijo.'">'.$nombre_menu_hijo.'</a></li>';
					  echo'<li><a href="'.$nombre_pagina_hijo.'">'.$nombre_menu_hijo.'</a></li>';
					}
				}
				echo'</ul>';
				echo'</li>';
				echo'<li class="sep">|</li>';
			}
	    }
	    echo'<li><a href="pw_cambio.php">Cambiar contrase&ntilde;a</a></li>';
	    echo'<li><a href="Logout.php">Salir</a></li>';
	        
	    echo'</ul>
			 </div><br />';
		    
 	 }
 	
	private function tabla_menu() {
 	    $perfil_usuario=1;
 	    $menu=array();
		$dbMenus = new DbMenus();
		$tabla_menu_principal = $dbMenus->getListaMenus($perfil_usuario);
		
	    $i=0;
	    foreach($tabla_menu_principal as $fila_menu_principal){
			$id_menu=$fila_menu_principal[0];
			$nombre_menu=$fila_menu_principal[1];
			$nombre_pagina=$fila_menu_principal[2];
			$id_menu_padre=$fila_menu_principal[3];
			$tipo_permiso=$fila_menu_principal[4];
			$menu[$i][0]=$id_menu;
			$menu[$i][1]=$nombre_menu;
			$menu[$i][2]=$nombre_pagina;
			$menu[$i][3]=$id_menu_padre;
			$menu[$i][4]=$tipo_permiso;
			$i=$i+1;
	    }
	    
	    return $menu;
 	}
 	
 	
 	private function obtener_hijo($tabla_menu, $id_papa){
 	
 	  $tabla_hijo=array();
 	  $i=0;
 	  foreach($tabla_menu as $fila_menu){
		 $id_menu=$fila_menu[0];
		 $nombre_menu=$fila_menu[1];
		 $nombre_pagina=$fila_menu[2];
		 $id_menu_padre=$fila_menu[3];
		 $tipo_permiso=$fila_menu[4];
		 //echo $id_menu.' - '.$nombre_menu.' - '.$nombre_pagina.' - '.$id_menu_padre.'<br />';
		 //echo $id_papa.'=='.$id_menu_padre.'<br/>';
		 if($id_papa==$id_menu_padre){
		     $tabla_hijo[$i][0]=$id_menu;
			 $tabla_hijo[$i][1]=$nombre_menu;
			 $tabla_hijo[$i][2]=$nombre_pagina;
			 $tabla_hijo[$i][3]=$id_menu_padre;
			 $tabla_hijo[$i][4]=$tipo_permiso;
			 $i=$i+1;
		 }
	  }
	  return $tabla_hijo;
 	
 	}
 	
    public function logo(){
       echo'<div class="logo_sitio">
	       <img style="margin:0 0 0 0;" src="'.Configuracion::$LOGO2_SITIO.'" alt="Observatorio de salud P&uacute;blica"/>
	       <img style="margin:0 0 0 40em;" src="'.Configuracion::$LOGO1_SITIO.'" alt="Gobernaci&oacute;n de Santander"/>
	       <div class="titulo_sistema">Sistema de informaci&oacute;n de Salud Oral</div> 
	       </div>';	 
 	 }
 	 
     public function logo_index(){
       echo'<div class="logo_sitio_index">
	       <img style="margin:0 0 0 0;" src="'.Configuracion::$LOGO2_SITIO.'" alt="Observatorio de salud P&uacute;blica"/>
	       <img style="margin:0 0 0 35em;" src="'.Configuracion::$LOGO1_SITIO.'" alt="Gobernaci&oacute;n de Santander"/>
	       <div class="titulo_sistema" style="font-size:25px;" >Sistema de informaci&oacute;n de Salud Oral</div> 
	       </div>';	 
 	 }
 	
	
	
 	 
 }
?>