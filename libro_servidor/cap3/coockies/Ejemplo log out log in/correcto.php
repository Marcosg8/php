<?php
// correcto.php - página intermedia después de comprobar.php
session_start();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Correcto</title>
</head>
<body>
  <h1>Has accedido correctamente</h1>
  <?php if (isset($_SESSION['token'])): ?>
    <p>Token de sesión: <?php echo htmlspecialchars($_SESSION['token']); ?></p>
  <?php endif; ?>
  <p><a href="dime_tu_dinero.php">Ir a dime_tu_dinero.php</a></p>
  <p><a href="logout.php">Cerrar sesión</a></p>
</body>
</html>
