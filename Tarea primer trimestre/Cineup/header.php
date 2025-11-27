<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
<link href="styles.css" rel="stylesheet">
<style>
  html,body{height:100%;margin:0}
  body{display:flex;flex-direction:column;min-height:100vh}
  main.content{flex:1 0 auto}
  /* espacio superior para la navbar fija (ajusta 70px si tu navbar es más alto/bajo) */
  body { padding-top: 70px; }
  .navbar.fixed-top { z-index: 1060; }
</style>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container">
    <div class="site-brand d-flex align-items-center">
      <?php
        $logoPath = 'img/logo.jpeg';
        if (file_exists(__DIR__ . '/img/logo_clean.png')) {
          $logoPath = 'img/logo_clean.png';
        }
      ?>
      <img src="<?php echo $logoPath; ?>" alt="CineUp logo" style="height:40px;width:auto;margin-right:10px;" />
      <span class="fw-bold">CineUp</span>
    </div>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0 text-center">
        <li class="nav-item"><a class="nav-link" href="movies_list.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="create_user.php">Crear usuario</a></li>
        <li class="nav-item"><a class="nav-link" href="orders_list.php">Tu historial</a></li>
      </ul>

      <div class="d-flex align-items-center">
        <?php if (!empty($_SESSION['user_id'])): ?>
           <span class="text-white me-2">Hola, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
           <?php
             $cartCount = isset($_SESSION['cart_count']) ? intval($_SESSION['cart_count']) : 0;
           ?>
           <a href="buy.php" class="btn-cart btn-sm me-2 position-relative d-inline-flex justify-content-center align-items-center icon-btn" aria-label="Carrito de la compra" title="Carrito">
             <img src="https://img.icons8.com/?size=100&id=CE7rP-35_XQR&format=png&color=000000" alt="Carrito" style="width:20px;height:20px;object-fit:contain;display:block;">
             <?php if ($cartCount > 0): ?>
               <span class="badge bg-danger position-absolute" style="top:-6px;right:-6px;font-size:0.67rem;line-height:1;padding:0.25rem 0.4rem;"><?php echo $cartCount; ?></span>
             <?php endif; ?>
           </a>
          <a class="btn btn-outline-light btn-sm me-2" href="admin_login.php">Administrador</a>
          <a class="btn btn-outline-light btn-sm" href="logout.php">Cerrar sesión</a>
        <?php else: ?>
          <a class="btn btn-outline-light btn-sm" href="index.php">Iniciar sesión</a>
        <?php endif; ?>
      </div>
    </div>

  </div>
</nav>

<!-- script que aplica color guardado en cookie al header/footer al cargar -->
<script>
  (function(){
    function getCookie(name){
      var m = document.cookie.match('(^|;)\\s*' + name + '\\s*=\\s*([^;]+)');
      return m ? decodeURIComponent(m[2]) : '';
    }
    function applyColor(color){
      if(!color) return;
      var nav = document.querySelector('nav.navbar');
      var ft = document.querySelector('footer');
      // usar setProperty con prioridad important para sobreescribir clases bootstrap
      if(nav) nav.style.setProperty('background-color', color, 'important');
      if(ft) ft.style.setProperty('background-color', color, 'important');
      document.documentElement.style.setProperty('--site-theme-color', color);
    }

    function loadTheme(){
      try {
        var col = getCookie('site_theme_color') || localStorage.getItem('site_theme_color');
        if(col) applyColor(col);
      } catch(e){}
    }

    // aplicar color guardado al cargar
    document.addEventListener('DOMContentLoaded', loadTheme);

    // escuchar cambio disparado desde index.php (sin recarga)
    document.addEventListener('themeChanged', function(e){
      try {
        var color = e && e.detail && e.detail.color ? e.detail.color : null;
        if(color){
          // aplicar y sincronizar en localStorage por si la cookie falla
          applyColor(color);
          try { localStorage.setItem('site_theme_color', color); } catch(err){}
        }
      } catch(err){}
    });

    // fallback: si se cambia en otra pestaña -> aplicar
    window.addEventListener('storage', function(e){
      if(e.key === 'site_theme_color' && e.newValue){
        applyColor(e.newValue);
      }
    });
  })();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>



