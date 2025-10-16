<?php
$arrl = [
0 => 555,
1 => 666,
2 => 777,
];
print_r($arrl);
echo"<br> pos 0: ".$arrl[0]."<br>";
foreach ($arrl as $clave=> $valor) {
    echo"Posición en el array es ". "$clave  "."el contenido de este array es ".$valor."<br>";
}