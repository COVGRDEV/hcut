$(document).ready(function() {
    $('#exito').click(function() {

        if (jQuery("#frmPass").valid()) {
            validar_contrasena();
        }


    });
});



function validar_contrasena() {

    var txtpass = $('#txtpass').val();
    var txtpass2 = $('#txtpass2').val();
    var txtpassa = $('#txtPassword').val();
    var params = 'opcion=1&txtpass=' + txtpass + '&txtpass2=' + txtpass2+ '&txtpassa=' + txtpassa;
    $("#contenedor_error").css("display", "none");
    $("#contenedor_exito").css("display", "none");
    llamarAjax("pass_ajax.php", params, "contenedor_error", "", "");

}