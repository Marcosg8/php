<?php
session_start();
require 'db.php';

// Forzar login
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// Mensaje opcional desde place_order.php
$msg = '';
if (!empty($_GET['msg'])) {
  $msg = htmlspecialchars($_GET['msg']);
}

$result = $mysqli->query("SELECT id, title, genre, year, price, stock FROM movies ORDER BY title");
$movies = [];
while ($row = $result->fetch_assoc()) {
  $movies[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CineUp - Catálogo</title>
  <style>
    /* Small tweaks for carousel captions */
    .carousel-caption { bottom: 20px; }
    .carousel-caption .bg-overlay { background: rgba(0,0,0,0.55); padding: 12px; border-radius: 6px; }
    .carousel-img { height: 420px; object-fit: cover; }
    @media (max-width:767px){ .carousel-img{ height:220px } }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>
  <main class="content container">
    <h1 class="mb-3">Catálogo de Películas</h1>

    <?php if ($msg): ?>
      <div class="alert alert-light" role="alert"><?php echo $msg; ?></div>
    <?php endif; ?>

  <p><a href="orders_list.php" class="btn btn-accent" role="button">Ver historial de compras</a></p>

    <?php if (empty($movies)): ?>
      <p>No hay películas disponibles.</p>
    <?php else: ?>

      <div id="moviesCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
          <?php foreach ($movies as $i => $m): ?>
            <button type="button" data-bs-target="#moviesCarousel" data-bs-slide-to="<?php echo $i; ?>" class="<?php echo $i===0 ? 'active' : ''; ?>" <?php echo $i===0 ? 'aria-current="true"' : ''; ?> aria-label="Slide <?php echo $i+1; ?>"></button>
          <?php endforeach; ?>
        </div>
        <div class="carousel-inner">
          <?php foreach ($movies as $i => $m): ?>
            <div class="carousel-item <?php echo $i===0 ? 'active' : ''; ?>">
              <?php
                // Prefer a local image if present in the img/ folder.
                $imgSrc = '';
                // Special filenames requested by user for specific titles
                $special_bt = __DIR__ . '/img/volver al futuro.jpeg';
                // Special images for particular titles
                $special_par_clean = __DIR__ . '/img/parasitos_clean.png';
                $special_par_jpeg = __DIR__ . '/img/parasitos.jpeg';
                $special_par_jpg = __DIR__ . '/img/parasitos.jpg';
                $special_par_png = __DIR__ . '/img/parasitos.png';
                $special_god = __DIR__ . '/img/el padrino.jpeg';
                $special_matrix_jpeg = __DIR__ . '/img/matrix.jpeg';
                $special_matrix_jpg = __DIR__ . '/img/matrix.jpg';
                $special_matrix_png = __DIR__ . '/img/matrix.png';
                if (preg_match('/back to the future|volver al futuro/i', $m['title']) && file_exists($special_bt)) {
                  $imgSrc = 'img/volver al futuro.jpeg';
                } elseif (preg_match('/parasite|par[aá]sitos|par[aá]sito/i', $m['title'])) {
                  // For "Parasite / Parásitos", prefer cleaned transparent PNG, then try common extensions
                  if (file_exists($special_par_clean)) {
                    $imgSrc = 'img/parasitos_clean.png';
                  } elseif (file_exists($special_par_png)) {
                    $imgSrc = 'img/parasitos.png';
                  } elseif (file_exists($special_par_jpeg)) {
                    $imgSrc = 'img/parasitos.jpeg';
                  } elseif (file_exists($special_par_jpg)) {
                    $imgSrc = 'img/parasitos.jpg';
                  }
                } elseif (preg_match('/godfather|el padrino|padrino/i', $m['title']) && file_exists($special_god)) {
                  // For The Godfather / El Padrino
                  $imgSrc = 'img/el padrino.jpeg';
                } elseif (preg_match('/the\s*matrix|matrix/i', $m['title'])) {
                  // For The Matrix / Matrix — prefer local images if present
                  if (file_exists($special_matrix_jpeg)) {
                    $imgSrc = 'img/matrix.jpeg';
                  } elseif (file_exists($special_matrix_png)) {
                    $imgSrc = 'img/matrix.png';
                  } elseif (file_exists($special_matrix_jpg)) {
                    $imgSrc = 'img/matrix.jpg';
                  }
                } else {
                  // sanitize title to ascii-safe filename
                  $base = preg_replace('/[^a-z0-9]+/','-', strtolower(@iconv('UTF-8','ASCII//TRANSLIT',$m['title'])));
                  if (!$base) {
                    $base = preg_replace('/[^a-z0-9]+/','-', strtolower($m['title']));
                  }
                  $exts = ['jpg','jpeg','png','webp'];
                  foreach ($exts as $ext) {
                    $p = __DIR__ . "/img/{$base}.{$ext}";
                    if (file_exists($p)) { $imgSrc = "img/{$base}.{$ext}"; break; }
                  }
                  // additional conventional fallback names
                  if (!$imgSrc) {
                    $candidates = ['img/back_to_the_future.jpg','img/back_to_the_future.png','img/volver_al_futuro.jpg','img/volver_al_futuro.jpeg'];
                    foreach ($candidates as $c) { if (file_exists(__DIR__ . '/' . $c)) { $imgSrc = $c; break; } }
                  }
                }
                if (!$imgSrc) {
                  $imgSrc = 'https://picsum.photos/seed/movie' . (int)$m['id'] . '/1200/500';
                }
              ?>
              <?php
                // Encode local file names so spaces and special chars are URL-safe
                if (strpos($imgSrc, 'img/') === 0) {
                  $url = dirname($imgSrc) . '/' . rawurlencode(basename($imgSrc));
                } else {
                  $url = $imgSrc;
                }
              ?>
              <img src="<?php echo $url; ?>" class="d-block w-100 carousel-img" alt="<?php echo htmlspecialchars($m['title']); ?>">
              <div class="carousel-caption d-none d-md-block">
                <div class="bg-overlay text-start text-white">
                  <h5 class="mb-1"><?php echo htmlspecialchars($m['title']); ?> <small class="text-muted">(<?php echo htmlspecialchars($m['year']); ?>)</small></h5>
                  <p class="mb-2 lead"><?php echo htmlspecialchars($m['genre']); ?> — <?php echo number_format($m['price'],2); ?> € — Stock: <?php echo (int)$m['stock']; ?></p>
                  <?php if ((int)$m['stock'] > 0): ?>
                    <form action="place_order.php" method="post" class="d-flex align-items-center gap-2">
                      <input type="hidden" name="movie_id" value="<?php echo (int)$m['id']; ?>">
                      <input type="number" name="quantity" value="1" min="1" max="<?php echo (int)$m['stock']; ?>" class="form-control" style="width:90px">
                      <input type="hidden" name="customer_name" value="<?php echo htmlspecialchars($_SESSION['username']); ?>">
                      <button type="submit" class="btn btn-accent">Comprar</button>
                    </form>
                  <?php else: ?>
                    <span class="badge bg-secondary">Agotado</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#moviesCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#moviesCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Siguiente</span>
        </button>
      </div>

    <?php endif; ?>

    <!-- Intro sección: entusiasmo y foto tipo carrete -->
    <section class="intro-section my-5">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-8">
            <h2 class="display-6">¡Vive el cine con pasión en CineUp!</h2>
            <p class="lead">Descubre estrenos, clásicos y joyas ocultas seleccionadas con cariño. Aquí celebramos historias que emocionan, personajes que perduran y experiencias para compartir. Navega por nuestro catálogo y encuentra tu próxima película favorita.</p>
            <p><a href="#moviesCarousel" class="btn btn-accent">Explorar catálogo</a></p>
          </div>
          <div class="col-md-4 text-center">
            <div class="film-reel mx-auto">
              <img src="img/inception.jpeg" alt="Carrete de película" class="img-fluid">
            </div>
          </div>
        </div>
      </div>
    </section>

  </main>

  <?php include 'footer.php'; ?>

  <!-- Bootstrap JS bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
