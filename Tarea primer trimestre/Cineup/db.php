<?php
// Datos de conexión: ajustados para tu cuenta Hostinger
$DB_HOST = 'localhost';                  // si el script corre en Hostinger; si es remoto usa el host que te indique el panel
$DB_USER = 'u336643015_Marcos';
$DB_PASS = 'Tostadora33';
$DB_NAME = 'u336643015_movies_db';      // nombre real de la BD según tu panel

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die('Error conexión DB: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');
?>