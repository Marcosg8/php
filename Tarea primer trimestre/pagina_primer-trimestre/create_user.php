
<?php
// Script pequeño para crear usuarios con contraseña hasheada (bcrypt via password_hash).
// Úsalo desde el navegador o desde CLI: php create_user.php usuario contraseña
require 'db.php';
session_start();

if (php_sapi_name() === 'cli') {
    $username = $argv[1] ?? null;
    $password = $argv[2] ?? null;
    $role = $argv[3] ?? 'user';
} else {
    $username = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;
    // only allow role selection if current user is admin
    $role = 'user';
    if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        $role = $_POST['role'] ?? 'user';
    }
}

if (!$username || !$password) {
        if (php_sapi_name() === 'cli') {
                echo "Uso: php create_user.php usuario contraseña\n";
        } else {
                // formulario simple con estilo
                ?>
                <!DOCTYPE html>
                <html lang="es">
                <head><meta charset="utf-8"><title>CineUp - Crear usuario</title></head>
                <body>
                <?php include 'header.php'; ?>
                <main class="content">
                    <div class="container" style="max-width:520px;margin:28px auto;padding:20px">
                        <div class="movie-card">
                            <h1 style="text-align:center;margin-top:0">Crear usuario</h1>

                            <form method="post" novalidate>
                                <div style="margin-bottom:10px">
                                    <label for="username">Usuario</label>
                                    <input id="username" name="username" required style="width:100%" />
                                </div>

                                <div style="margin-bottom:14px">
                                    <label for="password">Contraseña</label>
                                    <input id="password" name="password" type="password" required style="width:100%" />
                                </div>

                                                                <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                                                    <div style="margin-bottom:10px">
                                                                        <label for="role">Rol</label>
                                                                        <select id="role" name="role" style="width:100%">
                                                                            <option value="user">Usuario</option>
                                                                            <option value="admin">Administrador</option>
                                                                        </select>
                                                                    </div>
                                                                <?php endif; ?>

                                                                <div style="display:flex;gap:8px;align-items:center">
                                    <button type="submit" class="btn btn-accent">Crear cuenta</button>
                                    <a href="login.php" class="btn" style="background:transparent;border:1px solid rgba(255,255,255,0.06);color:var(--surface-text);padding:8px 12px;border-radius:6px;text-decoration:none">Volver a entrar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </main>
                <?php include 'footer.php'; ?>
                </body></html>
                <?php
        }
    exit;
}

// Ensure users table has a 'role' column. If not, try to add it (one-time migration)
$res = $mysqli->query("SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='users' AND COLUMN_NAME='role'");
$row = $res->fetch_assoc();
if ((int)$row['c'] === 0) {
    $mysqli->query("ALTER TABLE users ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'user'");
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $mysqli->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
$stmt->bind_param('sss', $username, $hash, $role);
try {
    $stmt->execute();
    echo "Usuario creado correctamente: $username\n";
} catch (Exception $e) {
    echo "Error al crear usuario: " . $e->getMessage();
}

?>
