$(document).ready(function() {
    //Carga el listado de convenios
    muestra_datos_entidad();
});




//Carga los datos de la tabla: datos_entidad
function muestra_datos_entidad() {

    var params = 'opcion=1';
    llamarAjax("datos_entidad_ajax.php", params, "principal", "");
}


//Muestra la venatan flotante de agregar Planes
function ventanaModificar(tipo, codPrestador) {

    var params = 'opcion=2&codPrestador='+codPrestador;
    llamarAjax("datos_entidad_ajax.php", params, "d_interno", "mostrar_formulario_flotante("+tipo+");");
}



//Funcion que busca un convenio especifico
function modificardatosEntidad(codPrestador) {
    $("#frmDatosEntidad").validate({
        rules: {
            txtCodPrestador: {
                required: true,
                
            },
            txtNombrePrestador: {
                required: true,
            },
            
            txtNumeroDocumento: {
                required: true,
            },
        },
        submitHandler: function() {
            
            var txtCodPrestador1 = $('#txtCodPrestador1').val();
            
            var txtCodPrestador = $('#txtCodPrestador').val();
            var txtNombrePrestador = $('#txtNombrePrestador').val();
            var cmbTipoId = $('#cmb_tipo_id').val();
            var txtNumeroDocumento = $('#txtNumeroDocumento').val();
            
            var params = 'opcion=3&txtCodPrestador=' + txtCodPrestador + '&txtNombrePrestador='+txtNombrePrestador+'&cmbTipoId='+cmbTipoId+'&txtNumeroDocumento='+txtNumeroDocumento+'&txtCodPrestador1='+txtCodPrestador1;
            llamarAjax("datos_entidad_ajax.php", params, "hddResultado", "postModificar();");
            
            return false;
        },
    });
}



function postModificar(){
    var resultado = $('#hddResultado').text();
    
    if(resultado == '1'){
        muestra_datos_entidad();//Carga de nuevo la tabla de fondo
        mostrar_formulario_flotante(0);
        $('#contenedor_exito').css('display', 'block');
        $('#contenedor_exito').html('El registro ha sido guardado.');
    }else if(resultado == '-1'){
        alert('Error!. intentar de nuevo');
    }
}