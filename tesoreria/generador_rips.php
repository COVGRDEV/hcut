<?php
	session_start();
	/**
	 * Generación de RIPS
	 * Autor: Feisar Moreno - 15/04/2014
	 */
	
	require_once("../db/DbConvenios.php");
	require_once("../db/DbDatosEntidad.php");
	require_once("../db/DbVariables.php");
	require_once("../principal/ContenidoHtml.php");
	require_once '../funciones/Class_Combo_Box.php';
	
	$dbConvenios = new DbConvenios();
	$dbDatosEntidad = new DbDatosEntidad();
	$dbVariables = new Dbvariables();
	$contenido = new ContenidoHtml();
	$combo = new Combo_Box();
	
	//variables
	$titulo = $dbVariables->getVariable(1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $titulo['valor_variable']; ?></title>
    <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
    <link href="../css/azul.css" rel="stylesheet" type="text/css" />
    <link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
    <link href="../css/foundation-datepicker.css" rel="stylesheet" type="text/css" />
    <link href="http://netdna.bootstrapcdn.com/font-awesome/3.0.2/css/font-awesome.css" rel="stylesheet">
	<link href="../css/jquery.datetimepicker.css" rel="stylesheet" type="text/css" />
    
	<script type='text/javascript' src='../js/jquery.min.js'></script>
    <script type="text/javascript" src="../js/jquery-ui.custom.js"></script>
    <script type="text/javascript" src="../js/jquery.cookie.js"></script>
    <script type='text/javascript' src='../js/jquery.validate.js'></script>
    <script type='text/javascript' src='../js/jquery.validate.add.js'></script>
    <script type='text/javascript' src='../js/ajax.js'></script>
    <script type='text/javascript' src='../js/funciones.js'></script>
    
    <script type='text/javascript' src='../js/validaFecha.js'></script>
    <script type='text/javascript' src='../js/foundation-datepicker.js'></script>
    <script type="text/javascript" src="../js/jquery.maskedinput.js"></script>
    
	<script type='text/javascript' src="../js/jquery.datetimepicker.js" ></script>
    <script type='text/javascript' src='generador_rips_v1.15.js'></script>
    
    <link href="../src/skin-vista/ui.dynatree.css" rel="stylesheet" type="text/css">
    <script src="../src/jquery.dynatree.js" type="text/javascript"></script>
</head>
<body>
	<?php
		$contenido->validar_seguridad(0);
		$contenido->cabecera_html();
	?>
    <div class="title-bar">
    	<div class="wrapper">
        	<div class="breadcrumb">
            	<ul>
                	<li class="breadcrumb_on">Generaci&oacute;n de RIPS</li>
                </ul>
            </div>
        </div>
    </div>
        <div class="contenedor_principal volumen">
            <div class="padding">
                <table style="width: 100%;" border="0">
                    <tr>
                        <td align="right" style="width:12%;">
                            <label class="inline">Convenio:&nbsp;</label>
                        </td>
                        <td align="left" style="width:38%;" colspan="3">
                            <?php
                            	$combo->getComboDb("cmb_convenio", "", $dbConvenios->getListaConveniosActivos(), "id_convenio, nombre_convenio", "Seleccione el convenio", "seleccionar_convenio(this.value, '', '');", "", "width: 280px;");
							?>
                        </td>
                        <td align="right" style="width:12%;">
                            <label class="inline">Plan:&nbsp;</label>
                        </td>
                        <td align="left" style="width:38%;">
                        	<div id="d_planes">
	                        	<select name="cmb_plan" id="cmb_plan" style="width:280px;">
    	                        	<option value="">Todos los planes</option>
        	                    </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">
                            <label class="inline">Fecha inicial:&nbsp;</label>
                        </td>
                        <td align="left" style="width:13%;">
                            <input type="text" class="input" maxlength="10" style="width:120px;" name="txt_fecha_inicial" id="txt_fecha_inicial" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                        </td>
                        <td align="right" style="width:9%;">
                            <label class="inline">Fecha final:&nbsp;</label>
                        </td>
                        <td align="left" style="width:16%;">
                            <input type="text" class="input" maxlength="10" style="width:120px;" name="txt_fecha_final" id="txt_fecha_final" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                        </td>
                        <td align="right">
                        	<label class="inline">Tipo de factura:&nbsp;</label>
                        </td>
                        <td align="left">
                        	<select name="cmb_tipo_factura" id="cmb_tipo_factura" style="width:280px;">
                            	<option value="" selected="selected">Selecione el tipo</option>
                                <option value="1">Factura &uacute;nica</option>
                                <option value="2">Factura por atenci&oacute;n</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                    	<td align="right">
                        	<label class="inline">Entidad:&nbsp;</label>
                        </td>
                        <td align="left">
                        	<?php
                            	$lista_datos_entidad = $dbDatosEntidad->getListaDatosEntidad();
								
								$combo->getComboDb("cmb_prestador", "", $lista_datos_entidad, "id_prestador, sigla_prestador", "", "", "", "width: 100px;");
							?>
                        </td>
                        <td align="left" colspan="2">
							<table border="0" cellpadding="0" cellspacing="3" align="left" style="width:100%;">
                            	<tr>
                                	<td align="right" style="width:70%;">
                                    	<label class="inline">Excluir atenciones NP:&nbsp;</label>
                                    </td>
                                    <td align="left" style="width:30%;">
                                    	<input type="checkbox" name="chk_sin_np" id="chk_sin_np" />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    	<td align="left" colspan="2">
							<table border="0" cellpadding="0" cellspacing="3" align="left" style="width:56%;">
                            	<tr>
                                	<td align="right" style="width:90%;">
                                    	<label class="inline">Generar nuevamente RIPS existentes:&nbsp;</label>
                                    </td>
                                    <td align="left" style="width:10%;">
                                    	<input type="checkbox" name="chk_rips_existentes" id="chk_rips_existentes" />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="6" id="td_rips">
                        	<?php
                            	//Se obtiene el listados de archivos del RIPS configurados para generación
								$var_arch_rips = $dbVariables->getVariable(9);
								
								$arch_rips = ";".$var_arch_rips["valor_variable"].";";
								
								$arr_arch_rips = array();
								$arr_arch_rips["CT"] = strpos($arch_rips, ";CT;") === false ? false : true;
								$arr_arch_rips["AF"] = strpos($arch_rips, ";AF;") === false ? false : true;
								$arr_arch_rips["US"] = strpos($arch_rips, ";US;") === false ? false : true;
								$arr_arch_rips["AD"] = strpos($arch_rips, ";AD;") === false ? false : true;
								$arr_arch_rips["AC"] = strpos($arch_rips, ";AC;") === false ? false : true;
								$arr_arch_rips["AP"] = strpos($arch_rips, ";AP;") === false ? false : true;
								$arr_arch_rips["AH"] = strpos($arch_rips, ";AH;") === false ? false : true;
								$arr_arch_rips["AU"] = strpos($arch_rips, ";AU;") === false ? false : true;
								$arr_arch_rips["AN"] = strpos($arch_rips, ";AN;") === false ? false : true;
								$arr_arch_rips["AM"] = strpos($arch_rips, ";AM;") === false ? false : true;
								$arr_arch_rips["AT"] = strpos($arch_rips, ";AT;") === false ? false : true;
								
								if ($arr_arch_rips["AC"]) {
									$visible_aux = "";
									$checked_aux = " checked=\"checked\"";
								} else {
									$visible_aux = " display:none;";
									$checked_aux = "";
								}
							?>
                        	<div class="div_en_linea" style="width:19%;<?php echo($visible_aux) ?>">
                            	<table border="0" cellpadding="0" cellspacing="3" style="width:100%;">
                                	<tr>
                                    	<td align="right" style="width:75%;">
			                            	<label class="inline">Consultas (AC):&nbsp;</label>
                                        </td>
                                        <td align="left" style="width:25%;">
                                        	<input type="checkbox" name="chk_ac" id="chk_ac"<?php echo($checked_aux); ?> />
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <?php
								if ($arr_arch_rips["AH"]) {
									$visible_aux = "";
									$checked_aux = " checked=\"checked\"";
								} else {
									$visible_aux = " display:none;";
									$checked_aux = "";
								}
							?>
                        	<div class="div_en_linea" style="width:19%;<?php echo($visible_aux) ?>">
                            	<table border="0" cellpadding="0" cellspacing="3" style="width:100%;">
                                	<tr>
                                    	<td align="right" style="width:75%;">
			                            	<label class="inline">Hospitalizaci&oacute;n (AH):&nbsp;</label>
                                        </td>
                                        <td align="left" style="width:25%;">
                                        	<input type="checkbox" name="chk_ah" id="chk_ah"<?php echo($checked_aux); ?> />
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <?php
								if ($arr_arch_rips["AU"]) {
									$visible_aux = "";
									$checked_aux = " checked=\"checked\"";
								} else {
									$visible_aux = " display:none;";
									$checked_aux = "";
								}
							?>
                        	<div class="div_en_linea" style="width:19%;<?php echo($visible_aux) ?>">
                            	<table border="0" cellpadding="0" cellspacing="3" style="width:100%;">
                                	<tr>
                                    	<td align="right" style="width:75%;">
			                            	<label class="inline">Urgencias (AU):&nbsp;</label>
                                        </td>
                                        <td align="left" style="width:25%;">
                                        	<input type="checkbox" name="chk_au" id="chk_au"<?php echo($checked_aux); ?> />
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <?php
								if ($arr_arch_rips["AP"]) {
									$visible_aux = "";
									$checked_aux = " checked=\"checked\"";
								} else {
									$visible_aux = " display:none;";
									$checked_aux = "";
								}
							?>
                        	<div class="div_en_linea" style="width:19%;<?php echo($visible_aux) ?>">
                            	<table border="0" cellpadding="0" cellspacing="3" style="width:100%;">
                                	<tr>
                                    	<td align="right" style="width:75%;">
			                            	<label class="inline">Procedimientos (AP):&nbsp;</label>
                                        </td>
                                        <td align="left" style="width:25%;">
                                        	<input type="checkbox" name="chk_ap" id="chk_ap"<?php echo($checked_aux); ?> />
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <?php
								if ($arr_arch_rips["AM"]) {
									$visible_aux = "";
									$checked_aux = " checked=\"checked\"";
								} else {
									$visible_aux = " display:none;";
									$checked_aux = "";
								}
							?>
                        	<div class="div_en_linea" style="width:19%;<?php echo($visible_aux) ?>">
                            	<table border="0" cellpadding="0" cellspacing="3" style="width:100%;">
                                	<tr>
                                    	<td align="right" style="width:75%;">
			                            	<label class="inline">Medicamentos (AM):&nbsp;</label>
                                        </td>
                                        <td align="left" style="width:25%;">
                                        	<input type="checkbox" name="chk_am" id="chk_am"<?php echo($checked_aux); ?> />
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <?php
								if ($arr_arch_rips["AT"]) {
									$visible_aux = "";
									$checked_aux = " checked=\"checked\"";
								} else {
									$visible_aux = " display:none;";
									$checked_aux = "";
								}
							?>
                        	<div class="div_en_linea" style="width:19%;<?php echo($visible_aux) ?>">
                            	<table border="0" cellpadding="0" cellspacing="3" style="width:100%;">
                                	<tr>
                                    	<td align="right" style="width:75%;">
			                            	<label class="inline">Otros servicios (AT):&nbsp;</label>
                                        </td>
                                        <td align="left" style="width:25%;">
                                        	<input type="checkbox" name="chk_at" id="chk_at"<?php echo($checked_aux); ?> />
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <?php
								if ($arr_arch_rips["AN"]) {
									$visible_aux = "";
									$checked_aux = " checked=\"checked\"";
								} else {
									$visible_aux = " display:none;";
									$checked_aux = "";
								}
							?>
                        	<div class="div_en_linea" style="width:19%;<?php echo($visible_aux) ?>">
                            	<table border="0" cellpadding="0" cellspacing="3" style="width:100%;">
                                	<tr>
                                    	<td align="right" style="width:75%;">
			                            	<label class="inline">Reci&eacute;n nacidos (AN):&nbsp;</label>
                                        </td>
                                        <td align="left" style="width:25%;">
                                        	<input type="checkbox" name="chk_an" id="chk_an"<?php echo($checked_aux); ?> />
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <?php
								if ($arr_arch_rips["US"]) {
									$visible_aux = "";
									$checked_aux = " checked=\"checked\"";
								} else {
									$visible_aux = " display:none;";
									$checked_aux = "";
								}
							?>
                        	<div class="div_en_linea" style="width:19%;<?php echo($visible_aux) ?>">
                            	<table border="0" cellpadding="0" cellspacing="3" style="width:100%;">
                                	<tr>
                                    	<td align="right" style="width:75%;">
			                            	<label class="inline">Usuarios (US):&nbsp;</label>
                                        </td>
                                        <td align="left" style="width:25%;">
                                        	<input type="checkbox" name="chk_us" id="chk_us"<?php echo($checked_aux); ?> />
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <?php
								if ($arr_arch_rips["AF"]) {
									$visible_aux = "";
									$checked_aux = " checked=\"checked\"";
								} else {
									$visible_aux = " display:none;";
									$checked_aux = "";
								}
							?>
                        	<div class="div_en_linea" style="width:19%;<?php echo($visible_aux) ?>">
                            	<table border="0" cellpadding="0" cellspacing="3" style="width:100%;">
                                	<tr>
                                    	<td align="right" style="width:75%;">
			                            	<label class="inline">Transacciones (AF):&nbsp;</label>
                                        </td>
                                        <td align="left" style="width:25%;">
                                        	<input type="checkbox" name="chk_af" id="chk_af"<?php echo($checked_aux); ?> />
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <?php
								if ($arr_arch_rips["AD"]) {
									$visible_aux = "";
									$checked_aux = " checked=\"checked\"";
								} else {
									$visible_aux = " display:none;";
									$checked_aux = "";
								}
							?>
                        	<div class="div_en_linea" style="width:19%;<?php echo($visible_aux) ?>">
                            	<table border="0" cellpadding="0" cellspacing="3" style="width:100%;">
                                	<tr>
                                    	<td align="right" style="width:75%;">
			                            	<label class="inline">Descripci&oacute;n (AD):&nbsp;</label>
                                        </td>
                                        <td align="left" style="width:25%;">
                                        	<input type="checkbox" name="chk_ad" id="chk_ad"<?php echo($checked_aux); ?> />
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <?php
								if ($arr_arch_rips["CT"]) {
									$visible_aux = "";
									$checked_aux = " checked=\"checked\"";
								} else {
									$visible_aux = " display:none;";
									$checked_aux = "";
								}
							?>
                        	<div class="div_en_linea" style="width:19%;<?php echo($visible_aux) ?>">
                            	<table border="0" cellpadding="0" cellspacing="3" style="width:100%;">
                                	<tr>
                                    	<td align="right" style="width:75%;">
			                            	<label class="inline">Control (CT):&nbsp;</label>
                                        </td>
                                        <td align="left" style="width:25%;">
                                        	<input type="checkbox" name="chk_ct" id="chk_ct"<?php echo($checked_aux); ?> />
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <input type="button" name="btn_cargar_datos" id="btn_cargar_datos" class="btnPrincipal" value="Cargar Datos" onclick="confirmar_cargar_datos_rips();" />
                            
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="button" name="btn_generar_rips" id="btn_generar_rips" class="btnPrincipal" value="Generar Archivos del RIPS" onclick="confirmar_generar_rips_directo();" />
                           
                        </td>
                        
                    </tr>
                </table>
                <div id="d_cargar_datos_rips"></div>
                <div id="d_generar_rips" style="display:none;"></div>
                <div id="d_generando_rips" style="display:none;">
                	<img src="../imagenes/ajax-loader.gif"/>
                    Generando archivos
                    <br /><br />
                </div>
                <div id="d_descargar_rips"></div>
            </div>
        </div>
        <script type='text/javascript' src='../js/foundation.min.js'></script>
        <script>
			cargar_rips_disponibles();
			
            $(document).foundation();

            $(function() {
                window.prettyPrint && prettyPrint();

                $('#txt_fecha_inicial').fdatepicker({
                    format: 'dd/mm/yyyy'
                });
                $('#txt_fecha_final').fdatepicker({
                    format: 'dd/mm/yyyy'
                });

            });
        </script>
        <?php
        $contenido->footer();
        ?>  
    </body>
</html>