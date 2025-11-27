<?php
// Footer global para CineUp
?>
<footer class="site-footer">
  <div class="container position-relative">
    <div class="row align-items-center">
      <div class="col-12 col-md-4">
        <h3 class="h5 text-white">Contáctenos</h3>
        <p class="text-white">Déjanos tu correo y te contestaremos:</p>
        <form onsubmit="alert('Gracias — esto es sólo estético en la demo.'); return false;" class="d-flex flex-column gap-2" style="max-width:320px;">
          <input type="email" placeholder="tu@correo.com" class="form-control bg-dark text-white" />
          <div class="text-start mt-1"><button type="submit" class="btn btn-accent">Enviar</button></div>
        </form>
      </div>

      <!-- CENTRO: Nuestras redes -->
      <div class="col-12 col-md-4 text-center">
        <h3 class="h5 text-white mb-2">Nuestras redes</h3>
        <p class="mb-0 d-flex gap-3 justify-content-center align-items-center">
          <a href="https://x.com/x" title="Twitter" aria-label="Twitter" target="_blank" rel="noopener" class="text-white">
            <!-- Twitter SVG -->
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path d="M22.162 5.656c-.64.284-1.326.476-2.048.562.736-.44 1.3-1.137 1.566-1.97-.688.408-1.45.705-2.262.866C18.96 4.6 18.07 4 17.07 4c-1.66 0-3.004 1.344-3.004 3.003 0 .235.026.463.077.682C10.59 7.56 7.35 5.68 5.12 2.97c-.258.444-.406.96-.406 1.513 0 1.044.532 1.964 1.342 2.504-.495-.016-.96-.152-1.366-.379v.038c0 1.458 1.037 2.674 2.415 2.951-.252.068-.517.104-.79.104-.193 0-.38-.019-.563-.055.38 1.187 1.48 2.05 2.787 2.076-1.02.8-2.31 1.276-3.712 1.276-.241 0-.48-.014-.716-.042 1.322.848 2.893 1.342 4.576 1.342 5.49 0 8.495-4.548 8.495-8.495v-.387c.583-.422 1.09-.95 1.49-1.553-.533.236-1.104.395-1.697.466z"/>
            </svg>
          </a>
          <a href="https://www.youtube.com/?gl=ES&hl=es" title="YouTube" aria-label="YouTube" target="_blank" rel="noopener" class="text-white">
            <!-- YouTube SVG -->
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path d="M23.498 6.186a2.998 2.998 0 0 0-2.112-2.12C19.6 3.6 12 3.6 12 3.6s-7.6 0-9.386.466A2.998 2.998 0 0 0 .502 6.186 31.05 31.05 0 0 0 0 12a31.05 31.05 0 0 0 .502 5.814 2.998 2.998 0 0 0 2.112 2.12C4.4 20.4 12 20.4 12 20.4s7.6 0 9.386-.466a2.998 2.998 0 0 0 2.112-2.12A31.05 31.05 0 0 0 24 12a31.05 31.05 0 0 0-.502-5.814zM9.75 15.02V8.98L15.5 12l-5.75 3.02z"/>
            </svg>
          </a>
          <a href="https://www.instagram.com/" title="Instagram" aria-label="Instagram" target="_blank" rel="noopener" class="text-white">
            <!-- Instagram SVG -->
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H7zm5 3.5a4.5 4.5 0 1 1 0 9 4.5 4.5 0 0 1 0-9zm0 2a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zm4.75-.88a1.12 1.12 0 1 1 0 2.24 1.12 1.12 0 0 1 0-2.24z"/>
            </svg>
          </a>
          <a href="https://www.facebook.com/" title="Facebook" aria-label="Facebook" target="_blank" rel="noopener" class="text-white">
            <!-- Facebook SVG -->
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path d="M22 12.07C22 6.48 17.52 2 11.93 2 6.48 2 2 6.48 2 12.07c0 4.99 3.66 9.13 8.44 9.94v-7.04H8.07v-2.9h2.37V9.41c0-2.34 1.39-3.63 3.52-3.63 1.02 0 2.09.18 2.09.18v2.3h-1.18c-1.16 0-1.52.72-1.52 1.46v1.75h2.59l-.41 2.9h-2.18v7.04C18.34 21.2 22 17.06 22 12.07z"/>
            </svg>
          </a>
        </p>
      </div>

      <!-- DERECHA: Nuestra Web + enlaces -->
      <div class="col-12 col-md-4 text-md-end">
        <div class="footer-links text-end w-100">
          <div class="footer-shop d-flex align-items-center justify-content-end mb-2">
            <svg class="icon-cart" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
              <path d="M7 4h-2l-1 2h2l3.6 7.59-1.35 2.45C8.89 16.37 9.4 17 10.13 17h7.72v-2H10.9l.6-1h6.1c.75 0 1.41-.41 1.75-1.03l2.58-5.03A1 1 0 0 0 22.08 6H6.21L5.27 4H2V2h3c.55 0 1 .45 1 1z"/>
            </svg>
            <span class="shop-title">Nuestra Web</span>
          </div>

          <nav aria-label="Enlaces rápidos" class="w-100">
            <!-- Primera fila: 2 enlaces -->
            <ul class="footer-nav row1 list-unstyled mb-1 d-flex justify-content-center align-items-center gap-2">
              <li class="d-inline"><a href="index.php">Inicio de sesión</a></li>
              <li class="d-inline sep">/</li>
              <li class="d-inline"><a href="orders_list.php">Realizar Pedido</a></li>
            </ul>

            <!-- Segunda fila: 3 enlaces -->
            <ul class="footer-nav row2 list-unstyled mb-0 d-flex justify-content-center align-items-center gap-2">
              <li class="d-inline"><a href="buy.php">Recibo</a></li>
              <li class="d-inline sep">/</li>
              <li class="d-inline"><a href="orders_list.php">Historial</a></li>
              <li class="d-inline sep">/</li>
              <li class="d-inline"><a href="policies.php">Políticas</a></li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
</footer>


