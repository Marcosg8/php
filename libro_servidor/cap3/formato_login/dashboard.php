<?php
require_once __DIR__ . '/header.php';
require_login();

$user = $_SESSION['user'];

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Dashboard</title>
    <style>body{font-family:Arial;margin:2rem}</style>
</head>
<body>
    <h1>Panel de usuario</h1>
    <p>Bienvenido, <strong><?=htmlspecialchars($user['name'])?></strong> (<?=htmlspecialchars($user['email'])?>)</p>
    <p><a href="logout.php">Cerrar sesión</a></p>
</body>
</html>
