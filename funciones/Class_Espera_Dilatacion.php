<?php
	require_once("../db/DbAdmision.php");
	
	/*
		Clase para el componente de pacientes en espera por dilatación de pupila
		Autor: Feisar Moreno - 02/10/2017
	*/
	class Class_Espera_Dilatacion {
		function __construct() {
			
		}
		
		public function getEsperaDilatacion($id_admision, $ind_dilatado = -1) {
			if ($ind_dilatado == -1) {
				//Se busca el valor de dilatación
				$dbAdmision = new DbAdmision();
				$admision_obj = $dbAdmision->get_admision($id_admision);
				if (isset($admision_obj["id_admision"])) {
					$ind_dilatado = ($admision_obj["id_tipo_espera"] != "" ? 1 : 0);
				}
			}
			$checked_aux = "";
			if ($ind_dilatado == 1) {
				$checked_aux = 'checked="checked"';
			}
	?>
    <div id="d_espera_dilatacion" style="display:none;"></div>
    <input type="checkbox" id="chk_dilatado" <?php echo($checked_aux); ?> class="no-margin" onchange="cambiar_espera_dilatacion(<?php echo($id_admision); ?>);" />
    &nbsp;<b>En dilataci&oacute;n de pupila</b>
    <?php
		}
	}
?>
