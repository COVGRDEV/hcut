<?php
	header("Content-Type: text/xml; charset=UTF-8");
	
	session_start();
	
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/LiquidadorPrecios.php");
	require_once("../db/DbListas.php");
	
	$utilidades = new Utilidades();
	$liquidadorPrecios = new LiquidadorPrecios();
	$dbListas = new DbListas();
	$opcion = $_POST["opcion"];
	
	switch ($opcion) {
		case "1": //Se recalculan los valores de procedimientos quirúrgicos, copagos y cuotas moderadoras
			@$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
			@$id_plan = $utilidades->str_decode($_POST["id_plan"]);
			@$tipo_coti_paciente = $utilidades->str_decode($_POST["tipo_coti_paciente"]);
			@$rango_paciente = $utilidades->str_decode($_POST["rango_paciente"]);
			@$cant_registros = intval($_POST["cant_registros"], 10);
			
			//Se obtienen los valores de procedimientos quirúrgicos
			$arr_procedimientos_cx = array();
			for ($i = 0; $i < $cant_registros; $i++) {
				$tipo_precio_aux = $utilidades->str_decode($_POST["tipo_precio_".$i]);
				$id_plan_aux = $utilidades->str_decode($_POST["id_plan_".$i]);
				if ($tipo_precio_aux == "P" && $id_plan_aux == $id_plan) {
					$arr_aux["orden"] = intval($_POST["orden_".$i], 10);
					$arr_aux["cod_procedimiento"] = $utilidades->str_decode($_POST["cod_producto_".$i]);
					$arr_aux["tipo_bilateral"] = $utilidades->str_decode($_POST["tipo_bilateral_".$i]);
					array_push($arr_procedimientos_cx, $arr_aux);
				}
			}
			
			$mapa_precios_cx = $liquidadorPrecios->liquidar_cirugias($id_plan, $arr_procedimientos_cx);
			
			
			//Se obtienen los valores para copagos y cuotas moderadoras
			$arr_productos = array();
			for ($i = 0; $i < $cant_registros; $i++) {
				$orden_aux = intval($_POST["orden_".$i], 10);
				$arr_aux = array();
				$arr_aux["orden"] = $orden_aux;
				$arr_aux["tipo_precio"] = $utilidades->str_decode($_POST["tipo_precio_".$i]);
				$arr_aux["cod_producto"] = $utilidades->str_decode($_POST["cod_producto_".$i]);
				$arr_aux["tipo_bilateral"] = $utilidades->str_decode($_POST["tipo_bilateral_".$i]);
				$arr_aux["id_plan"] = $utilidades->str_decode($_POST["id_plan_".$i]);
				if (isset($mapa_precios_cx[$orden_aux])) {
					$arr_aux["valor"] = $mapa_precios_cx[$orden_aux]["valor_total"];
				} else {
					$arr_aux["valor"] = $utilidades->str_decode($_POST["valor_".$i]);
				}
				$arr_aux["valor_cuota"] = $utilidades->str_decode($_POST["valor_cuota_".$i]);
				$arr_aux["cantidad"] = $utilidades->str_decode($_POST["cantidad_".$i]);
				array_push($arr_productos, $arr_aux);
			}
			
			if(intval($tipo_coti_paciente)>3){
				$tipo_coti_paciente_obj = $dbListas->getDetalle($tipo_coti_paciente);
				$tipo_coti_paciente = $tipo_coti_paciente_obj["codigo_detalle"]; 
			}
			
			$mapa_precios_cop_cm = $liquidadorPrecios->liquidar_cop_cm($id_paciente, $arr_productos, $tipo_coti_paciente, $rango_paciente);
			
			$cont_aux = 0;
			foreach ($mapa_precios_cx as $orden => $precio_aux) {
		?>
		<input type="hidden" id="hdd_orden_cal_cx_<?php echo($cont_aux); ?>" name="hdd_orden_cal_cx_<?php echo($cont_aux); ?>" value="<?php echo($orden); ?>" />
		<input type="hidden" id="hdd_valor_cal_cx_<?php echo($cont_aux); ?>" name="hdd_valor_cal_cx_<?php echo($cont_aux); ?>" value="<?php echo($precio_aux["valor_total"]); ?>" />
		<?php
				$cont_aux++;
			}
		?>
		<input type="hidden" id="hdd_cantidad_cal_cx" name="hdd_cantidad_cal_cx" value="<?php echo($cont_aux); ?>" />
		<?php
		
			$cont_aux = 0;
			foreach ($mapa_precios_cop_cm as $orden => $precio_aux) {
		?>
		<input type="hidden" id="hdd_orden_cop_cm_<?php echo($cont_aux); ?>" name="hdd_orden_cop_cm_<?php echo($cont_aux); ?>" value="<?php echo($orden); ?>" />
		<input type="hidden" id="hdd_valor_cop_cm_<?php echo($cont_aux); ?>" name="hdd_valor_cop_cm_<?php echo($cont_aux); ?>" value="<?php echo($precio_aux["valor_cuota"]); ?>" />
		<?php
				$cont_aux++;
			}
		?>
		<input type="hidden" id="hdd_cantidad_cop_cm" name="hdd_cantidad_cop_cm" value="<?php echo($cont_aux); ?>" />
		<?php
			break;
	}
?>
