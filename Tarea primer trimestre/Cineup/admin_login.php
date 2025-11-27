<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Incluye header (ya inicializa sesión si hace falta)
require_once 'header.php';
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Login Admin</title></head>
<body>
  <main class="content container">
    <h2>Login Administrador</h2>
    <?php if (!empty($_GET['error'])): ?>
      <p style="color:red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>
    <form action="admin_auth.php" method="post">
      <div class="mb-2">
        <label class="form-label">Nombre administrador:
          <input class="form-control" type="text" name="admin_name" required>
        </label>
      </div>
      <div class="mb-2">
        <label class="form-label">ID administrador:
          <input class="form-control" type="number" name="admin_id" required>
        </label>
      </div>
      <button class="btn btn-primary" type="submit">Entrar</button>
    </form>
  </main>
  
    <?php include 'footer.php'; ?>
</body>
</html>

