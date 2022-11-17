

//Funcion que genera el informe
function generarInforme() {



    var fechaInicial = $('#fechaInicial').val();
    var fechaFinal = $('#fechaFinal').val();
    var convenio = $('#cmb_convenio').val();

    if (fechaInicial == '' || fechaFinal == '') {
        alert('Fecha inicial y fecha final no deben ser NULL');
    } else {
        
        var fechaInicial_aux = fechaInicial.split('/');
        var fechaFinal_aux = fechaFinal.split('/');
        //fechaFinal2_aux = fechaInicial_aux[2] + '-' + fechaInicial_aux[1] + '-' + fechaInicial_aux[0];
        
        $('#fechaInicial2').val(fechaInicial_aux[2] + '-' + fechaInicial_aux[1] + '-' + fechaInicial_aux[0]);
        $('#fechaFinal2').val(fechaFinal_aux[2] + '-' + fechaFinal_aux[1] + '-' + fechaFinal_aux[0]);
        
        if(convenio == ''){
           convenio=0; 
        }
        
        $('#convenio2').val(convenio);
        
        //Enviar los datos del formulario
        if (isObject(document.getElementById("frmReporteOportunidad2"))) {
            document.getElementById("frmReporteOportunidad2").submit();
        } else {
            alert("Debe realizar una b\xfasqueda.");
        }
    }




}