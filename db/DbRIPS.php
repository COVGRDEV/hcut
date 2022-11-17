<?php
	require_once("DbConexion.php");
	
	class DbRIPS extends DbConexion {
		public function obtener_consecutivo() {
			try {
				$sql = "CALL pa_obtener_consecutivo_rips(@id)";
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				
				return $arrResultado["@id"];
			} catch (Exception $e) {
				return -2;
			}
		}
		
		public function crear_editar_rips($id_rips, $id_convenio, $id_plan, $fecha_ini, $fecha_fin, $num_consecutivo, $tipo_factura, $num_factura, $id_usuario) {
			try {
				if ($id_plan == "") {
					$id_plan = "NULL";
				}
				$fecha_ini = "STR_TO_DATE('".$fecha_ini."', '%d/%m/%Y')";
				$fecha_fin = "STR_TO_DATE('".$fecha_fin."', '%d/%m/%Y')";
				if ($num_consecutivo == "") {
					$num_consecutivo = "NULL";
				}
				if ($num_factura == "") {
					$num_factura = "NULL";
				} else {
					$num_factura = "'".$num_factura."'";
				}
				
				$sql = "CALL pa_crear_editar_rips(".$id_rips.", ".$id_convenio.", ".$id_plan.", ".$fecha_ini.", ".
						$fecha_fin.", ".$num_consecutivo.", ".$tipo_factura.", ".$num_factura.", ".$id_usuario.", @id)";
				//echo($sql);
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				
				return $arrResultado["@id"];
			} catch (Exception $e) {
				return -2;
			}
		}
		
		public function crear_rips_consulta($id_rips_ac, $id_detalle_precio, $id_admision, $id_paciente, $id_hc, $ind_revisado,
				$ind_borrado, $num_factura, $id_convenio, $id_plan, $cod_prestador, $tipo_documento, $numero_documento, $fecha_consulta,
				$num_autorizacion, $cod_procedimiento, $fin_consulta, $causa_ext, $cod_ciex_prin, $cod_ciex_rel1, $cod_ciex_rel2,
				$cod_ciex_rel3, $tipo_diag_prin, $valor_consulta, $valor_cuota, $observaciones, $id_usuario) {
			try {
				if ($id_detalle_precio == "") {
					$id_detalle_precio = "NULL";
				}
				if ($id_admision == "") {
					$id_admision = "NULL";
				}
				if ($id_paciente == "") {
					$id_paciente = "NULL";
				}
				if ($id_hc == "") {
					$id_hc = "NULL";
				}
				if ($num_factura == "") {
					$num_factura = "NULL";
				} else {
					$num_factura = "'".$num_factura."'";
				}
				if ($cod_prestador == "") {
					$cod_prestador = "NULL";
				} else {
					$cod_prestador = "'".$cod_prestador."'";
				}
				if ($tipo_documento == "") {
					$tipo_documento = "NULL";
				} else {
					$tipo_documento = "'".$tipo_documento."'";
				}
				if ($numero_documento == "") {
					$numero_documento = "NULL";
				} else {
					$numero_documento = "'".$numero_documento."'";
				}
				$fecha_consulta = "STR_TO_DATE('".$fecha_consulta."', '%d/%m/%Y')";
				if ($num_autorizacion == "") {
					$num_autorizacion = "NULL";
				} else {
					$num_autorizacion = "'".$num_autorizacion."'";
				}
				if ($cod_procedimiento == "") {
					$cod_procedimiento = "NULL";
				} else {
					$cod_procedimiento = "'".$cod_procedimiento."'";
				}
				if ($fin_consulta == "") {
					$fin_consulta = "NULL";
				} else {
					$fin_consulta = "'".$fin_consulta."'";
				}
				if ($causa_ext == "") {
					$causa_ext = "NULL";
				} else {
					$causa_ext = "'".$causa_ext."'";
				}
				if ($cod_ciex_prin == "") {
					$cod_ciex_prin = "NULL";
				} else {
					$cod_ciex_prin = "'".$cod_ciex_prin."'";
				}
				if ($cod_ciex_rel1 == "") {
					$cod_ciex_rel1 = "NULL";
				} else {
					$cod_ciex_rel1 = "'".$cod_ciex_rel1."'";
				}
				if ($cod_ciex_rel2 == "") {
					$cod_ciex_rel2 = "NULL";
				} else {
					$cod_ciex_rel2 = "'".$cod_ciex_rel2."'";
				}
				if ($cod_ciex_rel3 == "") {
					$cod_ciex_rel3 = "NULL";
				} else {
					$cod_ciex_rel3 = "'".$cod_ciex_rel3."'";
				}
				if ($tipo_diag_prin == "") {
					$tipo_diag_prin = "NULL";
				} else {
					$tipo_diag_prin = "'".$tipo_diag_prin."'";
				}
				if ($valor_consulta == "") {
					$valor_consulta = "0.0";
				}
				if ($valor_cuota == "") {
					$valor_cuota = "0.0";
				}
				if ($observaciones == "") {
					$observaciones = "NULL";
				} else {
					$observaciones = "'".$observaciones."'";
				}
				
				$sql = "CALL pa_crear_rips_consultas(".$id_rips_ac.", ".$id_detalle_precio.", ".$id_admision.", ".$id_paciente.", ".$id_hc.", ".
						$ind_revisado.", ".$ind_borrado.", ".$num_factura.", ".$id_convenio.", ".$id_plan.", ".$cod_prestador.", ".$tipo_documento.", ".
						$numero_documento.", ".$fecha_consulta.", ".$num_autorizacion.", ".$cod_procedimiento.", ".$fin_consulta.", ".
						$causa_ext.", ".$cod_ciex_prin.", ".$cod_ciex_rel1.", ".$cod_ciex_rel2.", ".$cod_ciex_rel3.", ".
						$tipo_diag_prin.", ".$valor_consulta.", ".$valor_cuota.", ".$observaciones.", ".$id_usuario.", @id)";
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				
				return $arrResultado["@id"];
			} catch (Exception $e) {
				return -2;
			}
		}
		
		public function crear_rips_procedimiento($id_rips_ap, $id_detalle_precio, $id_admision, $id_paciente, $id_hc, $ind_revisado, $ind_borrado, $num_factura,
				$id_convenio, $id_plan, $cod_prestador, $tipo_documento, $numero_documento, $fecha_pro, $num_autorizacion, $cod_procedimiento, $amb_rea, $fin_pro,
				$per_ati, $cod_ciex_prin, $cod_ciex_rel, $cod_ciex_com, $for_rea, $valor_pro, $valor_copago, $observaciones, $id_usuario) {
			try {
				if ($id_detalle_precio == "") {
					$id_detalle_precio = "NULL";
				}
				if ($id_admision == "") {
					$id_admision = "NULL";
				}
				if ($id_paciente == "") {
					$id_paciente = "NULL";
				}
				if ($id_hc == "") {
					$id_hc = "NULL";
				}
				if ($num_factura == "") {
					$num_factura = "NULL";
				} else {
					$num_factura = "'".$num_factura."'";
				}
				if ($cod_prestador == "") {
					$cod_prestador = "NULL";
				} else {
					$cod_prestador = "'".$cod_prestador."'";
				}
				if ($tipo_documento == "") {
					$tipo_documento = "NULL";
				} else {
					$tipo_documento = "'".$tipo_documento."'";
				}
				if ($numero_documento == "") {
					$numero_documento = "NULL";
				} else {
					$numero_documento = "'".$numero_documento."'";
				}
				$fecha_pro = "STR_TO_DATE('".$fecha_pro."', '%d/%m/%Y')";
				if ($num_autorizacion == "") {
					$num_autorizacion = "NULL";
				} else {
					$num_autorizacion = "'".$num_autorizacion."'";
				}
				if ($cod_procedimiento == "") {
					$cod_procedimiento = "NULL";
				} else {
					$cod_procedimiento = "'".$cod_procedimiento."'";
				}
				if ($amb_rea == "") {
					$amb_rea = "NULL";
				} else {
					$amb_rea = "'".$amb_rea."'";
				}
				if ($fin_pro == "") {
					$fin_pro = "NULL";
				} else {
					$fin_pro = "'".$fin_pro."'";
				}
				if ($per_ati == "") {
					$per_ati = "NULL";
				} else {
					$per_ati = "'".$per_ati."'";
				}
				if ($cod_ciex_prin == "") {
					$cod_ciex_prin = "NULL";
				} else {
					$cod_ciex_prin = "'".$cod_ciex_prin."'";
				}
				if ($cod_ciex_rel == "") {
					$cod_ciex_rel = "NULL";
				} else {
					$cod_ciex_rel = "'".$cod_ciex_rel."'";
				}
				if ($cod_ciex_com == "") {
					$cod_ciex_com = "NULL";
				} else {
					$cod_ciex_com = "'".$cod_ciex_com."'";
				}
				if ($for_rea == "") {
					$for_rea = "NULL";
				} else {
					$for_rea = "'".$for_rea."'";
				}
				if ($valor_pro == "") {
					$valor_pro = "0.0";
				}
				if ($valor_copago == "") {
					$valor_copago = "0.0";
				}
				if ($observaciones == "") {
					$observaciones = "NULL";
				} else {
					$observaciones = "'".$observaciones."'";
				}
				
				$sql = "CALL pa_crear_rips_procedimientos(".$id_rips_ap.", ".$id_detalle_precio.", ".$id_admision.", ".$id_paciente.", ".
						$id_hc.", ".$ind_revisado.", ".$ind_borrado.", ".$num_factura.", ".$id_convenio.", ".$id_plan.", ".$cod_prestador.", ".
						$tipo_documento.", ".$numero_documento.", ".$fecha_pro.", ".$num_autorizacion.", ".$cod_procedimiento.", ".
						$amb_rea.", ".$fin_pro.", ".$per_ati.", ".$cod_ciex_prin.", ".$cod_ciex_rel.", ".$cod_ciex_com.", ".$for_rea.", ".
						$valor_pro.", ".$valor_copago.", ".$observaciones.", ".$id_usuario.", @id)";
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				
				return $arrResultado["@id"];
			} catch (Exception $e) {
				return -2;
			}
		}
		
		public function crear_rips_medicamento($id_rips_am, $id_detalle_precio, $id_admision, $id_paciente, $ind_revisado, $ind_borrado,
				$num_factura, $id_convenio, $id_plan, $cod_prestador, $tipo_documento, $numero_documento, $fecha_medicamento,
				$num_autorizacion, $cod_medicamento, $tipo_medicamento, $nombre_generico, $forma_farma, $concentracion, $unidad_medida,
				$cantidad, $valor_medicamento, $observaciones, $id_usuario) {
			try {
				if ($id_detalle_precio == "") {
					$id_detalle_precio = "NULL";
				}
				if ($id_admision == "") {
					$id_admision = "NULL";
				}
				if ($id_paciente == "") {
					$id_paciente = "NULL";
				}
				if ($num_factura == "") {
					$num_factura = "NULL";
				} else {
					$num_factura = "'".$num_factura."'";
				}
				if ($cod_prestador == "") {
					$cod_prestador = "NULL";
				} else {
					$cod_prestador = "'".$cod_prestador."'";
				}
				if ($tipo_documento == "") {
					$tipo_documento = "NULL";
				} else {
					$tipo_documento = "'".$tipo_documento."'";
				}
				if ($numero_documento == "") {
					$numero_documento = "NULL";
				} else {
					$numero_documento = "'".$numero_documento."'";
				}
				$fecha_medicamento = "STR_TO_DATE('".$fecha_medicamento."', '%d/%m/%Y')";
				if ($num_autorizacion == "") {
					$num_autorizacion = "NULL";
				} else {
					$num_autorizacion = "'".$num_autorizacion."'";
				}
				if ($cod_medicamento == "") {
					$cod_medicamento = "NULL";
				}
				if ($tipo_medicamento == "") {
					$tipo_medicamento = "NULL";
				} else {
					$tipo_medicamento = "'".$tipo_medicamento."'";
				}
				if ($nombre_generico == "") {
					$nombre_generico = "NULL";
				} else {
					$nombre_generico = "'".$nombre_generico."'";
				}
				if ($forma_farma == "") {
					$forma_farma = "NULL";
				} else {
					$forma_farma = "'".$forma_farma."'";
				}
				if ($concentracion == "") {
					$concentracion = "NULL";
				} else {
					$concentracion = "'".$concentracion."'";
				}
				if ($unidad_medida == "") {
					$unidad_medida = "NULL";
				} else {
					$unidad_medida = "'".$unidad_medida."'";
				}
				if ($cantidad == "") {
					$cantidad = "0";
				}
				if ($valor_medicamento == "") {
					$valor_medicamento = "0.0";
				}
				if ($observaciones == "") {
					$observaciones = "NULL";
				} else {
					$observaciones = "'".$observaciones."'";
				}
				
				$sql = "CALL pa_crear_rips_medicamentos(".$id_rips_am.", ".$id_detalle_precio.", ".$id_admision.", ".$id_paciente.", ".
						$ind_revisado.", ".$ind_borrado.", ".$num_factura.", ".$id_convenio.", ".$id_plan.", ".$cod_prestador.", ".$tipo_documento.", ".
						$numero_documento.", ".$fecha_medicamento.", ".$num_autorizacion.", ".$cod_medicamento.", ".$tipo_medicamento.", ".
						$nombre_generico.", ".$forma_farma.", ".$concentracion.", ".$unidad_medida.", ".$cantidad.", ".
						$valor_medicamento.", ".$observaciones.", ".$id_usuario.", @id)";
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				
				return $arrResultado["@id"];
			} catch (Exception $e) {
				return -2;
			}
		}
		
		public function crear_rips_otros_servicios($id_rips_at, $id_detalle_precio, $id_admision, $id_paciente, $ind_revisado, $ind_borrado,
				$num_factura, $id_convenio, $id_plan, $cod_prestador, $tipo_documento, $numero_documento, $fecha_insumo, $num_autorizacion,
				$tipo_insumo, $cod_insumo, $nombre_insumo, $cantidad, $valor_insumo, $observaciones, $id_usuario) {
			try {
				if ($id_detalle_precio == "") {
					$id_detalle_precio = "NULL";
				}
				if ($id_admision == "") {
					$id_admision = "NULL";
				}
				if ($id_paciente == "") {
					$id_paciente = "NULL";
				}
				if ($num_factura == "") {
					$num_factura = "NULL";
				} else {
					$num_factura = "'".$num_factura."'";
				}
				if ($cod_prestador == "") {
					$cod_prestador = "NULL";
				} else {
					$cod_prestador = "'".$cod_prestador."'";
				}
				if ($tipo_documento == "") {
					$tipo_documento = "NULL";
				} else {
					$tipo_documento = "'".$tipo_documento."'";
				}
				if ($numero_documento == "") {
					$numero_documento = "NULL";
				} else {
					$numero_documento = "'".$numero_documento."'";
				}
				$fecha_insumo = "STR_TO_DATE('".$fecha_insumo."', '%d/%m/%Y')";
				if ($num_autorizacion == "") {
					$num_autorizacion = "NULL";
				} else {
					$num_autorizacion = "'".$num_autorizacion."'";
				}
				if ($tipo_insumo == "") {
					$tipo_insumo = "NULL";
				} else {
					$tipo_insumo = "'".$tipo_insumo."'";
				}
				if ($cod_insumo == "") {
					$cod_insumo = "NULL";
				}
				if ($nombre_insumo == "") {
					$nombre_insumo = "NULL";
				} else {
					$nombre_insumo = "'".$nombre_insumo."'";
				}
				if ($cantidad == "") {
					$cantidad = "0";
				}
				if ($valor_insumo == "") {
					$valor_insumo = "0.0";
				}
				if ($observaciones == "") {
					$observaciones = "NULL";
				} else {
					$observaciones = "'".$observaciones."'";
				}
				
				$sql = "CALL pa_crear_rips_otros_servicios(".$id_rips_at.", ".$id_detalle_precio.", ".$id_admision.", ".$id_paciente.", ".
						$ind_revisado.", ".$ind_borrado.", ".$num_factura.", ".$id_convenio.", ".$id_plan.", ".$cod_prestador.", ".$tipo_documento.", ".
						$numero_documento.", ".$fecha_insumo.", ".$num_autorizacion.", ".$tipo_insumo.", ".$cod_insumo.", ".$nombre_insumo.", ".
						$cantidad.", ".$valor_insumo.", ".$observaciones.", ".$id_usuario.", @id)";
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				
				return $arrResultado["@id"];
			} catch (Exception $e) {
				return -2;
			}
		}
		
		public function crear_rips_usuarios($id_rips_us, $id_rips, $id_paciente, $ind_revisado, $ind_borrado, $tipo_documento, $numero_documento, $nombre_1, $nombre_2,
				$apellido_1, $apellido_2, $cod_administradora, $tipo_usuario, $edad, $unidad_edad, $sexo, $cod_dep, $cod_mun, $zona, $fecha_nacimiento, 
				$cod_pais, $observaciones, $id_usuario) {

			try {
				if ($id_paciente == "") {
					$id_paciente = "NULL";
				}
				if ($tipo_documento == "") {
					$tipo_documento = "NULL";
				} else {
					$tipo_documento = "'".$tipo_documento."'";
				}
				if ($numero_documento == "") {
					$numero_documento = "NULL";
				} else {
					$numero_documento = "'".$numero_documento."'";
				}
				if ($nombre_1 == "") {
					$nombre_1 = "NULL";
				} else {
					$nombre_1 = "'".$nombre_1."'";
				}
				if ($nombre_2 == "") {
					$nombre_2 = "NULL";
				} else {
					$nombre_2 = "'".$nombre_2."'";
				}
				if ($apellido_1 == "") {
					$apellido_1 = "NULL";
				} else {
					$apellido_1 = "'".$apellido_1."'";
				}
				if ($apellido_2 == "") {
					$apellido_2 = "NULL";
				} else {
					$apellido_2 = "'".$apellido_2."'";
				}
				if ($cod_administradora == "") {
					$cod_administradora = "NULL";
				} else {
					$cod_administradora = "'".$cod_administradora."'";
				}
				if ($tipo_usuario == "") {
					$tipo_usuario = "NULL";
				} else {
					$tipo_usuario = "'".$tipo_usuario."'";
				}
				if ($edad == "") {
					$edad = "NULL";
				}
				if ($unidad_edad == "") {
					$unidad_edad = "NULL";
				} else {
					$unidad_edad = "'".$unidad_edad."'";
				}
				if ($sexo == "") {
					$sexo = "NULL";
				} else {
					$sexo = "'".$sexo."'";
				}
				if ($cod_dep == "") {
					$cod_dep = "NULL";
				} else {
					$cod_dep = "'".$cod_dep."'";
				}
				if ($cod_mun == "") {
					$cod_mun = "NULL";
				} else {
					$cod_mun = "'".$cod_mun."'";
				}
				if ($zona == "") {
					$zona = "NULL";
				} else {
					$zona = "'".$zona."'";
				}

				if ($fecha_nacimiento == "") {
					$fecha_nacimiento = "NULL";
				} else {
					$fecha_nacimiento = "'".$fecha_nacimiento."'";
				}

				if ($cod_pais == "") {
					$cod_pais = "NULL";
				} else {
					$cod_pais = "'".$cod_pais."'";
				}


				if ($observaciones == "") {
					$observaciones = "NULL";
				} else {
					$observaciones = "'".$observaciones."'";
				}
				
				$sql = "CALL pa_crear_rips_usuarios(".$id_rips_us.", ".$id_rips.", ".$id_paciente.", ".$ind_revisado.", ".$ind_borrado.", ".$tipo_documento.", ".
						$numero_documento.", ".$nombre_1.", ".$nombre_2.", ".$apellido_1.", ".$apellido_2.", ".$cod_administradora.", ".$tipo_usuario.", ".
						$edad.", ".$unidad_edad.", ".$sexo.", ".$cod_dep.", ".$cod_mun.", ".$zona.", ".$fecha_nacimiento.", ".$cod_pais.",
						".$observaciones.", ".$id_usuario.", @id)";
				echo($sql);
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				
				return $arrResultado["@id"];
			} catch (Exception $e) {
				return -2;
			}
		}
		
		
		public function crear_rips_facturas($id_rips, $cod_prestador, $nombre_prestador, $tipo_documento, $numero_documento, $num_fatura,
				$fecha_factura, $fecha_ini, $fecha_fin, $cod_administradora, $nombre_administradora, $num_contrato, $plan_benef,
				$num_poliza, $valor_copago, $valor_comision, $valor_descuento, $valor_neto, $id_usuario) {
			try {
				if ($cod_prestador == "") {
					$cod_prestador = "NULL";
				} else {
					$cod_prestador = "'".$cod_prestador."'";
				}
				if ($nombre_prestador == "") {
					$nombre_prestador = "NULL";
				} else {
					$nombre_prestador = "'".$nombre_prestador."'";
				}
				if ($tipo_documento == "") {
					$tipo_documento = "NULL";
				} else {
					$tipo_documento = "'".$tipo_documento."'";
				}
				if ($numero_documento == "") {
					$numero_documento = "NULL";
				} else {
					$numero_documento = "'".$numero_documento."'";
				}
				if ($num_fatura == "") {
					$num_fatura = "NULL";
				} else {
					$num_fatura = "'".$num_fatura."'";
				}
				if ($fecha_factura == "") {
					$fecha_factura = "NULL";
				} else {
					$fecha_factura = "STR_TO_DATE('".$fecha_factura."', '%d/%m/%Y')";
				}
				if ($fecha_ini == "") {
					$fecha_ini = "NULL";
				} else {
					$fecha_ini = "STR_TO_DATE('".$fecha_ini."', '%d/%m/%Y')";
				}
				if ($fecha_fin == "") {
					$fecha_fin = "NULL";
				} else {
					$fecha_fin = "STR_TO_DATE('".$fecha_fin."', '%d/%m/%Y')";
				}
				if ($cod_administradora == "") {
					$cod_administradora = "NULL";
				} else {
					$cod_administradora = "'".$cod_administradora."'";
				}
				if ($nombre_administradora == "") {
					$nombre_administradora = "NULL";
				} else {
					$nombre_administradora = "'".$nombre_administradora."'";
				}
				if ($num_contrato == "") {
					$num_contrato = "NULL";
				} else {
					$num_contrato = "'".$num_contrato."'";
				}
				if ($plan_benef == "") {
					$plan_benef = "NULL";
				} else {
					$plan_benef = "'".$plan_benef."'";
				}
				if ($num_poliza == "") {
					$num_poliza = "NULL";
				} else {
					$num_poliza = "'".$num_poliza."'";
				}
				if ($valor_copago == "") {
					$valor_copago = "NULL";
				}
				if ($valor_comision == "") {
					$valor_comision = "NULL";
				}
				if ($valor_descuento == "") {
					$valor_descuento = "NULL";
				}
				if ($valor_neto == "") {
					$valor_neto = "NULL";
				}
				
				$sql = "CALL pa_crear_rips_facturas(".$id_rips.", ".$cod_prestador.", ".$nombre_prestador.", ".$tipo_documento.", ".$numero_documento.", ".
						$num_fatura.", ".$fecha_factura.", ".$fecha_ini.", ".$fecha_fin.", ".$cod_administradora.", ".$nombre_administradora.", ".$num_contrato.", ".
						$plan_benef.", ".$num_poliza.", ".$valor_copago.", ".$valor_comision.", ".$valor_descuento.", ".$valor_neto.", ".$id_usuario.", @id)";
				//echo($sql);
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				
				return $arrResultado["@id"];
			} catch (Exception $e) {
				return -2;
			}
		}
		
		public function crear_rips_descripcion($id_rips, $num_factura, $cod_prestador, $cod_concepto, $cantidad, $valor_unitario, $id_usuario) {
			try {
				if ($num_factura == "") {
					$num_factura = "NULL";
				} else {
					$num_factura = "'".$num_factura."'";
				}
				if ($cod_prestador == "") {
					$cod_prestador = "NULL";
				} else {
					$cod_prestador = "'".$cod_prestador."'";
				}
				if ($cod_concepto == "") {
					$cod_concepto = "NULL";
				} else {
					$cod_concepto = "'".$cod_concepto."'";
				}
				if ($cantidad == "") {
					$cantidad = "NULL";
				}
				if ($valor_unitario == "") {
					$valor_unitario = "NULL";
				}
				
				$sql = "CALL pa_crear_rips_descripcion(".$id_rips.", ".$num_factura.", ".$cod_prestador.", ".
						$cod_concepto.", ".$cantidad.", ".$valor_unitario.", ".$id_usuario.", @id)";
				//echo($sql);
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				
				return $arrResultado["@id"];
			} catch (Exception $e) {
				return -2;
			}
		}
		
		public function borrar_rips_consultas($id_convenio, $id_plan, $fecha_inicial, $fecha_final, $arr_id) {
			try {
				$cadena_aux = "";
				foreach ($arr_id as $id_aux) {
					if ($cadena_aux != "") {
						$cadena_aux .= ", ";
					}
					$cadena_aux .= $id_aux;
				}
				if ($cadena_aux == "") {
					$cadena_aux = "0";
				}
				
				//Se marcan como borrados los registros que no se encuentren en el listado de ids
				$sql = "UPDATE rips_consultas
						SET ind_borrado=1
						WHERE id_convenio=".$id_convenio." ";
				if ($id_plan > 0) {
					$sql .= "AND id_plan=".$id_plan." ";
				}
				$sql .= "AND fecha_consulta BETWEEN STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_final."', '%d/%m/%Y') 
						AND id_rips_ac NOT IN (".$cadena_aux.")";
				
				$this->ejecutarSentencia($sql, array());
				
				return 1;
			} catch (Exception $e) {
				return -2;
			}
		}
		
		public function borrar_rips_procedimientos($id_convenio, $id_plan, $fecha_inicial, $fecha_final, $arr_id) {
			try {
				$cadena_aux = "";
				foreach ($arr_id as $id_aux) {
					if ($cadena_aux != "") {
						$cadena_aux .= ", ";
					}
					$cadena_aux .= $id_aux;
				}
				if ($cadena_aux == "") {
					$cadena_aux = "0";
				}
				
				//Se marcan como borrados los registros que no se encuentren en el listado de ids
				$sql = "UPDATE rips_procedimientos
						SET ind_borrado=1
						WHERE id_convenio=".$id_convenio." ";
				if ($id_plan > 0) {
					$sql .= "AND id_plan=".$id_plan." ";
				}
				$sql .= "AND fecha_pro BETWEEN STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_final."', '%d/%m/%Y') 
						AND id_rips_ap NOT IN (".$cadena_aux.")";
				
				$this->ejecutarSentencia($sql, array());
				
				return 1;
			} catch (Exception $e) {
				return -2;
			}
		}
		
		public function borrar_rips_medicamentos($id_convenio, $id_plan, $fecha_inicial, $fecha_final, $arr_id) {
			try {
				$cadena_aux = "";
				foreach ($arr_id as $id_aux) {
					if ($cadena_aux != "") {
						$cadena_aux .= ", ";
					}
					$cadena_aux .= $id_aux;
				}
				if ($cadena_aux == "") {
					$cadena_aux = "0";
				}
				
				//Se marcan como borrados los registros que no se encuentren en el listado de ids
				$sql = "UPDATE rips_medicamentos
						SET ind_borrado=1
						WHERE id_convenio=".$id_convenio." ";
				if ($id_plan > 0) {
					$sql .= "AND id_plan=".$id_plan." ";
				}
				$sql .= "AND fecha_medicamento BETWEEN STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_final."', '%d/%m/%Y') 
						AND id_rips_am NOT IN (".$cadena_aux.")";
				
				$this->ejecutarSentencia($sql, array());
				
				return 1;
			} catch (Exception $e) {
				return -2;
			}
		}
		
		public function borrar_rips_otros_servicios($id_convenio, $id_plan, $fecha_inicial, $fecha_final, $arr_id) {
			try {
				$cadena_aux = "";
				foreach ($arr_id as $id_aux) {
					if ($cadena_aux != "") {
						$cadena_aux .= ", ";
					}
					$cadena_aux .= $id_aux;
				}
				if ($cadena_aux == "") {
					$cadena_aux = "0";
				}
				
				//Se marcan como borrados los registros que no se encuentren en el listado de ids
				$sql = "UPDATE rips_otros_servicios
						SET ind_borrado=1
						WHERE id_convenio=".$id_convenio." ";
				if ($id_plan > 0) {
					$sql .= "AND id_plan=".$id_plan." ";
				}
				$sql .= "AND fecha_insumo BETWEEN STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_final."', '%d/%m/%Y') 
						AND id_rips_at NOT IN (".$cadena_aux.")";
				
				$this->ejecutarSentencia($sql, array());
				
				return 1;
			} catch (Exception $e) {
				return -2;
			}
		}
		
		public function borrar_rips_usuarios($id_rips, $arr_id) {
			try {
				$cadena_aux = "";
				foreach ($arr_id as $id_aux) {
					if ($cadena_aux != "") {
						$cadena_aux .= ", ";
					}
					$cadena_aux .= $id_aux;
				}
				if ($cadena_aux == "") {
					$cadena_aux = "0";
				}
				
				$sql = "DELETE FROM rips_usuarios
						WHERE id_rips=".$id_rips."
						AND id_rips_us NOT IN (".$cadena_aux.")";
				
				$this->ejecutarSentencia($sql, array());
				return 1;
			} catch (Exception $e) {
				return -2;
			}
		}
		
		public function borrar_rips_facturas($id_rips, $id_usuario) {
			try {
				$sql = "CALL pa_borrar_rips_facturas(".$id_rips.", ".$id_usuario.", @id)";
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				
				return $arrResultado["@id"];
			} catch (Exception $e) {
				return -2;
			}
		}
		
		public function borrar_rips_descripcion($id_rips, $id_usuario) {
			try {
				$sql = "CALL pa_borrar_rips_descripcion(".$id_rips.", ".$id_usuario.", @id)";
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				
				return $arrResultado["@id"];
			} catch (Exception $e) {
				return -2;
			}
		}
		
		public function get_lista_registros_ac($id_convenio, $id_plan, $fecha_inicial, $fecha_final, $ind_rips_hc, $ind_sin_np = 0) {
			try {
				$sql = "/*Registros con historia clínica*/
						SELECT 0 AS id_rips_ac, PD.id_detalle_precio, P.id_admision, HC.id_hc, P.id_convenio, P.id_plan,
						P.id_paciente, NULL AS num_factura, DATE_FORMAT(IFNULL(HC.fecha_hora_hc, P.fecha_pago), '%d/%m/%Y %H:%i') AS fecha_consulta_t,
						IFNULL(PD.num_autorizacion, A.num_autorizacion) AS num_autorizacion, PD.cod_procedimiento, MP.nombre_procedimiento, NULL AS fin_consulta,
						NULL AS causa_ext, NULL AS cod_ciex_prin, NULL AS cod_ciex_rel1, NULL AS cod_ciex_rel2,
						NULL AS cod_ciex_rel3, NULL AS tipo_diag_prin, PD.tipo_precio, PD.cantidad, 0 AS valor_consulta,
						0 AS valor_cuota, 0 AS valor_neto, PD.valor, PD.valor AS valor_p, PD.valor_cuota AS valor_cuota_p,
						TD.codigo_detalle AS tipo_documento, PA.numero_documento, PA.nombre_1, PA.nombre_2, PA.apellido_1, 
						PA.apellido_2, PL.ind_tipo_pago, 0 AS ind_revisado, 0 AS ind_borrado, NULL AS nom_ciex_prin, NULL AS nom_ciex_rel1,
						NULL AS nom_ciex_rel2, NULL AS nom_ciex_rel3, NULL AS observaciones
						FROM pagos P
						INNER JOIN pagos_detalle PD ON P.id_pago=PD.id_pago
						INNER JOIN planes PL ON P.id_plan=PL.id_plan
						INNER JOIN maestro_procedimientos MP ON PD.cod_procedimiento=MP.cod_procedimiento
						INNER JOIN pacientes PA ON P.id_paciente=PA.id_paciente
						LEFT JOIN listas_detalle TD ON PA.id_tipo_documento=TD.id_detalle
						INNER JOIN admisiones A ON P.id_admision=A.id_admision
						INNER JOIN tipos_citas_det CD ON A.id_tipo_cita=CD.id_tipo_cita
						INNER JOIN historia_clinica HC ON P.id_admision=HC.id_admision AND CD.id_tipo_reg=HC.id_tipo_reg
						WHERE P.id_convenio=".$id_convenio." ";
				if ($id_plan > 0) {
					$sql .= "AND P.id_plan=".$id_plan." ";
				}
				$sql .= "AND P.estado_pago=2
						AND HC.fecha_hora_hc BETWEEN STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_final." 23:59:59', '%d/%m/%Y %H:%i:%s')
						AND MP.tipo_procedimiento='C' ";
				if ($ind_sin_np == 1) {
					$sql .= "AND EXISTS (
								SELECT PG.id_pago
								FROM pagos PG
								INNER JOIN pagos_det_medios PM ON PG.id_pago=PM.id_pago
								WHERE PG.id_pago=P.id_pago
								AND PM.id_medio_pago<>99
							) ";
				}
				if ($ind_rips_hc != 1) {
					$sql .= "AND NOT EXISTS (
								SELECT RC.id_detalle_precio
								FROM rips_consultas RC
								WHERE RC.id_convenio=".$id_convenio." ";
					if ($id_plan > 0) {
						$sql .= "AND RC.id_plan=".$id_plan." ";
					}
					$sql .= 	"AND PD.id_detalle_precio=RC.id_detalle_precio
							)";
				}
				$sql .= "UNION ALL
						
						/*Registros sin historia clínica*/
						SELECT 0 AS id_rips_ac, PD.id_detalle_precio, P.id_admision, NULL AS id_hc, P.id_convenio, P.id_plan,
						P.id_paciente, NULL AS num_factura, DATE_FORMAT(P.fecha_pago,  '%d/%m/%Y %H:%i') AS fecha_consulta_t,
						IFNULL(PD.num_autorizacion, A.num_autorizacion) AS num_autorizacion, PD.cod_procedimiento, MP.nombre_procedimiento, NULL AS fin_consulta,
						NULL AS causa_ext, NULL AS cod_ciex_prin, NULL AS cod_ciex_rel1, NULL AS cod_ciex_rel2,
						NULL AS cod_ciex_rel3, NULL AS tipo_diag_prin, PD.tipo_precio, PD.cantidad, 0 AS valor_consulta,
						0 AS valor_cuota, 0 AS valor_neto, PD.valor, PD.valor AS valor_p, PD.valor_cuota AS valor_cuota_p,
						TD.codigo_detalle AS tipo_documento, PA.numero_documento, PA.nombre_1, PA.nombre_2, PA.apellido_1, 
						PA.apellido_2, PL.ind_tipo_pago, 0 AS ind_revisado, 0 AS ind_borrado, NULL AS nom_ciex_prin, NULL AS nom_ciex_rel1,
						NULL AS nom_ciex_rel2, NULL AS nom_ciex_rel3, NULL AS observaciones
						FROM pagos P
						INNER JOIN pagos_detalle PD ON P.id_pago=PD.id_pago
						INNER JOIN planes PL ON P.id_plan=PL.id_plan
						INNER JOIN maestro_procedimientos MP ON PD.cod_procedimiento=MP.cod_procedimiento
						INNER JOIN pacientes PA ON P.id_paciente=PA.id_paciente
						LEFT JOIN listas_detalle TD ON PA.id_tipo_documento=TD.id_detalle
						LEFT JOIN admisiones A ON P.id_admision=A.id_admision
						LEFT JOIN tipos_citas_det CD ON A.id_tipo_cita=CD.id_tipo_cita
						WHERE P.id_convenio=".$id_convenio." ";
				if ($id_plan > 0) {
					$sql .= "AND P.id_plan=".$id_plan." ";
				}
				$sql .= "AND P.estado_pago=2
						AND P.fecha_pago BETWEEN STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_final." 23:59:59', '%d/%m/%Y %H:%i:%s')
						AND MP.tipo_procedimiento='C'
						AND NOT EXISTS (
							SELECT *
							FROM historia_clinica HC
							WHERE P.id_admision=HC.id_admision
							AND CD.id_tipo_reg=HC.id_tipo_reg
						) ";
				if ($ind_sin_np == 1) {
					$sql .= "AND EXISTS (
								SELECT PG.id_pago
								FROM pagos PG
								INNER JOIN pagos_det_medios PM ON PG.id_pago=PM.id_pago
								WHERE PG.id_pago=P.id_pago
								AND PM.id_medio_pago<>99
							) ";
				}
				if ($ind_rips_hc == 1) {
					$sql .= "ORDER BY id_admision, id_detalle_precio, id_hc DESC";
				} else {
					$sql .= "AND NOT EXISTS (
								SELECT RC.id_detalle_precio
								FROM rips_consultas RC
								WHERE RC.id_convenio=".$id_convenio." ";
					if ($id_plan > 0) {
						$sql .= "AND RC.id_plan=".$id_plan." ";
					}
					$sql .= 	"AND PD.id_detalle_precio=RC.id_detalle_precio
							)
							
							UNION ALL
							
							/*Registros de RIPS*/
							SELECT RC.id_rips_ac, RC.id_detalle_precio, RC.id_admision, RC.id_hc, RC.id_convenio, RC.id_plan,
							RC.id_paciente, RC.num_factura, DATE_FORMAT(RC.fecha_consulta, '%d/%m/%Y %H:%i') AS fecha_consulta_t,
							RC.num_autorizacion, RC.cod_procedimiento, MP.nombre_procedimiento, RC.fin_consulta, RC.causa_ext,
							RC.cod_ciex_prin, RC.cod_ciex_rel1, RC.cod_ciex_rel2, RC.cod_ciex_rel3, RC.tipo_diag_prin,
							0 AS tipo_precio, 1 AS cantidad, RC.valor_consulta, RC.valor_cuota, RC.valor_neto, 0 AS valor,
							0 AS valor_p, 0 AS valor_cuota_p, RC.tipo_documento, RC.numero_documento, P.nombre_1, P.nombre_2,
							P.apellido_1, P.apellido_2, NULL AS ind_tipo_pago, RC.ind_revisado, RC.ind_borrado, DP.nombre AS nom_ciex_prin,
							D1.nombre AS nom_ciex_rel1, D2.nombre AS nom_ciex_rel2, D3.nombre AS nom_ciex_rel3, RC.observaciones
							FROM rips_consultas RC
							LEFT JOIN maestro_procedimientos MP ON RC.cod_procedimiento=MP.cod_procedimiento
							LEFT JOIN pacientes P ON RC.id_paciente=P.id_paciente
							LEFT JOIN ciex DP ON RC.cod_ciex_prin=DP.codciex
							LEFT JOIN ciex D1 ON RC.cod_ciex_rel1=D1.codciex
							LEFT JOIN ciex D2 ON RC.cod_ciex_rel2=D2.codciex
							LEFT JOIN ciex D3 ON RC.cod_ciex_rel3=D3.codciex
							WHERE RC.id_convenio=".$id_convenio." ";
					if ($id_plan > 0) {
						$sql .= "AND RC.id_plan=".$id_plan." ";
					}
					$sql .= "AND RC.fecha_consulta BETWEEN STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_final."', '%d/%m/%Y') ";
					if ($ind_sin_np == 1) {
						$sql .= "AND EXISTS (
									SELECT PD.id_detalle_precio
									FROM pagos_detalle PD
									INNER JOIN pagos_det_medios PM ON PD.id_pago=PM.id_pago
									WHERE PD.id_detalle_precio=RC.id_detalle_precio
									AND PM.id_medio_pago<>99
								)";
					}
					$sql .= "ORDER BY -id_admision DESC, id_detalle_precio, id_hc DESC, id_rips_ac";
				}
				//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_lista_rips_consultas($id_convenio, $id_plan, $fecha_ini, $fecha_fin, $ind_borrado) {
			try {
				$sql = "SELECT RC.*, MP.nombre_procedimiento, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2,
						DP.nombre AS nom_ciex_prin, D1.nombre AS nom_ciex_rel1, D2.nombre AS nom_ciex_rel2,
						D3.nombre AS nom_ciex_rel3, DATE_FORMAT(RC.fecha_consulta, '%d/%m/%Y %H:%i') AS fecha_consulta_t, 1 AS cantidad
						FROM rips_consultas RC
						LEFT JOIN maestro_procedimientos MP ON RC.cod_procedimiento=MP.cod_procedimiento
						LEFT JOIN pacientes P ON RC.id_paciente=P.id_paciente
						LEFT JOIN ciex DP ON RC.cod_ciex_prin=DP.codciex
						LEFT JOIN ciex D1 ON RC.cod_ciex_rel1=D1.codciex
						LEFT JOIN ciex D2 ON RC.cod_ciex_rel2=D2.codciex
						LEFT JOIN ciex D3 ON RC.cod_ciex_rel3=D3.codciex
						WHERE RC.id_convenio=".$id_convenio." ";
				if ($id_plan != "" && $id_plan > 0) {
					$sql .= "AND RC.id_plan=".$id_plan." ";
				}
				$sql .= "AND RC.fecha_consulta BETWEEN STR_TO_DATE('".$fecha_ini."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_fin."', '%d/%m/%Y') ";
				if ($ind_borrado != "") {
					$sql .= "AND RC.ind_borrado=".$ind_borrado." ";
				}
				$sql .= "ORDER BY RC.id_rips_ac";
				//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_lista_registros_ap($id_convenio, $id_plan, $fecha_inicial, $fecha_final, $ind_rips_hc, $ind_sin_np = 0) {
			try {
				$sql = "/*Registros de procedimientos no qx con historia clínica*/
						SELECT 0 AS id_rips_ap, PD.id_detalle_precio, P.id_admision, HC.id_hc, P.id_convenio, P.id_plan,
						P.id_paciente, NULL AS num_factura, DATE_FORMAT(HC.fecha_hora_hc, '%d/%m/%Y') AS fecha_pro_t,
						IFNULL(PD.num_autorizacion, A.num_autorizacion) AS num_autorizacion, PD.cod_procedimiento, MP.nombre_procedimiento,
						TR.id_clase_reg, CD.id_tipo_reg, '1' AS amb_rea,
						CASE WHEN CD.id_tipo_cita IN (6, 7) AND PD.cod_procedimiento=CD.cod_procedimiento THEN '2' ELSE '1' END AS fin_pro,
						NULL AS per_ati, NULL AS cod_ciex_prin, NULL AS cod_ciex_rel, NULL AS cod_ciex_com, NULL AS for_rea, CD.orden,
						PD.tipo_precio, PD.cantidad, 0 AS valor_pro, 0 AS valor_copago, PD.valor, PD.valor AS valor_p, PD.valor_cuota AS valor_cuota_p,
						TD.codigo_detalle AS tipo_documento, PA.numero_documento, PA.nombre_1, PA.nombre_2, PA.apellido_1, PA.apellido_2,
						PL.ind_tipo_pago, 0 AS ind_revisado, 0 AS ind_borrado, NULL AS nom_ciex_prin, NULL AS nom_ciex_rel, NULL AS nom_ciex_com,
						NULL AS observaciones
						FROM pagos P
						INNER JOIN pagos_detalle PD ON P.id_pago=PD.id_pago
						INNER JOIN planes PL ON P.id_plan=PL.id_plan
						INNER JOIN maestro_procedimientos MP ON PD.cod_procedimiento=MP.cod_procedimiento
						INNER JOIN pacientes PA ON P.id_paciente=PA.id_paciente
						LEFT JOIN listas_detalle TD ON PA.id_tipo_documento=TD.id_detalle
						INNER JOIN admisiones A ON P.id_admision=A.id_admision
						INNER JOIN tipos_citas TC ON A.id_tipo_cita=TC.id_tipo_cita
						INNER JOIN tipos_citas_det CD ON A.id_tipo_cita=CD.id_tipo_cita
						INNER JOIN tipos_registros_hc TR ON CD.id_tipo_reg=TR.id_tipo_reg
						INNER JOIN historia_clinica HC ON P.id_admision=HC.id_admision AND CD.id_tipo_reg=HC.id_tipo_reg
						WHERE P.id_convenio=".$id_convenio." ";
				if ($id_plan > 0) {
					$sql .= "AND P.id_plan=".$id_plan." ";
				}
				$sql .= "AND P.estado_pago=2
						AND HC.fecha_hora_hc BETWEEN STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_final." 23:59:59', '%d/%m/%Y %H:%i:%s')
						AND MP.tipo_procedimiento='P'
						AND TC.id_tipo_reg_cx IS NULL ";
				if ($ind_sin_np == 1) {
					$sql .= "AND EXISTS (
								SELECT PG.id_pago
								FROM pagos PG
								INNER JOIN pagos_det_medios PM ON PG.id_pago=PM.id_pago
								WHERE PG.id_pago=P.id_pago
								AND PM.id_medio_pago<>99
							) ";
				}
				if ($ind_rips_hc != 1) {
					$sql .= "AND NOT EXISTS (
								SELECT RP.id_detalle_precio
								FROM rips_procedimientos RP
								WHERE RP.id_convenio=".$id_convenio." ";
					if ($id_plan > 0) {
						$sql .= "AND RP.id_plan=".$id_plan." ";
					}
					$sql .= 	"AND RP.id_detalle_precio=PD.id_detalle_precio
							) ";
				}
				
				$sql .= "UNION ALL
						
						/*Registros de procedimientos no qx sin historia clínica*/
						SELECT 0 AS id_rips_ap, PD.id_detalle_precio, P.id_admision, NULL AS id_hc, P.id_convenio, P.id_plan,
						P.id_paciente, NULL AS num_factura, DATE_FORMAT(P.fecha_pago, '%d/%m/%Y') AS fecha_pro_t,
						IFNULL(PD.num_autorizacion, A.num_autorizacion) AS num_autorizacion, PD.cod_procedimiento, MP.nombre_procedimiento,
						TR.id_clase_reg, CD.id_tipo_reg, '1' AS amb_rea,
						CASE WHEN CD.id_tipo_cita IN (6, 7) AND PD.cod_procedimiento=CD.cod_procedimiento THEN '2' ELSE '1' END AS fin_pro,
						NULL AS per_ati, NULL AS cod_ciex_prin, NULL AS cod_ciex_rel, NULL AS cod_ciex_com, NULL AS for_rea, CD.orden,
						PD.tipo_precio, PD.cantidad, 0 AS valor_pro, 0 AS valor_copago, PD.valor, PD.valor AS valor_p, PD.valor_cuota AS valor_cuota_p,
						TD.codigo_detalle AS tipo_documento, PA.numero_documento, PA.nombre_1, PA.nombre_2, PA.apellido_1, PA.apellido_2,
						PL.ind_tipo_pago, 0 AS ind_revisado, 0 AS ind_borrado, NULL AS nom_ciex_prin, NULL AS nom_ciex_rel, NULL AS nom_ciex_com,
						NULL AS observaciones
						FROM pagos P
						INNER JOIN pagos_detalle PD ON P.id_pago=PD.id_pago
						INNER JOIN planes PL ON P.id_plan=PL.id_plan
						INNER JOIN maestro_procedimientos MP ON PD.cod_procedimiento=MP.cod_procedimiento
						INNER JOIN pacientes PA ON P.id_paciente=PA.id_paciente
						LEFT JOIN listas_detalle TD ON PA.id_tipo_documento=TD.id_detalle
						LEFT JOIN admisiones A ON P.id_admision=A.id_admision
						LEFT JOIN tipos_citas TC ON A.id_tipo_cita=TC.id_tipo_cita
						LEFT JOIN tipos_citas_det CD ON A.id_tipo_cita=CD.id_tipo_cita
						LEFT JOIN tipos_registros_hc TR ON CD.id_tipo_reg=TR.id_tipo_reg
						WHERE P.id_convenio=".$id_convenio." ";
				if ($id_plan > 0) {
					$sql .= "AND P.id_plan=".$id_plan." ";
				}
				$sql .= "AND P.estado_pago=2
						AND P.fecha_pago BETWEEN STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_final." 23:59:59', '%d/%m/%Y %H:%i:%s')
						AND MP.tipo_procedimiento='P'
						AND TC.id_tipo_reg_cx IS NULL
						AND NOT EXISTS (
							SELECT *
							FROM historia_clinica HC
							WHERE P.id_admision=HC.id_admision
							AND CD.id_tipo_reg=HC.id_tipo_reg
						) ";
				if ($ind_sin_np == 1) {
					$sql .= "AND EXISTS (
								SELECT PG.id_pago
								FROM pagos PG
								INNER JOIN pagos_det_medios PM ON PG.id_pago=PM.id_pago
								WHERE PG.id_pago=P.id_pago
								AND PM.id_medio_pago<>99
							) ";
				}
				if ($ind_rips_hc != 1) {
					$sql .= "AND NOT EXISTS (
								SELECT RP.id_detalle_precio
								FROM rips_procedimientos RP
								WHERE RP.id_convenio=".$id_convenio." ";
					if ($id_plan > 0) {
						$sql .= "AND RP.id_plan=".$id_plan." ";
					}
					$sql .= 	"AND RP.id_detalle_precio=PD.id_detalle_precio
							) ";
				}
				
				$sql .= "UNION ALL
						
						/*Registros de procedimientos qx con historia clínica*/
						SELECT 0 AS id_rips_ap, PD.id_detalle_precio, P.id_admision, CX.id_hc, P.id_convenio, P.id_plan,
						P.id_paciente, NULL AS num_factura, DATE_FORMAT(IFNULL(CX.fecha_cx, P.fecha_pago), '%d/%m/%Y') AS fecha_pro_t,
						PD.num_autorizacion, PD.cod_procedimiento, MP.nombre_procedimiento, TR.id_clase_reg, CD.id_tipo_reg,
						IFNULL(AR.codigo_detalle, '1') AS amb_rea, IFNULL(FP.codigo_detalle, '2') AS fin_pro,
						'1' AS per_ati, NULL AS cod_ciex_prin, NULL AS cod_ciex_rel, NULL AS cod_ciex_com, NULL AS for_rea, CD.orden,
						PD.tipo_precio, PD.cantidad, 0 AS valor_pro, 0 AS valor_copago, PD.valor, PD.valor AS valor_p, PD.valor_cuota AS valor_cuota_p,
						TD.codigo_detalle AS tipo_documento, PA.numero_documento, PA.nombre_1, PA.nombre_2, PA.apellido_1, PA.apellido_2,
						PL.ind_tipo_pago, 0 AS ind_revisado, 0 AS ind_borrado, NULL AS nom_ciex_prin, NULL AS nom_ciex_rel, NULL AS nom_ciex_com,
						NULL AS observaciones
						FROM (
							SELECT * FROM pagos
							WHERE fecha_pago BETWEEN (STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') - INTERVAL 15 DAY) AND STR_TO_DATE('".$fecha_final." 23:59:59', '%d/%m/%Y %H:%i:%s')
						) P
						INNER JOIN pagos_detalle PD ON P.id_pago=PD.id_pago
						INNER JOIN planes PL ON P.id_plan=PL.id_plan
						INNER JOIN maestro_procedimientos MP ON PD.cod_procedimiento=MP.cod_procedimiento
						INNER JOIN pacientes PA ON P.id_paciente=PA.id_paciente
						LEFT JOIN listas_detalle TD ON PA.id_tipo_documento=TD.id_detalle
						INNER JOIN admisiones A ON P.id_admision=A.id_admision
						INNER JOIN tipos_citas TC ON A.id_tipo_cita=TC.id_tipo_cita
						INNER JOIN tipos_citas_det CD ON A.id_tipo_cita=CD.id_tipo_cita
						INNER JOIN tipos_registros_hc TR ON CD.id_tipo_reg=TR.id_tipo_reg
						INNER JOIN historia_clinica HC ON P.id_admision=HC.id_admision AND CD.id_tipo_reg=HC.id_tipo_reg
						LEFT JOIN (
							SELECT CI.id_admision_preqx AS id_admision, HC.id_hc, CI.fecha_cx, TR.id_clase_reg, CI.id_amb_rea, CI.id_fin_pro
							FROM historia_clinica HC
							INNER JOIN tipos_registros_hc TR ON HC.id_tipo_reg=TR.id_tipo_reg
							INNER JOIN cirugias CI ON HC.id_hc=CI.id_hc
							WHERE TR.id_clase_reg=2
							AND CI.fecha_cx BETWEEN STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_final." 23:59:59', '%d/%m/%Y %H:%i:%s')
						) CX ON P.id_admision=CX.id_admision
						LEFT JOIN listas_detalle AR ON CX.id_amb_rea=AR.id_detalle
						LEFT JOIN listas_detalle FP ON CX.id_fin_pro=FP.id_detalle
						WHERE P.id_convenio=".$id_convenio." ";
				if ($id_plan > 0) {
					$sql .= "AND P.id_plan=".$id_plan." ";
				}
				$sql .= "AND P.estado_pago=2
						AND IFNULL(CX.fecha_cx, P.fecha_pago) BETWEEN STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_final." 23:59:59', '%d/%m/%Y %H:%i:%s')
						AND MP.tipo_procedimiento='P'
						AND TC.id_tipo_reg_cx IS NOT NULL ";
				if ($ind_sin_np == 1) {
					$sql .= "AND EXISTS (
								SELECT PG.id_pago
								FROM pagos PG
								INNER JOIN pagos_det_medios PM ON PG.id_pago=PM.id_pago
								WHERE PG.id_pago=P.id_pago
								AND PM.id_medio_pago<>99
							) ";
				}
				if ($ind_rips_hc != 1) {
					$sql .= "AND NOT EXISTS (
								SELECT RP.id_detalle_precio
								FROM rips_procedimientos RP
								WHERE RP.id_convenio=".$id_convenio." ";
					if ($id_plan > 0) {
						$sql .= "AND RP.id_plan=".$id_plan." ";
					}
					$sql .= 	"AND RP.id_detalle_precio=PD.id_detalle_precio
							) ";
				}
				
				$sql .= "UNION ALL
						
						/*Registros de procedimientos qx sin historia clínica*/
						SELECT 0 AS id_rips_ap, PD.id_detalle_precio, P.id_admision, NULL AS id_hc, P.id_convenio, P.id_plan,
						P.id_paciente, NULL AS num_factura, DATE_FORMAT(P.fecha_pago, '%d/%m/%Y') AS fecha_pro_t,
						PD.num_autorizacion, PD.cod_procedimiento, MP.nombre_procedimiento, TR.id_clase_reg, CD.id_tipo_reg,
						'1' AS amb_rea, '2' AS fin_pro,
						'1' AS per_ati, NULL AS cod_ciex_prin, NULL AS cod_ciex_rel, NULL AS cod_ciex_com, NULL AS for_rea, CD.orden,
						PD.tipo_precio, PD.cantidad, 0 AS valor_pro, 0 AS valor_copago, PD.valor, PD.valor AS valor_p, PD.valor_cuota AS valor_cuota_p,
						TD.codigo_detalle AS tipo_documento, PA.numero_documento, PA.nombre_1, PA.nombre_2, PA.apellido_1, PA.apellido_2,
						PL.ind_tipo_pago, 0 AS ind_revisado, 0 AS ind_borrado, NULL AS nom_ciex_prin, NULL AS nom_ciex_rel, NULL AS nom_ciex_com,
						NULL AS observaciones
						FROM pagos P
						INNER JOIN pagos_detalle PD ON P.id_pago=PD.id_pago
						INNER JOIN planes PL ON P.id_plan=PL.id_plan
						INNER JOIN maestro_procedimientos MP ON PD.cod_procedimiento=MP.cod_procedimiento
						INNER JOIN pacientes PA ON P.id_paciente=PA.id_paciente
						LEFT JOIN listas_detalle TD ON PA.id_tipo_documento=TD.id_detalle
						LEFT JOIN admisiones A ON P.id_admision=A.id_admision
						LEFT JOIN tipos_citas TC ON A.id_tipo_cita=TC.id_tipo_cita
						LEFT JOIN tipos_citas_det CD ON A.id_tipo_cita=CD.id_tipo_cita
						LEFT JOIN tipos_registros_hc TR ON CD.id_tipo_reg=TR.id_tipo_reg
						WHERE P.id_convenio=".$id_convenio." ";
				if ($id_plan > 0) {
					$sql .= "AND P.id_plan=".$id_plan." ";
				}
				$sql .= "AND P.estado_pago=2
						AND P.fecha_pago BETWEEN STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_final." 23:59:59', '%d/%m/%Y %H:%i:%s')
						AND MP.tipo_procedimiento='P'
						AND TC.id_tipo_reg_cx IS NOT NULL
						AND NOT EXISTS (
							SELECT *
							FROM historia_clinica HC
							WHERE P.id_admision=HC.id_admision
							AND CD.id_tipo_reg=HC.id_tipo_reg
						) ";
				if ($ind_sin_np == 1) {
					$sql .= "AND EXISTS (
								SELECT PG.id_pago
								FROM pagos PG
								INNER JOIN pagos_det_medios PM ON PG.id_pago=PM.id_pago
								WHERE PG.id_pago=P.id_pago
								AND PM.id_medio_pago<>99
							) ";
				}
				
				if ($ind_rips_hc == 1) {
					$sql .= "ORDER BY -id_admision DESC, id_detalle_precio, id_hc DESC, orden DESC, id_rips_ap";
				} else {
					$sql .= "AND NOT EXISTS (
								SELECT RP.id_detalle_precio
								FROM rips_procedimientos RP
								WHERE RP.id_convenio=".$id_convenio." ";
					if ($id_plan > 0) {
						$sql .= "AND RP.id_plan=".$id_plan." ";
					}
					$sql .= 	"AND RP.id_detalle_precio=PD.id_detalle_precio
							)
							
							UNION ALL
							
							/*Registros de RIPS*/
							SELECT RP.id_rips_ap, RP.id_detalle_precio, RP.id_admision, RP.id_hc, RP.id_convenio, RP.id_plan,
							RP.id_paciente, RP.num_factura, DATE_FORMAT(RP.fecha_pro, '%d/%m/%Y') AS fecha_pro_t, RP.num_autorizacion,
							RP.cod_procedimiento, MP.nombre_procedimiento, NULL AS id_clase_reg, NULL AS id_tipo_reg, RP.amb_rea,
							RP.fin_pro, RP.per_ati, RP.cod_ciex_prin, RP.cod_ciex_rel, RP.cod_ciex_com, RP.for_rea, 0 AS orden,
							0 AS tipo_precio, 1 AS cantidad, RP.valor_pro, RP.valor_copago, 0 AS valor, 0 AS valor_p, 0 AS valor_cuota_p,
							RP.tipo_documento, RP.numero_documento, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2,
							NULL AS ind_tipo_pago, RP.ind_revisado, RP.ind_borrado, DP.nombre AS nom_ciex_prin, DR.nombre AS nom_ciex_rel,
							DC.nombre AS nom_ciex_com, RP.observaciones
							FROM rips_procedimientos RP
							LEFT JOIN maestro_procedimientos MP ON RP.cod_procedimiento=MP.cod_procedimiento
							LEFT JOIN pacientes P ON RP.id_paciente=P.id_paciente
							LEFT JOIN ciex DP ON RP.cod_ciex_prin=DP.codciex
							LEFT JOIN ciex DR ON RP.cod_ciex_rel=DR.codciex
							LEFT JOIN ciex DC ON RP.cod_ciex_com=DC.codciex
							WHERE RP.id_convenio=".$id_convenio." ";
					if ($id_plan > 0) {
						$sql .= "AND RP.id_plan=".$id_plan." ";
					}
					$sql .= "AND RP.fecha_pro BETWEEN STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_final."', '%d/%m/%Y') ";
					if ($ind_sin_np == 1) {
						$sql .= "AND EXISTS (
									SELECT PD.id_detalle_precio
									FROM pagos_detalle PD
									INNER JOIN pagos_det_medios PM ON PD.id_pago=PM.id_pago
									WHERE PD.id_detalle_precio=RP.id_detalle_precio
									AND PM.id_medio_pago<>99
								) ";
					}
					$sql .= "ORDER BY -id_admision DESC, id_detalle_precio, id_hc DESC, orden DESC, id_rips_ap";
				}
				//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_lista_rips_procedimientos($id_convenio, $id_plan, $fecha_ini, $fecha_fin, $ind_borrado) {
			try {
				$sql = "SELECT RP.*, MP.nombre_procedimiento, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2,
						DP.nombre AS nom_ciex_prin, DR.nombre AS nom_ciex_rel, DC.nombre AS nom_ciex_com,
						DATE_FORMAT(RP.fecha_pro, '%d/%m/%Y') AS fecha_pro_t, 1 AS cantidad
						FROM rips_procedimientos RP
						LEFT JOIN maestro_procedimientos MP ON RP.cod_procedimiento=MP.cod_procedimiento
						LEFT JOIN pacientes P ON RP.id_paciente=P.id_paciente
						LEFT JOIN ciex DP ON RP.cod_ciex_prin=DP.codciex
						LEFT JOIN ciex DR ON RP.cod_ciex_rel=DR.codciex
						LEFT JOIN ciex DC ON RP.cod_ciex_com=DC.codciex
						WHERE RP.id_convenio=".$id_convenio." ";
				if ($id_plan != "" && $id_plan > 0) {
					$sql .= "AND RP.id_plan=".$id_plan." ";
				}
				$sql .= "AND RP.fecha_pro BETWEEN STR_TO_DATE('".$fecha_ini."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_fin."', '%d/%m/%Y') ";
				if ($ind_borrado != "") {
					$sql .= "AND RP.ind_borrado=".$ind_borrado." ";
				}
				$sql .= "ORDER BY RP.id_rips_ap";
				//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_lista_registros_am($id_convenio, $id_plan, $fecha_inicial, $fecha_final, $ind_rips_hc, $ind_sin_np = 0) {
			try {
				
				$sql= "SELECT 
					  0 AS id_rips_am, FMD.id_formula_medicamento_det, 0 AS id_admision, FM.id_convenio, FM.id_plan, FM.id_paciente, NULL AS num_factura,
					  DATE_FORMAT(
						IFNULL(NULL, FM.fecha_crea),
						'%d/%m/%Y'
					  ) AS fecha_medicamento_t,
					 
					  NULL AS num_autorizacion, FMD.cod_medicamento, MM.nombre_generico,
					  CASE MM.ind_pos WHEN 1  THEN '1' ELSE '2' END AS tipo_medicamento, MM.presentacion AS forma_farma, MM.concentracion,
					  MM.unidad_medida, NULL tipo_precio, FMD.cantidad_orden, 0 AS valor_medicamento,
					  0 AS valor, 0 AS valor_p, 0 AS valor_cuota_p, TD.codigo_detalle AS tipo_documento,
					  PA.numero_documento, PA.nombre_1, PA.nombre_2, PA.apellido_1, PA.apellido_2, 0 AS ind_tipo_pago,
					  0 AS ind_revisado, 0 AS ind_borrado, NULL AS observaciones 
					FROM
					  formulas_medicamentos FM 
					  INNER JOIN formulas_medicamentos_det FMD ON FM.id_formula_medicamento = FMD.id_formula_medicamento
					  INNER JOIN planes PL ON FM.id_plan = PL.id_plan 
					  INNER JOIN maestro_medicamentos MM ON FMD.cod_medicamento = MM.cod_medicamento 
					  INNER JOIN pacientes PA ON FM.id_paciente = PA.id_paciente 
					 
					  LEFT JOIN listas_detalle TD  ON PA.id_tipo_documento = TD.id_detalle 
					WHERE FM.id_convenio=".$id_convenio." ";
				if ($id_plan > 0) {
					$sql .= " AND FM.id_plan=".$id_plan." ";
				}
				$sql .= " AND FM.fecha_crea BETWEEN STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_final." 23:59:59', '%d/%m/%Y %H:%i:%s') ";
				$sql .= " AND NOT EXISTS (
								SELECT RM.id_detalle_precio FROM rips_medicamentos RM 
  								WHERE RM.id_convenio = $id_convenio ";
				if ($id_plan > 0) {
						$sql .= " AND RM.id_plan=".$id_plan." ";
					} 
					
				$sql .= " AND RM.id_detalle_precio = FMD.id_formula_medicamento_det) 
					
					 UNION ALL
					 SELECT RM.id_rips_am, RM.id_detalle_precio, RM.id_admision, RM.id_convenio, 
						RM.id_plan, RM.id_paciente, RM.num_factura,
						DATE_FORMAT( RM.fecha_medicamento, '%d/%m/%Y') AS fecha_medicamento_t,
						RM.num_autorizacion, RM.cod_medicamento, RM.nombre_generico, RM.tipo_medicamento,
						RM.forma_farma, RM.concentracion, RM.unidad_medida, 0 AS tipo_precio,
						RM.cantidad, RM.valor_medicamento, 0 AS valor, 0 AS valor_p, 0 AS valor_cuota_p,
						RM.tipo_documento, RM.numero_documento, P.nombre_1, P.nombre_2,
						P.apellido_1, P.apellido_2, NULL AS ind_tipo_pago, RM.ind_revisado,
						RM.ind_borrado, RM.observaciones 
					  FROM rips_medicamentos RM 
						LEFT JOIN pacientes P ON RM.id_paciente = P.id_paciente 
					  WHERE RM.id_convenio = $id_convenio ";
					  
					 if ($id_plan > 0) {
						$sql .= " AND RM.id_plan=".$id_plan." ";
					}
					$sql .= " AND RM.fecha_medicamento BETWEEN STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_final."', '%d/%m/%Y') ";
					
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_lista_rips_medicamentos($id_convenio, $id_plan, $fecha_ini, $fecha_fin, $ind_borrado) {
			try {
				$sql = "SELECT RM.*, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2
						FROM rips_medicamentos RM
						LEFT JOIN pacientes P ON RM.id_paciente=P.id_paciente
						WHERE RM.id_convenio=".$id_convenio." ";
				if ($id_plan != "" && $id_plan > 0) {
					$sql .= "AND RM.id_plan=".$id_plan." ";
				}
				$sql .= "AND RM.fecha_medicamento BETWEEN STR_TO_DATE('".$fecha_ini."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_fin."', '%d/%m/%Y') ";
				if ($ind_borrado != "") {
					$sql .= "AND RM.ind_borrado=".$ind_borrado." ";
				}
				$sql .= "ORDER BY RM.id_rips_am";
				//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_lista_registros_at($id_convenio, $id_plan, $fecha_inicial, $fecha_final, $ind_rips_hc, $ind_sin_np = 0) {
			try {
				$sql = "/*Registros de historia clínica*/
						SELECT 0 AS id_rips_at, PD.id_detalle_precio, P.id_admision, P.id_convenio, P.id_plan, P.id_paciente,
						NULL AS num_factura, DATE_FORMAT(IFNULL(A.fecha_admision, P.fecha_pago), '%d/%m/%Y') AS fecha_insumo_t, IFNULL(PD.num_autorizacion, A.num_autorizacion) AS num_autorizacion,
						TI.codigo_detalle AS tipo_insumo, PD.cod_insumo, MI.nombre_insumo, PD.tipo_precio, PD.cantidad, 0 AS valor_insumo,
						PD.valor, PD.valor AS valor_p, PD.valor_cuota AS valor_cuota_p, TD.codigo_detalle AS tipo_documento, PA.numero_documento,
						PA.nombre_1, PA.nombre_2, PA.apellido_1, PA.apellido_2, PL.ind_tipo_pago, 0 AS ind_revisado, 0 AS ind_borrado, NULL AS observaciones
						FROM pagos P
						INNER JOIN pagos_detalle PD ON P.id_pago=PD.id_pago
						INNER JOIN planes PL ON P.id_plan=PL.id_plan
						INNER JOIN maestro_insumos MI ON PD.cod_insumo=MI.cod_insumo
						LEFT JOIN listas_detalle TI ON MI.id_tipo_insumo=TI.id_detalle
						INNER JOIN pacientes PA ON P.id_paciente=PA.id_paciente
						LEFT JOIN admisiones A ON P.id_admision=A.id_admision
						LEFT JOIN listas_detalle TD ON PA.id_tipo_documento=TD.id_detalle
						WHERE P.id_convenio=".$id_convenio." ";
				if ($id_plan > 0) {
					$sql .= "AND P.id_plan=".$id_plan." ";
				}
				$sql .= "AND P.estado_pago=2
						AND P.fecha_pago BETWEEN STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_final." 23:59:59', '%d/%m/%Y %H:%i:%s') ";
				if ($ind_sin_np == 1) {
					$sql .= "AND EXISTS (
								SELECT PG.id_pago
								FROM pagos PG
								INNER JOIN pagos_det_medios PM ON PG.id_pago=PM.id_pago
								WHERE PG.id_pago=P.id_pago
								AND PM.id_medio_pago<>99
							) ";
				}
				if ($ind_rips_hc == 1) {
					$sql .= "ORDER BY P.id_admision, PD.id_detalle_precio";
				} else {
					$sql .= "AND NOT EXISTS (
								SELECT RT.id_detalle_precio
								FROM rips_otros_servicios RT
								WHERE RT.id_convenio=".$id_convenio." ";
					if ($id_plan > 0) {
						$sql .= "AND RT.id_plan=".$id_plan." ";
					}
					$sql .= 	"AND RT.id_detalle_precio=PD.id_detalle_precio
							)
							
							UNION ALL
							
							/*Registros de RIPS*/
							SELECT RT.id_rips_at, RT.id_detalle_precio, RT.id_admision, RT.id_convenio, RT.id_plan, RT.id_paciente,
							RT.num_factura, DATE_FORMAT(RT.fecha_insumo, '%d/%m/%Y') AS fecha_insumo_t, RT.num_autorizacion,
							RT.tipo_insumo, RT.cod_insumo, RT.nombre_insumo, 0 AS tipo_precio, RT.cantidad, RT.valor_insumo,
							0 AS valor, 0 AS valor_p, 0 AS valor_cuota_p, RT.tipo_documento, RT.numero_documento,
							P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2, NULL AS ind_tipo_pago, RT.ind_revisado,
							RT.ind_borrado, RT.observaciones
							FROM rips_otros_servicios RT
							LEFT JOIN pacientes P ON RT.id_paciente=P.id_paciente
							WHERE RT.id_convenio=".$id_convenio." ";
					if ($id_plan > 0) {
						$sql .= "AND RT.id_plan=".$id_plan." ";
					}
					$sql .= "AND RT.fecha_insumo BETWEEN STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_final."', '%d/%m/%Y') ";
					if ($ind_sin_np == 1) {
						$sql .= "AND EXISTS (
									SELECT PD.id_detalle_precio
									FROM pagos_detalle PD
									INNER JOIN pagos_det_medios PM ON PD.id_pago=PM.id_pago
									WHERE PD.id_detalle_precio=RT.id_detalle_precio
									AND PM.id_medio_pago<>99
								) ";
					}
					$sql .= "ORDER BY -id_admision DESC, id_detalle_precio, id_rips_at";
				}
					//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_lista_rips_otros_servicios($id_convenio, $id_plan, $fecha_ini, $fecha_fin, $ind_borrado) {
			try {
				$sql = "SELECT RT.*, P.nombre_1, P.nombre_2, P.apellido_1, P.apellido_2
						FROM rips_otros_servicios RT
						LEFT JOIN pacientes P ON RT.id_paciente=P.id_paciente
						WHERE RT.id_convenio=".$id_convenio." ";
				if ($id_plan != "" && $id_plan > 0) {
					$sql .= "AND RT.id_plan=".$id_plan." ";
				}
				$sql .= "AND RT.fecha_insumo BETWEEN STR_TO_DATE('".$fecha_ini."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_fin."', '%d/%m/%Y') ";
				if ($ind_borrado != "") {
					$sql .= "AND RT.ind_borrado=".$ind_borrado." ";
				}
				$sql .= "ORDER BY RT.id_rips_at";
				//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_lista_registros_us($id_convenio, $id_plan, $fecha_inicial, $fecha_final, $ind_rips_hc, $ind_sin_np = 0) {
			try {
				$sql = "SELECT DISTINCT 0 AS id_rips_us, 0 AS id_rips, PA.id_paciente, TD.codigo_detalle AS tipo_documento, PA.numero_documento,
						C.cod_administradora, TU.codigo_detalle AS tipo_usuario, PA.apellido_1, PA.apellido_2, PA.nombre_1, PA.nombre_2,
						NULL AS edad, NULL AS unidad_edad, fu_calcular_edad(PA.fecha_nacimiento, STR_TO_DATE('".$fecha_final."', '%d/%m/%Y')) AS edad2,
						PA.fecha_nacimiento,
						SX.codigo_detalle AS sexo, PA.id_pais, PS.cod_siesa as cod_pais, PA.cod_dep, M.cod_mun, ZN.codigo_detalle AS zona, IFNULL(DS.codigo_detalle, '2') AS desplazado,
						0 AS ind_revisado, 0 AS ind_borrado, NULL AS observaciones
						FROM pagos P
						INNER JOIN pacientes PA ON P.id_paciente=PA.id_paciente
						INNER JOIN convenios C ON P.id_convenio=C.id_convenio
						INNER JOIN planes PL ON P.id_plan=PL.id_plan
						LEFT JOIN listas_detalle TD ON PA.id_tipo_documento=TD.id_detalle
						LEFT JOIN listas_detalle TU ON PL.id_tipo_usuario=TU.id_detalle
						LEFT JOIN listas_detalle SX ON PA.sexo=SX.id_detalle
						LEFT JOIN paises PS ON PA.id_pais=PS.id_pais
						LEFT JOIN municipios M ON PA.cod_mun=M.cod_mun_dane
						LEFT JOIN listas_detalle ZN ON PA.id_zona=ZN.id_detalle
						LEFT JOIN listas_detalle DS ON PA.ind_desplazado=DS.id_detalle
						WHERE P.id_convenio=".$id_convenio." ";
				if ($id_plan > 0) {
					$sql .= "AND P.id_plan=".$id_plan." ";
				}
				$sql .= "AND P.estado_pago=2
						AND P.fecha_pago BETWEEN STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_final." 23:59:59', '%d/%m/%Y %H:%i:%s') ";
				if ($ind_sin_np == 1) {
					$sql .= "AND EXISTS (
								SELECT PG.id_pago
								FROM pagos PG
								INNER JOIN pagos_det_medios PM ON PG.id_pago=PM.id_pago
								WHERE PG.id_pago=P.id_pago
								AND PM.id_medio_pago<>99
							) ";
				}
				if ($ind_rips_hc == 1) {
					$sql .= "ORDER BY TD.codigo_detalle, PA.numero_documento";
				} else {
					$sql .= "UNION
							
							SELECT DISTINCT 0 AS id_rips_us, 0 AS id_rips, RC.id_paciente, RC.tipo_documento, RC.numero_documento,
							C.cod_administradora, TU.codigo_detalle AS tipo_usuario, PA.apellido_1, PA.apellido_2, PA.nombre_1, PA.nombre_2,
							NULL AS edad, NULL AS unidad_edad, fu_calcular_edad(PA.fecha_nacimiento, STR_TO_DATE('".$fecha_final."', '%d/%m/%Y')) AS edad2,
							PA.fecha_nacimiento,
							SX.codigo_detalle AS sexo, PA.id_pais, PS.cod_siesa as cod_pais, PA.cod_dep, M.cod_mun, ZN.codigo_detalle AS zona, IFNULL(DS.codigo_detalle, '2') AS desplazado,
							0 AS ind_revisado, 0 AS ind_borrado, NULL AS observaciones
							FROM rips_consultas RC
							INNER JOIN pacientes PA ON RC.id_paciente=PA.id_paciente
							INNER JOIN convenios C ON RC.id_convenio=C.id_convenio
							INNER JOIN planes PL ON RC.id_plan=PL.id_plan
							LEFT JOIN listas_detalle SX ON PA.sexo=SX.id_detalle
							LEFT JOIN listas_detalle TU ON PL.id_tipo_usuario=TU.id_detalle
							LEFT JOIN paises PS ON PA.id_pais=PS.id_pais
							LEFT JOIN municipios M ON PA.cod_mun=M.cod_mun_dane
							LEFT JOIN listas_detalle ZN ON PA.id_zona=ZN.id_detalle
							LEFT JOIN listas_detalle DS ON PA.ind_desplazado=DS.id_detalle
							WHERE RC.id_convenio=".$id_convenio." ";
					if ($id_plan > 0) {
						$sql .= "AND RC.id_plan=".$id_plan." ";
					}
					$sql .= "AND RC.fecha_consulta BETWEEN STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_final."', '%d/%m/%Y')
							AND RC.ind_borrado=0 ";
					if ($ind_sin_np == 1) {
						$sql .=	"AND EXISTS (
									SELECT PD.id_detalle_precio
									FROM pagos_detalle PD
									INNER JOIN pagos_det_medios PM ON PD.id_pago=PM.id_pago
									WHERE PD.id_detalle_precio=RC.id_detalle_precio
									AND PM.id_medio_pago<>99
								) ";
					}
					$sql .= "UNION
							
							SELECT DISTINCT 0 AS id_rips_us, 0 AS id_rips, RP.id_paciente, RP.tipo_documento, RP.numero_documento,
							C.cod_administradora, TU.codigo_detalle AS tipo_usuario, PA.apellido_1, PA.apellido_2, PA.nombre_1, PA.nombre_2,
							NULL AS edad, NULL AS unidad_edad, fu_calcular_edad(PA.fecha_nacimiento, STR_TO_DATE('".$fecha_final."', '%d/%m/%Y')) AS edad2,
							PA.fecha_nacimiento,
							SX.codigo_detalle AS sexo,  PA.id_pais, PS.cod_siesa as cod_pais, PA.cod_dep, M.cod_mun, ZN.codigo_detalle AS zona, IFNULL(DS.codigo_detalle, '2') AS desplazado,
							0 AS ind_revisado, 0 AS ind_borrado, NULL AS observaciones
							FROM rips_procedimientos RP
							INNER JOIN pacientes PA ON RP.id_paciente=PA.id_paciente
							INNER JOIN convenios C ON RP.id_convenio=C.id_convenio
							INNER JOIN planes PL ON RP.id_plan=PL.id_plan
							LEFT JOIN listas_detalle SX ON PA.sexo=SX.id_detalle
							LEFT JOIN listas_detalle TU ON PL.id_tipo_usuario=TU.id_detalle
							LEFT JOIN municipios M ON PA.cod_mun=M.cod_mun_dane
							LEFT JOIN paises PS ON PA.id_pais=PS.id_pais
							LEFT JOIN listas_detalle ZN ON PA.id_zona=ZN.id_detalle
							LEFT JOIN listas_detalle DS ON PA.ind_desplazado=DS.id_detalle
							WHERE RP.id_convenio=".$id_convenio." ";
					if ($id_plan > 0) {
						$sql .= "AND RP.id_plan=".$id_plan." ";
					}
					$sql .= "AND RP.fecha_pro BETWEEN STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_final."', '%d/%m/%Y')
							AND RP.ind_borrado=0 ";
					if ($ind_sin_np == 1) {
						$sql .= "AND EXISTS (
									SELECT PD.id_detalle_precio
									FROM pagos_detalle PD
									INNER JOIN pagos_det_medios PM ON PD.id_pago=PM.id_pago
									WHERE PD.id_detalle_precio=RP.id_detalle_precio
									AND PM.id_medio_pago<>99
								) ";
					}
					$sql .= "UNION
							
							SELECT DISTINCT 0 AS id_rips_us, 0 AS id_rips, RM.id_paciente, RM.tipo_documento, RM.numero_documento,
							C.cod_administradora, TU.codigo_detalle AS tipo_usuario, PA.apellido_1, PA.apellido_2, PA.nombre_1, PA.nombre_2,
							NULL AS edad, NULL AS unidad_edad, fu_calcular_edad(PA.fecha_nacimiento, STR_TO_DATE('".$fecha_final."', '%d/%m/%Y')) AS edad2,
							PA.fecha_nacimiento,
							SX.codigo_detalle AS sexo,  PA.id_pais, PS.cod_siesa as cod_pais, PA.cod_dep, M.cod_mun, ZN.codigo_detalle AS zona, IFNULL(DS.codigo_detalle, '2') AS desplazado,
							0 AS ind_revisado, 0 AS ind_borrado, NULL AS observaciones
							FROM rips_medicamentos RM
							INNER JOIN pacientes PA ON RM.id_paciente=PA.id_paciente
							INNER JOIN convenios C ON RM.id_convenio=C.id_convenio
							INNER JOIN planes PL ON RM.id_plan=PL.id_plan
							LEFT JOIN listas_detalle SX ON PA.sexo=SX.id_detalle
							LEFT JOIN listas_detalle TU ON PL.id_tipo_usuario=TU.id_detalle
							LEFT JOIN paises PS ON PA.id_pais=PS.id_pais
							LEFT JOIN municipios M ON PA.cod_mun=M.cod_mun_dane
							LEFT JOIN listas_detalle ZN ON PA.id_zona=ZN.id_detalle
							LEFT JOIN listas_detalle DS ON PA.ind_desplazado=DS.id_detalle
							WHERE RM.id_convenio=".$id_convenio." ";
					if ($id_plan > 0) {
						$sql .= "AND RM.id_plan=".$id_plan." ";
					}
					$sql .= "AND RM.fecha_medicamento BETWEEN STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_final."', '%d/%m/%Y')
							AND RM.ind_borrado=0 ";
					if ($ind_sin_np == 1) {
						$sql .= "AND EXISTS (
									SELECT PD.id_detalle_precio
									FROM pagos_detalle PD
									INNER JOIN pagos_det_medios PM ON PD.id_pago=PM.id_pago
									WHERE PD.id_detalle_precio=RM.id_detalle_precio
									AND PM.id_medio_pago<>99
								) ";
					}
					$sql .= "UNION
							
							SELECT DISTINCT 0 AS id_rips_us, 0 AS id_rips, RT.id_paciente, RT.tipo_documento, RT.numero_documento,
							C.cod_administradora, TU.codigo_detalle AS tipo_usuario, PA.apellido_1, PA.apellido_2, PA.nombre_1, PA.nombre_2,
							NULL AS edad, NULL AS unidad_edad, fu_calcular_edad(PA.fecha_nacimiento, STR_TO_DATE('".$fecha_final."', '%d/%m/%Y')) AS edad2,
							PA.fecha_nacimiento,
							SX.codigo_detalle AS sexo,  PA.id_pais, PS.cod_siesa as cod_pais, PA.cod_dep, M.cod_mun, ZN.codigo_detalle AS zona, IFNULL(DS.codigo_detalle, '2') AS desplazado,
							0 AS ind_revisado, 0 AS ind_borrado, NULL AS observaciones
							FROM rips_otros_servicios RT
							INNER JOIN pacientes PA ON RT.id_paciente=PA.id_paciente
							INNER JOIN convenios C ON RT.id_convenio=C.id_convenio
							INNER JOIN planes PL ON RT.id_plan=PL.id_plan
							LEFT JOIN listas_detalle SX ON PA.sexo=SX.id_detalle
							LEFT JOIN listas_detalle TU ON PL.id_tipo_usuario=TU.id_detalle
							LEFT JOIN paises PS ON PA.id_pais=PS.id_pais
							LEFT JOIN municipios M ON PA.cod_mun=M.cod_mun_dane
							LEFT JOIN listas_detalle ZN ON PA.id_zona=ZN.id_detalle
							LEFT JOIN listas_detalle DS ON PA.ind_desplazado=DS.id_detalle
							WHERE RT.id_convenio=".$id_convenio." ";
					if ($id_plan > 0) {
						$sql .= "AND RT.id_plan=".$id_plan." ";
					}
					$sql .= "AND RT.fecha_insumo BETWEEN STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_final."', '%d/%m/%Y')
							AND RT.ind_borrado=0 ";
					if ($ind_sin_np == 1) {
						$sql .= "AND EXISTS (
									SELECT PD.id_detalle_precio
									FROM pagos_detalle PD
									INNER JOIN pagos_det_medios PM ON PD.id_pago=PM.id_pago
									WHERE PD.id_detalle_precio=RT.id_detalle_precio
									AND PM.id_medio_pago<>99
								) ";
					}
					$sql .= "ORDER BY tipo_documento, numero_documento";
				}
					//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_lista_rips_usuarios($id_rips, $ind_borrado) {
			try {
				$sql = "SELECT RU.id_rips_us, RU.id_rips, RU.id_paciente, RU.tipo_documento, RU.numero_documento,
						RU.cod_administradora, RU.tipo_usuario, RU.apellido_1, RU.apellido_2, RU.nombre_1, RU.nombre_2,
						RU.edad, RU.unidad_edad, NULL AS edad2, RU.sexo, RU.cod_dep, RU.cod_mun, RU.zona, '2' AS desplazado,
						RU.ind_revisado, RU.ind_borrado, RU.observaciones
						FROM rips_usuarios RU
						WHERE RU.id_rips=".$id_rips." ";
				if ($ind_borrado != "") {
					$sql .= "AND RU.ind_borrado=".$ind_borrado." ";
				}
				$sql .= "ORDER BY RU.tipo_documento, RU.numero_documento";
				//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_lista_rips_facturas($id_rips) {
			try {
				$sql = "SELECT RF.*, DATE_FORMAT(RF.fecha_factura, '%d/%m/%Y') AS fecha_factura_t,
						DATE_FORMAT(RF.fecha_ini, '%d/%m/%Y') AS fecha_ini_t, DATE_FORMAT(RF.fecha_fin, '%d/%m/%Y') AS fecha_fin_t
						FROM rips_facturas RF
						WHERE RF.id_rips=".$id_rips."
						ORDER BY RF.id_rips_af";
				//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_lista_rips_descripcion($id_rips) {
			try {
				$sql = "SELECT RD.*
						FROM rips_descripcion RD
						WHERE RD.id_rips=".$id_rips."
						ORDER BY RD.id_rips_ad";
				//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_cant_ciex_paciente($id_paciente, $cod_ciex, $fecha) {
			try {
				$sql = "SELECT COUNT(*) AS cantidad
						FROM historia_clinica HC
						INNER JOIN diagnosticos_hc D ON HC.id_hc=D.id_hc
						WHERE HC.id_paciente=".$id_paciente."
						AND HC.fecha_hora_hc<STR_TO_DATE('".$fecha."', '%d/%m/%Y')
						AND D.cod_ciex='".$cod_ciex."'";
				//echo($sql);
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_lista_cx($id_convenio, $id_plan, $fecha_inicial, $fecha_final) {
			try {
				$sql = "SELECT id_hc, COUNT(*) AS cant_cx, COUNT(DISTINCT via_procedimiento) AS cant_vias
						FROM (
							SELECT DISTINCT CP.*
							FROM pagos P
							INNER JOIN admisiones A ON P.id_admision=A.id_admision
							INNER JOIN historia_clinica HC ON P.id_admision=HC.id_admision
							INNER JOIN cirugias_procedimientos CP ON HC.id_hc=CP.id_hc
							WHERE P.id_convenio=".$id_convenio." ";
				if ($id_plan > 0) {
					$sql .= "AND P.id_plan=".$id_plan." ";
				}
				$sql .=
							"AND P.estado_pago=2
							AND P.fecha_pago BETWEEN STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_final." 23:59:59', '%d/%m/%Y %H:%i:%s')
							) T
						GROUP BY id_hc
						ORDER BY id_hc";
				//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_lista_diagnosticos($id_convenio, $id_plan, $fecha_inicial, $fecha_final, $tipo_registro) {
			try {
				$sql = "SELECT DISTINCT D.*, CX.codciexori AS cod_ciex_ori, CX.nombre AS nom_ciex
						FROM pagos P
						INNER JOIN pagos_detalle PD ON P.id_admision=PD.id_admision
						INNER JOIN maestro_procedimientos MP ON PD.cod_procedimiento=MP.cod_procedimiento
						INNER JOIN admisiones A ON P.id_admision=A.id_admision
						INNER JOIN historia_clinica HC ON P.id_admision=HC.id_admision
						INNER JOIN diagnosticos_hc D ON HC.id_hc=D.id_hc
						INNER JOIN vi_ciex CX ON D.cod_ciex=CX.codciex
						WHERE P.id_convenio=".$id_convenio." ";
				if ($id_plan > 0) {
					$sql .= "AND P.id_plan=".$id_plan." ";
				}
				$sql .= "AND P.estado_pago=2
						AND P.fecha_pago BETWEEN STR_TO_DATE('".$fecha_inicial."', '%d/%m/%Y') AND STR_TO_DATE('".$fecha_final." 23:59:59', '%d/%m/%Y %H:%i:%s')
						AND MP.tipo_procedimiento='".$tipo_registro."'
						ORDER BY D.id_hc, D.orden";
				//echo($sql);
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_rips($id_convenio, $id_plan, $fecha_ini, $fecha_fin) {
			try {
				$sql = "SELECT R.*, C.nombre_convenio, P.nombre_plan, DATE_FORMAT(R.fecha_ini, '%d/%m/%Y') AS fecha_ini_t,
						DATE_FORMAT(R.fecha_fin, '%d/%m/%Y') AS fecha_fin_t
						FROM rips R
						INNER JOIN convenios C ON R.id_convenio=C.id_convenio
						LEFT JOIN planes P ON R.id_plan=P.id_plan
						WHERE R.id_convenio=".$id_convenio." ";
				if ($id_plan > 0) {
					$sql .= "AND R.id_plan=".$id_plan." ";
				} else {
					$sql .= "AND R.id_plan IS NULL ";
				}
				$sql .= "AND R.fecha_ini=STR_TO_DATE('".$fecha_ini."', '%d/%m/%Y')
						AND R.fecha_fin=STR_TO_DATE('".$fecha_fin."', '%d/%m/%Y')";
				
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function get_rips_id($id_rips) {
			try {
				$sql = "SELECT R.*, C.nombre_convenio, P.nombre_plan, DATE_FORMAT(R.fecha_ini, '%d/%m/%Y') AS fecha_ini_t,
						DATE_FORMAT(R.fecha_fin, '%d/%m/%Y') AS fecha_fin_t
						FROM rips R
						INNER JOIN convenios C ON R.id_convenio=C.id_convenio
						LEFT JOIN planes P ON R.id_plan=P.id_plan
						WHERE R.id_rips=".$id_rips;
			
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/* RIPS Directos ------------------------------------------------------------------------------------------------------------------------------- **/
		

	public function crear_rips_directo($id_rips, $id_prestador, $id_usuario, $num_factura_in) {
			try {
				if($num_factura_in == "-1"){
					$num_factura_in = NULL;
				}else{
					$num_factura_in = $num_factura_in;
				}
				$sql = "CALL pa_crear_rips_directo(".$id_rips.", ".$id_prestador.", ".$id_usuario." , '$num_factura_in' , @id)";
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				return $arrResultado["@id"];
			} catch (Exception $e) {
				return -2;
			}
		}
	   
		
	}
?>
