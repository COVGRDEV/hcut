<?php
session_start();
header('Content-Type: text/xml; charset=UTF-8');

require_once("../db/DbConvenios.php");
require_once("../db/DbPlanes.php");
require_once("../db/DbRIPS.php");
require_once("../db/DbDatosEntidad.php");
require_once("../db/DbCalendario.php");
require_once("../db/DbListas.php");
require_once("../db/DbDepartamentos.php");
require_once("../db/DbPaises.php");
require_once("../db/DbDepMuni.php");
require_once("../db/DbVariables.php");
require_once '../funciones/Class_Combo_Box.php';
require_once("../funciones/FuncionesPersona.php");
require_once '../funciones/Utilidades.php';

/***************/
/***CONSULTAS***/
/***************/
function generar_arch_ac($id_convenio, $id_plan, $fecha_ini, $fecha_fin, $cons_rips, $id_usuario) {
	$db_rips = new DbRIPS();
	$cant_registros = 0;
	
	//Se obtienen los registros de consultas
	$lista_reg_ac = $db_rips->get_lista_rips_consultas($id_convenio, $id_plan, $fecha_ini, $fecha_fin, "0");
	if (count($lista_reg_ac) > 0) {
		//Se crea el archivo
		$nombre_archivo = "./rips/".$id_usuario."/AC".substr("000000".$cons_rips, -6).".txt";
		$arch_aux = fopen($nombre_archivo, "w+");
		
		foreach ($lista_reg_ac as $reg_aux) {
			//Se construye el registro
			$linea_aux = $reg_aux["num_factura"].",".
						 $reg_aux["cod_prestador"].",".
						 $reg_aux["tipo_documento"].",".
						 $reg_aux["numero_documento"].",".
						 $reg_aux["fecha_consulta_t"].",".
						 $reg_aux["num_autorizacion"].",".
						 $reg_aux["cod_procedimiento"].",".
						 $reg_aux["fin_consulta"].",".
						 $reg_aux["causa_ext"].",".
						 $reg_aux["cod_ciex_prin"].",".
						 $reg_aux["cod_ciex_rel1"].",".
						 $reg_aux["cod_ciex_rel2"].",".
						 $reg_aux["cod_ciex_rel3"].",".
						 $reg_aux["tipo_diag_prin"].",".
						 $reg_aux["valor_consulta"].",".
						 $reg_aux["valor_cuota"].",".
						 $reg_aux["valor_neto"];
				
				//Se agrega el registro al archivo
				fwrite($arch_aux, $linea_aux."\r\n");
				
				$cant_registros++;
		}
		
		fclose($arch_aux);
	}
	
	return $cant_registros;
}


/********************/
/***PROCEDIMIENTOS***/
/********************/
function generar_arch_ap($id_convenio, $id_plan, $fecha_ini, $fecha_fin, $cons_rips, $id_usuario) {
	$db_rips = new DbRIPS();
	$cant_registros = 0;
	
	//Se obtienen los registros de procedimientos
	$lista_reg_ap = $db_rips->get_lista_rips_procedimientos($id_convenio, $id_plan, $fecha_ini, $fecha_fin, "0");
	if (count($lista_reg_ap) > 0) {
		//Se crea el archivo
		$nombre_archivo = "./rips/".$id_usuario."/AP".substr("000000".$cons_rips, -6).".txt";
		$arch_aux = fopen($nombre_archivo, "w+");
		
		foreach ($lista_reg_ap as $reg_aux) {
			//Se construye el registro
			$linea_aux = $reg_aux["num_factura"].",".
						 $reg_aux["cod_prestador"].",".
						 $reg_aux["tipo_documento"].",".
						 $reg_aux["numero_documento"].",".
						 $reg_aux["fecha_pro_t"].",".
						 $reg_aux["num_autorizacion"].",".
						 $reg_aux["cod_procedimiento"].",".
						 $reg_aux["amb_rea"].",".
						 $reg_aux["fin_pro"].",".
						 $reg_aux["per_ati"].",".
						 $reg_aux["cod_ciex_prin"].",".
						 $reg_aux["cod_ciex_rel"].",".
						 $reg_aux["cod_ciex_com"].",".
						 $reg_aux["for_rea"].",".
						 $reg_aux["valor_pro"];
				
			//Se agrega el registro al archivo
			fwrite($arch_aux, $linea_aux."\r\n");
			
			$cant_registros++;
		}
		
		fclose($arch_aux);
	}
	
	return $cant_registros;
}


/******************/
/***MEDICAMENTOS***/
/******************/
function generar_arch_am($id_convenio, $id_plan, $fecha_ini, $fecha_fin, $cons_rips, $id_usuario) {
	$db_rips = new DbRIPS();
	$cant_registros = 0;
	
	//Se obtienen los registros de medicamentos
	$lista_reg_am = $db_rips->get_lista_rips_medicamentos($id_convenio, $id_plan, $fecha_ini, $fecha_fin, "0");
	if (count($lista_reg_am) > 0) {
		//Se crea el archivo
		$nombre_archivo = "./rips/".$id_usuario."/AM".substr("000000".$cons_rips, -6).".txt";
		$arch_aux = fopen($nombre_archivo, "w+");
		
		foreach ($lista_reg_am as $reg_aux) {
			//Se construye el registro
			$linea_aux = $reg_aux["num_factura"].",".
						 $reg_aux["cod_prestador"].",".
						 $reg_aux["tipo_documento"].",".
						 $reg_aux["numero_documento"].",".
						 $reg_aux["num_autorizacion"].",".
						 $reg_aux["cod_medicamento"].",".
						 $reg_aux["tipo_medicamento"].",".
						 str_replace(",", ".", $reg_aux["nombre_generico"]).",".
						 str_replace(",", ".", $reg_aux["forma_farma"]).",".
						 str_replace(",", ".", $reg_aux["concentracion"]).",".
						 str_replace(",", ".", $reg_aux["unidad_medida"]).",".
						 $reg_aux["cantidad"].",".
						 $reg_aux["valor_medicamento"].",".
						 (intval($reg_aux["cantidad"], 10) * floatval("0".$reg_aux["valor_medicamento"]));
			
			//Se agrega el registro al archivo
			fwrite($arch_aux, $linea_aux."\r\n");
			
			$cant_registros++;
		}
		
		fclose($arch_aux);
	}
	
	return $cant_registros;
}


/*********************/
/***OTROS SERVICIOS***/
/*********************/
function generar_arch_at($id_convenio, $id_plan, $fecha_ini, $fecha_fin, $cons_rips, $id_usuario) {
	$db_rips = new DbRIPS();
	$cant_registros = 0;
	
	//Se obtienen los registros de otros servicios
	$lista_reg_at = $db_rips->get_lista_rips_otros_servicios($id_convenio, $id_plan, $fecha_ini, $fecha_fin, "0");
	if (count($lista_reg_at) > 0) {
		//Se crea el archivo
		$nombre_archivo = "./rips/".$id_usuario."/AT".substr("000000".$cons_rips, -6).".txt";
		$arch_aux = fopen($nombre_archivo, "w+");
		
		foreach ($lista_reg_at as $reg_aux) {
			//Se construye el registro
			$linea_aux = $reg_aux["num_factura"].",".
						 $reg_aux["cod_prestador"].",".
						 $reg_aux["tipo_documento"].",".
						 $reg_aux["numero_documento"].",".
						 $reg_aux["num_autorizacion"].",".
						 $reg_aux["tipo_insumo"].",".
						 $reg_aux["cod_insumo"].",".
						 str_replace(",", ".", $reg_aux["nombre_insumo"]).",".
						 $reg_aux["cantidad"].",".
						 $reg_aux["valor_insumo"].",".
						 (intval($reg_aux["cantidad"], 10) * floatval("0".$reg_aux["valor_insumo"]));
			
			//Se agrega el registro al archivo
			fwrite($arch_aux, $linea_aux."\r\n");
			
			$cant_registros++;
		}
		
		fclose($arch_aux);
	}
	
	return $cant_registros;
}


/**************/
/***USUARIOS***/
/**************/
function generar_arch_us($id_rips, $cons_rips, $id_usuario) {
	$db_rips = new DbRIPS();
	$cant_registros = 0;
	
	//Se obtienen los registros de usuarios
	$lista_reg_us = $db_rips->get_lista_rips_usuarios($id_rips, "0");
	if (count($lista_reg_us) > 0) {
		//Se crea el archivo
		$nombre_archivo = "./rips/".$id_usuario."/US".substr("000000".$cons_rips, -6).".txt";
		$arch_aux = fopen($nombre_archivo, "w+");
		
		foreach ($lista_reg_us as $reg_aux) {
			//Se construye el registro
			$linea_aux = $reg_aux["tipo_documento"].",".
						 $reg_aux["numero_documento"].",".
						 $reg_aux["cod_administradora"].",".
						 $reg_aux["tipo_usuario"].",".
						 str_replace(",", ".", $reg_aux["apellido_1"]).",".
						 str_replace(",", ".", $reg_aux["apellido_2"]).",".
						 str_replace(",", ".", $reg_aux["nombre_1"]).",".
						 str_replace(",", ".", $reg_aux["nombre_2"]).",".
						 $reg_aux["edad"].",".
						 $reg_aux["unidad_edad"].",".
						 $reg_aux["sexo"].",".
						 $reg_aux["cod_dep"].",".
						 $reg_aux["cod_mun"].",".
						 $reg_aux["zona"];
			
			//Se agrega el registro al archivo
			fwrite($arch_aux, $linea_aux."\r\n");
			
			$cant_registros++;
		}
		
		fclose($arch_aux);
	}
	
	return $cant_registros;
}


/**************/
/***FACTURAS***/
/**************/
function generar_arch_af($id_rips, $cons_rips, $id_usuario) {
	$db_rips = new DbRIPS();
	$cant_registros = 0;
	
	//Se obtienen los registros de facturas
	$lista_reg_af = $db_rips->get_lista_rips_facturas($id_rips);
	if (count($lista_reg_af) > 0) {
		//Se crea el archivo
		$nombre_archivo = "./rips/".$id_usuario."/AF".substr("000000".$cons_rips, -6).".txt";
		$arch_aux = fopen($nombre_archivo, "w+");
		
		foreach ($lista_reg_af as $reg_aux) {
			//Se construye el registro
			$linea_aux = $reg_aux["cod_prestador"].",".
						 str_replace(",", ".", $reg_aux["nombre_prestador"]).",".
						 $reg_aux["tipo_documento"].",".
						 $reg_aux["numero_documento"].",".
						 $reg_aux["num_factura"].",".
						 $reg_aux["fecha_factura_t"].",".
						 $reg_aux["fecha_ini_t"].",".
						 $reg_aux["fecha_fin_t"].",".
						 $reg_aux["cod_administradora"].",".
						 str_replace(",", ".", $reg_aux["nombre_administradora"]).",".
						 str_replace(",", ".", $reg_aux["num_contrato"]).",".
						 str_replace(",", ".", $reg_aux["plan_benef"]).",".
						 str_replace(",", ".", $reg_aux["num_poliza"]).",".
						 $reg_aux["valor_copago"].",".
						 $reg_aux["valor_comision"].",".
						 $reg_aux["valor_descuento"].",".
						 $reg_aux["valor_neto"];
			
			//Se agrega el registro al archivo
			fwrite($arch_aux, $linea_aux."\r\n");
			
			$cant_registros++;
		}
		fclose($arch_aux);
	}
	
	return $cant_registros;
}


/*****************/
/***DESCRIPCIÓN***/
/*****************/
function generar_arch_ad($id_rips, $cons_rips, $id_usuario) {
	$db_rips = new DbRIPS();
	$cant_registros = 0;
	
	//Se obtienen los registros de descripcion
	$lista_reg_ad = $db_rips->get_lista_rips_descripcion($id_rips);
	if (count($lista_reg_ad) > 0) {
		//Se crea el archivo
		$nombre_archivo = "./rips/".$id_usuario."/AD".substr("000000".$cons_rips, -6).".txt";
		$arch_aux = fopen($nombre_archivo, "w+");
		
		foreach ($lista_reg_ad as $reg_aux) {
			//Se construye el registro
			$linea_aux = $reg_aux["num_factura"].",".
						 $reg_aux["cod_prestador"].",".
						 $reg_aux["cod_concepto"].",".
						 $reg_aux["cantidad"].",".
						 $reg_aux["valor_unitario"].",".
						 (intval($reg_aux["cantidad"], 10) * floatval("0".$reg_aux["valor_unitario"]));
			
			//Se agrega el registro al archivo
			fwrite($arch_aux, $linea_aux."\r\n");
			
			$cant_registros++;
		}
		fclose($arch_aux);
	}
	
	return $cant_registros;
}


/*************/
/***CONTROL***/
/*************/
function generar_arch_ct($id_rips, $cons_rips, $arr_control, $cod_prestador, $fecha_ct, $id_usuario) {
	$db_calendario = new DbCalendario();
	
	if (count($arr_control) > 0) {
		//Se crea el archivo
		$nombre_archivo = "./rips/".$id_usuario."/CT".substr("000000".$cons_rips, -6).".txt";
		$arch_aux = fopen($nombre_archivo, "w+");
		
		foreach ($arr_control as $pref_arch => $cant_aux) {
			if ($cant_aux > 0) {
				//Se construye el registro
				$linea_aux = $cod_prestador.",".
							 $fecha_ct.",".
							 $pref_arch.substr("000000".$cons_rips, -6).",".
							 $cant_aux;
				
				//Se agrega el registro al archivo
				fwrite($arch_aux, $linea_aux."\r\n");
			}
		}
		
		fclose($arch_aux);
	}
}


/******************************/
/***CREACIÓN DE ARCHIVO .ZIP***/
/******************************/
function crear_zip($arr_archivos, $destino) {
	//Variables
	$arr_arch_validos = array();
	
	if (is_array($arr_archivos)) {
		foreach ($arr_archivos as $arch_aux) {
			if (file_exists($arch_aux)) {
				$arr_arch_validos[] = $arch_aux;
			}
		}
	}
	
	if (count($arr_arch_validos) > 0) {
		//Se crea el archivo
		$zip = new ZipArchive();
		if (($resul = $zip->open($destino, ZIPARCHIVE::OVERWRITE | ZIPARCHIVE::CREATE)) !== true) {
			echo($resul);
			return false;
		}
		//Se agregan los archivos
		foreach ($arr_arch_validos as $arch_aux) {
			//Se elimina toda la ruta, solo se deja el nombre
			$pos_aux = strrpos($arch_aux, "/");
			$arch2_aux = $arch_aux;
			if ($pos_aux !== false) {
				$arch2_aux = substr($arch_aux, $pos_aux + 1);
			}
			
			//Se agrega el archivo
			$zip->addFile($arch_aux, $arch2_aux);
		}
		
		$zip->close();
		
		return file_exists($destino);
	} else {
		return false;
	}
}


$db_convenios = new DbConvenios();
$db_planes = new DbPlanes();
$db_rips = new DbRIPS();
$db_datos_entidad = new DbDatosEntidad();
$db_listas = new DbListas();
$db_departamentos = new DbDepartamentos();
$db_paises = new DbPaises();
$db_dep_muni = new DbDepMuni();
$db_variables = new Dbvariables();

$combo = new Combo_Box();
$funciones_persona = new FuncionesPersona();
$utilidades = new Utilidades();

$opcion = $_POST["opcion"];

switch ($opcion) {
	case "1": //Combo de planes de un convenio
		@$id_convenio = intval($_POST["id_convenio"], 10);
		@$sigla = $utilidades->str_decode($_POST["sigla"]);
		@$indice = $utilidades->str_decode($_POST["indice"]);
		
		$lista_planes = $db_planes->getListaPlanesActivos($id_convenio);
		
		$nombre_aux = "cmb_plan";
		if ($sigla != "" || $indice != "") {
			if ($sigla != "") {
				$nombre_aux .= "_".$sigla;
			}
			if ($indice != "") {
				$nombre_aux .= "_".$indice;
			}
			$estilo_aux = "width:100%;";
		} else {
			$estilo_aux = "width:280px;";
		}
		$combo->getComboDb($nombre_aux, "", $lista_planes, "id_plan, nombre_plan", "Todos los planes", "seleccionar_plan(this.value);", true, $estilo_aux, "", "select_sin_margen");
		break;
		
    case "2": //Carga de datos de RIPS
		$id_usuario = $_SESSION["idUsuario"];
		
		@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
		@$id_plan = $utilidades->str_decode($_POST["id_plan"]);
		@$fecha_inicial = $utilidades->str_decode($_POST["fecha_inicial"]);
		@$fecha_final = $utilidades->str_decode($_POST["fecha_final"]);
		@$tipo_factura = $utilidades->str_decode($_POST["tipo_factura"]);
		@$id_prestador = $utilidades->str_decode($_POST["id_prestador"]);
		@$ind_rips_existentes = intval($utilidades->str_decode($_POST["ind_rips_existentes"]), 10);
		@$ind_ac = intval($utilidades->str_decode($_POST["ind_ac"]), 10);
		@$ind_ap = intval($utilidades->str_decode($_POST["ind_ap"]), 10);
		@$ind_am = intval($utilidades->str_decode($_POST["ind_am"]), 10);
		@$ind_at = intval($utilidades->str_decode($_POST["ind_at"]), 10);
		@$ind_us = intval($utilidades->str_decode($_POST["ind_us"]), 10);
		@$ind_af = intval($utilidades->str_decode($_POST["ind_af"]), 10);
		@$ind_ad = intval($utilidades->str_decode($_POST["ind_ad"]), 10);
		@$ind_ct = intval($utilidades->str_decode($_POST["ind_ct"]), 10);
		@$ind_sin_np = intval($utilidades->str_decode($_POST["ind_sin_np"]), 10);
		
		//Se obtiene el listado de convenios
		$lista_convenios = $db_convenios->getListaConveniosActivos();
		
		//Se obtiene el listado de planes
		$lista_planes = $db_planes->getListaPlanesActivos($id_convenio);
		
		//Se obtienen los datos del convenio
		$convenio_obj = $db_convenios->getConvenio($id_convenio);
		 
		//Se obtienen los datos del plan
		$plan_obj = array();
		if ($id_plan != "") {
			$plan_obj = $db_planes->getPlan($id_plan);
		} else {
			if (count($lista_planes) > 0) {
				$plan_obj = $lista_planes[0];
			}
		}
		
		//Se verifica si los RIPS ya fueron generados con anterioridad
		$rips_obj = $db_rips->get_rips($id_convenio, $id_plan, $fecha_inicial, $fecha_final);
		$id_rips = 0;
		$num_factura = "";
		if (!is_null($rips_obj) && count($rips_obj) > 0) {
			$id_rips = intval($rips_obj["id_rips"], 10);
			$num_factura = $rips_obj["num_factura"];
		}
	?>
    <input type="hidden" id="hdd_id_rips" value="<?php echo($id_rips); ?>" />
    <input type="hidden" id="hdd_id_convenio" value="<?php echo($id_convenio); ?>" />
    <input type="hidden" id="hdd_id_plan" value="<?php echo($id_plan); ?>" />
    <input type="hidden" id="hdd_fecha_ini" value="<?php echo($fecha_inicial); ?>" />
    <input type="hidden" id="hdd_fecha_fin" value="<?php echo($fecha_final); ?>" />
    <input type="hidden" id="hdd_tipo_factura" value="<?php echo($tipo_factura); ?>" />
    <input type="hidden" id="hdd_prestador" value="<?php echo($id_prestador); ?>" />
    <input type="hidden" id="hdd_ac" value="<?php echo($ind_ac); ?>" />
    <input type="hidden" id="hdd_ap" value="<?php echo($ind_ap); ?>" />
    <input type="hidden" id="hdd_am" value="<?php echo($ind_am); ?>" />
    <input type="hidden" id="hdd_at" value="<?php echo($ind_at); ?>" />
    <input type="hidden" id="hdd_us" value="<?php echo($ind_us); ?>" />
    <input type="hidden" id="hdd_af" value="<?php echo($ind_af); ?>" />
    <input type="hidden" id="hdd_ad" value="<?php echo($ind_ad); ?>" />
    <input type="hidden" id="hdd_ct" value="<?php echo($ind_ct); ?>" />
    <input type="hidden" id="hdd_sin_np" value="<?php echo($ind_sin_np); ?>" />
	<div id="advertenciasg">
    	<div class="contenedor_error" id="contenedor_error"></div>
	    <div class="contenedor_exito" id="contenedor_exito"></div>
    </div>
    <?php
		if ($tipo_factura == "1") {
	?>
    <table cellpadding="3" cellspacing="0" style="width:100%;">
    	<tr>
        	<td align="right" style="width:15%;">
            	<label class="inline">N&uacute;mero de factura:&nbsp;</label>
            </td>
            <td align="left" style="width:85%;">
            	<input type="text" id="txt_num_factura" maxlength="20" style="width:150px;" value="<?php echo($num_factura); ?>" onblur="agregar_numero_factura(this.value);" />
            </td>
        </tr>
    </table>
    <?php
		} else {
	?>
    <input type="hidden" id="txt_num_factura" value="" />
    <?php
		}
		
		//Se crean los tabs de los archivos a generar
		$bol_primera_pest = true;
	?>
	<div class="tabs-container" >
    	<dl class="tabs" data-tab>
        	<?php
				if ($ind_ac == 1) {
			?>
    	    <dd id="panel_opt_1"<?php if ($bol_primera_pest) { ?> class="active"<?php } ?>><a href="#panel2-1">AC&nbsp;(<span id="span_cant_ac">0</span>)</a></dd>
        	<?php
					$bol_primera_pest = false;
				}
				
				if ($ind_ap == 1) {
			?>
    	    <dd id="panel_opt_2"<?php if ($bol_primera_pest) { ?> class="active"<?php } ?>><a href="#panel2-2">AP&nbsp;(<span id="span_cant_ap">0</span>)</a></dd>
        	<?php
					$bol_primera_pest = false;
				}
				
				if ($ind_am == 1) {
			?>
    	    <dd id="panel_opt_3"<?php if ($bol_primera_pest) { ?> class="active"<?php } ?>><a href="#panel2-3">AM&nbsp;(<span id="span_cant_am">0</span>)</a></dd>
        	<?php
					$bol_primera_pest = false;
				}
				
				if ($ind_at == 1) {
			?>
    	    <dd id="panel_opt_4"<?php if ($bol_primera_pest) { ?> class="active"<?php } ?>><a href="#panel2-4">AT&nbsp;(<span id="span_cant_at">0</span>)</a></dd>
        	<?php
					$bol_primera_pest = false;
				}
				
				if ($ind_us == 1) {
			?>
    	    <dd id="panel_opt_5"<?php if ($bol_primera_pest) { ?> class="active"<?php } ?>><a href="#panel2-5">US&nbsp;(<span id="span_cant_us">0</span>)</a></dd>
        	<?php
					$bol_primera_pest = false;
				}
				
				if ($ind_af == 1) {
			?>
    	    <dd id="panel_opt_6"<?php if ($bol_primera_pest) { ?> class="active"<?php } ?>><a href="#panel2-6">AF</a></dd>
        	<?php
					$bol_primera_pest = false;
				}
				
				if ($ind_ad == 1) {
			?>
    	    <dd id="panel_opt_7"<?php if ($bol_primera_pest) { ?> class="active"<?php } ?>><a href="#panel2-7">AD</a></dd>
        	<?php
					$bol_primera_pest = false;
				}
				
				if ($ind_ct == 1) {
			?>
    	    <dd id="panel_opt_8"<?php if ($bol_primera_pest) { ?> class="active"<?php } ?>><a href="#panel2-8">CT</a></dd>
        	<?php
					$bol_primera_pest = false;
				}
			?>
    	</dl>
        <?php
        	$bol_primera_pest = true;
		?>
        <div class="tabs-content">
        	<?php
            	if ($ind_ac == 1) {
			?>
        	<!--INICIO CONSULTAS-->
            <div class="content<?php if ($bol_primera_pest) { ?> active<?php } ?>" id="panel2-1">
            	<div style="width:98%; overflow:auto; text-align:left">
                	<table border="0" cellpadding="1" cellspacing="0" style="width:100%;">
                    	<tr>
                        	<td align="right" style="width:8%;">
			                	<label class="inline">Filtrar por:&nbsp;</label>
                            </td>
                            <td align="left" style="width:23%;">
			                    <input type="text" id="txt_buscar_ac" placeholder="Nombre o n&uacute;mero de documento" style="width:100%;" onkeyup="filtrar_ac();" />
                            </td>
                            <td align="left" style="width:13%;">
			                    <input type="text" id="txt_buscar_factura_ac" placeholder="No. de factura" style="width:100%;" onkeyup="filtrar_ac();" />
                            </td>
                            <td align="left" style="width:11%;">
			                    <input type="text" id="txt_buscar_cups_ac" placeholder="C&oacute;digo" style="width:100%;" onkeyup="filtrar_ac();" />
                            </td>
                            <td align="left" style="width:11%;">
			                    <input type="text" id="txt_buscar_valor_ac" placeholder="Valor" style="width:100%;" onkeyup="filtrar_ac();" />
                            </td>
                        	<td align="right" style="width:8%;">
			                	<label class="inline">Revisado:&nbsp;</label>
                            </td>
                            <td align="left" style="width:10%;">
                            	<select id="cmb_revisado_ac" onchange="filtrar_ac();">
                                	<option value="2">&lt;Todos&gt;</option>
                                    <option value="1">Si</option>
                                    <option value="0">No</option>
                                </select>
                            </td>
                        	<td align="right" style="width:6%;">
			                	<label class="inline">Excluido:&nbsp;</label>
                            </td>
                            <td align="left" style="width:10%;">
                            	<select id="cmb_borrado_ac" onchange="filtrar_ac();">
                                	<option value="2">&lt;Todos&gt;</option>
                                    <option value="1">Si</option>
                                    <option value="0">No</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                        	<td align="left" colspan="9">
                            	<b>N&uacute;mero de registros:&nbsp;<span id="span_num_reg_ac">0</span></b>
                            </td>
                        </tr>
                    </table>
                </div>
            	<div id="d_registros_ac" style="width:98%; overflow:hidden;"></div>
                
                <script id="ajax" type="text/javascript">
					cargar_rips_ac("<?php echo($ind_rips_existentes); ?>");
				</script>
                
                <div style="width:98%; text-align:center">
                	<br />
                	<input type="button" id="btn_guardar_ac" class="btnPrincipal" value="Guardar consultas" onclick="guardar_ac(1);" />
                </div>
                <div id="d_guardar_ac" style="display:none;"></div>
            </div>
            <!--FIN CONSULTAS-->
            <?php
					$bol_primera_pest = false;
				}
				
				if ($ind_ap == 1) {
			?>
        	<!--INICIO PROCEDIMIENTOS-->
            <div class="content<?php if ($bol_primera_pest) { ?> active<?php } ?>" id="panel2-2">
            	<div style="width:98%; overflow:auto; text-align:left">
                	<table border="0" cellpadding="1" cellspacing="0" style="width:100%;">
                    	<tr>
                        	<td align="right" style="width:8%;">
			                	<label class="inline">Filtrar por:&nbsp;</label>
                            </td>
                            <td align="left" style="width:23%;">
			                    <input type="text" id="txt_buscar_ap" placeholder="Nombre o n&uacute;mero de documento" style="width:100%;" onkeyup="filtrar_ap();" />
                            </td>
                            <td align="left" style="width:13%;">
			                    <input type="text" id="txt_buscar_factura_ap" placeholder="No. de factura" style="width:100%;" onkeyup="filtrar_ap();" />
                            </td>
                            <td align="left" style="width:11%;">
			                    <input type="text" id="txt_buscar_cups_ap" placeholder="C&oacute;digo" style="width:100%;" onkeyup="filtrar_ap();" />
                            </td>
                            <td align="left" style="width:11%;">
                                <input type="text" id="txt_buscar_valor_ap" placeholder="Valor" style="width:100%;" onkeyup="filtrar_ap();" />
                            </td>
                        	<td align="right" style="width:8%;">
			                	<label class="inline">Revisado:&nbsp;</label>
                            </td>
                            <td align="left" style="width:10%;">
                            	<select id="cmb_revisado_ap" onchange="filtrar_ap();">
                                	<option value="2">&lt;Todos&gt;</option>
                                    <option value="1">Si</option>
                                    <option value="0">No</option>
                                </select>
                            </td>
                        	<td align="right" style="width:6%;">
			                	<label class="inline">Excluido:&nbsp;</label>
                            </td>
                            <td align="left" style="width:10%;">
                            	<select id="cmb_borrado_ap" onchange="filtrar_ap();">
                                	<option value="2">&lt;Todos&gt;</option>
                                    <option value="1">Si</option>
                                    <option value="0">No</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                        	<td align="left" colspan="8">
                            	<b>N&uacute;mero de registros:&nbsp;<span id="span_num_reg_ap">0</span></b>
                            </td>
                        </tr>
                    </table>
                </div>
            	<div id="d_registros_ap" style="width:98%; overflow:hidden;"></div>
                <script id="ajax" type="text/javascript">
					cargar_rips_ap("<?php echo($ind_rips_existentes); ?>");
				</script>
                <div style="width:98%; text-align:center">
                	<br />
                	<input type="button" id="btn_guardar_ap" class="btnPrincipal" value="Guardar procedimientos" onclick="guardar_ap(1);" />
                </div>
                <div id="d_guardar_ap" style="display:none;"></div>
            </div>
            <!--FIN PROCEDIMIENTOS-->
            <?php
					$bol_primera_pest = false;
				}
				
            	if ($ind_am == 1) {
			?>
        	<!--INICIO MEDICAMENTOS-->
            <div class="content<?php if ($bol_primera_pest) { ?> active<?php } ?>" id="panel2-3">
            	<div style="width:98%; overflow:auto; text-align:left">
                	<table border="0" cellpadding="1" cellspacing="0" style="width:100%;">
                    	<tr>
                        	<td align="right" style="width:8%;">
			                	<label class="inline">Filtrar por:&nbsp;</label>
                            </td>
                            <td align="left" style="width:23%;">
			                    <input type="text" id="txt_buscar_am" placeholder="Nombre o n&uacute;mero de documento" style="width:100%;" onkeyup="filtrar_am();" />
                            </td>
                            <td align="left" style="width:13%;">
			                    <input type="text" id="txt_buscar_factura_am" placeholder="No. de factura" style="width:100%;" onkeyup="filtrar_am();" />
                            </td>
                            <td align="left" style="width:11%;">
			                    <input type="text" id="txt_buscar_medicamento_am" placeholder="C&oacute;digo" style="width:100%;" onkeyup="filtrar_am();" />
                            </td>
                            <td align="left" style="width:11%;">
                                <input type="text" id="txt_buscar_valor_am" placeholder="Valor" style="width:100%;" onkeyup="filtrar_am();" />
                            </td>
                        	<td align="right" style="width:8%;">
			                	<label class="inline">Revisado:&nbsp;</label>
                            </td>
                            <td align="left" style="width:10%;">
                            	<select id="cmb_revisado_am" onchange="filtrar_am();">
                                	<option value="2">&lt;Todos&gt;</option>
                                    <option value="1">Si</option>
                                    <option value="0">No</option>
                                </select>
                            </td>
                        	<td align="right" style="width:6%;">
			                	<label class="inline">Excluido:&nbsp;</label>
                            </td>
                            <td align="left" style="width:10%;">
                            	<select id="cmb_borrado_am" onchange="filtrar_am();">
                                	<option value="2">&lt;Todos&gt;</option>
                                    <option value="1">Si</option>
                                    <option value="0">No</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                        	<td align="left" colspan="8">
                            	<b>N&uacute;mero de registros:&nbsp;<span id="span_num_reg_am">0</span></b>
                            </td>
                        </tr>
                    </table>
                </div>
            	<div id="d_registros_am" style="width:98%; overflow:hidden;"></div>
                <script id="ajax" type="text/javascript">
					cargar_rips_am("<?php echo($ind_rips_existentes); ?>");
				</script>
                <div style="width:98%; text-align:center">
                	<br />
                	<input type="button" id="btn_guardar_am" class="btnPrincipal" value="Guardar medicamentos" onclick="guardar_am(1);" />
                </div>
                <div id="d_guardar_am" style="display:none;"></div>
            </div>
            <!--FIN MEDICAMENTOS-->
            <?php
					$bol_primera_pest = false;
				}
				
            	if ($ind_at == 1) {
			?>
        	<!--INICIO OTROS SERVICIOS-->
            <div class="content<?php if ($bol_primera_pest) { ?> active<?php } ?>" id="panel2-4">
            	<div style="width:98%; overflow:auto; text-align:left">
                	<table border="0" cellpadding="1" cellspacing="0" style="width:100%;">
                    	<tr>
                        	<td align="right" style="width:8%;">
			                	<label class="inline">Filtrar por:&nbsp;</label>
                            </td>
                            <td align="left" style="width:23%;">
			                    <input type="text" id="txt_buscar_at" placeholder="Nombre o n&uacute;mero de documento" style="width:100%;" onkeyup="filtrar_at();" />
                            </td>
                            <td align="left" style="width:13%;">
			                    <input type="text" id="txt_buscar_factura_at" placeholder="No. de factura" style="width:100%;" onkeyup="filtrar_at();" />
                            </td>
                            <td align="left" style="width:11%;">
			                    <input type="text" id="txt_buscar_servicio_at" placeholder="C&oacute;digo" style="width:100%;" onkeyup="filtrar_at();" />
                            </td>
                            <td align="left" style="width:11%;">
                                <input type="text" id="txt_buscar_valor_at" placeholder="Valor" style="width:100%;" onkeyup="filtrar_at();" />
                            </td>
                        	<td align="right" style="width:8%;">
			                	<label class="inline">Revisado:&nbsp;</label>
                            </td>
                            <td align="left" style="width:10%;">
                            	<select id="cmb_revisado_at" onchange="filtrar_at();">
                                	<option value="2">&lt;Todos&gt;</option>
                                    <option value="1">Si</option>
                                    <option value="0">No</option>
                                </select>
                            </td>
                        	<td align="right" style="width:6%;">
			                	<label class="inline">Excluido:&nbsp;</label>
                            </td>
                            <td align="left" style="width:10%;">
                            	<select id="cmb_borrado_at" onchange="filtrar_at();">
                                	<option value="2">&lt;Todos&gt;</option>
                                    <option value="1">Si</option>
                                    <option value="0">No</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                        	<td align="left" colspan="8">
                            	<b>N&uacute;mero de registros:&nbsp;<span id="span_num_reg_at">0</span></b>
                            </td>
                        </tr>
                    </table>
                </div>
            	<div id="d_registros_at" style="width:98%; overflow:hidden;"></div>
                <script id="ajax" type="text/javascript">
					cargar_rips_at("<?php echo($ind_rips_existentes); ?>");
				</script>
                <div style="width:98%; text-align:center">
                	<br />
                	<input type="button" id="btn_guardar_at" class="btnPrincipal" value="Guardar servicios" onclick="guardar_at(1);" />
                </div>
                <div id="d_guardar_at" style="display:none;"></div>
            </div>
            <!--FIN OTROS SERVICIOS-->
            <?php
					$bol_primera_pest = false;
				}
				
            	if ($ind_us == 1) {
			?>
        	<!--INICIO USUARIOS-->
            <div class="content<?php if ($bol_primera_pest) { ?> active<?php } ?>" id="panel2-5">
                <div style="width:98%; text-align:center">
                	<input type="button" id="btn_recalcular_us" class="btnPrincipal" value="Actualizar usuarios" onclick="cargar_rips_us(0, 1);" />
                </div>
            	<div style="width:98%; overflow:hidden; text-align:left">
                	<table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                    	<tr>
                        	<td align="right" style="width:10%;">
			                	<label class="inline">Filtrar por:&nbsp;&nbsp;</label>
                            </td>
                            <td align="left" style="width:55%;">
			                    <input type="text" id="txt_buscar_us" placeholder="Nombre o n&uacute;mero de documento" style="width:400px;" onkeyup="filtrar_us();" />
                            </td>
                        	<td align="right" style="width:8%;">
			                	<label class="inline">Revisado:&nbsp;&nbsp;</label>
                            </td>
                            <td align="left" style="width:10%;">
                            	<select id="cmb_revisado_us" onchange="filtrar_us();">
                                	<option value="2">&lt;Todos&gt;</option>
                                    <option value="1">Si</option>
                                    <option value="0">No</option>
                                </select>
                            </td>
                        	<td align="right" style="width:7%;">
			                	<label class="inline">Excluido:&nbsp;</label>
                            </td>
                            <td align="left" style="width:10%;">
                            	<select id="cmb_borrado_us" onchange="filtrar_us();">
                                	<option value="2">&lt;Todos&gt;</option>
                                    <option value="1">Si</option>
                                    <option value="0">No</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                        	<td align="left" colspan="6">
                            	<b>N&uacute;mero de registros:&nbsp;<span id="span_num_reg_us">0</span></b>
                            </td>
                        </tr>
                    </table>
                </div>
            	<div id="d_registros_us" style="width:98%; overflow:hidden;"></div>
                <script id="ajax" type="text/javascript">
					cargar_rips_us("<?php echo($ind_rips_existentes); ?>", 0);
				</script>
                <div style="width:98%; text-align:center">
                	<br />
                	<input type="button" id="btn_guardar_us" class="btnPrincipal" value="Guardar usuarios" onclick="guardar_us(1);" />
                </div>
                <div id="d_guardar_us" style="display:none;"></div>
            </div>
            <!--FIN USUARIOS-->
            <?php
					$bol_primera_pest = false;
				}
				
            	if ($ind_af == 1) {
					/*Tipos de carga: 1. RIPS existentes - 2. Historia clínica*/
					$tipo_carga = 0;
					if ($id_rips > 0 && $ind_rips_existentes == 0) {
						$tipo_carga = 1;
					} else {
						$tipo_carga = 2;
					}
					
					//Se cargan los datos de facturas
					if ($tipo_carga == 1) {
						//Se obtienen los datos de los RIPS existentes
						$lista_rips_af = $db_rips->get_lista_rips_facturas($id_rips);
					} else {
						$lista_rips_af = array();
					}
					
					//Se obtiene la fecha actual
					$obj_fecha_act = $db_variables->getFechaActualMostrar();
			?>
        	<!--INICIO FACTURAS-->
            <div class="content<?php if ($bol_primera_pest) { ?> active<?php } ?>" id="panel2-6">
                <div style="width:98%; text-align:center">
                	<input type="button" id="btn_recalcular_af" class="btnPrincipal" value="Recalcular facturas" onclick="recalcular_af();" />
                </div>
            	<div style="width:98%; overflow:hidden;">
                	<div id="d_tbl_encabezado_af" style="overflow:hidden; width:100%;">
                    <table class="modal_table" style="width:1800px; margin:auto;">
                        <thead>
                            <tr>
                                <th class="th_reducido" align="center" style="width:50px;">#</th>
                                <th class="th_reducido" align="center" style="width:100px;">No. Factura</th>
                                <th class="th_reducido" align="center" style="width:100px;">Fecha expedici&oacute;n</th>
                                <th class="th_reducido" align="center" style="width:100px;">Fecha inicial</th>
                                <th class="th_reducido" align="center" style="width:100px;">Fecha final</th>
                                <th class="th_reducido" align="center" style="width:100px;">C&oacute;digo administradora</th>
                                <th class="th_reducido" align="center" style="width:250px;">Nombre administradora</th>
                                <th class="th_reducido" align="center" style="width:100px;">No. contrato</th>
                                <th class="th_reducido" align="center" style="width:250px;">Plan de beneficios</th>
                                <th class="th_reducido" align="center" style="width:250px;">No. p&oacute;liza</th>
                                <th class="th_reducido" align="center" style="width:100px;">Valor copago</th>
                                <th class="th_reducido" align="center" style="width:100px;">Valor comisi&oacute;n</th>
                                <th class="th_reducido" align="center" style="width:100px;">Valor descuentos</th>
                                <th class="th_reducido" align="center" style="width:100px;">Valor neto a pagar</th>
                            </tr>
                        </thead>
                    </table>
                    </div>
                    <div id="d_tbl_cuerpo_af" style="overflow:auto; width:100%; height:420px;" onscroll="desplazar_tabla_horizontal('af');">
                    <table class="modal_table" style="width:1800px; margin:auto;">
                        <?php
							$cont_fila = 0;
                        	if (count($lista_rips_af) > 0) {
								foreach ($lista_rips_af as $rips_af_aux) {
						?>
                        <tr id="tr_reg_af_<?php echo($cont_fila); ?>">
                            <td align="center" id="td_num_fila_af_<?php echo($cont_fila); ?>" style="width:50px;">
								<?php echo($cont_fila + 1); ?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_num_factura_af_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_af_aux["num_factura"]); ?>" readonly="readonly" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_fecha_factura_af_<?php echo($cont_fila); ?>" class="input_sin_margen" maxlength="10" style="width:100%;" value="<?php echo($rips_af_aux["fecha_factura_t"]); ?>" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
								<script type="text/javascript" id="ajax">
									$('#txt_fecha_factura_af_<?php echo($cont_fila); ?>').fdatepicker({
										format: 'dd/mm/yyyy'
									});
                                </script>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_fecha_ini_af_<?php echo($cont_fila); ?>" class="input_sin_margen" maxlength="10" style="width:100%;" value="<?php echo($rips_af_aux["fecha_ini_t"]); ?>" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
								<script type="text/javascript" id="ajax">
									$('#txt_fecha_ini_af_<?php echo($cont_fila); ?>').fdatepicker({
										format: 'dd/mm/yyyy'
									});
                                </script>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_fecha_fin_af_<?php echo($cont_fila); ?>" class="input_sin_margen" maxlength="10" style="width:100%;" value="<?php echo($rips_af_aux["fecha_fin_t"]); ?>" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
								<script type="text/javascript" id="ajax">
									$('#txt_fecha_fin_af_<?php echo($cont_fila); ?>').fdatepicker({
										format: 'dd/mm/yyyy'
									});
                                </script>
                            </td>
                            <td align="center" class="td_reducido" style="width:100px;">
                            	<?php echo($rips_af_aux["cod_administradora"]); ?>
                            </td>
                            <td align="left" class="td_reducido" style="width:250px;">
                            	<?php echo($rips_af_aux["nombre_administradora"]); ?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_num_contrato_af_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_af_aux["num_contrato"]); ?>" />
                            </td>
                            <td align="center" style="width:250px;">
                            	<input type="text" id="txt_plan_benef_af_<?php echo($cont_fila); ?>" maxlength="30" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_af_aux["plan_benef"]); ?>" />
                            </td>
                            <td align="center" style="width:250px;">
                            	<input type="text" id="txt_num_poliza_af_<?php echo($cont_fila); ?>" maxlength="10" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_af_aux["num_poliza"]); ?>" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_copago_af_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_af_aux["valor_copago"]); ?>" onkeypress="return solo_numeros(event, true);" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_comision_af_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_af_aux["valor_comision"]); ?>" onkeypress="return solo_numeros(event, true);" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_descuento_af_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_af_aux["valor_descuento"]); ?>" onkeypress="return solo_numeros(event, true);" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_neto_af_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_af_aux["valor_neto"]); ?>" onkeypress="return solo_numeros(event, true);" />
                            </td>
                        </tr>
                        <?php
									$cont_fila++;
								}
							}
						?>
                        <tr style="display:none;">
                        	<td>
                            	<input type="hidden" id="hdd_cant_registros_af" value="<?php echo($cont_fila); ?>" />
                            </td>
                        </tr>
                        <?php
							for ($i = 0; $i < 100; $i++) {
						?>
                        <tr id="tr_reg_af_<?php echo($cont_fila); ?>" style="display:none;">
                            <td align="center" id="td_num_fila_af_<?php echo($cont_fila); ?>" style="width:50px;">
								<?php echo($cont_fila + 1); ?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_num_factura_af_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="" readonly="readonly" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_fecha_factura_af_<?php echo($cont_fila); ?>" class="input_sin_margen" maxlength="10" style="width:100%;" value="<?php echo($fecha_final); ?>" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
								<script type="text/javascript" id="ajax">
									$('#txt_fecha_factura_af_<?php echo($cont_fila); ?>').fdatepicker({
										format: 'dd/mm/yyyy'
									});
                                </script>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_fecha_ini_af_<?php echo($cont_fila); ?>" class="input_sin_margen" maxlength="10" style="width:100%;" value="<?php echo($fecha_inicial); ?>" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
								<script type="text/javascript" id="ajax">
									$('#txt_fecha_ini_af_<?php echo($cont_fila); ?>').fdatepicker({
										format: 'dd/mm/yyyy'
									});
                                </script>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_fecha_fin_af_<?php echo($cont_fila); ?>" class="input_sin_margen" maxlength="10" style="width:100%;" value="<?php echo($fecha_final); ?>" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
								<script type="text/javascript" id="ajax">
									$('#txt_fecha_fin_af_<?php echo($cont_fila); ?>').fdatepicker({
										format: 'dd/mm/yyyy'
									});
                                </script>
                            </td>
                            <td align="center" class="td_reducido" style="width:100px;">
                            	<?php echo($convenio_obj["cod_administradora"]); ?>
                            </td>
                            <td align="left" class="td_reducido" style="width:250px;">
                            	<?php
									$nombre_administradora_aux = substr(str_replace(",", "", $convenio_obj["nombre_convenio"]), 0, 30);
                                	echo($nombre_administradora_aux);
								?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_num_contrato_af_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="" />
                            </td>
                            <td align="center" style="width:250px;">
                            	<input type="text" id="txt_plan_benef_af_<?php echo($cont_fila); ?>" maxlength="30" class="input_sin_margen" style="width:100%;" value="" />
                            </td>
                            <td align="center" style="width:250px;">
                            	<input type="text" id="txt_num_poliza_af_<?php echo($cont_fila); ?>" maxlength="10" class="input_sin_margen" style="width:100%;" value="" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_copago_af_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_numeros(event, true);" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_comision_af_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_numeros(event, true);" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_descuento_af_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_numeros(event, true);" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_neto_af_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_numeros(event, true);" />
                            </td>
                        </tr>
                        <?php
								$cont_fila++;
							}
						?>
                    </table>
                    </div>
                    <?php
                    	if (count($lista_rips_af) == 0) {
					?>
                    <script type="text/javascript" id="ajax">
						recalcular_af();
					</script>
                    <?php
						}
					?>
                </div>
                <div style="width:98%; text-align:center">
                	<br />
                	<input type="button" id="btn_guardar_af" class="btnPrincipal" value="Guardar facturas" onclick="guardar_af(1);" />
                </div>
                <div id="d_guardar_af" style="display:none;"></div>
            </div>
            <!--FIN FACTURAS-->
            <?php
					$bol_primera_pest = false;
				}
				
            	if ($ind_ad == 1) {
					/*Tipos de carga: 1. RIPS existentes - 2. Historia clínica*/
					$tipo_carga = 0;
					if ($id_rips > 0 && $ind_rips_existentes == 0) {
						$tipo_carga = 1;
					} else {
						$tipo_carga = 2;
					}
					
					//Se cargan los datos de descripción
					if ($tipo_carga == 1) {
						//Se obtienen los datos de los RIPS existentes
						$lista_rips_ad = $db_rips->get_lista_rips_descripcion($id_rips);
					} else {
						$lista_rips_ad = array();
					}
			?>
        	<!--INICIO DESCRIPCIÓN-->
            <div class="content<?php if ($bol_primera_pest) { ?> active<?php } ?>" id="panel2-7">
                <div style="width:98%; text-align:center">
                	<input type="button" id="btn_recalcular_ad" class="btnPrincipal" value="Recalcular descripci&oacute;n" onclick="recalcular_ad();" />
                </div>
            	<div style="width:98%; height:460px; overflow:auto;">
                    <table class="modal_table" style="width:800px; margin:auto;" >
                        <thead>
                            <tr>
                                <th class="th_reducido" align="center" style="width:50px;">#</th>
                                <th class="th_reducido" align="center" style="width:100px;">No. Factura</th>
                                <th class="th_reducido" align="center" style="width:350px;">Concepto</th>
                                <th class="th_reducido" align="center" style="width:100px;">Cantidad</th>
                                <th class="th_reducido" align="center" style="width:100px;">Valor unitario</th>
                                <th class="th_reducido" align="center" style="width:100px;">Valor total</th>
                            </tr>
                        </thead>
                        <?php
							//listado de conceptos
							$lista_conceptos = $db_listas->getListaDetalles(39);
							
							$cont_fila = 0;
                        	if (count($lista_rips_ad) > 0) {
								foreach ($lista_rips_ad as $rips_ad_aux) {
						?>
                        <tr id="tr_reg_ad_<?php echo($cont_fila); ?>">
                            <td align="center" id="td_num_fila_ad_<?php echo($cont_fila); ?>">
								<?php echo($cont_fila + 1); ?>
                            </td>
                            <td align="center">
                            	<input type="text" id="txt_num_factura_ad_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_ad_aux["num_factura"]); ?>" readonly="readonly" />
                            </td>
                            <td align="center" class="td_reducido">
                            	<?php
                                	$combo->getComboDb("cmb_cod_concepto_ad_".$cont_fila, $rips_ad_aux["cod_concepto"], $lista_conceptos, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center">
                            	<input type="text" id="txt_cantidad_ad_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_ad_aux["cantidad"]); ?>" onkeypress="return solo_numeros(event, false);" onblur="calcular_total_ad(<?php echo($cont_fila); ?>);" />
                            </td>
                            <td align="center">
                            	<input type="text" id="txt_valor_unitario_ad_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_ad_aux["valor_unitario"]); ?>" onkeypress="return solo_numeros(event, false);" onblur="calcular_total_ad(<?php echo($cont_fila); ?>);" />
                            </td>
                            <td align="center">
                            	<input type="text" id="txt_valor_total_ad_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="<?php echo(intval($rips_ad_aux["cantidad"], 10) * floatval("0".$rips_ad_aux["valor_unitario"])); ?>" readonly="readonly" />
                            </td>
                        </tr>
                        <?php
									$cont_fila++;
								}
							}
						?>
                        <tr style="display:none;">
                        	<td>
                            	<input type="hidden" id="hdd_cant_registros_ad" value="<?php echo($cont_fila); ?>" />
                            </td>
                        </tr>
                        <?php
							for ($i = 0; $i < 200; $i++) {
						?>
                        <tr id="tr_reg_ad_<?php echo($cont_fila); ?>" style="display:none;">
                            <td align="center" id="td_num_fila_ad_<?php echo($cont_fila); ?>">
								<?php echo($cont_fila + 1); ?>
                            </td>
                            <td align="center">
                            	<input type="text" id="txt_num_factura_ad_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="" readonly="readonly" />
                            </td>
                            <td align="center" class="td_reducido">
                            	<?php
                                	$combo->getComboDb("cmb_cod_concepto_ad_".$cont_fila, "", $lista_conceptos, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center">
                            	<input type="text" id="txt_cantidad_ad_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_numeros(event, false);" onblur="calcular_total_ad(<?php echo($cont_fila); ?>);" />
                            </td>
                            <td align="center">
                            	<input type="text" id="txt_valor_unitario_ad_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_numeros(event, true);" onblur="calcular_total_ad(<?php echo($cont_fila); ?>);" />
                            </td>
                            <td align="center">
                            	<input type="text" id="txt_valor_total_ad_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="" readonly="readonly" />
                            </td>
                        </tr>
                        <?php
								$cont_fila++;
							}
						?>
                    </table>
                    <?php
                    	if (count($lista_rips_ad) == 0) {
					?>
                    <script type="text/javascript" id="ajax">
						recalcular_ad();
					</script>
                    <?php
						}
					?>
                </div>
                <div style="width:98%; text-align:center">
                	<br />
                	<input type="button" id="btn_guardar_ad" class="btnPrincipal" value="Guardar descripci&oacute;n" onclick="guardar_ad(1);" />
                </div>
                <div id="d_guardar_ad" style="display:none;"></div>
            </div>
            <!--FIN DESCRIPCIÓN-->
            <?php
					$bol_primera_pest = false;
				}
				
            	if ($ind_ct == 1) {
					$cont_aux = 0;
			?>
        	<!--INICIO CONTROL-->
            <div class="content<?php if ($bol_primera_pest) { ?> active<?php } ?>" id="panel2-8">
                <div style="width:98%; text-align:center">
                	<input type="button" id="btn_recalcular_ct" class="btnPrincipal" value="Recalcular control" onclick="recalcular_ct();" />
                </div>
            	<div style="width:98%; height:460px; overflow:auto;">
                    <table class="modal_table" style="width:250px; margin:auto;" >
                        <thead>
                            <tr>
                                <th class="th_reducido" align="center" style="width:50px;">#</th>
                                <th class="th_reducido" align="center" style="width:100px;">Archivo</th>
                                <th class="th_reducido" align="center" style="width:100px;">Cantidad</th>
                            </tr>
                        </thead>
                        <tr style="display:none;">
                        	<td>
                            	<input type="hidden" id="hdd_cant_registros_ct" value="0" />
                            </td>
                        </tr>
                        <?php
							for ($i = 0; $i < 20; $i++) {
						?>
                        <tr id="tr_reg_ct_<?php echo($i); ?>" style="display:none;">
                            <td align="center" id="td_num_fila_ct_<?php echo($i); ?>">
								<?php echo($i + 1); ?>
                            </td>
                            <td align="center" id="td_cod_archivo_ct_<?php echo($i); ?>" class="td_reducido"></td>
                            <td align="center" id="td_cantidad_ct_<?php echo($i); ?>" class="td_reducido"></td>
                        </tr>
                        <?php
							}
						?>
                    </table>
                    <script type="text/javascript" id="ajax">
						setTimeout(function () { recalcular_ct(); }, 7500);
					</script>
                </div>
            </div>
            <!--FIN CONTROL-->
            <?php
					$bol_primera_pest = false;
				}
			?>
        </div>
    </div>
    <div style="width:98%; text-align:center">
        <input type="button" id="btn_generar_rips" class="btnPrincipal" value="Generar archivos del RIPS" onclick="generar_rips();" /> 
    </div>
    <script id="ajax" type="text/javascript">
		$(document).foundation();
	</script>
    <?php
        break;
		
	case "3": //Se muestran los archivos disponibles para descarga
		@$id_convenio = intval($utilidades->str_decode($_POST["id_convenio"]), 10);
		@$id_plan = intval($utilidades->str_decode($_POST["id_plan"]), 10);
		
		//Se obtiene el listado de archivos en la carpeta de rips
		$arr_arch_aux = scandir("./rips/");
		$arr_archivos = array();
		
		if (count($arr_arch_aux) > 0) {
			//Se crea el filtro de archivos dependiendo del convenio y plan seleccionados
			$pref_arch = "rips_";
			if ($id_convenio > 0) {
				$pref_arch .= $id_convenio."_";
				if ($id_plan > 0) {
					$pref_arch .= $id_plan."_";
				}
			}
			
			foreach ($arr_arch_aux as $arch_aux) {
				$pos_aux = strpos($arch_aux, $pref_arch);
				if ($pos_aux === 0 && substr($arch_aux, -4) == ".zip") {
					array_push($arr_archivos, $arch_aux);
				}
			}
		}
	?>
    <table class="modal_table" style="width:100%; margin: auto;" >
    	<thead>
        	<tr>
            	<th class="th_reducido" align="center" colspan="5">Archivos disponibles para descarga</th>
            </tr>
        	<tr>
            	<th class="th_reducido" align="center" style="width:30%;">Convenio</th>
            	<th class="th_reducido" align="center" style="width:25%;">Plan</th>
            	<th class="th_reducido" align="center" style="width:15%;">Fecha inicial</th>
            	<th class="th_reducido" align="center" style="width:15%;">Fecha final</th>
            	<th class="th_reducido" align="center" style="width:15%;">Descargar</th>
            </tr>
        </thead>
        <?php
        	if (!is_null($arr_archivos) && count($arr_archivos) > 0) {
				foreach ($arr_archivos as $arch_aux) {
					//Se obtienen los datos del archivo de su nombre
					$arr_aux = explode(".", $arch_aux);
					$arr_aux = explode("_", $arr_aux[0]);
					$id_convenio = intval($arr_aux[1], 10);
					$id_plan = intval($arr_aux[2], 10);
					$fecha_inicial = $arr_aux[3];
					$fecha_final = $arr_aux[4];
					
					//Se reacomodan las fechas al formato dd/mm/aaaa
					$arr_aux = explode("-", $fecha_inicial);
					$fecha_inicial = $arr_aux[2]."/".$arr_aux[1]."/".$arr_aux[0];
					$arr_aux = explode("-", $fecha_final);
					$fecha_final = $arr_aux[2]."/".$arr_aux[1]."/".$arr_aux[0];
					
					//Se obtienen los datos del convenio
					$convenio_obj = $db_convenios->getConvenio($id_convenio);
					$nombre_convenio = "";
					if (!is_null($convenio_obj) && count($convenio_obj) > 0) {
						$nombre_convenio = $convenio_obj["nombre_convenio"];
					}
					
					//Se obtienen los datos del plan
					$plan_obj = $db_planes->getPlan($id_plan);
					$nombre_plan = "(Todos los planes)";
					if (!is_null($plan_obj) &&  count($plan_obj) > 0) {
						$nombre_plan = $plan_obj["nombre_plan"];
					}
		?>
        <tr class="celdagrid">
        	<td align="left"><?php echo($nombre_convenio); ?></td>
        	<td align="left"><?php echo($nombre_plan); ?></td>
        	<td align="center"><?php echo($fecha_inicial); ?></td>
        	<td align="center"><?php echo($fecha_final); ?></td>
        	<td align="center">
            	<a href="rips/<?php echo($arch_aux); ?>" target="_new">
                	<img src="../imagenes/zip-icon.png" width="24" />
                </a>
            </td>
        </tr>
        <?php
				}
			} else {
		?>
        <tr class="celdagrid">
        	<td align="center" colspan="5">
				No se encontraron archivos para descargar
            </td>
        </tr>
        <?php
			}
		?>
    </table>
    <?php
		break;
		
    case "4": 
	//Generación de RIPS
	
        break;
		
	case "5": //Búsqueda de nombre de procedimiento
		require_once("../db/DbMaestroProcedimientos.php");
		$db_maestro_procedimientos = new DbMaestroProcedimientos();
		
		@$cod_procedimiento = $utilidades->str_decode($_POST["cod_procedimiento"]);
		
		if ($cod_procedimiento != "") {
			$procedimiento_obj = $db_maestro_procedimientos->getProcedimiento($cod_procedimiento);
			
			if (isset($procedimiento_obj["nombre_procedimiento"])) {
				echo($procedimiento_obj["nombre_procedimiento"]);
			} else {
		?>
		<span style="color:#Fe6B6B;">Procedimiento no hallado</span>
		<?php
			}
		}
		break;
		
	case "6": //Búsqueda de nombre de ciex
		require_once("../db/DbDiagnosticos.php");
		$db_diagnosticos = new DbDiagnosticos();
		
		@$cod_ciex = $utilidades->str_decode($_POST["cod_ciex"]);
		
		if ($cod_ciex != "") {
			$ciex_obj = $db_diagnosticos->getDiagnosticoCiex($cod_ciex);
			
			if (isset($ciex_obj["nombre"])) {
				echo($ciex_obj["nombre"]);
			} else {
		?>
		<span style="color:#Fe6B6B;">Diagn&oacute;stico CIEX no hallado</span>
		<?php
			}
		}
		break;
		
	case "7": //Guardar RIPS de consultas
		$id_usuario = $_SESSION["idUsuario"];
		
		@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
		@$id_plan = $utilidades->str_decode($_POST["id_plan"]);
		@$fecha_inicial = $utilidades->str_decode($_POST["fecha_inicial"]);
		@$fecha_final = $utilidades->str_decode($_POST["fecha_final"]);
		@$id_prestador = $utilidades->str_decode($_POST["id_prestador"]);
		@$cant_registros_ac = intval($_POST["cant_registros_ac"], 10);
		
		$arr_id_ac = array();
		$arr_rips_ac = array();
		for ($i = 0; $i < $cant_registros_ac; $i++) {
			@$arr_id_ac[$i] = intval($_POST["id_rips_ac_".$i], 10);
			@$arr_rips_ac[$i]["id_rips_ac"] = intval($_POST["id_rips_ac_".$i], 10);
			@$arr_rips_ac[$i]["id_admision"] = $utilidades->str_decode($_POST["id_admision_ac_".$i]);
			@$arr_rips_ac[$i]["id_detalle_precio"] = $utilidades->str_decode($_POST["id_detalle_precio_ac_".$i]);
			@$arr_rips_ac[$i]["id_paciente"] = $utilidades->str_decode($_POST["id_paciente_ac_".$i]);
			@$arr_rips_ac[$i]["id_hc"] = $utilidades->str_decode($_POST["id_hc_ac_".$i]);
			@$arr_rips_ac[$i]["ind_revisado"] = $utilidades->str_decode($_POST["ind_revisado_ac_".$i]);
			@$arr_rips_ac[$i]["ind_borrado"] = $utilidades->str_decode($_POST["ind_borrado_ac_".$i]);
			@$arr_rips_ac[$i]["num_factura"] = $utilidades->str_decode($_POST["num_factura_ac_".$i]);
			@$arr_rips_ac[$i]["id_convenio"] = $utilidades->str_decode($_POST["id_convenio_ac_".$i]);
			@$arr_rips_ac[$i]["id_plan"] = $utilidades->str_decode($_POST["id_plan_ac_".$i]);
			@$arr_rips_ac[$i]["tipo_documento"] = $utilidades->str_decode($_POST["tipo_documento_ac_".$i]);
			@$arr_rips_ac[$i]["numero_documento"] = $utilidades->str_decode($_POST["numero_documento_ac_".$i]);
			@$arr_rips_ac[$i]["fecha_consulta"] = $utilidades->str_decode($_POST["fecha_consulta_ac_".$i]);
			@$arr_rips_ac[$i]["num_autorizacion"] = $utilidades->str_decode($_POST["num_autorizacion_ac_".$i]);
			@$arr_rips_ac[$i]["cod_procedimiento"] = $utilidades->str_decode($_POST["cod_procedimiento_ac_".$i]);
			@$arr_rips_ac[$i]["fin_consulta"] = $utilidades->str_decode($_POST["fin_consulta_ac_".$i]);
			@$arr_rips_ac[$i]["causa_ext"] = $utilidades->str_decode($_POST["causa_ext_ac_".$i]);
			@$arr_rips_ac[$i]["cod_ciex_prin"] = $utilidades->str_decode($_POST["cod_ciex_ac_".$i."_0"]);
			@$arr_rips_ac[$i]["cod_ciex_rel1"] = $utilidades->str_decode($_POST["cod_ciex_ac_".$i."_1"]);
			@$arr_rips_ac[$i]["cod_ciex_rel2"] = $utilidades->str_decode($_POST["cod_ciex_ac_".$i."_2"]);
			@$arr_rips_ac[$i]["cod_ciex_rel3"] = $utilidades->str_decode($_POST["cod_ciex_ac_".$i."_3"]);
			@$arr_rips_ac[$i]["tipo_diag_prin"] = $utilidades->str_decode($_POST["tipo_diag_prin_ac_".$i]);
			@$arr_rips_ac[$i]["valor_consulta"] = floatval("0".$_POST["valor_consulta_ac_".$i]);
			@$arr_rips_ac[$i]["valor_cuota"] = floatval("0".$_POST["valor_cuota_ac_".$i]);
			@$arr_rips_ac[$i]["observaciones"] = trim($utilidades->str_decode($_POST["observaciones_ac_".$i]));
		}
		
		if (isset($_POST["cant_registros_ac"])) {
			//Se pudieron recibir todos los datos
	?>
    <input type="hidden" id="hdd_ac_ok_carga" value="1" />
    <?php
			//Se borran los registros que no se encuentren en el listado de ids
			$resul_aux = $db_rips->borrar_rips_consultas($id_convenio, $id_plan, $fecha_inicial, $fecha_final, $arr_id_ac);
	?>
    <input type="hidden" id="hdd_borrar_ac_resul" value="<?php echo($resul_aux); ?>" />
    <?php
			if ($resul_aux > 0) {
				//Se obtiene el código del prestador
				$datos_entidad_obj = $db_datos_entidad->getDatosEntidadId($id_prestador);
	?>
    <input type="hidden" id="hdd_cant_registros_ac_resul" value="<?php echo($cant_registros_ac); ?>" />
    <?php
				if ($cant_registros_ac > 0) {
					foreach ($arr_rips_ac as $i => $ac_aux) {
						$resul_aux = $db_rips->crear_rips_consulta($ac_aux["id_rips_ac"], $ac_aux["id_detalle_precio"], $ac_aux["id_admision"], $ac_aux["id_paciente"],
									 $ac_aux["id_hc"], $ac_aux["ind_revisado"], $ac_aux["ind_borrado"], $ac_aux["num_factura"], $ac_aux["id_convenio"], $ac_aux["id_plan"],
									 $datos_entidad_obj["cod_prestador"], $ac_aux["tipo_documento"], $ac_aux["numero_documento"], $ac_aux["fecha_consulta"],
									 $ac_aux["num_autorizacion"], $ac_aux["cod_procedimiento"], $ac_aux["fin_consulta"], $ac_aux["causa_ext"], $ac_aux["cod_ciex_prin"],
									 $ac_aux["cod_ciex_rel1"], $ac_aux["cod_ciex_rel2"], $ac_aux["cod_ciex_rel3"], $ac_aux["tipo_diag_prin"], $ac_aux["valor_consulta"],
									 $ac_aux["valor_cuota"], $ac_aux["observaciones"], $id_usuario);
	?>
    <input type="hidden" id="hdd_ac_resul_<?php echo($i); ?>" value="<?php echo($resul_aux); ?>" />
    <?php
					}
				}
			}
		} else {
			//Hubo un problema al recibir todos los datos
	?>
    <input type="hidden" id="hdd_ac_ok_carga" value="-1" />
    <?php
		}
		break;
		
	case "8": //Guardar RIPS de procedimientos
		$id_usuario = $_SESSION["idUsuario"];
		
		@$id_rips = $utilidades->str_decode($_POST["id_rips"]);
		@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
		@$id_plan = $utilidades->str_decode($_POST["id_plan"]);
		@$fecha_inicial = $utilidades->str_decode($_POST["fecha_inicial"]);
		@$fecha_final = $utilidades->str_decode($_POST["fecha_final"]);
		@$id_prestador = $utilidades->str_decode($_POST["id_prestador"]);
		@$cant_registros_ap = intval($_POST["cant_registros_ap"], 10);
		
		$arr_id_ap = array();
		$arr_rips_ap = array();
		for ($i = 0; $i < $cant_registros_ap; $i++) {
			@$arr_id_ap[$i] = intval($_POST["id_rips_ap_".$i], 10);
			@$arr_rips_ap[$i]["id_rips_ap"] = intval($_POST["id_rips_ap_".$i], 10);
			@$arr_rips_ap[$i]["id_admision"] = $utilidades->str_decode($_POST["id_admision_ap_".$i]);
			@$arr_rips_ap[$i]["id_detalle_precio"] = $utilidades->str_decode($_POST["id_detalle_precio_ap_".$i]);
			@$arr_rips_ap[$i]["id_paciente"] = $utilidades->str_decode($_POST["id_paciente_ap_".$i]);
			@$arr_rips_ap[$i]["id_hc"] = $utilidades->str_decode($_POST["id_hc_ap_".$i]);
			@$arr_rips_ap[$i]["ind_revisado"] = $utilidades->str_decode($_POST["ind_revisado_ap_".$i]);
			@$arr_rips_ap[$i]["ind_borrado"] = $utilidades->str_decode($_POST["ind_borrado_ap_".$i]);
			@$arr_rips_ap[$i]["num_factura"] = $utilidades->str_decode($_POST["num_factura_ap_".$i]);
			@$arr_rips_ap[$i]["id_convenio"] = $utilidades->str_decode($_POST["id_convenio_ap_".$i]);
			@$arr_rips_ap[$i]["id_plan"] = $utilidades->str_decode($_POST["id_plan_ap_".$i]);
			@$arr_rips_ap[$i]["tipo_documento"] = $utilidades->str_decode($_POST["tipo_documento_ap_".$i]);
			@$arr_rips_ap[$i]["numero_documento"] = $utilidades->str_decode($_POST["numero_documento_ap_".$i]);
			@$arr_rips_ap[$i]["fecha_pro"] = $utilidades->str_decode($_POST["fecha_pro_ap_".$i]);
			@$arr_rips_ap[$i]["num_autorizacion"] = $utilidades->str_decode($_POST["num_autorizacion_ap_".$i]);
			@$arr_rips_ap[$i]["cod_procedimiento"] = $utilidades->str_decode($_POST["cod_procedimiento_ap_".$i]);
			@$arr_rips_ap[$i]["amb_rea"] = $utilidades->str_decode($_POST["amb_rea_ap_".$i]);
			@$arr_rips_ap[$i]["fin_pro"] = $utilidades->str_decode($_POST["fin_pro_ap_".$i]);
			@$arr_rips_ap[$i]["per_ati"] = $utilidades->str_decode($_POST["per_ati_ap_".$i]);
			@$arr_rips_ap[$i]["cod_ciex_prin"] = $utilidades->str_decode($_POST["cod_ciex_prin_ap_".$i]);
			@$arr_rips_ap[$i]["cod_ciex_rel"] = $utilidades->str_decode($_POST["cod_ciex_rel_ap_".$i]);
			@$arr_rips_ap[$i]["cod_ciex_com"] = $utilidades->str_decode($_POST["cod_ciex_com_ap_".$i]);
			@$arr_rips_ap[$i]["for_rea"] = $utilidades->str_decode($_POST["for_rea_ap_".$i]);
			@$arr_rips_ap[$i]["valor_pro"] = floatval("0".$_POST["valor_pro_ap_".$i]);
			@$arr_rips_ap[$i]["valor_copago"] = floatval("0".$_POST["valor_copago_ap_".$i]);
			@$arr_rips_ap[$i]["observaciones"] = trim($utilidades->str_decode($_POST["observaciones_ap_".$i]));
		}
		
		if (isset($_POST["cant_registros_ap"])) {
			//Se pudieron recibir todos los datos
	?>
    <input type="hidden" id="hdd_ap_ok_carga" value="1" />
    <?php
			//Se borran los registros que no se encuentren en el listado de ids
			$resul_aux = $db_rips->borrar_rips_procedimientos($id_convenio, $id_plan, $fecha_inicial, $fecha_final, $arr_id_ap);
	?>
    <input type="hidden" id="hdd_borrar_ap_resul" value="<?php echo($resul_aux); ?>" />
    <?php
			if ($resul_aux > 0) {
				//Se obtiene el código del prestador
				$datos_entidad_obj = $db_datos_entidad->getDatosEntidadId($id_prestador);
	?>
    <input type="hidden" id="hdd_cant_registros_ap_resul" value="<?php echo($cant_registros_ap); ?>" />
    <?php
				if ($cant_registros_ap > 0) {
					foreach ($arr_rips_ap as $i => $ap_aux) {
						$resul_aux = $db_rips->crear_rips_procedimiento($ap_aux["id_rips_ap"], $ap_aux["id_detalle_precio"], $ap_aux["id_admision"], $ap_aux["id_paciente"],
									 $ap_aux["id_hc"], $ap_aux["ind_revisado"], $ap_aux["ind_borrado"], $ap_aux["num_factura"], $ap_aux["id_convenio"], $ap_aux["id_plan"],
									 $datos_entidad_obj["cod_prestador"], $ap_aux["tipo_documento"], $ap_aux["numero_documento"], $ap_aux["fecha_pro"], $ap_aux["num_autorizacion"],
									 $ap_aux["cod_procedimiento"], $ap_aux["amb_rea"], $ap_aux["fin_pro"], $ap_aux["per_ati"], $ap_aux["cod_ciex_prin"], $ap_aux["cod_ciex_rel"],
									 $ap_aux["cod_ciex_com"], $ap_aux["for_rea"], $ap_aux["valor_pro"], $ap_aux["valor_copago"], $ap_aux["observaciones"], $id_usuario);
	?>
    <input type="hidden" id="hdd_ap_resul_<?php echo($i); ?>" value="<?php echo($resul_aux); ?>" />
    <?php
					}
				}
			}
		} else {
			//Hubo un problema al recibir todos los datos
	?>
    <input type="hidden" id="hdd_ap_ok_carga" value="-1" />
    <?php
		}
		break;
		
	case "9": //Guardar RIPS de medicamentos
		$id_usuario = $_SESSION["idUsuario"];
		
		@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
		@$id_plan = $utilidades->str_decode($_POST["id_plan"]);
		@$fecha_inicial = $utilidades->str_decode($_POST["fecha_inicial"]);
		@$fecha_final = $utilidades->str_decode($_POST["fecha_final"]);
		@$id_prestador = $utilidades->str_decode($_POST["id_prestador"]);
		@$cant_registros_am = intval($_POST["cant_registros_am"], 10);
		
		$arr_id_am = array();
		$arr_rips_am = array();
		for ($i = 0; $i < $cant_registros_am; $i++) {
			@$arr_id_am[$i] = intval($_POST["id_rips_am_".$i], 10);
			@$arr_rips_am[$i]["id_rips_am"] = intval($_POST["id_rips_am_".$i], 10);
			@$arr_rips_am[$i]["id_admision"] = $utilidades->str_decode($_POST["id_admision_am_".$i]);
			@$arr_rips_am[$i]["id_detalle_precio"] = $utilidades->str_decode($_POST["id_detalle_precio_am_".$i]);
			@$arr_rips_am[$i]["id_paciente"] = $utilidades->str_decode($_POST["id_paciente_am_".$i]);
			@$arr_rips_am[$i]["ind_revisado"] = $utilidades->str_decode($_POST["ind_revisado_am_".$i]);
			@$arr_rips_am[$i]["ind_borrado"] = $utilidades->str_decode($_POST["ind_borrado_am_".$i]);
			@$arr_rips_am[$i]["num_factura"] = $utilidades->str_decode($_POST["num_factura_am_".$i]);
			@$arr_rips_am[$i]["id_convenio"] = $utilidades->str_decode($_POST["id_convenio_am_".$i]);
			@$arr_rips_am[$i]["id_plan"] = $utilidades->str_decode($_POST["id_plan_am_".$i]);
			@$arr_rips_am[$i]["tipo_documento"] = $utilidades->str_decode($_POST["tipo_documento_am_".$i]);
			@$arr_rips_am[$i]["numero_documento"] = $utilidades->str_decode($_POST["numero_documento_am_".$i]);
			@$arr_rips_am[$i]["fecha_medicamento"] = $utilidades->str_decode($_POST["fecha_medicamento_am_".$i]);
			@$arr_rips_am[$i]["num_autorizacion"] = $utilidades->str_decode($_POST["num_autorizacion_am_".$i]);
			@$arr_rips_am[$i]["cod_medicamento"] = $utilidades->str_decode($_POST["cod_medicamento_am_".$i]);
			@$arr_rips_am[$i]["tipo_medicamento"] = $utilidades->str_decode($_POST["tipo_medicamento_am_".$i]);
			@$arr_rips_am[$i]["nombre_generico"] = $utilidades->str_decode(str_replace(",", "", $_POST["nombre_generico_am_".$i]));
			@$arr_rips_am[$i]["forma_farma"] = $utilidades->str_decode(str_replace(",", "", $_POST["forma_farma_am_".$i]));
			@$arr_rips_am[$i]["concentracion"] = $utilidades->str_decode(str_replace(",", "", $_POST["concentracion_am_".$i]));
			@$arr_rips_am[$i]["unidad_medida"] = $utilidades->str_decode(str_replace(",", "", $_POST["unidad_medida_am_".$i]));
			@$arr_rips_am[$i]["cantidad"] = intval($_POST["cantidad_am_".$i], 10);
			@$arr_rips_am[$i]["valor_medicamento"] = floatval("0".$_POST["valor_medicamento_am_".$i]);
			@$arr_rips_am[$i]["observaciones"] = trim($utilidades->str_decode($_POST["observaciones_am_".$i]));
		}
		
		if (isset($_POST["cant_registros_am"])) {
			//Se pudieron recibir todos los datos
	?>
    <input type="hidden" id="hdd_am_ok_carga" value="1" />
    <?php
			//Se borran los registros que no se encuentren en el listado de ids
			$resul_aux = $db_rips->borrar_rips_medicamentos($id_convenio, $id_plan, $fecha_inicial, $fecha_final, $arr_id_am);
	?>
    <input type="hidden" id="hdd_borrar_am_resul" value="<?php echo($resul_aux); ?>" />
    <?php
			if ($resul_aux > 0) {
				//Se obtiene el código del prestador
				$datos_entidad_obj = $db_datos_entidad->getDatosEntidadId($id_prestador);
	?>
    <input type="hidden" id="hdd_cant_registros_am_resul" value="<?php echo($cant_registros_am); ?>" />
    <?php
				if ($cant_registros_am > 0) {
					foreach ($arr_rips_am as $i => $am_aux) {
						$resul_aux = $db_rips->crear_rips_medicamento($am_aux["id_rips_am"], $am_aux["id_detalle_precio"], $am_aux["id_admision"], $am_aux["id_paciente"],
									 $am_aux["ind_revisado"], $am_aux["ind_borrado"], $am_aux["num_factura"], $am_aux["id_convenio"], $am_aux["id_plan"], $datos_entidad_obj["cod_prestador"],
									 $am_aux["tipo_documento"], $am_aux["numero_documento"], $am_aux["fecha_medicamento"], $am_aux["num_autorizacion"], $am_aux["cod_medicamento"],
									 $am_aux["tipo_medicamento"], $am_aux["nombre_generico"], $am_aux["forma_farma"], $am_aux["concentracion"], $am_aux["unidad_medida"],
									 $am_aux["cantidad"], $am_aux["valor_medicamento"], $am_aux["observaciones"], $id_usuario);
	?>
	<input type="hidden" id="hdd_am_resul_<?php echo($i); ?>" value="<?php echo($resul_aux); ?>" />
	<?php
					}
				}
			}
		} else {
			//Hubo un problema al recibir todos los datos
	?>
    <input type="hidden" id="hdd_am_ok_carga" value="-1" />
    <?php
		}
		break;
		
	case "10": //Guardar RIPS de otros servicios
		$id_usuario = $_SESSION["idUsuario"];
		
		@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
		@$id_plan = $utilidades->str_decode($_POST["id_plan"]);
		@$fecha_inicial = $utilidades->str_decode($_POST["fecha_inicial"]);
		@$fecha_final = $utilidades->str_decode($_POST["fecha_final"]);
		@$id_prestador = $utilidades->str_decode($_POST["id_prestador"]);
		@$cant_registros_at = intval($_POST["cant_registros_at"], 10);
		
		$arr_id_at = array();
		$arr_rips_at = array();
		for ($i = 0; $i < $cant_registros_at; $i++) {
			@$arr_id_at[$i] = intval($_POST["id_rips_at_".$i], 10);
			@$arr_rips_at[$i]["id_rips_at"] = intval($_POST["id_rips_at_".$i], 10);
			@$arr_rips_at[$i]["id_admision"] = $utilidades->str_decode($_POST["id_admision_at_".$i]);
			@$arr_rips_at[$i]["id_detalle_precio"] = $utilidades->str_decode($_POST["id_detalle_precio_at_".$i]);
			@$arr_rips_at[$i]["id_paciente"] = $utilidades->str_decode($_POST["id_paciente_at_".$i]);
			@$arr_rips_at[$i]["ind_revisado"] = $utilidades->str_decode($_POST["ind_revisado_at_".$i]);
			@$arr_rips_at[$i]["ind_borrado"] = $utilidades->str_decode($_POST["ind_borrado_at_".$i]);
			@$arr_rips_at[$i]["num_factura"] = $utilidades->str_decode($_POST["num_factura_at_".$i]);
			@$arr_rips_at[$i]["id_convenio"] = $utilidades->str_decode($_POST["id_convenio_at_".$i]);
			@$arr_rips_at[$i]["id_plan"] = $utilidades->str_decode($_POST["id_plan_at_".$i]);
			@$arr_rips_at[$i]["tipo_documento"] = $utilidades->str_decode($_POST["tipo_documento_at_".$i]);
			@$arr_rips_at[$i]["numero_documento"] = $utilidades->str_decode($_POST["numero_documento_at_".$i]);
			@$arr_rips_at[$i]["fecha_insumo"] = $utilidades->str_decode($_POST["fecha_insumo_at_".$i]);
			@$arr_rips_at[$i]["num_autorizacion"] = $utilidades->str_decode($_POST["num_autorizacion_at_".$i]);
			@$arr_rips_at[$i]["tipo_insumo"] = $utilidades->str_decode($_POST["tipo_insumo_at_".$i]);
			@$arr_rips_at[$i]["cod_insumo"] = $utilidades->str_decode($_POST["cod_insumo_at_".$i]);
			@$arr_rips_at[$i]["nombre_insumo"] = $utilidades->str_decode(str_replace(",", "", $_POST["nombre_insumo_at_".$i]));
			@$arr_rips_at[$i]["cantidad"] = intval($_POST["cantidad_at_".$i], 10);
			@$arr_rips_at[$i]["valor_insumo"] = floatval("0".$_POST["valor_insumo_at_".$i]);
			@$arr_rips_at[$i]["observaciones"] = trim($utilidades->str_decode($_POST["observaciones_at_".$i]));
		}
		
		if (isset($_POST["cant_registros_at"])) {
			//Se pudieron recibir todos los datos
	?>
    <input type="hidden" id="hdd_at_ok_carga" value="1" />
    <?php
			//Se borran los registros que no se encuentren en el listado de ids
			$resul_aux = $db_rips->borrar_rips_otros_servicios($id_convenio, $id_plan, $fecha_inicial, $fecha_final, $arr_id_at);
	?>
    <input type="hidden" id="hdd_borrar_at_resul" value="<?php echo($resul_aux); ?>" />
    <?php
			if ($resul_aux > 0) {
				//Se obtiene el código del prestador
				$datos_entidad_obj = $db_datos_entidad->getDatosEntidadId($id_prestador);
	?>
    <input type="hidden" id="hdd_cant_registros_at_resul" value="<?php echo($cant_registros_at); ?>" />
    <?php
				if ($cant_registros_at > 0) {
					foreach ($arr_rips_at as $i => $at_aux) {
						$resul_aux = $db_rips->crear_rips_otros_servicios($at_aux["id_rips_at"], $at_aux["id_detalle_precio"], $at_aux["id_admision"], $at_aux["id_paciente"],
									 $at_aux["ind_revisado"], $at_aux["ind_borrado"], $at_aux["num_factura"], $at_aux["id_convenio"], $at_aux["id_plan"], $datos_entidad_obj["cod_prestador"],
									 $at_aux["tipo_documento"], $at_aux["numero_documento"], $at_aux["fecha_insumo"], $at_aux["num_autorizacion"], $at_aux["tipo_insumo"],
									 $at_aux["cod_insumo"], $at_aux["nombre_insumo"], $at_aux["cantidad"], $at_aux["valor_insumo"], $at_aux["observaciones"], $id_usuario);
	?>
    <input type="hidden" id="hdd_at_resul_<?php echo($i); ?>" value="<?php echo($resul_aux); ?>" />
    <?php
					}
				}
			}
		} else {
			//Hubo un problema al recibir todos los datos
	?>
    <input type="hidden" id="hdd_at_ok_carga" value="-1" />
    <?php
		}
		break;
		
	case "11": //Combo de municipios para usuarios
		@$cod_dep = $utilidades->str_decode($_POST["cod_dep"]);
		@$indice = $utilidades->str_decode($_POST["indice"]);
		
		$lista_municipios = $db_dep_muni->getMunicipiosDepartamento($cod_dep);
		
		$combo->getComboDb("cmb_cod_mun_us_".$indice, "", $lista_municipios, "cod_mun, nom_mun", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
		break;
		
	case "12": //Guardar RIPS de usuarios
		$id_usuario = $_SESSION["idUsuario"];
		
		@$id_rips = $utilidades->str_decode($_POST["id_rips"]);
		@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
		@$id_plan = $utilidades->str_decode($_POST["id_plan"]);
		@$fecha_ini = $utilidades->str_decode($_POST["fecha_ini"]);
		@$fecha_fin = $utilidades->str_decode($_POST["fecha_fin"]);
		@$tipo_factura = $utilidades->str_decode($_POST["tipo_factura"]);
		@$num_factura = $utilidades->str_decode($_POST["num_factura"]);
		@$cant_registros_us = intval($_POST["cant_registros_us"], 10);
	
		$arr_id_us = array();
		$arr_rips_us = array();
		for ($i = 0; $i < $cant_registros_us; $i++) {
			@$arr_id_us[$i] = intval($_POST["id_rips_us_".$i], 10);
			@$arr_rips_us[$i]["id_rips_us"] = $utilidades->str_decode($_POST["id_rips_us_".$i]);
			@$arr_rips_us[$i]["id_paciente"] = $utilidades->str_decode($_POST["id_paciente_us_".$i]);
			@$arr_rips_us[$i]["ind_revisado"] = $utilidades->str_decode($_POST["ind_revisado_us_".$i]);
			@$arr_rips_us[$i]["ind_borrado"] = $utilidades->str_decode($_POST["ind_borrado_us_".$i]);
			@$arr_rips_us[$i]["tipo_documento"] = $utilidades->str_decode($_POST["tipo_documento_us_".$i]);
			@$arr_rips_us[$i]["numero_documento"] = $utilidades->str_decode($_POST["numero_documento_us_".$i]);
			@$arr_rips_us[$i]["nombre_1"] = $utilidades->str_decode(str_replace(",", "", $_POST["nombre_1_us_".$i]));
			@$arr_rips_us[$i]["nombre_2"] = $utilidades->str_decode(str_replace(",", "", $_POST["nombre_2_us_".$i]));
			@$arr_rips_us[$i]["apellido_1"] = $utilidades->str_decode(str_replace(",", "", $_POST["apellido_1_us_".$i]));
			@$arr_rips_us[$i]["apellido_2"] = $utilidades->str_decode(str_replace(",", "", $_POST["apellido_2_us_".$i]));
			@$arr_rips_us[$i]["tipo_usuario"] = $utilidades->str_decode($_POST["tipo_usuario_us_".$i]);
			@$arr_rips_us[$i]["edad"] = $utilidades->str_decode($_POST["edad_us_".$i]);
			@$arr_rips_us[$i]["unidad_edad"] = $utilidades->str_decode($_POST["unidad_edad_us_".$i]);
			@$arr_rips_us[$i]["sexo"] = $utilidades->str_decode($_POST["sexo_us_".$i]);
			@$arr_rips_us[$i]["cod_dep"] = $utilidades->str_decode($_POST["cod_dep_us_".$i]);
			@$arr_rips_us[$i]["cod_mun"] = $utilidades->str_decode($_POST["cod_mun_us_".$i]);
			@$arr_rips_us[$i]["zona"] = $utilidades->str_decode($_POST["zona_us_".$i]);

			@$arr_rips_us[$i]["fecha_nacimiento"] = $utilidades->str_decode($_POST["fecha_nacimiento_us_".$i]);
			@$arr_rips_us[$i]["cod_pais"] = $utilidades->str_decode($_POST["cod_pais_us_".$i]);

			@$arr_rips_us[$i]["observaciones"] = trim($utilidades->str_decode($_POST["observaciones_us_".$i]));
		}
		
		if (isset($_POST["cant_registros_us"])) {
			//Se pudieron recibir todos los datos
	?>
    <input type="hidden" id="hdd_us_ok_carga" value="1" />
    <?php
			//Se crea o actualiza el registro de RIPS general
			$id_rips_aux = $db_rips->crear_editar_rips($id_rips, $id_convenio, $id_plan, $fecha_ini, $fecha_fin, "", $tipo_factura, $num_factura, $id_usuario);
	?>
    <input type="hidden" id="hdd_id_rips_resul_us" value="<?php echo($id_rips_aux); ?>" />
    <?php
			if ($id_rips_aux > 0) {
				//Se obtiene el código de la entidad administradora
				$convenio_obj = $db_convenios->getConvenio($id_convenio);
				
				//Se borran los registros de usuarios
				$db_rips->borrar_rips_usuarios($id_rips, $arr_id_us);
	?>
    <input type="hidden" id="hdd_cant_registros_us_resul" value="<?php echo($cant_registros_us); ?>" />
    <?php
				
				if ($cant_registros_us > 0) {
					foreach ($arr_rips_us as $i => $us_aux) {
						$resul_aux = $db_rips->crear_rips_usuarios($us_aux["id_rips_us"], $id_rips_aux, $us_aux["id_paciente"], $us_aux["ind_revisado"], $us_aux["ind_borrado"],
									 $us_aux["tipo_documento"], $us_aux["numero_documento"], $us_aux["nombre_1"], $us_aux["nombre_2"], $us_aux["apellido_1"],
									 $us_aux["apellido_2"], $convenio_obj["cod_administradora"], $us_aux["tipo_usuario"], $us_aux["edad"], $us_aux["unidad_edad"],
									 $us_aux["sexo"], $us_aux["cod_dep"], $us_aux["cod_mun"], $us_aux["zona"], $us_aux["fecha_nacimiento"], $us_aux["cod_pais"], $us_aux["observaciones"], $id_usuario);
	?>
    <input type="hidden" id="hdd_us_resul_<?php echo($i); ?>" value="<?php echo($resul_aux); ?>" />
    <?php
					}
				}
			}
		} else {
			//Hubo un problema al recibir todos los datos
	?>
    <input type="hidden" id="hdd_us_ok_carga" value="-1" />
    <?php
		}
		break;
		
	case "13": //Guardar RIPS de facturas
		$id_usuario = $_SESSION["idUsuario"];
		
		@$id_rips = $utilidades->str_decode($_POST["id_rips"]);
		@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
		@$id_plan = $utilidades->str_decode($_POST["id_plan"]);
		@$fecha_ini = $utilidades->str_decode($_POST["fecha_ini"]);
		@$fecha_fin = $utilidades->str_decode($_POST["fecha_fin"]);
		@$tipo_factura = $utilidades->str_decode($_POST["tipo_factura"]);
		@$num_factura = $utilidades->str_decode($_POST["num_factura"]);
		@$id_prestador = $utilidades->str_decode($_POST["id_prestador"]);
		@$cant_registros_af = intval($_POST["cant_registros_af"], 10);
		
		$arr_rips_af = array();
		for ($i = 0; $i < $cant_registros_af; $i++) {
			@$arr_rips_af[$i]["num_factura"] = $utilidades->str_decode($_POST["num_factura_af_".$i]);
			@$arr_rips_af[$i]["fecha_factura"] = $utilidades->str_decode($_POST["fecha_factura_af_".$i]);
			@$arr_rips_af[$i]["fecha_ini"] = $utilidades->str_decode($_POST["fecha_ini_af_".$i]);
			@$arr_rips_af[$i]["fecha_fin"] = $utilidades->str_decode($_POST["fecha_fin_af_".$i]);
			@$arr_rips_af[$i]["num_contrato"] = $utilidades->str_decode(str_replace(",", "", $_POST["num_contrato_af_".$i]));
			@$arr_rips_af[$i]["plan_benef"] = $utilidades->str_decode(str_replace(",", "", $_POST["plan_benef_af_".$i]));
			@$arr_rips_af[$i]["num_poliza"] = $utilidades->str_decode(str_replace(",", "", $_POST["num_poliza_af_".$i]));
			@$arr_rips_af[$i]["valor_copago"] = floatval("0".$_POST["valor_copago_af_".$i]);
			@$arr_rips_af[$i]["valor_comision"] = floatval("0".$_POST["valor_comision_af_".$i]);
			@$arr_rips_af[$i]["valor_descuento"] = floatval("0".$_POST["valor_descuento_af_".$i]);
			@$arr_rips_af[$i]["valor_neto"] = floatval("0".$_POST["valor_neto_af_".$i]);
		}
		
		if (isset($_POST["cant_registros_af"])) {
			//Se pudieron recibir todos los datos
	?>
    <input type="hidden" id="hdd_af_ok_carga" value="1" />
    <?php
			//Se crea o actualiza el registro de RIPS general
			$id_rips_aux = $db_rips->crear_editar_rips($id_rips, $id_convenio, $id_plan, $fecha_ini, $fecha_fin, "", $tipo_factura, $num_factura, $id_usuario);
	?>
    <input type="hidden" id="hdd_id_rips_resul_af" value="<?php echo($id_rips_aux); ?>" />
    <?php
			if ($id_rips_aux > 0) {
				//Se obtienen los datos de la entidad
				$datos_entidad_obj = $db_datos_entidad->getDatosEntidadId($id_prestador);
				
				//Se obtiene el código de la entidad administradora
				$convenio_obj = $db_convenios->getConvenio($id_convenio);
				
				$cod_prestador = $datos_entidad_obj["cod_prestador"];
				$nombre_prestador = substr(str_replace(",", "", $datos_entidad_obj["nombre_prestador"]), 0, 60);
				$tipo_documento = $datos_entidad_obj["cod_tipo_documento"];
				$numero_documento = $datos_entidad_obj["numero_documento"];
				$cod_administradora = substr(str_replace(",", "", $convenio_obj["cod_administradora"]), 0, 30);
				$nombre_administradora = $convenio_obj["nombre_convenio"];
				
				//Se borran los registros de facturas
				$db_rips->borrar_rips_facturas($id_rips, $id_usuario);
	?>
    <input type="hidden" id="hdd_cant_registros_af_resul" value="<?php echo($cant_registros_af); ?>" />
    <?php
				if ($cant_registros_af > 0) {
					foreach ($arr_rips_af as $i => $af_aux) {
						$resul_aux = $db_rips->crear_rips_facturas($id_rips_aux, $cod_prestador, $nombre_prestador, $tipo_documento, $numero_documento,
									 $af_aux["num_factura"], $af_aux["fecha_factura"], $af_aux["fecha_ini"], $af_aux["fecha_fin"], $cod_administradora,
									 $nombre_administradora, $af_aux["num_contrato"], $af_aux["plan_benef"], $af_aux["num_poliza"], $af_aux["valor_copago"],
									 $af_aux["valor_comision"], $af_aux["valor_descuento"], $af_aux["valor_neto"], $id_usuario);
	?>
    <input type="hidden" id="hdd_af_resul_<?php echo($i); ?>" value="<?php echo($resul_aux); ?>" />
    <?php
					}
				}
			}
		} else {
			//Hubo un problema al recibir todos los datos
	?>
    <input type="hidden" id="hdd_af_ok_carga" value="-1" />
    <?php
		}
		break;
		
	case "14": //Guardar RIPS de descripción de facturas
		$id_usuario = $_SESSION["idUsuario"];
		
		@$id_rips = $utilidades->str_decode($_POST["id_rips"]);
		@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
		@$id_plan = $utilidades->str_decode($_POST["id_plan"]);
		@$fecha_ini = $utilidades->str_decode($_POST["fecha_ini"]);
		@$fecha_fin = $utilidades->str_decode($_POST["fecha_fin"]);
		@$tipo_factura = $utilidades->str_decode($_POST["tipo_factura"]);
		@$num_factura = $utilidades->str_decode($_POST["num_factura"]);
		@$id_prestador = $utilidades->str_decode($_POST["id_prestador"]);
		@$cant_registros_ad = intval($_POST["cant_registros_ad"], 10);
		
		$arr_rips_ad = array();
		for ($i = 0; $i < $cant_registros_ad; $i++) {
			@$arr_rips_ad[$i]["num_factura"] = $utilidades->str_decode($_POST["num_factura_ad_".$i]);
			@$arr_rips_ad[$i]["cod_concepto"] = $utilidades->str_decode($_POST["cod_concepto_ad_".$i]);
			@$arr_rips_ad[$i]["cantidad"] = floatval("0".$_POST["cantidad_ad_".$i]);
			@$arr_rips_ad[$i]["valor_unitario"] = floatval("0".$_POST["valor_unitario_ad_".$i]);
		}
		
		if (isset($_POST["cant_registros_ad"])) {
			//Se pudieron recibir todos los datos
	?>
    <input type="hidden" id="hdd_ad_ok_carga" value="1" />
    <?php
			//Se crea o actualiza el registro de RIPS general
			$id_rips_aux = $db_rips->crear_editar_rips($id_rips, $id_convenio, $id_plan, $fecha_ini, $fecha_fin, "", $tipo_factura, $num_factura, $id_usuario);
	?>
    <input type="hidden" id="hdd_id_rips_resul_ad" value="<?php echo($id_rips_aux); ?>" />
    <?php
			if ($id_rips_aux > 0) {
				//Se obtienen los datos de la entidad
				$datos_entidad_obj = $db_datos_entidad->getDatosEntidadId($id_prestador);
				$cod_prestador = $datos_entidad_obj["cod_prestador"];
				
				//Se borran los registros de facturas
				$db_rips->borrar_rips_descripcion($id_rips, $id_usuario);
	?>
    <input type="hidden" id="hdd_cant_registros_ad_resul" value="<?php echo($cant_registros_ad); ?>" />
    <?php
				if ($cant_registros_ad > 0) {
					foreach ($arr_rips_ad as $i => $ad_aux) {
						$resul_aux = $db_rips->crear_rips_descripcion($id_rips_aux, $ad_aux["num_factura"], $cod_prestador,
									 $ad_aux["cod_concepto"], $ad_aux["cantidad"], $ad_aux["valor_unitario"], $id_usuario);
	?>
    <input type="hidden" id="hdd_ad_resul_<?php echo($i); ?>" value="<?php echo($resul_aux); ?>" />
    <?php
					}
				}
			}
		} else {
			//Hubo un problema al recibir todos los datos
	?>
    <input type="hidden" id="hdd_ad_ok_carga" value="-1" />
    <?php
		}
		break;
		
	case "15": //Generación de archivos planos del RIPS
		$id_usuario = $_SESSION["idUsuario"];

		@$id_rips = $utilidades->str_decode($_POST["id_rips"]);
		@$ind_ac = intval($_POST["ind_ac"], 10);
		@$ind_ap = intval($_POST["ind_ap"], 10);
		@$ind_am = intval($_POST["ind_am"], 10);
		@$ind_at = intval($_POST["ind_at"], 10);
		@$ind_us = intval($_POST["ind_us"], 10);
		@$ind_af = intval($_POST["ind_af"], 10);
		@$ind_ad = intval($_POST["ind_ad"], 10);
		@$ind_ct = intval($_POST["ind_ct"], 10);
		@$id_prestador = $utilidades->str_decode($_POST["id_prestador"]);
		@$num_factura_in = $utilidades->str_decode($_POST["num_factura"]);
         
		if ($id_rips == "0") {
			//Se trata de una carga directa, se deben buscar los datos del RIPS previos
			@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
			@$id_plan = 	$utilidades->str_decode($_POST["id_plan"]);
			@$fecha_ini = 	$utilidades->str_decode($_POST["fecha_ini"]);
			@$fecha_fin = 	$utilidades->str_decode($_POST["fecha_fin"]);
			
			/*Se verifica si el registro de RIPS existe*/
			$rips_obj = $db_rips->get_rips($id_convenio, $id_plan, $fecha_ini, $fecha_fin);
			
			if (isset($rips_obj["id_rips"])) {
				$id_rips = $rips_obj["id_rips"];
				//var_dump("IF ".$id_rips);
			} else {
				$id_rips = $db_rips->crear_editar_rips($id_rips, $id_convenio, $id_plan, $fecha_ini, $fecha_fin, "", "2", "", $id_usuario);
			 	 //var_dump("ELSE ".$id_rips);
			}
			//Se cargan los registros de usuarios para el nuevo registro de RIPS
			 	//var_dump("id_rips ".$id_rips."id_prestador ".$id_prestador."id_usuario ".$id_usuario);
			$resul_aux = $db_rips->crear_rips_directo($id_rips, $id_prestador, $id_usuario, $num_factura_in);
				//var_dump( "RESULTADO RIP ".$resul_aux);
	?>
 		   <input type="hidden" id="hdd_result_act_directo" value="<?php echo($resul_aux); ?>" />
    <?php
		}
		
		$rips_obj = $db_rips->get_rips_id($id_rips);
		if (isset($rips_obj["id_rips"])) {
			$id_convenio = $rips_obj["id_convenio"];
			$id_plan = intval($rips_obj["id_plan"], 10);
			$fecha_ini = $rips_obj["fecha_ini_t"];
			$fecha_fin = $rips_obj["fecha_fin_t"];
			//Se obtienen los datos de la entidad
			$datos_entidad_obj = $db_datos_entidad->getDatosEntidadId($id_prestador);
			//Se obtiene el consecutivo del RIPS a generar
			$num_consecutivo = trim($rips_obj["num_consecutivo"]);
			if ($num_consecutivo == "") {
				//El consecutivo no ha sido asignado, se busca
				$num_consecutivo = $db_rips->obtener_consecutivo();
			}
			
			//Se borran los RIPS anteriores del mismo convenio/plan
			$dir_rips = "./rips/";
			$arr_archivos = scandir($dir_rips);
			if (count($arr_archivos) > 0) {
				foreach ($arr_archivos as $arch_aux) {
					$pos_aux = strpos(" ".$arch_aux, "rips_".$id_convenio."_".$id_plan."_");
					
					if ($pos_aux == 1) {
						unlink($dir_rips.$arch_aux);
					}
				}
			}
			
			//Se crea el directorio que contendrá los archivos
			$dir_rips = "./rips/".$id_usuario."/";
			@mkdir($dir_rips);
			
			//Se limpia el contenido del directorio
			$arr_archivos = scandir($dir_rips);
			if (count($arr_archivos) > 0) {
				foreach ($arr_archivos as $arch_aux) {
					if ($arch_aux != "." && $arch_aux != "..") {
						@unlink($dir_rips.$arch_aux);
					}
				}
			}
			$arr_ct = array();
			if ($ind_ac == 1) {
				$cant_aux = generar_arch_ac($id_convenio, $id_plan, $fecha_ini, $fecha_fin, $num_consecutivo, $id_usuario);
				$arr_ct["AC"] = $cant_aux;
			}
			if ($ind_ap == 1) {
				$cant_aux = generar_arch_ap($id_convenio, $id_plan, $fecha_ini, $fecha_fin, $num_consecutivo, $id_usuario);
				$arr_ct["AP"] = $cant_aux;
			}
			if ($ind_am == 1) {
				$cant_aux = generar_arch_am($id_convenio, $id_plan, $fecha_ini, $fecha_fin, $num_consecutivo, $id_usuario);
				$arr_ct["AM"] = $cant_aux;
			}
			if ($ind_at == 1) {
				$cant_aux = generar_arch_at($id_convenio, $id_plan, $fecha_ini, $fecha_fin, $num_consecutivo, $id_usuario);
				$arr_ct["AT"] = $cant_aux;
			}
			if ($ind_us == 1) {
				$cant_aux = generar_arch_us($id_rips, $num_consecutivo, $id_usuario);
				$arr_ct["US"] = $cant_aux;
			}
			if ($ind_af == 1) {
				$cant_aux = generar_arch_af($id_rips, $num_consecutivo, $id_usuario);
				$arr_ct["AF"] = $cant_aux;
			}
			if ($ind_ad == 1) {
				$cant_aux = generar_arch_ad($id_rips, $num_consecutivo, $id_usuario);
				$arr_ct["AD"] = $cant_aux;
			}
			if ($ind_ct == 1) {
				generar_arch_ct($id_rips, $num_consecutivo, $arr_ct, $datos_entidad_obj["cod_prestador"], $rips_obj["fecha_fin_t"], $id_usuario);
			}
			
			if (count($arr_ct) > 0) {
				//Se obtiene el listado de los archivos existentes
				$arr_archivos = scandir($dir_rips);
				
				//Se construye el array de los archivos creados
				$arr_arch2 = array();
				if (count($arr_archivos) > 0) {
					foreach ($arr_archivos as $reg_aux) {
						if ($reg_aux != "." && $reg_aux != "..") {
							array_push($arr_arch2, $dir_rips.$reg_aux);
						}
					}
				}
				//Se construye el nombre del archivo zip
				$arr_fini = explode("/", $fecha_ini);
				$arr_ffin = explode("/", $fecha_fin);
				$nombre_zip = "./rips/rips_".$id_convenio."_".$id_plan."_".$arr_fini[2]."-".$arr_fini[1]."-".$arr_fini[0]."_".$arr_ffin[2]."-".$arr_ffin[1]."-".$arr_ffin[0].".zip";
				
				crear_zip($arr_arch2, $nombre_zip);
				
				//Se limpia el contenido del directorio
				if (count($arr_archivos) > 0) {
					foreach ($arr_archivos as $reg_aux) {
						if ($reg_aux != "." && $reg_aux != "..") {
							@unlink($dir_rips.$reg_aux);
						}
					}
				}
			}
		}
		break;
		
	case "16": //Registros de consultas
		@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
		@$id_plan = $utilidades->str_decode($_POST["id_plan"]);
		@$fecha_inicial = $utilidades->str_decode($_POST["fecha_inicial"]);
		@$fecha_final = $utilidades->str_decode($_POST["fecha_final"]);
		@$tipo_factura = $utilidades->str_decode($_POST["tipo_factura"]);
		@$num_factura = $utilidades->str_decode($_POST["num_factura"]);
		@$ind_rips_existentes = intval($utilidades->str_decode($_POST["ind_rips_existentes"]), 10);
		@$ind_sin_np = intval($utilidades->str_decode($_POST["ind_sin_np"]), 10);
		
		//Se obtienen los datos de los RIPS de consultas
		$lista_rips_ac = $db_rips->get_lista_registros_ac($id_convenio, $id_plan, $fecha_inicial, $fecha_final, $ind_rips_existentes, $ind_sin_np);
		
	?>
                <div id="d_tbl_encabezado_ac" style="overflow:hidden; width:100%;">
                    <table class="modal_table" style="width:4950px; margin:auto;" >
                        <thead>
                            <tr>
                                <th class="th_reducido" align="center" style="width:50px;">#</th>
                                <th class="th_reducido" align="center" style="width:100px;">Revisado</th>
                                <th class="th_reducido" align="center" style="width:100px;">Excluido</th>
                                <th class="th_reducido" align="center" style="width:250px;">Tipo documento</th>
                                <th class="th_reducido" align="center" style="width:100px;">No. documento</th>
                                <th class="th_reducido" align="center" style="width:250px;">Nombre completo</th>
                                <th class="th_reducido" align="center" style="width:100px;">No. Factura</th>
                                <th class="th_reducido" align="center" style="width:250px;">Convenio</th>
                                <th class="th_reducido" align="center" style="width:250px;">Plan</th>
                                <th class="th_reducido" align="center" style="width:100px;">Fecha consulta</th>
                                <th class="th_reducido" align="center" style="width:100px;">No. Autorizaci&oacute;n</th>
                                <th class="th_reducido" align="center" style="width:350px;" colspan="2">CUPS consulta</th>
                                <th class="th_reducido" align="center" style="width:350px;">Finalidad</th>
                                <th class="th_reducido" align="center" style="width:250px;">Causa externa</th>
                                <th class="th_reducido" align="center" style="width:350px;" colspan="2">Diagn&oacute;stico principal</th>
                                <th class="th_reducido" align="center" style="width:350px;" colspan="2">Diagn&oacute;stico relacionado no. 1</th>
                                <th class="th_reducido" align="center" style="width:350px;" colspan="2">Diagn&oacute;stico relacionado no. 2</th>
                                <th class="th_reducido" align="center" style="width:350px;" colspan="2">Diagn&oacute;stico relacionado no. 3</th>
                                <th class="th_reducido" align="center" style="width:250px;">Tipo diagn&oacute;stico principal</th>
                                <th class="th_reducido" align="center" style="width:100px;">Valor consulta</th>
                                <th class="th_reducido" align="center" style="width:100px;">Valor cuota moderadora</th>
                                <th class="th_reducido" align="center" style="width:100px;">Valor neto a pagar</th>
                                <th class="th_reducido" align="center" style="width:400px;">Observaciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div id="d_tbl_cuerpo_ac" style="overflow:auto; width:100%; height:420px;" onscroll="desplazar_tabla_horizontal('ac');">
                    <table class="modal_table" style="width:4950px; margin:auto;" >
                        <?php
							//Se obtiene el listado de convenios
							$lista_convenios = $db_convenios->getListaConveniosActivos();
							
							//Se obtiene el listado de planes
							$lista_planes = $db_planes->getListaPlanesActivos($id_convenio);
							
							//listado de tipos de documento
							$lista_tipos_documento = $db_listas->getListaDetalles(2);
							
							//listado de tipos de finalidades
							$lista_finalidades = $db_listas->getListaDetalles(32);
							
							//listado de tipos de causas externas
							$lista_causas_externas = $db_listas->getListaDetalles(33);
							
							//listado de tipos de diagnóstico principal
							$lista_tipos_diag_prin = $db_listas->getListaDetalles(34);
							
							$fin_consulta_obj = $db_variables->getVariable(15);
							
							$cont_fila = 0;
                        	if (count($lista_rips_ac) > 0) {
								//Se obtienen los diagnósticos
								$lista_diag_ac = $db_rips->get_lista_diagnosticos($id_convenio, $id_plan, $fecha_inicial, $fecha_final, "C");
								
								//var_dump($lista_diag_ac);
								//Se crea el mapa de diagnósticos
								$mapa_diag_ac = array();
								if (count($lista_diag_ac) > 0) {
									foreach ($lista_diag_ac as $diag_aux) {
										if (!isset($mapa_diag_ac[$diag_aux["id_hc"]])) {
											$mapa_diag_ac[$diag_aux["id_hc"]] = array();
										}
										array_push($mapa_diag_ac[$diag_aux["id_hc"]], $diag_aux);
									}
								}
								
								$id_detalle_precio_ant = -1;
								foreach ($lista_rips_ac as $rips_ac_aux) {
									$id_detalle_precio_aux = intval($rips_ac_aux["id_detalle_precio"], 0);
									if ($id_detalle_precio_aux != $id_detalle_precio_ant || $id_detalle_precio_aux == "") {
										for ($x = 0; $x < intval($rips_ac_aux["cantidad"], 10); $x++) {
						?>
                        <tr id="tr_reg_ac_<?php echo($cont_fila); ?>">
                            <td align="center" style="width:50px;">
                            	<input type="hidden" id="hdd_id_rips_ac_<?php echo($cont_fila); ?>" value="<?php echo($rips_ac_aux["id_rips_ac"]); ?>" />
                            	<input type="hidden" id="hdd_id_admision_ac_<?php echo($cont_fila); ?>" value="<?php echo($rips_ac_aux["id_admision"]); ?>" />
                            	<input type="hidden" id="hdd_id_detalle_precio_ac_<?php echo($cont_fila); ?>" value="<?php echo($rips_ac_aux["id_detalle_precio"]); ?>" />
                            	<input type="hidden" id="hdd_id_paciente_ac_<?php echo($cont_fila); ?>" value="<?php echo($rips_ac_aux["id_paciente"]); ?>" />
                            	<input type="hidden" id="hdd_id_hc_ac_<?php echo($cont_fila); ?>" value="<?php echo($rips_ac_aux["id_hc"]); ?>" />
                                <input type="hidden" id="hdd_con_datos_ac_<?php echo($cont_fila); ?>" value="1" />
								<?php echo($cont_fila + 1); ?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="checkbox" id="chk_revisado_ac_<?php echo($cont_fila); ?>" style="margin:auto;"<?php if ($rips_ac_aux["ind_revisado"] == "1") { ?> checked="checked"<?php } ?> />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="checkbox" id="chk_borrado_ac_<?php echo($cont_fila); ?>" style="margin:auto;"<?php if ($rips_ac_aux["ind_borrado"] == "1") { ?> checked="checked"<?php } ?> />
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$combo->getComboDb("cmb_tipo_documento_ac_".$cont_fila, $rips_ac_aux["tipo_documento"], $lista_tipos_documento, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_numero_documento_ac_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_ac_aux["numero_documento"]); ?>" onkeypress="return solo_alfanumericos(event);" />
                            </td>
                            <td align="left" class="td_reducido" id="td_nombre_completo_ac_<?php echo($cont_fila); ?>" style="width:250px;">
								<?php echo($funciones_persona->obtenerNombreCompleto($rips_ac_aux["nombre_1"], $rips_ac_aux["nombre_2"], $rips_ac_aux["apellido_1"], $rips_ac_aux["apellido_2"])); ?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<?php
                                	$num_factura_aux = $rips_ac_aux["num_factura"];
									if ($tipo_factura == "1") {
										$num_factura_aux = $num_factura;
									}
								?>
                            	<input type="text" id="txt_num_factura_ac_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="<?php echo($num_factura_aux); ?>" <?php if ($tipo_factura != "2") { ?> readonly="readonly"<?php } ?> />
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$combo->getComboDb("cmb_convenio_ac_".$cont_fila, $rips_ac_aux["id_convenio"], $lista_convenios, "id_convenio, nombre_convenio", "&nbsp;", "seleccionar_convenio(this.value, 'ac', '".$cont_fila."');", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:250px;">
                            	<div id="d_planes_ac_<?php echo($cont_fila); ?>">
									<?php
                                        $combo->getComboDb("cmb_plan_ac_".$cont_fila, $rips_ac_aux["id_plan"], $lista_planes, "id_plan, nombre_plan", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
                                    ?>
                                </div>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_fecha_consulta_ac_<?php echo($cont_fila); ?>" class="input_sin_margen" maxlength="10" style="width:100%;" value="<?php echo($rips_ac_aux["fecha_consulta_t"]); ?>" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
								<script type="text/javascript" id="ajax">
									
									$('#txt_fecha_consulta_ac_<?php echo($cont_fila); ?>').fdatepicker({
										format: 'dd/mm/yyyy'
									});

                                </script>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_num_autorizacion_ac_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="<?php echo(substr($rips_ac_aux["num_autorizacion"], 0, 15)); ?>" />
                            </td>
                            <td align="center" style="width:65px;">
                            	<input type="text" id="txt_cod_procedimiento_ac_<?php echo($cont_fila); ?>" maxlength="6" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_ac_aux["cod_procedimiento"]); ?>" onkeypress="return solo_numeros(event, false);" onblur="cargar_nombre_procedimiento(this.value, 'td_nombre_procedimiento_ac_<?php echo($cont_fila); ?>');" />
                            </td>
                            <td align="left" style="width:285px;" class="td_reducido" id="td_nombre_procedimiento_ac_<?php echo($cont_fila); ?>">
                            	<?php echo($rips_ac_aux["nombre_procedimiento"]); ?>
                            </td>
                            <td align="center" style="width:350px;">
                            	<?php
									$fin_consulta_aux = $rips_ac_aux["fin_consulta"];
									if ($fin_consulta_aux == "") {
										if (isset($fin_consulta_obj["valor_variable"]) && $fin_consulta_obj["valor_variable"] != "") {
											$fin_consulta_aux = $fin_consulta_obj["valor_variable"];
										} elseif (isset($mapa_diag_ac[$rips_ac_aux["id_hc"]][0]["cod_ciex_ori"]) && strtoupper(substr($mapa_diag_ac[$rips_ac_aux["id_hc"]][0]["cod_ciex_ori"], 0, 1)) === "Z") {
											$fin_consulta_aux = "08";
										} else {
											$fin_consulta_aux = "10";
										} 
									}
									
                                	$combo->getComboDb("cmb_fin_consulta_ac_".$cont_fila, $fin_consulta_aux, $lista_finalidades, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                              
                            <td align="center" style="width:250px;">
                            	<?php
								
                                	$combo->getComboDb("cmb_causa_ext_ac_".$cont_fila, ($rips_ac_aux["causa_ext"] != "" ? $rips_ac_aux["causa_ext"] : "13"), $lista_causas_externas, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <?php
                            	if (intval($rips_ac_aux["id_rips_ac"]) > 0) {
							?>
                            <td align="center" style="width:60px;">
                            	<input type="text" id="txt_cod_ciex_ac_<?php echo($cont_fila."_0"); ?>" maxlength="4" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_ac_aux["cod_ciex_prin"]); ?>" onkeypress="return solo_alfanumericos(event);" onblur="convertir_a_mayusculas(this); cargar_nombre_ciex(this.value, 'td_nombre_ciex_<?php echo($cont_fila."_0"); ?>');" />
                            </td>
                            <td align="left" style="width:290px;" class="td_reducido" id="td_nombre_ciex_<?php echo($cont_fila."_0"); ?>">
                            	<?php echo($rips_ac_aux["nom_ciex_prin"]); ?>
                            </td>
                            <td align="center" style="width:60px;">
                            	<input type="text" id="txt_cod_ciex_ac_<?php echo($cont_fila."_1"); ?>" maxlength="4" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_ac_aux["cod_ciex_rel1"]); ?>" onkeypress="return solo_alfanumericos(event);" onblur="convertir_a_mayusculas(this); cargar_nombre_ciex(this.value, 'td_nombre_ciex_<?php echo($cont_fila."_1"); ?>');" />
                            </td>
                            <td align="left" style="width:290px;" class="td_reducido" id="td_nombre_ciex_<?php echo($cont_fila."_1"); ?>">
                            	<?php echo($rips_ac_aux["nom_ciex_rel1"]); ?>
                            </td>
                            <td align="center" style="width:60px;">
                            	<input type="text" id="txt_cod_ciex_ac_<?php echo($cont_fila."_2"); ?>" maxlength="4" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_ac_aux["cod_ciex_rel2"]); ?>" onkeypress="return solo_alfanumericos(event);" onblur="convertir_a_mayusculas(this); cargar_nombre_ciex(this.value, 'td_nombre_ciex_<?php echo($cont_fila."_2"); ?>');" />
                            </td>
                            <td align="left" style="width:290px;" class="td_reducido" id="td_nombre_ciex_<?php echo($cont_fila."_2"); ?>">
                            	<?php echo($rips_ac_aux["nom_ciex_rel2"]); ?>
                            </td>
                            <td align="center" style="width:60px;">
                            	<input type="text" id="txt_cod_ciex_ac_<?php echo($cont_fila."_3"); ?>" maxlength="4" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_ac_aux["cod_ciex_rel3"]); ?>" onkeypress="return solo_alfanumericos(event);" onblur="convertir_a_mayusculas(this); cargar_nombre_ciex(this.value, 'td_nombre_ciex_<?php echo($cont_fila."_3"); ?>');" />
                            </td>
                            <td align="left" style="width:290px;" class="td_reducido" id="td_nombre_ciex_<?php echo($cont_fila."_3"); ?>">
                            	<?php echo($rips_ac_aux["nom_ciex_rel3"]); ?>
                            </td>
                            <?php
								} else {
									for ($j = 0; $j < 4; $j++) {
										$cod_ciex_aux = "";
										$nom_ciex_aux = "";
										if (isset($mapa_diag_ac[$rips_ac_aux["id_hc"]][$j])) {
											$cod_ciex_aux = $mapa_diag_ac[$rips_ac_aux["id_hc"]][$j]["cod_ciex_ori"];
											$nom_ciex_aux = $mapa_diag_ac[$rips_ac_aux["id_hc"]][$j]["nom_ciex"];
										}
							?>
                            <td align="center" style="width:60px;">
                            	<input type="text" id="txt_cod_ciex_ac_<?php echo($cont_fila."_".$j); ?>" maxlength="4" class="input_sin_margen" style="width:100%;" value="<?php echo($cod_ciex_aux); ?>" onkeypress="return solo_alfanumericos(event);" onblur="convertir_a_mayusculas(this); cargar_nombre_ciex(this.value, 'td_nombre_ciex_<?php echo($cont_fila."_".$j); ?>');" />
                            </td>
                            <td align="left" style="width:290px;" class="td_reducido" id="td_nombre_ciex_<?php echo($cont_fila."_".$j); ?>">
                            	<?php echo($nom_ciex_aux); ?>
                            </td>
                            <?php
									}
								}
								
								$tipo_diag_prin = "2";
								if (intval($rips_ac_aux["id_rips_ac"]) > 0) {
									$tipo_diag_prin = $rips_ac_aux["tipo_diag_prin"];
								} else {
									if (isset($mapa_diag_ac[$rips_ac_aux["id_hc"]][0])) {
										$diag_obj_aux = $mapa_diag_ac[$rips_ac_aux["id_hc"]][0];
										
										//Se encuentra la cantidad de diagnósticos anteriores del mismo tipo
										$cant_diag_ant = 0;
										$diag_obj_ant = $db_rips->get_cant_ciex_paciente($rips_ac_aux["id_paciente"], $diag_obj_aux["cod_ciex"], $rips_ac_aux["fecha_consulta_t"]);
										$cant_diag_ant = intval($diag_obj_ant["cantidad"], 10);
										
										if ($cant_diag_ant > 0) {
											$tipo_diag_prin = "3";
										}
									}
								}
							?>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$combo->getComboDb("cmb_tipo_diag_prin_ac_".$cont_fila, $tipo_diag_prin, $lista_tipos_diag_prin, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <?php
								$valor_aux = 0;
								$valor_cuota_aux = 0;
								if (intval($rips_ac_aux["id_rips_ac"]) > 0) {
									$valor_aux = $rips_ac_aux["valor_consulta"];
									$valor_cuota_aux = $rips_ac_aux["valor_cuota"];
								} else {
									$valor_aux = floatval("0".$rips_ac_aux["valor_p"]);
									$valor_cuota_aux = floatval("0".$rips_ac_aux["valor_cuota_p"]);
								}
							?>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_consulta_ac_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="<?php echo($valor_aux); ?>" onkeypress="return solo_numeros(event, true);" onblur="calcular_neto_ac(<?php echo($cont_fila); ?>);" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_cuota_ac_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="<?php echo($valor_cuota_aux); ?>" onkeypress="return solo_numeros(event, true);" onblur="calcular_neto_ac(<?php echo($cont_fila); ?>);" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_neto_ac_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="<?php echo($valor_aux - $valor_cuota_aux); ?>" readonly="readonly" />
                            </td>
                            <td align="center" style="width:400px;">
                            	<input type="text" id="txt_observaciones_ac_<?php echo($cont_fila); ?>" maxlength="200" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_ac_aux["observaciones"]); ?>" />
                            </td>
                        </tr>
                        <?php
											$cont_fila++;
										}
									}
									$id_detalle_precio_ant = $id_detalle_precio_aux;
								}
							}
						?>
                        <tr style="display:none;">
                        	<td>
                            	<input type="hidden" id="hdd_cant_registros_ac" value="<?php echo($cont_fila); ?>" />
                            </td>
                        </tr>
                        <?php
							for ($i = 0; $i < 100; $i++) {
						?>
                        <tr id="tr_reg_ac_<?php echo($cont_fila); ?>" style="display:none;">
                            <td align="center" style="width:50px;">
                            	<input type="hidden" id="hdd_id_rips_ac_<?php echo($cont_fila); ?>" value="0" />
                            	<input type="hidden" id="hdd_id_admision_ac_<?php echo($cont_fila); ?>" value="" />
                            	<input type="hidden" id="hdd_id_detalle_precio_ac_<?php echo($cont_fila); ?>" value="" />
                            	<input type="hidden" id="hdd_id_paciente_ac_<?php echo($cont_fila); ?>" value="" />
                                <input type="hidden" id="hdd_id_hc_ac_<?php echo($cont_fila); ?>" value="" />
                                <input type="hidden" id="hdd_con_datos_ac_<?php echo($cont_fila); ?>" value="0" />
								<?php echo($cont_fila + 1); ?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="checkbox" id="chk_revisado_ac_<?php echo($cont_fila); ?>" style="margin:auto;" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="checkbox" id="chk_borrado_ac_<?php echo($cont_fila); ?>" style="margin:auto;" />
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$combo->getComboDb("cmb_tipo_documento_ac_".$cont_fila, "", $lista_tipos_documento, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_numero_documento_ac_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_alfanumericos(event);" />
                            </td>
                            <td align="left" class="td_reducido" id="td_nombre_completo_ac_<?php echo($cont_fila); ?>" style="width:250px;"></td>
                            <td align="center" style="width:100px;">
                            	<?php
                                	$num_factura_aux = "";
									if ($tipo_factura == "1") {
										$num_factura_aux = $num_factura;
									}
								?>
                            	<input type="text" id="txt_num_factura_ac_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="<?php echo($num_factura_aux); ?>" <?php if ($tipo_factura != "2") { ?> readonly="readonly"<?php } ?> />
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$combo->getComboDb("cmb_convenio_ac_".$cont_fila, $id_convenio, $lista_convenios, "id_convenio, nombre_convenio", "&nbsp;", "seleccionar_convenio(this.value, 'ac', '".$cont_fila."');", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:250px;">
                            	<div id="d_planes_ac_<?php echo($cont_fila); ?>">
									<?php
                                        $combo->getComboDb("cmb_plan_ac_".$cont_fila, $id_plan, $lista_planes, "id_plan, nombre_plan", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
                                    ?>
                                </div>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_fecha_consulta_ac_<?php echo($cont_fila); ?>" class="input_sin_margen" maxlength="10" style="width:100%;" value="" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
								<script type="text/javascript" id="ajax">
									$('#txt_fecha_consulta_ac_<?php echo($cont_fila); ?>').fdatepicker({
										format: 'dd/mm/yyyy'
									});
                                </script>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_num_autorizacion_ac_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="" />
                            </td>
                            <td align="center" style="width:65px;">
                            	<input type="text" id="txt_cod_procedimiento_ac_<?php echo($cont_fila); ?>" maxlength="6" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_numeros(event, false);" onblur="cargar_nombre_procedimiento(this.value, 'td_nombre_procedimiento_ac_<?php echo($cont_fila); ?>');" />
                            </td>
                            <td align="left" style="width:285px;" class="td_reducido" id="td_nombre_procedimiento_ac_<?php echo($cont_fila); ?>"></td>
                            <td align="center" style="width:350px;">
                            	<?php
                                	$combo->getComboDb("cmb_fin_consulta_ac_".$cont_fila, "10", $lista_finalidades, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$combo->getComboDb("cmb_causa_ext_ac_".$cont_fila, "13", $lista_causas_externas, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <?php
								for ($j = 0; $j < 4; $j++) {
							?>
                            <td align="center" style="width:60px;">
                            	<input type="text" id="txt_cod_ciex_ac_<?php echo($cont_fila."_".$j); ?>" maxlength="4" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_alfanumericos(event);" onblur="convertir_a_mayusculas(this); cargar_nombre_ciex(this.value, 'td_nombre_ciex_<?php echo($cont_fila."_".$j); ?>');" />
                            </td>
                            <td align="left" style="width:290px;" class="td_reducido" id="td_nombre_ciex_<?php echo($cont_fila."_".$j); ?>"></td>
                            <?php
								}
							?>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$combo->getComboDb("cmb_tipo_diag_prin_ac_".$cont_fila, "2", $lista_tipos_diag_prin, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_consulta_ac_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_numeros(event, true);" onblur="calcular_neto_ac(<?php echo($cont_fila); ?>);" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_cuota_ac_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_numeros(event, true);" onblur="calcular_neto_ac(<?php echo($cont_fila); ?>);" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_neto_ac_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="" readonly="readonly" />
                            </td>
                            <td align="center" style="width:400px;">
                            	<input type="text" id="txt_observaciones_ac_<?php echo($cont_fila); ?>" maxlength="200" class="input_sin_margen" style="width:100%;" value="" />
                            </td>
                        </tr>
                        <?php
								$cont_fila++;
							}
						?>
                        <tr>
                        	<td align="left">
                            	<div class="Add-icon" onclick="agregar_registro_ac();" title="Agregar registro" style="width:50px;"></div>
                            </td>
                        </tr>
                    </table>
                </div>
                <script id="ajax" type="text/javascript">
					contar_registros("ac");
				</script>
    <?php
		break;
		
	case "17": //Registros de procedimientos
		@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
		@$id_plan = $utilidades->str_decode($_POST["id_plan"]);
		@$fecha_inicial = $utilidades->str_decode($_POST["fecha_inicial"]);
		@$fecha_final = $utilidades->str_decode($_POST["fecha_final"]);
		@$tipo_factura = $utilidades->str_decode($_POST["tipo_factura"]);
		@$num_factura = $utilidades->str_decode($_POST["num_factura"]);
		@$ind_rips_existentes = intval($utilidades->str_decode($_POST["ind_rips_existentes"]), 10);
		@$ind_sin_np = intval($utilidades->str_decode($_POST["ind_sin_np"]), 10);
		
		//Se obtienen los datos de los RIPS de procedimientos
		$lista_rips_ap = $db_rips->get_lista_registros_ap($id_convenio, $id_plan, $fecha_inicial, $fecha_final, $ind_rips_existentes, $ind_sin_np);
	?>
    			<div id="d_tbl_encabezado_ap" style="overflow:hidden; width:100%;">
                    <table class="modal_table" style="width:4650px; margin:auto;" >
                        <thead>
                            <tr>
                                <th class="th_reducido" align="center" style="width:50px;">#</th>
                                <th class="th_reducido" align="center" style="width:100px;">Revisado</th>
                                <th class="th_reducido" align="center" style="width:100px;">Excluido</th>
                                <th class="th_reducido" align="center" style="width:250px;">Tipo documento</th>
                                <th class="th_reducido" align="center" style="width:100px;">No. documento</th>
                                <th class="th_reducido" align="center" style="width:250px;">Nombre completo</th>
                                <th class="th_reducido" align="center" style="width:100px;">No. Factura</th>
                                <th class="th_reducido" align="center" style="width:250px;">Convenio</th>
                                <th class="th_reducido" align="center" style="width:250px;">Plan</th>
                                <th class="th_reducido" align="center" style="width:100px;">Fecha procedimiento</th>
                                <th class="th_reducido" align="center" style="width:100px;">No. Autorizaci&oacute;n</th>
                                <th class="th_reducido" align="center" style="width:350px;" colspan="2">CUPS procedimiento</th>
                                <th class="th_reducido" align="center" style="width:150px;">&Aacute;mbito de realizaci&oacute;n</th>
                                <th class="th_reducido" align="center" style="width:250px;">Finalidad del procedimiento</th>
                                <th class="th_reducido" align="center" style="width:250px;">Personal que atiende</th>
                                <th class="th_reducido" align="center" style="width:350px;" colspan="2">Diagn&oacute;stico principal</th>
                                <th class="th_reducido" align="center" style="width:350px;" colspan="2">Diagn&oacute;stico relacionado</th>
                                <th class="th_reducido" align="center" style="width:350px;" colspan="2">Diagn&oacute;stico de la complicaci&oacute;n</th>
                                <th class="th_reducido" align="center" style="width:350px;">Forma de realizaci&oacute;n del acto quir&uacute;rgico</th>
                                <th class="th_reducido" align="center" style="width:100px;">Valor del procedimiento</th>
                                <th class="th_reducido" align="center" style="width:100px;">Valor copago</th>
                                <th class="th_reducido" align="center" style="width:400px;">Observaciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div id="d_tbl_cuerpo_ap" style="overflow:auto; width:100%; height:420px;" onscroll="desplazar_tabla_horizontal('ap');">
                	<table class="modal_table" style="width:4650px; margin:auto;" >
                        <?php
							//Se obtiene el listado de convenios
							$lista_convenios = $db_convenios->getListaConveniosActivos();
							
							//Se obtiene el listado de planes
							$lista_planes = $db_planes->getListaPlanesActivos($id_convenio);
							
							//listado de tipos de documento
							$lista_tipos_documento = $db_listas->getListaDetalles(2);
							
							//listado de ámbitos de realización
							$lista_amb_rea = $db_listas->getListaDetalles(24);
							
							//listado de finalidades de procedimientos
							$lista_fin_pro = $db_listas->getListaDetalles(25);
							
							//listado de personal que atiende
							$lista_per_ati = $db_listas->getListaDetalles(35);
							
							//listado de formas de realización de actos quirúrgicos
							$lista_for_rea = $db_listas->getListaDetalles(36);
							
							$cont_fila = 0;
                        	if (count($lista_rips_ap) > 0) {
								//Se obtienen los diagnósticos
								$lista_diag_ap = $db_rips->get_lista_diagnosticos($id_convenio, $id_plan, $fecha_inicial, $fecha_final, "P");
								
								//Se crea el mapa de diagnósticos
								$mapa_diag_ap = array();
								if (count($lista_diag_ap) > 0) {
									foreach ($lista_diag_ap as $diag_aux) {
										if (!isset($mapa_diag_ap[$diag_aux["id_hc"]])) {
											$mapa_diag_ap[$diag_aux["id_hc"]] = array();
										}
										array_push($mapa_diag_ap[$diag_aux["id_hc"]], $diag_aux);
									}
								}
								
								//Se obtienen las cirugías
								$lista_cx_ap = $db_rips->get_lista_cx($id_convenio, $id_plan, $fecha_inicial, $fecha_final);
								
								//Se crea el mapa de cirugías
								$mapa_cx_ap = array();
								if (count($lista_cx_ap) > 0) {
									foreach ($lista_cx_ap as $cx_aux) {
										$mapa_cx_ap[$cx_aux["id_hc"]]["cant_cx"] = intval($cx_aux["cant_cx"], 10);
										$mapa_cx_ap[$cx_aux["id_hc"]]["cant_vias"] = intval($cx_aux["cant_vias"], 10);
									}
								}
								
								$id_detalle_precio_ant = -1;
								foreach ($lista_rips_ap as $rips_ap_aux) {
									$id_detalle_precio_aux = intval($rips_ap_aux["id_detalle_precio"], 0);
									if ($id_detalle_precio_aux != $id_detalle_precio_ant || $id_detalle_precio_aux == "") {
										for ($x = 0; $x < intval($rips_ap_aux["cantidad"], 10); $x++) {
						?>
                        <tr id="tr_reg_ap_<?php echo($cont_fila); ?>">
                            <td align="center" style="width:50px;">
                            	<input type="hidden" id="hdd_id_rips_ap_<?php echo($cont_fila); ?>" value="<?php echo($rips_ap_aux["id_rips_ap"]); ?>" />
                            	<input type="hidden" id="hdd_id_admision_ap_<?php echo($cont_fila); ?>" value="<?php echo($rips_ap_aux["id_admision"]); ?>" />
                            	<input type="hidden" id="hdd_id_detalle_precio_ap_<?php echo($cont_fila); ?>" value="<?php echo($rips_ap_aux["id_detalle_precio"]); ?>" />
                            	<input type="hidden" id="hdd_id_paciente_ap_<?php echo($cont_fila); ?>" value="<?php echo($rips_ap_aux["id_paciente"]); ?>" />
                            	<input type="hidden" id="hdd_id_hc_ap_<?php echo($cont_fila); ?>" value="<?php echo($rips_ap_aux["id_hc"]); ?>" />
                                <input type="hidden" id="hdd_con_datos_ap_<?php echo($cont_fila); ?>" value="1" />
								<?php echo($cont_fila + 1); ?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="checkbox" id="chk_revisado_ap_<?php echo($cont_fila); ?>" style="margin:auto;"<?php if ($rips_ap_aux["ind_revisado"] == "1") { ?> checked="checked"<?php } ?> />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="checkbox" id="chk_borrado_ap_<?php echo($cont_fila); ?>" style="margin:auto;"<?php if ($rips_ap_aux["ind_borrado"] == "1") { ?> checked="checked"<?php } ?> />
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$combo->getComboDb("cmb_tipo_documento_ap_".$cont_fila, $rips_ap_aux["tipo_documento"], $lista_tipos_documento, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_numero_documento_ap_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_ap_aux["numero_documento"]); ?>" onkeypress="return solo_alfanumericos(event);" />
                            </td>
                            <td align="left" class="td_reducido" id="td_nombre_completo_ap_<?php echo($cont_fila); ?>" style="width:250px;">
								<?php echo($funciones_persona->obtenerNombreCompleto($rips_ap_aux["nombre_1"], $rips_ap_aux["nombre_2"], $rips_ap_aux["apellido_1"], $rips_ap_aux["apellido_2"])); ?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<?php
                                	$num_factura_aux = $rips_ap_aux["num_factura"];
									if ($tipo_factura == "1") {
										$num_factura_aux = $num_factura;
									}
								?>
                            	<input type="text" id="txt_num_factura_ap_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="<?php echo($num_factura_aux); ?>" <?php if ($tipo_factura != "2") { ?> readonly="readonly"<?php } ?> />
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$combo->getComboDb("cmb_convenio_ap_".$cont_fila, $rips_ap_aux["id_convenio"], $lista_convenios, "id_convenio, nombre_convenio", "&nbsp;", "seleccionar_convenio(this.value, 'ap', '".$cont_fila."');", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:250px;">
                            	<div id="d_planes_ap_<?php echo($cont_fila); ?>">
									<?php
                                        $combo->getComboDb("cmb_plan_ap_".$cont_fila, $rips_ap_aux["id_plan"], $lista_planes, "id_plan, nombre_plan", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
                                    ?>
                                </div>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_fecha_pro_ap_<?php echo($cont_fila); ?>" class="input_sin_margen" maxlength="10" style="width:100%;" value="<?php echo($rips_ap_aux["fecha_pro_t"]); ?>" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
								<script type="text/javascript" id="ajax">
									$('#txt_fecha_pro_ap_<?php echo($cont_fila); ?>').fdatepicker({
										format: 'dd/mm/yyyy'
									});
                                </script>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_num_autorizacion_ap_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="<?php echo(substr($rips_ap_aux["num_autorizacion"], 0, 15)); ?>" />
                            </td>
                            <td align="center" style="width:65px;">
                            	<input type="text" id="txt_cod_procedimiento_ap_<?php echo($cont_fila); ?>" maxlength="6" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_ap_aux["cod_procedimiento"]); ?>" onkeypress="return solo_numeros(event, false);" onblur="cargar_nombre_procedimiento(this.value, 'td_nombre_procedimiento_ap_<?php echo($cont_fila); ?>');" />
                            </td>
                            <td align="left" style="width:285px;" class="td_reducido" id="td_nombre_procedimiento_ap_<?php echo($cont_fila); ?>">
                            	<?php echo($rips_ap_aux["nombre_procedimiento"]); ?>
                            </td>
                            <td align="center" style="width:150px;">
                            	<?php
                                	$combo->getComboDb("cmb_amb_rea_ap_".$cont_fila, ($rips_ap_aux["amb_rea"] != "" ? $rips_ap_aux["amb_rea"] : "1"), $lista_amb_rea, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$combo->getComboDb("cmb_fin_pro_ap_".$cont_fila, $rips_ap_aux["fin_pro"], $lista_fin_pro, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
									//Personal que atiende
									$per_ati_aux = "";
									if (intval($rips_ap_aux["id_rips_ap"]) > 0) {
										$per_ati_aux = $rips_ap_aux["per_ati"];
									} else {
										switch ($rips_ap_aux["id_tipo_reg"]) {
											case "1":
											case "6":
											case "8":
											case "10": //Optómetra
												$per_ati_aux = "5";
												break;
											default: //Oftalmólogo
												$per_ati_aux = "1";
												break;
										}
									}
									
                                	$combo->getComboDb("cmb_per_ati_ap_".$cont_fila, $per_ati_aux, $lista_per_ati, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <?php
								$cod_ciex_aux = "";
								$nom_ciex_aux = "";
                            	if (intval($rips_ap_aux["id_rips_ap"]) > 0) {
									$cod_ciex_aux = $rips_ap_aux["cod_ciex_prin"];
									$nom_ciex_aux = $rips_ap_aux["nom_ciex_prin"];
								} else if (isset($mapa_diag_ap[$rips_ap_aux["id_hc"]][0])) {
									$cod_ciex_aux = $mapa_diag_ap[$rips_ap_aux["id_hc"]][0]["cod_ciex_ori"];
									$nom_ciex_aux = $mapa_diag_ap[$rips_ap_aux["id_hc"]][0]["nom_ciex"];
								}
							?>
                            <td align="center" style="width:60px;">
                            	<input type="text" id="txt_cod_ciex_prin_ap_<?php echo($cont_fila); ?>" maxlength="4" class="input_sin_margen" style="width:100%;" value="<?php echo($cod_ciex_aux); ?>" onkeypress="return solo_alfanumericos(event);" onblur="convertir_a_mayusculas(this); cargar_nombre_ciex(this.value, 'td_nombre_ciex_prin_ap_<?php echo($cont_fila); ?>');" />
                            </td>
                            <td align="left" style="width:290px;" class="td_reducido" id="td_nombre_ciex_prin_ap_<?php echo($cont_fila); ?>">
                            	<?php echo($nom_ciex_aux); ?>
                            </td>
                            <?php
								$cod_ciex_aux = "";
								$nom_ciex_aux = "";
                            	if (intval($rips_ap_aux["id_rips_ap"]) > 0) {
									$cod_ciex_aux = $rips_ap_aux["cod_ciex_rel"];
									$nom_ciex_aux = $rips_ap_aux["nom_ciex_rel"];
								} else if (isset($mapa_diag_ap[$rips_ap_aux["id_hc"]][1])) {
									$cod_ciex_aux = $mapa_diag_ap[$rips_ap_aux["id_hc"]][1]["cod_ciex_ori"];
									$nom_ciex_aux = $mapa_diag_ap[$rips_ap_aux["id_hc"]][1]["nom_ciex"];
								}
							?>
                            <td align="center" style="width:60px;">
                            	<input type="text" id="txt_cod_ciex_rel_ap_<?php echo($cont_fila); ?>" maxlength="4" class="input_sin_margen" style="width:100%;" value="<?php echo($cod_ciex_aux); ?>" onkeypress="return solo_alfanumericos(event);" onblur="convertir_a_mayusculas(this); cargar_nombre_ciex(this.value, 'td_nombre_ciex_rel_ap_<?php echo($cont_fila); ?>');" />
                            </td>
                            <td align="left" style="width:290px;" class="td_reducido" id="td_nombre_ciex_rel_ap_<?php echo($cont_fila); ?>">
                            	<?php echo($nom_ciex_aux); ?>
                            </td>
                            <?php
								$cod_ciex_aux = "";
								$nom_ciex_aux = "";
                            	if (intval($rips_ap_aux["id_rips_ap"]) > 0) {
									$cod_ciex_aux = $rips_ap_aux["cod_ciex_com"];
									$nom_ciex_aux = $rips_ap_aux["nom_ciex_com"];
								}
							?>
                            <td align="center" style="width:60px;">
                            	<input type="text" id="txt_cod_ciex_com_ap_<?php echo($cont_fila); ?>" maxlength="4" class="input_sin_margen" style="width:100%;" value="<?php echo($cod_ciex_aux); ?>" onkeypress="return solo_alfanumericos(event);" onblur="convertir_a_mayusculas(this); cargar_nombre_ciex(this.value, 'td_nombre_ciex_com_ap_<?php echo($cont_fila); ?>');" />
                            </td>
                            <td align="left" style="width:290px;" class="td_reducido" id="td_nombre_ciex_com_ap_<?php echo($cont_fila); ?>">
                            	<?php echo($nom_ciex_aux); ?>
                            </td>
                            <?php
								$for_rea_aux = "";
								if (intval($rips_ap_aux["id_rips_ap"]) > 0) {
									$for_rea_aux = $rips_ap_aux["for_rea"];
								} else {
									if (isset($mapa_cx_ap[$rips_ap_aux["id_hc"]])) {
										$cx_aux = $mapa_cx_ap[$rips_ap_aux["id_hc"]];
										
										if ($cx_aux["cant_cx"] > 1) {
											if ($cx_aux["cant_vias"] > 1) {
												$for_rea_aux = "5";
											} else {
												$for_rea_aux = "3";
											}
										} else {
											$for_rea_aux = "1";
										}
									}
								}
							?>
                            <td align="center" style="width:350px;">
                            	<?php
                                	$combo->getComboDb("cmb_for_rea_ap_".$cont_fila, $for_rea_aux, $lista_for_rea, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <?php
								//Valor del procedimiento
								$valor_aux = 0;
								$valor_copago_aux = 0;
								if (intval($rips_ap_aux["id_rips_ap"]) > 0) {
									$valor_aux = $rips_ap_aux["valor_pro"];
									$valor_copago_aux = floatval("0".$rips_ap_aux["valor_copago"]);
								} else {
									$valor_aux = floatval("0".$rips_ap_aux["valor_p"]);
									$valor_copago_aux = floatval("0".$rips_ap_aux["valor_cuota_p"]);
								}
							?>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_pro_ap_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="<?php echo($valor_aux); ?>" onkeypress="return solo_numeros(event, true);" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_copago_ap_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="<?php echo($valor_copago_aux); ?>" onkeypress="return solo_numeros(event, true);" />
                            </td>
                            <td align="center" style="width:400px;">
                            	<input type="text" id="txt_observaciones_ap_<?php echo($cont_fila); ?>" maxlength="200" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_ap_aux["observaciones"]); ?>" />
                            </td>
                        </tr>
                        <?php
											$cont_fila++;
										}
									}
									$id_detalle_precio_ant = $id_detalle_precio_aux;
								}
							}
						?>
                        <tr style="display:none;">
                        	<td>
                            	<input type="hidden" id="hdd_cant_registros_ap" value="<?php echo($cont_fila); ?>" />
                            </td>
                        </tr>
                        <?php
							for ($i = 0; $i < 100; $i++) {
						?>
                        <tr id="tr_reg_ap_<?php echo($cont_fila); ?>" style="display:none;">
                            <td align="center" style="width:50px;">
                            	<input type="hidden" id="hdd_id_rips_ap_<?php echo($cont_fila); ?>" value="0" />
                            	<input type="hidden" id="hdd_id_admision_ap_<?php echo($cont_fila); ?>" value="" />
                            	<input type="hidden" id="hdd_id_detalle_precio_ap_<?php echo($cont_fila); ?>" value="" />
                            	<input type="hidden" id="hdd_id_paciente_ap_<?php echo($cont_fila); ?>" value="" />
                            	<input type="hidden" id="hdd_id_hc_ap_<?php echo($cont_fila); ?>" value="" />
                                <input type="hidden" id="hdd_con_datos_ap_<?php echo($cont_fila); ?>" value="0" />
								<?php echo($cont_fila + 1); ?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="checkbox" id="chk_revisado_ap_<?php echo($cont_fila); ?>" style="margin:auto;" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="checkbox" id="chk_borrado_ap_<?php echo($cont_fila); ?>" style="margin:auto;" />
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$combo->getComboDb("cmb_tipo_documento_ap_".$cont_fila, "", $lista_tipos_documento, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_numero_documento_ap_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_alfanumericos(event);" />
                            </td>
                            <td align="left" class="td_reducido" id="td_nombre_completo_ap_<?php echo($cont_fila); ?>" style="width:250px;"></td>
                            <td align="center" style="width:100px;">
                            	<?php
                                	$num_factura_aux = "";
									if ($tipo_factura == "1") {
										$num_factura_aux = $num_factura;
									}
								?>
                            	<input type="text" id="txt_num_factura_ap_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="<?php echo($num_factura_aux); ?>" <?php if ($tipo_factura != "2") { ?> readonly="readonly"<?php } ?> />
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$combo->getComboDb("cmb_convenio_ap_".$cont_fila, $id_convenio, $lista_convenios, "id_convenio, nombre_convenio", "&nbsp;", "seleccionar_convenio(this.value, 'ap', '".$cont_fila."');", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:250px;">
                            	<div id="d_planes_ap_<?php echo($cont_fila); ?>">
									<?php
                                        $combo->getComboDb("cmb_plan_ap_".$cont_fila, $id_plan, $lista_planes, "id_plan, nombre_plan", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
                                    ?>
                                </div>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_fecha_pro_ap_<?php echo($cont_fila); ?>" class="input_sin_margen" maxlength="10" style="width:100%;" value="" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
								<script type="text/javascript" id="ajax">
									$('#txt_fecha_pro_ap_<?php echo($cont_fila); ?>').fdatepicker({
										format: 'dd/mm/yyyy'
									});
                                </script>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_num_autorizacion_ap_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="" />
                            </td>
                            <td align="center" style="width:65px;">
                            	<input type="text" id="txt_cod_procedimiento_ap_<?php echo($cont_fila); ?>" maxlength="6" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_numeros(event, false);" onblur="cargar_nombre_procedimiento(this.value, 'td_nombre_procedimiento_ap_<?php echo($cont_fila); ?>');" />
                            </td>
                            <td align="left" style="width:285px;" class="td_reducido" id="td_nombre_procedimiento_ap_<?php echo($cont_fila); ?>"></td>
                            <td align="center" style="width:150px;">
                            	<?php
                                	$combo->getComboDb("cmb_amb_rea_ap_".$cont_fila, "1", $lista_amb_rea, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$combo->getComboDb("cmb_fin_pro_ap_".$cont_fila, "", $lista_fin_pro, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$combo->getComboDb("cmb_per_ati_ap_".$cont_fila, "", $lista_per_ati, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:60px;">
                            	<input type="text" id="txt_cod_ciex_prin_ap_<?php echo($cont_fila); ?>" maxlength="4" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_alfanumericos(event);" onblur="convertir_a_mayusculas(this); cargar_nombre_ciex(this.value, 'td_nombre_ciex_prin_ap_<?php echo($cont_fila); ?>');" />
                            </td>
                            <td align="left" style="width:290px;" class="td_reducido" id="td_nombre_ciex_prin_ap_<?php echo($cont_fila); ?>"></td>
                            <td align="center" style="width:60px;">
                            	<input type="text" id="txt_cod_ciex_rel_ap_<?php echo($cont_fila); ?>" maxlength="4" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_alfanumericos(event);" onblur="convertir_a_mayusculas(this); cargar_nombre_ciex(this.value, 'td_nombre_ciex_rel_ap_<?php echo($cont_fila); ?>');" />
                            </td>
                            <td align="left" style="width:290px;" class="td_reducido" id="td_nombre_ciex_rel_ap_<?php echo($cont_fila); ?>"></td>
                            <td align="center" style="width:60px;">
                            	<input type="text" id="txt_cod_ciex_com_ap_<?php echo($cont_fila); ?>" maxlength="4" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_alfanumericos(event);" onblur="convertir_a_mayusculas(this); cargar_nombre_ciex(this.value, 'td_nombre_ciex_com_ap_<?php echo($cont_fila); ?>');" />
                            </td>
                            <td align="left" style="width:290px;" class="td_reducido" id="td_nombre_ciex_com_ap_<?php echo($cont_fila); ?>"></td>
                            <td align="center" style="width:350px;">
                            	<?php
                                	$combo->getComboDb("cmb_for_rea_ap_".$cont_fila, "", $lista_for_rea, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_pro_ap_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_numeros(event, true);" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_copago_ap_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_numeros(event, true);" />
                            </td>
                            <td align="center" style="width:400px;">
                            	<input type="text" id="txt_observaciones_ap_<?php echo($cont_fila); ?>" maxlength="200" class="input_sin_margen" style="width:100%;" value="" />
                            </td>
                        </tr>
                        <?php
								$cont_fila++;
							}
						?>
                        <tr>
                        	<td align="left">
                            	<div class="Add-icon" onclick="agregar_registro_ap();" title="Agregar registro" style="width:50px;"></div>
                            </td>
                        </tr>
                    </table>
                </div>
                <script id="ajax" type="text/javascript">
					contar_registros("ap");
				</script>
    <?php
		break;
		
	case "18": //Registro de medicamentos
		@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
		@$id_plan = $utilidades->str_decode($_POST["id_plan"]);
		@$fecha_inicial = $utilidades->str_decode($_POST["fecha_inicial"]);
		@$fecha_final = $utilidades->str_decode($_POST["fecha_final"]);
		@$tipo_factura = $utilidades->str_decode($_POST["tipo_factura"]);
		@$num_factura = $utilidades->str_decode($_POST["num_factura"]);
		@$ind_rips_existentes = intval($utilidades->str_decode($_POST["ind_rips_existentes"]), 10);
		@$ind_sin_np = intval($utilidades->str_decode($_POST["ind_sin_np"]), 10);
		
		//Se obtienen los datos de los RIPS de medicamentos
		$lista_rips_am = $db_rips->get_lista_registros_am($id_convenio, $id_plan, $fecha_inicial, $fecha_final, $ind_rips_existentes, $ind_sin_np);
		
	?>
    			<div id="d_tbl_encabezado_am" style="overflow:hidden; width:100%;">
                    <table class="modal_table" style="width:3500px; margin:auto;" >
                        <thead>
                            <tr>
                                <th class="th_reducido" align="center" style="width:50px;">#</th>
                                <th class="th_reducido" align="center" style="width:100px;">Revisado</th>
                                <th class="th_reducido" align="center" style="width:100px;">Excluido</th>
                                <th class="th_reducido" align="center" style="width:250px;">Tipo documento</th>
                                <th class="th_reducido" align="center" style="width:100px;">No. documento</th>
                                <th class="th_reducido" align="center" style="width:250px;">Nombre completo</th>
                                <th class="th_reducido" align="center" style="width:100px;">No. Factura</th>
                                <th class="th_reducido" align="center" style="width:250px;">Convenio</th>
                                <th class="th_reducido" align="center" style="width:250px;">Plan</th>
                                <th class="th_reducido" align="center" style="width:100px;">Fecha medicamento</th>
                                <th class="th_reducido" align="center" style="width:100px;">No. Autorizaci&oacute;n</th>
                                <th class="th_reducido" align="center" style="width:100px;">C&oacute;digo del medicamento</th>
                                <th class="th_reducido" align="center" style="width:200px;">Tipo de medicamento</th>
                                <th class="th_reducido" align="center" style="width:250px;">Nombre gen&eacute;rico</th>
                                <th class="th_reducido" align="center" style="width:200px;">Forma farmac&eacute;utica</th>
                                <th class="th_reducido" align="center" style="width:200px;">Concentraci&oacute;n</th>
                                <th class="th_reducido" align="center" style="width:200px;">Unidad de medida</th>
                                <th class="th_reducido" align="center" style="width:100px;">N&uacute;mero de unidades</th>
                                <th class="th_reducido" align="center" style="width:100px;">Valor unitario</th>
                                <th class="th_reducido" align="center" style="width:100px;">Valor total</th>
                                <th class="th_reducido" align="center" style="width:400px;">Observaciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div id="d_tbl_cuerpo_am" style="overflow:auto; width:100%; height:420px;" onscroll="desplazar_tabla_horizontal('am');">
                    <table class="modal_table" style="width:3500px; margin:auto;" >
                        <?php
							//Se obtiene el listado de convenios
							$lista_convenios = $db_convenios->getListaConveniosActivos();
							
							//Se obtiene el listado de planes
							$lista_planes = $db_planes->getListaPlanesActivos($id_convenio);
							
							//listado de tipos de documento
							$lista_tipos_documento = $db_listas->getListaDetalles(2);
							
							//listado de tipos de medicamentos
							$lista_tipos_medicamentos = $db_listas->getListaDetalles(37);
							
							$cont_fila = 0;
                        	if (count($lista_rips_am) > 0) {
								$id_detalle_precio_ant = -1;
								foreach ($lista_rips_am as $rips_am_aux) {
									$id_detalle_precio_aux =  isset($rips_am_aux["id_detalle_precio"]) ? intval($rips_am_aux["id_detalle_precio"], 0) : 0;
									if ($id_detalle_precio_aux != $id_detalle_precio_ant || $id_detalle_precio_aux == "") {
										
										$det_precio = isset($rips_am_aux["id_detalle_precio"]) ? $rips_am_aux["id_detalle_precio"] : 0;
									
						?>
                        <tr id="tr_reg_am_<?php echo($cont_fila); ?>">
                            <td align="center" style="width:50px;">
                            	<input type="hidden" id="hdd_id_rips_am_<?php echo($cont_fila); ?>" value="<?php echo($rips_am_aux["id_rips_am"]); ?>" />
                            	<input type="hidden" id="hdd_id_admision_am_<?php echo($cont_fila); ?>" value="<?php echo($rips_am_aux["id_admision"]); ?>" />
                            	<input type="hidden" id="hdd_id_detalle_precio_am_<?php echo($cont_fila); ?>" value="<?= $det_precio ?>" />
                            	<input type="hidden" id="hdd_id_paciente_am_<?php echo($cont_fila); ?>" value="<?php echo($rips_am_aux["id_paciente"]); ?>" />
                                <input type="hidden" id="hdd_con_datos_am_<?php echo($cont_fila); ?>" value="1" />
								<?php echo($cont_fila + 1); ?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="checkbox" id="chk_revisado_am_<?php echo($cont_fila); ?>" style="margin:auto;"<?php if ($rips_am_aux["ind_revisado"] == "1") { ?> checked="checked"<?php } ?> />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="checkbox" id="chk_borrado_am_<?php echo($cont_fila); ?>" style="margin:auto;"<?php if ($rips_am_aux["ind_borrado"] == "1") { ?> checked="checked"<?php } ?> />
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$combo->getComboDb("cmb_tipo_documento_am_".$cont_fila, $rips_am_aux["tipo_documento"], $lista_tipos_documento, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_numero_documento_am_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_am_aux["numero_documento"]); ?>" onkeypress="return solo_alfanumericos(event);" />
                            </td>
                            <td align="left" class="td_reducido" id="td_nombre_completo_am_<?php echo($cont_fila); ?>" style="width:250px;">
								<?php echo($funciones_persona->obtenerNombreCompleto($rips_am_aux["nombre_1"], $rips_am_aux["nombre_2"], $rips_am_aux["apellido_1"], $rips_am_aux["apellido_2"])); ?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<?php
                                	$num_factura_aux = $rips_am_aux["num_factura"];
									if ($tipo_factura == "1") {
										$num_factura_aux = $num_factura;
									}
								?>
                            	<input type="text" id="txt_num_factura_am_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="<?php echo($num_factura_aux); ?>" <?php if ($tipo_factura != "2") { ?> readonly="readonly"<?php } ?> />
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$combo->getComboDb("cmb_convenio_am_".$cont_fila, $rips_am_aux["id_convenio"], $lista_convenios, "id_convenio, nombre_convenio", "&nbsp;", "seleccionar_convenio(this.value, 'am', '".$cont_fila."');", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:250px;">
                            	<div id="d_planes_am_<?php echo($cont_fila); ?>">
									<?php
                                        $combo->getComboDb("cmb_plan_am_".$cont_fila, $rips_am_aux["id_plan"], $lista_planes, "id_plan, nombre_plan", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
                                    ?>
                                </div>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_fecha_medicamento_am_<?php echo($cont_fila); ?>" class="input_sin_margen" maxlength="10" style="width:100%;" value="<?php echo($rips_am_aux["fecha_medicamento_t"]); ?>" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
								<script type="text/javascript" id="ajax">
									$('#txt_fecha_medicamento_am_<?php echo($cont_fila); ?>').fdatepicker({
										format: 'dd/mm/yyyy'
									});
                                </script>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_num_autorizacion_am_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="<?php echo(substr($rips_am_aux["num_autorizacion"], 0, 15)); ?>" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_cod_medicamento_am_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_am_aux["cod_medicamento"]); ?>" onkeypress="return solo_numeros(event, false);" />
                            </td>
                            <td align="center" style="width:200px;">
                            	<?php
                                	$combo->getComboDb("cmb_tipo_medicamento_am_".$cont_fila, $rips_am_aux["tipo_medicamento"], $lista_tipos_medicamentos, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$nombre_generico_aux = substr(str_replace(",", "", $rips_am_aux["nombre_generico"]), 0, 30);
								?>
                            	<input type="text" id="txt_nombre_generico_am_<?php echo($cont_fila); ?>" maxlength="30" class="input_sin_margen" style="width:100%;" value="<?php echo($nombre_generico_aux); ?>" />
                            </td>
                            <td align="center" style="width:200px;">
                            	<?php
                                	$forma_farma_aux = substr(str_replace(",", "", $rips_am_aux["forma_farma"]), 0, 20);
								?>
                            	<input type="text" id="txt_forma_farma_am_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="<?php echo($forma_farma_aux); ?>" />
                            </td>
                            <td align="center" style="width:200px;">
                            	<?php
                                	$concentracion_aux = substr(str_replace(",", "", $rips_am_aux["concentracion"]), 0, 20);
								?>
                            	<input type="text" id="txt_concentracion_am_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="<?php echo($concentracion_aux); ?>" />
                            </td>
                            <td align="center" style="width:200px;">
                            	<?php
                                	$unidad_medida_aux = substr(str_replace(",", "", $rips_am_aux["unidad_medida"]), 0, 20);
								?>
                            	<input type="text" id="txt_unidad_medida_am_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="<?php echo($unidad_medida_aux); ?>" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_cantidad_am_<?php echo($cont_fila); ?>" maxlength="5" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_am_aux["cantidad_orden"]); ?>" onkeypress="return solo_numeros(event, false);" onblur="calcular_total_am(<?php echo($cont_fila); ?>);" />
                            </td>
                            <?php
								//Valor unitario del medicamento
								$valor_aux = 0;
								if (intval($rips_am_aux["id_rips_am"]) > 0) {
									$valor_aux = $rips_am_aux["valor_medicamento"];
								} else {
									$valor_aux = floatval("0".$rips_am_aux["valor_p"]);
								}
							?>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_medicamento_am_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="<?php echo($valor_aux); ?>" onkeypress="return solo_numeros(event, true);" onblur="calcular_total_am(<?php echo($cont_fila); ?>);" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_total_med_am_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="<?php echo(intval($rips_am_aux["cantidad"], 10) * $valor_aux); ?>" readonly="readonly" />
                            </td>
                            <td align="center" style="width:400px;">
                            	<input type="text" id="txt_observaciones_am_<?php echo($cont_fila); ?>" maxlength="200" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_am_aux["observaciones"]); ?>" />
                            </td>
                        </tr>
                        <?php
										$cont_fila++;
									}
									$id_detalle_precio_ant = $id_detalle_precio_aux;
								}
							}
						?>
                        <tr style="display:none;">
                        	<td>
                            	<input type="hidden" id="hdd_cant_registros_am" value="<?php echo($cont_fila); ?>" />
                            </td>
                        </tr>
                        <?php
							for ($i = 0; $i < 100; $i++) {
						?>
                        <tr id="tr_reg_am_<?php echo($cont_fila); ?>" style="display:none;">
                            <td align="center" style="width:50px;">
                            	<input type="hidden" id="hdd_id_rips_am_<?php echo($cont_fila); ?>" value="0" />
                            	<input type="hidden" id="hdd_id_admision_am_<?php echo($cont_fila); ?>" value="" />
                            	<input type="hidden" id="hdd_id_detalle_precio_am_<?php echo($cont_fila); ?>" value="" />
                            	<input type="hidden" id="hdd_id_paciente_am_<?php echo($cont_fila); ?>" value="" />
                                <input type="hidden" id="hdd_con_datos_am_<?php echo($cont_fila); ?>" value="0" />
								<?php echo($cont_fila + 1); ?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="checkbox" id="chk_revisado_am_<?php echo($cont_fila); ?>" style="margin:auto;" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="checkbox" id="chk_borrado_am_<?php echo($cont_fila); ?>" style="margin:auto;" />
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$combo->getComboDb("cmb_tipo_documento_am_".$cont_fila, "", $lista_tipos_documento, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_numero_documento_am_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_alfanumericos(event);" />
                            </td>
                            <td align="left" class="td_reducido" id="td_nombre_completo_am_<?php echo($cont_fila); ?>" style="width:250px;"></td>
                            <td align="center" style="width:100px;">
                            	<?php
                                	$num_factura_aux = "";
									if ($tipo_factura == "1") {
										$num_factura_aux = $num_factura;
									}
								?>
                            	<input type="text" id="txt_num_factura_am_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="<?php echo($num_factura_aux); ?>" <?php if ($tipo_factura != "2") { ?> readonly="readonly"<?php } ?> />
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$combo->getComboDb("cmb_convenio_am_".$cont_fila, $id_convenio, $lista_convenios, "id_convenio, nombre_convenio", "&nbsp;", "seleccionar_convenio(this.value, 'am', '".$cont_fila."');", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:250px;">
                            	<div id="d_planes_am_<?php echo($cont_fila); ?>">
									<?php
                                        $combo->getComboDb("cmb_plan_am_".$cont_fila, $id_plan, $lista_planes, "id_plan, nombre_plan", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
                                    ?>
                                </div>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_fecha_medicamento_am_<?php echo($cont_fila); ?>" class="input_sin_margen" maxlength="10" style="width:100%;" value="" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
								<script type="text/javascript" id="ajax">
									$('#txt_fecha_medicamento_am_<?php echo($cont_fila); ?>').fdatepicker({
										format: 'dd/mm/yyyy'
									});
                                </script>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_num_autorizacion_am_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_cod_medicamento_am_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_numeros(event, false);" />
                            </td>
                            <td align="center" style="width:200px;">
                            	<?php
                                	$combo->getComboDb("cmb_tipo_medicamento_am_".$cont_fila, "1", $lista_tipos_medicamentos, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:250px;">
                            	<input type="text" id="txt_nombre_generico_am_<?php echo($cont_fila); ?>" maxlength="30" class="input_sin_margen" style="width:100%;" value="" />
                            </td>
                            <td align="center" style="width:200px;">
                            	<input type="text" id="txt_forma_farma_am_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="" />
                            </td>
                            <td align="center" style="width:200px;">
                            	<input type="text" id="txt_concentracion_am_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="" />
                            </td>
                            <td align="center" style="width:200px;">
                            	<input type="text" id="txt_unidad_medida_am_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_cantidad_am_<?php echo($cont_fila); ?>" maxlength="5" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_numeros(event, false);" onblur="calcular_total_am(<?php echo($cont_fila); ?>);" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_medicamento_am_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_numeros(event, true);" onblur="calcular_total_am(<?php echo($cont_fila); ?>);" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_total_med_am_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="" readonly="readonly" />
                            </td>
                            <td align="center" style="width:400px;">
                            	<input type="text" id="txt_observaciones_am_<?php echo($cont_fila); ?>" maxlength="200" class="input_sin_margen" style="width:100%;" value="" />
                            </td>
                        </tr>
                        <?php
								$cont_fila++;
							}
						?>
                        <tr>
                        	<td align="left">
                            	<div class="Add-icon" onclick="agregar_registro_am();" title="Agregar registro" style="width:50px;"></div>
                            </td>
                        </tr>
                    </table>
                </div>
                <script id="ajax" type="text/javascript">
					contar_registros("am");
				</script>
    <?php
		break;
		
	case "19": //Registro de otros servicios
		@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
		@$id_plan = $utilidades->str_decode($_POST["id_plan"]);
		@$fecha_inicial = $utilidades->str_decode($_POST["fecha_inicial"]);
		@$fecha_final = $utilidades->str_decode($_POST["fecha_final"]);
		@$tipo_factura = $utilidades->str_decode($_POST["tipo_factura"]);
		@$num_factura = $utilidades->str_decode($_POST["num_factura"]);
		@$ind_rips_existentes = intval($utilidades->str_decode($_POST["ind_rips_existentes"]), 10);
		@$ind_sin_np = intval($utilidades->str_decode($_POST["ind_sin_np"]), 10);
		
		//Se obtienen los datos de los RIPS de otros servicios
		$lista_rips_at = $db_rips->get_lista_registros_at($id_convenio, $id_plan, $fecha_inicial, $fecha_final, $ind_rips_existentes, $ind_sin_np);
	?>
    			<div id="d_tbl_encabezado_at" style="overflow:hidden; width:100%;">
                    <table class="modal_table" style="width:2900px; margin:auto;">
                        <thead>
                            <tr>
                                <th class="th_reducido" align="center" style="width:50px;">#</th>
                                <th class="th_reducido" align="center" style="width:100px;">Revisado</th>
                                <th class="th_reducido" align="center" style="width:100px;">Excluido</th>
                                <th class="th_reducido" align="center" style="width:250px;">Tipo documento</th>
                                <th class="th_reducido" align="center" style="width:100px;">No. documento</th>
                                <th class="th_reducido" align="center" style="width:250px;">Nombre completo</th>
                                <th class="th_reducido" align="center" style="width:100px;">No. Factura</th>
                                <th class="th_reducido" align="center" style="width:250px;">Convenio</th>
                                <th class="th_reducido" align="center" style="width:250px;">Plan</th>
                                <th class="th_reducido" align="center" style="width:100px;">Fecha servicio</th>
                                <th class="th_reducido" align="center" style="width:100px;">No. Autorizaci&oacute;n</th>
                                <th class="th_reducido" align="center" style="width:200px;">Tipo de servicio</th>
                                <th class="th_reducido" align="center" style="width:100px;">C&oacute;digo del servicio</th>
                                <th class="th_reducido" align="center" style="width:250px;">Nombre del servicio</th>
                                <th class="th_reducido" align="center" style="width:100px;">Cantidad</th>
                                <th class="th_reducido" align="center" style="width:100px;">Valor unitario</th>
                                <th class="th_reducido" align="center" style="width:100px;">Valor total</th>
                                <th class="th_reducido" align="center" style="width:400px;">Observaciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div id="d_tbl_cuerpo_at" style="overflow:auto; width:100%; height:420px;" onscroll="desplazar_tabla_horizontal('at');">
                    <table class="modal_table" style="width:2900px; margin:auto;">
                        <?php
							//Se obtiene el listado de convenios
							$lista_convenios = $db_convenios->getListaConveniosActivos();
							
							//Se obtiene el listado de planes
							$lista_planes = $db_planes->getListaPlanesActivos($id_convenio);
							
							//listado de tipos de documento
							$lista_tipos_documento = $db_listas->getListaDetalles(2);
							
							//listado de tipos de servicios
							$lista_tipos_insumos = $db_listas->getListaDetalles(28);
							
							$cont_fila = 0;
                        	if (count($lista_rips_at) > 0) {
								$id_detalle_precio_ant = -1;
								foreach ($lista_rips_at as $rips_at_aux) {
									$id_detalle_precio_aux = intval($rips_at_aux["id_detalle_precio"], 0);
									if ($id_detalle_precio_aux != $id_detalle_precio_ant || $id_detalle_precio_aux == "") {
						?>
                        <tr id="tr_reg_at_<?php echo($cont_fila); ?>">
                            <td align="center" style="width:50px;">
                            	<input type="hidden" id="hdd_id_rips_at_<?php echo($cont_fila); ?>" value="<?php echo($rips_at_aux["id_rips_at"]); ?>" />
                            	<input type="hidden" id="hdd_id_admision_at_<?php echo($cont_fila); ?>" value="<?php echo($rips_at_aux["id_admision"]); ?>" />
                            	<input type="hidden" id="hdd_id_detalle_precio_at_<?php echo($cont_fila); ?>" value="<?php echo($rips_at_aux["id_detalle_precio"]); ?>" />
                            	<input type="hidden" id="hdd_id_paciente_at_<?php echo($cont_fila); ?>" value="<?php echo($rips_at_aux["id_paciente"]); ?>" />
                                <input type="hidden" id="hdd_con_datos_at_<?php echo($cont_fila); ?>" value="1" />
								<?php echo($cont_fila + 1); ?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="checkbox" id="chk_revisado_at_<?php echo($cont_fila); ?>" style="margin:auto;"<?php if ($rips_at_aux["ind_revisado"] == "1") { ?> checked="checked"<?php } ?> />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="checkbox" id="chk_borrado_at_<?php echo($cont_fila); ?>" style="margin:auto;"<?php if ($rips_at_aux["ind_borrado"] == "1") { ?> checked="checked"<?php } ?> />
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$combo->getComboDb("cmb_tipo_documento_at_".$cont_fila, $rips_at_aux["tipo_documento"], $lista_tipos_documento, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_numero_documento_at_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_at_aux["numero_documento"]); ?>" onkeypress="return solo_alfanumericos(event);" />
                            </td>
                            <td align="left" class="td_reducido" id="td_nombre_completo_at_<?php echo($cont_fila); ?>" style="width:250px;">
								<?php echo($funciones_persona->obtenerNombreCompleto($rips_at_aux["nombre_1"], $rips_at_aux["nombre_2"], $rips_at_aux["apellido_1"], $rips_at_aux["apellido_2"])); ?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<?php
                                	$num_factura_aux = $rips_at_aux["num_factura"];
									if ($tipo_factura == "1") {
										$num_factura_aux = $num_factura;
									}
								?>
                            	<input type="text" id="txt_num_factura_at_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="<?php echo($num_factura_aux); ?>" <?php if ($tipo_factura != "2") { ?> readonly="readonly"<?php } ?> />
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$combo->getComboDb("cmb_convenio_at_".$cont_fila, $rips_at_aux["id_convenio"], $lista_convenios, "id_convenio, nombre_convenio", "&nbsp;", "seleccionar_convenio(this.value, 'at', '".$cont_fila."');", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:250px;">
                            	<div id="d_planes_at_<?php echo($cont_fila); ?>">
									<?php
                                        $combo->getComboDb("cmb_plan_at_".$cont_fila, $rips_at_aux["id_plan"], $lista_planes, "id_plan, nombre_plan", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
                                    ?>
                                </div>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_fecha_insumo_at_<?php echo($cont_fila); ?>" class="input_sin_margen" maxlength="10" style="width:100%;" value="<?php echo($rips_at_aux["fecha_insumo_t"]); ?>" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
								<script type="text/javascript" id="ajax">
									$('#txt_fecha_insumo_at_<?php echo($cont_fila); ?>').fdatepicker({
										format: 'dd/mm/yyyy'
									});
                                </script>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_num_autorizacion_at_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="<?php echo(substr($rips_at_aux["num_autorizacion"], 0, 15)); ?>" />
                            </td>
                            <td align="center" style="width:200px;">
                            	<?php
                                	$combo->getComboDb("cmb_tipo_insumo_at_".$cont_fila, $rips_at_aux["tipo_insumo"], $lista_tipos_insumos, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_cod_insumo_at_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_at_aux["cod_insumo"]); ?>" onkeypress="return solo_numeros(event, false);" />
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$nombre_insumo_aux = substr(str_replace(",", "", $rips_at_aux["nombre_insumo"]), 0, 60);
								?>
                            	<input type="text" id="txt_nombre_insumo_at_<?php echo($cont_fila); ?>" maxlength="60" class="input_sin_margen" style="width:100%;" value="<?php echo($nombre_insumo_aux); ?>" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_cantidad_at_<?php echo($cont_fila); ?>" maxlength="5" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_at_aux["cantidad"]); ?>" onkeypress="return solo_numeros(event, false);" onblur="calcular_total_at(<?php echo($cont_fila); ?>);" />
                            </td>
                            <?php
								//Valor unitario del servicio
								$valor_aux = 0;
								if (intval($rips_at_aux["id_rips_at"]) > 0) {
									$valor_aux = $rips_at_aux["valor_insumo"];
								} else {
									$valor_aux = floatval("0".$rips_at_aux["valor_p"]);
								}
							?>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_insumo_at_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="<?php echo($valor_aux); ?>" onkeypress="return solo_numeros(event, true);" onblur="calcular_total_at(<?php echo($cont_fila); ?>);" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_total_serv_at_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="<?php echo(intval($rips_at_aux["cantidad"], 10) * $valor_aux); ?>" readonly="readonly" />
                            </td>
                            <td align="center" style="width:400px;">
                            	<input type="text" id="txt_observaciones_at_<?php echo($cont_fila); ?>" maxlength="200" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_at_aux["observaciones"]); ?>" />
                            </td>
                        </tr>
                        <?php
										$cont_fila++;
									}
									$id_detalle_precio_ant = $id_detalle_precio_aux;
								}
							}
						?>
                        <tr style="display:none;">
                        	<td>
                            	<input type="hidden" id="hdd_cant_registros_at" value="<?php echo($cont_fila); ?>" />
                            </td>
                        </tr>
                        <?php
							for ($i = 0; $i < 100; $i++) {
						?>
                        <tr id="tr_reg_at_<?php echo($cont_fila); ?>" style="display:none;">
                            <td align="center" style="width:50px;">
                            	<input type="hidden" id="hdd_id_rips_at_<?php echo($cont_fila); ?>" value="0" />
                            	<input type="hidden" id="hdd_id_admision_at_<?php echo($cont_fila); ?>" value="" />
                            	<input type="hidden" id="hdd_id_detalle_precio_at_<?php echo($cont_fila); ?>" value="" />
                            	<input type="hidden" id="hdd_id_paciente_at_<?php echo($cont_fila); ?>" value="" />
                                <input type="hidden" id="hdd_con_datos_at_<?php echo($cont_fila); ?>" value="0" />
								<?php echo($cont_fila + 1); ?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="checkbox" id="chk_revisado_at_<?php echo($cont_fila); ?>" style="margin:auto;" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="checkbox" id="chk_borrado_at_<?php echo($cont_fila); ?>" style="margin:auto;" />
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$combo->getComboDb("cmb_tipo_documento_at_".$cont_fila, "", $lista_tipos_documento, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_numero_documento_at_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_alfanumericos(event);" />
                            </td>
                            <td align="left" class="td_reducido" id="td_nombre_completo_at_<?php echo($cont_fila); ?>" style="width:250px;"></td>
                            <td align="center" style="width:100px;">
                            	<?php
                                	$num_factura_aux = "";
									if ($tipo_factura == "1") {
										$num_factura_aux = $num_factura;
									}
								?>
                            	<input type="text" id="txt_num_factura_at_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="<?php echo($num_factura_aux); ?>" <?php if ($tipo_factura != "2") { ?> readonly="readonly"<?php } ?> />
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$combo->getComboDb("cmb_convenio_at_".$cont_fila, $id_convenio, $lista_convenios, "id_convenio, nombre_convenio", "&nbsp;", "seleccionar_convenio(this.value, 'at', '".$cont_fila."');", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:250px;">
                            	<div id="d_planes_at_<?php echo($cont_fila); ?>">
									<?php
                                        $combo->getComboDb("cmb_plan_at_".$cont_fila, $id_plan, $lista_planes, "id_plan, nombre_plan", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
                                    ?>
                                </div>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_fecha_insumo_at_<?php echo($cont_fila); ?>" class="input_sin_margen" maxlength="10" style="width:100%;" value="" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
								<script type="text/javascript" id="ajax">
									$('#txt_fecha_insumo_at_<?php echo($cont_fila); ?>').fdatepicker({
										format: 'dd/mm/yyyy'
									});
                                </script>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_num_autorizacion_at_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="" />
                            </td>
                            <td align="center" style="width:200px;">
                            	<?php
                                	$combo->getComboDb("cmb_tipo_insumo_at_".$cont_fila, "", $lista_tipos_insumos, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_cod_insumo_at_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_numeros(event, false);" />
                            </td>
                            <td align="center" style="width:250px;">
                            	<input type="text" id="txt_nombre_insumo_at_<?php echo($cont_fila); ?>" maxlength="60" class="input_sin_margen" style="width:100%;" value="" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_cantidad_at_<?php echo($cont_fila); ?>" maxlength="5" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_numeros(event, false);" onblur="calcular_total_at(<?php echo($cont_fila); ?>);" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_insumo_at_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_numeros(event, true);" onblur="calcular_total_at(<?php echo($cont_fila); ?>);" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_valor_total_serv_at_<?php echo($cont_fila); ?>" maxlength="15" class="input_sin_margen" style="width:100%;" value="" readonly="readonly" />
                            </td>
                            <td align="center" style="width:400px;">
                            	<input type="text" id="txt_observaciones_at_<?php echo($cont_fila); ?>" maxlength="200" class="input_sin_margen" style="width:100%;" value="" />
                            </td>
                        </tr>
                        <?php
								$cont_fila++;
							}
						?>
                        <tr>
                        	<td align="left">
                            	<div class="Add-icon" onclick="agregar_registro_at();" title="Agregar registro" style="width:50px;"></div>
                            </td>
                        </tr>
                    </table>
                </div>
                <script id="ajax" type="text/javascript">
					contar_registros("at");
				</script>
    <?php
		break;
		
	case "20": //Registro de usuarios
		@$id_convenio = $utilidades->str_decode($_POST["id_convenio"]);
		@$id_plan = $utilidades->str_decode($_POST["id_plan"]);
		@$fecha_inicial = $utilidades->str_decode($_POST["fecha_inicial"]);
		@$fecha_final = $utilidades->str_decode($_POST["fecha_final"]);
		@$ind_rips_existentes = intval($utilidades->str_decode($_POST["ind_rips_existentes"]), 10);
		@$ind_sin_np = intval($utilidades->str_decode($_POST["ind_sin_np"]), 10);
		@$id_rips = intval($utilidades->str_decode($_POST["id_rips"]), 10);
		
		//Tipos de carga: 1. RIPS existentes - 2. Historia clínica
		$tipo_carga = 0;
		if ($id_rips > 0 && $ind_rips_existentes == 0) {
			$tipo_carga = 1;
		} else {
			$tipo_carga = 2;
		}
		
		//Se cargan los datos de los usuarios
		
		if ($tipo_carga == 1) {
			//Se obtienen los datos de los RIPS existentes
			$lista_rips_us = $db_rips->get_lista_rips_usuarios($id_rips, "");
			if (count($lista_rips_us) == 0) {
				$lista_rips_us = $db_rips->get_lista_registros_us($id_convenio, $id_plan, $fecha_inicial, $fecha_final, $ind_rips_existentes, $ind_sin_np);
				$tipo_carga = 2;
			}
		} else {
			//Se obtienen los datos de la historia clínica
			$lista_rips_us = $db_rips->get_lista_registros_us($id_convenio, $id_plan, $fecha_inicial, $fecha_final, $ind_rips_existentes, $ind_sin_np);
		}

		//var_dump($lista_rips_us[0]);
		
		//listado de tipos de documento
		$lista_tipos_documento = $db_listas->getListaDetalles(2);
		
		//Se obtienen los datos del convenio
		$convenio_obj = $db_convenios->getConvenio($id_convenio);
					?>
                    <div id="d_tbl_encabezado_us" style="overflow:hidden; width:100%;">
                    <table class="modal_table" style="width:2800px; margin:auto;">
                        <thead>
                            <tr>
                                <th class="th_reducido" align="center" style="width:50px;">#</th>
                                <th class="th_reducido" align="center" style="width:100px;">Revisado</th>
                                <th class="th_reducido" align="center" style="width:100px;">Excluido</th>
                                <th class="th_reducido" align="center" style="width:250px;">Tipo documento</th>
                                <th class="th_reducido" align="center" style="width:100px;">No. documento</th>
                                <th class="th_reducido" align="center" style="width:150px;">Primer nombre</th>
                                <th class="th_reducido" align="center" style="width:150px;">Segundo nombre</th>
                                <th class="th_reducido" align="center" style="width:150px;">Primer apellido</th>
                                <th class="th_reducido" align="center" style="width:150px;">Segundo apellido</th>
                                <th class="th_reducido" align="center" style="width:100px;">C&oacute;digo administradora</th>
                                <th class="th_reducido" align="center" style="width:200px;">Tipo de usuario</th>
                                <th class="th_reducido" align="center" style="width:100px;">Edad</th>
                                <th class="th_reducido" align="center" style="width:100px;">Unidad de edad</th>
								<th class="th_reducido" align="center" style="width:100px;">Fecha nacimiento</th>
                                <th class="th_reducido" align="center" style="width:150px;">Sexo</th>
								<th class="th_reducido" align="center" style="width:150px;">Pais</th>
                                <th class="th_reducido" align="center" style="width:200px;">Departamento de residencia</th>
                                <th class="th_reducido" align="center" style="width:200px;">Municipio de residencia</th>
                                <th class="th_reducido" align="center" style="width:150px;">Zona de residencia</th>
                                <th class="th_reducido" align="center" style="width:400px;">Observaciones</th>
                            </tr>
                        </thead>
                    </table>
                    </div>
                    <div id="d_tbl_cuerpo_us" style="overflow:auto; width:100%; height:420px;" onscroll="desplazar_tabla_horizontal('us');">
                    <table class="modal_table" style="width:2800px; margin:auto;">
                        <?php
							//listado de tipos de usuario
							$lista_tipos_usuarios = $db_listas->getListaDetalles(29);
							
							//listado de tipos de unidades de edad
							$lista_unidades_edad = $db_listas->getListaDetalles(38);
							
							//listado de sexos
							$lista_sexos = $db_listas->getListaDetalles(1);
							
							//listado de zonas
							$lista_zonas = $db_listas->getListaDetalles(5);
							
							//Listado de departamentos
							$lista_departamentos = $db_departamentos->getDepartamentos();
							$lista_paises = $db_paises->getPaises();
							
							$cont_fila = 0;
                        	if (count($lista_rips_us) > 0) {
								foreach ($lista_rips_us as $rips_us_aux) {
						?>
                        <tr id="tr_reg_us_<?php echo($cont_fila); ?>">
                            <td align="center" style="width:50px;">
                            	<input type="hidden" id="hdd_id_rips_us_<?php echo($cont_fila); ?>" value="<?php echo($rips_us_aux["id_rips_us"]); ?>" />
                            	<input type="hidden" id="hdd_id_paciente_us_<?php echo($cont_fila); ?>" value="<?php echo($rips_us_aux["id_paciente"]); ?>" />
                                <input type="hidden" id="hdd_con_datos_us_<?php echo($cont_fila); ?>" value="1" />
								<?php echo($cont_fila + 1); ?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="checkbox" id="chk_revisado_us_<?php echo($cont_fila); ?>" style="margin:auto;"<?php if ($rips_us_aux["ind_revisado"] == "1") { ?> checked="checked"<?php } ?> />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="checkbox" id="chk_borrado_us_<?php echo($cont_fila); ?>" style="margin:auto;"<?php if ($rips_us_aux["ind_borrado"] == "1") { ?> checked="checked"<?php } ?> />
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$combo->getComboDb("cmb_tipo_documento_us_".$cont_fila, $rips_us_aux["tipo_documento"], $lista_tipos_documento, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_numero_documento_us_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_us_aux["numero_documento"]); ?>" onkeypress="return solo_alfanumericos(event);" />
                            </td>
                            <td align="center" style="width:150px;">
                            	<?php
                                	$nombre_1_aux = substr(str_replace(",", "", $rips_us_aux["nombre_1"]), 0, 20);
								?>
                            	<input type="text" id="txt_nombre_1_us_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="<?php echo($nombre_1_aux); ?>" onblur="trim_cadena(this);" />
                            </td>
                            <td align="center" style="width:150px;">
                            	<?php
                                	$nombre_2_aux = substr(str_replace(",", "", $rips_us_aux["nombre_2"]), 0, 20);
								?>
                            	<input type="text" id="txt_nombre_2_us_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="<?php echo($nombre_2_aux); ?>" onblur="trim_cadena(this);" />
                            </td>
                            <td align="center" style="width:150px;">
                            	<?php
                                	$apellido_1_aux = substr(str_replace(",", "", $rips_us_aux["apellido_1"]), 0, 30);
								?>
                            	<input type="text" id="txt_apellido_1_us_<?php echo($cont_fila); ?>" maxlength="30" class="input_sin_margen" style="width:100%;" value="<?php echo($apellido_1_aux); ?>" onblur="trim_cadena(this);" />
                            </td>
                            <td align="center" style="width:150px;">
                            	<?php
                                	$apellido_2_aux = substr(str_replace(",", "", $rips_us_aux["apellido_2"]), 0, 30);
								?>
                            	<input type="text" id="txt_apellido_2_us_<?php echo($cont_fila); ?>" maxlength="30" class="input_sin_margen" style="width:100%;" value="<?php echo($apellido_2_aux); ?>" onblur="trim_cadena(this);" />
                            </td>
                            <td align="center" class="td_reducido" style="width:100px;">
                            	<?php echo($convenio_obj["cod_administradora"]); ?>
                            </td>
                            <td align="center" style="width:200px;">
                            	<?php
                                	$combo->getComboDb("cmb_tipo_usuario_us_".$cont_fila, $rips_us_aux["tipo_usuario"], $lista_tipos_usuarios, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <?php
                            	$edad_aux = $rips_us_aux["edad"];
                            	$unidad_edad_aux = $rips_us_aux["unidad_edad"];
								if ($tipo_carga == 2) {
									$arr_edad_aux = explode("/", $rips_us_aux["edad2"]);
									@$edad_aux = $arr_edad_aux[0];
									@$unidad_edad_aux = $arr_edad_aux[1];
								}
							?>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_edad_us_<?php echo($cont_fila); ?>" maxlength="3" class="input_sin_margen" style="width:100%;" value="<?php echo($edad_aux); ?>" onkeypress="return solo_numeros(event, false);" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<?php
                                	$combo->getComboDb("cmb_unidad_edad_us_".$cont_fila, $unidad_edad_aux, $lista_unidades_edad, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>

							<td align="center" style="width:100px;">
                            	
								<input type="text" id="txt_fecha_nacimiento_us_<?php echo($cont_fila); ?>" maxlength="3" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_us_aux['fecha_nacimiento']); ?>" disabled " />
							
                            </td>

							<td align="center" style="width:150px;">
                            	<?php
                                	$combo->getComboDb("cmb_sexo_us_".$cont_fila, $rips_us_aux["sexo"], $lista_sexos, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>

							<td align="center" style="width:200px;">
								<input type="hidden" id="txt_cod_pais_us_<?php echo($cont_fila); ?>" maxlength="3" class="input_sin_margen" value="<?php echo($rips_us_aux['cod_pais']); ?>"  />
                            	<?php
                                	$combo->getComboDb("cmb_cod_pais_us_".$cont_fila, $rips_us_aux["id_pais"], $lista_paises, "id_pais, nombre_pais", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>

                            <td align="center" style="width:200px;">
                            	<?php
                                	$combo->getComboDb("cmb_cod_dep_us_".$cont_fila, $rips_us_aux["cod_dep"], $lista_departamentos, "cod_dep, nom_dep", "&nbsp;", "seleccionar_departamento_us(this.value, ".$cont_fila.");", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>

                            <td align="center" style="width:200px;">
                            	<div id="d_municipios_us_<?php echo($cont_fila); ?>">
									<?php
										$lista_municipios = $db_dep_muni->getMunicipiosDepartamento($rips_us_aux["cod_dep"]);
										
                                        $combo->getComboDb("cmb_cod_mun_us_".$cont_fila, $rips_us_aux["cod_mun"], $lista_municipios, "cod_mun, nom_mun", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
                                    ?>
                                </div>
                            </td>
                            <td align="center" style="width:150px;">
                            	<?php
                                	$combo->getComboDb("cmb_zona_us_".$cont_fila, $rips_us_aux["zona"], $lista_zonas, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:400px;">
                            	<input type="text" id="txt_observaciones_us_<?php echo($cont_fila); ?>" maxlength="200" class="input_sin_margen" style="width:100%;" value="<?php echo($rips_us_aux["observaciones"]); ?>" />
                            </td>
                        </tr>
                        <?php
									$cont_fila++;
								}
							}
						?>
                        <tr style="display:none;">
                        	<td>
                            	<input type="hidden" id="hdd_cant_registros_us" value="<?php echo($cont_fila); ?>" />
                            </td>
                        </tr>
                        <?php
							for ($i = 0; $i < 100; $i++) {
						?>
                        <tr id="tr_reg_us_<?php echo($cont_fila); ?>" style="display:none;">
                            <td align="center" style="width:50px;">
                            	<input type="hidden" id="hdd_id_rips_us_<?php echo($cont_fila); ?>" value="0" />
                            	<input type="hidden" id="hdd_id_paciente_us_<?php echo($cont_fila); ?>" value="" />
                                <input type="hidden" id="hdd_con_datos_us_<?php echo($cont_fila); ?>" value="0" />
								<?php echo($cont_fila + 1); ?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="checkbox" id="chk_revisado_us_<?php echo($cont_fila); ?>" style="margin:auto;" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="checkbox" id="chk_borrado_us_<?php echo($cont_fila); ?>" style="margin:auto;" />
                            </td>
                            <td align="center" style="width:250px;">
                            	<?php
                                	$combo->getComboDb("cmb_tipo_documento_us_".$cont_fila, "", $lista_tipos_documento, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_numero_documento_us_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_alfanumericos(event);" />
                            </td>
                            <td align="center" style="width:150px;">
                            	<input type="text" id="txt_nombre_1_us_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="" onblur="trim_cadena(this);" />
                            </td>
                            <td align="center" style="width:150px;">
                            	<input type="text" id="txt_nombre_2_us_<?php echo($cont_fila); ?>" maxlength="20" class="input_sin_margen" style="width:100%;" value="" onblur="trim_cadena(this);" />
                            </td>
                            <td align="center" style="width:150px;">
                            	<input type="text" id="txt_apellido_1_us_<?php echo($cont_fila); ?>" maxlength="30" class="input_sin_margen" style="width:100%;" value="" onblur="trim_cadena(this);" />
                            </td>
                            <td align="center" style="width:150px;">
                            	<input type="text" id="txt_apellido_2_us_<?php echo($cont_fila); ?>" maxlength="30" class="input_sin_margen" style="width:100%;" value="" onblur="trim_cadena(this);" />
                            </td>
                            <td align="center" class="td_reducido" style="width:100px;">
                            	<?php echo($convenio_obj["cod_administradora"]); ?>
                            </td>
                            <td align="center" style="width:200px;">
                            	<?php
									$tipo_usuario_aux = "";
									if (isset($plan_obj["cod_tipo_usuario"])) {
	                                	$tipo_usuario_aux = $plan_obj["cod_tipo_usuario"];
									}
									
                                	$combo->getComboDb("cmb_tipo_usuario_us_".$cont_fila, $tipo_usuario_aux, $lista_tipos_usuarios, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:100px;">
                            	<input type="text" id="txt_edad_us_<?php echo($cont_fila); ?>" maxlength="3" class="input_sin_margen" style="width:100%;" value="" onkeypress="return solo_numeros(event, false);" />
                            </td>
                            <td align="center" style="width:100px;">
                            	<?php
                                	$combo->getComboDb("cmb_unidad_edad_us_".$cont_fila, "1", $lista_unidades_edad, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:150px;">
                            	<?php
                                	$combo->getComboDb("cmb_sexo_us_".$cont_fila, "", $lista_sexos, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:200px;">
                            	<?php
                                	$combo->getComboDb("cmb_cod_dep_us_".$cont_fila, "68", $lista_departamentos, "cod_dep, nom_dep", "&nbsp;", "seleccionar_departamento_us(this.value, ".$cont_fila.");", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:200px;">
                            	<div id="d_municipios_us_<?php echo($cont_fila); ?>">
									<?php
										$lista_municipios = $db_dep_muni->getMunicipiosDepartamento("68");
										
                                        $combo->getComboDb("cmb_cod_mun_us_".$cont_fila, "001", $lista_municipios, "cod_mun, nom_mun", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
                                    ?>
                                </div>
                            </td>
                            <td align="center" style="width:150px;">
                            	<?php
                                	$combo->getComboDb("cmb_zona_us_".$cont_fila, "U", $lista_zonas, "codigo_detalle, nombre_detalle", "&nbsp;", "", 1, "width:100%;", "", "select_sin_margen");
								?>
                            </td>
                            <td align="center" style="width:400px;">
                            	<input type="text" id="txt_observaciones_us_<?php echo($cont_fila); ?>" maxlength="200" class="input_sin_margen" style="width:100%;" value="" />
                            </td>
                        </tr>
                        <?php
								$cont_fila++;
							}
						?>
                        <tr>
                        	<td align="left">
                            	<div class="Add-icon" onclick="agregar_registro_us();" title="Agregar registro" style="width:50px;"></div>
                            </td>
                        </tr>
                    </table>
                    </div>
                    <script id="ajax" type="text/javascript">
						contar_registros("us");
					</script>
	<?php
		break;
}
?>
