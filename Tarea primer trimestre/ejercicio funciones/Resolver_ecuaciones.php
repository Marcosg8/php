<?php
// Incluir el archivo con las funciones
include_once 'matematicas.php';

echo "<h1>Resolver Ecuaciones de Segundo Grado</h1>";
echo "<p>Ecuaciones de la forma: ax² + bx + c = 0</p>";

// Formulario
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo '<form method="POST">';
    echo 'Coeficiente a: <input type="number" step="any" name="a" value="1" required><br><br>';
    echo 'Coeficiente b: <input type="number" step="any" name="b" value="-5"><br><br>';
    echo 'Coeficiente c: <input type="number" step="any" name="c" value="6"><br><br>';
    echo '<input type="submit" value="Resolver">';
    echo '</form>';
    echo '<p><em>Ejemplo con valores por defecto: 1x² - 5x + 6 = 0 (tiene soluciones reales)</em></p>';
} else {
    // Procesar los datos
    $a = floatval($_POST['a']);
    $b = floatval($_POST['b']);
    $c = floatval($_POST['c']);
    
    echo "<h2>Ecuación: {$a}x² ";
    echo ($b >= 0) ? "+ {$b}x " : "- " . abs($b) . "x ";
    echo ($c >= 0) ? "+ {$c} " : "- " . abs($c) . " ";
    echo "= 0</h2>";
    
    // Mostrar valores de entrada para debugging
    echo "<p><strong>Valores recibidos:</strong> a=$a, b=$b, c=$c</p>";
    
    // Calcular discriminante para mostrar información
    $discriminante = $b * $b - 4 * $a * $c;
    echo "<p><strong>Discriminante:</strong> b² - 4ac = {$b}² - 4({$a})({$c}) = $discriminante</p>";
    
    // Resolver la ecuación
    $soluciones = resolver_ecuacion_segundo_grado($a, $b, $c);
    
    if ($soluciones === false) {
        if ($a == 0) {
            echo "<p style='color: red;'>Error: El coeficiente 'a' no puede ser cero.</p>";
        } else {
            echo "<p style='color: red;'>Error: La ecuación no tiene soluciones reales (discriminante negativo).</p>";
        }
    } else {
        echo "<h3>Soluciones:</h3>";
        if ($soluciones[0] == $soluciones[1]) {
            echo "<p style='color: blue;'>Solución doble: x = " . $soluciones[0] . "</p>";
        } else {
            echo "<p style='color: green;'>x₁ = " . $soluciones[0] . "</p>";
            echo "<p style='color: green;'>x₂ = " . $soluciones[1] . "</p>";
        }
    }
    
    echo '<br><a href="' . $_SERVER['PHP_SELF'] . '">Resolver otra ecuación</a>';
}
?>