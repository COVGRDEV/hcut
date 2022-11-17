<?php
/**
 * Description of Class_Terceros_Siesa
 *
 * @author Sistemas2
 */
require_once("../funciones/Class_Conector_Siesa.php");
require_once("../db/DbVariables.php");

class Class_Consultas_Siesa {

    public $classConectorSiesa;
	public $dbVariables;
	
    function __construct() {
        $this->classConectorSiesa = new Class_Conector_Siesa();
		$this->dbVariables = new Dbvariables();
    }
	
    public function consultarExistenciaTercero($compania, $num_documento) {
        $array = array();

        //La sigueinte es la estructura de parámetros para la consulta en SIESA
        $array["NIT"] = $num_documento;
        $array["CIA"] = $compania;

        $resultado = $this->classConectorSiesa->ejecutarConsulta(3, $compania, $array);
		
        return $resultado;
    }

    public function consultarCondicionPagoTercero($compania, $num_documento) {
        $array = array();
		
        //La sigueinte es la estructura de parámetros para la consulta en SIESA
        $array["NIT"] = $num_documento;
        $array["CIA"] = $compania;
		
        $resultado = $this->classConectorSiesa->ejecutarConsulta(1, $compania, $array);
		//var_dump($resultado);
		if (isset($resultado["CONDICION_PAGO"])) {
			$resul_aux = $resultado["CONDICION_PAGO"];
		} else {
			$resul_aux = -1;
		}
		
        return $resul_aux;
    }
	
	//Consulta que trae el identificador de una factura
    public function consultarFacturaPago($compania, $id_pago) {
        
		$array = array();
		$variable_obj = $this->dbVariables->getVariable(26);
		$pago_base = $variable_obj["valor_variable"];
		if(intval($id_pago)>=$pago_base){
			$id_pago = $id_pago."-2";
		}
		
        //La siguiente es la estructura de parámetros para la consulta en SIESA
        $array["ID_PAGO"] = $id_pago;
        $array["CIA"] = $compania;
		
		$resultado = $this->classConectorSiesa->ejecutarConsulta(6, $compania, $array);
		if (isset($resultado["CONSECUTIVO"])) {
			$resul_aux = $resultado["CONSECUTIVO"];
		} else {
			$resul_aux = -1;
		}
		
        return $resul_aux;
    }
	
	//Consulta que trae el identificador de un pedido
    public function consultarPedidoPago($compania, $id_pago) {
        $array = array();
		$variable_obj = $this->dbVariables->getVariable(26);
		$pago_base = $variable_obj["valor_variable"];
		if(intval($id_pago)>=$pago_base){
			$id_pago = $id_pago."-2";
		}
		
        //La sigueinte es la estructura de parámetros para la consulta en SIESA
        $array["ID_PAGO"] = $id_pago;
        $array["CIA"] = $compania;
		
		$resultado = $this->classConectorSiesa->ejecutarConsulta(7, $compania, $array);
		if (isset($resultado["DOCUMENTO"])) {
			$resul_aux = $resultado["DOCUMENTO"];
		} else {
			$resul_aux = -1;
		}
		
        return $resul_aux;
    }
	
	//Consulta que trae el identificador de un recibo de caja
    public function consultarReciboCaja($compania, $nit_tercero) {
        $array = array();
		
        //La sigueinte es la estructura de parámetros para la consulta en SIESA
        $array["CIA"] = $compania;
        $array["NIT"] = $nit_tercero;
		
		$resultado = $this->classConectorSiesa->ejecutarConsulta(8, $compania, $array);
		
		if (isset($resultado["DOCUMENTO"])) {
			$resul_aux = $resultado["DOCUMENTO"];
		} else {
			$resul_aux = -1;
		}
		
        return $resul_aux;
    }
	
	//Consulta que trae el último identificador de un documento generado a un tercero
    public function consultarUltimoDocumentoTercero($compania, $centro_operacion, $tipo_documento, $nit_tercero) {
        $array = array();
		
        //La sigueinte es la estructura de parámetros para la consulta en SIESA
        $array["CIA"] = $compania;
		$array["CO"] = $centro_operacion;
		$array["TIPO_DOCTO"] = $tipo_documento;
		$array["NIT"] = $nit_tercero;
		
		$resultado = $this->classConectorSiesa->ejecutarConsulta(9, $compania, $array);
		if (isset($resultado["DOCUMENTO"])) {
			$resul_aux = $resultado["DOCUMENTO"];
		} else {
			$resul_aux = -1;
		}
		
        return $resul_aux;
    }
	
	//Consulta que trae el listado de ROWID asociados a los movimientos de una factura
    public function consultarFacturasDetalle($compania, $num_factura) {
        $array = array();
		
        //La sigueinte es la estructura de parámetros para la consulta en SIESA
        $array["CIA"] = $compania;
		$array["CONSEC"] = $num_factura;
		
		$resultado = $this->classConectorSiesa->ejecutarConsulta(10, $compania, $array);
		
		if (isset($resultado["ROWID_MOVTO"])) {
			$resul_aux[0] = $resultado;
		} else {
			$resul_aux = $resultado;
		}
		
        return $resul_aux;
    }
	
	//Consulta que trae el listado de ROWID asociados a los movimientos de un pedido
    public function consultarPedidosDetalle($compania, $num_pedido) {
        $array = array();
		
        //La sigueinte es la estructura de parámetros para la consulta en SIESA
        $array["CIA"] = $compania;
		$array["CONSECUTIVO"] = $num_pedido;
		
		$resultado = $this->classConectorSiesa->ejecutarConsulta(11, $compania, $array);
		
		if (isset($resultado["ROWID_MOVTO"])) {
			$resul_aux[0] = $resultado;
		} else {
			$resul_aux = $resultado;
		}
		
        return $resul_aux;
    }
	
	//Consulta que trae los pedidos asociados a un pago junto con sus estados
    public function consultarPedidoEstados($compania, $id_pago) {
        
		$array = array();
		$variable_obj = $this->dbVariables->getVariable(26);
		$pago_base = $variable_obj["valor_variable"];
		if(intval($id_pago)>=$pago_base){
			$id_pago = $id_pago."-2";
		}
        //La sigueinte es la estructura de parámetros para la consulta en SIESA
        $array["ID_PAGO"] = $id_pago;
        $array["CIA"] = $compania;
		
		$resultado = $this->classConectorSiesa->ejecutarConsulta(12, $compania, $array);
		
		if (isset($resultado["DOCUMENTO"])) {
			$resul_aux[0] = $resultado;
		} else {
			$resul_aux = $resultado;
		}
		
        return $resul_aux;
    }
	
	//Consulta el costo_unitario de un medicamento
	public function consultarCostoUnitarioMedicamento($compañia, $referencia, $bodega, $lote){
		$array = array();
		
		$array["REFERENCIA"] = $referencia;
		$array["CIA"] = $compañia;
		$array["BODEGA"] = $bodega;
		$array["LOTE"] = $lote;
		
		$resultado = $this->classConectorSiesa->ejecutarConsulta(13, $compania, $array);
				
		if (($resultado["REFERENCIA"])) {
			$resul_aux[0] = $resultado;
		} else {
			$resul_aux = $resultado;
		}
		
        return $resul_aux;
		
		
	}
}
