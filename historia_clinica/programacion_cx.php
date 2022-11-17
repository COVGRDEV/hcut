<?php
	session_start();
	/*
	  Pagina para la programación de cirugías
	  Autor: Feisar Moreno - 21/11/2017
	 */
	require_once("../db/DbVariables.php");
	require_once("../principal/ContenidoHtml.php");
	require_once("../funciones/Utilidades.php");
	
	$dbVariables = new Dbvariables();
	$contenidoHtml = new ContenidoHtml();
	$utilidades = new Utilidades();
	
	//variables
	$titulo = $dbVariables->getVariable(1);
	$horas_edicion = $dbVariables->getVariable(7);
	
	//Cambiar las variables get a post
	$utilidades->get_a_post();
	
	$id_menu = $_POST["hdd_numero_menu"];
	$tipo_acceso_menu = $contenidoHtml->obtener_permisos_menu($id_menu);
	
	//Se verifica si hay parámetros de programación y paciente
	$id_prog_cx_ini = "";
	$id_paciente_ini = "";
	if (isset($_POST["hdd_id_prog_cx_cita"])) {
		@$id_prog_cx_ini = $utilidades->str_decode($_POST["hdd_id_prog_cx_cita"]);
		@$id_paciente_ini = $utilidades->str_decode($_POST["hdd_id_paciente_cita"]);
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $titulo["valor_variable"]; ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
        <link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
        <link href="../css/azul.css" rel="stylesheet" type="text/css" />
        <!--Para autocompletar DEBE IR DE PRIMERO-->
        <script type="text/javascript" src="../js/jquery_autocompletar.js"></script>
        <script type="text/javascript" src="../js/jquery-ui.js"></script>
        <!--Para validar DEBE IR DE SEGUNDO-->
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.add.js"></script>
        <!--Para funciones de optometria DEBE IR DE TERCERO-->
        <script type="text/javascript" src="../js/ajax.js"></script>
        <script type="text/javascript" src="../js/funciones.js"></script>
        <script type="text/javascript" src="../js/validaFecha.js"></script>
        <script type="text/javascript" src="programacion_cx_v1.1.js"></script>
    </head>
    <body <?php if ($id_paciente_ini != "") { ?>onload="buscar_paciente_prog_id(<?php echo($id_paciente_ini); ?>, '<?php echo($id_prog_cx_ini); ?>');"<?php } ?>>
        <?php
			$contenidoHtml->validar_seguridad(0);
			$contenidoHtml->cabecera_html();
			
			$id_usuario_crea = $_SESSION["idUsuario"];
			
			//Listado de posibles valores para el poder del lente
			$cadena_poder_lente = "";
			for ($i = -8; $i <= 40; $i++) {
				if ($cadena_poder_lente != "") {
					$cadena_poder_lente .= ",";
				}
				if ($i >= 0) {
					$cadena_poder_lente .= "'".$i.",00','".$i.",50'";
				} else if ($i < -1) {
					$cadena_poder_lente .= "'".$i.",00','".($i + 1).",50'";
				} else {
					$cadena_poder_lente .= "'".$i.",00','-".($i + 1).",50'";
				}
			}
			$cadena_poder_lente .= ",'NP/NA'";
		?>
		<script id="ajax">
            var array_poder_lente = [<?php echo($cadena_poder_lente) ?>];
            
            var Tags_poder_lente = [<?php echo($cadena_poder_lente) ?>];
        </script>
        <div class="title-bar">
            <div class="wrapper">
                <div class="breadcrumb">
                    <ul>
                        <li class="breadcrumb_on">Programaci&oacute;n de Cirug&iacute;as</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="contenedor_principal">
            <div id="guardar_historia_clinica" style="width: 100%; display: block;">
                <div class="contenedor_error" id="contenedor_error"></div>
                <div class="contenedor_exito" id="contenedor_exito"></div>
            </div>
            <div class="formulario" id="principal_historia_clinica" style="width: 100%; display: block; ">
            	<input type="hidden" id="hdd_menu" value="<?php echo($id_menu); ?>" />
                <table id="buscar">
                    <tr>
                        <td>
                        	<input type="text" id="txt_paciente_hc" name="txt_paciente_hc" placeholder="Buscar por nombre o n&uacute;mero de documento del paciente" style="width:500px;" onblur="trim_cadena(this);" />
                        </td>
                        <td>
                           	<input class="btnPrincipal peq" type="button" value="Consultar" id="btn_consultar" name="btn_consultar" onclick="buscar_paciente_prog();" />
                        </td>
                        <td>
                           	<?php
                               	if ($tipo_acceso_menu == "2") {
							?>
                            <input class="btnPrincipal peq" type="button" value="Crear nueva programaci&oacute;n" id="btn_ini_crear_prog_cx" name="btn_ini_crear_prog_cx" onclick="mostrar_crear_prog_cx();" />
                            <?php
								}
							?>
                        </td>
                    </tr>
                </table>
                <div id="d_contenedor_programacion_cx" style="min-height: 30px;"></div>
            </div>
        </div>
        <div id="d_impresion_stickers" style="display:none;"></div>
        <div class="div_centro" id="d_centro_stickers" style="width:600px; top:100px; display:none;">
        	<a name="a_cierre_panel2" id="a_cierre_panel2" href="#" onclick="cerrar_div_centro_stickers();"></a>
            <div id="d_interno_stickers" class="div_interno">
                <table class="datagrid" border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%;">
                    <tr style="height:30px;"></tr>
                    <tr class="headegrid">
                        <th align="right" style="width:30%; border: 1px solid #fff;">Cargar archivo:&nbsp;</th>
                        <th align="left" style="width:70%; border: 1px solid #fff;">
                            <form name="frm_arch_stickers" id="frm_arch_stickers" target="ifr_arch_stickers" action="registro_cx_ajax.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" id="opcion" name="opcion" value="3" />
                                <input type="file" id="fil_hoja_stickers" name="fil_hoja_stickers" />
                                <input type="hidden" id="hdd_id_hc_stickers" name="hdd_id_hc_stickers" value="" />
                            </form>
                            <div style="display:none;">
                                <iframe name="ifr_arch_stickers" id="ifr_arch_stickers"></iframe>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th align="center" colspan="2" style="border: 1px solid #fff;">
                            <input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" value="Aceptar" class="btnPrincipal" onclick="cargar_hoja_stickers();"/>&nbsp;&nbsp;
                            <input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" value="Cancelar" class="btnSecundario" onclick="cerrar_div_centro_stickers();"/>
                        </th>
                    </tr>
                </table>
            </div>
        </div>
        <script type="text/javascript" src="../js/foundation.min.js"></script>
        <script>
			$(document).foundation();
        </script>
        <?php
			$contenidoHtml->footer();
        ?>
    </body>
</html>
