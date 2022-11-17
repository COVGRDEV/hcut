<?php
	class Tabla_HTML {
		private $tabla="";
		
	 	function __construct() {
 		}
		
	    /**
    	 * Método que agrega el inicio de una tabla.
	     * @param borde indica se se agrega borde a la tabla
    	 */
	    public function add_inicio_tabla($borde) {
    	    if ($borde) {
        	    $this->tabla.="<table border=1>";
	        } else {
        	    $this->tabla.="<table border=0>";
        	}
    	}
    	
	    /**
    	 * Método que agrega el fin de una tabla
	     */
    	public function add_fin_tabla() {
        	$this->tabla.="</table>";
	    }
    	
	    /**
    	 * Método que agrega el inicio de una fila.
	     */
    	public function add_inicio_fila() {
        	$this->tabla.="<tr>";
	    }
    	
	    /**
    	 * Método que agrega el fin de una fila.
	     */
    	public function add_fin_fila() {
        	$this->tabla.="</tr>";
	    }
    	
	    /**
    	 * Método que agrega una celda con el contenido en negrilla dependiendo del segundo parámetro
	     * @param celda     contenido de la celda
    	 * @param negrilla  si el valor es <code>true</code>, inserta el contenido en negrilla
	     */
    	public function add_celda($celda, $negrilla) {
        	if ($negrilla) {
            	$this->tabla.="<td><b>".$celda."</b></td>";
	        } else {
            	$this->tabla.="<td>".$celda."</td>";
        	}
	    }
    	
	    /**
    	 * Método que agrega una celda vacía
	     */
    	public function add_celda_vacia() {
        	$this->tabla.="<td></td>";
	    }
    	
	    /**
    	 * Método que agrega una celda con colspan y rowspan
	     * y el contenido en negrilla dependiendo del segundo parámetro
    	 * @param celda     contenido de la celda
	     * @param colspan   colspan
    	 * @param rowspan   rowspan
	     * @param negrilla  si el valor es <code>true</code>, inserta el contenido en negrilla
    	 */
	    public function add_celda_ajustada($celda, $colspan, $rowspan, $negrilla) {
    	    if ($negrilla) {
        	    $this->tabla.="<td colspan=".$colspan." rowspan=".$rowspan."><b>".$celda."</b></td>";
	        } else {
    	        $this->tabla.="<td colspan=".$colspan." rowspan=".$rowspan.">".$celda."</td>";
        	}
	    }
    	
	    public function get_tabla() {
    	    return $this->tabla;
    	}
	}
?>
