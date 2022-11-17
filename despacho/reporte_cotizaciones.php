<?php
	session_start();
	/*
	  Pagina para consultar el reporte de cotizaciones
	  Autor: Feisar Moreno - 14/12/2015
	 */
	require_once("../db/DbVariables.php");
	require_once("../db/DbProcedimientosCotizaciones.php");
	require_once("../db/DbAsignarCitas.php");
	require_once("../db/DbListas.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/Utilidades.php");
	
	$dbVariables = new Dbvariables();
	$dbProcedimientosCotizaciones = new DbProcedimientosCotizaciones();
	$dbAsignarCitas = new DbAsignarCitas();
	$dbListas = new DbListas();
	
	$contenido = new ContenidoHtml();
	$combo = new Combo_Box();
	$utilidades = new Utilidades();
	
	//variables
	$titulo = $dbVariables->getVariable(1);
	$horas_edicion = $dbVariables->getVariable(7);
	
	//Cambiar las variables get a post
	$utilidades->get_a_post();
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
        <script type="text/javascript" src="reporte_cotizaciones_v1.1.js"></script>
    </head>
    <body>
        <?php
			$contenido->validar_seguridad(0);
			$contenido->cabecera_html();
			
			//Se obtiene el listado de procedimientos de cotizaciones
			$lista_proc_cotiz = $dbProcedimientosCotizaciones->getListaProcedimientosCotizaciones(1);
			
			//Se obtiene el listado de usuarios profesionales
			$lista_usuarios_prof = $dbAsignarCitas->getListaUsuariosCitas("", -1, 0);
			
			//Se obtiene el listado de lugares de atención
			$lista_lugares = $dbListas->getListaDetalles(12);
        ?>
        <div class="title-bar">
            <div class="wrapper">
                <div class="breadcrumb">
                    <ul>
                        <li class="breadcrumb_on">Reporte de cotizaciones</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="contenedor_principal">
            <div style="width:100%;">
                <div class="contenedor_error" id="contenedor_error"></div>
                <div class="contenedor_exito" id="contenedor_exito"></div>
            </div>
            <div class="formulario" id="principal_historia_clinica" style="width: 100%; display: block; ">
                <form id="frm_historia_clinica" name="frm_historia_clinica" method="post">
                    <table id="buscar" border="0" style="width: 100%;">
                        <tr>
                        	<td colspan="6"> <h4>Buscar registros de cotizaci&oacute;n</h4></td>
                        </tr>
                        <tr>
                        	<td align="right" style="widows:12%;">
                            	<label class="inline">Fecha inicial*:&nbsp;</label>
                            </td>
                            <td align="left" style="width:13%;">
                            	<input type="text" class="input" maxlength="10" style="width:120px;" name="txt_fecha_ini_b" id="txt_fecha_ini_b" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                            </td>
                        	<td align="right" style="widows:12%;">
                            	<label class="inline">Fecha final*:&nbsp;</label>
                            </td>
                            <td align="left" style="width:13%;">
                            	<input type="text" class="input" maxlength="10" style="width:120px;" name="txt_fecha_fin_b" id="txt_fecha_fin_b" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                            </td>
                        	<td align="right" style="width:12%;">
                            	<label class="inline">Paciente:&nbsp;</label>
                            </td>
                            <td align="left" style="width:38%;">
                                <input type="text" id="txt_paciente_b" placeholder="Nombre o documento del paciente" style="width:90%;"  onblur="trim_cadena(this);" />
                            </td>
                        </tr>
                        <tr>
                        	<td align="right">
                            	<label class="inline">Procedimiento:&nbsp;</label>
                            </td>
                            <td align="left" colspan="3">
                                <?php
                                	$combo->getComboDb("cmb_proc_cotiz_b", "", $lista_proc_cotiz, "id_proc_cotiz,nombre_proc_cotiz", "--Todos los procedimientos--", "", true, "width:90%;");
								?>
                            </td>
                        	<td align="right">
                            	<label class="inline">Atendi&oacute;:&nbsp;</label>
                            </td>
                            <td align="left">
                                <?php
                                	$combo->getComboDb("cmb_usuario_prof_b", "", $lista_usuarios_prof, "id_usuario,nombre_completo", "--Todos los profesionales--", "", true, "width:90%;");
								?>
                            </td>
                        </tr>
                        <tr>
                        	<td align="right">
                            	<label class="inline">Sede:&nbsp;</label>
                            </td>
                            <td align="left" colspan="3">
                                <?php
                                	$combo->getComboDb("cmb_lugar_cita_b", "", $lista_lugares, "id_detalle,nombre_detalle", "--Todas las sedes--", "", true, "width:90%;");
								?>
                            </td>
                        	<td align="right">
                            	<label class="inline">Observaciones:&nbsp;</label>
                            </td>
                            <td align="left">
                                <input type="text" id="txt_observaciones_cotiz_b" placeholder="Observaciones de la cotizaci&oacute;n" style="width:90%;"  onblur="trim_cadena(this);" />
                            </td>
                        </tr>
                        <tr>
                            <td align="center" colspan="6">
                                <input type="button" id="btn_consultar" name="btn_consultar" class="btnPrincipal" value="Buscar" onclick="buscar_cotizaciones();" />
                            </td>
                        </tr>
                    </table>
                </form>
                <div id="d_resul_cotizaciones" style="min-height: 30px;"></div>
            </div>
        </div>
        <script type="text/javascript" src="../js/foundation.min.js"></script>
        <script>
			$(document).foundation();
			
			window.prettyPrint && prettyPrint();
			
			$("#txt_fecha_ini_b").fdatepicker({
				format: "dd/mm/yyyy"
			});
			$("#txt_fecha_fin_b").fdatepicker({
				format: "dd/mm/yyyy"
			});
        </script>
        <?php
			$contenido->footer();
        ?>
    </body>
</html>
