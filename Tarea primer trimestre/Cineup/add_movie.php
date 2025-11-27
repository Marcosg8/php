<?php

session_start();
require_once 'db.php';

if (empty($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

$title = trim($_POST['title'] ?? '');
$genre = $_POST['genre'] ?? null;
$year = $_POST['year'] !== '' ? (int)$_POST['year'] : null;
$price = isset($_POST['price']) ? (float)$_POST['price'] : 0.0;
$stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;

if ($title === '') {
    header('Location: admin_panel.php');
    exit;
}

$stmt = $mysqli->prepare("INSERT INTO movies (title, genre, year, price, stock) VALUES (?, ?, ?, ?, ?)");
if ($stmt) {
    $stmt->bind_param('ssidi', $title, $genre, $year, $price, $stock);
    $stmt->execute();
    $stmt->close();
}

header('Location: admin_panel.php');
exit;
?>

