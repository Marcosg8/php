<?php
session_start();
require 'db.php';

// Si ya está logueado redirigimos
if (!empty($_SESSION['user_id'])) {
    header('Location: movies_list.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Introduce usuario y contraseña.';
    } else {
        $stmt = $mysqli->prepare('SELECT id, password FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();
        if ($user && password_verify($password, $user['password'])) {
            // Login OK
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            header('Location: movies_list.php');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>CineUp - Iniciar sesión</title>
    <link rel="stylesheet" href="styles.css">


</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="content">
        <div class="container auth-wrapper" style="max-width:720px;margin:28px auto;padding:20px">
            <div class="movie-card auth-card">
                <img src="img/logo.png" alt="Logo" style="display:block;margin:0 auto 12px auto;width:80px;height:80px;object-fit:cover;border-radius:50%;box-shadow:0 2px 6px rgba(0,0,0,0.25);">
                <h1 style="text-align:center;margin-top:0">Iniciar sesión</h1>
                <?php if ($error): ?>
                    <div style="color:#fff;background:#d63384;padding:10px;border-radius:6px;margin-bottom:12px"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="post" novalidate>
                    <div style="margin-bottom:10px">
                        <label for="username">Usuario</label>
                        <input id="username" name="username" required autofocus style="width:100%" />
                    </div>
                    <div style="margin-bottom:14px">
                        <label for="password">Contraseña</label>
                        <input id="password" name="password" type="password" required style="width:100%" />
                    </div>
                    <div style="display:flex;gap:8px;align-items:center">
                        <button type="submit" class="btn btn-accent">Entrar</button>
                        <a href="create_user.php" class="btn" style="background:transparent;border:1px solid rgba(255,255,255,0.06);color:var(--surface-text);padding:8px 12px;border-radius:6px;text-decoration:none">Crear cuenta</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Theme modal (necesario para la selección de color) -->
    <div id="themeModal" class="theme-modal" aria-hidden="true" role="dialog" aria-modal="true">
      <div class="theme-modal__panel">
        <header class="theme-modal__header">
          <h2>Personaliza el color del sitio</h2>
          <h3>Aqui podra cambiar el color del header y del footer a su gusto</h3>
        </header>

        <div class="theme-modal__body">
          <div class="theme-modal__controls">
            <!-- Color por defecto más oscuro y presets en tonos profundos -->
            <input id="customColor" type="color" value="#363a3aff" aria-label="Color personalizado">
 
          </div>
        </div>

        <footer class="theme-modal__footer">
          <button id="closeTheme" class="btn btn-ghost">Cerrar</button>
          <button id="applyTheme" class="btn btn-primary">Aplicar</button>
        </footer>
      </div>
    </div>

    <!-- Bloqueador que aparece al rechazar (ocupa toda la pantalla) -->
    <div id="cookieBlocker" class="cookie-blocker" role="alertdialog" aria-hidden="true">
      <div class="cookie-blocker__card">
        <h2>Acceso denegado</h2>
        <p>Has rechazado las cookies. Algunas funcionalidades pueden no estar disponibles. Acepta las cookies para continuar en el sitio.</p>
        <div class="cookie-blocker__actions">
          <button id="acceptCookiesBlocker" class="btn btn-primary">Aceptar cookies</button>
          <a href="https://www.google.com" class="btn btn-ghost leave" target="_blank" rel="noopener">Salir</a>
        </div>
      </div>
    </div>



    <?php include 'footer.php'; ?>

    <!-- Carga del script de cookies / theme (asegúrate de que la ruta sea correcta) -->
    <script src="js/coockie.js"></script>

</body>
</html>

