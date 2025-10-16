<?php
if(empty($_GET["num1"]) && empty($_GET["num2"]) ){
    echo"Faltan parametros";
}else if (empty($_GET["num1"]) || empty($_GET["num2"])){
    echo "Error faltan parametros";
}else{
    echo "La suma es: ".($_GET["num1"]+$_GET["num2"]);
}
?>