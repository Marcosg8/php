<?php

if (session_status() === PHP_SESSION_NONE) session_start();

// Restringir acceso a administradores
if (empty($_SESSION['is_admin'])) {
    header('Location: admin_login.php?error=' . urlencode('Acceso denegado'));
    exit;
}

?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Panel Admin</title></head>
<body>
<?php include 'header.php'; ?>
  <main class="content container py-4">
    <h2>Panel de administrador</h2>
    <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>.</p>

    <div class="d-flex flex-column flex-md-row gap-3 mt-4">
      <a class="btn btn-primary btn-lg" href="admin_panel.php">Modificar cartelera</a>
      <a class="btn btn-primary btn-lg" href="view_purchases.php">Ver compras realizadas</a>
    </div>
    
  </main>
<?php include 'footer.php'; ?>
</body>
</html>

