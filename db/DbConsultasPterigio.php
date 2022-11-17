<?php
	require_once("DbHistoriaClinica.php");
	
	class DbConsultasPterigio extends DbHistoriaClinica {
		public function getConsultaPterigio($id_hc) {
	        try {
	            $sql = "SELECT id_hc, 
							grado_oi, ind_reproducido_oi, mov_conjuntiva_sup_oi, ind_astigmatismo_ind_oi,
							grado_od, ind_reproducido_od, mov_conjuntiva_sup_od, ind_astigmatismo_ind_od, 
							observaciones 
						FROM consultas_pterigio 
						WHERE id_hc=".$id_hc;
				
				//echo($sql);
	            return $this->getUnDato($sql);
	        } catch (Exception $e) {
	            return array();
	        }
	    }
		
		public function crearEditarConsultaPterigio(
			$id_hc, 
			$grado_od, $ind_reproducido_od, $mov_conjuntiva_sup_od, $ind_astigmatismo_ind_od, 
			$grado_oi, $ind_reproducido_oi, $mov_conjuntiva_sup_oi, $ind_astigmatismo_ind_oi,
			$observaciones, $id_usuario 
		) { 			
			if ($grado_od == "") { $grado_od = "NULL"; } 
			if ($ind_reproducido_od == "") { $ind_reproducido_od = "NULL"; } 
			if ($mov_conjuntiva_sup_od == "") { $mov_conjuntiva_sup_od = "NULL"; } 
			if ($ind_astigmatismo_ind_od == "") { $ind_astigmatismo_ind_od = "NULL"; } 
			if ($grado_oi == "") { $grado_oi = "NULL"; } 
			if ($ind_reproducido_oi == "") { $ind_reproducido_oi = "NULL"; } 
			if ($mov_conjuntiva_sup_oi == "") { $mov_conjuntiva_sup_oi = "NULL"; } 
			if ($ind_astigmatismo_ind_oi == "") { $ind_astigmatismo_ind_oi = "NULL"; } 
			if ($observaciones == "") { $observaciones = ""; } 
			
			try {
				$sql = "CALL pa_crear_editar_consulta_pterigio(".$id_hc.", ".
							$grado_od.", ".$ind_reproducido_od.", ".$mov_conjuntiva_sup_od.", ".$ind_astigmatismo_ind_od.", ". 
							$grado_oi.", ".$ind_reproducido_oi.", ".$mov_conjuntiva_sup_oi.", ".$ind_astigmatismo_ind_oi.", '".$observaciones."', ".$id_usuario.", @id)"; 
				//echo($sql);
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$out_ind_oft = $arrResultado["@id"];
				return $out_ind_oft;
				return $out_ind_oft;
			} catch (Exception $e) {
				return array();			
			}
		}
	}
?>
