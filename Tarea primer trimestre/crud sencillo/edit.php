<?php
require __DIR__ . '/../bd_conect.php';
session_start();

$errors = [];
$usuario = null;

// carga por codigo o por clave (prefiere codigo si se pasa)
if (isset($_GET['codigo'])) {
    $codigoGet = $_GET['codigo'];
    $stmt = $bd->prepare('SELECT * FROM usuarios WHERE codigo = ? LIMIT 1');
    $stmt->execute([$codigoGet]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$usuario) {
        $errors[] = 'No se encontró usuario con ese código.';
    }
} elseif (isset($_GET['clave'])) {
    $clave = $_GET['clave'];
    $stmt = $bd->prepare('SELECT * FROM usuarios WHERE clave = ? LIMIT 1');
    $stmt->execute([$clave]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$usuario) {
        $errors[] = 'No se encontró usuario con esa clave.';
    }
}

// normalizar claves del array $usuario (por si la BD devuelve nombres diferentes)
if ($usuario && is_array($usuario)) {
    $usuarioNorm = [];
    foreach ($usuario as $k => $v) {
        $usuarioNorm[strtolower($k)] = $v;
    }
    $usuario = $usuarioNorm; // ahora las claves están en minúsculas: codigo, nombre, clave, rol
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = $_POST['codigo'] ?? null;
    $nombre = $_POST['nombre'] ?? '';
    $clave = $_POST['clave'] ?? '';
    $rol = $_POST['rol'] ?? 0;

    if (!$codigo) {
        $errors[] = 'Código inválido para actualizar.';
    }

    if (empty($errors)) {
        try {
            $stmt = $bd->prepare('UPDATE usuarios SET nombre = ?, clave = ?, rol = ? WHERE codigo = ?');
            $stmt->execute([$nombre, $clave, $rol, $codigo]);
            $filas = $stmt->rowCount();
            $_SESSION['flash'] = "Update correcto. Filas actualizadas: $filas";
            header('Location: index.php');
            exit;
        } catch (Exception $e) {
            $errors[] = 'Error al actualizar: ' . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modificar usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h1 class="h4 mb-3">Modificar usuario</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $err): ?><li><?= htmlspecialchars($err) ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($usuario || $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <?php
        // en caso de POST, rellenar con datos enviados; si venimos por GET usar $usuario
        $codigoVal = $_POST['codigo'] ?? ($usuario['codigo'] ?? '');
        $nombreVal = $_POST['nombre'] ?? ($usuario['nombre'] ?? '');
        $claveVal = $_POST['clave'] ?? ($usuario['clave'] ?? '');
        $rolVal = $_POST['rol'] ?? ($usuario['rol'] ?? 0);
    ?>
    <div class="card">
        <div class="card-body">
            <form method="post">
                <input type="hidden" name="codigo" value="<?= htmlspecialchars($codigoVal) ?>">
                <div class="mb-3">
                    <label class="form-label">Código</label>
                    <input class="form-control" value="<?= htmlspecialchars($codigoVal) ?>" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input name="nombre" class="form-control" required value="<?= htmlspecialchars($nombreVal) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Clave</label>
                    <input name="clave" class="form-control" required value="<?= htmlspecialchars($claveVal) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Rol</label>
                    <input name="rol" type="number" class="form-control" value="<?= htmlspecialchars($rolVal) ?>">
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary">Guardar cambios</button>
                    <a class="btn btn-outline-secondary" href="index.php">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
    <?php else: ?>
        <div class="alert alert-info">Proporcione el código o la clave del usuario a modificar mediante el formulario o en la URL.</div>
        <div class="card mb-3">
            <div class="card-body">
                <form method="get" class="row g-2">
                    <div class="col-auto">
                        <input name="codigo" class="form-control" placeholder="Código (opcional)">
                    </div>
                    <div class="col-auto">
                        <input name="clave" class="form-control" placeholder="Clave (opcional)">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary">Buscar</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
