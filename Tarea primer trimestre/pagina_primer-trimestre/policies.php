<?php
// Página de políticas enlazada desde el footer
session_start();
require 'db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CineUp - Políticas</title>
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1CmrxMRARb6aLqgBO7yyAxTOQE2AKb9GfXnEw3rYt0QK4mG6G6fJ0z2Q5F3X9G5b" crossorigin="anonymous">
  <style>
    /* small local tweaks to integrate with header/footer */
    main.content .lead { max-width: 900px; }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>
  <main class="content container py-4">
    <div class="row justify-content-center">
      <div class="col-12 col-md-10">
        <h1 class="mb-3">Políticas de CineUp</h1>
        <p class="lead">Esta página contiene las políticas de uso y privacidad para la demo de CineUp. Es un ejemplo y debes adaptarla a tus necesidades legales reales.</p>

        <section class="mb-4">
          <h2>Privacidad</h2>
          <p>No recopilamos datos reales en esta demo. Toda la información de usuarios y pedidos se almacena en la base de datos local para propósitos de demostración.</p>
        </section>

        <section class="mb-4">
          <h2>Contacto</h2>
          <p>Si quieres contactarnos, utiliza el formulario del footer; en esta demo ese formulario es meramente decorativo.</p>
        </section>
      </div>
    </div>
  </main>
  <?php include 'footer.php'; ?>
  <!-- Optional Bootstrap JS (for components if needed) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+AMYJ1Ykz3G1w5Y5n3tz8f7c6z9g5" crossorigin="anonymous"></script>
</body>
</html>
