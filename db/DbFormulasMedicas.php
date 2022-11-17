<?php

require_once("DbConexion.php");

class DbFormulasMedicas extends DbConexion {

    public function get_formula_medica($id_formula_med) {
        try {
            $sql = "SELECT FM.*, C.nombre_convenio, P.nombre_plan
						FROM formulas_medicas FM
						LEFT JOIN convenios C ON FM.id_convenio=C.id_convenio
						LEFT JOIN planes P ON FM.id_plan=P.id_plan
						WHERE Fm.id_formula_med=" . $id_formula_med;

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function get_lista_formulas_medicas_texto($texto_formula) {
        try {
            $sql = "SELECT FM.*, C.nombre_convenio, P.nombre_plan
						FROM formulas_medicas FM
						LEFT JOIN convenios C ON FM.id_convenio=C.id_convenio
						LEFT JOIN planes P ON FM.id_plan=P.id_plan ";
            if ($texto_formula != "") {
                $sql .= "WHERE C.nombre_convenio LIKE '%" . $texto_formula . "%'
							OR P.nombre_plan LIKE '%" . $texto_formula . "%' ";
            }
            $sql .= "ORDER BY C.nombre_convenio, P.nombre_plan";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function get_lista_formulas_ciex($id_formula_med) {
        try {
            $sql = "SELECT FC.*, CX.nombre AS nombre_ciex
						FROM formulas_ciex FC
						INNER JOIN vi_ciex CX ON FC.cod_ciex=CX.codciex
						WHERE FC.id_formula_med=" . $id_formula_med . "
						ORDER BY FC.cod_ciex";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function get_lista_formulas_texto($id_formula_med) {
        try {
            $sql = "SELECT * FROM formulas_texto
						WHERE id_formula_med=" . $id_formula_med . "
						ORDER BY num_texto";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function get_lista_formulas_hc($id_convenio, $id_plan, $arr_ciex) {
        try {
            $sql = "SELECT FM.*, C.nombre_convenio, P.nombre_plan
						FROM formulas_medicas FM
						LEFT JOIN convenios C ON FM.id_convenio=C.id_convenio
						LEFT JOIN planes P ON FM.id_plan=P.id_plan
						WHERE (FM.id_convenio=" . $id_convenio . " OR FM.id_convenio IS NULL)
						AND (FM.id_plan=" . $id_plan . " OR FM.id_plan IS NULL)
						AND FM.ind_activo=1 ";
            if (count($arr_ciex) > 0) {
                $cadena_ciex = "";
                foreach ($arr_ciex as $cod_ciex_aux) {
                    if ($cadena_ciex != "") {
                        $cadena_ciex .= ", ";
                    }
                    $cadena_ciex .= "'" . $cod_ciex_aux . "'";
                }
                $sql .= "AND EXISTS (
								SELECT * FROM formulas_ciex FC
								WHERE FC.cod_ciex IN (" . $cadena_ciex . ")
								AND FC.id_formula_med=FM.id_formula_med
							) ";
            } else {
                return array();
            }
            $sql .= "ORDER BY FM.id_convenio DESC, FM.id_plan DESC";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function crear_formula_medica($id_convenio, $id_plan, $ind_activo, $arr_ciex, $arr_ftexto, $id_usuario) {
        try {
            //Se borran los temporales para el usuario
            $sql = "DELETE FROM temporal_formulas_ciex WHERE id_usuario=" . $id_usuario;

            $this->ejecutarSentencia($sql, array());

            $sql = "DELETE FROM temporal_formulas_texto WHERE id_usuario=" . $id_usuario;

            $this->ejecutarSentencia($sql, array());

            //Se crean los temporeales de ciex
            foreach ($arr_ciex as $cod_ciex_aux) {
                $sql = "INSERT INTO temporal_formulas_ciex (id_usuario, cod_ciex)
							VALUES (" . $id_usuario . ", '" . $cod_ciex_aux . "')";

                $this->ejecutarSentencia($sql, array());
            }

            //Se crean los temporeales de textos
            foreach ($arr_ftexto as $i => $desc_texto_aux) {
                $sql = "INSERT INTO temporal_formulas_texto (id_usuario, num_texto, desc_texto)
							VALUES (" . $id_usuario . ", " . ($i + 1) . ", '" . $desc_texto_aux . "')";

                $this->ejecutarSentencia($sql, array());
            }

            //Se crea la fórmula médica
            if ($id_convenio == "") {
                $id_convenio = "NULL";
            }
            if ($id_plan == "") {
                $id_plan = "NULL";
            }
            $sql = "CALL pa_crear_formula_medica(" . $id_convenio . ", " . $id_plan . ", " . $ind_activo . ", " . $id_usuario . ", @id)";
            //echo($sql);

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $resultado = $arrResultado["@id"];

            return $resultado;
        } catch (Exception $e) {
            return -2;
        }
    }

    public function editar_formula_medica($id_formula_med, $id_convenio, $id_plan, $ind_activo, $arr_ciex, $arr_ftexto, $id_usuario) {
        try {
            //Se borran los temporales para el usuario
            $sql = "DELETE FROM temporal_formulas_ciex WHERE id_usuario=" . $id_usuario;

            $this->ejecutarSentencia($sql, array());

            $sql = "DELETE FROM temporal_formulas_texto WHERE id_usuario=" . $id_usuario;

            $this->ejecutarSentencia($sql, array());

            //Se crean los temporeales de ciex
            foreach ($arr_ciex as $cod_ciex_aux) {
                $sql = "INSERT INTO temporal_formulas_ciex (id_usuario, cod_ciex)
							VALUES (" . $id_usuario . ", '" . $cod_ciex_aux . "')";

                $this->ejecutarSentencia($sql, array());
            }

            //Se crean los temporeales de textos
            foreach ($arr_ftexto as $i => $desc_texto_aux) {
                $sql = "INSERT INTO temporal_formulas_texto (id_usuario, num_texto, desc_texto)
							VALUES (" . $id_usuario . ", " . ($i + 1) . ", '" . $desc_texto_aux . "')";

                $this->ejecutarSentencia($sql, array());
            }

            //Se modifica la fórmula médica
            if ($id_convenio == "") {
                $id_convenio = "NULL";
            }
            if ($id_plan == "") {
                $id_plan = "NULL";
            }
            $sql = "CALL pa_editar_formula_medica(" . $id_formula_med . ", " . $id_convenio . ", " . $id_plan . ", " . $ind_activo . ", " . $id_usuario . ", @id)";
            //echo($sql);

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $resultado = $arrResultado["@id"];

            return $resultado;
        } catch (Exception $e) {
            return -2;
        }
    }

}

?>
