<?php

/**
 * Description of Class_Terceros_Siesa
 *
 * @author Sistemas2
 */
require_once("../funciones/Class_Conector_Siesa.php");
require_once("../funciones/Class_Consultas_Siesa.php");
require_once("../funciones/FuncionesPersona.php");
require_once("../db/DbPacientes.php");
require_once("../db/DbTerceros.php");
require_once("../db/DbListas.php");
require_once("../db/DbComunicacionSiesa.php");

class Class_Terceros_Siesa {
    //Función que crea el tercero en SIESA con base en los datos de la tabla "pacientes"
    public function crearTerceroCliente($idPaciente, $compania, $id_lugar, $id_usuario) {
        $classConectorSiesa = new Class_Conector_Siesa();
        $classConsultasSiesa = new Class_Consultas_Siesa();
        $dbPacientes = new DbPacientes();
        $dbListas = new DbListas();
		$dbComunicacionSiesa = new DbComunicacionSiesa();
		
        $resultado = -1;
        $paciente = $dbPacientes->getExistepaciente3($idPaciente); //Consulta el paciente en local
        $banderaCrearTercero = false;
        $banderaCrearTerceroCliente = false;
		
        //Consulta existencia del tercero en SIESA
        $rtaTerceroExistencia = $classConsultasSiesa->consultarExistenciaTercero($compania, $paciente["numero_documento"]);
		
        $sede = $dbListas->getSedesDetalle($id_lugar);
        $email_aux = filter_var($paciente["email"], FILTER_VALIDATE_EMAIL) ? $paciente["email"] : $sede["email_sede_det"];
        $numero_documento = $paciente["numero_documento"];
        $nombres = trim($paciente["nombre_1"]." ".$paciente["nombre_2"]);
        $apellido_1 = $paciente["apellido_1"];
        $apellido_2 = $paciente["apellido_2"];
        $nombre_completo = substr($nombres." ".$apellido_1." ".$apellido_2, 0, 50);
        $direccion = substr($paciente["direccion"], 0, 40);
        $pais = ($paciente["paisCodSiesa"] <= 0 ? "169" : $paciente["paisCodSiesa"]); //Sí el país es diferente a Colombia quema el valor a Colombia (169)
        $departamento = ($paciente["paisCodSiesa"] <= 0 ? "68" : $paciente["cod_dep"]); //Sí el país es diferente a Colombia quema el valor a Santander(68),
        $ciudad = ($paciente["paisCodSiesa"] <= 0 ? "001" : (substr($paciente["cod_mun"], 2))); //Sí el país es diferente a Colombia quema el valor a Bucaramanga(001),
        $ciudadSiesa = ($pais . $departamento . $ciudad);
        $telefono_1 = substr($paciente["telefono_1"], 0, 20);

        if (!is_array($rtaTerceroExistencia)) {//Sí el tercero no existe            
            $banderaCrearTercero = true;
        } else {
            $banderaCrearTerceroCliente = true;
        }
		
		if ($banderaCrearTercero) {//Crea el tercero vigara
            //La siguiente es la estructura de parámetros para la creación del tercero
			$array = array(
				"Inicial" => array(
                    "COMPANIA" => $compania
                ),
                "Final" => array(
                    "COMPANIA" => $compania
                ),
                "TercerosVigara" => array(
                    "COMPANIA" => $compania,
                    "ID_TERCERO" => $numero_documento,
                    "NIT_TERCERO" => $numero_documento,
                    "TIPO_IDENT_TERCERO" => $paciente["codigo_doc_siesa"],
                    "TIPO_TERCERO" => 1, //Persona natural                    
                    "APELL1_TERCERO" => $apellido_1,
                    "APELL2_TERCERO" => $apellido_2,
                    "NOMBRES_TERCERO" => $nombres,
                    "F200_NOMBRE_EST" => $nombre_completo,
                    "IND_CLIENTE" => 1, //Indicador de tercero tipo Cliente                    
                    "CONTACTO" => $nombre_completo,
                    "ADDRESS_1" => $direccion,
                    "ID_PAIS" => $pais, //Sí el país es diferente a Colombia quema el valor a Colombia (169)
                    "ID_DEPTO" => $departamento, //Sí el país es diferente a Colombia quema el valor a Santander(68),
                    "ID_CIUDAD" => $ciudad, //Sí el país es diferente a Colombia quema el valor a Bucaramanga(001),
                    "TELEFONO_TERCERO" => $telefono_1,
                    "FECHA_NACIMMIENTO" => (date("Ymd", strtotime($paciente["fecha_nacimiento"]))), //Formatea la fecha de nacimiento: aaaammdd
                    "CELULAR_TERCERO" => $telefono_1,
                    "EMAIL_TERCERO" => $email_aux
                ),
            );

            //Crea el tercero vigara
            $rtaTerceroVigaraCreado = $classConectorSiesa->enviarXML(13, $array, $compania, 0);
			
            //Valida el resultado de la creación del tercero vigara
            if (strlen($rtaTerceroVigaraCreado["ImportarDatosXMLResult"]) == 19 && $rtaTerceroVigaraCreado["ImportarDatosXMLResult"] == "Importacion exitosa") {//19 es la longitud de caracteres para la respuesta: Importacion exitosa
                $banderaCrearTerceroCliente = true;
            } else {
				echo("3 ");
                $resultado = $rtaTerceroVigaraCreado["ImportarDatosXMLResult"]; //Error
				$dbComunicacionSiesa->insertar_comunicacion_siesa(13, $idPaciente, "", "", "", $resultado, $id_usuario);
            }
        }
		
		//Crear obligaciones y detalles tributarios ENTIDAD_FE_TERCEROS
		//Personas naturales
		if($banderaCrearTerceroCliente){
					
		 $arrayTercerosOblg = array(
				"Inicial" => array(
					"COMPANIA" => $compania
				),
				"Final" => array(
					"COMPANIA" => $compania
				),
				"Terceros" => array(
					1 => array(//Asignación de obligaciones y detalles tributarios
						"COMPANIA" => $compania,
						"ID_TERCERO" => $numero_documento,
						"NOM_ENTIDAD" => "EUNOECO017",
						"ATRIBUTO" => "co017_codigo_regimen",
						"CODIGO_MAESTRO" => "MUNOECO016",
						"DETALLE_MAESTRO" => 49
					),
					2 => array(//Asignación de obligaciones y detalles tributarios
						"COMPANIA" => $compania,
						"ID_TERCERO" => $numero_documento,
						"NOM_ENTIDAD" => "EUNOECO017",
						"ATRIBUTO" => "co017_cod_tipo_oblig",
						"CODIGO_MAESTRO" => "MUNOECO019",
						"DETALLE_MAESTRO" => "R-99-PN"
					),
					3 => array(//Asignación de obligaciones y detalles tributarios
						"COMPANIA" => $compania,
						"ID_TERCERO" => $numero_documento,
						"NOM_ENTIDAD" => "EUNOECO031",
						"ATRIBUTO" => "co031_detalle_tributario1",
						"CODIGO_MAESTRO" => "MUNOECO035",
						"DETALLE_MAESTRO" => "01"
					)

				),
			);
					
			$rtaTercerosOblg = $classConectorSiesa->enviarXML(21, $arrayTercerosOblg, $compania, 0);
			
			$banderaCrearTerceroCliente = false;
			//Valida la entidad dinámica FE TERCEROS
			if (strlen($rtaTercerosOblg["ImportarDatosXMLResult"]) == 19 && $rtaTercerosOblg["ImportarDatosXMLResult"] == "Importacion exitosa") {//19 es la longitud de caracteres para la respuesta: Importacion exitosa
				$resultado = 1; //Importación exitosa
				$banderaCrearTerceroCliente = true;
			} else {
				$resultado = $rtaHceoFe["ImportarDatosXMLResult"]; //Error
				$dbComunicacionSiesa->insertar_comunicacion_siesa(21, $idPaciente, "", "", "", $resultado, $id_usuario);
				
			}
					
		}
		
        //Crear o actualizar tercero cliente
        if ($banderaCrearTerceroCliente) {
            $arrayTerceroCliente = array(
                "Inicial" => array(
                    "COMPANIA" => $compania
                ),
                "Final" => array(
                    "COMPANIA" => $compania
                ),
                "Clientes" => array(
                    1 => array(
                        "COMPANIA" =>$compania,
                        "ID_TERCERO" => $numero_documento,
                        "SUCURSAL_CLIENTE" => "001",
                        "DESCRIPCION_CLIENTE" => substr($nombre_completo, 0, 40),
                        "CONDICION_PAGO" => "CON", //Contado
                        "TIPO_CLIENTE" => "0001", //Clientes PAC
                        "CONTACTO_CLIENTE" => $nombre_completo, //Nombre del paciente
                        "ADDRESS1" => $direccion, //Direccion del paciente
                        "ID_PAIS" => $pais, //Sí el país es diferente a Colombia quema el valor a Colombia (169)
                        "ID_DEPTO" => $departamento, //Sí el país es diferente a Colombia quema el valor a Santander(68),
                        "ID_CIUDAD" => $ciudad, //Sí el país es diferente a Colombia quema el valor a Bucaramanga(001),
                        "TELEFONO_CLIENTE" => $telefono_1,
                        "EMAIL_CLIENTE" => $email_aux,
                        "FECHA_INGRESO" => $classConectorSiesa->getFechaActual()
                    )
                ),
            );

            $rtaTerceroCliente = $classConectorSiesa->enviarXML(14, $arrayTerceroCliente, $compania, 0);

            //Valida el resultado de la importación
            //19 es la longitud de caracteres para la respuesta: Importacion exitosa
            if (strlen($rtaTerceroCliente["ImportarDatosXMLResult"]) == 19 && $rtaTerceroCliente["ImportarDatosXMLResult"] == "Importacion exitosa") {

                //Crea la entidad dinámica HCEO FE
                $arrayHceoFe = array(
                    "Inicial" => array(
                        "COMPANIA" => $compania
                    ),
                    "Final" => array(
                        "COMPANIA" => $compania
                    ),
                    "Clientes" => array(
                        1 => array(//Asignación del correo electrónico del tercero cliente para la factura electrónica
                            "COMPANIA" => $compania,
                            "TERCERO_NIT" => $numero_documento,
                            "ID_ENTIDAD" => $sede["id_entidadCorreo_sede_det"],
                            "ID_ATRIBUTO" => $sede["id_atributoCorreo_sede_det"],
                            "DATO_TEXTO" => $email_aux,
                            "ID_MAESTRO_DETALLE" => $ciudadSiesa
                        )
                    ),
                );

                $rtaHceoFe = $classConectorSiesa->enviarXML(16, $arrayHceoFe, $compania, 0);

                //Valida la entidad dinámica HCEO FE
                if (strlen($rtaHceoFe["ImportarDatosXMLResult"]) == 19 && $rtaHceoFe["ImportarDatosXMLResult"] == "Importacion exitosa") {//19 es la longitud de caracteres para la respuesta: Importacion exitosa
                    $resultado = 1; //Importación exitosa
                } else {
                    $resultado = $rtaHceoFe["ImportarDatosXMLResult"]; //Error
					$dbComunicacionSiesa->insertar_comunicacion_siesa(16, $idPaciente, "", "", "", $resultado, $id_usuario);
                }
            } else {
                $resultado = $rtaTerceroCliente["ImportarDatosXMLResult"]; //Error
				$dbComunicacionSiesa->insertar_comunicacion_siesa(14, $idPaciente, "", "", "", $resultado, $id_usuario);
            }
        }
        return $resultado;
    }

    public function crearTerceroClienteNoPaciente($idTercero, $idPaciente, $compania, $id_usuario) {
		$classConectorSiesa = new Class_Conector_Siesa();
        $classConsultasSiesa = new Class_Consultas_Siesa();
        $funcionesPersona = new FuncionesPersona();
        $dbTerceros = new DbTerceros();
        $dbPacientes = new DbPacientes();
        $dbListas = new DbListas();
		$dbComunicacionSiesa = new DbComunicacionSiesa();
		
        $resultado = -1;
        $tercero_obj = $dbTerceros->getTercero($idTercero);
        $paciente_obj = $dbPacientes->getExistepaciente3($idPaciente);
        $banderaCrearTercero = false;
        $banderaCrearTerceroCliente = false;
		
        //Consulta existencia del tercero en SIESA
        $rtaTerceroExistencia = $classConsultasSiesa->consultarExistenciaTercero($compania, $tercero_obj["numero_documento"]);
		
        $sede = $dbListas->getSedesDetalle($_SESSION["idLugarUsuario"]); //Obtiene el detalle de la sede con la cual inició sesión 
        $email_aux = (filter_var($tercero_obj["email"], FILTER_VALIDATE_EMAIL) ? $tercero_obj["email"] : (filter_var($paciente_obj["email"], FILTER_VALIDATE_EMAIL) ? $paciente_obj["email"] : $sede["email_sede_det"]));
        $numero_documento = $tercero_obj["numero_documento"];
		
		$banderaDetTributario = true;
		if($tercero_obj["id_tipo_documento"] == "146"){
			$cod_obligacion = $tercero_obj["cod_obligacion"];
			$cod_tributario = $tercero_obj["cod_tributario"];
			if(is_null($cod_obligacion) || empty($cod_obligacion) || is_null($cod_tributario) || empty($cod_tributario)){
				$banderaDetTributario = false;		
			}
			if($tercero_obj["ind_iva"] == "1"){
				$regimen = 48;			
			}else{
				$regimen = 49;		
			}
		}else{
			$cod_obligacion = "R-99-PN";
			$cod_tributario = "01";	
			$regimen = 49;		
		}
		
		if ($tercero_obj["nombre_1"] != "") {
	        $nombres = trim($tercero_obj["nombre_1"]." ".$tercero_obj["nombre_2"]);
    	    $apellido_1 = $tercero_obj["apellido_1"];
        	$apellido_2 = $tercero_obj["apellido_2"];
	        $nombre_completo = trim(substr($nombres." ".$apellido_1." ".$apellido_2, 0, 50));
		} else {
	        $nombres = $tercero_obj["nombre_tercero"];
    	    $apellido_1 = "";
        	$apellido_2 = "";
	        $nombre_completo = $tercero_obj["nombre_tercero"];
		}
		
        $direccion = substr($paciente_obj["direccion"], 0, 40);
        $pais = ($paciente_obj["paisCodSiesa"] <= 0 ? "169" : $paciente_obj["paisCodSiesa"]); //Sí el país es diferente a Colombia quema el valor a Colombia (169)
        $departamento = ($paciente_obj["paisCodSiesa"] <= 0 ? "68" : $paciente_obj["cod_dep"]); //Sí el país es diferente a Colombia quema el valor a Santander(68),
        $ciudad = ($paciente_obj["paisCodSiesa"] <= 0 ? "001" : (substr($paciente_obj["cod_mun"], 2))); //Sí el país es diferente a Colombia quema el valor a Bucaramanga(001),
        $ciudadSiesa = ($pais . $departamento . $ciudad);
        $telefono_1 = substr($paciente_obj["telefono_1"], 0, 20);
		
		if (!is_array($rtaTerceroExistencia)) {//Sí el tercero no existe            
            $banderaCrearTercero = true;
        } else {
            $banderaCrearTerceroCliente = true;
        }

        if ($banderaCrearTercero) {//Crea el tercero
			//La siguiente es la estructura de parámetros para la creación del tercero
            $array = array(
                "Inicial" => array(
                    "COMPANIA" => $compania
                ),
                "Final" => array(
                    "COMPANIA" => $compania
                ),
                "TercerosVigara" => array(
                    "COMPANIA" => $compania,
                    "ID_TERCERO" => $numero_documento,
                    "NIT_TERCERO" => $numero_documento,
                    "TIPO_IDENT_TERCERO" => $tercero_obj["codigo_doc_siesa"],
                    "TIPO_TERCERO" => 1, //Persona natural                    
                    "APELL1_TERCERO" => $apellido_1,
                    "APELL2_TERCERO" => $apellido_2,
                    "NOMBRES_TERCERO" => $nombres,
                    "F200_NOMBRE_EST" => $nombre_completo,
                    "IND_CLIENTE" => 1, //Indicador de tercero tipo Cliente                    
                    "CONTACTO" => $nombre_completo,
                    "ADDRESS_1" => $direccion,
                    "ID_PAIS" => $pais,
                    "ID_DEPTO" => $departamento,
                    "ID_CIUDAD" => $ciudad,
                    "TELEFONO_TERCERO" => $telefono_1,
                    "FECHA_NACIMMIENTO" => "20000101",
                    "CELULAR_TERCERO" => $telefono_1,
                    "EMAIL_TERCERO" => $email_aux
                ),
            );

            $rtaTerceroCliente = $classConectorSiesa->enviarXML(13, $array, $compania, 0);
			
            //Valida el resultado de la importación
            if (strlen($rtaTerceroCliente["ImportarDatosXMLResult"]) == 19 && $rtaTerceroCliente["ImportarDatosXMLResult"] == "Importacion exitosa") {//19 es la longitud de caracteres para la respuesta: Importacion exitosa
                $banderaCrearTerceroCliente = true;
            } else {
                $resultado = $rtaTerceroCliente["ImportarDatosXMLResult"]; //Error
				$dbComunicacionSiesa->insertar_comunicacion_siesa(13, $idPaciente, $idTercero, "", "", $resultado, $id_usuario);
			
            }
        }
		
			//Entidades y obligaciones
		
				//Crear obligaciones y detalles tributarios ENTIDAD_FE_TERCEROS
		//Personas naturales
		if($banderaCrearTerceroCliente){
					
		 $arrayTercerosOblg = array(
				"Inicial" => array(
					"COMPANIA" => $compania
				),
				"Final" => array(
					"COMPANIA" => $compania
				),
				
				"Terceros" => array(
					1 => array(//Asignación de obligaciones y detalles tributarios
						"COMPANIA" => $compania,
						"ID_TERCERO" => $numero_documento,
						"NOM_ENTIDAD" => "EUNOECO017",
						"ATRIBUTO" => "co017_codigo_regimen",
						"CODIGO_MAESTRO" => "MUNOECO016",
						"DETALLE_MAESTRO" => $regimen
					),
					2 => array(//Asignación de obligaciones y detalles tributarios
						"COMPANIA" => $compania,
						"ID_TERCERO" => $numero_documento,
						"NOM_ENTIDAD" => "EUNOECO017",
						"ATRIBUTO" => "co017_cod_tipo_oblig",
						"CODIGO_MAESTRO" => "MUNOECO019",
						"DETALLE_MAESTRO" => $cod_obligacion
					),
					3 => array(//Asignación de obligaciones y detalles tributarios
						"COMPANIA" => $compania,
						"ID_TERCERO" => $numero_documento,
						"NOM_ENTIDAD" => "EUNOECO031",
						"ATRIBUTO" => "co031_detalle_tributario1",
						"CODIGO_MAESTRO" => "MUNOECO035",
						"DETALLE_MAESTRO" => $cod_tributario
					)

				),
			);
			
			$rtaTercerosOblg = $classConectorSiesa->enviarXML(21, $arrayTercerosOblg, $compania, 0);
			//var_dump($rtaTercerosOblg);
			$banderaCrearTerceroCliente = false;
			
			//Valida la entidad dinámica FE TERCEROS
			if (strlen($rtaTercerosOblg["ImportarDatosXMLResult"]) == 19 && $rtaTercerosOblg["ImportarDatosXMLResult"] == "Importacion exitosa" && $banderaDetTributario) {//19 es la longitud de caracteres para la respuesta: Importacion exitosa
				$resultado = 1; //Importación exitosa
				$banderaCrearTerceroCliente = true;
			} else {
				$resultado = $rtaHceoFe["ImportarDatosXMLResult"]; //Error
				$dbComunicacionSiesa->insertar_comunicacion_siesa(21, $idPaciente, $idTercero, "", "", $resultado, $id_usuario);
				
			}
					
		}

        if ($banderaCrearTerceroCliente) {//Crear o actualizar tercero cliente
            //Consulta existencia del tercero en SIESA
            //$rtaTerceroExistencia = $classConsultasSiesa->consultarExistenciaTercero($compania, $paciente["numero_documento"]);
            $arrayTerceroCliente = array(
                "Inicial" => array(
                    "COMPANIA" => $compania
                ),
                "Final" => array(
                    "COMPANIA" => $compania
                ),
                "Clientes" => array(
					1 => array(
						"COMPANIA" => $compania,
						"ID_TERCERO" => $numero_documento,
						"SUCURSAL_CLIENTE" => "001",
						"DESCRIPCION_CLIENTE" => substr($nombre_completo, 0, 40),
						"CONDICION_PAGO" => "CON", //Contado
						"TIPO_CLIENTE" => "0001", //Clientes PAC
						"CONTACTO_CLIENTE" => $nombre_completo,
						"ADDRESS1" => $direccion,
						"ID_PAIS" => $pais,
						"ID_DEPTO" => $departamento,
						"ID_CIUDAD" => $ciudad,
						"TELEFONO_CLIENTE" => $telefono_1,
						"EMAIL_CLIENTE" => $email_aux,
						"FECHA_INGRESO" => $classConectorSiesa->getFechaActual()
					)
                )
            );
			
            $rtaTerceroCliente = $classConectorSiesa->enviarXML(14, $arrayTerceroCliente, $compania, 0);

            //Valida el resultado de la importación
			if (strlen($rtaTerceroCliente["ImportarDatosXMLResult"]) == 19 && $rtaTerceroCliente["ImportarDatosXMLResult"] == "Importacion exitosa") {//19 es la longitud de caracteres para la respuesta: Importacion exitosa
                //Crea la entidad dinámica HCEO FE
                $arrayHceoFe = array(
                    "Inicial" => array(
                        "COMPANIA" => $compania
                    ),
                    "Final" => array(
                        "COMPANIA" => $compania
                    ),
                    "Clientes" => array(
                        1 => array(//Asignación del correo electrónico del tercero cliente para la factura electrónica
                            "COMPANIA" => $compania,
                            "TERCERO_NIT" => $numero_documento,
                            "ID_ENTIDAD" => $_SESSION["entidadCorreo"],
                            "ID_ATRIBUTO" => $_SESSION["atributoCorreo"],
                            "DATO_TEXTO" => $email_aux,
                            "ID_MAESTRO_DETALLE" => $ciudadSiesa
                        )
                    ),
                );

                $rtaHceoFe = $classConectorSiesa->enviarXML(16, $arrayHceoFe, $compania, 0);
				//var_dump($rtaHceoFe);
				//echo("<br /><br />");

                //Valida la entidad dinámica HCEO FE
                if (strlen($rtaHceoFe["ImportarDatosXMLResult"]) == 19 && $rtaHceoFe["ImportarDatosXMLResult"] == "Importacion exitosa") {//19 es la longitud de caracteres para la respuesta: Importacion exitosa
                    $resultado = 1; //Importación exitosa
                } else {
                    $resultado = $rtaHceoFe["ImportarDatosXMLResult"]; //Error
					$dbComunicacionSiesa->insertar_comunicacion_siesa(16, $idPaciente, $idTercero, "", "", $resultado, $id_usuario);
					
                }
            } else {
                $resultado = $rtaTerceroCliente["ImportarDatosXMLResult"];
				$dbComunicacionSiesa->insertar_comunicacion_siesa(14, $idPaciente, $idTercero, "", "", $resultado, $id_usuario);
            }
        }
		
        return $resultado;
    }

}
?>
