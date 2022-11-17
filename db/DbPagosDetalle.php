<?php
require_once("DbConexion.php");

class DbPagosDetalle extends DbConexion {
	//Funcion para el formulario Registrar Pago - Guarda, Edita o elimina el pago_detalle para la admision seleccionada
	public function creaEditaModifica($idAdmision, $codServicio, $tipoBilateral, $cantidad, $valor, $valor_cuota, $tipoPrecio, $idUsuarioCrea) {
		try {
			if ($idAdmision == "") {
				$idAdmision = "NULL";
			}
			if ($valor_cuota == "") {
				$valor_cuota = "0";
			}
			$sql = "CALL pa_crear_editar_pagos_detalle(".$idAdmision.", '".$codServicio."', ".$tipoBilateral.", ".$cantidad.", ".$valor.", ".$valor_cuota.", '".$tipoPrecio."', ".$idUsuarioCrea.", @id)";
			//echo($sql);
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			$resultado_out = $arrResultado["@id"];
			
			return $resultado_out;
		} catch (Exception $e) {
			return array();
		}
	}
	
	//Funcion para el formulario Registrar Pago - Guarda, Edita o elimina el pago_detalle para la admision seleccionada
	public function borrar_temporal_pagos_detalle_pagos($id_usuario) {
		try {
			$sql = "DELETE FROM temporal_pagos_detalle_pagos
					WHERE id_usuario_crea=".$id_usuario;
			
			$arrResultado = $this->ejecutarSentencia($sql, array());
			
			return 1;
		} catch (Exception $e) {
			return -1;
		}
	}
}
?>
