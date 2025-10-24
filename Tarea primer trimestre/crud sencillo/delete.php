<?php

require __DIR__ . '/../bd_conect.php';
session_start();

if (!isset($_GET['codigo'])) {
    header('Location: index.php');
    exit;
}

$codigo = $_GET['codigo'];
try {
    $stmt = $bd->prepare('DELETE FROM usuarios WHERE codigo = ?');
    $stmt->execute([$codigo]);
    $filas = $stmt->rowCount();
    $_SESSION['flash'] = "Delete correcto. Filas borradas: $filas";
} catch (Exception $e) {
    $_SESSION['flash'] = 'Error al borrar: ' . $e->getMessage();
}
header('Location: index.php');
exit;
