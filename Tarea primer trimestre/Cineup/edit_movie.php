<?php

session_start();
require_once 'db.php';

if (empty($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$genre = $_POST['genre'] ?? null;
$year = $_POST['year'] !== '' ? (int)$_POST['year'] : null;
$price = isset($_POST['price']) ? (float)$_POST['price'] : 0.0;
$stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;

if ($id <= 0 || $title === '') {
    header('Location: admin_panel.php');
    exit;
}

$stmt = $mysqli->prepare("UPDATE movies SET title = ?, genre = ?, year = ?, price = ?, stock = ? WHERE id = ?");
$stmt->bind_param('ssiddi', $title, $genre, $year, $price, $stock, $id);
$stmt->execute();
$stmt->close();

header('Location: admin_panel.php');
exit;
?>