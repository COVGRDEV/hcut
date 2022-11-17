<?php session_start();
	header("Content-Type: text/xml; charset=UTF-8");
	
	require_once("../db/DbConsultaDermatologia.php");
	require_once("../db/DbMenus.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Class_Atencion_Remision.php");
	require_once("../funciones/Class_Formulacion.php");
	require_once("../funciones/Class_Solic_Procs.php");
	require_once("../funciones/Utilidades.php");
	
	$dbConsultaDermatologia = new DbConsultaDermatologia();
	$dbMenus = new DbMenus();
	
	$contenido = new ContenidoHtml();
	$contenido->validar_seguridad(1);
	$utilidades = new Utilidades();
	
	$opcion=$_POST["opcion"];
	
	function cambiar_mas($texto){
		$resultado = str_replace("|mas", "+", $texto);
		return $resultado;	
	}
	
	function cambiar_espacio($texto, $cambio){
		$valor = "";
		if ($texto == "") {
			$valor = $cambio;
		} else {
			$valor = $texto;
		}
		
		return $valor;
	}
	
	switch ($opcion) {
		case "1": //Guardar consulta dermatológica
			$id_usuario = $_SESSION["idUsuario"];
			@$id_hc_consulta = $utilidades->str_decode($_POST["id_hc_consulta"]);
			@$id_admision = $utilidades->str_decode($_POST["id_admision"]);
			@$peso = $utilidades->str_decode($_POST["peso"]);
			@$talla = $utilidades->str_decode($_POST["talla"]);
			@$id_ludwig = $utilidades->str_decode($_POST["id_ludwig"]);
			@$fg_labio_superior = $utilidades->str_decode($_POST["fg_labio_superior"]);
			@$fg_mejilla = $utilidades->str_decode($_POST["fg_mejilla"]);
			@$fg_torax = $utilidades->str_decode($_POST["fg_torax"]);
			@$fg_espalda_superior = $utilidades->str_decode($_POST["fg_espalda_superior"]);
			@$fg_espalda_inferior = $utilidades->str_decode($_POST["fg_espalda_inferior"]);
			@$fg_abdomen_superior = $utilidades->str_decode($_POST["fg_abdomen_superior"]);
			@$fg_abdomen_inferior = $utilidades->str_decode($_POST["fg_abdomen_inferior"]);
			@$fg_brazo = $utilidades->str_decode($_POST["fg_brazo"]);
			@$fg_muslo = $utilidades->str_decode($_POST["fg_muslo"]);
			@$descripcion_cara = $utilidades->str_decode($_POST["descripcion_cara"]);
			@$descripcion_cuerpo = $utilidades->str_decode($_POST["descripcion_cuerpo"]);
			@$desc_antecedentes_medicos = $utilidades->str_decode($_POST["desc_antecedentes_medicos"]);
			@$diagnostico_dermat = $utilidades->str_decode($_POST["diagnostico_dermat"]);
			@$solicitud_examenes = $utilidades->str_decode($_POST["solicitud_examenes"]);
			@$tratamiento_dermat = $utilidades->str_decode($_POST["tratamiento_dermat"]);
			@$tipo_guardar = $utilidades->str_decode($_POST["tipo_guardar"]);
			
			@$array_antecedentes_medicos_ids = $utilidades->str_decode($_POST["array_antecedentes_medicos_ids"]);
			@$array_antecedentes_medicos_val = $utilidades->str_decode($_POST["array_antecedentes_medicos_val"]);
			
			@$cant_ciex = $_POST["cant_ciex"];
			$array_diagnosticos = array();
			for ($i = 1; $i <= $cant_ciex; $i++) {
				if (isset($_POST["cod_ciex_".$i])) {
					@$array_diagnosticos[$i][0] = $utilidades->str_decode($_POST["cod_ciex_".$i]);
					@$array_diagnosticos[$i][1] = $utilidades->str_decode($_POST["val_ojos_".$i]);
				}	
			}
			
			$ind_opt = $dbConsultaDermatologia->EditarConsultaDermatologia($id_hc_consulta, $id_admision, $peso, $talla, $id_ludwig, $fg_labio_superior,
					$fg_mejilla, $fg_torax, $fg_espalda_superior, $fg_espalda_inferior, $fg_abdomen_superior, $fg_abdomen_inferior, $fg_brazo, $fg_muslo,
					$descripcion_cara, $descripcion_cuerpo, $desc_antecedentes_medicos, $array_antecedentes_medicos_ids, $array_antecedentes_medicos_val,
					$diagnostico_dermat, $solicitud_examenes, $tratamiento_dermat, $array_diagnosticos, $tipo_guardar, $id_usuario);
			
			if ($ind_opt > 0) {
				//Formulación de medicamentos
				$class_formulacion = new Class_Formulacion();
				$resultado_aux = $class_formulacion->guardarFormulacionHC($id_hc_consulta, $id_usuario);
		?>
        <input type="hidden" name="hdd_exito_formulacion_fm" id="hdd_exito_formulacion_fm" value="<?php echo($resultado_aux); ?>" />
        <?php
				//Solicitud de procedimientos
				$class_solic_procs = new Class_Solic_Procs();
				$resultado_aux = $class_solic_procs->guardarHCProcedimientosSolic($id_hc_consulta, $id_usuario);
		?>
    	<input type="hidden" name="hdd_exito_hc_procedimientos_solic" id="hdd_exito_hc_procedimientos_solic" value="<?php echo($resultado_aux); ?>" />
        <?php
			} else {
		?>
        <input type="hidden" name="hdd_exito_formulacion_fm" id="hdd_exito_formulacion_fm" value="1" />
        <input type="hidden" name="hdd_exito_hc_procedimientos_solic" id="hdd_exito_hc_procedimientos_solic" value="1" />
        <?php
			}
			
			$reg_menu = $dbMenus->getMenu(13);
			$url_menu = $reg_menu["pagina_menu"];
		?>
		<input type="hidden" value="<?php echo($ind_opt); ?>" name="hdd_exito" id="hdd_exito" />
		<input type="hidden" value="<?php echo($url_menu); ?>" name="hdd_url_menu" id="hdd_url_menu" />
		<input type="hidden" value="<?php echo($tipo_guardar); ?>" name="hdd_tipo_guardar" id="hdd_tipo_guardar" />
		<div class="contenedor_error" id="contenedor_error"></div>
		<div class="contenedor_exito" id="contenedor_exito"></div>
		<?php
			break;
			
		case "2": //Opciones de flujos alternativos
			$id_hc = $_POST["id_hc"];
			$id_admision = $_POST["id_admision"];
			$ind_preconsulta = $_POST["ind_preconsulta"];
			
			$atencion_remision = new Class_Atencion_Remision();
			$atencion_remision->getFormularioRemisiones($id_hc, $id_admision, "crear_dermatologia(4, 0);", "hdd_exito");
			break;
	}
?>
