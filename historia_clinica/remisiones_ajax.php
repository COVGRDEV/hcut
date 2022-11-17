<?php
	session_start();
	
	header('Content-Type: text/xml; charset=UTF-8');
	
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbListas.php");
	require_once("../db/DbDespacho.php");
	require_once("../db/DbMenus.php");
	require_once("../db/DbHistoriaClinica.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../db/DbVariables.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/Utilidades.php");
	require_once("../db/DbPacientes.php");
	
	$usuarios = new DbUsuarios();
	$listas = new DbListas();
	$despacho = new DbDespacho();
	$menus = new DbMenus();
	$variables = new Dbvariables();
	$contenido = new ContenidoHtml();
	$contenido->validar_seguridad(1);
	$funciones_persona = new FuncionesPersona();
	$utilidades = new Utilidades();
	$pacientes = new DbPacientes();
	$combo = new Combo_Box();
	$opcion = $_POST["opcion"];
	$dbHistoria = new DbHistoriaClinica();
	
	function ver_remisiones($id_paciente, $nombre_persona, $documento_persona, $tipo_documento, $telefonos, $fecha_nacimiento, $edad_paciente, $nombre_convenio, $estado_convenio) {
		$dbHistoria = new DbHistoriaClinica();
		$menus = new DbMenus();
		$tabla_registros = $dbHistoria->getHistoriasRemisionesActivas($id_paciente);
		@$credencial = $_POST["credencial"];
		@$id_menu = $_POST["hdd_numero_menu"];
    ?>
    <fieldset style="width: 65%; margin: auto;">
        <legend>Datos del paciente:</legend>
        <table style="width: 500px; margin: auto; font-size: 10pt;">
            <tr>
                <td align="right" style="width:40%">Tipo de documento:</td>
                <td align="left" style="width:60%"><b><?php echo($tipo_documento); ?></b></td>
            </tr>
            <tr>
                <td align="right">N&uacute;mero de identificaci&oacute;n:</td>
                <td align="left"><b><?php echo($documento_persona); ?></b></td>
            </tr>
            <tr>
                <td align="right">Nombre completo:</td>
                <td align="left"><b><?php echo($nombre_persona); ?></b></td>
            </tr>
            <tr>
                <td align="right">Fecha de nacimiento:</td>
                <td align="left"><b><?php echo($fecha_nacimiento); ?></b></td>
            </tr>
            <tr>
                <td align="right">Edad:</td>
                <td align="left"><b><?php echo($edad_paciente); ?> a&ntilde;os</b></td>
            </tr>
            <tr>
                <td align="right">Tel&eacute;fonos:</td>
                <td align="left"><b><?php echo($telefonos); ?></b></td>
            </tr>
            <tr>
                <td align="right">Entidad:</td>
                <td align="left" style="color: #008CC7;"><b><?php echo($nombre_convenio); ?></b></td>
            </tr>
            <tr>
                <?php
					$nombre_estado_convenio = "";
					$background_style = "background-color:#E3B02F;";
					$color_style = "color:#FFF;";
					switch ($estado_convenio) {
						case 1://Activo
							$nombre_estado_convenio = "ACTIVO";
							$background_style = "background-color:#399000;";
							break;
						case 2://Inactivo
							$nombre_estado_convenio = "INACTIVO";
							$background_style = "background-color:#E11111;";
							break;
						case 3://Atención especial
							$nombre_estado_convenio = "ATENCIÓN ESPECIAL";
							break;
						case 4://Retirado
							$nombre_estado_convenio = "RETIRADO";
							break;
					}
                ?>
                <td align="right" style="<?= $background_style . $color_style ?>">Estado:</td>
                <td align="left" style="<?= $background_style . $color_style ?>"><b><?php echo($nombre_estado_convenio); ?></b></td>
            </tr>
        </table>
    </fieldset>
    <table class="modal_table" style="width: 70%; margin: auto;" >
        <thead>
            <tr>
                <th class="th_reducido" align="center" style="width:15%;">Fecha</th>
                <th class="th_reducido" align="center" style="width:90%;">Tipo de cita</th>
            </tr>
        </thead>
        <?php
			$reg_menu = $menus->getMenu(72);
			$pagina_consulta = $reg_menu['pagina_menu'];
			$id_hc = 0;
			foreach ($tabla_registros as $fila_registro) {
				$fecha_hc = $fila_registro['fecha_HC'];
				$nombre_tipo_reg = $fila_registro['nombre_tipo_reg'];
				$id_admision = $fila_registro['id_admision'];
				$id_hc = $fila_registro['id_hc'];
		?>
        <tr onclick="mostrar_consultas_div(<?php echo($id_paciente); ?>, '<?php echo($nombre_persona); ?>', <?php echo($id_admision); ?>, '<?php echo($pagina_consulta); ?>', <?php echo($id_hc); ?>, <?php echo($credencial); ?>, <?php echo($id_menu); ?>);">
            <td class="td_reducido" align="left"><?php echo($fecha_hc); ?></td>
            <td class="td_reducido" align="left"><?php echo($nombre_tipo_reg); ?></td>
        </tr>
        <?php
			}
		?>
    </table>
    <div>
        <br>
        <br>
        <input class="btnPrincipal" type="button" value="Asigna cita" id="btn_consultar" name="btn_consultar" onclick="asignarCita(<?= $id_paciente ?>);" />
    </div>
    <?php
	}
	
	switch ($opcion) {
		case "1": //Consultar HC del paciente
			$txt_paciente_hc = $_POST["txt_paciente_hc"];
			//$tabla_personas = $dbHis
			
			$tabla_remisiones = $dbHistoria->consultarHistoriasRemisionesActivas($txt_paciente_hc);
			$cantidad_remisiones = count($tabla_remisiones);
			
			if ($cantidad_remisiones == 1) {//1 registro
				$id_paciente = $tabla_remisiones[0]['id_paciente'];
				$nombre_1 = $tabla_remisiones[0]['nombre_1'];
				$nombre_2 = $tabla_remisiones[0]['nombre_2'];
				$apellido_1 = $tabla_remisiones[0]['apellido_1'];
				$apellido_2 = $tabla_remisiones[0]['apellido_2'];
				$numero_documento = $tabla_remisiones[0]['numero_documento'];
				$tipo_documento = $tabla_remisiones[0]['tipo_documento'];
				$fecha_nacimiento = $tabla_remisiones[0]['fecha_nac_persona'];
				$telefonos = $tabla_remisiones[0]['telefono_1'] . " - " . $tabla_personas[0]['telefono_2'];
				$nombres_apellidos = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
				$datos_paciente = $pacientes->getEdadPaciente($id_paciente, '');
				$edad_paciente = $datos_paciente['edad'];
				$nombre_convenio = $tabla_remisiones[0]['nombre_convenio'];
				$estado_convenio = $tabla_remisiones[0]['status_convenio_paciente'];
				
				ver_remisiones($tabla_remisiones[0]['id_paciente'], $nombres_apellidos, $numero_documento, $tipo_documento, $telefonos, $fecha_nacimiento, $edad_paciente, $nombre_convenio, $estado_convenio);
			} else if ($cantidad_remisiones > 1) {//Múltiples registros
		?>
        <table id='tabla_persona_hc'  border='0' class="paginated modal_table" style="width: 70%; margin: auto;">
            <thead>
                <tr class='headegrid'>
                    <th class="headegrid" align="center">Documento</th>	
                    <th class="headegrid" align="center">Pacientes</th>
                </tr>
            </thead>
            <?php
				foreach ($tabla_remisiones as $fila_personas) {
					$id_personas = $fila_personas['id_paciente'];
					$nombre_1 = $fila_personas['nombre_1'];
					$nombre_2 = $fila_personas['nombre_2'];
					$apellido_1 = $fila_personas['apellido_1'];
					$apellido_2 = $fila_personas['apellido_2'];
					$numero_documento = $fila_personas['numero_documento'];
					$tipo_documento = $fila_personas['tipo_documento'];
					$fecha_nacimiento = $fila_personas['fecha_nac_persona'];
					$telefonos = $fila_personas['telefono_1'] . " - " . $fila_personas['telefono_2'];
					$nombres_apellidos = $funciones_persona->obtenerNombreCompleto($nombre_1, $nombre_2, $apellido_1, $apellido_2);
					$datos_paciente = $pacientes->getEdadPaciente($id_personas, '');
					$edad_paciente = $datos_paciente['edad'];
					$nombre_convenio = $tabla_remisiones[0]['nombre_convenio'];
					$estado_convenio = $tabla_remisiones[0]['status_convenio_paciente'];
			?>
            <tr class='celdagrid' onclick="ver_remisiones_paciente(<?php echo($id_personas); ?>, '<?php echo($nombres_apellidos); ?>', '<?php echo($numero_documento); ?>', '<?php echo($tipo_documento); ?>', '<?php echo($telefonos); ?>', '<?php echo($fecha_nacimiento); ?>', '<?php echo($edad_paciente); ?>', '<?php echo($nombre_convenio); ?>', '<?php echo($estado_convenio); ?>');">
                <td align="left"><?php echo $numero_documento; ?></td>	
                <td align="left"><?php echo $nombres_apellidos; ?></td>
            </tr>
            <?php
				}
			?>
        </table>
        <?php
			} else if ($cantidad_remisiones == 0) {//No hay datos
		?>
        <div class='msj-vacio'>
            <p>No se encontraron H.C. con remisiones para el paciente</p>
        </div>
        <?php
			}
			break;
			
		case "2":
			$id_persona = $_POST['id_persona'];
			$nombre_persona = $_POST['nombre_persona'];
			$documento_persona = $_POST['documento_persona'];
			$tipo_documento = $_POST['tipo_documento'];
			$telefonos = $_POST['telefonos'];
			$fecha_nacimiento = $_POST['fecha_nacimiento'];
			$edad_paciente = $_POST['edad_paciente'];
			
			$nombreConvenio = $_POST['nombreConvenio'];
			$estadoConvenio = $_POST['estadoConvenio'];
			ver_remisiones($id_persona, $nombre_persona, $documento_persona, $tipo_documento, $telefonos, $fecha_nacimiento, $edad_paciente, $nombreConvenio, $estadoConvenio);
			break;
	}
?>
