<?php
require_once __DIR__ . '/header.php';

$errors = [];
$old = ['email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $old['email'] = $email;

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';
    if ($password === '') $errors[] = 'Contraseña requerida.';

    if (empty($errors)) {
        $user = find_user_by_email($email);
        if (!$user || !isset($user['password']) || !password_verify($password, $user['password'])) {
            $errors[] = 'Email o contraseña incorrectos.';
        } else {
            // Login correcto
            $_SESSION['user'] = ['name' => $user['name'], 'email' => $user['email']];
            header('Location: dashboard.php');
            exit;
        }
    }
}

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Login</title>
    <style>body{font-family:Arial;margin:2rem}form{max-width:400px}</style>
</head>
<body>
    <h1>Iniciar sesión</h1>
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
            <label>Email<br>
                <input type="email" name="email" value="<?=htmlspecialchars($old['email'])?>">
            </label>
        </div>
        <div>
            <label>Contraseña<br>
                <input type="password" name="password">
            </label>
        </div>
        <div style="margin-top:1rem">
            <button type="submit">Entrar</button>
            <a href="register.php">Crear cuenta</a>
        </div>
    </form>
</body>
</html>
