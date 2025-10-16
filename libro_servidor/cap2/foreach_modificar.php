<?php
$arr1 = array(
    "viernes" => 22,
    "sabado" => 34,
);
foreach ($arr1 as $cantidad) {
    $cantidad =$cantidad *2;
    echo "Cantidad: $cantidad <br>";
}
print_r($arr1);
echo"<br>";
foreach ($arr1 as $cantidad) {
    $cantidad =$cantidad *2;
}
print_r($arr1); 