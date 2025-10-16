<?php
function potencia($base, $exponente = 2) {
    return pow($base, $exponente);
}

echo potencia(3);      // 9 (3^2)
echo "<br>";
echo potencia(2, 3);  // 8 (2^3)
?>