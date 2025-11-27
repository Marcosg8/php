<?php

if (session_status() === PHP_SESSION_NONE) session_start();

// Restringir acceso a administradores
if (empty($_SESSION['is_admin'])) {
    header('Location: admin_login.php?error=' . urlencode('Acceso denegado'));
    exit;
}

// Intentar usar configuración de conexión existente si hay (db.php o config.php)
if (file_exists(__DIR__ . '/db.php')) {
    require_once __DIR__ . '/db.php'; // opcional: puede definir $mysqli o variables de conexión
}

if (!isset($mysqli)) {
    $dbHost = $dbHost ?? '127.0.0.1';
    $dbUser = $dbUser ?? 'root';
    $dbPass = $dbPass ?? '';
    $dbName = $dbName ?? 'cineup';
    $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    if ($mysqli->connect_error) {
        die('Error BD: ' . htmlspecialchars($mysqli->connect_error));
    }
}

// Manejo de borrado múltiple
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['selected']) && is_array($_POST['selected'])) {
    // asegurar enteros
    $ids = array_map('intval', $_POST['selected']);
    if (count($ids) > 0) {
        // determinar nombre de PK: preferir "id" si existe, si no usar el primer campo de la tabla
        $resFields = $mysqli->query("SHOW COLUMNS FROM `orders`");
        $pk = 'id';
        if ($resFields) {
            $foundId = false;
            $firstField = null;
            while ($col = $resFields->fetch_assoc()) {
                if ($firstField === null) $firstField = $col['Field'];
                if ($col['Field'] === 'id') { $foundId = true; break; }
            }
            if (!$foundId && $firstField !== null) $pk = $firstField;
        }
        $in = implode(',', $ids); // seguros porque cast a int
        $delSql = "DELETE FROM `orders` WHERE `$pk` IN ($in)";
        $mysqli->query($delSql);
        header('Location: view_purchases.php?msg=' . urlencode('Eliminadas: ' . $mysqli->affected_rows));
        exit;
    }
}

require_once 'header.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Compras realizadas - Admin</title>
</head>
<body>
  <main class="content container py-4">
  <!-- botón cuadrado "Volver al menu admin" con fondo naranja -->
    <a href="admin_dashboard.php" class="btn btn-accent btn-sm me-2 position-relative d-inline-flex justify-content-center align-items-center"
       style="width:40px;height:34px;padding:0;background:#ff7a18;border-color:#e06b12;color:#fff;"
       aria-label="Volver al catálogo" title="Volver al catálogo">
      <img src="https://img.icons8.com/?size=100&id=60SNKbzxJ9sc&format=png&color=000000"
           alt="Volver al catálogo" style="width:20px;height:20px;object-fit:contain;display:block;">
    </a>  
  <h2>Compras realizadas</h2>

    <?php if (!empty($_GET['msg'])): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <?php
    // Obtener todas las ordenes
    $result = $mysqli->query("SELECT * FROM `orders`");
    if (!$result) {
        echo '<div class="alert alert-danger">Error al obtener pedidos: ' . htmlspecialchars($mysqli->error) . '</div>';
    } else {
        if ($result->num_rows === 0) {
            echo '<p>No hay órdenes registradas.</p>';
        } else {
            // columnas dinámicas
            $fields = $result->fetch_fields();
            ?>
            <form method="post" id="bulkDeleteForm">
              <div class="mb-2">
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirmDelete();">Eliminar seleccionadas</button>
                <button type="button" class="btn btn-danger btn-sm ms-2" id="selectAllBtn">Seleccionar todo</button>
              </div>

              <div class="table-responsive">
                <table class="table table-sm table-striped align-middle">
                  <thead class="table-dark">
                    <tr>
                      <th scope="col"><input type="checkbox" id="checkAllTop"></th>
                      <?php foreach ($fields as $f): ?>
                        <th scope="col"><?php echo htmlspecialchars($f->name); ?></th>
                      <?php endforeach; ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    // reset pointer to iterate rows (result already at first row? fetch_fields doesn't advance rows)
                    while ($row = $result->fetch_assoc()):
                      // determinar PK para checkbox: preferir 'id' o primer campo
                      $pk = array_key_exists('id', $row) ? 'id' : array_key_first($row);
                    ?>
                      <tr>
                        <td><input type="checkbox" name="selected[]" value="<?php echo (int)$row[$pk]; ?>" class="rowCheck"></td>
                        <?php foreach ($fields as $f): ?>
                          <td><?php echo htmlspecialchars((string)$row[$f->name]); ?></td>
                        <?php endforeach; ?>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            </form>
    <?php
        }
        $result->free();
    }
    ?>
  </main>

  <script>
    // Select all toggle
    const checkAllTop = document.getElementById('checkAllTop');
    const selectAllBtn = document.getElementById('selectAllBtn');
    function setAll(checked) {
      document.querySelectorAll('.rowCheck').forEach(cb => cb.checked = checked);
      if (checkAllTop) checkAllTop.checked = checked;
    }
    if (checkAllTop) {
      checkAllTop.addEventListener('change', () => setAll(checkAllTop.checked));
    }
    if (selectAllBtn) {
      selectAllBtn.addEventListener('click', () => {
        const all = Array.from(document.querySelectorAll('.rowCheck')).every(cb => cb.checked);
        setAll(!all);
      });
    }
    function confirmDelete() {
      const any = document.querySelectorAll('.rowCheck:checked').length > 0;
      if (!any) { alert('Selecciona al menos una orden para eliminar.'); return false; }
      return confirm('¿Eliminar las órdenes seleccionadas? Esta acción no se puede deshacer.');
    }
  </script>
  <?php include 'footer.php'; ?>
</body>
</html>

