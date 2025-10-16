<?php
require_once __DIR__ . '/header.php';

$errors = [];
$old = ['name' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    $old['name'] = $name;
    $old['email'] = $email;

    if ($name === '') $errors[] = 'El nombre es obligatorio.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';
    if (strlen($password) < 6) $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
    if ($password !== $password2) $errors[] = 'Las contraseñas no coinciden.';

    if (empty($errors)) {
        if (find_user_by_email($email)) {
            $errors[] = 'Ya existe un usuario con ese email.';
        } else {
            $users = load_users();
            $users[] = [
                'id' => uniqid('', true),
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'created_at' => date('c'),
            ];
            if (save_users($users)) {
                // Auto-login después de registro
                $_SESSION['user'] = ['name' => $name, 'email' => $email];
                header('Location: dashboard.php');
                exit;
            } else {
                $errors[] = 'Error guardando usuario. Comprueba permisos en la carpeta.';
            }
        }
    }
}

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Registro</title>
    <style>body{font-family:Arial;margin:2rem}form{max-width:400px}</style>
</head>
<body>
    <h1>Registro</h1>
    <?php if ($errors): ?>
        <div style="color:#900">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?=htmlspecialchars($e)?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" novalidate>
        <div>
            <label>Nombre<br>
                <input type="text" name="name" value="<?=htmlspecialchars($old['name'])?>">
            </label>
        </div>
        <div>
            <label>Email<br>
                <input type="email" name="email" value="<?=htmlspecialchars($old['email'])?>">
            </label>
        </div>
        <div>
            <label>Contraseña<br>
                <input type="password" name="password">
            </label>
        </div>
        <div>
            <label>Repetir contraseña<br>
                <input type="password" name="password2">
            </label>
        </div>
        <div style="margin-top:1rem">
            <button type="submit">Registrar</button>
            <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a>
        </div>
    </form>
</body>
</html>
