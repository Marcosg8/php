<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin_login.php');
    exit;
}

$admin_name = trim($_POST['admin_name'] ?? '');
$admin_id   = trim($_POST['admin_id'] ?? '');

if ($admin_name === '' || $admin_id === '') {
    header('Location: admin_login.php?error=' . urlencode('Datos incompletos'));
    exit;
}

// Aquí validamos contra la tabla `admins` de la BD.
// Ajusta $dbHost, $dbName, $dbUser y $dbPass según tu entorno.
try {
    // Credenciales Hostinger
    $dbHost = 'localhost';
    $dbName = 'u336643015_movies_db';
    $dbUser = 'u336643015_Marcos';
    $dbPass = 'Tostadora33';
    $dbPort = 3306; // normalmente 3306 en Hostinger; cámbialo si tu panel indica otro puerto

    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Si tu tabla sólo tiene id y name (como en la SQL), comparamos ambos.
    $stmt = $pdo->prepare('SELECT id, name FROM admins WHERE id = ? AND name = ? LIMIT 1');
    $stmt->execute([$admin_id, $admin_name]);
    $admin = $stmt->fetch();

    if ($admin) {
        $_SESSION['is_admin']  = true;
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_id']   = $admin['id'];

        header('Location: admin_dashboard.php');
        exit;
    } else {
        header('Location: admin_login.php?error=' . urlencode('Credenciales inválidas'));
        exit;
    }
} catch (PDOException $e) {
    // Registrar detalle para depuración (no mostrar al usuario en producción)
    error_log('DB error admin_auth.php: ' . $e->getMessage());
    header('Location: admin_login.php?error=' . urlencode('Error de conexión a la base de datos'));
    exit;
}