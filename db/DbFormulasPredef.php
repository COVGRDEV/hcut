<?php
require_once("DbConexion.php");

class DbFormulasPredef extends DbConexion {
	/*
	 * Esta funcion obtiene los registros de formulas_predef
	 */
	public function getFormulas() {
		try {
			$sql = "SELECT * FROM formulas_predef ORDER BY id_formula";
			
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	/*
	 * Esta funcion busca los registros de formulas_predef
	 */
	public function buscarFormulas($parametro) {
		try {
			$parametro = str_replace(" ", "%", $parametro);
			
			$sql = "SELECT *
					FROM formulas_predef
					WHERE id_formula LIKE '%".$parametro."%'
					OR titulo_formula LIKE '%".$parametro."%'
					OR text_formulas LIKE '%".$parametro."%'
					ORDER BY id_formula";
			
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}
		
	/*
	 * Esta funcion busca el registro de formulas_predef
	 */
	public function buscarFormula($parametro) {
		try {
			$parametro = str_replace(" ", "%", $parametro);
			
			$sql = "SELECT * 
					FROM formulas_predef
					WHERE id_formula = $parametro 
					ORDER BY id_formula";
			
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	/*
	 * Esta funcion guarda o modifica el registro
	 */
	public function guardarModificarFormula($accion, $idFormula, $tituloFormula, $textFormula, $indActivo, $idUsuario) {
		try {
			$sql = "CALL pa_crear_modificar_plantilla_formula(".$accion.", ".$idFormula.", '".$tituloFormula."', '".$textFormula."', ".$indActivo.", ".$idUsuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			$resultado_out = $arrResultado["@id"];
			
			return $resultado_out;
		} catch (Exception $e) {
			return array();
		}
	}
	
		public function actualizarFormulaPredef($id_formula, $text_formulas) {
			try {
				$sql = "UPDATE formulas_predef
						SET text_formulas='".$text_formulas."'
						WHERE id_formula=".$id_formula;
				
				$this->ejecutarSentencia($sql, array());
				
				return 1;
	        } catch (Exception $e) {
	            return -2;
	        }
    	}
}
?>
