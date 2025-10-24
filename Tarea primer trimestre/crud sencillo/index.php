<?php
session_start();
require __DIR__ . '/../bd_conect.php';

// obtener todos los usuarios
try {
    $stmt = $bd->query('SELECT * FROM usuarios ORDER BY codigo');
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $usuarios = [];
    $error = $e->getMessage();
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRUD sencillo - Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4">Usuarios</h1>
        <a href="add.php" class="btn btn-primary">Añadir usuario</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($_SESSION['flash']); ?></div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Clave</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($usuarios)): ?>
                    <tr><td colspan="5" class="text-center">No hay usuarios</td></tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $u): ?>
                        <?php
                            // evitar warnings si alguna columna falta
                            $codigoVal = isset($u['codigo']) ? $u['codigo'] : (isset($u['Codigo']) ? $u['Codigo'] : '');
                            $nombreVal = isset($u['nombre']) ? $u['nombre'] : (isset($u['Nombre']) ? $u['Nombre'] : '');
                            $claveVal = isset($u['clave']) ? $u['clave'] : (isset($u['Clave']) ? $u['Clave'] : '');
                            $rolVal = isset($u['rol']) ? $u['rol'] : (isset($u['Rol']) ? $u['Rol'] : '');
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($codigoVal); ?></td>
                            <td><?php echo htmlspecialchars($nombreVal); ?></td>
                            <td><?php echo htmlspecialchars($claveVal); ?></td>
                            <td><?php echo htmlspecialchars($rolVal); ?></td>
                            <td>
                                <a href="edit.php?codigo=<?php echo urlencode($codigoVal); ?>" class="btn btn-sm btn-outline-secondary">Modificar</a>
                                <a href="delete.php?codigo=<?php echo urlencode($codigoVal); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Borrar registro ' + <?php echo json_encode(htmlspecialchars($nombreVal)); ?> + ' (codigo ' + <?php echo json_encode(htmlspecialchars($codigoVal)); ?> + ')?')">Borrar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
