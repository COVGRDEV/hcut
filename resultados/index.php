<?php
	/*
	  Pagina inicio que permite iniciar sesion al usuario
	  Autor: Juan Pablo Gomez Quiroga - 11/09/2013
	 */
	
	require_once("../db/DbVariables.php");
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbMenus.php");
	require_once("../db/DbListas.php");
	require_once("../db/Configuracion.php");
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../db/DbPacientes.php");
	
	$dbVariables = new Dbvariables();
	$dbUsuarios = new DbUsuarios();
	$dbMenus = new DbMenus();
	$dbListas = new DbListas();
	$utilidades = new Utilidades();
	$funcionesPersona = new FuncionesPersona();
	$dbPacientes = new DbPacientes();
	
	//variables
	$titulo = $dbVariables->getVariable(1);
	
	//Recibe los datos POST o el GET
	$usuario = isset($_REQUEST['usuario']) ? $utilidades->limpiar_tags($_REQUEST['usuario']) : null;
	$contrasena = isset($_REQUEST['contrasena']) ? $utilidades->limpiar_tags($_REQUEST['contrasena']) : null;
	
	$error = null;
	$clase = "index_error2";
	
	if ($usuario || $contrasena) {
		//consulta en base de datos
		$resultado = $dbPacientes->validarIngresoResultados($_REQUEST['usuario'], $_REQUEST['contrasena']);
	
		if ($resultado['id_paciente'] <= 0) {
			$error = true;
			$clase = "index_error";
		} else {
			//Se cargan los datos del usuario en la sesión
			session_start();
			$_SESSION["idUsuario"] = $resultado["id_paciente"];
			$_SESSION["nomUsuario"] = $funcionesPersona->obtenerNombreCompleto($resultado["nombre_1"], $resultado["nombre_2"], $resultado["apellido_1"], $resultado["apellido_2"]);
			$_SESSION["idLugarUsuario"] = 0;
			
			$pagina_inicio = "resultados.php";
			$id_menu = "0";
			
			//Se redirecciona a la página inicial
			?>
			<form name="frm_login" id="frm_login" method="post" action="<?php echo($pagina_inicio); ?>">
				<input type="hidden" name="credencial" id="credencial" value="<?php echo($resultado["id_paciente"]); ?>" />
				<input type="hidden" name="hdd_numero_menu" id="hdd_numero_menu" value="<?php echo($id_menu); ?>" />
			</form>
			<script type="text/javascript">
				document.getElementById("frm_login").submit();
			</script>
			<?php
		}
	} else {
		$clase = "index_error2";
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $titulo['valor_variable']; ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
		<link href="../css/azul.css" rel="stylesheet" type="text/css" />
		
        <script type='text/javascript' src='../js/jquery.js'></script>
        <script type='text/javascript' src='../js/jquery.validate.js'></script>
		
        <script type="text/javascript">
			<!--
            $(document).ready(function() {
                $("#unFormulario").validate({
                    rules: {
                        usuario: {
                            required: true,
                            maxlength: 50,
                        },
                        contrasena: {
                            required: true,
                            maxlength: 50,
                        },
                        lugar: {
                            required: true,
                        },
                    },
                });
            });

            function dirigirFoco() {
                document.getElementById("usuario").focus();
            }
			// -->
        </script>
    </head>
    <body id="login" onLoad="dirigirFoco();">
        <div class="login-container">
            <div class="login">
                <img src="../<?php echo Configuracion::$LOGO_INICIO;?>" alt="logo-color" class="logo-login">
                    <div class='contenedor_error' id='contenedor_error'>
                        <p>Debe ingresar todos los campos</p>
                    </div>
                    <?php
                    if ($error) {
                        echo '<div class="' . $clase . '">';
                        echo '<p>Por favor corrija los siguientes errores de ingreso:</p>';
                        echo '<ul>';
                        echo '<li>Nombre de usuario o contrase&ntildea no validos</li>';
                        echo '</ul>';
                        echo '</div>';
                    }
					
					//Se obtiene el listado de lugares
					$lista_lugares = $dbListas->getListaDetalles(12, 1);
					
					//Se verifica si existe la coolie de lugares
					$id_lugar_act = "";
					if (isset($_COOKIE["LugarUsuario"])) {
						$id_lugar_act = $_COOKIE["LugarUsuario"];
					}
                    ?>
                    <form id='unFormulario' name='unFormulario' method="post" action="index.php">
                        <input class="input required usuario" type="text" id="usuario" name="usuario" placeholder="Usuario"/>
                        <input class="input required password" type="password" id="contrasena" name="contrasena" placeholder="Contrase&ntilde;a"/>
                        <input class="btnIniciarsesion" type="submit" value="Ingresar" id="enviar" />
                    </form>
            </div>
        </div>
        </div>
    </body>
</html>
