<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: movies_list.php');
    exit;
}

$movie_id = isset($_POST['movie_id']) ? (int)$_POST['movie_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
// Usamos el usuario logueado como cliente
$customer_name = $_SESSION['username'];

if ($movie_id <= 0 || $quantity <= 0) {
    $msg = 'Datos inválidos. Por favor completa el formulario correctamente.';
    header('Location: movies_list.php?msg=' . urlencode($msg));
    exit;
}

// Escapar nombre
$customer_name_esc = $mysqli->real_escape_string($customer_name);

// Llamar al procedimiento almacenado
$call = "CALL place_order($movie_id, $quantity, '$customer_name_esc', @success, @msg)";
if (!$mysqli->query($call)) {
    $error = 'Error al ejecutar el pedido: ' . $mysqli->error;
    header('Location: movies_list.php?msg=' . urlencode($error));
    exit;
}

// Limpiamos result sets pendientes
while ($mysqli->more_results() && $mysqli->next_result()) { /* vacía */ }

$res = $mysqli->query("SELECT @success AS success, @msg AS message");
$row = $res ? $res->fetch_assoc() : null;
$success = $row['success'] ?? 0;
$message = $row['message'] ?? 'No hay mensaje.';

// Redirigir al listado con mensaje
header('Location: movies_list.php?msg=' . urlencode($message));
exit;
?>