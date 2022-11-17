<?php
	class Color_Pick {
		private $arr_colores = array();
		private $cant_colores_linea = 100;
		private $sufijo = "";
		
		function __construct($arr_cadenas_colores, $cant_colores, $sufijo = "") {
			if ($sufijo != "") {
				$this->sufijo = "_".$sufijo;
			}
			
			$arr_colores = array();
			foreach ($arr_cadenas_colores as $cadena_colores) {
				for ($i = 0; $i < strlen($cadena_colores); $i++) {
					array_push($arr_colores, $cadena_colores{$i});
				}
			}
			for ($i = count($arr_colores); $i < $cant_colores; $i++) {
				array_push($arr_colores, "0");
			}
			
			$this->arr_colores = $arr_colores;
		}
		
		public function getArrayColores() {
			return $this->arr_colores;
		}
		
		public function getColorPick($id_componente, $indice, $estilo = "") {
			$color_inicio = intval($this->arr_colores[$indice]) % 4;
	?>
    <input type="hidden" id="hdd_cp_componente<?php echo($this->sufijo."_".$indice); ?>" value="<?php echo($id_componente); ?>" />
    <input type="hidden" id="hdd_cp_color<?php echo($this->sufijo."_".$indice); ?>" value="<?php echo($color_inicio); ?>" />
    <div id="d_cp<?php echo($this->sufijo."_".$indice); ?>" class="div_color_pick color_pick_<?php echo($color_inicio); ?>" style="<?php echo($estilo); ?>" onclick="cambiar_color_pick('<?php echo($this->sufijo."_".$indice); ?>');"></div>
    <?php
		}
		
		public function getListasColores($cadena_colores) {
			$arr_cadenas_colores = array();
			while (strlen($cadena_colores) > 0) {
				array_push($arr_cadenas_colores, substr($cadena_colores, 0, $this->cant_colores_linea));
				$cadena_colores = substr($cadena_colores, $this->cant_colores_linea);
			}
			
			return $arr_cadenas_colores;
		}
	}
?>
