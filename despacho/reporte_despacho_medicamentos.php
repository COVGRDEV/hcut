<?php
	session_start();
	/*
	  Reporte Despacho de Medicamentos
	  Autor: Jhonatan Mantilla Arenas- 27/08/2019
	 */
	
	require_once("../db/DbVariables.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../db/DbConvenios.php");
	require_once("../db/DbMaestroProcedimientos.php");
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbFormulasMedicamentos.php");

	require_once("../db/DbListas.php");
	
	
	$variables = new DbVariables();
	
	$contenido = new ContenidoHtml();
	$combo = new Combo_Box();
	$convenios = new DbConvenios();
	$maestroProcedimientos = new DbMaestroProcedimientos();
	$usuarios = new DbUsuarios();
	$dbListas = new DbListas();
	$formulasMedicamentos = new DbFormulasMedicamentos();
	
	
	//variables
	$titulo = $variables->getVariable(1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $titulo['valor_variable']; ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
        <link href="../css/azul.css" rel="stylesheet" type="text/css" />
        <link href="../css/foundation-datepicker.css" rel="stylesheet" type="text/css" />
		<link href="http://netdna.bootstrapcdn.com/font-awesome/3.0.2/css/font-awesome.css" rel="stylesheet">
        
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

        <script type='text/javascript' src='reporte_despacho_medicamentos.js'></script>

        <link href="../src/skin-vista/ui.dynatree.css" rel="stylesheet" type="text/css" >
        <script src="../src/jquery.dynatree.js" type="text/javascript"></script>
    </head>
    <body>
        <?php
        $contenido->validar_seguridad(0);
        $contenido->cabecera_html();
		
		//Se obtiene el listado de convenios
		$lista_convenios = $convenios->getConvenios()
        ?>
        <div class="title-bar">
            <div class="wrapper">
                <div class="breadcrumb">
                    <ul>
                        <li class="breadcrumb_on">Reportes de despacho de medicamentos</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="contenedor_principal volumen">
            <div class="padding">     
                <fieldset style="text-align: left;">
                    <legend>Reporte General Despacho de Medicamentos</legend>
                    <div>
                        <table border="0" cellpadding="5" cellspacing="0" style="width:100%;">
                            <tr>
                                <td align="right" style="width:15%;">
                                	<label class="inline">Fecha inicial</label>
                                </td>
                                <td style="width:25%;">
                                    <input type="text" class="input" maxlength="10" style="width:120px;" name="fechaInicial" id="fechaInicial" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                                </td>
                                <td align="right" style="width:15%;">
                                	<label class="inline">Fecha final</label>
                                </td>
                                <td style="width:25%;">
                                    <input type="text" class="input" maxlength="10" style="width:120px;" name="fechaFinal" id="fechaFinal" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                                </td>
                                <td align="center" rowspan="3" style="width:20%;">
                                    <input type="hidden" id="reporte" name="reporte" />   
                                                                    
                                    <input type="button" value="Generar Excel" class="btnPrincipal" onclick="reporteGeneralExcel();" />
                                    <form name="frm_excel_general" id="frm_excel_general" action="reporte_despacho_medicamentos_excel.php" method="post" style="display:none;" target="_blank">
                                        <input type="hidden" id="tipoReporte" name="tipoReporte" value="1" />
                                        <input type="hidden" id="hddfechaInicial" name="hddfechaInicial" />
                                        <input type="hidden" id="hddfechaFinal" name="hddfechaFinal" />
                                        <input type="hidden" id="hddconvenio" name="hddconvenio" />
                                        <input type="hidden" id="hddplan" name="hddplan" />
                                       
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td align="right">
                                	<label class="inline">Convenio</label>
                                </td>
                                <td>
                                    <?php $combo->getComboDb("cmbConvenio", '', $lista_convenios, "id_convenio, nombre_convenio", "Seleccione el convenio o entidad", "seleccionar_convenio(this.value);", "", "width:250px;"); ?>
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
                        </table>
                    </div>
                </fieldset>
            </div>
        </div>
        <script type='text/javascript' src='../js/foundation.min.js'></script>
        <script>
			$(document).foundation();
				$(function() {
					window.prettyPrint && prettyPrint();
					
					$('#fechaInicial').fdatepicker({
						format: 'dd/mm/yyyy'
					});
					$('#fechaFinal').fdatepicker({
						format: 'dd/mm/yyyy'
					});
					$('#txt_fecha_ini_aud').fdatepicker({
						format: 'dd/mm/yyyy'
					});
					$('#txt_fecha_fin_aud').fdatepicker({
						format: 'dd/mm/yyyy'
					});
					$('#fechaInicial2').fdatepicker({
						format: 'dd/mm/yyyy'
					});
					$('#fechaFinal2').fdatepicker({
						format: 'dd/mm/yyyy'
					});
				});
        </script>
        <div id="fondo_negro_pacientes" class="d_fondo_negro"></div>
        <div class="div_centro" id="d_centro_pacientes" style="display:none;">
            <a name="a_cierre_panel" id="a_cierre_panel" href="#" onclick="ventanaPacientes(0);"></a>
            <div class="div_interno" id="d_interno_pacientes"></div>
        </div>
        <div id="fondo_negro_conceptos" class="d_fondo_negro"></div>
        <div class="div_centro" id="d_centro_conceptos" style="display:none;">
            <a name="a_cierre_panel" id="a_cierre_panel" href="#" onclick="mostrar_formulario_conceptos(0);"></a>
            <div class="div_interno" id="d_interno_conceptos"></div>
        </div>
        <?php
        $contenido->footer();
        ?>  
    </body>
</html>