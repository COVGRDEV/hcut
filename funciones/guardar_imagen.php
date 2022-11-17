<?php
	require_once("../BaseDatos/Servidor_Base_Datos.php");
	require_once("../db/Configuracion.php");
	
	$nombreArch="";
	if (isset($_POST["id_persona_imagen"])) {
		$idPersona=$_POST["id_persona_imagen"];
	    $nombreTmp=$_FILES["fil_imagen"]["tmp_name"];
		
		//Se verifica que la imagen no supere los 100 kb de tamaño
		if (filesize($nombreTmp)<=102400) {
			//Se obtiene la fecha actual para la carpeta que contendrá la imagen
			$conector=Servidor_Base_Datos::conectar_base(Configuracion::$SERVIDOR, Configuracion::$USUARIOS, Configuracion::$PASS, Configuracion::$BASE_DATOS);
			$sql_fecha="SELECT DATE_FORMAT(NOW(), '%Y/%m/%d') AS fecha";
			$tabla_fecha=$conector->select($sql_fecha);
			$fecha=$tabla_fecha[0][0];
			$conector->cerrar_conexion();
			
			//Se obtiene la extensión del archivo
			$arrAux = explode(".", $_FILES["fil_imagen"]["name"]);
			$extension = strtolower($arrAux[count($arrAux) - 1]);
			
			//Se construye el directorio que contendrá el archivo
			$nombreDir = "../imagenes/fotos/".$fecha."/";
			mkdir($nombreDir, 0755, true);
			
			//Se copia el archivo
			echo($nombreTmp."<br />".$nombreDir.$idPersona.".".$extension."<br />");
			copy($nombreTmp, $nombreDir.$idPersona.".".$extension);
			
			$nombreArch="imagenes/fotos/".$fecha."/".$idPersona.".".$extension;
		} else {
			//No se copia el archivo de imagen
	?>
	<script type="text/javascript">
		parent.document.getElementById("mensaje_box").innerHTML = "Se han guardado los datos satisfactoriamente. No se copi&oacute; el archivo de imagen porque supera los 100 KB de tama&ntilde;o";
	</script>
    <?php
		}
	}
?>
<script type="text/javascript">
	parent.cargar_imagen_persona('<?php echo($nombreArch); ?>');
</script>
