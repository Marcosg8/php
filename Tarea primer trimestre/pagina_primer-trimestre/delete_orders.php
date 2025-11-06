<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: orders_list.php');
    exit;
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
if (!$username) {
    $_SESSION['msg'] = 'Usuario no identificado.';
    header('Location: orders_list.php');
    exit;
}

// Prepared statement to avoid SQL injection
if ($stmt = $mysqli->prepare('DELETE FROM orders WHERE customer_name = ?')) {
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();

    $_SESSION['msg'] = "Se han eliminado $affected pedido(s).";
} else {
    $_SESSION['msg'] = 'Error al preparar la eliminación de pedidos.';
}

header('Location: orders_list.php');
exit;
