<?php
session_start();
require_once("../db/DbVariables.php");
require_once 'ContenidoHtml.php';
$contenidoHtml = new ContenidoHtml();
$variables = new Dbvariables();
//variables
$titulo = $variables->getVariable(1);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $titulo['valor_variable']; ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
        <link href="../css/azul.css" rel="stylesheet" type="text/css" />
        <script type='text/javascript' src='../js/jquery.js'></script>
        <script type='text/javascript' src='../js/jquery.validate.js'></script>
        <script type='text/javascript' src='../js/funciones.js'></script>
        <script type='text/javascript' src='../js/ajax.js'></script>
        <script type='text/javascript' src='pass.js'></script>
    </head>
    <body>


        <?php
        $contenidoHtml->validar_seguridad(0);
        $contenidoHtml->cabecera_html();
        ?>


        <div class="title-bar">
            <div class="wrapper">
                <div class="breadcrumb">
                    <ul>
                        <li class="breadcrumb_on">Cambiar contrase&ntilde;a</li>
                    </ul>
                </div>
            </div>
        </div>


        <div class="contenedor_principal">
            <div class="padding">



                <div style="width: 600px;margin: auto;">



                    <div class="error" style="display:none; text-align: left;">
                        <img src="../imagenes/Target-icon.png" alt="Warning!" width="24" height="24" style="float:left; margin: 0px 10px 0px 0px; ">
                            <span></span>.<br clear="all">
                                </div>


                                <div style="text-align: center;">
                                    <form id="frmPass">
                                        <div class='contenedor_error' id='contenedor_error'></div>

                                        <div class='contenedor_exito' id='contenedor_exito'></div>
                                        <table width="100%">
                                            <tr>
                                                <td style="text-align: left; width: 30%;"><label>* Contrase&ntilde;a actual: </label></td>
                                                <td style="text-align: left; width: 70%;"><input type="password" class="required" id="txtPassword" name="txtPassword"></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left; width: 30%;"><label for="email">* Nueva contrase&ntilde;a:</label></td>
                                                <td style="text-align: left; width: 70%;"><input id="txtpass" class="required" name="txtpass" type="password"></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left; width: 30%;"><label for="email">* Repetir contrase&ntilde;a:</label></td>
                                                <td style="text-align: left; width: 70%;"><input id="txtpass2" class="required" name="txtpass2" type="password" ></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><input class="btnPrincipal" type="button" id="exito" name="exito"   value="Cambiar contrase&ntilde;a" /> </td>
                                            </tr>
                                        </table> 

                                    </form> 
                                </div>
                                </div>
                                </div>
                                </div>

                                <?php
                                $contenidoHtml->footer();
                                ?>
                                </body>
                                </html>