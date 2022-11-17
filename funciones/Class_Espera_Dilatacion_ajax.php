<?php
	session_start();
	//Autor: Helio Ruber López - 17/02/2014
	
	header("Content-Type: text/xml; charset=UTF-8");
	
	require_once("../db/DbAdmision.php");
	require_once("../funciones/Utilidades.php");
	require_once("../principal/ContenidoHtml.php");
	
	$dbAdmision = new DbAdmision();
	
	$utilidades = new Utilidades();
	$contenido = new ContenidoHtml();
	$contenido->validar_seguridad(1);
	$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);
	
	$opcion = $_POST["opcion"];
	
	switch ($opcion) {
		case "1": //Se registra nueva actividad de espera
			if ($tipo_acceso_menu == 2) {
				$id_usuario = $_SESSION["idUsuario"];
				@$id_admision = $utilidades->str_decode($_POST["id_admision"]);
				@$ind_dilatado = intval($_POST["ind_dilatado"], 10);
				
				if ($ind_dilatado == 1) {
					$id_tipo_espera = 430;
				} else {
					$id_tipo_espera = "";
				}
				$resultado = $dbAdmision->editar_admision_espera($id_admision, $id_tipo_espera, $id_usuario);
			} else {
				$resultado = 0;
			}
		?>
        <input type="hidden" id="hdd_resul_guardar_adm_espera" value="<?php echo($resultado); ?>" />
        <?php
			break;
	}
?>
