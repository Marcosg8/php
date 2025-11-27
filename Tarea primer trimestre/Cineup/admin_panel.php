<?php
session_start();
require_once 'db.php';
if (empty($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

// Buscar películas
$movies = [];
$res = $mysqli->query("SELECT * FROM movies ORDER BY id");
while ($r = $res->fetch_assoc()) $movies[] = $r;

// Si se pide editar, obtener datos de la peli
$edit = null;
if (!empty($_GET['edit_id'])) {
    $eid = (int)$_GET['edit_id'];
    $s = $mysqli->prepare("SELECT * FROM movies WHERE id = ?");
    $s->bind_param('i', $eid);
    $s->execute();
    $edit = $s->get_result()->fetch_assoc();
    $s->close();
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Panel Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link href="styles.css" rel="stylesheet">
</head>
<body>
<?php if (file_exists(__DIR__ . '/header.php')) include 'header.php'; ?>
<main class="container my-4">
  <!-- botón cuadrado "Volver al menu admin" con fondo naranja -->
    <a href="admin_dashboard.php" class="btn btn-accent btn-sm me-2 position-relative d-inline-flex justify-content-center align-items-center"
       style="width:40px;height:34px;padding:0;background:#ff7a18;border-color:#e06b12;color:#fff;"
       aria-label="Volver al catálogo" title="Volver al catálogo">
      <img src="https://img.icons8.com/?size=100&id=60SNKbzxJ9sc&format=png&color=000000"
           alt="Volver al catálogo" style="width:20px;height:20px;object-fit:contain;display:block;">
    </a>
  <h2>Panel Administrador (<?php echo htmlspecialchars($_SESSION['admin_name']); ?>) - <a href="logout_admin.php" class="btn btn-sm btn-danger">Cerrar sesión</a></h2>

  <h3>Añadir película</h3>
  <form action="add_movie.php" method="post" class="row g-2 mb-4">
    <div class="col-4"><input name="title" class="form-control" placeholder="Título" required></div>
    <div class="col-2"><input name="genre" class="form-control" placeholder="Género"></div>
    <div class="col-1"><input name="year" type="number" class="form-control" placeholder="Año"></div>
    <div class="col-2"><input name="price" type="number" step="0.01" class="form-control" placeholder="Precio" required></div>
    <div class="col-1"><input name="stock" type="number" class="form-control" placeholder="Stock" required></div>
    <div class="col-2"><button type="submit" class="btn btn-primary">Añadir</button></div>
  </form>

  <h3>Películas</h3>
  <table class="table table-sm table-striped">
    <thead><tr><th>ID</th><th>Título</th><th>Género</th><th>Año</th><th>Precio</th><th>Stock</th><th>Acciones</th></tr></thead>
    <tbody>
    <?php foreach ($movies as $m): ?>
      <tr>
        <td><?php echo $m['id']; ?></td>
        <td><?php echo htmlspecialchars($m['title']); ?></td>
        <td><?php echo htmlspecialchars($m['genre']); ?></td>
        <td><?php echo $m['year']; ?></td>
        <td><?php echo $m['price']; ?></td>
        <td><?php echo $m['stock']; ?></td>
        <td>
          <a class="btn btn-primary" href="admin_panel.php?edit_id=<?php echo $m['id']; ?>">Editar</a>
          <form action="delete_movie.php" method="post" style="display:inline" onsubmit="return confirm('Borrar película?');">
            <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
            <button class="btn btn-danger" type="submit">Borrar</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <?php if ($edit): ?>
    <h3>Editar película ID <?php echo $edit['id']; ?></h3>
    <form action="edit_movie.php" method="post" class="row g-2">
      <input type="hidden" name="id" value="<?php echo $edit['id']; ?>">
      <div class="col-4"><input name="title" class="form-control" value="<?php echo htmlspecialchars($edit['title']); ?>" required></div>
      <div class="col-2"><input name="genre" class="form-control" value="<?php echo htmlspecialchars($edit['genre']); ?>"></div>
      <div class="col-1"><input name="year" type="number" class="form-control" value="<?php echo $edit['year']; ?>"></div>
      <div class="col-2"><input name="price" type="number" step="0.01" class="form-control" value="<?php echo $edit['price']; ?>" required></div>
      <div class="col-1"><input name="stock" type="number" class="form-control" value="<?php echo $edit['stock']; ?>" required></div>
      <div class="col-2"><button class="btn btn-primary" type="submit">Guardar cambios</button></div>
    </form>
  <?php endif; ?>

</main>
</body>
</html>

