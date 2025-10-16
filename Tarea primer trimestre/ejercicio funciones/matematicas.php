<?php

// Función para resolver ecuaciones de segundo grado ax² + bx + c = 0
function resolver_ecuacion_segundo_grado($a, $b, $c) {
    // Verificar que 'a' no sea cero
    if ($a == 0) {
        return false;
    }
    
    // Calcular el discriminante
    $discriminante = $b * $b - 4 * $a * $c;
    
    // Si el discriminante es negativo, no hay soluciones reales
    if ($discriminante < 0) {
        return false;
    }
    
    // Si el discriminante es cero, hay una solución doble
    if ($discriminante == 0) {
        $x = -$b / (2 * $a);
        return [$x, $x];
    }
    
    // Si el discriminante es positivo, hay dos soluciones
    $x1 = (-$b + sqrt($discriminante)) / (2 * $a);
    $x2 = (-$b - sqrt($discriminante)) / (2 * $a);
    
    return [$x1, $x2];
}

// Función para comprobar si una cadena es un palíndromo
function es_palindromo($cadena) {
    // Convertir a minúsculas y eliminar espacios
    $cadena_limpia = strtolower(str_replace(' ', '', $cadena));
    
    // Comparar la cadena con su reverso
    return $cadena_limpia === strrev($cadena_limpia);
}

// Función para filtrar números menores que el límite
function filtrar_menores_que($numeros, $limite) {
    $resultado = array();
    
    foreach ($numeros as $numero) {
        if (is_numeric($numero) && $numero < $limite) {
            $resultado[] = $numero;
        }
    }
    
    return $resultado;
}

// Si se ejecuta este archivo directamente, mostrar información
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    echo "<h1>Librería de Funciones Matemáticas</h1>";
    echo "<p>Este archivo contiene las siguientes funciones:</p>";
    
    echo "<h2>Funciones disponibles:</h2>";
    echo "<ul>";
    echo "<li><strong>resolver_ecuacion_segundo_grado(\$a, \$b, \$c)</strong> - Resuelve ecuaciones ax² + bx + c = 0</li>";
    echo "<li><strong>es_palindromo(\$cadena)</strong> - Verifica si una cadena es palíndromo</li>";
    echo "<li><strong>filtrar_menores_que(\$numeros, \$limite)</strong> - Filtra números menores que el límite</li>";
    echo "</ul>";
    
    echo "<h2>Ejemplo de uso:</h2>";
    echo "<p>Para usar estas funciones, incluye este archivo en tu script:</p>";
    echo "<code>include_once 'matematicas.php';</code>";
    
    echo "<h2>Ejemplos prácticos:</h2>";
    
    // Ejemplo 1: Ecuación con dos soluciones
    echo "<h3>📊 Ejemplo 1: Ecuación con dos soluciones</h3>";
    $a1 = 1; $b1 = -5; $c1 = 6;
    echo "<p><strong>Valores de entrada:</strong></p>";
    echo "<ul>";
    echo "<li>a = $a1</li>";
    echo "<li>b = $b1</li>";
    echo "<li>c = $c1</li>";
    echo "</ul>";
    echo "<p><strong>Ecuación:</strong> {$a1}x² + ({$b1})x + {$c1} = 0</p>";
    
    $resultado1 = resolver_ecuacion_segundo_grado($a1, $b1, $c1);
    echo "<p><strong>Resultado:</strong></p>";
    if ($resultado1 !== false) {
        echo "<ul>";
        echo "<li>x₁ = " . $resultado1[0] . "</li>";
        echo "<li>x₂ = " . $resultado1[1] . "</li>";
        echo "</ul>";
        echo "<p style='color: green;'>✅ Dos soluciones reales encontradas</p>";
    } else {
        echo "<p style='color: red;'>❌ No hay soluciones reales</p>";
    }
    
    echo "<hr>";
    
    // Ejemplo 2: Ecuación con solución doble
    echo "<h3>📊 Ejemplo 2: Ecuación con solución doble</h3>";
    $a2 = 1; $b2 = -4; $c2 = 4;
    echo "<p><strong>Valores de entrada:</strong></p>";
    echo "<ul>";
    echo "<li>a = $a2</li>";
    echo "<li>b = $b2</li>";
    echo "<li>c = $c2</li>";
    echo "</ul>";
    echo "<p><strong>Ecuación:</strong> {$a2}x² + ({$b2})x + {$c2} = 0</p>";
    
    $resultado2 = resolver_ecuacion_segundo_grado($a2, $b2, $c2);
    echo "<p><strong>Resultado:</strong></p>";
    if ($resultado2 !== false) {
        echo "<ul>";
        echo "<li>Solución doble: x = " . $resultado2[0] . "</li>";
        echo "</ul>";
        echo "<p style='color: blue;'>🔵 Una solución doble encontrada</p>";
    } else {
        echo "<p style='color: red;'>❌ No hay soluciones reales</p>";
    }
    
    echo "<hr>";
    
    // Ejemplo 3: Sin soluciones reales
    echo "<h3>📊 Ejemplo 3: Sin soluciones reales</h3>";
    $a3 = 1; $b3 = 1; $c3 = 1;
    echo "<p><strong>Valores de entrada:</strong></p>";
    echo "<ul>";
    echo "<li>a = $a3</li>";
    echo "<li>b = $b3</li>";
    echo "<li>c = $c3</li>";
    echo "</ul>";
    echo "<p><strong>Ecuación:</strong> {$a3}x² + {$b3}x + {$c3} = 0</p>";
    
    $resultado3 = resolver_ecuacion_segundo_grado($a3, $b3, $c3);
    echo "<p><strong>Resultado:</strong></p>";
    if ($resultado3 !== false) {
        echo "<ul>";
        echo "<li>x₁ = " . $resultado3[0] . "</li>";
        echo "<li>x₂ = " . $resultado3[1] . "</li>";
        echo "</ul>";
    } else {
        $discriminante = $b3*$b3 - 4*$a3*$c3;
        echo "<p style='color: red;'>❌ No hay soluciones reales</p>";
        echo "<p>Discriminante = {$b3}² - 4({$a3})({$c3}) = $discriminante (negativo)</p>";
    }
    
    echo "<hr>";
    
    // Ejemplo de palíndromo
    echo "<h3>📝 Prueba de palíndromos</h3>";
    $palabras_test = array("ana", "radar", "hello", "reconocer");
    echo "<p><strong>Palabras a probar:</strong> [" . implode(", ", $palabras_test) . "]</p>";
    echo "<p><strong>Resultados:</strong></p>";
    echo "<ul>";
    foreach ($palabras_test as $palabra) {
        $es_pal = es_palindromo($palabra);
        $resultado_texto = $es_pal ? "✅ ES palíndromo" : "❌ NO es palíndromo";
        echo "<li>'$palabra' → $resultado_texto</li>";
    }
    echo "</ul>";
    
    echo "<hr>";
    
    // Ejemplo de filtrado
    echo "<h3>🔢 Filtrado de números</h3>";
    $numeros_test = array(1, 5, 3, 8, 2, 9, 4);
    $limite_test = 5;
    echo "<p><strong>Array original:</strong> [" . implode(", ", $numeros_test) . "]</p>";
    echo "<p><strong>Límite:</strong> $limite_test</p>";
    
    $filtrados = filtrar_menores_que($numeros_test, $limite_test);
    echo "<p><strong>Resultado:</strong> [" . implode(", ", $filtrados) . "]</p>";
    echo "<p>Se encontraron " . count($filtrados) . " números menores que $limite_test</p>";
    
    echo "<hr>";
    echo "<p><a href='resolver_ecuaciones.php'>🧮 Ir al resolvedor de ecuaciones</a></p>";
    echo "<p><a href='pruebas_funciones.php'>🧪 Ir a las pruebas completas</a></p>";
}

?>
