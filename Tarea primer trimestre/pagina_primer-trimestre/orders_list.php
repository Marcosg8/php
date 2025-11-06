<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$result = $mysqli->query("SELECT order_id, movie_id, title, quantity, unit_price, total_price, customer_name, created_at FROM purchases_view ORDER BY created_at DESC");
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
    <a href="movies_list.php" class="btn btn-accent" role="button">Volver al catálogo</a>

    <form method="post" action="delete_orders.php" onsubmit="return confirm('¿Borrar todas tus compras? Esta acción no se puede deshacer.');" style="margin:0;">
      <button type="submit" class="btn btn-accent" style="background:#c82333;border-color:#bd2130;">Borrar mis compras</button>
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
        <td><?php echo (int)$row['quantity']; ?></td>
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
