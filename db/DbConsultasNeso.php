<?php
	require_once("DbHistoriaClinica.php");
	
	class DbConsultasNeso extends DbHistoriaClinica {
		public function getConsultaNeso($id_hc) {
	        try {
	            $sql = "SELECT id_hc, 
							ind_interferon_od, cantidad_dosis_od, img_neso_od, ind_recidivante_od, 
							ind_interferon_oi, cantidad_dosis_oi, img_neso_oi, ind_recidivante_oi, 
							observaciones
						FROM consultas_neso 
						WHERE id_hc=".$id_hc;				
				//echo($sql);
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		public function crearEditarConsultaNeso(
			$id_hc, 
			$ind_interferon_od, $cantidad_dosis_od, $img_neso_od, $ind_recidivante_od, 
			$ind_interferon_oi, $cantidad_dosis_oi, $img_neso_oi, $ind_recidivante_oi, 
			$array_lesiones, $observaciones, $id_usuario 
		) { 		
			if ($ind_interferon_od == "") { $ind_interferon_od = "NULL"; } 
			if ($cantidad_dosis_od == "") { $cantidad_dosis_od = "NULL"; }
			if ($img_neso_od == "") { $img_neso_od = ""; }
			if ($ind_recidivante_od == "") { $ind_recidivante_od = "NULL"; } 
			if ($ind_interferon_oi == "") { $ind_interferon_oi = "NULL"; } 
			if ($cantidad_dosis_oi == "") { $cantidad_dosis_oi = "NULL"; }
			if ($img_neso_oi == "") { $img_neso_oi = ""; }
			if ($ind_recidivante_oi == "") { $ind_recidivante_oi = "NULL"; } 		
			if ($observaciones == "") { $observaciones = ""; } 
			
			// Procesar detalles - lesiones neso: 
			$sql_delete_detalle_tmp = "DELETE FROM lesiones_neso_tmp WHERE id_hc = ".$id_hc." AND id_usuario_crea = ".$id_usuario;
			$arrCampos_delete[0] = "@id";
			if($this->ejecutarSentencia($sql_delete_detalle_tmp, $arrCampos_delete)){
				
				$array_idojos = explode(",", $array_lesiones["idojos"]);
				$array_nhusos = explode(",", $array_lesiones["nhusos"]);
				$array_husoini = explode(",", $array_lesiones["husoini"]);
				$array_mmcornea = explode(",", $array_lesiones["mmcornea"]);
										
				$canti_lesiones=sizeof($array_idojos); 
				for ($i=0; $i<$canti_lesiones; $i++){
					$id_ojo = $array_idojos[$i]; 
					$canti_husos = $array_nhusos[$i];
					$id_huso_ini = $array_husoini[$i]; 					 
					$cornea_comprometida = $array_mmcornea[$i]; 
					
					if ($id_ojo == "") { $id_ojo = "NULL"; } 
					if ($id_huso_ini == "") { $id_huso_ini = "NULL"; } 
					if ($canti_husos == "") { $canti_husos= "NULL"; } 					
					if ($cornea_comprometida == "") { $cornea_comprometida= "NULL"; }
					
					$sql_insert_detalle="INSERT INTO lesiones_neso_tmp (id_hc, id_ojo, id_huso_ini, canti_husos, cornea_comprometida, id_usuario_crea) 
										VALUES ($id_hc, $id_ojo, $id_huso_ini, $canti_husos, $cornea_comprometida, $id_usuario)"; 
					
					//echo "<br>ins TMP detalle lesion_neso: ".$sql_insert_detalle; 
					$arrCampos[0] = "@id"; 
					$this->ejecutarSentencia($sql_insert_detalle, $arrCampos); 
				} 
			} 			
			
			try {
				$sql = "CALL pa_crear_editar_consulta_neso(".$id_hc.", ".
							$ind_interferon_od.", ".$cantidad_dosis_od.", '".$img_neso_od."', ".$ind_recidivante_od.", ".
							$ind_interferon_oi.", ".$cantidad_dosis_oi.", '".$img_neso_oi."', ".$ind_recidivante_oi.", ".
							" '".$observaciones."', ".$id_usuario.", @id)"; 
				//echo($sql);
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$out_ind_oft = $arrResultado["@id"];
				return $out_ind_oft;
			} catch (Exception $e) {
				return array();			
			}
		}
		
		/* Consultar lesiones de un listas_detalle.id_ojo específico */ 
		public function getLesionesIdOjo($id_hc, $id_ojo=null) { 
			try {
				$sql = "SELECT N.* 
						FROM lesiones_neso N, historia_clinica HC  
						WHERE N.id_hc=".$id_hc." AND N.id_hc=HC.id_hc "; 
				
				if (!is_null($id_ojo)) { 
					$sql .= " AND N.id_ojo=".$id_ojo; 
				} 				
				//echo "<br>".$sql;
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}		
	}
?>