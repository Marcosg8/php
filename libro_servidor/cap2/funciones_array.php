<?php

// Ejemplo de array asociativo
$arr = [
    "b" => 2,
    "a" => 1,
    "c" => 3
];

// ksort: Ordena el array por clave en orden ascendente (A-Z)
// Las claves serán: a, b, c
ksort($arr);
echo "ksort: ";
print_r($arr);

// krsort: Ordena el array por clave en orden descendente (Z-A)
// Las claves serán: c, b, a
krsort($arr);
echo "<br>krsort: ";
print_r($arr);

// sort: Ordena el array por valor en orden ascendente (menor a mayor)
// Este método elimina las claves originales y crea índices numéricos
$arr2 = [3, 1, 2];
sort($arr2);
echo "<br>sort: ";
print_r($arr2);

// rsort: Ordena el array por valor en orden descendente (mayor a menor)
// También elimina las claves originales y crea índices numéricos
rsort($arr2);
echo "<br>rsort: ";
print_r($arr2);

// array_values: Devuelve un array con solo los valores del array original
// Las claves serán numéricas (0, 1, 2)
echo "<br>array_values: ";
print_r(array_values($arr));

// array_keys: Devuelve un array con solo las claves del array original
echo "<br>array_keys: ";
print_r(array_keys($arr));

// array_key_exists: Verifica si existe la clave 'a' en el array
// Devuelve 'Sí' si existe, 'No' si no existe
echo "<br>array_key_exists('a'): ";
echo array_key_exists('a', $arr) ? 'Sí' : 'No';

// count: Devuelve el número de elementos del array
echo "<br>count: ";
echo count($arr);
?>