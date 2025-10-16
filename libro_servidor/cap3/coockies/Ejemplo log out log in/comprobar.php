<?php
// comprobar.php - valida credenciales y crea variables de sesión
session_start();

// Validación simple: aceptar cualquier usuario/contraseña no vacíos
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$user = isset($_POST['user']) ? trim($_POST['user']) : '';
$pass = isset($_POST['pass']) ? trim($_POST['pass']) : '';

if ($user === '' || $pass === '') {
    // Volver al formulario si faltan datos
    header('Location: login.php');
    exit;
}

// Crear token de sesión (número aleatorio)
$_SESSION['token'] = random_int(100000, 999999);
// Establecer dinero
$_SESSION['money'] = 300;
// Establecer tiempo actual
$_SESSION['time'] = time();

// Redirigir a correcto.php
header('Location: correcto.php');
exit;
