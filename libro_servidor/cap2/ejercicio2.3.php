<?php
function factorial($n) {
    if (!is_int($n) || $n < 0) {
        return -1;
    }
    if ($n === 0 || $n === 1) {
        return 1;
    }
    $resultado = 1;
    for ($i = 2; $i <= $n; $i++) {
        $resultado *= $i;
    }
    return $resultado;
}

echo factorial(5);  
echo "<br>";
echo factorial(-3); 
echo "<br>";
echo factorial(0);  
?>