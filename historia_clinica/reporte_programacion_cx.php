<?php
	session_start();
	/*
	  Reporte de programación de cirugías
	  Autor: Feisar Moreno - 06/12/2017
	 */
	
	require_once("../db/DbVariables.php");
	require_once("../db/DbConvenios.php");
	require_once("../db/DbUsuarios.php");
	require_once("../db/DbListas.php");
	
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Class_Combo_Box.php");
	
	$dbVariables = new Dbvariables();
	$dbConvenios = new DbConvenios();
	$dbUsuarios = new DbUsuarios();
	$dbListas = new DbListas();

	$contenido = new ContenidoHtml();
	$combo = new Combo_Box();
	
	//variables
	$titulo = $dbVariables->getVariable(1);
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

        <script type="text/javascript" src="reporte_programacion_cx_v1.1.js"></script>

        <link href="../src/skin-vista/ui.dynatree.css" rel="stylesheet" type="text/css" >
        <script src="../src/jquery.dynatree.js" type="text/javascript"></script>
    </head>
    <body>
        <?php
        $contenido->validar_seguridad(0);
			$contenido->cabecera_html();
			
			//Se obtiene el listado de convenios
			$lista_convenios = $dbConvenios->getConvenios();
			
			//Se obtiene el listado de estados de programación
			$lista_estados_prog = $dbListas->getListaDetalles(71, 1);
        ?>
        <div class="title-bar">
            <div class="wrapper">
                <div class="breadcrumb">
                    <ul>
                        <li class="breadcrumb_on">Reporte de programaci&oacute;n de cirug&iacute;as</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="contenedor_principal volumen">
            <div class="padding">     
                <fieldset style="text-align: left;">
                    <div>
                        <table border="0" cellpadding="5" cellspacing="0" style="width:100%;">
                            <tr>
                                <td align="right" style="width:15%;">
                                	<label class="inline">Fecha inicial</label>
                                </td>
                                <td style="width:18%;">
                                    <input type="text" class="input" maxlength="10" style="width:120px;" name="txt_fecha_ini" id="txt_fecha_ini" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                                </td>
                                <td align="right" style="width:10%;">
                                	<label class="inline">Fecha final</label>
                                </td>
                                <td style="width:18%;">
                                    <input type="text" class="input" maxlength="10" style="width:120px;" name="txt_fecha_fin" id="txt_fecha_fin" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onBlur="DateFormat(this, this.value, event, true, '3');" tabindex="" />
                                </td>
                                <td align="left" style="width:24%;">
                                	<form name="frm_tipos_fecha" id="frm_tipos_fecha">
                                        <input type="radio" id="rad_tipo_fecha" name="rad_tipo_fecha" value="1" class="no-margin" checked="checked" /><label>Fecha de programaci&oacute;n</label>
                                        <br />
                                        <input type="radio" id="rad_tipo_fecha" name="rad_tipo_fecha" value="2" class="no-margin" /><label>Fecha de registro</label>
                                    </form>
                                </td>
                                <td align="center" rowspan="4" style="width:15%;">
                                    <input type="button" value="Consultar" class="btnPrincipal" onclick="realizar_consulta_prog_cx();" />
                                </td>
                            </tr>
                            <tr>
                                <td align="right">
                                	<label class="inline">Concepto</label>
                                </td>
                                <td colspan="4">
                                    <input type="hidden" id="hdd_cod_concepto" name="hdd_cod_concepto" />
                                    <input type="hidden" id="hdd_tipo_concepto" name="hdd_tipo_concepto" />
                                	<table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    	<tr>
                                        	<td style="width:50%;">
            			                        <input type="text" name="txt_concepto" id="txt_concepto" value="" readonly="readonly" style="width:450px;" />
                                            </td>
                                            <td valign="top" style="width:1%">
			                                    <a href="#" onclick="abrir_buscar_concepto();"><img src="../imagenes/Search-icon.png" style="padding: 0 0 0 5px;" title="Buscar concepto" /></a>
                                            </td>
                                            <td valign="top" style="width:49%">
			                                    <a href="#" onclick="limpiar_concepto();"><img src="../imagenes/borrador.png" style="padding: 0 0 0 5px;" title="Limpiar" /></a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>                               
                            </tr>
                            <tr>
                                <td align="right">
                                	<label class="inline">Profesional</label>
                                </td>
                                <td colspan="2">
                                    <?php
										$lista_usuarios_cx = $dbUsuarios->getListaUsuariosCirugia(1);
	                                    $combo->getComboDb("cmb_lista_usuarios", "", $lista_usuarios_cx, "id_usuario, nombre_completo",  "--Todos los especialistas--", "", "", "width:250px;");
									?>
                                </td>
                            </tr>
                            <tr>
                            	<td align="right">
                                	<label class="inline">Estado</label>
                                </td>
                            	<td align="center" valign="top" colspan="4">
                                	<table style="width:100%" align="center">
                                    	<tr>
                                            <td align="center">
                                            	<input type="checkbox" id="chk_estado_prog_todos" name="chk_estado_prog_todos" class="no-margin" checked="checked" onchange="seleccional_check_estado('t');" />
                                                <label class="inline">Todos</label>
                                            </td>
                                        	<?php
                                            	for ($i = 0; $i < count($lista_estados_prog); $i++) {
													$estado_aux = $lista_estados_prog[$i];
											?>
                                            <td align="center">
                                            	<input type="checkbox" id="chk_estado_prog_<?php echo($i); ?>" name="chk_estado_prog_<?php echo($i); ?>" class="no-margin" checked="checked" onchange="seleccional_check_estado('<?php echo($i); ?>');" />
                                                <label class="inline"><?php echo($estado_aux["nombre_detalle"]); ?></label>
                                            </td>
                                            <?php
												}
											?>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                </fieldset>
                <div id="d_buscar_prog_cx"></div>
            </div>
        </div>
        <script type="text/javascript" src="../js/foundation.min.js"></script>
        <script>
			$(document).foundation();
				$(function() {
					window.prettyPrint && prettyPrint();
					
					$("#txt_fecha_ini").fdatepicker({
						format: "dd/mm/yyyy"
					});
					$("#txt_fecha_fin").fdatepicker({
						format: "dd/mm/yyyy"
					});
				});
        </script>
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