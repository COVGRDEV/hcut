<?php
	/*
		Pagina para la formulación de procedimientos y exámenes
		Autor: Feisar Moreno - 27/02/2018
	*/
	require_once("../db/DbListas.php");
	require_once("../db/DbMaestroProcedimientos.php");
	require_once("../db/DbHistoriaClinica.php");
	require_once("Class_Combo_Box.php");
	
	class Class_Solic_Procs {
		public function getFormularioSolicitud($id_hc, $ind_ojos = true) {
			$dbListas = new DbListas();
			$dbMaestroProcedimientos = new DbMaestroProcedimientos();
			$dbHistoriClinica = new DbHistoriaClinica();
			$combo = new Combo_Box();
			
			$lista_procedimientos_solic = $dbMaestroProcedimientos->getListaHCProcedimientosSolic($id_hc);
			$lista_ojos = $dbListas->getListaDetalles(14);
			
			$cant_procedimientos = count($lista_procedimientos_solic);
			if ($cant_procedimientos == 0) {
				$cant_procedimientos = 1;
			}
	?>
	<input type="hidden" name="hdd_cant_procedimientos" id="hdd_cant_procedimientos" value="<?php echo($cant_procedimientos);?>" />
    <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:90%;">
    	<tr>
        	<td align="center" style="width:12%;">
				<h7><b>C&oacute;digo CUPS</b></h7>
			</td>
            <?php
            	if ($ind_ojos) {
			?>
			<td align="center" style="width:76%;">
				<h7><b>Procedimiento/Examen</b></h7>
			</td>
			<td align="center" style="width:12%;">
				<h7><b>Ojo</b></h7>
			</td>
            <?php
				} else {
			?>
			<td align="center" style="width:88%;">
				<h7><b>Procedimiento/Examen</b></h7>
			</td>
            <?php
				}
			?>
		</tr>
    </table>
    <?php
		for ($i = 0; $i < 20; $i++) {
			if ($i < count($lista_procedimientos_solic)) {
				$procedimiento_aux = $lista_procedimientos_solic[$i];
				
				$cod_procedimiento_aux = $procedimiento_aux["cod_procedimiento"];
				$nombre_procedimiento_aux = $procedimiento_aux["nombre_procedimiento"];
				$id_ojo_aux = $procedimiento_aux["id_ojo"];
			} else {
				$cod_procedimiento_aux = "";
				$nombre_procedimiento_aux = "";
				$id_ojo_aux = "";
			}
	?>
    <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:90%; display:none;" id='tbl_proc_solic_<?php echo($i);?>'>
    	<tr>
        	<td align="left" style="width:12%;">
            	<input type="text" name="txt_cod_procedimiento_<?php echo($i);?>" id="txt_cod_procedimiento_<?php echo($i);?>" value="<?php echo($cod_procedimiento_aux);?>" style="width:100%;"  maxlength="6" class="no-margin" onblur="convertirAMayusculas(this); trim_cadena(this); buscar_cod_cups_solic('<?php echo($i);?>');" />
                <input type="hidden" name="hdd_cod_procedimiento_<?php echo($i);?>" id="hdd_cod_procedimiento_<?php echo($i);?>" value="<?php echo($cod_procedimiento_aux);?>" />
            </td>
            <?php
            	if ($ind_ojos) {
					$porcentaje_aux = 71;
				} else {
					$porcentaje_aux = 83;
				}
			?>
			<td align="left" style="width:<?php echo($porcentaje_aux); ?>%;">
            	<h6 style="margin: 0px;">
                	<div id="d_nombre_procedimiento_<?php echo($i);?>"><?php echo($nombre_procedimiento_aux);?></div>
                </h6>
            </td>
            <td align="right" style="width:5%;">
            	<div class="d_buscar" onclick="abrir_buscar_cups_solic('<?php echo($i);?>');"></div>
            </td>
            <?php
            	if ($ind_ojos) {
			?>
            <td align="left" style="width:12%;">
            	<?php
					$combo->getComboDb("cmb_ojo_".$i, $id_ojo_aux, $lista_ojos, "id_detalle, nombre_detalle", "No aplica", "", "", "width:90px;", "", "no-margin");
				?>
            </td>
            <?php
				} else {
			?>
            <input type="hidden" id="cmb_ojo_<?php echo($i); ?>" name="cmb_ojo_<?php echo($i); ?>" value="" />
            <?php
				}
			?>
        </tr>
    </table>
    <?php
			}
	?>
    <table border="0" cellpadding="3" cellspacing="0" align="center" style="width:90%;">
    	<tr>
        	<td>
            	<div class="agregar_alemetos" onclick="agregar_procedimientos_solic();"></div>
                <div class="restar_alemetos" onclick="restar_procedimientos_solic();"></div>
            </td>
        </tr>
        <tr style="height:10px;"></tr>
    </table>
    <script>
		mostrar_procedimientos_solic();
	</script>
    <?php	
		}
		
		public function guardarHCProcedimientosSolic($id_hc, $id_usuario) {
			$dbMaestroProcedimientos = new DbMaestroProcedimientos();
			$utilidades = new Utilidades();
			
			@$cant_procedimientos_solic = intval($_POST["cant_procedimientos_solic"], 10);
			
			$lista_procedimientos_solic = array();
			for ($i = 0; $i < $cant_procedimientos_solic; $i++) {
				@$lista_procedimientos_solic[$i]["cod_procedimiento"] = $utilidades->str_decode($_POST["cod_procedimiento_solic_".$i]);
				@$lista_procedimientos_solic[$i]["id_ojo"] = $utilidades->str_decode($_POST["id_ojo_proc_solic_".$i]);
			}
			
			//Se guarda la formulación
			$resultado = $dbMaestroProcedimientos->crearEditarHCProcedimientosSolic($id_hc, $lista_procedimientos_solic, $id_usuario);
			
			return $resultado;
		}
	}
?>
