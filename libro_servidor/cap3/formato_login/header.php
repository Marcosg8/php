<?php
// header.php - funciones comunes y arranque de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in(): bool {
    return !empty($_SESSION['user']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function get_users_path(): string {
    return __DIR__ . DIRECTORY_SEPARATOR . 'users.json';
}

function load_users(): array {
    $path = get_users_path();
    if (!file_exists($path)) return [];
    $json = file_get_contents($path);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function save_users(array $users): bool {
    $path = get_users_path();
    $json = json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($path, $json) !== false;
}

function find_user_by_email(string $email): ?array {
    $users = load_users();
    foreach ($users as $u) {
        if (isset($u['email']) && mb_strtolower($u['email']) === mb_strtolower($email)) {
            return $u;
        }
    }
    return null;
}
