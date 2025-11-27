<?php

session_start();
require 'db.php';

// Forzar login
if (!isset($_SESSION['user_id'])) {
  header('Location: index.php');
  exit;
}

$movie_id = isset($_GET['movie_id']) ? (int)$_GET['movie_id'] : 0;
if ($movie_id <= 0) {
  header('Location: movies_list.php?msg=' . urlencode('Película no válida'));
  exit;
}

// Obtener película
$stmt = $mysqli->prepare("SELECT id, title, genre, year, price, stock FROM movies WHERE id = ?");
$stmt->bind_param('i', $movie_id);
$stmt->execute();
$res = $stmt->get_result();
$movie = $res->fetch_assoc();
$stmt->close();

if (!$movie) {
  header('Location: movies_list.php?msg=' . urlencode('Película no encontrada'));
  exit;
}

// función para buscar poster local o fallback picsum (más estricta para evitar falsos positivos)
function poster_for($m) {
  if (!empty($m['img_url'])) return $m['img_url'];

  $title = $m['title'] ?? '';
  $id = (int)($m['id'] ?? 0);
  $dir = __DIR__ . '/img';
  $exts = ['jpg','jpeg','png','webp','gif'];

  // construir slug seguro (ASCII)
  $base = preg_replace('/[^a-z0-9]+/','-', strtolower(@iconv('UTF-8','ASCII//TRANSLIT',$title)));
  if (!$base) $base = preg_replace('/[^a-z0-9]+/','-', strtolower($title));
  $base = trim($base, '-');

  // 1) exacto: slug.ext
  foreach ($exts as $ext) {
    $fn = "{$dir}/{$base}.{$ext}";
    if (file_exists($fn)) return 'img/' . rawurlencode(basename($fn));
  }

  // listar archivos una vez
  $files = glob($dir . '/*.{jpg,jpeg,png,webp,gif}', GLOB_BRACE);

  // 2) coincidencia por id en el nombre de fichero (prioritaria)
  if ($id) {
    foreach ($files as $f) {
      $name = strtolower(pathinfo($f, PATHINFO_FILENAME));
      if (strpos($name, (string)$id) !== false) return 'img/' . rawurlencode(basename($f));
    }
  }

  // 2.5) buscar por token corto (ej. "Matrix.jpg" o "matrix.jpg" / "Matrix.JPG")
  $tokens = array_filter(preg_split('/[-\s_]+/', $base));
  if (!empty($tokens)) {
    $last = strtolower(end($tokens));
    foreach ($exts as $ext) {
      // nombre exactamente "matrix.jpg" (cualquier case en Windows funcionará)
      $candidate = "{$dir}/{$last}.{$ext}";
      if (file_exists($candidate)) return 'img/' . rawurlencode(basename($candidate));
      // versión capitalizada
      $candidate2 = "{$dir}/" . ucfirst($last) . ".{$ext}";
      if (file_exists($candidate2)) return 'img/' . rawurlencode(basename($candidate2));
    }
    // también aceptar cualquier fichero que contenga el token (fallback más laxo)
    foreach ($files as $f) {
      $name = strtolower(pathinfo($f, PATHINFO_FILENAME));
      if (strpos($name, $last) !== false) return 'img/' . rawurlencode(basename($f));
    }
  }

  // 3) coincidencia por slug como palabra completa o prefijo
  if ($base) {
    $pattern = '/(^|[^a-z0-9])' . preg_quote($base, '/') . '([^a-z0-9]|$)/i';
    foreach ($files as $f) {
      $name = pathinfo($f, PATHINFO_FILENAME);
      if (preg_match($pattern, $name)) return 'img/' . rawurlencode(basename($f));
    }
  }

  // 4) token matching más estricto: requiere al menos 2 tokens coincidentes (o 1 token largo)
  $tokens2 = array_filter(preg_split('/[-\s_]+/', $base), fn($t)=> strlen($t) > 2);
  if (!empty($tokens2)) {
    foreach ($files as $f) {
      $name = strtolower(pathinfo($f, PATHINFO_FILENAME));
      $matches = 0;
      foreach ($tokens2 as $t) {
        if (strpos($name, $t) !== false) $matches++;
      }
      if ($matches >= 2) return 'img/' . rawurlencode(basename($f));
      if ($matches === 1) {
        foreach ([$id, preg_replace('/[^0-9]/','', $m['year'] ?? '')] as $extra) {
          if ($extra && strpos($name, (string)$extra) !== false) return 'img/' . rawurlencode(basename($f));
        }
      }
    }
  }

  // fallback: usar picsum con semilla consistente (usa slug si hay)
  $seed = $id ? 'movie' . $id : 'movie-' . ($base ?: 'unknown');
  return 'https://picsum.photos/seed/' . rawurlencode($seed) . '/300/450';
}
$img = poster_for($movie);

// flash / detección compra
$justBought = false;
if (!empty($_GET['purchased']) && $_GET['purchased'] == '1') {
  $justBought = true;
}
if (!empty($_SESSION['purchase_success'])) {
  $justBought = true;
  unset($_SESSION['purchase_success']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Comprar - <?php echo htmlspecialchars($movie['title']); ?></title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <?php include 'header.php'; ?>

  <main class="content container my-4">

    <!-- CALL TO ACTION -->
    <?php if ($justBought): ?>
      <div id="cta-banner" class="alert alert-success d-flex justify-content-between align-items-center" role="alert" style="box-shadow:0 6px 18px rgba(0,0,0,.25);">
        <div>
          <strong>¡Compra realizada!</strong>
          <div>Gracias por comprar "<?php echo htmlspecialchars($movie['title']); ?>". Puedes ver tu recibo o seguir comprando.</div>
        </div>
        <div class="d-flex gap-2">
          <a href="receipt.php?movie_id=<?php echo (int)$movie['id']; ?>" class="btn btn-outline-dark">Ver recibo</a>
          <a href="movies_list.php" class="btn btn-accent">Seguir comprando</a>
        </div>
      </div>
    <?php else: ?>
      <div id="cta-banner" class="alert alert-info d-flex justify-content-between align-items-center" role="alert" style="box-shadow:0 6px 18px rgba(0,0,0,.12);">
        <div>
          <strong><h2 class="accent-heading">Ahorra 1€ por entrada comprando online</h2></strong>
          <div>Compra ahora "<?php echo htmlspecialchars($movie['title']); ?>" y ahorra en tu entrada.</div>
        </div>
        <div class="d-flex gap-2">
          <a href="buy.php" button form="purchase-form" class="btn btn-accent">Comprar ahora</button>
          <a href="movies_list.php" class="btn btn-outline-dark">Volver al catálogo</a>
        </div>
      </div>
    <?php endif; ?>

    <div class="row">
      <div class="col-md-5">
        <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" class="img-fluid rounded">
      </div>
      <div class="col-md-7">
        <h1><?php echo htmlspecialchars($movie['title']); ?> <small class="text-muted"></small></h1>
        <p class="lead"><?php echo htmlspecialchars($movie['genre']); ?></p>
        <p>Precio unidad: <strong><?php echo number_format($movie['price'],2); ?> €</strong></p>
        <p>Stock disponible: <strong><?php echo (int)$movie['stock']; ?></strong></p>

        <?php if ((int)$movie['stock'] > 0): ?>
          <form action="place_order.php" method="post" class="row g-2 align-items-center">
            <input type="hidden" name="movie_id" value="<?php echo (int)$movie['id']; ?>">
            <input type="hidden" name="customer_name" value="<?php echo htmlspecialchars($_SESSION['username']); ?>">
            <div class="col-auto">
              <label for="quantity" class="form-label">Cantidad</label>
              <input id="quantity" type="number" name="quantity" value="1" min="1" max="<?php echo (int)$movie['stock']; ?>" class="form-control" style="width:120px">
            </div>
            <div class="col-12 d-flex align-items-center gap-2 mt-3">
              <button type="submit" class="btn btn-accent">Confirmar compra</button>

              <a href="buy.php"
                 class="btn-cart btn-sm position-relative d-inline-flex align-items-center"
                 aria-label="Ver recibo" title="Ver recibo">
                <img src="https://img.icons8.com/?size=100&id=CE7rP-35_XQR&format=png&color=000000"
                     alt="" style="width:20px;height:20px;object-fit:contain;display:inline-block;margin-right:8px;">
                <span style="line-height:1;color:#fff;">Ver recibo</span>
                <?php if (!empty($cartCount) && $cartCount > 0): ?>
                  <span class="badge badge-cart position-absolute" style="top:-6px;right:-6px;"><?php echo $cartCount; ?></span>
                <?php endif; ?>
              </a>
            </div>
          </form>
        <?php else: ?>
          <div class="alert alert-secondary">Lo sentimos, esta película está agotada.</div>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <?php include 'footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>