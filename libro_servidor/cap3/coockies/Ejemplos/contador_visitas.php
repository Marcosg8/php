<?php
if (!isset($_COOKIE['visitas'])) { // si no existe
setcookie('visitas'
, '1', time() + 3600 * 24);
echo "Bienvenido por primera vez";
} else { // si existe
$visitas = (int) $_COOKIE['visitas'];
$visitas++; // se reescribe incrementada
setcookie('visitas'
, $visitas, time() + 3600 * 24);
echo "Bienvenido por $visitas vez";
}