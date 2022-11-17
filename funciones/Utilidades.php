<?php
class Utilidades {
	private $arr_enmascarar = array("1" => "A", "2" => "B", "3" => "C", "4" => "D", "5" => "E", "6" => "F", "7" => "G", "8" => "H", "9" => "I", "0" => "Z");
	
    function limpiar_tags($tags) {
        $tags = strip_tags($tags);
        $tags = stripslashes($tags);
        $tags = htmlentities($tags);
		
        return $tags;
    }
	
	function str_encode($texto) {
		$texto_rta = str_replace("+", "|PLUS|", $texto);
		$texto_rta = str_replace(chr(10), "|ENTER|", $texto_rta);
		$texto_rta = str_replace("&", "|AMP|", $texto_rta);
		$texto_rta = str_replace("'", "", $texto_rta);
		$texto_rta = str_replace('"', "", $texto_rta);
		
		return $texto_rta;
	}
	
	function str_decode($texto) {
		$texto_rta = str_replace("|PLUS|", "+", $texto);
		$texto_rta = str_replace("|ENTER|", chr(10), $texto_rta);
		$texto_rta = str_replace("|AMP|", "&", $texto_rta);
		$texto_rta = str_replace("'", "", $texto_rta);
		$texto_rta = str_replace('"', "", $texto_rta);
		
		return $texto_rta;
	}
	
	function get_extension_arch($nombre_arch) {
		$pos_punto = strrpos($nombre_arch, ".", -1);
		$extension = strtolower(substr($nombre_arch, $pos_punto + 1));
		
		return $extension;
	}
	
	//Funcio para cambiar las variables que llegan por get a post
	function get_a_post(){
		//Cambiar variables get a post
		if ($_GET) {
			$keys_get = array_keys($_GET);
			foreach ($keys_get as $key_get) {
				$_POST[$key_get] = $_GET[$key_get];
				error_log("variable $key_get viene desde $ _GET");
			 }
		}
	}
	
	function convertir_a_mayusculas($texto) {
		$texto = str_replace("á", "Á", $texto);
		$texto = str_replace("é", "É", $texto);
		$texto = str_replace("í", "Í", $texto);
		$texto = str_replace("ó", "Ó", $texto);
		$texto = str_replace("ú", "Ú", $texto);
		$texto = str_replace("ñ", "Ñ", $texto);
		$texto = str_replace("ü", "Ü", $texto);
		$texto = strtoupper($texto);
		
		return $texto;
	}
	
	function convertir_a_minusculas($texto) {
		$texto = str_replace("Á", "á", $texto);
		$texto = str_replace("É", "é", $texto);
		$texto = str_replace("Í", "í", $texto);
		$texto = str_replace("Ó", "ó", $texto);
		$texto = str_replace("Ú", "ú", $texto);
		$texto = str_replace("Ñ", "ñ", $texto);
		$texto = str_replace("Ü", "ü", $texto);
		$texto = strtolower($texto);
		
		return $texto;
	}
	
	//Función para ajustar los textos de observaciones a los editores wysiwyg
	function ajustar_texto_wysiwyg($texto) {
		$pos = strpos($texto, "<p>");
		if ($pos === false) {
			$texto = "<p>".$texto."</p>";
			$pos = strpos($texto, chr(10));
			if ($pos !== false) {
				$texto = str_replace(chr(10), "</p><p>", $texto);
				$texto = str_replace(chr(13), "", $texto);
			} else {
				$pos = strpos($texto, chr(13));
				if ($pos !== false) {
					$texto = str_replace(chr(10), "", $texto);
					$texto = str_replace(chr(13), "</p><p>", $texto);
				}
			}
		}
		
		return $texto;
	}
	
	function enmascarar_valor($valor) {
		//Se transforma el valor en un número
		$valor = intval($valor, 10);
		
		//Se obtiene el valor en miles
		@$valor_base = floor($valor/1000) + "";
		
		//Se enmascara el valor base
		foreach ($this->arr_enmascarar as $llave => $resul) {
			$valor_base = str_replace($llave, $resul, $valor_base);
		}
		
		return $valor_base;
	}
	
	function crear_formato_corto_factura($num_factura) {
		//Se recorre el número de factura por cada uno de sus caracteres
		$resultado = "";
		$ind_borrar_ceros = true;
		for ($i = 0; $i < strlen($num_factura); $i++) {
			$caracter_aux = substr($num_factura, $i, 1);
			if (strpos("0123456789", $caracter_aux) !== false) {
				if (strpos("123456789", $caracter_aux) !== false) {
					$ind_borrar_ceros = false;
					$resultado .= $caracter_aux;
				} else if (!$ind_borrar_ceros) {
					$resultado .= $caracter_aux;
				}
			} else {
				$resultado .= $caracter_aux;
			}
		}
		
		return $resultado;
	}
}
?>
