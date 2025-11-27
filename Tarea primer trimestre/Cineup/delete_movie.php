<?php

session_start();
require_once 'db.php';

if (empty($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: admin_panel.php');
    exit;
}

$stmt = $mysqli->prepare("DELETE FROM movies WHERE id = ?");
if ($stmt) {
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

header('Location: admin_panel.php');
exit;
?>
