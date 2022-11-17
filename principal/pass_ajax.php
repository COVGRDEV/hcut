<?php

session_start();
/*
  Pagina listado de usuarios, muestra los usuarios existentes, para modificar o crear uno nuevo
  Autor: Helio Ruber López - 16/09/2013
 */

header('Content-Type: text/xml; charset=UTF-8');

require_once("../principal/ContenidoHtml.php");
require_once("../db/DbUsuarios.php");

$contenido = new ContenidoHtml();
$usuarios = new DbUsuarios();
$contenido->validar_seguridad(1);

$opcion = $_POST["opcion"];

switch ($opcion) {
    case "1": //Opcion para buscar usuarios
        $txtpass = $_POST["txtpass"];
        $txtpass2 = $_POST["txtpass2"];
        $txtpassa = $_POST["txtpassa"];
        $pasaporte = false;

        
        $expresion = "/\\A(\\w|\\#| |\\@|\\$|\\%|\\&|\\*|\\(|\\))*\\Z/";  // /i case-insensitivo 


        if ($txtpass != $txtpass2) {
            ?>
            <script type="text/javascript" id="ajax">
                $("#contenedor_error").css("display", "block");
            </script>
            <?php
            echo 'Nueva contrase&ntilde;a y Repetir contrase&ntilde;a no son iguales';
            $pasaporte = false;
        } else {

            if (preg_match($expresion, $txtpass)) {
                //$rtas = $usuarios->updatePass($_SESSION["idUsuario"], $txtpass, $txtpassa);

                $rtas = $usuarios->updatePass($_SESSION["idUsuario"], $txtpass, $txtpassa);



                if ($rtas == 1) {
                    ?>
                    <script type="text/javascript" id="ajax">
                        $("#contenedor_exito").css("display", "block");
                        var params = 'opcion=2';
                        llamarAjax("pass_ajax.php", params, "contenedor_exito", "", "");
                    </script>
                    <?php
                } else {
                    ?>
                    <script type="text/javascript" id="ajax">
                        $("#contenedor_error").css("display", "block");
                    </script>
                    <?php

                    echo 'Contrase&ntilde;a No Valida';
                }
            } else {
                ?>
                <script type="text/javascript" id="ajax">
                    $("#contenedor_error").css("display", "block");
                </script>
                <?php

                echo 'Ingrese solo valores alfanumericos';
                $pasaporte = false;
            }
        }

        break;



    case "2": //Opcion para buscar usuarios
        echo 'Cambio Exitoso';

        break;
}
?>