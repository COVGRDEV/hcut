<?php session_start();
	/*
	  Pagina ajax para antecedentes
	  Autor: Feisar Moreno - 27/04/2017
	 */
	
 	header('Content-Type: text/xml; charset=UTF-8');

	require_once("../db/DbAntecedentes.php");
	require_once("antecedentes_funciones.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Utilidades.php");
	
	$dbAntecedentes = new DbAntecedentes();
	
	$contenido = new ContenidoHtml();
	$contenido->validar_seguridad(1);
	$utilidades = new Utilidades();
	
	$opcion=$_POST["opcion"];
	
	switch ($opcion) {
		case "1": //Listado de contactos
			@$cant_antecedentes = intval($_POST["cant_antecedentes"], 10);
			$arr_antecedentes_med = array();
			for ($i = 0; $i < $cant_antecedentes; $i++) {
				@array_push($arr_antecedentes_med, intval($_POST["id_antecedente_med_".$i], 10));
			}
			
			//Se obtienen los contactos relacionados con los antecedentes
			$lista_contactos = $dbAntecedentes->get_lista_antecedentes_med_contactos($arr_antecedentes_med);
		?>
        <input type="hidden" id="hdd_cant_contactos_antecedentes_med" value="<?php echo(count($lista_contactos)); ?>" />
        <?php
			if (count($lista_contactos) > 0) {
				$antecedente_ant = "";
				foreach ($lista_contactos as $contacto_aux) {
					if ($contacto_aux["nombre_antecedentes_medicos"] != $antecedente_ant) {
		?>
        <div class="div_subtitulo"><b><?php echo($contacto_aux["nombre_antecedentes_medicos"]); ?></b></div>
        <?php
					}
		?>
        <b><?php echo($contacto_aux["nombre_contacto"]); ?></b><br />(<?php echo($contacto_aux["especialidad"]); ?>)<br />
        <?php
        	if ($contacto_aux["direccion"] != "") {
				echo($contacto_aux["direccion"]."<br />");
			}
			
			$telefonos_aux = $contacto_aux["telefono_1"];
			if ($contacto_aux["telefono_2"] != "") {
				$telefonos_aux .= " - ".$contacto_aux["telefono_2"];
			}
		?>
        <b>Tel:</b>&nbsp;<?php echo($telefonos_aux); ?><br />
        <hr />
        <?php
					$antecedente_ant = $contacto_aux["nombre_antecedentes_medicos"];
				}
			}
			break;
	}
?>
