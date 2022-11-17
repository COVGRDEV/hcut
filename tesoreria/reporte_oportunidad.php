<?php
	session_start();
	
	require_once("../db/DbVariables.php");
	require_once("../db/DbConvenios.php");
	require_once("../funciones/Utilidades.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Class_Combo_Box.php");
	
	$variables = new DbVariables();
	$utilidades = new Utilidades();
	$contenido = new ContenidoHtml();
	$combo = new Combo_Box();
	$convenios = new DbConvenios();
	
	//variables
	$titulo = $variables->getVariable(1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $titulo["valor_variable"]; ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
        <link href="../css/azul.css" rel="stylesheet" type="text/css" />
        <link href="../css/foundation-datepicker.css" rel="stylesheet" type="text/css" />
        <link href="http://netdna.bootstrapcdn.com/font-awesome/3.0.2/css/font-awesome.css" rel="stylesheet">
        
        <script type="text/javascript" src="../js/jquery.min.js"></script>
        <script type="text/javascript" src="../js/jquery-ui.custom.js"></script>
        <script type="text/javascript" src="../js/jquery.cookie.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.add.js"></script>
        <script type="text/javascript" src="../js/ajax.js"></script>
        <script type="text/javascript" src="../js/funciones.js"></script>
        
        <script type="text/javascript" src="../js/validaFecha.js"></script>
        <script type="text/javascript" src="../js/foundation-datepicker.js"></script>
        <script type="text/javascript" src="../js/jquery.maskedinput.js"></script>
		
        <script type="text/javascript" src="reporte_oportunidad_v1.5.js"></script>
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
                        <li class="breadcrumb_on">Reportes para entidades</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="contenedor_principal volumen">
            <div class="padding">
                <div id="advertenciasg">
                    <div class="contenedor_error" id="contenedor_error"></div>
                    <div class="contenedor_exito" id="contenedor_exito"></div>
                </div>
                <fieldset style="text-align: left;">
                    <legend>Reporte de oportunidad</legend>
                    <table style="width: 100%;">
                        <tr>
                            <td align="right" style="width:15%;">
                                <label>Fecha inicial&nbsp;:&nbsp;</label>                            
                            </td>
                            <td align="left" style="width:35%;">
                                <input type="text" class="input" maxlength="10" style="width:120px;" name="fechaInicial" id="fechaInicial" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                            </td>
                            <td align="right" style="width:15%;">
                                <label>Fecha final&nbsp;:&nbsp;</label>
                            </td>
                            <td align="left" style="width:35%;">
                                <input type="text" class="input" maxlength="10" style="width:120px;" name="fechaFinal" id="fechaFinal" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                                <label>Convenio&nbsp;:&nbsp;</label>                            
                            </td>
                            <td align="left">
                                <?php
								 $lista_convenios_aux = $convenios->getConvenios();
								 $combo->getComboDb("cmb_convenio", '', $lista_convenios_aux, "id_convenio, nombre_convenio", "Seleccione el convenio o entidad", "seleccionar_convenio(this.value);", "", "width:250px;"); ?>
								
                            </td>
                            <td align="right">
                                	<label class="inline">Plan</label>
                                </td>
                                <td>
                                	<div id="d_plan">
                                    <?php $combo->getComboDb("cmbPlan", '', array(), "id_plan, nombre_plan", "Todos los planes", "", "", "width:250px;"); ?>
                                    </div>
                                </td>
                            
                        </tr>
  							<td colspan="2" align="center">
                                <input type="button" id="btnGenerar" name="btnGenerar" class="btnPrincipal" value="Generar Excel" onclick="generarInforme();" />
                            </td>
                    </table>
                    <form id="frmReporteOportunidad2" name="frmReporteOportunidad2" action="reporte_oportunidad_excel.php" method="post" style="display:none;" target="_blank"> 
                        <input type="hidden" id="fechaInicial2" name="fechaInicial2" />
                        <input type="hidden" id="fechaFinal2" name="fechaFinal2" />
                        <input type="hidden" id="convenio2" name="convenio2" />
                        <input type="hidden" id="plan" name="plan" />
                        <input type="hidden" id="tipoReporte" name="tipoReporte" value="1" />
                    </form>
                </fieldset>
                <fieldset style="text-align: left;">
                    <legend>Reporte Colpatria 1</legend>
                    <table style="width: 100%;">
                        <tr>
                            <td align="right" style="width:15%;">
                                <label>Fecha inicial&nbsp;:&nbsp;</label>                            
                            </td>
                            <td align="left" style="width:35%;">
                                <input type="text" class="input" maxlength="10" style="width:120px;" name="txt_fecha_ini_colpatria" id="txt_fecha_ini_colpatria" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                            </td>
                            <td align="right" style="width:15%;">
                                <label>Fecha final&nbsp;:&nbsp;</label>
                            </td>
                            <td align="left" style="width:35%;">
                                <input type="text" class="input" maxlength="10" style="width:120px;" name="txt_fecha_fin_colpatria" id="txt_fecha_fin_colpatria" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                                <label>Convenio&nbsp;:&nbsp;</label>                            
                            </td>
                            <td align="left">
                                <?php
									$lista_convenios_aux = $convenios->getListaConveniosActivos();
									$combo->getComboDb("cmb_convenio_colpatria", 23, $lista_convenios_aux, "id_convenio, nombre_convenio", "--Seleccione el convenio--", "", "", "width:250px;");
								?>
                            </td>
                            <td colspan="2" align="center">
                            	<input type="button" id="btn_generar_colpatria_excel" name="btn_generar_colpatria_excel" class="btnPrincipal" value="Generar Excel" onclick="generar_reporte_colpatria_excel();" />
                                &nbsp;&nbsp;
                                <input type="button" id="btn_generar_colpatria" name="btn_generar_colpatria" class="btnPrincipal" value="Generar PDF" onclick="generar_reporte_colpatria();" />
                            </td>
                        </tr>
                    </table>
                    <form id="frmReporteColpatriaExcel" name="frmReporteColpatriaExcel" action="reporte_oportunidad_excel.php" method="post" style="display:none;" target="_blank"> 
                        <input type="hidden" id="hdd_fecha_ini_colpatria_1" name="hdd_fecha_ini_colpatria_1" />
                        <input type="hidden" id="hdd_fecha_fin_colpatria_1" name="hdd_fecha_fin_colpatria_1" />
                        <input type="hidden" id="hdd_convenio_colpatria_1" name="hdd_convenio_colpatria_1" />
                        <input type="hidden" id="tipoReporte" name="tipoReporte" value="3" />
                    </form>
                </fieldset>
                <fieldset style="text-align: left;">
                    <legend>Reporte Colpatria 2</legend>
                    <table style="width: 100%;">
                        <tr>
                            <td align="right" style="width:15%;">
                                <label>Fecha inicial&nbsp;:&nbsp;</label>                            
                            </td>
                            <td align="left" style="width:35%;">
                                <input type="text" class="input" maxlength="10" style="width:120px;" name="txt_fecha_ini_colpatria_2" id="txt_fecha_ini_colpatria_2" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                            </td>
                            <td align="right" style="width:15%;">
                                <label>Fecha final&nbsp;:&nbsp;</label>
                            </td>
                            <td align="left" style="width:35%;">
                                <input type="text" class="input" maxlength="10" style="width:120px;" name="txt_fecha_fin_colpatria_2" id="txt_fecha_fin_colpatria_2" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                                <label>Convenio&nbsp;:&nbsp;</label>                            
                            </td>
                            <td align="left">
                                <?php
									$lista_convenios_aux = $convenios->getListaConveniosActivos();
									$combo->getComboDb("cmb_convenio_colpatria_2", 23, $lista_convenios_aux, "id_convenio, nombre_convenio", "--Seleccione el convenio--", "", "", "width:250px;");
								?>
                            </td>
                            <td colspan="2" align="center">
                            	<input type="button" id="btn_generar_colpatria_excel_2" name="btn_generar_colpatria_excel_2" class="btnPrincipal" value="Generar Excel" onclick="generar_reporte_colpatria_excel_2();" />
                            </td>
                        </tr>
                    </table>
                    <form id="frmReporteColpatriaExcel2" name="frmReporteColpatriaExcel2" action="reporte_oportunidad_excel.php" method="post" style="display:none;" target="_blank"> 
                        <input type="hidden" id="hdd_fecha_ini_colpatria_2" name="hdd_fecha_ini_colpatria_2" />
                        <input type="hidden" id="hdd_fecha_fin_colpatria_2" name="hdd_fecha_fin_colpatria_2" />
                        <input type="hidden" id="hdd_convenio_colpatria_2" name="hdd_convenio_colpatria_2" />
                        <input type="hidden" id="tipoReporte" name="tipoReporte" value="4" />
                    </form>
                </fieldset>
                <fieldset style="text-align: left;">
                    <legend>Reporte Colm&eacute;dica</legend>
                    <table style="width: 100%;">
                        <tr>
                            <td align="right" style="width:15%;">
                                <label>Fecha inicial&nbsp;:&nbsp;</label>                            
                            </td>
                            <td align="left" style="width:35%;">
                                <input type="text" class="input" maxlength="10" style="width:120px;" name="txt_fecha_ini_colmedica" id="txt_fecha_ini_colmedica" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                            </td>
                            <td align="right" style="width:15%;">
                                <label>Fecha final&nbsp;:&nbsp;</label>
                            </td>
                            <td align="left" style="width:35%;">
                                <input type="text" class="input" maxlength="10" style="width:120px;" name="txt_fecha_fin_colmedica" id="txt_fecha_fin_colmedica" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                                <label>Convenio&nbsp;:&nbsp;</label>                            
                            </td>
                            <td align="left">
                                <?php
									$lista_convenios_aux = $convenios->getListaConveniosActivos();
									$combo->getComboDb("cmb_convenio_colmedica", 6, $lista_convenios_aux, "id_convenio, nombre_convenio", "--Seleccione el convenio--", "", "", "width:250px;");
								?>
                            </td>
                            <td colspan="2" align="center">
                                <input type="button" id="btn_generar_colmedica" name="btn_generar_colmedica" class="btnPrincipal" value="Generar PDF" onclick="generar_reporte_colmedica();" />
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <fieldset style="text-align: left;">
                    <legend>Reporte FOSCAL</legend>
                    <table style="width: 100%;">
                        <tr>
                            <td align="right" style="width:15%;">
                                <label>Fecha inicial&nbsp;:&nbsp;</label>                            
                            </td>
                            <td align="left" style="width:35%;">
                                <input type="text" class="input" maxlength="10" style="width:120px;" name="txt_fecha_ini_foscal" id="txt_fecha_ini_foscal" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                            </td>
                            <td align="right" style="width:15%;">
                                <label>Fecha final&nbsp;:&nbsp;</label>
                            </td>
                            <td align="left" style="width:35%;">
                                <input type="text" class="input" maxlength="10" style="width:120px;" name="txt_fecha_fin_foscal" id="txt_fecha_fin_foscal" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                                <label>Convenio&nbsp;:&nbsp;</label>                            
                            </td>
                            <td align="left">
                                <?php
									$lista_convenios_aux = $convenios->getListaConveniosActivos();
									$combo->getComboDb("cmb_convenio_foscal", 17, $lista_convenios_aux, "id_convenio, nombre_convenio", "--Seleccione el convenio--", "", "", "width:250px;");
								?>
                            </td>
                            <td colspan="2" align="center">
                                <input type="button" id="btn_generar_foscal" name="btn_generar_foscal" class="btnPrincipal" value="Generar Excel" onclick="generar_reporte_foscal();" />
                            </td>
                        </tr>
                    </table>
                    <form id="frm_reporte_foscal" name="frm_reporte_foscal" action="reporte_oportunidad_excel.php" method="post" style="display:none;" target="_blank"> 
                        <input type="hidden" id="hdd_fecha_ini_foscal" name="hdd_fecha_ini_foscal" />
                        <input type="hidden" id="hdd_fecha_fin_foscal" name="hdd_fecha_fin_foscal" />
                        <input type="hidden" id="hdd_convenio_foscal" name="hdd_convenio_foscal" />
                        <input type="hidden" id="tipoReporte" name="tipoReporte" value="2" />
                    </form>
                </fieldset>
                <div id="principal"></div>
            </div>
        </div>
        <div id="d_reporte" style="display:none;"></div>
        <script type="text/javascript" src="../js/foundation.min.js"></script>
        <script>
			$(document).foundation();
			$(function() {
				window.prettyPrint && prettyPrint();
				
				$("#fechaInicial").fdatepicker({
					format: "dd/mm/yyyy"
				});
				$("#fechaFinal").fdatepicker({
					format: "dd/mm/yyyy"
				});
				$("#txt_fecha_ini_colpatria").fdatepicker({
					format: "dd/mm/yyyy"
				});
				$("#txt_fecha_fin_colpatria").fdatepicker({
					format: "dd/mm/yyyy"
				});
				$("#txt_fecha_ini_colpatria_2").fdatepicker({
					format: "dd/mm/yyyy"
				});
				$("#txt_fecha_fin_colpatria_2").fdatepicker({
					format: "dd/mm/yyyy"
				});
				$("#txt_fecha_ini_colmedica").fdatepicker({
					format: "dd/mm/yyyy"
				});
				$("#txt_fecha_fin_colmedica").fdatepicker({
					format: "dd/mm/yyyy"
				});
				$("#txt_fecha_ini_foscal").fdatepicker({
					format: "dd/mm/yyyy"
				});
				$("#txt_fecha_fin_foscal").fdatepicker({
					format: "dd/mm/yyyy"
				});
			});
        </script>
        <?php
			$contenido->footer();
        ?>  
    </body>
</html>
