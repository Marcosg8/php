<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];

// Intentar detectar nombre de columna en la vista purchases_view
$table = 'purchases_view';
$dbNameRow = $mysqli->query("SELECT DATABASE()")->fetch_row();
$dbName = $dbNameRow ? $dbNameRow[0] : '';

$candidates = ['user_id', 'customer_id', 'customer', 'user'];
$foundCol = null;

$check = $mysqli->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?");
foreach ($candidates as $cand) {
    $check->bind_param('sss', $dbName, $table, $cand);
    $check->execute();
    $check->bind_result($cnt);
    $check->fetch();
    if ($cnt > 0) { $foundCol = $cand; break; }
    // reset for next iteration
    $check->free_result();
}
$check->close();

if ($foundCol) {
    $sql = "SELECT order_id, movie_id, title, quantity, unit_price, total_price, customer_name, created_at FROM {$table} WHERE {$foundCol} = ? ORDER BY created_at DESC";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
} elseif (!empty($_SESSION['username'])) {
    // fallback por nombre de usuario si la vista sólo tiene customer_name
    $sql = "SELECT order_id, movie_id, title, quantity, unit_price, total_price, customer_name, created_at FROM {$table} WHERE customer_name = ? ORDER BY created_at DESC";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $_SESSION['username']);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // no se encontró columna para filtrar: devolver resultado vacío (evita error)
    $result = $mysqli->query("SELECT order_id, movie_id, title, quantity, unit_price, total_price, customer_name, created_at FROM {$table} WHERE 0");
}

$sumQuantity = 0;
$sumTotal = 0.0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>CineUp - Historial</title>
  <style> table { border-collapse: collapse; width: 100%; } th, td { padding: 8px; border: 1px solid #ddd; } </style>
</head>
<body>
  <?php include 'header.php'; ?>
  <main class="content">
  <h1>Historial de Compras</h1>
  <?php if (!empty($_SESSION['msg'])): ?>
    <div style="background:#0d6efd;color:#fff;padding:10px;border-radius:6px;margin-bottom:12px"><?php echo htmlspecialchars($_SESSION['msg']); unset($_SESSION['msg']); ?></div>
  <?php endif; ?>

  <div style="display:flex;gap:8px;align-items:center;margin-bottom:12px">
    <?php $cartCount = isset($_SESSION['cart_count']) ? intval($_SESSION['cart_count']) : 0; ?>
    <!-- botón cuadrado "Volver al catálogo" con fondo naranja -->
    <a href="movies_list.php" class="btn btn-accent btn-sm me-2 position-relative d-inline-flex justify-content-center align-items-center"
       style="width:40px;height:34px;padding:0;background:#ff7a18;border-color:#e06b12;color:#fff;"
       aria-label="Volver al catálogo" title="Volver al catálogo">
      <img src="https://img.icons8.com/?size=100&id=60SNKbzxJ9sc&format=png&color=000000"
           alt="Volver al catálogo" style="width:20px;height:20px;object-fit:contain;display:block;">
    </a>

    <!-- botón carrito cuadrado con fondo verde (igual formato) -->
    <a href="buy.php" class="btn-cart btn-sm me-2 position-relative d-inline-flex justify-content-center align-items-center icon-btn" aria-label="Carrito de la compra" title="Carrito">
             <img src="https://img.icons8.com/?size=100&id=CE7rP-35_XQR&format=png&color=000000" alt="Carrito" style="width:20px;height:20px;object-fit:contain;display:block;">
             <?php if ($cartCount > 0): ?>
               <span class="badge bg-danger position-absolute" style="top:-6px;right:-6px;font-size:0.67rem;line-height:1;padding:0.25rem 0.4rem;"><?php echo $cartCount; ?></span>
             <?php endif; ?>
           </a>

    <form method="post" action="delete_orders.php" onsubmit="return confirm('¿Borrar todas tus compras? Esta acción no se puede deshacer.');" style="margin:0;">
      <button type="submit" class="btn-danger-soft btn-sm me-2 position-relative d-inline-flex justify-content-center align-items-center icon-btn"
        aria-label="Borrar todas las compras" title="Borrar todas las compras">
        <img src="https://img.icons8.com/?size=100&id=11201&format=png&color=000000"
             alt="Borrar todas las compras" style="width:20px;height:20px;object-fit:contain;display:block;">
      </button>
    </form>
  </div>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Película</th>
        <th>Cantidad</th>
        <th>Precio unit.</th>
        <th>Total</th>
        <th>Cliente</th>
        <th>Fecha</th>
      </tr>
    </thead>
    <tbody>
<?php while ($row = $result->fetch_assoc()):
      $sumQuantity += (int)$row['quantity'];
      $sumTotal += (float)$row['total_price'];
?>
      <tr>
        <td><?php echo (int)$row['order_id']; ?></td>
        <td><?php echo htmlspecialchars($row['title']); ?></td>
        <td style="text-align:center">
          <?php echo (int)$row['quantity']; ?>
          <form method="post" action="adjust_purchase.php" style="display:inline-block;margin-left:8px;">
            <input type="hidden" name="order_id" value="<?php echo (int)$row['order_id']; ?>">
            <input type="number" name="remove_qty" min="1" max="<?php echo (int)$row['quantity']; ?>" value="1" style="width:70px;">
            <button type="submit" class="btn btn-danger remove-btn" style="margin-left:4px;">Quitar</button>
          </form>
        </td>
        <td><?php echo number_format($row['unit_price'],2); ?> €</td>
        <td><?php echo number_format($row['total_price'],2); ?> €</td>
        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
      </tr>
<?php endwhile; ?>
    </tbody>
    <tfoot>
      <tr>
        <th colspan="2" style="text-align:right">Total productos:</th>
        <th><?php echo $sumQuantity; ?></th>
        <th></th>
        <th><?php echo number_format($sumTotal,2); ?> €</th>
        <th colspan="2"></th>
      </tr>
    </tfoot>
  </table>
  </main>
  <?php include 'footer.php'; ?>
</body>
</html>




