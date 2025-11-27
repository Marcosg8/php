<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];

// Detectar posible nombre de columna en purchases_view
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
    $sql = "SELECT order_id, movie_id, title, quantity, unit_price, total_price, customer_name, created_at FROM {$table} WHERE customer_name = ? ORDER BY created_at DESC";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $_SESSION['username']);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $mysqli->query("SELECT order_id, movie_id, title, quantity, unit_price, total_price, customer_name, created_at FROM {$table} WHERE 0");
}

$sumQuantity = 0;
$sumTotal = 0.0;

// Obtener nombre cliente mostrado (si existe)
$clienteDisplay = !empty($_SESSION['username']) ? $_SESSION['username'] : 'Cliente';
if ($result && $result->num_rows > 0) {
    $firstRow = $result->fetch_assoc();
    if (!empty($firstRow['customer_name'])) {
        $clienteDisplay = $firstRow['customer_name'];
    }
    // Rewind result set: crear nuevo query or store rows
    $rows = [$firstRow];
    while ($r = $result->fetch_assoc()) { $rows[] = $r; }
} else {
    $rows = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Factura - CineUp</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
     :root{
      --bg:#f6f8fb;
      --card:#ffffff;
      --accent:#1f7a8c;
      --accent-2:#27ae60;
      --border: #e6e9ef;
      font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      color-scheme: light;
    }
    /* Texto general en negro (solo afectará esta página) */
    html,body { height:100%; margin:0; background:var(--bg); color:#000; }
    .container{ max-width:1000px; margin:28px auto; padding:20px; }
    .invoice-card{
      /* Fondo blanco semitransparente con ligero degradado y efecto glass */
      background: linear-gradient(180deg,
        rgba(0, 0, 0, 0.73) 0%,
        rgba(255, 255, 255, 0.8) 100%);
      border-radius:12px;
      padding:20px;
      border:1px solid rgba(230,233,239,0.6); /* borde semi-transparente */
      box-shadow: 0 6px 18px rgba(15,23,42,0.06);
      color: #00000052;
      backdrop-filter: blur(6px); /* suaviza lo que hay detrás */
      -webkit-backdrop-filter: blur(6px);
      -webkit-font-smoothing:antialiased;
    }

    /* -- NUEVO: texto de la parte superior de la factura en blanco (solo dentro de .invoice-card) */
    .invoice-card .invoice-head,
    .invoice-card .invoice-head .title,
    .invoice-card .invoice-head .muted-note,
    .invoice-card .invoice-head .brand,
    .invoice-card .invoice-head .brand b,
    .invoice-card .meta,
    .invoice-card .meta .big {
      color: #fff !important;
    }

    /* info-grid texto en blanco (solo dentro de .invoice-card) */
    .invoice-card .info-grid,
    .invoice-card .info-grid .info,
    .invoice-card .info-grid .info b,
    .invoice-card .info-grid .info p {
      color: #fff !important;
    }

    /* Reemplazado --muted por negro explícito */
    .muted, .muted-note, .info b, .invoice-table thead th, .meta { color: #000 !important; }

    .invoice-head{ display:flex; justify-content:space-between; align-items:flex-start; gap:16px; margin-bottom:18px; color: #000; }
    .brand{ display:flex; gap:12px; align-items:center; }
    .logo{
      width:56px; height:56px; border-radius:10px;
      background:linear-gradient(135deg,var(--accent),var(--accent-2));
      display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:18px;
      box-shadow: 0 4px 10px rgba(255, 255, 255, 0.12);
    }
    h1.title{ margin:0; font-size:20px; }
    .meta{ text-align:right; font-size:13px; }
    .meta .big{ color:#000; font-weight:700; font-size:16px; margin-top:6px; display:block; }

    .info-grid{ display:flex; gap:20px; flex-wrap:wrap; margin-bottom:18px; }
    .info{ background:transparent; padding:8px 0; min-width:180px; }
    .info b{ display:block; color:#000; font-size:12px; margin-bottom:6px; }
    .info p{ margin:0; font-weight:600; color:#000; }

    table.invoice-table{ width:100%; border-collapse:collapse; background:transparent; margin-bottom:12px; }
    .invoice-table th, .invoice-table td{
      padding:12px 10px; text-align:left; border-bottom:1px dashed var(--border);
      font-size:14px; color:#000;
    }
    .invoice-table thead th{ background:transparent; color:#000; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; font-size:12px; }

    /* Asegura que las cabeceras de la tabla dentro de la factura sean blancas */
    .invoice-card table.invoice-table thead th {
      color: #fff !important;
    }

    .right{ text-align:right; }

    .totals{ display:flex; justify-content:flex-end; margin-top:14px; gap:12px; }
    .totals .box{ background:rgba(0,0,0,0.03); padding:12px 16px; border-radius:8px; min-width:220px; }
    .totals .box p{ margin:0; color: #000; font-size:13px; }
    .totals .box .amount{ font-size:18px; font-weight:700; color:#000; margin-top:6px; }

    .actions{ display:flex; gap:10px; margin-top:14px; align-items:center; }
    .btn{
      display:inline-flex; align-items:center; gap:8px; padding:10px 14px; border-radius:8px; border:none; cursor:pointer;
      background:var(--accent); color:#fff; text-decoration:none; font-weight:600;
    }
    .btn.secondary{ background:#f3f4f6; color:#000; border:1px solid var(--border); }
    .btn.print{ background:#0ea5a4; color:#000; }
    .muted-note{ color:#000; font-size:13px; margin-top:10px; }

    @media print{
      body{ background:#fff; }
      .actions, .invoice-card { box-shadow:none; border:none; }
      .actions { display:none; }
      .container{ margin:0; padding:0; max-width:100%; }
    }
  </style>
  
</head>
<body>
  <?php include 'header.php'; ?>
  <div class="container">
    <div class="invoice-card">
      <div class="invoice-head">
        <div class="brand">
          <img src="img/logo.png" alt="Logo" style="display:block;margin:0 auto 12px auto;width:80px;height:80px;object-fit:cover;border-radius:50%;box-shadow:0 2px 6px rgba(255, 255, 255, 0.25);">
          <div>
            <h1 class="title">CineUp — Factura</h1>
            <div class="muted-note">Historial y factura de compras</div>
          </div>
        </div>
        <div class="meta">
          <div>Fecha: <span class="big"><?php echo date('Y-m-d'); ?></span></div>
          <div>Nº factura: <span class="big"><?php echo time(); ?></span></div>
        </div>
      </div>

      <div class="info-grid">
        <div class="info">
          <b>Cliente</b>
          <p><?php echo htmlspecialchars($clienteDisplay); ?></p>
        </div>
        <div class="info">
          <b>Email</b>
          <p><?php echo !empty($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : '—'; ?></p>
        </div>
        <div class="info">
          <b>Estado</b>
          <p>Pendiente / Generada</p>
        </div>
      </div>

      <?php if (count($rows) === 0): ?>
        <p>No hay compras para mostrar.</p>
      <?php else: ?>
        <table class="invoice-table" role="table" aria-label="Factura">
          <thead>
            <tr>
              <th>ID</th>
              <th>Película</th>
              <th>Cantidad</th>
              <th class="right">Precio unit.</th>
              <th class="right">Total</th>
              <th>Fecha</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $row):
              $sumQuantity += (int)$row['quantity'];
              $sumTotal += (float)$row['total_price'];
            ?>
              <tr>
                <td><?php echo (int)$row['order_id']; ?></td>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo (int)$row['quantity']; ?></td>
                <td class="right"><?php echo number_format($row['unit_price'], 2); ?> €</td>
                <td class="right"><?php echo number_format($row['total_price'], 2); ?> €</td>
                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div class="totals" aria-hidden="false">
          <div class="box">
            <p>Total artículos</p>
            <div class="amount"><?php echo $sumQuantity; ?></div>
          </div>
          <div class="box">
            <p>Importe total</p>
            <div class="amount"><?php echo number_format($sumTotal, 2); ?> €</div>
          </div>
        </div>

        <div class="actions">
          <a href="orders_list.php" class="btn secondary">Volver al historial</a>
          <button type="button" onclick="window.print()" class="btn print">Imprimir</button>
        </div>

        <div class="muted-note">
          Gracias por su compra. Esta factura refleja los artículos comprados asociados a su cuenta.
        </div>
      <?php endif; ?>
    </div>
  </div>

  <?php include 'footer.php'; ?>
</body>
</html>

