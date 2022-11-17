<?php
	/*
	  Pagina inicio que permite iniciar sesion al usuario
	  Autor: Juan Pablo Gomez Quiroga - 11/09/2013
	 */
	
	require_once("db/DbVariables.php");
	require_once("db/DbUsuarios.php");
	require_once("db/DbMenus.php");
	require_once("db/DbListas.php");
	require_once("db/Configuracion.php");
	require_once("funciones/Utilidades.php");
	require_once("funciones/FuncionesPersona.php");
	
	$dbVariables = new Dbvariables();
	$dbUsuarios = new DbUsuarios();
	$dbMenus = new DbMenus();
	$dbListas = new DbListas();
	$utilidades = new Utilidades();
	$funcionesPersona = new FuncionesPersona();
	
	//variables
	$titulo = $dbVariables->getVariable(1);
	
	//Recibe los datos POST
	$usuario = isset($_POST['usuario']) ? $utilidades->limpiar_tags($_POST['usuario']) : null;
	$contrasena = isset($_POST['contrasena']) ? $utilidades->limpiar_tags($_POST['contrasena']) : null;
	$id_lugar = isset($_POST['lugar']) ? $utilidades->limpiar_tags($_POST['lugar']) : null;
	
	$error = null;
	$clase = "index_error2";
	
	if ($usuario || $contrasena || $id_lugar) {
		//consulta en base de datos
		$resultado = $dbUsuarios->validarIngreso($_POST['usuario'], $_POST['contrasena']);
	
		if ($resultado['id_usuario'] <= 0) {
			$error = true;
			$clase = "index_error";
		} else {
			//Se cargan los datos del usuario en la sesión
			session_start();
			$_SESSION["idUsuario"] = $resultado["id_usuario"];
			$_SESSION["nomUsuario"] = $funcionesPersona->obtenerNombreCompleto($resultado["nombre_usuario"], $resultado["apellido_usuario"], '', '');
			$_SESSION["idLugarUsuario"] = $id_lugar;
			$nomLugarUsuario = $dbListas->getDetalle($id_lugar);
			$_SESSION["nomLugarUsuario"] = $nomLugarUsuario['nombre_detalle'];
			$sedeDetalle = $dbListas->getSedesDetalle($id_lugar);            
			$_SESSION["compania"] = $sedeDetalle['id_compania'];//Código de compañía SIESA
			$_SESSION["companiaDispensacion"] = $sedeDetalle['id_compania_dispensacion'];//Código de compañía SIESA para dispensación
			$_SESSION["coVentas"] = $sedeDetalle['id_co_ventas'];//Centro de operaciones SIESA para ventas
			$_SESSION["coDispensacion"] = $sedeDetalle['id_co_dispensacion'];//Centro de operaciones SIESA para dispensación
			$_SESSION["bodegaDispensacion"] = $sedeDetalle['id_bodega_dispensacion'];//Bodega de dispensación de medicamentos
			$_SESSION["bodegaVentas"] = $sedeDetalle['id_bodega_ventas'];//Bodega de ventas
			
		
			if($sedeDetalle['dir_logo_sede_det']!=""){
			
			$_SESSION["logo"] = $sedeDetalle['dir_logo_sede_det'];//Dirección del archivo logo para la sede
			}
			else{
				$_SESSION["logo"]="../".Configuracion::$LOGO_INICIO;
			}

							
			$_SESSION["telefono"] = $sedeDetalle['tel_sede_det'];//Telefono de concato de la sede
			$_SESSION["direccion"] = $sedeDetalle['dir_sede_det'];//Dirección de la sede
			//Las siguientes variables se sesión con utilizadas por el conector de entidad dinámica del tercero cliente para la asignación
			//del correo electronico y para la asignación de la zonificación del servicio de salud.
			$_SESSION["entidadCorreo"] = $sedeDetalle['id_entidadCorreo_sede_det'];//Id de la entidad correo en SIESA [conector para entidad dinamica tercero cliente]
			$_SESSION["atributoCorreo"] = $sedeDetalle['id_atributoCorreo_sede_det'];//Id atributo de la entidad correo en SIESA [conector para entidad dinamica tercero cliente]
			$_SESSION["entidadLocalidad"] = $sedeDetalle['id_entidadLocalidad_sede_det'];//Id atributo de la localidad en SIESA [conector para entidad dinamica tercero cliente]
			$_SESSION["atributoLocalidad"] = $sedeDetalle['id_atributoLocalidad_sede_det'];//Id atributo de la localidad en SIESA [conector para entidad dinamica tercero cliente]
            
			//Se actualiza la cookie del lugar
			setcookie("LugarUsuario", $id_lugar, (time() + (86400 * 30)), "/"); //Validez de 30 días
			
			//Se obtiene la página a la que se debe redireccionar
			$menu_obj = $dbMenus->getMenuInicioUsuario($resultado["id_usuario"]);
			$pagina_inicio = "principal/principal.php";
			$id_menu = "0";
			if (isset($menu_obj["pagina_menu"])) {
				$pagina_inicio = substr($menu_obj["pagina_menu"], 3);
				$id_menu = $menu_obj["id_menu"];
			}
			
			//Se redirecciona a la página inicial
			?>
			<form name="frm_login" id="frm_login" method="post" action="<?php echo($pagina_inicio); ?>">
				<input type="hidden" name="credencial" id="credencial" value="<?php echo($resultado["id_usuario"]); ?>" />
				<input type="hidden" name="hdd_numero_menu" id="hdd_numero_menu" value="<?php echo($id_menu); ?>" />
			</form>
			<script type="text/javascript">
				document.getElementById("frm_login").submit();
			</script>
			<?php
		}
	
		$clase = "index_error2";
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $titulo['valor_variable']; ?></title>
        <link href="css/estilos.css" rel="stylesheet" type="text/css" />
		<link href="css/azul.css" rel="stylesheet" type="text/css" />
		
        <script type='text/javascript' src='js/jquery.js'></script>
        <script type='text/javascript' src='js/jquery.validate.js'></script>
		
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
                <img src="<?php echo Configuracion::$LOGO_INICIO;?>" alt="logo-color" class="logo-login">
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
                        <input class="input required usuario" type="text" id="usuario" name="usuario" />
                        <input class="input required password" type="password" id="contrasena" name="contrasena" />
                        <select class="required" id="lugar" name="lugar">
                        	<option value="">&lt;Seleccione una sede&gt;</option>
                            <?php
                            	if (count($lista_lugares) > 0) {
									foreach ($lista_lugares as $lugar_aux) {
										$selected_aux = "";
										if ($lugar_aux["id_detalle"] == $id_lugar_act) {
											$selected_aux = " selected=\"selected\"";
										}
							?>
                            <option value="<?php echo($lugar_aux["id_detalle"]); ?>"<?php echo($selected_aux); ?>><?php echo($lugar_aux["nombre_detalle"]); ?></option>
                            <?php
									}
								}
							?>
                            <option value="999"<?php if ($id_lugar_act == "999") { ?> selected="selected"<?php } ?>>Acceso externo</option>
                        </select>
                        <input class="btnIniciarsesion" type="submit" value="Ingresar" id="enviar" />
                    </form>
            </div>
        </div>
        </div>
    </body>
</html>
