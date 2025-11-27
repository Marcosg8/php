<?php
session_start();
require 'db.php';

// Forzar login
if (!isset($_SESSION['user_id'])) {
  header('Location: index.php');
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

/* Helper: selecciona la mejor imagen (misma lógica que antes en el carrusel)
   Devuelve URL segura (rawurlencode para ficheros locales) o picsum fallback */
function select_image_for($m) {
  $special_bt = __DIR__ . '/img/volver al futuro.jpeg';
  $special_par_clean = __DIR__ . '/img/parasitos_clean.png';
  $special_par_jpeg = __DIR__ . '/img/parasitos.jpeg';
  $special_par_jpg = __DIR__ . '/img/parasitos.jpg';
  $special_par_png = __DIR__ . '/img/parasitos.png';
  $special_god = __DIR__ . '/img/el padrino.jpeg';
  $special_matrix_jpeg = __DIR__ . '/img/matrix.jpeg';
  $special_matrix_jpg = __DIR__ . '/img/matrix.jpg';
  $special_matrix_png = __DIR__ . '/img/matrix.png';

  $imgSrc = '';
  $title = $m['title'] ?? '';

  if (preg_match('/back to the future|volver al futuro/i', $title) && file_exists($special_bt)) {
    $imgSrc = 'img/volver al futuro.jpeg';
  } elseif (preg_match('/parasite|par[aá]sitos|par[aá]sito/i', $title)) {
    if (file_exists($special_par_clean)) {
      $imgSrc = 'img/parasitos_clean.png';
    } elseif (file_exists($special_par_png)) {
      $imgSrc = 'img/parasitos.png';
    } elseif (file_exists($special_par_jpeg)) {
      $imgSrc = 'img/parasitos.jpeg';
    } elseif (file_exists($special_par_jpg)) {
      $imgSrc = 'img/parasitos.jpg';
    }
  } elseif (preg_match('/godfather|el padrino|padrino/i', $title) && file_exists($special_god)) {
    $imgSrc = 'img/el padrino.jpeg';
  } elseif (preg_match('/the\s*matrix|matrix/i', $title)) {
    if (file_exists($special_matrix_jpeg)) {
      $imgSrc = 'img/matrix.jpeg';
    } elseif (file_exists($special_matrix_png)) {
      $imgSrc = 'img/matrix.png';
    } elseif (file_exists($special_matrix_jpg)) {
      $imgSrc = 'img/matrix.jpg';
    }
  } else {
    $base = preg_replace('/[^a-z0-9]+/','-', strtolower(@iconv('UTF-8','ASCII//TRANSLIT',$title)));
    if (!$base) {
      $base = preg_replace('/[^a-z0-9]+/','-', strtolower($title));
    }
    $exts = ['jpg','jpeg','png','webp'];
    foreach ($exts as $ext) {
      $p = __DIR__ . "/img/{$base}.{$ext}";
      if (file_exists($p)) { $imgSrc = "img/{$base}.{$ext}"; break; }
    }
    if (!$imgSrc) {
      $candidates = ['img/back_to_the_future.jpg','img/back_to_the_future.png','img/volver_al_futuro.jpg','img/volver_al_futuro.jpeg'];
      foreach ($candidates as $c) { if (file_exists(__DIR__ . '/' . $c)) { $imgSrc = $c; break; } }
    }
  }

  if (!$imgSrc) {
    // picsum con tamaño ancho similar al carrusel
    return 'https://picsum.photos/seed/movie' . (int)$m['id'] . '/1200/500';
  }

  if (strpos($imgSrc, 'img/') === 0) {
    return dirname($imgSrc) . '/' . rawurlencode(basename($imgSrc));
  }
  return $imgSrc;
}

/* Pre-calcula imagen para cada película y la adjunta al array (mantiene orden) */
foreach ($movies as &$mv) {
  $mv['img_url'] = select_image_for($mv);
}
unset($mv);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CineUp - Catálogo</title>
  <link rel="stylesheet" href="styles.css">
 
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
              <img src="<?php echo $m['img_url']; ?>" class="d-block w-100 carousel-img" alt="<?php echo htmlspecialchars($m['title']); ?>">
              <div class="carousel-caption d-none d-md-block">
                <div class="bg-overlay text-start text-white">
                  <h5 class="mb-1"><?php echo htmlspecialchars($m['title']); ?> <small class="movie-year">(<?php echo htmlspecialchars($m['year']); ?>)</small></h5>
                  <p class="mb-2 lead"><?php echo htmlspecialchars($m['genre']); ?> — <?php echo number_format($m['price'],2); ?> € — Stock: <?php echo (int)$m['stock']; ?></p>
                  <?php if ((int)$m['stock'] > 0): ?>
                    <a href="purchase.php?movie_id=<?php echo (int)$m['id']; ?>" class="btn btn-accent" role="button" aria-label="Comprar <?php echo htmlspecialchars($m['title']); ?>">Comprar</a>
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
            
          </div>
          <div class="col-md-4 text-center">
            <div class="film-reel mx-auto">
              <img src="img/logo.png" alt="Carrete de película" class="img-fluid">
            </div>
          </div>
        </div>
      </div>
    </section>

    <?php
    // Helper ligero para obtener poster (usa img/NAME.(jpg|jpeg|png|webp) o fallback a picsum)
    function poster_for($m) {
      // Si ya pre-calculaste img_url úsala
      if (!empty($m['img_url'])) return $m['img_url'];

      $title = $m['title'] ?? '';
      $id = (int)($m['id'] ?? 0);

      // slug seguro ASCII
      $base = preg_replace('/[^a-z0-9]+/','-', strtolower(@iconv('UTF-8','ASCII//TRANSLIT',$title)));
      if (!$base) $base = preg_replace('/[^a-z0-9]+/','-', strtolower($title));
      $base = trim($base, '-');

      $dir = __DIR__ . '/img';
      $exts = ['jpg','jpeg','png','webp','gif'];

      // 1) búsqueda exacta base.ext
      foreach ($exts as $ext) {
        $fn = "{$dir}/{$base}.{$ext}";
        if (file_exists($fn)) return 'img/' . rawurlencode(basename($fn));
      }

      // 2) busca por id en el nombre de fichero
      $files = glob($dir . '/*.{jpg,jpeg,png,webp,gif}', GLOB_BRACE);
      foreach ($files as $f) {
        $name = strtolower(pathinfo($f, PATHINFO_FILENAME));
        if ($id && strpos($name, (string)$id) !== false) {
          return 'img/' . rawurlencode(basename($f));
        }
      }

      // 3) busca por coincidencia parcial con el slug
      if ($base) {
        foreach ($files as $f) {
          $name = strtolower(pathinfo($f, PATHINFO_FILENAME));
          if (strpos($name, $base) !== false) return 'img/' . rawurlencode(basename($f));
        }
      }

      // 4) token match (palabras del título)
      $tokens = preg_split('/[-\s_]+/', $base);
      foreach ($files as $f) {
        $name = strtolower(pathinfo($f, PATHINFO_FILENAME));
        $matches = 0;
        foreach ($tokens as $t) {
          if (strlen($t) > 2 && strpos($name, $t) !== false) $matches++;
        }
        if ($matches >= 1) return 'img/' . rawurlencode(basename($f));
      }

      // fallback a picsum
      return 'https://picsum.photos/seed/movie' . $id . '/300/450';
    }
    ?>
    <?php
    // Excluir "Back to the Future" / "Volver al futuro" del strip inferior
    $strip_movies = [];
    foreach ($movies as $idx => $mv) {
      if (!preg_match('/back to the future|volver al futuro/i', $mv['title'])) {
        $strip_movies[$idx] = $mv;
      }
    }
    ?>

    <section class="top-movies-strip my-5">
      <h2 class="mb-4 text-center">Explora nuestras películas</h2>

      <?php
      // Preparamos chunks de 4 elementos (preservando keys para referenciar al carrusel principal)
      $chunks = array_chunk($strip_movies, 4, true);
      $carouselId = 'stripCarousel';
      ?>

      <div id="<?php echo $carouselId; ?>" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000" aria-label="Carrusel de películas">
        <div class="carousel-inner">
          <?php foreach ($chunks as $ci => $group): ?>
            <div class="carousel-item <?php echo $ci === 0 ? 'active' : ''; ?>">
              <div class="container">
                <div class="row gx-3 justify-content-center py-3">
                  <?php foreach ($group as $origIndex => $m): ?>
                    <div class="col-6 col-sm-4 col-md-3 d-flex align-items-stretch">
                      <article class="movie-card w-100" data-index="<?php echo (int)$origIndex; ?>" role="button" tabindex="0">
                        <a class="poster-link d-block" href="#" data-index="<?php echo (int)$origIndex; ?>">
                          <img src="<?php echo htmlspecialchars(poster_for($m)); ?>" alt="<?php echo htmlspecialchars($m['title']); ?>" class="img-fluid rounded">
                        </a>
                        <h3 class="movie-title text-center mt-2 small fw-bold"><?php echo htmlspecialchars($m['title']); ?></h3>
                      </article>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#<?php echo $carouselId; ?>" data-bs-slide="prev" aria-label="Anterior strip">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#<?php echo $carouselId; ?>" data-bs-slide="next" aria-label="Siguiente strip">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Siguiente</span>
        </button>
      </div>

      <script>
      (function(){
        // Vincula clicks/tecla en las tarjetas del strip con el carrusel principal (#moviesCarousel)
        const stripCarousel = document.getElementById('<?php echo $carouselId; ?>');
        if (!stripCarousel) return;
        const stripCards = stripCarousel.querySelectorAll('.movie-card, .poster-link');

        function goToMainCarousel(index) {
          const main = document.getElementById('moviesCarousel');
          if (!main) return;
          if (typeof bootstrap !== 'undefined' && bootstrap.Carousel) {
            const carousel = bootstrap.Carousel.getOrCreateInstance(main);
            carousel.to(Number(index));
          } else {
            const items = main.querySelectorAll('.carousel-item');
            items.forEach((it, i)=> it.classList.toggle('active', i===Number(index)));
          }
          try { window.scrollTo({ top:0, behavior:'smooth' }); } catch(e) { window.scrollTo(0,0); }
        }

        stripCarousel.addEventListener('click', function(ev){
          const el = ev.target.closest('.movie-card, .poster-link');
          if (!el) return;
          ev.preventDefault();
          const idx = el.dataset.index || el.getAttribute('data-index');
          if (idx !== undefined) goToMainCarousel(idx);
        });

        stripCarousel.addEventListener('keydown', function(ev){
          if (ev.key === 'Enter' || ev.key === ' ') {
            const el = ev.target.closest('.movie-card');
            if (!el) return;
            ev.preventDefault();
            const idx = el.dataset.index || el.getAttribute('data-index');
            if (idx !== undefined) goToMainCarousel(idx);
          }
        }, true);
      })();
      </script>
    </section>

    <script>
    (function(){
      const strip = document.getElementById('topMoviesStrip');
      if (!strip) return;
      const wrap = strip.closest('.strip-container') || strip.parentElement;
      const prev = wrap.querySelector('.strip-arrow.prev');
      const next = wrap.querySelector('.strip-arrow.next');

      // calcula ancho visible para desplazar (ancho del contenedor interno)
      function visibleWidth() {
        return Math.round(strip.getBoundingClientRect().width);
      }

      if (prev) prev.addEventListener('click', ()=> {
        strip.scrollBy({ left: -visibleWidth(), behavior:'smooth' });
      });
      if (next) next.addEventListener('click', ()=> {
        strip.scrollBy({ left: visibleWidth(), behavior:'smooth' });
      });

      // Delegación: click en tarjeta -> ir al slide del carrusel y subir top
      function initCarouselLinking() {
        var carouselEl = document.getElementById('moviesCarousel');
        if (!carouselEl) return;

        function goToIndex(idx) {
          // intenta usar bootstrap Carousel si está presente
          if (typeof bootstrap !== 'undefined' && bootstrap.Carousel) {
            const carousel = bootstrap.Carousel.getOrCreateInstance(carouselEl);
            carousel.to(idx);
          } else {
            // fallback: activar manualmente el item
            const items = carouselEl.querySelectorAll('.carousel-item');
            items.forEach((it, i)=> it.classList.toggle('active', i===idx));
          }
          try { window.scrollTo({ top:0, behavior:'smooth' }); } catch(e) { window.scrollTo(0,0); }
        }

        strip.addEventListener('click', function(ev){
          const card = ev.target.closest('.movie-card, .poster-link');
          if (!card) return;
          ev.preventDefault();
          const idx = Number(card.dataset.index || card.getAttribute('data-index'));
          if (!Number.isNaN(idx)) goToIndex(idx);
        });

        strip.addEventListener('keydown', function(ev){
          if (ev.key === 'Enter' || ev.key === ' ') {
            const card = ev.target.closest('.movie-card');
            if (!card) return;
            ev.preventDefault();
            const idx = Number(card.dataset.index || card.getAttribute('data-index'));
            if (!Number.isNaN(idx)) goToIndex(idx);
          }
        }, true);
      }

      // init carousel linking after load (bootstrap might load later)
      if (document.readyState === 'complete') initCarouselLinking();
      else window.addEventListener('load', initCarouselLinking);

      // soporte rueda para scroll horizontal
      strip.addEventListener('wheel', (e)=> {
        if (Math.abs(e.deltaX) < Math.abs(e.deltaY)) {
          e.preventDefault();
          strip.scrollBy({ left: e.deltaY, behavior:'auto' });
        }
      }, { passive:false });

    })();
    </script>

    <!-- Oferta destacada (imagen: img/oferta.jpg) -->
    <section class="special-offer my-5 py-4">
      <div class="container">
        <div class="offer-row px-3">
          <div class="offer-divider"></div>

          <div class="row align-items-center gy-3">
            <div class="col-12 col-md-3 text-center text-md-start">
              <img src="<?php echo 'img/' . rawurlencode('oferta.jpg'); ?>" alt="Oferta especial" class="offer-img rounded">
            </div>

            <div class="col-12 col-md-7">
              <h3 class="offer-title">AHORRA 1€ POR ENTRADA COMPRANDO ONLINE</h3>
              <p class="offer-desc mb-2">Ahora puedes ahorrar tiempo y dinero si compras tus entradas online. Además, disfruta de ventajas exclusivas y escoge tus butacas con total tranquilidad. Oferta válida en compra de entradas seleccionadas.</p>
              <div class="offer-desc mb-2">Válido desde: 22/10/2025</div>
            </div>

            <div class="col-12 col-md-2 text-center text-md-end">
              <a href="movies_list.php" class="btn btn-offer" role="button" aria-label="Más información sobre la oferta">Más información</a>
            </div>
          </div>

          <div class="offer-divider"></div>
        </div>
      </div>
    </section>
  </main>

  <?php include 'footer.php'; ?>

  <!-- Bootstrap JS bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>


