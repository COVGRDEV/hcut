<?php 

    require_once("BaseDatos/Servidor_Base_Datos.php");
	require_once("Class_Combo_Box.php");
	require_once("Configuracion.php");

class Estructura_Pagina{
 	//VARIABLES DE LA CLASE
 	
 	/**
 	 * FUNCION DE LA CLASE
 	 **/
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
	    $nombre_usuario=$_SESSION['NombreUsuario'];
	    $nombre_municipio=$_SESSION['NombreMunicipio'];
		$id_ips = $_SESSION['IdIpsMuncipio'];
		$nombre_ips = $_SESSION['NombreIpsMuncipio'];
		if($id_ips > 0){
			echo'<li style="float:right;"><a href="#"><b>IPS: ['.$nombre_ips.']</b></a></li>';
		}
		echo'<li style="float:right;"><a href="#"><b>Muncipio: ['.$nombre_municipio.']</b></a></li>';
	    echo'<li style="float:right;"><a href="#"><b>Usuario: ['.$nombre_usuario.']</b></a></li>';
		    
	    echo'</ul>
			 </div><br />';
		    
 	 }
 	
	private function tabla_menu() {
 	    $perfil_usuario=$_SESSION["IdPerfil"];
 	    $menu=array();
 	    $sql_menu_principal="SELECT m.id_menu, m.nombre_menu, m.nombre_pagina, id_menu_padre, p.tipo_permiso
							 FROM menu m
							 LEFT JOIN permisos p ON p.id_menu = m.id_menu
							 WHERE m.id_menu IN (SELECT DISTINCT m1.id_menu_padre
									             FROM menu m1
									             INNER JOIN permisos p1 ON p1.id_menu = m1.id_menu
									             WHERE p1.id_perfil = $perfil_usuario)
							 OR p.id_perfil = $perfil_usuario		    
							 ORDER BY m.orden, m.id_menu, m.id_menu_padre";
	    $tabla_menu_principal=$this->conector->select($sql_menu_principal);
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
 	
    /**
     PARAMETROS:
	 $fecha_nacimiento - Fecha de nacimiento de una persona.
	 $fecha_control - Fecha actual o fecha a consultar.
	 EJEMPLO:
	 tiempo_transcurrido('22/06/1977', '04/05/2009');
    */
 	public function calcular_edad($fecha_nacimiento, $fecha_control){
       $fecha_actual = $fecha_control;
   
	   if(!strlen($fecha_actual))
	   {
	      $fecha_actual = date('d/m/Y');
	   }

	   	// separamos en partes las fechas 
	   $array_nacimiento = explode ( "/", $fecha_nacimiento ); 
	   $array_actual = explode ( "/", $fecha_actual ); 
	
	   $anos =  $array_actual[2] - $array_nacimiento[2]; // calculamos a�os 
	   $meses = $array_actual[1] - $array_nacimiento[1]; // calculamos meses 
	   $dias =  $array_actual[0] - $array_nacimiento[0]; // calculamos d�as 

	   //ajuste de posible negativo en $d�as 
	   if ($dias < 0) 
	   { 
	      --$meses; 
	
	      //ahora hay que sumar a $dias los dias que tiene el mes anterior de la fecha actual 
	      switch ($array_actual[1]) { 
	         case 1: 
	            $dias_mes_anterior=31;
	            break; 
	         case 2:     
	            $dias_mes_anterior=31;
	            break; 
	         case 3:  
	            if ($this->bisiesto($array_actual[2])) 
	            { 
	               $dias_mes_anterior=29;
	               break; 
	            } 
	            else 
	            { 
	               $dias_mes_anterior=28;
	               break; 
	            } 
	         case 4:
	            $dias_mes_anterior=31;
	            break; 
	         case 5:
	            $dias_mes_anterior=30;
	            break; 
	         case 6:
	            $dias_mes_anterior=31;
	            break; 
	         case 7:
	            $dias_mes_anterior=30;
	            break; 
	         case 8:
	            $dias_mes_anterior=31;
	            break; 
	         case 9:
	            $dias_mes_anterior=31;
	            break; 
	         case 10:
	            $dias_mes_anterior=30;
	            break; 
	         case 11:
	            $dias_mes_anterior=31;
	            break; 
	         case 12:
	            $dias_mes_anterior=30;
	            break; 
	      } 
	
	      $dias=$dias + $dias_mes_anterior;
	
	      if ($dias < 0)
	      {
	         --$meses;
	         if($dias == -1)
	         {
	            $dias = 30;
	         }
	         if($dias == -2)
	         {
	            $dias = 29;
	         }
	      }
	   }

	   //ajuste de posible negativo en $meses 
	   if ($meses < 0) 
	   { 
	      --$anos; 
	      $meses=$meses + 12; 
	   }
	
	   $tiempo[0] = $anos;
	   $tiempo[1] = $meses;
	   $tiempo[2] = $dias;
	
	   return $tiempo;
	}

	public function bisiesto($anio_actual){ 
	   $bisiesto=false; 
	   //probamos si el mes de febrero del a�o actual tiene 29 d�as 
	   if(checkdate(2,29,$anio_actual)) 
	   { 
	      $bisiesto=true; 
	   } 
	   return $bisiesto; 
	}
	
	
	
	/*
	 * Muestra el odontograma del ipb para egregar o para mostrar resultados
	 * $id_encuesta = id de la encuesta
	 * $tipo = tipo de acccion
	 * 1 = vista agregar
	 * 2 = vista informacion
	 * 
	 * $sufijo = un sufijo delante de las variables para diferecniar 
	 * 1 = NO tiene sufijo
	 * 2 = SI tiene sufijo
	 */
	public function odontograma_ipb($id_encuesta, $tipo, $sufijo){
			
		if($sufijo==2){
			$sufijo="ver";
		}
		else{
			$sufijo="";
		}
		
		if($id_encuesta == -1){
			//Variables para los dientes IPB
			$diente_primer_cuad=-1;
			$diente_sect_anterior=-1;
			$diente_superior_izq=-1;
			$diente_segundo_cuad=-1;
			$diente_cuarto_cuad=-1;
			$diente_inferior_der=-1;
			$diente_tercer_cuad=-1;
			$pri_cua_d = ''; $pri_cua_v = ''; $pri_cua_o = ''; $pri_cua_p = ''; $pri_cua_m = ''; $d1151_d = ''; $d1151_v = ''; $d1151_p = ''; $d1151_m = ''; 
			$d2363_m = ''; $d2363_v = ''; $d2363_p = ''; $d2363_d = ''; $segu_cua_m = ''; $segu_cua_v = ''; $segu_cua_o = ''; $segu_cua_p = ''; $segu_cua_d = ''; 
			$terc_cua_d = ''; $terc_cua_v = ''; $terc_cua_o = ''; $terc_cua_l = ''; $terc_cua_m = ''; $d4484_m = ''; $d4484_v = ''; $d4484_o = ''; $d4484_l = ''; $d4484_d = '';
			$cuat_cua_m = ''; $cuat_cua_v = ''; $cuat_cua_o = ''; $cuat_cua_l = ''; $cuat_cua_d = '';
		}else{	
			$sql_ipb="SELECT * FROM valoracion_ipb WHERE id_visitas_valoracion = $id_encuesta";
		    $tabla_ipb=$this->conector->select($sql_ipb);	
		    $diente_primer_cuad= $tabla_ipb[0][1]; if($diente_primer_cuad > 0){?><script id='ajax'>activar_check_ipb(1, 1, '<?php echo($sufijo);?>');</script><?php } 
			$pri_cua_d= $tabla_ipb[0][2]; if($pri_cua_d == 1){?><script id='ajax'>activar_caras_ipb('pri_cua_d<?php echo($sufijo);?>', 1);</script><?php }
			$pri_cua_v= $tabla_ipb[0][3]; if($pri_cua_v == 1){?><script id='ajax'>activar_caras_ipb('pri_cua_v<?php echo($sufijo);?>', 1);</script><?php }
			$pri_cua_o= $tabla_ipb[0][4]; if($pri_cua_o == 1){?><script id='ajax'>activar_caras_ipb('pri_cua_o<?php echo($sufijo);?>', 1);</script><?php }
			$pri_cua_p= $tabla_ipb[0][5]; if($pri_cua_p == 1){?><script id='ajax'>activar_caras_ipb('pri_cua_p<?php echo($sufijo);?>', 1);</script><?php }
			$pri_cua_m= $tabla_ipb[0][6]; if($pri_cua_m == 1){?><script id='ajax'>activar_caras_ipb('pri_cua_m<?php echo($sufijo);?>', 1);</script><?php }
			$diente_sect_anterior= $tabla_ipb[0][7]; if($diente_sect_anterior > 0){?><script id='ajax'>activar_check_ipb(2, 1, '<?php echo($sufijo);?>');</script><?php } 
			$d1151_d= $tabla_ipb[0][8]; if($d1151_d == 1){?><script id='ajax'>activar_caras_ipb('1151_d<?php echo($sufijo);?>', 1);</script><?php }
			$d1151_v= $tabla_ipb[0][9]; if($d1151_v == 1){?><script id='ajax'>activar_caras_ipb('1151_v<?php echo($sufijo);?>', 1);</script><?php }
			$d1151_p= $tabla_ipb[0][10]; if($d1151_p == 1){?><script id='ajax'>activar_caras_ipb('1151_p<?php echo($sufijo);?>', 1);</script><?php }
			$d1151_m= $tabla_ipb[0][11]; if($d1151_m == 1){?><script id='ajax'>activar_caras_ipb('1151_m<?php echo($sufijo);?>', 1);</script><?php }
			$diente_superior_izq= $tabla_ipb[0][12]; if($diente_superior_izq > 0){?><script id='ajax'>activar_check_ipb(3, 1, '<?php echo($sufijo);?>');</script><?php } 
			$d2363_m= $tabla_ipb[0][13]; if($d2363_m == 1){?><script id='ajax'>activar_caras_ipb('2363_m<?php echo($sufijo);?>', 1);</script><?php }
			$d2363_v= $tabla_ipb[0][14]; if($d2363_v == 1){?><script id='ajax'>activar_caras_ipb('2363_v<?php echo($sufijo);?>', 1);</script><?php }
			$d2363_p= $tabla_ipb[0][15]; if($d2363_p == 1){?><script id='ajax'>activar_caras_ipb('2363_p<?php echo($sufijo);?>', 1);</script><?php }
			$d2363_d= $tabla_ipb[0][16]; if($d2363_d == 1){?><script id='ajax'>activar_caras_ipb('2363_d<?php echo($sufijo);?>', 1);</script><?php }
			$diente_segundo_cuad= $tabla_ipb[0][17]; if($diente_segundo_cuad > 0){?><script id='ajax'>activar_check_ipb(4, 1, '<?php echo($sufijo);?>');</script><?php } 
			$segu_cua_m= $tabla_ipb[0][18]; if($segu_cua_m == 1){?><script id='ajax'>activar_caras_ipb('segu_cua_m<?php echo($sufijo);?>', 1);</script><?php }
			$segu_cua_v= $tabla_ipb[0][19]; if($segu_cua_v == 1){?><script id='ajax'>activar_caras_ipb('segu_cua_v<?php echo($sufijo);?>', 1);</script><?php }
			$segu_cua_o= $tabla_ipb[0][20]; if($segu_cua_o == 1){?><script id='ajax'>activar_caras_ipb('segu_cua_o<?php echo($sufijo);?>', 1);</script><?php }
			$segu_cua_p= $tabla_ipb[0][21]; if($segu_cua_p == 1){?><script id='ajax'>activar_caras_ipb('segu_cua_p<?php echo($sufijo);?>', 1);</script><?php }
			$segu_cua_d= $tabla_ipb[0][22]; if($segu_cua_d == 1){?><script id='ajax'>activar_caras_ipb('segu_cua_d<?php echo($sufijo);?>', 1);</script><?php }
			$diente_tercer_cuad= $tabla_ipb[0][23]; if($diente_tercer_cuad > 0){?><script id='ajax'>activar_check_ipb(5, 1, '<?php echo($sufijo);?>');</script><?php } 
			$terc_cua_d= $tabla_ipb[0][24]; if($terc_cua_d == 1){?><script id='ajax'>activar_caras_ipb('terc_cua_d<?php echo($sufijo);?>', 1);</script><?php }
			$terc_cua_v= $tabla_ipb[0][25]; if($terc_cua_v == 1){?><script id='ajax'>activar_caras_ipb('terc_cua_v<?php echo($sufijo);?>', 1);</script><?php }
			$terc_cua_o= $tabla_ipb[0][26]; if($terc_cua_o == 1){?><script id='ajax'>activar_caras_ipb('terc_cua_o<?php echo($sufijo);?>', 1);</script><?php } 
			$terc_cua_l= $tabla_ipb[0][27]; if($terc_cua_l == 1){?><script id='ajax'>activar_caras_ipb('terc_cua_l<?php echo($sufijo);?>', 1);</script><?php }
			$terc_cua_m= $tabla_ipb[0][28]; if($terc_cua_m == 1){?><script id='ajax'>activar_caras_ipb('terc_cua_m<?php echo($sufijo);?>', 1);</script><?php }
			$diente_inferior_der= $tabla_ipb[0][29]; if($diente_inferior_der > 0){?><script id='ajax'>activar_check_ipb(6, 1, '<?php echo($sufijo);?>');</script><?php } 
			$d4484_m= $tabla_ipb[0][30]; if($d4484_m == 1){?><script id='ajax'>activar_caras_ipb('4484_m<?php echo($sufijo);?>', 1);</script><?php }
			$d4484_v= $tabla_ipb[0][31]; if($d4484_v == 1){?><script id='ajax'>activar_caras_ipb('4484_v<?php echo($sufijo);?>', 1);</script><?php }
			$d4484_o= $tabla_ipb[0][32]; if($d4484_o == 1){?><script id='ajax'>activar_caras_ipb('4484_o<?php echo($sufijo);?>', 1);</script><?php }
			$d4484_l= $tabla_ipb[0][33]; if($d4484_l == 1){?><script id='ajax'>activar_caras_ipb('4484_l<?php echo($sufijo);?>', 1);</script><?php }
			$d4484_d= $tabla_ipb[0][34]; if($d4484_d == 1){?><script id='ajax'>activar_caras_ipb('4484_d<?php echo($sufijo);?>', 1);</script><?php }
			$diente_cuarto_cuad= $tabla_ipb[0][35]; if($diente_cuarto_cuad > 0){?><script id='ajax'>activar_check_ipb(7, 1, '<?php echo($sufijo);?>');</script><?php } 
			$cuat_cua_m= $tabla_ipb[0][36]; if($cuat_cua_m == 1){?><script id='ajax'>activar_caras_ipb('cuat_cua_m<?php echo($sufijo);?>', 1);</script><?php }
			$cuat_cua_v= $tabla_ipb[0][37]; if($cuat_cua_v == 1){?><script id='ajax'>activar_caras_ipb('cuat_cua_v<?php echo($sufijo);?>', 1);</script><?php }
			$cuat_cua_o= $tabla_ipb[0][38]; if($cuat_cua_o == 1){?><script id='ajax'>activar_caras_ipb('cuat_cua_o<?php echo($sufijo);?>', 1);</script><?php }
			$cuat_cua_l= $tabla_ipb[0][39]; if($cuat_cua_l == 1){?><script id='ajax'>activar_caras_ipb('cuat_cua_l<?php echo($sufijo);?>', 1);</script><?php }
			$cuat_cua_d= $tabla_ipb[0][40];	if($cuat_cua_d == 1){?><script id='ajax'>activar_caras_ipb('cuat_cua_d<?php echo($sufijo);?>', 1);</script><?php }
		}
		
		
		$combo=new Combo_Box();
		$array_primer_cuad[0][0]=17; $array_primer_cuad[0][1]=17; $array_primer_cuad[1][0]=16; $array_primer_cuad[1][1]=16; $array_primer_cuad[2][0]=15; $array_primer_cuad[2][1]=15;
		$array_primer_cuad[3][0]=14; $array_primer_cuad[3][1]=14; $array_primer_cuad[4][0]=55; $array_primer_cuad[4][1]=55; $array_primer_cuad[5][0]=54; $array_primer_cuad[5][1]=54;
		$array_primer_cuad[6][0]=0; $array_primer_cuad[6][1]='Ausente';
					   
		$array_sect_anterior[0][0]=11; $array_sect_anterior[0][1]=11; $array_sect_anterior[1][0]=12; $array_sect_anterior[1][1]=12; $array_sect_anterior[2][0]=13; $array_sect_anterior[2][1]=13;
		$array_sect_anterior[3][0]=51; $array_sect_anterior[3][1]=51; $array_sect_anterior[4][0]=52; $array_sect_anterior[4][1]=52; $array_sect_anterior[5][0]=53; $array_sect_anterior[5][1]=53;
		$array_sect_anterior[6][0]=0; $array_sect_anterior[6][1]='Ausente';
					   
		$array_superior_izq[0][0]=23; $array_superior_izq[0][1]=23; $array_superior_izq[1][0]=63; $array_superior_izq[1][1]=63;
		$array_superior_izq[2][0]=0; $array_superior_izq[2][1]='Ausente'; $array_superior_izq[3][0]=-2; $array_superior_izq[3][1]='Dientes no indice';
					   
		$array_segundo_cuad[0][0]=27; $array_segundo_cuad[0][1]=27; $array_segundo_cuad[1][0]=26; $array_segundo_cuad[1][1]=26; $array_segundo_cuad[2][0]=25; $array_segundo_cuad[2][1]=25;
		$array_segundo_cuad[3][0]=24; $array_segundo_cuad[3][1]=24; $array_segundo_cuad[4][0]=65; $array_segundo_cuad[4][1]=65; $array_segundo_cuad[5][0]=64; $array_segundo_cuad[5][1]=64;
		$array_segundo_cuad[6][0]=0; $array_segundo_cuad[6][1]='Ausente';
					   
		$array_cuarto_cuad[0][0]=47; $array_cuarto_cuad[0][1]=47; $array_cuarto_cuad[1][0]=46; $array_cuarto_cuad[1][1]=46; $array_cuarto_cuad[2][0]=45; $array_cuarto_cuad[2][1]=45;
		$array_cuarto_cuad[3][0]=85; $array_cuarto_cuad[3][1]=85; $array_cuarto_cuad[4][0]=0; $array_cuarto_cuad[4][1]='Ausente';
					   
		$array_inferior_dere[0][0]=44; $array_inferior_dere[0][1]=44; $array_inferior_dere[1][0]=84; $array_inferior_dere[1][1]=84;
		$array_inferior_dere[2][0]=0; $array_inferior_dere[2][1]='Ausente'; $array_inferior_dere[3][0]=-2; $array_inferior_dere[3][1]='Dientes no indice';
					   
		$array_tercer_cuad[0][0]=37; $array_tercer_cuad[0][1]=37; $array_tercer_cuad[1][0]=36; $array_tercer_cuad[1][1]=36; $array_tercer_cuad[2][0]=35; $array_tercer_cuad[2][1]=35;
		$array_tercer_cuad[3][0]=34; $array_tercer_cuad[3][1]=34; $array_tercer_cuad[4][0]=75; $array_tercer_cuad[4][1]=75; $array_tercer_cuad[5][0]=74; $array_tercer_cuad[5][1]=74;
		$array_tercer_cuad[6][0]=0; $array_tercer_cuad[6][1]='Ausente'; $array_tercer_cuad[7][0]=-2; $array_tercer_cuad[7][1]='Dientes no indice';
			
		?>
		<table border="0" cellpadding="0" cellspacing="0" align="center" style="width:60em;">
		<tr>
			<th class='titulo_diente_sup_izq' style="width:20em;">
				&Uacute;ltimo molar <br /> 1er cuadrante <br/> 
				<?php
				if($tipo == 1){ 
					$combo->get('diente_primer_cuad', $diente_primer_cuad, $array_primer_cuad, '&nbsp', 'validar_ipb_diente_p(this.id, \'pri_cua_\');', '', 'width:5em;', '57');
				}
				else{
					echo "<br />".$diente_primer_cuad;
				} 
				?>
			</th>
			<th class='titulo_diente_int_izq' style="width:20em;">
				Sector anterior <br /> 11 / 51 <br />
				<?php
				if($tipo == 1){
					$combo->get('diente_sect_anterior', $diente_sect_anterior, $array_sect_anterior, '&nbsp', 'validar_ipb_diente(this.id, \'diente_primer_cuad\', \'1151_\' );', '', 'width:5em;', '58');
				}
				else{
				    echo "<br />".$diente_sect_anterior;
				} 
				?>
			</th>
			<th class='diente_div'>&nbsp;</th>
			<th class='titulo_diente_int_der' style="width:20em;">
				Canino superior izquierdo <br /> 23 / 63 <br />
				<?php 
				if($tipo == 1){
					echo"<div id='div_diente_superior_izq'>";
					$combo->get('diente_superior_izq', $diente_superior_izq, $array_superior_izq, '&nbsp', 'validar_ipb_diente(this.id, \'diente_sect_anterior\', \'2363_\' );', '', 'width:5em;', '59');
					echo"</div>";
			 	}
				else{
				    echo "<br />".$diente_superior_izq;
				}
				?> 
				
			</th>
			<th class='titulo_diente_sup_der' style="width:20em;">
				&Uacute;ltimo molar <br /> 2do cuadrante <br />
				<?php 
				if($tipo == 1){
					$combo->get('diente_segundo_cuad', $diente_segundo_cuad, $array_segundo_cuad, '&nbsp', 'validar_ipb_diente(this.id, \'diente_superior_izq\', \'segu_cua_\' );', '', 'width:5em;', '60');
				}
				else{
				    echo "<br />".$diente_segundo_cuad;
				}
				?>
			</th>
		</tr>
		<tr>
			<th class='diente_borde_izq' align="center" valign="middle" >
				<br />
				<div class="d_diente">
				<table border="0" cellpadding="0" cellspacing="0" align="center">
				<tr>
					<td valign="bottom" align="center" colspan='3'><div class='d_superior' id='c_pri_cua_v<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("v", "pri_cua_v", 1, "diente_primer_cuad")' <?php } ?> ></div></td>
				</tr>
				<tr>
					<td align="right" ><div class='d_izquierda' id='c_pri_cua_d<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("d", "pri_cua_d", 1, "diente_primer_cuad")' <?php } ?> ></div></td>
					<td class='d_tabla'><div class='d_centro' id='c_pri_cua_o<?php echo($sufijo);?>'<?php if($tipo == 1){?> onclick='seleccionar_diente("o", "pri_cua_o", 1, "diente_primer_cuad")' <?php } ?> ></div></td>
					<td align="left"><div class='d_derecha' id='c_pri_cua_m<?php echo($sufijo);?>'<?php if($tipo == 1){?> onclick='seleccionar_diente("m", "pri_cua_m", 1, "diente_primer_cuad")' <?php } ?> ></div></td>
				</tr>
				<tr>
					<td valign="top" align="center" colspan='3'><div class='d_inferior' id='c_pri_cua_p<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("p", "pri_cua_p", 1, "diente_primer_cuad")' <?php } ?> ></div></td>
				</tr>
				</table>
				</div>
				<br />
			</th>
			<th class='diente_borde_der' align="center" valign="middle" >
				<br />
				<div class="d_diente">
				<table border="0" cellpadding="0" cellspacing="0" align="center">
				<tr>
					<td valign="bottom" align="center" colspan='3'><div class='d_superior' id='c_1151_v<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("v", "1151_v", 1, "diente_sect_anterior")' <?php } ?> ></div></td>
				</tr>
				<tr>
					<td align="right" ><div class='d_izquierda' id='c_1151_d<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("d", "1151_d", 1, "diente_sect_anterior")' <?php } ?> ></div></td>
					<td class='d_tabla'><div class='d_centro'></div></td>
					<td align="left"><div class='d_derecha' id='c_1151_m<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("m", "1151_m", 1, "diente_sect_anterior")' <?php } ?> ></div></td>
				</tr>
				<tr>
					<td valign="top" align="center" colspan='3'><div class='d_inferior' id='c_1151_p<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("p", "1151_p", 1, "diente_sect_anterior")' <?php } ?> ></div></td>
				</tr>
				</table>	
				</div>
				<br />
			</th>
			<th class='diente_div'>&nbsp;</th>
			<th class='diente_borde_izq' align="center" valign="middle">
				<br />
				<div class="d_diente">
				<table border="0" cellpadding="0" cellspacing="0" align="center">
				<tr>
					<td valign="bottom" align="center" colspan='3'><div class='d_superior' id='c_2363_v<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("v", "2363_v", 1, "diente_superior_izq")' <?php } ?> ></div></td>
				</tr>
				<tr>
					<td align="right" ><div class='d_izquierda' id='c_2363_m<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("m", "2363_m", 1, "diente_superior_izq")' <?php } ?> ></div></td>
					<td class='d_tabla'><div class='d_centro'></div></td>
					<td align="left"><div class='d_derecha' id='c_2363_d<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("d", "2363_d", 1, "diente_superior_izq")' <?php } ?> ></div></td>
				</tr>
				<tr>
					<td valign="top" align="center" colspan='3'><div class='d_inferior' id='c_2363_p<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("p", "2363_p", 1, "diente_superior_izq")' <?php } ?> ></div></td>
				</tr>
				</table>	
				</div>
				<br />
			</th>
			<th class='diente_borde_der' align="center" valign="middle">
				<br />
				<div class="d_diente">
				<table border="0" cellpadding="0" cellspacing="0" align="center">
				<tr>
					<td valign="bottom" align="center" colspan='3'><div class='d_superior' id='c_segu_cua_v<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("v", "segu_cua_v", 1, "diente_segundo_cuad")' <?php } ?> ></div></td>
				</tr>
				<tr>
					<td align="right" ><div class='d_izquierda' id='c_segu_cua_m<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("m", "segu_cua_m", 1, "diente_segundo_cuad")' <?php } ?> ></div></td>
					<td class='d_tabla'><div class='d_centro' id='c_segu_cua_o<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("o", "segu_cua_o", 1, "diente_segundo_cuad")' <?php } ?> ></div></td>
					<td align="left"><div class='d_derecha' id='c_segu_cua_d<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("d", "segu_cua_d", 1, "diente_segundo_cuad")' <?php } ?> ></div></td>
				</tr> 
				<tr>
					<td valign="top" align="center" colspan='3'><div class='d_inferior' id='c_segu_cua_p<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("p", "segu_cua_p", 1, "diente_segundo_cuad")' <?php } ?> ></div></td>
				</tr>
				</table>	
				</div>
				<br />
			</th>
		</tr>
		<tr>
			<th class='titulo_diente_borde' >D &nbsp;&nbsp;&nbsp; V &nbsp;&nbsp;&nbsp; O &nbsp;&nbsp;&nbsp; P &nbsp;&nbsp;&nbsp; M</th>
			<th class='titulo_diente_borde' >D &nbsp;&nbsp;&nbsp; V &nbsp;&nbsp;&nbsp; P &nbsp;&nbsp;&nbsp; M</th>
			<th class='diente_div'>&nbsp;</th>
			<th class='titulo_diente_borde'>M &nbsp;&nbsp;&nbsp; V &nbsp;&nbsp;&nbsp; P &nbsp;&nbsp;&nbsp; D</th>
			<th class='titulo_diente_borde'>M &nbsp;&nbsp;&nbsp; V &nbsp;&nbsp;&nbsp; O &nbsp;&nbsp;&nbsp; P &nbsp;&nbsp;&nbsp; D</th>
		</tr>
		<tr>
		<?php
		if($tipo == 1){
		?>	
			<th class='titulo_diente_borde' >
				<input type="checkbox" value="" id="pri_cua_d" name="pri_cua_d" disabled onclick='seleccionar_diente("d", "pri_cua_d", 2, "diente_primer_cuad")' <?php if($pri_cua_d==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="pri_cua_v" name="pri_cua_v" disabled onclick='seleccionar_diente("v", "pri_cua_v", 2, "diente_primer_cuad")' <?php if($pri_cua_v==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="pri_cua_o" name="pri_cua_o" disabled onclick='seleccionar_diente("o", "pri_cua_o", 2, "diente_primer_cuad")' <?php if($pri_cua_o==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="pri_cua_p" name="pri_cua_p" disabled onclick='seleccionar_diente("p", "pri_cua_p", 2, "diente_primer_cuad")' <?php if($pri_cua_p==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="pri_cua_m" name="pri_cua_m" disabled onclick='seleccionar_diente("m", "pri_cua_m", 2, "diente_primer_cuad")' <?php if($pri_cua_m==1){ echo('checked');}?>/>
			</th>
			
			<th class='titulo_diente_borde' >
				<input type="checkbox" value="" id="1151_d" name="1151_d" disabled onclick='seleccionar_diente("d", "1151_d", 2, "diente_sect_anterior")' <?php if($d1151_d==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="1151_v" name="1151_v" disabled onclick='seleccionar_diente("v", "1151_v", 2, "diente_sect_anterior")' <?php if($d1151_v==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="1151_p" name="1151_p" disabled onclick='seleccionar_diente("p", "1151_p", 2, "diente_sect_anterior")' <?php if($d1151_p==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="1151_m" name="1151_m" disabled onclick='seleccionar_diente("m", "1151_m", 2, "diente_sect_anterior")' <?php if($d1151_m==1){ echo('checked');}?>/>
			</th>
			<th class='diente_div'>&nbsp;</th>
			<th class='titulo_diente_borde'>
				<input type="checkbox" value="" id="2363_m" name="2363_m" disabled onclick='seleccionar_diente("m", "2363_m", 2, "diente_superior_izq")' <?php if($d2363_m==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="2363_v" name="2363_v" disabled onclick='seleccionar_diente("v", "2363_v", 2, "diente_superior_izq")' <?php if($d2363_v==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="2363_p" name="2363_p" disabled onclick='seleccionar_diente("p", "2363_p", 2, "diente_superior_izq")' <?php if($d2363_p==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="2363_d" name="2363_d" disabled onclick='seleccionar_diente("d", "2363_d", 2, "diente_superior_izq")' <?php if($d2363_d==1){ echo('checked');}?>/>
			</th>
			
			<th class='titulo_diente_borde'>
				<input type="checkbox" value="" id="segu_cua_m" name="segu_cua_m" disabled onclick='seleccionar_diente("m", "segu_cua_m", 2, "diente_segundo_cuad")' <?php if($segu_cua_m==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="segu_cua_v" name="segu_cua_v" disabled onclick='seleccionar_diente("v", "segu_cua_v", 2, "diente_segundo_cuad")' <?php if($segu_cua_v==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="segu_cua_o" name="segu_cua_o" disabled onclick='seleccionar_diente("o", "segu_cua_o", 2, "diente_segundo_cuad")' <?php if($segu_cua_o==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="segu_cua_p" name="segu_cua_p" disabled onclick='seleccionar_diente("p", "segu_cua_p", 2, "diente_segundo_cuad")' <?php if($segu_cua_p==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="segu_cua_d" name="segu_cua_d" disabled onclick='seleccionar_diente("d", "segu_cua_d", 2, "diente_segundo_cuad")' <?php if($segu_cua_d==1){ echo('checked');}?>/>
			</th>
		<?php
		}
		else{
		echo"<th class='titulo_diente_borde'>";	
		    echo $pri_cua_d."  &nbsp;&nbsp;&nbsp;  ".$pri_cua_v."  &nbsp;&nbsp;&nbsp;  ".$pri_cua_o."  &nbsp;&nbsp;&nbsp;  ".$pri_cua_p."  &nbsp;&nbsp;&nbsp;  ".$pri_cua_m;
		echo"</th>";
		echo"<th class='titulo_diente_borde'>";	
		    echo $d1151_d."  &nbsp;&nbsp;&nbsp;  ".$d1151_v."  &nbsp;&nbsp;&nbsp;  ".$d1151_p."  &nbsp;&nbsp;&nbsp;  ".$d1151_m;
		echo"</th>
			 <th class='diente_div'>&nbsp;</th>";
		echo"<th class='titulo_diente_borde'>";	
		    echo $d2363_m."  &nbsp;&nbsp;&nbsp;  ".$d2363_v."  &nbsp;&nbsp;&nbsp;  ".$d2363_p."  &nbsp;&nbsp;&nbsp;  ".$d2363_d;
		echo"</th>";
		echo"<th class='titulo_diente_borde'>";	
		    echo $segu_cua_m."  &nbsp;&nbsp;&nbsp;  ".$segu_cua_v."  &nbsp;&nbsp;&nbsp;  ".$segu_cua_o."  &nbsp;&nbsp;&nbsp;  ".$segu_cua_p."  &nbsp;&nbsp;&nbsp;  ".$segu_cua_d;
		echo"</th>";	
		}
		?>
		</tr>
		<tr>
		<th class='diente_bottom' colspan='19'>&nbsp;</th>
		</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" align="center" style="width:60em;">
		<tr>
			<th class='titulo_diente_int_der' style="width:20em;">
				&Uacute;ltimo molar <br /> 4to cuadrante<br />
				<?php
				if($tipo == 1){
					$combo->get('diente_cuarto_cuad', $diente_cuarto_cuad, $array_cuarto_cuad, '&nbsp', 'validar_ipb_diente(this.id, \'diente_inferior_der\', \'cuat_cua_\' );', '', 'width:5em;', '63');
				}
				else{
				    echo "<br />".$diente_cuarto_cuad;
				}
				?>
			</th>
			<th class='titulo_diente_int_izq' style="width:20em;">
				Primer premolar inferior derecho <br /> 44 / 84<br />
				<?php
				if($tipo == 1){
					echo"<div id='div_diente_inferior_der'>";
					$combo->get('diente_inferior_der', $diente_inferior_der, $array_inferior_dere, '&nbsp', 'validar_ipb_diente(this.id, \'diente_tercer_cuad\', \'4484_\' );', '', 'width:5em;', '62');
					echo"</div>";
				}
				else{
				    echo "<br />".$diente_inferior_der;
				}
				?>
			</th>
			<th class='diente_div'>&nbsp;</th>
			<th class='titulo_diente_int_der' style="width:20em;">
				&Uacute;ltimo molar <br /> 3er cuadrante<br />
				<?php
				if($tipo == 1){
					echo"<div id='div_diente_tercer_cuad'>";
					$combo->get('diente_tercer_cuad', $diente_tercer_cuad, $array_tercer_cuad, '&nbsp', 'validar_ipb_diente(this.id, \'diente_segundo_cuad\', \'terc_cua_\' );', '', 'width:5em;', '61');
					echo"</div>";
				}
				else{
				    echo "<br />".$diente_tercer_cuad;
				}
				?>
			</th>
			<th class='titulo_diente_int_izq' style="width:20em;">&nbsp;</th>
		</tr>
		
		<tr>
			<th class='diente_borde_izq' align="center" valign="middle">
				<br />
				<div class="d_diente">
				<table border="0" cellpadding="0" cellspacing="0" align="center">
				<tr>
					<td valign="bottom" align="center" colspan='3'><div class='d_superior' id='c_cuat_cua_l<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("l", "cuat_cua_l", 1, "diente_cuarto_cuad")' <?php } ?> ></div></td>
				</tr>
				<tr>
					<td align="right" ><div class='d_izquierda' id='c_cuat_cua_d<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("d", "cuat_cua_d", 1, "diente_cuarto_cuad")' <?php } ?> ></div></td>
					<td class='d_tabla'><div class='d_centro' id='c_cuat_cua_o<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("o", "cuat_cua_o", 1, "diente_cuarto_cuad")' <?php } ?> ></div></td>
					<td align="left"><div class='d_derecha' id='c_cuat_cua_m<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("m", "cuat_cua_m", 1, "diente_cuarto_cuad")' <?php } ?> ></div></td>
				</tr>
				<tr>
					<td valign="top" align="center" colspan='3'><div class='d_inferior' id='c_cuat_cua_v<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("v", "cuat_cua_v", 1, "diente_cuarto_cuad")' <?php } ?> ></div></td>
				</tr>
				</table>	
				</div>
				<br />
			</th>
			<th class='diente_borde_der' align="center" valign="middle">
				<br />
				<div class="d_diente">
				<table border="0" cellpadding="0" cellspacing="0" align="center">
				<tr>
					<td valign="bottom" align="center" colspan='3'><div class='d_superior' id='c_4484_l<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("l", "4484_l", 1, "diente_inferior_der")' <?php } ?> ></div></td>
				</tr>
				<tr>
					<td align="right" ><div class='d_izquierda' id='c_4484_d<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("d", "4484_d", 1, "diente_inferior_der")' <?php } ?> ></div></td>
					<td class='d_tabla'><div class='d_centro' id='c_4484_o<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("o", "4484_o", 1, "diente_inferior_der")' <?php } ?> ></div></td>
					<td align="left"><div class='d_derecha' id='c_4484_m<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("m", "4484_m", 1, "diente_inferior_der")' <?php } ?> ></div></td>
				</tr>
				<tr>
					<td valign="top" align="center" colspan='3'><div class='d_inferior' id='c_4484_v<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("v", "4484_v", 1, "diente_inferior_der")' <?php } ?> ></div></td>
				</tr>
				</table>	
				</div>
				<br />
			</th>
			<th class='diente_div'>&nbsp;</th>
			<th class='diente_borde_izq' align="center" valign="middle">
				<br />
				<div class="d_diente">
				<table border="0" cellpadding="0" cellspacing="0" align="center">
				<tr>
					<td valign="bottom" align="center" colspan='3'><div class='d_superior' id='c_terc_cua_l<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("l", "terc_cua_l", 1, "diente_tercer_cuad")' <?php } ?>></div></td>
				</tr>
				<tr>
					<td align="right" ><div class='d_izquierda' id='c_terc_cua_m<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("m", "terc_cua_m", 1, "diente_tercer_cuad")' <?php } ?> ></div></td>
					<td class='d_tabla'><div class='d_centro' id='c_terc_cua_o<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("o", "terc_cua_o", 1, "diente_tercer_cuad")' <?php } ?> ></div></td>
					<td align="left"><div class='d_derecha' id='c_terc_cua_d<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("d", "terc_cua_d", 1, "diente_tercer_cuad")' <?php } ?> ></div></td>
				</tr>
				<tr>
					<td valign="top" align="center" colspan='3'><div class='d_inferior' id='c_terc_cua_v<?php echo($sufijo);?>' <?php if($tipo == 1){?> onclick='seleccionar_diente("v", "terc_cua_v", 1, "diente_tercer_cuad")' <?php } ?> ></div></td>
				</tr>
				</table>	
				</div>
				<br />
			</th>
			<th class='diente_borde_der' align="center" valign="middle" ><div id='val_ipb<?php echo($sufijo);?>' style="font-size:20px;"></div></th>
		</tr>
		
		<tr>
			<th class='titulo_diente_borde'>M &nbsp;&nbsp;&nbsp; V &nbsp;&nbsp;&nbsp; O &nbsp;&nbsp;&nbsp; L &nbsp;&nbsp;&nbsp; D</th>
			<th class='titulo_diente_borde'>M &nbsp;&nbsp;&nbsp; V &nbsp;&nbsp;&nbsp; O &nbsp;&nbsp;&nbsp; L &nbsp;&nbsp;&nbsp; D</th>
			<th class='diente_div'>&nbsp;</th>
			<th class='titulo_diente_borde'>D &nbsp;&nbsp;&nbsp; V &nbsp;&nbsp;&nbsp; O &nbsp;&nbsp;&nbsp; L &nbsp;&nbsp;&nbsp; M</th>
			<th class='titulo_diente_borde'>&nbsp;</th>
		</tr>
		<tr>
		<?php
		if($tipo == 1){
		?>
			<th class='titulo_diente_borde'>
				<input type="checkbox" value="" id="cuat_cua_m" name="cuat_cua_m" disabled  onclick='seleccionar_diente("m", "cuat_cua_m", 2, "diente_cuarto_cuad")' <?php if($cuat_cua_m==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="cuat_cua_v" name="cuat_cua_v" disabled  onclick='seleccionar_diente("v", "cuat_cua_v", 2, "diente_cuarto_cuad")' <?php if($cuat_cua_v==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="cuat_cua_o" name="cuat_cua_o" disabled  onclick='seleccionar_diente("o", "cuat_cua_o", 2, "diente_cuarto_cuad")' <?php if($cuat_cua_o==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="cuat_cua_l" name="cuat_cua_l" disabled  onclick='seleccionar_diente("l", "cuat_cua_l", 2, "diente_cuarto_cuad")' <?php if($cuat_cua_l==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="cuat_cua_d" name="cuat_cua_d" disabled  onclick='seleccionar_diente("d", "cuat_cua_d", 2, "diente_cuarto_cuad")' <?php if($cuat_cua_d==1){ echo('checked');}?>/>
			</th>
			<th class='titulo_diente_borde'>
				<input type="checkbox" value="" id="4484_m" name="4484_m" disabled onclick='seleccionar_diente("m", "4484_m", 2, "diente_inferior_der")' <?php if($d4484_m==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="4484_v" name="4484_v" disabled onclick='seleccionar_diente("v", "4484_v", 2, "diente_inferior_der")' <?php if($d4484_v==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="4484_o" name="4484_o" disabled onclick='seleccionar_diente("o", "4484_o", 2, "diente_inferior_der")' <?php if($d4484_o==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="4484_l" name="4484_l" disabled onclick='seleccionar_diente("l", "4484_l", 2, "diente_inferior_der")' <?php if($d4484_l==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="4484_d" name="4484_d" disabled onclick='seleccionar_diente("d", "4484_d", 2, "diente_inferior_der")' <?php if($d4484_d==1){ echo('checked');}?>/>
			</th>
			<th class='diente_div'>&nbsp;</th>
			<th class='titulo_diente_borde'>
				<input type="checkbox" value="" id="terc_cua_d" name="terc_cua_d" disabled onclick='seleccionar_diente("d", "terc_cua_d", 2, "diente_tercer_cuad")' <?php if($terc_cua_d==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="terc_cua_v" name="terc_cua_v" disabled onclick='seleccionar_diente("v", "terc_cua_v", 2, "diente_tercer_cuad")' <?php if($terc_cua_v==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="terc_cua_o" name="terc_cua_o" disabled onclick='seleccionar_diente("o", "terc_cua_o", 2, "diente_tercer_cuad")' <?php if($terc_cua_o==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="terc_cua_l" name="terc_cua_l" disabled onclick='seleccionar_diente("l", "terc_cua_l", 2, "diente_tercer_cuad")' <?php if($terc_cua_l==1){ echo('checked');}?>/>
				<input type="checkbox" value="" id="terc_cua_m" name="terc_cua_m" disabled onclick='seleccionar_diente("m", "terc_cua_m", 2, "diente_tercer_cuad")' <?php if($terc_cua_m==1){ echo('checked');}?>/>
			</th>
			<th class='titulo_diente_borde'>&nbsp;</th>
		<?php	
		}
		else{
		echo"<th class='titulo_diente_borde'>";	
		    echo $cuat_cua_m."  &nbsp;&nbsp;&nbsp;  ".$cuat_cua_v."  &nbsp;&nbsp;&nbsp;  ".$cuat_cua_o."  &nbsp;&nbsp;&nbsp;  ".$cuat_cua_l."  &nbsp;&nbsp;&nbsp;  ".$cuat_cua_d;
		echo"</th>";
		echo"<th class='titulo_diente_borde'>";	
		    echo $d4484_m."  &nbsp;&nbsp;&nbsp;  ".$d4484_v."  &nbsp;&nbsp;&nbsp;  ".$d4484_o."  &nbsp;&nbsp;&nbsp;  ".$d4484_l."  &nbsp;&nbsp;&nbsp;  ".$d4484_d;
		echo"</th>
			 <th class='diente_div'>&nbsp;</th>";
		echo"<th class='titulo_diente_borde'>";	
		    echo $terc_cua_d."  &nbsp;&nbsp;&nbsp;  ".$terc_cua_v."  &nbsp;&nbsp;&nbsp;  ".$terc_cua_o."  &nbsp;&nbsp;&nbsp;  ".$terc_cua_l."  &nbsp;&nbsp;&nbsp;  ".$terc_cua_m;
		echo"</th>
			 <th class='titulo_diente_borde'>&nbsp;</th>";	
		}
		?>
		</tr>
		</table>
					
		
		
		<?php
		
	}
	
	
 	 
 }
?>