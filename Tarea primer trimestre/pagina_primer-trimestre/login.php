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
</head>
<body>
    <?php include 'header.php'; ?>
    <main class="content">
        <div class="container" style="max-width:520px;margin:28px auto;padding:20px">
            <div class="movie-card">
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

                <p style="margin-top:12px;color:var(--muted);font-size:0.95rem">¿Olvidaste la contraseña? Contacta con el administrador.</p>
            </div>
        </div>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>