<?php
// dime_tu_dinero.php - muestra el dinero si llegaste en menos de 10 segundos desde comprobar.php
session_start();

// Comprobar que las variables de sesión existen
if (!isset($_SESSION['time']) || !isset($_SESSION['money'])) {
    // No hay sesión válida
    header('Location: login.php');
    exit;
}

$now = time();
$elapsed = $now - intval($_SESSION['time']);

if ($elapsed <= 10) {
    // Mostrar dinero
    $money = intval($_SESSION['money']);
    ?>
    <!doctype html>
    <html lang="es">
    <head>
      <meta charset="utf-8">
      <title>Tu dinero</title>
    </head>
    <body>
      <h1>Tu dinero</h1>
      <p>Han pasado <?php echo $elapsed; ?> segundos desde comprobar.php</p>
      <p>Tienes <?php echo $money; ?> euros.</p>
      <p><a href="logout.php">Cerrar sesión</a></p>
    </body>
    </html>
    <?php
    exit;
} else {
    // Tiempo excedido: destruir sesión y redirigir al formulario
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}
