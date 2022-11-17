<?php
session_start();
header('Content-Type: text/xml; charset=UTF-8');

require_once '../db/DbHistoriaClinica.php';
require_once '../funciones/Class_Combo_Box.php';
require_once("../funciones/FuncionesPersona.php");
require_once '../funciones/Utilidades.php';

$combo = new Combo_Box();
$funciones_persona = new FuncionesPersona();
$utilidades = new Utilidades();

$opcion = $_POST["opcion"];

switch ($opcion) {
	case "1": //Búsqueda de pacientes
		?>
        <div class="encabezado">
            <h3>Buscar Pacientes</h3>
        </div>
        <div>
            <table style="width: 100%;">
                <tr>
                    <td>
                        <input type="text" id="txp_paciente_b" name="txp_paciente_b" placeholder="Nombre o documento del paciente" onblur="convertirAMayusculas(this); trim_cadena(this);" />
                    </td>
                    <td style="width: 8%;">
                        <input type="button" id="btnBuscar" nombre="btn_buscar" value="Buscar" class="btnPrincipal peq" onclick="buscar_pacientes();" />
                    </td>
                </tr>
            </table>
            <div id="d_buscar_pacientes"></div>
        </div>    
        <?php
		break;
		
	case "2": //Resultados de búsqueda de pacientes
        @$texto_b = trim($utilidades->str_decode($_POST['texto_b']));
		
        require_once("../db/DbPacientes.php");
		$dbPacientes = new DbPacientes();
		
		$lista_pacientes = $dbPacientes->getBuscarpacientes($texto_b);
        ?>
        <table class="paginated modal_table" style="width:99%; margin:auto;">
            <thead>
                <tr>
                    <th style="width:15%;">Documento</th>
                    <th style="width:85%;">Nombre</th>
                </tr>
            </thead>
            <?php
				if (count($lista_pacientes) > 0) {
					foreach ($lista_pacientes as $paciente_aux) {
						$nombre_aux = $funciones_persona->obtenerNombreCompleto($paciente_aux["nombre_1"], $paciente_aux["nombre_2"], $paciente_aux["apellido_1"], $paciente_aux["apellido_2"]);
			?>
            <tr onclick="seleccionar_paciente('<?php echo($paciente_aux["id_paciente"]); ?>', '<?php echo($nombre_aux); ?>');">
            	<td align="center"><?php echo($paciente_aux['numero_documento']); ?></td>
            	<td align="left"><?php echo($nombre_aux); ?></td>
            </tr>
            <?php
					}
				} else {
			?>
            <td colspan="2">No se encontraron resultados</td>
            <?php
				}
            ?>
        </table>
        <script id='ajax'>
            //<![CDATA[ 
            $(function() {
                $('.paginated', 'table').each(function(i) {
                    $(this).text(i + 1);
                });

                $('table.paginated').each(function() {
                    var currentPage = 0;
                    var numPerPage = 10;
                    var $table = $(this);
                    $table.bind('repaginate', function() {
                        $table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
                    });
                    $table.trigger('repaginate');
                    var numRows = $table.find('tbody tr').length;
                    var numPages = Math.ceil(numRows / numPerPage);
                    var $pager = $('<div class="pager"></div>');
                    for (var page = 0; page < numPages; page++) {
                        $('<span class="page-number"></span>').text(page + 1).bind('click', {
                            newPage: page
                        }, function(event) {
                            currentPage = event.data['newPage'];
                            $table.trigger('repaginate');
                            $(this).addClass('active').siblings().removeClass('active');
                        }).appendTo($pager).addClass('clickable');
                    }
                    $pager.insertBefore($table).find('span.page-number:first').addClass('active');
                });
            });
            //]]>
        </script>
        <?php
		break;
		
	case "3": //Resultado de búsqueda de accesos
		@$id_usuario = $utilidades->str_decode($_POST["id_usuario"]);
		@$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
		@$fecha_inicial = $utilidades->str_decode($_POST["fecha_inicial"]);
		@$fecha_final = $utilidades->str_decode($_POST["fecha_final"]);
		
		$dbHistoriaClinica = new DbHistoriaClinica();
		
		$lista_ingresos = $dbHistoriaClinica->getListaIngresosHistoriaClinica($id_usuario, $id_paciente, $fecha_inicial, $fecha_final);
	?>
	<table class="paginated modal_table" style="width:100%;">
    	<thead>
        	<tr>
            	<th style="width:12%;">Fecha/Hora</th>
                <th style="width:25%;">Usuario</th>
                <th style="width:33%;">Paciente</th>
                <th style="width:30%;">Tipo acceso</th>
            </tr>
        </thead>
		<?php
			if (count($lista_ingresos) > 0) {
				foreach ($lista_ingresos as $ingreso_aux) {
					$nombre_aux = $funciones_persona->obtenerNombreCompleto($ingreso_aux["nombre_1"], $ingreso_aux["nombre_2"], $ingreso_aux["apellido_1"], $ingreso_aux["apellido_2"]);
					$tipo_ingreso_aux = "";
					switch ($ingreso_aux["id_tipo_ingreso"]) {
						case "160": //Registro detallado
							$tipo_ingreso_aux = $ingreso_aux["nombre_tipo_reg"]." (".$ingreso_aux["fecha_hc"].")";
							break;
						case "164": //Fórmula médica
							$tipo_ingreso_aux = $ingreso_aux["tipo_ingreso"]." (".$ingreso_aux["fecha_despacho"].")";
							break;
						default:
							$tipo_ingreso_aux = $ingreso_aux["tipo_ingreso"];
							break;
					}
		?>
        <tr>
        	<td align="center"><?php echo($ingreso_aux["fecha_ingreso_t"]); ?></td>
        	<td align="left"><?php echo($ingreso_aux["nombre_usuario"]." ".$ingreso_aux["apellido_usuario"]); ?></td>
        	<td align="left"><?php echo($ingreso_aux["cod_tipo_documento"]." ".$ingreso_aux["numero_documento"]." - ".$nombre_aux); ?></td>
        	<td align="left"><?php echo($tipo_ingreso_aux); ?></td>
        </tr>
        <?php
				}
			} else {
		?>
        <tr>
        	<td align="center" colspan="4">
            	No se encontraron resultados
            </td>
        </tr>
        <?php
			}
        ?>
    </table>
    <script id='ajax'>
		//<![CDATA[ 
		$(function() {
			$('.paginated', 'table').each(function(i) {
				$(this).text(i + 1);
			});
			
			$('table.paginated').each(function() {
				var currentPage = 0;
				var numPerPage = 10;
				var $table = $(this);
				$table.bind('repaginate', function() {
					$table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
				});
				$table.trigger('repaginate');
				var numRows = $table.find('tbody tr').length;
				var numPages = Math.ceil(numRows / numPerPage);
				var $pager = $('<div class="pager"></div>');
				for (var page = 0; page < numPages; page++) {
					$('<span class="page-number"></span>').text(page + 1).bind('click', {
						newPage: page
					}, function(event) {
						currentPage = event.data['newPage'];
						$table.trigger('repaginate');
						$(this).addClass('active').siblings().removeClass('active');
					}).appendTo($pager).addClass('clickable');
				}
				$pager.insertBefore($table).find('span.page-number:first').addClass('active');
			});
		});
		//]]>
    </script>
    <?php
    	if (count($lista_ingresos) > 0) {
		@$id_usuario = $utilidades->str_decode($_POST["id_usuario"]);
		@$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
		@$fecha_inicial = $utilidades->str_decode($_POST["fecha_inicial"]);
		@$fecha_final = $utilidades->str_decode($_POST["fecha_final"]);
	?>
    <br />
    <input type="button" class="btnPrincipal" value="Exportar a Excel" onclick="exportar_a_excel();" />
	<form name="frm_excel" id="frm_excel" action="accesos_hc_excel.php" method="post" style="display:none;" target="_blank">
    	<input type="hidden" id="hdd_id_usuario_e" name="hdd_id_usuario_e" value="<?php echo($id_usuario); ?>" />
        <input type="hidden" id="hdd_id_paciente_e" name="hdd_id_paciente_e" value="<?php echo($id_paciente); ?>" />
        <input type="hidden" id="hdd_fecha_inicial_e" name="hdd_fecha_inicial_e" value="<?php echo($fecha_inicial); ?>" />
        <input type="hidden" id="hdd_fecha_final_e" name="hdd_fecha_final_e" value="<?php echo($fecha_final); ?>" />
    </form>
    <?php
		}
		break;
}
?>
