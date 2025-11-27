<?php
session_start();
require 'db.php';

// Si ya está logueado redirigimos
if (!empty($_SESSION['user_id'])) {
    header('Location: movies_list.php');
    exit;
}

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($username === '' || $password === '' || $password2 === '') {
        $error = 'Rellena todos los campos.';
    } elseif ($password !== $password2) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        // Comprueba si el usuario existe
        $stmt = $mysqli->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->fetch_assoc()) {
            $error = 'El nombre de usuario ya existe.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $mysqli->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
            $ins->bind_param('ss', $username, $hash);
            if ($ins->execute()) {
                // Fue creado; redirigir al login
                header('Location: index.php?msg=' . urlencode('Cuenta creada. Por favor inicia sesión.'));
                exit;
            } else {
                $error = 'Error al crear la cuenta. Intenta más tarde.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>CineUp - Crear usuario</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <main class="content">
        <div class="container auth-wrapper" style="max-width:720px;margin:28px auto;padding:20px">
            <div class="movie-card auth-card">
                <!-- Logo pequeño justo encima del formulario -->
                <img src="img/logo.png" alt="Logo" style="display:block;margin:0 auto 12px auto;width:80px;height:80px;object-fit:cover;border-radius:50%;box-shadow:0 2px 6px rgba(0,0,0,0.25);">
                <h1 style="text-align:center;margin-top:0">Crear usuario</h1>

                <?php if ($error): ?>
                    <div style="color:#fff;background:#d63384;padding:10px;border-radius:6px;margin-bottom:12px"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div style="color:#111;background:#ffc107;padding:10px;border-radius:6px;margin-bottom:12px"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="post" novalidate>
                    <div style="margin-bottom:10px">
                        <label for="username">Usuario</label>
                        <input id="username" name="username" required autofocus style="width:100%" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" />
                    </div>

                    <div style="margin-bottom:10px">
                        <label for="password">Contraseña</label>
                        <input id="password" name="password" type="password" required style="width:100%" />
                    </div>

                    <div style="margin-bottom:14px">
                        <label for="password2">Repetir contraseña</label>
                        <input id="password2" name="password2" type="password" required style="width:100%" />
                    </div>

                    <div style="display:flex;gap:8px;align-items:center">
                        <button type="submit" class="btn btn-accent">Crear cuenta</button>
                        <a href="index.php" class="btn" style="background:transparent;border:1px solid rgba(255,255,255,0.06);color:var(--surface-text);padding:8px 12px;border-radius:6px;text-decoration:none">Volver a entrar</a>
                    </div>
                </form>

               
            </div>
        </div>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>


