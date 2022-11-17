<?php
/*
  Pagina para crear formularios de incapacidades
  Autor: Omar Ibanez - 01/06/2021
 */
require_once("../db/DbAdmision.php");
require_once("../db/DbHistoriaClinica.php");
require_once("../db/DbListas.php");
require_once("../db/DbVariables.php");
require_once("Class_Combo_Box.php");
require_once("../funciones/Utilidades.php");

class Class_Incapacidades {

    public function getFormulacionIncapacidades($id_hc, $id_admision,$id_paciente="", $id_usuario_profesional="", $admision_obj=array(), $ind_editar = 0) {
       $dbHistoriaClinica = new DbHistoriaClinica();
	   $dbListas = new DbListas();
	   $utilidades = new Utilidades();
	   $combo = new Combo_Box();
	   $dbVariables = new DbVariables();
	   ?>
			   <table border="0" cellpadding="5" cellspacing="0" align="center" style="width:95%;">
					<tr>
						<td align="center" colspan="4">
							<label>Imprimir incapacidad</label>
							<div style="text-align:left;" class="div_marco"></div>
						</td>
					</tr>
				</table>
				<table id="tabla_refrafinal" class="modal_table" cellpadding="5" cellspacing="0" align="center" style="width:95%;">
					<tr>
						 <td class="td_reducido" align="right" style="width:10%;">
							<label class="inline">Tipo de atención:</label>
						 </td>
							<?php
						
							$tipos_atencion = $dbListas->getListaDetalles(84,1);							
							$historia_clinica_det = $dbHistoriaClinica->getHistoriaClinicaDet($id_hc);
							
							$incapacidad_obj = "";
							if($id_admision <> ""){
								$incapacidad_obj = $dbHistoriaClinica->getIncapacidades($id_admision);
								
							}else if($id_hc <> ""){
								
								$incapacidad_obj = $dbHistoriaClinica->getIncapacidadesByHC($id_hc);
								
							}
							
							if(count($historia_clinica_det) > 0){	
							?>
								<input type="hidden" id="hdd_rta_hc" nombre="hdd_rta_hc" value="1"/>	
							<?php
							}
							?>
								
						 <input type="hidden" id="hdd_id_usuario" nombre="hdd_id_usuario" value="<?=$_SESSION["idUsuario"];?>"/>
						 <input type="hidden" id="hdd_id_paciente_hc" nombre="hdd_id_paciente_hc" value="<?=$id_paciente;?>"/>
						 <input type="hidden" id="hdd_id_convenio_hc" nombre="hdd_id_convenio_hc" value="<?= $admision_obj["id_convenio"];?>" />
						 <input type="hidden" id="hdd_id_plan_hc" nombre="hdd_id_plan_hc" value="<?= $admision_obj["id_plan"];?>" />
						 <input type="hidden" id="hdd_id_lugar_cita_hc" nombre="hdd_id_lugar_cita_hc" value="<?= $admision_obj["id_lugar_cita"];?>" />
						 <input type="hidden" id="hdd_id_profesional" nombre="hdd_id_profesional" value="<?=  $id_usuario_profesional;?>" /> 
						 <input type="hidden" id="hdd_id_admision" nombre="hdd_id_admision" value="<?= $id_admision;?>" />                                                        
						 <input type="hidden" id="hdd_id_paciente" nombre="hdd_id_paciente" value="<?=$id_paciente;?>"/>
						 <input type="hidden" id="hdd_id_lugar_cita" nombre="hdd_id_lugar_cita" value="<?= $historia_clinica_det["id_lugar_cita"];?>" />
						 <input type="hidden" id="hdd_id_hc" nombre="hdd_id_hc" value="<?=$id_hc; ?>" />
							 
						<td align="left" style="width:10%;">
							<?php
								$combo->getComboDb("cmb_tipo_atencion_hc", $incapacidad_obj["tipo_atencion"], $tipos_atencion, "id_detalle, nombre_detalle", "Seleccione---", "", true, "width: 250px;");
							?>
						
						</td>
						<td class="td_reducido" align="right" style="width:10%;">
							<label class="inline">Prórroga:</label>
						</td>

						<td align="left" style="width:10%;">
								
							<?php
							
								$array_prorroga = array();
								$array_prorroga[0]["id"] = "1";
								$array_prorroga[0]["nombre"] = "Sí";
								$array_prorroga[1]["id"] = "2";
								$array_prorroga[1]["nombre"] = "No";
								$combo->getComboDb("cmb_prorroga_hc", $incapacidad_obj["prorroga"], $array_prorroga, "id, nombre", "Seleccione---", "", true, "width: 250px;");
							?>
						
						</td>
						<td class="td_reducido" align="right" style="width:10%;">
							<label class="inline">Origen de Incapacidad:</label>
						</td>
				
						<td align="left" style="width:20%;">
								<?php
									$origenes_incapacidades = $dbListas->getListaDetalles(85,1);
									$combo->getComboDb("cmb_origen_incapacidad_hc", $incapacidad_obj["origen_incapacidad"], $origenes_incapacidades, "id_detalle, nombre_detalle", "Seleccione---", "", true, "width: 250px;");
						
								?>
						</td>
					</tr>
					<tr>
					   <td class="td_reducido" align="right" style="width:10%;"><label class="inline">Fecha Inicio:</label></td>
					   <td>
					   <?php 
							$fecha_ini="";
							$fecha_fin="";
							if($incapacidad_obj["fecha_inicio_incapacidad"] <> ""){ $fecha_ini =  $dbVariables->cambiarFormatoFecha($incapacidad_obj["fecha_inicio_incapacidad"]); }
							if($incapacidad_obj["fecha_fin_incapacidad"] <> ""){  $fecha_fin = $dbVariables->cambiarFormatoFecha($incapacidad_obj["fecha_fin_incapacidad"]); }
							
					   ?>
							<input type="text" value="<?= $fecha_ini; ?>" class="input" maxlength="10" style="width:120px;" name="fechaInicial" id="fechaInicial" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';" onchange="calcular_diff_fechas();" onblur="DateFormat(this, this.value, event, true, '3'); calcular_diff_fechas(); " tabindex="">
														
						</td>
						<td class="td_reducido" align="right" style="width:10%;"><label class="inline">Fecha Fin:</label></td>
						<td>
							 <input type="text"  value="<?= $fecha_fin ?>" class="input" maxlength="10" style="width:120px;" name="fechaFinal" id="fechaFinal" onkeyup="DateFormat(this, this.value, event, false, '3');" onfocus="vDateType = '3';"  onchange="calcular_diff_fechas();" onblur="DateFormat(this, this.value, event, true, '3'); calcular_diff_fechas();" tabindex="">
														
						</td>
						<td class="td_reducido" align="right" style="width:10%;"><label class="inline">Días incapacidad:</label></td>
						<td id="hdd_diferencia_dias">
							<input type="hidden" class="input" name="hdd_diferencia_dias" id="hdd_diferencia_dias"  />
						</td>
					</tr>
					<tr>
						<td align="left" colspan="8">
							<label>Observaciones adicionales:&nbsp;(Opcional)</label>
							<?php
								$observaciones_adicionales_aux = $utilidades->ajustar_texto_wysiwyg($incapacidad_obj["observaciones"]);
							?>
							<div id="txt_observaciones_adicionales"><?php echo($observaciones_adicionales_aux); ?></div>
															
						 </td>
					</tr>
					  <td colspan="8">
						 <input type="button" id="btn_imprimir_incapacidad" nombre="btn_imprimir_incapacidad" class="btnPrincipal peq" style="font-size: 16px;" value="Imprimir incapacidad" onclick="guardar_incapacidad();" />
					   </td>

				</table>
				<script type="text/javascript" src="../js/foundation.min.js"></script>
				<script>
					$(document).foundation();
					
					window.prettyPrint && prettyPrint();
					
					$("#fechaInicial").fdatepicker({
						format: "dd/mm/yyyy"
					});
					$("#fechaFinal").fdatepicker({
						format: "dd/mm/yyyy"
					});
				</script>
	 <?php	   
       
    }

  
}
