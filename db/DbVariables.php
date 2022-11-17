<?php
require_once("DbConexion.php");

class Dbvariables extends DbConexion {
    public function getVariable($id) {
        try {
            $sql = "SELECT * 
                    FROM variables 
                    WHERE id_variable = $id";
			
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
    public function getVariables($id) {
        try {
            $sql = "SELECT * 
                    FROM variables 
                    WHERE id_variable = $id";
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
    //Fecha del servidor de base de datos
    public function getFechaactual() {
        try {
            $sql = "SELECT DATE_FORMAT(NOW(),'%Y-%m-%d') AS fecha_actual";
          
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
	//Fecha del servidor de base de datos format
	public function getFechaActualMostrar() {
		try {
			$sql = "SELECT DATE_FORMAT(NOW(),'%d/%m/%Y') AS fecha_actual_mostrar,
					DATE_FORMAT(NOW(),'%h:%i %p') AS hora_actual_mostrar,
					DATE_FORMAT(NOW(),'%H:%i') AS hora24_actual_mostrar";
			
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	//Fecha del servidor de base de datos
    public function getAnoMesDia() {
        try {
            $sql = "SELECT DATE_FORMAT(NOW(),'%Y') AS anio_actual, DATE_FORMAT(NOW(),'%m') AS mes_actual, DATE_FORMAT(NOW(),'%d') AS dia_actual";
          
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
    public function getListaVariables() {
        try {
            $sql = "SELECT * ".
                   "FROM variables ".
                   "ORDER BY nombre_variable";
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
    public function actualizar_variable($id_variable, $nombre_variable, $descripcion_variable, $tipo_variable, $valor_variable, $id_usuario) {
		try {
			$sql = "CALL pa_editar_variable(".$id_variable.", '".$nombre_variable."', '".$descripcion_variable."', ".
				   $tipo_variable.", '".$valor_variable."', ".$id_usuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		} catch (Exception $e) {
			return -2;
		}
	}
	
	
	/*
	 * $fecha= en formato  '%d/%m/%Y'
	 * $dias= numero de dias
	 */
	public function sumar_dias_fecha($fecha, $dias) {
		try {
			$sql = "SELECT DATE_FORMAT(DATE_ADD(STR_TO_DATE('$fecha', '%d/%m/%Y'), INTERVAL $dias DAY), '%d/%m/%Y') AS fecha_resultado";
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return -2;
		}
	}
	
	
	/*
	 * $fecha= en formato  '%d/%m/%Y'
	 * $dias= numero de dias
	 */
	public function restar_dias_fecha($fecha, $dias) {
		try {
			$sql = "SELECT DATE_FORMAT(DATE_SUB(STR_TO_DATE('$fecha', '%d/%m/%Y'), INTERVAL $dias DAY), '%d/%m/%Y') AS fecha_resultado";
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return -2;
		}
	}
	
	//Fecha del servidor de base de datos format
	//Tipo formato: 1. dd/mm/aaaa - 2. aaaa-mm-dd
	public function getDiferenciaFechas($fecha_inicial, $fecha_final, $tipo_formato) {
		try {
			switch ($tipo_formato) {
				case 1: //dd/mm/aaaa
					$sql = "SELECT DATEDIFF(STR_TO_DATE('".$fecha_final."', '%d/%m/%Y'), STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y')) AS dias";
					break;
				case 2: //aaaa-mm-dd
					$sql = "SELECT DATEDIFF('".$fecha_final."', '".$fecha_inicial."') AS dias";
					

					break;		
			}
		 //echo($sql."<br>");
		
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	
	public function cambiarFormatoFecha($fecha){
		if(!is_null($fecha) || !empty($fecha)){
			$fecha = explode('-', $fecha);
			$año = $fecha[0];
			$mes = $fecha[1];
			$dia = $fecha[2];	
			$fecha_mod = $dia."/".$mes."/".$año;
			
		}else{
		$fecha_mod = "";	
		}
		return $fecha_mod;
	
	}
	
}
?>
