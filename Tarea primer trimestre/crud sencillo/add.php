<?php
require __DIR__ . '/../bd_conect.php';
session_start();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $clave = $_POST['clave'] ?? '';
    $rol = $_POST['rol'] ?? 0;
    $codigo = $_POST['codigo'] ?? null;

    if (trim($nombre) === '') {
        $errors[] = 'El nombre es obligatorio.';
    }
    if (trim($clave) === '') {
        $errors[] = 'La clave es obligatoria.';
    }

    if (empty($errors)) {
        try {
            if ($codigo === '' || $codigo === null) {
                $stmt = $bd->prepare('INSERT INTO usuarios (nombre, clave, rol) VALUES (?, ?, ?)');
                $stmt->execute([$nombre, $clave, $rol]);
            } else {
                // intentar insertar con código si se proporciona
                $stmt = $bd->prepare('INSERT INTO usuarios (codigo, nombre, clave, rol) VALUES (?, ?, ?, ?)');
                $stmt->execute([$codigo, $nombre, $clave, $rol]);
            }
            // similar a bd_insert_update_delete.php: filas insertadas y lastInsertId
            $filas = $stmt->rowCount();
            $lastId = $bd->lastInsertId();
            $_SESSION['flash'] = "Insert correcto. Filas insertadas: $filas. Código de la fila insertada: $lastId";
            header('Location: index.php');
            exit;
        } catch (Exception $e) {
            $errors[] = 'Error al insertar: ' . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Añadir usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h1 class="h4 mb-3">Añadir usuario</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $err): ?><li><?= htmlspecialchars($err) ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Código (opcional)</label>
                    <input name="codigo" class="form-control" value="<?= isset($_POST['codigo']) ? htmlspecialchars($_POST['codigo']) : '' ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input name="nombre" class="form-control" required value="<?= isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '' ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Clave</label>
                    <input name="clave" class="form-control" required value="<?= isset($_POST['clave']) ? htmlspecialchars($_POST['clave']) : '' ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Rol</label>
                    <input name="rol" type="number" class="form-control" value="<?= isset($_POST['rol']) ? htmlspecialchars($_POST['rol']) : '0' ?>">
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary">Guardar</button>
                    <a class="btn btn-outline-secondary" href="index.php">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
