<?php
	session_start();
	
	header("Content-Type: text/xml; charset=UTF-8");
	
	require_once("../db/DbDatosEntidad.php");
	require_once("../db/DbMaestroExamenes.php");
	require_once("../db/DbMaestroProcedimientos.php");
	require_once("../db/DbListas.php");
	require_once("../funciones/Utilidades.php");
	require_once("../funciones/Class_Combo_Box.php");
	
	$dbDatosEntidad = new DbdatosEntidad();
	$dbMaestroExamenes = new DbMaestroExamenes();
	$dbMaestroProcedimientos = new DbMaestroProcedimientos();
	$dbListas = new DbListas();
	
	$utilidades = new Utilidades();
	$combo = new Combo_Box();
	
	$opcion = $_POST["opcion"];
	
	switch ($opcion) {
		case "1": //Carga el listado de exámenes, la tabla: maestro_examenes
			$parametro = $utilidades->str_decode($_POST["parametro"]);
			
			if ($parametro == "0") {
				$resultado = $dbMaestroExamenes->get_lista_examenes(3);
			} else if ($parametro != "0") {
				$resultado = $dbMaestroExamenes->get_buscar_examenes($parametro);
			}
        ?>
        <table class="paginated modal_table" style="width: 99%; margin: auto;">
            <thead>
                <tr><th colspan="3">Listado de convenios - <?php echo(count($resultado)." registros"); ?></th></tr>
                <tr>
                    <th style="width:5%;">Codigo</th>
                    <th style="width:36%;">Nombre</th>
                    <th style="width:6%;">Estado</th>
                </tr>
            </thead>
            <?php
				if (count($resultado) >= 1) {
					foreach ($resultado as $value) {
						$estado = $value["ind_activo"];
						if ($estado == 1) {
							$estado = "Activo";
							$class_estado = "activo";
						} else if ($estado == 0) {
							$estado = "No Activo";
							$class_estado = "inactivo";
						}
			?>
            <tr onclick="seleccionar_examen('<?php echo($value["id_examen"]); ?>', 1);">
                <td><?php echo($value["id_examen"]); ?></td>
                <td style="text-align: left;"><?php echo($value["nombre_examen"]); ?></td>
                <td><span class="<?php echo($class_estado); ?>"><?php echo($estado); ?></span></td>
            </tr>
            <?php
					}
				} else {
			?>
            <tr>
                <td colspan="3">No hay resultados</td>
            </tr>
            <?php
				}
			?>
        </table>
        <script id="ajax">
			//<![CDATA[ 
			$(function () {
				$(".paginated", "table").each(function (i) {
					$(this).text(i + 1);
				});
				
				$("table.paginated").each(function () {
					var currentPage = 0;
					var numPerPage = 10;
					var $table = $(this);
					$table.bind("repaginate", function () {
						$table.find("tbody tr").hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
					});
					$table.trigger("repaginate");
					var numRows = $table.find("tbody tr").length;
					var numPages = Math.ceil(numRows / numPerPage);
					var $pager = $('<div class="pager"></div>');
					for (var page = 0; page < numPages; page++) {
						$('<span class="page-number"></span>').text(page + 1).bind("click", {
							newPage: page
						}, function (event) {
							currentPage = event.data["newPage"];
							$table.trigger("repaginate");
							$(this).addClass("active").siblings().removeClass("active");
						}).appendTo($pager).addClass("clickable");
					}
					$pager.insertBefore($table).find("span.page-number:first").addClass("active");
				});
			});
			//]]>
        </script>
        <?php
			break;
			
		case "2": //Administra Exámenes, crear y editar.
			$tipo = $_POST["tipo"];
			$codExamenP = $_POST["codExamen"];
	
			$codExamen = "";
			$nombreExamen = "";
			$nombreProcedimiento = "";
			$codigoProcedimiento = "";
			$idExamenCompl = "";
			$observacionPredef = "";
			
			$estiloOculto = "display:none"; //Estilo Css para mostrar u ocultar objetos.
			$regActivo = "checked";
			$accion = "guardaExamen"; //Define si guarda el registro, llama la funcion.
			
			//Si se crea un nuevo exámen
			if ($tipo == "0") {
				$resultado = $dbMaestroExamenes->get_buscar_examen($codExamenP);
				
				$estiloOculto = "display:table-row";
				$codExamen = $resultado["id_examen"];
				$nombreExamen = $resultado["nombre_examen"];
				$nombreProcedimiento = $resultado["nombre_procedimiento"];
				$codigoProcedimiento = $resultado["cod_procedimiento"];
				$idExamenCompl = $resultado["id_examen_compl"];
				$observacionPredef = $resultado["observacion_predef"];
				
				//Si el registro esta activado
				if ($resultado["ind_activo"] == "0") {
					$regActivo = "";
				}
				
				$accion = "editaExamen"; //Define si edita el registro, llama la funcion.
			}
        ?>
        <div class="encabezado">
            <h3>Ex&aacute;menes</h3>
        </div>
        <div class="contenedor_error" id="contenedor_error"></div>
        <div class="contenedor_exito" id="contenedor_exito"></div>
        <div style="width:98%; margin:auto;">
            <form id="frmNuevoExamen" name="frmNuevoExamen">
                <div style="text-align: left;">    
                    <fieldset>
                        <legend>Ex&aacute;men:</legend>
                        <table style="width:100%;">
                            <tr style="<?php echo($estiloOculto); ?>">
                                <td style="width:10%;">
                                    <label class="inline"><b>C&oacute;digo:</b></label>
                                    <input type="hidden" id="hdd_cod_examen" name="hdd_cod_examen" value="<?php echo($codExamen); ?>" />
                                </td>
                                <td style="width:72%;">
                                    <label class="inline"><?php echo($codExamen); ?></label>
                                </td>
                                <td align="right" style="width:12%;">
                                    <label class="inline"><b>Registro activo:</b></label>
                                </td>
                                <td style="width:6%;">
                                    <label style="margin: 6px 0 0 0;">
                                        <input type="checkbox" name="indActivo" id="indActivo" <?php echo($regActivo); ?> />
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="inline"><b>Nombre:</b></label>
                                </td>
                                <td colspan="2">
                                    <input type="text" id="txtNombre" name="txtNombre" value="<?php echo($nombreExamen); ?>" placeholder="Nombre del ex&aacute;men" onblur="trim_cadena(this);" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="inline"><b>Procedimiento:</b></label>
                                </td>
                                <td colspan="2">
                                    <input type="hidden" id="txtCodProcedimiento" name="txtCodProcedimiento" value="<?php echo($codigoProcedimiento); ?>" />
                                    <input type="text" id="txtNombreProcedimiento" name="txtNombreProcedimiento" value="<?php echo($nombreProcedimiento); ?>" placeholder="Procedimiento asociado" onblur="trim_cadena(this);" disabled="disabled" />
                                </td>
                                <td valign="top">
                                    <img src="../imagenes/Add-icon.png" onclick="formSeleccionarProcedimiento()" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="inline"><b>Complementos:</b></label>
                                </td>
                                <td colspan="2">
                                    <?php
                                    	//Se obtiene el listado de complementos
										$lista_complementos = $dbListas->getListaDetalles(54, 1);
										
										$combo->getComboDb("cmb_examen_compl", $idExamenCompl, $lista_complementos, "id_detalle,nombre_detalle", "--Seleccione--", "", 1);
									?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <label><b>Observaciones predefinidas</b></label>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <?php
										if ($observacionPredef != "") {
											$observacionPredef = $utilidades->ajustar_texto_wysiwyg($observacionPredef);
										}
									?>
                                    <div id="txt_observacion_predef"><?php echo($observacionPredef); ?></div>
                                </td>
                            </tr>
                        </table>
						<script id="ajax">
							initCKEditorObservacionPredef("txt_observacion_predef");
						</script>
                        <div style="text-align:center;">
                            <input type="submit" id="btnGuardarConvenio" nombre="btnGuardarConvenio" value="Guardar" class="btnPrincipal" onclick="<?php echo($accion); ?>()"/>
                            <div id="d_resultado_examen"></div>
                        </div>
                    </fieldset>
                </div>
            </form>
        </div>
        <?php
			break;
			
		case "3": //Administra Exámenes, crear y editar.
			$parametro = $utilidades->str_decode($_POST["parametro"]);
			
			$muestraTabla = false; //Variable que muestra la tabla
			
			if ($parametro != "0") {
				$resultado = $dbMaestroProcedimientos->getProcedimientos($parametro);
				$muestraTabla = true;
			}
        ?>
        <div class="encabezado">
            <h3>Procedimientos</h3>
        </div>
        <form id="frmBuscarProcedimiento" name="frmBuscarConvenio"> 
            <table style="width: 100%;">
                <tr valign="middle">
                    <td align="center" colspan="2">
                        <div id="advertenciasg">
                            <div class="contenedor_error" id="contenedor_error"></div>
                        </div> 
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="text" id="txtProcedimiento" name="txtProcedimiento" placeholder="Codigo o nombre del procedimiento" onblur="trim_cadena(this);" />
                    </td>
                    <td style="width: 10%;">
                        <input type="submit" id="btnBuscar" nombre="btnBuscar" value="Buscar" class="btnPrincipal peq" onclick="buscarProcedimiento();"/>
                    </td>
                </tr>
            </table>
        </form>
        <?php
			if ($muestraTabla) {
		?>
        <table class="paginated modal_table" style="width: 99%; margin: auto;">
            <thead>
                <tr><th colspan="2">Listado de procedimientos - <?php echo(count($resultado)." registros"); ?></th></tr>
                <tr>
                    <th style="width:5%;">Codigo</th>
                    <th style="width:36%;">Nombre</th>
                </tr>
            </thead>
            <?php
				if (count($resultado) >= 1) {
					foreach ($resultado as $value) {
			?>
            <tr onclick="seleccionar_proceso('<?php echo($value["cod_procedimiento"]); ?>', '<?php echo($value["nombre_procedimiento"]); ?>');">
                <td><?php echo($value["cod_procedimiento"]); ?></td>
                <td style="text-align: left;"><?php echo($value["nombre_procedimiento"]); ?></td>
            </tr>
            <?php
					}
				} else {
			?>
            <tr>
                <td colspan="2">No hay resultados</td>
            </tr>
            <?php
				}
			?>
        </table>
        <script id="ajax">
			//<![CDATA[ 
			$(function () {
				$(".paginated", "table").each(function (i) {
					$(this).text(i + 1);
				});
				
				$("table.paginated").each(function () {
					var currentPage = 0;
					var numPerPage = 10;
					var $table = $(this);
					$table.bind("repaginate", function () {
						$table.find("tbody tr").hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
					});
					$table.trigger("repaginate");
					var numRows = $table.find("tbody tr").length;
					var numPages = Math.ceil(numRows / numPerPage);
					var $pager = $('<div class="pager"></div>');
					for (var page = 0; page < numPages; page++) {
						$('<span class="page-number"></span>').text(page + 1).bind("click", {
							newPage: page
						}, function (event) {
							currentPage = event.data["newPage"];
							$table.trigger("repaginate");
							$(this).addClass("active").siblings().removeClass("active");
						}).appendTo($pager).addClass("clickable");
					}
					$pager.insertBefore($table).find("span.page-number:first").addClass("active");
				});
			});
			//]]>
		</script>
        <?php
			}
			break;
			
		case "4": //Guardar/Editar Exámenes
			$id_usuario = $_SESSION["idUsuario"];
			@$tipo = $utilidades->str_decode($_POST["tipo"]);
			@$codExamen = $utilidades->str_decode($_POST["codExamen"]);
			@$txtCodProcedimiento = $utilidades->str_decode($_POST["txtCodProcedimiento"]);
			@$txtNombre = $utilidades->str_decode($_POST["txtNombre"]);
			@$idExamenCompl = $utilidades->str_decode($_POST["idExamenCompl"]);
			@$observacionPredef = $utilidades->str_decode($_POST["observacionPredef"]);
			@$indActivo = $utilidades->str_decode($_POST["indActivo"]);
			
			if ($tipo == "1") {//Guarda
				$resultado = $dbMaestroExamenes->crear_examen($txtNombre, $txtCodProcedimiento, $indActivo, $id_usuario, $idExamenCompl, $observacionPredef);
			} else if ($tipo == "0") {//Edita
				$resultado = $dbMaestroExamenes->editar_examen($codExamen, $txtNombre, $txtCodProcedimiento, $indActivo, $id_usuario, $idExamenCompl, $observacionPredef);
			}
		?>
        <input type="hidden" id="hdd_resul_examen" value="<?php echo($resultado); ?>" />
        <?php
			break;
	}
?>
