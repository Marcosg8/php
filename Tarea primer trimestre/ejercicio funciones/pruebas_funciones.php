<?php
// Incluir el archivo con las funciones
include_once 'matematicas.php';

echo "<h1>Pruebas de Funciones</h1>";

echo "<h2>1. Pruebas de resolver_ecuacion_segundo_grado()</h2>";

// Caso 1: Dos soluciones reales
echo "<h3>Caso 1: x² - 5x + 6 = 0</h3>";
$resultado1 = resolver_ecuacion_segundo_grado(1, -5, 6);
if ($resultado1 !== false) {
    echo "Soluciones: x₁ = " . $resultado1[0] . ", x₂ = " . $resultado1[1] . "<br>";
} else {
    echo "No hay soluciones reales<br>";
}

// Caso 2: Solución doble
echo "<h3>Caso 2: x² - 4x + 4 = 0</h3>";
$resultado2 = resolver_ecuacion_segundo_grado(1, -4, 4);
if ($resultado2 !== false) {
    echo "Solución doble: x = " . $resultado2[0] . "<br>";
} else {
    echo "No hay soluciones reales<br>";
}

// Caso 3: Sin soluciones reales
echo "<h3>Caso 3: x² + x + 1 = 0</h3>";
$resultado3 = resolver_ecuacion_segundo_grado(1, 1, 1);
if ($resultado3 !== false) {
    echo "Soluciones: x₁ = " . $resultado3[0] . ", x₂ = " . $resultado3[1] . "<br>";
} else {
    echo "No hay soluciones reales<br>";
}

// Caso 4: a = 0
echo "<h3>Caso 4: 0x² + 2x + 1 = 0</h3>";
$resultado4 = resolver_ecuacion_segundo_grado(0, 2, 1);
if ($resultado4 !== false) {
    echo "Soluciones: x₁ = " . $resultado4[0] . ", x₂ = " . $resultado4[1] . "<br>";
} else {
    echo "Error: No es una ecuación de segundo grado<br>";
}

echo "<hr>";

echo "<h2>2. Pruebas de es_palindromo()</h2>";

$palabras = array("ana", "radar", "hello", "A man a plan a canal Panama", "race car");

foreach ($palabras as $palabra) {
    $es_palindromo = es_palindromo($palabra);
    echo "\"$palabra\" " . ($es_palindromo ? "ES" : "NO ES") . " un palíndromo<br>";
}

echo "<hr>";

echo "<h2>3. Pruebas de filtrar_menores_que()</h2>";

$numeros = array(1, 5, 3, 8, 2, 9, 4);
$limite = 5;

echo "Array original: [" . implode(", ", $numeros) . "]<br>";
echo "Límite: $limite<br>";

$filtrados = filtrar_menores_que($numeros, $limite);
echo "Números menores que $limite: [" . implode(", ", $filtrados) . "]<br>";

// Otro caso
$numeros2 = array(10, 20, 30, 40, 50);
$limite2 = 25;

echo "<br>Array original: [" . implode(", ", $numeros2) . "]<br>";
echo "Límite: $limite2<br>";

$filtrados2 = filtrar_menores_que($numeros2, $limite2);
echo "Números menores que $limite2: [" . implode(", ", $filtrados2) . "]<br>";

?>