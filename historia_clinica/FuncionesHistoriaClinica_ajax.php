<?php session_start();
	header('Content-Type: text/xml; charset=UTF-8');
	
	require_once("../db/DbDepMuni.php");
	require_once("../db/DbAdmision.php");
	require_once("../funciones/Class_Combo_Box.php");
	require_once("../funciones/FuncionesPersona.php");
	require_once("../funciones/Utilidades.php");
	require_once("FuncionesHistoriaClinica.php");
	
	$dbDepMuni = new DbDepMuni();
	$dbAdmision = new DbAdmision();
	$comboBox = new Combo_Box();
	$funciones_persona = new FuncionesPersona();
	$utilidades = new Utilidades();
	
	$opcion = $utilidades->str_decode($_POST["opcion"]);
	
	switch ($opcion) {
		case "1": //Combo de municipios de nacimiento y residencia
			@$cod_dep = $utilidades->str_decode($_POST["cod_dep"]);
			@$sufijo = $utilidades->str_decode($_POST["sufijo"]);
			
			$lista_municipios = $dbDepMuni->getMunicipiosDepartamento($cod_dep);
			$comboBox->getComboDb("cmb_mun_".$sufijo."_enc_hc", "", $lista_municipios, "cod_mun_dane, nom_mun", "--Seleccione--", "", true, "width:250px;", "", "no-margin");
			break;
			
		case "2": //Guardar datos de admisión y paciente
			$id_usuario = $_SESSION["idUsuario"];
			
			//Datos de la admisión
			@$id_admision = $utilidades->str_decode($_POST["id_admision"]);
			@$nombre_acompa = $utilidades->str_decode($_POST["nombre_acompa"]);
			@$numero_hijos = $utilidades->str_decode($_POST["numero_hijos"]);
			@$numero_hijas = $utilidades->str_decode($_POST["numero_hijas"]);
			@$numero_hermanos = $utilidades->str_decode($_POST["numero_hermanos"]);
			@$numero_hermanas = $utilidades->str_decode($_POST["numero_hermanas"]);
			@$presion_arterial = $utilidades->str_decode($_POST["presion_arterial"]);
			@$pulso = $utilidades->str_decode($_POST["pulso"]);
			@$observaciones_admision = $utilidades->str_decode($_POST["observaciones_admision"]);
			@$motivo_consulta = $utilidades->str_decode($_POST["motivo_consulta"]);
			@$cadena_colores_adm = $utilidades->str_decode($_POST["cadena_colores_adm"]);
			
			//Datos del paciente
			@$nombre_1 = $utilidades->str_decode($_POST["nombre_1"]);
			@$nombre_2 = $utilidades->str_decode($_POST["nombre_2"]);
			@$apellido_1 = $utilidades->str_decode($_POST["apellido_1"]);
			@$apellido_2 = $utilidades->str_decode($_POST["apellido_2"]);
			@$id_estado_civil = $utilidades->str_decode($_POST["id_estado_civil"]);
			@$profesion = $utilidades->str_decode($_POST["profesion"]);
			@$id_pais_nac = $utilidades->str_decode($_POST["id_pais_nac"]);
			@$cod_dep_nac = $utilidades->str_decode($_POST["cod_dep_nac"]);
			@$cod_mun_nac = $utilidades->str_decode($_POST["cod_mun_nac"]);
			@$nom_dep_nac = $utilidades->str_decode($_POST["nom_dep_nac"]);
			@$nom_mun_nac = $utilidades->str_decode($_POST["nom_mun_nac"]);
			@$fecha_nacimiento = $utilidades->str_decode($_POST["fecha_nacimiento"]);
			@$sexo = $utilidades->str_decode($_POST["sexo"]);
			@$direccion = $utilidades->str_decode($_POST["direccion"]);
			@$id_pais_res = $utilidades->str_decode($_POST["id_pais_res"]);
			@$cod_dep_res = $utilidades->str_decode($_POST["cod_dep_res"]);
			@$cod_mun_res = $utilidades->str_decode($_POST["cod_mun_res"]);
			@$nom_dep_res = $utilidades->str_decode($_POST["nom_dep_res"]);
			@$nom_mun_res = $utilidades->str_decode($_POST["nom_mun_res"]);
			@$telefono_1 = $utilidades->str_decode($_POST["telefono_1"]);
			@$telefono_2 = $utilidades->str_decode($_POST["telefono_2"]);
			@$email = $utilidades->str_decode($_POST["email"]);
			@$observ_paciente = $utilidades->str_decode($_POST["observ_paciente"]);
			@$cadena_colores_pac = $utilidades->str_decode($_POST["cadena_colores_pac"]);
			
			$resultado = $dbAdmision->editar_admision_paciente_hc($id_admision, $nombre_acompa, $numero_hijos, $numero_hijas, $numero_hermanos, $numero_hermanas,
						 $presion_arterial, $pulso, $observaciones_admision, $motivo_consulta, $cadena_colores_adm, $nombre_1, $nombre_2, $apellido_1, $apellido_2,
						 $id_estado_civil, $profesion, $id_pais_nac, $cod_dep_nac, $cod_mun_nac, $nom_dep_nac, $nom_mun_nac, $fecha_nacimiento, $sexo, $direccion,
						 $id_pais_res, $cod_dep_res, $cod_mun_res, $nom_dep_res, $nom_mun_res, $telefono_1, $telefono_2, $email, $cadena_colores_pac, $id_usuario, $observ_paciente);
		?>
        <input type="hidden" id="hdd_resultado_enc_hc" value="<?php echo($resultado); ?>" />
        <?php
			break;
			
		case "3": //Se carga nuevamente el encabezado dentro del mismo contenerdo
			@$id_paciente = $utilidades->str_decode($_POST["id_paciente"]);
			@$id_admision = $utilidades->str_decode($_POST["id_admision"]);
			@$id_hc = $utilidades->str_decode($_POST["id_hc"]);
			@$ind_editar = intval($_POST["ind_editar"], 10);
			
			$funciones_hc = new FuncionesHistoriaClinica();
			$funciones_hc->encabezado_historia_clinica($id_paciente, $id_admision, $id_hc, $ind_editar, true);
			break;
	}
?>
