<?php
// Ajusta estas credenciales según tu entorno XAMPP
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'movies_db';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die('Error de conexión MySQL: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

// Útil para depuración local (descomentar si necesitas ver errores):
// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>