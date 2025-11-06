<?php
// Header global para CineUp: navegación común a todas las páginas
// Asegúrate de llamar a session_start() antes de incluir este archivo si necesitas usar la sesión.
?>
<!-- Load Bootstrap (for layout) and site styles -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
<link href="styles.css" rel="stylesheet">
<style>
  /* Minimal layout CSS to keep footer at bottom if styles.css not loaded */
  html, body { height: 100%; margin: 0; }
  body { display: flex; flex-direction: column; min-height: 100vh; }
  main.content { flex: 1 0 auto; }
</style>
<header class="site-header bg-dark text-white py-2 mb-3">
  <div class="container d-flex align-items-center justify-content-between">
    <div class="site-brand d-flex align-items-center">
      <?php
        // Prefer cleaned transparent logo if available
        $logoPath = 'img/logo.jpeg';
        if (file_exists(__DIR__ . '/img/logo_clean.png')) {
          $logoPath = 'img/logo_clean.png';
        }
      ?>
      <img src="<?php echo $logoPath; ?>" alt="CineUp logo" style="height:40px;width:auto;margin-right:10px;" />
      <a href="movies_list.php" class="text-white text-decoration-none fw-bold h4 mb-0">CineUp</a>
    </div>
    <nav class="site-nav">
      <a href="movies_list.php" class="nav-link d-inline text-white">Inicio</a>
      <a href="orders_list.php" class="nav-link d-inline text-white">Historial</a>
      <a href="create_user.php" class="nav-link d-inline text-white">Crear usuario</a>
      <?php if (!empty($_SESSION['user_id'])): ?>
        <span class="text-white me-2">Hola, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <a href="logout.php" class="nav-link d-inline text-white">Cerrar sesión</a>
      <?php else: ?>
        <a href="login.php" class="nav-link d-inline text-white">Iniciar sesión</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
