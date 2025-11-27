<?php
session_start();

// DEBUG: mostrar errores en desarrollo (quitar en producción)
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'db.php'; // debe definir $mysqli

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    $_SESSION['msg'] = 'Acceso inválido.';
    header('Location: orders_list.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$removeQty = isset($_POST['remove_qty']) ? (int)$_POST['remove_qty'] : 0;

if ($orderId <= 0 || $removeQty <= 0) {
    $_SESSION['msg'] = 'Parámetros inválidos.';
    header('Location: orders_list.php');
    exit;
}

try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    // 1) Detectar columna para filtrar en la vista purchases_view (igual que orders_list.php)
    $view = 'purchases_view';
    $dbNameRow = $mysqli->query("SELECT DATABASE()")->fetch_row();
    $dbName = $dbNameRow ? $dbNameRow[0] : '';

    $candidates = ['user_id', 'customer_id', 'customer', 'user'];
    $foundCol = null;

    $check = $mysqli->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    foreach ($candidates as $cand) {
        $check->bind_param('sss', $dbName, $view, $cand);
        $check->execute();
        $check->bind_result($cnt);
        $check->fetch();
        if ($cnt > 0) { $foundCol = $cand; break; }
        $check->free_result();
    }
    $check->close();

    // 2) Validar existencia del pedido y que pertenezca al usuario leyendo la vista
    if ($foundCol) {
        $selView = $mysqli->prepare("SELECT quantity, unit_price FROM {$view} WHERE order_id = ? AND {$foundCol} = ?");
        $selView->bind_param('ii', $orderId, $userId);
    } elseif (!empty($_SESSION['username'])) {
        $selView = $mysqli->prepare("SELECT quantity, unit_price FROM {$view} WHERE order_id = ? AND customer_name = ?");
        $selView->bind_param('is', $orderId, $_SESSION['username']);
    } else {
        throw new Exception('No se puede verificar la pertenencia del pedido (falta columna o username).');
    }

    $selView->execute();
    $selView->bind_result($viewQty, $viewUnitPrice);
    if (!$selView->fetch()) {
        $selView->close();
        throw new Exception('Pedido no encontrado o sin permiso (vista).');
    }
    $selView->close();

    // 3) Bloqueo y modificación sobre la tabla orders (id)
    // Comprobar que la tabla orders exista
    $chk = $mysqli->prepare("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders'");
    $chk->execute();
    $chk->bind_result($ordersExists);
    $chk->fetch();
    $chk->close();
    if ($ordersExists == 0) {
        throw new Exception('Tabla orders no encontrada.');
    }

    $mysqli->begin_transaction();

    // FOR UPDATE sobre orders para asegurar concurrencia
    $sel = $mysqli->prepare("SELECT quantity, unit_price FROM orders WHERE id = ? FOR UPDATE");
    $sel->bind_param('i', $orderId);
    $sel->execute();
    $sel->bind_result($curQty, $unitPrice);
    if (!$sel->fetch()) {
        $sel->close();
        $mysqli->rollback();
        throw new Exception('Pedido no encontrado en tabla orders.');
    }
    $sel->close();

    if ($removeQty >= $curQty) {
        $del = $mysqli->prepare("DELETE FROM orders WHERE id = ?");
        $del->bind_param('i', $orderId);
        $del->execute();
        $del->close();
        $_SESSION['msg'] = 'Compra eliminada completamente.';
    } else {
        $newQty = $curQty - $removeQty;
        $newTotal = $newQty * $unitPrice;
        $up = $mysqli->prepare("UPDATE orders SET quantity = ?, total_price = ? WHERE id = ?");
        $up->bind_param('idi', $newQty, $newTotal, $orderId);
        $up->execute();
        $up->close();
        $_SESSION['msg'] = 'Cantidad ajustada correctamente.';
    }

    $mysqli->commit();
} catch (Exception $e) {
    if (isset($mysqli) && $mysqli->connect_errno === 0) {
        $mysqli->rollback();
    }
    $_SESSION['msg'] = 'Error al procesar la petición: ' . $e->getMessage();
}

header('Location: orders_list.php');
exit;
?>

